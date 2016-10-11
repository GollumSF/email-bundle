<?php
namespace GollumSF\EmailBundle\Builder;
use GollumSF\EmailBundle\Model\Email;

/**
 * EmailBuilderInterface
 *
 * @author Damien Duboeuf <smeagolworms4@gmail.com>
 */
interface EmailBuilderInterface{
	
	/**
	 * @param string $mailAction
	 * @param array|string $to
	 * @param array|NULl $params
	 * @param array|string|NULl $cc
	 * @param array|string|NULl $bcc
	 * @param array|string|NULl $from
	 * @param string|NULl $subject
	 * @return Email
	 */
	public function buildFromAction($mailAction, $to, $params = [], $cc = NULL, $bcc = NULL, $from = NULL, $subject = NULL);
	
	/**
	 * @param string $subject
	 * @param string $body
	 * @param array|string $to
	 * @param array|string|NULl $cc
	 * @param array|string|NULl $bcc
	 * @param array|string|NULl $from
	 * @return Email
	 */
	public function build($subject, $body, $to, $cc = NULL, $bcc = NULL, $from = NULL);
	
	
}