<?
header('Content-type:text/html; charset=utf-8');
session_start();
require_once('../../../../includes/common.php');
//require_once('../../../../includes/class4/class.conditions.php');
//require_once('../../../../includes/class4/class.reports.php');
//require_once('../../../../includes/class4/class.trims.php');


$user_id=$_SESSION['logic_erp']['user_id'];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
//---------------------------------------------------- Start---------------------------------------------------------------------------
$color_library=return_library_array("select id,color_name from lib_color", "id", "color_name");
$company_library=return_library_array("select id,company_name from lib_company", "id", "company_name");
$buyer_arr=return_library_array("select id, short_name from lib_buyer",'id','short_name');
$trim_group= return_library_array("select id, item_name from lib_item_group where item_category=4",'id','item_name');


include ("../../../../ext_resource/excel/excel/vendor/autoload.php");
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;


/*$userCredential = sql_select("SELECT unit_id as company_id, store_location_id, item_cate_id, supplier_id FROM user_passwd where id=$user_id");
//$company_id = $userCredential[0][csf('company_id')];
//$supplier_id = $userCredential[0][csf('supplier_id')];
$store_location_id = $userCredential[0][csf('store_location_id')];
//$item_cate_id = $userCredential[0][csf('item_cate_id')];

if ($store_location_id !='') {
    $store_location_credential_cond = "and a.id in($store_location_id)"; 
}*/
    $store_location_credential_cond="";

if ($action=="load_drop_down_store")
{
	$data = explode("_", $data);
	echo create_drop_down( "cbo_store_name", 110, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id='$data[0]' and b.category_type=4 and a.status_active=1 and a.is_deleted=0 $store_location_credential_cond group by a.id, a.store_name order by a.store_name","id,store_name", 0, "--Select store--", 0, "" );
	exit();
}

if ($action=="load_drop_down_buyer")
{
	//$data=explode('_',$data);
	echo create_drop_down( "cbo_buyer_id", 130, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",0);
	exit();
}

if($action=="print_button_variable_setting")
{
    $print_report_format_arr = return_library_array("select format_id,format_id from lib_report_template where template_name =$data and module_id=6 and report_id=288 and is_deleted=0 and status_active=1","format_id","format_id");
    echo "print_report_button_setting('".implode(',',$print_report_format_arr)."');\n";
    exit();
}

if ($action=="style_popup")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);
	//print_r ($data);
	?>
	 <script>
	var selected_id = new Array, selected_name = new Array(); selected_attach_id = new Array();
	function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		function check_all_data()
		{
			var row_num=$('#list_view tr').length-1;
			for(var i=1;  i<=row_num;  i++)
			{
				$("#tr_"+i).click();
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
		$('#txt_po_id').val( id );
		$('#txt_po_val').val( ddd );
	}

	</script>
 <input type="hidden" id="txt_po_id" />
 <input type="hidden" id="txt_po_val" />
     <?
	if ($data[0]==0) $company_name=""; else $company_name="company_name='$data[0]'";
	if ($data[1]==0) $buyer_name=""; else $buyer_name=" and buyer_name='$data[1]'";
	if($db_type==0) $year_field="YEAR(insert_date) as year";
	else if($db_type==2) $year_field="to_char(insert_date,'YYYY') as year";
	else $year_field="";
	$sql ="select id,style_ref_no,job_no_prefix_num as job_prefix,$year_field from wo_po_details_master where $company_name $buyer_name";
	echo create_list_view("list_view", "Style Ref. No.,Job No,Year","200,100,100","450","310",0, $sql , "js_set_value", "id,style_ref_no", "", 1, "0", $arr, "style_ref_no,job_prefix,year", "","setFilterGrid('list_view',-1)","0","",1) ;
	exit();
}

if ($action=="item_description_popup")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);
	//print_r ($data);  
	?>	
    <script>
	 var selected_id = new Array, selected_name = new Array(); selected_attach_id = new Array();
	 
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
		$('#item_desc_id').val( id );
		$('#item_desc_val').val( ddd );
	} 
		  
	</script>
     <input type="hidden" id="item_desc_id" />
     <input type="hidden" id="item_desc_val" />
 	<?
 	//$trim_group= return_library_array( "select id, item_name from lib_item_group",'id','item_name');
	if ($data[1]==0) $item_name =""; else $item_name =" and item_group_id in($data[1])";
	$sql="SELECT id, item_group_id,item_category_id, item_description from product_details_master where company_id=$data[0] and item_category_id=4 and status_active=1 and is_deleted=0 $item_name"; 
	$arr=array(0=>$trim_group,3=>$item_category);
	echo  create_list_view("list_view", "Item Group,Description,Product ID", "150,300,150","600","300",0, $sql , "js_set_value", "id,item_description,item_group_id", "", 1, "item_group_id,0,0", $arr , "item_group_id,item_description,id", "",'setFilterGrid("list_view",-1);','0,0,0','',1) ;
	exit();
}

if ($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company=str_replace("'","",$cbo_company_id);
	$cbo_buyer=str_replace("'","",$cbo_buyer_id);
	$all_style=str_replace("'","",$txt_style);
	$cbo_product_department=str_replace("'","",$cbo_product_department);
    $cbo_team_leader=str_replace("'","",$cbo_team_leader);
	$all_style=implode(",",array_unique(explode(",",$all_style)));
	$txt_order_no='';//pob
	$txt_order_no_id='';//40041
	$txt_ref_no=str_replace("'","",$txt_ref_no);

	$from_date=str_replace("'","",$txt_date_from);
	$to_date=str_replace("'","",$txt_date_to);

	$all_style_no=explode(",",str_replace("'","",$txt_style_id));
	$all_style_quted="";
	foreach($all_style_no as $style_no)
	{
		$all_style_quted.="'".$style_no."'".",";
	}
	$all_style_quted=chop($all_style_quted,",");
	//echo $all_style_quted;die;
	$cbo_store_name = str_replace("'","",$cbo_store_name);

	$txt_job_no = str_replace("'","",$txt_job_no);
	$cbo_item_group = str_replace("'","",$cbo_item_group);
	
	$item_description = str_replace("'","",$txt_item_description);
	$item_description_id = str_replace("'","",$txt_item_description_id);
	$value_with = str_replace("'","",$cbo_value_with);
	$get_upto = str_replace("'","",$cbo_get_upto);
	$txt_days = str_replace("'","",$txt_days);
	$get_upto_qnty = str_replace("'","",$cbo_get_upto_qnty);
	$txt_qnty = str_replace("'","",$txt_qnty);
	$shipping_status = str_replace("'","",$shipping_status);
	


	$conversion_rate = return_library_array("select currency, conversion_rate from currency_conversion_rate where status_active=1 and is_deleted=0 and company_id=$cbo_company order by con_date asc","currency","conversion_rate");
	//print_r($conversion_rate);die;
	/*echo '<pre>';
	;
	if($db_type==0)
	{
		$currency_convert_result=sql_select("select id, conversion_rate from currency_conversion_rate  where status_active=1 and currency=2 order by id desc LIMIT 1");
	}
	else
	{
		$currency_convert_result=sql_select("select * from (select a.id, a.conversion_rate, max(id) over () as max_pk from currency_conversion_rate a  where a.status_active=1 and a.currency=2) where id=max_pk");
	}
	$currency_convert_rate=$currency_convert_result[0][csf("conversion_rate")];*/

	$sql_cond="";
	if($cbo_buyer>0)
	{
		$sql_cond=" and e.buyer_name=$cbo_buyer";
	}

	if($txt_ref_no !="") $sql_cond .=" and d.grouping='$txt_ref_no'";
	if($all_style !="") $sql_cond .=" and e.id in(".$all_style.")";
	if($txt_job_no !="") $sql_cond .=" and e.job_no_prefix_num='$txt_job_no'";
	if($cbo_product_department > 0) $sql_cond .=" and e.product_dept=$cbo_product_department";
	if($cbo_team_leader > 0) $sql_cond .=" and e.team_leader=$cbo_team_leader";


	if($cbo_store_name!=0) $store_cond=" and b.store_id =".str_replace("'","",$cbo_store_name); else $store_cond="";
	if ($cbo_item_group!="") { $item_group_cond = " and a.item_group_id in($cbo_item_group)"; }
	if ($item_description!="") { $item_description_cond = " and a.item_description='$item_description'"; }
	
	if($txt_ref_no =="" && $all_style =="" && $txt_job_no =="")
	{
		if($from_date !="" && $to_date !="") $sql_date_cond.=" and b.transaction_date between '$from_date' and '$to_date'";
	}
	
	$con = connect();
    execute_query("delete from gbl_temp_engine where user_id=$user_id and entry_form=97)");
    oci_commit($con);

	$all_po_id_arr=array();
	if($shipping_status != 0)
	{
		$pre_main_query = "SELECT A.ID AS PRODUCT_ID, A.ITEM_COLOR AS ITEM_COLOR_ID, A.ITEM_SIZE, A.ITEM_GROUP_ID, A.ITEM_DESCRIPTION, A.AVG_RATE_PER_UNIT, C.PO_BREAKDOWN_ID, C.QUANTITY,  C.ENTRY_FORM, C.TRANS_TYPE, E.BUYER_NAME, E.JOB_NO, E.JOB_NO_PREFIX_NUM, E.STYLE_REF_NO, E.PRODUCT_DEPT, E.TEAM_LEADER, E.TOTAL_SET_QNTY, D.ID AS PO_ID, D.PO_NUMBER, D.FILE_NO, D.GROUPING AS INT_REF_NO, D.PO_QUANTITY, A.UNIT_OF_MEASURE, B.CONS_RATE AS RATE, B.STORE_ID, B.TRANSACTION_TYPE, B.ID AS TRANS_ID, B.CONS_QUANTITY, B.CONS_AMOUNT, B.TRANSACTION_DATE, B.ORDER_UOM, B.PROD_ID, C.ORDER_AMOUNT, C.ORDER_AMOUNT, C.QUANTITY, B.ORDER_RATE, B.MST_ID, c.ORDER_RATE as PO_ORDER_RATE 
		from product_details_master a, inv_transaction b, order_wise_pro_details c, wo_po_break_down d, wo_po_details_master e
		where a.id=b.prod_id and b.id=c.trans_id and c.po_breakdown_id=d.id and d.job_no_mst=e.job_no and c.entry_form in(24,25,49,73,78,112) and a.entry_form=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 AND c.quantity > 0 and e.company_name=$cbo_company $store_cond $sql_cond $item_group_cond $item_description_cond $sql_date_cond
		ORDER BY A.ITEM_GROUP_ID, B.ID";
		//echo $pre_main_query;die;
		$sql_pre_main_query =sql_select($pre_main_query);
		$po_brkdwn_arr=array();
		foreach($sql_pre_main_query as $row)
		{
			$po_brkdwn_arr[$row['PO_BREAKDOWN_ID']]=$row['PO_BREAKDOWN_ID'];  
		}
		unset($sql_pre_main_query);
		if (count($po_brkdwn_arr)>0)
		{
			$rid=fnc_tempengine("gbl_temp_engine", $user_id, 97, 1, $po_brkdwn_arr, $empty_arr);
			if($rid) oci_commit($con);
			
			$sql_shipping_status_po = "SELECT a.PO_BREAK_DOWN_ID from pro_ex_factory_mst a, gbl_temp_engine g
			 where a.PO_BREAK_DOWN_ID = g.ref_val and a.status_active=1 and a.is_deleted=0 and a.shiping_status=$shipping_status and g.user_id=$user_id and g.entry_form=97 and g.ref_from=1 group by a.po_break_down_id";
			//  echo $sql_shipping_status_po; //die('=B');// and PO_BREAK_DOWN_ID=24120
			$sql_shipping_status_po_Data =sql_select($sql_shipping_status_po);
			foreach($sql_shipping_status_po_Data as $row)
			{	
				$all_po_id_arr[$row['PO_BREAK_DOWN_ID']]=$row['PO_BREAK_DOWN_ID'];  
			}
			unset($sql_shipping_status_po_Data);
		}
	}
	
	
	//echo count($all_po_id_arr).test;die;

	if(count($all_po_id_arr)>0)
	{
		$rid2=fnc_tempengine("gbl_temp_engine", $user_id, 97, 2, $all_po_id_arr, $empty_arr);
		if($rid2) oci_commit($con);
		
		$main_query = "SELECT A.ID AS PRODUCT_ID, A.ITEM_COLOR AS ITEM_COLOR_ID, A.ITEM_SIZE, A.ITEM_GROUP_ID, A.ITEM_DESCRIPTION, A.AVG_RATE_PER_UNIT, C.PO_BREAKDOWN_ID, C.QUANTITY,  C.ENTRY_FORM, C.TRANS_TYPE, E.BUYER_NAME, E.JOB_NO, E.JOB_NO_PREFIX_NUM, E.STYLE_REF_NO, E.PRODUCT_DEPT, E.TEAM_LEADER, E.TOTAL_SET_QNTY, D.ID AS PO_ID, D.PO_NUMBER, D.FILE_NO, D.GROUPING AS INT_REF_NO, D.PO_QUANTITY, A.UNIT_OF_MEASURE, B.CONS_RATE AS RATE, B.STORE_ID, B.TRANSACTION_TYPE, B.ID AS TRANS_ID, B.CONS_QUANTITY, B.CONS_AMOUNT, B.TRANSACTION_DATE, B.ORDER_UOM, B.PROD_ID, C.ORDER_AMOUNT, C.ORDER_AMOUNT, C.QUANTITY, B.ORDER_RATE, B.MST_ID, c.ORDER_RATE as PO_ORDER_RATE 
		from product_details_master a, inv_transaction b, order_wise_pro_details c, wo_po_break_down d, wo_po_details_master e, gbl_temp_engine g
		where a.id=b.prod_id and b.id=c.trans_id and c.po_breakdown_id=d.id and d.job_no_mst=e.job_no and d.id = g.ref_val and g.user_id=$user_id and g.entry_form=97 and g.ref_from=2 and c.entry_form in(24,25,49,73,78,112) and a.entry_form=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 AND c.quantity > 0 and e.company_name=$cbo_company $store_cond $sql_cond $item_group_cond $item_description_cond $sql_date_cond
		ORDER BY A.ITEM_GROUP_ID, B.ID";
	}
	else
	{
		$main_query = "SELECT A.ID AS PRODUCT_ID, A.ITEM_COLOR AS ITEM_COLOR_ID, A.ITEM_SIZE, A.ITEM_GROUP_ID, A.ITEM_DESCRIPTION, A.AVG_RATE_PER_UNIT, C.PO_BREAKDOWN_ID, C.QUANTITY,  C.ENTRY_FORM, C.TRANS_TYPE, E.BUYER_NAME, E.JOB_NO, E.JOB_NO_PREFIX_NUM, E.STYLE_REF_NO, E.PRODUCT_DEPT, E.TEAM_LEADER, E.TOTAL_SET_QNTY, D.ID AS PO_ID, D.PO_NUMBER, D.FILE_NO, D.GROUPING AS INT_REF_NO, D.PO_QUANTITY, A.UNIT_OF_MEASURE, B.CONS_RATE AS RATE, B.STORE_ID, B.TRANSACTION_TYPE, B.ID AS TRANS_ID, B.CONS_QUANTITY, B.CONS_AMOUNT, B.TRANSACTION_DATE, B.ORDER_UOM, B.PROD_ID, C.ORDER_AMOUNT, C.ORDER_AMOUNT, C.QUANTITY, B.ORDER_RATE, B.MST_ID, c.ORDER_RATE as PO_ORDER_RATE 
		from product_details_master a, inv_transaction b, order_wise_pro_details c, wo_po_break_down d, wo_po_details_master e
		where a.id=b.prod_id and b.id=c.trans_id and c.po_breakdown_id=d.id and d.job_no_mst=e.job_no and c.entry_form in(24,25,49,73,78,112) and a.entry_form=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 AND c.quantity > 0 and e.company_name=$cbo_company $store_cond $sql_cond $item_group_cond $item_description_cond $sql_date_cond
		ORDER BY A.ITEM_GROUP_ID, B.ID";
	}

	
	//echo $main_query;die;
	
	$rcv_currency_id_arr = return_library_array("select f.id, f.currency_id from inv_receive_master f where f.entry_form=24 and f.item_category=4 and f.status_active=1 and f.is_deleted=0","id","currency_id");
	$team_leader_arr = return_library_array("select id,team_leader_name from lib_marketing_team where project_type=1 and team_type in (0,1,2) and status_active =1 and is_deleted=0 order by team_leader_name","id","team_leader_name");

	$sql_data_arr=sql_select($main_query);
	$dtls_data=array();$order_wise_rate=array();
    
	foreach($sql_data_arr as $row)
	{
		$item_key = $row['ITEM_GROUP_ID'].'*'.$row['ITEM_DESCRIPTION'].'*'.$row['ITEM_COLOR_ID'].'*'.$row['ITEM_SIZE'];

		$dtls_data[$row['JOB_NO']][$item_key]['BUYER_NAME']=$row['BUYER_NAME'];
		$dtls_data[$row['JOB_NO']][$item_key]['DEPT']=$product_dept[$row['PRODUCT_DEPT']];
		$dtls_data[$row['JOB_NO']][$item_key]['LEADER']=$team_leader_arr[$row['TEAM_LEADER']];
		$dtls_data[$row['JOB_NO']][$item_key]['JOB_PREFIX']=$row['JOB_NO_PREFIX_NUM'];
		$dtls_data[$row['JOB_NO']][$item_key]['INT_REF_NO']=$row['INT_REF_NO'];
		$dtls_data[$row['JOB_NO']][$item_key]['STYLE']=$row['STYLE_REF_NO'];
		$dtls_data[$row['JOB_NO']][$item_key]['ORDER_UOM']=$row['ORDER_UOM'];
		$dtls_data[$row['JOB_NO']][$item_key]['UOM']=$row['UNIT_OF_MEASURE'];
		$dtls_data[$row['JOB_NO']][$item_key]['STORE']=$row['STORE_ID'];
		$dtls_data[$row['JOB_NO']][$item_key]['PRODUCT_ID'].=$row['PRODUCT_ID'].',';
		$dtls_data[$row['JOB_NO']][$item_key]['PROD_ID']=$row['PROD_ID'];
		$dtls_data[$row['JOB_NO']][$item_key]['AVG_RATE_PER_UNIT']=$row['AVG_RATE_PER_UNIT'];
		
		
		if($row["TRANSACTION_TYPE"]==1) // Receive
		{
			$currency_id_rcv=$rcv_currency_id_arr[$row["MST_ID"]];
			$exchange_rate = ($currency_id_rcv==1) ? 1 : $conversion_rate[$currency_id_rcv];
			$mrr_amt_tk=$row["QUANTITY"]*($row["ORDER_RATE"]*$exchange_rate);
			$order_wise_rate[$row["PRODUCT_ID"]][$row["PO_ID"]]["quantity"]+=$row["QUANTITY"];
			$order_wise_rate[$row["PRODUCT_ID"]][$row["PO_ID"]]["mrr_amt_tk"]+=$mrr_amt_tk;
			// $exchange_rate = ($row["CURRENCY_ID"]==1) ? 1 : $conversion_rate[$row["CURRENCY_ID"]];
			if(strtotime($row["TRANSACTION_DATE"])<strtotime($from_date))
			{
				$dtls_data[$row['JOB_NO']][$item_key]['rcv_total_opening_qty']+=$row["QUANTITY"];
				$dtls_data[$row['JOB_NO']][$item_key]['rcv_total_opening_amt']+=$mrr_amt_tk;
				$dtls_data[$row['JOB_NO']][$item_key]['currency_id'].=$currency_id_rcv.',';
			}
			else if( (strtotime($row["TRANSACTION_DATE"])>=strtotime($from_date)) && (strtotime($row["TRANSACTION_DATE"])<=strtotime($to_date)) )
			{
				$dtls_data[$row['JOB_NO']][$item_key]['receive_qty']+=$row["QUANTITY"];
				$dtls_data[$row['JOB_NO']][$item_key]['receive_amt']+=$mrr_amt_tk;
				$dtls_data[$row['JOB_NO']][$item_key]['currency_id'].=$currency_id_rcv.',';
			}
		}
		else if($row["TRANSACTION_TYPE"]==2) // Issue
		{
			$trans_rate=$order_wise_rate[$row["PRODUCT_ID"]][$row["PO_ID"]]["mrr_amt_tk"]/$order_wise_rate[$row["PRODUCT_ID"]][$row["PO_ID"]]["quantity"];
			//echo $trans_rate."<br>";
			if(strtotime($row["TRANSACTION_DATE"])<strtotime($from_date))
			{
				$dtls_data[$row['JOB_NO']][$item_key]['iss_total_opening_qty']+=$row["QUANTITY"];
				$dtls_data[$row['JOB_NO']][$item_key]['iss_total_opening_amt']+=$row["QUANTITY"]*$trans_rate;
			}
			else if( (strtotime($row["TRANSACTION_DATE"])>=strtotime($from_date)) && (strtotime($row["TRANSACTION_DATE"])<=strtotime($to_date)) )
			{
				$dtls_data[$row['JOB_NO']][$item_key]['issue_qty']+=$row["QUANTITY"];
				$dtls_data[$row['JOB_NO']][$item_key]['issue_amt']+=$row["QUANTITY"]*$trans_rate;
			}
		}
		else if($row["TRANSACTION_TYPE"]==3) // Receive Return
		{
			$trans_rate=$order_wise_rate[$row["PRODUCT_ID"]][$row["PO_ID"]]["mrr_amt_tk"]/$order_wise_rate[$row["PRODUCT_ID"]][$row["PO_ID"]]["quantity"];
			if(strtotime($row["TRANSACTION_DATE"])<strtotime($from_date))
			{
				$dtls_data[$row['JOB_NO']][$item_key]['receive_return_opening_qty']+=$row["QUANTITY"];
				$dtls_data[$row['JOB_NO']][$item_key]['receive_return_opening_amt']+=$row["QUANTITY"]*$trans_rate;
			}
			else if( (strtotime($row["TRANSACTION_DATE"])>=strtotime($from_date)) && (strtotime($row["TRANSACTION_DATE"])<=strtotime($to_date)) )
			{
				$dtls_data[$row['JOB_NO']][$item_key]['receive_return_qty']+=$row["QUANTITY"];
				$dtls_data[$row['JOB_NO']][$item_key]['receive_return_amt']+=$row["QUANTITY"]*$trans_rate;
			}
		}
		else if($row["TRANSACTION_TYPE"]==4) // Issue Return
		{
			$trans_rate=$order_wise_rate[$row["PRODUCT_ID"]][$row["PO_ID"]]["mrr_amt_tk"]/$order_wise_rate[$row["PRODUCT_ID"]][$row["PO_ID"]]["quantity"];
			if(strtotime($row["TRANSACTION_DATE"])<strtotime($from_date))
			{
				$dtls_data[$row['JOB_NO']][$item_key]['issue_return_opening_qty']+=$row["QUANTITY"];
				$dtls_data[$row['JOB_NO']][$item_key]['issue_return_opening_amt']+=$row["QUANTITY"]*$trans_rate;
			}
			else if( (strtotime($row["TRANSACTION_DATE"])>=strtotime($from_date)) && (strtotime($row["TRANSACTION_DATE"])<=strtotime($to_date)) )
			{
				$dtls_data[$row['JOB_NO']][$item_key]['issue_return_qty']+=$row["QUANTITY"];
				$dtls_data[$row['JOB_NO']][$item_key]['issue_return_amt']+=$row["QUANTITY"]*$trans_rate;
			}
			
		}
		else if($row["TRANSACTION_TYPE"]==5) // Item Transfer Receive
		{
			$exchange_rate=set_conversion_rate(2,$row["TRANSACTION_DATE"]);
			//
			if($row["PO_ORDER_RATE"]>0)
			{
				$mrr_amt_tk=$row["QUANTITY"]*($row["PO_ORDER_RATE"]*$exchange_rate);
				//echo $row["QUANTITY"]."=".$row["PO_ORDER_RATE"]."=".$exchange_rate."=".$row["TRANSACTION_DATE"]."=1"."<br>";
			}
			else
			{
				$mrr_amt_tk=$row["QUANTITY"]*$row["RATE"];
				//echo $row["QUANTITY"]."=".$row["PO_ORDER_RATE"]."=".$exchange_rate."=".$row["TRANSACTION_DATE"]."=2"."<br>";
			}
			
			$order_wise_rate[$row["PRODUCT_ID"]][$row["PO_ID"]]["quantity"]+=$row["QUANTITY"];
			$order_wise_rate[$row["PRODUCT_ID"]][$row["PO_ID"]]["mrr_amt_tk"]+=$mrr_amt_tk;
			
			if(strtotime($row["TRANSACTION_DATE"])<strtotime($from_date))
			{
				$dtls_data[$row['JOB_NO']][$item_key]['item_transfer_receive_opening_qty']+=$row["QUANTITY"];
				$dtls_data[$row['JOB_NO']][$item_key]['item_transfer_receive_opening_amt']+=$mrr_amt_tk;
			}
			else if( (strtotime($row["TRANSACTION_DATE"])>=strtotime($from_date)) && (strtotime($row["TRANSACTION_DATE"])<=strtotime($to_date)) )
			{
				$dtls_data[$row['JOB_NO']][$item_key]['item_transfer_receive_qty']+=$row["QUANTITY"];
				$dtls_data[$row['JOB_NO']][$item_key]['item_transfer_receive_amt']+=$mrr_amt_tk;
			}
		}
		else if($row["TRANSACTION_TYPE"]==6) // Item Transfer Issue
		{
			$trans_rate=$order_wise_rate[$row["PRODUCT_ID"]][$row["PO_ID"]]["mrr_amt_tk"]/$order_wise_rate[$row["PRODUCT_ID"]][$row["PO_ID"]]["quantity"];
			if(strtotime($row["TRANSACTION_DATE"])<strtotime($from_date))
			{
				$dtls_data[$row['JOB_NO']][$item_key]['item_transfer_issue_opening_qty']+=$row["QUANTITY"];
				$dtls_data[$row['JOB_NO']][$item_key]['item_transfer_issue_opening_amt']+=$row["QUANTITY"]*$trans_rate;
			}
			else if( (strtotime($row["TRANSACTION_DATE"])>=strtotime($from_date)) && (strtotime($row["TRANSACTION_DATE"])<=strtotime($to_date)) )
			{
				$dtls_data[$row['JOB_NO']][$item_key]['item_transfer_issue']+=$row["QUANTITY"];
				$dtls_data[$row['JOB_NO']][$item_key]['item_transfer_issue_amt']+=$row["QUANTITY"]*$trans_rate;
			}
		}
	}
	unset($sql_data_arr);
	//echo "<pre>";print_r($dtls_data);die;
	
	$date_array=array();
	$returnRes_date="SELECT PROD_ID, MIN(TRANSACTION_DATE) AS MIN_DATE, MAX(TRANSACTION_DATE) AS MAX_DATE from inv_transaction where is_deleted=0 and status_active=1 and item_category=4 group by prod_id";
	//echo $returnRes_date;die;
	$result_returnRes_date = sql_select($returnRes_date);
	foreach($result_returnRes_date as $row)	
	{
		$date_array[$row["PROD_ID"]]['MIN_DATE']=$row["MIN_DATE"];
		$date_array[$row["PROD_ID"]]['MAX_DATE']=$row["MAX_DATE"];
	}
	unset($result_returnRes_date);
	
	execute_query("delete from gbl_temp_engine where user_id=$user_id and entry_form=97");
    oci_commit($con); disconnect($con);
	disconnect($con);
	
	?>
    <fieldset style="width:1960px;">
    <tr bgcolor="#666666" style="font-weight:bold; font-size:14px;">
    </fieldset>
    <?
	$html='';
	$html2='';
    $html.='<table width="1960px">
            <tr class="form_caption">
                <td colspan="17" align="center" style="border:none;font-size:16px; font-weight:bold">'.$report_title.'</td>
            </tr>
            <tr class="form_caption">
                <td colspan="17" align="center">'.$company_library[$cbo_company].'</td>
            </tr>
        </table>';
		$html2.='<fieldset style="width:1960px;">
		<table width="1960px">
            <tr class="form_caption">
                <td colspan="17" align="center" style="border:none;font-size:16px; font-weight:bold">'.$report_title.'</td>
            </tr>
            <tr class="form_caption">
                <td colspan="17" align="center">'.$company_library[$cbo_company].'</td>
            </tr>
        </table>';
		$html.='<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1957px" class="rpt_table" >
			<thead>
				<tr>
					<th width="40px" align="center"><b>SL </b></th>
					<th width="70px align="center"><b>Buyer</b></th>
					<th width="80px align="center"><b>Ref. No</b></th>
					<th width="70px align="center"><b>Job No</b></th>
					<th width="70px align="center"><b>Prod. Dept.</b></th>
					<th width="90px align="center"><b>Team Leader</b></th>
					<th width="70px align="center"><b>Style</b></th>
					<th width="110px align="center"><b>Item Group</b></th>
					<th width="140px align="center"><b>Item Description</b></th>
					<th width="70px align="center"><b>Item Color</b></th>
					<th width="50px align="center"><b>Item Size</b></th>
					<th width="40px align="center"><b>UOM</b></th>
					<th width="70px align="center"><b>Opaning Qty</b></th>
					<th width="90px align="center"><b>Opaning Value</b></th>
					<th width="60px align="center"><b>Recv. Qty</b></th>
					<th width="60px align="center"><b>Transfer IN</b></th>
					<th width="60px align="center"><b>Issue Return</b></th>
					<th width="70px align="center"><b>Total Receive</b></th>
					<th width="60px align="center"><b>Transfer Out</b></th>
					<th width="60px align="center"><b>Issue Qty.</b></th>
					<th width="60px align="center"><b>Rcv Return</b></th>
					<th width="70px align="center"><b>Total Issue Qty</b></th>
					<th width="60px align="center"><b>Stock Qty</b></th>
					<th width="60px align="center"><b>Rate [BDT]</b></th>
					<th width="100px align="center"><b>Stock Value (BDT)</b></th>
					<th width="60px align="center"><b>Age(Days)</b></th>
					<th align="center"><b>DOH</b></th>
				</tr>
            </thead>
			<tbody>';
			
			$html2.='<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1957px" class="rpt_table" >
			<thead>
				<tr bgcolor="#666666" style="font-weight:bold; font-size:14px;">
					<th width="40px">SL </th>
					<th width="70px">Buyer</th>
					<th width="80px">Ref. No</th>
					<th width="70px">Job No</th>
					<th width="70px">Prod. Dept.</th>
					<th width="90px">Team Leader</th>
					<th width="70px">Style</th>
					<th width="110px">Item Group</th>
					<th width="140px">Item Description</th>
					<th width="70px">Item Color</th>
					<th width="50px">Item Size</th>
					<th width="40px">UOM</th>
					<th width="70px">Opaning Qty</th>
					<th width="90px">Opaning Value</th>
					<th width="60px">Recv. Qty</th>
					<th width="60px">Transfer IN</th>
					<th width="60px">Issue Return</th>
					<th width="70px">Total Receive</th>
					<th width="60px">Transfer Out</th>
					<th width="60px">Issue Qty.</th>
					<th width="60px">Rcv Return</th>
					<th width="70px">Total Issue Qty</th>
					<th width="60px">Stock Qty</th>
					<th width="60px">Rate [BDT]</th>
					<th width="100px">Stock Value (BDT)</th>
					<th width="60px">Age(Days)</th>
					<th>DOH</th>
				</tr>
            </thead>
			</table>
			<div style="width:1960px; overflow-y:scroll; max-height:350px;font-size:12px; " id="scroll_body">
	        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1940px" class="rpt_table" id="tbl_issue_status" align="left">';

			    $store_name_arr = return_library_array("select a.id, a.store_name from lib_store_location a", 'id', 'store_name');
				$season_arr = return_library_array("select a.id, a.season_name from lib_buyer_season a", 'id', 'season_name');

				$i=1;
				if (count($dtls_data) == 0) 
				{
					echo "<span style='color: red;'>Data Not Found</span>";die;
				}
				
				
				$total_stock_amount=0;
				$item_group_wise_data=array();
				foreach($dtls_data as $job_no=>$job_data)
				{
					foreach ($job_data as $item_key=>$item_data)
					{
						// echo $item_key.'<br>';
						$data = explode("*",$item_key);
						$item_group_id = $data[0];
						$item_description = $data[1];
						$item_color_id = $data[2];
						$item_size = $data[3];
						// echo $item_data["PROD_ID"].'<br>';
						// $ageOfDays = datediff("d",$date_array[$job_no][$item_data["PROD_ID"]]['MIN_DATE'],date("Y-m-d"));
						// $daysOnHand = datediff("d",$date_array[$job_no][$item_data["PROD_ID"]]['MAX_DATE'],date("Y-m-d"));
						$ageOfDays = datediff("d",$date_array[$item_data["PROD_ID"]]['MIN_DATE'],date("Y-m-d"));
						$daysOnHand = datediff("d",$date_array[$item_data["PROD_ID"]]['MAX_DATE'],date("Y-m-d"));
						// echo $ageOfDays.' = Test';
						$all_currency_ids=chop($item_data["currency_id"],",");
						$all_currency_id=implode(",", array_unique(explode(",",$all_currency_ids)));
						/*$currency_id_arr=array_unique(explode(",",$all_currency_id));
						foreach ($currency_id_arr as $currency) 
						{
						   $exc_rate= implode(",", array_unique(explode(",",$conversion_rate[$currency]))) ;
						}*/

						$only_recv_qty= $item_data['rcv_total_opening_qty']+$item_data['receive_qty'];
						$only_recv_amount= $item_data['rcv_total_opening_amt']+$item_data['receive_amt'];
						$recv_rate = $only_recv_amount/$only_recv_qty;
						
						$only_transferIN_qty= $item_data['item_transfer_receive_opening_qty']+$item_data['item_transfer_receive_qty'];
						$only_transferIN_amount= $item_data['item_transfer_receive_opening_amt']+$item_data['item_transfer_receive_amt'];
						$transferIN_rate = $only_transferIN_amount/$only_transferIN_qty;
						if($recv_rate=="" && $transferIN_rate >0)
						{
							$recv_rate = $transferIN_rate;
						}
						//echo $recv_rate."=".$transferIN_rate.test;die;

						// echo $item_data['receive_qty'].'+'.$item_data['issue_return_qty'].'+'.$item_data['item_transfer_receive_qty'].'<br>';
						$total_recv_qty = $item_data['receive_qty']+$item_data['issue_return_qty']+$item_data['item_transfer_receive_qty'];
						$total_recv_amount = $item_data['receive_amt']+$item_data['issue_return_amt']+$item_data['item_transfer_receive_amt'];

						$total_issue_qty = $item_data['issue_qty']+$item_data['receive_return_qty']+$item_data['item_transfer_issue'];
						$total_issue_amount = $item_data['issue_amt']+$item_data['receive_return_amt']+$item_data['item_transfer_issue_amt'];						

						$recv_opening_qty = $item_data['rcv_total_opening_qty']+$item_data['issue_return_opening_qty']+$item_data['item_transfer_receive_opening_qty'];
						$issue_opening_qty = $item_data['iss_total_opening_qty']+$item_data['receive_return_opening_qty']+$item_data['item_transfer_issue_opening_qty'];

						$recv_opening_amount = $item_data['rcv_total_opening_amt']+$item_data['issue_return_opening_amt']+$item_data['item_transfer_receive_opening_amt'];
						$issue_opening_amount = $item_data['iss_total_opening_amt']+$item_data['receive_return_opening_amt']+$item_data['item_transfer_issue_opening_amt'];

						$opening_bal = $recv_opening_qty-$issue_opening_qty;
						$opening_amunt = $recv_opening_amount-$issue_opening_amount;
						$stock_qty=$opening_bal+$total_recv_qty-$total_issue_qty;
						$stock_amount_rcv_rate_wise=$opening_amunt+$total_recv_amount-$total_issue_amount;
						$recv_rate=0;
						if($stock_amount_rcv_rate_wise!=0 && $stock_qty!=0)
						{
							$recv_rate=$stock_amount_rcv_rate_wise/$stock_qty;
						}
						//$stock_amount_rcv_rate_wise=$stock_qty*$recv_rate;

						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						if((($get_upto==1 && $ageOfDays>$txt_days) || ($get_upto==2 && $ageOfDays<$txt_days) || ($get_upto==3 && $ageOfDays>=$txt_days) || ($get_upto==4 && $ageOfDays<=$txt_days) || ($get_upto==5 && $ageOfDays==$txt_days) || $get_upto==0) && (($get_upto_qnty==1 && $stock_qty>$txt_qnty) || ($get_upto_qnty==2 && $stock_qty<$txt_qnty) || ($get_upto_qnty==3 && $stock_qty>=$txt_qnty) || ($get_upto_qnty==4 && $stock_qty<=$txt_qnty) || ($get_upto_qnty==5 && $stock_qty==$txt_qnty) || $get_upto_qnty==0))
						{
							if($value_with==1)
							{
								if(number_format($stock_qty,2)>0.00)//||number_format($opening_bal,2)>0.00 
								{
									$html.='<tr>
										<td align="center">'.$i.'</td>
										<td>'.$buyer_arr[$item_data['BUYER_NAME']].'</td>
										<td>'.implode(",",array_unique(explode(",",chop($item_data["INT_REF_NO"],",")))).'</td>
										<td>'.$job_no.'</td>
                                        <td>'.$item_data['DEPT'].'</td>
                                        <td>'.$item_data['LEADER'].'</td>
										<td>';
										strlen($item_data['STYLE'])>20 ? $html.=substr($item_data['STYLE'], 0, 20) : $html.=$item_data['STYLE'];
										$html.='</td>
										<td>'.$trim_group[$item_group_id].'</td>
										<td>'.$item_description.'</td>
										<td>'.$color_library[$item_color_id].'</td>
										<td>';
										if($item_size!="0") $html.=$item_size; 
										$html.='</td>
										<td align="center">'.$unit_of_measurement[$item_data['UOM']].'</td>
										<td align="right">'.number_format($opening_bal,2).'</td>
										<td align="right">'.number_format($opening_amunt,2).'</td>
										
										<td align="right">'.number_format($item_data['receive_qty'],2).'</td>
										<td align="right">'.number_format($item_data['item_transfer_receive_qty'],2).'</td>
										<td align="right">'.number_format($item_data['issue_return_qty'],2).'</td>
										<td align="right">'.number_format($total_recv_qty,2).'</td>

										<td align="right">'.number_format($item_data['item_transfer_issue'],2).'</td>
										<td align="right">'.number_format($item_data['issue_qty'],2).'</td>

										<td align="right">'.number_format($item_data['receive_return_qty'],2).'</td>
										<td align="right">'.number_format($total_issue_qty,2).'</td>

										<td align="right">'.number_format($stock_qty,2).'</td>
										<td align="right">'.number_format($recv_rate,2).'</td>

										<td align="right">'.number_format($stock_amount_rcv_rate_wise,2).'</td>
										<td align="right">'.$ageOfDays.'</td>
										<td align="right">'.$daysOnHand.'</td>
									</tr>';
									
									$html2.='<tr bgcolor="'.$bgcolor.'"  onclick="change_color(\'tr_'.$i.','.$bgcolor.')" id="tr_'.$i.'">
										<td valign="middle" width="40" align="center">'.$i.'</td>
										<td valign="middle" width="70"><p>'.$buyer_arr[$item_data['BUYER_NAME']].'</p></td>
										<td valign="middle" width="80"><p>'.implode(",",array_unique(explode(",",chop($item_data["INT_REF_NO"],",")))).'</p></td>
										<td valign="middle" width="70"><p>'.$job_no.'</p></td>
                                        <td valign="middle" width="70"><p>'.$item_data['DEPT'].'</p></td>
                                        <td valign="middle" width="90"><p>'.$item_data['LEADER'].'</p></td>
										<td valign="middle" width="70"><p> ';
										strlen($item_data['STYLE'])>20 ? $html2.=substr($item_data['STYLE'], 0, 20) : $html2.=$item_data['STYLE'];
										$html2.='</p></td>
										<td valign="middle" width="110"><p>'.$trim_group[$item_group_id].'</p></td>
										<td valign="middle" width="140"><p>'.$item_description.'</p></td>
										<td valign="middle" width="70"><p>'.$color_library[$item_color_id].'</p></td>
										<td valign="middle" width="50"><p>';										
										if($item_size!="0") $html2.=$item_size; 
										$html2.='</p></td>
										<td valign="middle" width="40" align="center"><p>'.$unit_of_measurement[$item_data['UOM']].'</p></td>
										<td valign="middle" width="70" align="right">'.number_format($opening_bal,2).'</td>
										<td valign="middle" width="90" align="right">'.number_format($opening_amunt,2).'</td>
										
										<td valign="middle" width="60" align="right">'.number_format($item_data['receive_qty'],2).'</td>
										<td valign="middle" width="60" align="right">'.number_format($item_data['item_transfer_receive_qty'],2).'</td>
										<td valign="middle" width="60" align="right">'.number_format($item_data['issue_return_qty'],2).'</td>
										<td valign="middle" width="70" align="right">'.number_format($total_recv_qty,2).'</td>

										<td valign="middle" width="60" align="right">'.number_format($item_data['item_transfer_issue'],2).'</td>
										<td valign="middle" width="60" align="right">'.number_format($item_data['issue_qty'],2).'</td>

										<td valign="middle" width="60" align="right">'.number_format($item_data['receive_return_qty'],2).'</td>
										<td valign="middle" width="70" align="right">'.number_format($total_issue_qty,2).'</td>

										<td valign="middle" width="60" align="right">'.number_format($stock_qty,2).'</td>
										<td valign="middle" width="60" align="right">'.number_format($recv_rate,2).'</td>

										<td valign="middle" width="100" align="right">'.number_format($stock_amount_rcv_rate_wise,2).'</td>
										<td valign="middle" width="60" align="right">'.$ageOfDays.'</td>
										<td valign="middle" align="right">'.$daysOnHand.'</td>
									</tr>';
									
									$item_group_wise_data[$item_group_id]["uom"]=$item_data[('UOM')];
									$item_group_wise_data[$item_group_id]["recv_qnty"]+=$total_recv_qty;
									$item_group_wise_data[$item_group_id]["issue_qnty"]+=$total_issue_qty;
									
									$i++;
									$total_opening_bal+=$opening_bal;									
									$total_opening_amunt+=$opening_amunt;
									$grand_total_recv_qty+=$item_data['receive_qty'];
									$total_item_transfer_receive_qty+=$item_data['item_transfer_receive_qty'];
									$total_issue_return_qty+=$item_data['issue_return_qty'];
									$total_recv_qty+=$total_recv_qty;
									$total_item_transfer_issue+=$item_data['item_transfer_issue'];
									$total_issue_qty+=$item_data['issue_qty'];
									$total_receive_return_qty+=$item_data['receive_return_qty'];
									$grand_total_issue_qty+=$total_issue_qty;
									$total_stock_qty+=$stock_qty;
									if($stock_amount_rcv_rate_wise>0) $total_stock_amount+=$stock_amount_rcv_rate_wise;

								

								}
							}
							else
							{
								// number_format($stock_qty,3)>0.000 || number_format($opening_bal,3)>0.000 || number_format($total_recv_qty,3)>0.000 || number_format($total_issue_qty,3)>0.000
								if( number_format($stock_qty,3)>0.000 || number_format($stock_qty,3)==0.000 )
								{
									$html.='<tr>
										<td align="center">'.$i.'</td>
										<td>'.$buyer_arr[$item_data['BUYER_NAME']].'</td>
										<td>'.implode(",",array_unique(explode(",",chop($item_data["INT_REF_NO"],",")))).'</td>
										<td>'.$job_no.'</td>
                                        <td>'.$item_data['DEPT'].'</td>
                                        <td>'.$item_data['LEADER'].'</td>
										<td>';
										strlen($item_data['STYLE'])>20 ? $html.=substr($item_data['STYLE'], 0, 20) : $html.=$item_data['STYLE'];
										$html.='</td>
										<td>'.$trim_group[$item_group_id].'</td>
										<td>'.$item_description.'</td>
										<td>'.$color_library[$item_color_id].'</td>
										<td>';
										if($item_size!="0") $html.=$item_size; 
										$html.='</td>
										<td align="center">'.$unit_of_measurement[$item_data['UOM']].'</td>
										<td align="right">'.number_format($opening_bal,2).'</td>
										<td align="right">'.number_format($opening_amunt,2).'</td>
										
										<td align="right">'.number_format($item_data['receive_qty'],2).'</td>
										<td align="right">'.number_format($item_data['item_transfer_receive_qty'],2).'</td>
										<td align="right">'.number_format($item_data['issue_return_qty'],2).'</td>
										<td align="right">'.number_format($total_recv_qty,2).'</td>

										<td align="right">'.number_format($item_data['item_transfer_issue'],2).'</td>
										<td align="right">'.number_format($item_data['issue_qty'],2).'</td>

										<td align="right">'.number_format($item_data['receive_return_qty'],2).'</td>
										<td align="right">'.number_format($total_issue_qty,2).'</td>

										<td align="right">'.number_format($stock_qty,2).'</td>
										<td align="right">'.number_format($recv_rate,2).'</td>

										<td align="right">'.number_format($stock_amount_rcv_rate_wise,2).'</td>
										<td align="right">'.$ageOfDays.'</td>
										<td align="right">'.$daysOnHand.'</td>
									</tr>';
									
									$html2.='<tr bgcolor="'.$bgcolor.'"  onclick="change_color(\'tr_'.$i.','.$bgcolor.')" id="tr_'.$i.'">
										<td valign="middle" width="40" align="center">'.$i.'</td>
										<td valign="middle" width="70"><p>'.$buyer_arr[$item_data['BUYER_NAME']].'</p></td>
										<td valign="middle" width="80"><p>'.implode(",",array_unique(explode(",",chop($item_data["INT_REF_NO"],",")))).'</p></td>
										<td valign="middle" width="70"><p>'.$job_no.'</p></td>
                                        <td valign="middle" width="70"><p>'.$item_data['DEPT'].'</p></td>
                                        <td valign="middle" width="90"><p>'.$item_data['LEADER'].'</p></td>
										<td valign="middle" width="70"><p> ';
										strlen($item_data['STYLE'])>20 ? $html2.=substr($item_data['STYLE'], 0, 20) : $html2.=$item_data['STYLE'];
										$html2.='</p></td>
										<td valign="middle" width="110"><p>'.$trim_group[$item_group_id].'</p></td>
										<td valign="middle" width="140"><p>'.$item_description.'</p></td>
										<td valign="middle" width="70"><p>'.$color_library[$item_color_id].'</p></td>
										<td valign="middle" width="50"><p>';										
										if($item_size!="0") $html2.=$item_size; 
										$html2.='</p></td>
										<td valign="middle" width="40" align="center"><p>'.$unit_of_measurement[$item_data['UOM']].'</p></td>
										<td valign="middle" width="70" align="right">'.number_format($opening_bal,2).'</td>
										<td valign="middle" width="90" align="right">'.number_format($opening_amunt,2).'</td>
										
										<td valign="middle" width="60" align="right">'.number_format($item_data['receive_qty'],2).'</td>
										<td valign="middle" width="60" align="right">'.number_format($item_data['item_transfer_receive_qty'],2).'</td>
										<td valign="middle" width="60" align="right">'.number_format($item_data['issue_return_qty'],2).'</td>
										<td valign="middle" width="70" align="right">'.number_format($total_recv_qty,2).'</td>

										<td valign="middle" width="60" align="right">'.number_format($item_data['item_transfer_issue'],2).'</td>
										<td valign="middle" width="60" align="right">'.number_format($item_data['issue_qty'],2).'</td>

										<td valign="middle" width="60" align="right">'.number_format($item_data['receive_return_qty'],2).'</td>
										<td valign="middle" width="70" align="right">'.number_format($total_issue_qty,2).'</td>

										<td valign="middle" width="60" align="right">'.number_format($stock_qty,2).'</td>
										<td valign="middle" width="60" align="right">'.number_format($recv_rate,2).'</td>

										<td valign="middle" width="100" align="right">'.number_format($stock_amount_rcv_rate_wise,2).'</td>
										<td valign="middle" width="60" align="right">'.$ageOfDays.'</td>
										<td valign="middle" align="right">'.$daysOnHand.'</td>
									</tr>';
									

									$item_group_wise_data[$item_group_id]["uom"]=$item_data[('UOM')];
									$item_group_wise_data[$item_group_id]["recv_qnty"]+=$total_recv_qty;
									$item_group_wise_data[$item_group_id]["issue_qnty"]+=$total_issue_qty;
									
									$i++;
									$total_opening_bal+=$opening_bal;
									$total_opening_amunt+=$opening_amunt;
									$grand_total_recv_qty+=$item_data['receive_qty'];
									$total_item_transfer_receive_qty+=$item_data['item_transfer_receive_qty'];
									$total_issue_return_qty+=$item_data['issue_return_qty'];
									$total_recv_qty+=$total_recv_qty;
									$total_item_transfer_issue+=$item_data['item_transfer_issue'];
									$total_issue_qty+=$item_data['issue_qty'];
									$total_stock_qty+=$stock_qty;
									if($stock_amount_rcv_rate_wise>0) $total_stock_amount+=$stock_amount_rcv_rate_wise;

								}
							}
						}
					}
				}
				
                $html.='</tbody>
			<tfoot>
				<tr>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th>Grand Total</th>
					<th>'.number_format($total_opening_bal,2).'</th>
					<th>'.number_format($total_opening_amunt,2).'</th>
					<th>'.number_format($grand_total_recv_qty,2).'</th>
					<th>'.number_format($total_item_transfer_receive_qty,2).'</th>
					<th>'.number_format($total_issue_return_qty,2).'</th>
					<th>'.number_format($total_recv_qty,2).'</th>
					<th>'.number_format($total_item_transfer_issue,2).'</th>
					<th>'.number_format($total_issue_qty,2).'</th>
					<th>'.number_format($total_receive_return_qty,2).'</th>
					<th>'.number_format($grand_total_issue_qty,2).'</th>
					<th>'.number_format($total_stock_qty,2).'</th>
					<th></th>
					<th>'.number_format($total_stock_amount,2).'</th>
					<th></th>
					<th></th>
				<tr>
            </tfoot>
        </table>';
		
		$html2.='</table>
		</div>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1958px" class="rpt_table" >
			<tfoot>
				<th width="40">&nbsp;</th>
                <th width="70">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="70">&nbsp;</th>
                <th width="70">&nbsp;</th>
                <th width="90">&nbsp;</th>
                <th width="70">&nbsp;</th>
                <th width="110">&nbsp;</th>
                <th width="140">&nbsp;</th>
                <th width="70">&nbsp;</th>
                <th width="90">Grand Total</th>
                <th width="70" id="total_opening_qnty">l</th>
                <th width="90" id="total_opening_amunt">'.number_format($total_opening_amunt,2).'</th>
                <th width="60" id="grand_total_recv_qty">'.number_format($grand_total_recv_qty,2).'</th>
                <th width="60" id="total_item_transfer_receive_qty">&nbsp;</th>
                <th width="60" id="total_issue_return_qty">&nbsp;</th>
                <th width="70" id="total_recv_qty">&nbsp;</th>
                <th width="60" id="total_item_transfer_issue">&nbsp;</th>
                <th width="60" id="total_issue_qty">&nbsp;</th>
                <th width="60" id="total_receive_return_qty">&nbsp;</th>
                <th width="70" id="grand_total_issue_qty">&nbsp;</th>
                <th width="60" id="total_stock_qnty"></th>
                <th width="60">&nbsp;</th>
                <th width="100" id="total_stock_amount">'.number_format($total_stock_amount,2).'</th>
                <th width="60">&nbsp;</th>
                <th>&nbsp;</th>
            </tfoot>
        </table>


	<br/>
	<p style="text-align:left; padding-left:10px; font-size:16px; font-weight:bold">Summary Report</p>
	<br />
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table" align="left">
		<thead>
			<tr>
				<th width="50">SL </th>
				<th width="170">Item Group</th>
				<th width="70">UOM</th>
				<th width="100">Rec Qty</th>
				<th width="100">Issue Qty</th>
				<th>Left Over Qty</th>
			</tr>
		</thead>
		<tbody>';
			
			$i=1;
			foreach($item_group_wise_data as $item_id=>$val)
			{
				// echo "<pre>";
				// print_r($item_group_wise_data);
				$left_overs_qnty=$val["recv_qnty"]-$val["issue_qnty"];
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$html2.='<tr bgcolor="'.$bgcolor.'"  onclick="change_color(\'tr_'.$i.','.$bgcolor.')" id="tr_'.$i.'">
					<td align="center">'.$i.'</td>
					<td title="<? echo $item_id; ?>"><p>'.$trim_group[$item_id].'&nbsp;</p></td>
					<td align="right">'.$unit_of_measurement[$val["uom"]].'</td>
					<td align="right">'. number_format($val["recv_qnty"],2).'</td>
					<td align="right">'. number_format($val["issue_qnty"],2).'</td>
					<td align="right">'. number_format($left_overs_qnty,2).'</td>
				</tr>';
	
				$i++;
				$tot_rcv+=$val["recv_qnty"];
				$tot_issue+=$val["issue_qnty"];
				$tot_left_ov+=$left_overs_qnty;
			}
			
		'</tbody>';
		
		$html2.='<tfoot>
					<th width="50"></th>
					<th width="170">Grand Total: </th>
					<th width="70"></th>
					<th width="100">'.round($tot_rcv).'</th>
					<th width="100">'.round($tot_issue).'</th>
					<th>'.round($tot_left_ov).'</th>
				</tfoot>
	</table>

	</fieldset>';
	

	
	foreach (glob("bwffsr_$user_id*.xlsx") as $filename) {
		@unlink($filename);
	}
	$name=time();
	$filename='bwffsr_'.$user_id."_".$name.".xlsx";
	//echo "$html2####$filename"; die;
	//echo $filename;die;
	
	$reader = new \PhpOffice\PhpSpreadsheet\Reader\Html();
	$spreadsheet = $reader->loadFromString($html);
	
	//$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'xlsx');
	//$writer->save($filename);
	$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
	$writer->save($filename);  

	echo "$html2####$filename####5"; 
	exit();
}

if ($action=="report_generate_kal")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company=str_replace("'","",$cbo_company_id);
	$cbo_buyer=str_replace("'","",$cbo_buyer_id);
	$all_style=str_replace("'","",$txt_style);
	$cbo_product_department=str_replace("'","",$cbo_product_department);
    $cbo_team_leader=str_replace("'","",$cbo_team_leader);
	$all_style=implode(",",array_unique(explode(",",$all_style)));
	$txt_order_no='';//pob
	$txt_order_no_id='';//40041
	$txt_ref_no=str_replace("'","",$txt_ref_no);

	$from_date=str_replace("'","",$txt_date_from);
	$to_date=str_replace("'","",$txt_date_to);

	$all_style_no=explode(",",str_replace("'","",$txt_style_id));
	$all_style_quted="";
	foreach($all_style_no as $style_no)
	{
		$all_style_quted.="'".$style_no."'".",";
	}
	$all_style_quted=chop($all_style_quted,",");
	//echo $all_style_quted;die;
	$cbo_store_name = str_replace("'","",$cbo_store_name);

	$txt_job_no = str_replace("'","",$txt_job_no);
	$cbo_item_group = str_replace("'","",$cbo_item_group);
	
	$item_description = str_replace("'","",$txt_item_description);
	$item_description_id = str_replace("'","",$txt_item_description_id);
	$value_with = str_replace("'","",$cbo_value_with);
	$get_upto = str_replace("'","",$cbo_get_upto);
	$txt_days = str_replace("'","",$txt_days);
	$get_upto_qnty = str_replace("'","",$cbo_get_upto_qnty);
	$txt_qnty = str_replace("'","",$txt_qnty);
	$shipping_status = str_replace("'","",$shipping_status);
	//if($from_date !="" && $to_date !="") $sql_date_cond.=" and b.transaction_date between '$from_date' and '$to_date'";


	$current_date=date("d-m-Y");
	$p=1;
	$queryText = sql_select("select ID, COMPANY_ID, CON_DATE, CONVERSION_RATE from currency_conversion_rate where currency=2 and status_active=1 and is_deleted=0 and COMPANY_ID>0 order by COMPANY_ID, ID");
	$company_wise_data=array();
	foreach($queryText as $row)
	{
		$company_wise_data[$row["COMPANY_ID"]]++;
	}
	//echo "<pre>";print_r($company_wise_data);die;
	//echo "select ID, COMPANY_ID, CON_DATE, CONVERSION_RATE from currency_conversion_rate where currency=2 and status_active=1 and is_deleted=0 and COMPANY_ID>0 order by COMPANY_ID, ID";die;
	//echo count($queryText);die;
	$conversion_data_arr=array();$previous_date="";$company_check_arr=array();
	foreach($queryText as $val)
	{
		if($company_check_arr[$val["COMPANY_ID"]]=="")
		{
			$company_check_arr[$val["COMPANY_ID"]]=$val["COMPANY_ID"];
			$conversion_data_arr[$val["COMPANY_ID"]][change_date_format($val["CON_DATE"])]=$val["CONVERSION_RATE"];
			$sStartDate = date("Y-m-d", strtotime($val["CON_DATE"]));
			$sCurrentDate = $sStartDate;
			$sEndDate = $sStartDate;
			$previous_date=$sStartDate;
			$previous_rate=$val["CONVERSION_RATE"];
			//echo $sCurrentDate."=".$sEndDate."=".$val["CONVERSION_RATE"]."<br>";
			
			$sStartDate=date("Y-m-d", strtotime("+1 day", strtotime($val["CON_DATE"])));
			$sEndDate = date("Y-m-d", strtotime($current_date));
			$sCurrentDate = $sStartDate;
			//echo $sCurrentDate."=".$sEndDate."=".$val["CONVERSION_RATE"]."<br>";
			while ($sCurrentDate <= $sEndDate) {
				
				$conversion_data_arr[$val["COMPANY_ID"]][change_date_format($sCurrentDate)]=$val["CONVERSION_RATE"];
				$sCurrentDate = date("Y-m-d", strtotime("+1 day", strtotime($sCurrentDate)));
			}
			$q=1;
		}
		else
		{
			$q++;
			$sStartDate = date("Y-m-d", strtotime($previous_date));
			if($company_wise_data[$val["COMPANY_ID"]]==$q)
			{
				$sEndDate = gmdate("Y-m-d", strtotime($val["CON_DATE"]));
				$sCurrentDate = $sStartDate;
				//echo $sCurrentDate."=".$sEndDate."=".$previous_rate."<br>";
				while ($sCurrentDate <= $sEndDate) {
					$conversion_data_arr[$val["COMPANY_ID"]][change_date_format($sCurrentDate)]=$previous_rate;
					$sCurrentDate = date("Y-m-d", strtotime("+1 day", strtotime($sCurrentDate)));
				}
				
				$sStartDate=date("Y-m-d", strtotime("+1 day", strtotime($sEndDate)));
				$sEndDate = date("Y-m-d", strtotime($current_date));
				$sCurrentDate = $sStartDate;
				//echo $sCurrentDate."=".$sEndDate."=".$val["CONVERSION_RATE"]."<br>";
				while ($sCurrentDate <= $sEndDate) {
					
					$conversion_data_arr[$val["COMPANY_ID"]][change_date_format($sCurrentDate)]=$val["CONVERSION_RATE"];
					$sCurrentDate = date("Y-m-d", strtotime("+1 day", strtotime($sCurrentDate)));
				}
				$previous_date=$val["CON_DATE"];
				$previous_rate=$val["CONVERSION_RATE"];
			}
			else
			{
				$sEndDate = gmdate("Y-m-d", strtotime($val["CON_DATE"]));
				$sCurrentDate = $sStartDate;
				//echo $sCurrentDate."=".$sEndDate."=".$previous_rate."<br>";
				while ($sCurrentDate <= $sEndDate) {
					$conversion_data_arr[$val["COMPANY_ID"]][change_date_format($sCurrentDate)]=$previous_rate;
					$sCurrentDate = date("Y-m-d", strtotime("+1 day", strtotime($sCurrentDate)));
				}
				$previous_date=$val["CON_DATE"];
				$previous_rate=$val["CONVERSION_RATE"];
			}
		}
		$p++;
	}
	unset($queryText);

	$sql_cond="";
	if($cbo_buyer>0)
	{
		$sql_cond=" and e.buyer_name=$cbo_buyer";
	}

	if($txt_ref_no !="") $sql_cond .=" and d.grouping='$txt_ref_no'";
	if($all_style !="") $sql_cond .=" and e.id in(".$all_style.")";
	if($txt_job_no !="") $sql_cond .=" and e.job_no_prefix_num='$txt_job_no'";
	if($cbo_product_department > 0) $sql_cond .=" and e.product_dept=$cbo_product_department";
	if($cbo_team_leader > 0) $sql_cond .=" and e.team_leader=$cbo_team_leader";


	if($cbo_store_name!=0) $store_cond=" and b.store_id =".str_replace("'","",$cbo_store_name); else $store_cond="";
	if ($cbo_item_group!="") { $item_group_cond = " and a.item_group_id in($cbo_item_group)"; }
	if ($item_description!="") { $item_description_cond = " and a.item_description='$item_description'"; }


	$main_query = "SELECT A.ID AS PRODUCT_ID, A.ITEM_COLOR AS ITEM_COLOR_ID, A.ITEM_SIZE, A.ITEM_GROUP_ID, A.ITEM_DESCRIPTION, A.AVG_RATE_PER_UNIT, C.PO_BREAKDOWN_ID, C.QUANTITY,  C.ENTRY_FORM, C.TRANS_TYPE, E.BUYER_NAME, E.JOB_NO, E.JOB_NO_PREFIX_NUM, E.STYLE_REF_NO, E.TOTAL_SET_QNTY, D.ID AS PO_ID, D.PO_NUMBER, D.FILE_NO, D.GROUPING AS INT_REF_NO, D.PO_QUANTITY, A.UNIT_OF_MEASURE, B.CONS_RATE AS RATE, B.STORE_ID, B.TRANSACTION_TYPE, B.ID AS TRANS_ID, B.CONS_QUANTITY, B.CONS_AMOUNT, B.COMPANY_ID, B.TRANSACTION_DATE, B.ORDER_UOM, B.PROD_ID, C.ORDER_AMOUNT, C.ORDER_AMOUNT, C.QUANTITY, B.ORDER_RATE, B.MST_ID, c.ORDER_RATE as PO_ORDER_RATE 
	from product_details_master a, inv_transaction b, order_wise_pro_details c, wo_po_break_down d, wo_po_details_master e
	where a.id=b.prod_id and b.id=c.trans_id and c.po_breakdown_id=d.id and d.job_no_mst=e.job_no and c.entry_form in(24,25,49,73,78,112) and a.entry_form=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 AND c.quantity > 0 and e.company_name=$cbo_company $store_cond $sql_cond $item_group_cond $item_description_cond $sql_date_cond 
	ORDER BY A.ID, B.ID";
	//and a.id=18848 and d.id in(12178,10310)
	//echo $main_query."<br>";die;
	$sql_data_arr=sql_select($main_query);
	$dtls_data=array();$order_wise_rate=array();
    
	foreach($sql_data_arr as $row)
	{
		$item_key = $row['ITEM_GROUP_ID'].'*'.$row['ITEM_DESCRIPTION'].'*'.$row['ITEM_COLOR_ID'].'*'.$row['ITEM_SIZE'];

		$dtls_data[$row['JOB_NO']][$item_key]['BUYER_NAME']=$row['BUYER_NAME'];
		$dtls_data[$row['JOB_NO']][$item_key]['JOB_PREFIX']=$row['JOB_NO_PREFIX_NUM'];
		$dtls_data[$row['JOB_NO']][$item_key]['INT_REF_NO']=$row['INT_REF_NO'];
		$dtls_data[$row['JOB_NO']][$item_key]['STYLE']=$row['STYLE_REF_NO'];
		$dtls_data[$row['JOB_NO']][$item_key]['ORDER_UOM']=$row['ORDER_UOM'];
		$dtls_data[$row['JOB_NO']][$item_key]['UOM']=$row['UNIT_OF_MEASURE'];
		$dtls_data[$row['JOB_NO']][$item_key]['STORE']=$row['STORE_ID'];
		
		$dtls_data[$row['JOB_NO']][$item_key]['PROD_ID']=$row['PROD_ID'];
		$dtls_data[$row['JOB_NO']][$item_key]['AVG_RATE_PER_UNIT']=$row['AVG_RATE_PER_UNIT'];
		
		if($prod_check[$row['JOB_NO']][$item_key]=="")
		{
			$prod_check[$row['JOB_NO']][$item_key]=$item_key;
			$dtls_data[$row['JOB_NO']][$item_key]['PRODUCT_ID'].=$row['PRODUCT_ID'].',';
		}
		
		
		
		if($row["TRANSACTION_TYPE"]==1) // Receive
		{
			$exchange_rate = $conversion_data_arr[$row["COMPANY_ID"]][change_date_format($row["TRANSACTION_DATE"])];
			$mrr_amt_tk=$row["QUANTITY"]*($row["ORDER_RATE"]*$exchange_rate);
			$order_wise_rate[$row["PRODUCT_ID"]][$row["PO_ID"]]["quantity"]+=$row["QUANTITY"];
			$order_wise_rate[$row["PRODUCT_ID"]][$row["PO_ID"]]["mrr_amt_tk"]+=$mrr_amt_tk;
			// $exchange_rate = ($row["CURRENCY_ID"]==1) ? 1 : $conversion_rate[$row["CURRENCY_ID"]];
			if(strtotime($row["TRANSACTION_DATE"])<strtotime($from_date))
			{
				$dtls_data[$row['JOB_NO']][$item_key]['rcv_total_opening_qty']+=$row["QUANTITY"];
				$dtls_data[$row['JOB_NO']][$item_key]['rcv_total_opening_amt']+=$mrr_amt_tk;
				$dtls_data[$row['JOB_NO']][$item_key]['currency_id'].=$currency_id_rcv.',';
			}
			else if( (strtotime($row["TRANSACTION_DATE"])>=strtotime($from_date)) && (strtotime($row["TRANSACTION_DATE"])<=strtotime($to_date)) )
			{
				$dtls_data[$row['JOB_NO']][$item_key]['receive_qty']+=$row["QUANTITY"];
				$dtls_data[$row['JOB_NO']][$item_key]['receive_amt']+=$mrr_amt_tk;
				$dtls_data[$row['JOB_NO']][$item_key]['currency_id'].=$currency_id_rcv.',';
			}
			$trans_rate=0;
			if($order_wise_rate[$row["PRODUCT_ID"]][$row["PO_ID"]]["mrr_amt_tk"]!=0 && $order_wise_rate[$row["PRODUCT_ID"]][$row["PO_ID"]]["quantity"] !=0)
			{
				$trans_rate=$order_wise_rate[$row["PRODUCT_ID"]][$row["PO_ID"]]["mrr_amt_tk"]/$order_wise_rate[$row["PRODUCT_ID"]][$row["PO_ID"]]["quantity"];
			}
		}
		else if($row["TRANSACTION_TYPE"]==2) // Issue
		{
			if($order_wise_rate[$row["PRODUCT_ID"]][$row["PO_ID"]]["mrr_amt_tk"]!=0 && $order_wise_rate[$row["PRODUCT_ID"]][$row["PO_ID"]]["quantity"] !=0)
			{
				$trans_rate=$order_wise_rate[$row["PRODUCT_ID"]][$row["PO_ID"]]["mrr_amt_tk"]/$order_wise_rate[$row["PRODUCT_ID"]][$row["PO_ID"]]["quantity"];
			}
			
			//echo $trans_rate."<br>";
			if(strtotime($row["TRANSACTION_DATE"])<strtotime($from_date))
			{
				$dtls_data[$row['JOB_NO']][$item_key]['iss_total_opening_qty']+=$row["QUANTITY"];
				$dtls_data[$row['JOB_NO']][$item_key]['iss_total_opening_amt']+=$row["QUANTITY"]*$trans_rate;
			}
			else if( (strtotime($row["TRANSACTION_DATE"])>=strtotime($from_date)) && (strtotime($row["TRANSACTION_DATE"])<=strtotime($to_date)) )
			{
				$dtls_data[$row['JOB_NO']][$item_key]['issue_qty']+=$row["QUANTITY"];
				$dtls_data[$row['JOB_NO']][$item_key]['issue_amt']+=$row["QUANTITY"]*$trans_rate;
			}
			$order_wise_rate[$row["PRODUCT_ID"]][$row["PO_ID"]]["quantity"]-=$row["QUANTITY"];
			$order_wise_rate[$row["PRODUCT_ID"]][$row["PO_ID"]]["mrr_amt_tk"]-=$row["QUANTITY"]*$trans_rate;
		}
		else if($row["TRANSACTION_TYPE"]==3) // Receive Return
		{
			if($order_wise_rate[$row["PRODUCT_ID"]][$row["PO_ID"]]["mrr_amt_tk"]!=0 && $order_wise_rate[$row["PRODUCT_ID"]][$row["PO_ID"]]["quantity"] !=0)
			{
				$trans_rate=$order_wise_rate[$row["PRODUCT_ID"]][$row["PO_ID"]]["mrr_amt_tk"]/$order_wise_rate[$row["PRODUCT_ID"]][$row["PO_ID"]]["quantity"];
			}
			
			if(strtotime($row["TRANSACTION_DATE"])<strtotime($from_date))
			{
				$dtls_data[$row['JOB_NO']][$item_key]['receive_return_opening_qty']+=$row["QUANTITY"];
				$dtls_data[$row['JOB_NO']][$item_key]['receive_return_opening_amt']+=$row["QUANTITY"]*$trans_rate;
			}
			else if( (strtotime($row["TRANSACTION_DATE"])>=strtotime($from_date)) && (strtotime($row["TRANSACTION_DATE"])<=strtotime($to_date)) )
			{
				$dtls_data[$row['JOB_NO']][$item_key]['receive_return_qty']+=$row["QUANTITY"];
				$dtls_data[$row['JOB_NO']][$item_key]['receive_return_amt']+=$row["QUANTITY"]*$trans_rate;
			}
			$order_wise_rate[$row["PRODUCT_ID"]][$row["PO_ID"]]["quantity"]-=$row["QUANTITY"];
			$order_wise_rate[$row["PRODUCT_ID"]][$row["PO_ID"]]["mrr_amt_tk"]-=$row["QUANTITY"]*$trans_rate;
		}
		else if($row["TRANSACTION_TYPE"]==4) // Issue Return
		{
			//echo $order_wise_rate[$row["PRODUCT_ID"]][$row["PO_ID"]]["mrr_amt_tk"]."=".$order_wise_rate[$row["PRODUCT_ID"]][$row["PO_ID"]]["quantity"]."<br>";
			if($order_wise_rate[$row["PRODUCT_ID"]][$row["PO_ID"]]["mrr_amt_tk"]!=0 && $order_wise_rate[$row["PRODUCT_ID"]][$row["PO_ID"]]["quantity"] !=0)
			{
				$trans_rate=$order_wise_rate[$row["PRODUCT_ID"]][$row["PO_ID"]]["mrr_amt_tk"]/$order_wise_rate[$row["PRODUCT_ID"]][$row["PO_ID"]]["quantity"];
			}
			if(strtotime($row["TRANSACTION_DATE"])<strtotime($from_date))
			{
				$dtls_data[$row['JOB_NO']][$item_key]['issue_return_opening_qty']+=$row["QUANTITY"];
				$dtls_data[$row['JOB_NO']][$item_key]['issue_return_opening_amt']+=$row["QUANTITY"]*$trans_rate;
			}
			else if( (strtotime($row["TRANSACTION_DATE"])>=strtotime($from_date)) && (strtotime($row["TRANSACTION_DATE"])<=strtotime($to_date)) )
			{
				$dtls_data[$row['JOB_NO']][$item_key]['issue_return_qty']+=$row["QUANTITY"];
				$dtls_data[$row['JOB_NO']][$item_key]['issue_return_amt']+=$row["QUANTITY"]*$trans_rate;
			}
			
			$order_wise_rate[$row["PRODUCT_ID"]][$row["PO_ID"]]["quantity"]+=$row["QUANTITY"];
			$order_wise_rate[$row["PRODUCT_ID"]][$row["PO_ID"]]["mrr_amt_tk"]+=$row["QUANTITY"]*$trans_rate;
			
		}
		else if($row["TRANSACTION_TYPE"]==5) // Item Transfer Receive
		{
			$exchange_rate=$conversion_data_arr[$row["COMPANY_ID"]][change_date_format($row["TRANSACTION_DATE"])];
			//echo $row["QUANTITY"]."=".$row["PO_ORDER_RATE"]."=".$exchange_rate."<br>";
			if($row["PO_ORDER_RATE"]>0)
			{
				$mrr_amt_tk=$row["QUANTITY"]*($row["PO_ORDER_RATE"]*$exchange_rate);
			}
			else
			{
				$mrr_amt_tk=$row["QUANTITY"]*$row["RATE"];
			}
			
			$order_wise_rate[$row["PRODUCT_ID"]][$row["PO_ID"]]["quantity"]+=$row["QUANTITY"];
			$order_wise_rate[$row["PRODUCT_ID"]][$row["PO_ID"]]["mrr_amt_tk"]+=$mrr_amt_tk;
			if(strtotime($row["TRANSACTION_DATE"])<strtotime($from_date))
			{
				$dtls_data[$row['JOB_NO']][$item_key]['item_transfer_receive_opening_qty']+=$row["QUANTITY"];
				$dtls_data[$row['JOB_NO']][$item_key]['item_transfer_receive_opening_amt']+=$mrr_amt_tk;
			}
			else if( (strtotime($row["TRANSACTION_DATE"])>=strtotime($from_date)) && (strtotime($row["TRANSACTION_DATE"])<=strtotime($to_date)) )
			{
				$dtls_data[$row['JOB_NO']][$item_key]['item_transfer_receive_qty']+=$row["QUANTITY"];
				$dtls_data[$row['JOB_NO']][$item_key]['item_transfer_receive_amt']+=$mrr_amt_tk;
			}
			
			$trans_rate=0;
			if($order_wise_rate[$row["PRODUCT_ID"]][$row["PO_ID"]]["mrr_amt_tk"]!=0 && $order_wise_rate[$row["PRODUCT_ID"]][$row["PO_ID"]]["quantity"] !=0)
			{
				$trans_rate=$order_wise_rate[$row["PRODUCT_ID"]][$row["PO_ID"]]["mrr_amt_tk"]/$order_wise_rate[$row["PRODUCT_ID"]][$row["PO_ID"]]["quantity"];
			}
		}
		else if($row["TRANSACTION_TYPE"]==6) // Item Transfer Issue
		{
			if($order_wise_rate[$row["PRODUCT_ID"]][$row["PO_ID"]]["mrr_amt_tk"]!=0 && $order_wise_rate[$row["PRODUCT_ID"]][$row["PO_ID"]]["quantity"] !=0)
			{
				$trans_rate=$order_wise_rate[$row["PRODUCT_ID"]][$row["PO_ID"]]["mrr_amt_tk"]/$order_wise_rate[$row["PRODUCT_ID"]][$row["PO_ID"]]["quantity"];
			}
			
			if(strtotime($row["TRANSACTION_DATE"])<strtotime($from_date))
			{
				$dtls_data[$row['JOB_NO']][$item_key]['item_transfer_issue_opening_qty']+=$row["QUANTITY"];
				$dtls_data[$row['JOB_NO']][$item_key]['item_transfer_issue_opening_amt']+=$row["QUANTITY"]*$trans_rate;
			}
			else if( (strtotime($row["TRANSACTION_DATE"])>=strtotime($from_date)) && (strtotime($row["TRANSACTION_DATE"])<=strtotime($to_date)) )
			{
				$dtls_data[$row['JOB_NO']][$item_key]['item_transfer_issue']+=$row["QUANTITY"];
				$dtls_data[$row['JOB_NO']][$item_key]['item_transfer_issue_amt']+=$row["QUANTITY"]*$trans_rate;
			}
			$order_wise_rate[$row["PRODUCT_ID"]][$row["PO_ID"]]["quantity"]-=$row["QUANTITY"];
			$order_wise_rate[$row["PRODUCT_ID"]][$row["PO_ID"]]["mrr_amt_tk"]-=$row["QUANTITY"]*$trans_rate;
		}
		
		if(($row["TRANSACTION_TYPE"]==1 || $row["TRANSACTION_TYPE"]==5) && strtotime($row["TRANSACTION_DATE"])<=strtotime($to_date) )
		{
			$dtls_data[$row['JOB_NO']][$item_key]['last_transaction_date']=$row["TRANSACTION_DATE"];
		}
		
		if($trns_prod_check[$row["PRODUCT_ID"]]=="")
		{
			$trns_prod_check[$row["PRODUCT_ID"]]=$row["PRODUCT_ID"];
			$dtls_data[$row['JOB_NO']][$item_key]['MIN_DATE']=$row["TRANSACTION_DATE"];
		}
		
		if(strtotime($row["TRANSACTION_DATE"])<=strtotime($to_date) )
		{
			$dtls_data[$row['JOB_NO']][$item_key]['MAX_DATE']=$row["TRANSACTION_DATE"];
		}
	}
	unset($sql_data_arr);
	//echo "<pre>";print_r($dtls_data);die;
	//echo "test".$value_with;die;
	
	$html='';
	$html2='';
    $html.='<table width="1800px">
            <tr class="form_caption">
                <td colspan="19" align="center" style="border:none;font-size:16px; font-weight:bold">'.$report_title.'</td>
            </tr>
            <tr class="form_caption">
                <td colspan="19" align="center">'.$company_library[$cbo_company].'</td>
            </tr>
        </table>';
		$html2.='<fieldset style="width:1800px;">
		<table width="1800px">
            <tr class="form_caption">
                <td colspan="19" align="center" style="border:none;font-size:16px; font-weight:bold">'.$report_title.'</td>
            </tr>
            <tr class="form_caption">
                <td colspan="19" align="center">'.$company_library[$cbo_company].'</td>
            </tr>
        </table>';
		$html.='<table cellspacing="0" cellpadding="0" border="1" rules="all" width="2000px" class="rpt_table" >
			<thead>
				<tr>
					<th width="40px" align="center"><b>SL </b></th>
					<th width="70px align="center"><b>Buyer</b></th>
					<th width="80px align="center"><b>Ref. No</b></th>
					<th width="70px align="center"><b>Job No</b></th>
					<th width="70px align="center"><b>Style</b></th>
					<th width="110px align="center"><b>Item Group</b></th>
					<th width="140px align="center"><b>Item Description</b></th>
					<th width="70px align="center"><b>Item Color</b></th>
					<th width="50px align="center"><b>Item Size</b></th>
					<th width="40px align="center"><b>UOM</b></th>
					
					<th width="70px" align="center"><b>Opaning Qty</b></th>
					<th width="90px" align="center"><b>Opaning Value</b></th>
					<th width="60px" align="center"><b>Recv. Qty</b></th>
					<th width="60px" align="center"><b>Transfer IN</b></th>
					<th width="60px" align="center"><b>Issue Return</b></th>
					<th width="70px" align="center"><b>Total Receive</b></th>
					<th width="60px" align="center"><b>Transfer Out</b></th>
					<th width="60px" align="center"><b>Issue Qty.</b></th>
					<th width="60px" align="center"><b>Rcv Return</b></th>
					<th width="70px" align="center"><b>Total Issue Qty</b></th>
					<th width="60px" align="center"><b>Stock Qty</b></th>
					<th width="60px" align="center"><b>Rate [BDT]</b></th>
					<th width="100px" align="center"><b>Stock Value (BDT)</b></th>
					<th width="60px" align="center"><b>Age(Days)</b></th>
					<th width="60px" align="center"><b>DOH</b></th>
					<th width="60px" align="center"><b>Prod Id</b></th>
					<th width="70px" align="center"><b>Last Rcv. Date</b></th>
					<th align="center"><b>Last trans. Date</b></th>
				</tr>
            </thead>
			<tbody>';
			
			$html2.='<table cellspacing="0" cellpadding="0" border="1" rules="all" width="2000px" class="rpt_table" >
			<thead>
				<tr bgcolor="#666666" style="font-weight:bold; font-size:14px;">
					<th width="40px">SL </th>
					<th width="70px">Buyer</th>
					<th width="80px">Ref. No</th>
					<th width="70px">Job No</th>
					<th width="70px">Style</th>
					<th width="110px">Item Group</th>
					<th width="140px">Item Description</th>
					<th width="70px">Item Color</th>
					<th width="50px">Item Size</th>
					<th width="40px">UOM</th>
					<th width="70px">Opaning Qty</th>
					<th width="90px">Opaning Value</th>
					<th width="60px">Recv. Qty</th>
					<th width="60px">Transfer IN</th>
					<th width="60px">Issue Return</th>
					<th width="70px">Total Receive</th>
					<th width="60px">Transfer Out</th>
					<th width="60px">Issue Qty.</th>
					<th width="60px">Rcv Return</th>
					<th width="70px">Total Issue Qty</th>
					<th width="60px">Stock Qty</th>
					<th width="60px">Rate [BDT]</th>
					<th width="100px">Stock Value (BDT)</th>
					<th width="60px">Age(Days)</th>
					<th width="60px">DOH</th>
					<th width="60px" align="center"><b>Prod Id</b></th>
					<th width="70px" align="center"><b>Last Rcv. Date</b></th>
					<th align="center"><b>Last trans. Date</b></th>
				</tr>
            </thead>
			</table>
			<div style="width:2000px; overflow-y:scroll; max-height:350px;font-size:12px; " id="scroll_body">
	        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1980px" class="rpt_table" id="tbl_issue_status" align="left">';

			    $store_name_arr = return_library_array("select a.id, a.store_name from lib_store_location a", 'id', 'store_name');
				$season_arr = return_library_array("select a.id, a.season_name from lib_buyer_season a", 'id', 'season_name');

				$i=1;
				if (count($dtls_data) == 0) 
				{
					echo "<span style='color: red;'>Data Not Found</span>";die;
				}
				
				//echo "<pre>";print_r($dtls_data);die;
				$total_stock_amount=0;
				$item_group_wise_data=array();
				foreach($dtls_data as $job_no=>$job_data)
				{
					foreach ($job_data as $item_key=>$item_data)
					{
						// echo $item_key.'<br>';
						$data = explode("*",$item_key);
						$item_group_id = $data[0];
						$item_description = $data[1];
						$item_color_id = $data[2];
						$item_size = $data[3];
						// echo $item_data["PROD_ID"].'<br>';
						// $ageOfDays = datediff("d",$date_array[$job_no][$item_data["PROD_ID"]]['MIN_DATE'],date("Y-m-d")); 
						// $daysOnHand = datediff("d",$date_array[$job_no][$item_data["PROD_ID"]]['MAX_DATE'],date("Y-m-d"));
						$prod_id_arr=array_unique(explode(",",chop($item_data["PROD_ID"],",")));
						//$ageOfDays = datediff("d",$date_array[$prod_id_arr[0]]['MIN_DATE'],date("Y-m-d"));
						$ageOfDays = datediff("d",$item_data['last_transaction_date'], date("Y-m-d",strtotime($to_date)));
						$daysOnHand = datediff("d",$item_data['MAX_DATE'], date("Y-m-d",strtotime($to_date)));
						// echo $ageOfDays.' = Test';
						$all_currency_ids=chop($item_data["currency_id"],",");
						$all_currency_id=implode(",", array_unique(explode(",",$all_currency_ids)));

						$only_recv_qty= $item_data['rcv_total_opening_qty']+$item_data['receive_qty'];
						$only_recv_amount= $item_data['rcv_total_opening_amt']+$item_data['receive_amt'];
						//$recv_rate=0;
						//if($only_recv_amount!=0 && $only_recv_qty!=0) $recv_rate = $only_recv_amount/$only_recv_qty;
						
						$only_transferIN_qty= $item_data['item_transfer_receive_opening_qty']+$item_data['item_transfer_receive_qty'];
						$only_transferIN_amount= $item_data['item_transfer_receive_opening_amt']+$item_data['item_transfer_receive_amt'];
						$transferIN_rate =0;
						if($only_transferIN_amount!=0 && $only_transferIN_qty!=0) $transferIN_rate = $only_transferIN_amount/$only_transferIN_qty;
						
						//if($recv_rate==0 && $transferIN_rate >0)
						//{
							//$recv_rate = $transferIN_rate;
						//}
						//echo $recv_rate."=".$transferIN_rate.test;die;

						// echo $item_data['receive_qty'].'+'.$item_data['issue_return_qty'].'+'.$item_data['item_transfer_receive_qty'].'<br>';
						$total_recv_qty = $item_data['receive_qty']+$item_data['issue_return_qty']+$item_data['item_transfer_receive_qty'];
						$total_recv_amount = $item_data['receive_amt']+$item_data['issue_return_amt']+$item_data['item_transfer_receive_amt'];

						$total_issue_qty = $item_data['issue_qty']+$item_data['receive_return_qty']+$item_data['item_transfer_issue'];
						$total_issue_amount = $item_data['issue_amt']+$item_data['receive_return_amt']+$item_data['item_transfer_issue_amt'];						

						$recv_opening_qty = $item_data['rcv_total_opening_qty']+$item_data['issue_return_opening_qty']+$item_data['item_transfer_receive_opening_qty'];
						$issue_opening_qty = $item_data['iss_total_opening_qty']+$item_data['receive_return_opening_qty']+$item_data['item_transfer_issue_opening_qty'];

						$recv_opening_amount = $item_data['rcv_total_opening_amt']+$item_data['issue_return_opening_amt']+$item_data['item_transfer_receive_opening_amt'];
						$issue_opening_amount = $item_data['iss_total_opening_amt']+$item_data['receive_return_opening_amt']+$item_data['item_transfer_issue_opening_amt'];
						$opening_bal=$opening_amunt=0;
						$opening_bal = $recv_opening_qty-$issue_opening_qty;
						if(number_format($opening_bal,2,'.','')!=0) 
						{
							$opening_amunt = $recv_opening_amount-$issue_opening_amount;
						}
						
						$stock_qty=$stock_amount_rcv_rate_wise=0;$recv_rate=0;
						$stock_qty=$opening_bal+$total_recv_qty-$total_issue_qty;
						if(number_format($stock_qty,2,'.','')!=0) 
						{
							//$stock_amount_rcv_rate_wise=$stock_qty*$recv_rate;
							$stock_amount_rcv_rate_wise=$opening_amunt+$total_recv_amount-$total_issue_amount;
							if(number_format($stock_amount_rcv_rate_wise,2,'.','')!=0)
							{
								$recv_rate=$stock_amount_rcv_rate_wise/$stock_qty;
							}
						}

						

						if((($get_upto==1 && $ageOfDays>$txt_days) || ($get_upto==2 && $ageOfDays<$txt_days) || ($get_upto==3 && $ageOfDays>=$txt_days) || ($get_upto==4 && $ageOfDays<=$txt_days) || ($get_upto==5 && $ageOfDays==$txt_days) || $get_upto==0) && (($get_upto_qnty==1 && $stock_qty>$txt_qnty) || ($get_upto_qnty==2 && $stock_qty<$txt_qnty) || ($get_upto_qnty==3 && $stock_qty>=$txt_qnty) || ($get_upto_qnty==4 && $stock_qty<=$txt_qnty) || ($get_upto_qnty==5 && $stock_qty==$txt_qnty) || $get_upto_qnty==0))
						{
							if($value_with==1)
							{
								if(number_format($stock_qty,2)>0.00)//||number_format($opening_bal,2)>0.00 
								{
									$html.='<tr>
										<td align="center">'.$i.'</td>
										<td>'.$buyer_arr[$item_data['BUYER_NAME']].'</td>
										<td>'.implode(",",array_unique(explode(",",chop($item_data["INT_REF_NO"],",")))).'</td>
										<td>'.$job_no.'</td>
										<td>';
										strlen($item_data['STYLE'])>20 ? $html.=substr($item_data['STYLE'], 0, 20) : $html.=$item_data['STYLE'];
										$html.='</td>
										<td>'.$trim_group[$item_group_id].'</td>
										<td>'.$item_description.'</td>
										<td>'.$color_library[$item_color_id].'</td>
										<td>';
										if($item_size!="0") $html.=$item_size; 
										$html.='</td>
										<td align="center">'.$unit_of_measurement[$item_data['UOM']].'</td>
										<td align="right">'.number_format($opening_bal,2,'.','').'</td>
										<td align="right" title="'.$recv_opening_amount.'='.$issue_opening_amount.'">'.number_format($opening_amunt,2,'.','').'</td>
										
										<td align="right">'.number_format($item_data['receive_qty'],2,'.','').'</td>
										<td align="right">'.number_format($item_data['item_transfer_receive_qty'],2,'.','').'</td>
										<td align="right">'.number_format($item_data['issue_return_qty'],2,'.','').'</td>
										<td align="right">'.number_format($total_recv_qty,2,'.','').'</td>

										<td align="right">'.number_format($item_data['item_transfer_issue'],2,'.','').'</td>
										<td align="right">'.number_format($item_data['issue_qty'],2,'.','').'</td>

										<td align="right">'.number_format($item_data['receive_return_qty'],2,'.','').'</td>
										<td align="right">'.number_format($total_issue_qty,2,'.','').'</td>

										<td align="right">'.number_format($stock_qty,2,'.','').'</td>
										<td align="right">'.number_format($recv_rate,2,'.','').'</td>

										<td align="right">'.number_format($stock_amount_rcv_rate_wise,2,'.','').'</td>
										<td align="center">'.$ageOfDays.'</td>
										<td align="center">'.$daysOnHand.'</td>
										<td align="center">'.chop($item_data['PRODUCT_ID'],",").'</td>
										<td align="center">'.change_date_format($item_data['last_transaction_date']).'</td>
										<td align="center">'.change_date_format($item_data['MAX_DATE']).'</td>
									</tr>';
									
									if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									$html2.='<tr bgcolor="'.$bgcolor.'"  onclick="change_color(\'tr_'.$i.'\',\''.$bgcolor.'\')" id="tr_'.$i.'" style="cursor:pointer">
										<td valign="middle" width="40" align="center">'.$i.'</td>
										<td valign="middle" width="70"><p>'.$buyer_arr[$item_data['BUYER_NAME']].'</p></td>
										<td valign="middle" width="80"><p>'.implode(",",array_unique(explode(",",chop($item_data["INT_REF_NO"],",")))).'</p></td>
										<td valign="middle" width="70"><p>'.$job_no.'</p></td>
										<td valign="middle" width="70"><p> ';
										strlen($item_data['STYLE'])>20 ? $html2.=substr($item_data['STYLE'], 0, 20) : $html2.=$item_data['STYLE'];
										$html2.='</p></td>
										<td valign="middle" width="110"><p>'.$trim_group[$item_group_id].'</p></td>
										<td valign="middle" width="140"><p>'.$item_description.'</p></td>
										<td valign="middle" width="70"><p>'.$color_library[$item_color_id].'</p></td>
										<td valign="middle" width="50"><p>';										
										if($item_size!="0") $html2.=$item_size; 
										$html2.='</p></td>
										<td valign="middle" width="40" align="center"><p>'.$unit_of_measurement[$item_data['UOM']].'</p></td>
										<td valign="middle" width="70" align="right">'.number_format($opening_bal,2,'.','').'</td>
										<td valign="middle" width="90" align="right" title="'.$recv_opening_amount.'='.$issue_opening_amount.'">'.number_format($opening_amunt,2,'.','').'</td>
										
										<td valign="middle" width="60" align="right">'.number_format($item_data['receive_qty'],2,'.','').'</td>
										<td valign="middle" width="60" align="right">'.number_format($item_data['item_transfer_receive_qty'],2,'.','').'</td>
										<td valign="middle" width="60" align="right">'.number_format($item_data['issue_return_qty'],2,'.','').'</td>
										<td valign="middle" width="70" align="right">'.number_format($total_recv_qty,2,'.','').'</td>

										<td valign="middle" width="60" align="right">'.number_format($item_data['item_transfer_issue'],2,'.','').'</td>
										<td valign="middle" width="60" align="right">'.number_format($item_data['issue_qty'],2,'.','').'</td>

										<td valign="middle" width="60" align="right">'.number_format($item_data['receive_return_qty'],2,'.','').'</td>
										<td valign="middle" width="70" align="right">'.number_format($total_issue_qty,2,'.','').'</td>

										<td valign="middle" width="60" align="right">'.number_format($stock_qty,2,'.','').'</td>
										<td valign="middle" width="60" align="right" title="'.$opening_amunt.'='.$total_recv_amount.'='.$total_issue_amount.'='.$item_data['receive_amt'].'='.$item_data['issue_return_amt'].'='.$item_data['item_transfer_receive_amt'].'">'.number_format($recv_rate,2,'.','').'</td>

										<td valign="middle" width="100" align="right">'.number_format($stock_amount_rcv_rate_wise,2,'.','').'</td>
										<td valign="middle" width="60" align="right">'.$ageOfDays.'</td>
										<td width="60" valign="middle" align="right">'.$daysOnHand.'</td>
										<td width="60" align="center">'.chop($item_data['PRODUCT_ID'],",").'</td>
										<td width="70" align="center">'.change_date_format($item_data['last_transaction_date']).'</td>
										<td align="center">'.change_date_format($item_data['MAX_DATE']).'</td>
									</tr>';
									
									$item_group_wise_data[$item_group_id]["uom"]=$item_data[('UOM')];
									$item_group_wise_data[$item_group_id]["recv_qnty"]+=$total_recv_qty;
									$item_group_wise_data[$item_group_id]["issue_qnty"]+=$total_issue_qty;
									
									$i++;
									$total_opening_bal+=$opening_bal;									
									$total_opening_amunt+=$opening_amunt;
									$grand_total_recv_qty+=$item_data['receive_qty'];
									$total_item_transfer_receive_qty+=$item_data['item_transfer_receive_qty'];
									$total_issue_return_qty+=$item_data['issue_return_qty'];
									
									$all_grand_total_recv_qty+=$item_data['receive_qty']+$item_data['item_transfer_receive_qty']+$item_data['issue_return_qty'];
									
									$total_only_issue_qty+=$item_data['issue_qty'];
									$total_item_transfer_issue+=$item_data['item_transfer_issue'];
									$total_receive_return_qty+=$item_data['receive_return_qty'];
									$grand_total_issue_qty+=$item_data['issue_qty']+$item_data['item_transfer_issue']+$item_data['receive_return_qty'];
									
									$total_stock_qty+=$stock_qty;
									//if($stock_amount_rcv_rate_wise>0) $total_stock_amount+=$stock_amount_rcv_rate_wise;
									$total_stock_amount+=$stock_amount_rcv_rate_wise;
								}
							}
							else
							{
								// number_format($stock_qty,3)>0.000 || number_format($opening_bal,3)>0.000 || number_format($total_recv_qty,3)>0.000 || number_format($total_issue_qty,3)>0.000
								if( number_format($stock_qty,2,'.','')>0.000 || number_format($opening_bal,2,'.','')>0.000 || number_format($total_recv_qty,2,'.','')>0.000  || number_format($total_issue_qty,2,'.','')>0.000)
								{
									$html.='<tr>
										<td align="center">'.$i.'</td>
										<td>'.$buyer_arr[$item_data['BUYER_NAME']].'</td>
										<td>'.implode(",",array_unique(explode(",",chop($item_data["INT_REF_NO"],",")))).'</td>
										<td>'.$job_no.'</td>
										<td>';
										strlen($item_data['STYLE'])>20 ? $html.=substr($item_data['STYLE'], 0, 20) : $html.=$item_data['STYLE'];
										$html.='</td>
										<td>'.$trim_group[$item_group_id].'</td>
										<td>'.$item_description.'</td>
										<td>'.$color_library[$item_color_id].'</td>
										<td>';
										if($item_size!="0") $html.=$item_size; 
										$html.='</td>
										<td align="center">'.$unit_of_measurement[$item_data['UOM']].'</td>
										<td align="right">'.number_format($opening_bal,2,'.','').'</td>
										<td align="right" title="'.$recv_opening_amount.'='.$issue_opening_amount.'">'.number_format($opening_amunt,2,'.','').'</td>
										
										<td align="right">'.number_format($item_data['receive_qty'],2,'.','').'</td>
										<td align="right">'.number_format($item_data['item_transfer_receive_qty'],2,'.','').'</td>
										<td align="right">'.number_format($item_data['issue_return_qty'],2,'.','').'</td>
										<td align="right">'.number_format($total_recv_qty,2,'.','').'</td>

										<td align="right">'.number_format($item_data['item_transfer_issue'],2,'.','').'</td>
										<td align="right">'.number_format($item_data['issue_qty'],2,'.','').'</td>

										<td align="right">'.number_format($item_data['receive_return_qty'],2,'.','').'</td>
										<td align="right">'.number_format($total_issue_qty,2,'.','').'</td>

										<td align="right">'.number_format($stock_qty,2,'.','').'</td>
										<td align="right">'.number_format($recv_rate,2,'.','').'</td>

										<td align="right">'.number_format($stock_amount_rcv_rate_wise,2,'.','').'</td>
										<td align="right">'.$ageOfDays.'</td>
										<td align="right">'.$daysOnHand.'</td>
										<td align="center">'.chop($item_data['PRODUCT_ID'],",").'</td>
										<td align="center">'.change_date_format($item_data['last_transaction_date']).'</td>
										<td align="center">'.change_date_format($item_data['MAX_DATE']).'</td>
									</tr>';
									
									if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									$html2.='<tr bgcolor="'.$bgcolor.'"  onclick="change_color(\'tr_'.$i.'\',\''.$bgcolor.'\')" id="tr_'.$i.'" style="cursor:pointer">
										<td valign="middle" width="40" align="center">'.$i.'</td>
										<td valign="middle" width="70"><p>'.$buyer_arr[$item_data['BUYER_NAME']].'</p></td>
										<td valign="middle" width="80"><p>'.implode(",",array_unique(explode(",",chop($item_data["INT_REF_NO"],",")))).'</p></td>
										<td valign="middle" width="70"><p>'.$job_no.'</p></td>
										<td valign="middle" width="70"><p> ';
										strlen($item_data['STYLE'])>20 ? $html2.=substr($item_data['STYLE'], 0, 20) : $html2.=$item_data['STYLE'];
										$html2.='</p></td>
										<td valign="middle" width="110"><p>'.$trim_group[$item_group_id].'</p></td>
										<td valign="middle" width="140"><p>'.$item_description.'</p></td>
										<td valign="middle" width="70"><p>'.$color_library[$item_color_id].'</p></td>
										<td valign="middle" width="50"><p>';										
										if($item_size!="0") $html2.=$item_size; 
										$html2.='</p></td>
										<td valign="middle" width="40" align="center"><p>'.$unit_of_measurement[$item_data['UOM']].'</p></td>
										<td valign="middle" width="70" align="right">'.number_format($opening_bal,2,'.','').'</td>
										<td valign="middle" width="90" align="right" title="'.$recv_opening_amount.'='.$issue_opening_amount.'">'.number_format($opening_amunt,2,'.','').'</td>
										
										<td valign="middle" width="60" align="right">'.number_format($item_data['receive_qty'],2,'.','').'</td>
										<td valign="middle" width="60" align="right">'.number_format($item_data['item_transfer_receive_qty'],2,'.','').'</td>
										<td valign="middle" width="60" align="right">'.number_format($item_data['issue_return_qty'],2,'.','').'</td>
										<td valign="middle" width="70" align="right">'.number_format($total_recv_qty,2,'.','').'</td>

										<td valign="middle" width="60" align="right">'.number_format($item_data['item_transfer_issue'],2,'.','').'</td>
										<td valign="middle" width="60" align="right">'.number_format($item_data['issue_qty'],2,'.','').'</td>

										<td valign="middle" width="60" align="right">'.number_format($item_data['receive_return_qty'],2,'.','').'</td>
										<td valign="middle" width="70" align="right">'.number_format($total_issue_qty,2,'.','').'</td>

										<td valign="middle" width="60" align="right">'.number_format($stock_qty,2,'.','').'</td>
										<td valign="middle" width="60" align="right">'.number_format($recv_rate,2,'.','').'</td>

										<td valign="middle" width="100" align="right">'.number_format($stock_amount_rcv_rate_wise,2,'.','').'</td>
										<td valign="middle" width="60" align="right">'.$ageOfDays.'</td>
										<td width="60" valign="middle" align="center">'.$daysOnHand.'</td>
										<td width="60" align="center">'.chop($item_data['PRODUCT_ID'],",").'</td>
										<td width="70" align="center">'.change_date_format($item_data['last_transaction_date']).'</td>
										<td align="center">'.change_date_format($item_data['MAX_DATE']).'</td>
									</tr>';
									

									$item_group_wise_data[$item_group_id]["uom"]=$item_data[('UOM')];
									$item_group_wise_data[$item_group_id]["recv_qnty"]+=$total_recv_qty;
									$item_group_wise_data[$item_group_id]["issue_qnty"]+=$total_issue_qty;
									
									$i++;
									$total_opening_bal+=$opening_bal;
									$total_opening_amunt+=$opening_amunt;
									$grand_total_recv_qty+=$item_data['receive_qty'];
									$total_item_transfer_receive_qty+=$item_data['item_transfer_receive_qty'];
									$total_issue_return_qty+=$item_data['issue_return_qty'];
									
									$all_grand_total_recv_qty+=$item_data['receive_qty']+$item_data['item_transfer_receive_qty']+$item_data['issue_return_qty'];
									
									
									$total_only_issue_qty+=$item_data['issue_qty'];
									$total_item_transfer_issue+=$item_data['item_transfer_issue'];
									$total_receive_return_qty+=$item_data['receive_return_qty'];
									$grand_total_issue_qty+=$item_data['issue_qty']+$item_data['item_transfer_issue']+$item_data['receive_return_qty'];
									
									$total_stock_qty+=$stock_qty;
									//if($stock_amount_rcv_rate_wise>0) $total_stock_amount+=$stock_amount_rcv_rate_wise;
									$total_stock_amount+=$stock_amount_rcv_rate_wise;
								}
							}
						}
					}
				}
				
                $html.='</tbody>
			<tfoot>
				<tr>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th>Grand Total</th>

					<th>'.number_format($total_opening_bal,2,'.','').'</th>
					<th>'.number_format($total_opening_amunt,2,'.','').'</th>
					<th>'.number_format($grand_total_recv_qty,2,'.','').'</th>
					<th>'.number_format($total_item_transfer_receive_qty,2,'.','').'</th>
					<th>'.number_format($total_issue_return_qty,2,'.','').'</th>
					<th>'.number_format($all_grand_total_recv_qty,2,'.','').'</th>
					<th>'.number_format($total_item_transfer_issue,2,'.','').'</th>
					<th>'.number_format($total_only_issue_qty,2,'.','').'</th>
					<th>'.number_format($total_receive_return_qty,2,'.','').'</th>
					<th>'.number_format($grand_total_issue_qty,2,'.','').'</th>
					<th>'.number_format($total_stock_qty,2,'.','').'</th>
					<th></th>
					<th>'.number_format($total_stock_amount,2,'.','').'</th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
				<tr>
            </tfoot>
        </table>';
		
		$html2.='</table>
		</div>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="2000px" class="rpt_table" >
			<tfoot>
				<th width="40">&nbsp;</th>
                <th width="70">&nbsp;</th>
                <th width="80">&nbsp;</th>
                <th width="70">&nbsp;</th>
                <th width="70">&nbsp;</th>
                <th width="110">&nbsp;</th>
                <th width="140">&nbsp;</th>
                <th width="70">&nbsp;</th>
                <th width="90">Grand Total</th>
                <th width="70" id="value_total_opening_qnty">'.number_format($total_opening_bal,2,'.','').'</th>
                <th width="90" id="value_total_opening_amunt">'.number_format($total_opening_amunt,2,'.','').'</th>
                <th width="60" id="value_grand_total_recv_qty">'.number_format($grand_total_recv_qty,2,'.','').'</th>
                <th width="60" id="value_total_item_transfer_receive_qty">'.number_format($total_item_transfer_receive_qty,2,'.','').'</th>
                <th width="60" id="value_total_issue_return_qty">'.number_format($total_issue_return_qty,2,'.','').'</th>
                <th width="70" id="value_total_recv_qty">'.number_format($all_grand_total_recv_qty,2,'.','').'</th>
                <th width="60" id="value_total_item_transfer_issue">'.number_format($total_item_transfer_issue,2,'.','').'</th>
                <th width="60" id="value_total_issue_qty">'.number_format($total_only_issue_qty,2,'.','').'</th>
                <th width="60" id="value_total_receive_return_qty">'.number_format($total_receive_return_qty,2,'.','').'</th>
                <th width="70" id="value_grand_total_issue_qty">'.number_format($grand_total_issue_qty,2,'.','').'</th>
                <th width="60" id="value_total_stock_qnty">'.number_format($total_stock_qty,2,'.','').'</th>
                <th width="60">&nbsp;</th>
                <th width="100" id="value_total_stock_amount">'.number_format($total_stock_amount,2,'.','').'</th>
                <th width="60">&nbsp;</th>
                <th width="60">&nbsp;</th>
				<th width="60">&nbsp;</th>
				<th width="70">&nbsp;</th>
				<th>&nbsp;</th>
            </tfoot>
        </table>';
	

	
	foreach (glob("bwffsr_$user_id*.xls") as $filename) {
		@unlink($filename);
	}
	$name=time();
	$filename='bwffsr_'.$user_id."_".$name.".xls";
	//echo "$html2####$filename"; die;
	//echo $filename;die;
	
	$reader = new \PhpOffice\PhpSpreadsheet\Reader\Html();
	$spreadsheet = $reader->loadFromString($html);

	$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');
	$writer->save($filename); 
	
	
	header('Content-Type: application/x-www-form-urlencoded');
	header('Content-Transfer-Encoding: Binary');
	header("Content-disposition: attachment; filename=\"".$filename."\"");

	echo "$html2####$filename####6"; 
	exit();
}


?>