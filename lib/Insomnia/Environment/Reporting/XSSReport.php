<?php
class Insomnia_Environment_Reporting_XSSReport
	extends Insomnia_Environment_Reporting_report
{
	/**
	 * @var array $vulnerableDataInfo Data collection of XSSFormInfoEntity objects.
	 * Form: array ( XSSFormInfoEntity, ... ).
	 */
	private $vulnerableDataInfo = array();

	/**
	 * @var array $techniqueList Techniques, which can beat investigated resource :)
	 */
	private $techniqueList = array( 'check' => array(), 'inject' => array() );

	public function __construct($resourceURL, $resourceIP, $taskResult) {
		parent::__construct($resourceURL, $resourceIP);

		$this->initializeVulnerableDataInfo($taskResult->getVulnerabilityInfo());
		$this->initializeSecurityLevel();
	}

	private function initializeVulnerableDataInfo($vulnerableDataSet) {
		foreach($vulnerableDataSet as $vulnerableFormID => $vulnerableFormData) {
			$formEntity = new Insomnia_Environment_Reporting_XSSFormInfoEntity($vulnerableFormID, $vulnerableFormData['actionURL']);

			foreach($vulnerableFormData['vulnerableDataCollection'] as $vulnerableData) {
				$paramEntity = new Insomnia_Environment_Reporting_XSSParamInfoEntity($vulnerableData->getParamName());

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
							$result->reply->getURL(),
							null,
							null,
							$result->XSSType
						);
					}
				}

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
							$result->resultData,
							$result->XSSType
						);		
					}
				}

				// Saving param info, if param is vulnerable or injectable.
				if($paramEntity->isVulnerable() || $paramEntity->isInjectable())
					$formEntity->addVulnerableParam($paramEntity);
			}

			if($formEntity->isVulnerable())
				$this->vulnerableDataInfo[]  = $formEntity;
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
}