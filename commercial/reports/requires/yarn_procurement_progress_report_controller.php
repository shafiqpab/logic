<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id = $_SESSION['logic_erp']["user_id"];


if($action=="job_no_popup")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);

	?>
	<script>
	var selected_id = new Array, selected_name = new Array(); 
		selected_attach_id = new Array();

		function check_all_data() {
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 1;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				var onclickString = $('#tr_' + i).attr('onclick');
				var paramArr = onclickString.split("'");
				var functionParam = paramArr[1];
				js_set_value( functionParam );
				
			}
		}

		function toggle( x, origColor ) {
				var newColor = 'yellow';
				if ( x.style ) {
					x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
				}
			}

		function js_set_value(id)
		{
			var str=id.split("_");
			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFFF' );
			var strdt=str[2];
			str=str[1];

			if( jQuery.inArray(  str , selected_id ) == -1 ) {
				selected_id.push( str );
				selected_name.push( strdt );
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str  ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i,1 );
			}
			var id = '';
			var ddd='';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				ddd += selected_name[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			ddd = ddd.substr( 0, ddd.length - 1 );
			$('#hide_job_id').val( id );
			$('#hide_style_ref_no').val( ddd );
		}
	</script>
    <input type='hidden' id='hide_style_ref_no' name="hide_style_ref_no" />
    <input type='hidden' id='hide_job_id' name="hide_job_id" />
	<?
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	
	$company_id=str_replace("'","",$companyID);
	$buyer_id=str_replace("'","",$buyer_name);
	$year_id=str_replace("'","",$cbo_year_id);
	$search_type=str_replace("'","",$search_type);
	//$month_id=$data[5];
	//echo $month_id;
	$sql_cond="";
	if($buyer_id>0) $sql_cond .=" and a.buyer_name=$buyer_id";
	if($buyer_id>0) $sql_cond2 =" and a.buyer_id=$buyer_id";
	
	if($db_type==0) $year_field_by="year(a.insert_date)";
	else if($db_type==2) $year_field_by="to_char(a.insert_date,'YYYY')";
	else $year_field_by="";
	
	if($year_id!=0) $year_cond=" and $year_field_by='$year_id'"; else $year_cond="";
	

		$arr=array (0=>$buyer_arr);
		$sql= "select a.id, a.job_no, a.job_no_prefix_num, a.buyer_name, a.style_ref_no, $year_field_by as year from wo_po_details_master a where a.status_active=1 and a.is_deleted=0 and a.company_name=$company_id $sql_cond $year_cond order by id DESC";

		echo create_list_view("list_view", "Buyer Name,Job No,Style Ref. No", "170,130,100","610","350",0, $sql , "js_set_value", "id,style_ref_no", "", 1, "buyer_name,0,0,0", $arr , "buyer_name,job_no,style_ref_no", "yarn_procurement_progress_report_controller",'setFilterGrid("list_view",-1);','0','',1) ;
		exit();

}



if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$buyer_arr=return_library_array("select id,buyer_name from  lib_buyer","id","buyer_name");
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$yarnCount_arr=return_library_array( "select id,yarn_count from  lib_yarn_count where is_deleted=0 and status_active=1",'id','yarn_count');
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$bank_arr=return_library_array( "select id, bank_name from lib_bank",'id','bank_name');
	$user_arr=return_library_array( "select id, user_name from user_passwd",'id','user_name');
	//("cbo_company_name*cbo_year*cbo_based_on*txt_search_no*txt_date_from*txt_date_to","../../")
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_year=str_replace("'","",$cbo_year);
	$type=str_replace("'","",$type);
	$cbo_based_on=str_replace("'","",$cbo_based_on);
	$txt_search_no=str_replace("'","",$txt_search_no);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$cbo_receive_status=str_replace("'","",$cbo_receive_status);
	$txt_style_ref_no=str_replace("'","",$txt_style_ref_no);
	$txt_job_id=str_replace("'","",$txt_job_id);
	if($txt_style_ref_no !="")$style_ref_cond=" and b.JOB_ID in($txt_job_id)"; 

	// echo $style_ref_cond;die;
	$str_cond=$str_cond_independ="";
	
	//echo $cbo_based_on ; die;
	// req condition check here
	if($db_type==0)
	{
		$txt_date_from=change_date_format($txt_date_from,'yyyy-mm-dd');
		$txt_date_to=change_date_format($txt_date_to,'yyyy-mm-dd');
	}
	else if($db_type==2)
	{
		$txt_date_from=change_date_format($txt_date_from,'','',-1);
		$txt_date_to=change_date_format($txt_date_to,'','',-1);
	}

	if ($type==1)  // show
	{	
		$str_cond=$str_cond_independ=$pi_cond=$btb_cond="";
		if($cbo_based_on==1)
		{
			$str_cond.=" and a.requ_prefix_num like '%$txt_search_no%'";
			if($txt_date_from!="" && $txt_date_to!="") $str_cond.=" and a.requisition_date between '$txt_date_from' and '$txt_date_to'";
		}
		else if($cbo_based_on==2)
		{
			$str_cond.=" and d.wo_number_prefix_num like '%$txt_search_no%'";
			if($txt_date_from!="" && $txt_date_to!="")  $str_cond.=" and d.wo_date between '$txt_date_from' and '$txt_date_to'";
			$str_cond_independ.=" and d.wo_number_prefix_num like '%$txt_search_no%'";
			if($txt_date_from!="" && $txt_date_to!="") $str_cond_independ.=" and d.wo_date between '$txt_date_from' and '$txt_date_to'";
		}
		else if($cbo_based_on==3)
		{
			if ($txt_search_no != "")
			{
				$pi_cond.=" and a.pi_number like '%$txt_search_no%'";
			}
			if($txt_date_from!="" && $txt_date_to!="") $pi_cond.=" and a.pi_date between '$txt_date_from' and '$txt_date_to'";
		}
		
		
		$sql_pi=sql_select("select a.id as pi_id,a.entry_form, a.item_category_id, a.supplier_id as pi_suplier, a.pi_number, a.pi_date, a.last_shipment_date, a.currency_id, b.work_order_id, b.work_order_dtls_id, b.color_id, b.count_name, b.yarn_composition_item1, b.yarn_composition_percentage1, b.yarn_composition_item2, b.yarn_composition_percentage2, b.yarn_type, b.uom, b.quantity, b.rate, b.amount from com_pi_master_details a, com_pi_item_details b 
		where a.item_category_id=1 and a.id=b.pi_id and a.importer_id=$cbo_company_name and a.status_active=1 and b.status_active=1  and a.goods_rcv_status<>1 and b.work_order_dtls_id > 0 $pi_cond"); //and b.work_order_id is not null
		
		$pi_data_arr=array();
		foreach($sql_pi as $row)
		{
			if($row[csf("work_order_dtls_id")]) $pi_wo_dtls_id_all[]=$row[csf("work_order_dtls_id")];
			$pi_id_arr[]=$row[csf("pi_id")];
			$pi_data_arr[$row[csf("work_order_dtls_id")]]["work_order_dtls_id"]=$row[csf("work_order_dtls_id")];
			$pi_data_arr[$row[csf("work_order_dtls_id")]]["pi_id"]=$row[csf("pi_id")];
			$pi_data_arr[$row[csf("work_order_dtls_id")]]["entry_form"]=$row[csf("entry_form")];
			$pi_data_arr[$row[csf("work_order_dtls_id")]]["item_category_id"]=$row[csf("item_category_id")];
			$pi_data_arr[$row[csf("work_order_dtls_id")]]["pi_id_all"].=$row[csf("pi_id")].",";
			$pi_data_arr[$row[csf("work_order_dtls_id")]]["pi_suplier"].=$row[csf("pi_suplier")].",";
			$pi_data_arr[$row[csf("work_order_dtls_id")]]["pi_number"].=$row[csf("pi_number")].",";
			$pi_data_arr[$row[csf("work_order_dtls_id")]]["pi_date"].=$row[csf("pi_date")].",";
			$pi_data_arr[$row[csf("work_order_dtls_id")]]["last_shipment_date"].=$row[csf("last_shipment_date")].",";
			$pi_data_arr[$row[csf("work_order_dtls_id")]]["currency_id"].=$row[csf("currency_id")].",";
			$pi_data_arr[$row[csf("work_order_dtls_id")]]["work_order_id"]=$row[csf("work_order_id")];
			$pi_data_arr[$row[csf("work_order_dtls_id")]]["color_id"]=$row[csf("color_id")];
			$pi_data_arr[$row[csf("work_order_dtls_id")]]["count_name"]=$row[csf("count_name")];
			$pi_data_arr[$row[csf("work_order_dtls_id")]]["yarn_composition_item1"]=$row[csf("yarn_composition_item1")];
			$pi_data_arr[$row[csf("work_order_dtls_id")]]["yarn_composition_percentage1"]=$row[csf("yarn_composition_percentage1")];
			$pi_data_arr[$row[csf("work_order_dtls_id")]]["yarn_composition_item2"]=$row[csf("yarn_composition_item2")];
			$pi_data_arr[$row[csf("work_order_dtls_id")]]["yarn_comp_percent2nd"]=$row[csf("yarn_comp_percent2nd")];
			$pi_data_arr[$row[csf("work_order_dtls_id")]]["yarn_type"]=$row[csf("yarn_type")];
			$pi_data_arr[$row[csf("work_order_dtls_id")]]["uom"]=$row[csf("uom")];
			$pi_data_arr[$row[csf("work_order_dtls_id")]]["quantity"]+=$row[csf("quantity")];
			$pi_data_arr[$row[csf("work_order_dtls_id")]]["rate"]=$row[csf("rate")];
			$pi_data_arr[$row[csf("work_order_dtls_id")]]["amount"]+=$row[csf("amount")];
		}
		
		if($cbo_based_on==3)
		{
			$pi_id_arr_all=array_chunk(array_unique($pi_id_arr),999);
			//$sql_btb="select a.id as btb_id, a.lc_number, a.lc_date, a.issuing_bank_id, a.payterm_id, a.tenor, a.lc_value, b.pi_id from com_btb_lc_master_details a, com_btb_lc_pi b where a.id=b.com_btb_lc_master_details_id and a.item_category_id=1 and a.status_active=1 and b.status_active=1";
			
			$sql_btb="select a.id as btb_id, a.lc_number, a.lc_date, a.issuing_bank_id, a.payterm_id, a.tenor, a.lc_value, b.pi_id from com_btb_lc_master_details a, com_btb_lc_pi b where a.id=b.com_btb_lc_master_details_id and a.pi_entry_form=165 and a.status_active=1 and b.status_active=1";
			
			$p=1;
			if(!empty($pi_id_arr_all))
			{
				foreach($pi_id_arr_all as $pi_id)
				{
					if($p==1) $sql_btb .=" and (b.pi_id in(".implode(',',$pi_id).")"; else $sql_btb .=" or b.pi_id in(".implode(',',$pi_id).")";
					$p++;
				}
				$sql_btb .=" ) ";
			}
			
			//echo $sql_btb;die;
		}
		else
		{
			//$sql_btb="select a.id as btb_id, a.lc_number, a.lc_date, a.issuing_bank_id, a.payterm_id, a.tenor, a.lc_value, b.pi_id from com_btb_lc_master_details a, com_btb_lc_pi b where a.id=b.com_btb_lc_master_details_id and a.item_category_id=1 and a.status_active=1 and b.status_active=1";
			
			$sql_btb="select a.id as btb_id, a.lc_number, a.lc_date, a.issuing_bank_id, a.payterm_id, a.tenor, a.lc_value, b.pi_id from com_btb_lc_master_details a, com_btb_lc_pi b where a.id=b.com_btb_lc_master_details_id and a.pi_entry_form=165 and a.status_active=1 and b.status_active=1";
		}
		//echo $sql_btb;die;
			
		$sql_btb_result=sql_select($sql_btb);
		$btb_data_arr=array();
		foreach($sql_btb_result as $row)
		{
			$btb_data_arr[$row[csf("pi_id")]]["pi_id"]=$row[csf("pi_id")];
			$btb_data_arr[$row[csf("pi_id")]]["btb_id"]=$row[csf("btb_id")];
			$btb_data_arr[$row[csf("pi_id")]]["btb_id_all"].=$row[csf("btb_id")].",";
			$btb_data_arr[$row[csf("pi_id")]]["lc_number"].=$row[csf("lc_number")].",";
			$btb_data_arr[$row[csf("pi_id")]]["lc_date"].=$row[csf("lc_date")].",";
			$btb_data_arr[$row[csf("pi_id")]]["issuing_bank_id"].=$row[csf("issuing_bank_id")].",";
			$btb_data_arr[$row[csf("pi_id")]]["payterm_id"].=$row[csf("payterm_id")].",";
			$btb_data_arr[$row[csf("pi_id")]]["tenor"].=$row[csf("tenor")].",";
			$btb_data_arr[$row[csf("pi_id")]]["lc_value"]+=$row[csf("lc_value")];
		}
		
		$rcv_return_sql=sql_select("select b.prod_id, a.received_id, b.cons_quantity, b.cons_quantity, b.cons_amount from inv_issue_master a, inv_transaction b where a.id=b.mst_id and a.entry_form=8 and b.transaction_type=3 and b.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		$rcv_rtn_data=array();
		foreach($rcv_return_sql as $row)
		{
			$rcv_rtn_data[$row[csf("received_id")]][$row[csf("prod_id")]]["cons_quantity"]+=$row[csf("cons_quantity")];
			$rcv_rtn_data[$row[csf("received_id")]][$row[csf("prod_id")]]["cons_amount"]+=$row[csf("cons_amount")];
		}
		
		$req_wo_recv_sql=sql_select("select a.receive_basis, a.booking_id, c.color, c.yarn_type, c.yarn_count_id, c.yarn_comp_type1st, b.order_qnty as recv_qnty, b.order_amount as recv_amt, b.mst_id, b.prod_id, b.transaction_date, a.exchange_rate 
		from  inv_receive_master a, inv_transaction b, product_details_master c 
		where a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=1 and a.item_category=1 and a.booking_id>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
		order by a.booking_id, a.receive_basis, c.color, c.yarn_type, c.yarn_count_id, c.yarn_comp_type1st, b.transaction_date");
		$min_date=$max_date="";
		$b=0;
		foreach($req_wo_recv_sql as $row)
		{
			if($item_check[$row[csf("booking_id")]][$row[csf("receive_basis")]][$row[csf("color")]][$row[csf("yarn_type")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]]=="")
			{
				$item_check[$row[csf("booking_id")]][$row[csf("receive_basis")]][$row[csf("color")]][$row[csf("yarn_type")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]]=$row[csf("yarn_comp_type1st")];
				$min_date=$row[csf("transaction_date")];
				$max_date=$row[csf("transaction_date")];
				$b++;
			}
			else
			{
				if(strtotime($row[csf("transaction_date")])>strtotime($max_date)) $max_date=$row[csf("transaction_date")];
			}
			$req_wo_recv_arr[$row[csf("booking_id")]][$row[csf("receive_basis")]][$row[csf("color")]][$row[csf("yarn_type")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]]['booking_id']=$row[csf("booking_id")];
			$req_wo_recv_arr[$row[csf("booking_id")]][$row[csf("receive_basis")]][$row[csf("color")]][$row[csf("yarn_type")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]]['min_date']=$min_date;
			$req_wo_recv_arr[$row[csf("booking_id")]][$row[csf("receive_basis")]][$row[csf("color")]][$row[csf("yarn_type")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]]['max_date']=$max_date;
			
			$req_wo_recv_arr[$row[csf("booking_id")]][$row[csf("receive_basis")]][$row[csf("color")]][$row[csf("yarn_type")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]]['receive_basis']=$row[csf("receive_basis")];
			$req_wo_recv_arr[$row[csf("booking_id")]][$row[csf("receive_basis")]][$row[csf("color")]][$row[csf("yarn_type")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]]['recv_qnty']+=$row[csf("recv_qnty")]-$rcv_rtn_data[$row[csf("mst_id")]][$row[csf("prod_id")]]["cons_quantity"];
			$req_wo_recv_arr[$row[csf("booking_id")]][$row[csf("receive_basis")]][$row[csf("color")]][$row[csf("yarn_type")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]]['recv_amt']+=$row[csf("recv_amt")]-($rcv_rtn_data[$row[csf("mst_id")]][$row[csf("prod_id")]]["cons_amount"]/$row[csf("exchange_rate")]);
			
		}
		//echo $b."<pre>";print_r($req_wo_recv_arr[15678][1]);die;
		
		
		$wo_qty_arr=sql_select("select a.id as wo_id, b.color_name as color, b.yarn_type as yarn_type, b.yarn_count as yarn_count_id, b.yarn_comp_type1st as yarn_comp_type1st, sum(b.supplier_order_quantity) as qty from wo_non_order_info_mst a, wo_non_order_info_dtls b where a.id=b.mst_id and a.company_name=$cbo_company_name and a.item_category in (1) and a.pay_mode<>2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
		group by a.id,b.color_name,b.yarn_type,b.yarn_count,b.yarn_comp_type1st");
		$wo_pipe_array=array();
		foreach($wo_qty_arr as $row)
		{
			$wo_pipe_array[$row[csf("wo_id")]][$row[csf("color")]][$row[csf("yarn_type")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]]=$row[csf("qty")];
		}
		/*echo "select a.id as pi_id, b.color_id as color, b.yarn_type as yarn_type, b.count_name as yarn_count_id, b.yarn_composition_item1 as yarn_comp_type1st, sum(b.quantity) as qty from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.importer_id=$cbo_company_name and a.item_category_id in (1) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, b.color_id, b.yarn_type, b.count_name, b.yarn_composition_item1";die;*/
		$pi_qty_arr=sql_select("select a.id as pi_id, b.color_id as color, b.yarn_type as yarn_type, b.count_name as yarn_count_id, b.yarn_composition_item1 as yarn_comp_type1st, sum(b.quantity) as qty from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.importer_id=$cbo_company_name and a.item_category_id in (1) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, b.color_id, b.yarn_type, b.count_name, b.yarn_composition_item1"); 
		$pi_pipe_array=array();
		foreach($pi_qty_arr as $row)
		{
			$pi_pipe_array[$row[csf("pi_id")]][$row[csf("color")]][$row[csf("yarn_type")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]]=$row[csf("qty")];
		}
		
		//echo $cbo_based_on;
		//echo "<pre>";print_r($pi_pipe_array[5710]); die;
		
		if($cbo_based_on==3)
		{
			//print_r($pi_wo_dtls_id_all);die;
			if($cbo_year>0)
			{
				if($db_type==0) $year_cond=" and year(a.insert_date)='$cbo_year'"; else $year_cond=" and to_char(a.insert_date,'YYYY')='$cbo_year'";
			}
			if(!empty($pi_wo_dtls_id_all))
			
			{
				//echo $pi_wo_dtls_id_all;d
				$pi_wo_dtls_id_all_arr=array_chunk(array_unique($pi_wo_dtls_id_all),999);
				$sql_req_wo="select a.id, a.requ_no, b.job_no, b.style_ref_no, b.booking_no, a.requ_prefix_num,a.requisition_date, a.company_id, b.buyer_id, b.id as req_dtls_id, b.color_id, b.count_id, b.composition_id, b.com_percent, b.yarn_type_id, b.cons_uom as req_uom, b.quantity as req_qnty, b.rate as req_rate, b.amount as req_amt, b.yarn_inhouse_date, d.id as wo_id, d.wo_number_prefix_num, d.wo_date, e.id as wo_dtls_id, e.color_name as wo_color, e.yarn_count as wo_count, e.yarn_comp_type1st as wo_yarn_comp_type1st, e.yarn_comp_percent1st as wo_yarn_comp_percent1st, e.yarn_comp_type2nd, e.yarn_comp_percent2nd,  e.yarn_type as wo_yarn_type, e.uom as wo_uom, e.supplier_order_quantity as wo_qnty, e.rate as wo_rate, e.amount as wo_amount
				from inv_purchase_requisition_mst a,  inv_purchase_requisition_dtls b 
				left join  wo_non_order_info_dtls e  on  b.id=e.requisition_dtls_id and e.status_active=1 and e.is_deleted=0 
				left join wo_non_order_info_mst d on d.id=e.mst_id  and d.status_active=1 and d.is_deleted=0
				where a.id=b.mst_id and a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=70 and a.company_id='$cbo_company_name' $str_cond $year_cond ";
				//echo $sql_req_wo;die;
				if(!empty($pi_wo_dtls_id_all_arr))
				{
					$p=1;
					foreach($pi_wo_dtls_id_all_arr as $pi_wo_dtls_id)
					{
						if($p==1) $sql_req_wo .=" and (e.id in(".implode(',',$pi_wo_dtls_id).")"; else $sql_req_wo .=" or e.id in(".implode(',',$pi_wo_dtls_id).")";
						$p++;
					}
					$sql_req_wo .=" ) ";
				}
				$sql_req_wo .=" order by  a.id desc, b.color_id, b.yarn_type_id, b.count_id, b.composition_id ";	
			}
			
		}
		else
		{
			if($cbo_year>0)
			{
				if($db_type==0) $year_cond=" and year(a.insert_date)='$cbo_year'"; else $year_cond=" and to_char(a.insert_date,'YYYY')='$cbo_year'";
			}
			$sql_req_wo="SELECT a.id, a.requ_no, b.job_no, b.style_ref_no, b.booking_no, a.requ_prefix_num,a.inserted_by,a.insert_date,a.updated_by,a.update_date,a.requisition_date, a.company_id, b.buyer_id, b.id as req_dtls_id, b.color_id, b.count_id, b.composition_id, b.com_percent, b.yarn_type_id, b.cons_uom as req_uom, b.quantity as req_qnty, b.rate as req_rate, b.amount as req_amt, b.yarn_inhouse_date, d.id as wo_id, d.wo_number_prefix_num, d.wo_date, e.id as wo_dtls_id, e.color_name as wo_color, e.yarn_count as wo_count, e.yarn_comp_type1st as wo_yarn_comp_type1st, e.yarn_comp_percent1st as wo_yarn_comp_percent1st, e.yarn_comp_type2nd, e.yarn_comp_percent2nd, e.yarn_type as wo_yarn_type, e.uom as wo_uom, e.supplier_order_quantity as wo_qnty, e.rate as wo_rate, e.amount as wo_amount, b.remarks,d.inserted_by as wo_inserted_by,
			d.insert_date as wo_insert_date,d.updated_by as wo_updated_by,d.update_date as wo_update_date
			from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b 
			left join  wo_non_order_info_dtls e  on  b.id=e.requisition_dtls_id and e.status_active=1 and e.is_deleted=0 
			left join wo_non_order_info_mst d on d.id=e.mst_id  and d.status_active=1 and d.is_deleted=0
			where a.id=b.mst_id and a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id='$cbo_company_name' and a.entry_form=70 $str_cond $year_cond $style_ref_cond
			order by a.id desc, b.color_id, b.count_id, b.yarn_type_id, b.composition_id";
		}
		//echo $sql_req_wo;//die();
		$req_result=sql_select($sql_req_wo);
		//echo "jahid";die;
		ob_start();
		?>
		 <style>
			.wrd_brk{word-break: break-all;word-wrap: break-word;}          
		</style>
	    <div style="width:4570px">
	        <table width="3800" cellpadding="0" cellspacing="0" id="caption"  align="left">
	        <tr>
	            <td align="center" width="100%"  class="form_caption" colspan="47"><strong style="font-size:18px">Company Name:<? echo " ". $company_arr[str_replace("'","",$cbo_company_name)]; ?></strong></td>
	        </tr> 
	        <tr>  
	            <td align="center" width="100%" class="form_caption"  colspan="47"><strong style="font-size:18px"><? echo $report_title; ?></strong></td>
	        </tr>
	        <tr>  
	            <td align="center" width="100%"  class="form_caption"  colspan="47"><strong style="font-size:18px">From : <? echo $txt_date_from; ?> To : <? echo $txt_date_to; ?></strong></td>
	        </tr>
	        </table>
	    	<br />
	        <table width="3850"  align="left">
	        	<tr>
	            	<td style="font-size:18; font-weight:bold;">Based on Requisition</td>
	            </tr>
	        </table>
	        <br />       
	            <table width="4900" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_1"  align="left">
	            <thead>
	            	<tr>
	                	<th width="30" rowspan="2">Sl</th>
	                	<th colspan="19"> Requisiton Details</th>
	                    <th colspan="14">Work Order Details</th>
	                    <th colspan="12">PI Details</th>
	                    <th colspan="6">BTB LC Details</th>
	                    <th colspan="7">Matarials Received Information</th>
	                </tr>
	                <tr>
	                    <th width="50">Req. No</th>
	                    <th width="70">Req. Date</th>
	                    <th width="100">Buyer</th>
	                    <th width="100">Sales Order No.</th>
	                    <th width="110">Fab. Booking No.</th>
	                    <th width="120">Style</th>
	                    <th width="80">Yarn Color</th>
	                    <th width="50">Count</th>
	                    <th width="150">Composition</th>
	                    <th width="80">Yarn Type</th>
	                    <th width="50">UOM</th>
	                    <th width="80">Req. Qnty.</th>
	                    <th width="70">Req. Rate</th>
	                    <th width="100">Req. Amount</th>
	                    <th width="75">Yarn Inhouse Date</th>
	                    <th width="75">Insert User Name</th>
	                    <th width="60">Insert Date<br> and Time</th>
	                    <th width="75">Insert Update <br> User Name</th>
	                    <th width="60">Insert Update<br> Date and Time</th>
	                    <th width="50">WO No.</th>
	                    <th width="80">Yarn Color</th>
	                    <th width="50">Count</th>
	                    <th width="250">Composition</th>
	                    <th width="80">Yarn Type</th>
	                    <th width="50">UOM</th>
	                    <th width="80">WO Qnty</th>
	                    <th width="70">WO Rate</th>
	                    <th width="100">WO Amount</th>
						<th width="100">Insert User Name</th>
	                    <th width="100">Insert Date<br> and Time</th>
	                    <th width="100">Insert Update <br> User Name</th>
	                    <th width="100">Insert Update<br> Date and Time</th>
	                    <th width="150">Supplier</th>
	                    <th width="100">PI No.</th>
	                    <th width="70">PI Date</th>
	                    <th width="70">Last Ship Date</th>
	                    <th width="80">Yarn Color</th>
	                    <th width="50">Count</th>
	                    <th width="250">Composition</th>
	                    <th width="80">Yarn Type</th>
	                    <th width="50">UOM</th>
	                    <th width="80">PI Qnty</th>
	                    <th width="70">PI Rate</th>
	                    <th width="100">PI Amount</th>
	                    <th width="70">Currency</th>
	                    <th width="70">LC Date</th>
	                    <th width="100">LC No</th>
	                    <th width="100">Issuing Bank</th>
	                    <th width="70">Pay Term</th>
	                    <th width="80">Tenor</th>
	                    <th width="100">LC Amount</th>
	                    <th width="80">MRR Qnty</th>
	                    <th width="100">MRR Value</th>
	                    <th width="80">Short Value</th>
	                    <th width="80">Pipe Line</th>
	                    <th width="70">1st Rcv Date</th>
	                    <th width="100">Last Rcv Date</th>
	                    <th width="100">Remarks</th>
	                </tr>
	            </thead>
	        </table>
	        <div style="width:4920px; overflow-y:scroll; max-height:200px;font-size:12px; overflow-x:hidden;" id="scroll_body" align="left">
	        <table width="4900px" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left">
	            <tbody>
	            <?
	            $k=1;
				
				$array_check=array();$m=1;$q=1;
	            foreach($req_result as $row)
	            {
	            	if($row[csf("wo_dtls_id")]=='') 
	            	{
	            		$row[csf("wo_dtls_id")]=0;
	            	}
	            	
					if(!in_array($row[csf("id")],$temp_arr_req))
					{
						$temp_arr_req[]=$row[csf("id")];
						if($m%2==0)$bgcolor="#F8F9D0";else $bgcolor="#C8C4FD"; 
						$m++;
					}
					
					$mrr_qnty=$pipe_wo_qnty=$pipe_pi_qnty=$pipe_line=0;$min_date=$max_date="";
					$mrr_value=$short_value=0;
					$booking_id=$receive_basis="";			

					$id=$row_result[csf('id')];
					$reqsn_rate=$row[csf("amount")]/$row[csf("req_quantity")];
					if($req_wo_recv_arr[$row[csf("wo_id")]][2][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]]['recv_amt']!="")
					{
						$wo_pi_ids=$row[csf("wo_id")];
					}
					else
					{
						$wo_pi_ids=$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"];
					}
					
					if($mrr_check_prev[$wo_pi_ids][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]]=="")
	                {
						$mrr_check_prev[$wo_pi_ids][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]]=$row[csf("wo_id")];
						if($req_wo_recv_arr[$row[csf("wo_id")]][2][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]]['recv_qnty']!="")
						$mrr_qnty=$req_wo_recv_arr[$row[csf("wo_id")]][2][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]]['recv_qnty']; 
						else $mrr_qnty=$req_wo_recv_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]][1][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]]['recv_qnty'];
						
						
						
						if($req_wo_recv_arr[$row[csf("wo_id")]][2][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]]['recv_amt']!="")
						{
							$mrr_value=$req_wo_recv_arr[$row[csf("wo_id")]][2][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]]['recv_amt'];
							$min_date=$req_wo_recv_arr[$row[csf("wo_id")]][2][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]]['min_date'];
							$max_date=$req_wo_recv_arr[$row[csf("wo_id")]][2][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]]['max_date'];
							$short_value=$row[csf("wo_amount")]-$mrr_value;
							$booking_id=$req_wo_recv_arr[$row[csf("wo_id")]][2][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]]['booking_id'];
							
							$receive_basis=2;
						}
						else 
						{
							
							$mrr_value=$req_wo_recv_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]][1][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]]['recv_amt'];
							$min_date=$req_wo_recv_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]][1][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]]['min_date'];
							$max_date=$req_wo_recv_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]][1][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]]['max_date'];
							
							$short_value=$row[csf("wo_amount")]-$pi_data_arr[$row[csf("wo_dtls_id")]]["amount"];
							$booking_id=$req_wo_recv_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]][1][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]]['booking_id'];
							$receive_basis=1;
						}
						
					}
					
					if($pi_data_arr[$row[csf("wo_dtls_id")]]["amount"]=="") $pi_data_arr[$row[csf("wo_dtls_id")]]["amount"]=0;
					if($mrr_value=="") $mrr_value=0;
					if($mrr_qnty=="") $mrr_qnty=0;
					if($mrr_value>0 && $mrr_qnty>0)  $recv_rate=$mrr_value/$mrr_qnty;
					
					$receiving_cond=0;
					if($cbo_receive_status==1 && ($mrr_value=="" || $mrr_value==0)) $receiving_cond=1;
					if($cbo_receive_status==2 && $receive_basis==2 && $row[csf("wo_amount")]>$mrr_value && $mrr_value>0) $receiving_cond=1;
					if($cbo_receive_status==2 && $receive_basis==1 && $pi_data_arr[$row[csf("wo_dtls_id")]]["amount"]>$mrr_value && $mrr_value>0) $receiving_cond=1;
					if($cbo_receive_status==3 && $receive_basis==2 && $mrr_value>=$row[csf("wo_amount")] && $mrr_value>0) $receiving_cond=1;
					if($cbo_receive_status==3 && $receive_basis==1 && $mrr_value>=$pi_data_arr[$row[csf("wo_dtls_id")]]["amount"] && $mrr_value>0) $receiving_cond=1;
					if($cbo_receive_status==4 && $receive_basis==2 && $mrr_value < $row[csf("wo_amount")]) $receiving_cond=1;
					if($cbo_receive_status==4 && $receive_basis==1 && $mrr_value < $pi_data_arr[$row[csf("wo_dtls_id")]]["amount"]) $receiving_cond=1;
					if($cbo_receive_status==5) $receiving_cond=1;
					
					if($receiving_cond==1)
					{
						?>
	                    <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
	                        <td valign="middle"  class="wrd_brk" width="30"  align="center" title="<? echo $cbo_receive_status."=".$receive_basis."=".$mrr_value."=".$row[csf("wo_amount")]."=".$pi_data_arr[$row[csf("wo_dtls_id")]]["amount"]; ?>"><p><? echo $k;//$row_result[csf('id')];?></p></td>
	                        <td valign="middle" class="wrd_brk" width="50" align="center" title="<? echo $row[csf("id")]?>"><p><? echo $row[csf("requ_prefix_num")]; ?></p></td>
	                        <td valign="middle" class="wrd_brk" width="70" align="center"><p>&nbsp;<? if($row[csf("requisition_date")]!="" && $row[csf("requisition_date")]!='0000-00-00') echo change_date_format($row[csf("requisition_date")]); ?></p></td>
	                        <td valign="middle"  class="wrd_brk" width="100"><p><? echo $buyer_arr[$row[csf("buyer_id")]]; ?></p></td>
	                        <td valign="middle"  class="wrd_brk" width="100"><p><? echo $row[csf("job_no")]; ?></p></td>
	                        <td valign="middle"  class="wrd_brk" width="110"><p><? echo $row[csf("booking_no")]; ?></p></td>
	                        <td valign="middle"  class="wrd_brk" width="120"><p><? echo $row[csf("style_ref_no")]; ?></p></td>
	                        <td valign="middle" class="wrd_brk" width="80"><p><? echo $color_arr[$row[csf("color_id")]]; ?></p></td>
	                        <td valign="middle" class="wrd_brk" width="50" align="center"><p>&nbsp;<? echo $yarnCount_arr[$row[csf("count_id")]]; ?></p></td>
	                        <td valign="middle" class="wrd_brk" width="150"><p><? echo $composition[$row[csf("composition_id")]]." ".$row[csf("com_percent")]."%"; ?></p></td>
	                        <td valign="middle" class="wrd_brk" width="80"><p><? echo $yarn_type[$row[csf("yarn_type_id")]]; ?></p></td>
	                        <td valign="middle" class="wrd_brk" width="50"><p><? echo $unit_of_measurement[$row[csf("req_uom")]]; ?></p></td>
	                        <td valign="middle" class="wrd_brk" width="80" align="right"><p><? echo number_format($row[csf("req_qnty")],2); ?></p></td>
	                        <td valign="middle" class="wrd_brk" width="70" align="right"><p><? echo number_format($row[csf("req_rate")],2); ?></p></td>
	                        <td valign="middle" class="wrd_brk" width="100" align="right"><p><? echo number_format($row[csf("req_amt")],2); ?></p></td>
	                        <td valign="middle" class="wrd_brk" width="75"  align="center"><p>&nbsp;<? if($row[csf("yarn_inhouse_date")]!="" && $row[csf("yarn_inhouse_date")]!="0000-00-00") echo change_date_format($row[csf("yarn_inhouse_date")]); ?></p></td>
							<td valign="middle"  class="wrd_brk" width="75"  align="center"><p><? echo $user_arr[$row[csf("inserted_by")]]; ?></p></td>
							<td valign="middle" class="wrd_brk" width="60"  align="center"><p>&nbsp;<? if($row[csf("insert_date")]!="" && $row[csf("insert_date")]!="0000-00-00") echo $row[csf("insert_date")]; ?></p></td>
							<td valign="middle" class="wrd_brk" width="75"  align="center"><p><? echo $user_arr[$row[csf("updated_by")]]; ?></td>
							<td valign="middle" class="wrd_brk" width="60"  align="center"><p>&nbsp;<? if($row[csf("update_date")]!="" && $row[csf("update_date")]!="0000-00-00") echo $row[csf("update_date")]; ?></p></td>
							
	                        <td valign="middle" class="wrd_brk" width="50" align="center" title="<? echo $row[csf("wo_id")]?>"><p><? echo $row[csf("wo_number_prefix_num")]; ?></p></td>
	                        <td valign="middle" class="wrd_brk" width="80"><p><? echo $color_arr[$row[csf("wo_color")]]; ?></p></td>
	                        <td valign="middle" class="wrd_brk" width="50" align="center"><p>&nbsp;<? echo $yarnCount_arr[$row[csf("wo_count")]]; ?></p></td>
	                        <td valign="middle" class="wrd_brk" width="250"><p>
	                        <? 
	                        if($row[csf("yarn_comp_type2nd")]>0) $wo_com_percent2=$row[csf("yarn_comp_percent2nd")]."%"; else $wo_com_percent2=" ";
	                        if ($row[csf("wo_yarn_comp_percent1st")] >0 ) $wo_com_percent1=$row[csf("wo_yarn_comp_percent1st")]."%"; else $wo_com_percent1 = "";
	                        echo $composition[$row[csf("wo_yarn_comp_type1st")]]." ".$wo_com_percent1." ".$composition[$row[csf("yarn_comp_type2nd")]]." ".$wo_com_percent2; ?></p></td>
	                        <td valign="middle" class="wrd_brk" width="80"><p><? echo $yarn_type[$row[csf("wo_yarn_type")]]; ?></p></td>
	                        <td valign="middle" class="wrd_brk" width="50"><p><? echo $unit_of_measurement[$row[csf("wo_uom")]]; ?></p></td>
	                        <td valign="middle" class="wrd_brk" width="80" align="right"><p><? echo number_format($row[csf("wo_qnty")],2); ?></p></td>
	                        <td valign="middle" class="wrd_brk" width="70" align="right"><p><? echo number_format($row[csf("wo_rate")],2); ?></p></td>
	                        <td valign="middle" class="wrd_brk" width="100" align="right"><p><? echo number_format($row[csf("wo_amount")],2); ?></p></td>
	                        <td valign="middle" class="wrd_brk" width="100" align="right"><p><? echo $user_arr[$row[csf("wo_inserted_by")]]; ?></p></td>
	                        <td valign="middle" class="wrd_brk" width="100" align="right"><p><? if($row[csf("wo_insert_date")]!="" && $row[csf("wo_insert_date")]!="0000-00-00") echo $row[csf("wo_insert_date")]; ?></p></td>
	                        <td valign="middle" class="wrd_brk" width="100" align="right"><p><? echo $user_arr[$row[csf("wo_updated_by")]]; ?></p></td>
	                        <td valign="middle" class="wrd_brk" width="100" align="right"><p><? if($row[csf("wo_update_date")]!="" && $row[csf("wo_update_date")]!="0000-00-00") echo $row[csf("wo_update_date")]; ?></p></td>
	                       
	                        <td valign="middle" class="wrd_brk" width="150"><p><? $suplier_pi_arr=array_unique(explode(",",chop($pi_data_arr[$row[csf("wo_dtls_id")]]["pi_suplier"]," , ")));
	                        //echo "10**".$row[csf("wo_dtls_id")]; die; //print_r($suplier_pi_arr);
	                        $supplier='';
	                        foreach ($suplier_pi_arr as $value) 
	                        {
	                        	$supplier.=$supplier_arr[$value].",";
	                        }
	                        echo chop($supplier,","); ?></p></td>
	                        <td valign="middle" class="wrd_brk" width="100"><p><? $pi_num=implode(" , ",array_unique(explode(",",chop($pi_data_arr[$row[csf("wo_dtls_id")]]["pi_number"]," , ")))); echo $pi_num; ?></p></td>
	                        <td valign="middle" class="wrd_brk" width="70"><p>
	                        <? 
	                        $pi_date_arr=array_unique(explode(",",chop($pi_data_arr[$row[csf("wo_dtls_id")]]["pi_date"]," , ")));
	                        $pi_date='';
	                        foreach ($pi_date_arr as $value) 
	                        {
	                        	if($value !="" && $value!="0000-00-00")
	                        	{
	                        		$pi_date.=change_date_format($value)."</br>";
	                        	}
	                        }
	                        echo chop($pi_date,"</br>");
	                        ?></p></td>
	                        <td valign="middle" class="wrd_brk" width="70"><p>
	                        <? 
	                        $pi_last_ship_date_arr=array_unique(explode(",",chop($pi_data_arr[$row[csf("wo_dtls_id")]]["last_shipment_date"]," , "))); 

	                        $shipment_date='';
	                        foreach ($pi_last_ship_date_arr as $value) 
	                        {
	                        	if($value !="" && $value!="0000-00-00")
	                        	{
	                        		$shipment_date.=change_date_format($value)."</br>";
	                        	}
	                        }
	                       	// if($pi_last_ship_date_arr[0]!="" && $pi_last_ship_date_arr[0]!="0000-00-00") echo change_date_format($pi_last_ship_date_arr[0]); 
	                        echo chop($shipment_date,"</br>");
	                        ?></p></td>
	                        <td valign="middle" class="wrd_brk" width="80" title="<? echo "color id=".$pi_data_arr[$row[csf("wo_dtls_id")]]["color_id"];?>"><p><? echo  $color_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["color_id"]]; ?></p></td>
	                        <td valign="middle" class="wrd_brk" width="50" title="<? echo "count id=".$pi_data_arr[$row[csf("wo_dtls_id")]]["count_name"];?>"><p>&nbsp;<? echo  $yarnCount_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["count_name"]]; ?></p></td>
	                        <td valign="middle" class="wrd_brk" width="250" title="<? echo "count id=".$pi_data_arr[$row[csf("wo_dtls_id")]]["yarn_composition_item1"];?>"><p>
	                        <?
	                        if($pi_data_arr[$row[csf("wo_dtls_id")]]["yarn_composition_item2"]>0) $com_percent2=$pi_data_arr[$row[csf("wo_dtls_id")]]["yarn_comp_percent2nd"]."%"; else $com_percent2="";
	                        if($pi_data_arr[$row[csf("wo_dtls_id")]]["yarn_composition_item1"]>0) $com_percent=$pi_data_arr[$row[csf("wo_dtls_id")]]["yarn_composition_percentage1"]."%"; else $com_percent="";
	                        echo  $composition[$pi_data_arr[$row[csf("wo_dtls_id")]]["yarn_composition_item1"]]." ".$com_percent." ".$composition[$pi_data_arr[$row[csf("wo_dtls_id")]]["yarn_composition_item2"]]." ".$com_percent2;
	                        ?></p></td>
	                        <td valign="middle" class="wrd_brk" width="80" title="<? echo "yarn type id=".$pi_data_arr[$row[csf("wo_dtls_id")]]["yarn_type"];?>"><p><? echo  $yarn_type[$pi_data_arr[$row[csf("wo_dtls_id")]]["yarn_type"]]; ?></p></td>
	                        <td valign="middle" class="wrd_brk" width="50"><p><? echo  $unit_of_measurement[$pi_data_arr[$row[csf("wo_dtls_id")]]["uom"]]; ?></p></td>
	                        <td valign="middle" class="wrd_brk" width="80" align="right"><p><? $pi_qnty=$pi_data_arr[$row[csf("wo_dtls_id")]]["quantity"]; echo  number_format($pi_qnty,2); ?></p></td>
	                        <td valign="middle" class="wrd_brk" width="70" align="right"><p><? $pi_rate=$pi_data_arr[$row[csf("wo_dtls_id")]]["rate"]; echo  number_format($pi_rate,2); ?></p></td>
	                        <td valign="middle" class="wrd_brk" width="100" align="right"><p><? $pi_amt=$pi_data_arr[$row[csf("wo_dtls_id")]]["amount"]; echo  number_format($pi_amt,2); ?></p></td>
	                        <td valign="middle" class="wrd_brk" width="70"><p><? $pi_curency_arr=array_unique(explode(",",chop($pi_data_arr[$row[csf("wo_dtls_id")]]["currency_id"]," , ")));
	                        $currency_val='';
	                        foreach ($pi_curency_arr as $value) 
	                        {
	                        	$currency_val.=$currency[$value].",";
	                        }
	                        echo chop($currency_val,",");
	                        //echo  $currency[$pi_curency_arr[0]]; ?></p></td>
	                        <?
	                        $btb_lc_no=$btb_issue_bank=$btb_pay_term=$btb_tenor="";$btb_lc_amount=0; $btb_tenor="";$btb_lc_date="";
	                        if(!in_array($pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"],$temp_arr_btb))
	                        {
	                            $temp_arr_btb[]=$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"];
	                            $btb_lc_no_arr=array_unique(explode(",",chop($btb_data_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]]["lc_number"]," , ")));
	                            $btb_lc_no=implode(" , ",$btb_lc_no_arr);

	                            $btb_issue_bank_arr=array_unique(explode(",",chop($btb_data_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]]["issuing_bank_id"]," , "))); 
	                            foreach ($btb_issue_bank_arr as $value) 
		                        {
		                        	$btb_issue_bank.=$bank_arr[$value].",";
		                        }

	                            $btb_pay_tarm_arr=array_unique(explode(",",chop($btb_data_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]]["payterm_id"]," , ")));
		                        foreach ($btb_pay_tarm_arr as $value) 
		                        {
		                        	$btb_pay_term.=$pay_term[$value].",";
		                        }

	                            $btb_tenor_arr=array_unique(explode(",",chop($btb_data_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]]["tenor"]," , ")));
	                           	$btb_tenor=implode(" Days, ",$btb_tenor_arr);

	                            $btb_lc_amount=$btb_data_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]]["lc_value"];
	                            $btb_lc_date_arr=array_unique(explode(",",chop($btb_data_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]]["lc_date"]," , "))); 
	                            $btb_lc_date='';
		                        foreach ($pi_last_ship_date_arr as $value) 
		                        {
		                        	if($value !="" && $value!="0000-00-00")
		                        	{
		                        		$btb_lc_date.=change_date_format($value)."</br>";
		                        	}
		                        }
	                        }
	                        else
	                        {
	                            $btb_lc_no=$btb_issue_bank=$btb_pay_term="";
	                            $btb_lc_amount=0;
	                            $btb_tenor="";
	                            $btb_lc_date="";
	                        }
	                        ?>
	                        <td valign="middle" class="wrd_brk" width="70"><p>&nbsp;<? echo chop($btb_lc_date,"</br>");  ?></p></td>
	                        
	                        <td valign="middle" class="wrd_brk" width="100"><p><?  echo chop( $btb_lc_no,","); ?></p></td>
	                        <td valign="middle" class="wrd_brk" width="100"><p><? echo  chop( $btb_issue_bank,","); ?></p></td>
	                        <td valign="middle" class="wrd_brk" width="70"><p><? echo chop( $btb_pay_term,",");  ?></p></td>
	                        <td valign="middle" class="wrd_brk" width="80"><p><? if ($btb_tenor!='') echo $btb_tenor." Days";  ?></p></td>
	                        <td valign="middle" class="wrd_brk" width="100" align="right"><p><? if($btb_lc_amount>0) echo number_format($btb_lc_amount,2); ?></p></td>
	                        <?
	                        //if(!in_array($row[csf("wo_id")],$temp_arr_rcv))
							$pipe_line="";
							if($mrr_check[$wo_pi_ids][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]]=="")
	                        {
								$mrr_check[$wo_pi_ids][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]]=$row[csf("color_id")];
								$pipe_wo_qnty=$wo_pipe_array[$wo_pi_ids][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]];
								$pipe_pi_qnty=$pi_pipe_array[$wo_pi_ids][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]];
								$pipe_line=(($pipe_wo_qnty+$pipe_pi_qnty)-$mrr_qnty);
								$total_pipe_line += $pipe_line;
	                            if($mrr_qnty>0)
	                            { 
									$pipe_mrr_qnty=$mrr_qnty;
									
	                                ?>
	                                <td valign="middle" class="wrd_brk" width="80" align="right" title="<? echo $wo_pi_ids."=".$receive_basis."=".$row[csf("color_id")]."=".$row[csf("yarn_type_id")]."=".$row[csf("count_id")]."=".$row[csf("composition_id")];?>"><p><a href="##" onclick="fn_mrr_details('<? echo $booking_id;?>','<? echo $receive_basis;?>','<? echo $row[csf("color_id")];?>','<? echo $row[csf("yarn_type_id")];?>','<? echo $row[csf("count_id")];?>','<? echo $row[csf("composition_id")];?>','receive_details_popup')"><? echo number_format($mrr_qnty,2); ?></a></p></td>
	                                <?
									 $total_mrr_qnty  += $mrr_qnty;
	                    			 //
	                            }
	                            else
	                            {
	                                ?>
	                                <td valign="middle" class="wrd_brk" width="80" align="right" title="<? echo $wo_pi_ids."=".$receive_basis."=".$row[csf("color_id")]."=".$row[csf("yarn_type_id")]."=".$row[csf("count_id")]."=".$row[csf("composition_id")];?>"><p></p></td>
	                                <?
	                            }
	                        }
	                        else
	                        {
	                            $mrr_qnty=$mrr_value=0;
								//if($mrr_check[$wo_pi_ids][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]]=="")$pipe_mrr_qnty=0;
	                            ?>
	                            <td valign="middle" class="wrd_brk" width="80" align="right" title="<? echo $wo_pi_ids."=".$receive_basis."=".$row[csf("color_id")]."=".$row[csf("yarn_type_id")]."=".$row[csf("count_id")]."=".$row[csf("composition_id")];?>"><p></p></td>
	                            <?
	                        }
	                        ?>
	                        <td valign="middle" class="wrd_brk" width="100" align="right"><p><? if($mrr_value>0) { echo number_format($mrr_value,2); $total_mrr_value += $mrr_value; }?></p></td>
	                        <td valign="middle" class="wrd_brk" align="right" width="80"><p><? echo number_format($short_value,2); ?></p></td>

	                        <td valign="middle" class="wrd_brk" align="right" width="80" title="<? echo $wo_pi_ids."=".$pipe_wo_qnty."=".$pipe_pi_qnty."=".$pipe_mrr_qnty."=".$mrr_qnty; ?>"><? echo number_format($pipe_line,2); ?></td>
	                        <td valign="middle" class="wrd_brk" width="70" align="center"><? if($min_date!="" && $min_date!="0000-00-00" && $mrr_qnty>0) echo change_date_format($min_date);?></td>
	                        <td valign="middle" class="wrd_brk" align="center" width="100"><? if($max_date!="" && $max_date!="0000-00-00" && $mrr_qnty>0) echo change_date_format($max_date);?></td>
							<td valign="middle" class="wrd_brk" align="center" width="100"><? echo $row[csf("remarks")];?></td>
	                    </tr>
	                    <?
	                    $k++;
	                    $total_req_qty   += $row[csf("req_qnty")];
	                    $total_req_amount+=$row[csf("req_amt")];
	                    $total_wo_qty+=$row[csf("wo_qnty")];
	                    $total_wo_amount+=$row[csf("wo_amount")];
	                    $total_pi_qnty   += $pi_data_arr[$row[csf("wo_dtls_id")]]["quantity"];
	                    $total_pi_amt    += $pi_data_arr[$row[csf("wo_dtls_id")]]["amount"];
	                    $total_short_amt += $short_amt;
	                    
					}
	            }
	            ?>
	            </tbody>
	        </table>
	        </div>
	        <table cellspacing="0" width="4900"  border="1" rules="all" class="rpt_table" id="report_table_footer" >
	            <tfoot>
	            	<th width="30">&nbsp;</th>
					<th width="50">&nbsp;</th>
					<th width="70">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="110">&nbsp;</th>
					<th width="120">&nbsp;</th>
					<th width="80">&nbsp;</th>
					<th width="50">&nbsp;</th>
					<th width="150">&nbsp;</th>
					<th width="80">&nbsp;</th>
					<th width="50"><strong>Total:</strong></th>
					<th width="80"><? echo number_format($total_req_qty,2);?></th>
					<th width="70">&nbsp;</th>
					<th width="100"><? echo number_format($total_req_amount,2);?></th>
					<th width="75">&nbsp;</th>
					<th width="75">&nbsp;</th>
					<th width="60">&nbsp;</th>
					<th width="75">&nbsp;</th>
					<th width="60">&nbsp;</th>
					<th width="50">&nbsp;</th>
					<th width="80">&nbsp;</th>
					<th width="50">&nbsp;</th>
					<th width="250">&nbsp;</th>
					<th width="80">&nbsp;</th>
					<th width="50">&nbsp;</th>
					<th width="80"><? echo number_format($total_wo_qty,2);?></th>
					<th width="70">&nbsp;</th>
					<th width="100"><? echo number_format($total_wo_amount,2);?></th>
					<th width="100">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="150">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="70">&nbsp;</th>
					<th width="70">&nbsp;</th>
					<th width="80">&nbsp;</th>
					<th width="50">&nbsp;</th>
					<th width="250">&nbsp;</th>
					<th width="80">&nbsp;</th>
					<th width="50">&nbsp;</th>
					<th width="80"><? echo number_format($total_pi_qnty,2); ?></th>
					<th width="70">&nbsp;</th>
					<th width="100"><? echo number_format($total_pi_amt,2); ?></th>
					<th width="70">&nbsp;</th>
					<th width="70">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="100">&nbsp; </th>
					<th width="70">&nbsp;</th>
					<th width="80">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="80"><? echo number_format($total_mrr_qnty,2); ?></th>
					<th width="100"><? echo number_format($total_mrr_value,2); ?></th>
					<th width="80"><? echo number_format($total_short_amt,2); ?></th>
					<th width="80"><? echo number_format($total_pipe_line,2); ?></th>
					<th width="70">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="100">&nbsp;</th>

	            </tfoot>        
			</table>
	        
	        </div>
	    	<br />
	        <div style="width:2770px" >
	        <?
			
			if($cbo_based_on==3)
			{
				if(!empty($pi_wo_dtls_id_all_arr))
				{
					$pi_wo_dtls_id_all_arr=array_chunk(array_unique($pi_wo_dtls_id_all),999);
					$sql_wo_independ="select d.company_name, d.ref_closing_status, d.wo_basis_id, d.is_approved, d.pay_mode, d.id as wo_id, d.wo_number_prefix_num, d.wo_date, d.supplier_id, e.id as wo_dtls_id, e.color_name as wo_color, e.yarn_count as wo_count, e.yarn_comp_type1st as wo_yarn_comp_type1st, e.yarn_comp_percent1st as wo_yarn_comp_percent1st, e.yarn_comp_type2nd, e.yarn_comp_percent2nd, e.yarn_type as wo_yarn_type, e.uom as wo_uom, e.supplier_order_quantity as wo_qnty, e.rate as wo_rate, e.amount as wo_amount
				    from wo_non_order_info_mst d, wo_non_order_info_dtls e
				    where d.id=e.mst_id and d.wo_basis_id=2 and d.company_name=$cbo_company_name and d.entry_form=144 and e.item_category_id=1 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 $str_cond_independ ";
					$p=1;
					foreach($pi_wo_dtls_id_all_arr as $pi_wo_dtls_id)
					{
						if($p==1) $sql_wo_independ .=" and (e.id in(".implode(',',$pi_wo_dtls_id).")"; else $sql_wo_independ .=" or e.id in(".implode(',',$pi_wo_dtls_id).")";
						$p++;
					}
					$sql_wo_independ .=" ) ";
				}
				
			}
			else
			{
				$sql_wo_independ="select d.id as wo_id, d.ref_closing_status,d.is_approved, d.wo_basis_id, d.pay_mode, d.company_name, d.wo_number_prefix_num, d.wo_date, d.supplier_id, e.id as wo_dtls_id, e.color_name as wo_color, e.yarn_count as wo_count, e.yarn_comp_type1st as wo_yarn_comp_type1st, e.yarn_comp_percent1st as wo_yarn_comp_percent1st, e.yarn_comp_type2nd, e.yarn_comp_percent2nd, e.yarn_type as wo_yarn_type, e.uom as wo_uom, e.supplier_order_quantity as wo_qnty, e.rate as wo_rate, e.amount as wo_amount
			    from wo_non_order_info_mst d, wo_non_order_info_dtls e
		 	    where d.id=e.mst_id and d.wo_basis_id=2 and d.company_name=$cbo_company_name and d.entry_form=144 and e.item_category_id=1 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0   $str_cond_independ ";
			}
			//echo $sql_wo_independ;//die;
			$req_result_independ=sql_select($sql_wo_independ);

			$Print_Report_Format_PI=return_field_value("format_id","lib_report_template","template_name =$cbo_company_name and module_id=5 and report_id=183 and is_deleted=0 and status_active=1");
			$Print_Report_Format_Arr=explode(',',$Print_Report_Format_PI);
			$PiButtonID=$Print_Report_Format_Arr[0];

			$print_report_format=return_field_value("format_id","lib_report_template","template_name =$cbo_company_name and module_id=5 and report_id=45 and is_deleted=0 and status_active=1");
			$print_report_format_arr=explode(',',$print_report_format);
			$printButton=$print_report_format_arr[0];

			if($cbo_based_on==2 || $cbo_based_on==3)
			{
				?>
				<table width="2950"  align="left">
					<tr>
						<td style="font-size:18; font-weight:bold;">Based on Independent WO</td>
					</tr>
				</table>
				<br />
				<table width="2950" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_2"  align="left">
					<thead>
						<tr>
							<th width="30" rowspan="2">Sl</th>
							<th colspan="10">Work Order Details</th>
							<th colspan="12">PI Details</th>
							<th colspan="6">BTB LC Details</th>
							<th colspan="4">Matarials Received Information</th>
						</tr>
						<tr>
							<th width="50">WO No.</th>
							<th width="80">Yarn Color</th>
							<th width="50">Count</th>
							<th width="230">Composition</th>
							<th width="80">Yarn Type</th>
							<th width="50">UOM</th>
							<th width="80">WO Qnty</th>
							<th width="70">WO Rate</th>
							<th width="100">WO Amount</th>
							<th width="150">Supplier</th>
							<th width="100">PI No.</th>
							<th width="70">PI Date</th>
							<th width="70">Last Ship Date</th>
							<th width="80">Yarn Color</th>
							<th width="50">Count</th>
							<th width="230">Composition</th>
							<th width="80">Yarn Type</th>
							<th width="50">UOM</th>
							<th width="80">PI Qnty</th>
							<th width="70">PI Rate</th>
							<th width="100">PI Amount</th>
							<th width="70">Currency</th>
							<th width="70">LC Date</th>
							<th width="100">LC No</th>
							<th width="100">Issuing Bank</th>
							<th width="70">Pay Term</th>
							<th width="80">Tenor</th>
							<th width="100">LC Amount</th>
							<th width="80">MRR Qnty</th>
							<th width="100">MRR Value</th>
							<th width="100">Short Value</th>
	                        <th >Pipe Line</th>
						</tr>
					</thead>
				</table>
				<div style="width:2950px; overflow-y:scroll; max-height:200px;font-size:12px; overflow-x:hidden;" id="scroll_body2" align="left">
				<table width="2932" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body2" align="left">
					<tbody>
					<?
					$i=1;
					//print_r($wo_po_arr);die;
					$array_check=array();
					foreach($req_result_independ as $row)
					{ 
						$company_id=$row[csf("company_name")];						
						if ($i%2==0)
						$bgcolor="#E9F3FF";
						else
						$bgcolor="#FFFFFF";
						$pine_wo_qnty=$pipe_pi_qnty=$pipe_line=$mrr_qnty=$mrr_value=$short_amt="";
						$wo_rate_inde=$req_wo_recv_arr[$row[csf("id")]][$row[csf("prod_id")]]['recv_amt']/$req_wo_recv_arr[$row[csf("id")]][$row[csf("prod_id")]]['recv_qnty'];
						
						
						if($req_wo_recv_arr[$row[csf("wo_id")]][2][$row[csf("wo_color")]][$row[csf("wo_yarn_type")]][$row[csf("wo_count")]][$row[csf("wo_yarn_comp_type1st")]]['recv_qnty']!="") 
						$mrr_qnty=$req_wo_recv_arr[$row[csf("wo_id")]][2][$row[csf("wo_color")]][$row[csf("wo_yarn_type")]][$row[csf("wo_count")]][$row[csf("wo_yarn_comp_type1st")]]['recv_qnty']; 
						else $mrr_qnty=$req_wo_recv_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]][1][$row[csf("wo_color")]][$row[csf("wo_yarn_type")]][$row[csf("wo_count")]][$row[csf("wo_yarn_comp_type1st")]]['recv_qnty'];
						
						if($req_wo_recv_arr[$row[csf("wo_id")]][2][$row[csf("wo_color")]][$row[csf("wo_yarn_type")]][$row[csf("wo_count")]][$row[csf("wo_yarn_comp_type1st")]]['recv_amt']!="")
						{
							$mrr_value=$req_wo_recv_arr[$row[csf("wo_id")]][2][$row[csf("wo_color")]][$row[csf("wo_yarn_type")]][$row[csf("wo_count")]][$row[csf("wo_yarn_comp_type1st")]]['recv_amt'];
							$short_amt=$row[csf("wo_amount")]-$mrr_value;
							
							$booking_id=$req_wo_recv_arr[$row[csf("wo_id")]][2][$row[csf("wo_color")]][$row[csf("wo_yarn_type")]][$row[csf("wo_count")]][$row[csf("wo_yarn_comp_type1st")]]['booking_id'];
							$receive_basis=2;
						}
						else 
						{
							$mrr_value=$req_wo_recv_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]][1][$row[csf("wo_color")]][$row[csf("wo_yarn_type")]][$row[csf("wo_count")]][$row[csf("wo_yarn_comp_type1st")]]['recv_amt'];
							$short_amt=$pi_data_arr[$row[csf("wo_dtls_id")]]["amount"]-$mrr_value;
							$booking_id=$req_wo_recv_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]][1][$row[csf("wo_color")]][$row[csf("wo_yarn_type")]][$row[csf("wo_count")]][$row[csf("wo_yarn_comp_type1st")]]['booking_id'];
							$receive_basis=1;
						}
						
						$receiving_cond=0;
						if($cbo_receive_status==1 && ($mrr_value=="" || $mrr_value==0)) $receiving_cond=1;
						if($cbo_receive_status==2 && $receive_basis==2 && $row[csf("wo_amount")]>$mrr_value && $mrr_value>0) $receiving_cond=1;
						if($cbo_receive_status==2 && $receive_basis==1 && $pi_data_arr[$row[csf("wo_dtls_id")]]["amount"]>$mrr_value && $mrr_value>0) $receiving_cond=1;
						if($cbo_receive_status==3 && $receive_basis==2 && $mrr_value>=$row[csf("wo_amount")] && $mrr_value>0) $receiving_cond=1;
						if($cbo_receive_status==3 && $receive_basis==1 && $mrr_value>=$pi_data_arr[$row[csf("wo_dtls_id")]]["amount"] && $mrr_value>0) $receiving_cond=1;
						if($cbo_receive_status==4 && $receive_basis==2 && $mrr_value < $row[csf("wo_amount")]) $receiving_cond=1;
						if($cbo_receive_status==4 && $receive_basis==1 && $mrr_value < $pi_data_arr[$row[csf("wo_dtls_id")]]["amount"]) $receiving_cond=1;
						if($cbo_receive_status==5) $receiving_cond=1;
						if($receiving_cond==1)
						{
							?>
	                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
	                            <td width="30" title="<? echo $cbo_receive_status."=".$receive_basis."=".$mrr_value."=".$row[csf("wo_amount")]."=".$pi_data_arr[$row[csf("wo_dtls_id")]]["amount"]; ?>"><? echo $i; ?></td>
	                            <td width="50" align="center"> <a href="##" onClick="Fnc_Yd_HyperLink('<?=$printButton ?>','<?=$row[csf("company_name")]?>','<?=$row[csf("wo_id")]?>','','<?=$row[csf("ref_closing_status")]?>','<?=$row[csf("pay_mode")]?>','<?=$row[csf("is_approved")]?>','<?=$row[csf("wo_basis_id")]?>','<?=$row[csf("supplier_id")]?>','')" > <p><? echo $row[csf("wo_number_prefix_num")]; ?></p></a></td>
	                            <td width="80"><p><? echo $color_arr[$row[csf("wo_color")]]; ?></p></td>
	                            <td width="50" align="center"><p>&nbsp;<? echo $yarnCount_arr[$row[csf("wo_count")]]; ?></p></td>
	                            <td width="230"><p>
	                            <?
	                            if ($row[csf("yarn_comp_type2nd")]>0) $compo_percent2=$row[csf("yarn_comp_percent2nd")]."%"; else $compo_percent2="";
	                            echo $composition[$row[csf("wo_yarn_comp_type1st")]]." ".$row[csf("wo_yarn_comp_percent1st")]."% ".$composition[$row[csf("yarn_comp_type2nd")]]." ".$compo_percent2; 
	                            ?></p></td>
	                            <td width="80"><p><? echo $yarn_type[$row[csf("wo_yarn_type")]]; ?></p></td>
	                            <td width="50"><p><? echo $unit_of_measurement[$row[csf("wo_uom")]]; ?></p></td>
	                            <td width="80" align="right"><p><? echo number_format($row[csf("wo_qnty")],0); ?></p></td>
	                            <td width="70" align="right"><p><? echo number_format($row[csf("wo_rate")],2); ?></p></td>
	                            <td width="100" align="right"><p><? echo number_format($row[csf("wo_amount")],2); ?></p></td>
	                           
	                            <td width="150"><p><? echo $supplier_arr[$row[csf("supplier_id")]]; ?></p></td>
	                            <td width="100"> <a href="##" onClick="Fnc_Pro_Farma_InvoiceV2_Print_hyperLink('<?=$PiButtonID?>','<?=$row[csf("company_name")]?>','<?=$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]?>','<?=$pi_data_arr[$row[csf("wo_dtls_id")]]["entry_form"]?>','<?=$pi_data_arr[$row[csf("wo_dtls_id")]]["item_category_id"]?>')"> <p><? $pi_num=implode(" , ",array_unique(explode(",",chop($pi_data_arr[$row[csf("wo_dtls_id")]]["pi_number"]," , ")))); echo $pi_num; ?></p></a> </td>
	                            <td width="70"><p>&nbsp;
	                            <? 
		                            $pi_date_arr=array_unique(explode(",",chop($pi_data_arr[$row[csf("wo_dtls_id")]]["pi_date"]," , "))); 
		                            $pi_date='';
			                        foreach ($pi_date_arr as $value) 
			                        {
			                        	if($value !="" && $value!="0000-00-00")
			                        	{
			                        		$pi_date.=change_date_format($value)."</br>";
			                        	}
			                        }
		                        	echo chop($pi_date,"</br>");
	                            ?></p></td>
	                            <td width="70"><p>&nbsp;
	                            <? 
	                            $pi_last_ship_date_arr=array_unique(explode(",",chop($pi_data_arr[$row[csf("wo_dtls_id")]]["last_shipment_date"]," , "))); 
	                            $shipment_date='';
		                        foreach ($pi_last_ship_date_arr as $value) 
		                        {
		                        	if($value !="" && $value!="0000-00-00")
		                        	{
		                        		$shipment_date.=change_date_format($value)."</br>";
		                        	}
		                        }
		                        echo chop($shipment_date,"</br>");
	                            ?></p></td>
	                            <td width="80"><p><? echo  $color_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["color_id"]]; ?></p></td>
	                            <td width="50"><p>&nbsp;<? echo  $yarnCount_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["count_name"]]; ?></p></td>
	                            <td width="230"><p>
	                            <?
	                            if($pi_data_arr[$row[csf("wo_dtls_id")]]["yarn_composition_item2"]>0) $com_percent2=$pi_data_arr[$row[csf("wo_dtls_id")]]["yarn_comp_percent2nd"]."%"; else $com_percent2="";
	                            if($pi_data_arr[$row[csf("wo_dtls_id")]]["yarn_composition_item1"]>0) $com_percent=$pi_data_arr[$row[csf("wo_dtls_id")]]["yarn_composition_percentage1"]."%"; else $com_percent="";
	                            echo  $composition[$pi_data_arr[$row[csf("wo_dtls_id")]]["yarn_composition_item1"]]." ".$com_percent." ".$composition[$pi_data_arr[$row[csf("wo_dtls_id")]]["yarn_composition_item2"]]." ".$com_percent2;  
	                            ?></p></td>
	                            <td width="80"><p><? echo  $yarn_type[$pi_data_arr[$row[csf("wo_dtls_id")]]["yarn_type"]]; ?></p></td>
	                            <td width="50"><p><? echo  $unit_of_measurement[$pi_data_arr[$row[csf("wo_dtls_id")]]["uom"]]; ?></p></td>
	                            <td width="80" align="right"><p><? $pi_qnty=$pi_data_arr[$row[csf("wo_dtls_id")]]["quantity"]; echo  number_format($pi_qnty,0); ?></p></td>
	                            <td width="70" align="right"><p><? $pi_rate=$pi_data_arr[$row[csf("wo_dtls_id")]]["rate"]; echo  number_format($pi_rate,2); ?></p></td>
	                            <td width="100" align="right"><p><? $pi_amt=$pi_data_arr[$row[csf("wo_dtls_id")]]["amount"]; echo  number_format($pi_amt,2); ?></p></td>
	                            <td width="70"><p><? $pi_curency_arr=array_unique(explode(",",chop($pi_data_arr[$row[csf("wo_dtls_id")]]["currency_id"]," , "))); echo  $currency[$pi_curency_arr[0]]; ?></p></td>
	                            <td width="70"><p>&nbsp;
	                            <? 
	                            $btb_lc_date_arr=array_unique(explode(",",chop($btb_data_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]]["lc_date"]," , "))); 
	                            $btb_lc_date='';
		                        foreach ($btb_lc_date_arr as $value) 
		                        {
		                        	if($value !="" && $value!="0000-00-00")
		                        	{
		                        		$btb_lc_date.=change_date_format($value)."</br>";
		                        	}
		                        }
	                           	echo chop($btb_lc_date,"</br>"); 
	                            ?></p></td>
	                            <td width="100"><p><? $btb_lc_no_arr=array_unique(explode(",",chop($btb_data_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]]["lc_number"]," , "))); 
	                            $btb_lc_no=implode(" , ",$btb_lc_no_arr);
	                            echo $btb_lc_no;  ?></p></td>
	        
	                            <td width="100"><p><? $btb_issue_bank_arr=array_unique(explode(",",chop($btb_data_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]]["issuing_bank_id"]," , "))); 
	                            foreach ($btb_issue_bank_arr as $value) 
		                        {
		                        	$btb_issue_bank.=$bank_arr[$value].",";
		                        }
	                            echo chop($btb_issue_bank,",");  ?></p></td>
	                            <td width="70"><p><? $btb_pay_tarm_arr=array_unique(explode(",",chop($btb_data_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]]["payterm_id"]," , "))); 
								foreach ($btb_pay_tarm_arr as $value) 
		                        {
		                        	$btb_pay_term.=$pay_term[$value].",";
		                        }
	                            echo chop($btb_pay_term,","); ?></p></td>
	                            <td width="80"><p><? $btb_tenor_arr=array_unique(explode(",",chop($btb_data_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]]["tenor"]," , "))); 
	                            $btb_tenor=implode(" Days, ",$btb_tenor_arr);
	                            if ($btb_tenor!='') echo $btb_tenor." Days"; ?></p></td>
	                            <td width="100" align="right"><p><? $btb_lc_amount=$btb_data_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]]["lc_value"]; echo number_format($btb_lc_amount,2); ?></p></td>
	                            <?
	                            if($mrr_qnty>0)
	                            {
	                                ?>
	                                <td width="80" align="right"><p><a href="##" onclick="fn_mrr_details('<? echo $booking_id;?>','<? echo $receive_basis;?>','<? echo $row[csf("wo_color")];?>','<? echo $row[csf("wo_yarn_type")];?>','<? echo $row[csf("wo_count")];?>','<? echo $row[csf("wo_yarn_comp_type1st")];?>','receive_details_popup','<? echo $piIds;?>')"><? echo number_format($mrr_qnty,2);?> </a></p></td>
	                                <?
	                            }
	                            else
	                            {
	                                ?>
	                                <td width="80" align="right"></td>
	                                <?
	                            }
	                            ?>
	                            <td width="100" align="right"><p> <? echo number_format($mrr_value,2); ?></p></td>
	                            <td align="right" width="100"><p><? echo number_format($short_amt,2); ?></p></td>
	                            <td align="right"><p>
	                            <?
	                            //$pine_wo_qnty=$pipe_pi_qnty=$pipe_line 
	                            $pine_wo_qnty=$wo_pipe_array[$row[csf("wo_id")]][$row[csf("wo_color")]][$row[csf("wo_yarn_type")]][$row[csf("wo_count")]][$row[csf("wo_yarn_comp_type1st")]];
	                            $pipe_pi_qnty=$pi_pipe_array[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]][$row[csf("wo_color")]][$row[csf("wo_yarn_type")]][$row[csf("wo_count")]][$row[csf("wo_yarn_comp_type1st")]];
	                            $pipe_line=(($pine_wo_qnty+$pipe_pi_qnty)-$mrr_qnty);
	                            if(number_format($pipe_line,2) > 0.00) echo number_format($pipe_line,2); else echo "0.00";
	                            ?>
	                            </p></td>
	                        </tr>
	                        <?
							$tot_ind_wo_qnty+=$row[csf("wo_qnty")];
							$tot_ind_wo_amount+=$row[csf("wo_amount")];
							$tot_ind_pi_qnty+=$pi_qnty;
							$tot_ind_pi_amt+=$pi_amt;
							$tot_ind_btb_lc_amount+=$btb_lc_amount;
							$tot_ind_mrr_qnty+=$mrr_qnty;
							$tot_ind_mrr_value+=$mrr_value;
							$tot_ind_short_amt+=$short_amt;
						}
						$k++;
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
							<th>Total</th>
							<th align="right"><? echo number_format($tot_ind_wo_qnty,2);?></th>
							<th>&nbsp;</th>
							<th align="right"><? echo number_format($tot_ind_wo_amount,2);?></th>
							<th>&nbsp;</th>
							<th>&nbsp;</th>
							<th>&nbsp;</th>
							<th>&nbsp;</th>
							<th>&nbsp;</th>
							<th>&nbsp;</th>
							<th>&nbsp;</th>
							<th>&nbsp;</th>
							<th>&nbsp;</th>
							<th align="right"><? echo number_format($tot_ind_pi_qnty,2);?></th>
							<th>&nbsp;</th>
							<th align="right"><? echo number_format($tot_ind_pi_amt,2);?></th>
							<th>&nbsp;</th>
							<th>&nbsp;</th>
							<th>&nbsp;</th>
							<th>&nbsp;</th>
							<th>&nbsp;</th>
							<th>&nbsp;</th>
							<th align="right"><? echo number_format($tot_ind_btb_lc_amount,2);?></th>
							<th align="right"><? echo number_format($tot_ind_mrr_qnty,2);?></th>
							<th align="right"><? echo number_format($tot_ind_mrr_value,2);?></th>
							<th>&nbsp;</th>
	                        <th>&nbsp;</th>
						</tr>
	                </tfoot>
				</table>
				</div>
				<?
			}
			?>
	    </div>
	    <br />
	    <div style="width:2030px" >
	    <?
		if($cbo_based_on==3)
	    {
	    	$sql_independent_pi="select a.id as pi_id, a.supplier_id as pi_suplier, a.pi_number, a.pi_date, a.last_shipment_date, a.currency_id, b.work_order_id, b.work_order_dtls_id, b.color_id, b.count_name, b.yarn_composition_item1, b.yarn_composition_percentage1, b.yarn_composition_item2, b.yarn_composition_percentage2, b.yarn_type, b.uom, b.quantity, b.rate, b.amount from com_pi_master_details a, com_pi_item_details b where a.pi_basis_id=2 and a.item_category_id=1 and a.importer_id=$cbo_company_name and a.id=b.pi_id and a.status_active=1 and b.status_active=1 $pi_cond";
	    	$req_result_pi_independ=sql_select($sql_independent_pi);
		}
			//echo $sql_independent_pi;die;
		if($cbo_based_on==3)
		{
			?>
			<table width="2150"  align="left">
				<tr>
					<td style="font-size:18; font-weight:bold;">Based on Independent PI</td>
				</tr>
			</table>
			<br />
			<table width="2150" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_3"  align="left">
				<thead>
					<tr>
						<th width="30" rowspan="2">Sl</th>
						<th colspan="14">PI Details</th>
						<th colspan="6">BTB LC Details</th>
						<th colspan="4">Matarials Received Information</th>
					</tr>
					<tr>
						<th width="150">Supplier</th>
						<th width="100">PI No.</th>
						<th width="70">PI Date</th>
						<th width="70">Last Ship Date</th>
						<th width="80">Yarn Color</th>
						<th width="50">Count</th>
						<th width="230">Composition</th>
						<th width="80">Yarn Type</th>
						<th width="50">UOM</th>
						<th width="80">PI Qnty</th>
						<th width="70">PI Rate</th>
						<th width="100">PI Amount</th>
						<th width="70">Currency</th>
						<th width="70">LC Date</th>
						<th width="100">LC No</th>
						<th width="100">Issuing Bank</th>
						<th width="70">Pay Term</th>
						<th width="80">Tenor</th>
						<th width="100">LC Amount</th>
						<th width="80">MRR Qnty</th>
						<th width="100">MRR Value</th>
						<th width="100">Short Value</th>
	                    <th >Pipe Line</th>
					</tr>
				</thead>
			</table>

			


			<div style="width:2150px; overflow-y:scroll; max-height:200px;font-size:12px; overflow-x:hidden;" id="scroll_body3" align="left">
			<table width="2132" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body3" align="left">
				<tbody>
				<?
				$i=1;

				//var_dump($tem_arr);die;
				$array_check=array();
				foreach($req_result_pi_independ as $row)
				{
					if ($i%2==0)
					$bgcolor="#E9F3FF";
					else
					$bgcolor="#FFFFFF";
					$pipe_pi_qnty=$pipe_line="";
					$mrr_qnty=0;$mrr_value=0;
					$mrr_qnty=$req_wo_recv_arr[$row[csf("pi_id")]][1][$row[csf("color_id")]][$row[csf("yarn_type")]][$row[csf("count_name")]][$row[csf("yarn_composition_item1")]]['recv_qnty'];
					$mrr_value=$req_wo_recv_arr[$row[csf("pi_id")]][1][$row[csf("color_id")]][$row[csf("yarn_type")]][$row[csf("count_name")]][$row[csf("yarn_composition_item1")]]['recv_amt'];
					$pi_id_ref=$req_wo_recv_arr[$row[csf("pi_id")]][1][$row[csf("color_id")]][$row[csf("yarn_type")]][$row[csf("count_name")]][$row[csf("yarn_composition_item1")]]['booking_id'];
					$receive_basis=1;
					
					$receiving_cond=0;
					if($cbo_receive_status==1 && ($mrr_value=="" || $mrr_value==0)) $receiving_cond=1;
					if($cbo_receive_status==2 && $row[csf("amount")]>$mrr_value && $mrr_value>0) $receiving_cond=1;
					if($cbo_receive_status==3 && $mrr_value>=$row[csf("amount")]) $receiving_cond=1;
					if($cbo_receive_status==4 && $mrr_value < $row[csf("amount")]) $receiving_cond=1;
					if($cbo_receive_status==5) $receiving_cond=1;
					
					if($receiving_cond==1)
					{
						?>
	                    <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
	                        <td width="30"><? echo $i; ?></td>
	                        <td width="150"><p><? echo $supplier_arr[$row[csf("pi_suplier")]]; ?></p></td>
	                        <td width="100"><p><? echo $row[csf("pi_number")]; ?></p></td>
	                        <td width="70"><p>&nbsp;
	                        <? 
	                        if($row[csf("pi_date")]!="" && $row[csf("pi_date")]!="0000-00-00") echo change_date_format($row[csf("pi_date")]); 
	                        ?></p></td>
	                        <td width="70"><p>&nbsp;
	                        <? 
	                        if($row[csf("last_shipment_date")]!="" && $row[csf("last_shipment_date")]!="0000-00-00") echo change_date_format($row[csf("last_shipment_date")]); 
	                        ?></p></td>
	                        <td width="80"><p><? echo $color_arr[$row[csf("color_id")]]; ?></p></td>
	                        <td width="50"><p>&nbsp;<? echo $yarnCount_arr[$row[csf("count_name")]]; ?></p></td>
	                        <td width="230"><p>
	                        <? 
	                        if($row[csf("yarn_composition_item2")]>0) $comp_percent2=$row[csf("yarn_composition_percentage2")]."%"; else $comp_percent2="";
	                        echo $composition[$row[csf("yarn_composition_item1")]]." ".$row[csf("yarn_composition_percentage1")]."% ".$composition[$row[csf("yarn_composition_item2")]]; 
	                        ?></p></td>
	                        <td width="80"><p><? echo $yarn_type[$row[csf("yarn_type")]];?></p></td>
	                        <td width="50"><p><? echo $unit_of_measurement[$row[csf("uom")]];?></p></td>
	                        <td width="80" align="right"><p><? echo  number_format($row[csf("quantity")],0); ?> </p></td>
	                        <td width="70" align="right"><p><? echo  number_format($row[csf("rate")],2); ?> </p></td>
	                        <td width="100" align="right"><p><? echo number_format($row[csf("amount")],2); ?> </p></td>
	                        <td width="70"><p><? echo $currency[$row[csf("currency_id")]]; ?></p></td>
	                        <td width="70"><p>&nbsp;
	                        <? 
	                        $btb_lc_date_arr=array_unique(explode(",",chop($btb_data_arr[$row[csf("pi_id")]]["lc_date"]," , ")));
	                        $btb_lc_date='';
	                        foreach ($btb_lc_date_arr as $value) 
	                        {
	                        	if($value !="" && $value!="0000-00-00")
	                        	{
	                        		$btb_lc_date.=change_date_format($value)."</br>";
	                        	}
	                        }
	                        echo chop($btb_lc_date,"</br>");  ?> </p></td>
	                        <td width="100"><p><? $btb_lc_no_arr=array_unique(explode(",",chop($btb_data_arr[$row[csf("pi_id")]]["lc_number"]," , ")));
	                        $btb_lc_no=implode(" , ",$btb_lc_no_arr);
	                        echo $btb_lc_no;  ?></p></td>
	                        <td width="100"><p><? $btb_issue_bank_arr=array_unique(explode(",",chop($btb_data_arr[$row[csf("pi_id")]]["issuing_bank_id"]," , ")));
	                        foreach ($btb_issue_bank_arr as $value) 
	                        {
	                        	$btb_issue_bank.=$bank_arr[$value].",";
	                        }
	                        echo chop($btb_issue_bank,","); ?></p></td>
	                        <td width="70"><p><? $btb_pay_tarm_arr=array_unique(explode(",",chop($btb_data_arr[$row[csf("pi_id")]]["payterm_id"]," , ")));
	                        $btb_pay_term=''; 
	                        foreach ($btb_pay_tarm_arr as $value) 
	                        {
	                        	$btb_pay_term.=$pay_term[$value].",";
	                        }
	                        echo chop($btb_pay_term,","); ?></p></td>
	                        <td width="80"><p><? $btb_tenor_arr=array_unique(explode(",",chop($btb_data_arr[$row[csf("pi_id")]]["tenor"]," , "))); 
	                        $btb_tenor=implode(" Days, ",$btb_tenor_arr);
	                        if ($btb_tenor!='') echo $btb_tenor." Days"; ?></p></td>
	                        <td width="100" align="right"><p><? $btb_lc_amount=$btb_data_arr[$row[csf("pi_id")]]["lc_value"]; echo number_format($btb_lc_amount,2); ?> </p></td>
	                        <td width="80" align="right"><p><a href="##" onclick="fn_mrr_details('<? echo $pi_id_ref;?>','<? echo $receive_basis;?>','<? echo $row[csf("color_id")];?>','<? echo $row[csf("yarn_type")];?>','<? echo $row[csf("count_name")];?>','<? echo $row[csf("yarn_composition_item1")];?>','receive_details_popup')"><? echo number_format($mrr_qnty,2); ?></a></p></td>
	                        <td width="100" align="right"><p><? echo number_format($mrr_value,2);?></p></td>
	                        <td align="right"><p><? $short_amt=$row[csf("amount")]-$mrr_value; echo number_format($short_amt,2); ?></p></td>
	                        <td align="right" title="<?= $pi_pipe_array[$row[csf("pi_id")]][$row[csf("color_id")]][$row[csf("yarn_type")]][$row[csf("count_name")]][$row[csf("yarn_composition_item1")]];?>"><p>
	                        <?
	                        //$pipe_pi_qnty=$pipe_line 
	                        $pipe_pi_qnty=$pi_pipe_array[$row[csf("pi_id")]][$row[csf("color_id")]][$row[csf("yarn_type")]][$row[csf("count_name")]][$row[csf("yarn_composition_item1")]];
	                        $pipe_line=($pipe_pi_qnty-$mrr_qnty);
	                        echo number_format($pipe_line,2); 
	                        ?>
	                        </p></td>
	                    </tr>
	                    <?
	                    $k++;
	                    $i++;
	                    $total_pi_qntty+=$row[csf("quantity")];
	                    $total_pi_amount+=$row[csf("amount")];
	                    $total_mrr_qty+=$mrr_qnty;
	                    $total_mrr_val+=$mrr_value;
					}
				}
				?>
				</tbody>
				<tfoot style="background: #dbdbdb;">
					<td colspan="10" align="right"><strong>Total</strong></td>
					<td align="right"><b><? echo number_format($total_pi_qntty,2);?></b></td>
					<td>&nbsp;</td>
					<td align="right"><b><? echo number_format($total_pi_amount,2);?></b></td>
					<td colspan="8" align="right"><b><? echo number_format($total_mrr_qty,2);?></b></td>
					<td align="right"><b><? echo number_format($total_mrr_val,2);?></b></td>
					<td colspan="2">&nbsp;</td>
				</tfoot>
			</table>
			</div>
			<?
		}
	    ?>
		</div>
	    <?
	}
	else // show 2
	{
		$str_cond=$str_cond_independ=$pi_cond=$btb_cond="";
		if($cbo_based_on==1)
		{
			$str_cond.=" and a.requ_prefix_num like '%$txt_search_no%'";
			if($txt_date_from!="" && $txt_date_to!="") $str_cond.=" and a.requisition_date between '$txt_date_from' and '$txt_date_to'";
		}
		else if($cbo_based_on==2)
		{
			$str_cond.=" and d.wo_number_prefix_num like '%$txt_search_no%'";
			if($txt_date_from!="" && $txt_date_to!="")  $str_cond.=" and d.wo_date between '$txt_date_from' and '$txt_date_to'";
			$str_cond_independ.=" and d.wo_number_prefix_num like '%$txt_search_no%'";
			if($txt_date_from!="" && $txt_date_to!="") $str_cond_independ.=" and d.wo_date between '$txt_date_from' and '$txt_date_to'";
		}
		else if($cbo_based_on==3)
		{
			if ($txt_search_no != "")
			{
				$pi_cond.=" and a.pi_number like '%$txt_search_no%'";
			}
			if($txt_date_from!="" && $txt_date_to!="") $pi_cond.=" and a.pi_date between '$txt_date_from' and '$txt_date_to'";
		}
		
		
		$sql_pi=sql_select("SELECT a.id as pi_id, a.supplier_id as pi_suplier, a.pi_number, a.pi_date, a.last_shipment_date, a.currency_id, b.work_order_id, b.work_order_dtls_id, b.color_id, b.count_name, b.yarn_composition_item1, b.yarn_composition_percentage1, b.yarn_composition_item2, b.yarn_composition_percentage2, b.yarn_type, b.uom, b.quantity, b.rate, b.amount from com_pi_master_details a, com_pi_item_details b 
		where a.item_category_id=1 and a.id=b.pi_id and a.importer_id=$cbo_company_name and a.status_active=1 and b.status_active=1 and b.work_order_id is not null $pi_cond"); //and a.goods_rcv_status<>1
		
		$pi_data_arr=array();
		foreach($sql_pi as $row)
		{
			if($row[csf("work_order_dtls_id")]) $pi_wo_dtls_id_all[]=$row[csf("work_order_dtls_id")];
			$pi_id_arr[]=$row[csf("pi_id")];
			$pi_data_arr[$row[csf("work_order_dtls_id")]]["work_order_dtls_id"]=$row[csf("work_order_dtls_id")];
			$pi_data_arr[$row[csf("work_order_dtls_id")]]["pi_id"]=$row[csf("pi_id")];
			$pi_data_arr[$row[csf("work_order_dtls_id")]]["pi_id_all"].=$row[csf("pi_id")].",";
			$pi_data_arr[$row[csf("work_order_dtls_id")]]["pi_suplier"].=$row[csf("pi_suplier")].",";
			$pi_data_arr[$row[csf("work_order_dtls_id")]]["pi_number"].=$row[csf("pi_number")].",";
			$pi_data_arr[$row[csf("work_order_dtls_id")]]["pi_date"].=$row[csf("pi_date")].",";
			$pi_data_arr[$row[csf("work_order_dtls_id")]]["last_shipment_date"].=$row[csf("last_shipment_date")].",";
			$pi_data_arr[$row[csf("work_order_dtls_id")]]["currency_id"].=$row[csf("currency_id")].",";
			$pi_data_arr[$row[csf("work_order_dtls_id")]]["work_order_id"]=$row[csf("work_order_id")];
			$pi_data_arr[$row[csf("work_order_dtls_id")]]["color_id"]=$row[csf("color_id")];
			$pi_data_arr[$row[csf("work_order_dtls_id")]]["count_name"]=$row[csf("count_name")];
			$pi_data_arr[$row[csf("work_order_dtls_id")]]["yarn_composition_item1"]=$row[csf("yarn_composition_item1")];
			$pi_data_arr[$row[csf("work_order_dtls_id")]]["yarn_composition_percentage1"]=$row[csf("yarn_composition_percentage1")];
			$pi_data_arr[$row[csf("work_order_dtls_id")]]["yarn_composition_item2"]=$row[csf("yarn_composition_item2")];
			$pi_data_arr[$row[csf("work_order_dtls_id")]]["yarn_comp_percent2nd"]=$row[csf("yarn_comp_percent2nd")];
			$pi_data_arr[$row[csf("work_order_dtls_id")]]["yarn_type"]=$row[csf("yarn_type")];
			$pi_data_arr[$row[csf("work_order_dtls_id")]]["uom"]=$row[csf("uom")];
			$pi_data_arr[$row[csf("work_order_dtls_id")]]["quantity"]+=$row[csf("quantity")];
			$pi_data_arr[$row[csf("work_order_dtls_id")]]["rate"]=$row[csf("rate")];
			$pi_data_arr[$row[csf("work_order_dtls_id")]]["amount"]+=$row[csf("amount")];
		}
		
		if($cbo_based_on==3)
		{
			$pi_id_arr_all=array_chunk(array_unique($pi_id_arr),999);
		
			$sql_btb="select a.id as btb_id, a.lc_number, a.lc_date, a.issuing_bank_id, a.payterm_id, a.tenor, a.lc_value, b.pi_id from com_btb_lc_master_details a, com_btb_lc_pi b where a.id=b.com_btb_lc_master_details_id and a.pi_entry_form=165 and a.status_active=1 and b.status_active=1";
			
			$p=1;
			if(!empty($pi_id_arr_all))
			{
				foreach($pi_id_arr_all as $pi_id)
				{
					if($p==1) $sql_btb .=" and (b.pi_id in(".implode(',',$pi_id).")"; else $sql_btb .=" or b.pi_id in(".implode(',',$pi_id).")";
					$p++;
				}
				$sql_btb .=" ) ";
			}
			
			//echo $sql_btb;die;
		}
		else
		{
		
			$sql_btb="select a.id as btb_id, a.lc_number, a.lc_date, a.issuing_bank_id, a.payterm_id, a.tenor, a.lc_value, b.pi_id from com_btb_lc_master_details a, com_btb_lc_pi b where a.id=b.com_btb_lc_master_details_id and a.pi_entry_form=165 and a.status_active=1 and b.status_active=1";
		}
		//echo $sql_btb;die;
			
		$sql_btb_result=sql_select($sql_btb);
		$btb_data_arr=array();
		foreach($sql_btb_result as $row)
		{
			$btb_data_arr[$row[csf("pi_id")]]["pi_id"]=$row[csf("pi_id")];
			$btb_data_arr[$row[csf("pi_id")]]["btb_id"]=$row[csf("btb_id")];
			$btb_data_arr[$row[csf("pi_id")]]["btb_id_all"].=$row[csf("btb_id")].",";
			$btb_data_arr[$row[csf("pi_id")]]["lc_number"].=$row[csf("lc_number")].",";
			$btb_data_arr[$row[csf("pi_id")]]["lc_date"].=$row[csf("lc_date")].",";
			$btb_data_arr[$row[csf("pi_id")]]["issuing_bank_id"].=$row[csf("issuing_bank_id")].",";
			$btb_data_arr[$row[csf("pi_id")]]["payterm_id"].=$row[csf("payterm_id")].",";
			$btb_data_arr[$row[csf("pi_id")]]["tenor"].=$row[csf("tenor")].",";
			$btb_data_arr[$row[csf("pi_id")]]["lc_value"]+=$row[csf("lc_value")];
		}
		
		$rcv_return_sql=sql_select("select b.prod_id, a.received_id, b.cons_quantity, b.cons_quantity, b.cons_amount from inv_issue_master a, inv_transaction b where a.id=b.mst_id and a.entry_form=8 and b.transaction_type=3 and b.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		$rcv_rtn_data=array();
		foreach($rcv_return_sql as $row)
		{
			$rcv_rtn_data[$row[csf("received_id")]][$row[csf("prod_id")]]["cons_quantity"]+=$row[csf("cons_quantity")];
			$rcv_rtn_data[$row[csf("received_id")]][$row[csf("prod_id")]]["cons_amount"]+=$row[csf("cons_amount")];
		}
		
		$req_wo_recv_sql=sql_select("select a.receive_basis, a.booking_id, c.color, c.yarn_type, c.yarn_count_id, c.yarn_comp_type1st, b.order_qnty as recv_qnty, b.order_amount as recv_amt, b.mst_id, b.prod_id, b.transaction_date, a.exchange_rate 
		from  inv_receive_master a, inv_transaction b, product_details_master c 
		where a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=1 and a.item_category=1 and a.booking_id>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
		order by a.booking_id, a.receive_basis, c.color, c.yarn_type, c.yarn_count_id, c.yarn_comp_type1st, b.transaction_date");
		$min_date=$max_date="";
		$b=0;
		foreach($req_wo_recv_sql as $row)
		{
			if($item_check[$row[csf("booking_id")]][$row[csf("receive_basis")]][$row[csf("color")]][$row[csf("yarn_type")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]]=="")
			{
				$item_check[$row[csf("booking_id")]][$row[csf("receive_basis")]][$row[csf("color")]][$row[csf("yarn_type")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]]=$row[csf("yarn_comp_type1st")];
				$min_date=$row[csf("transaction_date")];
				$max_date=$row[csf("transaction_date")];
				$b++;
			}
			else
			{
				if(strtotime($row[csf("transaction_date")])>strtotime($max_date)) $max_date=$row[csf("transaction_date")];
			}
			$req_wo_recv_arr[$row[csf("booking_id")]][$row[csf("receive_basis")]][$row[csf("color")]][$row[csf("yarn_type")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]]['booking_id']=$row[csf("booking_id")];
			$req_wo_recv_arr[$row[csf("booking_id")]][$row[csf("receive_basis")]][$row[csf("color")]][$row[csf("yarn_type")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]]['min_date']=$min_date;
			$req_wo_recv_arr[$row[csf("booking_id")]][$row[csf("receive_basis")]][$row[csf("color")]][$row[csf("yarn_type")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]]['max_date']=$max_date;
			
			$req_wo_recv_arr[$row[csf("booking_id")]][$row[csf("receive_basis")]][$row[csf("color")]][$row[csf("yarn_type")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]]['receive_basis']=$row[csf("receive_basis")];
			$req_wo_recv_arr[$row[csf("booking_id")]][$row[csf("receive_basis")]][$row[csf("color")]][$row[csf("yarn_type")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]]['recv_qnty']+=$row[csf("recv_qnty")]-$rcv_rtn_data[$row[csf("mst_id")]][$row[csf("prod_id")]]["cons_quantity"];
			$req_wo_recv_arr[$row[csf("booking_id")]][$row[csf("receive_basis")]][$row[csf("color")]][$row[csf("yarn_type")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]]['recv_amt']+=$row[csf("recv_amt")]-($rcv_rtn_data[$row[csf("mst_id")]][$row[csf("prod_id")]]["cons_amount"]/$row[csf("exchange_rate")]);
			
		}
		//echo $b."<pre>";print_r($req_wo_recv_arr[15678][1]);die;		

		$wo_qty_arr=sql_select("select a.id as wo_id, b.color_name as color, b.yarn_type as yarn_type, b.yarn_count as yarn_count_id, b.yarn_comp_type1st as yarn_comp_type1st, sum(b.supplier_order_quantity) as qty from wo_non_order_info_mst a, wo_non_order_info_dtls b where a.id=b.mst_id and a.company_name=$cbo_company_name and b.item_category_id in (1) and a.pay_mode<>2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
		group by a.id,b.color_name,b.yarn_type,b.yarn_count,b.yarn_comp_type1st");
		$wo_pipe_array=array();
		foreach($wo_qty_arr as $row)
		{
			$wo_pipe_array[$row[csf("wo_id")]][$row[csf("color")]][$row[csf("yarn_type")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]]=$row[csf("qty")];
		}
		//echo '<pre>';print_r($wo_pipe_array);

		$pi_qty_arr=sql_select("select a.id as pi_id, b.color_id as color, b.yarn_type as yarn_type, b.count_name as yarn_count_id, b.yarn_composition_item1 as yarn_comp_type1st, sum(b.quantity) as qty from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.importer_id=$cbo_company_name and a.item_category_id in (1) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, b.color_id, b.yarn_type, b.count_name, b.yarn_composition_item1"); 
		$pi_pipe_array=array();
		foreach($pi_qty_arr as $row)
		{
			$pi_pipe_array[$row[csf("pi_id")]][$row[csf("color")]][$row[csf("yarn_type")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]]=$row[csf("qty")];
		}		
		//echo $cbo_based_on;
		//echo "<pre>";print_r($pi_pipe_array[5710]); die;
		
		if($cbo_based_on==3)
		{
			//print_r($pi_wo_dtls_id_all);die;
			if($cbo_year>0)
			{
				if($db_type==0) $year_cond=" and year(a.insert_date)='$cbo_year'"; else $year_cond=" and to_char(a.insert_date,'YYYY')='$cbo_year'";
			}
			if(!empty($pi_wo_dtls_id_all))
			{
				$pi_wo_dtls_id_all_arr=array_chunk(array_unique($pi_wo_dtls_id_all),999);
				$sql_req_wo="select a.id, a.requ_no, a.requ_prefix_num, a.requisition_date, a.company_id, b.buyer_id, b.id as req_dtls_id, b.color_id, b.count_id, b.composition_id, b.com_percent, b.yarn_type_id, b.cons_uom as req_uom, b.quantity as req_qnty, b.rate as req_rate, b.amount as req_amt, b.yarn_inhouse_date,b.booking_no, b.job_no, d.id as wo_id, d.wo_number_prefix_num, d.wo_date, e.id as wo_dtls_id, e.color_name as wo_color, e.yarn_count as wo_count, e.yarn_comp_type1st as wo_yarn_comp_type1st, e.yarn_comp_percent1st as wo_yarn_comp_percent1st, e.yarn_comp_type2nd, e.yarn_comp_percent2nd,  e.yarn_type as wo_yarn_type, e.uom as wo_uom, e.supplier_order_quantity as wo_qnty, e.rate as wo_rate, e.amount as wo_amount
				from inv_purchase_requisition_mst a,  inv_purchase_requisition_dtls b 
				left join  wo_non_order_info_dtls e  on  b.id=e.requisition_dtls_id and e.status_active=1 and e.is_deleted=0 
				left join wo_non_order_info_mst d on d.id=e.mst_id  and d.status_active=1 and d.is_deleted=0
				where a.id=b.mst_id and a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=70 and a.company_id='$cbo_company_name' $str_cond $year_cond ";
				//echo $sql_req_wo;die;
				if(!empty($pi_wo_dtls_id_all_arr))
				{
					$p=1;
					foreach($pi_wo_dtls_id_all_arr as $pi_wo_dtls_id)
					{
						if($p==1) $sql_req_wo .=" and (e.id in(".implode(',',$pi_wo_dtls_id).")"; else $sql_req_wo .=" or e.id in(".implode(',',$pi_wo_dtls_id).")";
						$p++;
					}
					$sql_req_wo .=" ) ";
				}
				$sql_req_wo .=" order by  a.id desc, b.color_id, b.yarn_type_id, b.count_id, b.composition_id ";	
			}
			
		}
		else
		{
			if($cbo_year>0)
			{
				if($db_type==0) $year_cond=" and year(a.insert_date)='$cbo_year'"; else $year_cond=" and to_char(a.insert_date,'YYYY')='$cbo_year'";
			}
			$sql_req_wo="select a.id, a.requ_no, a.is_approved, a.basis, a.requ_prefix_num, a.requisition_date, a.delivery_date, a.company_id, b.buyer_id, b.id as req_dtls_id, b.color_id, b.count_id, b.composition_id, b.com_percent, b.yarn_type_id, b.cons_uom as req_uom, b.quantity as req_qnty, b.rate as req_rate, b.amount as req_amt, b.yarn_inhouse_date, b.booking_no, b.job_no, d.id as wo_id, d.wo_number_prefix_num, d.wo_date, d.is_approved as wo_approved, d.ref_closing_status, d.pay_mode, e.id as wo_dtls_id, e.color_name as wo_color, e.yarn_count as wo_count, e.yarn_comp_type1st as wo_yarn_comp_type1st, e.yarn_comp_percent1st as wo_yarn_comp_percent1st, e.yarn_comp_type2nd, e.yarn_comp_percent2nd, e.yarn_type as wo_yarn_type, e.uom as wo_uom, e.supplier_order_quantity as wo_qnty, e.rate as wo_rate, e.amount as wo_amount
			from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b 
			left join  wo_non_order_info_dtls e  on  b.id=e.requisition_dtls_id and e.status_active=1 and e.is_deleted=0 
			left join wo_non_order_info_mst d on d.id=e.mst_id  and d.status_active=1 and d.is_deleted=0
			where a.id=b.mst_id and b.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id='$cbo_company_name' and a.entry_form=70 $str_cond $year_cond 
			order by a.id desc, b.color_id, b.count_id, b.yarn_type_id, b.composition_id";
		}
		//echo $sql_req_wo;//die;
		   
		$req_result=sql_select($sql_req_wo);
		foreach ($req_result as $val) {
			if ($val[csf("job_no")] != ''){
				//$job_no.="'".$val[csf("job_no")]."'".',';
				$job_nos.="'".$val[csf('job_no')]."'".',';
			}
		}

		$approval_arr=array();
		$sql_appr=sql_select("select mst_id as requ_id, approved_date, approved_by from approval_history where entry_form=20 and current_approval_status=1");
		foreach ($sql_appr as $val) {
			$approval_arr[$val[csf("requ_id")]]=$val[csf("approved_date")];
		}

		$job_nos_all=rtrim($job_nos,",");
		
		$job_nos_alls=explode(",",$job_nos_all);
		
		$job_nos_alls=array_chunk($job_nos_alls,999);
		$job_no_cond=" and";
		foreach($job_nos_alls as $dtls_id)
		{
			if($job_no_cond==" and")  $job_no_cond.="(a.job_no in(".implode(',',$dtls_id).")"; else $job_no_cond.=" or a.job_no in(".implode(',',$dtls_id).")";
		}
		$job_no_cond.=")";

		//$job_nos = implode(',',array_flip(array_flip(explode(',', rtrim($job_no,',')))));
		$job_order_arr=array();
		if ($job_nos != ''){
			$sql_order="select a.style_ref_no, a.job_no, a.job_quantity, b.po_number, b.po_received_date, b.pub_shipment_date, c.booking_no, c.booking_date from wo_po_details_master a, wo_po_break_down b, wo_booking_mst c
			where a.job_no=b.job_no_mst and a.job_no=c.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $job_no_cond and a.company_name='$cbo_company_name'";
			$sql_order_res=sql_select($sql_order);
			foreach ($sql_order_res as $val) {
				$job_order_arr[$val[csf("job_no")]]['style_ref_no']=$val[csf("style_ref_no")];
				$job_order_arr[$val[csf("job_no")]]['job_quantity']=$val[csf("job_quantity")];
				$job_order_arr[$val[csf("job_no")]]['po_number'].=$val[csf("po_number")].',';
				$job_order_arr[$val[csf("job_no")]]['po_received_date'].=change_date_format($val[csf("po_received_date")]).',';
				$job_order_arr[$val[csf("job_no")]]['pub_shipment_date'].=change_date_format($val[csf("pub_shipment_date")]).',';
				$job_order_arr[$val[csf("job_no")]]['booking_no'].=$val[csf("booking_no")].',';
				$job_order_arr[$val[csf("job_no")]]['booking_date'].=change_date_format($val[csf("booking_date")]).',';
			}
		}
		//echo '<pre>';print_r($job_order_arr);die;
		$print_report_format=sql_select("select format_id, report_id from lib_report_template where template_name=".$cbo_company_name." and module_id=5 and report_id in(69,45) and status_active=1 and is_deleted=0");
		foreach ($print_report_format as $val) {
			if ($val[csf("report_id")]==69) $print_report_format_yarnPurchaseRequ=explode(",",$val[csf("format_id")]);
			else if ($val[csf("report_id")]==45) $print_report_format_yarnPurchaseOrder=explode(",",$val[csf("format_id")]);			
			//else if ($val[csf("report_id")]==129) $print_report_format_issRtn=explode(",",$val[csf("format_id")]);			
		}
		//echo '<pre>';print_r($print_report_format_yarnPurchaseRequ);


		ob_start();
		?>
	    <div style="width:5100px">
	        <table width="5100" cellpadding="0" cellspacing="0" id="caption"  align="left">
	        <tr>
	            <td align="center" width="100%"  class="form_caption" colspan="57"><strong style="font-size:18px">Company Name:<? echo " ". $company_arr[str_replace("'","",$cbo_company_name)]; ?></strong></td>
	        </tr> 
	        <tr>  
	            <td align="center" width="100%" class="form_caption"  colspan="57"><strong style="font-size:18px"><? echo $report_title; ?></strong></td>
	        </tr>
	        <tr>  
	            <td align="center" width="100%"  class="form_caption"  colspan="57"><strong style="font-size:18px">From : <? echo $txt_date_from; ?> To : <? echo $txt_date_to; ?></strong></td>
	        </tr>
	        </table>
	    	<br />
	        <table width="5100"  align="left">
	        	<tr>
	            	<td style="font-size:18; font-weight:bold;">Based on Requisition</td>
	            </tr>
	        </table>
	        <br />       
	            <table width="5100" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_1"  align="left">
	            <thead>
	            	<tr>
	                	<th width="30" rowspan="2">Sl</th>
	                	<th colspan="22">Requisiton Details</th>
	                    <th colspan="9">Work Order Details</th>
	                    <th colspan="13">PI Details</th>
	                    <th colspan="6">BTB LC Details</th>
	                    <th colspan="6">Matarials Received Information</th>
	                </tr>
	                <tr>
	                    <th width="50">Req. No</th>
	                    <th width="70">Req. Date</th>
	                    <th width="80">Req Appr Date</th>
	                    <th width="100">Buyer</th>
	                    <th width="80">PO Recv Date</th>
	                    <th width="150">Order</th>
	                    <th width="100">Style</th>
	                    <th width="100">Job</th>
	                    <th width="150">Booking Number</th>
	                    <th width="130">Merchandising Booking Date</th>
	                    <th width="80">Shipment Date</th>
	                    <th width="80">Requested Delivery Date</th>
	                    <th width="80">Job Garments Qty</th>
	                    <th width="80">Yarn Color</th>
	                    <th width="50">Count</th>
	                    <th width="150">Composition</th>
	                    <th width="80">Yarn Type</th>
	                    <th width="50">UOM</th>
	                    <th width="80">Req. Qnty.</th>
	                    <th width="70">Req. Rate</th>
	                    <th width="100">Req. Amount</th>
	                    <th width="75">Yarn Inhouse Date</th>


	                    <th width="50">WO No.</th>
	                    <th width="80">Yarn Color</th>
	                    <th width="50">Count</th>
	                    <th width="250">Composition</th>
	                    <th width="80">Yarn Type</th>
	                    <th width="50">UOM</th>
	                    <th width="80">WO Qnty</th>
	                    <th width="70">WO Rate</th>
	                    <th width="100">WO Amount</th>

	                    <th width="150">Supplier</th>
	                    <th width="100">PI No.</th>
	                    <th width="70">PI Date</th>
	                    <th width="70">Last Ship Date</th>
	                    <th width="80">Yarn Color</th>
	                    <th width="50">Count</th>
	                    <th width="250">Composition</th>
	                    <th width="80">Yarn Type</th>
	                    <th width="50">UOM</th>
	                    <th width="80">PI Qnty</th>
	                    <th width="70">PI Rate</th>
	                    <th width="100">PI Amount</th>
	                    <th width="70">Currency</th>

	                    <th width="70">LC Date</th>
	                    <th width="100">LC No</th>
	                    <th width="100">Issuing Bank</th>
	                    <th width="70">Pay Term</th>
	                    <th width="80">Tenor</th>
	                    <th width="100">LC Amount</th>

	                    <th width="80">MRR Qnty</th>
	                    <th width="100">MRR Value</th>
	                    <th width="80">Short Value</th>
	                    <th width="80">Pipe Line</th>
	                    <th width="70">1st Rcv Date</th>
	                    <th>Last Rcv Date</th>
	                </tr>
	            </thead>
	        </table>
	        <div style="width:5120px; overflow-y:scroll; max-height:200px;font-size:12px; overflow-x:hidden;" id="scroll_body" align="left">
	        <table width="5100px" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left">
	            <tbody>
	            <?
	            $k=1;
				
				$array_check=array();$m=1;$q=1;
	            foreach($req_result as $row)
	            {
	            	if($row[csf("wo_dtls_id")]=='') 
	            	{
	            		$row[csf("wo_dtls_id")]=0;
	            	}
	            	
					if(!in_array($row[csf("id")],$temp_arr_req))
					{
						$temp_arr_req[]=$row[csf("id")];
						if($m%2==0)$bgcolor="#F8F9D0";else $bgcolor="#C8C4FD"; 
						$m++;
					}
					
					$mrr_qnty=$pipe_wo_qnty=$pipe_pi_qnty=$pipe_line=0;$min_date=$max_date="";
					$mrr_value=$short_value=0;
					$booking_id=$receive_basis="";			

					$id=$row_result[csf('id')];
					$reqsn_rate=$row[csf("amount")]/$row[csf("req_quantity")];
					if($req_wo_recv_arr[$row[csf("wo_id")]][2][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]]['recv_amt']!="")
					{
						$wo_pi_ids=$row[csf("wo_id")];
					}
					else
					{
						$wo_pi_ids=$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"];
					}
					
					if($mrr_check_prev[$wo_pi_ids][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]]=="")
	                {
						$mrr_check_prev[$wo_pi_ids][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]]=$row[csf("wo_id")];
						if($req_wo_recv_arr[$row[csf("wo_id")]][2][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]]['recv_qnty']!="")
						$mrr_qnty=$req_wo_recv_arr[$row[csf("wo_id")]][2][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]]['recv_qnty']; 
						else $mrr_qnty=$req_wo_recv_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]][1][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]]['recv_qnty'];
						
						
						
						if($req_wo_recv_arr[$row[csf("wo_id")]][2][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]]['recv_amt']!="")
						{
							$mrr_value=$req_wo_recv_arr[$row[csf("wo_id")]][2][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]]['recv_amt'];
							$min_date=$req_wo_recv_arr[$row[csf("wo_id")]][2][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]]['min_date'];
							$max_date=$req_wo_recv_arr[$row[csf("wo_id")]][2][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]]['max_date'];
							$short_value=$row[csf("wo_amount")]-$mrr_value;
							$booking_id=$req_wo_recv_arr[$row[csf("wo_id")]][2][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]]['booking_id'];
							
							$receive_basis=2;
						}
						else 
						{
							
							$mrr_value=$req_wo_recv_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]][1][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]]['recv_amt'];
							$min_date=$req_wo_recv_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]][1][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]]['min_date'];
							$max_date=$req_wo_recv_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]][1][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]]['max_date'];
							
							$short_value=$row[csf("wo_amount")]-$pi_data_arr[$row[csf("wo_dtls_id")]]["amount"];
							$booking_id=$req_wo_recv_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]][1][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]]['booking_id'];
							$receive_basis=1;
						}
						
					}
					
					if($pi_data_arr[$row[csf("wo_dtls_id")]]["amount"]=="") $pi_data_arr[$row[csf("wo_dtls_id")]]["amount"]=0;
					if($mrr_value=="") $mrr_value=0;
					if($mrr_qnty=="") $mrr_qnty=0;
					if($mrr_value>0 && $mrr_qnty>0)  $recv_rate=$mrr_value/$mrr_qnty;
					
					$receiving_cond=0;
					if($cbo_receive_status==1 && ($mrr_value=="" || $mrr_value==0)) $receiving_cond=1;
					if($cbo_receive_status==2 && $receive_basis==2 && $row[csf("wo_amount")]>$mrr_value && $mrr_value>0) $receiving_cond=1;
					if($cbo_receive_status==2 && $receive_basis==1 && $pi_data_arr[$row[csf("wo_dtls_id")]]["amount"]>$mrr_value && $mrr_value>0) $receiving_cond=1;
					if($cbo_receive_status==3 && $receive_basis==2 && $mrr_value>=$row[csf("wo_amount")] && $mrr_value>0) $receiving_cond=1;
					if($cbo_receive_status==3 && $receive_basis==1 && $mrr_value>=$pi_data_arr[$row[csf("wo_dtls_id")]]["amount"] && $mrr_value>0) $receiving_cond=1;
					if($cbo_receive_status==4 && $receive_basis==2 && $mrr_value < $row[csf("wo_amount")]) $receiving_cond=1;
					if($cbo_receive_status==4 && $receive_basis==1 && $mrr_value < $pi_data_arr[$row[csf("wo_dtls_id")]]["amount"]) $receiving_cond=1;
					if($cbo_receive_status==5) $receiving_cond=1;


					// Yarn Purchase Requisition Print Link
					$print_format_id_requ=$print_report_format_yarnPurchaseRequ[0];
					if ($print_format_id_requ==134)  //Print button
					{		
						$report_title='Yarn Purchase Requisition';							
						$print_link_requ="<a href='#' onClick=\"print_report('".str_replace("'", "", $cbo_company_name)."*".$row[csf("id")]."*".$report_title."*".$row[csf("is_approved")]."', 'yarn_requisition_print', '../../commercial/work_order/requires/yarn_requisition_entry_controller')\"> ".$row[csf("requ_prefix_num")]." <a/>";
					} 
					else if ($print_format_id_requ==135)  //Print 2 Button
					{									
						$report_title='Yarn Purchase Requisition';							
						$print_link_requ="<a href='#' onClick=\"print_report('".str_replace("'", "", $cbo_company_name)."*".$row[csf("id")]."*".$report_title."*".$row[csf("basis")]."', 'yarn_requisition_print_2', '../../commercial/work_order/requires/yarn_requisition_entry_controller')\"> ".$row[csf("requ_prefix_num")]." <a/>";
						
					}
					else if ($print_format_id_requ==136)  //Print 3 Button
					{									
						$report_title='Yarn Purchase Requisition';							
						$print_link_requ="<a href='#' onClick=\"print_report('".str_replace("'", "", $cbo_company_name)."*".$row[csf("id")]."*".$report_title."*7*".$row[csf("basis")]."', 'yarn_requisition_print_3', '../../commercial/work_order/requires/yarn_requisition_entry_controller')\"> ".$row[csf("requ_prefix_num")]." <a/>";
						
					}
					else if ($print_format_id_requ==137)  //Print 4 Button
					{									
						$report_title='Yarn Purchase Requisition';							
						$print_link_requ="<a href='#' onClick=\"print_report('".str_replace("'", "", $cbo_company_name)."*".$row[csf("id")]."*".$report_title."', 'yarn_requisition_print_4', '../../commercial/work_order/requires/yarn_requisition_entry_controller')\"> ".$row[csf("requ_prefix_num")]." <a/>";
						
					}
					else if ($print_format_id_requ==64)  //Print 5 Button
					{									
						$report_title='Yarn Purchase Requisition';							
						$print_link_requ="<a href='#' onClick=\"print_report('".str_replace("'", "", $cbo_company_name)."*".$row[csf("id")]."*".$report_title."', 'yarn_requisition_print_5', '../../commercial/work_order/requires/yarn_requisition_entry_controller')\"> ".$row[csf("requ_prefix_num")]." <a/>";
						
					}
					else if ($print_format_id_requ==72)  //Print 6 Button
					{									
						$report_title='Yarn Purchase Requisition';							
						$print_link_requ="<a href='#' onClick=\"print_report('".str_replace("'", "", $cbo_company_name)."*".$row[csf("id")]."*".$report_title."*".$row[csf("basis")]."', 'yarn_requisition_print_6', '../../commercial/work_order/requires/yarn_requisition_entry_controller')\"> ".$row[csf("requ_prefix_num")]." <a/>";
						
					}
					else $print_link_requ=$row[csf("requ_prefix_num")];


					// Yarn Purchase Work Orde Print Link
					$print_format_id_wo=$print_report_format_yarnPurchaseOrder[0];
					if ($print_format_id_wo==78)  //Print button
					{		
						$report_title='Yarn Purchase Order';
						$css_style='YarnPurchaseOrderPrintButton';						
						$print_link_wo="<a href='#' onClick=\"print_report('".str_replace("'", "", $cbo_company_name)."*".$row[csf("wo_id")]."*".$report_title."*".$row[csf("ref_closing_status")]."*".$row[csf("pay_mode")]."*".$row[csf("wo_approved")]."*".$css_style."', 'yarn_work_order_print', '../../commercial/work_order/requires/yarn_work_order_controller')\"> ".$row[csf("wo_number_prefix_num")]." <a/>";
					} 
					else if ($print_format_id_wo==84)  //Print 2 Button
					{									
						$report_title='Yarn Purchase Order';							
						$css_style='YarnPurchaseOrderPrintButton';
						$print_link_wo="<a href='#' onClick=\"print_report('".str_replace("'", "", $cbo_company_name)."*".$row[csf("wo_id")]."*".$report_title."*1*".$row[csf("ref_closing_status")]."*".$row[csf("wo_approved")]."*".$css_style."', 'print_to_html_report', '../../commercial/work_order/requires/yarn_work_order_controller')\"> ".$row[csf("wo_number_prefix_num")]." <a/>";
						
					}
					else if ($print_format_id_wo==85)  //Print 3 Button
					{									
						$report_title='Yarn Purchase Order';
						$css_style='YarnPurchaseOrderPrintButton';						
						$print_link_wo="<a href='#' onClick=\"print_report('".str_replace("'", "", $cbo_company_name)."*".$row[csf("wo_id")]."*".$report_title."*2*".$row[csf("ref_closing_status")]."*".$row[csf("wo_approved")]."*".$css_style."', 'print_to_html_report2', '../../commercial/work_order/requires/yarn_work_order_controller')\"> ".$row[csf("wo_number_prefix_num")]." <a/>";
						
					}
					else if ($print_format_id_wo==193)  //Print 4 Button
					{									
						$report_title='PURCHASE ORDER';	
						$css_style='YarnPurchaseOrderPrintButton';					
						$print_link_wo="<a href='#' onClick=\"print_report('".str_replace("'", "", $cbo_company_name)."*".$row[csf("wo_id")]."*".$report_title."*4*".$row[csf("ref_closing_status")]."*".$row[csf("wo_approved")]."*".$css_style."', 'print_to_html_report4', '../../commercial/work_order/requires/yarn_work_order_controller')\"> ".$row[csf("wo_number_prefix_num")]." <a/>";
						
					}
					else if ($print_format_id_wo==129)  //Print 5 Button
					{									
						$report_title='Yarn Purchase Order';						
						$print_link_wo="<a href='#' onClick=\"print_report('".str_replace("'", "", $cbo_company_name)."*".$row[csf("wo_id")]."*".$report_title."*".$row[csf("ref_closing_status")]."*".$row[csf("pay_mode")]."*".$row[csf("wo_approved")]."', 'yarn_work_order_print5', '../../commercial/work_order/requires/yarn_work_order_controller')\"> ".$row[csf("wo_number_prefix_num")]." <a/>";
						
					}
					else if ($print_format_id_wo==72)  //Print 6 Button
					{									
						$report_title='Yarn Purchase Order';
						$css_style='YarnPurchaseOrderPrintButton';							
						$print_link_wo="<a href='#' onClick=\"print_report('".str_replace("'", "", $cbo_company_name)."*".$row[csf("wo_id")]."*".$report_title."*3*".$row[csf("ref_closing_status")]."*".$row[csf("wo_approved")]."*".$css_style."', 'print_to_html_report3', '../../commercial/work_order/requires/yarn_work_order_controller')\"> ".$row[csf("wo_number_prefix_num")]." <a/>";
						
					}				
					else $print_link_wo=$row[csf("wo_number_prefix_num")];

					if($receiving_cond==1)
					{						
						?>
	                    <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
	                        <td width="30" align="center" title="<? echo $cbo_receive_status."=".$receive_basis."=".$mrr_value."=".$row[csf("wo_amount")]."=".$pi_data_arr[$row[csf("wo_dtls_id")]]["amount"]; ?>"><p><? echo $k;//$row_result[csf('id')];?></p></td>
	                        <td width="50" align="center" title="<? echo $row[csf("id")]?>"><p><? echo $print_link_requ; ?></p></td>
	                        <td width="70" align="center"><p>&nbsp;<? if($row[csf("requisition_date")]!="" && $row[csf("requisition_date")]!='0000-00-00') echo change_date_format($row[csf("requisition_date")]); ?></p></td>
	                        <td width="80"><p><? echo $approval_arr[$row[csf("id")]]; ?></p></td>
	                        <td width="100"><p><? echo $buyer_arr[$row[csf("buyer_id")]]; ?></p></td>
	                        <td width="80"><p>&nbsp;<? echo implode(',',array_flip(array_flip(explode(',', rtrim($job_order_arr[$row[csf("job_no")]]['po_received_date'],',')))));; ?></p></td>
	                        <td width="150"><p><? echo implode(',',array_flip(array_flip(explode(',', rtrim($job_order_arr[$row[csf("job_no")]]['po_number'],','))))); ?></p></td>
	                        <td width="100"><p><? echo $job_order_arr[$row[csf("job_no")]]['style_ref_no']; ?></p></td>
	                        <td width="100"><p><? echo $row[csf("job_no")]; ?></p></td>
	                        <td width="150"><p><? echo implode(',',array_flip(array_flip(explode(',', rtrim($job_order_arr[$row[csf("job_no")]]['booking_no'],','))))); ?></p></td>
	                        <td width="130"><p>&nbsp;<? echo implode(',',array_flip(array_flip(explode(',', rtrim($job_order_arr[$row[csf("job_no")]]['booking_date'],','))))); ?></p></td>
	                        <td width="80"><p>&nbsp;<? echo implode(',',array_flip(array_flip(explode(',', rtrim($job_order_arr[$row[csf("job_no")]]['pub_shipment_date'],','))))); ?></p></td>
	                        <td width="80"><p>&nbsp;<? echo change_date_format($row[csf("delivery_date")]); ?></p></td>
	                        <td width="80" align="right"><p><? echo $job_order_arr[$row[csf("job_no")]]['job_quantity']; ?></p></td>
	                        <td width="80" align="center"><p><? echo $color_arr[$row[csf("color_id")]]; ?></p></td>
	                        <td width="50" align="center"><p>&nbsp;<? echo $yarnCount_arr[$row[csf("count_id")]]; ?></p></td>
	                        <td width="150"><p><? echo $composition[$row[csf("composition_id")]]." ".$row[csf("com_percent")]."%"; ?></p></td>
	                        <td width="80"><p><? echo $yarn_type[$row[csf("yarn_type_id")]]; ?></p></td>
	                        <td width="50"><p><? echo $unit_of_measurement[$row[csf("req_uom")]]; ?></p></td>
	                        <td width="80" align="right"><p><? echo number_format($row[csf("req_qnty")],2); ?></p></td>
	                        <td width="70" align="right"><p>&nbsp;<? echo number_format($row[csf("req_rate")],2); ?></p></td>
	                        <td width="100" align="right"><p><? echo number_format($row[csf("req_amt")],2); ?></p></td>
	                        <td width="75"  align="center"><p>&nbsp;<? if($row[csf("yarn_inhouse_date")]!="" && $row[csf("yarn_inhouse_date")]!="0000-00-00") echo change_date_format($row[csf("yarn_inhouse_date")]); ?></p></td>
	                        <td width="50" align="center" title="<? echo $row[csf("wo_id")]?>"><p><? echo $print_link_wo; ?></p></td>
	                        <td width="80"><p><? echo $color_arr[$row[csf("wo_color")]]; ?></p></td>
	                        <td width="50" align="center"><p>&nbsp;<? echo $yarnCount_arr[$row[csf("wo_count")]]; ?></p></td>
	                        <td width="250"><p>
	                        <? 
	                        if($row[csf("yarn_comp_type2nd")]>0) $wo_com_percent2=$row[csf("yarn_comp_percent2nd")]."%"; else $wo_com_percent2=" ";
	                        if ($row[csf("wo_yarn_comp_percent1st")] >0 ) $wo_com_percent1=$row[csf("wo_yarn_comp_percent1st")]."%"; else $wo_com_percent1 = "";
	                        echo $composition[$row[csf("wo_yarn_comp_type1st")]]." ".$wo_com_percent1." ".$composition[$row[csf("yarn_comp_type2nd")]]." ".$wo_com_percent2; ?></p></td>
	                        <td width="80"><p><? echo $yarn_type[$row[csf("wo_yarn_type")]]; ?></p></td>
	                        <td width="50"><p><? echo $unit_of_measurement[$row[csf("wo_uom")]]; ?></p></td>
	                        <td width="80" align="right"><p><? echo number_format($row[csf("wo_qnty")],2); ?></p></td>
	                        <td width="70" align="right"><p><? echo number_format($row[csf("wo_rate")],2); ?></p></td>
	                        <td width="100" align="right"><p><? echo number_format($row[csf("wo_amount")],2); ?></p></td>	

	                        <td width="150"><p><? $suplier_pi_arr=array_unique(explode(",",chop($pi_data_arr[$row[csf("wo_dtls_id")]]["pi_suplier"]," , ")));
	                        //echo "10**".$row[csf("wo_dtls_id")]; die; //print_r($suplier_pi_arr);
	                        $supplier='';
	                        foreach ($suplier_pi_arr as $value) 
	                        {
	                        	$supplier.=$supplier_arr[$value].",";
	                        }
	                        echo chop($supplier,","); ?></p></td>
	                        <td width="100"><p><? $pi_num=implode(" , ",array_unique(explode(",",chop($pi_data_arr[$row[csf("wo_dtls_id")]]["pi_number"]," , ")))); echo $pi_num; ?></p></td>
	                        <td width="70"><p>&nbsp;
	                        <? 
	                        $pi_date_arr=array_unique(explode(",",chop($pi_data_arr[$row[csf("wo_dtls_id")]]["pi_date"]," , ")));
	                        $pi_date='';
	                        foreach ($pi_date_arr as $value) 
	                        {
	                        	if($value !="" && $value!="0000-00-00")
	                        	{
	                        		$pi_date.=change_date_format($value)."</br>";
	                        	}
	                        }
	                        //$pi_date=implode(",",$pi_date_arr); 
	                        //if($pi_date_arr[0]!="" && $pi_date_arr[0]!="0000-00-00") echo change_date_format($pi_date_arr[0]); 
	                        echo chop($pi_date,"</br>");
	                        ?></p></td>
	                        <td width="70"><p>&nbsp;
	                        <? 
	                        $pi_last_ship_date_arr=array_unique(explode(",",chop($pi_data_arr[$row[csf("wo_dtls_id")]]["last_shipment_date"]," , "))); 

	                        $shipment_date='';
	                        foreach ($pi_last_ship_date_arr as $value) 
	                        {
	                        	if($value !="" && $value!="0000-00-00")
	                        	{
	                        		$shipment_date.=change_date_format($value)."</br>";
	                        	}
	                        }
	                       	// if($pi_last_ship_date_arr[0]!="" && $pi_last_ship_date_arr[0]!="0000-00-00") echo change_date_format($pi_last_ship_date_arr[0]); 
	                        echo chop($shipment_date,"</br>");
	                        ?></p></td>
	                        <td width="80" title="<? echo "color id=".$pi_data_arr[$row[csf("wo_dtls_id")]]["color_id"];?>"><p><? echo  $color_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["color_id"]]; ?></p></td>
	                        <td width="50" title="<? echo "count id=".$pi_data_arr[$row[csf("wo_dtls_id")]]["count_name"];?>"><p>&nbsp;<? echo  $yarnCount_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["count_name"]]; ?></p></td>
	                        <td width="250" title="<? echo "count id=".$pi_data_arr[$row[csf("wo_dtls_id")]]["yarn_composition_item1"];?>"><p>
	                        <?
	                        if($pi_data_arr[$row[csf("wo_dtls_id")]]["yarn_composition_item2"]>0) $com_percent2=$pi_data_arr[$row[csf("wo_dtls_id")]]["yarn_comp_percent2nd"]."%"; else $com_percent2="";
	                        if($pi_data_arr[$row[csf("wo_dtls_id")]]["yarn_composition_item1"]>0) $com_percent=$pi_data_arr[$row[csf("wo_dtls_id")]]["yarn_composition_percentage1"]."%"; else $com_percent="";
	                        echo  $composition[$pi_data_arr[$row[csf("wo_dtls_id")]]["yarn_composition_item1"]]." ".$com_percent." ".$composition[$pi_data_arr[$row[csf("wo_dtls_id")]]["yarn_composition_item2"]]." ".$com_percent2;
	                        ?></p></td>
	                        <td width="80" title="<? echo "yarn type id=".$pi_data_arr[$row[csf("wo_dtls_id")]]["yarn_type"];?>"><p><? echo  $yarn_type[$pi_data_arr[$row[csf("wo_dtls_id")]]["yarn_type"]]; ?></p></td>
	                        <td width="50"><p><? echo  $unit_of_measurement[$pi_data_arr[$row[csf("wo_dtls_id")]]["uom"]]; ?></p></td>
	                        <td width="80" align="right"><p><? $pi_qnty=$pi_data_arr[$row[csf("wo_dtls_id")]]["quantity"]; echo  number_format($pi_qnty,2); ?></p></td>
	                        <td width="70" align="right"><p><? $pi_rate=$pi_data_arr[$row[csf("wo_dtls_id")]]["rate"]; echo  number_format($pi_rate,2); ?></p></td>
	                        <td width="100" align="right"><p><? $pi_amt=$pi_data_arr[$row[csf("wo_dtls_id")]]["amount"]; echo  number_format($pi_amt,2); ?></p></td>
	                        <td width="70"><p><? $pi_curency_arr=array_unique(explode(",",chop($pi_data_arr[$row[csf("wo_dtls_id")]]["currency_id"]," , "))); 
	                        $currency_val='';
	                        foreach ($pi_curency_arr as $value) 
	                        {
	                        	$currency_val.=$currency[$value].",";
	                        }
	                        echo chop($currency_val,",");
	                        //echo  $currency[$pi_curency_arr[0]]; ?></p></td>
	                        <?
	                        $btb_lc_no=$btb_issue_bank=$btb_pay_term=$btb_tenor="";$btb_lc_amount=0; $btb_tenor="";$btb_lc_date="";
	                        if(!in_array($pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"],$temp_arr_btb))
	                        {
	                            $temp_arr_btb[]=$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"];
	                            $btb_lc_no_arr=array_unique(explode(",",chop($btb_data_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]]["lc_number"]," , ")));
	                            $btb_lc_no=implode(" , ",$btb_lc_no_arr);

	                            $btb_issue_bank_arr=array_unique(explode(",",chop($btb_data_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]]["issuing_bank_id"]," , "))); 
	                            foreach ($btb_issue_bank_arr as $value) 
		                        {
		                        	$btb_issue_bank.=$bank_arr[$value].",";
		                        }

	                            $btb_pay_tarm_arr=array_unique(explode(",",chop($btb_data_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]]["payterm_id"]," , ")));
		                        foreach ($btb_pay_tarm_arr as $value) 
		                        {
		                        	$btb_pay_term.=$pay_term[$value].",";
		                        }

	                            $btb_tenor_arr=array_unique(explode(",",chop($btb_data_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]]["tenor"]," , ")));
	                           	$btb_tenor=implode(" Days, ",$btb_tenor_arr);

	                            $btb_lc_amount=$btb_data_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]]["lc_value"];
	                            $btb_lc_date_arr=array_unique(explode(",",chop($btb_data_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]]["lc_date"]," , "))); 
	                            $btb_lc_date='';
		                        foreach ($btb_lc_date_arr as $value) 
		                        {
		                        	if($value !="" && $value!="0000-00-00")
		                        	{
		                        		$btb_lc_date.=change_date_format($value)."</br>";
		                        	}
		                        }
	                        }
	                        else
	                        {
	                            $btb_lc_no=$btb_issue_bank=$btb_pay_term="";
	                            $btb_lc_amount=0;
	                            $btb_tenor="";
	                            $btb_lc_date="";
	                        }
	                        ?>
	                        <td width="70"><p>&nbsp;<? echo chop($btb_lc_date,"</br>");  ?></p></td>
	                        
	                        <td width="100"><p><?  echo chop( $btb_lc_no,","); ?></p></td>
	                        <td width="100"><p><? echo  chop( $btb_issue_bank,","); ?></p></td>
	                        <td width="70"><p><? echo chop( $btb_pay_term,",");  ?></p></td>
	                        <td width="80"><p><? if ($btb_tenor!='') echo $btb_tenor." Days";  ?></p></td>
	                        <td width="100" align="right"><p><? if($btb_lc_amount>0) echo number_format($btb_lc_amount,2); ?></p></td>
	                        <?
	                        //if(!in_array($row[csf("wo_id")],$temp_arr_rcv))
							$pipe_line="";
							if($mrr_check[$wo_pi_ids][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]]=="")
	                        {
								$mrr_check[$wo_pi_ids][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]]=$row[csf("color_id")];
								$pipe_wo_qnty=$wo_pipe_array[$wo_pi_ids][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]];
								$pipe_pi_qnty=$pi_pipe_array[$wo_pi_ids][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]];
								//echo $pipe_wo_qnty.'system'.$pipe_pi_qnty;
								$pipe_line=(($pipe_wo_qnty+$pipe_pi_qnty)-$mrr_qnty);
								$total_pipe_line += $pipe_line;
	                            if($mrr_qnty>0)
	                            { 
									$pipe_mrr_qnty=$mrr_qnty;
									
	                                ?>
	                                <td width="80" align="right" title="<? echo $wo_pi_ids."=".$receive_basis."=".$row[csf("color_id")]."=".$row[csf("yarn_type_id")]."=".$row[csf("count_id")]."=".$row[csf("composition_id")];?>"><p><a href="##" onclick="fn_mrr_details('<? echo $booking_id;?>','<? echo $receive_basis;?>','<? echo $row[csf("color_id")];?>','<? echo $row[csf("yarn_type_id")];?>','<? echo $row[csf("count_id")];?>','<? echo $row[csf("composition_id")];?>','receive_details_popup')"><? echo number_format($mrr_qnty,2); ?></a></p></td>
	                                <?
									 $total_mrr_qnty  += $mrr_qnty;
	                    			 //
	                            }
	                            else
	                            {
	                                ?>
	                                <td width="80" align="right" title="<? echo $wo_pi_ids."=".$receive_basis."=".$row[csf("color_id")]."=".$row[csf("yarn_type_id")]."=".$row[csf("count_id")]."=".$row[csf("composition_id")];?>"><p></p></td>
	                                <?
	                            }
	                        }
	                        else
	                        {
	                            $mrr_qnty=$mrr_value=0;
								//if($mrr_check[$wo_pi_ids][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]]=="")$pipe_mrr_qnty=0;
	                            ?>
	                            <td width="80" align="right" title="<? echo $wo_pi_ids."=".$receive_basis."=".$row[csf("color_id")]."=".$row[csf("yarn_type_id")]."=".$row[csf("count_id")]."=".$row[csf("composition_id")];?>"><p></p></td>
	                            <?
	                        }
	                        ?>
	                        <td width="100" align="right"><p><? if($mrr_value>0) { echo number_format($mrr_value,2); $total_mrr_value += $mrr_value; }?></p></td>
	                        <td align="right" width="80"><p><? echo number_format($short_value,2); ?></p></td>

	                        <td align="right" width="80" title="<? echo $wo_pi_ids."=".$pipe_wo_qnty."=".$pipe_pi_qnty."=".$pipe_mrr_qnty."=".$mrr_qnty; ?>"><? echo number_format($pipe_line,2); ?></td>
	                        <td width="70" align="center">&nbsp;<? if($min_date!="" && $min_date!="0000-00-00" && $mrr_qnty>0) echo change_date_format($min_date);?></td>
	                        <td align="center">&nbsp;<? if($max_date!="" && $max_date!="0000-00-00" && $mrr_qnty>0) echo change_date_format($max_date);?></td>
	                    </tr>
	                    <?
	                    $k++;
						$total_req_qty   += $row[csf("req_qnty")];
	                    $total_req_amount+=$row[csf("req_amt")];
	                    $total_wo_qty+=$row[csf("wo_qnty")];
	                    $total_wo_amount+=$row[csf("wo_amount")];
	                    $total_pi_qnty   += $pi_data_arr[$row[csf("wo_dtls_id")]]["quantity"];
	                    $total_pi_amt    += $pi_data_arr[$row[csf("wo_dtls_id")]]["amount"];
	                    $total_short_amt += $short_amt;                    
					}				
	            }
	            ?>
	            </tbody>
	        </table>
	        </div>
	        <table cellspacing="0" width="5100px"  border="1" rules="all" class="rpt_table" id="report_table_footer" >
	            <tfoot>
	            	<th width="30"></th>
	                <th width="50">&nbsp;</th>
	                <th width="70">&nbsp;</th>
	                <th width="80">&nbsp;</th>
	                <th width="100">&nbsp;</th>
	                <th width="80">&nbsp;</th>


	                <th width="150">&nbsp;</th>
	                <th width="100">&nbsp;</th>
	                <th width="100">&nbsp;</th>
	                <th width="150">&nbsp;</th>
	                <th width="130">&nbsp;</th>
	                <th width="80">&nbsp;</th>
	                <th width="80">&nbsp;</th>
	                <th width="80">&nbsp;</th>


	                <th width="80">&nbsp;</th>
	                <th width="50">&nbsp;</th>
	                <th width="150">&nbsp;</th>
	                <th width="80">&nbsp;</th>
	                <th width="50"><strong>Total:</strong></th>
					<th width="80" id="value_tot_req_qty_id"><p><? echo number_format($total_req_qty,2);?></p></th>
	                <th width="70">&nbsp;</th>
					<th width="100" id="value_tot_req_amount_id"><? echo number_format($total_req_amount,2);?></th>
	                <th width="75" align="right"></th>
	                <th width="50" align="center">&nbsp;</th>
	                <th width="80" align="right">&nbsp;</th>
	                <th width="50" align="center">&nbsp;</th>
	                <th width="250" align="center">&nbsp;</th>
	                <th width="80" align="center">&nbsp;</th>
	                <th width="50" align="center">&nbsp;</th>
	                <th width="80" align="right" id="value_tot_wo_qty_id"><? echo number_format($total_wo_qty,2);?></th>
	                <th width="70" align="right">&nbsp;</th>
	                <th width="100" align="right" id="value_tot_wo_amount_id"><? echo number_format($total_wo_amount,2);?></th>
	                <th width="150" align="center">&nbsp;</th>
	                <th width="100" align="center">&nbsp;</th>
	                <th width="70" align="center">&nbsp;</th>
	                <th width="70" align="center">&nbsp;</th>
	                <th width="80" align="center">&nbsp;</th>
	                <th width="50" align="center">&nbsp;</th>
	                <th width="250" align="center">&nbsp;</th>
	                <th width="80" align="center">&nbsp;</th>

	                <th width="50" align="center"></th>
	                <th width="80" align="right" id="value_tot_pi_qnty_id"><? echo number_format($total_pi_qnty,2); ?></th>

	                <th width="70" align="center"></th>
	                <th width="100" align="right" id="value_tot_pi_amt_id"><? echo number_format($total_pi_amt,2); ?></th>

	                <th width="70" align="center">&nbsp;</th>
	                <th width="70" align="center">&nbsp;</th>
	                <th width="100" align="center">&nbsp;</th>
	                <th align="right" width="100">&nbsp;</th>
	                <th width="70" align="center">&nbsp;</th>
	                <th width="80" align="center">&nbsp;</th>

	                <th width="100" align="center"></th>
	                <th width="80" align="right" id="value_tot_mrr_qnty_id"><? echo number_format($total_mrr_qnty,2); ?></th>                
	                <th width="100" align="right" id="value_tot_mrr_value_id"><? echo number_format($total_mrr_value,2); ?></th>
	                <th width="80" align="right" id="value_tot_short_amt_id"><? echo number_format($total_short_amt,2); ?></th>
	                <th width="80" align="right" id="value_tot_pipe_line_id" title="<? echo $test_data; ?>"><? echo number_format($total_pipe_line,2); ?></th>
	                <th width="70" align="center"></th>
	                <th align="center"></th>
	            </tfoot>        
			</table>
	        
	        </div>
	    	<br />
	        <div style="width:2770px" >
	        <?
			
			if($cbo_based_on==3)
			{
				if(!empty($pi_wo_dtls_id_all_arr))
				{
					$pi_wo_dtls_id_all_arr=array_chunk(array_unique($pi_wo_dtls_id_all),999);
					$sql_wo_independ="select d.id as wo_id, d.wo_number_prefix_num, d.wo_date, d.supplier_id, e.id as wo_dtls_id, e.color_name as wo_color, e.yarn_count as wo_count, e.yarn_comp_type1st as wo_yarn_comp_type1st, e.yarn_comp_percent1st as wo_yarn_comp_percent1st, e.yarn_comp_type2nd, e.yarn_comp_percent2nd, e.yarn_type as wo_yarn_type, e.uom as wo_uom, e.supplier_order_quantity as wo_qnty, e.rate as wo_rate, e.amount as wo_amount, d.company_name, d.wo_basis_id, d.is_approved, d.pay_mode, d.ref_closing_status
				from wo_non_order_info_mst d, wo_non_order_info_dtls e
				where d.id=e.mst_id and d.wo_basis_id=2 and d.company_name=$cbo_company_name and d.entry_form=144 and e.item_category_id=1 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 $str_cond_independ ";
					$p=1;
					foreach($pi_wo_dtls_id_all_arr as $pi_wo_dtls_id)
					{
						if($p==1) $sql_wo_independ .=" and (e.id in(".implode(',',$pi_wo_dtls_id).")"; else $sql_wo_independ .=" or e.id in(".implode(',',$pi_wo_dtls_id).")";
						$p++;
					}
					$sql_wo_independ .=" ) ";
				}
				
			}
			else
			{
				$sql_wo_independ="select d.id as wo_id, d.wo_number_prefix_num, d.wo_date, d.supplier_id, e.id as wo_dtls_id, e.color_name as wo_color, e.yarn_count as wo_count, e.yarn_comp_type1st as wo_yarn_comp_type1st, e.yarn_comp_percent1st as wo_yarn_comp_percent1st, e.yarn_comp_type2nd, e.yarn_comp_percent2nd, e.yarn_type as wo_yarn_type, e.uom as wo_uom, e.supplier_order_quantity as wo_qnty, e.rate as wo_rate, e.amount as wo_amount, d.company_name, d.wo_basis_id, d.is_approved, d.pay_mode, d.ref_closing_status
				from wo_non_order_info_mst d, wo_non_order_info_dtls e
				where d.id=e.mst_id and d.wo_basis_id=2 and d.company_name=$cbo_company_name and d.entry_form=144 and e.item_category_id=1 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0   $str_cond_independ ";
			}
			$req_result_independ=sql_select($sql_wo_independ);

			$print_report_format=return_field_value("format_id","lib_report_template","template_name =$cbo_company_name and module_id=5 and report_id=45 and is_deleted=0 and status_active=1");
			$print_report_format_arr=explode(',',$print_report_format);
			$printButton=$print_report_format_arr[0];

			$Print_Report_Format_PI=return_field_value("format_id","lib_report_template","template_name =$cbo_company_name and module_id=5 and report_id=183 and is_deleted=0 and status_active=1");
			$Print_Report_Format_Arr=explode(',',$Print_Report_Format_PI);
			$PiButtonID=$Print_Report_Format_Arr[0];

			
			if($cbo_based_on==2 || $cbo_based_on==3)
			{
				?>
				<table width="2950"  align="left">
					<tr>
						<td style="font-size:18; font-weight:bold;">Based on Independent WO</td>
					</tr>
				</table>
				<br />
				<table width="2950" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_2"  align="left">
					<thead>
						<tr>
							<th width="30" rowspan="2">Sl</th>
							<th colspan="10">Work Order Details</th>
							<th colspan="12">PI Details</th>
							<th colspan="6">BTB LC Details</th>
							<th colspan="4">Matarials Received Information</th>
						</tr>
						<tr>
							<th width="50">WO No.</th>
							<th width="80">Yarn Color</th>
							<th width="50">Count</th>
							<th width="230">Composition</th>
							<th width="80">Yarn Type</th>
							<th width="50">UOM</th>
							<th width="80">WO Qnty</th>
							<th width="70">WO Rate</th>
							<th width="100">WO Amount</th>
							<th width="150">Supplier</th>
							<th width="100">PI No.</th>
							<th width="70">PI Date</th>
							<th width="70">Last Ship Date</th>
							<th width="80">Yarn Color</th>
							<th width="50">Count</th>
							<th width="230">Composition</th>
							<th width="80">Yarn Type</th>
							<th width="50">UOM</th>
							<th width="80">PI Qnty</th>
							<th width="70">PI Rate</th>
							<th width="100">PI Amount</th>
							<th width="70">Currency</th>
							<th width="70">LC Date</th>
							<th width="100">LC No</th>
							<th width="100">Issuing Bank</th>
							<th width="70">Pay Term</th>
							<th width="80">Tenor</th>
							<th width="100">LC Amount</th>
							<th width="80">MRR Qnty</th>
							<th width="100">MRR Value</th>
							<th width="100">Short Value</th>
	                        <th >Pipe Line</th>
						</tr>
					</thead>
				</table>
				<div style="width:2950px; overflow-y:scroll; max-height:200px;font-size:12px; overflow-x:hidden;" id="scroll_body2" align="left">
				<table width="2932" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body2" align="left">
					<tbody>
					<?
					$i=1;
					//print_r($wo_po_arr);die;
					$array_check=array();
					foreach($req_result_independ as $row)
					{ 
						if ($i%2==0)
						$bgcolor="#E9F3FF";
						else
						$bgcolor="#FFFFFF";
						$pine_wo_qnty=$pipe_pi_qnty=$pipe_line=$mrr_qnty=$mrr_value=$short_amt="";
						$wo_rate_inde=$req_wo_recv_arr[$row[csf("id")]][$row[csf("prod_id")]]['recv_amt']/$req_wo_recv_arr[$row[csf("id")]][$row[csf("prod_id")]]['recv_qnty'];
						
						
						if($req_wo_recv_arr[$row[csf("wo_id")]][2][$row[csf("wo_color")]][$row[csf("wo_yarn_type")]][$row[csf("wo_count")]][$row[csf("wo_yarn_comp_type1st")]]['recv_qnty']!="") 
						$mrr_qnty=$req_wo_recv_arr[$row[csf("wo_id")]][2][$row[csf("wo_color")]][$row[csf("wo_yarn_type")]][$row[csf("wo_count")]][$row[csf("wo_yarn_comp_type1st")]]['recv_qnty']; 
						else $mrr_qnty=$req_wo_recv_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]][1][$row[csf("wo_color")]][$row[csf("wo_yarn_type")]][$row[csf("wo_count")]][$row[csf("wo_yarn_comp_type1st")]]['recv_qnty'];
						
						if($req_wo_recv_arr[$row[csf("wo_id")]][2][$row[csf("wo_color")]][$row[csf("wo_yarn_type")]][$row[csf("wo_count")]][$row[csf("wo_yarn_comp_type1st")]]['recv_amt']!="")
						{
							$mrr_value=$req_wo_recv_arr[$row[csf("wo_id")]][2][$row[csf("wo_color")]][$row[csf("wo_yarn_type")]][$row[csf("wo_count")]][$row[csf("wo_yarn_comp_type1st")]]['recv_amt'];
							$short_amt=$row[csf("wo_amount")]-$mrr_value;
							
							$booking_id=$req_wo_recv_arr[$row[csf("wo_id")]][2][$row[csf("wo_color")]][$row[csf("wo_yarn_type")]][$row[csf("wo_count")]][$row[csf("wo_yarn_comp_type1st")]]['booking_id'];
							$receive_basis=2;
						}
						else 
						{
							$mrr_value=$req_wo_recv_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]][1][$row[csf("wo_color")]][$row[csf("wo_yarn_type")]][$row[csf("wo_count")]][$row[csf("wo_yarn_comp_type1st")]]['recv_amt'];
							$short_amt=$pi_data_arr[$row[csf("wo_dtls_id")]]["amount"]-$mrr_value;
							$booking_id=$req_wo_recv_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]][1][$row[csf("wo_color")]][$row[csf("wo_yarn_type")]][$row[csf("wo_count")]][$row[csf("wo_yarn_comp_type1st")]]['booking_id'];
							$receive_basis=1;
						}
						
						$receiving_cond=0;
						if($cbo_receive_status==1 && ($mrr_value=="" || $mrr_value==0)) $receiving_cond=1;
						if($cbo_receive_status==2 && $receive_basis==2 && $row[csf("wo_amount")]>$mrr_value && $mrr_value>0) $receiving_cond=1;
						if($cbo_receive_status==2 && $receive_basis==1 && $pi_data_arr[$row[csf("wo_dtls_id")]]["amount"]>$mrr_value && $mrr_value>0) $receiving_cond=1;
						if($cbo_receive_status==3 && $receive_basis==2 && $mrr_value>=$row[csf("wo_amount")] && $mrr_value>0) $receiving_cond=1;
						if($cbo_receive_status==3 && $receive_basis==1 && $mrr_value>=$pi_data_arr[$row[csf("wo_dtls_id")]]["amount"] && $mrr_value>0) $receiving_cond=1;
						if($cbo_receive_status==4 && $receive_basis==2 && $mrr_value < $row[csf("wo_amount")]) $receiving_cond=1;
						if($cbo_receive_status==4 && $receive_basis==1 && $mrr_value < $pi_data_arr[$row[csf("wo_dtls_id")]]["amount"]) $receiving_cond=1;
						if($cbo_receive_status==5) $receiving_cond=1;
						if($receiving_cond==1)
						{ 
							?>
	                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
	                            <td width="30" title="<? echo $cbo_receive_status."=".$receive_basis."=".$mrr_value."=".$row[csf("wo_amount")]."=".$pi_data_arr[$row[csf("wo_dtls_id")]]["amount"]; ?>"><? echo $i; ?></td>                         
								<td width="50" align="center"> <a href="##" onClick="Fnc_Yd_HyperLink('<?=$printButton ?>','<?=$row[csf("company_name")]?>','<?=$row[csf("wo_id")]?>','','<?=$row[csf("ref_closing_status")]?>','<?=$row[csf("pay_mode")]?>','<?=$row[csf("is_approved")]?>','<?=$row[csf("wo_basis_id")]?>','<?=$row[csf("supplier_id")]?>','')" > <p><? echo $row[csf("wo_number_prefix_num")]; ?></p></a></td>
	                            <td width="80"><p><? echo $color_arr[$row[csf("wo_color")]]; ?></p></td>
	                            <td width="50" align="center"><p>&nbsp;<? echo $yarnCount_arr[$row[csf("wo_count")]]; ?></p></td>
	                            <td width="230"><p>
	                            <?
	                            if ($row[csf("yarn_comp_type2nd")]>0) $compo_percent2=$row[csf("yarn_comp_percent2nd")]."%"; else $compo_percent2="";
	                            echo $composition[$row[csf("wo_yarn_comp_type1st")]]." ".$row[csf("wo_yarn_comp_percent1st")]."% ".$composition[$row[csf("yarn_comp_type2nd")]]." ".$compo_percent2; 
	                            ?></p></td>
	                            <td width="80"><p><? echo $yarn_type[$row[csf("wo_yarn_type")]]; ?></p></td>
	                            <td width="50"><p><? echo $unit_of_measurement[$row[csf("wo_uom")]]; ?></p></td>
	                            <td width="80" align="right"><p><? echo number_format($row[csf("wo_qnty")],0); ?></p></td>
	                            <td width="70" align="right"><p><? echo number_format($row[csf("wo_rate")],2); ?></p></td>
	                            <td width="100" align="right"><p><? echo number_format($row[csf("wo_amount")],2); ?></p></td>
	                           
	                            <td width="150"><p><? echo $supplier_arr[$row[csf("supplier_id")]]; ?></p></td>
								
	                            <td width="100"><p><a href="##" onClick="Fnc_Pro_Farma_InvoiceV2_Print_hyperLink('<?=$PiButtonID?>','<?=$row[csf("company_name")]?>','<?=$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]?>','<?=$pi_data_arr[$row[csf("wo_dtls_id")]]["entry_form"]?>','<?=$pi_data_arr[$row[csf("wo_dtls_id")]]["item_category_id"]?>')"> <p><? $pi_num=implode(" , ",array_unique(explode(",",chop($pi_data_arr[$row[csf("wo_dtls_id")]]["pi_number"]," , ")))); echo $pi_num; ?></p></a></td>								
	                            <td width="70"><p>&nbsp;
	                            <? 
		                            $pi_date_arr=array_unique(explode(",",chop($pi_data_arr[$row[csf("wo_dtls_id")]]["pi_date"]," , "))); 
		                            $pi_date='';
			                        foreach ($pi_date_arr as $value) 
			                        {
			                        	if($value !="" && $value!="0000-00-00")
			                        	{
			                        		$pi_date.=change_date_format($value)."</br>";
			                        	}
			                        }
		                        	echo chop($pi_date,"</br>");
	                            ?></p></td>
	                            <td width="70"><p>&nbsp;
	                            <? 
	                            $pi_last_ship_date_arr=array_unique(explode(",",chop($pi_data_arr[$row[csf("wo_dtls_id")]]["last_shipment_date"]," , "))); 
	                            $shipment_date='';
		                        foreach ($pi_last_ship_date_arr as $value) 
		                        {
		                        	if($value !="" && $value!="0000-00-00")
		                        	{
		                        		$shipment_date.=change_date_format($value)."</br>";
		                        	}
		                        }
		                        echo chop($shipment_date,"</br>");
	                            ?></p></td>
	                            <td width="80"><p><? echo  $color_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["color_id"]]; ?></p></td>
	                            <td width="50"><p>&nbsp;<? echo  $yarnCount_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["count_name"]]; ?></p></td>
	                            <td width="230"><p>
	                            <?
	                            if($pi_data_arr[$row[csf("wo_dtls_id")]]["yarn_composition_item2"]>0) $com_percent2=$pi_data_arr[$row[csf("wo_dtls_id")]]["yarn_comp_percent2nd"]."%"; else $com_percent2="";
	                            if($pi_data_arr[$row[csf("wo_dtls_id")]]["yarn_composition_item1"]>0) $com_percent=$pi_data_arr[$row[csf("wo_dtls_id")]]["yarn_composition_percentage1"]."%"; else $com_percent="";
	                            echo  $composition[$pi_data_arr[$row[csf("wo_dtls_id")]]["yarn_composition_item1"]]." ".$com_percent." ".$composition[$pi_data_arr[$row[csf("wo_dtls_id")]]["yarn_composition_item2"]]." ".$com_percent2;  
	                            ?></p></td>
	                            <td width="80"><p><? echo  $yarn_type[$pi_data_arr[$row[csf("wo_dtls_id")]]["yarn_type"]]; ?></p></td>
	                            <td width="50"><p><? echo  $unit_of_measurement[$pi_data_arr[$row[csf("wo_dtls_id")]]["uom"]]; ?></p></td>
	                            <td width="80" align="right"><p><? $pi_qnty=$pi_data_arr[$row[csf("wo_dtls_id")]]["quantity"]; echo  number_format($pi_qnty,0); ?></p></td>
	                            <td width="70" align="right"><p><? $pi_rate=$pi_data_arr[$row[csf("wo_dtls_id")]]["rate"]; echo  number_format($pi_rate,2); ?></p></td>
	                            <td width="100" align="right"><p><? $pi_amt=$pi_data_arr[$row[csf("wo_dtls_id")]]["amount"]; echo  number_format($pi_amt,2); ?></p></td>
	                            <td width="70"><p><? $pi_curency_arr=array_unique(explode(",",chop($pi_data_arr[$row[csf("wo_dtls_id")]]["currency_id"]," , "))); echo  $currency[$pi_curency_arr[0]]; ?></p></td>
	                            <td width="70"><p>&nbsp;
	                            <? 
	                            $btb_lc_date_arr=array_unique(explode(",",chop($btb_data_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]]["lc_date"]," , "))); 
	                            $btb_lc_date='';
		                        foreach ($btb_lc_date_arr as $value) 
		                        {
		                        	if($value !="" && $value!="0000-00-00")
		                        	{
		                        		$btb_lc_date.=change_date_format($value)."</br>";
		                        	}
		                        }
	                           	echo chop($btb_lc_date,"</br>"); 
	                            ?></p></td>
	                            <td width="100"><p><? $btb_lc_no_arr=array_unique(explode(",",chop($btb_data_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]]["lc_number"]," , "))); 
	                            $btb_lc_no=implode(" , ",$btb_lc_no_arr);
	                            echo $btb_lc_no;  ?></p></td>
	        
	                            <td width="100"><p><? $btb_issue_bank_arr=array_unique(explode(",",chop($btb_data_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]]["issuing_bank_id"]," , "))); 
	                            foreach ($btb_issue_bank_arr as $value) 
		                        {
		                        	$btb_issue_bank.=$bank_arr[$value].",";
		                        }
	                            echo chop($btb_issue_bank,",");  ?></p></td>
	                            <td width="70"><p><? $btb_pay_tarm_arr=array_unique(explode(",",chop($btb_data_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]]["payterm_id"]," , "))); 
								foreach ($btb_pay_tarm_arr as $value) 
		                        {
		                        	$btb_pay_term.=$pay_term[$value].",";
		                        }
	                            echo chop($btb_pay_term,","); ?></p></td>
	                            <td width="80"><p><? $btb_tenor_arr=array_unique(explode(",",chop($btb_data_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]]["tenor"]," , "))); 
	                            $btb_tenor=implode(" Days, ",$btb_tenor_arr);
	                            if ($btb_tenor!='') echo $btb_tenor." Days"; ?></p></td>
	                            <td width="100" align="right"><p><? $btb_lc_amount=$btb_data_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]]["lc_value"]; echo number_format($btb_lc_amount,2); ?></p></td>
	                            <?
	                            if($mrr_qnty>0)
	                            {
	                                ?>
	                                <td width="80" align="right"><p><a href="##" onclick="fn_mrr_details('<? echo $booking_id;?>','<? echo $receive_basis;?>','<? echo $row[csf("wo_color")];?>','<? echo $row[csf("wo_yarn_type")];?>','<? echo $row[csf("wo_count")];?>','<? echo $row[csf("wo_yarn_comp_type1st")];?>','receive_details_popup','<? echo $piIds;?>')"><? echo number_format($mrr_qnty,2);?> </a></p></td>
	                                <?
	                            }
	                            else
	                            {
	                                ?>
	                                <td width="80" align="right"></td>
	                                <?
	                            }
	                            ?>
	                            <td width="100" align="right"><p> <? echo number_format($mrr_value,2); ?></p></td>
	                            <td align="right" width="100"><p><? echo number_format($short_amt,2); ?></p></td>
	                            <td align="right"><p>
	                            <?
	                            //$pine_wo_qnty=$pipe_pi_qnty=$pipe_line 
	                            $pine_wo_qnty=$wo_pipe_array[$row[csf("wo_id")]][$row[csf("wo_color")]][$row[csf("wo_yarn_type")]][$row[csf("wo_count")]][$row[csf("wo_yarn_comp_type1st")]];
	                            $pipe_pi_qnty=$pi_pipe_array[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]][$row[csf("wo_color")]][$row[csf("wo_yarn_type")]][$row[csf("wo_count")]][$row[csf("wo_yarn_comp_type1st")]];
	                            $pipe_line=(($pine_wo_qnty+$pipe_pi_qnty)-$mrr_qnty);
	                            if(number_format($pipe_line,2) > 0.00) echo number_format($pipe_line,2); else echo "0.00";
	                            ?>
	                            </p></td>
	                        </tr>
	                        <?
							$tot_ind_wo_qnty+=$row[csf("wo_qnty")];
							$tot_ind_wo_amount+=$row[csf("wo_amount")];
							$tot_ind_pi_qnty+=$pi_qnty;
							$tot_ind_pi_amt+=$pi_amt;
							$tot_ind_btb_lc_amount+=$btb_lc_amount;
							$tot_ind_mrr_qnty+=$mrr_qnty;
							$tot_ind_mrr_value+=$mrr_value;
							$tot_ind_short_amt+=$short_amt;
						}
						$k++;
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
							<th>Total</th>
							<th align="right"><? echo number_format($tot_ind_wo_qnty,2);?></th>
							<th>&nbsp;</th>
							<th align="right"><? echo number_format($tot_ind_wo_amount,2);?></th>
							<th>&nbsp;</th>
							<th>&nbsp;</th>
							<th>&nbsp;</th>
							<th>&nbsp;</th>
							<th>&nbsp;</th>
							<th>&nbsp;</th>
							<th>&nbsp;</th>
							<th>&nbsp;</th>
							<th>&nbsp;</th>
							<th align="right"><? echo number_format($tot_ind_pi_qnty,2);?></th>
							<th>&nbsp;</th>
							<th align="right"><? echo number_format($tot_ind_pi_amt,2);?></th>
							<th>&nbsp;</th>
							<th>&nbsp;</th>
							<th>&nbsp;</th>
							<th>&nbsp;</th>
							<th>&nbsp;</th>
							<th>&nbsp;</th>
							<th align="right"><? echo number_format($tot_ind_btb_lc_amount,2);?></th>
							<th align="right"><? echo number_format($tot_ind_mrr_qnty,2);?></th>
							<th align="right"><? echo number_format($tot_ind_mrr_value,2);?></th>
							<th>&nbsp;</th>
	                        <th>&nbsp;</th>
						</tr>
	                </tfoot>
				</table>
				</div>
				<?
			}
			?>
	    </div>
	    <br />
	    <div style="width:2030px" >
	    <?
		if($cbo_based_on==3)
	    {
	    	$sql_independent_pi="select a.id as pi_id, a.supplier_id as pi_suplier, a.pi_number, a.pi_date, a.last_shipment_date, a.currency_id, b.work_order_id, b.work_order_dtls_id, b.color_id, b.count_name, b.yarn_composition_item1, b.yarn_composition_percentage1, b.yarn_composition_item2, b.yarn_composition_percentage2, b.yarn_type, b.uom, b.quantity, b.rate, b.amount from com_pi_master_details a, com_pi_item_details b where a.pi_basis_id=2 and a.item_category_id=1 and a.importer_id=$cbo_company_name and a.id=b.pi_id and a.status_active=1 and b.status_active=1 $pi_cond";
	    	$req_result_pi_independ=sql_select($sql_independent_pi);
		}
			//echo $sql_independent_pi;die;
		if($cbo_based_on==3)
		{
			?>
			<table width="2150"  align="left">
				<tr>
					<td style="font-size:18; font-weight:bold;">Based on Independent PI</td>
				</tr>
			</table>
			<br />
			<table width="2150" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_3"  align="left">
				<thead>
					<tr>
						<th width="30" rowspan="2">Sl</th>
						<th colspan="14">PI Details</th>
						<th colspan="6">BTB LC Details</th>
						<th colspan="4">Matarials Received Information</th>
					</tr>
					<tr>
						<th width="150">Supplier</th>
						<th width="100">PI No.</th>
						<th width="70">PI Date</th>
						<th width="70">Last Ship Date</th>
						<th width="80">Yarn Color</th>
						<th width="50">Count</th>
						<th width="230">Composition</th>
						<th width="80">Yarn Type</th>
						<th width="50">UOM</th>
						<th width="80">PI Qnty</th>
						<th width="70">PI Rate</th>
						<th width="100">PI Amount</th>
						<th width="70">Currency</th>
						<th width="70">LC Date</th>
						<th width="100">LC No</th>
						<th width="100">Issuing Bank</th>
						<th width="70">Pay Term</th>
						<th width="80">Tenor</th>
						<th width="100">LC Amount</th>
						<th width="80">MRR Qnty</th>
						<th width="100">MRR Value</th>
						<th width="100">Short Value</th>
	                    <th >Pipe Line</th>
					</tr>
				</thead>
			</table>

			


			<div style="width:2150px; overflow-y:scroll; max-height:200px;font-size:12px; overflow-x:hidden;" id="scroll_body3" align="left">
			<table width="2132" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body3" align="left">
				<tbody>
				<?
				$i=1;

				//var_dump($tem_arr);die;
				$array_check=array();
				foreach($req_result_pi_independ as $row)
				{
					if ($i%2==0)
					$bgcolor="#E9F3FF";
					else
					$bgcolor="#FFFFFF";
					$pipe_pi_qnty=$pipe_line="";
					$mrr_qnty=0;$mrr_value=0;
					$mrr_qnty=$req_wo_recv_arr[$row[csf("pi_id")]][1][$row[csf("color_id")]][$row[csf("yarn_type")]][$row[csf("count_name")]][$row[csf("yarn_composition_item1")]]['recv_qnty'];
					$mrr_value=$req_wo_recv_arr[$row[csf("pi_id")]][1][$row[csf("color_id")]][$row[csf("yarn_type")]][$row[csf("count_name")]][$row[csf("yarn_composition_item1")]]['recv_amt'];
					$pi_id_ref=$req_wo_recv_arr[$row[csf("pi_id")]][1][$row[csf("color_id")]][$row[csf("yarn_type")]][$row[csf("count_name")]][$row[csf("yarn_composition_item1")]]['booking_id'];
					$receive_basis=1;
					
					$receiving_cond=0;
					if($cbo_receive_status==1 && ($mrr_value=="" || $mrr_value==0)) $receiving_cond=1;
					if($cbo_receive_status==2 && $row[csf("amount")]>$mrr_value && $mrr_value>0) $receiving_cond=1;
					if($cbo_receive_status==3 && $mrr_value>=$row[csf("amount")]) $receiving_cond=1;
					if($cbo_receive_status==4 && $mrr_value < $row[csf("amount")]) $receiving_cond=1;
					if($cbo_receive_status==5) $receiving_cond=1;
					
					if($receiving_cond==1)
					{
						?>
	                    <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
	                        <td width="30"><? echo $i; ?></td>
	                        <td width="150"><p><? echo $supplier_arr[$row[csf("pi_suplier")]]; ?></p></td>
	                        <td width="100"><p><? echo $row[csf("pi_number")]; ?></p></td>
	                        <td width="70"><p>&nbsp;
	                        <? 
	                        if($row[csf("pi_date")]!="" && $row[csf("pi_date")]!="0000-00-00") echo change_date_format($row[csf("pi_date")]); 
	                        ?></p></td>
	                        <td width="70"><p>&nbsp;
	                        <? 
	                        if($row[csf("last_shipment_date")]!="" && $row[csf("last_shipment_date")]!="0000-00-00") echo change_date_format($row[csf("last_shipment_date")]); 
	                        ?></p></td>
	                        <td width="80"><p><? echo $color_arr[$row[csf("color_id")]]; ?></p></td>
	                        <td width="50"><p>&nbsp;<? echo $yarnCount_arr[$row[csf("count_name")]]; ?></p></td>
	                        <td width="230"><p>
	                        <? 
	                        if($row[csf("yarn_composition_item2")]>0) $comp_percent2=$row[csf("yarn_composition_percentage2")]."%"; else $comp_percent2="";
	                        echo $composition[$row[csf("yarn_composition_item1")]]." ".$row[csf("yarn_composition_percentage1")]."% ".$composition[$row[csf("yarn_composition_item2")]]; 
	                        ?></p></td>
	                        <td width="80"><p><? echo $yarn_type[$row[csf("yarn_type")]];?></p></td>
	                        <td width="50"><p><? echo $unit_of_measurement[$row[csf("uom")]];?></p></td>
	                        <td width="80" align="right"><p><? echo  number_format($row[csf("quantity")],0); ?> </p></td>
	                        <td width="70" align="right"><p><? echo  number_format($row[csf("rate")],2); ?> </p></td>
	                        <td width="100" align="right"><p><? echo number_format($row[csf("amount")],2); ?> </p></td>
	                        <td width="70"><p><? echo $currency[$row[csf("currency_id")]]; ?></p></td>
	                        <td width="70"><p>&nbsp;
	                        <? 
	                        $btb_lc_date_arr=array_unique(explode(",",chop($btb_data_arr[$row[csf("pi_id")]]["lc_date"]," , ")));
	                        $btb_lc_date='';
	                        foreach ($btb_lc_date_arr as $value) 
	                        {
	                        	if($value !="" && $value!="0000-00-00")
	                        	{
	                        		$btb_lc_date.=change_date_format($value)."</br>";
	                        	}
	                        }
	                        echo chop($btb_lc_date,"</br>");  ?> </p></td>
	                        <td width="100"><p><? $btb_lc_no_arr=array_unique(explode(",",chop($btb_data_arr[$row[csf("pi_id")]]["lc_number"]," , ")));
	                        $btb_lc_no=implode(" , ",$btb_lc_no_arr);
	                        echo $btb_lc_no;  ?></p></td>
	                        <td width="100"><p><? $btb_issue_bank_arr=array_unique(explode(",",chop($btb_data_arr[$row[csf("pi_id")]]["issuing_bank_id"]," , ")));
	                        foreach ($btb_issue_bank_arr as $value) 
	                        {
	                        	$btb_issue_bank.=$bank_arr[$value].",";
	                        }
	                        echo chop($btb_issue_bank,","); ?></p></td>
	                        <td width="70"><p><? $btb_pay_tarm_arr=array_unique(explode(",",chop($btb_data_arr[$row[csf("pi_id")]]["payterm_id"]," , ")));
	                        $btb_pay_term=''; 
	                        foreach ($btb_pay_tarm_arr as $value) 
	                        {
	                        	$btb_pay_term.=$pay_term[$value].",";
	                        }
	                        echo chop($btb_pay_term,","); ?></p></td>
	                        <td width="80"><p><? $btb_tenor_arr=array_unique(explode(",",chop($btb_data_arr[$row[csf("pi_id")]]["tenor"]," , "))); 
	                        $btb_tenor=implode(" Days, ",$btb_tenor_arr);
	                        if ($btb_tenor!='') echo $btb_tenor." Days"; ?></p></td>
	                        <td width="100" align="right"><p><? $btb_lc_amount=$btb_data_arr[$row[csf("pi_id")]]["lc_value"]; echo number_format($btb_lc_amount,2); ?> </p></td>
	                        <td width="80" align="right"><p><a href="##" onclick="fn_mrr_details('<? echo $pi_id_ref;?>','<? echo $receive_basis;?>','<? echo $row[csf("color_id")];?>','<? echo $row[csf("yarn_type")];?>','<? echo $row[csf("count_name")];?>','<? echo $row[csf("yarn_composition_item1")];?>','receive_details_popup')"><? echo number_format($mrr_qnty,2); ?></a></p></td>
	                        <td width="100" align="right"><p><? echo number_format($mrr_value,2);?></p></td>
	                        <td align="right"><p><? $short_amt=$row[csf("amount")]-$mrr_value; echo number_format($short_amt,2); ?></p></td>
	                        <td align="right" title="<?= $pi_pipe_array[$row[csf("pi_id")]][$row[csf("color_id")]][$row[csf("yarn_type")]][$row[csf("count_name")]][$row[csf("yarn_composition_item1")]];?>"><p>
	                        <?
	                        //$pipe_pi_qnty=$pipe_line 
	                        $pipe_pi_qnty=$pi_pipe_array[$row[csf("pi_id")]][$row[csf("color_id")]][$row[csf("yarn_type")]][$row[csf("count_name")]][$row[csf("yarn_composition_item1")]];
	                        $pipe_line=($pipe_pi_qnty-$mrr_qnty);
	                        echo number_format($pipe_line,2); 
	                        ?>
	                        </p></td>
	                    </tr>
	                    <?
	                    $k++;
	                    $i++;
	                    $total_pi_qntty+=$row[csf("quantity")];
	                    $total_pi_amount+=$row[csf("amount")];
	                    $total_mrr_qty+=$mrr_qnty;
	                    $total_mrr_val+=$mrr_value;
					}
				}
				?>
				</tbody>
				<tfoot style="background: #dbdbdb;">
					<td colspan="10" align="right"><strong>Total</strong></td>
					<td align="right"><b><? echo number_format($total_pi_qntty,2);?></b></td>
					<td>&nbsp;</td>
					<td align="right"><b><? echo number_format($total_pi_amount,2);?></b></td>
					<td colspan="8" align="right"><b><? echo number_format($total_mrr_qty,2);?></b></td>
					<td align="right"><b><? echo number_format($total_mrr_val,2);?></b></td>
					<td colspan="2">&nbsp;</td>
				</tfoot>
			</table>
			</div>
			<?
		}
	    ?>
		</div>
	    <?
	}
		
	
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
	echo "$total_data####$filename####$cbo_based_on";
	exit();
}

if($action=="report_generate_yarn_analysis")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$buyer_arr=return_library_array("select id,buyer_name from  lib_buyer","id","buyer_name");
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$yarnCount_arr=return_library_array( "select id,yarn_count from  lib_yarn_count where is_deleted=0 and status_active=1",'id','yarn_count');
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$bank_arr=return_library_array( "select id, bank_name from lib_bank",'id','bank_name');
	//("cbo_company_name*cbo_year*cbo_based_on*txt_search_no*txt_date_from*txt_date_to","../../")
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_year=str_replace("'","",$cbo_year);
	$type=str_replace("'","",$type);
	$cbo_based_on=str_replace("'","",$cbo_based_on);
	$txt_search_no=str_replace("'","",$txt_search_no);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$cbo_receive_status=str_replace("'","",$cbo_receive_status);
	
	$str_cond=$str_cond_independ="";
	
	//echo $cbo_based_on ; die;
	// req condition check here
	if($db_type==0)
	{
		$txt_date_from=change_date_format($txt_date_from,'yyyy-mm-dd');
		$txt_date_to=change_date_format($txt_date_to,'yyyy-mm-dd');
	}
	else if($db_type==2)
	{
		$txt_date_from=change_date_format($txt_date_from,'','',-1);
		$txt_date_to=change_date_format($txt_date_to,'','',-1);
	}

	
	if($type==1)  // Yarn Analysis
	{
		$str_cond=$str_cond_independ=$pi_cond=$btb_cond="";
		if($cbo_based_on==1)
		{
			$str_cond.=" and a.requ_prefix_num like '%$txt_search_no%'";
			if($txt_date_from!="" && $txt_date_to!="") $str_cond.=" and a.requisition_date between '$txt_date_from' and '$txt_date_to'";
		}
		else if($cbo_based_on==2)
		{
			$str_cond.=" and d.wo_number_prefix_num like '%$txt_search_no%'";
			if($txt_date_from!="" && $txt_date_to!="")  $str_cond.=" and d.wo_date between '$txt_date_from' and '$txt_date_to'";
			$str_cond_independ.=" and d.wo_number_prefix_num like '%$txt_search_no%'";
			if($txt_date_from!="" && $txt_date_to!="") $str_cond_independ.=" and d.wo_date between '$txt_date_from' and '$txt_date_to'";
		}
		else if($cbo_based_on==3)
		{
			if ($txt_search_no != "")
			{
				$pi_cond.=" and a.pi_number like '%$txt_search_no%'";
			}
			if($txt_date_from!="" && $txt_date_to!="") $pi_cond.=" and a.pi_date between '$txt_date_from' and '$txt_date_to'";
		}
		
		
		// $sql_pi=sql_select("SELECT a.id as pi_id, a.supplier_id as pi_suplier, a.pi_number, a.pi_date, a.last_shipment_date, a.currency_id, b.work_order_id, b.work_order_dtls_id, b.color_id, b.count_name, b.yarn_composition_item1, b.yarn_composition_percentage1, b.yarn_composition_item2, b.yarn_composition_percentage2, b.yarn_type, b.uom, b.quantity, b.rate, b.amount from com_pi_master_details a, com_pi_item_details b 
		// where a.item_category_id=1 and a.id=b.pi_id and a.importer_id=$cbo_company_name and a.status_active=1 and b.status_active=1 and b.work_order_id is not null $pi_cond"); //and a.goods_rcv_status<>1
		
	
		$sql_pi=sql_select("SELECT a.id as pi_id, a.supplier_id as pi_suplier, a.pi_number, a.pi_date, a.last_shipment_date, a.currency_id, b.work_order_id, b.work_order_dtls_id, b.color_id, b.count_name, b.yarn_composition_item1, b.yarn_composition_percentage1, b.yarn_composition_item2, b.yarn_composition_percentage2, b.yarn_type, b.uom, b.quantity, b.rate, b.amount from com_pi_master_details a, com_pi_item_details b 
		where a.item_category_id=1 and a.id=b.pi_id and a.importer_id=$cbo_company_name and a.status_active=1 and b.status_active=1 and b.work_order_id is not null $pi_cond");
		
		$pi_data_arr=array();
		foreach($sql_pi as $row)
		{
			if($row[csf("work_order_dtls_id")] > 0 )
			{
				if($row[csf("work_order_dtls_id")]) $pi_wo_dtls_id_all[]=$row[csf("work_order_dtls_id")];
				$pi_id_arr[]=$row[csf("pi_id")];
				$pi_data_arr[$row[csf("work_order_dtls_id")]]["work_order_dtls_id"]=$row[csf("work_order_dtls_id")];
				$pi_data_arr[$row[csf("work_order_dtls_id")]]["pi_id"]=$row[csf("pi_id")];
				$pi_data_arr[$row[csf("work_order_dtls_id")]]["pi_id_all"].=$row[csf("pi_id")].",";
				$pi_data_arr[$row[csf("work_order_dtls_id")]]["pi_suplier"].=$row[csf("pi_suplier")].",";
				$pi_data_arr[$row[csf("work_order_dtls_id")]]["pi_number"].=$row[csf("pi_number")].",";
				$pi_data_arr[$row[csf("work_order_dtls_id")]]["pi_date"].=$row[csf("pi_date")].",";
				$pi_data_arr[$row[csf("work_order_dtls_id")]]["last_shipment_date"].=$row[csf("last_shipment_date")].",";
				$pi_data_arr[$row[csf("work_order_dtls_id")]]["currency_id"].=$row[csf("currency_id")].",";
				$pi_data_arr[$row[csf("work_order_dtls_id")]]["work_order_id"]=$row[csf("work_order_id")];
				$pi_data_arr[$row[csf("work_order_dtls_id")]]["color_id"]=$row[csf("color_id")];
				$pi_data_arr[$row[csf("work_order_dtls_id")]]["count_name"]=$row[csf("count_name")];
				$pi_data_arr[$row[csf("work_order_dtls_id")]]["yarn_composition_item1"]=$row[csf("yarn_composition_item1")];
				$pi_data_arr[$row[csf("work_order_dtls_id")]]["yarn_composition_percentage1"]=$row[csf("yarn_composition_percentage1")];
				$pi_data_arr[$row[csf("work_order_dtls_id")]]["yarn_composition_item2"]=$row[csf("yarn_composition_item2")];
				$pi_data_arr[$row[csf("work_order_dtls_id")]]["yarn_comp_percent2nd"]=$row[csf("yarn_comp_percent2nd")];
				$pi_data_arr[$row[csf("work_order_dtls_id")]]["yarn_type"]=$row[csf("yarn_type")];
				$pi_data_arr[$row[csf("work_order_dtls_id")]]["uom"]=$row[csf("uom")];
				$pi_data_arr[$row[csf("work_order_dtls_id")]]["quantity"]+=$row[csf("quantity")];
				$pi_data_arr[$row[csf("work_order_dtls_id")]]["rate"]=$row[csf("rate")];
				$pi_data_arr[$row[csf("work_order_dtls_id")]]["amount"]+=$row[csf("amount")];
			}
			
		}
		
		if($cbo_based_on==3)
		{
			$pi_id_arr_all=array_chunk(array_unique($pi_id_arr),999);
		
			$sql_btb="select a.id as btb_id, a.lc_number, a.lc_date, a.issuing_bank_id, a.payterm_id, a.tenor, a.lc_value, b.pi_id from com_btb_lc_master_details a, com_btb_lc_pi b where a.id=b.com_btb_lc_master_details_id and a.pi_entry_form=165 and a.status_active=1 and b.status_active=1";
			
			$p=1;
			if(!empty($pi_id_arr_all))
			{
				foreach($pi_id_arr_all as $pi_id)
				{
					if($p==1) $sql_btb .=" and (b.pi_id in(".implode(',',$pi_id).")"; else $sql_btb .=" or b.pi_id in(".implode(',',$pi_id).")";
					$p++;
				}
				$sql_btb .=" ) ";
			}
			
			//echo $sql_btb;die;
		}
		else
		{
		
			$sql_btb="select a.id as btb_id, a.lc_number, a.lc_date, a.issuing_bank_id, a.payterm_id, a.tenor, a.lc_value, b.pi_id from com_btb_lc_master_details a, com_btb_lc_pi b where a.id=b.com_btb_lc_master_details_id and a.pi_entry_form=165 and a.status_active=1 and b.status_active=1";
		}
		//echo $sql_btb;die;
			
		$sql_btb_result=sql_select($sql_btb);
		$btb_data_arr=array();
		foreach($sql_btb_result as $row)
		{
			$btb_data_arr[$row[csf("pi_id")]]["pi_id"]=$row[csf("pi_id")];
			$btb_data_arr[$row[csf("pi_id")]]["btb_id"]=$row[csf("btb_id")];
			$btb_data_arr[$row[csf("pi_id")]]["btb_id_all"].=$row[csf("btb_id")].",";
			$btb_data_arr[$row[csf("pi_id")]]["lc_number"].=$row[csf("lc_number")].",";
			$btb_data_arr[$row[csf("pi_id")]]["lc_date"].=$row[csf("lc_date")].",";
			$btb_data_arr[$row[csf("pi_id")]]["issuing_bank_id"].=$row[csf("issuing_bank_id")].",";
			$btb_data_arr[$row[csf("pi_id")]]["payterm_id"].=$row[csf("payterm_id")].",";
			$btb_data_arr[$row[csf("pi_id")]]["tenor"].=$row[csf("tenor")].",";
			$btb_data_arr[$row[csf("pi_id")]]["lc_value"]+=$row[csf("lc_value")];
		}
		
		$rcv_return_sql=sql_select("select b.prod_id, a.received_id, b.cons_quantity, b.cons_quantity, b.cons_amount from inv_issue_master a, inv_transaction b where a.id=b.mst_id and a.entry_form=8 and b.transaction_type=3 and b.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		$rcv_rtn_data=array();
		foreach($rcv_return_sql as $row)
		{
			$rcv_rtn_data[$row[csf("received_id")]][$row[csf("prod_id")]]["cons_quantity"]+=$row[csf("cons_quantity")];
			$rcv_rtn_data[$row[csf("received_id")]][$row[csf("prod_id")]]["cons_amount"]+=$row[csf("cons_amount")];
		}
		
		$req_wo_recv_sql=sql_select("select a.receive_basis, a.booking_id, c.color, c.yarn_type, c.yarn_count_id, c.yarn_comp_type1st, b.order_qnty as recv_qnty, b.order_amount as recv_amt, b.mst_id, b.prod_id, b.transaction_date, a.exchange_rate,b.pi_wo_req_dtls_id 
		from  inv_receive_master a, inv_transaction b, product_details_master c 
		where a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=1 and a.item_category=1 and a.booking_id>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
		order by a.booking_id, a.receive_basis, c.color, c.yarn_type, c.yarn_count_id, c.yarn_comp_type1st, b.transaction_date");
		//47889
		$min_date=$max_date=""; $prod_id_arr = array();
		$b=0;
		foreach($req_wo_recv_sql as $row)
		{
			if($item_check[$row[csf("pi_wo_req_dtls_id")]][$row[csf("receive_basis")]][$row[csf("color")]][$row[csf("yarn_type")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]]=="")
			{
				$item_check[$row[csf("pi_wo_req_dtls_id")]][$row[csf("receive_basis")]][$row[csf("color")]][$row[csf("yarn_type")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]]=$row[csf("yarn_comp_type1st")];
				$min_date=$row[csf("transaction_date")];
				$max_date=$row[csf("transaction_date")];
				$b++;
			}
			else
			{
				if(strtotime($row[csf("transaction_date")])>strtotime($max_date)) $max_date=$row[csf("transaction_date")];
			}
			// if($row[csf("pi_wo_req_dtls_id")] == "")
			// {
			// 	$req_wo_recv_arr[$row[csf("booking_id")]][$row[csf("receive_basis")]][$row[csf("color")]][$row[csf("yarn_type")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]]['booking_id']=$row[csf("booking_id")];
			// 	$req_wo_recv_arr[$row[csf("booking_id")]][$row[csf("receive_basis")]][$row[csf("color")]][$row[csf("yarn_type")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]]['min_date']=$min_date;
			// 	$req_wo_recv_arr[$row[csf("booking_id")]][$row[csf("receive_basis")]][$row[csf("color")]][$row[csf("yarn_type")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]]['max_date']=$max_date;
				
			// 	$req_wo_recv_arr[$row[csf("booking_id")]][$row[csf("receive_basis")]][$row[csf("color")]][$row[csf("yarn_type")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]]['receive_basis']=$row[csf("receive_basis")];
			// 	$req_wo_recv_arr[$row[csf("booking_id")]][$row[csf("receive_basis")]][$row[csf("color")]][$row[csf("yarn_type")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]]['recv_qnty']+=$row[csf("recv_qnty")]-$rcv_rtn_data[$row[csf("mst_id")]][$row[csf("prod_id")]]["cons_quantity"];
			// 	$req_wo_recv_arr[$row[csf("booking_id")]][$row[csf("receive_basis")]][$row[csf("color")]][$row[csf("yarn_type")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]]['recv_amt']+=$row[csf("recv_amt")]-($rcv_rtn_data[$row[csf("mst_id")]][$row[csf("prod_id")]]["cons_amount"]/$row[csf("exchange_rate")]);
			// 	$req_wo_recv_arr[$row[csf("booking_id")]][$row[csf("receive_basis")]][$row[csf("color")]][$row[csf("yarn_type")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]]['prod_id']=$row[csf("prod_id")];
			// 	$prod_id_arr[$row[csf("prod_id")]] = $row[csf("prod_id")];
			// }
			// else
			// {
				// $req_wo_recv_arr[$row[csf("pi_wo_req_dtls_id")]][$row[csf("receive_basis")]][$row[csf("color")]][$row[csf("yarn_type")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]]['booking_id']=$row[csf("pi_wo_req_dtls_id")];
				// $req_wo_recv_arr[$row[csf("pi_wo_req_dtls_id")]][$row[csf("receive_basis")]][$row[csf("color")]][$row[csf("yarn_type")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]]['min_date']=$min_date;
				// $req_wo_recv_arr[$row[csf("pi_wo_req_dtls_id")]][$row[csf("receive_basis")]][$row[csf("color")]][$row[csf("yarn_type")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]]['max_date']=$max_date;
				
				// $req_wo_recv_arr[$row[csf("pi_wo_req_dtls_id")]][$row[csf("receive_basis")]][$row[csf("color")]][$row[csf("yarn_type")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]]['receive_basis']=$row[csf("receive_basis")];
				// $req_wo_recv_arr[$row[csf("pi_wo_req_dtls_id")]][$row[csf("receive_basis")]][$row[csf("color")]][$row[csf("yarn_type")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]]['recv_qnty']+=$row[csf("recv_qnty")]-$rcv_rtn_data[$row[csf("mst_id")]][$row[csf("prod_id")]]["cons_quantity"];
				// $req_wo_recv_arr[$row[csf("pi_wo_req_dtls_id")]][$row[csf("receive_basis")]][$row[csf("color")]][$row[csf("yarn_type")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]]['recv_amt']+=$row[csf("recv_amt")]-($rcv_rtn_data[$row[csf("mst_id")]][$row[csf("prod_id")]]["cons_amount"]/$row[csf("exchange_rate")]);
				// $req_wo_recv_arr[$row[csf("pi_wo_req_dtls_id")]][$row[csf("receive_basis")]][$row[csf("color")]][$row[csf("yarn_type")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]]['prod_id']=$row[csf("prod_id")];
				// $prod_id_arr[$row[csf("prod_id")]] = $row[csf("prod_id")];
			//}

			if($row[csf("pi_wo_req_dtls_id")] > 0)
			{
				$req_wo_recv_arr[$row[csf("pi_wo_req_dtls_id")]][$row[csf("receive_basis")]][$row[csf("color")]][$row[csf("yarn_type")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]]['booking_id']=$row[csf("pi_wo_req_dtls_id")];
				$req_wo_recv_arr[$row[csf("pi_wo_req_dtls_id")]][$row[csf("receive_basis")]][$row[csf("color")]][$row[csf("yarn_type")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]]['min_date']=$min_date;
				$req_wo_recv_arr[$row[csf("pi_wo_req_dtls_id")]][$row[csf("receive_basis")]][$row[csf("color")]][$row[csf("yarn_type")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]]['max_date']=$max_date;
				
				$req_wo_recv_arr[$row[csf("pi_wo_req_dtls_id")]][$row[csf("receive_basis")]][$row[csf("color")]][$row[csf("yarn_type")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]]['receive_basis']=$row[csf("receive_basis")];
				$req_wo_recv_arr[$row[csf("pi_wo_req_dtls_id")]][$row[csf("receive_basis")]][$row[csf("color")]][$row[csf("yarn_type")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]]['recv_qnty']+=$row[csf("recv_qnty")]-$rcv_rtn_data[$row[csf("mst_id")]][$row[csf("prod_id")]]["cons_quantity"];
				$req_wo_recv_arr[$row[csf("pi_wo_req_dtls_id")]][$row[csf("receive_basis")]][$row[csf("color")]][$row[csf("yarn_type")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]]['recv_amt']+=$row[csf("recv_amt")]-($rcv_rtn_data[$row[csf("mst_id")]][$row[csf("prod_id")]]["cons_amount"]/$row[csf("exchange_rate")]);
				$req_wo_recv_arr[$row[csf("pi_wo_req_dtls_id")]][$row[csf("receive_basis")]][$row[csf("color")]][$row[csf("yarn_type")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]]['prod_id']=$row[csf("prod_id")];
				$prod_id_arr[$row[csf("prod_id")]] = $row[csf("prod_id")];
			}
			
			
		}
		//echo $b."<pre>";print_r($req_wo_recv_arr);die;	

		$prod_id_arr = array_filter($prod_id_arr);
		$prod_ids= implode(",",$prod_id_arr);
		$all_prod_ids_cond=""; $prod_idCond="";
		if($db_type==2 && count($prod_id_arr)>999)
		{
			$batch_id_arr_chunk=array_chunk($prod_id_arr,999) ;
			foreach($batch_id_arr_chunk as $chunk_arr)
			{
				$prod_ids_value=implode(",",$chunk_arr);
				$prod_idCond.="  b.prod_id in($prod_ids_value) or ";
			}
			$all_prod_ids_cond.=" and (".chop($prod_idCond,'or ').")";
		}
		else
		{
			$all_prod_ids_cond=" and b.prod_id in($prod_ids)";
		}

		
		$issue_return_sql=sql_select("SELECT b.prod_id, a.issue_id, b.cons_quantity, b.cons_amount from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.entry_form=9 and b.transaction_type=4 and b.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $all_prod_ids_cond");
		//a.recv_number='FAL-YIR-21-00100' and 
		$issue_rtn_data=array();
		foreach($issue_return_sql as $row)
		{
			$issue_rtn_data[$row[csf("issue_id")]][$row[csf("prod_id")]]["cons_quantity"]+=$row[csf("cons_quantity")];
		}
		//echo $c."<pre>";print_r($issue_rtn_data);die;
	
		

	
		$req_wo_issue_sql=sql_select("SELECT a.id, a.issue_basis, b.requisition_no, c.color, c.yarn_type, c.yarn_count_id, c.yarn_comp_type1st, b.cons_quantity as issue_qnty, b.cons_amount as issue_amt, b.mst_id, b.prod_id, b.transaction_date, d.booking_no
		from  inv_issue_master a, inv_transaction b, product_details_master c, ppl_yarn_requisition_breakdown d 
		where  a.id=b.mst_id and b.prod_id=c.id and b.prod_id=d.item_id and b.transaction_type=2 and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=3 $all_prod_ids_cond
		order by b.requisition_no, a.issue_basis, c.color, c.yarn_type, c.yarn_count_id, c.yarn_comp_type1st, b.transaction_date");
		//a.issue_number='FAL-YIS-21-00794' and b.prod_id=47889 and
		$issue_min_date=$issue_max_date="";
		$req_wo_issue_arr= array();
		$c=0;
		foreach($req_wo_issue_sql as $row)
		{
			if($item_check[$row[csf("prod_id")]][$row[csf("issue_basis")]][$row[csf("color")]][$row[csf("yarn_type")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]]=="")
			{
				$item_check[$row[csf("prod_id")]][$row[csf("issue_basis")]][$row[csf("color")]][$row[csf("yarn_type")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]]=$row[csf("yarn_comp_type1st")];
				$issue_min_date=$row[csf("transaction_date")];
				$issue_max_date=$row[csf("transaction_date")];
				$c++;
			}
			else
			{
				if(strtotime($row[csf("transaction_date")])>strtotime($issue_max_date)) $issue_max_date=$row[csf("transaction_date")];
			}
			$req_wo_issue_arr[$row[csf("booking_no")]][$row[csf("prod_id")]][$row[csf("issue_basis")]][$row[csf("color")]][$row[csf("yarn_type")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]]['prod_id']=$row[csf("prod_id")];
			$req_wo_issue_arr[$row[csf("booking_no")]][$row[csf("prod_id")]][$row[csf("issue_basis")]][$row[csf("color")]][$row[csf("yarn_type")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]]['min_date']=$issue_min_date;
			$req_wo_issue_arr[$row[csf("booking_no")]][$row[csf("prod_id")]][$row[csf("issue_basis")]][$row[csf("color")]][$row[csf("yarn_type")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]]['max_date']=$issue_max_date;
			$req_wo_issue_arr[$row[csf("booking_no")]][$row[csf("prod_id")]][$row[csf("issue_basis")]][$row[csf("color")]][$row[csf("yarn_type")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]]['issue_basis']=$row[csf("issue_basis")];
			$req_wo_issue_arr[$row[csf("booking_no")]][$row[csf("prod_id")]][$row[csf("issue_basis")]][$row[csf("color")]][$row[csf("yarn_type")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]]['issue_qnty']+=$row[csf("issue_qnty")]
			-$issue_rtn_data[$row[csf("id")]][$row[csf("prod_id")]]["cons_quantity"];
			
		}
		
		//echo $c."<pre>";print_r($req_wo_issue_arr);die;
		//var_dump($req_wo_issue_arr);die;


		$wo_qty_arr=sql_select("select a.id as wo_id, b.color_name as color, b.yarn_type as yarn_type, b.yarn_count as yarn_count_id, b.yarn_comp_type1st as yarn_comp_type1st, sum(b.supplier_order_quantity) as qty from wo_non_order_info_mst a, wo_non_order_info_dtls b where a.id=b.mst_id and a.company_name=$cbo_company_name and b.item_category_id in (1) and a.pay_mode<>2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
		group by a.id,b.color_name,b.yarn_type,b.yarn_count,b.yarn_comp_type1st");
		$wo_pipe_array=array();
		foreach($wo_qty_arr as $row)
		{
			$wo_pipe_array[$row[csf("wo_id")]][$row[csf("color")]][$row[csf("yarn_type")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]]=$row[csf("qty")];
		}
		//echo '<pre>';print_r($wo_pipe_array);

		$pi_qty_arr=sql_select("select a.id as pi_id, b.color_id as color, b.yarn_type as yarn_type, b.count_name as yarn_count_id, b.yarn_composition_item1 as yarn_comp_type1st, sum(b.quantity) as qty from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.importer_id=$cbo_company_name and a.item_category_id in (1) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, b.color_id, b.yarn_type, b.count_name, b.yarn_composition_item1"); 
		$pi_pipe_array=array();
		foreach($pi_qty_arr as $row)
		{
			$pi_pipe_array[$row[csf("pi_id")]][$row[csf("color")]][$row[csf("yarn_type")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]]=$row[csf("qty")];
		}		
		//echo $cbo_based_on;
		//echo "<pre>";print_r($pi_pipe_array[5710]); die;
		
		if($cbo_based_on==3)
		{
			//print_r($pi_wo_dtls_id_all);die;
			if($cbo_year>0)
			{
				if($db_type==0) $year_cond=" and year(a.insert_date)='$cbo_year'"; else $year_cond=" and to_char(a.insert_date,'YYYY')='$cbo_year'";
			}
			if(!empty($pi_wo_dtls_id_all))
			{
				$pi_wo_dtls_id_all_arr=array_chunk(array_unique($pi_wo_dtls_id_all),999);
				$sql_req_wo="SELECT a.id, a.requ_no, a.requ_prefix_num, a.requisition_date, a.company_id, b.buyer_id, b.id as req_dtls_id, b.color_id, b.count_id, b.composition_id, b.com_percent, b.yarn_type_id, b.cons_uom as req_uom, b.quantity as req_qnty, b.rate as req_rate, b.amount as req_amt, b.yarn_inhouse_date, b.booking_no, b.job_no, d.id as wo_id, d.wo_number_prefix_num, d.wo_date, e.id as wo_dtls_id, e.color_name as wo_color, e.yarn_count as wo_count, e.yarn_comp_type1st as wo_yarn_comp_type1st, e.yarn_comp_percent1st as wo_yarn_comp_percent1st, e.yarn_comp_type2nd, e.yarn_comp_percent2nd,  e.yarn_type as wo_yarn_type, e.uom as wo_uom, e.supplier_order_quantity as wo_qnty, e.rate as wo_rate, e.amount as wo_amount
				from inv_purchase_requisition_mst a,  inv_purchase_requisition_dtls b 
				left join  wo_non_order_info_dtls e  on  b.id=e.requisition_dtls_id and e.status_active=1 and e.is_deleted=0 
				left join wo_non_order_info_mst d on d.id=e.mst_id  and d.status_active=1 and d.is_deleted=0
				where a.id=b.mst_id and a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=70 and a.company_id='$cbo_company_name' $str_cond $year_cond ";
				//echo $sql_req_wo;die;
				if(!empty($pi_wo_dtls_id_all_arr))
				{
					$p=1;
					foreach($pi_wo_dtls_id_all_arr as $pi_wo_dtls_id)
					{
						if($p==1) $sql_req_wo .=" and (e.id in(".implode(',',$pi_wo_dtls_id).")"; else $sql_req_wo .=" or e.id in(".implode(',',$pi_wo_dtls_id).")";
						$p++;
					}
					$sql_req_wo .=" ) ";
				}
				$sql_req_wo .=" order by  a.id desc, b.color_id, b.yarn_type_id, b.count_id, b.composition_id ";	
			}
			
		}
		else
		{
			if($cbo_year>0)
			{
				if($db_type==0) $year_cond=" and year(a.insert_date)='$cbo_year'"; else $year_cond=" and to_char(a.insert_date,'YYYY')='$cbo_year'";
			}
			$sql_req_wo="SELECT a.id, a.requ_no, a.is_approved, a.basis, a.requ_prefix_num, a.requisition_date, a.delivery_date, a.company_id, b.buyer_id, b.id as req_dtls_id, b.color_id, b.count_id, b.composition_id, b.com_percent, b.yarn_type_id, b.cons_uom as req_uom, b.quantity as req_qnty, b.rate as req_rate, b.amount as req_amt, b.yarn_inhouse_date, b.booking_no, b.job_no, d.id as wo_id, d.wo_number_prefix_num, d.wo_date, d.is_approved as wo_approved, d.ref_closing_status, d.pay_mode, e.id as wo_dtls_id, e.color_name as wo_color, e.yarn_count as wo_count, e.yarn_comp_type1st as wo_yarn_comp_type1st, e.yarn_comp_percent1st as wo_yarn_comp_percent1st, e.yarn_comp_type2nd, e.yarn_comp_percent2nd, e.yarn_type as wo_yarn_type, e.uom as wo_uom, e.supplier_order_quantity as wo_qnty, e.rate as wo_rate, e.amount as wo_amount
			from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b 
			left join  wo_non_order_info_dtls e  on  b.id=e.requisition_dtls_id and e.status_active=1 and e.is_deleted=0 
			left join wo_non_order_info_mst d on d.id=e.mst_id  and d.status_active=1 and d.is_deleted=0
			where a.id=b.mst_id and b.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id='$cbo_company_name' and a.entry_form=70 $str_cond $year_cond 
			order by a.id desc, b.color_id, b.count_id, b.yarn_type_id, b.composition_id";
		}
		//echo $sql_req_wo;//die;
		   
		$req_result=sql_select($sql_req_wo);
		foreach ($req_result as $val) {
			if ($val[csf("job_no")] != ''){
				$job_no.="'".$val[csf("job_no")]."'".',';
			}
		}

		$approval_arr=array();
		$sql_appr=sql_select("select mst_id as requ_id, approved_date, approved_by from approval_history where entry_form=20 and current_approval_status=1");
		foreach ($sql_appr as $val) {
			$approval_arr[$val[csf("requ_id")]]=$val[csf("approved_date")];
		}

		$job_nos = implode(',',array_flip(array_flip(explode(',', rtrim($job_no,',')))));
		$job_order_arr=array();
		
		if ($job_nos != ''){
			
			$sql_order="select a.style_ref_no, a.job_no, a.job_quantity, b.po_number, b.po_received_date, b.pub_shipment_date, c.booking_no, c.booking_date from wo_po_details_master a, wo_po_break_down b, wo_booking_mst c
			where a.job_no=b.job_no_mst and a.job_no=c.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.job_no in($job_nos) and a.company_name='$cbo_company_name'";
			//echo $sql_order;
			$sql_order_res=sql_select($sql_order);
			foreach ($sql_order_res as $val) {
				$job_order_arr[$val[csf("job_no")]]['style_ref_no']=$val[csf("style_ref_no")];
				$job_order_arr[$val[csf("job_no")]]['job_quantity']=$val[csf("job_quantity")];
				$job_order_arr[$val[csf("job_no")]]['po_number'].=$val[csf("po_number")].',';
				$job_order_arr[$val[csf("job_no")]]['po_received_date'].=change_date_format($val[csf("po_received_date")]).',';
				$job_order_arr[$val[csf("job_no")]]['pub_shipment_date'].=change_date_format($val[csf("pub_shipment_date")]).',';
				$job_order_arr[$val[csf("job_no")]]['booking_no'].=$val[csf("booking_no")].',';
				$job_order_arr[$val[csf("job_no")]]['booking_date'].=change_date_format($val[csf("booking_date")]).',';
			}
		}
		//echo '<pre>';print_r($job_order_arr1);die;
		$po_number=return_library_array( "select id,po_number from wo_po_break_down", "id", "po_number");
		$po_array=array();
		$job_prefix_num=array();
	
		$sql_po= sql_select("select a.booking_no, a.po_break_down_id, a.job_no from wo_booking_mst a where a.company_id='$cbo_company_name' and a.booking_type=1 and a.is_short=2 and   a.status_active=1 and a.is_deleted=0 order by a.booking_no");
		foreach($sql_po as $row){
			$po_id=explode(",",$row[csf("po_break_down_id")]);
			$po_number_string="";
			foreach($po_id as $key=> $value ){
				$po_number_string.=$po_number[$value].",";
			}
			$po_array[$row[csf("po_break_down_id")]]=rtrim($po_number_string,",");
		}
		//var_dump($po_array);die;

		$sql= "select a.id, a.booking_no, a.po_break_down_id from wo_booking_mst a, wo_po_details_master b, wo_po_break_down c, wo_po_details_mas_set_details d where b.job_no in($job_nos)  and b.company_name='$cbo_company_name' ". set_user_lavel_filtering(' and a.buyer_id','buyer_id')." and a.job_no=b.job_no and a.job_no=c.job_no_mst and b.job_no=c.job_no_mst and b.job_no=d.job_no and a.booking_type=1 and a.is_short=2 and a.status_active=1 and a.is_deleted=0 and a.entry_form=118 group by a.id, a.booking_no, a.po_break_down_id order by a.id DESC";
		//echo $sql;
		$sql_order_pro=sql_select($sql);
		$used_order_arr=array();
		//var_dump($sql_order_pro);
			foreach ($sql_order_pro as $val) {

				$used_order_arr[$val[csf("booking_no")]]['po_number'].= $po_array[$val[csf("po_break_down_id")]].',';
			}
			//var_dump($used_order_arr);

		$print_report_format=sql_select("select format_id, report_id from lib_report_template where template_name=".$cbo_company_name." and module_id=5 and report_id in(69,45) and status_active=1 and is_deleted=0");
		foreach ($print_report_format as $val) {
			if ($val[csf("report_id")]==69) $print_report_format_yarnPurchaseRequ=explode(",",$val[csf("format_id")]);
			else if ($val[csf("report_id")]==45) $print_report_format_yarnPurchaseOrder=explode(",",$val[csf("format_id")]);			
			//else if ($val[csf("report_id")]==129) $print_report_format_issRtn=explode(",",$val[csf("format_id")]);			
		}
		//echo '<pre>';print_r($print_report_format_yarnPurchaseRequ);


		ob_start();
		?>
	    <div style="width:5100px">
	        <table width="5100" cellpadding="0" cellspacing="0" id="caption"  align="left">
	        <tr>
	            <td align="center" width="100%"  class="form_caption" colspan="57"><strong style="font-size:18px">Company Name:<? echo " ". $company_arr[str_replace("'","",$cbo_company_name)]; ?></strong></td>
	        </tr> 
	        <tr>  
	            <td align="center" width="100%" class="form_caption"  colspan="57"><strong style="font-size:18px"><? echo $report_title; ?></strong></td>
	        </tr>
	        <tr>  
	            <td align="center" width="100%"  class="form_caption"  colspan="57"><strong style="font-size:18px">From : <? echo $txt_date_from; ?> To : <? echo $txt_date_to; ?></strong></td>
	        </tr>
	        </table>
	    	<br />
	        <table width="5100"  align="left">
	        	<tr>
	            	<td style="font-size:18; font-weight:bold;">Based on Requisition</td>
	            </tr>
	        </table>
	        <br />       
	            <table width="5100" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_1"  align="left">
	            <thead>
	            	<tr>
	                	<th width="30" rowspan="2">Sl </th>
	                	<th colspan="22">Requisiton Details</th>
	                    <th colspan="9">Work Order Details</th>
	                    <th colspan="13">PI Details</th>
	                    <th colspan="6">BTB LC Details</th>
	                    <th colspan="6">Matarials Received Information</th>
	                </tr>
	                <tr>
	                    <th width="50">Req. No</th>
	                    <th width="70">Req. Date</th>
	                    <th width="80">Req Appr Date</th>
	                    <th width="100">Buyer</th>
	                    <th width="80">PO Recv Date</th>
	                    <th width="150">Order</th>
	                    <th width="100">Style</th>
	                    <th width="100">Job</th>
	                    <th width="150">Booking Number</th>
	                    <th width="130">Merchandising Booking Date</th>
	                    <th width="80">Shipment Date</th>
	                    <th width="80">Requested Delivery Date</th>
	                    <th width="80">Job Garments Qty</th>
	                    <th width="80">Yarn Color</th>
	                    <th width="50">Count</th>
	                    <th width="150">Composition</th>
	                    <th width="80">Yarn Type</th>
	                    <th width="50">UOM</th>
	                    <th width="80">Req. Qnty.</th>
	                    <th width="70">Req. Rate</th>
	                    <th width="100">Req. Amount</th>
	                    <th width="75">Yarn Inhouse Date</th>


	                    <th width="50">WO No.</th>
	                    <th width="80">Yarn Color</th>
	                    <th width="50">Count</th>
	                    <th width="250">Composition</th>
	                    <th width="80">Yarn Type</th>
	                    <th width="50">UOM</th>
	                    <th width="80">WO Qnty</th>
	                    <th width="70">WO Rate</th>
	                    <th width="100">WO Amount</th>

	                    <th width="150">Supplier</th>
	                    <th width="100">PI No.</th>
	                    <th width="70">PI Date</th>
	                    <th width="70">Last Ship Date</th>
	                    <th width="80">Yarn Color</th>
	                    <th width="50">Count</th>
	                    <th width="250">Composition</th>
	                    <th width="80">Yarn Type</th>
	                    <th width="50">UOM</th>
	                    <th width="80">PI Qnty</th>
	                    <th width="70">PI Rate</th>
	                    <th width="100">PI Amount</th>
	                    <th width="70">Currency</th>

	                    <th width="70">LC Date</th>
	                    <th width="100">LC No</th>
	                    <th width="100">Issuing Bank</th>
	                    <th width="70">Pay Term</th>
	                    <th width="80">Tenor</th>
	                    <th width="100">LC Amount</th>

	                    <th width="80">Rcvd</th>
	                    <th width="100">Rcvd Due</th>
	                    <th width="80">Issue</th>
	                    <th width="80">Stock Qnty</th>
	                    <th width="70">Used Buyer</th>
	                    <th>Used Order</th>
	                </tr>
	            </thead>
	        </table>
	        <div style="width:5120px; overflow-y:scroll; max-height:200px;font-size:12px; overflow-x:hidden;" id="scroll_body" align="left">
	        <table width="5100px" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left">
	            <tbody>
	            <?
	            $k=1;
				
				$array_check=array();$m=1;$q=1;
	            foreach($req_result as $row)
	            {
	            	if($row[csf("wo_dtls_id")]=='') 
	            	{
	            		$row[csf("wo_dtls_id")]=0;
	            	}
	            	
					if(!in_array($row[csf("id")],$temp_arr_req))
					{
						$temp_arr_req[]=$row[csf("id")];
						if($m%2==0)$bgcolor="#F8F9D0";else $bgcolor="#C8C4FD"; 
						$m++;
					}
					
					$mrr_qnty=$pipe_wo_qnty=$pipe_pi_qnty=$pipe_line=0;$min_date=$max_date="";
					$mrr_value=$short_value=0;
					$booking_id=$receive_basis="";			

					$id=$row_result[csf('id')];
					$reqsn_rate=$row[csf("amount")]/$row[csf("req_quantity")];
					if($req_wo_recv_arr[$row[csf("wo_dtls_id")]][2][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]]['recv_amt']!="")
					{
						$wo_pi_ids=$row[csf("wo_dtls_id")];
					}
					else
					{
						$wo_pi_ids=$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"];
					}
					
					if($mrr_check_prev[$wo_pi_ids][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]]=="")
	                {
						$mrr_check_prev[$wo_pi_ids][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]]=$row[csf("wo_dtls_id")];

						if($req_wo_recv_arr[$row[csf("wo_dtls_id")]][2][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]]['recv_qnty']!="")
						$mrr_qnty=$req_wo_recv_arr[$row[csf("wo_dtls_id")]][2][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]]['recv_qnty'];

						else $mrr_qnty=$req_wo_recv_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]][1][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]]['recv_qnty'];
						//else $mrr_qnty=$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"];
						
						if($req_wo_recv_arr[$row[csf("wo_dtls_id")]][2][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]]['recv_amt']!="")
						{
							$mrr_value=$req_wo_recv_arr[$row[csf("wo_dtls_id")]][2][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]]['recv_amt'];
							$min_date=$req_wo_recv_arr[$row[csf("wo_dtls_id")]][2][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]]['min_date'];
							$max_date=$req_wo_recv_arr[$row[csf("wo_dtls_id")]][2][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]]['max_date'];
							$short_value=$row[csf("wo_amount")]-$mrr_value;
							//echo $booking_id=$req_wo_recv_arr[$row[csf("wo_dtls_id")]][2][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]]['booking_id'];
							
							$receive_basis=2;
						}
						else 
						{
							
							$mrr_value=$req_wo_recv_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]][1][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]]['recv_amt'];
							$min_date=$req_wo_recv_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]][1][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]]['min_date'];
							$max_date=$req_wo_recv_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]][1][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]]['max_date'];
							
							$short_value=$row[csf("wo_amount")]-$pi_data_arr[$row[csf("wo_dtls_id")]]["amount"];
							$booking_id=$req_wo_recv_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]][1][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]]['booking_id'];
							$receive_basis=1;
						}

						//======================
						if($req_wo_recv_arr[$row[csf("wo_dtls_id")]][2][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]]['prod_id']!="")
						$prod_id=$req_wo_recv_arr[$row[csf("wo_dtls_id")]][2][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]]['prod_id'];

						else $prod_id=$req_wo_recv_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]][1][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]]['prod_id'];

						//$req_wo_recv_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]][1][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]]['recv_qnty'];

						
						//=============================
						
					}

					
					if($pi_data_arr[$row[csf("wo_dtls_id")]]["amount"]=="") $pi_data_arr[$row[csf("wo_dtls_id")]]["amount"]=0;
					if($mrr_value=="") $mrr_value=0;
					if($mrr_qnty=="") $mrr_qnty=0;
					if($mrr_value>0 && $mrr_qnty>0)  $recv_rate=$mrr_value/$mrr_qnty;
					
					$receiving_cond=0;
					if($cbo_receive_status==1 && ($mrr_value=="" || $mrr_value==0)) $receiving_cond=1;
					if($cbo_receive_status==2 && $receive_basis==2 && $row[csf("wo_amount")]>$mrr_value && $mrr_value>0) $receiving_cond=1;
					if($cbo_receive_status==2 && $receive_basis==1 && $pi_data_arr[$row[csf("wo_dtls_id")]]["amount"]>$mrr_value && $mrr_value>0) $receiving_cond=1;
					if($cbo_receive_status==3 && $receive_basis==2 && $mrr_value>=$row[csf("wo_amount")] && $mrr_value>0) $receiving_cond=1;
					if($cbo_receive_status==3 && $receive_basis==1 && $mrr_value>=$pi_data_arr[$row[csf("wo_dtls_id")]]["amount"] && $mrr_value>0) $receiving_cond=1;
					if($cbo_receive_status==4 && $receive_basis==2 && $mrr_value < $row[csf("wo_amount")]) $receiving_cond=1;
					if($cbo_receive_status==4 && $receive_basis==1 && $mrr_value < $pi_data_arr[$row[csf("wo_dtls_id")]]["amount"]) $receiving_cond=1;
					if($cbo_receive_status==5) $receiving_cond=1;


					// Yarn Purchase Requisition Print Link
					$print_format_id_requ=$print_report_format_yarnPurchaseRequ[0];
					if ($print_format_id_requ==134)  //Print button
					{		
						$report_title='Yarn Purchase Requisition';							
						$print_link_requ="<a href='#' onClick=\"print_report('".str_replace("'", "", $cbo_company_name)."*".$row[csf("id")]."*".$report_title."*".$row[csf("is_approved")]."', 'yarn_requisition_print', '../../commercial/work_order/requires/yarn_requisition_entry_controller')\"> ".$row[csf("requ_prefix_num")]." <a/>";
					} 
					else if ($print_format_id_requ==135)  //Print 2 Button
					{									
						$report_title='Yarn Purchase Requisition';							
						$print_link_requ="<a href='#' onClick=\"print_report('".str_replace("'", "", $cbo_company_name)."*".$row[csf("id")]."*".$report_title."*".$row[csf("basis")]."', 'yarn_requisition_print_2', '../../commercial/work_order/requires/yarn_requisition_entry_controller')\"> ".$row[csf("requ_prefix_num")]." <a/>";
						
					}
					else if ($print_format_id_requ==136)  //Print 3 Button
					{									
						$report_title='Yarn Purchase Requisition';							
						$print_link_requ="<a href='#' onClick=\"print_report('".str_replace("'", "", $cbo_company_name)."*".$row[csf("id")]."*".$report_title."*7*".$row[csf("basis")]."', 'yarn_requisition_print_3', '../../commercial/work_order/requires/yarn_requisition_entry_controller')\"> ".$row[csf("requ_prefix_num")]." <a/>";
						
					}
					else if ($print_format_id_requ==137)  //Print 4 Button
					{									
						$report_title='Yarn Purchase Requisition';							
						$print_link_requ="<a href='#' onClick=\"print_report('".str_replace("'", "", $cbo_company_name)."*".$row[csf("id")]."*".$report_title."', 'yarn_requisition_print_4', '../../commercial/work_order/requires/yarn_requisition_entry_controller')\"> ".$row[csf("requ_prefix_num")]." <a/>";
						
					}
					else if ($print_format_id_requ==64)  //Print 5 Button
					{									
						$report_title='Yarn Purchase Requisition';							
						$print_link_requ="<a href='#' onClick=\"print_report('".str_replace("'", "", $cbo_company_name)."*".$row[csf("id")]."*".$report_title."', 'yarn_requisition_print_5', '../../commercial/work_order/requires/yarn_requisition_entry_controller')\"> ".$row[csf("requ_prefix_num")]." <a/>";
						
					}
					else if ($print_format_id_requ==72)  //Print 6 Button
					{									
						$report_title='Yarn Purchase Requisition';							
						$print_link_requ="<a href='#' onClick=\"print_report('".str_replace("'", "", $cbo_company_name)."*".$row[csf("id")]."*".$report_title."*".$row[csf("basis")]."', 'yarn_requisition_print_6', '../../commercial/work_order/requires/yarn_requisition_entry_controller')\"> ".$row[csf("requ_prefix_num")]." <a/>";
						
					}
					else $print_link_requ=$row[csf("requ_prefix_num")];


					// Yarn Purchase Work Orde Print Link
					$print_format_id_wo=$print_report_format_yarnPurchaseOrder[0];
					if ($print_format_id_wo==78)  //Print button
					{		
						$report_title='Yarn Purchase Order';
						$css_style='YarnPurchaseOrderPrintButton';						
						$print_link_wo="<a href='#' onClick=\"print_report('".str_replace("'", "", $cbo_company_name)."*".$row[csf("wo_id")]."*".$report_title."*".$row[csf("ref_closing_status")]."*".$row[csf("pay_mode")]."*".$row[csf("wo_approved")]."*".$css_style."', 'yarn_work_order_print', '../../commercial/work_order/requires/yarn_work_order_controller')\"> ".$row[csf("wo_number_prefix_num")]." <a/>";
					} 
					else if ($print_format_id_wo==84)  //Print 2 Button
					{									
						$report_title='Yarn Purchase Order';							
						$css_style='YarnPurchaseOrderPrintButton';
						$print_link_wo="<a href='#' onClick=\"print_report('".str_replace("'", "", $cbo_company_name)."*".$row[csf("wo_id")]."*".$report_title."*1*".$row[csf("ref_closing_status")]."*".$row[csf("wo_approved")]."*".$css_style."', 'print_to_html_report', '../../commercial/work_order/requires/yarn_work_order_controller')\"> ".$row[csf("wo_number_prefix_num")]." <a/>";
						
					}
					else if ($print_format_id_wo==85)  //Print 3 Button
					{									
						$report_title='Yarn Purchase Order';
						$css_style='YarnPurchaseOrderPrintButton';						
						$print_link_wo="<a href='#' onClick=\"print_report('".str_replace("'", "", $cbo_company_name)."*".$row[csf("wo_id")]."*".$report_title."*2*".$row[csf("ref_closing_status")]."*".$row[csf("wo_approved")]."*".$css_style."', 'print_to_html_report2', '../../commercial/work_order/requires/yarn_work_order_controller')\"> ".$row[csf("wo_number_prefix_num")]." <a/>";
						
					}
					else if ($print_format_id_wo==193)  //Print 4 Button
					{									
						$report_title='PURCHASE ORDER';	
						$css_style='YarnPurchaseOrderPrintButton';					
						$print_link_wo="<a href='#' onClick=\"print_report('".str_replace("'", "", $cbo_company_name)."*".$row[csf("wo_id")]."*".$report_title."*4*".$row[csf("ref_closing_status")]."*".$row[csf("wo_approved")]."*".$css_style."', 'print_to_html_report4', '../../commercial/work_order/requires/yarn_work_order_controller')\"> ".$row[csf("wo_number_prefix_num")]." <a/>";
						
					}
					else if ($print_format_id_wo==129)  //Print 5 Button
					{									
						$report_title='Yarn Purchase Order';						
						$print_link_wo="<a href='#' onClick=\"print_report('".str_replace("'", "", $cbo_company_name)."*".$row[csf("wo_id")]."*".$report_title."*".$row[csf("ref_closing_status")]."*".$row[csf("pay_mode")]."*".$row[csf("wo_approved")]."', 'yarn_work_order_print5', '../../commercial/work_order/requires/yarn_work_order_controller')\"> ".$row[csf("wo_number_prefix_num")]." <a/>";
						
					}
					else if ($print_format_id_wo==72)  //Print 6 Button
					{									
						$report_title='Yarn Purchase Order';
						$css_style='YarnPurchaseOrderPrintButton';							
						$print_link_wo="<a href='#' onClick=\"print_report('".str_replace("'", "", $cbo_company_name)."*".$row[csf("wo_id")]."*".$report_title."*3*".$row[csf("ref_closing_status")]."*".$row[csf("wo_approved")]."*".$css_style."', 'print_to_html_report3', '../../commercial/work_order/requires/yarn_work_order_controller')\"> ".$row[csf("wo_number_prefix_num")]." <a/>";
						
					}				
					else $print_link_wo=$row[csf("wo_number_prefix_num")];

					if($receiving_cond==1)
					{						
						?>
	                    <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
	                        <td width="30" align="center" title="<? echo $cbo_receive_status."=".$receive_basis."=".$mrr_value."=".$row[csf("wo_amount")]."=".$pi_data_arr[$row[csf("wo_dtls_id")]]["amount"]; ?>"><p><? echo $k;//$row_result[csf('id')];?></p></td>
	                        <td width="50" align="center" title="<? echo $row[csf("id")]?>"><p><? echo $print_link_requ; ?></p></td>
	                        <td width="70" align="center"><p>&nbsp;<? if($row[csf("requisition_date")]!="" && $row[csf("requisition_date")]!='0000-00-00') echo change_date_format($row[csf("requisition_date")]); ?></p></td>
	                        <td width="80"><p><? echo $approval_arr[$row[csf("id")]]; ?></p></td>
	                        <td width="100"><p><? echo $buyer_arr[$row[csf("buyer_id")]]; ?></p></td>
	                        <td width="80"><p>&nbsp;<? echo  implode(',',array_flip(array_flip(explode(',', rtrim($job_order_arr[$row[csf("job_no")]]['po_received_date'],','))))); ?></p></td>
	                        <td width="150"><p><? echo implode(',',array_flip(array_flip(explode(',', rtrim($job_order_arr[$row[csf("job_no")]]['po_number'],','))))); ?></p></td>
	                        <td width="100"><p><? echo $job_order_arr[$row[csf("job_no")]]['style_ref_no']; ?></p></td>
	                        <td width="100"><p><? echo $row[csf("job_no")]; ?></p></td>
	                        <td width="150"><p><? echo $booking = implode(',',array_flip(array_flip(explode(',', rtrim($job_order_arr[$row[csf("job_no")]]['booking_no'],','))))); ?></p></td>
	                        <td width="130"><p>&nbsp;<? echo implode(',',array_flip(array_flip(explode(',', rtrim($job_order_arr[$row[csf("job_no")]]['booking_date'],','))))); ?></p></td>
	                        <td width="80"><p>&nbsp;<? echo implode(',',array_flip(array_flip(explode(',', rtrim($job_order_arr[$row[csf("job_no")]]['pub_shipment_date'],','))))); ?></p></td>
	                        <td width="80"><p>&nbsp;<? echo change_date_format($row[csf("delivery_date")]); ?></p></td>
	                        <td width="80" align="right"><p><? echo $job_order_arr[$row[csf("job_no")]]['job_quantity']; ?></p></td>
	                        <td width="80" align="center"><p><? echo $color_arr[$row[csf("color_id")]]; ?></p></td>
	                        <td width="50" align="center"><p>&nbsp;<? echo $yarnCount_arr[$row[csf("count_id")]]; ?></p></td>
	                        <td width="150"><p><? echo $composition[$row[csf("composition_id")]]." ".$row[csf("com_percent")]."%"; ?></p></td>
	                        <td width="80"><p><? echo $yarn_type[$row[csf("yarn_type_id")]]; ?></p></td>
	                        <td width="50"><p><? echo $unit_of_measurement[$row[csf("req_uom")]]; ?></p></td>
	                        <td width="80" align="right"><p><? echo number_format($row[csf("req_qnty")],2); ?></p></td>
	                        <td width="70" align="right"><p>&nbsp;<? echo number_format($row[csf("req_rate")],2); ?></p></td>
	                        <td width="100" align="right"><p><? echo number_format($row[csf("req_amt")],2); ?></p></td>
	                        <td width="75"  align="center"><p>&nbsp;<? if($row[csf("yarn_inhouse_date")]!="" && $row[csf("yarn_inhouse_date")]!="0000-00-00") echo change_date_format($row[csf("yarn_inhouse_date")]); ?></p></td>
	                        <td width="50" align="center" title="<? echo $row[csf("wo_id")]?>"><p><? echo $print_link_wo; ?></p></td>
	                        <td width="80"><p><? echo $color_arr[$row[csf("wo_color")]]; ?></p></td>
	                        <td width="50" align="center"><p>&nbsp;<? echo $yarnCount_arr[$row[csf("wo_count")]]; ?></p></td>
	                        <td width="250"><p>
	                        <? 
	                        if($row[csf("yarn_comp_type2nd")]>0) $wo_com_percent2=$row[csf("yarn_comp_percent2nd")]."%"; else $wo_com_percent2=" ";
	                        if ($row[csf("wo_yarn_comp_percent1st")] >0 ) $wo_com_percent1=$row[csf("wo_yarn_comp_percent1st")]."%"; else $wo_com_percent1 = "";
	                        echo $composition[$row[csf("wo_yarn_comp_type1st")]]." ".$wo_com_percent1." ".$composition[$row[csf("yarn_comp_type2nd")]]." ".$wo_com_percent2; ?></p></td>
	                        <td width="80"><p><? echo $yarn_type[$row[csf("wo_yarn_type")]]; ?></p></td>
	                        <td width="50"><p><? echo $unit_of_measurement[$row[csf("wo_uom")]]; ?></p></td>
	                        <td width="80" align="right"><p><? echo number_format($row[csf("wo_qnty")],2); ?></p></td>
	                        <td width="70" align="right"><p><? echo number_format($row[csf("wo_rate")],2); ?></p></td>
	                        <td width="100" align="right"><p><? echo number_format($row[csf("wo_amount")],2); ?></p></td>	

	                        <td width="150"><p><? $suplier_pi_arr=array_unique(explode(",",chop($pi_data_arr[$row[csf("wo_dtls_id")]]["pi_suplier"]," , ")));
	                        //echo "10**".$row[csf("wo_dtls_id")]; die; //print_r($suplier_pi_arr);
	                        $supplier='';
	                        foreach ($suplier_pi_arr as $value) 
	                        {
	                        	$supplier.=$supplier_arr[$value].",";
	                        }
	                        echo chop($supplier,","); ?></p></td>
	                        <td width="100"><p><? $pi_num=implode(" , ",array_unique(explode(",",chop($pi_data_arr[$row[csf("wo_dtls_id")]]["pi_number"]," , ")))); echo $pi_num; ?></p></td>
	                        <td width="70"><p>&nbsp;
	                        <? 
	                        $pi_date_arr=array_unique(explode(",",chop($pi_data_arr[$row[csf("wo_dtls_id")]]["pi_date"]," , ")));
	                        $pi_date='';
	                        foreach ($pi_date_arr as $value) 
	                        {
	                        	if($value !="" && $value!="0000-00-00")
	                        	{
	                        		$pi_date.=change_date_format($value)."</br>";
	                        	}
	                        }
	                        //$pi_date=implode(",",$pi_date_arr); 
	                        //if($pi_date_arr[0]!="" && $pi_date_arr[0]!="0000-00-00") echo change_date_format($pi_date_arr[0]); 
	                        echo chop($pi_date,"</br>");
	                        ?></p></td>
	                        <td width="70"><p>&nbsp;
	                        <? 
	                        $pi_last_ship_date_arr=array_unique(explode(",",chop($pi_data_arr[$row[csf("wo_dtls_id")]]["last_shipment_date"]," , "))); 

	                        $shipment_date='';
	                        foreach ($pi_last_ship_date_arr as $value) 
	                        {
	                        	if($value !="" && $value!="0000-00-00")
	                        	{
	                        		$shipment_date.=change_date_format($value)."</br>";
	                        	}
	                        }
	                       	// if($pi_last_ship_date_arr[0]!="" && $pi_last_ship_date_arr[0]!="0000-00-00") echo change_date_format($pi_last_ship_date_arr[0]); 
	                        echo chop($shipment_date,"</br>");
	                        ?></p></td>
	                        <td width="80" title="<? echo "color id=".$pi_data_arr[$row[csf("wo_dtls_id")]]["color_id"];?>"><p><? echo  $color_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["color_id"]]; ?></p></td>
	                        <td width="50" title="<? echo "count id=".$pi_data_arr[$row[csf("wo_dtls_id")]]["count_name"];?>"><p>&nbsp;<? echo  $yarnCount_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["count_name"]]; ?></p></td>
	                        <td width="250" title="<? echo "count id=".$pi_data_arr[$row[csf("wo_dtls_id")]]["yarn_composition_item1"];?>"><p>
	                        <?
	                        if($pi_data_arr[$row[csf("wo_dtls_id")]]["yarn_composition_item2"]>0) $com_percent2=$pi_data_arr[$row[csf("wo_dtls_id")]]["yarn_comp_percent2nd"]."%"; else $com_percent2="";
	                        if($pi_data_arr[$row[csf("wo_dtls_id")]]["yarn_composition_item1"]>0) $com_percent=$pi_data_arr[$row[csf("wo_dtls_id")]]["yarn_composition_percentage1"]."%"; else $com_percent="";
	                        echo  $composition[$pi_data_arr[$row[csf("wo_dtls_id")]]["yarn_composition_item1"]]." ".$com_percent." ".$composition[$pi_data_arr[$row[csf("wo_dtls_id")]]["yarn_composition_item2"]]." ".$com_percent2;
	                        ?></p></td>
	                        <td width="80" title="<? echo "yarn type id=".$pi_data_arr[$row[csf("wo_dtls_id")]]["yarn_type"];?>"><p><? echo  $yarn_type[$pi_data_arr[$row[csf("wo_dtls_id")]]["yarn_type"]]; ?></p></td>
	                        <td width="50"><p><? echo  $unit_of_measurement[$pi_data_arr[$row[csf("wo_dtls_id")]]["uom"]]; ?></p></td>
	                        <td width="80" align="right"><p><? $pi_qnty=$pi_data_arr[$row[csf("wo_dtls_id")]]["quantity"]; echo  number_format($pi_qnty,2); ?></p></td>
	                        <td width="70" align="right"><p><? $pi_rate=$pi_data_arr[$row[csf("wo_dtls_id")]]["rate"]; echo  number_format($pi_rate,2); ?></p></td>
	                        <td width="100" align="right"><p><? $pi_amt=$pi_data_arr[$row[csf("wo_dtls_id")]]["amount"]; echo  number_format($pi_amt,2); ?></p></td>
	                        <td width="70"><p><? $pi_curency_arr=array_unique(explode(",",chop($pi_data_arr[$row[csf("wo_dtls_id")]]["currency_id"]," , "))); 
	                        $currency_val='';
	                        foreach ($pi_curency_arr as $value) 
	                        {
	                        	$currency_val.=$currency[$value].",";
	                        }
	                        echo chop($currency_val,",");
	                        //echo  $currency[$pi_curency_arr[0]]; ?></p></td>
	                        <?
	                        $btb_lc_no=$btb_issue_bank=$btb_pay_term=$btb_tenor="";$btb_lc_amount=0; $btb_tenor="";$btb_lc_date="";
	                        if(!in_array($pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"],$temp_arr_btb))
	                        {
	                            $temp_arr_btb[]=$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"];
	                            $btb_lc_no_arr=array_unique(explode(",",chop($btb_data_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]]["lc_number"]," , ")));
	                            $btb_lc_no=implode(" , ",$btb_lc_no_arr);

	                            $btb_issue_bank_arr=array_unique(explode(",",chop($btb_data_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]]["issuing_bank_id"]," , "))); 
	                            foreach ($btb_issue_bank_arr as $value) 
		                        {
		                        	$btb_issue_bank.=$bank_arr[$value].",";
		                        }

	                            $btb_pay_tarm_arr=array_unique(explode(",",chop($btb_data_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]]["payterm_id"]," , ")));
		                        foreach ($btb_pay_tarm_arr as $value) 
		                        {
		                        	$btb_pay_term.=$pay_term[$value].",";
		                        }

	                            $btb_tenor_arr=array_unique(explode(",",chop($btb_data_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]]["tenor"]," , ")));
	                           	$btb_tenor=implode(" Days, ",$btb_tenor_arr);

	                            $btb_lc_amount=$btb_data_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]]["lc_value"];
	                            $btb_lc_date_arr=array_unique(explode(",",chop($btb_data_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]]["lc_date"]," , "))); 
	                            $btb_lc_date='';
		                        foreach ($btb_lc_date_arr as $value) 
		                        {
		                        	if($value !="" && $value!="0000-00-00")
		                        	{
		                        		$btb_lc_date.=change_date_format($value)."</br>";
		                        	}
		                        }
	                        }
	                        else
	                        {
	                            $btb_lc_no=$btb_issue_bank=$btb_pay_term="";
	                            $btb_lc_amount=0;
	                            $btb_tenor="";
	                            $btb_lc_date="";
	                        }
	                        ?>
	                        <td width="70"><p>&nbsp;<? echo chop($btb_lc_date,"</br>");  ?></p></td>
	                        
	                        <td width="100"><p><?  echo chop( $btb_lc_no,","); ?></p></td>
	                        <td width="100"><p><? echo  chop( $btb_issue_bank,","); ?></p></td>
	                        <td width="70"><p><? echo chop( $btb_pay_term,",");  ?></p></td>
	                        <td width="80"><p><? if ($btb_tenor!='') echo $btb_tenor." Days";  ?></p></td>
	                        <td width="100" align="right"><p><? if($btb_lc_amount>0) echo number_format($btb_lc_amount,2); ?></p></td>
	                        <?
	                        //if(!in_array($row[csf("wo_id")],$temp_arr_rcv))
							$pipe_line="";
							if($mrr_check[$wo_pi_ids][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]]=="")
	                        {
								$mrr_check[$wo_pi_ids][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]]=$row[csf("color_id")];
								$pipe_wo_qnty=$wo_pipe_array[$wo_pi_ids][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]];
								$pipe_pi_qnty=$pi_pipe_array[$wo_pi_ids][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]];
								//echo $pipe_wo_qnty.'system'.$pipe_pi_qnty;
								$pipe_line=(($pipe_wo_qnty+$pipe_pi_qnty)-$mrr_qnty);
								$total_pipe_line += $pipe_line;
	                            if($mrr_qnty>0)
	                            { 
									$pipe_mrr_qnty=$mrr_qnty;
									
	                                ?>
	                                <td width="80" align="right" title="<? echo $wo_pi_ids."=".$receive_basis."=".$row[csf("color_id")]."=".$row[csf("yarn_type_id")]."=".$row[csf("count_id")]."=".$row[csf("composition_id")];?>"><p><a href="##" onclick="fn_mrr_details('<? echo $booking_id;?>','<? echo $receive_basis;?>','<? echo $row[csf("color_id")];?>','<? echo $row[csf("yarn_type_id")];?>','<? echo $row[csf("count_id")];?>','<? echo $row[csf("composition_id")];?>','receive_details_popup_analysis')"><? echo number_format($mrr_qnty,2); ?></a></p></td>
	                                <?
									 $total_mrr_qnty  += $mrr_qnty;
	                    			 //
	                            }
	                            else
	                            {
	                                ?>
	                                <td width="80" align="right" title="<? echo $wo_pi_ids."=".$receive_basis."=".$row[csf("color_id")]."=".$row[csf("yarn_type_id")]."=".$row[csf("count_id")]."=".$row[csf("composition_id")];?>"><p></p></td>
	                                <?
	                            }
	                        }
	                        else
	                        {
	                            $mrr_qnty=$mrr_value=0;
								//if($mrr_check[$wo_pi_ids][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]]=="")$pipe_mrr_qnty=0;
	                            ?>
	                            <td width="80" align="right" title="<? echo $wo_pi_ids."=".$receive_basis."=".$row[csf("color_id")]."=".$row[csf("yarn_type_id")]."=".$row[csf("count_id")]."=".$row[csf("composition_id")];?>"><p></p></td>
	                            <?
	                        }
	                        ?>
	                        <td width="100" align="right"><p><? 
							$rcvd_due = $pi_qnty-$mrr_qnty;
							if($rcvd_due>0) { echo number_format($rcvd_due,2); $total_rcvd_due += $rcvd_due; }?>
							</p></td>
	                      
							<?php	
							//echo $booking."=".$prod_id."=3"."=".$row[csf("color_id")]."=".$row[csf("yarn_type_id")]."=".$row[csf("count_id")]."=".$row[csf("composition_id")]."<br>";
							$issue_qnty=0;
							$booking_arr=explode(",",$booking);
							foreach($booking_arr as $book_no)
							{
								$issue_qnty+=$req_wo_issue_arr[$book_no][$prod_id][3][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]]['issue_qnty'];
								$issue_basis=$req_wo_issue_arr[$book_no][$prod_id][3][$row[csf("color_id")]][$row[csf("yarn_type_id")]][$row[csf("count_id")]][$row[csf("composition_id")]]['issue_basis'];
							}
							//echo $issue_qnty."<br>";
							if($issue_qnty>0)
							{
								
								?>
								<td width="80" align="right" title=""><p>
								<a href="##" onclick="fn_mrr_details('<? echo $prod_id;?>','<? echo $issue_basis;?>','<? echo $row[csf("color_id")];?>','<? echo $row[csf("yarn_type_id")];?>','<? echo $row[csf("count_id")];?>','<? echo $row[csf("composition_id")];?>','issue_details_popup')"><? echo number_format($issue_qnty,2); $total_issue_qnty+= $issue_qnty;?></a>
								</p></td>
								<td align="right" width="80" title=""><? 
								$Stock_Qty= $mrr_qnty-$issue_qnty;
								if($Stock_Qty>0) { echo number_format($Stock_Qty,2); $total_Stock_Qty += $Stock_Qty; } ?></td>
								<td width="70"><p><? if($issue_qnty > 0 )  echo $buyer_arr[$row[csf("buyer_id")]]; ?></p></td>
								<td width=""><p><?  if($issue_qnty > 0 ) echo implode(',',array_flip(array_flip(explode(',', rtrim($used_order_arr[$booking]['po_number'],',')))));; ?></p></td>
								<?
								//var_dump($issue_basis);
							}
							else
							{
								//$Stock_Qty= $mrr_qnty-$issue_qnty;
								?>
								<td width="80" align="right" title=""><p></p></td>
								<td width="80" align="right" title=""><p><? echo $mrr_qnty?></p></td>
								<td width="70" align="right" title=""><p></p></td>
								<td width="" align="right" title=""><p></p></td>
								<?
							}
							?>
	                    </tr>
	                    <?
	                    $k++;
						$total_req_qty   += $row[csf("req_qnty")];
	                    $total_req_amount+=$row[csf("req_amt")];
	                    $total_wo_qty+=$row[csf("wo_qnty")];
	                    $total_wo_amount+=$row[csf("wo_amount")];
	                    $total_pi_qnty   += $pi_data_arr[$row[csf("wo_dtls_id")]]["quantity"];
	                    $total_pi_amt    += $pi_data_arr[$row[csf("wo_dtls_id")]]["amount"];
	                    $total_short_amt += $short_amt;                    
					}				
	            }
	            ?>
	            </tbody>
	        </table>
	        </div>
	        <table cellspacing="0" width="5100px"  border="1" rules="all" class="rpt_table" id="report_table_footer" >
	            <tfoot>
	            	<th width="30"></th>
	                <th width="50">&nbsp;</th>
	                <th width="70">&nbsp;</th>
	                <th width="80">&nbsp;</th>
	                <th width="100">&nbsp;</th>
	                <th width="80">&nbsp;</th>


	                <th width="150">&nbsp;</th>
	                <th width="100">&nbsp;</th>
	                <th width="100">&nbsp;</th>
	                <th width="150">&nbsp;</th>
	                <th width="130">&nbsp;</th>
	                <th width="80">&nbsp;</th>
	                <th width="80">&nbsp;</th>
	                <th width="80">&nbsp;</th>


	                <th width="80">&nbsp;</th>
	                <th width="50">&nbsp;</th>
	                <th width="150">&nbsp;</th>
	                <th width="80">&nbsp;</th>
	                <th width="50"><strong>Total:</strong></th>
					<th width="80" id="value_tot_req_qty_id"><p><? echo number_format($total_req_qty,2);?></p></th>
	                <th width="70">&nbsp;</th>
					<th width="100" id="value_tot_req_amount_id"><? echo number_format($total_req_amount,2);?></th>
	                <th width="75" align="right"></th>
	                <th width="50" align="center">&nbsp;</th>
	                <th width="80" align="right">&nbsp;</th>
	                <th width="50" align="center">&nbsp;</th>
	                <th width="250" align="center">&nbsp;</th>
	                <th width="80" align="center">&nbsp;</th>
	                <th width="50" align="center">&nbsp;</th>
	                <th width="80" align="right" id="value_tot_wo_qty_id"><? echo number_format($total_wo_qty,2);?></th>
	                <th width="70" align="right">&nbsp;</th>
	                <th width="100" align="right" id="value_tot_wo_amount_id"><? echo number_format($total_wo_amount,2);?></th>
	                <th width="150" align="center">&nbsp;</th>
	                <th width="100" align="center">&nbsp;</th>
	                <th width="70" align="center">&nbsp;</th>
	                <th width="70" align="center">&nbsp;</th>
	                <th width="80" align="center">&nbsp;</th>
	                <th width="50" align="center">&nbsp;</th>
	                <th width="250" align="center">&nbsp;</th>
	                <th width="80" align="center">&nbsp;</th>

	                <th width="50" align="center"></th>
	                <th width="80" align="right" id="value_tot_pi_qnty_id"><? echo number_format($total_pi_qnty,2); ?></th>

	                <th width="70" align="center"></th>
	                <th width="100" align="right" id="value_tot_pi_amt_id"><? echo number_format($total_pi_amt,2); ?></th>

	                <th width="70" align="center">&nbsp;</th>
	                <th width="70" align="center">&nbsp;</th>
	                <th width="100" align="center">&nbsp;</th>
	                <th align="right" width="100">&nbsp;</th>
	                <th width="70" align="center">&nbsp;</th>
	                <th width="80" align="center">&nbsp;</th>

	                <th width="100" align="center"></th>
	                <th width="80" align="right" id="value_tot_mrr_qnty_id"><? echo number_format($total_mrr_qnty,2); ?></th>                
	                <th width="100" align="right" id="value_tot_rcvd_due"><? echo number_format($total_rcvd_due,2); ?></th>
	                <th width="80" align="right" id="value_tot_issue_qnty"><? echo number_format($total_issue_qnty,2); ?></th>
	                <th width="80" align="right" id="value_tot_Stock_Qty" title="<? echo $test_data; ?>"><? echo number_format($total_Stock_Qty,2); ?></th>
	                <th width="70" align="center"></th>
	                <th align="center"></th>
	            </tfoot>        
			</table>
	        
	        </div>
	    	<br />
	        <div style="width:2770px" >
	        <?
			
			if($cbo_based_on==3)
			{
				if(!empty($pi_wo_dtls_id_all_arr))
				{
					$pi_wo_dtls_id_all_arr=array_chunk(array_unique($pi_wo_dtls_id_all),999);
					$sql_wo_independ="select d.id as wo_id, d.wo_number_prefix_num, d.wo_date, d.supplier_id, e.id as wo_dtls_id, e.color_name as wo_color, e.yarn_count as wo_count, e.yarn_comp_type1st as wo_yarn_comp_type1st, e.yarn_comp_percent1st as wo_yarn_comp_percent1st, e.yarn_comp_type2nd, e.yarn_comp_percent2nd, e.yarn_type as wo_yarn_type, e.uom as wo_uom, e.supplier_order_quantity as wo_qnty, e.rate as wo_rate, e.amount as wo_amount
				from wo_non_order_info_mst d, wo_non_order_info_dtls e
				where d.id=e.mst_id and d.wo_basis_id=2 and d.company_name=$cbo_company_name and d.entry_form=144 and e.item_category_id=1 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 $str_cond_independ ";
					$p=1;
					foreach($pi_wo_dtls_id_all_arr as $pi_wo_dtls_id)
					{
						if($p==1) $sql_wo_independ .=" and (e.id in(".implode(',',$pi_wo_dtls_id).")"; else $sql_wo_independ .=" or e.id in(".implode(',',$pi_wo_dtls_id).")";
						$p++;
					}
					$sql_wo_independ .=" ) ";
				}
				
			}
			else
			{
				$sql_wo_independ="select d.id as wo_id, d.wo_number_prefix_num, d.wo_date, d.supplier_id, e.id as wo_dtls_id, e.color_name as wo_color, e.yarn_count as wo_count, e.yarn_comp_type1st as wo_yarn_comp_type1st, e.yarn_comp_percent1st as wo_yarn_comp_percent1st, e.yarn_comp_type2nd, e.yarn_comp_percent2nd, e.yarn_type as wo_yarn_type, e.uom as wo_uom, e.supplier_order_quantity as wo_qnty, e.rate as wo_rate, e.amount as wo_amount
				from wo_non_order_info_mst d, wo_non_order_info_dtls e
				where d.id=e.mst_id and d.wo_basis_id=2 and d.company_name=$cbo_company_name and d.entry_form=144 and e.item_category_id=1 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0   $str_cond_independ ";
			}
			//echo $sql_wo_independ;//die;
			$req_result_independ=sql_select($sql_wo_independ);
			if($cbo_based_on==2 || $cbo_based_on==3)
			{
				?>
				<table width="2950"  align="left">
					<tr>
						<td style="font-size:18; font-weight:bold;">Based on Independent WO</td>
					</tr>
				</table>
				<br />
				<table width="2950" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_2"  align="left">
					<thead>
						<tr>
							<th width="30" rowspan="2">Sl</th>
							<th colspan="10">Work Order Details</th>
							<th colspan="12">PI Details</th>
							<th colspan="6">BTB LC Details</th>
							<th colspan="4">Matarials Received Information</th>
						</tr>
						<tr>
							<th width="50">WO No.</th>
							<th width="80">Yarn Color</th>
							<th width="50">Count</th>
							<th width="230">Composition</th>
							<th width="80">Yarn Type</th>
							<th width="50">UOM</th>
							<th width="80">WO Qnty</th>
							<th width="70">WO Rate</th>
							<th width="100">WO Amount</th>
							<th width="150">Supplier</th>
							<th width="100">PI No.</th>
							<th width="70">PI Date</th>
							<th width="70">Last Ship Date</th>
							<th width="80">Yarn Color</th>
							<th width="50">Count</th>
							<th width="230">Composition</th>
							<th width="80">Yarn Type</th>
							<th width="50">UOM</th>
							<th width="80">PI Qnty</th>
							<th width="70">PI Rate</th>
							<th width="100">PI Amount</th>
							<th width="70">Currency</th>
							<th width="70">LC Date</th>
							<th width="100">LC No</th>
							<th width="100">Issuing Bank</th>
							<th width="70">Pay Term</th>
							<th width="80">Tenor</th>
							<th width="100">LC Amount</th>
							<th width="80">MRR Qnty</th>
							<th width="100">MRR Value</th>
							<th width="100">Short Value</th>
	                        <th >Pipe Line</th>
						</tr>
					</thead>
				</table>
				<div style="width:2950px; overflow-y:scroll; max-height:200px;font-size:12px; overflow-x:hidden;" id="scroll_body2" align="left">
				<table width="2932" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body2" align="left">
					<tbody>
					<?
					$i=1;
					//print_r($wo_po_arr);die;
					$array_check=array();
					foreach($req_result_independ as $row)
					{ 
						if ($i%2==0)
						$bgcolor="#E9F3FF";
						else
						$bgcolor="#FFFFFF";
						$pine_wo_qnty=$pipe_pi_qnty=$pipe_line=$mrr_qnty=$mrr_value=$short_amt="";
						$wo_rate_inde=$req_wo_recv_arr[$row[csf("id")]][$row[csf("prod_id")]]['recv_amt']/$req_wo_recv_arr[$row[csf("id")]][$row[csf("prod_id")]]['recv_qnty'];
						
						
						if($req_wo_recv_arr[$row[csf("wo_id")]][2][$row[csf("wo_color")]][$row[csf("wo_yarn_type")]][$row[csf("wo_count")]][$row[csf("wo_yarn_comp_type1st")]]['recv_qnty']!="") 
						$mrr_qnty=$req_wo_recv_arr[$row[csf("wo_id")]][2][$row[csf("wo_color")]][$row[csf("wo_yarn_type")]][$row[csf("wo_count")]][$row[csf("wo_yarn_comp_type1st")]]['recv_qnty']; 
						else $mrr_qnty=$req_wo_recv_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]][1][$row[csf("wo_color")]][$row[csf("wo_yarn_type")]][$row[csf("wo_count")]][$row[csf("wo_yarn_comp_type1st")]]['recv_qnty'];
						
						if($req_wo_recv_arr[$row[csf("wo_id")]][2][$row[csf("wo_color")]][$row[csf("wo_yarn_type")]][$row[csf("wo_count")]][$row[csf("wo_yarn_comp_type1st")]]['recv_amt']!="")
						{
							$mrr_value=$req_wo_recv_arr[$row[csf("wo_id")]][2][$row[csf("wo_color")]][$row[csf("wo_yarn_type")]][$row[csf("wo_count")]][$row[csf("wo_yarn_comp_type1st")]]['recv_amt'];
							$short_amt=$row[csf("wo_amount")]-$mrr_value;
							
							$booking_id=$req_wo_recv_arr[$row[csf("wo_id")]][2][$row[csf("wo_color")]][$row[csf("wo_yarn_type")]][$row[csf("wo_count")]][$row[csf("wo_yarn_comp_type1st")]]['booking_id'];
							$receive_basis=2;
						}
						else 
						{
							$mrr_value=$req_wo_recv_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]][1][$row[csf("wo_color")]][$row[csf("wo_yarn_type")]][$row[csf("wo_count")]][$row[csf("wo_yarn_comp_type1st")]]['recv_amt'];
							$short_amt=$pi_data_arr[$row[csf("wo_dtls_id")]]["amount"]-$mrr_value;
							$booking_id=$req_wo_recv_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]][1][$row[csf("wo_color")]][$row[csf("wo_yarn_type")]][$row[csf("wo_count")]][$row[csf("wo_yarn_comp_type1st")]]['booking_id'];
							$receive_basis=1;
						}
						
						$receiving_cond=0;
						if($cbo_receive_status==1 && ($mrr_value=="" || $mrr_value==0)) $receiving_cond=1;
						if($cbo_receive_status==2 && $receive_basis==2 && $row[csf("wo_amount")]>$mrr_value && $mrr_value>0) $receiving_cond=1;
						if($cbo_receive_status==2 && $receive_basis==1 && $pi_data_arr[$row[csf("wo_dtls_id")]]["amount"]>$mrr_value && $mrr_value>0) $receiving_cond=1;
						if($cbo_receive_status==3 && $receive_basis==2 && $mrr_value>=$row[csf("wo_amount")] && $mrr_value>0) $receiving_cond=1;
						if($cbo_receive_status==3 && $receive_basis==1 && $mrr_value>=$pi_data_arr[$row[csf("wo_dtls_id")]]["amount"] && $mrr_value>0) $receiving_cond=1;
						if($cbo_receive_status==4 && $receive_basis==2 && $mrr_value < $row[csf("wo_amount")]) $receiving_cond=1;
						if($cbo_receive_status==4 && $receive_basis==1 && $mrr_value < $pi_data_arr[$row[csf("wo_dtls_id")]]["amount"]) $receiving_cond=1;
						if($cbo_receive_status==5) $receiving_cond=1;
						if($receiving_cond==1)
						{
							?>
	                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
	                            <td width="30" title="<? echo $cbo_receive_status."=".$receive_basis."=".$mrr_value."=".$row[csf("wo_amount")]."=".$pi_data_arr[$row[csf("wo_dtls_id")]]["amount"]; ?>"><? echo $i; ?></td>
	                            <td width="50" align="center"><p><? echo $row[csf("wo_number_prefix_num")]; ?></p></td>
	                            <td width="80"><p><? echo $color_arr[$row[csf("wo_color")]]; ?></p></td>
	                            <td width="50" align="center"><p>&nbsp;<? echo $yarnCount_arr[$row[csf("wo_count")]]; ?></p></td>
	                            <td width="230"><p>
	                            <?
	                            if ($row[csf("yarn_comp_type2nd")]>0) $compo_percent2=$row[csf("yarn_comp_percent2nd")]."%"; else $compo_percent2="";
	                            echo $composition[$row[csf("wo_yarn_comp_type1st")]]." ".$row[csf("wo_yarn_comp_percent1st")]."% ".$composition[$row[csf("yarn_comp_type2nd")]]." ".$compo_percent2; 
	                            ?></p></td>
	                            <td width="80"><p><? echo $yarn_type[$row[csf("wo_yarn_type")]]; ?></p></td>
	                            <td width="50"><p><? echo $unit_of_measurement[$row[csf("wo_uom")]]; ?></p></td>
	                            <td width="80" align="right"><p><? echo number_format($row[csf("wo_qnty")],0); ?></p></td>
	                            <td width="70" align="right"><p><? echo number_format($row[csf("wo_rate")],2); ?></p></td>
	                            <td width="100" align="right"><p><? echo number_format($row[csf("wo_amount")],2); ?></p></td>
	                           
	                            <td width="150"><p><? echo $supplier_arr[$row[csf("supplier_id")]]; ?></p></td>
	                            <td width="100"><p><? $pi_num=implode(" , ",array_unique(explode(",",chop($pi_data_arr[$row[csf("wo_dtls_id")]]["pi_number"]," , ")))); echo $pi_num; ?></p></td>
	                            <td width="70"><p>&nbsp;
	                            <? 
		                            $pi_date_arr=array_unique(explode(",",chop($pi_data_arr[$row[csf("wo_dtls_id")]]["pi_date"]," , "))); 
		                            $pi_date='';
			                        foreach ($pi_date_arr as $value) 
			                        {
			                        	if($value !="" && $value!="0000-00-00")
			                        	{
			                        		$pi_date.=change_date_format($value)."</br>";
			                        	}
			                        }
		                        	echo chop($pi_date,"</br>");
	                            ?></p></td>
	                            <td width="70"><p>&nbsp;
	                            <? 
	                            $pi_last_ship_date_arr=array_unique(explode(",",chop($pi_data_arr[$row[csf("wo_dtls_id")]]["last_shipment_date"]," , "))); 
	                            $shipment_date='';
		                        foreach ($pi_last_ship_date_arr as $value) 
		                        {
		                        	if($value !="" && $value!="0000-00-00")
		                        	{
		                        		$shipment_date.=change_date_format($value)."</br>";
		                        	}
		                        }
		                        echo chop($shipment_date,"</br>");
	                            ?></p></td>
	                            <td width="80"><p><? echo  $color_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["color_id"]]; ?></p></td>
	                            <td width="50"><p>&nbsp;<? echo  $yarnCount_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["count_name"]]; ?></p></td>
	                            <td width="230"><p>
	                            <?
	                            if($pi_data_arr[$row[csf("wo_dtls_id")]]["yarn_composition_item2"]>0) $com_percent2=$pi_data_arr[$row[csf("wo_dtls_id")]]["yarn_comp_percent2nd"]."%"; else $com_percent2="";
	                            if($pi_data_arr[$row[csf("wo_dtls_id")]]["yarn_composition_item1"]>0) $com_percent=$pi_data_arr[$row[csf("wo_dtls_id")]]["yarn_composition_percentage1"]."%"; else $com_percent="";
	                            echo  $composition[$pi_data_arr[$row[csf("wo_dtls_id")]]["yarn_composition_item1"]]." ".$com_percent." ".$composition[$pi_data_arr[$row[csf("wo_dtls_id")]]["yarn_composition_item2"]]." ".$com_percent2;  
	                            ?></p></td>
	                            <td width="80"><p><? echo  $yarn_type[$pi_data_arr[$row[csf("wo_dtls_id")]]["yarn_type"]]; ?></p></td>
	                            <td width="50"><p><? echo  $unit_of_measurement[$pi_data_arr[$row[csf("wo_dtls_id")]]["uom"]]; ?></p></td>
	                            <td width="80" align="right"><p><? $pi_qnty=$pi_data_arr[$row[csf("wo_dtls_id")]]["quantity"]; echo  number_format($pi_qnty,0); ?></p></td>
	                            <td width="70" align="right"><p><? $pi_rate=$pi_data_arr[$row[csf("wo_dtls_id")]]["rate"]; echo  number_format($pi_rate,2); ?></p></td>
	                            <td width="100" align="right"><p><? $pi_amt=$pi_data_arr[$row[csf("wo_dtls_id")]]["amount"]; echo  number_format($pi_amt,2); ?></p></td>
	                            <td width="70"><p><? $pi_curency_arr=array_unique(explode(",",chop($pi_data_arr[$row[csf("wo_dtls_id")]]["currency_id"]," , "))); echo  $currency[$pi_curency_arr[0]]; ?></p></td>
	                            <td width="70"><p>&nbsp;
	                            <? 
	                            $btb_lc_date_arr=array_unique(explode(",",chop($btb_data_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]]["lc_date"]," , "))); 
	                            $btb_lc_date='';
		                        foreach ($btb_lc_date_arr as $value) 
		                        {
		                        	if($value !="" && $value!="0000-00-00")
		                        	{
		                        		$btb_lc_date.=change_date_format($value)."</br>";
		                        	}
		                        }
	                           	echo chop($btb_lc_date,"</br>"); 
	                            ?></p></td>
	                            <td width="100"><p><? $btb_lc_no_arr=array_unique(explode(",",chop($btb_data_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]]["lc_number"]," , "))); 
	                            $btb_lc_no=implode(" , ",$btb_lc_no_arr);
	                            echo $btb_lc_no;  ?></p></td>
	        
	                            <td width="100"><p><? $btb_issue_bank_arr=array_unique(explode(",",chop($btb_data_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]]["issuing_bank_id"]," , "))); 
	                            foreach ($btb_issue_bank_arr as $value) 
		                        {
		                        	$btb_issue_bank.=$bank_arr[$value].",";
		                        }
	                            echo chop($btb_issue_bank,",");  ?></p></td>
	                            <td width="70"><p><? $btb_pay_tarm_arr=array_unique(explode(",",chop($btb_data_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]]["payterm_id"]," , "))); 
								foreach ($btb_pay_tarm_arr as $value) 
		                        {
		                        	$btb_pay_term.=$pay_term[$value].",";
		                        }
	                            echo chop($btb_pay_term,","); ?></p></td>
	                            <td width="80"><p><? $btb_tenor_arr=array_unique(explode(",",chop($btb_data_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]]["tenor"]," , "))); 
	                            $btb_tenor=implode(" Days, ",$btb_tenor_arr);
	                            if ($btb_tenor!='') echo $btb_tenor." Days"; ?></p></td>
	                            <td width="100" align="right"><p><? $btb_lc_amount=$btb_data_arr[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]]["lc_value"]; echo number_format($btb_lc_amount,2); ?></p></td>
	                            <?
	                            if($mrr_qnty>0)
	                            {
	                                ?>
	                                <td width="80" align="right"><p><a href="##" onclick="fn_mrr_details('<? echo $booking_id;?>','<? echo $receive_basis;?>','<? echo $row[csf("wo_color")];?>','<? echo $row[csf("wo_yarn_type")];?>','<? echo $row[csf("wo_count")];?>','<? echo $row[csf("wo_yarn_comp_type1st")];?>','receive_details_popup','<? echo $piIds;?>')"><? echo number_format($mrr_qnty,2);?> </a></p></td>
	                                <?
	                            }
	                            else
	                            {
	                                ?>
	                                <td width="80" align="right"></td>
	                                <?
	                            }
	                            ?>
	                            <td width="100" align="right"><p> <? echo number_format($mrr_value,2); ?></p></td>
	                            <td align="right" width="100"><p><? echo number_format($short_amt,2); ?></p></td>
	                            <td align="right"><p>
	                            <?
	                            //$pine_wo_qnty=$pipe_pi_qnty=$pipe_line 
	                            $pine_wo_qnty=$wo_pipe_array[$row[csf("wo_id")]][$row[csf("wo_color")]][$row[csf("wo_yarn_type")]][$row[csf("wo_count")]][$row[csf("wo_yarn_comp_type1st")]];
	                            $pipe_pi_qnty=$pi_pipe_array[$pi_data_arr[$row[csf("wo_dtls_id")]]["pi_id"]][$row[csf("wo_color")]][$row[csf("wo_yarn_type")]][$row[csf("wo_count")]][$row[csf("wo_yarn_comp_type1st")]];
	                            $pipe_line=(($pine_wo_qnty+$pipe_pi_qnty)-$mrr_qnty);
	                            if(number_format($pipe_line,2) > 0.00) echo number_format($pipe_line,2); else echo "0.00";
	                            ?>
	                            </p></td>
	                        </tr>
	                        <?
							$tot_ind_wo_qnty+=$row[csf("wo_qnty")];
							$tot_ind_wo_amount+=$row[csf("wo_amount")];
							$tot_ind_pi_qnty+=$pi_qnty;
							$tot_ind_pi_amt+=$pi_amt;
							$tot_ind_btb_lc_amount+=$btb_lc_amount;
							$tot_ind_mrr_qnty+=$mrr_qnty;
							$tot_ind_mrr_value+=$mrr_value;
							$tot_ind_short_amt+=$short_amt;
						}
						$k++;
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
							<th>Total</th>
							<th align="right"><? echo number_format($tot_ind_wo_qnty,2);?></th>
							<th>&nbsp;</th>
							<th align="right"><? echo number_format($tot_ind_wo_amount,2);?></th>
							<th>&nbsp;</th>
							<th>&nbsp;</th>
							<th>&nbsp;</th>
							<th>&nbsp;</th>
							<th>&nbsp;</th>
							<th>&nbsp;</th>
							<th>&nbsp;</th>
							<th>&nbsp;</th>
							<th>&nbsp;</th>
							<th align="right"><? echo number_format($tot_ind_pi_qnty,2);?></th>
							<th>&nbsp;</th>
							<th align="right"><? echo number_format($tot_ind_pi_amt,2);?></th>
							<th>&nbsp;</th>
							<th>&nbsp;</th>
							<th>&nbsp;</th>
							<th>&nbsp;</th>
							<th>&nbsp;</th>
							<th>&nbsp;</th>
							<th align="right"><? echo number_format($tot_ind_btb_lc_amount,2);?></th>
							<th align="right"><? echo number_format($tot_ind_mrr_qnty,2);?></th>
							<th align="right"><? echo number_format($tot_ind_mrr_value,2);?></th>
							<th>&nbsp;</th>
	                        <th>&nbsp;</th>
						</tr>
	                </tfoot>
				</table>
				</div>
				<?
			}
			?>
	    </div>
	    <br />
	    <div style="width:2030px" >
	    <?
		if($cbo_based_on==3)
	    {
	    	$sql_independent_pi="select a.id as pi_id, a.supplier_id as pi_suplier, a.pi_number, a.pi_date, a.last_shipment_date, a.currency_id, b.work_order_id, b.work_order_dtls_id, b.color_id, b.count_name, b.yarn_composition_item1, b.yarn_composition_percentage1, b.yarn_composition_item2, b.yarn_composition_percentage2, b.yarn_type, b.uom, b.quantity, b.rate, b.amount from com_pi_master_details a, com_pi_item_details b where a.pi_basis_id=2 and a.item_category_id=1 and a.importer_id=$cbo_company_name and a.id=b.pi_id and a.status_active=1 and b.status_active=1 $pi_cond";
	    	$req_result_pi_independ=sql_select($sql_independent_pi);
		}
			//echo $sql_independent_pi;die;
		if($cbo_based_on==3)
		{
			?>
			<table width="2150"  align="left">
				<tr>
					<td style="font-size:18; font-weight:bold;">Based on Independent PI</td>
				</tr>
			</table>
			<br />
			<table width="2150" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_3"  align="left">
				<thead>
					<tr>
						<th width="30" rowspan="2">Sl</th>
						<th colspan="14">PI Details</th>
						<th colspan="6">BTB LC Details</th>
						<th colspan="4">Matarials Received Information</th>
					</tr>
					<tr>
						<th width="150">Supplier</th>
						<th width="100">PI No.</th>
						<th width="70">PI Date</th>
						<th width="70">Last Ship Date</th>
						<th width="80">Yarn Color</th>
						<th width="50">Count</th>
						<th width="230">Composition</th>
						<th width="80">Yarn Type</th>
						<th width="50">UOM</th>
						<th width="80">PI Qnty</th>
						<th width="70">PI Rate</th>
						<th width="100">PI Amount</th>
						<th width="70">Currency</th>
						<th width="70">LC Date</th>
						<th width="100">LC No</th>
						<th width="100">Issuing Bank</th>
						<th width="70">Pay Term</th>
						<th width="80">Tenor</th>
						<th width="100">LC Amount</th>
						<th width="80">MRR Qnty</th>
						<th width="100">MRR Value</th>
						<th width="100">Short Value</th>
	                    <th >Pipe Line</th>
					</tr>
				</thead>
			</table>

			


			<div style="width:2150px; overflow-y:scroll; max-height:200px;font-size:12px; overflow-x:hidden;" id="scroll_body3" align="left">
			<table width="2132" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body3" align="left">
				<tbody>
				<?
				$i=1;

				//var_dump($tem_arr);die;
				$array_check=array();
				foreach($req_result_pi_independ as $row)
				{
					if ($i%2==0)
					$bgcolor="#E9F3FF";
					else
					$bgcolor="#FFFFFF";
					$pipe_pi_qnty=$pipe_line="";
					$mrr_qnty=0;$mrr_value=0;
					$mrr_qnty=$req_wo_recv_arr[$row[csf("pi_id")]][1][$row[csf("color_id")]][$row[csf("yarn_type")]][$row[csf("count_name")]][$row[csf("yarn_composition_item1")]]['recv_qnty'];
					$mrr_value=$req_wo_recv_arr[$row[csf("pi_id")]][1][$row[csf("color_id")]][$row[csf("yarn_type")]][$row[csf("count_name")]][$row[csf("yarn_composition_item1")]]['recv_amt'];
					$pi_id_ref=$req_wo_recv_arr[$row[csf("pi_id")]][1][$row[csf("color_id")]][$row[csf("yarn_type")]][$row[csf("count_name")]][$row[csf("yarn_composition_item1")]]['booking_id'];
					$receive_basis=1;
					
					$receiving_cond=0;
					if($cbo_receive_status==1 && ($mrr_value=="" || $mrr_value==0)) $receiving_cond=1;
					if($cbo_receive_status==2 && $row[csf("amount")]>$mrr_value && $mrr_value>0) $receiving_cond=1;
					if($cbo_receive_status==3 && $mrr_value>=$row[csf("amount")]) $receiving_cond=1;
					if($cbo_receive_status==4 && $mrr_value < $row[csf("amount")]) $receiving_cond=1;
					if($cbo_receive_status==5) $receiving_cond=1;
					
					if($receiving_cond==1)
					{
						?>
	                    <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
	                        <td width="30"><? echo $i; ?></td>
	                        <td width="150"><p><? echo $supplier_arr[$row[csf("pi_suplier")]]; ?></p></td>
	                        <td width="100"><p><? echo $row[csf("pi_number")]; ?></p></td>
	                        <td width="70"><p>&nbsp;
	                        <? 
	                        if($row[csf("pi_date")]!="" && $row[csf("pi_date")]!="0000-00-00") echo change_date_format($row[csf("pi_date")]); 
	                        ?></p></td>
	                        <td width="70"><p>&nbsp;
	                        <? 
	                        if($row[csf("last_shipment_date")]!="" && $row[csf("last_shipment_date")]!="0000-00-00") echo change_date_format($row[csf("last_shipment_date")]); 
	                        ?></p></td>
	                        <td width="80"><p><? echo $color_arr[$row[csf("color_id")]]; ?></p></td>
	                        <td width="50"><p>&nbsp;<? echo $yarnCount_arr[$row[csf("count_name")]]; ?></p></td>
	                        <td width="230"><p>
	                        <? 
	                        if($row[csf("yarn_composition_item2")]>0) $comp_percent2=$row[csf("yarn_composition_percentage2")]."%"; else $comp_percent2="";
	                        echo $composition[$row[csf("yarn_composition_item1")]]." ".$row[csf("yarn_composition_percentage1")]."% ".$composition[$row[csf("yarn_composition_item2")]]; 
	                        ?></p></td>
	                        <td width="80"><p><? echo $yarn_type[$row[csf("yarn_type")]];?></p></td>
	                        <td width="50"><p><? echo $unit_of_measurement[$row[csf("uom")]];?></p></td>
	                        <td width="80" align="right"><p><? echo  number_format($row[csf("quantity")],0); ?> </p></td>
	                        <td width="70" align="right"><p><? echo  number_format($row[csf("rate")],2); ?> </p></td>
	                        <td width="100" align="right"><p><? echo number_format($row[csf("amount")],2); ?> </p></td>
	                        <td width="70"><p><? echo $currency[$row[csf("currency_id")]]; ?></p></td>
	                        <td width="70"><p>&nbsp;
	                        <? 
	                        $btb_lc_date_arr=array_unique(explode(",",chop($btb_data_arr[$row[csf("pi_id")]]["lc_date"]," , ")));
	                        $btb_lc_date='';
	                        foreach ($btb_lc_date_arr as $value) 
	                        {
	                        	if($value !="" && $value!="0000-00-00")
	                        	{
	                        		$btb_lc_date.=change_date_format($value)."</br>";
	                        	}
	                        }
	                        echo chop($btb_lc_date,"</br>");  ?> </p></td>
	                        <td width="100"><p><? $btb_lc_no_arr=array_unique(explode(",",chop($btb_data_arr[$row[csf("pi_id")]]["lc_number"]," , ")));
	                        $btb_lc_no=implode(" , ",$btb_lc_no_arr);
	                        echo $btb_lc_no;  ?></p></td>
	                        <td width="100"><p><? $btb_issue_bank_arr=array_unique(explode(",",chop($btb_data_arr[$row[csf("pi_id")]]["issuing_bank_id"]," , ")));
	                        foreach ($btb_issue_bank_arr as $value) 
	                        {
	                        	$btb_issue_bank.=$bank_arr[$value].",";
	                        }
	                        echo chop($btb_issue_bank,","); ?></p></td>
	                        <td width="70"><p><? $btb_pay_tarm_arr=array_unique(explode(",",chop($btb_data_arr[$row[csf("pi_id")]]["payterm_id"]," , ")));
	                        $btb_pay_term=''; 
	                        foreach ($btb_pay_tarm_arr as $value) 
	                        {
	                        	$btb_pay_term.=$pay_term[$value].",";
	                        }
	                        echo chop($btb_pay_term,","); ?></p></td>
	                        <td width="80"><p><? $btb_tenor_arr=array_unique(explode(",",chop($btb_data_arr[$row[csf("pi_id")]]["tenor"]," , "))); 
	                        $btb_tenor=implode(" Days, ",$btb_tenor_arr);
	                        if ($btb_tenor!='') echo $btb_tenor." Days"; ?></p></td>
	                        <td width="100" align="right"><p><? $btb_lc_amount=$btb_data_arr[$row[csf("pi_id")]]["lc_value"]; echo number_format($btb_lc_amount,2); ?> </p></td>
	                        <td width="80" align="right"><p><a href="##" onclick="fn_mrr_details('<? echo $pi_id_ref;?>','<? echo $receive_basis;?>','<? echo $row[csf("color_id")];?>','<? echo $row[csf("yarn_type")];?>','<? echo $row[csf("count_name")];?>','<? echo $row[csf("yarn_composition_item1")];?>','receive_details_popup')"><? echo number_format($mrr_qnty,2); ?></a></p></td>
	                        <td width="100" align="right"><p><? echo number_format($mrr_value,2);?></p></td>
	                        <td align="right"><p><? $short_amt=$row[csf("amount")]-$mrr_value; echo number_format($short_amt,2); ?></p></td>
	                        <td align="right" title="<?= $pi_pipe_array[$row[csf("pi_id")]][$row[csf("color_id")]][$row[csf("yarn_type")]][$row[csf("count_name")]][$row[csf("yarn_composition_item1")]];?>"><p>
	                        <?
	                        //$pipe_pi_qnty=$pipe_line 
	                        $pipe_pi_qnty=$pi_pipe_array[$row[csf("pi_id")]][$row[csf("color_id")]][$row[csf("yarn_type")]][$row[csf("count_name")]][$row[csf("yarn_composition_item1")]];
	                        $pipe_line=($pipe_pi_qnty-$mrr_qnty);
	                        echo number_format($pipe_line,2); 
	                        ?>
	                        </p></td>
	                    </tr>
	                    <?
	                    $k++;
	                    $i++;
	                    $total_pi_qntty+=$row[csf("quantity")];
	                    $total_pi_amount+=$row[csf("amount")];
	                    $total_mrr_qty+=$mrr_qnty;
	                    $total_mrr_val+=$mrr_value;
					}
				}
				?>
				</tbody>
				<tfoot style="background: #dbdbdb;">
					<td colspan="10" align="right"><strong>Total</strong></td>
					<td align="right"><b><? echo number_format($total_pi_qntty,2);?></b></td>
					<td>&nbsp;</td>
					<td align="right"><b><? echo number_format($total_pi_amount,2);?></b></td>
					<td colspan="8" align="right"><b><? echo number_format($total_mrr_qty,2);?></b></td>
					<td align="right"><b><? echo number_format($total_mrr_val,2);?></b></td>
					<td colspan="2">&nbsp;</td>
				</tfoot>
			</table>
			</div>
			<?
		}
	    ?>
		</div>
	    <?
	}	
	
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
	echo "$total_data####$filename####$cbo_based_on";
	exit();
}

if($action=="report_generate_summary")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	$buyer_arr=return_library_array("select id,buyer_name from  lib_buyer","id","buyer_name");
	$yarnCount_arr=return_library_array( "select id,yarn_count from  lib_yarn_count",'id','yarn_count');
	//("cbo_company_name*cbo_year*cbo_based_on*txt_search_no*txt_date_from*txt_date_to","../../")
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_year=str_replace("'","",$cbo_year);
	$cbo_based_on=str_replace("'","",$cbo_based_on);
	$txt_search_no=str_replace("'","",$txt_search_no);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$cbo_receive_status=str_replace("'","",$cbo_receive_status);
	
	$str_cond="";
	$str_cond.=" and a.requ_prefix_num like '%$txt_search_no%'";
	if($txt_date_from!="" && $txt_date_to!="") $str_cond.=" and a.requisition_date between '$txt_date_from' and '$txt_date_to'";
	if($cbo_year>0)
	{
		if($db_type==0) $year_cond=" and year(a.insert_date)='$cbo_year'"; else $year_cond=" and to_char(a.insert_date,'YYYY')='$cbo_year'";
	}
	if($cbo_based_on==1)
	{
		$sql_req="select a.id, b.buyer_id, b.count_id, b.composition_id, b.yarn_type_id, b.quantity as req_qnty
		from inv_purchase_requisition_mst a,  inv_purchase_requisition_dtls b 
		where a.id=b.mst_id and b.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id='$cbo_company_name' and a.entry_form=70 $str_cond $year_cond 
		order by b.count_id, b.yarn_type_id, b.composition_id";
		//echo $sql_req;die;
		$req_result=sql_select($sql_req);
		$all_buyer=$total_data=$dtls_buyer_data=array();
		foreach($req_result as $row)
		{
			if($row[csf("buyer_id")]) 
			{
				$all_buyer[$row[csf("buyer_id")]]=$row[csf("buyer_id")];
				$buyer_id=$row[csf("buyer_id")];
			}
			else
			{
				$buyer_id=0;
			}
			$total_data[$row[csf("count_id")]][$row[csf("composition_id")]][$row[csf("yarn_type_id")]]+=$row[csf("req_qnty")];
			$dtls_buyer_data[$row[csf("count_id")]][$row[csf("composition_id")]][$row[csf("yarn_type_id")]][$buyer_id]+=$row[csf("req_qnty")];
		}
		//echo "<pre>";print_r($all_buyer);die;
		//echo "<pre>";print_r($dtls_buyer_data);die;
		$table_width=600+(count($all_buyer)*100);
		$div_width=$table_width+20;
		ob_start();
		?>
        <div style="width:<? echo $div_width ?>px" align="left">
		<table width="<? echo $table_width ?>"  align="left">
			<tr>
				<td style="font-size:18; font-weight:bold;" class="form_caption" align="center">Based on Requisition</td>
			</tr>
		</table>
		<br />
		<table width="<? echo $table_width ?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header"  align="left">
			<thead>
				<tr>
                	<th width="30">SL</th>
					<th width="100">Count</th>
					<th width="150">Composition</th>
					<th width="100">Type</th>
					<th width="100">Total</th>
                    <?
					foreach($all_buyer as $buyer_id)
					{
						?>
                        <th width="100" title="<? echo $buyer_id;?>"><? echo $buyer_arr[$buyer_id]; ?></th>
                        <?
					}
					?>
					<th width="100">Block</th>
				</tr>
			</thead>
		</table>
		<div style="width:<? echo $div_width ?>px; overflow-y:scroll; max-height:300px; overflow-x:hidden;" id="scroll_body" align="left">
		<table width="<? echo $table_width ?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left">
			<tbody>
			<?
			
			$i=1;
			foreach($total_data as $count_id=>$count_data)
			{
				foreach($count_data as $comp_id=>$comp_data)
				{
					foreach($comp_data as $type_id=>$val)
					{
						if ($i%2==0)
						$bgcolor="#E9F3FF";
						else
						$bgcolor="#FFFFFF";
						?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        	<td width="30" align="center"><? echo $i; ?></td>
                            <td width="100" title="<? echo "count id=".$count_id;?>"><p><? echo $yarnCount_arr[$count_id]; ?>&nbsp;</p></td>
                            <td width="150" title="<? echo "comp id=".$comp_id;?>"><p><? echo $composition[$comp_id]; ?>&nbsp;</p></td>
                            <td width="100" title="<? echo "type id=".$type_id;?>"><p><? echo $yarn_type[$type_id]; ?>&nbsp;</p></td>
                            <td width="100" align="right"><? echo number_format($val,2); $gr_total+=$val;?></td>
                            <?
							foreach($all_buyer as $buyer_id)
							{
								?>
								<td width="100" align="right" title="<? echo $buyer_id;?>"><? echo number_format($dtls_buyer_data[$count_id][$comp_id][$type_id][$buyer_id],2); ?></td>
                                <?
								$buyer_wise_total[$buyer_id]+=$dtls_buyer_data[$count_id][$comp_id][$type_id][$buyer_id];
							}
							?>
                            <td width="100" align="right"><? echo number_format($dtls_buyer_data[$count_id][$comp_id][$type_id][0],2); $buyer_wise_total[0]+=$dtls_buyer_data[$count_id][$comp_id][$type_id][0]; ?></td>
                        </tr>
                        <?
						$i++;
					}
				}
			}
			?>
			</tbody>
			<tfoot style="background: #dbdbdb;">
				<td colspan="4" align="right"><strong>Total</strong></td>
				<td align="right"><b><? echo number_format($gr_total,2);?></b></td>
                <?
				foreach($all_buyer as $buyer_id)
				{
					?>
					<td align="right"><b><? echo number_format($buyer_wise_total[$buyer_id],2); ?></b></td>
					<?
				}
				?>
				<td align="right"><b><? echo number_format($buyer_wise_total[0],2); ?></b></td>
			</tfoot>
		</table>
		</div>
        </div>
		<?
	}

	//echo "test";die;
	foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$total_data=ob_get_contents();
	ob_clean();
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,$total_data);
	echo "$total_data####$filename####$cbo_based_on";
	die;
}

if($action=="receive_details_popup")
{
	extract($_REQUEST);
 	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
	//echo $color_id."&&".$yarn_type."&&".$count_id."&&".$composition;die;
	?>
	  <script>
		function fnc_yarn_receive_entry(company_id,recieve_number)
		{
		

				print_report( company_id+'*'+recieve_number, "yarn_receive_print", "yarn_procurement_progress_report_controller" )
				return;
		
			
		}


	
</script>
<div style="width:620px;">
    	<table width="600" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_pop">
        	<thead>
            	<tr>
                	<th width="30">SL</th>
                    <th width="120">WO/PI No</th>
                    <th width="130">MRR No.</th>
                    <th width="70">Receive Date</th>
                    <th width="50">UOM</th>
                    <th width="50">Lot</th>
                    <th width="50">Receive Qty</th>
					<th width="100">Remarks</th>
                </tr>
            </thead>
        </table>
        <div style="width:620px; overflow-y:scroll; max-height:200px;font-size:12px; overflow-x:hidden;" id="scroll_body_pop" >
        <table width="600" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body_pop">
        	<tbody>
            <?
			$wo_num_arr=return_library_array( "select id, wo_number from wo_non_order_info_mst",'id','wo_number');
			$pi_num_arr=return_library_array( "select id, pi_number from  com_pi_master_details",'id','pi_number');
			
			$rcv_sql=sql_select("select a.id, a.recv_number, a.receive_basis,a.company_id,a.booking_id, a.receive_date,max(b.cons_uom) as cons_uom, sum(b.cons_quantity) as mrr_qnty,b.remarks, c.lot 
			from inv_receive_master a, inv_transaction b, product_details_master c
			where a.id=b.mst_id and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.receive_basis=$book_basis and a.booking_id=$booking_id and c.color=$color_id and c.yarn_type=$yarn_type and c.yarn_count_id=$count_id and c.yarn_comp_type1st=$composition 
			group by a.id, a.recv_number, a.receive_basis,a.company_id,a.booking_id, a.receive_date, b.remarks, c.lot");
			
			$k=1;$all_rcv_id=array();
			foreach($rcv_sql as $row)
			{
				if ($k%2==0)
				$bgcolor="#E9F3FF";
				else
				$bgcolor="#FFFFFF";
				$all_rcv_id[$row[csf("id")]]=$row[csf("id")];
				?>
            	<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
                	<td width="30" align="center"><? echo $k; ?></td>
                    <?
					$wo_pi_no="";
					if($row[csf("receive_basis")]==1)
					{
						$wo_pi_no=$pi_num_arr[$row[csf("booking_id")]];
					}
					else
					{
						$wo_pi_no=$wo_num_arr[$row[csf("booking_id")]];
					}

					
					?>
                    <td align="center" width="120"><p><? echo $wo_pi_no; ?></p></td>
                     <!-- <td align="center" width="130"><p><?// echo// $row[csf("recv_number")]; ?></p></td> -->
					<td align="center" width="130"><p><a href="##" onclick="fnc_yarn_receive_entry(<?php echo $row[csf('company_id')]?>,'<?echo $row[csf('recv_number')]; ?>')">
                         <?echo $row[csf("recv_number")]; ?></a></p></td>
                    <td align="center" width="70"><p>&nbsp;<? if($row[csf("receive_date")]!="" && $row[csf("receive_date")]!="0000-00-00") echo change_date_format($row[csf("receive_date")]); ?></p></td>
                    <td  align="center" width="50"><p><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></p></td>
                    <td align="center" width="50"><p><? echo $row[csf("lot")] ?></p></td>
                    <td align="right" width="50"><p><? echo number_format($row[csf("mrr_qnty")],2); $total_mrr_qnty+=$row[csf("mrr_qnty")]; ?></p></td>
					<td align="center" width="100"><p><? echo substr($row[csf("remarks")],0,30) ?> </p></td>
					<td><input type="hidden" id="" name="<? echo $row[csf("company_id")] ?>"></td>
                </tr>
                <?
				$k++;
			}
			unset($rcv_sql);
			
			//==============>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> for PI from WO
                     
			?>
            </tbody>
            <tfoot>
            	<tr>
                	<th></th>
                    <th></th>
                    <th></th>
					<th></th>
                    <th></th>
                    <th>Total</th>
                    <th><? echo number_format($total_mrr_qnty,2); ?></th>
					<th></th>
                </tr>
            </tfoot>
        </table>
        </div>
        <br/>
        <?
			$all_rcv_ids=implode(",",$all_rcv_id);
			if($all_rcv_ids=="") $all_rcv_ids=0;
			$rcv_return_sql=sql_select("select a.issue_number, a.issue_date, b.cons_uom, sum(b.cons_quantity) as qnty, sum(b.cons_amount) as amt , c.lot
			from inv_issue_master a, inv_transaction b, product_details_master c 
			where a.id=b.mst_id and b.prod_id=c.id and a.entry_form=8 and b.transaction_type=3 and b.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.received_id in($all_rcv_ids) and c.color=$color_id and c.yarn_type=$yarn_type and c.yarn_count_id=$count_id and c.yarn_comp_type1st=$composition 
			group by a.issue_number, a.issue_date, b.cons_uom, c.lot");
			?>
            <table width="600" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_pop">
                <thead>
                    <tr>
                        <th width="30">SL</th>
                        <th width="130">MRR No.</th>
                        <th width="130">Return Date</th>
                        <th width="80">UOM</th>
                         <th width="50">Lot</th>
                        <th>Return Qty</th>
						<th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                    </tr>
                </thead>
                <tbody>
					<?
					$i=1;
					foreach($rcv_return_sql as $row)
					{
						if ($k%2==0)
						$bgcolor="#E9F3FF";
						else
						$bgcolor="#FFFFFF";
						?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
                            <td align="center"><? echo $i;?></td>
                            <td><p><? echo $row[csf("issue_number")]; ?></p></td>
                            <td align="center"><p><? echo change_date_format($row[csf("issue_date")]); ?></p></td>
                            <td align="center"><p><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></p></td>
                            <td align="center" width="50"><p><? echo $row[csf("lot")] ?></p></td>
                            <td align="right"><? echo number_format($row[csf("qnty")],2); $total_rtn+=$row[csf("qnty")]; ?></td>
                        </tr>
                        <?
						$i++;$k++;
					}
                    ?>
                	
                </tbody>
                <tfoot>
                    <tr>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th>Total Rtn</th>
                        <th><? echo number_format($total_rtn,2); ?></th>
						<th></th>
						
                    </tr>
                    <tr>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th>Balance</th>
                        <th><? $balance_qnty=($total_mrr_qnty-$total_rtn); echo number_format($balance_qnty,2); ?></th>
						<th></th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <script>setFilterGrid("table_body_pop",-1);</script>
    <?
	exit();
}

if($action=="receive_details_popup_analysis")
{
	extract($_REQUEST);
 	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
	//echo $color_id."&&".$yarn_type."&&".$count_id."&&".$composition;die;
	?>
	

			<div style="width:620px;">
    	<table width="600" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_pop">
        	<thead>
            	<tr>
                	<th width="30">SL</th>
                    <th width="120">WO/PI No</th>
                    <th width="130">MRR No.</th>
                    <th width="70">Receive Date</th>
                    <th width="50">UOM</th>
                    <th width="50">Yarn Lot</th>
                    <th width="50">Receive Qty</th>
					<th width="100">Remarks</th>
                </tr>
            </thead>
        </table>
        <div style="width:620px; overflow-y:scroll; max-height:200px;font-size:12px; overflow-x:hidden;" id="scroll_body_pop" >
        <table width="600" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body_pop">
        	<tbody>
            <?
			$wo_num_arr=return_library_array( "select id, wo_number from wo_non_order_info_mst",'id','wo_number');
			$pi_num_arr=return_library_array( "select id, pi_number from  com_pi_master_details",'id','pi_number');
			
			$rcv_sql=sql_select("select a.id, a.recv_number, a.receive_basis, a.booking_id, a.receive_date,max(b.cons_uom) as cons_uom, sum(b.cons_quantity) as mrr_qnty,b.remarks, c.lot 
			from inv_receive_master a, inv_transaction b, product_details_master c
			where a.id=b.mst_id and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.receive_basis=$book_basis and b.pi_wo_req_dtls_id=$booking_id and c.color=$color_id and c.yarn_type=$yarn_type and c.yarn_count_id=$count_id and c.yarn_comp_type1st=$composition 
			group by a.id, a.recv_number, a.receive_basis, a.booking_id, a.receive_date, b.remarks, c.lot");
			
			$k=1;$all_rcv_id=array();
			foreach($rcv_sql as $row)
			{
				if ($k%2==0)
				$bgcolor="#E9F3FF";
				else
				$bgcolor="#FFFFFF";
				$all_rcv_id[$row[csf("id")]]=$row[csf("id")];
				?>
            	<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
                	<td width="30" align="center"><? echo $k; ?></td>
                    <?
					$wo_pi_no="";
					if($row[csf("receive_basis")]==1)
					{
						$wo_pi_no=$pi_num_arr[$row[csf("booking_id")]];
					}
					else
					{
						$wo_pi_no=$wo_num_arr[$row[csf("booking_id")]];
					}

					
					?>
                    <td align="center" width="120"><p><? echo $wo_pi_no; ?></p></td>
                    <td align="center" width="130"><p><? echo $row[csf("recv_number")]; ?></p></td>
                    <td align="center" width="70"><p>&nbsp;<? if($row[csf("receive_date")]!="" && $row[csf("receive_date")]!="0000-00-00") echo change_date_format($row[csf("receive_date")]); ?></p></td>
                    <td  align="center" width="50"><p><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></p></td>
                    <td align="center" width="50"><p><? echo $row[csf("lot")] ?></p></td>
                    <td align="right" width="50"><p><? echo number_format($row[csf("mrr_qnty")],2); $total_mrr_qnty+=$row[csf("mrr_qnty")]; ?></p></td>
					<td align="center" width="100"><p><? echo substr($row[csf("remarks")],0,30) ?> </p></td>
                </tr>
                <?
				$k++;
			}
			unset($rcv_sql);
			
			//==============>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> for PI from WO
                     
			?>
            </tbody>
            <tfoot>
            	<tr>
                	<th></th>
                    <th></th>
                    <th></th>
					<th></th>
                    <th></th>
                    <th>Total</th>
                    <th><? echo number_format($total_mrr_qnty,2); ?></th>
					<th></th>
                </tr>
            </tfoot>
        </table>
        </div>
        <br/>
        <?
			$all_rcv_ids=implode(",",$all_rcv_id);
			if($all_rcv_ids=="") $all_rcv_ids=0;
			$rcv_return_sql=sql_select("select a.issue_number, a.issue_date, b.cons_uom, sum(b.cons_quantity) as qnty, sum(b.cons_amount) as amt 
			from inv_issue_master a, inv_transaction b, product_details_master c 
			where a.id=b.mst_id and b.prod_id=c.id and a.entry_form=8 and b.transaction_type=3 and b.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.received_id in($all_rcv_ids) and c.color=$color_id and c.yarn_type=$yarn_type and c.yarn_count_id=$count_id and c.yarn_comp_type1st=$composition 
			group by a.issue_number, a.issue_date, b.cons_uom");
			?>
            <table width="600" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_pop">
                <thead>
                    <tr>
                        <th width="30">SL</th>
                        <th width="130">MRR No.</th>
                        <th width="130">Return Date</th>
                        <th width="80">UOM</th>
                        <th width="50">Yarn Lot</th>
                        <th>Return Qty</th>
						<th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                    </tr>
                </thead>
                <tbody>
					<?
					$i=1;
					foreach($rcv_return_sql as $row)
					{
						if ($k%2==0)
						$bgcolor="#E9F3FF";
						else
						$bgcolor="#FFFFFF";
						?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
                            <td align="center"><? echo $i;?></td>
                            <td><p><? echo $row[csf("issue_number")]; ?></p></td>
                            <td align="center"><p><? echo change_date_format($row[csf("issue_date")]); ?></p></td>
                            <td align="center"><p><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></p></td>
                            <td align="center" width="50"><p><? echo $row[csf("lot")] ?></p></td>
                            <td align="right"><? echo number_format($row[csf("qnty")],2); $total_rtn+=$row[csf("qnty")]; ?></td>
                        </tr>
                        <?
						$i++;$k++;
					}
                    ?>
                	
                </tbody>
                <tfoot>
                    <tr>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th>Total Rtn</th>
                        <th><? echo number_format($total_rtn,2); ?></th>
						<th></th>
						
                    </tr>
                    <tr>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th>Balance</th>
                        <th><? $balance_qnty=($total_mrr_qnty-$total_rtn); echo number_format($balance_qnty,2); ?></th>
						<th></th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <script>setFilterGrid("table_body_pop",-1);</script>
    <?
	exit();
}



if($action=="issue_details_popup")
{
	extract($_REQUEST);
 	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
	//echo $color_id."&&".$yarn_type."&&".$count_id."&&".$composition;die;
	?>
    	<div style="width:620px;">
    	<table width="600" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_pop">
        	<thead>
            	<tr>
                	<th width="30">SL</th>
                    <th width="130">Issue No.</th>
                    <th width="70">Issue Date</th>
					<th width="50">Yarn Lot</th>
                    <th width="50">UOM</th>
                    <th width="50">Issue Qty</th>
                </tr>
            </thead>
        </table>
        <div style="width:620px; overflow-y:scroll; max-height:200px;font-size:12px; overflow-x:hidden;" id="scroll_body_pop" >
        <table width="600" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body_pop">
        	<tbody>
            <?
			$wo_num_arr=return_library_array( "select id, wo_number from wo_non_order_info_mst",'id','wo_number');
			$pi_num_arr=return_library_array( "select id, pi_number from  com_pi_master_details",'id','pi_number');
			
			$issue_sql="SELECT a.id, a.issue_number, a.issue_date,b.prod_id,c.lot, max(b.cons_uom) as cons_uom, sum(b.cons_quantity) as issue_qnty
		from  inv_issue_master a, inv_transaction b, product_details_master c 
		where  a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=2 and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and a.issue_basis=$book_basis and b.prod_id=$booking_id and c.color=$color_id  and c.yarn_type=$yarn_type and c.yarn_count_id=$count_id and c.yarn_comp_type1st=$composition and b.status_active=1 and b.is_deleted=0 and a.entry_form=3
		group by a.id, a.issue_number, a.issue_date, b.prod_id, c.lot";
		//echo $issue_sql;die;
		$issue_sql_arr=sql_select($issue_sql);

			
			$k=1;$all_issue_id=array();$all_prod_id=array();$all_lot_id=array();
			foreach($issue_sql_arr as $row)
			{
				if ($k%2==0)
				$bgcolor="#E9F3FF";
				else
				$bgcolor="#FFFFFF";
				$all_issue_id[$row[csf("id")]]=$row[csf("id")];
				$all_prod_id[$row[csf("prod_id")]]=$row[csf("prod_id")];
				$all_lot_id[$row[csf("lot")]]="'".$row[csf("lot")]."'";
				?>
            	<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
                	<td width="30" align="center"><? echo $k; ?></td>
                    <?
				
					?>
                    
                    <td align="center" width="130"><p><? echo $row[csf("issue_number")]; ?></p></td>
                    <td align="center" width="70"><p>&nbsp;<? if($row[csf("issue_date")]!="" && $row[csf("issue_date")]!="0000-00-00") echo change_date_format($row[csf("issue_date")]); ?></p></td>
					<td align="center" width="50"><p><? echo $row[csf("lot")] ?></p></td>
                    <td  align="center" width="50"><p><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></p></td>
                    <td align="right" width="50"><p><? echo number_format($row[csf("issue_qnty")],2); $total_issue_qnty+=$row[csf("issue_qnty")]; ?></p></td>
                </tr>
                <?
				$k++;
			}
			unset($rcv_sql);
			
			//==============>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> for PI from WO
                     
			?>
            </tbody>
            <tfoot>
            	<tr>
                	
                    <th></th>
                    <th></th>
					<th></th>
                    <th></th>
                    <th>Total Issue  </th>
                    <th><? echo number_format($total_issue_qnty,2); ?></th>
				
                </tr>
            </tfoot>
        </table>
        </div>
        <br/>
        <?
			$all_issue_ids=implode(",",$all_issue_id);
			if($all_issue_ids=="") $all_issue_ids=0;

			$all_prod_ids=implode(",",$all_prod_id);
			if($all_prod_ids=="") $all_prod_ids=0;

			$all_lot_ids=implode(",",$all_lot_id);
			if($all_lot_ids=="") $all_lot_ids=0;

			$issue_return_sql="SELECT a.recv_number, a.issue_id,b.prod_id, b.cons_amount,a.receive_date, max(b.cons_uom) as cons_uom,sum(b.cons_quantity) as issue_rtn_qnty,c.lot from inv_receive_master a, inv_transaction b, product_details_master c where a.id=b.mst_id and a.entry_form=9 and b.transaction_type=4 and b.item_category=1 and a.status_active=1 and a.is_deleted=0 and a.issue_id in($all_issue_ids) and b.prod_id in($all_prod_ids) and c.lot in($all_lot_ids) and c.color=$color_id and c.yarn_type=$yarn_type and c.yarn_count_id=$count_id and c.yarn_comp_type1st=$composition and b.status_active=1 and b.is_deleted=0 group by a.recv_number, a.issue_id,b.prod_id, b.cons_quantity, b.cons_amount,a.receive_date, c.lot";
			//echo $issue_return_sql;die;
			$issue_return_sql_arr = sql_select($issue_return_sql);
		
			?>
            <table width="600" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_pop">
                <thead>
                    <tr>
                        <th width="30">SL</th>
                        <th width="130">Issue Return No.</th>
                        <th width="70">Return Date</th>
						<th width="50">Yarn Lot</th>
                        <th width="50">UOM</th>
                        <th width="50">Return Qty</th>
                    </tr>
                </thead>
                <tbody>
					<?
					$i=1;
					foreach($issue_return_sql_arr as $row)
					{
						if ($k%2==0)
						$bgcolor="#E9F3FF";
						else
						$bgcolor="#FFFFFF";

						?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
                            <td align="center"><? echo $i;?></td>
                            <td align="center"><p><? echo $row[csf("recv_number")]; ?></p></td>
                            <td align="center"><p><? echo change_date_format($row[csf("receive_date")]); ?></p></td>
							<td align="center"><p><? echo $row[csf("lot")]; ?></p></td>
                            <td align="center"><p><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></p></td>
                            <td align="right"><? echo number_format($row[csf("issue_rtn_qnty")],2); $total_rtn+=$row[csf("issue_rtn_qnty")]; ?></td>
                        </tr>
                        <?
						$i++;$k++;
					}
                    ?>
                	
                </tbody>
                <tfoot>
                    <tr>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th>Total Rtn  </th>
                        <th><? echo number_format($total_rtn,2); ?></th>
						
						
                    </tr>
                    <tr>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th>Balance</th>
                        <th><? $balance_qnty=($total_issue_qnty-$total_rtn); echo number_format($balance_qnty,2); ?></th>
					
                    </tr>
                </tfoot>
            </table>
        </div>
        <script>setFilterGrid("table_body_pop",-1);</script>
    <?
	exit();
}

if ($action == "yarn_receive_print")
{
	extract($_REQUEST);
	$data = explode('*', $data);
	//print_r ($data);

	$sql = " select id, recv_number,supplier_id,currency_id,challan_no, receive_date, exchange_rate, store_id, receive_basis,lc_no,receive_purpose,booking_id from inv_receive_master where recv_number='$data[1]'";


	$dataArray = sql_select($sql);
	$receive_pur = $dataArray[0][csf("receive_purpose")];
	$receive_basis = $dataArray[0][csf("receive_basis")];

	$wo_id = $dataArray[0][csf("booking_id")];

	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$location = return_field_value("location_name", "lib_location", "company_id=$data[0]");
	$address = return_field_value("address", "lib_location", "company_id=$data[0]");
	$supplier_library = return_library_array("select id,supplier_name from  lib_supplier", "id", "supplier_name");
	$store_library = return_library_array("select id, store_name from  lib_store_location", "id", "store_name");
	$yarn_desc_arr = return_library_array("select id,yarn_description from lib_subcon_charge", 'id', 'yarn_description');
	$const_comp_arr = return_library_array("select id,const_comp from lib_subcon_charge", 'id', 'const_comp');
	$lcNum = return_library_array("select id,lc_number from com_btb_lc_master_details", 'id', 'lc_number');
	$country_arr = return_library_array("select id,country_name from lib_country", 'id', 'country_name');
	$user_id 		= return_library_array("select id,user_name from user_passwd", "id", "user_name");
	$user_name 		= return_library_array("select id,user_full_name from user_passwd", "id", "user_full_name");

	$wo_service_type = array(2,7,12,15,38,46,50,51);
	$exchange_currency = 0;
	if( in_array($receive_pur, $wo_service_type) )
	{
		$table_width = 1415;
		$exchange_currency = return_field_value("ecchange_rate", "wo_yarn_dyeing_mst", "id=$wo_id", "ecchange_rate");
	} 
	else 
	{
		$table_width = 930;
	}

	if ( $receive_basis == 2 && ($receive_pur == 2 || $receive_pur == 12 || $receive_pur == 15 || $receive_pur == 38 || $receive_pur == 46 || $receive_pur == 50 || $receive_pur == 51) )  
	{

		$pay_mode = return_field_value("pay_mode", "wo_yarn_dyeing_mst", "id=$wo_id", "pay_mode");

		if($pay_mode==3 || $pay_mode==5)
		{
			$supplier_name = $company_library[$dataArray[0][csf('supplier_id')]];
		}else{
			$supplier_name = $supplier_library[$dataArray[0][csf('supplier_id')]];
		}
	}else{
		$supplier_name = $supplier_library[$dataArray[0][csf('supplier_id')]];
	}

	?>
	<div id="table_row" style="width:<? echo $table_width; ?>px;">
		<table width="<? echo $table_width; ?>" align="right">
			<tr class="form_caption">
				<td colspan="6" align="center" style="font-size:20px">
					<strong><? echo $company_library[$data[0]]; ?></strong></td>
				</tr>
				<tr class="form_caption">
					<td colspan="6" align="center" style="font-size:14px">
						<?
						$nameArray = sql_select("select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
						foreach ($nameArray as $result) {
							?>
							<? echo $result[csf('plot_no')]; ?>
							<? echo $result[csf('level_no')] ?>
							<? echo $result[csf('road_no')]; ?>
							<? echo $result[csf('block_no')]; ?>
							<? echo $result[csf('city')]; ?>
							<? echo $result[csf('zip_code')]; ?>
							<?php echo $result[csf('province')]; ?>
							<? echo $country_arr[$result[csf('country_id')]]; ?><br>
							<? echo $result[csf('email')]; ?>
							<? echo $result[csf('website')];
						}
						?>
					</td>
				</tr>
				<tr>
					<td colspan="6" align="center" style="font-size:16px"><strong><u>Material Receive
					Report</u></strong></center></td>
				</tr>
				<tr style="font-size:14px">
					<td width="120"><strong>Supplier Name:</strong></td>
					<td width="210px"><? echo $supplier_name; ?></td>
					<td width="110"><strong>MRR No:</strong></td>
					<td width="200px"><? echo $dataArray[0][csf('recv_number')]; ?></td>
					<td width="115"><strong>Currency:</strong></td>
					<td><? echo $currency[$dataArray[0][csf('currency_id')]]; ?></td>
				</tr>
				<tr style="font-size:14px">
					<td><strong>Challan No:</strong></td>
					<td><? echo $dataArray[0][csf('challan_no')]; ?></td>
					<td><strong>Receive Date:</strong></td>
					<td><? echo change_date_format($dataArray[0][csf('receive_date')]); ?></td>
					<? if ($exchange_currency > 0) {
						?>
						<td><strong>WO Exc. Rate:</strong></td>
						<td><? echo $exchange_currency; ?></td>
						<?
					} else {
						?>
						<td><strong>Exchange Rate:</strong></td>
						<td><? echo $dataArray[0][csf('exchange_rate')]; ?></td>
						<?
					}
					?>
				</tr>
				<tr
				=style="font-size:14px">
				<td><strong>Store Name:</strong></td>
				<td><? echo $store_library[$dataArray[0][csf('store_id')]]; ?></td>
				<td><strong>Receive Basis:</strong></td>
				<td><? echo $receive_basis_arr[$dataArray[0][csf('receive_basis')]]; ?></td>
				<?
				if ($dataArray[0][csf('receive_basis')] == 1) {
					?>
					<td><strong>LC NO:</strong></td>
					<td><? echo $lcNum[$dataArray[0][csf('lc_no')]]; ?></td>
					<?
				}
				if ($receive_pur == 2) $rate_text = "Avg. Rate BDT"; else $rate_text = "Rate";
				?>

			</tr>
		</table>
		<br>
		<div style="width:100%;">
			<table align="right" cellspacing="0" width="<? echo $table_width; ?>" border="1" rules="all"
				class="rpt_table">
				<thead bgcolor="#dddddd" align="center" style="font-size:12px">
					<tr>
						<th rowspan="2" width="30">SL</th>
						<th rowspan="2" width="100">WO/PI No</th>
						<th rowspan="2" width="100">Buyer</th>
						<th rowspan="2" width="140">Item Details</th>
						<?
						if ( $receive_basis == 2 && $receive_pur == 2) {
							?>
							<th rowspan="2" width="100">Color Range</th>
							<?
						}
						?>
						<th rowspan="2" width="60">Yarn Lot</th>
						<th rowspan="2" width="40">UOM</th>
						<th rowspan="2" width="60">Receive Qty</th>
						<th rowspan="2" width="50"><? echo $rate_text; ?></th>
						<?
						if( in_array($receive_pur, $wo_service_type) )
						{
							?>
							<th rowspan="2"  width="60">Avg. Rate Currency</th>
							<th rowspan="2"  width="60">Grey Rate BDT</th>
							<th rowspan="2"  width="60">Grey Rate Currency</th>
							<th rowspan="2"  width="60">Dye. Charge BDT</th>
							<th rowspan="2"  width="60">Dye. Charge Currency</th>
							<?
						}
						?>
						<th rowspan="2" width="60">ILE Cost</th>
						<?
						if ($dataArray[0][csf('currency_id')] != 1) {
							?>
							<th colspan="2" width="120">Amount(<? echo $currency[$dataArray[0][csf('currency_id')]]; ?>)</th>
							<?
						}
						?>
						<th colspan="2" width="150">Amount(BDT)</th>
						<?
						if( in_array($receive_pur, $wo_service_type) )
						{
							?>
							<th rowspan="2" width="80">Amount Currency</th>
							<?
						}
						?>
						<th rowspan="2" width="50">No. Of Bag</th>
						<th rowspan="2" width="50">No. Cons Per Bag</th>
						<th rowspan="2" width="50">No. Of Loose Cone</th>
						<th rowspan="2">Remarks</th>
					</tr>
					<tr>
						<?
						if ($dataArray[0][csf('currency_id')] != 1) {
							?>
							<th width="50">With ILE</th>
							<th width="50">Without ILE</th>
						<? } ?>
						<th width="50">With ILE</th>
						<th width="50">Without ILE</th>
					</tr>
				</thead>
				<?
				if ($db_type == 0) $wo_no_cond = " group_concat(b.work_order_no)"; else if ($db_type == 2) $wo_no_cond = "LISTAGG(b.work_order_no, ',') WITHIN GROUP (ORDER BY b.work_order_no)";
				$pi_arr = array();
				$pi_sql = "select a.id, a.pi_number, a.pi_basis_id, $wo_no_cond as work_order_no from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.item_category_id=1 group by a.id, a.pi_number, a.pi_basis_id";
				$pi_sql_res = sql_select($pi_sql);
				foreach ($pi_sql_res as $row) {
					if ($row[csf('pi_basis_id')] == 1) $wowoderno = implode(',', array_unique(explode(',', $row[csf('work_order_no')])));
					else if ($row[csf('pi_basis_id')] == 2) $wowoderno = "Independent"; else $wowoderno = "";
					$pi_arr[$row[csf('id')]]['pi_number'] = $row[csf('pi_number')];
					$pi_arr[$row[csf('id')]]['work_order'] = $wowoderno;
				}

				$wo_library = return_library_array("select id,wo_number from wo_non_order_info_mst where entry_form=144", "id", "wo_number");

				//$wo_yrn_library = return_library_array("select id, ydw_no from wo_yarn_dyeing_mst", "id", "ydw_no");

				if($wo_id!="")
				{	
					$wo_yarn_sql = "select a.id,a.ydw_no,b.count,b.yarn_color, b.color_range from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b where a.id=b.mst_id and a.id=$wo_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";

					$wo_yarn_data = sql_select($wo_yarn_sql);
					$wo_yarn_data_array = array();
					foreach ($wo_yarn_data as $row) {
						 $wo_yarn_data_array[$row[csf('id')]][$row[csf('count')]][$row[csf('yarn_color')]]['color_range'] = $row[csf('color_range')];
						 $wo_yrn_library[$row[csf('id')]] = $row[csf('ydw_no')];
					}

				}
				
				$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
				$cond = "";
				if ($data[1] != "") $cond .= " and a.recv_number='$data[1]'";

				$i = 1;
				$sql_result = sql_select("select a.recv_number, a.receive_basis, a.receive_purpose, b.id, b.receive_basis, b.pi_wo_batch_no, b.cone_per_bag, c.product_name_details, c.lot,c.yarn_count_id,c.color, b.order_uom, b.order_qnty, b.order_rate, b.cons_avg_rate, b.dye_charge, b.order_ile_cost, b.order_amount, b.cons_amount, b.no_of_bags,b.no_loose_cone, b.remarks,b.buyer_id,b.booking_no,a.audit_by, a.audit_date, a.is_audited
					from inv_receive_master a, inv_transaction b,  product_details_master c
					where a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=1 and b.item_category=1 and a.entry_form=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $cond");
				$total_amt_currency = 0;
				foreach ($sql_result as $row) {
					if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
					
					//order amount calculation here
					$qty = number_format($row[csf("order_qnty")], 2, '.', '')*1;
					$rate = $row[csf("order_rate")]*1;
					$ileCost = $row[csf("order_ile_cost")]*1;
					$row[csf("order_amount")] = $qty*($rate+$ileCost);
					//end
					
					$order_qnty_val_sum += $row[csf('order_qnty')];
					$order_amount_val_sum += $row[csf('order_amount')];
					$order_amount_val_without_ile_sum += $row[csf('order_amount')]-($row[csf('order_qnty')]*$row[csf('order_ile_cost')]);
					$no_of_bags_val_sum += $row[csf('no_of_bags')];
					$con_per_bags_sum += $row[csf('cone_per_bag')];
					$no_of_loose_cone += $row[csf('no_loose_cone')];

					if ($row[csf("receive_basis")] == 1)
						$receive_basis_cond = $pi_arr[$row[csf('pi_wo_batch_no')]]['pi_number'] . '<br><i>' . $pi_arr[$row[csf('pi_wo_batch_no')]]['work_order'] . '</i>';
					else if ($row[csf("receive_basis")] == 2 && ($row[csf("receive_purpose")] == 2 || $row[csf("receive_purpose")] == 7 || $row[csf("receive_purpose")] == 12 || $row[csf("receive_purpose")] == 15 || $row[csf("receive_purpose")] == 38 || $row[csf("receive_purpose")] == 46 || $row[csf("receive_purpose")] == 50 || $row[csf("receive_purpose")] == 51))
						$receive_basis_cond = $wo_yrn_library[$row[csf('pi_wo_batch_no')]];
					else if($row[csf("receive_basis")] == 14)
					{
						$receive_basis_cond = $row[csf('booking_no')];
					}
					else
						$receive_basis_cond = $wo_library[$row[csf('pi_wo_batch_no')]];

					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="font-size:12px">
						<td><? echo $i; ?></td>
						<td>
							<div style="word-wrap:break-word; width:80px"><? echo $receive_basis_cond; ?></div>
						</td>
						<td>
							<div style="word-wrap:break-word; width:80px"><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></div>
						</td>
						<td>
							<div style="word-wrap:break-word; width:140px"><? echo $row[csf('product_name_details')]; ?></div>
						</td>

						<?
						if ($receive_basis == 2 && $receive_pur == 2) {
							$color_range_id = $wo_yarn_data_array[$row[csf('pi_wo_batch_no')]][$row[csf('yarn_count_id')]][$row[csf('color')]]['color_range'];
							?>
							<td style="word-wrap:break-word; width:140px"><? echo $color_range[$color_range_id]; ?></td>
							<?
						}
						?>	

						<td><? echo $row[csf('lot')]; ?></td>
						<td><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?></td>
						<td align="right"><? echo number_format($row[csf('order_qnty')], 2, '.', ',');?></td>
						<td align="right"><? echo number_format($row[csf('order_rate')], 2, '.', ','); ?></td>
						<?
						if( in_array($receive_pur, $wo_service_type) )
						{
							?>
							<td align="right"><? echo number_format(($row[csf('order_rate')] / $exchange_currency), 4, '.', ','); ?></td>
							<td align="right"><? echo number_format($row[csf('cons_avg_rate')], 2, '.', ','); ?></td>
							<td align="right"><? echo number_format(($row[csf('cons_avg_rate')] / $exchange_currency), 2, '.', ','); ?></td>
							<td align="right"><? echo number_format(($row[csf('dye_charge')]), 2, '.', ','); ?></td>
							<td align="right"><? echo number_format(($row[csf('dye_charge')] / $exchange_currency), 2, '.', ','); ?></td>
							<?
						}
						?>
						<td align="right"><? echo $row[csf('order_ile_cost')]; ?></td>
						<? if ($dataArray[0][csf('currency_id')] != 1) {
							?>
							<td align="right">
								<? echo number_format(($row[csf('order_amount')]), 2, '.', ',');
								$total_amt_currency += ($row[csf('order_amount')]); ?>
							</td>
							<td align="right">
								<? echo number_format(($row[csf('order_amount')] - ($row[csf('order_qnty')]*$row[csf('order_ile_cost')])), 2, '.', ',');
								$total_amt_currency += ($row[csf('order_amount')] - ($row[csf('order_qnty')]*$row[csf('order_ile_cost')])); ?>
							</td>
						<? }
						?>
						<td align="right">
							<? echo number_format(($row[csf('order_amount')] * $dataArray[0][csf('exchange_rate')]), 2, '.', ',');
							$total_bdt_amt_currency += ($row[csf('order_amount')] * $dataArray[0][csf('exchange_rate')]); ?>
						</td>
						<td align="right">
							<? echo number_format((($row[csf('order_amount')] - ($row[csf('order_qnty')]*$row[csf('order_ile_cost')])) * $dataArray[0][csf('exchange_rate')]), 2, '.', ',');
							$total_bdt_amt_currency_without_ile += (($row[csf('order_amount')] - ($row[csf('order_qnty')]*$row[csf('order_ile_cost')])) * $dataArray[0][csf('exchange_rate')]); ?>
						</td>

						<?
						if( in_array($receive_pur, $wo_service_type) )
						{
							?>
							<td align="right"><? echo number_format(($row[csf('order_amount')] / $exchange_currency), 2, '.', ',');
							$total_amt_currency += ($row[csf('order_amount')] / $exchange_currency); ?></td>

							<?
						}
						?>
						<td align="right"><? echo $row[csf('no_of_bags')]; ?></td>
						<td align="right"><? echo $row[csf('cone_per_bag')]; ?></td>
						<td align="right"><? echo $row[csf('no_loose_cone')]; ?></td>
						<td><? echo $row[csf('remarks')]; ?></td>
					</tr>
					<?php
					$i++;
				}
				?>
				<tr>
					<td align="right" colspan="<? echo $colspan = ($receive_basis == 2 && $receive_pur == 2)?7:6; ?>">Total :</td>
					<td align="right"><? echo number_format($order_qnty_val_sum, 2, '.', ','); ?></td>
					<td align="right">&nbsp;</td>
					<?
					if( in_array($receive_pur, $wo_service_type) )
					{
						?>
						<td align="right">&nbsp;</td>
						<td align="right">&nbsp;</td>
						<td align="right">&nbsp;</td>
						<td align="right">&nbsp;</td>
						<td align="right">&nbsp;</td>
						<?
					}
					
					if ( $dataArray[0][csf('currency_id')] == 1 ||  in_array($receive_pur, $wo_service_type) ) 
					{
						$colspan = 2;
					} else {
						$colspan = 1;
					}

					if ($dataArray[0][csf('currency_id')] != 1) {
						?>
						<td align="right" colspan="2"><? echo number_format($order_amount_val_sum, 2, '.', ','); ?></td>
						<td align="right"><? echo number_format($order_amount_val_without_ile_sum, 2, '.', ','); ?></td>
						<?
					}
					?>
					<td align="right" colspan="<? echo $colspan ?>"><? echo number_format($total_bdt_amt_currency, 2, '.', ','); ?></td>
					<td align="right"><? echo number_format($total_bdt_amt_currency_without_ile, 2, '.', ','); ?></td>
					<?
					if( in_array($receive_pur, $wo_service_type) )
					{
						?>
						<td align="right"><? //echo number_format($total_amt_currency,2,'.',',') ?></td>
						<?
					}
					?>
					<td align="right"><? echo $no_of_bags_val_sum; ?></td>
					<td align="right"><? echo $con_per_bags_sum; ?></td>
					<td align="right"><? echo $no_of_loose_cone; ?></td>
					<td align="right">&nbsp;</td>
				</tr>
				
			</table>
			<table>
				<tr>
					<?php

					if($sql_result[0][csf("is_audited")]==1){
						?>
						<td><? echo 'Audited By &nbsp;'.$user_name[$sql_result[0][csf("audit_by")]].'&nbsp;'.$sql_result[0][csf("audit_date")]; ?></td>
					<?php
					}
					?>
					
					
				</tr>
			</table>
			<br>
			<?
			echo signature_table(65, $data[0], $table_width . "px");
			?>
		</div>
	</div>
	<?
	exit();
}


	disconnect($con);
?>


 