<?php 
session_start();
include('../../includes/common.php');
 
$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

//$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
extract($_REQUEST);

$fr_product_type_rvrs=array(
"LS TEE"=>1,
"SS TEE"=>2,
"POLO"=>3,
"POLO"=>4,
"TANK TOP"=>5,
"SS TEE"=>6,
"Hoody T-Shirt"=>7,
"Y-NECK TEE"=>8,
"VEST"=>9,
"SS TEE"=>10,
"LS TEE"=>11,
"Scarf"=>12,
"JACKET"=>14,
"JACKET"=>15,
"DRESS"=>16,
"FROCK"=>17,
"DRESS"=>18,
"DRESS"=>19,
"LONG PANT"=>20,
"SHORT PANT"=>21,
"LONG PANT"=>22,
"LONG PANT"=>23,
"JUMP SUIT"=>24,
"JUMP SUIT"=>25,
"VEST"=>26,
"JUMP SUIT"=>27,
"LEGGING"=>28,
"LEGGING"=>29,
"Long Skirt"=>30,
"Jump Suit"=>31,
"Cap"=>32,
"Top Btm Set"=>33,
"Top Btm Set"=>34,
"LONG PANT"=>35,
"Bag"=>36,
"Bag"=>37,
"PANTY"=>38,
"JACKET"=>39,
"VEST"=>40,
"TANK TOP"=>41,
"SHORT PANT"=>42,
"SHORT PANT"=>43,
"SHORT PANT"=>44,
"SHORT PANT"=>45,
"PANTY"=>46,
"SHORT PANT"=>47,
"SHORT PANT"=>48,
"SHORT PANT"=>49,
"SHORT PANT"=>50,
"PANTY"=>51,
"PANTY"=>52,
"PANTY"=>53,
"PANTY"=>54,
"Socks"=>60,
"Socks"=>61,
"Socks"=>62,
"Socks"=>63,
"Socks"=>64,
"Socks"=>65,
"Socks"=>66,
"LONG PANT"=>67,
"Sports Ware"=>68,
"SS TEE"=>69,
"LONG PANT"=>70,
"SHORT PANT"=>71,
"Bra"=>72,
"SS TEE"=>73,
"DRESS"=>74
);

/*$line_array=array( 67 => 1,
 	7 => 10,
    15 => 11,
    16 => 12,
    59 => 13,
    18 => 14,
    19 => 15,
    20 => 16,
    21 => 17,
    22 => 18,
    23 => 19,
    61 => 2,
    68 => 20,
    8 => 21,
    9 => 22,
    17 => 23,
    11 => 24,
    10 => 25,
    12 => 26,
    13 => 27,
    14 => 28,
    45 => 29,
    62 => 3,
    46 => 30,
    47 => 31,
    48 => 32,
    49 => 33,
    50 => 34,
    51 => 35,
    63 => 36,
    37 => 37,
    36 => 38,
    34 => 39,
    1 => 4,
    35 => 40, 
    33 => 41,
    57 => 42,
    32 => 43,
    31 => 44,
    56 => 45,
    52 => 46,
    54 => 47,
    55 => 48,
    70 => 49,
    2 => 5,
    24 => 50,
    25 => 51,
    60 => 52,
    26 => 53,
    27 => 54,
    28 => 55,
    29 => 56,
    30 => 57,
    38 => 58,
    58 =>0,
    39 => 59,
    3 => 6,
    40 => 60
);*/
$line_array=array( 67 => 1,
 	7 => 10,
    15 => 11,
    16 => 12,
    59 => 13,
    18 => 14,
    19 => 15,
    20 => 16,
    21 => 17,
    22 => 18,
    23 => 19,
    61 => 2,
    68 => 20,
    8 => 21,
    9 => 22,
    17 => 23,
    11 => 24,
    10 => 25,
    12 => 26,
    13 => 27,
    14 => 28,
    45 => 29,
    62 => 3,
    46 => 30,
    47 => 31,
    48 => 32,
    49 => 33,
    50 => 34,
    51 => 35,
    63 => 36,
    37 => 37,
    36 => 38,
    34 => 39,
    1 => 4,
    35 => 40, 
    33 => 41,
    57 => 42,
    32 => 43,
    31 => 44,
    56 => 45,
    52 => 46,
    54 => 47,
    55 => 48,
    70 => 49,
    2 => 5,
    24 => 50,
    25 => 51,
    60 => 52,
    26 => 53,
    27 => 54,
    28 => 55,
    29 => 56,
    30 => 57,
    38 => 58,
    58 =>0,
    39 => 59,
    3 => 6,
    40 => 60
);
$fr_line_array=array( "Line 1/U-1(New)" => 1,
	"Line 2/U-1(New)"=> 2,
	"Line 3/U-1(New)" => 3,
	"Line 4/U-1(New)" => 4,
	"Line 5/U-1(New)" => 5,
	"Line 6/U-1(New)" => 6,
	"Line 7/U-1(New)" => 7,
	"Line 8/U-1(New)" => 8,
	"Line 9/U-1(New)" => 9,
	"Line 10/U-1(New)" => 10,
	"Line 11/U-2(New)" => 11,
	"Line 12/U-2(New)" => 12,
	"Line 13/U-2(New)" => 13,
	"Line 14/U-2(New)" => 14,
	"Line 15/U-2(New)" => 15,
	"Line 16/U-2(New)" => 16,
	"Line 17/U-2(New)" => 17,
	"Line 18/U-2(New)" => 18,
	"Line 19/U-2(New)" => 19,
	"Line 21/U-3(New)" => 21,
	
	"Line 23/U-3(New)" => 23,
	"Line 24/U-3(New)" => 24,
	"Line 25/U-3(New)" => 25,
	"Line 26/U-3(New)" => 26,
	"Line 27/U-3(New)" => 27,
	"Line 28/U-3(New)" => 28,
	"Line 29/U-4(New)" => 29,
	"Line 30/U-4(New)" => 30,
	"Line 31/U-4(New)" => 31,
	"Line 32/U-4(New)" => 32,
	"Line 33/U-4(New)" => 33,
	"Line 34/U-4(New)" => 34,
	"Line 35/U-4(New)" => 35,
	"Line 36/U-4(New)" => 36,
	"Line 37/U-5(New)" => 37,
	"Line 39/U-5(New)" => 39,
	"Line 40/U-5(New)" => 40,
	"Line 41/U-5(New)" => 41,
	"Line 42/U-5(New)" => 42,
	"Line 43/U-5(New)" => 43,
	"Line 44/U-5(New)" => 44,
	"Line 45/U-6(New)" => 45,
	"Line 46/U-6(New)" => 46,
	"" => 47,
	"Line 48/U-6(New)" => 48,
	"" => 49,
	"" => 50,
	"Line 51/U-7(New)" => 51,
	"Line 52/U-7(New)" => 52,
	"Line 53/U-7(New)" => 53,
	"Line 54/U-7(New)" => 54,
	"Line 55/U-7(New)" => 55,
	"Line 56/U-7(New)" => 56,
	"" => 57,
	"" => 58,
	"" => 59,
	"" => 60,
	"Line 61/U-8(New)" => 61,
	"Line 62/U-8(New)" => 62,
	"" => 63,
	"" => 64,
	"Line 65/U-8(New)" => 65,
	"Line 66/U-9" => 66,
	"Line 67/U-9" => 67,
	"Line 68/U-9" => 68,
	"Line 69/U-9" => 69,
	"Line 70/U-9" => 70,
	"" => 71,
	"" => 72,
	"" => 73,
	"" => 74,
);
		
$color_library=return_library_array( "select id, color_name from lib_color", "color_name", "id"  );
$po_library=return_library_array( "select id, po_number from wo_po_break_down", "po_number", "id" );
$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name"  );
//$line_name = return_library_array("select id,line_name from lib_sewing_line","line_name","id");

if( $action=="action_save" )
{
	//echo "su..re";
	foreach(glob("files/"."*.csv")	as $filename){ @unlink($filename); }
	//die;
	$source 	= $_FILES['uploadfile']['tmp_name'];
	$targetzip 	= 'files/'.$_FILES['uploadfile']['name'];
	$file_name	= $_FILES['uploadfile']['name'];
	
	//$targetzip 	= 'files/fr_loading _01.csv';
	
	if(move_uploaded_file($source, $targetzip)) 
	{
		$file = fopen($targetzip,"r");
		$data_arr=array();
		$i=0;
		while( !feof($file) )
		{
			if($i==0)
			{
				$data_arrrr[]=fgetcsv($file);
			}
			else
			{
				$data_arr[]=fgetcsv($file);
			}
			$i++;
		}
		fclose($file);

		$line_arr=array();
		$line_no_arr=array();
		foreach($data_arr as $row)
		{
			$rows=str_replace("(","[",$row[0]);
			$line_no=$line_array[$fr_line_array[$row[0]]];
			
			if($line_no!='') $line_no_arr[$line_no]=$line_no;
			$tmp=explode("/",$row[12]);
			$tmp2=explode("/",$row[13]);
			
			$tmp3=explode("::",$row[2]);
			if(trim($tmp3[0])!='') $job_no[$tmp3[0]]=$tmp3[0];
			
			//$line_arr1[$line_no]['from_date']		= change_date_format(str_replace("/","-",$row[12]),'yyyy-mm-dd');
			//$line_arr1[$line_no]['to_date']			= change_date_format(str_replace("/","-",$row[13]),'yyyy-mm-dd');
			//	$line_arr1[$line_no]['total_smv']		= $row[11];
			//$line_arr1[$line_no]['active_machine']	= $row[17];
			
			//$line_arr2[$line_no][$po_library[$row[6]]][$fr_product_type_rvrs[$row[4]]][$color_library[$row[7]]]['po_number']	= $po_library[$row[6]];
			//$line_arr2[$line_no][$po_library[$row[6]]][$fr_product_type_rvrs[$row[4]]][$color_library[$row[7]]]['item_number']	= $fr_product_type_rvrs[$row[4]];
			//$line_arr2[$line_no][$po_library[$row[6]]][$fr_product_type_rvrs[$row[4]]][$color_library[$row[7]]]['color_number']	= $color_library[$row[7]];
			//$color_size_arr[$row['PO_NUMBER']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']]
			//echo $line_name[$line_no]."=".$row[7]."=".$row[4]."=".$row[6]; die;
			
			$new_arr[$i]['from_date']=date("Y-m-d",strtotime(str_pad($tmp[2],4,"20",STR_PAD_LEFT)."-".$tmp[1]."-".$tmp[0]  ));
			$new_arr[$i]['to_date']= date("Y-m-d",strtotime(str_pad($tmp2[2],4,"20",STR_PAD_LEFT)."-".$tmp2[1]."-".$tmp2[0]  ));
			
			if( $line_date[$line_no]['start']>$new_arr[$i]['from_date'] || $line_date[$line_no]['start']=='')
				$line_date[$line_no]['start']=$new_arr[$i]['from_date'];
				
			if($line_date[$line_no]['to_date']<$new_arr[$i]['to_date'])
				$line_date[$line_no]['to_date']=$new_arr[$i]['to_date'];	
			//$line_date[$line_no]['start']=$new_arr[$i]['to_date'];
			
			if($line_no!='') $line_arr[$line_no].=$new_arr[$i]['from_date'].'##'.$new_arr[$i]['to_date'].'##'.$po_library[$row[6]].'##'.$fr_product_type_rvrs[$row[4]].'##'.$color_library[$row[7]].'##'.$row[17].'##'.$row[11].'___';
			
			/*$new_arr[$i]['total_smv']=$row[11];
			$new_arr[$i]['active_machine']=$row[17];
			$new_arr[$i]['line_no']=$line_no;
			$new_arr[$i]['po_number']=$po_library[$row[6]];
			$new_arr[$i]['gmts_item']=$fr_product_type_rvrs[$row[4]];
			$new_arr[$i]['color']=$color_library[$row[7]];*/
			
			$i++;
		}
		//echo "0**";//.implode(',',$line_no_arr)
		
		//311=10-100 SOLID=SS TEE=390850-1643
		//echo "<pre>";
		// print_r($line_date); die;
		//echo $line_arr2['311']['390850-1643']['SS TEE']['10-100 SOLID']['po_number']."=="; die;
		//echo "SELECT a.id AS ID, a.resource_num AS RESOURCE_NUM, a.line_number AS LINE_NUMBER, b.pr_date AS PR_DATE FROM prod_resource_mst a, prod_resource_dtls b WHERE a.id=b.mst_id and a.company_id=1 and a.location_id=1 and a.is_deleted=0 and b.is_deleted=0 and a.line_number in (".implode(',',$line_no_arr).") "; die; 
		$sql_resource=sql_select("SELECT a.id AS ID, a.resource_num AS RESOURCE_NUM, a.line_number AS LINE_NUMBER, b.pr_date AS PR_DATE FROM prod_resource_mst a, prod_resource_dtls b WHERE a.id=b.mst_id and a.company_id=1 and a.location_id=1 and a.is_deleted=0 and b.is_deleted=0 and a.line_number in (".implode(',',$line_no_arr).") ");
		
		$prod_reso_no_arr=array();
		$prod_date_arr=array();
		foreach($sql_resource as $row)
		{
			$pr_date=''; 
			$date_frm=''; 
			$date_to=''; 
			$pr_date=date('Y-m-d',strtotime($row['PR_DATE']));
			
			//$prod_reso_no_arr[$row['LINE_NUMBER']]['line_number']	= $row['ID'];
			$prod_reso_no_arr[$row['LINE_NUMBER']]['resource_num']	= $row['RESOURCE_NUM'];
			//echo '<pre>';
			//echo $line_date[$row['LINE_NUMBER']]['start'].'=='.$line_date[$row['LINE_NUMBER']]['to_date'].'=='.$pr_date.'=='.$row['LINE_NUMBER'].'<br>';
			if( $pr_date>$line_date[$row['LINE_NUMBER']]['start'] && $pr_date<$line_date[$row['LINE_NUMBER']]['to_date'] )
			{
				$prod_date_arr[$row['LINE_NUMBER']]['resource_num'] = $row['RESOURCE_NUM'];
			}
		}
		/*var_dump($prod_reso_no_arr);
		die;*/
		
		unset($sql_resource);
		//echo "'".implode("','",$job_no)."'"; die;
		/*$sql_color=sql_select("select b.id AS ID, b.po_number AS PO_NUMBER, c.item_number_id AS ITEM_NUMBER_ID, c.color_number_id AS COLOR_NUMBER_ID from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and a.company_name=1 and a.job_no in ("."'".implode("','",$job_no)."'".")");
		$color_size_arr=array();
		foreach($sql_resource as $row)
		{
			//echo $row[csf('id')].'_'.$row[csf('item_number_id')].'_'.$row[csf('color_number_id')].'_'.$row[csf('size_number_id')];
			$color_size_arr[$row['PO_NUMBER']][$row['ITEM_NUMBER_ID']][$row['COLOR_NUMBER_ID']]=$row['ID']."_".$row['ITEM_NUMBER_ID']."_".$row['COLOR_NUMBER_ID']."_0";
		}
		unset($sql_color);*/
		
		if($db_type==0)
		{
			$timeArray=sql_select("select shift_id AS SHIFT_ID, TIME_FORMAT( prod_start_time, '%H:%i' ) AS PROD_START_TIME, TIME_FORMAT( lunch_start_time, '%H:%i' ) AS LUNCH_START_TIME from variable_settings_production where company_name=1 and variable_list=26 and status_active=1 and is_deleted=0");
			
			$new_mrrnumber=explode("*",return_mrr_number( 1, '', 'APR', date("Y",time()), 5, "select prefix, prod_resource_num from prod_resource_mst where company_id=1 and YEAR(insert_date)=".date('Y',time())." order by id DESC", "prefix", "prod_resource_num" ));// 
		}
		else if($db_type==2)
		{
			$timeArray=sql_select("select shift_id AS SHIFT_ID, TO_CHAR(prod_start_time,'HH24:MI') AS PROD_START_TIME, TO_CHAR(lunch_start_time,'HH24:MI') AS LUNCH_START_TIME from variable_settings_production where company_name=1 and variable_list=26 and status_active=1 and is_deleted=0");
			
			$new_mrrnumber=explode("*",return_mrr_number( 1, '', 'APR',date('Y',time()), 5, "select prefix, prod_resource_num from prod_resource_mst where company_id=1 and  TO_CHAR(insert_date,'YYYY')=".date('Y',time())." order by id DESC ", "prefix", "prod_resource_num" ));	
		}
		//echo $new_sys_number[2].'=='.$new_sys_number[1].'=='.$new_sys_number[0]; die;
		$save_string="";
		foreach($timeArray as $row)
		{
			if( $save_string !="") $save_string .= ","; 
			$save_string .= $row['SHIFT_ID']."_".$row['PROD_START_TIME']."_".$row['LUNCH_START_TIME']."_ ";//$shift_id."','".$prod_start_time."','".$lunch_start_time."','".$remarks.
		}
		unset($timeArray);
		$id_arr=array();
		$sqlmstId=sql_select("select id, resource_num  from prod_resource_mst");
		foreach($sqlmstId as $row)
		{
			$id_arr[$row[csf('resource_num')]]=$row[csf('id')];
		}
		unset($sqlmstId);
		$idMst=return_next_id("id", "prod_resource_mst", 1);
		$id2=return_next_id("id", "prod_resource_dtls", 1);
		$id3=return_next_id("id", "prod_resource_dtls_mast", 1);
		$id_time= return_next_id( "id", "prod_resource_dtls_time", 1 );
		$id_colSiz= return_next_id( "id", "prod_resource_color_size", 1 );

		$z=1;
		$y=1;
		$sys_number_arr=array();
		$save_resource_name=""; $data_array3=""; $data_array1=""; $data_array=""; $data_array_time="";
		
		$field_array1="id, prefix, prod_resource_num, resource_num, company_id, location_id, floor_id, line_marge, line_number, inserted_by, insert_date, is_deleted";
		
		$field_array3="id, mst_id, from_date, to_date, man_power, operator, helper, line_chief, active_machine, target_per_hour, working_hour, po_id, capacity, smv_adjust, smv_adjust_type, total_smv, inserted_by, insert_date, is_deleted";
		
		$field_array="id, mast_dtl_id, mst_id, pr_date, man_power, operator, helper, line_chief, active_machine, target_per_hour, working_hour, po_id, smv_adjust, smv_adjust_type, total_smv, is_csv, inserted_by, insert_date, is_deleted"; 
		
		$field_array_time="id, mst_id, mast_dtl_id, shift_id, prod_start_time, lunch_start_time, remarks";
		
		$field_colSiz="id, mst_id, dtls_id, po_id, gmts_item_id, color_id, size_id, inserted_by, insert_date";
		
		foreach($line_arr as $line_id=>$line_data)
		{
			$resource_mst=$prod_reso_no_arr[$line_id]['resource_num'];
			if($resource_mst=='')
			{
				if($z==1)
				{
					$sys_number_arr[$line_id]=$new_mrrnumber;
					$z++;
				}
				else
				{
					$new_job[0]=$new_mrrnumber[1].(str_pad($new_mrrnumber[2]+$y,5,0,STR_PAD_LEFT));
					$new_job[1]=$new_mrrnumber[1];
					$new_job[2]=$new_mrrnumber[2]+$y;
					$sys_number_arr[$line_id]=$new_job;
					$y++;
				}
				if($data_array1!="") $data_array1.=",";
				$data_array1.="(".$idMst.",'".$sys_number_arr[$line_id][1]."',".$sys_number_arr[$line_id][2].",'".$sys_number_arr[$line_id][0]."',1,1,0,2,'".$line_id."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',0)";
				$id=$idMst;
				$idMst++;
			}
			else
			{
				$new_sys_number[0]=$prod_reso_no_arr[$line_id]['resource_num']; 
				$id_pre=$id_arr[$resource_mst];
				$id=$id_pre;
			}
			
			if($save_resource_name !="") $save_resource_name .=", ";
			$save_resource_name .=$new_sys_number[0];
			
			$ex_line_data=array_filter(array_unique(explode('___',$line_data)));
			foreach($ex_line_data as $exData)
			{
				$ex_other='';
				$ex_other=explode('##',$exData);
				$from_date=''; $to_date=''; $po_id=''; $item_id=''; $color_id=''; $active_machine=''; $total_smv='';
				$from_date=$ex_other[0]; 
				$to_date=$ex_other[1]; 
				$po_id=$ex_other[2]; 
				$item_id=$ex_other[3]; 
				$color_id=$ex_other[4]; 
				$active_machine=$ex_other[5]; 
				$total_smv=$ex_other[6];
				
				if($prod_date_arr[$line_id]['resource_num']=="")
				{
					if($data_array3!="") $data_array3.=",";
					$data_array3.="(".$id3.", ".$id.", '".$from_date."', '".$to_date."', 0, 0, 0, 0, ".$active_machine.", 0, 0,".$po_id.", 0, 0, 0, ".$total_smv.", ".$_SESSION['logic_erp']['user_id'].", '".$pc_date_time."', 0)";
					
					$date_diff=0;
					//$date_diff=datediff('d', $line_arr1[$row]['from_date'], $line_arr1[$row]['to_date']);
					$date_diff=datediff("d",$from_date,$to_date);
					//echo $from_date.'=='.$date_diff.'=='.$to_date.'<br>'; 
					for($i=0; $i<$date_diff; $i++)
					{
						$date = add_date($from_date,$i); 
						if($db_type==0) $dd=change_date_format($date,'yyyy-mm-dd');
						
						if($data_array!="") $data_array.=",";
						$data_array.="(".$id2.",".$id3.",".$id.",'".$dd."',0,0,0,0,".$active_machine.",0,0,0,0,0,".$total_smv.",1,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',0)";
						
						$save_string=explode(",",str_replace("'","",$save_string));
						foreach($save_string as $prevData)
						{
							$time_dtls=explode("_",$prevData);
							$shift_id=$time_dtls[0];
							$prod_start_time=$time_dtls[1];
							$lunch_start_time=$time_dtls[2];
							$remarks=$time_dtls[3];
							
							if($data_array_time!="") $data_array_time.=",";
								$data_array_time.="(".$id_time.",".$id.",".$id3.",'".$shift_id."','".$prod_start_time."','".$lunch_start_time."','".$remarks."')";
							$id_time = $id_time+1;
						}
						$id2++;
					}
					
					if($color_id!="")
					{ 
						if($data_colSiz!="") $data_colSiz.=",";
						$data_colSiz.="(".$id_colSiz.",".$id.",".$id3.",'".$po_id."','".$item_id."','".$color_id."','0',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
						$id_colSiz = $id_colSiz+1;
					}
					$id3++;
				}
			}
		}
		/*echo "insert into prod_resource_mst (".$field_array1.") values ".$data_array1;
		echo "<br>";
		
		echo "insert into prod_resource_dtls_mast (".$field_array3.") values ".$data_array3;
		echo "<br>";
		
		echo "insert into prod_resource_dtls (".$field_array.") values ".$data_array;
		echo "<br>";
		
		//echo "INSERT ALL".$data_array_time." SELECT * FROM dual";
		echo "insert into prod_resource_dtls_time (".$field_array_time.") values ".$data_array_time;
		echo "<br>";
		//die;
		echo "insert into prod_resource_color_size (".$field_colSiz.") values ".$data_colSiz;
		echo "<br>";
		
		die;*/
		$rID=1;	
		$rID2=1; 
		$rID3=1; 
		$rID_time=true; 
		$rID_colSiz=true;
	
		if($data_array1!="") 
		{ 
			 $rID=sql_insert("prod_resource_mst",$field_array1,$data_array1,0);
		}
	
		/*if(str_replace("'",'',$h_dtl_mst_id)=="")
		{*/	
		$rID2=sql_insert("prod_resource_dtls_mast",$field_array3,$data_array3,0);
		$rID3=sql_insert("prod_resource_dtls",$field_array,$data_array,0);
		//}
	   
		if($data_array_time!="")
	  	{	
		  	/*if($db_type==2)
			{
				$query="INSERT ALL".$data_array_time." SELECT * FROM dual";
				$rID_time=execute_query($query);
			}
			else
			{*/
				$rID_time=sql_insert("prod_resource_dtls_time",$field_array_time,$data_array_time,1);
			//} 
	   	}
		
		if($data_colSiz!="")
		{
			$rID_colSiz=sql_insert("prod_resource_color_size",$field_colSiz,$data_colSiz,1);
		}
		$mesg="All Line Busy If you need Attached This File Please Update Manually.";
	    //echo $rID."**".$rID2."**".$rID3."**".$rID_time;die;
		if($db_type==0 )
		{	
			if($rID && $rID2 && $rID3 && $rID_time && $rID_colSiz)
			{
				mysql_query("COMMIT");  
				echo "0**".str_replace("'","",$id)."**".$new_sys_number[0];
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "5**".$mesg;
			}
		}
		if($db_type==2 || $db_type==1 )
		{	if($rID && $rID2 && $rID3 && $rID_time  && $rID_colSiz)
			{
				oci_commit($con);
				echo "0**".str_replace("'","",$id)."**".$new_sys_number[0];
			}
			else
			{
				oci_rollback($con);
				echo "5**".$mesg;
			}
		}
		disconnect($con);
		echo "0**Save resource number is : ".$save_resource_name;
	}
	die;
}
?>