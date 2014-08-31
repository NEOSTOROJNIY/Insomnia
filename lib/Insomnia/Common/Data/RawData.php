<?php
/**
 * Raw data class extends TaskData class.
 * It stores possibly vulnerable URL, that should be tested of vulnerabilities.
 */
abstract class Insomnia_Common_Data_RawData
	extends Insomnia_Common_Data_TaskData
{
	/**
	 * @var protected string $possiblyVulnerable URL, that should be tested on vulnerabilities.
	 */
	protected $possiblyVulnerableURL;

	/**
	 * @var protected string $resourceIP Resource IP.
	 */
	protected $resourceIP;

	/**
	 * @param string $possiblyVulnerableURL Possibly vulnerable URL.
	 * @return $this
	 */
	public function __construct($possiblyVulnerableURL) {
		$this->possiblyVulnerableURL = urldecode($possiblyVulnerableURL);
		
		$requestHandler = new Insomnia_Common_Request_SQLInjectionRequestHandler();
		$requestSet = Insomnia_Common_Request_Builder_SQLInjectionRequestSetBuilder::buildRequestSet($this->possiblyVulnerableURL);
		$requestHandler->setRequestSet($requestSet);
		$replySet = $requestHandler->executeProcess();

		$ipAddr = $replySet->getInfo()['primary_ip'];
		$this->resourceIP = ($ipAddr === '::1') ? '127.0.0.1' : $ipAddr;
	}

	/**
	 * @param string $possiblyVulnerableURL PossiblyVulnerable URL.
	 */
	public function setURL($possiblyVulnerableURL) {
		$this->possiblyVulnerableURL = $possiblyVulnerableURL;
	}

	/**
	 * @return string
	 */
	public function getURL() {
		return $this->possiblyVulnerableURL;
	}

	public function getIP() {
		return $this->resourceIP;
	}

	public function setIP($resourceIP) {
		$this->resourceIP = $resourceIP;
	}
}