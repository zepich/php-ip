<?php

namespace PhpExtended\Ip;

/**
 * Ipv6Tokenizer class file.
 * 
 * This class is made to transform any input as the components of an ipv6. It
 * acts as a factory.
 * 
 * @author Aanstaszor
 */
class Ipv6Tokenizer implements \Iterator
{
	
	/**
	 * The bytes of the address which were successfully parsed.
	 * 
	 * @var integer the bytes of the address.
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
	 * Special cases:
	 * - null will be interpreted as ::
	 * - false will be interpreted as ::
	 * - true will be interpreted as ::1
	 * - an Ipv4 instance will be interpreted as ::ffff:x.x.x.x address.
	 * - an Ipv6 instance will be cloned as-is
	 * - a float content will be converted to integer
	 * - an integer will be interpreted as signed 32-bit ipv4 address. For
	 * 		example, 167772285 will be interpreted as ::ffff.10.0.0.125
	 * - a string will be parsed in any form (canonical or short accepted).
	 * 		incomplete or malformed string (such with double ::) will throw exceptions
	 * - special strings
	 * 		'localhost', 'loopback', 'lo', and 'eth0' will be interpreted as ::1
	 * 		/24 will be interpreted as network mask ffff:ff00::
	 * 		\24 will be interpreted as wildcard mask ::ff:ffff:ffff:ffff:ffff:ffff:ffff
	 * - an 8-array will be interpreted as the 8-parts of the ip
	 * - a 10-array will be interpreted as the first 6-parts of the ipv6, then
	 * 		4-parts of ipv4.
	 * 
	 * @param mixed $content
	 * @throws IllegalArgumentException if the content value is not intepretable
	 * @throws IllegalRangeException if the bitmasks are not in [0-128]
	 * @throws IllegalValueException if the parsed integers ar not in [0-65535]
	 * @throws IpMalformedException if an error occur while interpreting the value
	 */
	public function tokenize($content = null)
	{
		if($content === null || $content === false)
		{
			$this->_object = array(0, 0, 0, 0, 0, 0, 0, 0);
			return;
		}
		
		if($content === true)
		{
			$this->_object = array(0, 0, 0, 0, 0, 0, 0, 1);
			return;
		}
		
		if($content instanceof Ipv4)
		{
			$this->_object = array(0, 0, 0, 0, 0, 0x0000ffff, 
				($content->getFirstByte() << 8) + $content->getSecondByte(),
				($content->getThirdByte() << 8) + $content->getLastByte(),
			);
			return;
		}
		
		if($content instanceof Ipv6)
		{
			$this->_object = array(
				$content->getFirstGroup(),
				$content->getSecondGroup(),
				$content->getThirdGroup(),
				$content->getFourthGroup(),
				$content->getFifthGroup(),
				$content->getSixthGroup(),
				$content->getSeventhGroup(),
				$content->getLastGroup(),
			);
			return;
		}
		
		if(is_float($content))
		{
			$content = (int) $float;
		}
		
		if(is_int($content))
		{
			// assumed ipv4 value.
			$this->_object = array(0, 0, 0, 0, 0, 0x0000ffff,
				($content >> 16) & 0x0000ffff,
				 $content        & 0x0000ffff,
			);
			return;
		}
		
		if(is_string($content))
		{
			if($content === 'localhost' || $content === 'lo'
				|| $content === 'loopback' || $content === 'eth0'
			) {
				$this->_object = array(0, 0, 0, 0, 0, 0, 0, 1);
				return;
			}
			
			if($content[0] === '/')		// assumed bitwise mask
			{
				$content = trim($content, '/');
				if(is_numeric($content))
				{
					$content = (int) $content;
					if($content >= 0 && $content <= 128)
					{
						$res = array(0, 0, 0, 0, 0, 0, 0, 0);
						if($content > 0 && $content <= 16)
							$res[0] = ((0xffffffff << (16 - $content)) & 0x0000ffff);
						elseif($content > 16)
							$res[0] = 0x0000ffff;
						
						if($content > 16 && $content <= 32)
							$res[1] = ((0xffffffff << (32 - $content)) & 0x0000ffff);
						elseif($content > 32)
							$res[1] = 0x0000ffff;
						
						if($content > 32 && $content <= 48)
							$res[2] = ((0xffffffff << (48 - $content)) & 0x0000ffff);
						elseif($content > 48)
							$res[2] = 0x0000ffff;
						
						if($content > 48 && $content <= 64)
							$res[3] = ((0xffffffff << (64 - $content)) & 0x0000ffff);
						elseif($content > 64)
							$res[3] = 0x0000ffff;
						
						if($content > 64 && $content <= 80)
							$res[4] = ((0xffffffff << (80 - $content)) & 0x0000ffff);
						elseif($content > 80)
							$res[4] = 0x0000ffff;
						
						if($content > 80 && $content <= 96)
							$res[5] = ((0xffffffff << (96 - $content)) & 0x0000ffff);
						elseif($content > 96)
							$res[5] = 0x0000ffff;
						
						if($content > 96 && $content <= 112)
							$res[6] = ((0xffffffff << (112 - $content)) & 0x0000ffff);
						elseif($content > 112)
							$res[6] = 0x0000ffff;
						
						if($content > 112 && $content <= 128)
							$res[7] = ((0xffffffff << (128 - $content)) & 0x0000ffff);
						
						$this->_object = $res;
						return;
					}
					throw new IllegalRangeException('/'.$content,
						'The given bitmask is not in range of allowed bitmasks which is 0 to 128 : {data} given.'
					);
				}
				throw new IllegalValueException('/'.$content,
					'The given bitmask is not a numerical bitmask : {data} given.'
				);
			}
			
			if($content[0] === '\\')
			{
				$content = trim($content, '/');
				if(is_numeric($content))
				{
					$content = (int) $content;
					if($content >= 0 && $content <= 128)
					{
						$res = array(0, 0, 0, 0, 0, 0, 0, 0);
						if($content > 112 && $content <= 128)
							$res[7] = 0x0000ffff >> ($content - 112);
						elseif($content < 112)
							$res[7] = 0x0000ffff;
						
						if($content > 96 && $content <= 112)
							$res[6] = 0x0000ffff >> ($content - 96);
						elseif($content < 96)
							$res[6] = 0x0000ffff;
						
						if($content > 80 && $content <= 96)
							$res[5] = 0x0000ffff >> ($content - 80);
						elseif($content < 80)
							$res[5] = 0x0000ffff;
						
						if($content > 64 && $content <= 80)
							$res[4] = 0x0000ffff >> ($content - 64);
						elseif($content < 64)
							$res[4] = 0x0000ffff;
						
						if($content > 48 && $content <= 64)
							$res[3] = 0x0000ffff >> ($content - 48);
						elseif($content < 48)
							$res[3] = 0x0000ffff;
						
						if($content > 32 && $content <= 48)
							$res[2] = 0x0000ffff >> ($content - 32);
						elseif($content < 32)
							$res[2] = 0x0000ffff;
						
						if($content > 16 && $content <= 32)
							$res[1] = 0x0000ffff >> ($content - 16);
						elseif($content < 16)
							$res[1] = 0x0000ffff;
						
						if($content <= 16)
							$res[0] = 0x0000ffff >> $content;
						
						$this->_object = $res;
						return;
					}
					throw new IllegalRangeException('/'.$content,
						'The given bitmask is not in range of allowed bitmasks which is 0 to 128 : {data} given.'
					);
				}
				throw new IllegalValueException('\\'.$content,
					'The given bitmask is not a numerical bitmask : {data} given.'
				);
			}
			
			$token = '';
			$stack = array(0, 0, 0, 0, 0, 0, 0, 0);
			$current = 0;
			// forward mode. we use it as long as we dont find a ::
			for($i = 0; $i < strlen($content); $i++)
			{
				$char = $content[$i];
				if($this->isHexa($char))
				{
					$token .= $char;
					continue;
				}
				if($char === ':')
				{
					if($token === '')
					{
						break; // found ::
					}
					$value = dechex($token);
					if($value < 0 || $value > 65535)
						throw new IllegalValueException($content,
							'The ip address contains a number which is not between 0 and ffff : {data} given.'
						);
					
					if($current > 7)
						throw new IpMalformedException($content,
							'The ip address contains more than 8 bit groups : {data} given.'
						);
					
					$stack[$current] = $value;
					$token = '';
					$current++;
					continue;
				}
				if($char === '.')
				{
					if(!is_numeric($token))
						throw new IllegalValueException($content,
							'The ip address contains a number which is not between 0 and 255 : {data} given.'
						);
					
					$token = (int) $token;
					
					switch(true)
					{
						case $current === 6:
							$stack[6] |= ($token << 8);
							break;
						
						case $current === 7:
							$stack[6] |= $token;
							break;
						
						case $current === 8:
							$stack[7] |= ($token << 8);
							break;
						
						case $current === 9:
							$stack[7] |= $token;
							break;
						
						default:
							throw new IpMalformedException($content,
								'The ip address contains an ipv4 bit groups which is not at the right place : {data} given.'
							);
					}
					
					$token = '';
					$current++;
					continue;
				}
				throw new IpMalformedException($content,
					'The ip address contains a non-numeric non-dot non-semicolon character : {data} given.'
				);
			}
			$token = '';
			$current = 7;
			// backward mode. we use it if we found ::
			for($j = strlen($content); $j >= $i ; $j++)	// back to the i'th position exactly. need to find the ":"
			{
				$char = $content[$j];
				if($this->isHexa($char))
				{
					$token = $char.$token;
					continue;
				}
				if($char === ':')
				{
					if($token === '')
						throw new IpMalformedException($content,
							'The ip address contains two groups of "::" : {data} given.'
						);
					
					$value = dechex($token);
					if($value < 0 || $value > 65535)
						throw new IllegalValueException($content,
							'The ip address contains a number which is not between 0 and ffff : {data} given.'
						);
					
					$stack[$current] = $value;
					$token = '';
					$current--;
					continue;
				}
				if($char === '.')
				{
					if(!is_numeric($token))
						throw new IllegalValueException($content,
							'The ip address contains a number which is not between 0 and 255 : {data} given.'
						);
					
					$token = (int) $token;
					
					switch(true)
					{
						case $current === 7:
							$stack[7] |= $token;
							break;
						
						case $current === 6:
							$stack[7] |= ($token << 8);
							break;
						
						case $current === 5:
							$stack[6] |= $token;
							break;
						
						case $current === 4:
							$stack[6] |= ($token << 8);
							break;
						
						default:
							throw new IpMalformedException($content,
								'The ip address contains an ipv4 bit groups which is not at the right place : {data} given.'
							);
					}
					
					$token = '';
					$current--;
					if($current === 3)
						$current = 5;	// correction of offset, normal ipv6 addresses take total 8 slots but hybrids take 10
					continue;
				}
				throw new IpMalformedException($content,
					'The ip address contains a non-numeric non-dot non-semicolon character : {data} given.'
				);
			}
			
			$this->_object = $stack;
			return;
		}
		
		if(is_array($content) && count($content) === 8)
		{
			foreach($content as $singleContent)
			{
				if(is_numeric($singleContent))
				{
					$singleContent = (int) $singleContent;
					if($singleContent < 0 || $singleContent > 65535)
						throw new IllegalValueException($content,
							'The ip address contains a number which is not between 0 and 65535 : {data} given.'
						);
					
					if(count($this->_object) < 8)
						$this->_object[] = $singleContent;
					else
						throw new IpMalformedException($content,
							'The ip address contains more than 8 bit groups : {data} given.'
						);
					
					continue;
				}
				throw new IpMalformedException($content,
					'The ip address contains a non-numeric value : {data} given.'
				);
			}
			return;
		}
		
		if(is_array($content) && count($content) === 10)	// special x:x:x:x:x:x:d.d.d.d hybrid notation
		{
			foreach($content as $c => $singleContent)
			{
				if(is_numeric($singleContent))
				{
					$singleContent = (int) $singleContent;
					if($singleContent < 0 || $singleContent > 65535)
						throw new IllegalValueException($content,
							'The ip address contains a number which is not between 0 and 65535 : {data} given.'
						);
					if($c > 5 && $singleContent > 255)
						throw new IllegalValueException($content,
							'The ip address contains a number which is not between 0 and 255 : {data} given.'
						);
					
					if(count($this->_object) < 8)
					{
						switch(true)
						{
							case $c < 6:
								$this->_object[] = $singleContent;
								break;
							case $c === 6:
								$this->_object[6] = $singleContent << 8;
								break;
							case $c === 7:
								$this->_object[6] += $singleContent;
								break;
							case $c === 8:
								$this->_object[7] = $singleContent << 8;
								break;
							case $c === 9:
								$this->_object[7] += $singleContent;
								break;
							default:
								throw new IpMalformedException($content,
									'The ip address contains more than 10 bit groups : {data} given.'
								);
						}
					}
					else
						throw new IpMalformedException($content,
							'The ip address contains more than 8 bit groups : {data} given.'
						);
					
					continue;
				}
				throw new IpMalformedException($content,
					'The ip address contains a non-numeric value : {data} given.'
				);
			}
			return;
		}
		
		throw new IllegalArgumentException($content,
			'The given ip address is not parsable. You should better use the string with : notation, or an array with integers to specify the bits of the ip address : {data} given.'
		);
	}
	
	/**
	 * Gets whether given char is hexa or not.
	 * 
	 * @param char $char
	 * @return boolean
	 */
	public function isHexa($char)
	{
		$lchar = strtolower($char);
		return is_numeric($char)
			|| $lchar === 'a' || $lchar === 'b' || $lchar === 'c'
			|| $lchar === 'd' || $lchar === 'e' || $lchar === 'f';
	}
	
	/**
	 * Gets the next token. This should be an integer between 0 and 65535. This
	 * returns null if there is not more tokens.
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
