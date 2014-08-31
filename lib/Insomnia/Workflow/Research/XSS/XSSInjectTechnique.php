<?php
abstract class Insomnia_Workflow_Research_XSS_XSSInjectTechnique
	extends Insomnia_Workflow_Research_InjectTechnique
{
	const XSS_REFLECTED = 'reflected';
	const XSS_STORED    = 'stored';
	const XSS_UNKNOWN   = 'unknown';
}