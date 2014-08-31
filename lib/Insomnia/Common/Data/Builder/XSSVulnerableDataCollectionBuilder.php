<?php
/**
 * DataBuilder for XSS Tests. Makes vulnerable data collection for XSS Tests.
 * Return form: array ( XSSVulnerableData, ... ).
 */
class Insomnia_Common_Data_Builder_XSSVulnerableDataCollectionBuilder
{
	// Form request types.
	// GET  - FORM_METHOD_GET
	// POST - FORM_METHOD_POST
	const FORM_METHOD_GET  = "GET";
	const FORM_METHOD_POST = "POST";

	// Form element types.
	// <input>             - FORM_ELEMENTTYPE_INPUT
	// <select><option>... - FORM_ELEMENTTYPE_SELECT
	// <textarea>... 	   - FORM_ELEMENTTYPE_TEXTAREA
	const FORM_ELEMENT_TYPE_INPUT    = "input";
	const FORM_ELEMENT_TYPE_SELECT   = "select-option";
	const FORM_ELEMENT_TYPE_TEXTAREA = "textarea";

	// Standart value for forms element.
	const FORM_ELEMENT_VALUE_STANDARTVALUE = "Insomnia";

	/**
	 * Method, that makes vulnerable data collection for XSS tests.
	 * @param Insomnia_Common_Data_XSSRawData
	 * @return array Array of XSSVulnerableData objects.
	 * Form: 
	 *  array ( 
	 * 		form_number => array (
	 *			"actionURL" 	=> ..,
	 *			"originalURL" 	=> ..,
	 *			"vulnerableDataCollection" 		=> array ( XSSVulnerableData, .., .., .. )
	 *		),
	 * 		..,
	 * 		..
	 * ).
	 */
	public static function build($rawData) {
		// Simple HTML DOM Parser
		$html   = new simple_html_dom();
		// Snoopy Parser and Browser Imitator
		$snoopy = new Snoopy();

		// XSS Vulnerable Data Collection
		// Form: array of forms: 
		// array ( 
		// 		form_number => array (
		//			"actionURL" 	=> ..,
		//			"originalURL" 	=> ..,
		//			"vulnerableDataCollection" 		=> array ( XSSVulnerableData, .., .., .. )
		//		),
		//		..,
		//		..
		// )
		$XSSVulnerableDataCollection = array();

		// Collection of FormData objects. Data for Vulnerable Data Collection. Parsed forms data.
		$formsDataCollection = array();

		// Download the page from $rawData's url.
		// If current url is downloadable -> start to collect data into $formsDataCollection array.
		// Else - do nothing and return vulnerable collection immediately.

		/////////////////////////////////////////////////////////////////////////////////////////////////////////
		// NEWWWWWWWWWWWWW
		$reqHandler = new Insomnia_Common_Request_XSSRequestHandler();
		$reqSet = Insomnia_Common_Request_Builder_XSSRequestSetBuilder::buildRequestSet($rawData->getURL());
		$reqHandler->setRequestSet($reqSet);
		$reqReply = $reqHandler->executeProcess();
		/////////////////////////////////////////////////////////////////////////////////////////////////////////

		//if($snoopy->fetch($rawData->getURL())) {

			// Download the page html code into parser.
			//$html->load($snoopy->results);
			$html->load($reqReply->getBody());


			// Deleting <!-- --> html code (code commentaries).
			$page = preg_replace("{<!--.*?-->}", '', $html);

			// Reload parsed page into parser.
			$html->load($page);

			// Searching for forms.
			$forms = $html->find('form');

			// Checking for forms existing.
			// If forms exists - start to parsing.
			// Else - do nothing and return vulnerable collection immediately.
			if($forms !== null) {

				foreach($forms as $form) {

					// Clearing the URL.
					$rawURL = $rawData->getURL(); $rawURLParts = parse_url($rawData->getURL());
					$newURLParts = array();
					$newURLParts['scheme'] = (isset($rawURLParts['scheme'])) ? $rawURLParts['scheme'] : 'http';
					$newURLParts['host']   = (isset($rawURLParts['host'])) ? $rawURLParts['host'] : 'localhost';
					if(isset($rawURLParts['port'])) $newURLParts['port'] = $rawURLParts['port'];
					$newURLParts['path'] = (isset($rawURLParts['path'])) ? $rawURLParts['path'] : '/';
					$clearURL = http_build_url($newURLParts);


					// Making new Action URL.
					$actionURL = $clearURL;
					if($form->action !== false) {
						$actionURLParts = parse_url($form->action);
						if(!isset($actionURLParts['scheme'])) $actionURLParts['scheme'] = $newURLParts['scheme'];
						if(!isset($actionURLParts['host'])) $actionURLParts['host'] = $newURLParts['host'];
						if(!isset($actionURLParts['port']) && isset($newURLParts['port'])) $actionURLParts['port'] = $newURLParts['port'];
						if(!isset($actionURLParts['path'])) $actionURLParts['path'] = $newURLParts['path'];
						$actionURL = http_build_url($actionURLParts);
					}

					// Getting data about form.
					//$action = ($form->action !== false) ? $form->action : $rawData->getURL(); // НАДО ПРОВЕРЯТЬ НА ОТСУТСТВИЕ http:/DOMAIN_NAME/ и на присутствие QUER_STRING в RAW_DATA->URL
					$action = $clearURL; // НАДО ПРОВЕРЯТЬ НА ОТСУТСТВИЕ http:/DOMAIN_NAME/ и на присутствие QUER_STRING в RAW_DATA->URL
					$method = ($form->method !== false) ? strtoupper($form->method) : self::FORM_METHOD_GET;

					// Making Form Object Insomnia_Common_Data_Builder_XSSFormData.
					$formData = new Insomnia_Common_Data_Builder_XSSFormData($action, $method);

					// Getting <INPUT>s, <SELECT>s, <TEXTAREA>s.
					$inputs    = $form->find('input');		// <INPUT> objects
					$selects   = $form->find('select');		// <SELECT> objects
					$textareas = $form->find('textarea');	// <TEXTAREA> objects

					// Checks that we have a useful info. If we havent -> in this form we dont need.
					if($inputs === null && $selects === null && $textareas === null)
						continue;

					// If we have <INPUT>s
					if($inputs !== null) {
						foreach($inputs as $input) {
							// If the input hasnt 'name' attribute - we dont need in this input.
							if($input->name === false) {
								continue;
							} else if(self::checkInputType($input->type) === false) {
								// If the current input type is invalid - we dont need in this input.
								continue 2;
							} else {
								// Else: all are ok and we start to collect input data in FormData->elements().
								$inputName = $input->name;
								$inputValue = ($input->value !== false && strlen($input->value) != 0)
									? $input->value
									: self::FORM_ELEMENT_VALUE_STANDARTVALUE;

								// Adds new element into Form Object.
								$formData->addElemment($inputName, $inputValue, self::FORM_ELEMENT_TYPE_INPUT . '-' . $input->type);
							}
						}
					}

					// If we have <SELECT>s
					if($selects !== null) {
						foreach($selects as $select) {
							// If the select hasnt 'name' attribute - we dont need in this select.
							if($select->name === false) {
								continue;
							} else {
								// Select the options collection from select.
								$options = $select->find('option');

								// If the current select hasnt options tags - we dont need in this select.
								if($options === null) {
									continue;
								} else {
									foreach($options as $option) {
										// Saving select and option data and adds it into Form Object.
										$selectName  = $select->name;
										$selectValue = ($option->value !== false && strlen($option->value) != 0) ? $option->value : self::FORM_ELEMENT_VALUE_STANDARTVALUE;

										// Adds new element into Form Object.
										$formData->addElemment($selectName, $selectValue, self::FORM_ELEMENT_TYPE_SELECT);
									}
								}
							}
						}
					}

					// If we have <TEXTAREA>s
					if($textareas !== null) {
						foreach($textareas as $textarea) {
							// If the textarea hasnt 'name' attribute - we dont need in this attribute.
							if($textarea->name === false) {
								continue;
							} else {
								//  Gettings data of textarea and saving it.
								$txtareaName = $textarea->name;
								$txtareaValue = self::FORM_ELEMENT_VALUE_STANDARTVALUE;

								// Adds new element into Form Object.
								$formData->addElemment($txtareaName, $txtareaValue, 'textarea');
							}
						}
					}

					// Checking the elements count of formData. If it has 0 - we dont need in this formData.
					if( count($formData->getElements()) == 0)
						continue;

					// Adds Data about researched form into formsDataCollection.
					$formsDataCollection[] = $formData;
				}
			}
		//}

		// Parsing all forms itno XSSVulnerableData objects with data about form into XSSVulnerableDataCollection array.
		foreach($formsDataCollection as $formData) {
			// Making entity of XSSVulnerableDataCollection (ACTIOURL,ORIGINALURL,METHOD,XSSVulnerableData COLLECTION OF OBJECTS).
			$data = array(
				"actionURL"                => $formData->getActionURL(),
				"originalURL"              => $rawData->getURL(),
				"method"				   => $formData->getMethod(),
				"vulnerableDataCollection" => array()
			);
			
			// Is POST form (checking method of form).
			$isPOSTForm = ($formData->getMethod() === self::FORM_METHOD_POST) ? true : false;

			// Making XSSVulnerableData object.
			foreach($formData->getElements() as $elementName => $elementData) {

				$data['vulnerableDataCollection'][] =
					new Insomnia_Common_Data_XSSVulnerableData(
						$rawData->getURL(),
						$formData->getActionURL(),
						$elementName,
						$elementData['value'],
						$elementData['type'],
						$formData->getElements(),
						$isPOSTForm);
			}

			// Adding data complex for XSS test into XSSVulnerableDataCollection
			$XSSVulnerableDataCollection[] = $data;
		}

		return $XSSVulnerableDataCollection;
	}

	/**
	 * This method validates input type.
	 * Valid inputs: button/checkbox/radio/hidden/password/submit/text
	 * @access private
	 * @return string|boolean
	 */
	private static function checkInputType($inputType) {

		$isValidType = true;

		switch ($inputType) {
			case 'button':
				break;

			case 'checkbox':
				break;

			case 'radio':
				break;

			case 'hidden':
				break;

			case 'password':
				break;

			case 'submit':
				break;

			case 'text':
				break;

			default:
				$isValidType = false;
				break;
		}

		return $isValidType;
	}
}