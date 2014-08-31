<?php
/**
 * 'Numerical Operations' SQL Injection Check Technique.
 * Makes request with numerical values. +2-1/-2+1. Results ==, errors -> resource is vulnerable, nope -> resource is not vulnerable.
 */
class Insomnia_Workflow_Research_SQLInjection_Check_NumericalOperations
	extends Insomnia_Workflow_Research_SQLInjection_SQLInjectionCheckTechnique
{
	/**
	 * @var string $techniqueName
	 */
	public static $techniqueName = "Numerical Operations (Blind)";

	/**
	 * @var integer $techniqueID
	 */
	public static $techniqueID = 2;

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

	private $possibleDBType = 'unknown';

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
	 * 1) Check for the integer type of param value (if integer => step 2, else => return NOT VULNERABLE result).
	 * 2) Infects current param value with values '-1+1' and '+1-1'
	 * 3) Checking results for errors and indetities with original result (it has errors => true, it has identites => true. ELSE => false).
	 * @return array ( Insomnia_Common_Data_SQLInjectionCheckTechResult, ...)
	 */
	public function execute() {

		// Result of current technique.
		// Form: array( SQLInjectionCheckTechResult, .., ..)
		$techniqueResults = array();

		// ALGORYTHM: STEP 1.

		// If the current value is an integer -> its possibly vulnerable.
		// If the current value is NOT an integer -> its NOT VULNERABLE.
		if(!$this->isIntegerParamValue($this->researchData['paramValue'])) { // not vulnerable

			$techniqueResults[] = new Insomnia_Common_Data_SQLInjectionCheckTechResult(
				self::$techniqueID,
				$this->researchData['paramValue'],
				null,
				false,
				"unknown"
			);

		} else { // possibly vulnerable. Checking is continue.

			// ALGORYTHM: STEP 2.

			$originalParamValue = $this->researchData['paramValue'];

			// infects the original value by '+1'/'-1' string addition.
			$increased_infectedParamValue = ($originalParamValue + 1) . '-1';
			$reduced_infectedParamValue   = ($originalParamValue - 1) . '+1';


			// Request handler which makes requests.
			$requestHandler = new Insomnia_Common_Request_SQLInjectionRequestHandler();

			// Making request sets with Original param value and Infected param value.
			// ORIGINAL
			$originalRequestSet = Insomnia_Common_Request_Builder_SQLInjectionRequestSetBuilder::buildRequestSet(
				$this->researchData["originalURL"],
				array(
					'paramPositionID' => $this->researchData['paramPositionID'],
					'pathParamFlag'   => $this->researchData['pathParamFlag'],
					'paramName'       => $this->researchData['paramName'],
					'paramValue'      => $originalParamValue
				)
			);

			// INCREASED BY 1
			$increase_infectedRequestSet = Insomnia_Common_Request_Builder_SQLInjectionRequestSetBuilder::buildRequestSet(
				$this->researchData["originalURL"],
				array(
					'paramPositionID' => $this->researchData['paramPositionID'],
					'pathParamFlag'   => $this->researchData['pathParamFlag'],
					'paramName'       => $this->researchData['paramName'],
					'paramValue'      => $increased_infectedParamValue
				)
			);

			// REDUCED BY 1
			$reduce_infectedRequestSet = Insomnia_Common_Request_Builder_SQLInjectionRequestSetBuilder::buildRequestSet(
				$this->researchData["originalURL"],
				array(
					'paramPositionID' => $this->researchData['paramPositionID'],
					'pathParamFlag'   => $this->researchData['pathParamFlag'],
					'paramName'       => $this->researchData['paramName'],
					'paramValue'      => $reduced_infectedParamValue
				)
			);


			// getting reply of original value.
			$requestHandler->setRequestSet($originalRequestSet);
			$originalReplySet = $requestHandler->executeProcess();

			// getting reply of increased original value ($increased_infectedParamValue)
			$requestHandler->setRequestSet($increase_infectedRequestSet);
			$increased_infectedReplySet = $requestHandler->executeProcess();

			// getting reply of reduced original value ($reduced_infectedParamValue)
			$requestHandler->setRequestSet($reduce_infectedRequestSet);
			$reduced_infectedReplySet = $requestHandler->executeProcess();

			// ALGORYTHM: STEP 3.

			// checking INCREASED_infectedReplySet for ERRORS, then for IDENTITY with ORIGINAL REPLYSET
			$vuln_increased_infectedParamValue = $this->checkingForErrorsAndIdentity( $increased_infectedReplySet, $originalReplySet );

			// checking REDUCED_infectedReplySet for ERRORS, then for IDENTITY with ORIGINAL REPLYSET
			$vuln_reduced_infectedParamValue = $this->checkingForErrorsAndIdentity( $reduced_infectedReplySet, $originalReplySet );

			// Add INCRASED reuslts
			$techniqueResults[] = new Insomnia_Common_Data_SQLInjectionCheckTechResult(
				self::$techniqueID,
				$increased_infectedParamValue,
				$increased_infectedReplySet,
				$vuln_increased_infectedParamValue,
				$this->possibleDBType
			);

			// Add REDUCED results
			$techniqueResults[] = new Insomnia_Common_Data_SQLInjectionCheckTechResult(
				self::$techniqueID,
				$reduced_infectedParamValue,
				$reduced_infectedReplySet,
				$vuln_reduced_infectedParamValue,
				$this->possibleDBType
			);
		}

		// END OF ALGORYTHM
		
		return $techniqueResults;
	}

	/**
	 * Method which checks infected reply for contains the error message and for identity with original reply.
	 * Return flag wich signals that current tested param is vulnerable.
	 * @param Insomnia_Request_reply_ReplyDataSet $infectedReplySet
	 * @param Insomnia_Request_reply_ReplyDataSet $originalReplySet
	 * @return boolean
	 */
	private function checkingForErrorsAndIdentity($infectedReplySet, $originalReplySet) {
		$vulnerable = false;

		$vulnerable = 
			$this->checkOutputForErrors($infectedReplySet)
			? true
			: $this->compareRequestBodyResults( $originalReplySet->getBody(), $infectedReplySet->getBody() )
				? true
				: false;

		return $vulnerable;
	}


	/**
	 * Method that checks param $value on the integer type.
	 * If the current value is an integer -> its possibly vulnerable.
	 * If the current value is NOT an integer -> its NOT VULNERABLE.
	 * @param string $value Value which should be checked for the integer type.
	 * @return boolean
	 */
	private function isIntegerParamValue($value) {
		// result value
		$integerValueFlag = false;

		// checking all matches.
		preg_match_all("/[0-9]/", $value, $matches);
		// length of $value
		$valueLength = strlen($value);

		// if matches count == value's str length => possibly vulenrable.
		// if matches count !== value's str length => NOT VULNERABLE
		$integerValueFlag = ( count($matches[0]) == $valueLength ) ? true : false;

		return $integerValueFlag;
	}

	/** 
	* Methoth which checks: if the original request result and infected request result approximately identical.
	* @param string $originalRequestResultBody Body of request with original tested param value.
	* @param string $infectedRequestResultBody Body of request with infected tested param value.
	* @return boolean
	*/
	private function compareRequestBodyResults($originalRequestResultBody, $infectedRequestResultBody, $accuracy = 98) {
		// result
		$approximatelyIdentical = false;	

		//checking for identity
		$percentIdentity = 0.0;
		similar_text($originalRequestResultBody, $infectedRequestResultBody, $percentIdentity);

		// if the identity percentage is more than 98, then this request bodies is identity [true], otherwise is not identity [false].
		$approximatelyIdentical = ($percentIdentity >= $accuracy) ? true : false;

		return $approximatelyIdentical;
	}

	/**
	 * Method wich checks the replyBody for vulnerability facts.
	 * @param Insomnia_Request_reply_ReplyDataSet $replySet
	 * @return array
	 */
	private function checkOutputForErrors($replySet) {
		
		$errorFlag = false;

		if($replySet->getInfo()['http_code'] === 200) {
			foreach(self::$errorMessages as $dbType => $errorStrings) {
				foreach($errorStrings as $errorString) {
					$pregResult = preg_match($errorString, $replySet->getBody());
					if($pregResult !== false && $pregResult !== 0) {
						$errorFlag = true;
						$this->possibleDBType = $dbType;
						break 2;
					}
						
				}
			}
		}

		return $errorFlag;

	}
}