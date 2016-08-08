<?php

namespace PhpExtended\Ip;

/**
 * Ipv4Tokenizer class file.
 * 
 * This class is made to transform any input as the components of an ipv4. It
 * acts as a factory.
 * 
 * @author Anastaszor
 */
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
	 * Special cases :
	 * - null will be interpreted as 0.0.0.0
	 * - false will be interpreted as 0.0.0.0
	 * - true will be interpreted as 127.0.0.1
	 * - an Ipv4 instance will be cloned as-is
	 * - an Ipv6 instance will be reduced if in range ::ffff:0:0/96
	 * - a float content will be converted to integer
	 * - an integer will be interpreted as signed 32-bit ip address. For example,
	 * 		167772285 will be interpreted as 10.0.0.125
	 * - a string will be parsed in canonical form (incomplete strings throws exceptions)
	 * - special strings:
	 * 		'localhost', 'loopback', 'lo' and 'eth0' will be interpreted as 127.0.0.1
	 * 		/24 will be interpreted as the network mask 11111111 11111111 11111111 00000000
	 * 		\24 will be interpreted as the wildcard mask 00000000 00000000 00000000 11111111 
	 * - an array will be interpreted as the 4-parts of the ip.
	 * 
	 * @param mixed $content
	 * @throws IllegalArgumentException if the content value is not interpretable
	 * @throws IllegalValueException if the parsed integers are not in [0-255]
	 * @throws IllegalRangeException if the ipv6 range is not in ::ffff:0:0/96
	 * @throws IpMalformedException if the value cannot be interpreted
	 */
	public function tokenize($content = null)
	{
		if($content === null || $content === false)
		{
			$this->_object = array(0, 0, 0, 0);
			return;
		}
		
		if($content === true)
		{
			$this->_object = array(127, 0, 0, 1);
			return;
		}
		
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
		
		if($content instanceof Ipv6)
		{
			if($content->isInRange('::ffff:0:0/96'))
			{
				$this->_object = array(
					($content->getSeventhGroup() >> 8) & 0x000000ff,
					 $content->getSeventhGroup()       & 0x000000ff,
					($content->getEighthGroup() >> 8)  & 0x000000ff,
					 $content->getEighthGroup()        & 0x000000ff,
				);
				return;
			}
			throw new IllegalRangeException($content, 
				'The given ipv6 is not in range of ipv4 wrapping range which is ::ffff:0:0/96 : {data} given.'
			);
		}
		
		if(is_float($content))
		{
			$content = (int) $content;
		}
		
		if(is_int($content))
		{
			$this->_object = array(
				($content >> 24) & 0x000000ff,
				($content >> 16) & 0x000000ff,
				($content >>  8) & 0x000000ff,
				 $content        & 0x000000ff,
			);
			return;
		}
		
		if(is_string($content))
		{
			if($content === 'localhost' || $content === 'lo' 
				|| $content === 'loopback' || $content === 'eth0'
			) {
				$this->_object = array(127, 0, 0, 1);
				return;
			}
			
			if($content[0] === '/')
			{
				$content = trim('/', $content);
				if(is_numeric($content))
				{
					$content = (int) $content;
					if($content >= 0 && $content <= 32)
					{
						$accu = 0;
						for($i = 0; $i < 32 - $content; $i++)
							$accu = $accu | (1 << $i);
						
						$value = ~ $accu;
						$this->_object = array(
							($value >> 24) & 0x000000ff,
							($value >> 16) & 0x000000ff,
							($value >> 8)  & 0x000000ff,
							 $value        & 0x000000ff,
						);
						return;
					}
					throw new IllegalRangeException('/'.$content,
						'The given bitmask is not in range of allowed bitmasks which is 0 to 32 : {data} given.'
					);
				}
				throw new IllegalValueException('/'.$content,
					'The given bitmask is not a numerical bitmask : {data} given.'
				);
			}
			
			if($content[0] === '\\')
			{
				$content = trim('\\', $content);
				if(is_numeric($content))
				{
					$content = (int) $content;
					if($content >= 0 && $content <= 32)
					{
						$value = 0;
						for($i = 0; $i < 32 - $content; $i++)
							$value = $value | (1 << $i);
						
						$this->_object = array(
							($value >> 24) & 0x000000ff,
							($value >> 16) & 0x000000ff,
							($value >> 8)  & 0x000000ff,
							 $value        & 0x000000ff,
						);
						return;
					}
					throw new IllegalRangeException('\\'.$content,
						'The given bitmask is not in range of allowed bitmasks which is 0 to 32 : {data} given.'
					);
				}
				throw new IllegalValueException('\\'.$content,
					'The given bitmask is not a numerical bitmask : {data} given.'
				);
			}
			
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
						throw new IllegalValueException($content,
							'The ip address contains a number which is not between 0 and 255 : {data} given.'
						);
					
					if(count($this->_object) < 4)
						$this->_object[] = $value;
					else
						throw new IpMalformedException($content,
							'The ip address contains more than 4 bit groups : {data} given.'
						);
					
					continue;
				}
				throw new IpMalformedException($content,
					'The ip address contains a non-numeric non-dot character : {data} given.'
				);
			}
			return;
		}
		
		if(is_array($content) && count($content) === 4)
		{
			foreach($content as $singleContent)
			{
				if(is_numeric($singleContent))
				{
					$singleContent = (int) $singleContent;
					if($singleContent < 0 || $singleContent > 255)
						throw new IllegalValueException($content,
							'The ip address contains a number which is not between 0 and 255 : {data} given.'
						);
					
					if(count($this->_object) < 4)
						$this->_object[] = $singleContent;
					else
						throw new IpMalformedException($content,
							'The ip address contains more than 4 bit groups : {data} given.'
						);
					
					continue;
				}
				throw new IpMalformedException($content,
					'The ip address contains a non-numeric non-dot character : {data} given.'
				);
			}
			return;
		}
		
		throw new IllegalArgumentException($content,
			'The given ip address is not parsable. You should better use the integer, a string with the dotted notation, or an array with integers to specify the bits of the ip address : {data} given.'
		);
	}
	
	/**
	 * Gets the next token. This should be an integer between 0 and 255. This
	 * returns null if there is no more tokens.
	 * 
	 * @return integer
	 */
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
