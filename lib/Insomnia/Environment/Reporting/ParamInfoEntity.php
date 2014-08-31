<?php
/**
 * Entity of param data. Base class for all techniques.
 */
abstract class Insomnia_Environment_Reporting_ParamInfoEntity
{
	/**
	 * @var string TECHNIIQUETYPE_* Technqiue Type constant.
	 */
	const TECHNIQUETYPE_CHECK  = 'check';
	const TECHNIQUETYPE_INJECT = 'inject';

	abstract function isVulnerable();
	abstract function isInjectable();
}