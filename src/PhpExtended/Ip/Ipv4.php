<?php

namespace PhpExtended\Ip;

/**
 * Ipv4 class file.
 * 
 * This class represents an Ipv4 as specified into rfc 791.
 * 
 * @author Anastaszor
 * @see https://www.rfc-editor.org/rfc/rfc791.txt
 */
class Ipv4
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
	 * @param mixed $ipAddress
	 * @throws IllegalArgumentException
	 * @throws IllegalRangeException
	 * @throws IpMalformedException
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
	 * @return number between 0 and 255.
	 */
	public function getFirstByte()
	{
		return $this->_oct1;
	}
	
	/**
	 * Gets the second byte.
	 * 
	 * @return number between 0 and 255.
	 */
	public function getSecondByte()
	{
		return $this->_oct2;
	}
	
	/**
	 * Gets the third byte.
	 * 
	 * @return number between 0 and 255.
	 */
	public function getThirdByte()
	{
		return $this->_oct3;
	}
	
	/**
	 * Gets the last byte.
	 * 
	 * @return number between 0 and 255.
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
	 * Gets the short representation of this ip address.
	 * 
	 * @return string
	 */
	public function getShortRepresentation()
	{
		if($this->getSignedValue() === 2130706433) return 'localhost';
		return $this->getCanonicalRepresentation();
	}
	
	/**
	 * Gets the canonical representation of this ip address.
	 * 
	 * @return string
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
	
}
