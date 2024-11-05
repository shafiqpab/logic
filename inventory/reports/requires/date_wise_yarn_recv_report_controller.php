<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($db_type==0)
{
	$select_year="year";
	$year_con="";
}
else
{
	$select_year="to_char";
	$year_con=",'YYYY'";
}
//--------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------

//$yarn_requisition_arr=return_library_array( "select id, requisition_no from  ppl_yarn_requisition_entry",'id','requisition_no');
//$yarn_booking_arr=return_library_array( "select id, booking_no from  wo_booking_mst",'id','booking_no');
/*$composition_arr=array();
$construction_arr=array();
$yarn_count_array=array();
//$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent,c.yarn_count from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b,lib_yarn_count c where a.id=b.mst_id and c.id=b.count_id";
$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b,lib_yarn_count c where a.id=b.mst_id ";
$data_array=sql_select($sql_deter);
if(count($data_array)>0)
{
	foreach( $data_array as $row )
	{
		if(array_key_exists($row[csf('id')],$construction_arr))
		{
			$construction_arr[$row[csf('id')]]=$construction_arr[$row[csf('id')]];
		}
		else
		{
			$construction_arr[$row[csf('id')]]=$row[csf('construction')];
		}
		if(array_key_exists($row[csf('id')],$composition_arr))
		{
			$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]];
		}
		else
		{
			$composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
		}
	}
}*/


if($action=="load_drop_down_store")
{   
	extract($_REQUEST);
	echo create_drop_down( "cbo_store_name", 170, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id in ($choosenCompany) and a.status_active=1 and a.is_deleted=0 $cetegory_cond group by a.id, a.store_name order by a.store_name","id,store_name", 1, "--Select store--", 0, "" );
	die;
}

//report generated here--------------------//
if($action=="generate_report")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	//var dataString = "&cbo_item_cat="+cbo_item_cat+"&cbo_company_name="+cbo_company_name+"&txt_date_from="+txt_date_from+"&txt_date_to="+txt_date_to+"&cbo_dyed_type="+cbo_dyed_type+"&cbo_yarn_count="+cbo_yarn_count+"&rptType="+rptType+"&cbo_store_name="+cbo_store_name;
	$cbo_item_cat	     = str_replace("'","",$cbo_item_cat);
	$cbo_company_name	 = str_replace("'","",$cbo_company_name);
	$txt_date_from		 = str_replace("'","",$txt_date_from);
	$txt_date_to		 = str_replace("'","",$txt_date_to);
	$cbo_dyed_type		 = str_replace("'","",$cbo_dyed_type);
	$cbo_yarn_count		 = str_replace("'","",$cbo_yarn_count);
	$rptType			 = str_replace("'","",$rptType);
	$cbo_store_name		 = str_replace("'","",$cbo_store_name);
	$cbo_receive_purpose = str_replace("'","",$cbo_receive_purpose);
	$cbo_source			 = str_replace("'","",$cbo_source);
	//var_dump($cbo_receive_purpose);

	if ($cbo_receive_purpose != "" && $cbo_receive_purpose != 0)
	{
        $receive_purpose_cond = " and receive_purpose in (".$cbo_receive_purpose.")";
    }
	else
	{
		$receive_purpose_cond = " and receive_purpose in (2,5,6,7,12,15,16,38,43,46,50,51)";
	}

	if ($cbo_source != "" && $cbo_source != 0)
	{
        $source_cond = " and source in (".$cbo_source.")";
    }
	else
	{
		$source_cond = " and source in (1,2,3)";
	}

	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$store_library=return_library_array( "select id, store_name from lib_store_location",'id','store_name');
	//$buyer_name_arr=return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
	$buyer_short_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$color_arr=return_library_array( "select id, color_name from  lib_color",'id','color_name');
	$supplier_arr=return_library_array( "select id, supplier_name from  lib_supplier",'id','supplier_name');
	$user_name_arr=return_library_array( "select id, user_name from  user_passwd",'id','user_name');
	$brand_arr=return_library_array( "select id, brand_name from  LIB_BRAND",'id','brand_name');
	if($db_type==0)
	{
		if($txt_date_from!="" && $txt_date_to!="") $date_cond="and a.transaction_date between '".change_date_format($txt_date_from,"yyyy-mm-dd")."' and '".change_date_format($txt_date_to,"yyyy-mm-dd")."' "; else $date_cond="";
		$select_insert_date="DATE_FORMAT(a.insert_date,'%d-%m-%Y %H:%i:%S') as insert_date";
		$select_insert_time="DATE_FORMAT(a.insert_date,'%H:%i:%S') as insert_time";
	}
	else
	{
		if( $txt_date_from!="" && $txt_date_to!="" ) $date_cond="and a.transaction_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."' "; else $date_cond="";

		if( $txt_date_from!="" && $txt_date_to!="" ) $date_cond_2="and b.transaction_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."' "; else $date_cond_2="";

		if($txt_date_from!="" && $txt_date_to!="") {
			$date_cond_roll="and x.receive_date between '".date("j-M-Y",strtotime($txt_date_from))."  ' and '".date("j-M-Y",strtotime($txt_date_to))."' ";
			$date_cond_rolldata="and a.receive_date between '".date("j-M-Y",strtotime($txt_date_from))."  ' and '".date("j-M-Y",strtotime($txt_date_to))."' ";
			$date_cond_issue=" and a.issue_date between '".date("j-M-Y",strtotime($txt_date_from))."  ' and '".date("j-M-Y",strtotime($txt_date_to))."' ";
		} else {
			$date_cond="";
			$date_cond_roll="";
			$date_cond_rolldata="";
			$date_cond_issue="";
		}
		$select_insert_date=" to_char(a.insert_date,'DD-MM-YYYY HH24:MI:SS')  as insert_date";//HH24:MI:SS,32,34,35,36,37,38,39
		$select_insert_time=" to_char(a.insert_date,'HH24:MI:SS')  as insert_time";
	}

	$yarn_recv_buyer_arr=return_library_array( "select mst_id, buyer_id from inv_transaction where transaction_type in(1) and item_category=1",'mst_id','buyer_id');

	$sql_receive=sql_select("select id,entry_form,recv_number,knitting_source,knitting_company,receive_basis,booking_no,buyer_id,
	receive_purpose,supplier_id,booking_id,currency_id,exchange_rate,challan_no,lc_no,remarks,loan_party,source 
	from inv_receive_master
	where company_id in ($cbo_company_name) $receive_purpose_cond $source_cond and item_category=1 and entry_form=1 and is_deleted = 0 and status_active = 1");

	$con = connect();	
	$r_id1=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (1999)");
	if($r_id1)
	{
		oci_commit($con);
	}

	$receive_data_arr=array();
	$all_rcvId_arr=array();
	$rcvIdChk=array();
	foreach($sql_receive as $row)
	{
		$receive_data_arr[$row[csf("id")]]['rec_id']=$row[csf("id")];
		$receive_data_arr[$row[csf("id")]]['recv_number']=$row[csf("recv_number")];
		$receive_data_arr[$row[csf("id")]]['receive_basis']=$row[csf("receive_basis")];
		$receive_data_arr[$row[csf("id")]]['booking_id']=$row[csf("booking_id")];
		$receive_data_arr[$row[csf("id")]]['receive_purpose']=$row[csf("receive_purpose")];
		$receive_data_arr[$row[csf("id")]]['supplier_id']=$row[csf("supplier_id")];
		$receive_data_arr[$row[csf("id")]]['entry_form']=$row[csf("entry_form")];
		$receive_data_arr[$row[csf("id")]]['currency_id']=$row[csf("currency_id")];
		$receive_data_arr[$row[csf("id")]]['exchange_rate']=$row[csf("exchange_rate")];
		$receive_data_arr[$row[csf("id")]]['challan_no']=$row[csf("challan_no")];
		$receive_data_arr[$row[csf("id")]]['lc_no']=$row[csf("lc_no")];
		$receive_data_arr[$row[csf("id")]]['knitting_source']=$row[csf("knitting_source")];
		$receive_data_arr[$row[csf("id")]]['knitting_company']=$row[csf("knitting_company")];
		$receive_data_arr[$row[csf("id")]]['remarks']=$row[csf("remarks")];
		$receive_data_arr[$row[csf("id")]]['buyer']=$yarn_recv_buyer_arr[$row[csf("id")]];
		$receive_data_arr[$row[csf("id")]]['loan_party']=$row[csf("loan_party")];
		$receive_data_arr[$row[csf("id")]]['source']=$row[csf("source")];

		if($rcvIdChk[$row[csf('id')]] == "")
		{
			$rcvIdChk[$row[csf('id')]] = $row[csf('id')]; 
			$all_rcvId_arr[$row[csf('id')]] = $row[csf('id')];
		}
		
		

		if($row[csf("receive_basis")]==1)
		{
			$receive_data_arr[$row[csf("id")]]['pi_id']=$row[csf("booking_id")];
		}
		else
		{
			$receive_data_arr[$row[csf("id")]]['pi_id']=0;
		}
	}

	// $all_rcvId_cond=""; $rcvIdCond=""; 
    // $all_rcvId_arr = array_filter($all_rcvId_arr);
    // $all_rcvId_ids="'".implode("','",$all_rcvId_arr)."'";

    // if($db_type==2 && count($all_job_arr)>999)
    // {
    //     $all_rcvId_chunk_arr=array_chunk($all_rcvId_arr,999) ;
    //     foreach($all_rcvId_chunk_arr as $chunk_arr)
    //     {
    //         $chunk_arr_value="'".implode("','",$chunk_arr)."'";   
    //         $rcvIdCond.=" a.mst_id in($chunk_arr_value) or ";  
    //     }
        
    //     $all_rcvId_cond.=" and (".chop($rcvIdCond,'or ').")";
    // }
    // else
    // {
    //     $all_rcvId_cond=" and a.mst_id in($all_rcvId_ids)";    
    // }
	
	$sql_issue=sql_select("select id,entry_form,issue_number,issue_basis,booking_no,issue_purpose,supplier_id,booking_id,remarks,knit_dye_company as issue_to,knit_dye_source,challan_no,received_id, buyer_id,loan_party,pi_id from inv_issue_master where company_id in ($cbo_company_name) and item_category=1  and is_deleted = 0 and status_active = 1 and entry_form=8");
	$issue_data_arr=array();
	foreach($sql_issue as $row)
	{
		$issue_data_arr[$row[csf("id")]]['issue_id']=$row[csf("id")];
		$issue_data_arr[$row[csf("id")]]['issue_number']=$row[csf("issue_number")];
		$issue_data_arr[$row[csf("id")]]['issue_basis']=$row[csf("issue_basis")];
		$issue_data_arr[$row[csf("id")]]['booking_id']=$row[csf("booking_id")];
		$issue_data_arr[$row[csf("id")]]['booking_no']=$row[csf("booking_no")];
		$issue_data_arr[$row[csf("id")]]['issue_purpose']=$row[csf("issue_purpose")];
		$issue_data_arr[$row[csf("id")]]['supplier_id']=$row[csf("supplier_id")];
		$issue_data_arr[$row[csf("id")]]['entry_form']=$row[csf("entry_form")];
		$issue_data_arr[$row[csf("id")]]['knit_dye_source']=$row[csf("knit_dye_source")];
		$issue_data_arr[$row[csf("id")]]['issue_to']=$row[csf("issue_to")];
		$issue_data_arr[$row[csf("id")]]['remarks']=$row[csf("remarks")];
		$issue_data_arr[$row[csf("id")]]['challan_no']=$row[csf("challan_no")];
		$issue_data_arr[$row[csf("id")]]['received_id']=$row[csf("received_id")];
		$issue_data_arr[$row[csf("id")]]['buyer_id']=$row[csf("buyer_id")];
		$issue_data_arr[$row[csf("id")]]['loan_party']=$row[csf("loan_party")];
		$issue_data_arr[$row[csf("id")]]['pi_id']=$row[csf("pi_id")];
	}
	
	$sql_lc_pi = "select a.id,a.lc_number,b.pi_id from com_btb_lc_master_details a, com_btb_lc_pi b where a.id=b.com_btb_lc_master_details_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
	$result_lc_pi = sql_select($sql_lc_pi);
	foreach ($result_lc_pi as  $row) {
		$pi_lc_data_arr[$row[csf("pi_id")]]=$row[csf("lc_number")];
	}
	//var_dump($pi_lc_data_arr);

	/*$sql_issue=sql_select("select id,entry_form,issue_number,issue_basis,booking_no,issue_purpose,supplier_id,booking_id,remarks,knit_dye_company as issue_to,knit_dye_source,challan_no,received_id, buyer_id,loan_party from inv_issue_master where company_id=$cbo_company_name and item_category=1  and is_deleted = 0 and status_active = 1");
	$issue_data_arr=array();
	foreach($sql_issue as $row)
	{
		$issue_data_arr[$row[csf("id")]]['issue_id']=$row[csf("id")];
		$issue_data_arr[$row[csf("id")]]['issue_number']=$row[csf("issue_number")];
		$issue_data_arr[$row[csf("id")]]['issue_basis']=$row[csf("issue_basis")];
		$issue_data_arr[$row[csf("id")]]['booking_id']=$row[csf("booking_id")];
		$issue_data_arr[$row[csf("id")]]['booking_no']=$row[csf("booking_no")];
		$issue_data_arr[$row[csf("id")]]['issue_purpose']=$row[csf("issue_purpose")];
		$issue_data_arr[$row[csf("id")]]['supplier_id']=$row[csf("supplier_id")];
		$issue_data_arr[$row[csf("id")]]['entry_form']=$row[csf("entry_form")];
		$issue_data_arr[$row[csf("id")]]['knit_dye_source']=$row[csf("knit_dye_source")];
		$issue_data_arr[$row[csf("id")]]['issue_to']=$row[csf("issue_to")];
		$issue_data_arr[$row[csf("id")]]['remarks']=$row[csf("remarks")];
		$issue_data_arr[$row[csf("id")]]['challan_no']=$row[csf("challan_no")];
		$issue_data_arr[$row[csf("id")]]['received_id']=$row[csf("received_id")];
		$issue_data_arr[$row[csf("id")]]['buyer_id']=$row[csf("buyer_id")];
		$issue_data_arr[$row[csf("id")]]['loan_party']=$row[csf("loan_party")];

	}*/



	/*$transfer_sql=sql_select("select a.id, a.transfer_system_id, a.challan_no, a.transfer_date from inv_item_transfer_mst a, inv_transaction b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.item_category=1");
	foreach($transfer_sql as $row)
	{
		$transfer_num_arr[$row[csf("id")]]["id"]=$row[csf("id")];
		$transfer_num_arr[$row[csf("id")]]["transfer_system_id"]=$row[csf("transfer_system_id")];
		$transfer_num_arr[$row[csf("id")]]["challan_no"]=$row[csf("challan_no")];
		$transfer_num_arr[$row[csf("id")]]["transfer_date"]=$row[csf("transfer_date")];
	}*/

	$yarn_pi_num_arr=return_library_array( "select id, pi_number from com_pi_master_details where item_category_id=1",'id','pi_number');
	//var_dump($yarn_pi_num_arr);//die;
	$yarn_work_order_arr=return_library_array( "select id, wo_number from  wo_non_order_info_mst where item_category in(0,1)",'id','wo_number');
	$yarn_dyeing_wo_arr=return_library_array( "select id, ydw_no from wo_yarn_dyeing_mst where entry_form in(42,94,135,41,114)",'id','ydw_no');
	//$yarn_dyeing_pay_mood_arr=return_library_array( "select id, pay_mode from wo_yarn_dyeing_mst where entry_form in(42,94,135,41,114)",'id','pay_mode');
	//$btb_lc_arr=return_library_array( "select id, lc_number from com_btb_lc_master_details where item_category_id=1",'id','lc_number');

	

	$yarn_count_cond="";
	if($cbo_yarn_count!=0) $yarn_count_cond=" and b.yarn_count_id=$cbo_yarn_count";
	if($cbo_store_name>0) $yarn_count_cond.=" and a.store_id=$cbo_store_name";
	if($cbo_dyed_type>0) $yarn_count_cond.=" and b.dyed_type=$cbo_dyed_type";
	//echo $date_cond;die;
	if(!empty($all_rcvId_arr))
	{	
		fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 1999, 1,$all_rcvId_arr, $empty_arr);
		//die;
		$sql="SELECT a.company_id, b.id as prod_id, a.id as trans_id, a.receive_basis, a.transaction_type, a.mst_id as rec_issue_id, a.transaction_date, a.buyer_id, case when a.transaction_type in(1) then a.cons_quantity else 0 end as receive_qty , case when a.transaction_type in(3) then a.cons_quantity else 0 end as receive_ret_qty, a.cons_uom, b.yarn_comp_type1st as yarn_comp_type1st, b.yarn_comp_percent1st as yarn_comp_percent1st, b.yarn_comp_type2nd as yarn_comp_type2nd, b.yarn_comp_percent2nd as yarn_comp_percent2nd, b.lot, b.brand, b.supplier_id, b.yarn_count_id, b.yarn_type, b.color, a.cons_rate, a.cons_amount, a.inserted_by, $select_insert_date, $select_insert_time, a.order_qnty, a.order_rate, a.store_id,a.pi_wo_batch_no 
		from inv_transaction a, product_details_master b, GBL_TEMP_ENGINE c 
		where a.prod_id=b.id and a.item_category=1 and a.company_id in ($cbo_company_name) and a.transaction_type in(1,3) and a.cons_quantity>0 and a.status_active=1 and a.is_deleted=0   $date_cond $yarn_count_cond and a.mst_id=c.ref_val and c.user_id=$user_id and c.entry_form=1999 order by a.transaction_date, a.id";
	}
	// echo $sql;die;
	$table_width=2820;
	$div_width="2840px";
	
	$sql_result=sql_select($sql);


	$con = connect();	
	$r_id111=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (1999)");
	if($r_id111)
	{
		oci_commit($con);
	}
	
	ob_start();
	?>

	<div style="width:<? echo $div_width; ?>">
	<fieldset style="width:<? echo $div_width; ?>;">
			<table width="<? echo $table_width; ?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="" align="left">
					<tr class="form_caption" style="border:none;">
						<td colspan="14" align="center" style="border:none;font-size:16px; font-weight:bold" >Date Wise Yarn Purchase Report </td>
					</tr>
					<tr style="border:none;">
					<?
						// /echo $cbo_company_name;
						$all_company_arr = explode(",", $cbo_company_name);
						$all_company = "";
						foreach($all_company_arr as $comp)
						{
							$all_company .= $company_arr[$comp].", ";
						}
						$all_company = rtrim($all_company,', ');
					?>
							<td colspan="14" align="center" style="border:none; font-size:14px;">
								Company Name : <? echo $all_company; //echo $company_arr[str_replace("'","",$cbo_company_name)]; ?>
							</td>
					</tr>
			   </table>
			   <br />
			<table width="<? echo $table_width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" align="left">
				<thead>
					<tr>
						<th width="30" >SL</th>
						<th width="120" >Company</th>
						<th width="60" >Prod. Id</th>
						<th width="120" >Store Names</th>
						<th width="70" >Trans. Date</th>
						<th width="130" >Trans. Ref.</th>
						<th width="80">Supplier</th>
						<th width="80" >Challan No</th>
						<th width="100">Yarn Lot</th>
						<th width="100">Yarn Count</th>
						<th width="100">Brand</th>
						<th width="150">Composition</th>
						<th width="90">Yarn Type</th>
						<th width="100">Color</th>
						<th width="100">Basis</th>
						<th width="100">WO/PI NO.</th>
						<th width="100">BTB LC NO.</th>
						<th width="120">Purpose</th>
						<th width="100">Source</th>
						<th width="80">Currency</th>
						<th width="80">Exchange Rate</th>
						<th width="80">Actual Rate</th>
						<th width="80">Receive Qty</th>
						<th width="80">Receive Return Qty</th>
						<th width="80">Actual Amt</th>
						<th width="80">Rate(TK)</th>
						<th width="100">Amount(TK)</th>
						<th width="110">User</th>
						<th width="110">Insert Date</th>
						<th>Remarks</th>
					</tr>
				</thead>
		   </table>
		  <div style="width:<? echo $div_width; ?>; overflow-y: scroll; max-height:320px;" id="scroll_body">
			<table width="<? echo $table_width; ?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left">
				<tbody>
				<?
				$book_sql=sql_select("select booking_no, buyer_id from wo_booking_mst where booking_type=1 and status_active=1 and is_deleted=0
				union all
				select booking_no, buyer_id from wo_non_ord_samp_booking_mst where booking_type=4 and status_active=1 and is_deleted=0");
				foreach($book_sql as $row)
				{
					$bookingArr[$row[csf("booking_no")]]=$row[csf("buyer_id")];
				}
				//$bookingArr = return_library_array("select booking_no,buyer_id from wo_booking_mst where status_active=1 and is_deleted=0","booking_no","buyer_id");
				
				$yarnDyeingArr=sql_select("select a.id,a.pay_mode,a.ydw_no,b.job_no as sales_order_no from wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls b where  a.id=b.mst_id and a.status_active=1 and a.is_deleted=0");

				foreach($yarnDyeingArr as $row)
				{
					$YarnfsoArr[$row[csf("ydw_no")]]=$row[csf("sales_order_no")];
					$YarnDyingSupplierArr[$row[csf("id")]]=$row[csf("pay_mode")];
				}

				/*$sql_sales= "select a.id,a.sales_booking_no,a.job_no,a.within_group,a.buyer_id from  fabric_sales_order_mst a where  a.status_active=1 and a.is_deleted=0 ";
				$result_sales=sql_select($sql_sales);
				foreach($result_sales as $row)
				{
					$sales_numArr[$row[csf('sales_booking_no')]]['within_group']=$row[csf('within_group')];
					$sales_numArr[$row[csf('sales_booking_no')]]['job_no']=$row[csf('job_no')];
					$sales_numArr[$row[csf('sales_booking_no')]]['buyer_id']=$row[csf('buyer_id')];
					$sales_numArr2[$row[csf('job_no')]]['sales_booking_no']=$row[csf('sales_booking_no')];
				}*/

				$sql_requ= "select a.dtls_id,a.booking_no,b.requisition_no from  ppl_planning_entry_plan_dtls a,ppl_yarn_requisition_entry b where  b.knit_id=a.dtls_id and  a.status_active=1 and a.is_deleted=0 and a.is_sales=1 ";
				$result_requ=sql_select($sql_requ);
				foreach($result_requ as $row)
				{
					$requisition_numArr[$row[csf('requisition_no')]]['booking_no']=$row[csf('booking_no')];
					$requisition_numArr[$row[csf('requisition_no')]]['dtls_id']=$row[csf('dtls_id')];
					//$sales_numArr[$row[csf('requisition_no')]]['buyer_id']=$row[csf('buyer_id')];
				}

				$yarn_count_arr=return_library_array( "select id, yarn_count from  lib_yarn_count",'id','yarn_count');
				$i=1;$total_receive="";$total_issue="";
				//var_dump($sql_result);
				foreach($sql_result as $val)
				{
					if ($i%2==0)
					$bgcolor="#E9F3FF";
					else
					$bgcolor="#FFFFFF";
					$receive_basis=$val[csf("receive_basis")];
					//$buyer_job_no=$val[csf('buyer_job_no')];
					//$booking_no=$val[csf('booking_no')];
					$buyer_name=$buyer_id="";
					if($rptType==3 || $rptType==2)
					{
						if($receive_basis==3) //Requisition
						{
							$requisition_no=$receive_data_arr[$val[csf('rec_issue_id')]]['booking_id'];
							$booking_no=$requisition_numArr[$requisition_no]['booking_no'];
							$sales_booking_no=$sales_numArr[$booking_no]['job_no'];
							$within_group=$sales_numArr[$booking_no]['within_group'];


							if($within_group==1) //Yes
							{
								$buyer_id=$bookingArr[$booking_no];
								//echo $buyer_id."=".$within_group."=".$booking_no.test;die;
								if($buyer_id<1) $buyer_id=$issue_data_arr[$val[csf('rec_issue_id')]]['buyer_id'];
								$buyer_name=$buyer_short_arr[$buyer_id];
							}
							else
							{
								$buyer_id=$sales_numArr[$booking_no]['buyer_id'];
								if($buyer_id<1) $buyer_id=$issue_data_arr[$val[csf('rec_issue_id')]]['buyer_id'];
								$buyer_name=$buyer_short_arr[$buyer_id];
							}
						}
						else if($receive_basis==1 && $val[csf("transaction_type")]==2 ) //Booking///Issue
						{
							//$requisition_no=$receive_data_arr[$val[csf('rec_issue_id')]]['booking_id'];
							//$booking_no=$val[csf('booking_no')];
							$booking_no=$issue_data_arr[$val[csf('rec_issue_id')]]['booking_no'];
							$sales_booking_no=$sales_numArr[$booking_no]['job_no'];
							$within_group=$sales_numArr[$booking_no]['within_group'];
							$receive_purpose=$issue_data_arr[$val[csf('rec_issue_id')]]['issue_purpose'];
							if($receive_purpose==2) //Issue//Yarn Dyeing
							{
								$sales_booking_no=$YarnfsoArr[$booking_no];
								$fso_booking_no=$sales_numArr2[$sales_booking_no]['sales_booking_no'];
								$within_group=$sales_numArr[$fso_booking_no]['within_group'];
								if($within_group==1) //Yes
								{
									$buyer_id=$bookingArr[$fso_booking_no];
									if($buyer_id<1) $buyer_id=$issue_data_arr[$val[csf('rec_issue_id')]]['buyer_id'];
									$buyer_name=$buyer_short_arr[$buyer_id];
								}
								else
								{
									$buyer_id=$sales_numArr[$fso_booking_no]['buyer_id'];
									if($buyer_id<1) $buyer_id=$issue_data_arr[$val[csf('rec_issue_id')]]['buyer_id'];
									$buyer_name=$buyer_short_arr[$buyer_id];
								}
							}
							else if($within_group==1 && $receive_purpose!=2) //Yes
							{
								$buyer_id=$bookingArr[$booking_no];
								if($buyer_id<1) $buyer_id=$issue_data_arr[$val[csf('rec_issue_id')]]['buyer_id'];
								$buyer_name=$buyer_short_arr[$buyer_id];
							}
							else
							{
								$buyer_name=$buyer_short_arr[$buyer_id];
							}
						}
						else if($receive_basis==1 && $val[csf("transaction_type")]==1 ) //PI///Recv
						{
							$buyer_id=$val[csf('buyer_id')];
							$buyer_name=$buyer_short_arr[$buyer_id];
						}
						else if($val[csf("transaction_type")]==3 ) //Recv Ret
						{

							$received_ids=$issue_data_arr[$val[csf("rec_issue_id")]]['received_id'];
							$buyer_id=$receive_data_arr[$received_ids]['buyer'];
							$buyer_name=$buyer_short_arr[$buyer_id];
							//echo $buyer_id.'ddd';
						}
						else
						{
							if($val[csf("transaction_type")]==1 || $val[csf("transaction_type")]==4)
							{
								$buyer_id=$val[csf('buyer_id')];
								$buyer_name=$buyer_short_arr[$buyer_id];
							}
							else
							{
								$receive_purpose=$issue_data_arr[$val[csf('rec_issue_id')]]['issue_purpose'];
								$booking_no=$val[csf('booking_no')];
								if($receive_purpose==2) //Issue//Yarn Dyeing
								{
									$buyer_id=$sales_numArr[$booking_no]['buyer_id'];
									if($buyer_id<1) $buyer_id=$issue_data_arr[$val[csf('rec_issue_id')]]['buyer_id'];
								}
								else
								{
									$buyer_id=$bookingArr[$booking_no];
									if($buyer_id<1) $buyer_id=$issue_data_arr[$val[csf('rec_issue_id')]]['buyer_id'];
								}
								$buyer_name=$buyer_short_arr[$buyer_id];
							}
							$sales_booking_no='';
						}
					}
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
						<td width="30"><p><? echo $i; ?>&nbsp;</p></td>
						<td width="120" align="center"><p><? echo $company_arr[$val['COMPANY_ID']]; ?>&nbsp;</p></td>
						<td width="60" align="center"><p><? echo $val[csf("prod_id")]; ?>&nbsp;</p></td>
						<td width="120"><p><? echo $store_library[$val[csf("store_id")]]; ?>&nbsp;</p></td>
						<td width="70" align="center"><p><? if($val[csf("transaction_date")]!="0000-00-00") echo change_date_format($val[csf("transaction_date")]); else echo ""; ?>&nbsp;</p></td>
						<td width="130"  align="center" title = "mst_id: <? echo $val[csf('rec_issue_id')];?>"><p>
						<?
						if($val[csf("transaction_type")]==1 )
						{
							echo $receive_data_arr[$val[csf('rec_issue_id')]]["recv_number"];
							$remarks=$receive_data_arr[$val[csf('rec_issue_id')]]["remarks"];
						}
						else
						{
							echo $issue_data_arr[$val[csf('rec_issue_id')]]['issue_number'];
							$remarks=$issue_data_arr[$val[csf('rec_issue_id')]]['remarks'];
						}
						?>&nbsp;
						</p></td>
						<td width="80"><p><? echo $supplier_arr[$val[csf("supplier_id")]]; ?>&nbsp;</p></td>
						<td width="80"><p>
						<?
						if($val[csf("transaction_type")]==1 )
						{
							echo $receive_data_arr[$val[csf('rec_issue_id')]]["challan_no"];
						}
						else
						{
							echo $issue_data_arr[$val[csf('rec_issue_id')]]['challan_no'];
						}
						
						?>&nbsp;</p></td>
						<td width="100" align="center" style="word-break:break-all;"><p><? echo $val[csf("lot")]; ?>&nbsp;</p></td>
						<td width="100" align="center"><p><? echo $yarn_count_arr[$val[csf("yarn_count_id")]]; ?>&nbsp;</p></td>
						<td width="100" align="center"><p><? echo $brand_arr[$val[csf("brand")]]; ?>&nbsp;</p></td>
						<td width="150"><p><?
						if($val[csf("yarn_comp_percent1st")]!=0) {$parcent1st=$val[csf("yarn_comp_percent1st")]."%";} else {$parcent1st="";}
						if($val[csf("yarn_comp_percent2nd")]!=0 ){ $parcent2nd=$val[csf("yarn_comp_percent2nd")]."%";} else {$parcent2nd="";}
						 echo $composition[$val[csf("yarn_comp_type1st")]].' '.$parcent1st.' '.$composition[$val[csf("yarn_comp_type2nd")]].' '.$parcent2nd;
						 ?>&nbsp;</p></td>
						<td width="90"><p><? echo $yarn_type[$val[csf("yarn_type")]]; ?>&nbsp;</p></td>
						<td width="100"><p><? echo $color_arr[$val[csf("color")]]; ?>&nbsp;</p></td>
						<td width="100"><p>
						<?
                        if($val[csf("transaction_type")]==1 )
                        {
                             echo $receive_basis_arr[$receive_data_arr[$val[csf("rec_issue_id")]]['receive_basis']];
                        }
						else
						{
							echo $receive_basis_arr[$issue_data_arr[$val[csf("rec_issue_id")]]['issue_basis']];
						}
                        ?>&nbsp;</p></td>
                        <td width="100"><p>
                        <?
                        $knitting_soucre=$receive_data_arr[$val[csf("rec_issue_id")]]['knitting_source'];
                        $knitting_company=$receive_data_arr[$val[csf("rec_issue_id")]]['knitting_company'];


                         if($receive_data_arr[$val[csf("rec_issue_id")]]['entry_form']==9)
                         {
                             if($knitting_soucre==1)
                             {
                                $supplier_name=$company_arr[$knitting_company];
                             }
                             else
                             {
                                $supplier_name=$supplier_arr[$knitting_company];
                             }

                             echo "Issue Return".'('.$supplier_name.')';
                         }
                         else
                         {
                             if($val[csf("receive_basis")]==1) echo $yarn_pi_num_arr[$receive_data_arr[$val[csf("rec_issue_id")]]['booking_id']];
                             if($val[csf("receive_basis")]==2){
                                 if($receive_data_arr[$val[csf('rec_issue_id')]]['receive_purpose']==2 || $receive_data_arr[$val[csf('rec_issue_id')]]['receive_purpose'] ==15 || $receive_data_arr[$val[csf('rec_issue_id')]]['receive_purpose'] ==38 || $receive_data_arr[$val[csf('rec_issue_id')]]['receive_purpose'] ==50 || $receive_data_arr[$val[csf('rec_issue_id')]]['receive_purpose'] ==51){
                                     echo $yarn_dyeing_wo_arr[$receive_data_arr[$val[csf("rec_issue_id")]]['booking_id']];
                                 }else{
                                    echo $yarn_work_order_arr[$receive_data_arr[$val[csf("rec_issue_id")]]['booking_id']];
                                 }
                             }
                         }
                        ?>&nbsp;</p></td>
                        <td width="100"><p>
						<? 
						if($val[csf("transaction_type")]==1 )
                        {
							echo  $pi_lc_data_arr[$receive_data_arr[$val[csf("rec_issue_id")]]['pi_id']];
						}
						else
						{
							echo  $pi_lc_data_arr[$issue_data_arr[$val[csf("rec_issue_id")]]['pi_id']];
						}
						 
						?>&nbsp;</p></td>
                        <td width="120" style="word-break:break-all"><p><? echo $yarn_issue_purpose[$receive_data_arr[$val[csf("rec_issue_id")]]['receive_purpose']]; ?>&nbsp;</p></td>
                        <td width="100" style="word-break:break-all"><p><? echo $source[$receive_data_arr[$val[csf("rec_issue_id")]]['source']]; ?>&nbsp;</p></td>
                        <td width="80" align="center"><p>
                        <?
						if($val[csf("transaction_type")]==1 )
                        {
							echo $currency[$receive_data_arr[$val[csf('rec_issue_id')]]["currency_id"]];
						}
						else
						{
							echo $currency[1];
						}
                        
                        ?>&nbsp;</p></td>
                        <td width="80" align="right"><p>
                        <?
                         echo number_format($receive_data_arr[$val[csf('rec_issue_id')]]["exchange_rate"],2);
                    	?></p></td>
						<td width="80" align="right"><p>
						<?
						echo number_format($val[csf("order_rate")],4);
						?></p></td>
						<td width="80" align="right"><p><? echo number_format($val[csf("receive_qty")],2,".",""); $total_receive +=$val[csf("receive_qty")]; ?></p></td>
						<td width="80" align="right"><? echo number_format($val[csf("receive_ret_qty")],2,".",""); $tot_receive_ret_qty +=$val[csf("receive_ret_qty")]; ?></td>
						<td width="80" align="right"><p>
						<?
						$order_amt=$val[csf("order_qnty")]*$val[csf("order_rate")];
						echo number_format($order_amt,4); $total_order_amt+=$order_amt; $order_amt=0;
						?></p></td>
						<td align="right"  width="80"><p><? echo number_format($val[csf("cons_rate")],2,".",""); ?></p></td>
						<td align="right" width="100" style="padding-right:3px;"><p><? echo number_format($val[csf("cons_amount")],2,".",""); $total_amount +=$val[csf("cons_amount")]; ?></p></td>
						<td width="107"><p><? echo $user_name_arr[$val[csf("inserted_by")]]; ?>&nbsp;</p></td>
						<td width="110"><p><? echo change_date_format($val[csf("insert_date")])." ".$val[csf("insert_time")]; ?>&nbsp;</p></td>
						<td><p><? echo $remarks; ?>&nbsp;</p></td>
					</tr>
					<?
					$i++;
				}
				?>
				</tbody>
			</table>
			<table width="<?  echo $table_width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="report_table_footer" align="left">
				<tfoot>
					<th width="30">&nbsp;</th>
					<th width="120">&nbsp;</th>
					<th width="60">&nbsp;</th>
					<th width="120">&nbsp;</th>
					<th width="70">&nbsp;</th>
					<th width="130">&nbsp;</th>
					<th width="80">&nbsp;</th>
					<th width="80">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="150">&nbsp;</th>
					<th width="90">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="120">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="80" >&nbsp;</th>
					<th width="80" >&nbsp;</th>
					<th width="80" >Total:</th>
					<th width="80" id="value_total_receive"><? echo number_format($total_receive,2); ?></th>
					<th width="80" id="value_tot_receive_ret_qty"><? echo number_format($tot_receive_ret_qty,2); ?></th>
					<th width="80" id="value_total_order_amt"><? echo number_format($total_order_amt,2); ?></th>
					<th width="80">&nbsp;</th>
					<th width="100" id="value_total_amount" style="padding-right:3px;"><? echo number_format($total_amount,2); ?></th>
					<th width="107">&nbsp;</th>
					<th width="110">&nbsp;</th>
					<th>&nbsp;</th>
				</tfoot>
			</table>
		 </div>
	</fieldset>
	</div>
	<?
	foreach (glob("*.xls") as $filename) {
	if( @filemtime($filename) < (time()-$seconds_old) )
	@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$html**$filename**$cbo_item_cat**$rptType";
	disconnect($con);
	exit();

}

?>
