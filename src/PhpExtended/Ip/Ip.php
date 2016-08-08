<?php

namespace PhpExtended\Ip;

/**
 * Ip interface file.
 * 
 * This interface treats ip for any version indifferently.
 * 
 * @author Anastaszor
 */
interface Ip
{
	
	/**
	 * Gets the short representation of this ip address.
	 *
	 * @return string
	 */
	public function getShortRepresentation();
	
	/**
	 * Gets the canonical representation of this ip address.
	 *
	 * @return string
	 */
	public function getCanonicalRepresentation();
	
}
