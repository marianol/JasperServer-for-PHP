<?php
namespace Jasper;

class ReportOptions {

	public $uri;
	public $id;
	public $label;

	public function __construct($uri = null, $id = null, $label = null) {
		$this->uri = (!empty($uri)) ? strval($uri) : null;
		$this->id = (!empty($id)) ? strval($id) : null;
		$this->label = (!empty($label)) ? strval($label) : null;
	}

	public static function createFromJSON($json) {
		$data_array = json_decode($json, true);
		$result = array();
		foreach ($data_array['reportOptionsSummary'] as $k) {
			$result[] = new self($k['uri'], $k['id'], $k['label']);
		}
		return $result;
	}

	public function getUri() {
		return $this->uri;
	}

	public function getId() {
		return $this->id;
	}

	public function getLabel() {
		return $this->label;
	}

	public function setUri($uri) {
		$this->uri = $uri;
	}

	public function setId($id) {
		$this->id = $id;
	}

	public function setLabel($label) {
		$this->label = $label;
	}

}

class InputOptions {

	public $uri;
	public $id;
	public $value;
	public $error;
	public $options = array();

	public function __construct($uri = null, $id = null, $value = null, $error = null) {
		$this->uri = (!empty($uri)) ? strval($uri) : null;
		$this->id = (!empty($id)) ? strval($id) : null;
		$this->value = (!empty($value)) ? strval($value) : null;
		$this->error = (!empty($error)) ? strval($error) : null;
	}

	public static function createFromJSON($json) {
		$data_array = json_decode($json, true);
		$result = array();
		foreach($data_array['inputControlState'] as $k) {
			$temp = new self($k['uri'], $k['id'], $k['value'], $k['error']);
			if (!empty($k['options'])) {
				foreach ($k['options'] as $o) {
					$temp->addOption($o['label'], $o['value'], $o['selected']);
				}
			}
			$result[] = $temp;
		}
		return $result;
	}

    public static function createFromArray($data_array) {
        $result = array();
        foreach($data_array['inputControlState'] as $k) {
            $temp = new self($k['uri'], $k['id'], $k['value'], $k['error']);
            if (!empty($k['options'])) {
                foreach ($k['options'] as $o) {
                    $temp->addOption($o['label'], $o['value'], $o['selected']);
                }
            }
            $result[] = $temp;
        }
        return $result;
    }
	public function addOption($label, $value, $selected) {
		$temp = array('label' => strval($label), 'value' => strval($value), 'selected' => $selected);
		if($selected == 1) { $temp['selected'] = 'true'; } else { $temp['selected'] = 'false'; }
		$this->options[] = $temp;
	}

	public function getOptions() {
		return $this->options;
	}

	public function getUri() {
		return $this->uri;
	}

    public function getSelected() {
        $selectedValues = array();
        foreach ($this->options as $opt) {
            if ($opt['selected'] == 'true') {
                $selectedValues[] = $opt['value'];
            }
        }
        return $selectedValues;
    }
    
	public function getId() {
		return $this->id;
	}

}

class InputStructure {

    public $id;
    public $type;
    public $uri;
    public $label;
    public $mandatory;
    public $readOnly;
    public $visible;
    public $masterDependencies = array();
    public $slaveDependencies = array();
    public $validationRules = array();
    public $inputOptions = array();

    public function __construct($uri = null, $id = null, $type = null, $label = null, 
                                $mandatory = null, $readOnly = null, $visible = null) {
        $this->uri = (!empty($uri)) ? strval($uri) : null;
        $this->id = (!empty($id)) ? strval($id) : null;
        $this->type = (!empty($type)) ? strval($type) : null;
        $this->label = (!empty($label)) ? strval($label) : null;
        $this->mandatory = (!empty($mandatory)) ? $mandatory : null;
        $this->readOnly = (!empty($readOnly)) ? $readOnly : null;
        $this->visible = (!empty($visible)) ? $visible : null;
    }

    public static function createFromJSON($json) {
        $data_array = json_decode($json, true);
        $result = array();
        foreach($data_array['inputControl'] as $k) {
            $temp = new self($k['uri'], $k['id'], $k['type'], $k['label'], $k['mandatory'], $k['readOnly'],
                             $k['visible']);
            if (!empty($k['state'])) {
                    $temp->inputOptions = InputOptions::createFromArray($k['state']);
            }
            $temp->masterDependencies = $k['masterDependecies'];
            $temp->slaveDependencies = $k['slaveDependencies'];
            $temp->validationRules = $k['validationRules'];
            $result[] = $temp;
        }
        return $result;
    }


    public function getOptions() {
        return $this->inputOptions;
    }

    public function getUri() {
        return $this->uri;
    }

    public function getType() {
        return $this->type;
    }
    
    public function getId() {
        return $this->id;
    }

}
?>