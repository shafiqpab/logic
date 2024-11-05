<?

header('Content-type:text/html; charset=utf-8');
session_start();
require_once('../../../../includes/common.php');
require_once('../../../../includes/class4/class.conditions.php');
require_once('../../../../includes/class4/class.reports.php');
require_once('../../../../includes/class4/class.trims.php');


$user_id=$_SESSION['logic_erp']['user_id'];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
//---------------------------------------------------- Start---------------------------------------------------------------------------
$color_library=return_library_array("select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
$color_size_library=return_library_array("select id,size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name");

$company_library=return_library_array("select id,company_name from lib_company", "id", "company_name");
$buyer_arr=return_library_array("select id, short_name from lib_buyer",'id','short_name');
$trim_group= return_library_array("select id, item_name from lib_item_group where status_active=1 and is_deleted=0",'id','item_name');

if ($action=="load_drop_down_buyer")
{
	//$data=explode('_',$data);
	echo create_drop_down( "cbo_buyer_id", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",0);  
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
if ($action=="order_no_popup")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);
	$company=$data[0];
	$buyer=$data[1];
	$style=$data[2];
	
	//print ($data[1]);
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
	if ($data[0]==0) $company_id=""; else $company_id=" and company_name=$data[0]";
	if ($data[1]==0) $buyer_id=""; else $buyer_id=" and buyer_name=$data[1]";
	if ($data[2]==0) $style=""; else $style=" and b.id in($data[2])";
	
	/*if($db_type==0) $year_cond="and year(a.insert_date)='$data[3]'"; 
	else if($db_type==2) $year_cond="and to_char(a.insert_date,'YYYY')='$data[3]'";*/
	
	if($db_type==0) $year_field="YEAR(b.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(b.insert_date,'YYYY') as year";
	else $year_field="";
	
	//$sql ="select distinct a.id,a.po_number,b.job_no_prefix_num as job_prefix,$year_field from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no $company_id  $buyer_id $style $year_cond";
	
	$sql ="select a.id,a.po_number,b.job_no_prefix_num as job_prefix,$year_field from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and a.is_deleted=0 $company_id  $buyer_id $style";
	
	//echo $sql;
	 
	echo create_list_view("list_view", "Order Number,Job No, Year","150,100,50","440","310",0, $sql , "js_set_value", "id,po_number", "", 1, "0", $arr, "po_number,job_prefix,year", "","setFilterGrid('list_view',-1)","0","",1) ;	
	exit();
}
// Style Wise Search.
if ($action=="report_generate_style")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$cbo_company=str_replace("'","",$cbo_company_id);
	$cbo_buyer=str_replace("'","",$cbo_buyer_id);
	$all_style=str_replace("'","",$txt_style_id);
	$all_style=array_unique(explode(",",$all_style));
	$all_style_quted="";
	foreach($all_style as $style_no)
	{
		$all_style_quted.="'".$style_no."'".",";
	}
	$all_style_quted=chop($all_style_quted,",");
	
	//echo $all_style_quted;die;
	
	if($cbo_buyer==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$cbo_buyer";
	}
	
	if(str_replace("'","",trim($txt_order_no))=="")
	{
		$po_id_cond="";
	}
	else
	{
		if(str_replace("'","",$txt_order_no_id)!="")
		{
			$po_id_cond=" and b.id in(".str_replace("'","",$txt_order_no_id).")";
		}
		else
		{
			$po_number=trim(str_replace("'","",$txt_order_no))."%";
			$po_id_cond=" and b.po_number like '$po_number'";
		}
	}
	if(str_replace("'","",$txt_file_no)!="") $file_cond=" and b.file_no in(".str_replace("'","",$txt_file_no).")"; else $file_cond="";
	if(str_replace("'","",$txt_ref_no)!="") $ref_cond=" and b.grouping in(".$txt_ref_no.")"; else $ref_cond="";
	
	if(str_replace("'","",$txt_style)!="") $style=" and a.id in(".str_replace("'","",$txt_style).")"; else $style="";
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	if( $date_from!="" && $date_to!="") $pub_date= " and b.pub_shipment_date between '".$date_from."' and '".$date_to."'"; else $pub_date="";
	
	$condition= new condition();
	$condition->company_name("=$cbo_company_id");
	if(str_replace("'","",$cbo_buyer_id)>0)
	{
		$condition->buyer_name("=$cbo_buyer_id");
	}
	if(str_replace("'","",$txt_style_id) !='')
	{
		$condition->style_ref_no(" in($all_style_quted)");
	}
	if(str_replace("'","",$txt_order_no_id)!='')
	{
		$condition->po_id("in(".str_replace("'","",$txt_order_no_id).")");
	}
	if(str_replace("'","",$txt_file_no) !='')
	{
		$condition->file_no("=$txt_file_no");
	}
	if(str_replace("'","",$txt_ref_no) !='')
	{
		$condition->grouping("=$txt_ref_no");
	}
	if(str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!='')
	{
		$condition->pub_shipment_date(" between '$date_from' and '$date_to'");
	}
	$condition->init();
	
	$trims= new trims($condition);
	//echo $trims->getQuery();die;
	$trims_costing_arr=$trims->getQtyArray_by_orderAndItemid();
	
	//echo "<pre>";
	//print_r($trims_costing_arr);die;
	
	 
	ob_start();	
	?>
    <fieldset style="width:2320px;">
        <table width="2320">
            <tr class="form_caption">
                <td colspan="24" align="center" style="border:none;font-size:16px; font-weight:bold"> <? echo $report_title; ?></td>
            </tr>
            <tr class="form_caption">
                <td colspan="24" align="center"><? echo $company_library[$cbo_company]; ?></td>
            </tr>
        </table>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="2320" class="rpt_table" >
			<thead>
				<th width="40">SL </th>
                <th width="70">Buyer</th>
                <th width="100">Job No</th>
                <th width="110">Style</th>
                <th width="140">Order No</th>
                <th width="70">File No</th>
                <th width="80">Ref. No</th>
                <th width="90">Order Qty.</th>
                <th width="80">Order Qty.(Dzn)</th>
                <th width="130">Item Group</th>
                <th width="150">Item Description</th>
                <th width="60">UOM</th>
                <th width="90">Req. Qty</th>
                <th width="100">Item Color</th>
                <th width="60">Item Size</th>
                <th width="80">WO. Qty</th>
                <th width="80">WO. Value</th>
                <th width="90">Recv. Qty</th>
                <th width="80">Recv. Bal.</th>
                <th width="100">Recv. Value</th>
                <th width="90">Issue Qty.</th>
                <th width="80">Issue Value</th>
                <th width="100">Left Over</th>
                <th width="60">Rate</th>
                <th width="60">Currency</th>
                <th>Left Over Value</th>
            </thead>
        </table>
        <div style="width:2320px; overflow-y:scroll; max-height:350px;font-size:12px; " id="scroll_body">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="2300" class="rpt_table" id="tbl_issue_status" >
		   <?
			$i=1; $y=1; $tot_receive_qty=0; $tot_receive_value=0; $tot_issue_qty=0; $total_left_over=0; $total_left_over_balance=0; $dataArrayRecv=array();$wo_qty_ArrayRecv=array();$wo_qty_ArrayRecv_pi=array();$total_wo_qty=0;$total_wo_val=$total_wo_amount=$total_issue_amount=0;
			
			
			 
			$sql_bookingqty_non = sql_select("select sum(b.trim_qty) as wo_qnty,sum(b.amount) as amount,b.trim_group as item_group,b.fabric_color as item_color,b.gmts_color as color_number_id,b.fabric_description as description from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where a.company_id=$cbo_company and a.booking_no=b.booking_no  $buyer_id_cond group by  b.trim_group,b.fabric_color,b.gmts_color,b.fabric_description");
			foreach($sql_bookingqty_non as $row)
			{
				$wo_qty_ArrayRecv[$row[csf('item_group')]][$row[csf('item_color')]][$row[csf('fabric_color')]]=$row[csf('wo_qnty')];
				$wo_qty_ArrayRecvAmt[$row[csf('item_group')]][$row[csf('item_color')]][$row[csf('fabric_color')]]=$row[csf('amount')];
			}
			
			$sql_pi = sql_select("select b.quantity as wo_qnty,b.item_group,b.item_color,b.color_id as color_number_id,b.item_description as description from  com_pi_master_details a,com_pi_item_details b where a.id=b.pi_id and a.item_category_id=4 and a.importer_id=$cbo_company ");		
			foreach($sql_pi as $row)
			{
				$wo_qty_ArrayRecv_pi[$row[csf('item_group')]][$row[csf('item_color')]][$row[csf('fabric_color')]]+=$row[csf('wo_qnty')];
				
			}
			
			
			if($db_type==0)
			{
				$sql="select a.style_ref_no, a.buyer_name, a.job_no, group_concat(b.po_number) as po_number, group_concat(b.id) as po_id,group_concat(b.file_no) as file_no,group_concat(b.grouping) as grouping, sum(c.order_quantity) as po_quantity from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst a.job_no=c.job_no_mst and b.id=c.po_break_down_id  and a.is_deleted=0 and a.status_active=1  and b.is_deleted=0 and b.status_active=1 and a.company_name=$cbo_company $buyer_id_cond $style $po_id_cond $file_cond $ref_cond $pub_date group by a.job_no, a.style_ref_no, a.buyer_name order by a.id";
			} 
			else
			{
				//rtrim(xmlagg(xmlelement(e,b.id,',').extract('//text()') order by b.id).GetClobVal(),',') as dtls_id
				/*$sql="select a.style_ref_no, a.buyer_name, a.job_no, LISTAGG(CAST(b.po_number AS VARCHAR2(4000)),',') WITHIN GROUP (ORDER BY b.id) as po_number, LISTAGG(CAST(b.id AS VARCHAR2(4000)),',') WITHIN GROUP (ORDER BY b.id) as po_id,LISTAGG(CAST(b.file_no AS VARCHAR2(4000)),',') WITHIN GROUP (ORDER BY b.id) as file_no,LISTAGG(CAST(b.grouping AS VARCHAR2(4000)),',') WITHIN GROUP (ORDER BY b.id) as grouping, sum(a.total_set_qnty*b.po_quantity) as po_quantity from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$cbo_company $buyer_id_cond $style $po_id_cond $pub_date $file_cond $ref_cond group by a.id, a.job_no, a.style_ref_no, a.buyer_name, a.total_set_qnty order by a.id";*/
				
				$sql="select a.style_ref_no, a.buyer_name, a.job_no,a.currency_id, rtrim(xmlagg(xmlelement(e,b.po_number,',').extract('//text()') order by b.id).GetClobVal(),',') as po_number,
				rtrim(xmlagg(xmlelement(e,b.id,',').extract('//text()') order by b.id).GetClobVal(),',') as po_id, rtrim(xmlagg(xmlelement(e,b.file_no,',').extract('//text()') order by b.id).GetClobVal(),',') as file_no, rtrim(xmlagg(xmlelement(e,b.grouping,',').extract('//text()') order by b.id).GetClobVal(),',') as grouping, sum(c.order_quantity) as po_quantity 
				from wo_po_details_master a, wo_po_break_down b ,wo_po_color_size_breakdown c
				where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.company_name=$cbo_company $buyer_id_cond $style $po_id_cond $pub_date $file_cond $ref_cond
				group by a.id, a.job_no, a.style_ref_no,a.currency_id, a.buyer_name, a.total_set_qnty order by a.id";
			}
			
			// echo $sql;die;
			$result=sql_select($sql);
			$all_order_id="";
			foreach($result as $row)
			{
				$all_order_id.=$row[csf("po_id")]->load().",";
			}
			$all_order_id=chop($all_order_id,",");
			$p=1;
			$sql_recv="select c.receive_basis, c.booking_without_order as without_order, a.item_group_id, a.item_description, c.store_id, a.order_uom, a.item_color, a.item_size, a.cons_rate, b.po_breakdown_id as po_id,a.gmts_color_id, b.quantity , b.order_amount as amount, b.trans_type
			from inv_receive_master c, inv_trims_entry_dtls a, order_wise_pro_details b,product_details_master d 
			where a.id=b.dtls_id and a.trans_id=b.trans_id  and a.prod_id=d.id and c.id=a.mst_id and b.trans_type=1 and b.entry_form=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1"; //and a.prod_id=21968 
			//echo count(array_unique(explode(",",$all_order_id))).'=';
			//echo $sql_recv;die;
			if($all_order_id!="")
			{
				$all_order_id_arr=array_chunk(array_unique(explode(",",$all_order_id)),999);
				foreach($all_order_id_arr as $order_id)
				{
					if($p==1) $sql_recv .=" and (b.po_breakdown_id in(".implode(',',$order_id).")"; else $sql_recv .=" or b.po_breakdown_id  in(".implode(',',$order_id).")";
					$p++;
				}
				$sql_recv .=" )";
			}
			$sql_recv.="union all select 0 as receive_basis, 0 as without_order, a.item_group_id, a.item_description, c.store_id, 0 as order_uom, a.item_color, a.item_size, c.cons_rate, b.po_breakdown_id as po_id, a.color as gmts_color_id, b.quantity, b.order_amount as amount, b.trans_type
			from product_details_master a , order_wise_pro_details b, inv_transaction c 
			where a.id=b.prod_id and b.trans_id=c.id and a.item_category_id=4 and a.entry_form=24 and b.entry_form in(78,112) and b.trans_type in(5) and c.transaction_type in(5) and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
			$p=1;
			if($all_order_id!="")
			{
				$all_order_id_arr=array_chunk(array_unique(explode(",",$all_order_id)),999);
				foreach($all_order_id_arr as $order_id)
				{
					if($p==1) $sql_recv .=" and (b.po_breakdown_id in(".implode(',',$order_id).")"; else $sql_recv .=" or b.po_breakdown_id  in(".implode(',',$order_id).")";
					$p++;
				}
				$sql_recv .=" )";
			}
			
			//echo $sql_recv;
			
			$sql_recv_result=sql_select($sql_recv);
			foreach($sql_recv_result as $row)
			{ 
				//if($row[csf('item_color')]==0) $row[csf('item_color')]=404; only for JK
				if($row[csf('item_size')]=="") $item_size=0; else $item_size=$row[csf('item_size')];
				$dataArrayRecv[$row[csf('po_id')]].=$row[csf('item_group_id')]."_".$row[csf('order_uom')]."_".$row[csf('item_color')]."_".$item_size."_".$row[csf('cons_rate')]."_".$row[csf('quantity')]."_".$row[csf('receive_basis')]."_".$row[csf('without_order')]."_".$row[csf('gmts_color_id')]."_".$row[csf('item_description')]."_".$row[csf('store_id')]."_".$row[csf('amount')]."_".$row[csf('trans_type')]."___";
			}
			
			//echo "<pre>";print_r($dataArrayRecv);die;
			
			
			if($db_type==0) $null_val="c.color_number_id,c.item_color,c.item_size,c.gmts_sizes,";
			else if($db_type==2) $null_val="nvl(c.color_number_id,0) as color_number_id,nvl(c.item_color,0) as item_color,nvl(c.gmts_sizes,0) as gmts_sizes,nvl(c.item_size,0) as item_size,";
			$sql_bookingqty ="select b.job_no,(c.cons) as wo_qnty,(c.amount) as amount,b.trim_group as item_group, $null_val c.description,c.brand_supplier,b.po_break_down_id as po_id,b.sensitivity from wo_booking_mst a, wo_booking_dtls b,wo_trim_book_con_dtls c where a.booking_no=b.booking_no and b.id=c.wo_trim_booking_dtls_id  and a.company_id=$cbo_company and a.item_category=4 and c.cons>0   and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ";
			$p=1;
			if($all_order_id!="")
			{
				$all_order_id_arr=array_chunk(array_unique(explode(",",$all_order_id)),999);
				foreach($all_order_id_arr as $order_id)
				{
					if($p==1) $sql_bookingqty .=" and (b.po_break_down_id in(".implode(',',$order_id).")"; else $sql_bookingqty .=" or b.po_break_down_id  in(".implode(',',$order_id).")";
					$p++;
				}
				$sql_bookingqty .=" )";
			}
			//$sql_bookingqty .=" ";
			//echo $sql_bookingqty;
			//echo $sql_bookingqty;die;
			$sql_bookingqty_result=sql_select($sql_bookingqty);
			//echo "<pre>";print_r($sql_bookingqty_result);die;
			foreach($sql_bookingqty_result as $row)
			{
				if($row[csf('item_size')]=="" || $row[csf('item_size')]=="0") $item_size=0; else $item_size=$row[csf('item_size')];
				$wo_qty_ArrayRecv[$row[csf('po_id')]][$row[csf('item_group')]][$row[csf('item_color')]][$item_size]+=$row[csf('wo_qnty')];
				$wo_qty_ArrayRecvAmt[$row[csf('po_id')]][$row[csf('item_group')]][$row[csf('item_color')]][$item_size]+=$row[csf('amount')];
				
				$job_wo_qty_ArrayRecv[$row[csf('job_no')]][$row[csf('item_group')]][$row[csf('item_color')]][$item_size]+=$row[csf('wo_qnty')];
			}
			//echo count($wo_qty_ArrayRecv);die;
			//echo "<pre>"; print_r($wo_qty_ArrayRecv);die;
			
			$issue_qty_sql="select b.po_breakdown_id, a.item_color as item_color_id, a.item_size, a.item_group_id, b.quantity, c.cons_rate as rate, b.entry_form, b.trans_type 
			from product_details_master a , order_wise_pro_details b, inv_transaction c 
			where a.id=b.prod_id and b.trans_id=c.id and item_category_id=4 and a.entry_form=24 and b.entry_form in(25,49,73,78,112) and b.trans_type in(2,3,4,6) and c.transaction_type in(2,3,4,6) and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
			$p=1;
			if($all_order_id!="")
			{
				$all_order_id_arr=array_chunk(array_unique(explode(",",$all_order_id)),999);
				foreach($all_order_id_arr as $order_id)
				{
					if($p==1) $issue_qty_sql .=" and (b.po_breakdown_id in(".implode(',',$order_id).")"; else $issue_qty_sql .=" or b.po_breakdown_id  in(".implode(',',$order_id).")";
					$p++;
				}
				$issue_qty_sql .=" )";
			}
			 //echo $issue_qty_sql;
			$issue_qty_sql_result=sql_select($issue_qty_sql);
			$issue_data_arr=array();
			foreach($issue_qty_sql_result as $row)
			{
				if($row[csf('item_color_id')]==0) $row[csf('item_color_id')]=404;
				if($row[csf('item_size')]=="") $item_size_id=0; else $item_size_id=$row[csf('item_size')];
				$issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][$row[csf('item_color_id')]][$item_size_id]["rate"]=$row[csf('rate')];
				if($row[csf('entry_form')]==25 && $row[csf('trans_type')]==2)
				{
					$issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][$row[csf('item_color_id')]][$item_size_id]["issue_quantity"]+=$row[csf('quantity')];
				}
				if($row[csf('entry_form')]==49 && $row[csf('trans_type')]==3)
				{
					$issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][$row[csf('item_color_id')]][$item_size_id]["rcv_rtn_quantity"]+=$row[csf('quantity')];
				}
				if($row[csf('entry_form')]==73 && $row[csf('trans_type')]==4)
				{
					$issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][$row[csf('item_color_id')]][$item_size_id]["issue_rtn_quantity"]+=$row[csf('quantity')];
				}
				/*if(($row[csf('entry_form')]==78 || $row[csf('entry_form')]==112) && $row[csf('trans_type')]==5)
				{
					$issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][$row[csf('item_color_id')]][$item_size_id]["transfer_in_quantity"]+=$row[csf('quantity')];
				}*/
				if(($row[csf('entry_form')]==78 || $row[csf('entry_form')]==112) && $row[csf('trans_type')]==6)
				{
					$issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][$row[csf('item_color_id')]][$item_size_id]["transfer_out_quantity"]+=$row[csf('quantity')];
				}
				
			}
			//echo "jahid<pre>";
			//print_r($issue_data_arr[17508][220]);die;
			//echo "<pre>"; print_r($wo_qty_ArrayRecv[13749][9][11]);die;
			//echo "<pre>"; print_r($issue_data_arr[13749][9][11]);die;
			
			$req_check=array();
			foreach ($result as $row)
			{
				$x=0; $z=0; $dataArray=array(); $uomArray=array(); $rowspan_array=array(); $rowspan_color_array=array();
				$all_po_id=$all_po_num=$all_file_no=$all_gropping="";
				if($db_type==0)
				{
					$all_po_id=implode(",",array_unique(explode(",",$row[csf('po_id')])));
					$all_po_num=implode(",",array_unique(explode(",",$row[csf('po_number')])));
					$all_file_no=implode(",",array_unique(explode(",",$row[csf('file_no')])));
					$all_gropping=implode(",",array_unique(explode(",",$row[csf('grouping')])));
				}
				else
				{
					$all_po_id=implode(",",array_unique(explode(",",$row[csf('po_id')]->load())));
					$all_po_num=implode(",",array_unique(explode(",",$row[csf('po_number')]->load())));
					$all_file_no=implode(",",array_unique(explode(",",$row[csf('file_no')]->load())));
					$all_gropping=implode(",",array_unique(explode(",",$row[csf('grouping')]->load())));
				}
				
				$job_po_id=explode(",",$all_po_id);
				foreach($job_po_id as $po_id)
				{
					//echo $dataArrayRecv[$po_id];die; chop($dataArrayRecv[$po_id],",")
					$dataRecv=explode("___",substr($dataArrayRecv[$po_id],0,-1));
					//print_r($dataRecv);die;
					foreach($dataRecv as $recvRow)
					{
						$recvRow=explode("_",$recvRow);
						$item_group_id=$recvRow[0];
						$order_uom=$recvRow[1];
						$item_color=$recvRow[2];
						$item_size=$recvRow[3];
						if($item_size=="") $item_size=0;
						$cons_rate=$recvRow[4];
						$quantity=$recvRow[5];
						$recv_basis=$recvRow[6];
						$without_order=$recvRow[7];
						$gmts_color=$recvRow[8];
						$item_description=$recvRow[9];
						$store_name_id=$recvRow[10];
						$recv_value=$recvRow[11];
						$trans_type=$recvRow[12];
						
						
						if($without_order=="") $without_order=0;
						
						//$recv_value=$cons_rate*$quantity;
						//$recv_data='".$recv_basis."'**'".$without_order."';
						
						
						if($quantity>0)
						{
							if($dataArray[$item_group_id][$item_color][$item_size]['qty']=="")
							{ 
								$rowspan_array[$item_group_id]+=1;
								$rowspan_color_array[$item_group_id][$item_color]+=1;
								
								$z++;
							}
							
							$dataArray[$item_group_id][$item_color][$item_size]['qty']+=$quantity;
							$dataArray[$item_group_id][$item_color][$item_size]['val']+=$recv_value;
							$uomArray[$item_group_id]=$order_uom;
							
							
							($descriptionArray[$item_group_id] =="")? $descriptionArray[$item_group_id]=$item_description:$descriptionArray[$item_group_id].=",".$item_description;
							($storeArray[$item_group_id] =="")? $storeArray[$item_group_id]=$store_name_arr[$store_name_id]:$storeArray[$item_group_id].=",".$store_name_arr[$store_name_id];
						}
					}
				}
				//echo $z;die; echo count($dataArray);
				if($z>0)
				{
					//echo "<pre>";print_r($dataArray);die;
					foreach($dataArray as $item_group_id=>$item_group_data)
					{ 
						$s=0;
						foreach($item_group_data as $item_color_id=>$item_color_data)
						{
							
							$c=0;
							
							foreach($item_color_data as $item_size=>$item_size_data)
							{
								
								$recv_qnty=$item_size_data['qty']; 
								$recv_value=$item_size_data['val'];
								$ord_avg_rate=$recv_value/$recv_qnty;
								$issue_qty=$issue_amount=$rcv_rtn_qty=$issue_rtn_qty=$transfer_in_qty=$transfer_out_qty=$net_recv_qnty=$net_recv_value=$net_issue_qnty=$net_issue_value=$wo_qty=$wo_amount=0;
								foreach($job_po_id as $po_id)
								{
									if($item_color_id==0) $item_color_id=404;
									if($po_mrr_check[$po_id][$item_group_id][$item_color_id][$item_size]=="")
									{
										$po_mrr_check[$po_id][$item_group_id][$item_color_id][$item_size]=$po_id;
										$issue_rate=$issue_qty_rate_arr[$po_id][$item_group_id][$item_color_id][$item_size]["rate"];
										$issue_qty+=$issue_data_arr[$po_id][$item_group_id][$item_color_id][$item_size]["issue_quantity"];
										$rcv_rtn_qty+=$issue_data_arr[$po_id][$item_group_id][$item_color_id][$item_size]["rcv_rtn_quantity"];
										$issue_rtn_qty+=$issue_data_arr[$po_id][$item_group_id][$item_color_id][$item_size]["issue_rtn_quantity"];
										//$transfer_in_qty+=$issue_data_arr[$po_id][$item_group_id][$item_color_id][$item_size]["transfer_in_quantity"];
										$transfer_out_qty+=$issue_data_arr[$po_id][$item_group_id][$item_color_id][$item_size]["transfer_out_quantity"];
										$issue_amount+=$issue_qty*$issue_rate;
										
										$wo_qty+=$wo_qty_ArrayRecv[$po_id][$item_group_id][$item_color_id][$item_size];
										$wo_amount+=$wo_qty_ArrayRecvAmt[$po_id][$item_group_id][$item_color_id][$item_size];
										
										//if($item_size=="M")  {echo $item_color_id."<br>";}
										
										/*if($without_order==0)
										{
											$wo_qty+=$wo_qty_ArrayRecv[$po_id][$item_group_id][$item_color_id][$item_size];
											$wo_amount+=$wo_qty_ArrayRecvAmt[$po_id][$item_group_id][$item_color_id][$item_size];
											//$wo_qty_ArrayRecvAmt[$row[csf('po_id')]][$row[csf('item_group')]][$row[csf('item_color')]][$item_size]
										}
										else
										{
											$wo_qty+=$wo_qty_ArrayRecv[$item_group_id][$gmts_color][$item_color_id];
											$wo_amount+=$wo_qty_ArrayRecvAmt[$item_group_id][$gmts_color][$item_color_id];
										}*/
									}
								}
								$job_wo_qty=$job_wo_qty_ArrayRecv[$row[csf('job_no')]][$item_group_id][$item_color_id][$item_size];
								
								/*$net_recv_qnty=$recv_qnty+$issue_rtn_qty+$transfer_in_qty;
								$net_recv_value=$net_recv_qnty*$ord_avg_rate;
								$net_issue_qnty=$issue_qty+$rcv_rtn_qty+$transfer_out_qty;
								$net_issue_value=$net_issue_qnty*$ord_avg_rate;*/
								
								//$net_recv_qnty=(($recv_qnty-$rcv_rtn_qty)+$transfer_in_qty);
								$net_recv_qnty=($recv_qnty-$rcv_rtn_qty);
								$net_recv_value=$net_recv_qnty*$ord_avg_rate;
								$net_issue_qnty=(($issue_qty-$issue_rtn_qty)+$transfer_out_qty);
								$net_issue_value=$net_issue_qnty*$ord_avg_rate;
								
								
								
								
								if($y%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								?> 
								<tr bgcolor="<? echo $bgcolor; ?>"  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<?
									if($x==0)
									{
										
										?>
										<td width="40" rowspan="<? echo $z; ?>"><? echo $y;?> </td>
										<td width="70" rowspan="<? echo $z; ?>" style="word-break:break-all"><p><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></p></td>
										<td width="100" rowspan="<? echo $z; ?>"><p><? echo $row[csf('job_no')]; ?></p></td>
										<td width="110" rowspan="<? echo $z; ?>"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
										<td width="140" rowspan="<? echo $z; ?>" style="word-break:break-all"><p><? echo $all_po_num; ?></p></td>
										<td width="70" rowspan="<? echo $z; ?>" style="word-break:break-all"><p><? echo $all_file_no; ?></p></td>
										<td width="80" rowspan="<? echo $z; ?>" style="word-break:break-all"><p><? echo $all_gropping; ?></p></td>
										<td width="90" align="right" rowspan="<? echo $z; ?>"><? echo $row[csf('po_quantity')]; ?></td>
										<td width="80" align="right" rowspan="<? echo $z; ?>"><? echo number_format($row[csf('po_quantity')]/12,2); ?></td>
										<?	
									}
									
									if($s==0)
									{
									?>
										<td width="130" rowspan="<? echo $rowspan_array[$item_group_id]; ?>" title="<? echo $item_group_id."==".$without_order;?>"><p><? echo $trim_group[$item_group_id]; ?></p></td>
                                        <td width="150" rowspan="<? echo $rowspan_array[$item_group_id]; ?>"><p><? echo implode(",",array_filter(array_unique(explode(",",$descriptionArray[$item_group_id])))); ?></p></td>
										<td width="60" align="center" rowspan="<? echo $rowspan_array[$item_group_id]; ?>"><p><? echo $unit_of_measurement[$uomArray[$item_group_id]]; ?></p></td>
                                        <td width="90" align="right" rowspan="<? echo $rowspan_array[$item_group_id]; ?>">
										<?
										$req_qnty=0;
										foreach($job_po_id as $po_id)
										{
											if($req_check[$po_id][$item_group_id]=="")
											{
												$req_check[$po_id][$item_group_id]=$po_id."**".$item_group_id;
												//echo number_format($trims_costing_arr[$po_id][$item_group_id],2);
												$req_qnty+=$trims_costing_arr[$po_id][$item_group_id];
											}
										}
										echo number_format($req_qnty,2); $total_required_qnty+=$req_qnty;  $req_qnty=0;
                                        ?></td>
									<?	
									}
									if($c==0)
									{
									?>
										<td width="100" title="<? echo $item_color_id; ?>" rowspan="<? echo $rowspan_color_array[$item_group_id][$item_color_id]; ?>" style="word-break:break-all"><p><? echo $color_library[$item_color_id]; ?></p></td>
									<?	
									}
									?>
									<td width="60" title="<? echo $po_id."==".$item_group_id."==".$item_color_id."==".$item_size; ?>"><p><? if($item_size=="0") echo "&nbsp;"; else echo $item_size; ?>&nbsp;</p></td>
									<td width="80" align="right" title="Wo qty=<? echo $job_wo_qty; ?>"><a href='#report_details' onClick="openmypage('<? echo $all_po_id; ?>','<? echo $item_group_id; ?>','<? echo $item_color_id; ?>','<? echo $gmts_color; ?>','<? echo $item_size; ?>','<? echo $recv_basis; ?>','<? echo $without_order; ?>',1,'wo_receive_popup');"><?  echo number_format($wo_qty,2); ?></a></td>
                                    <td width="80" align="right"><?  echo number_format($wo_amount,2); ?></td>
								  
									<td width="90" align="right" title="<? echo "rcv:".$recv_qnty." rcv rtn:".$rcv_rtn_qty;//." trans in:".$transfer_in_qty ?>"><a href='#report_details' onClick="openmypage('<? echo $all_po_id; ?>','<? echo $item_group_id; ?>','<? echo $item_color_id; ?>','<? echo $gmts_color; ?>','<? echo $item_size; ?>','<? echo $recv_basis; ?>','<? echo $without_order; ?>',2,'receive_popup');"><?  echo number_format($net_recv_qnty,2); ?></a>  </td>
									<td width="80" align="right"><? echo number_format($wo_qty-$net_recv_qnty,2); ?></td>
									<td width="100" align="right"><? echo number_format($net_recv_value,2); ?></td>
									<td width="90" align="right" title="<? echo "issue:".$issue_qty." iss rtn:".$issue_rtn_qty." trans out:".$transfer_out_qty; ?>"> <a href='#report_details' onClick="openmypage('<? echo $all_po_id; ?>','<? echo $item_group_id; ?>','<? echo $item_color_id; ?>','<? echo $gmts_color; ?>','<? echo $item_size; ?>','<? echo $recv_basis; ?>','<? echo $without_order; ?>',3,'issue_popup');"><?  echo number_format($net_issue_qnty,2); ?></a></td>
                                    <td width="80" align="right"><?  echo number_format($net_issue_value,2); ?></td>
									<td width="100" align="right"><? $left_over=$net_recv_qnty-$net_issue_qnty;echo number_format($left_over,2); ?></td>
									<td width="60" align="right"><? echo number_format($ord_avg_rate,2); ?></td>
									<td width="60" align="right"><? echo $currency[$row[csf('currency_id')]]; ?></td>
									<td align="right">
									<? 
										//$tot_left_val=$left_over*$cons_rate;
										$tot_left_val=$left_over*$ord_avg_rate;
										echo number_format($tot_left_val,2); 
									?>
                                    </td>
								</tr>
								<? //echo $cons_rate.',';
							
								$total_wo_qty+=$wo_qty;
							 	$total_wo_amount+=$wo_amount;
							    $total_issue_amount+=$net_issue_value; 
								$total_wo_val+=$wo_qty-$net_recv_qnty; 
								$total_recv_qty+=$net_recv_qnty; 
								$total_recv_value+=$net_recv_value; 
								$total_issue_qty+=$net_issue_qnty;
								$total_left_val+=$tot_left_val;
								$total_left_over+=$left_over; 
							   
								$i++;
								$x++;
								$s++;
								$c++;
							}
						}
					}
					$total_order_qty+=$row[csf('po_quantity')];   
				}
				else
				{
					if($y%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?> 
					<tr bgcolor="<? echo $bgcolor; ?>"  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
						<td width="40"><? echo $y;?> </td>
						<td width="70"><p><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></p></td>
						<td width="100"><p><? echo $row[csf('job_no')]; ?></p></td>
						<td width="110"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
						<td width="140"><p><? echo $all_po_num;  ?></p></td>
						<td width="70"><p><?  echo $all_file_no;  ?></p></td>
						<td width="80"><p><?  echo $all_gropping;  ?></p></td>
						<td width="90" align="right"><? echo $row[csf('po_quantity')]; ?></td>
						<td width="80" align="right"><? echo number_format($row[csf('po_quantity')]/12,2); ?></td>
						<td width="130">&nbsp;</td>
                        <td width="150">&nbsp;</td>
						<td width="60" align="center">&nbsp;</td>
                        <td width="90" align="right">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="60">&nbsp;</td>
						<td width="80" align="right">&nbsp;</td>
                        <td width="80" align="right">&nbsp;</td>
						<td width="90" align="right">&nbsp;</td>
						<td width="80" align="right">&nbsp;</td>
						<td width="100" align="right">&nbsp;</td>
						<td width="90" align="right">&nbsp;</td>
                        <td width="80" align="right">&nbsp;</td>
						<td width="100" align="right">&nbsp;</td>
						<td width="60" align="right">&nbsp;</td>
						<td width="60" align="right">&nbsp;</td>
						<td align="right">&nbsp;</td>
					</tr>
				<?        
					$i++;	
				}
				$y++;
			}
			?>
			<tr class="tbl_bottom">
				<td colspan="7" align="right">Total</td>
				<td align="right"><? echo number_format($total_order_qty,2); ?></td>
				<td colspan="4" align="right"></td>
				<td align="right"><? echo number_format($total_required_qnty,2); ?></td>
                <td></td>
                <td></td>
				<td align="right"><? echo number_format($total_wo_qty,2); ?></td>
                <td align="right"><? echo number_format($total_wo_amount,2); ?></td>
				<td align="right"><? echo number_format($total_recv_qty,2); ?></td>
				<td align="right"><? echo number_format($total_wo_val,2); ?></td>
				<td align="right"><? echo number_format($total_recv_value,2); ?></td>
				<td align="right"><? echo number_format($total_issue_qty,2); ?></td>
                <td><? echo number_format($total_issue_amount,2); ?></td>
				<td><? echo number_format($total_left_over,2); ?></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td align="right"><? echo number_format($total_left_val,2); ?></td>
			</tr>        
		</table>
		</div>
	</fieldset>
	<?
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

if ($action=="report_generate_style_old")//Not used
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$cbo_company=str_replace("'","",$cbo_company_id);
	$cbo_buyer=str_replace("'","",$cbo_buyer_id);
	$all_style=str_replace("'","",$txt_style_id);
	$all_style=array_unique(explode(",",$all_style));
	$all_style_quted="";
	foreach($all_style as $style_no)
	{
		$all_style_quted.="'".$style_no."'".",";
	}
	$all_style_quted=chop($all_style_quted,",");
	
	//echo $all_style_quted;die;
	
	if($cbo_buyer==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$cbo_buyer";
	}
	
	if(str_replace("'","",trim($txt_order_no))=="")
	{
		$po_id_cond="";
	}
	else
	{
		if(str_replace("'","",$txt_order_no_id)!="")
		{
			$po_id_cond=" and b.id in(".str_replace("'","",$txt_order_no_id).")";
		}
		else
		{
			$po_number=trim(str_replace("'","",$txt_order_no))."%";
			$po_id_cond=" and b.po_number like '$po_number'";
		}
	}
	if(str_replace("'","",$txt_file_no)!="") $file_cond=" and b.file_no in(".str_replace("'","",$txt_file_no).")"; else $file_cond="";
	if(str_replace("'","",$txt_ref_no)!="") $ref_cond=" and b.grouping in(".$txt_ref_no.")"; else $ref_cond="";
	
	if(str_replace("'","",$txt_style)!="") $style=" and a.id in(".str_replace("'","",$txt_style).")"; else $style="";
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	if( $date_from!="" && $date_to!="") $pub_date= " and b.pub_shipment_date between '".$date_from."' and '".$date_to."'"; else $pub_date="";
	
	$condition= new condition();
	$condition->company_name("=$cbo_company_id");
	if(str_replace("'","",$cbo_buyer_id)>0)
	{
		$condition->buyer_name("=$cbo_buyer_id");
	}
	if(str_replace("'","",$txt_style_id) !='')
	{
		$condition->style_ref_no(" in($all_style_quted)");
	}
	if(str_replace("'","",$txt_order_no_id)!='')
	{
		$condition->po_id("in(".str_replace("'","",$txt_order_no_id).")");
	}
	if(str_replace("'","",$txt_file_no) !='')
	{
		$condition->file_no("=$txt_file_no");
	}
	if(str_replace("'","",$txt_ref_no) !='')
	{
		$condition->grouping("=$txt_ref_no");
	}
	if(str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!='')
	{
		$condition->pub_shipment_date(" between '$date_from' and '$date_to'");
	}
	$condition->init();
	
	$trims= new trims($condition);
	//echo $trims->getQuery();die;
	$trims_costing_arr=$trims->getQtyArray_by_orderAndItemid();
	
	//echo "<pre>";
	//print_r($trims_costing_arr);die;
	
	 
	ob_start();	
	?>
    <fieldset style="width:2260px;">
        <table width="2260">
            <tr class="form_caption">
                <td colspan="24" align="center" style="border:none;font-size:16px; font-weight:bold"> <? echo $report_title; ?></td>
            </tr>
            <tr class="form_caption">
                <td colspan="24" align="center"><? echo $company_library[$cbo_company]; ?></td>
            </tr>
        </table>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="2260" class="rpt_table" >
			<thead>
				<th width="40">SL </th>
                <th width="70">Buyer</th>
                <th width="100">Job No</th>
                <th width="110">Style</th>
                <th width="140">Order No</th>
                <th width="70">File No</th>
                <th width="80">Ref. No</th>
                <th width="90">Order Qty.</th>
                <th width="80">Order Qty.(Dzn)</th>
                <th width="130">Item Group</th>
                <th width="150">Item Description</th>
                <th width="60">UOM</th>
                <th width="90">Req. Qty</th>
                <th width="100">Item Color</th>
                <th width="60">Item Size</th>
                <th width="80">WO. Qty</th>
                <th width="80">WO. Value</th>
                <th width="90">Recv. Qty</th>
                <th width="80">Recv. Bal.</th>
                <th width="100">Recv. Value</th>
                <th width="90">Issue Qty.</th>
                <th width="80">Issue Value</th>
                <th width="100">Left Over</th>
                <th width="60">Rate</th>
                <th>Left Over Value</th>
            </thead>
        </table>
        <div style="width:2260px; overflow-y:scroll; max-height:350px;font-size:12px; " id="scroll_body">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="2240" class="rpt_table" id="tbl_issue_status" >
		   <?
			$i=1; $y=1; $tot_receive_qty=0; $tot_receive_value=0; $tot_issue_qty=0; $total_left_over=0; $total_left_over_balance=0; $dataArrayRecv=array();$wo_qty_ArrayRecv=array();$wo_qty_ArrayRecv_pi=array();$total_wo_qty=0;$total_wo_val=$total_wo_amount=$total_issue_amount=0;
			
			
			 
			$sql_bookingqty_non = sql_select("select sum(b.trim_qty) as wo_qnty,sum(b.amount) as amount,b.trim_group as item_group,b.fabric_color as item_color,b.gmts_color as color_number_id,b.fabric_description as description from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where a.company_id=$cbo_company and a.booking_no=b.booking_no  $buyer_id_cond group by  b.trim_group,b.fabric_color,b.gmts_color,b.fabric_description");
			foreach($sql_bookingqty_non as $row)
			{
				$wo_qty_ArrayRecv[$row[csf('item_group')]][$row[csf('item_color')]][$row[csf('fabric_color')]]=$row[csf('wo_qnty')];
				$wo_qty_ArrayRecvAmt[$row[csf('item_group')]][$row[csf('item_color')]][$row[csf('fabric_color')]]=$row[csf('amount')];
			}
			
			$sql_pi = sql_select("select b.quantity as wo_qnty,b.item_group,b.item_color,b.color_id as color_number_id,b.item_description as description from  com_pi_master_details a,com_pi_item_details b where a.id=b.pi_id and a.item_category_id=4 and a.importer_id=$cbo_company ");		
			foreach($sql_pi as $row)
			{
				$wo_qty_ArrayRecv_pi[$row[csf('item_group')]][$row[csf('item_color')]][$row[csf('fabric_color')]]+=$row[csf('wo_qnty')];
				
			}
			
			
			if($db_type==0)
			{
				$sql="select a.style_ref_no, a.buyer_name, a.job_no, group_concat(b.po_number) as po_number, group_concat(b.id) as po_id,group_concat(b.file_no) as file_no,group_concat(b.grouping) as grouping, sum(a.total_set_qnty*b.po_quantity) as po_quantity from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$cbo_company $buyer_id_cond $style $po_id_cond $file_cond $ref_cond $pub_date group by a.job_no, a.style_ref_no, a.buyer_name order by a.id";
			} 
			else
			{
				//rtrim(xmlagg(xmlelement(e,b.id,',').extract('//text()') order by b.id).GetClobVal(),',') as dtls_id
				/*$sql="select a.style_ref_no, a.buyer_name, a.job_no, LISTAGG(CAST(b.po_number AS VARCHAR2(4000)),',') WITHIN GROUP (ORDER BY b.id) as po_number, LISTAGG(CAST(b.id AS VARCHAR2(4000)),',') WITHIN GROUP (ORDER BY b.id) as po_id,LISTAGG(CAST(b.file_no AS VARCHAR2(4000)),',') WITHIN GROUP (ORDER BY b.id) as file_no,LISTAGG(CAST(b.grouping AS VARCHAR2(4000)),',') WITHIN GROUP (ORDER BY b.id) as grouping, sum(a.total_set_qnty*b.po_quantity) as po_quantity from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$cbo_company $buyer_id_cond $style $po_id_cond $pub_date $file_cond $ref_cond group by a.id, a.job_no, a.style_ref_no, a.buyer_name, a.total_set_qnty order by a.id";*/
				
				$sql="select a.style_ref_no, a.buyer_name, a.job_no, rtrim(xmlagg(xmlelement(e,b.po_number,',').extract('//text()') order by b.id).GetClobVal(),',') as po_number,
				rtrim(xmlagg(xmlelement(e,b.id,',').extract('//text()') order by b.id).GetClobVal(),',') as po_id, rtrim(xmlagg(xmlelement(e,b.file_no,',').extract('//text()') order by b.id).GetClobVal(),',') as file_no, rtrim(xmlagg(xmlelement(e,b.grouping,',').extract('//text()') order by b.id).GetClobVal(),',') as grouping, sum(a.total_set_qnty*b.po_quantity) as po_quantity 
				from wo_po_details_master a, wo_po_break_down b 
				where a.job_no=b.job_no_mst and a.company_name=$cbo_company $buyer_id_cond $style $po_id_cond $pub_date $file_cond $ref_cond
				group by a.id, a.job_no, a.style_ref_no, a.buyer_name, a.total_set_qnty order by a.id";
			}
			
			//echo $sql;die;
			$result=sql_select($sql);
			$all_order_id="";
			foreach($result as $row)
			{
				$all_order_id.=$row[csf("po_id")]->load().",";
			}
			$all_order_id=chop($all_order_id,",");
			$p=1;
			$sql_recv="select c.receive_basis, c.booking_without_order as without_order, a.item_group_id, a.item_description, c.store_id, a.order_uom, a.item_color, a.item_size, a.cons_rate, b.po_breakdown_id as po_id,a.gmts_color_id, b.quantity , b.order_amount as amount, b.trans_type
			from inv_receive_master c, inv_trims_entry_dtls a, order_wise_pro_details b 
			where a.id=b.dtls_id and a.trans_id=b.trans_id and c.id=a.mst_id and b.trans_type=1 and b.entry_form=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 "; //and a.prod_id=21968
			//echo $sql_recv;die;
			if($all_order_id!="")
			{
				$all_order_id_arr=array_chunk(array_unique(explode(",",$all_order_id)),999);
				foreach($all_order_id_arr as $order_id)
				{
					if($p==1) $sql_recv .=" and (b.po_breakdown_id in(".implode(',',$order_id).")"; else $sql_recv .=" or b.po_breakdown_id  in(".implode(',',$order_id).")";
					$p++;
				}
				$sql_recv .=" )";
			}
			$sql_recv.="union all select 0 as receive_basis, 0 as without_order, a.item_group_id, a.item_description, c.store_id, 0 as order_uom, a.item_color, a.item_size, c.cons_rate, b.po_breakdown_id as po_id, a.color as gmts_color_id, b.quantity, b.order_amount as amount, b.trans_type
			from product_details_master a , order_wise_pro_details b, inv_transaction c 
			where a.id=b.prod_id and b.trans_id=c.id and a.item_category_id=4 and a.entry_form=24 and b.entry_form in(78,112) and b.trans_type in(5) and c.transaction_type in(5) and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
			$p=1;
			if($all_order_id!="")
			{
				$all_order_id_arr=array_chunk(array_unique(explode(",",$all_order_id)),999);
				foreach($all_order_id_arr as $order_id)
				{
					if($p==1) $sql_recv .=" and (b.po_breakdown_id in(".implode(',',$order_id).")"; else $sql_recv .=" or b.po_breakdown_id  in(".implode(',',$order_id).")";
					$p++;
				}
				$sql_recv .=" )";
			}
			
			//echo $sql_recv;
			
			$sql_recv_result=sql_select($sql_recv);
			foreach($sql_recv_result as $row)
			{ 
				if($row[csf('item_color')]==0) $row[csf('item_color')]=404;
				if($row[csf('item_size')]=="") $item_size=0; else $item_size=$row[csf('item_size')];
				$dataArrayRecv[$row[csf('po_id')]].=$row[csf('item_group_id')]."_".$row[csf('order_uom')]."_".$row[csf('item_color')]."_".$item_size."_".$row[csf('cons_rate')]."_".$row[csf('quantity')]."_".$row[csf('receive_basis')]."_".$row[csf('without_order')]."_".$row[csf('gmts_color_id')]."_".$row[csf('item_description')]."_".$row[csf('store_id')]."_".$row[csf('amount')]."_".$row[csf('trans_type')]."___";
			}
			
			//echo "<pre>";print_r($dataArrayRecv);die;
			
			
			if($db_type==0) $null_val="c.color_number_id,c.item_color,c.item_size,c.gmts_sizes,";
			else if($db_type==2) $null_val="nvl(c.color_number_id,0) as color_number_id,nvl(c.item_color,0) as item_color,nvl(c.gmts_sizes,0) as gmts_sizes,nvl(c.item_size,0) as item_size,";
			$sql_bookingqty ="select c.cons as wo_qnty,c.amount,b.id as dtls_id,c.id,b.trim_group as item_group, $null_val c.description,c.brand_supplier,b.po_break_down_id as po_id,b.sensitivity from wo_booking_mst a, wo_booking_dtls b,wo_trim_book_con_dtls c where a.booking_no=b.booking_no and b.id=c.wo_trim_booking_dtls_id  and a.company_id=$cbo_company and a.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
			$p=1;
			if($all_order_id!="")
			{
				$all_order_id_arr=array_chunk(array_unique(explode(",",$all_order_id)),999);
				foreach($all_order_id_arr as $order_id)
				{
					if($p==1) $sql_bookingqty .=" and (b.po_break_down_id in(".implode(',',$order_id).")"; else $sql_bookingqty .=" or b.po_break_down_id  in(".implode(',',$order_id).")";
					$p++;
				}
				$sql_bookingqty .=" )";
			}
			
			//echo $sql_bookingqty;die;
			//echo $sql_bookingqty;die;
			$sql_bookingqty_result=sql_select($sql_bookingqty);
			//echo "<pre>";print_r($sql_bookingqty_result);die;
			foreach($sql_bookingqty_result as $row)
			{
				if($row[csf('item_size')]=="" || $row[csf('item_size')]=="0") $item_size=0; else $item_size=$row[csf('item_size')];
				$wo_qty_ArrayRecv[$row[csf('po_id')]][$row[csf('item_group')]][$row[csf('item_color')]][$item_size]+=$row[csf('wo_qnty')];
				$wo_qty_ArrayRecvAmt[$row[csf('po_id')]][$row[csf('item_group')]][$row[csf('item_color')]][$item_size]+=$row[csf('amount')];
			}
			//echo count($wo_qty_ArrayRecv);die;
			//echo "<pre>"; print_r($wo_qty_ArrayRecv);die;
			
			$issue_qty_sql="select b.po_breakdown_id, a.item_color as item_color_id, a.item_size, a.item_group_id, b.quantity, c.cons_rate as rate, b.entry_form, b.trans_type 
			from product_details_master a , order_wise_pro_details b, inv_transaction c 
			where a.id=b.prod_id and b.trans_id=c.id and item_category_id=4 and a.entry_form=24 and b.entry_form in(25,49,73,78,112) and b.trans_type in(2,3,4,6) and c.transaction_type in(2,3,4,6) and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
			$p=1;
			if($all_order_id!="")
			{
				$all_order_id_arr=array_chunk(array_unique(explode(",",$all_order_id)),999);
				foreach($all_order_id_arr as $order_id)
				{
					if($p==1) $issue_qty_sql .=" and (b.po_breakdown_id in(".implode(',',$order_id).")"; else $issue_qty_sql .=" or b.po_breakdown_id  in(".implode(',',$order_id).")";
					$p++;
				}
				$issue_qty_sql .=" )";
			}
			 //echo $issue_qty_sql;
			$issue_qty_sql_result=sql_select($issue_qty_sql);
			$issue_data_arr=array();
			foreach($issue_qty_sql_result as $row)
			{
				if($row[csf('item_color_id')]==0) $row[csf('item_color_id')]=404;
				if($row[csf('item_size')]=="") $item_size_id=0; else $item_size_id=$row[csf('item_size')];
				$issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][$row[csf('item_color_id')]][$item_size_id]["rate"]=$row[csf('rate')];
				if($row[csf('entry_form')]==25 && $row[csf('trans_type')]==2)
				{
					$issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][$row[csf('item_color_id')]][$item_size_id]["issue_quantity"]+=$row[csf('quantity')];
				}
				if($row[csf('entry_form')]==49 && $row[csf('trans_type')]==3)
				{
					$issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][$row[csf('item_color_id')]][$item_size_id]["rcv_rtn_quantity"]+=$row[csf('quantity')];
				}
				if($row[csf('entry_form')]==73 && $row[csf('trans_type')]==4)
				{
					$issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][$row[csf('item_color_id')]][$item_size_id]["issue_rtn_quantity"]+=$row[csf('quantity')];
				}
				/*if(($row[csf('entry_form')]==78 || $row[csf('entry_form')]==112) && $row[csf('trans_type')]==5)
				{
					$issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][$row[csf('item_color_id')]][$item_size_id]["transfer_in_quantity"]+=$row[csf('quantity')];
				}*/
				if(($row[csf('entry_form')]==78 || $row[csf('entry_form')]==112) && $row[csf('trans_type')]==6)
				{
					$issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][$row[csf('item_color_id')]][$item_size_id]["transfer_out_quantity"]+=$row[csf('quantity')];
				}
				
			}
			//echo "jahid<pre>";
			//print_r($issue_data_arr[17508][220]);die;
			//echo "<pre>"; print_r($wo_qty_ArrayRecv[13749][9][11]);die;
			//echo "<pre>"; print_r($issue_data_arr[13749][9][11]);die;
			
			$req_check=array();
			foreach ($result as $row)
			{
				$x=0; $z=0; $dataArray=array(); $uomArray=array(); $rowspan_array=array(); $rowspan_color_array=array();
				$all_po_id=$all_po_num=$all_file_no=$all_gropping="";
				if($db_type==0)
				{
					$all_po_id=implode(",",array_unique(explode(",",$row[csf('po_id')])));
					$all_po_num=implode(",",array_unique(explode(",",$row[csf('po_number')])));
					$all_file_no=implode(",",array_unique(explode(",",$row[csf('file_no')])));
					$all_gropping=implode(",",array_unique(explode(",",$row[csf('grouping')])));
				}
				else
				{
					$all_po_id=implode(",",array_unique(explode(",",$row[csf('po_id')]->load())));
					$all_po_num=implode(",",array_unique(explode(",",$row[csf('po_number')]->load())));
					$all_file_no=implode(",",array_unique(explode(",",$row[csf('file_no')]->load())));
					$all_gropping=implode(",",array_unique(explode(",",$row[csf('grouping')]->load())));
				}
				
				$job_po_id=explode(",",$all_po_id);
				foreach($job_po_id as $po_id)
				{
					//echo $dataArrayRecv[$po_id];die; chop($dataArrayRecv[$po_id],",")
					$dataRecv=explode("___",substr($dataArrayRecv[$po_id],0,-1));
					//print_r($dataRecv);die;
					foreach($dataRecv as $recvRow)
					{
						$recvRow=explode("_",$recvRow);
						$item_group_id=$recvRow[0];
						$order_uom=$recvRow[1];
						$item_color=$recvRow[2];
						$item_size=$recvRow[3];
						if($item_size=="") $item_size=0;
						$cons_rate=$recvRow[4];
						$quantity=$recvRow[5];
						$recv_basis=$recvRow[6];
						$without_order=$recvRow[7];
						$gmts_color=$recvRow[8];
						$item_description=$recvRow[9];
						$store_name_id=$recvRow[10];
						$recv_value=$recvRow[11];
						$trans_type=$recvRow[12];
						
						
						if($without_order=="") $without_order=0;
						
						//$recv_value=$cons_rate*$quantity;
						//$recv_data='".$recv_basis."'**'".$without_order."';
						
						
						if($quantity>0)
						{
							if($dataArray[$item_group_id][$item_color][$item_size]['qty']=="")
							{ 
								$rowspan_array[$item_group_id]+=1;
								$rowspan_color_array[$item_group_id][$item_color]+=1;
								
								$z++;
							}
							
							$dataArray[$item_group_id][$item_color][$item_size]['qty']+=$quantity;
							$dataArray[$item_group_id][$item_color][$item_size]['val']+=$recv_value;
							$uomArray[$item_group_id]=$order_uom;
							
							
							($descriptionArray[$item_group_id] =="")? $descriptionArray[$item_group_id]=$item_description:$descriptionArray[$item_group_id].=",".$item_description;
							($storeArray[$item_group_id] =="")? $storeArray[$item_group_id]=$store_name_arr[$store_name_id]:$storeArray[$item_group_id].=",".$store_name_arr[$store_name_id];
						}
					}
				}
				//echo $z;die; echo count($dataArray);
				if($z>0)
				{
					//echo "<pre>";print_r($dataArray);die;
					foreach($dataArray as $item_group_id=>$item_group_data)
					{ 
						$s=0;
						foreach($item_group_data as $item_color_id=>$item_color_data)
						{
							
							$c=0;
							
							foreach($item_color_data as $item_size=>$item_size_data)
							{
								
								$recv_qnty=$item_size_data['qty']; 
								$recv_value=$item_size_data['val'];
								$ord_avg_rate=$recv_value/$recv_qnty;
								$issue_qty=$issue_amount=$rcv_rtn_qty=$issue_rtn_qty=$transfer_in_qty=$transfer_out_qty=$net_recv_qnty=$net_recv_value=$net_issue_qnty=$net_issue_value=$wo_qty=$wo_amount=0;
								foreach($job_po_id as $po_id)
								{
									if($po_mrr_check[$po_id][$item_group_id][$item_color_id][$item_size]=="")
									{
										$po_mrr_check[$po_id][$item_group_id][$item_color_id][$item_size]=$po_id;
										$issue_rate=$issue_qty_rate_arr[$po_id][$item_group_id][$item_color_id][$item_size]["rate"];
										$issue_qty+=$issue_data_arr[$po_id][$item_group_id][$item_color_id][$item_size]["issue_quantity"];
										$rcv_rtn_qty+=$issue_data_arr[$po_id][$item_group_id][$item_color_id][$item_size]["rcv_rtn_quantity"];
										$issue_rtn_qty+=$issue_data_arr[$po_id][$item_group_id][$item_color_id][$item_size]["issue_rtn_quantity"];
										//$transfer_in_qty+=$issue_data_arr[$po_id][$item_group_id][$item_color_id][$item_size]["transfer_in_quantity"];
										$transfer_out_qty+=$issue_data_arr[$po_id][$item_group_id][$item_color_id][$item_size]["transfer_out_quantity"];
										$issue_amount+=$issue_qty*$issue_rate;
										
										$wo_qty+=$wo_qty_ArrayRecv[$po_id][$item_group_id][$item_color_id][$item_size];
										$wo_amount+=$wo_qty_ArrayRecvAmt[$po_id][$item_group_id][$item_color_id][$item_size];
										
										//if($item_size=="M")  {echo $item_color_id."<br>";}
										
										/*if($without_order==0)
										{
											$wo_qty+=$wo_qty_ArrayRecv[$po_id][$item_group_id][$item_color_id][$item_size];
											$wo_amount+=$wo_qty_ArrayRecvAmt[$po_id][$item_group_id][$item_color_id][$item_size];
											//$wo_qty_ArrayRecvAmt[$row[csf('po_id')]][$row[csf('item_group')]][$row[csf('item_color')]][$item_size]
										}
										else
										{
											$wo_qty+=$wo_qty_ArrayRecv[$item_group_id][$gmts_color][$item_color_id];
											$wo_amount+=$wo_qty_ArrayRecvAmt[$item_group_id][$gmts_color][$item_color_id];
										}*/
									}
								}
								
								/*$net_recv_qnty=$recv_qnty+$issue_rtn_qty+$transfer_in_qty;
								$net_recv_value=$net_recv_qnty*$ord_avg_rate;
								$net_issue_qnty=$issue_qty+$rcv_rtn_qty+$transfer_out_qty;
								$net_issue_value=$net_issue_qnty*$ord_avg_rate;*/
								
								//$net_recv_qnty=(($recv_qnty-$rcv_rtn_qty)+$transfer_in_qty);
								$net_recv_qnty=($recv_qnty-$rcv_rtn_qty);
								$net_recv_value=$net_recv_qnty*$ord_avg_rate;
								$net_issue_qnty=(($issue_qty-$issue_rtn_qty)+$transfer_out_qty);
								$net_issue_value=$net_issue_qnty*$ord_avg_rate;
								
								
								
								
								if($y%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							?> 
								<tr bgcolor="<? echo $bgcolor; ?>"  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<?
									if($x==0)
									{
										
										?>
										<td width="40" rowspan="<? echo $z; ?>"><? echo $y;?> </td>
										<td width="70" rowspan="<? echo $z; ?>"><p><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></p></td>
										<td width="100" rowspan="<? echo $z; ?>"><p><? echo $row[csf('job_no')]; ?></p></td>
										<td width="110" rowspan="<? echo $z; ?>"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
										<td width="140" rowspan="<? echo $z; ?>" style="word-break:break-all"><p><? echo $all_po_num; ?></p></td>
										<td width="70" rowspan="<? echo $z; ?>" style="word-break:break-all"><p><? echo $all_file_no; ?></p></td>
										<td width="80" rowspan="<? echo $z; ?>" style="word-break:break-all"><p><? echo $all_gropping; ?></p></td>
										<td width="90" align="right" rowspan="<? echo $z; ?>"><? echo $row[csf('po_quantity')]; ?></td>
										<td width="80" align="right" rowspan="<? echo $z; ?>"><? echo number_format($row[csf('po_quantity')]/12,2); ?></td>
										<?	
									}
									
									if($s==0)
									{
									?>
										<td width="130" rowspan="<? echo $rowspan_array[$item_group_id]; ?>" title="<? echo $item_group_id."==".$without_order;?>"><p><? echo $trim_group[$item_group_id]; ?></p></td>
                                        <td width="150" rowspan="<? echo $rowspan_array[$item_group_id]; ?>"><p><? echo implode(",",array_filter(array_unique(explode(",",$descriptionArray[$item_group_id])))); ?></p></td>
										<td width="60" align="center" rowspan="<? echo $rowspan_array[$item_group_id]; ?>"><p><? echo $unit_of_measurement[$uomArray[$item_group_id]]; ?></p></td>
                                        <td width="90" align="right" rowspan="<? echo $rowspan_array[$item_group_id]; ?>">
										<?
										$req_qnty=0;
										foreach($job_po_id as $po_id)
										{
											if($req_check[$po_id][$item_group_id]=="")
											{
												$req_check[$po_id][$item_group_id]=$po_id."**".$item_group_id;
												//echo number_format($trims_costing_arr[$po_id][$item_group_id],2);
												$req_qnty+=$trims_costing_arr[$po_id][$item_group_id];
											}
										}
										echo number_format($req_qnty,2); $total_required_qnty+=$req_qnty;  $req_qnty=0;
                                        ?></td>
									<?	
									}
									if($c==0)
									{
									?>
										<td width="100" title="<? echo $item_color_id; ?>" rowspan="<? echo $rowspan_color_array[$item_group_id][$item_color_id]; ?>" style="word-break:break-all"><p><? echo $color_library[$item_color_id]; ?></p></td>
									<?	
									}
									?>
									<td width="60" title="<? echo $po_id."==".$item_group_id."==".$item_color_id."==".$item_size; ?>"><p><? if($item_size=="0") echo "&nbsp;"; else echo $item_size; ?>&nbsp;</p></td>
									<td width="80" align="right"><a href='#report_details' onClick="openmypage('<? echo $all_po_id; ?>','<? echo $item_group_id; ?>','<? echo $item_color_id; ?>','<? echo $gmts_color; ?>','<? echo $item_size; ?>','<? echo $recv_basis; ?>','<? echo $without_order; ?>',1,'wo_receive_popup');"><?  echo number_format($wo_qty,2); ?></a></td>
                                    <td width="80" align="right"><?  echo number_format($wo_amount,2); ?></td>
								  
									<td width="90" align="right" title="<? echo "rcv:".$recv_qnty." rcv rtn:".$rcv_rtn_qty;//." trans in:".$transfer_in_qty ?>"><a href='#report_details' onClick="openmypage('<? echo $all_po_id; ?>','<? echo $item_group_id; ?>','<? echo $item_color_id; ?>','<? echo $gmts_color; ?>','<? echo $item_size; ?>','<? echo $recv_basis; ?>','<? echo $without_order; ?>',2,'receive_popup');"><?  echo number_format($net_recv_qnty,2); ?></a>  </td>
									<td width="80" align="right"><? echo number_format($wo_qty-$net_recv_qnty,2); ?></td>
									<td width="100" align="right"><? echo number_format($net_recv_value,2); ?></td>
									<td width="90" align="right" title="<? echo "issue:".$issue_qty." iss rtn:".$issue_rtn_qty." trans out:".$transfer_out_qty; ?>"> <a href='#report_details' onClick="openmypage('<? echo $all_po_id; ?>','<? echo $item_group_id; ?>','<? echo $item_color_id; ?>','<? echo $gmts_color; ?>','<? echo $item_size; ?>','<? echo $recv_basis; ?>','<? echo $without_order; ?>',3,'issue_popup');"><?  echo number_format($net_issue_qnty,2); ?></a></td>
                                    <td width="80" align="right"><?  echo number_format($net_issue_value,2); ?></td>
									<td width="100" align="right"><? $left_over=$net_recv_qnty-$net_issue_qnty;echo number_format($left_over,2); ?></td>
									<td width="60" align="right"><? echo number_format($ord_avg_rate,2); ?></td>
									<td align="right">
									<? 
										//$tot_left_val=$left_over*$cons_rate;
										$tot_left_val=$left_over*$ord_avg_rate;
										echo number_format($tot_left_val,2); 
									?>
                                    </td>
								</tr>
								<? //echo $cons_rate.',';
							
								$total_wo_qty+=$wo_qty;
							 	$total_wo_amount+=$wo_amount;
							    $total_issue_amount+=$net_issue_value; 
								$total_wo_val+=$wo_qty-$net_recv_qnty; 
								$total_recv_qty+=$net_recv_qnty; 
								$total_recv_value+=$net_recv_value; 
								$total_issue_qty+=$net_issue_qnty;
								$total_left_val+=$tot_left_val;
								$total_left_over+=$left_over; 
							   
								$i++;
								$x++;
								$s++;
								$c++;
							}
						}
					}
					$total_order_qty+=$row[csf('po_quantity')];   
				}
				else
				{
					if($y%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?> 
					<tr bgcolor="<? echo $bgcolor; ?>"  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
						<td width="40"><? echo $y;?> </td>
						<td width="70"><p><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></p></td>
						<td width="100"><p><? echo $row[csf('job_no')]; ?></p></td>
						<td width="110"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
						<td width="140"><p><? echo $all_po_num;  ?></p></td>
						<td width="70"><p><?  echo $all_file_no;  ?></p></td>
						<td width="80"><p><?  echo $all_gropping;  ?></p></td>
						<td width="90" align="right"><? echo $row[csf('po_quantity')]; ?></td>
						<td width="80" align="right"><? echo number_format($row[csf('po_quantity')]/12,2); ?></td>
						<td width="130">&nbsp;</td>
                        <td width="150">&nbsp;</td>
						<td width="60" align="center">&nbsp;</td>
                        <td width="90" align="right">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="60">&nbsp;</td>
						<td width="80" align="right">&nbsp;</td>
                        <td width="80" align="right">&nbsp;</td>
						<td width="90" align="right">&nbsp;</td>
						<td width="80" align="right">&nbsp;</td>
						<td width="100" align="right">&nbsp;</td>
						<td width="90" align="right">&nbsp;</td>
                        <td width="80" align="right">&nbsp;</td>
						<td width="100" align="right">&nbsp;</td>
						<td width="60" align="right">&nbsp;</td>
						<td align="right">&nbsp;</td>
					</tr>
				<?        
					$i++;	
				}
				$y++;
			}
			?>
			<tr class="tbl_bottom">
				<td colspan="7" align="right">Total</td>
				<td align="right"><? echo number_format($total_order_qty,2); ?></td>
				<td colspan="4" align="right"></td>
				<td align="right"><? echo number_format($total_required_qnty,2); ?></td>
                <td></td>
                <td></td>
				<td align="right"><? echo number_format($total_wo_qty,2); ?></td>
                <td align="right"><? echo number_format($total_wo_amount,2); ?></td>
				<td align="right"><? echo number_format($total_recv_qty,2); ?></td>
				<td align="right"><? echo number_format($total_wo_val,2); ?></td>
				<td align="right"><? echo number_format($total_recv_value,2); ?></td>
				<td align="right"><? echo number_format($total_issue_qty,2); ?></td>
                <td><? echo number_format($total_issue_amount,2); ?></td>
				<td><? echo number_format($total_left_over,2); ?></td>
				<td>&nbsp;</td>
				<td align="right"><? echo number_format($total_left_val,2); ?></td>
			</tr>        
		</table>
		</div>
	</fieldset>
	<?
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



if ($action=="report_generate_style_color_size")// Item Color Size
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$cbo_company=str_replace("'","",$cbo_company_id);
	$cbo_buyer=str_replace("'","",$cbo_buyer_id);
	$all_style=str_replace("'","",$txt_style_id);
	$all_style=array_unique(explode(",",$all_style));
	$all_style_quted="";
	foreach($all_style as $style_no)
	{
		$all_style_quted.="'".$style_no."'".",";
	}
	$all_style_quted=chop($all_style_quted,",");
	
	if($cbo_buyer==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$cbo_buyer";
	}
	
	if(str_replace("'","",trim($txt_order_no))=="")
	{
		$po_id_cond="";
	}
	else
	{
		if(str_replace("'","",$txt_order_no_id)!="")
		{
			$po_id_cond=" and b.id in(".str_replace("'","",$txt_order_no_id).")";
		}
		else
		{
			$po_number=trim(str_replace("'","",$txt_order_no))."%";
			$po_id_cond=" and b.po_number like '$po_number'";
		}
	}
	
	if(str_replace("'","",$txt_style)!="") $style=" and a.id in(".str_replace("'","",$txt_style).")"; else $style="";
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	if( $date_from!="" && $date_to!="") $pub_date= " and b.pub_shipment_date between '".$date_from."' and '".$date_to."'"; else $pub_date=""; 
	ob_start();	
	
	
	/*$po_color_wise_arr=array();
	$sql_po=("select  b.id,c.item_number_id,c.color_number_id,c.size_number_id, sum(c.order_quantity/a.total_set_qnty) as order_quantity_set  from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst  and b.id=c.po_break_down_id   and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1  and a.company_name=$cbo_company $buyer_id_cond $pub_date $style  $po_id_cond group by b.id,c.color_number_id,c.size_number_id ,c.item_number_id order by b.id");
	$sql_po_qty_wise=sql_select($sql_po);
	foreach($sql_po_qty_wise as $po_row)
	{
	$po_color_wise_arr[$po_row[csf('id')]][$po_row[csf('item_number_id')]][$po_row[csf('color_number_id')]][$po_row[csf('size_number_id')]]=$po_row[csf('order_quantity_set')];
	//$po_job_arr[$sql_po_qty_country_wise_row[csf('id')]]=$sql_po_qty_country_wise_row[csf('job_no_mst')];
	}*/
	//var_dump($po_color_wise_arr);die;
	
		/*$i=1; $y=1; $tot_receive_qty=0; $tot_receive_value=0; $tot_issue_qty=0; $total_left_over=0; $total_left_over_balance=0; $dataArrayRecv=array();

		$sql_recv=sql_select("select b.trim_group as item_group_id, b.uom as order_uom, b.fabric_color_id as item_color ,b.rate as cons_rate, b.item_size,b.gmts_size,  b.po_break_down_id as po_id, b.wo_qnty as quantity from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.booking_type=2 and  a.status_active=1 and a.is_deleted=0 and a.item_category=4 and b.job_no=a.job_no and b.status_active=1 and b.is_deleted=0"); 
		foreach($sql_recv as $row)
		{
			$dataArrayRecv[$row[csf('po_id')]].=$row[csf('item_group_id')]."_".$row[csf('order_uom')]."_".$row[csf('item_color')]."_".$row[csf('item_size')]."_".$row[csf('cons_rate')]."_".$row[csf('quantity')].",";
		}
		//var_dump($dataArrayRecv);
	
	if($db_type==0)
		{
			 $sql="select a.style_ref_no, a.buyer_name, a.job_no, group_concat(distinct b.po_number) as po_number, group_concat(distinct b.id) as po_id, sum(a.total_set_qnty*c.order_quantity) as po_quantity,group_concat(distinct c.color_number_id) as color_number_id,c.item_number_id from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.job_no_mst=c.job_no_mst and c.po_break_down_id=b.id and a.company_name=$cbo_company $buyer_id_cond $style $po_id_cond $pub_date group by a.job_no, a.style_ref_no, a.buyer_name,c.item_number_id order by a.id";
		} 
		else
		{
			$sql="select a.style_ref_no, a.buyer_name, a.job_no, LISTAGG(CAST(b.po_number AS VARCHAR2(4000)),',') WITHIN GROUP (ORDER BY b.id) as po_number, LISTAGG(CAST(b.id AS VARCHAR2(4000)),',') WITHIN GROUP (ORDER BY b.id) as po_id,LISTAGG(CAST(c.color_number_id AS VARCHAR2(4000)),',') WITHIN GROUP (ORDER BY c.color_number_id) as color_number_id, sum(a.total_set_qnty*c.order_quantity) as po_quantity,c.item_number_id from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c  where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.job_no_mst=c.job_no_mst and c.po_break_down_id=b.id and a.company_name=$cbo_company $buyer_id_cond $style $po_id_cond $pub_date group by a.id, a.job_no, a.style_ref_no, a.buyer_name,c.item_number_id order by a.id";
		}
		$result=sql_select($sql);*/
	?>
    <div>
	<fieldset style="width:1790px;">
        <table width="1700">
            <tr class="form_caption">
                <td colspan="16" align="center" style="border:none;font-size:16px; font-weight:bold"> <? echo $report_title; ?></td>
            </tr>
            <tr class="form_caption">
                <td colspan="16" align="center"><? echo $company_library[$cbo_company]; ?></td>
            </tr>
        </table>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1790" class="rpt_table" >
			<thead>
				<th width="40">SL </th>
                <th width="100">Buyer Name</th>
                <th width="100">Job No</th>
                <th width="110">Style</th>
                <th width="140">Order No</th>
                <th width="90">Order Qty.</th>
                <th width="110">GMTS Item Name</th>
                <th width="130">Item Name</th>
                <th width="60">UOM</th>
                <th width="100">Color</th>
                <th width="100">Item Color</th>
                <th width="60">GMTS Size</th>
                <th width="60">Item Size</th>
                <th width="90">Req. Qnty</th>
                <th width="90">WO. Qty</th>
                <th width="90">Recv. Qty</th>
                <th width="100">Balance Recv. Qty</th>
                <th width="90">Issue Qty.</th>
                <th>Left Over Qty</th>
               
            </thead>
        </table>
        <div style="width:1790px; overflow-y:scroll; max-height:350px;font-size:12px; " id="scroll_body">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1770" class="rpt_table" id="tbl_issue_status" >
		   <?
				$i=1; $y=1; $tot_receive_qty=0; $tot_receive_value=0; $tot_issue_qty=0; $total_left_over=0; $total_left_over_balance=0; $dataArrayRecv=array();
					
				$poDataArray=sql_select("select b.id,b.pub_shipment_date, b.po_number,a.buyer_name,a.job_no,a.job_no_prefix_num,a.style_ref_no as style from  wo_po_break_down b,wo_po_details_master a where  a.job_no=b.job_no_mst and a.company_name=$cbo_company and b.status_active=1 and b.is_deleted=0  $buyer_id_cond $style $po_id_cond $pub_date");// and
				$all_po_id='';
				$job_array=array(); $all_job_id='';
				//$buyer_array=array();$all_buyer_id='';
				foreach($poDataArray as $row)
				{
				//$po_array[$row[csf('id')]]=$row[csf('po_number')];
				$job_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
				$job_array[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
				$job_array[$row[csf('id')]]['po']=$row[csf('po_number')];
				$job_array[$row[csf('job_no')]]['po_id'].=$row[csf('id')].',';
				$job_array[$row[csf('job_no')]]['po_no'].=$row[csf('po_number')].',';
				if($all_po_id=="") $all_po_id=$row[csf('id')]; else $all_po_id.=",".$row[csf('id')]; //echo $all_po_id;
				}  //$e=array_unique(explode(",",$all_po_id));
				$condition= new condition();
				$condition->company_name("=$cbo_company_id");
				if(str_replace("'","",$cbo_buyer_id)>0)
				{
					$condition->buyer_name("=$cbo_buyer_id");
				}
				if(str_replace("'","",$txt_style_id) !='')
				{
					$condition->style_ref_no(" in($all_style_quted)");
				}
				if(str_replace("'","",$txt_order_no_id)!='')
				{
					$condition->po_id("in(".str_replace("'","",$txt_order_no_id).")");
				}
				if(str_replace("'","",$txt_file_no) !='')
				{
					$condition->file_no("=$txt_file_no");
				}
				if(str_replace("'","",$txt_ref_no) !='')
				{
					$condition->grouping("=$txt_ref_no");
				}
				if(str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!='')
				{
					$condition->pub_shipment_date(" between '$date_from' and '$date_to'");
				}
				$condition->init();
				
				$trims= new trims($condition);
				//echo $trims->getQuery();die;
				$trims_item_color_costing_arr=$trims->getQtyArray_by_orderItemidGmtscolorAndrGmtssize();
				//print_r($trims_item_color_costing_arr);
			
				//echo count($e);die;
				/*$sql_data=sql_select("select b.trim_group as item_group_id, b.uom as order_uom, c.color_number_id as gmts_color,d.item_color as item_color  ,b.rate as cons_rate, d.item_size as item_size,c.size_number_id as gmts_size,  c.po_break_down_id as po_id, d.requirment as quantity from wo_booking_dtls b,wo_po_color_size_breakdown c,wo_trim_book_con_dtls d where   b.job_no=c.job_no_mst and b.id=d.wo_trim_booking_dtls_id and b.booking_no=d.booking_no and d.job_no=b.job_no and b.po_break_down_id=c.po_break_down_id and  d.po_break_down_id=c.po_break_down_id and c.job_no_mst=d.job_no and b.booking_type=2 and  c.status_active=1 and c.is_deleted=0   and d.color_size_table_id=c.id   and b.status_active=1 and b.is_deleted=0"); 
				foreach($sql_data as $row)
				{
					$dataArrayRecv[$row[csf('po_id')]].=$row[csf('item_group_id')]."_".$row[csf('order_uom')]."_".$row[csf('gmts_color')]."_".$row[csf('item_size')]."_".$row[csf('cons_rate')]."_".$row[csf('quantity')]."_".$row[csf('gmts_size')]."_".$row[csf('item_color')].",";
				}*/
				//if($all_po_id!=0) $po_concat=" and b.po_break_down_id in($all_po_id) ";else $po_concat="";
				$po_ids=count(array_unique(explode(",",$all_po_id)));
				$po_numIds=chop($all_po_id,','); $poIds_cond="";
				if($all_po_id!='' || $all_po_id!=0)
				{
					if($db_type==2 && $po_ids>1000)
					{
						$poIds_cond=" and (";
						$poIdsArr=array_chunk(explode(",",$po_numIds),990);
						foreach($poIdsArr as $ids)
						{
							$ids=implode(",",$ids);
							$poIds_cond.=" c.po_break_down_id  in($ids) or ";
						}
						$poIds_cond=chop($poIds_cond,'or ');
						$poIds_cond.=")";
					}
					else
					{
						$poIds_cond=" and  c.po_break_down_id  in($all_po_id)";
					}
				}	
				
				$book_wo_qty_arr=array();
				$sql_book=sql_select("select b.trim_group as item_group_id, b.uom as order_uom,b.sensitivity, c.color_number_id as gmts_color,d.color_number_id as color_number_id, d.item_color as item_color  ,b.rate as cons_rate, d.item_size as item_size,c.size_number_id as gmts_size,  c.po_break_down_id as po_id, d.requirment as quantity from wo_booking_dtls b,wo_po_color_size_breakdown c,wo_trim_book_con_dtls d where   b.job_no=c.job_no_mst and b.id=d.wo_trim_booking_dtls_id and b.booking_no=d.booking_no and d.job_no=b.job_no and b.po_break_down_id=c.po_break_down_id  and  d.po_break_down_id=c.po_break_down_id and c.job_no_mst=d.job_no and b.booking_type=2  and c.status_active=1 and c.is_deleted=0   and d.color_size_table_id=c.id and d.requirment!=0   and b.status_active=1 and b.is_deleted=0 $poIds_cond order by b.trim_group ,c.color_number_id,c.size_number_id");
				foreach($sql_book as $row)
				{
					$size_data=$row[csf('item_size')];
					if($size_data=="" || $size_data==0) $item_size_id=""; else $item_size_id=$size_data; 
					if($row[csf('sensitivity')]==1 || $row[csf('sensitivity')]==3) 
					{
						$book_wo_qty_arr[$row[csf('po_id')]][$row[csf('item_group_id')]][$row[csf('gmts_color')]][0]['quantity']+=$row[csf('quantity')];
					}
					else if($row[csf('sensitivity')]==2)
					{
						$book_wo_qty_arr[$row[csf('po_id')]][$row[csf('item_group_id')]][$row[csf('gmts_size')]][0]['quantity']+=$row[csf('quantity')];
					}
					else
					{
						$book_wo_qty_arr[$row[csf('po_id')]][$row[csf('item_group_id')]][$row[csf('gmts_color')]][$row[csf('gmts_size')]]['quantity']+=$row[csf('quantity')];	
					}
					
					$book_wo_qty_arr[$row[csf('po_id')]][$row[csf('item_group_id')]][$row[csf('gmts_color')]]['color']=$row[csf('item_color')];
					$book_wo_qty_arr[$row[csf('po_id')]][$row[csf('item_group_id')]][$row[csf('gmts_color')]][$row[csf('gmts_size')]]['item_size']=$row[csf('item_size')];
					$book_wo_qty_arr[$row[csf('po_id')]][$row[csf('item_group_id')]][$row[csf('gmts_color')]][$row[csf('gmts_size')]]['gmts_size']=$row[csf('gmts_size')];
					$book_wo_qty_arr[$row[csf('po_id')]][$row[csf('item_group_id')]][$row[csf('gmts_color')]]['sensitivity']=$row[csf('sensitivity')];				
					$book_wo_qty_arr[$row[csf('po_id')]][$row[csf('item_group_id')]][$row[csf('gmts_color')]]['color_number']=$row[csf('color_number_id')];
				} //var_dump($book_wo_qty_arr);die;
				
				
				//and c.id=b.color_size_table_id and b.item_number_id=c.item_number_id
				  $sql_data=sql_select("select a.trim_group as item_group_id, a.cons_uom as order_uom, c.color_number_id as gmts_color,a.rate as cons_rate, b.item_size as item_size,c.size_number_id as gmts_size,  c.po_break_down_id as po_id, b.cons as quantity 
				from wo_pre_cost_trim_cost_dtls a, wo_pre_cost_trim_co_cons_dtls b, wo_po_color_size_breakdown c 
				where a.job_no=b.job_no and a.job_no=c.job_no_mst and a.id=b.wo_pre_cost_trim_cost_dtls_id   and b.po_break_down_id=c.po_break_down_id and   c.job_no_mst=b.job_no and  c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $poIds_cond");
				$po_ids=count(array_unique(explode(",",$all_po_id)));
				$po_numIds=chop($all_po_id,','); $poIds_cond2="";
				if($all_po_id!='' || $all_po_id!=0)
				{
					if($db_type==2 && $po_ids>1000)
					{
						$poIds_cond2=" and (";
						$poIdsArr=array_chunk(explode(",",$po_numIds),990);
						foreach($poIdsArr as $ids)
						{
							$ids=implode(",",$ids);
							$poIds_cond2.=" b.po_break_down_id  in($ids) or ";
						}
						$poIds_cond2=chop($poIds_cond2,'or ');
						$poIds_cond2.=")";
					}
					else
					{
						$poIds_cond2=" and  b.po_break_down_id  in($all_po_id)";
					}
				}
			/*$sql_data=sql_select("select a.trim_group as item_group_id, a.cons_uom as order_uom, b.color_number_id as gmts_color,a.rate as cons_rate, b.item_size as item_size,b.size_number_id as gmts_size,  b.po_break_down_id as po_id, b.cons as quantity 
				from wo_pre_cost_trim_cost_dtls a, wo_pre_cost_trim_co_cons_dtls b
				where a.job_no=b.job_no and a.id=b.wo_pre_cost_trim_cost_dtls_id and a.status_active=1 and a.is_deleted=0 $poIds_cond2"); */
				
				$budge_data_arr=array();
				foreach($sql_data as $row)
				{
					$dataArrayRecv[$row[csf('po_id')]].=$row[csf('item_group_id')]."_".$row[csf('order_uom')]."_".$row[csf('gmts_color')]."_".$row[csf('item_size')]."_".$row[csf('cons_rate')]."_".$row[csf('quantity')]."_".$row[csf('gmts_size')].",";
					//$budge_data_arr[$row[csf('po_id')]][$row[csf('item_group_id')]]["costing_per"]=$row[csf('costing_per')];
					//$budge_data_arr[$row[csf('po_id')]][$row[csf('item_group_id')]]["quantity"]=$row[csf('quantity')];
					
				} 
				//var_dump($dataArrayRecv);
				
				$po_ids=count(array_unique(explode(",",$all_po_id)));
				$po_numIds=chop($all_po_id,','); $poIds_conds="";
				if($all_po_id!='' || $all_po_id!=0)
				{
					if($db_type==2 && $po_ids>1000)
					{
						$poIds_conds=" and (";
						$poIdsArr=array_chunk(explode(",",$po_numIds),990);
						foreach($poIdsArr as $ids)
						{
							$ids=implode(",",$ids);
							$poIds_conds.=" b.po_breakdown_id  in($ids) or ";
						}
						$poIds_conds=chop($poIds_conds,'or ');
						$poIds_conds.=")";
					}
					else
					{
						$poIds_conds=" and  b.po_breakdown_id  in($all_po_id)";
					}
				} 
				$issue_qty_arr=array();$issue_basis_arr=array();$gmts_color_data_arr=array();
				$issue_qty_data=sql_select("select b.po_breakdown_id, a.item_color_id,c.issue_basis, a.item_size, a.item_group_id,a.gmts_color_id as gmts_color_id,a.gmts_size_id, b.quantity as issue_qty from inv_trims_issue_dtls a , order_wise_pro_details b,inv_issue_master c where a.id=b.dtls_id and c.id=a.mst_id  and a.trans_id=b.trans_id and b.trans_type=2 and b.entry_form=25 and c.entry_form=25 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $poIds_conds order by a.item_group_id,a.gmts_color_id,b.po_breakdown_id");
				foreach($issue_qty_data as $row)
				{
					if($row[csf('item_size')]=="") $item_size_id=0; else $item_size_id=$row[csf('item_size')]; 
					if($row[csf('gmts_size_id')]=="") $gmt_size_id=0; else $gmt_size_id=$row[csf('gmts_size_id')]; 
					$issue_basis=$row[csf('issue_basis')]; 
					$issue_basis_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]]=$issue_basis;
					$gmts_color_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]]=$row[csf('gmts_color_id')];
					if($issue_basis==1)
					{
						
						if($row[csf('gmts_color_id')]==0)
						{
						$issue_qty_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][$gmt_size_id]+=$row[csf('issue_qty')];
						//$issue_basis_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][$row[csf('gmts_color_id')]]=$issue_basis;
						}
						else
						{
							$issue_qty_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][$row[csf('gmts_color_id')]][$gmt_size_id]+=$row[csf('issue_qty')];	
						}
					}
					else
					{
						$issue_qty_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][$row[csf('gmts_color_id')]][$item_size_id]+=$row[csf('issue_qty')];
						//$issue_basis_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][$row[csf('item_color_id')]]=$issue_basis;
					}
					
				}
				
				// $size_data=return_field_value("id", "lib_size", "size_name='".$row[csf('item_size')]."'  and status_active=1 and is_deleted=0");
				$size_data_arr=array();
				$sql_size=sql_select("select  id,size_name from lib_size  where  status_active=1 and is_deleted=0");
				foreach( $sql_size as $row_size)
				{
				$size_data_arr[$row_size[csf('id')]]=$row_size[csf('size_name')];	
				} 
				//var_dump($size_data_arr);
				$recv_qty_arr=array();$recv_basis_arr=array();$size_sensitivity_arr=array();
				
				$sql_result=sql_select("select a.item_group_id, a.order_uom,a.sensitivity, a.item_color,a.gmts_color_id, a.item_size,a.gmts_size_id, a.cons_rate, b.po_breakdown_id as po_id, b.quantity,c.receive_basis from inv_trims_entry_dtls a, order_wise_pro_details b,inv_receive_master c where  c.id=a.mst_id and a.id=b.dtls_id and a.trans_id=b.trans_id and b.trans_type=1 and b.entry_form=24  and c.entry_form=24  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $poIds_conds order by a.item_group_id,a.gmts_color_id,a.gmts_size_id"); 
				foreach($sql_result as $row)
				{
					if($row[csf('gmts_size_id')]=="" || $row[csf('gmts_size_id')]==0) $gmt_size_id=0; else $gmt_size_id=$row[csf('gmts_size_id')]; 
					if($row[csf('item_size')]=="") $item_size_id=0; else $item_size_id=$row[csf('item_size')];  
					$size_sensitivity=$row[csf('sensitivity')];
					$receive_basis=$row[csf('receive_basis')];
					$size_sensitivity_arr[$row[csf('po_id')]][$row[csf('item_group_id')]]=$size_sensitivity;
					if($size_sensitivity==1 || $size_sensitivity==3)
					{
						//if($size_sensitivity==1) echo $row[csf('quantity')].'ddfd';
						$recv_qty_arr[$row[csf('po_id')]][$row[csf('item_group_id')]][$row[csf('gmts_color_id')]][0]+=$row[csf('quantity')];
						$recv_basis_arr[$row[csf('po_id')]][$row[csf('item_group_id')]][$row[csf('gmts_color_id')]]=$row[csf('receive_basis')];
					}
					else if($size_sensitivity==2)
					{
						$recv_qty_arr[$row[csf('po_id')]][$row[csf('item_group_id')]][$row[csf('gmts_size_id')]][0]+=$row[csf('quantity')];	
					}
					else
					{
						//echo $gmt_size_id;
						//echo $row[csf('gmts_color_id')].'<br>';
						$recv_qty_arr[$row[csf('po_id')]][$row[csf('item_group_id')]][$row[csf('gmts_color_id')]][$gmt_size_id]+=$row[csf('quantity')];
						$recv_basis_arr[$row[csf('po_id')]][$row[csf('item_group_id')]][$row[csf('gmts_color_id')]]=$row[csf('receive_basis')];	
					}
					
				} //var_dump($recv_qty_arr);die;
				
				if($db_type==0)
				{
					 $sql="select a.style_ref_no, a.buyer_name, a.job_no, group_concat(distinct b.po_number) as po_number, group_concat(distinct b.id) as po_id, sum(a.total_set_qnty*c.order_quantity) as po_quantity,group_concat(distinct c.color_number_id) as color_number_id, sum(c.order_quantity/a.total_set_qnty) as order_quantity_set from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.job_no_mst=c.job_no_mst and c.po_break_down_id=b.id and a.company_name=$cbo_company $buyer_id_cond $style $po_id_cond $pub_date $poIds_cond group by a.job_no, a.style_ref_no, a.buyer_name,c.item_number_id order by a.id";
				} 
				else
				{
					//LISTAGG(CAST(b.po_number AS VARCHAR2(4000)),',') WITHIN GROUP (ORDER BY b.id) as po_number, 
					//LISTAGG(CAST(b.id AS VARCHAR2(4000)),',') WITHIN GROUP (ORDER BY b.id) as po_id
					
					$sql="select a.style_ref_no, a.buyer_name, a.job_no,a.gmts_item_id, sum(c.order_quantity*a.total_set_qnty) as po_quantity, sum(c.order_quantity/a.total_set_qnty) as order_quantity_set from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c  where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.job_no_mst=c.job_no_mst and c.po_break_down_id=b.id  and a.company_name=$cbo_company $buyer_id_cond $style $po_id_cond $pub_date $poIds_cond group by a.id, a.job_no, a.style_ref_no, a.buyer_name,a.gmts_item_id order by a.id";
				} 
				//echo $sql;
				$result=sql_select($sql);
				foreach ($result as $row)
				{
					
					//echo $row[csf('job_no')].'aa';
					$x=0; $z=0; $dataArray=array(); $uomArray=array(); $rowspan_array=array(); $rowspan_color_array=array();$item_colorArray=array();$gmts_sizeArray=array();$item_sizeArray=array();
					$job_poid=rtrim($job_array[$row[csf('job_no')]]['po_id'],',');
					$job_po_no=rtrim($job_array[$row[csf('job_no')]]['po_no'],',');
					//print_r($job_po_id);die;
					$job_po_id=explode(",",$job_poid);
					$gmts_item=''; $gmts_item_id=explode(",",$row[csf('gmts_item_id')]);
					foreach($gmts_item_id as $item_id)
					{
						if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=", ".$garments_item[$item_id];
					}
					$po_number_data='';
					$po_number=array_unique(explode(",",$job_po_no));
					foreach($po_number as $po_row)
					{	
						
						if($po_number_data=="") $po_number_data=$po_row; else $po_number_data.=",".$po_row;
					}
					
					foreach($job_po_id as $po_id)
					{
						$dataRecv=explode(",",substr($dataArrayRecv[$po_id],0,-1));
						
						foreach($dataRecv as $recvRow)
						{
							$recvRow=explode("_",$recvRow);
							$item_group_id=$recvRow[0];
							 $order_uom=$recvRow[1];
							$item_color=$recvRow[2];
							//$item_size=$recvRow[3];
							//if($item_size=="") $item_size=0;
							$cons_rate=$recvRow[4];
							$quantity=$recvRow[5];
							$gmts_size=$recvRow[6];
							$book_item_color=$recvRow[7];
							 //$size=$gmts_size;
							
							if($quantity>0)
							{
								if($dataArray[$item_group_id][$item_color][$gmts_size]['qty']=="")
								{ 
									$rowspan_array[$item_group_id]+=1;
									$rowspan_color_array[$item_group_id][$item_color]+=1;
									
									$z++;
								}
								
								$dataArray[$item_group_id][$item_color][$gmts_size]['qty']=$quantity;
								$dataArray[$item_group_id][$item_color][$gmts_size]['val']=$size;
								$uomArray[$item_group_id]=$order_uom;
								$item_colorArray[$item_group_id][$item_color]=$book_item_color;
								$gmts_sizeArray[$item_group_id][$item_color]=$gmts_size;
								$gmts_colorArray[$item_group_id][$item_color]=$item_color;
								//$item_sizeArray[$item_group_id][$item_color]=$item_size;
							}
						}
					}
					
					if($z>0)
					{
						//echo $z.'100';
						foreach($dataArray as $item_group_id=>$item_group_data)
						{ 
							$s=0;
							foreach($item_group_data as $item_color_id=>$item_color_data)
							{
								$c=0;
								foreach($item_color_data as $item_size=>$item_size_data)
								{
									//$wo_qnty=$item_size_data['qty'];
									$size=$item_size_data['val'];
									//echo $row[csf('po_id')];
									$trim_issue_qty=0;$trim_recv_qty=0;$trim_req_qty=0;
									foreach($job_po_id as $po_id)
									{
										//echo $item_size.'<br>';
										$item_color=$book_wo_qty_arr[$po_id][$item_group_id][$item_color_id]['color'];
										$color_number_id_book=$book_wo_qty_arr[$po_id][$item_group_id][$item_color_id]['color_number'];
										$gmt_size=$book_wo_qty_arr[$po_id][$item_group_id][$item_color_id]['gmts_size'];
										$item_size_book=$book_wo_qty_arr[$po_id][$item_group_id][$item_color_id][$item_size]['item_size'];
										 $gmts_size_book=$book_wo_qty_arr[$po_id][$item_group_id][$item_color_id][$item_size]['gmts_size'];
										 $size_data=$size_data_arr[$item_size_book];
										$sensitivity_book=$size_sensitivity_arr[$po_id][$item_group_id];
										
										$trim_req_qty+=$trims_item_color_costing_arr[$po_id][$item_group_id][$item_color_id][$item_size];
										//echo $po_id.'='.$item_group_id.'='.$item_color_id.'='.$item_size.'<br>';
										
										/*if($sensitivity_book==4)
										{
										
										//$issue_qty=$issue_qty_arr[$po_id][$item_group_id][$item_color_id][$size_data];
										}
										else
										{
											//echo $sensitivity_book;
										//$trim_recv_qty=$recv_qty_arr[$po_id][$item_group_id][$item_color_id][$item_size];
										//$issue_qty=$issue_qty_arr[$po_id][$item_group_id][$item_color_id][$size_data];
										}
										//echo $item_size;
										//$recv_basis_arr[$row[csf('po_id')]][$row[csf('item_group_id')]][$row[csf('gmts_color_id')]][$row[csf('gmts_size_id')]];*/
										//echo $item_size.'<br>';
										$sensitivity_book=$book_wo_qty_arr[$po_id][$item_group_id][$item_color_id]['sensitivity'];
										//if($item_size=="" || $item_size==0) $item_size=0;
										$recv_basis=$recv_basis_arr[$po_id][$item_group_id][$item_color_id];
										$recv_sensitivity=$size_sensitivity_arr[$po_id][$item_group_id];
										$issue_basis=$issue_basis_arr[$po_id][$item_group_id];
										$issue_gmt_color=$gmts_color_data_arr[$po_id][$item_group_id];//$gmts_color_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]]
										if($recv_sensitivity==1 || $recv_sensitivity==3)
										{
											 $trim_recv_qty+=$recv_qty_arr[$po_id][$item_group_id][$item_color_id][0];
										}
										else if($recv_sensitivity==2)
										{
											
											$trim_recv_qty+=$recv_qty_arr[$po_id][$item_group_id][$item_size][0];
										}
										else
										{
											//echo $gmts_size_book;
											$gmt_size=$gmts_sizeArray[$item_group_id][$item_color];
											$gmt_color_id=$gmts_colorArray[$item_group_id][$item_color];
											//echo $item_color_id.'<br>';
											//echo $recv_qty_arr[$po_id][$item_group_id][$item_color_id][$gmt_size];
											// $trim_recv_qty+=$recv_qty_arr[$po_id][$item_group_id][$item_color_id][$gmt_size];
											$trim_recv_qty+=$recv_qty_arr[$po_id][$item_group_id][$item_color_id][$item_size];
										}
										//echo $trim_recv_qty;
										if($issue_basis==1)
										{
											
											if($issue_gmt_color==0)
											{
												$trim_issue_qty+=$issue_qty_arr[$po_id][$item_group_id][$item_size];
											}
											else
											{	
												$trim_issue_qty+=$issue_qty_arr[$po_id][$item_group_id][$item_color_id][$item_size];
											}
											
	
										}
										else
										{
											
											if($issue_gmt_color==0)
											{
												$trim_issue_qty+=$issue_qty_arr[$po_id][$item_group_id][$item_color_id][$item_size];
											}
											else
											{
												$trim_issue_qty+=$issue_qty_arr[$po_id][$item_group_id][$item_color_id][$item_size];	
											}
										}
										
										if($sensitivity_book==1 || $sensitivity_book==3)
										{
											$wo_qty+=$book_wo_qty_arr[$po_id][$item_group_id][$item_color_id][0]['quantity'];
	
										}
										else if($sensitivity_book==2)
										{
											
											$wo_qty+=$book_wo_qty_arr[$po_id][$item_group_id][$item_size][0]['quantity'];
										}
										else
										{
											$wo_qty+=$book_wo_qty_arr[$po_id][$item_group_id][$item_color_id][$item_size]['quantity'];

										}
										
										 //$item_color_data=$book_wo_qty_arr[$po_id][$item_group_id][$item_color][$item_size]['color'];
										
										//$recv_qty_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][$row[csf('item_color')]][$item_size_id];
									}
									if($y%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								?> 
									<tr bgcolor="<? echo $bgcolor; ?>"  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
										<?
										if($x==0)
										{
										?>
											<td width="40" rowspan="<? echo $z; ?>"><? echo $y;?> </td>
											<td width="100" rowspan="<? echo $z; ?>"><p><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></p></td>
											<td width="100" rowspan="<? echo $z; ?>"><p><? echo $row[csf('job_no')]; ?></p></td>
											<td width="110" rowspan="<? echo $z; ?>"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
											<td width="140" rowspan="<? echo $z; ?>"><p><? echo $po_number_data; ?></p></td>
											<td width="90" align="right" rowspan="<? echo $z; ?>"><? echo $row[csf('po_quantity')]; ?></td>
											<td width="110"  rowspan="<? echo $z; ?>"><? echo  $gmts_item; ?></td>
										<?	
										}
										
										if($s==0)
										{
										?>
											<td width="130" title="<? echo $item_group_id;?>" rowspan="<? echo $rowspan_array[$item_group_id]; ?>"><p><? echo $trim_group[$item_group_id];//.'<br>'.$size_color_sensitive[$sensitivity_book]; ?></p></td>
											<td width="60" align="center" rowspan="<? echo $rowspan_array[$item_group_id]; ?>"><p><? echo $unit_of_measurement[$uomArray[$item_group_id]]; ?></p></td>
										<?	
										}
										if($c==0)
										{
										?>
											<td width="100" title="<? echo $item_color_id;?>" rowspan="<? echo $rowspan_color_array[$item_group_id][$item_color_id]; ?>"><p><? echo $color_library[$item_color_id]; ?></p></td>
                                            <td width="100" rowspan="<? echo $rowspan_color_array[$item_group_id][$item_color_id]; ?>"><p><? echo $color_library[$item_color];//$color_library[$item_colorArray[$item_group_id][$item_color_id]]; ?></p></td>
										<?	
										}
										$recv_balance_qty=$wo_qty-$trim_recv_qty;
										$left_over_qty=$trim_recv_qty-$trim_issue_qty;
										
										?>
										<td width="60" title="<? echo $item_size;?>"><p><? echo $color_size_library[$item_size];//$color_size_library[$item_size]; ?></p></td>
                                        <td width="60"><p><? echo $item_size_book; ?></p></td>
                                        <td width="90" align="right"><p><? echo number_format($trim_req_qty,2); ?></p></td>
                                      	<? 
									  	if($sensitivity_book==1 || $sensitivity_book==3)
										{
											if($c==0)
											{
												?>
												<td width="90" rowspan="<? echo $rowspan_color_array[$item_group_id][$item_color_id]; ?>" align="right"><? echo number_format($wo_qty,2); ?></td>
												<td width="90" rowspan="<? echo $rowspan_color_array[$item_group_id][$item_color_id]; ?>" align="right"><a href='#report_details' onClick="openmypage('<? echo $job_poid; ?>','<? echo $item_group_id; ?>','<? echo $item_color_id; ?>','<? echo $gmt_size; ?>','<? echo $item_size; ?>','<? echo $recv_basis; ?>','<? echo $without_order; ?>',2,'size_receive_popup');"><?  echo number_format($trim_recv_qty,2); ?></a> <? //echo number_format($trim_recv_qty,2); ?></td>
												<td width="100" rowspan="<? echo $rowspan_color_array[$item_group_id][$item_color_id]; ?>" align="right"><? echo number_format($recv_balance_qty,2); ?></td>
												<td width="90" rowspan="<? echo $rowspan_color_array[$item_group_id][$item_color_id]; ?>" align="right"><a href='#report_details' onClick="openmypage('<? echo $job_poid; ?>','<? echo $item_group_id; ?>','<? echo $item_color_id; ?>','<? echo $gmts_color; ?>','<? echo $item_size; ?>','<? echo $recv_basis; ?>','<? echo $without_order; ?>',3,'size_issue_popup');"><?  echo number_format($trim_issue_qty,2); ?></a><? //echo number_format($trim_issue_qty,2); ?></td>
												<td width="" rowspan="<? echo $rowspan_color_array[$item_group_id][$item_color_id]; ?>" align="right"><? echo number_format($left_over_qty,2); ?></td>
												<?  
											}
										}
										else
										{ 
											?>
											<td width="90"  align="right"><? echo number_format($wo_qty,2); ?></td>	
                                            <td width="90" align="right"><a href='#report_details' onClick="openmypage('<? echo $job_poid; ?>','<? echo $item_group_id; ?>','<? echo $item_color_id; ?>','<? echo $gmt_size; ?>','<? echo $item_size; ?>','<? echo $recv_basis; ?>','<? echo $without_order; ?>',2,'size_receive_popup');"><?  echo number_format($trim_recv_qty,2); ?></a><? //echo number_format($trim_recv_qty,2); ?></td>
                                            <td width="100" align="right"><? echo number_format($recv_balance_qty,2); ?></td>
                                            <td width="90" align="right"><a href='#report_details' onClick="openmypage('<? echo $job_poid; ?>','<? echo $item_group_id; ?>','<? echo $item_color_id; ?>','<? echo $gmts_color; ?>','<? echo $item_size; ?>','<? echo $recv_basis; ?>','<? echo $without_order; ?>',3,'size_issue_popup');"><?  echo number_format($trim_issue_qty,2); ?></a><? //echo number_format($trim_issue_qty,2); ?></td>
                                            <td width="" align="right"><? echo number_format($left_over_qty,2); ?></td>
											<?
										}
										?>
									</tr>
									<?    
                                    $total_wo_qty+=$wo_qty; $total_trim_req_qty+=$trim_req_qty; 
                                    $total_recv_qty+=$trim_recv_qty; 
                                    $total_issue_qty+=$trim_issue_qty;
                                    $total_recv_balance_qty+=$recv_balance_qty;
                                    $total_left_over_qty+=$left_over_qty; 
								   
									$i++;
									$x++;
									$s++;
									$c++;
								}
							}
						}
						$total_order_qty+=$row[csf('po_quantity')];   
					}
					else
					{
						if($y%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?> 
                        <tr bgcolor="<? echo $bgcolor; ?>"  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            <td width="40"><? echo $y;?> </td>
                            <td width="100"><p><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></p></td>
                            <td width="100"><p><? echo  $row[csf('job_no')]; ?></p></td>
                            <td width="110"><p><? echo  $row[csf('style_ref_no')];; ?></p></td>
                            <td width="140"><p><? echo $po_number_data; ?></p></td>
                            <td width="90" align="right"><? echo $row[csf('po_quantity')]; ?></td>
                            <td width="110" ><? echo  $gmts_item; ?></td>
                            <td width="130">&nbsp;</td>
                            <td width="60" align="center">&nbsp;</td>
                            <td width="100">&nbsp;</td>
                            <td width="100">&nbsp;</td>
                            <td width="60">&nbsp;</td>
                            <td width="60">&nbsp;</td>
                            <td width="90" align="right">&nbsp;</td>
                            <td width="90" align="right">&nbsp;</td>
                            <td width="90" align="right">&nbsp;</td>
                            <td width="100" align="right">&nbsp;</td>
                            <td width="90" align="right">&nbsp;</td>
                            <td align="right">&nbsp;</td>
                        </tr>
                    <?        
                    	$total_order_qty+=$row[csf('po_quantity')];   
                        $i++;	
					}
					$y++;
					
					$y++;
				}
				?>
             	<tr class="tbl_bottom">
               		<td colspan="5" align="right">Total</td>
                    <td align="right"><? echo number_format($total_order_qty,2); ?></td>
                    <td colspan="4" align="right"></td>
                    <td  align="right"></td> <td  align="right"></td>
                    <td align="right"><? //echo number_format($total_recv_qty,2); ?></td>
                    <td align="right"><? echo number_format($total_trim_req_qty,2); ?></td>
              		<td align="right"><? echo number_format($total_wo_qty,2); ?></td>
                    <td align="right"><? echo number_format($total_recv_qty,2); ?></td>
               		
               		
               		<td><? echo number_format($total_recv_balance_qty,2); ?></td>
               		<td><? echo number_format($total_issue_qty,2); ?></td>
               		<td align="right"><? echo number_format($total_left_over_qty,2); ?></td>
				</tr>        
			</table>
		</div>
	</fieldset>
  </div>
      <?
	exit();
}

if($action=="receive_item_color_size_popup")
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<fieldset style="width:470px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="450" cellpadding="0" cellspacing="0" align="center">
				<thead>
                    <th width="30">Sl</th>
                    <th width="100">Receive ID</th>
                    <th width="75">Receive Date</th>
                    <th width="80">Recv. Qty</th>
				</thead>
                <tbody>
                <?
					if ($itemcolor!=0) $itemcolor_cond=" and b.item_color=$itemcolor"; else $itemcolor_cond="";
					if ($item_size!=0) $item_size_cond=" and b.item_size=$item_size"; else $item_size_cond="";
					$i=1;
					$mrr_sql="select a.id, a.recv_number, a.receive_date, c.quantity as quantity
					from inv_receive_master a, inv_trims_entry_dtls b, order_wise_pro_details c 
					where a.id=b.mst_id  and a.entry_form=24 and b.id=c.dtls_id and c.trans_type=1 and   c.po_breakdown_id in($po_id)  and a.company_id='$companyID' and b.item_group_id='$item_group' $itemcolor_cond $item_size_cond and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 group by    c.po_breakdown_id,b.item_group_id,c.quantity,a.recv_number,a.id,a.receive_date";
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
        </div>
    </fieldset>
    <?
	exit();
}

if($action=="issue_color_size_popup")
{
	echo load_html_head_contents("Issue Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<fieldset style="width:470px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="450" cellpadding="0" cellspacing="0" align="center">
				<thead>
                    <th width="30">Sl</th>
                    <th width="100">Issue ID</th>
                    <th width="75">Issue Date</th>
                    <th width="80">Issue Qty</th>
				</thead>
                <tbody>
                <?
					if ($itemcolor!=0) $itemcolor_cond=" and b.item_color_id=$itemcolor"; else $itemcolor_cond="";
					if ($item_size!=0) $item_size_cond=" and b.item_size=$item_size"; else $item_size_cond="";
					$i=1;
					$mrr_sql="select a.id, a.issue_number, a.issue_date,c.quantity as quantity
					from  inv_issue_master a,inv_trims_issue_dtls b, order_wise_pro_details c,product_details_master p 
					where a.id=b.mst_id  and a.entry_form=25 and p.id=b.prod_id and b.id=c.dtls_id and  c.trans_type=2 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and  c.po_breakdown_id in($po_id)  and a.company_id='$companyID' and p.item_group_id='$item_group' $itemcolor_cond  $item_size_cond group by c.po_breakdown_id,p.item_group_id,c.quantity,a.issue_number,a.id,a.issue_date ";
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
        </div>
    </fieldset>
    <?
	exit();
}

if($action=="wo_receive_popup")
{
	echo load_html_head_contents("WO Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	
	?>
	<fieldset style="width:470px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="450" cellpadding="0" cellspacing="0" align="center">
				<caption>WO Detail</caption>
                <thead>
                    <th width="30">Sl</th>
                    <th width="100">WO No</th>
                    <th width="75">WO Date</th>
                    <th width="80">WO. Qty</th>
                    
                    
                    
				</thead>
                <tbody>
                <?
					
					$i=1;
					if($db_type==0) $null_val="c.color_number_id,c.item_color,c.item_size,c.gmts_sizes,";
					else if($db_type==2) $null_val="nvl(c.color_number_id,0) as color_number_id,nvl(c.item_color,0) as item_color,nvl(c.gmts_sizes,0) as gmts_sizes,c.item_size,";
					//echo $item_size."<br>";//die;
					if($item_group) $item_group_cond="and b.trim_group=$item_group"; else $item_group_cond="";
					if($item_color!="") $item_color_cond=" and c.item_color=$item_color"; else $item_color_cond=" and c.item_color is null";
					if($item_size) $item_size_cond="and c.item_size='$item_size'"; else $item_size_cond="";
					//echo $item_size_cond.jh;die;
					/*$mrr_sql="select a.id, a.recv_number, a.receive_date, SUM(c.quantity) as quantity
					from inv_receive_master a, inv_trims_entry_dtls b, order_wise_pro_details c 
					where a.id=b.mst_id  and a.entry_form=24 and c.entry_form=24  and b.id=c.dtls_id and c.trans_type=1  and  c.po_breakdown_id in($po_id)  and a.company_id='$companyID' and b.item_group_id='$item_group' and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 group by    c.po_breakdown_id,b.item_group_id,a.recv_number,a.id,a.receive_date";*/
					 $sql_bookingqty =("select a.booking_no,a.booking_date,c.cons as wo_qnty,b.id as dtls_id,c.id,b.trim_group as item_group,  c.description,c.brand_supplier,b.po_break_down_id as po_id,b.sensitivity from wo_booking_mst a, wo_booking_dtls b,wo_trim_book_con_dtls c where a.booking_no=b.booking_no and b.id=c.wo_trim_booking_dtls_id   and a.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and b.po_break_down_id in($po_id) $item_group_cond $item_color_cond $item_size_cond ");
			 
					//echo $sql_bookingqty;
					$dtlsArray=sql_select($sql_bookingqty);
					
					foreach($dtlsArray as $row)
					{
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
							if($row[csf('wo_qnty')]>0)
							{	
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
                            <td width="100"><p><? echo $row[csf('booking_no')]; ?></p></td>
                            <td width="75"><p><? echo change_date_format($row[csf('booking_date')]); ?></p></td>
                           
                            <td width="80" align="right"><p><? echo number_format($row[csf('wo_qnty')],2); ?></p></td>
                        </tr>
						<?
						$tot_qty+=$row[csf('wo_qnty')];
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
            
        </div>
    </fieldset>
    <?
	exit();
}

// Recv Qty

if($action=="receive_popup")
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	
	?>
	<fieldset style="width:730px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="730" cellpadding="0" cellspacing="0" align="center">
				<caption><strong>Recevied Detail</strong></caption>
                <thead>
                    <th width="30">Sl</th>
                    <th width="50">Prod. ID</th>
                    <th width="90">Trans type</th>
                    <th width="110">Recv. ID</th>
                    <th width="90">Chalan No</th>
                    <th width="70">Recv. Date</th>
                    <th width="120">Item Desc.</th>
                    <th width="80">Recv. Qty.</th>
                    <th>Reject Qty.</th>
				</thead>
                <tbody>
                <?
					//po_id,item_group,item_color,gmts_color,item_size,recv_basis,without_order,type,action
					$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details"  );
					$i=1;
					$item_color=str_replace("'","",$item_color);
					$item_size=str_replace("'","",$item_size);
					//echo $item_color.'=='.$item_size;
					$item_color_con2="";$item_size_con2="";
					if($item_color!="")
					{
						if($item_color==404)
						{
							$item_color_con2=" and d.item_color in(0,404)";
						}
						else
						{
							$item_color_con2=" and d.item_color=$item_color";
						}
						
					}
					 
					if($item_size) 
					{
						$item_size_con2=" and d.item_size='$item_size'";
					}
					
					//if($db_type==0) $null_val="c.color_number_id,c.item_color,c.item_size,c.gmts_sizes,";
					//else if($db_type==2) $null_val="nvl(c.color_number_id,0) as color_number_id,nvl(c.item_color,0) as item_color,nvl(c.gmts_sizes,0) as gmts_sizes,c.item_size,";
				
					
					
				   /*$mrr_sql="select a.id, a.recv_number, a.challan_no, a.receive_date, c.quantity as quantity, b.reject_receive_qnty as reject_qty, b.item_description, c.prod_id, c.trans_type
					from inv_receive_master a, inv_trims_entry_dtls b, order_wise_pro_details c 
					where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(24,49) and c.entry_form in(24,49) and c.trans_type in(1,4)  and  c.po_breakdown_id in($po_id)  and a.company_id='$companyID' and b.item_group_id='$item_group' and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 $item_color_con $item_size_con 
					union all
					select a.id, a.transfer_system_id as recv_number, a.challan_no, a.transfer_date as receive_date, c.quantity as quantity, 0 as reject_qty, d.item_description, c.prod_id, c.trans_type
					from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, product_details_master d 
					where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.entry_form in(78,112) and c.entry_form in(78,112) and c.trans_type in(5)  and  c.po_breakdown_id in($po_id)  and a.company_id='$companyID' and d.item_group_id='$item_group' and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 $item_color_con2 $item_size_con2";*/
					
					$mrr_sql="SELECT a.id, a.recv_number, a.challan_no, a.receive_date, c.quantity as quantity, b.cons_reject_qnty as reject_qty, d.item_description, c.prod_id, c.trans_type
					from inv_receive_master a, inv_transaction b, order_wise_pro_details c, product_details_master d 
					where a.id=b.mst_id and b.id=c.trans_id and c.prod_id=d.id and a.entry_form in(24) and c.entry_form in(24) and b.transaction_type in(1) and c.trans_type in(1) and c.po_breakdown_id in($po_id)  and a.company_id='$companyID' and d.item_group_id='$item_group' and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 $item_color_con2 $item_size_con2
					union all
					select a.id, a.issue_number as recv_number, a.challan_no, a.issue_date as receive_date, c.quantity as quantity, b.cons_reject_qnty as reject_qty, d.item_description, c.prod_id, c.trans_type
					from inv_issue_master a, inv_transaction b, order_wise_pro_details c, product_details_master d 
					where a.id=b.mst_id and b.id=c.trans_id and c.prod_id=d.id and a.entry_form in(49) and c.entry_form in(49) and b.transaction_type in(3) and c.trans_type in(3) and c.po_breakdown_id in($po_id)  and a.company_id='$companyID' and d.item_group_id='$item_group' and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 $item_color_con2 $item_size_con2 
					union all
					select a.id, a.transfer_system_id as recv_number, a.challan_no, a.transfer_date as receive_date, c.quantity as quantity, 0 as reject_qty, d.item_description, c.prod_id, c.trans_type
					from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, product_details_master d 
					where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.entry_form in(78,112) and c.entry_form in(78,112) and c.trans_type in(5)  and  c.po_breakdown_id in($po_id)  and a.to_company='$companyID' and d.item_group_id='$item_group' and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 $item_color_con2 $item_size_con2";
					//echo $mrr_sql;
					
					$dtlsArray=sql_select($mrr_sql);
					$tot_reject_qty=0;$tot_qty=0;
					foreach($dtlsArray as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						if($row[csf('quantity')]>0)
						{
							if($row[csf('trans_type')]==1) $trans_type="Receive";
							else if($row[csf('trans_type')]==3) $trans_type="Receive Rtn";
							else $trans_type="Transfer In";
							?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            	
								<td align="center"><p><? echo $i; ?></p></td>
								<td align="center"><p><? echo $row[csf('prod_id')]; ?></p></td>
                                <td align="center"><p><? echo $trans_type ?></p></td>
								<td align="center"><p><? echo $row[csf('recv_number')]; ?></p></td>
								<td align="center"><p><? echo $row[csf('challan_no')]; ?></p></td>
								<td align="center"><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
								<td><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
								<td align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
								<td align="right"><p><? echo number_format($row[csf('reject_qty')],2); ?></p></td>
							</tr>
							<?
							if($row[csf('trans_type')]==1 || $row[csf('trans_type')]==5)
							{
								$tot_qty +=$row[csf('quantity')];
							}
							else
							{
								$tot_qty -=$row[csf('quantity')];
							}
							
							$tot_reject_qty+=$row[csf('reject_qty')];
							$i++;
						}
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="7" align="right">Total</td>
                        <td align="right"><? echo number_format($tot_qty,2); ?>&nbsp;</td>
                         <td align="right"><? echo number_format($tot_reject_qty,2); ?>&nbsp;</td>
                    </tr>
                </tfoot>
            </table>
            <br>
            
        </div>
    </fieldset>
    <?
	exit();
}
//Trims Issue

if($action=="size_receive_popup")
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	
	?>
	<fieldset style="width:700px; margin-left:3px">
		<div id="scroll_body" align="center">
        <i>  Recv Return not allowed</i>
			<table border="1" class="rpt_table" rules="all" width="680" cellpadding="0" cellspacing="0" align="center">
           
				<caption><strong>Recevied Detail</strong></caption>
                <thead>
                   <th width="30">Sl</th>
                    <th width="70">Prod. ID</th>
                    <th width="120">Recv. ID</th>
                    <th width="100">Chalan No</th>
                    <th width="80">Recv. Date</th>
                    <th width="120">Item Desc.</th>
                    <th width="80">Recv. Qty.</th>
                    <th width="80">Reject Qty.</th>
				</thead>
                <tbody>
                <?
					//po_id,item_group,item_color,gmts_color,item_size,recv_basis,without_order,type,action
					$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details"  );
					$i=1;
					$item_color=str_replace("'","",$item_color);
					$item_size=str_replace("'","",$item_size);
					//echo $item_color.'=='.$item_size;
					if($item_color!=0)  $item_color_con="and b.item_color=$item_color"; else $item_color_con="";
					if($item_size==0 || $item_size=='') $item_size_con=""; else $item_size_con="and b.gmts_size_id='$item_size'";
					
					//if($db_type==0) $null_val="c.color_number_id,c.item_color,c.item_size,c.gmts_sizes,";
					//else if($db_type==2) $null_val="nvl(c.color_number_id,0) as color_number_id,nvl(c.item_color,0) as item_color,nvl(c.gmts_sizes,0) as gmts_sizes,c.item_size,";
				
					
					
				   $mrr_sql="select a.id, a.recv_number, a.challan_no,a.receive_date, c.quantity as quantity,b.reject_receive_qnty as reject_qty,b.item_description,c.prod_id
					from inv_receive_master a, inv_trims_entry_dtls b, order_wise_pro_details c 
					where a.id=b.mst_id  and a.entry_form=24 and c.entry_form=24  and b.id=c.dtls_id and c.trans_type=1  and  c.po_breakdown_id in($po_id)  and a.company_id='$companyID' and b.item_group_id='$item_group' and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 $item_color_con $item_size_con ";
					
					// echo $mrr_sql;
					$dtlsArray=sql_select($mrr_sql);
					$tot_reject_qty=0;$tot_qty=0;
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
                             <td width="70"><p><? echo $row[csf('prod_id')]; ?></p></td>
                            <td width="120"><p><? echo $row[csf('recv_number')]; ?></p></td>
                            <td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>
                            <td width="80"><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
                           
                           
                          
                            <td width="120"><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
                             <td width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($row[csf('reject_qty')],2); ?></p></td>
                        </tr>
						<?
						$tot_qty+=$row[csf('quantity')];
						$tot_reject_qty+=$row[csf('reject_qty')];
						$i++;
							}
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="6" align="right">Total</td>
                        <td align="right"><? echo number_format($tot_qty,2); ?>&nbsp;</td>
                         <td align="right"><? echo number_format($tot_reject_qty,2); ?>&nbsp;</td>
                    </tr>
                </tfoot>
            </table>
            <br>
            
        </div>
    </fieldset>
    <?
	exit();
}
//Trims Issue

if($action=="issue_popup")
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	
	?>
	<fieldset style="width:730px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="730" cellpadding="0" cellspacing="0" align="left">
				 <caption>Issue Details </caption>
                <thead>
                    <th width="30">Sl</th>
                    <th width="50">Prod. ID</th>
                    <th width="90">Trans type</th>
                    <th width="130">Issue. ID</th>
                    <th width="80">Chalan No</th>
                    <th width="80">Issue. Date</th>
                    <th width="120">Item Desc.</th>
                    <th width="80">Issue. Qty.</th>
                    <th>Sewing Line</th>
				</thead>
                <?
					$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details"  );
					$line_arr=return_library_array( "select id,line_name from lib_sewing_line where is_deleted=0 and status_active=1", "id", "line_name");

					$i=1;
					$item_color=str_replace("'","",$item_color);
					$item_size=str_replace("'","",$item_size);
					//echo $item_color.'=='.$item_size;
					//if($item_color==0 || $item_color=='') $item_color_con="";else $item_color_con="and b.item_color_id=$item_color";
					//if($item_size==0 || $item_size=='')  $item_size_con="";else $item_size_con="and b.item_size='$item_size'";
					
					
					//echo $item_size.jahid;die;
					$item_color_con2="";$item_size_con2=""; 
					if($item_color!="")
					{
						if($item_color==404)
						{
							$item_color_con2=" and d.item_color in(0,404)";
						}
						else
						{
							$item_color_con2=" and d.item_color=$item_color";
						}
						
					} 
					
					if($item_size) 
					{
						$item_size_con2=" and d.item_size='$item_size'";
					}
					//echo $item_color_con.'=='.$item_size_con;
				 	$mrr_sql=("select a.id, a.issue_number, a.challan_no, c.prod_id, a.issue_date, d.item_description, c.quantity as quantity, b.sewing_line, c.trans_type
					from  inv_issue_master a, inv_trims_issue_dtls b, order_wise_pro_details c,product_details_master d 
					where a.id=b.mst_id and b.id=c.dtls_id and b.trans_id=c.trans_id and c.prod_id=d.id and a.entry_form in(25)  and d.entry_form=24 and c.trans_type in(2) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and d.item_group_id='$item_group' $item_color_con2 $item_size_con2 
					union all
					select a.id, a.recv_number as issue_number, a.challan_no, c.prod_id, a.receive_date as issue_date, d.item_description, c.quantity as quantity, 0 as sewing_line, c.trans_type
					from  inv_receive_master a, inv_transaction b, order_wise_pro_details c,product_details_master d 
					where a.id=b.mst_id and b.id=c.trans_id and c.prod_id=d.id and a.entry_form in(73) and d.entry_form=24 and c.entry_form in(73) and b.transaction_type in(4) and c.trans_type in(4) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and d.item_group_id='$item_group' $item_color_con2 $item_size_con2 
					union all
					
					select a.id, a.transfer_system_id as issue_number, a.challan_no, c.prod_id, a.transfer_date as issue_date, d.item_description, c.quantity as quantity, 0 as sewing_line, c.trans_type
					from  inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c,product_details_master d 
					where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.entry_form in(78,112) and d.entry_form=24 and c.trans_type in(6) and a.is_deleted=0 and a.status_active=1 and
					b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and d.item_group_id='$item_group' and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 $item_color_con2 $item_size_con2");
					
					
										
					//echo $mrr_sql;
					$dtlsArray=sql_select($mrr_sql);
					?>
					<tbody> 
					<?
					foreach($dtlsArray as $row)
					{
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";	
						if($row[csf('trans_type')]==2) $trans_type="Issue";
						else if($row[csf('trans_type')]==4) $trans_type="Issue Rtn";
						else $trans_type="Transfer Out";	
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td align="center"><p><? echo $i; ?></p></td>
                            <td align="center"><p><? echo $row[csf('prod_id')]; ?></p></td>
                            <td align="center"><p><? echo $trans_type; ?></p></td>
                            <td><p><? echo $row[csf('issue_number')]; ?></p></td>
                            <td align="center"><p><? echo $row[csf('challan_no')]; ?></p></td>
                            <td align="center"><p><? echo  change_date_format($row[csf('issue_date')]); ?></p></td>
                            <td><p><? echo $row[csf('item_description')]; ?></p></td>
                            <td align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
                            <td align="center"><p><? echo $line_arr[$row[csf('sewing_line')]]; ?></p></td>
                        </tr>
						<?
						if($row[csf('trans_type')]==2 || $row[csf('trans_type')]==6)
						{
							$tot_qty +=$row[csf('quantity')];
						}
						else
						{
							$tot_qty -=$row[csf('quantity')];
						}
						
						$i++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="6" align="right"></td>
                        <td align="right">Total</td>
                        <td><? echo number_format($tot_qty,2); ?></td>
                        <td>&nbsp;</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </fieldset>
    <script type="text/javascript">
    	setFilterGrid('list_view',-1);
    </script>>
    <?
	exit();
}

if($action=="size_issue_popup")
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	
	?>
	<fieldset style="width:678px; margin-left:3px">
		<div id="scroll_body" align="center">
         <i>  Issue Return not Allowed</i>
			<table border="1" class="rpt_table" rules="all" width="660" cellpadding="0" cellspacing="0" align="left">
				 <caption>Issue Details </caption>
                <thead>
                    <th width="30">Sl</th>
                    <th width="70">Prod. ID</th>
                    <th width="100">Issue. ID</th>
                     <th width="100">Chalan No</th>
                     <th width="100">Issue. Date</th>
                    <th width="80">Item Desc.</th>
                    <th width="80">Issue. Qty.</th>
                    <th width="80">Sewing Line</th>
				</thead>
                </table>
                <?
                	$line_arr=return_library_array( "select id,line_name from lib_sewing_line where is_deleted=0 and status_active=1", "id", "line_name");
					$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details"  );
					$i=1;
					$item_color=str_replace("'","",$item_color);
					$item_size=str_replace("'","",$item_size);
					//echo $item_color.'=='.$item_size;
					if($item_color==0 || $item_color=='') $item_color_con="";else $item_color_con="and b.item_color_id=$item_color";
					if($item_size==0 || $item_size=='')  $item_size_con="";else $item_size_con="and b.gmts_size_id='$item_size'"; 
					//echo $item_color_con.'=='.$item_size_con;
				 $mrr_sql=("select a.id, a.issue_number,a.challan_no,b.prod_id, a.issue_date,b.item_description,SUM(c.quantity) as quantity,b.sewing_line
					from  inv_issue_master a,inv_trims_issue_dtls b, order_wise_pro_details c,product_details_master p 
					where a.id=b.mst_id  and a.entry_form=25  and p.id=b.prod_id and b.id=c.dtls_id and b.trans_id=c.trans_id and c.trans_type=2 and a.is_deleted=0 and a.status_active=1 and
					b.status_active=1 and b.is_deleted=0 and  c.po_breakdown_id in($po_id) and a.company_id='$companyID' and b.item_group_id='$item_group' $item_color_con $item_size_con group by c.po_breakdown_id,p.item_group_id,b.item_description,a.issue_number,a.id,a.issue_date,b.prod_id,a.challan_no,b.sewing_line ");					
					
					$dtlsArray=sql_select($mrr_sql);
					?>
				<div style="width:678px; max-height:250px; overflow-y:scroll" id="list_container_batch" align="left">	 
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="660" class="rpt_table" id="list_view"> 
					<tbody> 
					<?
					foreach($dtlsArray as $row)
					{
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";	
							
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30" align="center"><p><? echo $i; ?></p></td>
                            <td width="70" align="center"><p><? echo $row[csf('prod_id')]; ?></p></td>
                            <td width="100"><p><? echo $row[csf('issue_number')]; ?></p></td>
                            <td width="100" align="center"><p><? echo $row[csf('challan_no')]; ?></p></td>
                            <td width="100" align="center"><p><? echo  change_date_format($row[csf('issue_date')]); ?></p></td>
                            <td width="80" align="center"><p><? echo $row[csf('item_description')]; ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
                            <td width="80" align="center"><p><? echo $line_arr[$row[csf('sewing_line')]]; ?></p></td>
                        </tr>
						<?
						$tot_qty+=$row[csf('quantity')];
						$i++;
					}
				?>
	                </tbody>
	                <tfoot>
	                	<tr class="tbl_bottom">
	                    	<td colspan="5" align="right"></td>
	                        <td align="right">Total</td>
	                        <td><? echo number_format($tot_qty,2); ?></td>
	                        <td>&nbsp;</td>
	                    </tr>
	                </tfoot>
            	</table>
        		</div>
            <br>
            
        </div>
    </fieldset>
    <script type="text/javascript">
    	setFilterGrid("list_view",-1);
    </script>
    <?
	exit();
}


?>