<?php
/**
 * XSS Vulnerability Test Manager.
 * This class checks for possibly vulnerable params and getting useful information from attacked resource.
 * Algorythm:
 * 1) parsing raw data
 * 2) registering check techniques and inject techniques
 * 3) start teqhniques:
 * 3.1) infects each parameter separately
 * 3.2) making request with infected parametr
 * 3.3) analysing reply
 * 3.4) returns result set about exemined parametr.
 * 4) assembling complete result.
 */
class Insomnia_Workflow_XSSTask extends Insomnia_Workflow_Task
{
	private $vulnerableDataCollection = array();

	public function __construct($rawData) {
		parent::__construct($rawData);

		$this->vulnerableDataCollection = Insomnia_Common_Data_Builder_XSSVulnerableDataCollectionBuilder::build($this->rawData);

	}

	public function execute() {

		$this->registerCheckTechnique( Insomnia_Workflow_Research_TechniqueRegistry::XSS_CHECK_UNIQUEVECTOR );
		
		$this->registerInjectTechnique( Insomnia_Workflow_Research_TechniqueRegistry::XSS_INJECT_NUMERICCODE  );
		$this->registerInjectTechnique( Insomnia_Workflow_Research_TechniqueRegistry::XSS_INJECT_REMOTECODE   );
		$this->registerInjectTechnique( Insomnia_Workflow_Research_TechniqueRegistry::XSS_INJECT_DATAPROTOCOL );
		$this->registerInjectTechnique( Insomnia_Workflow_Research_TechniqueRegistry::XSS_INJECT_HANDSFREE 	  );


		$this->executeCheckAnalysis();
		$this->executeInjectAnalysis();

		$taskResult = new Insomnia_Workflow_XSSTaskResult(
			$this->rawData->getURL(),
			$this->rawData->getIP(),
			$this->vulnerableDataCollection
		);

		return $taskResult;
	}

	/**
	 * @method Executes Check techniques.
	 */
	protected function executeCheckAnalysis() {

		foreach($this->vulnerableDataCollection as $formKey => $formData) {
			foreach($formData['vulnerableDataCollection'] as $vulnerableData) {
				foreach($this->registeredTechniques['checkTechniquesIDs'] as $techniqueID) {

					$checkTechnique = Insomnia_Workflow_Research_TechniqueRegistry::getXSSCheckTechnique(
						$techniqueID,
						$vulnerableData->getOriginalURL(),
						$vulnerableData->getActionURL(),
						$vulnerableData->getParamName(),
						$vulnerableData->getParamValue(),
						$vulnerableData->getParamType(),
						$vulnerableData->getFormElements(),
						$vulnerableData->isPOSTParam()
					);

					$checkTechniqueResults = $checkTechnique->execute();

					foreach($checkTechniqueResults as $result)
						$vulnerableData->addCheckTechResult($result);
				}
			}

		}

	}

	/**
	 * @method Executes Inject techniques.
	 */
	protected function executeInjectAnalysis() {

		foreach($this->vulnerableDataCollection as $formKey => $formData) {
			foreach($formData['vulnerableDataCollection'] as $vulnerableData) {
				foreach($this->registeredTechniques['injectTechniquesIDs'] as $techniqueID) {

					$injectTechnique = Insomnia_Workflow_Research_TechniqueRegistry::getXSSInjectTechnique(
						$techniqueID,
						$vulnerableData->getOriginalURL(),
						$vulnerableData->getActionURL(),
						$vulnerableData->getParamName(),
						$vulnerableData->getParamValue(),
						$vulnerableData->getParamType(),
						$vulnerableData->getFormElements(),
						$vulnerableData->isPOSTParam()
					);

					$injectTechniqueResults = $injectTechnique->execute();

					foreach($injectTechniqueResults as $result)
						$vulnerableData->addInjectTechResult($result);
				}
			}

		}

	}		
}