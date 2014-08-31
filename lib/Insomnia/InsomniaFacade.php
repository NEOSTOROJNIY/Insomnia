<?php
class Insomnia_InsomniaFacade
{
	const TASKTYPE_SQLINJECTION = "sqlinjection";
	const TASKTYPE_XSS			= "xss";
	const TASKTYPE_GENERAL		= "general";

	private $projectSavingPath;
	private $reportSavingPath;
	private $statisticsDBPath;

	public function __construct($projectSavingPath, $reportSavingPath, $statisticsDBPath) {
		$this->projectSavingPath = $projectSavingPath;
		$this->reportSavingPath  = $reportSavingPath;
		$this->statisticsDBPath  = $statisticsDBPath;
	}

	private function parseTargetsData($targetsJSONDataSet) {
		$targetsDataSet = json_decode($targetsJSONDataSet, true);
		$targets = array();
		for($i = 0; $i < count($targetsDataSet['res']); ++$i) {
			$targets[] = new Insomnia_Environment_Target(
				$targetsDataSet['res'][$i], // resource
				($targetsDataSet['xss'][$i] === "true") ? true : false, // xss testing flag (true - need to test, false - not need)
				($targetsDataSet['sqli'][$i] === "true") ? true : false // sqlinjection testing flag (true - need to test, false - not need)
			);
		}

		return $targets;		
	}

	/**
	 * Method which start tests under targets.
	 * Returns the report which contains full test statitics.
	 * @param string $testingData JSON-formed data for testing (URLs, task flags).
	 * @param string $reportsSavingPath Path for the report files.
	 * @return string (json form)
	 */
	public function startTesting($testingData) {
		$taskResults = array();

		foreach($this->parseTargetsData($testingData) as $target) {

			$taskResultSet = new Insomnia_Environment_TaskResultSet();

			$taskResultSet->setResourceURL($target->getResourceURL());
			$taskResultSet->setResourceIP($target->getResourceIP());

			// If the current target is assigned to SQL Injection test - > start testing and colelct Data into TaskResultSet Object
			if( $target->isAssignedToSQLInjectionTest()) {

				$sqliResult = $this->testForSQLInjection( $target->getResourceURL() );
				$taskResultSet->setSQLInjectionTaskResult($sqliResult);
				$taskResultSet->setResourceURL( $sqliResult->getResourceURL() );
				$taskResultSet->setResourceIP( $sqliResult->getResourceIP() );
			}

			// If the current target is assigned to XSS test - > start testing and colelct Data into TaskResultSet Object
			if( $target->isAssignedToXSSTest() ) {

				$xssResult = $this->testForXSS( $target->getResourceURL() );
				$taskResultSet->setXSSTaskResult($xssResult);
				$taskResultSet->setResourceURL( $xssResult->getResourceURL() );
				$taskResultSet->setResourceIP( $xssResult->getResourceIP() );
			}

			// Set the server banner data.
			$taskResultSet->setServerBanner( $this->getServerBanner( $target->getResourceURL() ) );

			// Saving results.
			$taskResults[] = $taskResultSet;
		}

		// Making reports [json form], (XML)
		// JSON FORM: { 'resource' : __, 'securityLevel' : __, 'vulnerabilities': array, 'reportLinks': array }
		$reports = array();
		// ПЕРЕПИСАТЬ В СТАТИЧЕСКИЙ ВИД, ЧТОБЫ Я ДЕЛАЛ ТАК:
		// Insomnia_Environment_Reporting_CompletedReportBuilder::getReport($taskResult, _data_folders_ );
		foreach($taskResults as $taskResult) {
			$reportBuilder = new Insomnia_Environment_Reporting_CompletedReportBuilder($taskResult, $this->reportSavingPath, $this->statisticsDBPath);
			$reports[] = json_decode($reportBuilder->getReport(), true);
		}

		$JSONFormOfReportsCollection = json_encode($reports, JSON_UNESCAPED_SLASHES);

		return $JSONFormOfReportsCollection;
	}

	/**
	 * @param string $resource URL of the investigate resource.
	 */
	private function testForSQLInjection($resource) {

		$sqliRawData = new Insomnia_Common_Data_SQLInjectionRawData( $resource );
		$sqliTask    = new Insomnia_Workflow_SQLInjectionTask($sqliRawData);
		$sqliResult  = $sqliTask->execute();
		return $sqliResult;
	}

	/**
	 * @param string $resource URL of the investigate resource.
	 */
	private function testForXSS($resource) {

		$xssRawData = new Insomnia_Common_Data_XSSRawData( $resource );
		$xssTask    = new Insomnia_Workflow_XSSTask($xssRawData);
		$xssResult  = $xssTask->execute();
		return $xssResult;
	}

	private function getServerBanner($resourceURL) {
		$requestHandler = new Insomnia_Common_Request_SQLInjectionRequestHandler();
		$requestSet = Insomnia_Common_Request_Builder_SQLInjectionRequestSetBuilder::buildRequestSet( $resourceURL );
		$requestHandler->setRequestSet($requestSet);
		$replySet = $requestHandler->executeProcess();
		$serverBanner = isset($replySet->getHeaders()['Server']) ? $replySet->getHeaders()['Server'] : "NONE";

		return $serverBanner;
	}

	/**
	 * @param string $fileSavingData JSON-formed data that necessary should be saved.
	 * @param string $fullSavePath Path where file should to be saved.
	 */
	public function saveProjectFile($fileSavingData) {
		
		$result = '';

		$fileData = json_decode($fileSavingData, true);
		$fullSavePath = $this->projectSavingPath . DIRECTORY_SEPARATOR . base64_encode(htmlspecialchars($fileData['fileName'])) . '.xml';

		if(file_exists($fullSavePath)) {
			$result = json_encode( array('error' => 'The file with this name is already exists'));
		} else {
			$xmlHandler = new DomDocument('1.0', 'utf-8');

			$projectEntity = $xmlHandler->createElement('project');
			$taskSetEntity = $xmlHandler->createElement('tasks');

			for($i = 0; $i < count($fileData['res']); ++$i) {
				$taskEntity = $xmlHandler->createElement('task');
				$taskURLAttributeEntity = $xmlHandler->createAttribute('url');
				$taskURLAttributeEntity->appendChild($xmlHandler->createTextNode($fileData['res'][$i]));
				$taskEntity->appendChild($taskURLAttributeEntity);

				$assignedToSQLInjectionEntity = $xmlHandler->createElement('assignedtosqli');
				$assignedToSQLInjectionEntity->appendChild($xmlHandler->createTextNode($fileData['sqli'][$i]));
				$taskEntity->appendChild($assignedToSQLInjectionEntity);

				$assignedToXSSEntity = $xmlHandler->createElement('assignedtoxss');
				$assignedToXSSEntity->appendChild($xmlHandler->createTextNode($fileData['xss'][$i]));
				$taskEntity->appendChild($assignedToXSSEntity);

				$taskSetEntity->appendChild($taskEntity);
			}	
					
			$projectEntity->appendChild($taskSetEntity);
			$xmlHandler->appendChild($projectEntity);

			$xmlHandler->save($fullSavePath);

			$result = json_encode( array('complete' => 'File successfully saved!'), JSON_UNESCAPED_SLASHES );
		}

		return $result;
	}

	public function getProjectList() {

			$projectPool = $this->projectSavingPath;

			$projectsData = array_map(
				function($fileName) use($projectPool) {

					$fixedFileName = substr($fileName, (3 + strlen($projectPool)));
					$fixedFileName = substr($fixedFileName, 0, strpos($fixedFileName, ".xml"));

					return base64_decode($fixedFileName);
				},
				glob( "./" . $projectPool . "/*.xml" )
			);	

			return json_encode($projectsData, JSON_UNESCAPED_SLASHES);
	}

	public function deleteProject($fileName) {
		@unlink( '.' . DIRECTORY_SEPARATOR . $this->projectSavingPath . DIRECTORY_SEPARATOR . base64_encode(htmlspecialchars($fileName)) . '.xml' );
	}

	public function getProject($fileName) {
		$projectFile = @file_get_contents( './' . $this->projectSavingPath . DIRECTORY_SEPARATOR . base64_encode(htmlspecialchars($fileName)) . '.xml');

		return ($projectFile === false)
			? "<?xml version=\"1.0\" encoding=\"utf-8\"?><error>File is not exists</error>"
			: $projectFile;
	}


	public function getReportList() {

		$reportPool = $this->reportSavingPath;

		$reportFileData = array_map(
			function($fileName) use($reportPool) {
				$fixedFileName = substr($fileName, (3 + strlen($reportPool)));
				$filenameData = array( 
					'fileName' => $fixedFileName,
					'fileLink' => 'http://' . $_SERVER['HTTP_HOST'] . '/' . $reportPool . '/' . $fixedFileName
				);

				return $filenameData;
			},
			glob( "./" . $reportPool . "/*.xml" )
		);

		return json_encode($reportFileData, JSON_UNESCAPED_SLASHES);
	}

	public function getReport( $fileName) {

		$reportFile = @file_get_contents( './' . $this->reportSavingPath . DIRECTORY_SEPARATOR . $fileName );

		return ($reportFile === false)
			? "<?xml version=\"1.0\" encoding=\"utf-8\"?><error>File is not exists</error>"
			: $reportFile;
	}

	public function deleteReport( $fileName) {
		@unlink( '.' . DIRECTORY_SEPARATOR . $this->reportSavingPath . DIRECTORY_SEPARATOR . $fileName );
	}

	public function getGeneralStatistics() {
		//returns SQLInjectionStatistics Table Data
		//returns XSSStatistics Table Data
		//returns GeneralStatistics Table Data
		$statisticsDBHandler = new SQLite3($this->statisticsDBPath);

		$statisticsData = array (
			"GeneralStats" => array (
				"totalCount"       => 0,
				"successCount"     => 0,
				"vulnerableCount"  => 0,
				"exploitableCount" => 0
			),

			"SQLInjectionStats" => array (
				"totalCount"       => 0,
				"successCount"     => 0,
				"vulnerableCount"  => 0,
				"exploitableCount" => 0			
			),

			// form: techniqueName => techniqueCount, .., .., ..
			"techniqueStats_SQLInjection" => array (
			),

			"XSSStats" => array (
				"totalCount"       => 0,
				"successCount"     => 0,
				"vulnerableCount"  => 0,
				"exploitableCount" => 0	
			),

			// form: techniqueName => techniqueCount, .., .., ..
			"techniqueStats_XSS" => array (
			)
		);

		$results = $statisticsDBHandler->query('SELECT * FROM GeneralStats');
		while($row = $results->fetchArray()) {
			$statisticsData["GeneralStats"]["totalCount"]       = $row["total_count"];
			$statisticsData["GeneralStats"]["successCount"]     = $row["success_count"];
			$statisticsData["GeneralStats"]["vulnerableCount"]  = $row["vulnerable_count"];
			$statisticsData["GeneralStats"]["exploitableCount"] = $row["exploitable_count"];
		}

		$results = $statisticsDBHandler->query('SELECT * FROM SQLInjecitonStats');
		while($row = $results->fetchArray()) {
			$statisticsData["SQLInjectionStats"]["totalCount"]       = $row["total_count"];
			$statisticsData["SQLInjectionStats"]["successCount"]     = $row["success_count"];
			$statisticsData["SQLInjectionStats"]["vulnerableCount"]  = $row["vulnerable_count"];
			$statisticsData["SQLInjectionStats"]["exploitableCount"] = $row["exploitable_count"];
		}

		$results = $statisticsDBHandler->query('SELECT * FROM SQLInjectionTechniqueStats');
		while($row = $results->fetchArray()) {
			$statisticsData["techniqueStats_SQLInjection"][] = array (
				"techniqueName"  => $row["technique_name"],
				"techniqueCount" => $row["technique_count"]
			);
		}

		$results = $statisticsDBHandler->query('SELECT * FROM XSSStats');
		while($row = $results->fetchArray()) {
			$statisticsData["XSSStats"]["totalCount"]       = $row["total_count"];
			$statisticsData["XSSStats"]["successCount"]     = $row["success_count"];
			$statisticsData["XSSStats"]["vulnerableCount"]  = $row["vulnerable_count"];
			$statisticsData["XSSStats"]["exploitableCount"] = $row["exploitable_count"];
		}

		$results = $statisticsDBHandler->query('SELECT * FROM XSSTechniqueStats');
		while($row = $results->fetchArray()) {
			$statisticsData["techniqueStats_XSS"][] = array (
				"techniqueName"  => $row["technique_name"],
				"techniqueCount" => $row["technique_count"]
			);
		}

		return json_encode($statisticsData, true);
	}

	public function getSQLInjectionStatistics() {

		$statisticsDBHandler = new SQLite3($this->statisticsDBPath);
		$statisticsData = array (
			"SQLInjectionStats" => array (
			"totalCount"       => 0,
			"successCount"     => 0,
			"vulnerableCount"  => 0,
			"exploitableCount" => 0			
			),

			// form: techniqueName => techniqueCount, .., .., ..
			"techniqueStats_SQLInjection" => array (
			)
		);

		$results = $statisticsDBHandler->query('SELECT * FROM SQLInjecitonStats');
		while($row = $results->fetchArray()) {
			$statisticsData["SQLInjectionStats"]["totalCount"]       = $row["total_count"];
			$statisticsData["SQLInjectionStats"]["successCount"]     = $row["success_count"];
			$statisticsData["SQLInjectionStats"]["vulnerableCount"]  = $row["vulnerable_count"];
			$statisticsData["SQLInjectionStats"]["exploitableCount"] = $row["exploitable_count"];
		}

		$results = $statisticsDBHandler->query('SELECT * FROM SQLInjectionTechniqueStats');
		while($row = $results->fetchArray()) {
			$statisticsData["techniqueStats_SQLInjection"][] = array (
				"techniqueName"  => $row["technique_name"],
				"techniqueCount" => $row["technique_count"]
			);
		}

		return json_encode($statisticsData, true);		
	}

	public function getXSSStatistics() {
		$statisticsDBHandler = new SQLite3($this->statisticsDBPath);
		$statisticsData = array (
			"XSSStats" => array (
				"totalCount"       => 0,
				"successCount"     => 0,
				"vulnerableCount"  => 0,
				"exploitableCount" => 0	
			),

			// form: techniqueName => techniqueCount, .., .., ..
			"techniqueStats_XSS" => array (
			)
		);

		$results = $statisticsDBHandler->query('SELECT * FROM XSSStats');
		while($row = $results->fetchArray()) {
			$statisticsData["XSSStats"]["totalCount"]       = $row["total_count"];
			$statisticsData["XSSStats"]["successCount"]     = $row["success_count"];
			$statisticsData["XSSStats"]["vulnerableCount"]  = $row["vulnerable_count"];
			$statisticsData["XSSStats"]["exploitableCount"] = $row["exploitable_count"];
		}

		$results = $statisticsDBHandler->query('SELECT * FROM XSSTechniqueStats');
		while($row = $results->fetchArray()) {
			$statisticsData["techniqueStats_XSS"][] = array (
				"techniqueName"  => $row["technique_name"],
				"techniqueCount" => $row["technique_count"]
			);
		}

		return json_encode($statisticsData, true);	
	}

	public function clearStatistics() {
		@unlink($this->statisticsDBPath);
	}
}