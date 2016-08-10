<?php

namespace PhpExtended\Ip;

/**
 * Ipv6 class file.
 * 
 * @author Anastaszor
 * @see https://www.ietf.org/rfc/rfc2460.txt
 */
class Ipv6 implements Ip
{
	
	/**
	 * This is the first two octets in the ip address. In the ip address
	 * 2001:2002:2003:2004:2005:2006:2007:2008, this corresponds to the 2001.
	 * This integer is always between 0000 and ffff, inclusive, meaning it is
	 * between 0 and 65535, inclusive.
	 * 
	 * @var integer
	 */
	private $_group1 = 0;
	
	/**
	 * This is the second two octets in the ip address. In the ip address
	 * 2001:2002:2003:2004:2005:2006:2007:2008, this corresponds to the 2002.
	 * This integer is always between 0000 and ffff, inclusive, meaning it is
	 * between 0 and 65535, inclusive.
	 * 
	 * @var integer
	 */
	private $_group2 = 0;
	
	/**
	 * This is the third two octets in the ip address. In the ip address
	 * 2001:2002:2003:2004:2005:2006:2007:2008, this corresponds to the 2003.
	 * This integer is always between 0000 and ffff, inclusive, meaning it is
	 * between 0 and 65535, inclusive.
	 * 
	 * @var integer
	 */
	private $_group3 = 0;
	
	/**
	 * This is the fourth two octets in the ip address. In the ip address
	 * 2001:2002:2003:2004:2005:2006:2007:2008, this corresponds to the 2004.
	 * This integer is always between 0000 and ffff, inclusive, meaning it is
	 * between 0 and 65535, inclusive.
	 * 
	 * @var integer
	 */
	private $_group4 = 0;
	
	/**
	 * This is the fifth two octets in the ip address. In the ip address
	 * 2001:2002:2003:2004:2005:2006:2007:2008, this corresponds to the 2005.
	 * This integer is always between 0000 and ffff, inclusive, meaning it is
	 * between 0 and 65535, inclusive.
	 * 
	 * @var integer
	 */
	private $_group5 = 0;
	
	/**
	 * This is the sixth two octets in the ip address. In the ip address
	 * 2001:2002:2003:2004:2005:2006:2007:2008, this corresponds to the 2006.
	 * This integer is always between 0000 and ffff, inclusive, meaning it is
	 * between 0 and 65535, inclusive.
	 * 
	 * @var integer
	 */
	private $_group6 = 0;
	
	/**
	 * This is the seventh two octets in the ip address. In the ip address
	 * 2001:2002:2003:2004:2005:2006:2007:2008, this corresponds to the 2007.
	 * This integer is always between 0000 and ffff, inclusive, meaning it is
	 * between 0 and 65535, inclusive.
	 * 
	 * @var integer
	 */
	private $_group7 = 0;
	
	/**
	 * This is the last two octets in the ip address. In the ip address
	 * 2001:2002:2003:2004:2005:2006:2007:2008, this corresponds to the 2008.
	 * This integer is always between 0000 and ffff, inclusive, meaning it is
	 * between 0 and 65535, inclusive.
	 * 
	 * @var integer
	 */
	private $_group8 = 0;
	
	/**
	 * Builds a new Ipv6 address from a given object. This object can be an ip
	 * address, an integer, or a string which represents an ip address.
	 * 
	 * If null is provided, this is interpreted as :: (full zero) address.
	 * If 'localhost' is provided, this is interpreted as ::1 ip address.
	 * 
	 * If an Ipv4 is provided, it will be transformed into an ipv6 within range
	 * of ::ffff:0:0/96.
	 * If an Ipv6 is provided, it will be cloned as-is.
	 * 
	 * If the given argument is not parseable as an ip address, then an 
	 * IllegalArgumentException will be thrown.
	 * 
	 * If the given ip address is incomplete or seriously damaged, then an
	 * IpMalformedException will be thrown.
	 * 
	 * If a string which starts with '/' is provided, it will be interpreted as
	 * the bitmask. For example, the '/48' address will be the
	 * ffff:ffff:ffff:: address.
	 * 
	 * If a string which starts with '\\' is provided, it will be interpreted
	 * as the inverse bitmask. For example, the '\48' address will be the
	 * ::ffff:ffff:ffff:ffff:ffff address.
	 * 
	 * @param mixed $ipAddress
	 * @throws IllegalArgumentException if the content value is not intepretable
	 * @throws IllegalRangeException if the bitmasks are not in [0-128]
	 * @throws IllegalValueException if the parsed integers ar not in [0-65535]
	 * @throws IpMalformedException if an error occur while interpreting the value
	 */
	public function __construct($ipAddress = null)
	{
		if(empty($ipAddress)) return;
		
		$tokenizer = new Ipv6Tokenizer();
		$tokenizer->tokenize($ipAddress);
		
		$this->_group1 = (int) $tokenizer->getNextToken();
		$this->_group2 = (int) $tokenizer->getNextToken();
		$this->_group3 = (int) $tokenizer->getNextToken();
		$this->_group4 = (int) $tokenizer->getNextToken();
		$this->_group5 = (int) $tokenizer->getNextToken();
		$this->_group6 = (int) $tokenizer->getNextToken();
		$this->_group7 = (int) $tokenizer->getNextToken();
		$this->_group8 = (int) $tokenizer->getNextToken();
	}
	
	/**
	 * Gets the first group.
	 * 
	 * @return integer between 0 and 65535
	 */
	public function getFirstGroup()
	{
		return $this->_group1;
	}
	
	/**
	 * Gets the second group.
	 * 
	 * @return integer between 0 and 65535
	 */
	public function getSecondGroup()
	{
		return $this->_group2;
	}
	
	/**
	 * Gets the third group.
	 * 
	 * @return integer between 0 and 65535
	 */
	public function getThirdGroup()
	{
		return $this->_group3;
	}
	
	/**
	 * Gets the fourth group.
	 * 
	 * @return integer between 0 and 65535
	 */
	public function getFourthGroup()
	{
		return $this->_group4;
	}
	
	/**
	 * Gets the fifth group.
	 * 
	 * @return integer between 0 and 65535
	 */
	public function getFifthGroup()
	{
		return $this->_group5;
	}
	
	/**
	 * Gets the sixth group.
	 * 
	 * @return integer between 0 and 65535
	 */
	public function getSixthGroup()
	{
		return $this->_group6;
	}
	
	/**
	 * Gets the seventh group.
	 * 
	 * @return integer between 0 and 65535
	 */
	public function getSeventhGroup()
	{
		return $this->_group7;
	}
	
	/**
	 * Gets the eigth group.
	 * 
	 * @return integer between 0 and 65535
	 */
	public function getLastGroup()
	{
		return $this->_group8;
	}
	
	/**
	 * {@inheritDoc}
	 * @see Ip::getShortRepresentation()
	 */
	public function getShortRepresentation()
	{
		$desc = '';
		$hexvals = array(
			dechex($this->_group1),
			dechex($this->_group2),
			dechex($this->_group3),
			dechex($this->_group4),
			dechex($this->_group5),
			dechex($this->_group6),
			dechex($this->_group7),
			dechex($this->_group8),
		);
		$shortcutUsed = false;
		$currentShortcut = false;
		foreach($hexvals as $c => $hexval)
		{
			if($hexval === '0')
			{
				if($shortcutUsed === true)
				{
					$desc .= '0';
					if($c < 8)
						$desc .= ':';
				}
				else
				{
					if($currentShortcut === false)
					{
						$currentShortcut = true;
						$desc .= ':';
						if($c < 8)
							$desc .= ':';
					}
				}
			}
			else
			{
				if($currentShortcut === true)
				{
					$currentShortcut = false;
					$shortcutUsed = true;
				}
				$desc .= $hexval;
				if($c < 8)
					$desc .= ':';
			}
		}
		return $desc;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \PhpExtended\Ip\Ip::getCanonicalRepresentation()
	 */
	public function getCanonicalRepresentation()
	{
		return $this->make4Hex($this->_group1)
			.':'.$this->make4Hex($this->_group2)
			.':'.$this->make4Hex($this->_group3)
			.':'.$this->make4Hex($this->_group4)
			.':'.$this->make4Hex($this->_group5)
			.':'.$this->make4Hex($this->_group6)
			.':'.$this->make4Hex($this->_group7)
			.':'.$this->make4Hex($this->_group8);
	}
	
	/**
	 * Gets the 4-letter hexa value from given digit.
	 * 
	 * @param integer $value
	 * @return string hexadecimal value with 4 letters
	 */
	protected function make4Hex($value)
	{
		$hex = dechex($value);
		while(strlen($hex) < 4)
			$hex = '0'.$hex;
		return $hex;
	}
	
	/**
	 * Gets a string representation of the ip address.
	 * 
	 * @return string
	 */
	public function __toString()
	{
		return $this->getShortRepresentation();
	}
	
	/**
	 * Does the bitwise OR operation for each bit of this address, using the
	 * other given address.
	 * 
	 * @param Ipv6 $other
	 * @return Ipv6 the result
	 */
	public function _or(Ipv6 $other)
	{
		$new = new Ipv6();
		$new->_group1 = $this->_group1 | $other->_group1;
		$new->_group2 = $this->_group2 | $other->_group2;
		$new->_group3 = $this->_group3 | $other->_group3;
		$new->_group4 = $this->_group4 | $other->_group4;
		$new->_group5 = $this->_group5 | $other->_group5;
		$new->_group6 = $this->_group6 | $other->_group6;
		$new->_group7 = $this->_group7 | $other->_group7;
		$new->_group8 = $this->_group8 | $other->_group8;
		return $new;
	}
	
	/**
	 * Does the bitwise AND operation for each bit of this address, using the
	 * other given address. 
	 * 
	 * @param Ipv6 $other
	 * @return Ipv6 the result
	 */
	public function _and(Ipv6 $other)
	{
		$new = new Ipv6();
		$new->_group1 = $this->_group1 & $other->_group1;
		$new->_group2 = $this->_group2 & $other->_group2;
		$new->_group3 = $this->_group3 & $other->_group3;
		$new->_group4 = $this->_group4 & $other->_group4;
		$new->_group5 = $this->_group5 & $other->_group5;
		$new->_group6 = $this->_group6 & $other->_group6;
		$new->_group7 = $this->_group7 & $other->_group7;
		$new->_group8 = $this->_group8 & $other->_group8;
		return $new;
	}
	
	/**
	 * Does the bitwise NOT operation for each bit of this address.
	 * 
	 * @param Ipv6 $other
	 * @return Ipv6 the result
	 */
	public function _not()
	{
		$new = new Ipv6();
		$new->_group1 = (~ $this->_group1) & 0x0000ffff;
		$new->_group2 = (~ $this->_group2) & 0x0000ffff;
		$new->_group3 = (~ $this->_group3) & 0x0000ffff;
		$new->_group4 = (~ $this->_group4) & 0x0000ffff;
		$new->_group5 = (~ $this->_group5) & 0x0000ffff;
		$new->_group6 = (~ $this->_group6) & 0x0000ffff;
		$new->_group7 = (~ $this->_group7) & 0x0000ffff;
		$new->_group8 = (~ $this->_group8) & 0x0000ffff;
		return $new;
	}
	
	/**
	 * Does the addition of this address with the given address. Each byte adds
	 * up separately with remainders. No address may add upper than the 
	 * ffff:ffff:ffff:ffff:ffff:ffff:ffff:ffff address.
	 * 
	 * @param Ipv6 $other
	 */
	public function add(Ipv6 $other)
	{
		$new = new Ipv6();
		$add8 = $this->_group8 + $other->_group8;
		$new->_group8 = $add8 & 0x0000ffff;
		$rmd7 = ($add8 >> 16) & 0x0000ffff;
		$add7 = $this->_group7 + $other->_group7 + $rmd7;
		$new->_group7 = $add7 & 0x0000ffff;
		$rmd6 = ($add7 >> 16) & 0x0000ffff;
		$add6 = $this->_group6 + $other->_group6 + $rmd6;
		$new->_group6 = $add6 & 0x0000ffff;
		$rmd5 = ($add6 >> 16) & 0x0000ffff;
		$add5 = $this->_group5 + $other->_group5 + $rmd5;
		$new->_group5 = $add5 & 0x0000ffff;
		$rmd4 = ($add5 >> 16) & 0x0000ffff;
		$add4 = $this->_group4 + $other->_group4 + $rmd4;
		$new->_group4 = $add4 & 0x0000ffff;
		$rmd3 = ($add4 >> 16) & 0x0000ffff;
		$add3 = $this->_group3 + $other->_group3 + $rmd3;
		$new->_group3 = $add3 & 0x0000ffff;
		$rmd2 = ($add3 >> 16) & 0x0000ffff;
		$add2 = $this->_group2 + $other->_group2 + $rmd2;
		$new->_group2 = $add2 & 0x0000ffff;
		$rmd1 = ($add2 >> 16) & 0x0000ffff;
		$add1 = $this->_group1 + $other->_group1 + $rmd1;
		$new->_group1 = $add1 & 0x0000ffff;
		return $new;
	}
	
	/**
	 * Does the substraction of this address with the given address. Each byte
	 * substracts up separately with remainteds. No adderss may substract lower
	 * than the :: address.
	 * 
	 * @param Ipv6 $other
	 */
	public function substract(Ipv6 $other)
	{
		$new1 = $other->_not();
		$new2 = $this->add($new1);
		return $new2->add(new Ipv6(array(0, 0, 0, 0, 0, 0, 0, 1)));
	}
	
	/**
	 * Gets whether the given other thing represents exactly the same ipv6 as
	 * this one.
	 * 
	 * @param unknown $object
	 */
	public function equals($object)
	{
		if(is_object($object) && $object instanceof Ipv6)
		{
			return $this->getFirstGroup() === $object->getFirstGroup()
				&& $this->getSecondGroup() === $object->getSecondGroup()
				&& $this->getThirdGroup() === $object->getThirdGroup()
				&& $this->getFourthGroup() === $object->getFourthGroup()
				&& $this->getFifthGroup() === $object->getFifthGroup()
				&& $this->getSixthGroup() === $object->getSixthGroup()
				&& $this->getSeventhGroup() === $object->getSeventhGroup()
				&& $this->getLastGroup() === $object->getLastGroup();
		}
		return false;
	}
	
	/**
	 * Gets whether this ip object is included into the network represented by
	 * given ip address and netmask. 
	 * 
	 * @param mixed $ipAddress
	 * @return boolean
	 */
	public function isInRange($ipAddress, $bitmask = null)
	{
		return (new Ipv6Network($ipAddress, $bitmask))->contains($this);
	}
	
}
