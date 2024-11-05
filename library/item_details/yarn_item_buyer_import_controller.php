<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../includes/common.php');
$permission=$_SESSION['page_permission'];
$user_id=$_SESSION['logic_erp']['user_id'];
		
function check_special_character($string)
{
	$special_charater='*\'£$&()#~|=_"`^\\';
	$specialCharactersArr = str_split($special_charater);
	$splitStringArr=str_split($string);
	$result=array_diff($specialCharactersArr,$splitStringArr);
	if (count($result)<count($specialCharactersArr)) return 1;
	else return 0;
}

$cdate=date("d-m-Y");
include('excel_reader.php');
$output = `uname -a`;
if( isset( $_POST["submit"] ) )
{	
	//error_reporting(E_ALL);
	//ini_set('display_errors', '1');	
	extract($_REQUEST);

	$company_library=return_library_array("select company_name, id from lib_company where status_active=1 and is_deleted=0","company_name","id");		
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer",'buyer_name','id');
	
	foreach (glob("files/"."*.xls") as $filename){			
		@unlink($filename);
	}
	foreach (glob("files/"."*.xlsx") as $filename){			
		@unlink($filename);
	}

	$source_excel = $_FILES['uploadfile']['tmp_name'];
	$targetzip ='files/'.$_FILES['uploadfile']['name'];
	$file_name=$_FILES['uploadfile']['name'];
    // echo $source_excel.'**'.$targetzip.'**'.$file_name;die;
	unset($_SESSION['excel']);
	if (move_uploaded_file($source_excel, $targetzip)) 
	{
		$excel = new Spreadsheet_Excel_Reader($targetzip); 

		//die("system testing");

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
				$company=$buyer=$excel_prod_id='';
				//echo '<pre>';print_r($excel->sheets[0]['cells'][$i]);die;
				
				$str_rep=array("*",  "=", "\r", "\n", "#");
				//$str_rep= preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $excel->sheets[0]['cells'][$i]); 
				if (isset($excel->sheets[0]['cells'][$i][1])) $company = str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][1]);
				if (isset($excel->sheets[0]['cells'][$i][2])) $buyer= str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][2]);			
				if (isset($excel->sheets[0]['cells'][$i][3])) $excel_prod_id = str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][3]);				

				$all_data_arr[$i][1]['company']=trim($company);
				$all_data_arr[$i][2]['buyer']=trim($buyer);
				$all_data_arr[$i][3]['excel_prod_id']=(int) trim($excel_prod_id);
			}
		}
		
		$current_date = date("Y-m-d");
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
	
		$field_array_trans_update="buyer_id*is_excel*updated_by*update_date";

		$row_num_excel=1;
		$is_excel=1;
		
		foreach($all_data_arr as $column_val)
		{
			$row_num_excel++;

			$excel_prod_id= (int)$column_val[3]['excel_prod_id'];
			$company_id=$company_library[$column_val[1]['company']];
			$buyer_id=$buyer_library[$column_val[2]['buyer']];

			//echo $excel_prod_id."==".$company_id."==".$buyer_id."<br>";

			if($excel_prod_id<1)
			{
				if ( $column_val[1]['company']=="" && $company_id=="")
				{
					echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Fill-Up Correct Company Name ['.$column_val[1]["company"].'] and Excel row number ['.$row_num_excel.']</p>';
					oci_rollback($con); disconnect($con); die;
				}
				
				if ($column_val[2]['buyer']=="" && $buyer_id=="")
				{
					echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Fill-Up Correct Buyer Name ['.$column_val[2]["buyer"].'] and Excel row number ['.$row_num_excel.']</p>';
					oci_rollback($con); disconnect($con); die;
				}
			}

			if ($excel_prod_id != "") //update item with stock
			{
				$data_arr_trans = "".$buyer_id."*".$is_excel."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
				$updated_by = $_SESSION['logic_erp']['user_id'];
				$update_date = $pc_date_time;

				$rID = execute_query("UPDATE inv_transaction SET buyer_id=$buyer_id, is_excel=$is_excel, updated_by=$updated_by, update_date='".$update_date."' WHERE prod_id = $excel_prod_id and company_id = $company_id and item_category = 1 and transaction_type in (1,4,5) and buyer_id is null and status_active = 1");		

				if(!$rID)
				{
					echo "UPDATE inv_transaction SET buyer_id=$buyer_id, is_excel=$is_excel, updated_by=$updated_by, update_date='".$update_date."' WHERE prod_id = $excel_prod_id and company_id = $company_id and item_category = 1 and transaction_type in (1,4,5) and buyer_id is null and status_active = 1";

					if($db_type==0)
					{
						mysql_query("ROLLBACK");die;
					}
					else
					{
						oci_rollback($con);die;
					}
				}				
			}
		}

		//echo $rID ; oci_rollback($con);die();
		
		if($db_type==0)
		{
			if($rID==1)
			{
				mysql_query("COMMIT");
				echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Excel File Upload Successfully...</p><br/>';
				echo $all_datas;
			}
			else
			{
				mysql_query("ROLLBACK");
				echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Excel File is not Uploaded...</p><br/>';
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID==1)
			{
				oci_commit($con);
				echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Excel File Upload Successfully...</p><br/>';
				echo $all_datas;
			}
			else
			{
				oci_rollback($con);
				echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Excel File is not Uploaded...</p><br/>';		
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


function sql_update_test($strTable,$arrUpdateFields,$arrUpdateValues,$arrRefFields,$arrRefValues,$commit="",$return_query='')
{

	$strQuery = "UPDATE ".$strTable." SET ";
	$arrUpdateFields=explode("*",$arrUpdateFields);
	$arrUpdateValues=explode("*",$arrUpdateValues);

	if(count($arrUpdateFields)!=count($arrUpdateValues)){
		return "0";
	}

	if(is_array($arrUpdateFields))
	{
		$arrayUpdate = array_combine($arrUpdateFields,$arrUpdateValues);
		$Arraysize = count($arrayUpdate);
		$i = 1;
		foreach($arrayUpdate as $key=>$value):
			$strQuery .= ($i != $Arraysize)? $key."=".$value.", ":$key."=".$value;
			$i++;
		endforeach;
	}
	else
	{
		$strQuery .= $arrUpdateFields."=".$arrUpdateValues;
	}
	$strQuery .=" WHERE ";

	$arrRefFields=explode("*",$arrRefFields);
	$arrRefValues=explode("*",$arrRefValues);
	if(is_array($arrRefFields))
	{
		$arrayRef = array_combine($arrRefFields,$arrRefValues);
		$Arraysize = count($arrayRef);
		$i = 1;
		foreach($arrayRef as $key=>$value):
			$strQuery .= ($i != $Arraysize)? $key."=".$value." AND ":$key."=".$value."";
			$i++;
		endforeach;
	}
	else
	{
		$strQuery .= $arrRefFields."=".$arrRefValues."";
	}
	if($return_query==1){return $strQuery ;}

		return $strQuery;die;
	global $con;
	if( strpos($strQuery, "WHERE")==false)  return "0";
	$stid =  oci_parse($con, $strQuery);
	$exestd=oci_execute($stid,OCI_NO_AUTO_COMMIT);
	
	if ($exestd){user_activities($exestd);}
	if ($exestd)
		return "1";
	else
		return "0";

	die;
	if ( $commit==1 )
	{
		if (!oci_error($stid))
		{
			oci_commit($con);
			return "1";
		}
		else
		{
			oci_rollback($con);
			return "10";
		}
	}
	else
		return 1;
	die;
}
?>	