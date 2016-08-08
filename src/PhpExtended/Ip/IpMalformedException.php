<?php

namespace PhpExtended\Ip;

/**
 * IpMalformedException class file.
 * 
 * This exception is thrown when parsing for creation of an ip address an
 * element which was not expected by the parser.
 * 
 * @author Anastaszor
 */
class IpMalformedException extends IpException
{
	
	/**
	 * Builds a new Ip Malformed Exception.
	 * 
	 * @param mixed $data
	 * @param string $message
	 * @param integer $code
	 * @param \Exception $previous
	 */
	public function __construct($data, $message = null, $code = null, $previous = null)
	{
		if($message === null)
			$message = 'The given ip address is malformed: {data} given.';
		parent::__construct($data, $message, $code, $previous);
	}
	
}
