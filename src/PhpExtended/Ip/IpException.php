<?php

namespace PhpExtended\Ip;

/**
 * IpException class file.
 * 
 * This class is the base class for all exceptions in this package. This will
 * retains content about the data that made the parsing of Ip Adresses fail.
 * 
 * @author Anastaszor
 */
class IpException extends \Exception
{
	
	/**
	 * Gets the actual value that was not parseable.
	 * 
	 * @var mixed
	 */
	private $_content = null;
	
	/**
	 * Builds a new ip exception with the given content and message.
	 * 
	 * @param mixed $content
	 * @param string $message
	 * @param integer $code
	 * @param \Exception $previous
	 */
	public function __construct($content, $message = null, $code = null, $previous = null)
	{
		$this->_content = $content;
		parent::__construct($this->interfere($message), $code, $previous);
	}
	
	/**
	 * Gets a translated message about the content of this exception.
	 * 
	 * @param string $message
	 * @return string
	 */
	public function interfere($message)
	{
		return strtr('{data}', $this->stringify($this->_content));
	}
	
	/**
	 * Gets a string representation of the content object.
	 * 
	 * @param mixed $data
	 * @return string
	 */
	public function stringify($data)
	{
		if(is_scalar($data))
			return "$data";
		if(is_array($data))
		{
			$datum = array();
			foreach($data as $inner)
			{
				if(is_scalar($inner))
					$datum[] = "$inner";
				if(is_array($inner))
					$datum[] = 'array('.count($inner).')';
				if(is_object($inner))
					$datum[] = 'object('.get_class($inner).')';
				if(is_resource($inner))
					$datum[] = 'resource('.get_resource_type($inner).')';
				if($inner === null)
					$datum[] = 'null';
				if($inner === false)
					$datum[] = 'false';
				if($inner === true)
					$datum[] = 'true';
			}
			return 'array('.implode(', ', $datum).')';
		}
		if(is_object($data))
			return 'object('.get_class($data).')';
		if(is_resource($data))
			return 'resource('.get_resource_type($data).')';
		if($data === null)
			return 'null';
		if($data === true)
			return 'true';
		if($data === false)
			return 'false';
		
		return 'unable to resolve data type';
	}
	
}
