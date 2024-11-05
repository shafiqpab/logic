<? 
include('../../../includes/common.php');
session_start();
extract($_REQUEST);
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$date=date('Y-m-d');
$user_id=$_SESSION['logic_erp']['user_id'];

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_id", 120, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-Select Location-", $selected, "",0 );
	exit();     	 
}

if ($action=="load_drop_down_party_name")
{
	$data=explode('_',$data);
	if($data[0]==3)
	{
		echo create_drop_down( "cbo_party_name", 120, "select sup.id, sup.supplier_name from lib_supplier sup,lib_supplier_tag_company b where sup.status_active=1 and sup.is_deleted=0 and b.supplier_id=sup.id and b.tag_company='$data[1]' $supplier_cond and sup.id in (select  supplier_id from  lib_supplier_party_type where party_type in (9,20,21)) order by supplier_name","id,supplier_name", 1, "--Select--", $selected, "","","","","","",5 );
	}
	else if($data[0]==2)
	{
		echo create_drop_down( "cbo_party_name", 120, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id  and b.tag_company='$data[1]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-Select Party-", $selected, "","","","","","",5 ); 
	}
	else if($data[0]==1)
	{	
		echo create_drop_down( "cbo_party_name", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name",1, "-Select Party-", $selected, "","","","","","",5 ); 
	}
	else
	{
		echo create_drop_down( "cbo_party_name", 120, $blank_array,"", 1, "--Select Party-", $selected, "",0,"","","","",5);
	}
	exit();
}

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$company_name=str_replace("'","",$cbo_company_id);
	$cbo_location_id=str_replace("'","",$cbo_location_id);
	$cbo_source=str_replace("'","",$cbo_source);
	$cbo_bill_type=str_replace("'","",$cbo_bill_type);
	$party_name = str_replace("'","",$cbo_party_name);
	$start_chln=str_replace("'","",$txt_chln_from);
	$end_chln=str_replace("'","",$txt_chln_to);
	$cbo_year=str_replace("'","",$cbo_year);
	
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);

    if($db_type==0) $booking_without_order="IFNULL(a.booking_without_order,0)";
	 else if($db_type==2) $booking_without_order="nvl(a.booking_without_order,0)";
	 
	 if($db_type==2) $booking_without_order2="nvl(c.booking_without_order,0)";
	 
	if($cbo_location_id==0) $locationCond = ""; else $locationCond = "and a.location_id='$cbo_location_id'";
	if($cbo_location_id==0) $locationCond2 = ""; else $locationCond2 = "and c.location_id='$cbo_location_id'";
	
	$partyCond="";$partyCond2="";
	if($cbo_bill_type==3)
	{
		if($party_name!="0") $partyCond = "and a.party_id=$party_name";
		if($party_name!="0") $partyCond2 = "and c.party_id=$party_name";
		if($db_type==0)
		{ 
			if ($date_from!="" &&  $date_to!="") $delivery_date_cond= "and a.delivery_date between '".change_date_format($date_from, "", "",1)."' and '".change_date_format($date_to, "", "",1)."'";  else $delivery_date_cond= "";
			if ($date_from!="" &&  $date_to!="") $recv_date_cond= "and b.receive_date between '".change_date_format($date_from, "", "",1)."' and '".change_date_format($date_to, "", "",1)."'";  else $recv_date_cond= "";
		}
		else if ($db_type==2)
		{
			if ($date_from!="" &&  $date_to!="") $delivery_date_cond= "and a.delivery_date between '".change_date_format($date_from, "", "",1)."' and '".change_date_format($date_to, "", "",1)."'";  else $delivery_date_cond= "";
			if ($date_from!="" &&  $date_to!="") $recv_date_cond= "and b.receive_date between '".change_date_format($date_from, "", "",1)."' and '".change_date_format($date_to, "", "",1)."'";  else $recv_date_cond= "";
		}
	}
	else
	{
		if($cbo_source==2)
		{
			if($party_name!="0") $partyCond = "and a.party_id=$party_name";
			if($party_name!="0") $partyCond2 = "and c.party_id=$party_name";
			if($db_type==0)
			{ 
				if ($date_from!="" &&  $date_to!="") $date_cond= "and a.delivery_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'"; else $date_cond= "";
			}
			else if ($db_type==2)
			{
				if ($date_from!="" &&  $date_to!="") $date_cond= "and a.delivery_date between '".change_date_format($date_from, "", "",1)."' and '".change_date_format($date_to, "", "",1)."'";  else $date_cond= "";
				
				
			}
		}
		else
		{
			if($party_name!="0")
			{
				$partyCond = "and a.knitting_company=$party_name";
				$partyCond3 = "and c.knitting_company=$party_name";
				$partyCond2 = "and a.dyeing_company=$party_name";
			}
			if($db_type==0)
			{ 
				if ($date_from!="" &&  $date_to!="") $date_cond= "and a.receive_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'"; else $date_cond= "";
				if ($date_from!="" &&  $date_to!="") $delivery_date_cond= "and b.delivery_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'"; else $delivery_date_cond= "";
				
				if ($date_from!="" &&  $date_to!="") $recv_date_cond= "and b.receive_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'"; else $recv_date_cond= "";
			}
			else if ($db_type==2)
			{
				if ($date_from!="" &&  $date_to!="") $date_cond= "and a.receive_date between '".change_date_format($date_from, "", "",1)."' and '".change_date_format($date_to, "", "",1)."'";  else $date_cond= "";
				if ($date_from!="" &&  $date_to!="") $delivery_date_cond= "and b.delivery_date between '".change_date_format($date_from, "", "",1)."' and '".change_date_format($date_to, "", "",1)."'";  else $delivery_date_cond= "";
				if ($date_from!="" &&  $date_to!="") $recv_date_cond= "and b.receive_date between '".change_date_format($date_from, "", "",1)."' and '".change_date_format($date_to, "", "",1)."'";  else $recv_date_cond= "";
				//delevery_date
				if ($date_from!="" &&  $date_to!="") $delivery_date_cond2= "and a.delevery_date between '".change_date_format($date_from, "", "",1)."' and '".change_date_format($date_to, "", "",1)."'";  else $delivery_date_cond2= "";
				if ($date_from!="" &&  $date_to!="") $bill_date_cond= "and a.bill_date between '".change_date_format($date_from, "", "",1)."' and '".change_date_format($date_to, "", "",1)."'";  else $bill_date_cond= "";
			}
		}
	}

    if($start_chln!="" && $end_chln!="") $challandCond = "and a.recv_number_prefix_num between '$start_chln' and '$end_chln'"; else $challandCond = "";
	 if($start_chln!="" && $end_chln!="") $challandCond2 = "and c.recv_number_prefix_num between '$start_chln' and '$end_chln'"; else $challandCond2 = "";
	if($start_chln!="" && $end_chln!="") $tchallandCond = "and a.del_no_prefix_num between '$start_chln' and '$end_chln'"; else $tchallandCond = "";
	if($cbo_year!=0)
	{
		if($db_type==0) $year_cond=" and year(a.insert_date)='$cbo_year'"; else $year_cond=" and to_char(a.insert_date,'YYYY')='$cbo_year'";
	}
	else $year_cond="";

	$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name");
    $party_arr=return_library_array( "select id,supplier_name from lib_supplier", "id", "supplier_name");
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$deliveryId_arr=array();
	 
				
	$sql_resultVar = sql_select("select variable_list, dyeing_fin_bill from  variable_settings_subcon where company_id='$company_name' and variable_list in (7,8) order by id");
 	foreach($sql_resultVar as $result)
	{
		if($result[csf("variable_list")]==8)// inhouse bill from
		{
			$finishdata_source=$result[csf("dyeing_fin_bill")];
			if($finishdata_source=="") $finishdata_source=1; 
			else if ($finishdata_source==0) $finishdata_source=1;
			else $finishdata_source=$finishdata_source;
			$inhouse_bill_from=$finishdata_source;
		}
		if($result[csf("variable_list")]==7)// Knitting  bill from
		{
			 $in_house_knit_bill_from=0;
			$in_house_knit_bill_from=$result[csf("dyeing_fin_bill")];
			 if($in_house_knit_bill_from==0) $in_house_knit_bill_from=2;
		}
	}
	//echo $inhouse_bill_from.'D';
	if($cbo_bill_type==3)
	{
		$sectionWo_arr=return_library_array( "select order_no,section_id from trims_job_card_mst", "order_no", "section_id");
		
		$sql_bill="select c.id from trims_bill_mst a, trims_bill_dtls b, trims_delivery_mst c where a.id=b.mst_id and b.challan_no=c.trims_del and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $locationCond $partyCond";
		//echo $sql_bill;
		$sql_bill_result =sql_select($sql_bill);
		$delId="";
			
		foreach($sql_bill_result as $row)
		{
			if($row[csf("id")]!="")
			{
				$delId.=trim($row[csf("id")]).",";
				$deliveryId_arr[$row[csf("id")]]=1;
			}
		}
	}
	else
	{
		if($cbo_source!=2)
		{
			if($cbo_bill_type==1) $process_id_cond=" and b.process_id=2"; else if($cbo_bill_type==2) $process_id_cond=" and a.process_id in (3,4)"; 
			
			if($cbo_source==1)
			{
				 $sql_bill="select b.delivery_id from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.id=b.mst_id and a.party_source=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $process_id_cond $bill_date_cond group by b.delivery_id";
			}
			else if($cbo_source==3)
			{
				$sql_bill="select b.receive_id as delivery_id from subcon_outbound_bill_mst a, subcon_outbound_bill_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $process_id_cond $bill_date_cond group by b.receive_id";
			}
			
			$sql_bill_result =sql_select($sql_bill);
			$delId="";
			
			foreach($sql_bill_result as $row)
			{
				if($row[csf("delivery_id")]!="")
				{
					$delId.=trim($row[csf("delivery_id")]).",";
					$deliveryId_arr[$row[csf("delivery_id")]]=1;
				}
			}

			unset($sql_bill_result);
			/*//print_r($delId);
			$delIds=""; $tot_rows=0;
			$did=array_unique(array_filter(explode(",",$delId)));
			foreach($did as $deId)
			{
				$tot_rows++;
				$delIds.=trim($deId).",";
			}
			
			$delIds=chop($delIds,','); $del_id_cond='';
			if($db_type==2 && $tot_rows>999)
			{
				$del_id_cond=" and (";
				$delIdsArr=array_chunk(explode(",",$delIds),998);
				foreach($delIdsArr as $ids)
				{
					$ids=trim(implode(",",$ids));
					$del_id_cond.=" a.id not in ($ids) or ";
				}
				$del_id_cond=chop($del_id_cond,'or ');
				$del_id_cond.=")";
			}
			else
			{
				$del_id_cond=" and a.id not in ($delIds)";
			}*/
		}
	}
	//echo "<pre>";
	//print_r($deliveryId_arr);
	ob_start();
	if($cbo_bill_type==1)//Knitting
	{
		if($cbo_source==2)//Inbound Subcon
		{
			$sql="select a.id, a.party_id as knitting_company, a.delivery_no as recv_number, 2 as knitting_source, a.challan_no, a.delivery_date as receive_date, sum(b.delivery_qty) as challan_qty from subcon_delivery_mst a, subcon_delivery_dtls b where a.company_id=$company_name and a.id=b.mst_id and a.process_id=2 and a.status_active=1 and b.bill_status=0  and b.status_active=1 $locationCond $partyCond $challandCond $year_cond $date_cond group by a.id, a.party_id, a.delivery_no, a.challan_no, a.delivery_date order by a.id DESC";
		}
		else // Inhouse And Outbound subcon
		{
			 if($in_house_knit_bill_from==2) // ***=====Subcon Variable-In House Knit Bill From===***
			 {
				    $sql=" select a.id, c.knitting_company, a.sys_number as recv_number,c.knitting_source, b.grey_sys_number as challan_no, a.delevery_date as receive_date, c.remarks, sum(b.current_delivery) as challan_qty from pro_grey_prod_delivery_mst a, pro_grey_prod_delivery_dtls b,inv_receive_master c
						where a.id=b.mst_id and b.grey_sys_id=c.id and c.knitting_source=$cbo_source and c.company_id='$company_name'  and c.entry_form=2  and c.knitting_source=$cbo_source and a.company_id='$company_name' and a.entry_form in(56,53) and c.entry_form=2 and c.receive_basis in (2,4,11) 
						and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $partyCond3 $delivery_date_cond2    $locationCond2 $partyCond3 $challandCond2 
						group by a.id, c.knitting_company, a.sys_number,c.knitting_source, b.grey_sys_number, a.delevery_date, c.remarks
						order by a.id desc";	
			 }
			 else
			 {
				 $sql="SELECT a.id, a.knitting_company, a.recv_number, a.knitting_source, a.challan_no, a.receive_date, a.remarks, sum(c.quantity) as challan_qty FROM inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, inv_transaction d
				WHERE a.id=b.mst_id AND a.id=d.mst_id AND b.id=c.dtls_id and d.id=c.trans_id AND a.knitting_source=$cbo_source AND a.company_id=$company_name $year_cond  AND c.trans_type=1 AND a.entry_form in (2,22,58) AND c.entry_form in (2,22,58) AND a.item_category=13 AND a.receive_basis in (0,1,2,4,9,10,11) AND c.trans_id!=0 AND $booking_without_order=0 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 AND c.status_active=1 AND c.is_deleted=0 $locationCond $partyCond $challandCond $date_cond group by a.id, a.knitting_company, a.recv_number, a.knitting_source, a.challan_no, a.receive_date, a.remarks ORDER BY a.id  DESC";
			 }
			/*$sql="SELECT a.id, a.knitting_company, a.recv_number, a.knitting_source, a.challan_no, a.receive_date, a.remarks, sum(c.quantity) as challan_qty FROM inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, inv_transaction d
				WHERE a.id=b.mst_id AND a.id=d.mst_id AND b.id=c.dtls_id and d.id=c.trans_id AND a.knitting_source=$cbo_source AND a.company_id=$company_name $year_cond  AND c.trans_type=1 AND a.entry_form in (2,22,58) AND c.entry_form in (2,22,58) AND a.item_category=13 AND a.receive_basis in (0,1,2,4,9,10,11) AND c.trans_id!=0 AND $booking_without_order=0 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 AND c.status_active=1 AND c.is_deleted=0 $locationCond $partyCond $challandCond $date_cond group by a.id, a.knitting_company, a.recv_number, a.knitting_source, a.challan_no, a.receive_date, a.remarks ORDER BY a.id  DESC";*/
		}
	}
	else if($cbo_bill_type==2)//Dyeing
	{
		if($cbo_source==2)//Inbound Subcon
		{
			$sql="select a.id, a.party_id as knitting_company, a.delivery_no as recv_number, 2 as knitting_source, a.challan_no, a.delivery_date as receive_date, sum(b.delivery_qty) as challan_qty from subcon_delivery_mst a, subcon_delivery_dtls b where a.company_id=$company_name and a.id=b.mst_id and a.process_id in (3,4) and a.status_active=1 and b.bill_status=0  and b.status_active=1 $locationCond $partyCond $challandCond $year_cond $date_cond group by a.id, a.party_id, a.delivery_no, a.challan_no, a.delivery_date order by a.id DESC";
		}
		else // Inhouse And Outbound subcon
		{
			if($inhouse_bill_from==2)
			{
				   $sql="SELECT a.id, c.knitting_company, c.recv_number, c.knitting_source, c.challan_no, c.receive_date, c.remarks,sum(f.quantity) as challan_qty ,sum(e.production_qty) as grey_quantity,sum(f.grey_used_qty) as grey_used_qty
							FROM pro_grey_prod_delivery_mst a, pro_grey_prod_delivery_dtls b, inv_receive_master c, pro_batch_create_mst d, pro_finish_fabric_rcv_dtls e, order_wise_pro_details f
							WHERE a.id=b.mst_id and b.grey_sys_id=c.id and d.id=b.batch_id and c.id=e.mst_id and e.id=f.dtls_id and e.id=b.sys_dtls_id and b.order_id=f.po_breakdown_id 
							and a.entry_form in (54,67) AND c.knitting_source=1 AND a.company_id=$company_name    and c.receive_basis in (0,2,4,5,9,11) and c.item_category=2 and c.entry_form in (7,66) and b.current_delivery>0 and $booking_without_order2=0  and f.entry_form in (7,66)
							and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.batch_against in (0,1,2,3) and  c.knitting_source=$cbo_source AND c.company_id=$company_name AND c.item_category=2 $partyCond3 $delivery_date_cond2    $locationCond2 $partyCond3 $challandCond2 
							group by a.id, c.knitting_company, c.recv_number, c.knitting_source, c.challan_no, c.receive_date, c.remarks order by a.id DESC";
			}
			else
			{
				  //Bill from receive
					 
						$sql="SELECT a.id, a.knitting_company, a.recv_number, a.knitting_source, a.challan_no, a.receive_date, a.remarks, sum(c.quantity) as challan_qty ,e.grey_quantity,c.grey_used_qty
							FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c, pro_batch_create_mst d
							WHERE a.id=b.mst_id and b.id=c.dtls_id and d.id=b.batch_id and c.trans_type=1 and c.entry_form in (7,37,66,68) and c.trans_id!=0 and a.entry_form in (7,37,66,68) AND a.knitting_source=1 AND a.company_id=$data[3] AND a.location_id=$data[1] and c.quantity>0  $booking_without_order AND a.knitting_company=$data[0] and a.receive_basis in (2,4,5,9,11) and a.item_category=2 
							and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.batch_against in (0,1,3) and $booking_without_order=0 AND a.knitting_source=$cbo_source AND a.company_id=$company_name AND a.item_category=2  $locationCond $partyCond $challandCond $date_cond  
							group by a.id, a.knitting_company, a.recv_number, a.knitting_source, a.challan_no, a.receive_date, a.remarks, sum(c.quantity) as challan_qty ,e.grey_quantity,c.grey_used_qty ORDER BY a.id DESC";
					 
			}
			 /*$sql333="SELECT a.id, a.knitting_company, a.recv_number, a.knitting_source, a.challan_no, a.receive_date, a.remarks, sum(c.quantity) as challan_qty ,e.grey_quantity,c.grey_used_qty
                FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c, pro_batch_create_mst d, inv_transaction e
                WHERE a.id=b.mst_id  AND a.id=e.mst_id AND b.id=c.dtls_id and e.id=c.trans_id AND d.id=b.batch_id AND c.trans_type=1 AND c.entry_form IN (7,37,66,68) AND c.trans_id!=0 AND a.entry_form IN (7,37,66,68) AND a.knitting_source=$cbo_source AND a.company_id=$company_name AND a.item_category=2 AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 AND c.status_active=1 AND c.is_deleted=0 $locationCond $partyCond $challandCond $date_cond group by a.id, a.knitting_company, a.recv_number, a.knitting_source, a.challan_no, a.receive_date, a.remarks,e.grey_quantity,c.grey_used_qty ORDER BY a.id DESC";*/
		}
	}
	else //Trims
	{
       //$sql="select a.id, a.party_id as knitting_company, a.trims_bill as recv_number, a.within_group as knitting_source, a.challan_no, a.bill_date as receive_date, sum(b.total_delv_qty) as challan_qty from trims_bill_mst a, trims_bill_dtls b where a.company_id=$company_name and a.id=b.mst_id and a.status_active=1 $locationCond $partyCond $challandCond $year_cond $date_cond group by a.id, a.party_id, a.trims_bill, a.within_group, a.challan_no, a.bill_date order by a.id DESC";
	    /*$sql = "select a.id, a.party_id as knitting_company, a.trims_del as recv_number, a.within_group as knitting_source, a.challan_no, a.delivery_date as receive_date, sum(b.delevery_qty) as challan_qty, sum(b.delevery_qty*b.order_receive_rate) as delivery_value, b.order_no, b.order_uom from trims_delivery_mst a, trims_delivery_dtls b where a.id=b.mst_id and b.delevery_qty>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 AND a.company_id=$company_name
		$locationCond $partyCond $tchallandCond $year_cond $delivery_date_cond
		group by a.id, a.party_id, a.trims_del, a.within_group, a.challan_no, a.delivery_date, b.order_no, b.order_uom order by a.id DESC";*/

		
		if($db_type==0)
		{
			$buyer_buyer_cond=",group_concat(c.buyer_buyer) as buyer_buyer";
		}
		else if($db_type==2)
		{
			$buyer_buyer_cond=",LISTAGG(CAST(c.buyer_buyer AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.id) as buyer_buyer";
		} 


		$sql = "select a.id, a.party_id as knitting_company, a.trims_del as recv_number, a.within_group as knitting_source, a.challan_no, a.delivery_date, sum(b.delevery_qty) as challan_qty, sum(b.delevery_qty*c.rate) as delivery_value, b.order_no, b.order_uom, d.currency_id, d.receive_date $buyer_buyer_cond from trims_delivery_mst a, trims_delivery_dtls b, subcon_ord_dtls c, subcon_ord_mst d where a.id=b.mst_id and b.receive_dtls_id=c.id and d.id=c.mst_id and b.delevery_qty>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 AND a.company_id=$company_name
		$locationCond $partyCond $tchallandCond $year_cond $delivery_date_cond
		group by a.id, a.party_id, a.trims_del, a.within_group, a.challan_no, a.delivery_date, b.order_no, b.order_uom, d.currency_id, d.receive_date order by a.id DESC";
	}
	//echo $sql;
	$bill_type_arr=array(1=>"Knitting",2=>"Dyeing",3=>"Trims"); 
	if($cbo_bill_type!=3) // kniting and finishing 
	{
		?>
		<div style="width:1020px; margin: 0 auto;">
			<fieldset style="width:920px;">
				<table cellpadding="0" cellspacing="0" width="1000">
					<tr class="form_caption" style="border:none;">
					   <td align="center" width="100%" colspan="7" style="font-size:20px"><strong><? echo $report_title.' ('.$bill_type_arr[$cbo_bill_type].')'; ?></strong></td>
					</tr>
					<tr class="form_caption" style="border:none;">
					   <td align="center" width="100%" colspan="7" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
					</tr>
				</table>
				<table width="1000" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table">
					<thead>
						<th width="40">SL</th>
						<th width="150">System No</th>
						<th width="100">Pending Challan No</th>
						<th width="200">Party Name</th>
						<th width="80">Challan Date</th>
						<th width="100">Challan Qty</th>
						<th width="100">Grey  Quantity</th>
						<th>Remarks</th>
					</thead>
				</table>
				<div style="max-height:300px; overflow-y:scroll; width:1020px" id="scroll_body" >
					<table width="1000" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body">
					<?
						$sql_result=sql_select($sql); 
						$i=1;
						foreach ($sql_result as $row)
						{
							  //echo $row[csf("id")].'<br>';
							if($deliveryId_arr[$row[csf("id")]]=='')
							{
								//echo $row[csf("id")].'<br>';
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								if($row[csf('knitting_source')]==1) $knitting_company=$company_arr[$row[csf('knitting_company')]]; else if($row[csf('knitting_source')]==2) $knitting_company=$buyer_arr[$row[csf('knitting_company')]]; else if($row[csf('knitting_source')]==3) $knitting_company=$party_arr[$row[csf('knitting_company')]];
								?>
								<tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									 <td width="40" align="center"><? echo $i; ?></td>
									 <td width="150"><? echo $row[csf('recv_number')]; ?></td>
									 <td width="100" style="word-break:break-all" align="center"><? echo $row[csf('challan_no')]; ?></td>
									 <td width="200" style="word-break:break-all"><? echo $knitting_company; ?></td>
									 <td width="80" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
									 <td width="100" align="right"><? echo number_format($row[csf('challan_qty')],2,'.',''); ?></td>
									 <td width="100" align="right"><? echo number_format($row[csf('grey_used_qty')],2,'.',''); ?></td>
									 <td style="word-break:break-all"><? echo $row[csf('remarks')]; ?></td>
								</tr>
								<?	
								$i++;
							}
						}
						?>
					</table>
				</div>
			</fieldset>
		</div>
		<?
	}
	else if($cbo_bill_type==3)//trims
	{
		?>
		<div style="width:1070px; margin: 0 auto;">
			<fieldset style="width:1070px;">
				<table cellpadding="0" cellspacing="0" width="1050">
					<tr class="form_caption" style="border:none;">
					   <td align="center" width="100%" colspan="8" style="font-size:20px"><strong><? echo $report_title.' ('.$bill_type_arr[$cbo_bill_type].')'; ?></strong></td>
					</tr>
					<tr class="form_caption" style="border:none;">
					   <td align="center" width="100%" colspan="8" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
					</tr>
				</table>
				<table width="1050" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table">
					<thead>
						<th width="30">SL</th>
						<th width="110">System No</th>
						<th width="80">Pending Challan No</th>
						<th width="150">Party Name</th>
						<th width="150">Costumer buyer Name</th>
						<th width="70">Challan Date</th>
						<th width="80">Challan Qty</th>
                        <th width="60">Order UOM</th>
                        <th width="70">Delivery Value ($)</th>
                        <th width="70">Section Name</th>
                        <th width="110">W/O No.</th>
						<th>Remarks</th>
					</thead>
				</table>
				<div style="max-height:300px; overflow-y:scroll; width:1070px" id="scroll_body" >
					<table width="1050" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body">
					<?
						$sql_result=sql_select($sql); 
						$i=1;
						foreach ($sql_result as $row)
						{
							//echo $deliveryId_arr[$row[csf("id")]].'==';
							if($deliveryId_arr[$row[csf("id")]]=='')
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								if($row[csf('knitting_source')]==1){
									$knitting_company=$company_arr[$row[csf('knitting_company')]];
									$buyer_buyer=""; 
									$buyer_buyer_ids=explode(",",$row[csf('buyer_buyer')]);
									foreach($buyer_buyer_ids as $buyer_id)
									{
										if($buyer_buyer=="") $buyer_buyer=$buyer_arr[$buyer_id]; else $buyer_buyer.=','.$buyer_arr[$buyer_id];
									}
									$buyer_buyer=implode(",",array_unique(explode(",",$buyer_buyer)));
								} 
								else if($row[csf('knitting_source')]==2)
								{
									$knitting_company=$buyer_arr[$row[csf('knitting_company')]];
									$buyer_buyer=chop(implode(',',array_unique(explode(",",$row[csf('buyer_buyer')]))),',');
								} 
								$amount=0; $currency_rate=1;
								if($row[csf('currency_id')]!=2)
								{
									$currency_rate=set_conversion_rate( 2, $row[csf('receive_date')]);
									$amount=$row[csf('delivery_value')]/$currency_rate;
								}
								else
								{
									$amount=$row[csf('delivery_value')];
								}
								
								?>
								<tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									 <td width="30" align="center"><? echo $i; ?></td>
									 <td width="110"><? echo $row[csf('recv_number')]; ?></td>
									 <td width="80" style="word-break:break-all" align="center"><? echo $row[csf('challan_no')]; ?></td>
									 <td width="150" style="word-break:break-all"><? echo $knitting_company; ?></td>
									 <td width="150" style="word-break:break-all"><? echo $buyer_buyer; ?></td>
									 <td width="70" align="center"><? echo change_date_format($row[csf('delivery_date')]); ?></td>
									 <td width="80" align="right"><? echo number_format($row[csf('challan_qty')],2,'.',''); ?></td>
                                     
                                     <td width="60" style="word-break:break-all"><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?></td>
                                     <td width="70" align="right" title="<?="Currency Rate= ".$currency_rate.'; Amt= '.$row[csf('delivery_value')]; ?>"><? echo number_format($amount,2); ?></td>
                                     <td width="70" style="word-break:break-all"><? echo $trims_section[$sectionWo_arr[$row[csf('order_no')]]]; ?></td>
                                     <td width="110" style="word-break:break-all"><? echo $row[csf('order_no')]; ?></td>
									 <td style="word-break:break-all"><? echo $row[csf('remarks')]; ?></td>
								</tr>
								<?	
								$i++;
								$totalValue+=$amount;
							}
						}
						?>
					</table>
				</div>
                <table width="1050" border="1" cellpadding="0" cellspacing="0" rules="all" class="tbl_bottom">
					<thead>
                    	<tr>
                            <td width="30">&nbsp;</td>
                            <td width="110">&nbsp;</td>
                            <td width="80">&nbsp;</td>
                            <td width="150">&nbsp;</td>
                            <td width="150">&nbsp;</td>
                            <td width="70">&nbsp;</td>
                            <td width="80">&nbsp;</td>
                            <td width="60">Total:</td>
                            <td width="70" align="right"><?=number_format($totalValue,2); ?></td>
                            <td width="70">&nbsp;</td>
                            <td width="110">&nbsp;</td>
							<td>&nbsp;</td>
                        </tr>
					</thead>
				</table>
			</fieldset>
		</div>
		<?
	}
    $html = ob_get_contents();
    ob_clean();
    foreach (glob("*.xls") as $filename) {
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');	
    $is_created = fwrite($create_new_doc, $html);
    echo "$html**$filename"; 
    exit();
}
?>