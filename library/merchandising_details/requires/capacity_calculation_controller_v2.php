<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$user_id=$_SESSION['logic_erp']['user_id'];

if ($action=="load_drop_down_location")
{
	 echo create_drop_down( "cbo_location_id", 150, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "--Select Location--", $selected, "","","","","","",3 );		 
}

if ($action=="load_drop_down_product_type")
{
	if($data==1)
	{
		echo create_drop_down( "cbo_product_type", 150, $product_types,'', 1,"--- Select Product Type ---",0,"",0,"3,4");
	}
	else if($data==2)
	{
		echo create_drop_down( "cbo_product_type", 150, $product_types,'', 1,"--- Select Product Type ---",0,"",0,"1,2,5");
	}
	else if($data==6 || $data==9)
	{
		echo create_drop_down( "cbo_product_type", 150, $product_types,'', 1,"--- Select Product Type ---",0,"",0,"6");
	}
	else if($data==7)
	{
		echo create_drop_down( "cbo_product_type", 150, $product_types,'', 1,"--- Select Product Type ---",0,"",0,"7");
	}
	else if($data==8)
	{
		echo create_drop_down( "cbo_product_type", 150, $product_types,'', 1,"--- Select Product Type ---",0,"",0,"8");
	}
	else
	{
		echo create_drop_down( "cbo_product_type", 150, $blank_array,'', 1,"--- Select Product Type ---",0);
	}
	exit();
}

if ($action=="load_php_dtls_form")
{
	$data=explode('_',$data);
	//echo $data;
	$cbo_item_cat = $data[4];
	
	$daysinmonth=cal_days_in_month(CAL_GREGORIAN, $data[1], $data[0]);
	$c_date="$data[0]-$data[1]-01";
	$k=0; $kk=1; 
	for( $i = 1; $i <= $daysinmonth; $i++ ) 
	{  
	$day_txt=$i<10?'0'.$i:$i;
		$month_txt=$data[1]<10?'0'.$data[1]:$data[1];
		$date=$day_txt.'-'.$month_txt.'-'.$data[0];
		$bgcolor=( $i%2==0 ? "#E9F3FF" : "#FFFFFF" );
		$day_name = date('D', strtotime($date));
		
		if($day_name=='Fri' && $mdr[$i]['day_status']==""){$style='style="color: red;"';$status_select=2;} 
		else if($mdr[$i]['day_status']==2){$style='style="color: red;"';$status_select=2;}
		else{$style='';$status_select=1;}
	?>
		<tr align="center" <?php echo $style; ?>>
			<td>
            	<input type="hidden" id="update_id_dtls_<? echo $i; ?>" name="update_id_dtls_<? echo $i; ?>" value="" />
				<input type="text" name="txt_date_<? echo $i; ?>" id="txt_date_<? echo $i; ?>" class="datepicker" style="width:67px" value="<? echo  change_date_format(add_date($c_date, $k));  ?>" readonly />
			</td>
             <td width="40" id="tdDay_<? echo $i; ?>"><?php echo $day_name; ?></td>
			<td>
				<?
				$day_select=1;
				if ($day_name=='Fri') {
					$day_select=2;
					
				}

					$day_status=array(1=>"Open",2=>"Closed");
					echo create_drop_down( "cbo_day_status_$i", 72,$day_status,"", 0, "-- Select --", $day_select,"open_close(this.value,$i,$kk)" );
				?>
			</td>
			<td>
				<input type="text" name="txt_no_of_line_<? echo $i; ?>" id="txt_no_of_line_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px" onblur="calculate_capacity_min_pcs(this.value,<? echo $i; ?>);" />
			</td>
			<td>
				<input type="text" name="txt_capacity_min_<? echo $i; ?>" id="txt_capacity_min_<? echo $i; ?>" class="text_boxes_numeric" style="width:100px" readonly="readonly" />
			</td>
			<td>
				<input type="text" name="txt_capacity_pcs_<? echo $i; ?>" id="txt_capacity_pcs_<? echo $i; ?>" class="text_boxes_numeric" style="width:100px" readonly="readonly" />
			</td>
		</tr>
	 <? 
	 $k++;	
	 $kk++; 
	 } 	
}


if($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));  
	
	if ($operation==0)  // Insert Here==================================================
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		if(str_replace("'",'',$update_id)=="")
		{
			$mst_id=return_next_id( "id", " lib_capacity_calc_mst", 1 ) ; 
			$field_array_mst="id,comapny_id,capacity_source,year,location_id,avg_machine_line,basic_smv,effi_percent,prod_category_id,prod_type_id,inserted_by,insert_date,status_active,is_deleted";
			
			$data_array_mst="(".$mst_id.",".$cbo_company_id.",".$cbo_capacity_source.",".$cbo_year.",".$cbo_location_id.",".$txt_avg_mch_line.",".$txt_basic_smv.",".$txt_efficiency_per.",".$cbo_product_category.",".$cbo_product_type.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
		//echo "insert into lib_capacity_calc_mst (".$field_array_mst.") values ".$data_array_mst;die;
			//$rID=sql_insert("lib_capacity_calc_mst",$field_array_mst,$data_array_mst,0);
		}
		else
		{
			$mst_id=str_replace("'",'',$update_id);
			$field_array_mst="capacity_source*year*location_id*avg_machine_line*basic_smv*effi_percent*prod_category_id*prod_type_id*updated_by*update_date*status_active*is_deleted";
			
			$data_array_mst="".$cbo_capacity_source."*".$cbo_year."*".$cbo_location_id."*".$txt_avg_mch_line."*".$txt_basic_smv."*".$txt_efficiency_per."*".$cbo_product_category."*".$cbo_product_type."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";
			$rID=sql_update("lib_capacity_calc_mst",$field_array_mst,$data_array_mst,"id",$update_id,0);
		}
		
		$dtls_id=return_next_id( "id", "lib_capacity_calc_dtls", 1 ); 
		$field_array_dtls="id,mst_id,month_id,date_calc,day_status,no_of_line,capacity_min,capacity_pcs";
			for($i=1; $i<=$tot_row_date; $i++)
			{
				$txt_date= "txt_date_".$i;
				$cbo_day_status="cbo_day_status_".$i;
				$txt_no_of_line="txt_no_of_line_".$i;
				$txt_capacity_min="txt_capacity_min_".$i;
				$txt_capacity_pcs="txt_capacity_pcs_".$i;
				$update_id_dtls="update_id_dtls_".$i;
				
				$txt_date=date("d-M-Y",strtotime(str_replace("'",'',$$txt_date)));

				if(str_replace("'",'',$$update_id_dtls)=="")
				{
					if ($i!=1) $data_array_dtls .=",";
					$data_array_dtls.="(".$dtls_id.",".$mst_id.",".$cbo_month.",'".$txt_date."',".$$cbo_day_status.",".$$txt_no_of_line.",".$$txt_capacity_min.",".$$txt_capacity_pcs.")";
					$dtls_id=$dtls_id+1;
				}
			}
			//echo "0**"."insert into lib_capacity_calc_dtls (".$field_array_dtls.") values ".$data_array_dtls; die;	 
			//$rID1=sql_insert("lib_capacity_calc_dtls",$field_array_dtls,$data_array_dtls,0);
			
		$dtls_year_id=return_next_id( "id", "lib_capacity_year_dtls", 1 ); 
		$field_array_year="id,mst_id,month_id,working_day,capacity_month_min,capacity_month_pcs";
		
			for($i=1; $i<=$tot_row_year; $i++)
			{
				$txt_month= "txt_month_id_".$i;
				$txt_working_day="txt_working_day_".$i;
				$txt_year_capacity_min="txt_year_capacity_min_".$i;
				$txt_year_capacity_pcs="txt_year_capacity_pcs_".$i;
				$update_id_year_dtls="update_id_year_dtls_".$i;

				if(str_replace("'",'',$$update_id_year_dtls)=="")
				{
					if ($i!=1) $data_array_year .=",";
					$data_array_year.="(".$dtls_year_id.",".$mst_id.",".$$txt_month.",".$$txt_working_day.",".$$txt_year_capacity_min.",".$$txt_year_capacity_pcs.")";
					$dtls_year_id=$dtls_year_id+1;
				}
				else
				{
					$id_arr_year=array();
					$data_array_year=array();
					$field_array_year="working_day*capacity_month_min*capacity_month_pcs";
					for($i=1; $i<=12; $i++)
					{
						$txt_month= "txt_month_id_".$i;
						$txt_working_day="txt_working_day_".$i;
						$txt_year_capacity_min="txt_year_capacity_min_".$i;
						$txt_year_capacity_pcs="txt_year_capacity_pcs_".$i;
						$update_id_year_dtls="update_id_year_dtls_".$i;
						
						if(str_replace("'",'',$$update_id_year_dtls)!="")
						{
							$id_arr_year[]=str_replace("'",'',$$update_id_year_dtls);
							$data_array_year[str_replace("'",'',$$update_id_year_dtls)] =explode(",",("".$$txt_working_day.",".$$txt_year_capacity_min.",".$$txt_year_capacity_pcs.""));
						}
					}
					//echo bulk_update_sql_statement( 'lib_capacity_year_dtls', 'id', $field_array_year, $data_array_year, $id_arr_year ); die;	 
					//$rID2=execute_query(bulk_update_sql_statement( "lib_capacity_year_dtls", "id", $field_array_year, $data_array_year, $id_arr_year ),1);
				}
			}
			//echo "0**"."insert into lib_capacity_year_dtls (".$field_array_year.") values ".$data_array_year; die;
		if(str_replace("'",'',$update_id)=="")
		{
			$rID=sql_insert("lib_capacity_calc_mst",$field_array_mst,$data_array_mst,0);	
		}
		else
		{
			$rID=sql_update("lib_capacity_calc_mst",$field_array_mst,$data_array_mst,"id",$update_id,0);
		}
		$rID1=sql_insert("lib_capacity_calc_dtls",$field_array_dtls,$data_array_dtls,0);
		if(count($id_arr_year)==0)
		{
			$rID2=sql_insert("lib_capacity_year_dtls",$field_array_year,$data_array_year,1);
		}
		else
		{
			$rID2=execute_query(bulk_update_sql_statement( "lib_capacity_year_dtls", "id", $field_array_year, $data_array_year, $id_arr_year ),1);
		}
	 	//echo "10**=".$rID.'='.$rID1.'='.$rID2.'=';die;
		if($db_type==0)
		{
			if( $rID && $rID1 && $rID2 )
			{
				mysql_query("COMMIT");  
				echo "0**".str_replace("'",'',$mst_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$mst_id);
			}
		}
		if($db_type==2 || $db_type==1 )
		{
		if( $rID && $rID1 && $rID2 )
			{
				oci_commit($con); 
				echo "0**".str_replace("'",'',$mst_id);
			}
		else
			{
				oci_rollback($con); 
				echo "10**".str_replace("'",'',$mst_id);
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here=============================================================================
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$field_array_mst="capacity_source*year*location_id*avg_machine_line*basic_smv*effi_percent*prod_category_id*prod_type_id*updated_by*update_date*status_active*is_deleted";
		
		$data_array_mst="".$cbo_capacity_source."*".$cbo_year."*".$cbo_location_id."*".$txt_avg_mch_line."*".$txt_basic_smv."*".$txt_efficiency_per."*".$cbo_product_category."*".$cbo_product_type."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";
		
		//$rID=sql_update("lib_capacity_calc_mst",$field_array_mst,$data_array_mst,"id",$update_id,0);
		$id_arr=array();
		$data_array_dtls_up=array();
		$field_array_dtls_up="day_status*no_of_line*capacity_min*capacity_pcs";
		
		$dtls_id=return_next_id( "id", "lib_capacity_calc_dtls", 1 ); 
		$field_array_dtls="id,mst_id,month_id,date_calc,day_status,no_of_line,capacity_min,capacity_pcs";
		//$testID = "";
		for($i=1; $i<=$tot_row_date; $i++)
		{
			$txt_date= "txt_date_".$i;
			$cbo_day_status="cbo_day_status_".$i;
			$txt_no_of_line="txt_no_of_line_".$i;
			$txt_capacity_min="txt_capacity_min_".$i;
			$txt_capacity_pcs="txt_capacity_pcs_".$i;
			$update_id_dtls="update_id_dtls_".$i;
			//print_r ($$update_id_dtls);die;
			//$testID.=str_replace("'",'',$$update_id_dtls)."_";
			if(str_replace("'",'',$$update_id_dtls)!="")
			{
				$id_arr[]=str_replace("'",'',$$update_id_dtls);
				$data_array_dtls_up[str_replace("'",'',$$update_id_dtls)] =explode(",",("".$$cbo_day_status.",".$$txt_no_of_line.",".$$txt_capacity_min.",".$$txt_capacity_pcs.""));
			}
			else 
			{
				$txt_date=date("d-M-Y",strtotime(str_replace("'",'',$$txt_date)));
				if ($i!=1) $data_array_dtls .=",";
				$data_array_dtls.="(".$dtls_id.",".$update_id.",".$cbo_month.",'".$txt_date."',".$$cbo_day_status.",".$$txt_no_of_line.",".$$txt_capacity_min.",".$$txt_capacity_pcs.")";
				$dtls_id=$dtls_id+1;
			}
		}
		//echo "10**".$testID; die;
		
		
	/*	$rID1=execute_query(bulk_update_sql_statement( "lib_capacity_calc_dtls", "id", $field_array_dtls_up, $data_array_dtls_up, $id_arr ),0);
		
		if(str_replace("'",'',$data_array_dtls)!="")
		{
			$rID1=sql_insert("lib_capacity_calc_dtls",$field_array_dtls,$data_array_dtls,0);
		}*/
		$id_arr_year=array();
		$data_array_year=array();
		$field_array_year="working_day*capacity_month_min*capacity_month_pcs";
		for($i=1; $i<=12; $i++)
		{
			$txt_month= "txt_month_id_".$i;
			$txt_working_day="txt_working_day_".$i;
			$txt_year_capacity_min="txt_year_capacity_min_".$i;
			$txt_year_capacity_pcs="txt_year_capacity_pcs_".$i;
			$update_id_year_dtls="update_id_year_dtls_".$i;
			
			if(str_replace("'",'',$$update_id_year_dtls)!="")
			{
				$id_arr_year[]=str_replace("'",'',$$update_id_year_dtls);
				$data_array_year[str_replace("'",'',$$update_id_year_dtls)] =explode(",",("".$$txt_working_day.",".$$txt_year_capacity_min.",".$$txt_year_capacity_pcs.""));
			}
		}
	//	 echo bulk_update_sql_statement( 'lib_capacity_year_dtls', 'id', $field_array_year, $data_array_year, $id_arr_year ); die;	 
		$rID=sql_update("lib_capacity_calc_mst",$field_array_mst,$data_array_mst,"id",$update_id,0);
		$rID1=execute_query(bulk_update_sql_statement( "lib_capacity_calc_dtls", "id", $field_array_dtls_up, $data_array_dtls_up, $id_arr ),0);
		
		if(str_replace("'",'',$data_array_dtls)!="")
		{
			$rID1=sql_insert("lib_capacity_calc_dtls",$field_array_dtls,$data_array_dtls,0);
		}
		// echo "10**=".bulk_update_sql_statement( "lib_capacity_year_dtls", "id", $field_array_year, $data_array_year, $id_arr_year );die;
		$rID2=execute_query(bulk_update_sql_statement( "lib_capacity_year_dtls", "id", $field_array_year, $data_array_year, $id_arr_year ),1);
			 //	echo "10**".$rID .'&&'.$rID1 .'&&'. $rID2.'='.str_replace("'",'',$update_id);die;
		if($db_type==0)
		{
			if( $rID && $rID1 && $rID2 )
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'",'',$update_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$update_id);
			}
		}
		if($db_type==2 || $db_type==1 )
		{
		   if( $rID && $rID1 && $rID2 )
			{
				oci_commit($con);  
				echo "1**".str_replace("'",'',$update_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$update_id);
			}
		}
		disconnect($con);
		die;
	}
}

function sql_update2($strTable,$arrUpdateFields,$arrUpdateValues,$arrRefFields,$arrRefValues,$commit="",$return_query='')
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
echo  $strQuery;die;
		//return $strQuery;die;
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

if ($action=="load_php_dtls_form_update_val")
{
	$data=explode('_',$data);
	// cbo_location_id
	//$mc_per_line=$data[5];
	//$smv_basic=$data[6];
	$smv_basic_cond="";$machine_line_cond="";

	$cbo_prod_cat = $data[5];
	$cbo_prod_type = $data[6];
	
	//if($smv_basic) $smv_basic_cond="and a.basic_smv=$smv_basic";
	//if($mc_per_line) $machine_line_cond="and a.avg_machine_line=$mc_per_line";
	//$sql_result=sql_select("SELECT a.id, b.id as bid, b.month_id, b.date_calc, b.day_status, b.no_of_line, b.capacity_min, b.capacity_pcs, a.avg_machine_line, a.basic_smv, a.effi_percent from lib_capacity_calc_mst a, lib_capacity_calc_dtls b where a.id=b.mst_id and a.comapny_id=$data[0] and a.location_id=$data[1] and a.year=$data[2] and b.month_id=$data[3] and a.capacity_source=$data[4] and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $smv_basic_cond $machine_line_cond  order by b.date_calc");
	    $sql="SELECT a.id, a.avg_machine_line, a.basic_smv, a.effi_percent from lib_capacity_calc_mst a, lib_capacity_year_dtls b where a.id=b.mst_id and a.comapny_id=$data[0] and a.location_id=$data[1] and a.year=$data[2] and b.month_id=$data[3] and a.capacity_source=$data[4] and a.prod_category_id ='$cbo_prod_cat' and a.prod_type_id ='$cbo_prod_type' and b.WORKING_DAY is not null and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $smv_basic_cond $machine_line_cond";
	//echo "SELECT a.id, a.avg_machine_line, a.basic_smv, a.effi_percent from lib_capacity_calc_mst a, lib_capacity_year_dtls b where a.id=b.mst_id and a.comapny_id=$data[0] and a.location_id=$data[1] and a.year=$data[2] and b.month_id=$data[3] and a.capacity_source=$data[4] and b.WORKING_DAY is not null and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $smv_basic_cond $machine_line_cond";
 
	//echo $sql;die;

	$sql_result = sql_select($sql);
	//echo "<pre>";
	//print_r($sql_result);die;
//
	$i=0;
	$data_count=count($sql_result);
	foreach ($sql_result as $inf)
	{
		$i++;
		if ($i==$data_count)
		{
			
			echo "document.getElementById('txt_avg_mch_line').value 	= ".$inf[csf("avg_machine_line")].";\n";
			echo "document.getElementById('txt_basic_smv').value 	= ".$inf[csf("basic_smv")].";\n";
			echo "document.getElementById('txt_efficiency_per').value 	= ".$inf[csf("effi_percent")].";\n";
			echo "document.getElementById('update_id').value= '".$inf[csf("id")]."';\n";
			echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_capacity_calculation',1);\n";
			
			$mstid=$inf[csf("id")];
		}
	}

	$sqlDtls="SELECT b.id as bid, b.month_id, b.date_calc, b.day_status, b.no_of_line, b.capacity_min, b.capacity_pcs from lib_capacity_calc_dtls b where b.mst_id='$mstid' and b.month_id=$data[3] and b.status_active=1 and b.is_deleted=0 order by b.date_calc";
	//echo $sqlDtls;die;

	$sqlDtlsResult=sql_select($sqlDtls); $k=0; $capacity_min_sum=$capacity_pcs_sum=0;
	foreach ($sqlDtlsResult as $row)
	{
		$capacity_min=$capacity_pcs=0;
		$capacity_min=$row[csf('capacity_min')];
		$capacity_min_sum += $capacity_min;
		
		$capacity_pcs=$row[csf('capacity_pcs')];
		$capacity_pcs_sum += $capacity_pcs;
		$k++;
		echo "document.getElementById('cbo_day_status_".$k."').value 	= '".$row[csf("day_status")]."';\n";
		echo "document.getElementById('txt_no_of_line_".$k."').value 	= '".$row[csf("no_of_line")]."';\n";
		echo "document.getElementById('line_id').value					= '".$row[csf("no_of_line")]."';\n";
		echo "document.getElementById('txt_capacity_min_".$k."').value 	= '".$row[csf("capacity_min")]."';\n";
		echo "document.getElementById('txt_capacity_pcs_".$k."').value 	= '".$row[csf("capacity_pcs")]."';\n";
		echo "document.getElementById('update_id_dtls_".$k."').value 	= '".$row[csf("bid")]."';\n";
		
		if ($row[csf("day_status")]==2)
		{
			echo "document.getElementById('txt_capacity_min_".$k."').value 			= '';\n";
			echo "document.getElementById('txt_capacity_pcs_".$k."').value 			= '';\n";
			echo "disable_enable_fields( 'txt_capacity_min_".$k."*txt_capacity_pcs_".$k."', 1, '', '');\n";
		}
	}
	echo "document.getElementById('total_min').value 							= '".$capacity_min_sum."';\n";
	echo "document.getElementById('total_pcs').value 							= '".$capacity_pcs_sum."';\n";
	
	if($data_count==0)
	{
		echo "document.getElementById('txt_avg_mch_line').value 	= '';\n";
		echo "document.getElementById('txt_basic_smv').value 	= '';\n";
		echo "document.getElementById('txt_efficiency_per').value 	= '';\n";
		echo "document.getElementById('update_id').value= '';\n";
		echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_capacity_calculation',1);\n";
	}
	 
	exit;
}
if ($action=="load_php_dtls_form_update_val__old")
{
	$data=explode('_',$data);
	
	$cbo_prod_cat = $data[5];
	$cbo_prod_type = $data[6];
	
	
	//$sql_result=sql_select("select a.id,b.id as uid,b.month_id,b.date_calc,b.day_status,b.no_of_line,b.capacity_min,b.capacity_pcs from lib_capacity_calc_mst a,lib_capacity_calc_dtls b where a.comapny_id=$data[0] and a.location_id=$data[1] and a.year=$data[2]  and b.month_id=$data[3] and a.capacity_source=$data[4] and  a.status_active=1 and a.is_deleted=0 and a.id=b.mst_id");
	
	
	$sql_result=sql_select("select a.id, a.prod_category_id, b.id as bid, b.month_id, b.date_calc, b.day_status, b.no_of_line, b.capacity_min, b.capacity_pcs from lib_capacity_calc_mst a,lib_capacity_calc_dtls b where a.id=b.mst_id and a.comapny_id=$data[0] and a.location_id=$data[1] and a.year=$data[2] and a.capacity_source=$data[4] and b.month_id=$data[3] and a.prod_category_id ='$cbo_prod_cat' and a.prod_type_id ='$cbo_prod_type' and a.status_active=1 and a.is_deleted=0");
	
	$i=0;
	$data_count=count($sql_result);
	foreach ($sql_result as $inf)
	{
		$i++;
		
		$capacity_min=$inf[csf('capacity_min')];
		$capacity_min_sum += $capacity_min;
		
		$capacity_pcs=$inf[csf('capacity_pcs')];
		$capacity_pcs_sum += $capacity_pcs;

		
		echo "document.getElementById('update_id_dtls_".$i."').value 	= '".$inf[csf("bid")]."';\n";
		echo "document.getElementById('cbo_day_status_".$i."').value 	= '".$inf[csf("day_status")]."';\n";
		echo "document.getElementById('txt_no_of_line_".$i."').value 	= '".$inf[csf("no_of_line")]."';\n";
		echo "document.getElementById('line_id').value					= '".$inf[csf("no_of_line")]."';\n";
		echo "document.getElementById('txt_capacity_min_".$i."').value 	= '".$inf[csf("capacity_min")]."';\n";
		echo "document.getElementById('txt_capacity_pcs_".$i."').value 	= '".$inf[csf("capacity_pcs")]."';\n";
		
		if ($i==$data_count)
		{
			echo "document.getElementById('total_min').value	= '".$capacity_min_sum."';\n";
			echo "document.getElementById('total_pcs').value	= '".$capacity_pcs_sum."';\n";
			echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_capacity_calculation',1);\n";
		}
		
		if ($inf[csf("day_status")]==2)
		{
			echo "document.getElementById('txt_capacity_min_".$i."').value	= '';\n";
			echo "document.getElementById('txt_capacity_pcs_".$i."').value	= '';\n";
			echo "disable_enable_fields( 'txt_capacity_min_".$i."*txt_capacity_pcs_".$i."', 1, '', '');\n";
		}
	}
	exit;
}

if ($action=="load_php_dtls_form_update")
{
	$data=explode('_',$data);

	//$sql_res=sql_select("select a.id,a.comapny_id,a.capacity_source,a.year,a.location_id,a.avg_machine_line,a.basic_smv,a.effi_percent,c.id as year_id,c.month_id,c.working_day,c.capacity_month_min,c.capacity_month_pcs from lib_capacity_calc_mst a,lib_capacity_calc_dtls b,lib_capacity_year_dtls c where a.comapny_id=$data[0] and a.location_id=$data[1] and a.year=$data[2] and a.capacity_source=$data[3] and  a.status_active=1 and a.is_deleted=0 and a.id=c.mst_id group by c.month_id");
	 
	 
	$sql_res=sql_select("select a.id, a.comapny_id, a.capacity_source, a.year, a.prod_category_id,a.location_id, a.avg_machine_line, a.basic_smv, a.effi_percent, a.prod_category_id, a.prod_type_id, c.id as year_id, c.month_id, c.working_day, c.capacity_month_min, c.capacity_month_pcs from lib_capacity_calc_mst a, lib_capacity_calc_dtls b, lib_capacity_year_dtls c where  a.id = c.mst_id and a.id=b.mst_id and b.mst_id=c.mst_id and b.month_id=c.month_id and a.comapny_id=$data[0] and a.location_id=$data[1] and a.year=$data[2] and a.capacity_source=$data[3] and a.prod_category_id='$data[4]' and a.prod_type_id='$data[5]' and  a.status_active=1 and a.is_deleted=0 group by a.id, a.comapny_id, a.prod_category_id,a.capacity_source, a.year, a.location_id, a.avg_machine_line, a.basic_smv, a.effi_percent, a.prod_category_id, a.prod_type_id, c.id, c.month_id, c.working_day, c.capacity_month_min, c.capacity_month_pcs");
	 

	$i=0;
	$month_count=count($sql_res);
	foreach ($sql_res as $row)
	{
		$i++;
		
		$working_day=$row[csf('working_day')];
		$working_day_sum += $working_day;

		$capacity_month_min=$row[csf('capacity_month_min')];
		$capacity_month_min_sum += $capacity_month_min;

		$capacity_month_pcs=$row[csf('capacity_month_pcs')];
		$capacity_month_pcs_sum += $capacity_month_pcs;
		
		// echo "document.getElementById('txt_sl_no_".$i."').value 			= '".$i."';\n";
		// echo "document.getElementById('txt_month_".$i."').value 			= '".$months[$row[csf("month_id")]]."';\n";
		// echo "document.getElementById('txt_month_id_".$i."').value 			= '".$row[csf("month_id")]."';\n";
		// echo "document.getElementById('txt_working_day_".$i."').value 			= '".$row[csf("working_day")]."';\n";
		// echo "document.getElementById('txt_year_capacity_min_".$i."').value 	= '".$row[csf("capacity_month_min")]."';\n";
		// echo "document.getElementById('txt_year_capacity_pcs_".$i."').value 	= '".$row[csf("capacity_month_pcs")]."';\n";
		// echo "document.getElementById('update_id_year_dtls_".$i."').value 		= '".$row[csf("year_id")]."';\n";
		echo "document.getElementById('txt_sl_no_".$row[csf("month_id")]."').value = '".$row[csf("month_id")]."';\n";
		echo "document.getElementById('txt_month_".$row[csf("month_id")]."').value = '".$months[$row[csf("month_id")]]."';\n";
		echo "document.getElementById('txt_month_id_".$row[csf("month_id")]."').value = '".$row[csf("month_id")]."';\n";
		echo "document.getElementById('txt_working_day_".$row[csf("month_id")]."').value = '".$row[csf("working_day")]."';\n";
		echo "document.getElementById('txt_year_capacity_min_".$row[csf("month_id")]."').value = '".$row[csf("capacity_month_min")]."';\n";
		echo "document.getElementById('txt_year_capacity_pcs_".$row[csf("month_id")]."').value = '".$row[csf("capacity_month_pcs")]."';\n";
		echo "document.getElementById('update_id_year_dtls_".$row[csf("month_id")]."').value = '".$row[csf("year_id")]."';\n";
	
	
		//echo "document.getElementById('avg_rate_".$row[csf("month_id")]."').value = '".$row[csf("avg_rate")]."';\n";	
		
		if ($i==$month_count)
		{
			echo "document.getElementById('txt_avg_mch_line').value 			= '".$row[csf("avg_machine_line")]."';\n";
			echo "document.getElementById('txt_basic_smv').value 				= '".$row[csf("basic_smv")]."';\n";
			echo "document.getElementById('txt_efficiency_per').value 			= '".$row[csf("effi_percent")]."';\n";
			echo "document.getElementById('cbo_product_category').value 		= '".$row[csf("prod_category_id")]."';\n";
			//echo "load_drop_down('requires/capacity_calculation_controller_v2', '".($inf[csf("prod_category_id")])."', 'load_drop_down_product_type', 'product_type' );\n"; 
			echo "document.getElementById('cbo_product_type').value 			= '".$row[csf("prod_type_id")]."';\n";
			echo "document.getElementById('txt_working_day_total').value 		= '".$working_day_sum."';\n";
			echo "document.getElementById('txt_capacity_min_total').value 		= '".$capacity_month_min_sum."';\n";
			echo "document.getElementById('txt_capacity_pcs_total').value 		= '".$capacity_month_pcs_sum."';\n";
			echo "document.getElementById('update_id').value 					= '".$row[csf("id")]."';\n";
			if ($row[csf("id")]!="")
			{
				echo "disable_enable_fields( 'cbo_company_id*cbo_capacity_source*cbo_location_id*cbo_year*txt_avg_mch_line*txt_basic_smv*cbo_product_category*cbo_product_type*txt_efficiency_per', 1, '', '');\n";
			}
		}
	}
	exit;
}


if($action=="load_php_dtls_form_return_id_date")
{
	$qry_result=sql_select( "select id,mst_id from lib_capacity_calc_dtls where mst_id='$data' order by id ASC");
	foreach ($qry_result as $row)
	{
		if($id=="") $id=$row[csf("id")]; else $id.="*".$row[csf("id")];
		
	}
	echo $id;
}

if($action=="load_php_dtls_form_return_id_year")
{
	$qry_res=sql_select( "select id,mst_id from lib_capacity_year_dtls where mst_id='$data' order by id ASC");
	foreach ($qry_res as $inf)
	{
		if($id_year=="") $id_year=$inf[csf("id")]; else $id_year.="*".$inf[csf("id")];
		
	}
	echo $id_year;
}

if($action=="working_hour")
{
	$data_ex=explode("_",$data);
	//echo $data_ex[1];
	//applying_period_date 	
	if($db_type==0) 
	{
		$applying_period_date=change_date_format($data_ex[1],'yyyy-mm-dd');
		//$to_date=change_date_format($to_date,'yyyy-mm-dd');
	}
	else if($db_type==2) 
	{
		$applying_period_date=change_date_format($data_ex[1],'','',1);
		//$to_date=change_date_format($to_date,'','',1);
	}
	$working_hour=0;
	
	$qry_working_hour=sql_select( "select working_hour from  lib_standard_cm_entry where company_id='$data_ex[0]' and '$applying_period_date' between applying_period_date and applying_period_to_date and is_deleted=0 and  status_active=1");
	foreach ($qry_working_hour as $row)
	{
		$working_hour=$row[csf("working_hour")];
	}
	echo trim($working_hour);
	exit();
}
?>