<?php
/**
 * Base report class.
 */
abstract class Insomnia_Environment_Reporting_Report
{
	/**
	 * @var integer SECURITYLEVEL_* Security Level. Green: good, Yellow: bad, Red: OMFG!!!
	 */
	const SECURITYLEVEL_GREEN  = 3;
	const SECURITYLEVEL_YELLOW = 2;
	const SECURITYLEVEL_RED	   = 1;

	/**
	 * @var string $resourceURL URL of investigated resource.
	 */
	protected $resourceURL;

	/**
	 * @var string $resourceIP IP of investigated resource.
	 */
	protected $resourceIP;

	/**
	 * @var integer $securityLevel Security Level.
	 */
	protected $securityLevel;

	/**
	 * @param string $resourceURL URL of investigated resource.
	 */
	public function __construct($resourceURL, $resourceIP) {
		$this->resourceURL = $resourceURL;
		$this->resourceIP = $resourceIP;
	}

	/**
	 * @return string
	 */
	public function getResourceURL() {
		return $this->resourceURL;
	}

	/**
	 * @return string
	 */
	public function getResourceIP() {
		return $this->resourceIP;
	}

	/**
	 * @return integer
	 */
	public function getSecurityLevel() {
		return $this->securityLevel;
	}


}