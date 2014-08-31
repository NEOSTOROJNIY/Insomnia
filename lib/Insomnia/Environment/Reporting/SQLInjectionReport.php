<?php
class Insomnia_Environment_Reporting_SQLInjectionReport
	extends Insomnia_Environment_Reporting_Report
{
	/**
	 * @var array $vulnerableDataInfo Data collection of SQLInjectionParamInfoEntity
	 */
	private $vulnerableDataInfo = array();

	private $possibleDB;

	/**
	 * @var array $techniqueList Techniques, which can beat investigated resource :)
	 */
	private $techniqueList = array( 'check' => array(), 'inject' => array() );

	public function __construct($resourceURL, $resourceIP, $taskResult) {
		parent::__construct($resourceURL, $resourceIP);

		$this->initializeVulnerableDataInfo($taskResult->getVulnerabilityInfo());
		$this->possibleDB = $taskResult->getPossibleDB();
		$this->initializeSecurityLevel();
	}

	/**
	 * This method adds useful data to $this->vulnerableDataInfo
	 * @param array $vulnerableDataSet Array of Insomnia_Common_Data_SQLInjectionVulnerableData objects.
	 */
	private function initializeVulnerableDataInfo($vulnerableDataSet) {
	
		foreach($vulnerableDataSet as $vulnerableData) {
				// Making new Param Entity	
				$paramEntity = new Insomnia_Environment_Reporting_SQLInjectionParamInfoEntity( $vulnerableData->getParamName() );

				// Checking current param on the vulnerability info.
				foreach($vulnerableData->getInfectedDataCollection()['checkTechniquesResultCollection'] as $result) {

					if($result->vulnerable) {
						// Getting technique name by ID.
						$techniqueName = Insomnia_Workflow_Research_TechniqueRegistry::getTechniqueName($result->techniqueID);
						// Registering technique name into technique list.
						$this->addTechnique('check', $techniqueName);
						// Making param entity of current param.
						$paramEntity->addVulnerableData(
							$techniqueName,
							Insomnia_Environment_Reporting_ParamInfoEntity::TECHNIQUETYPE_CHECK,
							$result->paramValue,
							$result->reply->getURL()
						);
					}
				}

			// Checking current param on the injectable info.
			foreach($vulnerableData->getInfectedDataCollection()['injectTechniquesResultCollection'] as $result) {
				if($result->injectable) {
					// Getting technique name by ID.
					$techniqueName = Insomnia_Workflow_Research_TechniqueRegistry::getTechniqueName($result->techniqueID);
					// Registering technique name into technique list.
					$this->addTechnique('inject', $techniqueName);
					// Making param entity of current param.
					$paramEntity->addVulnerableData(
						$techniqueName,
						Insomnia_Environment_Reporting_ParamInfoEntity::TECHNIQUETYPE_INJECT,
						null,
						null,
						$result->resultQueue,
						$result->resultData
					);
				}
			}

			// Saving param info, if param is vulnerable or injectable.
			if($paramEntity->isVulnerable() || $paramEntity->isInjectable())
				$this->vulnerableDataInfo[] = $paramEntity;
		}
	}

	/**
	 * Add useful technique name into technique list, that contains data about techniques which can attacks investigated resource.
	 * @param string $techniqueType Technqiue type ('check'/'inject') of technique name.
	 * @param string $techniqueName Technique name that should be added.
	 */
	private function addTechnique($techniqueType, $techniqueName) {
		if( !in_array($techniqueName, $this->techniqueList[$techniqueType]) )
			$this->techniqueList[$techniqueType][] = $techniqueName;
	}

	/**
	 * Initalizing security level.
	 */
	private function initializeSecurityLevel() {
		$this->securityLevel = self::SECURITYLEVEL_GREEN;

		if(count($this->techniqueList['check']) > 0)
			$this->securityLevel = self::SECURITYLEVEL_YELLOW;

		if(count($this->techniqueList['inject']) > 0)
			$this->securityLevel = self::SECURITYLEVEL_RED;
	}
	
	/**
	 * @return array
	 */
	public function getVulnerableDataInfo() {
		return $this->vulnerableDataInfo;
	}

	/**
	 * @return array
	 */
	public function getTechniqueList() {
		return $this->techniqueList;
	}

	public function getPossibleDB() {
		return $this->possibleDB;
	}

}