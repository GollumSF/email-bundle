<?php
namespace GollumSF\EmailBundle\Model;

/**
 * Email
 *
 * @author Damien Duboeuf <damien@swabbl.com>
 */
class Email {
	
	/**
	 * @var array
	 */
	protected $to = [];
	
	/**
	 * @var array
	 */
	protected $cc = [];
	
	/**
	 * @var array
	 */
	protected $bcc = [];
	
	/**
	 * @var array
	 */
	protected $from = [];
	
	/**
	 * @var string
	 */
	protected $body = '';
	
	/**
	 * @var string
	 */
	protected $text;
	
	/**
	 * @var string
	 */
	protected $subject;
	
	/**
	 * @return array
	 */
	public function getTo() {
		return $this->to;
	}
	
	
	/////////////
	// Getters //
	/////////////
	
	/**
	 * @return array
	 */
	public function getCc() {
		return $this->cc;
	}
	
	/**
	 * @return array
	 */
	public function getBcc() {
		return $this->bcc;
	}
	
	/**
	 * @return array
	 */
	public function getFrom() {
		return $this->from;
	}
	
	/**
	 * @return string
	 */
	public function getBody() {
		return $this->body;
	}
	
	/**
	 * @return string
	 */
	public function getText() {
		return $this->text;
	}
	
	/**
	 * @return string
	 */
	public function getSubject() {
		return $this->subject;
	}
	
	
	/////////////
	// Setters //
	/////////////
	
	/**
	 * @param array $to
	 * @return self
	 */
	public function setTo(array $to) {
		$this->to = $to;
		return $this;
	}
	
	/**
	 * @param array $cc
	 * @return self
	 */
	public function setCc(array $cc) {
		$this->cc = $cc;
		return $this;
	}
	
	/**
	 * @param array $bcc
	 * @return self
	 */
	public function setBcc(array $bcc) {
		$this->bcc = $bcc;
		return $this;
	}
	
	/**
	 * @param array $from
	 * @return self
	 */
	public function setFrom(array $from) {
		$this->from = $from;
		return $this;
	}
	
	/**
	 * @param string $body
	 * @return self
	 */
	public function setBody($body) {
		$this->body = $body;
		return $this;
	}
	
	/**
	 * @param string $text
	 * @return self
	 */
	public function setText($text) {
		$this->text = $text;
		return $this;
	}
	
	/**
	 * @param string $subject
	 * @return self
	 */
	public function setSubject($subject) {
		$this->subject = $subject;
		return $this;
	}
	
	
	/////////
	// Add //
	/////////
	
	/**
	 * @param string $mail
	 * @return self
	 */
	public function addTo($mail) {
		$this->to[] = $mail;
		return $this;
	}
	
	/**
	 * @param string $mail
	 * @return self
	 */
	public function addCc($mail) {
		$this->cc[] = $mail;
		return $this;
	}
	
	/**
	 * @param string $mail
	 * @return self
	 */
	public function addBcc($mail) {
		$this->bcc[] = $mail;
		return $this;
	}
	
	/**
	 * @param string $mail
	 * @param string $name
	 * @return self
	 */
	public function addFrom($mail, $name = NULL) {
		$name = $name ? $name : $mail;
		$this->from[$name] = $mail;
		return $this;
	}
	
	
	////////////
	// Others //
	////////////
	
	/**
	 * @return \Swift_Message
	 */
	public function toSwiftMessage() {
		$message = new \Swift_Message();
		
		$message->setSubject($this->getSubject());
		$message->setBody($this->getBody(), 'text/html');
		
		if ($this->getText()) {
			$message->addPart($this->getText(),'text/plain');
		}
		
		$first = true;
		foreach ($this->getTo() as $i => $to) {
			$method = $first ? 'setTo' : 'addTo';
			$message->$method($to);
			$first = false;
		}
		$first = true;
		foreach ($this->getCc() as $i => $cc) {
			$method = $first ? 'setCc' : 'addCc';
			$message->$method($cc);
			$first = false;
		}
		$first = true;
		foreach ($this->getBcc() as $i => $bcc) {
			$method = $first ? 'setBcc' : 'addBcc';
			$message->$method($bcc);
			$first = false;
		}
		
		if ($this->getFrom()) {
			$message->setFrom($this->getFrom());
		}
		
		return $message;
	}
	
}