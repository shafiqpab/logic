<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];

echo load_html_head_contents("Fast React Import","../../", 1, 1, $unicode,1,'');

include( 'excel_reader.php' );
$output = `uname -a`;
if( isset( $_POST["submit"] ) )
{	
	error_reporting(E_ALL);
	//ini_set('display_errors', '1');
	
	extract($_REQUEST);
	
	foreach (glob("files/"."*.xls") as $filename){			
		@unlink($filename);
	}
	foreach (glob("files/"."*.xlsx") as $filename){			
		@unlink($filename);
	}
	//die;
	$source = $_FILES['uploadfile']['tmp_name'];
	$targetzip ='files/'.$_FILES['uploadfile']['name'];
	$file_name=$_FILES['uploadfile']['name'];
	unset($_SESSION['excel']);
	if (move_uploaded_file($source, $targetzip)) 
	{
		$excel = new Spreadsheet_Excel_Reader($targetzip);  
		//$excel->read($targetzip);
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$id=return_next_id( "id", "fr_import", 1);
		$field_arr="id, frdate, line, style, description, product_type, order_no, color, plan_qty";
		$data_arr=array();
		
		$card_colum=0; $m=1; $style_data_array=array(); $po_data_array=array(); $country_data_array=array(); $style_all_data_arr=array();
		for ($i = 1; $i <= $excel->sheets[0]['numRows']; $i++) 
		{
			if($m==1)
			{
				for ($j = 1; $j <= $excel->sheets[0]['numCols']; $j++) 
				{
					//$k++;
					//echo "\"".$data->sheets[0]['cells'][$i][4]."\",";
					//$card_colum=$excel->sheets[0]['cells'][$i][$j];
					
					//echo $card_colum.'=='.$i.'=='.$j.'<br>';
					/*$date_fld2=$data->sheets[0]['cells'][$i][$date_fld];
					$in_out_time=$data->sheets[0]['cells'][$i][$time_fld_len[0]].",".$data->sheets[0]['cells'][$i][$time_fld_len[1]];*/
					//print_r($in_out_time_arr);
					//$date_time_colum=$data->sheets[0]['cells'][$i][4];
				}
				$m++;
			}
			else
			{ 
				$all_data='';
				$str_rep=array("(", ")", "'",",","\r", "\n",'"');
				
				$date_cell=date("d-m-Y", strtotime( str_replace('/', '-',$excel->sheets[0]['cells'][$i][1]) ));
				//echo $date_cell.'<br>'; 
				if($db_type==0)
				{
					$date=change_date_format($date_cell, "Y-m-d", "-",1);
				}
				else
				{
					$date=change_date_format($date_cell, "d-M-y", "-",1);
				}
				
				$line=$excel->sheets[0]['cells'][$i][2];
				
				$style=str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][3]);
				$description=str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][4]);
				$product_type=$excel->sheets[0]['cells'][$i][5];
				$order_no=$excel->sheets[0]['cells'][$i][6];
				$color=str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][7]);
				$plancut_qty=$excel->sheets[0]['cells'][$i][8]*1;//str_replace("-",$excel->sheets[0]['cells'][$i][8])*1;
				//echo $date.'__'.$line.'__'.$style.'__'.$description.'__'.$product_type.'__'.$order_no.'__'.$color.'__'.$plancut_qty.'<br>';
				
				if ($add_comma!=0)
				$data_arr[$id] ="(".$id.",'".$date."','".$line."','".$style."','".$description."','".$product_type."','".$order_no."','".$color."','".$plancut_qty."')";
				$add_comma++;
				$id=$id+1;
			}
		}
		//die;
		$roll_back_msg="Data not save.";
		$commit_msg="Data Save Successfully.";
		$flag=1;
		$data_str=array_chunk($data_arr,1);
		foreach( $data_str as $setRows)
		{
			//echo "10**INSERT INTO fr_import (".$field_arr.") VALUES ".implode(",",$setRows).'<br>'; 
			$rID=sql_insert("fr_import",$field_arr,implode(",",$setRows),0);
			//echo $rID.'<br>';
			if($rID==1) $flag=1; //else $flag=0;
			else if($rID==0) 
			{
				//echo "10**INSERT INTO fr_import (".$field_arr.") VALUES ".implode(",",$setRows).'<br>';
				$flag=0;
				if($db_type==0)
				{
					mysql_query("COMMIT");
					echo "10**".$roll_back_msg; die;
				}
				else if($db_type==2 || $db_type==1 )
				{
					oci_rollback($con);
					echo "10**".$roll_back_msg; die;
				}
			}
		}
		
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "0**".$commit_msg;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$roll_back_msg;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);  
				echo "0**".$commit_msg;
			}
			else
			{
				oci_rollback($con);
				echo "10**".$roll_back_msg;
			}
		}
		
		disconnect($con);
		die;
	}
	else
	{
		echo "File Upload Failed.";	
	}
	die;
}
else
{
	echo "File Submit Failed.";
}
?>
