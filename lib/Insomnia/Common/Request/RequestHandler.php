<?php
/**
 * Request Abstract Class.
 * Makes requests by CURL library and prepares result in commfort form of ReplyDataSet Class.
 * Implodes:
 * 1) CURL based network managment.
 * 2) Result data implode to ReplyDataSet object.
 */
abstract class Insomnia_Common_Request_RequestHandler
{
	/**
	 * @var protected Insomnia_Common_Request_RequestSet $requestSet Vulnerable request url.
	 */
	protected $requestSet;

	/**
	 * @var protected resource $curlSessionHandler CURL resourse that initialized by curl_init() function.
	 */
	protected $curlSessionHandler;

	/**
	 * @var protected array $headers Standart HTTP Request headers, that will be setted to Request.
	 */
	protected $headers = array(
		"Accept-Language" => "en-us,en;q=0.5",
		"User-Agent"      => "Mozilla/5.0 (Windows NT 6.2) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.64 Safari/537.31"
	);

	/**
	 * @var protected array $standartCURLOptions Standart CURL session Options
	 */
	protected $standartCURLOptions = array(
		CURLOPT_HEADER         => true, // require the header answer
		CURLOPT_RETURNTRANSFER => true, // require the output into variable
		CURLOPT_BINARYTRANSFER => true, // require binary reply
		CURLOPT_FOLLOWLOCATION => true, // require to follow for location headers
		CURLOPT_MAXREDIRS      => 10,   // max redirects of follow locations
		CURLOPT_FRESH_CONNECT  => true, // require create absolutly new curl resource
		CURLOPT_FORBID_REUSE   => true  // require for non caching and closing curl resource after work
	);

	/**
	 * @var protected array $useragents Collection of browser user-gents. It need to build the customized random request.
	 */
	protected $userAgents = array(
		"Mozilla/5.0 (Windows NT 6.2) AppleWebKit/537.17 (KHTML, like Gecko) Chrome/24.0.1312.56 Safari/537.17 CoolNovo/2.0.6.12",
		"Mozilla/5.0 (Windows NT 6.2) AppleWebKit/537.8 (KHTML, like Gecko) Chrome/23.0.1255.0 Safari/537.8",
		"Mozilla/5.0 (Windows NT 6.2) AppleWebKit/537.1 (KHTML, like Gecko) Maxthon/4.0.5.3000 Chrome/22.0.1229.79 Safari/537.1",
		"Mozilla/5.0 (Windows NT 6.2) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.64 Safari/537.31",
		"Mozilla/5.0 (Windows NT 6.2; rv:20.0) Gecko/20100101 Firefox/20.0",
		"Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.2; Trident/6.0)",
		"Opera/9.80 (Windows NT 6.2) Presto/2.12.388 Version/12.15"
	);

	/**
	 * @param Insomnia_Common_Request_RequestSet $requestSet Vulnerable URL
	 */
	//public function __construct($requestSet) {
	//	$this->requestSet = $requestSet;
	//}

	/**
	 * This method sets new header or reset value of old header.
	 * @param array $headers Set of HTTP headers. Template: array( "header_name" => "header_value", "header_name" => "header_value", ...)
	 */
	public function setHeader($headers) {
		foreach($headers as $headerName => $headerValue)
			$this->headers[$headerName] = $headerValue;
	}

	/**
	 * This method deletes exists header
	 * @param string $headerName HTTP header name.
	 */
	public function unsetHeader($headerName) {
		unset($headers[$headerName]);
	}

	/**
	 * This method converts Predefined headers into HTTP-Header form, that will be included into CURL request by CURLOPT_HTTPHEADER option.
	 * @return array
	 */
	protected function getHeadersInHTTPForm($headers) {
		$HTTPForm = array();
		foreach($headers as $headerName => $headerValue)
			$HTTPForm[] = $headerName.": ".$headerValue; // HTTP header implementation
		return $HTTPForm;
	}

	/**
	 * This method executes network request. Returns information about request in the ReplyDataSet form.
	 * @return Insomnia_Common_Request_Reply_ReplyDataSet
	 */
	public function executeProcess($randomUserAgent = true) {
		if($randomUserAgent)
			$this->setHeader( array( "User-Agent", $this->userAgents[ array_rand($this->userAgents) ] ) );
		return $this->sendRequestAndGetReply();
	}

	/**
	 * This method executes network request and generates ResultDataSet result. Returns information about request in the ReplyDataSet object form.
	 * @return Insomnia_Common_Request_Reply_ReplyDataSet
	 */
	protected function sendRequestAndGetReply() {

		// Initiating CURL session.
		$this->curlSessionHandler = curl_init($this->requestSet->getURL());

		// Configurating CURL session.
		$this->configureCurlSession();

		// Executes CURL request.
		$requestOutput = curl_exec($this->curlSessionHandler);

		// Making Reply set.
		$replyDataSet = new Insomnia_Common_Request_Reply_ReplyDataSet($this->requestSet->getURL(), $this->curlSessionHandler, $requestOutput);

		// Closing CURL session.
		curl_close($this->curlSessionHandler);

		return $replyDataSet;
	}

	/**
	 * Abstract CURL session handler configurator.
	 */
	protected abstract function configureCURLSession();

	/**
	 * @param Insomnia_Common_Request_RequestSet $requestSet Request set.
	 */
	public function setRequestSet($requestSet) {
		$this->requestSet = $requestSet;
	}

	/**
	 * @return Insomnia_Common_Request_RequestSet
	 */
	public function getRequestSet() {
		return $this->requestSet;
	}
}