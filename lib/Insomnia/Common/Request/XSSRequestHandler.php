<?php
/**
 * XSS Request Handler. Extends Insomnia_Common_Request_RequestHandler.
 * Unique methods: 
 * configureCURLSession (specialized for XSS Task).
 * This class configures CURL session to XSS Vulnerability Test.
 */
class Insomnia_Common_Request_XSSRequestHandler
	extends Insomnia_Common_Request_RequestHandler
{
	/**
	 * @method configureCURLSession
	 * @access protected
	 * This method configures CURL Session especially for XSS test: makes correct CURL request (POST/GET) for test.
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