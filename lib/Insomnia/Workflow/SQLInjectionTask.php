<?php
/**
 * SQL Injection Vulnerability Test Manager.
 * This class checks for possibly vulnerable params and getting useful information from attacked resource.
 * Algorythm:
 * 1) parsing raw data
 * 2) registering check techniques and inject techniques
 * 3) start teqhniques:
 * 3.1) infects each parameter separately
 * 3.2) making request with infected parametr
 * 3.3) analysing reply
 * 3.4) returns result set about exemined parametr.
 * 4) assembling complete result
 */
class Insomnia_Workflow_SQLInjectionTask extends Insomnia_Workflow_Task
{
	/**
	 * @var private array $vulnerableDataCollection Collection of Vulnerable data objects.
	 * Form: array( vulnerableData, vulnerableData, vulnerableData ).
	 * Vulnerable data - collection of query params with infection info.
	 */
	private $vulnerableDataCollection = array();

	/**
	 * @param Insomnia_Common_Data_SQLInjectionRawData $rawData Raw data for sql injection research.
	 */
	public function __construct($rawData) {
		parent::__construct($rawData);

		// Generating Vulnerable data collection.
		$this->vulnerableDataCollection = Insomnia_Common_Data_Builder_SQLInjectionVulnerableDataCollectionBuilder::build($this->rawData);
	}

	/**
	 * @method Executes current task.
	 * @return Insomnia_Workflow_SQLInjectionTaskResult
	 */
	public function execute() {
		
		$this->registerCheckTechnique( Insomnia_Workflow_Research_TechniqueRegistry::SQLINJ_CHECK_MAGICQUOTES );
		$this->registerCheckTechnique( Insomnia_Workflow_Research_TechniqueRegistry::SQLINJ_CHECK_NUMERICALOPERATIONS );
		$this->registerCheckTechnique( Insomnia_Workflow_Research_TechniqueRegistry::SQLINJ_CHECK_GROUPINGOPERATIONS );
		$this->registerCheckTechnique( Insomnia_Workflow_Research_TechniqueRegistry::SQLINJ_CHECK_BOOLEANOPERATIONS );
		
		$this->registerInjectTechnique( Insomnia_Workflow_Research_TechniqueRegistry::SQLINJ_INJECT_COLUMNCOUNT );
		$this->registerInjectTechnique( Insomnia_Workflow_Research_TechniqueRegistry::SQLINJ_INJECT_BASEINFO );
		$this->registerInjectTechnique( Insomnia_Workflow_Research_TechniqueRegistry::SQLINJ_INJECT_SYSTEMDBCHECK );

		$this->executeCheckAnalysis();
		$this->executeInjectAnalysis();

		$taskResult = new Insomnia_Workflow_SQLInjectionTaskResult(
			$this->rawData->getURL(), 
			$this->rawData->getIP(), 
			$this->vulnerableDataCollection,
			$this->analyzePossibleDB(),
			$this->getServerBanner()
		);

		return $taskResult;
	}

	private function analyzePossibleDB() {

		$dbTypeCollection = array();

		foreach($this->vulnerableDataCollection as $vulnerableData) {
			foreach($vulnerableData->getInfectedDataCollection() as $results) {
				foreach($results as $result) {
						
					if(!isset($dbTypeCollection[$result->possibleDB]))
						$dbTypeCollection[$result->possibleDB] = 0;

					$dbTypeCollection[$result->possibleDB] += 1;
				}
			}
		}

		$possibleDB = 'unknown'; $possibleDBMeetings = 0;

		foreach($dbTypeCollection as $dbType => $dbMeetings) {
			if($dbType === 'unknown') continue;
			if($dbMeetings === 0) continue;

			if($dbMeetings >= $possibleDBMeetings) {
				$possibleDB = ($dbMeetings == $possibleDBMeetings) ? $possibleDB . '/' . $dbType : $dbType;
				$possibleDBMeetings = $dbMeetings;
			}
		}

		return $possibleDB;
	}

	/**
	 * @method Executes Check techniques.
	 */
	protected function executeCheckAnalysis() {

		foreach($this->vulnerableDataCollection as $vulnerableDataKey => $vulnerableData) {
			foreach($this->registeredTechniques['checkTechniquesIDs'] as $techniqueID) {
				
				$checkTechnique = Insomnia_Workflow_Research_TechniqueRegistry::getSQLINJCheckTechnique(
					$techniqueID,
					$vulnerableData->getOriginalURL(),
					$vulnerableData->getParamName(),
					$vulnerableData->getParamValue(),
					$vulnerableData->getParamPositionID(),
					$vulnerableData->isPathParam(),
					$vulnerableData->isPOSTParam()
				);	

				$checkTechniqueResults = $checkTechnique->execute();

				foreach($checkTechniqueResults as $result)
					$vulnerableData->addCheckTechResult($result);	
			}
		}

	}

	/**
	 * @method Executes Inject techniques.
	 */
	protected function executeInjectAnalysis() {

		foreach($this->vulnerableDataCollection as $vulnerableDatakey => $vulnerableData) {

			// Checking all results of the current vulnerable param.
			foreach($vulnerableData->getInfectedDataCollection()['checkTechniquesResultCollection'] as $checkTechniqueResult) {

				if($checkTechniqueResult->vulnerable) {
					// Start testing for inject and BREAK(2).

					foreach($this->registeredTechniques['injectTechniquesIDs'] as $techniqueID) {

						$injectTechnique = Insomnia_Workflow_Research_TechniqueRegistry::getSQLINJInjectTechnique(
							$techniqueID,
							$vulnerableData->getOriginalURL(),
							$vulnerableData->getParamName(),
							$vulnerableData->getParamValue(),
							$vulnerableData->getParamPositionID(),
							$vulnerableData->isPathParam(),
							$vulnerableData->isPOSTParam()
						);

						$injectTechniqueResults = $injectTechnique->execute();
						foreach($injectTechniqueResults as $result)
							$vulnerableData->addInjectTechResult($result);
					}

					break 2;
				}

			}

		}

	}

	private function getServerBanner() {

		$requestHandler = new Insomnia_Common_Request_SQLInjectionRequestHandler();
		$requestSet = Insomnia_Common_Request_Builder_SQLInjectionRequestSetBuilder::buildRequestSet( $this->rawData->getURL() );
		$requestHandler->setRequestSet($requestSet);
		$replySet = $requestHandler->executeProcess();
		$serverBanner = isset($replySet->getHeaders()['Server']) ? $replySet->getHeaders()['Server'] : "NONE";

		return $serverBanner;
	}	
}