<?php
/**
 * This class is an entitie of request result, that creates by Insomnia_Common_Request_RequestHandler->executeProcess method.
 * Current entitie containts the data about:
 * 1) vulnerable internet resource ($requestURL);
 * 2) technical info about reply ($replyInfo);
 * 3) result http headers ($replyHeaders);
 * 4) body of reply ($replyBody);
 * 5) if request throws some errors while processing, this signal will be saved ($replyError);
 * 6) if request throws some errors while processing, this information will be saved ($replyErrorData).
 */
class Insomnia_Common_Request_Reply_ReplyDataSet
{
	/**
	 * @var private string $requestURL Vulnerable URL.
	 */
	private $requestURL = "";

	/**
	 * @var private array $replyInfo Reply information created by curl_getinfo() function.
	 */
	private $replyInfo = array();

	/**
	 * @var private array $replyHeaders Reply headers.
	 */
	private $replyHeaders = array();

	/**
	 * @var private string $replyBody Reply Body.
	 */
	private $replyBody = "";

	/**
	 * @var private boolean $replyError Reply error flag.
	 */
	private $replyError = false;

	/**
	 * @var private array $replyErrorData Reply Error Data Collection. Form: (error_code (integer) => error_message (string)).
	 */
	private $replyErrorData = array();

	/**
	 * @var const CURLSESSION_OK CURL session code signals that CURL session successfully completed.
	 */
	const CURLSESSION_OK = 0;

	/**
	 * @param string $requestURL Request Vulnerable URL
	 * @param resource $curlSessionHandler CURL Handler
	 * @param string $curlReplyBody Reply body of HTTP Request.
	 */
	public function __construct($requestURL, $curlSessionHandler, $curlReplyBody = null) {

		$this->requestURL = $requestURL;
		$this->replyInfo = curl_getinfo($curlSessionHandler);

		if(curl_errno($curlSessionHandler) == self::CURLSESSION_OK)
			$this->setDoneOptions($curlReplyBody);
		else
			$this->setErrorOptions($curlSessionHandler);
	}

	/**
	 * This method registers the error data of curl request.
	 * @param resource $curlSessionHandler CURL Handler
	 */
	protected function setErrorOptions($curlSessionHandler) {

		$this->replyError = true;
		$this->replyErrorData =  array (
			"curl_error_code"    => curl_errno($curlSessionHandler),
			"curl_error_message" => curl_error($curlSessionHandler)
		);		
	}

	/**
	 * This method registers the done options of curl request.
	 * @param string $curlReplyBody Reply body of curl request.
	 */
	protected function setDoneOptions($curlReplyBody) {

		$this->replyHeaders = http_parse_headers($curlReplyBody);
		if($curlReplyBody != null)
			$this->replyBody = $curlReplyBody;
	}

	/**
	 * Error checking.
	 * @return boolean
	 */
	public function isError() {
		return $this->replyError;
	}

	/**
	 * @return array
	 */
	public function getErrorData() {
		return $this->replyErrorData;
	}

	/**
	 * @return array
	 */
	public function getInfo() {
		return $this->replyInfo;
	}

	/**
	 * @return array
	 */
	public function getHeaders() {
		return $this->replyHeaders;
	}

	/**
	 * @return string
	 */
	public function getBody() {
		return $this->replyBody;
	}

	/**
	 * @return string
	 */
	public function getURL() {
		return $this->requestURL;
	}
}