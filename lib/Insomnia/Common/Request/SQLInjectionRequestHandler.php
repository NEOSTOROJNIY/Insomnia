<?php
/**
 * SQLInjection Request Handler. Extends Insomnia_Common_Request_RequestHandler.
 * Unique methods: 
 * configureCURLSession (specialized for SQL Injection Task).
 * This class configures CURL session on SQL Injection Vulnerability Test.
 */
class Insomnia_Common_Request_SQLInjectionRequestHandler
	extends Insomnia_Common_Request_RequestHandler
{
	/**
	 * @method configureCURLSession
	 * @access protected
	 * This method configures CURL session especially for SQL Injection test.
	 */
	protected function configureCURLSession() {
		$customCURLOptions = array();

		// Configurating POST options.
		if($this->requestSet->isPOSTRequest()) {
			$customCURLOptions[CURLOPT_POST] = true;
			$customCURLOptions[CURLOPT_POSTFIELDS] = $this->requestSet->getPOSTData();
		}

		$customCURLOptions[CURLOPT_URL] = $this->requestSet->getURL();
		$customCURLOptions[CURLOPT_HTTPHEADER] = $this->getHeadersInHTTPForm( 
			array_merge( $this->headers, $this->requestSet->getCustomHeaders() )
		);

		curl_setopt_array($this->curlSessionHandler, $this->standartCURLOptions);
		curl_setopt_array($this->curlSessionHandler, $customCURLOptions);
	}
}