<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../includes/common.php');
//echo '<pre>';print_r($_SESSION);
$permission=$_SESSION['page_permission'];

$operations_grades = [1=>'H-1', 2=>'H-2', 3=>'P', 4=>'Q', 5=>'R', 6=>'S'];

$cdate=date("d-m-Y");
include('excel_reader.php');
$output = `uname -a`;
if( isset( $_POST["submit"] ) )
{	
	//error_reporting(E_ALL);
	//ini_set('display_errors', '1');	
	extract($_REQUEST);
	
	foreach (glob("files/"."*.xls") as $filename){			
		@unlink($filename);
	}
	foreach (glob("files/"."*.xlsx") as $filename){			
		@unlink($filename);
	}

	$source = $_FILES['uploadfile']['tmp_name'];
	$targetzip ='files/'.$_FILES['uploadfile']['name'];
	$file_name=$_FILES['uploadfile']['name'];
    //echo $source.'**'.$targetzip.'**'.$file_name;die;
	unset($_SESSION['excel']);
	if (move_uploaded_file($source, $targetzip)) 
	{
		$excel = new Spreadsheet_Excel_Reader($targetzip);
		$card_colum=0; $m=1; 
		$all_data_arr=array();
		
		//echo $excel->sheets[0]['numCols'];die;

		for ($i = 1; $i <= $excel->sheets[0]['numRows']; $i++) 
		{
			if($m==1)
			{
				for ($j = 1; $j <= $excel->sheets[0]['numCols']; $j++) 
				{
				}
				$m++;
			}
			else
			{
				$product_dept_val=$garments_item_val=$body_part=$operation_name=='';
				$rate=$smv_basis=$seam_length=$resource=$machine_smv='';
				$manual_smv=$department_code=$uom=$action=$ope_grade='';
				//echo '<pre>';print_r($excel->sheets[0]['cells'][$i]);die;
				
				$str_rep=array("*",  "=", "\r", "\n", "#");
				//$str_rep= preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $excel->sheets[0]['cells'][$i]); 
				if (isset($excel->sheets[0]['cells'][$i][1])) $product_dept_val= trim(str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][1]));
				if (isset($excel->sheets[0]['cells'][$i][2])) $garments_item_val = trim(str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][2]));
				if (isset($excel->sheets[0]['cells'][$i][3])) $body_part = trim(str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][3]));
				if (isset($excel->sheets[0]['cells'][$i][4])) $code = trim(str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][4]));
				if (isset($excel->sheets[0]['cells'][$i][5])) $operation_name = trim(str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][5]));
				if (isset($excel->sheets[0]['cells'][$i][6])) $rate = trim(str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][6]));
				if (isset($excel->sheets[0]['cells'][$i][7])) $smv_basis  = trim(str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][7]));
				if (isset($excel->sheets[0]['cells'][$i][8])) $seam_length = trim(str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][8]));
				if (isset($excel->sheets[0]['cells'][$i][9])) $resource= trim(str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][9]));
				if (isset($excel->sheets[0]['cells'][$i][10])) $machine_smv = trim(str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][10]));
				if (isset($excel->sheets[0]['cells'][$i][11])) $manual_smv = trim(str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][11]));
				if (isset($excel->sheets[0]['cells'][$i][12])) $department_code = trim(str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][12]));
				if (isset($excel->sheets[0]['cells'][$i][13])) $uom = trim(str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][13]));
				if (isset($excel->sheets[0]['cells'][$i][14])) $action = trim(str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][14]));
				if (isset($excel->sheets[0]['cells'][$i][15])) $ope_grade = trim(str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][15]));
				

				$all_data_arr[$i][1]['product_dept_val']=$product_dept_val;
				$all_data_arr[$i][2]['garments_item_val']=$garments_item_val;
				$all_data_arr[$i][3]['body_part']=$body_part;
				$all_data_arr[$i][4]['code']=$code;
				$all_data_arr[$i][5]['operation_name']=$operation_name;
				$all_data_arr[$i][6]['rate']=$rate;
				$all_data_arr[$i][7]['smv_basis']=$smv_basis;
				$all_data_arr[$i][8]['seam_length']=$seam_length;
				$all_data_arr[$i][9]['resource']=$resource;
				$all_data_arr[$i][10]['machine_smv']=$machine_smv;
				$all_data_arr[$i][11]['manual_smv']=$manual_smv;
				$all_data_arr[$i][12]['department_code']=$department_code;
				$all_data_arr[$i][13]['uom']=$uom;
				$all_data_arr[$i][14]['action']=$action;
				$all_data_arr[$i][15]['ope_grade']=$ope_grade;
			
			}
		}

		

		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		

		$product_dept_arr = array_flip($product_dept);
		$garments_item_arr = array_flip($garments_item);
		$smv_basis_arr = array_flip($smv_basis);
		//$production_resource_arr = array_flip($production_resource);
		$machine_category_arr = array_flip($machine_category);
		$unit_of_measurement_arr = array_flip($unit_of_measurement);
		$row_status_arr = array_flip($row_status);
		$operations_grade_arr = array_flip($operations_grades);

		$resourceSql = "select PROCESS_ID,RESOURCE_NAME,RESOURCE_ID, ID from LIB_OPERATION_RESOURCE where status_active=1 and is_deleted=0";
		$resourceSqlRes = sql_select($resourceSql);
		$production_resource_arr = array();
		foreach($resourceSqlRes as $pRow){
			$production_resource_arr[$machine_category[$pRow['PROCESS_ID']]][$pRow['RESOURCE_NAME']] = $pRow['RESOURCE_ID'];
		}
		//var_dump($production_resource_arr);die;

		$body_part_arr=return_library_array("select UPPER(body_part_full_name) as body_part_full_name, id from lib_body_part where status_active=1 and is_deleted=0", "body_part_full_name", "id");

		$id=return_next_id( "id", "lib_sewing_operation_entry", 1 );
		$field_array="id, operation_name, ope_grade, rate, uom, resource_sewing, operator_smv, helper_smv, product_dept, bodypart_id, gmt_item_id, product_code, gmts_code, body_part_code, code_prefix, code, smv_basis, seam_length, department_code, status_active, is_excel, inserted_by,  insert_date";

		$row_num_excel=1;
		$code_no=return_next_id( "code_prefix", "lib_sewing_operation_entry", 1 ) ;

		foreach ($all_data_arr as $column_val)
		{
			$row_num_excel++; 

			$product_dept_val = $product_dept_arr[$column_val[1]['product_dept_val']];
			$garments_item_val = $garments_item_arr[$column_val[2]['garments_item_val']];
			//echo $garments_item_val.'system'.$column_val[2]['garments_item_val'];die;
			if ($column_val[2]['garments_item_val']==""){
				echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Fill-Up Garments Item Column and Excel row number ['.$row_num_excel.']</p>';
				oci_rollback($con);disconnect($con);die;
			}
			if ($garments_item_val==""){
				echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Fill-Up Correct Garments Item name and Excel row number ['.$row_num_excel.']</p>';
				oci_rollback($con);disconnect($con);die;
			}

	

			$body_part = $body_part_arr[strtoupper($column_val[3]['body_part'])];
			if ($column_val[3]['body_part']==""){
				echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Fill-Up Body Part  Column and Excel row number ['.$row_num_excel.']</p>';
				oci_rollback($con);disconnect($con);die;
			}
			if ($body_part==""){
				echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Fill-Up Correct Body Part name and Excel row number ['.$row_num_excel.']</p>';
				oci_rollback($con);disconnect($con);die;
			}


			$code=$column_val[4]['code'];
			$ex_code=explode("-",$code);
			$product_code=$ex_code[0];	
			$gmts_code=$ex_code[1];	
			$body_part_code=$ex_code[2];	
			
			
			$full_code=strtoupper($product_code).'-'.strtoupper($gmts_code).'-'.strtoupper($body_part_code).'-'.$code_no;

			$operation_name = $column_val[5]['operation_name'];
			if ($column_val[5]['operation_name']==""){
				echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Fill-Up Operation name  Column and Excel row number ['.$row_num_excel.']</p>';
				oci_rollback($con);disconnect($con);die;
			}

			$rate = $column_val[6]['rate'];

			$smv_basis = $smv_basis_arr[$column_val[7]['smv_basis']];
			if ($smv_basis != 1) $smv_basis=1;
			if ($column_val[7]['smv_basis']==""){
				echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Fill-Up SMV Basis Column and Excel row number ['.$row_num_excel.']</p>';
				oci_rollback($con);disconnect($con);die;
			}

			$seam_length = $column_val[8]['seam_length'];
			$resource = $production_resource_arr[$column_val[12]['department_code']][$column_val[9]['resource']];
			
			
			
			if ($column_val[9]['resource']==""){
				echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Fill-Up Resource Column and Excel row number ['.$row_num_excel.']</p>';
				oci_rollback($con);disconnect($con);die;
			}	

			$machine_smv = $column_val[10]['machine_smv'];
			$manual_smv = $column_val[11]['manual_smv'];
			if ($machine_smv=="" && $manual_smv=="" ){
				echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Fill-Up only one Column (Machine SMV or Manual SMV) and Excel row number ['.$row_num_excel.']</p>';
				oci_rollback($con);disconnect($con);die;
			}
			else if ($machine_smv!="" && $manual_smv!="" ){
				echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Fill-Up only one Column (Machine SMV or Manual SMV) and Excel row number ['.$row_num_excel.']</p>';
				oci_rollback($con);disconnect($con);die;
			}

			//echo $column_val[12]['department_code'];die;
			if ($column_val[12]['department_code']=="" ){
				echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Fill-Up Process Column and Excel row number ['.$row_num_excel.']</p>';
				oci_rollback($con);disconnect($con);die;
			}

			$department_code = $machine_category_arr[$column_val[12]['department_code']];
			$uom = $unit_of_measurement_arr[$column_val[13]['uom']];
			$ope_grade = $operations_grade_arr[$column_val[15]['ope_grade']];

			$action = $row_status_arr[$column_val[14]['action']];
			if ($action != 1) $action=1;

			if($db_type==0) $null_check="IFNULL";
			else $null_check="NVL";
		
			if ($seam_length=="") $seam_length=0; 
			
			if (is_duplicate_field( "operation_name", "lib_sewing_operation_entry", "gmt_item_id='$garments_item_val' and bodypart_id='$body_part' and operation_name='$operation_name' and resource_sewing='$resource' and $null_check(seam_length,0)='$seam_length' and product_dept='$product_dept_val' and is_deleted=0" ) == 1)
			{
				echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Duplicate Data Found and Excel row number ['.$row_num_excel.']</p>';
				oci_rollback($con);disconnect($con);die;
			}
			//echo "alert('AMC EMI Processed upto. Please check in AMC EMI Report for details');\n";			
			
			if ($data_array != '') $data_array .=",";
			$data_array.="(".$id.",'".$operation_name."','".$ope_grade."','".$rate."','".$uom."','".$resource."','".$machine_smv."','".$manual_smv."','".$product_dept_val."','".$body_part."','".$garments_item_val."','".$product_code."','".$gmts_code."','".$body_part_code."','".$code_no."','".$full_code."','".$smv_basis."','".$seam_length."','".$department_code."',".$action.",1,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			$id=$id+1;
			$code_no=$code_no+1;
		}


		//echo $data_array;die;


		// echo "10** insert into lib_sewing_operation_entry ($field_array) values $data_array";die;
		$rID=sql_insert("lib_sewing_operation_entry",$field_array,$data_array,0);
		if($rID)
		{
			oci_commit($con);  
			echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Excel File Upload Successfully...</p>';
		}
		else
		{
			oci_rollback($con); 
			echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Excel File is not Uploaded...</p>';
		}
		disconnect($con);
		die;
	}
	else
	{
		echo "Failed";	
	}
	die;
}

?>
