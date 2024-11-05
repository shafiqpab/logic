<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../includes/common.php');
//echo '<pre>';print_r($_SESSION);
$permission=$_SESSION['page_permission'];

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
				$item_category_val=$group_code=$item_group='';
				$trims_type_val=$order_uom_higher=$order_uom_lower='';
				$conv_factor=$fancy_item=$cal_parameter_val=$status='';
				//echo '<pre>';print_r($excel->sheets[0]['cells'][$i]);die;
				
				$str_rep=array("*",  "=", "\r", "\n", "#");
				//$str_rep= preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $excel->sheets[0]['cells'][$i]); 
				if (isset($excel->sheets[0]['cells'][$i][1])) $item_category_val= trim(str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][1]));
				if (isset($excel->sheets[0]['cells'][$i][2])) $group_code = trim(str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][2]));
				if (isset($excel->sheets[0]['cells'][$i][3])) $item_group = trim(str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][3]));
				if (isset($excel->sheets[0]['cells'][$i][4])) $trims_type_val = trim(str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][4]));
				if (isset($excel->sheets[0]['cells'][$i][5])) $order_uom_higher = trim(str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][5]));
				if (isset($excel->sheets[0]['cells'][$i][6])) $order_uom_lower = trim(str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][6]));
				if (isset($excel->sheets[0]['cells'][$i][7])) $conv_factor  = trim(str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][7]));
				if (isset($excel->sheets[0]['cells'][$i][8])) $fancy_item = trim(str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][8]));
				if (isset($excel->sheets[0]['cells'][$i][9])) $cal_parameter_val= trim(str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][9]));
				if (isset($excel->sheets[0]['cells'][$i][10])) $status = trim(str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][10]));
				

				$all_data_arr[$i][1]['item_category_val']=$item_category_val;
				$all_data_arr[$i][2]['group_code']=$group_code;
				$all_data_arr[$i][3]['item_group']=$item_group;
				$all_data_arr[$i][4]['trims_type_val']=$trims_type_val;
				$all_data_arr[$i][5]['order_uom_higher']=$order_uom_higher;
				$all_data_arr[$i][6]['order_uom_lower']=$order_uom_lower;
				$all_data_arr[$i][7]['conv_factor']=$conv_factor;
				$all_data_arr[$i][8]['fancy_item']=$fancy_item;
				$all_data_arr[$i][9]['cal_parameter_val']=$cal_parameter_val;
				$all_data_arr[$i][10]['status']=$status;
			
			}
		}

		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$item_category_arr = array_flip($item_category);
		$trims_type_arr = array_flip($trim_type);
		$unit_of_measurement_arr = array_flip($unit_of_measurement);
		$yes_no_arr = array_flip($yes_no);
		$cal_parameter_arr = array_flip($cal_parameter);
		$row_status_arr = array_flip($row_status);

		$id=return_next_id( "id", "lib_item_group", 1 );
		$field_array="id, item_name, item_category, item_group_code, trim_type, order_uom, trim_uom, conversion_factor, fancy_item, cal_parameter, status_active, is_deleted, inserted_by, insert_date";

		$row_num_excel=1;
		$data_array='';
		foreach ($all_data_arr as $column_val)
		{
			$row_num_excel++;
			$item_category_val = $item_category_arr[$column_val[1]['item_category_val']];
			if ($column_val[1]['item_category_val']=="" || $item_category_val==""){
				echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Fill-Up Correct Item Category['.$column_val[1]["item_category_val"].'] and Excel row number ['.$row_num_excel.']</p>';
				disconnect($con);die;
			}

			$group_code = $column_val[2]['group_code'];

			$item_group = $column_val[3]['item_group'];
			if ($item_group==""){
				echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Fill-Up Item Group and Excel row number ['.$row_num_excel.']</p>';
				disconnect($con);die;
			}

			$trims_type_val = $trims_type_arr[$column_val[4]['trims_type_val']];
			if ($column_val[4]['trims_type_val'] !=""){
				if ($trims_type_val==""){
					echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Fill-Up Correct Trims Type['.$column_val[4]['trims_type_val'].'] and Excel row number ['.$row_num_excel.']</p>';
					disconnect($con);die;
				}				
			}
			if ($trims_type_val == "") $trims_type_val=0;

			$order_uom_higher = $unit_of_measurement_arr[$column_val[5]['order_uom_higher']];
			if ($column_val[5]['order_uom_higher']=="" || $order_uom_higher==""){
				echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Fill-Up Correct Order UOM Higher['.$column_val[6]['order_uom_higher'].'] and Excel row number ['.$row_num_excel.']</p>';
				disconnect($con);die;
			}

			$order_uom_lower = $unit_of_measurement_arr[$column_val[6]['order_uom_lower']];
			if ($column_val[6]['order_uom_lower']=="" || $order_uom_lower==""){
				echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Fill-Up Correct Order UOM Lower['.$column_val[6]['order_uom_lower'].'] and Excel row number ['.$row_num_excel.']</p>';
				disconnect($con);die;
			}

			$conv_factor = $column_val[7]['conv_factor'];

			$fancy_item = $yes_no_arr[$column_val[8]['fancy_item']];
			if ($column_val[8]['fancy_item'] !=""){
				if ($fancy_item==""){
					echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Fill-Up Correct Fancy Item['.$column_val[8]['fancy_item'].'] and Excel row number ['.$row_num_excel.']</p>';
					disconnect($con);die;
				}				
			}
			if ($fancy_item == "") $fancy_item=2;

			$cal_parameter_val = $cal_parameter_arr[$column_val[9]['cal_parameter_val']];
			if ($column_val[9]['cal_parameter_val'] !=""){
				if ($cal_parameter_val==""){
					echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Fill-Up Correct Cal Parameter['.$column_val[9]['cal_parameter_val'].'] and Excel row number ['.$row_num_excel.']</p>';
					disconnect($con);die;
				}				
			}
			if ($cal_parameter_val == "") $cal_parameter_val=0;

			$status = $row_status_arr[$column_val[10]['status']];
			if ($status != 1) $action=1;

			
			if (is_duplicate_field( "item_name", "lib_item_group", "item_category=$item_category_val and item_name='$item_group' and is_deleted=0" ) == 1)
			{
				echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Duplicate Data Found and Excel row number ['.$row_num_excel.']</p>';
				disconnect($con); die;
			}
			//echo "alert('AMC EMI Processed upto. Please check in AMC EMI Report for details');\n";			
			
			if ($data_array != '') $data_array .=",";
			$data_array.="(".$id.",'".$item_group."','".$item_category_val."','".$group_code."','".$trims_type_val."','".$order_uom_higher."','".$order_uom_lower."','".$conv_factor."','".$fancy_item."','".$cal_parameter_val."',".$status.",0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			$id=$id+1;
		}

		//echo "10** insert into lib_item_group ($field_array) values $data_array";die;
		$rID=sql_insert("lib_item_group",$field_array,$data_array,0);
		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");
				echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Excel File Upload Successfully...</p>';				
			}
			else
			{
				mysql_query("ROLLBACK");
				echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Excel File is not Uploaded...</p>';
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
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
