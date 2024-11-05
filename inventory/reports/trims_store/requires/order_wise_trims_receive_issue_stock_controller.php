<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');
require_once('../../../../includes/class4/class.conditions.php');
require_once('../../../../includes/class4/class.reports.php');
require_once('../../../../includes/class4/class.trims.php');

$user_id=$_SESSION['logic_erp']['user_id'];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//---------------------------------------------------- Start---------------------------------------------------------------------------

if ($action=="load_drop_down_buyer")
{
	//$data=explode('_',$data);
	echo create_drop_down( "cbo_buyer_id", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",0);  
	exit();
}

if ($action=="load_drop_down_store")
{
	echo create_drop_down( "cbo_store_name", 115, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id='$data' and b.category_type=4 and a.status_active=1 and a.is_deleted=0 $store_location_credential_cond group by a.id, a.store_name order by a.store_name","id,store_name", 1, "--Select store--", 0, "" );
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
	if($db_type==0)
	{
		$year_field="YEAR(insert_date) as year";
		$year_cond=" and YEAR(insert_date)=".$data[2];
	} 
	else 
	{
		$year_field="to_char(insert_date,'YYYY') as year";
		$year_cond=" and to_char(insert_date,'YYYY')=".$data[2];
	}

	
	$sql ="select id,style_ref_no,job_no_prefix_num as job_prefix,$year_field from wo_po_details_master where $company_name $buyer_name $year_cond"; 
	echo create_list_view("list_view", "Style Ref. No.,Job No,Year","200,100,100","450","310",0, $sql , "js_set_value", "id,style_ref_no", "", 1, "0", $arr, "style_ref_no,job_prefix,year", "","setFilterGrid('list_view',-1)","0","",1) ;	
	exit();	 
}

if ($action=="order_no_popup")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);
	//print_r($data);die;
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
	{ //alert(id);
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
	$sql_cond="";
	if ($data[0]>0) $sql_cond=" and company_name=$data[0]";
	if ($data[1]>0) $sql_cond.=" and buyer_name=$data[1]";
	if ($data[2]!="") $sql_cond.=" and b.id in($data[2])";
	
	if($db_type==0)
	{
		$year_field="YEAR(b.insert_date) as year";
		$year_cond=" and YEAR(b.insert_date)=".$data[3];
	} 
	else 
	{
		$year_field="to_char(b.insert_date,'YYYY') as year";
		$year_cond=" and to_char(b.insert_date,'YYYY')=".$data[3];
	}

	$sql ="select distinct a.id,a.po_number,b.job_no_prefix_num as job_prefix,$year_field from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and a.status_active in(1,3) $sql_cond  $year_cond";
	//echo $sql; 
	echo create_list_view("list_view", "Order Number,Job No, Year","350,90","580","310",0, $sql , "js_set_value", "id,po_number", "", 1, "0", $arr, "po_number,job_prefix,year", "","setFilterGrid('list_view',-1)","0","",1) ;	
	exit();
}

if ($action=="report_generate_des")// Item Description wise Search.
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	$cbo_company=str_replace("'","",$cbo_company_id);
	$cbo_buyer=str_replace("'","",$cbo_buyer_id);
	$txt_style_id=str_replace("'","",$txt_style_id);
	$txt_style=str_replace("'","",$txt_style);
	$txt_order_no=str_replace("'","",$txt_order_no);
	$txt_order_id=str_replace("'","",$txt_order_no_id);
	$txt_int_ref_no=str_replace("'","",$txt_int_ref_no);
	$txt_file_no=str_replace("'","",$txt_file_no);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	$cbo_store_name=str_replace("'","",$cbo_store_name);
	
	
	
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$buyer_arr = return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$store_arr = return_library_array( "select id, store_name from lib_store_location",'id','store_name');
	//$trim_group= return_library_array( "select id, item_name from lib_item_group",'id','item_name');
	$sql_item_group=sql_select("select id, item_name, order_uom from lib_item_group ");
	foreach($sql_item_group as $row)
	{
		$item_group_arr[$row[csf("id")]]["item_name"]=$row[csf("item_name")];
		$item_group_arr[$row[csf("id")]]["order_uom"]=$row[csf("order_uom")];
	}
	$condition= new condition();
	$condition->company_name("=$cbo_company_id");
	if(str_replace("'","",$cbo_buyer_id)>0)
	{
		$condition->buyer_name("=$cbo_buyer_id");
	}
	if(str_replace("'","",$txt_style) !='')
	{
		$condition->jobid_in("$txt_style");
	}
	if(str_replace("'","",$txt_order_no_id)!='')
	{
		$condition->po_id("in(".str_replace("'","",$txt_order_no_id).")");
	}
	if(str_replace("'","",$txt_file_no) !='')
	{
		$condition->file_no("=$txt_file_no");
	}
	if(str_replace("'","",$txt_int_ref_no) !='')
	{
		$condition->grouping("='$txt_int_ref_no'");
	}
	if(str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!='')
	{
		$condition->pub_shipment_date(" between '$date_from' and '$date_to'");
	}
	$condition->init();
	
	$trims= new trims($condition);
	//echo $trims->getQuery();die;
	$trims_costing_arr=$trims->getQtyArray_by_orderAndItemid();
	//echo "<pre>";print_r($trims_costing_arr);die;
	
	$con = connect();
	$rID=execute_query("delete from gbl_temp_engine where user_id=$user_id and entry_form in (122)");
	if ($rID) oci_commit($con);

	ob_start();	
	?>
    <div style="width:1850px; margin-left:5px;">
    
        <table width="1830" cellspacing="0" cellpadding="0" border="0" rules="all"  >
            <tr class="form_caption">
                <td colspan="20" align="center" style="border:none;font-size:16px; font-weight:bold"> <? echo $report_title; ?></td>
            </tr>
            <tr class="form_caption">
                <td colspan="20" align="center"><? echo $company_library[$cbo_company]; ?></td>
            </tr>
        </table>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1830" class="rpt_table" >
            <thead>
                <th width="30">SL</th>
                <th width="100">Order No</th>
                <th width="110">Buyer Name</th>
                <th width="80">Style</th>
                <th width="50">RMG Qty.</th>
                <th width="65">RMG Qty(Dzn)</th>
				<th width="60">Prod. Id</th>
                <th width="130">Item Group</th>
				<th width="150">Descp.</th>
                <th width="40">UOM</th>
                <th width="70">Req. Qty</th>
                <th width="70">Recv. Qty</th>
                <th width="70">Recv. Value</th>
                <th width="70">Recv. Value[BDT]</th>
                <th width="70">Yet to Rev.</th>
                <th width="70">Issue Qty.</th>
                <th width="70">Left Over</th>
                <th width="60">Rate</th>
                <th width="50">Rate[BDT]</th>
                <th width="70">Left Over Val.</th>
                <th width="70">Left Over Val.[BDT]</th>
             	<th width="60">Int. Ref</th>
                <th width="60">File No</th>
                <th>Store Name</th>
            </thead>
        </table>
 		<div style="width:1850px; overflow-y:scroll; max-height:350px;font-size:12px; overflow-x:hidden;" id="scroll_body">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1830" class="rpt_table"  id="tbl_header" >
           <tbody>
		   <?	
		   		/*
				,
				sum(case when b.entry_form in(25) and b.trans_type in(2)  then b.order_amount else 0 end) as issue_amt,
				sum(case when b.entry_form in(73) and b.trans_type in(4)  then b.order_amount else 0 end) as issue_return_amt,
				sum(case when b.entry_form in(49)  and b.trans_type in(3) then b.order_amount else 0 end) as recv_return_amt,
				sum(case when b.entry_form in(78) and b.trans_type in(6)  then b.order_amount else 0 end) as item_transfer_issue_amt,
				sum(case when b.entry_form in(78)  and b.trans_type in(5) then b.order_amount else 0 end) as item_transfer_receive_amt
				*/
				$sql_cond="";
				if($cbo_company>0) $sql_cond .=" and d.company_name=$cbo_company";
				if($cbo_buyer>0) $sql_cond .=" and d.buyer_name=$cbo_buyer";
				if($txt_style!="") $sql_cond .=" and d.id in($txt_style)";
				if($txt_order_id!="") $sql_cond .=" and c.id in($txt_order_id)";
				if($txt_file_no!="") $sql_cond .=" and c.file_no ='$txt_file_no' ";
				if($txt_int_ref_no!="") $sql_cond .=" and c.grouping ='$txt_int_ref_no' ";
				if($date_from!="" && $date_to!="")  $sql_cond .=" and c.pub_shipment_date  between '$date_from' and '$date_to' ";
				
				if($cbo_store_name>0)
				{
					$sql_trim = "SELECT a.id as prod_id, a.item_group_id, a.product_name_details, a.item_description, a.item_color, a.item_size, a.color, a.gmts_size, d.job_no_prefix_num, d.job_no, d.company_name, d.buyer_name, d.style_ref_no, c.id as po_id, c.po_number, c.pub_shipment_date, c.grouping, c.file_no, c.po_quantity,
					sum(case when b.entry_form in(24) and b.trans_type in(1)  then b.quantity else 0 end) as rcv_qty,
					sum(case when b.entry_form in(25) and b.trans_type in(2)  then b.quantity else 0 end) as issue_qty,
					sum(case when b.entry_form in(73) and b.trans_type in(4)  then b.quantity else 0 end) as issue_return_qty,
					sum(case when b.entry_form in(49)  and b.trans_type in(3) then b.quantity else 0 end) as recv_return_qty,
					sum(case when b.entry_form in(78,112) and b.trans_type in(6)  then b.quantity else 0 end) as item_transfer_issue,
					sum(case when b.entry_form in(78,112)  and b.trans_type in(5) then b.quantity else 0 end) as item_transfer_receive,
					sum(case when b.entry_form in(24) and b.trans_type in(1)  then b.order_amount else 0 end) as rcv_amt,
					sum(case when b.entry_form in(24) and b.trans_type in(1) and t.ORDER_AMOUNT>0 then b.order_amount*(t.CONS_AMOUNT/t.ORDER_AMOUNT) else 0 end) as RCV_AMT_BDT,
					t.store_id
					from product_details_master a, inv_transaction t, order_wise_pro_details b, wo_po_break_down c, wo_po_details_master d
					where a.id=b.prod_id and a.id=t.prod_id and t.id=b.trans_id and b.po_breakdown_id=c.id and c.job_no_mst=d.job_no and a.id=b.prod_id and a.item_category_id=4 and b.entry_form in(24,25,78,73,49,112) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and t.store_id=$cbo_store_name $sql_cond 
					group by a.id, a.item_group_id, a.product_name_details, a.item_description, a.item_color, a.item_size, a.color, a.gmts_size, d.job_no_prefix_num, d.job_no, d.company_name, d.buyer_name, d.style_ref_no, c.id, c.po_number, c.pub_shipment_date, c.grouping, c.file_no, c.po_quantity, t.store_id
					order by c.id, a.item_group_id";
				}
				else
				{
					if($db_type==0)
					{
						$sql_trim = "SELECT a.id as prod_id, a.item_group_id, a.product_name_details, a.item_description, a.item_color, a.item_size, a.color, a.gmts_size, d.job_no_prefix_num, d.job_no, d.company_name, d.buyer_name, d.style_ref_no, c.id as po_id, c.po_number, c.pub_shipment_date, c.grouping, c.file_no, c.po_quantity,
						sum(case when b.entry_form in(24) and b.trans_type in(1)  then b.quantity else 0 end) as rcv_qty,
						sum(case when b.entry_form in(25) and b.trans_type in(2)  then b.quantity else 0 end) as issue_qty,
						sum(case when b.entry_form in(73) and b.trans_type in(4)  then b.quantity else 0 end) as issue_return_qty,
						sum(case when b.entry_form in(49)  and b.trans_type in(3) then b.quantity else 0 end) as recv_return_qty,
						sum(case when b.entry_form in(78,112) and b.trans_type in(6)  then b.quantity else 0 end) as item_transfer_issue,
						sum(case when b.entry_form in(78,112)  and b.trans_type in(5) then b.quantity else 0 end) as item_transfer_receive,
						sum(case when b.entry_form in(24) and b.trans_type in(1)  then b.order_amount else 0 end) as rcv_amt,
						sum(case when b.entry_form in(24) and b.trans_type in(1) and t.ORDER_AMOUNT>0 then b.order_amount*(t.CONS_AMOUNT/t.ORDER_AMOUNT) else 0 end) as RCV_AMT_BDT,
						group_concat(t.store_id) as store_id
						from product_details_master a, inv_transaction t, order_wise_pro_details b, wo_po_break_down c, wo_po_details_master d
						where a.id=b.prod_id and a.id=t.prod_id and t.id=b.trans_id and b.po_breakdown_id=c.id and c.job_no_mst=d.job_no and a.id=b.prod_id and a.item_category_id=4 and b.entry_form in(24,25,78,73,49,112) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sql_cond 
						group by a.id, a.item_group_id, a.product_name_details, a.item_description, a.item_color, a.item_size, a.color, a.gmts_size, d.job_no_prefix_num, d.job_no, d.company_name, d.buyer_name, d.style_ref_no, c.id, c.po_number, c.pub_shipment_date, c.grouping, c.file_no, c.po_quantity
						order by c.id, a.item_group_id";
					}
					else
					{
						$sql_trim = "SELECT a.id as prod_id, a.item_group_id, a.product_name_details, a.item_description, a.item_color, a.item_size, a.color, a.gmts_size, d.job_no_prefix_num, d.job_no, d.company_name, d.buyer_name, d.style_ref_no, c.id as po_id, c.po_number, c.pub_shipment_date, c.grouping, c.file_no, c.po_quantity,
						sum(case when b.entry_form in(24) and b.trans_type in(1)  then b.quantity else 0 end) as rcv_qty,
						sum(case when b.entry_form in(25) and b.trans_type in(2)  then b.quantity else 0 end) as issue_qty,
						sum(case when b.entry_form in(73) and b.trans_type in(4)  then b.quantity else 0 end) as issue_return_qty,
						sum(case when b.entry_form in(49)  and b.trans_type in(3) then b.quantity else 0 end) as recv_return_qty,
						sum(case when b.entry_form in(78,112) and b.trans_type in(6)  then b.quantity else 0 end) as item_transfer_issue,
						sum(case when b.entry_form in(78,112)  and b.trans_type in(5) then b.quantity else 0 end) as item_transfer_receive,
						sum(case when b.entry_form in(24) and b.trans_type in(1)  then b.order_amount else 0 end) as rcv_amt,
						sum(case when b.entry_form in(24) and b.trans_type in(1) and t.ORDER_AMOUNT>0 then b.order_amount*(t.CONS_AMOUNT/t.ORDER_AMOUNT) else 0 end) as RCV_AMT_BDT,
						LISTAGG(CAST(t.store_id AS VARCHAR2(4000)),',') WITHIN GROUP (ORDER BY t.store_id) as store_id
						from product_details_master a, inv_transaction t, order_wise_pro_details b, wo_po_break_down c, wo_po_details_master d
						where a.id=b.prod_id and a.id=t.prod_id and t.id=b.trans_id and b.po_breakdown_id=c.id and c.job_no_mst=d.job_no and a.id=b.prod_id and a.item_category_id=4 and b.entry_form in(24,25,78,73,49,112) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sql_cond 
						group by a.id, a.item_group_id, a.product_name_details, a.item_description, a.item_color, a.item_size, a.color, a.gmts_size, d.job_no_prefix_num, d.job_no, d.company_name, d.buyer_name, d.style_ref_no, c.id, c.po_number, c.pub_shipment_date, c.grouping, c.file_no, c.po_quantity
						order by c.id, a.item_group_id";
					}
				}
				
				// echo $sql_trim;//die;	
				$data_array=sql_select($sql_trim);
				$po_id_arr=array();
				$item_id_arr=array();
				foreach ($data_array as $row)
				{
					$po_id_arr[$row[csf('po_id')]] = $row[csf('po_id')];
					//$item_id_arr[$row[csf('item_group_id')]] = $row[csf('item_group_id')];

					 $item_group_id .= $row[csf('item_group_id')].",";
					// $po_id .= $row[csf('po_id')].",";
				}
				$all_item_group_id = ltrim(implode(",", array_unique(explode(",", chop($item_group_id, ",")))), ',');
				//$all_po_id = ltrim(implode(",", array_unique(explode(",", chop($po_id, ",")))), ',');
				if(!empty($po_id_arr))
				{
					fnc_tempengine("gbl_temp_engine", $user_id, 122, 1, $po_id_arr, $empty_arr);
					//fnc_tempengine("gbl_temp_engine", $user_id, 122, 2, $item_id_arr, $empty_arr);
		
					$rcv_mst_sql="SELECT A.ID as MST_ID, b.ITEM_GROUP_ID, A.RECV_NUMBER, C.PO_BREAKDOWN_ID, SUM(E.CONS_RATE) AS RATE ,E.ID,A.CURRENCY_ID
					FROM INV_RECEIVE_MASTER A, INV_TRIMS_ENTRY_DTLS B, ORDER_WISE_PRO_DETAILS C ,INV_TRANSACTION E,GBL_TEMP_ENGINE G
					WHERE A.ID=B.MST_ID  AND A.ENTRY_FORM=24 AND C.ENTRY_FORM=24  AND B.ID=C.DTLS_ID AND E.ID=C.TRANS_ID AND  C.TRANS_TYPE=1  AND  C.PO_BREAKDOWN_ID =G.REF_VAL  and a.company_id='$cbo_company' and b.item_group_id in ($all_item_group_id) and a.IS_DELETED=0 AND A.STATUS_ACTIVE=1 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND C.STATUS_ACTIVE=1 AND C.IS_DELETED=0 AND E.STATUS_ACTIVE=1 AND E.IS_DELETED=0 AND G.USER_ID= $user_id AND G.ENTRY_FORM=122 AND G.REF_FROM=1 
					GROUP BY C.PO_BREAKDOWN_ID,B.ITEM_GROUP_ID,A.RECV_NUMBER,A.ID,A.RECEIVE_DATE ,e.id,A.currency_id";
					//echo $rcv_mst_sql;
					$data_rcv_mst_sql=sql_select($rcv_mst_sql);
					$bdt_arr=array();
					foreach ($data_rcv_mst_sql as $row)
					{
						$bdt_arr[$row['ITEM_GROUP_ID']][$row['PO_BREAKDOWN_ID']]['bdt_rate']= $row['RATE'];
						$bdt_arr[$row['ITEM_GROUP_ID']][$row['PO_BREAKDOWN_ID']]['currency']= $row['CURRENCY_ID'];
					}

				}
				foreach ($data_array as $selectResult)
				{
					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
					$rcv_qnty=$selectResult[csf('rcv_qty')]+$selectResult[csf('issue_return_qty')]+$selectResult[csf('item_transfer_receive')];
					$issue_qnty=$selectResult[csf('issue_qty')]+$selectResult[csf('recv_return_qty')]+$selectResult[csf('item_transfer_issue')];
					if($selectResult[csf('rcv_qty')]>0 && $selectResult[csf('rcv_amt')] >0)
					{
						$rate=$selectResult[csf('rcv_amt')]/$selectResult[csf('rcv_qty')];
					}
					$rcv_value=$rcv_qnty*$rate;
					$issue_value=$issue_qnty*$rate;
					$req_qnty="";
					if($order_item_check[$selectResult[csf('po_id')]][$selectResult[csf('item_group_id')]]=="")
					{
						$order_item_check[$selectResult[csf('po_id')]][$selectResult[csf('item_group_id')]]=$selectResult[csf('item_group_id')];
						$req_qnty=$trims_costing_arr[$selectResult[csf('po_id')]][$selectResult[csf('item_group_id')]];
					}
					//$bdt_rate = ($selectResult['RCV_AMT_BDT']/$rcv_value);
					if($bdt_arr[$selectResult[csf('item_group_id')]][$selectResult[csf('po_id')]]['currency']==1){
						$bdt_rate= $bdt_arr[$selectResult[csf('item_group_id')]][$selectResult[csf('po_id')]]['bdt_rate'];
						$total_bdt_rate = $bdt_rate;
					}
					else{
						$bdt_rate = ($selectResult['RCV_AMT_BDT']/$rcv_value);
						$total_bdt_rate = $bdt_rate*$rate;
					}
					
					$yet_recv=$trims_costing_arr[$selectResult[csf('po_id')]][$selectResult[csf('item_group_id')]]-$rcv_qnty;
					$left_over=$rcv_qnty-$issue_qnty;
					?> 
                    <tr bgcolor="<? echo $bgcolor; ?>"  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <?
                    	if ($order_id_array[$selectResult[csf('po_id')]]=="")
                        {
                            $k++;
							?>
							<td width="30" align="center"> <? echo $k;?> </td>
							<td width="100"><p> <? echo $selectResult[csf('po_number')];?></p></td>
							<td width="110"><p><? echo $buyer_arr[$selectResult[csf('buyer_name')]];?></p></td>
							<td width="80"><p><? echo $selectResult[csf('style_ref_no')];?></p></td>
							<td width="50" align="right"> <? echo number_format($selectResult[csf('po_quantity')]);?> </td>
							<td width="65" align="right"><? echo number_format($selectResult[csf('po_quantity')]/12,0);?> </td>
							<? 
							$order_id_array[$selectResult[csf('po_id')]]=$selectResult[csf('po_id')];
                        }
                        else
                        {
							?>
							<td width="30">&nbsp;</td>
							<td width="100"><p>&nbsp;</p></td>
							<td width="110"><p>&nbsp;</p></td>
							<td width="80"><p>&nbsp;</p></td>
							<td width="50" align="right">&nbsp;</td>
							<td width="65" align="right">&nbsp;</td>	
							<?
                        } 
                    	?>
                        <td width="60" title="Prod. Id" align="center"><? echo $selectResult[csf('prod_id')]; ?></td>
                        <td width="130"><p><? echo $item_group_arr[$selectResult[csf('item_group_id')]]["item_name"]; ?></p></td>
                        <td width="150"><p><? echo $selectResult[csf('product_name_details')];   ?></p></td>
                        <td width="40" align="center"><p><? echo  $unit_of_measurement[$item_group_arr[$selectResult[csf('item_group_id')]]["order_uom"]]; ?></p></td>
                        <td width="70" title="Req. Qty" align="right"><? echo number_format($req_qnty,2); ?> </td>
                        <td width="70"  align="right" title="Received Qty"><a href='#report_details' onClick="openmypage_des('<? echo $selectResult[csf('po_id')]; ?>','<? echo $selectResult[csf('item_group_id')]; ?>','<? echo  $selectResult[csf('prod_id')];?>','receive_des_popup');"><? echo number_format($rcv_qnty,2); ?> </a>  </td>
                        <td width="70" align="right"> <? echo number_format($rcv_value,2); ?>  </td>
                        <td width="70" align="right"> <? echo number_format($selectResult['RCV_AMT_BDT'],2); ?> </td>
                        <td width="70" title="yet" align="right"> <? echo number_format($yet_recv,2); ?></td>
                        <td width="70" title="Issue Qty" align="right"><a href='#report_details' onClick="openmypage_des('<? echo $selectResult[csf('po_id')]; ?>','<? echo $selectResult[csf('item_group_id')]; ?>','<? echo  $selectResult[csf('prod_id')] ;?>','issue_des_popup');"><? echo number_format($issue_qnty,2); ?></a> </td>
                        <td width="70" title="Left Over"><? echo number_format($left_over,2); ?> </td>
                        <td width="50" title="Rate" align="right" > <? echo  number_format($rate,4); ?></td>
                        <td width="60" title="Rate BDT" align="right" > <? echo  number_format($total_bdt_rate,4); ?></td>
                        <td width="70" title="Left Over Value" align="right"><? $total_left=$left_over*$rate; 
						echo number_format($total_left,2); 
						$left_val+=$total_left; ?></td>
						<td width="70" title="Left Over Value BDT" align="right"><? $total_left_bdt=$left_over*$total_bdt_rate; 
						echo number_format($total_left_bdt,2); 
						$left_val_bdt+=$total_left_bdt; ?></td>
                        <td width="60" title="Internal Ref/Grouping"><? echo $selectResult[csf('grouping')];?></td>
                        <td width="60" title="File No"><? echo $selectResult[csf('file_no')];?></td>
                        <td>
						<?
						$store_id_arr=array_unique(explode(",",$selectResult[csf('store_id')]));
						$all_store_name="";
						foreach($store_id_arr as $store_id)
						{
							$all_store_name.=$store_arr[$store_id].",";
						}
						$all_store_name=chop($all_store_name,",");
						echo $all_store_name;
						?>
                        </td>
                    </tr>
                    <?
                    $i++;
                    $total_rec_value+=$rcv_value;
                    $total_rec_value_bdt+=$selectResult['RCV_AMT_BDT'];
				}
				?>     
                 
        </tbody>
        <tfoot>
            <tr>
                <th colspan="12" align="right"><b>Total:</b></th>
                <th align="right"><?  echo number_format($total_rec_value,2); ?></th>
                <th align="right"><?  echo number_format($total_rec_value_bdt,2); ?></th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th align="right"><? echo number_format($left_val,2); ?></th>
                <th align="right"><? echo number_format($left_val_bdt,2); ?></th> 
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
            </tr> 
        </tfoot>
    </table>
    </div>
    </div>
    <?
	$rID2=execute_query("delete from gbl_temp_engine where user_id=$user_id and entry_form in (122)");
	if ($rID2) oci_commit($con);
	disconnect($con);

    exit();
}
//report_generate //Item Group Wise Search.
if ($action=="report_generate")// Item Group Wise Search.
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	$cbo_company=str_replace("'","",$cbo_company_id);
	$cbo_buyer=str_replace("'","",$cbo_buyer_id);
	$txt_style_id=str_replace("'","",$txt_style_id);
	$txt_style=str_replace("'","",$txt_style);
	$txt_order_no=str_replace("'","",$txt_order_no);
	$txt_order_id=str_replace("'","",$txt_order_no_id);
	$txt_int_ref_no=str_replace("'","",$txt_int_ref_no);
	$txt_file_no=str_replace("'","",$txt_file_no);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	$cbo_store_name=str_replace("'","",$cbo_store_name);
	$cbo_value_with=str_replace("'","",$cbo_value_with);

	
	
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$buyer_arr = return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$store_arr = return_library_array( "select id, store_name from lib_store_location",'id','store_name');
	//$trim_group= return_library_array( "select id, item_name from lib_item_group",'id','item_name');
	$sql_item_group=sql_select("select id, item_name, order_uom from lib_item_group ");
	foreach($sql_item_group as $row)
	{
		$item_group_arr[$row[csf("id")]]["item_name"]=$row[csf("item_name")];
		$item_group_arr[$row[csf("id")]]["order_uom"]=$row[csf("order_uom")];
	}
	$condition= new condition();
	$condition->company_name("=$cbo_company_id");
	if(str_replace("'","",$cbo_buyer_id)>0)
	{
		$condition->buyer_name("=$cbo_buyer_id");
	}
	if(str_replace("'","",$txt_style) !='')
	{
		//$condition->style_ref_no(" in($all_style_quted)");
		$condition->jobid_in("$txt_style");
	}
	if(str_replace("'","",$txt_order_no_id)!='')
	{
		$condition->po_id("in(".str_replace("'","",$txt_order_no_id).")");
	}
	if(str_replace("'","",$txt_file_no) !='')
	{
		$condition->file_no("=$txt_file_no");
	}
	if(str_replace("'","",$txt_int_ref_no) !='')
	{
		$condition->grouping("='$txt_int_ref_no'");
	}
	if(str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!='')
	{
		$condition->pub_shipment_date(" between '$date_from' and '$date_to'");
	}
	$condition->init();
	
	$trims= new trims($condition);
	//echo $trims->getQuery();die;
	$trims_costing_arr=$trims->getQtyArray_by_orderAndItemid();
	//echo "<pre>";print_r($trims_costing_arr);die;
	$con = connect();
	$rID=execute_query("delete from gbl_temp_engine where user_id=$user_id and entry_form in (122)");
	if ($rID) oci_commit($con);

	ob_start();	
	?>
    <div style="width:1930px;">
    
        <table width="1910" cellspacing="0" cellpadding="0" border="0" rules="all"  >
            <tr class="form_caption">
                <td colspan="24" align="center" style="border:none;font-size:16px; font-weight:bold"> <? echo $report_title; ?></td>
            </tr>
            <tr class="form_caption">
                <td colspan="24" align="center"><? echo $company_library[$cbo_company]; ?></td>
            </tr>
        </table>
         <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1910" class="rpt_table" >
            <thead>
                <th width="30">SL</th>
                <th width="100">Order No</th>
                <th width="110">Buyer Name</th>
                <th width="80">Style</th>
                <th width="50">RMG Qty.</th>
                <th width="65">RMG Qty(Dzn)</th>
                <th width="130">Item Group</th>
                <th width="40">UOM</th>
                <th width="70">Req. Qty</th>
                <th width="70">Wo Qty</th>
                <th width="70">Wo Bal.</th>
                <th width="70">Recv. Qty</th>
                <th width="70">Recv. Value</th>
				<th width="70">Recv. Value[BDT]</th>
                <th width="70">Yet to Rev.</th>
                <th width="70">Issue Qty.</th>
                <th width="70">Stock</th>
                <th width="50">Rate</th>
				<th width="60">Rate[BDT]</th>
                <th width="70">Stock Value</th>
                <th width="70">Left Over</th>
                <th width="70">Left Over Val.</th>
				<th width="70">Left Over Val.[BDT]</th>
             	<th width="60">Int. Ref</th>
                <th width="60">File No</th>
                <th>Store Name</th>
            </thead>
        </table>
 		<div style="width:1930px; overflow-y:scroll; max-height:350px;font-size:12px; overflow-x:hidden;" id="scroll_body">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1910" class="rpt_table"  id="tbl_issue_status" >
           <tbody>
		   <?
				$sql_cond="";
				if($cbo_company>0) $sql_cond .=" and d.company_name=$cbo_company";
				if($cbo_buyer>0) $sql_cond .=" and d.buyer_name=$cbo_buyer";
				if($txt_style!="") $sql_cond .=" and d.id in($txt_style)";
				if($txt_order_id!="") $sql_cond .=" and c.id in($txt_order_id)";
				if($txt_file_no!="") $sql_cond .=" and c.file_no ='$txt_file_no' ";
				if($txt_int_ref_no!="") $sql_cond .=" and c.grouping ='$txt_int_ref_no' ";
				if($date_from!="" && $date_to!="")  $sql_cond .=" and c.pub_shipment_date  between '$date_from' and '$date_to' ";
				
				if($cbo_store_name>0)
				{
					$sql_trim = "SELECT a.item_group_id, d.job_no_prefix_num, d.job_no, d.company_name, d.buyer_name, d.style_ref_no, c.id as po_id, c.po_number, c.pub_shipment_date, c.grouping, c.file_no, c.po_quantity,
					sum(case when b.entry_form in(24) and b.trans_type in(1)  then b.quantity else 0 end) as rcv_qty,
					sum(case when b.entry_form in(25) and b.trans_type in(2)  then b.quantity else 0 end) as issue_qty,
					sum(case when b.entry_form in(73) and b.trans_type in(4)  then b.quantity else 0 end) as issue_return_qty,
					sum(case when b.entry_form in(49)  and b.trans_type in(3) then b.quantity else 0 end) as recv_return_qty,
					sum(case when b.entry_form in(78,112) and b.trans_type in(6)  then b.quantity else 0 end) as item_transfer_issue,
					sum(case when b.entry_form in(78,112)  and b.trans_type in(5) then b.quantity else 0 end) as item_transfer_receive,
					sum(case when b.entry_form in(24) and b.trans_type in(1)  then b.order_amount else 0 end) as rcv_amt,
					sum(case when b.entry_form in(24) and b.trans_type in(1) and t.ORDER_AMOUNT>0 then b.order_amount*(t.CONS_AMOUNT/t.ORDER_AMOUNT) else 0 end) as RCV_AMT_BDT,
					t.store_id
					from product_details_master a, inv_transaction t, order_wise_pro_details b, wo_po_break_down c, wo_po_details_master d
					where a.id=b.prod_id and a.id=t.prod_id and t.id=b.trans_id and b.po_breakdown_id=c.id and c.job_no_mst=d.job_no and a.id=b.prod_id and a.item_category_id=4 and b.entry_form in(24,25,78,73,49,112) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and t.status_active=1 and t.is_deleted=0 and t.store_id=$cbo_store_name $sql_cond 
					group by a.item_group_id, d.job_no_prefix_num, d.job_no, d.company_name, d.buyer_name, d.style_ref_no, c.id, c.po_number, c.pub_shipment_date, c.grouping, c.file_no, c.po_quantity, t.store_id
					order by c.id, a.item_group_id";
				}
				else
				{
					if($db_type==0)
					{
						$sql_trim = "SELECT a.item_group_id, d.job_no_prefix_num, d.job_no, d.company_name, d.buyer_name, d.style_ref_no, c.id as po_id, c.po_number, c.pub_shipment_date, c.grouping, c.file_no, c.po_quantity,
						sum(case when b.entry_form in(24) and b.trans_type in(1)  then b.quantity else 0 end) as rcv_qty,
						sum(case when b.entry_form in(25) and b.trans_type in(2)  then b.quantity else 0 end) as issue_qty,
						sum(case when b.entry_form in(73) and b.trans_type in(4)  then b.quantity else 0 end) as issue_return_qty,
						sum(case when b.entry_form in(49)  and b.trans_type in(3) then b.quantity else 0 end) as recv_return_qty,
						sum(case when b.entry_form in(78,112) and b.trans_type in(6)  then b.quantity else 0 end) as item_transfer_issue,
						sum(case when b.entry_form in(78,112)  and b.trans_type in(5) then b.quantity else 0 end) as item_transfer_receive,
						sum(case when b.entry_form in(24) and b.trans_type in(1)  then b.order_amount else 0 end) as rcv_amt,
						sum(case when b.entry_form in(24) and b.trans_type in(1) and t.ORDER_AMOUNT>0 then b.order_amount*(t.CONS_AMOUNT/t.ORDER_AMOUNT) else 0 end) as RCV_AMT_BDT,
						group_concat(t.store_id) as store_id
						from product_details_master a, inv_transaction t, order_wise_pro_details b, wo_po_break_down c, wo_po_details_master d
						where a.id=b.prod_id and a.id=t.prod_id and t.id=b.trans_id and b.po_breakdown_id=c.id and c.job_no_mst=d.job_no and a.id=b.prod_id and a.item_category_id=4 and b.entry_form in(24,25,78,73,49,112) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and t.status_active=1 and t.is_deleted=0 $sql_cond 
						group by a.item_group_id, d.job_no_prefix_num, d.job_no, d.company_name, d.buyer_name, d.style_ref_no, c.id, c.po_number, c.pub_shipment_date, c.grouping, c.file_no, c.po_quantity
						order by c.id, a.item_group_id";
					}
					else
					{
						$sql_trim = "SELECT a.item_group_id, d.job_no_prefix_num, d.job_no, d.company_name, d.buyer_name, d.style_ref_no, c.id as po_id, c.po_number, c.pub_shipment_date, c.grouping, c.file_no, c.po_quantity,
						sum(case when b.entry_form in(24) and b.trans_type in(1)  then b.quantity else 0 end) as rcv_qty,
						sum(case when b.entry_form in(25) and b.trans_type in(2)  then b.quantity else 0 end) as issue_qty,
						sum(case when b.entry_form in(73) and b.trans_type in(4)  then b.quantity else 0 end) as issue_return_qty,
						sum(case when b.entry_form in(49)  and b.trans_type in(3) then b.quantity else 0 end) as recv_return_qty,
						sum(case when b.entry_form in(78,112) and b.trans_type in(6)  then b.quantity else 0 end) as item_transfer_issue,
						sum(case when b.entry_form in(78,112)  and b.trans_type in(5) then b.quantity else 0 end) as item_transfer_receive,
						sum(case when b.entry_form in(24) and b.trans_type in(1)  then b.order_amount else 0 end) as rcv_amt,
						sum(case when b.entry_form in(24) and b.trans_type in(1) and t.ORDER_AMOUNT>0 then b.order_amount*(t.CONS_AMOUNT/t.ORDER_AMOUNT) else 0 end) as RCV_AMT_BDT,
						LISTAGG(CAST(t.store_id AS VARCHAR2(4000)),',') WITHIN GROUP (ORDER BY t.store_id) as store_id						
						from product_details_master a, inv_transaction t, order_wise_pro_details b, wo_po_break_down c, wo_po_details_master d
						where a.id=b.prod_id and a.id=t.prod_id and t.id=b.trans_id and b.po_breakdown_id=c.id and c.job_no_mst=d.job_no and a.id=b.prod_id and a.item_category_id=4 and b.entry_form in(24,25,78,73,49,112) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and t.status_active=1 and t.is_deleted=0 $sql_cond 
						group by a.item_group_id, d.job_no_prefix_num, d.job_no, d.company_name, d.buyer_name, d.style_ref_no, c.id, c.po_number, c.pub_shipment_date, c.grouping, c.file_no, c.po_quantity
						order by c.id, a.item_group_id";
					}
					
				}
				$sql_get_ship_status = return_library_array("select a.shiping_status, b.id as po_id from pro_ex_factory_mst a, wo_po_break_down b, pro_ex_factory_delivery_mst c where a.po_break_down_id = b.id and a.delivery_mst_id = c.id and c.company_id = $cbo_company and a.shiping_status = 3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.shiping_status, b.id", "po_id", "shiping_status");
				//echo $sql_trim;
				$data_array=sql_select($sql_trim);
				$span_arr=array();
				$po_id_arr=array();
				foreach ($data_array as $row)
				{
                    $rcv_qnty=$row[csf('rcv_qty')]+$row[csf('issue_return_qty')]+$row[csf('item_transfer_receive')];
                    $issue_qnty=$row[csf('issue_qty')]+$row[csf('recv_return_qty')]+$row[csf('item_transfer_issue')];
                    if($cbo_value_with == 1) {
                        if (($rcv_qnty - $issue_qnty) > 0) {
                            $span_arr[$row[csf('po_id')]][$row[csf('buyer_name')]][$row[csf('style_ref_no')]]++;
                        }
                    }else{
                        $span_arr[$row[csf('po_id')]][$row[csf('buyer_name')]][$row[csf('style_ref_no')]]++;
                    }
					$item_group_id .= $row[csf('item_group_id')].",";
					//$po_id .= $row[csf('po_id')].",";
					$po_id_arr[$row[csf('po_id')]] = $row[csf('po_id')];
				}
				$all_item_group_id = ltrim(implode(",", array_unique(explode(",", chop($item_group_id, ",")))), ',');
				//$all_po_id = ltrim(implode(",", array_unique(explode(",", chop($po_id, ",")))), ',');


				if(!empty($po_id_arr))
				{
					fnc_tempengine("gbl_temp_engine", $user_id, 122, 2, $po_id_arr, $empty_arr);
					//fnc_tempengine("gbl_temp_engine", $user_id, 122, 2, $item_id_arr, $empty_arr);
		
					$rcv_mst_sql="SELECT A.ID as MST_ID, b.ITEM_GROUP_ID, A.RECV_NUMBER, C.PO_BREAKDOWN_ID, SUM(E.CONS_RATE) AS RATE ,E.ID,A.CURRENCY_ID
					FROM INV_RECEIVE_MASTER A, INV_TRIMS_ENTRY_DTLS B, ORDER_WISE_PRO_DETAILS C ,INV_TRANSACTION E,GBL_TEMP_ENGINE G
					WHERE A.ID=B.MST_ID  AND A.ENTRY_FORM=24 AND C.ENTRY_FORM=24  AND B.ID=C.DTLS_ID AND E.ID=C.TRANS_ID AND  C.TRANS_TYPE=1  AND  C.PO_BREAKDOWN_ID =G.REF_VAL  and a.company_id='$cbo_company' and b.item_group_id in ($all_item_group_id) and a.IS_DELETED=0 AND A.STATUS_ACTIVE=1 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND C.STATUS_ACTIVE=1 AND C.IS_DELETED=0 AND E.STATUS_ACTIVE=1 AND E.IS_DELETED=0 AND G.USER_ID= $user_id AND G.ENTRY_FORM=122 AND G.REF_FROM=2 
					GROUP BY C.PO_BREAKDOWN_ID,B.ITEM_GROUP_ID,A.RECV_NUMBER,A.ID,A.RECEIVE_DATE ,e.id,A.currency_id";
					//echo $rcv_mst_sql;
					$data_rcv_mst_sql=sql_select($rcv_mst_sql);
					$bdt_arr=array();
					foreach ($data_rcv_mst_sql as $row)
					{
						$bdt_arr[$row['ITEM_GROUP_ID']][$row['PO_BREAKDOWN_ID']]['bdt_rate']= $row['RATE'];
						$bdt_arr[$row['ITEM_GROUP_ID']][$row['PO_BREAKDOWN_ID']]['currency']= $row['CURRENCY_ID'];
					}


					// $wo_qty_sql = " SELECT a.job_no, c.trim_group, d.id as po_id ,sum(f.WO_QNTY) as WO_QNTY,f.uom
					// from GBL_TEMP_ENGINE G ,wo_po_details_master a
					// join wo_pre_cost_trim_cost_dtls c on a.id=c.JOB_ID 
					// join wo_po_break_down d on a.id=d.JOB_ID 
					// join wo_pre_cost_trim_co_cons_dtls e on a.id=e.JOB_ID and c.id=e.wo_pre_cost_trim_cost_dtls_id and d.id=e.po_break_down_id 
					// join wo_booking_dtls f on a.job_no=f.job_no and e.wo_pre_cost_trim_cost_dtls_id= f.pre_cost_fabric_cost_dtls_id 
					// and e.po_break_down_id=f.po_break_down_id 
					// where f.booking_type=2 and d.id = g.ref_val and a.company_name=1 and d.is_deleted=0 
					// and d.status_active=1 and f.status_active=1 and f.is_deleted=0 AND G.USER_ID= $user_id AND G.ENTRY_FORM=122 AND G.REF_FROM=2 
					// group by a.job_no, c.trim_group, d.id ,f.uom
					// order by d.id";

					$wo_qty_sql = "SELECT A.JOB_NO, B.ID AS PO_ID ,SUM(C.WO_QNTY) AS WO_QNTY,C.UOM,C.TRIM_GROUP
					from wo_po_details_master a,wo_po_break_down b,wo_booking_dtls c,GBL_TEMP_ENGINE G
					where a.id=b.JOB_ID and a.job_no=c.job_no and  c.booking_type=2 and b.id = g.REF_VAL and a.company_name=$cbo_company and b.is_deleted=0 
					and b.status_active=1 and c.status_active=1 and c.is_deleted=0 and G.USER_ID= $user_id AND G.ENTRY_FORM=122 AND G.REF_FROM=2 
					group by a.job_no, b.id ,c.uom,c.TRIM_GROUP";

					//echo $wo_qty_sql;
					$wo_qty_sql_result=sql_select($wo_qty_sql);
					$wo_qty_arr = array();
					foreach($wo_qty_sql_result as $row)
					{
						$wo_qty_arr[$row['JOB_NO']][$row['TRIM_GROUP']][$row['UOM']]['qo_qty'] = $row['WO_QNTY'];
					}
				}

				//print_r($span_arr);die;
                $stockValueTotal = 0; $totStock=0;$tot_left_over=0;
				foreach ($data_array as $selectResult)
				{
					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
					$rcv_qnty=$selectResult[csf('rcv_qty')]+$selectResult[csf('issue_return_qty')]+$selectResult[csf('item_transfer_receive')];
					$issue_qnty=$selectResult[csf('issue_qty')]+$selectResult[csf('recv_return_qty')]+$selectResult[csf('item_transfer_issue')];
					if($selectResult[csf('rcv_qty')]>0 && $selectResult[csf('rcv_amt')] >0)
					{
						$rate=$selectResult[csf('rcv_amt')]/$selectResult[csf('rcv_qty')];
					}
					$rcv_value=$rcv_qnty*$rate;
					$issue_value=$issue_qnty*$rate;
					$req_qnty="";
					$req_qnty=$trims_costing_arr[$selectResult[csf('po_id')]][$selectResult[csf('item_group_id')]];
					$yet_recv=$trims_costing_arr[$selectResult[csf('po_id')]][$selectResult[csf('item_group_id')]]-$rcv_qnty;
                    $left_over = 0;
                    $stock = 0;
                    if(isset($sql_get_ship_status[$selectResult[csf('po_id')]]) && $sql_get_ship_status[$selectResult[csf('po_id')]]==3)
					    $left_over=$rcv_qnty-$issue_qnty;
                    else
                        $stock = $rcv_qnty-$issue_qnty;
					$span = $span_arr[$selectResult[csf('po_id')]][$selectResult[csf('buyer_name')]][$selectResult[csf('style_ref_no')]];

					//print_r($bdt_arr[$selectResult[csf('item_group_id')]][$selectResult[csf('po_id')]]);
					if($bdt_arr[$selectResult[csf('item_group_id')]][$selectResult[csf('po_id')]]['currency']==1){
						$bdt_rate= $bdt_arr[$selectResult[csf('item_group_id')]][$selectResult[csf('po_id')]]['bdt_rate'];
						$total_bdt_rate = $bdt_rate;
					}
					else{
						$bdt_rate = ($selectResult['RCV_AMT_BDT']/$rcv_value);
						$total_bdt_rate = $bdt_rate*$rate;
					}

					$wo_qty = $wo_qty_arr[$selectResult[csf('JOB_NO')]][$selectResult[csf('item_group_id')]][$item_group_arr[$selectResult[csf('item_group_id')]]["order_uom"]]['qo_qty'];


					if($cbo_value_with == 1)
					{
                        if( $stock > 0)
						{
                         ?>
                            <tr bgcolor="<? echo $bgcolor; ?>"  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                <?
                                if ($order_id_array[$selectResult[csf('po_id')]]=="")
                                {
                                    $k++;
                                    ?>
                                    <td width="30" align="center" rowspan="<? echo $span;?>"  > <? echo $k;?> </td>
                                    <td width="100" rowspan="<? echo $span;?>"><p> <? echo $selectResult[csf('po_number')];?></p></td>
                                    <td width="110" rowspan="<? echo $span;?>"><p><? echo $buyer_arr[$selectResult[csf('buyer_name')]];?></p></td>
                                    <td width="80" rowspan="<? echo $span;?>"><p><? echo $selectResult[csf('style_ref_no')];?></p></td>
                                    <td width="50" rowspan="<? echo $span;?>" align="right"> <? echo number_format($selectResult[csf('po_quantity')]);?> </td>
                                    <td width="65" rowspan="<? echo $span;?>" align="right"><? echo number_format($selectResult[csf('po_quantity')]/12,0);?> </td>
                                    <?
                                    $order_id_array[$selectResult[csf('po_id')]]=$selectResult[csf('po_id')];
                                }
                                ?>
                                <td width="130" align="left"><p><? echo $item_group_arr[$selectResult[csf('item_group_id')]]["item_name"]; ?></p></td>
                                <td width="40" align="center"><p><? echo  $unit_of_measurement[$item_group_arr[$selectResult[csf('item_group_id')]]["order_uom"]]; ?></p></td>
                                <td width="70" title="Req. Qty" align="right"><? echo number_format($req_qnty,2); ?> </td>

                                <td width="70" title="Wo Qty" align="right"><? echo number_format($wo_qty,2); ?> </td>
                                <td width="70" title="Req. Qty - Wo Qty" align="right"><? echo number_format($req_qnty-$wo_qty,2); ?> </td>


                                <td width="70" align="right" title="Received Qty"><a href='#report_details' onClick="openmypage('<? echo $selectResult[csf('po_id')]; ?>','<? echo $selectResult[csf('item_group_id')]; ?>','receive_popup');"><? echo number_format($rcv_qnty,2); 
								?> </a>  </td>
                                <td width="70" align="right"> <p><? echo number_format($rcv_value,2); ?></p>  </td>

								<td width="70" align="right"> <p><? echo number_format($selectResult['RCV_AMT_BDT'],2);?></p></td>

                                <td width="70" title="Wo qty -Recv Qty" align="right"> <? 
								
								//echo number_format($yet_recv,2); 
								echo number_format($wo_qty-$rcv_qnty,2); 
								
								?></td>
                                <td width="70" align="right"><a href='#report_details' onClick="openmypage('<? echo $selectResult[csf('po_id')]; ?>','<? echo $selectResult[csf('item_group_id')]; ?>','issue_popup');"><? echo number_format($issue_qnty,2); ?></a> </td>
                                <td width="70" title="Stock" align="right"><p> <? echo number_format($stock,2); $totStock=$totStock+$stock;?> </td></p>
								<td width="50" title="Rate" align="right" > <p><? echo  number_format($rate,4); ?></p></td>

								<td width="60" title="Rate BDT" align="right" > <p><? echo  number_format($total_bdt_rate,4); ?></p></td>

								<td width="70" title="Stock Value" align="right"><p><? echo number_format($stock*$rate,2); ?></p> </td>
                                <td width="70" title="Left Over" align="right"><p><? echo number_format($left_over,2);$tot_left_over=$tot_left_over+$left_over; ?></p> </td>

                                <td width="70" title="Left Over Value" align="right"><p><? $total_left=$left_over*$rate; echo number_format($total_left,2); $left_val+=$total_left; ?></p></td>

								<td width="70" title="Left Over Value BDT" align="right"><p><? $total_left_bdt=$left_over*$total_bdt_rate; 
								echo number_format($total_left_bdt,2); 
								$left_val_bdt+=$total_left_bdt; ?></td>

                                <td width="60" title="Internal Ref/Grouping"><? echo $selectResult[csf('grouping')];?></td>
                                <td width="60" title="File No"><? echo $selectResult[csf('file_no')];?></td>
                                <td>
                                <?
                                $store_id_arr=array_unique(explode(",",$selectResult[csf('store_id')]));
                                $all_store_name="";
                                foreach($store_id_arr as $store_id)
                                {
                                    $all_store_name.=$store_arr[$store_id].",";
                                }
                                $all_store_name=chop($all_store_name,",");
                                echo $all_store_name;
                                ?>
                                </td>
                        	</tr>
                    		<?
                             $stockValueTotal  += $stock*$rate;
                        }
                    }
					else
					{
                         ?>
                             <tr bgcolor="<? echo $bgcolor; ?>"  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <?
                                if ($order_id_array[$selectResult[csf('po_id')]]=="")
                                {
                                    $k++;
                                    ?>
                                    <td width="30" align="center" rowspan="<? echo $span;?>"  > <? echo $k;?> </td>
                                    <td width="100" rowspan="<? echo $span;?>"><p> <? echo $selectResult[csf('po_number')];?></p></td>
                                    <td width="110" rowspan="<? echo $span;?>"><p><? echo $buyer_arr[$selectResult[csf('buyer_name')]];?></p></td>
                                    <td width="80" rowspan="<? echo $span;?>"><p><? echo $selectResult[csf('style_ref_no')];?></p></td>
                                    <td width="50" rowspan="<? echo $span;?>" align="right"> <? echo number_format($selectResult[csf('po_quantity')]);?> </td>
                                    <td width="65" rowspan="<? echo $span;?>" align="right"><? echo number_format($selectResult[csf('po_quantity')]/12,0);?> </td>
                                    <?
                                    $order_id_array[$selectResult[csf('po_id')]]=$selectResult[csf('po_id')];
                                }
                                ?>
									<td width="130" align="left"><p><? echo $item_group_arr[$selectResult[csf('item_group_id')]]["item_name"]; ?></p></td>
									<td width="40" align="center"><p><? echo  $unit_of_measurement[$item_group_arr[$selectResult[csf('item_group_id')]]["order_uom"]]; ?></p></td>
									<td width="70" title="Req. Qty" align="right"><p><? echo number_format($req_qnty,2); ?></p> </td>

									<td width="70" title="Wo Qty" align="right"><? echo number_format($wo_qty,2); ?> </td>
									<td width="70" title="Req. Qty - Wo Qty" align="right"><? echo number_format($req_qnty-$wo_qty,2); ?> </td>

									<td width="70" align="right" title="Received Qty"><a href='#report_details' onClick="openmypage('<? echo $selectResult[csf('po_id')]; ?>','<? echo $selectResult[csf('item_group_id')]; ?>','receive_popup');"><? echo number_format($rcv_qnty,2); ?> </a>  </td>
									<td width="70" align="right"> <p><? echo number_format($rcv_value,2); ?></p>  </td>
									<td width="70" align="right"> <p><? echo number_format($selectResult['RCV_AMT_BDT'],2); ?></p>  </td>
									<td width="70" title="yet" align="right"><p> <? 
									
									//echo number_format($yet_recv,2); 
									echo number_format($wo_qty-$rcv_qnty,2); 
									?></p></td>
									<td width="70" align="right"><a href='#report_details' onClick="openmypage('<? echo $selectResult[csf('po_id')]; ?>','<? echo $selectResult[csf('item_group_id')]; ?>','issue_popup');"><? echo number_format($issue_qnty,2); ?></a> </td>
									<td width="70" title="Stock" align="right"><p><? echo number_format($stock,2);$totStock=$totStock+$stock; ?> </p></td>
									<td width="50" title="Rate" align="right" > <p><? echo  number_format($rate,4); ?></p></td>
									<td width="60" title="Rate BDT" align="right" > <p><? echo  number_format($total_bdt_rate,4); ?></p></td>

									<td width="70" title="Stock Value" align="right"><p><? echo number_format($stock*$rate,2); ?></p> </td>
									<td width="70" title="Left Over" align="right"><p><? echo number_format($left_over,2);$tot_left_over=$tot_left_over+$left_over; ?></p> </td>
									<td width="70" title="Left Over Value" align="right"><? $total_left=$left_over*$rate; echo number_format($total_left,2); $left_val+=$total_left; ?></td>
									
									<td width="70" title="Left Over Value BDT" align="right"><p><? $total_left_bdt=$left_over*$total_bdt_rate; 
									echo number_format($total_left_bdt,2); 
									$left_val_bdt+=$total_left_bdt; ?></p></td>

									<td width="60" title="Internal Ref/Grouping"><p><? echo $selectResult[csf('grouping')];?></p></td>
									<td width="60" title="File No"><? echo $selectResult[csf('file_no')];?></td>
									<td>
										<?
										$store_id_arr=array_unique(explode(",",$selectResult[csf('store_id')]));
										$all_store_name="";
										foreach($store_id_arr as $store_id)
										{
											$all_store_name.=$store_arr[$store_id].",";
										}
										$all_store_name=chop($all_store_name,",");
										echo $all_store_name;
										?>
                                     </td>
                             </tr>
                        <?
                         $stockValueTotal  += $stock*$rate;
                     }

                    $i++;
                    
                    $total_rec_value+=$rcv_value;
					$total_rec_value_bdt+=$selectResult['RCV_AMT_BDT'];
				}

				?> 
        </tbody>
        <tfoot>
            <tr>
                <th colspan="12" align="right"><b>Total:</b></th>
                <th align="right"><?  echo number_format($total_rec_value,2); ?></th>
                <th align="right"><?  echo number_format($total_rec_value_bdt,2); ?></th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th><? echo number_format($totStock,2);?></th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th align="right"><?  echo number_format($stockValueTotal,2); ?></th>
                <th><? echo number_format($tot_left_over,2); ?></th>
                <th align="right"><? echo number_format($left_val,2); ?></th>
                <th align="right"><? echo number_format($left_val_bdt,2); ?></th> 
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
            </tr> 
        </tfoot>
    </table>
   </div>
  </div>
	<?
	$rID2=execute_query("delete from gbl_temp_engine where user_id=$user_id and entry_form in (122)");
	if ($rID2) oci_commit($con);
	disconnect($con);
	exit();
}
if ($action=="report_generate_color_size")//Item Color & Size Wise Search.
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	$cbo_company=str_replace("'","",$cbo_company_id);
	$cbo_buyer=str_replace("'","",$cbo_buyer_id);
	$txt_style_id=str_replace("'","",$txt_style_id);
	$txt_style=str_replace("'","",$txt_style);
	$txt_order_no=str_replace("'","",$txt_order_no);
	$txt_order_id=str_replace("'","",$txt_order_no_id);
	$txt_int_ref_no=str_replace("'","",$txt_int_ref_no);
	$txt_file_no=str_replace("'","",$txt_file_no);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	$cbo_store_name=str_replace("'","",$cbo_store_name);
	
	
	
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$buyer_arr = return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$color_library=return_library_array("select id,color_name from lib_color", "id", "color_name");
	$sql_item_group=sql_select("select id, item_name, order_uom from lib_item_group ");
	$store_arr = return_library_array( "select id, store_name from lib_store_location",'id','store_name');
	foreach($sql_item_group as $row)
	{
		$item_group_arr[$row[csf("id")]]["item_name"]=$row[csf("item_name")];
		$item_group_arr[$row[csf("id")]]["order_uom"]=$row[csf("order_uom")];
	}
	
	$condition= new condition();
	$condition->company_name("=$cbo_company_id");
	if(str_replace("'","",$cbo_buyer_id)>0)
	{
		$condition->buyer_name("=$cbo_buyer_id");
	}
	if(str_replace("'","",$txt_style) !='')
	{
		$condition->jobid_in("$txt_style");
	}
	if(str_replace("'","",$txt_order_no_id)!='')
	{
		$condition->po_id("in(".str_replace("'","",$txt_order_no_id).")");
	}
	if(str_replace("'","",$txt_file_no) !='')
	{
		$condition->file_no("=$txt_file_no");
	}
	if(str_replace("'","",$txt_int_ref_no) !='')
	{
		$condition->grouping("='$txt_int_ref_no'");
	}
	if(str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!='')
	{
		$condition->pub_shipment_date(" between '$date_from' and '$date_to'");
	}
	
	$condition->init();
	$trims= new trims($condition);
	//echo "test3";die;
	//echo $trims->getQuery();die;
	$trims_costing_arr=$trims->getQtyArray_by_orderAndItemid();
	//echo "<pre>";print_r($trims_costing_arr);die;
	
	$con = connect();
	$rID=execute_query("delete from gbl_temp_engine where user_id=$user_id and entry_form in (122)");
	if ($rID) oci_commit($con);

	ob_start();	
	?>
    <div style="width:1910px; margin-left:5px;">
    
        <table width="1910" cellspacing="0" cellpadding="0" border="0" rules="all"  >
            <tr class="form_caption">
                <td colspan="21" align="center" style="border:none;font-size:16px; font-weight:bold"> <? echo $report_title; ?></td>
            </tr>
            <tr class="form_caption">
                <td colspan="21" align="center"><? echo $company_library[$cbo_company]; ?></td>
            </tr>
        </table>
         <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1890" class="rpt_table" >
            <thead>
                <th width="30">SL</th>
                <th width="100">Order No</th>
                <th width="110">Buyer Name</th>
                <th width="80">Style</th>
                <th width="50">RMG Qty.</th>
                <th width="65">RMG Qty(Dzn)</th>
				<th width="60">Prod. Id</th>
                <th width="130">Item Group</th>
				<th width="80">Item Color</th>
                <th width="70">Item Size</th>
                <th width="40">UOM</th>
                <th width="70">Req. Qty</th>
                <th width="70">Recv. Qty</th>
                <th width="70">Recv. Value</th>
				<th width="70">Recv. Value[BDT]</th>
                <th width="70">Yet to Rev.</th>
                <th width="70">Issue Qty.</th>
                <th width="70">Left Over</th>
                <th width="50">Rate</th>
				<th width="60">Rate[BDT]</th>
                <th width="70">Left Over Val.</th>
				<th width="70">Left Over Val.[BDT]</th>
             	<th width="60">Int. Ref</th>
                <th width="60">File No</th>
                <th width="60">DOH</th>
                <th>Store Name</th>
            </thead>
        </table>
 		<div style="width:1910px; overflow-y:scroll; max-height:350px;font-size:12px; overflow-x:hidden;" id="scroll_body">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1890" class="rpt_table"  id="tbl_issue_status" >
           <tbody>
		   <?
				$sql_cond="";
				if($cbo_company>0) $sql_cond .=" and d.company_name=$cbo_company";
				if($cbo_buyer>0) $sql_cond .=" and d.buyer_name=$cbo_buyer";
				if($txt_style!="") $sql_cond .=" and d.id in($txt_style)";
				if($txt_order_id!="") $sql_cond .=" and c.id in($txt_order_id)";
				if($txt_file_no!="") $sql_cond .=" and c.file_no ='$txt_file_no' ";
				if($txt_int_ref_no!="") $sql_cond .=" and c.grouping ='$txt_int_ref_no' ";
				if($date_from!="" && $date_to!="")  $sql_cond .=" and c.pub_shipment_date  between '$date_from' and '$date_to' ";
				
				if($cbo_store_name>0)
				{
					$sql_trim = "SELECT a.id as prod_id, a.item_group_id, a.product_name_details, a.item_description, a.item_color, a.item_size, a.color, a.gmts_size, d.job_no_prefix_num, d.job_no, d.company_name, d.buyer_name, d.style_ref_no, c.id as po_id, c.po_number, c.pub_shipment_date, c.grouping, c.file_no, c.po_quantity,
					sum(case when b.entry_form in(24) and b.trans_type in(1)  then b.quantity else 0 end) as rcv_qty,
					sum(case when b.entry_form in(25) and b.trans_type in(2)  then b.quantity else 0 end) as issue_qty,
					sum(case when b.entry_form in(73) and b.trans_type in(4)  then b.quantity else 0 end) as issue_return_qty,
					sum(case when b.entry_form in(49)  and b.trans_type in(3) then b.quantity else 0 end) as recv_return_qty,
					sum(case when b.entry_form in(78,112) and b.trans_type in(6)  then b.quantity else 0 end) as item_transfer_issue,
					sum(case when b.entry_form in(78,112)  and b.trans_type in(5) then b.quantity else 0 end) as item_transfer_receive,
					sum(case when b.entry_form in(24) and b.trans_type in(1)  then b.order_amount else 0 end) as rcv_amt,
					sum(case when b.entry_form in(24) and b.trans_type in(1) and t.ORDER_AMOUNT>0 then b.order_amount*(t.CONS_AMOUNT/t.ORDER_AMOUNT) else 0 end) as RCV_AMT_BDT,
					t.store_id
					from product_details_master a, inv_transaction t, order_wise_pro_details b, wo_po_break_down c, wo_po_details_master d
					where a.id=b.prod_id and b.po_breakdown_id=c.id and c.job_no_mst=d.job_no and a.id=b.prod_id and a.item_category_id=4 and b.entry_form in(24,25,78,73,49,112) and a.status_active=1 and a.is_deleted=0 and a.id=t.prod_id and t.id=b.trans_id and b.status_active=1 and b.is_deleted=0 and t.store_id=$cbo_store_name $sql_cond 
					group by a.id, a.item_group_id, a.product_name_details, a.item_description, a.item_color, a.item_size, a.color, a.gmts_size, d.job_no_prefix_num, d.job_no, d.company_name, d.buyer_name, d.style_ref_no, c.id, c.po_number, c.pub_shipment_date, c.grouping, c.file_no, c.po_quantity, t.store_id
					order by c.id, a.item_group_id, a.item_color";
				}
				else
				{
					if($db_type==0)
					{
						$sql_trim = "SELECT a.id as prod_id, a.item_group_id, a.product_name_details, a.item_description, a.item_color, a.item_size, a.color, a.gmts_size, d.job_no_prefix_num, d.job_no, d.company_name, d.buyer_name, d.style_ref_no, c.id as po_id, c.po_number, c.pub_shipment_date, c.grouping, c.file_no, c.po_quantity,
						sum(case when b.entry_form in(24) and b.trans_type in(1)  then b.quantity else 0 end) as rcv_qty,
						sum(case when b.entry_form in(25) and b.trans_type in(2)  then b.quantity else 0 end) as issue_qty,
						sum(case when b.entry_form in(73) and b.trans_type in(4)  then b.quantity else 0 end) as issue_return_qty,
						sum(case when b.entry_form in(49)  and b.trans_type in(3) then b.quantity else 0 end) as recv_return_qty,
						sum(case when b.entry_form in(78,112) and b.trans_type in(6)  then b.quantity else 0 end) as item_transfer_issue,
						sum(case when b.entry_form in(78,112)  and b.trans_type in(5) then b.quantity else 0 end) as item_transfer_receive,
						sum(case when b.entry_form in(24) and b.trans_type in(1)  then b.order_amount else 0 end) as rcv_amt,
						sum(case when b.entry_form in(24) and b.trans_type in(1) and t.ORDER_AMOUNT>0 then b.order_amount*(t.CONS_AMOUNT/t.ORDER_AMOUNT) else 0 end) as RCV_AMT_BDT,
						group_concat(t.store_id) as store_id
						from product_details_master a, inv_transaction t, order_wise_pro_details b, wo_po_break_down c, wo_po_details_master d
						where a.id=b.prod_id and b.po_breakdown_id=c.id and c.job_no_mst=d.job_no and a.id=b.prod_id and a.item_category_id=4 and b.entry_form in(24,25,78,73,49,112) and a.status_active=1 and a.is_deleted=0 and a.id=t.prod_id and t.id=b.trans_id and b.status_active=1 and b.is_deleted=0 $sql_cond 
						group by a.id, a.item_group_id, a.product_name_details, a.item_description, a.item_color, a.item_size, a.color, a.gmts_size, d.job_no_prefix_num, d.job_no, d.company_name, d.buyer_name, d.style_ref_no, c.id, c.po_number, c.pub_shipment_date, c.grouping, c.file_no, c.po_quantity
						order by c.id, a.item_group_id, a.item_color";
					}
					else
					{
						$sql_trim = "SELECT a.id as prod_id, a.item_group_id, a.product_name_details, a.item_description, a.item_color, a.item_size, a.color, a.gmts_size, d.job_no_prefix_num, d.job_no, d.company_name, d.buyer_name, d.style_ref_no, c.id as po_id, c.po_number, c.pub_shipment_date, c.grouping, c.file_no, c.po_quantity,
						sum(case when b.entry_form in(24) and b.trans_type in(1)  then b.quantity else 0 end) as rcv_qty,
						sum(case when b.entry_form in(25) and b.trans_type in(2)  then b.quantity else 0 end) as issue_qty,
						sum(case when b.entry_form in(73) and b.trans_type in(4)  then b.quantity else 0 end) as issue_return_qty,
						sum(case when b.entry_form in(49)  and b.trans_type in(3) then b.quantity else 0 end) as recv_return_qty,
						sum(case when b.entry_form in(78,112) and b.trans_type in(6)  then b.quantity else 0 end) as item_transfer_issue,
						sum(case when b.entry_form in(78,112)  and b.trans_type in(5) then b.quantity else 0 end) as item_transfer_receive,
						sum(case when b.entry_form in(24) and b.trans_type in(1)  then b.order_amount else 0 end) as rcv_amt,
						sum(case when b.entry_form in(24) and b.trans_type in(1) and t.ORDER_AMOUNT>0 then b.order_amount*(t.CONS_AMOUNT/t.ORDER_AMOUNT) else 0 end) as RCV_AMT_BDT,
						LISTAGG(CAST(t.store_id AS VARCHAR2(4000)),',') WITHIN GROUP (ORDER BY t.store_id) as store_id
						from product_details_master a, inv_transaction t, order_wise_pro_details b, wo_po_break_down c, wo_po_details_master d
						where a.id=b.prod_id and b.po_breakdown_id=c.id and c.job_no_mst=d.job_no and a.id=b.prod_id and a.item_category_id=4 and b.entry_form in(24,25,78,73,49,112) and a.status_active=1 and a.is_deleted=0 and a.id=t.prod_id and t.id=b.trans_id and b.status_active=1 and b.is_deleted=0 $sql_cond 
						group by a.id, a.item_group_id, a.product_name_details, a.item_description, a.item_color, a.item_size, a.color, a.gmts_size, d.job_no_prefix_num, d.job_no, d.company_name, d.buyer_name, d.style_ref_no, c.id, c.po_number, c.pub_shipment_date, c.grouping, c.file_no, c.po_quantity
						order by c.id, a.item_group_id, a.item_color";
					}
				}

				
				//echo $sql_trim;die;	
				$data_array=sql_select($sql_trim);
				$prod_id_arr = array();
				foreach($data_array as $row)
				{
					$prod_id_arr[$row[csf('prod_id')]] = $row[csf('prod_id')];
					$item_group_id .= $row[csf('item_group_id')].",";
					//$po_id .= $row[csf('po_id')].",";
					$po_id_arr[$row[csf('po_id')]] = $row[csf('po_id')];
				}

				$all_item_group_id = ltrim(implode(",", array_unique(explode(",", chop($item_group_id, ",")))), ',');
				//$all_po_id = ltrim(implode(",", array_unique(explode(",", chop($po_id, ",")))), ',');

				
				if(!empty($po_id_arr))
				{
					fnc_tempengine("gbl_temp_engine", $user_id, 122, 3, $po_id_arr, $empty_arr);
					//fnc_tempengine("gbl_temp_engine", $user_id, 122, 2, $item_id_arr, $empty_arr);
		
					$rcv_mst_sql="SELECT A.ID as MST_ID, b.ITEM_GROUP_ID, A.RECV_NUMBER, C.PO_BREAKDOWN_ID, SUM(E.CONS_RATE) AS RATE ,E.ID,A.CURRENCY_ID
					FROM INV_RECEIVE_MASTER A, INV_TRIMS_ENTRY_DTLS B, ORDER_WISE_PRO_DETAILS C ,INV_TRANSACTION E,GBL_TEMP_ENGINE G
					WHERE A.ID=B.MST_ID  AND A.ENTRY_FORM=24 AND C.ENTRY_FORM=24  AND B.ID=C.DTLS_ID AND E.ID=C.TRANS_ID AND  C.TRANS_TYPE=1  AND  C.PO_BREAKDOWN_ID =G.REF_VAL  and a.company_id='$cbo_company' and b.item_group_id in ($all_item_group_id) and a.IS_DELETED=0 AND A.STATUS_ACTIVE=1 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND C.STATUS_ACTIVE=1 AND C.IS_DELETED=0 AND E.STATUS_ACTIVE=1 AND E.IS_DELETED=0 AND G.USER_ID= $user_id AND G.ENTRY_FORM=122 AND G.REF_FROM=3
					GROUP BY C.PO_BREAKDOWN_ID,B.ITEM_GROUP_ID,A.RECV_NUMBER,A.ID,A.RECEIVE_DATE ,e.id,A.currency_id";
					//echo $rcv_mst_sql;
					$data_rcv_mst_sql=sql_select($rcv_mst_sql);
					$bdt_arr=array();
					foreach ($data_rcv_mst_sql as $row)
					{
						$bdt_arr[$row['ITEM_GROUP_ID']][$row['PO_BREAKDOWN_ID']]['bdt_rate']= $row['RATE'];
						$bdt_arr[$row['ITEM_GROUP_ID']][$row['PO_BREAKDOWN_ID']]['currency']= $row['CURRENCY_ID'];
					}

				}


               $date_array=array();
               if(count($prod_id_arr)  > 0) {
                   $prod_id_chunk = array_chunk($prod_id_arr,900);
                   $prod_id_cond = "";
                   foreach ($prod_id_chunk as $k => $v){
                       if($k == 0){
                           $prod_id_cond .= " prod_id in (".implode(",", $v).")";
                       }else{
                           $prod_id_cond .= " or prod_id in (".implode(",", $v).")";
                       }
                   }
                   $returnRes_date = "SELECT PROD_ID, MIN(TRANSACTION_DATE) AS MIN_DATE, MAX(TRANSACTION_DATE) AS MAX_DATE from inv_transaction where is_deleted=0 and status_active=1 and item_category=4 and ($prod_id_cond) group by prod_id";
                   //echo $returnRes_date;die;
                   $result_returnRes_date = sql_select($returnRes_date);
                   foreach ($result_returnRes_date as $row) {
                       $date_array[$row["PROD_ID"]]['MIN_DATE'] = $row["MIN_DATE"];
                       $date_array[$row["PROD_ID"]]['MAX_DATE'] = $row["MAX_DATE"];
                   }
               }
				
				foreach ($data_array as $selectResult)
				{
					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
					$rcv_qnty=$selectResult[csf('rcv_qty')]+$selectResult[csf('issue_return_qty')]+$selectResult[csf('item_transfer_receive')];
					$issue_qnty=$selectResult[csf('issue_qty')]+$selectResult[csf('recv_return_qty')]+$selectResult[csf('item_transfer_issue')];
					if($selectResult[csf('rcv_qty')]>0 && $selectResult[csf('rcv_amt')] >0)
					{
						$rate=$selectResult[csf('rcv_amt')]/$selectResult[csf('rcv_qty')];
					}
					$rcv_value=$rcv_qnty*$rate;
					$issue_value=$issue_qnty*$rate;
					$req_qnty="";
					if($order_item_check[$selectResult[csf('po_id')]][$selectResult[csf('item_group_id')]]=="")
					{
						$order_item_check[$selectResult[csf('po_id')]][$selectResult[csf('item_group_id')]]=$selectResult[csf('item_group_id')];
						$req_qnty=$trims_costing_arr[$selectResult[csf('po_id')]][$selectResult[csf('item_group_id')]];
					}
					
					$yet_recv=$trims_costing_arr[$selectResult[csf('po_id')]][$selectResult[csf('item_group_id')]]-$rcv_qnty;
					$left_over=$rcv_qnty-$issue_qnty;
					//$bdt_rate = ($selectResult['RCV_AMT_BDT']/$rcv_value);
					if($bdt_arr[$selectResult[csf('item_group_id')]][$selectResult[csf('po_id')]]['currency']==1){
						$bdt_rate= $bdt_arr[$selectResult[csf('item_group_id')]][$selectResult[csf('po_id')]]['bdt_rate'];
						$total_bdt_rate = $bdt_rate;
					}
					else{
						$bdt_rate = ($selectResult['RCV_AMT_BDT']/$rcv_value);
						$total_bdt_rate = $bdt_rate*$rate;
					}
					?> 
                    <tr bgcolor="<? echo $bgcolor; ?>"  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <?
                    	if ($order_id_array[$selectResult[csf('po_id')]]=="")
                        {
                            $k++;
							?>
							<td width="30" align="center"> <? echo $k;?> </td>
							<td width="100"><p> <? echo $selectResult[csf('po_number')];?></p></td>
							<td width="110"><p><? echo $buyer_arr[$selectResult[csf('buyer_name')]];?></p></td>
							<td width="80"><p><? echo $selectResult[csf('style_ref_no')];?></p></td>
							<td width="50" align="right"> <? echo number_format($selectResult[csf('po_quantity')]);?> </td>
							<td width="65" align="right"><? echo number_format($selectResult[csf('po_quantity')]/12,0);?> </td>
							<? 
							$order_id_array[$selectResult[csf('po_id')]]=$selectResult[csf('po_id')];
                        }
                        else
                        {
							?>
							<td width="30">&nbsp;</td>
							<td width="100"><p>&nbsp;</p></td>
							<td width="110"><p>&nbsp;</p></td>
							<td width="80"><p>&nbsp;</p></td>
							<td width="50" align="right">&nbsp;</td>
							<td width="65" align="right">&nbsp;</td>	
							<?
                        } 
                    	?>
                        <td width="60" title="Prod. Id" align="center"><? echo $selectResult[csf('prod_id')]; ?></td>
                        <td width="130" align="left"><p><? echo $item_group_arr[$selectResult[csf('item_group_id')]]["item_name"]; ?></p></td>
                        <td width="80"><p><? echo $color_library[$selectResult[csf('item_color')]];   ?></p></td>
                        <td width="70"><p><? echo $selectResult[csf('item_size')];   ?></p></td>
                        <td width="40" align="center"><p><? echo  $unit_of_measurement[$item_group_arr[$selectResult[csf('item_group_id')]]["order_uom"]]; ?></p></td>
                        <td width="70" title="Req. Qty" align="right"><? echo number_format($req_qnty,2); ?> </td>
                        <td width="70"  align="right" title="Received Qty"><a href='#report_details' onClick="openmypage_des('<? echo $selectResult[csf('po_id')]; ?>','<? echo $selectResult[csf('item_group_id')]; ?>','<? echo  $selectResult[csf('prod_id')];?>','receive_des_popup');"><? echo number_format($rcv_qnty,2); ?> </a>  </td>
                        <td width="70" align="right"> <? echo number_format($rcv_value,2); ?>  </td>
                        <td width="70" align="right"> <? echo number_format($selectResult['RCV_AMT_BDT'],2); ?>  </td>
                        <td width="70" title="yet" align="right"> <? echo number_format($yet_recv,2); ?></td>
                        <td width="70" align="right"><a href='#report_details' onClick="openmypage_des('<? echo $selectResult[csf('po_id')]; ?>','<? echo $selectResult[csf('item_group_id')]; ?>','<? echo  $selectResult[csf('prod_id')] ;?>','issue_des_popup');"><? echo number_format($issue_qnty,2); ?></a> </td>
                        <td width="70" title="Left Over"><? echo number_format($left_over,2); ?> </td>
                        <td width="50" title="Rate" align="right" > <? echo  number_format($rate,4); ?></td>
                        <td width="60" title="Rate BDT" align="right" > <? echo  number_format($total_bdt_rate,4); ?></td>
                        <td width="70" title="Left Over Value" align="right"><? $total_left=$left_over*$rate; echo number_format($total_left,2); $left_val+=$total_left; ?></td>

						<td width="70" title="Left Over Value BDT" align="right"><? $total_left_bdt=$left_over*$total_bdt_rate; 
						echo number_format($total_left_bdt,2); 
						$left_val_bdt+=$total_left_bdt; ?></td>
                        <td width="60" title="Internal Ref/Grouping" align="left"><? echo $selectResult[csf('grouping')];?></td>
                        <td width="60" title="File No" align="left"><? echo $selectResult[csf('file_no')];?></td>
                        <td width="60" title="DOH" align="center">
                            <?
                            $daysOnHand = datediff("d",$date_array[$selectResult[csf('prod_id')]]['MAX_DATE'],date("Y-m-d"));
                            echo $daysOnHand;
                            ?>
                        </td>
                        <td>
						<?
						$store_id_arr=array_unique(explode(",",$selectResult[csf('store_id')]));
						$all_store_name="";
						foreach($store_id_arr as $store_id)
						{
							$all_store_name.=$store_arr[$store_id].",";
						}
						$all_store_name=chop($all_store_name,",");
						echo $all_store_name;
						?>
                        </td>
                    </tr>
                    <?
                    $i++;
                    $total_rec_value+=$rcv_value;
					$total_rec_value_bdt+=$selectResult['RCV_AMT_BDT'];
				}
				?>     
                 
        </tbody>
        <tfoot>
            <tr>
                <th colspan="13" align="right"><b>Total:</b></th>
                <th align="right"><?  echo number_format($total_rec_value,2); ?></th>
                <th align="right"><?  echo number_format($total_rec_value_bdt,2); ?></th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th align="right"><? echo number_format($left_val,2); ?></th>
                <th align="right"><? echo number_format($left_val_bdt,2); ?></th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
            </tr>
        </tfoot>
    </table>
   </div>
  </div>
	<?
	$rID2=execute_query("delete from gbl_temp_engine where user_id=$user_id and entry_form in (122)");
	if ($rID2) oci_commit($con);
	disconnect($con);
	exit();
}

if($action=="receive_popup")
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	
	?>
<fieldset style="width:470px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="450" cellpadding="0" cellspacing="0" align="center">
				<caption>Recevied Detail</caption>
                <thead>
                    <th width="30">Sl</th>
                    <th width="100">Receive ID</th>
                    <th width="75">Receive Date</th>
                    <th width="80">Recv. Qty</th>
				</thead>
                <tbody>
                <?
				
					if($storeName>0) $store_cond=" and e.store_id=$storeName";
					$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details"  );
					$i=1;
					/*$mrr_sql="select a.id, a.recv_number, a.receive_date, SUM(c.quantity) as quantity
					from inv_receive_master a, inv_trims_entry_dtls b, order_wise_pro_details c 
					where a.id=b.mst_id  and a.entry_form=24 and c.entry_form=24  and b.id=c.dtls_id and c.trans_type=1  and  c.po_breakdown_id in($po_id)  and a.company_id='$companyID' and b.item_group_id='$item_group' and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 group by    c.po_breakdown_id,b.item_group_id,a.recv_number,a.id,a.receive_date";*/
					$mrr_sql="select a.id, a.recv_number, a.receive_date, SUM(c.quantity) as quantity
					from inv_receive_master a, inv_trims_entry_dtls b, order_wise_pro_details c ,inv_transaction e
					where a.id=b.mst_id  and a.entry_form=24 and c.entry_form=24  and b.id=c.dtls_id and e.id=c.trans_id and  c.trans_type=1  and  c.po_breakdown_id in($po_id)  and a.company_id='$companyID' and b.item_group_id='$item_group' $store_cond and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and e.status_active=1 and e.is_deleted=0 group by    c.po_breakdown_id,b.item_group_id,a.recv_number,a.id,a.receive_date";
					
					//echo $mrr_sql;
					$dtlsArray=sql_select($mrr_sql);
					
					foreach($dtlsArray as $row)
					{
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
							if($row[csf('quantity')]>0)
							{	
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="100"><p><? echo $row[csf('recv_number')]; ?></p></td>
                            <td width="75"><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
                           
                            <td width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
                        </tr>
						<?
						$tot_qty+=$row[csf('quantity')];
						$i++;
							}
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="3" align="right">Total</td>
                        <td align="right"><? echo number_format($tot_qty,2); ?>&nbsp;</td>
                    </tr>
                </tfoot>
            </table>
            <br>
            <table border="1" class="rpt_table" rules="all" width="450" cellpadding="0" cellspacing="0" align="center">
				<tr>
                <caption>Issue Return Detail</caption>
                </tr>
                <thead>
                    <th width="30">Sl</th>
                    <th width="100">Issue Return ID</th>
                    <th width="75">Issue Return Date</th>
                    <th width="80">Issue Ret. Qty</th>
				</thead>
                <tbody>
                <?
				
				$issue_return_qty=$trims_qty_array[$selectResult[csf('po_id')]][$selectResult[csf('trim_group')]]['issue_return_qty'];
					$recv_return_qty=$trims_qty_array[$selectResult[csf('po_id')]][$selectResult[csf('trim_group')]]['recv_return_qty'];
					$transfer_out_qty=$trims_qty_array[$selectResult[csf('po_id')]][$selectResult[csf('trim_group')]]['item_transfer_issue'];
					
					$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details"  );
					$j=1;
					  $mrr_sql2="select a.id, a.recv_number, a.receive_date, SUM(c.quantity) as quantity
					from inv_receive_master a, inv_transaction b, order_wise_pro_details c , product_details_master d
					where a.id=b.mst_id  and a.entry_form=73 and c.entry_form=73  and b.id=c.trans_id and d.id=c.prod_id and c.trans_type=4  and  c.po_breakdown_id in($po_id)  and a.company_id='$companyID' and d.item_group_id='$item_group' and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0
					 group by    c.po_breakdown_id,d.item_group_id,a.recv_number,a.id,a.receive_date";
					
				/* $sql_trim2 = "select b.po_breakdown_id,a.item_group_id,
		 			sum(case when b.entry_form in(73) and b.trans_type in(4)  then b.quantity else 0 end) as issue_return_qty
			from 			
				product_details_master a, order_wise_pro_details b, inv_trims_entry_dtls c
			where  
				a.id=b.prod_id and b.dtls_id=c.id and a.item_category_id=4 and b.entry_form in(73)  and b.po_breakdown_id='$po_id'  and a.item_group_id=$item_group and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id,a.item_group_id";	*/
					
					//echo $mrr_sql;
					$dtlsArray2=sql_select($mrr_sql2);
						$tot_qty_issue_ret=0;
					foreach($dtlsArray2 as $row)
					{
						if ($j%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";	
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $j; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $j;?>">
							<td width="30"><p><? echo $j; ?></p></td>
                            <td width="100"><p><? echo $row[csf('recv_number')]; ?></p></td>
                            <td width="75"><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
                           
                            <td width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
                        </tr>
						<?
						$tot_qty_issue_ret+=$row[csf('quantity')];
						$j++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="3" align="right">Total</td>
                        <td align="right"><? echo number_format($tot_qty_issue_ret,2); ?>&nbsp;</td>
                    </tr>
                </tfoot>
            </table>
            <br>
            <table border="1" class="rpt_table" rules="all" width="450" cellpadding="0" cellspacing="0" align="center">
				<tr>
                <caption>Transfer In Detail</caption>
                </tr>
                <thead>
                    <th width="30">Sl</th>
                    <th width="100">Transfer ID</th>
                    <th width="75">Transfer Date</th>
                    <th width="80">Transfer Qty</th>
				</thead>
                <tbody>
                <?
				
				
					$k=1;
					  /*$mrr_sql2="select a.id, a.recv_number, a.receive_date, SUM(c.quantity) as quantity
					from inv_receive_master a, inv_trims_entry_dtls b, order_wise_pro_details c 
					where a.id=b.mst_id  and a.entry_form=73 and c.entry_form=73  and b.id=c.dtls_id and c.trans_type=4  and  c.po_breakdown_id in($po_id)  and a.company_id='$companyID' and b.item_group_id='$item_group' and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 group by    c.po_breakdown_id,b.item_group_id,a.recv_number,a.id,a.receive_date";*/
					
					$sql_transfer = "SELECT b.po_breakdown_id,a.item_group_id,e.transfer_system_id,e.transfer_date,
					sum(case when b.entry_form in(78,112) and b.trans_type in(5)  then b.quantity else 0 end) as item_transfer_issue
					from product_details_master a, order_wise_pro_details b, inv_item_transfer_dtls c,inv_item_transfer_mst e 
					where a.id=b.prod_id and b.dtls_id=c.id and a.item_category_id=4 and e.id=c.mst_id and b.entry_form in(78,112) and b.po_breakdown_id in($po_id) and  a.item_group_id='$item_group' and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and e.status_active=1 and e.is_deleted=0
					group by b.po_breakdown_id,a.item_group_id,e.transfer_system_id,e.transfer_date";	
					
					/* $sql_trim2 = "select b.po_breakdown_id,a.item_group_id,
					sum(case when b.entry_form in(73) and b.trans_type in(4)  then b.quantity else 0 end) as issue_return_qty
					from 			
					product_details_master a, order_wise_pro_details b, inv_trims_entry_dtls c
					where  
					a.id=b.prod_id and b.dtls_id=c.id and a.item_category_id=4 and b.entry_form in(73)  and b.po_breakdown_id='$po_id'  and a.item_group_id=$item_group and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id,a.item_group_id";	*/
					
					//echo $mrr_sql;
					$dtlsArray3=sql_select($sql_transfer);
						$tot_qty_transfer_out=0;
					foreach($dtlsArray3 as $row)
					{
						if ($k%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";	
							if($row[csf('item_transfer_issue')]>0)
							{
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $k;?>">
							<td width="30"><p><? echo $k; ?></p></td>
                            <td width="100"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
                            <td width="75"><p><? echo change_date_format($row[csf('transfer_date')]); ?></p></td>
                           
                            <td width="80" align="right"><p><? echo number_format($row[csf('item_transfer_issue')],2); ?></p></td>
                        </tr>
						<?
						$tot_qty_transfer_out+=$row[csf('item_transfer_issue')];
						$k++;
							}
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="3" align="right">Total</td>
                        <td align="right"><? echo number_format($tot_qty_transfer_out,2); ?>&nbsp;</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </fieldset>
    <?
	exit();
}

if($action=="receive_des_popup")
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<fieldset style="width:470px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="450" cellpadding="0" cellspacing="0" align="center">
			<caption> Recevied Details</caption>
				<thead>
                    <th width="30">Sl</th>
                    <th width="100">Receive ID</th>
                    <th width="75">Receive Date</th>
                    <th width="80">Recv.Qty</th>
				</thead>
                <tbody>
                <?
				
					if($storeName>0) $store_cond=" and e.store_id=$storeName";
					$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details"  );
					$i=1;
					/*$mrr_sql="select a.id, a.recv_number, a.receive_date, b.prod_id,p.item_description, SUM(c.quantity) as quantity
					from  inv_receive_master a, inv_trims_entry_dtls b, order_wise_pro_details c,product_details_master p
					where a.id=b.mst_id  and p.id=b.prod_id and a.entry_form=24 and c.entry_form=24  and b.id=c.dtls_id and c.trans_type=1 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and  c.po_breakdown_id in($po_id)  and a.company_id='$companyID' and b.item_group_id='$item_group' and p.id='$des_prod' group by c.po_breakdown_id,b.item_group_id,p.item_description,a.id, a.recv_number, a.receive_date, b.prod_id ";*/
					
					$mrr_sql="select a.id, a.recv_number, a.receive_date, b.prod_id,p.item_description, SUM(c.quantity) as quantity
					from  inv_receive_master a, inv_trims_entry_dtls b, order_wise_pro_details c,product_details_master p,inv_transaction e
					where a.id=b.mst_id  and p.id=b.prod_id and a.entry_form=24 and c.entry_form=24  and b.id=c.dtls_id and e.id=c.trans_id and c.trans_type=1 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and  c.po_breakdown_id in($po_id)  and a.company_id='$companyID' and b.item_group_id='$item_group' and p.id='$des_prod' $store_cond group by c.po_breakdown_id,b.item_group_id,p.item_description,a.id, a.recv_number, a.receive_date, b.prod_id ";
					//echo $mrr_sql;
					$tot_qty=0;
					$dtlsArray=sql_select($mrr_sql);
					foreach($dtlsArray as $row)
					{
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";	
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="100"><p><? echo $row[csf('recv_number')]; ?></p></td>
                            <td width="75"><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
                        </tr>
						<?
						$tot_qty+=$row[csf('quantity')];
						$i++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="3" align="right">Total</td>
                        <td align="right"><? echo number_format($tot_qty,2); ?>&nbsp;</td>
                    </tr>
                </tfoot>
            </table>
			<br>
			<table border="1" class="rpt_table" rules="all" width="450" cellpadding="0" cellspacing="0" align="center">
							<caption> Issue Return Details</caption>

				<thead>
                    <th width="30">Sl</th>
                    <th width="100">Issue Return ID</th>
                    <th width="75">Issue Return Date</th>
                    <th width="80">Issue Ret.Qty</th>
				</thead>
                <tbody>
                <?
				//from inv_receive_master a,  product_details_master b, order_wise_pro_details c ,inv_transaction d
					$j=1;
				$mrr_sql2="select a.id, a.recv_number, a.receive_date, SUM(c.quantity) as issue_return
					from inv_receive_master a, inv_transaction b, order_wise_pro_details c ,product_details_master d
					where a.id=b.mst_id and d.id=c.prod_id  and a.entry_form=73 and c.entry_form=73  
					and b.id=c.trans_id and c.trans_type=6  and  c.po_breakdown_id in($po_id)  and a.company_id='$companyID'
					 and d.item_group_id='$item_group' and a.is_deleted=0 and a.status_active=1 and b.status_active=1
					  and b.is_deleted=0 group by    c.po_breakdown_id,d.item_group_id,a.recv_number,a.id,a.receive_date";
					$tot_qty_recv_ret=0;
					//echo $mrr_sql;
					$dtlsArray2=sql_select($mrr_sql2);
					foreach($dtlsArray2 as $row)
					{
						if ($j%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";	
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $j; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $j;?>">
							<td width="30"><p><? echo $j; ?></p></td>
                            <td width="100"><p><? echo $row[csf('recv_number')]; ?></p></td>
                            <td width="75"><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($row[csf('issue_return')],2); ?></p></td>
                        </tr>
						<?
						$tot_qty_recv_ret+=$row[csf('issue_return')];
						$j++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="3" align="right">Total</td>
                        <td align="right"><? echo number_format($tot_qty_recv_ret,2); ?>&nbsp;</td>
                    </tr>
                </tfoot>
            </table>
			<br>
			
			<table border="1" class="rpt_table" rules="all" width="450" cellpadding="0" cellspacing="0" align="center">
							<caption> Transfer Out Details</caption>

				<thead>
                    <th width="30">Sl</th>
                    <th width="100">Transfer ID</th>
                    <th width="75">Transfer Date</th>
                    <th width="80">Transfer In.Qty</th>
				</thead>
                <tbody>
                <?
					$k=1;
					$tot_qty_in=0;
					$sql_transfer = "SELECT b.po_breakdown_id,a.item_group_id,e.transfer_system_id,e.transfer_date,
					sum(case when b.entry_form in(78,112) and b.trans_type in(5)  then b.quantity else 0 end) as item_transfer_recv
					from product_details_master a, order_wise_pro_details b, inv_item_transfer_dtls c,inv_item_transfer_mst e 
					where a.id=b.prod_id and b.dtls_id=c.id and a.item_category_id=4 and e.id=c.mst_id and b.entry_form in(78,112) and e.to_company=$companyID and  b.po_breakdown_id in($po_id) and  a.item_group_id='$item_group' and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0
					group by b.po_breakdown_id,a.item_group_id,e.transfer_system_id,e.transfer_date";	
					//echo $sql_transfer;
					$dtlsArray3=sql_select($sql_transfer);
					foreach($dtlsArray3 as $row)
					{
						if ($k%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";	
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $k;?>">
							<td width="30"><p><? echo $k; ?></p></td>
                            <td width="100"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>

                            <td width="75"><p><? echo change_date_format($row[csf('transfer_date')]); ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($row[csf('item_transfer_recv')],2); ?></p></td>
                        </tr>
						<?
						$tot_qty_in+=$row[csf('item_transfer_recv')];
						$k++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="3" align="right">Total</td>
                        <td align="right"><? echo number_format($tot_qty_in,2); ?>&nbsp;</td>
                    </tr>
                </tfoot>
            </table>
		 </div>
    </fieldset>
    <?
	exit();
}


if($action=="issue_popup")
{
	echo load_html_head_contents("Issue Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	
	  
				
	?>
	<fieldset style="width:470px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="450" cellpadding="0" cellspacing="0" align="center">
				<caption> Issue Details</caption> 
                <thead>
                    <th width="30">Sl</th>
                    <th width="100">Issue ID</th>
                    <th width="75">Issue Date</th>
                    <th width="80">Issue Qty</th>
				</thead>
                <tbody>
                <?
					/*$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details"  );
					$i=1;
					 $mrr_sql="select a.id, a.issue_number, a.issue_date,SUM(c.quantity) as quantity
					from  inv_issue_master a,inv_trims_issue_dtls b, order_wise_pro_details c,product_details_master p 
					where a.id=b.mst_id  and a.entry_form=25 and p.id=c.prod_id and b.id=c.dtls_id and c.trans_type=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and  c.po_breakdown_id in($po_id)  and a.company_id='$companyID' and p.item_group_id='$item_group' group by c.po_breakdown_id, p.item_group_id,a.issue_number,a.id,a.issue_date ";*/
					if($storeName>0) $store_cond=" and e.store_id=$storeName";
					$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details"  );
					$i=1;
					 $mrr_sql="select a.id, a.issue_number, a.issue_date,SUM(c.quantity) as quantity
					from  inv_issue_master a,inv_trims_issue_dtls b, order_wise_pro_details c,product_details_master p  ,inv_transaction e
					where a.id=b.mst_id  and a.entry_form=25 and p.id=c.prod_id and b.id=c.dtls_id and e.id=c.trans_id and c.trans_type=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and  c.po_breakdown_id in($po_id)  and a.company_id='$companyID' $store_cond   and p.item_group_id='$item_group' group by c.po_breakdown_id, p.item_group_id,a.issue_number,a.id,a.issue_date ";
					//echo $mrr_sql;
					$dtlsArray=sql_select($mrr_sql);
					
					foreach($dtlsArray as $row)
					{
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";	
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="100"><p><? echo $row[csf('issue_number')]; ?></p></td>
                            <td width="75"><p><? echo change_date_format($row[csf('issue_date')]); ?></p></td>
                           
                            <td width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
                        </tr>
						<?
						$tot_qty+=$row[csf('quantity')];
						$i++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="3" align="right">Total</td>
                        <td align="right"><? echo number_format($tot_qty,2); ?>&nbsp;</td>
                    </tr>
                </tfoot>
            </table>
            <br>
            <table border="1" class="rpt_table" rules="all" width="450" cellpadding="0" cellspacing="0" align="center">
            <caption>  Recv Return Details </caption> 
				<thead>
                    <th width="30">Sl</th>
                    <th width="100">Issue ID</th>
                    <th width="75">Issue Date</th>
                    <th width="80">Recv Ret. Qty</th>
				</thead>
                <tbody>
                <?
					$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details"  );
					$i=1;
					 $mrr_sql2="select a.id, a.issue_number, a.issue_date,SUM(c.quantity) as quantity
					from  inv_issue_master a,inv_trims_issue_dtls b, order_wise_pro_details c,product_details_master p 
					where a.id=b.mst_id  and a.entry_form=49 and p.id=b.prod_id and b.id=c.dtls_id and c.trans_type=3 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and  c.po_breakdown_id in($po_id)  and a.company_id='$companyID' and p.item_group_id='$item_group' group by c.po_breakdown_id,p.item_group_id,a.issue_number,a.id,a.issue_date ";
					//echo $mrr_sql;
					$dtlsArray2=sql_select($mrr_sql2);
					$tot_qty_recv_ret=0;
					foreach($dtlsArray2 as $row)
					{
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";	
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="100"><p><? echo $row[csf('issue_number')]; ?></p></td>
                            <td width="75"><p><? echo change_date_format($row[csf('issue_date')]); ?></p></td>
                           
                            <td width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
                        </tr>
						<?
						$tot_qty_recv_ret+=$row[csf('quantity')];
						$i++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="3" align="right">Total</td>
                        <td align="right"><? echo number_format($tot_qty_recv_ret,2); ?>&nbsp;</td>
                    </tr>
                </tfoot>
            </table>
            <br>
            <table border="1" class="rpt_table" rules="all" width="450" cellpadding="0" cellspacing="0" align="center">
              <caption>  Transfer Out Details </caption> 
				<thead>
                    <th width="30">Sl</th>
                    <th width="100">Transfer. ID</th>
                    <th width="75">Transfer Date</th>
                    <th width="80">Transfer In Qty</th>
				</thead>
                <tbody>
                <?
					$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details"  );
					$i=1;
						
					$sql_transfer = "SELECT b.po_breakdown_id,a.item_group_id,e.transfer_system_id,e.transfer_date,
					sum(case when b.entry_form in(78,112) and b.trans_type in(6)  then b.quantity else 0 end) as item_transfer_recv
					from product_details_master a, order_wise_pro_details b, inv_item_transfer_dtls c,inv_item_transfer_mst e 
					where a.id=b.prod_id and b.dtls_id=c.id and a.item_category_id=4 and e.id=c.mst_id and b.entry_form in(78,112) and  b.po_breakdown_id in($po_id)  and  a.item_group_id='$item_group' and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id,a.item_group_id,e.transfer_system_id,e.transfer_date";	
					$dtlsArray=sql_select($sql_transfer);
					$tot_qty_transfer_recv=0;
					foreach($dtlsArray as $row)
					{
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
							if($row[csf('item_transfer_recv')]>0)
							{	
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="100"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
                            <td width="75"><p><? echo change_date_format($row[csf('transfer_date')]); ?></p></td>
                           
                            <td width="80" align="right"><p><? echo number_format($row[csf('item_transfer_recv')],2); ?></p></td>
                        </tr>
						<?
						$tot_qty_transfer_recv+=$row[csf('item_transfer_recv')];
						$i++;
						}
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="3" align="right">Total</td>
                        <td align="right"><? echo number_format($tot_qty_transfer_recv,2); ?>&nbsp;</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </fieldset>
    <?
	exit();
}

if($action=="issue_des_popup")
{
	echo load_html_head_contents("Issue Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<fieldset style="width:470px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="450" cellpadding="0" cellspacing="0" align="center">
				<caption> Issue Details</caption>
				<thead>
                    <th width="30">Sl</th>
                    <th width="100">Issue ID</th>
                    <th width="75">Issue Date</th>
                    <th width="80">Issue Qty</th>
				</thead>
                <tbody>
                <?
					if($storeName>0) $store_cond=" and e.store_id=$storeName";	
					$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details"  );
					$i=1;
					/*$mrr_sql="select a.id, a.issue_number, a.issue_date, b.prod_id,p.item_description, SUM(c.quantity) as quantity
					from  inv_issue_master a,inv_trims_issue_dtls b, order_wise_pro_details c,product_details_master p 
					where a.id=b.mst_id  and a.entry_form=25 and p.id=b.prod_id and b.id=c.dtls_id and c.trans_type=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and  c.po_breakdown_id in($po_id) and a.company_id='$companyID' and p.item_group_id='$item_group' and p.id='$des_prod' 
					group by c.po_breakdown_id,p.item_group_id,p.item_description,a.issue_number,a.id,a.issue_date, b.prod_id ";*/
					
					$mrr_sql="select a.id, a.issue_number, a.issue_date, b.prod_id,p.item_description, SUM(c.quantity) as quantity
					from  inv_issue_master a,inv_trims_issue_dtls b, order_wise_pro_details c,product_details_master p ,inv_transaction e
					where a.id=b.mst_id  and a.entry_form=25 and p.id=b.prod_id and b.id=c.dtls_id  and e.id=c.trans_id and   c.trans_type=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and  c.po_breakdown_id in($po_id) and a.company_id='$companyID' and p.item_group_id='$item_group' and p.id='$des_prod'  $store_cond
					group by c.po_breakdown_id,p.item_group_id,p.item_description,a.issue_number,a.id,a.issue_date,b.prod_id ";
					
					//echo $mrr_sql;
					$tot_qty=0;
					$dtlsArray=sql_select($mrr_sql);
					
					foreach($dtlsArray as $row)
					{
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";	
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="100"><p><? echo $row[csf('issue_number')]; ?></p></td>
                            <td width="75"><p><? echo change_date_format($row[csf('issue_date')]); ?></p></td>
                           
                            <td width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
                        </tr>
						<?
						$tot_qty+=$row[csf('quantity')];
						$i++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="3" align="right">Total</td>
                        <td align="right"><? echo number_format($tot_qty,2); ?>&nbsp;</td>
                    </tr>
                </tfoot>
            </table>
			<br/>
			<table border="1" class="rpt_table" rules="all" width="450" cellpadding="0" cellspacing="0" align="center">
				<caption> Recv Ret Detail</caption>
				<thead>
                    <th width="30">Sl</th>
                    <th width="100">Recv ID</th>
                    <th width="75">Recv Date</th>
                    <th width="80">Recv. Ret Qty</th>
				</thead>
                <tbody>
                <?
					$i=1;
					$mrr_sql="select a.id, a.issue_number, a.issue_date, b.prod_id,p.item_description, SUM(c.quantity) as quantity
					from  inv_issue_master a,inv_trims_issue_dtls b, order_wise_pro_details c,product_details_master p 
					where a.id=b.mst_id  and a.entry_form=49 and p.id=b.prod_id and b.id=c.dtls_id and c.trans_type=3 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and  c.po_breakdown_id in($po_id)  and a.company_id='$companyID' and p.item_group_id='$item_group' and p.id='$des_prod' 
					group by c.po_breakdown_id,p.item_group_id,p.item_description,a.issue_number,a.id,a.issue_date, b.prod_id ";
					
					//echo $mrr_sql;
					$tot_qty=0;
					$dtlsArray=sql_select($mrr_sql);
					
					foreach($dtlsArray as $row)
					{
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";	
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="100"><p><? echo $row[csf('issue_number')]; ?></p></td>
                            <td width="75"><p><? echo change_date_format($row[csf('issue_date')]); ?></p></td>
                           
                            <td width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
                        </tr>
						<?
						$tot_qty+=$row[csf('quantity')];
						$i++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="3" align="right">Total</td>
                        <td align="right"><? echo number_format($tot_qty,2); ?>&nbsp;</td>
                    </tr>
                </tfoot>
            </table>
			<br/>
			<table border="1" class="rpt_table" rules="all" width="450" cellpadding="0" cellspacing="0" align="center">
				<caption>Transfer In Details</caption>
				<thead>
                    <th width="30">Sl</th>
                    <th width="100">Transfer ID</th>
                    <th width="75">Transfer Date</th>
                    <th width="80">Transfer Qty</th>
				</thead>
                <tbody>
                <?
					$k=1;
					$sql_transfer = "SELECT b.po_breakdown_id,a.item_group_id,e.transfer_system_id,e.transfer_date,
					sum(case when b.entry_form in(78,112) and b.trans_type in(6) then b.quantity else 0 end) as item_transfer_recv
					from product_details_master a, order_wise_pro_details b, inv_item_transfer_dtls c,inv_item_transfer_mst e 
					where a.id=b.prod_id and b.dtls_id=c.id and a.item_category_id=4 and e.id=c.mst_id and b.entry_form in(78,112) and b.po_breakdown_id in($po_id) and  a.item_group_id='$item_group' and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id='$des_prod' group by b.po_breakdown_id,a.item_group_id,e.transfer_system_id,e.transfer_date";	
					//echo $sql_transfer;
					$dtlsArray3=sql_select($sql_transfer);
					$tot_qty_recv_ret=0;
					foreach($dtlsArray3 as $row)
					{
						if ($row[csf('item_transfer_recv')] > 0 ) // discuse with Mr. Bijoy
						{
							if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
							?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $k;?>">
								<td width="30"><p><? echo $k; ?></p></td>
	                            <td width="100"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
	                            <td width="75"><p><? echo change_date_format($row[csf('transfer_date')]); ?></p></td>
	                            <td width="80" align="right"><p><? echo number_format($row[csf('item_transfer_recv')],2); ?></p></td>
	                        </tr>
							<?
						}
						$tot_qty_recv_ret+=$row[csf('item_transfer_recv')];
						$k++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="3" align="right">Total</td>
                        <td align="right"><? echo number_format($tot_qty_recv_ret,2); ?>&nbsp;</td>
                    </tr>
                </tfoot>
            </table>
			
        </div>
    </fieldset>
    <?
	exit();
}


?>