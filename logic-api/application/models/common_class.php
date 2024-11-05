<?php
class Common
{
    public $ID;
    public $FLOOR_NAME;
    public $LOCATION_ID;
    public $COMPANY_ID;
    function __construct($id,$floor_name,$company_id,$location_id)
    {
       $this->ID=$id;
       $this->FLOOR_NAME=$floor_name;
       $this->LOCATION_ID=$location_id;
       $this->COMPANY_ID=$company_id;
        
      
    }

} 
?> 