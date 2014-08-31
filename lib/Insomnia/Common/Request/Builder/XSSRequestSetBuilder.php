<?php
/**
 * Factory class that makes RepquestSet Object of XSS Based Requests.
 */
class Insomnia_Common_Request_Builder_XSSRequestSetBuilder
{
	const URL_PATH_PART_SEPARATOR  = '/';
	const URL_QUERY_PART_SEPARATOR = '&';	

	/**
	 * @param string $originalRequestURL Original Request URL.
	 * @param array|null $newGETParamData GET Params, that should be inserted into $originalRequestURL for the GET request
	 * Form: array('name' => 'value', 'name' => 'value', ... ).
	 * @param boolean $POSTRequestFlag We need in POST request or GET (true = POST, false = GET).
	 * @param array $POSTData POST Data, if we doing post request and we have POST data, that should be sended.
	 */
	public static function buildRequestSet($originalRequestURL, $newGETParamData = null,  $POSTRequestFlag = false, $POSTData = array()) {

		$requestSet = new Insomnia_Common_Request_RequestSet(
			self::buildURL($originalRequestURL, $newGETParamData),
			$POSTRequestFlag,
			$POSTData
		);

		return $requestSet;

	}

	/**
	 * @param string $originalRequestURL
	 * @param array|null $newGETParamData
	 * @return 
	 */
	private static function buildURL($originalRequestURL, $newGETParamData) {

		$url = $originalRequestURL;

		// If new get param data is not null - go add it to our url.
		if($newGETParamData !== null) {

			$queryString = '';
			$queryParams = array();

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
			$urlParts = parse_url($url);
			if(!isset($urlParts['path']))
				$urlParts['path'] = self::URL_PATH_PART_SEPARATOR;

			foreach($newGETParamData as $paramName => $paramValue)
				$queryParams[] = $paramName . "=" . $paramValue;

			$queryString = implode($queryParams, self::URL_QUERY_PART_SEPARATOR);

			// Checking for existing 'query' url part.
			if(isset($urlParts['query'])) // exists - additing $queryString to existed.
				$urlParts['query'] .= self::URL_QUERY_PART_SEPARATOR . $queryString;
			else // else additing new part into urlParts array.
				$urlParts['query'] = $queryString;

			// Build url from the parts.
			$url = http_build_url($urlParts);
		}

		return $url;
	}
}