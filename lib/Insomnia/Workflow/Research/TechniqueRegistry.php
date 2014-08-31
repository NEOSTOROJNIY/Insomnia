<?php
/**
 * Research techniques registry.
 * Contains all technique IDS and playing Factory role (mekes technique objects).
 */
class Insomnia_Workflow_Research_TechniqueRegistry
{
	/**
	 * @var const integer <<..>> Technique ID.
	 * Naming rules:
	 * VULNERABILITY-TYPE_TECHNIQUE-TYPE_TECHNIQUE-NAME
	 */

	/***********************************************
	 * SQL INJECTION TECHNIQUES IDs
	 ***********************************************/
	const SQLINJ_CHECK_MAGICQUOTES         = 0;
	const SQLINJ_CHECK_NUMERICALOPERATIONS = 2;
	const SQLINJ_CHECK_GROUPINGOPERATIONS  = 3;
	const SQLINJ_CHECK_BOOLEANOPERATIONS   = 4;

	const SQLINJ_INJECT_COLUMNCOUNT = 5;
	const SQLINJ_INJECT_BASEINFO    = 6;
	const SQLINJ_INJECT_SYSTEMDBCHECK = 7;

	/***********************************************
	 * XSS TECHNIQUES IDs
	 ***********************************************/
	const XSS_CHECK_UNIQUEVECTOR = 1;

	const XSS_INJECT_NUMERICCODE  = 8;
	const XSS_INJECT_REMOTECODE   = 9;
	const XSS_INJECT_DATAPROTOCOL = 10;
	const XSS_INJECT_HANDSFREE    = 11;

	/**********************************************
	 * UNIVERSAL TECHNIQUE NAMES
	 **********************************************/
	const UNIVERSAL_SYNTAXERROR   = 'Syntax Error';
	const UNIVERSAL_PLATFORMERROR = 'Platform Error';

	/**
	 * This method returns technique name by its ID.
	 * @param integer $techniqueID Technique ID.
	 * @return string
	 */
	public static function getTechniqueName($techniqueID) {

		$techniqueName = 'unknown';

		switch($techniqueID) {

			case self::SQLINJ_CHECK_MAGICQUOTES:
				$techniqueName = Insomnia_Workflow_Research_SQLInjection_Check_MagicQuotes::$techniqueName;
				break;

			case self::SQLINJ_CHECK_NUMERICALOPERATIONS:
				$techniqueName = Insomnia_Workflow_Research_SQLInjection_Check_NumericalOperations::$techniqueName;
				break;

			case self::SQLINJ_CHECK_GROUPINGOPERATIONS:
				$techniqueName = Insomnia_Workflow_Research_SQLInjection_Check_GroupingOperations::$techniqueName;
				break;

			case self::SQLINJ_CHECK_BOOLEANOPERATIONS:
				$techniqueName = Insomnia_Workflow_Research_SQLInjection_Check_BooleanOperations::$techniqueName;
				break;

			case self::SQLINJ_INJECT_COLUMNCOUNT:
				$techniqueName = Insomnia_Workflow_Research_SQLInjection_Inject_ColumnCount::$techniqueName;
				break;

			case self::SQLINJ_INJECT_BASEINFO:
				$techniqueName = Insomnia_Workflow_Research_SQLInjection_Inject_BaseInfo::$techniqueName;
				break;

			case self::SQLINJ_INJECT_SYSTEMDBCHECK:
				$techniqueName = Insomnia_Workflow_Research_SQLInjection_Inject_SystemDBCheck::$techniqueName;
				break;

			case self::XSS_CHECK_UNIQUEVECTOR:
				$techniqueName = Insomnia_Workflow_Research_XSS_Check_UniqueVector::$techniqueName;
				break;

			case self::XSS_INJECT_NUMERICCODE:
				$techniqueName = Insomnia_Workflow_Research_XSS_Inject_NumericCode::$techniqueName;
				break;

			case self::XSS_INJECT_REMOTECODE:
				$techniqueName = Insomnia_Workflow_Research_XSS_Inject_RemoteCode::$techniqueName;
				break;

			case self::XSS_INJECT_DATAPROTOCOL:
				$techniqueName = Insomnia_Workflow_Research_XSS_Inject_DataProtocol::$techniqueName;
				break;

			case self::XSS_INJECT_HANDSFREE:
				$techniqueName = Insomnia_Workflow_Research_XSS_Inject_HandsFree::$techniqueName;
				break;				

			default:
				break;
		}

		return $techniqueName;
	}

	/******************************************************************************
	 * SQL INJECTION TECHNIQUE BUILDERS
	 *****************************************************************************/

	/**
	 * Method wich instanciate SQL Injection Check Technique object and returns its.
	 * @param integer $techniqueID Technique ID.
	 * @param string $originalURL Original URL.
	 * @param string $paramName Param Name.
	 * @param string $paramValue Param value.
	 * @param integer $paramPositionID Position of param in url.
	 * @param boolean $pathParamFlag Signals that current param is Path param.
	 * @param boolean $POSTParamFlag Signals that current param is POST param.
	 */
	public static function getSQLINJCheckTechnique(
		$techniqueID,
		$originalURL,
		$paramName,
		$paramValue,
		$paramPositionID,
		$pathParamFlag = false,
		$POSTParamFlag = false 
	) {

		$techniqueInstance = null;

		switch ($techniqueID) {
			
			// 'Magic Quotes' technique.
			case self::SQLINJ_CHECK_MAGICQUOTES:
				$techniqueInstance = new Insomnia_Workflow_Research_SQLInjection_Check_MagicQuotes(
					$originalURL, 
					$paramName, 
					$paramValue, 
					$paramPositionID, 
					$pathParamFlag,
					$POSTParamFlag
				);
				break;
			
			// 'Numerical Operations' technique.
			case self::SQLINJ_CHECK_NUMERICALOPERATIONS:
				$techniqueInstance = new Insomnia_Workflow_Research_SQLInjection_Check_NumericalOperations(
					$originalURL, 
					$paramName, 
					$paramValue, 
					$paramPositionID, 
					$pathParamFlag,
					$POSTParamFlag
				);
				break;

			// 'Grouping Operations' technique.
			case self::SQLINJ_CHECK_GROUPINGOPERATIONS:
				$techniqueInstance = new Insomnia_Workflow_Research_SQLInjection_Check_GroupingOperations(
					$originalURL, 
					$paramName, 
					$paramValue, 
					$paramPositionID, 
					$pathParamFlag,
					$POSTParamFlag
				);
				break;

			// 'Boolean Operations' technique.
			case self::SQLINJ_CHECK_BOOLEANOPERATIONS:
				$techniqueInstance = new Insomnia_Workflow_Research_SQLInjection_Check_BooleanOperations(
					$originalURL, 
					$paramName, 
					$paramValue, 
					$paramPositionID, 
					$pathParamFlag,
					$POSTParamFlag
				);
				break;

			default:
				break;
		}

		return $techniqueInstance;
	}

	/**
	 * Method witch instanciate SQL Injection Inject Technique and returns its.
	 */
	public static function getSQLINJInjectTechnique(
		$techniqueID,
		$originalURL,
		$paramName,
		$originalParamValue,
		$paramPositionID,
		$pathParamFlag = false,
		$POSTParamFlag = false
	) {
		$techniqueInstance = null;

		switch($techniqueID) {

			// 'Column Count' technique.
			case self::SQLINJ_INJECT_COLUMNCOUNT:

				$techniqueInstance = new Insomnia_Workflow_Research_SQLInjection_Inject_ColumnCount(
					$originalURL,
					$paramName,
					$originalParamValue,
					$paramPositionID,
					$pathParamFlag,
					$POSTParamFlag
				);
				break;

			case self::SQLINJ_INJECT_BASEINFO:

				$techniqueInstance = new Insomnia_Workflow_Research_SQLInjection_Inject_BaseInfo(
					$originalURL,
					$paramName,
					$originalParamValue,
					$paramPositionID,
					$pathParamFlag,
					$POSTParamFlag					
				);
				break;

			case self::SQLINJ_INJECT_SYSTEMDBCHECK:

				$techniqueInstance = new Insomnia_Workflow_Research_SQLInjection_Inject_SystemDBCheck(
					$originalURL,
					$paramName,
					$originalParamValue,
					$paramPositionID,
					$pathParamFlag,
					$POSTParamFlag					
				);
				break;

			default:
				break;
		}

		return $techniqueInstance;
	}


	/******************************************************************************
	 * XSS TECHNIQUE BUILDERS
	 *****************************************************************************/

	/**
	 * Method wich instanciate XSS Check Technique object and returns its
	 */
	public static function getXSSCheckTechnique(
		$techniqueID,
		$originalURL,
		$actionURL,
		$paramName,
		$paramValue,
		$paramType,
		$formElements,
		$POSTParamFlag = false) {

		$techniqueInstance = null;

		switch ($techniqueID) {
			
			// 'Unique Vector' technique
			case self::XSS_CHECK_UNIQUEVECTOR:
				$techniqueInstance = new Insomnia_Workflow_Research_XSS_Check_UniqueVector(
					$originalURL,
					$actionURL,
					$paramName,
					$paramValue,
					$paramType,
					$formElements,
					$POSTParamFlag
				);
				break;
			
			default:
				break;
		}

		return $techniqueInstance;
	}

	public static function getXSSInjectTechnique(
		$techniqueID,
		$originalURL,
		$actionURL,
		$paramName,
		$paramValue,
		$paramType,
		$formElements,
		$POSTParamFlag = false) {

		$techniqueInstance = null;

		switch ($techniqueID) {

			case self::XSS_INJECT_NUMERICCODE:
				$techniqueInstance = new Insomnia_Workflow_Research_XSS_Inject_NumericCode(
					$originalURL,
					$actionURL,
					$paramName,
					$paramValue,
					$paramType,
					$formElements,
					$POSTParamFlag
				);
				break;

			case self::XSS_INJECT_REMOTECODE:
				$techniqueInstance = new Insomnia_Workflow_Research_XSS_Inject_RemoteCode(
					$originalURL,
					$actionURL,
					$paramName,
					$paramValue,
					$paramType,
					$formElements,
					$POSTParamFlag
				);
				break;

			case self::XSS_INJECT_DATAPROTOCOL:
				$techniqueInstance = new Insomnia_Workflow_Research_XSS_Inject_DataProtocol(
					$originalURL,
					$actionURL,
					$paramName,
					$paramValue,
					$paramType,
					$formElements,
					$POSTParamFlag
				);
				break;

			case self::XSS_INJECT_HANDSFREE:
				$techniqueInstance = new Insomnia_Workflow_Research_XSS_Inject_HandsFree(
					$originalURL,
					$actionURL,
					$paramName,
					$paramValue,
					$paramType,
					$formElements,
					$POSTParamFlag
				);
				break;				

			default:
				break;
		}

		return $techniqueInstance;
	}
}