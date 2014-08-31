<?php
/**
 * Special vulenrable data genereted by Inject Vulnerability Technique.
 */
abstract class Insomnia_Common_Data_InjectTechniqueResult
	extends Insomnia_Common_Data_TechniqueResult
{
	/**
	 * @var boolean $injectable Flag signals that current injected param value is injectable.
	 */
	public $injectable;
	public $resultQueue;
	public $resultData;
}