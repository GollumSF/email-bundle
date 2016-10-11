<?php
namespace GollumSF\EmailBundle\Sender;

/**
 * EmailSenderInterface
 *
 * @author Damien Duboeuf <smeagolworms4@gmail.com>
 */
interface EmailSenderInterface {
	
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
	public function sendFromAction($mailAction, $to, $params = [], $cc = NULL, $bcc = NULL, $from = NULL);
	
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
	public function send($subject, $body, $to, $cc = NULL, $bcc = NULL, $from = NULL);
	
}