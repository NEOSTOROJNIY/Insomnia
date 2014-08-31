<?php
class Insomnia_Workflow_XSSTaskResult extends Insomnia_Workflow_TaskResult
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

	/**
	 * @var protected array ( Insomnia_Common_SQLInjectionVulerableData, ..ю ) $vulnerabilityInfo Vulnerability facts.
	 */
	protected $vulnerabilityInfo;

	/**
	 * @param string $resourceURL
	 * @param array $vulnerabilityInfo
	 * Form:
	 * array ( 
	 * 		form_number => array (
	 *			"actionURL" 	=> ..,
	 *		    "originalURL" 	=> ..,
	 *			"vulnerableDataCollection" 		=> array ( XSSVulnerableData, .., .., .. )
	 *		),
	 *		.. ,
	 *		..
	 * )
	 */
	public function __construct($resourceURL, $resourceIP, $vulnerabilityInfo,  $serverBanner = "NONE") {
		$this->resourceURL       = $resourceURL;
		$this->resourceIP        = $resourceIP;
		$this->serverBanner		 = $serverBanner;
		$this->vulnerabilityInfo = $vulnerabilityInfo;
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
	 * @return array ( Insomnia_Common_SQLInjectionVulnerableData, ... )
	 */
	public function getVulnerabilityInfo() {
		return $this->vulnerabilityInfo;
	}	
}