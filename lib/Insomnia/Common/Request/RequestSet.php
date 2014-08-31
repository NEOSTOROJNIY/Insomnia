<?php
/**
 * Set of request Data:
 * 1) requestURL (requested URL);
 * 2) POSTRequestFlag (true = POST, false = GET);
 * 3) POSTData (null|array).
 */
class Insomnia_Common_Request_RequestSet
{
	/**
	 * @var private strubg $requestURL Parts of URL like as parse_url return array.
	 */
	private $requestURL;

	/**
	 * @var private boolean $POSTRequestFlag Signals that this is POST request.
	 * flase = GET request
	 * true  = POST request
	 */
	private $POSTRequestFlag;

	/**
	 * @var private array $POSTData Post data submitted in the form: (Name => Value, Name => Value, ...)
	 */
	private $POSTData;

	/**
	 * @var priavte array $customHeaders Headers, that should be inserted into request. Form: array ( Name => Value, ...).
	 */
	private $customHeaders;

	/**
	 * @param string $requestURL Request URL.
	 * @param boolean $POSTRequestFlag POST request flag: true - POST request, false - GET request.
	 * @param array $POSTData POST data set submitted in array fom.
	 */
	public function __construct($requestURL, $POSTRequestFlag = false, $POSTData = array(), $customHeaders = array()) {
		$this->requestURL      = $requestURL;
		$this->POSTRequestFlag = $POSTRequestFlag;
		$this->POSTData        = $POSTData;
		$this->customHeaders   = $customHeaders;
	}

	/**
	 * @return string
	 */
	public function getURL() {
		return $this->requestURL;
	}

	/**
	 * @return boolean
	 */
	public function isPOSTRequest() {
		return $this->POSTRequestFlag;
	}

	/**
	 * @return array
	 */
	public function getPOSTData() {
		return $this->POSTData;
	}

	/**
	 * @return array
	 */
	public function getCustomHeaders() {
		return $this->customHeaders;
	}
}