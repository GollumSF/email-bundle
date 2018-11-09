<?php
namespace GollumSF\EmailBundle\Sender;

use GollumSF\EmailBundle\Builder\EmailBuilderInterface;
use GollumSF\EmailBundle\Model\Email;

/**
 * EmailSender
 *
 * @author Damien Duboeuf <smeagolworms4@gmail.com>
 */
class EmailSender implements EmailSenderInterface {
	
	/**
	 * @var EmailBuilderInterface
	 */
	private $emailBuilder;
	
	/**
	 * @var \Swift_Mailer
	 */
	private $mailer;
	
	public function __construct(
		EmailBuilderInterface $emailBuilder,
		\Swift_Mailer $mailer
	) {
		$this->emailBuilder = $emailBuilder;
		$this->mailer = $mailer;
	}
	
	/**
	 * @param string $mailAction
	 * @param array|string $to
	 * @param array|NULl $params
	 * @param array|string|NULl $cc
	 * @param array|string|NULl $bcc
	 * @param array|string|NULl $from
	 * @param string|NULl $subject
	 * @return boolean
	 */
	public function sendFromAction($mailAction, $to, $params = [], $cc = NULL, $bcc = NULL, $from = NULL, $subject = NULL) {
		$email = $this->emailBuilder->buildFromAction($mailAction, $to, $params, $cc, $bcc, $from, $subject);
		return $this->sendEmailModel($email);
	}
	
	/**
	 * @param string $subject
	 * @param string $body
	 * @param array|string $to
	 * @param array|NULl $params
	 * @param array|string|NULl $cc
	 * @param array|string|NULl $bcc
	 * @param array|string|NULl $from
	 * @return boolean
	 */
	public function send($subject, $body, $to, $cc = NULL, $bcc = NULL, $from = NULL) {
		$email = $this->emailBuilder->build($subject, $body, $to, $cc, $bcc, $from);
		return $this->sendEmailModel($email);
	}
	
	/**
	 * @param Email $email
	 * @return bool
	 */
	protected function sendEmailModel(Email $email) {
		$message = $email->toSwiftMessage();
		return !!$this->mailer->send($message);
	}
}