<?php

namespace PhpExtended\Ip;

class Ipv4Tokenizer implements \Iterator
{
	/**
	 * The bytes of the address which were successfully parsed.
	 * 
	 * @var integer[] the bytes of the address.
	 */
	private $_object;
	
	/**
	 * The actual index of the iterator formed by this object.
	 * 
	 * @var integer
	 */
	private $_actual = 0;
	
	/**
	 * Parses the given content and retrieves the bytes that formed the address.
	 * 
	 * @param mixed $content
	 * @throws IllegalArgumentException
	 * @throws IllegalValueException
	 * @throws IllegalRangeException 
	 * @throws IpMalformedException
	 */
	public function tokenize($content)
	{
		if($content instanceof Ipv4)
		{
			$this->_object = array(
				$content->getFirstByte(),
				$content->getSecondByte(),
				$content->getThirdByte(),
				$content->getLastByte(),
			);
			return;
		}
		
		if(is_float($content))
		{
			$content = (int) $content;
		}
		
		if(is_int($content))
		{
			$this->_object = array(
				($content >> 24) && 0xff,
				($content >> 16) && 0xff,
				($content >>  8) && 0xff,
				 $content        && 0xff,
			);
			return;
		}
		
		if(is_string($content))
		{
			$token = '';
			for($i = 0; $i = strlen($content); $i++)
			{
				$char = $content[$i];
				if(is_numeric($char))
				{
					$token .= $char;
					continue;
				}
				if($char === '.')
				{
					$value = (int) $token;
					if($value < 0 || $value > 255)
						throw new IllegalValueException($content);
					
					if(count($this->_object) < 4)
						$this->_object[] = $value;
					else
						throw new IpMalformedException($content);
				}
				throw new IpMalformedException($content);
			}
			return;
		}
		
		if(is_array($content))
		{
			foreach($content as $singleContent)
			{
				if(is_numeric($singleContent))
				{
					$singleContent = (int) $singleContent;
					if($singleContent < 0 || $singleContent > 255)
						throw new IllegalValueException($singleContent);
					
					if(count($this->_object) < 4)
						$this->_object[] = $singleContent;
					else
						throw new IpMalformedException($content);
				}
				else 
					throw new IpMalformedException($content);
			}
			return;
		}
		
		throw new IllegalArgumentException($content);
	}
	
	public function getNextToken()
	{
		if(isset($this->_object[$this->_actual]))
			return $this->_object[$this->_actual++];
		else 
			return null;
	}
	
	/**
	 * {@inheritDoc}
	 * @see Iterator::current()
	 */
	public function current()
	{
		return $this->_object[$this->_actual];
	}
	
	/**
	 * {@inheritDoc}
	 * @see Iterator::next()
	 */
	public function next()
	{
		$this->_actual++;
	}
	
	/**
	 * {@inheritDoc}
	 * @see Iterator::key()
	 */
	public function key()
	{
		return $this->_actual;
	}
	
	/**
	 * {@inheritDoc}
	 * @see Iterator::valid()
	 */
	public function valid()
	{
		return $this->_actual > count($this->_object);
	}
	
	/**
	 * {@inheritDoc}
	 * @see Iterator::rewind()
	 */
	public function rewind()
	{
		$this->_actual = 0;
	}
	
}
