<?php
/**
 * 'Grouping Operations' SQL Injection Check Technique.
 * Makes request with ORDER BY / GROUP BY operators. In results: nothing/grouping_ordering error message/syntax|platform error
 */
class Insomnia_Workflow_Research_SQLInjection_Check_GroupingOperations
	extends Insomnia_Workflow_Research_SQLInjection_SQLInjectionCheckTechnique
{
	/**
	 * @var string $techniqueName
	 */
	public static $techniqueName = "Grouping Operations";

	/**
	 * @var integer $techniqueID
	 */
	public static $techniqueID = 3;

	/**
	 * @var private array $researchData
	 */
	protected $researchData = array (
		"originalURL"     => "",
		"paramName"       => "",
		"paramValue"      => "",
		"paramPositionID" => null,
		"pathParamFlag"   => null,
		"POSTParamFlag"   => null
	);

	private $possibleDBType = "unknown";

	/**
	 * @var private array $operations Collection of error-throwing grouping operations.
	 */
	private static $operations = array (
		" GROUP BY 1839", 		" GROUP BY 1839 -- ",
		"' GROUP BY 1839", 		"' GROUP BY 1839 -- ",
		"\" GROUP BY 1839", 	"\" GROUP BY 1839 -- ",
		") GROUP BY 1839", 		") GROUP BY 1839 -- ",
		"') GROUP BY 1839",	 	"') GROUP BY 1839 -- ",
		"\") GROUP BY 1839", 	"\") GROUP BY 1839 -- ",
		"')) GROUP BY 1839", 	"')) GROUP BY 1839 -- ",
		"\")) GROUP BY 1839", 	"\")) GROUP BY 1839 -- ",
		" ORDER BY 1839", 		" ORDER BY 1839 -- ",
		"' ORDER BY 1839", 		"' ORDER BY 1839 -- ",
		"\" ORDER BY 1839", 	"\" ORDER BY 1839 -- ",
		") ORDER BY 1839", 		") ORDER BY 1839 -- ",
		"') ORDER BY 1839",	 	"') ORDER BY 1839 -- ",
		"\") ORDER BY 1839", 	"\") ORDER BY 1839 -- ",
		"')) ORDER BY 1839", 	"')) ORDER BY 1839 -- ",
		"\")) ORDER BY 1839", 	"\")) ORDER BY 1839 -- "
	);

	private static $operationErrors = array (
		"MySQL" => array (
			"/Unknown column.*in 'order clause'/",		// PHP Platform error
			"/Unknown column.*in 'group statement'/"	// PHP Platform error
		),
		
		"PostgreSQL" => array (
			"/Query error.*ORDER BY/",	// PHP Platform error
			"/Query error.*GROUP BY/"	// PHP Platform error
		)
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
	 * Algorythm:
	 * 1) Inserts GROUP BY / ORDER BY operators in different forms
	 * 2) Cheking results.
	 */
	public function execute() {
		
		// Result of current technique.
		// Form: array( SQLInjectionCheckTechResult, .., ..)
		$techniqueResults = array();

		// Request maker.
		$requestHandler = new Insomnia_Common_Request_SQLInjectionRequestHandler();

		foreach(self::$operations as $operation) {
			// Infects current param value.
			$infectedParamValue = $this->researchData['paramValue'] . $operation;
			
			// Making request with infected data.
			$infectedRequestSet = Insomnia_Common_Request_Builder_SQLInjectionRequestSetBuilder::buildRequestSet(
				$this->researchData['originalURL'],
				array(
					'paramPositionID' => $this->researchData['paramPositionID'],
					'pathParamFlag'   => $this->researchData['pathParamFlag'],
					'paramName'       => $this->researchData['paramName'],
					'paramValue'      => $infectedParamValue
				)
			);

			// Requesting.
			$requestHandler->setRequestSet($infectedRequestSet);
			$replySet = $requestHandler->executeProcess();

			// Check for errors. Return object: array('vulnerable'=>boolean, 'dbVersion'=>string)
			$errorCheckResult = $this->checkForErrors($replySet);

			// Make results and insert results into the result collection.
			$techniqueResults[] = new Insomnia_Common_Data_SQLInjectionCheckTechResult(
				self::$techniqueID,
				$infectedParamValue,
				$replySet,
				$errorCheckResult['vulnerable'],
				$errorCheckResult['dbVersion']
			);
		}

		return $techniqueResults;
	}
	
	/**
	 * @param ReplyDataSet $replySet
	 * @return array Form: array('vulnerable'=>boolean, 'dbVersion'=>string)
	 */
	private function checkForErrors($replySet) {
		$errorData = array ('vulnerable' => false, 'dbVersion' => 'unknown');

		if($replySet->getInfo()['http_code'] === 200) {

			// Checking for special errors.
			foreach(self::$operationErrors as $dbVersion => $operationErrors) {

				foreach($operationErrors as $operationError) {

					$pregResult = preg_match($operationError, $replySet->getBody());
					if($pregResult !== false && $pregResult !== 0) {
						$errorData['vulnerable'] = true;
						$errorData['dbVersion']  = $dbVersion;
						break 2;
					}					
				}
			}

			/*
			// Checking for common errors.
			if(!$errorData['vulnerable']) {
				foreach(self::$errorMessages as $dbVersion => $errorStrings) {

					foreach($errorStrings as $errorString) {

						$pregResult = preg_match($errorString, $replySet->getBody());
						if($pregResult !== false && $pregResult !== 0) {
							$errorData['vulnerable'] = true;
							$errorData['dbVersion']  = $dbVersion;
							break 2;
						}
					}
				}				
			}
			*/
		}

		return $errorData;
	}
}