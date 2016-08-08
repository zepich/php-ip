<?php

namespace PhpExtended\Ip;

/**
 * IllegalRangeException class file.
 * 
 * This exception is thrown when parsing for creation of an ip address, an
 * element which turns out to be out of range from its representation's bounds.
 * 
 * @author Anastaszor
 */
class IllegalRangeException extends IpException
{
	
	/**
	 * Builds a new Illegal Range Exception.
	 * 
	 * @param mixed $data
	 * @param string $message
	 * @param integer $code
	 * @param \Exception $previous
	 */
	public function __construct($data, $message = null, $code = null, $previous = null)
	{
		if($message === null)
			$message = 'The given ip address has an element out of range: {data} given.';
		parent::__construct($data, $message, $code, $previous);
	}
	
}
