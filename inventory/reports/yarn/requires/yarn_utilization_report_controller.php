<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );


if ($action=="load_drop_down_supplier")
{
	echo create_drop_down( "cbo_suppler_name", 150, "select a.id, a.supplier_name from lib_supplier a, lib_supplier_tag_company b  where a.id=b.supplier_id and b.tag_company='$data' and a.status_active=1 and a.is_deleted=0 order by a.supplier_name","id,supplier_name", 1, "-- Select Supplier --", 0, "date_range_herder_color(this.value);" );
	exit();
}

if($action=="btbLc_popup")
{
  	echo load_html_head_contents("BTB LC Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);  
?>
<script>
	function js_set_value(str)
	{
		var splitData = str.split("_");		 
		$("#btbLc_id").val(splitData[0]); 
		$("#btbLc_no").val(splitData[1]); 
		parent.emailwindow.hide();
	}
	
</script>
</head>
<body>
<div align="center" style="width:100%; margin-top:5px" >
<form name="searchlcfrm_1" id="searchlcfrm_1" autocomplete="off">
	<table width="600" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
            <thead>
                <tr>                	 
                    <th>Supplier</th>
                    <th id="search_by_td_up">Enter BTB LC Number</th>
                    <th>
                    	<input type="reset" id="res" value="Reset" style="width:100px" class="formbutton" onClick="reset_form('searchlcfrm_1','search_div','','','','');" />
                        <input type="hidden" id="btbLc_id" value="" />
                        <input type="hidden" id="btbLc_no" value="" />
                    </th>           
                </tr>
            </thead>
            <tbody>
                <tr align="center">
                    <td>
                        <?  
							echo create_drop_down( "cbo_supplier_id", 160,"select DISTINCT(c.id),c.supplier_name from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$companyID' and b.party_type=2",'id,supplier_name', 1, '-- All Supplier --',$supplierID,'',0);
                        ?>
                    </td>
                    <td align="center" id="search_by_td">				
                        <input type="text" style="width:230px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
                    </td> 
                     <td align="center">
                        <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_supplier_id').value+'_'+document.getElementById('txt_search_common').value+'_'+<? echo $companyID; ?>, 'create_lc_search_list_view', 'search_div', 'yarn_utilization_report_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />				
                    </td>
           	 	</tr> 
            </tbody>         
        </table>    
        <div align="center" style="margin-top:10px" id="search_div"> </div> 
        </form>
   </div>
</body>           
<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

//----------------------------------------------------------------------------------------------------------
if($action=="create_lc_search_list_view")
{
	$ex_data = explode("_",$data);
	
	if($ex_data[0]==0) $cbo_supplier = "%%"; else $cbo_supplier = $ex_data[0];
	$txt_search_common = trim($ex_data[1]);
	$company = $ex_data[2];
	
	$sql= "select id, lc_number, supplier_id, importer_id, lc_date, last_shipment_date, lc_value from com_btb_lc_master_details where importer_id=$company and supplier_id like '$cbo_supplier' and lc_number like '%".$txt_search_common."%' and is_deleted=0 and status_active=1"; //and item_category_id=1

	//echo $sql;
	
	
	$arr=array(1=>$company_arr,2=>$supplier_arr);
	echo create_list_view("list_view", "LC No, Importer, Supplier Name, LC Date, Last Shipment Date, LC Value","130,110,130,90,130","780","260",0, $sql , "js_set_value", "id,lc_number", "", 1, "0,importer_id,supplier_id,0,0,0,0", $arr, "lc_number,importer_id,supplier_id,lc_date,last_shipment_date,lc_value", "",'','0,0,0,3,3,2') ;	
	exit();
	
}



if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_suppler_name=str_replace("'","",$cbo_suppler_name);
	$btbLc_id=str_replace("'","",$btbLc_id);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$cbo_receiving_status=str_replace("'","",$cbo_receiving_status);

	require_once('../../../../includes/class3/class.conditions.php');
	require_once('../../../../includes/class3/class.reports.php');
	require_once('../../../../includes/class3/class.yarns.php');	
	
	
	
	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer",'id','buyer_name');
	$count_arr=return_library_array("select id,yarn_count from lib_yarn_count where is_deleted = 0 AND status_active = 1 ORDER BY yarn_count ASC", "id", "yarn_count");
	$currency_conversion_rate=return_field_value("conversion_rate","currency_conversion_rate","status_active=1 order by con_date desc","conversion_rate");
	

	$sql_cond="";
	$btbLc_id_str=str_replace("'","",$btbLc_id);
	if($cbo_suppler_name>0) $sql_cond=" and a.supplier_id=$cbo_suppler_name";
	if($btbLc_id!="") $sql_cond.=" and a.id=$btbLc_id";
	
	if(str_replace("'","",$cbo_company_name)>0) $sql_cond.=" and a.importer_id=$cbo_company_name";

	
	if($txt_date_from!="" && $txt_date_to!="")
	{
		if($db_type==0)
		{
			$sql_cond.="  and a.last_shipment_date between '".$txt_date_from."' and '".$txt_date_to."'";
		}
		else
		{
			$sql_cond.="  and a.last_shipment_date between '".$txt_date_from."' and '".$txt_date_to."'";
		}
	}
	
	
	$sql="SELECT
	a.id as btb_id, a.lc_number,a.importer_id, a.lc_date, a.supplier_id, a.tenor, a.payterm_id, a.last_shipment_date, a.pi_id
	FROM com_btb_lc_master_details a
	WHERE
		a.status_active=1 and
		a.is_deleted=0 and
		a.item_basis_id=1
		$sql_cond 
	ORDER BY a.lc_number";

	//echo $sql; die; //a.item_category_id=1 and
	
	$sql_result=sql_select($sql);
	$piArr=array();$btb_lc_id=$btb_sc_id=0;
	foreach($sql_result as $row)
	{
		$btbData[$row[csf("lc_number")]]=array(
			btb_id		=>$row[csf("btb_id")],
			lc_number	=>$row[csf("lc_number")],
			importer_id	=>$row[csf("importer_id")],
			supplier_id	=>$row[csf("supplier_id")],
			tenor		=>$row[csf("tenor")],
			payterm_id	=>$row[csf("payterm_id")],
			last_shipment_date=>$row[csf("last_shipment_date")],
			pi_id		=>$row[csf("pi_id")],
			lc_date		=>$row[csf("lc_date")]
		);
		$all_btb_id.=$row[csf("btb_id")].",";
		foreach(explode(',',$row[csf("pi_id")]) as $pi){
			$piArr[$pi]=$pi;
		}
	}
	
	//var_dump($btbData);die;
	
	$pi_string = implode(',',$piArr);
	$all_btb_id=chop($all_btb_id,",");
	
	$all_lc_sql="select a.export_lc_no as export_lc_no, a.internal_file_no as internal_file_no, a.lc_year as lc_sc_year, b.import_mst_id as btb_id from com_export_lc a, com_btb_export_lc_attachment b where a.id=b.lc_sc_id and b.is_lc_sc=0 and b.import_mst_id in($all_btb_id)";
	$allo_lc_sc_data=array();
	$all_lc_result=sql_select($all_lc_sql);
	foreach($all_lc_result as $row)
	{
		$allo_lc_sc_data[$row[csf("btb_id")]][0]["export_lc_no"].=$row[csf("export_lc_no")].",";
		$allo_lc_sc_data[$row[csf("btb_id")]][0]["internal_file_no"].=$row[csf("internal_file_no")].",";
		$allo_lc_sc_data[$row[csf("btb_id")]][0]["lc_sc_year"].=$row[csf("lc_sc_year")].",";
	}
	
	$all_sc_sql="select a.contract_no as export_lc_no, a.internal_file_no as internal_file_no, a.sc_year as lc_sc_year, b.import_mst_id as btb_id from com_sales_contract a, com_btb_export_lc_attachment b where a.id=b.lc_sc_id and b.is_lc_sc=1 and b.import_mst_id in($all_btb_id)";
	$all_sc_result=sql_select($all_sc_sql);
	foreach($all_sc_result as $row)
	{
		$allo_lc_sc_data[$row[csf("btb_id")]][1]["export_lc_no"].=$row[csf("export_lc_no")].",";
		$allo_lc_sc_data[$row[csf("btb_id")]][1]["internal_file_no"].=$row[csf("internal_file_no")].",";
		$allo_lc_sc_data[$row[csf("btb_id")]][1]["lc_sc_year"].=$row[csf("lc_sc_year")].",";
	}
	
	
	
	$recSql="SELECT a.id as rcv_id, a.company_id, c.id as product_id, a.booking_id, c.supplier_id, c.lot, b.id as trans_id, b.order_qnty, b.order_amount, b.order_rate, c.yarn_count_id
	FROM
		inv_receive_master a,
		inv_transaction b,
		product_details_master c
	WHERE 
		a.id=b.mst_id and
		b.prod_id=c.id and  
		a.entry_form=1 and 
		a.receive_basis=1 and
		b.transaction_type=1 and 
		b.item_category=1 and 
		a.status_active=1 and 
		a.is_deleted=0 and
		b.status_active=1 and 
		b.is_deleted=0 and
		a.booking_id in($pi_string)";
	$recSql_result=sql_select($recSql);
	foreach($recSql_result as $row)
	{
		$key=$row[csf("product_id")]."__".$row[csf("booking_id")];
		$keyDataArr[$row[csf("booking_id")]][$key]=$key;
		$rceiveDataArr[$key]["lot"]=$row[csf("lot")];
		$rceiveDataArr[$key]["count"]=$row[csf("yarn_count_id")];
		$rceiveDataArr[$key]["rec_qty"]+=$row[csf("order_qnty")];
		$rceiveDataArr[$key]["rec_amount"]+=$row[csf("order_amount")];
		$rateArr[$row[csf("product_id")]]=$row[csf("order_rate")];
		$countArr[$row[csf("yarn_count_id")]]=$row[csf("yarn_count_id")];
		$allRcvIdArr[$row[csf("rcv_id")]]=$row[csf("rcv_id")];
		$transBookId[$row[csf("trans_id")]]=$row[csf("booking_id")];
		$prodIdArr[$row[csf("product_id")]]=$row[csf("product_id")];
		$rcvTransId[$row[csf("trans_id")]]=$row[csf("trans_id")];
		
	}
	$count_id_string=implode(',',$countArr);
	//$product_id_string=implode(',',$productIdArr);
	//echo var_dump();
	//echo $recSql;die;
	
	
	$mrr_trans_con=" and";
	$rcv_trans_id_arr=array_chunk($rcvTransId,999);
	foreach($rcv_trans_id_arr as $trans_data)
	{
		if($mrr_trans_con==" and")
		{
			$mrr_trans_con .="  ( recv_trans_id in(".implode(',',$trans_data).")";
		}
		else
		{
			$mrr_trans_con .=" or recv_trans_id in(".implode(',',$trans_data).")";
		}
	}
	$mrr_trans_con .=")";
	
	$rcv_rtn_mrr_sql="select recv_trans_id, issue_trans_id from inv_mrr_wise_issue_details where status_active=1 $mrr_trans_con";
	$rcv_rtn_mrr_result=sql_select($rcv_rtn_mrr_sql);
	$rcv_rtn_mrr_trans_id=array();
	foreach($rcv_rtn_mrr_result as $row)
	{
		$rcv_rtn_mrr_trans_id[$row[csf("issue_trans_id")]]=$row[csf("recv_trans_id")];
		$issueTrId[$row[csf("issue_trans_id")]]=$row[csf("issue_trans_id")];
	}
	
	
	
	$rcv_id_con=" and";
	$rcv_id_arr=array_chunk($allRcvIdArr,999);
	foreach($rcv_id_arr as $rcv_data)
	{
		if($rcv_id_con==" and")
		{
			$rcv_id_con .="  ( a.received_id in(".implode(',',$rcv_data).")";
		}
		else
		{
			$rcv_id_con .=" or a.received_id in(".implode(',',$rcv_data).")";
		}
	}
	$rcv_id_con .=")";
	
	$rcv_rtn_sql="select a.id, a.received_id, c.supplier_id, c.lot, b.id as trans_id, b.prod_id, b.cons_quantity, b.cons_amount
	from inv_issue_master a, inv_transaction b, product_details_master c 
	where a.id=b.mst_id and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and  b.is_deleted=0 and a.entry_form=8 and b.item_category=1 and b.transaction_type=3 $rcv_id_con";
	
	//echo $rcv_rtn_sql;die;
	
	$rcv_rtn_result=sql_select($rcv_rtn_sql);
	$rcv_rtn_data=array();
	foreach($rcv_rtn_result as $row)
	{
		$key=$row[csf("prod_id")]."__".$transBookId[$rcv_rtn_mrr_trans_id[$row[csf("trans_id")]]];
		$rcv_rtn_data[$key]["rcv_rtn_qnty"]+=$row[csf("cons_quantity")];
		$rcv_rtn_data[$key]["rcv_rtn_amt"]+=$row[csf("cons_amount")]/$currency_conversion_rate;
	}
	//echo $rcv_rtn_sql;die;
	
	
	$product_trans_con=" and";
	$issue_trans_id_arr=array_chunk($issueTrId,999);
	foreach($issue_trans_id_arr as $trans_data)
	{
		if($product_trans_con==" and")
		{
			$product_trans_con .="  ( b.id in(".implode(',',$trans_data).")";
		}
		else
		{
			$product_trans_con .=" or b.id in(".implode(',',$trans_data).")";
		}
	}
	$product_trans_con .=")";
	
	
	$product_con=" and";
	$rcv_prod_id_arr=array_chunk($prodIdArr,999);
	foreach($rcv_prod_id_arr as $prod_data)
	{
		if($product_con==" and")
		{
			$product_con .="  ( b.prod_id in(".implode(',',$prod_data).")";
		}
		else
		{
			$product_con .=" or b.prod_id in(".implode(',',$prod_data).")";
		}
	}
	$product_con .=")";
	
	
	//echo $product_con."==".$product_trans_con;die;
	
	
	$issSql="SELECT a.id as issue_id, a.company_id, c.id as product_id, a.buyer_id, a.buyer_job_no, a.style_ref, a.booking_id, a.issue_basis, a.issue_purpose, c.supplier_id, c.lot, d.quantity, (d.quantity*b.cons_rate) as issue_amt, c.avg_rate_per_unit, c.yarn_count_id, d.po_breakdown_id, e.po_number, e.pub_shipment_date, e.shiping_status, b.id as trans_id
	FROM
		inv_issue_master a,
		inv_transaction b,
		product_details_master c,
		order_wise_pro_details d,
		wo_po_break_down e
	WHERE
		a.id=b.mst_id and 
		b.id=d.trans_id and
		e.id=d.po_breakdown_id and
		d.prod_id=c.id and
		b.prod_id=c.id and
		a.issue_basis=1 and
		a.issue_purpose in(1,2) and 
		b.transaction_type=2 and 
		b.item_category=1 and 
		a.entry_form=3 and
		d.entry_form=3 and 
		a.status_active=1 and 
		a.is_deleted=0 and
		b.status_active=1 and 
		b.is_deleted=0 
		$product_con $product_trans_con"; 
	
	
	//c.yarn_count_id in ($count_id_string) and c.id in($product_id_string)
	
	//echo $issSql;die;
	
	$issSql_result=sql_select($issSql);
	$item_total_issue=array();
	foreach($issSql_result as $row)
	{
		$key=$row[csf("product_id")]."__".$transBookId[$rcv_rtn_mrr_trans_id[$row[csf("trans_id")]]];
		$issueDataArr[$key]["po_breakdown_id"][$row[csf("po_breakdown_id")]]=$row[csf("po_breakdown_id")];
		$issueDataArr[$key][$row[csf("po_breakdown_id")]]["issue_basis"]=$row[csf("issue_basis")];
		$issueDataArr[$key][$row[csf("po_breakdown_id")]]["issue_purpose"]=$row[csf("issue_purpose")];
		$issueDataArr[$key][$row[csf("po_breakdown_id")]]["buyer_id"]=$row[csf("buyer_id")];
		$issueDataArr[$key][$row[csf("po_breakdown_id")]]["buyer_job_no"]=$row[csf("buyer_job_no")];
		$issueDataArr[$key][$row[csf("po_breakdown_id")]]["style_ref"]=$row[csf("style_ref")];
		$issueDataArr[$key][$row[csf("po_breakdown_id")]]["issue_qty"]+=$row[csf("quantity")];
		$issueDataArr[$key][$row[csf("po_breakdown_id")]]["issue_amount"]+=($row[csf("issue_amt")]/$currency_conversion_rate);
		$item_total_issue[$key]["issue_qty"]+=$row[csf("quantity")];
		$item_total_issue[$key]["issue_amount"]+=($row[csf("issue_amt")]/$currency_conversion_rate);
		
		$ShipStatusArr[$row[csf("po_breakdown_id")]]=$row[csf("shiping_status")];
		$pubShipDateArr[$row[csf("po_breakdown_id")]]=$row[csf("pub_shipment_date")];
		$orderNoArr[$row[csf("po_breakdown_id")]]=$row[csf("po_number")];
		$orderIdArr[$row[csf("po_breakdown_id")]]=$row[csf("po_breakdown_id")];
		$buyerIdArr[$row[csf("buyer_id")]]=$row[csf("buyer_id")];
		$issueId_arr[$row[csf("issue_id")]]=$row[csf("issue_id")];
		$issueBooking[$row[csf("issue_id")]]=$transBookId[$rcv_rtn_mrr_trans_id[$row[csf("trans_id")]]];
	}
	//var_dump($issueDataArr);die;
	 //$order_id_string=implode(',',$orderIdArr);
	 $buyer_id_string=implode(',',$buyerIdArr);
	
	
	$issue_con=" and";
	$issue_id_arr=array_chunk($issueId_arr,999);
	foreach($issue_id_arr as $issue_data)
	{
		if($issue_con==" and")
		{
			$issue_con .="  ( b.issue_id in(".implode(',',$issue_data).")";
		}
		else
		{
			$issue_con .=" or b.issue_id in(".implode(',',$issue_data).")";
		}
	}
	$issue_con .=")";
	//echo $issue_con;die; 
	$sql_issue_rtn="Select c.id as prod_id, c.supplier_id, c.lot, d.po_breakdown_id, d.quantity as issue_rtn_qnty, (d.quantity*b.cons_rate) as issue_rtn_amt, b.issue_id 
	from  inv_receive_master a, inv_transaction b, product_details_master c, order_wise_pro_details d
	where a.id=b.mst_id and b.id=d.trans_id and d.prod_id=c.id and a.receive_basis=1 and b.item_category=1 and b.transaction_type=4 and a.entry_form=9 and d.entry_form=9 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $issue_con";
	
	//echo $sql_issue_rtn;die;

	$sql_issue_rtn_result=sql_select( $sql_issue_rtn );
	$issue_return_data=$item_wise_issue_rtn=array();
	foreach ($sql_issue_rtn_result as $row)
	{
		$key=$row[csf("prod_id")]."__".$issueBooking[$row[csf("issue_id")]];
		$issue_return_data[$key][$row[csf("po_breakdown_id")]]['issue_rtn_qnty']+=$row[csf("issue_rtn_qnty")];
		$issue_return_data[$key][$row[csf("po_breakdown_id")]]['issue_rtn_amt']+=$row[csf("issue_rtn_amt")]/$currency_conversion_rate;
		$item_wise_issue_rtn[$key]['issue_rtn_qnty']+=$row[csf("issue_rtn_qnty")];
		$item_wise_issue_rtn[$key]['issue_rtn_amt']+=$row[csf("issue_rtn_amt")]/$currency_conversion_rate;
	}
	
	$sql_trans_out="Select b.prod_id, f.recv_trans_id, f.issue_qnty as trans_out_qnty, f.amount as trans_out_amt 
	from inv_transaction b, inv_mrr_wise_issue_details f
	where b.id=f.issue_trans_id and b.item_category=1 and b.transaction_type=6 and b.status_active=1 and b.is_deleted=0 and f.status_active=1 and f.is_deleted=0 $product_con $mrr_trans_con";
	
	//echo $sql_issue_rtn;die;

	$sql_trans_out_result=sql_select( $sql_trans_out );
	$trans_out_data=array();
	foreach ($sql_trans_out_result as $row)
	{
		$key=$row[csf("prod_id")]."__".$transBookId[$row[csf("recv_trans_id")]];
		$trans_out_data[$key]['trans_out_qnty']+=$row[csf("trans_out_qnty")];
		$trans_out_data[$key]['trans_out_amt']+=$row[csf("trans_out_amt")]/$currency_conversion_rate;
	}
	
	
	
	//var_dump($trans_out_data);die;
	
	$po_list_arr=array_chunk($orderIdArr,999);
	$sc_sql="select b.wo_po_break_down_id,contract_no, internal_file_no,a.sc_year from com_sales_contract a,com_sales_contract_order_info b where  a.id=b.com_sales_contract_id";
	$lc_sc_sql=" and";
	$s=0;
	foreach($po_list_arr as $po_process)
	{
		if($s==0)
		{$lc_sc_sql .="  ( b.wo_po_break_down_id in(".implode(',',$po_process).")";
		}
		else{
		$lc_sc_sql .=" or b.wo_po_break_down_id in(".implode(',',$po_process).")";
		}
		$s++;	
	}
	$lc_sc_sql .=")";
				
	$sc_sql_result=sql_select($sc_sql.$lc_sc_sql);
	foreach($sc_sql_result as $row)
	{
		$lcscArr[$row[csf('wo_po_break_down_id')]]['file']['SC:'.$row[csf('internal_file_no')]]='SC:'.$row[csf('internal_file_no')];
		$lcscArr[$row[csf('wo_po_break_down_id')]]['contact']['SC:'.$row[csf('contract_no')]]='SC:'.$row[csf('contract_no')];
		$lcscArr[$row[csf('wo_po_break_down_id')]]['year']['SC:'.$row[csf('sc_year')]]='SC:'.$row[csf('sc_year')];

	}
	
	
	$lc_sql="select b.wo_po_break_down_id,a.export_lc_no, a.internal_file_no,a.lc_year from com_export_lc a,com_export_lc_order_info b where  a.id=b.com_export_lc_id ";
	
	$sc_sql_result=sql_select($lc_sql.$lc_sc_sql);
	foreach($sc_sql_result as $row)
	{
		$lcscArr[$row[csf('wo_po_break_down_id')]]['file'][$row[csf('export_lc_no')]]='LC:'.$row[csf('internal_file_no')];
		$lcscArr[$row[csf('wo_po_break_down_id')]]['contact'][$row[csf('contract_no')]]='LC:'.$row[csf('export_lc_no')];
		$lcscArr[$row[csf('wo_po_break_down_id')]]['year'][$row[csf('lc_year')]]='LC:'.$row[csf('lc_year')];

	}
	
 	ob_start();
	?>
    <!--<fieldset style="width:1120px">-->
    	<div style="width:3020px; margin-left:10px;" align="left">
            <table width="3000" cellpadding="0" cellspacing="0" style="visibility:hidden; border:none" id="caption">
                <tr>
                   <td align="center" width="100%" colspan="11" style="font-size:16px"><strong><? echo $report_title; ?></strong></td>
                </tr>
            </table>
            <table width="3000" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
                <thead>
                	<tr>
                        <th width="35">SL</th>
                        <th width="120">Company</th>	
                        <th width="100">BTB LC No</th>
                        <th width="80">LC Date</th>
                        <th width="140">Sup Name</th>	
                        <th width="80">Pay Term</th>	
                        <th width="60">Tenor</th>	
                        <th width="80">Last Delv dt</th>	
                        <th width="80">Maturity dt</th>	
                        <th width="80">Lot No</th>	
                        <th width="80">Count</th>	
                        <th width="80">Qty Rcv (Kg)	</th>
                        <th width="80">Value Rcv (USD)</th>
                        <th width="80">Buyer</th>	
                        <th width="80">Style</th>	
                        <th width="100">Job No</th>	
                        <th width="80">PO No</th>	
                        <th width="80">Total Req Qty (Kg)</th>	
                        <th width="80">Total Req Value (USD)</th>	
                        <th width="80">Issue Basis</th>	
                        <th width="80">Issue Purpose</th>	
                        <th width="80">Issue Qty (Kg)</th>	
                        <th width="80">Issue Value (USD)</th>	
                        <th width="80">Ship date</th>	
                        <th width="120">Used in LC/SC</th>	
                        <th width="80">Actual File No.</th>
                        <th width="80">Actual File Year</th>
                        <th width="80">Transfer Qty (Kg)</th>	
                        <th width="80">Transfer Value (USD)</th>
                        <th width="80">To be used Qty (Kg)</th>	
                        <th width="80">To be used Value (USD)</th>	
                        <th width="120">Allocated LC/SC</th>	
                        <th width="80">Alloc.File No.</th>
                        <th width="80">Alloc.File Year</th>	
                        <th>Shiping Status</th>
                    </tr>
                </thead>
            </table>
   			<div style="width:3020px; overflow-y:scroll; max-height:350px; overflow-x:hidden;" id="scroll_body" align="left">
            <table width="3000" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body">   
            	<tbody>
				<?
				$condition= new condition();
				if(str_replace("'","",$cbo_company_name)){
					$condition->company_name(" =$cbo_company_name");
				}
				if(str_replace("'","",$buyer_id_string)){
					$condition->buyer_name(" in($buyer_id_string)");
				}
				
								
			
				$po_list_arr=array_chunk($orderIdArr,999);
				$sqlCon = "!=0 and";
				foreach($po_list_arr as $po_process)
				{
					if($sqlCon=="!=0 and")
					{$sqlCon .="  ( b.id in(".implode(',',$po_process).")";
					}
					else{
					$sqlCon .=" or b.id in(".implode(',',$po_process).")";
					}
					
				}
				$sqlCon .=")";
				$condition->po_id($sqlCon); 	
				
				$condition->init();
				$yarn = new yarn($condition);
				//echo $yarn->getQuery();die;
				//$fabricAmunt = new fabric($condition);
				//$fabric2= new fabric($condition);
				$yarn_req_qty_amu_arr=$yarn->getOrderWiseYarnQtyAndAmountArray();
				//$yarn_req_qty_arr=$fabricQty->getQtyArray_by_order_knitAndwoven_greyAndfinish();
				//$yarn_req_amu_arr=$fabricAmunt->getAmountArray_by_order_knitAndwoven_greyAndfinish();	
				 //var_dump($yarn_req_qty_amu_arr);die;
				
				$i=1; 	
                foreach($btbData as $rows)
                {
					$lot_count_arr=array();
					foreach(explode(',',$rows["pi_id"]) as $pi)
					{
						foreach($keyDataArr[$pi] as $key_val)
						{
							$lot_count_arr[$key_val]=$key_val;	
						}
					}
					
					//var_dump($lot_count_arr);die;
					
					foreach($lot_count_arr as $key=>$value)
					{	
						if($issueDataArr[$key]["po_breakdown_id"]==''){$issueDataArr[$key]["po_breakdown_id"]=array(0);}
						
						foreach($issueDataArr[$key]["po_breakdown_id"] as $po)
						{
							$last_shipment_date=change_date_format($rows[last_shipment_date]);
							$maturity=date('d-m-Y', strtotime($last_shipment_date. ' + '.($rows[tenor]*1).' days'));
							$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF"; 
							$receive_qnty=$receive_amt=$balance_qnty=$balance_amt=$item_issue_qnty=$item_issue_amt=$transfer_qnty=$transfer_value=0;
							if($rcv_data_test[$key]=="")
							{
								$rcv_data_test[$key]=$key;
								$receive_qnty=$rceiveDataArr[$key]["rec_qty"]-$rcv_rtn_data[$key]["rcv_rtn_qnty"];
								$receive_amt=$rceiveDataArr[$key]["rec_amount"]-$rcv_rtn_data[$key]["rcv_rtn_amt"];
								$item_issue_qnty=($item_total_issue[$key]["issue_qty"]-$item_wise_issue_rtn[$key]['issue_rtn_qnty']);
								$item_issue_amt=($item_total_issue[$key]["issue_amount"]-$item_wise_issue_rtn[$key]['issue_rtn_amt']);
								$transfer_qnty=$trans_out_data[$key]['trans_out_qnty'];
								$transfer_value=$trans_out_data[$key]['trans_out_amt'];
								$balance_qnty=($receive_qnty-($item_issue_qnty+$transfer_qnty));
								$balance_amt=($receive_amt-($item_issue_amt+$transfer_value));
							}
							
							$issue_qnty=$issueDataArr[$key][$po]["issue_qty"]-$issue_return_data[$key][$po]['issue_rtn_qnty'];
							$issue_amt=$issueDataArr[$key][$po]["issue_amount"]-$issue_return_data[$key][$po]['issue_rtn_amt'];
							$rqty=$ramu=0;
							if($order_test[$po]=="")
							{
								$order_test[$po]=$po;
								$rqty = $yarn_req_qty_amu_arr[$po][qty];
								$ramu = $yarn_req_qty_amu_arr[$po][amount];
							}
							
							if(chop($allo_lc_sc_data[$rows[btb_id]][0]["export_lc_no"],",")!="")
							{
								$allo_lc_sc=chop($allo_lc_sc_data[$rows[btb_id]][0]["export_lc_no"],","); 
								$allo_file=chop($allo_lc_sc_data[$rows[btb_id]][0]["export_lc_no"],",");
								$allo_year=chop($allo_lc_sc_data[$rows[btb_id]][0]["lc_sc_year"],",");
							}
							else 
							{
								$allo_lc_sc=chop($allo_lc_sc_data[$rows[btb_id]][1]["export_lc_no"],",");
								$allo_file=chop($allo_lc_sc_data[$rows[btb_id]][1]["internal_file_no"],",");
								$allo_year=chop($allo_lc_sc_data[$rows[btb_id]][1]["lc_sc_year"],",");
							} 
							
							 
							
							?>
							<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
								<td width="35" align="center"><? echo $i; ?></td>
								<td width="120"><? echo $company_arr[$rows[importer_id]]; ?></td>
								<td width="100"><? echo $rows[lc_number]; ?></td>
								<td width="80" align="center"><? echo change_date_format($rows[lc_date]); ?></td>
								<td width="140"><? echo $supplier_arr[$rows[supplier_id]]; ?></td>
								<td width="80"><? echo $pay_term[$rows[payterm_id]]; ?></td>
								<td width="60" align="center"><? echo $rows[tenor]; ?></td>
								<td width="80" align="center"><p><? echo change_date_format($rows[last_shipment_date]); ?></p></td>
								<td width="80" align="center"><p><? echo $maturity; ?></p></td>
								<td width="80"><p><? echo $rceiveDataArr[$key]["lot"]; ?></p></td>
								<td width="80"><p><? echo $count_arr[$rceiveDataArr[$key]["count"]]; ?></p></td>
								<td width="80" align="right"><p><? echo number_format($receive_qnty,2,".","");?></p></td>
								<td width="78" align="right" style="padding-right:2px;"><p><? echo number_format($receive_amt,2,".","");?></p></td>
								<td width="80"><p><? echo $buyer_arr[$issueDataArr[$key][$po]["buyer_id"]]; ?></p></td>
								<td width="80"><p><? echo $issueDataArr[$key][$po]["style_ref"]; ?></p></td>
								<td width="100"><p><? echo $issueDataArr[$key][$po]["buyer_job_no"]; ?></p></td>
								<td width="80" align="right"><p><? echo $orderNoArr[$po]; ?></p></td>
								<td width="80" align="right"><? echo number_format($rqty,2,".",""); ?></td>
								<td width="80" align="right"><? echo number_format($ramu,2,".",""); ?> </td>
								<td width="80"><? echo $issue_basis[$issueDataArr[$key][$po]["issue_basis"]]; ?></td>
								<td width="80"><? echo $yarn_issue_purpose[$issueDataArr[$key][$po]["issue_purpose"]]; ?></td>
								<td width="80" align="right"><? echo number_format($issue_qnty,2,".",""); ?></td>
								<td width="80" align="right"><? echo number_format($issue_amt,2,".",""); ?></td>
								<td width="80" align="center"><? echo change_date_format($pubShipDateArr[$po]); ?></td>
								<td width="120"><p><?  echo implode(',',$lcscArr[$po]['contact']);  ?></p> </td> 
								<td width="80"><p><? echo implode(',',$lcscArr[$po]['file']);?></p></td>
								<td width="80"><p><? echo implode(',',$lcscArr[$po]['year']); ?></p></td>
                                <td width="80" align="right"><? echo number_format($transfer_qnty,2,".",""); ?></td>
								<td width="80" align="right"><? echo number_format($transfer_value,2,".",""); ?></td>
                                <td width="80" align="right" title="<? echo $item_issue_qnty; ?>"><? echo number_format($balance_qnty,2,".",""); ?></td>
								<td width="80" align="right" title="<? echo $item_issue_amt; ?>"><? echo number_format($balance_amt,2,".",""); ?></td>
                                <td width="120"><p><?  echo $allo_lc_sc;  ?></p> </td> 
								<td width="80"><p><? echo $allo_file;?></p></td>
								<td width="80"><p><? echo $allo_year; ?></p></td>  
								<td><? echo $shipment_status[$ShipStatusArr[$po]]; ?></td> 
							</tr>
							<?
							$total_rcv_qnty+=$receive_qnty;
							$total_rcv_amt+=$receive_amt;
							$total_req_qnty+=$rqty;
							$total_req_amt+=$ramu;
							$total_issue_qnty+=$issue_qnty;
							$total_issue_amt+=$issue_amt;
							$total_transfer_qnty+=$transfer_qnty;
							$total_transfer_value+=$transfer_value;
							$total_balance_qnty+=$balance_qnty;
							$total_balance_amt+=$balance_amt;
							$i++;
						}
					}
					
				}
				?>
                </tbody>         	
            </table>
            </div>
            <table width="3000" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
                <tfoot>
                	<tr>
                        <th width="35">&nbsp;</th>
                        <th width="120">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="140">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="60">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="80" align="right">Total:</th>
                        <th width="80" align="right" id="total_rcv_qnty"><? echo number_format($total_rcv_qnty,2,".",""); ?></th>
                        <th width="80" align="right" id="value_total_rcv_amt"><? echo number_format($total_rcv_amt,2,".",""); ?></th>
                        <th width="80">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="80" align="right" id="total_total_req_qnty"><? echo number_format($total_req_qnty,2,".",""); ?></th>
                        <th width="80" align="right" id="value_total_req_amt"><? echo number_format($total_req_amt,2,".",""); ?></th>
                        <th width="80">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="80" align="right" id="total_total_issue_qnty"><? echo number_format($total_issue_qnty,2,".",""); ?></th>
                        <th width="80" align="right" id="value_total_issue_amt"><? echo number_format($total_issue_amt,2,".",""); ?></th>
                        <th width="80">&nbsp;</th>
                        <th width="120">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="80" align="right" id="total_total_transfer_qnty"><? echo number_format($total_transfer_qnty,2,".",""); ?></th>
                        <th width="80" align="right" id="value_total_transfer_value"><? echo number_format($total_transfer_value,2,".",""); ?></th>
                        <th width="80" align="right" id="total_total_balance_qnty"><? echo number_format($total_balance_qnty,2,".",""); ?></th>
                        <th width="80" align="right" id="value_total_balance_amt"><? echo number_format($total_balance_amt,2,".",""); ?></th>
                        <th width="120">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th>&nbsp;</th>
                    </tr>
                </tfoot>
            </table>
        </div>      
    <!--</fieldset>-->      
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




if($action=="receive_popup")
{
	
	echo load_html_head_contents("Report Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$company_id=str_replace("'","",$company_id);
	$pi_id=str_replace("'","",$pi_id);
	$yarn_count_arr=return_library_array("SELECT id,yarn_count FROM lib_yarn_count","id","yarn_count");
	$pi_dtsl_sql=sql_select("select pi_id, count_name, yarn_composition_item1, yarn_composition_percentage1, yarn_composition_item2, yarn_composition_percentage2, yarn_type from  com_pi_item_details where pi_id in($pi_id)");
	$pi_data_all=array();
	foreach($pi_dtsl_sql as $row)
	{
		$pi_data_all[$row[csf("pi_id")]]["count_name"]=$row[csf("count_name")];
		$pi_data_all[$row[csf("pi_id")]]["yarn_composition_item1"]=$row[csf("yarn_composition_item1")];
		$pi_data_all[$row[csf("pi_id")]]["yarn_composition_percentage1"]=$row[csf("yarn_composition_percentage1")];
		$pi_data_all[$row[csf("pi_id")]]["yarn_composition_item2"]=$row[csf("yarn_composition_item2")];
		$pi_data_all[$row[csf("pi_id")]]["yarn_composition_percentage2"]=$row[csf("yarn_composition_percentage2")];
		$pi_data_all[$row[csf("pi_id")]]["yarn_type"]=$row[csf("yarn_type")];
	}
	//var_dump($composition[1]);die;
	//echo $pi_dtsl_sql;die;
	?>
	<script>
	
	/*function print_window()
	{
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
	}*/	
	
	</script>	
    <div id="report_container" align="center" style="width:700px">
	<fieldset style="width:700px; margin-left:10px">
            <table class="rpt_table" border="1" rules="all" width="700" cellpadding="0" cellspacing="0">
             	<thead>
                	<tr>
                        <th width="100">PI No.</th>
                        <th width="70">PI Date</th>
                        <th width="60">Count</th>
                        <th width="160">Composition</th>
                        <th width="80">Type</th>
                        <th width="70">Recv. Date</th>
                        <th width="80">Challan No</th>
                        <th>Qnty</th>
                    </tr>
                </thead>
                <tbody>
                <?
				
				$sql="select c.id as pi_id, c.pi_number, c.pi_date, b.id as receive_id, b.receive_date, b.challan_no, sum(a.order_qnty) as qnty 
				from   inv_transaction a,  inv_receive_master b, com_pi_master_details c 
				where a.mst_id=b.id and b.booking_id=c.id and b.entry_form=1 and b.item_category=1 and b.receive_basis=1 and  b.booking_id in($pi_id) and b.company_id=$company_id
				group by c.id, c.pi_number, c.pi_date, b.id, b.receive_date, b.challan_no";
				$total_pi_qnty=0;
				//echo $sql;die;
				$result=sql_select($sql);$i=1;
				foreach($result as $row)  
				{
					if ($i%2==0)
					$bgcolor="#E9F3FF";
					else
					$bgcolor="#FFFFFF";
					$composition_data="";
					if($pi_data_all[$row[csf("pi_id")]]["yarn_composition_item1"]>0) $composition_data=$composition[$pi_data_all[$row[csf("pi_id")]]["yarn_composition_item1"]]." ".$pi_data_all[$row[csf("pi_id")]]["yarn_composition_percentage1"]."%";
					if($pi_data_all[$row[csf("pi_id")]]["yarn_composition_item2"]>0) $composition_data.=" ".$composition[$pi_data_all[$row[csf("pi_id")]]["yarn_composition_item2"]]." ".$pi_data_all[$row[csf("pi_id")]]["yarn_composition_percentage2"]."%";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
						<td><p><? echo $row[csf('pi_number')]; ?>&nbsp;</p></td>
						<td><p><? if($row[csf('pi_date')]!="" && $row[csf('pi_date')]!="0000-00-00") echo change_date_format($row[csf('pi_date')]); ?>&nbsp;</p></td>
						<td><p><? echo $yarn_count_arr[$pi_data_all[$row[csf("pi_id")]]["count_name"]]; ?>&nbsp;</p></td>
						<td><p><? echo $composition_data; ?>&nbsp;</p></td>
						<td><p><? echo $yarn_type[$pi_data_all[$row[csf("pi_id")]]["yarn_type"]]; ?>&nbsp;</p></td>
                        <td><p><? if($row[csf('receive_date')]!="" && $row[csf('receive_date')]!="0000-00-00") echo change_date_format($row[csf('receive_date')]); ?>&nbsp;</p></td>
                        <td><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
                        <td align="right"><? echo number_format($row[csf('qnty')],2); $total_pi_qnty+=$row[csf('qnty')];  ?></td>
					</tr>
					<?
					$i++;
				}
				?>
                </tbody> 
                <tfoot>
                	<tr>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>Total :</th>
                        <th align="right"><? echo number_format($total_pi_qnty,2) ; ?></th>
                    </tr>
                </tfoot>  
            </table>
        </fieldset>
    </div>
	<?
    exit();

}


if($action=="pi_popup")
{
	
	echo load_html_head_contents("Report Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$company_id=str_replace("'","",$company_id);
	$pi_id=str_replace("'","",$pi_id);
	$yarn_count_arr=return_library_array("SELECT id,yarn_count FROM lib_yarn_count","id","yarn_count");
	$pi_dtsl_sql=("select a.id as pi_id , a.pi_number, a.pi_date, b.count_name, b.yarn_composition_item1, b.yarn_composition_percentage1, b.yarn_composition_item2, b.yarn_composition_percentage2, b.yarn_type, sum(b.quantity) as pi_quantity, sum(b.amount) as pi_amount
	from com_pi_master_details a, com_pi_item_details b 
	where a.id=b.pi_id and a.id in($pi_id)
	group by  a.id, a.pi_number, a.pi_date, b.count_name, b.yarn_composition_item1, b.yarn_composition_percentage1, b.yarn_composition_item2, b.yarn_composition_percentage2, b.yarn_type order by a.id");
	
	//echo $pi_dtsl_sql;die;
	?>
	<script>
	
	/*function print_window()
	{
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
	}*/	
	
	</script>	
    <div id="report_container" align="center" style="width:700px">
	<fieldset style="width:700px; margin-left:10px">
            <table class="rpt_table" border="1" rules="all" width="700" cellpadding="0" cellspacing="0">
             	<thead>
                	<tr>
                    	<th width="50">SL</th>
                        <th width="100">PI No.</th>
                        <th width="70">PI Date</th>
                        <th width="60">Count</th>
                        <th width="160">Composition</th>
                        <th width="80">Type</th>
                        <th width="70">PI Qnty</th>
                        <th>PI Value</th>
                    </tr>
                </thead>
                <tbody>
                <?
				$total_pi_qnty=0;
				$result=sql_select($pi_dtsl_sql);$i=1;$pi_test=array();
				foreach($result as $row)  
				{
					if ($i%2==0)
					$bgcolor="#E9F3FF";
					else
					$bgcolor="#FFFFFF";
					$composition_data="";
					if($row[csf("yarn_composition_item1")]>0) $composition_data=$composition[$row[csf("yarn_composition_item1")]]." ".$row[csf("yarn_composition_percentage1")]."%";
					if($row[csf("yarn_composition_item2")]>0) $composition_data.=" ".$composition[$row[csf("yarn_composition_item2")]]." ".$row[csf("yarn_composition_percentage2")]."%";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                    	<td align="center"><p><? echo $i; ?>&nbsp;</p></td>
						<td><p><? if($pi_test[$row[csf('pi_number')]]=="") echo $row[csf('pi_number')]; ?>&nbsp;</p></td>
						<td><p>
						<?
						if($pi_test[$row[csf('pi_number')]]=="")
						{
							$pi_test[$row[csf('pi_number')]]=$row[csf('pi_number')];
							if($row[csf('pi_date')]!="" && $row[csf('pi_date')]!="0000-00-00") echo change_date_format($row[csf('pi_date')]);
						}
						?>&nbsp;</p></td>
						<td><p><? echo $yarn_count_arr[$row[csf("count_name")]]; ?>&nbsp;</p></td>
						<td><p><? echo $composition_data; ?>&nbsp;</p></td>
						<td><p><? echo $yarn_type[$row[csf("yarn_type")]]; ?>&nbsp;</p></td>
                        <td align="right"><? echo number_format($row[csf('pi_quantity')],2); $total_pi_qnty+=$row[csf('pi_quantity')];  ?>&nbsp;</p></td>
                        <td align="right"><? echo number_format($row[csf('pi_amount')],2); $total_pi_value+=$row[csf('pi_amount')];  ?></td>
					</tr>
					<?
					$i++;
				}
				?>
                </tbody> 
                <tfoot>
                	<tr>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>Total :</th>
                        <th align="right"><? echo number_format($total_pi_qnty,2) ; ?></th>
                        <th align="right"><? echo number_format($total_pi_value,2) ; ?></th>
                    </tr>
                </tfoot>  
            </table>
        </fieldset>
    </div>
	<?
    exit();

}

?>
