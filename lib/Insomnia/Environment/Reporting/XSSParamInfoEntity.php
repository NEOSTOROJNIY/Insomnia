<?php
class Insomnia_Environment_Reporting_XSSParamInfoEntity
	extends Insomnia_Environment_Reporting_ParamInfoEntity
{
	/**
	 * @var string $paramName Param Name.
	 */
	public $paramName;

	/**
	 * @param array $vulnerableParamValues Info set about params and their vulnerable values.
	 * Form:
	 * array (
	 * 	  'check' =>
	 *    		'techniqueName' => array (
	 * 		  		array( 
	 * 			 		'vulnerableValue'   => string,
	 *           		'vulnerableRequest' => string
	 * 		 		),
	 *   		    ...,
	 *        		...
	 * 	  		),
	 *    		...,
	 *    		...
	 * 	  'inject' =>
	 * 			'techniqueName' => array (
	 * 				array(
	 * 					'resultQueue' => array(stepID -> string, stepID -> string, ),
	 * 					'resultData' => customized_string
	 * 				)
	 * 			)
	 * 			-- // -- // -- // --
	 * )
	 */
	public $vulnerableParamValues = array(
		'check'  => array(),
		'inject' => array()
	);

	public $XSSType;

	/**
	 * @var array $techniqueList Registered techniques, that can beat investigated resource and current param.
	 */
	public $techniqueList = array( 'check' => array(), 'inject' => array() );

	/**
	 * @param string $paramName Name of infected param.
	 */
	public function __construct($paramName) {
		$this->paramName = $paramName;
	}

	/**
	 * Add new infected param into $vulnerableParamValue.
	 * @param string $techniqueName Technique Name
	 * @param string $techniqueType Technique Type (check/inject).
	 * @param string $vulnerableVale Vulnerable value of CHECK technique (CHECK TECHNIQUE DATA).
	 * @param string $vulnerableRequest Vulenrable request of CHECK  technique (CHECK TECHNIQUE DATA).
	 * @param array $requestQueue Request steps for inject. Form (int:stepID => str:request, .., .. ) (INJECT TECHNIQUE DATA).
	 * @param customized_string $resultData Information about inject attack, based on $requestQueue (INJECT TECHNIQUE DATA).
	 * @param string $XSSType XSS Type (stored/reflected/unknown);
	 */
	public function addVulnerableData(
		$techniqueName,
		$techniqueType,
		$vulnerableValue = null,
		$vulnerableRequest = null,
		$resultQueue = null,
		$resultData = null,
		$XSSType) {

		// Checking type of technique for the correct data additing.
		if( $techniqueType === self::TECHNIQUETYPE_CHECK ) {

			if( !in_array($techniqueName, $this->techniqueList['check']) )
				$this->techniqueList['check'][] = $techniqueName;

			if( !isset($this->vulnerableParamValues['check'][$techniqueName]) ) {
				$this->vulnerableParamValues['check'][$techniqueName] = array();
				$this->vulnerableParamValues['check'][$techniqueName][] = array (
					'vulnerableValue'   => $vulnerableValue,
					'vulnerableRequest' => $vulnerableRequest
				);					
			} else {
				$this->vulnerableParamValues['check'][$techniqueName][] = array (
					'vulnerableValue'   => $vulnerableValue,
					'vulnerableRequest' => $vulnerableRequest
				);
			}

		} else {
			if( !in_array($techniqueName, $this->techniqueList['inject']) )
				$this->techniqueList['inject'][] = $techniqueName;

			if ( !isset($this->vulnerableParamValues['inject'][$techniqueName]) ) {
				$this->vulnerableParamValues['inject'][$techniqueName] = array();
				$this->vulnerableParamValues['inject'][$techniqueName][] = array (
					'resultQueue' => $resultQueue,
					'resultData'    => $resultData
				);				
			} else {
				$this->vulnerableParamValues['inject'][$techniqueName][] = array (
					'resultQueue' => $resultQueue,
					'resultData'    => $resultData
				);
			}

		}

		$this->XSSType = $XSSType;		

	}

	/**
	 * @return boolean
	 */
	public function isVulnerable() {
		return (count($this->vulnerableParamValues['check']) == 0) ? false : true;
	}

	/**
	 * @return boolean
	 */
	public function isInjectable() {
		return (count($this->vulnerableParamValues['inject']) == 0) ? false : true;
	}

	public function getParamName() {
		return $this->paramName;
	}

	public function getXSSType() {
		return $this->XSSType;
	}
}