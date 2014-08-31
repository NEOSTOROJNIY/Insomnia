<?php
/**
 * InfectedParamValue of VulnerableData.
 */
abstract class Insomnia_Common_Data_TechniqueResult
{
	/**
	 * @var integer $techniqueID ID of used technique.
	 */
	public $techniqueID;

	/**
	 * @var string|mixed $paramValue Infected value of param.
	 */
	public $paramValue;

	/**
	 * @var Insomnia_Common_Request_Reply_ReplyDataSet $reply Reply of request with current infected param value $paramValue.
	 */
	public $reply;
}