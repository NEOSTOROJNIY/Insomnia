<?php
/**
 * XSS Vulnerable Param Data Object for Check Techniques.
 * Contains:
 * - used technique that infects param,
 * - infected param value,
 * - reply of request with this infected param value,
 * - type of XSS (unknown/reflected/stored).
 */
class Insomnia_Common_Data_XSSCheckTechResult
	extends Insomnia_Common_Data_CheckTechniqueResult
{
	/**
	 * @var string $XSSType unknown/stored/reflectd
	 */
	public $XSSType = "unknown";

	/**
	 * @param integer $techniqueID ID of used attack technique.
	 * @param string|mixed $paramValue Infected value of param.
	 * @param Insomnia_Common_Request_Reply_ReplyDataSet $reply Reply of request with current infected param value $paramValue.
	 * @param boolean $vulnerable Flag which signals that current injected param is vulnerable.
	 * @param string $XSSType Contains info about type of XSS (unknown/stored/reflected).
	 */
	public function __construct($techniqueID, $paramValue, $reply, $vulnerable, $XSSType = "unknown") {
		$this->techniqueID = $techniqueID;
		$this->paramValue  = $paramValue;
		$this->reply       = $reply;
		$this->vulnerable  = $vulnerable;
		$this->XSSType     = $XSSType;
	}
}