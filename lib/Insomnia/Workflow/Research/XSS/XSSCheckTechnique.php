<?php
abstract class Insomnia_Workflow_Research_XSS_XSSCheckTechnique
	extends Insomnia_Workflow_Research_CheckTechnique
{
	const XSS_REFLECTED = 'reflected';
	const XSS_STORED    = 'stored';
	const XSS_UNKNOWN   = 'unknown';
}