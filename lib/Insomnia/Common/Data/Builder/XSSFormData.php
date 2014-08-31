<?php
/**
 * This class reperesnts form entity. Contains info about:
 * - action
 * - method
 * - input/select/textarea data in array form.
 */
class Insomnia_Common_Data_Builder_XSSFormData
{
	/**
	 * @var string $action Form action attribute (URL).
	 */
	private $action;

	/**
	 * @var string $method Form method attribute ("GET"/"POST").
	 */
	private $method;

	/**
	 * @var array $elements Form: array ('name' => ('value' => .. , 'type' => .. ))
	 */
	private $elements = array();

	/**
	 * @param string $action Form action attribute (URL).
	 * @param string $method Form method attribute ("GET"/"POST").
	 */
	public function __construct($action, $method) {
		$this->action = $action;
		$this->method = $method;
	}

	/**
	 * This method adds new element into element collection $this->elements.
	 * @param string $elemName Name of element.
	 * @param string $elemValue Value of element.
	 * @param string $elemType Type of element ('input-XXX','select-option','textarea').
	 */
	public function addElemment($elemName, $elemValue, $elemType) {
		$this->elements[$elemName] = array('value' => $elemValue, 'type' => $elemType);
	}

	/**
	 * @return string
	 */
	public function getActionURL() {
		return $this->action;
	}

	/**
	 * @return string
	 */
	public function getMethod() {
		return $this->method;
	}

	/**
	 * @return string
	 */
	public function getElements() {
		return $this->elements;
	}
}