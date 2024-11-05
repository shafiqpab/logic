<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:../../../login.php");
extract($_REQUEST);
include('../../../includes/common.php');

//---------------------------------------------------- Start


if ($action=="show_po_active_listview")
{
	 
	$arr=array (0=>$order_status,11=>$row_status);
 	$sql= "select is_confirmed,po_number,po_received_date,shipment_date,pub_shipment_date,po_quantity,unit_price,po_total_price,excess_cut,plan_cut,DATEDIFF(shipment_date,po_received_date) date_diff,status_active,id from  wo_po_break_down  where   status_active=1 and is_deleted=0 and job_no_mst='$data'"; 
	 
	echo  create_list_view("list_view", "Order Status,PO No,PO Recv. Date,Ship Date,Orgn. Ship Date,PO Qnty,Avg. Rate,Amount, Excess Cut %,Plan Cut Qnty,Lead Time,Status", "90,130,80,80,80,80,80,80,80,80,50,70","1050","220",0, $sql , "get_php_form_data", "id", 1, 1, "is_confirmed,0,0,0,0,0,0,0,0,0,0,status_active", $arr , "is_confirmed,po_number,po_received_date,shipment_date,pub_shipment_date,po_quantity,unit_price,po_total_price,excess_cut,plan_cut,date_diff,status_active", "../woven_order/requires/woven_order_entry_controller/",'','0,0,3,3,3,1,2,2,2,2,1') ;
	
}

if ($action=="show_deleted_po_active_listview")
{
	 
	$arr=array (0=>$order_status,11=>$row_status);
 	$sql= "select is_confirmed,po_number,po_received_date,shipment_date,pub_shipment_date,po_quantity,unit_price,po_total_price,excess_cut,plan_cut,DATEDIFF(shipment_date,po_received_date) date_diff,status_active,id from  wo_po_break_down  where   status_active<>1 and is_deleted<>0 and job_no_mst='$data'"; 
  
	echo  create_list_view("list_view", "Order Status,PO No,PO Recv. Date,Ship Date,Orgn. Ship Date,PO Qnty,Avg. Rate,Amount, Excess Cut %,Plan Cut Qnty,Lead Time,Status", "90,130,80,80,80,80,80,80,80,80,50,70","1050","220",0, $sql , "get_php_form_data", "id", 1, 1, "is_confirmed,0,0,0,0,0,0,0,0,0,0,status_active", $arr , "is_confirmed,po_number,po_received_date,shipment_date,pub_shipment_date,po_quantity,unit_price,po_total_price,excess_cut,plan_cut,date_diff,status_active", "../woven_order/requires/woven_order_entry_controller/",'','0,0,3,3,3,1,2,2,2,2,1') ;
	
}


if ($action=="cbo_dealing_merchant")
{
	echo create_drop_down( "cbo_dealing_merchant", 172, "select id,team_member_name from lib_mkt_team_member_info where status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-- Select Team Member --", $selected, "" );
	
}

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 172, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select --", $selected, "" );		 
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 172, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id and b.tag_company='$data' and a.party_type='' order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );   	 
} 

if ($action=="load_drop_down_agent")
{
	echo create_drop_down( "cbo_agent", 172, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id and b.tag_company='$data' and a.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3))  order by buyer_name","id,buyer_name", 1, "-- Select Agent --", $selected, "" );  
	 	 
} 


/*

if ($action=="save_update_delete_mst")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		 
		$id=return_next_id( "id", "wo_po_details_master", 1 ) ;
		$new_job_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', '', date("Y",time()), 5, "select job_no_prefix,job_no_prefix_num from wo_po_details_master where company_name=$cbo_company_name order by job_no_prefix_num desc ", "job_no_prefix", "job_no_prefix_num" ));
		 
		$field_array="id,garments_nature,job_no,job_no_prefix,job_no_prefix_num,company_name,buyer_name,location_name,style_ref_no,style_description,product_dept,currency_id,agent_name,order_repeat_no,region,exporting_item_catg,team_leader,dealing_marchant,packing,remarks,ship_mode,delay_clause,is_deleted,status_active,inserted_by,insert_date";
		 
		$data_array="(".$id.",".$garments_nature.",'".$new_job_no[0]."','".$new_job_no[1]."','".$new_job_no[2]."',".$cbo_company_name.",".$cbo_location_name.",".$cbo_buyer_name.",".$txt_style_ref.",".$txt_style_description.",".$cbo_product_department.",".$cbo_currercy.",".$cbo_agent.",".$txt_repeat_no.",".$cbo_region.",".$txt_item_catgory.",".$cbo_team_leader.",".$cbo_dealing_merchant.",".$cbo_packing.",".$txt_remarks.",".$cbo_ship_mode.",".$txt_delay_clause.",0,1,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
 	
 		$rID=sql_insert("wo_po_details_master",$field_array,$data_array,1);
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "0**".$new_job_no[0]."**".$rID;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$new_job_no[0]."**".$rID;
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			echo "0**".$new_job_no[0]."**".$rID;
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$field_array="company_name*buyer_name*location_name*style_ref_no*style_description*product_dept*currency_id*agent_name*order_repeat_no*region*exporting_item_catg*team_leader*dealing_marchant*packing*remarks*ship_mode*delay_clause*is_deleted*status_active*updated_by*update_date";
		 
		$data_array="".$cbo_company_name."*".$cbo_location_name."*".$cbo_buyer_name."*".$txt_style_ref."*".$txt_style_description."*".$cbo_product_department."*".$cbo_currercy."*".$cbo_agent."*".$txt_repeat_no."*".$cbo_region."*".$txt_item_catgory."*".$cbo_team_leader."*".$cbo_dealing_merchant."*".$cbo_packing."*".$txt_remarks."*".$cbo_ship_mode."*".$txt_delay_clause."*0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
 	
		$rID=sql_update("wo_po_details_master",$field_array,$data_array,"job_no","".$txt_job_no."",1);
		
		$txt_job_no=str_replace("'","",$txt_job_no);
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "0**".$txt_job_no."**".$rID;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$txt_job_no."**".$rID;
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			echo "0**".$txt_job_no."**".$rID;
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)   // Delete Here
	{
		$con = connect();
		
		$field_array="status_active*is_deleted";
		$data_array="'2'*'1'";
		$rID=sql_delete("wo_po_details_master",$field_array,$data_array,"job_no","".$txt_job_no."",1);
		disconnect($con);
		echo "2****".$rID;
	}
}

 
*/

if ($action=="get_excess_cut_percent")
{
	$data=explode("_",$data);
	
	 $qry_result=sql_select( "select slab_rang_start,slab_rang_end,excess_percent from  var_prod_excess_cutting_slab where company_name='$data[1]' and variable_list=2 and status_active=1 and is_deleted=0");
	 foreach ($qry_result as $row)
	 {
		 if ( $data[0]>=$row[csf("slab_rang_start")] && $data[0]<=$row[csf("slab_rang_end")] )
		 {
			 echo $row[csf("excess_percent")]; die;
		 }
	 }
	 echo "0"; die;
}
 
 
if($action=="create_po_search_list_view")
{
	
	$data=explode('_',$data);
	if ($data[0]!=0) $company=" and a.company_name='$data[0]'"; else { echo "Please Select Company First."; die; }
	
	if ($data[1]!=0) $buyer=" and a.buyer_name='$data[1]'"; else { echo "Please Select Buyer First."; die; }
	
	//if ($data[2]!=0) $buyer=" and a.buyer_name='$data[2]'"; else { echo "Please Select CHEK First."; die; } die;
	
	if ($data[3]!="" &&  $data[4]!="") $shipment_date = "and b.shipment_date between '".change_date_format($data[3], "yyyy-mm-dd", "-")."' and '".change_date_format($data[4], "yyyy-mm-dd", "-")."'"; else $shipment_date ="";
	 
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');

	$arr=array (1=>$comp,2=>$buyer_arr);
	
	if ($data[2]==0)
	{
		$sql= "select a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.job_quantity, b.po_number,b.po_quantity,b.shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1  and b.status_active=1 $shipment_date $company $buyer order by a.job_no";  
		
		echo  create_list_view("list_view", "Job No,Company,Buyer Name,Style Ref. No,Job Qty.,PO number,PO Quantity,Shipment Date", "90,120,100,100,90,90,90,80","1000","320",0, $sql , "get_php_form_data", "id", 1, 1, "0,company_name,buyer_name,0,0,0,0", $arr , "job_no,company_name,buyer_name,style_ref_no,job_quantity,po_number,po_quantity,shipment_date", "../contact_details/requires/",'','0,0,0,0,1,0,1,3') ;
	}
	else
	{
		$sql= "select a.job_no,a.company_name,a.buyer_name,a.style_ref_no from wo_po_details_master a where a.status_active=1  and a.is_deleted=0 $company $buyer order by a.job_no";  
		// $sql= "select a.job_no,a.company_name,a.buyer_name,a.style_ref_no,a.job_quantity from wo_po_details_master a,wo_po_break_down b where a.job_no!=b.job_no_mst and  a.status_active=1  and a.is_deleted=0 $company $buyer $extra_cond"; 
		echo  create_list_view("list_view", "Job No,Company,Buyer Name,Style Ref. No,", "90,120,100,100,90","1000","320",0, $sql , "get_php_form_data", "id", 1, 1, "0,company_name,buyer_name,0,0,0,0", $arr , "job_no,company_name,buyer_name,style_ref_no", "../contact_details/requires/",'','0,0,0,0,1,0,2,3') ;
	}
	
} 



//------------------------------------------------------------- reza
if ($action=="save_update_delete_mst")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		// Master part insert-----------------------------------------------
		$cbo_company_id=str_replace("'","",$cbo_company_id);
		$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
		$cbo_agent=str_replace("'","",$cbo_agent);
		$cbo_team_leader=str_replace("'","",$cbo_team_leader);
		$cbo_starting_month=str_replace("'","",$cbo_starting_month);
		$cbo_starting_year=str_replace("'","",$cbo_starting_year);
		$txt_total_qty=str_replace("'","",$txt_total_qty);
		$txt_total_val=str_replace("'","",$txt_total_val);
		$txt_total_target_mint=str_replace("'","",$txt_total_alo_prcnt);
		
		
		$mst_id=return_next_id( "id", "wo_sales_target_mst", 1 ) ;		 
		$field_array="id,company_id,buyer_id,agent,team_leader,designation,starting_month,starting_year, total_target_qty,total_target_value,total_target_mint,inserted_by,insert_date,status_active,is_deleted";
		$data_array="(".$mst_id.",".$cbo_company_id.",".$cbo_buyer_name.",".$cbo_agent.",".$cbo_team_leader.",'".$text_designation_value."',".$cbo_starting_month.",".$cbo_starting_year.",".$txt_total_qty.",".$txt_total_val.",".$txt_total_target_mint.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1','0')";
		
		
		// Details part insert ------------------------------------------------------
		$dtls_id=return_next_id("id", "wo_sales_target_dtls", 1 );
		$field_array_dtls="id, sales_target_mst_id, year_month_name, sales_target_qty, sales_target_value,sales_target_mint,sales_target_date";
		
         $k=1;
         $data_array_dtls='';
            for ($i=0; $i<12; $i++)
            {
                if ($k<13)
                {
                    $month=$cbo_starting_month+$i;
                    $yy=$cbo_starting_year;
					
					$month_year="month_".$month.$yy;
					$target_qty="qty_".$month.$yy;
					$target_value="val_".$month.$yy;
					$target_mint="mint_".$month.$yy;
					if($db_type==0){$target_date=$yy.'-'.$month."-01";}else{$target_date="01-".$months[$month].'-'.$yy;}
					if($data_array_dtls=='')
					{
						$data_array_dtls="(".$dtls_id.",".$mst_id.",".$$month_year.",".$$target_qty.",".$$target_value.",".$$target_mint.",'".$target_date."')";
					}
					else
					{
						$data_array_dtls.=",(".$dtls_id.",".$mst_id.",".$$month_year.",".$$target_qty.",".$$target_value.",".$$target_mint.",'".$target_date."')";
					}
                    $dtls_id++;
					if ($month==12)
                        { $cbo_starting_month=0; $i=0; $cbo_starting_year=$cbo_starting_year+1; }
                    
                }
                $k++;
            }	
		
//echo $field_array_dtls; die;
		
		$rID1=sql_insert("wo_sales_target_mst",$field_array,$data_array,1);
		$rID2=sql_insert("wo_sales_target_dtls",$field_array_dtls,$data_array_dtls,1);
		//echo "INSERT INTO wo_sales_target_dtls (".$field_array_dtls.") VALUES ".$data_array_dtls.""; die;

		if($db_type==0)
		{
			if($rID1==1 && $rID2==1){
				mysql_query("COMMIT");  
				echo "0**".$mst_id;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$mst_id;
			}
		}
		
		else if($db_type==2 || $db_type==1 )
		{
			if($rID1==1 && $rID2==1){
				oci_commit($con);  
				echo "0**".$mst_id;
			}
			else{
				oci_rollback($con);
				echo "10**".$mst_id;
			}
		}
		disconnect($con);
		die;
	}


	else if ($operation==1)   // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		// delete here--------------------------------------------------------------- 
		 $update_id=str_replace("'","",$update_id);
		 //echo $update_id; die;
		if($update_id){
			//$rID_dlt_1=execute_query("delete from wo_sales_target_mst where id =".$update_id."",0);
			//$rID_dlt_2=execute_query("delete from wo_sales_target_dtls where sales_target_mst_id =".$update_id."",0);
		}
		 
		
		
		/*if($db_type==0)
		{
			if($rID_dlt_1==1 && $rID_dlt_2==1){
				mysql_query("COMMIT");  
				echo "0**".$mst_id;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$mst_id;
			}
		}
		
		else if($db_type==2 || $db_type==1 )
		{
			if($rID_dlt_1==1 && $rID_dlt_2==1){
				oci_commit($con);  
				//echo "1**".$mst_id;
			}
			else{
				oci_rollback($con);
				//echo "10**".$mst_id;
			}
		}*/
		//Delete end----------------------------------------------
		
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
// Master part insert-----------------------------------------------
		$cbo_company_id=str_replace("'","",$cbo_company_id);
		$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
		$cbo_agent=str_replace("'","",$cbo_agent);
		$cbo_team_leader=str_replace("'","",$cbo_team_leader);
		$cbo_starting_month=str_replace("'","",$cbo_starting_month);
		$cbo_starting_year=str_replace("'","",$cbo_starting_year);
		$txt_total_qty=str_replace("'","",$txt_total_qty);
		$txt_total_val=str_replace("'","",$txt_total_val);
		$txt_total_target_mint=str_replace("'","",$txt_total_alo_prcnt);
		
		
		$field_array="company_id*buyer_id*agent*team_leader*starting_month*starting_year*total_target_qty*total_target_value*total_target_mint*updated_by*update_date*status_active*is_deleted";
		//txt_bhmerchant*txt_product_code
		$data_array="".$cbo_company_id."*".$cbo_buyer_name."*".$cbo_agent."*".$cbo_team_leader."*".$cbo_starting_month."*".$cbo_starting_year."*".$txt_total_qty."*".$txt_total_val."*".$txt_total_target_mint."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";
		//echo "update wo_sales_target_mst set(".$field_array.")=".$data_array.' where id= '.$update_id; die;
		 $rID11=sql_update("wo_sales_target_mst",$field_array,$data_array,"id","".$update_id."",1);
	//echo $rID11;
		/*$mst_id=return_next_id( "id", "wo_sales_target_mst", 1 ) ;		 
		$field_array="id,company_id,buyer_id,agent,team_leader,designation,starting_month,starting_year, total_target_qty,total_target_value,total_target_mint,inserted_by,insert_date,status_active,is_deleted";
		$data_array="(".$mst_id.",".$cbo_company_id.",".$cbo_buyer_name.",".$cbo_agent.",".$cbo_team_leader.",'".$text_designation_value."',".$cbo_starting_month.",".$cbo_starting_year.",".$txt_total_qty.",".$txt_total_val.",".$txt_total_target_mint.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1','0')";*/
		
		// Details part insert ------------------------------------------------------
		/*$dtls_id=return_next_id("id", "wo_sales_target_dtls", 1 );
		$field_array_dtls="id, sales_target_mst_id, year_month_name, sales_target_qty, sales_target_value,sales_target_mint,sales_target_date";
		
         $k=1;
         $data_array_dtls='';
            for ($i=0; $i<12; $i++)
            {
                if ($k<13)
                {
                    $month=$cbo_starting_month+$i;
                    $yy=$cbo_starting_year;
					
					$month_year="month_".$month.$yy;
					$target_qty="qty_".$month.$yy;
					$target_value="val_".$month.$yy;
					$target_mint="mint_".$month.$yy;
					if($db_type==0){$target_date=$yy.'-'.$month."-01";}else{$target_date="01-".$months[$month].'-'.$yy;}
					if($data_array_dtls=='')
					{
						$data_array_dtls="(".$dtls_id.",".$mst_id.",".$$month_year.",".$$target_qty.",".$$target_value.",".$$target_mint.",'".$target_date."')";
					}
					else
					{
						$data_array_dtls.=",(".$dtls_id.",".$mst_id.",".$$month_year.",".$$target_qty.",".$$target_value.",".$$target_mint.",'".$target_date."')";
					}
                    $dtls_id++;
					if ($month==12)
                        { $cbo_starting_month=0; $i=0; $cbo_starting_year=$cbo_starting_year+1; }
                    
                }
                $k++;
            }*/	
		
		//echo $data_array_dtls; die;
		//echo "INSERT INTO wo_sales_target_mst (".$field_array.") VALUES ".$data_array.""; die;
		//$rID11=sql_insert("wo_sales_target_mst",$field_array,$data_array,1);
		//$rID22=sql_insert("wo_sales_target_dtls",$field_array_dtls,$data_array_dtls,1);
	
	
		if($db_type==0)
		{
			if($rID11==1 && $rID22==1){
				mysql_query("COMMIT");  
				echo "0**".$mst_id;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$mst_id;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID11){
				oci_commit($con);  
				echo "0**".$update_id;
			}
			else{
				oci_rollback($con);
				echo "10**".$update_id;
			}
		}
		
		disconnect($con);
		die;
	}
	
	
	else if ($operation==2)   // Delete Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
/*		
		// delete here--------------------------------------------------------------- 
		 $update_id=str_replace("'","",$update_id);
		if($update_id){
			$rID1=execute_query("delete from wo_sales_target_mst where id =".$update_id."",0);
			$rID2=execute_query("delete from wo_sales_target_dtls where sales_target_mst_id =".$update_id."",0);
		}
		 
		
		
		if($db_type==0)
		{
			if($rID1==1 && $rID2==1){
				mysql_query("COMMIT");  
				echo "0**".$mst_id;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$mst_id;
			}
		}
		
		else if($db_type==2 || $db_type==1 )
		{
			if($rID1==1 && $rID2==1){
				oci_commit($con);  
				//echo "1**".$mst_id;
			}
			else{
				oci_rollback($con);
				//echo "10**".$mst_id;
			}
		}
		//Delete end----------------------------------------------
		
*/		
		
		disconnect($con);
		echo "2****".$rID;
	}
}

if ($action=="select_month_from_variable")
{
	$month=return_field_value("sales_year_started","variable_order_tracking","company_name='$data' and variable_list=12","sales_year_started");	
	echo "document.getElementById('cbo_starting_month').value='".$month."'";	
}




if ($action=="generate_list_view")
{
	list($company_id,$buyer_id,$agent,$tem_leader,$t_month,$t_year)=explode( "_", $data );
	if($t_year!=0 and $t_month!=0){

	//Previous Data History------------------------------------------------------------------
	$sql="select dtls.year_month_name, dtls.sales_target_qty, dtls.sales_target_value, dtls.sales_target_mint FROM wo_sales_target_mst mst,wo_sales_target_dtls dtls WHERE mst.id=dtls.sales_target_mst_id AND mst.company_id='$company_id' AND mst.buyer_id='$buyer_id' AND mst.agent='$agent' AND mst.team_leader='$tem_leader' AND mst.starting_year=($t_year-1)";
	$sql_result=sql_select($sql);

	foreach($sql_result as $row)
	{
		$mmyy=trim(str_replace(",","",$row[csf("year_month_name")]));
		$prv_data_arr['qty'.$mmyy]=$row[csf("sales_target_qty")];
		$prv_data_arr['val'.$mmyy]=$row[csf("sales_target_value")];
		$prv_data_arr['mint'.$mmyy]=$row[csf("sales_target_mint")];
	}
	
	//Current Data History------------------------------------------------------------------
	$sql="select mst.id,dtls.year_month_name, dtls.sales_target_qty, dtls.sales_target_value,dtls.sales_target_mint FROM wo_sales_target_mst mst,wo_sales_target_dtls dtls WHERE mst.id=dtls.sales_target_mst_id AND mst.company_id='$company_id' AND mst.buyer_id='$buyer_id' AND mst.agent='$agent' AND mst.team_leader='$tem_leader' AND mst.starting_year=$t_year";
	$sql_result=sql_select($sql);
	$mst_id="";
	$target_mint="";
	foreach($sql_result as $row)
	{
		$mmyy=trim(str_replace(",","",$row[csf("year_month_name")]));
		$cur_data_arr['qty'.$mmyy]=$row[csf("sales_target_qty")];
		$cur_data_arr['val'.$mmyy]=$row[csf("sales_target_value")];
		$cur_data_arr['mint'.$mmyy]=$row[csf("sales_target_mint")];
		$mst_id=$row[csf("id")];
		$target_mint=$row[csf("sales_target_mint")];
	}
	//echo $mst_id;
	//current data------------------------------------------------------------------------------
	 $sql_data="select a.company_id,a.location_id,a.year_id,a.month_id,b.buyer_id,b.allocation_percentage,c.comapny_id,c.location_id,c.year,d.month_id,d.capacity_min,e.capacity_month_min from lib_capacity_allocation_mst a, lib_capacity_allocation_dtls b,lib_capacity_calc_mst c, lib_capacity_calc_dtls d,lib_capacity_year_dtls e where a.id=b.mst_id and c.id=d.mst_id and c.capacity_source=1 and a.company_id=c.comapny_id and a.location_id=c.location_id and a.year_id=c.year and a.month_id=d.month_id and a.company_id= $company_id and b.buyer_id= $buyer_id and c.year= $t_year and c.id=e.mst_id and d.month_id=e.month_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.capacity_min is not null and b.allocation_percentage is not null  group by a.company_id,a.location_id,a.year_id,a.month_id,b.buyer_id,b.allocation_percentage,c.comapny_id,c.location_id,c.year,d.month_id,d.capacity_min,e.capacity_month_min"; 
	//$sql_data="select a.company_id,a.location_id,a.year_id,a.month_id,b.buyer_id,b.allocation_percentage,c.comapny_id,c.location_id,c.year,d.month_id,d.capacity_min,e.capacity_month_min from lib_capacity_allocation_mst a, lib_capacity_allocation_dtls b,lib_capacity_calc_mst c, lib_capacity_calc_dtls d where a.id=b.mst_id and c.id=d.mst_id and c.capacity_source=1 and a.company_id=c.comapny_id and a.location_id=c.location_id and a.year_id=c.year and a.month_id=d.month_id and a.company_id= $company_id and b.buyer_id= $buyer_id and c.year= $t_year and a.is_deleted=0 and a.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.capacity_min is not null and b.allocation_percentage is not null  group by a.company_id,a.location_id,a.year_id,a.month_id,b.buyer_id,b.allocation_percentage,c.comapny_id,c.location_id,c.year,d.month_id,d.capacity_min"; 

	$al_prcnt_arr=array();
	$capacity_min_arr=array();
	$month_id_arr=array();
	$sql_data=sql_select($sql_data);
	foreach($sql_data as $row)
	{
		//$mmyy=trim(str_replace(",","",$row[csf("year_month_name")]));	
		$al_prcnt_arr[]=$row[csf("allocation_percentage")];
		//$capacity_min_arr[]=$row[csf("capacity_min")];
		$capacity_min_arr[]=$row[csf("capacity_month_min")];
		$month_id_arr[]=$row[csf("month_id")];
	}
	//$get_target_min=$al_prcnt*$capacity_min/100;
	$arr_al_prcnt_arr=array_combine($month_id_arr,$al_prcnt_arr);
	$arr_capacity_arr=array_combine($month_id_arr,$capacity_min_arr);
	//previous data----------------------------------------------------------------------------
	$pre_sql_data=sql_select("select a.company_id,a.location_id,a.year_id,a.month_id,b.buyer_id,b.allocation_percentage,c.comapny_id,c.location_id,c.year,d.month_id,d.capacity_min,e.capacity_month_min from lib_capacity_allocation_mst a, lib_capacity_allocation_dtls b,lib_capacity_calc_mst c, lib_capacity_calc_dtls d,lib_capacity_year_dtls e where a.id=b.mst_id and c.id=d.mst_id and c.capacity_source=1 and a.company_id=c.comapny_id and a.location_id=c.location_id and a.year_id=c.year and a.month_id=d.month_id and a.company_id= $company_id and b.buyer_id= $buyer_id and c.year= ($t_year-1) and c.id=e.mst_id and d.month_id=e.month_id and  a.is_deleted=0 and a.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.capacity_min is not null and b.allocation_percentage is not null  group by a.company_id,a.location_id,a.year_id,a.month_id,b.buyer_id,b.allocation_percentage,c.comapny_id,c.location_id,c.year,d.month_id,d.capacity_min,e.capacity_month_min"); 
	$pre_al_prcnt_arr=array();
	$pre_capacity_min_arr=array();
	$pre_month_id_arr=array();
	foreach($pre_sql_data as $row)
	{
		//$mmyy=trim(str_replace(",","",$row[csf("year_month_name")]));
		$pre_al_prcnt_arr[]=$row[csf("allocation_percentage")];
		//$pre_capacity_min_arr[]=$row[csf("capacity_min")];
		$pre_capacity_min_arr[]=$row[csf("capacity_month_min")];
		$pre_month_id_arr[]=$row[csf("month_id")];
	}
	//$pre_get_target_min=$pre_al_prcnt*$pre_capacity_min/100;
	$pre_arr_al_prcnt_arr=array_combine($pre_month_id_arr,$pre_al_prcnt_arr);
	$pre_arr_capacity_arr=array_combine($pre_month_id_arr,$pre_capacity_min_arr);
	?> 
    <fieldset>
      <div><!--main div start-->
        <div style="width:1170px;"><!--first div start-->
            <table width="100%" class="rpt_table" id="tbl_header" cellpadding="0" cellspacing="1" rules="all">
                <thead class="form_table_header">
                  <tr>                      
                     <th  width="150" rowspan="2" >Month</th>
                     <th colspan="3">Current Year </th>                             
                     <th colspan="3"><p>Previous Year's Target </p></th>
                  </tr> 
                   <tr>
                        <th  width="160">Qnty Target </th>
                        <th width="160">Value Target</th>
                        <th width="160">Target Mint </th>  
                        <th width="160">Qnty. Target </th>                              
                        <th width="160">Value Target</th>
                        <th>Target Mint </th> 
                   </tr>                                                 	  
               </thead> 
          </table>
      </div>
     <div style="width:1170px;"><!--Second div start-->
     	<input type="text" id="update_id" name="update_id" value="<? echo $mst_id; ?>" />
       <table width="100%"  class="rpt_table" id="table_body" cellpadding="0" cellspacing="1" rules="all">
       <?
         $k=1;
         $tot_prv_qty=$tot_prv_val=0;
            for ($i=0; $i<12; $i++)
            {
                if ($k<13)
                {
                    $month=$t_month+$i;
                    $yy=$t_year;
					
                    ?>
                    <tbody>
                    <tr>                       	 
                         <td width="150" align="center">
                             <input type="hidden" id="month_<? echo $month.$yy; ?>" name="month_<? echo $month.$yy; ?>" value="<? echo $month.','.$yy; ?>" />
                             <input type="text" name="monthdisplay_<? echo $month.$yy; ?>" id="monthdisplay_<? echo $month.$yy; ?>" readonly="readonly" value="<? echo $months[$month].','.$yy; ?>"  class="text_boxes"/>
                         </td>
                         <td width="160" align="center">
							 <? $cur_qty=$cur_data_arr['qty'.$month.$yy]; $tot_cur_qty+=$cur_qty; ?>
                             <input type="text"  name="qty_<? echo $month.$yy; ?>" id="qty_<? echo $month.$yy; ?>" onkeyup="fn_calculate()" class="text_boxes_numeric" value="<? echo $cur_qty;?>" />
                         </td>
                         <td width="160" align="center">
							 <? $cur_val=$cur_data_arr['val'.$month.$yy]; $tot_cur_val+=$cur_val; ?>
                             <input type="text" name="val_<? echo $month.$yy; ?>" id="val_<? echo $month.$yy; ?>"  onkeyup="fn_calculate()" class="text_boxes_numeric" value="<? echo $cur_val;?>" />
                         </td> 
                         
                         
                         <td width="160" align="center">
                         <? if($mst_id=="")
						 {
							 $cur_trg_mint=$cur_data_arr['mint'.$month.$yy]; $tot_trg_mint+=$cur_trg_mint;  $tot_alo_prcnt_val+=$arr_al_prcnt_arr[$month]*$arr_capacity_arr[$month]/100; echo $tot_alo_prcnt_val;?>
                             <input type="text" name="mint_<? echo $month.$yy; ?>" id="mint_<? echo $month.$yy; ?>"  onkeyup="fn_calculate()" class="text_boxes_numeric" value="<? echo $arr_al_prcnt_arr[$month]*$arr_capacity_arr[$month]/100; ?>" />
                          <?
						 }
						 else
						 {
							  $cur_trg_mint=$cur_data_arr['mint'.$month.$yy]; $tot_trg_mint+=$cur_trg_mint; ?>
                             <input type="text" name="mint_<? echo $month.$yy; ?>" id="mint_<? echo $month.$yy; ?>"  onkeyup="fn_calculate()" class="text_boxes_numeric" value="<? echo  $cur_trg_mint; ?>" />
                          <?
						 }
						 ?>
						
                         </td>
                         
                         <td width="160" align="right"><? echo $prv_data_arr['qty'.$month.($yy-1)];$tot_prv_qty+=$prv_data_arr['qty'.$month.($yy-1)]; ?></td>
                         <td width="160" align="right"><? echo $prv_data_arr['val'.$month.($yy-1)];$tot_prv_val+=$prv_data_arr['val'.$month.($yy-1)]; ?></td>
                         <td align="right"><? echo $pre_arr_al_prcnt_arr[$month]*$pre_arr_capacity_arr[$month]/100;$pre_tot_alo_prcnt_val+=$pre_arr_al_prcnt_arr[$month]*$pre_arr_capacity_arr[$month]/100; ?></td>
                         
                     </tr>
                    <?							
                    if ($month==12)
                        { $t_month=0; $i=0; $t_year=$t_year+1; }
                    
                }
                $k++;
            }	
            ?>									
            <tr>                       	 
                 <td>Total</td>
                 <td align="center"><input type="text" id="txt_total_qty" value="<? echo $tot_cur_qty;?>" readonly="readonly" class="text_boxes" /></td>
                 <td align="center"><input type="text" id="txt_total_val" value="<? echo $tot_cur_val;?>" readonly="readonly" class="text_boxes" /> </td>
                 <td align="center"><input type="text" id="txt_total_alo_prcnt" value="<? echo $target_mint;?>" readonly="readonly" class="text_boxes" style="text-align:right;"/> </td>
                
                 <td align="right"><? echo $tot_prv_qty; ?></td>
                 <td align="right"><? echo $tot_prv_val; ?></td>
                 <td align="right"><? echo $pre_tot_alo_prcnt_val; ?></td>
            </tr>
        </tbody>
    </table>
</div>
</div> <!--main div close-->
</fieldset>                       
 
<?
	}//end if con
echo "*_*".$mst_id;
exit();
}
?>


 