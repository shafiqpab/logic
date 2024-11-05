<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action']; 

if ($action=="load_drop_down_buyer")
{ 
	list($company,$type)=explode("_",$data);
	if($type==1)
	{
		echo create_drop_down( "cbo_customer_name", 100, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $company, "$load_function");
	}
	else
	{
		echo create_drop_down( "cbo_customer_name", 100, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", "", "" );
	}
	exit();	 
} 

	
if ($action=="load_drop_down_location") {	
	echo create_drop_down( 'cbo_location_name', 150, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", $selected, "" );
	exit();
}

if ($action=="load_drop_down_member")
{
	$data=explode("_",$data);
	$sql="select b.id,b.team_member_name  from lib_marketing_team a, lib_mkt_team_member_info b where a.id=b.team_id and   a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and a.project_type=3 and team_id='$data[0]'";
	echo create_drop_down( "cbo_team_member", 100, $sql,"id,team_member_name", 1, "-- Select Member --", $selected, "" );	
	exit();
}

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
	echo create_drop_down( "cbo_sub_section_id", 100, $trims_sub_section,"",1, "-- Select Sub-Section --","","",0,$subID,'','','','','',"");
	exit();
}

if($action=="company_wise_report_button_setting")
{
	extract($_REQUEST);

	$print_report_format=return_field_value("format_id"," lib_report_template","template_name ='".$data."'  and module_id=17 and report_id=185 and is_deleted=0 and status_active=1");
	
	//echo $print_report_format; disconnect($con); die;
	
	$print_report_format_arr=explode(",",$print_report_format);
	//print_r($print_report_format_arr);
	echo "$('#show').hide();\n";
	echo "$('#print_without_rate').hide();\n";
	echo "$('#summary').hide();\n";
	
	
	if($print_report_format != "")
	{
		foreach($print_report_format_arr as $id)
		{
			if($id==108){echo "$('#show').show();\n";}
			if($id==80){echo "$('#print_without_rate').show();\n";}
			if($id==149){echo "$('#summary').show();\n";}
			
		}
	}
	exit();	
}

if($action=="generate_report")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	$cbo_company_id=str_replace("'","", $cbo_company_id);
	$cbo_customer_source=str_replace("'","", $cbo_customer_source);
	$cbo_location_name=str_replace("'","", $cbo_location_name);
	$cbo_customer_name=str_replace("'","", $cbo_customer_name);
	$txt_order_no=str_replace("'","", $txt_order_no);
	$internal_no=str_replace("'","", $txt_internal_no);
	$cbo_section_id=str_replace("'","", $cbo_section_id);
	$cbo_sub_section_id=str_replace("'","", $cbo_sub_section_id);
	$txt_item_description=str_replace("'","", $txt_item_description);
	$cbo_date_category=str_replace("'","", $cbo_date_category);
	$txt_date_from=str_replace("'","", $txt_date_from);
	$txt_date_to=str_replace("'","", $txt_date_to);
	$cbo_delivery_status=str_replace("'","", $cbo_delivery_status);
	$cbo_customer_buyer=str_replace("'","", $cbo_customer_buyer);
	$cbo_team_leader=str_replace("'","", $cbo_team_leader);
	$cbo_team_member=str_replace("'","", $cbo_team_member);
	$cbo_wo_type_id=str_replace("'","", $cbo_wo_type_id);
	


	if($cbo_delivery_status== 0){$delivery_status_con="";}
	else
	{
		$delivery_status_con=" and c.delivery_status in ($cbo_delivery_status) and b.delivery_status in ($cbo_delivery_status)";
	}
		
	if($db_type==0) 
	{
		$txt_date_from=change_date_format($txt_date_from,'yyyy-mm-dd');
		$txt_date_to=change_date_format($txt_date_to,'yyyy-mm-dd');
	}
	else if($db_type==2) 
	{
		$txt_date_from=change_date_format($txt_date_from,'','',1);
		$txt_date_to=change_date_format($txt_date_to,'','',1);
	}
	
	$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
	$buyerArr = return_library_array("select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0 ","id","buyer_name");
	$trimsGroupArr = return_library_array("select id, item_name from lib_item_group where item_category=4 and status_active=1","id","item_name");
	$colorNameArr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0","id","color_name");
	$leaderArr = return_library_array("select id, team_leader_name from lib_marketing_team where status_active=1 and is_deleted=0 and project_type=3","id","team_leader_name");
	$memberArr = return_library_array("select id, team_member_name from lib_mkt_team_member_info","id","team_member_name");

	$currency_rate=return_field_value( "conversion_rate", "currency_conversion_rate", "currency=2 and status_active=1  and id in(select max(id) from currency_conversion_rate where currency=2 and is_deleted=0 and status_active=1)" , "conversion_rate" );
	
	if($txt_date_from!="" and $txt_date_to!="")
	{	
		if($cbo_date_category==1){$where_con.=" and a.receive_date between '$txt_date_from' and '$txt_date_to'";}	
		else if($cbo_date_category==2){$where_con.=" and a.delivery_date between '$txt_date_from' and '$txt_date_to'";}
		else if($cbo_date_category==3)
			{
				$delivery_date_con.=" and d.delivery_date between '$txt_date_from' and '$txt_date_to'";
				$delivery_date_con1.=" and a.delivery_date between '$txt_date_from' and '$txt_date_to'";
			}
		else if($cbo_date_category==4){$where_con_prod.=" and a.production_date between '$txt_date_from' and '$txt_date_to'";}
	}
	if($cbo_section_id){$where_con.=" and b.section='$cbo_section_id'";} 
	if($cbo_sub_section_id){$where_con.=" and b.sub_section='$cbo_sub_section_id'";} 
	if($cbo_customer_source){$where_con.=" and a.within_group='$cbo_customer_source'";} 
	if($cbo_customer_name){$where_con.=" and a.party_id='$cbo_customer_name'";} 
	if($cbo_customer_buyer){$where_con.=" and b.buyer_buyer='$cbo_customer_buyer'";} 
	if($cbo_team_leader){$where_con.=" and a.team_leader='$cbo_team_leader'";} 
	if($cbo_team_member){$where_con.=" and a.team_member='$cbo_team_member'";}
	if($cbo_wo_type_id){$where_con.=" and a.wo_type='$cbo_wo_type_id'";}
	if($cbo_location_name){$where_con.=" and a.location_id = $cbo_location_name";} 
	
	
	//echo $internal_no;
 	 $buyer_po_id_cond = '';
    if($cbo_customer_source==1 || $cbo_customer_source==0)
    {
         //$buyer_po_lib = return_library_array("select id, id as id2 from wo_po_break_down where grouping like '%$internal_no%'",'id','id2');
  		if($internal_no !="")
		{
			
			if($internal_no !="") $internal_no_cond = " and grouping like('%$internal_no%')";
			if($internal_no !="") $internal_no_cond2 = " and internal_ref like('%$internal_no%')";
			
			$buyer_po_arr=array();
			$buyer_po_id_arr=array();
			$po_sql ="Select id,grouping, job_no_mst from  wo_po_break_down  where is_deleted=0 and status_active=1 $internal_no_cond "; 
			$po_sql_res=sql_select($po_sql);
			foreach ($po_sql_res as $row)
			{
				$buyer_po_arr[$row[csf("id")]]['grouping']=$row[csf("grouping")];
				$buyer_po_arr[$row[csf("id")]]['job_no_mst']=$row[csf("job_no_mst")];
				$buyer_po_id_arr[]=$row[csf("id")];
			}
			unset($po_sql_res);
			
			
			$buyer_po_id = implode(",", $buyer_po_id_arr);
			if ($buyer_po_id=="")
			{
				echo "Not Found."; die;
			}
       	 	if($buyer_po_id !="") $buyer_po_id_cond = " and b.buyer_po_id in($buyer_po_id)";
  		}
    }
	//echo $buyer_po_id_cond; die;
	
	if($txt_order_no !="") $order_no_cond = " and b.order_no like('%$txt_order_no%')";
	
	//print_r($buyer_po_arr); die;
	
	
	$con = connect();
	$r_id=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (22001)");
	if($r_id)
	{
		oci_commit($con);
	}


	if($cbo_date_category == 4)
	{

		$trims_production_sql="select a.section_id,c.sub_section,a.received_id,a.job_id,b.job_dtls_id,c.id as order_receive_dtls_id,c.item_group_id,c.item_description,c.color_id,c.size_id,c.uom,c.job_quantity,b.production_qty,b.qc_qty,a.production_date  from trims_production_mst a,trims_production_dtls b,trims_job_card_dtls c where a.id=b.mst_id and c.id=b.job_dtls_id $where_con_prod $trimsjobid_cond $trimsreceiveid_cond and a.entry_form=269
		and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0"; 
		//echo $trims_production_sql; die;
		$trims_production_data_arr=array();	 $order_id_array = array();
		$result_trims_production_sql = sql_select($trims_production_sql);
		foreach($result_trims_production_sql as $row)
		{
			$key=$row[csf("received_id")].'*'.$row[csf("section_id")].'*'.$row[csf("sub_section")].'*'.$row[csf("item_group_id")].'*'.$row[csf("item_description")].'*'.$row[csf("color_id")].'*'.$row[csf("size_id")].'*'.$row[csf("uom")];
			
			$trims_production_data_arr[$key][qc_qty]+=$row[csf("qc_qty")];
			$trims_production_data_arr[$key][job_quantity]+=$row[csf("job_quantity")];
			$trims_production_data_arr[$key][production_date].=$row[csf("production_date")].',';
			$all_received_ids.=$row[csf("received_id")].',';
			$order_id_array[$row[csf('received_id')]] = $row[csf('received_id')];
		}

		$all_subcon_job_arr=array_unique(explode(",",(chop($all_received_ids,','))));
		
		
		
		
		if(count($order_id_array)>0)
		{
			fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 22001, 1,$order_id_array, $empty_arr);
		}
		
		//die;
		/*$con = connect();
		foreach($all_subcon_job_arr as $key=>$row_val)
		{
			//echo $row_val; die;
			$r_id2=execute_query("insert into tmp_job_no (userid, job_id, entry_form) values ($user_id,$row_val,2220)");
		}
		//print_r($issue_item_arr);
		//$wo_ids=implode(",",array_unique(explode(",",(chop($wo_id,',')))));
		if($db_type==0)
		{
			if($r_id2)
			{
				mysql_query("COMMIT");  
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			//echo $r_id2; die;
			if($r_id2)
			{
				oci_commit($con);  
			}
		} */


 
		$trims_order_sql="select a.id,a.subcon_job,a.receive_date,a.delivery_date,a.order_no as cust_order_no,a.party_id,a.within_group,a.subcon_job,a.currency_id,a.team_leader,a.team_member,b.item_group,b.buyer_buyer,b.section,b.sub_section,b.job_no_mst,b.order_no,b.booked_uom,b.order_uom,b.buyer_po_no,b.buyer_po_id,c.id as break_id,c.description,c.order_id,c.item_id,c.color_id,c.size_id ,c.qnty,c.rate ,c.amount,c.booked_qty,c.delivery_status, a.trims_ref, a.buyer_tb
		from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c ,GBL_TEMP_ENGINE d
		where a.id=b.mst_id  and b.id=c.mst_id and a.entry_form=255 and a.id=d.ref_val and b.mst_id=d.ref_val and d.entry_form=22001 and d.user_id=$user_id and d.ref_from=1 and a.company_id =$cbo_company_id  $buyer_po_id_cond  $order_no_cond $where_con $delivery_status_con
		and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and c.is_deleted=0 and c.status_active=1";
	}
	else if($cbo_date_category == 3)
	{
 		  $trims_order_sql="select a.id,a.subcon_job,a.receive_date,a.delivery_date,a.order_no as cust_order_no,a.party_id,a.within_group,a.subcon_job,a.currency_id, a.team_leader,a.team_member,b.item_group,b.buyer_buyer,b.section,b.sub_section,b.job_no_mst,b.order_no,b.booked_uom,b.order_uom,b.buyer_po_no, b.buyer_po_id,c.id as break_id,c.description,c.order_id,c.item_id,c.color_id,c.size_id ,c.qnty,c.rate ,c.amount,c.booked_qty,c.delivery_status, a.trims_ref, a.buyer_tb from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c , trims_delivery_mst d, trims_delivery_dtls e  where a.id=b.mst_id  and b.id=c.mst_id and b.id=e.receive_dtls_id and d.id=e.mst_id and  a.entry_form=255  and a.company_id =$cbo_company_id  $buyer_po_id_cond  $order_no_cond $where_con $delivery_status_con $delivery_date_con
		and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and c.is_deleted=0 and c.status_active=1";  
	}
	else
	{
 		$trims_order_sql="select a.id,a.subcon_job,a.receive_date,a.wo_type ,a.delivery_date,a.order_no as cust_order_no,a.party_id,a.within_group,a.subcon_job,a.currency_id,a.team_leader,a.team_member,b.item_group,b.buyer_buyer,b.section,b.sub_section,b.job_no_mst,b.order_no,b.booked_uom,b.order_uom,b.buyer_po_no,b.buyer_po_id,c.id as break_id,c.description,c.order_id,c.item_id,c.color_id,c.size_id ,c.qnty,c.rate ,c.amount,c.booked_qty,c.delivery_status, a.trims_ref, a.buyer_tb
		 from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c 
		where a.id=b.mst_id  and b.id=c.mst_id and a.entry_form=255 and a.company_id =$cbo_company_id  $buyer_po_id_cond  $order_no_cond $where_con $delivery_status_con
		and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and c.is_deleted=0 and c.status_active=1";



	

	}



	
	 //echo $trims_order_sql;die;
	$trims_receive_id_arr=array();
	$trims_data_arr=array();	
	$result_trims_order_sql = sql_select($trims_order_sql);
	foreach($result_trims_order_sql as $row)
	{
		$key=$row[csf("id")].'*'.$row[csf("order_id")].'*'.$row[csf("section")].'*'.$row[csf("sub_section")].'*'.$row[csf("item_group")].'*'.$row[csf("item_id")].'*'.$row[csf("description")].'*'.$row[csf("color_id")].'*'.$row[csf("size_id")].'*'.$row[csf("booked_uom")].'*'.$row[csf("rate")].'*'.$row[csf("wo_type")];
		
		$trims_data_arr[$key][qnty]+=$row[csf("qnty")];
		$trims_data_arr[$key][amount]+=$row[csf("amount")];
		$trims_data_arr[$key][booked_qty]+=$row[csf("booked_qty")];
		$trims_data_arr[$key][break_ids].=$row[csf("break_id")].',';
		
		$date_array[$key]=array(
		subcon_job=>$row[csf("subcon_job")],
		item_group=>$row[csf("item_group")],
		receive_date=>$row[csf("receive_date")],
		delivery_date=>$row[csf("delivery_date")],
		order_qty=>$trims_data_arr[$key][qnty],
		order_rate=>$row[csf("rate")],
		order_amount=>$trims_data_arr[$key][amount],
		booked_qty=>$trims_data_arr[$key][booked_qty],
		cust_order_no=>$row[csf("cust_order_no")],
		party_id=>$row[csf("party_id")],
		within_group=>$row[csf("within_group")],
		buyer_buyer=>$row[csf("buyer_buyer")],
		section=>$row[csf("section")],
		sub_section=>$row[csf("sub_section")],
		booked_uom=>$row[csf("booked_uom")],
		order_uom=>$row[csf("order_uom")],
		description=>$row[csf("description")],
		delivery_status=>$row[csf("delivery_status")],
		order_no=>$row[csf("order_no")],
		buyer_po_no=>$row[csf("buyer_po_no")],
		currency_id=>$row[csf("currency_id")],
		team_leader=>$row[csf("team_leader")],
		team_member=>$row[csf("team_member")],
		break_ids=>$trims_data_arr[$key][break_ids],
		buyer_po_id=>$row[csf("buyer_po_id")],
		trims_ref=>$row[csf("trims_ref")],
		buyer_tb=>$row[csf("buyer_tb")],
		wo_type=>$row[csf("wo_type")],
		
		);
		$trims_receive_id_arr[$row[csf("id")]]=$row[csf("id")];
		$trims_buyer_po_id_arr[$row[csf("buyer_po_id")]]=$row[csf("buyer_po_id")];
	}
	
	//print_r($trims_buyer_po_id_arr);
	    if(count($trims_buyer_po_id_arr)>0)
		{
			fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 22001, 5,$trims_buyer_po_id_arr, $empty_arr);
		}
	
	//die;
	if($cbo_customer_source==1 || $cbo_customer_source==0)
    {
 			$buyer_po_arr=array();
			$buyer_po_id_arr=array();
			  $po_sql ="Select a.id,a.grouping,a.job_no_mst from  wo_po_break_down a,GBL_TEMP_ENGINE d  where a.is_deleted=0 and a.status_active=1 and a.id=d.ref_val and d.entry_form=22001 and d.user_id=$user_id and d.ref_from=5 "; 
			$po_sql_res=sql_select($po_sql);
			foreach ($po_sql_res as $row)
			{
				$buyer_po_arr[$row[csf("id")]]['grouping']=$row[csf("grouping")];
				$buyer_po_arr[$row[csf("id")]]['job_no_mst']=$row[csf("job_no_mst")];
				//$buyer_po_id_arr[]=$row[csf("id")];
			}
			unset($po_sql_res);
     }
	
	//die;
	    
		if(count($trims_receive_id_arr)>0)
		{
			fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 22001, 2,$trims_receive_id_arr, $empty_arr);
		}
		
	
	
	/*$trims_receive_id=implode(',',$trims_receive_id_arr);
	if($trims_receive_id!='')
	{
		$trimsreceiveid=chop($trims_receive_id,','); 
		$trimsreceiveid_cond="";
		$trimsreceive_ids=count(array_unique(explode(",",$trimsreceiveid)));
		if($db_type==2 && $trimsreceive_ids>1000)
		{
			$trimsreceiveid_cond=" and (";
			$trimsreceiveidArr=array_chunk(explode(",",$trimsreceiveid),999);
			foreach($trimsreceiveidArr as $ids)
			{
				$ids=implode(",",$ids);
				$trimsreceiveid_cond.=" a.received_id in($ids) or"; 
			}
			$trimsreceiveid_cond=chop($trimsreceiveid_cond,'or ');
			$trimsreceiveid_cond.=")";
		}
		else
		{
			if($trimsreceiveid!="")
			{
				$issue_ids=implode(",",array_unique(explode(",",$trimsreceiveid)));
				$trimsreceiveid_cond=" and a.received_id in($issue_ids)";
			}
			else { $trimsreceiveid_cond ="";}
		}
	}*/
		
	//Job-------------------------------	
	  $trims_job_sql="select a.id,a.trims_job,a.received_no from trims_job_card_mst a ,GBL_TEMP_ENGINE d where a.status_active=1 and a.is_deleted=0 and a.received_id=d.ref_val and d.entry_form=22001 and d.user_id=$user_id and d.ref_from=2";   
	$trims_job_no_arr=array();	
	$result_trims_job_sql = sql_select($trims_job_sql);
	foreach($result_trims_job_sql as $row)
	{
		$trims_job_no_arr[$row[csf("received_no")]]=$row[csf("trims_job")];
		
		if($row[csf("id")])
		{
			$trims_job_id_arr[$row[csf("id")]]=$row[csf("id")];
		}
	}
		//$trims_job_id=implode(',',$trims_job_id_arr);
 	   if(count($trims_job_id_arr)>0)
		{
			fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 22001, 3,$trims_job_id_arr, $empty_arr);
		}
 	 
	//$trims_job_id=implode(',',$trims_job_id_arr);
	/*if($trims_job_id!='')
	{
		$trimsjobid=chop($trims_job_id,','); 
		$trimsjobid_cond="";
		$trimsjobids=count(array_unique(explode(",",$trimsjobid)));
		
		if($db_type==2 && $trimsjobids>1000)
		{
			$trimsjobid_cond=" and (";
			$trimsreceiveidArr=array_chunk(explode(",",$trimsjobid),999);
			foreach($trimsreceiveidArr as $jobids)
			{
				$jobids=implode(",",$jobids);
				$trimsjobid_cond.=" a.job_id in($jobids) or"; 
			}
			$trimsjobid_cond=chop($trimsjobid_cond,'or ');
			$trimsjobid_cond.=")";
		}
		else
		{
			if($trimsjobid!="")
			{
				$issue_ids=implode(",",array_unique(explode(",",$trimsjobid)));
				$trimsjobid_cond=" and a.job_id in($issue_ids)";
			}
			else { $trimsjobid_cond ="";}
		}
	}*/
		
	if($cbo_date_category != 4)
	{
		$trims_production_sql="select a.section_id,c.sub_section,a.received_id,a.job_id,b.job_dtls_id,c.id as order_receive_dtls_id,c.item_group_id,c.item_description,c.color_id,c.size_id,c.uom,c.job_quantity,b.production_qty,b.qc_qty,a.production_date  from trims_production_mst a,trims_production_dtls b,trims_job_card_dtls c,GBL_TEMP_ENGINE d where a.id=b.mst_id and c.id=b.job_dtls_id  and a.entry_form=269
		and a.job_id=d.ref_val and d.entry_form=22001 and d.user_id=$user_id and d.ref_from=3  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0"; 
		// echo $trims_production_sql; die;
		$trims_production_data_arr=array();	
		$result_trims_production_sql = sql_select($trims_production_sql);
		foreach($result_trims_production_sql as $row)
		{
			$key=$row[csf("received_id")].'*'.$row[csf("section_id")].'*'.$row[csf("sub_section")].'*'.$row[csf("item_group_id")].'*'.$row[csf("item_description")].'*'.$row[csf("color_id")].'*'.$row[csf("size_id")].'*'.$row[csf("uom")];
			
			$trims_production_data_arr[$key][qc_qty]+=$row[csf("qc_qty")];
			$trims_production_data_arr[$key][job_quantity]+=$row[csf("job_quantity")];
			$trims_production_data_arr[$key][production_date].=$row[csf("production_date")].',';
		}
	}

	$trims_delivery_sql="select a.delivery_date,a.received_id,b.delevery_qty,b.order_receive_rate,c.booked_uom as uom,b.section,c.sub_section,b.item_group,b.color_id,b.size_id,b.description,b.delevery_status,b.break_down_details_id,c.id from trims_delivery_mst a, trims_delivery_dtls b, subcon_ord_dtls c,GBL_TEMP_ENGINE d where a.id=b.mst_id and c.id=b.receive_dtls_id and a.received_id=d.ref_val and d.entry_form=22001 and d.user_id=$user_id and d.ref_from=2 $delivery_date_con1 and a.entry_form=208 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by a.delivery_date"; 

	 //echo $trims_delivery_sql; die;
	$trims_delivery_data_arr=array();	
	$result_trims_delivery_sql = sql_select($trims_delivery_sql);
	foreach($result_trims_delivery_sql as $row)
	{
		$key=$row[csf("received_id")].'*'.$row[csf("section")].'*'.$row[csf("sub_section")].'*'.$row[csf("item_group")].'*'.$row[csf("description")].'*'.$row[csf("color_id")].'*'.$row[csf("size_id")].'*'.$row[csf("uom")];
		//c.id 
		
		$trims_delivery_data_arr[$key][delevery_qty]+=$row[csf("delevery_qty")];
		$trims_delivery_data_arr[$key][delevery_val]+=$row[csf("delevery_qty")]*$row[csf("order_receive_rate")];
		$trims_delivery_data_arr[$key][delevery_last_date]=$row[csf("delivery_date")];
		$trims_delivery_data_arr[$key][id]=$row[csf("id")];
		$trims_delivery_data_arr[$key][delevery_status]=$row[csf("delevery_status")];
		$trims_delivery_data_arr[$key][break_ids].=$row[csf("break_down_details_id")].',';
	}
	


	//$trims_bill_sql="select c.mst_id as received_id,c.section,c.sub_section,b.item_description,b.color_id,b.size_id,c.booked_uom as uom ,total_delv_qty,b.quantity,b.bill_amount,b.id from trims_bill_mst d, trims_bill_dtls b, subcon_ord_dtls c, subcon_ord_mst a,trims_delivery_dtls e where d.id=b.mst_id and c.mst_id=a.id and b.job_dtls_id = e.job_dtls_id  and b.ITEM_DESCRIPTION = e.DESCRIPTION and b.COLOR_ID = e.COLOR_ID and b.SIZE_ID = e.SIZE_ID   and c.mst_id = e.RECEIVED_ID and c.id=e.receive_dtls_id  and   c.order_no=b.order_no and d.entry_form=276 and a.entry_form=255 and d.status_active=1 and d.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by c.mst_id,c.section,c.sub_section,b.item_description,b.color_id,b.size_id,c.booked_uom,total_delv_qty,b.quantity,b.bill_amount,b.id";
	//// 

	$trims_bill_sql="select c.mst_id as received_id,c.section,c.sub_section,c.item_group,b.item_description,b.color_id,b.size_id,c.booked_uom as uom ,total_delv_qty,b.quantity,b.bill_amount,b.id from trims_bill_mst d, trims_bill_dtls b, subcon_ord_dtls c, subcon_ord_mst a,GBL_TEMP_ENGINE f where d.id=b.mst_id and c.mst_id=a.id and c.order_no=b.order_no and a.id=f.ref_val and f.entry_form=22001 and f.user_id=$user_id and f.ref_from=2 and d.entry_form=276 and a.entry_form=255 and d.status_active=1 and d.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 ";





	$trims_bill_data_arr=array();	
	$result_trims_bill_sql = sql_select($trims_bill_sql);
	foreach($result_trims_bill_sql as $row)
	{
		$key=$row[csf("received_id")].'*'.$row[csf("section")].'*'.$row[csf("sub_section")].'*'.$row[csf("item_group")].'*'.$row[csf("item_description")].'*'.$row[csf("color_id")].'*'.$row[csf("size_id")].'*'.$row[csf("uom")];
		
		$trims_bill_data_arr[$key][bill_qty]+=$row[csf("quantity")];
		$trims_bill_data_arr[$key][bill_val]+=$row[csf("bill_amount")];
		$trims_bill_data_arr[$key][dtls_ids].=$row[csf("id")].',';
	}
	//echo "<pre>";
	//print_r($trims_bill_data_arr);
	$width=4100;
	ob_start();
	
	  $r_id=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (22001)");
		if($r_id)
		{
			oci_commit($con);
		}
		disconnect($con);
	?>
	<div align="center" style="height:auto; width:<? echo $width+20;?>px; margin:0 auto; padding:0;">
		<table width="<? echo $width;?>" cellpadding="0" cellspacing="0" id="caption" align="center">
			<thead class="form_caption" >
				<tr>
					<td colspan="36" align="center" style="font-size:20px;"><? echo $companyArr[$cbo_company_id]; ?></td>
				</tr>
				<tr>
					<td colspan="36" align="center" style="font-size:14px; font-weight:bold" ><? echo $report_title; ?></td>
				</tr>
				<tr>
					<td colspan="36" align="center" style="font-size:14px; font-weight:bold">
						<? echo " From : ".$txt_date_from." To : ". $txt_date_to ;?>
					</td>
				</tr>
			</thead>
		</table>
        <table align="left">
            <tr>
                <td bgcolor="#FFF" width="15">&nbsp;</td><td>Full Pending</td>
                <td bgcolor="#FFCC66" width="15">&nbsp;</td><td>Partial Deliverd</td>
                <td bgcolor="#8CD59C" width="15">&nbsp;</td><td>Full Deliverd</td>
          	</tr>
        </table>
        <table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="<? echo $width;?>" rules="all" id="rpt_table_header" align="left">
			<thead>
                <th width="35">SL</th>
                <th width="120">Order Rcv. No</th>
				<th width="80">WO Type</th>
                <th width="60">Order Source</th>
                <th width="100">Job Card No</th>
                <th width="100">Main Job</th>
                <th width="100">Cust. WO No</th>
                <th width="100">Customer Name</th>
                <th width="100"> Buyers TB</th>
                <th width="100">Cust. Buyer</th>
                <th width="100">Cust. Order No</th>
                <th width="100">Internal Ref</th>
                <th width="100">Trims Ref</th>
                <th width="60">Section</th>
                <th width="100">Sub-Section</th>
                <th width="100">Trims Group</th>
                <th width="100">Item Description</th>
                <th width="100">Color</th>
                <th width="60">Order UOM</th>
                <th width="100">Order Qty</th>
                <th width="60">Booked UOM</th>
                <th width="100">Booked Qty</th>
                <th width="100">Order Rate ($)</th>
                <th width="100">Order Amount ($)</th>
                <th width="80">Order Rcv.Date</th>
                <th width="80">Target Delv. Date</th>
                <th width="80">Production Date</th>
                <th width="80">Last Delv. Date</th>
                <th width="100">Delv.Status</th>
                <th width="100">Production Qty</th>
                <th width="100">Prod Bal Qty</th>
                <th width="100">Delv.Qty</th>
                <th width="100">Delv.Amount($)</th>
                <th width="100">Delv. Balance Qty</th>
                <th width="100">Delv. Balance Amount($)</th>
                <th width="100">Short Delv Qty</th>
                <th width="100">Short Delv Value</th>
                <th width="100">Bill Qty</th>
                <th width="100">Bill Amount</th>
                <th width="100">Bill Bal. Qty</th>
                <th width="100">Bill Bal. Amnt ($)</th>
                <th width="100">Team Leader</th>
                <th width="100">Mkt. by.</th>
                <th>Remarks</th>
			</thead>
		</table>
		<div style="width:<? echo $width+18;?>px; max-height:250px; float:left; overflow-y:scroll;" id="scroll_body">
		<table border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body_id" width="<? echo $width;?>" rules="all" align="left">
            <? 
			$i=1;
			$total_order_qty=0;$total_order_val=0;$total_booked_qty=0;$total_production_qty=0;
			foreach($date_array as $keysss=>$row)
			{
				list($id,$order_id,$section,$sub_section,$item_group,$item_id,$description,$color_id,$size_id,$booked_uom,$rate)=explode('*',$keysss);
				$key=$id.'*'.$section.'*'.$sub_section.'*'.$item_group.'*'.$description.'*'.$color_id.'*'.$size_id.'*'.$booked_uom; //.'*'.$item_group.'*'.
				

				//echo $key; die;
				//$production_qty_on_order_parcent=($trims_production_data_arr[$key][qc_qty]/$trims_production_data_arr[$key][job_quantity])*$row[order_qty];
				$production_qty_on_order_parcent=$trims_production_data_arr[$key][qc_qty];
				$production_date=$trims_production_data_arr[$key][production_date];
				$production_date=implode("<br>",array_unique(explode(",",chop($production_date,','))));

				//echo $production_qty_on_order_parcent."__"; die;
				
				//WORK ORDER NO : 161
				$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
				$party=($row[within_group]==1)?$companyArr[$row[party_id]]:$buyerArr[$row[party_id]];
				$buyer_buyer=($row[within_group]==1)?$buyerArr[$row[buyer_buyer]]:$row[buyer_buyer];
			
				//$row[delivery_status]=$trims_delivery_data_arr[$item_group.'*'.$key][delevery_status];
				
				if($row[delivery_status]==2){$bgcolor="#FFCC66";}
				elseif($row[delivery_status]==3){$bgcolor="#8CD59C";}
				else{$row[delivery_status]=1;}
			
			//---------------------------------------
			$total_order_qty+=$row[order_qty];
			$total_order_val+=$row[order_amount];
			$total_booked_qty+=$row[booked_qty];
			$total_production_qty+=$production_qty_on_order_parcent;
			
			if($row["currency_id"]==1){
				$row[order_rate]=$row[order_rate]/$currency_rate;
				$row[order_amount]=$row[order_amount]/$currency_rate;
			}

			$trims_delivery_data_arr[$key][delevery_qty]+=$row[csf("delevery_qty")];
			$trims_delivery_data_arr[$key][delevery_val]+=$row[csf("delevery_qty")]*$row[csf("order_receive_rate")];
			$trims_delivery_data_arr[$key][delevery_last_date]=$row[csf("delivery_date")];
			$trims_delivery_data_arr[$key][id]=$row[csf("id")];
			$trims_delivery_data_arr[$key][delevery_status]=$row[csf("delevery_status")];
			$trims_delivery_data_arr[$key][break_ids].=$row[csf("break_down_details_id")].',';
			
			/*if($cbo_delivery_status!=0 && ($trims_delivery_data_arr[$item_group.'*'.$key][delevery_status] == $cbo_delivery_status))
			{*/
				$delivery_amt=number_format($row[order_rate]*$trims_delivery_data_arr[$key][delevery_qty],2);
				$delivery_qty=number_format($trims_delivery_data_arr[$key][delevery_qty],2);
				$DelvBalanceQty=$row[order_qty]-$trims_delivery_data_arr[$key][delevery_qty];
			/*}*/

			
			$bill_amt=number_format($row[order_rate]*$trims_bill_data_arr[$key][bill_qty],2);
			$receive_ids=implode(',',$trims_receive_id_arr);
			if($row["within_group"]==1) $orderSource='Internal'; else $orderSource='External';
			?>
            <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                <td width="35" align="center"><? echo $i;?></td>
                <td width="120" align="center"><p><? echo $row[subcon_job];?></p></td>
				<td width="80" align="center"><p><? echo $wo_type_arr[$row[wo_type]];?></p></td>
                <td width="60" align="center"><p><? echo $orderSource;?></p></td>
                <td width="100" align="center"><p><? echo $trims_job_no_arr[$row[subcon_job]];?></p></td>
                <td width="100" align="center"><p><? echo $buyer_po_arr[$row[buyer_po_id]]['job_no_mst'];?></p></td>
                <td width="100" align="center"><p><? echo $row[cust_order_no];?></p></td>
                <td width="100"><p><? echo $party;?></p></td>
                <td width="100"><p><? echo  $row["buyer_tb"];?></p></td>
                <td width="100"><p><? echo $buyer_buyer;?></p></td>
                <td width="100"><p><? echo $row[buyer_po_no];?></p></td>
                <td width="100"><p><? echo $buyer_po_arr[$row[buyer_po_id]]['grouping']; //$row[buyer_po_id];?></p></td>
                <td width="100"><p><?php echo $row[trims_ref]; ?></p></td>
                <td width="60"><p><? echo $trims_section[$row[section]];?></p></td>
                <td width="100"><p><? echo $trims_sub_section[$row[sub_section]];?></p></td>
                <td width="100"><p><? echo $trimsGroupArr[$row[item_group]];?></p></td>
                <td width="100"><p><? echo $row[description];?></p></td>
                <td width="100"><p><? echo $colorNameArr[$color_id];?></p></td>
                <td width="60" align="center"><? echo $unit_of_measurement[$row[order_uom]];?></td>
                <td width="100" align="right"><? echo number_format($row[order_qty],0); ?></td>
                <td width="60" align="center"><? echo $unit_of_measurement[$row[booked_uom]];?></td>
                <td width="100" align="right"><? echo number_format($row[booked_qty],0);?></td>
                <td width="100" align="right"><? echo number_format($row[order_rate],4);?></td>
                <td width="100" align="right"><? echo number_format($row[order_amount],2);?></td>
                <td width="80" align="center"><? echo $row[receive_date];?></td>
                <td width="80" align="center"><? echo $row[delivery_date];?></td>
                <td width="80" align="center"><? echo $production_date;?></td>
                <td width="80" align="center"><? echo $trims_delivery_data_arr[$key][delevery_last_date];?></td>
                <td width="100" align="center"><? echo $delivery_status[$row[delivery_status]];?></td>
                <td width="100" align="right"><? echo number_format($production_qty_on_order_parcent,0);?></td>
                <td width="100" align="right"><? 
				//echo number_format($row[order_qty]-$production_qty_on_order_parcent,0);
				echo number_format($row[booked_qty]-$production_qty_on_order_parcent,0);
				?></td>
                <td width="100" align="right"><? echo $delivery_qty;?></td>
                <?
                if($delivery_amt>0){
                	?><td width="100" align="right"><a href="##" onclick="fnc_amount_details('<? echo $trims_delivery_data_arr[$key][break_ids];?>','<? echo $row[order_rate];?>','delivery_popup')"><p><? echo $delivery_amt;?></p></a> </td><?
                }else{
                	?><td width="100" align="right"><? echo '0'; ?></td><?
                }?>
                <td width="100" align="right"><?
				//$DelvBalanceQty=number_format($row[order_qty]-$trims_delivery_data_arr[$item_group.'*'.$key][delevery_qty],0);
				
				if($DelvBalanceQty<=1){
				 	echo $DelvBalanceQty=0;
				}else{
					echo $DelvBalanceQty=number_format($row[order_qty]-$trims_delivery_data_arr[$key][delevery_qty],0);
				}
				// echo number_format($row[order_qty],0)-number_format($trims_delivery_data_arr[$key][delevery_qty],0);
				//echo number_format($row[booked_qty]-$trims_delivery_data_arr[$key][delevery_qty],0);
				?></td>
                <td width="100" align="right"><? echo number_format(($row[order_amount]-$row[order_rate]*$trims_delivery_data_arr[$key][delevery_qty]),2);?></td>
                <td width="100" align="right"><? //echo number_format($row[order_qty]-$trims_delivery_data_arr[$key][delevery_qty],2);?></td>
                <td width="100" align="right"><? //echo number_format($row[order_amount]-$trims_delivery_data_arr[$key][delevery_val]);?></td>
                <td width="100" align="right"><? echo $trims_bill_data_arr[$key][bill_qty];?></td>
                <?
                if($bill_amt>0){
                	?><td width="100" align="right"><a href="##" onclick="fnc_amount_details('<? echo chop($trims_bill_data_arr[$key][dtls_ids],',');?>','<? echo $row[order_rate];?>','bill_popup')"><p><? echo $bill_amt;?></p></a> </td><?
                }else{
                	?><td width="100" align="right"><? echo '0'; ?></td><?
                }?>
                
                <td width="100" align="right"><? echo number_format($trims_delivery_data_arr[$key][delevery_qty]-$trims_bill_data_arr[$key][bill_qty]);?></td>
                <td width="100" align="right"><? echo number_format(($trims_delivery_data_arr[$key][delevery_qty]-$trims_bill_data_arr[$key][bill_qty])*$row[order_rate],2); //number_format($row[order_rate]*$trims_delivery_data_arr[$item_group.'*'.$key][delevery_qty]-$trims_bill_data_arr[$key][bill_qty],2)//number_format($trims_delivery_data_arr[$item_group.'*'.$key][delevery_val]-$trims_bill_data_arr[$key][bill_val],2);?></td>
                <td width="100"><p><? echo $leaderArr[$row[team_leader]];?></p></td>
                <td width="100"><p><? echo $memberArr[$row[team_member]];?></p></td>
                <td></td>
            </tr>
            <? 
			$i++;
			} ?>
		</table>
		</div>
		<table width="<? echo $width;?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body_footer" align="left">
			<tfoot>
                <th width="35"></th>
                <th width="120"></th>
				<th width="80"></th>
                <th width="60"></th>
                <th width="100"></th>
                <th width="100"></th>
                <th width="100"></th>
                <th width="100"></th>
                <th width="100"> </th>
                <th width="100"> </th>
                <th width="100"></th>
                <th width="100"></th>
                <th width="100"></th>
                <th width="60"></th>
                <th width="100"></th>
                <th width="100"></th>
                <th width="100"></th>
                <th width="100" align="right"><? //echo number_format($total_order_qty);?></th>
                <th width="60"></th>
                <th width="100"></th>
                <th width="60" align="right"><? //echo number_format($total_booked_qty);?></th>
                <th width="100"></th>
                <th width="100" align="right"><? //echo number_format($total_order_val,2);?></th>
                <th width="100" align="right"><? //echo number_format($total_order_val,2);?></th>
                <th width="80"></th>
                <th width="80"></th>
                <th width="80"></th>
                <th width="80"></th>
                <th width="100"></th>
                <th width="100" align="right"><? //echo number_format($total_production_qty,2);?></th>
                <th width="100"><!--Prod Bal Qty--></th>
                <th width="100" align="right"><? //echo $total_delivery_qty;?></th>
                <th width="100"><!--Delv.Amount--></th>
                <th width="100"><!--Delv. Balance Qty--></th>
                <th width="100"><!--Delv. Balance Amount--></th>
                <th width="100"><!--Short Delv Qty--></th>
                <th width="100"><!--Short Delv Value--></th>
                <th width="100"><!--Bill Qty--></th>
                <th width="100"><!--Bill Amount--></th>
                <th width="100"><!--Bill Bal. Qty--></th>
                <th width="100"><!--Bill Bal. Amnt--></th>
                <th width="100"><!--Leader--></th>
                <th width="100"><!--Member--></th>
                <th>&nbsp;</th>
			</tfoot>
		</table>
    </div>
    <?
    if($cbo_date_category == 4){
	    $r_id3=execute_query("delete from tmp_job_no where userid=$user_id and entry_form=2220");
		if($db_type==0)
		{
			if($r_id3)
			{
				mysql_query("COMMIT");  
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($r_id3)
			{
				oci_commit($con);  
			}
		}
	}


    $html = ob_get_contents();
    ob_clean();
    //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
    foreach (glob("*.xls") as $filename) {
    //if( @filemtime($filename) < (time()-$seconds_old) )
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, $html);
    echo "$html**$filename**$report_type";
    exit();
}

if($action=="generate_report_2")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	$cbo_company_id=str_replace("'","", $cbo_company_id);
	$cbo_customer_source=str_replace("'","", $cbo_customer_source);
	$cbo_customer_name=str_replace("'","", $cbo_customer_name);
	$txt_order_no=str_replace("'","", $txt_order_no);
	$internal_no=str_replace("'","", $txt_internal_no);
	$cbo_section_id=str_replace("'","", $cbo_section_id);
	$cbo_sub_section_id=str_replace("'","", $cbo_sub_section_id);
	$txt_item_description=str_replace("'","", $txt_item_description);
	$cbo_date_category=str_replace("'","", $cbo_date_category);
	$txt_date_from=str_replace("'","", $txt_date_from);
	$txt_date_to=str_replace("'","", $txt_date_to);
	$cbo_delivery_status=str_replace("'","", $cbo_delivery_status);
	$cbo_customer_buyer=str_replace("'","", $cbo_customer_buyer);
	$cbo_team_leader=str_replace("'","", $cbo_team_leader);
	$cbo_team_member=str_replace("'","", $cbo_team_member);
	$cbo_location_name=str_replace("'","", $cbo_location_name);



	if($cbo_delivery_status== 0){$delivery_status_con="";}
	else{
		$delivery_status_con=" and c.delivery_status in ($cbo_delivery_status) and b.delivery_status in ($cbo_delivery_status)";
	}
		
	if($db_type==0) 
	{
		$txt_date_from=change_date_format($txt_date_from,'yyyy-mm-dd');
		$txt_date_to=change_date_format($txt_date_to,'yyyy-mm-dd');
	}
	else if($db_type==2) 
	{
		$txt_date_from=change_date_format($txt_date_from,'','',1);
		$txt_date_to=change_date_format($txt_date_to,'','',1);
	}
	
	$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
	$buyerArr = return_library_array("select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0 ","id","buyer_name");
	$trimsGroupArr = return_library_array("select id, item_name from lib_item_group where item_category=4 and status_active=1","id","item_name");
	$colorNameArr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0","id","color_name");
	$leaderArr = return_library_array("select id, team_leader_name from lib_marketing_team where status_active=1 and is_deleted=0 and project_type=3","id","team_leader_name");
	$memberArr = return_library_array("select id, team_member_name from lib_mkt_team_member_info","id","team_member_name");

	$currency_rate=return_field_value( "conversion_rate", "currency_conversion_rate", "currency=2 and status_active=1  and id in(select max(id) from currency_conversion_rate where currency=2 and is_deleted=0 and status_active=1)" , "conversion_rate" );
	
	if($txt_date_from!="" and $txt_date_to!=""){	
		if($cbo_date_category==1){$where_con.=" and a.receive_date between '$txt_date_from' and '$txt_date_to'";}	
		else if($cbo_date_category==2){$where_con.=" and a.delivery_date between '$txt_date_from' and '$txt_date_to'";}
	}
	if($cbo_section_id){$where_con.=" and b.section='$cbo_section_id'";} 
	if($cbo_sub_section_id){$where_con.=" and b.sub_section='$cbo_sub_section_id'";} 
	if($cbo_customer_source){$where_con.=" and a.within_group='$cbo_customer_source'";} 
	if($cbo_customer_name){$where_con.=" and a.party_id='$cbo_customer_name'";} 
	if($cbo_customer_buyer){$where_con.=" and b.buyer_buyer='$cbo_customer_buyer'";} 
	if($cbo_team_leader){$where_con.=" and a.team_leader='$cbo_team_leader'";} 
	if($cbo_team_member){$where_con.=" and a.team_member='$cbo_team_member'";} 
	if($cbo_location_name){$where_con.=" and a.location_id = $cbo_location_name";}	
	
	
	//echo $internal_no;
 	$buyer_po_id_cond = '';
    if($cbo_customer_source==1 || $cbo_customer_source==0)
    {
		if($internal_no !="") $internal_no_cond = " and grouping like('%$internal_no%')";
		
		$buyer_po_arr=array();
		$buyer_po_id_arr=array();
		 $po_sql ="Select id,grouping from  wo_po_break_down  where is_deleted=0 and status_active=1 $internal_no_cond "; 
		$po_sql_res=sql_select($po_sql);
		foreach ($po_sql_res as $row)
		{
			$buyer_po_arr[$row[csf("id")]]['grouping']=$row[csf("grouping")];
			$buyer_po_id_arr[]=$row[csf("id")];
		}
		unset($po_sql_res);
        //$buyer_po_lib = return_library_array("select id, id as id2 from wo_po_break_down where grouping like '%$internal_no%'",'id','id2');
		if($internal_no !="")
		{
			$buyer_po_id = implode(",", $buyer_po_id_arr);
			if ($buyer_po_id=="")
			{
				echo "Not Found."; die;
			}
       	 if($buyer_po_id !="") $buyer_po_id_cond = " and b.buyer_po_id in($buyer_po_id)";
		 
		 
		/*$trims_buyer_po_id=implode(",", $buyer_po_id_arr);
		if($trims_buyer_po_id!='')
		{
			
			$trimsbuyerpoid=chop($trims_buyer_po_id,','); 
			$buyer_po_id_cond="";
			$trimsbuyerpoids=count(array_unique(explode(",",$trimsbuyerpoid)));
			if($db_type==2 && $trimsbuyerpoids>1000)
			{
				$buyer_po_id_cond=" and (";
				$trimsbuyerpoidArr=array_chunk(explode(",",$trimsbuyerpoid),999); 
				foreach($trimsbuyerpoidArr as $bpoids)
				{
					$bpoids=implode(",",$bpoids);
					$buyer_po_id_cond.=" b.buyer_po_id in($bpoids) or"; 
				}
				$buyer_po_id_cond=chop($buyer_po_id_cond,'or ');
				$buyer_po_id_cond.=")";
			}
			else
			{
				if($trimsbuyerpoid!="")
				{
					$bpoids=implode(",",array_unique(explode(",",$trimsbuyerpoid)));
					$buyer_po_id_cond=" and b.buyer_po_id in($bpoids)";
				}
				else { $buyer_po_id_cond ="";}
			}
		
		}*/
		}
    }
	//echo $buyer_po_id_cond; die;
	
	if($txt_order_no !="") $order_no_cond = " and b.order_no like('%$txt_order_no%')";
	
	//print_r($buyer_po_arr); die;
	
	/*$trims_order_sql="select a.id,a.subcon_job,a.receive_date,a.delivery_date,a.order_no as cust_order_no,a.party_id,a.within_group,a.subcon_job,a.currency_id,a.team_leader,a.team_member,b.item_group,b.buyer_buyer,b.section,b.sub_section,b.job_no_mst,b.order_no,b.booked_uom,b.order_uom,b.buyer_po_no,b.buyer_po_id,c.id as break_id,c.description,c.order_id,c.item_id,c.color_id,c.size_id ,c.qnty,c.rate ,c.amount,c.booked_qty,c.delivery_status from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c where a.subcon_job=b.job_no_mst and b.job_no_mst=c.job_no_mst and b.id=c.mst_id and a.entry_form=255 and a.company_id =$cbo_company_id  $buyer_po_id_cond  $order_no_cond $where_con $delivery_status_con
	and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 ";*/
	$trims_order_sql="select a.id,a.subcon_job,a.receive_date,a.delivery_date,a.order_no as cust_order_no,a.party_id,a.within_group,a.subcon_job,a.currency_id,a.team_leader,a.team_member,b.item_group,b.buyer_buyer,b.section,b.sub_section,b.job_no_mst,b.order_no,b.booked_uom,b.order_uom,b.buyer_po_no,b.buyer_po_id,c.id as break_id,c.description,c.order_id,c.item_id,c.color_id,c.size_id ,c.qnty,c.rate ,c.amount,c.booked_qty,c.delivery_status, a.trims_ref
		from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c where a.subcon_job=b.job_no_mst and b.job_no_mst=c.job_no_mst and b.id=c.mst_id and a.entry_form=255 and a.company_id =$cbo_company_id  $buyer_po_id_cond  $order_no_cond $where_con $delivery_status_con
		and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 ";
	//echo $trims_order_sql;die;
	$trims_receive_id_arr=array();
	$trims_data_arr=array();	
	$result_trims_order_sql = sql_select($trims_order_sql);
	foreach($result_trims_order_sql as $row)
	{
		$key=$row[csf("id")].'*'.$row[csf("order_id")].'*'.$row[csf("section")].'*'.$row[csf("sub_section")].'*'.$row[csf("item_group")].'*'.$row[csf("item_id")].'*'.$row[csf("description")].'*'.$row[csf("color_id")].'*'.$row[csf("size_id")].'*'.$row[csf("booked_uom")];
		
		$trims_data_arr[$key][qnty]+=$row[csf("qnty")];
		$trims_data_arr[$key][amount]+=$row[csf("amount")];
		$trims_data_arr[$key][booked_qty]+=$row[csf("booked_qty")];
		$trims_data_arr[$key][break_ids].=$row[csf("break_id")].',';
		$trims_data_arr[$key][buyer_po_no].=$row[csf("buyer_po_no")].',';
		$trims_data_arr[$key][delivery_status].=$row[csf("delivery_status")].',';

		$buyer_po_no=chop(implode(',',array_unique(explode(",",$trims_data_arr[$key][buyer_po_no]))),',');
		$order_rate=$trims_data_arr[$key][amount]/$trims_data_arr[$key][qnty];
		$date_array[$key]=array(
		subcon_job=>$row[csf("subcon_job")],
		item_group=>$row[csf("item_group")],
		receive_date=>$row[csf("receive_date")],
		delivery_date=>$row[csf("delivery_date")],
		order_qty=>$trims_data_arr[$key][qnty],
		//order_rate=>$row[csf("rate")],
		order_rate=>$order_rate,
		order_amount=>$trims_data_arr[$key][amount],
		booked_qty=>$trims_data_arr[$key][booked_qty],
		cust_order_no=>$row[csf("cust_order_no")],
		party_id=>$row[csf("party_id")],
		within_group=>$row[csf("within_group")],
		buyer_buyer=>$row[csf("buyer_buyer")],
		section=>$row[csf("section")],
		sub_section=>$row[csf("sub_section")],
		booked_uom=>$row[csf("booked_uom")],
		order_uom=>$row[csf("order_uom")],
		description=>$row[csf("description")],
		delivery_status=>$trims_data_arr[$key][delivery_status],
		order_no=>$row[csf("order_no")],
		buyer_po_no=>$buyer_po_no,
		currency_id=>$row[csf("currency_id")],
		team_leader=>$row[csf("team_leader")],
		team_member=>$row[csf("team_member")],
		break_ids=>$trims_data_arr[$key][break_ids],
		buyer_po_id=>$row[csf("buyer_po_id")],
		trims_ref=>$row[csf("trims_ref")],
		
		);
		$trims_receive_id_arr[$row[csf("id")]]=$row[csf("id")];
	}
	$trims_receive_id=implode(',',$trims_receive_id_arr);
	if($trims_receive_id!='')
	{
		$trimsreceiveid=chop($trims_receive_id,','); 
		$trimsreceiveid_cond="";
		$trimsRcv_cond="";
		$trimsreceive_ids=count(array_unique(explode(",",$trimsreceiveid)));
			if($db_type==2 && $trimsreceive_ids>1000)
			{
				$trimsreceiveid_cond=" and (";
				$trimsRcv_cond=" and (";
				$trimsreceiveidArr=array_chunk(explode(",",$trimsreceiveid),999);
				foreach($trimsreceiveidArr as $ids)
				{
					$ids=implode(",",$ids);
					$trimsreceiveid_cond.=" a.received_id in($ids) or"; 
					$trimsRcv_cond.=" a.id in($ids) or"; 
				}
				$trimsreceiveid_cond=chop($trimsreceiveid_cond,'or ');
				$trimsRcv_cond=chop($trimsRcv_cond,'or ');
				$trimsreceiveid_cond.=")";
				$trimsRcv_cond.=")";
			}
			else
			{
				if($trimsreceiveid!="")
				{
					$issue_ids=implode(",",array_unique(explode(",",$trimsreceiveid)));
					$trimsreceiveid_cond=" and a.received_id in($issue_ids)";
					$trimsRcv_cond.="  and a.id in($issue_ids)"; 
				} else { 
					$trimsreceiveid_cond ="";
					$trimsRcv_cond ="";
				}
			}
		}
		
	//Job-------------------------------	
	$trims_job_sql="select a.id,a.trims_job,a.received_no from trims_job_card_mst a where a.status_active=1 and a.is_deleted=0 $trimsreceiveid_cond "; 
	$trims_job_no_arr=array();	
	$result_trims_job_sql = sql_select($trims_job_sql);
	foreach($result_trims_job_sql as $row)
	{
		$trims_job_no_arr[$row[csf("received_no")]]=$row[csf("trims_job")];
		$trims_job_id_arr[$row[csf("id")]]=$row[csf("id")];
	}
		
	$trims_job_id=implode(',',$trims_job_id_arr);
	if($trims_job_id!='')
	{
		
		$trimsjobid=chop($trims_job_id,','); 
		$trimsjobid_cond="";
		$trimsjobids=count(array_unique(explode(",",$trimsjobid)));
		
		
		if($db_type==2 && $trimsjobids>1000)
		{
			$trimsjobid_cond=" and (";
			$trimsreceiveidArr=array_chunk(explode(",",$trimsjobid),999);
			foreach($trimsreceiveidArr as $jobids)
			{
				$jobids=implode(",",$jobids);
				$trimsjobid_cond.=" a.job_id in($jobids) or"; 
			}
			$trimsjobid_cond=chop($trimsjobid_cond,'or ');
			$trimsjobid_cond.=")";
		}
		else
		{
			if($trimsjobid!="")
			{
				$issue_ids=implode(",",array_unique(explode(",",$trimsjobid)));
				$trimsjobid_cond=" and a.job_id in($issue_ids)";
			}
			else { $trimsjobid_cond ="";}
		}
	
	}
		
	//production.................................
	$trims_production_sql="select a.section_id,c.sub_section,a.received_id,a.job_id,b.job_dtls_id,c.id as order_receive_dtls_id,c.item_group_id,c.item_description,c.color_id,c.size_id,c.uom,c.job_quantity,b.production_qty,b.qc_qty  from trims_production_mst a,trims_production_dtls b,trims_job_card_dtls c where a.id=b.mst_id and c.id=b.job_dtls_id $trimsjobid_cond $trimsreceiveid_cond and a.entry_form=269
	and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0"; 
	$trims_production_data_arr=array();	
	$result_trims_production_sql = sql_select($trims_production_sql);
	foreach($result_trims_production_sql as $row)
	{
		$key=$row[csf("item_group_id")].'*'.$row[csf("received_id")].'*'.$row[csf("section_id")].'*'.$row[csf("sub_section")].'*'.$row[csf("item_description")].'*'.$row[csf("color_id")].'*'.$row[csf("size_id")].'*'.$row[csf("uom")];
		
		$trims_production_data_arr[$key][qc_qty]+=$row[csf("qc_qty")];
		$trims_production_data_arr[$key][job_quantity]+=$row[csf("job_quantity")];
	}	
		
	//Delivery.................................
	//$trims_delivery_sql="select a.delivery_date,a.received_id,b.delevery_qty,b.order_receive_rate,c.uom,b.section,c.sub_section,b.item_group,b.color_id,b.size_id,b.description,b.delevery_status,b.break_down_details_id,c.id from trims_delivery_mst a, trims_delivery_dtls b, trims_job_card_dtls c  where a.id=b.mst_id and c.id=b.job_dtls_id $trimsreceiveid_cond  and a.entry_form=208 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by a.delivery_date"; 
	$trims_delivery_sql="select a.delivery_date,a.received_id,b.delevery_qty,b.order_receive_rate,c.booked_uom as uom,b.section,c.sub_section,b.item_group,b.color_id,b.size_id,b.description,b.delevery_status,b.break_down_details_id,c.id from trims_delivery_mst a, trims_delivery_dtls b, subcon_ord_dtls c where a.id=b.mst_id and c.id=b.receive_dtls_id $trimsreceiveid_cond  and a.entry_form=208 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by a.delivery_date"; 
	$trims_delivery_data_arr=array();	
	$result_trims_delivery_sql = sql_select($trims_delivery_sql);
	foreach($result_trims_delivery_sql as $row)
	{
		$key=$row[csf("item_group")].'*'.$row[csf("received_id")].'*'.$row[csf("section")].'*'.$row[csf("sub_section")].'*'.$row[csf("description")].'*'.$row[csf("color_id")].'*'.$row[csf("size_id")].'*'.$row[csf("uom")];
		//c.id
		
		$trims_delivery_data_arr[$key][delevery_qty]+=$row[csf("delevery_qty")];
		$trims_delivery_data_arr[$key][delevery_val]+=$row[csf("delevery_qty")]*$row[csf("order_receive_rate")];
		$trims_delivery_data_arr[$key][delevery_last_date]=$row[csf("delivery_date")];
		$trims_delivery_data_arr[$key][id]=$row[csf("id")];
		$trims_delivery_data_arr[$key][delevery_status]=$row[csf("delevery_status")];
		$trims_delivery_data_arr[$key][break_ids].=$row[csf("break_down_details_id")].',';
	}
	//echo "<pre>";
	//print_r($trims_delivery_data_arr);
	//Bill.................................
	//select SECTION,ITEM_DESCRIPTION,COLOR_ID,SIZE_ID,ORDER_UOM,TOTAL_DELV_QTY,b.QUANTITY     from TRIMS_BILL_MST a, TRIMS_BILL_dtls b where a.id=b.mst_id and a.ENTRY_FORM=276	
	//bill.................................
	/*$trims_bill_sql="select d.received_id,b.section,c.sub_section,b.item_description,b.color_id,b.size_id,c.uom,total_delv_qty,b.quantity,b.bill_amount,b.id from trims_bill_mst a, trims_bill_dtls b,trims_job_card_dtls c, trims_job_card_mst d  where a.id=b.mst_id and c.id=b.job_dtls_id and c.job_no_mst=d.trims_job and a.entry_form=276 and d.received_id in(".implode(',',$trims_receive_id_arr).") and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";*/
	//$trims_bill_sql="select a.received_id,b.section,c.sub_section,b.item_description,b.color_id,b.size_id,c.uom,total_delv_qty,b.quantity,b.bill_amount,b.id from trims_bill_mst d, trims_bill_dtls b,trims_job_card_dtls c, trims_job_card_mst a  where d.id=b.mst_id and c.id=b.job_dtls_id and c.job_no_mst=a.trims_job and d.entry_form=276 $trimsreceiveid_cond and d.status_active=1 and d.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
	
	$trims_bill_sql="select c.mst_id as received_id,c.section,c.sub_section,b.item_description,b.color_id,b.size_id,c.booked_uom as uom ,total_delv_qty,b.quantity,b.bill_amount,b.id from trims_bill_mst d, trims_bill_dtls b, subcon_ord_dtls c, subcon_ord_mst a where d.id=b.mst_id and c.mst_id=a.id and c.order_no=b.order_no and d.entry_form=276 and a.entry_form=255 $trimsRcv_cond and d.status_active=1 and d.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by c.mst_id ,received_id,c.section,c.sub_section,b.item_description,b.color_id,b.size_id,c.booked_uom ,total_delv_qty,b.quantity,b.bill_amount,b.id";
	$trims_bill_data_arr=array();	
	$result_trims_bill_sql = sql_select($trims_bill_sql);
	foreach($result_trims_bill_sql as $row)
	{
		$key=$row[csf("received_id")].'*'.$row[csf("section")].'*'.$row[csf("sub_section")].'*'.$row[csf("item_description")].'*'.$row[csf("color_id")].'*'.$row[csf("size_id")].'*'.$row[csf("uom")];
		
		$trims_bill_data_arr[$key][bill_qty]+=$row[csf("quantity")];
		$trims_bill_data_arr[$key][bill_val]+=$row[csf("bill_amount")];
		$trims_bill_data_arr[$key][dtls_ids].=$row[csf("id")].',';
	}
	//echo "<pre>";
	//print_r($trims_bill_data_arr);
	$width=3700;
	ob_start();
	?>
	<div align="center" style="height:auto; width:<? echo $width+20;?>px; margin:0 auto; padding:0;">
		<table width="<? echo $width;?>" cellpadding="0" cellspacing="0" id="caption" align="center">
			<thead class="form_caption" >
				<tr>
					<td colspan="36" align="center" style="font-size:20px;"><? echo $companyArr[$cbo_company_id]; ?></td>
				</tr>
				<tr>
					<td colspan="36" align="center" style="font-size:14px; font-weight:bold" ><? echo $report_title; ?></td>
				</tr>
				<tr>
					<td colspan="36" align="center" style="font-size:14px; font-weight:bold">
						<? echo " From : ".$txt_date_from." To : ". $txt_date_to ;?>
					</td>
				</tr>
			</thead>
		</table>
        <table align="left">
            <tr>
                <td bgcolor="#FFF" width="15">&nbsp;</td><td>Full Pending</td>
                <td bgcolor="#FFCC66" width="15">&nbsp;</td><td>Partial Deliverd</td>
                <td bgcolor="#8CD59C" width="15">&nbsp;</td><td>Full Deliverd</td>
          	</tr>
        </table>
        <table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="<? echo $width;?>" rules="all" id="rpt_table_header" align="left">
			<thead>
                <th width="35">SL</th>
                <th width="120">Order Rcv. No</th>
                <th width="60">Order Source</th>
                <th width="100">Job Card No</th>
                <th width="100">Cust. WO No</th>
                <th width="100">Customer Name</th>
                <th width="100">Cust. Buyer</th>
                <th width="200">Cust. Order No</th>
                <th width="100">Internal Ref</th>
                <th width="100">Trims Ref</th>
                <th width="60">Section</th>
                <th width="100">Sub-Section</th>
                <th width="100">Trims Group</th>
                <th width="100">Item Description</th>
                <th width="100">Color</th>
                <th width="60">Order UOM</th>
                <th width="100">Order Qty</th>
                <th width="60">Booked UOM</th>
                <th width="100">Booked Qty</th>
                <th width="100">Order Amount ($)</th>
                <th width="80">Order Rcv.Date</th>
                <th width="80">Target Delv. Date</th>
                <th width="80">Last Delv. Date</th>
                <th width="100">Delv.Status</th>
                <th width="100">Production Qty</th>
                <th width="100">Prod Bal Qty</th>
                <th width="100">Delv.Qty</th>
                <th width="100">Delv.Amount($)</th>
                <th width="100">Delv. Balance Qty</th>
                <th width="100">Delv. Balance Amount($)</th>
                <th width="100">Short Delv Qty</th>
                <th width="100">Short Delv Value</th>
                <th width="100">Bill Qty</th>
                <th width="100">Bill Amount</th>
                <th width="100">Bill Bal. Qty</th>
                <th width="100">Bill Bal. Amnt ($)</th>
                <th width="100">Team Leader</th>
                <th width="100">Mkt. by.</th>
                <th>Remarks</th>
			</thead>
		</table>
		<div style="width:<? echo $width+18;?>px; max-height:250px; float:left; overflow-y:scroll;" id="scroll_body">
		<table border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body_id" width="<? echo $width;?>" rules="all" align="left">
            <? 
			$i=1;
			$total_order_qty=0;$total_order_val=0;$total_booked_qty=0;$total_production_qty=0;
			foreach($date_array as $keysss=>$row)
			{
				list($id,$order_id,$section,$sub_section,$item_group,$item_id,$description,$color_id,$size_id,$booked_uom)=explode('*',$keysss);
				$key=$item_group.'*'.$id.'*'.$section.'*'.$sub_section.'*'.$description.'*'.$color_id.'*'.$size_id.'*'.$booked_uom;
				$key=trim($key);
				//$production_qty_on_order_parcent=($trims_production_data_arr[$key][qc_qty]/$trims_production_data_arr[$key][job_quantity])*$row[order_qty];
				$production_qty_on_order_parcent=$trims_production_data_arr[$key][qc_qty];
				
				//WORK ORDER NO : 161
				$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
				$party=($row[within_group]==1)?$companyArr[$row[party_id]]:$buyerArr[$row[party_id]];
				$buyer_buyer=($row[within_group]==1)?$buyerArr[$row[buyer_buyer]]:$row[buyer_buyer];
			
				//$row[delivery_status]=$trims_delivery_data_arr[$item_group.'*'.$key][delevery_status];
				
				$deliveryStatus=chop($row[delivery_status],',');
				$del_status=array_unique(explode(",",$deliveryStatus));
				$sts=min($del_status);
				if($sts==2){$bgcolor="#FFCC66";}
				elseif($sts==3){$bgcolor="#8CD59C";}
				else{$sts=1;}
			
			//---------------------------------------
			$total_order_qty+=$row[order_qty];
			$total_order_val+=$row[order_amount];
			$total_booked_qty+=$row[booked_qty];
			$total_production_qty+=$production_qty_on_order_parcent;
			
			if($row["currency_id"]==1){
				$row[order_rate]=$row[order_rate]/$currency_rate;
				$row[order_amount]=$row[order_amount]/$currency_rate;
			}
			//echo $row[order_rate].'=='.$currency_rate.'++';
			/*$trims_delivery_data_arr[$key][delevery_qty]+=$row[csf("delevery_qty")];
			$trims_delivery_data_arr[$key][delevery_val]+=$row[csf("delevery_qty")]*$row[csf("order_receive_rate")];
			$trims_delivery_data_arr[$key][delevery_last_date]=$row[csf("delivery_date")];
			$trims_delivery_data_arr[$key][id]=$row[csf("id")];
			$trims_delivery_data_arr[$key][delevery_status]=$row[csf("delevery_status")];
			$trims_delivery_data_arr[$key][break_ids].=$row[csf("break_down_details_id")].',';*/
			
			/*if($cbo_delivery_status!=0 && ($trims_delivery_data_arr[$item_group.'*'.$key][delevery_status] == $cbo_delivery_status))
			{*/
				$delivery_amt=number_format($row[order_rate]*$trims_delivery_data_arr[$key][delevery_qty],2);
				$delivery_qty=number_format($trims_delivery_data_arr[$key][delevery_qty],0);
				$DelvBalanceQty=$row[order_qty]-$trims_delivery_data_arr[$key][delevery_qty];
			/*}*/
			//bill_val
			
			//$bill_amt=number_format($row[order_rate]*$trims_bill_data_arr[$key][bill_qty],2);
			/*$bill_amt=$trims_bill_data_arr[$key][bill_val];
			$bill_qty=$trims_bill_data_arr[$key][bill_qty];
			$bill_rate=$bill_amt/$bill_qty;*/
			$bill_amt=$bill_qty=$bill_rate=0;
			if($trims_bill_data_arr[$key][bill_qty] !=''){
				$bill_amt=$trims_bill_data_arr[$key][bill_val];
				$bill_qty=$trims_bill_data_arr[$key][bill_qty];
				if($bill_qty<=1){
					$bill_rate=$bill_amt/$bill_qty;
				}else{
					$bill_rate=0;
				}
			}
			if($bill_qty==0){
				$bill_rate=$row[order_rate];
			}
			//echo $bill_amt.'=='.$bill_qty.'=='.$bill_rate.'++'; 
			$receive_ids=implode(',',$trims_receive_id_arr);
			if($row["within_group"]==1) $orderSource='Internal'; else $orderSource='External';
			?>
            <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                <td width="35" align="center"><? echo $i;?></td>
                <td width="120" align="center"><p><? echo $row[subcon_job];?></p></td>
                <td width="60" align="center"><p><? echo $orderSource;?></p></td>
                <td width="100" align="center"><p><? echo $trims_job_no_arr[$row[subcon_job]];?></p></td>
                <td width="100" align="center"><p><? echo $row[cust_order_no];?></p></td>
                <td width="100"><p><? echo $party;?></p></td>
                <td width="100"><p><? echo $buyer_buyer;?></p></td>
                <td width="200"><p><? echo $row[buyer_po_no];?></p></td>
                <td width="100"><p><? echo $buyer_po_arr[$row[buyer_po_id]]['grouping']; //$row[buyer_po_id];?></p></td>
                <td width="100"><p><? echo $row[trims_ref];?></p></td>
                <td width="60"><p><? echo $trims_section[$row[section]];?></p></td>
                <td width="100"><p><? echo $trims_sub_section[$row[sub_section]];?></p></td>
                <td width="100"><p><? echo $trimsGroupArr[$row[item_group]];?></p></td>
                <td width="100"><p><? echo $row[description];?></p></td>
                <td width="100"><p><? echo $colorNameArr[$color_id];?></p></td>
                <td width="60" align="center"><? echo $unit_of_measurement[$row[order_uom]];?></td>
                <td width="100" align="right"><? echo number_format($row[order_qty],0); ?></td>
                <td width="60" align="center"><? echo $unit_of_measurement[$row[booked_uom]];?></td>
                <td width="100" align="right"><? echo number_format($row[booked_qty],0);?></td>
                <td width="100" align="right"><? echo number_format($row[order_amount],2);?></td>
                <td width="80" align="center"><? echo $row[receive_date];?></td>
                <td width="80" align="center"><? echo $row[delivery_date];?></td>
                <td width="80" align="center"><? echo $trims_delivery_data_arr[$item_group.'*'.$key][delevery_last_date];?></td>
                <td width="100" align="center"><? echo $delivery_status[$sts];?></td>
                <td width="100" align="right"><? echo number_format($production_qty_on_order_parcent,0);?></td>
                <td width="100" align="right"><? 
				//echo number_format($row[order_qty]-$production_qty_on_order_parcent,0);
				echo number_format($row[booked_qty]-$production_qty_on_order_parcent,0);
				?></td>
                <td width="100" align="right"><? echo $delivery_qty;?></td>
                <?
                if($delivery_amt>0){
                	?><td width="100" align="right"><a href="##" onclick="fnc_amount_details('<? echo $trims_delivery_data_arr[$key][break_ids];?>','<? echo $row[order_rate];?>','delivery_popup')"><p><? echo $delivery_amt;?></p></a> </td><?
                }else{
                	?><td width="100" align="right"><? echo '0'; ?></td><?
                }?>
                <td width="100" align="right"><?
				//$DelvBalanceQty=number_format($row[order_qty]-$trims_delivery_data_arr[$item_group.'*'.$key][delevery_qty],0);
				
				if($DelvBalanceQty<=1){
				 	echo $DelvBalanceQty=0;
				}else{
					echo $DelvBalanceQty=number_format($row[order_qty]-$trims_delivery_data_arr[$key][delevery_qty],0);
				}
				// echo number_format($row[order_qty],0)-number_format($trims_delivery_data_arr[$item_group.'*'.$key][delevery_qty],0);
				//echo number_format($row[booked_qty]-$trims_delivery_data_arr[$item_group.'*'.$key][delevery_qty],0);
				?></td>
                <td width="100" align="right"><? 
               	// echo $DelvBalanceQty.'==';
                if($DelvBalanceQty<=1){
				 	echo 0;
				}else{
					echo number_format(($row[order_amount]-$delivery_amt),2);
					
				}
				?></td>
                <td width="100" align="right"><? //echo number_format($row[order_qty]-$trims_delivery_data_arr[$item_group.'*'.$key][delevery_qty],2);?></td>
                <td width="100" align="right"><? //echo number_format($row[order_amount]-$trims_delivery_data_arr[$item_group.'*'.$key][delevery_val]);?></td>
                <td width="100" align="right"><? echo $bill_qty;?></td>
                <?
                if($bill_amt>0){
                	?><td width="100" align="right"><a href="##" onclick="fnc_amount_details('<? echo chop($trims_bill_data_arr[$key][dtls_ids],',');?>','<? echo $bill_rate;?>','bill_popup')"><p><? echo number_format($bill_amt,2);?></p></a> </td><?
                }else{
                	?><td width="100" align="right"><? echo '0'; ?></td><?
                }?>
                
                <td width="100" align="right"><? echo number_format($trims_delivery_data_arr[$key][delevery_qty]-$bill_qty);?></td>
                <td width="100" align="right"><? echo number_format(($trims_delivery_data_arr[$key][delevery_qty]-$bill_qty)*$bill_rate,2); //number_format($row[order_rate]*$trims_delivery_data_arr[$item_group.'*'.$key][delevery_qty]-$trims_bill_data_arr[$key][bill_qty],2)//number_format($trims_delivery_data_arr[$item_group.'*'.$key][delevery_val]-$trims_bill_data_arr[$key][bill_val],2);?></td>
                <td width="100"><p><? echo $leaderArr[$row[team_leader]];?></p></td>
                <td width="100"><p><? echo $memberArr[$row[team_member]];?></p></td>
                <td></td>
            </tr>
            <? 
			$i++;
			} ?>
		</table>
		</div>
		<table width="<? echo $width;?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body_footer" align="left">
			<tfoot>
                <th width="35"></th>
                <th width="120"></th>
                <th width="60"></th>
                <th width="100"></th>
                <th width="100"></th>
                <th width="100"> </th>
                <th width="100"></th>
                <th width="100"></th>
                <th width="200"></th>
                <th width="100"></th>
                <th width="60"></th>
                <th width="100"></th>
                <th width="100"></th>
                <th width="100"></th>
                <th width="100" align="right"><? //echo number_format($total_order_qty);?></th>
                <th width="60"></th>
                <th width="100"></th>
                <th width="60" align="right"><? //echo number_format($total_booked_qty);?></th>
                <th width="100"></th>
                <th width="100" align="right"><? //echo number_format($total_order_val,2);?></th>
                <th width="80"></th>
                <th width="80"></th>
                <th width="80"></th>
                <th width="100"></th>
                <th width="100" align="right"><? //echo number_format($total_production_qty,2);?></th>
                <th width="100"><!--Prod Bal Qty--></th>
                <th width="100" align="right"><? //echo $total_delivery_qty;?></th>
                <th width="100"><!--Delv.Amount--></th>
                <th width="100"><!--Delv. Balance Qty--></th>
                <th width="100"><!--Delv. Balance Amount--></th>
                <th width="100"><!--Short Delv Qty--></th>
                <th width="100"><!--Short Delv Value--></th>
                <th width="100"><!--Bill Qty--></th>
                <th width="100"><!--Bill Amount--></th>
                <th width="100"><!--Bill Bal. Qty--></th>
                <th width="100"><!--Bill Bal. Amnt--></th>
                <th width="100"><!--Leader--></th>
                <th width="100"><!--Member--></th>
                <th>&nbsp;</th>
			</tfoot>
		</table>
    </div>
    <?
    $html = ob_get_contents();
    ob_clean();
    //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
    foreach (glob("*.xls") as $filename) {
    //if( @filemtime($filename) < (time()-$seconds_old) )
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, $html);
    echo "$html**$filename**$report_type";
    exit();
}

if($action=="generate_report_3_old")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	$cbo_company_id=str_replace("'","", $cbo_company_id);
	$cbo_location_name=str_replace("'","", $cbo_location_name);
	$cbo_customer_source=str_replace("'","", $cbo_customer_source);
	$cbo_customer_name=str_replace("'","", $cbo_customer_name);
	$txt_order_no=str_replace("'","", $txt_order_no);
	$internal_no=str_replace("'","", $txt_internal_no);
	$cbo_section_id=str_replace("'","", $cbo_section_id);
	$cbo_sub_section_id=str_replace("'","", $cbo_sub_section_id);
	$txt_item_description=str_replace("'","", $txt_item_description);
	$cbo_date_category=str_replace("'","", $cbo_date_category);
	$txt_date_from=str_replace("'","", $txt_date_from);
	$txt_date_to=str_replace("'","", $txt_date_to);
	$cbo_delivery_status=str_replace("'","", $cbo_delivery_status);
	$cbo_customer_buyer=str_replace("'","", $cbo_customer_buyer);
	$cbo_team_leader=str_replace("'","", $cbo_team_leader);
	$cbo_team_member=str_replace("'","", $cbo_team_member);


	if($cbo_delivery_status== 0){$delivery_status_con="";}
	else{
		//$delivery_status_con=" and c.delivery_status=$cbo_delivery_status and b.delivery_status=$cbo_delivery_status";
		$delivery_status_con=" and c.delivery_status in ($cbo_delivery_status) and b.delivery_status in ($cbo_delivery_status)";
	}
		
	if($db_type==0) 
	{
		$txt_date_from=change_date_format($txt_date_from,'yyyy-mm-dd');
		$txt_date_to=change_date_format($txt_date_to,'yyyy-mm-dd');
	}
	else if($db_type==2) 
	{
		$txt_date_from=change_date_format($txt_date_from,'','',1);
		$txt_date_to=change_date_format($txt_date_to,'','',1);
	}
	
	$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
	$buyerArr = return_library_array("select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0 ","id","buyer_name");
	$trimsGroupArr = return_library_array("select id, item_name from lib_item_group where item_category=4 and status_active=1","id","item_name");
	$colorNameArr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0","id","color_name");
	$size_arr=return_library_array( "select id,size_name from lib_size",'id','size_name');
	$leaderArr = return_library_array("select id, team_leader_name from lib_marketing_team where status_active=1 and is_deleted=0 and project_type=3","id","team_leader_name");
	$memberArr = return_library_array("select id, team_member_name from lib_mkt_team_member_info","id","team_member_name");

	$currency_rate=return_field_value( "conversion_rate", "currency_conversion_rate", "currency=2 and status_active=1  and id in(select max(id) from currency_conversion_rate where currency=2 and is_deleted=0 and status_active=1)" , "conversion_rate" );
	
	if($txt_date_from!="" and $txt_date_to!=""){	
		if($cbo_date_category==1){$where_con.=" and a.receive_date between '$txt_date_from' and '$txt_date_to'";}	
		else if($cbo_date_category==2){$where_con.=" and a.delivery_date between '$txt_date_from' and '$txt_date_to'";}
		else if($cbo_date_category==3){$date_con.=" and a.delivery_date between '$txt_date_from' and '$txt_date_to'";}
	}
	if($cbo_date_category==3){
		if($cbo_section_id){$where_con_ord.=" and b.section='$cbo_section_id'";} 
		if($cbo_sub_section_id){$where_con_ord.=" and c.sub_section='$cbo_sub_section_id'";} 
		if($txt_item_description){$where_con_ord.=" and d.description like '%$txt_item_description%'";}
		//if($cbo_customer_source){$where_con_ord.=" and a.within_group='$cbo_customer_source'";} 
		//if($cbo_customer_name){$where_con_ord.=" and a.party_id='$cbo_customer_name'";} 

	}
	if($cbo_section_id){$where_con.=" and b.section='$cbo_section_id'";} 
	if($cbo_sub_section_id){$where_con.=" and b.sub_section='$cbo_sub_section_id'";} 
	if($cbo_customer_source){$where_con.=" and a.within_group='$cbo_customer_source'";} 
	if($cbo_customer_name){$where_con.=" and a.party_id='$cbo_customer_name'";} 
	if($cbo_customer_buyer){$where_con.=" and b.buyer_buyer='$cbo_customer_buyer'";} 
	if($cbo_team_leader){$where_con.=" and a.team_leader='$cbo_team_leader'";} 
	if($cbo_team_member){$where_con.=" and a.team_member='$cbo_team_member'";}
	if($cbo_location_name){$where_con.=" and a.location_id = $cbo_location_name";}
	if($txt_item_description){$where_con.=" and c.description like '%$txt_item_description%'";}
	
	
	//echo $internal_no;
 	$buyer_po_id_cond = '';
    if($cbo_customer_source==1 || $cbo_customer_source==0)
    {
		if($internal_no !="") $internal_no_cond = " and grouping like('%$internal_no%')";
		
		$buyer_po_arr=array();
		$buyer_po_id_arr=array();
		$po_sql ="Select id,grouping from  wo_po_break_down  where is_deleted=0 and status_active=1 $internal_no_cond "; 
		$po_sql_res=sql_select($po_sql);
		foreach ($po_sql_res as $row)
		{
			$buyer_po_arr[$row[csf("id")]]['grouping']=$row[csf("grouping")];
			$buyer_po_id_arr[]=$row[csf("id")];
		}
		unset($po_sql_res);
        //$buyer_po_lib = return_library_array("select id, id as id2 from wo_po_break_down where grouping like '%$internal_no%'",'id','id2');
		if($internal_no !="")
		{
			$buyer_po_id = implode(",", $buyer_po_id_arr);
			if ($buyer_po_id=="")
			{
				echo "Not Found."; die;
			}
       	 	if($buyer_po_id !="") $buyer_po_id_cond = " and b.buyer_po_id in($buyer_po_id)";
		}
    }

    $buyer_po_id_cond = '';
    if($cbo_date_category==3)
    {
    	/*$trims_delivery_sql="select a.delivery_date,a.received_id,b.delevery_qty,b.order_receive_rate,c.booked_uom as uom,b.section,c.sub_section,b.item_group,b.color_id,b.size_id,b.description,b.delevery_status,b.break_down_details_id,c.id from trims_delivery_mst a, trims_delivery_dtls b, subcon_ord_dtls c where a.id=b.mst_id and c.id=b.receive_dtls_id $where_con_ord $date_con and a.entry_form=208  and a.company_id =$cbo_company_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by a.delivery_date"; */

    	$trims_delivery_sql="select a.delivery_date,a.received_id,b.delevery_qty,b.order_receive_rate,c.booked_uom as uom,b.section,c.sub_section,b.item_group,b.color_id,b.size_id,d.description,b.delevery_status,b.break_down_details_id,c.id from trims_delivery_mst a, trims_delivery_dtls b, subcon_ord_dtls c, subcon_ord_breakdown d where b.break_down_details_id=d.id and c.id=d.mst_id and  a.id=b.mst_id and c.id=b.receive_dtls_id  $where_con_ord $date_con  and a.company_id =$cbo_company_id and a.entry_form=208 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by a.delivery_date"; 
    	//and a.company_id =$cbo_company_id  
    	$trims_delivery_data_arr=array();	
		$result_trims_delivery_sql = sql_select($trims_delivery_sql);
		foreach($result_trims_delivery_sql as $row)
		{
			$key=$row[csf("item_group")].'*'.$row[csf("received_id")].'*'.$row[csf("section")].'*'.$row[csf("sub_section")].'*'.$row[csf("description")].'*'.$row[csf("color_id")].'*'.$row[csf("size_id")].'*'.$row[csf("uom")].'*'.$row[csf("break_down_details_id")];
			//$key=$id.'*'.$section.'*'.$sub_section.'*'.$description.'*'.$color_id.'*'.$size_id.'*'.$booked_uom.'*'.chop($row[break_ids],',');
			//c.id
			
			$trims_delivery_data_arr[$key][delevery_qty]+=$row[csf("delevery_qty")];
			$trims_delivery_data_arr[$key][delevery_val]+=$row[csf("delevery_qty")]*$row[csf("order_receive_rate")];
			$trims_delivery_data_arr[$key][delevery_last_date]=$row[csf("delivery_date")];
			$trims_delivery_data_arr[$key][id]=$row[csf("id")];
			$trims_delivery_data_arr[$key][delevery_status]=$row[csf("delevery_status")];
			$trims_delivery_data_arr[$key][break_ids].=$row[csf("break_down_details_id")].',';
			$receive_id_arr[$row[csf("received_id")]]=$row[csf("received_id")];
			$break_down_details_ids.=$row[csf("break_down_details_id")].',';
		}
		
		$receive_id=implode(',',$receive_id_arr);
		$receive_brk_id=implode(',',$receive_brk_id_arr);
		if($receive_id!='')
		{
			$trimsreceiveid=chop($receive_id,','); 
			
			$reiveid_id_cond=""; $reiveid_brk_id_cond="";
			$trimsreceive_ids=count(array_unique(explode(",",$trimsreceiveid)));
			$breakDownIds=chop($break_down_details_ids,',');
			$breakDownIds_arr=array_unique(explode(",",$breakDownIds));
			//print_r($breakDownIds_arr);
			$breakDownId_count=count($breakDownIds_arr);
			if($db_type==2 && $trimsreceive_ids>1000)
			{
				$reiveid_id_cond=" and (";
				$trimsreceiveidArr=array_chunk(explode(",",$trimsreceiveid),999);
				foreach($trimsreceiveidArr as $ids)
				{
					$ids=implode(",",$ids);
					$reiveid_id_cond.=" a.id in($ids) or"; 
				}
				$reiveid_id_cond=chop($reiveid_id_cond,'or ');
				$reiveid_id_cond.=")";
			}
			else
			{
				if($trimsreceiveid!="")
				{
					$issue_ids=implode(",",array_unique(explode(",",$trimsreceiveid)));
					$reiveid_id_cond=" and a.id in($issue_ids)";
				}
				else { $reiveid_id_cond ="";}
			}

			if($db_type==2 && $breakDownId_count>1000)
			{
				$reiveid_brk_id_cond=" and (";
				//$trimsBreakreceiveidArr=array_chunk(explode(",",$trimsBreakreceiveid),999);
				foreach($breakDownIds_arr as $ids)
				{
					$ids=implode(",",$ids);
					$reiveid_brk_id_cond.=" c.id in($ids) or"; 
				}
				$reiveid_brk_id_cond=chop($reiveid_brk_id_cond,'or ');
				$reiveid_brk_id_cond.=")";
			}
			else
			{
				if($breakDownIds_arr!="")
				{
					$issue_ids=implode(",",$breakDownIds_arr);
					$reiveid_brk_id_cond=" and c.id in($issue_ids)";
				}
				else { $reiveid_brk_id_cond ="";}
			}
		}
    }

    if($cbo_date_category==3 && $reiveid_brk_id_cond==''){
    	echo "No Data Found"; die;
    }
	//echo $buyer_po_id_cond; die; //print_r($buyer_po_arr); die;
	
	if($txt_order_no !="") $order_no_cond = " and b.order_no like('%$txt_order_no%')";
	$trims_order_sql="select a.id,a.subcon_job,a.receive_date,a.delivery_date,a.order_no as cust_order_no,a.party_id,a.within_group,a.subcon_job,a.currency_id,a.team_leader,a.team_member,a.team_marchant,b.item_group,b.buyer_buyer,b.section,b.sub_section,b.job_no_mst,b.order_no,b.booked_uom,b.order_uom,b.buyer_po_no,b.buyer_po_id,b.source_for_order,b.submit_date,b.approve_date,c.id as break_id,c.description,c.order_id,c.item_id,c.color_id,c.size_id ,c.qnty,c.rate ,c.amount,c.booked_qty,c.delivery_status,c.style, a.trims_ref,a.delivery_point,b.booked_conv_fac as conv_factor
	from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c
	where a.subcon_job=b.job_no_mst and b.job_no_mst=c.job_no_mst and b.id=c.mst_id and a.entry_form=255 and a.company_id =$cbo_company_id  $buyer_po_id_cond  $order_no_cond $where_con $delivery_status_con $reiveid_id_cond $reiveid_brk_id_cond
	and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1
	ORDER BY a.subcon_job";
	//echo $trims_order_sql;//die;
	$trims_receive_id_arr=array();
	$trims_data_arr=array();	
	$result_trims_order_sql = sql_select($trims_order_sql);
	foreach($result_trims_order_sql as $row)
	{
		$key=$row[csf("id")].'*'.$row[csf("order_id")].'*'.$row[csf("section")].'*'.$row[csf("sub_section")].'*'.$row[csf("item_group")].'*'.$row[csf("item_id")].'*'.$row[csf("description")].'*'.$row[csf("color_id")].'*'.$row[csf("size_id")].'*'.$row[csf("booked_uom")].'*'.$row[csf("rate")].'*'.$row[csf("style")];
		
		$trims_data_arr[$key][qnty]+=$row[csf("qnty")];
		$trims_data_arr[$key][amount]+=$row[csf("amount")];
		$trims_data_arr[$key][booked_qty]+=$row[csf("booked_qty")];
		$trims_data_arr[$key][break_ids].=$row[csf("break_id")].',';
		$trims_data_arr[$key][delivery_status].=$row[csf("delivery_status")].',';
		
		$date_array[$key]=array(
		subcon_job=>$row[csf("subcon_job")],
		item_group=>$row[csf("item_group")],
		receive_date=>$row[csf("receive_date")],
		delivery_date=>$row[csf("delivery_date")],
		order_qty=>$trims_data_arr[$key][qnty],
		order_rate=>$row[csf("rate")],
		order_amount=>$trims_data_arr[$key][amount],
		booked_qty=>$trims_data_arr[$key][booked_qty],
		cust_order_no=>$row[csf("cust_order_no")],
		party_id=>$row[csf("party_id")],
		within_group=>$row[csf("within_group")],
		buyer_buyer=>$row[csf("buyer_buyer")],
		section=>$row[csf("section")],
		sub_section=>$row[csf("sub_section")],
		booked_uom=>$row[csf("booked_uom")],
		order_uom=>$row[csf("order_uom")],
		description=>$row[csf("description")],
		//delivery_status=>$row[csf("delivery_status")],
		delivery_status=>$trims_data_arr[$key][delivery_status],
		order_no=>$row[csf("order_no")],
		buyer_po_no=>$row[csf("buyer_po_no")],
		currency_id=>$row[csf("currency_id")],
		team_leader=>$row[csf("team_leader")],
		team_member=>$row[csf("team_member")],
		team_marchant=>$row[csf("team_marchant")],
		break_ids=>$trims_data_arr[$key][break_ids],
		buyer_po_id=>$row[csf("buyer_po_id")],
		trims_ref=>$row[csf("trims_ref")],
		style=>$row[csf("style")],
		size_id=>$row[csf("size_id")],
		challan_no=>$row[csf("challan_no")],
		source_for_order=>$row[csf('source_for_order')],
		submit_date=>$row[csf('submit_date')],
		approve_date=>$row[csf('approve_date')],
		delivery_point=>$row[csf('delivery_point')],
		conv_factor=>$row[csf('conv_factor')],
		
		);
		$trims_receive_id_arr[$row[csf("id")]]=$row[csf("id")];
	}
	$trims_receive_id=implode(',',$trims_receive_id_arr);
	if($trims_receive_id!='')
	{
		$trimsreceiveid=chop($trims_receive_id,','); 
		$trimsreceiveid_cond="";
		$trimsreceive_ids=count(array_unique(explode(",",$trimsreceiveid)));
		if($db_type==2 && $trimsreceive_ids>1000)
		{
			$trimsreceiveid_cond=" and (";
			$trimsreceiveidArr=array_chunk(explode(",",$trimsreceiveid),999);
			foreach($trimsreceiveidArr as $ids)
			{
				$ids=implode(",",$ids);
				$trimsreceiveid_cond.=" a.received_id in($ids) or"; 
			}
			$trimsreceiveid_cond=chop($trimsreceiveid_cond,'or ');
			$trimsreceiveid_cond.=")";
		}
		else
		{
			if($trimsreceiveid!="")
			{
				$issue_ids=implode(",",array_unique(explode(",",$trimsreceiveid)));
				$trimsreceiveid_cond=" and a.received_id in($issue_ids)";
			}
			else { $trimsreceiveid_cond ="";}
		}
	}
		
	//Job-------------------------------	
	$trims_job_sql="select a.id,a.trims_job,a.received_no from trims_job_card_mst a where a.status_active=1 and a.is_deleted=0 $trimsreceiveid_cond "; 
	$trims_job_no_arr=array();	
	$result_trims_job_sql = sql_select($trims_job_sql);
	foreach($result_trims_job_sql as $row)
	{
		$trims_job_no_arr[$row[csf("received_no")]]=$row[csf("trims_job")];
		$trims_job_id_arr[$row[csf("id")]]=$row[csf("id")];
	}

	$trims_job_id=implode(',',$trims_job_id_arr);
	if($trims_job_id!='')
	{
		
		$trimsjobid=chop($trims_job_id,','); 
		$trimsjobid_cond="";
		$trimsjobids=count(array_unique(explode(",",$trimsjobid)));
		
		if($db_type==2 && $trimsjobids>1000)
		{
			$trimsjobid_cond=" and (";
			$trimsreceiveidArr=array_chunk(explode(",",$trimsjobid),999);
			foreach($trimsreceiveidArr as $jobids)
			{
				$jobids=implode(",",$jobids);
				$trimsjobid_cond.=" a.job_id in($jobids) or"; 
			}
			$trimsjobid_cond=chop($trimsjobid_cond,'or ');
			$trimsjobid_cond.=")";
		}
		else
		{
			if($trimsjobid!="")
			{
				$issue_ids=implode(",",array_unique(explode(",",$trimsjobid)));
				$trimsjobid_cond=" and a.job_id in($issue_ids)";
			}
			else { $trimsjobid_cond ="";}
		}
	}
		
	//production.................................
	$trims_production_sql="select a.section_id,c.sub_section,a.received_id,a.job_id,b.job_dtls_id,c.id as order_receive_dtls_id,c.item_description,c.color_id,c.size_id,c.uom,c.job_quantity,b.production_qty,b.qc_qty  from trims_production_mst a,trims_production_dtls b,trims_job_card_dtls c where a.id=b.mst_id and c.id=b.job_dtls_id $trimsjobid_cond $trimsreceiveid_cond and a.entry_form=269
	and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0"; 
	$trims_production_data_arr=array();	
	$result_trims_production_sql = sql_select($trims_production_sql);
	foreach($result_trims_production_sql as $row)
	{
		$key=$row[csf("received_id")].'*'.$row[csf("section_id")].'*'.$row[csf("sub_section")].'*'.$row[csf("item_description")].'*'.$row[csf("color_id")].'*'.$row[csf("size_id")].'*'.$row[csf("uom")];
		
		$trims_production_data_arr[$key][qc_qty]+=$row[csf("qc_qty")];
		$trims_production_data_arr[$key][job_quantity]+=$row[csf("job_quantity")];
	}	
		
	//Delivery.................................
	//$trims_delivery_sql="select a.delivery_date,a.received_id,b.delevery_qty,b.order_receive_rate,c.uom,b.section,c.sub_section,b.item_group,b.color_id,b.size_id,b.description,b.delevery_status,b.break_down_details_id,c.id from trims_delivery_mst a, trims_delivery_dtls b, trims_job_card_dtls c  where a.id=b.mst_id and c.id=b.job_dtls_id $trimsreceiveid_cond  and a.entry_form=208 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by a.delivery_date";
	if($cbo_date_category!=3) {
		$trims_delivery_sql="select a.delivery_date,a.received_id,b.delevery_qty,b.order_receive_rate,c.booked_uom as uom,b.section,c.sub_section,b.item_group,b.color_id,b.size_id,d.description,b.delevery_status,b.break_down_details_id,c.id from trims_delivery_mst a, trims_delivery_dtls b, subcon_ord_dtls c, subcon_ord_breakdown d where b.break_down_details_id=d.id and c.id=d.mst_id and  a.id=b.mst_id and c.id=b.receive_dtls_id $trimsreceiveid_cond  and a.entry_form=208 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by a.delivery_date"; 
		//,c.description,c.order_id,c.item_id,c.color_id,c.size_id ,c.qnty,c.rate ,c.amount,c.booked_qty,c.delivery_status
		$trims_delivery_data_arr=array();	
		$result_trims_delivery_sql = sql_select($trims_delivery_sql);
		foreach($result_trims_delivery_sql as $row)
		{
			$key=$row[csf("item_group")].'*'.$row[csf("received_id")].'*'.$row[csf("section")].'*'.$row[csf("sub_section")].'*'.$row[csf("description")].'*'.$row[csf("color_id")].'*'.$row[csf("size_id")].'*'.$row[csf("uom")].'*'.$row[csf("break_down_details_id")];
			$trims_delivery_data_arr[$key][delevery_qty]+=$row[csf("delevery_qty")];
			$trims_delivery_data_arr[$key][delevery_val]+=$row[csf("delevery_qty")]*$row[csf("order_receive_rate")];
			$trims_delivery_data_arr[$key][delevery_last_date]=$row[csf("delivery_date")];
			$trims_delivery_data_arr[$key][id]=$row[csf("id")];
			$trims_delivery_data_arr[$key][delevery_status]=$row[csf("delevery_status")];
			$trims_delivery_data_arr[$key][break_ids].=$row[csf("break_down_details_id")].',';
		}
	}
	
	//echo "<pre>";
	//print_r($trims_delivery_data_arr);
	//Bill.................................
	//select SECTION,ITEM_DESCRIPTION,COLOR_ID,SIZE_ID,ORDER_UOM,TOTAL_DELV_QTY,b.QUANTITY     from TRIMS_BILL_MST a, TRIMS_BILL_dtls b where a.id=b.mst_id and a.ENTRY_FORM=276	
	//bill.................................
	/*$trims_bill_sql="select d.received_id,b.section,c.sub_section,b.item_description,b.color_id,b.size_id,c.uom,total_delv_qty,b.quantity,b.bill_amount,b.id from trims_bill_mst a, trims_bill_dtls b,trims_job_card_dtls c, trims_job_card_mst d  where a.id=b.mst_id and c.id=b.job_dtls_id and c.job_no_mst=d.trims_job and a.entry_form=276 and d.received_id in(".implode(',',$trims_receive_id_arr).") and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";*/
	//$trims_bill_sql="select a.received_id,b.section,c.sub_section,b.item_description,b.color_id,b.size_id,c.uom,total_delv_qty,b.quantity,b.bill_amount,b.id from trims_bill_mst d, trims_bill_dtls b,trims_job_card_dtls c, trims_job_card_mst a  where d.id=b.mst_id and c.id=b.job_dtls_id and c.job_no_mst=a.trims_job and d.entry_form=276 $trimsreceiveid_cond and d.status_active=1 and d.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
	
	/*$trims_bill_sql="select c.mst_id as received_id,c.section,c.sub_section,b.item_description,b.color_id,b.size_id,c.booked_uom as uom ,total_delv_qty,b.quantity,b.bill_amount,b.id,b.order_no,b.challan_no from trims_bill_mst d, trims_bill_dtls b, subcon_ord_dtls c, subcon_ord_mst a where d.id=b.mst_id and c.mst_id=a.id and c.order_no=b.order_no and d.entry_form=276 and a.entry_form=255 and d.status_active=1 and d.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 ";
	$trims_bill_data_arr=array();	
	$result_trims_bill_sql = sql_select($trims_bill_sql);
	foreach($result_trims_bill_sql as $row)
	{
		$key=$row[csf("received_id")].'*'.$row[csf("section")].'*'.$row[csf("sub_section")].'*'.$row[csf("item_description")].'*'.$row[csf("color_id")].'*'.$row[csf("size_id")].'*'.$row[csf("uom")].'*'.$row[csf("challan_no")].'*'.$row[csf("order_no")];
		
		$trims_bill_data_arr[$key][bill_qty]+=$row[csf("quantity")];
		$trims_bill_data_arr[$key][bill_val]+=$row[csf("bill_amount")];
		$trims_bill_data_arr[$key][dtls_ids].=$row[csf("id")].',';
		$trims_bill_data_arr[$row[csf("order_no")]]['challan_val']=$row[csf("challan_no")];
	}*/
	// echo "<pre>";
	// print_r($trims_bill_data_arr);
	$width=2440;
	ob_start();
	?>
	<div align="center" style="height:auto; width:<? echo $width+20;?>px; margin:0 auto; padding:0;">
		<table width="<? echo $width;?>" cellpadding="0" cellspacing="0" id="caption" align="center">
			<thead class="form_caption" >
				<tr>
					<td colspan="36" align="center" style="font-size:20px;"><? echo $companyArr[$cbo_company_id]; ?></td>
				</tr>
				<tr>
					<td colspan="36" align="center" style="font-size:14px; font-weight:bold" ><? echo $report_title; ?></td>
				</tr>
				<tr>
					<td colspan="36" align="center" style="font-size:14px; font-weight:bold">
						<? echo " From : ".$txt_date_from." To : ". $txt_date_to ;?>
					</td>
				</tr>
			</thead>
		</table>
        <table align="left">
            <tr>
                <td bgcolor="#FFF" width="15">&nbsp;</td><td>Full Pending</td>
                <td bgcolor="#FFCC66" width="15">&nbsp;</td><td>Partial Deliverd</td>
                <td bgcolor="#8CD59C" width="15">&nbsp;</td><td>Full Deliverd</td>
          	</tr>
        </table>
        <table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="<? echo $width;?>" rules="all" id="rpt_table_header" align="left">
			<thead>
                <th width="35">SL</th>
                <th width="120">Order Rcv. No</th>
				<th width="100">Customer Name</th>
                <th width="100">Cust. Buyer</th>
				<th width="100">Delv. Point</th>
				<th width="100">Factory Merchant</th>
                <th width="100">Style Ref.</th>
				<th width="100">Trims Ref.</th>
				<th width="100">Trims Group</th>
                <th width="100">Item Description</th>
                <th width="100">Size</th>
				<th width="100">Color</th>
                <th width="60">Order UOM</th>
                <th width="100">Order Qty</th>
				<th width="80">Order Rcv.Date</th>
				<th width="80">Sample Submit Date</th>
				<th width="80">Sample Approved Date</th>
                <th width="80">Target Delv. Date</th>
				<th width="80">Last Delv. Date</th>
                <th width="100">Delv. Status</th>
                <th width="60">Order Source</th>
				<th width="100">Production Qty</th>
                <th width="100">Prod Bal Qty</th>
                <th width="100">Delv. Qty</th>
                <th width="100">Delv. Amount(USD)</th>
				<th width="100">Delv. Balance Qty</th>
				<th width="100">Delv. Balance Amount(USD)</th>
			</thead>
		</table>
		<div style="width:<? echo $width+18;?>px; max-height:250px; float:left; overflow-y:scroll;" id="scroll_body">
		<table border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body_id" width="<? echo $width;?>" rules="all" align="left">
            <? 
			foreach($date_array as $val){
				$rowspan[$val['subcon_job']]++;
			}

			$i=1;
			$total_order_qty=0;$total_order_val=0;$total_booked_qty=0;$total_production_qty=0;
			$checkarray=array();
			$j=0;
			foreach($date_array as $keysss=>$row)
			{
				// echo"<pre>";
				// print_r($row);
				if(!in_array($row[subcon_job],$checkarray))
				{
					$j=0;
					$checkarray[$row[subcon_job]] = $row[subcon_job];
				}
				
				list($id,$order_id,$section,$sub_section,$item_group,$item_id,$description,$color_id,$size_id,$booked_uom,$rate)=explode('*',$keysss);
				$key=$id.'*'.$section.'*'.$sub_section.'*'.$description.'*'.$color_id.'*'.$size_id.'*'.$booked_uom.'*'.chop($row[break_ids],',');
				//$production_qty_on_order_parcent=($trims_production_data_arr[$key][qc_qty]/$trims_production_data_arr[$key][job_quantity])*$row[order_qty];
				$production_qty_on_order_parcent=$trims_production_data_arr[$key][qc_qty];
				
				//WORK ORDER NO : 161
				$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
				$party=($row[within_group]==1)?$companyArr[$row[party_id]]:$buyerArr[$row[party_id]];
				$buyer_buyer=($row[within_group]==1)?$buyerArr[$row[buyer_buyer]]:$row[buyer_buyer];

			
				//$row[delivery_status]=$trims_delivery_data_arr[$item_group.'*'.$key][delevery_status];
				
				


				/*if($row[delivery_status]==2){$bgcolor="#FFCC66";}
				elseif($row[delivery_status]==3){$bgcolor="#8CD59C";}
				else{$row[delivery_status]=1;}
				*/
				//---------------------------------------
				$total_order_qty+=$row[order_qty];
				$total_order_val+=$row[order_amount];
				$total_booked_qty+=$row[booked_qty];
				$total_production_qty+=$production_qty_on_order_parcent;
				
				/*if($row["currency_id"]==1){
					//echo $row[order_rate]."=".$currency_rate;
					$row[order_rate]=number_format($row[order_rate]/$currency_rate,6);
					$row[order_amount]=$row[order_amount]/$currency_rate;
				}*/


				//echo '++'.$row[order_rate]."=".$currency_rate;
				/*$trims_delivery_data_arr[$item_group.'*'.$key][delevery_qty]+=$row[csf("delevery_qty")];
				$trims_delivery_data_arr[$item_group.'*'.$key][delevery_val]+=$row[csf("delevery_qty")]*$row[csf("order_receive_rate")];
				$trims_delivery_data_arr[$item_group.'*'.$key][delevery_last_date]=$row[csf("delivery_date")];
				$trims_delivery_data_arr[$item_group.'*'.$key][id]=$row[csf("id")];
				$trims_delivery_data_arr[$item_group.'*'.$key][delevery_status]=$row[csf("delevery_status")];
				$trims_delivery_data_arr[$item_group.'*'.$key][break_ids].=$row[csf("break_down_details_id")].',';*/
				//$delivery_amt=number_format($row[order_rate]*$trims_delivery_data_arr[$item_group.'*'.$key][delevery_qty],2);
			
				/*if($cbo_delivery_status!=0 && ($trims_delivery_data_arr[$item_group.'*'.$key][delevery_status] == $cbo_delivery_status))
				{*/
				$breakIDs=chop($row[break_ids],',');
				$breakIDs=explode(',', $breakIDs); $delivery_qty=0; $delevery_status='';
				// echo "<pre>";
				// print_r($breakIDs); 
				foreach ($breakIDs as $key => $value) {
					//echo $item_group.'*'.$id.'*'.$section.'*'.$sub_section.'*'.$description.'*'.$color_id.'*'.$size_id.'*'.$booked_uom.'*'.$value.'#';
					$delivery_qty+= $trims_delivery_data_arr[$item_group.'*'.$id.'*'.$section.'*'.$sub_section.'*'.$description.'*'.$color_id.'*'.$size_id.'*'.$booked_uom.'*'.$value][delevery_qty];
					$delevery_status.=$trims_delivery_data_arr[$item_group.'*'.$id.'*'.$section.'*'.$sub_section.'*'.$description.'*'.$color_id.'*'.$size_id.'*'.$booked_uom.'*'.$value][delevery_status].',';

				}

				$breakIDs=chop($row[break_ids],',');
				$delevery_status=chop($delevery_status,',');
				$breakIDs=explode(',', $breakIDs); $break_idss=0;
				foreach ($breakIDs as $key => $value) {
					
					$break_idss+= $trims_delivery_data_arr[$item_group.'*'.$id.'*'.$section.'*'.$sub_section.'*'.$description.'*'.$color_id.'*'.$size_id.'*'.$booked_uom.'*'.$value][break_ids];
				}
                $popupbreakIDs=implode(',', $breakIDs);
				// echo"$popupbreakIDs";
				//$id.'*'.$section.'*'.$sub_section.'*'.$description.'*'.$color_id.'*'.$size_id.'*'.$booked_uom.'*'.chop($row[break_ids],',')
				
				//$delivery_qty=number_format($delivery_qty,0);
				//echo "++".$row[order_rate]."=".$delivery_qty; 
				// $DelvBalanceQty=$row[order_qty]-$trims_delivery_data_arr[$item_group.'*'.$key][delevery_qty];
				$delivery_amt=number_format($row[order_rate]*$delivery_qty,2);
				$DelvBalanceQty=$row[order_qty]-$delivery_qty;
				
				//echo $orderAmount.'=='.$row[order_qty].'=='.$row[order_rate].'#';

				$rate=number_format($row[order_amount]/$row[order_qty],6);
				
				if($row["currency_id"]==1)
				{
					$takarate=$rate;
					$orderamounttaka=$orderamount;
					$usdrate=number_format($rate/$currency_rate,6);
					$orderamountusd=$orderamount/$currency_rate;
					$delevery_valu_taka=$delivery_qty/$row[conv_factor]*$takarate;
					$delevery_valu_usd=$delivery_qty/$row[conv_factor]*$usdrate;
					$total_delevery_valu_taka1=$delevery_quantity/$row[conv_factor]*$takarate;
					$total_delevery_valu_usd1=$delevery_quantity/$row[conv_factor]*$usdrate;
					$orderAmount=$row[order_amount]/$currency_rate;
				}
				else if($row["currency_id"]==2)
				{
					//echo $delivery_qty.'=='.
					$orderAmount=$row[order_qty]*$row[order_rate];
					$takarate=number_format($rate*$currency_rate,6);
					$orderamounttaka=$orderamount*$currency_rate;
					$usdrate=$rate;
					$orderamountusd=$orderamount;
					$delevery_valu_taka=$delivery_qty/$row[conv_factor]*$takarate;
					$delevery_valu_usd=$delivery_qty/$row[conv_factor]*$usdrate;
					$total_delevery_valu_taka1=$delevery_quantity/$row[conv_factor]*$takarate;
					$total_delevery_valu_usd1=$delevery_quantity/$row[conv_factor]*$usdrate;
				}



				/*}*/
			
				$bill_amt=number_format($row[order_rate]*$trims_bill_data_arr[$key][bill_qty],2);
				$receive_ids=implode(',',$trims_receive_id_arr);
				if($row["within_group"]==1) $orderSource='Internal'; else $orderSource='External';
				if ($row["source_for_order"]==1) $sourceOfOrder='In-House'; else $sourceOfOrder='Sub-Contract';
					//echo $delevery_status.'==';
				$deliveryStatus=$delevery_status;
				/*echo $delevery_status.'==';
				if($delevery_status !='')
				{
					$deliveryStatus=$delevery_status;
				}
				else
				{
					$deliveryStatus=chop($row[delivery_status],',');
				}*/
				
				/*$del_status=array_unique(explode(",",$deliveryStatus));
				$sts=min($del_status);
				if($sts==2){$bgcolor="#FFCC66";}
				elseif($sts==3){$bgcolor="#8CD59C";}
				else{$sts=1;}*/

				if($DelvBalanceQty<=1){
					$sts=3; $bgcolor="#8CD59C";
				}else if($DelvBalanceQty >=1 && $row[order_qty]!=$DelvBalanceQty){
					$sts=2; $bgcolor="#FFCC66";
				}else{
					$sts=1;
				}

				//echo $sts.'=='.$delevery_status.'++';
				?>
	            <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
	                <td width="35" align="center"><? echo $i;?></td>
					<?
					if($j==0)
					{
						?>
						<td width="120" align="center" align="center" rowspan="<? echo $rowspan[$row[subcon_job]];?>"><p><? echo $row[subcon_job];?></p></td>
						<?
						$j++;
					}
					?>
					<td width="100"><p><? echo $party;?></p></td>
	                <td width="100"><p><? echo $buyer_buyer;?></p></td>
					<td width="100"><p><? echo $row['delivery_point']; ?></p></td>
					<td width="100"><p><? echo $row['team_marchant']; ?></p></td>
	                <td width="100"><p><? echo $row[style];?></p></td>
					<td width="100"><p><?php echo $row[trims_ref]; ?></p></td>
					<td width="100"><p><? echo $trimsGroupArr[$row[item_group]];?></p></td>
	                <td width="100"><p><? echo $row[description];?></p></td>
	                <td width="100"><p><? echo $size_arr[$row[size_id]];?></p></td>
					<td width="100"><p><? echo $colorNameArr[$color_id];?></p></td>
	                <td width="60" align="center"><? echo $unit_of_measurement[$row[order_uom]];?></td>
	                <td width="100" align="right"><? echo number_format($row[order_qty],0); ?></td>
					<td width="80" align="center"><? echo $row[receive_date];?></td>
					<td width="80" align="center"><? echo $row[submit_date];?></td>
					<td width="80" align="center"><? echo $row[approve_date];?></td>
	                <td width="80" align="center"><? echo $row[delivery_date];?></td>
					<td width="80" align="center"><? echo $trims_delivery_data_arr[$item_group.'*'.$id.'*'.$section.'*'.$sub_section.'*'.$description.'*'.$color_id.'*'.$size_id.'*'.$booked_uom.'*'.$value][delevery_last_date];?></td>
					<td width="100" align="center"><? echo $delivery_status[$sts];?></td>
	                <td width="60" align="center"><p><? echo $sourceOfOrder;?></p></td>
					<td width="100" align="right"><? echo number_format($production_qty_on_order_parcent,0);?></td>
	                <td width="100" align="right"><? echo number_format($row[booked_qty]-$production_qty_on_order_parcent,0);?></td>
	                <?

	                if($delivery_qty !='')
	                {
	                	?><td width="100" align="right">
					<a href="##" onclick="fnc_delivery_amount_details('<? echo $popupbreakIDs;?>','<? echo $row[order_rate];?>','delivery_quantity_popup')"><p><? echo number_format($delivery_qty); ?></p></a>
					</td><?
	                }
	                else{
	                	?><td width="100" align="right"><? echo 0; ?></td><?
	                }
	               
	                ?>
	                
					<td width="100" align="right"><p><? echo number_format($delevery_valu_usd,4); $total_del_amtb +=$delevery_valu_usd;?></p></td>
					<td width="100" align="right">
						<?

						if($DelvBalanceQty<=1){
							echo 0;
						}else{
							//echo $row[order_qty].'=='.$delivery_qty;
							echo number_format($DelvBalanceQty,0);
						}
						?>
					</td>
					<td width="100" align="right"><p><?
					$balance_amt=$orderAmount-$delevery_valu_usd;
					//echo $orderAmount.'=='.$row[order_qty].'=='.$row[order_rate].'=='.$delevery_valu_usd.'=='.$balance_amt.'#';
					//500000==500000==1==5952.5==494047.5#
					if($balance_amt<=1){
							echo 0;
						}else{
							//echo $row[order_qty].'=='.$delivery_qty;
							echo number_format($balance_amt,4);
							$total_bal_del_amtb +=$balance_amt;
						}

					?></p></td>
	            </tr>
	            <? 
				$i++;
			} ?>
		</table>
		</div>
		<table width="<? echo $width;?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body_footer" align="left">
			<tfoot>
				<th width="35"></th>
                <th width="120"></th>
				<th width="100"></th>
                <th width="100"></th>
                <th width="100"></th>
				<th width="100"></th>
                <th width="100"></th>
				<th width="100"></th>
				<th width="100"></th>
                <th width="100"></th>
                <th width="100"></th>
				<th width="100"></th>
                <th width="60"></th>
                <th width="100"></th>
				<th width="80"></th>
				<th width="80"></th>
				<th width="80"></th>
                <th width="80"></th>
				<th width="80"></th>
                <th width="100"></th>
                <th width="60"></th>
				<th width="100"></th>
                <th width="100"></th>
                <th width="100"></th>
                <th width="100"><? echo number_format($total_del_amtb,4) ; ?></th>
				<th width="100"></th>
				<th width="100"><? echo number_format($total_bal_del_amtb,4) ; ?></th>
			</tfoot>
		</table>
    </div>
    <?
    $html = ob_get_contents();
    ob_clean();
    //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
    foreach (glob("*.xls") as $filename) {
    //if( @filemtime($filename) < (time()-$seconds_old) )
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, $html);
    echo "$html**$filename**$report_type";
    exit();
}
if($action=="generate_report_3")
{//chk
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	$cbo_company_id=str_replace("'","", $cbo_company_id);
	$cbo_location_name=str_replace("'","", $cbo_location_name);
	$cbo_customer_source=str_replace("'","", $cbo_customer_source);
	$cbo_customer_name=str_replace("'","", $cbo_customer_name);
	$txt_order_no=str_replace("'","", $txt_order_no);
	$internal_no=str_replace("'","", $txt_internal_no);
	$cbo_section_id=str_replace("'","", $cbo_section_id);
	$cbo_sub_section_id=str_replace("'","", $cbo_sub_section_id);
	$txt_item_description=str_replace("'","", $txt_item_description);
	$cbo_date_category=str_replace("'","", $cbo_date_category);
	$txt_date_from=str_replace("'","", $txt_date_from);
	$txt_date_to=str_replace("'","", $txt_date_to);
	$cbo_delivery_status=str_replace("'","", $cbo_delivery_status);
	$cbo_customer_buyer=str_replace("'","", $cbo_customer_buyer);
	$cbo_team_leader=str_replace("'","", $cbo_team_leader);
	$cbo_team_member=str_replace("'","", $cbo_team_member);
	$txt_style_ref=str_replace("'","", $txt_style_ref);
	$txt_buyer=str_replace("'","", $txt_buyer); 
	$cbo_wo_type_id=str_replace("'","", $cbo_wo_type_id);


	if($cbo_delivery_status== 0){$delivery_status_con="";}
	else{
		$delivery_status_con=" and c.delivery_status=$cbo_delivery_status and b.delivery_status=$cbo_delivery_status";
	}
		
	if($db_type==0) 
	{
		$txt_date_from=change_date_format($txt_date_from,'yyyy-mm-dd');
		$txt_date_to=change_date_format($txt_date_to,'yyyy-mm-dd');
	}
	else if($db_type==2) 
	{
		$txt_date_from=change_date_format($txt_date_from,'','',1);
		$txt_date_to=change_date_format($txt_date_to,'','',1);
	}
	
	$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
	$buyerArr = return_library_array("select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0 ","id","buyer_name");
	$trimsGroupArr = return_library_array("select id, item_name from lib_item_group where item_category=4 and status_active=1","id","item_name");
	$colorNameArr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0","id","color_name");
	$size_arr=return_library_array( "select id,size_name from lib_size",'id','size_name');
	$leaderArr = return_library_array("select id, team_leader_name from lib_marketing_team where status_active=1 and is_deleted=0 and project_type=3","id","team_leader_name");
	$memberArr = return_library_array("select id, team_member_name from lib_mkt_team_member_info","id","team_member_name");

	$currency_rate=return_field_value( "conversion_rate", "currency_conversion_rate", "currency=2 and status_active=1  and id in(select max(id) from currency_conversion_rate where currency=2 and is_deleted=0 and status_active=1)" , "conversion_rate" );
	
	if($txt_date_from!="" and $txt_date_to!=""){	
		if($cbo_date_category==1){$where_con.=" and a.receive_date between '$txt_date_from' and '$txt_date_to'";}	
		else if($cbo_date_category==2){$where_con.=" and a.delivery_date between '$txt_date_from' and '$txt_date_to'";}
		else if($cbo_date_category==3){$date_con.=" and a.delivery_date between '$txt_date_from' and '$txt_date_to'";}
	}
	if($cbo_date_category==3){
		if($cbo_section_id){$where_con_ord.=" and b.section='$cbo_section_id'";} 
		if($cbo_sub_section_id){$where_con_ord.=" and c.sub_section='$cbo_sub_section_id'";} 
		if($txt_item_description){$where_con_ord.=" and d.description like '%$txt_item_description%'";}
		//if($cbo_customer_source){$where_con_ord.=" and a.within_group='$cbo_customer_source'";} 
		//if($cbo_customer_name){$where_con_ord.=" and a.party_id='$cbo_customer_name'";} 

	}
	if($cbo_section_id){$where_con.=" and b.section='$cbo_section_id'";} 
	if($cbo_sub_section_id){$where_con.=" and b.sub_section='$cbo_sub_section_id'";} 
	if($cbo_customer_source){$where_con.=" and a.within_group='$cbo_customer_source'";} 
	if($cbo_customer_name){$where_con.=" and a.party_id='$cbo_customer_name'";} 
	if($cbo_customer_buyer){$where_con.=" and b.buyer_buyer='$cbo_customer_buyer'";} 
	if($cbo_team_leader){$where_con.=" and a.team_leader='$cbo_team_leader'";} 
	if($cbo_team_member){$where_con.=" and a.team_member='$cbo_team_member'";}
	if($cbo_location_name){$where_con.=" and a.location_id = $cbo_location_name";}
	if($cbo_wo_type_id){$where_con.=" and a.wo_type = $cbo_wo_type_id";}
	if($txt_item_description){$where_con.=" and c.description like '%$txt_item_description%'";}
	
	
	//echo $internal_no;
 	$buyer_po_id_cond = '';
    if($cbo_customer_source==1 || $cbo_customer_source==0)
    {
		if($internal_no !="") $internal_no_cond = " and grouping like('%$internal_no%')";
		
		$buyer_po_arr=array();
		$buyer_po_id_arr=array();
		$po_sql ="Select id,grouping from  wo_po_break_down  where is_deleted=0 and status_active=1 $internal_no_cond "; 
		$po_sql_res=sql_select($po_sql);
		foreach ($po_sql_res as $row)
		{
			$buyer_po_arr[$row[csf("id")]]['grouping']=$row[csf("grouping")];
			$buyer_po_id_arr[]=$row[csf("id")];
		}
		unset($po_sql_res);
        //$buyer_po_lib = return_library_array("select id, id as id2 from wo_po_break_down where grouping like '%$internal_no%'",'id','id2');
		if($internal_no !="")
		{
			$buyer_po_id = implode(",", $buyer_po_id_arr);
			if ($buyer_po_id=="")
			{
				echo "Not Found."; die;
			}
       	 	if($buyer_po_id !="") $buyer_po_id_cond = " and b.buyer_po_id in($buyer_po_id)";
		}
    }

    $buyer_po_id_cond = '';
    if($cbo_date_category==3)
    {
    	$con = connect();
    	/*$trims_delivery_sql="select a.delivery_date,a.received_id,b.delevery_qty,b.order_receive_rate,c.booked_uom as uom,b.section,c.sub_section,b.item_group,b.color_id,b.size_id,b.description,b.delevery_status,b.break_down_details_id,c.id from trims_delivery_mst a, trims_delivery_dtls b, subcon_ord_dtls c where a.id=b.mst_id and c.id=b.receive_dtls_id $where_con_ord $date_con and a.entry_form=208  and a.company_id =$cbo_company_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by a.delivery_date"; */

    	$trims_delivery_sql="select a.delivery_date,a.received_id,b.delevery_qty,b.order_receive_rate,c.booked_uom as uom,b.section,c.sub_section,b.item_group,b.color_id,b.size_id,d.description,b.delevery_status,c.buyer_po_no,b.break_down_details_id,c.id from trims_delivery_mst a, trims_delivery_dtls b, subcon_ord_dtls c, subcon_ord_breakdown d where b.break_down_details_id=d.id and c.id=d.mst_id and  a.id=b.mst_id and c.id=b.receive_dtls_id  $where_con_ord $date_con  and a.company_id =$cbo_company_id and a.entry_form=208 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by a.delivery_date"; 
    	//and a.company_id =$cbo_company_id  
    	$trims_delivery_data_arr=array();	
		$result_trims_delivery_sql = sql_select($trims_delivery_sql);
		foreach($result_trims_delivery_sql as $row)
		{
			$key=$row[csf("item_group")].'*'.$row[csf("buyer_po_no")].'*'.$row[csf("received_id")].'*'.$row[csf("section")].'*'.$row[csf("sub_section")].'*'.$row[csf("description")].'*'.$row[csf("color_id")].'*'.$row[csf("size_id")].'*'.$row[csf("uom")].'*'.$row[csf("break_down_details_id")];
			//$key=$id.'*'.$section.'*'.$sub_section.'*'.$description.'*'.$color_id.'*'.$size_id.'*'.$booked_uom.'*'.chop($row[break_ids],',');
			//c.id
			
			$trims_delivery_data_arr[$key][delevery_qty]+=$row[csf("delevery_qty")];
			$trims_delivery_data_arr[$key][delevery_val]+=$row[csf("delevery_qty")]*$row[csf("order_receive_rate")];
			$trims_delivery_data_arr[$key][delevery_last_date]=$row[csf("delivery_date")];
			$trims_delivery_data_arr[$key][id]=$row[csf("id")];
			$trims_delivery_data_arr[$key][delevery_status]=$row[csf("delevery_status")];
			$trims_delivery_data_arr[$key][break_ids].=$row[csf("break_down_details_id")].',';
			$receive_id_arr[$row[csf("received_id")]]=$row[csf("received_id")];
			$break_down_details_ids.=$row[csf("break_down_details_id")].',';
		}
		
		$receive_id=implode(',',$receive_id_arr);
		$receive_brk_id=implode(',',$receive_brk_id_arr);
		if($receive_id!='')
		{
			$trimsreceiveid=chop($receive_id,','); 
			
			$reiveid_id_cond=""; $reiveid_brk_id_cond="";
			$trimsreceive_ids=count(array_unique(explode(",",$trimsreceiveid)));
			$breakDownIds=chop($break_down_details_ids,',');
			$breakDownIds_arr=array_unique(explode(",",$breakDownIds));
			//print_r($breakDownIds_arr);
			$breakDownId_count=count($breakDownIds_arr);
			//echo  $breakDownId_count;
			if($db_type==2 && $trimsreceive_ids>1000)
			{
				$reiveid_id_cond=" and (";
				$trimsreceiveidArr=array_chunk(explode(",",$trimsreceiveid),999);
				foreach($trimsreceiveidArr as $ids)
				{
					$ids=implode(",",$ids);
					$reiveid_id_cond.=" a.id in($ids) or"; 
				}
				$reiveid_id_cond=chop($reiveid_id_cond,'or ');
				$reiveid_id_cond.=")";
			}
			else
			{
				if($trimsreceiveid!="")
				{
					$issue_ids=implode(",",array_unique(explode(",",$trimsreceiveid)));
					$reiveid_id_cond=" and a.id in($issue_ids)";
				}
				else { $reiveid_id_cond ="";}
			}

			foreach($breakDownIds_arr as $val)
			{

				$issue_row_id=$val;
				if($issue_row_id!=0)
				{
					//$issue_dtls_id=$row[csf('issue_dtls_id')];
					$r_id2=execute_query("insert into tmp_poid (userid, poid) values ($user_id,$issue_row_id)");
					//echo $r_id2; die;
					//if($issue_dtls_id=="") $issue_dtls_id=$row[csf('issue_dtls_id')];else $issue_dtls_id.=",".$row[csf('issue_dtls_id')];
				}
			}
			if($breakDownId_count>0){
				$reiveid_brk_id_cond=" and c.id=d.poid";
				$reiveid_brk_table_cond=" ,tmp_poid d";
			}

			/*if($db_type==2 && $breakDownId_count>1000)
			{
				$reiveid_brk_id_cond=" and (";
				$breakDownIds_arr=array_chunk(explode(",",$breakDownIds),999);
				foreach($breakDownIds_arr as $ids)
				{
					$ids=implode(",",$ids);
					$reiveid_brk_id_cond.=" c.id in($ids) or"; 
				}
				$reiveid_brk_id_cond=chop($reiveid_brk_id_cond,'or ');
				$reiveid_brk_id_cond.=")";
			}
			else
			{
				$reiveid_brk_id_cond=" and c.id in($breakDownIds)";
			}*/

			/*if($db_type==2 && $breakDownId_count>1000)
			{
				//echo "15**"; die;
				$reiveid_brk_id_cond=" and (";
				//$trimsBreakreceiveidArr=array_chunk(explode(",",$trimsBreakreceiveid),999);
				foreach($breakDownIds_arr as $ids)
				{
					
					$ids=implode(",",$ids);
					//echo $ids.'__'.$id.'==';
					echo $ids.'==';
					$reiveid_brk_id_cond.=" c.id in($ids) or";
					//echo $ids.'**' ;
				}
				$reiveid_brk_id_cond=chop($reiveid_brk_id_cond,'or ');
				$reiveid_brk_id_cond.=")";
			}
			else
			{
				if($breakDownIds_arr!="")
				{
					$issue_ids=implode(",",$breakDownIds_arr);
					$reiveid_brk_id_cond=" and c.id in($issue_ids)";
				}
				else { $reiveid_brk_id_cond ="";}
			}*/
		}
    }
    if($db_type==0)
	{
		if($r_id2==1 && $r_id3==1)
		{
			mysql_query("COMMIT");  
		}
	}
	if($db_type==2 || $db_type==1 )
	{
		//echo $r_id2; die;
		if($r_id2==1 && $r_id3==1)
		{
			oci_commit($con);  
		}
	}
    //echo $reiveid_brk_id_cond;

    if($cbo_date_category==3 && $reiveid_brk_id_cond==''){
    	echo "No Data Found"; die;
    }

   // echo $reiveid_brk_id_cond;
	//echo $buyer_po_id_cond; die; //print_r($buyer_po_arr); die;
	
	if($txt_order_no !="") $order_no_cond = " and b.order_no like('%$txt_order_no%')";
	if($txt_style_ref !="") $style_cond = " and c.style like('%$txt_style_ref%')";
	if($txt_buyer !="") $buyer_cond1 = " and b.buyer_buyer like('%$txt_buyer%')";
	$trims_order_sql="select a.id,a.subcon_job,a.receive_date,a.delivery_date,a.order_no as cust_order_no,a.party_id,a.within_group,a.subcon_job,a.currency_id,a.team_leader,a.team_member,a.team_marchant,b.item_group,b.buyer_buyer,b.section,b.sub_section,b.job_no_mst,b.order_no,b.booked_uom,b.order_uom,b.buyer_po_no,b.buyer_po_id,b.source_for_order,b.submit_date,b.approve_date,c.id as break_id,c.description,c.order_id,c.item_id,c.color_id,c.size_id ,c.qnty,c.rate ,c.amount,c.booked_qty,c.delivery_status,c.style, a.trims_ref,a.delivery_point,b.booked_conv_fac as conv_factor,a.wo_type
	from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c $reiveid_brk_table_cond
	where a.subcon_job=b.job_no_mst and b.job_no_mst=c.job_no_mst and b.id=c.mst_id and a.entry_form=255 and a.company_id =$cbo_company_id  $buyer_po_id_cond  $order_no_cond $style_cond $buyer_cond1 $where_con $delivery_status_con $reiveid_id_cond $reiveid_brk_id_cond
	and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1
	ORDER BY a.subcon_job";
	//echo $trims_order_sql;//die;
	$trims_receive_id_arr=array();
	$trims_data_arr=array();	
	$result_trims_order_sql = sql_select($trims_order_sql);
	foreach($result_trims_order_sql as $row)
	{
		$key=$row[csf("id")].'*'.$row[csf("order_id")].'*'.$row[csf("section")].'*'.$row[csf("sub_section")].'*'.$row[csf("item_group")].'*'.$row[csf("buyer_po_no")].'*'.$row[csf("item_id")].'*'.$row[csf("description")].'*'.$row[csf("color_id")].'*'.$row[csf("size_id")].'*'.$row[csf("booked_uom")].'*'.$row[csf("rate")].'*'.$row[csf("style")].'*'.$row[csf('wo_type')]; 
		
		$trims_data_arr[$key][qnty]+=$row[csf("qnty")];
		$trims_data_arr[$key][amount]+=$row[csf("amount")];
		$trims_data_arr[$key][booked_qty]+=$row[csf("booked_qty")];
		$trims_data_arr[$key][break_ids].=$row[csf("break_id")].',';
		$trims_data_arr[$key][delivery_status].=$row[csf("delivery_status")].',';
		
		$date_array[$key]=array(
		subcon_job=>$row[csf("subcon_job")],
		item_group=>$row[csf("item_group")],
		buyer_po_no=>$row[csf("buyer_po_no")],
		receive_date=>$row[csf("receive_date")],
		delivery_date=>$row[csf("delivery_date")],
		order_qty=>$trims_data_arr[$key][qnty],
		order_rate=>$row[csf("rate")],
		order_amount=>$trims_data_arr[$key][amount],
		booked_qty=>$trims_data_arr[$key][booked_qty],
		cust_order_no=>$row[csf("cust_order_no")],
		party_id=>$row[csf("party_id")],
		within_group=>$row[csf("within_group")],
		buyer_buyer=>$row[csf("buyer_buyer")],
		section=>$row[csf("section")],
		sub_section=>$row[csf("sub_section")],
		booked_uom=>$row[csf("booked_uom")],
		order_uom=>$row[csf("order_uom")],
		description=>$row[csf("description")],
		//delivery_status=>$row[csf("delivery_status")],
		delivery_status=>$trims_data_arr[$key][delivery_status],
		order_no=>$row[csf("order_no")],
		buyer_po_no=>$row[csf("buyer_po_no")],
		currency_id=>$row[csf("currency_id")],
		team_leader=>$row[csf("team_leader")],
		team_member=>$row[csf("team_member")],
		team_marchant=>$row[csf("team_marchant")],
		break_ids=>$trims_data_arr[$key][break_ids],
		buyer_po_id=>$row[csf("buyer_po_id")],
		trims_ref=>$row[csf("trims_ref")],
		style=>$row[csf("style")],
		size_id=>$row[csf("size_id")],
		challan_no=>$row[csf("challan_no")],
		source_for_order=>$row[csf('source_for_order')],
		submit_date=>$row[csf('submit_date')],
		approve_date=>$row[csf('approve_date')],
		delivery_point=>$row[csf('delivery_point')],
		conv_factor=>$row[csf('conv_factor')],
		wo_type=>$row[csf('wo_type')],
		
		);
		$trims_receive_id_arr[$row[csf("id")]]=$row[csf("id")];
	}
	$trims_receive_id=implode(',',$trims_receive_id_arr);
	if($trims_receive_id!='')
	{
		$trimsreceiveid=chop($trims_receive_id,','); 
		$trimsreceiveid_cond="";
		$trimsreceive_ids=count(array_unique(explode(",",$trimsreceiveid)));
		if($db_type==2 && $trimsreceive_ids>1000)
		{
			$trimsreceiveid_cond=" and (";
			$trimsreceiveidArr=array_chunk(explode(",",$trimsreceiveid),999);
			foreach($trimsreceiveidArr as $ids)
			{
				$ids=implode(",",$ids);
				$trimsreceiveid_cond.=" a.received_id in($ids) or"; 
			}
			$trimsreceiveid_cond=chop($trimsreceiveid_cond,'or ');
			$trimsreceiveid_cond.=")";
		}
		else
		{
			if($trimsreceiveid!="")
			{
				$issue_ids=implode(",",array_unique(explode(",",$trimsreceiveid)));
				$trimsreceiveid_cond=" and a.received_id in($issue_ids)";
			}
			else { $trimsreceiveid_cond ="";}
		}
	}
		
	//Job-------------------------------	
	$trims_job_sql="select a.id,a.trims_job,a.received_no from trims_job_card_mst a where a.status_active=1 and a.is_deleted=0 $trimsreceiveid_cond "; 
	$trims_job_no_arr=array();	
	$result_trims_job_sql = sql_select($trims_job_sql);
	foreach($result_trims_job_sql as $row)
	{
		$trims_job_no_arr[$row[csf("received_no")]]=$row[csf("trims_job")];
		$trims_job_id_arr[$row[csf("id")]]=$row[csf("id")];
	}

	$trims_job_id=implode(',',$trims_job_id_arr);
	if($trims_job_id!='')
	{
		
		$trimsjobid=chop($trims_job_id,','); 
		$trimsjobid_cond="";
		$trimsjobids=count(array_unique(explode(",",$trimsjobid)));
		
		if($db_type==2 && $trimsjobids>1000)
		{
			$trimsjobid_cond=" and (";
			$trimsreceiveidArr=array_chunk(explode(",",$trimsjobid),999);
			foreach($trimsreceiveidArr as $jobids)
			{
				$jobids=implode(",",$jobids);
				$trimsjobid_cond.=" a.job_id in($jobids) or"; 
			}
			$trimsjobid_cond=chop($trimsjobid_cond,'or ');
			$trimsjobid_cond.=")";
		}
		else
		{
			if($trimsjobid!="")
			{
				$issue_ids=implode(",",array_unique(explode(",",$trimsjobid)));
				$trimsjobid_cond=" and a.job_id in($issue_ids)";
			}
			else { $trimsjobid_cond ="";}
		}
	}
		
	//production.................................
	$trims_production_sql="select a.section_id,c.sub_section,a.received_id,a.job_id,b.job_dtls_id,c.id as order_receive_dtls_id,c.item_description,c.color_id,c.size_id,c.uom,c.job_quantity,b.production_qty,b.qc_qty  from trims_production_mst a,trims_production_dtls b,trims_job_card_dtls c where a.id=b.mst_id and c.id=b.job_dtls_id $trimsjobid_cond $trimsreceiveid_cond and a.entry_form=269
	and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0"; 

	//echo $trims_production_sql; die;
	$trims_production_data_arr=array();	
	$result_trims_production_sql = sql_select($trims_production_sql);
	foreach($result_trims_production_sql as $row)
	{
		$key=$row[csf("received_id")].'*'.$row[csf("section_id")].'*'.$row[csf("sub_section")].'*'.$row[csf("item_description")].'*'.$row[csf("color_id")].'*'.$row[csf("size_id")].'*'.$row[csf("uom")];
		
		$trims_production_data_arr[$key][qc_qty]+=$row[csf("qc_qty")];
		$trims_production_data_arr[$key][job_quantity]+=$row[csf("job_quantity")];
	}	

	//echo "<pre>";
	//print_r($trims_production_data_arr); die;
		
	//Delivery.................................
	//$trims_delivery_sql="select a.delivery_date,a.received_id,b.delevery_qty,b.order_receive_rate,c.uom,b.section,c.sub_section,b.item_group,b.color_id,b.size_id,b.description,b.delevery_status,b.break_down_details_id,c.id from trims_delivery_mst a, trims_delivery_dtls b, trims_job_card_dtls c  where a.id=b.mst_id and c.id=b.job_dtls_id $trimsreceiveid_cond  and a.entry_form=208 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by a.delivery_date";
	if($cbo_date_category!=3) {
		$trims_delivery_sql="select a.delivery_date,a.received_id,b.delevery_qty,b.order_receive_rate,c.booked_uom as uom,c.section,c.sub_section,b.item_group,d.color_id,d.size_id,d.description,b.delevery_status,c.buyer_po_no,b.break_down_details_id,c.id from trims_delivery_mst a, trims_delivery_dtls b, subcon_ord_dtls c, subcon_ord_breakdown d where b.break_down_details_id=d.id and c.id=d.mst_id and  a.id=b.mst_id and c.id=b.receive_dtls_id $trimsreceiveid_cond  and a.entry_form=208 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by a.delivery_date"; 
		//,c.description,c.order_id,c.item_id,c.color_id,c.size_id ,c.qnty,c.rate ,b.section,,c.amount,c.booked_qty,c.delivery_status
		$trims_delivery_data_arr=array();	
		$result_trims_delivery_sql = sql_select($trims_delivery_sql);
		foreach($result_trims_delivery_sql as $row)
		{
			$key=$row[csf("item_group")].'*'.$row[csf("buyer_po_no")].'*'.$row[csf("received_id")].'*'.$row[csf("section")].'*'.$row[csf("sub_section")].'*'.$row[csf("description")].'*'.$row[csf("color_id")].'*'.$row[csf("size_id")].'*'.$row[csf("uom")].'*'.$row[csf("break_down_details_id")];
			$trims_delivery_data_arr[$key][delevery_qty]+=$row[csf("delevery_qty")];
			$trims_delivery_data_arr[$key][delevery_val]+=$row[csf("delevery_qty")]*$row[csf("order_receive_rate")];
			$trims_delivery_data_arr[$key][delevery_last_date]=$row[csf("delivery_date")];
			$trims_delivery_data_arr[$key][id]=$row[csf("id")];
			$trims_delivery_data_arr[$key][delevery_status]=$row[csf("delevery_status")];
			$trims_delivery_data_arr[$key][break_ids].=$row[csf("break_down_details_id")].',';
		}
	}
	
	//echo "<pre>";
	//print_r($trims_delivery_data_arr);
	
	// echo "<pre>";
	// print_r($trims_bill_data_arr);
	$width=2640;
	ob_start();
	?>
	<div align="center" style="height:auto; width:<? echo $width+20;?>px; margin:0 auto; padding:0;">
		<table width="<? echo $width;?>" cellpadding="0" cellspacing="0" id="caption" align="center">
			<thead class="form_caption" >
				<tr>
					<td colspan="37" align="center" style="font-size:20px;"><? echo $companyArr[$cbo_company_id]; ?></td>
				</tr>
				<tr>
					<td colspan="37" align="center" style="font-size:14px; font-weight:bold" ><? echo $report_title; ?></td>
				</tr>
				<tr>
					<td colspan="37" align="center" style="font-size:14px; font-weight:bold">
						<? echo " From : ".$txt_date_from." To : ". $txt_date_to ;?>
					</td>
				</tr>
			</thead>
		</table>
        <table align="left">
            <tr>
                <td bgcolor="#FFF" width="15">&nbsp;</td><td>Full Pending</td>
                <td bgcolor="#FFCC66" width="15">&nbsp;</td><td>Partial Deliverd</td>
                <td bgcolor="#8CD59C" width="15">&nbsp;</td><td>Full Deliverd</td>
          	</tr>
        </table>
        <table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="<? echo $width;?>" rules="all" id="rpt_table_header" align="left">
			<thead>
                <th width="35">SL</th>
                <th width="120">Order Rcv. No</th>
				<th width="100">WO Type</th> 
				<th width="100">Customer Name</th>
                <th width="100">Cust. Buyer</th>
				<th width="100">Delv. Point</th>
				<th width="100">Factory Merchant</th>
				<th width="100">Buyer's PO</th>
                <th width="100">Style Ref.</th>
				<th width="100">Trims Ref.</th>
				<th width="100">Trims Group</th>
                <th width="100">Item Description</th>
                <th width="100">Size</th>
				<th width="100">Color</th>
                <th width="60">Order UOM</th>
                <th width="100">Order Qty</th>
				<th width="80">Order Rcv.Date</th>
				<th width="80">Sample Submit Date</th>
				<th width="80">Sample Approved Date</th>
                <th width="80">Target Delv. Date</th>
				<th width="80">Last Delv. Date</th>
                <th width="100">Delv. Status</th>
                <th width="60">Order Source</th>
				<th width="100">Production Qty</th>
                <th width="100">Prod Bal Qty</th>
                <th width="100">Delv. Qty</th>
                <th width="100">Delv. Amount(USD)</th>
				<th width="100">Delv. Balance Qty</th>
				<th width="100">Delv. Balance Amount(USD)</th>
			</thead>
		</table>
		<div style="width:<? echo $width+18;?>px; max-height:250px; float:left; overflow-y:scroll;" id="scroll_body">
		<table border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body_id" width="<? echo $width;?>" rules="all" align="left">
            <? 
			foreach($date_array as $val){
				$rowspan[$val['subcon_job']]++;
			}

			$i=1;
			$total_order_qty=0;$total_order_val=0;$total_booked_qty=0;$total_production_qty=0;
			$checkarray=array();
			$j=0;
			foreach($date_array as $keysss=>$row)
			{
				// echo"<pre>";
				// print_r($row);
				if(!in_array($row[subcon_job],$checkarray))
				{
					$j=0;
					$checkarray[$row[subcon_job]] = $row[subcon_job];
				}
				
				list($id,$order_id,$section,$sub_section,$item_group,$buyer_po_no,$item_id,$description,$color_id,$size_id,$booked_uom,$rate)=explode('*',$keysss);
				//$key=$id.'*'.$section.'*'.$sub_section.'*'.$description.'*'.$color_id.'*'.$size_id.'*'.$booked_uom.'*'.chop($row[break_ids],',');

				$key=$id.'*'.$section.'*'.$sub_section.'*'.$description.'*'.$color_id.'*'.$size_id.'*'.$booked_uom;
				$key=trim($key);



				//echo $id.'*'.$section.'*'.$sub_section.'*'.$description.'*'.$color_id.'*'.$size_id.'*'.$booked_uom.'*'.chop($row[break_ids],','); 


				//$production_qty_on_order_parcent=($trims_production_data_arr[$key][qc_qty]/$trims_production_data_arr[$key][job_quantity])*$row[order_qty];
				$production_qty_on_order_parcent=$trims_production_data_arr[$key][qc_qty];
				
				//WORK ORDER NO : 161
				$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
				$party=($row[within_group]==1)?$companyArr[$row[party_id]]:$buyerArr[$row[party_id]];
				$buyer_buyer=($row[within_group]==1)?$buyerArr[$row[buyer_buyer]]:$row[buyer_buyer];

			
				//$row[delivery_status]=$trims_delivery_data_arr[$item_group.'*'.$key][delevery_status];
				
				


				/*if($row[delivery_status]==2){$bgcolor="#FFCC66";}
				elseif($row[delivery_status]==3){$bgcolor="#8CD59C";}
				else{$row[delivery_status]=1;}
				*/
				//---------------------------------------
				$total_order_qty+=$row[order_qty];
				$total_order_val+=$row[order_amount];
				$total_booked_qty+=$row[booked_qty];
				$total_production_qty+=$production_qty_on_order_parcent;
				
				/*if($row["currency_id"]==1){
					//echo $row[order_rate]."=".$currency_rate;
					$row[order_rate]=number_format($row[order_rate]/$currency_rate,6);
					$row[order_amount]=$row[order_amount]/$currency_rate;
				}*/


				//echo '++'.$row[order_rate]."=".$currency_rate;
				/*$trims_delivery_data_arr[$item_group.'*'.$key][delevery_qty]+=$row[csf("delevery_qty")];
				$trims_delivery_data_arr[$item_group.'*'.$key][delevery_val]+=$row[csf("delevery_qty")]*$row[csf("order_receive_rate")];
				$trims_delivery_data_arr[$item_group.'*'.$key][delevery_last_date]=$row[csf("delivery_date")];
				$trims_delivery_data_arr[$item_group.'*'.$key][id]=$row[csf("id")];
				$trims_delivery_data_arr[$item_group.'*'.$key][delevery_status]=$row[csf("delevery_status")];
				$trims_delivery_data_arr[$item_group.'*'.$key][break_ids].=$row[csf("break_down_details_id")].',';*/
				//$delivery_amt=number_format($row[order_rate]*$trims_delivery_data_arr[$item_group.'*'.$key][delevery_qty],2);
			
				/*if($cbo_delivery_status!=0 && ($trims_delivery_data_arr[$item_group.'*'.$key][delevery_status] == $cbo_delivery_status))
				{*/
				$breakIDs=chop($row[break_ids],',');
				$breakIDs=explode(',', $breakIDs); $delivery_qty=0; $delevery_status='';
				// echo "<pre>";
				// print_r($breakIDs); 
				foreach ($breakIDs as $key => $value) {
					//echo $item_group.'*'.$id.'*'.$section.'*'.$sub_section.'*'.$description.'*'.$color_id.'*'.$size_id.'*'.$booked_uom.'*'.$value.'#';
					$delivery_qty+= $trims_delivery_data_arr[$item_group.'*'.$buyer_po_no.'*'.$id.'*'.$section.'*'.$sub_section.'*'.$description.'*'.$color_id.'*'.$size_id.'*'.$booked_uom.'*'.$value][delevery_qty];
					$delevery_status.=$trims_delivery_data_arr[$item_group.'*'.$buyer_po_no.'*'.$id.'*'.$section.'*'.$sub_section.'*'.$description.'*'.$color_id.'*'.$size_id.'*'.$booked_uom.'*'.$value][delevery_status].',';

				}

				$breakIDs=chop($row[break_ids],',');
				$delevery_status=chop($delevery_status,',');
				//echo $delevery_status; die;
				$breakIDs=explode(',', $breakIDs); $break_idss=0;
				foreach ($breakIDs as $key => $value) {
					
					$break_idss+= $trims_delivery_data_arr[$item_group.'*'.$buyer_po_no.'*'.$id.'*'.$section.'*'.$sub_section.'*'.$description.'*'.$color_id.'*'.$size_id.'*'.$booked_uom.'*'.$value][break_ids];
				}
                $popupbreakIDs=implode(',', $breakIDs);
				// echo"$popupbreakIDs";
				//$id.'*'.$section.'*'.$sub_section.'*'.$description.'*'.$color_id.'*'.$size_id.'*'.$booked_uom.'*'.chop($row[break_ids],',')
				
				//$delivery_qty=number_format($delivery_qty,0);
				//echo "++".$row[order_rate]."=".$delivery_qty; 
				// $DelvBalanceQty=$row[order_qty]-$trims_delivery_data_arr[$item_group.'*'.$key][delevery_qty];
				$delivery_amt=number_format($row[order_rate]*$delivery_qty,2);
				$DelvBalanceQty=$row[order_qty]-$delivery_qty;
				
				//echo $orderAmount.'=='.$row[order_qty].'=='.$row[order_rate].'#';

				$rate=number_format($row[order_amount]/$row[order_qty],6);
				
				if($row["currency_id"]==1)
				{
					$takarate=$rate;
					$orderamounttaka=$orderamount;
					$usdrate=number_format($rate/$currency_rate,6);
					$orderamountusd=$orderamount/$currency_rate;
					$delevery_valu_taka=$delivery_qty/$row[conv_factor]*$takarate;
					$delevery_valu_usd=$delivery_qty/$row[conv_factor]*$usdrate;
					$total_delevery_valu_taka1=$delevery_quantity/$row[conv_factor]*$takarate;
					$total_delevery_valu_usd1=$delevery_quantity/$row[conv_factor]*$usdrate;
					$orderAmount=$row[order_amount]/$currency_rate;
				}
				else if($row["currency_id"]==2)
				{
					//echo $delivery_qty.'=='.
					$orderAmount=$row[order_qty]*$row[order_rate];
					$takarate=number_format($rate*$currency_rate,6);
					$orderamounttaka=$orderamount*$currency_rate;
					$usdrate=$rate;
					$orderamountusd=$orderamount;
					$delevery_valu_taka=$delivery_qty/$row[conv_factor]*$takarate;
					$delevery_valu_usd=$delivery_qty/$row[conv_factor]*$usdrate;
					$total_delevery_valu_taka1=$delevery_quantity/$row[conv_factor]*$takarate;
					$total_delevery_valu_usd1=$delevery_quantity/$row[conv_factor]*$usdrate;
				}



				/*}*/
			
				$bill_amt=number_format($row[order_rate]*$trims_bill_data_arr[$key][bill_qty],2);
				$receive_ids=implode(',',$trims_receive_id_arr);
				if($row["within_group"]==1) $orderSource='Internal'; else $orderSource='External';
				if ($row["source_for_order"]==1) $sourceOfOrder='In-House'; else $sourceOfOrder='Sub-Contract';
					//echo $delevery_status.'==';
				$deliveryStatus=$delevery_status;
				/*echo $delevery_status.'==';
				if($delevery_status !='')
				{
					$deliveryStatus=$delevery_status;
				}
				else
				{
					$deliveryStatus=chop($row[delivery_status],',');
				}*/
				
				/*$del_status=array_unique(explode(",",$deliveryStatus));
				$sts=min($del_status);
				if($sts==2){$bgcolor="#FFCC66";}
				elseif($sts==3){$bgcolor="#8CD59C";}
				else{$sts=1;}*/

				if($DelvBalanceQty<=1){
					$sts=3; $bgcolor="#8CD59C";
				}else if($DelvBalanceQty >=1 && $row[order_qty]!=$DelvBalanceQty){
					$sts=2; $bgcolor="#FFCC66";
				}else{
					$sts=1;
				}


				//echo '<pre>'.$sts.'=='.$delivery_status[$sts].'=='.$DelvBalanceQty.'=='.$row[order_qty].'</pre>';
				?>
	            <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
	                <td width="35" align="center"><? echo $i;?></td>
	                <td width="120" align="center" ><p><? echo $row[subcon_job];?></p></td>
					<td width="100"><p><? echo $wo_type_arr[$row[wo_type]];?></p></td>
					<td width="100"><p><? echo $party;?></p></td>
	                <td width="100"><p><? echo $buyer_buyer;?></p></td>
					<td width="100"><p><? echo $row['delivery_point']; ?></p></td>
					<td width="100"><p><? echo $row['team_marchant']; ?></p></td>
	                <td width="100"><p><? echo $row[buyer_po_no];?></p></td>
	                <td width="100"><p><? echo $row[style];?></p></td>
					<td width="100"><p><?php echo $row[trims_ref]; ?></p></td>
					<td width="100"><p><? echo $trimsGroupArr[$row[item_group]];?></p></td>
	                <td width="100"><p><? echo $row[description];?></p></td>
	                <td width="100"><p><? echo $size_arr[$row[size_id]];?></p></td>
					<td width="100"><p><? echo $colorNameArr[$color_id];?></p></td>
	                <td width="60" align="center"><? echo $unit_of_measurement[$row[order_uom]];?></td>
	                <td width="100" align="right"><? echo number_format($row[order_qty],0); ?></td>
					<td width="80" align="center"><? echo $row[receive_date];?></td>
					<td width="80" align="center"><? echo $row[submit_date];?></td>
					<td width="80" align="center"><? echo $row[approve_date];?></td>
	                <td width="80" align="center"><? echo $row[delivery_date];?></td>
					<td width="80" align="center"><? echo $trims_delivery_data_arr[$item_group.'*'.$buyer_po_no.'*'.$id.'*'.$section.'*'.$sub_section.'*'.$description.'*'.$color_id.'*'.$size_id.'*'.$booked_uom.'*'.$value][delevery_last_date];?></td>
					<td width="100" align="center"><? echo $delivery_status[$sts];?></td>
	                <td width="60" align="center"><p><? echo $sourceOfOrder;?></p></td>
					<td width="100" align="right"><? echo number_format($production_qty_on_order_parcent,0);?></td>
	                <td width="100" align="right"><? echo number_format($row[booked_qty]-$production_qty_on_order_parcent,0);?></td>
	                <?

	                if($delivery_qty !='')
	                {
	                	?><td width="100" align="right">
					<a href="##" onclick="fnc_delivery_amount_details('<? echo $popupbreakIDs;?>','<? echo $row[order_rate];?>','delivery_quantity_popup')"><p><? echo number_format($delivery_qty); ?></p></a>
					</td><?
	                }
	                else{
	                	?><td width="100" align="right"><? echo 0; ?></td><?
	                }
	               
	                ?>
	                
					<td width="100" align="right"><p><? echo number_format($delevery_valu_usd,4); $total_del_amtb +=$delevery_valu_usd;?></p></td>
					<td width="100" align="right">
						<?

						if($DelvBalanceQty<=1){
							echo 0;
						}else{
							//echo $row[order_qty].'=='.$delivery_qty;
							echo number_format($DelvBalanceQty,0);
						}
						?>
					</td>
					<td width="100" align="right"><p><?
					$balance_amt=$orderAmount-$delevery_valu_usd;
					//echo $orderAmount.'=='.$row[order_qty].'=='.$row[order_rate].'=='.$delevery_valu_usd.'=='.$balance_amt.'#';
					//500000==500000==1==5952.5==494047.5#
					if($balance_amt<=1){
							echo 0;
						}else{
							//echo $row[order_qty].'=='.$delivery_qty;
							echo number_format($balance_amt,4);
							$total_bal_del_amtb +=$balance_amt;
						}

					?></p></td>
	            </tr>
	            <? 
				$i++;
			} ?>
		</table>
		</div>
		<table width="<? echo $width;?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body_footer" align="left">
			<tfoot>
				<th width="35"></th>
                <th width="120"></th>
				<th width="100"></th>
				<th width="100"></th>
                <th width="100"></th>
                <th width="100"></th>
                <th width="100"></th>
				<th width="100"></th>
                <th width="100"></th>
				<th width="100"></th>
				<th width="100"></th>
                <th width="100"></th>
                <th width="100"></th>
				<th width="100"></th>
                <th width="60"></th>
                <th width="100"></th>
				<th width="80"></th>
				<th width="80"></th>
				<th width="80"></th>
                <th width="80"></th>
				<th width="80"></th>
                <th width="100"></th>
                <th width="60"></th>
				<th width="100"></th>
                <th width="100"></th>
                <th width="100"></th>
                <th width="100"><? echo number_format($total_del_amtb,4) ; ?></th>
				<th width="100"></th>
				<th width="100"><? echo number_format($total_bal_del_amtb,4) ; ?></th>
			</tfoot>
		</table>
    </div>
    <?
    $html = ob_get_contents();
    ob_clean();
    //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
    foreach (glob("*.xls") as $filename) {
    //if( @filemtime($filename) < (time()-$seconds_old) )
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, $html);
    echo "$html**$filename**$report_type";

    $r_id3=execute_query("delete from tmp_poid where userid=$user_id");
	if($db_type==0)
	{
		if($r_id3)
		{
			mysql_query("COMMIT");  
		}
	}
	if($db_type==2 || $db_type==1 )
	{
		if($r_id3)
		{
			oci_commit($con);  
		}
	}
    exit();
}



if($action=="delivery_quantity_popup")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
	$buyerArr = return_library_array("select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0 ","id","buyer_name");
	$trimsGroupArr = return_library_array("select id, item_name from lib_item_group where item_category=4 and status_active=1","id","item_name");
	$colorNameArr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0","id","color_name");
	$size_arr=return_library_array( "select id,size_name from lib_size",'id','size_name');
		?>
    <table width="935" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all">
    	<thead>
        	<tr> 					
            	<th width="30">S.L</th>
                <th width="100">System ID</th>
                <th width="100">Transection Date</th>
				<th width="100">Work Order No</th>

				<th width="140">Item Name</th>
				<th width="60">Style</th>
                <th width="135">Item Description</th>
                <th width="100">Item Color</th>
                <th width="50">Item Size</th>
                <th width="50">Order UOM</th>

                <th width="70">Delivery  Qty</th>
             
            </tr>
        </thead>
        <tbody>
		<?
		$details_sql="select a.trims_del, a.challan_no, a.delivery_date,a.party_id,
		b.item_group, b.description, b.color_name, b.size_name,b.order_uom,b.order_no,d.style,sum(b.delevery_qty) as delevery_qty from trims_delivery_mst a, trims_delivery_dtls b, subcon_ord_breakdown d where a.id=b.mst_id and b.break_down_details_id=d.id and b.break_down_details_id  in ($ids)  and a.entry_form=208 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.trims_del, a.challan_no, a.delivery_date,a.party_id, b.item_group, b.description, b.color_name, b.size_name,b.order_uom,b.order_no,d.style";
		// echo $details_sql;
		$sql_result=sql_select($details_sql); $t=1;
		foreach($sql_result as $row)
		{
			if ($t%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$delevery_amt=$row[csf("delevery_qty")]*$rate; ?>
        	<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $t; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $t; ?>">
        		<td width="30" align="center"><p><? echo $t; ?>&nbsp;</p></td>
                <td width="100"><p><? echo $row[csf('trims_del')]; ?>&nbsp;</p></td>
                <td width="100"><p><? echo change_date_format($row[csf('delivery_date')]); ?>&nbsp;</p></td>
				<td width="100"><p> <? echo $row[csf('order_no')]; ?> </p></td>

                <td width="140"><p> <? echo $trimsGroupArr[$row[csf('item_group')]];?> </p></td>
				<td width="60"><p> <? echo $row[csf('style')];?> </p></td>
				<td width="135"><p><? echo $row[csf('description')]; ?> </p></td>
				<td width="100"><p><? echo $row[csf('color_name')];?></p></td>
				<td width="50"><p><? echo $row[csf('size_name')];?></p></td>
				<td width="50"><p><? echo $unit_of_measurement[$row[csf('order_uom')]];?></p></td>
				

                <td width="70" align="right"><p><? echo number_format($row[csf('delevery_qty')],0); $total_delevery_qty+=$row[csf("delevery_qty")]; ?>&nbsp;</p></td>
                
            </tr>
            <?
			$t++;
		}
		?>
        </tbody>
        <tfoot>
        	<tr>
                <th >&nbsp;</th>
                <th >&nbsp;</th>
               	<th >&nbsp;</th>
				<th >&nbsp;</th>
                <th >&nbsp;</th>
               	<th >&nbsp;</th>
				<th >&nbsp;</th>
                <th >&nbsp;</th>
               	<th >&nbsp;</th>
                <th >Total</th>
               	<th align="right"><? echo number_format($total_delevery_qty,0); ?></th>
            </tr>
        </tfoot>
    </table>
    <?
}

if($action=="delivery_popup")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
	//$item_group_name = return_field_value("item_name","lib_item_group","id=".$product_sql[0][csf("item_group_id")],"item_name");	?>
    <table width="480" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all">
    	<thead>
        	<tr> 					
            	<th width="30">S.L</th>
                <th width="100">System ID</th>
                <th width="100">Challan No</th>
                <th width="100">Delivery Date</th>
                <th width="70">Delivery  Qty</th>
                <th>Delivery  Amount</th>
            </tr>
        </thead>
        <tbody>
		<?
		$details_sql="select a.trims_del, a.challan_no, a.delivery_date,sum(b.delevery_qty) as delevery_qty from trims_delivery_mst a, trims_delivery_dtls b, trims_job_card_dtls c  where a.id=b.mst_id and c.id=b.job_dtls_id and b.break_down_details_id  in(".chop($ids,',').")  and a.entry_form=208 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.trims_del, a.challan_no, a.delivery_date";
		//echo $details_sql;
		$sql_result=sql_select($details_sql); $t=1;
		foreach($sql_result as $row)
		{
			if ($t%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$delevery_amt=$row[csf("delevery_qty")]*$rate; ?>
        	<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $t; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $t; ?>">
        		<td width="30"><p><? echo $t; ?>&nbsp;</p></td>
                <td width="100"><p><? echo $row[csf('trims_del')]; ?>&nbsp;</p></td>
                <td width="100"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
                <td width="100"><p><? echo change_date_format($row[csf('delivery_date')]); ?>&nbsp;</p></td>
                <td width="70" align="right"><p><? echo number_format($row[csf('delevery_qty')],0); $total_delevery_qty+=$row[csf("delevery_qty")]; ?>&nbsp;</p></td>
                <td align="right"><p><? echo number_format($delevery_amt,2); $total_delevery_amt+=$delevery_amt; ?>&nbsp;</p></td>
            </tr>
            <?
			$t++;
		}
		?>
        </tbody>
        <tfoot>
        	<tr>
                <th >&nbsp;</th>
                <th >&nbsp;</th>
               	<th >&nbsp;</th>
                <th >Total</th>
               	<th align="right"><? echo number_format($total_delevery_qty,0); ?></th>
              	<th align="right"><? echo number_format($total_delevery_amt,2); ?></th>
            </tr>
        </tfoot>
    </table>
    <?
}

if($action=="bill_popup")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
	//$item_group_name = return_field_value("item_name","lib_item_group","id=".$product_sql[0][csf("item_group_id")],"item_name");	?>
    <table width="480" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all">
    	<thead>
        	<tr> 					
            	<th width="30">S.L</th>
                <th width="100">System ID</th>
                <th width="100">Bill No</th>
                <th width="100">Bill Date</th>
                <th width="70">Bill  Qty</th>
                <th>Bill  Amount</th>
            </tr>
        </thead>
        <tbody>
		<?
		$details_sql="select a.trims_bill ,a.bill_date, a.bill_no, sum(b.quantity) as quantity from trims_bill_mst a, trims_bill_dtls b,trims_job_card_dtls c, trims_job_card_mst d  where a.id=b.mst_id and c.id=b.job_dtls_id and c.job_no_mst=d.trims_job and a.entry_form=276 and b.id in($ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.trims_bill ,a.bill_date, a.bill_no";
		$sql_result=sql_select($details_sql); $t=1;
		foreach($sql_result as $row)
		{
			echo $rate;
			if ($t%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$bill_amount=$row[csf("quantity")]*$rate; ?>
        	<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $t; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $t; ?>">
        		<td width="30"><p><? echo $t; ?>&nbsp;</p></td>
                <td width="100"><p><? echo $row[csf('trims_bill')]; ?>&nbsp;</p></td>
                <td width="100"><p><? echo $row[csf('bill_no')]; ?>&nbsp;</p></td>
                <td width="100"><p><? echo change_date_format($row[csf('bill_date')]); ?>&nbsp;</p></td>
                <td width="70" align="right"><p><? echo number_format($row[csf('quantity')],0); $total_bill_qty+=$row[csf("quantity")]; ?>&nbsp;</p></td>
                <td align="right"><p><? echo number_format($bill_amount,2); $total_bill_amt+=$bill_amount; ?>&nbsp;</p></td>
            </tr>
            <?
			$t++;
		}
		?>
        </tbody>
        <tfoot>
        	<tr>
                <th >&nbsp;</th>
                <th >&nbsp;</th>
               	<th >&nbsp;</th>
                <th >Total</th>
               	<th align="right"><? echo number_format($total_bill_qty,0); ?></th>
              	<th align="right"><? echo number_format($total_bill_amt,2); ?></th>
            </tr>
        </tfoot>
    </table>
    <?
}
?>
