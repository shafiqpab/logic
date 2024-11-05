<?
/*-------------------------------------------- Comments -----------------------
Purpose         :   This Report Will Create Knit Garments Date Wise Fabric Booking.
Functionality   :
JS Functions    :
Created by      :   Zakaria
Creation date   :   31-07-2019
Updated by      :
Update date     :
QC Performed BY :   Ashique
QC Date         :   31-07-19
Comments        :
*/
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_buyer"){
	//echo "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active=1 and buy.is_deleted=0 $buyer_cond and b.buyer_id=buy.id and b.tag_company in($data) and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name";
    echo create_drop_down( "cbo_buyer_id", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active=1 and buy.is_deleted=0 $buyer_cond and b.buyer_id=buy.id and b.tag_company in($data) and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,21,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, "","0","" );
    exit();
}
if ($action=="load_drop_down_suplier")
{
    if($data==5 || $data==3){
       echo create_drop_down( "cbo_party_id", 150, "select id,company_name from lib_company where status_active=1 and is_deleted=0 and core_business not in(3) order by company_name", "id,company_name",0, "-- Select Supplier --", $selected);
    }
    else{
       echo create_drop_down( "cbo_party_id", 150, " select a.id,a.supplier_name from lib_supplier a,lib_supplier_tag_company b,lib_company  c  where a.id=b.supplier_id and c.id=b.tag_company and a.status_active =1 and a.is_deleted=0 and c.core_business not in(3) group by a.id,a.supplier_name","id,supplier_name", 0, "-- Select Supplier --", $selected, "");

    }
    exit();
}

if ($action == "fabricBooking_popup")
{
	echo load_html_head_contents("WO Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	//if($recieve_basis==1) $width=1045; else $width=1055;
	$width = 1055;
	?>
	
	 <script>
		var selected_id = new Array;
		var selected_name = new Array;
		var selected_no = new Array;
    	function check_all_data() {
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count - 0;
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
		
		function js_set_value( strCon ) 
		{
			var splitSTR = strCon.split("_");
			var str = splitSTR[0];
			var selectID = splitSTR[1];
			var selectDESC = splitSTR[2];
			//$('#txt_individual_id' + str).val(splitSTR[1]);
			//$('#txt_individual' + str).val('"'+splitSTR[2]+'"');
			
			toggle( document.getElementById( 'tr_' + str ), '#FFFFCC' );
			
			if( jQuery.inArray( selectID, selected_id ) == -1 ) {
				selected_id.push( selectID );
				selected_name.push( selectDESC );
				selected_no.push( str );				
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == selectID ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
				selected_no.splice( i, 1 ); 
			}
			var id = ''; var name = ''; var job = ''; var num='';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
				num += selected_no[i] + ','; 
			}
			id 		= id.substr( 0, id.length - 1 );
			name 	= name.substr( 0, name.length - 1 ); 
			num 	= num.substr( 0, num.length - 1 );
			//alert(num);
			$('#hidden_booking_id').val( id );
			$('#hidden_booking_no').val( name ); 
			//$('#txt_selected_no').val( num );
		}
    </script>
    </head>
    <body>
        <div align="center" style="width:600x;">
            <form name="searchwofrm" id="searchwofrm" autocomplete=off>
                <fieldset style="width:600px;">
                    <legend>Enter search words</legend>
                    <table cellpadding="0" cellspacing="0" border="1" rules="all" width="600" class="rpt_table">
                        <thead>
                        	<tr>
								<th colspan="5"><? echo create_drop_down( "cbo_string_search_type", 100, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                            </tr>
                            <tr>
                                <th width="60">Year</th>
                                <th width="150">Buyer</th>
                                <th width="150">Search By</th>
                                <th id="search_by_td_up" width="140">Enter Booking No</th>
                                <th>
                                    <input type="reset" name="reset" id="reset" value="Reset" style="width:70px" class="formbutton"/>
                                    <input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes" value="<?=$cbo_company_id?>">
                                    <input type="hidden" name="hidden_booking_id" id="hidden_booking_id" class="text_boxes" value="">
                                    <input type="hidden" name="hidden_booking_no" id="hidden_booking_no" class="text_boxes" value="">
                                    <input type="hidden" name="hidden_booking_type" id="hidden_booking_type" class="text_boxes" value="<?= $cbo_fab_booking_type ?>">
                                </th>
                            </tr>
                        </thead>
                        <tr class="general">
                            <td><?=create_drop_down( "cbo_year", 60, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" ); ?></td>
                            <td>
                                <?
                                    $search_by_arr = array(1 => "Booking No", 2 => "Buyer Order", 3 => "Job No", 4 => "Style Ref.", 5 => "Internal Ref", 6 => "File No");
                                    $dd = "change_search_event(this.value, '0*0*0*0*0*0', '0*0*0*0*0*0', '../../') ";
    
                                echo create_drop_down("cbo_buyer_name", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in($cbo_company_id) $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buy.buyer_name", "id,buyer_name", 1, "-- All Buyer --", '', "", $disable); ?>
                            </td>
                            <td><?=create_drop_down("cbo_search_by", 150, $search_by_arr, "", 0, "--Select--", 5, $dd, 0); ?></td>
                            <td id="search_by_td"><input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common"/></td>
                            <td><input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('cbo_year').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('hidden_booking_type').value+'_'+'<? echo $txt_booking_no; ?>'+'_'+'<? echo $txt_booking_no_id; ?>', 'create_booking_search_list_view', 'search_div', 'date_wise_fabric_booking_report_controller', 'setFilterGrid(\'tbl_list_search\',-1);');" style="width:70px;"/>
                            </td>
                        </tr>
                    </table>
                    
                </fieldset>
            </form>
            <div id="search_div" align="left"></div>
			
        </div>
    </body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if ($action == "create_booking_search_list_view")
{
	$data = explode("_", $data);
	$search_string =trim($data[0]);
	$search_by = $data[1];
	$company_id = $data[2];
	$bookingType = $data[6];
	$buyer_id = $data[3];
	$booking_year = $data[4];
	$string_search_type=str_replace("'","",$data[5]);
	$txt_booking_no_no=str_replace("'","",$data[6]);
	$txt_booking_no_id=str_replace("'","",$data[7]);

	$buyer_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');

	//if ($buyer_id == 0) $buyer_id = "%%";
	$buyer_cond='';
	$buyer_cond_s='';
	if(!empty($buyer_id))
	{
		$buyer_cond=" and a.buyer_id  in(".$buyer_id.")";
		$buyer_cond_s=" and s.buyer_id  in(".$buyer_id.")";
	}
	
	$search_field_cond = ""; $search_field_cond_sample = "";
	if($string_search_type==1)
	{
		if (trim($data[0]) != "") {
			if ($search_by == 1) {
				$search_field_cond = "and a.booking_no='$search_string'";
				$search_field_cond_sample = "and s.booking_no_prefix_num='".trim($data[0])."'";
			} 
			else if ($search_by == 2) $search_field_cond = "and b.po_number='$search_string'";
			else if ($search_by == 3) $search_field_cond = "and b.job_no_mst='$search_string'";
			else if ($search_by == 5) $search_field_cond = "and b.grouping='$search_string'";
			else if ($search_by == 6) $search_field_cond = "and b.file_no='$search_string'";
			else $search_field_cond = "and d.style_ref_no='$search_string'";
		} else {
			$search_field_cond = ""; $search_field_cond_sample = "";
		}
	}
	else if($string_search_type==2)
	{
		if (trim($data[0]) != "") {
			if ($search_by == 1) {
				$search_field_cond = "and a.booking_no like '$search_string%'";
				$search_field_cond_sample = "and s.booking_no_prefix_num='".trim($data[0])."'";
			} 
			else if ($search_by == 2) $search_field_cond = "and b.po_number like '$search_string%'";
			else if ($search_by == 3) $search_field_cond = "and b.job_no_mst like '$search_string%'";
			else if ($search_by == 5) $search_field_cond = "and b.grouping like '$search_string%'";
			else if ($search_by == 6) $search_field_cond = "and b.file_no like '$search_string%'";
			else $search_field_cond = "and d.style_ref_no like '$search_string%'";
		} else {
			$search_field_cond = ""; $search_field_cond_sample = "";
		}
	}
	else if($string_search_type==3)
	{
		if (trim($data[0]) != "") {
			if ($search_by == 1) {
				$search_field_cond = "and a.booking_no like '%$search_string'";
				$search_field_cond_sample = "and s.booking_no_prefix_num='".trim($data[0])."'";
			} 
			else if ($search_by == 2) $search_field_cond = "and b.po_number like '%$search_string'";
			else if ($search_by == 3) $search_field_cond = "and b.job_no_mst like '%$search_string'";
			else if ($search_by == 5) $search_field_cond = "and b.grouping like '%$search_string'";
			else if ($search_by == 6) $search_field_cond = "and b.file_no like '%$search_string'";
			else $search_field_cond = "and d.style_ref_no like '%$search_string'";
		} else {
			$search_field_cond = ""; $search_field_cond_sample = "";
		}
	}
	else if($string_search_type==4 || $string_search_type==0)
	{
		if (trim($data[0]) != "") {
			if ($search_by == 1) {
				$search_field_cond = "and a.booking_no like '%$search_string%'";
				$search_field_cond_sample = "and s.booking_no_prefix_num='".trim($data[0])."'";
			} 
			else if ($search_by == 2) $search_field_cond = "and b.po_number like '%$search_string%'";
			else if ($search_by == 3) $search_field_cond = "and b.job_no_mst like '%$search_string%'";
			else if ($search_by == 5) $search_field_cond = "and b.grouping like '%$search_string%'";
			else if ($search_by == 6) $search_field_cond = "and b.file_no like '%$search_string%'";
			else $search_field_cond = "and d.style_ref_no like '%$search_string%'";
		} else {
			$search_field_cond = ""; $search_field_cond_sample = "";
		}
	}
	
	$po_arr = array();
	$po_data = sql_select("select b.id, b.po_number, b.pub_shipment_date, b.grouping, b.file_no, b.po_quantity, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs from wo_po_details_master a, wo_po_break_down b where a.id=b.job_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	foreach ($po_data as $row) {
		$po_arr[$row[csf('id')]] = $row[csf('po_number')] . "**" . $row[csf('pub_shipment_date')] . "**" . $row[csf('po_quantity')] . "**" . $row[csf('po_qnty_in_pcs')] . "**" . $row[csf('grouping')] . "**" . $row[csf('file_no')];
	}
	$year_cond = "";
	$year_cond_non_order = "";
	$booking_year_condition="";
	$booking_year_non_order_condition="";
	
	if ($db_type == 0)
	{
		$year_cond = "YEAR(a.insert_date) as year";
		$year_cond_non_order = "YEAR(s.insert_date) as year";
		if($booking_year>0)
		{
			$booking_year_condition=" and YEAR(a.insert_date)=$booking_year";
			$booking_year_non_order_condition=" and YEAR(s.insert_date)=$booking_year";
		}
	}
	else if ($db_type == 2)
	{
		$year_cond = "to_char(a.insert_date,'YYYY') as year";
		$year_cond_non_order = "to_char(s.insert_date,'YYYY') as year";
		if($booking_year>0)
		{
			$booking_year_condition=" and to_char(a.insert_date,'YYYY')=$booking_year";
			$booking_year_non_order_condition=" and to_char(s.insert_date,'YYYY')=$booking_year";
		}
	}
	// check variable settings if allocation is available or not
	/* $variable_set_allocation = return_field_value("allocation", "variable_settings_inventory", "company_name=$company_id and variable_list=18 and item_category_id = 1");
	$booking_type_cond = ($variable_set_allocation==1)?" and a.booking_type not in(1,4)":""; */
	$booking_type_cond="";
	if($bookingType==1 || $bookingType==2){
		$booking_type_cond="and a.booking_type=1 and a.is_short=2";
	}
	elseif($bookingType==3){
		$booking_type_cond="and a.booking_type=1 and a.is_short=1";
	}
	elseif($bookingType==4){
		$booking_type_cond="and a.booking_type=4 and a.is_short=2";
	}
	elseif($bookingType==5){
		$booking_type_cond="and s.booking_type=4 and s.entry_form_id=90";
	}
	//echo $bookingType.'--'.$search_by; 
	if ($bookingType!=5 && ($search_by == 1 || $search_by == 2 || $search_by == 3 ||  $search_by == 4 || $search_by == 5 || $search_by == 6)) {
		$sql = "SELECT a.id, a.booking_no,a.booking_no_prefix_num, a.booking_date, a.buyer_id, a.entry_form, c.po_break_down_id, a.item_category, a.delivery_date, c.job_no as job_no_mst, 0 as type,a.booking_type, a.is_short, $year_cond from wo_booking_mst a, wo_booking_dtls c, wo_po_break_down b, wo_po_details_master d where a.booking_no=c.booking_no and c.po_break_down_id=b.id and d.id=b.job_id and a.company_id in($company_id) $buyer_cond and a.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $search_field_cond $booking_year_condition $booking_type_cond group by a.id,a.booking_no, a.booking_date, a.buyer_id,a.entry_form,c.po_break_down_id,a.item_category, a.delivery_date, c.job_no,a.insert_date,a.booking_no_prefix_num, a.booking_type,a.is_short order by a.id";
	} else {
		$sql = "SELECT s.id, s.booking_no,s.booking_no_prefix_num, s.booking_date, s.buyer_id,s.entry_form_id as entry_form, null as po_break_down_id, s.item_category, s.delivery_date, null as job_no_mst, 1 as type, 0 as booking_type, 0 as is_short, $year_cond_non_order FROM wo_non_ord_samp_booking_mst s, wo_non_ord_samp_booking_dtls t WHERE s.booking_no=t.booking_no and s.company_id in($company_id) $buyer_cond_s and s.status_active =1 and s.is_deleted=0 and t.status_active=1 and t.is_deleted=0 and s.item_category=2 and (s.fabric_source=1 OR t.fabric_source=1) $search_field_cond_sample $booking_year_non_order_condition $booking_type_cond group by s.id, s.booking_no, s.booking_no_prefix_num, s.booking_date, s.buyer_id,s.entry_form_id, s.item_category, s.delivery_date, s.insert_date order by id, type desc";
	}
	//echo $sql; die;
	$result = sql_select($sql);
	$po_id_arr = $booking_arr = array(); $job_ids = "";
	foreach ($result as $value) {
		$po_id_arr[$value[csf("booking_no")]] .= $value[csf("po_break_down_id")] . ",";
		$booking_arr[$value[csf("booking_no")]] = $value[csf("id")] . "**" . $value[csf("booking_no")] . "**" . $value[csf("booking_no_prefix_num")] . "**" . $value[csf("booking_date")] . "**" . $value[csf("buyer_id")] . "**" . $value[csf("item_category")] . "**" . $value[csf("delivery_date")] . "**" . $value[csf("job_no_mst")] . "**" . $value[csf("type")] . "**" . $value[csf("booking_type")] . "**" . $value[csf("is_short")] . "**" . $value[csf("year")]. "**" . $value[csf("entry_form")];
	}
	?>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1030" class="rpt_table">
			<thead>
				<th width="30">SL</th>
				<th width="60">Booking No</th>
				<th width="70">Type</th>
				<th width="50">Year</th>
				<th width="75">Booking Date</th>
				<th width="60">Buyer</th>
				<th width="88">Item Category</th>
				<th width="75">Delivary date</th>
				<th width="80">Job No</th>
				<th width="70">Order Qty</th>
				<th width="75">Shipment Date</th>
				<th width="130">Order No</th>
				<th width="70">Internal Ref</th>
				<th>File No</th>
			</thead>
		</table>
		<div style="width:1050px; max-height:280px; overflow-y:scroll" id="list_container_batch" align="left">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1030" class="rpt_table"
			id="tbl_list_search">
			<?
			$i = 1;
			foreach ($booking_arr as $row) {
				$data = explode("**",$row);
				if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
	
				$booking_type = '';
				if ($data[9] == 0)  $booking_type = 'Sample Without Order'; else if ($data[9] == 4) $booking_type = 'Sample'; 
				else {
					if ($data[10] == 1) $booking_type = 'Short'; else $booking_type = 'Main';
				}
	
				$po_qnty_in_pcs = '';
				$po_no = '';
				$min_shipment_date = '';
				$internal_ref = '';
				$file_nos = '';
				if ($data[1] != "" && $data[8] == 0) {
					$po_id = explode(",", rtrim($po_id_arr[$data[1]],","));
					foreach ($po_id as $id) {
						$po_data = explode("**", $po_arr[$id]);
						$po_number = $po_data[0];
						$pub_shipment_date = $po_data[1];
						$po_qnty = $po_data[2];
						$poQntyPcs = $po_data[3];
						$grouping = $po_data[4];
						$file_no = $po_data[5];
	
						if ($po_no == "") $po_no = $po_number; else $po_no .= "," . $po_number;
						if ($grouping!= "") {
							if ($internal_ref == "") $internal_ref = $grouping; else $internal_ref .= "," . $grouping;
						}
						if ($file_no != "") {
							if ($file_nos == "") $file_nos = $file_no; else $file_nos .= "," . $file_no;
						}
	
						if ($min_shipment_date == '') {
							$min_shipment_date = $pub_shipment_date;
						} else {
							if ($pub_shipment_date < $min_shipment_date) $min_shipment_date = $pub_shipment_date; else $min_shipment_date = $min_shipment_date;
						}
	
						$po_qnty_in_pcs += $poQntyPcs;
					}
				}
	
				$internal_ref = implode(",", array_unique(explode(",", $internal_ref)));
				$file_nos = implode(",", array_unique(explode(",", $file_nos)));
				?>
				<tr bgcolor="<?=$bgcolor; ?>" id="tr_<?=$i; ?>" style="text-decoration:none; cursor:pointer"
					onClick="js_set_value('<?=$i.'_'.$data[0].'_'.$data[1]; ?>');">
					<td width="30"><?=$i; ?></td>
					<td width="60" align="left" style="word-break:break-all"><? echo $data[2]; ?></td>
					<td width="70" align="center" style="word-break:break-all"><? echo $booking_type; ?></td>
					<td width="50" align="center" style="word-break:break-all"><? echo $data[11]; ?></td>
					<td width="75" align="center" style="word-break:break-all"><? echo change_date_format($data[3]); ?></td>
					<td width="60" style="word-break:break-all"><? echo $buyer_arr[$data[4]]; ?></td>
					<td width="88" style="word-break:break-all"><? echo $item_category[$data[5]]; ?></td>
					<td width="75" align="center" style="word-break:break-all"><? echo change_date_format($data[6]); ?></td>
					<td width="80" style="word-break:break-all"><? echo $data[7]; ?></td>
					<td width="70" align="right" style="word-break:break-all"><? echo $po_qnty_in_pcs; ?></td>
					<td width="75" align="center" style="word-break:break-all"><? echo change_date_format($min_shipment_date); ?></td>
					<td width="130" style="word-break:break-all"><? echo $po_no; ?></td>
					<td width="70" style="word-break:break-all"><? echo $internal_ref; ?></td>
					<td style="word-break:break-all"><? echo $file_nos; ?></td>
				</tr>
				<?
				$i++;
			}
			?>
		</table>
		<div style="width:50%; float:left" align="left">
		<input type="checkbox" name="check_all" id="check_all" onclick="check_all_data()"> Check / Uncheck All
	 </div>
	 <div style="width:50%; float:left" align="left">
		<input type="button" name="close" id="close" onclick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px">
		</div>
	 <script language="javascript" type="text/javascript">
		var style_no='<? echo $txt_booking_no_no;?>';
		var style_id='<? echo $txt_booking_no_id;?>';
		//var style_des='<?// echo $txt_style_ref;?>';
		//alert(style_id);
		if(style_no!="")
		{
			style_no_arr=style_no.split(",");
			style_id_arr=style_id.split(",");
		//	style_des_arr=style_des.split(",");
			var str_ref="";
			for(var k=0;k<style_no_arr.length; k++)
			{
				str_ref=style_no_arr[k]+'_'+style_id_arr[k];
				js_set_value(str_ref);
			}
		}
	</script>
	</div>
	<?
	exit();
}

if ($action=="report_generate")
{
    extract($_REQUEST);
    $cbo_company_id=str_replace("'","",$cbo_company_id);
    $cbo_buyer_id=str_replace("'","",$cbo_buyer_id);
    $cbo_booking_type=str_replace("'","",$cbo_fab_booking_type);
    $cbo_fabric_source=str_replace("'","",$cbo_fabric_source);
    $cbo_party_id=str_replace("'","",$cbo_party_id);
    $cbo_pay_mode=str_replace("'","",$cbo_pay_mode);
    $txt_date_from=str_replace("'","",$txt_date_from);
    $txt_date_to=str_replace("'","",$txt_date_to);
    $txt_booking_no_id=str_replace("'","",$txt_booking_no_id);
	$txt_booking_no=str_replace("'","",$txt_booking_no);
	$cbofabricnature=str_replace("'","",$cbofabricnature);
	$based_on=str_replace("'","",$cbo_based_on);

    $companyArr = return_library_array("select id,company_name from lib_company ","id","company_name");
    $buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
	$colorArr = return_library_array( "select id, color_name from lib_color", "id", "color_name");
    $supplierArr = return_library_array("select a.id,a.supplier_name from lib_supplier a,lib_supplier_tag_company b,lib_company c where a.id=b.supplier_id and c.id=b.tag_company and a.status_active =1 and a.is_deleted=0 and c.core_business not in(3) group by a.id,a.supplier_name","id","supplier_name");
	$sub_deptArr = return_library_array("select id,sub_department_name from lib_pro_sub_deparatment where  status_active =1 and is_deleted=0 order by sub_department_name","id","sub_department_name");
    $is_short = array(1=>'Short',2=>'Main');
    $fabric_booking_type = array(118 => "Main Fabric Booking",108=>'Partial Fabric Booking', 88 => "Short Fabric Booking", 89 => "Sample Fabric Booking - With Order", 0 => 'Sample Fabric Booking - Without Order');

    
	if($type==0)//show Button
	{
		if($cbo_booking_type == 1 || $cbo_booking_type == 2 || $cbo_booking_type == 3 || $cbo_booking_type == 4 || $cbo_booking_type == 0){
			if($cbo_booking_type == 1 || $cbo_booking_type == 2) $type_cond = " and e.booking_type=1 and e.is_short=2 and e.entry_form in (118,86, 108,271)";
			if($cbo_booking_type == 3) $type_cond = " and e.booking_type=1 and e.is_short=1 and e.entry_form in(88,275)";
			if($cbo_booking_type == 4) $type_cond = "and e.booking_type=4 and e.is_short=2";

			if($cbo_booking_type == 0) $type_cond = "and e.entry_form in (118,108,88,89,90,86,271,275)";
			if($cbo_company_id) $company_cond=" and e.company_id in($cbo_company_id)"; else $company_cond="";
			if($cbo_party_id) $party_cond=" and e.supplier_id in($cbo_party_id)"; else $party_cond="";
			if($cbo_pay_mode) $pay_cond=" and e.pay_mode in($cbo_pay_mode)"; else $pay_cond="";
			if($cbo_buyer_id) $buyerCond=" and e.buyer_id in($cbo_buyer_id)"; else $buyerCond="";
			if($cbo_fabric_source) $fabric_source_Cond=" and e.fabric_source in($cbo_fabric_source)"; else $fabric_source_Cond="";
			//echo $fabric_source_Cond.'D';;
			if($based_on==1){
				if($txt_date_from!="" && $txt_date_to!="") $booking_date_cond="and e.booking_date between '$txt_date_from' and '$txt_date_to'"; else $booking_date_cond="";
			}
			else{
				if($txt_date_from!="" && $txt_date_to!="") $booking_date_cond="and g.pub_shipment_date between '$txt_date_from' and '$txt_date_to'"; else $booking_date_cond="";
			}
			
	
			if($txt_booking_no_id!='')
			{
				if ($txt_booking_no_id=="") $bookingCond=""; else $bookingCond=" and e.id in ( $txt_booking_no_id )";
			}
			else
			{
				if ($txt_booking_no=="") $bookingCond=""; else $bookingCond=" and e.booking_no like '%$txt_booking_no%'";
			}


			$item_category_cond='';
			if(!empty($cbofabricnature))
			{
				$item_category_cond=" and e.item_category in($cbofabricnature)";
			}
	
		   $get_booking = "Select e.booking_no, e.booking_date,e.company_id, a.insert_date, e.update_date, e.is_approved, e.is_short, e.short_booking_type, e.buyer_id, a.job_no, a.style_ref_no, a.client_id, e.item_category, e.fabric_source, e.source, e.pay_mode, e.supplier_id, sum(f.grey_fab_qnty) as booking_qty, f.responsible_person, f.reason, sum(f.amount) as booking_amount, d.uom, g.grouping, e.entry_form, nvl(e.po_break_down_id,f.po_break_down_id) as po_break_down_id,a.product_dept,g.shipment_date from wo_booking_dtls f join wo_booking_mst e on f.booking_no=e.booking_no join wo_pre_cost_fabric_cost_dtls d on d.id=f.pre_cost_fabric_cost_dtls_id join wo_pre_cost_mst c on d.job_no=c.job_no join wo_po_details_master a on a.job_no = c.job_no join wo_booking_mst e on f.booking_no=e.booking_no join wo_pre_cost_fabric_cost_dtls d on d.id=f.pre_cost_fabric_cost_dtls_id join wo_po_break_down g on f.po_break_down_id=g.id where f.status_active=1 and f.is_deleted=0  and d.is_deleted=0 and d.status_active=1 and a.is_deleted=0 and a.status_active=1 and e.is_deleted=0 and e.status_active=1 and g.is_deleted=0 and g.status_active=1 $company_cond $buyerCond $type_cond $fabric_source_Cond $booking_date_cond $party_cond $pay_cond $bookingCond $item_category_cond group by e.booking_no, e.booking_date, e.is_short, e.short_booking_type, e.buyer_id, a.job_no, e.update_date, e.is_approved, a.style_ref_no, a.client_id, e.item_category,e.company_id, e.fabric_source, e.source, e.pay_mode, e.supplier_id, f.responsible_person, f.reason, d.uom, e.entry_form, a.insert_date, e.po_break_down_id, f.po_break_down_id, g.grouping, a.product_dept,g.shipment_date  order by e.booking_date asc";
			//echo $get_booking; die;
		}
		else{
			if($cbo_booking_type == 5) $type_cond = " and a.booking_type=4 and a.entry_form_id in (90,140,610,439,139)";
			if($cbo_company_id) $company_cond=" and a.company_id in($cbo_company_id)"; else $company_cond="";
			if($cbo_party_id) $party_cond=" and a.supplier_id in($cbo_party_id)"; else $party_cond="";
			if($cbo_pay_mode) $pay_cond=" and a.pay_mode in($cbo_pay_mode)"; else $pay_cond="";
			if($cbo_buyer_id) $buyerCond=" and a.buyer_id in($cbo_buyer_id)"; else $buyerCond="";
			if($cbo_fabric_source) $fabric_source_Cond=" and a.fabric_source in($cbo_fabric_source)"; else $fabric_source_Cond="";
			if($txt_date_from!="" && $txt_date_to!="") $booking_date_cond="and a.booking_date between '$txt_date_from' and '$txt_date_to'"; else $booking_date_cond="";
			if($txt_booking_no_id!='')
			{
				if ($txt_booking_no_id=="") $bookingCond=""; else $bookingCond=" and a.id in ( $txt_booking_no_id )";
			}
			else
			{
				if ($txt_booking_no=="") $bookingCond=""; else $bookingCond=" and a.booking_no like '%$txt_po_no%'";
			}

			$item_category_cond='';
			
			if(!empty($cbofabricnature))
			{
				$item_category_cond=" and a.item_category in($cbofabricnature)";
				//wo_non_ord_samp_booking_mst
			}
			$get_booking = "Select a.booking_no, a.booking_date, a.company_id,a.update_date, a.insert_date, a.is_approved, a.is_short, '0' as short_booking_type, a.buyer_id, '' as job_no, '' as style_ref_no, '' as client_id, '' as garments_nature, a.fabric_source, a.source, a.pay_mode, a.supplier_id, b.uom, sum(b.grey_fabric) as booking_qty, sum(b.amount) as booking_amount, a.entry_form_id as entry_form, a.po_break_down_id from wo_non_ord_samp_booking_mst a join wo_non_ord_samp_booking_dtls b on a.booking_no=b.booking_no where a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $company_cond $buyerCond $fabric_source_Cond $booking_date_cond $type_cond $party_cond $pay_cond $bookingCond $item_category_cond group by a.booking_no,a.company_id, a.booking_date, a.is_short, a.buyer_id, a.fabric_source, a.update_date, a.insert_date, a.is_approved, a.source, a.pay_mode, a.supplier_id, b.uom, a.entry_form_id, a.po_break_down_id";
			//echo $get_booking; die;
			$nameArray_approved_non = sql_select("select a.booking_no as booking_no, max(b.approved_date) as last_approve_date, min(b.approved_date) as first_approve_date from wo_non_ord_samp_booking_mst a, approval_history b where a.id=b.mst_id and b.entry_form in(9) $company_cond $buyerCond $fabric_source_Cond $booking_date_cond $type_cond $party_cond $pay_cond $approval_cond  group by a.booking_no ");
			//echo "select a.booking_no as booking_no,max(b.approved_date) as app_date from wo_non_ord_samp_booking_mst a, approval_history b where a.id=b.mst_id and b.entry_form in(9) $company_cond $buyerCond $fabric_source_Cond $booking_date_cond $type_cond $party_cond $pay_cond $approval_cond  group by a.booking_no ";
			foreach($nameArray_approved_non as $row)
			{
				$approve_data_arr[$row[csf('booking_no')]]['first_approve_date']=$row[csf('first_approve_date')];
				$approve_data_arr[$row[csf('booking_no')]]['last_approve_date']=$row[csf('last_approve_date')];
			}
		}
		// echo $get_booking;
		$sql_data=sql_select($get_booking);
		$booking_date_arr =array();
		$po_break_down_ids="";
		foreach ($sql_data as $value) {
			$booking_date_arr[$value[csf('booking_no')]]['booking_no'] = $value[csf('booking_no')];
			$booking_date_arr[$value[csf('booking_no')]]['update_date'] = $value[csf('update_date')];
			$booking_date_arr[$value[csf('booking_no')]]['company_id'] = $value[csf('company_id')];
			$booking_date_arr[$value[csf('booking_no')]]['insert_date'] = $value[csf('insert_date')];
			$booking_date_arr[$value[csf('booking_no')]]['is_approved'] = $value[csf('is_approved')];
			$booking_date_arr[$value[csf('booking_no')]]['booking_date'] = $value[csf('booking_date')];
			$booking_date_arr[$value[csf('booking_no')]]['shipment_date'] = $value[csf('shipment_date')];
			$booking_date_arr[$value[csf('booking_no')]]['booking_type'] = $value[csf('booking_type')];
			$booking_date_arr[$value[csf('booking_no')]]['short_booking_type'] = $value[csf('short_booking_type')];
			$booking_date_arr[$value[csf('booking_no')]]['short_booking_type'] = $value[csf('short_booking_type')];
			$booking_date_arr[$value[csf('booking_no')]]['buyer_id'] = $value[csf('buyer_id')];
			$booking_date_arr[$value[csf('booking_no')]]['grouping'] = $value[csf('grouping')];
			$booking_date_arr[$value[csf('booking_no')]]['style_ref_no'] = $value[csf('style_ref_no')];
			$booking_date_arr[$value[csf('booking_no')]]['client_id'] = $value[csf('client_id')];
			$booking_date_arr[$value[csf('booking_no')]]['job_no'] = $value[csf('job_no')];
			$booking_date_arr[$value[csf('booking_no')]]['item_category'] = $value[csf('item_category')];
			$booking_date_arr[$value[csf('booking_no')]]['fabric_source'] = $value[csf('fabric_source')];
			$booking_date_arr[$value[csf('booking_no')]]['source'] = $value[csf('source')];
			$booking_date_arr[$value[csf('booking_no')]]['pay_mode'] = $value[csf('pay_mode')];
			$booking_date_arr[$value[csf('booking_no')]]['supplier_id'] = $value[csf('supplier_id')];
			$booking_date_arr[$value[csf('booking_no')]]['entry_form'] = $value[csf('entry_form')];
			$booking_date_arr[$value[csf('booking_no')]]['product_dept'] = $product_dept[$value[csf('product_dept')]];
			$booking_date_arr[$value[csf('booking_no')]]['po_break_down_id'] = $value[csf('po_break_down_id')];
			$booking_date_arr[$value[csf('booking_no')]]['booking_amount'] += $value[csf('booking_amount')];
			$booking_date_arr[$value[csf('booking_no')]][$value[csf('uom')]]+= $value[csf('booking_qty')];
			
			$booking_date_arr[$value[csf('booking_no')]]['responsible_person'][$value[csf('responsible_person')]]= $value[csf('responsible_person')];
			$booking_date_arr[$value[csf('booking_no')]]['reason'][$value[csf('reason')]]= $value[csf('reason')];
	
			if(!empty($value[csf('entry_form')]));
			{
				if(!empty($value[csf('po_break_down_id')]))
				{
	
					$po_break_down_ids.=$value[csf('po_break_down_id')].",";
				}
			}
	   }
	   $po_break_down_ids=chop($po_break_down_ids,",");
	   $po_ids= explode(",", $po_break_down_ids);
	   $po_data=array();
	   if(count($po_ids))
	   {
			$sql_po="SELECT id,po_received_date from wo_po_break_down where status_active=1 ". where_con_using_array($po_ids,0,"id");
		   // echo $sql_po;
			$po_res=sql_select($sql_po);
			$po_data=array();
			foreach ($po_res as  $value) {
				$po_data[$value[csf('id')]]=$value[csf('po_received_date')];
			}
		}
		$nameArray_approved = sql_select("select e.booking_no as booking_no,max(b.approved_date) as last_approve_date,min(b.approved_date) as first_approve_date from wo_booking_mst e, approval_history b where e.id=b.mst_id and b.entry_form in(12,13,7) $company_cond $buyerCond $type_cond $fabric_source_Cond $booking_date_cond $party_cond $pay_cond $approval_cond group by e.booking_no ");
		foreach($nameArray_approved as $row)
		{
			$approve_data_arr[$row[csf('booking_no')]]['last_approve_date']=$row[csf('last_approve_date')];
			$approve_data_arr[$row[csf('booking_no')]]['first_approve_date']=$row[csf('first_approve_date')];
		}
		/* echo '<pre>';
		print_r($approve_data_arr); die;*/
	
		ob_start();
		?>
		<div align="center">
			<table width="2050px" cellspacing="0">
				<tr class="form_caption" style="border:none;">
					<td colspan="22" align="center" style="border:none;font-size:14px; font-weight:bold" ><? echo $report_title; ?></td>
				</tr>
				<tr style="border:none;">
					<td colspan="22" align="center" style="border:none; font-size:16px; font-weight:bold"><? echo $companyArr[$cbo_company_id]; ?></td>
				</tr>
			</table>
			<table width="2670px" cellspacing="0" border="1" class="rpt_table" rules="all">
				<thead>
					<tr style="font-size:13px">
						<th style="word-wrap: break-word;" width="30">SL.</th>
						<th style="word-wrap: break-word;"  width="100">Company</th>
						<th style="word-wrap: break-word;" width="100">Booking Number</th>
						<th style="word-wrap: break-word;" width="100">Revise No</th>
						<th style="word-wrap: break-word;" width="70">Booking Insert Date</th>
						<th style="word-wrap: break-word;" width="100">Shipment Date</th>
						<th style="word-wrap: break-word;" width="70">1st Appv. Date</th>
						<th style="word-wrap: break-word;" width="70">Last Appv. Date</th>
						<th style="word-wrap: break-word;" width="100">Po Received Date</th>
						<th style="word-wrap: break-word;" width="100">Booking Type</th>
						<th style="word-wrap: break-word;" width="100">Short Booking Type</th>
						<th style="word-wrap: break-word;" width="100">Buyer</th>
						<th style="word-wrap: break-word;" width="100">Buyer Client</th>
						<th style="word-wrap: break-word;" width="100">Internal Ref.</th>
						<th style="word-wrap: break-word;" width="100">Style Ref.</th>
						<th style="word-wrap: break-word;" width="100">Product Dept.</th>
						<th style="word-wrap: break-word;" width="100">Job</th>
						<th style="word-wrap: break-word;" width="100">Fabric Nature</th>
						<th style="word-wrap: break-word;" width="100">Fabric Source</th>
						<th style="word-wrap: break-word;" width="100">Source</th>
						<th style="word-wrap: break-word;" width="100">Paymode</th>
						<th style="word-wrap: break-word;" width="100">Party Name</th>
						<th style="word-wrap: break-word;" width="100">Booking Qty (Kg)</th>
						<th style="word-wrap: break-word;" width="100">Booking Qty (Yds)</th>
						<th style="word-wrap: break-word;" width="100">Booking Qty (Mtr)</th>
						<th style="word-wrap: break-word;" width="100" >Booking Amount ($)</th>
						<th style="word-wrap: break-word;" width="100" >Reason</th>
						<th style="word-wrap: break-word;" >Responsible Person</th>
					 </tr>
				</thead>
			</table>
			 <div style="width:2670px; max-height:300px; overflow-y:scroll" id="scroll_body">
			<table width="2650px" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_body">
				<tbody>
					<? $i=1; $tot_rows=0;
					$total_booking_qty_kg =0; $total_booking_qty_mtr =0; $total_booking_qty_yds= 0;
					foreach ($booking_date_arr as $row) {
						//echo $row[12]; die;
						$booking_qty_kg = 0; $booking_qty_mtr=0; $booking_qty_yds = 0;
						 if(array_key_exists(12, $row)){
							$booking_qty_kg = $row[12];
						 }
						 if(array_key_exists(23, $row)){
							$booking_qty_mtr = $row[23];
						 }
						 if(array_key_exists(27, $row)){
							$booking_qty_yds = $row[27];
						 }
						 if($row['pay_mode']==3 || $row['pay_mode']==5){
							$party_name = $companyArr[$row['supplier_id']];
						 }
						 else{
							$party_name = $supplierArr[$row['supplier_id']];
						 }
						 $total_booking_qty_kg += $booking_qty_kg;
						 $total_booking_qty_mtr += $booking_qty_mtr;
						 $total_booking_qty_yds += $booking_qty_yds;
						 $total_booking_amount = $row['booking_amount'];
		
						 if($row['entry_form'] == ''){
							$entry_form = 0;
						 }
						 else{
							$entry_form = $row['entry_form'];
						 }
						$is_approved=$row['is_approved'];
						$first_approve_dateTime=$last_approve_dateTime='';
					   
						$first_approve_dateTime=$approve_data_arr[$row['booking_no']]['first_approve_date'];
						$last_approve_dateTime=$approve_data_arr[$row['booking_no']]['last_approve_date'];
						//$approve_dateTimeArr=explode(" ",$approve_dateTime);
						$first_approve_dateTimeArr=explode(" ",$first_approve_dateTime);
						$last_approve_dateTimeArr=explode(" ",$last_approve_dateTime);
							//$booking_app_in_date=$insert_date_in_dateArr[0];
						
						  //  $booking_up_dateArr=explode(" ",$row['update_date']);
					
							$last_approve_date=$first_approve_date="";
							if(count($first_approve_dateTimeArr))
							{
								$first_approve_date=$first_approve_dateTimeArr[0];
							}
							if(count($last_approve_dateTimeArr))
							{
								$last_approve_date=$last_approve_dateTimeArr[0];
							}
							
							
						
						
		
						$po_received_date="";
						$po_br_ids=$row['po_break_down_id'];
						if(!empty($po_br_ids))
						{
						   $po_br_idss= explode(",", $po_br_ids);
						   $po_received_date=$po_data[min($po_br_idss)];
						}
						if($entry_form==0){
							$re_booking_no=$row['booking_no'];
					
							$revise_approved = sql_select("select max(b.approved_no) as approved_no from wo_non_ord_samp_booking_mst a, approval_history b where a.id=b.mst_id and booking_no='$re_booking_no' and b.entry_form=9 $company_cond $buyerCond $type_cond $fabric_source_Cond $booking_date_cond $party_cond $pay_cond $approval_cond");
						
							list($revise_approved_row) = $revise_approved;
						}else{
							$re_booking_no=$row['booking_no'];
						
							$revise_approved = sql_select("select max(b.approved_no) as approved_no from wo_booking_mst e, approval_history b where e.id=b.mst_id and booking_no='$re_booking_no'  $company_cond $buyerCond $type_cond $fabric_source_Cond $booking_date_cond $party_cond $pay_cond $approval_cond");
							list($revise_approved_row) = $revise_approved;
						}
						
						?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
							<td style="word-wrap: break-word;" width="30"><? echo $i; ?></td>
							<td style="word-wrap: break-word;" width="100"><? echo $companyArr[$row['company_id']]; ?></td>
							<td style="word-wrap: break-word;" width="100" title="<? echo $is_approved;?>"><? echo $row['booking_no']; ?></td>
							<td style="word-wrap: break-word;" width="100"><?
								if($revise_approved_row[csf('approved_no')]>1)
								{ 
									echo $revise_approved_row[csf('approved_no')]-1;
								}
							 ?></td>
							<td style="word-wrap: break-word;" width="70" title="<? echo $row['booking_date'];?>"><? echo change_date_format($row['booking_date'], "d-M-y", "-", 1); ?></td>
							<td style="word-wrap: break-word;" width="100" title="<? echo $row['shipment_date'];?>"><? echo change_date_format($row['shipment_date'], "d-M-y", "-", 1); ?></td>
							<td style="word-wrap: break-word;" width="70" title="<? echo $first_approve_dateTime;?>"><? echo change_date_format($first_approve_date, "d-M-y", "-", 1); ?> </td>
							<td style="word-wrap: break-word;" width="70" title="<? echo $last_approve_dateTime;?>"><? echo change_date_format($last_approve_date, "d-M-y", "-", 1); ?></td>
							<td style="word-wrap: break-word;" width="100"><? echo change_date_format($po_received_date, "d-M-y", "-", 1); ?></td>
							<td style="word-wrap: break-word;" width="100"><? echo $fabric_booking_type[$entry_form]; ?></td>
							<td style="word-wrap: break-word;" width="100"><? echo $short_booking_type[$row['short_booking_type']]; ?></td>
							<td style="word-wrap: break-word;" width="100"><? echo $buyerArr[$row['buyer_id']]; ?></td>
							<td style="word-wrap: break-word;" width="100"><? echo $buyerArr[$row['client_id']]; ?></td>
							<td style="word-wrap: break-word;" width="100"><? echo $row['grouping']; ?></td>
							<td style="word-wrap: break-word;" width="100"><? echo $row['style_ref_no']; ?></td>
							<td style="word-wrap: break-word;" width="100"><? echo $row['product_dept']; ?></td>
							<td style="word-wrap: break-word;" width="100"><? echo $row['job_no']; ?></td>
							<td style="word-wrap: break-word;" width="100"><? echo $item_category[$row['item_category']]; ?></td>
							<td style="word-wrap: break-word;" width="100"><? echo $fabric_source[$row['fabric_source']] ?></td>
							<td style="word-wrap: break-word;" width="100"><? echo $source[$row['source']]; ?></td>
							<td style="word-wrap: break-word;" width="100"><? echo $pay_mode[$row['pay_mode']]; ?></td>
							<td style="word-wrap: break-word;" width="100"><? echo $party_name; ?></td>
							<td style="word-wrap: break-word;" width="100" align="right"><? echo number_format($booking_qty_kg,2); ?></td>
							<td style="word-wrap: break-word;" width="100" align="right"><? echo number_format($booking_qty_yds,2); ?></td>
							<td style="word-wrap: break-word;" width="100" align="right"><? echo number_format($booking_qty_mtr,2); ?></td>
							<td style="word-wrap: break-word;" width="100" align="right"><? echo number_format($row['booking_amount'],2); ?></td>
		
							<td style="word-wrap: break-word;" width="100" align="center"><p><? echo implode(",", $row['reason']); ?></p></td>
							<td style="word-wrap: break-word;" align="center"><p><? echo implode(",", $row['responsible_person']); ?></p></td>
						</tr>
		
					  <? $i++; $tot_rows++;
					} ?>
				</tbody>
			</table>
            </div>
			<table width="2670px" cellspacing="0" border="1" class="rpt_table" rules="all" id="report_table_footer" >
				<tfoot>
					<tr style="font-size:13px">
						<th bgcolor= "#A0A6AC" width="30"></th>
						<th bgcolor= "#A0A6AC" width="100"></th>
						<th bgcolor= "#A0A6AC" width="100"></th>
						<th bgcolor= "#A0A6AC" width="100"></th>
					
						<th bgcolor= "#A0A6AC" width="70"></th>
						<th bgcolor= "#A0A6AC" width="100"></th>
						<th bgcolor= "#A0A6AC" width="70"></th>
						<th bgcolor= "#A0A6AC" width="70"></th>
					
						<th bgcolor= "#A0A6AC" width="100"></th>
						<th bgcolor= "#A0A6AC" width="100"></th>
						<th bgcolor= "#A0A6AC" width="100"></th>
						<th bgcolor= "#A0A6AC" width="100"></th>
						<th bgcolor= "#A0A6AC" width="100"></th>
						<th bgcolor= "#A0A6AC" width="100"></th>
						<th bgcolor= "#A0A6AC" width="100"></th>
						<th bgcolor= "#A0A6AC" width="100"></th>
						<th bgcolor= "#A0A6AC" width="100"></th>
						<th bgcolor= "#A0A6AC" width="100"></th>
						<th bgcolor= "#A0A6AC" width="100"></th>
						<th bgcolor= "#A0A6AC" width="100"></th>
						<th bgcolor= "#A0A6AC" width="100"></th>
						<th bgcolor= "#A0A6AC" width="100"></th>
						<th bgcolor= "#A0A6AC" style="word-wrap: break-word;" width="100" id="total_booking_qty_kg" align="right"><? echo number_format($total_booking_qty_kg,2); ?></th>
						<th bgcolor= "#A0A6AC" style="word-wrap: break-word;" width="100" id="total_booking_qty_yds" align="right"><? echo number_format($total_booking_qty_yds,2); ?></th>
						<th bgcolor= "#A0A6AC" style="word-wrap: break-word;" width="100" id="total_booking_qty_mtr" align="right"><? echo number_format($total_booking_qty_mtr,2); ?></th>
						<th bgcolor= "#A0A6AC" style="word-wrap: break-word;" width="100" id="total_booking_amount" align="right"><? echo number_format($total_booking_amount,2); ?></th>
						<th bgcolor= "#A0A6AC" style="word-wrap: break-word;" width="100"></th>
						<th bgcolor= "#A0A6AC"></th>
					</tr>
				</tfoot>
			</table>
		</div>
		<?
	}
	else if($type==1)//Details Button
	{
		if($cbo_booking_type == 1 || $cbo_booking_type == 2 || $cbo_booking_type == 3 || $cbo_booking_type == 4 || $cbo_booking_type == 0){
			if($cbo_booking_type == 1 || $cbo_booking_type == 2) $type_cond = " and e.booking_type=1 and e.is_short=2 and e.entry_form in (118,86, 108,271)";
			if($cbo_booking_type == 3) $type_cond = " and e.booking_type=1 and e.is_short=1 and e.entry_form in(88,275)";
			if($cbo_booking_type == 4) $type_cond = "and e.booking_type=4 and e.is_short=2";

			if($cbo_booking_type == 0) $type_cond = "and e.entry_form in (118,108,88,89,90,86,271,275)";
			if($cbo_company_id) $company_cond=" and e.company_id in($cbo_company_id)"; else $company_cond="";
			if($cbo_party_id) $party_cond=" and e.supplier_id in($cbo_party_id)"; else $party_cond="";
			if($cbo_pay_mode) $pay_cond=" and e.pay_mode in($cbo_pay_mode)"; else $pay_cond="";
			if($cbo_buyer_id) $buyerCond=" and e.buyer_id in($cbo_buyer_id)"; else $buyerCond="";
			if($cbo_fabric_source) $fabric_source_Cond=" and e.fabric_source in($cbo_fabric_source)"; else $fabric_source_Cond="";
			if($txt_date_from!="" && $txt_date_to!="") $booking_date_cond="and e.booking_date between '$txt_date_from' and '$txt_date_to'"; else $booking_date_cond="";
	
			if($txt_booking_no_id!='')
			{
				if ($txt_booking_no_id=="") $bookingCond=""; else $bookingCond=" and e.id in ( $txt_booking_no_id )";
			}
			else
			{
				if ($txt_booking_no=="") $bookingCond=""; else $bookingCond=" and e.booking_no like '%$txt_booking_no%'";
			}

			$item_category_cond='';
			if(!empty($cbofabricnature))
			{
				$item_category_cond=" and e.item_category in($cbofabricnature)";
			}
	 
		  /*  $get_booking = "Select e.booking_no, e.booking_type, e.is_short, e.short_booking_type, e.booking_date, e.buyer_id, a.job_no, a.style_ref_no, e.fabricstructure, f.fabric_color_id, d.body_part_id, d.color_type_id, d.construction, d.composition, d.gsm_weight, f.dia_width, sum(f.grey_fab_qnty) as booking_qty, sum(f.fin_fab_qnty) as finQty, f.process_loss_percent,d.id, f.responsible_person,d.body_part_type, f.reason, d.uom, e.item_category, e.entry_form, nvl(e.po_break_down_id,f.po_break_down_id) as po_break_down_id from wo_booking_dtls f join wo_booking_mst e on f.booking_no=e.booking_no join wo_pre_cost_fabric_cost_dtls d on d.id=f.pre_cost_fabric_cost_dtls_id join wo_pre_cost_mst c on d.job_no=c.job_no join wo_po_details_master a on a.job_no = c.job_no where f.status_active=1 and f.is_deleted=0  and d.is_deleted=0 and d.status_active=1 and a.is_deleted=0 and a.status_active=1 and e.is_deleted=0 and e.status_active=1 $company_cond $buyerCond $type_cond $fabric_source_Cond $booking_date_cond $party_cond $pay_cond $bookingCond $item_category_cond group by e.booking_no, e.booking_type, e.is_short, e.short_booking_type, e.booking_date, e.buyer_id, a.job_no, a.style_ref_no, e.fabricstructure, f.fabric_color_id, d.body_part_id, d.color_type_id, d.construction, d.composition, d.gsm_weight, f.dia_width, f.process_loss_percent, f.responsible_person, f.reason, d.uom, e.item_category, e.entry_form, e.po_break_down_id, f.po_break_down_id,d.id,d.body_part_type order by e.booking_date asc"; */
		$get_booking = "Select e.booking_no, e.booking_type, e.is_short, e.short_booking_type, e.booking_date, e.buyer_id, a.job_no, a.style_ref_no, e.fabricstructure, f.fabric_color_id, d.body_part_id, d.color_type_id, d.construction, d.composition, d.gsm_weight, f.dia_width, sum(f.grey_fab_qnty) as booking_qty, sum(f.fin_fab_qnty) as finQty, f.process_loss_percent,d.id, f.responsible_person,d.body_part_type, f.reason, d.uom, e.item_category, e.entry_form, nvl(e.po_break_down_id,f.po_break_down_id) as po_break_down_id,sum(g.plan_cut_qnty) as plan_cut_qnty from wo_booking_dtls f join wo_booking_mst e on f.booking_no=e.booking_no join wo_pre_cost_fabric_cost_dtls d on d.id=f.pre_cost_fabric_cost_dtls_id join wo_pre_cost_mst c on d.job_no=c.job_no join wo_po_details_master a on a.job_no = c.job_no join wo_po_color_size_breakdown g on g.id=f.color_size_table_id and f.po_break_down_id=g.po_break_down_id where f.status_active=1 and f.is_deleted=0  and d.is_deleted=0 and d.status_active=1 and a.is_deleted=0 and a.status_active=1  and g.is_deleted=0 and g.status_active=1 and e.is_deleted=0 and e.status_active=1 $company_cond $buyerCond $type_cond $fabric_source_Cond $booking_date_cond $party_cond $pay_cond $bookingCond $item_category_cond group by e.booking_no, e.booking_type, e.is_short, e.short_booking_type, e.booking_date, e.buyer_id, a.job_no, a.style_ref_no, e.fabricstructure, f.fabric_color_id, d.body_part_id, d.color_type_id, d.construction, d.composition, d.gsm_weight, f.dia_width, f.process_loss_percent, f.responsible_person, f.reason, d.uom, e.item_category, e.entry_form, e.po_break_down_id, f.po_break_down_id,d.id,d.body_part_type order by e.booking_date asc";

			
		}
		else{
			if($cbo_booking_type == 5) $type_cond = " and a.booking_type=4 and a.entry_form_id in (90,140,610,439,139)";
			if($cbo_company_id) $company_cond=" and a.company_id in($cbo_company_id)"; else $company_cond="";
			if($cbo_party_id) $party_cond=" and a.supplier_id in($cbo_party_id)"; else $party_cond="";
			if($cbo_pay_mode) $pay_cond=" and a.pay_mode in($cbo_pay_mode)"; else $pay_cond="";
			if($cbo_buyer_id) $buyerCond=" and a.buyer_id in($cbo_buyer_id)"; else $buyerCond="";
			if($cbo_fabric_source) $fabric_source_Cond=" and a.fabric_source in($cbo_fabric_source)"; else $fabric_source_Cond="";
			if($txt_date_from!="" && $txt_date_to!="") $booking_date_cond="and a.booking_date between '$txt_date_from' and '$txt_date_to'"; else $booking_date_cond="";
			if($txt_booking_no_id!='')
			{
				if ($txt_booking_no_id=="") $bookingCond=""; else $bookingCond=" and a.id in ( $txt_booking_no_id )";
			}
			else
			{
				if ($txt_booking_no=="") $bookingCond=""; else $bookingCond=" and a.booking_no like '%$txt_booking_no%'";
			}

			$item_category_cond='';
			if(!empty($cbofabricnature))
			{
				$item_category_cond=" and a.item_category in($cbofabricnature)";
				//wo_non_ord_samp_booking_mst
			}
			$get_booking = "Select a.booking_no, '0' as booking_type, a.is_short, '0' as short_booking_type, a.booking_date, a.buyer_id, '' as job_no, '' as style_ref_no, b.fabric_color as fabric_color_id, b.body_part as body_part_id, '0' as color_type_id, b.construction, b.composition, b.gsm_weight, b.dia_width, sum(b.grey_fabric) as booking_qty, sum(b.finish_fabric) as finQty, b.process_loss as process_loss_percent, '' as responsible_person, '' as reason, b.uom, a.item_category, a.entry_form_id as entry_form  from wo_non_ord_samp_booking_mst a join wo_non_ord_samp_booking_dtls b on a.booking_no=b.booking_no where a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $company_cond $buyerCond $fabric_source_Cond $booking_date_cond $type_cond $party_cond $pay_cond $bookingCond $item_category_cond group by a.booking_no, a.is_short, a.booking_date, a.buyer_id, b.fabric_color, b.body_part, b.construction, b.composition, b.gsm_weight, b.dia_width,  b.process_loss, b.uom, a.item_category, a.entry_form_id order by a.booking_date asc";
		}
		$sql_data=sql_select($get_booking);
		$job_no_arr=array();
		foreach ($sql_data as $row) {
			if($row[csf('is_short')]== 1 && !empty($row[csf('job_no')]))
			{
				array_push($job_no_arr, $row[csf('job_no')]);
			}
		}
		$job_cond=where_con_using_array($job_no_arr,1,"b.job_no");
		$sql_short="SELECT b.booking_no, b.job_no
					  FROM wo_booking_mst a, wo_booking_dtls b
					  WHERE     a.booking_no = b.booking_no
					       AND a.status_active = 1
					       AND a.is_deleted = 0
					       AND b.status_active = 1
					       AND b.is_deleted = 0
					       AND a.booking_type = 1
					       AND a.item_category=2
					       AND a.entry_form IN (118, 86, 108) $job_cond";
		//echo $sql_short;
		$res_short=sql_select($sql_short);
		$booking_for_short_booking=array();
		foreach ($res_short as $row) {
			$booking_for_short_booking[$row[csf('job_no')]].=$row[csf('booking_no')].",";
		}
	
		ob_start();
		?>
		<div align="center">
			<table width="2250px" cellspacing="0">
				<tr class="form_caption" style="border:none;">
					<td colspan="22" align="center" style="border:none;font-size:14px; font-weight:bold" ><?=$report_title; ?></td>
				</tr>
				<tr style="border:none;">
					<td colspan="22" align="center" style="border:none; font-size:16px; font-weight:bold"><?=$companyArr[$cbo_company_id]; ?></td>
				</tr>
			</table>
			<table width="2250px" cellspacing="0" border="1" class="rpt_table" rules="all">
				<thead>
					<tr style="font-size:13px">
						<th style="word-wrap: break-word;" width="30">SL.</th>
						<th style="word-wrap: break-word;" width="100">Booking Type</th>
						<th style="word-wrap: break-word;" width="100">Booking Number</th>
						<th style="word-wrap: break-word;" width="100">M.Booking No</th>
						<th  width="70">Booking Date</th>
                        <th style="word-wrap: break-word;" width="100">Buyer</th>
						<th style="word-wrap: break-word;" width="100">Style Ref.</th>
						<th style="word-wrap: break-word;" width="100">Job</th>
                        <th style="word-wrap: break-word;" width="100">Fabric Nature</th>
                        
						<th style="word-wrap: break-word;" width="100">Color</th>
						<th style="word-wrap: break-word;" width="200">Body Part</th>
						<th style="word-wrap: break-word;" width="100">Color Type</th>
						<th style="word-wrap: break-word;" width="130">Fabric Type</th>
						<th style="word-wrap: break-word;" width="100">Yarn Composition</th>

						<th  width="100">GSM</th>
						<th  width="100">Dia</th>
						<th  width="100">Grey Qty</th>
						<th  width="100">Finished Qty</th>
						<th  width="100">PCS Qty</th>
						<th  width="100">Process Loss</th>
						<th style="word-wrap: break-word;" width="100">Responsible person</th>
						<th style="word-wrap: break-word;">Reason</th>
					 </tr>
				</thead>
			</table>
			<div style="width:2250px; max-height:300px; overflow-y:scroll" id="scroll_body"> 
			<table width="2230px" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_body">
				<tbody>
					<? $i=1; $tot_rows=0;
					$total_booking_qty_kg =0; $total_booking_qty_mtr =0; $total_booking_qty_yds= 0;
					foreach ($sql_data as $row)
					{
						$plan_cut=$plan_cut_qnty_arr[$row[csf('id')]];
						$mbooking_no='';
						if ($row[csf('booking_type')]== 0)  $booking_type = 'Sample Without Order'; else if ($row[csf('booking_type')] == 4) $booking_type = 'Sample'; 
						else {
							if ($row[csf('is_short')]== 1) $booking_type = 'Short'; else $booking_type = 'Main';
							if($row[csf('is_short')]== 1)
							{
								$mbooking_no=implode(", ", array_unique(explode(",", chop($booking_for_short_booking[$row[csf('job_no')]],","))));
							}
							
						}
						?>
						<tr bgcolor="<?=$bgcolor;?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor;?>');" id="tr_<?=$i; ?>">
							<td width="30"><?=$i; ?></td>
							<td width="100" style="word-wrap: break-word;"><?=$booking_type; ?></td>
							<td width="100" style="word-wrap: break-word;"><?=$row[csf('booking_no')]; ?></td>
							<td width="100" style="word-wrap: break-word;"><?=$mbooking_no; ?></td>
							<td width="70"><?=change_date_format($row[csf('booking_date')], "d-M-y", "-", 1); ?></td>
	                        <td width="100" style="word-wrap: break-word;"><?=$buyerArr[$row[csf('buyer_id')]]; ?></td>
							<td width="100" style="word-wrap: break-word;"><?=$row[csf('style_ref_no')]; ?></td>
							<td width="100" style="word-wrap: break-word;"><?=$row[csf('job_no')]; ?></td>
	                        <td width="100" style="word-wrap: break-word;"><?=$item_category[$row[csf('item_category')]]; ?></td>
	                        
	                        <td width="100" style="word-wrap: break-word;"><?=$colorArr[$row[csf('fabric_color_id')]]; ?></td>
							<td width="200" style="word-wrap: break-word;"><?=$body_part[$row[csf('body_part_id')]]; ?></td>
							<td width="100" style="word-wrap: break-word;"><?=$color_type[$row[csf('color_type_id')]]; ?></td>
							<td width="130" style="word-wrap: break-word;"><?=$row[csf('construction')]; ?></td>
							<td width="100" style="word-wrap: break-word;"><?=$row[csf('composition')]; ?></td>
	                        
							<td width="100"><?=$row[csf('gsm_weight')]; ?></td>
							<td width="100"><?=$row[csf('dia_width')]; ?></td>
							<td width="100" align="right"><?=number_format($row[csf('booking_qty')],2); ?></td>
							<td width="100" align="right"><?=number_format($row[csf('finQty')],2); ?></td>
							<td width="100" align="right"><?
							if($row[csf('body_part_type')]==40){
								$coller_cuff_qty=$row[csf('plan_cut_qnty')];
								echo number_format($coller_cuff_qty,2);
							}else if($row[csf('body_part_type')]==50) {
								$coller_cuff_qty=$row[csf('plan_cut_qnty')]*2;
								echo number_format($coller_cuff_qty,2);
							}else{
								echo "";
							}
							//=number_format($row[csf('finQty')],2); ?></td>
							<td width="100" align="right"><?=number_format($row[csf('process_loss_percent')],2); ?></td>
							<td width="100" style="word-wrap: break-word;"><?=$row[csf('responsible_person')]; ?></td>
							<td style="word-wrap: break-word;"><?=$row[csf('reason')]; ?></td>
						</tr>
		
					  <? $i++; $tot_rows++;
					  $tot_booking_qty+=$row[csf('booking_qty')];
					  $tot_fin_qnty+=$row[csf('finQty')];
					  $tot_plan_qnty+=$coller_cuff_qty;
					} ?>
				</tbody>
			</table>
            </div>
			<table width="2230px" cellspacing="0" border="1" class="rpt_table" rules="all" id="report_table_footer" >
				<tr style="font-size:13px">
					<th bgcolor= "#A0A6AC" width="30">&nbsp;</th>
					<th bgcolor= "#A0A6AC"width="100">&nbsp;</th>
					<th bgcolor= "#A0A6AC"width="100">&nbsp;</th>
					<th bgcolor= "#A0A6AC"width="100">&nbsp;</th>
					<th bgcolor= "#A0A6AC"width="70">&nbsp;</th>
					<th bgcolor= "#A0A6AC"width="100">&nbsp;</th>
					<th bgcolor= "#A0A6AC"width="100">&nbsp;</th>
					<th bgcolor= "#A0A6AC"width="100">&nbsp;</th>
					<th bgcolor= "#A0A6AC"width="100">&nbsp;</th>
					<th bgcolor= "#A0A6AC"width="100">&nbsp;</th>
					<th bgcolor= "#A0A6AC"width="200">&nbsp;</th> 
					<th bgcolor= "#A0A6AC"width="100">&nbsp;</th>
					<th bgcolor= "#A0A6AC"width="130">&nbsp;</th>
					<th bgcolor= "#A0A6AC"width="100">&nbsp;</th>
					<th bgcolor= "#A0A6AC"width="100">&nbsp;</th>
					<th bgcolor= "#A0A6AC"width="100">&nbsp;</th>
					<th bgcolor= "#A0A6AC"width="100" align="right" id="td_tot_booking_qty"><? echo number_format($tot_booking_qty,2); ?></th>
					<th bgcolor= "#A0A6AC"width="100" align="right" id="td_tot_fin_qnty"><? echo number_format($tot_fin_qnty,2); ?></th>
					<th bgcolor= "#A0A6AC"width="100" align="right" id="td_tot_plan_qnty"><? echo number_format($tot_plan_qnty,2); ?></th>
					<th bgcolor= "#A0A6AC"width="100" align="right"><?// echo number_format($total_booking_qty_mtr,2); ?></th>
					<th bgcolor= "#A0A6AC"width="100" align="right"><?// echo number_format($total_booking_amount,2); ?></th>
					<th bgcolor= "#A0A6AC">&nbsp;</th>
				 </tr>
			</table>
		</div>
		<?
	
	}
	else if($type==2)//Fabric Wise Button
	{
		if($cbo_booking_type == 1 || $cbo_booking_type == 2 || $cbo_booking_type == 3 || $cbo_booking_type == 4 || $cbo_booking_type == 0){
			if($cbo_booking_type == 1 || $cbo_booking_type == 2) $type_cond = " and e.booking_type=1 and e.is_short=2 and e.entry_form in (118,86, 108,271)";
			if($cbo_booking_type == 3) $type_cond = " and e.booking_type=1 and e.is_short=1 and e.entry_form in(88,275)";
			if($cbo_booking_type == 4) $type_cond = "and e.booking_type=4 and e.is_short=2";

			if($cbo_booking_type == 0) $type_cond = "and e.entry_form in (118,108,88,89,90,86,271,275)";
			if($cbo_company_id) $company_cond=" and e.company_id in($cbo_company_id)"; else $company_cond="";
			if($cbo_party_id) $party_cond=" and e.supplier_id in($cbo_party_id)"; else $party_cond="";
			if($cbo_pay_mode) $pay_cond=" and e.pay_mode in($cbo_pay_mode)"; else $pay_cond="";
			if($cbo_buyer_id) $buyerCond=" and e.buyer_id in($cbo_buyer_id)"; else $buyerCond="";
			if($cbo_fabric_source) $fabric_source_Cond=" and e.fabric_source in($cbo_fabric_source)"; else $fabric_source_Cond="";
			if($based_on==1){
				if($txt_date_from!="" && $txt_date_to!="") $booking_date_cond="and e.booking_date between '$txt_date_from' and '$txt_date_to'"; else $booking_date_cond="";
			}
			else{
				if($txt_date_from!="" && $txt_date_to!="") $booking_date_cond="and c.pub_shipment_date between '$txt_date_from' and '$txt_date_to'"; else $booking_date_cond="";
			}
			
	
			if($txt_booking_no_id!='')
			{
				if ($txt_booking_no_id=="") $bookingCond=""; else $bookingCond=" and e.id in ( $txt_booking_no_id )";
			}
			else
			{
				if ($txt_booking_no=="") $bookingCond=""; else $bookingCond=" and e.booking_no_prefix_num='$txt_booking_no'";
			}
			$item_category_cond='';
			if(!empty($cbofabricnature))
			{
				$item_category_cond=" and e.item_category in($cbofabricnature)";
			}
	
		    $get_booking = "Select a.job_no,a.product_dept, a.style_ref_no, a.client_id, e.booking_no,e.company_id, e.booking_date, a.insert_date, e.update_date, e.is_approved, e.is_short, e.short_booking_type, e.buyer_id, e.item_category, e.fabric_source, e.source, e.pay_mode, e.supplier_id, e.entry_form, e.delivery_date, f.grey_fab_qnty as booking_qty, f.responsible_person, f.reason, f.amount as booking_amount, d.uom, d.lib_yarn_count_deter_id as libfabid, d.fabric_description, c.po_received_date, c.pub_shipment_date from wo_booking_dtls f join wo_booking_mst e on f.booking_no=e.booking_no join wo_pre_cost_fabric_cost_dtls d on d.id=f.pre_cost_fabric_cost_dtls_id join wo_po_break_down c on d.job_no=c.job_no_mst and f.po_break_down_id=c.id join wo_po_details_master a on a.job_no = c.job_no_mst where f.status_active=1 and f.is_deleted=0  and d.is_deleted=0 and d.status_active=1 and a.is_deleted=0 and a.status_active=1 and e.is_deleted=0 and e.status_active=1 $company_cond $buyerCond $type_cond $fabric_source_Cond $booking_date_cond $party_cond $pay_cond $bookingCond $item_category_cond order by e.booking_date asc";
			//echo $get_booking; die;
		}
		else{
			if($cbo_booking_type == 5) $type_cond = " and a.booking_type=4 and a.entry_form_id in (90,140,610,439,139)";
			if($cbo_company_id) $company_cond=" and a.company_id in($cbo_company_id)"; else $company_cond="";
			if($cbo_party_id) $party_cond=" and a.supplier_id in($cbo_party_id)"; else $party_cond="";
			if($cbo_pay_mode) $pay_cond=" and a.pay_mode in($cbo_pay_mode)"; else $pay_cond="";
			if($cbo_buyer_id) $buyerCond=" and a.buyer_id in($cbo_buyer_id)"; else $buyerCond="";
			if($cbo_fabric_source) $fabric_source_Cond=" and a.fabric_source in($cbo_fabric_source)"; else $fabric_source_Cond="";
			if($txt_date_from!="" && $txt_date_to!="") $booking_date_cond="and a.booking_date between '$txt_date_from' and '$txt_date_to'"; else $booking_date_cond="";
			if($txt_booking_no_id!='')
			{
				if ($txt_booking_no_id=="") $bookingCond=""; else $bookingCond=" and a.id in ( $txt_booking_no_id )";
			}
			else
			{
				if ($txt_booking_no=="") $bookingCond=""; else $bookingCond=" and a.booking_no_prefix_num='$txt_booking_no'";
			}
			$item_category_cond='';
			if(!empty($cbofabricnature))
			{
				$item_category_cond=" and e.item_category in($cbofabricnature)";
			}
			
			$get_booking = "Select a.booking_no, a.booking_date,a.company_id, a.update_date, a.insert_date, a.is_approved, a.is_short, '0' as short_booking_type, a.buyer_id, '' as job_no, '' as style_ref_no, '' as client_id, '' as garments_nature, a.fabric_source, a.source, a.pay_mode, a.supplier_id, b.uom, sum(b.grey_fabric) as booking_qty, sum(b.amount) as booking_amount, a.entry_form_id as entry_form, a.po_break_down_id from wo_non_ord_samp_booking_mst a join wo_non_ord_samp_booking_dtls b on a.booking_no=b.booking_no where a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $company_cond $buyerCond $fabric_source_Cond $booking_date_cond $type_cond $party_cond $pay_cond $bookingCond $item_category_cond group by a.booking_no, a.booking_date,a.company_id, a.is_short, a.buyer_id, a.fabric_source, a.update_date, a.insert_date, a.is_approved, a.source, a.pay_mode, a.supplier_id, b.uom, a.entry_form_id, a.po_break_down_id";
			$nameArray_approved_non = sql_select("select a.booking_no as booking_no, max(b.approved_date) as last_approve_date, min(b.approved_date) as first_approve_date from wo_non_ord_samp_booking_mst a, approval_history b where a.id=b.mst_id and b.entry_form in(9) $company_cond $buyerCond $fabric_source_Cond $booking_date_cond $type_cond $party_cond $pay_cond $approval_cond  group by a.booking_no ");
			//echo "select a.booking_no as booking_no,max(b.approved_date) as app_date from wo_non_ord_samp_booking_mst a, approval_history b where a.id=b.mst_id and b.entry_form in(9) $company_cond $buyerCond $fabric_source_Cond $booking_date_cond $type_cond $party_cond $pay_cond $approval_cond  group by a.booking_no ";
			foreach($nameArray_approved_non as $row)
			{
				$approve_data_arr[$row[csf('booking_no')]]['first_approve_date']=$row[csf('first_approve_date')];
				$approve_data_arr[$row[csf('booking_no')]]['last_approve_date']=$row[csf('last_approve_date')];
			}
		}
		//echo $get_booking;
		$sql_data=sql_select($get_booking);
		$booking_date_arr =array(); $po_data=array();
		$po_break_down_ids="";
		foreach ($sql_data as $value) {
			$booking_date_arr[$value[csf('booking_no')]][$value[csf('libfabid')]]['booking_no'] = $value[csf('booking_no')];
			$booking_date_arr[$value[csf('booking_no')]][$value[csf('libfabid')]]['company_id'] = $value[csf('company_id')];
			$booking_date_arr[$value[csf('booking_no')]][$value[csf('libfabid')]]['update_date'] = $value[csf('update_date')];
			$booking_date_arr[$value[csf('booking_no')]][$value[csf('libfabid')]]['insert_date'] = $value[csf('insert_date')];
			$booking_date_arr[$value[csf('booking_no')]][$value[csf('libfabid')]]['is_approved'] = $value[csf('is_approved')];
			$booking_date_arr[$value[csf('booking_no')]][$value[csf('libfabid')]]['booking_date'] = $value[csf('booking_date')];
			$booking_date_arr[$value[csf('booking_no')]][$value[csf('libfabid')]]['booking_type'] = $value[csf('booking_type')];
			$booking_date_arr[$value[csf('booking_no')]][$value[csf('libfabid')]]['short_booking_type'] = $value[csf('short_booking_type')];
			$booking_date_arr[$value[csf('booking_no')]][$value[csf('libfabid')]]['short_booking_type'] = $value[csf('short_booking_type')];
			$booking_date_arr[$value[csf('booking_no')]][$value[csf('libfabid')]]['buyer_id'] = $value[csf('buyer_id')];
			$booking_date_arr[$value[csf('booking_no')]][$value[csf('libfabid')]]['style_ref_no'] = $value[csf('style_ref_no')];
			$booking_date_arr[$value[csf('booking_no')]][$value[csf('libfabid')]]['client_id'] = $value[csf('client_id')];
			$booking_date_arr[$value[csf('booking_no')]][$value[csf('libfabid')]]['job_no'] = $value[csf('job_no')];
			$booking_date_arr[$value[csf('booking_no')]][$value[csf('libfabid')]]['product_dept'] = $product_dept[$value[csf('product_dept')]];
			$booking_date_arr[$value[csf('booking_no')]][$value[csf('libfabid')]]['item_category'] = $value[csf('item_category')];
			$booking_date_arr[$value[csf('booking_no')]][$value[csf('libfabid')]]['fabric_source'] = $value[csf('fabric_source')];
			$booking_date_arr[$value[csf('booking_no')]][$value[csf('libfabid')]]['source'] = $value[csf('source')];
			$booking_date_arr[$value[csf('booking_no')]][$value[csf('libfabid')]]['pay_mode'] = $value[csf('pay_mode')];
			$booking_date_arr[$value[csf('booking_no')]][$value[csf('libfabid')]]['supplier_id'] = $value[csf('supplier_id')];
			$booking_date_arr[$value[csf('booking_no')]][$value[csf('libfabid')]]['entry_form'] = $value[csf('entry_form')];
			$booking_date_arr[$value[csf('booking_no')]][$value[csf('libfabid')]]['delivery_date'] = $value[csf('delivery_date')];
			$booking_date_arr[$value[csf('booking_no')]][$value[csf('libfabid')]]['fabric'] = $value[csf('fabric_description')];
			
			$po_data[$value[csf('booking_no')]]['porecdate'].=$value[csf('po_received_date')].',';
			$po_data[$value[csf('booking_no')]]['poshipdate'].=$value[csf('pub_shipment_date')].',';
			$booking_date_arr[$value[csf('booking_no')]][$value[csf('libfabid')]]['booking_amount'] += $value[csf('booking_amount')];
			$booking_date_arr[$value[csf('booking_no')]][$value[csf('libfabid')]][$value[csf('uom')]]+= $value[csf('booking_qty')];
			
			$booking_date_arr[$value[csf('booking_no')]][$value[csf('libfabid')]]['responsible_person'][$value[csf('responsible_person')]]= $value[csf('responsible_person')];
			$booking_date_arr[$value[csf('booking_no')]][$value[csf('libfabid')]]['reason'][$value[csf('reason')]]= $value[csf('reason')];
	
			/*if(!empty($value[csf('entry_form')]));
			{
				if(!empty($value[csf('po_break_down_id')]))
				{
	
					$po_break_down_ids.=$value[csf('po_break_down_id')].",";
				}
			}*/
	   }
	   /*$po_break_down_ids=chop($po_break_down_ids,",");
	   $po_ids= explode(",", $po_break_down_ids);*/
	   
	   /*if(count($po_ids))
	   {
			$sql_po="SELECT id, po_received_date from wo_po_break_down where status_active=1 ". where_con_using_array($po_ids,0,"id");
		   // echo $sql_po;
			$po_res=sql_select($sql_po);
			$po_data=array();
			foreach ($po_res as  $value) {
				$po_data[$value[csf('id')]]['porecdate']=$value[csf('po_received_date')];
			}
		}*/
		$nameArray_approved = sql_select("select e.booking_no as booking_no,max(b.approved_date) as last_approve_date,min(b.approved_date) as first_approve_date from wo_booking_mst e, approval_history b where e.id=b.mst_id and b.entry_form in(12,13,7) $company_cond $buyerCond $type_cond $fabric_source_Cond $booking_date_cond $party_cond $pay_cond $approval_cond group by e.booking_no ");
		foreach($nameArray_approved as $row)
		{
			$approve_data_arr[$row[csf('booking_no')]]['last_approve_date']=$row[csf('last_approve_date')];
			$approve_data_arr[$row[csf('booking_no')]]['first_approve_date']=$row[csf('first_approve_date')];
		}
		/* echo '<pre>';
		print_r($approve_data_arr); die;*/
	
		ob_start();
		?>
		<div align="center">
			<table width="2700px" cellspacing="0">
				<tr class="form_caption" style="border:none;">
					<td colspan="26" align="center" style="border:none;font-size:14px; font-weight:bold" ><? echo $report_title; ?></td>
				</tr>
				<tr style="border:none;">
					<td colspan="26" align="center" style="border:none; font-size:16px; font-weight:bold"><? echo $companyArr[$cbo_company_id]; ?></td>
				</tr>
			</table>
			<table width="2700px" cellspacing="0" border="1" class="rpt_table" rules="all">
				<thead>
					<tr>
						<th style="word-wrap: break-word;" width="30">SL.</th>
						<th style="word-wrap: break-word;" width="100">Company</th>
						<th style="word-wrap: break-word;" width="100">Booking Number</th>
						<th style="word-wrap: break-word;" width="70">Booking Insert Date</th>
						<th style="word-wrap: break-word;" width="70">1st Appv. Date</th>
						<th style="word-wrap: break-word;" width="70">Last Appv. Date</th>
						<th style="word-wrap: break-word;" width="70">Po Received Date</th>
                        <th style="word-wrap: break-word;" width="70">1st Ship Date</th>
                        <th style="word-wrap: break-word;" width="70">Last Ship Date</th>
                        <th style="word-wrap: break-word;" width="70">Delivery Date</th>
						<th style="word-wrap: break-word;" width="100">Booking Type</th>
						<th style="word-wrap: break-word;" width="100">Short Booking Type</th>
						<th style="word-wrap: break-word;" width="100">Buyer</th>
						<th style="word-wrap: break-word;" width="100">Buyer Client</th>
						<th style="word-wrap: break-word;" width="100">Style Ref.</th>
						<th style="word-wrap: break-word;" width="100">Product Dept.</th>
						<th style="word-wrap: break-word;" width="100">Job</th>
						<th style="word-wrap: break-word;" width="100">Fabric Nature</th>
						<th style="word-wrap: break-word;" width="100">Fabric Source</th>
						<th style="word-wrap: break-word;" width="100">Source</th>
						<th style="word-wrap: break-word;" width="100">Paymode</th>
						<th style="word-wrap: break-word;" width="100">Party Name</th>
                        <th style="word-wrap: break-word;" width="250">Fabrication</th>
						<th style="word-wrap: break-word;" width="100">Booking Qty (Kg)</th>
						<th style="word-wrap: break-word;" width="100">Booking Qty (Yds)</th>
						<th style="word-wrap: break-word;" width="100">Booking Qty (Mtr)</th>
						<th style="word-wrap: break-word;">Booking Amount ($)</th>
					 </tr>
				</thead>
			</table>
			<div style="width:2700px; max-height:600px; overflow-y:scroll" id="scroll_body"> 
			<table width="2682px" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_body">
				<tbody>
					<? $i=1; $tot_rows=0;
					$total_booking_qty_kg =0; $total_booking_qty_mtr =0; $total_booking_qty_yds= 0;
					foreach ($booking_date_arr as $bookingno=>$bookingdata) 
					{
						foreach ($bookingdata as $fabid=>$row)
						{
							//echo $row[12]; die;
							$booking_qty_kg = 0; $booking_qty_mtr=0; $booking_qty_yds = 0;
							 if(array_key_exists(12, $row)){
								$booking_qty_kg = $row[12];
							 }
							 if(array_key_exists(23, $row)){
								$booking_qty_mtr = $row[23];
							 }
							 if(array_key_exists(27, $row)){
								$booking_qty_yds = $row[27];
							 }
							 if($row['pay_mode']==3 || $row['pay_mode']==5){
								$party_name = $companyArr[$row['supplier_id']];
							 }
							 else{
								$party_name = $supplierArr[$row['supplier_id']];
							 }
							 $total_booking_qty_kg += $booking_qty_kg;
							 $total_booking_qty_mtr += $booking_qty_mtr;
							 $total_booking_qty_yds += $booking_qty_yds;
							 $total_booking_amount = $row['booking_amount'];
			
							 if($row['entry_form'] == ''){
								$entry_form = 0;
							 }
							 else{
								$entry_form = $row['entry_form'];
							 }
							$is_approved=$row['is_approved'];
							$first_approve_dateTime=$last_approve_dateTime='';
						   
							$first_approve_dateTime=$approve_data_arr[$bookingno]['first_approve_date'];
							$last_approve_dateTime=$approve_data_arr[$bookingno]['last_approve_date'];
							//$approve_dateTimeArr=explode(" ",$approve_dateTime);
							$first_approve_dateTimeArr=explode(" ",$first_approve_dateTime);
							$last_approve_dateTimeArr=explode(" ",$last_approve_dateTime);
								//$booking_app_in_date=$insert_date_in_dateArr[0];
							
							  //  $booking_up_dateArr=explode(" ",$row['update_date']);
						
							$last_approve_date=$first_approve_date="";
							if(count($first_approve_dateTimeArr))
							{
								$first_approve_date=$first_approve_dateTimeArr[0];
							}
							if(count($last_approve_dateTimeArr))
							{
								$last_approve_date=$last_approve_dateTimeArr[0];
							}
			
							/*$po_received_date="";
							$po_br_ids=$row['po_break_down_id'];
							if(!empty($po_br_ids))
							{
							   $po_br_idss= explode(",", $po_br_ids);
							   $po_received_date=$po_data[min($po_br_idss)];
							}*/
							
							$po_received_date=min(array_unique(array_filter(explode(",",$po_data[$bookingno]['porecdate']))));
							$first_ship_date=min(array_unique(array_filter(explode(",",$po_data[$bookingno]['poshipdate']))));
							$last_ship_date=max(array_unique(array_filter(explode(",",$po_data[$bookingno]['poshipdate']))));
							$dates=array_unique(array_filter(explode(",",$po_data[$bookingno]['poshipdate'])));
							$fs_date='';
							$ls_date='';
							foreach ($dates as $key => $val) {
								   if(empty($fs_date))
								   {
								   	 $fs_date=strtotime('21-01-2025');
								   }
								  $curDate = strtotime($val);
								  if ($curDate > $ls_date) {
								     $ls_date = $curDate;
								  }
								  if ($curDate < $fs_date) {
								     $fs_date = $curDate;
								  }

							}
							 // echo "<pre>";
							 // print_r(array_unique(array_filter(explode(",",$po_data[$bookingno]['poshipdate']))));
							 // echo "</pre>";
							// echo $first_ship_date."_".$last_ship_date.";".date('d-M-y',$fs_date)."_".date('d-M-y',$ls_date)."<br>";
							?>
							<tr bgcolor="<?=$bgcolor;?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>');" id="tr_<?=$i; ?>">
								<td width="30"><?=$i; ?></td>
								<td width="100" style="word-wrap: break-word;"><?=$companyArr[$row['company_id']]; ?></td>
								<td width="100" style="word-wrap: break-word;" title="<?=$is_approved; ?>"><?=$bookingno; ?></td>
								<td width="70" title="<?=$row['booking_date']; ?>"><?=change_date_format($row['booking_date'], "d-M-y", "-", 1); ?></td>
								<td width="70" style="word-wrap: break-word;" title="<?=$first_approve_dateTime; ?>"><?=change_date_format($first_approve_date, "d-M-y", "-", 1); ?> </td>
								<td width="70" style="word-wrap: break-word;" title="<?=$last_approve_dateTime;?>"><?=change_date_format($last_approve_date, "d-M-y", "-", 1); ?></td>
								<td width="70"><?=change_date_format($po_received_date, "d-M-y", "-", 1); ?></td>
								<td width="70"><? /*echo change_date_format($first_ship_date, "d-M-y", "-", 1);*/ echo date('d-M-y',$fs_date); ?></td>
								<td width="70"><? /*=change_date_format($last_ship_date, "d-M-y", "-", 1);*/ echo date('d-M-y',$ls_date) ?></td>
								<td width="70"><?=change_date_format($row['delivery_date'], "d-M-y", "-", 1); ?></td>
								<td width="100" style="word-wrap: break-word;"><?=$fabric_booking_type[$entry_form]; ?></td>
								<td width="100" style="word-wrap: break-word;"><?=$short_booking_type[$row['short_booking_type']]; ?></td>
								<td width="100" style="word-wrap: break-word;"><?=$buyerArr[$row['buyer_id']]; ?></td>
								<td width="100" style="word-wrap: break-word;"><?=$buyerArr[$row['client_id']]; ?></td>
								<td width="100" style="word-wrap: break-word;"><?=$row['style_ref_no']; ?></td>
								<td width="100" style="word-wrap: break-word;"><?=$row['product_dept']; ?></td>
								<td width="100" style="word-wrap: break-word;"><?=$row['job_no']; ?></td>
								<td width="100" style="word-wrap: break-word;"><?=$item_category[$row['item_category']]; ?></td>
								<td width="100" style="word-wrap: break-word;"><?=$fabric_source[$row['fabric_source']] ?></td>
								<td width="100" style="word-wrap: break-word;"><?=$source[$row['source']]; ?></td>
								<td width="100" style="word-wrap: break-word;"><?=$pay_mode[$row['pay_mode']]; ?></td>
								<td width="100" style="word-wrap: break-word;"><?=$party_name; ?></td>
								<td width="250" style="word-wrap: break-word;"><?=$row['fabric']; ?></td>
								<td width="100" align="right"><?=number_format($booking_qty_kg,2); ?></td>
								<td width="100" align="right"><?=number_format($booking_qty_yds,2); ?></td>
								<td width="100" align="right"><?=number_format($booking_qty_mtr,2); ?></td>
								<td align="right"><?=number_format($row['booking_amount'],2); ?></td>
							</tr>
			
							<? $i++; $tot_rows++;
						}
					} ?>
				</tbody>
			</table>
            </div>
			<table width="2700px" cellspacing="0" border="1" class="tbl_bottom" rules="all">
				<tfoot>
					<tr>
						<td width="30">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="100">&nbsp;</td>
		
						<td width="70">&nbsp;</td>
						<td width="70">&nbsp;</td>
						<td width="70">&nbsp;</td>
	                    <td width="70">&nbsp;</td>
						<td width="70">&nbsp;</td>
						<td width="70">&nbsp;</td>
	                    <td width="70">&nbsp;</td>
		
						<td width="100">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="100">&nbsp;</td>
	                    <td width="250">&nbsp;</td>
						<td width="100" id="value_qtykg" align="right"><? echo number_format($total_booking_qty_kg,2); ?></td>
						<td width="100" id="value_qtyyds" align="right"><? echo number_format($total_booking_qty_yds,2); ?></td>
						<td width="100" id="value_qtymtr" align="right"><? echo number_format($total_booking_qty_mtr,2); ?></td>
						<td id="value_amount" align="right"><? echo number_format($total_booking_amount,2); ?></td>
					</tr>
				</tfoot>
			</table>
		</div>
		<?
	}
	else if($type==3)//Color Wise Button
	{
		if($cbo_booking_type == 1 || $cbo_booking_type == 2 || $cbo_booking_type == 3 || $cbo_booking_type == 4 || $cbo_booking_type == 0){
			if($cbo_booking_type == 1 || $cbo_booking_type == 2) $type_cond = " and e.booking_type=1 and e.is_short=2 and e.entry_form in (118,86, 108,271)";
			if($cbo_booking_type == 3) $type_cond = " and e.booking_type=1 and e.is_short=1 and e.entry_form in(88,275)";
			if($cbo_booking_type == 4) $type_cond = "and e.booking_type=4 and e.is_short=2";

			if($cbo_booking_type == 0) $type_cond = "and e.entry_form in (118,108,88,89,90,86,271,275)";
			if($cbo_company_id) $company_cond=" and e.company_id in($cbo_company_id)"; else $company_cond="";
			if($cbo_party_id) $party_cond=" and e.supplier_id in($cbo_party_id)"; else $party_cond="";
			if($cbo_pay_mode) $pay_cond=" and e.pay_mode in($cbo_pay_mode)"; else $pay_cond="";
			if($cbo_buyer_id) $buyerCond=" and e.buyer_id in($cbo_buyer_id)"; else $buyerCond="";
			if($cbo_fabric_source) $fabric_source_Cond=" and e.fabric_source in($cbo_fabric_source)"; else $fabric_source_Cond="";
			
			if($based_on==1){
				if($txt_date_from!="" && $txt_date_to!="") $booking_date_cond="and e.booking_date between '$txt_date_from' and '$txt_date_to'"; else $booking_date_cond="";
			}
			else{
				if($txt_date_from!="" && $txt_date_to!="") $booking_date_cond="and c.pub_shipment_date between '$txt_date_from' and '$txt_date_to'"; else $booking_date_cond="";
			}
	
			if($txt_booking_no_id!='')
			{
				if ($txt_booking_no_id=="") $bookingCond=""; else $bookingCond=" and e.id in ( $txt_booking_no_id )";
			}
			else
			{
				if ($txt_booking_no=="") $bookingCond=""; else $bookingCond=" and e.booking_no_prefix_num='$txt_booking_no'";
			}
			$item_category_cond='';
			if(!empty($cbofabricnature))
			{
				$item_category_cond=" and e.item_category in($cbofabricnature)";
			}
	
		    $get_booking = "Select a.job_no, a.style_ref_no, a.client_id, e.booking_no, e.company_id, e.booking_date, a.insert_date, e.update_date, e.is_approved, e.is_short, e.short_booking_type, e.booking_type, e.buyer_id, e.item_category, e.fabric_source, e.source, e.pay_mode, e.supplier_id, e.entry_form, e.delivery_date, f.fin_fab_qnty as booking_qty, f.fabric_color_id, f.gmts_color_id, f.responsible_person, f.reason, d.gsm_weight as gsm_weight, f.dia_width, f.amount as booking_amount, d.uom, d.lib_yarn_count_deter_id as libfabid, d.fabric_description, d.item_number_id, d.color_type_id, d.costing_per, d.budget_on, avg(g.cons) as avg_cons, d.avg_finish_cons, c.id as po_id, c.po_received_date, c.pub_shipment_date ,a.product_dept, a.pro_sub_dep from wo_booking_dtls f join wo_booking_mst e on f.booking_no=e.booking_no join wo_pre_cost_fabric_cost_dtls d on d.id=f.pre_cost_fabric_cost_dtls_id join wo_po_break_down c on d.job_no=c.job_no_mst and f.po_break_down_id=c.id join wo_po_details_master a on a.job_no = c.job_no_mst join wo_pre_cos_fab_co_avg_con_dtls g on d.id=g.pre_cost_fabric_cost_dtls_id and c.id=f.po_break_down_id and g.cons>0 where f.status_active=1 and f.is_deleted=0  and d.is_deleted=0 and d.status_active=1 and a.is_deleted=0 and a.status_active=1 and e.is_deleted=0 and e.status_active=1 and g.is_deleted=0 and g.status_active=1 $company_cond $buyerCond $type_cond $fabric_source_Cond $booking_date_cond $party_cond $pay_cond $item_category_cond $bookingCond group by a.job_no, a.style_ref_no, a.client_id, e.booking_no, e.company_id, e.booking_date, a.insert_date, e.update_date, e.is_approved, e.is_short, e.short_booking_type, e.booking_type, e.buyer_id, e.item_category, e.fabric_source, e.source, e.pay_mode, e.supplier_id, e.entry_form, e.delivery_date, f.fin_fab_qnty, f.fabric_color_id, f.gmts_color_id, f.responsible_person, f.reason, d.gsm_weight, f.dia_width, f.amount, d.uom, d.lib_yarn_count_deter_id, d.fabric_description, d.item_number_id, d.color_type_id, d.costing_per, d.budget_on, d.avg_finish_cons, c.id, c.po_received_date, c.pub_shipment_date  ,a.product_dept, a.pro_sub_dep order by e.booking_no asc";
			//echo "10**".$get_booking; die;
		}
		else{
			if($cbo_booking_type == 5) $type_cond = " and a.booking_type=4 and a.entry_form_id in (90,140,610,439,139)";
			if($cbo_company_id) $company_cond=" and a.company_id in($cbo_company_id)"; else $company_cond="";
			if($cbo_party_id) $party_cond=" and a.supplier_id in($cbo_party_id)"; else $party_cond="";
			if($cbo_pay_mode) $pay_cond=" and a.pay_mode in($cbo_pay_mode)"; else $pay_cond="";
			if($cbo_buyer_id) $buyerCond=" and a.buyer_id in($cbo_buyer_id)"; else $buyerCond="";
			if($cbo_fabric_source) $fabric_source_Cond=" and a.fabric_source in($cbo_fabric_source)"; else $fabric_source_Cond="";
			if($txt_date_from!="" && $txt_date_to!="") $booking_date_cond="and a.booking_date between '$txt_date_from' and '$txt_date_to'"; else $booking_date_cond="";
			if($txt_booking_no_id!='')
			{
				if ($txt_booking_no_id=="") $bookingCond=""; else $bookingCond=" and a.id in ( $txt_booking_no_id )";
			}
			else
			{
				if ($txt_booking_no=="") $bookingCond=""; else $bookingCond=" and a.booking_no_prefix_num='$txt_booking_no'";
			}
			$item_category_cond='';
			if(!empty($cbofabricnature))
			{
				$item_category_cond=" and e.item_category in($cbofabricnature)";
			}
			
			$get_booking = "Select a.booking_no, a.booking_date,a.company_id, a.update_date, a.insert_date, a.is_approved, a.is_short, '0' as short_booking_type, a.buyer_id, '' as job_no, '' as style_ref_no, '' as client_id, '' as garments_nature, a.fabric_source, a.source, a.pay_mode, a.supplier_id, b.uom,b.color_type_id as color_type, sum(b.grey_fabric) as booking_qty,b.fabric_color as fabric_color_id, sum(b.amount) as booking_amount, null as gsm_weight,b.dia as dia_width, a.entry_form_id as entry_form, a.po_break_down_id from wo_non_ord_samp_booking_mst a join wo_non_ord_samp_booking_dtls b on a.booking_no=b.booking_no where a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $company_cond $buyerCond $fabric_source_Cond $booking_date_cond $type_cond $party_cond $pay_cond $bookingCond $item_category_cond group by a.booking_no,a.company_id, a.booking_date, a.is_short, a.buyer_id, a.fabric_source, a.update_date, a.insert_date, a.is_approved, a.source, a.pay_mode,b.dia, a.supplier_id, b.color_type_id,b.uom, a.entry_form_id, b.fabric_color,a.po_break_down_id order by a.booking_no asc";
			$nameArray_approved_non = sql_select("select a.booking_no as booking_no,max(b.approved_no) as approved_no, max(b.approved_date) as last_approve_date, min(b.approved_date) as first_approve_date from wo_non_ord_samp_booking_mst a, approval_history b where a.id=b.mst_id and b.entry_form in(9) $company_cond $buyerCond $fabric_source_Cond $booking_date_cond $type_cond $party_cond $pay_cond $approval_cond  group by a.booking_no ");
			foreach($nameArray_approved_non as $row)
			{
				$approve_data_arr[$row[csf('booking_no')]]['first_approve_date']=$row[csf('first_approve_date')];
				$approve_data_arr[$row[csf('booking_no')]]['last_approve_date']=$row[csf('last_approve_date')];
				$approve_data_arr[$row[csf('booking_no')]]['approved_no']=$row[csf('approved_no')];
			}
		}
		//echo $get_booking;
		$sql_data=sql_select($get_booking);
		
		$booking_date_arr =array(); $po_data=array();
		$po_break_down_ids="";
		foreach ($sql_data as $value) {
			$fab_color_id=$value[csf('fabric_color_id')];
			
			$costingperQty=$avgFinConsPcs=$avgFinConsDzn=0;
			if($value[csf('costing_per')]==1) $costingperQty=12;
			else if($value[csf('costing_per')]==2) $costingperQty=1;
			else if($value[csf('costing_per')]==3) $costingperQty=24;
			else if($value[csf('costing_per')]==4) $costingperQty=36;
			else if($value[csf('costing_per')]==5) $costingperQty=48;
			
			$avgFinConsPcs=$value[csf('avg_finish_cons')]/$costingperQty;
			$avgFinConsDzn=$avgFinConsPcs*12;
			
			$bookingType="";
			
			if($value[csf('booking_type')]==4) $bookingType="Sample Fabric Booking - With Order";
			else if($value[csf('booking_type')]==1 && $value[csf('is_short')]==1 && $value[csf('entry_form')]==88) $bookingType="Short Fabric Booking"; 
			else if($value[csf('booking_type')]==1 && $value[csf('is_short')]==2 && $value[csf('entry_form')]==108) $bookingType="Partial Fabric Booking"; 
			else if($value[csf('booking_type')]==1 && $value[csf('is_short')]==2 && $value[csf('entry_form')]==118) $bookingType="Main Fabric Booking"; 
			else if($value[csf('short_booking_type')]=='0') $bookingType="Sample Fabric Booking - Without Order"; 
			
			$booking_date_arr[$value[csf('booking_no')]][$value[csf('libfabid')]][$fab_color_id]['booking_no'] = $value[csf('booking_no')];
			$booking_date_arr[$value[csf('booking_no')]][$value[csf('libfabid')]][$fab_color_id]['color_type'] = $value[csf('color_type_id')];
			$booking_date_arr[$value[csf('booking_no')]][$value[csf('libfabid')]][$fab_color_id]['company_id'] = $value[csf('company_id')];
			$booking_date_arr[$value[csf('booking_no')]][$value[csf('libfabid')]][$fab_color_id]['update_date'] = $value[csf('update_date')];
			$booking_date_arr[$value[csf('booking_no')]][$value[csf('libfabid')]][$fab_color_id]['insert_date'] = $value[csf('insert_date')];
			$booking_date_arr[$value[csf('booking_no')]][$value[csf('libfabid')]][$fab_color_id]['is_approved'] = $value[csf('is_approved')];
			$booking_date_arr[$value[csf('booking_no')]][$value[csf('libfabid')]][$fab_color_id]['booking_date'] = $value[csf('booking_date')];
			$booking_date_arr[$value[csf('booking_no')]][$value[csf('libfabid')]][$fab_color_id]['booking_type'] = $value[csf('booking_type')];
			$booking_date_arr[$value[csf('booking_no')]][$value[csf('libfabid')]][$fab_color_id]['short_booking_type'] = $bookingType;
			//$booking_date_arr[$value[csf('booking_no')]][$value[csf('libfabid')]][$fab_color_id]['short_booking_type'] = $value[csf('short_booking_type')];
			$booking_date_arr[$value[csf('booking_no')]][$value[csf('libfabid')]][$fab_color_id]['buyer_id'] = $value[csf('buyer_id')];
			$booking_date_arr[$value[csf('booking_no')]][$value[csf('libfabid')]][$fab_color_id]['style_ref_no'] = $value[csf('style_ref_no')];
			$booking_date_arr[$value[csf('booking_no')]][$value[csf('libfabid')]][$fab_color_id]['client_id'] = $value[csf('client_id')];
			$booking_date_arr[$value[csf('booking_no')]][$value[csf('libfabid')]][$fab_color_id]['job_no'] = $value[csf('job_no')];
			$booking_date_arr[$value[csf('booking_no')]][$value[csf('libfabid')]][$fab_color_id]['product_dept'] = $product_dept[$value[csf('product_dept')]];
			$booking_date_arr[$value[csf('booking_no')]][$value[csf('libfabid')]][$fab_color_id]['pro_sub_dep'] = $sub_deptArr[$value[csf('pro_sub_dep')]];
			$booking_date_arr[$value[csf('booking_no')]][$value[csf('libfabid')]][$fab_color_id]['item_category'] = $value[csf('item_category')];
			$booking_date_arr[$value[csf('booking_no')]][$value[csf('libfabid')]][$fab_color_id]['fabric_source'] = $value[csf('fabric_source')];
			$booking_date_arr[$value[csf('booking_no')]][$value[csf('libfabid')]][$fab_color_id]['source'] = $value[csf('source')];
			$booking_date_arr[$value[csf('booking_no')]][$value[csf('libfabid')]][$fab_color_id]['pay_mode'] = $value[csf('pay_mode')];
			$booking_date_arr[$value[csf('booking_no')]][$value[csf('libfabid')]][$fab_color_id]['supplier_id'] = $value[csf('supplier_id')];
			$booking_date_arr[$value[csf('booking_no')]][$value[csf('libfabid')]][$fab_color_id]['entry_form'] = $value[csf('entry_form')];
			$booking_date_arr[$value[csf('booking_no')]][$value[csf('libfabid')]][$fab_color_id]['delivery_date'] = $value[csf('delivery_date')];
			$booking_date_arr[$value[csf('booking_no')]][$value[csf('libfabid')]][$fab_color_id]['fabric'] = $value[csf('fabric_description')];
			$booking_date_arr[$value[csf('booking_no')]][$value[csf('libfabid')]][$fab_color_id]['gsm'] = $value[csf('gsm_weight')];
			$booking_date_arr[$value[csf('booking_no')]][$value[csf('libfabid')]][$fab_color_id]['dia'] = $value[csf('dia_width')];
			$booking_date_arr[$value[csf('booking_no')]][$value[csf('libfabid')]][$fab_color_id]['uom'] = $value[csf('uom')];
			$booking_date_arr[$value[csf('booking_no')]][$value[csf('libfabid')]][$fab_color_id]['consDzn'] = $value[csf('avg_cons')];
			
			//gsm_weight,dia_width
			
			$po_data[$value[csf('booking_no')]]['porecdate'].=$value[csf('po_received_date')].',';
			$po_data[$value[csf('booking_no')]]['poshipdate'].=$value[csf('pub_shipment_date')].',';
			$booking_date_arr2[$value[csf('booking_no')]][$value[csf('libfabid')]][$fab_color_id][$value[csf('uom')]]['booking_amount'] += $value[csf('booking_amount')];
			$booking_date_arr2[$value[csf('booking_no')]][$value[csf('libfabid')]][$fab_color_id][$value[csf('uom')]]['booking_qty']+= $value[csf('booking_qty')];
			//$booking_date_arr[$value[csf('booking_no')]][$value[csf('libfabid')]][$fab_color_id][$value[csf('uom')]]+= $value[csf('booking_qty')];
			
			$booking_date_arr[$value[csf('booking_no')]][$value[csf('libfabid')]][$fab_color_id]['responsible_person'].= $value[csf('responsible_person')].',';
			$booking_date_arr[$value[csf('booking_no')]][$value[csf('libfabid')]][$fab_color_id]['reason'].= $value[csf('reason')].',';
	
			
	   }
	   
		$nameArray_approved = sql_select("select e.booking_no as booking_no,max(b.approved_no) as approved_no,max(b.approved_date) as last_approve_date,min(b.approved_date) as first_approve_date from wo_booking_mst e, approval_history b where e.id=b.mst_id and b.entry_form in(12,13,7) $company_cond $buyerCond $type_cond $fabric_source_Cond $booking_date_cond $party_cond $pay_cond $approval_cond group by e.booking_no ");
		foreach($nameArray_approved as $row)
		{
			$approve_data_arr[$row[csf('booking_no')]]['last_approve_date']=$row[csf('last_approve_date')];
			$approve_data_arr[$row[csf('booking_no')]]['first_approve_date']=$row[csf('first_approve_date')];
			$approve_data_arr[$row[csf('booking_no')]]['approved_no']=$row[csf('approved_no')];
		}
		/* echo '<pre>';
		print_r($approve_data_arr); die;*/
	
		ob_start();
		$width=3260;
		?>
		<div align="center">
			<table width="<?=$width;?>px" cellspacing="0">
				<tr class="form_caption" style="border:none;">
					<td colspan="27" align="center" style="border:none;font-size:14px; font-weight:bold" ><?=$report_title; ?></td>
				</tr>
				<tr style="border:none;">
					<td colspan="27" align="center" style="border:none; font-size:16px; font-weight:bold"><?=$companyArr[$cbo_company_id]; ?></td>
				</tr>
			</table>
			<table width="<?=$width;?>px" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1">
				<thead>
					<tr>
						<th style="word-wrap: break-word;" width="30">SL.</th>
						<th style="word-wrap: break-word;" width="120">Company</th>
                        <th style="word-wrap: break-word;" width="100">Buyer</th>
                        <th style="word-wrap: break-word;" width="100">Buyer Client</th>
						<th style="word-wrap: break-word;" width="100">Style Ref.</th>
						<th style="word-wrap: break-word;" width="100">Product Dept.</th>
						<th style="word-wrap: break-word;" width="100">Sub. Dept.</th>
						<th style="word-wrap: break-word;" width="100">Job</th>
						<th style="word-wrap: break-word;" width="100">Booking Number</th>
                       
                        <th style="word-wrap: break-word;" width="70">Revised No</th>
                        <th style="word-wrap: break-word;" width="100">Booking Type</th>
						<th style="word-wrap: break-word;" width="100">Short Booking Type</th>
						<th style="word-wrap: break-word;" width="100">Fabric Nature</th>
						<th style="word-wrap: break-word;" width="100">Fabric Source</th>
						<th style="word-wrap: break-word;" width="100">Source</th>
						<th style="word-wrap: break-word;" width="100">Paymode</th>
						<th style="word-wrap: break-word;" width="100">Party Name</th>
                        
						<th style="word-wrap: break-word;" width="70">Booking Insert Date</th>
						<th style="word-wrap: break-word;" width="70">1st Appv. Date</th>
						<th style="word-wrap: break-word;" width="70">Last Appv. Date</th>
						<th style="word-wrap: break-word;" width="70">Po Received Date</th>
                        <th style="word-wrap: break-word;" width="70">1st Ship Date</th>
                        <th style="word-wrap: break-word;" width="70">Last Ship Date</th>
                        <th style="word-wrap: break-word;" width="70">Delivery Date</th>
                        <th style="word-wrap: break-word;" width="70">Colot Type</th>
                        <th style="word-wrap: break-word;" width="70">Fabric Color</th>
						
                        <th style="word-wrap: break-word;" width="250">Fabrication</th>
                        <th style="word-wrap: break-word;" width="50">Dia</th>
                        <th style="word-wrap: break-word;" width="50">GSM</th>
                        <th style="word-wrap: break-word;" width="50">Cons /Dzn</th>
                        
						<th style="word-wrap: break-word;" width="100">Booking Qty [Kg]</th>
						<th style="word-wrap: break-word;" width="100">Booking Qty [Yds]</th>
						<th style="word-wrap: break-word;" width="100">Booking Qty [Mtr]</th>
						<th style="word-wrap: break-word;" width="100">Booking Amount [$]</th>
                        <th style="word-wrap: break-word;" width="100">Reason</th>
                        <th style="word-wrap: break-word;" >Responsible Person</th>
					 </tr>
				</thead>
			</table>
			<div style="width:<?=$width+20; ?>px; max-height:600px; overflow-y:scroll" id="scroll_body"> 
			<table width="<?=$width; ?>px" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_body">
				<tbody>
					<? $i=1; $tot_rows=0;
					$total_booking_qty_kg =0; $total_booking_qty_mtr =0; $total_booking_qty_yds= 0;
					foreach ($booking_date_arr as $bookingno=>$bookingdata) 
					{
						foreach ($bookingdata as $fabid=>$fabidData)
						{
						foreach ($fabidData as $colorid=>$row)
						{
							//echo $row[12]; die;
							$uom_id=$row[('uom')];
							$booking_amount=$booking_date_arr2[$bookingno][$fabid][$colorid][$uom_id]['booking_amount'];
							$booking_qty=$booking_date_arr2[$bookingno][$fabid][$colorid][$uom_id]['booking_qty'];
							$booking_qty_kg = 0; $booking_qty_mtr=0; $booking_qty_yds = 0;
							 if($uom_id==12){
								$booking_qty_kg = $booking_qty;
							 }
							 else if($uom_id==23){
								$booking_qty_mtr =$booking_qty;
							 }
							 else if($uom_id==27){
								$booking_qty_yds = $booking_qty;
							 }
							 
							 if($row['pay_mode']==3 || $row['pay_mode']==5){
								$party_name = $companyArr[$row['supplier_id']];
							 }
							 else{
								$party_name = $supplierArr[$row['supplier_id']];
							 }
							 $total_booking_qty_kg += $booking_qty_kg;
							 $total_booking_qty_mtr += $booking_qty_mtr;
							 $total_booking_qty_yds += $booking_qty_yds;
							 $total_booking_amount = $booking_amount;
			
							 if($row['entry_form'] == ''){
								$entry_form = 0;
							 }
							 else{
								$entry_form = $row['entry_form'];
							 }
							$is_approved=$row['is_approved'];
							$first_approve_dateTime=$last_approve_dateTime='';
						   
							$first_approve_dateTime=$approve_data_arr[$bookingno]['first_approve_date'];
							$last_approve_dateTime=$approve_data_arr[$bookingno]['last_approve_date'];
							//$approve_dateTimeArr=explode(" ",$approve_dateTime);
							$first_approve_dateTimeArr=explode(" ",$first_approve_dateTime);
							$last_approve_dateTimeArr=explode(" ",$last_approve_dateTime);
								//$booking_app_in_date=$insert_date_in_dateArr[0];
							
							  //  $booking_up_dateArr=explode(" ",$row['update_date']);
						
							$last_approve_date=$first_approve_date="";
							if(count($first_approve_dateTimeArr))
							{
								$first_approve_date=$first_approve_dateTimeArr[0];
							}
							if(count($last_approve_dateTimeArr))
							{
								$last_approve_date=$last_approve_dateTimeArr[0];
							}							
							$po_received_date=min(array_unique(array_filter(explode(",",$po_data[$bookingno]['porecdate']))));
							$first_ship_date=min(array_unique(array_filter(explode(",",$po_data[$bookingno]['poshipdate']))));
							$last_ship_date=max(array_unique(array_filter(explode(",",$po_data[$bookingno]['poshipdate']))));
							$dates=array_unique(array_filter(explode(",",$po_data[$bookingno]['poshipdate'])));
							$fs_date='';
							$ls_date='';
							foreach ($dates as $key => $val) {
								   if(empty($fs_date))
								   {
								   	 $fs_date=strtotime('21-01-2025');
								   }
								  $curDate = strtotime($val);
								  if ($curDate > $ls_date) {
								     $ls_date = $curDate;
								  }
								  if ($curDate < $fs_date) {
								     $fs_date = $curDate;
								  }

							}
							$revised_no=$approve_data_arr[$bookingno]['approved_no'];
							if($revised_no>1)
							{
								$app_revised_no=$revised_no-1;
							}
							 // echo "<pre>";
							 // print_r(array_unique(array_filter(explode(",",$po_data[$bookingno]['poshipdate']))));
							 // echo "</pre>";
							// echo $first_ship_date."_".$last_ship_date.";".date('d-M-y',$fs_date)."_".date('d-M-y',$ls_date)."<br>";
							$reason=rtrim($row['reason'],',');
							$reason_val=implode(",",array_unique(explode(",",$reason)));
							$responsible_person=rtrim($row['responsible_person'],',');
							$responsible_person_val=implode(",",array_unique(explode(",",$responsible_person)));
							?>
							<tr bgcolor="<?=$bgcolor;?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>');" id="tr_<?=$i; ?>">
								<td width="30"><?=$i; ?></td>
								<td width="120" style="word-wrap: break-word;"><?=$companyArr[$row['company_id']]; ?></td>
	                            <td width="100" style="word-wrap: break-word;"><?=$buyerArr[$row['buyer_id']]; ?></td>
	                            <td width="100" style="word-wrap: break-word;"><?=$buyerArr[$row['client_id']]; ?></td>
								<td width="100" style="word-wrap: break-word;"><?=$row['style_ref_no']; ?></td>
								<td width="100" style="word-wrap: break-word;"><?=$row['product_dept']; ?></td>
								<td width="100" style="word-wrap: break-word;"><?=$row['pro_sub_dep']; ?></td>
								<td width="100" style="word-wrap: break-word;"><?=$row['job_no']; ?></td>
								<td width="100" style="word-wrap: break-word;" title="<?=$is_approved; ?>"><?=$bookingno; ?></td>
	                        
	                            <td width="70" style="word-wrap: break-word;" title="<? //$is_approved; ?>"><?=$app_revised_no; ?></td>
	                            <td width="100" style="word-wrap: break-word;"><?=$fabric_booking_type[$entry_form]; ?></td>
								<td width="100" style="word-wrap: break-word;"><?=$row['short_booking_type']; ?></td>
								
								<td width="100" style="word-wrap: break-word;"><?=$item_category[$row['item_category']]; ?></td>
								<td width="100" style="word-wrap: break-word;"><?=$fabric_source[$row['fabric_source']]; ?></td>
								<td width="100" style="word-wrap: break-word;"><?=$source[$row['source']]; ?></td>
								<td width="100" style="word-wrap: break-word;"><?=$pay_mode[$row['pay_mode']]; ?></td>
								<td width="100" style="word-wrap: break-word;"><?=$party_name; ?></td>
	                            
								<td width="70" title="<?=$row['booking_date']; ?>"><?=change_date_format($row['booking_date'], "d-M-y", "-", 1); ?></td>
								<td width="70" style="word-wrap: break-word;" title="<?=$first_approve_dateTime; ?>"><?=change_date_format($first_approve_date, "d-M-y", "-", 1); ?> </td>
								<td width="70" style="word-wrap: break-word;" title="<?=$last_approve_dateTime;?>"><?=change_date_format($last_approve_date, "d-M-y", "-", 1); ?></td>
								<td width="70"><?=change_date_format($po_received_date, "d-M-y", "-", 1); ?></td>
								<td width="70"><? /*echo change_date_format($first_ship_date, "d-M-y", "-", 1);*/ echo date('d-M-y',$fs_date); ?></td>
								<td width="70"><? /*=change_date_format($last_ship_date, "d-M-y", "-", 1);*/ echo date('d-M-y',$ls_date) ?></td>
								<td width="70"><?=change_date_format($row['delivery_date'], "d-M-y", "-", 1); ?></td>
	                             <td width="70"><?=$color_type[$row['color_type']]; ?></td>
	                            <td width="70"><?=$colorArr[$colorid]; ?></td>
								
								<td width="250" style="word-wrap: break-word;"><?=$row['fabric']; ?></td>
	                            <td width="50"><?=$row['dia']; ?></td>
	                            <td width="50"><?=$row['gsm']; ?></td>
	                            <td width="50" align="right"><?=number_format($row['consDzn'],4); ?></td>
	                            
								<td width="100" align="right"><?=number_format($booking_qty_kg,2); ?></td>
								<td width="100" align="right"><?=number_format($booking_qty_yds,2); ?></td>
								<td width="100" align="right"><?=number_format($booking_qty_mtr,2); ?></td>
								<td width="100" align="right"><?=number_format($booking_amount,2); ?></td>
	                            <td width="100" align="center"><? echo $reason_val; ?></td>
								<td width="" align="center"><? echo $responsible_person_val; ?></td>
							</tr>
			
							<? $i++; $tot_rows++;
							}
						}
					} ?>
				</tbody>
			</table>
            </div>
			<table width="<?=$width; ?>px" cellspacing="0" border="1" class="tbl_bottom" rules="all">
				<tfoot>
					<tr>
						<td width="30">&nbsp;</td>
	                    <td width="120">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="100">&nbsp;</td>
	                    
						<td width="100">&nbsp;</td>
						<td width="100">&nbsp;</td>
	                    
	                    <td width="70">&nbsp;</td>
	                    <td width="100">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="100">&nbsp;</td>
		
						<td width="70">&nbsp;</td>
						<td width="70">&nbsp;</td>
						<td width="70">&nbsp;</td>
	                    <td width="70">&nbsp;</td>
						<td width="70">&nbsp;</td>
						<td width="70">&nbsp;</td>
	                    <td width="70">&nbsp;</td>
	                    <td width="70">&nbsp;</td>
	                    <td width="70">&nbsp;</td>
						
	                    <td width="250">&nbsp;</td>
	                    <td width="50">&nbsp;</td>
	                    <td width="50">&nbsp;</td>
	                    <td width="50">&nbsp;</td>
						<td width="100" id="value_qtykg" align="right"><? echo number_format($total_booking_qty_kg,2); ?></td>
						<td width="100" id="value_qtyyds" align="right"><? echo number_format($total_booking_qty_yds,2); ?></td>
						<td width="100" id="value_qtymtr" align="right"><? echo number_format($total_booking_qty_mtr,2); ?></td>
						<td width="100" id="value_amount" align="right"><? echo number_format($total_booking_amount,2); ?></td>
	                    <td width="100">&nbsp;</td>
	                    <td width="">&nbsp;</td>
					</tr>
				</tfoot>
			</table>
		</div>
		<?
	  	
	}
	else if($type==4)//show 2 Button /md mamun ahmed sagor /28723/15-12-2022
	{
		$date_from=str_replace("'","",$txt_date_from);
		$date_to=str_replace("'","",$txt_date_to);
		$lib_user = return_library_array("select id,user_name from user_passwd","id","user_name");
		if($cbo_booking_type == 1 || $cbo_booking_type == 2 || $cbo_booking_type == 3 || $cbo_booking_type == 4 || $cbo_booking_type == 0){
			if($cbo_booking_type == 1 || $cbo_booking_type == 2) $type_cond = " and e.booking_type=1 and e.is_short=2 and e.entry_form in (118,86, 108,271)";
			if($cbo_booking_type == 3) $type_cond = " and e.booking_type=1 and e.is_short=1 and e.entry_form in(88,275)";
			if($cbo_booking_type == 4) $type_cond = "and e.booking_type=4 and e.is_short=2";

			if($cbo_booking_type == 0) $type_cond = "and e.entry_form in (118,108,88,89,90,86,271,275)";
			if($cbo_company_id) $company_cond=" and e.company_id in($cbo_company_id)"; else $company_cond="";
			if($cbo_company_id) $company_cond2=" and e.company_id in($cbo_company_id)"; else $company_con2="";
			if($cbo_party_id) $party_cond=" and e.supplier_id in($cbo_party_id)"; else $party_cond="";
			if($cbo_pay_mode) $pay_cond=" and e.pay_mode in($cbo_pay_mode)"; else $pay_cond="";
			if($cbo_buyer_id) $buyerCond=" and e.buyer_id in($cbo_buyer_id)"; else $buyerCond="";
			if($cbo_fabric_source) $fabric_source_Cond=" and e.fabric_source in($cbo_fabric_source)"; else $fabric_source_Cond="";
			if($based_on==3 || $based_on==4){
				if($txt_date_from!="" && $txt_date_to!="") $booking_date_cond2.=" and b.approved_date between '".$txt_date_from."' and '".$txt_date_to." 11:59:59 PM'"; else $booking_date_cond2="";
			}else{
				if($txt_date_from!="" && $txt_date_to!="") $booking_date_cond="and e.booking_date between '$txt_date_from' and '$txt_date_to'"; else $booking_date_cond="";
			}
			
	
			if($txt_booking_no_id!='')
			{
				if ($txt_booking_no_id=="") $bookingCond=""; else $bookingCond=" and e.id in ( $txt_booking_no_id )";
			}
			else
			{
				if ($txt_booking_no=="") $bookingCond=""; else $bookingCond=" and e.booking_no like '%$txt_booking_no%'";
			}


			$item_category_cond='';
			if(!empty($cbofabricnature))
			{
				$item_category_cond=" and e.item_category in($cbofabricnature)";
			}
			if($based_on==3){
				$get_booking = "Select e.booking_no, e.booking_date,e.company_id, a.insert_date, e.update_date, e.is_approved, e.is_short, e.short_booking_type, e.buyer_id, a.job_no, a.style_ref_no, a.client_id, e.item_category, e.fabric_source, e.source, e.pay_mode, e.supplier_id, sum(f.grey_fab_qnty) as booking_qty, sum(f.fin_fab_qnty) as fin_fab_qnty, f.responsible_person, f.reason, sum(f.amount) as booking_amount, d.uom, g.grouping, e.entry_form, nvl(e.po_break_down_id,f.po_break_down_id) as po_break_down_id,a.product_dept,g.shipment_date,e.booking_type,e.inserted_by,e.ready_to_approved,c.costing_date,c.costing_per,a.buyer_name,MIN(b.approved_date) as first_approval_date,MAX(b.approved_date) as last_approval_date,b.current_approval_status,max(b.id) as appId from wo_booking_dtls f join wo_booking_mst e on f.booking_no=e.booking_no join wo_pre_cost_fabric_cost_dtls d on d.id=f.pre_cost_fabric_cost_dtls_id join wo_pre_cost_mst c on d.job_no=c.job_no join wo_po_details_master a on a.job_no = c.job_no  join approval_history b on e.id = b.mst_id join wo_pre_cost_fabric_cost_dtls d on d.id=f.pre_cost_fabric_cost_dtls_id join wo_po_break_down g on f.po_break_down_id=g.id where f.status_active=1 and f.is_deleted=0  and d.is_deleted=0 and d.status_active=1 and a.is_deleted=0 and a.status_active=1 and e.is_deleted=0 and e.status_active=1 and g.is_deleted=0 and g.status_active=1  and b.approved=1 and b.current_approval_status =1 $company_cond $buyerCond $type_cond $fabric_source_Cond $booking_date_cond2 $party_cond $pay_cond $bookingCond $item_category_cond group by e.booking_no, e.booking_date, e.is_short, e.short_booking_type, e.buyer_id, a.job_no, e.update_date, e.is_approved, a.style_ref_no, a.client_id, e.item_category,e.company_id, e.fabric_source, e.source, e.pay_mode, e.supplier_id, f.responsible_person, f.reason, d.uom, e.entry_form, a.insert_date, e.po_break_down_id, f.po_break_down_id, g.grouping, a.product_dept,g.shipment_date,e.booking_type,e.inserted_by,e.ready_to_approved,c.costing_date,c.costing_per,a.buyer_name,b.current_approval_status  order by e.booking_date asc";
			}else if($based_on==4){
				$get_booking = "Select e.booking_no, e.booking_date,e.company_id, a.insert_date, e.update_date, e.is_approved, e.is_short, e.short_booking_type, e.buyer_id, a.job_no, a.style_ref_no, a.client_id, e.item_category, e.fabric_source, e.source, e.pay_mode, e.supplier_id, sum(f.grey_fab_qnty) as booking_qty, sum(f.fin_fab_qnty) as fin_fab_qnty, f.responsible_person, f.reason, sum(f.amount) as booking_amount, d.uom, g.grouping, e.entry_form, nvl(e.po_break_down_id,f.po_break_down_id) as po_break_down_id,a.product_dept,g.shipment_date,e.booking_type,e.inserted_by,e.ready_to_approved,c.costing_date,c.costing_per,a.buyer_name,MIN(b.approved_date) as first_approval_date,MAX(b.approved_date) as last_approval_date,b.current_approval_status,max(b.id) as appId from wo_booking_dtls f join wo_booking_mst e on f.booking_no=e.booking_no join wo_pre_cost_fabric_cost_dtls d on d.id=f.pre_cost_fabric_cost_dtls_id join wo_pre_cost_mst c on d.job_no=c.job_no join wo_po_details_master a on a.job_no = c.job_no  join approval_history b on e.id = b.mst_id join wo_pre_cost_fabric_cost_dtls d on d.id=f.pre_cost_fabric_cost_dtls_id join wo_po_break_down g on f.po_break_down_id=g.id where f.status_active=1 and f.is_deleted=0  and d.is_deleted=0 and d.status_active=1 and a.is_deleted=0 and a.status_active=1 and e.is_deleted=0 and e.status_active=1 and g.is_deleted=0 and g.status_active=1 and b.approved!=1 and b.current_approval_status =1 $company_cond $buyerCond $type_cond $fabric_source_Cond $booking_date_cond2 $party_cond $pay_cond $bookingCond $item_category_cond group by e.booking_no, e.booking_date, e.is_short, e.short_booking_type, e.buyer_id, a.job_no, e.update_date, e.is_approved, a.style_ref_no, a.client_id, e.item_category,e.company_id, e.fabric_source, e.source, e.pay_mode, e.supplier_id, f.responsible_person, f.reason, d.uom, e.entry_form, a.insert_date, e.po_break_down_id, f.po_break_down_id, g.grouping, a.product_dept,g.shipment_date,e.booking_type,e.inserted_by,e.ready_to_approved,c.costing_date,c.costing_per,a.buyer_name,b.current_approval_status  order by e.booking_date asc";
			}else{
				$get_booking = "Select e.booking_no, e.booking_date,e.company_id, a.insert_date, e.update_date, e.is_approved, e.is_short, e.short_booking_type, e.buyer_id, a.job_no, a.style_ref_no, a.client_id, e.item_category, e.fabric_source, e.source, e.pay_mode, e.supplier_id, sum(f.grey_fab_qnty) as booking_qty, sum(f.fin_fab_qnty) as fin_fab_qnty, f.responsible_person, f.reason, sum(f.amount) as booking_amount, d.uom, g.grouping, e.entry_form, nvl(e.po_break_down_id,f.po_break_down_id) as po_break_down_id,a.product_dept,g.shipment_date,e.booking_type,e.inserted_by,e.ready_to_approved,c.costing_date,c.costing_per,a.buyer_name from wo_booking_dtls f join wo_booking_mst e on f.booking_no=e.booking_no join wo_pre_cost_fabric_cost_dtls d on d.id=f.pre_cost_fabric_cost_dtls_id join wo_pre_cost_mst c on d.job_no=c.job_no join wo_po_details_master a on a.job_no = c.job_no join wo_booking_mst e on f.booking_no=e.booking_no join wo_pre_cost_fabric_cost_dtls d on d.id=f.pre_cost_fabric_cost_dtls_id join wo_po_break_down g on f.po_break_down_id=g.id where f.status_active=1 and f.is_deleted=0  and d.is_deleted=0 and d.status_active=1 and a.is_deleted=0 and a.status_active=1 and e.is_deleted=0 and e.status_active=1 and g.is_deleted=0 and g.status_active=1 $company_cond $buyerCond $type_cond $fabric_source_Cond $booking_date_cond $party_cond $pay_cond $bookingCond $item_category_cond group by e.booking_no, e.booking_date, e.is_short, e.short_booking_type, e.buyer_id, a.job_no, e.update_date, e.is_approved, a.style_ref_no, a.client_id, e.item_category,e.company_id, e.fabric_source, e.source, e.pay_mode, e.supplier_id, f.responsible_person, f.reason, d.uom, e.entry_form, a.insert_date, e.po_break_down_id, f.po_break_down_id, g.grouping, a.product_dept,g.shipment_date,e.booking_type,e.inserted_by,e.ready_to_approved,c.costing_date,c.costing_per,a.buyer_name  order by e.booking_date asc";
			}
				$nameArray_approved = sql_select("select e.booking_no as booking_no,max(b.approved_date) as last_approve_date from wo_booking_mst e, approval_history b where e.id=b.mst_id and b.entry_form in(12,13,7) and b.approved=1 $company_cond $buyerCond $type_cond $fabric_source_Cond $party_cond $pay_cond $approval_cond group by e.booking_no ");
				foreach($nameArray_approved as $row)
				{		
					$approve_data_arr[$row[csf('booking_no')]]['last_approve_date']=$row[csf('last_approve_date')];
				}
				$first_nameArray_approved = sql_select("select e.booking_no as booking_no,min(b.approved_date) as first_approve_date from wo_booking_mst e, approval_history b where e.id=b.mst_id and b.entry_form in(12,13,7) and b.approved=1 $company_cond $buyerCond $type_cond $fabric_source_Cond $party_cond $pay_cond $approval_cond group by e.booking_no ");
				foreach($first_nameArray_approved as $row)
				{		
					$approve_data_arr[$row[csf('booking_no')]]['first_approve_date']=$row[csf('first_approve_date')];
				}
				$nameArray_unapproved = sql_select("select e.booking_no as booking_no,max(b.approved_date) as last_unapprove_date from wo_booking_mst e, approval_history b where e.id=b.mst_id and b.entry_form in(12,13,7) $company_cond $buyerCond $type_cond $fabric_source_Cond $party_cond $pay_cond $approval_cond and b.approved!=1 group by e.booking_no ");
				foreach($nameArray_unapproved as $row)
				{		
					$approve_data_arr[$row[csf('booking_no')]]['last_unapprove_date']=$row[csf('last_unapprove_date')];
				}
				$yesno_approved = sql_select("select e.booking_no as booking_no,max(b.id) as appId,max(b.approved_date) as last_approve_date,min(b.approved_date) as first_approve_date,b.current_approval_status from wo_booking_mst e, approval_history b where e.id=b.mst_id and b.entry_form in(12,13,7) $company_cond $buyerCond $type_cond $fabric_source_Cond $party_cond $pay_cond $approval_cond group by e.booking_no,b.current_approval_status ");
				foreach($yesno_approved as $row)
				{		
					$approve_data_arr[$row[csf('booking_no')]]['approval_status'][$row[csf('appId')]]=$row[csf('current_approval_status')];
				}
		   
		     /* echo "<pre>";
		   print_r($approve_data_arr);die;  */
			//echo $get_booking; die;
		}
		else{
			if($cbo_booking_type == 5) $type_cond = " and a.booking_type=4 and a.entry_form_id in (90,140,610,439,139)";
			if($cbo_company_id) $company_cond=" and a.company_id in($cbo_company_id)"; else $company_cond="";
			if($cbo_company_id) $company_cond2=" and e.company_id in($cbo_company_id)"; else $company_con2="";
			if($cbo_party_id) $party_cond=" and a.supplier_id in($cbo_party_id)"; else $party_cond="";
			if($cbo_pay_mode) $pay_cond=" and a.pay_mode in($cbo_pay_mode)"; else $pay_cond="";
			if($cbo_buyer_id) $buyerCond=" and a.buyer_id in($cbo_buyer_id)"; else $buyerCond="";
			if($cbo_fabric_source) $fabric_source_Cond=" and a.fabric_source in($cbo_fabric_source)"; else $fabric_source_Cond="";
			if($txt_date_from!="" && $txt_date_to!="") $booking_date_cond="and a.booking_date between '$txt_date_from' and '$txt_date_to'"; else $booking_date_cond="";
			if($txt_booking_no_id!='')
			{
				if ($txt_booking_no_id=="") $bookingCond=""; else $bookingCond=" and a.id in ( $txt_booking_no_id )";
			}
			else
			{
				if ($txt_booking_no=="") $bookingCond=""; else $bookingCond=" and a.booking_no like '%$txt_po_no%'";
			}

			$item_category_cond='';
			
			if(!empty($cbofabricnature))
			{
				$item_category_cond=" and a.item_category in($cbofabricnature)";
				//wo_non_ord_samp_booking_mst
			}
			$get_booking = "Select a.booking_no, a.booking_date, a.company_id,a.update_date, a.insert_date, a.is_approved, a.is_short, '0' as short_booking_type, a.buyer_id, '' as job_no, '' as style_ref_no, '' as client_id, '' as garments_nature, a.fabric_source, a.source, a.pay_mode, a.supplier_id, b.uom, sum(b.grey_fabric) as booking_qty, sum(b.amount) as booking_amount, a.entry_form_id as entry_form, a.po_break_down_id from wo_non_ord_samp_booking_mst a join wo_non_ord_samp_booking_dtls b on a.booking_no=b.booking_no where a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $company_cond $buyerCond $fabric_source_Cond $booking_date_cond $type_cond $party_cond $pay_cond $bookingCond $item_category_cond group by a.booking_no,a.company_id, a.booking_date, a.is_short, a.buyer_id, a.fabric_source, a.update_date, a.insert_date, a.is_approved, a.source, a.pay_mode, a.supplier_id, b.uom, a.entry_form_id, a.po_break_down_id";
			$nameArray_approved_non = sql_select("select a.booking_no as booking_no, max(b.approved_date) as last_approve_date, min(b.approved_date) as first_approve_date from wo_non_ord_samp_booking_mst a, approval_history b where a.id=b.mst_id and b.entry_form in(9) $company_cond $buyerCond $fabric_source_Cond $booking_date_cond $type_cond $party_cond $pay_cond $approval_cond  group by a.booking_no ");
			foreach($nameArray_approved_non as $row)
			{
				$approve_data_arr[$row[csf('booking_no')]]['first_approve_date']=$row[csf('first_approve_date')];
				$approve_data_arr[$row[csf('booking_no')]]['last_approve_date']=$row[csf('last_approve_date')];
			}
		}
		 //echo $get_booking;die;
		$sql_data=sql_select($get_booking);
		$booking_date_arr =array();
		$po_break_down_ids="";
		foreach ($sql_data as $value) {
			$booking_date_arr[$value[csf('booking_no')]]['booking_no'] = $value[csf('booking_no')];
			$booking_date_arr[$value[csf('booking_no')]]['booking_id'] = $value[csf('booking_id')];
			$booking_date_arr[$value[csf('booking_no')]]['booking_type'] = $value[csf('booking_type')];
			$booking_date_arr[$value[csf('booking_no')]]['costing_date'] = $value[csf('costing_date')];
			$booking_date_arr[$value[csf('booking_no')]]['buyer_name'] = $value[csf('buyer_name')];
			$booking_date_arr[$value[csf('booking_no')]]['booking_type'] = $value[csf('booking_type')];
			$booking_date_arr[$value[csf('booking_no')]]['update_date'] = $value[csf('update_date')];
			$booking_date_arr[$value[csf('booking_no')]]['company_id'] = $value[csf('company_id')];
			$booking_date_arr[$value[csf('booking_no')]]['insert_date'] = $value[csf('insert_date')];
			$booking_date_arr[$value[csf('booking_no')]]['is_approved'] = $value[csf('is_approved')];
			$booking_date_arr[$value[csf('booking_no')]]['booking_date'] = $value[csf('booking_date')];
			$booking_date_arr[$value[csf('booking_no')]]['shipment_date'] = $value[csf('shipment_date')];
			$booking_date_arr[$value[csf('booking_no')]]['booking_type'] = $value[csf('booking_type')];
			$booking_date_arr[$value[csf('booking_no')]]['short_booking_type'] = $value[csf('short_booking_type')];
			$booking_date_arr[$value[csf('booking_no')]]['ready_to_approved'] = $value[csf('ready_to_approved')];
			$booking_date_arr[$value[csf('booking_no')]]['buyer_id'] = $value[csf('buyer_id')];
			$booking_date_arr[$value[csf('booking_no')]]['grouping'] = $value[csf('grouping')];
			$booking_date_arr[$value[csf('booking_no')]]['style_ref_no'] = $value[csf('style_ref_no')];
			$booking_date_arr[$value[csf('booking_no')]]['client_id'] = $value[csf('client_id')];
			$booking_date_arr[$value[csf('booking_no')]]['job_no'] = $value[csf('job_no')];
			$booking_date_arr[$value[csf('booking_no')]]['item_category'] = $value[csf('item_category')];
			$booking_date_arr[$value[csf('booking_no')]]['fabric_source'] = $value[csf('fabric_source')];
			$booking_date_arr[$value[csf('booking_no')]]['source'] = $value[csf('source')];
			$booking_date_arr[$value[csf('booking_no')]]['pay_mode'] = $value[csf('pay_mode')];
			$booking_date_arr[$value[csf('booking_no')]]['supplier_id'] = $value[csf('supplier_id')];
			$booking_date_arr[$value[csf('booking_no')]]['entry_form'] = $value[csf('entry_form')];
			$booking_date_arr[$value[csf('booking_no')]]['product_dept'] = $product_dept[$value[csf('product_dept')]];
			$booking_date_arr[$value[csf('booking_no')]]['po_break_down_id'] = $value[csf('po_break_down_id')];
			$booking_date_arr[$value[csf('booking_no')]]['inserted_by'] = $lib_user[$value[csf('inserted_by')]];
			$booking_date_arr[$value[csf('booking_no')]]['booking_amount'] += $value[csf('booking_amount')];
			$booking_date_arr[$value[csf('booking_no')]][$value[csf('uom')]]+= $value[csf('booking_qty')];
			if($based_on==3 || $based_on==4){
				$booking_date_arr[$value[csf('booking_no')]]['first_approval_date'] = $value[csf('first_approval_date')];
				$booking_date_arr[$value[csf('booking_no')]]['last_approval_date'] = $value[csf('last_approval_date')];
				$booking_date_arr[$value[csf('booking_no')]]['approval_status'][$value[csf('appId')]]=$value[csf('current_approval_status')];
			}
			
			$booking_arr[$value[csf("booking_no")]]=$value[csf("booking_no")];
			$booking_date_arr[$value[csf('booking_no')]]['responsible_person'][$value[csf('responsible_person')]]= $value[csf('responsible_person')];
			$booking_date_arr[$value[csf('booking_no')]]['reason'][$value[csf('reason')]]= $value[csf('reason')];
	
			if(!empty($value[csf('entry_form')]));
			{
				if(!empty($value[csf('po_break_down_id')]))
				{
	
					$po_break_down_ids.=$value[csf('po_break_down_id')].",";
				}
			}
			if($value[csf('entry_form')]==188){
				$poIdArr[$value[csf('po_break_down_id')]]=$value[csf('po_break_down_id')];
			}
	   }
	   $po_break_down_ids=chop($po_break_down_ids,",");
	   $po_ids= explode(",", $po_break_down_ids);
	   $po_data=array();
	   if(count($po_ids))
	   {
			$sql_po="SELECT id,po_received_date from wo_po_break_down where status_active=1 ". where_con_using_array($po_ids,0,"id");
		   // echo $sql_po;
			$po_res=sql_select($sql_po);
			$po_data=array();
			foreach ($po_res as  $value) {
				$po_data[$value[csf('id')]]=$value[csf('po_received_date')];
			}
		}
		
		/*  echo '<pre>';
		print_r($booking_date_arr); die;
 */

		//=====================================FSO===================================================
						
		$fso_data = sql_select("select a.job_no,a.booking_date,b.booking_no,sum(c.cons_quantity) as grey_qty from fabric_sales_order_mst a left join wo_booking_mst b 	on a.booking_id=b.id ,inv_transaction c,order_wise_pro_details d where c.id=d.trans_id and d.po_breakdown_id=a.id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0  ".where_con_using_array($booking_arr,1,'a.sales_booking_no')."  and c.transaction_type=2 and c.receive_basis in(3,8) group by  a.job_no,a.booking_date,b.booking_no");
		foreach($fso_data as $value){
			//$booking_wise_data_arr[$vals[csf('sales_booking_no')]]['fso_no']=$vals[csf('job_no')];
			$booking_date_arr[$value[csf('booking_no')]]['fso_qty'] = $value[csf('grey_qty')];
			$booking_date_arr[$value[csf('booking_no')]]['sales_booking_no'] = $value[csf('job_no')];
			$booking_date_arr[$value[csf('booking_no')]]['sales_booking_date'] = $value[csf('booking_date')];
		}
		unset($fso_data);
		$fso_rtn_data = sql_select("select a.job_no,a.booking_date,b.booking_no,sum(c.cons_quantity) as grey_rtn_qty from fabric_sales_order_mst a left join wo_booking_mst b 	on a.booking_id=b.id ,inv_transaction c,order_wise_pro_details d where c.id=d.trans_id and d.po_breakdown_id=a.id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0  ".where_con_using_array($booking_arr,1,'a.sales_booking_no')."  and c.transaction_type=4 and d.entry_form=9 and c.receive_basis in(3,8) group by  a.job_no,a.booking_date,b.booking_no");
		foreach($fso_rtn_data as $value){
			$booking_date_arr[$value[csf('booking_no')]]['fso_rtn_qty'] = $value[csf('grey_rtn_qty')];
		}
		unset($fso_rtn_data);
		//echo '<pre>';print_r($fso_booking_arr);die;
		//=================================================================================================
		//=========================================short booking sql==============================================
		if($cbo_company_id) $company_cond=" and a.company_id in($cbo_company_id)"; else $company_cond="";
		if($cbo_company_id) $company_cond2=" and e.company_id in($cbo_company_id)"; else $company_con2="";
		$poCond="";
		if(count($poIdArr)>0){
			$poIds=implode(",",$poIdArr);
			$poCond="and b.po_break_down_id in ($poIds)";
		}
		$short_booking_sql="select a.booking_no,a.job_no , sum(b.grey_fab_qnty) as grey_fab_qnty as booking_qty  from wo_booking_mst a, wo_booking_dtls b where  a.job_no=b.job_no and a.id=b.booking_mst_id  and a.entry_form=88 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $poCond $company_cond  group by a.booking_no,a.job_no";

		$short_booking_data=sql_select($short_booking_sql);
		foreach($short_booking_data as $row){
			$short_booking_arr[$row[csf('job_no')]]['booking_no']=$row[csf('booking_no')];
			$short_booking_arr[$row[csf('job_no')]]['booking_qnty']+=$row[csf('booking_qty')];
		}
	
		ob_start();
		?>
		<div align="center">
			<table width="3100px" cellspacing="0">
				<tr class="form_caption" style="border:none;">
					<td colspan="33" align="center" style="border:none;font-size:14px; font-weight:bold" ><? echo $report_title; ?></td>
				</tr>
				<tr style="border:none;">
					<td colspan="33" align="center" style="border:none; font-size:16px; font-weight:bold"><? echo $companyArr[$cbo_company_id]; ?></td>
				</tr>
			</table>
			<table width="3860px" cellspacing="0" border="1" class="rpt_table" rules="all">
				<thead>
					<tr style="font-size:13px">
						<th style="word-wrap: break-word;" width="30">SL.</th>
						<th style="word-wrap: break-word;"  width="100">Company</th>
						<th style="word-wrap: break-word;" width="100">System No</th>
						<th style="word-wrap: break-word;" width="100">Internal Booking No</th>
						<th style="word-wrap: break-word;" width="100">System Short Booking No</th>
						<th style="word-wrap: break-word;" width="100">FSO No</th>
						<th style="word-wrap: break-word;" width="100">FSO Date</th>
						<th style="word-wrap: break-word;" width="100">Revise No</th>
						<th style="word-wrap: break-word;" width="100">Net Yarn Issue Qty</th>
						<th style="word-wrap: break-word;" width="100">Job</th>
						<th style="word-wrap: break-word;" width="70">Booking Insert Date</th>
						<th style="word-wrap: break-word;" width="50">Waiting For 1st Approval</th>
						<th style="word-wrap: break-word;" width="100">Shipment Date</th>
						<th style="word-wrap: break-word;" width="70">Ready To Approved</th>
						<th style="word-wrap: break-word;" width="70">Approval Status</th>
						<th style="word-wrap: break-word;" width="70">1st Appv. Date</th>
						<th style="word-wrap: break-word;" width="70">Last Appv. Date</th>
						<th style="word-wrap: break-word;" width="100">Last Un-Appv. Date</th>
						<th style="word-wrap: break-word;" width="100">Days Passed From<br>Last Un Approval</th>
						<th style="word-wrap: break-word;" width="100">Po Received Date</th>
						<th style="word-wrap: break-word;" width="100">Booking Type</th>
						<th style="word-wrap: break-word;" width="100">Short Booking Type</th>
						<th style="word-wrap: break-word;" width="100">Buyer</th>
						<th style="word-wrap: break-word;" width="100">Buyer Client</th>
						
						<th style="word-wrap: break-word;" width="100">Style Ref.</th>
						<th style="word-wrap: break-word;" width="100">Product Dept.</th>
						<th style="word-wrap: break-word;" width="100">Job</th>
						<th style="word-wrap: break-word;" width="100">Fabric Nature</th>
						<th style="word-wrap: break-word;" width="100">Fabric Source</th>
						<th style="word-wrap: break-word;" width="100">Source</th>
						<th style="word-wrap: break-word;" width="100">Paymode</th>
						<th style="word-wrap: break-word;" width="100">Party Name</th>
						<th style="word-wrap: break-word;" width="100">Booking Qty (Kg)</th>
						<th style="word-wrap: break-word;" width="100">Booking Qty (Yds)</th>
						<th style="word-wrap: break-word;" width="100">Booking Qty (Mtr)</th>
						<th style="word-wrap: break-word;" width="100">Short Booking Qty</th>
						<th style="word-wrap: break-word;" width="100">Total Qty</th>
						<th style="word-wrap: break-word;" width="100" >Booking Amount ($)</th>
						<th style="word-wrap: break-word;" width="100" >Reason</th>
						<th style="word-wrap: break-word;" width="100" >Responsible Person</th>
						<th style="word-wrap: break-word;" >User Name</th>
					 </tr>
				</thead>
			</table>
			 <div style="width:3860px; max-height:300px; overflow-y:scroll" id="scroll_body">
			<table width="3840px" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_body">
				<tbody>
					<?
					// $print_report_format_ids2 = return_field_value("format_id","lib_report_template","template_name=".$cbo_company_id." and module_id=2 and report_id=1 and is_deleted=0 and status_active=1");
					// $format_ids=explode(",",$print_report_format_ids2);
					$print_report_format_arr=sql_select("select format_id,template_name from lib_report_template where module_id=2 and report_id=1 and is_deleted=0 and status_active=1 and template_name in ($cbo_company_id) ");	//main fabric booking				
					foreach($print_report_format_arr as $row){
						$format_ids=explode(",",$row[csf('format_id')]);
						$report_btn_arr[118][$row[csf('template_name')]]=$format_ids[0];
						
					}

					$print_report_format_arr2=sql_select("select format_id,template_name from lib_report_template where module_id=2 and report_id=2 and is_deleted=0 and status_active=1 and template_name in ($cbo_company_id) ");//short fabric booking
					foreach($print_report_format_arr2 as $row){
						$format_ids=explode(",",$row[csf('format_id')]);
						$report_btn_arr[88][$row[csf('template_name')]]=$format_ids[0];
						
					}
					$print_report_format_arr3=sql_select("select format_id,template_name from lib_report_template where module_id=2 and report_id=35 and is_deleted=0 and status_active=1 and template_name in ($cbo_company_id) ");//short fabric booking
					foreach($print_report_format_arr2 as $row){
						$format_ids=explode(",",$row[csf('format_id')]);
						$report_btn_arr[108][$row[csf('template_name')]]=$format_ids[0];
						
					}
					
					$i=1; $tot_rows=0;
					$total_booking_qty_kg =0; $total_booking_qty_mtr =0; $total_booking_qty_yds= 0;
					foreach ($booking_date_arr as $row) {
						//echo $row[12]; die;
						$booking_qty_kg = 0; $booking_qty_mtr=0; $booking_qty_yds = 0;
						 if(array_key_exists(12, $row)){
							$booking_qty_kg = $row[12];
						 }
						 if(array_key_exists(23, $row)){
							$booking_qty_mtr = $row[23];
						 }
						 if(array_key_exists(27, $row)){
							$booking_qty_yds = $row[27];
						 }
						 if($row['pay_mode']==3 || $row['pay_mode']==5){
							$party_name = $companyArr[$row['supplier_id']];
						 }
						 else{
							$party_name = $supplierArr[$row['supplier_id']];
						 }
						 $total_booking_qty_kg += $booking_qty_kg;
						 $total_booking_qty_mtr += $booking_qty_mtr;
						 $total_booking_qty_yds += $booking_qty_yds;
						 $total_booking_amount = $row['booking_amount'];
						 $total_booking_amount = $row['booking_amount'];
		
						 if($row['entry_form'] == ''){
							$entry_form = 0;
						 }
						 else{
							$entry_form = $row['entry_form'];
						 }
						$is_approved=$row['is_approved'];
						$first_approve_dateTime=$last_approve_dateTime=$last_unapprove_dateTime='';
					   
						if($based_on==3){
							$first_approve_dateTime=$booking_date_arr[$row['booking_no']]['first_approval_date'];
							$last_approve_dateTime=$booking_date_arr[$row['booking_no']]['last_approval_date'];
							$last_unapprove_dateTime=$approve_data_arr[$row['booking_no']]['last_unapprove_date'];
						}else if($based_on==4){
							$first_approve_dateTime=$booking_date_arr[$row['booking_no']]['first_approval_date'];
							$last_approve_dateTime=$approve_data_arr[$row['booking_no']]['last_approve_date'];
							$last_unapprove_dateTime=$booking_date_arr[$row['booking_no']]['last_approval_date'];
						}else{
							$first_approve_dateTime=$approve_data_arr[$row['booking_no']]['first_approve_date'];
							$last_approve_dateTime=$approve_data_arr[$row['booking_no']]['last_approve_date'];
							$last_unapprove_dateTime=$approve_data_arr[$row['booking_no']]['last_unapprove_date'];
						}
						if($based_on==3 || $based_on==4){
							$approval_status=max($booking_date_arr[$row['booking_no']]['approval_status']);
						}else{
							$approval_status=max($approve_data_arr[$row['booking_no']]['approval_status']);
						}


						
						$first_approve_dateTimeArr=explode(" ",$first_approve_dateTime);
						$last_approve_dateTimeArr=explode(" ",$last_approve_dateTime);
						$last_unapprove_dateTimeArr=explode(" ",$last_unapprove_dateTime);
							$last_approve_date=$first_approve_date=$first_unapprove_date="";
							if(count($first_approve_dateTimeArr))
							{
								$first_approve_date=$first_approve_dateTimeArr[0];
							}
							if(count($last_approve_dateTimeArr))
							{
								$last_approve_date=$last_approve_dateTimeArr[0];
							}
							if(count($last_unapprove_dateTimeArr))
							{
								$last_unapprove_date=$last_unapprove_dateTimeArr[0];
							}
							
						$po_received_date="";
						$po_br_ids=$row['po_break_down_id'];
						if(!empty($po_br_ids))
						{
						   $po_br_idss= explode(",", $po_br_ids);
						   $po_received_date=$po_data[min($po_br_idss)];
						}
						if($entry_form==0){
							$re_booking_no=$row['booking_no'];
					
							$revise_approved = sql_select("select max(b.approved_no) as approved_no from wo_non_ord_samp_booking_mst a, approval_history b where a.id=b.mst_id and booking_no='$re_booking_no' and b.entry_form=9 $company_cond $buyerCond $type_cond $fabric_source_Cond $booking_date_cond $party_cond $pay_cond $approval_cond");
							list($revise_approved_row) = $revise_approved;
						}else{
							$re_booking_no=$row['booking_no'];
						
							$revise_approved = sql_select("select max(b.approved_no) as approved_no,count(b.id) as revised_no,e.is_approved from wo_booking_mst e, approval_history b where e.id=b.mst_id and booking_no='$re_booking_no'  $company_cond2 $buyerCond $type_cond $fabric_source_Cond $booking_date_cond $party_cond $pay_cond $approval_cond and b.entry_form=7 group by e.is_approved");
							list($nameArray_approved_row) = $revise_approved;
						}
						$fabric_nature=$row[csf('item_category')];
						//  if($row['booking_type']==1 && $row['entry_form']==118) $row_id=$report_btn_arr[$row['entry_form']][$row['company_id']];

							
						if($row['booking_type']==1 && $row['entry_form']==118){
							$row_id=$report_btn_arr[$row['entry_form']][$row['company_id']];
							
							if($row_id==786){
								$variable="<a href='#' onClick=\"generate_worder_report('".$row['booking_no']."','".$row['company_id']."','".$po_br_ids."','".$row['item_category']."','".$row['fabric_source']."','".$row['job_no']."','".$row['is_approved']."','".$row_id."','".$row['entry_form']."','show_fabric_booking_report25','".$i."',".$fabric_nature.")\"> ".$row['booking_no']."<a/>";
							}
							else if($row_id==426){
								$variable="<a href='#' onClick=\"generate_worder_report('".$row['booking_no']."','".$row['company_id']."','".$po_br_ids."','".$row['item_category']."','".$row['fabric_source']."','".$row['job_no']."','".$row['is_approved']."','".$row_id."','".$row['entry_form']."','show_fabric_booking_report_print23','".$i."',".$fabric_nature.")\"> ".$row['booking_no']."<a/>";
							}else if($row_id==502){
								$variable="<a href='#' onClick=\"generate_worder_report('".$row['booking_no']."','".$row['company_id']."','".$po_br_ids."','".$row['item_category']."','".$row['fabric_source']."','".$row['job_no']."','".$row['is_approved']."','".$row_id."','".$row['entry_form']."','show_fabric_booking_report26','".$i."',".$fabric_nature.")\"> ".$row['booking_no']."<a/>";
							}
						}elseif($row['booking_type']==1 && $row['entry_form']==88){
							$row_id=$report_btn_arr[$row['entry_form']][$row['company_id']];
							if($row_id==72){
								$variable="<a href='#' onClick=\"generate_worder_report('".$row['booking_no']."','".$row['company_id']."','".$po_br_ids."','".$row['item_category']."','".$row['fabric_source']."','".$row['job_no']."','".$row['is_approved']."','".$row_id."','".$row['entry_form']."','print_booking_6','".$i."',".$fabric_nature.")\"> ".$row['booking_no']."<a/>";
							}
							else if($row_id==191){
								$variable="<a href='#' onClick=\"generate_worder_report('".$row['booking_no']."','".$row['company_id']."','".$po_br_ids."','".$row['item_category']."','".$row['fabric_source']."','".$row['job_no']."','".$row['is_approved']."','".$row_id."','".$row['entry_form']."','print_booking_7','".$i."',".$fabric_nature.")\"> ".$row['booking_no']."<a/>";
							}
							else if($row_id==45){
								$variable="<a href='#' onClick=\"generate_worder_report('".$row['booking_no']."','".$row['company_id']."','".$po_br_ids."','".$row['item_category']."','".$row['fabric_source']."','".$row['job_no']."','".$row['is_approved']."','".$row_id."','".$row['entry_form']."','print_booking_4','".$i."',".$fabric_nature.")\"> ".$row['booking_no']."<a/>";
							}
							else if($row_id==53){
								$variable="<a href='#' onClick=\"generate_worder_report('".$row['booking_no']."','".$row['company_id']."','".$po_br_ids."','".$row['item_category']."','".$row['fabric_source']."','".$row['job_no']."','".$row['is_approved']."','".$row_id."','".$row['entry_form']."','print_booking_5','".$i."',".$fabric_nature.")\"> ".$row['booking_no']."<a/>";
							}
						}elseif($row['booking_type']==1 && $row['entry_form']==108){
							$variable="<a href='#' onClick=\"generate_worder_report('".$row['booking_no']."','".$row['company_id']."','".$po_br_ids."','".$row['item_category']."','".$row['fabric_source']."','".$row['job_no']."','".$row['is_approved']."','".$row_id."','".$row['entry_form']."','show_fabric_booking_report','".$i."',".$fabric_nature.")\"> ".$row['booking_no']."<a/>";
						}

						$print_report_format=return_field_value("format_id","lib_report_template","template_name =".$cbo_company_id." and module_id=2 and report_id=43 and is_deleted=0 and status_active=1");
						$print_button=explode(",",$print_report_format);
						if($print_button[0]==25) $precost_button="budgetsheet2";
						else if($print_button[0]==50) $precost_button="preCostRpt";
						else if($print_button[0]==51) $precost_button="preCostRpt2";
						else if($print_button[0]==52) $precost_button="bomRpt";
						else if($print_button[0]==63) $precost_button="bomRpt2";
						else if($print_button[0]==156) $precost_button="accessories_details";
						else if($print_button[0]==157) $precost_button="accessories_details2";
						else if($print_button[0]==158) $precost_button="preCostRptWoven";
						else if($print_button[0]==159) $precost_button="bomRptWoven";
						else if($print_button[0]==170) $precost_button="preCostRpt3";
						else if($print_button[0]==171) $precost_button="preCostRpt4";
						else if($print_button[0]==142) $precost_button="preCostRptBpkW";
						else if($print_button[0]==192) $precost_button="checkListRpt";
						else if($print_button[0]==197) $precost_button="bomRpt3";
						else if($print_button[0]==211) $precost_button="mo_sheet";
						else if($print_button[0]==221) $precost_button="fabric_cost_detail";
						else if($print_button[0]==173) $precost_button="preCostRpt5";
						else if($print_button[0]==238) $precost_button="summary";
						else if($print_button[0]==215) $precost_button="budget3_details";
						else if($print_button[0]==270) $precost_button="preCostRpt6";
						else if($print_button[0]==581) $precost_button="costsheet";
						else if($print_button[0]==730) $precost_button="budgetsheet";
						else if($print_button[0]==351) $precost_button="bomRpt4";
						else if($print_button[0]==381) $precost_button="mo_sheet_1";
						else if($print_button[0]==268) $precost_button="budget_4";
						else if($print_button[0]==403) $precost_button="mo_sheet_3";
						else if($print_button[0]==769) $precost_button="preCostRpt7";
						else if($print_button[0]==445) $precost_button="preCostRpt8";
						else if($print_button[0]==460) $precost_button="trims_check_list";
						else if($print_button[0]==129) $precost_button="budget5";
						else if($print_button[0]==235) $precost_button="preCostRpt9";
						
						else if($print_button[0]==120) $precost_button="budgetsheet3";
						else if($print_button[0]==494) $precost_button="ocsReport";
						else if($print_button[0]==498) $precost_button="preCostRpt10";
						else if($print_button[0]==800) $precost_button="preCostRpt11";
						else if($print_button[0]==427) $precost_button="preCostRpt12";
						else if($print_button[0]==341) $precost_button="budgetsheet4";
						else $precost_button="";
						
						
						
							/*if($print_button[0]==25) $precost_button="budgetsheet2";
							else if($print_button[0]==50) $precost_button="preCostRpt";
							else if($print_button[0]==51) $precost_button="preCostRpt2";
							else if($print_button[0]==52) $precost_button="bomRpt";
							else if($print_button[0]==63) $precost_button="bomRpt2";
							else if($print_button[0]==730) $precost_button="budgetsheet";
							
							else if($print_button[0]==156) $precost_button="accessories_details";
							else if($print_button[0]==157) $precost_button="accessories_details2";
							else if($print_button[0]==158) $precost_button="preCostRptWoven";
							else if($print_button[0]==159) $precost_button="bomRptWoven";
							else if($print_button[0]==170) $precost_button="preCostRpt3";
							else if($print_button[0]==171) $precost_button="preCostRpt4";
							else if($print_button[0]==142) $precost_button="preCostRptBpkW";
							else if($print_button[0]==192) $precost_button="checkListRpt";
							else if($print_button[0]==197) $precost_button="bomRpt3";
							else if($print_button[0]==211) $precost_button="mo_sheet";
							else if($print_button[0]==221) $precost_button="fabric_cost_detail";
							else if($print_button[0]==173) $precost_button="preCostRpt5";
							else if($print_button[0]==238) $precost_button="summary";
							else if($print_button[0]==215) $precost_button="budget3_details";
							else if($print_button[0]==270) $precost_button="preCostRpt6";
							else if($print_button[0]==769) $precost_button="preCostRpt7";
							else if($print_button[0]==445) $precost_button="preCostRpt8";
							else  $precost_button="";*/
							
						$print_report_format_v3=return_field_value("format_id","lib_report_template","template_name =".$cbo_company_id." and module_id=2 and report_id=161 and is_deleted=0 and status_active=1");
						$print_button_v3=explode(",",$print_report_format_v3);

							if($print_button_v3[0]==50) $action_v3="preCostRpt";
							if($print_button_v3[0]==51) $action_v3="preCostRpt2";
							if($print_button_v3[0]==52) $action_v3="bomRpt";
							if($print_button_v3[0]==63) $action_v3="bomRpt2";
							if($print_button_v3[0]==156) $action_v3="accessories_details";
							if($print_button_v3[0]==157) $action_v3="accessories_details2";
							if($print_button_v3[0]==158) $action_v3="preCostRptWoven";
							if($print_button_v3[0]==159) $action_v3="bomRptWoven";
							if($print_button_v3[0]==170) $action_v3="preCostRpt3";
							if($print_button_v3[0]==171) $action_v3="preCostRpt4";
							if($print_button_v3[0]==142) $action_v3="preCostRptBpkW";
							if($print_button_v3[0]==192) $action_v3="checkListRpt";
							if($print_button_v3[0]==197) $action_v3="bomRpt3";
							if($print_button_v3[0]==211) $action_v3="mo_sheet";
							if($print_button_v3[0]==221) $action_v3="fabric_cost_detail";
							if($print_button_v3[0]==173) $action_v3="preCostRpt5";
							if($print_button_v3[0]==238) $action_v3="summary";
							if($print_button_v3[0]==215) $action_v3="budget3_details";
							if($print_button_v3[0]==270) $action_v3="preCostRpt6";
							if($print_button_v3[0]==581) $action_v3="costsheet";
							if($print_button_v3[0]==730) $action_v3="budgetsheet";
							if($print_button_v3[0]==759) $action_v3="materialSheet";
							if($print_button_v3[0]==351) $action_v3="bomRpt4";
							if($print_button_v3[0]==268) $action_v3="budget_4";
							if($print_button_v3[0]==381) $action_v3="mo_sheet_2";
							if($print_button_v3[0]==405) $action_v3="materialSheet2";
							if($print_button_v3[0]==765) $action_v3="bomRpt5";
							if($print_button_v3[0]==403) $action_v3="mo_sheet_3";
							if($print_button_v3[0]==445) $action_v3="preCostRpt8";
						?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
							<td style="word-wrap: break-word;" width="30" align="center"><? echo $i; ?></td>
							<td style="word-wrap: break-word;" width="100" align="center"><? echo $companyArr[$row['company_id']]; ?></td>
							<td style="word-wrap: break-word;" width="100" align="center" title="<? echo $is_approved;?>"><? echo $variable; ?></td>
							<td style="word-wrap: break-word;" width="100" align="center"><? echo $row['grouping']; ?></td>
							<td style="word-wrap: break-word;" width="100" align="center"><? echo $short_booking_arr[$row['job_no']]['booking_no']; ?></td>
							<td style="word-wrap: break-word;" width="100" align="center"><? echo $row['sales_booking_no']; ?></td>
							<td style="word-wrap: break-word;" width="100" align="center"><? echo change_date_format($row['sales_booking_date']); ?></td>
							<td style="word-wrap: break-word;" width="100" align="center"><?
							if($nameArray_approved_row[csf('approved_no')]>1)
							{
								?>
								
								<b><? echo $nameArray_approved_row[csf('approved_no')]-1; ?></b>
								<?
							}
							  	?></td>
							 <td style="word-wrap: break-word;" width="100" ><?
							 $net_qty=$row['fso_qty']-$row['fso_rtn_qty'];
							 echo $net_qty; ?></td>
							 <td style="word-wrap: break-word;" width="100">
							 <p>
							<?php 
								$company_name=$row['company_id'];
								$buyer_name=$row['buyer_name'];
								$costing_date=$row['costing_date'];
								$costing_per=$row['costing_per'];
								$style_ref_no=$row['style_ref_no'];
							 ?>
							<a href='#report_details' onClick="generate_report_v3('<? echo $company_name; ?>','<? echo $row['job_no']; ?>','<? echo $style_ref_no; ?>','<? echo $buyer_name;?>','<? echo $costing_date;?>','<? echo $po_br_ids;?>','<? echo $precost_button;?>');">
								<? echo $row['job_no']; ?>
							</a>
							
						&nbsp;</p>
							</td>
							<td style="word-wrap: break-word;" width="70" align="center" title="<? echo $row['booking_date'];?>"><? 
							$booking_date=$row['booking_date'];
							echo change_date_format($row['booking_date'], "d-M-y", "-", 1); ?></td>
							<td style="word-wrap: break-word;" width="50" align="center" title="<? echo $row['booking_date'];?>"><? 
								$lead_time=0;
								if($row[("booking_date")]!="" && $row[("booking_date")]!="0000-00-00")
								{
									$lead_time=datediff("d", $row[("booking_date")], date("d-m-Y"));
								}
								$waiting_time=$lead_time-1;
								if($first_approve_date=="" && $waiting_time>0) echo $waiting_time." Days"; else echo " ";
							 ?></td>
							<td style="word-wrap: break-word;" width="100" align="center" title="<? echo $row['shipment_date'];?>"><? echo change_date_format($row['shipment_date'], "d-M-y", "-", 1); ?></td>
							<td style="word-wrap: break-word;" width="70"  align="center" align="center"><?=$yes_no[$row['ready_to_approved']]; ?></td>
							<td style="word-wrap: break-word;" width="70" align="center"><?=$yes_no[$approval_status];; ?></td>
							<td style="word-wrap: break-word;" width="70" align="center" title="<? echo $first_approve_dateTime;?>"><? echo change_date_format($first_approve_date, "d-M-y", "-", 1); ?> </td>
							<td style="word-wrap: break-word;" width="70" align="center" title="<? echo $last_approve_dateTime;?>"><? echo change_date_format($last_approve_date, "d-M-y", "-", 1); ?></td>
							<td style="word-wrap: break-word;" width="100" align="center" title="<? echo $last_unapprove_dateTime;?>"><? if($last_unapprove_dateTime>$last_approve_dateTime)echo change_date_format($last_unapprove_dateTime, "d-M-y", "-", 1); ?></td>
							<td style="word-wrap: break-word;" width="100" align="center"><? 
							$approval_statuss=max($approve_data_arr[$row['booking_no']]['approval_status']);
							$lead_times=0;
							if($last_unapprove_dateTime!="" && $last_unapprove_dateTime!="0000-00-00")
							{
								$lead_times=datediff("d", $last_unapprove_dateTime, date("d-m-Y"));
							}
							if($last_unapprove_dateTime>$last_approve_dateTime && $approval_statuss!=1) echo $lead_times." Days"; else echo "";
							//echo change_date_format($row['booking_date'], "d-M-y", "-", 1); ?></td>
							<td style="word-wrap: break-word;" width="100" align="center"><? echo change_date_format($po_received_date, "d-M-y", "-", 1); ?></td>
							<td style="word-wrap: break-word;" width="100" align="center" title="entry form=<?=$entry_form;?>"><? echo $fabric_booking_type[$entry_form]; ?></td>
							<td style="word-wrap: break-word;" width="100" align="center"><? echo $short_booking_type[$row['short_booking_type']]; ?></td>
							<td style="word-wrap: break-word;" width="100" align="center"><? echo $buyerArr[$row['buyer_id']]; ?></td>
							<td style="word-wrap: break-word;" width="100" align="center"><? echo $buyerArr[$row['client_id']]; ?></td>
						
							<td style="word-wrap: break-word;" width="100" align="center"><? echo $row['style_ref_no']; ?></td>
							<td style="word-wrap: break-word;" width="100" align="center"><? echo $row['product_dept']; ?></td>
							<td style="word-wrap: break-word;" width="100"align="center" ><? echo $row['job_no']; ?></td>
							<td style="word-wrap: break-word;" width="100" align="center"><? echo $item_category[$row['item_category']]; ?></td>
							<td style="word-wrap: break-word;" width="100" align="center"><? echo $fabric_source[$row['fabric_source']] ?></td>
							<td style="word-wrap: break-word;" width="100" align="center"><? echo $source[$row['source']]; ?></td>
							<td style="word-wrap: break-word;" width="100" align="center"><? echo $pay_mode[$row['pay_mode']]; ?></td>
							<td style="word-wrap: break-word;" width="100" align="center"><? echo $party_name; ?></td>
							<td style="word-wrap: break-word;" width="100" align="right"><? echo number_format($booking_qty_kg,2); ?></td>
							<td style="word-wrap: break-word;" width="100" align="right"><? echo number_format($booking_qty_yds,2); ?></td>
							<td style="word-wrap: break-word;" width="100" align="right"><? echo number_format($booking_qty_mtr,2); ?></td>
							<td style="word-wrap: break-word;" width="100" align="right"><? echo number_format($short_booking_arr[$row['job_no']]['booking_qnty'],2); ?></td>
							<td style="word-wrap: break-word;" width="100" align="right"><? echo number_format($booking_qty_kg+$short_booking_arr[$row['job_no']]['booking_qnty'],2); ?></td>
							<td style="word-wrap: break-word;" width="100" align="right"><? echo number_format($row['booking_amount'],2); ?></td>
		
							<td style="word-wrap: break-word;" width="100" align="center"><p><? echo implode(",", $row['reason']); ?></p></td>
							<td style="word-wrap: break-word;" width="100" align="center"><p><? echo implode(",", $row['responsible_person']); ?></p></td>
							<td style="word-wrap: break-word;" align="center"><? echo $row['inserted_by']; ?></td>
						</tr>
					
					  <? $i++; $tot_rows++;
					  	$tot_short_booking_qnty+=$short_booking_arr[$row['job_no']]['booking_qnty'];
						$tot_booking_qnty+=$booking_qty_kg+$booking_qty_yds+$booking_qty_mtr+$short_booking_arr[$row['job_no']]['booking_qnty'];
					} ?>
				</tbody>
			</table>
            </div>
			<table width="3860px" cellspacing="0" border="1" class="rpt_table" rules="all" id="report_table_footer" >
				<tfoot>
					<tr style="font-size:13px">
						<th bgcolor= "#A0A6AC" width="30"></th>
						<th bgcolor= "#A0A6AC" width="100"></th>
						<th bgcolor= "#A0A6AC" width="100"></th>
						<th bgcolor= "#A0A6AC" width="100"></th>
						<th bgcolor= "#A0A6AC"width="100"></th>
						<th bgcolor= "#A0A6AC"width="100"></th>
						<th bgcolor= "#A0A6AC"width="100"></th>
						<th bgcolor= "#A0A6AC"width="100"></th>
						<th bgcolor= "#A0A6AC"width="100"></th>
						<th bgcolor= "#A0A6AC"width="100"></th>
						<th bgcolor= "#A0A6AC"width="70"></th>
						<th bgcolor= "#A0A6AC"width="50"></th>
						<th bgcolor= "#A0A6AC"width="100"></th>
						<th bgcolor= "#A0A6AC"width="70"></th>
						<th bgcolor= "#A0A6AC"width="70"></th>
						<th bgcolor= "#A0A6AC"width="70"></th>
					
						<th bgcolor= "#A0A6AC"width="70"></th>
						<th bgcolor= "#A0A6AC"width="100"></th>
						<th bgcolor= "#A0A6AC"width="100"></th>
						<th bgcolor= "#A0A6AC"width="100"></th>
						<th bgcolor= "#A0A6AC"width="100"></th>
						<th bgcolor= "#A0A6AC"width="100"></th>
						<th bgcolor= "#A0A6AC"width="100"></th>
						<th bgcolor= "#A0A6AC"width="100"></th>
						<th bgcolor= "#A0A6AC"width="100"></th>
						<th bgcolor= "#A0A6AC"width="100"></th>
						<th bgcolor= "#A0A6AC"width="100"></th>
						<th bgcolor= "#A0A6AC"width="100"></th>
						<th bgcolor= "#A0A6AC"width="100"></th>
						<th bgcolor= "#A0A6AC"width="100"></th>
						<th bgcolor= "#A0A6AC"width="100"></th>
						<th bgcolor= "#A0A6AC"width="100"></th>
						<th bgcolor= "#A0A6AC" style="word-wrap: break-word;" width="100" id="total_booking_qty_kg" align="right"><? echo number_format($total_booking_qty_kg,2); ?></th>
						<th bgcolor= "#A0A6AC" style="word-wrap: break-word;" width="100" id="total_booking_qty_yds" align="right"><? echo number_format($total_booking_qty_yds,2); ?></th>
						<th bgcolor= "#A0A6AC" style="word-wrap: break-word;" width="100" id="total_booking_qty_mtr" align="right"><? echo number_format($total_booking_qty_mtr,2); ?></th>
						<th bgcolor= "#A0A6AC" style="word-wrap: break-word;" width="100" id="tot_short_booking_qnty" align="right"><? echo number_format($tot_short_booking_qnty,2); ?></th>
						<th bgcolor= "#A0A6AC" style="word-wrap: break-word;" width="100" id="tot_booking_qnty" align="right"><? echo number_format($tot_booking_qnty,2); ?></th>
						<th bgcolor= "#A0A6AC" style="word-wrap: break-word;" width="100" id="total_booking_amount" align="right"><? echo number_format($total_booking_amount,2); ?></th>
						<th bgcolor= "#A0A6AC" style="word-wrap: break-word;" width="100"></th>
						<th bgcolor= "#A0A6AC" width="100"></th>
						<th></th>
					</tr>
				</tfoot>
			</table>
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
    echo "$total_data####$filename####$tot_rows####$type";
    exit();
}

if($action=="report_generate_exel_only")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$cbo_company_id=str_replace("'","",$cbo_company_id);
    $cbo_buyer_id=str_replace("'","",$cbo_buyer_id);
    $cbo_booking_type=str_replace("'","",$cbo_fab_booking_type);
    $cbo_fabric_source=str_replace("'","",$cbo_fabric_source);
    $cbo_party_id=str_replace("'","",$cbo_party_id);
    $cbo_pay_mode=str_replace("'","",$cbo_pay_mode);
    $txt_date_from=str_replace("'","",$txt_date_from);
    $txt_date_to=str_replace("'","",$txt_date_to);
    $txt_booking_no_id=str_replace("'","",$txt_booking_no_id);
	$txt_booking_no=str_replace("'","",$txt_booking_no);
	$cbofabricnature=str_replace("'","",$cbofabricnature);
	$cbo_based_on=str_replace("'","",$cbo_based_on);

	$sql_cond="";
	if ($cbo_company_id != "") $sql_cond.=" and e.company_id in($cbo_company_id)";
	if ($cbo_buyer_id != "") $sql_cond.=" and e.buyer_id in($cbo_buyer_id)";
	if ($cbo_booking_type > 0) $sql_cond.=" and e.booking_type=$cbo_booking_type";
	if ($cbo_fabric_source != "") $sql_cond.=" and e.fabric_source in($cbo_fabric_source)";
	if ($cbo_pay_mode > 0) $sql_cond.=" and e.pay_mode in($cbo_pay_mode)";
	if ($cbo_party_id != "") $sql_cond.=" and e.supplier_id in($cbo_party_id)";
	if ($txt_booking_no_id != "") $sql_cond.=" and e.id in($txt_booking_no_id)";
	if ($cbofabricnature != "") $sql_cond.=" and e.item_category=$cbofabricnature";
	if ($txt_date_from != "" && $txt_date_to != "") {
		if ($cbo_based_on==1) $sql_cond .= " and e.booking_date between '".change_date_format($txt_date_from,'','',1)."' and '".change_date_format($txt_date_to,'','',1)."'";
		else $sql_cond .= " and f.pub_shipment_date between '".change_date_format($txt_date_from,'','',1)."' and '".change_date_format($txt_date_to,'','',1)."'";
	}

    $companyArr = return_library_array("select id,company_name from lib_company ","id","company_name");
    $buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
	$colorArr = return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$team_leader_arr = return_library_array( "select id,team_leader_name from lib_marketing_team  where project_type=1 and status_active=1 and is_deleted=0", "id", "team_leader_name");
	/* $labdip_sql=sql_select("select LAPDIP_NO, COLOR_NAME_ID, po_break_down_id as POID from wo_po_lapdip_approval_info where approval_status=3 and is_deleted=0  and status_active=1");
	$labdipArr=array();
	foreach ($labdip_sql as $row) 
	{
		$labdipArr[$row['POID']][$row['COLOR_NAME_ID']]=$row['LAPDIP_NO'];
	} */

	$sql= "SELECT a.BODY_PART_ID, a.lib_yarn_count_deter_id as DETERMIN_ID, a.COLOR_TYPE_ID, a.CONSTRUCTION, a.COMPOSITION, a.GSM_WEIGHT, min(a.width_dia_type) as WIDTH_DIA_TYPE, b.DIA_WIDTH, b.REMARKS, avg(b.cons) as CONS, b.PROCESS_LOSS_PERCENT, avg(b.requirment) as REQUIRMENT, b.PO_BREAK_DOWN_ID, d.FABRIC_COLOR_ID, d.GMTS_COLOR_ID, d.id as DTLS_ID, d.JOB_NO, d.BOOKING_NO, sum(d.fin_fab_qnty) as FIN_FAB_QNTY, sum(d.grey_fab_qnty) as GREY_FAB_QNTY, a.ID, f.PO_NUMBER, sum(c.plan_cut_qnty) as PLAN_CUT_QTY, max(f.shipment_date) as SHIPMENT_DATE, g.TEAM_LEADER 
	from wo_pre_cost_fabric_cost_dtls a, wo_po_color_size_breakdown c, wo_po_break_down f, wo_po_details_master g, wo_pre_cos_fab_co_avg_con_dtls b, wo_booking_dtls d, wo_booking_mst e 
	where a.job_id=b.job_id and a.id=b.pre_cost_fabric_cost_dtls_id and c.job_id=a.job_id and c.id=b.color_size_table_id and b.po_break_down_id=d.po_break_down_id and b.color_size_table_id=d.color_size_table_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and d.booking_no=e.booking_no and c.po_break_down_id=f.id and f.job_id=c.job_id and f.job_id=g.id and e.status_active=1 and e.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and g.status_active=1 and g.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sql_cond
	group by a.body_part_id,a.id, a.lib_yarn_count_deter_id, a.color_type_id, a.construction, a.composition, a.gsm_weight, b.dia_width, b.remarks,d.fabric_color_id, d.gmts_color_id, d.id, d.job_no, d.booking_no, b.po_break_down_id, b.process_loss_percent, f.po_number, g.team_leader 
	order by a.id, a.body_part_id, b.dia_width";
	$sql_res=sql_select($sql);
	
	$fabric_data_arr=array();
	foreach ($sql_res as $row) 
	{	
		$grouping_item=$row['JOB_NO'].'*'.$row['BOOKING_NO'].'*'.$row['PO_NUMBER'].'*'.$row['FABRIC_COLOR_ID'].'*'.$row['GMTS_COLOR_ID'].'*'.$row['BODY_PART_ID'].'*'.$row['CONSTRUCTION'].'*'.$row['COMPOSITION'].'*'.$row['GSM_WEIGHT'].'*'.$row['DIA_WIDTH'].'*'.$row['COLOR_TYPE_ID'];
		//$labdip_no=$labdipArr[$row['PO_BREAK_DOWN_ID']][$row['FABRIC_COLOR_ID']];
		$fabric_data_arr[$grouping_item]['team_leader']     = $row['TEAM_LEADER'];
		$fabric_data_arr[$grouping_item]['job_no']          = $row['JOB_NO'];
		$fabric_data_arr[$grouping_item]['booking_no']      = $row['BOOKING_NO'];
		$fabric_data_arr[$grouping_item]['job_no']          = $row['JOB_NO'];
		$fabric_data_arr[$grouping_item]['po_number']       = $row['PO_NUMBER'];
		$fabric_data_arr[$grouping_item]['labdip_no']       = $labdip_no;
		$fabric_data_arr[$grouping_item]['shipment_date']   = $row['SHIPMENT_DATE'];
		$fabric_data_arr[$grouping_item]['plan_cut_qty']   += $row['PLAN_CUT_QTY'];
		$fabric_data_arr[$grouping_item]['fabric_color_id'] = $row['FABRIC_COLOR_ID'];
		$fabric_data_arr[$grouping_item]['gmts_color_id']   = $row['GMTS_COLOR_ID'];
		$fabric_data_arr[$grouping_item]['body_part_id']    = $row['BODY_PART_ID'];
		$fabric_data_arr[$grouping_item]['fabric_des']      = $row['CONSTRUCTION'].','.$row['COMPOSITION'];
		$fabric_data_arr[$grouping_item]['gsm']             = $row['GSM_WEIGHT'];
		$fabric_data_arr[$grouping_item]['fabric_dia']      = $row['DIA_WIDTH'].",".$fabric_typee[$row['WIDTH_DIA_TYPE']];
		$fabric_data_arr[$grouping_item]['color_type_id']   = $row['COLOR_TYPE_ID'];
		$fabric_data_arr[$grouping_item]['finsh_cons']      = $row['CONS'];
		$fabric_data_arr[$grouping_item]['gray_cons']       = $row['REQUIRMENT'];
		$fabric_data_arr[$grouping_item]['fin_fab_qnty']   += $row['FIN_FAB_QNTY'];
		$fabric_data_arr[$grouping_item]['grey_fab_qnty']  += $row['GREY_FAB_QNTY'];
		$fabric_data_arr[$grouping_item]['process_loss_percent'] = $row['PROCESS_LOSS_PERCENT'];	
	}
   
	$html = "";
	$html .= '
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1610" class="rpt_table" >
		<thead>
			<tr>
				<th width="100">Team Leader</th>
				<th width="120">Job No</th>
				<th width="120">FB No</th>
				<th width="100">Order No</th>
				<th width="80">Max Ship Date</th>
				<th width="80">Gmts Color</th>
				<th width="80">Fabric Color</th>
				<th width="80">Order Plan Cut Qty</th>
				<th width="80">Lab Dip No</th>
				<th width="80">Body Part</th>
				<th width="150">Fabrication</th>
				<th width="80">GSM</th>
				<th width="80">Dia Type with Fabric Dia</th>
				<th width="80">Color Type</th>
				<th width="80">Finsh Cons.</th>
				<th width="80">Finish Qty</th>
				<th width="80">Grey Cons.</th>
				<th width="80">Grey Qty</th>
				<th width="80">Process Loss %</th>
			</tr>
		</thead>
		<tbody>';
			$m=1;
			foreach ($fabric_data_arr as $row)
			{
				$html .= '<tr id="tr_'.$m.'">
					<td>'. $team_leader_arr[$row['team_leader']].'</td>
					<td>'. $row['job_no'].'</td>
					<td>'. $row['booking_no'].'</td>
					<td>'. $row['po_number'].'</td>
					<td>'. $row['shipment_date'].'</td>
					<td>'. $colorArr[$row['gmts_color_id']].'</td>
					<td>'. $colorArr[$row['fabric_color_id']].'</td>
					<td>'. $row['plan_cut_qty'].'</td>
					<td>'. $row['labdip_no'].'</td>
					<td>'. $body_part[$row['body_part_id']].'</td>
					<td>'. $row['fabric_des'].'</td>
					<td>'. $row['gsm'].'</td>
					<td>'. $row['fabric_dia'].'</td>
					<td>'. $color_type[$row['color_type_id']].'</td>
					<td>'. number_format($row['finsh_cons'],4,'.','').'</td>
					<td>'. number_format($row['fin_fab_qnty'],4,'.','').'</td>
					<td>'. number_format($row['gray_cons'],4,'.','').'</td>
					<td>'. number_format($row['grey_fab_qnty'],4,'.','').'</td>
					<td>'. $row['process_loss_percent'].'</td>
				</tr>';
				$m++;
				$show_row_sub_total = true;
				$total_fin_qty+=$row['fin_fab_qnty'];
				$total_grey_qty+=$row['grey_fab_qnty'];
			}
		$html .='</tbody>';
		$html .='<tfoot>';
		if($show_row_sub_total == true)
		{
			$html .='<tr>
				<td colspan="15" align="right"><b>Total:</b></td>
				<td><b>'. number_format($total_fin_qty,4,".","").'</b></td>
				<td>&nbsp;</td>
				<td><b>'. number_format($total_grey_qty,4,".","").'</b></td>
				<td>&nbsp;</td>
			</tr>';
		}
		$html .='</tfoot>
	</table>';
	
	foreach (glob("fwdwfbr_*.xls") as $filename) {
		@unlink($filename);
	}
	$name=time();
	$filename="fwdwfbr_".$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, $html);
	echo "$filename####$filename";
	exit();

}