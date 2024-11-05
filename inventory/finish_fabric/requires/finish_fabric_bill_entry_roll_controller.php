<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
$user_id=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action=="check_conversion_rate")
{
	$data=explode("**",$data);
	$conversion_date=date("Y/m/d");
	$exchange_rate=set_conversion_rate( $data[0], $conversion_date,$data[1] );
	echo $exchange_rate;
	exit();	
}

//====================Location ACTION========
if ($action=="load_drop_down_location")
{
	$data=explode("_",$data);
	$location_arr=return_library_array( "select id, location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 order by location_name",'id','location_name');
	if(count($location_arr)==1) $selected = key($location_arr); else $selected=0;
	echo create_drop_down( "cbo_location", 152, $location_arr,"", 1, "-- Select Location --", $selected, "" );
	exit();
}

//==================== party========
if ($action == "load_drop_down_party") 
{
	$data = explode("_", $data);
	$company_id = $data[1];

	if ($data[0] == 1) {
		echo create_drop_down("cbo_party_id", 152, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 0, "--Select Party--", "$company_id", "", "");
	} else if ($data[0] == 2) {

		//$partysQl = sql_select("select id,tag_company,party_type from lib_buyer where status_active=1 and is_deleted=0 and tag_company='".$company_id."'");
		$partysQl = sql_select("select a.id,a.tag_company,a.party_type from lib_buyer a, lib_buyer_tag_company b where a.id = b.buyer_id and a.status_active=1 and a.is_deleted=0 and b.tag_company='".$company_id."'");


		$buyerId = "";
		foreach ($partysQl as $row) {

			$partyTypeArr = explode(",", $row[csf('party_type')]);

			foreach ($partyTypeArr as $partyType) {
				if($partyType == 3)
				{
					$buyerId .=  $row[csf('id')].",";
				}
			}
		}

		$buyerIds = chop($buyerId,",");

		if($buyerIds!="")
		{
			echo create_drop_down("cbo_party_id", 152, "select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0 and id in($buyerIds)", "id,buyer_name", 1, "--Select Party--", 0, "");
		}


	}
	exit();
}

// =========== Sales order popup ================//
if($action=="fabric_sales_order_popup")
{
	echo load_html_head_contents("Fabric Sales Order Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);

	?>
	<script>
		var selected_id = new Array();
		var selected_name = new Array();
		var selected_fso = new Array();
		var selected_currency_id = new Array();

		function check_all_data(str) {
			tbl_row_count=str.split(',');
			for( var i = 0; i <= tbl_row_count.length; i++ ) {
				js_set_value( tbl_row_count[i] );
			}
		}

		function toggle(x,origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function js_set_value( str ) 
		{
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );

			/*if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) 
			{
				selected_id.push( $('#txt_individual_id' + str).val() );
				selected_name.push( $('#txt_individual' + str).val() );
				selected_fso.push( $('#txt_individual_fso_id' + str).val() );
				selected_currency_id.push( $('#txt_individual_currency_id' + str).val() );
			}
			else 
			{
				for( var i = 0; i < selected_id.length; i++ ) 
				{
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
				selected_fso.splice( i, 1 );
				selected_currency_id.splice( i, 1 );
			}*/

			// ========================================
			/*var ids = $('#txt_individual_id'+str).val();
			var names = $('#txt_individual'+str).val();
			var fsos = $('#txt_individual_fso_id'+str).val();
			var currencys = $('#txt_individual_currency_id'+str).val();

			if(selected_currency_id.length==0)
			{
				selected_currency_id.push( currencys );
			}
			else if( jQuery.inArray( currencys, selected_currency_id )==-1 &&  selected_currency_id.length>0)
			{
				toggle( document.getElementById( 'search' + str ), '#FFFFFF' );
				alert("Multiple Currency is not allowed");return true;
			}
			
			if( jQuery.inArray( ids, selected_id  ) == -1 ) 
			{
				selected_id.push(ids);
				selected_name.push(names);
				selected_fso.push(fsos);
				selected_currency_id.push(currencys);	
			}
			else
			{
				for( var i = 0; i < selected_id.length; i++ )
				{
					if( selected_id[i] == id ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
				selected_fso.splice( i, 1 );
				selected_currency_id.splice( i, 1 );
			}*/
			// -------------------or below----------
			if( jQuery.inArray($('#txt_individual_currency_id' + str).val(), selected_currency_id )== -1) 
			{
				if(selected_currency_id.length > 0)
				{
					toggle( document.getElementById( 'search' + str ), '#FFFFFF' );
					alert("Multiple Currency is not allowed");
				}
				else
				{
					if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) 
					{
						selected_id.push( $('#txt_individual_id' + str).val() );
						selected_name.push( $('#txt_individual' + str).val() );
						selected_fso.push( $('#txt_individual_fso_id' + str).val() );
						selected_currency_id.push( $('#txt_individual_currency_id' + str).val() );	
					} 
					else 
					{
						for( var i = 0; i < selected_id.length; i++ ) 
						{
							if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
						}
						selected_id.splice( i, 1 );
						selected_name.splice( i, 1 );
						selected_fso.splice( i, 1 );
						selected_currency_id.splice( i, 1 );
					}
				}
			}
			else
			{
				if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) 
				{
					selected_id.push( $('#txt_individual_id' + str).val() );
					selected_name.push( $('#txt_individual' + str).val() );
					selected_fso.push( $('#txt_individual_fso_id' + str).val() );
					selected_currency_id.push( $('#txt_individual_currency_id' + str).val() );	
				}
				else 
				{
					for( var i = 0; i < selected_id.length; i++ ) 
					{
						if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
					}
					selected_id.splice( i, 1 );
					selected_name.splice( i, 1 );
					selected_fso.splice( i, 1 );
					selected_currency_id.splice( i, 1 );
				}
			}
			// ========================================

			var id =''; var name = ''; var fsoid=''; var currencyId='';
			for( var i = 0; i < selected_id.length; i++ ) 
			{
				id += selected_id[i] + ',';
				name += selected_name[i] + '*';
				fsoid += selected_fso[i] + ',';
				currencyId += selected_currency_id[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			fsoid = fsoid.substr( 0, fsoid.length - 1 );
			currencyId = currencyId.substr( 0, currencyId.length - 1 );
			// alert(currencyId);
			$('#txt_selected_id').val( id );
			$('#txt_selected').val( name );
			$('#txt_selected_fso').val( fsoid );
			$('#txt_currency_id').val( currencyId );
		}
	</script>
</head>
<body>
	<div align="center" style="width:1100px;">
		<form name="searchwofrm"  id="searchwofrm">
			<fieldset style="width:1000px; margin-left:3px">
				<legend>Enter search words</legend>
				<table cellpadding="0" cellspacing="0" border="1" rules="all" width="960" class="rpt_table">
					<thead>
						<th>Within Group</th>
						<th>Sales Order No</th>
						<th>Booking No</th>
						<th>Style Ref. No</th>
						<th>Delivery Date Range</th>
						<th>
							<input type="reset" name="reset" id="reset" value="Reset" style="width:70px" class="formbutton" />
							<input type="hidden" name="hidden_booking_data" id="hidden_booking_data" value="">
							<input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes" value="<? echo $cbo_company_id; ?>">
							<input type="hidden" name="txt_selected_id" id="txt_selected_id" value="" />
							<input type="hidden" name="txt_selected"  id="txt_selected" width="650px" value="" />
							<input type="hidden" name="txt_selected_fso"  id="txt_selected_fso" width="650px" value="" />
							<input type="hidden" name="txt_currency_id"  id="txt_currency_id" width="650px" value="" />
						</th>
					</thead>
					<tr class="general">
						<td><? echo create_drop_down("cbo_within_group", 150, $yes_no, "", 1, "--Select--", $cbo_within_group, '', 1); ?></td>
						<td><input type="text" style="width:130px" class="text_boxes"  name="txt_sale_order_no" id="txt_sale_order_no" /></td>
						<td id="search_by_td"><input type="text" style="width:130px" class="text_boxes"  name="txt_booking_no" id="txt_booking_no" /></td>
						<td><input type="text" style="width:130px" class="text_boxes"  name="txt_style_no" id="txt_style_no" /></td>
						<td>
							<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>To
							<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
						</td>
						<td>
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_company_id').value+'_'+document.getElementById('txt_sale_order_no').value+'_'+document.getElementById('txt_booking_no').value+'_'+document.getElementById('txt_style_no').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+<? echo $cbo_party_id ;?>+'_'+<? echo $cbo_bill_for ;?>, 'create_fso_search_list_view', 'search_div', 'finish_fabric_bill_entry_roll_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:70px;" />
						</td>
					</tr>
					<tr>
						<td colspan="7" align="center" valign="middle"><? echo load_month_buttons(1); ?></td>
					</tr>
				</table>
				<div style="margin-top:10px;" id="search_div" align="center"></div>
			</fieldset>
		</form>
	</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="create_fso_search_list_view")
{
	$data 				= explode("_",$data);
	$company_id 		= $data[0];
	$fso_no		 		= trim($data[1]);
	$txt_booking_no		= trim($data[2]);
	$txt_style_no		= trim($data[3]);
	$within_group 		= trim($data[4]);
	$date_from 			= trim($data[5]);
	$date_to 			= trim($data[6]);
	$cbo_selection_year = trim($data[7]);
	$cbo_party_id		= trim($data[8]);
	$cbo_bill_for		= trim($data[9]);

	$company_arr 	= return_library_array("select id,company_name from lib_company", 'id', 'company_name');
	$supplier_arr 	= return_library_array("select id,supplier_name from lib_supplier","id","supplier_name");
	$buyer_arr 		= return_library_array("select id,short_name from lib_buyer", 'id', 'short_name');
	$location_arr 	= return_library_array("select id,location_name from lib_location", 'id', 'location_name');

	$search_field_cond  = "";
	$search_field_cond .= ($txt_booking_no != "")?" and e.sales_booking_no like '%" . $txt_booking_no . "'":"";
	$search_field_cond .= ($fso_no!= "")?" and e.job_no_prefix_num=$fso_no":"";
	$search_field_cond .= ($txt_style_no != "")?" and e.style_ref_no like '%" . $txt_style_no . "%'":"";

	$date_cond = '';
	if ($date_from != "" && $date_to != "") {

		$year_condition = "";
		if ($db_type == 0) {
			$date_cond = "and a.issue_date between '" . change_date_format(trim($date_from), "yyyy-mm-dd", "-") . "' and '" . change_date_format(trim($date_to), "yyyy-mm-dd", "-") . "'";
		} else {
			$date_cond = "and a.issue_date between '" . change_date_format(trim($date_from), '', '', 1) . "' and '" . change_date_format(trim($date_to), '', '', 1) . "'";
		}
	}else{
		$date_cond = "";
		if($db_type==0)
		{
			if($cbo_selection_year>0) $year_condition=" and YEAR(a.issue_date)=$cbo_selection_year";
		}
		else
		{
			if($cbo_selection_year>0) $year_condition=" and to_char(a.issue_date,'YYYY')=$cbo_selection_year";
		}
	}

	if($within_group>0) $within_group_cond = "and e.within_group = $within_group "; else $within_group_cond = "";

	if($cbo_party_id>0)
	{
		if($within_group==1) $partyCondition = "and e.po_company_id = $cbo_party_id "; else $partyCondition = "and e.buyer_id = $cbo_party_id ";
	}
	
	$str_data="";
	$sql_challan=sql_select("SELECT b.id, b.delivery_id, b.delivery_dtls_id from subcon_inbound_bill_mst a,subcon_inbound_bill_dtls b where a.id=b.mst_id and a.process_id=16 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ");
	foreach ($sql_challan as $row)
	{
		if($str_data=="") $str_data=$row[csf('delivery_id')]; else $str_data.='!!!!'.$row[csf('delivery_id')];
	}
	unset($sql_challan);
	$ex_str_data=explode("!!!!",$str_data);
	$str_arr=array();
	foreach($ex_str_data as $str)
	{
		$str_arr[]=$str;
	}

	if ($cbo_bill_for==16) // Knit Finish Fabric
	{
		$sql = "SELECT a.id, a.issue_number, e.within_group, e.sales_booking_no, e.style_ref_no, e.job_no_prefix_num, e.job_no, e.booking_date, e.id as salse_order, e.po_job_no, e.po_company_id, e.po_buyer, e.buyer_id, e.insert_date, count(b.id) as dtls_id, e.currency_id
		from inv_issue_master a, inv_finish_fabric_issue_dtls b, order_wise_pro_details c, fabric_sales_order_mst e
		where a.company_id=$company_id and a.entry_form=318 and a.id=b.mst_id  and b.trans_id=c.trans_id and c.po_breakdown_id=e.id $search_field_cond $within_group_cond $year_condition $date_cond $partyCondition  and a.status_active=1  and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and e.status_active=1 and b.issue_qnty>0  
		group by a.id, a.issue_number, e.within_group, e.sales_booking_no, e.style_ref_no, e.job_no_prefix_num, e.job_no, e.booking_date, e.id, e.po_job_no, e.po_company_id, e.po_buyer, e.buyer_id, e.insert_date, e.currency_id order by a.id";
	}
	else // Knitting
	{
		$sql = "SELECT a.id, a.issue_number, e.within_group, e.sales_booking_no, e.style_ref_no, e.job_no_prefix_num, e.job_no, e.booking_date, e.id as salse_order, e.po_job_no, e.po_company_id, e.po_buyer, e.buyer_id, e.insert_date, count(b.id) as dtls_id, e.currency_id
		from inv_issue_master a, inv_grey_fabric_issue_dtls b, order_wise_pro_details c, fabric_sales_order_mst e
		where a.id=b.mst_id  and b.trans_id=c.trans_id and c.po_breakdown_id=e.id and a.company_id=$company_id and a.entry_form=61 and a.issue_basis=1 and a.issue_purpose=0 $search_field_cond $within_group_cond $year_condition $date_cond $partyCondition  and a.status_active=1  and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and e.status_active=1 and b.issue_qnty>0 and e.sales_order_type=2 and e.entry_form=109
		group by a.id, a.issue_number, e.within_group, e.sales_booking_no, e.style_ref_no, e.job_no_prefix_num, e.job_no, e.booking_date, e.id, e.po_job_no, e.po_company_id, e.po_buyer, e.buyer_id, e.insert_date, e.currency_id order by a.id";
	}
	
	// echo $sql;die;
	$result = sql_select($sql);

	?>
	<style type="text/css">
		.rpt_table tr{ text-decoration:none; cursor:pointer; }
		.rpt_table tr td{ text-align: center; }
	</style>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="890" class="rpt_table">
		<thead>
			<th width="40">SL</th>
			<th width="90">Challan No</th>
			<th width="90">Sales Order No</th>
			<th width="60">Year</th>
			<th width="80">Within Group</th>
			<th width="70">Buyer</th>
			<th width="120">Booking No</th>
			<th width="80">Booking date</th>
			<th width="110">Style Ref.</th>
			<th>Currency</th>
		</thead>
	</table>
	<div style="width:890px; max-height:260px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="870" class="rpt_table"
		id="tbl_list_search">
			<?
			$i = 1;
			if(!empty($result))
			{
				foreach ($result as $row)
				{
					$all_value=$row[csf('id')];
					if(!in_array($all_value,$str_arr))
					{
						if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
		
						if($row[csf('within_group')]==1) $buyer = $buyer_arr[$row[csf('po_buyer')]]; else $buyer = $buyer_arr[$row[csf('buyer_id')]];
						$data = $row[csf('salse_order')].'**'. $row[csf('job_no')];
						$id_arr[]=$row[csf('id')];
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value('<? echo $row[csf('id')]; ?>');" id="search<? echo $row[csf('id')];?>">
							<td width="40"><? echo $i; ?>
							<input type="hidden" name="txt_individual" id="txt_individual<? echo $row[csf('id')];?>" value="<?php echo $row[csf('job_no')]; ?>"/>
							<input type="hidden" name="txt_individual_id" id="txt_individual_id<? echo $row[csf('id')];?>" value="<?php echo $row[csf('id')]; ?>"/>
							<input type="hidden" name="txt_individual_fso_id" id="txt_individual_fso_id<? echo $row[csf('id')];?>" value="<?php echo $row[csf('salse_order')]; ?>"/>
							<input type="hidden" name="txt_individual_currency_id" id="txt_individual_currency_id<? echo $row[csf('id')];?>" value="<?php echo $row[csf('currency_id')]; ?>"/>
						</td>
						<td width="90"><? echo $row[csf('issue_number')]; ?></td>
						<td width="90"><? echo $row[csf('job_no_prefix_num')]; ?></td>
						<td width="60"><p><? echo date("Y",strtotime($row[csf('insert_date')])); ?></p></td>
						<td width="80"><p><? echo $yes_no[$row[csf('within_group')]]; ?></p></td>
						<td width="70"><p><? echo $buyer; ?></p></td>
						<td width="120"><p><? echo $row[csf('sales_booking_no')]; ?></p></td>
						<td width="80"><? echo change_date_format($row[csf('booking_date')]); ?></td>
						<td width="110"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
						<td><p><? echo $currency[$row[csf('currency_id')]]; ?></p></td>
						</tr>
						<?
		                $i++;
					}
				}
			}
			else
			{
				?>
				<tr bgcolor="<? echo $bgcolor; ?>">
					<th colspan="9">No data found</th>
				</tr>
				<?
			}
			?>
		</table>
	</div>
	<div style="width:625px;" align="left">
		<table width="100%">
			<tr>
				<td align="center" colspan="6" height="30" valign="bottom">
					<div style="width:100%">
						<div style="width:50%; float:left" align="left">
							<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data('<? echo implode(',',$id_arr);?>')" /> Check / Uncheck All
						</div>
						<div style="width:50%; float:left" align="left">
							<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
						</div>
					</div>
				</td>
			</tr>
		</table>
	</div>

	<?
	exit();
}

if($action=='get_challan_list_view')
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$update_id 		= str_replace("'","",$update_id);
	$company_name 	= str_replace("'","",$cbo_company_id);
	$location_id 	= str_replace("'","",$cbo_location);
	$within_group 	= str_replace("'","",$cbo_within_group);
	$cbo_party_id 	= str_replace("'","",$cbo_party_id);
	$txt_fso_no 	= str_replace("'","",$txt_fso_no);
	$hdn_fso_id 	= str_replace("'","",$hdn_fso_id);
	$hdn_challan_id = str_replace("'","",$hdn_challan_id);
	$deliv_challan 	= str_replace("'","",$delivery_challan);
	$exchange_rate 	= str_replace("'","",$exchange_rate);
	$fso_currency 	= str_replace("'","",$fso_currency);
	$bill_currency 	= str_replace("'","",$bill_currency);
	$bill_for 	= str_replace("'","",$cbo_bill_for);

	$delivery_challan_no 	= trim(str_replace("'","",$delivery_challan_no));
	if($delivery_challan_no != ""){
		$deli_challan_sql = sql_select("select id from inv_issue_master where issue_number_prefix_num=$delivery_challan_no");
		foreach ($deli_challan_sql as $row) {
			$delivery_ids[$row[csf("id")]] = $row[csf("id")];
		}
	}

	$delivery_cond = (!empty($delivery_ids))?" and a.id in(".implode(",",$delivery_ids).")":"";
	if($within_group==1){
		$party_cond = ($cbo_party_id!="")?" and e.po_company_id = $cbo_party_id ":"";
	}else{
		$party_cond = ($cbo_party_id!="")?" and e.buyer_id = $cbo_party_id ":"";
	}

	$booking_type_arr = array("118"=>"Main","108"=>"Partial","88"=>"Short","89"=>"Sample With Order","90"=>"Sample Without Order");
	$buyer_arr 		= return_library_array( "select id, short_name from lib_buyer where status_active=1 and is_deleted=0",'id','short_name');
	$company_arr 	= return_library_array("select id,company_short_name from lib_company", 'id', 'company_short_name');
	
	$fsoids= "'".implode("','",explode(",", $hdn_fso_id))."'";
	if($hdn_fso_id!="")
	{
		$fso_id_cond= "and b.order_id in($fsoids)";
	}else {
		$fso_id_cond = "";
	}

	if($hdn_fso_id!="")
	{
		$fso_id_cond2= "and a.fso_id in($fsoids)";
	}else {
		$fso_id_cond2 = "";
	}

	if($txt_fso_no!="" && $hdn_challan_id=="")
	{
		$fso_no_cond= "and e.job_no in('$txt_fso_no')";
	}else{
		$fso_no_cond="";
	}
	if($deliv_challan!="") { $deliv_challan_cond= "and a.issue_number in('$deliv_challan')"; }
	// === All ready Received ommit here ==== start //
	$currenitDeliverCond = "";
	$currenitDeliverCond2 = "";
	$update_id_cond = "";
	if($update_id!="")
	{
		$currenitDeliverCond = "and mst_id not in ($update_id)";
		$currenitDeliverCond2 = "and y.mst_id not in ($update_id)";
		$update_id_cond = " and a.id=$update_id";
	}
	else
	{
		$currenitDeliverCond = "";
		$currenitDeliverCond2 = "";
		$update_id_cond = "";
	}

	$con = connect();
    $r_id=execute_query("delete from tmp_barcode_no where userid=$user_id");
    oci_commit($con);

	if($update_id!="")
	{
		$sql= "SELECT y.delivery_dtls_id 
		FROM subcon_inbound_bill_mst x INNER JOIN subcon_inbound_bill_dtls y ON x.id = y.mst_id 
		WHERE x.status_active = 1 AND x.is_deleted = 0 AND x.company_id = ".$company_name." AND x.location_id = ".$location_id." AND x.party_id = ".$cbo_party_id." AND y.status_active = 1 AND y.is_deleted = 0 and y.mst_id in ($update_id) and x.entry_form=486 and x.bill_for=$bill_for group by delivery_dtls_id";

		$sql_receieved  = sql_select($sql);
		foreach ($sql_receieved as $row) {
			$delivery_dtls_id .= $row[csf('delivery_dtls_id')].",";
		}

		$all_receieved_id = chop($delivery_dtls_id,",");
		if($all_receieved_id!="")
		{
			$all_receieved_idsArr=array_unique(explode(",",$all_receieved_id));
			if($db_type==2 && count($all_receieved_idsArr)>999)
			{
				$received_cond=" and (";
				$all_receieved_idsArr=array_chunk($all_receieved_idsArr,999);
				foreach($all_receieved_idsArr as $receieved_id)
				{
					$receieved_ids=implode(",",$receieved_id);
					$received_cond.="b.id in($receieved_ids) or ";
				}

				$received_cond=chop($received_cond,'or ');
				$received_cond.=")";
			}
			else
			{
				$received_cond=" and b.id in (".implode(",",$all_receieved_idsArr).")";
			}
		}

		if ($bill_for==16) // Knit Finish Fabric
		{
			$sql_2 = "union all
			SELECT a.id delivery_id, a.issue_number challan_no, a.issue_date delevery_date, b.id dtls_id, b.prod_id product_id, b.batch_id, b.order_id, b.body_part_id bodypart_id, a.location_id, d.unit_of_measure as uom, b.fabric_shade, sum(b.issue_qnty) delivery_qty, b.no_of_roll roll_no, b.width_type, b.trans_id, c.batch_no, c.extention_no, c.color_id, d.detarmination_id determination_id, d.gsm, d.dia_width dia, e.job_no as fso_no, e.sales_booking_no as booking_no, e.company_id, e.po_company_id, e.po_buyer, e.buyer_id, e.style_ref_no, e.season, e.within_group, e.booking_entry_form, f.order_rate, f.order_amount, g.barcode_no
			from inv_issue_master a, inv_transaction f, inv_finish_fabric_issue_dtls b, pro_batch_create_mst c, product_details_master d, fabric_sales_order_mst e, pro_roll_details g
			where a.company_id=$company_name and a.entry_form=318 and a.id=f.mst_id and f.id=b.trans_id and b.batch_id=c.id and b.prod_id=d.id and b.order_id=TO_CHAR(e.id) and a.id=g.mst_id and b.id=g.dtls_id and e.id=g.po_breakdown_id and g.entry_form=318 and a.status_active='1' and a.is_deleted='0' $challan_id_cond $fso_id_cond $delivery_cond $fso_no_cond $party_cond $deliv_challan_cond and b.status_active=1 and c.status_active=1 and d.status_active=1 and e.status_active=1 and f.status_active=1 and f.is_deleted=0 and b.is_bill_processing_entry=1 $received_cond
			group by a.id, a.issue_number, a.issue_date, b.id, b.prod_id, b.batch_id, b.order_id, b.body_part_id, a.location_id, d.unit_of_measure, b.fabric_shade, b.no_of_roll, b.width_type, b.order_id, b.trans_id, c.batch_no, c.extention_no, c.color_id, d.detarmination_id, d.gsm, d.dia_width, e.job_no, e.sales_booking_no, e.company_id, e.po_company_id, e.po_buyer, e.buyer_id, e.style_ref_no, e.season, e.within_group, e.booking_entry_form, f.order_rate, f.order_amount, g.barcode_no";
			$order_by =" order by delivery_id, order_id";
		}
		else
		{
			$sql_2 = "union all
			SELECT a.id delivery_id, a.issue_number challan_no, a.issue_date delevery_date, a.fso_id as order_id, b.id dtls_id, b.prod_id product_id, 0 as batch_id, b.body_part_id bodypart_id, a.location_id, d.unit_of_measure as uom, b.issue_qnty delivery_qty, 1 as roll_no, b.trans_id, b.color_id, d.detarmination_id determination_id, d.gsm, d.dia_width dia, e.job_no as fso_no, e.sales_booking_no as booking_no, e.company_id, e.po_company_id, e.po_buyer, e.buyer_id, e.style_ref_no, e.season, e.within_group, e.booking_entry_form, f.order_rate, f.order_amount, g.barcode_no 
			from inv_issue_master a, inv_transaction f, inv_grey_fabric_issue_dtls b, product_details_master d, fabric_sales_order_mst e, pro_roll_details g 
			where a.id=f.mst_id and f.id=b.trans_id and b.prod_id=d.id and a.fso_id=e.id and a.id=g.mst_id and b.id=g.dtls_id and e.id=g.po_breakdown_id and a.company_id=$company_name and a.entry_form=61 and g.entry_form=61 and a.status_active=1 and a.is_deleted=0 $challan_id_cond $fso_id_cond2 $delivery_cond $fso_no_cond $party_cond $deliv_challan_cond and b.status_active=1 and d.status_active=1 and e.status_active=1 and f.status_active=1 and f.is_deleted=0 and e.sales_order_type=2 and g.is_service=1 and b.is_bill_processing_entry=1 $received_cond";
			$order_by2 =" order by delivery_id, order_id";
		}
	}
	else
	{
		$order_by =" order by a.id, b.order_id";
		$order_by2 =" order by a.id, a.fso_id";
	}

	//echo $received_cond;die;
	if($db_type ==0){
		$is_bill_processing_entry_null_chk = " or b.is_bill_processing_entry=''";
	}else{
		$is_bill_processing_entry_null_chk = " or b.is_bill_processing_entry is null";
	}

	if ($bill_for==16) // Knit Finish Fabric
	{
		$mainQuery = "SELECT a.id delivery_id, a.issue_number challan_no, a.issue_date delevery_date, b.id dtls_id, b.prod_id product_id, b.batch_id, b.order_id, b.body_part_id bodypart_id, a.location_id, d.unit_of_measure as uom, b.fabric_shade, sum(b.issue_qnty) delivery_qty, b.no_of_roll roll_no, b.width_type, b.trans_id, c.batch_no, c.extention_no, c.color_id, d.detarmination_id determination_id, d.gsm, d.dia_width dia, e.job_no as fso_no, e.sales_booking_no as booking_no, e.company_id, e.po_company_id, e.po_buyer, e.buyer_id, e.style_ref_no, e.season, e.within_group, e.booking_entry_form, f.order_rate, f.order_amount, g.barcode_no
		from inv_issue_master a, inv_transaction f, inv_finish_fabric_issue_dtls b, pro_batch_create_mst c, product_details_master d, fabric_sales_order_mst e, pro_roll_details g
		where a.company_id=$company_name and a.entry_form=318 and a.id=f.mst_id and f.id=b.trans_id and b.batch_id=c.id and b.prod_id=d.id and b.order_id=TO_CHAR(e.id) and a.id=g.mst_id and b.id=g.dtls_id and e.id=g.po_breakdown_id and g.entry_form=318 and a.status_active='1' and a.is_deleted='0' $challan_id_cond $fso_id_cond $delivery_cond $fso_no_cond $party_cond $deliv_challan_cond and b.status_active=1 and c.status_active=1 and d.status_active=1 and e.status_active=1 and f.status_active=1 and f.is_deleted=0 and (b.is_bill_processing_entry=0 $is_bill_processing_entry_null_chk)
		group by a.id, a.issue_number, a.issue_date, b.id, b.prod_id, b.batch_id, b.order_id, b.body_part_id, a.location_id, d.unit_of_measure, b.fabric_shade, b.no_of_roll, b.width_type, b.order_id, b.trans_id, c.batch_no, c.extention_no, c.color_id, d.detarmination_id, d.gsm, d.dia_width, e.job_no, e.sales_booking_no, e.company_id, e.po_company_id, e.po_buyer, e.buyer_id, e.style_ref_no, e.season, e.within_group, e.booking_entry_form, f.order_rate, f.order_amount, g.barcode_no 
		$sql_2 $order_by ";
	}
	else
	{
		$mainQuery = "SELECT a.id delivery_id, a.issue_number challan_no, a.issue_date delevery_date, a.fso_id as order_id, b.id dtls_id, b.prod_id product_id, 0 as batch_id, b.body_part_id bodypart_id, a.location_id, d.unit_of_measure as uom, b.issue_qnty delivery_qty, 1 as roll_no, b.trans_id, b.color_id, d.detarmination_id determination_id, d.gsm, d.dia_width dia, e.job_no as fso_no, e.sales_booking_no as booking_no, e.company_id, e.po_company_id, e.po_buyer, e.buyer_id, e.style_ref_no, e.season, e.within_group, e.booking_entry_form, f.order_rate, f.order_amount, g.barcode_no 
		from inv_issue_master a, inv_transaction f, inv_grey_fabric_issue_dtls b, product_details_master d, fabric_sales_order_mst e, pro_roll_details g
		where a.id=f.mst_id and f.id=b.trans_id and b.prod_id=d.id and a.fso_id=e.id and a.id=g.mst_id and b.id=g.dtls_id and e.id=g.po_breakdown_id and a.company_id=$company_name and a.issue_basis=1 and a.entry_form=61 and g.entry_form=61 and a.status_active=1 and a.is_deleted=0 $challan_id_cond $fso_id_cond2 $delivery_cond $fso_no_cond $party_cond $deliv_challan_cond and b.is_bill_processing_entry=0 and b.status_active=1 and d.status_active=1 and e.status_active=1 and f.status_active=1 and f.is_deleted=0 and e.sales_order_type=2 and g.is_service=1
		$sql_2 $order_by2 ";
	}

	// echo $mainQuery;die;
	$mainQueryResult = sql_select($mainQuery);

	if(empty($mainQueryResult))
	{
		echo "<span style='color:red; font-weight:bold; font-size:14px;'><center>No Data Found</center></span>";
		exit();
	}

	$maniDataArr = array();$barcode_no_check=array();
	foreach ($mainQueryResult as  $row)
	{
		$batch_id_arr[] = $row[csf("batch_id")];
		$color_id_arr[] = $row[csf("color_id")];
		$salesOrderIds .= $row[csf('order_id')].",";
		$challan_ids .= "'".$row[csf('delivery_id')]."',";
		
		$barcodeArr[$row[csf("barcode_no")]] = $row[csf("barcode_no")];
		if( $barcode_no_check[$row[csf('barcode_no')]] =="" )
        {
            $barcode_no_check[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
            $barcodeno = $row[csf('barcode_no')];
            // echo "insert into tmp_barcode_no (userid, barcode_no) values ($user_id,$barcodeno)";
            $r_id=execute_query("insert into tmp_barcode_no (userid, barcode_no) values ($user_id,$barcodeno)");
        }
	}
	oci_commit($con);

	$barcodeArr = array_filter($barcodeArr);
    if(count($barcodeArr ) >0 ) // production
    {
    	$prodBarcodeData=array();
        $production_sql = sql_select("SELECT b.original_gsm, b.original_width, c.barcode_no
        from pro_grey_prod_entry_dtls b, pro_roll_details c, tmp_barcode_no d
        where b.id=c.dtls_id and c.barcode_no=d.barcode_no and d.userid=$user_id and c.entry_form in(2) and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0");
        $yarn_prod_id_check=array();$prog_no_check=array();
        foreach ($production_sql as $row)
        {
            $prodBarcodeData[$row[csf("barcode_no")]]["gsm"] = $row[csf("original_gsm")];
            $prodBarcodeData[$row[csf("barcode_no")]]["dia"] = $row[csf("original_width")];
        }
    }

	execute_query("delete from tmp_barcode_no where userid=$user_id");
    oci_commit($con);
	disconnect($con);

	$salesOrderIds = implode(",", array_filter(array_unique(explode(",",chop($salesOrderIds,",")))));
	$fso_rate_sql=sql_select("SELECT a.id, a.job_no, b.body_part_id, b.determination_id, b.color_id, b.gsm_weight, b.dia, b.avg_rate 
	FROM FABRIC_SALES_ORDER_MST a, FABRIC_SALES_ORDER_DTLS b WHERE A.ID=B.mst_id AND A.ID IN($salesOrderIds)");
	$fso_rate_arr=array();
	foreach ($fso_rate_sql as $row)
	{
		// $fso_rate_arr[$row[csf('id')]][$row[csf('body_part_id')]][$row[csf('determination_id')]][$row[csf('color_id')]]['fso_rate'] = $row[csf('avg_rate')];
		$fso_rate_arr[$row[csf('id')]][$row[csf('body_part_id')]][$row[csf('determination_id')]][$row[csf('color_id')]][$row[csf('gsm_weight')]][$row[csf('dia')]]['fso_rate'] = $row[csf('avg_rate')];

		// echo $row[csf('id')].'='.$row[csf('body_part_id')].'='.$row[csf('determination_id')].'='.$row[csf('color_id')].'='.$row[csf('gsm_weight')].'='.$row[csf('dia')].'==<br>';
	}


	$all_challan_id = chop($challan_ids,",");
	if($all_challan_id!="")
	{
		$all_challan_idsArr=array_unique(explode(",",$all_challan_id));
		if($db_type==2 && count($all_challan_idsArr)>999)
		{
			$challan_cond=" and (";
			$all_challan_idsArr=array_chunk($all_challan_idsArr,999);
			foreach($all_challan_idsArr as $challan_id)
			{
				$challanids=implode(",",$challan_id);
				$challan_cond.="b.delivery_id in($challanids) or ";
			}

			$challan_cond=chop($challan_cond,'or ');
			$challan_cond.=")";
		}
		else
		{
			$challan_cond=" and b.delivery_id in (".implode(",",$all_challan_idsArr).")";
		}
	}

	$sql_challan=sql_select("SELECT b.id, b.delivery_id, b.delivery_dtls_id, b.delivery_qty, b.remarks as detailsremarks, b.rate, b.amount
	from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b 
	where a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and a.entry_form=486 and a.bill_for=$bill_for $update_id_cond $challan_cond");
	foreach ($sql_challan as $row)
	{
		$already_billed[$row[csf('delivery_id')]][$row[csf('delivery_dtls_id')]]['bill_dtls_id'] 	= $row[csf('id')];
		$already_billed[$row[csf('delivery_id')]][$row[csf('delivery_dtls_id')]]['challan'] 		= $row[csf('delivery_dtls_id')];
		$already_billed[$row[csf('delivery_id')]][$row[csf('delivery_dtls_id')]]['detailsremarks'] 	= $row[csf('detailsremarks')];
		$already_billed[$row[csf('delivery_id')]][$row[csf('delivery_dtls_id')]]['delivery_qty'] 	+= $row[csf('delivery_qty')];
		$already_billed[$row[csf('delivery_id')]][$row[csf('delivery_dtls_id')]]['bill_rate'] 	+= $row[csf('rate')];
		$already_billed[$row[csf('delivery_id')]][$row[csf('delivery_dtls_id')]]['bill_amount'] 	+= $row[csf('amount')];
	}

	$composition_arr=array();
	$sql_deter="SELECT a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$data_array=sql_select($sql_deter);
	if(count($data_array)>0)
	{
		foreach( $data_array as $row )
		{
			if(array_key_exists($row[csf('id')],$composition_arr))
			{
				$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
			}
			else
			{
				$composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
			}
		}
	}

	$color_arr=array();
	if(!empty($color_id_arr)){
		$color_arr=return_library_array( "select id, color_name from lib_color where id in(".implode(",",$color_id_arr).") and status_active=1 and is_deleted=0",'id','color_name');
	}
	?>
	<style>
		.text-bold { font-weight:bold; }
	</style>
	<br />
	<table width="1420" border="1" rules="all" class="rpt_table" cellpadding="0" cellspacing="0" >
		<thead>
			<th width="30" valign="middle"><input id="all_check" onClick="check_all('all_check')" type="checkbox"> </th>
			<th width="30">SL</th>
			<th width="110">Challan No</th>
			<th width="100">Barcode No</th>
			<th width="80">Delivery Date</th>
			<th width="110">Booking No</th>
			<th width="120">FSO No</th>
			<th width="100">Body Part</th>
			<th width="230">Fabric Description</th>
			<th width="130">Fab Color</th>
			<th width="40">UOM</th>
			<th width="80">Delivery Qty</th>
			<th width="50">Rate</th>
			<th width="80">Amount</th>
			<th>Remarks</th>
		</thead>
	</table>

	<div style="width:1420px; overflow-y:scroll; max-height:350px;" id="scroll_body">
		<table width="1400" border="1" rules="all" class="rpt_table" cellpadding="0" cellspacing="0" id="tbl_list_search">
			<tbody>
				<?php
				$i=1;
				$total_delivery_qnty = $total_order_amount = $total_roll_no = 0;
				foreach ($mainQueryResult as  $row)
				{
					$delivery_qty=$already_billed[$row[csf('delivery_id')]][$row[csf('dtls_id')]]['delivery_qty'];
					if($update_id!="") $delivery_qty=0;
					$avilable_qty=$row[csf('delivery_qty')]-$delivery_qty;
					if($avilable_qty>0)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
	
						if($row[csf('within_group')]==1)
						{
							$partyName = $row[csf('po_company_id')];
							$buyerName = $buyer_arr[$row[csf('po_buyer')]];
	
						}else{
							$partyName = $row[csf('company_id')];
							$buyerName = $buyer_arr[$row[csf('buyer_id')]];
						}
	
						if( $already_billed[$row[csf('delivery_id')]][$row[csf('dtls_id')]]['challan']!="" &&  ($already_billed[$row[csf('delivery_id')]][$row[csf('dtls_id')]]['challan']) ==  $row[csf('dtls_id')] && $update_id!="")
						{
							$checkedRow = "checked='checked'";
						}else {
							$checkedRow = "";
						}

						$delivery_qty = number_format($row[csf('delivery_qty')],2,".","");
						$bill_dtls_id = $already_billed[$row[csf('delivery_id')]][$row[csf('dtls_id')]]['bill_dtls_id'];
						$bill_rate=0;$bill_amount=0;
						if ($bill_dtls_id=="") 
						{
							$grey_gsm=$prodBarcodeData[$row[csf("barcode_no")]]["gsm"];
							$grey_dia=$prodBarcodeData[$row[csf("barcode_no")]]["dia"];

							// echo $row[csf('order_id')].'='.$row[csf('bodypart_id')].'='.$row[csf('determination_id')].'='.$row[csf('color_id')].'='.$grey_gsm.'='.$grey_dia.'<br>';

							$bill_rate = $fso_rate_arr[$row[csf('order_id')]][$row[csf('bodypart_id')]][$row[csf('determination_id')]][$row[csf('color_id')]][$grey_gsm][$grey_dia]['fso_rate'];
							$fso_rate = $fso_rate_arr[$row[csf('order_id')]][$row[csf('bodypart_id')]][$row[csf('determination_id')]][$row[csf('color_id')]][$grey_gsm][$grey_dia]['fso_rate'];

							if($fso_currency==2 && $bill_currency==1)
							{
								$bill_rate=$bill_rate*$exchange_rate;
								$bill_rate=number_format($bill_rate,2,'.','');
							}
							else if($fso_currency==1 && $bill_currency==1)
							{
								$bill_rate=$bill_rate*1;
							}
							else if($fso_currency==2 && $bill_currency==2)
							{
								$bill_rate=$bill_rate*1;
							}
							else if($fso_currency==1 && $bill_currency==2)
							{
								$bill_rate=$bill_rate/$exchange_rate;
								$bill_rate=number_format($bill_rate,2,'.','');
							}
							$bill_amount = $bill_rate*$delivery_qty;
						}
						else
						{
							$bill_rate = $already_billed[$row[csf('delivery_id')]][$row[csf('dtls_id')]]['bill_rate'];
							$bill_amount = $already_billed[$row[csf('delivery_id')]][$row[csf('dtls_id')]]['bill_amount'];
						}
	
						

						
						$order_rate   = number_format($bill_rate,2,".","");
						$order_amount = number_format($bill_amount,2,".","");

						?>
						<tr bgcolor="<? echo $bgcolor; ?>"  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<?echo $i;?>">
							<td width="30" align="center" valign="middle">
	
								<input id="check<? echo $i ?>_<? echo $row[csf('challan_no')];?>" name="chkSelect[]" onClick="fnc_challan(<? echo $i;?>);" class="chkSelect" data-seq="<? echo $i ?>" type="checkbox"  <? echo $checkedRow;?>
								value="<? echo $row[csf('order_id')]."**".$row[csf('within_group')]."**".$row[csf('booking_no')]."**".$row[csf('delivery_id')]."**".$row[csf('dtls_id')]."**".$row[csf('product_id')]."**".$row[csf('batch_id')]."**".$row[csf('color_id')]."**".$row[csf('uom')]."**".$row[csf('determination_id')]."**".$row[csf('gsm')]."**".$row[csf('dia')]."**".$row[csf('bodypart_id')]."**".$row[csf('width_type')]."**".$delivery_qty."**".$row[csf('roll_no')]."**".$row[csf('fabric_shade')]; ?>" >
	
								<input type="hidden" id="hdn_deli_qnty" value="<? echo $delivery_qty;?>" />
								<!-- <input type="hidden" id="hdn_amount" value="<? //echo $order_amount;?>" /> -->
								<input type="hidden" id="hdn_roll_no" value="<? echo $row[csf('roll_no')];?>" />
							</td>
							<td width="30" align="center" title="<? echo $row[csf('delivery_id')].'='.$row[csf('dtls_id')]; ?>"><? echo $i;?>
                            	<input type="hidden" id="hdn_update_dtls_id_<? echo $i;?>" value="<? echo $bill_dtls_id;?>" />
								<input type="hidden" id="hdn_dtls_id_<? echo $i;?>" value="<? echo $row[csf('dtls_id')];?>" />
								<input type="hidden" id="hdn_delivery_id_<? echo $i;?>" value="<? echo $row[csf('delivery_id')];?>" />
								<input type="hidden" id="hdn_batch_id_<? echo $i;?>" value="<? echo $row[csf('batch_id')];?>" />
                            </td>
							<td width="110" align="center" id="tdchallan_<? echo $i;?>" style="word-break:break-all"><? echo $row[csf('challan_no')];?></td>
							<td width="100" align="center" id="tdbarcode_<? echo $i;?>" style="word-break:break-all"><? echo $row[csf('barcode_no')];?></td>
							<td width="80" align="center">
								<p><? echo change_date_format($row[csf('delevery_date')]);?></p>
								<input type="hidden" id="hdn_delivery_date_<? echo $i;?>" value="<? echo $row[csf('delevery_date')];?>" />
							</td>
							<td width="110" align="center"><p><? echo $row[csf('booking_no')];?></p></td>
							<td width="120" align="center">
								<p><? echo $row[csf('fso_no')];?></p>
								<input type="hidden" id="hdn_fso_id_<? echo $i;?>" value="<? echo $row[csf('order_id')];?>" />
							</td>
							<td width="100">
								<p><? echo $body_part[$row[csf('bodypart_id')]];?></p>
								<input type="hidden" id="hdn_body_part_<? echo $i;?>" value="<? echo $row[csf('bodypart_id')];?>" />
							</td>
							<td width="230">
								<p><? echo $composition_arr[$row[csf('determination_id')]];?></p>
								<input type="hidden" id="hdn_deter_id_<? echo $i;?>" value="<? echo $row[csf('determination_id')];?>" />
							</td>
							<td width="130">
								<p><? echo $color_arr[$row[csf('color_id')]];?></p>
								<input type="hidden" id="hdn_color_id_<? echo $i;?>" value="<? echo $row[csf('color_id')];?>" />
							</td>
							<td width="40" align="center">
								<p><?php echo $unit_of_measurement[$row[csf('uom')]];?></p>
								<input type="hidden" id="hdn_uom_id_<? echo $i;?>" value="<? echo $row[csf('uom')];?>" />
							</td>
							<td width="80" align="right">
								<p><? echo $delivery_qty;?></p>
								<input type="hidden" id="hdn_delivery_qnty_<? echo $i;?>" value="<? echo $delivery_qty;?>" />
							</td>

							<td width="50" align="right">
								<input id="txt_rate_<? echo $i; ?>" name="txt_rate[]" type="text" style="width:38px" value="<? echo $order_rate; ?>"   class="text_boxes_numeric" readonly />
								<input type="hidden" id="hidden_fso_rate_<? echo $i;?>" value="<? echo $fso_rate;?>" />
							</td>

							<td width="80" align="right">
								<p><input type="text" id="hdn_amount_<? echo $i; ?>"  name="hdn_amount_<? echo $i; ?>" class="text_boxes_numeric" style="width: 68px;" value="<? echo $order_amount; ?>"  readonly/></p>
								<!-- <input type="hidden" id="hdn_amount_<? //echo $i;?>" value="<? //echo $order_amount;?>" /> -->
							</td>
							<td>
								<input type="text" name="text_dtls_remarks[]" id="text_dtls_remarks_<? echo $i;?>" value="<? echo $already_billed[$row[csf('delivery_id')]][$row[csf('dtls_id')]]['detailsremarks'];?>" style="width: 100%;" placeholder="write" />
							</td>
						</tr>
						<?php
						if( $already_billed[$row[csf('delivery_id')]][$row[csf('dtls_id')]]['challan']!="" &&  ($already_billed[$row[csf('delivery_id')]][$row[csf('dtls_id')]]['challan']) ==  $row[csf('dtls_id')] && $update_id!="")
						{
							$total_delivery_qnty += $delivery_qty;
						}
						$total_order_amount  += $order_amount;
						$i++;
					}
				}
				?>
			</tbody>
		</table>
	</div>
	<table width="1420" border="1" rules="all" class="rpt_table" cellpadding="0" cellspacing="0" id="tbl_list_search_1">
		<tr>
			<td width="30"></td>
			<td width="30"></td>
			<td width="110"></td>
			<td width="100"></td>
			<td width="80"></td>
			<td width="110"></td>
			<td width="120"></td>
			<td width="100"></td>
			<td width="230"></td>
			<td width="130"></td>
			<td width="40" class="text-bold" align="right">Total:</td>
			<td width="80" align="right" class="text-bold" id="delivery_qty_con"><? echo number_format($total_delivery_qnty,2,".","");?></td>
			<td width="50"></td>
			<!-- <td width="80" align="right" class="text-bold" id="amount_con"><? //echo number_format($total_order_amount,2,".","");?></td> -->
			<td width="80" align="right" class="text-bold"><input type="text" id="amount_con" name="amount_con" class="text_boxes_numeric" style="width:68px;" value="<? echo number_format($total_order_amount,2,".","");?>" readonly></td>
			<td></td>
		</tr>
	</table>
	<?
	exit;
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_bill_for=str_replace("'",'',$cbo_bill_for);
	if ($cbo_bill_for==16) 
	{
		$bill_process_id=16;
		$short_name='FBER';
	}
	else
	{
		$bill_process_id=2;
		$short_name='GBER';
	}
	
	$total_hdn_amount = str_replace("'",'',$total_hdn_amount);
	if ($operation==0)   // Insert Here===========================================delivery_id
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		if (is_duplicate_field( "delivery_id", "subcon_inbound_bill_dtls", "mst_id=$update_id" )==1)
		{
			echo "11**0";
			disconnect($con);die;
		}
		if($db_type==0)
		{
			$year_cond=" and YEAR(insert_date)";
		}
		else if($db_type==2)
		{
			$year_cond=" and TO_CHAR(insert_date,'YYYY')";
		}

		$bill_date = date("d-M-Y", strtotime($txt_bill_date));

		$new_bill_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', $short_name, date("Y",time()), 5, "select prefix_no, prefix_no_num from  subcon_inbound_bill_mst where company_id=$cbo_company_id and process_id=$bill_process_id and entry_form=486 $year_cond=".date('Y',time())." order by id desc ", "prefix_no", "prefix_no_num" ));

		if(str_replace("'",'',$update_id)=="")
		{
			$id=return_next_id( "id", "subcon_inbound_bill_mst", 1 ) ;
			$cbo_party_source=1;
			$field_array="id, prefix_no, prefix_no_num, bill_no, company_id, location_id, bill_date, party_id, party_source, bill_for, process_id, exchange_rate, currency, wo_currency_id, entry_form, inserted_by, insert_date";
			$data_array="(".$id.",'".$new_bill_no[1]."','".$new_bill_no[2]."','".$new_bill_no[0]."',".$cbo_company_id.",".$cbo_location.",'".$bill_date."',".$party_id.",".$cbo_party_source.",".$cbo_bill_for.",".$bill_process_id.",".$txt_exchange_rate.",".$cbo_bill_currency.",".$cbo_fso_currency.",486,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

			// echo "10**INSERT INTO subcon_inbound_bill_mst (".$field_array.") VALUES ".$data_array; die;
			$rID=sql_insert("subcon_inbound_bill_mst",$field_array,$data_array,1);
			if($rID) $flag=1; else $flag=0;
			$return_no=$new_bill_no[0];
		}
		else
		{
			$field_array="location_id*bill_date*party_id*bill_for*exchange_rate*currency*wo_currency_id*updated_by*update_date";
			$data_array="".$cbo_location."*'".$bill_date."'*'".$party_id."'*'".$cbo_bill_for."'*'".$txt_exchange_rate."'*'".$cbo_bill_currency."'*'".$cbo_fso_currency."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

			$rID=sql_update("subcon_inbound_bill_mst",$field_array,$data_array,"id",$update_id,0);
			if($rID) $flag=1; else $flag=0;
			$return_no=str_replace("'",'',$txt_bill_no);
		}

		$id1=return_next_id( "id", "subcon_inbound_bill_dtls",1);
		$field_array1 ="id, mst_id, delivery_id, delivery_date, order_id, febric_description_id, body_part_id, uom, delivery_qty, rate, amount, remarks,process_id, inserted_by, insert_date, delivery_dtls_id,is_sales,color_id,batch_id";

		$field_array_up ="delivery_id*delivery_date*order_id*febric_description_id*body_part_id*uom*delivery_qty*rate*amount*remarks*updated_by*update_date*delivery_dtls_id*is_sales*color_id*batch_id";

		$add_comma=0;
		//echo "10**";
		for($i=1; $i<=$tot_row; $i++)
		{
			$update_dtls_id	= "update_dtls_id_".$i;
			$hdn_dtls_id 	= "hdn_dtls_id_".$i;
			$delivery_id 	= "hdn_delivery_id_".$i;
			$delevery_date 	= "hdn_delivery_date_".$i;
			$hdn_fso_id		= "hdn_fso_id_".$i;
			$hdn_batch_id 	= "hdn_batch_id_".$i;
			$hdn_body_part 	= "hdn_body_part_".$i;
			$hdn_deter_id 	= "hdn_deter_id_".$i;
			$hdn_color_id 	= "hdn_color_id_".$i;
			$hdn_uom_id 	= "hdn_uom_id_".$i;
			$delivery_qnty 	= "hdn_delivery_qnty_".$i;
			$txt_rate 		= "txt_rate_".$i;
			$hdn_amount 	= "hdn_amount_".$i;
			$remarks 		= "text_dtls_remarks_".$i;

			if(str_replace("'",'',$$update_dtls_id)=="")
			{
				if(str_replace("'",'',$$hdn_amount) != "")
				{
					if ($add_comma!=0) $data_array1 .=",";
					$data_array1 .="(".$id1.",".$id.",".$$delivery_id.",'".$$delevery_date."',".$$hdn_fso_id.",".$$hdn_deter_id.",".$$hdn_body_part.",".$$hdn_uom_id.",".$$delivery_qnty.",".$$txt_rate.",".$$hdn_amount.",'".$$remarks."',".$bill_process_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$hdn_dtls_id.",1,".$$hdn_color_id.",".$$hdn_batch_id.")";
					$id1=$id1+1;
					$add_comma++;
				}
			}
			else
			{
				$id_arr[]=str_replace("'",'',$$update_dtls_id);
				$data_array_up[str_replace("'",'',$$update_dtls_id)] =explode("*",("".$$delivery_id."*'".$$delevery_date."'*".$$hdn_fso_id."*".$$hdn_deter_id."*".$$hdn_body_part."*".$$hdn_uom_id."*".$$delivery_qnty."*".$$txt_rate."*".$$hdn_amount."*'".$$remarks."'*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'*".$$hdn_dtls_id."*1*".$$hdn_color_id."*".$$hdn_batch_id.""));
			}

			if(str_replace("'", '', $$hdn_dtls_id) !="")
			{
				$delivery_dtls_ids .=str_replace("'",'',$$hdn_dtls_id).",";
			}
		}

		$delivery_dtls_ids = chop($delivery_dtls_ids,",");
		//echo "10**";
		// echo bulk_update_sql_statement("subcon_inbound_bill_dtls", "id",$field_array_up,$data_array_up,$id_arr );die;
		$rID1=execute_query(bulk_update_sql_statement("subcon_inbound_bill_dtls", "id",$field_array_up,$data_array_up,$id_arr ));
		if($rID1) $flag=1; else $flag=0;

		if($data_array1!="")
		{
			// echo "10**insert into subcon_inbound_bill_dtls (".$field_array1.") values ".$data_array1;die;
			$rID1=sql_insert("subcon_inbound_bill_dtls",$field_array1,$data_array1,1);
			if($rID1) $flag=1; else $flag=0;
		}

		if($delivery_dtls_ids !="")
		{
			$field_array_status="is_bill_processing_entry";
			$data_array_status="1";
			if ($cbo_bill_for==16) // Knit Finish Fabric
			{
				$rID2=sql_multirow_update("inv_finish_fabric_issue_dtls",$field_array_status,$data_array_status,"id",$delivery_dtls_ids,0);
			}
			else // Knitting
			{
				$rID2=sql_multirow_update("inv_grey_fabric_issue_dtls",$field_array_status,$data_array_status,"id",$delivery_dtls_ids,0);
			}			
			if($rID2) $flag=1; else $flag=0;
		}		

		// echo "10**" . $rID ."&&".$rID1 ."&&".$rID2;oci_rollback($con);disconnect($con);die;

		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");
				echo "0**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no)."**".'0';
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no)."**".'0';
			}
		}
		if($db_type==2)
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "0**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no)."**".'0';
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no)."**".'0';
			}
		}

		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here=====================================
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$id=str_replace("'",'',$update_id);
		$bill_date = date("d-M-Y", strtotime($txt_bill_date));
		$field_array="location_id*bill_date*party_id*bill_for*exchange_rate*currency*wo_currency_id*updated_by*update_date";
		//echo "10**";
		$data_array="".$cbo_location."*'".$bill_date."'*'".$party_id."'*'".$cbo_bill_for."'*'".$txt_exchange_rate."'*'".$cbo_bill_currency."'*'".$cbo_fso_currency."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

		$sql_dtls="Select id, delivery_dtls_id from subcon_inbound_bill_dtls where mst_id=$update_id and status_active=1 and is_deleted=0";
		$nameArray=sql_select( $sql_dtls );
		foreach($nameArray as $row)
		{
			$dtls_update_id_array[]=$row[csf('id')];
			$pre_delivery_dtls_id_arr[$row[csf('delivery_dtls_id')]]=$row[csf('delivery_dtls_id')];
		}

		$return_no=str_replace("'",'',$txt_system_id);
		$dtls_id=return_next_id( "id", "subcon_inbound_bill_dtls",1);

		$field_array1 ="id, mst_id, delivery_id, delivery_date, order_id, febric_description_id, body_part_id, uom, delivery_qty, rate, amount, remarks,process_id, inserted_by, insert_date, delivery_dtls_id,is_sales,color_id,batch_id";

		$add_comma=0;
		for($i=1; $i<=$tot_row; $i++)
		{
			$update_dtls_id	= "update_dtls_id_".$i;
			$hdn_dtls_id 	= "hdn_dtls_id_".$i;
			$delivery_id 	= "hdn_delivery_id_".$i;
			$delevery_date 	= "hdn_delivery_date_".$i;
			$hdn_fso_id		= "hdn_fso_id_".$i;
			$hdn_batch_id 	= "hdn_batch_id_".$i;
			$hdn_body_part 	= "hdn_body_part_".$i;
			$hdn_deter_id 	= "hdn_deter_id_".$i;
			$hdn_color_id 	= "hdn_color_id_".$i;
			$hdn_uom_id 	= "hdn_uom_id_".$i;
			$delivery_qnty 	= "hdn_delivery_qnty_".$i;
			$txt_rate 		= "txt_rate_".$i;
			$hdn_amount 	= "hdn_amount_".$i;
			$remarks 		= "text_dtls_remarks_".$i;

			$dtls_upcharge = (str_replace("'",'',$$hdn_amount) / $total_hdn_amount) * str_replace("'",'',$txtUpcharge)*1;
			$dtls_discount = (str_replace("'",'',$$hdn_amount) / $total_hdn_amount) * str_replace("'",'',$txtDiscount)*1;
			if(str_replace("'", "", $$hdn_amount) !=""  )
			{
				if ($add_comma!=0) $data_array1 .=",";
				$data_array1 .="(".$dtls_id.",".$id.",".$$delivery_id.",'".$$delevery_date."',".$$hdn_fso_id.",".$$hdn_deter_id.",".$$hdn_body_part.",".$$hdn_uom_id.",".$$delivery_qnty.",".$$txt_rate.",".$$hdn_amount.",'".$$remarks."',".$bill_process_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$hdn_dtls_id.",1,".$$hdn_color_id.",".$$hdn_batch_id.")";
				$dtls_id=$dtls_id+1;
				$add_comma++;
			}

			if(str_replace("'", "", $$hdn_dtls_id) !="")
			{
				$new_deli_dtls[str_replace("'", "", $$hdn_dtls_id)]=str_replace("'", "", $$hdn_dtls_id);
				
				$update_deli_dtls_id[]=str_replace("'", "", $$hdn_dtls_id);
				$data_array_delivery_dtls[str_replace("'", "", $$hdn_dtls_id)]=explode("*",("1"));
				//Current inserted delivery dtls is set to 1 flag (is_bill_processing_entry) in inv_finish_fabric_issue_dtls or inv_grey_fabric_issue_dtls
			}
			//order table insert====================================================================================================
		}

		if(!empty($pre_delivery_dtls_id_arr))
		{
			foreach ($pre_delivery_dtls_id_arr as $dtls_id => $value) 
			{
				if($new_deli_dtls[$dtls_id] =="")
				{
					$update_deli_dtls_id[]=$dtls_id;
					$data_array_delivery_dtls[$dtls_id]=explode("*",("0"));
					//Previous inserted delivery dtls is set to 0 flag (is_bill_processing_entry) in inv_finish_fabric_issue_dtls or inv_grey_fabric_issue_dtls
				}
			}
		}


		$rID=sql_update("subcon_inbound_bill_mst",$field_array,$data_array,"id",$id,0);
		if($rID) $flag=1; else $flag=0;

		$rID_Delete = execute_query("delete FROM subcon_inbound_bill_dtls WHERE mst_id = $id",1);

		if($data_array1!="")
		{
			//echo "insert into subcon_inbound_bill_dtls (".$field_array1.") values ".$data_array1;
			$rID1=sql_insert("subcon_inbound_bill_dtls",$field_array1,$data_array1,0);
			if($rID1) $flag=1; else $flag=0;
		}

		if(!empty($data_array_delivery_dtls))
		{
			$field_array_status="is_bill_processing_entry";

			if ($cbo_bill_for==16) // Knit Finish Fabric
			{
				$rID2=execute_query(bulk_update_sql_statement( "inv_finish_fabric_issue_dtls", "id", $field_array_status, $data_array_delivery_dtls, $update_deli_dtls_id ));
			}
			else // Knitting
			{
				$rID2=execute_query(bulk_update_sql_statement( "inv_grey_fabric_issue_dtls", "id", $field_array_status, $data_array_delivery_dtls, $update_deli_dtls_id ));
			}

			if($rID2) $flag=1; else $flag=0;
		}
		//echo "10**".bulk_update_sql_statement( "inv_finish_fabric_issue_dtls", "id", $field_array_status, $data_array_delivery_dtls, $update_deli_dtls_id );
		//oci_rollback($con);die;

		// echo "10**".$rID.'##'.$rID_Delete.'##'.$rID1.'##'.$rID2;die;
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");
				echo "1**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
		}
		if($db_type==2)
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "1**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no)."**".str_replace("'",'',$posted_account);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)  //Delete here development later =====================
	{
		echo "Delete here development later";die;
		/*$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$id=str_replace("'",'',$update_id);
		$return_no=str_replace("'",'',$txt_bill_no);
		$field_array_delivery="status_active";
		if(str_replace("'",'',$delete_id)!="")
		{
			$delete_id=str_replace("'",'',$delete_id);
			$rID3=execute_query( "delete from subcon_inbound_bill_dtls where delivery_id in ($delete_id)",0);
			$delete_id=explode(",",str_replace("'",'',$delete_id));
			for ($i=0;$i<count($delete_id);$i++)
			{
				$id_delivery[]=$delete_id[$i];
				$data_delivery[str_replace("'",'',$delete_id[$i])] =explode(",",("0"));
			}
		}
		//echo bulk_update_sql_statement( "subcon_inbound_bill_mst", "id",$field_array_delivery,$data_delivery,$id_delivery );
		$rID4=execute_query(bulk_update_sql_statement( "subcon_inbound_bill_mst", "id",$field_array_delivery,$data_delivery,$id_delivery ));

		if($db_type==0)
		{
			if($rID3 && $rID4)
			{
				mysql_query("COMMIT");
				echo "2**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
		}
		if($db_type==2)
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "2**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
		}
		disconnect($con);
		die;*/
	}
}

//====================SYSTEM ID POPUP========
if ($action=="systemId_popup")
{
	echo load_html_head_contents("System ID Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(id,sys_number)
		{
			$('#hidden_sys_id').val(id);
			$("#hidden_sys_no").val(sys_number);
			parent.emailwindow.hide();
		}
	</script>
</head>

<body>
	<div align="center" style="width:800px;">
		<form name="searchsystemidfrm"  id="searchsystemidfrm">
			<fieldset style="width:800px;">
				<legend>Enter search words</legend>
				<table cellpadding="0" cellspacing="0" width="800" border="1" rules="all" class="rpt_table">
					<thead>
						<th>Within Group</th>
						<th>Party</th>
						<th>Search By</th>
						<th id="search_by_td_up">Please Enter System Id</th>
						<th>Bill Date Range</th>
						<th>
							<input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
							<input type="hidden" name="txt_company_id" id="txt_company_id" value="<? echo $cbo_company_id; ?>">
							<input type="hidden" name="hidden_sys_id" id="hidden_sys_id" value="">
							<input type="hidden" name="hidden_sys_no" id="hidden_sys_no" value="">
						</th>
					</thead>
					<tr class="general">
						<td>
							<? echo create_drop_down("cbo_within_group",80,$yes_no,"", 0, "-- Select --", 1,"load_drop_down( 'finish_fabric_bill_entry_roll_controller', this.value+'_'+document.getElementById('txt_company_id').value, 'load_drop_down_party','cbo_party_id');",0,'');
							?>
						</td>
						<td>
							<?
							echo create_drop_down( "cbo_party_id", 100, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 order by comp.company_name","id,company_name", 1, "--Select Party--", 0, "" );
							?>
						</td>
						<td>
							<?
							$search_by_arr=array(1=>"System ID",2=>"FSO NO",3=>"Booking No");
							$dd="change_search_event(this.value, '0*0*0*0', '0*0*0*0', '../../') ";
							echo create_drop_down( "cbo_search_by", 100, $search_by_arr,"",0, "--Select--", "",$dd,0 );
							?>
						</td>
						<td id="search_by_td">
							<input type="text" style="width:110px;" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
						</td>
						<td>
							<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px;" />To
							<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px;" />
						</td>
						<td>
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_party_id').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('cbo_year_selection').value, 'create_finish_search_list_view', 'search_div', 'finish_fabric_bill_entry_roll_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
						</td>
					</tr>
					<tr>
						<td colspan="6" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
					</tr>
				</table>
				<div style="margin-top:10px; margin-left:3px;" id="search_div" align="center"></div>
			</fieldset>
		</form>
	</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_finish_search_list_view")
{
	$data = explode("_",$data);
	$within_group 	= $data[0];
	$party_id 		= $data[1];
	$search_string  = trim($data[2]);
	$search_by 		= $data[3];
	$start_date 	= $data[4];
	$end_date 		= $data[5];
	$company_id 	= $data[6];
	$cbo_year_selection = $data[7];

	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			if(strtotime($start_date) == strtotime($end_date))
			{
				$date_cond = "and DATE_FORMAT(a.bill_date, '%Y-%m-%d') = '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."'";
			}
			else
			{
				$date_cond = "and a.bill_date between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";
			}
		}
		else
		{
			if(strtotime($start_date) == strtotime($end_date))
			{
				$date_cond = "and TO_CHAR(a.bill_date, 'DD-Mon-YYYY') = '".change_date_format(trim($start_date),'','',1)."'";
			}
			else
			{
				$date_cond = "and a.bill_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";
			}
		}

		$year_condition = "";
	}
	else
	{
		$date_cond="";

		if($db_type==0)
		{
			if($cbo_year_selection>0)
			{
				$year_condition=" and YEAR(a.bill_date)=$cbo_year_selection";
			}
		}else
		{
			if($cbo_year_selection>0)
			{
				$year_condition=" and to_char(a.bill_date,'YYYY')=$cbo_year_selection";
			}
		}
	}

	$wg_cond    = "and c.within_group=$within_group";
	$party_cond = ($party_id>0)?"and a.party_id=$party_id":"";

	if($search_string!="")
	{
		if($search_by==1)
		{
			$search_field_cond="and a.prefix_no_num=$search_string";
		}
		else if($search_by==2)
		{
			$search_field_cond="and c.job_no_prefix_num=$search_string";
		}
		else if($search_by==3)
		{
			$search_field_cond="and c.sales_booking_no like '%$search_string'";
		}
	}
	else
	{
		$search_field_cond="";
	}

	$sql ="SELECT a.id,a.bill_no,a.company_id,a.party_id,a.bill_date,a.upcharge,a.discount,b.batch_id,c.within_group,c.job_no,c.sales_booking_no 
	from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b, fabric_sales_order_mst c 
	where a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and a.process_id in(2,16) $year_condition and a.id=b.mst_id and b.order_id=c.id and b.status_active=1 and b.is_sales=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $search_field_cond $wg_cond $party_cond $date_cond and a.entry_form=486
	group by a.id,a.bill_no,a.company_id,a.party_id,a.bill_date,a.upcharge,a.discount,b.batch_id,c.within_group,c.job_no,c.sales_booking_no order by a.id desc";
	// echo $sql;//die;
	$result = sql_select($sql);
	foreach ($result as $row)
	{
		$batch_ids .= $row[csf('batch_id')].",";
		$party_ids .= $row[csf('party_id')].",";
	}


	$batch_ids = implode(",", array_filter(array_unique(explode(",",chop($batch_ids,",")))));
	$party_ids = implode(",", array_filter(array_unique(explode(",",chop($party_ids,",")))));

	if($batch_ids!=""){
		$batch_arr = return_library_array("select id, batch_no from pro_batch_create_mst where id in ($batch_ids) and status_active=1 and is_deleted=0","id","batch_no");
	}

	$company_arr=return_library_array("select id, company_short_name from lib_company where status_active=1 and is_deleted=0",'id','company_short_name');

	$buyer_arr=array();
	if($party_ids!=""){
		$buyer_arr=return_library_array( "select id, short_name from lib_buyer where id in($party_ids) and status_active=1 and is_deleted=0",'id','short_name');
	}

	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="750" class="rpt_table">
		<thead>
			<th width="30">SL</th>
			<th width="50">Company</th>
			<th width="50">Party</th>
			<th width="100">Within Group</th>
			<th width="110">FSO No</th>
			<th width="110">Booking No</th>
			<th width="100">Batch No</th>
			<th width="100">Bill No</th>
			<th>Bill Date</th>
		</thead>
	</table>
	<div style="width:750px; max-height:240px; overflow-y:scroll" id="list_container_batch" align="center">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="730" class="rpt_table" id="tbl_list_search">
			<?
			$i=1;
			foreach ($result as $row)
			{
				if ($i%2==0)
					$bgcolor="#E9F3FF";
				else
					$bgcolor="#FFFFFF";
				if($row[csf('within_group')]==1)
				{
					$buyer_name = $company_arr[$row[csf('party_id')]];
				}else{
					$buyer_name = $buyer_arr[$row[csf('party_id')]];
				}

				$batchsids = explode(',', $row[csf('batch_id')]);
				$batchNo = "";
				foreach ($batchsids as $batchid) {
					$batchNo .= $batch_arr[$batchid].",";
				}
				$batchNo = chop($batchNo,",");
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $row[csf('id')]; ?>,'<? echo $row[csf('bill_no')]; ?>');">
					<td width="30" align="center"><? echo $i; ?></td>
					<td width="50" align="center"><p><? echo $company_arr[$row[csf('company_id')]]; ?></p></td>
					<td width="50" align="center"><p><? echo $buyer_name; ?></p></td>
					<td width="100" align="center"><p><? echo ($row[csf('within_group')]==1)?"Yes":"No"; ?></p></td>
					<td width="110"><p><? echo $row[csf('job_no')]; ?></p></td>
					<td width="110"><p><? echo $row[csf('sales_booking_no')]; ?></p></td>
					<td width="100"><p><? echo $batch_arr[$row[csf('batch_id')]]; ?></p></td>
					<td width="100"><? echo $row[csf('bill_no')]; ?></td>
					<td><? echo $row[csf('bill_date')]; ?></td>
				</tr>
				<?
				$i++;
			}
			?>
		</table>
	</div>
	<?
	exit();
}

if($action=='populate_data_from_finish_fabric')
{
	$data_array = sql_select("SELECT a.id,a.bill_no,a.company_id,a.location_id,a.party_id,a.bill_date,b.batch_id,b.delivery_id,c.within_group,c.id sales_id,c.job_no,c.sales_booking_no, a.exchange_rate, a.currency, a.bill_for, a.wo_currency_id
	from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b, fabric_sales_order_mst c 
	where a.status_active=1 and a.is_deleted=0 and a.process_id in(2,16) and a.id=b.mst_id and b.order_id=c.id and b.status_active=1 and b.is_sales=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.id=$data 
	group by a.id,a.bill_no,a.company_id,a.location_id,a.party_id,a.bill_date,b.batch_id,b.delivery_id,c.id,c.within_group,c.job_no,c.sales_booking_no, a.exchange_rate, a.currency, a.bill_for, a.wo_currency_id order by a.id desc");
	$j = 1;
	foreach ($data_array as $row)
	{
		$salesNo .= $row[csf('job_no')].",";
		$salesIds .= $row[csf('sales_id')].",";
		$delivery_ids  .= $row[csf('delivery_id')].",";

		echo "document.getElementById('update_id').value 			= '".$row[csf("id")]."';\n";
		echo "document.getElementById('cbo_company_id').value 		= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_location').value 		= '".$row[csf("location_id")]."';\n";
		echo "document.getElementById('txt_bill_date').value 		= '".change_date_format($row[csf("bill_date")])."';\n";
		echo "document.getElementById('cbo_within_group').value 	= '".$row[csf("within_group")]."';\n";
		echo "document.getElementById('txt_fso_no').value 			= '".$row[csf("job_no")]."';\n";
		echo "document.getElementById('hdn_fso_id').value 			= '".$row[csf("sales_id")]."';\n";

		echo "load_drop_down( 'requires/finish_fabric_bill_entry_roll_controller', " . $row[csf("within_group")] . "+'_'+" . $row[csf("company_id")] . ", 'load_drop_down_party','cbo_party_id');\n";
		echo "document.getElementById('cbo_party_id').value 		= '".$row[csf("party_id")]."';\n";
		echo "document.getElementById('cbo_fso_currency').value 	= '".$row[csf("wo_currency_id")]."';\n";
		echo "document.getElementById('txt_exchange_rate').value 	= '".$row[csf("exchange_rate")]."';\n";
		echo "document.getElementById('cbo_bill_currency').value 	= '".$row[csf("currency")]."';\n";
		echo "document.getElementById('cbo_bill_for').value 		= '".$row[csf("bill_for")]."';\n";
		echo "$('#cbo_bill_for').attr('disabled','true')".";\n";

		$j++;
	}


	$delivery_ids = chop($delivery_ids,",");
	$salesNos = implode(",", array_filter(array_unique(explode(",",chop($salesNo,",")))));
	$salesOrderIds = implode(",", array_filter(array_unique(explode(",",chop($salesIds,",")))));

	echo "document.getElementById('hdn_challan_id').value 	= '".$delivery_ids."';\n";
	echo "document.getElementById('hdn_fso_id').value 	= '".$salesOrderIds."';\n";
	echo "document.getElementById('txt_fso_no').value 	= '".$salesNos."';\n";

	exit();
}

if ($action == "finish_fabric_receive_print")
{
	extract($_REQUEST);
	$data = explode('*', $data);

	$companysql = sql_select("select id,company_name,city,group_id from lib_company where status_active=1 and is_deleted=0");
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer where status_active=1 and is_deleted=0",'id','short_name');
	$country_arr=return_library_array( "SELECT id, country_name from  lib_country where status_active=1 and is_deleted=0", "id", "country_name"  );

	$company_arr = array();
	foreach ($companysql as $row) {
		$company_arr[$row[csf('id')]]['name'] = $row[csf('company_name')];
		$company_arr[$row[csf('id')]]['city_town'] = $row[csf('city')];
	}
	
	$sql_challan=sql_select("SELECT a.id, a.bill_no, a.bill_date, a.party_id, a.currency, a.exchange_rate, a.bill_for, b.delivery_id, b.delivery_dtls_id, b.remarks as remarks, c.within_group 
	from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b, fabric_sales_order_mst c 
	where a.id=b.mst_id and b.order_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.process_id in(2,16) and a.id=$data[1]");
	foreach ($sql_challan as $row)
	{
		$dtls_arr[$row[csf("delivery_dtls_id")]] = $row[csf("delivery_dtls_id")];
		$party_id = $row[csf("party_id")];
		$within_group = $row[csf("within_group")];
		$bill_no = $row[csf("bill_no")];
		$bill_date = $row[csf("bill_date")];
		$bill_currency = $row[csf("currency")];
		$exchange_rate = $row[csf("exchange_rate")];
		$remarks_arr[$row[csf("delivery_dtls_id")]] = $row[csf("remarks")];
		$bill_for_arr[$row[csf("bill_for")]] = $row[csf("bill_for")];
	}

	if($within_group==1)
	{
		$partyName = $company_arr[$party_id]['name'];
	}else{
		$partyName = $buyer_arr[$party_id];
	}

	if($sql_challan[0][csf("bill_for")]==16) // Knit Finish Fabric
	{
		$mainQuery = "SELECT a.id as delivery_id, a.issue_number as challan_no, a.issue_date as delevery_date, b.id as dtls_id, b.prod_id as product_id, b.body_part_id as bodypart_id, c.uom,
		sum(b.issue_qnty) delivery_qty, b.no_of_roll, c.color_id, c.febric_description_id as determination_id, d.job_no as fso_no, d.sales_booking_no as booking_no, c.rate, c.amount, c.remarks, d.within_group, d.po_company_id, d.po_buyer, d.company_id, d.buyer_id
		from inv_issue_master a, inv_finish_fabric_issue_dtls b, subcon_inbound_bill_dtls c, fabric_sales_order_mst d
		where a.id=b.mst_id and b.id=c.delivery_dtls_id and a.id=c.delivery_id and c.order_id=d.id and b.order_id=to_char(d.id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and b.id in(".implode(",",$dtls_arr).") 
		group by  a.id, a.issue_number, a.issue_date, b.id, b.prod_id, b.body_part_id, c.uom, b.no_of_roll, c.color_id, c.febric_description_id, d.job_no, d.sales_booking_no, c.rate, c.amount, c.remarks, d.within_group, d.po_company_id, d.po_buyer, d.company_id, d.buyer_id order by a.id";
	}
	else // Knitting
	{
		$mainQuery = "SELECT a.id as delivery_id, a.issue_number as challan_no, a.issue_date as delevery_date, b.id as dtls_id, b.prod_id as product_id, b.body_part_id as bodypart_id, c.uom,
		sum(b.issue_qnty) delivery_qty, b.no_of_roll, c.color_id, c.febric_description_id as determination_id, d.job_no as fso_no, d.sales_booking_no as booking_no, c.rate, c.amount, c.remarks, d.within_group, d.po_company_id, d.po_buyer, d.company_id, d.buyer_id
		from inv_issue_master a, inv_grey_fabric_issue_dtls b, subcon_inbound_bill_dtls c, fabric_sales_order_mst d
		where a.id=b.mst_id and b.id=c.delivery_dtls_id and a.id=c.delivery_id and c.order_id=d.id and a.fso_id=d.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and b.id in(".implode(",",$dtls_arr).") 
		group by  a.id, a.issue_number, a.issue_date, b.id, b.prod_id, b.body_part_id, c.uom, b.no_of_roll, c.color_id, c.febric_description_id, d.job_no, d.sales_booking_no, c.rate, c.amount, c.remarks, d.within_group, d.po_company_id, d.po_buyer, d.company_id, d.buyer_id order by a.id";
	}
	
	// echo $mainQuery;die;

	$mainQueryResult = sql_select($mainQuery);
	// echo "<pre>"; print_r($mainQueryResult);
	if(empty($mainQueryResult))
	{
		echo "<span style='color:red; font-weight:bold; font-size:14px;'><center>No Data Found</center></span>";
		exit();
	}

	$maniDataArr = array();
	foreach ($mainQueryResult as  $row)
	{
		$color_id_arr[] = $row[csf("color_id")];
		$maniDataArr[$row[csf("challan_no")]][$row[csf("bodypart_id")]][$row[csf('determination_id')]][$row[csf("color_id")]][$row[csf('uom')]]['delevery_date']=$row[csf("delevery_date")];
		$maniDataArr[$row[csf("challan_no")]][$row[csf("bodypart_id")]][$row[csf('determination_id')]][$row[csf("color_id")]][$row[csf('uom')]]['booking_no']=$row[csf("booking_no")];
		$maniDataArr[$row[csf("challan_no")]][$row[csf("bodypart_id")]][$row[csf('determination_id')]][$row[csf("color_id")]][$row[csf('uom')]]['fso_no']=$row[csf("fso_no")];
		$maniDataArr[$row[csf("challan_no")]][$row[csf("bodypart_id")]][$row[csf('determination_id')]][$row[csf("color_id")]][$row[csf('uom')]]['delivery_qty']+=$row[csf("delivery_qty")];
		$maniDataArr[$row[csf("challan_no")]][$row[csf("bodypart_id")]][$row[csf('determination_id')]][$row[csf("color_id")]][$row[csf('uom')]]['rate']+=$row[csf("rate")];
		$maniDataArr[$row[csf("challan_no")]][$row[csf("bodypart_id")]][$row[csf('determination_id')]][$row[csf("color_id")]][$row[csf('uom')]]['amount']+=$row[csf("amount")];
		$maniDataArr[$row[csf("challan_no")]][$row[csf("bodypart_id")]][$row[csf('determination_id')]][$row[csf("color_id")]][$row[csf('uom')]]['no_of_roll']++;
		$maniDataArr[$row[csf("challan_no")]][$row[csf("bodypart_id")]][$row[csf('determination_id')]][$row[csf("color_id")]][$row[csf('uom')]]['remarks']=$row[csf("remarks")];
	}

	// echo "<pre>"; print_r($maniDataArr);die;

	$composition_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$data_array=sql_select($sql_deter);
	if(count($data_array)>0)
	{
		foreach( $data_array as $row )
		{
			if(array_key_exists($row[csf('id')],$composition_arr))
			{
				$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
			}
			else
			{
				$composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
			}
		}
	}

	$color_arr=array();
	if(!empty($color_id_arr)){
		$color_arr=return_library_array( "select id, color_name from lib_color where id in(".implode(",",$color_id_arr).") and status_active=1 and is_deleted=0",'id','color_name');
	}
	?>
	<style type="text/css">
		.text-bold { font-weight:bold; }
		.a4size {
           width: 21cm;
           height: 26.7cm;
           /*font-family: Cambria, Georgia, serif;*/
		   font-size: 14px;
		   text-align:left;
		   padding-top:40px;
        }
		.a4size table tr td, .a4size table tr th{
			font-size:inherit!important;
			}
        @media print {
        .a4size{ 
        	font-size: 18px;margin: 90px 100PX 54px 25px;
        	/*font-family: Cambria;*/
            }
        size: A4 portrait;
        }
	</style>
	<!-- <div class="a4size"> 790 -->
	<div style="width:1250px;"> 
		<table width="1250" cellspacing="0" align="center" border="0" style="font-family: tahoma; font-size: 12px;">

			<tr>
				<td style="font-size:x-large" align="center">
					<strong><? echo $company_arr[$data[0]]['name']; ?></strong>
				</td>
			</tr>

			<tr>
				<td align="center">
					<?
 					$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]"); 
					foreach ($nameArray as $result)
					{ 
											 
						 echo $result[csf('plot_no')].', '.$result[csf('level_no')].', '.$result[csf('road_no')].', '.$result[csf('block_no')].', '.$result[csf('city')].', '.$result[csf('zip_code')].', '.$result[csf('province')].', '.$country_arr[$result[csf('country_id')]]; ?><br> 
						 <? echo $result[csf('email')];?> 
						 <? echo $result[csf('website')];
					}
					?>
				</td>
				<td id="barcode_img_id" align="right"></td>
			</tr>
			<tr>
				<td align="center" style="font-size:20px"><strong>Finish Fabric Bill Entry Roll</strong></td>
			</tr>
		</table>

		<br>

		<div style="width:100%;">
			<div style="clear:both;">
				<table width="1250" cellspacing="0" align="center" border="0" style="font-family: tahoma; font-size: 12px;">
					<tr>
						<td style="font-size:16px; font-weight:bold;" width="100">Party Name </td>
						<td>:</td>
						<td style="font-size:16px;"><? echo $partyName;?></strong></td>
						<td style="font-size:16px; font-weight:bold;">Bill No </td>
						<td>:</td>
						<td style="font-size:16px;"><? echo $bill_no;?></td>
						<td style="font-size:16px; font-weight:bold;">Bill Date </td>
						<td>:</td>
						<td style="font-size:16px;"><? echo $bill_date; ?></td>
					</tr>
					<tr>
						<td style="font-size:16px; font-weight:bold;" width="100">  Bill Currency</td>
						<td>:</td>
						<td style="font-size:16px;"><? echo $currency[$bill_currency];?></strong></td>
						<td style="font-size:16px; font-weight:bold;">Exchange Rate </td>
						<td>:</td>
						<td style="font-size:16px;"><? echo $exchange_rate;?></td>
						<td style="font-size:16px; font-weight:bold;">Within Group </td>
						<td>:</td>
						<td style="font-size:16px;"><? echo $yes_no[$within_group]; ?></td>
					</tr>
				</table>
				<br />
			
				<table width="1250" cellspacing="0" border="1" rules="all" class="rpt_table">
					<thead>
						<th width="30">SL</th>
						<th width="130">Challan No</th>
						<th width="80">Delivery Date</th>
						<th width="130">Booking No</th>
						<th width="130">FSO No</th>
						<th width="100">Body Part</th>
						<th width="230">Fabric Description</th>
						<th width="130">Fab Color</th>
						<th width="40">UOM</th>
						<th width="80">Delivery Qty</th>
						<th width="50">Rate </th>
						<th width="80">Amount </th>
						<th width="70">No Of Roll</th>
						<th>Remarks</th>
					</thead>
					<tbody>
						<?php
						$i=1;
						$total_delivery_qnty = $total_order_amount = $total_roll_no = 0;
						foreach ($maniDataArr as $challan_no => $challan_noArr)
						{
							foreach ($challan_noArr as $bodypart_id => $bodypartArr)
							{
								foreach ($bodypartArr as $deter_id => $deter_idArr)
								{
									foreach ($deter_idArr as $color_id => $color_idArr)
									{
										foreach ($color_idArr as $uom => $row)
										{
											if ($i%2==0)
												$bgcolor="#E9F3FF";
											else
												$bgcolor="#FFFFFF";

											$delivery_qty = number_format($row['delivery_qty'],2,".","");
											$rate   = number_format($row['rate'],2,".","");
											$amount = number_format($row['amount'],2,".","");
											?>
											<tr bgcolor="<? echo $bgcolor; ?>"  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<?echo $i;?>">
												<td width="30" align="center"><? echo $i;?></td>
												<td width="130" align="center">
													<p><? echo $challan_no;?></p>
												</td>
												<td width="80" align="center">
													<p><? echo change_date_format($row['delevery_date']);?></p>
												</td>
												<td width="130" align="center"><p><? echo $row['booking_no'];?></p></td>
												<td width="130" align="center">
													<p><? echo $row['fso_no'];?></p>
												</td>
												<td width="100" align="center">
													<p><? echo $body_part[$bodypart_id];?></p>
												</td>
												<td width="230">
													<p><? echo $composition_arr[$deter_id];?></p>
												</td>
												<td width="130" title="<? echo $color_id; ?>" align="center">
													<p><? echo $color_arr[$color_id];?></p>
												</td>
												<td width="40" align="center">
													<p><?php echo $unit_of_measurement[$uom];?></p>
												</td>
												<td width="80" align="right">
													<p><? echo $delivery_qty;?></p>
												</td>
												<td width="50" align="right">
													<p><? echo number_format($amount/$delivery_qty,2);?></p>
												</td>
												<td width="80" align="right">
													<p><? echo $amount;?></p>
												</td>
												<td width="70" align="center"><p><?php echo $row['no_of_roll'];?></p></td>
												<td style="word-break: break-all;"><p><? echo $row['remarks'];?></p></td>
											</tr>
											<?php
											$total_delivery_qnty += $delivery_qty;
											$total_order_amount  += $amount;
											$total_roll_no       += $row['no_of_roll'];
											$i++;
										}
									}
								}
							}
						}
						?>
						<tr class="tbl_bottom">
							<td width="30"></td>
							<td width="130"></td>
							<td width="80"></td>
							<td width="130"></td>
							<td width="130"></td>
							<td width="100"></td>
							<td width="230"></td>
							<td width="130" class="text-bold" align="right"></td>
							<td width="40" class="text-bold" align="right">Total:</td>
							<td width="80" align="right" class="text-bold"><? echo number_format($total_delivery_qnty,2,".","");?></td>
							<td width="50"></td>
							<td width="80" align="right" class="text-bold"><? echo number_format($total_order_amount,2,".","");?></td>
							<td width="70" align="center" class="text-bold"><? echo $total_roll_no;?></td>
							<td></td>
						</tr>
					</tbody>
				</table>
			</div>
			<div style="font-family: tahoma; font-size: 11px;"><? echo signature_table(241, $data[0], "800px"); ?></div>
		</div>
	</div>
	<?
	exit();
}

?>

