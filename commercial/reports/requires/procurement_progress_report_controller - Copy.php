<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id = $_SESSION['logic_erp']["user_id"];


$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
$location_arr=return_library_array("select id,location_name from  lib_location","id","location_name");
$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');



if($action=="load_drop_down_location")
{ 
	echo create_drop_down( "cbo_location", 140, "select id,location_name from lib_location where company_id=$data","id,location_name", 1, "-- Select Location --", $selected,"load_drop_down( 'requires/procurement_progress_report_controller',document.getElementById('cbo_company_name').value+'_'+this.value, 'load_drop_down_store', 'store_td' )",0,"" );
}

if($action=="load_drop_down_store")
{ 
	$data=explode("_",$data);
	if($data[1] !=0) $loc_cond=" and a.location_id=$data[1]"; else $loc_cond='';
	//echo $loc_cond; die;
	echo create_drop_down( "cbo_store", 120, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id='$data[0]' $loc_cond and b.category_type not in (1,2,3,12,13,14,24,25) and a.status_active=1 and a.is_deleted=0 group by a.id, a.store_name order by a.store_name","id,store_name", 1, "-- Select Store --", $selected, "",0 ); 
	// and b.category_type=1
}

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_location=str_replace("'","",$cbo_location);
	$cbo_item_category_id=str_replace("'","",$cbo_item_category_id);
	$txt_req_no=str_replace("'","",$txt_req_no);
	$cbo_job_year=str_replace("'","",$cbo_job_year);
	$txt_wo_no=str_replace("'","",$txt_wo_no);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$cbo_store=str_replace("'","",$cbo_store);
	$value_with=str_replace("'","",$cbo_value_with);
	$str_cond=$str_cond_independ="";
	//echo $cbo_item_category_id;die;
    //print_r($cbo_job_year);die(asdf);
	if($cbo_location>0) $str_cond.="and a.location_id='$cbo_location'";
	if($cbo_item_category_id !="") $str_cond.="and b.item_category in($cbo_item_category_id)";
	if($cbo_store>0) $str_cond.="and a.store_name='$cbo_store'";
	if($txt_req_no!="") $str_cond.="and a.requ_prefix_num ='$txt_req_no'";

    if($cbo_job_year>0) $str_cond.=" and to_char(a.requisition_date,'YYYY')=$cbo_job_year";

    //$year_cond=" and to_char(a.insert_date,'YYYY')=$data[7]";
    //if($cbo_job_year>0) $str_cond.="and a.requisition_date='$cbo_job_year'";


	if($txt_wo_no!="") $str_cond.="and d.wo_number_prefix_num like '%$txt_wo_no%'";
	if($txt_date_from!="" && $txt_date_to!="") $str_cond.="and a.requisition_date between '$txt_date_from' and '$txt_date_to'";
	
	if($cbo_item_category_id  !="") $str_cond_independ.="and b.item_category_id in($cbo_item_category_id)";
    if($cbo_job_year>0) $str_cond_independ.=" and to_char(a.wo_date,'YYYY')=$cbo_job_year";
	if($txt_wo_no!="") $str_cond_independ.="and a.wo_number_prefix_num like '%$txt_wo_no%'";
	if($txt_date_from!="" && $txt_date_to!="") $str_cond_independ.="and a.wo_date between '$txt_date_from' and '$txt_date_to'";
	//echo $cbo_based_on;die;
	//LISTAGG(CAST(b.pi_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.pi_id) as pi_id
	if($db_type==2)
	{
		$sql_req_wo="SELECT a.id as req_id, e.rate, a.requ_no, a.requ_prefix_num, a.requisition_date, a.company_id, a.division_id, a.department_id, a.section_id, b.item_category,a.store_name, a.pay_mode, a.source, a.cbo_currency, a.delivery_date,b.cons_uom,LISTAGG(CAST(b.required_for AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.required_for) as required_for, sum(b.quantity) as req_quantity, sum(b.amount) as amount, c.id as prod_id, c.item_group_id, c.sub_group_name, c.unit_of_measure, c.item_code, (c.item_description|| c.item_size) as product_name_details , d.id as wo_id, d.wo_number_prefix_num, d.wo_number, d.supplier_id, d.wo_date, d.currency_id, d.location_id, d.delivery_date, d.attention, d.wo_basis_id, sum(e.supplier_order_quantity) as wo_qnty, sum(e.amount) as wo_value, b.remarks
		from inv_purchase_requisition_mst a, product_details_master c,  inv_purchase_requisition_dtls b 
		left join  wo_non_order_info_dtls e  on  b.id=e.requisition_dtls_id and e.status_active=1 and e.is_deleted=0 
		left join wo_non_order_info_mst d on d.id=e.mst_id  and d.status_active=1 and d.is_deleted=0  and d.company_name=$cbo_company_name 
		where a.id=b.mst_id and b.product_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id='$cbo_company_name' $str_cond
		group by  d.id, d.wo_number_prefix_num, d.wo_number, d.supplier_id, d.wo_date, d.currency_id, d.location_id, d.delivery_date, d.attention, d.wo_basis_id, a.id, a.requ_no, a.requ_prefix_num, a.requisition_date, a.company_id, a.division_id, a.department_id, a.section_id, b.item_category, a.store_name, a.pay_mode, a.source, a.cbo_currency, a.delivery_date, b.cons_uom, c.id, c.item_group_id, c.sub_group_name, c.unit_of_measure, c.item_code, c.item_description, c.item_size, e.rate, b.remarks
		order by d.id,c.id,a.id"; 
        // echo $sql_req_wo;die();
	}
	else if($db_type==0)
	{
		$sql_req_wo="SELECT a.id as req_id, e.rate, a.requ_no, a.requ_prefix_num, a.requisition_date, a.company_id, a.division_id, a.department_id, a.section_id, b.item_category, a.store_name, a.pay_mode, a.source, a.cbo_currency, a.delivery_date, b.cons_uom, group_concat(b.required_for) as required_for, sum(b.quantity) as req_quantity, sum(b.amount) as amount, c.id as prod_id, c.item_group_id, c.sub_group_name, c.unit_of_measure, c.item_code, concat(c.item_description, c.item_size) as product_name_details, d.id as wo_id, d.wo_number_prefix_num, d.wo_number, d.supplier_id, d.wo_date, d.currency_id, d.location_id, d.delivery_date, d.attention, d.wo_basis_id, sum(e.supplier_order_quantity) as wo_qnty, sum(e.amount) as wo_value, b.remarks
		from inv_purchase_requisition_mst a, product_details_master c, inv_purchase_requisition_dtls b 
		left join wo_non_order_info_dtls e on b.id=e.requisition_dtls_id and e.status_active=1 and e.is_deleted=0 
		left join wo_non_order_info_mst d on d.id=e.mst_id and d.status_active=1 and d.is_deleted=0 and d.company_name=$cbo_company_name 
		where a.id=b.mst_id and b.product_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id='$cbo_company_name' $str_cond
		group by  d.id, d.wo_number_prefix_num, d.wo_number, d.supplier_id, d.wo_date, d.currency_id, d.location_id, d.delivery_date, d.attention, d.wo_basis_id, a.id, a.requ_no, a.requ_prefix_num, a.requisition_date, a.company_id, a.division_id, a.department_id, a.section_id, b.item_category, a.store_name, a.pay_mode, a.source, a.cbo_currency, a.delivery_date, b.cons_uom, c.id, c.item_group_id, c.sub_group_name, c.unit_of_measure, c.item_code, c.item_description, c.item_size, e.rate, b.remarks
		order by d.id,c.id,a.id";
	}
	//echo $sql_req_wo;die;
	$req_result=sql_select($sql_req_wo);

	foreach($req_result as $val)
	{
		if($row[csf("wo_id")]=='' || $row[csf("wo_id")]==0)
		{
			$tem_arr2[$val[csf("wo_id")]][$val[csf("prod_id")]]++;
		}

		if($checkArr_wo_number[$val[csf("wo_number")]]=='')
		{
			$checkArr_wo_number[$val[csf("wo_number")]]=$val[csf("wo_number")];
			$wo_numbers.="'".$val[csf("wo_number")]."',";
		}

	}

	$wo_numbers=rtrim($wo_numbers,',');
	if ($wo_numbers != ''){
		$pi_id_arr=array();
		$sql_pi_res=sql_select("select a.wo_number, b.id as pi_id from wo_non_order_info_mst a, com_pi_item_details b where a.wo_number=b.work_order_no and a.wo_number in($wo_numbers)");
		foreach ($sql_pi_res as $val) {
			$pi_id_arr[$val[csf("wo_number")]]=$val[csf("pi_id")];
		}
		//echo '<pre>';print_r($pi_id_arr);
	}

	$req_wo_recv_arr=array();
	$req_wo_recv_sql=sql_select("select a.id as recv_id, a.receive_basis, a.booking_id, a.currency_id, b.prod_id, b.order_uom, sum(b.order_qnty) as recv_qnty, sum(b.order_amount) as recv_amt, b.remarks as comments, b.item_category
	from  inv_receive_master a, inv_transaction b where a.id=b.mst_id and b.transaction_type=1 and b.item_category not in(1,2,3,13,14) and a.receive_basis in(1,2,7) and a.booking_id>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.receive_basis, a.booking_id, a.currency_id, b.prod_id, b.order_uom, b.item_category, b.remarks");
	foreach($req_wo_recv_sql as $row)
	{
		if ($row[csf("receive_basis")] == 1)
		{
			$req_wo_recv_arr[$row[csf("booking_id")]][$row[csf("prod_id")]][1]['recv_id']=$row[csf("recv_id")];
			$req_wo_recv_arr[$row[csf("booking_id")]][$row[csf("prod_id")]][1]['recv_qnty']+=$row[csf("recv_qnty")];
			$req_wo_recv_arr[$row[csf("booking_id")]][$row[csf("prod_id")]][1]['recv_amt']+=$row[csf("recv_amt")];
			$req_wo_recv_arr[$row[csf("booking_id")]][$row[csf("prod_id")]][1]['order_uom']=$row[csf("order_uom")];
			$req_wo_recv_arr[$row[csf("booking_id")]][$row[csf("prod_id")]][1]['currency_id']=$row[csf("currency_id")];
			if ($row[csf("item_category")]==16 && $row[csf("comments")] != '') //only maintenance category
			$req_wo_recv_arr[$row[csf("booking_id")]][$row[csf("prod_id")]][1]['comments'].=$row[csf("comments")].',';
		}
		else if ($row[csf("receive_basis")] == 2)
		{
			$req_wo_recv_arr[$row[csf("booking_id")]][$row[csf("prod_id")]][2]['recv_id']=$row[csf("recv_id")];
			$req_wo_recv_arr[$row[csf("booking_id")]][$row[csf("prod_id")]][2]['recv_qnty']+=$row[csf("recv_qnty")];
			$req_wo_recv_arr[$row[csf("booking_id")]][$row[csf("prod_id")]][2]['recv_amt']+=$row[csf("recv_amt")];
			$req_wo_recv_arr[$row[csf("booking_id")]][$row[csf("prod_id")]][2]['order_uom']=$row[csf("order_uom")];
			$req_wo_recv_arr[$row[csf("booking_id")]][$row[csf("prod_id")]][2]['currency_id']=$row[csf("currency_id")];
			if ($row[csf("item_category")]==16 && $row[csf("comments")] != '') //only maintenance category
			$req_wo_recv_arr[$row[csf("booking_id")]][$row[csf("prod_id")]][2]['comments'].=$row[csf("comments")].',';
		}
		else //receive_basis=7
		{
			$req_wo_recv_arr[$row[csf("booking_id")]][$row[csf("prod_id")]][7]['recv_id']=$row[csf("recv_id")];
			$req_wo_recv_arr[$row[csf("booking_id")]][$row[csf("prod_id")]][7]['recv_qnty']+=$row[csf("recv_qnty")];
			$req_wo_recv_arr[$row[csf("booking_id")]][$row[csf("prod_id")]][7]['recv_amt']+=$row[csf("recv_amt")];
			$req_wo_recv_arr[$row[csf("booking_id")]][$row[csf("prod_id")]][7]['order_uom']=$row[csf("order_uom")];
			$req_wo_recv_arr[$row[csf("booking_id")]][$row[csf("prod_id")]][7]['currency_id']=$row[csf("currency_id")];
			if ($row[csf("item_category")]==16 && $row[csf("comments")] != '') //only maintenance category
			$req_wo_recv_arr[$row[csf("booking_id")]][$row[csf("prod_id")]][7]['comments'].=$row[csf("comments")].',';
		}
		
	}
	//echo '<pre>';print_r($req_wo_recv_arr);die;
	
	//if ($db_type==2) $group_concat="group_concat(b.remarks) as remarks";
	//else $group_concat="listagg(cast(b.remarks as varchar2(4000)),',') within group (order by b.id) as grouping";
	/*$req_wo_recv_sql=sql_select("select a.id as recv_id, a.booking_id, a.currency_id, b.prod_id, b.order_uom, sum(b.order_qnty) as recv_qnty, sum(b.order_amount) as recv_amt, b.remarks as comments, b.item_category
	from  inv_receive_master a, inv_transaction b where a.id=b.mst_id and b.transaction_type=1 and b.item_category not in(1,2,3,13,14) and  a.receive_basis=2 and a.booking_id>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.booking_id, a.currency_id, b.prod_id, b.order_uom, b.item_category, b.remarks");
	foreach($req_wo_recv_sql as $row)
	{
		$req_wo_recv_arr[$row[csf("booking_id")]][$row[csf("prod_id")]][1]['recv_id']=$row[csf("recv_id")];
		$req_wo_recv_arr[$row[csf("booking_id")]][$row[csf("prod_id")]][1]['recv_qnty']+=$row[csf("recv_qnty")];
		$req_wo_recv_arr[$row[csf("booking_id")]][$row[csf("prod_id")]][1]['recv_amt']+=$row[csf("recv_amt")];
		$req_wo_recv_arr[$row[csf("booking_id")]][$row[csf("prod_id")]][1]['order_uom']=$row[csf("order_uom")];
		$req_wo_recv_arr[$row[csf("booking_id")]][$row[csf("prod_id")]][1]['currency_id']=$row[csf("currency_id")];
		if ($row[csf("item_category")]==16 && $row[csf("comments")] != '') //only maintenance category
		$req_wo_recv_arr[$row[csf("booking_id")]][$row[csf("prod_id")]][1]['comments'].=$row[csf("comments")].',';
	}

	
	$req_req_recv_sql=sql_select("select a.id as recv_id, a.booking_id, a.currency_id, b.prod_id, b.order_uom, sum(b.order_qnty) as recv_qnty, sum(b.order_amount) as recv_amt, b.item_category, b.remarks as comments 
	from  inv_receive_master a, inv_transaction b where a.id=b.mst_id and b.transaction_type=1 and b.item_category not in(1,2,3,13,14) and  a.receive_basis=7 and a.booking_id>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.currency_id, a.booking_id, b.prod_id, b.order_uom, b.item_category, b.remarks");
	foreach($req_req_recv_sql as $row)
	{
		$req_wo_recv_arr[$row[csf("booking_id")]][$row[csf("prod_id")]][2]['recv_id']=$row[csf("recv_id")];
		$req_wo_recv_arr[$row[csf("booking_id")]][$row[csf("prod_id")]][2]['recv_qnty']+=$row[csf("recv_qnty")];
		$req_wo_recv_arr[$row[csf("booking_id")]][$row[csf("prod_id")]][2]['recv_amt']+=$row[csf("recv_amt")];
		$req_wo_recv_arr[$row[csf("booking_id")]][$row[csf("prod_id")]][2]['order_uom']=$row[csf("order_uom")];
		$req_wo_recv_arr[$row[csf("booking_id")]][$row[csf("prod_id")]][2]['currency_id']=$row[csf("currency_id")];
		if ($row[csf("item_category")]==16 && $row[csf("comments")] != '') //only maintenance category
		$req_wo_recv_arr[$row[csf("booking_id")]][$row[csf("prod_id")]][2]['comments'].=$row[csf("comments")].',';
	}*/
	//var_dump($req_wo_recv_arr[186][4546][2]['recv_qnty']);die;
	//echo $sql_requisition;die;
	$store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');
	$item_group_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
	$division_arr=return_library_array( "select id, division_name from lib_division",'id','division_name');
	$department_arr=return_library_array( "select id, department_name from lib_department",'id','department_name');
	$section_arr=return_library_array( "select id, section_name from lib_section",'id','section_name');
	ob_start();
	?>
    <div style="width:2780px">
        <table width="2540" cellpadding="0" cellspacing="0" id="caption"  align="left">
        <tr>
            <td align="center" width="100%"  class="form_caption" colspan="26"><strong style="font-size:18px">Company Name:<? echo " ". $company_arr[str_replace("'","",$cbo_company_name)]; ?></strong></td>
        </tr> 
        <tr>  
            <td align="center" width="100%" class="form_caption" colspan="26"><strong style="font-size:18px"><? echo $report_title; ?></strong></td>
        </tr>
        <tr>  
            <td align="center" width="100%"  class="form_caption" colspan="26"><strong style="font-size:18px">From : <? echo $txt_date_from; ?> To : <? echo $txt_date_to; ?></strong></td>
        </tr>
        </table>
    	<br />
        <table width="2540"  align="left">
        	<tr>
            	<td style="font-size:18; font-weight:bold;">Based on Requisition</td>
            </tr>
        </table>
        <div style="width:2030px; float:left" align="left">
            <table width="2030" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_1"  align="left">
            <thead>
            	<tr>
                	<th colspan="22" title="Without WO">Requisition Details</th>
                </tr>
                <tr>
                    <th width="30">Sl</th>
                    <th width="60">Req. No</th>
                    <th width="70">Req. Date</th>
                    <th width="100">Store Name</th>
                    <th width="70">Pay Mode</th>
                    <th width="70">Source</th>
                    <th width="70">Currency</th>
                    <th width="70">Delivery Date</th>
                    <th width="120">Item Category</th>
                    <th width="130">Item Group</th>
                    <th width="100">Item Sub. Group</th>
                    <th width="80">Item Code</th>
                    <th width="200">Item Description</th>
                    <th width="100">Required For</th>
                    <th width="150">Requisition For</th>
                    <th width="60">UOM</th>
                    <th width="80">Reqsn Quantity</th>
                    <th width="80">Receive Quantity</th>
                    <th width="70">Reqsn Rate</th>
                    <th width="70">Reqsn Amount</th>
                    <th width="70">Receive Balance</th>
                    <th width="">Remarks</th>
                 
                </tr>
            </thead>
        </table>
        <div style="width:2050px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;" id="scroll_body3" align="left">
        <table width="2030" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body3" align="left" >
            <tbody>
            <?
            $kk=1;  
			
			//var_dump($tem_arr);die;
			//$total_req_qty+=$row[csf("req_quantity")];
			$total_reqqnty=$total_amount=$tot_woqnty=$total_wovalue=$total_req_qnty=$total_wo_qnty=$wo_total_balan=$total_recv_qty=0;
            foreach($req_result as $row)
            {
            	//echo $row[csf("wo_id")]."K";
				if($row[csf("wo_id")]=='' || $row[csf("wo_id")]==0)
				{
					$total_reqqnty+=$row[csf("req_quantity")];
					$total_amount+=$row[csf("amount")];
					$total_woqnty+=$row[csf("wo_qnty")];
					$total_wovalue+=$row[csf("wo_value")];
					if($row[csf("pay_mode")]==4) $recv_qnty=$req_wo_recv_arr[$row[csf("req_id")]][$row[csf("prod_id")]][7]['recv_qnty']; else $recv_qnty='';
					$rcv_bal=$row[csf("req_quantity")]-$recv_qnty;
					$total_recv_qnty+=$recv_qnty;
					$total_rcv_bal+=$rcv_bal;
					//$wo_total+= $wo_balance;
					if ($kk%2==0)
					$bgcolor="#E9F3FF";
					else
					$bgcolor="#FFFFFF";
					$id=$row_result[csf('req_id')];
					$reqsn_rate=$row[csf("amount")]/$row[csf("req_quantity")];
					$total_req_qnty+=$row[csf("req_quantity")];
					//$total_amnt+=$row[csf("amount")];
					$total_wo_qnty+=$tem_arr[$row[csf("wo_id")]][$row[csf("prod_id")]];
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('trwo_<? echo $kk; ?>','<? echo $bgcolor; ?>')" id="trwo_<? echo $kk; ?>">
	                    <td width="30" align="center"><p><? echo $kk; //echo $row[csf("req_id")];?></p></td>
	                    
	                    <td width="60" align="center"><p> <a href="##" onclick="openmypage2(<? echo $row[csf("req_id")]; ?>,<? echo "'".str_replace("'","",$cbo_company_name)."'"; ?>,'requisition_qnty')"><? echo $row[csf("requ_prefix_num")]; ?></a></p></td>
	                    
	                    <td width="70" align="center"><p><? if($row[csf("requisition_date")]!="" && $row[csf("requisition_date")]!='0000-00-00') echo change_date_format($row[csf("requisition_date")]); ?></p></td>
	                    <td width="100"><p><? echo $store_arr[$row[csf("store_name")]]; ?></p></td>
	                    <td width="70"><p><? echo $pay_mode[$row[csf("pay_mode")]]; ?></p></td>
	                    <td width="70"><p><? echo $source[$row[csf("source")]]; ?></p></td>
	                    <td width="70"><p><? echo $currency[$row[csf("cbo_currency")]]; ?></p></td>
	                    <td width="70"><p><? if($row[csf("delivery_date")]!="" && $row[csf("delivery_date")]!='0000-00-00') echo change_date_format($row[csf("delivery_date")]); ?></p></td>
	                    <td width="120"><p><? echo $item_category[$row[csf("item_category")]]; ?></p></td>
	                    <td width="130"><p><? echo $item_group_arr[$row[csf("item_group_id")]]; ?></p></td>
	                    <td width="100"><p><? echo $row[csf("sub_group_name")]; ?></p></td>
	                    <td width="80"><p><? echo $row[csf("item_code")]; ?></p></td>
	                    <td width="200"><p><? echo $row[csf("product_name_details")]; ?></p></td>
	                    <td width="100"><p><? echo $use_for[$row[csf("required_for")]]; ?></p></td>
	                    <td width="150"><p>
						<?
						$reqsition_for=""; 
						if($row[csf("division_id")]>0) $reqsition_for=$division_arr[$row[csf("division_id")]];
						if($row[csf("department_id")]>0){ if($reqsition_for!="") $reqsition_for.=", ".$department_arr[$row[csf("department_id")]];  else $reqsition_for=$department_arr[$row[csf("department_id")]]; }
						if($row[csf("section_id")]>0){if($reqsition_for!="") $reqsition_for.=", ".$section_arr[$row[csf("section_id")]];  else $reqsition_for=$section_arr[$row[csf("section_id")]];} 
						echo $reqsition_for;
						?></p></td>
	                    <td width="60"><p><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></p></td>
	                    <td width="80" align="right"><p><? echo number_format($row[csf("req_quantity")],0); ?></p></td>

	                    <td width="80" align="right"><a href="##" onClick="openmypage_popup(<? echo $row[csf("req_id")]; ?>,'<? echo $row[csf("prod_id")]; ?>','7','Receive Info','receive_popup');" ><p><? echo number_format($recv_qnty,0); ?></p></a></td>
	    				<td width="70" align="right"><p><? echo number_format($reqsn_rate,2); ?></p></td>
	                    <td width="70" align="right"><p><? echo number_format($row[csf("amount")],2); ?></p></td>
	                    <td width="70" align="right"><p><? echo number_format($rcv_bal,2); ?></p></td>
	                    <td width=""><p><? echo $row[csf("remarks")]; ?></p></td>
	                    <?
							$recv_rate=$req_wo_recv_arr[$row[csf("req_id")]][$row[csf("prod_id")]][7]['recv_amt']/$req_wo_recv_arr[$row[csf("req_id")]][$row[csf("prod_id")]][7]['recv_qnty'];
							$total_recv_qty+=$req_wo_recv_arr[$row[csf("req_id")]][$row[csf("prod_id")]][7]['recv_qnty'];
						?>
					</tr>
					<?
	 				$kk++;
				}
            }
            ?>
            </tbody>
            
        </table>
            </div>
        <table width="2030" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_footer_2"  align="left">
        <tfoot>
                <tr>
                    <th width="30">&nbsp;</th>
                    <th width="60">&nbsp;</th>
                    <th width="70">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="70">&nbsp;</th>
                    <th width="70">&nbsp;</th>
                    <th width="70">&nbsp;</th>
                    <th width="70">&nbsp;</th>
                    <th width="120">&nbsp;</th>
                    <th width="130">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="200">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="150">&nbsp;</th>
                    <th width="60">Total:</th>
                    <th width="80"><? echo number_format($total_reqqnty,2);?></th>
                    <th width="80"><? echo number_format($total_recv_qnty,2);?></th>
                    <th width="70">&nbsp;</th>
                    <th width="70"><? echo number_format($total_amount,2);?></th>
                    <th width="70"><? echo number_format($total_rcv_bal,2);?></th>
                    <th width="">&nbsp;</th>
                  
                </tr>
            </tfoot>
        </table>
    
       
        </div>
        <br />
         <table width="2900" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_1"  align="left">
            <thead>
            	<tr>
                	<th colspan="18">Requisition and Purchase Order Details</th>
                    <th colspan="13">Work Order and Receive Details</th>
                </tr>
                <tr>
                    <th width="30">Sl</th>
                    <th width="120">Req. No</th>
                    <th width="70">Req. Date</th>
                    <th width="100">Store Name</th>
                    <th width="70">Pay Mode</th>
                    <th width="70">Source</th>
                    <th width="70">Currency</th>
                    <th width="70">Delivery Date</th>
                    <th width="120">Item Category</th>
                    <th width="130">Item Group</th>
                    <th width="100">Item Sub. Group</th>
                    <th width="80">Item Code</th>
                    <th width="200">Item Description</th>
                    <th width="100">Required For</th>
                    <th width="60">UOM</th>
                    <th width="80">Reqsn Quantity</th>
                    <th width="70">Rate</th>
                    <th width="100">Amount</th>
                    <th width="120">WO No</th>
                    <th width="80">WO Qnty</th>
                    <th width="100">WO Rate</th>
                    <th width="100">WO Value</th>
                    <th width="70">WO Date</th>
                    <th width="80">WO Balance</th>
                    <th width="130">Supplier</th>
                    <th width="80">Recv Qty.</th>
                    <th width="60">Rate</th>
                    <th width="100">Amount</th>
                    <th width="80">Recv Balance</th>
                    <th width="80">Recv Currency</th>
                    <th width="">Comments</th>
                </tr>
            </thead>
        </table>
        <div style="width:2920px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;" id="scroll_body" align="left">
        <table width="2900" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left">
            <tbody>
            <?
            $k=1;
            
			// foreach($req_result as $val)
			// {
			// 	if($val[csf("wo_id")]>0 || $val[csf("wo_id")]!='')
			// 	{
			// 		$tem_arr[$val[csf("wo_id")]][$val[csf("prod_id")]]++;
			// 	}
			// 	else if(empty($val[csf("wo_id")])) 
			// 	{
			// 		$tem_arr[$val[csf("req_id")]][$val[csf("prod_id")]]++;
			// 	}
			// }
			$array_check_arr=array();
			foreach($req_result as $val)
			{
				if($val[csf("wo_id")]>0 || $val[csf("wo_id")]!='')
				{
					if(!in_array($val[csf("wo_id")].'*'.$val[csf("prod_id")],$array_check_arr))
					{
						$array_check_arr[]=$val[csf("wo_id")].'*'.$val[csf("prod_id")];
						$tem_arr[$val[csf("wo_id")]][$val[csf("prod_id")]]++;
					}
				}
				else if(empty($val[csf("wo_id")])) 
				{
					if(!in_array($val[csf("req_id")].'*'.$val[csf("prod_id")],$array_check_arr))
					{
						$array_check_arr[]=$val[csf("req_id")].'*'.$val[csf("prod_id")];
						$tem_arr[$val[csf("req_id")]][$val[csf("prod_id")]]++;
					}
				}	
			}
			
			//echo $val[csf("req_id")].'D';die;
		//var_dump($tem_arr);die;
			//$total_req_qty+=$row[csf("req_quantity")];
			$tot_reqqnty=$tot_amount=$tot_woqnty=$tot_wovalue=$total_req_qty=$total_amount=$total_wo_qty=$tot_wo_total_bal=$tot_rec_qty=0;
			$array_check=array();
            foreach($req_result as $row)
            {
            	///echo $row[csf("wo_id")].'mjjD';die;
                /*if($row[csf("wo_id")]>0 && $row[csf("wo_id")]!='')
                {
                    echo 'Tipu';die;
                }*/
				if($row[csf("wo_id")]>0 && $row[csf("wo_id")]!='')
				{
					
    				$tot_reqqnty+=$row[csf("req_quantity")];
    				$tot_amount+=$row[csf("amount")];
    				$tot_woqnty+=$row[csf("wo_qnty")];
    				$tot_wovalue+=$row[csf("wo_value")];
    				//$wo_total+= $wo_balance;
    				if ($k%2==0)
    				$bgcolor="#E9F3FF";
    				else
    				$bgcolor="#FFFFFF";
    				$id=$row_result[csf('req_id')];
    				$reqsn_rate=$row[csf("amount")]/$row[csf("req_quantity")];
    				$total_req_qty+=$row[csf("req_quantity")];
    				$total_amount+=$row[csf("amount")];
    				$total_wo_qty+=$tem_arr[$row[csf("wo_id")]][$row[csf("prod_id")]];

                    if($row[csf('item_category')]==4 || $row[csf('item_category')]==11)
                    {
                        $report_data=$cbo_company_name.'*'.$row[csf("wo_number")].'*'.$row[csf("item_category")].'*'.$row[csf("supplier_id")].'*'.change_date_format($row[csf("wo_date")]).'*'.$req_wo_recv_arr[$row[csf("wo_id")]][$row[csf("prod_id")]][2]['currency_id'].'*'.$row[csf("wo_basis_id")].'*'.$row[csf("pay_mode")].'*'.$row[csf("source")].'*'.change_date_format($row[csf("delivery_date")]).'*'.$row[csf("attention")].'*'.$row[csf("requ_no")].'*'.$row[csf("requ_id")].'***'.$row[csf("wo_id")].'*Procurement Progress Report*'.$row[csf("location_id")].'*1';

                        $report_action="stationary_work_print";
                        $report_path="../work_order/requires/stationary_work_order_controller";
                    }
                    else if(in_array($row[csf('item_category')], explode(',', '8,9,10,15,16,17,18,19,20,21,22,32,34,36,35,37,38,39,33,40,41,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,69,70,89,90,91,92,93,94')))
                    {
                        $report_data= $cbo_company_name.'*'.$row[csf("wo_id")].'*Procurement Progress Report*'.$row[csf("location_id")]; 
                        $report_action="spare_parts_work_print";
                        $report_path="../work_order/requires/spare_parts_work_order_controller";
                    }
                    else if(in_array($row[csf('item_category')], explode(',', '5,6,7,23')))
                    {
                        $report_data= $cbo_company_name.'*'.$row[csf("wo_id")].'*Procurement Progress Report'; 
                        $report_action="dyes_chemical_work_print";
                        $report_path="../work_order/requires/dyes_and_chemical_work_order_controller";
                    }
					$required_for='';
					$required_for_arr=array_unique(explode(",",$row[csf("required_for")]));
					foreach($required_for_arr as $val)
					{
						$required_for.=$use_for[$val].', ';
					}

    				?>
    				<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
                        <td width="30" align="center"><p><? echo $k; //echo $row[csf("req_id")];?></p></td>
                        
                        <td width="120" align="center"><p> <a href="##" onclick="openmypage2(<? echo $row[csf("req_id")]; ?>,<? echo "'".str_replace("'","",$cbo_company_name)."'"; ?>,'requisition_qnty')"><? echo $row[csf("requ_no")]; ?></a></p></td>
                        
                        <td width="70" align="center"><p><? if($row[csf("requisition_date")]!="" && $row[csf("requisition_date")]!='0000-00-00') echo change_date_format($row[csf("requisition_date")]); ?></p></td>
                        <td width="100"><p><? echo $store_arr[$row[csf("store_name")]]; ?></p></td>
                        <td width="70"><p><? echo $pay_mode[$row[csf("pay_mode")]]; ?></p></td>
                        <td width="70"><p><? echo $source[$row[csf("source")]]; ?></p></td>
                        <td width="70"><p><? echo $currency[$row[csf("cbo_currency")]]; ?></p></td>
                        <td width="70"><p><? if($row[csf("delivery_date")]!="" && $row[csf("delivery_date")]!='0000-00-00') echo change_date_format($row[csf("delivery_date")]); ?></p></td>
                        <td width="120"><p><? echo $item_category[$row[csf("item_category")]]; ?></p></td>
                        <td width="130"><p><? echo $item_group_arr[$row[csf("item_group_id")]]; ?></p></td>
                        <td width="100"><p><? echo $row[csf("sub_group_name")]; ?></p></td>
                        <td width="80"><p><? echo $row[csf("item_code")]; ?></p></td>
                        <td width="200"><p><? echo $row[csf("product_name_details")]; ?></p></td>
                        <td width="100"><p><? echo rtrim($required_for,', '); ?></p></td>
                        <td width="60"><p><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($row[csf("req_quantity")],0); ?></p></td>
                        <td width="70" align="right"><p><? echo number_format($reqsn_rate,2); ?></p></td>
                        <td width="100" align="right"><p><? echo number_format($row[csf("amount")],2); ?></p></td>
                        <?
    					//$wo_total+=$row[csf("req_quantity")]-$row[csf("wo_qnty")];
    						//$recv_rate=$req_wo_recv_arr[$row[csf("wo_id")]][$row[csf("prod_id")]][2]['recv_amt']/$req_wo_recv_arr[$row[csf("wo_id")]][$row[csf("prod_id")]][2]['recv_qnty'];

    						if(!in_array($row[csf("wo_id")].'*'.$row[csf("prod_id")],$array_check))
    						{
    							$recv_qnty=0;
                        		$recv_rate=0;
                        		$recv_amt=0;
                        		$rec_currency=$comments='';
                        		$wo_id=0;
    							$array_check[]=$row[csf("wo_id")].'*'.$row[csf("prod_id")];
    							?>
    							<td width="120"  valign="middle" align="center" rowspan="<? echo ($tem_arr[$row[csf("wo_id")]][$row[csf("prod_id")]]); ?>">
                                    <p>
                                        <a href="javascript:generate_wo_print_report('<? echo $report_data; ?>','<? echo $report_action;?>','<? echo $report_path;?>')">
                                            <? 
                                                echo  $row[csf("wo_number")];
                                                //$row[csf("wo_number_prefix_num")]; 
                                            ?>
                                        </a>
                                    </p>
                                </td>
                                <td width="80"  valign="middle" align="right" rowspan="<? echo ($tem_arr[$row[csf("wo_id")]][$row[csf("prod_id")]]); ?>"><p><? echo  number_format($row[csf("wo_qnty")],2); ?></p></td>

    							<td width="100"  valign="middle" align="right" rowspan="<? echo ($tem_arr[$row[csf("wo_id")]][$row[csf("prod_id")]]); ?>"><p> <? echo  $row[csf("rate")]; ?> </p></td>                            

                                <td width="100"  valign="middle" align="right" rowspan="<? echo ($tem_arr[$row[csf("wo_id")]][$row[csf("prod_id")]]); ?>"><p><? echo  number_format($row[csf("wo_value")],2); ?></p></td>
    							<td width="70"  valign="middle" align="center" rowspan="<? echo ($tem_arr[$row[csf("wo_id")]][$row[csf("prod_id")]]); ?>"><p><? if($row[csf("wo_date")]!="" && $row[csf("wo_date")]!='0000-00-00') echo change_date_format($row[csf("wo_date")]); ?></p></td>


                                <td width="80"  valign="middle" align="right" rowspan="<? echo ($tem_arr[$row[csf("wo_id")]][$row[csf("prod_id")]]); ?>"><p>
                                <?
                                $wo_balance=$row[csf("req_quantity")]-$row[csf("wo_qnty")];
                                 echo  number_format($wo_balance,2); 
                                 $tot_wo_total_bal+= $wo_balance;

                                ?></p></td>
    							<td width="130"  valign="middle" rowspan="<? echo ($tem_arr[$row[csf("wo_id")]][$row[csf("prod_id")]]); ?>"><p><? echo $supplier_arr[$row[csf("supplier_id")]]; ?></p></td>

    							<?
    							if ($req_wo_recv_arr[$pi_id_arr[$row[csf("wo_number")]]][$row[csf("prod_id")]][1]['recv_qnty']>0) 
								{
									$recv_qnty=$req_wo_recv_arr[$pi_id_arr[$row[csf("wo_number")]]][$row[csf("prod_id")]][1]['recv_qnty'];
									$tot_rec_qty+=$req_wo_recv_arr[$pi_id_arr[$row[csf("wo_number")]]][$row[csf("prod_id")]][1]['recv_qnty'];
									$recv_rate=$req_wo_recv_arr[$pi_id_arr[$row[csf("wo_number")]]][$row[csf("prod_id")]][1]['recv_amt']/$req_wo_recv_arr[$pi_id_arr[$row[csf("wo_number")]]][$row[csf("prod_id")]][1]['recv_qnty'];
									$recv_amt=$req_wo_recv_arr[$pi_id_arr[$row[csf("wo_number")]]][$row[csf("prod_id")]][1]['recv_amt'];
									$rec_currency=$currency[$req_wo_recv_arr[$pi_id_arr[$row[csf("wo_number")]]][$row[csf("prod_id")]][1]['currency_id']];
									$comments=rtrim($req_wo_recv_arr[$pi_id_arr[$row[csf("wo_number")]]][$row[csf("prod_id")]][1]['comments'],',');
									$receive_basis=1; //pi
									$wo_id=$pi_id_arr[$row[csf("wo_number")]];
								}    									
								else if($req_wo_recv_arr[$row[csf("wo_id")]][$row[csf("prod_id")]][2]['recv_qnty']>0)
								{    									
									$recv_qnty=$req_wo_recv_arr[$row[csf("wo_id")]][$row[csf("prod_id")]][2]['recv_qnty'];
									$tot_rec_qty+=$req_wo_recv_arr[$row[csf("wo_id")]][$row[csf("prod_id")]][2]['recv_qnty'];
									$recv_rate=$req_wo_recv_arr[$row[csf("wo_id")]][$row[csf("prod_id")]][2]['recv_amt']/$req_wo_recv_arr[$row[csf("wo_id")]][$row[csf("prod_id")]][2]['recv_qnty'];
									$recv_amt=$req_wo_recv_arr[$row[csf("wo_id")]][$row[csf("prod_id")]][2]['recv_amt'];
									$rec_currency=$currency[$req_wo_recv_arr[$row[csf("wo_id")]][$row[csf("prod_id")]][2]['currency_id']];
									$comments=rtrim($req_wo_recv_arr[$row[csf("wo_id")]][$row[csf("prod_id")]][2]['comments'],',');
									$receive_basis=2; //booking
									$wo_id=$row[csf("wo_id")];
								}
    							?>


    							<td width="80" align="right"  valign="middle" rowspan="<? echo ($tem_arr[$row[csf("wo_id")]][$row[csf("prod_id")]]); ?>"><a href="##" onClick="openmypage_popup(<? echo $wo_id; ?>,'<? echo $row[csf("prod_id")]; ?>',<? echo $receive_basis; ?>,'Receive Info','receive_popup');" >
    								<p>
    								<? 
    								echo number_format($recv_qnty,2);
    								?>    									
    								</p></a>
    							</td>
    							<td width="60" align="right"  valign="middle" rowspan="<? echo ($tem_arr[$row[csf("wo_id")]][$row[csf("prod_id")]]); ?>" title="Rec Amt/Rec Qnty"><p>
    								<? 
    									echo number_format($recv_rate,2);
    								?>    									
    								</p></td>
    							<td width="100" align="right"  valign="middle" rowspan="<? echo ($tem_arr[$row[csf("wo_id")]][$row[csf("prod_id")]]); ?>"><p><? echo number_format($recv_amt,2); ?></p></td>

                                <td width="80" align="right"  valign="middle" rowspan="<? echo ($tem_arr[$row[csf("wo_id")]][$row[csf("prod_id")]]); ?>"><p><? $receive_balance=$row[csf("wo_qnty")]-$recv_qnty; echo number_format($receive_balance,2); ?></p></td>

    							<td  width="80" valign="middle" rowspan="<? echo ($tem_arr[$row[csf("wo_id")]][$row[csf("prod_id")]]); ?>"><p><? echo $rec_currency; ?></p></td>

    							<td  valign="middle" rowspan="<? echo ($tem_arr[$row[csf("wo_id")]][$row[csf("prod_id")]]); ?>"><p><? echo $comments; ?></p></td>
    							<?
    							
    						}
    					
    					
    					?>
    				</tr>
    				<?
     					$k++;
				}
				/*else if(empty($row[csf("wo_id")])) 
				{
    				$tot_reqqnty+=$row[csf("req_quantity")];
    				$tot_amount+=$row[csf("amount")];
    				$tot_woqnty+=$row[csf("wo_qnty")];
    				$tot_wovalue+=$row[csf("wo_value")];
    				//$wo_total+= $wo_balance;
    				if ($k%2==0)
    				$bgcolor="#E9F3FF";
    				else
    				$bgcolor="#FFFFFF";
    				$id=$row_result[csf('req_id')];
    				//echo $row[csf("req_id")].'mjjD';die;
    				//echo $id; die; 
    				
    				$reqsn_rate=$row[csf("amount")]/$row[csf("req_quantity")];
    				$total_req_qty+=$row[csf("req_quantity")];
    				$total_amount+=$row[csf("amount")];
    				$total_wo_qty+=$tem_arr[$row[csf("wo_id")]][$row[csf("prod_id")]];

                    if($row[csf('item_category')]==4 || $row[csf('item_category')]==11)
                    {
                        $report_data=$cbo_company_name.'*'.$row[csf("wo_number")].'*'.$row[csf("item_category")].'*'.$row[csf("supplier_id")].'*'.change_date_format($row[csf("wo_date")]).'*'.$req_wo_recv_arr[$row[csf("wo_id")]][$row[csf("prod_id")]][1]['currency_id'].'*'.$row[csf("wo_basis_id")].'*'.$row[csf("pay_mode")].'*'.$row[csf("source")].'*'.change_date_format($row[csf("delivery_date")]).'*'.$row[csf("attention")].'*'.$row[csf("requ_no")].'*'.$row[csf("requ_id")].'***'.$row[csf("wo_id")].'*Procurement Progress Report*'.$row[csf("location_id")].'*1';

                        $report_action="stationary_work_print";
                        $report_path="../work_order/requires/stationary_work_order_controller";
                    }
                    else if(in_array($row[csf('item_category')], explode(',', '8,9,10,15,16,17,18,19,20,21,22,32,34,36,35,37,38,39,33,40,41,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,69,70,89,90,91,92,93,94')))
                    {
                        $report_data= $cbo_company_name.'*'.$row[csf("wo_id")].'*Procurement Progress Report*'.$row[csf("location_id")]; 
                        $report_action="spare_parts_work_print";
                        $report_path="../work_order/requires/spare_parts_work_order_controller";
                    }
                    else if(in_array($row[csf('item_category')], explode(',', '5,6,7,23')))
                    {
                        $report_data= $cbo_company_name.'*'.$row[csf("wo_id")].'*Procurement Progress Report'; 
                        $report_action="dyes_chemical_work_print";
                        $report_path="../work_order/requires/dyes_and_chemical_work_order_controller";
                    }

    				?>
    				<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
                        <td width="30" align="center"><p><? echo $k; //echo $row[csf("req_id")];?></p></td>
                        
                        <td width="60" align="center"><p> <a href="##" onclick="openmypage2(<? echo $row[csf("req_id")]; ?>,<? echo "'".str_replace("'","",$cbo_company_name)."'"; ?>,'requisition_qnty')"><? echo $row[csf("requ_prefix_num")]; ?></a></p></td>
                        
                        <td width="70" align="center"><p><? if($row[csf("requisition_date")]!="" && $row[csf("requisition_date")]!='0000-00-00') echo change_date_format($row[csf("requisition_date")]); ?></p></td>
                        <td width="100"><p><? echo $store_arr[$row[csf("store_name")]]; ?></p></td>
                        <td width="70"><p><? echo $pay_mode[$row[csf("pay_mode")]]; ?></p></td>
                        <td width="70"><p><? echo $source[$row[csf("source")]]; ?></p></td>
                        <td width="70"><p><? echo $currency[$row[csf("cbo_currency")]]; ?></p></td>
                        <td width="70"><p><? if($row[csf("delivery_date")]!="" && $row[csf("delivery_date")]!='0000-00-00') echo change_date_format($row[csf("delivery_date")]); ?></p></td>
                        <td width="120"><p><? echo $item_category[$row[csf("item_category")]]; ?></p></td>
                        <td width="130"><p><? echo $item_group_arr[$row[csf("item_group_id")]]; ?></p></td>
                        <td width="100"><p><? echo $row[csf("sub_group_name")]; ?></p></td>
                        <td width="80"><p><? echo $row[csf("item_code")]; ?></p></td>
                        <td width="200"><p><? echo $row[csf("product_name_details")]; ?></p></td>
                        <td width="100"><p><? echo $row[csf("required_for")]; ?></p></td>
                        <td width="60"><p><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($row[csf("req_quantity")],0); ?></p></td>
                        <td width="70" align="right"><p><? echo number_format($reqsn_rate,2); ?></p></td>
                        <td width="100" align="right"><p><? echo number_format($row[csf("amount")],2); ?></p></td>
                        <?
    					//$wo_total+=$row[csf("req_quantity")]-$row[csf("wo_qnty")];
    						//$recv_rate=$req_wo_recv_arr[$row[csf("wo_id")]][$row[csf("prod_id")]][1]['recv_amt']/$req_wo_recv_arr[$row[csf("wo_id")]][$row[csf("prod_id")]][1]['recv_qnty'];
    						//echo "<pre>";
    						//print_r($req_wo_recv_arr); 
    						//echo $row[csf("req_id")].'mjjD';die;	
    						$recv_rate=$req_wo_recv_arr[$row[csf("req_id")]][$row[csf("prod_id")]][2]['recv_amt']/$req_wo_recv_arr[$row[csf("req_id")]][$row[csf("prod_id")]][2]['recv_qnty'];
    						
    					//echo $req_wo_recv_arr[$row[csf("req_id")]][$row[csf("prod_id")]][2]['recv_amt'].'mjjD';die;	
    						
    						if(!in_array($row[csf("req_id")].'*'.$row[csf("prod_id")],$array_check))
    						{
    							 
    							$array_check[]=$row[csf("req_id")].'*'.$row[csf("prod_id")];
    							?>
    							<td width="120"  valign="middle" align="center" rowspan="<? echo ($tem_arr[$row[csf("req_id")]][$row[csf("prod_id")]]); ?>">
                                    <p>
                                        <a href="javascript:generate_wo_print_report('<? echo $report_data; ?>','<? echo $report_action;?>','<? echo $report_path;?>')">
                                            <? 
                                                //echo  $row[csf("requ_no")];
    											 echo  $row[csf("wo_number")];
                                                //$row[csf("wo_number_prefix_num")]; 
                                            ?>
                                        </a>
                                    </p>
                                </td>
                                <td width="80"  valign="middle" align="right" rowspan="<? echo ($tem_arr[$row[csf("req_id")]][$row[csf("prod_id")]]); ?>"><p><? echo  number_format($row[csf("wo_qnty")],2); ?></p></td>

    							<td width="100"  valign="middle" align="right" rowspan="<? echo ($tem_arr[$row[csf("req_id")]][$row[csf("prod_id")]]); ?>"><p> <? echo  $row[csf("rate")]; ?> </p></td>                            

                                <td width="100"  valign="middle" align="right" rowspan="<? echo ($tem_arr[$row[csf("req_id")]][$row[csf("prod_id")]]); ?>"><p><? echo  number_format($row[csf("wo_value")],2); ?></p></td>
    							<td width="70"  valign="middle" align="center" rowspan="<? echo ($tem_arr[$row[csf("req_id")]][$row[csf("prod_id")]]); ?>"><p><? if($row[csf("wo_date")]!="" && $row[csf("wo_date")]!='0000-00-00') echo change_date_format($row[csf("wo_date")]); ?></p></td>


                                <td width="80"  valign="middle" align="right" rowspan="<? echo ($tem_arr[$row[csf("req_id")]][$row[csf("prod_id")]]); ?>"><p>
                                <?
                                $wo_balance=$row[csf("req_quantity")]-$row[csf("wo_qnty")];
                                 echo  number_format($wo_balance,2); 
                                 $tot_wo_total_bal+= $wo_balance;

                                ?></p></td>
    							<td width="130"  valign="middle" rowspan="<? echo ($tem_arr[$row[csf("req_id")]][$row[csf("prod_id")]]); ?>"><p><? echo $supplier_arr[$row[csf("supplier_id")]]; ?></p></td>
    							<td width="80" align="right"  valign="middle" rowspan="<? echo ($tem_arr[$row[csf("req_id")]][$row[csf("prod_id")]]); ?>"><a href="##" onClick="openmypage_popup(<? echo $row[csf("req_id")]; ?>,'<? echo $row[csf("prod_id")]; ?>','2','Receive Info','receive_popup2');" ><p>
    							<? echo number_format($req_wo_recv_arr[$row[csf("req_id")]][$row[csf("prod_id")]][2]['recv_qnty'],2); ?></p></a><p><? $tot_rec_qty+=$req_wo_recv_arr[$row[csf("req_id")]][$row[csf("prod_id")]][2]['recv_qnty'];?></p></td>
    							<td width="60" align="right"  valign="middle" rowspan="<? echo ($tem_arr[$row[csf("req_id")]][$row[csf("prod_id")]]); ?>"><p><? if($req_wo_recv_arr[$row[csf("req_id")]][$row[csf("prod_id")]][2]['recv_qnty']>0) echo number_format($recv_rate,2); else echo "0.00"; ?></p></td>
    							<td width="100" align="right"  valign="middle" rowspan="<? echo ($tem_arr[$row[csf("req_id")]][$row[csf("prod_id")]]); ?>"><p><? echo number_format($req_wo_recv_arr[$row[csf("req_id")]][$row[csf("prod_id")]][2]['recv_amt'],2); ?></p></td>
                                <td width="80" align="right"  valign="middle" 
                                rowspan="<? echo ($tem_arr[$row[csf("req_id")]][$row[csf("prod_id")]]); ?>"><p><? $receive_balance=$row[csf("req_quantity")]-$req_wo_recv_arr[$row[csf("req_id")]][$row[csf("prod_id")]][2]['recv_qnty']; echo number_format($receive_balance,2); ?></p></td>
    							<td  valign="middle" rowspan="<? echo ($tem_arr[$row[csf("req_id")]][$row[csf("prod_id")]]); ?>"><p><? echo $currency[$req_wo_recv_arr[$row[csf("req_id")]][$row[csf("prod_id")]][2]['currency_id']]; ?></p></td>
    							<?
    							
    						}
    					
    					
    					?>
    				</tr>
    				<?
     				$k++;
				}*/
            }
            ?>
            </tbody>
        </table>
        </div>
		<table width="2900" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_footer_1"  align="left">
            <tfoot>
                <tr>
                    <th width="30">&nbsp;</th>
                    <th width="120">&nbsp;</th>
                    <th width="70">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="70">&nbsp;</th>
                    <th width="70">&nbsp;</th>
                    <th width="70">&nbsp;</th>
                    <th width="70">&nbsp;</th>
                    <th width="120">&nbsp;</th>
                    <th width="130">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="200">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="60">Total:</th>
                    <th width="80"><? echo number_format($tot_reqqnty,2);?></th>
                    <th width="70">&nbsp;</th>
                    <th width="100"><? echo number_format($tot_amount,2);?></th>
                    <th width="120">&nbsp;</th>
                    <th width="80"><? echo number_format($tot_woqnty,2);?></th>
                    <th width="100"></th>
                    <th width="100"><? echo number_format($tot_wovalue,2);?></th>
                    <th width="70">&nbsp;</th>
                    <th width="80"><? echo number_format($tot_wo_total_bal,2);?></th>
                    <th width="130">&nbsp;</th>
                    <th width="80"><? echo number_format($tot_rec_qty,2);?></th>
                    <th width="60">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th >&nbsp;</th>
                </tr>
            </tfoot>
        </table>
        
		
        </div>
    	<br />
        <?
		if($db_type==0) $select_prod_size=",concat(c.item_description, c.item_size) as product_name_details"; 
		else if($db_type==2) $select_prod_size=",(c.item_description||c.item_size) as product_name_details";

		if ($txt_req_no=="")
		{
			$sql_wo_independ="select a.id, a.wo_number_prefix_num, a.wo_number, a.wo_date, a.supplier_id, a.pay_mode, a.source, a.currency_id, a.delivery_date, b.item_category_id, b.rate, c.id as prod_id, c.item_group_id, c.sub_group_name, c.item_code $select_prod_size, c.unit_of_measure , b.supplier_order_quantity as wo_qnty , b.amount as wo_value
			from wo_non_order_info_mst a, wo_non_order_info_dtls b,  product_details_master c
			where a.id=b.mst_id and b.item_id=c.id and a.wo_basis_id=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and a.company_name=$cbo_company_name $str_cond_independ ";
			
			//echo $sql_wo_independ;
			$req_result_independ=sql_select($sql_wo_independ);

			$independent_sql=sql_select("select b.prod_id, b.order_qnty as recv_qnty, b.order_amount as recv_amt, b.item_category, b.remarks as comments
			from  inv_receive_master a, inv_transaction b where a.id=b.mst_id and b.transaction_type=1 and b.item_category not in(1,2,3,13,14) and b.receive_basis=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");

			$independent_arr=array();
			foreach($independent_sql as $row)
			{
				$independent_arr[$row[csf("prod_id")]]['prod_id']=$row[csf("prod_id")];
				$independent_arr[$row[csf("prod_id")]]['recv_qnty']+=$row[csf("recv_qnty")];
				$independent_arr[$row[csf("prod_id")]]['recv_amt']+=$row[csf("recv_amt")];
				if ($row[csf("item_category")]==16 && $row[csf("comments")] != '') //only maintenance category
				$independent_arr[$row[csf("prod_id")]]['comments'].=$row[csf("comments")].', ';
			}
		}	
		

		?>
        <div style="width:2130px;" align="left">
            <table width="1900"  align="left">
                <tr>
                    <td style="font-size:18; font-weight:bold;">Based on Independent</td>
                </tr>
            </table>
            <br />
			<table width="2130" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_2"  align="left">
				<thead>
					<tr>
						<th width="30">Sl</th>
						<th width="60">WO No</th>
						<th width="70">WO Date</th>
						<th width="100">Supplier</th>
						<th width="100">Store Name</th>
						<th width="70">Pay Mode</th>
						<th width="70">Source</th>
						<th width="70">Currency</th>
						<th width="70">Delivery Date</th>
						<th width="130">Item Category</th>
						<th width="100">Item Group</th>
						<th width="80">Item Sub. Group</th>
						<th width="80">Item Code</th>
						<th width="200">Item Description</th>
						<th width="60">UOM</th>
                        <th width="80">WO Qnty</th>
                        <th width="100">WO Rate</th>
                        <th width="100">WO Value</th>
						<th width="80">Recv Qty.</th>
						<th width="70">Rate</th>
						<th width="100">Amount</th>
                        <th >Recv Balance</th>
                        <th width="150">Comments</th>
					</tr>
				</thead>
			</table>
        	<div style="width:2150px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;" id="scroll_body2" align="left">
			<table width="2130" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body2">
				<tbody>
				<?
				$i=1;
				
				//var_dump($tem_arr);die;
				$array_check=array();
				foreach($req_result_independ as $row)
				{
					$tot_wo_qty+=$row[csf("wo_qnty")];
					$tot_wo_value+=$row[csf("wo_value")];
 					$tot_rec+=$independent_arr[$row[csf("prod_id")]]['recv_qnty'];
 					$tot_amount+=$independent_arr[$row[csf("prod_id")]]['recv_amt'];
 					
					if ($i%2==0)
					$bgcolor="#E9F3FF";
					else
					$bgcolor="#FFFFFF";
					$wo_rate_inde=$independent_arr[$row[csf("prod_id")]]['recv_amt']/$independent_arr[$row[csf("prod_id")]]['recv_qnty'];
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
						<td width="30" align="center"><p><? echo $i;//$row_result[csf('id')];?></p></td>
						<td width="60" align="center"><p><a href="javascript:generate_trim_report('show_trim_booking_report2',1,'<? echo $row[csf("wo_number")]; ?>',<? echo $cbo_company_name;?>,1)"><? echo $row[csf("wo_number")]; ?></a></p></td>
						<td width="70" align="center"><p><? if($row[csf("wo_date")]!="" && $row[csf("wo_date")]!='0000-00-00') echo change_date_format($row[csf("wo_date")]); ?></p></td>
						<td width="100"><p><? echo $supplier_arr[$row[csf("supplier_id")]]; ?></p></td>
                        <td width="100"><p><? //echo $supplier_arr[$row[csf("supplier_id")]]; ?></p></td>
						<td width="70"><p><? echo $pay_mode[$row[csf("pay_mode")]]; ?></p></td>
						<td width="70"><p><? echo $source[$row[csf("source")]]; ?></p></td>
						<td width="70"><p><? echo $currency[$row[csf("currency_id")]]; ?></p></td>
						<td width="70"><p><? if($row[csf("delivery_date")]!="" && $row[csf("delivery_date")]!='0000-00-00') echo change_date_format($row[csf("delivery_date")]); ?></p></td>
						<td width="130"><p><? echo $item_category[$row[csf("item_category_id")]]; ?></p></td>
						<td width="100"><p><? echo $item_group_arr[$row[csf("item_group_id")]]; ?></p></td>
						<td width="80"><p><? echo $row[csf("sub_group_name")]; ?></p></td>
						<td width="80"><p><? echo $row[csf("item_code")]; ?></p></td>
						<td width="200"><p><? echo $row[csf("product_name_details")]; ?></p></td>
						<td width="60"><p><? echo $unit_of_measurement[$row[csf("unit_of_measure")]]; ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($row[csf("wo_qnty")],2); ?></p></td>

                        <td width="100" align="right"><p> <? echo $row[csf("rate")]; ?></p></td>

                        <td width="100" align="right"><p><? echo number_format($row[csf("wo_value")],2); ?></p></td>
						<td width="80" align="right"><a href="##" onClick="openmypage_popupIndependent(<? echo $row[csf("prod_id")]; ?>,'Receive Info','receive_popup_independent');" ><p><? echo number_format($independent_arr[$row[csf("prod_id")]]['recv_qnty'],0); ?></p></a></td>
						<td width="70" align="right"><p><? if($independent_arr[$row[csf("prod_id")]]['recv_qnty']>0) echo number_format($wo_rate_inde,2); else echo "0.00"; ?></p></td>
						
						<td  align="right" width="100"><p><? echo number_format($independent_arr[$row[csf("prod_id")]]['recv_amt'],2); ?></p></td>
						<td  align="right" ><p><? $wo_balance=$row[csf("wo_qnty")]-$independent_arr[$row[csf("prod_id")]]['recv_qnty']; echo number_format($wo_balance,2); $tot_recv_bal+=$wo_balance; ?></p></td>
						<td width="150"><p><? echo rtrim($independent_arr[$row[csf("prod_id")]]['comments'],', '); ?></p></td>
					</tr>
					<?
					$k++;
					$i++;
				}
				?>
				</tbody>
			</table>
            </div>
			<table width="2130" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_footer_2"  align="left">
				<tfoot>
					<tr>
                    	<th width="30">&nbsp;</th>
						<th width="60">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="130">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="200">&nbsp;</th>

						<th width="60">&nbsp;</th>
                        <th width="80"><? echo number_format($tot_wo_qty,2);?></th>
                        <th width="100">&nbsp;</th>
                        <th width="100"><? echo number_format($tot_wo_value,2);?></th>
						<th width="80"><? echo number_format($tot_rec,2);?></th>
						<th width="70">&nbsp;</th>
						<th width="100"><? echo number_format($tot_amount,2);?></th>
                        <th><? echo number_format($tot_recv_bal,2); ?></th>
                        <th width="150">&nbsp;</th>
					</tr>
				</tfoot>
			</table>
            </div> 
    <?
	
	foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename";
	exit();
}

if ($action=='receive_popup_independent')
{
	echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $receive_basis;die;
	$wo_num_arr=return_library_array("select id, wo_number from wo_non_order_info_mst","id","wo_number");
	//$req_num_arr=return_library_array("select id, requ_no from inv_purchase_requisition_mst","id","requ_no");
	$item_group_arr=return_library_array("select id, item_name from  lib_item_group","id","item_name");
	
	$sql_item_cat=sql_select("select id, item_category_id, item_description, item_group_id from product_details_master where id=$prod_id");
	
	$req_wo_recv_sql=sql_select("select a.id as recv_id, a.recv_number, a.receive_date, b.remarks, a.receive_basis, b.prod_id, sum(b.order_qnty) as recv_qnty
	from  inv_receive_master a, inv_transaction b 
	where a.id=b.mst_id and b.transaction_type=1 and b.prod_id=$prod_id and a.receive_basis=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
	group by a.id, a.recv_number, a.receive_date, b.remarks, a.receive_basis, b.prod_id");
?>	
<script>
	function print_window()
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		d.close();
	}
	
	function window_close()
	{
		parent.emailwindow.hide();
	}
	
</script>	
<fieldset style="width:630px; margin-left:10px" >
    <input type="button" value="Print" onClick="print_window()" style="width:100px; margin-left:250px;"  class="formbutton" /><br /><br />
    <div style="100%" id="report_container">
        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="630">
        	<tr>
            	<td width="90">Item Category :  </td>
                <td width="100"><? echo $item_category[$sql_item_cat[0][csf("item_category_id")]]; ?></td>
                <td width="80">Item Group :  </td>
                <td width="100"><? echo $item_group_arr[$sql_item_cat[0][csf("item_group_id")]]; ?></td>
                <td width="80">Item Name :  </td>
                <td ><? echo $sql_item_cat[0][csf("item_description")]; ?></td>		
            </tr>
        </table>
        <br />
        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="630">
            <thead>
                <tr>
                	<th width="40" >SI</th>
                    <th width="130">MRR No</th>
                    <th width="100">MRR Date</th>
                	<th width="80">MRR Qty.</th>
					<th >Remarks</th>
                </tr>
            </thead>
            <tbody>
            <?
			$i=1;
			foreach($req_wo_recv_sql as $row)
			{
				if ($i%2==0)
				$bgcolor="#E9F3FF";
				else
				$bgcolor="#FFFFFF";
				?>
                <tr bgcolor="<? echo $bgcolor; ?>">
                	<td><? echo $i; ?></td>
                    <td><p><? echo $row[csf("recv_number")]; ?> </p></td>
                    <td><p><? if($row[csf("receive_date")]!="" && $row[csf("receive_date")]!="0000-00-00") echo change_date_format($row[csf("receive_date")]); ?> </p></td>
                    <td align="right"><? echo number_format($row[csf("recv_qnty")],2); $grand_tot_in+=$row[csf("recv_qnty")]; ?></td>
                    <td><p><? echo $row[csf("remarks")]; ?> </p></td>
                </tr>
                <?
				$i++;
			}
			?>
            </tbody>
            <tfoot>
                <th >&nbsp;</th>
                <th>&nbsp;</th>
                <th >Total:</th>
                <th ><? echo number_format($grand_tot_in,2); ?></th>
                <th >&nbsp;</th>
                <th>&nbsp;</th>
            </tfoot>
        </table>
        </div>
    </fieldset>
    <?
}

if($action=="requisition_qnty")
{
	extract($_REQUEST);
	//echo $reqId.'-'.$company_id;
	echo load_html_head_contents("Requisition Report", "../../../", 1, 1,$unicode,'','');	
	//echo $date." ".$po_id;die;//company_id
	
	$sql="select id, requ_no, item_category_id, requisition_date, location_id, delivery_date, source, manual_req, department_id, section_id, store_name, pay_mode, cbo_currency, remarks,req_by from inv_purchase_requisition_mst where id=$reqId";
	$dataArray=sql_select($sql);
 	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$division_library=return_library_array( "select id, division_name from  lib_division", "id", "division_name"  );
	$department=return_library_array("select id,department_name from lib_department",'id','department_name');
	$section=return_library_array("select id,section_name from lib_section",'id','section_name');
	$country_arr=return_library_array( "select id,country_name from lib_country",'id','country_name');
	$supplier_array=return_library_array( "select id,supplier_name from lib_supplier",'id','supplier_name');
	$origin_lib=return_library_array( "select country_name,id from lib_country where is_deleted=0  and status_active=1 order by country_name", "id", "country_name"  );
	
	$pay_cash=$dataArray[0][csf('pay_mode')];
    ?>
		
	
	<table width="1000" class="rpt_tables">
    	<tr>
			<td>&nbsp; </td>
			<td colspan="5" align="center" style="font-size:22px"><strong><u><? echo $data[2] ?></u></strong></td>
		</tr>
		<tr>
			<td width="120" style="font-size:16px"><strong>Req. No:</strong></td>
			<td width="175px" style="font-size:16px"><strong><? echo $dataArray[0][csf('requ_no')];
			//$req[2].'-'.$req[3]; ?></strong></td>
			<td style="font-size:16px;" width="130"><strong>Req. Date:</strong></td><td style="font-size:16px;" width="175"><? if($dataArray[0][csf('requisition_date')]!="0000-00-00") echo change_date_format($dataArray[0][csf('requisition_date')]);?></td>
			<td width="125" style="font-size:16px"><strong>Source:</strong></td><td width="175px" style="font-size:16px"><? echo $source[$dataArray[0][csf('source')]]; ?></td>
		</tr>
		<tr>
			<td style="font-size:16px"><strong>Manual Req.:</strong></td> <td width="175px" style="font-size:16px"><? echo $dataArray[0][csf('manual_req')]; ?></td>
			<td style="font-size:16px"><strong>Department:</strong></td><td width="175px" style="font-size:16px"><? echo $department[$dataArray[0][csf('department_id')]]; ?></td>
			<td style="font-size:16px"><strong>Section:</strong></td><td width="175px" style="font-size:16px"><? echo $section[$dataArray[0][csf('section_id')]]; ?></td>
		</tr>
		<tr>
			 <td style="font-size:16px"><strong>Del. Date:</strong></td><td style="font-size:16px"><? if($dataArray[0][csf('delivery_date')]!="0000-00-00") echo change_date_format($dataArray[0][csf('delivery_date')]);?></td>
			<td style="font-size:16px"><strong>Store Name:</strong></td><td style="font-size:16px"><? echo $store_library[$dataArray[0][csf('store_name')]]; ?></td>
			<td style="font-size:16px"><strong>Pay Mode:</strong></td><td style="font-size:16px"><? echo $pay_mode[$dataArray[0][csf('pay_mode')]]; ?></td>
		</tr>
		<tr>
			<td style="font-size:16px"><strong>Location:</strong></td> <td style="font-size:16px"><? echo $location_arr[$dataArray[0][csf('location_id')]]; ?></td>
			<td style="font-size:16px"><strong>Currency:</strong></td> <td style="font-size:16px"><? echo $currency[$dataArray[0][csf('cbo_currency')]]; ?></td>
		   <td style="font-size:16px"><strong>Remarks:</strong></td> <td style="font-size:16px"><? echo $dataArray[0][csf('remarks')]; ?></td>
		</tr>
		<tr>
			<td style="font-size:16px"><strong>Req. By:</strong></td> <td style="font-size:16px"><? echo $dataArray[0][csf('req_by')]; ?></td>
			<td colspan="4"></td>
		</tr>
	</table>
	<br>
	<?
	//$margin='-133px;';
	
	//echo $th_span.'='.$cash_span.'='.$span.'='.$margin.'='.$widths.'='.$cash;
	?>
	
	<table cellspacing="0" width="980"  border="0" rules="all" class="rpt_table rpt_tables" >
		<thead bgcolor="#dddddd" align="center">
			<tr>
				<!-- <th width="980" align="center" ><strong>Item Details</strong></th> -->
				<th colspan="17" width="980" align="center" ><strong>ITEM DETAILS</strong></th>
			</tr>
			<tr>
				<th width="20">SL</th>
				<th width="80">Item Group</th>
				<th width="150">Item Des & Item Size</th>
				<th width="40">Req. For</th>
				<th width="35">UOM</th>
				<th width="40">Req. Qty.</th>
				<th width="40">Rate</th>
				<th width="40">Amount</th>
				<th width="50">Stock</th>
				<th width="50">Last Rec. Date</th>
				<th width="40">Last Rec. Qty.</th>
				<th width="40">Last Rate</th>
				<th width="55">Req. Value</th>
				<th width="60">Avg. Monthly issue</th>
				<th width="60">Avg. Monthly Rec.</th>
				<th width="90">Supplier</th>
				<th width="90">Remarks</th>                  
				
			</tr>
		</thead>
		<tbody>
		<?
		$item_name_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
		$receive_array=array();
		/*$rec_sql="select b.item_category, b.prod_id, max(b.transaction_date) as transaction_date, sum(b.cons_quantity) as rec_qty,avg(cons_rate) as cons_rate from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.entry_form=20 and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.item_category, b.prod_id, b.transaction_date";
		$rec_sql_result= sql_select($rec_sql);
		foreach($rec_sql_result as $row)
		{
			$receive_array[$row[csf('item_category')]][$row[csf('prod_id')]]['transaction_date']=$row[csf('transaction_date')];
			$receive_array[$row[csf('item_category')]][$row[csf('prod_id')]]['rec_qty']=$row[csf('rec_qty')];			
			//$receive_array[$row[csf('item_category')]][$row[csf('prod_id')]]['rec_qty']=$row[csf('rec_qty')];
		}*/
		 $rec_sql="select b.id,b.item_category, b.prod_id, b.transaction_date as transaction_date,b.supplier_id, b.cons_quantity as rec_qty,cons_rate as cons_rate from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.entry_form=20 and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by  b.prod_id,b.id";
		$rec_sql_result= sql_select($rec_sql);
		foreach($rec_sql_result as $row)
		{
			$receive_array[$row[csf('prod_id')]]['transaction_date']=$row[csf('transaction_date')];
			$receive_array[$row[csf('prod_id')]]['rec_qty']=$row[csf('rec_qty')];			
			$receive_array[$row[csf('prod_id')]]['rate']=$row[csf('cons_rate')];
			$receive_array[$row[csf('prod_id')]]['supplier_id']=$row[csf('supplier_id')];
		}

		if($db_type==2)
		{ 
			$cond_date="'".date('d-M-Y',strtotime(change_date_format($pc_date))-31536000)."' and '". date('d-M-Y',strtotime($pc_date))."'"; 
		}
		elseif($db_type==0) $cond_date="'".date('Y-m-d',strtotime(change_date_format($pc_date))-31536000)."' and '". date('Y-m-d',strtotime($pc_date))."'";

		$issue_sql=sql_select("select prod_id, min(transaction_date) as transaction_date, sum(cons_quantity) as isssue_qty from  inv_transaction where transaction_type=2 and is_deleted=0 and status_active=1 and transaction_date between $cond_date group by prod_id");
		$prev_issue_data=array();
		foreach($issue_sql as $row)
		{
			$prev_issue_data[$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
			$prev_issue_data[$row[csf("prod_id")]]["transaction_date"]=$row[csf("transaction_date")];
			$prev_issue_data[$row[csf("prod_id")]]["isssue_qty"]=$row[csf("isssue_qty")];
		}
		
		//var_dump($prev_issue_data);die;
		
		$receive_sql=sql_select("select prod_id, min(transaction_date) as transaction_date, sum(cons_quantity) as receive_qty from  inv_transaction where transaction_type=1 and is_deleted=0 and status_active=1 and transaction_date between $cond_date group by prod_id");
		$prev_receive_data=array();
		foreach($receive_sql as $row)
		{
			$prev_receive_data[$row[csf("prod_id")]]["prod_id"]=$row[csf("prod_id")];
			$prev_receive_data[$row[csf("prod_id")]]["transaction_date"]=$row[csf("transaction_date")];
			$prev_receive_data[$row[csf("prod_id")]]["receive_qty"]=$row[csf("receive_qty")];
		}
		
		$i=1; $k=1;
		// echo " select a.id,a.requisition_date,b.product_id,b.required_for,b.cons_uom,b.quantity,b.rate,b.amount,b.stock,b.product_id,b.remarks,c.item_account,c.item_category_id,c.item_description,c.item_size,c.item_group_id,c.unit_of_measure,c.current_stock,c.re_order_label from  inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b,product_details_master c where a.id=b.mst_id and b.product_id=c.id and a.id=$update_id and a.company_id=c.company_id and a.status_active=1 and b.product_id=c.id and a.is_deleted=0  ";
		 $sql= " select a.id,b.item_category,b.brand_name,b.origin,b.model, a.requisition_date, b.product_id, b.required_for, b.cons_uom, b.quantity, b.rate, b.amount, b.stock, b.product_id, b.remarks, c.item_account, c.item_category_id, c.item_description,c.sub_group_name,c.item_code, c.item_size, c.item_group_id, c.unit_of_measure, c.current_stock, c.re_order_label from  inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b, product_details_master c where a.id=b.mst_id and b.product_id=c.id and a.id=$reqId and a.company_id=c.company_id and a.status_active=1 and b.status_active=1 and b.product_id=c.id and a.is_deleted=0 and b.is_deleted=0 order by b.item_category,c.item_group_id";
		$sql_result=sql_select($sql);  
		//echo $sql;die;
		$item_category_array=array();
		$category_wise_data = array(); 
		/*foreach ($sql_result as $row) {
			$category_wise_data[$row[csf("item_category")]]['item_group_id']=$row[csf("item_group_id")];
			$category_wise_data[$row[csf("item_category")]]['item_size']=$row[csf("item_size")];
			$category_wise_data[$row[csf("item_category")]]['item_description']=$row[csf("item_description")];
			$category_wise_data[$row[csf("item_category")]]['required_for']=$row[csf("required_for")];
			$category_wise_data[$row[csf("item_category")]]['cons_uom']=$row[csf("cons_uom")];
			$category_wise_data[$row[csf("item_category")]]['quantity']=$row[csf("quantity")];
			$category_wise_data[$row[csf("item_category")]]['rate']=$row[csf("rate")];
			$category_wise_data[$row[csf("item_category")]]['amount']=$row[csf("amount")];
			$category_wise_data[$row[csf("item_category")]]['stock']=$row[csf("stock")];
			$category_wise_data[$row[csf("item_category")]]['rate']=$row[csf("rate")];
			$category_wise_data[$row[csf("item_category")]]['rate']=$row[csf("rate")];
		}*/
		foreach($sql_result as $row)
		{

			if (!in_array($row[csf("item_category")],$item_category_array) )
			{
				if($k!=1)
				{ 
					?>
					<tr bgcolor="#dddddd">
                        <td align="right" colspan="7"><strong>Sub Total : </strong></td>
                        <td align="right"><? echo number_format($total_amount,0,'',','); ?></td>
                        <td align="right"><? echo number_format($total_stock,0,'',','); ?></td>
                        <td align="right">&nbsp;</td>
                        <td align="right"><? echo number_format($total_last_rec_qty,0,'',','); ?></td>
                        <td align="right">&nbsp;</td>
                        <td align="right"><? echo number_format($total_reqsit_value,0,'',','); ?></td>
                        <td align="right"><? echo number_format($total_issue_avg,0,'',','); ?></td>
                        <td align="right"><? echo number_format($total_receive_avg,0,'',','); ?></td>
                        <td align="right">&nbsp;</td>
                        <td align="right">&nbsp;</td>     
                    </tr>
					<tr bgcolor="#dddddd">
						<td colspan="17" align="left" ><b>Category : <? echo $item_category[$row[csf("item_category")]]; ?></b></td>
					</tr>
					<?
					$total_amount=$total_stock=$total_last_rec_qty=$total_reqsit_value=$total_issue_avg=$total_receive_avg=0;
				}
				else
				{
					?>
					<tr bgcolor="#dddddd">
						<td colspan="17" align="left" ><b>Category : <? echo $item_category[$row[csf("item_category")]]; ?></b></td>
					</tr>
					<?
				}					
				$item_category_array[]=$row[csf('item_category')];            
				$k++;
			}

			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$quantity=$row[csf('quantity')];
			$quantity_sum += $quantity;
			$amount=$row[csf('amount')];
			//test 
			$sub_group_name=$row[csf('sub_group_name')];
			$amount_sum += $amount;
			$remarks=$row[csf('remarks')];
			$current_stock=$row[csf('stock')];
			$current_stock_sum += $current_stock;
			if($db_type==2)
			{
				$last_req_info=return_field_value( "a.requisition_date || '_' || b. quantity || '_' || b.rate as data", "inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b", " a.id=b.mst_id and b.product_id='".$row[csf('product_id')]."' and  a.requisition_date<'".change_date_format($row[csf('requisition_date')],'','',1)."' order by requisition_date desc", "data" );
			}
			if($db_type==0)
			{
				$last_req_info=return_field_value( "concat(requisition_date,'_',quantity,'_',rate) as data", "inv_purchase_requisition_mst a,inv_purchase_requisition_dtls b", " a.id=b.mst_id and b.product_id='".$row[csf('product_id')]."' and  requisition_date<'".$row[csf('requisition_date')]."' order by requisition_date desc", "data" );
			}
			$last_req_info=explode('_',$last_req_info);
			//print_r($dataaa);
			
			$item_account=explode('-',$row[csf('item_account')]);
			$item_code=$item_account[3];
			/*$last_rec_date=$receive_array[$row[csf('item_category_id')]][$row[csf('product_id')]]['transaction_date'];
			$last_rec_qty=$receive_array[$row[csf('item_category_id')]][$row[csf('product_id')]]['rec_qty'];*/
			$last_rec_date=$receive_array[$row[csf('product_id')]]['transaction_date'];
			$last_rec_qty=$receive_array[$row[csf('product_id')]]['rec_qty'];
			$last_rec_rate=$receive_array[$row[csf('product_id')]]['rate'];
			$last_rec_supp=$receive_array[$row[csf('product_id')]]['supplier_id'];
			
			
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="font-size:20px">
				<td align="center"><? echo $i; ?></td>
                <td><p style="font-size: 13px"><? echo $item_name_arr[$row[csf('item_group_id')]]; ?></td>
				<td><p style="font-size: 13px"> <? echo $row[csf("item_description")].', '.$row[csf("item_size")];?> </p></td>
				<td><p style="font-size: 13px">  <? echo $row[csf("required_for")]; ?></p></td>
				<td align="center"><p style="font-size: 13px">  <? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></p></td>
				<td align="right"><p style="font-size: 13px"><? echo $row[csf('quantity')]; ?></p></td>
				<td align="right"><? echo $row[csf('rate')]; ?></td>
				<td align="right"><? echo $row[csf('amount')]; ?></td>
				<td align="right"><p style="font-size: 13px"><? echo number_format($row[csf('stock')],2); ?></p></td>
				<td align="center"><p style="font-size: 13px"><? if(trim($last_rec_date)!="0000-00-00" && trim($last_rec_date)!="") echo change_date_format($last_rec_date); else echo "";?></p></td>
				<td align="right"><p style="font-size: 13px"><? echo number_format($last_rec_qty,0,'',','); ?></p></td>
				<td align="right"><p style="font-size: 13px"><? echo $last_rec_rate; ?></p></td>
				<td align="right"><p style="font-size: 13px"><? echo number_format($row[csf('quantity')]*$last_rec_rate,0,'',',') ?></p></td>
				<td align="right">
				<? 
				$min_issue_date=$prev_issue_data[$row[csf("product_id")]]["transaction_date"];
				if($min_issue_date=="")
				{
					echo number_format(0,2);
				}
				else
				{
					$month_issue_diff=datediff('m',$min_issue_date,$pc_date);
					$year_issue_total=$prev_issue_data[$row[csf("product_id")]]["isssue_qty"];
					$issue_avg=$year_issue_total/$month_issue_diff;
					echo number_format($issue_avg,2);
				}
				?>
				</td>
				<td align="right">
				<? 
				$min_receive_date=$prev_receive_data[$row[csf("product_id")]]["transaction_date"];
				if($min_receive_date=="")
				{
					echo number_format(0,2);
				}
				else
				{
					$month_receive_diff=datediff('m',$min_receive_date,$pc_date);
					$year_receive_total=$prev_receive_data[$row[csf("product_id")]]["receive_qty"];
					$receive_avg=$year_receive_total/$month_receive_diff;
					echo number_format($receive_avg,2);
				}
				?>
				</td>
				<td align="center"><p style="font-size: 13px"><? echo $supplier_array[$last_rec_supp];?></p></td>
				<td align="left"><p style="font-size: 13px"><? echo $remarks; ?></p></td>
			</tr>
			<?
			
			$total_amount+=$row[csf('amount')];
			$total_stock+=$row[csf('stock')];
			$total_last_rec_qty +=$last_rec_qty;
			$total_reqsit_value += $row[csf('quantity')]*$last_rec_rate;
			$total_issue_avg +=$issue_avg;
			$total_receive_avg +=$receive_avg;
			
			$Grand_tot_total_amount+=$row[csf('amount')];
			$Grand_tot_total_stock+=$row[csf('stock')];
			$Grand_tot_last_qnty +=$last_rec_qty;
			$Grand_tot_reqsit_value += $row[csf('quantity')]*$last_rec_rate;
			$Grand_tot_issue_avg +=$issue_avg;
			$Grand_tot_receive_avg +=$receive_avg;

			$i++;
		}
		?>
		</tbody>
		<tr bgcolor="#dddddd">
			<td align="right" colspan="7"><strong>Sub Total : </strong></td>
			<td align="right"><? echo number_format($total_amount,0,'',','); ?></td>
			<td align="right"><? echo number_format($total_stock,0,'',','); ?></td>
			<td align="right">&nbsp;</td>
			<td align="right"><? echo number_format($total_last_rec_qty,0,'',','); ?></td>
			<td align="right">&nbsp;</td>
			<td align="right"><? echo number_format($total_reqsit_value,0,'',','); ?></td>
			<td align="right"><? echo number_format($total_issue_avg,0,'',','); ?></td>
			<td align="right"><? echo number_format($total_receive_avg,0,'',','); ?></td>
			<td align="right">&nbsp;</td>
			<td align="right">&nbsp;</td>     
		</tr>

		<tr bgcolor="#dddddd">
			<td align="right" colspan="7"><strong>Grand Sub Total : </strong></td>
			<td align="right"><? echo number_format($Grand_tot_total_amount,0,'',','); ?></td>
			<td align="right"><? echo number_format($Grand_tot_total_stock,0,'',','); ?></td>
			<td align="right">&nbsp;</td>
			<td align="right"><? echo number_format($Grand_tot_last_qnty,0,'',','); ?></td>
			<td align="right">&nbsp;</td>
			<td align="right"><? echo number_format($Grand_tot_reqsit_value,0,'',','); ?></td>
			<td align="right"><? echo number_format($Grand_tot_issue_avg,0,'',','); ?></td>
			<td align="right"><? echo number_format($Grand_tot_receive_avg,0,'',','); ?></td>
			<td align="right">&nbsp;</td>
			<td align="right">&nbsp;</td>
		</tr>
		<tr bgcolor="#dddddd">
			<td align="right" colspan="7"><strong>Grand Total Amount in Word: </strong></td>
			<td align="left" colspan="10"><? echo number_to_words(number_format($Grand_tot_total_amount,0,'',','))." ".$currency[$dataArray[0][csf('cbo_currency')]]." only"; ?></td>
		</tr>
		
	</table>
	

	<?
    exit();	
}

if($action=='receive_popup')
{
	echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $receive_basis;die;
	$wo_num_arr=return_library_array("select id, wo_number from wo_non_order_info_mst","id","wo_number");
	$req_num_arr=return_library_array("select id, requ_no from inv_purchase_requisition_mst","id","requ_no");
	$pi_num_arr=return_library_array("select id, pi_number from com_pi_master_details","id","pi_number");
	$item_group_arr=return_library_array("select id, item_name from  lib_item_group","id","item_name");
	
	$sql_item_cat=sql_select("select id, item_category_id, item_description, item_group_id from product_details_master where id=$prod_id");
	$sql_req_wo_recv="select a.challan_no,a.id as recv_id, a.recv_number, a.receive_date, a.remarks, a.receive_basis, a.booking_id, b.prod_id, sum(b.order_qnty) as recv_qnty
	from  inv_receive_master a, inv_transaction b 
	where a.id=b.mst_id and b.transaction_type=1 and a.booking_id=$wo_id and b.prod_id=$prod_id and a.receive_basis=$receive_basis and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
	group by a.challan_no, a.id, a.recv_number, a.receive_date, a.remarks, a.receive_basis, a.booking_id, b.prod_id";
	$sql_req_wo_recv_res=sql_select($sql_req_wo_recv);
   ?>	
   <script>
	function print_window()
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		d.close();
	}
	
	function window_close()
	{
		parent.emailwindow.hide();
	}
	
</script>	
<fieldset style="width:630px; margin-left:10px" >
    <input type="button" value="Print" onClick="print_window()" style="width:100px; margin-left:250px;"  class="formbutton" /><br /><br />
    <div style="100%" id="report_container">
        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="630">
        	<tr>
            	<td width="90">Item Category :  </td>
                <td width="100"><? echo $item_category[$sql_item_cat[0][csf("item_category_id")]]; ?></td>
                <td width="80">Item Group :  </td>
                <td width="100"><? echo $item_group_arr[$sql_item_cat[0][csf("item_group_id")]]; ?></td>
                <td width="80">Item Name :  </td>
                <td ><? echo $sql_item_cat[0][csf("item_description")]; ?></td>		
            </tr>
        </table>
        <br />
        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="630">
            <thead>
                <tr>
                	<th width="35" >SI</th>
                    <th width="100">MRR No</th>
                    <th width="100">Challan No</th>
                    <th width="70">MRR Date</th>
                	<th width="80">MRR Qty.</th>
                    <? if ($receive_basis==1) { ?>
						<th width="100">PI No</th>
					<? } else if ($receive_basis==2) { ?>
						<th width="100">WO No</th>
					<? } else { ?>
						<th width="100">Reqsition No</th>
					<? } ?>
                    <th >Remarks</th>
                </tr>
            </thead>
            <tbody>
            <?
			$i=1;
			foreach($sql_req_wo_recv_res as $row)
			{
				if ($i%2==0)
				$bgcolor="#E9F3FF";
				else
				$bgcolor="#FFFFFF";
				?>
                <tr bgcolor="<? echo $bgcolor; ?>">
                	<td><? echo $i; ?></td>
                    <td><p><? echo $row[csf("recv_number")]; ?> </p></td>
                    <td><p><? echo $row[csf("CHALLAN_NO")]; ?> </p></td>
                    
                    <td align="center"><p><? if($row[csf("receive_date")]!="" && $row[csf("receive_date")]!="0000-00-00") echo change_date_format($row[csf("receive_date")]); ?> </p></td>
                    <td align="right"><? echo number_format($row[csf("recv_qnty")],2); $grand_tot_in+=$row[csf("recv_qnty")]; ?></td>
                    <td align="center"><p>
                    	<? 
                    	if ($receive_basis==1) echo $pi_num_arr[$row[csf("booking_id")]];
                    	else if($receive_basis==2) echo $wo_num_arr[$row[csf("booking_id")]];
                    	else echo $req_num_arr[$row[csf("booking_id")]];
                    	?> 
                    </p></td>
                    <td><p><? echo $row[csf("remarks")]; ?> </p></td>
                </tr>
                <?
				$i++;
			}
			?>
            </tbody>
            <tfoot>
                <th >&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th >Total:</th>
                <th ><? echo number_format($grand_tot_in,2); ?></th>
                <th >&nbsp;</th>
                <th>&nbsp;</th>
            </tfoot>
        </table>
        </div>
    </fieldset>
<?	
}

if($action=='receive_popup2')
{
	echo load_html_head_contents("Date Wise Production Report", "../../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $receive_basis;die;
	$wo_num_arr=return_library_array("select id, wo_number from wo_non_order_info_mst","id","wo_number");
	$req_num_arr=return_library_array("select id, requ_no from inv_purchase_requisition_mst","id","requ_no");
	$item_group_arr=return_library_array("select id, item_name from  lib_item_group","id","item_name");
	
	$sql_item_cat=sql_select("select id, item_category_id, item_description, item_group_id from product_details_master where id=$prod_id");
	
	$req_wo_recv_sql=sql_select("select a.id as recv_id, a.recv_number, a.receive_date, a.remarks, a.receive_basis, a.booking_id, b.prod_id, sum(b.order_qnty) as recv_qnty
	from  inv_receive_master a, inv_transaction b 
	where a.id=b.mst_id and b.transaction_type=1 and a.booking_id=$wo_id and b.prod_id=$prod_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
	group by a.id, a.recv_number, a.receive_date, a.remarks, a.receive_basis, a.booking_id, b.prod_id");
?>	
<script>
	function print_window()
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		d.close();
	}
	
	function window_close()
	{
		parent.emailwindow.hide();
	}
	
</script>	
<fieldset style="width:630px; margin-left:10px" >
    <input type="button" value="Print" onClick="print_window()" style="width:100px; margin-left:250px;"  class="formbutton" /><br /><br />
    <div style="100%" id="report_container">
        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="630">
        	<tr>
            	<td width="90">Item Category :  </td>
                <td width="100"><? echo $item_category[$sql_item_cat[0][csf("item_category_id")]]; ?></td>
                <td width="80">Item Group :  </td>
                <td width="100"><? echo $item_group_arr[$sql_item_cat[0][csf("item_group_id")]]; ?></td>
                <td width="80">Item Name :  </td>
                <td ><? echo $sql_item_cat[0][csf("item_description")]; ?></td>		
            </tr>
        </table>
        <br />
        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="630">
            <thead>
                <tr>
                	<th width="40" >SI</th>
                    <th width="130">MRR No</th>
                    <th width="100">MRR Date</th>
                	<th width="80">MRR Qty.</th>
                    <th width="120">Reqsition No</th>
					<th >Remarks</th>
                </tr>
            </thead>
            <tbody>
            <?
			$i=1;
			foreach($req_wo_recv_sql as $row)
			{
				if ($i%2==0)
				$bgcolor="#E9F3FF";
				else
				$bgcolor="#FFFFFF";
				?>
                <tr bgcolor="<? echo $bgcolor; ?>">
                	<td><? echo $i; ?></td>
                    <td><p><? echo $row[csf("recv_number")]; ?> </p></td>
                    <td><p><? if($row[csf("receive_date")]!="" && $row[csf("receive_date")]!="0000-00-00") echo change_date_format($row[csf("receive_date")]); ?> </p></td>
                    <td align="right"><? echo number_format($row[csf("recv_qnty")],2); $grand_tot_in+=$row[csf("recv_qnty")]; ?></td>
                    <td><p><? echo $req_num_arr[$row[csf("booking_id")]]; ?> </p></td>
                    <td><p><? echo $row[csf("remarks")]; ?> </p></td>
                </tr>
                <?
				$i++;
			}
			?>
            </tbody>
            <tfoot>
                <th >&nbsp;</th>
                <th>&nbsp;</th>
                <th >Total:</th>
                <th ><? echo number_format($grand_tot_in,2); ?></th>
                <th >&nbsp;</th>
                <th>&nbsp;</th>
            </tfoot>
        </table>
        </div>
    </fieldset>
<?	
}
disconnect($con);
?>


