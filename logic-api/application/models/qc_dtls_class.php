<?php
class QcDtls
{ 
	public $DEFECT_NAME; 
    public $DEFECT_COUNT; 
    public $FOUND_IN_INCH; 
    public $PENALTY_POINT; 
    function __construct( $defect_name,$defect_count,$found_in_inch,$penalty_point)
    {
        
       $this->DEFECT_NAME=$defect_name;
       $this->DEFECT_COUNT=$defect_count;
       $this->FOUND_IN_INCH=$found_in_inch;
       $this->PENALTY_POINT=$penalty_point;
        
    }

} 
?> 