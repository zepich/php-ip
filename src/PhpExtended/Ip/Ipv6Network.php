<?php

namespace PhpExtended\Ip;

/**
 * Ipv6Network class file.
 * 
 * An Ipv6 Network is a network from which we can deduce some information about
 * the addresses it manages.
 * 
 * @author Anastaszor
 */
class Ipv6Network
{
	
	/**
	 * Gets the Ipv6 that is at the start of the start of the network range.
	 * 
	 * @var Ipv6
	 */
	private $_start_ip = null;
	
	/**
	 * Gets the number of bits that are taken by the mask. (0 <= x <= 128).
	 * 
	 * @var integer
	 */
	private $_mask_bits = null;
	
	/**
	 * Builds a new Ipv6Network object with given ip address and bitmask. The
	 * given ipAddress object may be anything which is useful to build an Ipv6
	 * address. See {@link Ipv6} for more explanations.
	 * 
	 * It is still possible to define a newtork using only the ipAddress with 
	 * the CIDR notation, without the bitmask argument. If so, it will use the
	 * 128-bits bitmask as default.
	 * 
	 * If is also possible to define a network using a configuration array like
	 * array(0, 0, 0, 0, 0, 0, 0, 1, 64) ; or array(0, 0, 0, 0, 0, 0, 0, 1, 'mask' =>64).
	 * The 'mask' element or the ninth element of the array will be used as
	 * bitmask when building this network. Aside from this 9-element array, an
	 * 11-element array may also be provided for the hybrid x:x:x:x:x:x:d.d.d.d/m
	 * notation, like array(0, 0, 0, 0, 0, 0xffff, 127, 0, 0, 1, 96);
	 * 
	 * This network can also be build with another instance of an Ipv6 network, 
	 * and redefining the bitmask it should use. If no bitmask is provided, this
	 * network will be a clone of given network.
	 * 
	 * When the bitmask is not precised and cannot be found into the ipAddress
	 * representation, the default bitmask is 128 bits (meaning 1 address only
	 * in this network).
	 * 
	 * @param mixed $ipAddress
	 * @param integer $bitmask
	 * @throws IllegalArgumentException if the content value is not intepretable
	 * @throws IllegalRangeException if the bitmasks are not in [0-128]
	 * @throws IllegalValueException if the parsed integers ar not in [0-65535]
	 * @throws IpMalformedException if an error occur while interpreting the value
	 */
	public function __construct($ipAddress = null, $bitmask = null)
	{
		if($ipAddress instanceof Ipv6Network)
		{
			$mask = $ipAddress->getMaskBits();
			$ipAddress = $ipAddress->getStartIp();
		}
		if(is_array($ipAddress) && count($ipAddress) === 9)
		{
			if(isset($ipAddress['mask']) && is_numeric($ipAddress['mask']))
			{
				$mask = (int) $ipAddress['mask'];
				unset($ipAddress['mask']);
			}
			elseif(isset($ipAddress[8]) && is_numeric($ipAddress[8]))
			{
				$mask = (int) $ipAddress[8];
				unset($ipAddress[8]);
			}
		}
		if(is_array($ipAddress) && count($ipAddress) === 11)
		{
			if(isset($ipAddress['mask']) && is_numeric($ipAddress['mask']))
			{
				$mask = (int) $ipAddress['mask'];
				unset($ipAddress['mask']);
			}
			elseif(isset($ipAddress[10]) && is_numeric($ipAddress[10]))
			{
				$mask = (int) $ipAddress[10];
				unset($ipAddress[10]);
			}
		}
		if(is_string($ipAddress) && ($dp = strpos($ipAddress, '/')) !== false)
		{
			$ipAddress = substr($ipAddress, 0, $dp);
			$mask = (int) substr($ipAddress, $dp + 1);
		}
		if(is_numeric($bitmask))
			$mask = (int) $bitmask;
		
		if(!isset($mask))
			$mask = 128;
		
		$this->_start_ip = (new Ipv6($ipAddress))->_and(new Ipv6('/'.$mask));
		$this->_mask_bits = $mask;
	}
	
	/**
	 * Gets the ipv6 which starts the network range.
	 * 
	 * @return Ipv6
	 */
	public function getStartIp()
	{
		return $this->_start_ip;
	}
	
	/**
	 * Gets the ipv6 which ends the network range.
	 * 
	 * @return Ipv6
	 */
	public function getEndIp()
	{
		return $this->getStartIp()->_or($this->getWildmaskIp());
	}
	
	/**
	 * Gets the ipv6 known as the network address.
	 * 
	 * @return Ipv6
	 */
	public function getNetworkIp()
	{
		return $this->_start_ip;
	}
	
	/**
	 * Gets the ipv6 which represents the subnet mask.
	 * 
	 * @return Ipv6
	 */
	public function getNetmaskIp()
	{
		return new Ipv6('/'.$this->getMaskBits());
	}
	
	/**
	 * Gets the ipv6 which represents the inverse subnet mask (known as 
	 * wildcard mask).
	 * 
	 * @return Ipv6
	 */
	public function getWildmaskIp()
	{
		return new Ipv6('\\'.$this->getMaskBits());
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
	 * Gets the gateway ipv6 address.
	 * 
	 * @return Ipv6
	 */
	public function getGatewayIp()
	{
		return $this->getStartIp()->add(new Ipv6(array(0, 0, 0, 0, 0, 0, 0, 1)));
	}
	
	/**
	 * Gets the broadcast ipv6 address.
	 * 
	 * @return Ipv6
	 */
	public function getBroadcastIp()
	{
		return $this->getEndIp()->substract(new Ipv6(array(0, 0, 0, 0, 0, 0, 0, 1)));
	}
	
	/**
	 * Gets the number of addresses that are available in this network.
	 * 
	 * @return string because of integer overflow.
	 */
	public function getNumberOfAddresses()
	{
		return bcsub(bcpow("2", (string) (128 - $this->getMaskBits())), "2");
	}
	
	/**
	 * Gets whether given ipv6 is included in this network.
	 * 
	 * @param Ipv6 $address
	 * @return boolean
	 */
	public function contains(Ipv6 $address)
	{
		return $address->_and($this->getNetmaskIp())->equals($this->getNetworkIp());
	}
	
	/**
	 * Gets whether given network is included in this network.
	 * 
	 * @param Ipv6Network $subnetwork
	 * @return boolean
	 */
	public function containsNetwork(Ipv6Network $subnetwork)
	{
		return $this->getMaskBits() <= $subnetwork->getMaskBits()
			&& $this->contains($subnetwork->getStartIp())
			&& $this->contains($subnetwork->getEndIp());
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
