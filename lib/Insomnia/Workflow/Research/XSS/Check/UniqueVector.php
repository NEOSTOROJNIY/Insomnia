<?php
/**
 * 'Unique Vector' XSS Check Technique.
 * Inserts unique symbol into param and checking the result for vulnerabilities data.
 */
class Insomnia_Workflow_Research_XSS_Check_UniqueVector
	extends Insomnia_Workflow_Research_XSS_XSSCheckTechnique
{

	//Insomnia_Workflow_Research_XSS_XSSCheckTechnique::XSS_REFLECTED
	//Insomnia_Workflow_Research_XSS_XSSCheckTechnique::XSS_STORED
	//Insomnia_Workflow_Research_XSS_XSSCheckTechnique::XSS_UNKNOWN

	/**
	 * @var public string $techniqueName
	 */
	public static $techniqueName = "Unique Vector";

	/**
	 * @var public integer $techniqueID
	 */
	public static $techniqueID = 1;

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

	/**
	 * @var private array $uniqueVectorsSet
	 * Form:
	 * array (
	 *    array (
	 *       "vector"  => ...
	 * 		 "answers" => array ( ..., ..., ... )
	 *    ),
	 *    ... ,
	 *    ...
	 * )
	 */
	private static $uniqueVectors = array (
		array (
			"vectorValue" => "'';!--\"<XSS>={()}",
			"vectorAnswers" => array (
				'<XSS>',
				'<xss>',
				"'';!--\"<XSS>={()}"
			)
		),
		array (
			"vectorValue" => "<XSS></XSS>",
			"vectorAnswers" => array (
				'<xss></xss>',
				'<XSS></XSS>'
			)
		),
		array (
			"vectorValue" => 'xss></xss',
			"vectorAnswers" => array (
				'xss></xss',
				'xss>',
				'</xss',
				'XSS>',
				'</XSS'
			)
		)
	);

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

	/**
	 * Execute method. Starts technique research.
	 * @return array ( Insomnia_Common_Data_XSSCheckTechResult, ...)
	 */
	public function execute() {
		
		// Result of current technique.
		// Form: array ( Insomnia_Common_Data_XSSCheckTechResult, ... )
		$techniqueResults = array();

		// Request hanler that makes requests.
		$requestHandler = new Insomnia_Common_Request_XSSRequestHandler();

		// Checking all vectors for vulnerabilities.
		foreach(self::$uniqueVectors as $vector) {

			// Infects researched param.
			$infectedParamValue = $vector['vectorValue'];

			// Making request data for request with infected values and without.
			// INFECTED REQUEST
			$infectedRequestSet = $this->buildInfectedRequestSet($infectedParamValue);

			// ORIGINAL REQUEST
			$originalRequestSet = Insomnia_Common_Request_Builder_XSSRequestSetBuilder::buildRequestSet($this->researchData["originalURL"]);

			// Doing Infected request, then the Original request and saving reply.
			// INFECTED REQUEST
			$requestHandler->setRequestSet($infectedRequestSet);
			$infectedReplySet = $requestHandler->executeProcess();

			// ORIGINAL REQUEST
			$requestHandler->setRequestSet($originalRequestSet);
			$originalReplySet = $requestHandler->executeProcess();

			// Checking for XSS Facts.
			// checking of infected reply.
			$infectedReplyXSSVulnerable = $this->checkForXSSFacts(
				$infectedReplySet->getBody(), $vector["vectorAnswers"]);
			// checking of original reply.
			$originalReplyXSSVulnerable = $this->checkForXSSFacts(
				$originalReplySet->getBody(), $vector["vectorAnswers"]);

			// Checking for Vulnerable. If true -> vulnerability exists. Then - not.
			if($infectedReplyXSSVulnerable || $originalReplyXSSVulnerable) {

				// Registering standart XSS Type.
				$XSSType = Insomnia_Workflow_Research_XSS_XSSCheckTechnique::XSS_UNKNOWN;

				// Making non infected request/
				$nonInfectedRequestSet = $this->buildNonInfectedRequestSet();

				// Saving reply sets for check on XSS type.

				$requestHandler->setRequestSet($originalRequestSet);
				$originalReplySet = $requestHandler->executeProcess();

				$requestHandler->setRequestSet($nonInfectedRequestSet);
				$nonInfectedReplySet = $requestHandler->executeProcess();

				// Checks type of XSS Vulnerability.
				$XSSType = ( $this->checkForXSSFacts($originalReplySet->getBody(), $vector["vectorAnswers"]) )
					? Insomnia_Workflow_Research_XSS_XSSCheckTechnique::XSS_STORED
					: Insomnia_Workflow_Research_XSS_XSSCheckTechnique::XSS_UNKNOWN;

				if($XSSType === Insomnia_Workflow_Research_XSS_XSSCheckTechnique::XSS_UNKNOWN) {
					$XSSType = ( $this->checkForXSSFacts($nonInfectedReplySet->getBody(), $vector["vectorAnswers"]) )
						? Insomnia_Workflow_Research_XSS_XSSCheckTechnique::XSS_STORED
						: Insomnia_Workflow_Research_XSS_XSSCheckTechnique::XSS_REFLECTED;
				}

				// Saving current result.
				$techniqueResults[] = new Insomnia_Common_Data_XSSCheckTechResult(
					self::$techniqueID,
					$infectedParamValue,
					$infectedReplySet,
					true,
					$XSSType
				);

			} else {
				$techniqueResults[] = new Insomnia_Common_Data_XSSCheckTechResult(
					self::$techniqueID,
					$infectedParamValue,
					$infectedReplySet,
					false,
					Insomnia_Workflow_Research_XSS_XSSCHeckTechnique::XSS_UNKNOWN
				);
			}
		}

		return $techniqueResults;

	}

	// Тут можно почекать и на HTTP REQUEST CODE, и на всю байду. но пока заглушимся простым просмотром на XSS.
	private function checkForXSSFacts($htmlBody, $facts) {
		$XSSExists = false;

		foreach($facts as $fact) {
			if(strpos($htmlBody, $fact) !== false)
				$XSSExists = true;
		}

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