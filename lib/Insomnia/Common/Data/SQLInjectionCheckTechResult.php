<?php
/**
 * SQLInjection Vulnerable Param Data Object for Check Techniques.
 * Contains:
 * - used technique that infects param,
 * - infected param value,
 * - reply of request with this infected param value,
 * - vulnerable this infected parametr or not,
 * - possible DB of attacked network resource.
 */
class Insomnia_Common_Data_SQLInjectionCheckTechResult
	extends Insomnia_Common_Data_CheckTechniqueResult
{
	/**
	 * @var string $possibleDB Contains info about possible DB of attacked Internet Resource.
	 */
	public $possibleDB;

	/**
	 * @param integer $techniqueID ID of used attack technique.
	 * @param string|mixed $paramValue Infected value of param.
	 * @param Insomnia_Common_Request_Reply_ReplyDataSet $reply Reply of request with current infected param value $paramValue.
	 * @param boolean $vulnerable Flag which signals that current injected param is vulnerable.
	 * @param string $possibleDB Contains info about possible DB of attacked Internet Resource.
	 */
	public function __construct($techniqueID, $paramValue, $reply, $vulnerable, $possibleDB) {
		$this->techniqueID = $techniqueID;
		$this->paramValue  = $paramValue;
		$this->reply       = $reply;
		$this->vulnerable  = $vulnerable;
		$this->possibleDB  = $possibleDB;
	}
}