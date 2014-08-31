<?php
/**
 * Form entity for the XSS report.
 */
class Insomnia_Environment_Reporting_XSSFormInfoEntity
{
	/**
	 * @var integer $formID Index number of the FORM on the page.
	 */
	private $formID;

	/**
	 * @var string $actionURL Action URL of FORM action attribute.
	 */
	private $actionURL;

	/**
	 * @var array $vulnerableParams Array of vulnerableParams reperesnted in XSSParamInfoEntity.
	 * Form: array ( XSSParamInfoEntity, XSSParamInfoEntity, ... )
	 */
	private $vulnerableParams = array();

	/**
	 * @param integer $formID Index number of the FORM on the page.
	 * @param string $actionURL Action URL of the FORM action attribute.
	 */
	public function __construct($formID, $actionURL) {
		$this->formID    = $formID;
		$this->actionURL = $actionURL;
	}

	/**
	 * @param XSSParamInfoEntity $XSSParamInfoEntity
	 */
	public function addVulnerableParam($XSSParamInfoEntity) {
		$this->vulnerableParams[] = $XSSParamInfoEntity;
	}

	/**
	 * @return integer
	 */
	public function getFormID() {
		return $this->formID;
	}

	/**
	 * @return string
	 */
	public function getActionURL() {
		return $this->actionURL;
	}
	
	/**
	 * @return array
	 */
	public function getVulnerableParams() {
		return $this->vulnerableParams;
	}

	public function isVulnerable() {
		return (count($this->vulnerableParams) !== 0) ? true : false;
	}
}