<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action=="load_drop_down_subsection")
{
	$data=explode('_',$data);
	if($data[0]==1) $subID='1,2,3,23';
	else if($data[0]==3) $subID='4,5,18';
	else if($data[0]==5) $subID='6,7,8,9,10,11,12,13,16,17,17,24';
	else if($data[0]==10) $subID='14,15';
	else if($data[0]==7) $subID='19,20,21,25,26';
	else if($data[0]==9) $subID='22';
	else $subID='0';
	//echo $data[0]."**".$subID;
	//echo create_drop_down( "cboembtype_".$data[1], 80,$emb_type,"", 1, "-- Select --", "", "","","" ); 
	echo create_drop_down( "cboSubSection_1", 150, $trims_sub_section,"",1, "-- Select Sub-Section --","","for_uom();for_listview(1)",0,$subID,'','','','','',"cboSubSection[]");
	exit();
}

if ($action=="load_drop_down_member")
{
	$data=explode("_",$data);
	$sql="select b.id,b.team_member_name  from lib_marketing_team a, lib_mkt_team_member_info b where a.id=b.team_id and   a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and a.project_type=3 and team_id='$data[0]'";
	echo create_drop_down( "cbo_team_member", 150, $sql,"id,team_member_name", 1, "-- Select Member --", $selected, "for_listview(1);" );
	exit();
}

if ($action=="load_drop_down_month")
{
	$sales_year = return_field_value("sales_year_started", "variable_order_tracking", "company_name='$data' and variable_list=12", "sales_year_started");
	if($sales_year =='')$sales_year=0;
	echo create_drop_down( "cbo_month", 150, $months_short,"", 1, "-- Select Month --", $sales_year, "for_listview(1)",1 );	
	exit();
}

if($action=="check_booked_uom")
{
	$data=explode("_",$data);
	$subSection_cond='';
	if($data[2]!=0 && $data[2]!='')
	{
		$subSection_cond=" and sub_section_id=$data[2]";
	}
	$uom=return_field_value( "uom_id","lib_booked_uom_setup","company_id=$data[0] and section_id=$data[1] $subSection_cond");
	echo $uom;
	exit();	
}

if($action=="check_is_saved")
{
	$data=explode("_",$data);
	$mst_id=return_field_value( "id","trims_sales_target_mst","company_id=$data[0] and year_id=$data[1] and section_id=$data[2] and sub_section_id=$data[3] and team_leader_id = $data[4] and team_member_id = $data[5]  and status_active=1 and is_deleted=0" );
	echo $mst_id;
	exit();	
}

if ($action=="load_php_data_to_form")
{
	//echo $data;
	$nameArray=sql_select( "select id, company_id, section_id, sub_section_id, team_leader_id, team_member_id, year_id, starting_month_id, uom_id from trims_sales_target_mst where id='$data'" );
	foreach ($nameArray as $row)
	{	$list_data= $row[csf("company_id")].'_'.$row[csf("year_id")].'_'.$row[csf("starting_month_id")].'_'.$row[csf("section_id")].'_'.$row[csf("sub_section_id")].'_'.$row[csf("id")];
		echo "document.getElementById('cbo_company_name').value 		= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cboSection_1').value 			= '".$row[csf("section_id")]."';\n";
		echo "load_sub_section();\n";  
		echo "document.getElementById('cboSubSection_1').value 			= '".$row[csf("sub_section_id")]."';\n";
		echo "document.getElementById('cbo_team_leader').value			= '".$row[csf("team_leader_id")]."';\n";
		echo "load_drop_down( 'requires/sales_target_entry_for_trims_controller', document.getElementById('cbo_team_leader').value+'_'+1, 'load_drop_down_member', 'member_td' );\n";
		echo "document.getElementById('cbo_team_member').value			= '".$row[csf("team_member_id")]."';\n";
		echo "document.getElementById('cbo_year').value					= '".$row[csf("year_id")]."';\n";	
		echo "document.getElementById('cbo_month').value				= '".$row[csf("starting_month_id")]."';\n"; 
		echo "document.getElementById('hdn_uom_id').value          		= '".$row[csf("uom_id")]."';\n";
		echo "document.getElementById('update_id').value         		= '".$row[csf("id")]."';\n";

		echo "$('#cbo_company_name').attr('disabled','true')".";\n";
		echo "$('#cboSection_1').attr('disabled','true')".";\n";
		echo "$('#cboSubSection_1').attr('disabled','true')".";\n";
		echo "$('#cbo_year').attr('disabled','true')".";\n";

		echo "show_list_view('".$list_data."','order_dtls_list_view','details_view','requires/sales_target_entry_for_trims_controller','');\n";
		echo "set_button_status(1,'".$_SESSION['page_permission']."', 'fnc_sales_target_setup',1);\n";	
	}
	exit();	
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	if ($operation==0)  // Insert Here
	{
		if (is_duplicate_field( "id", "trims_sales_target_mst", "company_id=$cbo_company_name and year_id=$cbo_year and section_id=$section and sub_section_id=$sub_section and team_leader_id = $cbo_team_leader and team_member_id = $cbo_team_member and status_active=1 and is_deleted=0" ) == 1)
		{
			echo "11**0"; die; 
		}
		else
		{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			//if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
			$id=return_next_id( "id", "trims_sales_target_mst", 1 );
			$id1=return_next_id( "id", "trims_sales_target_dtls", 1 );
			$field_array= "id, company_id, section_id, sub_section_id, team_leader_id, team_member_id, year_id, starting_month_id, uom_id, total_quantity, total_amount,inserted_by,insert_date";
			$field_array2= "id, mst_id, month_id, year_id, uom_id, quantity, amount, inserted_by, insert_date";
			$data_array="(".$id.", '".$cbo_company_name."', '".$section."', '".$sub_section."', '".$cbo_team_leader."', '".$cbo_team_member."', '".$cbo_year."', '".$cbo_month."', '".$hdn_uom_id."', '".$total_qty."', '".$total_amt."', ".$_SESSION['logic_erp']['user_id'].", '".$pc_date_time."')";
			
			for($i=1; $i<=$total_row; $i++)
			{			
				$hdnMonthYear			= "hdnMonthYear_".$i;
				$txtQuantity			= "txtQuantity_".$i;
				$txtAmount 				= "txtAmount_".$i;
				$hdnDtlsUpdateId 		= "hdnDtlsUpdateId_".$i;

				$monthYear=explode("_",str_replace("'",'',$$hdnMonthYear));
				if ($i!=1) $data_array2 .=",";
				
				$data_array2 .="(".$id1.",".$id.",".$monthYear[0].",".$monthYear[1].", '".$hdn_uom_id."',".$$txtQuantity.",".$$txtAmount.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				$id1++;
			}
			//echo "10**INSERT INTO trims_sales_target_dtls(".$field_array2.") VALUES ".$data_array2;die;
			$rID=sql_insert("trims_sales_target_mst",$field_array,$data_array,0);
			$rID2=sql_insert("trims_sales_target_dtls",$field_array2,$data_array2,1);
			//echo $rID."==".$rID2; die;
			//check_table_status( $_SESSION['menu_id'],0);
			if($db_type==0)
			{
				if($rID && $rID2){
					mysql_query("COMMIT");
					echo "0**".$id;
				}
				else{
					mysql_query("ROLLBACK"); 
					echo "10**".$id;
				}
			}
			else if($db_type==2 || $db_type==1 )
			{
				if($rID && $rID2)
				{
					oci_commit($con);  
					echo "0**".$id;
				}
			else{
					oci_rollback($con); 
					echo "10**".$id;
				}
			}
			disconnect($con);
			die;
		}
	}
	else if ($operation==1)   // Update Here
	{
		if (is_duplicate_field( "id", "trims_sales_target_mst", "company_id=$cbo_company_name and year_id=$cbo_year and section_id=$section and sub_section_id=$sub_section and id!=$update_id and team_leader_id = $cbo_team_leader and team_member_id = $cbo_team_member and status_active=1 and is_deleted=0" ) == 1)
		{
			echo "11**0"; die; 
		}
		else
		{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			$field_array= "sub_section_id*team_leader_id*team_member_id*uom_id*total_quantity*total_amount*updated_by*update_date";
			$field_array2= "uom_id*quantity*amount*updated_by*update_date";
			$data_array="'".$sub_section."'*'".$cbo_team_leader."'*'".$cbo_team_member."'*'".$hdn_uom_id."'*'".$total_qty."'*'".$total_amt."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

			for($i=1; $i<=$total_row; $i++)
			{			
				$hdnMonthYear			= "hdnMonthYear_".$i;
				$txtQuantity			= "txtQuantity_".$i;
				$txtAmount 				= "txtAmount_".$i;
				$hdnDtlsUpdateId 		= "hdnDtlsUpdateId_".$i;
				//$monthYear=explode("_",str_replace("'",'',$$hdnMonthYear));
				$id_arr[]=str_replace("'",'',$$hdnDtlsUpdateId);
				$data_array2[str_replace("'",'',$$hdnDtlsUpdateId)] = explode("*",("'".str_replace("'","",$hdn_uom_id)."'*'".str_replace("'","",$$txtQuantity)."'*'".str_replace("'","",$$txtAmount)."'*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
			}
			$rID2=true;
			$rID=sql_update("trims_sales_target_mst",$field_array,$data_array,"id",$update_id,0);
			if(count($data_array2)>0)
			{
				//echo "10**".bulk_update_sql_statement( "lib_booked_uom_setup", "id", $field_array_update, $data_array_update, $id_arr ); die;
				$rID2=execute_query(bulk_update_sql_statement( "trims_sales_target_dtls", "id", $field_array2, $data_array2, $id_arr ));
			}

			//echo "10**".$rID."**".$rID2."**".$rID3; die;
			if($db_type==0)
			{
				if($rID && $rID2){
					mysql_query("COMMIT");  
					echo "1**".str_replace("'",'',$update_id);
				}
				else{
					mysql_query("ROLLBACK"); 
					echo "10**".str_replace("'",'',$update_id);
				}
			}
			else if($db_type==2 || $db_type==1 )
			{
				
				if($rID && $rID2)
				{
					oci_commit($con);  
					echo "1**".str_replace("'",'',$update_id);
				}
				else{
					oci_rollback($con); 
					echo "10**".str_replace("'",'',$update_id);
				}
			}
			disconnect($con);
			die;
		}
	}
	else if ($operation==2) // Delete Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$user_id=$_SESSION['logic_erp']['user_id'];

		$field_array_status="updated_by*update_date*status_active*is_deleted";
		$data_array_status=$user_id."*'".$pc_date_time."'*0*1";

		$rID=sql_update("trims_sales_target_mst",$field_array_status,$data_array_status,"id",$update_id,0);
		$rID2=sql_multirow_update("trims_sales_target_dtls",$field_array_status,$data_array_status,"mst_id",$update_id,0);
		
		if($db_type==0)
		{
			if($rID){
				mysql_query("COMMIT");  
				echo "2**".$rID;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$rID;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
		 if($rID)
			{
				oci_commit($con);   
				echo "2**".$rID;
			}
			else{
				oci_rollback($con);
				echo "10**".$rID;
			}
		}
		disconnect($con);
		die;
	}
}

if( $action=='order_dtls_list_view' ) 
{
	//echo $data; //die;
	$data=explode('_',$data);
	$prevYear=$data[1]-1;
	//echo "select id from trims_sales_target_mst where company_id=$data[0] and year_id=$prevYear and section_id=$data[2] and sub_section_id=$data[3] and status_active=1 and is_deleted=0 "; die;
	$prev_mst_id=return_field_value( "id","trims_sales_target_mst","company_id=$data[0] and year_id=$prevYear and section_id=$data[3] and sub_section_id=$data[4] and status_active=1 and is_deleted=0" );
	//echo $prevYear; die;
	$prev_sql = "SELECT id, mst_id, month_id, year_id, uom_id, quantity, amount from trims_sales_target_dtls where mst_id='$prev_mst_id' and status_active=1 and is_deleted=0 order by id ASC";
	//echo $sql; //die; 
	$prev_data_array=sql_select($prev_sql); $prev_data_arr=array();
	foreach ($prev_data_array as $rows)
	{
		$prev_data_arr[$rows[csf("year_id")]][$rows[csf("month_id")]]["uom"]	=$unit_of_measurement[$rows[csf("uom_id")]];
		$prev_data_arr[$rows[csf("year_id")]][$rows[csf("month_id")]]["quantity"]	=$rows[csf("quantity")];
		$prev_data_arr[$rows[csf("year_id")]][$rows[csf("month_id")]]["amount"]	=$rows[csf("amount")];
		
	}
	//echo "<pre>";
	//print_r($prev_data_arr);
	$mst_id=$data[5];
	$sql = "SELECT id, mst_id, month_id, year_id, uom_id, quantity, amount from trims_sales_target_dtls where mst_id='$mst_id' and status_active=1 and is_deleted=0 order by id ASC";
	//echo $sql; //die; 
	$data_array=sql_select($sql); 
	if(count($data_array) > 0)
	{
		$i=0; $selectMonth=$data_array[0][("month_id")];
		foreach($data_array as $row)
		{
			$i++; 
			?>
			<tr id="row_<? echo $i; ?>">
	            <td width="80" align="center" id="txtMonth_<? echo $i; ?>" style="word-break:break-all;"><? echo $months_short[$row[csf("month_id")]]."/".$row[csf("year_id")]; ?>&nbsp;
					<input type="hidden" name="hdnMonthYear[]" id="hdnMonthYear_<? echo $i; ?>" value="<? echo $row[csf("month_id")].'_'.$row[csf("year_id")]; ?>" ></td>
	            <td width="80" align="center" id="txtUom_<? echo $i; ?>" style="word-break:break-all;"><? echo $unit_of_measurement[$row[csf("uom_id")]]; ?>&nbsp;</td>
	            <td><input id="txtQuantity_<? echo $i; ?>" name="txtQuantity[]" class="text_boxes_numeric" type="text"  style="width:100px" value="<? echo $row[csf("quantity")]; ?>" onKeyUp="sum_total(1)" /></td>
	            <td><input id="txtAmount_<? echo $i; ?>" name="txtAmount[]" type="text" style="width:150px"  class="text_boxes_numeric" value="<? echo $row[csf("amount")]; ?>" onKeyUp="sum_total(2)" /></td>
	            <td width="80" align="center" id="txtPrevUom_<? echo $i; ?>" style="word-break:break-all;"><? echo $prev_data_arr[$prevYear][$row[csf("month_id")]]['uom']; ?>&nbsp;</td>
	            <td width="100" align="right" id="txtPrevQty_<? echo $i; ?>" style="word-break:break-all;"><? echo $prev_data_arr[$prevYear][$row[csf("month_id")]]['quantity']; ?>&nbsp;</td>
	            <td width="150" align="right" id="txtPrevAmount_<? echo $i; ?>" style="word-break:break-all;"><? echo $prev_data_arr[$prevYear][$row[csf("month_id")]]['amount']; ?>&nbsp;
	            	<input type="hidden" name="hdnDtlsUpdateId[]" id="hdnDtlsUpdateId_<? echo $i; ?>" value="<? echo $row[csf("id")]; ?>" ></td>
	        </tr>
			<?
			if($selectMonth==12)
			{
				 $selectYear=$selectYear+1;
				 $prevYear=$prevYear+1;
				 $selectMonth=0;
			}
			$selectMonth++;
		}
	}
	else
	{
		$selectMonth=$data[2];
		$selectYear=$data[1];
		$uomName='';
		$subSection_cond='';
		if($data[4]!=0 && $data[4]!='')
		{
			$subSection_cond=" and sub_section_id=$data[4]";
		}
		if($data[3]!=0 && $data[3]!='')
		{
			$uom=return_field_value( "uom_id","lib_booked_uom_setup","company_id=$data[0] and section_id=$data[3] $subSection_cond");
			$uomName=$unit_of_measurement[$uom];
		}
		//echo "select uom_id from lib_booked_uom_setup where company_id=$data[0] and section_id=$data[3] $subSection_cond";
		//echo $subSection_cond;
		for($i=1; $i<=12; $i++)
		{
			?>
			<tr id="row_<? echo $i; ?>">
	            <td width="80" align="center" id="txtMonth_<? echo $i; ?>" style="word-break:break-all;"><? echo $months_short[$selectMonth]."/".$selectYear; ?>&nbsp;
					<input type="hidden" name="hdnMonthYear[]" id="hdnMonthYear_<? echo $i; ?>" value="<? echo $selectMonth.'_'.$selectYear; ?>" ></td>
	            <td width="80" align="center" id="txtUom_<? echo $i; ?>" style="word-break:break-all;"><? echo $uomName; ?>&nbsp;</td>
	            <td><input id="txtQuantity_<? echo $i; ?>" name="txtQuantity[]" class="text_boxes_numeric" type="text"  style="width:100px"  onKeyUp="sum_total(1)" /></td>
	            <td><input id="txtAmount_<? echo $i; ?>" name="txtAmount[]" type="text" style="width:150px"  class="text_boxes_numeric"  onKeyUp="sum_total(2)" /></td>
	            <td width="80" align="center" id="txtPrevUom_<? echo $i; ?>" style="word-break:break-all;"><? echo $prev_data_arr[$prevYear][$selectMonth]['uom']; ?>&nbsp;</td>
	            <td width="100" align="right" id="txtPrevQty_<? echo $i; ?>" style="word-break:break-all;"><? echo $prev_data_arr[$prevYear][$selectMonth]['quantity']; ?>&nbsp;</td>
	            <td width="150" align="right" id="txtPrevAmount_<? echo $i; ?>" style="word-break:break-all;"><? echo $prev_data_arr[$prevYear][$selectMonth]['amount']; ?>&nbsp;
	            	<input type="hidden" name="hdnDtlsUpdateId[]" id="hdnDtlsUpdateId_<? echo $i; ?>" value="" ></td>
	        </tr>
			<?
			if($selectMonth==12)
			{
				 $selectYear=$selectYear+1;
				 $prevYear=$prevYear+1;
				 $selectMonth=0;
			}
			$selectMonth++;
		}
	}
	exit();
}
