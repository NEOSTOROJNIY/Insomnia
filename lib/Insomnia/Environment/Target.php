<?php
/**
 * Assigned target to the test.
 */
class Insomnia_Environment_Target
{
	/**
	 * @var string $resource URL of target resource.
	 */
	private $resourceURL;

	private $resourceIP;

	/**
	 * @var boolean $XSSAssigned Flag which signals that current target assigned to XSS Test.
	 */
	private $XSSAssigned;

	/**
	 * @var boolean $SQLInjectionAssigned Flag which signals that current target assigned to SQLInjection Test.
	 */
	private $SQLInjectionAssigned;

	/**
	 * @param string $resource
	 * @param boolean $SQLInjectionAssigned
	 * @param boolean $XSSAssigned
	 */
	public function __construct($resourceURL, $XSSAssigned, $SQLInjectionAssigned) {
		$this->resourceURL          = $resourceURL;
		$this->XSSAssigned          = $XSSAssigned;
		$this->SQLInjectionAssigned = $SQLInjectionAssigned;

		$requestHandler = new Insomnia_Common_Request_SQLInjectionRequestHandler();
		$requestSet = Insomnia_Common_Request_Builder_SQLInjectionRequestSetBuilder::buildRequestSet($resourceURL);
		$requestHandler->setRequestSet($requestSet);
		$replySet = $requestHandler->executeProcess();

		$ipAddr = $replySet->getInfo()['primary_ip'];
		$this->resourceIP = ($ipAddr === '::1') ? '127.0.0.1' : $ipAddr;
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

	/**
	 * @return boolean
	 */
	public function isAssignedToSQLInjectionTest() {
		return $this->SQLInjectionAssigned;
	}

	/**
	 * @return boolean
	 */
	public function isAssignedToXSSTest() {
		return $this->XSSAssigned;
	}
}