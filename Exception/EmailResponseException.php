<?php
namespace GollumSF\EmailBundle\Exception;
use Symfony\Component\HttpFoundation\Response;

/**
 * EmailException
 *
 * @author Damien Duboeuf <smeagolworms4@gmail.com>
 */
class EmailResponseException extends \Exception{
	
	/**
	 * @var Response
	 */
	protected $response;
	
	public function __construct($message, $response, $code = 0, Exception $previous = NULL) {
		parent::__construct($message, $code, $previous);
		$this->response = $response;
	}
	
	/**
	 * @return Response
	 */
	public function getResponse() {
		return $this->response;
	}
}