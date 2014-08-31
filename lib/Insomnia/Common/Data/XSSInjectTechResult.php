<?php
/**
 * XSS Vulnerable Param Data Object for Inject Techniques.
 * Contains:
 * - used technique that infects param,
 * - infected param value,
 * - request reply with this infected param value,
 * - injectable this infected parametr or not,
 * - type of XSS (unknown/reflected/stored.
 */
class Insomnia_Common_Data_XSSInjectTechResult
	extends Insomnia_Common_Data_InjectTechniqueResult
{
	/**
	 * @var string $XSSType unknown/stored/reflectd
	 */
	public $XSSType = "unknown";

	/**
	 * @param integer $techniqueID ID of used attack technique.
	 * @param string|mixed $paramValue Infected value of param.
	 * @param Insomnia_Common_Request_Reply_ReplyDataSet $reply Reply of request with current infected param value $paramValue.
	 * @param boolean $injectable Flag which signals that current injected param is injectable.
	 * @param string $XSSType Contains info about type of XSS (unknown/reflected/stored).
	 * @param array $resultQueue Queue of requests which gots data from resource. Form: array ( int:stepID => str:vulnerable_request|vulnerable_value, ...)
	 * @param customized_string $resultData Info about inject
	 */
	public function __construct($techniqueID, $paramValue, $reply, $injectable, $XSSType = "unknown",  $resultQueue = null, $resultData = null) {
		$this->techniqueID = $techniqueID;
		$this->paramValue  = $paramValue;
		$this->reply       = $reply;
		$this->injectable  = $injectable;
		$this->XSSType     = $XSSType;
		$this->resultQueue = $resultQueue;
		$this->resultData  = $resultData;
	}
}