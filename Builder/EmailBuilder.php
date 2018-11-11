<?php
namespace GollumSF\EmailBundle\Builder;
use GollumSF\EmailBundle\Exception\EmailResponseException;
use GollumSF\EmailBundle\Exception\LogicException;
use GollumSF\EmailBundle\Model\Email;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * EmailBuilder
 *
 * @author Damien Duboeuf <smeagolworms4@gmail.com>
 */
class EmailBuilder implements EmailBuilderInterface{
	
	const HEADER_SUBJECT  = 'mail-subject';
	const HEADER_ALT_TEXT = 'mail-text';
	
	/**
	 * @var HttpKernelInterface
	 */
	private $httpKernel;
	
	/**
	 * @var RequestStack
	 */
	private $requestStack;
	
	/**
	 * @var TranslatorInterface
	 */
	private $translator;
	
	/** @var string/ */
	private $senderAddress;
	
	public function __construct(
		HttpKernelInterface $httpKernel,
		RequestStack $requestStack,
		TranslatorInterface $translator,
		ContainerInterface $container
	) {
		$this->httpKernel = $httpKernel;
		$this->requestStack = $requestStack;
		$this->translator = $translator;
		$this->senderAddress =
			$container->hasParameter('swiftmailer.sender_address') ?
			$container->getParameter('swiftmailer.sender_address') :
			NULL
		;
	}
	
	/**
	 * @param string $mailAction
	 * @param array|string NULl $to
	 * @param array|NULl $params
	 * @param array|string|NULl $cc
	 * @param array|string|NULl $bcc
	 * @param array|string|NULl $from
	 * @param string|NULl $subject
	 * @return Email
	 * @throws EmailResponseException, LogicException
	 */
	public function buildFromAction($mailAction, $to, $params = [], $cc = NULL, $bcc = NULL, $from = NULL, $subject = NULL) {
		
		$email = $this->build($subject, NULL, $to, $cc, $bcc, $from);
		
		$request = $this->getSubRequest($mailAction, $params);
		$response = $this->httpKernel->handle($request, HttpKernelInterface::SUB_REQUEST);
		
		if (!$response instanceof Response) {
			throw new LogicException(sprintf("The mail action %s must be returned a %s ", $mailAction, Response::class));
		}
		if (!$response->isSuccessful()) {
			throw new EmailResponseException(sprintf("The mail action %s response not is successful. Statut code = %s : content: %s", $mailAction, $response->getStatusCode(), html_entity_decode(strip_tags($response->getContent()))), $response);
		}
		$body = $response->getContent();
		
		if (!$subject) {
			$subject = $this->translator->trans($response->headers->get(self::HEADER_SUBJECT));
		}
		
		$email->setText($response->headers->get(self::HEADER_ALT_TEXT));
		$email->setSubject($subject);
		$email->setBody($body);
		
		return $email;
	}
	
	/**
	 * @param string $subject
	 * @param string $body
	 * @param array|string $to
	 * @param array|string|NULl $cc
	 * @param array|string|NULl $bcc
	 * @param array|string|NULl $from
	 * @return Email
	 */
	public function build($subject, $body, $to, $cc = NULL, $bcc = NULL, $from = NULL) {
		$to   = $this->parseMails($to);
		$cc   = $this->parseMails($cc);
		$bcc  = $this->parseMails($bcc);
		
		if (!$from && $this->senderAddress) {
			$from = $this->senderAddress;
		}
		
		return (new Email())
			->setBody($body)
			->setSubject($subject)
			->setTo  ($to)
			->setCc  ($cc)
			->setBcc ($bcc)
			->setFrom($from)
		;
	}
	
	/**
	 * @param string $mailAction
	 * @param array $params
	 * @return Request
	 */
	protected function getSubRequest($mailAction, $params = []) {
		$request = $this->requestStack->getCurrentRequest();
		return $request->duplicate([], [], array_merge($params, [ "_controller" => $mailAction ]));
	}
	
	private function parseMails($emails) {
		$emails = is_string($emails) ? [ $emails  ] : (is_array($emails) ? $emails  : []);
		$results = [];
		foreach ($emails as $name => $email) {
			if (is_int($name)) {
				$name = $email;
			}
			$results[$name] = $email;
		}
		return $results;
	}
	
}