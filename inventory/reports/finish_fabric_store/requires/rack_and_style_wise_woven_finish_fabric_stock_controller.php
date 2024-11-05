<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

include('../../../../includes/class4/class.conditions.php');
include('../../../../includes/class4/class.reports.php');
include('../../../../includes/class4/class.fabrics.php');


$user_id=$_SESSION['logic_erp']['user_id'];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action=="load_drop_down_buyer")
{
	$data=explode("_",$data);
	if($data[1]==1) $party="1,3,21,90"; else $party="80";
	echo create_drop_down( "cbo_buyer_id", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in ($party)) order by buy.buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, "","" );
	exit();
}

if ($action=="load_room_rack_self_bin")
{
	load_room_rack_self_bin("requires/rack_and_style_wise_woven_finish_fabric_stock_controller",$data);
}
if($action=="job_no_popup")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
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
			$('#hide_job_id').val( id );
			$('#hide_job_no').val( ddd );
		}

		/*function js_set_value(str)
		{
			var splitData = str.split("_");
			//alert (splitData[1]);
			$("#hide_job_id").val(splitData[0]);
			$("#hide_job_no").val(splitData[1]);
			parent.emailwindow.hide();
		}*/
	</script>
</head>
<body>
	<div align="center">
		<form name="styleRef_form" id="styleRef_form">
			<fieldset style="width:580px;">
				<table width="570" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
					<thead>
						<th>Buyer</th>
						<th>Search Job</th>
						<th>Search Style</th>
						<!--<th>Search Order</th>-->
						<th><input type="reset" name="button" class="formbutton" value="Reset" style="width:70px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th> 					<input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
						<input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
					</thead>
					<tbody>
						<tr>
							<td align="center">
								<?
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
								?>
							</td>
							<td align="center">
								<input type="text" style="width:130px" class="text_boxes" name="txt_search_job" id="txt_search_job" placeholder="Job No" />
							</td>
							<td align="center">
								<input type="text" style="width:130px" class="text_boxes" name="txt_search_style" id="txt_search_style" placeholder="Style Ref." />
							</td>
	                        <!--<td align="center">
	                            <input type="text" style="width:80px" class="text_boxes" name="txt_search_order" id="txt_search_order" placeholder="Order No" />
	                        </td> +'**'+document.getElementById('txt_search_order').value-->
	                        <td align="center">
	                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('txt_search_job').value+'**'+document.getElementById('txt_search_style').value+'**'+'<? echo $cbo_year_id; ?>', 'create_job_no_search_list_view', 'search_div', 'order_wise_finish_fabric_stock_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:70px;" />
	                        </td>
	                    </tr>
	                </tbody>
	            </table>
	            <div style="margin-top:15px" id="search_div"></div> 
	        </fieldset>
	    </form>
	</div>
</body>
<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="create_job_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$year_id=$data[4];
	//$month_id=$data[5];
	//echo $month_id;

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');

	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and buyer_name=$data[1]";
	}

	if($data[2]!='') $job_cond=" and job_no_prefix_num=$data[2]"; else $job_cond="";
	if($data[3]!='') $style_cond=" and style_ref_no like '$data[3]'"; else $style_cond="";
	//if($data[4]!='') $order_cond=" and po_number like '$data[4]'"; else $order_cond="";

	$search_by=$data[2];
	$search_string="%".trim($data[3])."%";

	if($search_by==2) $search_field="style_ref_no"; else $search_field="job_no";
	//$year="year(insert_date)";
	if($db_type==0) $year_field_by="year(insert_date)";
	else if($db_type==2) $year_field_by="to_char(insert_date,'YYYY')";
	else $year_field_by="";

	if($year_id!=0) $year_cond=" and $year_field_by='$year_id'"; else $year_cond="";
	//if($month_id!=0) $month_cond="$month_field_by=$month_id"; else $month_cond="";
	$arr=array (0=>$buyer_arr);
	$sql= "select id, job_no, job_no_prefix_num, buyer_name, style_ref_no, $year_field_by as year from wo_po_details_master where status_active=1 and is_deleted=0 and company_name=$company_id $buyer_id_cond $job_cond $style_cond $year_cond order by id DESC";

	echo create_list_view("tbl_list_search", "Buyer Name,Job No,Year,Style Ref. No", "170,130,80,60","610","270",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "buyer_name,0,0,0", $arr , "buyer_name,job_no_prefix_num,year,style_ref_no", "",'','0,0,0,0','',1) ;
	exit();
}

$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );


if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$cbo_search_by 		= str_replace("'","",$cbo_search_by);
	$txt_search_comm 	= str_replace("'","",$txt_search_comm);
	$txt_file_no 		= str_replace("'","",$txt_file_no);
	$txt_ref_no 		= str_replace("'","",$txt_ref_no);
	$cbo_company_id 	= str_replace("'","",$cbo_company_id);
	$cbo_value_range_by = str_replace("'","",$cbo_value_range_by);
	$cbo_store_name 	= str_replace("'","",$cbo_store_name);
	$cbo_search_get_upto = str_replace("'","",$cbo_search_get_upto);

	if(str_replace("'","",$cbo_buyer_id)!="" && str_replace("'","",$cbo_buyer_id)!=0) $buyer_id_cond=" and a.buyer_name=$cbo_buyer_id";
	$job_no=str_replace("'","",$txt_job_no);
	$search_cond='';
	if($cbo_search_by==1)
	{
		if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and a.job_no_prefix_num in ($txt_search_comm) ";
	}
	else if($cbo_search_by==2)
	{
		if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and a.style_ref_no LIKE '%$txt_search_comm%'";
	}
	else if($cbo_search_by==3)
	{
		if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and b.po_number LIKE '%$txt_search_comm%'";
	}
	else if($cbo_search_by==4)
	{
		if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and b.file_no LIKE '%$txt_search_comm%'";
	}
	else if($cbo_search_by==5)
	{
		if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and b.grouping LIKE '$txt_search_comm'";
	}
	else
	{
		$search_cond.="";
	}

	if($cbo_store_name!="" && $cbo_store_name!=0) $store_id_cond_trans=" and c.store_id in ($cbo_store_name)";

	$date_from=str_replace("'","",$txt_date_from);
	if( $date_from=="") $receive_date=""; else $receive_date= " and c.transaction_date <=".$txt_date_from."";

	if( $date_from=="") $today_receive_date=""; else $today_receive_date= " c.transaction_date=".$txt_date_from."";

	$cbo_year_val=str_replace("'","",$cbo_year);
	$order_no=str_replace("'","",$txt_order_id);
	if(str_replace("'","",$txt_order_id)!="" && str_replace("'","",$txt_order_id)!=0) $order_id_cond_trans=" and c.id in ($order_no)";

	$date_from=str_replace("'","",$txt_date_from);
	if( $date_from=="") $receive_date_trans=""; else $receive_date_trans= " and a.transfer_date <=".$txt_date_from."";
	if($db_type==0)
	{
		$prod_id_cond=" group_concat(b.from_prod_id)";
		if($cbo_year_val!=0) $year_cond="and year(a.insert_date)='$cbo_year_val'"; else $year_cond="";
	}
	else if($db_type==2)
	{
		$prod_id_cond=" listagg(cast(b.from_prod_id as varchar2(4000)),',') within group (order by b.from_prod_id)";
		if($cbo_year_val!=0) $year_cond="and to_char(a.insert_date,'YYYY')='$cbo_year_val'";  else $year_cond="";
	}

	if($db_type==0)
	{
		$select_fld= "year(a.insert_date)as year";
	}
	else if($db_type==2)
	{
		$select_fld= "TO_CHAR(a.insert_date,'YYYY') as year";
	}
	ob_start();



	$product_array=array();
	$sql_product="select id, color, detarmination_id,dia_width,weight,product_name_details from product_details_master where item_category_id=3 and status_active=1 and is_deleted=0";
	$sql_product_result=sql_select($sql_product);
	foreach( $sql_product_result as $row )
	{
		$product_array[$row[csf('product_name_details')]]['color']=$row[csf('color')];
		$product_array[$row[csf('product_name_details')]]['dia_width']=$row[csf('dia_width')];
		$product_array[$row[csf('product_name_details')]]['weight']=$row[csf('weight')];
		$product_array[$row[csf('product_name_details')]]['yarn_did']=$row[csf('detarmination_id')];
	}

	$issue_qnty=array();
	$sql_issue=sql_select(" select b.po_breakdown_id,b.color_id, sum(b.quantity) as issue_qnty  from inv_issue_master a,inv_transaction c, order_wise_pro_details b where a.id=c.mst_id and c.prod_id=b.prod_id  and b.trans_id!=0 and a.status_active=1 and a.is_deleted=0 and a.issue_purpose in(4,9) and b.entry_form=19 group by b.po_breakdown_id,b.color_id");
	foreach( $sql_issue as $row_iss )
	{
		$issue_qnty[$row_iss[csf('po_breakdown_id')]][$row_iss[csf('color_id')]]['issue_qnty']=$row_iss[csf('issue_qnty')];
	} //var_dump($issue_qnty);

	$booking_qnty=array();
	//$sql_booking=sql_select("select b.po_break_down_id as po_id, b.fabric_color_id, b.job_no, (b.fin_fab_qnty) as fin_fab_qnty, from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(3,14) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 ");
	$sql_booking=sql_select("select b.po_break_down_id as po_id, b.fabric_color_id, b.job_no, (b.fin_fab_qnty) as fin_fab_qnty, c.lib_yarn_count_deter_id,b.gsm_weight,b.dia_width from wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_fabric_cost_dtls c where a.booking_no=b.booking_no and b.job_no = c.job_no and b.pre_cost_fabric_cost_dtls_id = c.id and a.item_category in(3,14) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 ");
	foreach( $sql_booking as $row)
	{
		$booking_qnty[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('fabric_color_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('dia_width')]][$row[csf('gsm_weight')]]['fin_fab_qnty']+=$row[csf('fin_fab_qnty')];
	}
	unset($sql_booking);

	$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details"  );
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );

	//floorSql
	$floorSql = "
		SELECT a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id
    	FROM lib_floor_room_rack_mst a
		INNER JOIN lib_floor_room_rack_dtls b ON a.floor_room_rack_id = b.floor_id
    	WHERE a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND a.company_id IN(".$cbo_company_id.")
	";
	$floorDetails = return_library_array( $floorSql, 'floor_room_rack_id', 'floor_room_rack_name');
	
	//roomSql
	$roomSql = "
		SELECT a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id
    	FROM lib_floor_room_rack_mst a
		INNER JOIN lib_floor_room_rack_dtls b ON a.floor_room_rack_id = b.room_id
    	WHERE a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND a.company_id IN(".$cbo_company_id.")
	";
	$roomDetails = return_library_array( $roomSql, 'floor_room_rack_id', 'floor_room_rack_name');
	
	//rackSql
	$rackSql = "
		SELECT a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id, b.serial_no
    	FROM lib_floor_room_rack_mst a
		INNER JOIN lib_floor_room_rack_dtls b ON a.floor_room_rack_id = b.rack_id
    	WHERE a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND a.company_id IN(".$cbo_company_id.")
	";
	$rackDetails = return_library_array( $rackSql, 'floor_room_rack_id', 'floor_room_rack_name');
	
	$rackSerialNoSql = "
		SELECT b.floor_room_rack_dtls_id, b.serial_no
    	FROM lib_floor_room_rack_mst a
		INNER JOIN lib_floor_room_rack_dtls b ON a.floor_room_rack_id = b.rack_id
    	WHERE a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND a.company_id IN(".$cbo_company_id.")
		GROUP BY b.floor_room_rack_dtls_id, b.serial_no
		ORDER BY b.serial_no ASC
	";
	$rackSerialNoResult = sql_select($rackSerialNoSql);
	foreach($rackSerialNoResult as $row)
	{
		$rackSerialNoArr[$row[csf('floor_room_rack_dtls_id')]] = $row[csf('serial_no')];
	}
	unset($rackSerialNoResult);

	//selfSql
	$selfSql = "
		SELECT a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id
    	FROM lib_floor_room_rack_mst a
		INNER JOIN lib_floor_room_rack_dtls b ON a.floor_room_rack_id = b.shelf_id
    	WHERE a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND a.company_id IN(".$cbo_company_id.")
	";
	$selfDetails = return_library_array( $selfSql, 'floor_room_rack_id', 'floor_room_rack_name');
	
	//binSql
	$binSql = "
		SELECT a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id
    	FROM lib_floor_room_rack_mst a
		INNER JOIN lib_floor_room_rack_dtls b ON a.floor_room_rack_id = b.bin_id
    	WHERE a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND a.company_id IN(".$cbo_company_id.")
	";
	$binDetails = return_library_array( $binSql, 'floor_room_rack_id', 'floor_room_rack_name');


	?>
	<fieldset style="width:3130px;">
		<table cellpadding="0" cellspacing="0" width="2390">
			<tr  class="form_caption" style="border:none;">
				<td align="center" width="100%" colspan="14" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
			</tr>
			<tr  class="form_caption" style="border:none;">
				<td align="center" width="100%" colspan="14" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
			</tr>
			<tr  class="form_caption" style="border:none;">
				<td align="center" width="100%" colspan="14" style="font-size:14px"><strong> <? if($date_from!="") echo "Upto : ".change_date_format(str_replace("'","",$txt_date_from)) ;?></strong></td>
			</tr>
		</table>
		<table width="3100" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
			<thead>
				<tr>
					<th width="30">SL</th>
					<th width="100">Buyer</th>

					<th width="80">Floor</th>
					<th width="80">Room</th>
					<th width="80">Rack</th>
					<th width="80">Shelf</th>
					<th width="80">Bin</th>

					<th width="60">Job</th>
					<th width="50">Year</th>
					<th width="200">Style</th>

					<th width="60">Order Status</th>
					<th width="150">Shipment Status</th>
					<th width="270">Fab. Desc.</th>
					
					<th width="100">RD No</th>
					<th width="100">Fabric Ref No</th>
					<th width="110">Fin. Fab. Color</th>
					

					<th width="120">Req. Qty</th>
					<th width="120">Today Recv.</th>
					<th width="120" title="Rec.+Issue Ret.+Trans. in">Total Received</th>

					<th width="120" title="Req.-Totat Rec.">Received Balance</th>
					<th width="80" title="">Avg. Rate</th>
					<th width="120" title="">Received Value</th>

					<th width="120">Today Issue</th>
					<th width="120" title="Issue+Rec. Ret.+Trans. out">Total Issued</th>
					<th width="80" title="">Avg. Rate</th>
					<th width="120" title="">Issue Value</th>

					<th width="120" title="Total Rec.- Total Issue">Stock</th>
					<th width="80" title="">Avg. Rate</th>
					<th width="" title="">Stock Value</th>
				</tr>
			</thead>
		</table>
		<div style="width:3120px; max-height:350px; overflow-y:scroll;" id="scroll_body">
			<table width="3100" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body" >
				<?
				if($db_type==0) $prod_cond="group_concat(e.prod_id) as prod_id";
				else if($db_type==2) $prod_cond="listagg(cast(e.prod_id as varchar2(4000)),',') within group (order by e.prod_id) as prod_id";

				//all amount query
				$sql_queryAmount="SELECT a.company_name, a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no,d.color_id,d.prod_id, $select_fld,e.product_name_details as prod_desc,c.rd_no,c.fabric_ref,c.batch_lot,c.transaction_type, c.batch_id,c.pi_wo_batch_no, c.store_id,c.floor_id,c.room,c.rack,c.self,c.bin_box,
				(case when d.entry_form in (17) and c.transaction_type=1 then c.cons_amount else 0 end) as receive_amount, 
				(case when d.entry_form in (258) and c.transaction_type=5 and d.trans_type=5 then c.cons_amount else 0 end) as transfer_in_amount,
				(case when d.entry_form in (209) and c.transaction_type=4 and d.trans_type=4 then c.cons_amount else 0 end) as issue_rtn_amount,
				(case when d.entry_form in (19) and c.transaction_type=2 then c.cons_amount else 0 end) as issue_amount, 
				(case when d.entry_form in (258) and c.transaction_type=6 and d.trans_type=6 then c.cons_amount else 0 end) as transfer_out_amount, 
				(case when d.entry_form in (202) and c.transaction_type=3 and d.trans_type=3 then c.cons_amount else 0 end) as recv_rtn_amount 

				from  wo_po_details_master a, wo_po_break_down b,inv_transaction c,  order_wise_pro_details d,product_details_master e 
				where a.job_no=b.job_no_mst and d.po_breakdown_id=b.id and  d.trans_id=c.id and d.prod_id=e.id and c.prod_id=e.id  and d.entry_form in (17,19,258,209,202) and d.trans_id!=0 and d.entry_form in (17,19,258,209,202)  and c.item_category=3 and d.status_active=1 and d.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.company_name=$cbo_company_id $receive_date $buyer_id_cond $search_cond $store_id_cond_trans $year_cond order by a.job_no,d.color_id,c.transaction_date"; 
				$nameArrayAmount=sql_select($sql_queryAmount);
				$style_wise_arr_amount=array();
				foreach ($nameArrayAmount as $row)
				{

					if($cbo_search_get_upto==1)
					{
						$getupToString=$row[csf('store_id')];
					}
					else if ($cbo_search_get_upto==2) {
						$getupToString=$row[csf('store_id')]."*".$row[csf('floor_id')];
					}
					else if ($cbo_search_get_upto==3) {
						$getupToString=$row[csf('store_id')]."*".$row[csf('floor_id')]."*".$row[csf('room')];
					}
					else if ($cbo_search_get_upto==4) {
						$getupToString=$row[csf('store_id')]."*".$row[csf('floor_id')]."*".$row[csf('room')]."*".$row[csf('rack')];
					}
					else if ($cbo_search_get_upto==5) {
						$getupToString=$row[csf('store_id')]."*".$row[csf('floor_id')]."*".$row[csf('room')]."*".$row[csf('rack')]."*".$row[csf('self')];
					}
					else if ($cbo_search_get_upto==6) {
						$getupToString=$row[csf('store_id')]."*".$row[csf('floor_id')]."*".$row[csf('room')]."*".$row[csf('rack')]."*".$row[csf('self')]."*".$row[csf('bin_box')];
					}
					else
					{
						$getupToString=$row[csf('store_id')]."*".$row[csf('floor_id')]."*".$row[csf('room')]."*".$row[csf('rack')]."*".$row[csf('self')]."*".$row[csf('bin_box')];
					}


					$style_wise_arr_amount[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$row[csf('transaction_type')]][$getupToString]['receive_amount']=$row[csf('receive_amount')];
					$style_wise_arr_amount[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$row[csf('transaction_type')]][$getupToString]['transfer_in_amount']=$row[csf('transfer_in_amount')];
					$style_wise_arr_amount[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$row[csf('transaction_type')]][$getupToString]['issue_rtn_amount']=$row[csf('issue_rtn_amount')];

					$style_wise_arr_amount[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$row[csf('transaction_type')]][$getupToString]['issue_amount']=$row[csf('issue_amount')];
					$style_wise_arr_amount[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$row[csf('transaction_type')]][$getupToString]['transfer_out_amount']=$row[csf('transfer_out_amount')];
					$style_wise_arr_amount[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$row[csf('transaction_type')]][$getupToString]['recv_rtn_amount']=$row[csf('recv_rtn_amount')];

				}

				/*echo "<pre>";
				print_r($style_wise_arr_amount);
				echo "</pre>";*/


				//all amount end query

				$sql_query="SELECT a.company_name, a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no, b.id as po_id, b.po_number as po_no,d.color_id,d.prod_id,c.balance_qnty, $select_fld,e.product_name_details as prod_desc,c.rd_no,c.fabric_ref,c.batch_lot,c.transaction_type, c.batch_id,c.pi_wo_batch_no, c.store_id,c.floor_id,c.room,c.rack,c.self,c.bin_box,

				(case when d.entry_form in (17) then d.quantity else 0 end) as receive_qnty,
				(case when d.entry_form in (258) and c.transaction_type=5 and d.trans_type=5 then d.quantity else 0 end) as finish_fabric_transfer_in,					
				(case when d.entry_form in (209) and c.transaction_type=4 and d.trans_type=4 then d.quantity else 0 end) as issue_rtn,

				(case when d.entry_form in (17) and c.transaction_type=1 then c.cons_amount else 0 end) as receive_amount, 
				(case when d.entry_form in (258) and c.transaction_type=5 and d.trans_type=5 then c.cons_amount else 0 end) as transfer_in_amount,
				(case when d.entry_form in (209) and c.transaction_type=4 and d.trans_type=4 then c.cons_amount else 0 end) as issue_rtn_amount,

				(case when d.entry_form in (17) and $today_receive_date then d.quantity else 0 end) as today_receive_qnty,					
				(case when d.entry_form in (258) and c.transaction_type=5 and d.trans_type=5 and $today_receive_date then d.quantity else 0 end) as today_trans_in,
				(case when d.entry_form in (209)  and c.transaction_type=4 and d.trans_type=4  and $today_receive_date then d.quantity else 0 end) as today_issue_rtn,

				(case when d.entry_form in (19) then d.quantity else 0 end) as issue_qnty,
				(case when d.entry_form in (258) and c.transaction_type=6 and d.trans_type=6 then d.quantity else 0 end) as finish_fabric_transfer_out,
				(case when d.entry_form in (202) and c.transaction_type=3 and d.trans_type=3 then d.quantity else 0 end) as recv_rtn,

				(case when d.entry_form in (19) and c.transaction_type=2 then c.cons_amount else 0 end) as issue_amount, 
				(case when d.entry_form in (258) and c.transaction_type=6 and d.trans_type=6 then c.cons_amount else 0 end) as transfer_out_amount, 
				(case when d.entry_form in (202) and c.transaction_type=3 and d.trans_type=3 then c.cons_amount else 0 end) as recv_rtn_amount, 

					(case when d.entry_form in (202) and c.transaction_type=3 and d.trans_type=3 and $today_receive_date then d.quantity else 0 end) as today_recv_rtn,
				(case when d.entry_form in (258) and c.transaction_type=6 and d.trans_type=6 and $today_receive_date then d.quantity else 0 end) as today_trans_out,
				(case when d.entry_form in (19) and $today_receive_date then d.quantity else 0 end) as today_issue_qnty

				from  wo_po_details_master a, wo_po_break_down b,inv_transaction c,  order_wise_pro_details d,product_details_master e 
				where a.job_no=b.job_no_mst and d.po_breakdown_id=b.id and  d.trans_id=c.id and d.prod_id=e.id and c.prod_id=e.id  and d.entry_form in (17,19,258,209,202) and d.trans_id!=0 and d.entry_form in (17,19,258,209,202)  and c.item_category=3 and d.status_active=1 and d.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.company_name=$cbo_company_id $receive_date $buyer_id_cond $search_cond $store_id_cond_trans $year_cond order by a.job_no,d.color_id,c.transaction_date"; 
				//and c.batch_id in(5063) and e.id in(16068,16069)
				//and   c.id in(121684,121645,121597,121593,121590)
				// and a.style_ref_no='testt' and c.id in(121645,121590,121593,121597,121684,121642,121638,121592,121594) and c.mst_id in(49149,49144) 
				//echo $sql_query;
				$con = connect();
				$color_id_check=array();
				$style_wise_arr=array();
				$style_wise_info_arr=array();
				$nameArray=sql_select($sql_query);
				foreach ($nameArray as $row)
				{
					if(!$color_id_check[$row[csf('color_id')]])
					{
					    $color_id_check[$row[csf('color_id')]]=$row[csf('color_id')];
					    $ColorId = $row[csf('color_id')];
					    $rID=execute_query("insert into tmp_color_id (user_id, color_id) values ($user_id,$ColorId)");
					}

					if($row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==2)
					{
						$recv_issue_batchId=$row[csf("batch_id")];
					}
					else
					{
						$recv_issue_batchId=$row[csf("pi_wo_batch_no")];
					}
					if($cbo_search_get_upto==1)
					{
						$getupToString=$row[csf('store_id')];
					}
					else if ($cbo_search_get_upto==2) {
						$getupToString=$row[csf('store_id')]."*".$row[csf('floor_id')];
					}
					else if ($cbo_search_get_upto==3) {
						$getupToString=$row[csf('store_id')]."*".$row[csf('floor_id')]."*".$row[csf('room')];
					}
					else if ($cbo_search_get_upto==4) {
						$getupToString=$row[csf('store_id')]."*".$row[csf('floor_id')]."*".$row[csf('room')]."*".$row[csf('rack')];
					}
					else if ($cbo_search_get_upto==5) {
						$getupToString=$row[csf('store_id')]."*".$row[csf('floor_id')]."*".$row[csf('room')]."*".$row[csf('rack')]."*".$row[csf('self')];
					}
					else if ($cbo_search_get_upto==6) {
						$getupToString=$row[csf('store_id')]."*".$row[csf('floor_id')]."*".$row[csf('room')]."*".$row[csf('rack')]."*".$row[csf('self')]."*".$row[csf('bin_box')];
					}
					else
					{
						$getupToString=$row[csf('store_id')]."*".$row[csf('floor_id')]."*".$row[csf('room')]."*".$row[csf('rack')]."*".$row[csf('self')]."*".$row[csf('bin_box')];
					}

					$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$getupToString]['job_no']=$row[csf('job_no')];
					$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$getupToString]['job_no_pre']=$row[csf('job_no_prefix_num')];
					$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$getupToString]['year']=$row[csf('year')];
					$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$getupToString]['buyer_name']=$row[csf('buyer_name')];
					$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$getupToString]['style_ref_no']=$row[csf('style_ref_no')];
					
					$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$getupToString]['prod_id']=$row[csf('prod_id')];
					$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$getupToString]['batch_id'].=$recv_issue_batchId.',';
					
					$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$getupToString]['po_id'].=$row[csf('po_id')].',';
					$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$getupToString]['po_no'].=$row[csf('po_no')].',';
					$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$getupToString]['receive_qnty']+=$row[csf('receive_qnty')];
					$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$getupToString]['rec_trns_qnty']+=$row[csf('rec_trns_qnty')];
					$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$getupToString]['issue_qnty']+=$row[csf('issue_qnty')];
					$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$getupToString]['issue_rtn']+=$row[csf('issue_rtn')];
					$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$getupToString]['recv_rtn']+=$row[csf('recv_rtn')];
					$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$getupToString]['issue_trns_qnty']+=$row[csf('issue_trns_qnty')];
					$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$getupToString]['balance_qnty']+=$row[csf('balance_qnty')];

					$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$getupToString]['today_receive_qnty']+=$row[csf('today_receive_qnty')];
					$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$getupToString]['today_issue_qnty']+=$row[csf('today_issue_qnty')];

					$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$getupToString]['today_trans_in']+=$row[csf('today_trans_in')];
					$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$getupToString]['today_trans_out']+=$row[csf('today_trans_out')];
					$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$getupToString]['today_issue_rtn']+=$row[csf('today_issue_rtn')];
					$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$getupToString]['today_recv_rtn']+=$row[csf('today_recv_rtn')];

					$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$getupToString]['finish_fabric_transfer_in']+=$row[csf('finish_fabric_transfer_in')];
					$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$getupToString]['finish_fabric_transfer_out']+=$row[csf('finish_fabric_transfer_out')];
					
					
					/*$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$row[csf('store_id')]][$row[csf('floor_id')]][$row[csf('room')]][$row[csf('rack')]][$row[csf('self')]][$row[csf('bin_box')]]['job_no_pre']=$row[csf('job_no_prefix_num')];
					$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$row[csf('store_id')]][$row[csf('floor_id')]][$row[csf('room')]][$row[csf('rack')]][$row[csf('self')]][$row[csf('bin_box')]]['year']=$row[csf('year')];
					$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$row[csf('store_id')]][$row[csf('floor_id')]][$row[csf('room')]][$row[csf('rack')]][$row[csf('self')]][$row[csf('bin_box')]]['buyer_name']=$row[csf('buyer_name')];
					$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$row[csf('store_id')]][$row[csf('floor_id')]][$row[csf('room')]][$row[csf('rack')]][$row[csf('self')]][$row[csf('bin_box')]]['style_ref_no']=$row[csf('style_ref_no')];
					
					$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$row[csf('store_id')]][$row[csf('floor_id')]][$row[csf('room')]][$row[csf('rack')]][$row[csf('self')]][$row[csf('bin_box')]]['prod_id']=$row[csf('prod_id')];
					$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$row[csf('store_id')]][$row[csf('floor_id')]][$row[csf('room')]][$row[csf('rack')]][$row[csf('self')]][$row[csf('bin_box')]]['batch_id'].=$recv_issue_batchId.',';
					
					$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$row[csf('store_id')]][$row[csf('floor_id')]][$row[csf('room')]][$row[csf('rack')]][$row[csf('self')]][$row[csf('bin_box')]]['po_id'].=$row[csf('po_id')].',';
					$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$row[csf('store_id')]][$row[csf('floor_id')]][$row[csf('room')]][$row[csf('rack')]][$row[csf('self')]][$row[csf('bin_box')]]['po_no'].=$row[csf('po_no')].',';
					$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$row[csf('store_id')]][$row[csf('floor_id')]][$row[csf('room')]][$row[csf('rack')]][$row[csf('self')]][$row[csf('bin_box')]]['receive_qnty']+=$row[csf('receive_qnty')];
					$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$row[csf('store_id')]][$row[csf('floor_id')]][$row[csf('room')]][$row[csf('rack')]][$row[csf('self')]][$row[csf('bin_box')]]['rec_trns_qnty']+=$row[csf('rec_trns_qnty')];
					$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$row[csf('store_id')]][$row[csf('floor_id')]][$row[csf('room')]][$row[csf('rack')]][$row[csf('self')]][$row[csf('bin_box')]]['issue_qnty']+=$row[csf('issue_qnty')];
					$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$row[csf('store_id')]][$row[csf('floor_id')]][$row[csf('room')]][$row[csf('rack')]][$row[csf('self')]][$row[csf('bin_box')]]['issue_rtn']+=$row[csf('issue_rtn')];
					$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$row[csf('store_id')]][$row[csf('floor_id')]][$row[csf('room')]][$row[csf('rack')]][$row[csf('self')]][$row[csf('bin_box')]]['recv_rtn']+=$row[csf('recv_rtn')];
					$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$row[csf('store_id')]][$row[csf('floor_id')]][$row[csf('room')]][$row[csf('rack')]][$row[csf('self')]][$row[csf('bin_box')]]['issue_trns_qnty']+=$row[csf('issue_trns_qnty')];
					$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['balance_qnty']+=$row[csf('balance_qnty')];

					$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$row[csf('store_id')]][$row[csf('floor_id')]][$row[csf('room')]][$row[csf('rack')]][$row[csf('self')]][$row[csf('bin_box')]]['today_receive_qnty']+=$row[csf('today_receive_qnty')];
					$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$row[csf('store_id')]][$row[csf('floor_id')]][$row[csf('room')]][$row[csf('rack')]][$row[csf('self')]][$row[csf('bin_box')]]['today_issue_qnty']+=$row[csf('today_issue_qnty')];

					$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$row[csf('store_id')]][$row[csf('floor_id')]][$row[csf('room')]][$row[csf('rack')]][$row[csf('self')]][$row[csf('bin_box')]]['today_trans_in']+=$row[csf('today_trans_in')];
					$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$row[csf('store_id')]][$row[csf('floor_id')]][$row[csf('room')]][$row[csf('rack')]][$row[csf('self')]][$row[csf('bin_box')]]['today_trans_out']+=$row[csf('today_trans_out')];
					$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$row[csf('store_id')]][$row[csf('floor_id')]][$row[csf('room')]][$row[csf('rack')]][$row[csf('self')]][$row[csf('bin_box')]]['today_issue_rtn']+=$row[csf('today_issue_rtn')];
					$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]]['today_recv_rtn']+=$row[csf('today_recv_rtn')];

					$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$row[csf('store_id')]][$row[csf('floor_id')]][$row[csf('room')]][$row[csf('rack')]][$row[csf('self')]][$row[csf('bin_box')]]['finish_fabric_transfer_in']+=$row[csf('finish_fabric_transfer_in')];
					$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$row[csf('store_id')]][$row[csf('floor_id')]][$row[csf('room')]][$row[csf('rack')]][$row[csf('self')]][$row[csf('bin_box')]]['finish_fabric_transfer_out']+=$row[csf('finish_fabric_transfer_out')];
					
					$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$row[csf('store_id')]][$row[csf('floor_id')]][$row[csf('room')]][$row[csf('rack')]][$row[csf('self')]][$row[csf('bin_box')]]['receive_amount']+=$row[csf('receive_amount')];
					$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$row[csf('store_id')]][$row[csf('floor_id')]][$row[csf('room')]][$row[csf('rack')]][$row[csf('self')]][$row[csf('bin_box')]]['transfer_in_amount']+=$row[csf('transfer_in_amount')];
					$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$row[csf('store_id')]][$row[csf('floor_id')]][$row[csf('room')]][$row[csf('rack')]][$row[csf('self')]][$row[csf('bin_box')]]['issue_rtn_amount']+=$row[csf('issue_rtn_amount')];

					$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$row[csf('store_id')]][$row[csf('floor_id')]][$row[csf('room')]][$row[csf('rack')]][$row[csf('self')]][$row[csf('bin_box')]]['issue_amount']+=$row[csf('issue_amount')];
					$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$row[csf('store_id')]][$row[csf('floor_id')]][$row[csf('room')]][$row[csf('rack')]][$row[csf('self')]][$row[csf('bin_box')]]['transfer_out_amount']+=$row[csf('transfer_out_amount')];
					$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$row[csf('store_id')]][$row[csf('floor_id')]][$row[csf('room')]][$row[csf('rack')]][$row[csf('self')]][$row[csf('bin_box')]]['recv_rtn_amount']+=$row[csf('recv_rtn_amount')];*/

					if($row[csf('transaction_type')]==1)
					{
						$style_wise_info_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$row[csf('prod_id')]]['rd_no']=$row[csf('rd_no')];
						$style_wise_info_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$row[csf('prod_id')]]['fabric_ref']=$row[csf('fabric_ref')];
						$style_wise_info_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$row[csf('prod_id')]]['batch_lot']=$row[csf('batch_lot')];
					}
				}
				/*echo "<pre>";
				print_r($style_wise_arr);
				echo "</pre>";*/
				if($rID)
				{
				    oci_commit($con);
				}
				$color_arr=return_library_array( "select a.id,a.color_name from lib_color a,tmp_color_id b where a.id=b.color_id and b.user_id=$user_id", "id", "color_name"  );

				$rID=execute_query("delete from tmp_color_id where user_id=$user_id");
				if($rID)
				{
				    oci_commit($con);
				}
						

				$i=1;$total_rec_amount=$total_iss_amount=$total_StockValue=0;$total_stock=0;
				foreach ($style_wise_arr  as $job_key=>$job_val)
				{
					foreach ($job_val  as $color_key=>$color_val)
					{
						foreach ($color_val  as $desc_key=>$desc_val)
						{
							foreach ($desc_val  as $storeData_key=>$val)
							{
								/*foreach ($store_val  as $floor_key=>$floor_val)
								{
									foreach ($floor_val  as $room_key=>$room_val)
									{
										foreach ($room_val  as $rack_key=>$rack_val)
										{
											foreach ($rack_val  as $self_key=>$self_val)
											{
												foreach ($self_val  as $bin_key=>$val)
												{*/		
													/*$today_recv=0;
													$today_issue=0;
													$rec_qty=0;
													$rec_amount=0;
													$iss_qty=0;
													$iss_amount=0;*/
													//foreach ($desc_val  as $batch_key=>$val)
													//{


													$receive_amount=	$style_wise_arr_amount[$job_key][$color_key][$desc_key][1][$storeData_key]['receive_amount'];
													$transfer_in_amount=$style_wise_arr_amount[$job_key][$color_key][$desc_key][5][$storeData_key]['transfer_in_amount'];
													$issue_rtn_amount=$style_wise_arr_amount[$job_key][$color_key][$desc_key][4][$storeData_key]['issue_rtn_amount'];

													$issue_amount= $style_wise_arr_amount[$job_key][$color_key][$desc_key][2][$storeData_key]['issue_amount'];
													$transfer_out_amount= $style_wise_arr_amount[$job_key][$color_key][$desc_key][6][$storeData_key]['transfer_out_amount'];
													$recv_rtn_amount= $style_wise_arr_amount[$job_key][$color_key][$desc_key][3][$storeData_key]['recv_rtn_amount'];


														$storeDatas=explode("*", $storeData_key);

														if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

														$dzn_qnty=0;
														if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
														if($costing_per_id_library[$job_key]==1) $dzn_qnty=12;
														else if($costing_per_id_library[$job_key]==3) $dzn_qnty=12*2;
														else if($costing_per_id_library[$job_key]==4) $dzn_qnty=12*3;
														else if($costing_per_id_library[$job_key]==5) $dzn_qnty=12*4;
														else $dzn_qnty=1;

														$color_id=$row[csf("color_id")];
														//$fab_desc_type=$product_arr[$desc_key];
														$fab_desc_type=$desc_key;

														//----batch---
														$batchids=rtrim($val['batch_id'],',');
														$batch_ids=array_unique(explode(",",$batchids));
														$batch_ids=implode(",",$batch_ids);
														//----------
														$po_nos=rtrim($val['po_no'],',');
														$po_nos=implode(",",array_unique(explode(",",$po_nos)));
														$poids=rtrim($val['po_id'],',');



														$po_ids=array_unique(explode(",",$poids));
														$book_qty=0;$issue_ret_qnty=0;$receive_ret_qnty=0;
														foreach($po_ids as $po_id)
														{
															//echo $job_key."=".$po_id."=".$color_key."=".$product_array[$desc_key]['yarn_did']."<br>";
															$book_qty+=$booking_qnty[$job_key][$po_id][$color_key][$product_array[$desc_key]['yarn_did']][$product_array[$desc_key]['dia_width']][$product_array[$desc_key]['weight']]['fin_fab_qnty'];
																//echo $job_key.'ii'.$po_id;
															$issue_ret_qnty+=$iss_return_qnty[$job_key][$po_id][$color_key][$desc_key]['issue_ret_qnty'];
															$receive_ret_qnty+=$rec_return_qnty[$job_key][$po_id][$color_key][$desc_key]['rec_ret_qnty'];
														}

															//echo $job_key.'ii';
														$po_ids=implode(",",$po_ids);
														$today_recv=$val[("today_receive_qnty")]+$val[("today_trans_in")]+$val[("today_issue_rtn")];
														$today_issue=$val[("today_issue_qnty")]+$val[("today_trans_out")]+$val[("today_recv_rtn")];
														$rec_qty=($val[("receive_qnty")]+$val[("finish_fabric_transfer_in")]+$val[("issue_rtn")]);
														$rec_amount=($receive_amount+$transfer_in_amount+$issue_rtn_amount);
														$rec_avg_rate=$rec_amount/$rec_qty;
														// echo $rec_qty.'<br>';
														//$rec_qty_cal=($val[("receive_qnty")]+$val[("finish_fabric_transfer_in")]+$val[("issue_rtn")]);
														//echo $val[("issue_qnty")]."+".$val[("finish_fabric_transfer_out")]."+".$val[("recv_rtn")]."<br/>";
														//echo $job_key."+".$color_key."+".$desc_key."+".$batch_key."<br/>";
														//echo $val[("issue_qnty")]."+".$val[("finish_fabric_transfer_out")]."+".$val[("recv_rtn")]."<br/>";
														$iss_qty=($val[("issue_qnty")]+$val[("finish_fabric_transfer_out")]+$val[("recv_rtn")]);
														$iss_amount=($issue_amount+$transfer_out_amount+$recv_rtn_amount);
														$issue_avg_rate=$iss_amount/$iss_qty;
														$StockValue=$rec_amount-$iss_amount;
														$stock=($rec_qty-$iss_qty);
														$stock_avg_rate=$StockValue/$stock;
														if(is_nan($issue_avg_rate)){$issue_avg_rate=0.00;}
														if(is_nan($stock_avg_rate)){$stock_avg_rate=0.0000;}
														//$iss_qty_cal=($val[("issue_qnty")]+$val[("finish_fabric_transfer_out")]+$val[("recv_rtn")]);

														$rd_no=$style_wise_info_arr[$job_key][$color_key][$desc_key][$val[("prod_id")]]['rd_no'];
														$fabric_ref=$style_wise_info_arr[$job_key][$color_key][$desc_key][$val[("prod_id")]]['fabric_ref'];
														$batch_lot=$style_wise_info_arr[$job_key][$color_key][$desc_key][$val[("prod_id")]]['batch_lot'];

														//($cbo_value_with ==1 && (number_format($opening,2,'.','')!= 0
													
														if($cbo_value_range_by==1 && $stock>=0)
														{
															?>
															<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
																<td width="30"><? echo $i; ?></td>
																<td width="100"><p><? echo $buyer_arr[$val[("buyer_name")]]; ?></p></td>

																<td width="80"><p><? echo $floorDetails[$storeDatas[1]]; ?></p></td>
																<td width="80"><p><? echo $roomDetails[$storeDatas[2]]; ?></p></td>
																<td width="80"><p><? echo $rackDetails[$storeDatas[3]]; ?></p></td>
																<td width="80"><p><? echo $selfDetails[$storeDatas[4]]; ?></p></td>
																<td width="80"><p><? echo $binDetails[$storeDatas[5]]; ?></p></td>
																
																<td width="60" align="center"><p><? echo $val[("job_no_pre")]; ?></p></td>
																<td width="50" align="center"><p><? echo $val[("year")]; ?></p></td>
																<td width="200"><p><? echo $val[("style_ref_no")]; ?></p></td>

																<td width="60" align="center" title="<? echo 'PO No-'.$po_nos;?>"><a href='#report_details'  onClick="openmypage_ex_factory('<? echo $po_ids; ?>','1');">View</a></td>
																<td width="150" title="<? echo $po_ids;?>"><p>
																	<? 
																		$po_ids_exp=explode(",", $po_ids);
																		$poId_countx=0; $partial_shipSts_countx=0;$panding_shipSts_countx=0;$full_shipSts_countx=0;
																		foreach ($po_ids_exp as $row) 
																		{
																			if($po_id_shipingStatus_arr[$row]==3)
																			{
																				$full_shipSts_countx++;
																			}
																			else if($po_id_shipingStatus_arr[$row]==2){
																				$partial_shipSts_countx++;
																			}
																			else if($po_id_shipingStatus_arr[$row]==1){
																				$panding_shipSts_countx++;
																			}
																			$poId_countx++;
																		}
																		if($full_shipSts_countx==$poId_countx){
																			$ShipingStatus="Full Delivery/Closed";
																		}
																		else if ($partial_shipSts_countx==$poId_countx) {
																			$ShipingStatus="Partial Delivery";
																		}
																		else if ($panding_shipSts_countx==$poId_countx) {
																			$ShipingStatus="Full Pending";
																		}
																		else
																		{
																			$ShipingStatus="Partial Delivery";
																		}
																		echo $ShipingStatus;
																	?></p></td>
																<td width="270" title="<? echo 'Prod. ID='.$desc_key;?>"><p><? echo trim($fab_desc_type, " , " ); ?></p></td>
																<td width="100" align="center"><p><? echo $rd_no; ?></p></td>
																<td width="100"><p><? echo  $fabric_ref; ?></p></td>
																<td width="110"><p><? echo $color_arr[$color_key]; ?></p></td>
																
																
																<td width="120" align="right"><p><? echo number_format($book_qty,2,'.',''); ?>&nbsp;</p></td>

																<td width="120" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 0; ?>','woven_today_receive_popup','','','<? echo $batch_ids; ?>','<? echo $storeData_key; ?>');"><? echo number_format($today_recv,2,'.',''); ?>&nbsp;</p></td>


																<td width="120" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 1; ?>','woven_receive_popup','','','<? echo $batch_ids; ?>','<? echo $storeData_key; ?>');"><? echo number_format($rec_qty,2,'.',''); ?></a></p></td>

																<td width="120" align="right"><p><?
																$rec_bal=$book_qty-$rec_qty;
																//$rec_bal=$val[("balance_qnty")];
																echo number_format($rec_bal,2,'.','');
																?></p></td>
																<td width="80" align="right" title="Received Value/Total Received"><p><? echo number_format($rec_avg_rate,4,'.',''); ?></p></td>
																<td width="120" align="right"><p><? echo number_format($rec_amount,2,'.',''); ?></p></td>

																<td width="120" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 0; ?>','woven_today_issue_popup','','','<? echo $batch_ids; ?>','<? echo $storeData_key; ?>');"><? echo number_format($today_issue,2,'.',''); ?></a></p></td>
																<td width="120" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 2; ?>','woven_issue_popup','','','<? echo $batch_ids; ?>','<? echo $storeData_key; ?>');"><? echo number_format($iss_qty,2,'.',''); ?></a></p></td>
																<td width="80" align="right"><p><? echo number_format($issue_avg_rate,4,'.',''); ?></p></td>
																<td width="120" align="right"><p><? echo number_format($iss_amount,2,'.',''); ?></p></td>

																<td width="120" align="right" title="<? echo "Receive: ".$val[("receive_qnty")]."##".$issue_ret_qnty."##".$val[("rec_trns_qnty")]."Issue: ".$val[("issue_qnty")]."##".$receive_ret_qnty."##".$val[("issue_trns_qnty")]; ?>" ><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 6; ?>','woven_knit_stock_popup','','','<? echo $batch_ids; ?>','<? echo $storeData_key; ?>');"><?
																//$stock=$rec_qty_cal-$iss_qty_cal; old
																 //new $rec_qty
																echo number_format($stock,2,'.','');
																?></a><? //$stock=$rec_qty_cal-$iss_qty_cal; echo number_format($stock,2,'.',''); ?></p></td>
																<td width="80" align="right"><p><? echo number_format($stock_avg_rate,4,'.',''); ?></p></td>
																<td width="" align="right"><p><? echo number_format($StockValue,2,'.',''); ?></p></td>
															</tr>
															<?
															$i++;

															$total_req_qty+=$book_qty;
															$total_rec_qty+=$rec_qty;
															$total_rec_bal+=$rec_bal;
															$total_issue_qty+=$iss_qty;
															$total_stock+=$stock;
															$total_possible_cut_pcs+=$possible_cut_pcs;
															$total_actual_cut_qty+=$actual_qty;
															$total_rec_return_qnty+=$receive_ret_qnty;
															$total_issue_ret_qnty+=$issue_ret_qnty;
															$total_today_issue+=$today_issue;
															$total_today_recv+=$today_recv;

															$total_rec_amount+=$rec_amount;
															$total_iss_amount+=$iss_amount;
															$total_StockValue+=$StockValue;
														}
														else if($cbo_value_range_by==2 && $stock>0) 
														{
															?>
															<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
																<td width="30"><? echo $i; ?></td>
																<td width="100"><p><? echo $buyer_arr[$val[("buyer_name")]]; ?></p></td>

																<td width="80"><p><? echo $floorDetails[$storeDatas[1]]; ?></p></td>
																<td width="80"><p><? echo $roomDetails[$storeDatas[2]]; ?></p></td>
																<td width="80"><p><? echo $rackDetails[$storeDatas[3]]; ?></p></td>
																<td width="80"><p><? echo $selfDetails[$storeDatas[4]]; ?></p></td>
																<td width="80"><p><? echo $binDetails[$storeDatas[5]]; ?></p></td>

																<td width="60" align="center"><p><? echo $val[("job_no_pre")]; ?></p></td>
																<td width="50" align="center"><p><? echo $val[("year")]; ?></p></td>
																<td width="200"><p><? echo $val[("style_ref_no")]; ?></p></td>

																<td width="60" align="center" title="<? echo 'PO No-'.$po_nos;?>"><a href='#report_details'  onClick="openmypage_ex_factory('<? echo $po_ids; ?>','1');">View</a></td>
																<td width="150" title="<? echo $po_ids;?>"><p>
																	<? 
																		$po_ids_exp=explode(",", $po_ids);
																		$poId_countx=0; $partial_shipSts_countx=0;$panding_shipSts_countx=0;$full_shipSts_countx=0;
																		foreach ($po_ids_exp as $row) 
																		{
																			if($po_id_shipingStatus_arr[$row]==3)
																			{
																				$full_shipSts_countx++;
																			}
																			else if($po_id_shipingStatus_arr[$row]==2){
																				$partial_shipSts_countx++;
																			}
																			else if($po_id_shipingStatus_arr[$row]==1){
																				$panding_shipSts_countx++;
																			}
																			$poId_countx++;
																		}
																		if($full_shipSts_countx==$poId_countx){
																			$ShipingStatus="Full Delivery/Closed";
																		}
																		else if ($partial_shipSts_countx==$poId_countx) {
																			$ShipingStatus="Partial Delivery";
																		}
																		else if ($panding_shipSts_countx==$poId_countx) {
																			$ShipingStatus="Full Pending";
																		}
																		else
																		{
																			$ShipingStatus="Partial Delivery";
																		}
																		echo $ShipingStatus;
																	?></p></td>
																<td width="270" title="<? echo 'Prod. ID='.$desc_key;?>"><p><? echo trim($fab_desc_type, " , " ); ?></p></td>
																<td width="100" align="center"><p><? echo $rd_no; ?></p></td>
																<td width="100"><p><? echo  $fabric_ref; ?></p></td>
																<td width="110"><p><? echo $color_arr[$color_key]; ?></p></td>
																
																
																<td width="120" align="right"><p><? echo number_format($book_qty,2,'.',''); ?>&nbsp;</p></td>

																<td width="120" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 0; ?>','woven_today_receive_popup','','','<? echo $batch_ids; ?>','<? echo $storeData_key; ?>');"><? echo number_format($today_recv,2,'.',''); ?>&nbsp;</p></td>


																<td width="120" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 1; ?>','woven_receive_popup','','','<? echo $batch_ids; ?>','<? echo $storeData_key; ?>');"><? echo number_format($rec_qty,2,'.',''); ?></a></p></td>

																<td width="120" align="right"><p><?
																$rec_bal=$book_qty-$rec_qty;
																//$rec_bal=$val[("balance_qnty")];
																echo number_format($rec_bal,2,'.','');
																?></p></td>
																<td width="80" align="right" title="Received Value/Total Received"><p><? echo number_format($rec_avg_rate,4,'.',''); ?></p></td>
																<td width="120" align="right"><p><? echo number_format($rec_amount,2,'.',''); ?></p></td>

																<td width="120" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 0; ?>','woven_today_issue_popup','','','<? echo $batch_ids; ?>','<? echo $storeData_key; ?>');"><? echo number_format($today_issue,2,'.',''); ?></a></p></td>
																<td width="120" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 2; ?>','woven_issue_popup','','',<? echo $batch_ids; ?>','<? echo $storeData_key; ?>');"><? echo number_format($iss_qty,2,'.',''); ?></a><? //echo number_format($iss_qty,2,'.',''); ?></p></td>
																<td width="80" align="right"><p><? echo number_format($issue_avg_rate,4,'.',''); ?></p></td>
																<td width="120" align="right"><p><? echo number_format($iss_amount,2,'.',''); ?></p></td>

																<td width="120" align="right" title="<? echo "Receive: ".$val[("receive_qnty")]."##".$issue_ret_qnty."##".$val[("rec_trns_qnty")]."Issue: ".$val[("issue_qnty")]."##".$receive_ret_qnty."##".$val[("issue_trns_qnty")]; ?>" ><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from;; ?>','<? echo 6; ?>','woven_knit_stock_popup','','','<? echo $batch_ids; ?>','<? echo $storeData_key; ?>');"><?
																//$stock=$rec_qty_cal-$iss_qty_cal; old
																 //new $rec_qty
																echo number_format($stock,2,'.','');
																?></a><? //$stock=$rec_qty_cal-$iss_qty_cal; echo number_format($stock,2,'.',''); ?></p></td>
																<td width="80" align="right"><p><? echo number_format($stock_avg_rate,4,'.',''); ?></p></td>
																<td width="" align="right"><p><? echo number_format($StockValue,2,'.',''); ?></p></td>
															</tr>
															<?
															$i++;

															$total_req_qty+=$book_qty;
															$total_rec_qty+=$rec_qty;
															$total_rec_bal+=$rec_bal;
															$total_issue_qty+=$iss_qty;
															$total_stock+=$stock;
															$total_possible_cut_pcs+=$possible_cut_pcs;
															$total_actual_cut_qty+=$actual_qty;
															$total_rec_return_qnty+=$receive_ret_qnty;
															$total_issue_ret_qnty+=$issue_ret_qnty;
															$total_today_issue+=$today_issue;
															$total_today_recv+=$today_recv;

															$total_rec_amount+=$rec_amount;
															$total_iss_amount+=$iss_amount;
															$total_StockValue+=$StockValue;


														}
													
												/*}
											}
										}
									}
								}*/

								
							}
						}
					}
				}
				?>
			</table>
			<table width="3100" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" >
				<tfoot>
					<th width="30"></th>
					<th width="100"></th>
					<th width="80"></th>
					<th width="80"></th>
					<th width="80"></th>
					<th width="80"></th>
					<th width="80"></th>
					<th width="60"></th>
					<th width="50"></th>
					<th width="200">&nbsp;</th>

					<th width="60">&nbsp;</th>
					<th width="150">&nbsp;</th>
					<th width="270"></th>
					
					<th width="100">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="110">Total</th>
					
					<th width="120" align="right" id="value_total_req_qty"><? echo number_format($total_req_qty,2,'.',''); ?></th>
					<th width="120" align="right" id="value_total_today_rec_qty"><? echo number_format($total_today_recv,2,'.',''); ?></th>
					<th width="120" align="right" id="value_total_rec_qty"><? echo number_format($total_rec_qty,2,'.',''); ?></th>
					<th width="120" align="right" id="value_total_rec_bal"><? echo number_format($total_rec_bal,2,'.',''); ?></th>
					<th width="80" align="right"></th>
					<th width="120" align="right" id="value_total_rec_amount"><? echo number_format($total_rec_amount,2,'.',''); ?></th>
					<th width="120"  id="value_recv_today_issue_qty"><? echo number_format($total_today_issue,2,'.',''); ?></th>
					<th width="120" align="right" id="value_total_issue_qty"><? echo number_format($total_issue_qty,2,'.',''); ?></th>
					<th width="80" align="right"></th>
					<th width="120" align="right" id="value_total_iss_amount"><? echo number_format($total_iss_amount,2,'.',''); ?></th>
					<th width="120" align="right" id="value_total_stock"><? echo number_format($total_stock,2,'.',''); ?></th>
					<th width="80" align="right" ></th>
					<th width="" align="right" id="value_total_StockValue"><? echo number_format($total_StockValue,2,'.',''); ?></th>
				</tfoot>
			</table>
		</div>
	</fieldset>
	<?
	$html = ob_get_contents();
	ob_clean();
	foreach (glob("*.xls") as $filename)
	{
		@unlink($filename);
	}
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, $html);
	echo "$html**$filename**$report_type";
	exit();
}

if($action=="open_exfactory")
{
	echo load_html_head_contents("Ex-factory Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);

	$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details"  );
	?>
	<fieldset style="width:470px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="450" cellpadding="0" cellspacing="0" align="center">
				<caption><strong> Order Status</strong></caption>
				<thead>
					<tr>
						<th width="50">Sl</th>
						<th width="100">Order No</th>
						<th width="100">Ex-factory Date</th>
						<th width="80">Ex-factory Qty</th>
						<th>Order Status</th>
					</tr>
				</thead>
				<tbody>
					<?

					$sql="select a.id, a.ex_factory_date, a.ex_factory_qnty,b.po_number,b.shiping_status
					from  pro_ex_factory_mst a, wo_po_break_down b
					where b.id=a.po_break_down_id and a.status_active=1 and a.is_deleted=0 and a.po_break_down_id in($po_id)";
					//echo $mrr_sql;

					$dtlsArray=sql_select($sql);
					$i=1;
					foreach($dtlsArray as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$tot_reject=$row[csf('returnable_qnty')];
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="50" align="center"><p><? echo $i; ?></p></td>
							<td width="100" align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
							<td width="100" align="center"><p><? if($row[csf('ex_factory_date')]!="" &&  $row[csf('ex_factory_date')]!="0000-00-00") echo change_date_format($row[csf('ex_factory_date')]); ?></p></td>
							<td width="80" align="right"><? echo number_format($row[csf('ex_factory_qnty')],2); ?></td>
							<td width="" align="center"><p><? echo  $shipment_status[$row[csf('shiping_status')]]; ?></p></td>
						</tr>
						<?
						$tot_exfact_qty+=$row[csf('ex_factory_qnty')];
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="3" align="right">Total</td>
						<td align="right">&nbsp;<? echo number_format($tot_exfact_qty,2); ?>&nbsp;</td>
						<td>&nbsp; </td>
					</tr>
				</tfoot>
			</table>
		</div>
	</fieldset>
	<?
	exit();
}

if($action=="open_order_exfactory")
{
	echo load_html_head_contents("Ex-factory Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<fieldset style="width:480px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="450" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th width="50">Sl</th>
						<th width="120">Order No</th>
						<th width="80">Ex-factory Date</th>
						<th width="100">Ex-factory Qty</th>
						<th>Order Status</th>
					</tr>
				</thead>
				<tbody>
					<?

					$sql="select a.id as order_id, a.po_number, a.shiping_status, b.ex_factory_date, b.ex_factory_qnty
					from wo_po_break_down a, pro_ex_factory_mst b
					where a.id=b.po_break_down_id and b.status_active=1 and b.is_deleted=0 and b.po_break_down_id in($po_id)";
				//echo $sql;

					$dtlsArray=sql_select($sql);
					$i=1;
					foreach($dtlsArray as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$tot_reject=$row[csf('returnable_qnty')];
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="50" align="center"><p><? echo $i; ?></p></td>
							<td width="120"><? echo $row[csf('po_number')]; ?></td>
							<td width="80" align="center"><p><? if($row[csf('ex_factory_date')]!="" &&  $row[csf('ex_factory_date')]!="0000-00-00") echo change_date_format($row[csf('ex_factory_date')]); ?></p></td>
							<td align="right" width="100"><? echo number_format($row[csf('ex_factory_qnty')],2); ?></td>
							<td><? echo $shipment_status[$row[csf('shiping_status')]]; ?></td>
						</tr>
						<?
						$tot_exfact_qty+=$row[csf('ex_factory_qnty')];
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<th width="50">&nbsp;</th>
						<th width="120">&nbsp;</th>
						<th width="80">Total</th>
						<th width="100"><? echo number_format($tot_exfact_qty,2); ?></th>
						<th>&nbsp;</th>
					</tr>
				</tfoot>
			</table>
		</div>
	</fieldset>
	<?
	exit();
}

//Today total_rec_popup end
if($action=="woven_receive_popup")
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$prod_id_arr=explode("__", $prod_id);
	$prod_id=$prod_id_arr[0];
	$product_id=$prod_id_arr[1];


	$storeDatas=explode("*", $storeInfo);
	$store=$storeDatas[0];
	$floor=$storeDatas[1];
	$room=$storeDatas[2];
	$rack=$storeDatas[3];
	$shelf=$storeDatas[4];
	$bin=$storeDatas[5];

	if($store!=""){$storeCond="and b.store_id=$store";}
	if($floor!=""){$floorCond="and b.floor_id=$floor";}
	if($room!=""){$roomCond="and b.room=$room";}
	if($rack!=""){$rackCond="and b.rack=$rack";}
	if($shelf!=""){$shelfCond="and b.self=$shelf";}
	if($bin!=""){$binCond="and b.bin_box=$bin";}
	?>
	<fieldset style="width:1195px; margin-left:3px">
		<script>
			function print_window()
			{
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
					'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
				d.close();
			}

		</script>
		<?
		ob_start();
		$color_arr=return_library_array( "select id,color_name from lib_color where id in(".$color.")", "id", "color_name"  );
		$batch_no_arr=return_library_array( "select id,batch_no from pro_batch_create_mst where id in(".$batchId.")", "id", "batch_no"  );

		?>
		<div id="scroll_body" align="center">
			<table border="0" class="rpt_table" rules="all" cellpadding="0" cellspacing="0">
				<tr>
					<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
					<td> <div id="report_container"> </div> </td>
				</tr>
			</table>
			<table border="1" class="rpt_table" rules="all" width="1275" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th colspan="19">Receive Details</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="110">System ID</th>
						<th width="70">Receive Date</th>
						<th width="100">Challan No</th>
						<th width="80">Color</th>
						<th width="100">Batch No</th>
						<th width="80">Fin. Rcv. Qty.</th>
						<th width="50">Rate</th>
						<th width="80">Amount</th>
						<th width="200">Fabric Des.</th>
						<th width="50">GSM</th>
						<th width="50">F.Dia</th>
						<th>Collar/Cuff Pcs</th>
					</tr>
				</thead>
				<tbody>
					<?
					$gsm_arr=return_library_array( "select id, gsm_weight from lib_yarn_count_determina_mst", "id", "gsm_weight");
					$prodData=sql_select("select id, item_description, unit_of_measure, product_name_details, detarmination_id, gsm, dia_width from product_details_master where item_category_id in(3) and status_active=1 and is_deleted=0");
					$prod_array=array();
					foreach($prodData as $row)
					{
						$prod_array[$row[csf('id')]]['gsm']=$gsm_arr[$row[csf('detarmination_id')]];
						$prod_array[$row[csf('id')]]['dia']=$row[csf('dia_width')];
						$prod_array[$row[csf('id')]]['name_details']=$row[csf('product_name_details')];
					}

					$book_sql="select a.booking_no, b.grey_fab_qnty from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no and b.po_break_down_id in($po_id)";
					$dtlsbook=sql_select($book_sql);
					$booking_arr=array();
					foreach($dtlsbook as $row)
					{
						$booking_arr[$row[csf('booking_no')]]['grey_qty']+=$row[csf('grey_fab_qnty')];
					}

					$i=1;

					$mrr_sql="select a.recv_number,a.knitting_source,a.booking_no,a.knitting_company,a.challan_no, a.receive_date,c.color_id, b.prod_id,b.rack, b.order_rate, sum(c.quantity) as quantity,b.batch_id 
						from  inv_receive_master a, inv_transaction b, order_wise_pro_details c,product_details_master e 
						where a.id=b.mst_id and b.id=c.trans_id and c.prod_id=e.id and b.prod_id=e.id and a.entry_form in (17) and c.entry_form in (17)  and a.item_category=3 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and b.batch_id in($batchId) and e.product_name_details='$prod_id' and c.color_id='$color' $storeCond $floorCond $roomCond $rackCond $shelfCond $binCond group by a.recv_number,a.knitting_source,a.knitting_company,a.challan_no, a.receive_date,c.color_id, b.prod_id,b.rack,a.booking_no, b.order_rate,b.batch_id order by a.receive_date";
				
					
					//echo $mrr_sql;
					$dtlsArray=sql_select($mrr_sql);

					foreach($dtlsArray as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$tot_reject=$row[csf('returnable_qnty')];
						if($row[csf('knitting_source')]==1) $knitting_company=$company_arr[$row[csf('knitting_company')]];
						else $knitting_company=$supplier_name_arr[$row[csf('knitting_company')]];

						$booking_qty=$booking_arr[$row[csf('booking_no')]]['grey_qty'];
						$process_loss=($row[csf('quantity')]/$booking_qty)*100;

						$gsm=$prod_array[$row[csf('prod_id')]]['gsm'];
						$dia=$prod_array[$row[csf('prod_id')]]['dia'];
						$description = $prod_array[$row[csf('prod_id')]]['name_details'];
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
							<td width="110"><p><? echo $row[csf('recv_number')]; ?></p></td>
							<td width="70"><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
							<td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>
							<td width="80"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
							<td width="100"><p><? echo $batch_no_arr[$row[csf('batch_id')]]; ?></p></td>
							<td width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
							<td width="50" align="right"><p><? echo number_format($row[csf('order_rate')],2); ?></p></td>
							<td width="80" align="right"><p><? echo number_format(($row[csf('quantity')]*$row[csf('order_rate')]),2); ?></p></td>
							<td width="200" ><p><? echo trim($description," , "); ?></p></td>
							<td width="50" align="center"><p><? echo $gsm; ?>&nbsp;</p></td>
							<td width="50" align="center"><p><? echo $dia; ?></p></td>
							<td><p><? echo $body_part[$row[csf('body_part_id')]]; ?></p></td>
						</tr>
						<?
						$tot_qty+=$row[csf('quantity')];
						$tot_amount+=number_format(($row[csf('quantity')]*$row[csf('order_rate')]),2,'.','');
						$tot_booking_qty+=$booking_qty;
						$tot_reject_qty+=$row[csf('returnable_qnty')];
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="5" align="right"></td>
						<td align="right">Total<? //echo number_format($tot_booking_qty,2); ?> </td>
						<td align="right"><? echo number_format($tot_qty,2); ?> </td>
						<td align="right"></td>
						<td align="right"><? echo number_format($tot_amount,2); ?> </td>
						<td colspan="2"> </td>
						<td align="right">&nbsp;<? //echo number_format($tot_qty,2); ?>&nbsp;</td>
						<td align="right">&nbsp;<? //echo number_format($tot_reject_qty,2); ?>&nbsp;</td>
					</tr>
				</tfoot>
			</table>
            <br/>
			<table border="1" class="rpt_table" rules="all" width="1275" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th colspan="19">Transfer In Details</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="110">System ID</th>
						<th width="70">Receive Date</th>
                        <!-- <th width="80">Dyeing Source</th>
                        <th width="110">Dyeing Company</th> -->
                        <th width="100">Challan No</th>
                        <th width="80">Color</th>
                        <th width="100">Batch No</th>
                        <!-- <th width="60">Rack No</th>
                        <th width="80">Grey Qty.</th> -->
                        <th width="80">Fin. Transfer. Qty.</th>
                        <th width="50">Rate</th>
                        <th width="80">Amount</th>
                        <!-- <th width="70">Process Loss Qty.</th>
                        <th width="60">QC ID</th>
                        <th width="80">QC Name</th> -->
                        <th width="200">Fabric Des.</th>
                        <th width="50">GSM</th>
                        <th width="50">F.Dia</th>
                        <th>Collar/Cuff Pcs</th>
                    </tr>
                </thead>
                <tbody>
                	<?
                	$prodData=sql_select("select id, item_description,unit_of_measure, product_name_details, detarmination_id, gsm, dia_width from product_details_master where item_category_id in(3) and status_active=1 and is_deleted=0");
                	$prod_array=array();
                	foreach($prodData as $row)
                	{
                		$prod_array[$row[csf('id')]]['gsm']=$row[csf('gsm')];
                		$prod_array[$row[csf('id')]]['dia']=$row[csf('dia_width')];
                		$prod_array[$row[csf('id')]]['name_details']=$row[csf('product_name_details')];
                	}

                	$book_sql="select a.booking_no, b.grey_fab_qnty from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no and b.po_break_down_id in($po_id)";
                	$dtlsbook=sql_select($book_sql);

                	$booking_arr=array();
                	foreach($dtlsbook as $row)
                	{
                		$booking_arr[$row[csf('booking_no')]]['grey_qty']+=$row[csf('grey_fab_qnty')];
                	}

                	$i=1;

					/*	echo	$mrr_sql_trnsf="select a.recv_number,a.knitting_source,a.booking_no,a.knitting_company,a.challan_no, a.receive_date,c.color_id, b.prod_id,b.rack, sum(c.quantity) as quantity from  inv_receive_master a, inv_transaction b, order_wise_pro_details c
					where a.id=b.mst_id and b.id=c.trans_id  and a.entry_form in (17) and c.entry_form in (17)  and a.item_category=3 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and c.prod_id=$prod_id and c.color_id='$color' group by a.recv_number,a.knitting_source,a.knitting_company,a.challan_no, a.receive_date,c.color_id, b.prod_id,b.rack,a.booking_no";*/

					
						$mrr_sql_trnsf="select a.transfer_system_id, a.challan_no,a.transfer_date,c.color_id, b.prod_id,b.rack,d.feb_description_id,d.gsm,d.dia_width,
						sum(c.quantity) as quantity,b.pi_wo_batch_no 
						from inv_item_transfer_mst a, inv_item_transfer_dtls d, inv_transaction b, order_wise_pro_details c,product_details_master e 
						where a.id=d.mst_id and d.to_trans_id=b.id
						and a.id=b.mst_id and b.id=c.trans_id and c.prod_id=e.id and b.prod_id=e.id and b.transaction_type=5 and c. trans_type=5 and a.entry_form in (258) and c.entry_form in (258) and a.item_category=3 
						and a.is_deleted=0 and a.status_active=1 
						and b.status_active=1 and b.is_deleted=0
						and c.po_breakdown_id in($po_id) and a.to_company='$companyID' and b.pi_wo_batch_no in($batchId) and e.id=$product_id  and c.color_id='$color'
						group by a.transfer_system_id, a.challan_no, a.transfer_date,c.color_id, b.prod_id,b.rack,d.feb_description_id,d.gsm,d.dia_width,b.pi_wo_batch_no order by a.transfer_date";
						//and e.product_name_details='$prod_id'
					

					$dtlsArray=sql_select($mrr_sql_trnsf);

					foreach($dtlsArray as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$tot_reject=$row[csf('returnable_qnty')];
						if($row[csf('knitting_source')]==1)
						{
							$knitting_company=$company_arr[$row[csf('knitting_company')]];
						}
						else
						{
							$knitting_company=$supplier_name_arr[$row[csf('knitting_company')]];
						}
						$booking_qty=$booking_arr[$row[csf('booking_no')]]['grey_qty'];
						$process_loss=($row[csf('quantity')]/$booking_qty)*100;

						$gsm=$prod_array[$row[csf('prod_id')]]['gsm'];
						$dia=$prod_array[$row[csf('prod_id')]]['dia'];
						$description = $prod_array[$row[csf('prod_id')]]['name_details'];
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="30"><p><? echo $i; ?></p></td>
                            <td width="110"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
                            <td width="70"><p><? echo change_date_format($row[csf('transfer_date')]); ?></p></td>
                            <!--  <td width="80"><p><? //echo $knitting_source[$row[csf('knitting_source')]]; ?></p></td>
                            <td width="110"><p><? //echo $knitting_company; ?></p></td> -->
                            <td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>
                            <td width="80"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
                             <td width="100"><p><? echo $batch_no_arr[$row[csf('pi_wo_batch_no')]]; ?></p></td>
                            <!--<td width="60"><p><? //echo $row[csf('rack')]; ?></p></td>
                            <td width="80" align="right"><p><? //echo number_format($booking_qty,2); ?></p></td>-->
                            <td width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
                            <td width="50" align="right"><p><? echo number_format($row[csf('order_rate')],2); ?></p></td>
                            <td width="80" align="right"><p><? echo number_format(($row[csf('quantity')]*$row[csf('order_rate')]),2); ?></p></td>
                            <!-- <td width="70" title="Fin Recv Qty/Grey Qty*100" align="right"><p><? //echo number_format($process_loss); ?></p></td>
                            <td width="60" align="center"><p><? //echo $row[csf('emp_id')]; ?></p></td>
                            <td width="80" align="center"><p><? //echo $row[csf('qc_name')]; ?></p></td> -->
                            
                            <td width="200" ><p><? echo trim($description," , "); ?></p></td>
                            <td width="50" align="center"><p><? echo $gsm; ?>&nbsp;</p></td>
                            <td width="50" align="center"><p><? echo $dia; ?></p></td>
                            <td><p><? echo $body_part[$row[csf('body_part_id')]]; ?></p></td>
                         </tr>
                         <?
                         $tot_qty_trns+=$row[csf('quantity')];
						 $tot_amount_trns+=number_format(($row[csf('quantity')]*$row[csf('order_rate')]),2,'.','');
                         $tot_booking_qty+=$booking_qty;
                         $tot_reject_qty+=$row[csf('returnable_qnty')];
                         $i++;

                     }
                     ?>
                 </tbody>
                 <tfoot>
                 	<tr class="tbl_bottom">
                 		<td colspan="5" align="right"></td>
                 		<td align="right">Total<? //echo number_format($tot_booking_qty,2); ?> </td>
                 		<td align="right"><? echo number_format($tot_qty_trns,2); ?> </td>
                 		<td align="right"></td>
                 		<td align="right"><? echo number_format($tot_amount_trns,2); ?> </td>
                 		<td colspan="2"> </td>
                 		<td align="right">&nbsp;<? //echo number_format($tot_qty,2); ?>&nbsp;</td>
                 		<td align="right">&nbsp;<? //echo number_format($tot_reject_qty,2); ?>&nbsp;</td>
                 	</tr>
                 </tfoot>
             </table>
            <br/>
             <table border="1" class="rpt_table" rules="all" width="1275" cellpadding="0" cellspacing="0" align="center">
             	<thead>
             		<tr>
             			<th colspan="18">Issue Return Details</th>
             		</tr>
             		<tr>
                        <th width="30">Sl</th>
                        <th width="110">System ID</th>
                        <th width="70">Return Date</th>
                        <!-- <th width="80">Dyeing Source</th>
                        <th width="110">Dyeing Company</th> -->
                        <th width="100">Challan No</th>
                        <th width="80">Color</th>
                         <th width="100">Batch No</th>
                        <!--<th width="60">Rack No</th>
                        <th width="80">Grey Qty.</th> -->
                        <th width="80">Issue Return Qty.</th>
                        <th width="50">Rate</th>
                        <th width="80">Amount</th>
                        <!-- <th width="70">Process Loss Qty.</th>
                        <th width="60">QC ID</th>
                        <th width="80">QC Name</th> -->
                        <th width="200">Fabric Des.</th>
                        <th width="50">GSM</th>
                        <th width="50">F.Dia</th>
                        <th>Collar/Cuff Pcs</th>
                    </tr>
                </thead>
                <tbody>
                	<?
                	$prodData=sql_select("select id, item_description,unit_of_measure, product_name_details, detarmination_id, gsm, dia_width from product_details_master where item_category_id in(3) and status_active=1 and is_deleted=0");
                	$prod_array=array();
                	foreach($prodData as $row)
                	{
                		$prod_array[$row[csf('id')]]['gsm']=$row[csf('gsm')];
                		$prod_array[$row[csf('id')]]['dia']=$row[csf('dia_width')];
                		$prod_array[$row[csf('id')]]['name_details']=$row[csf('product_name_details')];
                	}

                	$book_sql="select a.booking_no, b.grey_fab_qnty from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no and b.po_break_down_id in($po_id)";
                	$dtlsbook=sql_select($book_sql);

                	$booking_arr=array();
                	foreach($dtlsbook as $row)
                	{
                		$booking_arr[$row[csf('booking_no')]]['grey_qty']+=$row[csf('grey_fab_qnty')];
                	}

                	$i=1;

					
					$mrr_sql_issue_rtn="select a.recv_number,a.knitting_source,a.booking_no,a.knitting_company,a.challan_no, a.receive_date,c.color_id, b.prod_id,b.rack, sum(c.quantity) as quantity,b.pi_wo_batch_no 
						from  inv_receive_master a, inv_transaction b, order_wise_pro_details c,product_details_master e
						where a.id=b.mst_id and b.id=c.trans_id and c.prod_id=e.id and b.prod_id=e.id and a.entry_form in (209) and c.entry_form in (209)  and a.item_category=3 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and b.pi_wo_batch_no in($batchId) and e.product_name_details='$prod_id' and c.color_id='$color' group by a.recv_number,a.knitting_source,a.knitting_company,a.challan_no, a.receive_date,c.color_id, b.prod_id,b.rack,a.booking_no,b.pi_wo_batch_no order by a.receive_date";
					

					$dtlsArray=sql_select($mrr_sql_issue_rtn);
					foreach($dtlsArray as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$tot_reject=$row[csf('returnable_qnty')];
						if($row[csf('knitting_source')]==1)
						{
							$knitting_company=$company_arr[$row[csf('knitting_company')]];
						}
						else
						{
							$knitting_company=$supplier_name_arr[$row[csf('knitting_company')]];
						}
						$booking_qty=$booking_arr[$row[csf('booking_no')]]['grey_qty'];
						$process_loss=($row[csf('quantity')]/$booking_qty)*100;

						$gsm=$prod_array[$row[csf('prod_id')]]['gsm'];
						$dia=$prod_array[$row[csf('prod_id')]]['dia'];
						$description = $prod_array[$row[csf('prod_id')]]['name_details'];
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="30"><p><? echo $i; ?></p></td>
                            <td width="110"><p><? echo $row[csf('recv_number')]; ?></p></td>
                            <td width="70"><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
                            <!--  <td width="80"><p><? //echo $knitting_source[$row[csf('knitting_source')]]; ?></p></td>
                            <td width="110"><p><? //echo $knitting_company; ?></p></td> -->
                            <td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>
                            <td width="80"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
                             <td width="100"><p><? echo $batch_no_arr[$row[csf('pi_wo_batch_no')]]; ?></p></td>
                            <!--<td width="60"><p><? //echo $row[csf('rack')]; ?></p></td>
                            <td width="80" align="right"><p><? //echo number_format($booking_qty,2); ?></p></td>-->
                            <td width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
                            <td width="50" align="right"><p><? echo number_format($row[csf('order_rate')],2); ?></p></td>
                            <td width="80" align="right"><p><? echo number_format(($row[csf('quantity')]*$row[csf('order_rate')]),2); ?></p></td>
                            <!-- <td width="70" title="Fin Recv Qty/Grey Qty*100" align="right"><p><? //echo number_format($process_loss); ?></p></td>
                            <td width="60" align="center"><p><? //echo $row[csf('emp_id')]; ?></p></td>
                            <td width="80" align="center"><p><? //echo $row[csf('qc_name')]; ?></p></td> -->
                            <td width="200" ><p><? echo trim($description," , "); ?></p></td>
                            <td width="50" align="center"><p><? echo $gsm; ?>&nbsp;</p></td>
                            <td width="50" align="center"><p><? echo $dia; ?></p></td>
                            <td><p><? echo $body_part[$row[csf('body_part_id')]]; ?></p></td>
                        </tr>
                         <?
                         $tot_issueRtn_qty+=$row[csf('quantity')];
						 $tot_issueRtn_amount+=number_format(($row[csf('quantity')]*$row[csf('order_rate')]),2,'.','');
                         $tot_booking_qty+=$booking_qty;
                         $tot_reject_qty+=$row[csf('returnable_qnty')];
                         $i++;
                     }
                     ?>
                 </tbody>
                 <tfoot>
                 	<tr class="tbl_bottom">
                 		<td colspan="5" align="right"></td>
                 		<td align="right">Total<? //echo number_format($tot_booking_qty,2); ?> </td>
                 		<td align="right"><? echo number_format($tot_issueRtn_qty,2); ?> </td>
                 		<td align="right"></td>
                 		<td align="right"><? //echo number_format($tot_issueRtn_qty,2); ?> </td>
                 		<td align="right"></td>
                 		<td align="right"><? //echo number_format($tot_issueRtn_amount,2); ?> </td>
                 		
                 		<td align="right">&nbsp;<? //echo number_format($tot_qty,2); ?>&nbsp;</td>
                 		<td align="right">&nbsp;<? //echo number_format($tot_reject_qty,2); ?>&nbsp;</td>
                 	</tr>
                 </tfoot>
             </table>
         </div>
     </fieldset>
     <?
     $html=ob_get_contents();
     ob_flush();

     foreach (glob(""."*.xls") as $filename)
     {
     	@unlink($filename);
     }

			//html to xls convert
     $name=time();
     $name=$user_id."_".$name.".xls";
     $create_new_excel = fopen(''.$name, 'w');
     $is_created = fwrite($create_new_excel,$html);

     ?>
     <input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />
     <script>
     	$(document).ready(function(e) {
     		document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
     	});

     </script>
     <?
     exit();
}

if($action=="woven_today_receive_popup")
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<fieldset style="width:1250px; margin-left:3px">
		<script>
			function print_window()
			{
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
					'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
				d.close();
			}

		</script>
		<?
		ob_start();
		$color_arr=return_library_array( "select id,color_name from lib_color where id in(".$color.")", "id", "color_name"  );
		$batch_no_arr=return_library_array( "select id,batch_no from pro_batch_create_mst where id in(".$batchId.")", "id", "batch_no"  );

		?>
		<div id="scroll_body" align="center">
			<table border="0" class="rpt_table" rules="all" cellpadding="0" cellspacing="0">
				<tr>
					<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
					<td> <div id="report_container"> </div> </td>
				</tr>
			</table>
				<?
					$gsm_arr=return_library_array( "select id, gsm_weight from lib_yarn_count_determina_mst", "id", "gsm_weight");
					$supplier_name_arr=return_library_array( "select id, supplier_name from  lib_supplier", "id", "supplier_name");

					$prodData=sql_select("select id, item_description,unit_of_measure, product_name_details, detarmination_id, gsm, dia_width from product_details_master where item_category_id in(3) and status_active=1 and is_deleted=0");
					$prod_array=array();
					foreach($prodData as $row)
					{
						$prod_array[$row[csf('id')]]['gsm']=$gsm_arr[$row[csf('detarmination_id')]];
						$prod_array[$row[csf('id')]]['dia']=$row[csf('dia_width')];
						$prod_array[$row[csf('id')]]['name_details']=$row[csf('product_name_details')];
					}

					$date_from=str_replace("'","",$from_date);
					if( $date_from=="") $today_receive_date=""; else $today_receive_date= "and b.transaction_date='".$date_from."'";

					
						$mrr_sql="select a.recv_number,a.knitting_source,a.booking_no,a.knitting_company,a.challan_no, a.receive_date,c.color_id, b.prod_id,b.rack, b.order_rate, sum(c.quantity) as quantity,b.batch_id 
						from  inv_receive_master a, inv_transaction b, order_wise_pro_details c,product_details_master e 
						where a.id=b.mst_id and b.id=c.trans_id and c.prod_id=e.id and b.prod_id=e.id  and a.entry_form in (17) and c.entry_form in (17)  and a.item_category=3 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and b.batch_id in($batchId) and e.product_name_details='$prod_id' and c.color_id='$color' $today_receive_date group by a.recv_number,a.knitting_source,a.knitting_company,a.challan_no, a.receive_date,c.color_id, b.prod_id,b.rack,a.booking_no, b.order_rate,b.batch_id";
				
						$mrr_issue_rtn_sql="select a.recv_number,a.knitting_source,a.booking_no,a.knitting_company,a.challan_no, a.receive_date,c.color_id, b.prod_id,b.rack, b.order_rate, sum(c.quantity) as quantity,b.pi_wo_batch_no as batch_id
						from  inv_receive_master a, inv_transaction b, order_wise_pro_details c,product_details_master e 
						where a.id=b.mst_id and b.id=c.trans_id and c.prod_id=e.id and b.prod_id=e.id and a.entry_form in (209) and c.entry_form in (209)  and a.item_category=3 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and b.pi_wo_batch_no in($batchId) and e.product_name_details='$prod_id' and c.color_id='$color' $today_receive_date group by a.recv_number,a.knitting_source,a.knitting_company,a.challan_no, a.receive_date,c.color_id, b.prod_id,b.rack,a.booking_no, b.order_rate,b.pi_wo_batch_no";

						$mrr_trns_in_sql="select a.transfer_system_id as  recv_number,null as knitting_source,null as booking_no, null as knitting_company, a.challan_no,a.transfer_date as receive_date,c.color_id, b.prod_id,b.rack, b.order_rate, sum(c.quantity) as quantity,b.pi_wo_batch_no as batch_id
						from inv_item_transfer_mst a, inv_item_transfer_dtls d, inv_transaction b, order_wise_pro_details c,product_details_master e  
						where a.id=d.mst_id and d.to_trans_id=b.id
						and a.id=b.mst_id and b.id=c.trans_id and c.prod_id=e.id and b.prod_id=e.id and b.transaction_type=5 and c. trans_type=5 and a.entry_form in (258) and c.entry_form in (258) and a.item_category=3 
						and a.is_deleted=0 and a.status_active=1 
						and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in($po_id) and a.to_company='$companyID' and b.pi_wo_batch_no in($batchId) and e.product_name_details='$prod_id' and c.color_id='$color' $today_receive_date  group by a.transfer_system_id, a.challan_no, a.transfer_date,c.color_id, b.prod_id,b.rack, b.order_rate,b.pi_wo_batch_no";
					
				
					

					$dtlsArray=sql_select($mrr_sql);
					$dtlsArray_trns_in=sql_select($mrr_trns_in_sql);
					$dtlsArray_issue_rtn=sql_select($mrr_issue_rtn_sql);
				?>
			<table border="1" class="rpt_table" rules="all" width="1335" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th colspan="14">Receive Details</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="110">System ID</th>
						<th width="70">Receive Date</th>
						<th width="80">Dyeing Source</th>
						<th width="110">Dyeing Company</th>
						<th width="100">Challan No</th>
						<th width="80">Color</th>
						<th width="100">Batch No</th>
						<th width="80">Fin. Rcv. Qty.</th>
						<th width="50">Rate</th>
						<th width="80">Amount</th>
						<th width="200">Fabric Des.</th>
						<th width="50">GSM</th>
						<th width="50">F.Dia</th>

					</tr>
				</thead>
				<tbody>
					<?
					$i=1;
					foreach($dtlsArray as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$tot_reject=$row[csf('returnable_qnty')];

						if($row[csf('knitting_source')]==1)
						{
							$knitting_company=$company_arr[$row[csf('knitting_company')]];
						}
						else
						{
							$knitting_company=$supplier_name_arr[$row[csf('knitting_company')]];
						}

						$gsm=$prod_array[$row[csf('prod_id')]]['gsm'];
						$dia=$prod_array[$row[csf('prod_id')]]['dia'];
						$description = $prod_array[$row[csf('prod_id')]]['name_details'];
						
						//amount calculating
						$amount = ($row[csf('quantity')]*$row[csf('order_rate')]);
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
							<td width="110"><p><? echo $row[csf('recv_number')]; ?></p></td>
							<td width="70"><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
							<td width="80"><p><? echo $knitting_source[$row[csf('knitting_source')]]; ?></p></td>
							<td width="110"><p><? echo $knitting_company; ?></p></td>
							<td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>
							<td width="80"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
							<td width="100"><p><? echo $batch_no_arr[$row[csf('batch_id')]]; ?></p></td>
							<td width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
							<td width="50" align="right"><p><? echo number_format($row[csf('order_rate')],2); ?></p></td>
							<td width="80" align="right"><p><? echo number_format($amount,2); ?></p></td>
							<td width="200" ><p><? echo trim($description," , "); ?></p></td>
							<td width="50" align="center"><p><? echo $gsm; ?>&nbsp;</p></td>
							<td width="50" align="center"><p><? echo $dia; ?></p></td>
						</tr>
						<?
						$tot_qty+=$row[csf('quantity')];
						$tot_amount+=number_format($amount,2,'.','');
						$tot_reject_qty+=$row[csf('returnable_qnty')];
						$i++;

					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="8" align="right">Total</td>
						<td align="right"><? echo number_format($tot_qty,2); ?> </td>
						<td align="right"></td>
						<td align="right"><? echo number_format($tot_amount,2); ?> </td>
						<td align="right">&nbsp;</td>
						<td align="right">&nbsp;</td>
						<td align="right">&nbsp;</td>
					</tr>
				</tfoot>
			</table>
			<table border="1" class="rpt_table" rules="all" width="1335" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th colspan="14">Transfer In Details</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="110">System ID</th>
						<th width="70">Receive Date</th>
						<th width="80">Dyeing Source</th>
						<th width="110">Dyeing Company</th>
						<th width="100">Challan No</th>
						<th width="80">Color</th>
						<th width="100">Batch No</th>
						<th width="80">Fin. Rcv. Qty.</th>
						<th width="50">Rate</th>
						<th width="80">Amount</th>
						<th width="200">Fabric Des.</th>
						<th width="50">GSM</th>
						<th width="50">F.Dia</th>

					</tr>
				</thead>
				<tbody>
					<?
					$i=1;
					foreach($dtlsArray_trns_in as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$tot_reject=$row[csf('returnable_qnty')];

						if($row[csf('knitting_source')]==1)
						{
							$knitting_company=$company_arr[$row[csf('knitting_company')]];
						}
						else
						{
							$knitting_company=$supplier_name_arr[$row[csf('knitting_company')]];
						}

						$gsm=$prod_array[$row[csf('prod_id')]]['gsm'];
						$dia=$prod_array[$row[csf('prod_id')]]['dia'];
						$description = $prod_array[$row[csf('prod_id')]]['name_details'];
						
						//amount calculating
						$amount = ($row[csf('quantity')]*$row[csf('order_rate')]);
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
							<td width="110"><p><? echo $row[csf('recv_number')]; ?></p></td>
							<td width="70"><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
							<td width="80"><p><? echo $knitting_source[$row[csf('knitting_source')]]; ?></p></td>
							<td width="110"><p><? echo $knitting_company; ?></p></td>
							<td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>
							<td width="80"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
							<td width="80"><p><? echo $batch_no_arr[$row[csf('batch_id')]]; ?></p></td>
							<td width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
							<td width="50" align="right"><p><? echo number_format($row[csf('order_rate')],2); ?></p></td>
							<td width="80" align="right"><p><? echo number_format($amount,2); ?></p></td>
							<td width="200" ><p><? echo trim($description," , "); ?></p></td>
							<td width="50" align="center"><p><? echo $gsm; ?>&nbsp;</p></td>
							<td width="50" align="center"><p><? echo $dia; ?></p></td>
						</tr>
						<?
						$tot_qty2+=$row[csf('quantity')];
						$tot_amount2+=number_format($amount,2,'.','');
						$tot_reject_qty2+=$row[csf('returnable_qnty')];
						$i++;

					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="8" align="right">Total</td>
						<td align="right"><? echo number_format($tot_qty2,2); ?> </td>
						<td align="right"></td>
						<td align="right"><? echo number_format($tot_amount2,2); ?> </td>
						<td align="right">&nbsp;</td>
						<td align="right">&nbsp;</td>
						<td align="right">&nbsp;</td>
					</tr>
				</tfoot>
			</table>
			<table border="1" class="rpt_table" rules="all" width="1335" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th colspan="14">Issue Return Details</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="110">System ID</th>
						<th width="70">Receive Date</th>
						<th width="80">Dyeing Source</th>
						<th width="110">Dyeing Company</th>
						<th width="100">Challan No</th>
						<th width="80">Color</th>
						<th width="100">Batch No</th>
						<th width="80">Fin. Rcv. Qty.</th>
						<th width="50">Rate</th>
						<th width="80">Amount</th>
						<th width="200">Fabric Des.</th>
						<th width="50">GSM</th>
						<th width="50">F.Dia</th>

					</tr>
				</thead>
				<tbody>
					<?
					$i=1;
					foreach($dtlsArray_issue_rtn as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						$tot_reject=$row[csf('returnable_qnty')];

						if($row[csf('knitting_source')]==1)
						{
							$knitting_company=$company_arr[$row[csf('knitting_company')]];
						}
						else
						{
							$knitting_company=$supplier_name_arr[$row[csf('knitting_company')]];
						}

						$gsm=$prod_array[$row[csf('prod_id')]]['gsm'];
						$dia=$prod_array[$row[csf('prod_id')]]['dia'];
						$description = $prod_array[$row[csf('prod_id')]]['name_details'];
						
						//amount calculating
						$amount = ($row[csf('quantity')]*$row[csf('order_rate')]);
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30"><p><? echo $i; ?></p></td>
							<td width="110"><p><? echo $row[csf('recv_number')]; ?></p></td>
							<td width="70"><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
							<td width="80"><p><? echo $knitting_source[$row[csf('knitting_source')]]; ?></p></td>
							<td width="110"><p><? echo $knitting_company; ?></p></td>
							<td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>
							<td width="80"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
							<td width="80"><p><? echo $batch_no_arr[$row[csf('batch_id')]]; ?></p></td>
							<td width="80" align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
							<td width="50" align="right"><p><? echo number_format($row[csf('order_rate')],2); ?></p></td>
							<td width="80" align="right"><p><? echo number_format($amount,2); ?></p></td>
							<td width="200" ><p><? echo trim($description," , "); ?></p></td>
							<td width="50" align="center"><p><? echo $gsm; ?>&nbsp;</p></td>
							<td width="50" align="center"><p><? echo $dia; ?></p></td>
						</tr>
						<?
						$tot_qty3+=$row[csf('quantity')];
						$tot_amount3+=number_format($amount,2,'.','');
						$tot_reject_qty3+=$row[csf('returnable_qnty')];
						$i++;

					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="8" align="right">Total</td>
						<td align="right"><? echo number_format($tot_qty3,2); ?> </td>
						<td align="right"></td>
						<td align="right"><? echo number_format($tot_amount3,2); ?> </td>
						<td align="right">&nbsp;</td>
						<td align="right">&nbsp;</td>
						<td align="right">&nbsp;</td>
					</tr>
				</tfoot>
			</table>
		</div>
	</fieldset>
	<?
	$html=ob_get_contents();
	ob_flush();

	foreach (glob(""."*.xls") as $filename)
	{
		@unlink($filename);
	}

			//html to xls convert
	$name=time();
	$name=$user_id."_".$name.".xls";
	$create_new_excel = fopen(''.$name, 'w');
	$is_created = fwrite($create_new_excel,$html);

	?>
	<input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />
	<script>
		$(document).ready(function(e) {
			document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
		});

	</script>
	<?
	exit();
}


if($action=="woven_issue_popup")
{
	echo load_html_head_contents("Issue Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
				'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_id').innerHTML+'</body</html>');
			d.close();
		}
	</script>
	<?
	ob_start();
	$color_arr=return_library_array( "select id,color_name from lib_color where id in(".$color.")", "id", "color_name"  );
	$batch_no_arr=return_library_array( "select id,batch_no from pro_batch_create_mst where id in(".$batchId.")", "id", "batch_no"  );

	?>
	<fieldset style="width:1130px; margin-left:3px">
		<div id="report_id" align="center">
			<table border="0" class="rpt_table" rules="all" align="center" cellpadding="0" cellspacing="0">
				<tr>
					<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
					<td> <div id="report_container"> </div> </td>
				</tr>
			</table>
			<table border="1" class="rpt_table" rules="all" width="1210" cellpadding="0" cellspacing="0" align="left">
				<thead>
					<tr>
						<th colspan="11">Issue To Cutting Info</th>
					</tr>
					<tr>
						<th width="30">Sl</th>
						<th width="110">Issue No</th>
						<th width="120">Issue to Company</th>
						<th width="100">Challan No</th>
						<th width="100">Issue Date</th>
						<th width="100">Color</th>
                        <th width="100">Batch No</th>
                        <!-- <th width="60">Rack No</th> -->
                        <th width="80">Qty</th>
                        <th width="50">Rate</th>
                        <th width="80">Amount</th>
                        <th width="">Fabric Des.</th>
                    </tr>
                </thead>
                <tbody>
                    <?
                    $i=1;
					
						$mrr_sql="select x.company_id, x.issue_number, x.challan_no,x.issue_date, sum(x.quantity) as quantity,x.color_id,x.prod_id,x.rack.x.batch_id from(select a.company_id, a.issue_number, a.challan_no,a.issue_date, sum(c.quantity) as quantity,c.color_id,c.prod_id,b.rack,b.batch_id
	                    from  inv_issue_master a, inv_transaction b, order_wise_pro_details c,product_details_master e 
	                    where a.id=b.mst_id and b.id=c.trans_id and c.prod_id=e.id and b.prod_id=e.id  and a.entry_form=19 and c.entry_form=19 and a.item_category=3 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in ($po_id) and a.company_id='$companyID' and b.batch_id in($batchId) and e.product_name_details='$prod_id' and c.color_id='$color' and b.transaction_date <='$from_date'  group by a.company_id, a.issue_number, a.challan_no,a.issue_date,c.color_id,c.prod_id,b.rack,b.batch_id 
						union all  
						
						select a.company_id, a.transfer_system_id as issue_number, a.challan_no,a. transfer_date as issue_date, sum(c.quantity) as quantity,c.color_id,c.prod_id,b.rack,b.pi_wo_batch_no as batch_id  
						from inv_item_transfer_mst a, inv_item_transfer_dtls d, inv_transaction b, order_wise_pro_details c,product_details_master e  
						where a.id=d.mst_id and d.trans_id=b.id and a.id=b.mst_id and b.id=c.trans_id and c.prod_id=e.id and b.prod_id=e.id  and b.transaction_type=6 and c. trans_type=6 and a.entry_form in (258) and c.entry_form in (258) 
						and a.item_category=3 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in($po_id) 
						and a.company_id='$companyID' and b.pi_wo_batch_no in($batchId) and e.product_name_details='$prod_id'  and c.color_id='$color' and b.transaction_date<='$from_date'  
						group by a.company_id, a.transfer_system_id, a.challan_no, a.transfer_date,c.color_id,c.prod_id,b.rack,b.pi_wo_batch_no )x group by  x.company_id, x.issue_number, x.challan_no,x.issue_date,x.color_id,x.prod_id,x.rack,x.batch_id order by x.issue_date";
					
                    $dtlsArray=sql_select($mrr_sql);
					
					$poIdArr = array();
					foreach($dtlsArray as $row)
                    {
						$poIdArr[$row[csf('prod_id')]] = $row[csf('prod_id')];
					}
                    $product_arr=return_library_array( "select id, product_name_details from product_details_master where id in(".implode(",",$poIdArr).")", "id", "product_name_details"  );

                    foreach($dtlsArray as $row)
                    {
                        if ($i%2==0)
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";
							
						//amount calculating
						$amount = ($row[csf('quantity')]*$rate);
                        ?>
                        <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="30"><p><? echo $i; ?></p></td>
                            <td width="110"><p><? echo $row[csf('issue_number')]; ?></p></td>
                            <td width="120"><p><? echo $company_arr[$row[csf('company_id')]]; ?></p></td>
                            <td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>
                            <td align="center" width="100"><p><? echo change_date_format($row[csf('issue_date')]); ?></p></td>
                            <td align="center" width="100"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
                            <td width="100"><p><? echo $batch_no_arr[$row[csf('batch_id')]]; ?></p></td>
                            <!--<td width="60" ><p><? //echo $row[csf('rack')]; ?></p></td> -->
                            <td width="80" align="right" ><p><? echo number_format($row[csf('quantity')],2); ?> &nbsp;</p></td>
                            <td width="50" align="right" ><p><? echo number_format($rate,2); ?> &nbsp;</p></td>
                            <td width="80" align="right" ><p><? echo number_format($amount,2); ?> &nbsp;</p></td>
                            <td><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
                        </tr>
                        <?
                        $tot_qty+=$row[csf('quantity')];
                        $tot_amount+=number_format($amount,2,'.','');
                        $i++;
                    }
                    ?>
                </tbody>
                <tfoot>
                    <tr class="tbl_bottom">
                        <td colspan="7" align="right">Total</td>
                        <td align="right"><? echo number_format($tot_qty,2); ?>&nbsp;</td>
                        <td align="right"></td>
                        <td align="right"><? echo number_format($tot_amount,2); ?>&nbsp;</td>
                        <td>&nbsp; </td>
                    </tr>
                </tfoot>
            </table>
            
            <table border="1" class="rpt_table" rules="all" width="1210" cellpadding="0" cellspacing="0" align="left">
                <thead>
                    <tr>
                        <th colspan="11">Receive Return To Supplier</th>
                    </tr>
                    <tr>
                        <th width="30">Sl</th>
                        <th width="110">Issue No</th>
                        <th width="120">Issue to Company</th>
                        <th width="100">Challan No</th>
                        <th width="100">Issue Date</th>
                        <th width="100">Color</th>
                    	 <th width="100">Batch No</th>
                        <!--<th width="60">Rack No</th> -->
                        <th width="80">Qty</th>
                        <th width="50">Rate</th>
                        <th width="80">Amount</th>
                        <th width="">Fabric Des.</th>
                    </tr>
                </thead>
                <tbody>
                    <?
                    $i=1;
                    
						$mrr_sql_recv_rtrn="select a.company_id, a.issue_number, a.challan_no,a.issue_date, b.prod_id, sum(c.quantity) as quantity,c.color_id,c.prod_id,b.rack,b.pi_wo_batch_no as batch_id
	                    from  inv_issue_master a, inv_transaction b, order_wise_pro_details c,product_details_master e 
	                    where a.id=b.mst_id and b.id=c.trans_id  and c.prod_id=e.id and b.prod_id=e.id and a.entry_form=202 and c.entry_form=202 and a.item_category=3 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in ($po_id) and a.company_id='$companyID' and b.pi_wo_batch_no='".$batchId."' and e.product_name_details='$prod_id' and c.color_id='$color' group by a.company_id, a.issue_number, a.challan_no,a.issue_date, b.prod_id,c.color_id,c.prod_id,b.rack,b.pi_wo_batch_no order by a.issue_date";
               	 	
                    $dtlsArray=sql_select($mrr_sql_recv_rtrn);
					$poIdArr = array();
					foreach($dtlsArray as $row)
                    {
						$poIdArr[$row[csf('prod_id')]] = $row[csf('prod_id')];
					}
                    $product_arr=return_library_array( "select id, product_name_details from product_details_master where id in(".implode(",",$poIdArr).")", "id", "product_name_details"  );

                    foreach($dtlsArray as $row)
                    {
                        if ($i%2==0)
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";
							
						//amount calculating
						$amount = ($row[csf('quantity')]*$rate);
                        ?>
                        <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="30"><p><? echo $i; ?></p></td>
                            <td width="110"><p><? echo $row[csf('issue_number')]; ?></p></td>
                            <td width="120"><p><? echo $company_arr[$row[csf('company_id')]]; ?></p></td>
                            <td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>
                            <td align="center" width="100"><p><? echo change_date_format($row[csf('issue_date')]); ?></p></td>
                            <td align="center" width="100"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
                            <td width="100"><p><? echo $batch_no_arr[$row[csf('batch_id')]]; ?></p></td>
                            <!--<td width="60" ><p><? //echo $row[csf('rack')]; ?></p></td> -->
                            <td width="80" align="right" ><p><? echo number_format($row[csf('quantity')],2); ?> &nbsp;</p></td>
                            <td width="50" align="right" ><p><? echo number_format($rate,2); ?> &nbsp;</p></td>
                            <td width="80" align="right" ><p><? echo number_format($amount,2); ?> &nbsp;</p></td>
                            <td><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
                        </tr>
                        <?
                        $tot_qty2+=$row[csf('quantity')];
                        $tot_amount2+=number_format($amount,2,'.','');
                        $i++;
                    }
                    ?>
                </tbody>
                <tfoot>
                    <tr class="tbl_bottom">
                        <td colspan="7" align="right">Total</td>
                        <td align="right"><? echo number_format($tot_qty2,2); ?>&nbsp;</td>
                        <td align="right"></td>
                        <td align="right"><? echo number_format($tot_amount2,2); ?>&nbsp;</td>
                        <td>&nbsp; </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </fieldset>
	<?
    $html=ob_get_contents();
    ob_flush();

    foreach (glob(""."*.xls") as $filename)
    {
        @unlink($filename);
    }
        //html to xls convert
    $name=time();
    $name=$user_id."_".$name.".xls";
    $create_new_excel = fopen(''.$name, 'w');
    $is_created = fwrite($create_new_excel,$html);
    ?>
    <input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />
    <script>
        $(document).ready(function(e) {
            document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
        });

    </script>
    <?
    exit();
}

if($action=="woven_today_issue_popup")
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<fieldset style="width:1150px; margin-left:3px">
		<script>
			function print_window()
			{
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
					'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
				d.close();
			}

		</script>
		<?
		ob_start();
		$color_arr=return_library_array( "select id,color_name from lib_color where id in(".$color.")", "id", "color_name"  );
		$batch_no_arr=return_library_array( "select id,batch_no from pro_batch_create_mst where id in(".$batchId.")", "id", "batch_no"  );

		?>
		<div id="scroll_body" align="center">
			<table border="0" class="rpt_table" rules="all" cellpadding="0" cellspacing="0">
				<tr>
					<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
					<td> <div id="report_container"> </div> </td>
				</tr>
			</table>

			
					<?
					$prodData=sql_select("select id, item_description,unit_of_measure, product_name_details, detarmination_id, gsm, dia_width from product_details_master where item_category_id in(3) and status_active=1 and is_deleted=0");
					$prod_array=array();
					foreach($prodData as $row)
					{
						$prod_array[$row[csf('id')]]['gsm']=$row[csf('gsm')];
						$prod_array[$row[csf('id')]]['dia']=$row[csf('dia_width')];
						$prod_array[$row[csf('id')]]['name_details']=$row[csf('product_name_details')];
					}

					$date_from=str_replace("'","",$from_date);
					if( $date_from=="") $today_issue_date=""; else $today_issue_date= "and b.transaction_date='".$from_date."'";
					/*select a.company_id,a.recv_number as issue_number, a.challan_no,a.receive_date as issue_date, b.prod_id, c.quantity,c.color_id,c.prod_id,b.rack
					from  inv_receive_master a, inv_transaction b, order_wise_pro_details c
					where a.id=b.mst_id and b.id=c.trans_id  and a.entry_form in (209) and c.entry_form in (209)  and a.item_category=3 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and c.prod_id=$prod_id and c.color_id='$color' $today_issue_date */
					
						$mrr_issue_sql="select a.company_id, a.issue_number, a.challan_no,a.issue_date, b.prod_id, sum(c.quantity) as quantity,c.color_id,c.prod_id,b.rack,b.batch_id 
						from inv_issue_master a, inv_transaction b, order_wise_pro_details c,product_details_master e 
						where a.id=b.mst_id and b.id=c.trans_id and b.prod_id=e.id and c.prod_id=e.id  and a.entry_form=19 and c.entry_form=19 and a.item_category=3 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in ($po_id) and a.company_id='$companyID' and b.batch_id in($batchId) and e.product_name_details='$prod_id' and c.color_id='$color' $today_issue_date 
						  group by  a.company_id, a.issue_number, a.challan_no,a.issue_date, b.prod_id,c.color_id,c.prod_id,b.rack,b.batch_id"; 
						
						$mrr_recvRtn_sql="select a.company_id, a.issue_number, a.challan_no,a.issue_date, b.prod_id, sum(c.quantity) as quantity,c.color_id,c.prod_id,b.rack,b.pi_wo_batch_no as batch_id
						from inv_issue_master a, inv_transaction b, order_wise_pro_details c,product_details_master e 
						where a.id=b.mst_id and b.id=c.trans_id and b.prod_id=e.id and c.prod_id=e.id and  a.entry_form=202 and c.entry_form=202 and a.item_category=3 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in ($po_id) and a.company_id='$companyID' and b.pi_wo_batch_no in($batchId) and e.product_name_details='$prod_id' and c.color_id='$color' $today_issue_date 
						group by  a.company_id, a.issue_number, a.challan_no,a.issue_date, b.prod_id,c.color_id,c.prod_id,b.rack,b.pi_wo_batch_no"; 
						
						$mrr_transOut_sql="select a.company_id, a.transfer_system_id as issue_number, a.challan_no,a. transfer_date as issue_date, b.prod_id, sum(c.quantity) as quantity,c.color_id,c.prod_id,b.rack,b.pi_wo_batch_no as batch_id
						from inv_item_transfer_mst a, inv_item_transfer_dtls d, inv_transaction b, order_wise_pro_details c,product_details_master e  
						where a.id=d.mst_id and d.trans_id=b.id and a.id=b.mst_id and b.id=c.trans_id and b.prod_id=e.id and c.prod_id=e.id and b.transaction_type=6 and c. trans_type=6 and a.entry_form in (258) and c.entry_form in (258) 
						and a.item_category=3 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in ($po_id) 
						and a.company_id='$companyID' and b.pi_wo_batch_no in($batchId) and e.product_name_details='$prod_id' and c.color_id='$color' $today_issue_date 
						  group by a.company_id, a.transfer_system_id, a.challan_no,a. transfer_date, b.prod_id,c.color_id,c.prod_id,b.rack,b.pi_wo_batch_no";
					
					
						?>
						<table border="1" class="rpt_table" rules="all" width="1230" cellpadding="0" cellspacing="0" align="center">
							<thead>
								<tr>
									<th colspan="11">Issue To Cutting Info</th>
								</tr>
								<tr>
									<th width="30">Sl</th>
									<th width="110">Issue No</th>
									<th width="120">Issue to Company</th>
									<th width="100">Challan No</th>
									<th width="100">Issue Date</th>
									<th width="100">Color</th>
									<th width="100">Batch No</th>
									<th width="80">Qty</th>
									<th width="50">Rate</th>
									<th width="80">Amount</th>
									<th width="">Fabric Des.</th>
								</tr>
							</thead>
							<tbody>
								<?

								$dtlsArray_issue=sql_select($mrr_issue_sql);

								$i=1;
								foreach($dtlsArray_issue as $row)
								{
									if ($i%2==0)
										$bgcolor="#E9F3FF";
									else
										$bgcolor="#FFFFFF";

									$description = $prod_array[$row[csf('prod_id')]]['name_details'];
									$amount = ($row[csf('quantity')]*$rate);
									?>
									<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
										<td width="30"><p><? echo $i; ?></p></td>
										<td width="110"><p><? echo $row[csf('issue_number')]; ?></p></td>
										<td width="120"><p><? echo $company_arr[$row[csf('company_id')]]; ?></p></td>
										<td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>
										<td width="100"><p><? echo change_date_format($row[csf('issue_date')]); ?></p></td>
										<td width="100"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
										<td width="100"><p><? echo $batch_no_arr[$row[csf('batch_id')]]; ?></p></td>
										<td width="80" align="right" ><p><? echo number_format($row[csf('quantity')],2); ?> &nbsp;</p></td>
										<td width="50" align="right" ><p><? echo number_format($rate); ?> &nbsp;</p></td>
										<td width="80" align="right" ><p><? echo number_format($amount,2); ?> &nbsp;</p></td>
										<td  align="right"><p><? echo trim($description, " , "); ?></p></td>
									</tr>
									<?
									$tot_qty_issue+=$row[csf('quantity')];
									$tot_amount_issue+=number_format($amount,2,'.','');
									$i++;
								}
								?>
							</tbody>
							<tfoot>
								<tr class="tbl_bottom">
									<td colspan="7" align="right">Total</td>
									<td align="right"><? echo number_format($tot_qty_issue,2); ?></td>
									<td align="right"></td>
									<td align="right"><? echo number_format($tot_amount_issue,2); ?></td>
									<td>&nbsp; </td>
								</tr>
							</tfoot>
						</table>
						<table border="1" class="rpt_table" rules="all" width="1230" cellpadding="0" cellspacing="0" align="center">
							<thead>
								<tr>
									<th colspan="11">Receive Return To Supplier</th>
								</tr>
								<tr>
									<th width="30">Sl</th>
									<th width="110">Issue No</th>
									<th width="120">Issue to Company</th>
									<th width="100">Challan No</th>
									<th width="100">Issue Date</th>
									<th width="100">Color</th>
									<th width="100">Batch No</th>
									<th width="80">Qty</th>
									<th width="50">Rate</th>
									<th width="80">Amount</th>
									<th width="">Fabric Des.</th>
								</tr>
							</thead>
							<tbody>
								<?

								$dtlsArray_recv_rtn=sql_select($mrr_recvRtn_sql);

								$i=1;
								foreach($dtlsArray_recv_rtn as $row)
								{
									if ($i%2==0)
										$bgcolor="#E9F3FF";
									else
										$bgcolor="#FFFFFF";

									$description_rcvrtn = $prod_array[$row[csf('prod_id')]]['name_details'];
									$amount_rcvrtn = ($row[csf('quantity')]*$rate);
									?>
									<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
										<td width="30"><p><? echo $i; ?></p></td>
										<td width="110"><p><? echo $row[csf('issue_number')]; ?></p></td>
										<td width="120"><p><? echo $company_arr[$row[csf('company_id')]]; ?></p></td>
										<td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>
										<td width="100"><p><? echo change_date_format($row[csf('issue_date')]); ?></p></td>
										<td width="100"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
										<td width="100"><p><? echo $batch_no_arr[$row[csf('batch_id')]]; ?></p></td>
										<td width="80" align="right" ><p><? echo number_format($row[csf('quantity')],2); ?> &nbsp;</p></td>
										<td width="50" align="right" ><p><? echo number_format($rate); ?> &nbsp;</p></td>
										<td width="80" align="right" ><p><? echo number_format($amount_rcvrtn,2); ?> &nbsp;</p></td>
										<td  align="right"><p><? echo trim($description_rcvrtn, " , "); ?></p></td>
									</tr>
									<?
									$tot_qty_rcvrtn+=$row[csf('quantity')];
									$tot_amount_rcvrtn+=number_format($amount_rcvrtn,2,'.','');
									$i++;
								}
								?>
							</tbody>
							<tfoot>
								<tr class="tbl_bottom">
									<td colspan="7" align="right">Total</td>
									<td align="right"><? echo number_format($tot_qty_rcvrtn,2); ?></td>
									<td align="right"></td>
									<td align="right"><? echo number_format($tot_amount_rcvrtn,2); ?></td>
									<td>&nbsp; </td>
								</tr>
							</tfoot>
						</table>
						<table border="1" class="rpt_table" rules="all" width="1230" cellpadding="0" cellspacing="0" align="center">
							<thead>
								<tr>
									<th colspan="11">Transfer To Supplier</th>
								</tr>
								<tr>
									<th width="30">Sl</th>
									<th width="110">Issue No</th>
									<th width="120">Issue to Company</th>
									<th width="100">Challan No</th>
									<th width="100">Issue Date</th>
									<th width="100">Color</th>
									<th width="100">Batch No</th>
									<th width="80">Qty</th>
									<th width="50">Rate</th>
									<th width="80">Amount</th>
									<th width="">Fabric Des.</th>
								</tr>
							</thead>
							<tbody>
								<?

								$dtlsArray_transOut=sql_select($mrr_transOut_sql);

								$i=1;
								foreach($dtlsArray_transOut as $row)
								{
									if ($i%2==0)
										$bgcolor="#E9F3FF";
									else
										$bgcolor="#FFFFFF";

									$description_transOut = $prod_array[$row[csf('prod_id')]]['name_details'];
									$amount_transOut = ($row[csf('quantity')]*$rate);
									?>
									<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
										<td width="30"><p><? echo $i; ?></p></td>
										<td width="110"><p><? echo $row[csf('issue_number')]; ?></p></td>
										<td width="120"><p><? echo $company_arr[$row[csf('company_id')]]; ?></p></td>
										<td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>
										<td width="100"><p><? echo change_date_format($row[csf('issue_date')]); ?></p></td>
										<td width="100"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
										<td width="100"><p><? echo $batch_no_arr[$row[csf('batch_id')]]; ?></p></td>
										<td width="80" align="right" ><p><? echo number_format($row[csf('quantity')],2); ?> &nbsp;</p></td>
										<td width="50" align="right" ><p><? echo number_format($rate); ?> &nbsp;</p></td>
										<td width="80" align="right" ><p><? echo number_format($amount_transOut,2); ?> &nbsp;</p></td>
										<td  align="right"><p><? echo trim($description_transOut, " , "); ?></p></td>
									</tr>
									<?
									$tot_qty_transOut+=$row[csf('quantity')];
									$tot_amount_transOut+=number_format($amount_transOut,2,'.','');
									$i++;
								}
								?>
							</tbody>
							<tfoot>
								<tr class="tbl_bottom">
									<td colspan="7" align="right">Total</td>
									<td align="right"><? echo number_format($tot_qty_transOut,2); ?></td>
									<td align="right"></td>
									<td align="right"><? echo number_format($tot_amount_transOut,2); ?></td>
									<td>&nbsp; </td>
								</tr>
							</tfoot>
						</table>
						
		</div>
	</fieldset>
	<?
	$html=ob_get_contents();
	ob_flush();

	foreach (glob(""."*.xls") as $filename)
	{
		@unlink($filename);
	}

	//html to xls convert
	$name=time();
	$name=$user_id."_".$name.".xls";
	$create_new_excel = fopen(''.$name, 'w');
	$is_created = fwrite($create_new_excel,$html);

	?>
	<input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />
	<script>
		$(document).ready(function(e) {
			document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
		});

	</script>
	<?
	exit();
}

if($action=="woven_knit_stock_popup") //Stock
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $po_id."**".$color;die;
	?>
	<script>
		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
				'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_id').innerHTML+'</body</html>');
			d.close();
		}
	</script>
	<?
	ob_start();
	?>
	<fieldset style="width:570px; margin-left:3px">
		<div id="report_id" align="center">
			<table border="0" class="rpt_table" rules="all" align="center" cellpadding="0" cellspacing="0">
				<tr>
					<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
					<td> <div id="report_container"> </div> </td>
				</tr>
			</table>
			<table border="1" class="rpt_table" rules="all" width="850" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th colspan="8"> Woven Stock Details</th>
					</tr>
					<tr>
						<th width="50">Sl</th>
						<th width="80">Product ID</th>
						<th width="200">Batch No</th>
						<th width="100">Floor</th>
						<th width="100">Room</th>
						<th width="100">Rack</th>
						<th width="100">Shelf</th>
						<th>Qty</th>
					</tr>
				</thead>
				<tbody>
					<?

					
						$mrr_sql="
						select c.prod_id, b.floor_id, b.room, b.rack, b.self,
							sum(case when c.entry_form in (17) then c.quantity else 0 end) as recv_qnty,
							sum(case when c.entry_form in (19) then c.quantity else 0 end) as issue_qnty,
							 0  as issue_retn_qnty, 
							 0 as recv_rtn_qnty ,0 as finish_fabric_transfer_in,
							 0 as finish_fabric_transfer_out 
						from
							inv_transaction b,
							order_wise_pro_details c,product_details_master e 
						where
							b.id=c.trans_id  and c.prod_id=e.id and b.prod_id=e.id and c.entry_form in(17,19,258,209,202) and b.item_category=3 and c.is_deleted=0 and c.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in (".$po_id.") and c.color_id='".$color."' and b.batch_id in($batchId) and e.product_name_details='".$prod_id."'  and b.transaction_date <='$from_date' 
						group by
							c.prod_id, b.floor_id, b.room, b.rack, b.self 

						union all

						select c.prod_id, b.floor_id, b.room, b.rack, b.self,0 as recv_qnty, 0 as issue_qnty,
							 sum(case when c.entry_form in (209) and b.transaction_type=4 then c.quantity else 0 end) as issue_retn_qnty, 
							 sum(case when c.entry_form in (202) and b.transaction_type=3 then c.quantity else 0 end) as recv_rtn_qnty,
							 sum (case when c.entry_form in (258) and b.transaction_type=5 then c.quantity else 0 end) as finish_fabric_transfer_in, 
							 sum (case when c.entry_form in (258) and b.transaction_type=6 then c.quantity else 0 end) as finish_fabric_transfer_out 
						from
							inv_transaction b,
							order_wise_pro_details c,product_details_master e 
						where
							b.id=c.trans_id  and c.prod_id=e.id and b.prod_id=e.id and c.entry_form in(17,19,258,209,202) and b.item_category=3 and c.is_deleted=0 and c.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.po_breakdown_id in (".$po_id.") and c.color_id='".$color."' and b.pi_wo_batch_no in($batchId) and e.product_name_details='".$prod_id."'  and b.transaction_date <='$from_date' 
						group by
							c.prod_id, b.floor_id, b.room, b.rack, b.self 

							";
				
					
					//echo $mrr_sql;
					$dtlsArray=sql_select($mrr_sql);
					if(empty($dtlsArray))
					{
						echo get_empty_data_msg();
						die;
					}
					
					$batchIdArr = array();
					$floorIdArr = array();
					$roomIdArr = array();
					$rackIdArr = array();
					$shelfIdArr = array();
					foreach($dtlsArray as $row)
					{
						
						$batchIdArr[$batchId] = $batchId;
						
						
						$row[csf('floor_id')] = ($row[csf('floor_id')]*1);
						$row[csf('room')] = ($row[csf('room')]*1);
						$row[csf('rack')] = ($row[csf('rack')]*1);
						$row[csf('self')] = ($row[csf('self')]*1);
						
						if($row[csf('floor_id')] != 0)
						{
							$floorIdArr[$row[csf('floor_id')]] = $row[csf('floor_id')];
						}
						
						if($row[csf('room')] != 0)
						{
							$roomIdArr[$row[csf('room')]] = $row[csf('room')];
						}
						
						if($row[csf('rack')] != 0)
						{
							$rackIdArr[$row[csf('rack')]] = $row[csf('rack')];
						}
						
						if($row[csf('self')] != 0)
						{
							$shelfIdArr[$row[csf('self')]] = $row[csf('self')];
						}
					}
					
					//product_name_details
					$product_arr=return_library_array( "select id, product_name_details from product_details_master where id in (".$po_id.") and status_active = 1 and is_deleted = 0", "id", "product_name_details"  );
					
					//pro_batch_create_mst
					$batchCondition = '';
					if(!empty($batchIdArr))
					{
						$batchCondition = " and id in(".implode(",", $batchIdArr).")";
					}
					$batch_no_arr=return_library_array( "select id, batch_no from pro_batch_create_mst where status_active = 1 and is_deleted = 0 ".$batchCondition."",'id','batch_no');

					
						$batchName="";
						$batchNameArr=explode(',', $batchId);
						foreach ($batchNameArr as $vall) {
						 	$batchName.=$batch_no_arr[$vall].",";
						 } 
						 $batchName=chop($batchName,",");
				
					
					//floorSql
					$floorSql = "
						SELECT a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id
						FROM lib_floor_room_rack_mst a
						INNER JOIN lib_floor_room_rack_dtls b ON a.floor_room_rack_id = b.floor_id
						WHERE a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND a.company_id IN(".$companyID.")
					";
					//echo $floorSql;
					$floorDetails = return_library_array( $floorSql, 'floor_room_rack_id', 'floor_room_rack_name');
					
					//roomSql
					$roomSql = "
						SELECT a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id
						FROM lib_floor_room_rack_mst a
						INNER JOIN lib_floor_room_rack_dtls b ON a.floor_room_rack_id = b.room_id
						WHERE a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND a.company_id IN(".$companyID.")
					";
					$roomDetails = return_library_array( $roomSql, 'floor_room_rack_id', 'floor_room_rack_name');
					
					//rackSql
					$rackSql = "
						SELECT a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id, b.serial_no
						FROM lib_floor_room_rack_mst a
						INNER JOIN lib_floor_room_rack_dtls b ON a.floor_room_rack_id = b.rack_id
						WHERE a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND a.company_id IN(".$companyID.")
					";
					$rackDetails = return_library_array( $rackSql, 'floor_room_rack_id', 'floor_room_rack_name');
					
					/*
					$rackSerialNoSql = "
						SELECT b.floor_room_rack_dtls_id, b.serial_no
						FROM lib_floor_room_rack_mst a
						INNER JOIN lib_floor_room_rack_dtls b ON a.floor_room_rack_id = b.rack_id
						WHERE a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND a.company_id IN(".$companyID.")
						GROUP BY b.floor_room_rack_dtls_id, b.serial_no
						ORDER BY b.serial_no ASC
					";
					$rackSerialNoResult = sql_select($rackSerialNoSql);
					foreach($rackSerialNoResult as $row)
					{
						$rackSerialNoArr[$row[csf('floor_room_rack_dtls_id')]] = $row[csf('serial_no')];
					}
					*/
				
					//selfSql
					
					$shelfSql = "
						SELECT a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id
						FROM lib_floor_room_rack_mst a
						INNER JOIN lib_floor_room_rack_dtls b ON a.floor_room_rack_id = b.shelf_id
						WHERE a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND a.company_id IN(".$companyID.")
					";
					$shelfDetails = return_library_array( $shelfSql, 'floor_room_rack_id', 'floor_room_rack_name');
					
					//binSql
					/*
					$binSql = "
						SELECT a.floor_room_rack_id, a.floor_room_rack_name, a.company_id, b.location_id, b.store_id
						FROM lib_floor_room_rack_mst a
						INNER JOIN lib_floor_room_rack_dtls b ON a.floor_room_rack_id = b.bin_id
						WHERE a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND a.company_id IN(".$companyId.")
					";
					$binDetails = return_library_array( $binSql, 'floor_room_rack_id', 'floor_room_rack_name');					
					*/
					
					$i=1;
					foreach($dtlsArray as $row)
					{
						if ($i%2==0)
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
						
						$row[csf('floor_id')] = (($row[csf('floor_id')]*1) == 0 ? '' : $floorDetails[$row[csf('floor_id')]]);
						$row[csf('room')] = (($row[csf('room')]*1) == 0 ? '' : $roomDetails[$row[csf('room')]]);
						$row[csf('rack')] = (($row[csf('rack')]*1) == 0 ? '' : $rackDetails[$row[csf('rack')]]);
						$row[csf('self')] = (($row[csf('self')]*1) == 0 ? '' : $shelfDetails[$row[csf('self')]]);

						$tot_balance=($row[csf('recv_qnty')]+$row[csf('finish_fabric_transfer_in')]+$row[csf('issue_retn_qnty')])-($row[csf('issue_qnty')]+$row[csf('finish_fabric_transfer_out')]+$row[csf('recv_rtn_qnty')]);
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td align="center"><p><? echo $i; ?></p></td>
							<td align="center" title="<? echo $product_arr[$row[csf('prod_id')]];?>"><p><? echo $row[csf('prod_id')]; ?></p></td>
							<td><p><? echo $batchName; ?></p></td>
							<td align="center"><p><? echo $row[csf('floor_id')]; ?></p></td>
							<td align="center"><p><? echo $row[csf('room')]; ?></p></td>
							<td align="center"><p><? echo $row[csf('rack')]; ?></p></td>
							<td align="center"><p><? echo $row[csf('self')]; ?></p></td>
							<td align="right"><p><? echo number_format($tot_balance,2); ?></p></td>
						</tr>
						<?
						$tot_qty+=$tot_balance;
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="7" align="right">Total</td>
						<td align="right"><? echo number_format($tot_qty,2); ?>&nbsp;</td>
					</tr>
				</tfoot>
			</table>
		</div>
		<?
		$html=ob_get_contents();
		ob_flush();
		foreach (glob(""."*.xls") as $filename)
		{
			@unlink($filename);
		}
			//html to xls convert
		$name=time();
		$name=$user_id."_".$name.".xls";
		$create_new_excel = fopen(''.$name, 'w');
		$is_created = fwrite($create_new_excel,$html);
		?>
		<input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />
		<script>
			$(document).ready(function(e) {
				document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
			});

		</script>
	</fieldset>
	<?
	exit();
}

?>