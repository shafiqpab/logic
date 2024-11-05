<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//--------------------------------------------------------------------------------------------------------------------

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location", 110, "select distinct location_name from lib_employee where status_active =1 and is_deleted=0 order by location_name","location_name,location_name", 1, "-- Select --", $selected, "",0 );  
	die;   	 
}

if ($action=="load_drop_down_floor")
{
	echo create_drop_down( "cbo_floor", 110, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' order by floor_name","id,floor_name", 1, "-- Select --", $selected, "",0 );   
	die;  	 
}


if($action=="show_employee_listview")
{
	//echo $data;
	$data=explode("__",$data);
	if( $data[0]!="" ) $comp_cond=" and company_name='$data[0]' "; else $comp_cond="";
	//if( $data[1]!="" || $data[1]!=0 ) $loc_cond=" and location_name='$data[1]' "; else $loc_cond="";
	if(  $data[2]!='0' && $data[2]!='' ) $div_cond=" and division_name='$data[2]' "; else $div_cond="";
	if(  $data[3]!='0' && $data[3]!='') $dep_cond=" and department_name='$data[3]' "; else $dep_cond="";
	if(  $data[4]!='0' && $data[4]!='') $floor_cond=" and floor_id='$data[4]' "; else $floor_cond="";
	if( $data[5]!='0' && $data[5]!='') $line_cond=" and line_name='$data[5]' "; else $line_cond="";
	if( $data[6]!="" ) $emp_code_cond=" and emp_code like '%$data[6]%' "; else $emp_code_cond="";
	if( $data[7]!="" ) $emp_id_cond=" and id_card_no like '%$data[7]%' "; else $emp_id_cond="";
	// echo "Select emp_code,first_name,middle_name,last_name,emp_catagory,designation_id,id_card_no,joining_date,location_id,division_id,department_id,section_id,line_no,subsection_id,floor_id from lib_employee where status_active=1 and is_deleted=0 $comp_cond $loc_cond $div_cond $dep_cond $floor_cond $line_cond $emp_cond";  
	$emp_data = "Select emp_code,id_card_no, concat(first_name,middle_name,last_name) as emp_name, designation_name, line_name, company_name, location_id, division_name,department_name,section_name from lib_employee where status_active=1 and is_deleted=0 $comp_cond $loc_cond $div_cond $dep_cond $floor_cond $line_cond $emp_code_cond $emp_id_cond";
	// echo $emp_data; die;
	/*
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	$division_arr=return_library_array( "select id, division_name from lib_division",'id','division_name');
	$department_arr=return_library_array( "select id, department_name from lib_department",'id','department_name');
	$section_arr=return_library_array( "select id, section_name from lib_section",'id','section_name');
	$line_no_arr=return_library_array( "select id, line_name from  lib_sewing_line",'id','line_name');
	$designation_arr=return_library_array( "select id, custom_designation from lib_designation",'id','custom_designation');
	//print_r($location_arr);
	
	$arr=array(2=>$designation_arr,3=>$line_no_arr,4=>$company_arr,5=>$location_arr,6=>$division_arr,7=>$department_arr,8=>$section_arr);
	*/
	echo create_list_view("list_view", "Emp Code,ID Card,Employee Name,Designation,Line No,Company,Location,Division,Department,Section","80,80,140,120,110,110,110,110,110,110","1140","260",0, $emp_data , "js_set_value", "emp_code", "", 1, "0,0,0,0,0,0,0,0,0,0", $arr, "emp_code,id_card_no,emp_name,designation_name,line_name,company_name,location_id,division_name,department_name,section_name", "requires/sewing_line_controller",'','','','1') ;	
	//$arr , "company_name,location_name,floor_name,line_name,sewing_line_serial", "../production/requires/sewing_line_controller", 'setFilterGrid("list_view",-1);' 
	
	die;
}


if($action=="print_report_employee_barcode")
{
	define('FPDF_FONTPATH', '../../../ext_resource/pdf/fpdf/font/');
	require('../../../ext_resource/pdf/code39.php');

	$floor_arr=return_library_array( "select id, floor_name from  lib_prod_floor",'id','floor_name');
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	$division_arr=return_library_array( "select id, division_name from lib_division",'id','division_name');
	$department_arr=return_library_array( "select id, department_name from lib_department",'id','department_name');
	$section_arr=return_library_array( "select id, section_name from lib_section",'id','section_name');
	$line_no_arr=return_library_array( "select id, line_name from  lib_sewing_line",'id','line_name');
	$designation_arr=return_library_array( "select id, custom_designation from lib_designation",'id','custom_designation');
	
	$data=explode(",",$data);  
	$data="'".implode("','",$data)."'";

	$ext_data=explode("__",$data[1]);
	$cs_data=explode("__",$data[2]);
	 
	$pdf=new PDF_Code39();
	$pdf->AddPage();
	$color_sizeID_arr=sql_select( "select emp_code,id_card_no,first_name,middle_name,dob,designation_name, line_name,floor_id, company_name, location_id, division_name,department_name,section_name from lib_employee where emp_code in ( $data ) order by emp_code" ); 
	$i=5; $j=10; $k=0;
    $bundle_array=array();
	$br=0;
	$n=0;
	$n=1;
	foreach($color_sizeID_arr as $val)
	{
		
		$pdf->Code39($i, $j, $val[csf("emp_code")]);
		$pdf->Code39($i+38, $j-5,  "Emp Name	:".$val[csf("first_name")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
		$pdf->Code39($i+90, $j-5,  "ID Card	:".$val[csf("id_card_no")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
		$pdf->Code39($i+123, $j-5, "Desig:".$val[csf("designation_name")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
		$pdf->Code39($i+38, $j, "Dept.:".$val[csf("department_name")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
		$pdf->Code39($i+90, $j, "Sect.	:".$val[csf("section_name")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
		$pdf->Code39($i+123, $j, "Floor.	:".$floor_arr[$val[csf("floor_id")]], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
		$pdf->Code39($i+145, $j, "Line.	:".$val[csf("line_name")], $ext = true, $cks = false, $w = 0.2, $h = 10, $wide = true, true) ;
		if($n!=count($color_sizeID_arr)) $pdf->AddPage();
		$n++;
	}
	foreach (glob(""."*.pdf") as $filename) {			
			@unlink($filename);
		}
	$name = 'emp_barcode_' . date('j-M-Y_h-iA') . '.pdf';
	$pdf->Output( "".$name, 'F');
	echo $name;
}


?>