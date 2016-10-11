<?php
namespace GollumSF\EmailBundle\Builder;
use GollumSF\EmailBundle\Exception\EmailResponseException;
use GollumSF\EmailBundle\Exception\LogicException;
use GollumSF\EmailBundle\Model\Email;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * EmailBuilder
 *
 * @author Damien Duboeuf <smeagolworms4@gmail.com>
 */
class EmailBuilder implements EmailBuilderInterface{
	
	/**
	 * @var HttpKernel
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
	
	public function __construct(HttpKernel $httpKernel, RequestStack $requestStack, TranslatorInterface $translator) {
		$this->httpKernel = $httpKernel;
		$this->requestStack = $requestStack;
		$this->translator = $translator;
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
			throw new EmailResponseException(sprintf("The mail action %s response not is successful. Statut code = %s", $mailAction, $response->getStatusCode()), $response);
		}
		$body = $response->getContent();
		
		if (!$subject && array_key_exists('Mail-Subject', $response->headers)) {
			$subject = $this->translator->trans($response->headers['Mail-Subject']);
		}
		
		if (!$subject && array_key_exists('Mail-Text', $response->headers)) {
			$text = $this->translator->trans($response->headers['Mail-Text']);
			$email->setText($text);
		}
		
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
		$from = $this->parseMails($from, true);
		
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
	
	private function parseMails($emails, $isFrom = false) {
		$emails = is_string($emails) ? [ $emails  ] : (is_array($emails) ? $emails  : []);
		if ($isFrom) {
			$results = [];
			foreach ($emails as $name => $email) {
				if (is_int($name)) {
					$name = $email;
				}
				$results[$name] = $email;
			}
			$emails = $results;
		}
		return $emails;
	}
	
}