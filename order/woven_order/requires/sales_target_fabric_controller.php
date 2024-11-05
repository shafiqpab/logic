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
		
		$mst_id=return_next_id( "id", "wo_sales_target_mst", 1 ) ;		 
		$field_array="id, company_id, buyer_id, agent, team_leader, designation, starting_month, starting_year, total_target_qty, total_target_value,  inserted_by, insert_date, status_active, is_deleted, entry_form";
		$data_array="(".$mst_id.",".$cbo_company_id.",".$cbo_buyer_name.",".$cbo_agent.",".$cbo_team_leader.",'".$text_designation_value."',".$cbo_starting_month.",".$cbo_starting_year.",".$txt_total_qty.",".$txt_total_val.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1','0',513)";
		
		
		// Details part insert ------------------------------------------------------
		$dtls_id=return_next_id("id", "wo_sales_target_dtls", 1 );
		$field_array_dtls="id, sales_target_mst_id, year_month_name, sales_target_qty, sales_target_value, sales_target_date";
		
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
					
					if($db_type==0){$target_date=$yy.'-'.$month."-01";}else{$target_date="01-".$months[$month].'-'.$yy;}
					if($data_array_dtls=='')
					{
						$data_array_dtls="(".$dtls_id.",".$mst_id.",".$$month_year.",".$$target_qty.",".$$target_value.",'".$target_date."')";
					}
					else
					{
						$data_array_dtls.=",(".$dtls_id.",".$mst_id.",".$$month_year.",".$$target_qty.",".$$target_value.",'".$target_date."')";
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
			////echo "INSERT INTO pro_roll_details (".$field_array_roll.") VALUES ".$data_array_roll.""; die;
			//echo "update inv_receive_master set(".$field_array_update.")=".$data_array_update; die;

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

		$starting_month=str_replace("'","",$cbo_starting_month);
		$starting_year=str_replace("'","",$cbo_starting_year);
		$k=1;
		for ($i=0; $i<12; $i++)
		{
			if ($k<13)
			{
				$month=$starting_month+$i;
				$yy=$starting_year;
				$month_year="month_".$month.$yy;
				$target_qty="qty_".$month.$yy;
				$target_value="val_".$month.$yy;

				$month_year_arr[$$month_year] = $$month_year;
				//$month_year_quantity[$$month_year] += str_replace("'","",$$target_qty)*1;

				if ($month==12)
				{ $starting_month=0; $i=0; $starting_year=$starting_year+1; }
			}
			$k++;
		}	

		$month_year_arr = array_filter($month_year_arr);
		$month_years = implode(",",$month_year_arr);

		$month_target_arr = return_library_array("select sum(sales_target_qty) as sales_target_qty, b.year_month_name from wo_sales_target_mst a, wo_sales_target_dtls b where a.id=b.sales_target_mst_id and a.entry_form =513 and a.company_id=$cbo_company_id and a.team_leader=$cbo_team_leader and b.year_month_name in ($month_years) and a.status_active=1 and a.id!=$update_id group by b.year_month_name","year_month_name","sales_target_qty");

		$pre_fso_sql = sql_select("select sum(b.finish_qty) as finish_qty, sum(b.pp_qnty) as pp_qnty, sum(b.mtl_qnty) as mtl_qnty, sum(b.fpt_qnty) as fpt_qnty, sum(b.gpt_qnty) as gpt_qnty, to_char(a.delivery_date, 'YYYY') as d_year, to_char(a.delivery_date, 'MM') as d_month
		from fabric_sales_order_mst a, fabric_sales_order_dtls b 
		where a.id=b.mst_id and a.entry_form= 472 and b.status_active=1 and a.company_id=$cbo_company_id and a.team_leader=$cbo_team_leader 
		group by a.team_leader, to_char(a.delivery_date, 'YYYY'),  to_char(a.delivery_date, 'MM')");

		foreach($pre_fso_sql as $fsoVal)
		{
			$fso_year_month_arr[($fsoVal[csf("d_month")]*1).",".$fsoVal[csf("d_year")]]=$fsoVal[csf("finish_qty")] + $fsoVal[csf("pp_qnty")] + $fsoVal[csf("mtl_qnty")] + $fsoVal[csf("fpt_qnty")] + $fsoVal[csf("gpt_qnty")];
		}
		/* echo  "10**";
		print_r($fso_year_month_arr);
		die; */

		$starting_month=str_replace("'","",$cbo_starting_month);
		$starting_year=str_replace("'","",$cbo_starting_year);
		$k=1;
		for ($i=0; $i<12; $i++)
		{
			if ($k<13)
			{
				$month=$starting_month+$i;
				$yy=$starting_year;
				$month_year="month_".$month.$yy;
				$target_qty="qty_".$month.$yy;
				$target_value="val_".$month.$yy;

				$pre_fso_qnty_by_TL = $fso_year_month_arr[str_replace("'","",$$month_year)];
				$pre_month_target_qnty = $month_target_arr[str_replace("'","",$$month_year)];

				if($pre_fso_qnty_by_TL  >  $pre_month_target_qnty + str_replace("'","",$$target_qty)*1)
				{
					echo "20**Total finish qty exceeded team wise target qty\nSales Finish Qnty :".$pre_fso_qnty_by_TL ."\nMonthly Target :".($pre_month_target_qnty + str_replace("'","",$$target_qty)*1);
					die;
				}
				//echo "10**".$pre_fso_qnty_by_TL ." > ".  $pre_month_target_qnty ."+". str_replace("'","",$$target_qty)*1; die; 

				if ($month==12)
				{ $starting_month=0; $i=0; $starting_year=$starting_year+1; }
			}
			$k++;
		}

		/* echo  "10**";
		print_r($fso_year_month_arr);
		die; */
		
		// delete here--------------------------------------------------------------- 
		 $update_id=str_replace("'","",$update_id);
		 //echo $update_id; die;
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
		
		$mst_id=return_next_id( "id", "wo_sales_target_mst", 1 ) ;		 
		$field_array="id, company_id, buyer_id, agent, team_leader, designation, starting_month, starting_year, total_target_qty, total_target_value,  inserted_by, insert_date, status_active, is_deleted, entry_form";
		$data_array="(".$mst_id.",".$cbo_company_id.",".$cbo_buyer_name.",".$cbo_agent.",".$cbo_team_leader.",'".$text_designation_value."',".$cbo_starting_month.",".$cbo_starting_year.",".$txt_total_qty.",".$txt_total_val.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1','0',513)";

		
		// Details part insert ------------------------------------------------------
		$dtls_id=return_next_id("id", "wo_sales_target_dtls", 1 );
		$field_array_dtls="id, sales_target_mst_id, year_month_name, sales_target_qty, sales_target_value, sales_target_date";
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
					
					if($db_type==0){$target_date=$yy.'-'.$month."-01";}else{$target_date="01-".$months[$month].'-'.$yy;}
					if($data_array_dtls=='')
					{
						$data_array_dtls="(".$dtls_id.",".$mst_id.",".$$month_year.",".$$target_qty.",".$$target_value.",'".$target_date."')";
					}
					else
					{
						$data_array_dtls.=",(".$dtls_id.",".$mst_id.",".$$month_year.",".$$target_qty.",".$$target_value.",'".$target_date."')";
					}
                    $dtls_id++;
					if ($month==12)
                    { $cbo_starting_month=0; $i=0; $cbo_starting_year=$cbo_starting_year+1; }
                    
                }
                $k++;
            }	
		
		
		//echo $data_array_dtls; die;
		//echo "INSERT INTO wo_sales_target_mst (".$field_array.") VALUES ".$data_array.""; die;
		$rID11=sql_insert("wo_sales_target_mst",$field_array,$data_array,1);
		$rID22=sql_insert("wo_sales_target_dtls",$field_array_dtls,$data_array_dtls,1);
		//echo "INSERT INTO wo_sales_target_dtls (".$field_array_dtls.") VALUES ".$data_array_dtls.""; die;

		if($db_type==0)
		{
			if($rID11==1 && $rID22==1 && $rID1==1 && $rID2==1){
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
			if($rID11==1 && $rID22==1 && $rID1==1 && $rID2==1){
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
	$sql="select dtls.year_month_name, dtls.sales_target_qty, dtls.sales_target_value, dtls.sales_target_mint FROM wo_sales_target_mst mst, wo_sales_target_dtls dtls WHERE mst.entry_form=513 and mst.id=dtls.sales_target_mst_id AND mst.company_id='$company_id' AND mst.buyer_id='$buyer_id' AND mst.agent='$agent' AND mst.team_leader='$tem_leader' AND mst.starting_year=($t_year-1)";
	$sql_result=sql_select($sql);

	foreach($sql_result as $row)
	{
		$mmyy=trim(str_replace(",","",$row[csf("year_month_name")]));
		$prv_data_arr['qty'.$mmyy]=$row[csf("sales_target_qty")];
		$prv_data_arr['val'.$mmyy]=$row[csf("sales_target_value")];
		$prv_data_arr['mint'.$mmyy]=$row[csf("sales_target_mint")];
	}
	
	//Current Data History------------------------------------------------------------------
	 $sql="select mst.id, mst.total_target_mint, dtls.year_month_name, dtls.sales_target_qty, dtls.sales_target_value, dtls.sales_target_mint, dtls.cm, dtls.cm_val_per, dtls.rm_val_per, dtls.actual_margin_per FROM wo_sales_target_mst mst, wo_sales_target_dtls dtls WHERE mst.entry_form=513 and mst.id=dtls.sales_target_mst_id AND mst.company_id='$company_id' AND mst.buyer_id='$buyer_id' AND mst.agent='$agent' AND mst.team_leader='$tem_leader' AND mst.starting_year=$t_year";
	$sql_result=sql_select($sql);
	$mst_id="";
	$target_mint="";
	foreach($sql_result as $row)
	{
		$mmyy=trim(str_replace(",","",$row[csf("year_month_name")]));
		$cur_data_arr['qty'.$mmyy]=$row[csf("sales_target_qty")];
		$cur_data_arr['val'.$mmyy]=$row[csf("sales_target_value")];
		$cur_data_arr['mint'.$mmyy]=$row[csf("sales_target_mint")];
		
		$cur_data_arr['cm'.$mmyy]=$row[csf("cm")];
		$cur_data_arr['cmval'.$mmyy]=$row[csf("cm_val_per")];
		$cur_data_arr['rmval'.$mmyy]=$row[csf("rm_val_per")];
		$cur_data_arr['actval'.$mmyy]=$row[csf("actual_margin_per")];
		
		$mst_id=$row[csf("id")];
		$target_mint=$row[csf("total_target_mint")];
	}
	//echo $mst_id;

	?> 
    <fieldset>
      <div><!--main div start-->
        <div style="width:570px;"><!--first div start-->
            <table width="100%" class="rpt_table" id="tbl_header" cellpadding="0" cellspacing="1" rules="all">
                <thead class="form_table_header">
                  <tr>                      
                     <th width="150" rowspan="2" >Month</th>
                     <th colspan="2">Current Year </th>                           
                     <th colspan="2"><p>Previous Year's Target</p></th>
                  </tr> 
                   <tr>
                        <th width="100">Qty Target </th>
                        <th width="100">Value Target</th>
                        <th width="100">Qty. Target </th>                              
                        <th width="100">Value Target</th>
                   </tr>                                                 	  
               </thead> 
          </table>
      </div>
     <div style="width:570px;"><!--Second div start-->
     	<input type="hidden" id="update_id" name="update_id" value="<? echo $mst_id; ?>" />
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
					
					
					$cur_qty=$cur_data_arr['qty'.$month.$yy]; $tot_cur_qty+=$cur_qty;
					$cur_val=$cur_data_arr['val'.$month.$yy]; $tot_cur_val+=$cur_val;
					
                    ?>
                    <tbody>
                    <tr>                       	 
						<td width="150">
							<input type="hidden" id="month_<? echo $month.$yy; ?>" name="month_<? echo $month.$yy; ?>" value="<? echo $month.','.$yy; ?>" />
							<input type="text" name="monthdisplay_<? echo $month.$yy; ?>" id="monthdisplay_<? echo $month.$yy; ?>" readonly="readonly" value="<? echo $months[$month].','.$yy; ?>"  class="text_boxes" style="width:140px;"/>
						</td>
						<td width="100"><input type="text"  name="qty_<? echo $month.$yy; ?>" id="qty_<? echo $month.$yy; ?>" onkeyup="fn_calculate()" class="text_boxes_numeric" value="<? echo $cur_qty;?>" style="width:85px;"/></td>
						<td width="100"><input type="text" name="val_<? echo $month.$yy; ?>" id="val_<? echo $month.$yy; ?>"  onkeyup="fn_calculate()" class="text_boxes_numeric" value="<? echo $cur_val;?>" style="width:85px;"/> </td> 

						<td width="100" align="right"><?=$prv_data_arr['qty'.$month.($yy-1)]; $tot_prv_qty+=$prv_data_arr['qty'.$month.($yy-1)]; ?></td>
						<td width="100" align="right"><?=$prv_data_arr['val'.$month.($yy-1)]; $tot_prv_val+=$prv_data_arr['val'.$month.($yy-1)]; ?></td>
                     </tr>
                    <?							
                    if ($month==12)
                    { 
						$t_month=0; $i=0; $t_year=$t_year+1;
					}
                }
                $k++;
            }	
            ?>									
            <tr>                       	 
                 <td>Total</td>
                 <td align="center"><input type="text" id="txt_total_qty" value="<? echo $tot_cur_qty;?>" readonly="readonly" class="text_boxes_numeric" style="width:85px;"/></td>
                 <td align="center"><input type="text" id="txt_total_val" value="<? echo $tot_cur_val;?>" readonly="readonly" class="text_boxes_numeric" style="width:85px;"/> </td>
                 <td align="right"><? echo $tot_prv_qty; ?></td>
                 <td align="right"><? echo $tot_prv_val; ?></td>
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


 