<?php
/**
 * SQLInjection Vulnerable Param Data Object for Inject Techniques.
 * Contains:
 * - used technique that infects param,
 * - infected param value,
 * - request reply with this infected param value,
 * - injectable this infected parametr or not,
 * - possible DB of attacked network resource.
 */
class Insomnia_Common_Data_SQLInjectionInjectTechResult
	extends Insomnia_Common_Data_InjectTechniqueResult
{
	/**
	 * @var string $possibleDB Contains info about possible DB of attacked Internet Resource.
	 */
	public $possibleDB;

	/**
	 * NOT NEED: $paramValue, $reply
	 * @param integer $techniqueID ID of used attack technique.
	 * @param string|mixed $paramValue Infected value of param.
	 * @param Insomnia_Common_Request_Reply_ReplyDataSet $reply Reply of request with current infected param value $paramValue.
	 * @param boolean $injectable Flag which signals that current injected param is injectable.
	 * @param string $possibleDB Contains info about possible DB of attacked Internet Resource.
	 * @param array $resultQeueue Queue of requests which gots data from resource. Form: array ( int:stepID => str:vulnerable_request|vulnerable_value, ...)
	 * @param customized_string $resultData Info about inject
	 */
	public function __construct($techniqueID, $paramValue, $reply, $injectable, $possibleDB, $resultQueue = null, $resultData = null) {
		$this->techniqueID = $techniqueID;
		$this->paramValue  = $paramValue;
		$this->reply       = $reply;
		$this->injectable  = $injectable;
		$this->possibleDB  = $possibleDB;
		$this->resultQueue = $resultQueue;
		$this->resultData  = $resultData;
	}
}