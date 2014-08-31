<?php
/**
 * Collection of potentially vulnerable data, exposed to SQL Injection attack.
 * Extends VulnerableData Class.
 */
class Insomnia_Common_Data_SQLInjectionVulnerableData
	extends Insomnia_Common_Data_VulnerableData
{
	/**
	 * @var private string $originalURL Original URL that contains current param.
	 */
	private $originalURL;

	/**
	 * @var private string $paramName Param name.
	 */
	private $paramName;

	/**
	 * @var priavte string|mixed $paramValue Param value.
	 */
	private $paramValue;

	/**
	 * @var private string $POSTParamFlag
	 */
	private $POSTParamFlag;

	/**
	 * @var private boolean $pathParamFlag Signals that current param is PATH parm (true) or VARIABLE param (false).
	 */
	private $pathParamFlag;

	/**
	 * @var private integer $paramPositionID Position place of param in URL.
	 */
	private $paramPositionID;

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
	 * 			SQLInjectionCheckTechResSet Object,
	 * 			SQLInjectionCheckTechResSet Object,
	 * 			...
	 *  	),
	 * 		injectTechniquesResultCollection => array(
	 * 			SQLInjectionInjectTechResSet Object,
	 * 			SQLInjectionInjectTechResSet Object,
	 * 			...
	 * 		)
	 *  )
	 */
	private $infectedDataCollection = array ( 
		"checkTechniquesResultCollection"  => array(),
		"injectTechniquesResultCollection" => array()
	);

	/**
	 * @param string $originalURL Original URL that contains current param.
	 * @param string $paramName Param name.
	 * @param string|mixed $paramValue Param value.
	 * @param boolean $pathParamFlag Signals that current param is PATH parm (true) or VARIABLE param (false). DEFAULT = false.
	 */
	public function __construct($paramPositionID, $originalURL, $paramName, $paramValue, $pathParamFlag = false, $POSTParamFlag = false) {
		$this->paramPositionID = $paramPositionID;
		$this->originalURL     = $originalURL;
		$this->paramName       = $paramName;
		$this->paramValue      = $paramValue;
		$this->pathParamFlag   = $pathParamFlag;
		$this->POSTParamFlag   = $POSTParamFlag;
	}

	/**
	 * @param integer $paramPositionID Position place of param in URL. 
	 */
	public function setParamPositionID($paramPositionID) {
		$this->paramPositionID = $paramPositionID;
	}

	/**
	 * @param string $originalURL Original URL that contains current param.
	 */
	public function setOriginalURL($originalURL) {
		$this->originalURL = $originalURL;
	}

	/**
	 * @param string $paramName Param name.
	 */
	public function setParamName($paramName) {
		$this->paramName = $paramName;
	}

	/**
	 * @param boolean $pathParamFlag Signals that current param is PATH parm (true) or paramIABLE param (false).
	 */	
	public function setPathParamFlag($pathParamFlag) {
		$this->pathParamFlag = $pathParamFlag;
	}

	/**
	 * @param Insomnia_Common_Data_SQLInjectionCheckTechResult $checkTechniqueResult Result of Check Technique.
	 */
	public function addCheckTechResult($checkTechniqueResult) {
		$this->infectedDataCollection["checkTechniquesResultCollection"][] = $checkTechniqueResult;
	}

	/**
	 * @param Insomnia_Common_Data_SQLInjectionInjectTechResult $injectTechniqueResultt Result of Inject Technique.
	 */
	public function addInjectTechResult($injectTechniqueResult) {
		$this->infectedDataCollection["injectTechniquesResultCollection"][] = $injectTechniqueResult;
	}

	/**
	 * @return integer
	 */
	public function getParamPositionID() {
		return $this->paramPositionID;
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
	public function getParamName() {
		return $this->paramName;
	}

	/**
	 * @return mixed
	 */
	public function getParamValue() {
		return $this->paramValue;
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
	public function isPathParam() {
		return $this->pathParamFlag;
	}

	public function isPOSTParam() {
		return $this->POSTParamFlag;
	}
}