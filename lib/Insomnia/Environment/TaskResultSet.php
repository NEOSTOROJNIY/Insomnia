<?php
/**
 * Test results of the resource. Used in InsomniaFacade.
 */
class Insomnia_Environment_TaskResultSet
{
	/**
	 * @var string $resource URL of resource.
	 */
	private $resourceURL;

	private $resourceIP;

	private $serverBanner;

	/**
	 * @var SQLInjectionTaskResult|boolean $SQLInjectionTaskResult If we dont making this test it will be FALSE.
	 */
	private $SQLInjectionTaskResult;

	/**
	 * @var XSSTaskResult|boolean $XSSTaskResult If we dont making this test it will be FALSE.
	 */
	private $XSSTaskResult;

	/**
	 * @param string $resource
	 * @param SQLInjectionTaskResult|boolean $SQLInjectionTaskResult
	 * @param XSSTaskResult|boolean $XSSTaskResult
	 */
	public function __construct($resourceURL = "", $resourceIP = "", $SQLInjectionTaskResult = false, $XSSTaskResult = false, $serverBanner = "NONE") {
		$this->resourceURL            = $resourceURL;
		$this->resourceIP 			  = $resourceIP;
		$this->serverBanner			  = $serverBanner;
		$this->SQLInjectionTaskResult = $SQLInjectionTaskResult;
		$this->XSSTaskResult          = $XSSTaskResult;
	}

	/**
	 * 
	 */
	public function setResourceURL($resourceURL) {
		$this->resourceURL = $resourceURL;
	}

	public function setResourceIP($resourceIP) {
		$this->resourceIP = $resourceIP;
	}

	public function setServerBanner($serverBanner) {
		$this->serverBanner = $serverBanner;
	}

	/**
	 * @return string
	 */
	public function getResourceURL() {
		return $this->resourceURL;
	}

	public function getResourceIP() {
		return $this->resourceIP;
	}

	public function getServerBanner() {
		return $this->serverBanner;
	}

	/**
	 * @param SQLInjectionTaskResult|null $SQLInjectionTaskResult
	 */
	public function setSQLInjectionTaskResult($SQLInjectionTaskResult) {
		$this->SQLInjectionTaskResult = $SQLInjectionTaskResult;
	}

	/**
	 * @return SQLInjectionTaskResult|null
	 */
	public function getSQLInjectionTaskResult() {
		return $this->SQLInjectionTaskResult;
	}

	/**
	 * @param XSSTaskResult|null $XSSTaskResult
	 */
	public function setXSSTaskResult($XSSTaskResult) {
		$this->XSSTaskResult = $XSSTaskResult;
	}

	/**
	 * @return XSSTaskResult|null
	 */
	public function getXSSTaskResult() {
		return $this->XSSTaskResult;
	}
}