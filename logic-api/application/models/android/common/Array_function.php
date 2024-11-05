<?php

class Array_function extends CI_Model {

	function __construct() {
		error_reporting(0);
		parent::__construct();
	}

	
	public function get_fabric_shade_array() {
		$dataArr[data_arr][]=array('key'=>1,'value'=>"A");
		$dataArr[data_arr][]=array('key'=>2,'value'=>"B");
		$dataArr[data_arr][]=array('key'=>3,'value'=>"C");
		$dataArr[data_arr][]=array('key'=>4,'value'=>"D");
		$dataArr[data_arr][]=array('key'=>5,'value'=>"E");
		return $dataArr ;
	}//end method;
	
	public function get_knit_finish_defect_inchi_array() {
		$dataArr[data_arr][]=array('key'=>1,'value'=>'Defect=<3" : 1');
		$dataArr[data_arr][]=array('key'=>2,'value'=>'Defect=<6" but >3" : 2');
		$dataArr[data_arr][]=array('key'=>3,'value'=>'Defect=<9" but >6" : 3');
		$dataArr[data_arr][]=array('key'=>4,'value'=>'Defect>9" : 4');
		$dataArr[data_arr][]=array('key'=>5,'value'=>'Hole<1" : 2');
		$dataArr[data_arr][]=array('key'=>6,'value'=>'Hole>1" : 4');
		return $dataArr ;
	}//end method;
	
	public function get_ovservation_knit_finish_defect_inchi_array() {
		$dataArr[data_arr][]=array('key'=>1,'value'=>'Select');
		$dataArr[data_arr][]=array('key'=>2,'value'=>'Present');
		$dataArr[data_arr][]=array('key'=>3,'value'=>'Not Found');
		$dataArr[data_arr][]=array('key'=>4,'value'=>'Major');
		$dataArr[data_arr][]=array('key'=>5,'value'=>'Minor');
		$dataArr[data_arr][]=array('key'=>6,'value'=>'Acceptable');
		$dataArr[data_arr][]=array('key'=>7,'value'=>'Good');
		return $dataArr ;
		
	}//end method;
	
	public function get_ovservation_knit_finish_qc_defect_array(){
		$dataArr[data_arr][]=array('key'=>1,'value'=>"Fly Conta");
		$dataArr[data_arr][]=array('key'=>2,'value'=>"PP conta");
		$dataArr[data_arr][]=array('key'=>3,'value'=>"Patta/Barrie");
		$dataArr[data_arr][]=array('key'=>4,'value'=>"Needle Mark");
		$dataArr[data_arr][]=array('key'=>5,'value'=>"Sinker Mark");
		$dataArr[data_arr][]=array('key'=>6,'value'=>"thick-thin");
		$dataArr[data_arr][]=array('key'=>7,'value'=>"neps/knot");
		$dataArr[data_arr][]=array('key'=>8,'value'=>"white speck");
		$dataArr[data_arr][]=array('key'=>9,'value'=>"Black Speck");
		$dataArr[data_arr][]=array('key'=>10,'value'=>"Star Mark");
		$dataArr[data_arr][]=array('key'=>11,'value'=>"Dia/Edge Mark");
		$dataArr[data_arr][]=array('key'=>12,'value'=>"Dead fibre");
		$dataArr[data_arr][]=array('key'=>13,'value'=>"Running shade");
		$dataArr[data_arr][]=array('key'=>14,'value'=>"Hairiness");
		$dataArr[data_arr][]=array('key'=>15,'value'=>"crease mark");
		$dataArr[data_arr][]=array('key'=>16,'value'=>"Uneven");
		$dataArr[data_arr][]=array('key'=>17,'value'=>"Padder Crease");
		$dataArr[data_arr][]=array('key'=>18,'value'=>"Absorbency");
		$dataArr[data_arr][]=array('key'=>19,'value'=>"Bowing");
		$dataArr[data_arr][]=array('key'=>20,'value'=>"Handfeel");
		$dataArr[data_arr][]=array('key'=>21,'value'=>"Dia Up-down");
		$dataArr[data_arr][]=array('key'=>22,'value'=>"Cut hole");
		$dataArr[data_arr][]=array('key'=>23,'value'=>"Snagging/Pull out");
		$dataArr[data_arr][]=array('key'=>24,'value'=>"Pin Hole");
		$dataArr[data_arr][]=array('key'=>25,'value'=>"Bad Smell");
		$dataArr[data_arr][]=array('key'=>26,'value'=>"Bend Mark");
		return $dataArr ;
	}//end method;
	
	public function get_knit_finish_qc_defect_array(){
		
		$defectArr = return_library_array("select defect_name, short_name from  lib_defect_name where type=1 order by defect_name", "defect_name", "short_name");
		$dataArr=array();
		foreach($defectArr as $key=>$val){
			$dataArr[data_arr][]=array('key'=>$key,'value'=>$val);	
		}
		return $dataArr ;
	}//end method;
	

	public function get_department_array(){
		
		//$dataArr[data_arr][]=array('key'=>1,'value'=>"Cutting");
		$dataArr[data_arr][]=array('key'=>2 ,'value'=> "Knitting");
		$dataArr[data_arr][]=array('key'=>3 ,'value'=> "Dyeing");
		$dataArr[data_arr][]=array('key'=>4 ,'value'=> "Finishing");
		//$dataArr[data_arr][]=array('key'=>5 ,'value'=> "Sewing");
		//$dataArr[data_arr][]=array('key'=>6 ,'value'=> "Fabric Printing");
		//$dataArr[data_arr][]=array('key'=>7 ,'value'=> "Washing");
		//$dataArr[data_arr][]=array('key'=>8 ,'value'=> "Gmts Printing");
		//$dataArr[data_arr][]=array('key'=>9 ,'value'=> "Embroidery");
		//$dataArr[data_arr][]=array('key'=>10 ,'value'=> "Iron");
		//$dataArr[data_arr][]=array('key'=>11 ,'value'=> "Gmts Finishing");
		//$dataArr[data_arr][]=array('key'=>12 ,'value'=> "Gmts Dyeing");
		//$dataArr[data_arr][]=array('key'=>13 ,'value'=> "Poly");
		//$dataArr[data_arr][]=array('key'=>14 ,'value'=> "Re Conning");
		$dataArr[data_arr][]=array('key'=>15 ,'value'=> "Common");
		//$dataArr[data_arr][]=array('key'=>16 ,'value'=> "Knit Finish Fabric");
		//$dataArr[data_arr][]=array('key'=>17 ,'value'=> "Dyeing process");		
		return $dataArr ;
	}//end method;














}//class;
