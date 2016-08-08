<?php

namespace PhpExtended\Ip;

/**
 * Ipv4Network class file.
 * 
 * An Ipv4 Network is a network from which we can deduce some information about
 * the addresses it manages.
 * 
 * @author Anastaszor
 */
class Ipv4Network
{
	
	/**
	 * Gets the ipv4 that is at the start of the network range.
	 * 
	 * @var Ipv4
	 */
	private $_start_ip = null;
	
	/**
	 * Gets the number of bits that are taken by the mask.
	 * 
	 * @var integer
	 */
	private $_mask_bits = null;
	
	/**
	 * Builds a new Ipv4Network object with given ip address and bitmask. The
	 * given ipAddress object may be anything which is useful to build an Ipv4
	 * address. See {@link Ipv4} for more explanations.
	 * 
	 * It is still possible to define a network using only the ipAddress with 
	 * the CIDR notation, without the bitmask argument. For example, the default
	 * class-A network is 10.0.0.0/8.
	 * 
	 * It is also possible to define a network using a configuration array like
	 * array(10, 0, 0, 0, 8) ; or array(10, 0, 0, 0, 'mask' => 8). The 'mask'
	 * element or the fifth element of the array will be used as bitmask when
	 * building this network.
	 * 
	 * This network can also be build as a subnet of an existing network by
	 * setting the bitmask to another value.
	 * 
	 * When the bitmask is not precised and cannot be found into the ipAddress
	 * representation, the default bitmask for class-A through class-E networks
	 * will be used (A:8 ; B:16 ; C:24 ; D:28 ; E:32).
	 * 
	 * @param mixed $ipAddress
	 * @return integer $bitmask
	 * @throws IllegalArgumentException if the content value is not interpretable
	 * @throws IllegalValueException if the parsed integers are not in [0-255]
	 * @throws IllegalRangeException if the ipv6 range is not in ::ffff:0:0/96
	 * @throws IpMalformedException if the value cannot be interpreted
	 */
	public function __construct($ipAddress = null, $bitmask = null)
	{
		if($ipAddress instanceof Ipv4Network)
			$ipAddress = $ipAddress->getStartIp();
		if(is_array($ipAddress) && count($ipAddress) === 5)
		{
			if(isset($ipAddress['mask']) && is_numeric($ipAddress['mask']))
			{
				$mask = (int) $ipAddress['mask'];
				unset($ipAddress['mask']);
			}
			elseif(isset($ipAddress[4]) && is_numeric($ipAddress[4]))
			{
				$mask = (int) $ipAddress[4];
				unset($ipAddress[4]);
			}
		}
		if(is_string($ipAddress) && ($dp = strpos($ipAddress, '/')) !== false)
		{
			$ipAddress = substr($ipAddress, 0, $dp);
			$mask = (int) substr($ipAddress, $dp + 1);
		}
		if(is_numeric($bitmask))
			$mask = (int) $bitmask;
		// else assume default mask from ip address
		$this->_start_ip = new Ipv4($ipAddress);
		if(!isset($mask))
		{
			$nwk_class = $this->getNetworkClass();
			switch($nwk_class)
			{
				case 'A':
					$mask = 8;
					break;
				case 'B':
					$mask = 16;
					break;
				case 'C':
					$mask = 24;
					break;
				case 'D':
					$mask = 28;
					break;
				default:
					$mask = 32;
			}
		}
		$this->_start_ip = $this->_start_ip->_and(new Ipv4('/'.$mask));
	}
	
	/**
	 * Gets the ipv4 which starts the network range.
	 * 
	 * @return Ipv4
	 */
	public function getStartIp()
	{
		return $this->_start_ip;
	}
	
	/**
	 * Gets the ipv4 which ends the network range.
	 * 
	 * @return Ipv4
	 */
	public function getEndIp()
	{
		return $this->getStartIp()->_or($this->getWildmask());
	}
	
	/**
	 * Gets the ipv4 which known as the network address.
	 * 
	 * @return Ipv4
	 */
	public function getNetwork()
	{
		return $this->getStartIp();
	}
	
	/**
	 * Gets the ipv4 which represents the subnet mask.
	 * 
	 * @return Ipv4
	 */
	public function getNetmask()
	{
		return new Ipv4('/'.$this->getMaskBits());
	}
	
	/**
	 * Gets the ipv4 which represents the inverse subnet mask (known as 
	 * wildcard mask).
	 * 
	 * @return Ipv4
	 */
	public function getWildmask()
	{
		return new Ipv4('\\'.$this->getMaskBits());
	}
	
	/**
	 * Gets the number of bits that are taken by the mask.
	 * 
	 * @return integer
	 */
	public function getMaskBits()
	{
		return $this->_mask_bits;
	}
	
	/**
	 * Gets the gateway ipv4 address.
	 * 
	 * @return Ipv4
	 */
	public function getGateway()
	{
		return $this->getStartIp()->add(new Ipv4(array(0, 0, 0, 1)));
	}
	
	/**
	 * Gets the broadcast ipv4 address.
	 * 
	 * @return Ipv4
	 */
	public function getBroadcast()
	{
		return $this->getEndIp()->substract(new Ipv4(array(0, 0, 0, 1)));
	}
	
	/**
	 * Gets the number of addresses that are available in this network
	 * 
	 * @return integer
	 */
	public function getNumberOfaddresses()
	{
		return 2 ** (32 - $this->getMaskBits());
	}
	
	/**
	 * Gets the network class of this network. 
	 * Networks of class A have their first octet like 0xxxxxxx
	 *          of class B have their first octet like 10xxxxxx
	 *          of class C have their first octet like 110xxxxx
	 *          of class D have their first octet like 1110xxxx
	 *          of class E are the rest
	 * 
	 * @return enum('A','B','C','D','E')
	 */
	public function getNetworkClass()
	{
		$byte = $this->getStartIp()->getFirstByte();
		if($byte & 128 === 0)
			return 'A';
		if($byte & 192 === 128)
			return 'B';
		if($byte & 224 === 192)
			return 'C';
		if($byte & 240 === 224)
			return 'D';
		
		return 'E';
	}
	
	/**
	 * Gets whether given ipv4 is included in this network.
	 * 
	 * @param Ipv4 $address
	 * @return boolean
	 */
	public function isIncluded(Ipv4 $address)
	{
		return $this->getStartIp()->getSignedValue() <= $address->getSignedValue()
			&& $this->getEndIp()->getSignedValue() >= $address->getSignedValue();
	}
	
	/**
	 * Gets whether given network is included in this network.
	 * 
	 * @param Ipv4Network $subnetwork
	 * @return boolean
	 */
	public function isNetworkIncluded(Ipv4Network $subnetwork)
	{
		return $this->getMaskBits() <= $subnetwork->getMaskBits()
			&& $this->isIncluded($subnetwork->getStartIp())
			&& $this->isIncluded($subnetwork->getEndIp());
	}
	
	/**
	 * Gets a canonical string representation of this network.
	 * 
	 * @return string
	 */
	public function getCanonicalRepresentation()
	{
		return $this->getStartIp()->getCanonicalRepresentation().'/'.$this->getMaskBits();
	}
	
	/**
	 * Gets a string representation of this network.
	 * 
	 * @return string
	 */
	public function __toString()
	{
		return $this->getCanonicalRepresentation();
	}
	
}
