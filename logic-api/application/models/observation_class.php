<?php
class Observation {
	public $ID;
	public $DEFECT_NAME;
	public $FOUND_IN_INCH;
	public $DEPARTMENT;
	function __construct($id, $defect_name, $found_in_inch, $department) {
		$this->ID = $id;
		$this->DEFECT_NAME = $defect_name;
		$this->FOUND_IN_INCH = $found_in_inch;
		$this->DEPARTMENT = $department;
	}
}
?>