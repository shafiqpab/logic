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



$company_library=return_library_array("select id,company_name from lib_company", "id", "company_name");
$buyer_arr=return_library_array("select id, short_name from lib_buyer",'id','short_name');
$trim_group= return_library_array("select id, item_name from lib_item_group",'id','item_name');


if ($action=="load_drop_down_store")
{
	echo create_drop_down( "cbo_store_name", 140, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id='$data' and b.category_type=4 and a.status_active=1 and a.is_deleted=0 $store_location_credential_cond group by a.id, a.store_name order by a.store_name","id,store_name", 1, "--Select store--", 0, "" );
	exit();
}

if ($action=="load_drop_down_buyer")
{
	//$data=explode('_',$data);
	echo create_drop_down( "cbo_buyer_id", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "load_drop_down( 'requires/style_wise_trims_receive_issue_stock_controller',this.value, 'load_drop_down_season_buyer', 'season_td') ;",0);
	exit();
}

if ($action=="load_drop_down_season_buyer")
{
	echo create_drop_down( "cbo_season_name", 120, "select id, season_name from lib_buyer_season where buyer_id='$data' and status_active =1 and is_deleted=0 order by season_name ASC","id,season_name", 1, "-- Select Season--", "", "" );
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
				if($("#tr_"+i).css('display') != 'none'){
					$("#tr_"+i).click();
				}

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

	if($db_type==0) $year_cond="and year(a.insert_date)='$data[3]'";
	else if($db_type==2) $year_cond="and to_char(a.insert_date,'YYYY')='$data[3]'";

	if($db_type==0) $year_field="YEAR(b.insert_date) as year";
	else if($db_type==2) $year_field="to_char(b.insert_date,'YYYY') as year";
	else $year_field="";

	$sql ="select distinct a.id,a.po_number,b.job_no_prefix_num as job_prefix,$year_field from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no $company_id  $buyer_id $style $year_cond";
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
	$cbo_store_name = str_replace("'","",$cbo_store_name);
	$cbo_season_name = str_replace("'","",$cbo_season_name);
	
	
	$color_library=return_library_array("select id,color_name from lib_color", "id", "color_name");
	
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

	if($cbo_season_name>0)  $buyer_id_cond.=" and a.season_buyer_wise=$cbo_season_name";



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
	

	$cbo_search_by = str_replace("'","",$cbo_search_by);
	$exFactoryDate_cond = "";
	if($cbo_search_by == 2) // Ex-factory date
	{
		if( $date_from!="" && $date_to!="") $exFactoryDate_cond= " and ex_factory_date between '".$date_from."' and '".$date_to."'"; else $exFactoryDate_cond="";
		$sql_exFactory = "SELECT PO_BREAK_DOWN_ID from pro_ex_factory_mst where status_active=1 and is_deleted=0 $exFactoryDate_cond group by po_break_down_id";

		$sqlExFactoryData =sql_select($sql_exFactory);
		foreach($sqlExFactoryData as $row)
		{
			if ($trans_check[$row['PO_BREAK_DOWN_ID']]=='') 
			{
				$trans_check[$row['PO_BREAK_DOWN_ID']]=$row['PO_BREAK_DOWN_ID'];
				$all_po_id.=$row['PO_BREAK_DOWN_ID'].',';
			}
		}
		$all_po_id=chop($all_po_id,",");
		$p=1;
		if($all_po_id!="")
		{
			$all_po_id_arr=array_chunk(array_unique(explode(",",$all_po_id)),999);
			foreach($all_po_id_arr as $po_id)
			{
				if($p==1) $exfactory_po_id_cond .=" and (b.id in(".implode(',',$po_id).")"; else $exfactory_po_id_cond .=" or b.id in(".implode(',',$po_id).")";
				$p++;
			}
			$exfactory_po_id_cond .=" )";
		}
		// echo $exfactory_po_id_cond.Tipu;die;
	}
	else // Shipment date
	{
		if( $date_from!="" && $date_to!="") $pub_date= " and b.pub_shipment_date between '".$date_from."' and '".$date_to."'"; else $pub_date="";
	}
	

	if($cbo_store_name!=0) $store_cond=" and c.store_id =".str_replace("'","",$cbo_store_name); else $store_cond="";

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
	if(str_replace("'","",$all_po_id) !='')
	{
		$condition->po_id_in("$all_po_id");
	}
	if($cbo_search_by == 1) // Shipment date
	{
		if(str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!='')
		{
			$condition->pub_shipment_date(" between '$date_from' and '$date_to'");
		}
	}

	$condition->init();

	$trims= new trims($condition);
	//echo $trims->getQuery();die;
	$trims_costing_arr=$trims->getQtyArray_by_orderAndItemid();

	//echo "<pre>";
	//print_r($trims_costing_arr);die;


	ob_start();
	?>
    <fieldset style="width:2580px;">
        <table width="1580">
            <tr class="form_caption">
                <td colspan="23" align="center" style="border:none;font-size:16px; font-weight:bold"> <? echo $report_title; ?></td>
            </tr>
            <tr class="form_caption">
                <td colspan="23" align="center"><? echo $company_library[$cbo_company]; ?></td>
            </tr>
        </table>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="2580" class="rpt_table" >
			<thead>
				<th width="40">SL </th>
                <th width="70">Buyer</th>
                <th width="70">Season</th>
                <th width="100">Job No</th>
                <th width="110">Style</th>
                <th width="140">Order No</th>
                <th width="70">File No</th>
                <th width="80">Ref. No</th>
                <th width="90">Order Qty.</th>
                <th width="80">Order Qty.(Dzn)</th>
                <th width="150">Store</th>
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
                <th width="100">Left Over Value</th>
                <th>DOH</th>
            </thead>
        </table>
        <div style="width:2580px; overflow-y:scroll; max-height:350px;font-size:12px; " id="scroll_body">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="2560" class="rpt_table" id="tbl_issue_status" align="left">
		    <?

		    $store_name_arr = return_library_array("select a.id, a.store_name from lib_store_location a", 'id', 'store_name');
			$season_arr = return_library_array("select a.id, a.season_name from lib_buyer_season a", 'id', 'season_name');

			$i=1; $y=1; $tot_receive_qty=0; $tot_receive_value=0; $tot_issue_qty=0; $total_left_over=0; $total_left_over_balance=0; $dataArrayRecv=array();$wo_qty_ArrayRecv=array();$total_wo_qty=0;$total_wo_val=$total_wo_amount=$total_issue_amount=0;



			$sql_bookingqty_non = sql_select("select sum(b.trim_qty) as wo_qnty,sum(b.amount) as amount,b.trim_group as item_group,b.fabric_color as item_color,b.gmts_color as color_number_id,b.fabric_description as description from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where a.company_id=$cbo_company and a.booking_no=b.booking_no  $buyer_id_cond group by  b.trim_group,b.fabric_color,b.gmts_color,b.fabric_description");
			foreach($sql_bookingqty_non as $row)
			{
				$wo_qty_ArrayRecv[$row[csf('item_group')]][$row[csf('item_color')]][$row[csf('fabric_color')]]=$row[csf('wo_qnty')];
				$wo_qty_ArrayRecvAmt[$row[csf('item_group')]][$row[csf('item_color')]][$row[csf('fabric_color')]]=$row[csf('amount')];
			}


			if($db_type==0)
			{
				$sql="select a.style_ref_no, a.buyer_name, a.season_buyer_wise, a.job_no, group_concat(b.po_number) as po_number, group_concat(b.id) as po_id,group_concat(b.file_no) as file_no,group_concat(b.grouping) as grouping, sum(a.total_set_qnty*b.po_quantity) as po_quantity , p.costing_date, p.costing_per
				from wo_pre_cost_mst p, wo_po_details_master a, wo_po_break_down b
				where p.job_no=a.job_no and a.job_no=b.job_no_mst and a.company_name=$cbo_company $buyer_id_cond $style $po_id_cond $file_cond $ref_cond $exfactory_po_id_cond $pub_date
				group by a.job_no, a.style_ref_no, a.buyer_name, a.season_buyer_wise, p.costing_date, p.costing_per
				order by a.id";
			}
			else
			{
				$sql="select a.style_ref_no, a.buyer_name, a.season_buyer_wise, a.job_no, LISTAGG(CAST(b.po_number AS VARCHAR2(4000)),',') WITHIN GROUP (ORDER BY b.id) as po_number, LISTAGG(CAST(b.id AS VARCHAR2(4000)),',') WITHIN GROUP (ORDER BY b.id) as po_id,LISTAGG(CAST(b.file_no AS VARCHAR2(4000)),',') WITHIN GROUP (ORDER BY b.id) as file_no,LISTAGG(CAST(b.grouping AS VARCHAR2(4000)),',') WITHIN GROUP (ORDER BY b.id) as grouping, sum(a.total_set_qnty*b.po_quantity) as po_quantity, p.costing_date, p.costing_per
				from wo_pre_cost_mst p, wo_po_details_master a, wo_po_break_down b
				where p.job_no=a.job_no and a.job_no=b.job_no_mst and a.company_name=$cbo_company $buyer_id_cond $style $po_id_cond $exfactory_po_id_cond $pub_date $file_cond $ref_cond
				group by a.id, a.job_no, a.style_ref_no, a.buyer_name, a.season_buyer_wise, a.total_set_qnty, p.costing_date, p.costing_per
				order by a.id";
			}
			// echo $sql; die;
			$result=sql_select($sql);
			$all_order_id="";
			foreach($result as $row)
			{
				$all_order_id.=$row[csf("po_id")].",";

			}
			$all_order_id=chop($all_order_id,",");
			$p=1;
			$sql_recv="select c.receive_basis,c.booking_without_order as without_order,a.item_group_id,a.item_description,c.store_id, a.order_uom, a.item_color, a.item_size, a.cons_rate, b.po_breakdown_id as po_id,a.gmts_color_id, b.quantity , (b.quantity*a.rate) as amount, a.item_color_temp, a.prod_id
			from inv_receive_master c,inv_trims_entry_dtls a, order_wise_pro_details b
			where a.id=b.dtls_id and a.trans_id=b.trans_id and c.id=a.mst_id and b.trans_type=1 and b.entry_form=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $store_cond";
			/*$sql_recv="select c.receive_basis,c.booking_without_order as without_order,a.item_group_id,a.item_description,c.store_id, a.order_uom, a.item_color, a.item_size, a.cons_rate, b.po_breakdown_id as po_id,a.gmts_color_id, b.quantity , (b.quantity*a.rate) as amount
			from inv_receive_master c,inv_trims_entry_dtls a, order_wise_pro_details b
			where a.id=b.dtls_id and a.trans_id=b.trans_id and c.id=a.mst_id and b.trans_type=1 and b.entry_form=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $store_cond";*/
			if($all_order_id!="")
			{
				$all_order_id_arr=array_chunk(array_unique(explode(",",$all_order_id)),999);
				foreach($all_order_id_arr as $order_id)
				{
					if($p==1) $sql_recv .=" and (b.po_breakdown_id in(".implode(',',$order_id).")"; else $sql_sub_lc .=" or b.po_breakdown_id  in(".implode(',',$order_id).")";
					$p++;
				}
				$sql_recv .=" )";
			}
			//echo $sql_recv;die;
			$sql_recv_result=sql_select($sql_recv);
            $prod_id_arr = [];
			foreach($sql_recv_result as $row)
			{
                $prod_id_arr[$row[csf('prod_id')]] = $row[csf('prod_id')];
				if($row[csf('item_size')]=="") $item_size=0; else $item_size=$row[csf('item_size')];
				$dataArrayRecv[$row[csf('po_id')]].=$row[csf('item_group_id')]."_".$row[csf('order_uom')]."_".$row[csf('item_color')]."_".$item_size."_".$row[csf('cons_rate')]."_".$row[csf('quantity')]."_".$row[csf('receive_basis')]."_".$row[csf('without_order')]."_".$row[csf('gmts_color_id')]."_".$row[csf('item_description')]."_".$row[csf('store_id')]."_".$row[csf('amount')]."_".$row[csf('item_color_temp')]."_".$row[csf('prod_id')]."**";
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
//                echo $returnRes_date;die;
                $result_returnRes_date = sql_select($returnRes_date);
                foreach ($result_returnRes_date as $row) {
                    $date_array[$row["PROD_ID"]]['MIN_DATE'] = $row["MIN_DATE"];
                    $date_array[$row["PROD_ID"]]['MAX_DATE'] = $row["MAX_DATE"];
                }
            }

			if($db_type==0) $null_val="c.color_number_id,c.item_color,c.item_size,c.gmts_sizes,";
			else if($db_type==2) $null_val="nvl(c.color_number_id,0) as color_number_id,nvl(c.item_color,0) as item_color,nvl(c.gmts_sizes,0) as gmts_sizes,nvl(c.item_size,0) as item_size,";
			$sql_bookingqty ="select c.cons as wo_qnty,c.amount,b.id as dtls_id,c.id,b.trim_group as item_group, $null_val c.description,c.brand_supplier,b.po_break_down_id as po_id,b.sensitivity from wo_booking_mst a, wo_booking_dtls b,wo_trim_book_con_dtls c where a.booking_no=b.booking_no and b.id=c.wo_trim_booking_dtls_id and b.booking_no=c.booking_no and a.company_id=$cbo_company and a.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  ";

			//echo $all_order_id;die;

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
			//echo $sql_bookingqty;
			$sql_bookingqty_result=sql_select($sql_bookingqty);
			foreach($sql_bookingqty_result as $row)
			{
				if($row[csf('item_size')]=="" || $row[csf('item_size')]=="0") $item_size=0; else $item_size=$row[csf('item_size')];
				$wo_qty_ArrayRecv[$row[csf('po_id')]][$row[csf('item_group')]][$row[csf('item_color')]][$item_size]+=$row[csf('wo_qnty')];
				$wo_qty_ArrayRecvAmt[$row[csf('po_id')]][$row[csf('item_group')]][$row[csf('item_color')]][$item_size]+=$row[csf('amount')];
			}

			$issue_qty_sql="select b.po_breakdown_id, a.item_color as item_color_id, a.item_size, a.item_group_id, b.quantity, c.cons_rate as rate, b.entry_form, b.trans_type
			from product_details_master a, order_wise_pro_details b, inv_transaction c
			where a.id=b.prod_id and b.trans_id=c.id and item_category_id=4 and b.entry_form in(25,49,73,78,112) and b.trans_type in(2,3,4,5,6) and c.transaction_type in(2,3,4,5,6) and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
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
			//echo $issue_qty_sql;//die;
			$issue_qty_sql_result=sql_select($issue_qty_sql);
			$issue_data_arr=array();
			foreach($issue_qty_sql_result as $row)
			{
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
				if(($row[csf('entry_form')]==78 || $row[csf('entry_form')]==112) && $row[csf('trans_type')]==5)
				{
					$issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][$row[csf('item_color_id')]][$item_size_id]["transfer_in_quantity"]+=$row[csf('quantity')];
				}
				if(($row[csf('entry_form')]==78 || $row[csf('entry_form')]==112) && $row[csf('trans_type')]==6)
				{
					$issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][$row[csf('item_color_id')]][$item_size_id]["transfer_out_quantity"]+=$row[csf('quantity')];
				}

			}

			//echo $test_data."<pre>"; print_r($dataArrayRecv);die;
			$req_check=array();
			foreach ($result as $row)
			{
				$x=0; $z=0; $dataArray=array(); $uomArray=array(); $rowspan_array=array(); $rowspan_color_array=array();
				$job_po_id=explode(",",$row[csf('po_id')]);
				//echo $test_data."<pre>"; print_r($job_po_id);die;
				foreach($job_po_id as $po_id)
				{
					$dataRecv=explode("**",chop($dataArrayRecv[$po_id],"**"));
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
						$item_color_temp=$recvRow[12];
                        $prod_id=$recvRow[13];
						if($item_color_temp>0) $item_color_temp=$item_color_temp; else $item_color_temp=$item_color;

						if($quantity>0)
						{
							if($dataArray[$item_group_id][$item_color_temp][$item_size]['qty']=="")
							{
								$rowspan_array[$item_group_id]+=1;
								$rowspan_color_array[$item_group_id][$item_color_temp]+=1;
								$z++;
							}
							//if($item_group_id==333) $test_data.="$po_id==$item_color==$item_color_temp==$quantity ___";
							$dataArray[$item_group_id][$item_color_temp][$item_size]['qty']+=$quantity;
							$dataArray[$item_group_id][$item_color_temp][$item_size]['val']+=$recv_value;
							$dataArray[$item_group_id][$item_color_temp][$item_size]['item_color'].=$item_color.",";
							$uomArray[$item_group_id]=$order_uom;
							//$storeArray[$item_group_id]=$store_name_id;
							($descriptionArray[$item_group_id] =="")? $descriptionArray[$item_group_id]=$item_description:$descriptionArray[$item_group_id].=",".$item_description;
							if($dohArray[$item_group_id] =="") {
                                $daysOnHand = datediff("d",$date_array[$prod_id]['MAX_DATE'],date("Y-m-d"));
                                $dohArray[$item_group_id] = $daysOnHand;
                            }else{
                                $daysOnHand = datediff("d",$date_array[$prod_id]['MAX_DATE'],date("Y-m-d"));
                                $dohArray[$item_group_id].=",".$daysOnHand;
                            }
							($storeArray[$item_group_id] =="")? $storeArray[$item_group_id]=$store_name_arr[$store_name_id]:$storeArray[$item_group_id].=",".$store_name_arr[$store_name_id];
						}
					}
				}

				if($z>0)
				{
					foreach($dataArray as $item_group_id=>$item_group_data)
					{
						$s=0;
						foreach($item_group_data as $item_color_id=>$item_color_data)
						{
							$c=0;
							foreach($item_color_data as $item_size=>$item_size_data)
							{
								if($item_size=='')$item_size=0;
								$recv_qnty=$item_size_data['qty'];
								$recv_value=$item_size_data['val'];
								$item_color_arr=array_unique(explode(",",chop($item_size_data['item_color'],",")));
								$item_color=implode(",",$item_color_arr);
								$ord_avg_rate=$recv_value/$recv_qnty;

								//$issue_qty=$issue_amount=0;$wo_qty=$wo_amount=0;
								$issue_qty=$issue_amount=$rcv_rtn_qty=$issue_rtn_qty=$transfer_in_qty=$transfer_out_qty=$net_recv_qnty=$net_recv_value=$net_issue_qnty=$net_issue_value=$wo_qty=$wo_amount=0;
								foreach($job_po_id as $po_id)
								{
									//$issue_qty+=$issue_qty_arr[$po_id][$item_group_id][$item_color_id][$item_size];
									//$issue_rate=$issue_qty_rate_arr[$po_id][$item_group_id][$item_color_id][$item_size];
									//$issue_amount+=$issue_qty_arr[$po_id][$item_group_id][$item_color_id][$item_size]*$issue_rate;
									foreach($item_color_arr as $c_id)
									{
										//echo $c_id."++";
										$issue_rate=$issue_qty_rate_arr[$po_id][$item_group_id][$c_id][$item_size]["rate"];
										$issue_qty+=$issue_data_arr[$po_id][$item_group_id][$c_id][$item_size]["issue_quantity"];
										$rcv_rtn_qty+=$issue_data_arr[$po_id][$item_group_id][$c_id][$item_size]["rcv_rtn_quantity"];
										$issue_rtn_qty+=$issue_data_arr[$po_id][$item_group_id][$c_id][$item_size]["issue_rtn_quantity"];
										$transfer_in_qty+=$issue_data_arr[$po_id][$item_group_id][$c_id][$item_size]["transfer_in_quantity"];
										$transfer_out_qty+=$issue_data_arr[$po_id][$item_group_id][$c_id][$item_size]["transfer_out_quantity"];
									}

									$issue_amount+=$issue_qty*$issue_rate;

									if($without_order==0)
									{
										$wo_qty+=$wo_qty_ArrayRecv[$po_id][$item_group_id][$item_color_id][$item_size];
										$wo_amount+=$wo_qty_ArrayRecvAmt[$po_id][$item_group_id][$item_color_id][$item_size];
										//$wo_qty_ArrayRecvAmt[$row[csf('po_id')]][$row[csf('item_group')]][$row[csf('item_color')]][$item_size]
									}
									else
									{
										$wo_qty+=$wo_qty_ArrayRecv[$item_group_id][$gmts_color][$item_color_id];
										$wo_amount+=$wo_qty_ArrayRecvAmt[$item_group_id][$gmts_color][$item_color_id];
									}

								}

								$net_recv_qnty=$recv_qnty+$transfer_in_qty-$rcv_rtn_qty;
								$net_recv_value=$net_recv_qnty*$ord_avg_rate;
								$net_issue_qnty=$issue_qty+$transfer_out_qty-$issue_rtn_qty;
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
                                        <td width="70" rowspan="<? echo $z; ?>"><p><? echo $season_arr[$row[csf('season_buyer_wise')]]; ?></p></td>
										<td width="100" rowspan="<? echo $z; ?>"><p><? echo $row[csf('job_no')]; ?></p></td>
										<td width="110" rowspan="<? echo $z; ?>"><p><a href='#report_details' onClick="openmypage_budge('<? echo $row[csf('job_no')]; ?>','<? echo $cbo_company; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('style_ref_no')]; ?>','<? echo change_date_format($row[csf('costing_date')]); ?>','<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('costing_per')]; ?>','accessories_details2');"><? echo $row[csf('style_ref_no')]; ?></a></p></td>
										<td width="140" rowspan="<? echo $z; ?>"><p><? echo implode(",",array_unique(explode(",",$row[csf('po_number')]))); ?></p></td>
										<td width="70" rowspan="<? echo $z; ?>"><p><? echo implode(",",array_unique(explode(",",$row[csf('file_no')]))); ?></p></td>
										<td width="80" rowspan="<? echo $z; ?>"><p><? echo implode(",",array_unique(explode(",",$row[csf('grouping')]))); ?></p></td>
										<td width="90" align="right" rowspan="<? echo $z; ?>"><? echo $row[csf('po_quantity')]; ?></td>
										<td width="80" align="right" rowspan="<? echo $z; ?>"><? echo number_format($row[csf('po_quantity')]/12,2); ?></td>
									<?
									}

									if($s==0)
									{
										?>
										<td width="150" rowspan="<? echo $rowspan_array[$item_group_id]; ?>">
											<p>
											<?
												echo implode(",",array_filter(array_unique(explode(",",$storeArray[$item_group_id]))));

											?>
											</p>
										</td>
										<td width="130" rowspan="<? echo $rowspan_array[$item_group_id]; ?>" title="<? echo $item_group_id; ?>"><p><? echo $trim_group[$item_group_id]; ?></p></td>
										<td width="150" rowspan="<? echo $rowspan_array[$item_group_id]; ?>"><p><? echo implode(",",array_filter(array_unique(explode(",",$descriptionArray[$item_group_id])))); ?></p></td>
										<td width="60" align="center" rowspan="<? echo $rowspan_array[$item_group_id]; ?>"><p><? echo $unit_of_measurement[$uomArray[$item_group_id]]; ?></p></td>
                                        <td width="90" align="right" rowspan="<? echo $rowspan_array[$item_group_id]; ?>">
										<?
                                        /*if($req_check[$row[csf('po_id')]][$item_group_id]=="")
                                        {
                                            $req_check[$row[csf('po_id')]][$item_group_id]=$row[csf('po_id')]."**".$item_group_id;
                                            echo number_format($trims_costing_arr[$row[csf('po_id')]][$item_group_id],2);
                                            $total_required_qnty+=$trims_costing_arr[$row[csf('po_id')]][$item_group_id];
                                        }*/

										foreach($job_po_id as $po_id)
										{
											if($req_check[$po_id][$item_group_id]=="")
											{
												$req_check[$po_id][$item_group_id]=$po_id."**".$item_group_id;
												$req_qnty+=$trims_costing_arr[$po_id][$item_group_id];
												//echo number_format($trims_costing_arr[$row[csf('po_id')]][$item_group_id],2);
												//echo $po_id.'='.$item_group_id.'<br>';

											}
										}

										echo number_format($req_qnty,2);$total_required_qnty+=$req_qnty;  $req_qnty=0;

                                        ?></td>
										<?
									}
									if($c==0)
									{
										?>
										<td width="100" rowspan="<? echo $rowspan_color_array[$item_group_id][$item_color_id]; ?>" title="<? echo $item_color_id; ?>"><p><? echo $color_library[$item_color_id]; ?></p></td>
										<?
									}
									?>
									<td width="60"><p><? if($item_size=="0") echo "&nbsp;"; else echo $item_size; ?></p></td>
									<td width="80" align="right" title="<? echo "po id==". $row[csf('po_id')]."item group id==".$item_group_id."item color id==".$item_color_id."item size id==".$item_size; ?>"><a href='#report_details' onClick="openmypage('<? echo $row[csf('po_id')]; ?>','<? echo $item_group_id; ?>','<? echo $item_color_id; ?>','<? echo $gmts_color; ?>','<? echo $item_size; ?>','<? echo $item_desc; ?>','<? echo $recv_basis; ?>','<? echo $without_order; ?>',1,'wo_receive_popup');"><?  echo number_format($wo_qty,2); ?></a></td>
                                    <td width="80" align="right"><?  echo number_format($wo_amount,2); ?></td>

									<td width="90" align="right" title="<? echo "rcv=".$recv_qnty." recv rtn=".$rcv_rtn_qty." trans In=".$transfer_in_qty ?>"><a href='#report_details' onClick="openmypage('<? echo $row[csf('po_id')]; ?>','<? echo $item_group_id; ?>','<? echo $item_color_id; ?>','<? echo $gmts_color; ?>','<? echo $item_size; ?>','<? echo $item_desc; ?>','<? echo $recv_basis; ?>','<? echo $without_order; ?>',2,'receive_popup');"><?  echo number_format($net_recv_qnty,2); ?></a> </td>
									<td width="80" align="right"><? echo number_format($wo_qty-$net_recv_qnty,2); ?></td>
									<td width="100" align="right"><? echo number_format($net_recv_value,2); ?></td>
									<td width="90" align="right" title="<? echo "issue=".$issue_qty." issue rtn=".$issue_rtn_qty." transfer out=".$transfer_out_qty;?>"> <a href='#report_details' onClick="openmypage('<? echo $row[csf('po_id')]; ?>','<? echo $item_group_id; ?>','<? echo $item_color_id; ?>','<? echo $gmts_color; ?>','<? echo $item_size; ?>','<? echo $item_desc; ?>','<? echo $recv_basis; ?>','<? echo $without_order; ?>',3,'issue_popup');"><?  echo number_format($net_issue_qnty,2); ?></a></td>
                                    <td width="80" align="right"><?  echo number_format($net_issue_value,2); ?></td>
									<td width="100" align="right"><? $left_over=$net_recv_qnty-$net_issue_qnty;echo number_format($left_over,2); ?></td>
									<td width="60" align="right"><?  echo number_format($ord_avg_rate,2); ?></td>
									<td align="right" width="100">
									<?
										//$tot_left_val=$left_over*$cons_rate;
										$tot_left_val=$left_over*$ord_avg_rate;
										echo number_format($tot_left_val,2);
									?>
                                    </td>
                                    <td align="right"><? echo implode(",",array_filter(array_unique(explode(",",$dohArray[$item_group_id])))); ?></td>
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
                        <td width="70"><p><? echo $season_arr[$row[csf('season_buyer_wise')]]; ?></p></td>
						<td width="100"><p><? echo $row[csf('job_no')]; ?></p></td>
						<td width="110"><p><a href='#report_details' onClick="openmypage_budge('<? echo $row[csf('job_no')]; ?>','<? echo $cbo_company; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('style_ref_no')]; ?>','<? echo change_date_format($row[csf('costing_date')]); ?>','<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('costing_per')]; ?>','accessories_details2');"><? echo $row[csf('style_ref_no')]; ?></a></p></td>
						<td width="140"><p><? echo implode(",",array_unique(explode(",",$row[csf('po_number')])));  ?></p></td>
						<td width="70"><p><?  echo implode(",",array_unique(explode(",",$row[csf('file_no')])));  ?></p></td>
						<td width="80"><p><?  echo implode(",",array_unique(explode(",",$row[csf('grouping')])));  ?></p></td>
						<td width="90" align="right"><? echo $row[csf('po_quantity')]; ?></td>
						<td width="80" align="right"><? echo number_format($row[csf('po_quantity')]/12,2); ?></td>
						<td width="150">&nbsp;</td>
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
						<td align="right" width="100">&nbsp;</td>
						<td align="right">&nbsp;</td>
					</tr>
					<?
					$i++;
				}
				$y++;
			}
			?>
			<tr class="tbl_bottom">
				<td colspan="8" align="right">Total</td>
				<td align="right"><? echo number_format($total_order_qty,2); ?></td>
				<td colspan="5" align="right"></td>
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
                <td>&nbsp;</td>

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
    echo "$html**$filename**3";
    exit();
}
// Style Wise Search.
if ($action=="report_generate_style2")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company=str_replace("'","",$cbo_company_id);
	$cbo_buyer=str_replace("'","",$cbo_buyer_id);
	$cbo_season_name = str_replace("'","",$cbo_season_name);
	$all_style=str_replace("'","",$txt_style);
	$all_style=implode(",",array_unique(explode(",",$all_style)));
	$txt_order_no=str_replace("'","",$txt_order_no);
	$txt_order_no_id=str_replace("'","",$txt_order_no_id);
	$txt_file_no=str_replace("'","",$txt_file_no);
	$txt_ref_no=str_replace("'","",$txt_ref_no);

	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);

	$all_style_no=explode(",",str_replace("'","",$txt_style_id));
	$all_style_quted="";
	foreach($all_style_no as $style_no)
	{
		$all_style_quted.="'".$style_no."'".",";
	}
	$all_style_quted=chop($all_style_quted,",");
	//echo $all_style_quted.jahid;die;
	
	
	$color_library=return_library_array("select id,color_name from lib_color", "id", "color_name");

	$cbo_store_name = str_replace("'","",$cbo_store_name);

	if($db_type==0)
	{
		$currency_convert_result=sql_select("select id, conversion_rate from currency_conversion_rate  where status_active=1 and currency=2 order by id desc LIMIT 1");
	}
	else
	{
		$currency_convert_result=sql_select("select * from (select a.id, a.conversion_rate, max(id) over () as max_pk from currency_conversion_rate a  where a.status_active=1 and a.currency=2) where id=max_pk");
	}
	$currency_convert_rate=$currency_convert_result[0][csf("conversion_rate")];


	//echo $all_style_quted;die;
	$sql_cond="";
	if($cbo_buyer>0)
	{
		$sql_cond=" and d.buyer_name=$cbo_buyer";
	}

	if($cbo_season_name>0)  $sql_cond.=" and d.season_buyer_wise=$cbo_season_name";


	if(str_replace("'","",$txt_order_no_id)!="")
	{
		$sql_cond .=" and c.id in(".str_replace("'","",$txt_order_no_id).")";
	}
	elseif($txt_order_no !="")
	{
		$po_number=trim($txt_order_no)."%";
		$sql_cond .=" and c.po_number like '$po_number'";
	}

	if($txt_file_no !="") $sql_cond .=" and c.file_no in($txt_file_no)";
	if($txt_ref_no !="") $sql_cond .=" and c.grouping='$txt_ref_no'";
	if($all_style !="") $sql_cond .=" and d.id in(".$all_style.")";

	if( $date_from!="" && $date_to!="")
	{
		$sql_cond .= " and c.pub_shipment_date between '".$date_from."' and '".$date_to."'";
	}

	if($cbo_store_name!=0) $store_cond=" and c.store_id =".str_replace("'","",$cbo_store_name); else $store_cond="";

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
	if(str_replace("'","",$txt_order_no_id)=='' && str_replace("'","",$txt_order_no)!='')
	{
		//if(str_replace("'","",$txt_order_no)!='')
		//{
			$condition->po_number("in('".$txt_order_no."')");
		//}
	}
	if(str_replace("'","",$txt_file_no) !='')
	{
		$condition->file_no("=$txt_file_no");
	}
	if(str_replace("'","",$txt_ref_no) !='')
	{
		$condition->grouping("='$txt_ref_no'");
	}
	if(str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!='')
	{
		$condition->pub_shipment_date(" between '$date_from' and '$date_to'");
	}
	$condition->init();

	$trims= new trims($condition);
	//echo $trims->getQuery();die;
	$trims_costing_arr=$trims->getQtyArray_by_orderAndItemid();
	//$trims_costing_arr=$trims->getQtyArray_by_orderItemidAndDescription();
	//getQtyArray_by_orderItemidAndDescription()
	//print_r($trims_costing_arr);die;

	//echo "<pre>";
	//print_r($trims_costing_arr);die;


	ob_start();
	?>
    <fieldset style="width:2500px;">
        <table width="2110">
            <tr class="form_caption">
                <td colspan="23" align="center" style="border:none;font-size:16px; font-weight:bold"> <? echo $report_title; ?></td>
            </tr>
            <tr class="form_caption">
                <td colspan="23" align="center"><? echo $company_library[$cbo_company]; ?></td>
            </tr>
        </table>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="2500" class="rpt_table" >
			<thead>
				<th width="40">SL </th>
                <th width="70">Buyer</th>
                <th width="70">Season</th>
                <th width="100">Job No</th>
                <th width="110">Style</th>
                <th width="140">Order No</th>
                <th width="70">File No</th>
                <th width="80">Ref. No</th>
                <th width="90">Order Qty.</th>
                <th width="80">Order Qty.(Dzn)</th>
                <th width="150">Store</th>
                <th width="130">Item Group</th>
                <th width="150">Item Description</th>
                <th width="60">UOM</th>
                <th width="90">Req. Qty</th>
                <th width="100">Item Color</th>
                <th width="60">Item Size</th>
                <th width="80">WO. Qty</th>
                <th width="80">WO. Value (TK)</th>
                <th width="90">Recv. Qty</th>
                <th width="80">Recv. Bal.</th>
                <th width="100">Recv. Value (TK)</th>
                <th width="90">Issue Qty.</th>
                <th width="80">Issue Value (TK)</th>
                <th width="100">Left Over</th>
                <th width="60">Rate (TK)</th>
                <th>Left Over Value (TK)</th>
            </thead>
        </table>
        <div style="width:2500px; overflow-y:scroll; max-height:350px;font-size:12px; " id="scroll_body">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="2480" class="rpt_table" id="tbl_issue_status" >
		    <?

		    $store_name_arr = return_library_array("select a.id, a.store_name from lib_store_location a", 'id', 'store_name');
			$season_arr = return_library_array("select a.id, a.season_name from lib_buyer_season a", 'id', 'season_name');

			$i=1; $y=1; $tot_receive_qty=0; $tot_receive_value=0; $tot_issue_qty=0; $total_left_over=0; $total_left_over_balance=0; $dataArrayRecv=array();$wo_qty_ArrayRecv=array();$total_wo_qty=0;$total_wo_val=$total_wo_amount=$total_issue_amount=0;

			if($db_type==0) $null_val=" IFNULL(a.item_color, 0) as item_color, IFNULL(a.item_size, 0) as item_size";
			else if($db_type==2) $null_val=" nvl(a.item_color,0) as item_color, nvl(a.item_size,0) as item_size";

			$sql_bookingqty ="SELECT d.buyer_name, d.season_buyer_wise, d.job_no, d.style_ref_no, d.total_set_qnty, c.id as po_id, c.po_number, c.file_no, c.grouping as int_ref_no, c.po_quantity, b.trim_group, b.booking_no, b.uom, a.description, $null_val, a.cons, a.amount, p.currency_id
			from  wo_booking_mst p, wo_trim_book_con_dtls a, wo_booking_dtls b, wo_po_break_down c, wo_po_details_master d
			where p.booking_no=a.booking_no and p.booking_no=b.booking_no and a.wo_trim_booking_dtls_id=b.id and b.po_break_down_id=c.id and c.job_no_mst=d.job_no and d.company_name=$cbo_company and b.booking_type=2 and a.cons>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sql_cond order by d.job_no";

			//echo $sql_bookingqty;die;

			$sql_bookingqty_result=sql_select($sql_bookingqty);
			//print_r($sql_bookingqty_result);
			$details_data=array();
			foreach($sql_bookingqty_result as $row)
			{
				$details_data[$row[csf("job_no")]][1][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("item_color")]][$row[csf("item_size")]]["job_no"]=$row[csf("job_no")];
				$details_data[$row[csf("job_no")]][1][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("item_color")]][$row[csf("item_size")]]["buyer_name"]=$row[csf("buyer_name")];
				$details_data[$row[csf("job_no")]][1][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("item_color")]][$row[csf("item_size")]]["season_buyer_wise"]=$row[csf("season_buyer_wise")];
				$details_data[$row[csf("job_no")]][1][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("item_color")]][$row[csf("item_size")]]["style_ref_no"]=$row[csf("style_ref_no")];

				if($order_check[$row[csf("po_id")]]=="")
				{
					$order_check[$row[csf("po_id")]]=$row[csf("po_id")];
					$all_order_id.=$row[csf("po_id")].",";

					$details_data[$row[csf("job_no")]][1][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("item_color")]][$row[csf("item_size")]]["po_quantity"]+=$row[csf("po_quantity")]*$row[csf("total_set_qnty")];
				}

				$details_data[$row[csf("job_no")]][1][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("item_color")]][$row[csf("item_size")]]["po_id"].=$row[csf("po_id")].",";
				$details_data[$row[csf("job_no")]][1][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("item_color")]][$row[csf("item_size")]]["po_number"].=$row[csf("po_number")].",";
				$details_data[$row[csf("job_no")]][1][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("item_color")]][$row[csf("item_size")]]["file_no"].=$row[csf("file_no")].",";
				$details_data[$row[csf("job_no")]][1][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("item_color")]][$row[csf("item_size")]]["int_ref_no"].=$row[csf("int_ref_no")].",";

				$details_data[$row[csf("job_no")]][1][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("item_color")]][$row[csf("item_size")]]["trim_group"]=$row[csf("trim_group")];
				$details_data[$row[csf("job_no")]][1][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("item_color")]][$row[csf("item_size")]]["uom"]=$row[csf("uom")];
				$details_data[$row[csf("job_no")]][1][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("item_color")]][$row[csf("item_size")]]["booking_no"]=$row[csf("booking_no")];
				$details_data[$row[csf("job_no")]][1][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("item_color")]][$row[csf("item_size")]]["description"]=$row[csf("description")];
				$details_data[$row[csf("job_no")]][1][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("item_color")]][$row[csf("item_size")]]["item_color"]=$row[csf("item_color")];
				$details_data[$row[csf("job_no")]][1][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("item_color")]][$row[csf("item_size")]]["item_size"]=$row[csf("item_size")];
				$details_data[$row[csf("job_no")]][1][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("item_color")]][$row[csf("item_size")]]["book_qnty"]+=$row[csf("cons")];
				if($row[csf("currency_id")]==1)
				{
					$details_data[$row[csf("job_no")]][1][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("item_color")]][$row[csf("item_size")]]["book_amt"]+=$row[csf("amount")];
				}
				else
				{
					$details_data[$row[csf("job_no")]][1][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("item_color")]][$row[csf("item_size")]]["book_amt"]+=$row[csf("amount")]*$currency_convert_rate;
				}
			}
			//echo "<pre>";print_r($details_data);die;
			$all_order_id=chop($all_order_id,",");
			if($db_type==0) $null_val=" IFNULL(a.item_color, 0) as item_color, IFNULL(a.item_size, 0) as item_size";
			else if($db_type==2) $null_val=" nvl(a.item_color,0) as item_color, nvl(a.item_size,0) as item_size";
			
			$general_item_issue_sql="SELECT d.buyer_name, d.season_buyer_wise, d.job_no, d.style_ref_no, d.total_set_qnty, c.id as po_id, c.po_number, c.file_no, c.grouping as int_ref_no, c.po_quantity, a.item_group_id as trim_group, a.unit_of_measure as uom, a.item_description as description, b.store_id, a.id as prod_id, $null_val, b.cons_quantity, b.cons_amount
			from product_details_master a, inv_transaction b, wo_po_break_down c, wo_po_details_master d
			where a.id=b.prod_id and b.order_id=c.id and c.job_no_mst=d.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form<>24 and b.transaction_type in(2,3,6) and b.item_category in(".implode(",",array_keys($general_item_category)).")";
			// echo $general_item_issue_sql;die;    
			$p=1;
			if($all_order_id!="")
			{
				$all_order_id_arr=array_chunk(array_unique(explode(",",$all_order_id)),999);
				foreach($all_order_id_arr as $order_id)
				{
					if($p==1) $general_item_issue_sql .=" and (c.id in(".implode(',',$order_id).")"; else $issue_qty_sql .=" or c.id  in(".implode(',',$order_id).")";
					$p++;
				}
				$general_item_issue_sql .=" )";
			}
			//echo $general_item_issue_sql;die;
			$general_item_issue_result=sql_select($general_item_issue_sql);
			
			foreach($general_item_issue_result as $row)
			{
				$details_data[$row[csf("job_no")]][2][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("item_color")]][$row[csf("item_size")]]["job_no"]=$row[csf("job_no")];
				$details_data[$row[csf("job_no")]][2][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("item_color")]][$row[csf("item_size")]]["buyer_name"]=$row[csf("buyer_name")];
				$details_data[$row[csf("job_no")]][2][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("item_color")]][$row[csf("item_size")]]["season_buyer_wise"]=$row[csf("season_buyer_wise")];
				$details_data[$row[csf("job_no")]][2][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("item_color")]][$row[csf("item_size")]]["style_ref_no"]=$row[csf("style_ref_no")];
				$details_data[$row[csf("job_no")]][2][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("item_color")]][$row[csf("item_size")]]["po_id"].=$row[csf("po_id")].",";
				$details_data[$row[csf("job_no")]][2][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("item_color")]][$row[csf("item_size")]]["po_number"].=$row[csf("po_number")].",";
				$details_data[$row[csf("job_no")]][2][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("item_color")]][$row[csf("item_size")]]["file_no"].=$row[csf("file_no")].",";
				$details_data[$row[csf("job_no")]][2][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("item_color")]][$row[csf("item_size")]]["int_ref_no"].=$row[csf("int_ref_no")].",";

				$details_data[$row[csf("job_no")]][2][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("item_color")]][$row[csf("item_size")]]["trim_group"]=$row[csf("trim_group")];
				$details_data[$row[csf("job_no")]][2][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("item_color")]][$row[csf("item_size")]]["uom"]=$row[csf("uom")];
				$details_data[$row[csf("job_no")]][2][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("item_color")]][$row[csf("item_size")]]["store_id"].=$row[csf("store_id")].",";
				$details_data[$row[csf("job_no")]][2][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("item_color")]][$row[csf("item_size")]]["prod_id"].=$row[csf("prod_id")].",";
				$details_data[$row[csf("job_no")]][2][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("item_color")]][$row[csf("item_size")]]["cons_quantity"]+=$row[csf("cons_quantity")];
				$details_data[$row[csf("job_no")]][2][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("item_color")]][$row[csf("item_size")]]["cons_amount"]+=$row[csf("cons_amount")];
			}
			

			//echo $all_order_id;die;

			/*$sql_recv="select c.receive_basis, c.booking_without_order as without_order, a.item_group_id, a.item_description, c.store_id, a.order_uom, a.item_color, a.item_size, a.cons_rate, b.po_breakdown_id as po_id, a.gmts_color_id, b.quantity, d.order_amount as amount, a.item_color_temp, a.trans_id, d.order_rate, c.currency_id
			from inv_receive_master c, inv_trims_entry_dtls a, order_wise_pro_details b, inv_transaction d
			where a.id=b.dtls_id and a.trans_id=b.trans_id and c.id=a.mst_id and b.trans_id=d.id and a.trans_id=d.id and b.trans_type=1 and b.entry_form=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $store_cond";

			//echo $sql_recv;die;
			$p=1;
			if($all_order_id !="" )
			{

				$all_order_id_arr=array_chunk(array_unique(explode(",",$all_order_id)),999);
				foreach($all_order_id_arr as $order_id)
				{
					if($p==1) $sql_recv .=" and (b.po_breakdown_id in(".implode(',',$order_id).")"; else $sql_sub_lc .=" or b.po_breakdown_id  in(".implode(',',$order_id).")";
					$p++;
				}
				$sql_recv .=" )";
			}
			//echo $sql_recv;die;
			$sql_recv_result=sql_select($sql_recv);
			foreach($sql_recv_result as $row)
			{

				if($row[csf('item_size')]=="") $item_size=0; else $item_size=$row[csf('item_size')];
				if($trans_data_check[$row[csf('trans_id')]][$row[csf('po_id')]]=="")
				{
					$trans_data_check[$row[csf('trans_id')]][$row[csf('po_id')]]=$row[csf('trans_id')];
					if($row[csf('currency_id')]==2)
					{
						$ord_amount=($row[csf('quantity')]*($row[csf('order_rate')]*$currency_convert_rate));
					}
					else
					{
						$ord_amount=$row[csf('quantity')]*$row[csf('order_rate')];
					}

					if($row[csf('item_color_temp')]>0) $item_color_temp=$row[csf('item_color_temp')]; else $item_color_temp=$row[csf('item_color')];
					if($issue_color_check[$row[csf('po_id')]][$row[csf('item_group_id')]][$item_size][$item_color_temp]=="")
					{
						$issue_color_check[$row[csf('po_id')]][$row[csf('item_group_id')]][$item_size][$item_color_temp]=$item_color_temp;
						$issue_color[$row[csf('po_id')]][$row[csf('item_group_id')]][$item_size].=$item_color_temp.",";
					}

					$dataArrayRecv[$row[csf('po_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]][$item_color_temp][$item_size]["store_id"].=$row[csf('store_id')].",";
					$dataArrayRecv[$row[csf('po_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]][$item_color_temp][$item_size]["item_description"].=$row[csf('item_description')].",";

					$dataArrayRecv[$row[csf('po_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]][$item_color_temp][$item_size]["item_group_id"]=$row[csf('item_group_id')];
					$dataArrayRecv[$row[csf('po_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]][$item_color_temp][$item_size]["order_uom"]=$row[csf('order_uom')];
					$dataArrayRecv[$row[csf('po_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]][$item_color_temp][$item_size]["item_color"]=$row[csf('item_color')];
					$dataArrayRecv[$row[csf('po_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]][$item_color_temp][$item_size]["item_color_temp"]=$row[csf('item_color_temp')];
					$dataArrayRecv[$row[csf('po_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]][$item_color_temp][$item_size]["item_size"]=$item_size;

					$dataArrayRecv[$row[csf('po_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]][$item_color_temp][$item_size]["quantity"]+=$row[csf('quantity')];
					$dataArrayRecv[$row[csf('po_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]][$item_color_temp][$item_size]["ord_amount"]+=$ord_amount;

				}
			}*/

			//echo "<pre>";print_r($details_data);die;
			// and a.entry_form=24
			$issue_qty_sql="SELECT b.po_breakdown_id, a.item_color as item_color_id, a.item_size, a.item_group_id, a.item_description, b.quantity, c.cons_rate as rate, b.entry_form, b.trans_type, c.store_id
			from product_details_master a, order_wise_pro_details b, inv_transaction c
			where a.id=b.prod_id and b.trans_id=c.id and item_category_id=4  and b.entry_form in(24,25,49,73,78,112) and b.trans_type in(1,2,3,4,5,6) and c.transaction_type in(1,2,3,4,5,6) and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $store_cond";
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
			//echo $issue_qty_sql;die;

			$store_to_store_sql="SELECT b.po_breakdown_id, a.item_color as item_color_id, a.item_size, a.item_group_id, a.item_description, b.quantity, c.cons_rate as rate, b.entry_form, b.trans_type, c.store_id, d.transfer_criteria
			from product_details_master a, order_wise_pro_details b, inv_transaction c, inv_item_transfer_mst d
			where a.id=b.prod_id and b.trans_id=c.id and d.id=c.mst_id and item_category_id=4 and a.entry_form=24 and b.entry_form in(112) and b.trans_type in(5,6) and c.transaction_type in(5,6) and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $store_cond and d.transfer_criteria=2";
			$q=1;
			if($all_order_id!="")
			{
				$all_order_id_arr=array_chunk(array_unique(explode(",",$all_order_id)),999);
				foreach($all_order_id_arr as $order_id)
				{
					if($q==1) $store_to_store_sql .=" and (b.po_breakdown_id in(".implode(',',$order_id).")"; else $store_to_store_sql .=" or b.po_breakdown_id  in(".implode(',',$order_id).")";
					$q++;
				}
				$store_to_store_sql .=" )";
			}
			$store_to_store_sql_result=sql_select($store_to_store_sql);
			$transfer_criteria_arr=array();
			foreach($store_to_store_sql_result as $rows)
			{
				if($rows[csf('item_size')]=="") $item_sizeId=0; else $item_sizeId=$rows[csf('item_size')];
				if($rows[csf('trans_type')]==5 || $rows[csf('trans_type')]==6)
				{
					$transfer_criteria_arr[$rows[csf('po_breakdown_id')]][$rows[csf('item_group_id')]][$rows[csf('item_description')]][$rows[csf('item_color_id')]][$item_sizeId]["transfer_criteria"]=$rows[csf('transfer_criteria')];
				}
			}
			/*echo '<pre>';
			print_r($transfer_criteria_arr);die;*/

			$issue_qty_sql_result=sql_select($issue_qty_sql);
			$issue_data_arr=array();
			foreach($issue_qty_sql_result as $row)
			{
				if($row[csf('item_size')]=="") $item_size_id=0; else $item_size_id=$row[csf('item_size')];
				
				if($row[csf('entry_form')]==24 && $row[csf('trans_type')]==1)
				{
					$issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][trim($row[csf('item_description')])][$row[csf('item_color_id')]][$item_size_id]["rcv_quantity"]+=$row[csf('quantity')];
					$issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][trim($row[csf('item_description')])][$row[csf('item_color_id')]][$item_size_id]["rcv_amt"]+=$row[csf('quantity')]*$row[csf('rate')];
				}

				if($row[csf('entry_form')]==25 && $row[csf('trans_type')]==2)
				{
					$issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][trim($row[csf('item_description')])][$row[csf('item_color_id')]][$item_size_id]["issue_quantity"]+=$row[csf('quantity')];
					$issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][trim($row[csf('item_description')])][$row[csf('item_color_id')]][$item_size_id]["issue_amt"]+=$row[csf('quantity')]*$row[csf('rate')];
				}
				if($row[csf('entry_form')]==49 && $row[csf('trans_type')]==3)
				{
					$issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][trim($row[csf('item_description')])][$row[csf('item_color_id')]][$item_size_id]["rcv_rtn_quantity"]+=$row[csf('quantity')];
					$issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][trim($row[csf('item_description')])][$row[csf('item_color_id')]][$item_size_id]["rcv_rtn_amt"]+=$row[csf('quantity')]*$row[csf('rate')];
				}
				if($row[csf('entry_form')]==73 && $row[csf('trans_type')]==4)
				{
					$issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][trim($row[csf('item_description')])][$row[csf('item_color_id')]][$item_size_id]["issue_rtn_quantity"]+=$row[csf('quantity')];
					$issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][trim($row[csf('item_description')])][$row[csf('item_color_id')]][$item_size_id]["issue_rtn_amt"]+=$row[csf('quantity')]*$row[csf('rate')];
				}
				if(($row[csf('entry_form')]==78 || $row[csf('entry_form')]==112) && $row[csf('trans_type')]==5)
				{
					if ($transfer_criteria_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][trim($row[csf('item_description')])][$row[csf('item_color_id')]][$item_size_id]["transfer_criteria"] != 2)
					{
						$issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][trim($row[csf('item_description')])][$row[csf('item_color_id')]][$item_size_id]["transfer_in_quantity"]+=$row[csf('quantity')];
						$issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][trim($row[csf('item_description')])][$row[csf('item_color_id')]][$item_size_id]["transfer_in_amt"]+=$row[csf('quantity')]*$row[csf('rate')];					
					}
				}
				if(($row[csf('entry_form')]==78 || $row[csf('entry_form')]==112) && $row[csf('trans_type')]==6)
				{
					if ($transfer_criteria_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][trim($row[csf('item_description')])][$row[csf('item_color_id')]][$item_size_id]["transfer_criteria"] != 2)
					{
						$issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][trim($row[csf('item_description')])][$row[csf('item_color_id')]][$item_size_id]["transfer_out_quantity"]+=$row[csf('quantity')];
						$issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][trim($row[csf('item_description')])][$row[csf('item_color_id')]][$item_size_id]["transfer_out_amt"]+=$row[csf('quantity')]*$row[csf('rate')];					
					}
				}
				if($store_check[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][trim($row[csf('item_description')])][$row[csf('item_color_id')]][$item_size_id][$row[csf('store_id')]]=="")
				{
					$store_check[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][trim($row[csf('item_description')])][$row[csf('item_color_id')]][$item_size_id][$row[csf('store_id')]]=$row[csf('store_id')];
					$issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][trim($row[csf('item_description')])][$row[csf('item_color_id')]][$item_size_id]["store_id"].=$row[csf('store_id')].",";
				}
			}
			//echo "<pre>"; print_r($issue_data_arr[8058][347]);die;
			foreach ($details_data as $job_no=>$job_data)
			{
				foreach($job_data[1] as $item_group_id=>$item_group_data)
				{
					foreach($item_group_data as $item_desc=>$item_desc_data)
					{
						foreach($item_desc_data as $item_color_id=>$item_color_data)
						{
							foreach($item_color_data as $item_size=>$item_size_data)
							{
								$job_row_span[$job_no][1]++;
								$job_gorup_row_span[$job_no][1][$item_group_id]++;
								$po_quantity[$item_size_data['job_no']] += $item_size_data[('po_quantity')];
							}
						}
					}
				}
				foreach($job_data[2] as $item_group_id=>$item_group_data)
				{
					foreach($item_group_data as $item_desc=>$item_desc_data)
					{
						foreach($item_desc_data as $item_color_id=>$item_color_data)
						{
							foreach($item_color_data as $item_size=>$item_size_data)
							{
								$job_row_span[$job_no][2]++;
								$job_gorup_row_span[$job_no][2][$item_group_id]++;
								//$po_quantity[$item_size_data['job_no']] += $item_size_data[('po_quantity')];
							}
						}
					}
				}
			}
			//echo "<pre>";print_r($details_data);die;

			$req_check=array();$i=1;$item_group_wise_data=array();$k=1;
			foreach ($details_data as $job_no=>$job_data)
			{
				//$job_row_span=count($job_data);
				$job_row_count=$job_row_span[$job_no][1];
				$job_row_count_general=$job_row_span[$job_no][2];
				//echo $job_row_span;die;
				foreach($job_data[1] as $item_group_id=>$item_group_data)
				{
					foreach($item_group_data as $item_desc=>$item_desc_data)
					{
						if($item_desc=='') $item_desc=0;
						foreach($item_desc_data as $item_color_id=>$item_color_data)
						{
							if($item_color_id=='') $item_color_id=0;
							foreach($item_color_data as $item_size=>$item_size_data)
							{
								if($item_size=='') $item_size=0;
								//echo $po_quantity;
								$all_po_id_arr=array_unique(explode(",",chop($item_size_data["po_id"],",")));
								$po_id_all=implode(",",$all_po_id_arr);
								$recv_qnty=$recv_value=$issue_qty=$issue_amount=$rcv_rtn_qty=$issue_rtn_qty=$transfer_in_qty=$transfer_out_qty=$net_recv_qnty=$net_recv_value=$net_issue_qnty=$net_issue_value=$wo_qty=$wo_amount=$req_qnty=$net_recv_value=$net_issue_value=$recv_amt=$issue_amt=$rcv_rtn_amt=$issue_rtn_amt=$transfer_in_amt=$transfer_out_amt=0;$all_store="";
								foreach($all_po_id_arr as $po_id)
								{
									/*$recv_qnty+=$dataArrayRecv[$po_id][$item_group_id][$item_desc][$item_color_id][$item_size]["quantity"];
									$recv_value+=$dataArrayRecv[$po_id][$item_group_id][$item_desc][$item_color_id][$item_size]["ord_amount"];*/
									$recv_qnty+=$issue_data_arr[$po_id][$item_group_id][trim($item_desc)][$item_color_id][$item_size]["rcv_quantity"];
									$recv_amt+=$issue_data_arr[$po_id][$item_group_id][trim($item_desc)][$item_color_id][$item_size]["rcv_amt"];
									
									$issue_qty+=$issue_data_arr[$po_id][$item_group_id][trim($item_desc)][$item_color_id][$item_size]["issue_quantity"];
									$issue_amt+=$issue_data_arr[$po_id][$item_group_id][trim($item_desc)][$item_color_id][$item_size]["issue_amt"];
									
									$rcv_rtn_qty+=$issue_data_arr[$po_id][$item_group_id][trim($item_desc)][$item_color_id][$item_size]["rcv_rtn_quantity"];
									$rcv_rtn_amt+=$issue_data_arr[$po_id][$item_group_id][trim($item_desc)][$item_color_id][$item_size]["rcv_rtn_amt"];
									
									$issue_rtn_qty+=$issue_data_arr[$po_id][$item_group_id][trim($item_desc)][$item_color_id][$item_size]["issue_rtn_quantity"];
									$issue_rtn_amt+=$issue_data_arr[$po_id][$item_group_id][trim($item_desc)][$item_color_id][$item_size]["issue_rtn_amt"];
									
									$transfer_in_qty+=$issue_data_arr[$po_id][$item_group_id][trim($item_desc)][$item_color_id][$item_size]["transfer_in_quantity"];
									//echo $po_id."==".$item_group_id."==".trim($item_desc)."==".$item_color_id."==".$item_size."==".$issue_data_arr[$po_id][$item_group_id][trim($item_desc)][$item_color_id][$item_size]["transfer_in_quantity"]."++";
									$transfer_in_amt+=$issue_data_arr[$po_id][$item_group_id][trim($item_desc)][$item_color_id][$item_size]["transfer_in_amt"];
									
									$transfer_out_qty+=$issue_data_arr[$po_id][$item_group_id][trim($item_desc)][$item_color_id][$item_size]["transfer_out_quantity"];
									$transfer_out_amt+=$issue_data_arr[$po_id][$item_group_id][trim($item_desc)][$item_color_id][$item_size]["transfer_out_amt"];

									$req_qnty+=$trims_costing_arr[$po_id][$item_group_id];


									$all_store_id=array_unique(explode(",",chop($issue_data_arr[$po_id][$item_group_id][trim($item_desc)][$item_color_id][$item_size]["store_id"],",")));
									foreach($all_store_id as $str_id)
									{
										$all_store.=$store_name_arr[$str_id].",";
									}
								}
								$all_store=implode(",",array_unique(explode(",",chop($all_store,","))));
								if($recv_amt>0 && $recv_qnty>0)
								{
									$ord_avg_rate=($recv_amt/$recv_qnty);
								}
								else
								{
									$ord_avg_rate=0;
								}
								$wo_qty=$item_size_data[('book_qnty')];
								$wo_amount=$item_size_data[('book_amt')];

								/*$net_recv_qnty=$recv_qnty+$issue_rtn_qty+$transfer_in_qty;
								$net_recv_value=$net_recv_qnty*$ord_avg_rate;
								$net_issue_qnty=$issue_qty+$rcv_rtn_qty+$transfer_out_qty;
								$net_issue_value=$net_issue_qnty*$ord_avg_rate;*/
								
								
								$net_recv_qnty=$recv_qnty+$transfer_in_qty-$rcv_rtn_qty;
								$net_recv_value=$recv_amt+$transfer_in_amt-$rcv_rtn_amt;
								$net_issue_qnty=$issue_qty+$transfer_out_qty-$issue_rtn_qty;
								$net_issue_value=$issue_amt+$transfer_out_amt-$issue_rtn_amt;

								if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor; ?>"  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<?
									if($job_check[$job_no][1]=="")
									{
										$job_check[$job_no][1]=$job_no;
										?>
										<td width="40" align="center" rowspan="<? echo $job_row_count; ?>"><? echo $k;?> </td>
										<td width="70" rowspan="<? echo $job_row_count; ?>" title="Trims Accessories"><p><? echo $buyer_arr[$item_size_data[('buyer_name')]]; ?></p></td>
										<td width="70" rowspan="<? echo $job_row_count; ?>" title="Trims Accessories"><p><? echo $season_arr[$item_size_data[('season_buyer_wise')]]; ?></p></td>
										<td width="100" rowspan="<? echo $job_row_count; ?>" title="Trims Accessories"><p><? echo $item_size_data[('job_no')]; ?></p></td>

										<?
										/*

										?>
										<a href='#report_details' onClick="openmypage_budge('<? echo $item_size_data[('job_no')]; ?>','<? echo $cbo_company; ?>','<? echo $item_size_data[('buyer_name')]; ?>','<? echo $item_size_data[('style_ref_no')]; ?>','<? echo change_date_format($item_size_data[('costing_date')]); ?>','<? echo $po_id_all; ?>','<? echo $item_size_data[('costing_per')]; ?>','accessories_details2');"></a>
										<?
										*/
										?>
										<td width="110" rowspan="<? echo $job_row_count; ?>" title="Trims Accessories"><p><? echo $item_size_data[('style_ref_no')]; ?></p></td>
										<td width="140" rowspan="<? echo $job_row_count; ?>"><p><? echo chop($item_size_data["po_number"],","); ?></p></td>
										<td width="70" rowspan="<? echo $job_row_count; ?>"><p><? echo implode(",",array_unique(explode(",",chop($item_size_data["file_no"],",")))); ?></p></td>
										<td width="80" rowspan="<? echo $job_row_count; ?>"><p><? echo implode(",",array_unique(explode(",",chop($item_size_data["int_ref_no"],",")))); ?></p></td>
										<td width="90" align="right" rowspan="<? echo $job_row_count; ?>"><? echo  $po_quantity[$item_size_data['job_no']]; ?></td>
										<td width="80" align="right" rowspan="<? echo $job_row_count; ?>"><? echo number_format($po_quantity[$item_size_data['job_no']]/12,2); ?></td>
										<?
									}
									?>
									<td width="150"><p><? echo $all_store;?></p></td>
									<td width="130" title="<? echo $item_group_id; ?>"><p><? echo $trim_group[$item_group_id]; ?></p></td>
									<td width="150"><p><? echo trim($item_desc); ?></p></td>
									<td width="60" align="center"><p><? echo $unit_of_measurement[$item_size_data[('uom')]]; ?></p></td>
                                    <?
									if($job_group_check[$job_no][1][$item_group_id]=="")
									{
										$job_group_check[$job_no][1][$item_group_id]=$job_no."==".$item_group_id;
										$item_group_wise_data[$item_group_id]["req_qnty"]+=$req_qnty;
										?>
                                        <td width="90" align="right"  rowspan="<? echo $job_gorup_row_span[$job_no][1][$item_group_id]; ?>"><? echo number_format($req_qnty,2);$total_required_qnty+=$req_qnty;?></td>
                                        <?
									}
									?>
									<td width="100" title="<? echo $item_color_id; ?>"><p><? echo $color_library[$item_color_id]; ?></p></td>
									<td width="60"><p><? if($item_size=="0") echo "&nbsp;"; else echo $item_size; ?></p></td>
									<td width="80" align="right" title="<? echo "po id==". $po_id_all."item group id==".$item_group_id."item description==".trim($item_desc)."item color id==".$item_color_id."item size id==".$item_size; ?>"><a href='#report_details' onClick="openmypage('<? echo $po_id_all; ?>','<? echo $item_group_id; ?>','<? echo $item_color_id; ?>','<? echo $gmts_color; ?>','<? echo $item_size; ?>','<? echo trim($item_desc); ?>','<? echo $recv_basis; ?>','<? echo $without_order; ?>',1,'wo_receive_popup');"><?  echo number_format($wo_qty,2); ?></a></td>
									<td width="80" align="right" title="<? echo "po id=". $po_id." item group id=".$item_group_id." item desc=".trim($item_desc)." item color id=".$item_color_id." item size id=".$item_size; ?>"><?  echo number_format($wo_amount,2); ?></td>
									<!-- $recv_qnty.'=='.$transfer_in_qty.'=='.$rcv_rtn_qty.'=='. -->
									<td width="90" align="right" title="<? echo "rcv=".$recv_qnty." iss rtn=".$issue_rtn_qty." trans In=".$transfer_in_qty ?>"><a href='#report_details' onClick="openmypage('<? echo $po_id_all; ?>','<? echo $item_group_id; ?>','<? echo $item_color_id; ?>','<? echo $gmts_color; ?>','<? echo $item_size; ?>','<? echo trim($item_desc); ?>','<? echo $recv_basis; ?>','<? echo $without_order; ?>',2,'receive_popup');"><?  echo number_format($recv_qnty,2); ?></a>  </td>
									<td width="80" align="right"><? echo number_format($wo_qty-$net_recv_qnty,2); ?></td>
									<td width="100" align="right"><? echo number_format($net_recv_value,2); ?></td>
									<td width="90" align="right"> <a href='#report_details' onClick="openmypage('<? echo $po_id_all; ?>','<? echo $item_group_id; ?>','<? echo $item_color_id; ?>','<? echo $gmts_color; ?>','<? echo $item_size; ?>','<? echo trim($item_desc); ?>','<? echo $recv_basis; ?>','<? echo $without_order; ?>',3,'issue_popup');"><?  echo number_format($net_issue_qnty,2); ?></a></td>
									<td width="80" align="right"><?  echo number_format($net_issue_value,2); ?></td>
									<td width="100" align="right"><? $left_over=$net_recv_qnty-$net_issue_qnty;echo number_format($left_over,2); ?></td>
									<td width="60" align="right"><?  echo number_format($ord_avg_rate,2); ?></td>
									<td align="right"><? $tot_left_val=$net_recv_value-$net_issue_value; echo number_format($tot_left_val,2);?></td>
								</tr>
								<?

								$item_group_wise_data[$item_group_id]["uom"]=$item_size_data[('uom')];
								$item_group_wise_data[$item_group_id]["wo_qty"]+=$wo_qty;
								$item_group_wise_data[$item_group_id]["recv_qnty"]+=$net_recv_qnty;
								$item_group_wise_data[$item_group_id]["issue_qnty"]+=$net_issue_qnty;

								$total_wo_qty+=$wo_qty;
								$total_wo_amount+=$wo_amount;
								$total_issue_amount+=$net_issue_value;
								$total_wo_val+=$wo_qty-$net_recv_qnty;
								$total_recv_qty+=$net_recv_qnty;
								$total_recv_value+=$net_recv_value;
								$total_issue_qty+=$net_issue_qnty;
								$total_left_val+=$tot_left_val;
								$total_left_over+=$left_over;
								$left_over=$tot_left_val=0;

								$i++;
							}
						}
					}
				}
				$k++;
				foreach($job_data[2] as $item_group_id=>$item_group_data)
				{
					foreach($item_group_data as $item_desc=>$item_desc_data)
					{
						if($item_desc=='') $item_desc=0;
						foreach($item_desc_data as $item_color_id=>$item_color_data)
						{
							foreach($item_color_data as $item_size=>$item_size_data)
							{
								if($item_size=='') $item_size=0;
								$all_store=$po_id_all=$prod_id_all="";
								
								$prod_id_all=implode(",",array_unique(explode(",",chop($item_size_data["prod_id"],","))));
								$po_id_all=implode(",",array_unique(explode(",",chop($item_size_data["po_id"],","))));
								$all_store_id=array_unique(explode(",",chop($item_size_data["store_id"],",")));
								foreach($all_store_id as $str_id)
								{
									$all_store.=$store_name_arr[$str_id].",";
								}
								$all_store=chop($all_store,",");
								if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor; ?>"  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<?
									if($job_check[$job_no][2]=="")
									{
										$job_check[$job_no][2]=$job_no;
										?>
										<td width="40" align="center" rowspan="<? echo $job_row_count_general; ?>"><? echo $k;?> </td>
										<td width="70" rowspan="<? echo $job_row_count_general; ?>" title="General Accessories"><p><? echo $buyer_arr[$item_size_data[('buyer_name')]]; ?></p></td>
										<td width="70" rowspan="<? echo $job_row_count_general; ?>" title="General Accessories"><p><? echo $season_arr[$item_size_data[('season_buyer_wise')]]; ?></p></td>
										<td width="100" rowspan="<? echo $job_row_count_general; ?>" title="General Accessories"><p><? echo $item_size_data[('job_no')]; ?></p></td>
										<td width="110" rowspan="<? echo $job_row_count_general; ?>" title="General Accessories"><p><? echo $item_size_data[('style_ref_no')]; ?></p></td>
										<td width="140" rowspan="<? echo $job_row_count_general; ?>"><p><? echo chop($item_size_data["po_number"],","); ?></p></td>
										<td width="70" rowspan="<? echo $job_row_count_general; ?>"><p><? echo implode(",",array_unique(explode(",",chop($item_size_data["file_no"],",")))); ?></p></td>
										<td width="80" rowspan="<? echo $job_row_count_general; ?>"><p><? echo implode(",",array_unique(explode(",",chop($item_size_data["int_ref_no"],",")))); ?></p></td>
										<td width="90" align="right" rowspan="<? echo $job_row_count_general; ?>"><? //echo $po_quantity[$item_size_data['job_no']]; ?></td>
										<td width="80" align="right" rowspan="<? echo $job_row_count_general; ?>"><? //echo number_format($po_quantity[$item_size_data['job_no']]/12,2); ?></td>
										<?
									}
									?>
									<td width="150"><p><? echo $all_store;?></p></td>
									<td width="130" title="<? echo $item_group_id; ?>"><p><? echo $trim_group[$item_group_id]; ?></p></td>
									<td width="150"><p><? echo $item_desc; ?></p></td>
									<td width="60" align="center"><p><? echo $unit_of_measurement[$item_size_data[('uom')]]; ?></p></td>
                                    <?
									if($job_group_check[$job_no][2][$item_group_id]=="")
									{
										$job_group_check[$job_no][2][$item_group_id]=$job_no."==".$item_group_id;
										//$item_group_wise_data[$item_group_id]["req_qnty"]+=$req_qnty;
										?>
                                        <td width="90" align="right"  rowspan="<? echo $job_gorup_row_span[$job_no][2][$item_group_id]; ?>"><? //echo number_format($req_qnty,2);$total_required_qnty+=$req_qnty;?></td>
                                        <?
									}

									?>
									<td width="100" title="<? echo $item_color_id; ?>"><p><? echo $color_library[$item_color_id]; ?></p></td>
									<td width="60"><p><? if($item_size=="0") echo "&nbsp;"; else echo $item_size; ?></p></td>
									<td width="80" align="right">&nbsp;</td>
									<td width="80" align="right">&nbsp;</td>

									<td width="90" align="right">&nbsp;</td>
									<td width="80" align="right">&nbsp;</td>
									<td width="100" align="right">&nbsp;</td>
									<td width="90" align="right"><a href='#report_details' onClick="openmypage('<? echo $po_id_all; ?>','<? echo $item_group_id; ?>','<? echo $item_color; ?>','<? echo $gmts_color; ?>','<? echo $item_size; ?>','<? echo $item_desc; ?>','<? echo $prod_id_all; ?>','<? echo $without_order; ?>',3,'issue_popup_general');"><?  echo number_format($item_size_data["cons_quantity"],2); ?></a></td>
									<td width="80" align="right"><?  echo number_format($item_size_data["cons_amount"],2); ?></td>
									<td width="100" align="right">&nbsp;</td>
									<td width="60" align="right">&nbsp;</td>
									<td align="right">&nbsp;</td>
								</tr>
								<?
								$i++;
								$total_issue_amount+=$item_size_data["cons_amount"];
							}
						}
					}
				}
				$k++;
			}
			?>
			<tr class="tbl_bottom">
				<td colspan="8" align="right">Total</td>
				<td align="right"><? echo number_format(array_sum($po_quantity),2); ?></td>
				<td colspan="5" align="right"></td>
				<td align="right"><? //echo number_format($total_required_qnty,2); ?></td>
                <td></td>
                <td></td>
				<td align="right"><? //echo number_format($total_wo_qty,2); ?></td>
                <td align="right"><? echo number_format($total_wo_amount,2); ?></td>
				<td align="right"><? //echo number_format($total_recv_qty,2); ?></td>
				<td align="right"><? //echo number_format($total_wo_val,2); ?></td>
				<td align="right"><? echo number_format($total_recv_value,2); ?></td>
				<td align="right"><? //echo number_format($total_issue_qty,2); ?></td>
                <td align="right"><? echo number_format($total_issue_amount,2); ?></td>
				<td align="right"><? //echo number_format($total_left_over,2); ?></td>
				<td>&nbsp;</td>
				<td align="right"><? echo number_format($total_left_val,2); ?></td>
			</tr>
		</table>
		</div>
        <br />
        <p style="text-align:left; padding-left:10px; font-size:16px; font-weight:bold">Summary Report</p>
        <br />
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1200" class="rpt_table" align="left">
        	<thead>
                <tr>
                    <th width="50">SL </th>
                    <th width="170">Item Group</th>
                    <th width="70">UOM</th>
                    <th width="100">Req Qty</th>
                    <th width="100">WO Qty</th>
                    <th width="100">WO %</th>
                    <th width="100">In-House Qty</th>
                    <th width="100">In-House %</th>
                    <th width="100">In-House Balance Qty</th>
                    <th width="100">Issue Qty</th>
                    <th width="100">Issue %</th>
                    <th>Left Over Qty</th>
                </tr>
            </thead>
            <tbody>
				<?
				$i=1;
                foreach($item_group_wise_data as $item_id=>$val)
                {
					$wo_qnty_percent=(($val["wo_qty"]/$val["req_qnty"])*100);
					$recv_qnty_percent=(($val["recv_qnty"]/$val["wo_qty"])*100);
					$recv_qnty_bal=$val["wo_qty"]-$val["recv_qnty"];
					$issue_qnty_percent=(($val["issue_qnty"]/$val["recv_qnty"])*100);
					$left_overs_qnty=$val["recv_qnty"]-$val["issue_qnty"];

					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td align="center"><? echo $i;?></td>
                        <td title="<? echo $item_id; ?>"><p><? echo $trim_group[$item_id] ; ?>&nbsp;</p></td>
                        <td align="right"><? echo $unit_of_measurement[$val["uom"]];?></td>
                        <td align="right"><? echo number_format($val["req_qnty"],2);?></td>
                        <td align="right"><? echo number_format($val["wo_qty"],2);?></td>
                        <td align="right"><? echo number_format($wo_qnty_percent,2);?></td>
                        <td align="right"><? echo number_format($val["recv_qnty"],2);?></td>
                        <td align="right"><? echo number_format($recv_qnty_percent,2);?></td>
                        <td align="right"><? echo number_format($recv_qnty_bal,2);?></td>
                        <td align="right"><? echo number_format($val["issue_qnty"],2);?></td>
                        <td align="right"><? echo number_format($issue_qnty_percent,2);?></td>
                        <td align="right"><? echo number_format($left_overs_qnty,2);?></td>
                    </tr>
					<?
                    $i++;
                }
                ?>
            </tbody>
            <tfoot>
            </tfoot>
        </table>
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
    echo "$html**$filename**5";
    exit();
}

if ($action=="report_generate_style4")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company=str_replace("'","",$cbo_company_id);
	$cbo_buyer=str_replace("'","",$cbo_buyer_id);
	$cbo_season_name = str_replace("'","",$cbo_season_name);
	$all_style=str_replace("'","",$txt_style);
	$all_style=implode(",",array_unique(explode(",",$all_style)));
	$txt_order_no=str_replace("'","",$txt_order_no);
	$txt_order_no_id=str_replace("'","",$txt_order_no_id);
	$txt_file_no=str_replace("'","",$txt_file_no);
	$txt_ref_no=str_replace("'","",$txt_ref_no);

	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);

	$all_style_no=explode(",",str_replace("'","",$txt_style_id));
	$all_style_quted="";
	foreach($all_style_no as $style_no)
	{
		$all_style_quted.="'".$style_no."'".",";
	}
	$all_style_quted=chop($all_style_quted,",");
	//echo $all_style_quted.jahid;die;
	
	
	$color_library=return_library_array("select id,color_name from lib_color", "id", "color_name");

	$cbo_store_name = str_replace("'","",$cbo_store_name);

	if($db_type==0)
	{
		$currency_convert_result=sql_select("select id, conversion_rate from currency_conversion_rate  where status_active=1 and currency=2 order by id desc LIMIT 1");
	}
	else
	{
		$currency_convert_result=sql_select("select * from (select a.id, a.conversion_rate, max(id) over () as max_pk from currency_conversion_rate a  where a.status_active=1 and a.currency=2) where id=max_pk");
	}
	$currency_convert_rate=$currency_convert_result[0][csf("conversion_rate")];


	//echo $all_style_quted;die;
	$sql_cond="";
	if($cbo_buyer>0)
	{
		$sql_cond=" and d.buyer_name=$cbo_buyer";
	}

	if($cbo_season_name>0)  $sql_cond.=" and d.season_buyer_wise=$cbo_season_name";


	if(str_replace("'","",$txt_order_no_id)!="")
	{
		$sql_cond .=" and c.id in(".str_replace("'","",$txt_order_no_id).")";
	}
	elseif($txt_order_no !="")
	{
		$po_number=trim($txt_order_no)."%";
		$sql_cond .=" and c.po_number like '$po_number'";
	}

	if($txt_file_no !="") $sql_cond .=" and c.file_no in($txt_file_no)";
	if($txt_ref_no !="") $sql_cond .=" and c.grouping='$txt_ref_no'";
	if($all_style !="") $sql_cond .=" and d.id in(".$all_style.")";

	if( $date_from!="" && $date_to!="")
	{
		$sql_cond .= " and c.pub_shipment_date between '".$date_from."' and '".$date_to."'";
	}

	if($cbo_store_name!=0) $store_cond=" and c.store_id =".str_replace("'","",$cbo_store_name); else $store_cond="";

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
	if(str_replace("'","",$txt_order_no_id)=='' && str_replace("'","",$txt_order_no)!='')
	{
		//if(str_replace("'","",$txt_order_no)!='')
		//{
			$condition->po_number("in('".$txt_order_no."')");
		//}
	}
	if(str_replace("'","",$txt_file_no) !='')
	{
		$condition->file_no("=$txt_file_no");
	}
	if(str_replace("'","",$txt_ref_no) !='')
	{
		$condition->grouping("='$txt_ref_no'");
	}
	if(str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!='')
	{
		$condition->pub_shipment_date(" between '$date_from' and '$date_to'");
	}
	$condition->init();

	$trims= new trims($condition);
	//echo $trims->getQuery();die;
	$trims_costing_arr=$trims->getQtyArray_by_orderAndItemid();
	//$trims_costing_arr=$trims->getQtyArray_by_orderItemidAndDescription();
	//getQtyArray_by_orderItemidAndDescription()
	//print_r($trims_costing_arr);die;

	//echo "<pre>";
	//print_r($trims_costing_arr);die;


	ob_start();
	?>
    <fieldset style="width:2500px;">
        <table width="2110">
            <tr class="form_caption">
                <td colspan="25" align="center" style="border:none;font-size:16px; font-weight:bold"> <? echo $report_title; ?></td>
            </tr>
            <tr class="form_caption">
                <td colspan="25" align="center"><? echo $company_library[$cbo_company]; ?></td>
            </tr>
        </table>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="2680" class="rpt_table" >
			<thead>
				<th width="40">SL </th>
                <th width="70">Buyer</th>
                <th width="70">Season</th>
                <th width="100">Job No</th>
                <th width="110">Style</th>
                <th width="140">Order No</th>
                <th width="70">File No</th>
                <th width="80">Ref. No</th>
                <th width="90">Order Qty.</th>
                <th width="80">Order Qty.(Dzn)</th>
                <th width="150">Store</th>
                <th width="130">Item Group</th>
                <th width="150">Item Description</th>
                <th width="60">UOM</th>
                <th width="90">Req. Qty</th>
                <th width="100">Item Color</th>
                <th width="60">Item Size</th>
                <th width="80">WO. Qty</th>
                <th width="80">WO. Value (TK)</th>
                <th width="90">Recv. Qty</th>
                <th width="90">Transfer in</th>
                <th width="80">Recv. Bal.</th>
                <th width="100">Recv. Value (TK)</th>
                <th width="90">Issue Qty.</th>
                <th width="90">Transfer Out</th>
                <th width="80">Issue Value (TK)</th>
                <th width="100">Left Over</th>
                <th width="60">Rate (TK)</th>
                <th>Left Over Value (TK)</th>
            </thead>
        </table>
        <div style="width:2680px; overflow-y:scroll; max-height:350px;font-size:12px; " id="scroll_body">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="2660" class="rpt_table" id="tbl_issue_status" >
		    <?

		    $store_name_arr = return_library_array("select a.id, a.store_name from lib_store_location a", 'id', 'store_name');
			$season_arr = return_library_array("select a.id, a.season_name from lib_buyer_season a", 'id', 'season_name');

			$i=1; $y=1; $tot_receive_qty=0; $tot_receive_value=0; $tot_issue_qty=0; $total_left_over=0; $total_left_over_balance=0; $dataArrayRecv=array();$wo_qty_ArrayRecv=array();$total_wo_qty=0;$total_wo_val=$total_wo_amount=$total_issue_amount=0;

			if($db_type==0) $null_val=" IFNULL(a.item_color, 0) as item_color, IFNULL(a.item_size, 0) as item_size";
			else if($db_type==2) $null_val=" nvl(a.item_color,0) as item_color, nvl(a.item_size,0) as item_size";

			$sql_bookingqty ="SELECT d.buyer_name, d.season_buyer_wise, d.job_no, d.style_ref_no, d.total_set_qnty, c.id as po_id, c.po_number, c.file_no, c.grouping as int_ref_no, c.po_quantity, b.trim_group, b.booking_no, b.uom, a.description, $null_val, a.cons, a.amount, p.currency_id
			from  wo_booking_mst p, wo_trim_book_con_dtls a, wo_booking_dtls b, wo_po_break_down c, wo_po_details_master d
			where p.booking_no=a.booking_no and p.booking_no=b.booking_no and a.wo_trim_booking_dtls_id=b.id and b.po_break_down_id=c.id and c.job_no_mst=d.job_no and d.company_name=$cbo_company and b.booking_type=2 and a.cons>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sql_cond order by d.job_no";

			//echo $sql_bookingqty;die;

			$sql_bookingqty_result=sql_select($sql_bookingqty);
			//print_r($sql_bookingqty_result);
			$details_data=array();
			foreach($sql_bookingqty_result as $row)
			{
				$details_data[$row[csf("job_no")]][1][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("item_color")]][$row[csf("item_size")]]["job_no"]=$row[csf("job_no")];
				$details_data[$row[csf("job_no")]][1][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("item_color")]][$row[csf("item_size")]]["buyer_name"]=$row[csf("buyer_name")];
				$details_data[$row[csf("job_no")]][1][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("item_color")]][$row[csf("item_size")]]["season_buyer_wise"]=$row[csf("season_buyer_wise")];
				$details_data[$row[csf("job_no")]][1][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("item_color")]][$row[csf("item_size")]]["style_ref_no"]=$row[csf("style_ref_no")];

				if($order_check[$row[csf("po_id")]]=="")
				{
					$order_check[$row[csf("po_id")]]=$row[csf("po_id")];
					$all_order_id.=$row[csf("po_id")].",";

					$details_data[$row[csf("job_no")]][1][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("item_color")]][$row[csf("item_size")]]["po_quantity"]+=$row[csf("po_quantity")]*$row[csf("total_set_qnty")];
				}

				$details_data[$row[csf("job_no")]][1][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("item_color")]][$row[csf("item_size")]]["po_id"].=$row[csf("po_id")].",";
				$details_data[$row[csf("job_no")]][1][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("item_color")]][$row[csf("item_size")]]["po_number"].=$row[csf("po_number")].",";
				$details_data[$row[csf("job_no")]][1][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("item_color")]][$row[csf("item_size")]]["file_no"].=$row[csf("file_no")].",";
				$details_data[$row[csf("job_no")]][1][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("item_color")]][$row[csf("item_size")]]["int_ref_no"].=$row[csf("int_ref_no")].",";

				$details_data[$row[csf("job_no")]][1][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("item_color")]][$row[csf("item_size")]]["trim_group"]=$row[csf("trim_group")];
				$details_data[$row[csf("job_no")]][1][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("item_color")]][$row[csf("item_size")]]["uom"]=$row[csf("uom")];
				$details_data[$row[csf("job_no")]][1][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("item_color")]][$row[csf("item_size")]]["booking_no"]=$row[csf("booking_no")];
				$details_data[$row[csf("job_no")]][1][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("item_color")]][$row[csf("item_size")]]["description"]=$row[csf("description")];
				$details_data[$row[csf("job_no")]][1][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("item_color")]][$row[csf("item_size")]]["item_color"]=$row[csf("item_color")];
				$details_data[$row[csf("job_no")]][1][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("item_color")]][$row[csf("item_size")]]["item_size"]=$row[csf("item_size")];
				$details_data[$row[csf("job_no")]][1][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("item_color")]][$row[csf("item_size")]]["book_qnty"]+=$row[csf("cons")];
				if($row[csf("currency_id")]==1)
				{
					$details_data[$row[csf("job_no")]][1][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("item_color")]][$row[csf("item_size")]]["book_amt"]+=$row[csf("amount")];
				}
				else
				{
					$details_data[$row[csf("job_no")]][1][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("item_color")]][$row[csf("item_size")]]["book_amt"]+=$row[csf("amount")]*$currency_convert_rate;
				}
			}
			//echo "<pre>";print_r($details_data);die;
			$all_order_id=chop($all_order_id,",");
			if($db_type==0) $null_val=" IFNULL(a.item_color, 0) as item_color, IFNULL(a.item_size, 0) as item_size";
			else if($db_type==2) $null_val=" nvl(a.item_color,0) as item_color, nvl(a.item_size,0) as item_size";
			
			$general_item_issue_sql="SELECT d.buyer_name, d.season_buyer_wise, d.job_no, d.style_ref_no, d.total_set_qnty, c.id as po_id, c.po_number, c.file_no, c.grouping as int_ref_no, c.po_quantity, a.item_group_id as trim_group, a.unit_of_measure as uom, a.item_description as description, b.store_id, a.id as prod_id, $null_val, b.cons_quantity, b.cons_amount
			from product_details_master a, inv_transaction b, wo_po_break_down c, wo_po_details_master d
			where a.id=b.prod_id and b.order_id=c.id and c.job_no_mst=d.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form<>24 and b.transaction_type in(2,3,6) and b.item_category in(".implode(",",array_keys($general_item_category)).")";
			// echo $general_item_issue_sql;die;    
			$p=1;
			if($all_order_id!="")
			{
				$all_order_id_arr=array_chunk(array_unique(explode(",",$all_order_id)),999);
				foreach($all_order_id_arr as $order_id)
				{
					if($p==1) $general_item_issue_sql .=" and (c.id in(".implode(',',$order_id).")"; else $issue_qty_sql .=" or c.id  in(".implode(',',$order_id).")";
					$p++;
				}
				$general_item_issue_sql .=" )";
			}
			//echo $general_item_issue_sql;die;
			$general_item_issue_result=sql_select($general_item_issue_sql);
			
			foreach($general_item_issue_result as $row)
			{
				$details_data[$row[csf("job_no")]][2][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("item_color")]][$row[csf("item_size")]]["job_no"]=$row[csf("job_no")];
				$details_data[$row[csf("job_no")]][2][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("item_color")]][$row[csf("item_size")]]["buyer_name"]=$row[csf("buyer_name")];
				$details_data[$row[csf("job_no")]][2][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("item_color")]][$row[csf("item_size")]]["season_buyer_wise"]=$row[csf("season_buyer_wise")];
				$details_data[$row[csf("job_no")]][2][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("item_color")]][$row[csf("item_size")]]["style_ref_no"]=$row[csf("style_ref_no")];
				$details_data[$row[csf("job_no")]][2][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("item_color")]][$row[csf("item_size")]]["po_id"].=$row[csf("po_id")].",";
				$details_data[$row[csf("job_no")]][2][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("item_color")]][$row[csf("item_size")]]["po_number"].=$row[csf("po_number")].",";
				$details_data[$row[csf("job_no")]][2][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("item_color")]][$row[csf("item_size")]]["file_no"].=$row[csf("file_no")].",";
				$details_data[$row[csf("job_no")]][2][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("item_color")]][$row[csf("item_size")]]["int_ref_no"].=$row[csf("int_ref_no")].",";

				$details_data[$row[csf("job_no")]][2][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("item_color")]][$row[csf("item_size")]]["trim_group"]=$row[csf("trim_group")];
				$details_data[$row[csf("job_no")]][2][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("item_color")]][$row[csf("item_size")]]["uom"]=$row[csf("uom")];
				$details_data[$row[csf("job_no")]][2][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("item_color")]][$row[csf("item_size")]]["store_id"].=$row[csf("store_id")].",";
				$details_data[$row[csf("job_no")]][2][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("item_color")]][$row[csf("item_size")]]["prod_id"].=$row[csf("prod_id")].",";
				$details_data[$row[csf("job_no")]][2][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("item_color")]][$row[csf("item_size")]]["cons_quantity"]+=$row[csf("cons_quantity")];
				$details_data[$row[csf("job_no")]][2][$row[csf("trim_group")]][$row[csf("description")]][$row[csf("item_color")]][$row[csf("item_size")]]["cons_amount"]+=$row[csf("cons_amount")];
			}

			if($db_type==0) $null_val=" IFNULL(a.item_color, 0) as item_color, IFNULL(a.item_size, 0) as item_size";
			else if($db_type==2) $null_val=" nvl(a.item_color,0) as item_color, nvl(a.item_size,0) as item_size";

			/*$item_mismatch_issue_sql="SELECT d.buyer_name, d.season_buyer_wise, d.job_no, d.style_ref_no, d.total_set_qnty, c.id as po_id, c.po_number, c.file_no, c.grouping as int_ref_no, c.po_quantity, a.item_group_id as trim_group, a.unit_of_measure as uom, a.item_description as description, b.store_id, a.id as prod_id, $null_val, b.cons_quantity, b.cons_amount
			from product_details_master a, inv_transaction b, wo_po_break_down c, wo_po_details_master d, order_wise_pro_details e 
			where  a.id=b.prod_id AND b.order_id=c.id AND c.job_no_mst=d.job_no and a.id=e.prod_id and b.id=e.trans_id and c.id=e.po_breakdown_id AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 AND b.transaction_type IN (5,6)";*/
			$item_mismatch_issue_sql="SELECT d.buyer_name, d.season_buyer_wise, d.job_no, d.style_ref_no, d.total_set_qnty, c.id as po_id, c.po_number, c.file_no, c.grouping as int_ref_no, c.po_quantity, a.item_group_id as trim_group, a.unit_of_measure as uom, a.item_description as description, b.store_id, a.id as prod_id, $null_val, b.cons_quantity, b.cons_amount
			from product_details_master a, inv_transaction b, wo_po_break_down c, wo_po_details_master d, order_wise_pro_details e
			where  a.id=b.prod_id AND b.order_id=c.id AND c.job_no_mst=d.job_no and a.id=e.prod_id and b.id=e.trans_id and c.id=e.po_breakdown_id AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 AND b.transaction_type IN (5,6)";
			//echo $item_mismatch_issue_sql;die;
			$q=1;
			if($all_order_id!="")
			{
				$all_order_id_arr=array_chunk(array_unique(explode(",",$all_order_id)),999);
				foreach($all_order_id_arr as $order_id)
				{
					if($q==1) $item_mismatch_issue_sql .=" and (c.id in(".implode(',',$order_id).")"; 
					//else $issue_qty_sql .=" or c.id  in(".implode(',',$order_id).")";
					$q++;
				}
				$item_mismatch_issue_sql .=" )";
			}
			//echo $item_mismatch_issue_sql;die;
			$item_mismatch_issue_result=sql_select($item_mismatch_issue_sql);
			
			foreach($item_mismatch_issue_result as $row)
			{
				$details_data[$row[csf("job_no")]][1][$row[csf("trim_group")]][chop($row[csf("description")],", [BS]")][$row[csf("item_color")]][$row[csf("item_size")]]["job_no"]=$row[csf("job_no")];
				$details_data[$row[csf("job_no")]][1][$row[csf("trim_group")]][chop($row[csf("description")],", [BS]")][$row[csf("item_color")]][$row[csf("item_size")]]["buyer_name"]=$row[csf("buyer_name")];
				$details_data[$row[csf("job_no")]][1][$row[csf("trim_group")]][chop($row[csf("description")],", [BS]")][$row[csf("item_color")]][$row[csf("item_size")]]["season_buyer_wise"]=$row[csf("season_buyer_wise")];
				$details_data[$row[csf("job_no")]][1][$row[csf("trim_group")]][chop($row[csf("description")],", [BS]")][$row[csf("item_color")]][$row[csf("item_size")]]["style_ref_no"]=$row[csf("style_ref_no")];
				$details_data[$row[csf("job_no")]][1][$row[csf("trim_group")]][chop($row[csf("description")],", [BS]")][$row[csf("item_color")]][$row[csf("item_size")]]["po_id"].=$row[csf("po_id")].",";
				$details_data[$row[csf("job_no")]][1][$row[csf("trim_group")]][chop($row[csf("description")],", [BS]")][$row[csf("item_color")]][$row[csf("item_size")]]["po_number"].=$row[csf("po_number")].",";
				$details_data[$row[csf("job_no")]][1][$row[csf("trim_group")]][chop($row[csf("description")],", [BS]")][$row[csf("item_color")]][$row[csf("item_size")]]["file_no"].=$row[csf("file_no")].",";
				$details_data[$row[csf("job_no")]][1][$row[csf("trim_group")]][chop($row[csf("description")],", [BS]")][$row[csf("item_color")]][$row[csf("item_size")]]["int_ref_no"].=$row[csf("int_ref_no")].",";

				$details_data[$row[csf("job_no")]][1][$row[csf("trim_group")]][chop($row[csf("description")],", [BS]")][$row[csf("item_color")]][$row[csf("item_size")]]["trim_group"]=$row[csf("trim_group")];
				$details_data[$row[csf("job_no")]][1][$row[csf("trim_group")]][chop($row[csf("description")],", [BS]")][$row[csf("item_color")]][$row[csf("item_size")]]["uom"]=$row[csf("uom")];
				$details_data[$row[csf("job_no")]][1][$row[csf("trim_group")]][chop($row[csf("description")],", [BS]")][$row[csf("item_color")]][$row[csf("item_size")]]["store_id"].=$row[csf("store_id")].",";
				$details_data[$row[csf("job_no")]][1][$row[csf("trim_group")]][chop($row[csf("description")],", [BS]")][$row[csf("item_color")]][$row[csf("item_size")]]["prod_id"].=$row[csf("prod_id")].",";
				$details_data[$row[csf("job_no")]][1][$row[csf("trim_group")]][chop($row[csf("description")],", [BS]")][$row[csf("item_color")]][$row[csf("item_size")]]["cons_quantity"]+=$row[csf("cons_quantity")];
				$details_data[$row[csf("job_no")]][1][$row[csf("trim_group")]][chop($row[csf("description")],", [BS]")][$row[csf("item_color")]][$row[csf("item_size")]]["cons_amount"]+=$row[csf("cons_amount")];
				$details_data[$row[csf("job_no")]][1][$row[csf("trim_group")]][chop($row[csf("description")],", [BS]")][$row[csf("item_color")]][$row[csf("item_size")]]["order_ids"].=$row[csf("from_order_id")].','.$row[csf("to_order_id")].',';
			}



			//echo $all_order_id;die;
			//echo "<pre>";print_r($details_data);die;
			// and a.entry_form=24
			$issue_qty_sql="SELECT b.po_breakdown_id, a.item_color as item_color_id, a.item_size, a.item_group_id, a.item_description, b.quantity, c.cons_rate as rate, b.entry_form, b.trans_type, c.store_id
			from product_details_master a, order_wise_pro_details b, inv_transaction c where a.id=b.prod_id and b.trans_id=c.id and item_category_id=4  and b.entry_form in(24,25,49,73) and b.trans_type in(1,2,3,4) and c.transaction_type in(1,2,3,4) and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $store_cond";
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

			$trans_qty_sql="SELECT b.po_breakdown_id, a.item_color as item_color_id, a.item_size, a.item_group_id, a.item_description, b.quantity, c.cons_rate as rate, b.entry_form, b.trans_type, c.store_id, d.job_no_mst as job_no
			from product_details_master a, order_wise_pro_details b, inv_transaction c ,wo_po_break_down d 
			where a.id=b.prod_id and b.trans_id=c.id and d.id=b.po_breakdown_id and d.id=c.order_id and item_category_id=4  and b.entry_form in(78,112) and b.trans_type in(5,6) and c.transaction_type in(5,6) and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $store_cond";
			$q=1;
			if($all_order_id!="")
			{
				$all_order_id_arr=array_chunk(array_unique(explode(",",$all_order_id)),999);
				foreach($all_order_id_arr as $order_id)
				{
					if($q==1) $trans_qty_sql .=" and (b.po_breakdown_id in(".implode(',',$order_id).")"; else $trans_qty_sql .=" or b.po_breakdown_id  in(".implode(',',$order_id).")";
					$q++;		
				}
				$trans_qty_sql .=" )";
			}
			//echo $trans_qty_sql;die;

			$store_to_store_sql="SELECT b.po_breakdown_id, a.item_color as item_color_id, a.item_size, a.item_group_id, a.item_description, b.quantity, c.cons_rate as rate, b.entry_form, b.trans_type, c.store_id, d.transfer_criteria
			from product_details_master a, order_wise_pro_details b, inv_transaction c, inv_item_transfer_mst d
			where a.id=b.prod_id and b.trans_id=c.id and d.id=c.mst_id and item_category_id=4 and a.entry_form=24 and b.entry_form in(112) and b.trans_type in(5,6) and c.transaction_type in(5,6) and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $store_cond and d.transfer_criteria=2";
			$q=1;
			if($all_order_id!="")
			{
				$all_order_id_arr=array_chunk(array_unique(explode(",",$all_order_id)),999);
				foreach($all_order_id_arr as $order_id)
				{
					if($q==1) $store_to_store_sql .=" and (b.po_breakdown_id in(".implode(',',$order_id).")"; else $store_to_store_sql .=" or b.po_breakdown_id  in(".implode(',',$order_id).")";
					$q++;
				}
				$store_to_store_sql .=" )";
			}
			$store_to_store_sql_result=sql_select($store_to_store_sql);
			$transfer_criteria_arr=array();
			foreach($store_to_store_sql_result as $rows)
			{
				if($rows[csf('item_size')]=="") $item_sizeId=0; else $item_sizeId=$rows[csf('item_size')];
				if($rows[csf('trans_type')]==5 || $rows[csf('trans_type')]==6)
				{
					$transfer_criteria_arr[$rows[csf('po_breakdown_id')]][$rows[csf('item_group_id')]][chop($rows[csf('item_description')],", [BS]")][$rows[csf('item_color_id')]][$item_sizeId]["transfer_criteria"]=$rows[csf('transfer_criteria')];
				}
			}
			/*echo '<pre>';
			print_r($transfer_criteria_arr);die;*/

			$issue_qty_sql_result=sql_select($issue_qty_sql);
			$issue_data_arr=array();
			foreach($issue_qty_sql_result as $row)
			{
				if($row[csf('item_size')]=="") $item_size_id=0; else $item_size_id=$row[csf('item_size')];
				
				if($row[csf('entry_form')]==24 && $row[csf('trans_type')]==1)
				{
					$issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][chop(trim($row[csf('item_description')]),", [BS]")][$row[csf('item_color_id')]][$item_size_id]["rcv_quantity"]+=$row[csf('quantity')];
					$issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][chop(trim($row[csf('item_description')]),", [BS]")][$row[csf('item_color_id')]][$item_size_id]["rcv_amt"]+=$row[csf('quantity')]*$row[csf('rate')];
				}

				if($row[csf('entry_form')]==25 && $row[csf('trans_type')]==2)
				{
					$issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][chop(trim($row[csf('item_description')]),", [BS]")][$row[csf('item_color_id')]][$item_size_id]["issue_quantity"]+=$row[csf('quantity')];
					$issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][chop(trim($row[csf('item_description')]),", [BS]")][$row[csf('item_color_id')]][$item_size_id]["issue_amt"]+=$row[csf('quantity')]*$row[csf('rate')];
				}
				if($row[csf('entry_form')]==49 && $row[csf('trans_type')]==3)
				{
					$issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][chop(trim($row[csf('item_description')]),", [BS]")][$row[csf('item_color_id')]][$item_size_id]["rcv_rtn_quantity"]+=$row[csf('quantity')];
					$issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][chop(trim($row[csf('item_description')]),", [BS]")][$row[csf('item_color_id')]][$item_size_id]["rcv_rtn_amt"]+=$row[csf('quantity')]*$row[csf('rate')];
				}
				if($row[csf('entry_form')]==73 && $row[csf('trans_type')]==4)
				{
					$issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][chop(trim($row[csf('item_description')]),", [BS]")][$row[csf('item_color_id')]][$item_size_id]["issue_rtn_quantity"]+=$row[csf('quantity')];
					$issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][chop(trim($row[csf('item_description')]),", [BS]")][$row[csf('item_color_id')]][$item_size_id]["issue_rtn_amt"]+=$row[csf('quantity')]*$row[csf('rate')];
				}
				if($store_check[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][chop(trim($row[csf('item_description')]),", [BS]")][$row[csf('item_color_id')]][$item_size_id][$row[csf('store_id')]]=="")
				{
					$store_check[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][chop(trim($row[csf('item_description')]),", [BS]")][$row[csf('item_color_id')]][$item_size_id][$row[csf('store_id')]]=$row[csf('store_id')];
					$issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][chop(trim($row[csf('item_description')]),", [BS]")][$row[csf('item_color_id')]][$item_size_id]["store_id"].=$row[csf('store_id')].",";
				}
			}

			$trans_qty_sql_result=sql_select($trans_qty_sql); 
			$transfer_data_arr=array();
			$chk_all_order_id=array_unique(explode(",",$all_order_id));
			foreach($trans_qty_sql_result as $row)
			{
				if($row[csf('item_size')]=="") $item_size_id=0; else $item_size_id=$row[csf('item_size')];
				
				if(($row[csf('entry_form')]==78 || $row[csf('entry_form')]==112) && $row[csf('trans_type')]==5)
				{
					if ($transfer_criteria_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][chop(trim($row[csf('item_description')]),", [BS]")][$row[csf('item_color_id')]][$item_size_id]["transfer_criteria"] != 2)
						{
							$transfer_data_arr[$row[csf('job_no')]][$row[csf('item_group_id')]][chop(trim($row[csf('item_description')]),", [BS]")][$row[csf('item_color_id')]][$item_size_id]["transfer_in_quantity"]+=$row[csf('quantity')];
							$transfer_data_arr[$row[csf('job_no')]][$row[csf('item_group_id')]][chop(trim($row[csf('item_description')]),", [BS]")][$row[csf('item_color_id')]][$item_size_id]["transfer_in_amt"]+=$row[csf('quantity')]*$row[csf('rate')];					
						}
					/*if (in_array($row[csf('po_breakdown_id')], $chk_all_order_id)){
						
					}
					else{
						if ($transfer_criteria_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][trim($row[csf('item_description')])][$row[csf('item_color_id')]][$item_size_id]["transfer_criteria"] != 2)
						{
							$transfer_data_arr[$row[csf('job_no')]][3][$row[csf('item_group_id')]][trim($row[csf('item_description')])][$row[csf('item_color_id')]][$item_size_id]["transfer_in_quantity"]+=$row[csf('quantity')];
							$transfer_data_arr[$row[csf('job_no')]][3][$row[csf('item_group_id')]][trim($row[csf('item_description')])][$row[csf('item_color_id')]][$item_size_id]["transfer_in_amt"]+=$row[csf('quantity')]*$row[csf('rate')];					
						}
					}*/
				}
				if(($row[csf('entry_form')]==78 || $row[csf('entry_form')]==112) && $row[csf('trans_type')]==6)
				{
					if ($transfer_criteria_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][chop(trim($row[csf('item_description')]),", [BS]")][$row[csf('item_color_id')]][$item_size_id]["transfer_criteria"] != 2)
						{
							$transfer_data_arr[$row[csf('job_no')]][$row[csf('item_group_id')]][chop(trim($row[csf('item_description')]),", [BS]")][$row[csf('item_color_id')]][$item_size_id]["transfer_out_quantity"]+=$row[csf('quantity')];
							$transfer_data_arr[$row[csf('job_no')]][$row[csf('item_group_id')]][chop(trim($row[csf('item_description')]),", [BS]")][$row[csf('item_color_id')]][$item_size_id]["transfer_out_amt"]+=$row[csf('quantity')]*$row[csf('rate')];					
						}
					/*if (in_array($row[csf('po_breakdown_id')], $chk_all_order_id)){
						
					}else{
						if ($transfer_criteria_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][trim($row[csf('item_description')])][$row[csf('item_color_id')]][$item_size_id]["transfer_criteria"] != 2)
						{
							$transfer_data_arr[$row[csf('job_no')]][3][$row[csf('item_group_id')]][trim($row[csf('item_description')])][$row[csf('item_color_id')]][$item_size_id]["transfer_out_quantity"]+=$row[csf('quantity')];
							$transfer_data_arr[$row[csf('job_no')]][3][$row[csf('item_group_id')]][trim($row[csf('item_description')])][$row[csf('item_color_id')]][$item_size_id]["transfer_out_amt"]+=$row[csf('quantity')]*$row[csf('rate')];					
						}
					}*/
				}

				if($store_check[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][chop(trim($row[csf('item_description')]),", [BS]")][$row[csf('item_color_id')]][$item_size_id][$row[csf('store_id')]]=="")
				{
					$store_check[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][chop(trim($row[csf('item_description')]),", [BS]")][$row[csf('item_color_id')]][$item_size_id][$row[csf('store_id')]]=$row[csf('store_id')];
					$transfer_data_arr[$row[csf('job_no')]][$row[csf('item_group_id')]][chop(trim($row[csf('item_description')]),", [BS]")][$row[csf('item_color_id')]][$item_size_id]["store_id"].=$row[csf('store_id')].",";
				}
			}
			//echo "<pre>"; print_r($details_data['JKCL-22-00012'][1][]);die;
			foreach ($details_data as $job_no=>$job_data)
			{
				foreach($job_data[1] as $item_group_id=>$item_group_data)
				{
					foreach($item_group_data as $item_desc=>$item_desc_data)
					{
						foreach($item_desc_data as $item_color_id=>$item_color_data)
						{
							foreach($item_color_data as $item_size=>$item_size_data)
							{
								$job_row_span[$job_no][1]++;
								$job_gorup_row_span[$job_no][1][$item_group_id]++;
								$po_quantity[$item_size_data['job_no']] += $item_size_data[('po_quantity')];
							}
						}
					}
				}
				foreach($job_data[2] as $item_group_id=>$item_group_data)
				{
					foreach($item_group_data as $item_desc=>$item_desc_data)
					{
						foreach($item_desc_data as $item_color_id=>$item_color_data)
						{
							foreach($item_color_data as $item_size=>$item_size_data)
							{
								$job_row_span[$job_no][2]++;
								$job_gorup_row_span[$job_no][2][$item_group_id]++;
								//$po_quantity[$item_size_data['job_no']] += $item_size_data[('po_quantity')];
							}
						}
					}
				}
				foreach($job_data[3] as $item_group_id=>$item_group_data)
				{
					foreach($item_group_data as $item_desc=>$item_desc_data)
					{
						foreach($item_desc_data as $item_color_id=>$item_color_data)
						{
							foreach($item_color_data as $item_size=>$item_size_data)
							{
								$job_row_span[$job_no][3]++;
								$job_gorup_row_span[$job_no][3][$item_group_id]++;
								//$po_quantity[$item_size_data['job_no']] += $item_size_data[('po_quantity')];
							}
						}
					}
				}
			}
			//echo "<pre>";print_r($details_data);die;
			//array_unique(explode(",",$all_order_id));
			$req_check=array();$i=1;$item_group_wise_data=array();$k=1;
			foreach ($details_data as $job_no=>$job_data)
			{
				//$job_row_span=count($job_data);
				$job_row_count=$job_row_span[$job_no][1];
				$job_row_count_general=$job_row_span[$job_no][2];
				$job_row_count_item_mismatch=$job_row_span[$job_no][3];
				//echo $job_row_span;die;
				foreach($job_data[1] as $item_group_id=>$item_group_data)
				{
					foreach($item_group_data as $item_desc=>$item_desc_data)
					{
						if($item_desc=='') $item_desc=0;
						foreach($item_desc_data as $item_color_id=>$item_color_data)
						{
							if($item_color_id=='') $item_color_id=0;
							foreach($item_color_data as $item_size=>$item_size_data)
							{
								if($item_size=='') $item_size=0;
								//echo $po_quantity;
								$all_po_id_arr=array_unique(explode(",",chop($item_size_data["po_id"],",")));
								$po_id_all=implode(",",$all_po_id_arr);
								$recv_qnty=$recv_value=$issue_qty=$issue_amount=$rcv_rtn_qty=$issue_rtn_qty=$transfer_in_qty=$transfer_out_qty=$net_recv_qnty=$net_recv_value=$net_issue_qnty=$net_issue_value=$wo_qty=$wo_amount=$req_qnty=$net_recv_value=$net_issue_value=$recv_amt=$issue_amt=$rcv_rtn_amt=$issue_rtn_amt=$transfer_in_amt=$transfer_out_amt=0;$all_store="";
								foreach($all_po_id_arr as $po_id)
								{
									/*$recv_qnty+=$dataArrayRecv[$po_id][$item_group_id][$item_desc][$item_color_id][$item_size]["quantity"];
									$recv_value+=$dataArrayRecv[$po_id][$item_group_id][$item_desc][$item_color_id][$item_size]["ord_amount"];*/
									$recv_qnty+=$issue_data_arr[$po_id][$item_group_id][trim($item_desc)][$item_color_id][$item_size]["rcv_quantity"];
									$recv_amt+=$issue_data_arr[$po_id][$item_group_id][trim($item_desc)][$item_color_id][$item_size]["rcv_amt"];
									
									$issue_qty+=$issue_data_arr[$po_id][$item_group_id][trim($item_desc)][$item_color_id][$item_size]["issue_quantity"];
									$issue_amt+=$issue_data_arr[$po_id][$item_group_id][trim($item_desc)][$item_color_id][$item_size]["issue_amt"];
									
									$rcv_rtn_qty+=$issue_data_arr[$po_id][$item_group_id][trim($item_desc)][$item_color_id][$item_size]["rcv_rtn_quantity"];
									$rcv_rtn_amt+=$issue_data_arr[$po_id][$item_group_id][trim($item_desc)][$item_color_id][$item_size]["rcv_rtn_amt"];
									$issue_rtn_qty+=$issue_data_arr[$po_id][$item_group_id][trim($item_desc)][$item_color_id][$item_size]["issue_rtn_quantity"];
									$issue_rtn_amt+=$issue_data_arr[$po_id][$item_group_id][trim($item_desc)][$item_color_id][$item_size]["issue_rtn_amt"];
									$req_qnty+=$trims_costing_arr[$po_id][$item_group_id];
									$all_store_id=array_unique(explode(",",chop($issue_data_arr[$po_id][$item_group_id][trim($item_desc)][$item_color_id][$item_size]["store_id"],",")));
									foreach($all_store_id as $str_id)
									{
										$all_store.=$store_name_arr[$str_id].",";
									}
								}
								$transfer_in_qty=$transfer_data_arr[$job_no][$item_group_id][trim($item_desc)][$item_color_id][$item_size]["transfer_in_quantity"];
									//echo $po_id."==".$item_group_id."==".trim($item_desc)."==".$item_color_id."==".$item_size."==".$transfer_data_arr[$job_no][$item_group_id][trim($item_desc)][$item_color_id][$item_size]["transfer_in_quantity"]."++";
								$transfer_in_amt=$transfer_data_arr[$job_no][$item_group_id][trim($item_desc)][$item_color_id][$item_size]["transfer_in_amt"];
								
								$transfer_out_qty=$transfer_data_arr[$job_no][$item_group_id][trim($item_desc)][$item_color_id][$item_size]["transfer_out_quantity"];
								$transfer_out_amt=$transfer_data_arr[$job_no][$item_group_id][trim($item_desc)][$item_color_id][$item_size]["transfer_out_amt"];
								//$transfer_in_qty=$transfer_out_qty=0;

								$all_store=implode(",",array_unique(explode(",",chop($all_store,","))));
								if($recv_amt>0 && $recv_qnty>0)
								{
									$ord_avg_rate=($recv_amt/$recv_qnty);
								}
								else
								{
									$ord_avg_rate=0;
								}
								$wo_qty=$item_size_data[('book_qnty')];
								$wo_amount=$item_size_data[('book_amt')];

								/*$net_recv_qnty=$recv_qnty+$issue_rtn_qty+$transfer_in_qty;
								$net_recv_value=$net_recv_qnty*$ord_avg_rate;
								$net_issue_qnty=$issue_qty+$rcv_rtn_qty+$transfer_out_qty;
								$net_issue_value=$net_issue_qnty*$ord_avg_rate;*/
								
								$receive_qnty=$recv_qnty-$rcv_rtn_qty;
								$net_recv_qnty=$receive_qnty+$transfer_in_qty;
								$receive_value=$recv_amt-$rcv_rtn_amt;
								$net_recv_value=$receive_value+$transfer_in_amt;

								$iss_qnty=$issue_qty-$issue_rtn_qty;
								$net_issue_qnty=$iss_qnty+$transfer_out_qty;
								$net_issue_value=$issue_amt+$transfer_out_amt-$issue_rtn_amt;

								if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor; ?>"  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<?
									if($job_check[$job_no][1]=="")
									{
										$job_check[$job_no][1]=$job_no;
										?>
										<td width="40" align="center" rowspan="<? echo $job_row_count; ?>"><? echo $k;?> </td>
										<td width="70" rowspan="<? echo $job_row_count; ?>" title="Trims Accessories"><p><? echo $buyer_arr[$item_size_data[('buyer_name')]]; ?></p></td>
										<td width="70" rowspan="<? echo $job_row_count; ?>" title="Trims Accessories"><p><? echo $season_arr[$item_size_data[('season_buyer_wise')]]; ?></p></td>
										<td width="100" rowspan="<? echo $job_row_count; ?>" title="Trims Accessories"><p><? echo $item_size_data[('job_no')]; ?></p></td>
										<?
										/*
										?>
										<a href='#report_details' onClick="openmypage_budge('<? echo $item_size_data[('job_no')]; ?>','<? echo $cbo_company; ?>','<? echo $item_size_data[('buyer_name')]; ?>','<? echo $item_size_data[('style_ref_no')]; ?>','<? echo change_date_format($item_size_data[('costing_date')]); ?>','<? echo $po_id_all; ?>','<? echo $item_size_data[('costing_per')]; ?>','accessories_details2');"></a>
										<?
										*/
										?>
										<td width="110" rowspan="<? echo $job_row_count; ?>" title="Trims Accessories"><p><? echo $item_size_data[('style_ref_no')]; ?></p></td>
										<td width="140" rowspan="<? echo $job_row_count; ?>"><p><? echo chop($item_size_data["po_number"],","); ?></p></td>
										<td width="70" rowspan="<? echo $job_row_count; ?>"><p><? echo implode(",",array_unique(explode(",",chop($item_size_data["file_no"],",")))); ?></p></td>
										<td width="80" rowspan="<? echo $job_row_count; ?>"><p><? echo implode(",",array_unique(explode(",",chop($item_size_data["int_ref_no"],",")))); ?></p></td>
										<td width="90" align="right" rowspan="<? echo $job_row_count; ?>"><? echo  $po_quantity[$item_size_data['job_no']]; ?></td>
										<td width="80" align="right" rowspan="<? echo $job_row_count; ?>"><? echo number_format($po_quantity[$item_size_data['job_no']]/12,2); ?></td>
										<?
									}
									?>
									<td width="150"><p><? echo $all_store;?></p></td>
									<td width="130" title="<? echo $item_group_id; ?>"><p><? echo $trim_group[$item_group_id]; ?></p></td>
									<td width="150"><p><? echo trim($item_desc); ?></p></td>
									<td width="60" align="center"><p><? echo $unit_of_measurement[$item_size_data[('uom')]]; ?></p></td>
                                    <?
									if($job_group_check[$job_no][1][$item_group_id]=="")
									{
										$job_group_check[$job_no][1][$item_group_id]=$job_no."==".$item_group_id;
										$item_group_wise_data[$item_group_id]["req_qnty"]+=$req_qnty;
										?>
                                        <td width="90" align="right"  rowspan="<? echo $job_gorup_row_span[$job_no][1][$item_group_id]; ?>"><? echo number_format($req_qnty,2);$total_required_qnty+=$req_qnty;?></td>
                                        <?
									}
									?>
									<td width="100" title="<? echo $item_color_id; ?>"><p><? echo $color_library[$item_color_id]; ?></p></td>
									<td width="60"><p><? if($item_size=="0") echo "&nbsp;"; else echo $item_size; ?></p></td>
									<td width="80" align="right" title="<? echo "po id==". $po_id_all."item group id==".$item_group_id."item description==".trim($item_desc)."item color id==".$item_color_id."item size id==".$item_size; ?>"><a href='#report_details' onClick="openmypage('<? echo $po_id_all; ?>','<? echo $item_group_id; ?>','<? echo $item_color_id; ?>','<? echo $gmts_color; ?>','<? echo $item_size; ?>','<? echo trim($item_desc); ?>','<? echo $recv_basis; ?>','<? echo $without_order; ?>',1,'wo_receive_popup');"><?  echo number_format($wo_qty,2); ?></a></td>
									<td width="80" align="right" title="<? echo "po id=". $po_id." item group id=".$item_group_id." item desc=".trim($item_desc)." item color id=".$item_color_id." item size id=".$item_size; ?>"><?  echo number_format($wo_amount,2); ?></td>
									<!-- $recv_qnty.'=='.$transfer_in_qty.'=='.$rcv_rtn_qty.'=='. -->
									<td width="90" align="right" title="<? echo "rcv=".$recv_qnty." iss rtn=".$issue_rtn_qty ;?>"><a href='#report_details' onClick="openmypage('<? echo $po_id_all; ?>','<? echo $item_group_id; ?>','<? echo $item_color_id; ?>','<? echo $gmts_color; ?>','<? echo $item_size; ?>','<? echo trim($item_desc); ?>','<? echo $recv_basis; ?>','<? echo $without_order; ?>',2,'receive_popup');"><?  echo number_format($receive_qnty,2); ?></a>  </td>
									<td width="90" align="right" title="<? echo " trans In=".$transfer_in_qty ;?>"><a href='#report_details' onClick="openmypage('<? echo $po_id_all; ?>','<? echo $item_group_id; ?>','<? echo $item_color_id; ?>','<? echo $gmts_color; ?>','<? echo $item_size; ?>','<? echo trim($item_desc); ?>','<? echo $recv_basis; ?>','<? echo $without_order; ?>',2,'transfer_popup');"><?  echo number_format($transfer_in_qty,2); ?></a>  </td>
									<td width="80" align="right"><? echo number_format($wo_qty-$net_recv_qnty,2); ?></td>
									<td width="100" align="right"><? echo number_format($net_recv_value,2); ?></td>
									<td width="90" align="right"> <a href='#report_details' onClick="openmypage('<? echo $po_id_all; ?>','<? echo $item_group_id; ?>','<? echo $item_color_id; ?>','<? echo $gmts_color; ?>','<? echo $item_size; ?>','<? echo trim($item_desc); ?>','<? echo $recv_basis; ?>','<? echo $without_order; ?>',3,'issue_popup');"><?  echo number_format($iss_qnty,2); ?></a></td>
									<td width="90" align="right"> <a href='#report_details' onClick="openmypage('<? echo $po_id_all; ?>','<? echo $item_group_id; ?>','<? echo $item_color_id; ?>','<? echo $gmts_color; ?>','<? echo $item_size; ?>','<? echo trim($item_desc); ?>','<? echo $recv_basis; ?>','<? echo $without_order; ?>',3,'transfer_out_popup');"><?  echo number_format($transfer_out_qty,2); ?></a></td>
									<td width="80" align="right"><?  echo number_format($net_issue_value,2); ?></td>
									<td width="100" align="right"><? $left_over=$net_recv_qnty-$net_issue_qnty;echo number_format($left_over,2); ?></td>
									<td width="60" align="right"><?  echo number_format($ord_avg_rate,2); ?></td>
									<td align="right"><? $tot_left_val=$net_recv_value-$net_issue_value; echo number_format($tot_left_val,2);?></td>
								</tr>
								<?

								$item_group_wise_data[$item_group_id]["uom"]=$item_size_data[('uom')];
								$item_group_wise_data[$item_group_id]["wo_qty"]+=$wo_qty;
								$item_group_wise_data[$item_group_id]["recv_qnty"]+=$net_recv_qnty;
								$item_group_wise_data[$item_group_id]["issue_qnty"]+=$net_issue_qnty;

								$total_wo_qty+=$wo_qty;
								$total_wo_amount+=$wo_amount;
								$total_issue_amount+=$net_issue_value;
								$total_wo_val+=$wo_qty-$net_recv_qnty;
								$total_recv_qty+=$net_recv_qnty;
								$total_recv_value+=$net_recv_value;
								$total_issue_qty+=$net_issue_qnty;
								$total_left_val+=$tot_left_val;
								$total_left_over+=$left_over;
								$left_over=$tot_left_val=0;

								$i++;
							}
						}
					}
				}
				$k++;

				foreach($job_data[2] as $item_group_id=>$item_group_data)
				{
					foreach($item_group_data as $item_desc=>$item_desc_data)
					{
						if($item_desc=='') $item_desc=0;
						foreach($item_desc_data as $item_color_id=>$item_color_data)
						{
							foreach($item_color_data as $item_size=>$item_size_data)
							{
								if($item_size=='') $item_size=0;
								$all_store=$po_id_all=$prod_id_all="";
								
								$prod_id_all=implode(",",array_unique(explode(",",chop($item_size_data["prod_id"],","))));
								$po_id_all=implode(",",array_unique(explode(",",chop($item_size_data["po_id"],","))));
								$all_store_id=array_unique(explode(",",chop($item_size_data["store_id"],",")));
								foreach($all_store_id as $str_id)
								{
									$all_store.=$store_name_arr[$str_id].",";
								}
								$all_store=chop($all_store,",");
								if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor; ?>"  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<?
									if($job_check[$job_no][2]=="")
									{
										$job_check[$job_no][2]=$job_no;
										?>
										<td width="40" align="center" rowspan="<? echo $job_row_count_general; ?>"><? echo $k;?> </td>
										<td width="70" rowspan="<? echo $job_row_count_general; ?>" title="General Accessories"><p><? echo $buyer_arr[$item_size_data[('buyer_name')]]; ?></p></td>
										<td width="70" rowspan="<? echo $job_row_count_general; ?>" title="General Accessories"><p><? echo $season_arr[$item_size_data[('season_buyer_wise')]]; ?></p></td>
										<td width="100" rowspan="<? echo $job_row_count_general; ?>" title="General Accessories"><p><? echo $item_size_data[('job_no')]; ?></p></td>
										<td width="110" rowspan="<? echo $job_row_count_general; ?>" title="General Accessories"><p><? echo $item_size_data[('style_ref_no')]; ?></p></td>
										<td width="140" rowspan="<? echo $job_row_count_general; ?>"><p><? echo chop($item_size_data["po_number"],","); ?></p></td>
										<td width="70" rowspan="<? echo $job_row_count_general; ?>"><p><? echo implode(",",array_unique(explode(",",chop($item_size_data["file_no"],",")))); ?></p></td>
										<td width="80" rowspan="<? echo $job_row_count_general; ?>"><p><? echo implode(",",array_unique(explode(",",chop($item_size_data["int_ref_no"],",")))); ?></p></td>
										<td width="90" align="right" rowspan="<? echo $job_row_count_general; ?>"><? //echo $po_quantity[$item_size_data['job_no']]; ?></td>
										<td width="80" align="right" rowspan="<? echo $job_row_count_general; ?>"><? //echo number_format($po_quantity[$item_size_data['job_no']]/12,2); ?></td>
										<?
									}
									?>
									<td width="150"><p><? echo $all_store;?></p></td>
									<td width="130" title="<? echo $item_group_id; ?>"><p><? echo $trim_group[$item_group_id]; ?></p></td>
									<td width="150"><p><? echo $item_desc; ?></p></td>
									<td width="60" align="center"><p><? echo $unit_of_measurement[$item_size_data[('uom')]]; ?></p></td>
                                    <?
									if($job_group_check[$job_no][2][$item_group_id]=="")
									{
										$job_group_check[$job_no][2][$item_group_id]=$job_no."==".$item_group_id;
										//$item_group_wise_data[$item_group_id]["req_qnty"]+=$req_qnty;
										?>
                                        <td width="90" align="right"  rowspan="<? echo $job_gorup_row_span[$job_no][2][$item_group_id]; ?>"><? //echo number_format($req_qnty,2);$total_required_qnty+=$req_qnty;?></td>
                                        <?
									}

									?>
									<td width="100" title="<? echo $item_color_id; ?>"><p><? echo $color_library[$item_color_id]; ?></p></td>
									<td width="60"><p><? if($item_size=="0") echo "&nbsp;"; else echo $item_size; ?></p></td>
									<td width="80" align="right">&nbsp;</td>
									<td width="80" align="right">&nbsp;</td>
									<td width="90" align="right">&nbsp;</td>
									<td width="90" align="right">&nbsp;</td>
									<td width="80" align="right">&nbsp;</td>
									<td width="100" align="right">&nbsp;</td>
									<td width="90" align="right"><a href='#report_details' onClick="openmypage('<? echo $po_id_all; ?>','<? echo $item_group_id; ?>','<? echo $item_color; ?>','<? echo $gmts_color; ?>','<? echo $item_size; ?>','<? echo $item_desc; ?>','<? echo $prod_id_all; ?>','<? echo $without_order; ?>',3,'issue_popup_general');"><?  echo number_format($item_size_data["cons_quantity"],2); ?></a></td>
									<td width="90" align="right">&nbsp;</td>
									<td width="80" align="right"><?  echo number_format($item_size_data["cons_amount"],2); ?></td>
									<td width="100" align="right">&nbsp;</td>
									<td width="60" align="right">&nbsp;</td>
									<td align="right">&nbsp;</td>
								</tr>
								<?
								$i++;
								$total_issue_amount+=$item_size_data["cons_amount"];
							}
						}
					}
				}
				$k++;
			}
			?>
			<tr class="tbl_bottom">
				<td colspan="8" align="right">Total</td>
				<td align="right"><? echo number_format(array_sum($po_quantity),2); ?></td>
				<td colspan="5" align="right"></td>
				<td align="right"><? //echo number_format($total_required_qnty,2); ?></td>
                <td></td>
                <td></td>
				<td align="right"><? //echo number_format($total_wo_qty,2); ?></td>
                <td align="right"><? echo number_format($total_wo_amount,2); ?></td>
				<td align="right"><? //echo number_format($total_recv_qty,2); ?></td>
				<td align="right"><? //echo number_format($total_recv_qty,2); ?></td>
				<td align="right"><? //echo number_format($total_wo_val,2); ?></td>
				<td align="right"><? echo number_format($total_recv_value,2); ?></td>
				<td align="right"><? //echo number_format($total_issue_qty,2); ?></td>
				<td align="right"><? //echo number_format($total_issue_qty,2); ?></td>
                <td align="right"><? echo number_format($total_issue_amount,2); ?></td>
				<td align="right"><? //echo number_format($total_left_over,2); ?></td>
				<td>&nbsp;</td>
				<td align="right"><? echo number_format($total_left_val,2); ?></td>
			</tr>
		</table>
		</div>
        <br />
        <p style="text-align:left; padding-left:10px; font-size:16px; font-weight:bold">Summary Report</p>
        <br />
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1200" class="rpt_table" align="left">
        	<thead>
                <tr>
                    <th width="50">SL </th>
                    <th width="170">Item Group</th>
                    <th width="70">UOM</th>
                    <th width="100">Req Qty</th>
                    <th width="100">WO Qty</th>
                    <th width="100">WO %</th>
                    <th width="100">In-House Qty</th>
                    <th width="100">In-House %</th>
                    <th width="100">In-House Balance Qty</th>
                    <th width="100">Issue Qty</th>
                    <th width="100">Issue %</th>
                    <th>Left Over Qty</th>
                </tr>
            </thead>
            <tbody>
				<?
				$i=1;
                foreach($item_group_wise_data as $item_id=>$val)
                {
					$wo_qnty_percent=(($val["wo_qty"]/$val["req_qnty"])*100);
					$recv_qnty_percent=(($val["recv_qnty"]/$val["wo_qty"])*100);
					$recv_qnty_bal=$val["wo_qty"]-$val["recv_qnty"];
					$issue_qnty_percent=(($val["issue_qnty"]/$val["recv_qnty"])*100);
					$left_overs_qnty=$val["recv_qnty"]-$val["issue_qnty"];

					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td align="center"><? echo $i;?></td>
                        <td title="<? echo $item_id; ?>"><p><? echo $trim_group[$item_id] ; ?>&nbsp;</p></td>
                        <td align="right"><? echo $unit_of_measurement[$val["uom"]];?></td>
                        <td align="right"><? echo number_format($val["req_qnty"],2);?></td>
                        <td align="right"><? echo number_format($val["wo_qty"],2);?></td>
                        <td align="right"><? echo number_format($wo_qnty_percent,2);?></td>
                        <td align="right"><? echo number_format($val["recv_qnty"],2);?></td>
                        <td align="right"><? echo number_format($recv_qnty_percent,2);?></td>
                        <td align="right"><? echo number_format($recv_qnty_bal,2);?></td>
                        <td align="right"><? echo number_format($val["issue_qnty"],2);?></td>
                        <td align="right"><? echo number_format($issue_qnty_percent,2);?></td>
                        <td align="right"><? echo number_format($left_overs_qnty,2);?></td>
                    </tr>
					<?
                    $i++;
                }
                ?>
            </tbody>
            <tfoot>
            </tfoot>
        </table>
	</fieldset>
	<?
    $html=ob_get_contents();
    ob_clean();
    foreach (glob("*.xls") as $filename) {
    	@unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, $html);
    echo "$html**$filename**5";
    exit();
}

// Style Wise Search.
if ($action=="report_generate_style3") // Report 3
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company=str_replace("'","",$cbo_company_id);
	$cbo_buyer=str_replace("'","",$cbo_buyer_id);
	$cbo_season_name = str_replace("'","",$cbo_season_name);
	$all_style=str_replace("'","",$txt_style);
	$all_style=implode(",",array_unique(explode(",",$all_style)));
	$txt_order_no=str_replace("'","",$txt_order_no);
	$txt_order_no_id=str_replace("'","",$txt_order_no_id);
	$txt_file_no=str_replace("'","",$txt_file_no);
	$txt_ref_no=str_replace("'","",$txt_ref_no);

	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);

	$all_style_no=explode(",",str_replace("'","",$txt_style_id));
	$all_style_quted="";
	foreach($all_style_no as $style_no)
	{
		$all_style_quted.="'".$style_no."'".",";
	}
	$all_style_quted=chop($all_style_quted,",");
	//echo $all_style_quted.jahid;die;

	$cbo_store_name = str_replace("'","",$cbo_store_name);

	if($db_type==0)
	{
		$currency_convert_result=sql_select("select id, conversion_rate from currency_conversion_rate  where status_active=1 and currency=2 order by id desc LIMIT 1");
	}
	else
	{
		$currency_convert_result=sql_select("select * from (select a.id, a.conversion_rate, max(id) over () as max_pk from currency_conversion_rate a  where a.status_active=1 and a.currency=2) where id=max_pk");
	}
	$currency_convert_rate=$currency_convert_result[0][csf("conversion_rate")];


	//echo $all_style_quted;die;
	$sql_cond="";
	if($cbo_buyer>0)
	{
		$sql_cond=" and d.buyer_name=$cbo_buyer";
	}

	if($cbo_season_name>0)  $sql_cond.=" and d.season_buyer_wise=$cbo_season_name";


	if(str_replace("'","",$txt_order_no_id)!="")
	{
		$sql_cond .=" and c.id in(".str_replace("'","",$txt_order_no_id).")";
	}
	elseif($txt_order_no !="")
	{
		$po_number=trim($txt_order_no)."%";
		$sql_cond .=" and c.po_number like '$po_number'";
	}

	if($txt_file_no !="") $sql_cond .=" and c.file_no in($txt_file_no)";
	if($txt_ref_no !="") $sql_cond .=" and c.grouping='$txt_ref_no'";
	if($all_style !="") $sql_cond .=" and d.id in(".$all_style.")";

	//=============================================
	$shipping_status = str_replace("'","",$shipping_status);
	$cbo_search_by = str_replace("'","",$cbo_search_by);
	$exFactoryDate_cond = $shipping_status_cond="";
	if($cbo_search_by == 2) // Ex-factory date
	{
		if( $shipping_status!=0) $shipping_status_cond= " and shiping_status=$shipping_status"; else $shipping_status_cond="";

		if( $date_from!="" && $date_to!="") $exFactoryDate_cond= " and ex_factory_date between '".$date_from."' and '".$date_to."'"; else $exFactoryDate_cond="";
		$sql_exFactory = "SELECT PO_BREAK_DOWN_ID, SHIPING_STATUS from pro_ex_factory_mst where status_active=1 and is_deleted=0 $exFactoryDate_cond $shipping_status_cond group by po_break_down_id";
		// echo $sql_exFactory;die('=A');
		$sqlExFactoryData =sql_select($sql_exFactory);
		foreach($sqlExFactoryData as $row)
		{
			if ($trans_check[$row['PO_BREAK_DOWN_ID']]=='') 
			{
				$trans_check[$row['PO_BREAK_DOWN_ID']]=$row['PO_BREAK_DOWN_ID'];
				$all_po_id.=$row['PO_BREAK_DOWN_ID'].',';
			}
		}
		$all_po_id=chop($all_po_id,",");
		$p=1;
		if($all_po_id!="")
		{
			$all_po_id_arr=array_chunk(array_unique(explode(",",$all_po_id)),999);
			foreach($all_po_id_arr as $po_id)
			{
				if($p==1) $exfactory_po_id_cond .=" and (c.id in(".implode(',',$po_id).")"; else $exfactory_po_id_cond .=" or c.id in(".implode(',',$po_id).")";
				$p++;
			}
			$exfactory_po_id_cond .=" )";
		}
		// echo $exfactory_po_id_cond.Tipu;die;
	}
	else // Shipment date
	{
		if( $date_from!="" && $date_to!="")
		{
			$sql_cond .= " and c.pub_shipment_date between '".$date_from."' and '".$date_to."'";
		}
	}
	//======================================================
	//===========================================Shipping status cond Start
	
	if($shipping_status != 0 && $cbo_search_by != 2) // shipping_status
	{
		$sql_shipping_status_po = "SELECT PO_BREAK_DOWN_ID, SHIPING_STATUS from pro_ex_factory_mst where status_active=1 and is_deleted=0 and shiping_status=$shipping_status group by po_break_down_id";
		// echo $sql_shipping_status_po;die('=B');// and PO_BREAK_DOWN_ID=24120
		$sql_shipping_status_po_Data =sql_select($sql_shipping_status_po);
		foreach($sql_shipping_status_po_Data as $row)
		{
			if ($trans_check[$row['PO_BREAK_DOWN_ID']]=='') 
			{
				$trans_check[$row['PO_BREAK_DOWN_ID']]=$row['PO_BREAK_DOWN_ID'];
				$all_po_id.=$row['PO_BREAK_DOWN_ID'].',';
			}
		}
		$all_po_id=chop($all_po_id,",");
		$pp=1;
		if($all_po_id!="")
		{
			$all_po_id_arr=array_chunk(array_unique(explode(",",$all_po_id)),999);
			foreach($all_po_id_arr as $po_id)
			{
				if($pp==1) $shipping_status_po_id_cond .=" and (c.id in(".implode(',',$po_id).")"; else $shipping_status_po_id_cond .=" or c.id in(".implode(',',$po_id).")";
				$pp++;
			}
			$shipping_status_po_id_cond .=" )";
		}
		//echo $shipping_status_po_id_cond;die;
	}
	//===========================================Shipping status cond end

	if($cbo_store_name!=0) $store_cond=" and c.store_id =".str_replace("'","",$cbo_store_name); else $store_cond="";

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
	if(str_replace("'","",$txt_order_no_id)=='' && str_replace("'","",$txt_order_no)!='')
	{
		//if(str_replace("'","",$txt_order_no)!='')
		//{
			$condition->po_number("in('".$txt_order_no."')");
		//}
	}
	if(str_replace("'","",$txt_file_no) !='')
	{
		$condition->file_no("=$txt_file_no");
	}
	if(str_replace("'","",$txt_ref_no) !='')
	{
		$condition->grouping("='$txt_ref_no'");
	}	
	if($cbo_search_by == 1) // Shipment date
	{
		if(str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!='')
		{
			$condition->pub_shipment_date(" between '$date_from' and '$date_to'");
		}
	}
	$condition->init();

	$trims= new trims($condition);
	//echo $trims->getQuery();die;
	$trims_costing_arr=$trims->getQtyArray_by_orderAndItemid();
	//$trims_costing_arr=$trims->getQtyArray_by_orderItemidAndDescription();
	//getQtyArray_by_orderItemidAndDescription()
	//print_r($trims_costing_arr);die;

	//echo "<pre>";
	//print_r($trims_costing_arr);die;


	ob_start();
	?>
    <fieldset style="width:2560px;">
        <table width="2560">
            <tr class="form_caption">
                <td colspan="2560" align="center" style="border:none;font-size:16px; font-weight:bold"> <? echo $report_title; ?></td>
            </tr>
            <tr class="form_caption">
                <td colspan="28" align="center"><? echo $company_library[$cbo_company]; ?></td>
            </tr>
        </table>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="2560" class="rpt_table" >
			<thead>
				<th width="40">SL </th>
                <th width="70">Buyer</th>
                <th width="70">Season</th>
                <th width="100">Job No</th>
                <th width="110">Style</th>
                <th width="140">Order No</th>
                <th width="70">File No</th>
                <th width="80">Ref. No</th>
                <th width="90">Order Qty.</th>
                <th width="80">Order Qty.(Dzn)</th>
                <th width="150">Store</th>
                <th width="130">Item Group</th>
                <th width="150">Item Description</th>
                <th width="100">Remarks</th>
                <th width="60">UOM</th>
                <th width="60">Avg. Cons</th>
                <th width="60">EX. %</th>
                <th width="90">Req. Qty</th>
                <th width="80">WO. Qty</th>
                <th width="80">WO. Value (TK)</th>
                <th width="90">Recv. Qty</th>
                <th width="80">Recv. Bal.</th>
                <th width="100">Recv. Value (TK)</th>
                <th width="90">Issue Qty.</th>
                <th width="80">Issue Value (TK)</th>
                <th width="100">Left Over</th>
                <th width="60">Rate (TK)</th>
                <th>Left Over Value (TK)</th>
            </thead>
        </table>
        <div style="width:2560px; overflow-y:scroll; max-height:350px;font-size:12px; " id="scroll_body">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="2540" class="rpt_table" id="tbl_issue_status" >
		    <?

		    $store_name_arr = return_library_array("select a.id, a.store_name from lib_store_location a", 'id', 'store_name');
			$season_arr = return_library_array("select a.id, a.season_name from lib_buyer_season a", 'id', 'season_name');

			$i=1; $y=1; $tot_receive_qty=0; $tot_receive_value=0; $tot_issue_qty=0; $total_left_over=0; $total_left_over_balance=0; $dataArrayRecv=array();$wo_qty_ArrayRecv=array();$total_wo_qty=0;$total_wo_val=$total_wo_amount=$total_issue_amount=0;

			/*if($db_type==0) $null_val=" IFNULL(a.item_color, 0) as item_color, IFNULL(a.item_size, 0) as item_size";
			else if($db_type==2) $null_val=" nvl(a.item_color,0) as item_color, nvl(a.item_size,0) as item_size";*/

			

			$sql_precost ="SELECT a.id as trim_cost_id, a.trim_group, a.cons_uom as uom, a.description, a.cons_dzn_gmts as avg_cons, a.amount, b.cons, b.cons as cons_cal, c.id as po_id, c.po_number, c.file_no, c.grouping as int_ref_no, c.po_quantity, d.buyer_name, d.season_buyer_wise, d.job_no, d.style_ref_no, d.total_set_qnty, d.currency_id, a.remark, b.excess_per
			from wo_pre_cost_trim_cost_dtls a, wo_pre_cost_trim_co_cons_dtls b,  wo_po_break_down c, wo_po_details_master d 
			where a.job_no=b.job_no and d.job_no=a.job_no and a.id=b.wo_pre_cost_trim_cost_dtls_id and b.po_break_down_id=c.id and c.job_no_mst=d.job_no and d.company_name=$cbo_company 
			and b.cons>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sql_cond $exfactory_po_id_cond $shipping_status_po_id_cond 
			group by a.id, a.trim_group, a.cons_uom, a.description, a.cons_dzn_gmts, a.amount, b.cons, c.id, c.po_number, c.file_no, c.grouping, c.po_quantity, d.buyer_name, d.season_buyer_wise, d.job_no, d.style_ref_no, d.total_set_qnty, d.currency_id, a.remark, b.excess_per
			order by a.trim_group";
			// , b.id as trim_dtla_id			
			
			/*$sql_precost ="SELECT a.id, a.trim_group, a.cons_uom as uom, a.description, a.cons_dzn_gmts as avg_cons, a.amount, c.id as po_id, c.po_number, c.file_no, c.grouping as int_ref_no, c.po_quantity, d.buyer_name, d.season_buyer_wise, d.job_no, d.style_ref_no, d.total_set_qnty, d.currency_id, a.remark
			from wo_pre_cost_trim_cost_dtls a,  wo_po_break_down c, wo_po_details_master d 
			where a.job_no=d.job_no and c.job_no_mst=d.job_no and d.company_name=$cbo_company 
			and a.cons_dzn_gmts>0 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $sql_cond $exfactory_po_id_cond $shipping_status_po_id_cond
			order by a.trim_group";*/
			// echo $sql_precost;die;
			$sql_precost_result=sql_select($sql_precost);
			//print_r($sql_precost_result);
			$details_data=array();
			foreach($sql_precost_result as $row)
			{
				$details_data[$row[csf("job_no")]][1][$row[csf("trim_group")]][$row[csf("description")]]["job_no"]=$row[csf("job_no")];
				$details_data[$row[csf("job_no")]][1][$row[csf("trim_group")]][$row[csf("description")]]["buyer_name"]=$row[csf("buyer_name")];
				$details_data[$row[csf("job_no")]][1][$row[csf("trim_group")]][$row[csf("description")]]["season_buyer_wise"]=$row[csf("season_buyer_wise")];
				$details_data[$row[csf("job_no")]][1][$row[csf("trim_group")]][$row[csf("description")]]["style_ref_no"]=$row[csf("style_ref_no")];

				if($order_check[$row[csf("po_id")]]=="")
				{
					$order_check[$row[csf("po_id")]]=$row[csf("po_id")];
					$all_order_id.=$row[csf("po_id")].",";

					$details_data[$row[csf("job_no")]][1][$row[csf("trim_group")]][$row[csf("description")]]["po_quantity"]+=$row[csf("po_quantity")]*$row[csf("total_set_qnty")];
				}

				$details_data[$row[csf("job_no")]][1][$row[csf("trim_group")]][$row[csf("description")]]["po_id"].=$row[csf("po_id")].",";
				$details_data[$row[csf("job_no")]][1][$row[csf("trim_group")]][$row[csf("description")]]["po_number"].=$row[csf("po_number")].",";
				$details_data[$row[csf("job_no")]][1][$row[csf("trim_group")]][$row[csf("description")]]["file_no"].=$row[csf("file_no")].",";
				$details_data[$row[csf("job_no")]][1][$row[csf("trim_group")]][$row[csf("description")]]["int_ref_no"].=$row[csf("int_ref_no")].",";
				$details_data[$row[csf("job_no")]][1][$row[csf("trim_group")]][$row[csf("description")]]["remark"].=$row[csf("remark")].",";
				$details_data[$row[csf("job_no")]][1][$row[csf("trim_group")]][$row[csf("description")]]["trim_cost_id"].=$row[csf("trim_cost_id")].",";

				$details_data[$row[csf("job_no")]][1][$row[csf("trim_group")]][$row[csf("description")]]["trim_group"]=$row[csf("trim_group")];
				$details_data[$row[csf("job_no")]][1][$row[csf("trim_group")]][$row[csf("description")]]["uom"]=$row[csf("uom")];
				$details_data[$row[csf("job_no")]][1][$row[csf("trim_group")]][$row[csf("description")]]["booking_no"]=$row[csf("booking_no")];
				$details_data[$row[csf("job_no")]][1][$row[csf("trim_group")]][$row[csf("description")]]["description"]=$row[csf("description")];
				$details_data[$row[csf("job_no")]][1][$row[csf("trim_group")]][$row[csf("description")]]["avg_cons"]+=$row[csf("avg_cons")];
				$details_data[$row[csf("job_no")]][1][$row[csf("trim_group")]][$row[csf("description")]]["excess_per"]+=$row[csf("excess_per")];

				$details_data[$row[csf("job_no")]][1][$row[csf("trim_group")]][$row[csf("description")]]["rowCount"]+=1;
			}
			//echo "<pre>";print_r($details_data);die;
			$all_order_id=chop($all_order_id,",");
			/*if($db_type==0) $null_val=" IFNULL(a.item_color, 0) as item_color, IFNULL(a.item_size, 0) as item_size";
			else if($db_type==2) $null_val=" nvl(a.item_color,0) as item_color, nvl(a.item_size,0) as item_size";*/
			
			$general_item_issue_sql="SELECT d.buyer_name, d.season_buyer_wise, d.job_no, d.style_ref_no, d.total_set_qnty, c.id as po_id, c.po_number, c.file_no, c.grouping as int_ref_no, c.po_quantity, a.item_group_id as trim_group, a.unit_of_measure as uom, a.item_description as description, b.store_id, a.id as prod_id, b.cons_quantity, b.cons_amount
			from product_details_master a, inv_transaction b, wo_po_break_down c, wo_po_details_master d
			where a.id=b.prod_id and b.order_id=c.id and c.job_no_mst=d.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form<>24 and b.transaction_type in(2,3,6) and b.item_category in(".implode(",",array_keys($general_item_category)).")";
			// echo $general_item_issue_sql;die;    
			$p=1;
			if($all_order_id!="")
			{
				$all_order_id_arr=array_chunk(array_unique(explode(",",$all_order_id)),999);
				foreach($all_order_id_arr as $order_id)
				{
					if($p==1) $general_item_issue_sql .=" and (c.id in(".implode(',',$order_id).")"; else $issue_qty_sql .=" or c.id  in(".implode(',',$order_id).")";
					$p++;
				}
				$general_item_issue_sql .=" )";
			}
			//echo $general_item_issue_sql;die;
			$general_item_issue_result=sql_select($general_item_issue_sql);
			
			foreach($general_item_issue_result as $row)
			{
				$details_data[$row[csf("job_no")]][2][$row[csf("trim_group")]][$row[csf("description")]]["job_no"]=$row[csf("job_no")];
				$details_data[$row[csf("job_no")]][2][$row[csf("trim_group")]][$row[csf("description")]]["buyer_name"]=$row[csf("buyer_name")];
				$details_data[$row[csf("job_no")]][2][$row[csf("trim_group")]][$row[csf("description")]]["season_buyer_wise"]=$row[csf("season_buyer_wise")];
				$details_data[$row[csf("job_no")]][2][$row[csf("trim_group")]][$row[csf("description")]]["style_ref_no"]=$row[csf("style_ref_no")];
				$details_data[$row[csf("job_no")]][2][$row[csf("trim_group")]][$row[csf("description")]]["po_id"].=$row[csf("po_id")].",";
				$details_data[$row[csf("job_no")]][2][$row[csf("trim_group")]][$row[csf("description")]]["po_number"].=$row[csf("po_number")].",";
				$details_data[$row[csf("job_no")]][2][$row[csf("trim_group")]][$row[csf("description")]]["file_no"].=$row[csf("file_no")].",";
				$details_data[$row[csf("job_no")]][2][$row[csf("trim_group")]][$row[csf("description")]]["int_ref_no"].=$row[csf("int_ref_no")].",";

				$details_data[$row[csf("job_no")]][2][$row[csf("trim_group")]][$row[csf("description")]]["trim_group"]=$row[csf("trim_group")];
				$details_data[$row[csf("job_no")]][2][$row[csf("trim_group")]][$row[csf("description")]]["uom"]=$row[csf("uom")];
				$details_data[$row[csf("job_no")]][2][$row[csf("trim_group")]][$row[csf("description")]]["store_id"].=$row[csf("store_id")].",";
				$details_data[$row[csf("job_no")]][2][$row[csf("trim_group")]][$row[csf("description")]]["prod_id"].=$row[csf("prod_id")].",";
				$details_data[$row[csf("job_no")]][2][$row[csf("trim_group")]][$row[csf("description")]]["cons_quantity"]+=$row[csf("cons_quantity")];
				$details_data[$row[csf("job_no")]][2][$row[csf("trim_group")]][$row[csf("description")]]["cons_amount"]+=$row[csf("cons_amount")];
			}

			//echo $all_order_id;die;
			//echo "<pre>";print_r($details_data);die;

			$issue_qty_sql="SELECT b.po_breakdown_id, a.item_color as item_color_id, a.item_size, a.item_group_id, a.item_description, b.quantity, c.cons_rate as rate, b.entry_form, b.trans_type, c.store_id
			from product_details_master a, order_wise_pro_details b, inv_transaction c
			where a.id=b.prod_id and b.trans_id=c.id and item_category_id=4 and a.entry_form=24 and b.entry_form in(24,25,49,73,78,112) and b.trans_type in(1,2,3,4,5,6) and c.transaction_type in(1,2,3,4,5,6) and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $store_cond";
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
			//echo $issue_qty_sql;die;
			$issue_qty_sql_result=sql_select($issue_qty_sql);
			$issue_data_arr=array();
			foreach($issue_qty_sql_result as $row)
			{
				if($row[csf('item_size')]=="") $item_size_id=0; else $item_size_id=$row[csf('item_size')];
				
				if($row[csf('entry_form')]==24 && $row[csf('trans_type')]==1)
				{
					$issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]]["rcv_quantity"]+=$row[csf('quantity')];
					$issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]]["rcv_amt"]+=$row[csf('quantity')]*$row[csf('rate')];
				}

				if($row[csf('entry_form')]==25 && $row[csf('trans_type')]==2)
				{
					$issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]]["issue_quantity"]+=$row[csf('quantity')];
					$issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]]["issue_amt"]+=$row[csf('quantity')]*$row[csf('rate')];
				}
				if($row[csf('entry_form')]==49 && $row[csf('trans_type')]==3)
				{
					$issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]]["rcv_rtn_quantity"]+=$row[csf('quantity')];
					$issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]]["rcv_rtn_amt"]+=$row[csf('quantity')]*$row[csf('rate')];
				}
				if($row[csf('entry_form')]==73 && $row[csf('trans_type')]==4)
				{
					$issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]]["issue_rtn_quantity"]+=$row[csf('quantity')];
					$issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]]["issue_rtn_amt"]+=$row[csf('quantity')]*$row[csf('rate')];
				}
				if(($row[csf('entry_form')]==78 || $row[csf('entry_form')]==112) && $row[csf('trans_type')]==5)
				{
					if ($transfer_criteria_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]]["transfer_criteria"] != 2)
					{
						$issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]]["transfer_in_quantity"]+=$row[csf('quantity')];
						$issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]]["transfer_in_amt"]+=$row[csf('quantity')]*$row[csf('rate')];					
					}
				}
				if(($row[csf('entry_form')]==78 || $row[csf('entry_form')]==112) && $row[csf('trans_type')]==6)
				{
					if ($transfer_criteria_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]]["transfer_criteria"] != 2)
					{
						$issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]]["transfer_out_quantity"]+=$row[csf('quantity')];
						$issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]]["transfer_out_amt"]+=$row[csf('quantity')]*$row[csf('rate')];					
					}
				}
				if($store_check[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]][$row[csf('store_id')]]=="")
				{
					$store_check[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]][$row[csf('store_id')]]=$row[csf('store_id')];
					$issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]]["store_id"].=$row[csf('store_id')].",";
				}
			}
			//echo "<pre>";
			//print_r($issue_data_arr);

			$store_to_store_sql="SELECT b.po_breakdown_id, a.item_color as item_color_id, a.item_size, a.item_group_id, a.item_description, b.quantity, c.cons_rate as rate, b.entry_form, b.trans_type, c.store_id, d.transfer_criteria
			from product_details_master a, order_wise_pro_details b, inv_transaction c, inv_item_transfer_mst d
			where a.id=b.prod_id and b.trans_id=c.id and d.id=c.mst_id and item_category_id=4 and a.entry_form=24 and b.entry_form in(112) and b.trans_type in(5,6) and c.transaction_type in(5,6) and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $store_cond and d.transfer_criteria=2";
			$q=1;
			if($all_order_id!="")
			{
				$all_order_id_arr=array_chunk(array_unique(explode(",",$all_order_id)),999);
				foreach($all_order_id_arr as $order_id)
				{
					if($q==1) $store_to_store_sql .=" and (b.po_breakdown_id in(".implode(',',$order_id).")"; else $store_to_store_sql .=" or b.po_breakdown_id  in(".implode(',',$order_id).")";
					$q++;
				}
				$store_to_store_sql .=" )";
			}
			$store_to_store_sql_result=sql_select($store_to_store_sql);
			$transfer_criteria_arr=array();
			foreach($store_to_store_sql_result as $rows)
			{
				if($rows[csf('item_size')]=="") $item_sizeId=0; else $item_sizeId=$rows[csf('item_size')];
				if($rows[csf('trans_type')]==5 || $rows[csf('trans_type')]==6)
				{
					$transfer_criteria_arr[$rows[csf('po_breakdown_id')]][$rows[csf('item_group_id')]][$rows[csf('item_description')]][$rows[csf('item_color_id')]][$item_sizeId]["transfer_criteria"]=$rows[csf('transfer_criteria')];
				}
			}
			/*echo '<pre>';
			print_r($transfer_criteria_arr);die;*/

			$sql_booking ="SELECT d.job_no, c.id as po_id, b.trim_group, b.booking_no, a.description, a.cons, a.amount, p.currency_id
			from  wo_booking_mst p, wo_trim_book_con_dtls a, wo_booking_dtls b, wo_po_break_down c, wo_po_details_master d
			where p.booking_no=a.booking_no and p.booking_no=b.booking_no and a.wo_trim_booking_dtls_id=b.id and b.po_break_down_id=c.id and c.job_no_mst=d.job_no and d.company_name=$cbo_company and b.booking_type=2 and a.cons>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sql_cond order by d.job_no";
			//echo $sql_booking;die;
			$booking_sql_result=sql_select($sql_booking);
			$booking_data_arr=array();
			foreach($booking_sql_result as $row)
			{
				$booking_data_arr[$row[csf("po_id")]][$row[csf("trim_group")]][$row[csf("description")]]["book_qnty"]+=$row[csf("cons")];
				if($row[csf("currency_id")]==1)
				{
					$booking_data_arr[$row[csf("po_id")]][$row[csf("trim_group")]][$row[csf("description")]]["book_amt"]+=$row[csf("amount")];
				}
				else
				{
					$booking_data_arr[$row[csf("po_id")]][$row[csf("trim_group")]][$row[csf("description")]]["book_amt"]+=$row[csf("amount")]*$currency_convert_rate;
				}
			}
			/*echo "<pre>";
			print_r($booking_data_arr);die;*/
			$shipp_status_cond="";
			if($shipping_status!=0)
			{
				$shipp_status_cond=" and b.shiping_status=$shipping_status";
			}
			$sql_shipping_status = "SELECT b.po_break_down_id, b.shiping_status from pro_ex_factory_mst b where b.status_active=1 and b.is_deleted=0 $shipp_status_cond ";
			$kk=1;
			if($all_order_id!="")
			{
				$all_order_id_arr=array_chunk(array_unique(explode(",",$all_order_id)),999);
				foreach($all_order_id_arr as $order_id)
				{
					if($kk==1) $sql_shipping_status .=" and (b.po_break_down_id in(".implode(',',$order_id).")"; else $sql_shipping_status .=" or b.po_break_down_id  in(".implode(',',$order_id).")";
					$kk++;
				}
				$sql_shipping_status .=" )";
			}
			$sql_shipping_status.= " group by b.po_break_down_id, b.shiping_status";
			// echo $sql_shipping_status;die('=B');// and PO_BREAK_DOWN_ID=24120

			$sqlshipping_statusData =sql_select($sql_shipping_status);
			$shiping_statusArr=array();
			foreach($sqlshipping_statusData as $row)
			{
				if ($trans_checkArr[$row[csf('po_break_down_id')]]=='') 
				{
					$trans_checkArr[$row[csf('po_break_down_id')]]=$row[csf('po_break_down_id')];
					$shiping_statusArr[$row[csf('po_break_down_id')]]['shiping_status']=$row[csf('shiping_status')];
				}
			}
			/*echo '<pre>';
			print_r($shiping_statusArr);die;*/
			
			
			foreach ($details_data as $job_no=>$job_data)
			{
				foreach($job_data[1] as $item_group_id=>$item_group_data)
				{
					foreach($item_group_data as $item_desc=>$item_desc_data)
					{
						$trim_cost_id=implode(",",array_unique(explode(",",chop($item_desc_data["trim_cost_id"],","))));
						$job_row_span[$job_no][1]++;
						$job_gorup_row_span[$job_no][1][$item_group_id]++;
						$po_quantity[$item_desc_data['job_no']] += $item_desc_data[('po_quantity')];
						$trimCost_id_arr[$job_no][1][$item_group_id].=$item_desc_data["trim_cost_id"];
					}
				}
				foreach($job_data[2] as $item_group_id=>$item_group_data)
				{
					foreach($item_group_data as $item_desc=>$item_desc_data)
					{
						$job_row_span[$job_no][2]++;
						$job_gorup_row_span[$job_no][2][$item_group_id]++;
						$po_quantity[$item_desc_data['job_no']] += $item_desc_data[('po_quantity')];
					}
				}
			}
			// echo "<pre>";print_r($trimCost_id_arr);die;
			$req_check=array();$i=1;$item_group_wise_data=array();$k=1;
			foreach ($details_data as $job_no=>$job_data)
			{
				//$job_row_span=count($job_data);
				$job_row_count=$job_row_span[$job_no][1];
				$job_row_count_general=$job_row_span[$job_no][2];
				//echo $job_row_span;die;
				foreach($job_data[1] as $item_group_id=>$item_group_data)
				{
					foreach($item_group_data as $item_desc=>$item_desc_data)
					{
						if($item_size=='') $item_size=0;
						//echo $po_quantity;
						$all_po_id_arr=array_unique(explode(",",chop($item_desc_data["po_id"],",")));
						$po_id_all=implode(",",$all_po_id_arr);
						$recv_qnty=$recv_value=$issue_qty=$issue_amount=$rcv_rtn_qty=$issue_rtn_qty=$transfer_in_qty=$transfer_out_qty=$net_recv_qnty=$net_recv_value=$net_issue_qnty=$net_issue_value=$wo_qty=$wo_amount=$req_qnty=$net_recv_value=$net_issue_value=$recv_amt=$issue_amt=$rcv_rtn_amt=$issue_rtn_amt=$transfer_in_amt=$transfer_out_amt=0;$all_store="";
						foreach($all_po_id_arr as $po_id)
						{
							$wo_qty+=$booking_data_arr[$po_id][$item_group_id][$item_desc]["book_qnty"];
							$wo_amount+=$booking_data_arr[$po_id][$item_group_id][$item_desc]["book_amt"];
							$recv_qnty+=$issue_data_arr[$po_id][$item_group_id][$item_desc]["rcv_quantity"];
							$recv_amt+=$issue_data_arr[$po_id][$item_group_id][$item_desc]["rcv_amt"];
							
							$issue_qty+=$issue_data_arr[$po_id][$item_group_id][$item_desc]["issue_quantity"];
							$issue_amt+=$issue_data_arr[$po_id][$item_group_id][$item_desc]["issue_amt"];
							
							$rcv_rtn_qty+=$issue_data_arr[$po_id][$item_group_id][$item_desc]["rcv_rtn_quantity"];
							$rcv_rtn_amt+=$issue_data_arr[$po_id][$item_group_id][$item_desc]["rcv_rtn_amt"];
							
							$issue_rtn_qty+=$issue_data_arr[$po_id][$item_group_id][$item_desc]["issue_rtn_quantity"];
							$issue_rtn_amt+=$issue_data_arr[$po_id][$item_group_id][$item_desc]["issue_rtn_amt"];
							
							$transfer_in_qty+=$issue_data_arr[$po_id][$item_group_id][$item_desc]["transfer_in_quantity"];
							//echo $po_id."==".$item_group_id."==".$item_desc."==".$item_color_id."==".$item_size."==".$issue_data_arr[$po_id][$item_group_id][$item_desc]["transfer_in_quantity"]."++";
							$transfer_in_amt+=$issue_data_arr[$po_id][$item_group_id][$item_desc]["transfer_in_amt"];
							
							$transfer_out_qty+=$issue_data_arr[$po_id][$item_group_id][$item_desc]["transfer_out_quantity"];
							$transfer_out_amt+=$issue_data_arr[$po_id][$item_group_id][$item_desc]["transfer_out_amt"];

							$req_qnty+=$trims_costing_arr[$po_id][$item_group_id];

							$all_store_id=array_unique(explode(",",chop($issue_data_arr[$po_id][$item_group_id][$item_desc]["store_id"],",")));
							foreach($all_store_id as $str_id)
							{
								$all_store.=$store_name_arr[$str_id].",";
							}

							$all_shipp_status_id=array_unique(explode(",",chop($shiping_statusArr[$po_id]["shiping_status"],",")));
							foreach($all_shipp_status_id as $str_id)
							{
								$all_shipp_status.=$shipment_status[$str_id].",";
							}
						}
						$all_store=implode(",",array_unique(explode(",",chop($all_store,","))));
						$all_shipp_status=implode(",",array_unique(explode(",",chop($all_shipp_status,","))));
						if($recv_amt>0 && $recv_qnty>0)
						{
							$ord_avg_rate=($recv_amt/$recv_qnty);
						}
						else
						{
							$ord_avg_rate=0;
						}
						//$wo_qty=$item_desc_data[('book_qnty')];
						//$wo_amount=$item_desc_data[('book_amt')];

						/*$net_recv_qnty=$recv_qnty+$issue_rtn_qty+$transfer_in_qty;
						$net_recv_value=$net_recv_qnty*$ord_avg_rate;
						$net_issue_qnty=$issue_qty+$rcv_rtn_qty+$transfer_out_qty;
						$net_issue_value=$net_issue_qnty*$ord_avg_rate;*/
						
						
						$net_recv_qnty=$recv_qnty+$transfer_in_qty-$rcv_rtn_qty;
						$net_recv_value=$recv_amt+$transfer_in_amt-$rcv_rtn_amt;
						$net_issue_qnty=$issue_qty+$transfer_out_qty-$issue_rtn_qty;
						$net_issue_value=$issue_amt+$transfer_out_amt-$issue_rtn_amt;

						$trim_cost_id=implode(",",array_unique(explode(",",chop($item_desc_data["trim_cost_id"],","))));

						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo $bgcolor; ?>"  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<?
							if($job_check[$job_no][1]=="")
							{
								$job_check[$job_no][1]=$job_no;
								$po_number=implode(", ",array_unique(explode(",",chop($item_desc_data["po_number"],",")))).'<br>';
								?>
								<td width="40" align="center" rowspan="<? echo $job_row_count; ?>"><? echo $k;?> </td>
								<td width="70" rowspan="<? echo $job_row_count; ?>" title="Trims Accessories"><p><? echo $buyer_arr[$item_desc_data[('buyer_name')]]; ?></p></td>
								<td width="70" rowspan="<? echo $job_row_count; ?>" title="Trims Accessories"><p><? echo $season_arr[$item_desc_data[('season_buyer_wise')]]; ?></p></td>
								<td width="100" rowspan="<? echo $job_row_count; ?>" title="Trims Accessories"><p><? echo $item_desc_data[('job_no')]; ?></p></td>
								<td width="110" rowspan="<? echo $job_row_count; ?>" title="Trims Accessories"><p><? echo $item_desc_data[('style_ref_no')]; ?></p></td>
								<td width="140" style="word-break: break-all; word-wrap: break-word;" rowspan="<? echo $job_row_count; ?>"><p><? echo $po_number.$all_shipp_status;//chop($item_desc_data["po_number"],","); ?></p></td>
								<td width="70" rowspan="<? echo $job_row_count; ?>"><p><? echo implode(",",array_unique(explode(",",chop($item_desc_data["file_no"],",")))); ?></p></td>
								<td width="80" rowspan="<? echo $job_row_count; ?>"><p><? echo implode(",",array_unique(explode(",",chop($item_desc_data["int_ref_no"],",")))); ?></p></td>
								<td width="90" align="right" rowspan="<? echo $job_row_count; ?>"><? echo  $po_quantity[$item_desc_data['job_no']]; ?></td>
								<td width="80" title="<? echo $item_desc_data['job_no']; ?>" align="right" rowspan="<? echo $job_row_count; ?>"><? echo number_format($po_quantity[$item_desc_data['job_no']]/12,2); ?></td>
								<?
							}
							?>
							<td width="150"><p><? echo $all_store;?></p></td>
							<td width="130" title="<? echo $item_group_id; ?>"><p><? echo $trim_group[$item_group_id]; ?></p></td>
							<td width="150"><p><? echo $item_desc; ?></p></td>
							<td width="100"><p><? echo implode(", ",array_unique(explode(",",chop($item_desc_data["remark"],",")))); ?></p></td>
							<td width="60" align="center"><p><? echo $unit_of_measurement[$item_desc_data[('uom')]]; ?></p></td>
							<td width="60"><p><? echo $item_desc_data["avg_cons"]/$item_desc_data["rowCount"]; ?></p></td>
							<td width="60" title="<? echo $trim_cost_id; ?>"><p><? echo $item_desc_data["excess_per"]/$item_desc_data["rowCount"]; ?></p></td>
                            <?
                            // echo $trim_cost_id;
							if($job_itemGroup_check[$job_no][1][$item_group_id]=="")
							{
								$job_itemGroup_check[$job_no][1][$item_group_id]=$job_no."==".$item_group_id;
								$trimCost_id=chop($trimCost_id_arr[$job_no][1][$item_group_id],",");
								//$item_group_wise_data[$item_group_id]["req_qnty"]+=$req_qnty;
								?>
                                <td width="90" align="right" title="<? echo $trimCost_id; ?>" rowspan="<? echo $job_gorup_row_span[$job_no][1][$item_group_id]; ?>"><a href='#report_details' onClick="openmypage('<? echo $po_id_all; ?>','<? echo $item_group_id; ?>','<? echo $trimCost_id; ?>','<? echo $gmts_color; ?>','<? echo $item_size; ?>','<? echo $item_desc; ?>','<? echo $recv_basis; ?>','<? echo $without_order; ?>',4,'req_popup');"><? echo number_format($req_qnty,2);$total_required_qnty+=$req_qnty;?></a></td>
                                <?
							}
							?>
							<td width="80" align="right" title="<? echo "po id==". $po_id_all."item group id==".$item_group_id."item_desc==".$item_desc; ?>"><a href='#report_details' onClick="openmypage('<? echo $po_id_all; ?>','<? echo $item_group_id; ?>','<? echo $item_color_id; ?>','<? echo $gmts_color; ?>','<? echo $item_size; ?>','<? echo $item_desc; ?>','<? echo $recv_basis; ?>','<? echo $without_order; ?>',1,'wo_receive_popup');"><?  echo number_format($wo_qty,2); ?></a></td>
							
							<td width="80" align="right" title="<? echo "po id=". $po_id." item group id=".$item_group_id." item desc=".$item_desc; ?>"><?  echo number_format($wo_amount,2); ?></td>
							<!-- $recv_qnty.'=='.$transfer_in_qty.'=='.$rcv_rtn_qty.'=='. -->
							<td width="90" align="right" title="<? echo "rcv=".$recv_qnty." iss rtn=".$issue_rtn_qty." trans In=".$transfer_in_qty ?>"><a href='#report_details' onClick="openmypage('<? echo $po_id_all; ?>','<? echo $item_group_id; ?>','<? echo $item_color_id; ?>','<? echo $gmts_color; ?>','<? echo $item_size; ?>','<? echo $item_desc; ?>','<? echo $recv_basis; ?>','<? echo $without_order; ?>',2,'receive_popup');"><?  echo number_format($net_recv_qnty,2); ?></a>  </td>
							<td width="80" align="right"><? echo number_format($wo_qty-$net_recv_qnty,2); ?></td>
							<td width="100" align="right"><? echo number_format($net_recv_value,2); ?></td>
							<td width="90" align="right"> <a href='#report_details' onClick="openmypage('<? echo $po_id_all; ?>','<? echo $item_group_id; ?>','<? echo $item_color_id; ?>','<? echo $gmts_color; ?>','<? echo $item_size; ?>','<? echo $item_desc; ?>','<? echo $recv_basis; ?>','<? echo $without_order; ?>',3,'issue_popup');"><?  echo number_format($net_issue_qnty,2); ?></a></td>
							<td width="80" align="right"><?  echo number_format($net_issue_value,2); ?></td>
							<td width="100" align="right"><? $left_over=$net_recv_qnty-$net_issue_qnty;echo number_format($left_over,2); ?></td>
							<td width="60" align="right"><?  echo number_format($ord_avg_rate,2); ?></td>
							<td align="right"><? $tot_left_val=$net_recv_value-$net_issue_value; echo number_format($tot_left_val,2);?></td>
						</tr>
						<?

						$item_group_wise_data[$item_group_id]["uom"]=$item_desc_data[('uom')];
						$item_group_wise_data[$item_group_id]["wo_qty"]+=$wo_qty;
						$item_group_wise_data[$item_group_id]["recv_qnty"]+=$net_recv_qnty;
						$item_group_wise_data[$item_group_id]["issue_qnty"]+=$net_issue_qnty;

						$total_wo_qty+=$wo_qty;
						$total_wo_amount+=$wo_amount;
						$total_issue_amount+=$net_issue_value;
						$total_wo_val+=$wo_qty-$net_recv_qnty;
						$total_recv_qty+=$net_recv_qnty;
						$total_recv_value+=$net_recv_value;
						$total_issue_qty+=$net_issue_qnty;
						$total_left_val+=$tot_left_val;
						$total_left_over+=$left_over;
						$left_over=$tot_left_val=0;

						$i++;
					}
				}
				$k++;
				foreach($job_data[2] as $item_group_id=>$item_group_data)
				{
					foreach($item_group_data as $item_desc=>$item_desc_data)
					{
						if($item_size=='') $item_size=0;
						$all_store=$po_id_all=$prod_id_all="";
						
						$prod_id_all=implode(",",array_unique(explode(",",chop($item_desc_data["prod_id"],","))));
						$po_id_all=implode(",",array_unique(explode(",",chop($item_desc_data["po_id"],","))));
						$all_store_id=array_unique(explode(",",chop($item_desc_data["store_id"],",")));
						foreach($all_store_id as $str_id)
						{
							$all_store.=$store_name_arr[$str_id].",";
						}
						$all_store=chop($all_store,",");
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo $bgcolor; ?>"  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<?
							if($job_check[$job_no][2]=="")
							{
								$job_check[$job_no][2]=$job_no;
								$po_number=implode(",",array_unique(explode(",",chop($item_desc_data["po_number"],","))));
								?>
								<td width="40" align="center" rowspan="<? echo $job_row_count_general; ?>"><? echo $k;?> </td>
								<td width="70" rowspan="<? echo $job_row_count_general; ?>" title="General Accessories"><p><? echo $buyer_arr[$item_desc_data[('buyer_name')]]; ?></p></td>
								<td width="70" rowspan="<? echo $job_row_count_general; ?>" title="General Accessories"><p><? echo $season_arr[$item_desc_data[('season_buyer_wise')]]; ?></p></td>
								<td width="100" rowspan="<? echo $job_row_count_general; ?>" title="General Accessories"><p><? echo $item_desc_data[('job_no')]; ?></p></td>
								<td width="110" rowspan="<? echo $job_row_count_general; ?>" title="General Accessories"><p><? echo $item_desc_data[('style_ref_no')]; ?></p></td>
								<td width="140" rowspan="<? echo $job_row_count_general; ?>"><p><? echo $po_number; ?></p></td>
								<td width="70" rowspan="<? echo $job_row_count_general; ?>"><p><? echo implode(",",array_unique(explode(",",chop($item_desc_data["file_no"],",")))); ?></p></td>
								<td width="80" rowspan="<? echo $job_row_count_general; ?>"><p><? echo implode(",",array_unique(explode(",",chop($item_desc_data["int_ref_no"],",")))); ?></p></td>
								<td width="90" align="right" rowspan="<? echo $job_row_count_general; ?>"><? //echo $po_quantity[$item_desc_data['job_no']]; ?></td>
								<td width="80" align="right" rowspan="<? echo $job_row_count_general; ?>"><? //echo number_format($po_quantity[$item_desc_data['job_no']]/12,2); ?></td>
								<?
							}
							?>
							<td width="150"><p><? echo $all_store;?></p></td>
							<td width="130" title="<? echo $item_group_id; ?>"><p><? echo $trim_group[$item_group_id]; ?></p></td>
							<td width="150"><p><? echo $item_desc; ?></p></td>
							<td width="100" title="<? echo 'Remarks'; ?>"><p></p></td>
							<td width="60" align="center"><p><? echo $unit_of_measurement[$item_desc_data[('uom')]]; ?></p></td>
							<td width="60" title="<? echo 'Avg. Cons'; ?>"><p></p></td>
							<td width="60" title="<? echo 'Ex. %'; ?>"><p></p></td>
                            <?
							if($job_group_check[$job_no][2][$item_group_id]=="")
							{
								$job_group_check[$job_no][2][$item_group_id]=$job_no."==".$item_group_id;
								//$item_group_wise_data[$item_group_id]["req_qnty"]+=$req_qnty;
								?>
                                <td width="90" align="right"  rowspan="<? echo $job_gorup_row_span[$job_no][2][$item_group_id]; ?>"><? //echo number_format($req_qnty,2);$total_required_qnty+=$req_qnty;?></td>
                                <?
							}

							?>
							<td width="80" align="right">&nbsp;</td>
							<td width="80" align="right">&nbsp;</td>

							<td width="90" align="right">&nbsp;</td>
							<td width="80" align="right">&nbsp;</td>
							<td width="100" align="right">&nbsp;</td>
							<td width="90" align="right"><a href='#report_details' onClick="openmypage('<? echo $po_id_all; ?>','<? echo $item_group_id; ?>','<? echo $item_color; ?>','<? echo $gmts_color; ?>','<? echo $item_size; ?>','<? echo $item_desc; ?>','<? echo $prod_id_all; ?>','<? echo $without_order; ?>',3,'issue_popup_general');"><?  echo number_format($item_desc_data["cons_quantity"],2); ?></a></td>
							<td width="80" align="right"><?  echo number_format($item_desc_data["cons_amount"],2); ?></td>
							<td width="100" align="right">&nbsp;</td>
							<td width="60" align="right">&nbsp;</td>
							<td align="right">&nbsp;</td>
						</tr>
						<?
						$i++;
						$total_issue_amount+=$item_desc_data["cons_amount"];
					}
				}
				$k++;
			}
			?>
			<tr class="tbl_bottom">
				<td colspan="8" align="right">Total</td>
				<td align="right"><? echo number_format(array_sum($po_quantity),2); ?></td>
				<td colspan="6" align="right"></td>
				<td align="right"><? //echo number_format($total_required_qnty,2); ?></td>
                <td></td>
                <td></td>
				<td align="right"><? //echo number_format($total_wo_qty,2); ?></td>
                <td align="right"><? echo number_format($total_wo_amount,2); ?></td>
				<td align="right"><? //echo number_format($total_recv_qty,2); ?></td>
				<td align="right"><? //echo number_format($total_wo_val,2); ?></td>
				<td align="right"><? echo number_format($total_recv_value,2); ?></td>
				<td align="right"><? //echo number_format($total_issue_qty,2); ?></td>
                <td align="right"><? echo number_format($total_issue_amount,2); ?></td>
				<td align="right"><? //echo number_format($total_left_over,2); ?></td>
				<td>&nbsp;</td>
				<td align="right"><? echo number_format($total_left_val,2); ?></td>
			</tr>
		</table>
		</div>
        <br />
        <p style="text-align:left; padding-left:10px; font-size:16px; font-weight:bold">Summary Report</p>
        <br />
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1200" class="rpt_table" align="left">
        	<thead>
                <tr>
                    <th width="50">SL </th>
                    <th width="170">Item Group</th>
                    <th width="70">UOM</th>
                    <th width="100">Req Qty</th>
                    <th width="100">WO Qty</th>
                    <th width="100">WO %</th>
                    <th width="100">In-House Qty</th>
                    <th width="100">In-House %</th>
                    <th width="100">In-House Balance Qty</th>
                    <th width="100">Issue Qty</th>
                    <th width="100">Issue %</th>
                    <th>Left Over Qty</th>
                </tr>
            </thead>
            <tbody>
				<?
				$i=1;
                foreach($item_group_wise_data as $item_id=>$val)
                {
					$wo_qnty_percent=(($val["wo_qty"]/$val["req_qnty"])*100);
					$recv_qnty_percent=(($val["recv_qnty"]/$val["wo_qty"])*100);
					$recv_qnty_bal=$val["wo_qty"]-$val["recv_qnty"];
					$issue_qnty_percent=(($val["issue_qnty"]/$val["recv_qnty"])*100);
					$left_overs_qnty=$val["recv_qnty"]-$val["issue_qnty"];

					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td align="center"><? echo $i;?></td>
                        <td title="<? echo $item_id; ?>"><p><? echo $trim_group[$item_id] ; ?>&nbsp;</p></td>
                        <td align="right"><? echo $unit_of_measurement[$val["uom"]];?></td>
                        <td align="right"><? echo number_format($val["req_qnty"],2);?></td>
                        <td align="right"><? echo number_format($val["wo_qty"],2);?></td>
                        <td align="right"><? echo number_format($wo_qnty_percent,2);?></td>
                        <td align="right"><? echo number_format($val["recv_qnty"],2);?></td>
                        <td align="right"><? echo number_format($recv_qnty_percent,2);?></td>
                        <td align="right"><? echo number_format($recv_qnty_bal,2);?></td>
                        <td align="right"><? echo number_format($val["issue_qnty"],2);?></td>
                        <td align="right"><? echo number_format($issue_qnty_percent,2);?></td>
                        <td align="right"><? echo number_format($left_overs_qnty,2);?></td>
                    </tr>
					<?
                    $i++;
                }
                ?>
            </tbody>
            <tfoot>
            </tfoot>
        </table>
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
    echo "$html**$filename**5";
    exit();
}

if ($action=="report_generate_style_color_size")// Item Color Size
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company=str_replace("'","",$cbo_company_id);
	$cbo_buyer=str_replace("'","",$cbo_buyer_id);
	$color_library=return_library_array("select id,color_name from lib_color", "id", "color_name");
	$color_size_library=return_library_array("select id,size_name from lib_size", "id", "size_name");
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
				}
				//echo "<pre>";
				//print_r($book_wo_qty_arr); die;

				 //var_dump($book_wo_qty_arr);die;


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
				//echo $poIds_cond2; die;
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
									$trim_issue_qty=0;$trim_recv_qty=0; $req_qty='';
									foreach($job_po_id as $po_id)
									{
										//echo $item_size.'<br>';
										$item_color=$book_wo_qty_arr[$po_id][$item_group_id][$item_color_id]['color'];
										$color_number_id_book=$book_wo_qty_arr[$po_id][$item_group_id][$item_color_id]['color_number'];
										$gmt_size=$book_wo_qty_arr[$po_id][$item_group_id][$item_color_id]['gmts_size'];
										$item_size_book=$book_wo_qty_arr[$po_id][$item_group_id][$item_color_id][$item_size]['item_size'];
										$gmts_size_book=$book_wo_qty_arr[$po_id][$item_group_id][$item_color_id][$item_size]['gmts_size'];
										$size_data=$size_data_arr[$item_size_book];
										//$sensitivity_book=$size_sensitivity_arr[$po_id][$item_group_id];

										

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
										//echo $recv_sensitivity;
										if($recv_sensitivity==1 || $recv_sensitivity==3)
										{
											 $trim_recv_qty+=$recv_qty_arr[$po_id][$item_group_id][$item_color_id][0];
											 $req_qty=$book_wo_qty_arr[$po_id][$item_group_id][$item_color_id][0]['quantity'];
										}
										else if($recv_sensitivity==2)
										{

											$trim_recv_qty+=$recv_qty_arr[$po_id][$item_group_id][$item_size][0];
											//$req_qty=$book_wo_qty_arr[$po_id][$item_group_id][$item_size][0]['quantity']; hayder vai
										}
										else
										{
											$req_qty=$book_wo_qty_arr[$po_id][$item_group_id][$item_color_id][$item_size]['quantity'];
											//echo $gmts_size_book;
											$gmt_size=$gmts_sizeArray[$item_group_id][$item_color];
											$gmt_color_id=$gmts_colorArray[$item_group_id][$item_color];
											//echo $item_color_id.'<br>';
											//echo $recv_qty_arr[$po_id][$item_group_id][$item_color_id][$gmt_size];
											$trim_recv_qty+=$recv_qty_arr[$po_id][$item_group_id][$item_color_id][$gmt_size];
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
											<td width="130" rowspan="<? echo $rowspan_array[$item_group_id]; ?>"><p><? echo $trim_group[$item_group_id];//.'<br>'.$size_color_sensitive[$sensitivity_book]; ?></p></td>
											<td width="60" align="center" rowspan="<? echo $rowspan_array[$item_group_id]; ?>"><p><? echo $unit_of_measurement[$uomArray[$item_group_id]]; ?></p></td>
										<?
										}
										if($c==0)
										{
										?>
											<td width="100" rowspan="<? echo $rowspan_color_array[$item_group_id][$item_color_id]; ?>"><p><? echo $color_library[$item_color_id]; ?></p></td>
                                            <td width="100" rowspan="<? echo $rowspan_color_array[$item_group_id][$item_color_id]; ?>"><p><? echo $color_library[$item_color];//$color_library[$item_colorArray[$item_group_id][$item_color_id]]; ?></p></td>
										<?
										}
										$recv_balance_qty=$wo_qty-$trim_recv_qty;
										$left_over_qty=$trim_recv_qty-$trim_issue_qty;

										?>
										<td width="60"><p><? echo $color_size_library[$item_size];//$color_size_library[$item_size]; ?></p></td>
                                        <td width="60"><p><? echo $item_size_book; ?></p></td>
                                        <td width="90"><p><? echo $req_qty; ?></p></td>
                                      	<?
									  	if($sensitivity_book==1 || $sensitivity_book==3)
										{
											if($c==0)
											{
												?>
												<td width="90" rowspan="<? echo $rowspan_color_array[$item_group_id][$item_color_id]; ?>" align="right"><? echo number_format($wo_qty,2); ?></td>
												<td width="90" rowspan="<? echo $rowspan_color_array[$item_group_id][$item_color_id]; ?>" align="right"><a href='#report_details' onClick="openmypage('<? echo $job_poid; ?>','<? echo $item_group_id; ?>','<? echo $item_color_id; ?>','<? echo $item_size; ?>','<? echo $gmt_size; ?>','<? echo $recv_basis; ?>','<? echo $without_order; ?>',2,'size_receive_popup');"><?  echo number_format($trim_recv_qty,2); ?></a> <? //echo number_format($trim_recv_qty,2); ?></td>
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
                                            <td width="90" align="right"><a href='#report_details' onClick="openmypage('<? echo $job_poid; ?>','<? echo $item_group_id; ?>','<? echo $item_color_id; ?>','<? echo $item_size; ?>','<? echo $gmt_size; ?>','<? echo $recv_basis; ?>','<? echo $without_order; ?>',2,'size_receive_popup');"><?  echo number_format($trim_recv_qty,2); ?></a><? //echo number_format($trim_recv_qty,2); ?></td>
                                            <td width="100" align="right"><? echo number_format($recv_balance_qty,2); ?></td>
                                            <td width="90" align="right"><a href='#report_details' onClick="openmypage('<? echo $job_poid; ?>','<? echo $item_group_id; ?>','<? echo $item_color_id; ?>','<? echo $gmts_color; ?>','<? echo $item_size; ?>','<? echo $recv_basis; ?>','<? echo $without_order; ?>',3,'size_issue_popup');"><?  echo number_format($trim_issue_qty,2); ?></a><? //echo number_format($trim_issue_qty,2); ?></td>
                                            <td width="" align="right"><? echo number_format($left_over_qty,2); ?></td>
											<?
										}
										?>
									</tr>
									<?
                                    $total_wo_qty+=$wo_qty;
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
                    <td align="right"><? //echo number_format($total_recv_qty,2); ?></td>
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
    <script>

	function openmypage_booking(txt_booking_no,cbo_company_name,id_approved_id,entry_form,action)
	{
		var show_comment='';
		var r=confirm("Press  \"Cancel\"  to hide  Remarks\nPress  \"OK\"  to Show Remarks");
		if (r==true){
			show_comment="1";
		}
		else{
			show_comment="0";
		}
		var report_type=1;
		var report_title="Accessories Details V2";
		var data="action="+action+'&report_title='+report_title+'&show_comment='+show_comment+'&report_type='+report_type+'&txt_booking_no='+txt_booking_no+'&cbo_company_name='+cbo_company_name+'&id_approved_id='+id_approved_id;
		http.open("POST","../../../../order/woven_order/requires/trims_booking_multi_job_controllerurmi.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = generate_trim_report_reponse;
	}

	function generate_trim_report_reponse()
	{
		if(http.readyState == 4){
			var file_data=http.responseText.split("****");
			//$('#pdf_file_name').html(file_data[1]);
			//$('#data_panel').html(file_data[0]);
			var w = window.open("Surprise", "_blank");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../../../css/prt.css" type="text/css" /><title></title></head><body>'+file_data[0]+'</body</html>');
			d.close();
		}
	}

	</script>
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
					//echo $item_group;
					if($item_group!=0) $item_group_cond="and b.trim_group=$item_group";else $item_group_cond="";
					if($item_color!=0) $item_color_cond="and c.item_color=$item_color";else $item_color_cond="";
					//echo $item_size;die;
					if($item_size) $item_size_cond="and c.item_size='$item_size'"; else $item_size_cond="";
					if(trim($item_desc)) $item_desc_cond="and c.description='$item_desc'"; else $item_desc_cond="";
					//$item_desc_cond="and c.description='$item_desc'";
					/*$mrr_sql="select a.id, a.recv_number, a.receive_date, SUM(c.quantity) as quantity
					from inv_receive_master a, inv_trims_entry_dtls b, order_wise_pro_details c
					where a.id=b.mst_id  and a.entry_form=24 and c.entry_form=24  and b.id=c.dtls_id and c.trans_type=1  and  c.po_breakdown_id in($po_id)  and a.company_id='$companyID' and b.item_group_id='$item_group' and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 group by    c.po_breakdown_id,b.item_group_id,a.recv_number,a.id,a.receive_date";*/

					$sql_bookingqty =("SELECT a.booking_no, a.booking_date, a.entry_form, a.company_id, a.is_approved, c.cons as wo_qnty, b.id as dtls_id, c.id,b.trim_group as item_group,  c.description,c.brand_supplier,b.po_break_down_id as po_id,b.sensitivity
					from wo_booking_mst a, wo_booking_dtls b, wo_trim_book_con_dtls c
					where a.booking_no=b.booking_no and b.id=c.wo_trim_booking_dtls_id and b.booking_no=c.booking_no and a.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and wo_qnty>0 and b.po_break_down_id in($po_id) $item_group_cond $item_color_cond $item_size_cond $item_desc_cond");

					//echo $sql_bookingqty; die;
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
                            <td width="100"><p><a href='#report_details' onClick="openmypage_booking('<? echo $row[csf('booking_no')]; ?>','<? echo $row[csf('company_id')]; ?>','<? echo $row[csf('is_approved')]; ?>','<? echo $row[csf('entry_form')]; ?>','show_trim_booking_report2');"><? echo $row[csf('booking_no')]; ?></a></p></td>
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
                    <th width="90">Trans_type</th>
                    <th width="110">Recv. ID</th>
                    <th width="90">Chalan No</th>
                    <th width="70">Recv. Date</th>
                    <th width="120">Item Desc.</th>
                    <th width="80">Recv. Qty.</th>
                    <th>Reject Qty.</th>
				</thead>
                <tbody>
                <?
					$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details"  );
					$i=1;
					$item_color=str_replace("'","",$item_color);
					$item_size=str_replace("'","",$item_size);
					//echo $item_color.'=='.$item_size;
					$item_color_con="";
					$item_color_con2="";
					if($item_color)
					{
						$item_color_con=" and b.item_color in($item_color)";
						$item_color_con2=" and d.item_color in($item_color)";
					}
					//echo $item_size.jahid;die;
					$item_size_con="";
					$item_size_con2="";
					if($item_size)
					{
						$item_size_con=" and b.item_size='$item_size'";
						$item_size_con2=" and d.item_size='$item_size'";
					}
					$item_desc_con="";
					$item_desc_con2="";
					if($item_desc)
					{
						$item_desc_con2=" and (trim(d.item_description)='".trim($item_desc)."' or trim(d.item_description)='".trim($item_desc).", [BS]"."')";
					}
					//$item_desc_con2=" and d.item_description='$item_desc'";

					//if($db_type==0) $null_val="c.color_number_id,c.item_color,c.item_size,c.gmts_sizes,";
					//else if($db_type==2) $null_val="nvl(c.color_number_id,0) as color_number_id,nvl(c.item_color,0) as item_color,nvl(c.gmts_sizes,0) as gmts_sizes,c.item_size,";



				   /*$mrr_sql="select a.id, a.recv_number, a.challan_no, a.receive_date, c.quantity as quantity, b.reject_receive_qnty as reject_qty, b.item_description, c.prod_id, c.trans_type
					from inv_receive_master a, inv_trims_entry_dtls b, order_wise_pro_details c
					where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(24,49) and c.entry_form in(24,49) and c.trans_type in(1,4)  and  c.po_breakdown_id in($po_id)  and a.company_id='$companyID' and b.item_group_id='$item_group' and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 $item_color_con $item_size_con
					union all
					select a.id, a.transfer_system_id as recv_number, a.challan_no, a.transfer_date as receive_date, c.quantity as quantity, 0 as reject_qty, d.item_description, c.prod_id, c.trans_type
					from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, product_details_master d
					where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.entry_form in(78,112) and c.entry_form in(78,112) and c.trans_type in(5)  and  c.po_breakdown_id in($po_id)  and a.company_id='$companyID' and d.item_group_id='$item_group' and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 $item_color_con2 $item_size_con2";*/


					$mrr_sql="select a.id, a.recv_number, a.challan_no, a.receive_date, c.quantity as quantity, b.cons_reject_qnty as reject_qty, d.item_description, c.prod_id, c.trans_type
					from inv_receive_master a, inv_transaction b, order_wise_pro_details c, product_details_master d
					where a.id=b.mst_id and b.id=c.trans_id and c.prod_id=d.id and a.entry_form in(24,73) and c.entry_form in(24,73) and b.transaction_type in(1,3) and c.trans_type in(1,3) and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and d.item_group_id='$item_group' and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 $item_color_con2 $item_size_con2 $item_desc_con2
					union all
					select a.id, a.issue_number, a.challan_no, a.issue_date, c.quantity as quantity, b.cons_reject_qnty as reject_qty, d.item_description, c.prod_id, c.trans_type 
					from inv_issue_master a, inv_transaction b, order_wise_pro_details c, product_details_master d 
					where a.id=b.mst_id and b.id=c.trans_id and c.prod_id=d.id and a.entry_form in(49) and c.entry_form in(49) and b.transaction_type in(3) and c.trans_type in(3) and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and d.item_group_id='$item_group' and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 $item_color_con2 $item_size_con2 $item_desc_con2 ";
					//echo $mrr_sql;
					/*union all
					select a.id, a.transfer_system_id as recv_number, a.challan_no, a.transfer_date as receive_date, c.quantity as quantity, 0 as reject_qty, d.item_description, c.prod_id, c.trans_type
					from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, product_details_master d
					where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.entry_form in(78,112) and c.entry_form in(78,112) and c.trans_type in(5)  and  c.po_breakdown_id in($po_id)  and a.company_id='$companyID' and d.item_group_id='$item_group' and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 $item_color_con2 $item_size_con2 $item_desc_con2*/
					//echo $mrr_sql;
					// $issue_qty_sql="select b.po_breakdown_id, a.item_color as item_color_id, a.item_size, a.item_group_id, b.quantity, c.cons_rate as rate, b.entry_form, b.trans_type
					// from product_details_master a , order_wise_pro_details b, inv_transaction c
					// where a.id=b.prod_id and b.trans_id=c.id and item_category_id=4 and a.entry_form=24 and b.entry_form in(25,49,73,78,112) and b.trans_type in(2,3,4,5,6) and c.transaction_type in(2,3,4,5,6) and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
					
					

					// $p=1;
					// if($po_id!="")
					// {
					// 	$all_order_id_arr=array_chunk(array_unique(explode(",",$po_id)),999);
					// 	foreach($all_order_id_arr as $order_id)
					// 	{
					// 		if($p==1) $issue_qty_sql .=" and (b.po_breakdown_id in(".implode(',',$order_id).")"; else $issue_qty_sql .=" or b.po_breakdown_id  in(".implode(',',$order_id).")";
					// 		$p++;
					// 	}
					// 	$issue_qty_sql .=" )";
					// }
					// //echo $issue_qty_sql;//die;
					// $issue_qty_sql_result=sql_select($issue_qty_sql);
					// $issue_data_arr=array();
					// foreach($issue_qty_sql_result as $row)
					// {
					// 	if($row[csf('entry_form')]==49 && $row[csf('trans_type')]==3)
					// 	{
					// 		$issue_data_arr[$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][$row[csf('item_color_id')]][$item_size_id]["rcv_rtn_quantity"]+=$row[csf('quantity')];
					// 	}
					// }
					//echo "<pre>";
					//print_r($issue_data_arr);

					$dtlsArray=sql_select($mrr_sql);
					$tot_reject_qty=0;$tot_qty=0;
					foreach($dtlsArray as $row)
					{
						if ($i%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
						if($row[csf('quantity')]>0 && ($row[csf('trans_type')]==1 || $row[csf('trans_type')]==5))
						{
							if($row[csf('trans_type')]==1) $trans_type="Receive";
							
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
							$tot_qty+=$row[csf('quantity')];
							$tot_reject_qty+=$row[csf('reject_qty')];
							$i++;						
						?>
						<tr bgcolor="#e7ffae">
							<td colspan="7" align="right"><strong>Total</strong></td>						
							<td align="right"><strong><? echo number_format($tot_qty,2); ?></strong></td>
							<td align="right"><strong><? echo number_format($tot_reject_qty,2); ?></strong></td>
						</tr>
						<?
						}
						if($row[csf('quantity')]>0 && $row[csf('trans_type')]==3)
						{
							
								if($row[csf('trans_type')]==3) $trans_type="Receive Rtn";
								?>
								<tr><td colspan="9" align="center"><strong>Recevied Return Detail</strong></td></tr>
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
								$tot_rcv_rtn_qty+= $row[csf('quantity')]*1;
								$tot_rcv_rtn_reject_qty+=$row[csf('reject_qty')]*1;

								$grand_total = $tot_qty-$tot_rcv_rtn_qty;
								$grand_total_reject = $tot_reject_qty-$tot_reject_qty;
								$i++;
							
							?>
							<tr bgcolor="#e7ffae">
								<td colspan="7" align="right"><strong>Total</strong></td>						
								<td align="right"><strong><? echo number_format($tot_rcv_rtn_qty,2); ?></strong></td>
								<td align="right"><strong><? echo number_format($tot_rcv_rtn_reject_qty,2); ?></strong></td>
							</tr>
							<tr bgcolor="#ffc4b2">
								<td colspan="7" align="right"><strong>Grand Total</strong></td>						
								<td align="right"><strong><? echo number_format($grand_total,2); ?></strong></td>
								<td align="right"><strong><? echo number_format($grand_total_reject,2); ?></strong></td>
							</tr>
							<?
						}
					}
				?>
                </tbody>
                <!-- <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="7" align="right">Total</td>
                        <td align="right"><? //echo number_format($tot_qty,2); ?>&nbsp;</td>
                         <td align="right"><? //echo number_format($tot_reject_qty,2); ?>&nbsp;</td>
                    </tr>
                </tfoot> -->
            </table>
            <br>

        </div>
    </fieldset>
    <?
	exit();
}

if($action=="transfer_popup")
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);

	?>
	<fieldset style="width:730px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="730" cellpadding="0" cellspacing="0" align="center">
				<caption><strong>Transfer IN Details</strong></caption>
                <thead>
                    <th width="30">Sl</th>
                    <th width="50">Prod. ID</th>
                    <th width="90">Trans_type</th>
                    <th width="110">Transfer ID</th>
                    <th width="90">Chalan No</th>
                    <th width="70">Transfer. Date</th>
                    <th width="220">Item Desc.</th>
                    <th>Transfer. Qty.</th>
				</thead>
                <tbody>
                <?
					$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details"  );
					$i=1;
					$item_color=str_replace("'","",$item_color);
					$item_size=str_replace("'","",$item_size);
					//echo $item_color.'=='.$item_size;
					$item_color_con="";
					$item_color_con2="";
					if($item_color)
					{
						$item_color_con=" and b.item_color in($item_color)";
						$item_color_con2=" and d.item_color in($item_color)";
					}
					//echo $item_size.jahid;die;
					$item_size_con="";
					$item_size_con2="";
					if($item_size)
					{
						$item_size_con=" and b.item_size='$item_size'";
						$item_size_con2=" and d.item_size='$item_size'";
					}
					$item_desc_con="";
					$item_desc_con2="";
					if($item_desc)
					{
						$item_desc_con2=" and trim(d.item_description)='".trim($item_desc)."'";
					}
					

					$mrr_sql="select a.id, a.transfer_system_id as recv_number, a.challan_no, a.transfer_date as receive_date, c.quantity as quantity, 0 as reject_qty, d.item_description, c.prod_id, c.trans_type
					from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, product_details_master d
					where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.entry_form in(78,112) and c.entry_form in(78,112) and c.trans_type in(5)  and  c.po_breakdown_id in($po_id)  and d.item_group_id='$item_group' and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 $item_color_con2 $item_size_con2 $item_desc_con2
					";
					

					$dtlsArray=sql_select($mrr_sql);
					$tot_reject_qty=0;$tot_qty=0;
					foreach($dtlsArray as $row)
					{
						if ($i%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
						if($row[csf('quantity')]>0 && ($row[csf('trans_type')]==1 || $row[csf('trans_type')]==5))
						{
							if($row[csf('trans_type')]==1) $trans_type="Receive";
							
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
							</tr>
							<?
							$tot_qty+=$row[csf('quantity')];
							$tot_reject_qty+=$row[csf('reject_qty')];
							$i++;						
						?>
						<tr bgcolor="#e7ffae">
							<td colspan="7" align="right"><strong>Total</strong></td>						
							<td align="right"><strong><? echo number_format($tot_qty,2); ?></strong></td>
						</tr>
						<?
						}
					}
				?>
                </tbody>
                <!-- <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="7" align="right">Total</td>
                        <td align="right"><? //echo number_format($tot_qty,2); ?>&nbsp;</td>
                         <td align="right"><? //echo number_format($tot_reject_qty,2); ?>&nbsp;</td>
                    </tr>
                </tfoot> -->
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

					//echo $mrr_sql;
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
    
 	<script>
	 	function new_window()
		{
			$('#scroll_body tbody tr:first').hide();
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
			d.close();
			$('#scroll_body tbody tr:first').show();
		}
	</script>   
	<fieldset style="width:910px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="910" cellpadding="0" cellspacing="0" align="left" >
				<caption><strong>Issue Details</strong> </caption>
                <thead>
                    <th width="30">Sl</th>
                    <th width="100">Order No</th>
                    <th width="50">Prod. ID</th>
                    <th width="90">Trans type</th>
                    <th width="130">Issue. ID</th>
                    <th width="80">Chalan No</th>
                    <th width="80">Issue. Date</th>
                    <th width="120">Item Desc.</th>
                    <th width="80">Issue. Qty.</th>
                    <th width="80">Sewing Floor</th>
                    <th>Sewing Line</th>
				</thead>
			</table>
                <?
					$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details"  );
					$line_arr=return_library_array( "select id,line_name from lib_sewing_line where is_deleted=0 and status_active=1", "id", "line_name");
					$floor_arr=return_library_array( "select id,floor_name from lib_prod_floor where is_deleted=0 and status_active=1", "id", "floor_name");
					$po_arr=return_library_array( "select id,po_number from wo_po_break_down where is_deleted=0 and status_active=1", "id", "po_number");

					$i=1;
					$item_color=str_replace("'","",$item_color);
					$item_size=str_replace("'","",$item_size);
					//echo $item_color.'=='.$item_size;
					//if($item_color==0 || $item_color=='') $item_color_con="";else $item_color_con="and b.item_color_id=$item_color";
					//if($item_size==0 || $item_size=='')  $item_size_con="";else $item_size_con="and b.item_size='$item_size'";


					//echo $item_size.jahid;die;

					$item_color_con2="";$item_size_con2=""; $item_desc_con2="";
					if($item_color)
					{
						$item_color_con2=" and d.item_color in($item_color)";
					}
					if($item_size)
					{
						$item_size_con2=" and d.item_size='$item_size'";
					}
					if($item_desc)
					{
						$item_desc_con2=" and (d.item_description='$item_desc' or d.item_description='$item_desc".', [BS]'."')";
					}
					//echo $item_color_con.'=='.$item_size_con;
				 	$mrr_sql="select a.id, a.issue_number, a.challan_no, c.prod_id, a.issue_date, d.item_description, c.quantity as quantity, b.sewing_line,b.floor_sewing, c.trans_type,c.po_breakdown_id
					from  inv_issue_master a, inv_trims_issue_dtls b, order_wise_pro_details c,product_details_master d
					where a.id=b.mst_id and b.id=c.dtls_id and b.trans_id=c.trans_id and c.prod_id=d.id and a.entry_form in(25) and c.trans_type in(2) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and d.item_group_id='$item_group' $item_color_con2 $item_size_con2 $item_desc_con2
					union all
					select a.id, a.transfer_system_id as issue_number, a.challan_no, c.prod_id, a.transfer_date as issue_date, d.item_description, c.quantity as quantity, 0 as sewing_line, 0 as floor_sewing, c.trans_type, c.po_breakdown_id
					from  inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c,product_details_master d
					where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.entry_form in(78,112) and c.trans_type in(6) and a.is_deleted=0 and a.status_active=1 and
					b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and d.item_group_id='$item_group' and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 $item_color_con2 $item_size_con2 $item_desc_con2
					union all
					select a.id, a.recv_number, a.challan_no, c.prod_id, a.receive_date, d.item_description, c.quantity as quantity, 0 as sewing_line, 0 as floor_sewing, c.trans_type, c.po_breakdown_id 
					from inv_receive_master a, inv_transaction b, order_wise_pro_details c,product_details_master d 
					where a.id=b.mst_id and b.id=c.trans_id and c.prod_id=d.id and a.entry_form in(73) and b.transaction_type in(4) and c.trans_type in(4) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and d.item_group_id='$item_group' $item_color_con2 $item_size_con2 $item_desc_con2"; 

					//echo $mrr_sql;
					$dtlsArray=sql_select($mrr_sql);
					?>
					<table border="1" class="rpt_table" rules="all" width="910" cellpadding="0" cellspacing="0" align="left" id="list_view">
					<tbody>
					<?
					foreach($dtlsArray as $row)
					{
						if ($i%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
						if($row[csf('quantity')]>0 && ($row[csf('trans_type')]==2 || $row[csf('trans_type')]==6))
						{
							if($row[csf('trans_type')]==2) $trans_type="Issue";
							//else if($row[csf('trans_type')]==3) $trans_type="Receive Rtn";
							else $trans_type="Transfer Out";
							?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td align="center" width="30"><p><? echo $i; ?></p></td>
								<td align="center" width="100" style="word-wrap: break-word; word-break: break-all"><? echo $po_arr[$row[csf('po_breakdown_id')]]; ?></td>
								<td align="center" width="50"><p><? echo $row[csf('prod_id')]; ?></p></td>
								<td align="center" width="90"><p><? echo $trans_type; ?></p></td>
								<td width="130"><p><? echo $row[csf('issue_number')]; ?></p></td>
								<td align="center" width="80"><p><? echo $row[csf('challan_no')]; ?></p></td>
								<td align="center" width="80"><p><? echo  change_date_format($row[csf('issue_date')]); ?></p></td>
								<td width="120"><p><? echo $row[csf('item_description')]; ?></p></td>
								<td align="right" width="80"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
								<td align="center" width="80"><p><? echo $floor_arr[$row[csf('floor_sewing')]]; ?></p></td>
								<td align="center" ><p><? echo $line_arr[$row[csf('sewing_line')]]; ?></p></td>
							</tr>
							<?
							$tot_qty+=$row[csf('quantity')];
							$i++;
							?>
							<tr bgcolor="#e7ffae">
								<td colspan="8" align="right"><strong>Total</strong></td>						
								<td align="right"><strong><? echo number_format($tot_qty,2); ?></strong></td>
								<td align="right"><strong></strong></td>
								<td align="right"><strong></strong></td>
							</tr>
							<?
						}
						if($row[csf('quantity')]>0 && $row[csf('trans_type')]==4)
						{
								
								if($row[csf('trans_type')]==4) $trans_type="Issue Rtn";
								?>
							<tr><td colspan="11" align="center"><strong>Issue Return Detail</strong></td></tr>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td align="center" width="30"><p><? echo $i; ?></p></td>
								<td align="center" width="100" style="word-wrap: break-word; word-break: break-all"><? echo $po_arr[$row[csf('po_breakdown_id')]]; ?></td>
								<td align="center" width="50"><p><? echo $row[csf('prod_id')]; ?></p></td>
								<td align="center" width="90"><p><? echo $trans_type; ?></p></td>
								<td width="130"><p><? echo $row[csf('issue_number')]; ?></p></td>
								<td align="center" width="80"><p><? echo $row[csf('challan_no')]; ?></p></td>
								<td align="center" width="80"><p><? echo  change_date_format($row[csf('issue_date')]); ?></p></td>
								<td width="120"><p><? echo $row[csf('item_description')]; ?></p></td>
								<td align="right" width="80"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
								<td align="center" width="80"><p><? echo $floor_arr[$row[csf('floor_sewing')]]; ?></p></td>
								<td align="center" ><p><? echo $line_arr[$row[csf('sewing_line')]]; ?></p></td>
							</tr>
							<?
								$tot_issue_rtn_qty+= $row[csf('quantity')]*1;
								//$tot_rcv_rtn_reject_qty+=$row[csf('reject_qty')]*1;

								$grand_total = $tot_qty-$tot_issue_rtn_qty;
								//$grand_total_reject = $tot_reject_qty-$tot_reject_qty;
								$i++;
							
							?>
							<tr bgcolor="#e7ffae">
								<td colspan="8" align="right"><strong>Total</strong></td>						
								<td align="right"><strong><? echo number_format($tot_issue_rtn_qty,2); ?></strong></td>
								<td align="right"></td>
								<td align="right"></td>
							</tr>
							<tr bgcolor="#ffc4b2">
								<td colspan="8" align="right"><strong>Grand Total</strong></td>						
								<td align="right"><strong><? echo number_format($grand_total,2); ?></strong></td>
								<td align="right"></td>
								<td align="right"></td>
							</tr>
							<?
							
						}
					}
				?>
                </tbody>
                <!-- <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="7" align="right"></td>
                        <td align="right">Total</td>
                        <td align="right"><? //echo number_format($tot_qty,2); ?></td>
                        <td colspan="2">&nbsp;</td>
                    </tr>
                </tfoot> -->
            </table>
        </div>
        <table  border="1" class="rpt_table" rules="all" width="910" cellpadding="0" cellspacing="0" align="center">
        	<tr>
        		<td align="center">
        			<input type="button" name="isuue_print" style="width:43px" class="formbutton" onclick="new_window()" value="Print">
        		</td>
        	</tr>
        </table>
        
        <!-- <a href="javascript:new_window();">dddddd</a>  -->
    </fieldset>
    <script type="text/javascript">
    	setFilterGrid('list_view',-1);
    </script>
    <?
	exit();
}

if($action=="transfer_out_popup")
{
	echo load_html_head_contents("Transfer Out Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
 	<script>
	 	function new_window()
		{
			$('#scroll_body tbody tr:first').hide();
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
			d.close();
			$('#scroll_body tbody tr:first').show();
		}
	</script>   
	<fieldset style="width:910px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="910" cellpadding="0" cellspacing="0" align="left" >
				<caption><strong>Issue Details</strong> </caption>
                <thead>
                    <th width="30">Sl</th>
                    <th width="100">Order No</th>
                    <th width="50">Prod. ID</th>
                    <th width="90">Trans type</th>
                    <th width="130">Transfer Out ID</th>
                    <th width="80">Chalan No</th>
                    <th width="80">Transfer Out Date</th>
                    <th width="220">Item Description</th>
                    <th>Transfer Out Qty.</th>
				</thead>
			</table>
                <?
					$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details"  );
					$line_arr=return_library_array( "select id,line_name from lib_sewing_line where is_deleted=0 and status_active=1", "id", "line_name");
					$floor_arr=return_library_array( "select id,floor_name from lib_prod_floor where is_deleted=0 and status_active=1", "id", "floor_name");
					$po_arr=return_library_array( "select id,po_number from wo_po_break_down where is_deleted=0 and status_active=1", "id", "po_number");

					$i=1;
					$item_color=str_replace("'","",$item_color);
					$item_size=str_replace("'","",$item_size);
					//echo $item_color.'=='.$item_size;
					//if($item_color==0 || $item_color=='') $item_color_con="";else $item_color_con="and b.item_color_id=$item_color";
					//if($item_size==0 || $item_size=='')  $item_size_con="";else $item_size_con="and b.item_size='$item_size'";


					//echo $item_size.jahid;die;

					$item_color_con2="";$item_size_con2=""; $item_desc_con2="";
					if($item_color)
					{
						$item_color_con2=" and d.item_color in($item_color)";
					}
					if($item_size)
					{
						$item_size_con2=" and d.item_size='$item_size'";
					}
					if($item_desc)
					{
						$item_desc_con2=" and d.item_description='$item_desc'";
					}
					//echo $item_color_con.'=='.$item_size_con;
				 	$mrr_sql="select a.id, a.transfer_system_id as issue_number, a.challan_no, c.prod_id, a.transfer_date as issue_date, d.item_description, c.quantity as quantity, 0 as sewing_line, 0 as floor_sewing, c.trans_type, c.po_breakdown_id
					from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, product_details_master d
					where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.entry_form in(78,112) and c.trans_type in(6) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and d.item_group_id='$item_group' and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 $item_color_con2 $item_size_con2 $item_desc_con2"; 

					//echo $mrr_sql;
					$dtlsArray=sql_select($mrr_sql);
					?>
					<table border="1" class="rpt_table" rules="all" width="910" cellpadding="0" cellspacing="0" align="left" id="list_view">
					<tbody>
					<?
					foreach($dtlsArray as $row)
					{
						if ($i%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
						if($row[csf('quantity')]>0 && ($row[csf('trans_type')]==2 || $row[csf('trans_type')]==6))
						{
							if($row[csf('trans_type')]==2) $trans_type="Issue";
							//else if($row[csf('trans_type')]==3) $trans_type="Receive Rtn";
							else $trans_type="Transfer Out";
							?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td align="center" width="30"><p><? echo $i; ?></p></td>
								<td align="center" width="100" style="word-wrap: break-word; word-break: break-all"><? echo $po_arr[$row[csf('po_breakdown_id')]]; ?></td>
								<td align="center" width="50"><p><? echo $row[csf('prod_id')]; ?></p></td>
								<td align="center" width="90"><p><? echo $trans_type; ?></p></td>
								<td width="130"><p><? echo $row[csf('issue_number')]; ?></p></td>
								<td align="center" width="80"><p><? echo $row[csf('challan_no')]; ?></p></td>
								<td align="center" width="80"><p><? echo  change_date_format($row[csf('issue_date')]); ?></p></td>
								<td width="220"><p><? echo $row[csf('item_description')]; ?></p></td>
								<td align="right" ><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
							</tr>
							<?
							$tot_qty+=$row[csf('quantity')];
							$i++;
							?>
							<tr bgcolor="#e7ffae">
								<td colspan="8" align="right"><strong>Total</strong></td>						
								<td align="right"><strong><? echo number_format($tot_qty,2); ?></strong></td>
							</tr>
							<?
						}
					}
				?>
                </tbody>
                <!-- <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="7" align="right"></td>
                        <td align="right">Total</td>
                        <td align="right"><? //echo number_format($tot_qty,2); ?></td>
                        <td colspan="2">&nbsp;</td>
                    </tr>
                </tfoot> -->
            </table>
        </div>
        <table  border="1" class="rpt_table" rules="all" width="910" cellpadding="0" cellspacing="0" align="center">
        	<tr>
        		<td align="center">
        			<input type="button" name="isuue_print" style="width:43px" class="formbutton" onclick="new_window()" value="Print">
        		</td>
        	</tr>
        </table>
        
        <!-- <a href="javascript:new_window();">dddddd</a>  -->
    </fieldset>
    <script type="text/javascript">
    	setFilterGrid('list_view',-1);
    </script>
    <?
	exit();
}

if($action=="issue_popup_general")
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);

	?>
	<script>
	 	function new_window()
		{
			$('#scroll_body tbody tr:first').hide();
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
			d.close();
			$('#scroll_body tbody tr:first').show();
		}
	</script>
	<fieldset style="width:910px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="910" cellpadding="0" cellspacing="0" align="left">
				<caption>Issue Details </caption>
                <thead>
                    <th width="30">Sl</th>
                    <th width="100">Order No</th>
                    <th width="50">Prod. ID</th>
                    <th width="90">Trans type</th>
                    <th width="130">Issue. ID</th>
                    <th width="80">Challan No</th>
                    <th width="80">Issue. Date</th>
                    <th width="120">Item Desc.</th>
                    <th width="80">Issue. Qty.</th>
                    <th width="80">Sewing Floor</th>
                    <th>Sewing Line</th>
				</thead>
			</table>
                <?
					//$recv_basis;
				 	/*$mrr_sql="select a.id, a.issue_number, a.challan_no, b.prod_id, a.issue_date, d.item_description, b.cons_quantity as quantity,b.order_id
					from  inv_issue_master a, inv_transaction b, product_details_master d
					where a.id=b.mst_id and b.prod_id=d.id and a.entry_form in(21) and b.transaction_type in(2) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and b.order_id in($po_id) and a.company_id='$companyID' and d.id in($recv_basis)";*/

					/*$mrr_sql=("SELECT a.id, a.issue_number, a.challan_no, c.prod_id, a.issue_date, d.item_description, c.quantity AS quantity, b.sewing_line, b.floor_sewing, c.trans_type, c.po_breakdown_id as order_id
					FROM inv_issue_master a, inv_trims_issue_dtls b, order_wise_pro_details c, product_details_master d
					WHERE     a.id = b.mst_id AND b.id = c.dtls_id AND b.trans_id = c.trans_id AND c.prod_id = d.id AND a.entry_form IN (25) AND c.trans_type IN (2) AND a.is_deleted = 0 AND a.status_active = 1 AND b.status_active = 1 AND b.is_deleted = 0 AND c.po_breakdown_id IN ($po_id) AND a.company_id = '3' AND d.item_group_id = '2'
					UNION ALL
					SELECT a.id, a.issue_number, a.challan_no, c.prod_id, a.issue_date, d.item_description, c.quantity AS quantity, 0 AS sewing_line, 0 AS floor_sewing, c.trans_type, c.po_breakdown_id as order_id
					FROM inv_issue_master a, inv_transaction b, order_wise_pro_details c, product_details_master d
					WHERE     a.id = b.mst_id AND b.id = c.trans_id AND c.prod_id = d.id AND a.entry_form IN (49,21) AND b.transaction_type IN (3) AND c.trans_type IN (3) AND a.is_deleted = 0 AND a.status_active = 1 AND b.status_active = 1 AND b.is_deleted = 0 AND c.po_breakdown_id IN ($po_id) AND a.company_id = '3' AND d.item_group_id = '2'
					UNION ALL
					SELECT a.id, a.transfer_system_id AS issue_number, a.challan_no, c.prod_id, a.transfer_date AS issue_date, d.item_description, c.cons_quantity AS quantity, 0 AS sewing_line, 0 AS floor_sewing, c.transaction_type as trans_type, c.order_id
					FROM inv_item_transfer_mst a, inv_item_transfer_dtls b, inv_transaction c, product_details_master d
					WHERE     a.id = b.mst_id AND a.id = c.mst_id AND c.prod_id = d.id AND a.entry_form IN (78, 112,21) AND a.is_deleted = 0 AND a.status_active = 1 AND b.status_active = 1 AND b.is_deleted = 0 AND c.order_id IN ($po_id) AND a.company_id = '3' AND d.item_group_id = '2' AND a.is_deleted = 0 AND a.status_active = 1 AND b.status_active = 1 AND b.is_deleted = 0 ");*/

					$mrr_sql="select a.item_description, a.id as prod_id, b.floor_id, b.line_id, e.id, e.issue_number, e.challan_no, e.issue_date, b.transaction_type as trans_type,b.cons_quantity AS quantity,b.order_id
					from product_details_master a, inv_transaction b, wo_po_break_down c, wo_po_details_master d, inv_issue_master e
					where a.id=b.prod_id and b.order_id=c.id and c.job_no_mst=d.job_no and e.id = b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form<>24 and b.transaction_type in(2,3,6) and b.order_id IN ($po_id)";

					//echo $mrr_sql;floor_id,line_id
					$dtlsArray=sql_select($mrr_sql);
					//$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details"  );
					$line_arr=return_library_array( "select id,line_name from lib_sewing_line where is_deleted=0 and status_active=1", "id", "line_name");
					$floor_arr=return_library_array( "select id,floor_name from lib_prod_floor where is_deleted=0 and status_active=1", "id", "floor_name");
					$po_arr=return_library_array( "select id,po_number from wo_po_break_down where is_deleted=0 and status_active=1", "id", "po_number");

					?>
				<table border="1" class="rpt_table" rules="all" width="910" cellpadding="0" cellspacing="0" align="left" id="list_view">
					<tbody>
					<?
					$i=1;
					foreach($dtlsArray as $row)
					{
						if ($i%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
						if($row[csf('trans_type')]==2) $trans_type="Issue";
						else if($row[csf('trans_type')]==3) $trans_type="Receive Rtn";
						else if($row[csf('trans_type')]==5) $trans_type="Transfer IN";
						else $trans_type="Transfer Out";
						?>
                        <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td align="center" width="30" ><p><? echo $i; ?></p></td>
							<td align="center" width="100" style="word-wrap: break-word; word-break: break-all"><? echo $po_arr[$row[csf('order_id')]]; ?></td>
                            <td align="center" width="50"><p><? echo $row[csf('prod_id')]; ?></p></td>
                            <td align="center" width="90"><p><? echo $trans_type; ?></p></td>
                            <td width="130"><p><? echo $row[csf('issue_number')]; ?></p></td>
                            <td align="center" width="80"><p><? echo $row[csf('challan_no')]; ?></p></td>
                            <td align="center" width="80"><p><? echo  change_date_format($row[csf('issue_date')]); ?></p></td>
                            <td width="120"><p><? echo $row[csf('item_description')]; ?></p></td>
                            <td align="right" width="80"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
                            <td align="center" width="80"><p><? echo $floor_arr[$row[csf('floor_id')]]; ?></p></td>
                            <td align="center" ><p><? echo $line_arr[$row[csf('line_id')]]; ?></p></td>
                        </tr>
						<?
						$tot_qty+=$row[csf('quantity')];
						$i++;
					}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="7" align="right"></td>
                        <td align="right">Total</td>
                        <td><? echo number_format($tot_qty,2); ?></td>
                        <td colspan="2">&nbsp;</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <table  border="1" class="rpt_table" rules="all" width="910" cellpadding="0" cellspacing="0" align="center">
        	<tr>
        		<td align="center"><input type="button" name="isuue_print" style="width:43px" class="formbutton" onclick="new_window()" value="Print"></td>
        	</tr>
        </table>
    </fieldset>
    <script type="text/javascript">
    	setFilterGrid('list_view',-1);
    </script>
    <?
	exit();
}

if($action=="size_issue_popup")
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$color_library=return_library_array("select id,color_name from lib_color", "id", "color_name");
	?>
	<fieldset style="width:570px; margin-left:3px">
		<div id="scroll_body" align="center">
         <i>  Issue Return not Allowed</i>
			<table border="1" class="rpt_table" rules="all" width="560" cellpadding="0" cellspacing="0" align="center">
				 <caption>Issue Details </caption>
                <thead>
                    <th width="30">Sl</th>
                    <th width="70">Prod. ID</th>
                    <th width="100">Issue. ID</th>
                     <th width="100">Chalan No</th>
                     <th width="100">Issue. Date</th>
                    <th width="80">Item Desc.</th>
                    <th width="80">Issue. Qty.</th>
				</thead>
                <tbody>
                <?
					$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details"  );
					$i=1;
					$item_color=str_replace("'","",$item_color);
					$item_size=str_replace("'","",$item_size);
					//echo $item_color.'=='.$item_size;
					if($item_color==0 || $item_color=='') $item_color_con="";else $item_color_con="and b.item_color_id=$item_color";
					if($item_size==0 || $item_size=='')  $item_size_con="";else $item_size_con="and b.gmts_size_id='$item_size'";
					//echo $item_color_con.'=='.$item_size_con;
				 $mrr_sql=("select a.id, a.issue_number,a.challan_no,b.prod_id, a.issue_date,b.item_description,SUM(c.quantity) as quantity
					from  inv_issue_master a,inv_trims_issue_dtls b, order_wise_pro_details c,product_details_master p
					where a.id=b.mst_id  and a.entry_form=25  and p.id=b.prod_id and b.id=c.dtls_id and b.trans_id=c.trans_id and c.trans_type=2 and a.is_deleted=0 and a.status_active=1 and
					b.status_active=1 and b.is_deleted=0 and  c.po_breakdown_id in($po_id) and a.company_id='$companyID' and b.item_group_id='$item_group' $item_color_con $item_size_con group by c.po_breakdown_id,p.item_group_id,b.item_description,a.issue_number,a.id,a.issue_date,b.prod_id,a.challan_no ");

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
                            <td width="70" align="center"><p><? echo $row[csf('prod_id')]; ?></p></td>
                            <td width="100"><p><? echo $row[csf('issue_number')]; ?></p></td>
                            <td width="100" align="center"><p><? echo $row[csf('challan_no')]; ?></p></td>
                            <td width="100" align="center"><p><? echo  change_date_format($row[csf('issue_date')]); ?></p></td>
                            <td width="80" align="center"><p><? echo $row[csf('item_description')]; ?></p></td>
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
                    	<td colspan="5" align="right"></td>
                        <td align="right">Total</td>
                        <td><? echo number_format($tot_qty,2); ?></td>
                    </tr>
                </tfoot>
            </table>
            <br>

        </div>
    </fieldset>
    <?
	exit();
}

if($action=="req_popup")
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$size_library=return_library_array( "select id, size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name"  );
	$color_library=return_library_array("select id,color_name from lib_color", "id", "color_name");
	?>
	<fieldset style="width:860px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="860" cellpadding="0" cellspacing="0" align="center">
				<caption><strong>Req Detail</strong></caption>
                <thead>
                    <th width="50">Order Qty</th>
                    <th width="90">Color Name</th>
                    <th width="110">Color Qty</th>
                    <th width="90">Size</th>
                    <th width="70">Size qty</th>
                    <th width="120">Counsumption/Dzn</th>
                    <th width="80">Req Qty/Dzn</th>
                    <th width="80">Ex. %</th>
                    <th width="80">Total Cons</th>
                    <th>Total Qty</th>
				</thead>
                <tbody>
                <?
					
					$item_desc_con="";
					$trim_cost_id=$item_color;
					if($trim_cost_id)
					{
						$trim_cost_id_con=" and a.id='$trim_cost_id'";
					}

					$sql="SELECT a.id as trims_id, a.trim_group, a.description, b.cons, c.id as po_id, c.po_quantity, b.excess_per, b.color_number_id, b.item_size, b.size_number_id, b.job_no
					from wo_pre_cost_trim_cost_dtls a, wo_pre_cost_trim_co_cons_dtls b,  wo_po_break_down c, wo_po_details_master d 
					where a.job_no=b.job_no and d.job_no=a.job_no and a.id=b.wo_pre_cost_trim_cost_dtls_id and b.po_break_down_id=c.id and c.job_no_mst=d.job_no and d.company_name=$companyID
					and b.cons>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.trim_group=$item_group and c.id in($po_id) and a.id in($trim_cost_id) and b.wo_pre_cost_trim_cost_dtls_id in($trim_cost_id)
					group by a.id, a.trim_group, a.description, b.cons, c.id, c.po_quantity, b.excess_per, b.color_number_id, b.item_size, b.size_number_id, b.job_no
					order by c.id, b.color_number_id, b.size_number_id";
					//echo $sql;die;

					$dtlsArray=sql_select($sql);
					$po_row_span_arr=array(); $po_color_row_span_arr=array();
					foreach ($dtlsArray as $row)
					{
						$color_size_arr[$row[csf('trims_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['trim_group']=$row[csf('trim_group')];
						$color_size_arr[$row[csf('trims_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['description']=$row[csf('description')];
						$color_size_arr[$row[csf('trims_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['cons']=$row[csf('cons')];
						$color_size_arr[$row[csf('trims_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['po_id']=$row[csf('po_id')];
						$color_size_arr[$row[csf('trims_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['po_quantity']=$row[csf('po_quantity')];
						$color_size_arr[$row[csf('trims_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['excess_per']=$row[csf('excess_per')];
						$color_size_arr[$row[csf('trims_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['color_number_id']=$row[csf('color_number_id')];
						$color_size_arr[$row[csf('trims_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['item_size']=$row[csf('item_size')];
						$color_size_arr[$row[csf('trims_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['size_number_id']=$row[csf('size_number_id')];
						$color_size_arr[$row[csf('trims_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['job_no']=$row[csf('job_no')];

						$po_row_span_arr[$row[csf('po_id')]]++;
						$po_color_row_span_arr[$row[csf('po_id')]][$row[csf('color_number_id')]]++;
					}
					/*echo "<pre>";
					print_r($color_size_arr);die;*/

					$sql_plan="SELECT plan_cut_qnty, po_break_down_id, color_number_id, size_number_id
					from wo_po_color_size_breakdown 
					where po_break_down_id in($po_id) and status_active=1 and is_deleted=0";
					$result_arr=sql_select($sql_plan);
					$color_size_qty_arr=array();
					foreach ($result_arr as $row)
					{
						$color_size_qty_arr[$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('plan_cut_qnty')];
					}

					foreach($color_size_arr as $trims_id=>$trims_id_val)
					{
						foreach($trims_id_val as $color_id=>$color_number)
						{
							foreach ($color_number as $size_id=>$row) 
							{							
								//$qty_row_span++;
								//echo $color_number;
								$qty_row_span[$row['po_quantity']]++;
								$color_row_span[$color_id]++;
								$size_row_span[$color_id][$size_id]++;
							}
							//$color_gorup_row_span[$row['color_number_id']]=$qty_row_span;
						}
					}
					// echo '<pre>';print_r($qty_row_span);die;
					$condition= new condition();
					$condition->company_name("=$companyID");
					if(str_replace("'","",$po_id)!='')
					{
						$condition->po_id("in(".str_replace("'","",$po_id).")");
					}
					
					$condition->init();

					$trims= new trims($condition);
					//echo $trims->getQuery();die;
					$trims_qty_arr=$trims->getQtyArray_by_jobItemidDescriptionGmtcolorAndSizeid();
					// print_r($trims_qty_arr);die;
					$i=1; $tot_qty=0;
					foreach($color_size_arr as $trims_id=>$trims_id_val)
					{
						foreach($trims_id_val as $color_id=>$color_number)
						{
							$p=1;
							foreach ($color_number as $size_id=>$row) 
							{
							    $trims_total_qty = $trims_qty_arr[$row['job_no']][$row['trim_group']][$row['description']][$row['color_number_id']][$row['size_number_id']];
								$plan_color_qty = array_sum($color_size_qty_arr[$row['color_number_id']]);
								$plan_size_qty = $color_size_qty_arr[$row['color_number_id']][$row['size_number_id']];

								$ReqQtyDzn=($trims_total_qty*$row['cons'])/12;
								$Total_Cons=$row['cons']+$row['excess_per'];
								//$Total_Qty=($trims_total_qty*$Total_Cons)/12;

								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								?>
								<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
									<td align="right" align="center"><p><? echo $row['po_quantity']; ?></p></td>
		                            <td align="center"><p><? echo $color_library[$row['color_number_id']]; ?></p></td>
									<td align="center"><p><? echo number_format($plan_color_qty,2); ?></p></td>
									<td align="center" title="<? echo $row['description'].'='.$row['color_number_id'].'='.$row['size_number_id']; ?>"><p><? echo $size_library[$row['size_number_id']]; ?></p></td>
									<td align="center"><p><? echo number_format($plan_size_qty,2); ?></p></td>
									<td align="right"><p><? echo $row['cons']; ?></p></td>
									<td align="right"><p><? echo number_format($ReqQtyDzn,2); ?></p></td>
									<td align="right"><p><? echo $row['excess_per']; ?></p></td>
									<td align="right"><p><? echo number_format($Total_Cons,2); ?></p></td>
									<td align="right"><p><? echo number_format($trims_total_qty,2);?></p></td>
								</tr>
								<?
								$tot_qty+=$trims_total_qty;
								$i++;
							}
						}
					}
					?>
                </tbody>
	                <tfoot>
	                	<tr class="tbl_bottom">
	                    	<td colspan="9" align="right">Total</td>
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

?>