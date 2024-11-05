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
    echo create_drop_down( "cbo_buyer_id", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active=1 and buy.is_deleted=0 $buyer_cond and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, "","0","" );
    exit();
}
if ($action=="load_drop_down_suplier")
{
    if($data==5 || $data==3){
       echo create_drop_down( "cbo_party_id", 150, "select id,company_name from lib_company where status_active=1 and is_deleted=0 and core_business not in(3) order by company_name", "id,company_name",1, "-- Select Supplier --", $selected);
    }
    else{
       echo create_drop_down( "cbo_party_id", 150, " select a.id,a.supplier_name from lib_supplier a,lib_supplier_tag_company b,lib_company  c  where a.id=b.supplier_id and c.id=b.tag_company and a.status_active =1 and a.is_deleted=0 and c.core_business not in(3) group by a.id,a.supplier_name","id,supplier_name", 1, "-- Select Supplier --", $selected, "");

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
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	<script>
		function js_set_value(id, booking_no) {
			$('#hidden_booking_id').val(id);
			$('#hidden_booking_no').val(booking_no);
			parent.emailwindow.hide();
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
                            <th width="60">Year</th>
                            <th width="150">Buyer</th>
                            <th width="150">Search By</th>
                            <th id="search_by_td_up" width="140">Enter Booking No</th>
                            <th>
                                <input type="reset" name="reset" id="reset" value="Reset" style="width:70px" class="formbutton"/>
                                <input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes" value="<?=$cbo_company_id.'_'.$cbo_fab_booking_type; ?>">
                                <input type="hidden" name="hidden_booking_id" id="hidden_booking_id" class="text_boxes" value="">
                                <input type="hidden" name="hidden_booking_no" id="hidden_booking_no" class="text_boxes" value="">
                            </th>
                        </thead>
                        <tr class="general">
                            <td><?=create_drop_down( "cbo_year", 60, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" ); ?></td>
                            <td>
                                <?
                                    $search_by_arr = array(1 => "Booking No", 2 => "Buyer Order", 3 => "Job No", 4 => "Style Ref.", 5 => "Internal Ref", 6 => "File No");
                                    $dd = "change_search_event(this.value, '0*0*0*0*0*0', '0*0*0*0*0*0', '../../') ";
    
                                echo create_drop_down("cbo_buyer_name", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name", "id,buyer_name", 1, "-- All Buyer --", '', "", $disable); ?>
                            </td>
                            <td><?=create_drop_down("cbo_search_by", 150, $search_by_arr, "", 0, "--Select--", $selected, $dd, 0); ?></td>
                            <td id="search_by_td"><input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common"/></td>
                            <td><input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('cbo_year').value, 'create_booking_search_list_view', 'search_div', 'date_wise_fabric_booking_report_controller', 'setFilterGrid(\'tbl_list_search\',-1);');" style="width:70px;"/>
                            </td>
                        </tr>
                    </table>
                    
                </fieldset>
            </form>
            <div id="search_div" align="left"></div>
        </div>
    </body>
    </html>
    <?
    exit();
}

if ($action == "create_booking_search_list_view")
{
	$data = explode("_", $data);
	$search_string = "%" . trim($data[0]) . "%";
	$search_by = $data[1];
	$company_id = $data[2];
	$bookingType = $data[3];
	$buyer_id = $data[4];
	$booking_year = $data[5];

	$buyer_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');

	if ($buyer_id == 0) $buyer_id = "%%";

	if (trim($data[0]) != "") {
		if ($search_by == 1) {
			$search_field_cond = "and a.booking_no like '$search_string'";
			$search_field_cond_sample = "and s.booking_no_prefix_num='".trim($data[0])."'";
		} else if ($search_by == 2) {
			$search_field_cond = "and b.po_number like '$search_string'";
			$search_field_cond_sample = "";
		} else if ($search_by == 3) {
			$search_field_cond = "and b.job_no_mst like '$search_string'";
			$search_field_cond_sample = "";
		} else if ($search_by == 5) {
			$search_field_cond = "and b.grouping like '$search_string'";
			$search_field_cond_sample = "";
		} else if ($search_by == 6) {
			$search_field_cond = "and b.file_no like '$search_string'";
			$search_field_cond_sample = "";
		} else {
			$search_field_cond = "and d.style_ref_no like '$search_string'";
			$search_field_cond_sample = "";
		}
	} else {
		$search_field_cond = ""; $search_field_cond_sample = "";
	}
	$po_arr = array();
	$po_data = sql_select("select b.id, b.po_number, b.pub_shipment_date, b.grouping, b.file_no, b.po_quantity, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
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
	$variable_set_allocation = return_field_value("allocation", "variable_settings_inventory", "company_name=$company_id and variable_list=18 and item_category_id = 1");
	$booking_type_cond = ($variable_set_allocation==1)?" and a.booking_type not in(1,4)":"";
	if (trim($data[0]) != "" && ($search_by == 2 || $search_by == 3 ||  $search_by == 4 || $search_by == 5 || $search_by == 6)) {
		$sql = "select a.id, a.booking_no,a.booking_no_prefix_num, a.booking_date, a.buyer_id, a.entry_form, c.po_break_down_id, a.item_category, a.delivery_date, c.job_no as job_no_mst, 0 as type,a.booking_type, a.is_short, $year_cond from wo_booking_mst a, wo_booking_dtls c, wo_po_break_down b, wo_po_details_master d where a.booking_no=c.booking_no and c.po_break_down_id=b.id and d.id=b.job_id and a.company_id=$company_id and a.buyer_id like '$buyer_id' and a.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $search_field_cond $booking_year_condition group by a.id,a.booking_no, a.booking_date, a.buyer_id,a.entry_form,c.po_break_down_id,a.item_category, a.delivery_date, c.job_no,a.insert_date,a.booking_no_prefix_num, a.booking_type,a.is_short order by a.id";
	} else {
		$sql = "select a.id, a.booking_no,a.booking_no_prefix_num, a.booking_date, a.buyer_id,a.entry_form,c.po_break_down_id, a.item_category, a.delivery_date, c.job_no as job_no_mst, 0 as type, a.booking_type, a.is_short, $year_cond from wo_booking_mst a, wo_booking_dtls c, wo_po_break_down b where a.booking_no=c.booking_no and c.po_break_down_id=b.id and a.company_id=$company_id and a.buyer_id like '$buyer_id' and a.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $search_field_cond $booking_year_condition group by a.id,a.booking_no, a.booking_date, a.buyer_id,a.entry_form,c.po_break_down_id,a.item_category, a.delivery_date, c.job_no,a.insert_date,a.booking_no_prefix_num,a.booking_type, a.is_short
		union all
		SELECT s.id, s.booking_no,s.booking_no_prefix_num, s.booking_date, s.buyer_id,s.entry_form_id as entry_form, null as po_break_down_id, s.item_category, s.delivery_date, null as job_no_mst, 1 as type, 0 as booking_type, 0 as is_short, $year_cond_non_order
		FROM wo_non_ord_samp_booking_mst s, wo_non_ord_samp_booking_dtls t
		WHERE s.booking_no=t.booking_no and s.company_id=$company_id and s.buyer_id like '$buyer_id' and s.status_active =1 and s.is_deleted=0 and t.status_active=1 and t.is_deleted=0 and s.item_category=2 and (s.fabric_source=1 OR t.fabric_source=1) $search_field_cond_sample $booking_year_non_order_condition
		group by s.id, s.booking_no, s.booking_no_prefix_num, s.booking_date, s.buyer_id,s.entry_form_id, s.item_category, s.delivery_date, s.insert_date
		order by id, type desc";
	}
	//echo $sql;
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
				<tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none; cursor:pointer"
					onClick="js_set_value(<?=$data[0]; ?>,'<?=$data[1]; ?>');">
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

    $companyArr = return_library_array("select id,company_name from lib_company ","id","company_name");
    $buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
	$colorArr = return_library_array( "select id, color_name from lib_color", "id", "color_name");
    $supplierArr = return_library_array("select a.id,a.supplier_name from lib_supplier a,lib_supplier_tag_company b,lib_company c where a.id=b.supplier_id and c.id=b.tag_company and a.status_active =1 and a.is_deleted=0 and c.core_business not in(3) group by a.id,a.supplier_name","id","supplier_name");
    $is_short = array(1=>'Short',2=>'Main');
    $fabric_booking_type = array(118 => "Main Fabric Booking",108=>'Partial Fabric Booking', 88 => "Short Fabric Booking", 89 => "Sample Fabric Booking - With Order", 0 => 'Sample Fabric Booking - Without Order');

    
    //die;
	if($type==0)//show Button
	{
		if($cbo_booking_type == 1 || $cbo_booking_type == 2 || $cbo_booking_type == 3 || $cbo_booking_type == 4 || $cbo_booking_type == 0){
			if($cbo_booking_type == 1) $type_cond = " and e.booking_type=1 and e.is_short=2 and e.entry_form in (118,86)";
			if($cbo_booking_type == 2) $type_cond = " and e.booking_type=1 and e.is_short=2 and e.entry_form=108";
			if($cbo_booking_type == 3) $type_cond = " and e.booking_type=1 and e.is_short=1 and e.entry_form=88";
			if($cbo_booking_type == 4) $type_cond = "and e.booking_type=4 and e.is_short=2 and e.entry_form=89";
			/*if($cbo_booking_type == 5) $type_cond = "and e.entry_form=90"; */
			if($cbo_booking_type == 0) $type_cond = "and e.entry_form in (118,108,88,89,90,86)";
			if($cbo_company_id != 0) $company_cond=" and e.company_id='$cbo_company_id'"; else $company_cond="";
			if($cbo_party_id != 0) $party_cond=" and e.supplier_id='$cbo_party_id'"; else $party_cond="";
			if($cbo_pay_mode != 0) $pay_cond=" and e.pay_mode='$cbo_pay_mode'"; else $pay_cond="";
			if($cbo_buyer_id != 0) $buyerCond=" and e.buyer_id='$cbo_buyer_id'"; else $buyerCond="";
			if($cbo_fabric_source != 0) $fabric_source_Cond=" and e.fabric_source='$cbo_fabric_source'"; else $fabric_source_Cond="";
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
				$item_category_cond=" and e.item_category=$cbofabricnature";
			}
	
		   $get_booking = "Select e.booking_no, e.booking_date, a.insert_date, e.update_date, e.is_approved, e.is_short, e.short_booking_type, e.buyer_id, a.job_no, a.style_ref_no, e.item_category, e.fabric_source, e.source, e.pay_mode, e.supplier_id, sum(f.grey_fab_qnty) as booking_qty, f.responsible_person, f.reason, sum(f.amount) as booking_amount, d.uom, e.entry_form, nvl(e.po_break_down_id,f.po_break_down_id) as po_break_down_id from wo_booking_dtls f join wo_booking_mst e on f.booking_no=e.booking_no join wo_pre_cost_fabric_cost_dtls d on d.id=f.pre_cost_fabric_cost_dtls_id join wo_pre_cost_mst c on d.job_no=c.job_no join wo_po_details_master a on a.job_no = c.job_no where f.status_active=1 and f.is_deleted=0  and d.is_deleted=0 and d.status_active=1 and a.is_deleted=0 and a.status_active=1 and e.is_deleted=0 and e.status_active=1 $company_cond $buyerCond $type_cond $fabric_source_Cond $booking_date_cond $party_cond $pay_cond $bookingCond $item_category_cond group by e.booking_no, e.booking_date, e.is_short, e.short_booking_type, e.buyer_id, a.job_no, e.update_date, e.is_approved, a.style_ref_no, e.item_category, e.fabric_source, e.source, e.pay_mode, e.supplier_id, f.responsible_person, f.reason, d.uom, e.entry_form, a.insert_date, e.po_break_down_id, f.po_break_down_id order by e.booking_date asc";
			//echo $get_booking; die;
		}
		else{
			if($cbo_booking_type == 5) $type_cond = " and a.booking_type=4 and (a.entry_form_id is null or a.entry_form_id =0)";
			if($cbo_company_id != 0) $company_cond=" and a.company_id='$cbo_company_id'"; else $company_cond="";
			if($cbo_party_id != 0) $party_cond=" and a.supplier_id='$cbo_party_id'"; else $party_cond="";
			if($cbo_pay_mode != 0) $pay_cond=" and a.pay_mode='$cbo_pay_mode'"; else $pay_cond="";
			if($cbo_buyer_id != 0) $buyerCond=" and a.buyer_id='$cbo_buyer_id'"; else $buyerCond="";
			if($cbo_fabric_source != 0) $fabric_source_Cond=" and a.fabric_source='$cbo_fabric_source'"; else $fabric_source_Cond="";
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
				$item_category_cond=" and a.item_category=$cbofabricnature";
				//wo_non_ord_samp_booking_mst
			}
			$get_booking = "Select a.booking_no, a.booking_date, a.update_date, a.insert_date, a.is_approved, a.is_short, '0' as short_booking_type, a.buyer_id, '' as job_no, '' as style_ref_no, '' as garments_nature, a.fabric_source, a.source, a.pay_mode, a.supplier_id, b.uom, sum(b.grey_fabric) as booking_qty, sum(b.amount) as booking_amount, a.entry_form_id as entry_form, a.po_break_down_id from wo_non_ord_samp_booking_mst a join wo_non_ord_samp_booking_dtls b on a.booking_no=b.booking_no where a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $company_cond $buyerCond $fabric_source_Cond $booking_date_cond $type_cond $party_cond $pay_cond $bookingCond $item_category_cond group by a.booking_no, a.booking_date, a.is_short, a.buyer_id, a.fabric_source, a.update_date, a.insert_date, a.is_approved, a.source, a.pay_mode, a.supplier_id, b.uom, a.entry_form_id, a.po_break_down_id";
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
		$booking_date_arr =array();
		$po_break_down_ids="";
		foreach ($sql_data as $value) {
			$booking_date_arr[$value[csf('booking_no')]]['booking_no'] = $value[csf('booking_no')];
			$booking_date_arr[$value[csf('booking_no')]]['update_date'] = $value[csf('update_date')];
			$booking_date_arr[$value[csf('booking_no')]]['insert_date'] = $value[csf('insert_date')];
			$booking_date_arr[$value[csf('booking_no')]]['is_approved'] = $value[csf('is_approved')];
			$booking_date_arr[$value[csf('booking_no')]]['booking_date'] = $value[csf('booking_date')];
			$booking_date_arr[$value[csf('booking_no')]]['booking_type'] = $value[csf('booking_type')];
			$booking_date_arr[$value[csf('booking_no')]]['short_booking_type'] = $value[csf('short_booking_type')];
			$booking_date_arr[$value[csf('booking_no')]]['short_booking_type'] = $value[csf('short_booking_type')];
			$booking_date_arr[$value[csf('booking_no')]]['buyer_id'] = $value[csf('buyer_id')];
			$booking_date_arr[$value[csf('booking_no')]]['style_ref_no'] = $value[csf('style_ref_no')];
			$booking_date_arr[$value[csf('booking_no')]]['job_no'] = $value[csf('job_no')];
			$booking_date_arr[$value[csf('booking_no')]]['item_category'] = $value[csf('item_category')];
			$booking_date_arr[$value[csf('booking_no')]]['fabric_source'] = $value[csf('fabric_source')];
			$booking_date_arr[$value[csf('booking_no')]]['source'] = $value[csf('source')];
			$booking_date_arr[$value[csf('booking_no')]]['pay_mode'] = $value[csf('pay_mode')];
			$booking_date_arr[$value[csf('booking_no')]]['supplier_id'] = $value[csf('supplier_id')];
			$booking_date_arr[$value[csf('booking_no')]]['entry_form'] = $value[csf('entry_form')];
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
			<table width="1950px" cellspacing="0">
				<tr class="form_caption" style="border:none;">
					<td colspan="21" align="center" style="border:none;font-size:14px; font-weight:bold" ><? echo $report_title; ?></td>
				</tr>
				<tr style="border:none;">
					<td colspan="21" align="center" style="border:none; font-size:16px; font-weight:bold"><? echo $companyArr[$cbo_company_id]; ?></td>
				</tr>
			</table>
			<table width="2350px" cellspacing="0" border="1" class="rpt_table" rules="all">
				<thead>
					<tr style="font-size:13px">
						<th width="30">SL.</th>
						<th width="100">Company</th>
						<th width="100">Booking Number</th>
						<th width="100">Revise No</th>
						<th width="70">Booking Insert Date</th>
						<th width="70">1st Appv. Date</th>
						<th width="70">Last Appv. Date</th>
						<th width="100">Po Received Date</th>
						<th width="100">Booking Type</th>
						<th width="100">Short Booking Type</th>
						<th width="100">Buyer</th>
						<th width="100">Style Ref.</th>
						<th width="100">Job</th>
						<th width="100">Fabric Nature</th>
						<th width="100">Fabric Source</th>
						<th width="100">Source</th>
						<th width="100">Paymode</th>
						<th width="100">Party Name</th>
						<th width="100">Booking Qty (Kg)</th>
						<th width="100">Booking Qty (Yds)</th>
						<th width="100">Booking Qty (Mtr)</th>
						<th width="100" >Booking Amount ($)</th>
						<th width="100" >Reason</th>
						<th width="100" >Responsible Person</th>
					 </tr>
				</thead>
			</table>
			<!-- <div style="width:1850px; max-height:300px; overflow-y:scroll" id="scroll_body"> -->
			<table width="2350px" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_body">
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
						<td width="30"><? echo $i; ?></td>
						<td width="100"><? echo $companyArr[$cbo_company_id]; ?></td>
						<td width="100" title="<? echo $is_approved;?>"><? echo $row['booking_no']; ?></td>
						<td width="100"><?
												if($revise_approved_row[csf('approved_no')]>1)
												{ 
													echo $revise_approved_row[csf('approved_no')]-1;
												}
						 ?></td>
						<td width="70" title="<? echo $row['booking_date'];?>"><? echo change_date_format($row['booking_date'], "d-M-y", "-", 1); ?></td>
						<td width="70" title="<? echo $first_approve_dateTime;?>"><? echo change_date_format($first_approve_date, "d-M-y", "-", 1); ?> </td>
						<td width="70" title="<? echo $last_approve_dateTime;?>"><? echo change_date_format($last_approve_date, "d-M-y", "-", 1); ?></td>
						<td width="100"><? echo change_date_format($po_received_date, "d-M-y", "-", 1); ?></td>
						<td width="100"><? echo $fabric_booking_type[$entry_form]; ?></td>
						<td width="100"><? echo $short_booking_type[$row['short_booking_type']]; ?></td>
						<td width="100"><? echo $buyerArr[$row['buyer_id']]; ?></td>
						<td width="100"><? echo $row['style_ref_no']; ?></td>
						<td width="100"><? echo $row['job_no']; ?></td>
						<td width="100"><? echo $item_category[$row['item_category']]; ?></td>
						<td width="100"><? echo $fabric_source[$row['fabric_source']] ?></td>
						<td width="100"><? echo $source[$row['source']]; ?></td>
						<td width="100"><? echo $pay_mode[$row['pay_mode']]; ?></td>
						<td width="100"><? echo $party_name; ?></td>
						<td width="100" align="right"><? echo number_format($booking_qty_kg,2); ?></td>
						<td width="100" align="right"><? echo number_format($booking_qty_yds,2); ?></td>
						<td width="100" align="right"><? echo number_format($booking_qty_mtr,2); ?></td>
						<td width="100" align="right"><? echo number_format($row['booking_amount'],2); ?></td>
	
						<td width="100" align="center"><? echo implode(",", $row['reason']); ?></td>
						<td width="100" align="center"><? echo implode(",", $row['responsible_person']); ?></td>
					</tr>
	
				  <? $i++; $tot_rows++;
				} ?>
			</table>
			<table width="2350px" cellspacing="0" border="1" class="rpt_table" rules="all" id="report_table_footer" >
				<tr style="font-size:13px">
					<td width="30"></td>
					<td width="100"></td>
					<td width="100"></td>
					<td width="100"></td>
	
					<td width="70"></td>
					<td width="70"></td>
					<td width="70"></td>
	
					<td width="100"></td>
					<td width="100"></td>
					<td width="100"></td>
					<td width="100"></td>
					<td width="100"></td>
					<td width="100"></td>
					<td width="100"></td>
					<td width="100"></td>
					<td width="100"></td>
					<td width="100"></td>
					<td width="100"></td>
					<td width="100" id="total_booking_qty_kg" align="right"><? echo number_format($total_booking_qty_kg,2); ?></td>
					<td width="100" id="total_booking_qty_yds" align="right"><? echo number_format($total_booking_qty_yds,2); ?></td>
					<td width="100" id="total_booking_qty_mtr" align="right"><? echo number_format($total_booking_qty_mtr,2); ?></td>
					<td width="100" id="total_booking_amount" align="right"><? echo number_format($total_booking_amount,2); ?></td>
					<td width="100"></td>
					<td width="100"></td>
				 </tr>
			</table>
		</div>
		<?
	}
	else if($type==1)//Details Button
	{
		if($cbo_booking_type == 1 || $cbo_booking_type == 2 || $cbo_booking_type == 3 || $cbo_booking_type == 4 || $cbo_booking_type == 0){
			if($cbo_booking_type == 1) $type_cond = " and e.booking_type=1 and e.is_short=2 and e.entry_form in (118,86)";
			if($cbo_booking_type == 2) $type_cond = " and e.booking_type=1 and e.is_short=2 and e.entry_form=108";
			if($cbo_booking_type == 3) $type_cond = " and e.booking_type=1 and e.is_short=1 and e.entry_form=88";
			if($cbo_booking_type == 4) $type_cond = "and e.booking_type=4 and e.is_short=2 and e.entry_form is null";
			/*if($cbo_booking_type == 5) $type_cond = "and e.entry_form=90"; */
			if($cbo_booking_type == 0) $type_cond = "and e.entry_form in (118,108,88,89,90,86)";
			if($cbo_company_id != 0) $company_cond=" and e.company_id='$cbo_company_id'"; else $company_cond="";
			if($cbo_party_id != 0) $party_cond=" and e.supplier_id='$cbo_party_id'"; else $party_cond="";
			if($cbo_pay_mode != 0) $pay_cond=" and e.pay_mode='$cbo_pay_mode'"; else $pay_cond="";
			if($cbo_buyer_id != 0) $buyerCond=" and e.buyer_id='$cbo_buyer_id'"; else $buyerCond="";
			if($cbo_fabric_source != 0) $fabric_source_Cond=" and e.fabric_source='$cbo_fabric_source'"; else $fabric_source_Cond="";
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
				$item_category_cond=" and e.item_category=$cbofabricnature";
			}
	
		   $get_booking = "Select e.booking_no, e.booking_type, e.is_short, e.short_booking_type, e.booking_date, e.buyer_id, a.job_no, a.style_ref_no, e.fabricstructure, f.fabric_color_id, d.body_part_id, d.color_type_id, d.construction, d.composition, d.gsm_weight, f.dia_width, sum(f.grey_fab_qnty) as booking_qty, sum(f.fin_fab_qnty) as finQty, f.process_loss_percent, f.responsible_person, f.reason, d.uom, e.item_category, e.entry_form, nvl(e.po_break_down_id,f.po_break_down_id) as po_break_down_id from wo_booking_dtls f join wo_booking_mst e on f.booking_no=e.booking_no join wo_pre_cost_fabric_cost_dtls d on d.id=f.pre_cost_fabric_cost_dtls_id join wo_pre_cost_mst c on d.job_no=c.job_no join wo_po_details_master a on a.job_no = c.job_no where f.status_active=1 and f.is_deleted=0  and d.is_deleted=0 and d.status_active=1 and a.is_deleted=0 and a.status_active=1 and e.is_deleted=0 and e.status_active=1 $company_cond $buyerCond $type_cond $fabric_source_Cond $booking_date_cond $party_cond $pay_cond $bookingCond $item_category_cond group by e.booking_no, e.booking_type, e.is_short, e.short_booking_type, e.booking_date, e.buyer_id, a.job_no, a.style_ref_no, e.fabricstructure, f.fabric_color_id, d.body_part_id, d.color_type_id, d.construction, d.composition, d.gsm_weight, f.dia_width, f.process_loss_percent, f.responsible_person, f.reason, d.uom, e.item_category, e.entry_form, e.po_break_down_id, f.po_break_down_id order by e.booking_date asc";
			//echo $get_booking; die;
		}
		else{
			if($cbo_booking_type == 5) $type_cond = " and a.booking_type=4 and (a.entry_form_id is null or a.entry_form_id =0)";
			if($cbo_company_id != 0) $company_cond=" and a.company_id='$cbo_company_id'"; else $company_cond="";
			if($cbo_party_id != 0) $party_cond=" and a.supplier_id='$cbo_party_id'"; else $party_cond="";
			if($cbo_pay_mode != 0) $pay_cond=" and a.pay_mode='$cbo_pay_mode'"; else $pay_cond="";
			if($cbo_buyer_id != 0) $buyerCond=" and a.buyer_id='$cbo_buyer_id'"; else $buyerCond="";
			if($cbo_fabric_source != 0) $fabric_source_Cond=" and a.fabric_source='$cbo_fabric_source'"; else $fabric_source_Cond="";
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
				$item_category_cond=" and a.item_category=$cbofabricnature";
				//wo_non_ord_samp_booking_mst
			}
			$get_booking = "Select a.booking_no, '0' as booking_type, a.is_short, '0' as short_booking_type, a.booking_date, a.buyer_id, '' as job_no, '' as style_ref_no, b.fabric_color as fabric_color_id, b.body_part as body_part_id, '0' as color_type_id, b.construction, b.composition, b.gsm_weight, b.dia_width, sum(b.grey_fabric) as booking_qty, sum(b.finish_fabric) as finQty, b.process_loss as process_loss_percent, '' as responsible_person, '' as reason, b.uom, a.item_category, a.entry_form_id as entry_form  from wo_non_ord_samp_booking_mst a join wo_non_ord_samp_booking_dtls b on a.booking_no=b.booking_no where a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $company_cond $buyerCond $fabric_source_Cond $booking_date_cond $type_cond $party_cond $pay_cond $bookingCond $item_category_cond group by a.booking_no, a.is_short, a.booking_date, a.buyer_id, b.fabric_color, b.body_part, b.construction, b.composition, b.gsm_weight, b.dia_width,  b.process_loss, b.uom, a.item_category, a.entry_form_id order by a.booking_date asc";
		}
		//echo $get_booking;
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
			<table width="1920px" cellspacing="0">
				<tr class="form_caption" style="border:none;">
					<td colspan="21" align="center" style="border:none;font-size:14px; font-weight:bold" ><?=$report_title; ?></td>
				</tr>
				<tr style="border:none;">
					<td colspan="21" align="center" style="border:none; font-size:16px; font-weight:bold"><?=$companyArr[$cbo_company_id]; ?></td>
				</tr>
			</table>
			<table width="1920px" cellspacing="0" border="1" class="rpt_table" rules="all">
				<thead>
					<tr style="font-size:13px">
						<th width="30">SL.</th>
						<th width="100">Booking Type</th>
						<th width="100">Booking Number</th>
						<th width="100">M.Booking No</th>
						<th width="70">Booking Date</th>
                        <th width="100">Buyer</th>
						<th width="100">Style Ref.</th>
						<th width="100">Job</th>
                        <th width="100">Fabric Nature</th>
                        
						<th width="70">Color</th>
						<th width="70">Body Part</th>
						<th width="100">Color Type</th>
						<th width="100">Fabric Type</th>
						<th width="100">Yarn Composition</th>

						<th width="100">GSM</th>
						<th width="100">Dia</th>
						<th width="100">Grey Qty</th>
						<th width="100">Finished Qty</th>
						<th width="100">Process Loss</th>
						<th width="100">Responsible person</th>
						<th>Reason</th>
					 </tr>
				</thead>
			</table>
			<!-- <div style="width:1850px; max-height:300px; overflow-y:scroll" id="scroll_body"> -->
			<table width="1920px" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_body">
				<? $i=1; $tot_rows=0;
				$total_booking_qty_kg =0; $total_booking_qty_mtr =0; $total_booking_qty_yds= 0;
				foreach ($sql_data as $row)
				{
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
						<td width="100" style="word-break:break-all"><?=$booking_type; ?></td>
						<td width="100" style="word-break:break-all"><?=$row[csf('booking_no')]; ?></td>
						<td width="100" style="word-break:break-all"><p style="word-wrap: break-word;width: 100px;"><?=$mbooking_no; ?></p></td>
						<td width="70"><?=change_date_format($row[csf('booking_date')], "d-M-y", "-", 1); ?></td>
                        <td width="100" style="word-break:break-all"><?=$buyerArr[$row[csf('buyer_id')]]; ?></td>
						<td width="100" style="word-break:break-all"><?=$row[csf('style_ref_no')]; ?></td>
						<td width="100" style="word-break:break-all"><?=$row[csf('job_no')]; ?></td>
                        <td width="100" style="word-break:break-all"><?=$item_category[$row[csf('item_category')]]; ?></td>
                        
                        <td width="70" style="word-break:break-all"><?=$colorArr[$row[csf('fabric_color_id')]]; ?></td>
						<td width="70" style="word-break:break-all"><?=$body_part[$row[csf('body_part_id')]]; ?></td>
						<td width="100" style="word-break:break-all"><?=$color_type[$row[csf('color_type_id')]]; ?></td>
						<td width="100" style="word-break:break-all"><?=$row[csf('construction')]; ?></td>
						<td width="100" style="word-break:break-all"><?=$row[csf('composition')]; ?></td>
                        
						<td width="100"><?=$row[csf('gsm_weight')]; ?></td>
						<td width="100"><?=$row[csf('dia_width')]; ?></td>
						<td width="100" align="right"><?=number_format($row[csf('booking_qty')],2); ?></td>
						<td width="100" align="right"><?=number_format($row[csf('finQty')],2); ?></td>
						<td width="100" align="right"><?=number_format($row[csf('process_loss_percent')],2); ?></td>
						<td width="100" style="word-break:break-all"><?=$row[csf('responsible_person')]; ?></td>
						<td style="word-break:break-all"><?=$row[csf('reason')]; ?></td>
					</tr>
	
				  <? $i++; $tot_rows++;
				} ?>
			</table>
			<!--<table width="1920px" cellspacing="0" border="1" class="rpt_table" rules="all" id="report_table_footer" >
				<tr style="font-size:13px">
					<td width="30">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="70">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="70">&nbsp;</td>
					<td width="70">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="100" align="right"><?// echo number_format($total_booking_qty_kg,2); ?></td>
					<td width="100" align="right"><?// echo number_format($total_booking_qty_yds,2); ?></td>
					<td width="100" align="right"><?// echo number_format($total_booking_qty_mtr,2); ?></td>
					<td width="100" align="right"><?// echo number_format($total_booking_amount,2); ?></td>
					<td>&nbsp;</td>
				 </tr>
			</table>-->
		</div>
		<?
	
	}
	else if($type==2)//Fabric Wise Button
	{
		if($cbo_booking_type == 1 || $cbo_booking_type == 2 || $cbo_booking_type == 3 || $cbo_booking_type == 4 || $cbo_booking_type == 0){
			if($cbo_booking_type == 1) $type_cond = " and e.booking_type=1 and e.is_short=2 and e.entry_form=118";
			if($cbo_booking_type == 2) $type_cond = " and e.booking_type=1 and e.is_short=2 and e.entry_form=108";
			if($cbo_booking_type == 3) $type_cond = " and e.booking_type=1 and e.is_short=1 and e.entry_form=88";
			if($cbo_booking_type == 4) $type_cond = "and e.booking_type=4 and e.is_short=2 and e.entry_form=89";
			/*if($cbo_booking_type == 5) $type_cond = "and e.entry_form=90"; */
			if($cbo_booking_type == 0) $type_cond = "and e.entry_form in (118,108,88,89,90)";
			if($cbo_company_id != 0) $company_cond=" and e.company_id='$cbo_company_id'"; else $company_cond="";
			if($cbo_party_id != 0) $party_cond=" and e.supplier_id='$cbo_party_id'"; else $party_cond="";
			if($cbo_pay_mode != 0) $pay_cond=" and e.pay_mode='$cbo_pay_mode'"; else $pay_cond="";
			if($cbo_buyer_id != 0) $buyerCond=" and e.buyer_id='$cbo_buyer_id'"; else $buyerCond="";
			if($cbo_fabric_source != 0) $fabric_source_Cond=" and e.fabric_source='$cbo_fabric_source'"; else $fabric_source_Cond="";
			if($txt_date_from!="" && $txt_date_to!="") $booking_date_cond="and e.booking_date between '$txt_date_from' and '$txt_date_to'"; else $booking_date_cond="";
	
			if($txt_booking_no_id!='')
			{
				if ($txt_booking_no_id=="") $bookingCond=""; else $bookingCond=" and e.id in ( $txt_booking_no_id )";
			}
			else
			{
				if ($txt_booking_no=="") $bookingCond=""; else $bookingCond=" and e.booking_no_prefix_num='$txt_booking_no'";
			}
	
		   $get_booking = "Select a.job_no, a.style_ref_no, e.booking_no, e.booking_date, a.insert_date, e.update_date, e.is_approved, e.is_short, e.short_booking_type, e.buyer_id, e.item_category, e.fabric_source, e.source, e.pay_mode, e.supplier_id, e.entry_form, e.delivery_date, f.grey_fab_qnty as booking_qty, f.responsible_person, f.reason, f.amount as booking_amount, d.uom, d.lib_yarn_count_deter_id as libfabid, d.fabric_description, c.po_received_date, c.pub_shipment_date from wo_booking_dtls f join wo_booking_mst e on f.booking_no=e.booking_no join wo_pre_cost_fabric_cost_dtls d on d.id=f.pre_cost_fabric_cost_dtls_id join wo_po_break_down c on d.job_no=c.job_no_mst and f.po_break_down_id=c.id join wo_po_details_master a on a.job_no = c.job_no_mst where f.status_active=1 and f.is_deleted=0  and d.is_deleted=0 and d.status_active=1 and a.is_deleted=0 and a.status_active=1 and e.is_deleted=0 and e.status_active=1 $company_cond $buyerCond $type_cond $fabric_source_Cond $booking_date_cond $party_cond $pay_cond $bookingCond order by e.booking_date asc";
			//echo $get_booking; die;
		}
		else{
			if($cbo_booking_type == 5) $type_cond = " and a.booking_type=4 and (a.entry_form_id is null or a.entry_form_id =0)";
			if($cbo_company_id != 0) $company_cond=" and a.company_id='$cbo_company_id'"; else $company_cond="";
			if($cbo_party_id != 0) $party_cond=" and a.supplier_id='$cbo_party_id'"; else $party_cond="";
			if($cbo_pay_mode != 0) $pay_cond=" and a.pay_mode='$cbo_pay_mode'"; else $pay_cond="";
			if($cbo_buyer_id != 0) $buyerCond=" and a.buyer_id='$cbo_buyer_id'"; else $buyerCond="";
			if($cbo_fabric_source != 0) $fabric_source_Cond=" and a.fabric_source='$cbo_fabric_source'"; else $fabric_source_Cond="";
			if($txt_date_from!="" && $txt_date_to!="") $booking_date_cond="and a.booking_date between '$txt_date_from' and '$txt_date_to'"; else $booking_date_cond="";
			if($txt_booking_no_id!='')
			{
				if ($txt_booking_no_id=="") $bookingCond=""; else $bookingCond=" and a.id in ( $txt_booking_no_id )";
			}
			else
			{
				if ($txt_booking_no=="") $bookingCond=""; else $bookingCond=" and a.booking_no_prefix_num='$txt_booking_no'";
			}
			$get_booking = "Select a.booking_no, a.booking_date, a.update_date, a.insert_date, a.is_approved, a.is_short, '0' as short_booking_type, a.buyer_id, '' as job_no, '' as style_ref_no, '' as garments_nature, a.fabric_source, a.source, a.pay_mode, a.supplier_id, b.uom, sum(b.grey_fabric) as booking_qty, sum(b.amount) as booking_amount, a.entry_form_id as entry_form, a.po_break_down_id from wo_non_ord_samp_booking_mst a join wo_non_ord_samp_booking_dtls b on a.booking_no=b.booking_no where a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $company_cond $buyerCond $fabric_source_Cond $booking_date_cond $type_cond $party_cond $pay_cond $bookingCond group by a.booking_no, a.booking_date, a.is_short, a.buyer_id, a.fabric_source, a.update_date, a.insert_date, a.is_approved, a.source, a.pay_mode, a.supplier_id, b.uom, a.entry_form_id, a.po_break_down_id";
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
			$booking_date_arr[$value[csf('booking_no')]][$value[csf('libfabid')]]['update_date'] = $value[csf('update_date')];
			$booking_date_arr[$value[csf('booking_no')]][$value[csf('libfabid')]]['insert_date'] = $value[csf('insert_date')];
			$booking_date_arr[$value[csf('booking_no')]][$value[csf('libfabid')]]['is_approved'] = $value[csf('is_approved')];
			$booking_date_arr[$value[csf('booking_no')]][$value[csf('libfabid')]]['booking_date'] = $value[csf('booking_date')];
			$booking_date_arr[$value[csf('booking_no')]][$value[csf('libfabid')]]['booking_type'] = $value[csf('booking_type')];
			$booking_date_arr[$value[csf('booking_no')]][$value[csf('libfabid')]]['short_booking_type'] = $value[csf('short_booking_type')];
			$booking_date_arr[$value[csf('booking_no')]][$value[csf('libfabid')]]['short_booking_type'] = $value[csf('short_booking_type')];
			$booking_date_arr[$value[csf('booking_no')]][$value[csf('libfabid')]]['buyer_id'] = $value[csf('buyer_id')];
			$booking_date_arr[$value[csf('booking_no')]][$value[csf('libfabid')]]['style_ref_no'] = $value[csf('style_ref_no')];
			$booking_date_arr[$value[csf('booking_no')]][$value[csf('libfabid')]]['job_no'] = $value[csf('job_no')];
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
			<table width="2500px" cellspacing="0">
				<tr class="form_caption" style="border:none;">
					<td colspan="25" align="center" style="border:none;font-size:14px; font-weight:bold" ><? echo $report_title; ?></td>
				</tr>
				<tr style="border:none;">
					<td colspan="25" align="center" style="border:none; font-size:16px; font-weight:bold"><? echo $companyArr[$cbo_company_id]; ?></td>
				</tr>
			</table>
			<table width="2500px" cellspacing="0" border="1" class="rpt_table" rules="all">
				<thead>
					<tr>
						<th width="30">SL.</th>
						<th width="100">Company</th>
						<th width="100">Booking Number</th>
						<th width="70">Booking Insert Date</th>
						<th width="70">1st Appv. Date</th>
						<th width="70">Last Appv. Date</th>
						<th width="70">Po Received Date</th>
                        <th width="70">1st Ship Date</th>
                        <th width="70">Last Ship Date</th>
                        <th width="70">Delivery Date</th>
						<th width="100">Booking Type</th>
						<th width="100">Short Booking Type</th>
						<th width="100">Buyer</th>
						<th width="100">Style Ref.</th>
						<th width="100">Job</th>
						<th width="100">Fabric Nature</th>
						<th width="100">Fabric Source</th>
						<th width="100">Source</th>
						<th width="100">Paymode</th>
						<th width="100">Party Name</th>
                        <th width="250">Fabrication</th>
						<th width="100">Booking Qty (Kg)</th>
						<th width="100">Booking Qty (Yds)</th>
						<th width="100">Booking Qty (Mtr)</th>
						<th>Booking Amount ($)</th>
					 </tr>
				</thead>
			</table>
			<div style="width:2500px; max-height:600px; overflow-y:scroll" id="scroll_body"> 
			<table width="2482px" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_body">
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
							<td width="100" style="word-break:break-all"><?=$companyArr[$cbo_company_id]; ?></td>
							<td width="100" style="word-break:break-all" title="<?=$is_approved; ?>"><?=$bookingno; ?></td>
							<td width="70" title="<?=$row['booking_date']; ?>"><?=change_date_format($row['booking_date'], "d-M-y", "-", 1); ?></td>
							<td width="70" style="word-break:break-all" title="<?=$first_approve_dateTime; ?>"><?=change_date_format($first_approve_date, "d-M-y", "-", 1); ?> </td>
							<td width="70" style="word-break:break-all" title="<?=$last_approve_dateTime;?>"><?=change_date_format($last_approve_date, "d-M-y", "-", 1); ?></td>
							<td width="70"><?=change_date_format($po_received_date, "d-M-y", "-", 1); ?></td>
							<td width="70"><? /*echo change_date_format($first_ship_date, "d-M-y", "-", 1);*/ echo date('d-M-y',$fs_date); ?></td>
							<td width="70"><? /*=change_date_format($last_ship_date, "d-M-y", "-", 1);*/ echo date('d-M-y',$ls_date) ?></td>
							<td width="70"><?=change_date_format($row['delivery_date'], "d-M-y", "-", 1); ?></td>
							<td width="100" style="word-break:break-all"><?=$fabric_booking_type[$entry_form]; ?></td>
							<td width="100" style="word-break:break-all"><?=$short_booking_type[$row['short_booking_type']]; ?></td>
							<td width="100" style="word-break:break-all"><?=$buyerArr[$row['buyer_id']]; ?></td>
							<td width="100" style="word-break:break-all"><?=$row['style_ref_no']; ?></td>
							<td width="100" style="word-break:break-all"><?=$row['job_no']; ?></td>
							<td width="100" style="word-break:break-all"><?=$item_category[$row['item_category']]; ?></td>
							<td width="100" style="word-break:break-all"><?=$fabric_source[$row['fabric_source']] ?></td>
							<td width="100" style="word-break:break-all"><?=$source[$row['source']]; ?></td>
							<td width="100" style="word-break:break-all"><?=$pay_mode[$row['pay_mode']]; ?></td>
							<td width="100" style="word-break:break-all"><?=$party_name; ?></td>
							<td width="250" style="word-break:break-all"><?=$row['fabric']; ?></td>
							<td width="100" align="right"><?=number_format($booking_qty_kg,2); ?></td>
							<td width="100" align="right"><?=number_format($booking_qty_yds,2); ?></td>
							<td width="100" align="right"><?=number_format($booking_qty_mtr,2); ?></td>
							<td align="right"><?=number_format($row['booking_amount'],2); ?></td>
						</tr>
		
						<? $i++; $tot_rows++;
					}
				} ?>
			</table>
            </div>
			<table width="2500px" cellspacing="0" border="1" class="tbl_bottom" rules="all">
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
                    <td width="250">&nbsp;</td>
					<td width="100" id="value_qtykg" align="right"><? echo number_format($total_booking_qty_kg,2); ?></td>
					<td width="100" id="value_qtyyds" align="right"><? echo number_format($total_booking_qty_yds,2); ?></td>
					<td width="100" id="value_qtymtr" align="right"><? echo number_format($total_booking_qty_mtr,2); ?></td>
					<td id="value_amount" align="right"><? echo number_format($total_booking_amount,2); ?></td>
				 </tr>
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