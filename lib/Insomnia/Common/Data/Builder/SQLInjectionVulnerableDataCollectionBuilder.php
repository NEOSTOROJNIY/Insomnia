<?php
/**
 * This class plaing the role of DataBuilder for SQLInjection Technics.
 * Making dataset for task.
 */
class Insomnia_Common_Data_Builder_SQLInjectionVulnerableDataCollectionBuilder
{
	const URL_PATH_PART_SEPARATOR  = '/';
	const URL_QUERY_PART_SEPARATOR = '&';

	/**
	 * This method parsing raw data to SQLInjectionVulnerableData entitie.
	 * @param SQLInjectionRawData $rawData Raw data object;
	 * @return array Array of SQLInjectionVulnerableData objects. Form: array ( SQLIVDObject, SQLIVDObject, ...).
	 */
	public static function build($rawData) {
		// Return value.
		$vulnerableDataCollection = array();

		// Array of divided URL on the logical parts.
		// $urlParams array form:
		//		scheme 	 => _, 
		//		host 	 => _,
		//		port 	 => _,
		// 		user 	 => _,
		// 		pass 	 => _,
		// 		path 	 => _,
		// 		query 	 => _, # (after symbol '?')
		// 		fragment => _  # (after symbol '#')

		
		$urlParams = parse_url($rawData->getURL());

		if(!isset($urlParams['scheme'])) {
			$rawData->setURL( 'http://' . $rawData->getURL() );
			$urlParams = parse_url( $rawData->getURL() );
		}

		if(!isset($urlParams['path']))
			$urlParams['path'] = self::URL_PATH_PART_SEPARATOR;


		// Parsing path params.
		// Form: array ( pathParamValue, pathParamValue, ... )
		// Notice: param value '1' in substr needs for drop the first symbol of path param url part ('/' symbol).
		$pathParams = explode(self::URL_PATH_PART_SEPARATOR, substr($urlParams['path'], 1));

		// Parsing query params.
		// Form: array ( array ( 0 => paramName, 1 => paramValue ), array ( .. ), .. )
		if(isset($urlParams['query'])) {
			$queryParams = array_map(
				function($param) { return explode("=", $param); },
				explode(self::URL_QUERY_PART_SEPARATOR, $urlParams['query'])
			);
		}


		// Making VulnerableData objects of path params.
		foreach($pathParams as $pathParamValue) {
			$vulnerableDataCollection[] = new Insomnia_Common_Data_SQLInjectionVulnerableData(
				count($vulnerableDataCollection, COUNT_NORMAL), // current param position
				$rawData->getURL(), // original URL;
				$pathParamValue, // Path param name (like as its value);
				$pathParamValue, // Path param value;
				true // Path param flag (true, if current param is Path Param).
			);
		}

		// Making Vulnerable Data objects of query params.
		if(isset($urlParams['query'])) {
			foreach($queryParams as $queryParamSet) {
				$vulnerableDataCollection[] = new Insomnia_Common_Data_SQLInjectionVulnerableData (
					count($vulnerableDataCollection, COUNT_NORMAL), // current param position
					$rawData->getURL(), // original URL;
					$queryParamSet[0], // Query Param Name;
					$queryParamSet[1] // Query Param Value.
				);
			}			
		}


		return $vulnerableDataCollection;		
	}
}