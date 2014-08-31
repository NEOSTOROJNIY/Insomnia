<?php
/**
 * Universal Report Builder.
 * Takes two params:
 * 1) Insomnia_Environment_TaskResultSet
 * 2) Path to save
 */
class Insomnia_Environment_Reporting_CompletedReportBuilder
{
	/**
	 * @var resource $xmlHandler XML Object Handler. Maked by DomDocument Class (included in php).
	 */
	private $xmlHandler;

	/**
	 * @var resource $xmlRoot XML Object handler, maked from $xmlHandler.
	 */
	private $xmlRoot;

	/**
	 * @var Insomnia_Environment_TaskResultSet $taskResultSet Task Result set (test results).
	 */
	private $taskResultSet;

	/**
	 * @var string $savePath Path to  save the completed report file.
	 */
	private $savePath;

	/**
	 * @var string $statisticsDBFilePath Path of SQLite DB file with the scan statistics.
	 */
	private $statisticsDBFilePath;

	/**
	 * @var Insomnia_Environment_Reporting_XSSReport|boolean $XSSReport Report of XSS task. If results of XSS in $taskResultSet is not exists, this parametr takes "FALSE" boolean value.
	 */
	private $XSSReport;

	/**
	 * @var Insomnia_Environment_Reporting_SQLInjectionReport|boolean $SQLInjectionReport If results of SQL Injection in $taskResult is not exists, this parametr takes "FALSE" boolean value
	 */
	private $SQLInjectionReport;

	/**
	 * @var integer $securityLevel Complex Security Level, based on Security level of all tested vulnerabilities.
	 */
	private $securityLevel;

	/**
	 * @var array $testedVulnerabilities Array of tested vulnerabilities info. Form: array ( array( 'vulnerability' => string:type, 'securityLevel' => integer:securityLevel ), ..., ..., ...)
	 */
	private $testedVulnerabilities = array();

	/**
	 * @var integer SECURITYLEVEL_* Security Level. Green: good, Yellow: bad, Red: OMFG!!!
	 */
	const SECURITYLEVEL_GREEN  		= 3;
	const SECURITYLEVEL_YELLOW 		= 2;
	const SECURITYLEVEL_RED	   		= 1;
	const SECURITYLEVEL_GREY		= -1;

	/**
	 * @param Insomnia_Environment_TaskResultSet $taskResultSet Task Result set of teste (test results).
	 * @param string $savePath Path to save the completed report file.
	 */
	public function __construct($taskResultSet, $savePath, $statisticsDBFilePath = "") {

		$this->taskResultSet            = $taskResultSet;
		$this->savePath                 = $savePath;
		$this->xmlHandler               = new DomDocument('1.0', 'utf-8');
		$this->xmlHandler->formatOutput = true;
		$this->statisticsDBFilePath 	= $statisticsDBFilePath;

		$this->xmlRoot = $this->xmlHandler->createElement('report');

		$this->XSSReport = ($taskResultSet->getXSSTaskResult() !== false)
			? new Insomnia_Environment_Reporting_XSSReport( 
				$taskResultSet->getXSSTaskResult()->getResourceURL(), 
				$taskResultSet->getXSSTaskResult()->getResourceIP(), 
				$taskResultSet->getXSSTaskResult() )
			: false;

		$this->SQLInjectionReport = ($taskResultSet->getSQLInjectionTaskResult() !== false)
			? new Insomnia_Environment_Reporting_SQLInjectionReport( 
				$taskResultSet->getSQLInjectionTaskResult()->getResourceURL(), 
				$taskResultSet->getSQLInjectionTaskResult()->getResourceIP(), 
				$taskResultSet->getSQLInjectionTaskResult() )
			: false;

		$this->initializeSecurityLevel();
		$this->initializeTestedVulnerabilities();

		$this->registerStatisticalResults();
	}

	/**
	 * Makes complex Security Level.
	 */
	public function initializeSecurityLevel() {
		// Standart level (GREEN).
		$this->securityLevel = self::SECURITYLEVEL_GREEN;


		if($this->XSSReport !== false) {
			$xssSecurityLevel = $this->XSSReport->getSecurityLevel();
			if($xssSecurityLevel < $this->securityLevel)
				$this->securityLevel = $xssSecurityLevel;
		}
			
		if($this->SQLInjectionReport !== false) {
			$sqlinjSecurityLevel = $this->SQLInjectionReport->getSecurityLevel();
			if($sqlinjSecurityLevel < $this->securityLevel)
				$this->securityLevel = $sqlinjSecurityLevel;
		}
			
	}

	public function getSecurityLevel() {
		return $this->securityLevel;
	}

	public function registerStatisticalResults() {

		$statRegger = new Insomnia_Environment_Statistics_StatisticsRegister();

		$statRegger->registerGeneralStatistics(
			$this->statisticsDBFilePath,
			$this->getSecurityLevel()
		);

		if($this->XSSReport !== false) {
			$statRegger->registerXSSStatistics(
				$this->statisticsDBFilePath,
				$this->XSSReport->getSecurityLevel()
			);

			foreach($this->XSSReport->getTechniqueList() as $techniqueType) {
				foreach($techniqueType as $techniqueName) {
					$statRegger->registerXSSTechniqueStatistics(
						$this->statisticsDBFilePath,
						$techniqueName
					);
				}
			}
		}


		if($this->SQLInjectionReport !== false) {
			$statRegger->registerSQLInjectionStatistics(
				$this->statisticsDBFilePath,
				$this->SQLInjectionReport->getSecurityLevel()
			);

			foreach($this->SQLInjectionReport->getTechniqueList() as $techniqueType) {
				foreach($techniqueType as $techniqueName) {
					$statRegger->registerSQLInjectionTechniqueStatistics(
						$this->statisticsDBFilePath,
						$techniqueName
					);
				}
			}
		}


	}

	/**
	 * Collect all tested vulnerabilities and thoose levels.
	 */
	public function initializeTestedVulnerabilities() {

		if($this->XSSReport !== false)
			$this->testedVulnerabilities[] = array ('vulnerability' => 'XSS', 'securityLevel' => $this->XSSReport->getSecurityLevel() );
		if($this->SQLInjectionReport !== false)
			$this->testedVulnerabilities[] = array ('vulnerability' => 'SQL Injection', 'securityLevel' => $this->SQLInjectionReport->getSecurityLevel() );
	}

	private function filtrateData($param) {

		if(is_array($param)) {
			for($i = 0; $i < count($param); ++$i)
				$param[$i] = htmlspecialchars($param[$i]);
		} else {
			$param = htmlspecialchars($param);
			$param = str_replace('[', '', $param);//$param = str_replace('[', '&#91;', $param);
			$param = str_replace(']', '', $param);//$param = str_replace(']', '&#93;', $param);			
		}

		return $param;
	}

	/**
	 * Makes reports in forms: XML, PDF, JSON (completed mini report).
	 * Returns JSON report with links to XML and PDF reports.
	 * @return string (json  form)
	 */
	public function getReport() {
		$xmlReportFileLink = $this->generateXMLReport();

		$JSONMiniReport = $this->generateJSONMiniReport($xmlReportFileLink);

		return $JSONMiniReport;
	}

	private function generateReportFilePrefix() {
		return date("Y-m-d_H-i-s") . "-" . rand();
	}

	/**
	 * Makes XML report file. Returns the link to XML report file.
	 * @return string
	 */
	private function generateXMLReport() {
		// Adds Date Block.
		$this->XML_addDate();

		// Adds Resource Block.
		$this->XML_addResource($this->taskResultSet->getResourceURL(), $this->taskResultSet->getResourceIP(), $this->taskResultSet->getServerBanner() );

		// Adds Complex Security Level Block.
		$this->XML_addSecurityLevel($this->securityLevel);

		// Adds block which contains Info about tested vulnerabilities.
		$this->XML_addTestedVulnerabilities();

		// Adds block which contains details about scan.
		$this->XML_addScanDetails();

		// Adds all blocks in the general xml-container.
		$this->xmlHandler->appendChild($this->xmlRoot);

		// Generating and saving the XML File.
		$xmlReportFilePath = $this->savePath . DIRECTORY_SEPARATOR . $this->generateReportFilePrefix() . '.xml';


		$xmlReportFileLink = 'http://' . $_SERVER['HTTP_HOST'] . '/' . $xmlReportFilePath;
		$xmlReportFileLink = str_replace('\\', '/', $xmlReportFileLink);


		$this->xmlHandler->save($xmlReportFilePath);

		return $xmlReportFileLink;
	}

	private function generatePDFReport() {
	}

	/**
	 * Makes JSON-stringified Mini Version of completed report.
	 * @return string (json-form)
	 */
	private function generateJSONMiniReport($xmlReportFileLink) {

		$testedVulnerabilities = array();
		
		// Adds XSS test results.
		$testedVulnerabilities[] = array (
			'vulnerability' => 'XSS',
			'securityLevel' => 
					($this->XSSReport !== false) 
						? $this->XSSReport->getSecurityLevel()
						: self::SECURITYLEVEL_GREY
		);

		// Adds SQL Injection test results.
		$testedVulnerabilities[] = array (
			'vulnerability' => 'SQL Injection',
			'securityLevel' => 
					($this->SQLInjectionReport !== false)
						? $this->SQLInjectionReport->getSecurityLevel()
						: self::SECURITYLEVEL_GREY
		);

		$miniReport = array(
			'resource'        => $this->taskResultSet->getResourceURL(),
			'securityLevel'   => $this->securityLevel,
			'vulnerabilities' => $testedVulnerabilities,
			'reportLinks' => array (
				'xmlReportLink' => $xmlReportFileLink
			)
		);

		$JSONMiniReport = json_encode($miniReport, JSON_UNESCAPED_SLASHES);

		return $JSONMiniReport;
	}

	/**
	 * Method, that makes XML Block about the report make date.
	 */
	private function XML_addDate() {
		$dateEntity = $this->xmlHandler->createElement('date');
		$dateEntity->appendChild($this->xmlHandler->createTextNode(date("F j, Y, g:i a")));

		$this->xmlRoot->appendChild($dateEntity);
	}

	/**
	 * @param integer $securityLevel Security Level.
	 */
	private function XML_addSecurityLevel($securityLevel) {
		$securityLevelEntity = $this->xmlHandler->createElement('securitylevel');
		$securityLevelEntity->appendChild($this->xmlHandler->createTextNode($securityLevel));

		$this->xmlRoot->appendChild($securityLevelEntity);
	}

	/**
	 * @param string $resoruceURL Resource URL.
	 * Form: <resource>URL</resource>
	 */
	private function XML_addResource($resourceURL, $resourceIP, $serverBanner) {
		// Making the resource xml block.
		$resourceEntity = $this->xmlHandler->createElement('resource');

		// Make <addrdata> (URL/IP)
		$addrDataEntity = $this->xmlHandler->createElement('addrdata');

			// Make <urladdr> block of <addrdata>-block. (URL INFO)
			$urlAddrDataEntity = $this->xmlHandler->createElement('urladdr');
			$urlAddrDataEntity->appendChild($this->xmlHandler->createTextNode($this->filtrateData($resourceURL)));

			// Make <ipaddr> block of <addrdata>-block. (IP INFO)
			$ipAddrDataEntity = $this->xmlHandler->createElement('ipaddr');
			$ipAddrDataEntity->appendChild($this->xmlHandler->createTextNode($this->filtrateData($resourceIP)));

		// Adds url/ip data into the <addrdata> block.
		$addrDataEntity->appendChild($urlAddrDataEntity); // URL DATA BLOCK
		$addrDataEntity->appendChild($ipAddrDataEntity);  // IP DATA BLOCK

		$serverBannerDataEntity = $this->xmlHandler->createElement('serverbanner');
		$serverBannerDataEntity->appendChild($this->xmlHandler->createTextNode($this->filtrateData($serverBanner)));

		$resourceEntity->appendChild($addrDataEntity);
		$resourceEntity->appendChild($serverBannerDataEntity);

		// Append data to the root xml block.
		$this->xmlRoot->appendChild($resourceEntity);
	}

	/**
	 * Method, that parses tested vulnerabilities data block into xml representation.
	 * Form of parsed data: array ( string:testedVulnerabilityName, string:testedVulnerabilityName, ..., ...).
	 * 
	 * Form: <testedvulnerabilities>
	 * 			<vulnerability security_level="SECURITY_LEVEL">VULNERABILITY_NAME</vulnerability>
	 * 		 </testedvulnerabilities>
	 */
	private function XML_addTestedVulnerabilities() {

		// Making the tested vulnerabilities xml block.
		$testedVulnerabilitiesEntity = $this->xmlHandler->createElement('testedvulnerabilities');

		// Making for all vulnerabilites its own xml block.
		// Form: <vulnerability security_level=SECURITY_LEVEL>VULNERABBILITY_NAME</vulnearbility>
		foreach($this->testedVulnerabilities as $vulnerabilityData) {


			$vulnerabilityEntity = $this->xmlHandler->createElement('vulnerability');
			$vulnerabilityEntity->appendChild($this->xmlHandler->createTextNode($vulnerabilityData['vulnerability']));

			$vulnerabilitySecurityLevelEntity = $this->xmlHandler->createAttribute('security_level');
			$vulnerabilitySecurityLevelEntity->appendChild($this->xmlHandler->createTextNode($vulnerabilityData['securityLevel']));

			$vulnerabilityEntity->appendChild($vulnerabilitySecurityLevelEntity);

			$testedVulnerabilitiesEntity->appendChild($vulnerabilityEntity);
		}

		// Appends the vulnerabilities xml block to the root xml-block.
		$this->xmlRoot->appendChild($testedVulnerabilitiesEntity);
	}


	/**
	 * Makes the "details about scan" xml block.
	 * Contains data about all tested vulnerabilities.
	 * Form: <scandetails><xssdetails>...</xssdetails<sqlinjdetails>...</sqlinjdetails></scandetails>
	 */
	private function XML_addScanDetails() {
		$scanDetailsEntity = $this->xmlHandler->createElement('scandetails');

		// If we have report about XSS test, then needs to add the XML test details.
		if($this->XSSReport !== false) {

			$xssTestDetailsEntity = $this->XML_getXSSTestDetails();
			$scanDetailsEntity->appendChild( $xssTestDetailsEntity );
		}
		
		// If we have report abous SQL Injection test, then we needs to add the SQL Inejction test details.	
		if($this->SQLInjectionReport !== false) {

			$sqlinjTestDetailsEntity = $this->XML_getSQLInjectionTestDetails();
			$scanDetailsEntity->appendChild( $sqlinjTestDetailsEntity );
		}

		// Appends details about scan into the root xml-block.
		$this->xmlRoot->appendChild($scanDetailsEntity);
	}

	/**
	 * Makes XSS Test details block.
	 * Form: <xssdetails>
	 * 	1) <notvulnerable>is not vulnerable</notvulnerable>
	 *  2) <subjects>...</subjects>
	 * 		</xssdetails>
	 */
	private function XML_getXSSTestDetails() {
		$xssTestDetailsEntity = $this->xmlHandler->createElement('xsstestdetails');

		if($this->XSSReport->getSecurityLevel() === 3) {
			$xssTestNotVulnerableEntity = $this->xmlHandler->createElement('notvulnerable');
			$xssTestNotVulnerableEntity->appendChild($this->xmlHandler->createTextNode('is not vulnerable'));

			$xssTestDetailsEntity->appendChild($xssTestNotVulnerableEntity);
		} else {
			// Adds subjects.
			$xssTestDetailsEntity->appendChild( $this->XML_getXSSTestSubjects() );

			// Adds vulnerable params (details).
			$xssTestDetailsEntity->appendChild( $this->XML_getXSSVulnerableParams() );
		}

		return $xssTestDetailsEntity;
	}

	/**
	 * Makes XSS Test Subkects block.
	 * Form: <subjects>
	 * 		  <subject>TECHNIQUE_NAME</subject>
	 * 		  ...
	 * 		 </subject>
	 */
	private function XML_getXSSTestSubjects() {
		$subjectsEntity = $this->xmlHandler->createElement('subjects');

		foreach( $this->XSSReport->getTechniqueList() as $techniqueType => $techniques ) {
			foreach($techniques as $techniqueName) {
				$subjectEntity = $this->xmlHandler->createElement('subject');
				$subjectEntity->appendChild($this->xmlHandler->createTextNode($techniqueName));
				$subjectTechniqueTypeEntity = $this->xmlHandler->createAttribute('type');
				if($techniqueType === 'check' )
					$subjectTechniqueTypeEntity->appendChild($this->xmlHandler->createTextNode('vulnerability'));
				elseif($techniqueType === 'inject')
					$subjectTechniqueTypeEntity->appendChild($this->xmlHandler->createTextNode('exploit'));
				$subjectEntity->appendChild($subjectTechniqueTypeEntity);

				$subjectsEntity->appendChild($subjectEntity);
			}
		}

		return $subjectsEntity;
	}

	/**
	 * Makes XSS Vulnerable Params Block.
	 * Form:
	 * 	<vulnerableparams>
	 * 		<vulnerableforms>
	 * 			<form form_id=ID>
	 * 				<vulnerableparam>
	 * 					<paramname>PARAM_NAME</paramname>
	 * 					<value technique_name=TECHNIQUE_NAME</value>
	 * 					<value technique_name=TECHNIQUE_NAME</value>
	 * 					...
	 * 				</vulnerableparam>
	 * 				..
	 * 				<exploits>
	 * 					<exploittechnique technique_name=TECHNIQUE_NAME>
	 * 						<exploitkind>
	 * 							<valuesqueue>VALUES_QUEUE</valuesqueue>
	 * 							<exploitdata>TEXT</exploitdata>
	 * 						</exploitkind>
	 * 						...
	 * 					</exploittechnique>
	 * 					...
	 * 				</exploits
	 * 			</form>
	 * 			..
	 * 		</vulnerableforms>
	 * 	</vulnerableparams>	
	 */
	private function XML_getXSSVulnerableParams() {
		$vulnerableParamsEntity = $this->xmlHandler->createElement('vulnerableparams');

		$vulnerableFormsEntity = $this->xmlHandler->createElement('vulnerableforms');

		foreach($this->XSSReport->getVulnerableDataInfo() as $formDataSet ) {

			$formEntity = $this->xmlHandler->createElement('form');
			$formIDAttrEntity = $this->xmlHandler->createAttribute('form_id');
			$formIDAttrEntity->appendChild(
				$this->xmlHandler->createTextNode($formDataSet->getFormID()));

			$formEntity->appendChild($formIDAttrEntity);

			foreach($formDataSet->getVulnerableParams() as $paramDataSet) {

				$vulnerableParamEntity = $this->xmlHandler->createElement('vulnerableparam');

				$paramNameEntity = $this->xmlHandler->createElement('paramname');
				$paramNameEntity->appendChild($this->xmlHandler->createTextNode($this->filtrateData( $paramDataSet->getParamName() )));

				$vulnerableParamEntity->appendChild($paramNameEntity);

				$xssTypeEntity = $this->xmlHandler->createElement('xsstype');
				$xssTypeEntity->appendChild($this->xmlHandler->createTextNode($this->filtrateData( $paramDataSet->getXSSType() )));

				$vulnerableParamEntity->appendChild($xssTypeEntity);

				// check values = vulnerable values ($paramValuesEntity)
				// inject values = exploits ($exploitsEntity)
				if(count($paramDataSet->vulnerableParamValues['check']) !== 0) {
					$paramValuesEntity = $this->xmlHandler->createElement('vulnerablevalues');

					foreach($paramDataSet->vulnerableParamValues['check'] as $techniqueName => $paramValues) {

						$techniqueEntity = $this->xmlHandler->createElement('checktechnique');
						$techniqueNameAttrEntity = $this->xmlHandler->createAttribute('technique_name');
						$techniqueNameAttrEntity->appendChild($this->xmlHandler->createTextNode($techniqueName));
						$techniqueEntity->appendChild($techniqueNameAttrEntity);


						foreach($paramValues as $paramValueSet) {
							$valueEntity = $this->xmlHandler->createElement('value');
							$valueEntity->appendChild($this->xmlHandler->createTextNode($this->filtrateData($paramValueSet['vulnerableValue']) ));

							$techniqueEntity->appendChild($valueEntity);
						}

						$paramValuesEntity->appendChild($techniqueEntity);
					}

					$vulnerableParamEntity->appendChild($paramValuesEntity);					
				}

				if(count($paramDataSet->vulnerableParamValues['inject']) !== 0) {
					$exploitsEntity = $this->xmlHandler->createElement('exploits');

					foreach($paramDataSet->vulnerableParamValues['inject'] as $techniqueName => $exploits) {
						$techniqueEntity = $this->xmlHandler->createElement('exploittechnique');
						$techniqueNameAttrEntity = $this->xmlHandler->createAttribute('technique_name');
						$techniqueNameAttrEntity->appendChild($this->xmlHandler->createTextNode($techniqueName));
						$techniqueEntity->appendChild($techniqueNameAttrEntity);

						foreach($exploits as $exploitValueSet) {
							//'resultQueue' => array stepID -> value
							//'resultData'    => string
							$techniqueExploitKindEntity = $this->xmlHandler->createElement('exploitkind');
							
							$techniqueValueQueueEntity = $this->xmlHandler->createElement('valuesqueue');
							$techniqueValueQueueEntity->appendChild($this->xmlHandler->createTextNode(implode(' ->-> ', $this->filtrateData($exploitValueSet['resultQueue']) )));

							$techniqueDataEntity = $this->xmlHandler->createElement('exploitdata');
							$techniqueDataEntity->appendChild($this->xmlHandler->createTextNode($this->filtrateData($exploitValueSet['resultData']) ));

							$techniqueExploitKindEntity->appendChild($techniqueValueQueueEntity);
							$techniqueExploitKindEntity->appendChild($techniqueDataEntity);

							$techniqueEntity->appendChild($techniqueExploitKindEntity);
						}

						$exploitsEntity->appendChild($techniqueEntity);
					}

					$vulnerableParamEntity->appendChild($exploitsEntity);
				}

				$formEntity->appendChild($vulnerableParamEntity);
			}

			$vulnerableFormsEntity->appendChild($formEntity);
		}

		$vulnerableParamsEntity->appendChild($vulnerableFormsEntity);

		return $vulnerableParamsEntity;
	}

	/**
	 * Makes SQL Injection Test Details Block.
	 * Form: <sqlinjtestdetails>
	 * 	1) <notvulnerable>is not vulnerable</notvulnerable>
	 * 	2) <subjects>...</subjects><vulnerableparams>...</vulnerableparams>
	 */
	private function XML_getSQLInjectionTestDetails() {

		$sqlinjTestDetailsEntity = $this->xmlHandler->createElement('sqlinjtestdetails');

		if($this->SQLInjectionReport->getSecurityLevel() === 3) {
			$sqlinjTestNotVulnerableEntity = $this->xmlHandler->createElement('notvulnerable');
			$sqlinjTestNotVulnerableEntity->appendChild($this->xmlHandler->createTextNode('is not vulnerable'));

			$sqlinjTestDetailsEntity->appendChild($sqlinjTestNotVulnerableEntity);


		} else {

			// Add possible database type.
			$sqlinjTestDetailsEntity->appendChild( $this->XML_getSQLInjectionPossibleDB() );

			// Adds subjects.
			$sqlinjTestDetailsEntity->appendChild( $this->XML_getSQLInjectionTestSubjects() );

			// Adds vulnerable params (details).
			$sqlinjTestDetailsEntity->appendChild( $this->XML_getSQLInjectionVulnerableParams() );
		}

		return $sqlinjTestDetailsEntity;
	}

	private function XML_getSQLInjectionPossibleDB() {
		$possibleDBEntity = $this->xmlHandler->createElement('possibledb');
		$possibleDBEntity->appendChild($this->xmlHandler->createTextNode($this->filtrateData( $this->SQLInjectionReport->getPossibleDB() )));

		return $possibleDBEntity;
	}

	/**
	 * Makes SQL Injection Test Subjects block.
	 * Form:
	 * 	<subjects>
	 * 		<subject>TECHNIQUE NAME</subject>
	 * 		...
	 * 	</subjects>
	 */
	private function XML_getSQLInjectionTestSubjects() {

		$subjectsEntity = $this->xmlHandler->createElement('subjects');

		foreach( $this->SQLInjectionReport->getTechniqueList() as $techniqueType => $techniques) {
			foreach($techniques as $techniqueName) {
				$subjectEntity = $this->xmlHandler->createElement('subject');
				$subjectEntity->appendChild($this->xmlHandler->createTextNode($techniqueName));
				$subjectTechniqueTypeEntity = $this->xmlHandler->createAttribute('type');
				if($techniqueType === 'check' )
					$subjectTechniqueTypeEntity->appendChild($this->xmlHandler->createTextNode('vulnerability'));
				elseif($techniqueType === 'inject')
					$subjectTechniqueTypeEntity->appendChild($this->xmlHandler->createTextNode('exploit'));
				$subjectEntity->appendChild($subjectTechniqueTypeEntity);


				$subjectsEntity->appendChild($subjectEntity);
			}
		}

		return $subjectsEntity;
	}

	/**
	 * Makes SQLInjection Vulnerable Params Block.
	 * Form:
	 * 	<vulnerableparams>
	 * 		<vulnerableparam>
	 * 			<paramname>PARAM_NAME</paramname>
	 * 			<vulnerablerequests>
	 * 				<request technique_name="TECHNIQUE_NAME">REQUEST</request>
	 * 				...
	 * 			</vulnerablerequests>
	 * 			<exploits>
	 * 				<exploittechnique technique_name="TECHNIQUE_NAME">
	 * 					<exploitkind>
	 * 						<resultQueue>REQUEST_QUEUE<resultQueue>
	 * 						<exploitdata>EXPLOIT_DATA</exploitdata>
	 * 					</exploitkind>
	 * 					...
	 * 				</exploittechnique>
	 * 				...
	 * 			</exploits>
	 * 		</vulnerableparam>
	 * 		...
	 * 	</vulnerableparams>
	 */
	private function XML_getSQLInjectionVulnerableParams() {
		$vulnerableParamsEntity = $this->xmlHandler->createElement('vulnerableparams');

		foreach($this->SQLInjectionReport->getVulnerableDataInfo() as $paramDataSet) {
			$vulnerableParamEntity = $this->xmlHandler->createElement('vulnerableparam');

			$paramNameEntity = $this->xmlHandler->createElement('paramname');
			$paramNameEntity->appendChild($this->xmlHandler->createTextNode($this->filtrateData($paramDataSet->getParamName()) ));

			$vulnerableParamEntity->appendChild($paramNameEntity);

			// check values = vulnerable requests with this params ($vulnerableRequestsEntity)
			// inject values = exploits ($exploitsEntity)
			if(count($paramDataSet->vulnerableParamValues['check']) !== 0) {
				$vulnerableRequestsEntity = $this->xmlHandler->createElement('vulnerablerequests');

				foreach($paramDataSet->vulnerableParamValues['check'] as $techniqueName => $paramValues) {

					$techniqueEntity = $this->xmlHandler->createElement('checktechnique');
					$techniqueNameAttrEntity = $this->xmlHandler->createAttribute('technique_name');
					$techniqueNameAttrEntity->appendChild($this->xmlHandler->createTextNode($techniqueName));
					$techniqueEntity->appendChild($techniqueNameAttrEntity);

					foreach($paramValues as $paramValueSet) {

						$requestEntity = $this->xmlHandler->createElement('request');
						$requestEntity->appendChild($this->xmlHandler->createTextNode($this->filtrateData($paramValueSet['vulnerableRequest'])));

						$techniqueEntity->appendChild($requestEntity);
					}

					$vulnerableRequestsEntity->appendChild($techniqueEntity);

				}

				$vulnerableParamEntity->appendChild($vulnerableRequestsEntity);
			}

			if(count($paramDataSet->vulnerableParamValues['inject']) !== 0) {
				$exploitsEntity = $this->xmlHandler->createElement('exploits');

				foreach($paramDataSet->vulnerableParamValues['inject'] as $techniqueName => $exploits) {
					//foreach($paramValues as $paramValueSet) {

						$techniqueEntity = $this->xmlHandler->createElement('exploittechnique');
						$techniqueNameAttrEntity = $this->xmlHandler->createAttribute('technique_name');
						$techniqueNameAttrEntity->appendChild($this->xmlHandler->createTextNode($techniqueName));
						$techniqueEntity->appendChild($techniqueNameAttrEntity);

						foreach($exploits as $exploitValueSet) {
							//'resultQueue' => array stepID -> value
							//'resultData'	  => string
							$techniqueExploitKindEntity = $this->xmlHandler->createElement('exploitkind');

							$techniqueValueQueueEntity = $this->xmlHandler->createElement('resultqueue');
							$techniqueValueQueueEntity->appendChild($this->xmlHandler->createTextNode(implode(' ->-> ', $this->filtrateData($exploitValueSet['resultQueue']) )));

							$techniqueDataEntity = $this->xmlHandler->createElement('exploitdata');
							$techniqueDataEntity->appendChild($this->xmlHandler->createTextNode($this->filtrateData($exploitValueSet['resultData']) ));

							$techniqueExploitKindEntity->appendChild($techniqueValueQueueEntity);
							$techniqueExploitKindEntity->appendChild($techniqueDataEntity);

							$techniqueEntity->appendChild($techniqueExploitKindEntity);
						}

						$exploitsEntity->appendChild($techniqueEntity);
					//}
				}

				$vulnerableParamEntity->appendChild($exploitsEntity);
			}

			$vulnerableParamsEntity->appendChild($vulnerableParamEntity);
		}

		return $vulnerableParamsEntity;
	}

}