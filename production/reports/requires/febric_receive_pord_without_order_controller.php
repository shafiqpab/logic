<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$user_name=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


if($db_type==2 || $db_type==1 )
{
	$select_year="to_char(a.insert_date,'YYYY')";
}
else if ($db_type==0)
{
	$select_year="year(a.insert_date)";
}

$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
$supplier_details=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
$buyer_short_name_library=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
$yarn_count_details=return_library_array( "select id, yarn_count from lib_yarn_count", "id", "yarn_count");
$sample_array=return_library_array( "select id,sample_name from lib_sample","id","sample_name");

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 140, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );     	 
	exit();
}

$tmplte=explode("**",$data);

if ($tmplte[0]=="viewtemplate") $template=$tmplte[1]; else $template=$lib_report_template_array[$_SESSION['menu_id']]['0'];
if ($template=="") $template=1;

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$cbo_wo_year=str_replace("'","",$cbo_wo_year);
	$txt_wo_no=trim(str_replace("'","",$txt_wo_no));
	
	$wo_cond="";
	if($txt_wo_no!="") $wo_cond=" and a.booking_no_prefix_num=$txt_wo_no"; 
	
	if($cbo_wo_year>0) $wo_cond.=" and $select_year='$cbo_wo_year'";
	//echo $wo_cond;die;
	
	if($cbo_buyer_name==0) $buyer_name="%%"; else $buyer_name=$cbo_buyer_name;

	$c_wo_no_date_cond="";
	if($txt_wo_no!="") $c_wo_no_date_cond.=" and c.booking_no_prefix_num=$txt_wo_no"; 
	if($cbo_wo_year) 
	{
		if($db_type==2 || $db_type==1 )
		{
			$c_wo_no_date_cond.=" and to_char(c.insert_date,'YYYY')='$cbo_wo_year'";
		}
		else if ($db_type==0)
		{
			$c_wo_no_date_cond.=" and year(c.insert_date)='$cbo_wo_year'";
		}
	} 

	if($db_type==0)
	{
		if($txt_date_from!="" && $txt_date_to!="")
		{
			$str_cond=" and a.booking_date  between '$txt_date_from' and '$txt_date_to'";
			$c_wo_no_date_cond.=" and c.booking_date  between '$txt_date_from' and '$txt_date_to'";
		}
		else
		{
			$str_cond="";
		}
	}
	else if($db_type==2)
	{
		if($txt_date_from!="" && $txt_date_to!="")
		{
			$str_cond=" and a.booking_date  between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'";
			$c_wo_no_date_cond.=" and c.booking_date  between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'";
		}
		else
		{
			$str_cond="";
		}
	}
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$cbo_company_name."' and module_id=2 and report_id=4 and is_deleted=0 and status_active=1");
	$format_ids=explode(",",$print_report_format);

	//echo $print_report_format;die;
	ob_start();
	?>
	<table cellpadding="0" cellspacing="0" width="1900">
		<tr>
		   <td align="center" width="100%" colspan="20" ><strong style="font-size:16px"><? echo $company_library[$cbo_company_name]; ?></strong></td>
		</tr>
	</table>
    <?
	//echo $sql;die;

	$sql_yarn_issue=sql_select("SELECT a.booking_id, b.id as trans_id, b.cons_quantity as issue_qty from inv_issue_master a, inv_transaction b, wo_non_ord_samp_booking_mst c where a.id=b.mst_id and a.booking_id = c.id and a.company_id=$cbo_company_name $c_wo_no_date_cond and a.issue_basis=1 and a.issue_purpose=8 and a.item_category=1 and b.transaction_type=2 and a.entry_form=3 and a.booking_id>0 and b.status_active=1 and b.is_deleted=0 union all select c.id as booking_id, b.id as trans_id, b.cons_quantity as issue_qty from inv_issue_master a, inv_transaction b, ppl_yarn_requisition_entry d, ppl_planning_info_entry_dtls e, ppl_planning_info_entry_mst f, wo_non_ord_samp_booking_mst c where a.id=b.mst_id and a.company_id=$cbo_company_name and b.requisition_no=d.requisition_no and d.knit_id= e.id and e.mst_id= f.id and f.booking_no=c.booking_no $c_wo_no_date_cond and a.issue_basis=3 and a.issue_purpose in (1,8) and a.item_category=1 and b.transaction_type=2 and a.entry_form=3 and b.status_active=1 and b.is_deleted=0  and a.status_active=1 and a.is_deleted=0");

	$y_issue_trans_id_arr=array();
	foreach($sql_yarn_issue as $row)
	{
		if($y_issue_trans_id_arr[$row[csf("trans_id")]] == "")
		{
			$y_issue_trans_id_arr[$row[csf("trans_id")]] = $row[csf("trans_id")];
			$yarn_issue_arr[$row[csf("booking_id")]] += $row[csf("issue_qty")];
		}
	}

	$sql_yarn_issue_rtn=sql_select("SELECT a.booking_id,b.id as trans_id, b.cons_quantity as issue_rtn_qty from  inv_receive_master a, inv_transaction b, wo_non_ord_samp_booking_mst c where a.id=b.mst_id and a.booking_id = c.id and a.company_id=$cbo_company_name $c_wo_no_date_cond and a.receive_basis=1 and a.item_category=1 and b.transaction_type=4 and a.entry_form=9 and a.booking_id>0 and a.booking_without_order=1 and a.status_active=1 and a.is_deleted=0  and b.status_active =1 and b.is_deleted=0 union all select c.id as booking_id, b.id as trans_id, b.cons_quantity as issue_rtn_qty from  inv_receive_master a, inv_transaction b, ppl_yarn_requisition_entry d, ppl_planning_info_entry_dtls e, ppl_planning_info_entry_mst f, wo_non_ord_samp_booking_mst c where a.id=b.mst_id  and a.company_id=$cbo_company_name $c_wo_no_date_cond and a.receive_basis=3 and a.item_category=1 and b.transaction_type=4 and a.entry_form=9 and a.booking_without_order=1 and a.booking_id= d.requisition_no and d.knit_id= e.id and e.mst_id= f.id and f.booking_no = c.booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0");

	foreach($sql_yarn_issue_rtn as $row)
	{
		if($y_issue_trans_id_arr[$row[csf("trans_id")]] == "")
		{
			$y_issue_trans_id_arr[$row[csf("trans_id")]] = $row[csf("trans_id")];
			$yarn_issue_rtn_arr[$row[csf("booking_id")]] += $row[csf("issue_rtn_qty")];
		}
	}

	//$sql_grey_knit_production=sql_select("select a.booking_id,sum(b.grey_receive_qnty) as receive_qty from inv_receive_master a, pro_grey_prod_entry_dtls b where a.id=b.mst_id and a.receive_basis=1 and a.booking_without_order=1 and a.entry_form=2 and a.booking_id>0  group by a.booking_id order by a.booking_id");


	$sql_grey_knit_production=sql_select("SELECT a.booking_id,sum(b.grey_receive_qnty) as receive_qty,b.trans_id from inv_receive_master a, pro_grey_prod_entry_dtls b, wo_non_ord_samp_booking_mst c where a.id=b.mst_id and a.booking_id=c.id and a.receive_basis=1 and a.booking_without_order=1 and a.entry_form=2 and a.booking_id>0 $c_wo_no_date_cond and b.status_active =1 and b.is_deleted=0 group by a.booking_id,b.trans_id union all select c.id as booking_id,sum(b.grey_receive_qnty) as receive_qty,b.trans_id from inv_receive_master a, pro_grey_prod_entry_dtls b, ppl_planning_info_entry_dtls d, ppl_planning_info_entry_mst e, wo_non_ord_samp_booking_mst c where a.id=b.mst_id and a.booking_id=d.id and d.mst_id= e.id and e.booking_no= c.booking_no and a.receive_basis=2 and a.booking_without_order=1 and a.entry_form=2 and a.booking_id>0 $c_wo_no_date_cond and b.status_active =1 and b.is_deleted=0 group by c.id,b.trans_id");

	foreach($sql_grey_knit_production as $row)
	{
		$grey_knit_receive_arr[$result[csf("booking_id")]]+=$result[csf("receive_qty")]; //auto receive
		$grey_knit_production_arr[$row[csf("booking_id")]]+=$row[csf("receive_qty")];

		/* if($row[csf("trans_id")])
		{
			$grey_knit_receive_arr[$result[csf("booking_id")]]+=$result[csf("receive_qty")]; //auto receive
		}
		else
		{
			$grey_knit_production_arr[$row[csf("booking_id")]]+=$row[csf("receive_qty")];
		} */
		
	}
	
	$sql_grey_knit_receive=sql_select("select a.booking_id,sum(b.cons_quantity) as receive_qty from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.receive_basis in(1,2,4,6) and a.booking_without_order=1 and a.entry_form in(22,2) and b.transaction_type in(1,4) and a.booking_id>0 group by a.booking_id union all 
	select  c.po_breakdown_id as booking_id,sum(c.qc_pass_qnty) as receive_qty 
	from inv_receive_master a,pro_roll_details c 
	where a.id=c.mst_id and a.receive_basis in(10) and c.booking_without_order=1 and a.entry_form in(58)  and a.booking_id>0 
	and c.status_active=1 and c.is_deleted=0 and c.entry_form in(58)
	group by c.po_breakdown_id 
	order by booking_id");




	//echo "select a.booking_id,sum(b.cons_quantity) as receive_qty from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.receive_basis in(1,2,4,6) and a.booking_without_order=1 and a.entry_form in(22) and b.transaction_type in(1,4) and a.booking_id>0 group by a.booking_id order by a.booking_id";
	
	foreach($sql_grey_knit_receive as $result)
	{
		$grey_knit_receive_arr[$result[csf("booking_id")]]+=$result[csf("receive_qty")];
	}
	
	$sql_grey_roll=sql_select("SELECT b.po_breakdown_id as booking_id, sum(b.qnty) as issue_qty from inv_issue_master a,pro_roll_details b, wo_non_ord_samp_booking_mst c where a.id=b.mst_id and b.po_breakdown_id=c.id and b.booking_without_order=1 and b.entry_form= 61 and a.entry_form=61 and a.company_id=$cbo_company_name $c_wo_no_date_cond group by b.po_breakdown_id,b.booking_without_order union all select a.booking_id, sum(b.issue_qnty) as issue_qty from inv_issue_master a, inv_grey_fabric_issue_dtls b, wo_non_ord_samp_booking_mst c where a.id=b.mst_id and a.booking_id=c.id and a.entry_form=16 and a.issue_basis=1 and a.issue_purpose=8 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$cbo_company_name $c_wo_no_date_cond group by a.booking_id");

	foreach($sql_grey_roll as $result)
	{
		$grey_issue_arr[$result[csf("booking_id")]]+=$result[csf("issue_qty")];
	}

	
	/*$sql_batch_qty=sql_select("select a.id,a.batch_no,a.booking_no_id,a.color_id,sum(b.batch_qnty) as batch_qnty from  pro_batch_create_mst a,  pro_batch_create_dtls b where a.id=b.mst_id and a.booking_without_order=1 group by a.id,a.batch_no,a.booking_no_id ,a.color_id ");*/


	$sql_batch_qty=sql_select("select a.id,a.batch_no,a.booking_no_id,a.color_id,sum(b.batch_qnty) as batch_qnty ,c.buyer_id
	from pro_batch_create_mst a, pro_batch_create_dtls b,wo_non_ord_samp_booking_mst c 
	where a.id=b.mst_id 
	 and a.booking_no_id=c.id 
	 and a.booking_without_order=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
	and c.status_active=1 and c.is_deleted=0 $c_wo_no_date_cond
	 group by a.id,a.batch_no,a.booking_no_id ,a.color_id ,c.buyer_id");


	//echo $sql_batch_qty;die;
	foreach($sql_batch_qty as $row)
	{
		$batch_qty_arr[$row[csf("booking_no_id")]][$row[csf("color_id")]][$row[csf("id")]]['batch_qnty']=$row[csf("batch_qnty")];
		$batch_qty_arr_check[$row[csf("booking_no_id")]][$row[csf("color_id")]]['batch_id'].=$row[csf("id")].",";
		$batch_qty_buyer_arr[$row[csf("booking_no_id")]][$row[csf("buyer_id")]]['batch_buyer_qnty']+=$row[csf("batch_qnty")];
		$all_batch_id_arr[$row[csf("id")]]=$row[csf("id")];
	}

	$all_batch_id_arr =array_filter($all_batch_id_arr);
	if(count($all_batch_id_arr)>0)
	{
		$all_batch_ids = implode(",", $all_batch_id_arr);
		$all_batch_ids_cond=""; $batchCond="";
		if($db_type==2 && count($all_batch_id_arr)>999)
		{
			$all_batch_id_arr_chunk=array_chunk($all_batch_id_arr,999) ;
			foreach($all_batch_id_arr_chunk as $chunk_arr)
			{
				$chunk_arr_value=implode(",",$chunk_arr);
				$batchCond.="  batch_id in($chunk_arr_value) or ";
			}
			$all_batch_ids_cond.=" and (".chop($batchCond,'or ').")";
		}
		else
		{
			$all_batch_ids_cond=" and batch_id in($all_batch_ids)";
		}
	}

	//var_dump($batch_qty_arr_check);die;
	$sql_dyeing_qty=sql_select("select id,batch_id,batch_no from pro_fab_subprocess where load_unload_id=2 $all_batch_ids_cond");
	foreach($sql_dyeing_qty as $row)
	{
		$dyeing_check_arr[$row[csf("batch_id")]]=$row[csf("id")];
	}
	//var_dump($dyeing_check_arr[1232]);die;


	$sql_finish_product=sql_select("SELECT c.id as booking_no_id, b.color_id, sum(b.receive_qnty) as receive_qty, b.trans_id from inv_receive_master a, pro_finish_fabric_rcv_dtls b,  pro_batch_create_mst d, wo_non_ord_samp_booking_mst c where a.id=b.mst_id and b.batch_id=d.id  and a.receive_basis=5 and a.entry_form in (7) and d.booking_without_order=1 and d.booking_no=c.booking_no $c_wo_no_date_cond group by c.id,b.color_id ,b.trans_id");

	foreach($sql_finish_product as $row)
	{
		if($row[csf("trans_id")])
		{
			$finish_receive_arr[$row[csf("booking_no_id")]][$row[csf("color_id")]]+=$row[csf("receive_qty")]; //auto receive
		}else{
			$finish_product_arr[$row[csf("booking_no_id")]][$row[csf("color_id")]] +=$row[csf("receive_qty")];
		}
	}

	$sql_finish_product_qc=sql_select("SELECT c.id as booking_no_id, b.color_id, sum(b.receive_qnty) as receive_qty, b.trans_id from inv_receive_master a, pro_finish_fabric_rcv_dtls b,  pro_batch_create_mst d, wo_non_ord_samp_booking_mst c where a.id=b.mst_id and b.batch_id=d.id  and  a.entry_form in (66) and  d.booking_no=c.booking_no $c_wo_no_date_cond and a.company_id=$cbo_company_name group by c.id,b.color_id ,b.trans_id");//d.booking_without_order=1 and

	foreach($sql_finish_product_qc as $row)
	{
		$finish_product_arr[$row[csf("booking_no_id")]][$row[csf("color_id")]] +=$row[csf("receive_qty")];
		
	}
	

	$sql_finish_receive=sql_select("SELECT c.id as booking_no_id,b.color_id,sum(b.receive_qnty) as receive_qty from inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_batch_create_mst d, wo_non_ord_samp_booking_mst c where a.id=b.mst_id  and a.entry_form in(37,68) and a.status_active=1 and a.is_deleted=0 and b.batch_id=d.id and d.booking_no=c.booking_no $c_wo_no_date_cond and a.company_id=$cbo_company_name group by c.id,b.color_id");
	
	foreach($sql_finish_receive as $row)
	{
		$finish_receive_arr[$row[csf("booking_no_id")]][$row[csf("color_id")]]+=$row[csf("receive_qty")];
	}

	$sql_finish_rcv_iss=sql_select("SELECT c.id as booking_no_id,b.color_id,sum(b.receive_qnty) as receive_qty,e.barcode_no from inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_batch_create_mst d, wo_non_ord_samp_booking_mst c,pro_roll_details e where a.id=b.mst_id  and a.entry_form in(37,68) and a.status_active=1 and a.is_deleted=0 and b.batch_id=d.id and d.booking_no=c.booking_no and b.id=e.dtls_id $c_wo_no_date_cond and a.company_id=$cbo_company_name group by c.id,b.color_id,e.barcode_no");
	
	$finish_rcv_iss_arr=array();
	foreach($sql_finish_rcv_iss as $row)
	{
		$finish_rcv_iss_arr[$row[csf("barcode_no")]]=$row[csf("barcode_no")];
	}

	$finish_rcv_iss_arr = array_filter($finish_rcv_iss_arr);

	$finish_rcv_iss_ids = implode(",", $finish_rcv_iss_arr);
	$all_finish_rcv_iss_arr_cond=""; $finish_rcv_issCond="";
	if($db_type==2 && count($finish_rcv_iss_arr)>999)
	{
		$all_finish_rcv_iss_arr_chunk=array_chunk($finish_rcv_iss_arr,999) ;
		foreach($all_finish_rcv_iss_arr_chunk as $chunk_arr)
		{
			$chunk_arr_value=implode(",",$chunk_arr);
			$finish_rcv_issCond.=" a.barcode_no in($chunk_arr_value) or ";
		}

		$all_finish_rcv_iss_arr_cond.=" and (".chop($finish_rcv_issCond,'or ').")";
	}
	else
	{
		$all_finish_rcv_iss_arr_cond=" and a.barcode_no in($finish_rcv_iss_ids)";
	}


	$sql_finish_issue_arr=sql_select("SELECT a.id, a.barcode_no, a.dtls_id, a.roll_id, a.rate, a.qnty, a.po_breakdown_id, a.booking_without_order, b.trans_id,a.reprocess,
	a.prev_reprocess, b.floor, b.room, b.rack_no, b.shelf_no, b.bin_box from pro_roll_details a, inv_finish_fabric_issue_dtls b 
	where a.dtls_id=b.id and a.entry_form=71 $all_finish_rcv_iss_arr_cond  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	foreach($sql_finish_issue_arr as $row)
	{
		$issued_data_arr[$row[csf('barcode_no')]]['qnty']+=$row[csf("qnty")];
	}

	foreach($sql_finish_rcv_iss as $row)
	{
		$issue_to_cut_arr[$row[csf("booking_no_id")]][$row[csf("color_id")]]+=$issued_data_arr[$row[csf('barcode_no')]]['qnty'];
	}

	$sql_cutting_issue=sql_select("SELECT c.id as booking_no_id,d.color_id,sum(b.issue_qnty) as issue_qty from inv_issue_master a, inv_finish_fabric_issue_dtls b, pro_batch_create_mst d, wo_non_ord_samp_booking_mst c where a.id=b.mst_id and b.batch_id=d.id and a.entry_form=18  and d.booking_without_order=1  and d.booking_no=c.booking_no $c_wo_no_date_cond group by c.id, d.color_id");
	foreach($sql_cutting_issue as $row)
	{
		$issue_to_cut_arr[$row[csf("booking_no_id")]][$row[csf("color_id")]]+=$row[csf("issue_qty")];
	}
	
	/*echo "select a.id as booking_id, a.booking_no, a.booking_no_prefix_num,$select_year as wo_year,a.is_short,a.po_break_down_id,a.fabric_source,a.is_approved,a.job_no,a.buyer_id,a.company_id,a.supplier_id,a.item_category,a.is_approved,b.sample_type,b.grey_fabric as grey_fabric_qnty, b.finish_fabric as finish_fabric_qty, b.fabric_color,b.fabric_description 
	from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b 
	where a.booking_no=b.booking_no and a.item_category in(2,13) and a.company_id like '$cbo_company_name' and a.buyer_id like '$buyer_name'  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $str_cond  $wo_cond order by a.booking_no";*/
	$sample_type_id=trim(str_replace("'","",$cbo_sample_type));
	if($sample_type_id!=0) $sample_type_cond=" and b.sample_type='$sample_type_id'"; else $sample_type_cond="";
	
	
	$sql=sql_select("select a.id as booking_id, a.booking_no, a.booking_no_prefix_num,$select_year as wo_year, a.is_short, a.po_break_down_id, a.fabric_source, a.is_approved, a.job_no, a.buyer_id, a.company_id, a.supplier_id, a.item_category, a.is_approved, b.sample_type, b.grey_fabric as grey_fabric_qnty, b.finish_fabric as finish_fabric_qty, b.fabric_color, b.fabric_description,b.style_des 
	from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b 
	where a.booking_no=b.booking_no and a.item_category in(2,13) and a.company_id like '$cbo_company_name' and a.buyer_id like '$buyer_name'  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $str_cond $wo_cond $sample_type_cond order by a.booking_no");
	
	foreach($sql as $row)
	{
		$result_mst_array[$row[csf("booking_id")]]["booking_id"]=$row[csf("booking_id")];
		$result_mst_array[$row[csf("booking_id")]]["style_des"]=$row[csf("style_des")];
		$result_mst_array[$row[csf("booking_id")]]["booking_no"]=$row[csf("booking_no")];
		$result_mst_array[$row[csf("booking_id")]]["booking_no_prefix_num"]=$row[csf("booking_no_prefix_num")];
		$result_mst_array[$row[csf("booking_id")]]["wo_year"]=$row[csf("wo_year")];
		$result_mst_array[$row[csf("booking_id")]]["is_short"]=$row[csf("is_short")];
		$result_mst_array[$row[csf("booking_id")]]["po_id"]=$row[csf("po_break_down_id")];
		$result_mst_array[$row[csf("booking_id")]]["job_no"]=$row[csf("job_no")];
		//$result_mst_array[$row[csf("booking_id")]]["fabric_source"]=$row[csf("fabric_source")];
		$result_mst_array[$row[csf("booking_id")]]["company_id"]=$row[csf("company_id")];
		$result_mst_array[$row[csf("booking_id")]]["supplier_id"]=$row[csf("supplier_id")];
		$result_mst_array[$row[csf("booking_id")]]["item_category"]=$row[csf("item_category")];
		$result_mst_array[$row[csf("booking_id")]]["is_approved"]=$row[csf("is_approved")];
		$result_mst_array[$row[csf("booking_id")]]["sample_type"].=$row[csf("sample_type")].",";
		$result_mst_array[$row[csf("booking_id")]]["buyer_id"]=$row[csf("buyer_id")];
		$result_mst_array[$row[csf("booking_id")]]["grey_fabric_qnty"]+=$row[csf("grey_fabric_qnty")];
		
		$result_dtls_array[$row[csf("booking_id")]][$row[csf("fabric_color")]]["fabric_source"]=$row[csf("fabric_source")];
		$result_dtls_array[$row[csf("booking_id")]][$row[csf("fabric_color")]]["fabric_description"][]=$row[csf("fabric_description")];
		$result_dtls_array[$row[csf("booking_id")]][$row[csf("fabric_color")]]["fabric_color"]=$row[csf("fabric_color")];
		$result_dtls_array[$row[csf("booking_id")]][$row[csf("fabric_color")]]["finish_fabric_qty"]+=$row[csf("finish_fabric_qty")];
		$result_dtls_array[$row[csf("booking_id")]][$row[csf("fabric_color")]]["batchQnty"]+=$batch_qty_buyer_arr[$row[csf("booking_id")]][$row[csf("buyer_id")]]['batch_buyer_qnty'];;

		$arry_fab_color[$row[csf("booking_id")]]["fabric_color"]=$row[csf("fabric_color")];

		$buyer_wise_result[$row[csf("buyer_id")]]["buyer_id"]=$row[csf("buyer_id")];
		$buyer_wise_result[$row[csf("buyer_id")]]["grey_fabric_qnty"] +=$row[csf("grey_fabric_qnty")];
		$buyer_wise_result[$row[csf("buyer_id")]]["finish_fabric_qty"] +=$row[csf("finish_fabric_qty")];
		if(!in_array($row[csf("booking_id")],$temp_book_arr))
		{
			$temp_book_arr[]=$row[csf("booking_id")];
			$buyer_wise_result[$row[csf("buyer_id")]]["yarn_issue"] +=$yarn_issue_arr[$row[csf("booking_id")]]-$yarn_issue_rtn_arr[$row[csf("booking_id")]];
			$buyer_wise_result[$row[csf("buyer_id")]]["knitting_total"] +=($grey_knit_production_arr[$row[csf("booking_id")]]);
			//+$grey_knit_receive_arr[$row[csf("booking_id")]]
			$buyer_wise_result[$row[csf("buyer_id")]]["grey_issue"] +=$grey_issue_arr[$row[csf("booking_id")]];
			$buyer_wise_result[$row[csf("buyer_id")]]["batch_qty"] +=$batch_qty_arr[$row[csf("booking_id")]]['batch_qnty'];
			$buyer_wise_result[$row[csf("buyer_id")]]["batch_buyer_qnty"] +=$batch_qty_buyer_arr[$row[csf("booking_id")]][$row[csf("buyer_id")]]['batch_buyer_qnty'];
			
			$gt_yarn_issue+=$yarn_issue_arr[$row[csf("booking_id")]]-$yarn_issue_rtn_arr[$row[csf("booking_id")]];
			//$gt_grey_available+=($grey_knit_production_arr[$row[csf("booking_id")]]+$grey_knit_receive_arr[$row[csf("booking_id")]]);
			$gt_grey_available+=($grey_knit_receive_arr[$row[csf("booking_id")]]);
			$gt_batch_qty +=$batch_qty_buyer_arr[$row[csf("booking_id")]][$row[csf("buyer_id")]]['batch_buyer_qnty'];//$batch_qty_arr[$row[csf("booking_id")]]['batch_qnty'];
			$gt_batch_buyer_qty +=$batch_qty_buyer_arr[$row[csf("booking_id")]][$row[csf("buyer_id")]]['batch_buyer_qnty'];
			//$gt_batch_buyer_qty +=$batch_qty_arr[$row[csf("booking_id")]][$row[csf("buyer_id")]]['batch_qnty'];
			
		}
		
		$batch_id_arr=array_unique(explode(",",chop($batch_qty_arr_check[$row[csf("booking_id")]][$row[csf("fabric_color")]]['batch_id']," , ")));
		foreach($batch_id_arr as $bat_id)
		{
			if(str_replace("'","",$dyeing_check_arr[$bat_id])>0)
			{
				$buyer_wise_result[$row[csf("buyer_id")]]["dyeing_qty"] +=$batch_qty_arr[$row[csf("booking_id")]][$row[csf("fabric_color")]][$bat_id]['batch_qnty'];
				$gt_dying_qty+=$batch_qty_arr[$row[csf("booking_id")]][$row[csf("fabric_color")]][$bat_id]['batch_qnty'];
			}
		}
		/*$dtls_batch_qty=$batch_qty_arr_check[$row[csf("booking_id")]][$row[csf("fabric_color")]]['batch_qnty'];
		$dy_check_id=$dyeing_check_arr[$batch_qty_arr_check[$row[csf("booking_id")]][$row[csf("fabric_color")]]['batch_id']];
		if($dy_check_id!="") 
		{
			$buyer_wise_result[$row[csf("buyer_id")]]["dyeing_qty"] +=$dtls_batch_qty;
			$gt_dying_qty+=$dtls_batch_qty;
		}*/
		//echo $finish_receive_arr[$row[csf("booking_id")]][$row[csf("fabric_color")]].'='.$finish_product_arr[$row[csf("booking_id")]][$row[csf("fabric_color")]];
		//echo $row[csf("booking_id")].'='.$row[csf("fabric_color")].'GG,';
		
		$buyer_wise_result[$row[csf("buyer_id")]]["fin_total_available"] +=$finish_receive_arr[$row[csf("booking_id")]][$row[csf("fabric_color")]]+$finish_product_arr[$row[csf("booking_id")]][$row[csf("fabric_color")]];
		//+$finish_receive_arr[$row[csf("booking_id")]][$row[csf("fabric_color")]]
		$buyer_wise_result[$row[csf("buyer_id")]]["issue_to_cut"] +=$issue_to_cut_arr[$row[csf("booking_id")]][$row[csf("fabric_color")]];
		
		
		$gt_yarn_grey_required+=$row[csf("grey_fabric_qnty")];
		
		/*$g_dtls_batch_qty=$batch_qty_arr_check[$row[csf("booking_id")]][$row[csf("fabric_color")]]['batch_qnty'];
		$g_dy_check_id=$dyeing_check_arr[$batch_qty_arr_check[$row[csf("booking_id")]][$row[csf("fabric_color")]]['batch_id']];
		if($g_dy_check_id!="")
		{
			$gt_dying_qty+=$g_dtls_batch_qty; 
		}*/
		//echo $finish_receive_arr[$row[csf("booking_id")]][$row[csf("fabric_color")]].'='.$finish_receive_arr[$row[csf("booking_id")]][$row[csf("fabric_color")]].'<br>';
		
		$gt_finish_requir+=$row[csf("finish_fabric_qty")];
		$gt_finish_available+=($finish_receive_arr[$row[csf("booking_id")]][$row[csf("fabric_color")]]+$finish_product_arr[$row[csf("booking_id")]][$row[csf("fabric_color")]]);
		$gt_issue_cutting+=$issue_to_cut_arr[$row[csf("booking_id")]][$row[csf("fabric_color")]];

		$all_color[$row[csf("fabric_color")]]=$row[csf("fabric_color")];
		
	}

	$all_color =array_filter($all_color);
	if(count($all_color)>0)
	{
		$all_color_ids = implode(",", $all_color);
		$all_color_ids_cond=""; $colorCond="";
		if($db_type==2 && count($all_color)>999)
		{
			$all_color_chunk=array_chunk($all_color,999) ;
			foreach($all_color_chunk as $chunk_arr)
			{
				$chunk_arr_value=implode(",",$chunk_arr);
				$colorCond.="  id in($chunk_arr_value) or ";
			}
			$all_color_ids_cond.=" and (".chop($colorCond,'or ').")";
		}
		else
		{
			$all_color_ids_cond=" and id in($all_color_ids)";
		}
	}

	$color_array=return_library_array( "select id, color_name from lib_color where status_active=1 $all_color_ids_cond", "id", "color_name"  );
		//var_dump($buyer_wise_result);die
		//var_dump($result_dtls_array);die;
		
		//echo $sql;die;
		
		//$result=sql_select($sql);

	//echo $sql_batch_qty;die;
	//var_dump($result);die;
	?>
    <div style="width:2250px; margin-bottom:10px;">
    <div style="float:left; width:320px; margin-bottom:10px;">
    <table class="rpt_table" border="1" rules="all" width="300" cellpadding="0" cellspacing="0">
        <thead>
        	<tr>
            	<th colspan="4">Summary</th>
            </tr>
            <tr>
            	<th width="30">Sl</th>
            	<th width="120">Particulars</th>
                <th width="80">Quantity</th>
                <th width="70">%</th>
            </tr>
        </thead>
        <tbody>
        	<tr>
            	<td>1</td>
            	<td>Yarn Required</td>
                <td align="right"><? echo number_format($gt_yarn_grey_required,2); ?></td>
                <td align="right"><? $yarn_req_parcent=(($gt_yarn_grey_required/$gt_yarn_grey_required)*100); echo number_format($yarn_req_parcent,2)."%"; ?></td>
            </tr>
            <tr>
            	<td>2</td>
            	<td>Yarn Issued</td>
                <td align="right"><? echo number_format($gt_yarn_issue,2); ?></td>
                <td align="right"><? $yarn_issue_parcent=(($gt_yarn_issue/$gt_yarn_grey_required)*100); echo number_format($yarn_issue_parcent,2)."%"; ?></td>
            </tr>
            <tr>
            	<td>3</td>
            	<td >Issue Balance</td>
                <td align="right"><? $gt_issue_balance=$gt_yarn_grey_required-$gt_yarn_issue; echo number_format($gt_issue_balance,2); ?></td>
                <td align="right"><? $issue_balance_parcentage=(($gt_issue_balance/$gt_yarn_grey_required)*100); echo number_format($issue_balance_parcentage,2)."%";  ?></td>
            </tr>
            <tr>
            	<td>4</td>
            	<td>Grey Required</td>
                <td align="right"><? echo number_format($gt_yarn_grey_required,2); ?></td>
                <td align="right"><? $grey_req_parcent=(($gt_yarn_grey_required/$gt_yarn_grey_required)*100); echo number_format($grey_req_parcent,2)."%"; ?></td>
            </tr>
            <tr>
            	<td>5</td>
            	<td>Grey Available</td>
                <td align="right"><? echo number_format($gt_grey_available,2); ?></td>
                <td align="right"><? $grey_available_parcentage=(($gt_grey_available/$gt_yarn_grey_required)*100); echo number_format($grey_available_parcentage,2)."%";  ?></td>
            </tr>
             <tr>
            	<td>6</td>
            	<td>Grey Balance</td>
                <td align="right"><? $gt_grey_balance=$gt_yarn_grey_required-$gt_grey_available;  echo number_format($gt_grey_balance,2); ?></td>
                <td align="right"><? $grey_balance_parcentage=(($gt_grey_balance/$gt_yarn_grey_required)*100); echo number_format($grey_balance_parcentage,2)."%";  ?></td>
            </tr>
            <tr>
            	<td>7</td>
            	<td>Grey to Dyeing</td>
                <td align="right"><? echo number_format($gt_dying_qty,2); ?></td>
                <td align="right"><? $grey_dying_parcentage=(($gt_dying_qty/$gt_yarn_grey_required)*100); echo number_format($grey_dying_parcentage,2)."%";  ?></td>
            </tr>
            <tr>
            	<td>8</td>
            	<td>Batch Qty.</td>
                <td align="right"><? echo number_format($gt_batch_qty,2); ?></td>
                <td align="right"><? $grey_batch_parcentage=(($gt_batch_qty/$gt_yarn_grey_required)*100); echo number_format($grey_batch_parcentage,2)."%";  ?></td>
            </tr>
            <tr>
            	<td>9</td>
            	<td>Finish Fabric Required</td>
                <td align="right"><? echo number_format($gt_finish_requir,2); ?></td>
                <td align="right"><? $gt_finish_requir_parcentage=(($gt_finish_requir/$gt_finish_requir)*100); echo number_format($gt_finish_requir_parcentage,2)."%";  ?></td>
            </tr>
            <tr>
            	<td>10</td>
            	<td>Finish Fabric Available</td>
                <td align="right"><? echo number_format($gt_finish_available,2); ?></td>
                <td align="right"><? $finish_available_parcentage=(($gt_finish_available/$gt_finish_requir)*100); echo number_format($finish_available_parcentage,2)."%";  ?></td>
            </tr>
            <tr>
            	<td>11</td>
            	<td>Finish Fabric Balance</td>
                <td align="right"><? $gt_finish_balance=$gt_finish_requir-$gt_finish_available;  echo number_format($gt_finish_balance,2); ?></td>
                <td align="right"><? $finish_balance_parcentage=(($gt_finish_balance/$gt_finish_requir)*100); echo number_format($finish_balance_parcentage,2)."%";  ?></td>
            </tr>
            <tr>
            	<td>12</td>
            	<td>Issue to Cutting</td>
                <td align="right"><? echo number_format($gt_issue_cutting,2); ?></td>
                <td align="right"><? $finish_issue_cutting_parcentage=(($gt_issue_cutting/$gt_finish_requir)*100); echo number_format($finish_issue_cutting_parcentage,2)."%";  ?></td>
            </tr>
            <tr>
            	<td>13</td>
            	<td>Issue Balance</td>
                <td align="right"><? $gt_finish_issue_cut_balance=$gt_finish_requir-$gt_issue_cutting;  echo number_format($gt_finish_issue_cut_balance,2); ?></td>
                <td align="right"><? $finish_issue_cut_bal_parcentage=(($gt_finish_issue_cut_balance/$gt_finish_requir)*100); echo number_format($finish_issue_cut_bal_parcentage,2)."%";  ?></td>
            </tr>
        </tbody>
    </table>
    </div>
    
    <div style="float:left; widows:1920px; margin-bottom:10px;">
	<table class="rpt_table" border="1" rules="all" width="1600" cellpadding="0" cellspacing="0">
		<thead>
        	<tr>
				<th width="40">SL</th>
                <th width="80">Buyer Name</th>
				<th width="100">Grey Req.</th>
                <th width="100">Yarn Issue</th>
                <th width="100">Yarn Balance</th>
				<th width="100">Knitting Total</th>
				<th width="100">Knit Balance</th>
				<th width="100">Grey Issue</th>
				<th width="100">Batch Qnty</th>
				<th width="100">Batch Balance</th>
				<th width="100">Total Dyeing</th>
				<th width="100">Dyeing Balance</th>
				<th width="100">Fin. Fab Req.</th>
                <th width="100">Fin. Fab total</th>
                <th width="100">Fin. Fab Balance</th>
				<th>Issue to Cutting </th>
			</tr>
        </thead>
        <tbody>
        <?
		$p=1;
		foreach($buyer_wise_result as $row_result)
		{
			if ($p%2==0)
			$bgcolor="#E9F3FF";
			else
			$bgcolor="#FFFFFF";
			?>
            
        	<tr bgcolor="<? echo $bgcolor; ?>">
				<td><? echo $p; ?></td>
                <td><? echo $buyer_short_name_library[$row_result["buyer_id"]]; ?></td>
				<td align="right"><? echo number_format($row_result["grey_fabric_qnty"],2); $buyer_tot_feb_req+=$row_result["grey_fabric_qnty"];?></td>
                <td align="right"><? echo number_format($row_result["yarn_issue"],2);  $buyer_tot_yarn_issue+=$row_result["yarn_issue"]; ?></td>
                <td align="right"><? echo number_format(($row_result["grey_fabric_qnty"]-$row_result["yarn_issue"]),2); $buyer_tot_yarn_balance+=($row_result["grey_fabric_qnty"]-$row_result["yarn_issue"]); ?></td>
				<td align="right"><? echo number_format($row_result["knitting_total"],2); $buyer_tot_grey_knitting+=$row_result["knitting_total"]; ?></td>
				<td align="right"><? echo number_format(($row_result["grey_fabric_qnty"]-$row_result["knitting_total"]),2); $buyer_tot_grey_knitting_bal+=($row_result["grey_fabric_qnty"]-$row_result["knitting_total"]); ?></td>
				<td align="right"><? echo number_format($row_result["grey_issue"],2); $buyer_tot_grey_issue+=$row_result["grey_issue"];  ?></td>
				<td align="right"><? echo number_format($row_result["batch_buyer_qnty"],2); $buyer_tot_batch_qty+=$row_result["batch_buyer_qnty"]; ?></td>
				<td align="right"><? echo number_format(($row_result["grey_fabric_qnty"]-$row_result["batch_buyer_qnty"]),2); $buyer_tot_batch_balance+=($row_result["grey_fabric_qnty"]-$row_result["batch_buyer_qnty"]); ?></td>
				<td align="right"><? echo number_format($row_result["dyeing_qty"],2);  $buyer_tot_dyeing_qty+=$row_result["dyeing_qty"];  ?></td>
				<td align="right"><? echo number_format(($row_result["grey_fabric_qnty"]-$row_result["dyeing_qty"]),2); $buyer_tot_dyeing_balance+=($row_result["grey_fabric_qnty"]-$row_result["dyeing_qty"]); ?></td>
				<td align="right"><? echo number_format($row_result["finish_fabric_qty"],2);  $buyer_tot_finish_req_qty+=$row_result["finish_fabric_qty"];  ?></td>
                <td align="right"><? echo number_format($row_result["fin_total_available"],2); $buyer_tot_finish_abable_qty+=$row_result["fin_total_available"];  ?></td>
                <td align="right"><? echo number_format(($row_result["finish_fabric_qty"]-$row_result["fin_total_available"]),2); $buyer_tot_finish_balance+=($row_result["finish_fabric_qty"]-$row_result["fin_total_available"]);  ?></td>
				<td align="right"><? echo number_format($row_result["issue_to_cut"],2); $buyer_tot_cutting_qty+=$row_result["issue_to_cut"]; ?> </td>
			</tr>
            <?
			$p++;
		}
		?>
        </tbody>
        <tfoot>
        	<tr>
                <th colspan="2">Total:</th>
				<th align="right"><? echo number_format($buyer_tot_feb_req,2); ?></th>
                <th align="right"><? echo number_format($buyer_tot_yarn_issue,2); ?></th>
                <th align="right"><? echo number_format($buyer_tot_yarn_balance,2); ?></th>
				<th align="right"><? echo number_format($buyer_tot_grey_knitting,2); ?></th>
				<th align="right"><? echo number_format($buyer_tot_grey_knitting_bal,2); ?></th>
				<th align="right"><? echo number_format($buyer_tot_grey_issue,2); ?></th>
				<th align="right"><? echo number_format($buyer_tot_batch_qty,2); ?></th>
				<th align="right"><? echo number_format($buyer_tot_batch_balance,2); ?></th>
				<th align="right"><? echo number_format($buyer_tot_dyeing_qty,2); ?></th>
				<th align="right"><? echo number_format($buyer_tot_dyeing_balance,2); ?></th>
				<th align="right"><? echo number_format($buyer_tot_finish_req_qty,2); ?></th>
                <th align="right"><? echo number_format($buyer_tot_finish_abable_qty,2); ?></th>
                <th align="right"><? echo number_format($buyer_tot_finish_balance,2); ?></th>
				<th align="right"><? echo number_format($buyer_tot_cutting_qty,2); ?> </th>
			</tr>
        </tfoot>
    </table>
    </div>
    </div>
    <br />
	<table class="rpt_table" border="1" rules="all" width="2570" cellpadding="0" cellspacing="0">
		<thead>
			<tr>
				<th colspan="6">Booking Details</th>
				<th colspan="3">Yarn Details</th>
				<th colspan="6">Grey Fabric Status</th>
				<th colspan="10">Finish Fabric Status</th>
			</tr>
			<tr>
				<th width="40">SL</th>
				<th width="65">Booking Year</th>
                <th width="65">Booking No</th>
				<th width="140">Sample Type</th>
                <th width="80">Buyer Name</th>

                <th width="100">Style Description</th>


				<th width="110">Required<br/><font style="font-size:9px; font-weight:100">(As Per Booking)</font></th>
                <th width="110">Issued</th>
                <th width="110">Balance<br/><font style="font-size:9px; font-weight:100">Grey Req-Yarn Issue</font></th>
				<th width="110">Knitted Production</th>
                <th width="100">Knitted Receive</th>
                <th width="100">Total Available</th>
				<th width="110">Knit Balance</th>
				<th width="110">Grey Issue</th>
				<th width="110">Batch Qnty</th>
				<th width="110">Fabric Color</th>
				<th width="110">Required</th>
				<th width="110">Dyeing Qnty</th>
                <th width="110">Fab Production</th>
				<th width="110">Fab Receive</th>
                <th width="110">Total Available</th>
				<th width="110">Balance</th>
				<th width="110">Issue to Cutting</th>
				<th width="110">Fabrication</th>
				<th>Fabric Source</th>
			</tr>
		</thead>
	</table>
	<div style="width:2590px; overflow-y:scroll; max-height:300px" id="scroll_body">
    <table class="rpt_table" border="1" rules="all" width="2570" cellpadding="0" cellspacing="0" id="table_body">
        <tbody>
        <?
		$i=1;
		foreach($result_mst_array as $row)
		{
			if ($i%2==0)
			$bgcolor="#E9F3FF";
			else
			$bgcolor="#FFFFFF";
			
			foreach($format_ids as $row_id)
			{
			
				if($row_id==34)
				{ 
				
				 $variable="<input type='button' value='PB' onClick=\"generate_order_report('".$row[('booking_no')]."','".$row[('company_id')]."','".$row[('is_approved')]."','".$row[('item_category')]."','show_fabric_booking_report','".$i."')\" style='width:40px;' class='formbutton' name='print_pb' id='print_pb' />";
				?>
				
				<?
					
				}
				if($row_id==35)
				{ 
				
				
				 $variable="<input type='button' value='PB 2' onClick=\"generate_order_report('".$row[('booking_no')]."','".$row[('company_id')]."','".$row[('is_approved')]."','".$row[('item_category')]."','show_fabric_booking_report2','".$i."')\" style='width:40px;' name='print_pb' id='print_pb' class='formbutton' />";
				?>
				  
		  
				
			   <? }
			   if($row_id==36)
				{ 
				 $variable="<input type='button' value='P A' onClick=\"generate_order_report('".$row[('booking_no')]."','".$row[('company_id')]."','".$row[('is_approved')]."','".$row[('item_category')]."','show_fabric_booking_report3','".$i."')\" style='width:40px;' name='print_pa' id='print_pa' class='formbutton' />";
				?>
				
				
			   <? }
			   
				if($row_id==37)
				{ 
				 $variable="<input type='button' value='AKH' onClick=\"generate_order_report('".$row[('booking_no')]."','".$row[('company_id')]."','".$row[('is_approved')]."','".$row[('item_category')]."','show_fabric_booking_report4','".$i."')\" style='width:40px;' name='print_akh' id='print_akh' class='formbutton' />";
				?>
				
				
			   <? }
			   
			   
			   
			}
			?>
        	<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
				<td width="40"><? echo $i; ?></td>
				<td width="65" align="center"><p><? echo $row[("wo_year")]; ?></p></td>
                <? //$main_booking.="<a href='##' style='color:#000' onclick=\"generate_worder_report('$row_wo[is_short]','$row_wo[booking_no]','$row_wo[company_id]','$row[po_id]','$row_wo[item_category]','$row_wo[fabric_source]','$row_wo[job_no]','$row_wo[is_approved]')\"> ?>
                <td width="65" align="center"><p><!--<a href='##' style='color:#000' onclick="generate_order_report('<? //echo $row['booking_no'];?>','<? //echo $row['company_id']; ?>','<? //echo $row['is_approved'];?>')"><? //echo $row[("booking_no_prefix_num")]; ?></a>--> <? echo $row[("booking_no_prefix_num")]; ?> <a href='##' style='color:#000'><? echo $variable; ?></a></p></td>
				<td width="140"><p>
				<?
					$sample_arr=array_unique(explode(",",substr($row[("sample_type")], 0, -1))); 
					$p=1;
					foreach($sample_arr as $row_style)
					{
						if($p!=1) echo "<br>";
						echo $sample_array[$row_style];
						$p++;
					}
				?>
                </p></td>
                <td width="80"><p><? echo $buyer_short_name_library[$row[("buyer_id")]]; ?></p></td>
                <td width="100"><p><? echo $row[("style_des")]; ?></p></td>
				<td width="110" align="right"><p><? echo number_format($row[("grey_fabric_qnty")],2); $dtls_tot_gery_req+=$row[("grey_fabric_qnty")]; ?></p></td>
                <td width="110" align="right"><p>
                <a href="##" onclick="openmypage('<? echo $row[("booking_id")]; ?>','yarn_issue')">
					<?
						$net_yarn_issue=$yarn_issue_arr[$row[("booking_id")]]-$yarn_issue_rtn_arr[$row[("booking_id")]];
                    	echo number_format($net_yarn_issue,2); $dtls_tot_yarn_issue+=$net_yarn_issue; 
                    ?>
                </a>
                </p></td>
                <td width="110" align="right"><p><? $yarn_balance=$row[("grey_fabric_qnty")]-$net_yarn_issue; echo number_format($yarn_balance,2); $dtls_tot_yarn_balance += $yarn_balance; ?></p></td>
				<td width="110" title="BookingID=<? echo $row[("booking_id")].',Qty='.$grey_knit_production_arr[$row[("booking_id")]];?>" align="right"><p>
                <a  href="##" onclick="openmypage('<? echo $row[("booking_id")]; ?>','grey_receive','')">
					<?
                     	echo number_format($grey_knit_production_arr[$row[("booking_id")]],2); $dtls_tot_gery_knit_product+=$grey_knit_production_arr[$row[("booking_id")]];  
                    ?>
                </a>
                </p></td>
                <td width="100" align="right"><p><? echo number_format($grey_knit_receive_arr[$row[("booking_id")]],2); $dtls_tot_gery_knit_receive+=$grey_knit_receive_arr[$row[("booking_id")]];  ?></p></td>
                <td width="100" align="right"><p><? 
                //$grey_total_available=($grey_knit_production_arr[$row[("booking_id")]]+$grey_knit_receive_arr[$row[("booking_id")]]);  
                $grey_total_available=$grey_knit_receive_arr[$row[("booking_id")]];
               
                echo number_format($grey_total_available,2);  
                $dtls_tot_gery_available+=$grey_total_available;   ?></p></td>
				<td width="110" align="right"><p><? $grey_balance=$row[("grey_fabric_qnty")]-$grey_knit_production_arr[$row[("booking_id")]]; echo number_format($grey_balance,2); $dtls_tot_gery_balance +=$grey_balance; ?></p></td>
				<td width="110" align="right"><p>
                <a  href="##" onclick="openmypage('<? echo $row[("booking_id")]; ?>','grey_issue','')">
					<? 
                    	echo number_format($grey_issue_arr[$row[("booking_id")]],2); $dtls_tot_gery_issue+=$grey_issue_arr[$row[("booking_id")]];  
                    ?>
                </a>
                </p></td>
				
                <?
				//details_part start here
				$m=1;

				foreach($result_dtls_array[$row[("booking_id")]] as $dts_row)
				{
					if($m==1)
					{
						?>
						<td width="110" align="right"><p><? 
						$batch_qt=$batch_qty_arr[$row[("booking_id")]]['batch_qnty']; //$row[("booking_id")][$row[csf("fabric_color")]]["fabric_color"];
						//echo $row[("booking_id")].'='.$row[("fabric_color")].'='.$bat_id;
						$batch_qt=$batch_qty_arr[$row[("booking_id")]][$arry_fab_color[$row[("booking_id")]]["fabric_color"]][$bat_id]['batch_qnty'];
						//echo $arry_fab_color[$row[("booking_id")]]["fabric_color"];
						//echo $row[("booking_id")]."=".$row[("fabric_color")]."=".$bat_id;
						echo number_format($dts_row[("batchQnty")],2);
						//$dtls_tot_batch_qty_only +=$gt_dying_qty; 
						$dtls_tot_batch_qty_only +=$dts_row[("batchQnty")]; 
						$dtls_tot_batch_qty +=$batch_qt;  ?></p></td>

						<td width="110"><p><? echo $color_array[$dts_row[("fabric_color")]]; ?></p> </td>
						<td width="110" align="right"><p><? echo number_format($dts_row[("finish_fabric_qty")],2); $dtls_tot_fin_req_qty +=$dts_row[("finish_fabric_qty")]; ?></p></td>
						<td width="110" align="right">
						<p><?
						/*$dtls_batch_qty=$batch_qty_arr_check[$row[("booking_id")]][$dts_row[("fabric_color")]]['batch_qnty'];
						$dy_check_id=$dyeing_check_arr[$batch_qty_arr_check[$row[("booking_id")]][$dts_row[("fabric_color")]]['batch_id']];
						if($dy_check_id!="")*/  
						$dtls_batch_qty=0;
						$batch_id_arr=array_unique(explode(",",chop($batch_qty_arr_check[$row[("booking_id")]][$dts_row[("fabric_color")]]['batch_id']," , ")));
						foreach($batch_id_arr as $bat_id)
						{
							if(str_replace("'","",$dyeing_check_arr[$bat_id])>0)
							{
								$dtls_batch_qty +=$batch_qty_arr[$row[("booking_id")]][$dts_row[("fabric_color")]][$bat_id]['batch_qnty'];
							}
						}
						echo number_format($dtls_batch_qty,2); $dtls_tot_batch_qty +=$dtls_batch_qty;
						 ?></p></td>
						<td width="110" align="right"><p>
                        <a  href="##" onclick="open_febric_receive_status_color_wise_popup('<? echo $row[("booking_id")]; ?>','finish_feb_pord','<? echo $dts_row[("fabric_color")]; ?>')">
						<? 
						//echo $row[("booking_id")];
							echo number_format($finish_product_arr[$row[("booking_id")]][$dts_row[("fabric_color")]],2); $dtls_tot_fin_prod_qty +=$finish_product_arr[$row[("booking_id")]][$dts_row[("fabric_color")]];  
						?>
                        </a>
                        </p></td>
						<td width="110" align="right">
						<p>
	                        <a  href="##" onclick="open_febric_receive_status_color_wise_popup('<? echo $row[("booking_id")]; ?>','fabric_purchase','<? echo $dts_row[("fabric_color")]; ?>')">
							<? 
							/*echo number_format($finish_receive_arr[$row[("booking_id")]][$dts_row[("fabric_color")]]+$finish_product_arr[$row[("booking_id")]][$dts_row[("fabric_color")]],2);  
							$dtls_tot_fin_receive_qty +=$finish_receive_arr[$row[("booking_id")]][$dts_row[("fabric_color")]]+$finish_product_arr[$row[("booking_id")]][$dts_row[("fabric_color")]]; */
							echo number_format($finish_receive_arr[$row[("booking_id")]][$dts_row[("fabric_color")]],2);  
							$dtls_tot_fin_receive_qty +=$finish_receive_arr[$row[("booking_id")]][$dts_row[("fabric_color")]];  
							?>
	                        </a>
                        </p>
                        </td>
						<td width="110" align="right">
						<p>
						<? 
							//$finish_total_available=($finish_receive_arr[$row[("booking_id")]][$dts_row[("fabric_color")]]+$finish_product_arr[$row[("booking_id")]][$dts_row[("fabric_color")]]); 
							$finish_total_available=($finish_receive_arr[$row[("booking_id")]][$dts_row[("fabric_color")]]); 
							//echo number_format($finish_total_available,2); 
							echo number_format($finish_receive_arr[$row[("booking_id")]][$dts_row[("fabric_color")]],2);  
							$dtls_tot_fin_avabile_qty +=$finish_total_available; ?>
								
							</p>
						</td>
						<td width="110" align="right"><p><? $finish_balance=$dts_row[("finish_fabric_qty")]-$finish_total_available; echo number_format($finish_balance,2); $dtls_tot_fin_balance +=$finish_balance;  ?></p></td>
						<td width="110" align="right">
						<p>
	                        <a  href="##" onclick="open_febric_receive_status_color_wise_popup('<? echo $row[("booking_id")]; ?>','issue_to_cut','<? echo $dts_row[("fabric_color")]; ?>')">
							<? 
							//echo $row[("booking_id")];
							echo number_format($issue_to_cut_arr[$row[("booking_id")]][$dts_row[("fabric_color")]],2); $dtls_tot_cutting_qty +=$issue_to_cut_arr[$row[("booking_id")]][$dts_row[("fabric_color")]]; 
							?>
	                        </a>
                        </p>
                        </td>
                        <td width="110"><? echo implode(',',$dts_row['fabric_description']); ?></td>
                        <td>
						<? 
							echo $fabric_source[$dts_row["fabric_source"]];
						?>
                        </td>
					</tr>
					<?
					}
					else
					{
						?>
                        <tr>
                            <td width="40">&nbsp;</td>
                            <td width="65">&nbsp;</td>
                            <td width="65">&nbsp;</td>
                            <td width="140">&nbsp;</td>
                            <td width="80">&nbsp;</td>
                            <td width="100">&nbsp;</td>

                            <td width="110" align="right">&nbsp;</td>
                            <td width="110" align="right">&nbsp;</td>
                            <td width="110" align="right">&nbsp;</td>
                            <td width="110" align="right">&nbsp;</td>
                            <td width="100" align="right">&nbsp;</td>
                            <td width="100" align="right">&nbsp;</td>
                            <td width="110" align="right">&nbsp;</td>
                            <td width="110" align="right">&nbsp;</td>
                            <td width="110" align="right">&nbsp;</td>
                            <td width="110"><p><? echo $color_array[$dts_row[("fabric_color")]]; ?></p> </td>
                            <td width="110" align="right"><p><? echo number_format($dts_row[("finish_fabric_qty")],2); $dtls_tot_fin_req_qty +=$dts_row[("finish_fabric_qty")]; ?></p></td>
                            <td width="110" align="right">
                            <p><?
							$dtls_batch_qty=$batch_qty_arr_check[$row[("booking_id")]][$dts_row[("fabric_color")]]['batch_qnty'];
							$dy_check_id=$dyeing_check_arr[$batch_qty_arr_check[$row[("booking_id")]][$dts_row[("fabric_color")]]['batch_id']];
							if($dy_check_id!="")  echo number_format($dtls_batch_qty,2);   $dtls_tot_batch_qty +=$dtls_batch_qty; 
                             ?></p>
                             </td>
                            <td width="110" align="right"><p>
							 <a  href="##" onclick="open_febric_receive_status_color_wise_popup('<? echo $row[("booking_id")]; ?>','finish_feb_pord','<? echo $dts_row[("fabric_color")]; ?>')">
						<? 
							echo number_format($finish_product_arr[$row[("booking_id")]][$dts_row[("fabric_color")]],2); $dtls_tot_fin_prod_qty +=$finish_product_arr[$row[("booking_id")]][$dts_row[("fabric_color")]];  
						?>
                        </a>
                            </p></td>
                            <td width="110" align="right"><p>
							 <a  href="##" onclick="open_febric_receive_status_order_wise_popup('<? echo $row[("booking_id")]; ?>','fabric_purchase','<? echo $dts_row[("fabric_color")]; ?>')">
								<? 
                                    echo number_format(($finish_receive_arr[$row[("booking_id")]][$dts_row[("fabric_color")]]+$finish_product_arr[$row[("booking_id")]][$dts_row[("fabric_color")]]),2); $dtls_tot_fin_receive_qty +=$finish_receive_arr[$row[("booking_id")]][$dts_row[("fabric_color")]]+$finish_product_arr[$row[("booking_id")]][$dts_row[("fabric_color")]];  
                                ?>
                            </a>
                            </p></td>
                            <td width="110" align="right"><p><? $finish_total_available=($finish_receive_arr[$row[("booking_id")]][$dts_row[("fabric_color")]]+$finish_product_arr[$row[("booking_id")]][$dts_row[("fabric_color")]]); echo number_format($finish_total_available,2); $dtls_tot_fin_avabile_qty +=$finish_product_arr[$row[("booking_id")]][$dts_row[("fabric_color")]];  ?></p></td>
                            <td width="110" align="right"><p><? $finish_balance=$dts_row[("finish_fabric_qty")]-$finish_total_available; echo number_format($finish_balance,2); $dtls_tot_fin_balance +=$finish_balance; ?></p></td>
                            <td width="110" align="right"><p>
							<a  href="##" onclick="open_febric_receive_status_color_wise_popup('<? echo $row[("booking_id")]; ?>','issue_to_cut','<? echo $dts_row[("fabric_color")]; ?>')">
							<? 
                            echo number_format($issue_to_cut_arr[$row[("booking_id")]][$dts_row[("fabric_color")]],2); $dtls_tot_cutting_qty +=$issue_to_cut_arr[$row[("booking_id")]][$dts_row[("fabric_color")]]; 
                            ?>
                            </a>
                            </p></td>
                            <td width="110"><? echo implode(',',$dts_row['fabric_description']); ?></td>
                            <td>
                            <? 
                                echo $fabric_source[$dts_row["fabric_source"]];
                            ?>
                            </td>
                            
                        </tr>
                        <?
					}
				$m++;
				}
			$i++;
		}
		?>
        </tbody>
    </table>
    </div>
    <table class="rpt_table" border="1" rules="all" width="2570" cellpadding="0" cellspacing="0">
		<tfoot>
			<tr>
            	<th width="40">&nbsp;</th>
				<th width="65">&nbsp;</th>
                <th width="65">&nbsp;</th>
				<th width="140">&nbsp;</th>
                <th width="80"></th>
                <th width="100">Total:</th>
				<th width="110" align="right"><? echo number_format($dtls_tot_gery_req,2); ?></th>
                <th width="110" align="right"><? echo number_format($dtls_tot_yarn_issue,2); ?></th>
                <th width="110" align="right"><? echo number_format($dtls_tot_yarn_balance,2); ?></th>
				<th width="110" align="right"><? echo number_format($dtls_tot_gery_knit_product,2); ?></th>
                <th width="100" align="right"><? echo number_format($dtls_tot_gery_knit_receive,2); ?></th>
                <th width="100" align="right"><? echo number_format($dtls_tot_gery_available,2); ?></th>
				<th width="110" align="right"><? echo number_format($dtls_tot_gery_balance,2); ?></th>
				<th width="110" align="right"><? echo number_format($dtls_tot_gery_issue,2); ?></th>
				<th width="110" align="right"><? echo number_format($dtls_tot_batch_qty_only,2); ?></th>
				<th width="110" align="right"></th>
				<th width="110" align="right"><? echo number_format($dtls_tot_fin_req_qty,2); ?></th>
				<th width="110" align="right"><? echo number_format($dtls_tot_batch_qty,2); ?></th>
                <th width="110" align="right"><? echo number_format($dtls_tot_fin_prod_qty,2); ?></th>
				<th width="110" align="right"><? echo number_format($dtls_tot_fin_receive_qty,2); ?></th>
                <th width="110" align="right"><? echo number_format($dtls_tot_fin_avabile_qty,2); ?></th>
				<th width="110" align="right"><? echo number_format($dtls_tot_fin_balance,2); ?></th>
				<th width="110" align="right"><? echo number_format($dtls_tot_cutting_qty,2); ?> </th>
				<th width="110">&nbsp; </th>
				<th>&nbsp;</th>
			</tr>
		</tfoot>
	</table>
     <?
	foreach (glob("$user_name*.xls") as $filename) 
	{
	//if( @filemtime($filename) < (time()-$seconds_old) )
	@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_name."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$total_data####$filename####$tot_rows";
	exit();
}

if($action=="yarn_issue")
{
	
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?>
<script>

	function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="230px";
	}	
	
</script>	
	<fieldset style="width:1065px; margin-left:3px">
		<div id="report_container">
                <table border="1" class="rpt_table" rules="all" width="1060" cellpadding="0" cellspacing="0">
                <?
                $i=1; $total_yarn_issue_qnty=0; $total_yarn_issue_qnty_out=0;

				$yarn_count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
				
				$sql="SELECT a.id, d.id as trans_id, a.booking_id, g.booking_no,a.issue_number, a.issue_date, a.challan_no, a.knit_dye_source, a.knit_dye_company, c.id as prod_id, c.yarn_count_id, c.yarn_comp_type1st,c.yarn_comp_percent1st, c.yarn_comp_type2nd, c.yarn_comp_type2nd, c.yarn_comp_percent2nd, c.lot, c.yarn_type, c.product_name_details, c.brand, (d.cons_quantity) as issue_qnty from inv_issue_master a, product_details_master c, inv_transaction d, wo_non_ord_samp_booking_mst g where a.id=d.mst_id and d.prod_id=c.id and a.booking_id=g.id and d.transaction_type=2 and d.item_category=1 and a.issue_basis=1 and a.issue_purpose=8  and d.transaction_type=2 and a.entry_form=3 and a.booking_id=$boking_id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 union all select a.id, b.id as trans_id, g.id as booking_id, g.booking_no,a.issue_number, a.issue_date, a.challan_no, a.knit_dye_source, a.knit_dye_company, c.id as prod_id, c.yarn_count_id,c.yarn_comp_type1st,c.yarn_comp_percent1st,c.yarn_comp_type2nd,c.yarn_comp_type2nd, c.yarn_comp_percent2nd, c.lot, c.yarn_type, c.product_name_details, c.brand, (b.cons_quantity) as issue_qnty from inv_issue_master a, inv_transaction b, ppl_yarn_requisition_entry d, ppl_planning_info_entry_dtls e, ppl_planning_info_entry_mst f, wo_non_ord_samp_booking_mst g, product_details_master c where a.id=b.mst_id and b.prod_id= c.id and b.requisition_no=d.requisition_no and d.knit_id= e.id and e.mst_id= f.id and f.booking_no=g.booking_no and g.id=$boking_id and a.issue_basis=3 and a.issue_purpose in (1,8) and a.item_category=1 and b.transaction_type=2 and a.entry_form=3 and b.status_active=1 and b.is_deleted=0  and a.status_active=1 and a.is_deleted=0 ";
				//echo $sql;//die;
                $result=sql_select($sql);
				if(!empty($result))
				{
				?>
            	<thead>
					<th colspan="11"><b>Yarn Issue</b></th>
				</thead>
				<thead>
                    <th width="105">Issue Id</th>
                    <th width="90">Issue To</th>
                    <th width="105">Booking No</th>
                    <th width="80">Challan No</th>
                    <th width="70">Brand</th>
                    <th width="60">Lot No</th>
                    <th width="75">Issue Date</th>
                    <th width="80">Yarn Type</th>
                    <th width="200">Yarn Description</th>
                    <th width="90">Issue Qnty (In)</th>
                    <th>Issue Qnty (Out)</th>
				</thead>
                
                <?
				}
				foreach($result as $row)
				{
					if($y_issue_trans_id_arr[$row[csf("trans_id")]] == "")
					{
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";	
					
						if($row[csf('knit_dye_source')]==1) 
						{
							$issue_to=$company_library[$row[csf('knit_dye_company')]]; 
						}
						else if($row['knit_dye_source']==3) 
						{
							$issue_to=$supplier_details[$row[csf('knit_dye_company')]];
						}
						else
							$issue_to="&nbsp;";
							
	                    $yarn_issued=$row[csf('issue_qnty')];
	                    $composition_string = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')];
	    				if ($row[csf('yarn_comp_type2nd')] != 0) $composition_string .= " " . $composition[$row[csf('yarn_comp_type2nd')]] . " " . $row[csf('yarn_comp_percent2nd')];
	                    ?>
	                    <tr bgcolor="<? echo  $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
	                    	<td width="105"><p><? echo $row[csf('issue_number')]; ?></p></td>
	                        <td width="90"><p><? echo $issue_to; ?></p></td>
	                        <td width="105"><p><? echo $row[csf('booking_no')];?></p></td>
	                        <td width="80"><p><? echo $row[csf('challan_no')]; ?></p></td>
	                        <td width="70"><p><? $brand=return_field_value("brand_name","lib_brand","id='$row[brand_id]'"); echo $brand; ?></p></td>
	                        <td width="60"><p><? echo $row[csf('lot')]; ?></p></td>
	                        <td width="75" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
	                        <td width="80"><p><? echo $yarn_type[$row[csf('yarn_type')]]; ?></p></td>
	                        <td width="200"><p><? echo $yarn_count_arr[$row[csf('yarn_count_id')]].','.$composition_string; ?></p></td>

	                        <td align="right" width="90">
								<? 
									if($row[csf('knit_dye_source')]!=3)
									{
										echo number_format($yarn_issued,2);
										$total_yarn_issue_qnty+=$yarn_issued;
									}
									else echo "&nbsp;";
	                            ?>
	                        </td>
	                        <td align="right">
								<? 
									if($row[csf('knit_dye_source')]==3)
									{ 
										echo number_format($yarn_issued,2); 
										$total_yarn_issue_qnty_out+=$yarn_issued;
									}
									else echo "&nbsp;";
	                            ?>
	                        </td>
	                    </tr>
	                	<?
	                	$i++;
	                	$y_issue_trans_id_arr[$row[csf("trans_id")]] = $row[csf("trans_id")];
                	}
                }
                ?>
                <tr style="font-weight:bold">
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td align="right">Total</td>
                    <td align="right"><? echo number_format($total_yarn_issue_qnty,2);?></td>
                    <td align="right"><? echo number_format($total_yarn_issue_qnty_out,2);?></td>
                </tr>
                <tr style="font-weight:bold">
                    <td align="right" colspan="10">Issue Total</td>
                    <td align="right"><? echo number_format($total_yarn_issue_qnty+$total_yarn_issue_qnty_out,2);?></td>
                </tr>
                
                <?
                $total_yarn_return_qnty=0; $total_yarn_return_qnty_out=0;
                $sql_out="SELECT a.recv_number, d.id as trans_id, a.receive_date, a.challan_no, a.knitting_source, a.knitting_company, g.booking_no, (d.cons_quantity) as returned_qnty, c.lot, c.yarn_type, c.id as prod_id, c.product_name_details, d.brand_id from inv_receive_master a, product_details_master c, inv_transaction d, wo_non_ord_samp_booking_mst g where a.id=d.mst_id and d.prod_id=c.id and a.booking_id=g.id  and d.transaction_type=4 and d.item_category=1 and a.entry_form=9 and a.booking_id=$boking_id and a.booking_without_order=1 and a.receive_basis=1 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0  union all select a.recv_number, b.id as trans_id, a.receive_date, a.challan_no, a.knitting_source, a.knitting_company, g.booking_no, (b.cons_quantity) as returned_qnty, c.lot, c.yarn_type, c.id as prod_id, c.product_name_details, b.brand_id from  inv_receive_master a, inv_transaction b, ppl_yarn_requisition_entry d, ppl_planning_info_entry_dtls e, ppl_planning_info_entry_mst f, wo_non_ord_samp_booking_mst g, product_details_master c where a.id=b.mst_id and b.prod_id=c.id  and a.receive_basis=3 and a.item_category=1 and b.transaction_type=4 and a.entry_form=9 and a.booking_without_order=1 and g.id=$boking_id and a.booking_id= d.requisition_no and d.knit_id= e.id and e.mst_id= f.id and f.booking_no = g.booking_no and a.status_active=1 and a.is_deleted=0  and b.status_active =1 and b.is_deleted=0";
				//echo $sql_out;
				
                $result_out=sql_select($sql_out);
				if(!empty($result_out))
				{
				?>
                <thead>
                    <th colspan="11"><b>Yarn Return</b></th>
                </thead>
                <thead>
                	<th width="105">Return Id</th>
                    <th width="90">Return From</th>
                    <th width="105">Booking No</th>
                    <th width="80">Challan No</th>
                    <th width="70">Brand</th>
                    <th width="60">Lot No</th>
                    <th width="75">Return Date</th>
                    <th width="80">Yarn Type</th>
                    <th width="80">Yarn Description</th>
                    <th width="90">Return Qnty (In)</th>
                    <th>Return Qnty (Out)</th>
               </thead>
                
                <?
				}
				
				foreach($result_out as $row)
				{
					if($y_issue_trans_id_arr[$row[csf("trans_id")]] =="")
					{
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";	
					
						if($row[csf('knitting_source')]==1) 
						{
							$return_from=$company_library[$row[csf('knitting_company')]]; 
						}
						else if($row['knitting_source']==3) 
						{
							$return_from=$supplier_details[$row[csf('knitting_company')]];
						}
						else
							$return_from="&nbsp;";
							
	                    $yarn_returned=$row[csf('returned_qnty')];
	                    ?>
	                    <tr bgcolor="<? echo  $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
	                    	<td width="105"><p><? echo $row[csf('recv_number')]; ?></p></td>
	                        <td width="90"><p><? echo $return_from; ?></p></td>
	                        <td width="105"><p><? echo $row[csf('booking_no')];?></p></td>
	                        <td width="80"><p><? echo $row[csf('challan_no')]; ?></p></td>
	                        <td width="70"><p><? $brand=return_field_value("brand_name","lib_brand","id='$row[brand_id]'"); echo $brand; ?></p></td>
	                        <td width="60"><p><? echo $row[csf('lot')]; ?></p></td>
	                        <td width="75" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
	                        <td width="80"><p><? echo $yarn_type[$row[csf('yarn_type')]]; ?></p></td>
	                        <td width="200"><p><? echo $yarn_count_arr[$row[csf('yarn_count_id')]].','.$composition_string; ?></p></td>
	                        <td align="right" width="90">
								<? 
									if($row[csf('knitting_source')]!=3)
									{
										echo number_format($yarn_returned,2);
										$total_yarn_return_qnty+=$yarn_returned;
									}
									else echo "&nbsp;";
	                            ?>
	                        </td>
	                        <td align="right">
								<? 
									if($row[csf('knitting_source')]==3)
									{ 
										echo number_format($yarn_returned,2); 
										$total_yarn_return_qnty_out+=$yarn_returned;
									}
									else echo "&nbsp;";
	                            ?>
	                        </td>
	                    </tr>
	                	<?
	                	$i++;
	                	$y_issue_trans_id_arr[$row[csf("trans_id")]] = $row[csf("trans_id")];
                	}
                }
                ?>
                <tr style="font-weight:bold">
                    <td align="right" colspan="10">Total Issue Rtn</td>
                    <td align="right"><? echo number_format(($total_yarn_return_qnty+$total_yarn_return_qnty_out),2);?></td>
                </tr>
                <tfoot>    
                    <tr>
                        <th align="right" colspan="10">Net Issue</th>
                        <th align="right"><? echo number_format(($total_yarn_issue_qnty+$total_yarn_issue_qnty_out)-($total_yarn_return_qnty+$total_yarn_return_qnty_out),2);?></th>
                    </tr>
                </tfoot>
            </table>	
		</div>
	</fieldset> 
<?
exit();

}


if($action=="grey_receive")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	
	$machine_arr = return_library_array("select id, machine_no from lib_machine_name","id","machine_no");
	
?>
<script>

	function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="230px";
	}	
	
</script>	
<!--	<div style="width:990px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>-->
	<fieldset style="width:990px;">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="970" cellpadding="0" cellspacing="0">
				<thead>
					<th colspan="12"><b>Grey Receive Info</b></th>
				</thead>
				<thead>
                	<th width="20">SL</th>
                    <th width="100">Receive Id</th>
                    <th width="90">Prod. Basis</th>
                    <th width="145">Product Details</th>
                    <th width="100">Booking / Program No</th>
                    <th width="50">Machine No</th>
                    <th width="70">Production Date</th>
                    <th width="75">Inhouse Production</th>
                    <th width="75">Outside Production</th>
                    <th width="75">Total Prod. Qnty</th>
                    <th width="70">Challan No</th>
                    <th>Kniting Com.</th>
				</thead>
             </table>
             <div style="width:990px; max-height:330px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="970" cellpadding="0" cellspacing="0">
                    <?
					$receive_basis=array(0=>"Independent",1=>"Fabric Booking",2=>"Knitting Plan");
                    $i=1; $total_receive_qnty=0;

					$sql="SELECT a.recv_number, a.receive_date, a.receive_basis, a.booking_no, a.knitting_source, a.challan_no, a.knitting_company, b.machine_no_id, b.prod_id, g.product_name_details, sum(b.grey_receive_qnty) as quantity from inv_receive_master a, pro_grey_prod_entry_dtls b, wo_non_ord_samp_booking_mst c, product_details_master g where a.id=b.mst_id and a.booking_id=c.id and b.prod_id=g.id and a.receive_basis=1 and a.booking_without_order=1 and a.entry_form=2 and a.booking_id>0 and a.booking_id=$boking_id and b.status_active =1 and b.is_deleted=0 group by a.recv_number, a.receive_date, a.receive_basis, a.booking_no, a.knitting_source, a.challan_no, a.knitting_company, b.machine_no_id, b.prod_id, g.product_name_details union all select a.recv_number, a.receive_date, a.receive_basis, a.booking_no, a.knitting_source, a.challan_no, a.knitting_company, b.machine_no_id, b.prod_id, g.product_name_details, sum(b.grey_receive_qnty) as quantity from inv_receive_master a, pro_grey_prod_entry_dtls b, ppl_planning_info_entry_dtls d, ppl_planning_info_entry_mst e, wo_non_ord_samp_booking_mst c, product_details_master g where a.id=b.mst_id and a.booking_id=d.id and d.mst_id= e.id and e.booking_no= c.booking_no and b.prod_id=g.id and a.receive_basis=2 and a.booking_without_order=1 and a.entry_form=2 and a.booking_id>0 and c.id=$boking_id and b.status_active =1 and b.is_deleted=0 group by a.recv_number, a.receive_date, a.receive_basis, a.booking_no, a.knitting_source, a.challan_no, a.knitting_company, b.machine_no_id, b.prod_id, g.product_name_details";

                    $result=sql_select($sql);
        			foreach($result as $row)
                    {
                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";	
                    
                        $total_receive_qnty+=$row[csf('quantity')];
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="20" align="center"><? echo $i; ?></td>
                            <td width="100"><p><? echo $row[csf('recv_number')]; ?></p></td>
                            <td width="90"><p><? echo $receive_basis[$row[csf('receive_basis')]]; ?></p></td>
                            <td width="145"><p><? echo $row[csf('product_name_details')]; ?></p></td>
                            <td width="100"><p><? echo $row[csf('booking_no')]; ?>&nbsp;</p></td>
                            <td width="50" align="center"><p>&nbsp;<? echo $machine_arr[$row[csf('machine_no_id')]]; ?></p></td>
                            <td width="70" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                            <td align="right" width="75">
								<? 
                                	if($row[csf('knitting_source')]!=3)
									{
										echo number_format($row[csf('quantity')],2,'.','');
										$total_receive_qnty_in+=$row[csf('quantity')];
									}
									else echo "&nbsp;";
                                ?>
                            </td>
                            <td align="right" width="75">
								<? 
                                	if($row[csf('knitting_source')]==3)
									{
										echo number_format($row[csf('quantity')],2,'.','');
										$total_receive_qnty_out+=$row[csf('quantity')];
									}
									else echo "&nbsp;";
                                ?>
                            </td>
                            <td align="right" width="75"><? echo number_format($row[csf('quantity')],2,'.',''); ?></td>
                            <td width="70"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
                            <td><p><? if ($row[csf('knitting_source')]==1) echo $company_library[$row[csf('knitting_company')]]; else if ($row[csf('knitting_source')]==3) echo $supplier_details[$row[csf('knitting_company')]]; ?></p></td>
                        </tr>
                    <?
                    $i++;
                    }
                    ?>
                    <tfoot>
                        <th colspan="7" align="right">Total</th>
                        <th align="right"><? echo number_format($total_receive_qnty_in,2,'.',''); ?></th>
                        <th align="right"><? echo number_format($total_receive_qnty_out,2,'.',''); ?></th>
                        <th align="right"><? echo number_format($total_receive_qnty,2,'.',''); ?></th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                    </tfoot>
                </table>
            </div>	
        </div>
	</fieldset>   
<?
exit();
}

if($action=="fabric_purchase")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?>
<script>

	function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="230px";
	}	
	
</script>	
	<!--<div style="width:885px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>-->
	<fieldset style="width:880px; margin-left:3px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="860" cellpadding="0" cellspacing="0">
				<thead>
					<th colspan="9"><b>Fabric Receive Info</b></th>
				</thead>
				<thead>
                	<th width="30">SL</th>
                    <th width="120">System Id</th>
                    <th width="75">Rec. Date</th>
                    <th width="80">Rec. Basis</th>
                    <th width="90">Batch No</th>
                    <th width="90">Dyeing Source</th>
                    <th width="100">Dyeing Company</th>
                    <th width="90">Receive Qnty</th>
                    <th>Fabric Description</th>
				</thead>
             </table>
             <div style="width:877px; max-height:320px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="860" cellpadding="0" cellspacing="0">
                    <?

					$composition_arr=array(); $constructtion_arr=array();
					$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
					$data_array=sql_select($sql_deter);
					foreach( $data_array as $row )
					{
						$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
						$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
					}

					$sql = "SELECT a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, a.knitting_company, b.batch_id, c.batch_no, b.prod_id, e.product_name_details, sum(b.receive_qnty) as quantity,a.entry_form,b.fabric_description_id from inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_batch_create_mst c, wo_non_ord_samp_booking_mst d,  product_details_master e where a.id=b.mst_id and b.batch_id=c.id and a.entry_form in(7,37,68) and c.booking_no = d.booking_no and b.prod_id=e.id and d.id=$boking_id and b.color_id='$color_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.trans_id>0 group by a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, a.knitting_company, b.batch_id, c.batch_no, b.prod_id, e.product_name_details,a.entry_form,b.fabric_description_id";



                    $result=sql_select($sql);
                    $i=1;
        			foreach($result as $row)
                    {
                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";	
                    
                        if($row[csf('knitting_source')]==1) 
                        {
                            $dye_company=$company_library[$row[csf('knitting_company')]]; 
                        }
                        else if($row['knitting_source']==3) 
                        {
                            $dye_company=$supplier_details[$row[csf('knitting_company')]];
                        }
                        else
                            $dye_company="&nbsp;";
                    
                        $total_fabric_recv_qnty+=$row[csf('quantity')];

						$feb_des= '';
						if($row[csf('entry_form')]==68) 
                        {
							$feb_des=$constructtion_arr[$row[csf('fabric_description_id')]].", ".$composition_arr[$row[csf('fabric_description_id')]]; 
                        }
						else
						{
							$feb_des = $row[csf('product_name_details')];
						}

                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="30"><? echo $i; ?></td>
                            <td width="120"><p><? echo $row[csf('recv_number')]; ?></p></td>
                            <td width="75" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                            <td width="80"><? echo $receive_basis_arr[$row[csf('receive_basis')]]; ?></td>
                            <td width="90"><p><? echo $row[csf('batch_no')]; ?></p></td>
                            <td width="90"><? echo $knitting_source[$row[csf('knitting_source')]]; ?></td>
                            <td width="100"><p><? echo $dye_company; ?></p></td>
                            <td width="90" align="right"><? echo number_format($row[csf('quantity')],2); ?></td>
                            <td><p><? echo $feb_des; ?></p></td>
                        </tr>
                    <?
                    $i++;
                    }
                    ?>
                    <tfoot>
                        <th colspan="7" align="right">Total</th>
                        <th align="right"><? echo number_format($total_fabric_recv_qnty,2); ?></th>
                        <th>&nbsp;</th>
                    </tfoot>
                </table>
            </div>	
        </div>
	</fieldset>   
<?
exit();
}

if($action=="grey_issue")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?>
<script>

	function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="230px";
	}	
	
</script>	
	<!--<div style="width:885px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>-->
	<fieldset style="width:990px; margin-left:3px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="960" cellpadding="0" cellspacing="0">
                <thead>
                	<tr>
                        <th colspan="9"><b>Grey Issue Info</b></th>
                    </tr>
                    <tr>
                        <th width="40">SL</th>
                        <th width="120">Issue Id</th>
                        <th width="150">Issue Purpose</th>
                        <th width="150">Issue To</th>
                        <th width="115">Booking No</th>
                        <th width="90">Batch No</th>
                        <th width="80">Issue Date</th>
                        <th width="100">Issue Qnty (In)</th>
                        <th>Issue Qnty (Out)</th>
                    </tr>
				</thead>
             </table>
             <div style="width:978px; max-height:320px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="960" cellpadding="0" cellspacing="0">
                    <?
                    $i=1; $issue_to='';

					$sql="select  a.issue_number, a.issue_date, a.issue_purpose, a.knit_dye_source, a.knit_dye_company, a.booking_no, e.batch_no , sum(b.issue_qnty) as quantity from inv_issue_master a left join pro_batch_create_mst e on a.batch_no =e.id, inv_grey_fabric_issue_dtls b where a.id=b.mst_id  and a.entry_form=16 and a.booking_id=$boking_id and a.issue_basis=1 and a.issue_purpose=8 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.issue_number, a.issue_date, a.issue_purpose, a.knit_dye_source, a.knit_dye_company, a.booking_no, e.batch_no
					 union all 
					 select  a.issue_number, a.issue_date, a.issue_purpose, a.knit_dye_source, a.knit_dye_company, d.booking_no, e.batch_no, sum(c.qnty) as quantity from inv_issue_master a left join pro_batch_create_mst e on a.batch_no =e.id,pro_roll_details c, wo_non_ord_samp_booking_mst d where  a.id=c.mst_id and c.po_breakdown_id=d.id and c.booking_without_order=1 and c.po_breakdown_id=$boking_id and c.entry_form= 61 and a.status_active=1 and a.is_deleted=0  and c.status_active=1 and c.is_deleted=0 group by  a.issue_number, a.issue_date, a.issue_purpose, a.knit_dye_source, a.knit_dye_company, d.booking_no, e.batch_no";
					//echo $sql; 
                    $result=sql_select($sql);
        			foreach($result as $row)
                    {
                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";	
                    
                        if($row[csf('knit_dye_source')]==1) 
                        {
                            $issue_to=$company_library[$row[csf('knit_dye_company')]]; 
                        }
                        else if($row['knit_dye_source']==3) 
                        {
                            $issue_to=$supplier_details[$row[csf('knit_dye_company')]];
                        }
                        else
                            $issue_to="&nbsp;";
                    
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="40" align="center"><? echo $i; ?></td>
                            <td width="120"><p><? echo $row[csf('issue_number')]; ?></p></td>
                            <td width="150"><? echo $yarn_issue_purpose[$row[csf('issue_purpose')]]; ?></td>
                            <td width="150"><p><? echo $issue_to; ?></p></td>
                            <td width="115"><? echo $row[csf('booking_no')]; ?>&nbsp;</td>
                            <td width="90"><p><? echo $row[csf('batch_no')]; ?>&nbsp;</p></td>
                            <td width="80" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
                            <td width="100" align="right">
								<?
                                    if($row[csf('knit_dye_source')]!=3)
                                    {
                                        echo number_format($row[csf('quantity')],2);
                                        $total_issue_qnty+=$row[csf('quantity')];
                                    }
                                    else echo "&nbsp;";
                                ?>
                            </td>
                            <td align="right">
                                <?
                                    if($row[csf('knit_dye_source')]==3)
                                    {
                                        echo number_format($row[csf('quantity')],2);
                                        $total_issue_qnty_out+=$row[csf('quantity')];
                                    }
                                    else echo "&nbsp;";
                                ?>
                            </td>
                        </tr>
                    <?
                    $i++;
                    }
                    ?>
                    <tfoot>
                    	<tr>
                            <th colspan="7" align="right">Total</th>
                            <th align="right"><? echo number_format($total_issue_qnty,2); ?></th>
                            <th align="right"><? echo number_format($total_issue_qnty_out,2); ?></th>
                        </tr>
                        <tr>
                            <th colspan="7" align="right">Grand Total</th>
                            <th align="right" colspan="2"><? echo number_format($total_issue_qnty+$total_issue_qnty_out,2); ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>	
        </div>
	</fieldset>   
<?
exit();
}

if($action=="finish_feb_pord")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?>
<script>

	function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="230px";
	}	
	
</script>	
	<!--<div style="width:885px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>-->
	<fieldset style="width:880px; margin-left:3px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="860" cellpadding="0" cellspacing="0">
				<thead>
					<th colspan="9"><b>Fabric Receive Info</b></th>
				</thead>
				<thead>
                	<th width="30">SL</th>
                    <th width="120">System Id</th>
                    <th width="75">Rec. Date</th>
                    <th width="80">Rec. Basis</th>
                    <th width="90">Batch No</th>
                    <th width="90">Dyeing Source</th>
                    <th width="100">Dyeing Company</th>
                    <th width="90">Receive Qnty</th>
                    <th>Fabric Description</th>
				</thead>
             </table>
             <div style="width:877px; max-height:320px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="860" cellpadding="0" cellspacing="0">
                    <?
                    $i=1;
                    $total_fabric_recv_qnty=0; $dye_company='';

                    $sql="SELECT a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, a.knitting_company, b.batch_id, c.batch_no, b.prod_id, sum(b.receive_qnty) as quantity, e.product_name_details,0 as fabric_description_id,a.entry_form
					from inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_batch_create_mst c, product_details_master e
					where a.id=b.mst_id and b.batch_id=c.id and b.prod_id=e.id and a.receive_basis=5 and a.entry_form=7 and c.booking_without_order=1 and c.booking_no_id=$boking_id and b.color_id='$color_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, a.knitting_company, b.batch_id, c.batch_no, b.prod_id, e.product_name_details,a.entry_form
					union all 
					SELECT a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, a.knitting_company, b.batch_id, c.batch_no, b.prod_id, sum(b.receive_qnty) as quantity, e.product_name_details,b.fabric_description_id,a.entry_form
					from inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_batch_create_mst c, product_details_master e
					where a.id=b.mst_id and b.batch_id=c.id and b.prod_id=e.id and a.entry_form=66  and c.booking_no_id=$boking_id and b.color_id='$color_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, a.knitting_company, b.batch_id, c.batch_no, b.prod_id, e.product_name_details,b.fabric_description_id,a.entry_form";
					//echo $sql;
					$composition_arr=array(); $constructtion_arr=array();
					$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
					$data_array=sql_select($sql_deter);
					foreach( $data_array as $row )
					{
						$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
						$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
					}
                    $result=sql_select($sql);
        			foreach($result as $row)
                    {
                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";	
                    
                        if($row[csf('knitting_source')]==1) 
                        {
                            $dye_company=$company_library[$row[csf('knitting_company')]]; 
                        }
                        else if($row['knitting_source']==3) 
                        {
                            $dye_company=$supplier_details[$row[csf('knitting_company')]];
                        }	
                        else
                            $dye_company="&nbsp;";
                    
                        $total_fabric_recv_qnty+=$row[csf('quantity')];
						$feb_des='';
						if($row[csf('entry_form')]==66)
						{
							$feb_des= $constructtion_arr[$row[csf('fabric_description_id')]].", ".$composition_arr[$row[csf('fabric_description_id')]];
						}
						else
						{
							$feb_des = $row[csf('product_name_details')];
						}
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="30"><? echo $i; ?></td>
                            <td width="120"><p><? echo $row[csf('recv_number')]; ?></p></td>
                            <td width="75" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                            <td width="80"><? echo $receive_basis_arr[$row[csf('receive_basis')]]; ?></td>
                            <td width="90"><p><? echo $row[csf('batch_no')]; ?></p></td>
                            <td width="90"><? echo $knitting_source[$row[csf('knitting_source')]]; ?></td>
                            <td width="100"><p><? echo $dye_company; ?></p></td>
                            <td width="90" align="right"><? echo number_format($row[csf('quantity')],2); ?></td>
                            <td><p><? echo $feb_des; ?></p></td>
                        </tr>
                    <?
                    $i++;
                    }
                    ?>
                    <tfoot>
                        <th colspan="7" align="right">Total</th>
                        <th align="right"><? echo number_format($total_fabric_recv_qnty,2); ?></th>
                        <th>&nbsp;</th>
                    </tfoot>
                </table>
            </div>	
        </div>
	</fieldset>   
<?
exit();
}

if($action=="issue_to_cut")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?>
<script>

	function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="230px";
	}	
	
</script>	
	<!--<div style="width:775px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>-->
	<fieldset style="width:770px; margin-left:7px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="750" cellpadding="0" cellspacing="0">
				<thead>
					<th colspan="6"><b>Issue To Cutting Info</b></th>
				</thead>
				<thead>
                	<th width="50">SL</th>
                    <th width="120">System Id</th>
                    <th width="80">Issue Date</th>
                    <th width="120">Batch No</th>
                    <th width="110">Issue Qnty</th>
                    <th>Fabric Description</th>
				</thead>
             </table>
             <div style="width:767px; max-height:320px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="750" cellpadding="0" cellspacing="0">
                    <?

					$composition_arr=array(); $constructtion_arr=array();
					$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
					$data_array=sql_select($sql_deter);
					foreach( $data_array as $row )
					{
						$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
						$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
					}

                    $i=1; $total_issue_to_cut_qnty=0;
				
					
                    $sql="SELECT a.issue_number, a.issue_date, b.batch_id, c.batch_no, b.prod_id, b.issue_qnty, e.product_name_details from inv_issue_master a, inv_finish_fabric_issue_dtls b, pro_batch_create_mst c, product_details_master e where a.id=b.mst_id and b.prod_id=e.id and b.batch_id=c.id and a.entry_form=18 and c.booking_no_id>0 and c.booking_without_order=1 and c.booking_no_id=$boking_id and c.color_id='$color_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 ";
					//echo  $sql;
					$result=sql_select($sql);
					$issued_data_arr=array();
					foreach($result as $row)
					{
						$issued_data_arr[$row[csf('issue_number')]][$row[csf('prod_id')]]['issue_number']=$row[csf("issue_number")];
						$issued_data_arr[$row[csf('issue_number')]][$row[csf('prod_id')]]['issue_date']=$row[csf("issue_date")];
						$issued_data_arr[$row[csf('issue_number')]][$row[csf('prod_id')]]['batch_no']=$row[csf("batch_no")];
						$issued_data_arr[$row[csf('issue_number')]][$row[csf('prod_id')]]['issue_qnty']+=$row[csf("issue_qnty")];
						$issued_data_arr[$row[csf('issue_number')]][$row[csf('prod_id')]]['febric_des']=$row[csf("product_name_details")];
					}
					
					$sql_finish_rcv_iss=sql_select("SELECT c.id as booking_no_id,b.color_id,sum(b.receive_qnty) as receive_qty,e.barcode_no,d.batch_no,b.fabric_description_id from inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_batch_create_mst d, wo_non_ord_samp_booking_mst c,pro_roll_details e where a.id=b.mst_id  and a.entry_form in(37,68) and a.status_active=1 and a.is_deleted=0 and b.batch_id=d.id and d.booking_no=c.booking_no and b.id=e.dtls_id and d.booking_no_id=$boking_id and d.color_id='$color_id' group by c.id,b.color_id,e.barcode_no,d.batch_no,b.fabric_description_id");
	
					$finish_rcv_iss_arr=array();
					$finish_rcv_iss_batch_arr=array();
					foreach($sql_finish_rcv_iss as $row)
					{
						$finish_rcv_iss_arr[$row[csf("barcode_no")]]=$row[csf("barcode_no")];
						$finish_rcv_iss_batch_arr[$row[csf("barcode_no")]]["batch_no"]=$row[csf("batch_no")];
						$finish_rcv_iss_batch_arr[$row[csf("barcode_no")]]["febric_des"]=$constructtion_arr[$row[csf("fabric_description_id")]].", ".$composition_arr[$row[csf("fabric_description_id")]];

					}

					$finish_rcv_iss_arr = array_filter($finish_rcv_iss_arr);

					$finish_rcv_iss_ids = implode(",", $finish_rcv_iss_arr);
					$all_finish_rcv_iss_arr_cond=""; $finish_rcv_issCond="";
					if($db_type==2 && count($finish_rcv_iss_arr)>999)
					{
						$all_finish_rcv_iss_arr_chunk=array_chunk($finish_rcv_iss_arr,999) ;
						foreach($all_finish_rcv_iss_arr_chunk as $chunk_arr)
						{
							$chunk_arr_value=implode(",",$chunk_arr);
							$finish_rcv_issCond.=" a.barcode_no in($chunk_arr_value) or ";
						}

						$all_finish_rcv_iss_arr_cond.=" and (".chop($finish_rcv_issCond,'or ').")";
					}
					else
					{
						$all_finish_rcv_iss_arr_cond=" and a.barcode_no in($finish_rcv_iss_ids)";
					}
				
					$sql_finish_issue_arr=sql_select("SELECT a.id, a.barcode_no,  c.issue_number, c.issue_date,b.issue_qnty, b.prod_id from pro_roll_details a, inv_finish_fabric_issue_dtls b,inv_issue_master c,product_details_master d 
					where a.dtls_id=b.id and c.id=b.mst_id  and b.prod_id=d.id and a.entry_form=71 $all_finish_rcv_iss_arr_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");

					
					foreach($sql_finish_issue_arr as $row)
					{
						$issued_data_arr[$row[csf('issue_number')]][$row[csf('prod_id')]]['issue_number']=$row[csf("issue_number")];
						$issued_data_arr[$row[csf('issue_number')]][$row[csf('prod_id')]]['issue_date']=$row[csf("issue_date")];
						$issued_data_arr[$row[csf('issue_number')]][$row[csf('prod_id')]]['batch_no']=$finish_rcv_iss_batch_arr[$row[csf("barcode_no")]]["batch_no"];
						$issued_data_arr[$row[csf('issue_number')]][$row[csf('prod_id')]]['issue_qnty']+=$row[csf("issue_qnty")];
						$issued_data_arr[$row[csf('issue_number')]][$row[csf('prod_id')]]['febric_des']=$finish_rcv_iss_batch_arr[$row[csf("barcode_no")]]["febric_des"];
					}
					//var_dump($issued_data_arr);
                   
        			foreach($issued_data_arr as $prod_id=>$prod_data)
                    {
					
						foreach ($prod_data as $key => $row) {

                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";	
                    
                        $total_issue_to_cut_qnty+=$row['issue_qnty'];
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="50"><? echo $i; ?></td>
                            <td width="120"><p><? echo $row['issue_number']; ?></p></td>
                            <td width="80" align="center"><? echo change_date_format($row['issue_date']); ?></td>
                            <td width="120"><p><? echo $row['batch_no']; ?></p></td>
                            <td width="110" align="right"><? echo number_format($row['issue_qnty'],2); ?></td>
                            <td><p><? echo $row['febric_des']; ?></p></td>
                        </tr>
                    <?
                    $i++;
				}
                    }
                    ?>
                    <tfoot>
                        <th colspan="4" align="right">Total</th>
                        <th align="right"><? echo number_format($total_issue_to_cut_qnty,2); ?></th>
                        <th>&nbsp;</th>
                    </tfoot>
                </table>
            </div>	
        </div>
	</fieldset>   
<?
exit();
}

?>