<?php

namespace PhpExtended\Ip;

/**
 * Ipv4 class file.
 * 
 * This class represents an Ipv4 as specified into rfc 791.
 * 
 * @author Anastaszor
 * @see https://www.ietf.org/rfc/rfc791.txt
 */
class Ipv4 implements Ip
{
	
	/**
	 * This is the first octet of the ip address. In the ip 192.168.0.1, 
	 * this corresponds to the 192. This integer is always between 0 and 255,
	 * inclusive.
	 * 
	 * @var integer
	 */
	private $_oct1 = 0;
	
	/**
	 * This is the second octet of the ip address. In the ip 192.168.0.1,
	 * this corresponds to the 168. This integer is always between 0 and 255,
	 * inclusive.
	 * 
	 * @var integer
	 */
	private $_oct2 = 0;
	
	/**
	 * This is the third octet of the ip address. In the ip 192.168.0.1,
	 * this corresponds to the 0. This integer is always between 0 and 255,
	 * inclusive.
	 * 
	 * @var integer
	 */
	private $_oct3 = 0;
	
	/**
	 * This is the last octet of the ip address. In the ip 192.168.0.1, this
	 * corresponds to the 1. This integer is always between 0 and 255,
	 * inclusive.
	 * 
	 * @var integer
	 */
	private $_oct4 = 0;
	
	/**
	 * Builds a new Ipv4 address from a given object. This object can be
	 * an ip address, an integer or a string which represents an ip address.
	 * 
	 * If null is provided, this is interpreted as the 0.0.0.0 ip address.
	 * If 'localhost' is provided, this is interpreted as the 127.0.0.1 address.
	 * 
	 * If an Ipv4 is provided, this ip address will be cloned from the other.
	 * If an Ipv6 is provided, this will be transformed from the ipv6 as if
	 * the given ipv6 is in the ::ffff:0:0/96 form. If the ipv6 is not in this
	 * form, then an IllegalRangeException will be thrown.
	 * 
	 * If the given argument is not parseable as an ip address, then an 
	 * IllegalArgumentException will be thrown.
	 * 
	 * If the given ip address is incomplete or seriously damaged, then an
	 * IpMalformedException will be thrown.
	 * 
	 * If a string which starts with '/' is provided, it will be interpreted as
	 * the bitmask. For example, the '/24' address will be the 255.255.255.0
	 * address.
	 * 
	 * If a string which starts with '\' is provided, it will be interpreted as
	 * the inverse bitmask. For example, the '\24' address will be the 0.0.0.255
	 * address.
	 * 
	 * @param mixed $ipAddress
	 * @throws IllegalArgumentException if the content value is not interpretable
	 * @throws IllegalValueException if the parsed integers are not in [0-255]
	 * @throws IllegalRangeException if the ipv6 range is not in ::ffff:0:0/96
	 * @throws IpMalformedException if the value cannot be interpreted
	 */
	public function __construct($ipAddress = null)
	{
		if(empty($ipAddress)) return;
		
		$tokenizer = new Ipv4Tokenizer();
		$tokenizer->tokenize($ipAddress);
		
		$this->_oct1 = (int) $tokenizer->getNextToken();
		$this->_oct2 = (int) $tokenizer->getNextToken();
		$this->_oct3 = (int) $tokenizer->getNextToken();
		$this->_oct4 = (int) $tokenizer->getNextToken();
	}
	
	/**
	 * Gets the first byte.
	 * 
	 * @return integer between 0 and 255.
	 */
	public function getFirstByte()
	{
		return $this->_oct1;
	}
	
	/**
	 * Gets the second byte.
	 * 
	 * @return integer between 0 and 255.
	 */
	public function getSecondByte()
	{
		return $this->_oct2;
	}
	
	/**
	 * Gets the third byte.
	 * 
	 * @return integer between 0 and 255.
	 */
	public function getThirdByte()
	{
		return $this->_oct3;
	}
	
	/**
	 * Gets the last byte.
	 * 
	 * @return integer between 0 and 255.
	 */
	public function getLastByte()
	{
		return $this->_oct4;
	}
	
	/**
	 * This function returns the exact unsigned 32 bit integer that corresponds
	 * to this ip address.
	 * 
	 * @return integer
	 */
	public function getSignedValue()
	{
		return $this->_oct1 << 24 + $this->_oct2 << 16 + $this->_oct3 << 8 + $this->_oct4;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Ip::getShortRepresentation()
	 */
	public function getShortRepresentation()
	{
		if($this->getSignedValue() === 2130706433) return 'localhost';
		return $this->getCanonicalRepresentation();
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Ip::getCanonicalRepresentation()
	 */
	public function getCanonicalRepresentation()
	{
		return $this->_oct1.'.'.$this->_oct2.'.'.$this->_oct3.'.'.$this->_oct4;
	}
	
	/**
	 * Gets a string representation of the ip address.
	 * 
	 * @return string
	 */
	public function __toString()
	{
		return $this->getCanonicalRepresentation();
	}
	
	/**
	 * Does the bitwise OR operation for each bit of this address, using the 
	 * other given address.
	 * 
	 * @param Ipv4 $other
	 * @return Ipv4 the result
	 */
	public function _or(Ipv4 $other)
	{
		$new = new Ipv4();
		$new->_oct1 = $this->_oct1 | $other->_oct1;
		$new->_oct2 = $this->_oct2 | $other->_oct2;
		$new->_oct3 = $this->_oct3 | $other->_oct3;
		$new->_oct4 = $this->_oct4 | $other->_oct4;
		return $new;
	}
	
	/**
	 * Does the bitwise AND operation for each bit of this address, using the
	 * other given address.
	 * 
	 * @param Ipv4 $other
	 * @return Ipv4 the result
	 */
	public function _and(Ipv4 $other)
	{
		$new = new Ipv4();
		$new->_oct1 = $this->_oct1 & $other->_oct1;
		$new->_oct2 = $this->_oct2 & $other->_oct2;
		$new->_oct3 = $this->_oct3 & $other->_oct3;
		$new->_oct4 = $this->_oct4 & $other->_oct4;
		return $new;
	}
	
	/**
	 * Does the bitwise NOT operation for each bit of this address.
	 * 
	 * @return Ipv4 the result
	 */
	public function _not()
	{
		$new = new Ipv4();
		$new->_oct1 = ~ $this->_oct1;
		$new->_oct2 = ~ $this->_oct2;
		$new->_oct3 = ~ $this->_oct3;
		$new->_oct4 = ~ $this->_oct4;
		return $new;
	}
	
	/**
	 * Does the addition of this address with the given address. Each byte adds
	 * up separately with remainders. No address may add upper than the 
	 * 255.255.255.255 address.
	 * 
	 * @param Ipv4 $other
	 * @return Ipv4 the result
	 */
	public function add(Ipv4 $other)
	{
		$new = new Ipv4();
		$add4 = $this->_oct4 + $other->_oct4;
		$new->_oct4 = $add4 & 0x000000ff;
		$rmd3 = ($add4 & 0x0000ff00) >> 8;
		$add3 = $this->_oct3 + $other->_oct3 + $rmd3;
		$new->_oct3 = $add3 & 0x000000ff;
		$rmd2 = ($add3 & 0x0000ff00) >> 8;
		$add2 = $this->_oct2 + $other->_oct2 + $rmd2;
		$new->_oct2 = $add2 & 0x000000ff;
		$rmd1 = ($add2 & 0x0000ff00) >> 8;
		$add1 = $this->_oct1 + $other->_oct1 + $rmd1;
		$new->_oct1 = $add1 & 0x000000ff;
		return $new;
	}
	
	/**
	 * Does the substraction of this address with the given address. Each byte
	 * substracts up separately with remainteds. No adderss may substract lower
	 * than the 0.0.0.0 address.
	 * 
	 * @param Ipv4 $other
	 * @return Ipv4 the result
	 */
	public function substract(Ipv4 $other)
	{
		return new Ipv4(max(0, $this->getSignedValue() - $other->getSignedValue()));
	}
	
}
