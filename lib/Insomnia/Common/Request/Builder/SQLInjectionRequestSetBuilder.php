<?php
/**
 * Factory class that makes RepquestSet Object of SQLInjection Based Requests.
 */
class Insomnia_Common_Request_Builder_SQLInjectionRequestSetBuilder
{
	const URL_PATH_PART_SEPARATOR  = '/';
	const URL_QUERY_PART_SEPARATOR = '&';	

	/**
	 * @param array $newGETParamData Param Information, that shoud be injected into original request URL.
	 * Form: array ( "paramPositionID" => integer, "pathParamFlag" => boolean, "paramName" => string, "paramValue" => string|mixed )
	 * @param string $originalRequestURL Original request URL.
	 * @param boolean $POSTRequestFlag Flag which signals that current request is POST (true = POST, false = GET).
	 * @param array $POSTData If you making POST request with some POST data, add data in this params in array form: (paramName => value, ...).
	 * @return Insomnia_Common_Request_RequestSet
	 */
	public static function buildRequestSet($originalRequestURL, $newGETParamData = null, $POSTRequestFlag = false, $POSTData = array()) {

		$requestSet = new Insomnia_Common_Request_RequestSet(
			self::buildURL($originalRequestURL, $newGETParamData),
			$POSTRequestFlag,
			$POSTData
		);

		return $requestSet;
	}

	/**
	 * @param string $originalRequestURL Original request URL.
	 * @param array $newGETParamData Param Information, that shoud be injected into original request URL.
	 * @return string
	 */
	private static function buildURL($originalRequestURL, $newGETParamData) {
		
		$url = $originalRequestURL;

		if($newGETParamData !== null) {
			// Array of the divided URL by logical parts.
			// $urlParams array form:
			//		scheme 	 => _, 
			//		host 	 => _,
			//		port 	 => _,
			// 		user 	 => _,
			// 		pass 	 => _,
			// 		path 	 => _,
			// 		query 	 => _, # after '?'
			// 		fragment => _  # after '#'
			$urlParams = parse_url($originalRequestURL);
			if(!isset($urlParams['path']))
				$urlParams['path'] = self::URL_PATH_PART_SEPARATOR;

			// Prepares params into string form to update params.
			$paramSet = self::parseURLIntoArrayForm($urlParams);

			// Updating params.
			$paramSet[$newGETParamData['paramPositionID']]['paramValue'] = $newGETParamData['paramValue'];

			// Making URL parts in special parse_url() funciton form.
			$urlParts = self::buildURLParts($paramSet);

			// Checking for param parts and updating original parts.
			if(isset($urlParts['query']))
				$urlParams['query'] = $urlParts['query'];
			if(isset($urlParts['path']))
				$urlParams['path'] = $urlParts['path'];

			// Build url.
			$url = http_build_url($urlParams);
		} 

		return $url;
	}

	/**
	 * @param array $urlParams URL parts presented in array form of function parse_url()/
	 * @return array $paramSet Set of params, that shoud be updated by $newGETData of buildRequestSet method.
	 */
	private static function parseURLIntoArrayForm($urlParams) {

		$paramSet = array();

		// Prepearing param sets.
		$pathParamsExists  = isset($urlParams['path']);  $pathParams  = array();
		$queryParamsExists = isset($urlParams['query']); $queryParams = array();

		// If path params exists:
		if($pathParamsExists) {
			// Parsing path params.
			// Form: array ( pathParamValue, pathParamValue, ... )
			$pathParams = explode(self::URL_PATH_PART_SEPARATOR, substr($urlParams['path'], 1));
		}

		// If query params exists:
		if($queryParamsExists) {
			// Parsing query params.
			// Form: array ( array ( 0 => paramName, 1 => paramValue ), array ( .. ), .. )
			$queryParams = array_map(

				function($param) { 
					$paramSet = explode("=", $param);
					return array('paramName' => $paramSet[0], 'paramValue' => $paramSet[1]); 
				},

				explode(self::URL_QUERY_PART_SEPARATOR, $urlParams['query'])
			);			
		}

		// Making special param Set in form: array ( array( 'paramType' => 'path'/'query', 'paramName' => __, 'paramValue' => __), ... ),
		// if currennt param type is exists in $urlParam parts.
		if($pathParamsExists) {
			foreach($pathParams as $paramValue) {
				$paramSet[] = array(
					'paramType'  => 'path',
					'paramName'  => $paramValue,
					'paramValue' => $paramValue
				);
			}			
		}

		// Making special param Set in form: array ( array( 'paramType' => 'path'/'query', 'paramName' => __, 'paramValue' => __), ... )
		// if currennt param type is exists in $urlParam parts.
		if($queryParamsExists) {
			foreach($queryParams as $paramValue) {
				$paramSet[] = array(
					'paramType'  => 'query',
					'paramName'  => $paramValue['paramName'],
					'paramValue' => $paramValue['paramValue']
				);
			}			
		}

		return $paramSet;
	}

	/**
	 * This function builds parse_url()-form array from $paramSet of parseURLIntoArray method.
	 * Needs for building completed url of URL.
	 * @param array $paramSet Returned value from parseURLIntoArrayForm method.
	 * @return array $URLParts  URL parts presented in parse_url()-form.
	 */
	private static function buildURLParts($paramSet) {

		$URLParts = array();

		$PathURLParts  = '';
		$QueryURLParts = '';
		$QueryURLPartsSet = array();

		foreach($paramSet as $param) {
			switch ($param['paramType']) {
				
				case 'path':
					//$PathURLParts .= self::URL_PATH_PART_SEPARATOR . $param['paramValue'];
					$PathURLParts .= self::URL_PATH_PART_SEPARATOR . urlencode($param['paramValue']);
					break;
				
				case 'query':
					//$QueryURLPartsSet[] = $param['paramName']. "=" . $param['paramValue'];
					$QueryURLPartsSet[] = $param['paramName']. "=" . urlencode($param['paramValue']);
					break;

				default:
					break;
			}
		}

		$QueryURLParts = implode(self::URL_QUERY_PART_SEPARATOR, $QueryURLPartsSet);

		if(strlen($PathURLParts) !== 0) // if $PathURLParts exists
			$URLParts['path'] = $PathURLParts;
		if(strlen($QueryURLParts) !== 0) // if QueryURLParts exists
			$URLParts['query'] = $QueryURLParts;

		return $URLParts;
	}
}