<?php
/**
 * Collection of potentially vulnerable data, exposed to XSS attack.
 * Extends VulnerableData Class.
 */
class Insomnia_Common_Data_XSSVulnerableData
	extends Insomnia_Common_Data_VulnerableData
{
	/**
	 * @var private string $originalURL Original URL. Resource, that tested in current test.
	 */
	private $originalURL;

	/**
	 * @var private string $actionURL Action URL. URL of Form Action attribute.
	 */
	private $actionURL;

	/**
	 * @var private string $paramName Param name.
	 */
	private $paramName;

	/**
	 * @var private string $paramValue Param Value.
	 */
	private $paramValue;

	/**
	 * @var private string $paramType Type of param (index-XXX|select-option|textarea)
	 */
	private $paramType;

	/**
	 * @var private boolean $POSTParamFlag
	 */
	private $POSTParamFlag;

	/**
	 * @var private array $formElements Parametrs of current form, that contains current vulnerable Param.
	 * Form: array ( 
	 * 		name => (value => .., type => ..) , 
	 * 		name => (value => .., type => ..) ,
	 * 		... )
	 */
	private $formElements;

	/**
	 * @var private array $infectedDataCollection Collection of results with infected current param.
	 * Results contains:
	 * - information about used technique of param infection,
	 * - infected param value,
	 * - vulnerable/injected flag,
	 * - reply of the request with infected param value
	 * - possible DB type and version.
	 * Form:
	 * array(
	 * 		checkTechniquesResultCollection => array( 
	 *
	 * 			XSSCheckTechResSet Object,
	 * 			XSSCheckTechResSet Object,
	 * 			...
	 *  	),
	 * 		injectTechniquesResultCollection => array(
	 * 
	 * 			XSSInjectTechResSet Object,
	 * 			XSSInjectTechResSet Object,
	 * 			...
	 * 		)
	 *  )
	 */
	private $infectedDataCollection = array (
		"checkTechniquesResultCollection"  => array(),
		"injectTechniquesResultCollection" => array()
	);

	/**
	 * @param string $originalURL Original URL.
	 * @param string $actionURL Action URL.
	 * @param string $paramName Param name.
	 * @param string $paramValue Param value.
	 * @param string $paramType Param type ('input-XXX'|'select-option'|'textarea')
	 * @param array $formElements
	 * @param boolean $POSTParamFlag
	 */
	public function __construct($originalURL, $actionURL, $paramName, $paramValue, $paramType, $formElements, $POSTParamFlag = false) {
		$this->originalURL   = $originalURL;
		$this->actionURL     = $actionURL;
		$this->paramName     = $paramName;
		$this->paramValue    = $paramValue;
		$this->paramType     = $paramType;
		$this->formElements  = $formElements;
		$this->POSTParamFlag = $POSTParamFlag;
	}

	/**
	 * @param string $originalURL
	 */
	public function setOriginalURL($originalURL) {
		$this->originalURL = $originalURL;
	}

	/**
	 * @param string $actionURL
	 */
	public function setActionURL($actionURL) {
		$this->actionURL = $actionURL;
	}

	/**
	 * @param string $paramName
	 */
	public function setParamName($paramName) {
		$this->paramName = $paramName;
	}

	/**
	 * @param string $paramValue
	 */
	public function setParamValue($paramValue) {
		$this->paramValue = $paramValue;
	}

	/**
	 * @param string $paramType
	 */
	public function setParamType($paramType) {
		$this->paramType = $paramType;
	}

	/**
	 * @param boolean $POSTParamFlag
	 */
	public function setPOSTParamFlag($POSTParamFlag) {
		$this->POSTParamFlag = $POSTParamFlag;
	}

	/**
	 * @param Insomnia_Common_Data_XSSCheckTechResult $checkTechniqueResult Result of Check Technique.
	 */
	public function addCheckTechResult($checkTechniqueResult) {
		$this->infectedDataCollection["checkTechniquesResultCollection"][] = $checkTechniqueResult;
	}

	/**
	 * @param Insomnia_Common_Data_XSSInjectTechResult $injectTechniqueResultt Result of Inject Technique.
	 */
	public function addInjectTechResult($injectTechniqueResult) {
		$this->infectedDataCollection["injectTechniquesResultCollection"][] = $injectTechniqueResult;
	}

	/**
	 * @return array
	 */
	public function getFormElements() {
		return $this->formElements;
	}

	/**
	 * @return string
	 */
	public function getOriginalURL() {
		return $this->originalURL;
	}

	/**
	 * @return string
	 */
	public function getActionURL() {
		return $this->actionURL;
	}

	/**
	 * @return string
	 */
	public function getParamName() {
		return $this->paramName;
	}

	/**
	 * @return string|mixed
	 */
	public function getParamValue() {
		return $this->paramValue;
	}

	/**
	 * @return string
	 */
	public function getParamType() {
		return $this->paramType;
	}

	/**
	 * @return array
	 */
	public function getInfectedDataCollection() {
		return $this->infectedDataCollection;
	}

	/**
	 * @return boolean
	 */
	public function isPOSTParam() {
		return $this->POSTParamFlag;
	}
}