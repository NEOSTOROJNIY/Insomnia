<?php
/**
 * Results collection of task.
 */
class Insomnia_Workflow_SQLInjectionTaskResult extends Insomnia_Workflow_TaskResult
{
	/**
	 * @var protected string $resourceURL URL of researched resource.
	 */
	protected $resourceURL;

	/**
	 * @var protected string $resourceIP IP of researched resource.
	 */
	protected $resourceIP;

	protected $serverBanner;

	protected $possibleDB;

	/**
	 * @var protected array ( Insomnia_Common_SQLInjectionVulerableData, ..ю ) $vulnerabilityInfo Vulnerability facts.
	 */
	protected $vulnerabilityInfo;

	/**
	 * @param string $resourceURL
	 * @param array ( Insomnia_Common_SQLInjectionVulnerableData, ... ) $vulnerabilityInfo
	 */
	public function __construct($resourceURL, $resourceIP, $vulnerabilityInfo, $possibleDB = "unknown", $serverBanner = "NONE") {
		$this->resourceURL       = $resourceURL;
		$this->resourceIP        = $resourceIP;
		$this->serverBanner 	 = $serverBanner;
		$this->vulnerabilityInfo = $vulnerabilityInfo;
		$this->possibleDB		 = $possibleDB;
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

	public function getServerBanner() {
		return $this->serverBanner;
	}

	public function getPossibleDB() {
		return $this->possibleDB;
	}

	/**
	 * @return array ( Insomnia_Common_SQLInjectionVulnerableData, ... )
	 */
	public function getVulnerabilityInfo() {
		return $this->vulnerabilityInfo;
	}

	public function setResourceURL($resourceURL) {
		$this->resourceURL = $resourceURL;
	}

	public function setResourceIP($resourceIP) {
		$this->resourceIP = $resourceIP;
	}

	public function setServerBanner($serverBanner) {
		$this->serverBanner = $serverBanner;
	}

	public function setPossibleDB($possibleDB) {
		$this->possibleDB = $possibleDB;
	}
}