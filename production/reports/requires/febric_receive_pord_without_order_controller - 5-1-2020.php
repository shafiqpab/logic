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

$color_array=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );
$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
$supplier_details=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
$buyer_short_name_library=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
$yarn_count_details=return_library_array( "select id, yarn_count from lib_yarn_count", "id", "yarn_count");
$sample_array=return_library_array( "select id,sample_name from lib_sample order by sample_name","id","sample_name");
$batch_details=return_library_array( "select id, batch_no from pro_batch_create_mst", "id", "batch_no");
$product_details=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details");
$booking_no_details=return_library_array( "select id, booking_no from wo_non_ord_samp_booking_mst", "id", "booking_no");
if($db_type==0)
{
	$fabric_desc_details=return_library_array( "select job_no, group_concat(distinct(fabric_description)) as fabric_description from wo_pre_cost_fabric_cost_dtls group by job_no", "job_no", "fabric_description");
}
else
{
	$fabric_desc_details=return_library_array( "select job_no, LISTAGG(cast(fabric_description as varchar2(4000)), ',') WITHIN GROUP (ORDER BY id) as fabric_description from wo_pre_cost_fabric_cost_dtls group by job_no", "job_no", "fabric_description");
}

$costing_per_id_library=array(); $costing_date_library=array();
$costing_sql=sql_select("select job_no, costing_per, costing_date from wo_pre_cost_mst");
foreach($costing_sql as $row)
{
	$costing_per_id_library[$row[csf('job_no')]]=$row[csf('costing_per')]; 
	$costing_date_library[$row[csf('job_no')]]=$row[csf('costing_date')];
}


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
	if($db_type==0)
	{
		if($txt_date_from!="" && $txt_date_to!="")
		{
			$str_cond=" and a.booking_date  between '$txt_date_from' and '$txt_date_to'";
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
	
	
	$sql_yarn_issue=sql_select("select a.booking_id,sum(b.cons_quantity) as issue_qty from inv_issue_master a, inv_transaction b where a.id=b.mst_id and a.issue_basis=1 and a.issue_purpose=8 and a.item_category=1 and b.transaction_type=2 and a.entry_form=3 and a.booking_id>0 group by a.booking_id order by a.booking_id");
	
	foreach($sql_yarn_issue as $row)
	{
		$yarn_issue_arr[$row[csf("booking_id")]]=$row[csf("issue_qty")];
	}
	$sql_yarn_issue_rtn=sql_select("select a.booking_id,sum(b.cons_quantity) as issue_rtn_qty from  inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.receive_basis=1 and a.item_category=1 and b.transaction_type=4 and a.entry_form=9 and a.booking_id>0 and a.booking_without_order=1 group by a.booking_id order by a.booking_id");
	foreach($sql_yarn_issue_rtn as $row)
	{
		$yarn_issue_rtn_arr[$row[csf("booking_id")]]=$row[csf("issue_rtn_qty")];
	}
	$sql_grey_knit_production=sql_select("select a.booking_id,sum(b.grey_receive_qnty) as receive_qty from inv_receive_master a, pro_grey_prod_entry_dtls b where a.id=b.mst_id and a.receive_basis=1 and a.booking_without_order=1 and a.entry_form=2 and a.booking_id>0  group by a.booking_id order by a.booking_id");
	
	foreach($sql_grey_knit_production as $row)
	{
		$grey_knit_production_arr[$row[csf("booking_id")]]+=$row[csf("receive_qty")];
	}
	
	$sql_grey_knit_receive=sql_select("select a.booking_id,sum(b.cons_quantity) as receive_qty from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.receive_basis in(1,2,4,6) and a.booking_without_order=1 and a.entry_form in(22) and b.transaction_type in(1,4) and a.booking_id>0 group by a.booking_id union all 
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
	
	if($txt_wo_no!="") $wo_cond_non=" and d.booking_no_prefix_num=$txt_wo_no"; 
	$sql_grey_roll=sql_select("select c.po_breakdown_id as booking_id, sum(c.qnty) as issue_qty,c.booking_without_order
	  from inv_issue_master a, inv_transaction b,pro_roll_details c, wo_non_ord_samp_booking_mst d 
	 where a.id=b.mst_id and a.id=c.mst_id and c.po_breakdown_id=d.id  and a.issue_purpose=8 and c.booking_without_order=1 $wo_cond_non  and c.entry_form= 61 and b.transaction_type=2  and a.company_id=$cbo_company_name
	 group by c.po_breakdown_id,c.booking_without_order order by c.po_breakdown_id ");

	foreach($sql_grey_roll as $result)
	{
		$grey_issue_arr[$result[csf("booking_id")]]+=$result[csf("issue_qty")];
	}
	/*$sql_grey_issue=sql_select("select a.booking_id,sum(b.cons_quantity) as issue_qty from inv_issue_master a, inv_transaction b where a.id=b.mst_id and a.issue_basis=1 and a.issue_purpose=8 and a.entry_form=16 and b.transaction_type=2  group by a.booking_id order by a.booking_id");
	foreach($sql_grey_issue as $result)
	{
		$grey_issue_arr[$result[csf("booking_id")]]+=$result[csf("issue_qty")];
	}*/
	
	/*$sql_batch_qty=sql_select("select a.id,a.batch_no,a.booking_no_id,a.color_id,sum(b.batch_qnty) as batch_qnty from  pro_batch_create_mst a,  pro_batch_create_dtls b where a.id=b.mst_id and a.booking_without_order=1 group by a.id,a.batch_no,a.booking_no_id ,a.color_id ");*/


	$sql_batch_qty=sql_select("select a.id,a.batch_no,a.booking_no_id,a.color_id,sum(b.batch_qnty) as batch_qnty ,c.buyer_id
	from pro_batch_create_mst a, pro_batch_create_dtls b,wo_non_ord_samp_booking_mst c 
	where a.id=b.mst_id 
	 and a.booking_no_id=c.id 
	 and a.booking_without_order=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
	and c.status_active=1 and c.is_deleted=0
	 group by a.id,a.batch_no,a.booking_no_id ,a.color_id ,c.buyer_id");



	//echo $sql_batch_qty;die;
	foreach($sql_batch_qty as $row)
	{
		$batch_qty_arr[$row[csf("booking_no_id")]][$row[csf("color_id")]][$row[csf("id")]]['batch_qnty']=$row[csf("batch_qnty")];
		$batch_qty_arr_check[$row[csf("booking_no_id")]][$row[csf("color_id")]]['batch_id'].=$row[csf("id")].",";
		$batch_qty_buyer_arr[$row[csf("booking_no_id")]][$row[csf("buyer_id")]]['batch_buyer_qnty']+=$row[csf("batch_qnty")];
	}
	//var_dump($batch_qty_arr_check);die;
	$sql_dyeing_qty=sql_select("select id,batch_id,batch_no from pro_fab_subprocess where load_unload_id=2");
	foreach($sql_dyeing_qty as $row)
	{
		$dyeing_check_arr[$row[csf("batch_id")]]=$row[csf("id")];
	}
	//var_dump($dyeing_check_arr[1232]);die;
	
	$sql_finish_product=sql_select("select c.booking_no_id,b.color_id,sum(b.receive_qnty) as receive_qty from inv_receive_master a, pro_finish_fabric_rcv_dtls b,  pro_batch_create_mst c where a.id=b.mst_id and b.batch_id=c.id and a.receive_basis=5 and a.entry_form=7 and c.booking_without_order=1 and c.booking_no_id>0 group by c.booking_no_id,b.color_id");
	foreach($sql_finish_product as $row)
	{
		$finish_product_arr[$row[csf("booking_no_id")]][$row[csf("color_id")]]=$row[csf("receive_qty")];
	}
	
	/*$sql_finish_receive=sql_select("select a.booking_id,b.color_id,sum(b.receive_qnty) as receive_qty from inv_receive_master a, pro_finish_fabric_rcv_dtls b where a.id=b.mst_id and a.receive_basis<>9 and a.booking_without_order=1 and a.entry_form=37  and a.status_active=1 and a.is_deleted=0 group by a.booking_id,b.color_id");*/

	$sql_finish_receive=sql_select("select c.booking_no_id,b.color_id,sum(b.receive_qnty) as receive_qty from inv_receive_master a, pro_finish_fabric_rcv_dtls b ,pro_batch_create_mst c where a.id=b.mst_id and a.entry_form=37 and a.status_active=1 and a.is_deleted=0 and b.batch_id=c.id group by c.booking_no_id,b.color_id ");
	
	//and a.receive_basis<>9 and a.booking_without_order=1 
	
	foreach($sql_finish_receive as $row)
	{
		$finish_receive_arr[$row[csf("booking_no_id")]][$row[csf("color_id")]]+=$row[csf("receive_qty")];
	}
	
	$sql_cutting_issue=sql_select("select c.booking_no_id,c.color_id,sum(b.issue_qnty) as issue_qty from inv_issue_master a, inv_finish_fabric_issue_dtls b, pro_batch_create_mst c where a.id=b.mst_id and b.batch_id=c.id and a.entry_form=18 and c.booking_no_id>0 and c.booking_without_order=1 group by c.booking_no_id,c.color_id");
	foreach($sql_cutting_issue as $row)
	{
		$issue_to_cut_arr[$row[csf("booking_no_id")]][$row[csf("color_id")]]+=$row[csf("issue_qty")];
	}
	
	/*echo "select a.id as booking_id, a.booking_no, a.booking_no_prefix_num,$select_year as wo_year,a.is_short,a.po_break_down_id,a.fabric_source,a.is_approved,a.job_no,a.buyer_id,a.company_id,a.supplier_id,a.item_category,a.is_approved,b.sample_type,b.grey_fabric as grey_fabric_qnty, b.finish_fabric as finish_fabric_qty, b.fabric_color,b.fabric_description 
	from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b 
	where a.booking_no=b.booking_no and a.item_category in(2,13) and a.company_id like '$cbo_company_name' and a.buyer_id like '$buyer_name'  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $str_cond  $wo_cond order by a.booking_no";*/
	$sample_type_id=trim(str_replace("'","",$cbo_sample_type));
	if($sample_type_id!=0) $sample_type_cond=" and b.sample_type='$sample_type_id'"; else $sample_type_cond="";
	
	$sql=sql_select("select a.id as booking_id, a.booking_no, a.booking_no_prefix_num,$select_year as wo_year, a.is_short, a.po_break_down_id, a.fabric_source, a.is_approved, a.job_no, a.buyer_id, a.company_id, a.supplier_id, a.item_category, a.is_approved, b.sample_type, b.grey_fabric as grey_fabric_qnty, b.finish_fabric as finish_fabric_qty, b.fabric_color, b.fabric_description 
	from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b 
	where a.booking_no=b.booking_no and a.item_category in(2,13) and a.company_id like '$cbo_company_name' and a.buyer_id like '$buyer_name'  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $str_cond $wo_cond $sample_type_cond order by a.booking_no");
	
	foreach($sql as $row)
	{
		$result_mst_array[$row[csf("booking_id")]]["booking_id"]=$row[csf("booking_id")];
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
			$buyer_wise_result[$row[csf("buyer_id")]]["yarn_issue"] +=$yarn_issue_arr[$row[csf("booking_id")]];
			$buyer_wise_result[$row[csf("buyer_id")]]["knitting_total"] +=($grey_knit_production_arr[$row[csf("booking_id")]]);
			//+$grey_knit_receive_arr[$row[csf("booking_id")]]
			$buyer_wise_result[$row[csf("buyer_id")]]["grey_issue"] +=$grey_issue_arr[$row[csf("booking_id")]];
			$buyer_wise_result[$row[csf("buyer_id")]]["batch_qty"] +=$batch_qty_arr[$row[csf("booking_id")]]['batch_qnty'];
			$buyer_wise_result[$row[csf("buyer_id")]]["batch_buyer_qnty"] +=$batch_qty_buyer_arr[$row[csf("booking_id")]][$row[csf("buyer_id")]]['batch_buyer_qnty'];
			
			$gt_yarn_issue+=$yarn_issue_arr[$row[csf("booking_id")]]-$yarn_issue_rtn_arr[$row[csf("booking_id")]];
			//$gt_grey_available+=($grey_knit_production_arr[$row[csf("booking_id")]]+$grey_knit_receive_arr[$row[csf("booking_id")]]);
			$gt_grey_available+=($grey_knit_receive_arr[$row[csf("booking_id")]]);
			$gt_batch_qty +=$batch_qty_arr[$row[csf("booking_id")]]['batch_qnty'];
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
		
		$buyer_wise_result[$row[csf("buyer_id")]]["fin_total_available"] +=$finish_product_arr[$row[csf("booking_id")]][$row[csf("fabric_color")]];
		//+$finish_receive_arr[$row[csf("booking_id")]][$row[csf("fabric_color")]]
		$buyer_wise_result[$row[csf("buyer_id")]]["issue_to_cut"] +=$issue_to_cut_arr[$row[csf("booking_id")]][$row[csf("fabric_color")]];
		
		
		$gt_yarn_grey_required+=$row[csf("grey_fabric_qnty")];
		
		/*$g_dtls_batch_qty=$batch_qty_arr_check[$row[csf("booking_id")]][$row[csf("fabric_color")]]['batch_qnty'];
		$g_dy_check_id=$dyeing_check_arr[$batch_qty_arr_check[$row[csf("booking_id")]][$row[csf("fabric_color")]]['batch_id']];
		if($g_dy_check_id!="")
		{
			$gt_dying_qty+=$g_dtls_batch_qty; 
		}*/
		
		$gt_finish_requir+=$row[csf("finish_fabric_qty")];
		$gt_finish_available+=($finish_receive_arr[$row[csf("booking_id")]][$row[csf("fabric_color")]]+$finish_product_arr[$row[csf("booking_id")]][$row[csf("fabric_color")]]);
		$gt_issue_cutting+=$issue_to_cut_arr[$row[csf("booking_id")]][$row[csf("fabric_color")]];
		
	}
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
                <td></td>
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
                <td></td>
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
                <td></td>
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
	<table class="rpt_table" border="1" rules="all" width="2470" cellpadding="0" cellspacing="0">
		<thead>
			<tr>
				<th colspan="5">Booking Details</th>
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
	<div style="width:2490px; overflow-y:scroll; max-height:300px" id="scroll_body">
    <table class="rpt_table" border="1" rules="all" width="2470" cellpadding="0" cellspacing="0" id="table_body">
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
				<td width="110" align="right"><p>
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
							echo number_format($finish_product_arr[$row[("booking_id")]][$dts_row[("fabric_color")]],2); $dtls_tot_fin_prod_qty +=$finish_product_arr[$row[("booking_id")]][$dts_row[("fabric_color")]];  
						?>
                        </a>
                        </p></td>
						<td width="110" align="right"><p>
                        <a  href="##" onclick="open_febric_receive_status_color_wise_popup('<? echo $row[("booking_id")]; ?>','fabric_purchase','<? echo $dts_row[("fabric_color")]; ?>')">
						<? 
						echo number_format($finish_receive_arr[$row[("booking_id")]][$dts_row[("fabric_color")]],2);  
						$dtls_tot_fin_receive_qty +=$finish_receive_arr[$row[("booking_id")]][$dts_row[("fabric_color")]];  
						?>
                        </a>
                        </p></td>
						<td width="110" align="right"><p><? 
						//$finish_total_available=($finish_receive_arr[$row[("booking_id")]][$dts_row[("fabric_color")]]+$finish_product_arr[$row[("booking_id")]][$dts_row[("fabric_color")]]); 
						$finish_total_available=($finish_receive_arr[$row[("booking_id")]][$dts_row[("fabric_color")]]); 
						//echo number_format($finish_total_available,2); 
						echo number_format($finish_receive_arr[$row[("booking_id")]][$dts_row[("fabric_color")]],2);  
						$dtls_tot_fin_avabile_qty +=$finish_total_available; ?></p></td>
						<td width="110" align="right"><p><? $finish_balance=$dts_row[("finish_fabric_qty")]-$finish_total_available; echo number_format($finish_balance,2); $dtls_tot_fin_balance +=$finish_balance;  ?></p></td>
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
					else
					{
						?>
                        <tr>
                            <td width="40">&nbsp;</td>
                            <td width="65">&nbsp;</td>
                            <td width="65">&nbsp;</td>
                            <td width="140">&nbsp;</td>
                            <td width="80">&nbsp;</td>
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
                                    echo number_format($finish_receive_arr[$row[("booking_id")]][$dts_row[("fabric_color")]],2); $dtls_tot_fin_receive_qty +=$finish_receive_arr[$row[("booking_id")]][$dts_row[("fabric_color")]];  
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
    <table class="rpt_table" border="1" rules="all" width="2470" cellpadding="0" cellspacing="0">
		<tfoot>
			<tr>
            	<th width="40">&nbsp;</th>
				<th width="65">&nbsp;</th>
                <th width="65">&nbsp;</th>
				<th width="140">&nbsp;</th>
                <th width="80">Total:</th>
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
<!--	<div style="width:870px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
-->	<fieldset style="width:1065px; margin-left:3px">
		<div id="report_container">
                <table border="1" class="rpt_table" rules="all" width="1060" cellpadding="0" cellspacing="0">
                <?
                $i=1; $total_yarn_issue_qnty=0; $total_yarn_issue_qnty_out=0;
				/*$sql="select a.issue_number, a.issue_date, a.challan_no, a.knit_dye_source, a.knit_dye_company, a.booking_no, sum(b.quantity) as issue_qnty, c.lot, c.yarn_type, c.id as prod_id, c.product_name_details, d.brand_id 
				from inv_issue_master a, order_wise_proportionate_details b, product_details_master c, inv_transaction d 
				where a.id=d.mst_id and d.id=b.trans_id and b.prod_id=c.id  and d.transaction_type=2 and d.item_category=1 and b.trans_type=2 and b.entry_form=3 and a.booking_id=$boking_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id, c.id";
					$sql_yarn_issue=sql_select("select a.booking_id,sum(b.cons_quantity) as issue_qty from inv_issue_master a, inv_transaction b where a.id=b.mst_id and a.issue_basis=1 and a.issue_purpose=8 and a.item_category=1 and b.transaction_type=2 and a.booking_id>0 group by a.booking_id order by a.booking_id");
					
					"select a.booking_id,sum(b.cons_quantity) as issue_qty from inv_issue_master a, inv_transaction b where a.id=b.mst_id and a.issue_basis=1 and a.issue_purpose=8 and a.item_category=1 and b.transaction_type=2 and a.entry_form=3 and a.booking_id>0 group by a.booking_id order by a.booking_id"
*/
					$yarn_count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');

				$sql="select a.id, a.booking_id, a.booking_no,a.issue_number, a.issue_date, a.challan_no, a.knit_dye_source, a.knit_dye_company, c.id as prod_id, c.yarn_count_id,c.yarn_comp_type1st,c.yarn_comp_percent1st,c.yarn_comp_type2nd,c.yarn_comp_type2nd,c.yarn_comp_percent2nd,c.lot, c.yarn_type, c.product_name_details, c.brand, sum(d.cons_quantity) as issue_qnty
				from inv_issue_master a, product_details_master c, inv_transaction d 
				where a.id=d.mst_id and d.prod_id=c.id  and d.transaction_type=2 and d.item_category=1 and a.issue_basis=1 and a.issue_purpose=8  and d.transaction_type=2 and a.entry_form=3 and a.booking_id=$boking_id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id, a.booking_id, a.booking_no,a.issue_number, a.issue_date, a.challan_no, a.knit_dye_source, a.knit_dye_company, c.id,c.yarn_count_id,c.yarn_comp_type1st,c.yarn_comp_percent1st,c.yarn_comp_type2nd,c.yarn_comp_type2nd,c.yarn_comp_percent2nd, c.lot, c.yarn_type, c.product_name_details, c.brand";
				//echo $sql;die;
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
				//	$sql_yarn_issue_rtn=sql_select("select a.booking_id,sum(b.cons_quantity) as issue_rtn_qty from  inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.receive_basis=1 and a.item_category=1 and b.transaction_type=4 and a.entry_form=9 and a.booking_id>0 group by a.booking_id order by a.booking_id");

                $total_yarn_return_qnty=0; $total_yarn_return_qnty_out=0;
                $sql_out="select a.recv_number, a.receive_date, a.challan_no, a.knitting_source, a.knitting_company, a.booking_no, sum(d.cons_quantity) as returned_qnty, c.lot, c.yarn_type, c.id as prod_id, c.product_name_details, d.brand_id 
				from inv_receive_master a, product_details_master c, inv_transaction d 
				where a.id=d.mst_id  and d.prod_id=c.id  and d.transaction_type=4 and d.item_category=1 and a.entry_form=9 and a.booking_id=$boking_id and a.booking_without_order=1 and a.receive_basis=1 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by d.id";
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
                    <th width="200">Return Qnty (In)</th>
                    <th>Return Qnty (Out)</th>
               </thead>
                
                <?
				}
				
				foreach($result_out as $row)
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
                        <td width="105"><p><? echo $booking_no_details[$boking_id];//$row[csf('booking_no')];?></p></td>
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
					$product_arr=return_library_array( "select id,product_name_details from  product_details_master",'id','product_name_details'); 
					/*
					"select a.booking_id,sum(b.grey_receive_qnty) as receive_qty from inv_receive_master a, pro_grey_prod_entry_dtls b where a.id=b.mst_id and a.receive_basis=1 and a.booking_without_order=1 and a.entry_form=2 and a.booking_id>0  group by a.booking_id order by a.booking_id"
					*/
                    $sql="select a.recv_number, a.receive_date, a.receive_basis, a.booking_no, a.knitting_source, a.challan_no, a.knitting_company, b.machine_no_id, b.prod_id, sum(b.grey_receive_qnty) as quantity 
					from inv_receive_master a, pro_grey_prod_entry_dtls b
					where a.id=b.mst_id and a.receive_basis=1 and a.booking_without_order=1 and a.entry_form=2 and a.booking_id=$boking_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.recv_number, a.receive_date, a.receive_basis, a.booking_no, a.knitting_source, a.challan_no, a.knitting_company, b.machine_no_id, b.prod_id";
					//echo $sql;
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
                            <td width="145"><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
                            <td width="100"><p><? echo $booking_no_details[$boking_id];//echo $row[csf('booking_no')]; ?>&nbsp;</p></td>
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
					$receive_basis=array(0=>"Independent",1=>"Fabric Booking",2=>"Knitting Plan");
                    $i=1;
                    $total_fabric_recv_qnty=0; $dye_company='';
                    $sql="select a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, a.knitting_company, b.batch_id, b.prod_id, sum(b.receive_qnty) as quantity 
					from inv_receive_master a, pro_finish_fabric_rcv_dtls b
					where a.id=b.mst_id and a.entry_form=37 and a.booking_id=$boking_id and b.color_id='$color_id' and a.receive_basis!=9 and a.booking_without_order=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, a.knitting_company, b.batch_id, b.prod_id";
					//echo $sql;die;
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
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="30"><? echo $i; ?></td>
                            <td width="120"><p><? echo $row[csf('recv_number')]; ?></p></td>
                            <td width="75" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                            <td width="80"><? echo $receive_basis_arr[$row[csf('receive_basis')]]; ?></td>
                            <td width="90"><p><? echo $batch_details[$row[csf('batch_id')]]; ?></p></td>
                            <td width="90"><? echo $knitting_source[$row[csf('knitting_source')]]; ?></td>
                            <td width="100"><p><? echo $dye_company; ?></p></td>
                            <td width="90" align="right"><? echo number_format($row[csf('quantity')],2); ?></td>
                            <td><p><? echo $product_details[$row[csf('prod_id')]]; ?></p></td>
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
					/*
					"select a.booking_id,sum(b.cons_quantity) as issue_qty from inv_issue_master a, inv_transaction b where a.id=b.mst_id and a.issue_basis=1 and a.issue_purpose=8 and a.entry_form=16 and b.transaction_type=2  group by a.booking_id order by a.booking_id"
					*/

                    $sql="select a.issue_number, a.issue_date, a.issue_purpose, a.knit_dye_source, a.knit_dye_company, a.booking_no, a.batch_no, b.id , sum(b.issue_qnty) as quantity 
					from inv_issue_master a, inv_grey_fabric_issue_dtls b
					where a.id=b.mst_id and a.entry_form=16 and a.booking_id=$boking_id and a.issue_basis=1 and a.issue_purpose=8 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
					group by a.issue_number, a.issue_date, a.issue_purpose, a.knit_dye_source, a.knit_dye_company, a.booking_no, a.batch_no, b.id";
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
                            <td width="90"><p><? echo $batch_details[$row[csf('batch_no')]]; ?>&nbsp;</p></td>
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
					
					/* $sql="select a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, a.knitting_company, b.batch_id, b.prod_id, sum(b.receive_qnty) as quantity 
					from inv_receive_master a, pro_finish_fabric_rcv_dtls b
					where a.id=b.mst_id and a.entry_form=37 and a.booking_id=$boking_id and b.color_id='$color_id' and a.receive_basis!=9 and a.booking_without_order=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.id";
					
						$sql_finish_product=sql_select("select c.booking_no_id,b.color_id,sum(b.receive_qnty) as receive_qty from inv_receive_master a, pro_finish_fabric_rcv_dtls b,  pro_batch_create_mst c where a.id=b.mst_id and b.batch_id=c.id and a.receive_basis=5 and a.entry_form=7 and c.booking_without_order=1 and c.booking_no_id>0 group by c.booking_no_id,b.color_id");
						
						"select c.booking_no_id,b.color_id,sum(b.receive_qnty) as receive_qty from inv_receive_master a, pro_finish_fabric_rcv_dtls b,  pro_batch_create_mst c where a.id=b.mst_id and b.batch_id=c.id and a.receive_basis=5 and a.entry_form=7 and c.booking_without_order=1 and c.booking_no_id>0 group by c.booking_no_id,b.color_id"

					*/
					
                    $sql="select a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, a.knitting_company, b.batch_id, b.prod_id, sum(b.receive_qnty) as quantity 
					from inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_batch_create_mst c
					where a.id=b.mst_id and b.batch_id=c.id  and a.receive_basis=5 and a.entry_form=7 and c.booking_without_order=1 and c.booking_no_id=$boking_id and b.color_id='$color_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, a.knitting_company, b.batch_id, b.prod_id";
					//echo $sql;
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
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="30"><? echo $i; ?></td>
                            <td width="120"><p><? echo $row[csf('recv_number')]; ?></p></td>
                            <td width="75" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                            <td width="80"><? echo $receive_basis_arr[$row[csf('receive_basis')]]; ?></td>
                            <td width="90"><p><? echo $batch_details[$row[csf('batch_id')]]; ?></p></td>
                            <td width="90"><? echo $knitting_source[$row[csf('knitting_source')]]; ?></td>
                            <td width="100"><p><? echo $dye_company; ?></p></td>
                            <td width="90" align="right"><? echo number_format($row[csf('quantity')],2); ?></td>
                            <td><p><? echo $product_details[$row[csf('prod_id')]]; ?></p></td>
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
                    $i=1; $total_issue_to_cut_qnty=0;
					//	$sql_cutting_issue=sql_select("select c.booking_no_id,c.color_id,sum(b.issue_qnty) as issue_qty from inv_issue_master a, inv_finish_fabric_issue_dtls b, pro_batch_create_mst c where a.id=b.mst_id and b.batch_id=c.id and a.entry_form=18 and c.booking_no_id>0 and c.booking_without_order=1 group by c.booking_no_id,c.color_id");

                    $sql="select a.issue_number, a.issue_date, b.batch_id, b.prod_id, sum(b.issue_qnty) as quantity from inv_issue_master a, inv_finish_fabric_issue_dtls b, pro_batch_create_mst c where a.id=b.mst_id and b.batch_id=c.id and a.entry_form=18 and c.booking_no_id>0 and c.booking_without_order=1 and c.booking_no_id=$boking_id and c.color_id='$color_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.issue_number, a.issue_date, b.batch_id, b.prod_id";
					//echo $sql;
                    $result=sql_select($sql);
        			foreach($result as $row)
                    {
                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";	
                    
                        $total_issue_to_cut_qnty+=$row[csf('quantity')];
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="50"><? echo $i; ?></td>
                            <td width="120"><p><? echo $row[csf('issue_number')]; ?></p></td>
                            <td width="80" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
                            <td width="120"><p><? echo $batch_details[$row[csf('batch_id')]]; ?></p></td>
                            <td width="110" align="right"><? echo number_format($row[csf('quantity')],2); ?></td>
                            <td><p><? echo $product_details[$row[csf('prod_id')]]; ?></p></td>
                        </tr>
                    <?
                    $i++;
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