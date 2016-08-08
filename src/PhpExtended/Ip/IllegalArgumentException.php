<?php

namespace PhpExtended\Ip;

/**
 * IllegalArgumentException class file.
 * 
 * This exception is thrown when parsing for creation of an ip address to find
 * an element which is not in anyway appropriate to build that ip address.
 * 
 * @author Anastaszor
 */
class IllegalArgumentException extends IpException
{
	
	/**
	 * Builds a new Illegal Argument Exception.
	 * 
	 * @param mixed $data
	 * @param string $message
	 * @param integer $code
	 * @param \Exception $previous
	 */
	public function __construct($data, $message = null, $code = null, $previous = null)
	{
		if($message === null)
			$message = 'The given data is unsuitable to build an ip address : {data} given.';
		parent::__construct($data, $message, $code, $previous);
	}
	
}
