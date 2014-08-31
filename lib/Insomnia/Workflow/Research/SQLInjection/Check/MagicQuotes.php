<?php
/**
 * 'Mgic Quotes' SQL Injection Check Technique.
 * Inserts quotes symbols into param and checking the result for vulnerabilities.
 */
class Insomnia_Workflow_Research_SQLInjection_Check_MagicQuotes 
	extends Insomnia_Workflow_Research_SQLInjection_SQLInjectionCheckTechnique
{
	/**
	 * @var string $techniqueName
	 */
	public static $techniqueName = "Magic Quotes";

	/**
	 * @var integer $techniqueID
	 */
	public static $techniqueID = 0;

	/**
	 * @var private array (string, string, ...) $magicQuotesSet Set of quotes.
	 */
	private static $magicQuotesSet = array ("'", "\"", "/*", "'/*", "' -- ", ")/*", ") --", "\"/*", "#");

	/**
	 * @var private array $researchData
	 */
	private $researchData = array (
		"originalURL"     => "",
		"paramName"       => "",
		"paramValue"      => "",
		"paramPositionID" => null,
		"pathParamFlag"   => null,
		"POSTParamFlag"   => null
	);

	/**
	 * @param string $originalURL Original Request URL (without infected params).
	 * @param string $paramName Researched param name.
	 * @param string $paramValue Original param value.
	 * @param integer $paramPositionID Position of researched param in URL.
	 * @param boolean $pathParamFlag Flag which signals that current param is Path param.
	 * @param boolean $POSTParamFlag Flag which signals that current param is POST param.
	 */
	public function __construct(
		$originalURL, 
		$paramName,
		$paramValue,
		$paramPositionID, 
		$pathParamFlag, 
		$POSTParamFlag) {

		$this->researchData["originalURL"]     = $originalURL;
		$this->researchData["paramName"]       = $paramName;
		$this->researchData["paramValue"]      = $paramValue;
		$this->researchData["paramPositionID"] = $paramPositionID;
		$this->researchData["pathParamFlag"]   = $pathParamFlag;
		$this->researchData["POSTParamFlag"]   = $POSTParamFlag;
	}

	/**
	 * Execute method. Starts technique research.
	 * @return array ( Insomnia_Common_Data_SQLInjectionCheckTechResult, ...)
	 */
	public function execute() {
		
		// Result of current technique. 
		// Form: array( SQLInjectionCheckTechResult, SQLInjectionCheckTechResult, ... ).
		$techniqueResults = array();

		// Request handler that makes requests.
		$requestHandler = new Insomnia_Common_Request_SQLInjectionRequestHandler();

		// Checking all magic quotes for vulnerabilities.
		foreach(self::$magicQuotesSet as $magicQuote) {

			// Infects researched param.
			$infectedParamValue = $this->researchData["paramValue"] . $magicQuote;


			//if($this->researchData["POSTParamFlag"]) Необходимо будет сделать.

			// Making request data for request with infected values.
			// ONLY FOR [GET] REQUESTS. NOT FOR [POST].
			// POST REQUEST IS NOT READY NOW.
			$requestSet = Insomnia_Common_Request_Builder_SQLInjectionRequestSetBuilder::buildRequestSet(
				$this->researchData["originalURL"],
				array(
					'paramPositionID' => $this->researchData["paramPositionID"],
					'pathParamFlag'   => $this->researchData["pathParamFlag"],
					'paramName'       => $this->researchData["paramName"],
					'paramValue'      => $infectedParamValue
				)
			);

			// Registration of request set.
			$requestHandler->setRequestSet($requestSet);

			// Making request and getting reply.
			$replySet = $requestHandler->executeProcess();

			// Checking reply for errors.
			$errorCheckData = $this->checkOutput($replySet);

			// Add checking result into result set.
			$techniqueResults[] = new Insomnia_Common_Data_SQLInjectionCheckTechResult(
				self::$techniqueID,
				$infectedParamValue,
				$replySet,
				$errorCheckData['vulnerable'],
				$errorCheckData['dbType']
			);
		}

		return $techniqueResults;
	}

	/**
	 * This method checks reply body for vulnerability facts.
	 * @param Insomnia_Request_Reply_ReplyDataSet $replySet
	 * @return array
	 */
	private function checkOutput($replySet) {

		$checkResult = array( "dbType" => "unknown", "vulnerable" => false);

		if($replySet->getInfo()['http_code'] === 200)
		{
			// ERROR-BASED check.
			foreach(self::$errorMessages as $dbType => $errorStrings) {

				foreach($errorStrings as $errorString) {
					$pregResult = preg_match($errorString, $replySet->getBody());
					if( $pregResult !== false && $pregResult !== 0) {
						$checkResult['dbType']     = $dbType;
						$checkResult['vulnerable'] = true;
						break 2; // quit from cycles.
					}
				}
			}			
		}

		return $checkResult;
	}
}