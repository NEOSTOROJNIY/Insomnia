<?php
/**
 * Collection of raw data, that should be tested. This class extends RawData class, that contains the possibly vulnerable URL.
 */
class Insomnia_Common_Data_XSSRawData
	extends Insomnia_Common_Data_RawData
{
	/**
	 * @param string $possiblyVulnerableURL Possibly vulnerable URL.
	 * @return $this
	 */
	public function __construct($possiblyVulnerableURL) {
		parent::__construct($possiblyVulnerableURL);
	}
}