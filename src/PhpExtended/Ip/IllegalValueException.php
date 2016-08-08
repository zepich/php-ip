<?php

namespace PhpExtended\Ip;

/**
 * IllegalValueException class file.
 * 
 * This exception is thrown when parsing for an element which is not the type
 * or the form that was expected.
 * 
 * @author Anastaszor
 */
class IllegalValueException extends IpException
{
	
	/**
	 * Builds a new Illegal Value Exception.
	 * 
	 * @param mixed $data
	 * @param string $message
	 * @param integer $code
	 * @param \Exception $previous
	 */
	public function __construct($data, $message = null, $code = null, $previous = null)
	{
		if($message === null)
			$message = 'The given value is not parseable as an ip address : {data} given.';
		parent::__construct($data, $message, $code, $previous);
	}
	
}
