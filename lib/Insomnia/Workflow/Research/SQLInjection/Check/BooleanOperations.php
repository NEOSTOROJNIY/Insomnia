<?php
/**
 * 'Boolean Operations' SQL Injection Check Technique.
 * Makes request with AND / OR operators.
 */
class Insomnia_Workflow_Research_SQLInjection_Check_BooleanOperations
	extends Insomnia_Workflow_Research_SQLInjection_SQLInjectionCheckTechnique
{
	/**
	 * @var public string $techniqueName
	 */
	public static $techniqueName = "Boolean Operations (Blind)";

	/**
	 * @var public integer $techniqueID
	 */
	public static $techniqueID = 4;

	/**
	 * @var public array $quoteCheckers Quotes checkers for testing on the integer/string param.
	 */
	public static $quoteCheckers = array (
		"'" => array ( "' -- ", "') -- ", "')) -- ", "'))) -- " ),
		'"' => array ('" -- ',  '") -- ', '")) -- ', '"))) -- ')
	);

	protected $possibleDBType = 'unknown';

	/**
	 * Quotes Contants.
	 */
	const QUOTE_SINGLEQUOTE = "'";
	const QUOTE_DOUBLEQUOTE = '"';
	const QUOTE_EMPTYQUOTE  = "";


	protected $researchData = array(
		"originalURL"     => "",
		"paramName"       => "",
		"paramValue"      => "",
		"paramPositionID" => null,
		"pathParamFlag"   => null,
		"POSTParamFlag"   => null
	);
	

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
	 * Algorythm:
	 * 1) Insert quotes in param value and check for errors.
	 * 1.A) 1 or more Error rised AND 1 or moreError not rised => using quote, that rised an error and Go Step 2.
	 * 1.B) All errors => dont use Quotes and go Setp 2.
	 * 1.C) No errors => Haven't bug and stop scan. Technique is OFF.
	 * 2) Make request with unique param value wich will return the same result like as the result of request with original param value.
	 * 2.A) Have errors => this technique is OFF.
	 * 2.B) [Page with infected param value] =  [Page without infected param value] => this technique is ON.
	 * 2.C) [Page with infected param value] != [Page without infected param value] => this technique is OFF.
	 */
	public function execute() {

		// Result of current technique.
		// Form: array ( SQLInjectionCheckTechResult, .., ..)
		$techniqueResults = array();

		// <technique off> result.
		$techniqueOFFResult = new Insomnia_Common_Data_SQLInjectionCheckTechResult(
			self::$techniqueID,
			$this->researchData["paramValue"],
			null,
			false,
			'unknown'
		);

		// STEP 1.
		$quoteCheckResult = $this->checkForQuotes();

		// STEP 2.
		if($quoteCheckResult === false) { // NO ERRORS => TECHNIQUE IS OFF
			// STEP 2.A
			$techniqueResults[] = $techniqueOFFResult;

		} else {
			// STEP 2.B/C

			// If boolean operators generates errors => start boolean operator injecting.
			// Otherwise => return <technique off> result.
			if(!$this->booleanSyntaxErrorCheck($quoteCheckResult)) {
				$techniqueResults[] = $techniqueOFFResult;
			} else {
				$results = $this->injectBooleanOperations($quoteCheckResult);
				foreach($results as $techniqueResult)
					$techniqueResults[] = $techniqueResult;
			}
		}

		return $techniqueResults;
	}

	/**
	 * Step 1 Method.
	 * @return string | boolean
	 */
	private function checkForQuotes() {

		$result = self::QUOTE_EMPTYQUOTE;
		
		$requestHandler = new Insomnia_Common_Request_SQLInjectionRequestHandler();

		$singleQuoteCheckersCount = count(self::$quoteCheckers[self::QUOTE_SINGLEQUOTE]);
		$singleQuoteErrorCount = 0; 
		
		$doubleQuoteCheckersCount = count(self::$quoteCheckers[self::QUOTE_DOUBLEQUOTE]);
		$doubleQuoteErrorCount = 0;

		foreach(self::$quoteCheckers as $quoteType => $quoteValueSet) {
			foreach($quoteValueSet as $quoteValue) {
				$requestSet = Insomnia_Common_Request_Builder_SQLInjectionRequestSetBuilder::buildRequestSet(
					$this->researchData["originalURL"],
					array(
						'paramPositionID' => $this->researchData['paramPositionID'],
						'pathParamFlag'   => $this->researchData['pathParamFlag'],
						'paramName'       => $this->researchData['paramName'],
						'paramValue'      => $this->researchData['paramValue'] . $quoteValue
					)
				);

				

				$requestHandler->setRequestSet($requestSet);
				$replySet = $requestHandler->executeProcess();


				$errorCheckResult = $this->checkForErrors($replySet);

				switch($quoteType) {
					case self::QUOTE_SINGLEQUOTE:
						
						if($errorCheckResult['vulnerable'])
							$singleQuoteErrorCount += 1;

						break;

					case self::QUOTE_DOUBLEQUOTE:

						if($errorCheckResult['vulnerable'])
							$doubleQuoteErrorCount += 1;

						break;

					default:
						break;
				}
			}
		}


		if($singleQuoteErrorCount < $singleQuoteCheckersCount)
			$result = self::QUOTE_SINGLEQUOTE;
		elseif($doubleQuoteErrorCount < $doubleQuoteCheckersCount)
			$result = self::QUOTE_DOUBLEQUOTE;

		if($singleQuoteErrorCount == 0)
			$result = false;
		elseif($doubleQuoteErrorCount == 0)
			$result = false;
		
		return $result;	
	}


	/**
	 * @param string $quoteValue Quote Value (', ", _empty_string_ ).
	 * @return boolean
	 */
	public function booleanSyntaxErrorCheck($quoteValue) {

		$errorable = false;

		$operatorANDchecker = $this->researchData['paramValue'] . $quoteValue . " AND 1=";
		$operatorORchecker  = $this->researchData['paramValue'] . $quoteValue . " OR 1=";

		$requestHandler = new Insomnia_Common_Request_SQLInjectionRequestHandler();

		$requestSet_ANDcheker = Insomnia_Common_Request_Builder_SQLInjectionRequestSetBuilder::buildRequestSet(
			$this->researchData['originalURL'],
			array(
				'paramPositionID' => $this->researchData['paramPositionID'],
				'pathParamFlag'   => $this->researchData['pathParamFlag'],
				'paramName'       => $this->researchData['paramName'],
				'paramValue'      => $operatorANDchecker
			)
		);

		$requestSet_ORchecker = Insomnia_Common_Request_Builder_SQLInjectionRequestSetBuilder::buildRequestSet(
			$this->researchData['originalURL'],
			array(
				'paramPositionID' => $this->researchData['paramPositionID'],
				'pathParamFlag'   => $this->researchData['pathParamFlag'],
				'paramName'       => $this->researchData['paramName'],
				'paramValue'      => $operatorORchecker
			)
		);

		$requestHandler->setRequestSet($requestSet_ANDcheker);
		$ANDcheckerReply = $requestHandler->executeProcess();

		$requestHandler->setRequestSet($requestSet_ORchecker);
		$ORcheckerReply = $requestHandler->executeProcess();

		$ANDcheckerResult = $this->checkForErrors($ANDcheckerReply);
		$ORcheckerResult = $this->checkForErrors($ORcheckerReply);

		$errorable = ($ANDcheckerResult['vulnerable'] || $ORcheckerResult['vulnerable']) ? true : false;
		$this->possibleDBType = $ANDcheckerResult['dbVersion'];

		return $errorable;
	}

	/**
	 * Steo 2 Methid.
	 * @return array (SQLInjectionCheckTechResult, .., ..)
	 */
	private function injectBooleanOperations($quoteValue, $similarTextAccuracy = 90) {

		$results = array();

		$booleanInjectorOR  = 
			$this->researchData["paramValue"] . $quoteValue . " OR 1=2 OR "   . $this->researchData["paramName"] . "=" . $quoteValue . $this->researchData["paramValue"];
		
		$booleanInjectorAND = 
			$this->researchData["paramValue"] . $quoteValue . " AND 1=1 AND " . $this->researchData["paramName"] . "=" . $quoteValue . $this->researchData["paramValue"];

		$requestHandler = new Insomnia_Common_Request_SQLInjectionRequestHandler();
		
		// Request with original param value.
		$requestSet_original = Insomnia_Common_Request_Builder_SQLInjectionRequestSetBuilder::buildRequestSet(
			$this->researchData['originalURL'],
			array(
				'paramPositionID' => $this->researchData['paramPositionID'],
				'pathParamFlag'   => $this->researchData['pathParamFlag'],
				'paramName'       => $this->researchData['paramName'],
				'paramValue'      => $this->researchData['paramValue']
			)
		);

		// Request with OR operator injection.
		$requestSet_OR = Insomnia_Common_Request_Builder_SQLInjectionRequestSetBuilder::buildRequestSet(
			$this->researchData['originalURL'],
			array(
				'paramPositionID' => $this->researchData['paramPositionID'],
				'pathParamFlag'   => $this->researchData['pathParamFlag'],
				'paramName'       => $this->researchData['paramName'],
				'paramValue'      => $booleanInjectorOR
			)
		);

		// Request with AND operator injection.
		$requestSet_AND = Insomnia_Common_Request_Builder_SQLInjectionRequestSetBuilder::buildRequestSet(
			$this->researchData['originalURL'],
			array(
				'paramPositionID' => $this->researchData['paramPositionID'],
				'pathParamFlag'   => $this->researchData['pathParamFlag'],
				'paramName'       => $this->researchData['paramName'],
				'paramValue'      => $booleanInjectorAND
			)
		);

		// Original Reply.
		$requestHandler->setRequestSet($requestSet_original);
		$originalRequestReply = $requestHandler->executeProcess();

		// Reply with operator OR.
		$requestHandler->setRequestSet($requestSet_OR);
		$operatorORrequestReply = $requestHandler->executeProcess();
		
		// Reply with operator AND.
		$requestHandler->setRequestSet($requestSet_AND);
		$operatorANDrequestReply = $requestHandler->executeProcess();

		$ORpercent  = 0.0; $ORvulnerable  = false;
		$ANDpercent = 0.0; $ANDvulnerable = false;
		similar_text($originalRequestReply->getBody(), $operatorORrequestReply->getBody(), $ORpercent);
		similar_text($originalRequestReply->getBody(), $operatorANDrequestReply->getBody(), $ANDpercent);

		if($ORpercent  >= $similarTextAccuracy) $ORvulnerable  = true;
		if($ANDpercent >= $similarTextAccuracy) $ANDvulnerable = true;

		// Register OR result.
		$results[] = new Insomnia_Common_Data_SQLInjectionCheckTechResult(
			self::$techniqueID,
			$booleanInjectorOR,
			$operatorORrequestReply,
			$ORvulnerable,
			$this->possibleDBType
		);

		// Register AND result.
		$results[] = new Insomnia_Common_Data_SQLInjectionCheckTechResult(
			self::$techniqueID,
			$booleanInjectorAND,
			$operatorANDrequestReply,
			$ANDvulnerable,
			$this->possibleDBType
		);

		return $results;
	}

	/**
	 * @param ReplyDataSet $replySet
	 * @return array Form: array('vulnerable'=>boolean, 'dbVersion'=>string)
	 */
	private function checkForErrors($replySet) {

		$errorData = array ('vulnerable' => false, 'dbVersion' => 'unknown');

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

		return $errorData;
	}
}