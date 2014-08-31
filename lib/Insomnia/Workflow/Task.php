<?php
/**
 * General Task Class.
 * Contains Raw Data and Genelral Execution method "run".
 */
abstract class Insomnia_Workflow_Task
{
	/**
	 * @var Insomnia_Common_Data_RawData $rawData Raw Data which should be analyzed.
	 */
	protected $rawData;

	protected $registeredTechniques = array (
		"checkTechniquesIDs"  => array(),
		"injectTechniquesIDs" => array()
	);


	public function __construct($rawData) {
		$this->rawData = $rawData;
	}
	
	/**
	 * Method that should start current task.
	 * Можно добавить параметр $INJECT=TRUE/FALSE, который будет говорить: проводить inject-тест или не проводить.
	 */
	public abstract function execute();

	/**
	 * @param integer $checkTechniqueID ID of check technique that should be registered.
	 */
	protected function registerCheckTechnique($checkTechniqueID) {
		$this->registeredTechniques['checkTechniquesIDs'][] = $checkTechniqueID;
	}

	/**
	 * @param integer $injectTechniqueID ID of inject technique that should be registered.
	 */
	protected function registerInjectTechnique($injectTechniqueID) {
		$this->registeredTechniques['injectTechniquesIDs'][] = $injectTechniqueID;
	}

	protected abstract function executeCheckAnalysis();
	protected abstract function executeInjectAnalysis();
}