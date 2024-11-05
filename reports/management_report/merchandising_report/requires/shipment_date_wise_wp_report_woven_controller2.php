<?
session_start();
require_once('../../../../includes/common.php');
require_once('../../../../includes/class4/class.conditions.php');
require_once('../../../../includes/class4/class.reports.php');
require_once('../../../../includes/class4/class.trims.php');

extract($_REQUEST);
if ($_SESSION['logic_erp']['user_id'] == "") {
    header("location:login.php");
    die;
}

$date = date('Y-m-d');
$user_id=$_SESSION['logic_erp']['user_id'];
if ($action == "load_drop_down_buyer") {
    echo create_drop_down("cbo_buyer_name", 110, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "load_drop_down( 'requires/shipment_date_wise_wp_report_woven_controller', this.value, 'load_drop_down_season', 'season_td');");
    exit();
}

if ($action=="load_drop_down_season")
{
	echo create_drop_down( "cbo_season_id", 80, "select id, season_name from lib_buyer_season where buyer_id='$data' and status_active =1 and is_deleted=0 order by season_name ASC","id,season_name", 1, "-Season-", "", "" );
	exit();
}

if ($action == "load_drop_down_team_member") {
    echo create_drop_down("cbo_team_member", 120, "select id,team_member_name from lib_mkt_team_member_info where team_id='$data' and status_active=1 and is_deleted=0 order by team_member_name", "id,team_member_name", 1, "- Team Member-", $selected, "");
}

$buyer_short_name_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');
$company_short_name_arr = return_library_array("select id,company_name from lib_company", 'id', 'company_name');
$imge_arr = return_library_array("select master_tble_id,image_location from common_photo_library where file_type=1", 'master_tble_id', 'image_location');
//$dealing_merchant_array = return_library_array("select id, team_member_name from lib_mkt_team_member_info","id","team_member_name");

$SqlResult = sql_select("select id, team_member_name,member_contact_no from lib_mkt_team_member_info");
foreach ($SqlResult as $row) {
    if ($row[csf('member_contact_no')]) {
        $phone = "<br>Ph:" . $row[csf('member_contact_no')];
    } else {
        $phone = "";
    }
    $dealing_merchant_array[$row[csf('id')]] = $row[csf('team_member_name')] . $phone;
}

$team_library = return_library_array("select id, team_name from lib_marketing_team", "id", "team_name");
$country_library = return_library_array("select id, country_name from  lib_country", "id", "country_name");

if ($action == "report_generate") {

    $job_no = str_replace("'", "", $txt_job_no);
    $txt_style_ref = str_replace("'", "", $txt_style_ref);
    $txt_order_no = str_replace("'", "", $txt_order_no);
    $year_id = str_replace("'", "", $cbo_year);
    $cbo_team_name = str_replace("'", "", $cbo_team_name);
    $cbo_team_member = str_replace("'", "", $cbo_team_member);
    $type_search = str_replace("'", "", $cbo_search_by);
    $cbo_ls_sc = str_replace("'", "", $cbo_ls_sc);
    $txt_file_no = str_replace("'", "", $txt_file_no);
    $txt_ref_no = str_replace("'", "", $txt_ref_no);
	$cbo_season_id = str_replace("'", "", $cbo_season_id);
    $cbo_group_by = str_replace("'", "", $cbo_group_by);
    $cbo_order_status = str_replace("'","", $cbo_order_status);
    $cbo_brand_id = str_replace("'", "", $cbo_brand_id);
    $cbo_season_year = str_replace("'", "", $cbo_season_year);

    if($cbo_group_by==2){
        $group_cond="";

    }else{
        $group_cond="";
    }

    if($db_type==0)
    {
        if ($cbo_season_year==0) $season_year_cond=""; else $season_year_cond=" and a.season_year=$cbo_season_year";
    }
    elseif($db_type==2)
    { 
        if ($cbo_season_year==0) $season_year_cond=""; else $season_year_cond=" and a.season_year=$cbo_season_year";
    }
    if ($db_type == 0) {
        if ($year_id != 0)
            $year_cond = " and year(a.insert_date)=$year_id";
        else
            $year_cond = "";
        $toDay = date("Y-m-d");
    }
    else {
        if ($year_id != 0)
            $year_cond = " and to_char(a.insert_date,'YYYY')=$year_id";
        else
            $year_cond = "";
        $toDay = date("Y-M-d");
    }
    if ($cbo_brand_id == 0)
    $brand_id_cond = "";
    else
    $brand_id_cond = " and a.brand_id=$cbo_brand_id "; 

    if ($job_no == "")
        $job_no_cond = "";
    else
        $job_no_cond = " and a.job_no_prefix_num in ($job_no) ";
    if (trim($txt_style_ref) != "")
        $style_ref_cond = "%" . trim($txt_style_ref) . "%";
    else
        $style_ref_cond = "%%";
    if (trim($txt_order_no) != "")
        $order_no_cond = "%" . trim($txt_order_no) . "%";
    else
        $order_no_cond = "%%";
    if (trim($txt_file_no) != "")
        $file_no_cond = "and b.file_no in ($txt_file_no)";
    else
        $file_no_cond = "";
    if (trim($txt_ref_no) != "")
        $ref_no_cond = "and b.grouping in ('$txt_ref_no')";
    else
        $ref_no_cond = "";
    if ($cbo_team_name == 0)
        $team_name_cond = "";
    else
        $team_name_cond = " and a.team_leader='$cbo_team_name'";
    if ($cbo_team_member == 0)
        $team_member_cond = "";
    else
        $team_member_cond = " and a.dealing_marchant='$cbo_team_member'";

	if ($cbo_season_id== 0) $season_id_cond = ""; else $season_id_cond = " and a.season_buyer_wise='$cbo_season_id'";
    if ($cbo_order_status== 0)
        $order_status_cond = "";
    else
        $order_status_cond = " and b.is_confirmed='$cbo_order_status'";

    //echo $order_status_cond.'system';die;

    if (str_replace("'", "", $cbo_search_by) == 1 || str_replace("'", "", $cbo_search_by) == 2) {
        if (str_replace("'", "", trim($txt_date_from)) == "" && str_replace("'", "", trim($txt_date_to)) == "")
            $date_cond = "";
        else
            $date_cond = " and b.pub_shipment_date between $txt_date_from and $txt_date_to";
    }
    else {
        if (str_replace("'", "", trim($txt_date_from)) == "" && str_replace("'", "", trim($txt_date_to)) == "")
            $date_cond = "";
        else
            $date_cond = " and c.country_ship_date between $txt_date_from and $txt_date_to";
    }

    $searchCond = "";
    if (str_replace("'", "", $cbo_company_name) != 0)
        $searchCond .= " and a.company_name=$cbo_company_name";

    if (str_replace("'", "", $cbo_buyer_name) == 0) {
        if ($_SESSION['logic_erp']["data_level_secured"] == 1) {
            if ($_SESSION['logic_erp']["buyer_id"] != "")
                $searchCond .= " and a.buyer_name in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
        }
    }
    else {
        $searchCond .= " and a.buyer_name=$cbo_buyer_name";
    }

    if (str_replace("'", "", $cbo_search_by) == 1 || str_replace("'", "", $cbo_search_by) == 3)
        $td_width = 100;
    else if (str_replace("'", "", $cbo_search_by) == 2)
        $td_width = 60;

    if (str_replace("'", "", $cbo_search_by) == 3)
        $td_width_c = 100;
    else
        $td_width_c = 0;
        $season_arr=return_library_array("select id,season_name from lib_buyer_season","id","season_name");
		$brand_arr=return_library_array("select id,brand_name from lib_buyer_brand","id","brand_name");
    ob_start();
    ?>
    <div align="center">
        <table style="margin-left:5px; margin-top:5px" id="table_notes">
            <tr>
                <td bgcolor="orange" height="15" width="30"></td>
                <td>Maximum 10 Days Remaing To Ship</td>
                <td bgcolor="green" height="15" width="30">&nbsp;</td>
                <td>On Time Shipment</td>
                <td bgcolor="#2A9FFF" height="15" width="30"></td>
                <td>Delay shipment</td>
                <td bgcolor="red" height="15" width="30"></td>
                <td>Shipment Date Over & Pending</td>
                <td bgcolor="#FF99CC" height="15" width="30"></td>
                <td>Delay Approved</td>
                
                
                <td bgcolor="#FF0000" height="15" width="30"></td>
                <td>Buyer Inspection Fail</td>
                <td bgcolor="#4EB97F" height="15" width="30"></td>
                <td>Buyer Inspection Pass after Fail</td>
                <!--<td bgcolor="#048AD5" height="15" width="30"></td>
                <td>Buyer Inspection Re-check</td>-->
            </tr>
        </table>

        <table cellpadding="0"  cellspacing="0" width="<? echo $td_width_c + 1960; ?>">
            <tr>
                <td align="center" class="form_caption" width="100%" colspan="20"><b><?php
                                if (str_replace("'", "", $cbo_company_name) != 0) {
                                    echo $company_short_name_arr[str_replace("'", "", $cbo_company_name)];
                                }
                      ?></b>
                </td>
            </tr>
        </table>
        <?
		 if(str_replace("'", "", $cbo_search_by)==3)
		 {
		  echo   "<b style='color:#F00;font-size:larger'>Not Development</b>";die;
		 }
        if (str_replace("'", "", $cbo_search_by) == 3) {
            ?>
            <table width="4490" id="table_header_1" border="1" class="rpt_table" rules="all">
                <thead>
                    <tr>
                        <th width="40">SL</th>
                        <th width="60">Job No</th>
                        <th width="60">Buyer</th>
                        <th width="100">Brand</th>
                        <th width="100">Season</th>
                        <th width="70">Season Year</th>
                        <th width="100">Team Leader</th>
                        <th width="100">Dealing Merchant</th>
                        <th width="<? echo $td_width; ?>">Order no</th>
                        <th width="80">File no</th>
                        <th width="80">Internal Ref:/Master Style</th>
                        <th width="50">Agent</th>
                        <th width="50">Image</th>
                        <th width="100">Item Name</th>
                        <th width="50">Sew. SMV</th>
                        <th width="100">Style Name</th>
                        <th width="100">Country Name</th>
                        <th width="90">Order Quantity</th>
                        <th width="100">Order Value</th>
                        <th width="100">Order Qnty (Pcs)</th>
                        <th width="80">PO Insert Date</th>
                        <th width="60">UOM</th>
                        <th width="60">Unit Price</th>
                        <th width="80">PO Recv. Date</th>
                        <th width="80">Shipment Date</th>
                        <th width="80">Lead Time</th>
                        <th width="60">Days in Hand</th>
                        <th width="80">Sample Approved</th>
                        <th width="80">Lapdip Approved</th>
                        <th width="80">Accessories Approved</th>
                        <th width="80">Embel. Approved</th>
                        <? if ($garmentBtn != 2) { ?>
                            <th width="80">Knit Fabric Booking</th>
                        <? } ?>
                        <th width="80">Woven Fabric Booking</th>
                        <? if ($garmentBtn != 2) { ?>
                            <th width="80">Knitting Finished</th>
                        <? } ?>
                        <th width="80">LC/SC Received</th>
                        <? if ($garmentBtn != 2) { ?>
                            <th width="80">Finished Fab Recv</th>
                        <? } ?>
                        <th width="80">Woven Fin. Fab Recv</th>
                        <th width="80">Trims Received</th>
                        <th width="80">Cutting Finished</th>
                        <th width="80">Print Completed</th>
                        <th width="80">Emb. Completed</th>
                        <th width="80">Wash Completed</th>
                        <th width="80">Special Completed</th>
                        <th width="80">Sewing Finished</th>
                        <th width="80">Iron Output</th>
                        <th width="80">Packing finishing completed</th>
                        <th width="80">Buyer Inspection</th>
                        <th width="100">Ship Qnty(Pcs) As Per Ex-Fact.</th>
                        <th width="80">Actual Shipment Date</th>
                        <th width="90">Ship Qnty. (Pcs) As Per Invoice</th>
                        <th width="100">Ship Value (Gross)</th>
                        <th width="100">Balance Ship Qnty As Per Invoice</th>
                        <th width="120">Balance Ship Qnty As Per Ex-factory</th>
                        <th width="">Remarks</th>
                    </tr>
                </thead>
            </table>
            <div style="max-height:400px; overflow-y:scroll; width:4510px" align="left" id="scroll_body">
                <table width="4490" border="1" class="rpt_table" rules="all" id="table_body">
                    <?
                } else {
                    ?>
                    <table width="3960" id="table_header_1" border="1" class="rpt_table" rules="all">
                        <thead>
                            <tr>
                                <th width="40">SL</th>
                                <th width="60">Job No</th>
                                <th width="60">Buyer</th>
                                <th width="100">Brand</th>
                                <th width="100">Season</th>
                                <th width="70">Season Year</th>
                                <th width="100">Team Leader</th>
                                <th width="100">Dealing Merchant</th>
                                <th width="<? echo $td_width; ?>">Order no</th>
                                <th width="80">File no</th>
                                <th width="80">Master Style/Internal Ref</th>
                                <th width="50">Agent</th>
                                <th width="50">Image</th>
                                <th width="100">Item Name</th>
                                <th width="50">Sew. SMV</th>
                                <th width="100">Style Name</th>
                                <th width="90">Order Quantity</th>
                                <th width="100">Order Value</th>
                                <th width="100">Order Qnty (Pcs)</th>
                                <th width="80">PO Insert Date</th>
                                <th width="60">UOM</th>
                                <th width="60">Unit Price</th>
                                <th width="80">PO Recv. Date</th>
                                <th width="80">Shipment Date</th>
                                <th width="80">Lead Time</th>
                                <th width="60">Days in Hand</th>
                                <th width="80">Sample Approved</th>
                                <th width="80">Lapdip Approved</th>
                                <th width="80">Accessories Approved</th>
                                <th width="80">Embel. Approved</th>
                                <? if ($garmentBtn != 2) { ?>
                                    <th width="80">Knit Fabric Booking</th>
                                <? } ?>
                                <th width="80">Woven Fabric Booking</th>
                                <? if ($garmentBtn != 2) { ?>
                                    <th width="80">Knitting Finished</th>
                                <? } ?>
                                <th width="80">LC/SC Received</th>
                                <? if ($garmentBtn != 2) { ?>
                                    <th width="80">Finished Fab Recv</th>

                                <? } ?>
                                <th width="80">Woven Fin. Fab Recv</th>
                                <th width="80">Trims Received</th>
                                <th width="80">Cutting Finished</th>
                                <th width="80">Print & Emb. Completed</th>
                                <th width="80">Sewing Finished</th>
                                <th width="80">Wash Booking</th>
                                <th width="80">Iron Output</th>
                                <th width="80">Packing finishing completed</th>
                                <th width="80">Woven finishing completed</th>
                                 
                                <th width="80">Buyer Inspection</th>
                                <th width="100">Ship Qnty(Pcs) As Per Ex-Fact.</th>
                                <th width="80">Actual Shipment Date</th>
                                <th width="90">Ship Qnty. (Pcs) As Per Invoice</th>
                                <th width="100">Ship Value (Gross)</th>
                                <th width="120">Balance Ship Qnty As Per Invoice</th>
                                <th width="">Remarks</th>
                            </tr>
                        </thead>
                    </table>
                    <div style="max-height:400px; overflow-y:scroll; width:3980px" align="left" id="scroll_body">
                        <table width="3960" border="1" class="rpt_table" rules="all" id="table_body">
                            <?
                        }

                        if (str_replace("'", "", $cbo_search_by) == 1 || str_replace("'", "", $cbo_search_by) == 2 || str_replace("'", "", $cbo_search_by) == 3) {
                            $poIds = '';
                            if (str_replace("'", "", $cbo_search_by) == 1) {//Order wise report==================
                                if ($db_type == 0) {
                                    $jobSql = "select a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.agent_name, a.style_ref_no, a.job_quantity, a.product_category, a.job_no, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant,a.set_smv, b.id, b.is_confirmed, b.po_number, b.po_quantity, b.plan_cut, b.pub_shipment_date, b.po_received_date, DATEDIFF(b.pub_shipment_date, '$date') date_diff_1, DATEDIFF(b.shipment_date,'$date') date_diff_2, b.unit_price, b.po_total_price, b.details_remarks, b.shiping_status, b.shipment_date, DATEDIFF(pub_shipment_date,po_received_date) as lead_time,b.grouping,b.file_no,b.insert_date,a.brand_id,a.season_buyer_wise,a.season_year  from	wo_po_details_master a, wo_po_break_down b ,wo_pre_cost_mst c where	a.job_no=b.job_no_mst and a.job_no=c.job_no and b.job_no_mst=c.job_no and c.entry_from in(158,425,521,520) and a.status_active=1 and b.status_active=1 and c.status_active=1 and b.po_number like '$order_no_cond' and a.style_ref_no like '$style_ref_cond' $date_cond $searchCond $job_no_cond $team_name_cond $team_member_cond $year_cond $file_no_cond $ref_no_cond $season_id_cond $season_year_cond $brand_id_cond  $order_status_cond		order by a.id desc, b.pub_shipment_date,a.job_no_prefix_num,b.id";
                                } else {
                                    $jobSql = "select a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.agent_name, a.style_ref_no, a.job_quantity, a.product_category, a.job_no, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant,a.set_smv, b.id, b.is_confirmed, b.po_number, b.po_quantity, b.plan_cut, b.pub_shipment_date, b.po_received_date, trunc(b.pub_shipment_date-SYSDATE) date_diff_1, trunc(b.shipment_date-SYSDATE) date_diff_2, b.unit_price, b.po_total_price, b.details_remarks, b.shiping_status, b.shipment_date, trunc(pub_shipment_date-po_received_date) as lead_time,b.grouping,b.file_no,b.insert_date,a.brand_id,a.season_buyer_wise,a.season_year from 		wo_po_details_master a, wo_po_break_down b,wo_pre_cost_mst c where 	a.job_no=b.job_no_mst  and a.job_no=c.job_no and b.job_no_mst=c.job_no and c.entry_from in(158,425,521,520) and a.status_active=1 and b.status_active=1 and c.status_active=1 and b.po_number like '$order_no_cond' and a.style_ref_no like '$style_ref_cond' $date_cond $searchCond $job_no_cond $team_name_cond $team_member_cond $year_cond $file_no_cond $ref_no_cond $season_id_cond $season_year_cond $brand_id_cond  $order_status_cond order by a.id desc, b.pub_shipment_date,a.job_no_prefix_num, b.id";
                                }
                            }
                            else if (str_replace("'", "", $cbo_search_by) == 2) { //style wise report
                                if ($db_type == 0) {
                                    $jobSql = "select a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.agent_name, a.style_ref_no, a.job_quantity, a.product_category, a.job_no, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant, group_concat(b.id) as id, b.is_confirmed, b.po_number, sum(b.po_quantity*a.total_set_qnty) as po_quantity, sum(b.plan_cut*a.total_set_qnty) as plan_cut, MIN(b.pub_shipment_date) as pub_shipment_date, MIN(b.po_received_date) as po_received_date, DATEDIFF(b.pub_shipment_date,'$date') date_diff_1, DATEDIFF(b.shipment_date,'$date') date_diff_2, b.unit_price, sum(b.po_total_price) as po_total_price, b.details_remarks, b.shiping_status,group_concat(b.grouping) as grouping, group_concat(b.file_no) as file_no,b.insert_date,a.set_smv,a.brand_id,a.season_buyer_wise,a.season_year from wo_po_details_master a, wo_po_break_down b ,wo_pre_cost_mst c where a.job_no=b.job_no_mst  and a.job_no=c.job_no and b.job_no_mst=c.job_no and c.entry_from in(158,425,521,520) and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.style_ref_no like '$style_ref_cond' $date_cond $searchCond $job_no_cond $year_cond $file_no_cond $ref_no_cond $team_name_cond $team_member_cond $season_id_cond $season_year_cond $brand_id_cond  $order_status_cond group by b.job_no_mst order by a.id desc, b.job_no_mst,a.brand_id,a.season_buyer_wise,a.season_year";
                                } else {
                                    $jobSql = "select a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.agent_name, a.style_ref_no, a.job_quantity, a.product_category, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant, listagg(cast(b.id as varchar2(4000)),',') within group (order by b.id) as id, sum(b.po_quantity*a.total_set_qnty) as po_quantity, sum(b.plan_cut*a.total_set_qnty) as plan_cut, MIN(b.pub_shipment_date) as pub_shipment_date, MIN(b.po_received_date) as po_received_date, trunc(MIN(b.pub_shipment_date)-SYSDATE) date_diff_1, trunc(MIN(b.shipment_date)-SYSDATE) date_diff_2, sum(b.po_total_price) as po_total_price, max(b.shiping_status) as shiping_status,listagg(cast(b.grouping as varchar2(4000)),',') within group (order by b.id) as grouping,listagg(cast(b.file_no as varchar2(4000)),',') within group (order by b.id) as file_no,max(b.insert_date) as insert_date,a.set_smv,b.details_remarks,a.brand_id,a.season_buyer_wise,a.season_year from 		wo_po_details_master a, wo_po_break_down b,wo_pre_cost_mst c where 							a.job_no=b.job_no_mst  and a.job_no=c.job_no and b.job_no_mst=c.job_no and c.entry_from in(158,425,521,520)  and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.style_ref_no like '$style_ref_cond' $date_cond $searchCond $job_no_cond  $year_cond $file_no_cond $ref_no_cond $team_name_cond $team_member_cond $season_id_cond $season_year_cond $brand_id_cond  $order_status_cond group by a.job_no, a.job_no_prefix_num, a.job_no,a.set_smv, a.company_name, a.buyer_name, a.agent_name, a.style_ref_no, a.job_quantity, a.product_category, a.job_no, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant,b.details_remarks,a.brand_id,a.season_buyer_wise,a.season_year order by a.job_no desc";
                                    //order by a.id desc, a.job_no
                                }
                            }
							
                           // echo $jobSql;die;
                            $jobSqlResult = sql_select($jobSql);
                            foreach ($jobSqlResult as $row) {
                                $poIds .= $row[csf('id')] . ",";
                            }

                            $noOfPos = count(explode(",", chop($poIds, ',')));
                            //echo $noOfPos;die;
                            $poIds = chop($poIds, ',');
                            $poIds_cond = "";
                            $poIds_cond_emb = "";
                            $poIds_cond_inv = ""; $poIds_cond_inv2 = "";
                            $poIds_cond_prod = "";
                            $poIds_cond_tna = "";
                            $poIds_cond_lc = "";
                            if ($db_type == 2 && $noOfPos > 1000) {
                                $poIds_cond_pre = " and (";
                                $poIds_cond_suff .= ")";
                                $poIdsArr = array_chunk(explode(",", $poIds), 999);
                                foreach ($poIdsArr as $ids) {
                                    $ids = implode(",", $ids);
                                    $poIds_cond .= " b.po_break_down_id in($ids) or ";
                                    $poIds_cond_emb .= " a.id in($ids) or ";
                                    $poIds_cond_inv .= " po_breakdown_id in($ids) or ";
                                    $poIds_cond_prod .= " po_break_down_id in($ids) or ";
                                    $poIds_cond_tna .= " po_number_id in($ids) or ";
                                    $poIds_cond_lc .= " wo_po_break_down_id in($ids) or ";
									$poIds_cond_inv2 .= " c.po_breakdown_id in($ids) or ";
									$poIds_cond_inv3 .= " a.po_break_down_id in($ids) or ";
                                }

                                $poIds_cond = $poIds_cond_pre . chop($poIds_cond, 'or ') . $poIds_cond_suff;
                                $poIds_cond_emb = $poIds_cond_pre . chop($poIds_cond_emb, 'or ') . $poIds_cond_suff;
                                $poIds_cond_inv = $poIds_cond_pre . chop($poIds_cond_inv, 'or ') . $poIds_cond_suff;
                                $poIds_cond_prod = $poIds_cond_pre . chop($poIds_cond_prod, 'or ') . $poIds_cond_suff;
                                $poIds_cond_tna = $poIds_cond_pre . chop($poIds_cond_tna, 'or ') . $poIds_cond_suff;
                                $poIds_cond_lc = $poIds_cond_pre . chop($poIds_cond_lc, 'or ') . $poIds_cond_suff;
								$poIds_cond_inv2 = $poIds_cond_pre . chop($poIds_cond_inv2, 'or ') . $poIds_cond_suff;
								$poIds_cond_inv3 = $poIds_cond_pre . chop($poIds_cond_inv3, 'or ') . $poIds_cond_suff;
                            } else {
                                $poIds_cond = " and b.po_break_down_id in($poIds)";
                                $poIds_cond_emb = " and a.id in($poIds)";
                                $poIds_cond_inv = " and po_breakdown_id in($poIds)";
                                $poIds_cond_prod = " and po_break_down_id in($poIds)";
                                $poIds_cond_tna = " and po_number_id in($poIds)";
                                $poIds_cond_lc = " and wo_po_break_down_id in($poIds)";
								$poIds_cond_inv2 = " and c.po_breakdown_id in($poIds)";
								$poIds_cond_inv3 = " and a.po_break_down_id in($poIds)";
                            }

                            //Cutting Finished 	Print & Emb. Completed 	Sewing Finished Finishing Input Finishing Completed query -----------------------------//
                            $sqlOrder = "SELECT  a.id,
							SUM(CASE WHEN b.emb_name=1 and b.emb_type!=0 THEN a.po_quantity*b.cons_dzn_gmts ELSE 0 END) AS print,
							SUM(CASE WHEN b.emb_name=2 and b.emb_type!=0 THEN a.po_quantity*b.cons_dzn_gmts ELSE 0 END) AS emb,
							SUM(CASE WHEN b.emb_name=3 and b.emb_type!=0 THEN a.po_quantity*b.cons_dzn_gmts ELSE 0 END) AS wash,
							SUM(CASE WHEN b.emb_name=4 and b.emb_type!=0 THEN a.po_quantity*b.cons_dzn_gmts ELSE 0 END) AS special FROM
							wo_po_break_down a, wo_pre_cost_embe_cost_dtls b WHERE a.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $poIds_cond_emb group by a.id";
                            $sql_order = sql_select($sqlOrder);
                            $poReqArr = array();
                            foreach ($sql_order as $resultRow) {
                                $poReqArr[$resultRow[csf("id")]][1] = $resultRow[csf("print")];
                                $poReqArr[$resultRow[csf("id")]][2] = $resultRow[csf("emb")];
                                $poReqArr[$resultRow[csf("id")]][3] = $resultRow[csf("wash")];
                                $poReqArr[$resultRow[csf("id")]][4] = $resultRow[csf("special")];
                            }

                            $prod_sql = "SELECT po_break_down_id,
						sum(CASE WHEN production_type ='1' THEN production_quantity END) AS cutting,
						sum(CASE WHEN production_type ='3' THEN production_quantity END) AS printing,
						sum(CASE WHEN production_type ='3' and embel_name=1 THEN production_quantity END) AS prnt,
						sum(CASE WHEN production_type ='3' and embel_name=2 THEN production_quantity END) AS embel,
						sum(CASE WHEN production_type ='3' and embel_name=3 THEN production_quantity END) AS wash,
						sum(CASE WHEN production_type ='3' and embel_name=4 THEN production_quantity END) AS special,
						sum(CASE WHEN production_type ='5' THEN production_quantity END) AS sewingout,
						sum(CASE WHEN production_type ='6' THEN production_quantity END) AS finishinput,
						sum(CASE WHEN production_type ='7' THEN production_quantity END) AS ironoutput,
						sum(CASE WHEN production_type  in(8) THEN production_quantity END) AS finishcompleted,
						sum(CASE WHEN production_type  in(80) THEN production_quantity END) AS wvn_finishcompleted
						from pro_garments_production_mst
						where status_active=1 and is_deleted=0 $poIds_cond_prod group by po_break_down_id";
                            //echo $prod_sql;die;
                            $prodSQLresult = sql_select($prod_sql);
                            $prodArr = array();
                            foreach ($prodSQLresult as $key => $val) {
                                $prodArr[$val[csf('po_break_down_id')]]['cutting'] = $val[csf('cutting')];
                                $prodArr[$val[csf('po_break_down_id')]]['printing'] = $val[csf('printing')];
                                $prodArr[$val[csf('po_break_down_id')]]['prnt'] = $val[csf('prnt')];
                                $prodArr[$val[csf('po_break_down_id')]]['embel'] = $val[csf('embel')];
                                $prodArr[$val[csf('po_break_down_id')]]['wash'] = $val[csf('wash')];
                                $prodArr[$val[csf('po_break_down_id')]]['special'] = $val[csf('special')];
                                $prodArr[$val[csf('po_break_down_id')]]['sewingout'] = $val[csf('sewingout')];
                                $prodArr[$val[csf('po_break_down_id')]]['finishinput'] += $val[csf('finishinput')];
                                $prodArr[$val[csf('po_break_down_id')]]['ironoutput'] += $val[csf('ironoutput')];
                                $prodArr[$val[csf('po_break_down_id')]]['finishcompleted'] += $val[csf('finishcompleted')];
								 $prodArr[$val[csf('po_break_down_id')]]['wvn_finishcompleted'] += $val[csf('wvn_finishcompleted')];
                            }

                            //buyer inspection query----------------------------------//
                            $insp_qnty = "select inspection_status,po_break_down_id, sum(inspection_qnty) as inspection_qnty from pro_buyer_inspection where is_deleted=0 and status_active=1 $poIds_cond_prod group by po_break_down_id,inspection_status";// inspection_status=1 and 
                            $inspSQLresult = sql_select($insp_qnty);
                            $inspArr = array();$inspectionStatusArr = array();
                            foreach ($inspSQLresult as $key => $val) {
                                if($val[csf('inspection_status')]==1){
									$inspArr[$val[csf('po_break_down_id')]] = $val[csf('inspection_qnty')];
								}
                                if($val[csf('inspection_status')]==3){
									$inspectionStatusArr[$val[csf('po_break_down_id')]] = $val[csf('inspection_status')];
								}
                            }
							
							
                            //echo $insp_qnty;die;
                            //Ship Qnty(Pcs)As Per Ex-Fact query----------------------------------//
                            $ex_qnty = "select po_break_down_id, sum(ex_factory_qnty) as ex_factory_qnty, MAX(ex_factory_date) as ex_factory_date from pro_ex_factory_mst where is_deleted=0 and status_active=1 $poIds_cond_prod group by po_break_down_id";
                            $exSQLresult = sql_select($ex_qnty);
                            $exArr = array();
                            $exDateArr = array();
                            foreach ($exSQLresult as $key => $val) {
                                $exArr[$val[csf('po_break_down_id')]] = $val[csf('ex_factory_qnty')];
                                $exDateArr[$val[csf('po_break_down_id')]] = $val[csf('ex_factory_date')];
                            }
                            //echo $ex_qnty;die;
                            //Ship Qnty. (Pcs) As Per Invoice Ship Value (Gross) query----------------------------------//
                            $invoice_qnty = "select po_breakdown_id, sum(current_invoice_qnty) as invoice_qnty, sum(current_invoice_value) as invoice_value from com_export_invoice_ship_dtls where is_deleted=0 and status_active=1 and current_invoice_qnty>0 $poIds_cond_inv group by po_breakdown_id";
                            $invoiceSQLresult = sql_select($invoice_qnty);
                            $invoiceArr = array();
                            foreach ($invoiceSQLresult as $key => $val) {
                                $invoiceArr[$val[csf('po_breakdown_id')]]['invoice_qnty'] = $val[csf('invoice_qnty')];
                                $invoiceArr[$val[csf('po_breakdown_id')]]['invoice_value'] = $val[csf('invoice_value')];
                            }
                            //echo $invoice_qnty;die;
                        } else { //Country Ship Date Start
                            //Cutting Finished 	Print & Emb. Completed 	Sewing Finished Finishing Input Finishing Completed query -----------------------------//
                            $sqlOrder = "SELECT  a.id,
									SUM(CASE WHEN b.emb_name=1 and b.emb_type!=0 THEN b.cons_dzn_gmts ELSE 0 END) AS print,
									SUM(CASE WHEN b.emb_name=2 and b.emb_type!=0 THEN b.cons_dzn_gmts ELSE 0 END) AS emb,
									SUM(CASE WHEN b.emb_name=3 and b.emb_type!=0 THEN b.cons_dzn_gmts ELSE 0 END) AS wash,
									SUM(CASE WHEN b.emb_name=4 and b.emb_type!=0 THEN b.cons_dzn_gmts ELSE 0 END) AS special
								FROM
									wo_po_break_down a, wo_pre_cost_embe_cost_dtls b
								WHERE
									a.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id";
                            //echo $sqlOrder;die;
                            $sql_order = sql_select($sqlOrder);
                            $poReqArr = array();
                            foreach ($sql_order as $resultRow) {
                                $poReqArr[$resultRow[csf("id")]][1] = $resultRow[csf("print")];
                                $poReqArr[$resultRow[csf("id")]][2] = $resultRow[csf("emb")];
                                $poReqArr[$resultRow[csf("id")]][3] = $resultRow[csf("wash")];
                                $poReqArr[$resultRow[csf("id")]][4] = $resultRow[csf("special")];
                            }

                           $prod_sql = "SELECT po_break_down_id,country_id,
						sum(CASE WHEN production_type ='1' THEN production_quantity END) AS cutting,
						sum(CASE WHEN production_type ='3' THEN production_quantity END) AS printing,
						sum(CASE WHEN production_type ='3' and embel_name=1 THEN production_quantity END) AS prnt,
						sum(CASE WHEN production_type ='3' and embel_name=2 THEN production_quantity END) AS embel,
						sum(CASE WHEN production_type ='3' and embel_name=3 THEN production_quantity END) AS wash,
						sum(CASE WHEN production_type ='3' and embel_name=4 THEN production_quantity END) AS special,
						sum(CASE WHEN production_type ='5' THEN production_quantity END) AS sewingout,
						sum(CASE WHEN production_type ='7' THEN production_quantity END) AS ironoutput,
						sum(CASE WHEN production_type  in(80,8) THEN production_quantity END) AS finishcompleted
						from pro_garments_production_mst
						where status_active=1 and is_deleted=0 group by po_break_down_id,country_id";
                            $prodSQLresult = sql_select($prod_sql);
                            $prodArr = array();
                            foreach ($prodSQLresult as $key => $val) {
                                $prodArr[$val[csf('po_break_down_id')]][$val[csf('country_id')]]['cutting'] = $val[csf('cutting')];
                                $prodArr[$val[csf('po_break_down_id')]][$val[csf('country_id')]]['printing'] = $val[csf('printing')];
                                $prodArr[$val[csf('po_break_down_id')]][$val[csf('country_id')]]['prnt'] = $val[csf('prnt')];
                                $prodArr[$val[csf('po_break_down_id')]][$val[csf('country_id')]]['embel'] = $val[csf('embel')];
                                $prodArr[$val[csf('po_break_down_id')]][$val[csf('country_id')]]['wash'] = $val[csf('wash')];
                                $prodArr[$val[csf('po_break_down_id')]][$val[csf('country_id')]]['special'] = $val[csf('special')];
                                $prodArr[$val[csf('po_break_down_id')]][$val[csf('country_id')]]['sewingout'] = $val[csf('sewingout')];
                                $prodArr[$val[csf('po_break_down_id')]][$val[csf('country_id')]]['ironoutput'] = $val[csf('ironoutput')];
                                $prodArr[$val[csf('po_break_down_id')]][$val[csf('country_id')]]['finishcompleted'] += $val[csf('finishcompleted')];
                            } //var_dump($prodArr);
                            //buyer inspection query----------------------------------//
                            $insp_qnty = "select po_break_down_id, country_id, sum(inspection_qnty) as inspection_qnty from pro_buyer_inspection where inspection_status=1 and is_deleted=0 and status_active=1 group by po_break_down_id, country_id";
                            $inspSQLresult = sql_select($insp_qnty);
                            $inspArr = array();
                            foreach ($inspSQLresult as $key => $val) {
                                $inspArr[$val[csf('po_break_down_id')]][$val[csf('country_id')]] = $val[csf('inspection_qnty')];
                            }


                            //Ship Qnty(Pcs)As Per Ex-Fact query----------------------------------//
                            $ex_qnty = "select po_break_down_id,country_id, sum(ex_factory_qnty) as ex_factory_qnty, MAX(ex_factory_date) as ex_factory_date from pro_ex_factory_mst where is_deleted=0 and status_active=1 group by po_break_down_id,country_id";
                            $exSQLresult = sql_select($ex_qnty);
                            $exArr = array();
                            $exDateArr = array();
                            foreach ($exSQLresult as $key => $val) {
                                $exArr[$val[csf('po_break_down_id')]][$val[csf('country_id')]] = $val[csf('ex_factory_qnty')];
                                $exDateArr[$val[csf('po_break_down_id')]][$val[csf('country_id')]] = date("Y-m-d", strtotime($val[csf('ex_factory_date')]));
                            }


                            //Ship Qnty. (Pcs) As Per Invoice Ship Value (Gross) query----------------------------------//
                            $invoice_qnty = "select a.po_breakdown_id, c.country_id, sum(a.current_invoice_qnty) as invoice_qnty, sum(a.current_invoice_value) as invoice_value from com_export_invoice_ship_dtls a, com_export_invoice_ship_mst c where a.mst_id=c.id and a.is_deleted=0 and a.status_active=1 and a.current_invoice_qnty>0 group by a.po_breakdown_id,c.country_id";
                            $invoiceSQLresult = sql_select($invoice_qnty);
                            $invoiceArr = array();
                            foreach ($invoiceSQLresult as $key => $val) {
                                $invoiceArr[$val[csf('po_breakdown_id')]][$val[csf('country_id')]]['invoice_qnty'] = $val[csf('invoice_qnty')];
                                $invoiceArr[$val[csf('po_breakdown_id')]][$val[csf('country_id')]]['invoice_value'] = $val[csf('invoice_value')];
                            }
                        }

                        $i = 1;
                        if ($db_type == 0)
                            $date_diff = "DATEDIFF(approval_status_date,target_approval_date)";
                        else
                            $date_diff = "trunc(approval_status_date-target_approval_date)";
                        $sampleSQL = "select po_break_down_id, approval_status, sample_type_id, $date_diff as delay_day from wo_po_sample_approval_info where approval_status<>4 and current_status=1 and is_deleted=0 and status_active=1 $poIds_cond_prod";
                        //echo $sampleSQL;die;
                        $sampleSQLresult = sql_select($sampleSQL);
                        $sampleAPParr = array();
                        foreach ($sampleSQLresult as $key => $val) {
                            if ($val[csf('approval_status')] == 3) {
                                $sampleAPParr[$val[csf('po_break_down_id')]]['apprv_status'] += 1;
                                if ($val[csf("delay_day")] > 0) {
                                    $sampleAPParr[$val[csf('po_break_down_id')]]['delay_status'] += 1;
                                }
                            }
                            $sampleAPParr[$val[csf('po_break_down_id')]]['total_po'] += 1;
                            if($sampleAPParr[$val[csf('po_break_down_id')]][$val[csf('sample_type_id')]] != $val[csf('sample_type_id')]){

                                $sampleAPParr[$val[csf('po_break_down_id')]][$val[csf('sample_type_id')]] = $val[csf('sample_type_id')];

                            }
                        }
                        //print_r($sampleSQLresult);

                        if ($db_type == 0)
                            $date_diff = "DATEDIFF(approval_status_date,lapdip_target_approval_date)";
                        else
                            $date_diff = "trunc(approval_status_date-lapdip_target_approval_date)";
                        //for lapdip approval percentage----------------------------------//
                        $lapdipSQL = "select po_break_down_id,approval_status, $date_diff as delay_day from wo_po_lapdip_approval_info where current_status=1 and is_deleted=0 and status_active=1 $poIds_cond_prod";
                        //echo $lapdipSQL;die;
                        $lapdipSQLresult = sql_select($lapdipSQL);
                        $lapdipAPParr = array();
                        foreach ($lapdipSQLresult as $key => $val) {
                            if ($val[csf('approval_status')] == 3) {
                                $lapdipAPParr[$val[csf('po_break_down_id')]]['apprv_status'] += 1;
                                if ($val[csf("delay_day")] > 0) {
                                    $lapdipAPParr[$val[csf('po_break_down_id')]]['delay_status'] += 1;
                                }
                            }
                            $lapdipAPParr[$val[csf('po_break_down_id')]]['total_po'] += 1;
                        }
                        //for trims/accessories approval percentage----------------------------------//
                        $trimsSQL = "select po_break_down_id,SUM(CASE WHEN approval_status=3 THEN 1 ELSE 0 END) as apprv_status,
							SUM(CASE WHEN approval_status<>4 THEN 1 ELSE 0 END) as total_po
							from wo_po_trims_approval_info where current_status=1 and is_deleted=0 and status_active=1 $poIds_cond_prod group by po_break_down_id";
                        $trimsSQLresult = sql_select($trimsSQL);
                        $trimsAPParr = array();
                        foreach ($trimsSQLresult as $key => $val) {
                            $trimsAPParr[$val[csf('po_break_down_id')]]['apprv_status'] = $val[csf('apprv_status')];
                            $trimsAPParr[$val[csf('po_break_down_id')]]['total_po'] = $val[csf('total_po')];
                        }
                        //for Embellishment Approval approval percentage----------------------------------//
                         $embelSQL = "select po_break_down_id,SUM(CASE WHEN approval_status=3 THEN 1 ELSE 0 END) as apprv_status,
							SUM(CASE WHEN approval_status<>4 THEN 1 ELSE 0 END) as total_po
							from wo_po_embell_approval where current_status=1 and is_deleted=0 and status_active=1 $poIds_cond_prod group by po_break_down_id";
                        $embelSQLresult = sql_select($embelSQL);
                        $embelAPParr = array();
                        foreach ($embelSQLresult as $key => $val) {
                            $embelAPParr[$val[csf('po_break_down_id')]]['apprv_status'] = $val[csf('apprv_status')];
                            $embelAPParr[$val[csf('po_break_down_id')]]['total_po'] = $val[csf('total_po')];
                        }
                        //for Fabric Booking percentage----------------------------------//
                          $fabricSQL = "select a.item_category, b.po_break_down_id, SUM(b.grey_fab_qnty) as grey_fab_qnty, SUM(b.fin_fab_qnty) as fin_fab_qnty
							 from wo_booking_mst a, wo_booking_dtls b ,wo_pre_cost_fabric_cost_dtls c
							 where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and b.job_no=c.job_no and a.booking_type!=3 and a.item_category in (2,3,13) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $poIds_cond group by a.item_category, b.po_break_down_id";
                        $fabricSQLresult = sql_select($fabricSQL);
                        $fabricArr = array();
                        $finfabricArr = array();
                        $woven_fabricArr = array();
                        $woven_finfabricArr = array();
                        foreach ($fabricSQLresult as $key => $val) {
                            if ($val[csf('item_category')] == 3) {
                                $woven_fabricArr[$val[csf('po_break_down_id')]] += $val[csf('grey_fab_qnty')];
                                $woven_finfabricArr[$val[csf('po_break_down_id')]] += $val[csf('fin_fab_qnty')];
                            } else {
                                $fabricArr[$val[csf('po_break_down_id')]] += $val[csf('grey_fab_qnty')];
                                $finfabricArr[$val[csf('po_break_down_id')]] += $val[csf('fin_fab_qnty')];
                            }
                        }

                        $reqSQL = "select b.po_break_down_id, c.fab_nature_id, (b.requirment/b.pcs)*a.plan_cut_qnty as requirment
						  from wo_po_color_size_breakdown a, wo_pre_cos_fab_co_avg_con_dtls b, wo_pre_cost_fabric_cost_dtls c
						  where a.po_break_down_id=b.po_break_down_id and a.color_number_id=b.color_number_id and a.size_number_id=b.gmts_sizes and b.pre_cost_fabric_cost_dtls_id=c.id and a.job_no_mst=c.job_no and a.item_number_id=c.item_number_id and a.is_deleted=0 and a.status_active=1 and b.pcs>0  $poIds_cond";
                        
						//echo $reqSQL;die;
						$reqSQLresult = sql_select($reqSQL);
                        $reqArr = array();
                        $woven_reqArr = array();
                        foreach ($reqSQLresult as $val) {
                            if ($val[csf('fab_nature_id')] == 3) {
                                $woven_reqArr[$val[csf('po_break_down_id')]] += $val[csf('requirment')];
                            } else {
                                $reqArr[$val[csf('po_break_down_id')]] += $val[csf('requirment')];
                            }
                        }

                        //knitting finsih percentage-----------------------------//
                       $knitSQL = "select c.po_breakdown_id,
								sum(CASE WHEN c.entry_form in(2,22,58) and c.trans_id<>0 THEN c.quantity ELSE 0 END) AS grey_receive,
								sum(CASE WHEN c.entry_form =13 and c.trans_type=5 THEN c.quantity ELSE 0 END) AS transfer_in_qnty_grey,
								sum(CASE WHEN c.entry_form =13 and c.trans_type=6 THEN c.quantity ELSE 0 END) AS transfer_out_qnty_grey,
								sum(CASE WHEN c.entry_form in(7,37) and c.trans_id<>0 THEN c.quantity ELSE 0 END) AS finish_receive,
								sum(CASE WHEN c.entry_form ='66' THEN c.quantity ELSE 0 END) AS finish_receive_roll_wise,
								sum(CASE WHEN c.entry_form=17 THEN c.quantity ELSE 0 END) AS woven_fin_receive,
								sum(CASE WHEN c.entry_form =15 and c.trans_type=5 THEN c.quantity ELSE 0 END) AS transfer_in_qnty_fin,
								sum(CASE WHEN c.entry_form =15 and c.trans_type=6 THEN c.quantity ELSE 0 END) AS transfer_out_qnty_fin
							 from inv_transaction b,order_wise_pro_details c
							 where c.trans_id=b.id and  c.entry_form in(2,22,7,37,13,15,17,66,58) and b.is_deleted=0 and b.status_active=1  and c.is_deleted=0 and c.status_active=1 $poIds_cond_inv2 group by c.po_breakdown_id";
                        $knitArr = array();
                        $finishArr = array();
                        $wovenfinishArr = array();
						 $knitSQLresult = sql_select($knitSQL);
                        foreach ($knitSQLresult as $key => $val) {
                            $knitArr[$val[csf('po_breakdown_id')]] = $val[csf('grey_receive')] + $val[csf('transfer_in_qnty_grey')] - $val[csf('transfer_out_qnty_grey')];
                            $finishArr[$val[csf('po_breakdown_id')]] = $val[csf('finish_receive')] + $val[csf('transfer_in_qnty_fin')] + $val[csf('finish_receive_roll_wise')] - $val[csf('transfer_out_qnty_fin')];
                            $wovenfinishArr[$val[csf('po_breakdown_id')]] = $val[csf('woven_fin_receive')];
                        }

                        //LC/SC Received percentage-----------------------------//
                        $lcscSQL = "select wo_po_break_down_id as po_break_down_id,sum(attached_value) as attached_value
							 from com_sales_contract_order_info
							 where is_deleted=0 and status_active=1 $poIds_cond_lc group by wo_po_break_down_id
							 UNION ALL
							 select wo_po_break_down_id as po_break_down_id,sum(attached_value) as attached_value
							 from com_export_lc_order_info
							 where is_deleted=0 and status_active=1 $poIds_cond_lc group by wo_po_break_down_id";
                        $lcscSQLresult = sql_select($lcscSQL);
                        $lcscArr = array();
                        foreach ($lcscSQLresult as $key => $val) {
                            $lcscArr[$val[csf('po_break_down_id')]] += $val[csf('attached_value')];
                        }
                        //echo $lcscSQL;die;

                        $tna_integrated = return_field_value("tna_integrated", "variable_order_tracking", "company_name=$cbo_company_name and variable_list=14 and status_active=1 and is_deleted=0");
                        //echo $tna_integrated;die;
                        $tna_array = array();
                        if ($tna_integrated == 1) {
                            $tna_sql = sql_select("select a.task_category, b.task_name, a.po_number_id, a.task_finish_date, a.notice_date_end, a.actual_start_date, a.actual_finish_date from tna_process_mst a, lib_tna_task b where a.task_number=b.task_name and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $poIds_cond_tna");
                            foreach ($tna_sql as $row) {

                                $task_finish_date = change_date_format($row[csf('task_finish_date')]);
                                $notice_date_end = change_date_format($row[csf('notice_date_end')]);
                                $actual_start_date = change_date_format($row[csf('actual_start_date')]);
                                $actual_finish_date = change_date_format($row[csf('actual_finish_date')]);

                                $tna_array[$row[csf('po_number_id')]][$row[csf('task_name')]]['task_finish_date'] = $task_finish_date;
                                $tna_array[$row[csf('po_number_id')]][$row[csf('task_name')]]['notice_date_end'] = $notice_date_end;
                                $tna_array[$row[csf('po_number_id')]][$row[csf('task_name')]]['actual_start_date'] = $actual_start_date;
                                $tna_array[$row[csf('po_number_id')]][$row[csf('task_name')]]['actual_finish_date'] = $actual_finish_date;
                            }
                        }

                        
						//Wash Req for Booking.........................
						$wash_req_sql="SELECT e.COSTING_PER,a.job_no,a.po_break_down_id,a.item_number_id,a.color_number_id,a.size_number_id,(a.requirment*b.plan_cut_qnty) as requirment, c.emb_type FROM wo_pre_cos_emb_co_avg_con_dtls a, wo_po_color_size_breakdown b,wo_pre_cost_embe_cost_dtls c,wo_pre_cost_mst e WHERE a.job_no=e.job_no and a.color_size_table_id=b.id  AND c.id = A.PRE_COST_EMB_COST_DTLS_ID $poIds_cond_inv3";
						$wash_req_sql_result = sql_select($wash_req_sql);
                        $woven_wash_req_arr = array();
                        foreach ($wash_req_sql_result as $val) {
							 if($val[COSTING_PER]==1){$dozonToPcs=(1*12);}
							 else if($val[COSTING_PER]==2){$dozonToPcs=1;}
							 else if($val[COSTING_PER]==3){$dozonToPcs=(2*12);}
							 else if($val[COSTING_PER]==4){$dozonToPcs=(3*12);}
							 else if($val[COSTING_PER]==5){$dozonToPcs=(4*12);}
							 $woven_wash_req_arr[$val[csf('po_break_down_id')]] += $val[csf('requirment')]/$dozonToPcs;
                        }
						
						//Wash Booking...........
							$wash_booking_sql="SELECT a.pre_cost_fabric_cost_dtls_id,
							a.gmt_item,a.po_break_down_id,b.item_color,b.requirment,b.rate, b.amount, c.emb_name,c.emb_type,c.body_part_id
							FROM wo_booking_dtls a, wo_emb_book_con_dtls b, wo_pre_cost_embe_cost_dtls c
							WHERE     a.id = b.wo_booking_dtls_id
							AND a.booking_no = b.booking_no
							AND a.pre_cost_fabric_cost_dtls_id = c.id
							$poIds_cond_inv3 AND a.sensitivity = 1 AND b.requirment != 0 AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0";
						$wash_booking_sql_result = sql_select($wash_booking_sql);
                        $woven_wash_booked_arr = array();
                        foreach ($wash_booking_sql_result as $val) {
                              $woven_wash_booked_arr[$val[csf('po_break_down_id')]] += $val[csf('requirment')];
                        }
						//echo $wash_booking_sql;die;
						
						
						if (str_replace("'", "", $cbo_search_by) == 1) {//Order wise report==================
                            $template_id_arr = return_library_array("select po_number_id, template_id from tna_process_mst group by po_number_id, template_id", "po_number_id", "template_id");

                            $curr_date = date("d-m-Y");
                            foreach ($jobSqlResult as $row) {
                                $lcsc_amnt = $lcscArr[$row[csf('id')]];
                                if ($cbo_ls_sc == 1 || ($cbo_ls_sc == 2 && $lcsc_amnt > 0) || ($cbo_ls_sc == 3 && $lcsc_amnt <= 0)) {
                                    if ($i % 2 == 0)
                                        $bgcolor = "#E9F3FF";
                                    else
                                        $bgcolor = "#FFFFFF";

                                    $shipment_performance = 0;
                                    $ex_factory_date = $exDateArr[$row[csf('id')]];

                                    if ($row[csf('shiping_status')] == 1 || $row[csf('shiping_status')] == 2) {
                                        $date_diff_3 = datediff('d', $toDay, $row[csf('pub_shipment_date')]) - 1;
                                    } else {
                                        $date_diff_3 = datediff('d', $ex_factory_date, $row[csf('pub_shipment_date')]) - 1;
                                    }

                                    //$date_diff_3=datediff('d',$row[csf('pub_shipment_date')],$ex_factory_date);
                                    $date_diff_4 = datediff('d', $row[csf('shipment_date')], $ex_factory_date);

                                    if ($row[csf('shiping_status')] == 1 && $row[csf('date_diff_1')] > 10) {
                                        $color = "";
                                        $number_of_order['yet'] += 1;
                                        $shipment_performance = 0;
                                    }

                                    if ($row[csf('shiping_status')] == 1 && ($row[csf('date_diff_1')] <= 10 && $row[csf('date_diff_1')] >= 0)) {
                                        $color = "orange";
                                        $number_of_order['yet'] += 1;
                                        $shipment_performance = 0;
                                    }

                                    if ($row[csf('shiping_status')] == 1 && $row[csf('date_diff_1')] < 0) {
                                        $color = "red";
                                        $number_of_order['yet'] += 1;
                                        $shipment_performance = 0;
                                    }

                                    if ($row[csf('shiping_status')] == 2 && $row[csf('date_diff_1')] > 10) {
                                        $color = "";
                                    }

                                    if ($row[csf('shiping_status')] == 2 && ($row[csf('date_diff_1')] <= 10 && $row[csf('date_diff_1')] >= 0)) {
                                        $color = "orange";
                                    }

                                    if ($row[csf('shiping_status')] == 2 && $row[csf('date_diff_1')] < 0) {
                                        $color = "red";
                                    }

                                    if ($row[csf('shiping_status')] == 2 && $row[csf('date_diff_2')] >= 0) {
                                        $number_of_order['ontime'] += 1;
                                        $shipment_performance = 1;
                                    }

                                    if ($row[csf('shiping_status')] == 2 && $row[csf('date_diff_2')] < 0) {
                                        $number_of_order['after'] += 1;
                                        $shipment_performance = 2;
                                    }

                                    if ($row[csf('shiping_status')] == 3 && $date_diff_3 >= 0) {
                                        $color = "green";
                                    } else if ($row[csf('shiping_status')] == 3 && $date_diff_3 < 0) {
                                        $color = "#2A9FFF";
                                    }

                                    if ($row[csf('shiping_status')] == 3 && $date_diff_4 >= 0) {
                                        $number_of_order['ontime'] += 1;
                                        $shipment_performance = 1;
                                    } else if ($row[csf('shiping_status')] == 3 && $date_diff_4 < 0) {
                                        $number_of_order['after'] += 1;
                                        $shipment_performance = 2;
                                    }

                                    $template_id = $template_id_arr[$row[csf('id')]];
                                    ?>
                                    <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                        <td width="40" bgcolor="<? echo $color; ?>"> <? echo $i; ?></td>
                                        <td width="60" align="center"><p><? echo $row[csf('job_no_prefix_num')]; ?></td>
                                        <td width="60"><p><? echo $buyer_short_name_arr[$row[csf('buyer_name')]]; ?></p></td>
                                        <td width="100"><p><? echo $brand_arr[$row[csf('brand_id')]]; ?></p></td>
                                        <td width="100"><p><? echo $season_arr[$row[csf('season_buyer_wise')]]; ?></p></td>
                                        <td width="70"><p><? echo $row[csf('season_year')]; ?></p></td>
                                        <td width="100"><p><? echo $team_library[$row[csf('team_leader')]]; ?></p></td>
                                        <td width="100"><p><? echo $dealing_merchant_array[$row[csf('dealing_marchant')]]; ?></p></td>
                                        <td width="<? echo $td_width; ?>"><p><a href='#report_details' onclick="progress_comment_popup('<? echo $row[csf('job_no')]; ?>', '<? echo $row[csf('id')]; ?>', '<? echo $template_id; ?>', '<? echo $tna_process_type; ?>');"><? echo $row[csf('po_number')]; ?></a></p></td>
                                        <td width="80"><p><? echo $row[csf('file_no')]; ?>&nbsp;</p></td>
                                        <td width="80"><p><? echo $row[csf('grouping')]; ?>&nbsp;</p></td>
                                        <td width="50"><p><? echo $buyer_short_name_arr[$row[csf('agent_name')]]; ?>&nbsp;</p></td>
                                        <? if($imge_arr[$row[csf('job_no')]] != ''){ ?>
                                        <td width="50" align="center" onclick="openmypage_image('requires/shipment_date_wise_wp_report_woven_controller.php?action=show_image&job_no=<? echo $row[csf('job_no')]; ?>', 'Image View')"><img src='../../../<? echo $imge_arr[$row[csf('job_no')]]; ?>' height='25' width='30' /></td>
                                        <? } else { echo '<td width="50" align="center"></td>' ;} ?>
                                        <td width="100"><p><? echo $garments_item[$row[csf('gmts_item_id')]]; ?></p></td>
                                        <td width="50" align="center"><p><? echo $row[csf('set_smv')]; ?></p></td>
                                        <td width="100"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
                                        <td width="90" align="right"><a href="##" onclick="openmypage_order(<? echo $row[csf("id")] . "," . str_replace("'", "", $cbo_company_name) . "," . $row[csf('gmts_item_id')]; ?>, 0, 'OrderPopup')"><? echo number_format(($row[csf('po_quantity')] * $row[csf('total_set_qnty')]), 0); ?></a></td>
                                        <td width="100" align="right"><? echo number_format($row[csf('po_total_price')], 2); ?></td>
                                        <td width="100" align="right"><?
                                            $poQtyPcs = $row[csf('po_quantity')] * $row[csf('total_set_qnty')];
                                            echo $poQtyPcs;
                                            ?></td>
                                        <td width="80" align="center"><? echo change_date_format($row[csf('insert_date')]); ?></td>
                                        <td width="60" align="center"><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?></td>
                                        <td width="60" align="right"><? echo number_format($row[csf('unit_price')],2); ?></td>
                                        <td width="80" align="center" title="<? echo "Lead Time- " . $row[csf('lead_time')]; ?>"><? echo change_date_format($row[csf('po_received_date')]); ?></td>


                                        <td width="80" align="center"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
                                        <td width="80" align="center"><? echo $row[csf('lead_time')]; ?></td>
                                        <td width="60" align="center" bgcolor="<? echo $color; ?>" >
                                        <? echo $date_diff_3; ?>
                                        </td>

                                        <?
                                        if ($sampleAPParr[$row[csf('id')]]['delay_status'] > 0) {
                                            $sample_td = "#FF99CC";
                                        } else {
                                            $sample_td = "";
                                        }

                                        if ($lapdipAPParr[$row[csf('id')]]['delay_status'] > 0) {
                                            $labdip_td = "#FF99CC";
                                        } else {
                                            $labdip_td = "";
                                        }
                                        ?>
                                        <td width="80" align="right" bgcolor="<? echo $sample_td; ?>">
                                            <a href="##" onclick="show_progress_report_details('sample_status', '<? echo $row[csf("job_no")]; ?>_<? echo $row[csf("id")]; ?>', '920px')">
                                                <?
                                                $sample_perc = $sampleAPParr[$row[csf('id')]]['apprv_status'] * 100 / $sampleAPParr[$row[csf('id')]]['total_po'];
                                                if(is_infinite($sample_perc) || is_nan($sample_perc)){$sample_perc=0;}
											    echo number_format(($sample_perc), 2) . " %";
                                                ?>
                                            </a>
                                        </td>
                                        <td width="80" align="right" bgcolor="<? echo $labdip_td; ?>">
                                            <a href="##" onclick="show_progress_report_details('lapdip_status', '<? echo $row[csf("job_no")]; ?>_<? echo $row[csf('id')]; ?>', '920px')">
                                                <?
                                                $lapdip_perc = $lapdipAPParr[$row[csf('id')]]['apprv_status'] * 100 / $lapdipAPParr[$row[csf('id')]]['total_po'];
                                                if(is_infinite($lapdip_perc) || is_nan($lapdip_perc)){$lapdip_perc=0;}
												echo number_format(($lapdip_perc), 2) . " %";
                                                ?>
                                            </a>
                                        </td>
                                        <td width="80" align="right">
                                            <?
                                                $trims_perc = $trimsAPParr[$row[csf('id')]]['apprv_status'] * 100 / $trimsAPParr[$row[csf('id')]]['total_po'];
                                                if($trims_perc > 0 && (!is_infinite($trims_perc) and !is_nan($trims_perc))){
                                            ?>
                                                <a href="##" onclick="show_progress_report_details('accessories_status', '<? echo $row[csf("job_no")]; ?>_<? echo $row[csf("id")]; ?>', '850px')">
                                                    <?
                                                    echo number_format(($trims_perc), 2) . " %";
                                                    ?>
                                                </a>
                                                <? }
                                                else {
                                                    echo 'N/A';
                                                }
                                                ?>
                                        </td>
                                        <td width="80" align="right">
                                            <?
                                            $is_req = $poReqArr[$row[csf('id')]][1] + $poReqArr[$row[csf('id')]][2] + $poReqArr[$row[csf('id')]][3] + $poReqArr[$row[csf('id')]][4];
                                            //if($embelAPParr[$row[csf('id')]]['total_po']>0)
                                            $embel_perc = $embelAPParr[$row[csf('id')]]['apprv_status'] * 100 / $embelAPParr[$row[csf('id')]]['total_po'];

											if ($is_req > 0 && (!is_infinite($embel_perc) and !is_nan($embel_perc))) {
                                                ?>
                                                <a href="##" onclick="show_progress_report_details('embelishment_status', '<? echo $row[csf("job_no")]; ?>_<? echo $row[csf("id")]; ?>', '920px')">
                                                    <?
													echo number_format(($embel_perc), 2) . " %";
                                                    ?>
                                                </a>
                                                <?
                                            } else {
                                                echo 'N/A';
                                            }
                                            ?>
                                        </td>
                                        <? if ($garmentBtn != 2) { ?>
                                        <td width="80" align="right">
                                            <?
                                            $fabric_booking_perc = $fabricArr[$row[csf('id')]] * 100 / $reqArr[$row[csf('id')]];

                                            $task_finish_date = '';
                                            $notice_date_end = '';
                                            $actual_finish_date = '';
                                            if ($tna_integrated == 1) {
                                                $task_finish_date = $tna_array[$row[csf('id')]][31]['task_finish_date'];
                                                $notice_date_end = $tna_array[$row[csf('id')]][31]['notice_date_end'];
                                                $actual_finish_date = $tna_array[$row[csf('id')]][31]['actual_finish_date'];

                                                if ($fabric_booking_perc < 100) {
                                                    if ($curr_date > $task_finish_date && ($actual_finish_date == "" || $actual_finish_date == "0000-00-00")) {
                                                        $bok_color = "#FF0000";
                                                    } else if ($curr_date < $task_finish_date && $curr_date >= $notice_date_end) {
                                                        $bok_color = "orange";
                                                    } else if (!($actual_finish_date == "" || $actual_finish_date == "0000-00-00") && $actual_finish_date > $task_finish_date) {
                                                        $bok_color = "blue";
                                                    } else {
                                                        $bok_color = "#000000";
                                                    }
                                                } else {
                                                    $bok_color = "#000000";
                                                }
                                            } else {
                                                $bok_color = "#000000";
                                            }
      										if(is_infinite($fabric_booking_perc) || is_nan($fabric_booking_perc)){$fabric_booking_perc=0;}
											?>
                                            <a href="##" <? echo "style='color:$bok_color'"; ?> onclick="show_progress_report_details('fabric_booking_popup', '<? echo $row[csf("job_no")]; ?>_<? echo $row[csf('id')] . "_" . $tna_integrated . "_" . $actual_finish_date . "_" . $task_finish_date; ?>', '900px')"><? echo number_format(($fabric_booking_perc), 2) . " %"; ?></a>
                                        </td>
										<?
                                            }
											$woven_fabric_booking_perc = $woven_fabricArr[$row[csf('id')]] * 100 / $woven_reqArr[$row[csf('id')]];
											 if(is_infinite($woven_fabric_booking_perc) || is_nan($woven_fabric_booking_perc)){$woven_fabric_booking_perc=0;}
											 if ($woven_reqArr[$row[csf('id')]] > 0  && (!is_infinite($woven_fabric_booking_perc) and !is_nan($woven_fabric_booking_perc)))
											 {
											 	$bg_color_wv_booking="";
											 }
											 else $bg_color_wv_booking="bgcolor='#FF0000'";
										?>
                                        <td width="80" align="right" <? echo $bg_color_wv_booking;?>>
                                            <?

                                            if ($woven_reqArr[$row[csf('id')]] > 0  && (!is_infinite($woven_fabric_booking_perc) and !is_nan($woven_fabric_booking_perc))) {
                                                ?>
                                                <a href="##" onclick="show_progress_report_details('woven_fabric_booking_popup', '<? echo $row[csf("job_no")]; ?>_<? echo $row[csf('id')]; ?>', '850px')">
                                                    <?


													echo number_format(($woven_fabric_booking_perc), 2) . " %";
                                                    ?>
                                                </a>
                                                <?
                                            } else {
                                                echo 'N/A';
                                            }
                                            ?>

                                        </td>
                                            <? if ($garmentBtn != 2) { ?>
                                            <td width="80" align="right">
                                                <?
                                                $knit_perc = $knitArr[$row[csf('id')]] * 100 / $fabricArr[$row[csf('id')]];
												if(is_finite($knit_perc) || is_nan($knit_perc)){$knit_perc=0;}
                                                $task_finish_date = '';
                                                $notice_date_end = '';
                                                $actual_finish_date = '';
                                                if ($tna_integrated == 1) {
                                                    $task_finish_date = $tna_array[$row[csf('id')]][60]['task_finish_date']; //60=>Knitting Fabric Production
                                                    $notice_date_end = $tna_array[$row[csf('id')]][60]['notice_date_end']; //72=>Grey Fabric Receive
                                                    $actual_finish_date = $tna_array[$row[csf('id')]][60]['actual_finish_date'];

                                                    if ($knit_perc < 100) {
                                                        if ($curr_date > $task_finish_date && ($actual_finish_date == "" || $actual_finish_date == "0000-00-00")) {
                                                            $knit_color = "#FF0000";
                                                        } else if ($curr_date < $task_finish_date && $curr_date >= $notice_date_end) {
                                                            $knit_color = "orange";
                                                        } else if (!($actual_finish_date == "" || $actual_finish_date == "0000-00-00") && $actual_finish_date > $task_finish_date) {
                                                            $knit_color = "blue";
                                                        } else {
                                                            $knit_color = "#000000";
                                                        }
                                                    } else {
                                                        $knit_color = "#000000";
                                                    }
                                                } else {
                                                    $knit_color = "#000000";
                                                }
                                                ?>
                                                <a href="##" <? echo "style='color:$knit_color'"; ?> onclick="show_progress_report_details('knitting_finish_popup', '<? echo $row[csf("job_no")]; ?>_<? echo $row[csf('id')] . "_" . $tna_integrated . "_" . $actual_finish_date; ?>', '850px')"><? echo number_format(($knit_perc), 2) . " %"; ?></a>
                                            </td>
                <? } ?>
                                        <td width="80" align="right">
                                            <a href="##" onclick="show_progress_report_details('lcsc_rcv_popup', '<? echo $row[csf("job_no")]; ?>_<? echo $row[csf('id')]; ?>_<? echo str_replace("'", "", $cbo_company_name); ?>', '850px')"><? echo number_format($lcscArr[$row[csf('id')]], 2); ?></a>
                                        </td>
                                            <? if ($garmentBtn != 2) { ?>
                                            <td width="80" align="right">
                                                <?
                                                $finish_perc = $finishArr[$row[csf('id')]] * 100 / $finfabricArr[$row[csf('id')]];
												if(is_infinite($finish_perc) || is_nan($finish_perc)){$finish_perc=0;}

                                                $task_finish_date = '';
                                                $notice_date_end = '';
                                                $actual_finish_date = '';
                                                if ($tna_integrated == 1) {
                                                    $task_finish_date = $tna_array[$row[csf('id')]][73]['task_finish_date']; //64=>Finish Fabric Production
                                                    $notice_date_end = $tna_array[$row[csf('id')]][73]['notice_date_end']; //73=>Finish Fabric Receive
                                                    $actual_finish_date = $tna_array[$row[csf('id')]][73]['actual_finish_date'];

                                                    if ($finish_perc < 100) {
                                                        if ($curr_date > $task_finish_date && ($actual_finish_date == "" || $actual_finish_date == "0000-00-00")) {
                                                            $fin_color = "#FF0000";
                                                        } else if ($curr_date < $task_finish_date && $curr_date >= $notice_date_end) {
                                                            $fin_color = "orange";
                                                        } else if (!($actual_finish_date == "" || $actual_finish_date == "0000-00-00") && $actual_finish_date > $task_finish_date) {
                                                            $fin_color = "blue";
                                                        } else {
                                                            $fin_color = "#000000";
                                                        }
                                                    } else {
                                                        $fin_color = "#000000";
                                                    }
                                                } else {
                                                    $fin_color = "#000000";
                                                }
                                                ?>
                                                <a href="##" <? echo "style='color:$fin_color'"; ?> onclick="show_progress_report_details('finish_fabric_popup', '<? echo $row[csf("job_no")]; ?>_<? echo $row[csf('id')] . "_" . $tna_integrated . "_" . $actual_finish_date; ?>', '850px')"><? echo number_format(($finish_perc), 2) . " %"; ?></a>
                                            </td>

                                        <? } ?>
                                        <td width="80" align="right">
                                                <?
                                                $woven_finish_perc = $wovenfinishArr[$row[csf('id')]] * 100 / $woven_finfabricArr[$row[csf('id')]];
                                                if ($woven_reqArr[$row[csf('id')]] > 0 and (!is_infinite($woven_finish_perc) and !is_nan($woven_finish_perc)) ) {
                                                    ?>
                                                    <a href="##" onclick="show_progress_report_details('woven_finish_fabric_popup', '<? echo $row[csf("job_no")]; ?>_<? echo $row[csf('id')]; ?>', '850px')">
                                                        <?

                                                        echo number_format(($woven_finish_perc), 2) . " %";
                                                        ?>
                                                    </a>
                                                    <?
                                                } else {
                                                    echo 'N/A';
                                                }
                                                ?>
                                        </td>
                                        <td width="80" align="center">
                                            <a href="##" onclick="show_trims_rec('trims_rec_popup', '<? echo $row[csf("po_number")]; ?>', '<? echo $row[csf('id')]; ?>', '1200px')">View</a>
                                        </td>
                                        <td width="80" align="right" title="Calculation Based on Plan Cut">
                                            <?
                                            $cutting_qty = $prodArr[$row[csf('id')]]['cutting'];
                                            if ($cutting_qty != '') {
                                                $cutting_qty_perc = ($cutting_qty * 100) / ($row[csf('plan_cut')] * $row[csf('total_set_qnty')]);
                                            } else {
                                                $cutting_qty_perc = '0.00';
                                            }
                                            $task_finish_date = '';
                                            $notice_date_end = '';
                                            $actual_finish_date = '';
                                            if ($tna_integrated == 1) {
                                                $task_finish_date = $tna_array[$row[csf('id')]][84]['task_finish_date'];
                                                $notice_date_end = $tna_array[$row[csf('id')]][84]['notice_date_end'];
                                                $actual_finish_date = $tna_array[$row[csf('id')]][84]['actual_finish_date'];

                                                if ($cutting_qty_perc < 100) {
                                                    if ($curr_date > $task_finish_date && ($actual_finish_date == "" || $actual_finish_date == "0000-00-00")) {
                                                        $cut_color = "#FF0000";
                                                    } else if ($curr_date < $task_finish_date && $curr_date >= $notice_date_end) {
                                                        $cut_color = "orange";
                                                    } else if (!($actual_finish_date == "" || $actual_finish_date == "0000-00-00") && $actual_finish_date > $task_finish_date) {
                                                        $cut_color = "blue";
                                                    } else {
                                                        $cut_color = "#000000";
                                                    }
                                                } else {
                                                    $cut_color = "#000000";
                                                }
                                            } else {
                                                $cut_color = "#000000";
                                            }

										   if(is_infinite($cutting_qty_perc) || is_nan($cutting_qty_perc)){$cutting_qty_perc=0;}
										    ?>
                                            <a href="##" <? echo "style='color:$cut_color'"; ?> onclick="show_progress_report_details('cutting_finish_popup', '<? echo $row[csf("job_no")]; ?>_<? echo $row[csf('id')] . "_" . $tna_integrated . "_" . $actual_finish_date; ?>', '850px')"><? echo number_format($cutting_qty_perc, 2) . ' ' . '%'; ?></a>
                                        </td>
                                        <td width="80" align="right">
                                            <?
                                            $is_req = $poReqArr[$row[csf('id')]][1] + $poReqArr[$row[csf('id')]][2] + $poReqArr[$row[csf('id')]][3] + $poReqArr[$row[csf('id')]][4];

                                            $prnt_qty = $prodArr[$row[csf('id')]]['prnt'] / $poReqArr[$row[csf('id')]][1] * 100;
                                            $embel_qty = $prodArr[$row[csf('id')]]['embel'] / $poReqArr[$row[csf('id')]][2] * 100;
                                            $wash_qty = $prodArr[$row[csf('id')]]['wash'] / $poReqArr[$row[csf('id')]][3] * 100;
                                            $special_qty = $prodArr[$row[csf('id')]]['special'] / $poReqArr[$row[csf('id')]][4] * 100;
                                            $totalP = 0;
                                            $n = 0;
                                            if ($prnt_qty !== 0) {
                                                $totalP += $prnt_qty;
                                                $n++;
                                            }
                                            if ($embel_qty !== 0) {
                                                $totalP += $embel_qty;
                                                $n++;
                                            }
                                            if ($wash_qty !== 0) {
                                                $totalP += $wash_qty;
                                                $n++;
                                            }
                                            if ($special_qty !== 0) {
                                                $totalP += $special_qty;
                                                $n++;
                                            }

                                            $embPercentage = $totalP / $n;
                                            $task_finish_date = '';
                                            $notice_date_end = '';
                                            $actual_finish_date = '';
                                            if ($tna_integrated == 1) {
                                                $task_finish_date = $tna_array[$row[csf('id')]][85]['task_finish_date'];
                                                $notice_date_end = $tna_array[$row[csf('id')]][85]['notice_date_end'];
                                                $actual_finish_date = $tna_array[$row[csf('id')]][85]['actual_finish_date'];

                                                if ($embPercentage < 100) {
                                                    if ($curr_date > $task_finish_date && ($actual_finish_date == "" || $actual_finish_date == "0000-00-00")) {
                                                        $emb_color = "#FF0000";
                                                    } else if ($curr_date < $task_finish_date && $curr_date >= $notice_date_end) {
                                                        $emb_color = "orange";
                                                    } else if (!($actual_finish_date == "" || $actual_finish_date == "0000-00-00") && $actual_finish_date > $task_finish_date) {
                                                        $emb_color = "blue";
                                                    } else {
                                                        $emb_color = "#000000";
                                                    }
                                                } else {
                                                    $emb_color = "#000000";
                                                }
                                            } else {
                                                $emb_color = "#000000";
                                            }

                                            if(is_infinite($embPercentage) || is_nan($embPercentage)){$embPercentage=0;}


											if ($is_req > 0) {
                                                ?>
                                                <a href="##" <? echo "style='color:$emb_color'"; ?> onclick="show_progress_report_details('print_emb_completed_popup', '<? echo $row[csf("job_no")]; ?>_<? echo $row[csf('id')] . "_" . $tna_integrated . "_" . $actual_finish_date; ?>', '850px')"><? echo number_format($embPercentage, 2) . ' ' . '%'; ?></a>
                                                <?
                                            } else {
                                                echo 'N/A';
                                            }
                                            ?>
                                        </td>
                                        <td width="80" align="right"   title="Calculation Based on Order Qnty.">
                                            <?
                                            $sewingout_qty = $prodArr[$row[csf('id')]]['sewingout'];
                                            if ($sewingout_qty != '') {
                                                $sewingout_qty_perc = ($sewingout_qty * 100) / ($row[csf('po_quantity')] * $row[csf('total_set_qnty')]);
                                            } else {
                                                $sewingout_qty_perc = '0.00';
                                            }

                                            $task_finish_date = '';
                                            $notice_date_end = '';
                                            $actual_finish_date = '';
                                            if ($tna_integrated == 1) {
                                                $task_finish_date = $tna_array[$row[csf('id')]][86]['task_finish_date'];
                                                $notice_date_end = $tna_array[$row[csf('id')]][86]['notice_date_end'];
                                                $actual_finish_date = $tna_array[$row[csf('id')]][86]['actual_finish_date'];

                                                if ($sewingout_qty_perc < 100) {
                                                    if ($curr_date > $task_finish_date && ($actual_finish_date == "" || $actual_finish_date == "0000-00-00")) {
                                                        $sew_color = "#FF0000";
                                                    } else if ($curr_date < $task_finish_date && $curr_date >= $notice_date_end) {
                                                        $sew_color = "orange";
                                                    } else if (!($actual_finish_date == "" || $actual_finish_date == "0000-00-00") && $actual_finish_date > $task_finish_date) {
                                                        $sew_color = "blue";
                                                    } else {
                                                        $sew_color = "#000000";
                                                    }
                                                } else {
                                                    $sew_color = "#000000";
                                                }
                                            } else {
                                                $sew_color = "#000000";
                                            }
                                           if(is_infinite($sewingout_qty_perc) || is_nan($sewingout_qty_perc)){$sewingout_qty_perc=0;}
										    ?>
                                            <a href="##" <? echo "style='color:$sew_color'"; ?> onclick="show_progress_report_details('sewing_finish_popup', '<? echo $row[csf("job_no")]; ?>_<? echo $row[csf('id')] . "_" . $tna_integrated . "_" . $actual_finish_date; ?>', '850px')"><? echo number_format($sewingout_qty_perc, 2) . ' ' . '%'; ?></a>
                                        </td>
                                       <td width="80" align="right">
									   
									   <?
									   $show_val='';
									   if($woven_wash_req_arr[$row[csf('id')]]<1){
										  echo "N/A"; 
									   }
									   else if($woven_wash_req_arr[$row[csf('id')]]>0 && $woven_wash_booked_arr[$row[csf('id')]]<1){
										   $show_val=  "0.00 %"; 
									   }
									   else{
									   $wash_booked_per=($woven_wash_booked_arr[$row[csf('id')]]*100)/$woven_wash_req_arr[$row[csf('id')]];
									   $show_val= number_format($wash_booked_per,2)."%"; ;
									   }
									   
									   //ddddddd;?>
                                       <a href="##" onclick="show_report_details('wash_booked_popup', '<? echo $row[csf("job_no")]; ?>_<? echo $row[csf('id')]; ?>', '870px','Wash Booking Dtls')">
                                       <?= $show_val;?>
                                       </a>
                                       </td>


                                        <td width="80" align="right" title="Calculation Based on Order Qnty.">
                                            <a href="##" onclick="show_progress_report_details('iron_output_popup', '<? echo $row[csf("job_no")]; ?>_<? echo $row[csf('id')]; ?>', '850px')">
                                                <?
                                                $cutting_qty = $prodArr[$row[csf('id')]]['ironoutput'];
                                                if ($cutting_qty != '') {
                                                    $cutting_qty_perc = ($cutting_qty * 100) / ($row[csf('po_quantity')] * $row[csf('total_set_qnty')]);
                                                    if(is_infinite($cutting_qty_perc) || is_nan($cutting_qty_perc)){$cutting_qty_perc=0;}

													echo number_format($cutting_qty_perc, 2) . ' ' . '%';
                                                } else
                                                    echo '0.00' . '%';
                                                ?>
                                            </a>
                                        </td>
                                        
                                        <td width="80" align="right" title="Calculation Based on Order Qnty.">
                                            <?
                                            $finishcompleted_qty = $prodArr[$row[csf('id')]]['finishcompleted'];
                                            if ($finishcompleted_qty != '') {
                                                $finishcompleted_qty_perc = ($finishcompleted_qty * 100) / ($row[csf('po_quantity')] * $row[csf('total_set_qnty')]);
												if(is_infinite($finishcompleted_qty_perc) || is_nan($finishcompleted_qty_perc)){$finishcompleted_qty_perc=0;}
                                            } else
                                                $finishcompleted_qty_perc = '0.00';

                                            $task_finish_date = '';
                                            $notice_date_end = '';
                                            $actual_finish_date = '';
                                            if ($tna_integrated == 1) {
                                                $task_finish_date = $tna_array[$row[csf('id')]][88]['task_finish_date'];
                                                $notice_date_end = $tna_array[$row[csf('id')]][88]['notice_date_end'];
                                                $actual_finish_date = $tna_array[$row[csf('id')]][88]['actual_finish_date'];

                                                if ($finishcompleted_qty_perc < 100) {
                                                    if ($curr_date > $task_finish_date && ($actual_finish_date == "" || $actual_finish_date == "0000-00-00")) {
                                                        $pack_color = "#FF0000";
                                                    } else if ($curr_date < $task_finish_date && $curr_date >= $notice_date_end) {
                                                        $pack_color = "orange";
                                                    } else if (!($actual_finish_date == "" || $actual_finish_date == "0000-00-00") && $actual_finish_date > $task_finish_date) {
                                                        $pack_color = "blue";
                                                    } else {
                                                        $pack_color = "#000000";
                                                    }
                                                } else {
                                                    $pack_color = "#000000";
                                                }
                                            } else {
                                                $pack_color = "#000000";
                                            }
                                            ?>
                                            <a href="##" <? echo "style='color:$pack_color'"; ?> onclick="show_progress_report_details('finish_completed_popup', '<? echo $row[csf("job_no")]; ?>_<? echo $row[csf('id')] . "_" . $tna_integrated . "_" . $actual_finish_date; ?>', '850px')"><? echo number_format($finishcompleted_qty_perc, 2) . ' ' . '%'; ?></a>
                                        <?
										
										
										if($inspArr[$row[csf('id')]]>0 && $inspectionStatusArr[$row[csf('id')]]==''){$insBg="#FFF";/* white*/}
										
										else if($inspArr[$row[csf('id')]]>0 && $inspectionStatusArr[$row[csf('id')]]==3){$insBg="#4EB97F";/*green*/}
										else if($inspectionStatusArr[$row[csf('id')]]==3){$insBg="#FF0000";/*Red*/}
										//else{$insBg="#048AD5";/*Blue*/}
										?>
                                        
                                        </td>
                                          <td width="80" align="right" title="Woven Fin.">
                                            <?
                                            $wvn_finishcompleted_qty = $prodArr[$row[csf('id')]]['wvn_finishcompleted'];
                                            if ($wvn_finishcompleted_qty != '') {
                                                $wvn_finishcompleted_qty_perc = ($wvn_finishcompleted_qty * 100) / ($row[csf('po_quantity')] * $row[csf('total_set_qnty')]);
												if(is_infinite($wvn_finishcompleted_qty_perc) || is_nan($wvn_finishcompleted_qty_perc)){$finishcompleted_qty_perc=0;}
                                            } else
                                                $wvn_finishcompleted_qty_perc = '0.00';

                                            $wvn_task_finish_date = '';
                                            $wvn_notice_date_end = '';
                                            $wvn_actual_finish_date = '';
                                            if ($tna_integrated == 1) {
                                                $wvn_task_finish_date = $tna_array[$row[csf('id')]][88]['task_finish_date'];
                                                $wvn_notice_date_end = $tna_array[$row[csf('id')]][88]['notice_date_end'];
                                                $wvn_actual_finish_date = $tna_array[$row[csf('id')]][88]['actual_finish_date'];

                                                if ($wvn_finishcompleted_qty_perc < 100) {
                                                    if ($curr_date > $wvn_task_finish_date && ($wvn_actual_finish_date == "" || $wvn_actual_finish_date == "0000-00-00")) {
                                                        $wvn_pack_color = "#FF0000";
                                                    } else if ($curr_date < $wvn_task_finish_date && $curr_date >= $wvn_notice_date_end) {
                                                        $wvn_pack_color = "orange";
                                                    } else if (!($wvn_actual_finish_date == "" || $wvn_actual_finish_date == "0000-00-00") && $wvn_actual_finish_date > $wvn_task_finish_date) {
                                                        $wvn_pack_color = "blue";
                                                    } else {
                                                        $wvn_pack_color = "#000000";
                                                    }
                                                } else {
                                                    $wvn_pack_color = "#000000";
                                                }
                                            } else {
                                                $wvn_pack_color = "#000000";
                                            }
                                            ?>
                                            <a href="##" <? echo "style='color:$wvn_pack_color'"; ?> onclick="show_progress_report_details('wvn_finish_completed_popup', '<? echo $row[csf("job_no")]; ?>_<? echo $row[csf('id')] . "_" . $tna_integrated . "_" . $wvn_actual_finish_date; ?>', '850px')"><? echo number_format($wvn_finishcompleted_qty_perc, 2) . ' ' . '%'; ?></a>
                                        <?
										
										
										if($inspArr[$row[csf('id')]]>0 && $inspectionStatusArr[$row[csf('id')]]==''){$insBg="#FFF";/* white*/}
										
										else if($inspArr[$row[csf('id')]]>0 && $inspectionStatusArr[$row[csf('id')]]==3){$insBg="#4EB97F";/*green*/}
										else if($inspectionStatusArr[$row[csf('id')]]==3){$insBg="#FF0000";/*Red*/}
										//else{$insBg="#048AD5";/*Blue*/}
										?>
                                        
                                        </td>
                                        
                                        <td width="80" align="right" bgcolor="<?= $insBg;?>">
                                            <?
											$insp_qty_perc = ($inspArr[$row[csf('id')]] * 100) / ($row[csf('po_quantity')] * $row[csf('total_set_qnty')]);
                                            if(is_infinite($insp_qty_perc) || is_nan($insp_qty_perc)){$insp_qty_perc=0;}

											$task_finish_date = '';
                                            $notice_date_end = '';
                                            $actual_finish_date = '';
                                            if ($tna_integrated == 1) {
                                                $task_finish_date = $tna_array[$row[csf('id')]][101]['task_finish_date'];
                                                $notice_date_end = $tna_array[$row[csf('id')]][101]['notice_date_end'];
                                                $actual_finish_date = $tna_array[$row[csf('id')]][101]['actual_finish_date'];

                                                if ($insp_qty_perc < 100) {
                                                    if ($curr_date > $task_finish_date && ($actual_finish_date == "" || $actual_finish_date == "0000-00-00")) {
                                                        $insp_color = "#FF0000";
                                                    } else if ($curr_date < $task_finish_date && $curr_date >= $notice_date_end) {
                                                        $insp_color = "orange";
                                                    } else if (!($actual_finish_date == "" || $actual_finish_date == "0000-00-00") && $actual_finish_date > $task_finish_date) {
                                                        $insp_color = "blue";
                                                    } else {
                                                        $insp_color = "#000000";
                                                    }
                                                } else {
                                                    $insp_color = "#000000";
                                                }
                                            } else {
                                                $insp_color = "#000000";
                                            }
                                            ?>
                                            <a href="##" <? echo "style='color:$insp_color'"; ?> onclick="show_progress_report_details('buyer_inspection_popup', '<? echo $row[csf("job_no")]; ?>_<? echo $row[csf('id')] . "_" . $tna_integrated . "_" . $actual_finish_date; ?>', '850px')"><? echo number_format($insp_qty_perc, 2) . ' ' . '%'; ?> </a>
                                        </td>
                                        <td width="100" align="right">
                                            <?
                                            $exf_perc = ($exArr[$row[csf('id')]] * 100) / ($row[csf('po_quantity')] * $row[csf('total_set_qnty')]);
                                           if(is_infinite($exf_perc) || is_nan($exf_perc)){$exf_perc=0;}

											$task_finish_date = '';
                                            $notice_date_end = '';
                                            $actual_finish_date = '';
                                            if ($tna_integrated == 1) {
                                                $task_finish_date = $tna_array[$row[csf('id')]][110]['task_finish_date'];
                                                $notice_date_end = $tna_array[$row[csf('id')]][110]['notice_date_end'];
                                                $actual_finish_date = $tna_array[$row[csf('id')]][110]['actual_finish_date'];

                                                if ($exf_perc < 100) {
                                                    if ($curr_date > $task_finish_date && ($actual_finish_date == "" || $actual_finish_date == "0000-00-00")) {
                                                        $exf_color = "#FF0000";
                                                    } else if ($curr_date < $task_finish_date && $curr_date >= $notice_date_end) {
                                                        $exf_color = "orange";
                                                    } else if (!($actual_finish_date == "" || $actual_finish_date == "0000-00-00") && $actual_finish_date > $task_finish_date) {
                                                        $exf_color = "blue";
                                                    } else {
                                                        $exf_color = "#000000";
                                                    }
                                                } else {
                                                    $exf_color = "#000000";
                                                }
                                            } else {
                                                $exf_color = "#000000";
                                            }

                                            $invoice_qty = $invoiceArr[$row[csf('id')]]['invoice_qnty'] * $row[csf('total_set_qnty')];
                                            $invoice_color = "";
                                            if ($exArr[$row[csf('id')]] > $invoice_qty) {
                                                $invoice_color = "#FF0000";
                                            }
                                            ?>
                                            <a href="##" <? echo "style='color:$exf_color'"; ?> onclick="show_progress_report_details('ex_factory_popup', '<? echo $row[csf("job_no")]; ?>_<? echo $row[csf('id')] . "_" . $tna_integrated . "_" . $actual_finish_date; ?>', '850px')"><?
                                                echo number_format($exArr[$row[csf('id')]], 0);
                                                $tsq += $exArr[$row[csf('id')]];
                                                ?></a>
                                        </td>
                                        <td width="80" align="center">
                                            <a href="##" onclick="show_progress_report_details('actual_shipment_popup', '<? echo $row[csf("job_no")]; ?>_<? echo $row[csf('id')]; ?>', '850px')">View</a>
                                        </td>
                                        <td width="90" align="right" bgcolor="<? echo $invoice_color; ?>">
                                            <?
                                            echo number_format($invoice_qty, 0);
                                            ?>
                                        </td>
                                        <td width="100" align="right">
                                            <?
                                            echo number_format($invoiceArr[$row[csf('id')]]['invoice_value'], 2);
                                            ?>
                                        </td>
                                        <td align="right" width="120">
                                            <?
                                            $balance_ship_qnty = $row[csf('po_quantity')] * $row[csf('total_set_qnty')] - $invoice_qty;
                                            echo number_format($balance_ship_qnty, 0);
                                            ?>
                                        </td>
                                        <td><p><? echo $row[csf('details_remarks')]; ?></p></td>
                                    </tr>
                                    <?
                                    $i++;
                                }
                            }
                        }// end order wise report generate====================================================
                        else if (str_replace("'", "", $cbo_search_by) == 2) {
                            foreach ($jobSqlResult as $row) {
                                if ($i % 2 == 0)
                                    $bgcolor = "#E9F3FF";
                                else
                                    $bgcolor = "#FFFFFF";

                                $shipment_performance = 0;
                                if ($row[csf('shiping_status')] == 1 && $row[csf('date_diff_1')] > 10) {
                                    $color = "";
                                    $number_of_order['yet'] += 1;
                                    $shipment_performance = 0;
                                }
                                if ($row[csf('shiping_status')] == 1 && ($row[csf('date_diff_1')] <= 10 && $row[csf('date_diff_1')] >= 0)) {
                                    $color = "orange";
                                    $number_of_order['yet'] += 1;
                                    $shipment_performance = 0;
                                }
                                if ($row[csf('shiping_status')] == 1 && $row[csf('date_diff_1')] < 0) {
                                    $color = "red";
                                    $number_of_order['yet'] += 1;
                                    $shipment_performance = 0;
                                }
                                //=====================================
                                if ($row[csf('shiping_status')] == 2 && $row[csf('date_diff_1')] > 10) {
                                    $color = "";
                                }
                                if ($row[csf('shiping_status')] == 2 && ($row[csf('date_diff_1')] <= 10 && $row[csf('date_diff_1')] >= 0)) {
                                    $color = "orange";
                                }
                                if ($row[csf('shiping_status')] == 2 && $row[csf('date_diff_1')] < 0) {
                                    $color = "red";
                                }
                                if ($row[csf('shiping_status')] == 2 && $row[csf('date_diff_2')] >= 0) {
                                    $number_of_order['ontime'] += 1;
                                    $shipment_performance = 1;
                                }
                                if ($row[csf('shiping_status')] == 2 && $row[csf('date_diff_2')] < 0) {
                                    $number_of_order['after'] += 1;
                                    $shipment_performance = 2;
                                }

                                $ex_factory_date = '';
                                $ex_factory_qnty = 0;
                                $sampleAppStatus = 0;
                                $sampleTotPo = 0;
                                $labdipAppStatus = 0;
                                $labdipTotPo = 0;
                                $trimsAppStatus = 0;
                                $trimsTotPo = 0;
                                $embellAppStatus = 0;
                                $embellTotPo = 0;
                                $booking_qnty = 0;
                                $req_qnty = 0;
                                $knit_qnty = 0;
                                $lcSc_recv = 0;
                                $fin_booking_qnty = 0;
                                $finish_qnty = 0;
                                $cutting_qty = 0;
                                $sewingout_qty = 0;
                                $finishinput_qty = 0;
                                $ironoutput_qnty = 0;
                                $finishcompleted_qty = 0;
                                $inspec_qty = 0;
                                $prnt_qty = 0;
                                $embel_qty = 0;
                                $wash_qty = 0;
                                $special_qty = 0;
                                $prntReq_qty = 0;
                                $embelReq_qty = 0;
                                $washReq_qty = 0;
                                $specialReq_qty = 0;
                                $invoice_qnty = 0;
                                $invoice_value = 0;
                                $woven_booking_qnty = 0;
                                $woven_req_qnty = 0;
                                $woven_fin_booking_qnty = 0;
                                $woven_finish_qnty = 0;
                                $po_id = explode(",", $row[csf('id')]);
                                foreach ($po_id as $id) {
                                    $ex_factory_qnty += $exArr[$id];
                                    if ($ex_factory_date == "") {
                                        $ex_factory_date = $exDateArr[$id];
                                    } else {
                                        if ($exDateArr[$id] > $ex_factory_date)
                                            $ex_factory_date = $exDateArr[$id];
                                    }

                                    $invoice_qnty += $invoiceArr[$id]['invoice_qnty'] * $row[csf('total_set_qnty')];
                                    $invoice_value += $invoiceArr[$id]['invoice_value'];

                                    $sampleAppStatus += $sampleAPParr[$id]['apprv_status'];
                                    $sampleTotPo += $sampleAPParr[$id]['total_po'];
                                    $sampleDelayStatus += $sampleAPParr[$id]['delay_status'];

                                    $labdipAppStatus += $lapdipAPParr[$id]['apprv_status'];
                                    $labdipTotPo += $lapdipAPParr[$id]['total_po'];
                                    $labdipDelayStatus += $lapdipAPParr[$id]['delay_status'];

                                    $trimsAppStatus += $trimsAPParr[$id]['apprv_status'];
                                    $trimsTotPo += $trimsAPParr[$id]['total_po'];

                                    $embellAppStatus += $embelAPParr[$id]['apprv_status'];
                                    $embellTotPo += $embelAPParr[$id]['total_po'];

                                    $booking_qnty += $fabricArr[$id];
                                    $req_qnty += $reqArr[$id];
                                    $knit_qnty += $knitArr[$id];
                                    $lcSc_recv += $lcscArr[$id];
                                    $fin_booking_qnty += $finfabricArr[$id];
                                    $finish_qnty += $finishArr[$id];

                                    $woven_booking_qnty += $woven_fabricArr[$id];
                                    $woven_req_qnty += $woven_reqArr[$id];
                                    $woven_fin_booking_qnty += $woven_finfabricArr[$id];
                                    $woven_finish_qnty += $wovenfinishArr[$id];

                                    $cutting_qty += $prodArr[$id]['cutting'];
                                    $sewingout_qty += $prodArr[$id]['sewingout'];
                                    $finishinput_qty += $prodArr[$id]['finishinput'];
                                    $ironoutput_qnty += $prodArr[$id]['ironoutput'];
                                    $finishcompleted_qty += $prodArr[$id]['finishcompleted'];
                                    $inspec_qty += $inspArr[$id];

                                    $prnt_qty += $prodArr[$id]['prnt'];
                                    $embel_qty += $prodArr[$id]['embel'];
                                    $wash_qty += $prodArr[$id]['wash'];
                                    $special_qty += $prodArr[$id]['special'];
                                    $prntReq_qty += $poReqArr[$id][1];
                                    $embelReq_qty += $poReqArr[$id][2];
                                    $washReq_qty += $poReqArr[$id][3];
                                    $specialReq_qty += $poReqArr[$id][4];
                                }


                                //---------------------add by reza start-----------
                                if ($row[csf('shiping_status')] == 1 || $row[csf('shiping_status')] == 2) {
                                    $date_diff_3 = datediff('d', $toDay, $row[csf('pub_shipment_date')]) - 1;
                                } else {
                                    $date_diff_3 = datediff('d', $ex_factory_date, $row[csf('pub_shipment_date')]) - 1;
                                }
                                //---------------------add by reza end-----------
                                //$date_diff_3=datediff('d',$row[csf('pub_shipment_date')],$ex_factory_date);
                                $date_diff_4 = datediff('d', $row[csf('shipment_date')], $ex_factory_date);

                                if ($row[csf('shiping_status')] == 3 && $date_diff_3 >= 0) {
                                    $color = "green";
                                }
                                if ($row[csf('shiping_status')] == 3 && $date_diff_3 < 0) {
                                    $color = "#2A9FFF";
                                }
                                if ($row[csf('shiping_status')] == 3 && $date_diff_4 >= 0) {
                                    $number_of_order['ontime'] += 1;
                                    $shipment_performance = 1;
                                }
                                if ($row[csf('shiping_status')] == 3 && $date_diff_4 < 0) {
                                    $number_of_order['after'] += 1;
                                    $shipment_performance = 2;
                                }


                                $grouping = implode(",", array_unique(explode(",", $row[csf("grouping")])));
                                $file_no = implode(",", array_unique(explode(",", $row[csf("file_no")])));
                                ?>
                                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                    <td width="40" bgcolor="<? echo $color; ?>"> <? echo $i; ?></td>
                                    <td width="60" align="center"><p><? echo $row[csf('job_no_prefix_num')]; ?></td>
                                    <td width="60"><p><? echo $buyer_short_name_arr[$row[csf('buyer_name')]]; ?></p></td>
                                    <td width="100"><p><? echo $brand_arr[$row[csf('brand_id')]]; ?></p></td>
                                    <td width="100"><p><? echo $season_arr[$row[csf('season_buyer_wise')]]; ?></p></td>
                                    <td width="70"><p><? echo $row[csf('season_year')]; ?></p></td>
                                    <td width="100"><p><? echo $team_library[$row[csf('team_leader')]]; ?></p></td>
                                    <td width="100"><p><? echo $dealing_merchant_array[$row[csf('dealing_marchant')]]; ?></p></td>
                                    <td width="<? echo $td_width; ?>" align="center"><a href="##" onclick="show_progress_report_details('order_number_popup', '<? echo $row[csf("job_no")]; ?>_<? echo $row[csf("id")]; ?>', '850px')"> View</a></td>
                                    <td width="80"><p><? echo $file_no; ?>&nbsp;</p></td>
                                    <td width="80"><p><? echo $grouping; ?>&nbsp;</p></td>
                                    <td width="50"><p><? echo $buyer_short_name_arr[$row[csf('agent_name')]]; ?>&nbsp;</p></td>
                                    <td width="50" onclick="openmypage_image('requires/shipment_date_wise_wp_report_woven_controller.php?action=show_image&job_no=<? echo $row[csf('job_no')]; ?>', 'Image View')"><img  src='../../../<? echo $imge_arr[$row[csf('job_no')]]; ?>' height='25' width='30' /></td>
                                    <td width="100"><p><? echo $garments_item[$row[csf('gmts_item_id')]]; ?></p></td>
                                    <td width="50" align="center"><p><? echo $row[csf('set_smv')]; ?></p></td>
                                    <td width="100"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
                                    <td width="90" align="right"><a href="##" onclick="openmypage_order('<? echo $row[csf("id")] . "'," . str_replace("'", "", $cbo_company_name) . "," . $row[csf('gmts_item_id')]; ?>,0,'OrderPopup
                                            ')"><? echo number_format($row[csf('po_quantity')], 0); ?></a></td>
                                    <td width="100" align="right"><? echo number_format($row[csf('po_total_price')], 2); ?></td>

                                    <td width="100" align="right"><?
                                                        $poQtyPcs = $row[csf('po_quantity')] * $row[csf('total_set_qnty')];
                                                        echo $poQtyPcs;
                                                        ?></td>
                                    <td width="80" align="center"><? echo change_date_format($row[csf('insert_date')]); ?></td>

                                    <td width="60" align="center"><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?></td>
                                    <td width="60" align="right"><? echo number_format($row[csf('unit_price')],2); ?></td>
                                    <td width="80" align="center"><? echo change_date_format($row[csf('po_received_date')]); ?></td>
                                    <td width="80" align="center"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
                                    <td width="80"></td>
                                    <td width="60" align="center" bgcolor="<? echo $color; ?>" >
                                        <?
                                        echo $date_diff_3;
                                        ?>
                                    </td>
                                    <?
                                    if ($sampleDelayStatus > 0) {
                                        $sample_td = "#FF99CC";
                                    } else {
                                        $sample_td = "";
                                    }

                                    if ($labdipDelayStatus > 0) {
                                        $labdip_td = "#FF99CC";
                                    } else {
                                        $labdip_td = "";
                                    }
                                    ?>
                                    <td width="80" title="<? echo  $sampleAppStatus.'*100/'. $sampleTotPo;?>" align="right" bgcolor="<? echo $sample_td; ?>">
                                        <a href="##" onclick="show_progress_report_details('sample_status', '<? echo $row[csf("job_no")]; ?>_<? echo $row[csf("id")]; ?>', '920px')">
                                            <?
                                            $sample_perc = $sampleAppStatus * 100 / $sampleTotPo;
											if(is_infinite($sample_perc) || is_nan($sample_perc)){$sample_perc=0;}
                                            echo number_format(($sample_perc), 2) . " %";
                                            ?>
                                        </a>
                                    </td>
                                    <td width="80" align="right" bgcolor="<? echo $labdip_td; ?>">
                                        <a href="##" onclick="show_progress_report_details('lapdip_status', '<? echo $row[csf("job_no")]; ?>_<? echo $row[csf('id')]; ?>', '920px')">
                                            <?
                                            $lapdip_perc = $labdipAppStatus * 100 / $labdipTotPo;
											if(is_infinite($lapdip_perc) || is_nan($lapdip_perc)){$lapdip_perc=0;}
                                            echo number_format(($lapdip_perc), 2) . " %";
                                            ?>
                                        </a>
                                    </td>
                                    <td width="80" align="right">
                                        <a href="##" onclick="show_progress_report_details('accessories_status', '<? echo $row[csf("job_no")]; ?>_<? echo $row[csf("id")]; ?>', '850px')">
                                            <?
                                            $trims_perc = $trimsAppStatus * 100 / $trimsTotPo;
											if(is_infinite($trims_perc) || is_nan($trims_perc)){$trims_perc=0;}
                                            echo number_format(($trims_perc), 2) . " %";
                                            ?>
                                        </a>
                                    </td>
                                    <td width="80" align="right">
                                            <?
                                            if ($embellTotPo > 0) {
                                                ?>
                                            <a href="##" onclick="show_progress_report_details('embelishment_status', '<? echo $row[csf("job_no")]; ?>_<? echo $row[csf("id")]; ?>', '920px')">
                                                <?
                                                $embel_perc = $embellAppStatus * 100 / $embellTotPo;
                                           		if(is_infinite($embel_perc) || is_nan($embel_perc)){$embel_perc=0;}
                                                echo number_format(($embel_perc), 2) . " %";
                                                ?>
                                            </a>
                                            <?
                                        } else {
                                            echo 'N/A';
                                        }
                                        ?>
                                    </td>
                                    <? if ($garmentBtn != 2) { ?>
                                    <td width="80" align="right">
                                        <a href="##" onclick="show_progress_report_details('fabric_booking_popup', '<? echo $row[csf("job_no")]; ?>_<? echo $row[csf('id')]; ?>', '850px')">
                                            <?
                                            $fabric_booking_perc = $booking_qnty * 100 / $req_qnty;
                                            if(is_infinite($fabric_booking_perc) || is_nan($fabric_booking_perc)){$fabric_booking_perc=0;}

											echo number_format(($fabric_booking_perc), 2) . " %";
                                            ?>
                                        </a>
                                    </td>
                                    <? } ?>
                                    <td width="80" align="right">
                                            <?
                                            if ($woven_req_qnty > 0) {
                                                ?>
                                            <a href="##" onclick="show_progress_report_details('woven_fabric_booking_popup', '<? echo $row[csf("job_no")]; ?>_<? echo $row[csf('id')]; ?>', '850px')">
                                                <?
                                                $woven_fabric_booking_perc = $woven_booking_qnty * 100 / $woven_req_qnty;
                                                if(is_infinite($woven_fabric_booking_perc) || is_nan($woven_fabric_booking_perc)){$woven_fabric_booking_perc=0;}
												echo number_format(($woven_fabric_booking_perc), 2) . " %";
                                                ?>
                                            </a>
                                            <?
                                        } else {
                                            echo 'N/A';
                                        }
                                        ?>
                                    </td>
                                            <? if ($garmentBtn != 2) { ?>
                                        <td  width="80" align="right">
                                            <a href="##" onclick="show_progress_report_details('knitting_finish_popup', '<? echo $row[csf("job_no")]; ?>_<? echo $row[csf('id')]; ?>', '850px')">
                                                <?
                                                $knit_perc = $knit_qnty * 100 / $booking_qnty;
                                                if(is_infinite($knit_perc) || is_nan($knit_perc)){$knit_perc=0;}
                                                echo number_format(($knit_perc), 2) . " %";
                                                ?>
                                            </a>
                                        </td>
                                            <? } ?>
                                    <td width="80" align="right">
                                        <a href="##" onclick="show_progress_report_details('lcsc_rcv_popup', '<? echo $row[csf("job_no")]; ?>_<? echo $row[csf('id')]; ?>_<? echo str_replace("'", "", $cbo_company_name); ?>', '850px')">
            <?
                        echo number_format($lcSc_recv, 2);
            ?>
                                        </a>
                                    </td>
                                        <? if ($garmentBtn != 2) { ?>
                                        <td width="80" align="right">
                                                <?
                                                if ($woven_req_qnty > 0) {
                                                    ?>
                                                <a href="##" onclick="show_progress_report_details('finish_fabric_popup', '<? echo $row[csf("job_no")]; ?>_<? echo $row[csf('id')]; ?>', '850px')">
                                                    <?
                                                    $finish_perc = $finish_qnty * 100 / $fin_booking_qnty;
                                                    if(is_infinite($finish_perc) || is_nan($finish_perc)){$finish_perc=0;}

													echo number_format(($finish_perc), 2) . " %";
                                                    ?>
                                                </a>
                                                <?
                                            } else {
                                                echo 'N/A';
                                            }
                                            ?>
                                        </td>

                                <? } ?>
                                    <td width="80" align="right">
                                            <a href="##" onclick="show_progress_report_details('woven_finish_fabric_popup', '<? echo $row[csf("job_no")]; ?>_<? echo $row[csf('id')]; ?>', '850px')">
                                                <?
                                                $woven_finish_perc = $woven_finish_qnty * 100 / $woven_fin_booking_qnty;
                                                if(is_infinite($woven_finish_perc) || is_nan($woven_finish_perc)){$woven_finish_perc=0;}
                                                echo number_format(($woven_finish_perc), 2) . " %";
                                                ?>
                                            </a>
                                        </td>
                                    <td width="80" align="center">
                                        <a href="##" onclick="show_trims_rec('trims_rec_popup', '<? echo $row[csf("job_no")]; ?>', '<? echo $row[csf('id')]; ?>', '1200px')">View</a>
                                    </td>
                                    <td width="80" align="right" title="Calculation Based on Plan Cut">
                                        <a href="##" onclick="show_progress_report_details('cutting_finish_popup', '<? echo $row[csf("job_no")]; ?>_<? echo $row[csf('id')]; ?>', '850px')">
                                            <?
                                            //$cutting_qty =  $prodArr[$row[csf('job_no')]]['cutting'];
                                            $cutting_qty_perc = ($cutting_qty * 100) / $row[csf('plan_cut')];
											if(is_infinite($cutting_qty_perc) || is_nan($cutting_qty_perc)){$cutting_qty_perc=0;}
											echo number_format($cutting_qty_perc, 2) . ' ' . '%';
                                            ?>
                                        </a>
                                    </td>
                                    <td width="80" align="right">
                                        <?
                                        $is_req = $prntReq_qty + $embelReq_qty + $washReq_qty + $specialReq_qty;
                                        if ($is_req > 0) {
                                            ?>
                                            <a href="##" onclick="show_progress_report_details('print_emb_completed_popup', '<? echo $row[csf("job_no")]; ?>_<? echo $row[csf('id')]; ?>', '850px')">
                                                <?
                                                $prnt_qty = $prnt_qty / $prntReq_qty * 100;
                                                $embel_qty = $embel_qty / $embelReq_qty * 100;
                                                $wash_qty = $wash_qty / $washReq_qty * 100;
                                                $special_qty = $special_qty / $specialReq_qty * 100;
                                                $totalP = 0;
                                                $n = 0;
                                                if ($prnt_qty !== 0) {
                                                    $totalP += $prnt_qty;
                                                    $n++;
                                                }
                                                if ($embel_qty !== 0) {
                                                    $totalP += $embel_qty;
                                                    $n++;
                                                }
                                                if ($wash_qty !== 0) {
                                                    $totalP += $wash_qty;
                                                    $n++;
                                                }
                                                if ($special_qty !== 0) {
                                                    $totalP += $special_qty;
                                                    $n++;
                                                }

                                                $embPercentage = $totalP / $n;
                                                if(is_infinite($embPercentage) || is_nan($embPercentage)){$embPercentage=0;}
                                                 echo number_format($embPercentage, 2) . ' ' . '%';

                                                ?>
                                            </a>
                                            <?
                                        }
                                        else {
                                            echo 'N/A';
                                        }
                                        ?>
                                    </td>
                                    <td width="80" align="right" title="Calculation Based on Order Qnty.">
                                        <a href="##" onclick="show_progress_report_details('sewing_finish_popup', '<? echo $row[csf("job_no")]; ?>_<? echo $row[csf('id')]; ?>', '850px')">
                                            <?
                                            //$sewingout_qty =  $prodArr[$row[csf('job_no')]]['sewingout'];
                                            if ($sewingout_qty != '') {
                                                $sewingout_qty_perc = ($sewingout_qty * 100) / $row[csf('po_quantity')];
                                                echo number_format($sewingout_qty_perc, 2) . ' ' . '%';
                                            } else
                                                echo '0.00' . '%';
                                            ?>
                                        </a>
                                    </td>

                                    <td width="80" align="right">
                                        <a href="##" onclick="show_report_details('wash_booked_style_popup', '<? echo $row[csf("job_no")]; ?>_<? echo $row[csf('id')]; ?>', '850px','Wash')">
                                            <?
                                            
                                            if ($washReq_qty != '') {
                                                $washoutput_qnty_perc = ($washReq_qty * 100) / $row[csf('po_quantity')];
                                                echo number_format($washoutput_qnty_perc, 2) . ' ' . '%';
                                            } else
                                                echo '0.00' . '%';
                                            ?>
                                        </a>
                                    </td>
                                    <td width="80" align="right" title="Calculation Based on Order Qnty.">
                                        <a href="##" onclick="show_progress_report_details('iron_output_popup', '<? echo $row[csf("job_no")]; ?>_<? echo $row[csf('id')]; ?>', '850px')">
                                            <?
											// $ironoutput_qnty =  $prodArr[$row[csf('job_no')]]['ironoutput'];
                                             
                                            if ($ironoutput_qnty != '') {
                                                $ironoutput_qnty_qty_perc = ($ironoutput_qnty * 100) / $row[csf('po_quantity')];
                                                echo number_format($ironoutput_qnty_qty_perc, 2) . ' ' . '%';
                                            } else
                                                echo '0.00' . '%';
                                            ?>
                                        </a>
                                    </td>
                                    <td width="80" align="right">
                                        <a href="##" onclick="show_progress_report_details('finish_completed_popup', '<? echo $row[csf("job_no")]; ?>_<? echo $row[csf('id')]; ?>', '850px')">
                                            <?
											//$finishcompleted_qty =  $prodArr[$row[csf('job_no')]]['finishcompleted'];
                                            //$insp_qty_perc = ($inspec_qty * 100) / $row[csf('po_quantity')];
											//if(is_infinite($insp_qty_perc) || is_nan($insp_qty_perc)){$insp_qty_perc=0;}
                                           // echo number_format($insp_qty_perc, 2) . ' ' . '%';
										    if ($finishcompleted_qty != '') {
                                                $finishcompleted_qty_perc = ($finishcompleted_qty * 100) / $row[csf('po_quantity')];
                                                echo number_format($finishcompleted_qty_perc, 2) . ' ' . '%';
                                            } else
                                                echo '0.00' . '%';
												
                                            ?>
                                        </a>
                                    </td>
                                     <td width="80" align="right" title="<? echo "(".$inspec_qty ."* 100) /". $row[csf('po_quantity')];?>">
                                        <a href="##" onclick="show_progress_report_details('buyer_inspection_popup', '<? echo $row[csf("job_no")]; ?>_<? echo $row[csf('id')]; ?>', '850px')">
                                            <?
											//$finishcompleted_qty =  $prodArr[$row[csf('job_no')]]['finishcompleted'];
                                           $insp_qty_perc = ($inspec_qty * 100) / $row[csf('po_quantity')];
											//if(is_infinite($insp_qty_perc) || is_nan($insp_qty_perc)){$insp_qty_perc=0;}
                                           // echo number_format($insp_qty_perc, 2) . ' ' . '%';
										    if ($insp_qty_perc != '') {
                                                $inspcompleted_qty_perc = ($inspec_qty * 100) / $row[csf('po_quantity')];
                                                echo number_format($inspcompleted_qty_perc, 2) . ' ' . '%';
                                            } else
                                                echo '0.00' . '%';
												
                                            ?>
                                        </a>
                                    </td>
                                    
                                    <td width="100" align="right">
                                        <a href="##" onclick="show_progress_report_details('ex_factory_popup', '<? echo $row[csf("job_no")]; ?>_<? echo $row[csf('id')]; ?>', '850px')">
                                            <?
                                            echo number_format($ex_factory_qnty, 0);
                                            $tsq += $ex_factory_qnty;

                                            $invoice_color = "";
                                            if ($ex_factory_qnty > $invoice_qnty) {
                                                $invoice_color = "#FF0000";
                                            }
                                            ?>
                                        </a>
                                    </td>
                                    <td width="80" align="center">
                                        <a href="##" onclick="show_progress_report_details('actual_shipment_popup', '<? echo $row[csf("job_no")]; ?>_<? echo $row[csf('id')]; ?>', '850px')">View</a>
                                    </td>
                                    <td width="90" align="right" bgcolor="<? echo $invoice_color; ?>"><? echo number_format($invoice_qnty, 0); ?></td>
                                    <td width="100" align="right"><? echo number_format($invoice_value, 2); ?></td>
                                    <td width="120" align="right">
                                        <?
                                        $balance_ship_qnty = $row[csf('po_quantity')] - $invoice_qnty;
                                        echo number_format($balance_ship_qnty, 0);
                                        ?>
                                    </td>
                                    <td><p><? echo $row[csf('details_remarks')]; ?></p></td>
                                </tr>
                                <?
                                $i++;
                            }
                        }// end style wise report generate====================================================
                        else if (str_replace("'", "", $cbo_search_by) == 3) {//Country Ship Date wise report==================
                            $template_id_arr = return_library_array("select po_number_id, template_id from tna_process_mst group by po_number_id, template_id", "po_number_id", "template_id");
                            if ($db_type == 0) {
                                $jobSql = "select a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.agent_name, a.style_ref_no, a.total_set_qnty, a.gmts_item_id, a.order_uom, a.team_leader, a.dealing_marchant, b.id, b.is_confirmed, b.po_number, sum(c.order_quantity) as po_quantity, sum(c.plan_cut_qnty) as plan_cut, sum(c.order_total) as po_total_price, b.pub_shipment_date, b.po_received_date, DATEDIFF(c.country_ship_date, '$date') date_diff_1, DATEDIFF(pub_shipment_date,po_received_date) as lead_time, c.country_ship_date, group_concat(c.country_id) as country_id, group_concat(c.shiping_status) as shiping_status, b.grouping, b.file_no,a.set_smv,b.insert_date,b.details_remarks,a.brand_id,a.season_buyer_wise,a.season_year from 			wo_po_details_master a, wo_po_break_down b ,wo_po_color_size_breakdown c,wo_pre_cost_mst d where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and b.id=c.po_break_down_id  and  d.job_no=c.job_no_mst and  a.job_no=d.job_no  and  b.job_no_mst=d.job_no and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and b.po_number like '$order_no_cond' and a.style_ref_no like '$style_ref_cond' $date_cond $searchCond $job_no_cond $team_name_cond $team_member_cond $year_cond $file_no_cond $ref_no_cond $season_year_cond $brand_id_cond $season_id_cond group by b.id, c.country_ship_date, a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.agent_name, a.style_ref_no, a.gmts_item_id, a.order_uom, a.team_leader, a.dealing_marchant, a.total_set_qnty, b.is_confirmed, b.po_number, b.pub_shipment_date, b.shipment_date, b.po_received_date, b.grouping, b.,a.brand_id,a.season_buyer_wise,a.season_year order by b.pub_shipment_date, a.job_no_prefix_num, b.id, c.country_ship_date";
                            } else {
                                $jobSql = "select a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.agent_name, a.style_ref_no, a.gmts_item_id, a.order_uom, a.team_leader, a.dealing_marchant, a.total_set_qnty, b.id, b.is_confirmed, b.po_number, sum(c.order_quantity) as po_quantity, sum(c.plan_cut_qnty) as plan_cut, sum(c.order_total) as po_total_price, b.pub_shipment_date, b.po_received_date, trunc(c.country_ship_date-to_date('$date', 'yyyy-mm-dd')) date_diff_1, trunc(pub_shipment_date-po_received_date) as lead_time, c.country_ship_date, LISTAGG(CAST(c.country_id AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY c.country_id) as country_id, LISTAGG(CAST(c.shiping_status AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY c.shiping_status) as shiping_status, b.grouping, b.file_no,max(b.insert_date) as insert_date,a.set_smv,b.details_remarks,a.brand_id,a.season_buyer_wise,a.season_year from 		wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_mst d where	a.job_no=b.job_no_mst and a.job_no=c.job_no_mst  and b.id=c.po_break_down_id  and  d.job_no=c.job_no_mst and  a.job_no=d.job_no  and  b.job_no_mst=d.job_no and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and b.po_number like '$order_no_cond' and a.style_ref_no like '$style_ref_cond' $date_cond $searchCond $job_no_cond $team_name_cond $team_member_cond $year_cond $file_no_cond $ref_no_cond $season_year_cond $brand_id_cond $season_id_cond group by b.id, c.country_ship_date, a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.agent_name, a.style_ref_no, a.gmts_item_id, a.order_uom, a.team_leader, a.dealing_marchant, a.total_set_qnty, b.is_confirmed, b.po_number, b.pub_shipment_date, b.shipment_date, b.po_received_date, b.grouping, b.file_no,a.set_smv,b.details_remarks,a.brand_id,a.season_buyer_wise,a.season_year order by b.pub_shipment_date, a.job_no_prefix_num, b.id, c.country_ship_date";
                            }//$year_cond
                          //  echo $jobSql;

                            $curr_date = date("d-m-Y");
                            $jobSqlResult = sql_select($jobSql);
                            foreach ($jobSqlResult as $row) {
                                if ($i % 2 == 0)
                                    $bgcolor = "#E9F3FF";
                                else
                                    $bgcolor = "#FFFFFF";

                                $country_names = "";
                                $country_ids = "";
                                $shiping_status = "";
                                $exf_qty = 0;
                                $ship_qty_as_per = 0;
                                $ship_value = 0;
                                $ex_factory_date = '';
                                $cutting_qty = 0;
                                $prnt_qty = 0;
                                $embel_qty = 0;
                                $wash_qty = 0;
                                $special_qty = 0;
                                $sewingout_qty = 0;
                                $ironoutput_qty = 0;
                                $finish_qty = 0;
                                $inspection_qty = 0;

                                $country_data = array_unique(explode(",", $row[csf('country_id')]));
                                foreach ($country_data as $country_id) {
                                    if ($country_ids == "") {
                                        $country_ids = $country_id;
                                        $country_names = $country_library[$country_id];
                                    } else {
                                        $country_ids .= "," . $country_id;
                                        $country_names .= "," . $country_library[$country_id];
                                    }

                                    $cutting_qty += $prodArr[$row[csf('id')]][$country_id]['cutting'];
                                    $prnt_qty += $prodArr[$row[csf('id')]][$country_id]['prnt'];
                                    $embel_qty += $prodArr[$row[csf('id')]][$country_id]['embel'];
                                    $wash_qty += $prodArr[$row[csf('id')]][$country_id]['wash'];
                                    $special_qty += $prodArr[$row[csf('id')]][$country_id]['special'];
                                    $sewingout_qty += $prodArr[$row[csf('id')]][$country_id]['sewingout'];
                                    $ironoutput_qty += $prodArr[$row[csf('id')]][$country_id]['ironoutput'];
                                    $finish_qty += $prodArr[$row[csf('id')]][$country_id]['finishcompleted'];
                                    $inspection_qty += $inspArr[$row[csf('id')]][$country_id];
                                    $exf_qty += $exArr[$row[csf('id')]][$country_id];
                                    $ship_qty_as_per += $invoiceArr[$row[csf('id')]][$country_id]['invoice_qnty'] * $row[csf('total_set_qnty')];
                                    $ship_value += $invoiceArr[$row[csf('id')]][$country_id]['invoice_value'];

                                    if ($ex_factory_date == "") {
                                        $ex_factory_date = $exDateArr[$row[csf('id')]][$country_id];
                                    } else {
                                        if ($exDateArr[$row[csf('id')]][$country_id] > $ex_factory_date)
                                            $ex_factory_date = $exDateArr[$row[csf('id')]][$country_id];
                                    }
                                }

                                $shiping_status_data = array_unique(explode(",", $row[csf('shiping_status')]));
                                if (count($shiping_status_data) > 1) {
                                    $shiping_status = 2;
                                } else {
                                    $shiping_status = $shiping_status_all = implode(",", $shiping_status_data);
                                    ;
                                }

                                if ($shiping_status == 1 || $shiping_status == 2) {
                                    $date_diff_3 = datediff('d', $toDay, $row[csf('country_ship_date')]) - 1;
                                } else {
                                    $date_diff_3 = datediff('d', $ex_factory_date, $row[csf('country_ship_date')]) - 1;
                                }

                                $display_font_color = "<font style='display:none' color='$bgcolor'>";
                                $font_end = "</font>";
                                if (!in_array($row[csf('id')], $po_print_arr)) {
                                    $display_font_color = "";
                                    $font_end = "";
                                    $po_print_arr[] = $row[csf('id')];
                                }

                                if (($shiping_status == 1 || $shiping_status == 2) && $row[csf('date_diff_1')] > 10) {
                                    $color = "";
                                } else if (($shiping_status == 1 || $shiping_status == 2) && ($row[csf('date_diff_1')] <= 10 && $row[csf('date_diff_1')] >= 0)) {
                                    $color = "orange";
                                } else if (($shiping_status == 1 || $shiping_status == 2) && $row[csf('date_diff_1')] < 0) {
                                    $color = "red";
                                } else if ($shiping_status == 3 && $date_diff_3 >= 0) {
                                    $color = "green";
                                } else if ($shiping_status == 3 && $date_diff_3 < 0) {
                                    $color = "#2A9FFF";
                                }

                                $template_id = $template_id_arr[$row[csf('id')]];
                                ?>
                                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                    <td width="40" bgcolor="<? echo $color; ?>"> <? echo $i; ?></td>
                                    <td width="60" align="center"><p><? echo $row[csf('job_no_prefix_num')]; ?></td>
                                    <td width="60"><p><? echo $buyer_short_name_arr[$row[csf('buyer_name')]]; ?></p></td>
                                    <td width="100"><p><? echo $brand_arr[$row[csf('brand_id')]]; ?></p></td>
                                    <td width="100"><p><? echo $season_arr[$row[csf('season_buyer_wise')]]; ?></p></td>
                                    <td width="70"><p><? echo $row[csf('season_year')]; ?></p></td>
                                    <td width="100"><p><? echo $team_library[$row[csf('team_leader')]]; ?></p></td>
                                    <td width="100"><p><? echo $dealing_merchant_array[$row[csf('dealing_marchant')]]; ?></p></td>
                                    <td width="<? echo $td_width; ?>"><p><a href='#report_details' onclick="progress_comment_popup('<? echo $row[csf('job_no')]; ?>', '<? echo $row[csf('id')]; ?>', '<? echo $template_id; ?>', '<? echo $tna_process_type; ?>');"><? echo $row[csf('po_number')]; ?></a></p></td>
                                    <td width="80"><p><? echo $row[csf('file_no')]; ?>&nbsp;</p></td>
                                    <td width="80"><p><? echo $row[csf('grouping')]; ?>&nbsp;</p></td>
                                    <td width="50"><p><? echo $buyer_short_name_arr[$row[csf('agent_name')]]; ?>&nbsp;</p></td>
                                    <td width="50" onclick="openmypage_image('requires/shipment_date_wise_wp_report_woven_controller.php?action=show_image&job_no=<? echo $row[csf('job_no')]; ?>', 'Image View')"><img src='../../../<? echo $imge_arr[$row[csf('job_no')]]; ?>' height='25' width='30' /></td>
                                    <td width="100"><p><? echo $garments_item[$row[csf('gmts_item_id')]]; ?></p></td>
                                    <td width="50" align="center"><p><? echo $row[csf('set_smv')]; ?></p></td>

                                    <td width="100"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
                                    <td width="100"><p><? echo $country_names; ?></p></td>
                                    <td width="90" align="right"><a href="##" onclick="openmypage_order('<? echo $row[csf("id")] . "'," . str_replace("'", "", $cbo_company_name) . "," . $row[csf('gmts_item_id')]; ?>,0,'OrderPopup')"><? echo $row[csf('po_quantity')]; ?></a></td>
                                    <td width="100" align="right"><? echo number_format($row[csf('po_total_price')], 2, '.', ''); ?></td>
                                    <td width="100" align="right"><?
                            $poQtyPcs = $row[csf('po_quantity')] * $row[csf('total_set_qnty')];
                            echo $poQtyPcs;
                            ?></td>
                                    <td width="80" align="center"><? echo change_date_format($row[csf('insert_date')]); ?></td>
                                    <td width="60" align="center"><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?></td>
                                    <td width="60" align="right"><? echo number_format($row[csf('unit_price')],2); ?></td>
                                    <td width="80" align="center" title="<? echo "Lead Time- " . $row[csf('lead_time')]; ?>"><? echo change_date_format($row[csf('po_received_date')]); ?></td>
                                    <td width="80" align="center"><? echo change_date_format($row[csf('country_ship_date')]); ?></td>
                                    <td width="80" align="center"><? echo $row[csf('lead_time')]; ?></td>
                                    <td width="60" align="center" bgcolor="<? echo $color; ?>" >
                                        <a href="##" <? echo "style='color:black'"; ?> onclick="show_progress_report_daysInHand('daysInHand_popup', '<? echo $row[csf("job_no")]; ?>_<? echo $row[csf('id')]; ?>', '550px', '<? echo $country_ids; ?>')"><? echo $date_diff_3; ?></a>
                                    </td>
                                    <?
                                    if ($sampleAPParr[$row[csf('id')]]['delay_status'] > 0) {
                                        $sample_td = "#FF99CC";
                                    } else {
                                        $sample_td = "";
                                    }

                                    if ($lapdipAPParr[$row[csf('id')]]['delay_status'] > 0) {
                                        $labdip_td = "#FF99CC";
                                    } else {
                                        $labdip_td = "";
                                    }
                                    ?>
                                    <td width="80" align="right" title="<? echo  $sampleAPParr[$row[csf('id')]]['apprv_status'].'='. $sampleAPParr[$row[csf('id')]]['total_po'];?>" bgcolor="<? echo $sample_td; ?>">
                                        <a href="##" onclick="show_progress_report_details('sample_status', '<? echo $row[csf("job_no")]; ?>_<? echo $row[csf("id")]; ?>', '920px')">
            <?
            $sample_perc = $sampleAPParr[$row[csf('id')]]['apprv_status'] * 100 / $sampleAPParr[$row[csf('id')]]['total_po'];
			if(is_infinite($sample_perc) || is_nan($sample_perc)){$sample_perc=0;}
            echo $display_font_color . number_format(($sample_perc), 2) . " %" . $font_end;
            ?>
                                        </a>
                                    </td>
                                    <td width="80" align="right" bgcolor="<? echo $labdip_td; ?>">
                                        <a href="##" onclick="show_progress_report_details('lapdip_status', '<? echo $row[csf("job_no")]; ?>_<? echo $row[csf('id')]; ?>', '920px')">
            <?
            $lapdip_perc = $lapdipAPParr[$row[csf('id')]]['apprv_status'] * 100 / $lapdipAPParr[$row[csf('id')]]['total_po'];
			if(is_infinite($lapdip_perc) || is_nan($lapdip_perc)){$lapdip_perc=0;}
			echo $display_font_color . number_format(($lapdip_perc), 2) . " %" . $font_end;
            ?>
                                        </a>
                                    </td>
                                    <td width="80" align="right">
                                        <a href="##" onclick="show_progress_report_details('accessories_status', '<? echo $row[csf("job_no")]; ?>_<? echo $row[csf("id")]; ?>', '850px')">
            <?
            $trims_perc = $trimsAPParr[$row[csf('id')]]['apprv_status'] * 100 / $trimsAPParr[$row[csf('id')]]['total_po'];
            if(is_infinite($trims_perc) || is_nan($trims_perc)){$trims_perc=0;}
			echo $display_font_color . number_format(($trims_perc), 2) . " %" . $font_end;
            ?>
                                        </a>
                                    </td>
                                    <td width="80" align="right">
                                        <a href="##" onclick="show_progress_report_details('embelishment_status', '<? echo $row[csf("job_no")]; ?>_<? echo $row[csf("id")]; ?>', '920px')">
            <?
            $embel_perc = $embelAPParr[$row[csf('id')]]['apprv_status'] * 100 / $embelAPParr[$row[csf('id')]]['total_po'];
            if(is_infinite($embel_perc) || is_nan($embel_perc)){$embel_perc=0;}
			echo $display_font_color . number_format(($embel_perc), 2) . " %" . $font_end;
            ?>
                                        </a>
                                    </td>
                                    <? if ($garmentBtn != 2) { ?>
                                    <td width="80" align="right">
                                        <?
                                        $fabric_booking_perc = $fabricArr[$row[csf('id')]] * 100 / $reqArr[$row[csf('id')]];
										if(is_infinite($fabric_booking_perc) || is_nan($fabric_booking_perc)){$fabric_booking_perc=0;}

										$task_finish_date = '';
                                        $notice_date_end = '';
                                        $actual_start_date = '';
                                        $actual_finish_date = '';
                                        if ($tna_integrated == 1) {
                                            $task_finish_date = $tna_array[$row[csf('id')]][31]['task_finish_date'];
                                            $notice_date_end = $tna_array[$row[csf('id')]][31]['notice_date_end'];
                                            $actual_finish_date = $tna_array[$row[csf('id')]][31]['actual_finish_date'];
                                            $actual_start_date = $tna_array[$row[csf('id')]][31]['actual_start_date'];

                                            if ($fabric_booking_perc < 100) {
                                                if ($curr_date > $task_finish_date && ($actual_finish_date == "" || $actual_finish_date == "0000-00-00")) {
                                                    $bok_color = "#FF0000";
                                                } else if ($curr_date < $task_finish_date && $curr_date >= $notice_date_end) {
                                                    $bok_color = "orange";
                                                } else if (!($actual_finish_date == "" || $actual_finish_date == "0000-00-00") && $actual_finish_date > $task_finish_date) {
                                                    $bok_color = "blue";
                                                } else {
                                                    $bok_color = "#000000";
                                                }
                                            } else {
                                                $bok_color = "#000000";
                                            }
                                        } else {
                                            $bok_color = "#000000";
                                        }
                                        echo $display_font_color;
                                        ?>
                                        <a href="##" <? echo "style='color:$bok_color'"; ?> onclick="show_progress_report_details('fabric_booking_popup', '<? echo $row[csf("job_no")]; ?>_<? echo $row[csf('id')] . "_" . $tna_integrated . "_" . $actual_finish_date . "_" . $actual_start_date; ?>', '850px')"><? echo number_format(($fabric_booking_perc), 2) . " %"; ?></a><? echo $font_end; ?>
                                    </td>
                                    <? } ?>
                                    <td width="80" align="right">
                                            <? echo $display_font_color; ?>
                                        <a href="##" onclick="show_progress_report_details('woven_fabric_booking_popup', '<? echo $row[csf("job_no")]; ?>_<? echo $row[csf('id')]; ?>', '850px')">
                                        <?
                                        $woven_fabric_booking_perc = $woven_fabricArr[$row[csf('id')]] * 100 / $woven_reqArr[$row[csf('id')]];
                                        if(is_infinite($woven_fabric_booking_perc) || is_nan($woven_fabric_booking_perc)){$woven_fabric_booking_perc=0;}
										echo number_format(($woven_fabric_booking_perc), 2) . " %";
                                        ?>
                                        </a>
                                        <? echo $font_end; ?>
                                    </td>
                                    <? if($garmentBtn != 2){?>
                                    <td width="80" align="right">
                                        <?
                                        $knit_perc = $knitArr[$row[csf('id')]] * 100 / $fabricArr[$row[csf('id')]];
										if(is_infinite($knit_perc) || is_nan($knit_perc)){$knit_perc=0;}
                                        $task_finish_date = '';
                                        $notice_date_end = '';
                                        $actual_finish_date = '';
                                        $actual_start_date = '';
                                        if ($tna_integrated == 1) {
                                            $task_finish_date = $tna_array[$row[csf('id')]][60]['task_finish_date']; //60=>Knitting Fabric Production
                                            $notice_date_end = $tna_array[$row[csf('id')]][60]['notice_date_end']; //72=>Grey Fabric Receive
                                            $actual_finish_date = $tna_array[$row[csf('id')]][60]['actual_finish_date'];
                                            $actual_start_date = $tna_array[$row[csf('id')]][60]['actual_start_date'];

                                            if ($knit_perc < 100) {
                                                if ($curr_date > $task_finish_date && ($actual_finish_date == "" || $actual_finish_date == "0000-00-00")) {
                                                    $knit_color = "#FF0000";
                                                } else if ($curr_date < $task_finish_date && $curr_date >= $notice_date_end) {
                                                    $knit_color = "orange";
                                                } else if (!($actual_finish_date == "" || $actual_finish_date == "0000-00-00") && $actual_finish_date > $task_finish_date) {
                                                    $knit_color = "blue";
                                                } else {
                                                    $knit_color = "#000000";
                                                }
                                            } else {
                                                $knit_color = "#000000";
                                            }
                                        } else {
                                            $knit_color = "#000000";
                                        }
                                        echo $display_font_color;
                                        ?>
                                        <a href="##" <? echo "style='color:$knit_color'"; ?> onclick="show_progress_report_details('knitting_finish_popup', '<? echo $row[csf("job_no")]; ?>_<? echo $row[csf('id')] . "_" . $tna_integrated . "_" . $actual_finish_date . "_" . $actual_start_date; ?>', '850px')"><? echo number_format(($knit_perc), 2) . " %"; ?></a><? echo $font_end; ?>
                                    </td>
                                    <? }?>
                                    <td width="80" align="right">
                                        <? echo $display_font_color; ?><a href="##" onclick="show_progress_report_details('lcsc_rcv_popup', '<? echo $row[csf("job_no")]; ?>_<? echo $row[csf('id')]; ?>_<? echo str_replace("'", "", $cbo_company_name); ?>', '850px')"><? echo number_format($lcscArr[$row[csf('id')]], 2); ?></a><? echo $font_end; ?>
                                    </td>
                                    <? if($garmentBtn != 2){?>
                                    <td width="80" align="right">
                                        <?
                                        $finish_perc = $finishArr[$row[csf('id')]] * 100 / $finfabricArr[$row[csf('id')]];
										if(is_infinite($finish_perc) || is_nan($finish_perc)){$finish_perc=0;}

                                        $task_finish_date = '';
                                        $notice_date_end = '';
                                        $actual_start_date = '';
                                        $actual_finish_date = '';
                                        if ($tna_integrated == 1) {
                                            $task_finish_date = $tna_array[$row[csf('id')]][73]['task_finish_date']; //64=>Finish Fabric Production
                                            $notice_date_end = $tna_array[$row[csf('id')]][73]['notice_date_end']; //73=>Finish Fabric Receive
                                            $actual_finish_date = $tna_array[$row[csf('id')]][73]['actual_finish_date'];
                                            $actual_start_date = $tna_array[$row[csf('id')]][73]['actual_start_date'];

                                            if ($finish_perc < 100) {
                                                if ($curr_date > $task_finish_date && ($actual_finish_date == "" || $actual_finish_date == "0000-00-00")) {
                                                    $fin_color = "#FF0000";
                                                } else if ($curr_date < $task_finish_date && $curr_date >= $notice_date_end) {
                                                    $fin_color = "orange";
                                                } else if (!($actual_finish_date == "" || $actual_finish_date == "0000-00-00") && $actual_finish_date > $task_finish_date) {
                                                    $fin_color = "blue";
                                                } else {
                                                    $fin_color = "#000000";
                                                }
                                            } else {
                                                $fin_color = "#000000";
                                            }
                                        } else {
                                            $fin_color = "#000000";
                                        }
                                        echo $display_font_color;
                                        ?>
                                        <a href="##" <? echo "style='color:$fin_color'"; ?> onclick="show_progress_report_details('finish_fabric_popup', '<? echo $row[csf("job_no")]; ?>_<? echo $row[csf('id')] . "_" . $tna_integrated . "_" . $actual_finish_date . "_" . $actual_start_date; ?>', '850px')"><? echo number_format(($finish_perc), 2) . " %"; ?></a><? echo $font_end; ?>
                                    </td>

                                    <? }?>
                                    <td width="80" align="right">
                                            <? echo $display_font_color; ?>
                                        <a href="##" onclick="show_progress_report_details('woven_finish_fabric_popup', '<? echo $row[csf("job_no")]; ?>_<? echo $row[csf('id')]; ?>', '850px')">
                                        <?
                                        $woven_finish_perc = $wovenfinishArr[$row[csf('id')]] * 100 / $woven_finfabricArr[$row[csf('id')]];
                                        if(is_infinite($woven_finish_perc) || is_nan($woven_finish_perc)){$woven_finish_perc=0;}
                                        echo number_format(($woven_finish_perc), 2) . " %";
                                        ?>
                                        </a>
                                        <? echo $font_end; ?>
                                    </td>
                                    <td width="80" align="center">
                                        <? echo $display_font_color; ?>
                                        <a href="##" onclick="show_trims_rec('trims_rec_popup', '<? echo $row[csf("po_number")]; ?>', '<? echo $row[csf('id')]; ?>', '1200px')">View</a>
                                        <? echo $font_end; ?>
                                    </td>
                                    <td width="80" align="right" title="Calculation Based on Plan Cut">
                                        <?
                                        if ($cutting_qty != '') {
                                            $cutting_qty_perc = ($cutting_qty * 100) / ($row[csf('plan_cut')]);
											if(is_infinite($cutting_qty_perc) || is_nan($cutting_qty_perc)){$cutting_qty_perc=0;}
                                        } else {
                                            $cutting_qty_perc = '0.00';
                                        }

                                        $task_finish_date = '';
                                        $notice_date_end = '';
                                        $actual_start_date = '';
                                        $actual_finish_date = '';
                                        if ($tna_integrated == 1) {
                                            $task_finish_date = $tna_array[$row[csf('id')]][84]['task_finish_date'];
                                            $notice_date_end = $tna_array[$row[csf('id')]][84]['notice_date_end'];
                                            $actual_finish_date = $tna_array[$row[csf('id')]][84]['actual_finish_date'];
                                            $actual_start_date = $tna_array[$row[csf('id')]][84]['actual_start_date'];

                                            if ($cutting_qty_perc < 100) {
                                                if ($curr_date > $task_finish_date && ($actual_finish_date == "" || $actual_finish_date == "0000-00-00")) {
                                                    $cut_color = "#FF0000";
                                                } else if ($curr_date < $task_finish_date && $curr_date >= $notice_date_end) {
                                                    $cut_color = "orange";
                                                } else if (!($actual_finish_date == "" || $actual_finish_date == "0000-00-00") && $actual_finish_date > $task_finish_date) {
                                                    $cut_color = "blue";
                                                } else {
                                                    $cut_color = "#000000";
                                                }
                                            } else {
                                                $cut_color = "#000000";
                                            }
                                        } else {
                                            $cut_color = "#000000";
                                        }


										?>
                                        <a href="##" <? echo "style='color:$cut_color'"; ?> onclick="show_progress_report_details('cutting_finish_popup', '<? echo $row[csf("job_no")]; ?>_<? echo $row[csf('id')] . "_" . $tna_integrated . "_" . $actual_finish_date . "_" . $actual_start_date; ?>', '850px', '<? echo $type_search; ?>', '<? echo $country_ids; ?>')"><? echo number_format($cutting_qty_perc, 2) . ' ' . '%'; ?></a>
                                    </td>
                                    <td width="80" align="right">
                                        <?
                                        if ($prnt_qty != '') {
                                            $prnt_qty_perc = ($prnt_qty * 100) / ($row[csf('po_quantity')]);
                                        } else {
                                            $prnt_qty_perc = '0.00';
                                        }

                                        $task_finish_date = '';
                                        $notice_date_end = '';
                                        $actual_start_date = '';
                                        $actual_finish_date = '';
                                        if ($tna_integrated == 1) {
                                            $task_finish_date = $tna_array[$row[csf('id')]][85]['task_finish_date'];
                                            $notice_date_end = $tna_array[$row[csf('id')]][85]['notice_date_end'];
                                            $actual_finish_date = $tna_array[$row[csf('id')]][85]['actual_finish_date'];
                                            $actual_start_date = $tna_array[$row[csf('id')]][85]['actual_start_date'];

                                            if ($prnt_qty_perc < 100) {
                                                if ($curr_date > $task_finish_date && ($actual_finish_date == "" || $actual_finish_date == "0000-00-00")) {
                                                    $print_color = "#FF0000";
                                                } else if ($curr_date < $task_finish_date && $curr_date >= $notice_date_end) {
                                                    $print_color = "orange";
                                                } else if (!($actual_finish_date == "" || $actual_finish_date == "0000-00-00") && $actual_finish_date > $task_finish_date) {
                                                    $print_color = "blue";
                                                } else {
                                                    $print_color = "#000000";
                                                }
                                            } else {
                                                $print_color = "#000000";
                                            }
                                        } else {
                                            $print_color = "#000000";
                                        }
                                        ?>
                                        <a href="##" <? echo "style='color:$print_color'"; ?> onclick="show_progress_report_details('print_emb_completed_popup', '<? echo $row[csf("job_no")]; ?>_<? echo $row[csf('id')] . "_" . $tna_integrated . "_" . $actual_finish_date . "_" . $actual_start_date; ?>', '850px', '<? echo $type_search; ?>', '<? echo $country_ids; ?>')"><? echo number_format($prnt_qty_perc, 2) . ' ' . '%'; ?></a>
                                    </td>
                                    <td width="80" align="right">
                                        <?
                                        if ($embel_qty != '') {
                                            $embel_qty_perc = ($embel_qty * 100) / ($row[csf('po_quantity')]);
                                        } else {
                                            $embel_qty_perc = '0.00';
                                        }

                                        $task_finish_date = '';
                                        $notice_date_end = '';
                                        $actual_start_date = '';
                                        $actual_finish_date = '';
                                        if ($tna_integrated == 1) {
                                            $task_finish_date = $tna_array[$row[csf('id')]][85]['task_finish_date'];
                                            $notice_date_end = $tna_array[$row[csf('id')]][85]['notice_date_end'];
                                            $actual_finish_date = $tna_array[$row[csf('id')]][85]['actual_finish_date'];
                                            $actual_start_date = $tna_array[$row[csf('id')]][85]['actual_start_date'];

                                            if ($embel_qty_perc < 100) {
                                                if ($curr_date > $task_finish_date && ($actual_finish_date == "" || $actual_finish_date == "0000-00-00")) {
                                                    $emb_color = "#FF0000";
                                                } else if ($curr_date < $task_finish_date && $curr_date >= $notice_date_end) {
                                                    $emb_color = "orange";
                                                } else if (!($actual_finish_date == "" || $actual_finish_date == "0000-00-00") && $actual_finish_date > $task_finish_date) {
                                                    $emb_color = "blue";
                                                } else {
                                                    $emb_color = "#000000";
                                                }
                                            } else {
                                                $emb_color = "#000000";
                                            }
                                        } else {
                                            $emb_color = "#000000";
                                        }
                                        ?>

                                        <a href="##" <? echo "style='color:$emb_color'"; ?> onclick="show_progress_report_details('print_emb_completed_popup', '<? echo $row[csf("job_no")]; ?>_<? echo $row[csf('id')] . "_" . $tna_integrated . "_" . $actual_finish_date . "_" . $actual_start_date; ?>', '850px', '<? echo $type_search; ?>', '<? echo $country_ids; ?>')"><? echo number_format($embel_qty_perc, 2) . ' ' . '%'; ?></a>
                                    </td>
                                    <td width="80" align="right">
                                        <?
                                        if ($wash_qty != '') {
                                            $wash_qty_perc = ($wash_qty * 100) / ($row[csf('po_quantity')]);
                                        } else {
                                            $wash_qty_perc = '0.00';
                                        }

                                        $task_finish_date = '';
                                        $notice_date_end = '';
                                        $actual_start_date = '';
                                        $actual_finish_date = '';
                                        if ($tna_integrated == 1) {
                                            $task_finish_date = $tna_array[$row[csf('id')]][85]['task_finish_date'];
                                            $notice_date_end = $tna_array[$row[csf('id')]][85]['notice_date_end'];
                                            $actual_finish_date = $tna_array[$row[csf('id')]][85]['actual_finish_date'];
                                            $actual_start_date = $tna_array[$row[csf('id')]][85]['actual_start_date'];

                                            if ($wash_qty_perc < 100) {
                                                if ($curr_date > $task_finish_date && ($actual_finish_date == "" || $actual_finish_date == "0000-00-00")) {
                                                    $wash_color = "#FF0000";
                                                } else if ($curr_date < $task_finish_date && $curr_date >= $notice_date_end) {
                                                    $wash_color = "orange";
                                                } else if (!($actual_finish_date == "" || $actual_finish_date == "0000-00-00") && $actual_finish_date > $task_finish_date) {
                                                    $wash_color = "blue";
                                                } else {
                                                    $wash_color = "#000000";
                                                }
                                            } else {
                                                $wash_color = "#000000";
                                            }
                                        } else {
                                            $wash_color = "#000000";
                                        }
                                        ?>

                                        <a href="##" <? echo "style='color:$wash_color'"; ?> onclick="show_progress_report_details('print_emb_completed_popup', '<? echo $row[csf("job_no")]; ?>_<? echo $row[csf('id')] . "_" . $tna_integrated . "_" . $actual_finish_date . "_" . $actual_start_date; ?>', '850px', '<? echo $type_search; ?>', '<? echo $country_ids; ?>')"><? echo number_format($wash_qty_perc, 2) . ' ' . '%'; ?></a>
                                    </td>
                                    <td width="80" align="right">
                                        <?
                                        if ($special_qty != '') {
                                            $special_qty_perc = ($special_qty * 100) / ($row[csf('po_quantity')]);
                                        } else {
                                            $special_qty_perc = '0.00';
                                        }

                                        $task_finish_date = '';
                                        $notice_date_end = '';
                                        $actual_start_date = '';
                                        $actual_finish_date = '';
                                        if ($tna_integrated == 1) {
                                            $task_finish_date = $tna_array[$row[csf('id')]][85]['task_finish_date'];
                                            $notice_date_end = $tna_array[$row[csf('id')]][85]['notice_date_end'];
                                            $actual_finish_date = $tna_array[$row[csf('id')]][85]['actual_finish_date'];
                                            $actual_start_date = $tna_array[$row[csf('id')]][85]['actual_start_date'];

                                            if ($special_qty_perc < 100) {
                                                if ($curr_date > $task_finish_date && ($actual_finish_date == "" || $actual_finish_date == "0000-00-00")) {
                                                    $sp_color = "#FF0000";
                                                } else if ($curr_date < $task_finish_date && $curr_date >= $notice_date_end) {
                                                    $sp_color = "orange";
                                                } else if (!($actual_finish_date == "" || $actual_finish_date == "0000-00-00") && $actual_finish_date > $task_finish_date) {
                                                    $sp_color = "blue";
                                                } else {
                                                    $sp_color = "#000000";
                                                }
                                            } else {
                                                $sp_color = "#000000";
                                            }
                                        } else {
                                            $sp_color = "#000000";
                                        }
                                        ?>

                                        <a href="##" <? echo "style='color:$sp_color'"; ?> onclick="show_progress_report_details('print_emb_completed_popup', '<? echo $row[csf("job_no")]; ?>_<? echo $row[csf('id')] . "_" . $tna_integrated . "_" . $actual_finish_date . "_" . $actual_start_date; ?>', '850px', '<? echo $type_search; ?>', '<? echo $country_ids; ?>')"><? echo number_format($special_qty_perc, 2) . ' ' . '%'; ?></a>
                                    </td>
                                    <td width="80" align="right"   title="Calculation Based on Order Qnty.">
                                        <?
                                        if ($sewingout_qty != '') {
                                            $sewingout_qty_perc = ($sewingout_qty * 100) / ($row[csf('po_quantity')]);
											if(is_infinite($sewingout_qty_perc) || is_nan($sewingout_qty_perc)){$sewingout_qty_perc=0;}

                                        } else {
                                            $sewingout_qty_perc = '0.00';
                                        }
                                        $task_finish_date = '';
                                        $notice_date_end = '';
                                        $actual_start_date = '';
                                        $actual_finish_date = '';
                                        if ($tna_integrated == 1) {
                                            $task_finish_date = $tna_array[$row[csf('id')]][86]['task_finish_date'];
                                            $notice_date_end = $tna_array[$row[csf('id')]][86]['notice_date_end'];
                                            $actual_finish_date = $tna_array[$row[csf('id')]][86]['actual_finish_date'];
                                            $actual_start_date = $tna_array[$row[csf('id')]][86]['actual_start_date'];

                                            if ($sewingout_qty_perc < 100) {
                                                if ($curr_date > $task_finish_date && ($actual_finish_date == "" || $actual_finish_date == "0000-00-00")) {
                                                    $sew_color = "#FF0000";
                                                } else if ($curr_date < $task_finish_date && $curr_date >= $notice_date_end) {
                                                    $sew_color = "orange";
                                                } else if (!($actual_finish_date == "" || $actual_finish_date == "0000-00-00") && $actual_finish_date > $task_finish_date) {
                                                    $sew_color = "blue";
                                                } else {
                                                    $sew_color = "#000000";
                                                }
                                            } else {
                                                $sew_color = "#000000";
                                            }
                                        } else {
                                            $sew_color = "#000000";
                                        }
                                        ?>
                                        <a href="##" <? echo "style='color:$sew_color'"; ?> onclick="show_progress_report_details('sewing_finish_popup', '<? echo $row[csf("job_no")]; ?>_<? echo $row[csf('id')] . "_" . $tna_integrated . "_" . $actual_finish_date . "_" . $actual_start_date; ?>', '850px', '<? echo $type_search; ?>', '<? echo $country_ids; ?>')"><? echo number_format($sewingout_qty_perc, 2) . ' ' . '%'; ?></a>
                                    </td>
                                    <td width="80" align="right">
                                        <a href="##" onclick="show_progress_report_details('iron_output_popup', '<? echo $row[csf("job_no")]; ?>_<? echo $row[csf('id')]; ?>', '850px', '<? echo $type_search; ?>', '<? echo $row[csf('country_id')]; ?>')">
                                            <?
                                            if ($ironoutput_qty != '') {
                                                $ironoutput_qty_perc = ($ironoutput_qty * 100) / ($row[csf('po_quantity')]);
                                                if(is_infinite($ironoutput_qty_perc) || is_nan($ironoutput_qty_perc)){$ironoutput_qty_perc=0;}
												echo number_format($ironoutput_qty_perc, 2) . ' ' . '%';
                                            } else
                                                echo '0.00' . '%';
                                            ?>
                                        </a>
                                    </td>
                                    <td width="80" align="right"  title="Calculation Based on Order Qnty.">
                                        <?
                                        if ($finish_qty != '') {
                                            $finish_qty_perc = ($finish_qty * 100) / ($row[csf('po_quantity')]);
											if(is_infinite($finish_qty_perc) || is_nan($finish_qty_perc)){$finish_qty_perc=0;}
                                        } else
                                            $finish_qty_perc = '0.00';

                                        $task_finish_date = '';
                                        $notice_date_end = '';
                                        $actual_start_date = '';
                                        $actual_finish_date = '';
                                        if ($tna_integrated == 1) {
                                            $task_finish_date = $tna_array[$row[csf('id')]][88]['task_finish_date'];
                                            $notice_date_end = $tna_array[$row[csf('id')]][88]['notice_date_end'];
                                            $actual_finish_date = $tna_array[$row[csf('id')]][88]['actual_finish_date'];
                                            $actual_start_date = $tna_array[$row[csf('id')]][88]['actual_start_date'];

                                            if ($finish_qty_perc < 100) {
                                                if ($curr_date > $task_finish_date && ($actual_finish_date == "" || $actual_finish_date == "0000-00-00")) {
                                                    $pack_color = "#FF0000";
                                                } else if ($curr_date < $task_finish_date && $curr_date >= $notice_date_end) {
                                                    $pack_color = "orange";
                                                } else if (!($actual_finish_date == "" || $actual_finish_date == "0000-00-00") && $actual_finish_date > $task_finish_date) {
                                                    $pack_color = "blue";
                                                } else {
                                                    $pack_color = "#000000";
                                                }
                                            } else {
                                                $pack_color = "#000000";
                                            }
                                        } else {
                                            $pack_color = "#000000";
                                        }
                                        ?>
                                        <a href="##" <? echo "style='color:$pack_color'"; ?> onclick="show_progress_report_details('finish_completed_popup', '<? echo $row[csf("job_no")]; ?>_<? echo $row[csf('id')] . "_" . $tna_integrated . "_" . $actual_finish_date . "_" . $actual_start_date; ?>', '850px', '<? echo $type_search; ?>', '<? echo $country_ids; ?>')"><? echo number_format($finish_qty_perc, 2) . ' ' . '%'; ?></a>
                                    </td>
                                    <td width="80" align="right">
                                        <?
                                        $insp_qty_perc = ($inspection_qty * 100) / ($row[csf('po_quantity')]);
										if(is_infinite($insp_qty_perc) || is_nan($insp_qty_perc)){$insp_qty_perc=0;}
                                        $task_finish_date = '';
                                        $notice_date_end = '';
                                        $actual_start_date = '';
                                        $actual_finish_date = '';
                                        if ($tna_integrated == 1) {
                                            $task_finish_date = $tna_array[$row[csf('id')]][101]['task_finish_date'];
                                            $notice_date_end = $tna_array[$row[csf('id')]][101]['notice_date_end'];
                                            $actual_finish_date = $tna_array[$row[csf('id')]][101]['actual_finish_date'];
                                            $actual_start_date = $tna_array[$row[csf('id')]][101]['actual_start_date'];

                                            if ($insp_qty_perc < 100) {
                                                if ($curr_date > $task_finish_date && ($actual_finish_date == "" || $actual_finish_date == "0000-00-00")) {
                                                    $insp_color = "#FF0000";
                                                } else if ($curr_date < $task_finish_date && $curr_date >= $notice_date_end) {
                                                    $insp_color = "orange";
                                                } else if (!($actual_finish_date == "" || $actual_finish_date == "0000-00-00") && $actual_finish_date > $task_finish_date) {
                                                    $insp_color = "blue";
                                                } else {
                                                    $insp_color = "#000000";
                                                }
                                            } else {
                                                $insp_color = "#000000";
                                            }
                                        } else {
                                            $insp_color = "#000000";
                                        }
                                        ?>
                                        <a href="##" <? echo "style='color:$insp_color'"; ?> onclick="show_progress_report_details('buyer_inspection_popup', '<? echo $row[csf("job_no")]; ?>_<? echo $row[csf('id')] . "_" . $tna_integrated . "_" . $actual_finish_date . "_" . $actual_start_date; ?>', '850px', '<? echo $type_search; ?>', '<? echo $country_ids; ?>')"><? echo number_format($insp_qty_perc, 2) . ' ' . '%'; ?> </a>
                                    </td>
                                    <td width="100" align="right">
                                        <?
                                        $exf_perc = ($exf_qty * 100) / ($row[csf('po_quantity')]);
										if(is_infinite($exf_perc) || is_nan($exf_perc)){$exf_perc=0;}
                                        $task_finish_date = '';
                                        $notice_date_end = '';
                                        $actual_start_date = '';
                                        $actual_finish_date = '';
                                        if ($tna_integrated == 1) {
                                            $task_finish_date = $tna_array[$row[csf('id')]][110]['task_finish_date'];
                                            $notice_date_end = $tna_array[$row[csf('id')]][110]['notice_date_end'];
                                            $actual_finish_date = $tna_array[$row[csf('id')]][110]['actual_finish_date'];
                                            $actual_start_date = $tna_array[$row[csf('id')]][110]['actual_start_date'];

                                            if ($exf_perc < 100) {
                                                if ($curr_date > $task_finish_date && ($actual_finish_date == "" || $actual_finish_date == "0000-00-00")) {
                                                    $exf_color = "#FF0000";
                                                } else if ($curr_date < $task_finish_date && $curr_date >= $notice_date_end) {
                                                    $exf_color = "orange";
                                                } else if (!($actual_finish_date == "" || $actual_finish_date == "0000-00-00") && $actual_finish_date > $task_finish_date) {
                                                    $exf_color = "blue";
                                                } else {
                                                    $exf_color = "#000000";
                                                }
                                            } else {
                                                $exf_color = "#000000";
                                            }
                                        } else {
                                            $exf_color = "#000000";
                                        }

                                        $invoice_color = "";
                                        if ($exf_qty > $ship_qty_as_per) {
                                            $invoice_color = "#FF0000";
                                        }
										if(is_infinite($exf_qty) || is_nan($exf_qty)){$exf_qty=0;}
                                        ?>
                                        <a href="##" <? echo "style='color:$exf_color'"; ?> onclick="show_progress_report_details('ex_factory_popup', '<? echo $row[csf("job_no")]; ?>_<? echo $row[csf('id')] . "_" . $tna_integrated . "_" . $actual_finish_date . "_" . $actual_start_date; ?>', '850px', '<? echo $type_search; ?>', '<? echo $country_ids; ?>')"><?
                                        echo number_format($exf_qty, 0, '.', '');
                                        $tsq += $exf_qty;
                                        ?></a>
                                    </td>
                                    <td width="80" align="center">
                                        <a href="##" onclick="show_progress_report_details('actual_shipment_popup', '<? echo $row[csf("job_no")]; ?>_<? echo $row[csf('id')]; ?>', '850px', '<? echo $type_search; ?>', '<? echo $country_ids; ?>')">View</a>
                                    </td>
                                    <td width="90" align="right" bgcolor="<? echo $invoice_color; ?>">
                                        <?
                                        echo number_format($ship_qty_as_per, 0, '.', '');
                                        ?>
                                    </td>
                                    <td width="100" align="right">
                                        <?
                                        echo number_format($ship_value, 2, '.', '');
                                        ?>
                                    </td>
                                    <td align="right" width="100">
                                        <?
                                        $balance_ship_qnty = $row[csf('po_quantity')] - $ship_qty_as_per;
                                        echo number_format($balance_ship_qnty, 0, '.', '');
                                        ?>
                                    </td>
                                    <td width="120" align="right">
									<?
                                    $balance_ship_qnty_as_ex = $exf_qty - $ship_qty_as_per;
                                    echo number_format($balance_ship_qnty_as_ex, 0, '.', '');
                                    ?>
                                    </td>
                                    <td><p><? echo $row[csf('details_remarks')]; ?></p></td>
                                </tr>
                            <?
                            $i++;
                        }
                    }// end Country Ship Date report generate====================================================
                    ?>
                    
                    </table>
    <?
    if (str_replace("'", "", $cbo_search_by) == 3) {
        ?>
                        <table border="1" class="rpt_table" width="4220" rules="all" id="report_table_footer" >
                            <tfoot>
                            <th width="40">&nbsp;</th>
                            <th width="60"></th>
                            <th width="60"></th>
                            <th width="100"></th>
                            <th width="100"></th>
                            <th width="70"></th>
                            <th width="80"></th>
                            <th width="80"></th>
                            <th width="100"></th>
                            <th width="100"></th>
                            <th width="80"></th>
                            <th width="50"></th>
                            <th width="100"></th>
                            <th width="100"></th>
                            <th width="90" id="total_order_qnty"></th>
                            <th width="100" id="value_total_order_value"></th>
                            <th width="100" id="total_order_qnty_pcs"></th>
                            <th width="60"></th>
                            <th width="60"></th>
                            <th width="80"></th>
                            <th width="80"></th>
                            <th width="80"></th>
                            <th width="60"></th>
                            <th width="80"></th>
                            <th width="80"></th>
                            <th width="80"></th>
                            <th width="80"></th>
                            <th width="80"></th>
                            <? if($garmentBtn != 2){?>
                            <th width="80"></th>
                            <? }?>
                            <th width="80"></th>
                            <? if($garmentBtn != 2){?>
                            <th width="80"></th>
                            <? }?>
                            <th width="80"></th>
                            <? if($garmentBtn != 2){?>
                            <th width="80"></th>
                            <? }?>
                            <th width="80"></th>
                            <th width="80"></th>
                            <th width="80"></th>
                            <th width="80"></th>
                            <th width="80"></th>
                            <th width="80"></th>
                            <th width="80"></th>
                            <th width="80"></th>
                            <th width="80"></th>
                            <th width="80"></th>
                            <th width="80"></th>
                            <th width="100" id="td_ship_per_ex_qty"><? echo $tsq; ?></th>
                            <th width="80"></th>
                            <th width="90" id="total_ship_qnty"></th>
                            <th width="100" id="value_total_ship_value"></th>
                            <th width="100" id="total_balance_ship_qnty"></th>
                            <th width="120" id="total_balance_ship_qnty_as_ex"></th>
                            <th></th>
                            </tfoot>
                        </table>
        <?
    } else {
        ?>
                        <table border="1" class="rpt_table"  width="3960" rules="all" id="report_table_footer" >
                            <tfoot>
                            <th width="40">&nbsp;</th>
                            <th width="60"></th>
                            <th width="60"></th>
                            <th width="100"></th>
                            <th width="100"></th>
                            <th width="70"></th>
                           
                            <th width="100"></th>
                            <th width="100"></th>
                            <th width="100"></th>
                            <th width="80"></th>
                            <th width="80"></th>
                            <th width="50"></th>
                            <th width="50"></th>
                            <th width="100"></th>
                            <th width="50" ></th>
                            <th width="100" ></th>
                            <th width="90" ></th>
                            <th width="100" id="value_total_order_value"></th>
                            <th width="100" id="total_order_qnty_pcs"></th>
                            <th width="80"></th>
                            <th width="60"></th>
                            <th width="60"></th>
                            <th width="80"></th>
                            <th width="80"></th>
                            <th width="80"></th>
                            <th width="60"></th>
                            <th width="80"></th>
                            <? if($garmentBtn != 2){?>
                            <th width="80"></th>
                            <? } ?>
                            <th width="80"></th>
                            <? if($garmentBtn != 2){?>
                            <th width="80"></th>
                            <? } ?>
                            <th width="80"></th>
                            <? if($garmentBtn != 2){?>
                            <th width="80"></th>
                            <? } ?>
                            <th width="80"></th>
                            <th width="80"></th>
                            <th width="80">D</th>
                            <th width="80"></th>
                            <th width="80"></th>
                            <th width="80"><? //echo $rrrrrrrrrr; ?></th>
                            <th width="80"></th>
                            <th width="80"></th>
                            <th width="80"></th>
                            <th width="80" id="td_ship_per_ex_qty"><? echo $tsq; ?></th>
                            <th width="80"></th>
                             <th width="80" id="td_wvn_fin_per_qty"></th>
                            <th width="90" id="total_ship_qnty">CS</th>
                            <th width="100" id="value_total_ship_value"></th>
                            <th width="120" id="total_balance_ship_qnty"></th>
                            <th></th>
                            </tfoot>
                        </table>
            <?
        }
        ?>
                </div>
        </div>
        <?        
        foreach (glob("$user_id*.xls") as $filename)
        {

            if( @filemtime($filename) < (time()-$seconds_old) )
            @unlink($filename);
        }
        //---------end------------//
        $name=time();
        $filename=$user_id."_".$name.".xls";
        $create_new_doc = fopen($filename, 'w') or die('can not open');
        $is_created = fwrite($create_new_doc,ob_get_contents()) or die('can not write');
        //$filename=$user_id."_".$name.".xls";
        echo "$total_data####$garmentBtn####$filename";
        exit();
       /* $html = ob_get_contents();
        ob_clean();
        $new_link = create_delete_report_file($html, 1, 1, "../../../");

        echo "$html**".$garmentBtn;
        exit();*/
    }






//######################################## ALL POP UP Here START ##############################
//#############################################################################################
	
	//
	if ($action == "wash_booked_style_popup")
	 {
        echo load_html_head_contents("Wash Booking Dtls", "../../../../", 1, 1, $unicode, '', '');
	
        list($job_number,$po_id) = explode('_', $data_str);
		$po_id=str_replace("'","",$po_id);
		$job_number=str_replace("'","",$job_number);
		
        //$po_arr = return_library_array("select id, po_number from wo_po_break_down where id in ($po_id)", 'id', 'po_number');
		$job_sql = sql_select("select job_no,buyer_name,company_name,style_ref_no from wo_po_details_master where job_no='$job_number'");
		$row_job=$job_sql[0];
		
		
		
		
		//Wash Booking...........
			$wash_booking_sql="SELECT d.BOOKING_DATE,a.BOOKING_NO,a.PRE_COST_FABRIC_COST_DTLS_ID,
            a.GMT_ITEM,a.PO_BREAK_DOWN_ID,b.ITEM_COLOR,b.REQUIRMENT,b.RATE, b.AMOUNT, c.EMB_NAME,c.EMB_TYPE,c.BODY_PART_ID,c.SUPPLIER_ID
			FROM wo_booking_dtls a, wo_emb_book_con_dtls b, wo_pre_cost_embe_cost_dtls c,wo_booking_mst d
			WHERE a.id = b.wo_booking_dtls_id
			AND a.booking_no = b.booking_no
			AND b.booking_no=d.booking_no
			AND a.pre_cost_fabric_cost_dtls_id = c.id
			and a.po_break_down_id in($po_id) AND a.sensitivity = 1 AND b.requirment != 0 AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0";
		$wash_booking_sql_result = sql_select($wash_booking_sql);
		$woven_wash_booked_arr = array();
		foreach ($wash_booking_sql_result as $val) {
			  $key=$val[PO_BREAK_DOWN_ID].$val[EMB_NAME].$val[EMB_TYPE];
			  $woven_wash_booked_arr[$key][BOOKED_QTY] += $val[REQUIRMENT];
			  $woven_wash_booked_arr[$key][BOOKING_NO][$val[BOOKING_NO]]= $val[BOOKING_NO];
			  
			 $woven_wash_booked_no_arr[BOOKING_NO][$val[BOOKING_NO]]+= $val[REQUIRMENT];
			 $woven_wash_booked_no_arr[BOOKING_DATE][$val[BOOKING_NO]]= $val[BOOKING_DATE];
 
			  
		}
		
		
//Wash Req for Booking......................... //*e.TOTAL_SET_QNTY
		$wash_req_sql="SELECT d.PO_NUMBER,max(d.PO_QUANTITY) as PO_QUANTITY,a.job_no,a.po_break_down_id,a.item_number_id, c.EMB_TYPE,c.EMB_NAME,e.COSTING_PER,
		
		sum (a.requirment*b.plan_cut_qnty) as REQUIRMENT 
		FROM wo_pre_cos_emb_co_avg_con_dtls a, wo_po_color_size_breakdown b,wo_pre_cost_embe_cost_dtls c,WO_PO_BREAK_DOWN d,wo_pre_cost_mst e 
		
		WHERE a.job_no=e.job_no and d.JOB_NO_MST=e.job_no and a.po_break_down_id=d.id and a.color_size_table_id=b.id AND c.id = a.PRE_COST_EMB_COST_DTLS_ID and a.po_break_down_id in($po_id) group by d.PO_NUMBER,a.job_no,a.po_break_down_id,a.item_number_id, c.EMB_TYPE,c.EMB_NAME,e.COSTING_PER";
		$wash_req_sql_result = sql_select($wash_req_sql);
						
		 //echo $wash_req_sql;
				
		
        ?>
        <div style="width:100%" align="center">
            <fieldset style="width:850px">
                <table align="center" cellpadding="10">

                    <tr class="form_caption">
                        <td align="center" colspan="4"><strong>Wash Booking Details</strong></td>
                    </tr>
                    <tr>
                        <td align="right"> <strong>Job Number</strong> :</td>
                        <td align="left"><? echo $job_number; ?></td>
                        <td align="right"><strong>Buyer Name</strong> :</td>
                        <td align="left"><? echo $buyer_short_name_arr[$row_job[csf("buyer_name")]]; ?></td>
                    </tr>
                    <tr>
                        <td align="right"><strong>Company Name</strong> :</td>
                        <td align="left"><? echo $company_short_name_arr[$row_job[csf("company_name")]]; ?></td>
                        <td align="right"><strong>Style Ref No</strong> : </td>
                        <td align="left"><? echo $row_job[csf("style_ref_no")]; ?> </td>
                    </tr>
                 
                </table>
                <div style="width:100%;" align="left">
                    <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                        <thead>
                            <tr>
                                <th width="35">SL</th>	
                                <th width="100">PO Number</th>	
                                <th width="80">PO Qnty</th>	
                                <th width="100">Wash Name</th>	
                                <th width="100">Wash Type</th>	
                                <th width="60">UOM</th>	
                                <th width="80">Required Qty	</th>
                                <th width="80">Booking Qnty	</th>
                                <th width="80">Yet to Service</th>	
                                <th>Booking No</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <div style="width:100%; max-height:270px; overflow-y:scroll" align="left">
                    <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >
                        <?
						$i = 1;
						foreach ($wash_req_sql_result as $row) {
						//$woven_wash_req_arr[$val[csf('emb_type')]] = $val[csf('requirment')];
						 
						  if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						 if($row[COSTING_PER]==1){$row[REQUIRMENT]=$row[REQUIRMENT]/(1*12);$uom="DZN";}
						 else if($row[COSTING_PER]==2){$row[REQUIRMENT]=$row[REQUIRMENT];$uom="PCS";}
						 else if($row[COSTING_PER]==3){$row[REQUIRMENT]=$row[REQUIRMENT]/(2*12);$uom="DZN";}
						 else if($row[COSTING_PER]==4){$row[REQUIRMENT]=$row[REQUIRMENT]/(3*12);$uom="DZN";}
						 else if($row[COSTING_PER]==5){$row[REQUIRMENT]=$row[REQUIRMENT]/(4*12);$uom="DZN";}

						 
						 
						 $key=$row[PO_BREAK_DOWN_ID].$row[EMB_NAME].$row[EMB_TYPE];
						?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            <td width="35" align="center"><? echo $i; ?></td>
                            <td width="100"><p><? echo $row[PO_NUMBER]; ?></p></td>
                            <td width="80" align="right"><p><? echo $row[PO_QUANTITY]; ?></p></td>
                            <td width="100"><p><? echo $emblishment_name_array[$row[EMB_NAME]]; ?></p></td>
                            <td width="100"><p><? echo $emblishment_wash_type[$row[EMB_TYPE]]; ?></p></td>
                            <td width="60" align="center"><p><? echo $uom; ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($row[REQUIRMENT],2); ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($woven_wash_booked_arr[$key][BOOKED_QTY],2); ?></p></td>
                            <td width="80" align="right"><p><? 
							if($row[REQUIRMENT]-$woven_wash_booked_arr[$key][BOOKED_QTY]<0 && $row[REQUIRMENT]-$woven_wash_booked_arr[$key][BOOKED_QTY]> -1){ echo "0.00";}
							else{
							echo number_format($row[REQUIRMENT]-$woven_wash_booked_arr[$key][BOOKED_QTY],2);
							}
							
							?></p></td>
                            <td><p><? echo implode(',',$woven_wash_booked_arr[$key][BOOKING_NO]); ?></p></td>
                         </tr>
                         <?
						 $i++;
						}
						?>
                    </table>
                    <br />
                    <table cellpadding="0" class="rpt_table" rules="all" border="1" >
                        <thead>
                            <th width="35" align="center">SL</th>	
                            <th width="80">WO Date</th>	
                            <th width="100">Booking No</th>	
                            <th width="80">Booking Qnty</th>
                        </thead>
					<?
					$i=1;
                    foreach($woven_wash_booked_no_arr[BOOKING_NO] as $BOOKING_NO=>$REQUIRMENT){
					?>
                        <tbody>
                            <td><?= $i;?></td>	
                            <td align="center"><?= $woven_wash_booked_no_arr[BOOKING_DATE][$BOOKING_NO];?></td>	
                            <td align="center"><?= $BOOKING_NO;?></td>	
                            <td align="right"><?= number_format($REQUIRMENT,2);?></td>
                        </tbody>
                    <?
					$TOTAL_REQUIRMENT+=$REQUIRMENT;
					$i++;
                    }
					?>
                        <tfoot>
                            <td align="center" colspan="3">Total</td>	
                            <td align="right"><?= number_format($TOTAL_REQUIRMENT,2);?></td>
                        </tfoot>
                    </table>
                    
                    
                </div>
            </fieldset>
        </div>
        <?
        exit();
    }


	if ($action == "wash_booked_popup")
	 {
        echo load_html_head_contents("Wash Booking Dtls", "../../../../", 1, 1, $unicode, '', '');

        list($job_number,$po_id) = explode('_', $data_str);
		
        //$po_arr = return_library_array("select id, po_number from wo_po_break_down where id in ($po_id)", 'id', 'po_number');
		$job_sql = sql_select("select job_no,buyer_name,company_name,style_ref_no from wo_po_details_master where job_no='$job_number'");
		$row_job=$job_sql[0];
		
		
		
		
		//Wash Booking...........
			$wash_booking_sql="SELECT d.BOOKING_DATE,a.BOOKING_NO,a.PRE_COST_FABRIC_COST_DTLS_ID,
            a.GMT_ITEM,a.PO_BREAK_DOWN_ID,b.ITEM_COLOR,b.REQUIRMENT,b.RATE, b.AMOUNT, c.EMB_NAME,c.EMB_TYPE,c.BODY_PART_ID,c.SUPPLIER_ID
			FROM wo_booking_dtls a, wo_emb_book_con_dtls b, wo_pre_cost_embe_cost_dtls c,wo_booking_mst d
			WHERE a.id = b.wo_booking_dtls_id
			AND a.booking_no = b.booking_no
			AND b.booking_no=d.booking_no
			AND a.pre_cost_fabric_cost_dtls_id = c.id
			and a.po_break_down_id=$po_id AND a.sensitivity = 1 AND b.requirment != 0 AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0";
		$wash_booking_sql_result = sql_select($wash_booking_sql);
		$woven_wash_booked_arr = array();
		foreach ($wash_booking_sql_result as $val) {
			  $key=$val[PO_BREAK_DOWN_ID].$val[EMB_NAME].$val[EMB_TYPE];
			  $woven_wash_booked_arr[$key][BOOKED_QTY] += $val[REQUIRMENT];
			  $woven_wash_booked_arr[$key][BOOKING_NO][$val[BOOKING_NO]]= $val[BOOKING_NO];
			  
			 $woven_wash_booked_no_arr[BOOKING_NO][$val[BOOKING_NO]]+= $val[REQUIRMENT];
			 $woven_wash_booked_no_arr[BOOKING_DATE][$val[BOOKING_NO]]= $val[BOOKING_DATE];
 
			  
		}
		
		
//Wash Req for Booking......................... //*e.TOTAL_SET_QNTY
		$wash_req_sql="SELECT d.PO_NUMBER,max(d.PO_QUANTITY) as PO_QUANTITY,a.job_no,a.po_break_down_id,a.item_number_id, c.EMB_TYPE,c.EMB_NAME,e.COSTING_PER,
		
		sum (a.requirment*b.plan_cut_qnty) as REQUIRMENT 
		FROM wo_pre_cos_emb_co_avg_con_dtls a, wo_po_color_size_breakdown b,wo_pre_cost_embe_cost_dtls c,WO_PO_BREAK_DOWN d,wo_pre_cost_mst e 
		
		WHERE a.job_no=e.job_no and d.JOB_NO_MST=e.job_no and a.po_break_down_id=d.id and a.color_size_table_id=b.id AND c.id = a.PRE_COST_EMB_COST_DTLS_ID and a.po_break_down_id=$po_id group by d.PO_NUMBER,a.job_no,a.po_break_down_id,a.item_number_id, c.EMB_TYPE,c.EMB_NAME,e.COSTING_PER";
		$wash_req_sql_result = sql_select($wash_req_sql);
						
		 //echo $wash_req_sql;
				
		
        ?>
        <div style="width:100%" align="center">
            <fieldset style="width:850px">
                <table align="center" cellpadding="10">

                    <tr class="form_caption">
                        <td align="center" colspan="4"><strong>Wash Booking Details</strong></td>
                    </tr>
                    <tr>
                        <td align="right"> <strong>Job Number</strong> :</td>
                        <td align="left"><? echo $job_number; ?></td>
                        <td align="right"><strong>Buyer Name</strong> :</td>
                        <td align="left"><? echo $buyer_short_name_arr[$row_job[csf("buyer_name")]]; ?></td>
                    </tr>
                    <tr>
                        <td align="right"><strong>Company Name</strong> :</td>
                        <td align="left"><? echo $company_short_name_arr[$row_job[csf("company_name")]]; ?></td>
                        <td align="right"><strong>Style Ref No</strong> : </td>
                        <td align="left"><? echo $row_job[csf("style_ref_no")]; ?> </td>
                    </tr>
                 
                </table>
                <div style="width:100%;" align="left">
                    <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                        <thead>
                            <tr>
                                <th width="35">SL</th>	
                                <th width="100">PO Number</th>	
                                <th width="80">PO Qnty</th>	
                                <th width="100">Wash Name</th>	
                                <th width="100">Wash Type</th>	
                                <th width="60">UOM</th>	
                                <th width="80">Required Qty	</th>
                                <th width="80">Booking Qnty	</th>
                                <th width="80">Yet to Service</th>	
                                <th>Booking No</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <div style="width:100%; max-height:270px; overflow-y:scroll" align="left">
                    <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >
                        <?
						$i = 1;
						foreach ($wash_req_sql_result as $row) {
						//$woven_wash_req_arr[$val[csf('emb_type')]] = $val[csf('requirment')];
						 
						 
						 if($row[COSTING_PER]==1){$row[REQUIRMENT]=$row[REQUIRMENT]/(1*12);$uom="DZN";}
						 else if($row[COSTING_PER]==2){$row[REQUIRMENT]=$row[REQUIRMENT];$uom="PCS";}
						 else if($row[COSTING_PER]==3){$row[REQUIRMENT]=$row[REQUIRMENT]/(2*12);$uom="DZN";}
						 else if($row[COSTING_PER]==4){$row[REQUIRMENT]=$row[REQUIRMENT]/(3*12);$uom="DZN";}
						 else if($row[COSTING_PER]==5){$row[REQUIRMENT]=$row[REQUIRMENT]/(4*12);$uom="DZN";}

						 
						 
						 $key=$row[PO_BREAK_DOWN_ID].$row[EMB_NAME].$row[EMB_TYPE];
						?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            <td width="35" align="center"><? echo $i; ?></td>
                            <td width="100"><p><? echo $row[PO_NUMBER]; ?></p></td>
                            <td width="80" align="right"><p><? echo $row[PO_QUANTITY]; ?></p></td>
                            <td width="100"><p><? echo $emblishment_name_array[$row[EMB_NAME]]; ?></p></td>
                            <td width="100"><p><? echo $emblishment_wash_type[$row[EMB_TYPE]]; ?></p></td>
                            <td width="60" align="center"><p><? echo $uom; ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($row[REQUIRMENT],2); ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($woven_wash_booked_arr[$key][BOOKED_QTY],2); ?></p></td>
                            <td width="80" align="right"><p><? 
							if($row[REQUIRMENT]-$woven_wash_booked_arr[$key][BOOKED_QTY]<0 && $row[REQUIRMENT]-$woven_wash_booked_arr[$key][BOOKED_QTY]> -1){ echo "0.00";}
							else{
							echo number_format($row[REQUIRMENT]-$woven_wash_booked_arr[$key][BOOKED_QTY],2);
							}
							
							?></p></td>
                            <td><p><? echo implode(',',$woven_wash_booked_arr[$key][BOOKING_NO]); ?></p></td>
                         </tr>
                         <?
						 $i++;
						}
						?>
                    </table>
                    <br />
                    <table cellpadding="0" class="rpt_table" rules="all" border="1" >
                        <thead>
                            <th width="35" align="center">SL</th>	
                            <th width="80">WO Date</th>	
                            <th width="100">Booking No</th>	
                            <th width="80">Booking Qnty</th>
                        </thead>
					<?
					$i=1;
                    foreach($woven_wash_booked_no_arr[BOOKING_NO] as $BOOKING_NO=>$REQUIRMENT){
					?>
                        <tbody>
                            <td><?= $i;?></td>	
                            <td align="center"><?= $woven_wash_booked_no_arr[BOOKING_DATE][$BOOKING_NO];?></td>	
                            <td align="center"><?= $BOOKING_NO;?></td>	
                            <td align="right"><?= number_format($REQUIRMENT,2);?></td>
                        </tbody>
                    <?
					$TOTAL_REQUIRMENT+=$REQUIRMENT;
					$i++;
                    }
					?>
                        <tfoot>
                            <td align="center" colspan="3">Total</td>	
                            <td align="right"><?= number_format($TOTAL_REQUIRMENT,2);?></td>
                        </tfoot>
                    </table>
                    
                    
                </div>
            </fieldset>
        </div>
        <?
        exit();
    }

    
	
	
	
	if ($action == "sample_status") {
        $color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
        $sampleArr = return_library_array("select id, sample_name from lib_sample", 'id', 'sample_name');
        echo load_html_head_contents("Sample Approve Details", "../../../../", 1, 1, $unicode, '', '');

        $expData = explode('_', $job_number);
        $job_number = $expData[0];
        $po_id = $expData[1];
        $po_arr = return_library_array("select id, po_number from wo_po_break_down where id in ($po_id)", 'id', 'po_number');
        ?>
        <div style="width:100%" align="center">
            <fieldset style="width:900px">
                <table width="600">
    <?
    $job_sql = sql_select("select job_no,buyer_name,company_name,style_ref_no from wo_po_details_master where job_no='$job_number'");
    foreach ($job_sql as $row_job)
        ;  // Master Job  table queery ends here
    ?>
                    <tr class="form_caption">
                        <td align="center" colspan="4"><strong>Sample Approval Details</strong></td>
                    </tr>
                    <tr>
                        <td align="right" width="130"> <strong>Job Number</strong> :</td>
                        <td align="left"  width="200"><? echo $job_number; ?></td>
                        <td align="right"  width="130"><strong>Buyer Name</strong> :</td>
                        <td align="left"><? echo $buyer_short_name_arr[$row_job[csf("buyer_name")]]; ?></td>
                    </tr>
                    <tr>
                        <td align="right"><strong>Company Name</strong> :</td>
                        <td align="left"><? echo $company_short_name_arr[$row_job[csf("company_name")]]; ?></td>
                        <td align="right"><strong>Style Ref No</strong> : </td>
                        <td align="left"><? echo $row_job[csf("style_ref_no")]; ?> </td>
                    </tr>
                    <tr>
                        <td colspan="4" height="15"></td>
                    </tr>
                </table>
                <div style="width:100%;" align="left">
                    <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                        <thead>
                            <tr>
                                <th width="35">SL</th>
                                <th width="100">PO Number</th>
                                <th width="90">Sample Type</th>
                                <th width="90">Color Name</th>
                                <th width="80">Target Date</th>
                                <th width="80">To Factory</th>
                                <th width="80">To Buyer</th>
                                <th width="70">Status</th>
                                <th width="80">Approval Date</th>
                                <th width="70">Delay Day</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <div style="width:100%; max-height:270px; overflow-y:scroll" align="left">
                    <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >
                        <?
                        $i = 0;
                        if ($db_type == 0)
                            $date_diff = "DATEDIFF(approval_status_date,target_approval_date)";
                        else
                            $date_diff = "trunc(approval_status_date-target_approval_date)";
                        $sql = "select a.sample_type_id,b.color_number_id,b.po_break_down_id, target_approval_date, send_to_factory_date, submitted_to_buyer, approval_status, approval_status_date, sample_comments, $date_diff as delay_day from wo_po_sample_approval_info a, wo_po_color_size_breakdown b where a.color_number_id=b.id and a.job_no_mst='$job_number' and a.current_status=1 and a.is_deleted=0 and a.status_active=1 and b.po_break_down_id in ($po_id) order by a.sample_type_id,b.color_number_id";
                        //echo $sql; //and a.approval_status<>0
                        $apprv_sql = sql_select($sql);
                        foreach ($apprv_sql as $row) {
                            $i++;
                            if ($i % 2 == 0)
                                $bgcolor = "#EFEFEF";
                            else
                                $bgcolor = "#FFFFFF";

                            if ($row[csf("delay_day")] > 0) {
                                $td_color = "red";
                                $delay_day = $row[csf("delay_day")];
                            } else {
                                $td_color = "";
                                $delay_day = "&nbsp;";
                            }
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                <td width="35"><? echo $i; ?></td>
                                <td width="100"><p><? echo $po_arr[$row[csf("po_break_down_id")]]; ?></p></td>
                                <td width="90"><p><? echo $sampleArr[$row[csf("sample_type_id")]]; ?></p></td>
                                <td width="90"><p><? echo $color_arr[$row[csf("color_number_id")]]; ?></p></td>
                                <td align="center" width="80"><?
                            if ($row[csf("target_approval_date")] == "0000-00-00" || $row[csf("target_approval_date")] == "")
                                echo "&nbsp;";
                            else
                                echo change_date_format($row[csf("target_approval_date")]);
                            ?>&nbsp;</td>
                                <td align="center" width="80"><?
                            if ($row[csf("send_to_factory_date")] == "0000-00-00" || $row[csf("send_to_factory_date")] == "")
                                echo "&nbsp;";
                            else
                                echo change_date_format($row[csf("send_to_factory_date")]);
                            ?>&nbsp;</td>
                                <td align="center" width="80"><?
                            if ($row[csf("submitted_to_buyer")] == "0000-00-00" || $row[csf("submitted_to_buyer")] == "")
                                echo "&nbsp;";
                            else
                                echo change_date_format($row[csf("submitted_to_buyer")]);
                            ?>&nbsp;</td>
                                <td align="center" width="70"><? echo $approval_status[$row[csf("approval_status")]]; ?>&nbsp;</td>
                                <td align="center" width="80"><?
                            if ($row[csf("approval_status_date")] == "0000-00-00" || $row[csf("approval_status_date")] == "")
                                echo "&nbsp;";
                            else
                                echo change_date_format($row[csf("approval_status_date")]);
                            ?>&nbsp;</td>
                                <td width="70" bgcolor="<? echo $td_color; ?>" align="right"><? echo $delay_day; ?>&nbsp;&nbsp;</td>
                                <td><p><? echo $row[csf("sample_comments")]; ?>&nbsp;</p></td>
                            </tr>
        <? } ?>
                    </table>
                </div>
            </fieldset>
        </div>
        <?
        exit();
    }

    if ($action == "lapdip_status") {
        $expData = explode('_', $job_number);
        $job_number = $expData[0];
        $po_id = $expData[1];
        $po_arr = return_library_array("select id, po_number from wo_po_break_down where id in ($po_id)", 'id', 'po_number');
        $color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
        echo load_html_head_contents("Lapdip Approve Details", "../../../../", 1, 1, $unicode, '', '');
        ?>

        <div style="width:100%" align="center">
            <fieldset style="width:900px">
                <table width="600">
    <?
    $job_sql = sql_select("select job_no,buyer_name,company_name,style_ref_no from wo_po_details_master where job_no='$job_number'");
    foreach ($job_sql as $row_job)
        ;  // Master Job  table queery ends here
    ?>
                    <tr class="form_caption">
                        <td align="center" colspan="4"><strong>Labdip Approval Details</strong></td>
                    </tr>
                    <tr>
                        <td align="right" width="130"> <strong>Job Number</strong> :</td>
                        <td width="200"><? echo $job_number; ?></td>
                        <td align="right"  width="130"><strong>Buyer Name</strong> :</td>
                        <td><? echo $buyer_short_name_arr[$row_job[csf("buyer_name")]]; ?></td>
                    </tr>
                    <tr>
                        <td align="right"><strong>Company Name</strong> :</td>
                        <td><? echo $company_short_name_arr[$row_job[csf("company_name")]]; ?></td>
                        <td align="right"><strong>Style Ref No</strong> : </td>
                        <td><? echo $row_job[csf("style_ref_no")]; ?> </td>
                    </tr>
                    <tr>
                        <td colspan="4" height="15"></td>
                    </tr>
                </table>
                <div style="width:100%">
                    <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                        <thead>
                            <tr>
                                <th width="35">SL</th>
                                <th width="100">PO Number</th>
                                <th width="90">Color Name</th>
                                <th width="80">Target Date</th>
                                <th width="80">To Factory</th>
                                <th width="80">To Buyer</th>
                                <th width="80">Status</th>
                                <th width="80">Approval Date</th>
                                <th width="80">Lapdip No</th>
                                <th width="70">Delay Day</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <div style="width:100%; max-height:270px; overflow-y:scroll">
                    <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                        <?
                        $i = 0;
                        if ($db_type == 0)
                            $date_diff = "DATEDIFF(a.approval_status_date,a.lapdip_target_approval_date)";
                        else
                            $date_diff = "trunc(a.approval_status_date-a.lapdip_target_approval_date)";
                        $sql = "select a.color_name_id, a.lapdip_target_approval_date, a.send_to_factory_date, a.submitted_to_buyer, a.approval_status, a.approval_status_date, a.lapdip_no, a.lapdip_comments, a.po_break_down_id, $date_diff as delay_day from wo_po_lapdip_approval_info a where a.job_no_mst='$job_number' and a.approval_status<>4 and a.current_status=1 and a.is_deleted=0 and a.status_active=1 and a.po_break_down_id in ($po_id)";
                        $lapdip_sql = sql_select($sql);
                        foreach ($lapdip_sql as $row) {
                            $i++;
                            if ($i % 2 == 0)
                                $bgcolor = "#EFEFEF";
                            else
                                $bgcolor = "#FFFFFF";

                            if ($row[csf("delay_day")] > 0) {
                                $td_color = "red";
                                $delay_day = $row[csf("delay_day")];
                            } else {
                                $td_color = "";
                                $delay_day = "&nbsp;";
                            }
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                <td width="35"><? echo $i; ?></td>
                                <td width="100"><p><? echo $po_arr[$row[csf("po_break_down_id")]]; ?></p></td>
                                <td width="90"><p><? echo $color_arr[$row[csf("color_name_id")]]; ?></p></td>
                                <td width="80" align="center"><?
                            if ($row[csf("lapdip_target_approval_date")] == "0000-00-00" || $row[csf("lapdip_target_approval_date")] == "")
                                echo "&nbsp;";
                            else
                                echo change_date_format($row[csf("lapdip_target_approval_date")]);
                            ?>&nbsp;</td>
                                <td width="80" align="center"><?
                            if ($row[csf("send_to_factory_date")] == "0000-00-00" || $row[csf("send_to_factory_date")] == "")
                                echo "&nbsp;";
                            else
                                echo change_date_format($row[csf("send_to_factory_date")]);
                            ?>&nbsp;</td>
                                <td width="80" align="center"><?
                            if ($row[csf("submitted_to_buyer")] == "0000-00-00" || $row[csf("submitted_to_buyer")] == "")
                                echo "&nbsp;";
                            else
                                echo change_date_format($row[csf("submitted_to_buyer")]);
                            ?>&nbsp;</td>
                                <td width="80" align="center"><p><? echo $approval_status[$row[csf("approval_status")]]; ?>&nbsp;</p></td>
                                <td width="80" align="center"><?
                            if ($row[csf("approval_status_date")] == "0000-00-00" || $row[csf("approval_status_date")] == "")
                                echo "&nbsp;";
                            else
                                echo change_date_format($row[csf("approval_status_date")]);
                            ?>&nbsp;</td>
                                <td width="80"><p><? echo $row[csf("lapdip_no")]; ?>&nbsp;</p></td>
                                <td width="70" bgcolor="<? echo $td_color; ?>" align="right"><? echo $delay_day; ?>&nbsp;&nbsp;</td>
                                <td><p><? echo $row[csf("lapdip_comments")]; ?>&nbsp;</p></td>
                            </tr>
        <? } ?>
                    </table>
                </div>
            </fieldset>
        </div>
        <?
        exit();
    }



    if ($action == "accessories_status") {
        echo load_html_head_contents("Lapdip Approve Details", "../../../../", 1, 1, $unicode, '', '');
        $expData = explode('_', $job_number);
        $job_number = $expData[0];
        $po_id = $expData[1];
        $po_arr = return_library_array("select id, po_number from wo_po_break_down where id in ($po_id)", 'id', 'po_number');
        ?>

        <div style="width:100%" align="center">
            <fieldset style="width:800px">
                <table width="600">
    <?
    $job_sql = sql_select("select job_no,buyer_name,company_name,style_ref_no from wo_po_details_master where job_no='$job_number'");
    foreach ($job_sql as $row_job)
        ;  // Master Job  table queery ends here
    ?>
                    <tr class="form_caption">
                        <td align="center" colspan="4"><strong>Accessories Approval Details</strong></td>
                    </tr>
                    <tr>
                        <td align="right" width="130"> <strong>Job Number</strong> :</td>
                        <td width="200"><? echo $job_number; ?></td>
                        <td align="right"  width="130"><strong>Buyer Name</strong> :</td>
                        <td><? echo $buyer_short_name_arr[$row_job[csf("buyer_name")]]; ?></td>
                    </tr>
                    <tr>
                        <td align="right"><strong>Company Name</strong> :</td>
                        <td><? echo $company_short_name_arr[$row_job[csf("company_name")]]; ?></td>
                        <td align="right"><strong>Style Ref No</strong> : </td>
                        <td><? echo $row_job[csf("style_ref_no")]; ?> </td>
                    </tr>
                    <tr>
                        <td colspan="4" height="15"></td>
                    </tr>
                </table>

                <div style="width:100%">
                    <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                        <thead>
                            <tr>
                                <th width="35">SL</th>
                                <th width="80">PO Number</th>
                                <th width="100">Accessories Type</th>
                                <th width="80">Target Date</th>
                                <th width="80">To Supplier</th>
                                <th width="80">To Buyer</th>
                                <th width="80">Status</th>
                                <th width="80">Approval Date</th>
                                <th width="80">Supplier</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <div style="width:100%; max-height:270px; overflow-y:scroll">
                    <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                        <?
                        $i = 0;
                        $sql = "select a.po_break_down_id,b.item_name, accessories_type_id, target_approval_date, sent_to_supplier, submitted_to_buyer, approval_status, approval_status_date, c.supplier_name, accessories_comments
					from wo_po_trims_approval_info a left join lib_supplier c on a.supplier_name=c.id, lib_item_group b
					where a.job_no_mst='$job_number' and a.accessories_type_id=b.id and a.approval_status<>0 and a.current_status=1 and a.status_active=1 and a.is_deleted=0 and a.po_break_down_id in ($po_id) and b.status_active=1 and b.is_deleted=0";
                        //echo $sql;
                        $acces_sql = sql_select($sql);
                        foreach ($acces_sql as $row) {
                            $i++;
                            if ($i % 2 == 0)
                                $bgcolor = "#EFEFEF";
                            else
                                $bgcolor = "#FFFFFF";
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                <td width="35"><? echo $i; ?></td>
                                <td width="80"><? echo $po_arr[$row[csf("po_break_down_id")]]; ?> </td>
                                <td width="100"><p><? echo $row[csf("item_name")]; ?></p></td>
                                <td width="80" align="center"><? echo change_date_format($row[csf("target_approval_date")]); ?></td>
                                <td width="80" align="center"><? echo change_date_format($row[csf("sent_to_supplier")]); ?></td>
                                <td width="80" align="center"><? echo change_date_format($row[csf("submitted_to_buyer")]); ?></td>
                                <td width="80" align="center"><p><? echo $approval_status[$row[csf("approval_status")]]; ?></p></td>
                                <td width="80" align="center"><? echo change_date_format($row[csf("approval_status_date")]); ?></td>
                                <td width="80"><p><? echo $row[csf("supplier_name")]; ?></p></td>
                                <td><p><? echo $row[csf("accessories_comments")]; ?></p></td>
                            </tr>
        <? } ?>
                    </table>
                </div>
            </fieldset>
        </div>
        <?
        exit();
    }



    if ($action == "embelishment_status") {
        echo load_html_head_contents("Lapdip Approve Details", "../../../../", 1, 1, $unicode, '', '');

        $expData = explode('_', $job_number);
        $job_number = $expData[0];
        $po_id = $expData[1];
        $po_arr = return_library_array("select id, po_number from wo_po_break_down where id in ($po_id)", 'id', 'po_number');
        ?>

        <div style="width:100%" align="center">
            <fieldset style="width:900px">
                <table width="600">
    <?
    $job_sql = sql_select("select job_no,buyer_name,company_name,style_ref_no from wo_po_details_master where job_no='$job_number'");
    foreach ($job_sql as $row_job)
        ;  // Master Job  table queery ends here
    ?>
                    <tr class="form_caption">
                        <td align="center" colspan="4"><strong>Embellishment Approval Details</strong></td>
                    </tr>
                    <tr>
                        <td align="right" width="130"> <strong>Job Number</strong> :</td>
                        <td width="200"><? echo $job_number; ?></td>
                        <td align="right"  width="130"><strong>Buyer Name</strong> :</td>
                        <td><? echo $buyer_short_name_arr[$row_job[csf("buyer_name")]]; ?></td>
                    </tr>
                    <tr>
                        <td align="right"><strong>Company Name</strong> :</td>
                        <td><? echo $company_short_name_arr[$row_job[csf("company_name")]]; ?></td>
                        <td align="right"><strong>Style Ref No</strong> : </td>
                        <td><? echo $row_job[csf("style_ref_no")]; ?> </td>
                    </tr>
                    <tr>
                        <td colspan="4" height="15"></td>
                    </tr>
                </table>

                <div style="width:100%">
                    <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                        <thead>
                            <tr>
                                <th width="35">SL</th>
                                <th width="80">PO Number</th>
                                <th width="100">Embellishment Name</th>
                                <th width="100">Embellishment Type</th>
                                <th width="80">Target Date</th>
                                <th width="80">To Supplier</th>
                                <th width="80">To Buyer</th>
                                <th width="80">Status</th>
                                <th width="80">Approval Date</th>
                                <th width="80">Supplier</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <div style="width:100%; max-height:270px; overflow-y:scroll">
                    <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                        <?
                        $i = 0;
                        $emb_sql = sql_select("select a.po_break_down_id, embellishment_id, embellishment_type_id, target_approval_date, sent_to_supplier, submitted_to_buyer, approval_status, approval_status_date, b.supplier_name, embellishment_comments
					from  wo_po_embell_approval a left join lib_supplier b on a.supplier_name=b.id
					where a.job_no_mst='$job_number' and a.approval_status<>0 and a.current_status=1 and a.status_active=1 and a.is_deleted=0 and a.po_break_down_id in ($po_id)");
                        foreach ($emb_sql as $row) {
                            $i++;
                            if ($i % 2 == 0)
                                $bgcolor = "#EFEFEF";
                            else
                                $bgcolor = "#FFFFFF";
                            $embl_type_name = "";
                            if ($row[csf("embellishment_id")] == 1)
                                $embl_type_name = $emblishment_print_type[$row[csf("embellishment_type_id")]];
                            else if ($row[csf("embellishment_id")] == 2)
                                $embl_type_name = $emblishment_embroy_type[$row[csf("embellishment_type_id")]];
                            else if ($row[csf("embellishment_id")] == 3)
                                $embl_type_name = $emblishment_wash_type[$row[csf("embellishment_type_id")]];
                            else if ($row[csf("embellishment_id")] == 4)
                                $embl_type_name = $emblishment_spwork_type[$row[csf("embellishment_type_id")]];
                            else
                                $embl_type_name = "";
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                <td width="35"><? echo $i; ?></td>
                                <td width="80"><? echo $po_arr[$row[csf("po_break_down_id")]]; ?> </td>
                                <td width="100"><p><? echo $emblishment_name_array[$row[csf("embellishment_id")]]; ?></p></td>
                                <td width="100"><p><? echo $embl_type_name; ?></p></td>
                                <td width="80" align="center"><? echo change_date_format($row[csf("target_approval_date")]); ?></td>
                                <td width="80" align="center"><? echo change_date_format($row[csf("sent_to_supplier")]); ?></td>
                                <td width="80" align="center"><? echo change_date_format($row[csf("submitted_to_buyer")]); ?></td>
                                <td width="80" align="center"><p><? echo $approval_status[$row[csf("approval_status")]]; ?></p></td>
                                <td width="80" align="center"><? echo change_date_format($row[csf("approval_status_date")]); ?></td>
                                <td width="80"><p><? echo $row[csf("supplier_name")]; ?></p></td>
                                <td><p><? echo $row[csf("embellishment_comments")]; ?></p></td>
                            </tr>
        <? } ?>
                    </table>
                </div>
            </fieldset>
        </div>
        <?
        exit();
    }


    if ($action == "fabric_booking_popup") {
        echo load_html_head_contents("Fabric Booking Details", "../../../../", 1, 1, $unicode, '', '');

        $expData = explode('_', $job_number);
        $job_number = $expData[0];
        $po_id = $expData[1];
        $tna_integrated = $expData[2];
        $actual_finish_date = $expData[3];
        $task_finish_date = $expData[4];
        ?>

        <div style="width:100%" align="center">
            <fieldset style="width:850px">
                <table width="800">
    <?
    $job_sql = sql_select("select job_no,buyer_name,company_name,style_ref_no from wo_po_details_master where job_no='$job_number'");
    foreach ($job_sql as $row_job)
        ;  // Master Job  table queery ends here
    ?>
                    <tr class="form_caption">
                        <td align="center" colspan="4"><strong>Fabric Booking Details</strong></td>
                    </tr>
                    <tr>
                        <td align="right" width="130"> <strong>Job Number</strong> :</td>
                        <td width="200"><? echo $job_number; ?></td>
                        <td align="right"  width="130"><strong>Buyer Name</strong> :</td>
                        <td><? echo $buyer_short_name_arr[$row_job[csf("buyer_name")]]; ?></td>
                    </tr>
                    <tr>
                        <td align="right"><strong>Company Name</strong> :</td>
                        <td><? echo $company_short_name_arr[$row_job[csf("company_name")]]; ?></td>
                        <td align="right"><strong>Style Ref No</strong> : </td>
                        <td><? echo $row_job[csf("style_ref_no")]; ?> </td>
                    </tr>
                    <tr>
                        <td colspan="4" height="15"></td>
                    </tr>
                </table>

                <div style="width:100%">
                    <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                        <thead>
                            <tr>
                                <th rowspan="2" width="30">SL</th>
                                <th rowspan="2" width="110">PO Number</th>
                                <th rowspan="2" width="90">PO Qnty</th>
                                <th rowspan="2" width="90">Gray Required</th>
                                <th rowspan="2" width="90">Booking Qnty</th>
                                <th rowspan="2" width="90">Yet to booking</th>
                                <th colspan="2" width="90">TNA</th>
                                <th rowspan="2">Booking No</th>
                            </tr>
                            <tr>
                                <th width="80">Plan Finish Date</th>
                                <th width="80">Actual Finish Date</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <div style="width:100%; max-height:270px; overflow-y:scroll">
                    <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                        <?
                        $i = 0;
                        /* $reqSQL = sql_select("select b.po_break_down_id, b.requirment/b.pcs as requirment, a.plan_cut_qnty
                          from wo_po_color_size_breakdown a, wo_pre_cos_fab_co_avg_con_dtls b
                          where a.po_break_down_id in ($po_id) and a.po_break_down_id=b.po_break_down_id and a.color_number_id=b.color_number_id and a.size_number_id=b.gmts_sizes and a.is_deleted=0 and a.status_active=1 group by b.id, b.requirment, b.pcs, b.po_break_down_id, a.plan_cut_qnty"); */
                        /* echo "select b.po_break_down_id, b.requirment/b.pcs as requirment, a.plan_cut_qnty
                          from wo_po_color_size_breakdown a, wo_pre_cos_fab_co_avg_con_dtls b, wo_pre_cost_fabric_cost_dtls c
                          where a.po_break_down_id in ($po_id) and a.po_break_down_id=b.po_break_down_id and a.color_number_id=b.color_number_id and a.size_number_id=b.gmts_sizes and b.pre_cost_fabric_cost_dtls_id=c.id and a.job_no_mst=c.job_no and a.item_number_id=c.item_number_id and a.is_deleted=0 and a.status_active=1 and b.pcs>0 group by b.id, b.requirment, b.pcs, b.po_break_down_id, a.plan_cut_qnty"; */

                        /* $reqSQL = sql_select("select b.po_break_down_id, b.requirment/b.pcs as requirment, a.plan_cut_qnty
                          from wo_po_color_size_breakdown a, wo_pre_cos_fab_co_avg_con_dtls b, wo_pre_cost_fabric_cost_dtls c
                          where a.po_break_down_id in ($po_id) and a.po_break_down_id=b.po_break_down_id and a.color_number_id=b.color_number_id and a.size_number_id=b.gmts_sizes and b.pre_cost_fabric_cost_dtls_id=c.id and a.job_no_mst=c.job_no and a.item_number_id=c.item_number_id and a.is_deleted=0 and a.status_active=1 and b.pcs>0 and c.fab_nature_id=2 group by b.id, b.requirment, b.pcs, b.po_break_down_id, a.plan_cut_qnty"); */
                        /* $reqSQL = sql_select("select b.po_break_down_id, b.requirment/b.pcs as requirment, sum(a.plan_cut_qnty) as plan_cut_qnty
                          from wo_po_color_size_breakdown a, wo_pre_cos_fab_co_avg_con_dtls b, wo_pre_cost_fabric_cost_dtls c
                          where a.po_break_down_id in ($po_id) and a.po_break_down_id=b.po_break_down_id and a.color_number_id=b.color_number_id and a.size_number_id=b.gmts_sizes and b.pre_cost_fabric_cost_dtls_id=c.id and a.job_no_mst=c.job_no and a.item_number_id=c.item_number_id and a.is_deleted=0 and a.status_active=1 and b.pcs>0 and c.fab_nature_id=2"); */
                        $reqSQL = sql_select("select b.po_break_down_id, b.requirment/b.pcs as requirment, a.plan_cut_qnty
							  from wo_po_color_size_breakdown a, wo_pre_cos_fab_co_avg_con_dtls b, wo_pre_cost_fabric_cost_dtls c
							  where a.po_break_down_id in ($po_id) and a.po_break_down_id=b.po_break_down_id and a.color_number_id=b.color_number_id and a.size_number_id=b.gmts_sizes and b.pre_cost_fabric_cost_dtls_id=c.id and a.job_no_mst=c.job_no and a.item_number_id=c.item_number_id and a.is_deleted=0 and a.status_active=1 and b.pcs>0 and c.fab_nature_id=2");
                        $requirment_arr = array();
                        foreach ($reqSQL as $key => $val) {
                            $requirment_arr[$val[csf('po_break_down_id')]] += $val[csf('requirment')] * $val[csf('plan_cut_qnty')]; // grey required
                        }

                        /* if($db_type==0)
                          {
                          $sql= "select a.id, a.po_number, a.po_quantity, a.plan_cut, a.shipment_date, sum(b.grey_fab_qnty) as grey_fab_qnty, group_concat(concat_ws('**',c.booking_no,c.booking_type,c.is_short,c.insert_date)) as booking_data
                          from wo_po_break_down a, wo_booking_dtls b, wo_booking_mst c
                          where a.job_no_mst='$job_number' and a.id=b.po_break_down_id and c.booking_no=b.booking_no and c.item_category in (2,13) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id in ($po_id) group by b.po_break_down_id";
                          }
                          else
                          {
                          $sql= "select a.id, a.po_number, a.po_quantity, a.plan_cut, a.shipment_date, sum(b.grey_fab_qnty) as grey_fab_qnty, LISTAGG(cast(c.booking_no || '**' || c.booking_type || '**' || c.is_short || '**' || c.insert_date as varchar2(4000)), ',') WITHIN GROUP (ORDER BY c.id) as booking_data
                          from wo_po_break_down a, wo_booking_dtls b, wo_booking_mst c
                          where a.job_no_mst='$job_number' and a.id=b.po_break_down_id and c.booking_no=b.booking_no and c.item_category in (2,13) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id in ($po_id) group by a.id, a.po_number, a.po_quantity, a.plan_cut, a.shipment_date";
                          }
                         */

                        $booking_data_arr = array();
                      $sqlBooking = "select c.booking_no,c.booking_type,c.is_short,c.insert_date, b.po_break_down_id, sum(b.grey_fab_qnty) as grey_fab_qnty
						from wo_booking_mst c, wo_booking_dtls b, wo_pre_cost_fabric_cost_dtls d where c.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=d.id and b.job_no=d.job_no and b.job_no='$job_number' and c.item_category in (2,3,13) and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and b.status_active=1 and b.is_deleted=0  and c.is_deleted=0 and b.po_break_down_id in ($po_id) group by b.po_break_down_id,c.booking_no,c.booking_type,c.is_short,c.insert_date";
                        $result = sql_select($sqlBooking);
                        foreach ($result as $rowB) {
                            $booking_data_arr[$rowB[csf('po_break_down_id')]] .= $rowB[csf('booking_no')] . "**" . $rowB[csf('booking_type')] . "**" . $rowB[csf('is_short')] . "**" . $rowB[csf('insert_date')] . "**" . $rowB[csf('grey_fab_qnty')] . ",";
                        }

                        $sql = "select id, po_number, po_quantity, plan_cut, shipment_date from wo_po_break_down where job_no_mst='$job_number' and status_active=1 and is_deleted=0 and id in ($po_id)";
                        //echo $sql;
                        $fabric_sql = sql_select($sql);
                        foreach ($fabric_sql as $row) {
                            $i++;
                            if ($i % 2 == 0)
                                $bgcolor = "#EFEFEF";
                            else
                                $bgcolor = "#FFFFFF";

                            $requirment = $requirment_arr[$row[csf('id')]];
                            $booking_nos = '';
                            //$booking_data=array_unique(explode(",",$row[csf('booking_data')]));
                            $booking_data = array_unique(explode(",", chop($booking_data_arr[$row[csf('id')]], ',')));
                            $grey_fab_qnty = 0;
                            foreach ($booking_data as $woRow) {
                                $woRow = explode("**", $woRow);
                                $booking_no = $woRow[0];
                                $booking_type = $woRow[1];
                                $is_short = $woRow[2];
                                $system_date = date('d-M-Y', strtotime($woRow[3]));
                                if ($booking_type == 4) {
                                    $booking_nos .= $booking_no . "(" . $system_date . ")" . ',';
                                } else {
                                    if ($is_short == 1) {
                                        $booking_nos .= $booking_no . "(" . $system_date . ")S " . ',';
                                    } else {
                                        $booking_nos .= $booking_no . "(" . $system_date . ")M " . ',';
                                    }
                                }

                                $grey_fab_qnty += $woRow[4];
                            }
                            $booking_nos = chop($booking_nos, ',');
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                <td width="30"><? echo $i; ?></td>
                                <td width="110"><p><? echo $row[csf("po_number")]; ?></p></td>
                                <td width="90" align="right"><? echo $row[csf("po_quantity")]; ?></td>
                                <td width="90" align="right"><? echo number_format($requirment, 2); ?></td>
                                <td width="90" align="right"><? echo number_format($grey_fab_qnty, 2); ?></td>
                                <? $yet_to_booking = $requirment - $grey_fab_qnty; ?>
                                <td width="90" align="right"><p><? echo number_format($yet_to_booking, 2); ?></p></td>
                                <?
                                //if($tna_integrated==1)
                                //{
                                echo '<td width="80" align="center">' . $task_finish_date . '&nbsp;</td>';
                                echo '<td width="80" align="center">' . $actual_finish_date . '&nbsp;</td>';
                                //}
                                ?>
                                <td><p><? echo $booking_nos; // implode(",",array_unique(explode(",",$row[csf('booking_no')])));   ?></p></td>
                            </tr>
        <?
    }
    ?>
                    </table>
                </div>
            </fieldset>
        </div>
        <?
        exit();
    }

    if ($action == "woven_fabric_booking_popup") {
        echo load_html_head_contents("Fabric Booking Details", "../../../../", 1, 1, $unicode, '', '');

        $expData = explode('_', $job_number);
        $job_number = $expData[0];
        $po_id = $expData[1];
        $tna_integrated = $expData[2];
        $actual_finish_date = $expData[3];
        ?>

        <div style="width:100%" align="center">
            <fieldset style="width:800px">
                <table width="600">
    <?
    $job_sql = sql_select("select job_no,buyer_name,company_name,style_ref_no from wo_po_details_master where job_no='$job_number'");
    foreach ($job_sql as $row_job)
        ;  // Master Job  table queery ends here
    ?>
                    <tr class="form_caption">
                        <td align="center" colspan="4"><strong>Fabric Booking Details</strong></td>
                    </tr>
                    <tr>
                        <td align="right" width="130"> <strong>Job Number</strong> :</td>
                        <td width="200"><? echo $job_number; ?></td>
                        <td align="right"  width="130"><strong>Buyer Name</strong> :</td>
                        <td><? echo $buyer_short_name_arr[$row_job[csf("buyer_name")]]; ?></td>
                    </tr>
                    <tr>
                        <td align="right"><strong>Company Name</strong> :</td>
                        <td><? echo $company_short_name_arr[$row_job[csf("company_name")]]; ?></td>
                        <td align="right"><strong>Style Ref No</strong> : </td>
                        <td><? echo $row_job[csf("style_ref_no")]; ?> </td>
                    </tr>
                    <tr>
                        <td colspan="4" height="15"></td>
                    </tr>
                </table>

                <div style="width:100%">
                    <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                        <thead>
                            <tr>
                                <th width="30">SL</th>
                                <th width="110">PO Number</th>
                                <th width="90">PO Qnty</th>
                                <th width="90">Gray Required</th>
                                <th width="90">Booking Qnty</th>
                                <th width="90">Yet to booking</th>
                                <th>Booking No</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <div style="width:100%; max-height:270px; overflow-y:scroll">
                    <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                        <?
                        $i = 0;
                        /* $reqSQL = sql_select("select b.po_break_down_id, b.requirment/b.pcs as requirment, a.plan_cut_qnty
                          from wo_po_color_size_breakdown a, wo_pre_cos_fab_co_avg_con_dtls b
                          where a.po_break_down_id in ($po_id) and a.po_break_down_id=b.po_break_down_id and a.color_number_id=b.color_number_id and a.size_number_id=b.gmts_sizes and a.is_deleted=0 and a.status_active=1 group by b.id, b.requirment, b.pcs, b.po_break_down_id, a.plan_cut_qnty"); */
                        /* echo "select b.po_break_down_id, b.requirment/b.pcs as requirment, a.plan_cut_qnty
                          from wo_po_color_size_breakdown a, wo_pre_cos_fab_co_avg_con_dtls b, wo_pre_cost_fabric_cost_dtls c
                          where a.po_break_down_id in ($po_id) and a.po_break_down_id=b.po_break_down_id and a.color_number_id=b.color_number_id and a.size_number_id=b.gmts_sizes and b.pre_cost_fabric_cost_dtls_id=c.id and a.job_no_mst=c.job_no and a.item_number_id=c.item_number_id and a.is_deleted=0 and a.status_active=1 and b.pcs>0 group by b.id, b.requirment, b.pcs, b.po_break_down_id, a.plan_cut_qnty"; */
                        $reqSQL = sql_select("select b.po_break_down_id, b.requirment/b.pcs as requirment, a.plan_cut_qnty
							  from wo_po_color_size_breakdown a, wo_pre_cos_fab_co_avg_con_dtls b, wo_pre_cost_fabric_cost_dtls c
							  where a.po_break_down_id in ($po_id) and a.po_break_down_id=b.po_break_down_id and a.color_number_id=b.color_number_id and a.size_number_id=b.gmts_sizes and b.pre_cost_fabric_cost_dtls_id=c.id and a.job_no_mst=c.job_no and a.item_number_id=c.item_number_id and a.is_deleted=0 and a.status_active=1 and b.pcs>0 and c.fab_nature_id=3"); // group by b.id, b.requirment, b.pcs, b.po_break_down_id, a.plan_cut_qnty
                        $requirment_arr = array();
                        foreach ($reqSQL as $key => $val) {
                            $requirment_arr[$val[csf('po_break_down_id')]] += $val[csf('requirment')] * $val[csf('plan_cut_qnty')]; // grey required
                        }

                        if ($db_type == 0) {
                            $sql = "select a.id, a.po_number, a.po_quantity, a.plan_cut, a.shipment_date, sum(b.grey_fab_qnty) as grey_fab_qnty,group_concat(distinct c.booking_no) as booking_no
						from wo_po_break_down a, wo_booking_dtls b, wo_booking_mst c
						where a.job_no_mst='$job_number' and a.id=b.po_break_down_id and c.booking_no=b.booking_no and c.item_category=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id in ($po_id) group by b.po_break_down_id";
                            /* $sql= "select a.id, a.po_number, a.po_quantity, a.plan_cut, a.shipment_date, sum(b.grey_fab_qnty) as grey_fab_qnty,group_concat(distinct b.booking_no) as booking_no
                              from wo_po_break_down a, wo_booking_dtls b
                              where a.job_no_mst='$job_number' and a.id=b.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id in ($po_id) group by b.po_break_down_id"; */
                        } else {
                            $sql = "select a.id, a.po_number, a.po_quantity, a.plan_cut, a.shipment_date, sum(b.grey_fab_qnty) as grey_fab_qnty, LISTAGG(c.booking_no, ',') WITHIN GROUP (ORDER BY c.id) as booking_no
						from wo_po_break_down a, wo_booking_dtls b, wo_booking_mst c
						where a.job_no_mst='$job_number' and a.id=b.po_break_down_id and c.booking_no=b.booking_no and c.item_category=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id in ($po_id) group by a.id, a.po_number, a.po_quantity, a.plan_cut, a.shipment_date";
                        }
                        //echo $sql;
                        $fabric_sql = sql_select($sql);
                        foreach ($fabric_sql as $row) {
                            $i++;
                            if ($i % 2 == 0)
                                $bgcolor = "#EFEFEF";
                            else
                                $bgcolor = "#FFFFFF";

                            $requirment = $requirment_arr[$row[csf('id')]];
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                <td width="30"><? echo $i; ?></td>
                                <td width="110"><p><? echo $row[csf("po_number")]; ?></p></td>
                                <td width="90" align="right"><? echo $row[csf("po_quantity")]; ?></td>
                                <td width="90" align="right"><? echo number_format($requirment, 2); ?></td>
                                <td width="90" align="right"><? echo number_format($row[csf("grey_fab_qnty")], 2); ?></td>
                            <? $yet_to_booking = $requirment - $row[csf("grey_fab_qnty")]; ?>
                                <td width="90" align="right"><p><? echo number_format($yet_to_booking, 2); ?></p></td>
                                <td><p><? echo implode(",", array_unique(explode(",", $row[csf('booking_no')]))); ?></p></td>
                            </tr>
        <?
    }
    ?>
                    </table>
                </div>
            </fieldset>
        </div>
        <?
        exit();
    }


    if ($action == "knitting_finish_popup_fuad") {
        echo load_html_head_contents("Knitting Finish Details", "../../../../", 1, 1, $unicode, '', '');

        $expData = explode('_', $job_number);
        $job_number = $expData[0];
        $po_id = $expData[1];
        $tna_integrated = $expData[2];
        $actual_finish_date = $expData[3];
        ?>

        <div style="width:100%" align="center">
            <fieldset style="width:820px">
                <table width="800" border="1" class="ro">
                    <?
                    $i = 1;
                    $job_sql = sql_select("select job_no,buyer_name,company_name,style_ref_no from wo_po_details_master where job_no='$job_number'");
                    foreach ($job_sql as $row_job)
                        ;  // Master Job  table queery ends here
                    ?>
                    <tr class="form_caption">
                        <td align="center" colspan="4"><strong>Knitting Finish Details</strong></td>
                    </tr>
                    <tr>
                        <td align="right" width="130"> <strong>Job Number</strong> :</td>
                        <td width="200"><? echo $job_number; ?></td>
                        <td align="right"  width="130"><strong>Buyer Name</strong> :</td>
                        <td><? echo $buyer_short_name_arr[$row_job[csf("buyer_name")]]; ?></td>
                    </tr>
                    <tr>
                        <td align="right"><strong>Company Name</strong> :</td>
                        <td><? echo $company_short_name_arr[$row_job[csf("company_name")]]; ?></td>
                        <td align="right"><strong>Style Ref No</strong> : </td>
                        <td><? echo $row_job[csf("style_ref_no")]; ?> </td>
                    </tr>
                    <tr>
                        <td colspan="4" height="15"></td>
                    </tr>
                </table>

                <div style="width:100%">
                    <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                        <thead>
                            <tr>
                                <th width="35">SL</th>
                                <th width="100">Grey Required</th>
                                <th width="100">Grey Production</th>
                                <th width="100">Grey Recv./ Purchase</th>
                                <th width="100">Net Transfer</th>
                                <th width="100">Grey Available</th>
                                <th>Balance</th>
    <?
    if ($tna_integrated == 1) {
        echo '<th width="150">TNA Actual Finish Date</th>';
    }
    ?>
                            </tr>
                        </thead>
                        <?
                        $bgcolor = "#EFEFEF";
                        $booking_qnty = return_field_value("sum(b.grey_fab_qnty) as grey_fab_qnty", "wo_booking_mst a, wo_booking_dtls b", "a.booking_no=b.booking_no and b.po_break_down_id in ($po_id) and a.item_category in (2,13) and b.status_active=1 and b.is_deleted=0", "grey_fab_qnty");
                        $dataKnitTrans = sql_select("select
								sum(CASE WHEN entry_form=2 THEN quantity ELSE 0 END) AS grey_receive,
								sum(CASE WHEN entry_form=13 and trans_type=5 THEN quantity ELSE 0 END) AS transfer_in_qnty_knit,
								sum(CASE WHEN entry_form=13 and trans_type=6 THEN quantity ELSE 0 END) AS transfer_out_qnty_knit
							from order_wise_pro_details where status_active=1 and is_deleted=0 and entry_form in(2,13) and po_breakdown_id in ($po_id)");

                        $grey_purchase_qnty = return_field_value("sum(c.quantity) as grey_purchase_qnty", "inv_receive_master a,pro_grey_prod_entry_dtls b, order_wise_pro_details c", "a.id=b.mst_id and b.id=c.dtls_id and a.receive_basis<>9 and a.entry_form=22 and c.entry_form=22 and c.po_breakdown_id in ($po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0", "grey_purchase_qnty"); //$grey_purchase_qnty+

                        $net_transfer_qnty = $dataKnitTrans[0][csf('transfer_in_qnty_knit')] - $dataKnitTrans[0][csf('transfer_out_qnty_knit')];
                        $grey_availlable = $dataKnitTrans[0][csf('grey_receive')] + $grey_purchase_qnty + $net_transfer_qnty;
                        $balance = $booking_qnty - $grey_availlable;
                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1', '<? echo $bgcolor; ?>')" id="tr_1">
                            <td width="35"><? echo $i; ?></td>
                            <td width="100" align="right"><? echo number_format($booking_qnty, 2); ?></td>
                            <td width="100" align="right"><? echo number_format($dataKnitTrans[0][csf('grey_receive')], 2); ?></td>
                            <td width="100" align="right"><? echo number_format($grey_purchase_qnty, 2); ?></td>
                            <td width="100" align="right"><? echo number_format($net_transfer_qnty, 2); ?></td>
                            <td width="100" align="right"><? echo number_format($grey_availlable, 2); ?></td>
                            <td align="right"><p><? echo number_format($balance, 2); ?></p></td>
    <?
    if ($tna_integrated == 1) {
        echo '<td width="150" align="center">' . $actual_finish_date . '&nbsp;</td>';
    }
    ?>
                        </tr>
                    </table>
                </div>
            </fieldset>
        </div>
        <?
        exit();
    }

    if ($action == "knitting_finish_popup") {
        echo load_html_head_contents("Knitting Finish Details", "../../../../", 1, 1, $unicode, '', '');

        $expData = explode('_', $job_number);
        $job_number = $expData[0];
        $po_id = $expData[1];
        $tna_integrated = $expData[2];
        $actual_finish_date = $expData[3];
        ?>

        <div style="width:100%" align="center">
            <fieldset style="width:820px">
                <table width="800" border="1" class="ro">
                    <?
                    $i = 1;
                    $job_sql = sql_select("select job_no,buyer_name,company_name,style_ref_no from wo_po_details_master where job_no='$job_number'");
                    foreach ($job_sql as $row_job)
                        ;  // Master Job  table queery ends here
                    ?>
                    <tr class="form_caption">
                        <td align="center" colspan="4"><strong>Knitting Finish Details</strong></td>
                    </tr>
                    <tr>
                        <td align="right" width="130"> <strong>Job Number</strong> :</td>
                        <td width="200"><? echo $job_number; ?></td>
                        <td align="right"  width="130"><strong>Buyer Name</strong> :</td>
                        <td><? echo $buyer_short_name_arr[$row_job[csf("buyer_name")]]; ?></td>
                    </tr>
                    <tr>
                        <td align="right"><strong>Company Name</strong> :</td>
                        <td><? echo $company_short_name_arr[$row_job[csf("company_name")]]; ?></td>
                        <td align="right"><strong>Style Ref No</strong> : </td>
                        <td><? echo $row_job[csf("style_ref_no")]; ?> </td>
                    </tr>
                    <tr>
                        <td colspan="4" height="15"></td>
                    </tr>
                </table>

                <div style="width:100%">
                    <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="2">
                        <thead>
                            <tr>
                                <th width="35">SL</th>
                                <th width="95">Grey Required</th>
                                <th width="95">Grey Production</th>
                                <th width="95">Grey Recv.</th>
                                <th width="95">Purchase</th>
                                <th width="95">Net Transfer</th>
                                <th width="95">Grey Available</th>
                                <th>Balance</th>
    <?
    if ($tna_integrated == 1) {
        echo '<th width="90">TNA Actual Finish Date</th>';
    }
    ?>
                            </tr>
                        </thead>
                        <?
                        $bgcolor = "#EFEFEF";
                        $booking_qnty = return_field_value("sum(b.grey_fab_qnty) as grey_fab_qnty", "wo_booking_mst a, wo_booking_dtls b", "a.booking_no=b.booking_no and b.po_break_down_id in ($po_id) and a.item_category in (2,13) and b.status_active=1 and b.is_deleted=0", "grey_fab_qnty");
                        /* echo "select
                          sum(CASE WHEN entry_form=2 THEN quantity ELSE 0 END) AS grey_pord,
                          sum(CASE WHEN entry_form=13 and trans_type=5 THEN quantity ELSE 0 END) AS transfer_in_qnty_knit,
                          sum(CASE WHEN entry_form=13 and trans_type=6 THEN quantity ELSE 0 END) AS transfer_out_qnty_knit
                          from order_wise_pro_details
                          where status_active=1 and is_deleted=0 and entry_form in(2,13) and po_breakdown_id in ($po_id)"; */
                        $dataKnitTrans = sql_select("select
								sum(CASE WHEN entry_form=2 THEN quantity ELSE 0 END) AS grey_pord,
								sum(CASE WHEN entry_form=13 and trans_type=5 THEN quantity ELSE 0 END) AS transfer_in_qnty_knit,
								sum(CASE WHEN entry_form=13 and trans_type=6 THEN quantity ELSE 0 END) AS transfer_out_qnty_knit
							from order_wise_pro_details
							where status_active=1 and is_deleted=0 and entry_form in(2,13) and po_breakdown_id in ($po_id)");

                        $grey_purchase_qnty = return_field_value("sum(c.quantity) as grey_purchase_qnty", "inv_receive_master a,pro_grey_prod_entry_dtls b, order_wise_pro_details c", "a.id=b.mst_id and b.id=c.dtls_id and a.receive_basis<>9 and a.entry_form=22 and c.entry_form=22 and c.po_breakdown_id in ($po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0", "grey_purchase_qnty"); //$grey_purchase_qnty+
                        /* $grey_rcv_qnty=return_field_value("sum(case a.entry_form=22 when c.quantity) as grey_purchase_qnty","inv_receive_master a, inv_transaction b, order_wise_pro_details c","a.id=b.mst_id and b.id=c.trans_id and a.receive_basis=9 and a.entry_form=22 and c.entry_form=22 and c.po_breakdown_id in ($po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0","grey_purchase_qnty"); */
                        $grey_receive_sql = sql_select("select sum(case when a.entry_form=2 and a.receive_basis=1 then c.quantity else 0 end) as grey_auto_rcv, sum(case when a.entry_form in(22,58) and a.receive_basis in(9,10) then c.quantity else 0 end) as grey_prod_rcv
						from inv_receive_master a, inv_transaction b, order_wise_pro_details c
						where a.id=b.mst_id and b.id=c.trans_id and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and c.po_breakdown_id in ($po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0");

                        $grey_rcv = $grey_receive_sql[0][csf("grey_auto_rcv")] + $grey_receive_sql[0][csf("grey_prod_rcv")];
                        $net_transfer_qnty = $dataKnitTrans[0][csf('transfer_in_qnty_knit')] - $dataKnitTrans[0][csf('transfer_out_qnty_knit')];
                        $grey_availlable = $grey_rcv + $grey_purchase_qnty + $net_transfer_qnty;
                        $balance = $booking_qnty - $grey_availlable;
                        ?>
                        <tr>
                            <td ><? echo $i; ?></td>
                            <td align="right"><? echo number_format($booking_qnty, 2); ?></td>
                            <td align="right"><? echo number_format($dataKnitTrans[0][csf('grey_pord')], 2); ?></td>
                            <td align="right"><? echo number_format($grey_rcv, 2); ?></td>
                            <td align="right"><? echo number_format($grey_purchase_qnty, 2); ?></td>
                            <td align="right"><? echo number_format($net_transfer_qnty, 2); ?></td>
                            <td align="right"><? echo number_format($grey_availlable, 2); ?></td>
                            <td align="right"><p><? echo number_format($balance, 2); ?></p></td>
    <?
    if ($tna_integrated == 1) {
        echo '<td align="center">' . $actual_finish_date . '&nbsp;</td>';
    }
    ?>
                        </tr>
                    </table>
                </div>
            </fieldset>
        </div>
        <?
        exit();
    }


    if ($action == "lcsc_rcv_popup") {
        echo load_html_head_contents("LC/SC Receive Details", "../../../../", 1, 1, $unicode, '', '');

        $expData = explode('_', $job_number);
        $job_number = $expData[0];
        $po_id = $expData[1];
        ?>

        <div style="width:100%" align="center">
            <fieldset style="width:700px">
            <table width="600">
    <?
	   $sql_comm = sql_select("select a.internal_file_no,a.bank_file_no,a.sc_year as sc_year,a.last_shipment_date
		from com_sales_contract a, com_sales_contract_order_info b
		where a.id=b.com_sales_contract_id and b.wo_po_break_down_id in ($po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.internal_file_no,a.bank_file_no,a.sc_year,a.last_shipment_date
		UNION ALL
		select a.internal_file_no,a.bank_file_no,a.lc_year as sc_year,a.last_shipment_date
		from com_export_lc a, com_export_lc_order_info b
		where a.id=b.com_export_lc_id and b.wo_po_break_down_id in ($po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.bank_file_no,a.internal_file_no,a.lc_year,a.last_shipment_date");

		 foreach ($sql_comm as $row_job)
		 {
			$internal_file_no.=$row_job[csf("internal_file_no")].',';
			$sc_year.=$row_job[csf("sc_year")].',';
			$bank_file_no.=$row_job[csf("bank_file_no")].',';
			$last_shipment_date.=change_date_format($row_job[csf("last_shipment_date")]).',';
		 }
		 $internal_file_no=rtrim($internal_file_no,',');
		 $internal_file_nos=implode(",",array_unique(explode(",",$internal_file_no)));
		 $sc_year=rtrim($sc_year,',');
		 $sc_years=implode(",",array_unique(explode(",",$sc_year)));
		 $bank_file_no=rtrim($bank_file_no,',');
		 $bank_file_nos=implode(",",array_unique(explode(",",$bank_file_no)));
		 $last_shipment_date=rtrim($last_shipment_date,',');
		 $last_shipment_dates=implode(",",array_unique(explode(",",$last_shipment_date)));

	   if($db_type==2)
		{ 	$grp_cond="listagg((cast(b.file_no as varchar2(4000))),',') within group (order by file_no) as file_no,listagg((cast(b.grouping as varchar2(4000))),',') within group (order by grouping) as grouping ";
		$grp_cond.=",to_char(a.insert_date,'YYYY') as year";
		}
		else
		{
			$grp_cond="group_concat(b.file_no) as file_no,group_concat(b.grouping) as grouping";
			$grp_cond.=",YEAR(a.insert_date) as year";
		}
		$job_sql = sql_select("select  $grp_cond ,a.job_no,a.buyer_name,a.company_name,a.style_ref_no,max(b.pub_shipment_date) as pub_shipment_date from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and a.job_no='$job_number' group by a.job_no,a.buyer_name,
	a.company_name,a.style_ref_no,a.insert_date");

    foreach ($job_sql as $row_job)
        // Master Job  table queery ends here
    ?>
                    <tr class="form_caption">
                        <td align="center" colspan="4"><strong>LC/SC Receive Details</strong></td>
                    </tr>
                    <tr>
                        <td align="right" width="130"> <strong>Job Number</strong> :</td>
                        <td width="200"><? echo $job_number; ?></td>
                        <td align="right"  width="130"><strong>Buyer Name</strong> :</td>
                        <td><? echo $buyer_short_name_arr[$row_job[csf("buyer_name")]]; ?></td>
                    </tr>
                    <tr>
                        <td align="right"><strong>Company Name</strong> :</td>
                        <td><? echo $company_short_name_arr[$row_job[csf("company_name")]]; ?></td>
                        <td align="right"><strong>Style Ref No</strong> : </td>
                        <td><? echo $row_job[csf("style_ref_no")]; ?> </td>
                    </tr>
					 <tr>
                        <td align="right"><strong>Year</strong> :</td>
                        <td><? echo  $sc_years; ?></td>
                        <td align="right"><strong>Int. Ref. No</strong> : </td>
                        <td><? echo $internal_file_nos; ?> </td>
                    </tr>
					 <tr>
                        <td align="right"><strong>File No</strong> : </td>
                        <td><? echo $bank_file_nos; ?> </td>
                        <td align="right"><strong>Last Shipdate</strong> : </td>
                        <td><? echo  $last_shipment_dates; ?> </td>
                    </tr>

                    <tr>
                        <td colspan="4" height="15"></td>
                    </tr>
                </table>

                <div style="width:100%">
                    <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                        <thead>
                            <tr>
                                <th width="35">SL</th>
                                <th width="150">LC/SC Number</th>
                                <th width="120">LC/SC Value</th>
                                <th width="120">Attached Value</th>
                                <th width="100">Expiry Date</th>
                                <th>Is LC/SC</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <div style="width:100%; max-height:270px; overflow-y:scroll">
                    <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                        <?
                        $i = 0;
                        /* if($db_type==0)
                          {
                          $sql= "select a.contract_no, a.contract_value, a.expiry_date, sum(b.attached_value) as attached_value,'1' as type
                          from com_sales_contract a, com_sales_contract_order_info b
                          where a.id=b.com_sales_contract_id and b.wo_po_break_down_id in ($po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.contract_no,b.wo_po_break_down_id
                          UNION ALL
                          select a.export_lc_no, a.lc_value, a.expiry_date, sum(b.attached_value) as attached_value,'0' as type
                          from com_export_lc a, com_export_lc_order_info b
                          where a.id=b.com_export_lc_id and b.wo_po_break_down_id in ($po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.export_lc_no,b.wo_po_break_down_id";
                          }
                          else
                          {
                          $sql= "select a.contract_no, a.contract_value, a.expiry_date, sum(b.attached_value) as attached_value,'1' as type
                          from com_sales_contract a, com_sales_contract_order_info b
                          where a.id=b.com_sales_contract_id and b.wo_po_break_down_id in ($po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.contract_no, a.contract_value, a.expiry_date, b.wo_po_break_down_id
                          UNION ALL
                          select a.export_lc_no, a.lc_value, a.expiry_date, sum(b.attached_value) as attached_value,'0' as type
                          from com_export_lc a, com_export_lc_order_info b
                          where a.id=b.com_export_lc_id and b.wo_po_break_down_id in ($po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.export_lc_no, a.lc_value, a.expiry_date, b.wo_po_break_down_id";
                          } */

                        $sql = "select a.contract_no, a.contract_value, a.expiry_date, sum(b.attached_value) as attached_value,'1' as type
						from com_sales_contract a, com_sales_contract_order_info b
						where a.id=b.com_sales_contract_id and b.wo_po_break_down_id in ($po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.contract_no, a.contract_value, a.expiry_date, b.wo_po_break_down_id
						UNION ALL
						select a.export_lc_no, a.lc_value, a.expiry_date, sum(b.attached_value) as attached_value,'0' as type
						from com_export_lc a, com_export_lc_order_info b
						where a.id=b.com_export_lc_id and b.wo_po_break_down_id in ($po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.export_lc_no, a.lc_value, a.expiry_date, b.wo_po_break_down_id";
                        //echo $sql;
                        $fabric_sql = sql_select($sql);
                        $total_booking_qnty = $totalreceive_qnty = 0;
                        foreach ($fabric_sql as $row) {
                            $i++;
                            if ($i % 2 == 0)
                                $bgcolor = "#EFEFEF";
                            else
                                $bgcolor = "#FFFFFF";
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                <td width="35"><? echo $i; ?></td>
                                <td width="150"><p><? echo $row[csf("contract_no")]; ?></p></td>
                                <td width="120" align="right"><? echo number_format($row[csf("contract_value")], 2); ?></td>
                                <td width="120" align="right"><? echo number_format($row[csf("attached_value")], 2); ?></td>
                                <td width="100" align="center"><? echo change_date_format($row[csf("expiry_date")]); ?></td>
        <?
        if ($row[csf("type")] == 1)
            $isLCSC = "Sales Contact";
        else
            $isLCSC = "Export LC";
        ?>
                                <td><p><? echo $isLCSC; ?></p></td>
                            </tr>
        <? } ?>
                    </table>
                </div>
            </fieldset>
        </div>
        <?
        exit();
    }

    if ($action == "finish_fabric_popup_fuad") {
        echo load_html_head_contents("Finish Fabric Details", "../../../../", 1, 1, $unicode, '', '');

        $expData = explode('_', $job_number);
        $job_number = $expData[0];
        $po_id = $expData[1];
        $tna_integrated = $expData[2];
        $actual_finish_date = $expData[3];
        ?>

        <div style="width:100%" align="center">
            <fieldset style="width:830px">
                <table width="600">
                    <?
                    $i = 1;
                    if ($tna_integrated == 1)
                        $table_width = 830;
                    else
                        $table_width = 680;
                    $job_sql = sql_select("select job_no,buyer_name,company_name,style_ref_no from wo_po_details_master where job_no='$job_number'");
                    foreach ($job_sql as $row_job)
                        ;  // Master Job  table queery ends here
                    ?>
                    <tr class="form_caption">
                        <td align="center" colspan="4"><strong>Finish Fabric Details</strong></td>
                    </tr>
                    <tr>
                        <td align="right" width="130"> <strong>Job Number</strong> :</td>
                        <td width="200"><? echo $job_number; ?></td>
                        <td align="right"  width="130"><strong>Buyer Name</strong> :</td>
                        <td><? echo $buyer_short_name_arr[$row_job[csf("buyer_name")]]; ?></td>
                    </tr>
                    <tr>
                        <td align="right"><strong>Company Name</strong> :</td>
                        <td><? echo $company_short_name_arr[$row_job[csf("company_name")]]; ?></td>
                        <td align="right"><strong>Style Ref No</strong> : </td>
                        <td><? echo $row_job[csf("style_ref_no")]; ?> </td>
                    </tr>
                    <tr>
                        <td colspan="4" height="15"></td>
                    </tr>
                </table>
                <table cellpadding="0" width="<? echo $table_width; ?>" class="rpt_table" rules="all" border="1">
                    <thead>
                        <tr>
                            <th width="40">SL</th>
                            <th width="100">Color</th>
                            <th width="100">Finish Required</th>
                            <th width="100">Finish Production</th>
                            <th width="100">Finish Recv./ Purchase</th>
                            <th width="90">Net Transfer</th>
                            <th width="100">Finish Available</th>
                            <?
                            if ($tna_integrated == 1) {
                                echo '<th width="90">Balance</th><th>TNA Actual Finish Date</th>';
                            } else {
                                echo '<th>Balance</th>';
                            }
                            ?>
                        </tr>
                    </thead>
                </table>
                <div style="width:<? echo $table_width; ?>px; overflow-y:scroll; max-height:250px">
                    <table cellpadding="0" width="<? echo $table_width - 18; ?>" class="rpt_table" rules="all" border="1">
                        <?
                        $bgcolor = "#EFEFEF";
                        $color_arr = array();
                        $booking_qnty_arr = array();
                        $datafinArr = array();
                        $finPurchaseArr = array();
                        $colorArr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
                        //$booking_qnty= return_field_value("sum(b.fin_fab_qnty) as fin_fab_qnty","wo_booking_mst a, wo_booking_dtls b","a.booking_no=b.booking_no and b.po_break_down_id in ($po_id) and a.item_category in (2,13) and b.status_active=1 and b.is_deleted=0","fin_fab_qnty");
                        $bookingData = sql_select("select b.fabric_color_id, sum(b.fin_fab_qnty) as fin_fab_qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and b.po_break_down_id in ($po_id) and a.item_category in (2,13) and b.status_active=1 and b.is_deleted=0 group by b.fabric_color_id");
                        foreach ($bookingData as $row) {
                            $booking_qnty_arr[$row[csf('fabric_color_id')]] = $row[csf('fin_fab_qnty')];
                            $color_arr[$row[csf('fabric_color_id')]] = $row[csf('fabric_color_id')];
                        }

                        $datafinTrans = sql_select("select color_id,
							sum(CASE WHEN entry_form=7 THEN quantity ELSE 0 END) AS fin_receive,
							sum(CASE WHEN entry_form ='66' THEN quantity ELSE 0 END) AS finish_receive_roll_wise,
							sum(CASE WHEN entry_form =15 and trans_type=5 THEN quantity ELSE 0 END) AS transfer_in_qnty_fin,
							sum(CASE WHEN entry_form =15 and trans_type=6 THEN quantity ELSE 0 END) AS transfer_out_qnty_fin
						from order_wise_pro_details where status_active=1 and is_deleted=0 and entry_form in(7,15,66) and po_breakdown_id in ($po_id) group by color_id");
                        foreach ($datafinTrans as $row) {
                            $datafinArr[$row[csf('color_id')]]['rcv'] = $row[csf('fin_receive')] + $row[csf('finish_receive_roll_wise')];
                            $datafinArr[$row[csf('color_id')]]['trans_in'] = $row[csf('transfer_in_qnty_fin')];
                            $datafinArr[$row[csf('color_id')]]['trans_out'] = $row[csf('transfer_out_qnty_fin')];
                            $color_arr[$row[csf('color_id')]] = $row[csf('color_id')];
                        }

                        $finPurchaseArr = return_library_array("select c.color_id, sum(c.quantity) as fin_purchase_qnty from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.receive_basis<>9 and a.entry_form=37 and c.entry_form=37 and c.po_breakdown_id in ($po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by c.color_id", 'color_id', 'fin_purchase_qnty');

                        //var_dump($finPurchaseArr);
                        $tot_req_qnty = 0;
                        $tot_avl_qnty = 0;
                        $tot_bl_qnty = 0;
                        foreach ($color_arr as $color_id) {
                            if ($i % 2 == 0)
                                $bgcolor = "#EFEFEF";
                            else
                                $bgcolor = "#FFFFFF";

                            $booking_qnty = $booking_qnty_arr[$color_id];
                            $fin_purchase_qnty = $finPurchaseArr[$color_id];
                            $net_transfer_qnty = $datafinArr[$color_id]['trans_in'] - $datafinArr[$color_id]['trans_out'];
                            $fin_availlable = $fin_purchase_qnty + $datafinArr[$color_id]['rcv'] + $net_transfer_qnty;
                            $balance = $booking_qnty - $fin_availlable;
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                <td width="40"><? echo $i; ?></td>
                                <td width="100"><p><? echo $colorArr[$color_id]; ?>&nbsp;</p></td>
                                <td width="100" align="right"><? echo number_format($booking_qnty, 2); ?></td>
                                <td width="100" align="right"><? echo number_format($datafinArr[$color_id]['rcv'], 2); ?></td>
                                <td width="100" align="right"><? echo number_format($fin_purchase_qnty, 2); ?></td>
                                <td width="90" align="right"><? echo number_format($net_transfer_qnty, 2); ?></td>
                                <td width="100" align="right"><? echo number_format($fin_availlable, 2); ?></td>
                                <?
                                if ($tna_integrated == 1) {
                                    echo '<td width="90" align="right">' . number_format($balance, 2) . '</td>';
                                    echo '<td align="center">' . $actual_finish_date . '&nbsp;</td>';
                                } else {
                                    echo '<td align="right">' . number_format($balance, 2) . '&nbsp;</td>';
                                }
                                ?>
                            </tr>
                            <?
                            $tot_req_qnty += $booking_qnty;
                            $tot_prod_qnty += $datafinArr[$color_id]['rcv'];
                            $tot_pur_qnty += $fin_purchase_qnty;
                            $tot_net_qnty += $net_transfer_qnty;
                            $tot_avl_qnty += $fin_availlable;
                            $tot_bl_qnty += $balance;

                            $i++;
                        }
                        ?>
                        <tfoot>
                        <th colspan="2">&nbsp;</th>
                        <th align="right"><? echo number_format($tot_req_qnty, 2); ?></th>
                        <th align="right"><? echo number_format($tot_prod_qnty, 2); ?></th>
                        <th align="right"><? echo number_format($tot_pur_qnty, 2); ?></th>
                        <th align="right"><? echo number_format($tot_net_qnty, 2); ?></th>
                        <th align="right"><? echo number_format($tot_avl_qnty, 2); ?></th>
                        <th align="right"><? echo number_format($tot_bl_qnty, 2); ?></th>
    <?
    if ($tna_integrated == 1) {
        echo '<th>&nbsp;</th>';
    }
    ?>
                        </tfoot>
                    </table>
                </div>
            </fieldset>
        </div>
        <?
        exit();
    }

    if ($action == "finish_fabric_popup") {
        echo load_html_head_contents("Finish Fabric Details", "../../../../", 1, 1, $unicode, '', '');

        $expData = explode('_', $job_number);
        $job_number = $expData[0];
        $po_id = $expData[1];
        $tna_integrated = $expData[2];
        $actual_finish_date = $expData[3];
        ?>

        <div style="width:100%" align="center">
            <fieldset style="width:830px">
                <table width="600">
                    <?
                    $i = 1;
                    if ($tna_integrated == 1)
                        $table_width = 830;
                    else
                        $table_width = 755;
                    $job_sql = sql_select("select job_no,buyer_name,company_name,style_ref_no from wo_po_details_master where job_no='$job_number'");
                    foreach ($job_sql as $row_job)
                        ;  // Master Job  table queery ends here
                    ?>
                    <tr class="form_caption">
                        <td align="center" colspan="4"><strong>Finish Fabric Details</strong></td>
                    </tr>
                    <tr>
                        <td align="right" width="130"> <strong>Job Number</strong> :</td>
                        <td width="200"><? echo $job_number; ?></td>
                        <td align="right"  width="130"><strong>Buyer Name</strong> :</td>
                        <td><? echo $buyer_short_name_arr[$row_job[csf("buyer_name")]]; ?></td>
                    </tr>
                    <tr>
                        <td align="right"><strong>Company Name</strong> :</td>
                        <td><? echo $company_short_name_arr[$row_job[csf("company_name")]]; ?></td>
                        <td align="right"><strong>Style Ref No</strong> : </td>
                        <td><? echo $row_job[csf("style_ref_no")]; ?> </td>
                    </tr>
                    <tr>
                        <td colspan="4" height="15"></td>
                    </tr>
                </table>
                <table cellpadding="0" width="<? echo $table_width; ?>" class="rpt_table" rules="all" border="1">
                    <thead>
                        <tr>
                            <!--<th width="40">SL</th>
                            <th width="100">Color</th>
                            <th width="100">Finish Required</th>
                            <th width="100">Finish Production</th>
                            <th width="100">Finish Recv.</th>
                            <th width="100">Purchase Recv.</th>
                            <th width="90">Net Transfer</th>
                            <th width="100">Finish Available</th>-->
                            <th width="30">SL</th>
                            <th width="100">Color</th>
                            <th width="80">Finish Required</th>
                            <th width="80">Finish Production</th>
                            <th width="75">Finish Deli. To Store</th>
                            <th width="75">Finish Recv.</th>
                            <th width="75">Purchase Recv.</th>
                            <th width="70">Net Transfer</th>
                            <th width="75">Finish Available</th>
                            <?
                            if ($tna_integrated == 1) {
                                echo '<th width="75">Balance</th><th>TNA Actual Finish Date</th>';
                            } else {
                                echo '<th>Balance</th>';
                            }
                            ?>
                        </tr>
                    </thead>
                </table>
                <div style="width:<? echo $table_width; ?>px; overflow-y:scroll; max-height:250px">
                    <table cellpadding="0" width="<? echo $table_width - 18; ?>" class="rpt_table" rules="all" border="1">
                        <?
                        $bgcolor = "#EFEFEF";
                        $color_arr = array();
                        $booking_qnty_arr = array();
                        $datafinArr = array();
                        $finPurchaseArr = array();
                        $colorArr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
                        //$booking_qnty= return_field_value("sum(b.fin_fab_qnty) as fin_fab_qnty","wo_booking_mst a, wo_booking_dtls b","a.booking_no=b.booking_no and b.po_break_down_id in ($po_id) and a.item_category in (2,13) and b.status_active=1 and b.is_deleted=0","fin_fab_qnty");
                        $bookingData = sql_select("select b.fabric_color_id, sum(b.fin_fab_qnty) as fin_fab_qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and b.po_break_down_id in ($po_id) and a.item_category in (2,13) and b.status_active=1 and b.is_deleted=0 group by b.fabric_color_id");
                        foreach ($bookingData as $row) {
                            $booking_qnty_arr[$row[csf('fabric_color_id')]] = $row[csf('fin_fab_qnty')];
                            $color_arr[$row[csf('fabric_color_id')]] = $row[csf('fabric_color_id')];
                        }

                        $datafinTrans = sql_select("select color_id,
							sum(CASE WHEN entry_form=7 THEN quantity ELSE 0 END) AS fin_receive,
							sum(CASE WHEN entry_form ='66' THEN quantity ELSE 0 END) AS finish_receive_roll_wise,
							sum(CASE WHEN entry_form ='68' THEN quantity ELSE 0 END) AS finish_receive_roll_store,
							sum(CASE WHEN entry_form =15 and trans_type=5 THEN quantity ELSE 0 END) AS transfer_in_qnty_fin,
							sum(CASE WHEN entry_form =15 and trans_type=6 THEN quantity ELSE 0 END) AS transfer_out_qnty_fin
						from order_wise_pro_details where status_active=1 and is_deleted=0 and entry_form in(7,15,66,68) and po_breakdown_id in ($po_id) group by color_id");
                        foreach ($datafinTrans as $row) {
                            $datafinArr[$row[csf('color_id')]]['rcv'] = $row[csf('fin_receive')] + $row[csf('finish_receive_roll_wise')];
                            $datafinArr[$row[csf('color_id')]]['trans_in'] = $row[csf('transfer_in_qnty_fin')];
                            $datafinArr[$row[csf('color_id')]]['trans_out'] = $row[csf('transfer_out_qnty_fin')];
							 $datafinArr[$row[csf('color_id')]]['finish_receive_roll_store'] = $row[csf('finish_receive_roll_store')];
                            $color_arr[$row[csf('color_id')]] = $row[csf('color_id')];
                        }

                        //$finPurchaseArr=return_library_array( "select c.color_id, sum(c.quantity) as fin_purchase_qnty from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.receive_basis<>9 and a.entry_form=37 and c.entry_form=37 and c.po_breakdown_id in ($po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by c.color_id",'color_id','fin_purchase_qnty');

                        /* $grey_receive_sql=sql_select("select sum(case when a.entry_form=2 and a.receive_basis=1 then c.quantity else 0 end) as grey_auto_rcv, sum(case when a.entry_form=22 and a.receive_basis=9 then c.quantity else 0 end) as grey_prod_rcv
                          from inv_receive_master a, inv_transaction b, order_wise_pro_details c
                          where a.id=b.mst_id and b.id=c.trans_id and a.entry_form in(2,22) and c.entry_form in(2,22) and c.po_breakdown_id in ($po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0"); */

                       $finPurchaseArr =sql_select("select c.color_id,
					sum(case when a.entry_form=7 then c.quantity else 0 end) as grey_auto_rcv,
					sum(case when a.entry_form=37 and a.receive_basis=9 then c.quantity else 0 end) as grey_prod_rcv,
					sum(case when a.entry_form=37 and a.receive_basis<>9 then  c.quantity else 0 end) as fin_purchase_qnty
					from inv_receive_master a, inv_transaction b, order_wise_pro_details c
					where a.id=b.mst_id and b.id=c.trans_id and c.po_breakdown_id in ($po_id) and a.entry_form in(7,37) and c.entry_form in(7,37) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by c.color_id");
                        $rcv_data_arr = array();
                        foreach ($finPurchaseArr as $row) {
                            $rcv_data_arr[$row[csf("color_id")]]["color_id"] = $row[csf("color_id")];
                            $rcv_data_arr[$row[csf("color_id")]]["grey_auto_rcv"] = $row[csf("grey_auto_rcv")];
                            $rcv_data_arr[$row[csf("color_id")]]["grey_prod_rcv"] = $row[csf("grey_prod_rcv")];
                            $rcv_data_arr[$row[csf("color_id")]]["fin_purchase_qnty"] = $row[csf("fin_purchase_qnty")];
                        }
                        $finDeliveryArr = return_library_array("select b.color as color_id, sum(a.current_delivery) as current_delivery from pro_grey_prod_delivery_dtls a, product_details_master b where a.product_id=b.id and a.entry_form in(54,67) and a.order_id in ($po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  group by b.color", 'color_id', 'current_delivery');


                        //echo $finPurchaseArr;die;
                        //var_dump($finPurchaseArr);
                        $tot_req_qnty = 0;
                        $tot_avl_qnty = 0;
                        $tot_bl_qnty = 0;
                        $tot_delivery_qnty = $tot_fin_prod_qnty = 0;
                        foreach ($color_arr as $color_id) {
                            if ($i % 2 == 0)
                                $bgcolor = "#EFEFEF";
                            else
                                $bgcolor = "#FFFFFF";

                            $booking_qnty = $booking_qnty_arr[$color_id];
                            $fin_purchase_qnty = $rcv_data_arr[$color_id]["fin_purchase_qnty"];
							$finish_receive_roll_store=$datafinArr[$color_id]['finish_receive_roll_store'];
                            $fin_prod_qnty = $rcv_data_arr[$color_id]["grey_auto_rcv"] + $rcv_data_arr[$color_id]["grey_prod_rcv"]+$rcv_data_arr[$color_id]["fin_prod_rcv_roll"]+$finish_receive_roll_store;
                            $net_transfer_qnty = $datafinArr[$color_id]['trans_in'] - $datafinArr[$color_id]['trans_out'];
                            $fin_availlable = $fin_purchase_qnty + $fin_prod_qnty + $net_transfer_qnty;
                            $balance = $booking_qnty - $fin_availlable;
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                <td width="30"><? echo $i; ?></td>
                                <td width="100"><p><? echo $colorArr[$color_id]; ?>&nbsp;</p></td>
                                <td width="80" align="right"><? echo number_format($booking_qnty, 2); ?></td>
                                <td width="80" align="right"><? echo number_format($datafinArr[$color_id]['rcv'], 2); ?></td>
                                <td width="75" align="right"><? echo number_format($finDeliveryArr[$color_id], 2); ?></td>
                                <td width="75" align="right"><? echo number_format($fin_prod_qnty, 2); ?></td>
                                <td width="75" align="right"><? echo number_format($fin_purchase_qnty, 2); ?></td>
                                <td width="70" align="right"><? echo number_format($net_transfer_qnty, 2); ?></td>
                                <td width="75" align="right"><? echo number_format($fin_availlable, 2); ?></td>
                                <?
                                if ($tna_integrated == 1) {
                                    echo '<td width="75" align="right">' . number_format($balance, 2) . '</td>';
                                    echo '<td align="center">' . $actual_finish_date . '&nbsp;</td>';
                                } else {
                                    echo '<td align="right">' . number_format($balance, 2) . '&nbsp;</td>';
                                }
                                ?>
                            </tr>
                            <?
                            $tot_req_qnty += $booking_qnty;
                            $tot_prod_qnty += $datafinArr[$color_id]['rcv'];
                            $tot_delivery_qnty += $finDeliveryArr[$color_id];
                            $tot_fin_prod_qnty += $fin_prod_qnty;
                            $tot_pur_qnty += $fin_purchase_qnty;
                            $tot_net_qnty += $net_transfer_qnty;
                            $tot_avl_qnty += $fin_availlable;
                            $tot_bl_qnty += $balance;

                            $i++;
                        }
                        ?>
                        <tfoot>
                        <th colspan="2">&nbsp;</th>
                        <th align="right"><? echo number_format($tot_req_qnty, 2); ?></th>
                        <th align="right"><? echo number_format($tot_prod_qnty, 2); ?></th>
                        <th align="right"><? echo number_format($tot_delivery_qnty, 2); ?></th>
                        <th align="right"><? echo number_format($tot_fin_prod_qnty, 2); ?></th>
                        <th align="right"><? echo number_format($tot_pur_qnty, 2); ?></th>
                        <th align="right"><? echo number_format($tot_net_qnty, 2); ?></th>
                        <th align="right"><? echo number_format($tot_avl_qnty, 2); ?></th>
                        <th align="right"><? echo number_format($tot_bl_qnty, 2); ?></th>
    <?
    if ($tna_integrated == 1) {
        echo '<th>&nbsp;</th>';
    }
    ?>
                        </tfoot>
                    </table>
                </div>
            </fieldset>
        </div>
        <?
        exit();
    }

    if ($action == "woven_finish_fabric_popup") {
        echo load_html_head_contents("Finish Fabric Details", "../../../../", 1, 1, $unicode, '', '');

        $expData = explode('_', $job_number);
        $job_number = $expData[0];
        $po_id = $expData[1];
        ?>

        <div style="width:100%" align="center">
            <fieldset style="width:830px">
                <table width="600">
    <?
    $i = 1;
    $job_sql = sql_select("select job_no,buyer_name,company_name,style_ref_no from wo_po_details_master where job_no='$job_number'");
    foreach ($job_sql as $row_job)
        ;  // Master Job  table queery ends here
    ?>
                    <tr class="form_caption">
                        <td align="center" colspan="4"><strong>Finish Fabric Details</strong></td>
                    </tr>
                    <tr>
                        <td align="right" width="130"> <strong>Job Number</strong> :</td>
                        <td width="200"><? echo $job_number; ?></td>
                        <td align="right"  width="130"><strong>Buyer Name</strong> :</td>
                        <td><? echo $buyer_short_name_arr[$row_job[csf("buyer_name")]]; ?></td>
                    </tr>
                    <tr>
                        <td align="right"><strong>Company Name</strong> :</td>
                        <td><? echo $company_short_name_arr[$row_job[csf("company_name")]]; ?></td>
                        <td align="right"><strong>Style Ref No</strong> : </td>
                        <td><? echo $row_job[csf("style_ref_no")]; ?> </td>
                    </tr>
                    <tr>
                        <td colspan="4" height="15"></td>
                    </tr>
                </table>
                <table cellpadding="0" width="600" class="rpt_table" rules="all" border="1">
                    <thead>
                        <tr>
                            <th width="40">SL</th>
                            <th width="120">Color</th>
                            <th width="120">Finish Required</th>
                            <th width="120">Finish Available</th>
                            <th>Balance</th>
                        </tr>
                    </thead>
                </table>
                <div style="width:600px; overflow-y:scroll; max-height:250px">
                    <table cellpadding="0" width="580" class="rpt_table" rules="all" border="1">
                        <?
                        $bgcolor = "#EFEFEF";
                        $color_arr = array();
                        $booking_qnty_arr = array();
                        $datafinArr = array();
                        $finPurchaseArr = array();
                        $colorArr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');

                        $bookingData = sql_select("select b.fabric_color_id, sum(b.fin_fab_qnty) as fin_fab_qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and b.po_break_down_id in ($po_id) and a.item_category=3 and b.status_active=1 and b.is_deleted=0 group by b.fabric_color_id");
                        foreach ($bookingData as $row) {
                            $booking_qnty_arr[$row[csf('fabric_color_id')]] = $row[csf('fin_fab_qnty')];
                            $color_arr[$row[csf('fabric_color_id')]] = $row[csf('fabric_color_id')];
                        }
                        //echo "select color_id, sum(quantity) AS woven_fin_receive from order_wise_pro_details where status_active=1 and is_deleted=0 and entry_form=17 and po_breakdown_id in ($po_id) group by color_id";
                        $datafinTrans = sql_select("SELECT color_id, sum(quantity) AS woven_fin_receive from order_wise_pro_details where status_active=1 and is_deleted=0 and entry_form=17 and po_breakdown_id in ($po_id) group by color_id order by color_id");
                        foreach ($datafinTrans as $row) {
                            $datafinArr[$row[csf('color_id')]] = $row[csf('woven_fin_receive')];
                            $color_arr[$row[csf('color_id')]] = $row[csf('color_id')];
                        }
                        //var_dump($color_arr);
                        $tot_req_qnty = 0;
                        $tot_avl_qnty = 0;
                        $tot_bl_qnty = 0;
                        foreach ($color_arr as $color_id) {
                            if ($i % 2 == 0)
                                $bgcolor = "#EFEFEF";
                            else
                                $bgcolor = "#FFFFFF";

                            $booking_qnty = $booking_qnty_arr[$color_id];
                            $fin_availlable = $datafinArr[$color_id];
                            $balance = $booking_qnty - $fin_availlable;
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                <td width="40"><? echo $i; ?></td>
                                <td width="120"><p><? echo $colorArr[$color_id]; ?>&nbsp;</p></td>
                                <td width="120" align="right"><? echo number_format($booking_qnty, 2); ?></td>
                                <td width="120" align="right"><? echo number_format($fin_availlable, 2); ?></td>
                                <td align="right"><? echo number_format($balance, 2); ?></td>
                            </tr>
                            <?
                            $tot_req_qnty += $booking_qnty;
                            $tot_avl_qnty += $fin_availlable;
                            $tot_bl_qnty += $balance;

                            $i++;
                        }
                        ?>
                        <tfoot>
                        <th colspan="2">&nbsp;</th>
                        <th align="right"><? echo number_format($tot_req_qnty, 2); ?></th>
                        <th align="right"><? echo number_format($tot_avl_qnty, 2); ?></th>
                        <th align="right"><? echo number_format($tot_bl_qnty, 2); ?></th>
                        </tfoot>
                    </table>
                </div>
            </fieldset>
        </div>
        <?
        exit();
    }


    if ($action == "cutting_finish_popup") {
        echo load_html_head_contents("Cutting Finish Details", "../../../../", 1, 1, $unicode, '', '');

        $expData = explode('_', $job_number);
        $job_number = $expData[0];
        $po_id = $expData[1];
        $tna_integrated = $expData[2];
        $actual_finish_date = $expData[3];
        //echo $country;
        $country_id_arr = array_unique(explode(",", $country));
        $country_name_arr = "";
        foreach ($country_id_arr as $row_id) {
            if ($country_name_arr == "")
                $country_name_arr = $row_id;
            else
                $country_name_arr .= "," . $row_id;
        }//echo $country_name_arr;

        $colorArr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
        $sizeArr = return_library_array("select id, size_name from  lib_size", 'id', 'size_name');

        /* $sql = "select a.id,c.size_number_id,c.color_number_id,sum(b.production_qnty) as production_qnty
          from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c
          where a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id in ($po_id) and a.production_type=1 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.id,c.size_number_id,c.color_number_id"; */
        if ($type == 3) {
            $sql = "select c.size_number_id,c.color_number_id,sum(b.production_qnty) as production_qnty
			from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c
			where a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id in ($po_id) and c.country_id in($country_name_arr) and a.production_type=1 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.status_active=1 and c.status_active=1 group by c.size_number_id,c.color_number_id order by c.color_number_id,c.size_number_id";
        } else {
            $sql = "select c.size_number_id,c.color_number_id,sum(b.production_qnty) as production_qnty
			from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c
			where a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id in ($po_id) and a.production_type=1 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by c.size_number_id,c.color_number_id order by c.color_number_id,c.size_number_id";
        }

        //echo $sql;//die;
        $sqlResult = sql_select($sql);
        $poColorArr = $poSizeArr = $ColorSizeArr = array();
        //$ColorvalueArr=array();
        foreach ($sqlResult as $row) {
            $index = $row[csf("color_number_id")] . $row[csf("size_number_id")];
            if (!in_array($row[csf("size_number_id")], $poSizeArr))
                $poSizeArr[] = $row[csf("size_number_id")];
            if (!in_array($row[csf("color_number_id")], $poColorArr))
                $poColorArr[] = $row[csf("color_number_id")];
            $ColorSizeArr[$index] += $row[csf("production_qnty")];
            //$ColorvalueArr[]+=$row[csf("plan_cut_qnty")];
            //$mst_id=$row[csf("id")];
            /* $totalcolorqnty = return_field_value("sum(c.plan_cut_qnty) as plan_cut_qnty"," pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c"," a.id=$mst_id and a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id in ($po_id) and a.production_type=1 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by c.color_number_id","plan_cut_qnty"); */
        }


        $row_total_color_qnty = 0;
        $col_total_size_qnty = array();

        $qnty_array = array();
        $sizeArr_pend = array();
        $color_id_array = array();
        if ($type == 3) {
            $colorData = sql_select("select color_number_id, size_number_id, sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown where po_break_down_id in ($po_id) and country_id in($country_name_arr) group by color_number_id, size_number_id order by color_number_id, size_number_id");
        } else {
            $colorData = sql_select("select color_number_id, size_number_id, sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown where po_break_down_id in ($po_id) group by color_number_id, size_number_id order by color_number_id, size_number_id");
            //$colorData = sql_select("sum(plan_cut_qnty) as plan_cut_qnty","wo_po_color_size_breakdown","po_break_down_id in ($po_id) and color_number_id='".$poColorArr[$k]."'","plan_cut_qnty");
        }

        foreach ($colorData as $row) {
            $qnty_array[$row[csf('color_number_id')]][$row[csf('size_number_id')]] += $row[csf('plan_cut_qnty')];
            $sizeArr_pend[$row[csf('size_number_id')]] = $row[csf('size_number_id')];
            $color_id_array[$row[csf('color_number_id')]] = $row[csf('color_number_id')];
        }

        $noSize = count($sizeArr_pend);
        $width = 430 + ($noSize * 80);
        ?>

        <div style="width:100%" align="center">
            <fieldset style="width:<? echo $width; ?>px">
                <table width="600">
                    <?
                    if ($type == 3) {
                        $job_sql = sql_select("select a.job_no,a.buyer_name,a.company_name,a.style_ref_no,sum(c.order_quantity) as po_quantity,sum(c.plan_cut_qnty) as plan_cut from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and c.po_break_down_id=b.id and a.job_no='$job_number' and b.id in($po_id) and c.country_id in($country_name_arr) and a.is_deleted=0 and a.status_active=1 and c.status_active=1 and c.status_active=1 group by a.job_no,a.buyer_name,a.company_name,a.style_ref_no");
                    } else {
                        $job_sql = sql_select("select a.job_no,a.buyer_name,a.company_name,a.style_ref_no,b.po_quantity,b.plan_cut from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.job_no='$job_number' and b.id in($po_id) and a.is_deleted=0 and a.status_active=1 ");
                    }
                    foreach ($job_sql as $row_job)
                        ;  // Master Job  table queery ends here
                    ?>
                    <tr class="form_caption">
                        <td align="center" colspan="4"><strong>Cutting Finish Details</strong></td>
                    </tr>
                    <tr>
                        <td align="right" width="130"> <strong>Job Number</strong> :</td>
                        <td width="200"><? echo $job_number; ?></td>
                        <td align="right" width="140"><strong>Buyer Name</strong> :</td>
                        <td><? echo $buyer_short_name_arr[$row_job[csf("buyer_name")]]; ?></td>
                    </tr>
                    <tr>
                        <td align="right"><strong>Company Name</strong> :</td>
                        <td><? echo $company_short_name_arr[$row_job[csf("company_name")]]; ?></td>
                        <td align="right"><strong>Style Ref No</strong> : </td>
                        <td><? echo $row_job[csf("style_ref_no")]; ?> </td>
                    </tr>
                    <tr>
                        <td align="right"><strong>Plan Cut</strong> :</td>
                        <td><? echo $row_job[csf("plan_cut")]; ?> </td>
                        <td align="right"><strong>Order Quantity</strong> :</td>
                        <td><? echo $row_job[csf("po_quantity")]; ?> </td>
                    </tr>
                    <tr>
                        <td align="right"><strong><? if ($tna_integrated == 1) echo "TNA Actual Finish Date :"; ?></strong></td>
                        <td><? if ($tna_integrated == 1) echo $actual_finish_date; ?></td>
                    </tr>
                </table>

                <div style="width:100%" align="left"><b>Actual Quantity</b></div>
                <div style="width:100%">
                    <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                        <thead>
                            <tr>
                                <th width="130">Color/Size</th>
                                <th width="100">Color Qnty</th>
                                <?
                                foreach ($sizeArr_pend as $val) {
                                    ?>
                                    <th width="80"><? echo $sizeArr[$val]; ?></th>
        <?
    }
    ?>
                                <th width="80">Total</th>
                                <th width="">Balance</th>
                            </tr>
                        </thead>
                    </table>
                </div>

                <div style="width:100%; max-height:200px; overflow-y:scroll">
                    <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                        <?
                        $i = 0;
                        $grandTotal = 0;
                        $noColor = count($poColorArr);
                        // for($k=0;$k<$noColor;$k++)
                        foreach ($color_id_array as $color_id) {
                            $i++;
                            if ($i % 2 == 0)
                                $bgcolor = "#EFEFEF";
                            else
                                $bgcolor = "#FFFFFF";

                            if ($type == 3) {
                                //echo "select sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown where po_break_down_id in ($po_id) and country_id in($country_name_arr) and color_number_id='".$poColorArr[$k]."'";
                                $totalcolorqnty = return_field_value("sum(plan_cut_qnty) as plan_cut_qnty", "wo_po_color_size_breakdown", "po_break_down_id in ($po_id) and country_id in($country_name_arr) and color_number_id='" . $color_id . "'", "plan_cut_qnty");
                            } else {
                                $totalcolorqnty = return_field_value("sum(plan_cut_qnty) as plan_cut_qnty", "wo_po_color_size_breakdown", "po_break_down_id in ($po_id) and color_number_id='" . $color_id . "'", "plan_cut_qnty");
                            }
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                <td width="130"><? echo $colorArr[$color_id]; ?></td>
                                <td width="100" align="right"><? echo $totalcolorqnty; //$ColorvalueArr[$k];//[$poColorArr[$k].$poSizeArr[$j]];  ?></td>
                                <? foreach ($sizeArr_pend as $size_id) { ?>
                                    <td width="80" align="right">&nbsp;<? echo $ColorSizeArr[$color_id . $size_id]; ?></td>
            <?
            $row_total_color_qnty += $ColorSizeArr[$color_id . $size_id];
            $col_total_size_qnty[$size_id] += $ColorSizeArr[$color_id . $size_id];
        }
        ?>
                                <td width="80" align="right"><? echo $row_total_color_qnty; ?></td>
                                <td width="" align="right"><? echo $balance = $totalcolorqnty - $row_total_color_qnty; ?></td>
                            </tr>
                                    <?
                                    $row_total_color_qnty = 0;
                                }
                                ?>
                        <tfoot>
                            <tr>
                                <th colspan="2">Total</th>
    <?
    foreach ($sizeArr_pend as $val) {
        $grandTotal += $col_total_size_qnty[$val];
        ?>
                                    <th width="80" align="right"><? echo $col_total_size_qnty[$val]; ?></th>
    <? } ?>
                                <th><? echo $grandTotal; ?></th>
                                <th><? //echo $grandTotal;  ?></th>
                            </tr>
                        </tfoot>

                    </table>
                </div>

                <div style="width:100%" align="left"><b>Pending Quantity</b></div>
                <div style="width:100%">
                    <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                        <thead>
                            <tr>
                                <th width="130">Color/Size</th>
    <?
    				foreach ($sizeArr_pend as $val) {
        ?>
                                    <th width="80"><? echo $sizeArr[$val]; ?></th>
        <?
  					  }
    ?>
                                <th>Total</th>
                            </tr>
                        </thead>
                    </table>
                </div>

                <div style="width:100%; max-height:200px; overflow-y:scroll">
                    <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                        <?
                        $i = 0;
                        $grandTotal = 0;
                        $row_total_color_qnty = 0;
                        $col_total_size_qnty = array();
                        $noColor = count($poColorArr);
                        foreach ($color_id_array as $color_id) {
                            $i++;
                            if ($i % 2 == 0)
                                $bgcolor = "#EFEFEF";
                            else
                                $bgcolor = "#FFFFFF";
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_2<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_2<? echo $i; ?>">
                                <td width="130"><? echo $colorArr[$color_id]; ?></td>
                                <? foreach ($sizeArr_pend as $size_id) { ?>
                                    <td width="80" align="right">&nbsp;<?
                        $balance_qnty = $qnty_array[$color_id][$size_id] - $ColorSizeArr[$color_id . $size_id];
                        echo $balance_qnty;
                                    ?></td>
                                <?
                                $row_total_color_qnty += $balance_qnty;
                                $col_total_size_qnty[$size_id] += $balance_qnty;
                            }
                            ?>
                                <td align="right"><? echo $row_total_color_qnty; ?></td>
                            </tr>
                                    <?
                                    $row_total_color_qnty = 0;
                                }
                                ?>
                        <tfoot>
                            <tr>
                                <th>Total</th>
    <?
    foreach ($sizeArr_pend as $val) {
        $grandTotal += $col_total_size_qnty[$val];
        ?>
                                    <th width="80" align="right"><? echo $col_total_size_qnty[$val]; ?></th>
        <? } ?>
                                <th><? echo $grandTotal; ?></th>
                            </tr>
                        </tfoot>

                    </table>
                </div>
            </fieldset>
        </div>
        <?
        exit();
    }

    if ($action == "print_emb_completed_popup") {

        extract($_REQUEST);
        echo load_html_head_contents("Print Emb Details", "../../../../", 1, 1, $unicode, '', '');

        $expData = explode('_', $job_number);
        $job_number = $expData[0];
        $po_id = $expData[1];
        $tna_integrated = $expData[2];
        $actual_finish_date = $expData[3];
        $country_id_arr = array_unique(explode(",", $country));
        $country_name_arr = "";
        foreach ($country_id_arr as $row_id) {
            if ($country_name_arr == "")
                $country_name_arr = $row_id;
            else
                $country_name_arr .= "," . $row_id;
        }
        if ($country_name_arr == '')
            $country_cond = "";
        else
            $country_cond = " and country_id='$country_name_arr'";
        ?>
        <div id="data_panel" align="center" style="width:100%">
            <script>
                function new_window()
                {
                    var w = window.open("Surprise", "#");
                    var d = w.document.open();
                    d.write(document.getElementById('details_reports').innerHTML);
                    d.close();
                }
            </script>
            <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
        </div>
        <div id="details_reports">


            <table id="tbl_id" class="rpt_table" width="" border="1" rules="all" >
                <thead>
                    <tr>
                        <th width="100">Buyer</th>
                        <th width="100">Job Number</th>
                        <th width="100">Style Name</th>
                        <th width="200">Order Number</th>
                        <th width="100">Ship Date</th>
                        <th width="200">Item Name</th>
                        <th width="100">Order Qty.</th>
                    </tr>
                </thead>
                <?
                $buyer_short_library = return_library_array("select id,short_name from lib_buyer", "id", "short_name");
                $sql = "select a.job_no_mst, a.po_number as po_number, a.pub_shipment_date as pub_shipment_date, a.po_quantity as po_quantity, a.packing, b.set_break_down, b.company_name, b.order_uom, b.buyer_name, b.style_ref_no, b.gmts_item_id, b.total_set_qnty as set_item_ratio
                        from wo_po_break_down a, wo_po_details_master b
                        where a.job_no_mst=b.job_no and a.id in ($po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
                //echo $sql;
                $resultRow = sql_select($sql);

                foreach ($resultRow as $row) {
                    ?>
                    <tr style=" background-color:#FFFFFF">
                        <td><? echo $buyer_short_library[$row[csf("buyer_name")]]; ?></td>
                        <td><p><? echo $row[csf("job_no_mst")]; ?></p></td>
                        <td><p><? echo $row[csf("style_ref_no")]; ?></p></td>
                        <td><p><? echo implode(",", array_unique(explode(",", $row[csf("po_number")]))); ?></p></td>
                        <td><? echo change_date_format($row[csf("pub_shipment_date")]); ?></td>
                        <td>
        <?
        $garments_item_arr = explode(",", $row[csf("gmts_item_id")]);
        $all_garments = "";
        foreach ($garments_item_arr as $item_id) {
            $all_garments .= $garments_item[$item_id] . ",";
        }
        $all_garments = chop($all_garments, " , ");
        echo $all_garments;
        ?></td>
                        <td align="right"><? echo $row[csf("po_quantity")] * $row[csf("set_item_ratio")]; ?></td>
                    </tr>
        <?
    }
    $prod_sewing_sql = sql_select("SELECT sum(alter_qnty) as alter_qnty, sum(reject_qnty) as reject_qnty from pro_garments_production_mst where production_type=5 and po_break_down_id in ($po_id) and is_deleted=0 and status_active=1");
    foreach ($prod_sewing_sql as $sewingRow)
        ;
    ?>
                <tr>
                    <td colspan="2">Total Alter Sewing Qty : <b><? echo $sewingRow[csf("alter_qnty")]; ?></b></td>
                    <td colspan="2">Total Reject Sewing Qty : <b><? echo $sewingRow[csf("reject_qnty")]; ?></b></td>
                    <td colspan="2">Pack Assortment: <b><? echo $packing[$resultRow[csf("packing")]]; ?></b></td>
                </tr>
            </table>

            <br/>

            <table width="1040" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1" >
                <thead>

                    <tr>
                        <th width="30" rowspan="2">Sl.</th>
                        <th width="70" rowspan="2">Date</th>
                        <th colspan="3">Printing Receive</th>
                        <th colspan="3">Embroidery Receive</th>
                        <th colspan="3">Wash Receive</th>
                        <th colspan="3">Special Work Receive</th>
                    </tr>

                    <tr>

                        <th width="70">In-house</th>
                        <th width="70">Outside</th>
                        <th width="80">Embl. Company</th>

                        <th width="70">In-house</th>
                        <th width="70">Outside</th>
                        <th width="80">Embl. Company</th>

                        <th width="70">In-house</th>
                        <th width="70">Outside</th>
                        <th width="80">Embl. Company</th>

                        <th width="70">In-house</th>
                        <th width="70">Outside</th>
                        <th>Embl. Company</th>
                    </tr>
                </thead>
            </table>
            <div style="max-height:425px; overflow-y:scroll; width:1040px;" id="scroll_body">
                <table cellspacing="0" border="1" class="rpt_table"  width="1022" rules="all" id="table_body" >
                    <?
                    $company_library = return_library_array("select id,company_short_name from lib_company", "id", "company_short_name");
                    $supplier_library = return_library_array("select id,short_name from  lib_supplier", "id", "short_name");
                    //sql_select
                    $sql = sql_select("SELECT production_date,production_source,serving_company,
						SUM(CASE WHEN production_source =1 AND embel_name=1 THEN production_quantity ELSE 0 END) AS prod11,
						SUM(CASE WHEN production_source =1 AND embel_name=2 THEN production_quantity ELSE 0 END) AS prod12,
						SUM(CASE WHEN production_source =1 AND embel_name=3 THEN production_quantity ELSE 0 END) AS prod13,
						SUM(CASE WHEN production_source =1 AND embel_name=4 THEN production_quantity ELSE 0 END) AS prod14,

						SUM(CASE WHEN production_source =3 AND embel_name=1 THEN production_quantity ELSE 0 END) AS prod31,
						SUM(CASE WHEN production_source =3 AND embel_name=2 THEN production_quantity ELSE 0 END) AS prod32,
						SUM(CASE WHEN production_source =3 AND embel_name=3 THEN production_quantity ELSE 0 END) AS prod33,
						SUM(CASE WHEN production_source =3 AND embel_name=4 THEN production_quantity ELSE 0 END) AS prod34
 					FROM
						pro_garments_production_mst
					WHERE
						status_active=1 and is_deleted=0 and production_type=3 and po_break_down_id in ($po_id)
					GROUP BY production_date,production_source,serving_company");
                    // echo $sql; die;

                    $printing_in_qnty = 0;
                    $emb_in_qnty = 0;
                    $wash_in_qnty = 0;
                    $special_in_qnty = 0;
                    $printing_out_qnty = 0;
                    $emb_out_qnty = 0;
                    $wash_out_qnty = 0;
                    $special_out_qnty = 0;
                    $dataArray = array();
                    $companyArray = array();
                    $i = 1;
                    foreach ($sql as $resultRow) {
                        if ($i % 2 == 0)
                            $bgcolor = "#E9F3FF";
                        else
                            $bgcolor = "#FFFFFF";

                        if ($resultRow[csf('production_source')] == 3)
                            $serving_company = $supplier_library[$resultRow[csf('serving_company')]];
                        else
                            $serving_company = $company_library[$resultRow[csf('serving_company')]];
                        $td_count = 2;
                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                            <td width="30"><? echo $i; ?></td>
                            <td width="70" align="center"><? echo change_date_format($resultRow[csf("production_date")]); ?></td>

                            <td width="70" align="right"><?
                                if ($resultRow[csf('production_source')] == 1) {
                                    echo $resultRow[csf("prod11")];
                                    $printing_in_qnty += $resultRow[csf("prod11")];
                                } else
                                    echo "0";
                                ?></td>
                            <td width="70" align="right"><?
                            if ($resultRow[csf('production_source')] == 3) {
                                echo $resultRow[csf("prod31")];
                                $printing_out_qnty += $resultRow[csf("prod31")];
                            } else
                                echo "0";
                            ?></td>
                            <td width="80"><p>&nbsp;<? if ($resultRow[csf('prod11')] > 0 || $resultRow[csf('prod31')] > 0) echo $serving_company; ?></p></td>
                                <?
                                $companyArray[$serving_company] = $serving_company;
                                $dataArray[1][$serving_company] += $resultRow[csf("prod11")] + $resultRow[csf("prod31")]
                                ?>

                            <td width="70" align="right"><?
                                if ($resultRow[csf('production_source')] == 1) {
                                    echo $resultRow[csf("prod12")];
                                    $emb_in_qnty += $resultRow[csf("prod12")];
                                } else
                                    echo "0";
                                ?></td>
                            <td width="70" align="right"><?
                    if ($resultRow[csf('production_source')] == 3) {
                        echo $resultRow[csf("prod32")];
                        $emb_out_qnty += $resultRow[csf("prod32")];
                    } else
                        echo "0";
                                ?></td>
                            <td width="80"><p>&nbsp;<? if ($resultRow[csf('prod12')] > 0 || $resultRow[csf('prod32')] > 0) echo $serving_company; ?></p></td>
                                <? $dataArray[2][$serving_company] += $resultRow[csf("prod12")] + $resultRow[csf("prod32")] ?>

                            <td width="70" align="right"><?
                                if ($resultRow[csf('production_source')] == 1) {
                                    echo $resultRow[csf("prod13")];
                                    $wash_in_qnty += $resultRow[csf("prod13")];
                                } else
                                    echo "0";
                                ?></td>
                            <td width="70" align="right"><?
                    if ($resultRow[csf('production_source')] == 3) {
                        echo $resultRow[csf("prod33")];
                        $wash_out_qnty += $resultRow[csf("prod33")];
                    } else
                        echo "0";
                                ?></td>
                            <td width="80"><p>&nbsp;<? if ($resultRow[csf('prod13')] > 0 || $resultRow[csf('prod33')] > 0) echo $serving_company; ?></p></td>
                                <? $dataArray[3][$serving_company] += $resultRow[csf("prod13")] + $resultRow[csf("prod33")] ?>

                            <td width="70" align="right"><?
                                if ($resultRow[csf('production_source')] == 1) {
                                    echo $resultRow[csf("prod14")];
                                    $special_in_qnty += $resultRow[csf("prod14")];
                                } else
                                    echo "0";
                                ?></td>
                            <td width="70" align="right"><?
                        if ($resultRow[csf('production_source')] == 3) {
                            echo $resultRow[csf("prod34")];
                            $special_out_qnty += $resultRow[csf("prod34")];
                        } else
                            echo "0";
                        ?></td>
                            <td><p>&nbsp;<? if ($resultRow[csf('prod14')] > 0 || $resultRow[csf('prod34')] > 0) echo $serving_company; ?></p></td>
        <? $dataArray[4][$serving_company] += $resultRow[csf("prod14")] + $resultRow[csf("prod34")] ?>
                        </tr>
        <?
        $i++;
    }//end foreach 1st
    ?>
                    <tfoot>
                        <tr>
                            <th align="right" colspan="2">Grand Total</th>
                            <th align="right"><? echo $printing_in_qnty; ?></th>
                            <th align="right"><? echo $printing_out_qnty; ?></th>
                            <th align="right">&nbsp;</th>
                            <th align="right"><? echo $emb_in_qnty; ?></th>
                            <th align="right"><? echo $emb_out_qnty; ?></th>
                            <th align="right">&nbsp;</th>
                            <th align="right"><? echo $wash_in_qnty; ?></th>
                            <th align="right"><? echo $wash_out_qnty; ?></th>
                            <th align="right">&nbsp;</th>
                            <th align="right"><? echo $special_in_qnty; ?></th>
                            <th align="right"><? echo $special_out_qnty; ?></th>
                            <th align="right">&nbsp;</th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div style="clear:both">&nbsp;</div>

            <div style="width:450px; float:left">
                <table width="400" cellspacing="0" border="1" class="rpt_table" rules="all" >
                    <label><h3>Receive Summary</h3></label>
                    <thead>
                        <tr>
                            <th>SL</th>
                            <th>Emb.Company</th>
                            <th>Print</th>
                            <th>Embroidery</th>
                            <th>Emb	Wash</th>
                            <th>Special Work</th>
                        </tr>
                    </thead>
                    <?
                    $printing_total = 0;
                    $emb_total = 0;
                    $wash_total = 0;
                    $special_total = 0;
                    $i = 1;
                    foreach ($companyArray as $com) {
                        if ($i % 2 == 0)
                            $bgcolor = "#E9F3FF";
                        else
                            $bgcolor = "#FFFFFF";
                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>">
                            <td><? echo $i; ?></td>
                            <td><? echo $com; ?></td>
                            <td align="right"><?
                                echo number_format($dataArray[1][$com]);
                                $printing_total += $dataArray[1][$com];
                                ?></td>
                            <td align="right"><?
                        echo number_format($dataArray[2][$com]);
                        $emb_total += $dataArray[2][$com];
                        ?></td>
                            <td align="right"><?
                echo number_format($dataArray[3][$com]);
                $wash_total += $dataArray[3][$com];
                ?></td>
                            <td align="right"><?
                echo number_format($dataArray[4][$com]);
                $special_total += $dataArray[4][$com];
                ?></td>
                        </tr>
        <?
        $i++;
    }
    ?>
                    <tfoot>
                        <tr>
                            <th align="right" colspan="2">Grand Total</th>
                            <th align="right"><? echo number_format($printing_total); ?></th>
                            <th align="right"><? echo number_format($emb_total); ?></th>
                            <th align="right"><? echo number_format($wash_total); ?></th>
                            <th align="right"><? echo number_format($special_total); ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <?
        exit();
    }

    if ($action == "print_emb_completed_popup_fuad") {
        echo load_html_head_contents("Print Emb Details", "../../../../", 1, 1, $unicode, '', '');

        $expData = explode('_', $job_number);
        $job_number = $expData[0];
        $po_id = $expData[1];
        $tna_integrated = $expData[2];
        $actual_finish_date = $expData[3];
        $country_id_arr = array_unique(explode(",", $country));
        $country_name_arr = "";
        foreach ($country_id_arr as $row_id) {
            if ($country_name_arr == "")
                $country_name_arr = $row_id;
            else
                $country_name_arr .= "," . $row_id;
        }//echo $country_name_arr;
        //$colorArr = return_library_array( "select id, color_name from lib_color",'id','color_name');
        //$sizeArr = return_library_array( "select id, size_name from  lib_size",'id','size_name');
        //echo $type;
        if ($type == 3) {
            $sqlOrder = "SELECT
			SUM(CASE WHEN b.emb_name=1 and b.emb_type!=0 THEN b.cons_dzn_gmts ELSE 0 END) AS print,
			SUM(CASE WHEN b.emb_name=2 and b.emb_type!=0 THEN b.cons_dzn_gmts ELSE 0 END) AS emb,
			SUM(CASE WHEN b.emb_name=3 and b.emb_type!=0 THEN b.cons_dzn_gmts ELSE 0 END) AS wash,
			SUM(CASE WHEN b.emb_name=4 and b.emb_type!=0 THEN b.cons_dzn_gmts ELSE 0 END) AS special
		FROM
			wo_po_break_down a, wo_pre_cost_embe_cost_dtls b
		WHERE
			a.id in ($po_id) and a.job_no_mst=b.job_no";
        } else {
            $sqlOrder = "SELECT
			SUM(CASE WHEN b.emb_name=1 and b.emb_type!=0 THEN a.po_quantity*b.cons_dzn_gmts ELSE 0 END) AS print,
			SUM(CASE WHEN b.emb_name=2 and b.emb_type!=0 THEN a.po_quantity*b.cons_dzn_gmts ELSE 0 END) AS emb,
			SUM(CASE WHEN b.emb_name=3 and b.emb_type!=0 THEN a.po_quantity*b.cons_dzn_gmts ELSE 0 END) AS wash,
			SUM(CASE WHEN b.emb_name=4 and b.emb_type!=0 THEN a.po_quantity*b.cons_dzn_gmts ELSE 0 END) AS special
		FROM
			wo_po_break_down a, wo_pre_cost_embe_cost_dtls b
		WHERE
			a.id in ($po_id) and a.job_no_mst=b.job_no";
        }

        //echo $sqlOrder;
        $sql_order = sql_select($sqlOrder);
        $reqArr = array();
        foreach ($sql_order as $resultRow) {
            $reqArr[1] = $resultRow[csf("print")];
            $reqArr[2] = $resultRow[csf("emb")];
            $reqArr[3] = $resultRow[csf("wash")];
            $reqArr[4] = $resultRow[csf("special")];
        }
        ?>

        <div style="width:100%" align="center">
            <fieldset style="width:600px">
                <table width="600">
    <?
    if ($type == 3) {
        $job_sql = sql_select("select a.job_no,a.buyer_name,a.company_name,a.style_ref_no,sum(c.order_quantity) as po_quantity,sum(c.plan_cut_qnty) as plan_cut from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and  a.job_no=c.job_no_mst and  c.po_break_down_id=b.id and a.job_no='$job_number' and c.country_id in($country_name_arr) and a.status_active=1 and a.status_active=1 and  c.status_active=1 and c.status_active=1 group by a.job_no,a.buyer_name,a.company_name,a.style_ref_no ");
    } else {
        $job_sql = sql_select("select a.job_no,a.buyer_name,a.company_name,a.style_ref_no,b.po_quantity,b.plan_cut from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.job_no='$job_number'");
    }

    foreach ($job_sql as $row_job)
        ;
    ?>
                    <tr class="form_caption">
                        <td align="center" colspan="4"><strong>Print & Emb. Details</strong></td>
                    </tr>
                    <tr>
                        <td align="right" width="130"> <strong>Job Number</strong> :</td>
                        <td width="200"><? echo $job_number; ?></td>
                        <td align="right"  width="130"><strong>Buyer Name</strong> :</td>
                        <td><? echo $buyer_short_name_arr[$row_job[csf("buyer_name")]]; ?></td>
                    </tr>
                    <tr>
                        <td align="right"><strong>Company Name</strong> :</td>
                        <td><? echo $company_short_name_arr[$row_job[csf("company_name")]]; ?></td>
                        <td align="right"><strong>Style Ref No</strong> : </td>
                        <td><? echo $row_job[csf("style_ref_no")]; ?> </td>
                    </tr>
                    <tr>
                        <td align="right"><strong>Order Qnty</strong> :</td>
                        <td><? echo $row_job[csf("po_quantity")]; ?> </td>
                        <td align="right"><strong><? if ($tna_integrated == 1) echo "TNA Actual Finish Date"; ?></strong>:</td>
                        <td><? if ($tna_integrated == 1) echo $actual_finish_date; ?></td>
                    </tr>
                    <tr>
                        <td colspan="4" height="15"></td>
                    </tr>
                </table>


                <div style="width:100%">
                    <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                        <thead>
                            <tr>
                                <th width="50">SL</th>
                                <th width="100">Embel. Name</th>
                                <th width="100">Required</th>
                                <th width="100">Receive</th>
                                <th width="100">Balance</th>
                                <th width="">Receive(%)</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <div style="width:100%; max-height:270px; overflow-y:scroll">
                    <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                        <?
                        $i = 0;
                        if ($type == 3) {
                            $sql = "select a.embel_name,sum(b.production_qnty) as production_qnty
							from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c
							where a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id in ($po_id) and c.country_id in($country_name_arr) and a.production_type=3 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.embel_name";
                        } else {
                            $sql = "select a.embel_name,sum(b.production_qnty) as production_qnty
							from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c
							where a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id in ($po_id) and a.production_type=3 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.embel_name";
                        }

                        //echo $sql;die;
                        $sqlResult = sql_select($sql);
                        $totalPer = 0;
                        foreach ($sqlResult as $resultRow) {
                            $i++;
                            if ($i % 2 == 0)
                                $bgcolor = "#EFEFEF";
                            else
                                $bgcolor = "#FFFFFF";
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" >
                                <td width="50"><? echo $i; ?></td>
                                <td width="100"><? echo $emblishment_name_array[$resultRow[csf("embel_name")]]; ?></td>
                                <td width="100" align="right"><?
                                if ($type == 3)
                                    echo $reqArr[$resultRow[csf("embel_name")]] * $row_job[csf("po_quantity")];
                                else
                                    echo $reqArr[$resultRow[csf("embel_name")]];
                                ?>&nbsp;</td>
                                <td width="100" align="right"><? echo $resultRow[csf("production_qnty")]; ?></td>
                                <?
                                //$country_emb=$reqArr[$resultRow[csf("embel_name")]]*$row_job[csf("po_quantity")];
                                //$country_emb_without=$reqArr[$resultRow[csf("embel_name")]];
                                if ($type == 3) {
                                    $balance = ($reqArr[$resultRow[csf("embel_name")]] * $row_job[csf("po_quantity")]) - $resultRow[csf("production_qnty")];
                                } else {
                                    $balance = $reqArr[$resultRow[csf("embel_name")]] - $resultRow[csf("production_qnty")];
                                }
                                ?>
                                <td width="100" align="right"><? echo number_format($balance, 2); ?></td>
        <?
        if ($type == 3) {
            $percentage = $resultRow[csf("production_qnty")] / ($reqArr[$resultRow[csf("embel_name")]] * $row_job[csf("po_quantity")]) * 100;
            $totalPer += $percentage;
        } else {
            $percentage = $resultRow[csf("production_qnty")] / $reqArr[$resultRow[csf("embel_name")]] * 100;
            $totalPer += $percentage;
        }
        ?>
                                <td width="" align="right"><? echo number_format($percentage, 2) . "%"; ?></td>
                            </tr>
        <? } ?>
                        <tfoot>
                            <tr>
                                <th colspan="5"></th>
                                <th align="right"><? echo number_format($totalPer / $i, 2) . "%"; ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </fieldset>
        </div>
        <?
        exit();
    }

    if ($action == "sewing_finish_popup_prev") {
        echo load_html_head_contents("Sewing Finish Details", "../../../../", 1, 1, $unicode, '', '');

        $expData = explode('_', $job_number);
        $job_number = $expData[0];
        $po_id = $expData[1];
        $tna_integrated = $expData[2];
        $actual_finish_date = $expData[3];

        $colorArr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
        $sizeArr = return_library_array("select id, size_name from  lib_size", 'id', 'size_name');
        $country_id_arr = array_unique(explode(",", $country));
        $country_name_arr = "";
        foreach ($country_id_arr as $row_id) {
            if ($country_name_arr == "")
                $country_name_arr = $row_id;
            else
                $country_name_arr .= "," . $row_id;
        }//echo $country_name_arr;
        //echo $type;
        if ($type == 3) { //Country Wise
            $sql = "select  c.size_number_id,c.color_number_id,sum(b.production_qnty) as production_qnty, c.order_quantity as color_qnty
		from pro_garments_production_mst a, wo_po_color_size_breakdown c left join pro_garments_production_dtls b on b.color_size_break_down_id=c.id
		where a.id=b.mst_id  and a.po_break_down_id in ($po_id) and c.country_id in($country_name_arr) and a.production_type=5 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by c.color_number_id,c.size_number_id,c.order_quantity";
        } else {
            $sql = "select  c.size_number_id,c.color_number_id,sum(b.production_qnty) as production_qnty, c.order_quantity as color_qnty
		from pro_garments_production_mst a, wo_po_color_size_breakdown c left join pro_garments_production_dtls b on b.color_size_break_down_id=c.id
		where a.id=b.mst_id  and a.po_break_down_id in ($po_id) and a.production_type=5 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by c.color_number_id,c.size_number_id,c.order_quantity";
        }


        //echo $sql;
        $sqlResult = sql_select($sql);
        $poColorArr = $poSizeArr = $ColorSizeArr = array();
        $ColorvalueArr = array();
        foreach ($sqlResult as $row) {
            $index = $row[csf("color_number_id")] . $row[csf("size_number_id")];
            if (!in_array($row[csf("size_number_id")], $poSizeArr))
                $poSizeArr[] = $row[csf("size_number_id")];
            if (!in_array($row[csf("color_number_id")], $poColorArr))
                $poColorArr[] = $row[csf("color_number_id")];
            $ColorvalueArr[$row[csf("color_number_id")]] += $row[csf("color_qnty")];
            $ColorSizeArr[$index] += $row[csf("production_qnty")];
        }
        //var_dump($ColorSizeArr);
        //var_dump($ColorvalueArr);
        $noSize = count($poSizeArr);
        $width = 450 + ($noSize * 80);
        $row_total_color_qnty = 0;
        $col_total_size_qnty = array();
        ?>

        <div style="width:100%" align="center">
            <fieldset style="width:<? echo $width; ?>px">
                <table width="600">
    <?
    //$job_sql= sql_select("select a.job_no,a.buyer_name,a.company_name,a.style_ref_no,sum(a.job_quantity) as job_quantity,b.po_quantity,b.plan_cut from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.job_no='$job_number'");
    if ($type == 3) {
        $job_sql = sql_select("select a.id, a.job_no,a.buyer_name,a.company_name,a.style_ref_no,sum(c.order_quantity) as po_quantity from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and c.po_break_down_id=b.id and b.id in ($po_id) and c.country_id in($country_name_arr) group by a.id, a.job_no,a.buyer_name,a.company_name,a.style_ref_no ");
    } else {
        $job_sql = sql_select("select a.id, a.job_no,a.buyer_name,a.company_name,a.style_ref_no,sum(b.po_quantity) as po_quantity from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in ($po_id) group by a.id, a.job_no,a.buyer_name,a.company_name,a.style_ref_no ");
    }
    //a.job_no='$job_number' group by a.job_no
    foreach ($job_sql as $row_job)
        ;  // Master Job  table queery ends here
    ?>
                    <tr class="form_caption">
                        <td align="center" colspan="4"><strong>Sewing Finish Details</strong></td>
                    </tr>
                    <tr>
                        <td align="right" width="130"> <strong>Job Number</strong> :</td>
                        <td width="200"><? echo $job_number; ?></td>
                        <td align="right"  width="130"><strong>Buyer Name</strong> :</td>
                        <td><? echo $buyer_short_name_arr[$row_job[csf("buyer_name")]]; ?></td>
                    </tr>
                    <tr>
                        <td align="right"><strong>Company Name</strong> :</td>
                        <td><? echo $company_short_name_arr[$row_job[csf("company_name")]]; ?></td>
                        <td align="right"><strong>Style Ref No</strong> : </td>
                        <td><? echo $row_job[csf("style_ref_no")]; ?> </td>
                    </tr>
                    <tr>
                        <td align="right"><strong>Order Qnty</strong> :</td>
                        <td><?
                        $tot_color_qnty += $ColorvalueArr[$poColorArr[$k]];
                        echo $row_job[csf("po_quantity")];
    ?> </td>
                        <td align="right"><strong><? if ($tna_integrated == 1) echo "TNA Actual Finish Date"; ?></strong>:</td>
                        <td><? if ($tna_integrated == 1) echo $actual_finish_date; ?></td>
                    </tr>
                    <tr>
                        <td colspan="4" height="15"></td>
                    </tr>
                </table>
                <div style="width:100%" align="left"><b>Actual Quantity</b></div>
                <div style="width:100%">
                    <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                        <thead>
                            <tr>
                                <th width="150">Color/Size</th>
                                <th width="100">Color Qnty</th>
                        <? foreach ($poSizeArr as $val) { ?>
                                    <th width="80"><? echo $sizeArr[$val]; ?></th>
                        <? } ?>
                                <th width="80">Total</th>
                                <th width="">Balance</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <div style="width:100%; max-height:470px; overflow-y:scroll">
                    <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                                <?
                            $i = 0;
                            $grandTotal = 0;
                            $noColor = count($poColorArr);
                            for ($k = 0; $k < $noColor; $k++) {
                                $i++;
                                if ($i % 2 == 0)
                                    $bgcolor = "#EFEFEF";
                                else
                                    $bgcolor = "#FFFFFF";
                                ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                <td width="150"><? echo $colorArr[$poColorArr[$k]]; ?></td>
                                <td width="100" align="right"><?
                            echo $ColorvalueArr[$poColorArr[$k]];
                            $tot_color_qnty += $ColorvalueArr[$poColorArr[$k]]; //[$poColorArr[$k].$poSizeArr[$j]];
                            ?></td>
        <? for ($j = 0; $j < $noSize; $j++) { ?>
                                    <td width="80" align="right"><? echo $ColorSizeArr[$poColorArr[$k] . $poSizeArr[$j]]; ?></td>
                                        <?
                                        $row_total_color_qnty += $ColorSizeArr[$poColorArr[$k] . $poSizeArr[$j]];
                                        $col_total_size_qnty[$poSizeArr[$j]] += $ColorSizeArr[$poColorArr[$k] . $poSizeArr[$j]];
                                    }
                                    ?>
                                <td width="80" align="right"><? echo $row_total_color_qnty; ?></td>
                                <td width="" align="right"><? echo $balance = $ColorvalueArr[$poColorArr[$k]] - $row_total_color_qnty; ?></td>
                            </tr>
        <?
        $row_total_color_qnty = 0;
    }
    ?>
                        <tfoot>
                            <tr>
                                <th></th>
                                <th width="80" align="right"><? echo $tot_color_qnty; ?></th>
                <?
                foreach ($poSizeArr as $val) {
                    $grandTotal += $col_total_size_qnty[$val];
                    ?>
                                    <th width="80" align="right"><? echo $col_total_size_qnty[$val]; ?></th>
                <? } ?>
                                <th><? echo $grandTotal; ?></th>
                                <th><? //echo $grandTotal; ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <?
                $qnty_array = array();
                $sizeArr_pend = array();
                $color_id_array = array();
                if ($type == 3) {
                    //echo "select color_number_id, size_number_id, sum(order_quantity) as order_quantity from wo_po_color_size_breakdown where po_break_down_id in ($po_id) and country_id in($country_name_arr) group by color_number_id, size_number_id order by color_number_id, size_number_id";
                    $colorData = sql_select("select color_number_id, size_number_id, sum(order_quantity) as order_quantity from wo_po_color_size_breakdown where po_break_down_id in ($po_id) and country_id in($country_name_arr) group by color_number_id, size_number_id order by color_number_id, size_number_id");
                } else {
                    $colorData = sql_select("select color_number_id, size_number_id, sum(order_quantity) as order_quantity from wo_po_color_size_breakdown where po_break_down_id in ($po_id) group by color_number_id, size_number_id order by color_number_id, size_number_id");
                    ///echo "select color_number_id, size_number_id, sum(order_quantity) as order_quantity from wo_po_color_size_breakdown where po_break_down_id in ($po_id) group by color_number_id, size_number_id order by color_number_id, size_number_id";
                }

                foreach ($colorData as $row) {
                    $qnty_array[$row[csf('color_number_id')]][$row[csf('size_number_id')]] += $row[csf('order_quantity')];
                    $sizeArr_pend[$row[csf('size_number_id')]] = $row[csf('size_number_id')];
                    $color_id_array[$row[csf('color_number_id')]] = $row[csf('color_number_id')];
                }
                ?>
                <div style="width:100%" align="left"><b>Pending Quantity</b></div>
                <div style="width:100%">
                    <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                        <thead>
                            <tr>
                                <th width="130">Color/Size</th>
                        <?
                        foreach ($sizeArr_pend as $val) {
                            ?>
                                    <th width="80"><? echo $sizeArr[$val]; ?></th>
                        <? } ?>
                                <th>Total</th>
                            </tr>
                        </thead>
                    </table>
                </div>

                <div style="width:100%; max-height:200px; overflow-y:scroll">
                    <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                                <?
                                $i = 0;
                                $grandTotal = 0;
                                $row_total_color_qnty = 0;
                                $col_total_size_qnty = array();
                                $noColor = count($poColorArr);
                                foreach ($color_id_array as $color_id) {
                                    $i++;
                                    if ($i % 2 == 0)
                                        $bgcolor = "#EFEFEF";
                                    else
                                        $bgcolor = "#FFFFFF";
                                    ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_2<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_2<? echo $i; ?>">
                                <td width="130"><? echo $colorArr[$color_id]; ?></td>
                                    <? foreach ($sizeArr_pend as $size_id) { ?>
                                    <td width="80" align="right">&nbsp;<?
                                        $balance_qnty = $qnty_array[$color_id][$size_id] - $ColorSizeArr[$color_id . $size_id];
                                        echo $balance_qnty;
                                        ?></td>
                                        <?
                                        $row_total_color_qnty += $balance_qnty;
                                        $col_total_size_qnty[$size_id] += $balance_qnty;
                                    }
                                    ?>
                                <td align="right"><? echo $row_total_color_qnty; ?></td>
                            </tr>
        <?
        $row_total_color_qnty = 0;
    }
    ?>
                        <tfoot>
                            <tr>
                                <th>Total</th>
        <?
        foreach ($sizeArr_pend as $val) {
            $grandTotal += $col_total_size_qnty[$val];
            ?>
                                    <th width="80" align="right"><? echo $col_total_size_qnty[$val]; ?></th>
        <? } ?>
                                <th><? echo $grandTotal; ?></th>
                            </tr>
                        </tfoot>

                    </table>
                </div>
            </fieldset>
        </div>
        <?
        exit();
    }

    if ($action == "sewing_finish_popup") {
        echo load_html_head_contents("Sewing Finish Details", "../../../../", 1, 1, $unicode, '', '');

        $expData = explode('_', $job_number);
        $job_number = $expData[0];
        $po_id = $expData[1];
        $tna_integrated = $expData[2];
        $actual_finish_date = $expData[3];
        //echo $country;
        $country_id_arr = array_unique(explode(",", $country));
        $country_name_arr = "";
        foreach ($country_id_arr as $row_id) {
            if ($country_name_arr == "")
                $country_name_arr = $row_id;
            else
                $country_name_arr .= "," . $row_id;
        }//echo $country_name_arr;

        $colorArr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
        $sizeArr = return_library_array("select id, size_name from  lib_size", 'id', 'size_name');

        /* $sql = "select a.id,c.size_number_id,c.color_number_id,sum(b.production_qnty) as production_qnty
          from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c
          where a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id in ($po_id) and a.production_type=1 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.id,c.size_number_id,c.color_number_id"; */
        if ($type == 3) {
            $sql = "select c.size_number_id,c.color_number_id,sum(b.production_qnty) as production_qnty
			from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c
			where a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id in ($po_id) and c.country_id in($country_name_arr) and a.production_type=5 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.status_active=1 and c.status_active=1 group by c.size_number_id,c.color_number_id order by c.color_number_id,c.size_number_id";
        } else {
            $sql = "select c.size_number_id,c.color_number_id,sum(b.production_qnty) as production_qnty
			from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c
			where a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id in ($po_id) and a.production_type=5 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by c.size_number_id,c.color_number_id order by c.color_number_id,c.size_number_id";
        }

        //echo $sql;//die;
        $sqlResult = sql_select($sql);
        $poColorArr = $poSizeArr = $ColorSizeArr = array();
        //$ColorvalueArr=array();
        foreach ($sqlResult as $row) {
            $index = $row[csf("color_number_id")] . $row[csf("size_number_id")];
            if (!in_array($row[csf("size_number_id")], $poSizeArr))
                $poSizeArr[] = $row[csf("size_number_id")];
            if (!in_array($row[csf("color_number_id")], $poColorArr))
                $poColorArr[] = $row[csf("color_number_id")];
            $ColorSizeArr[$index] += $row[csf("production_qnty")];
            //$ColorvalueArr[]+=$row[csf("plan_cut_qnty")];
            //$mst_id=$row[csf("id")];
            /* $totalcolorqnty = return_field_value("sum(c.plan_cut_qnty) as plan_cut_qnty"," pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c"," a.id=$mst_id and a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id in ($po_id) and a.production_type=1 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by c.color_number_id","plan_cut_qnty"); */
        }


        $row_total_color_qnty = 0;
        $col_total_size_qnty = array();

        $qnty_array = array();
        $sizeArr_pend = array();
        $color_id_array = array();
        if ($type == 3) {
            $colorData = sql_select("select color_number_id, size_number_id, sum(order_quantity) as order_quantity from wo_po_color_size_breakdown where po_break_down_id in ($po_id) and country_id in($country_name_arr) group by color_number_id, size_number_id order by color_number_id, size_number_id");
        } else {
            $colorData = sql_select("select color_number_id, size_number_id, sum(order_quantity) as order_quantity from wo_po_color_size_breakdown where po_break_down_id in ($po_id) group by color_number_id, size_number_id order by color_number_id, size_number_id");
            //$colorData = sql_select("sum(plan_cut_qnty) as plan_cut_qnty","wo_po_color_size_breakdown","po_break_down_id in ($po_id) and color_number_id='".$poColorArr[$k]."'","plan_cut_qnty");
        }

        foreach ($colorData as $row) {
            $qnty_array[$row[csf('color_number_id')]][$row[csf('size_number_id')]] += $row[csf('order_quantity')];
            $sizeArr_pend[$row[csf('size_number_id')]] = $row[csf('size_number_id')];
            $color_id_array[$row[csf('color_number_id')]] += $row[csf('order_quantity')];
        }

        $noSize = count($sizeArr_pend);
        $width = 430 + ($noSize * 80);
        ?>

        <div style="width:100%" align="center">
            <fieldset style="width:<? echo $width; ?>px">
                <table width="600">
    <?
    if ($type == 3) {
        $job_sql = sql_select("select a.job_no,a.buyer_name,a.company_name,a.style_ref_no,sum(c.order_quantity) as po_quantity,sum(c.plan_cut_qnty) as plan_cut from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and c.po_break_down_id=b.id and a.job_no='$job_number' and b.id in($po_id) and c.country_id in($country_name_arr) and a.is_deleted=0 and a.status_active=1 and c.status_active=1 and c.status_active=1 group by a.job_no,a.buyer_name,a.company_name,a.style_ref_no");
    } else {
        $job_sql = sql_select("select a.job_no,a.buyer_name,a.company_name,a.style_ref_no,b.po_quantity,b.plan_cut from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.job_no='$job_number' and b.id in($po_id) and a.is_deleted=0 and a.status_active=1 ");
    }
    foreach ($job_sql as $row_job)
        ;  // Master Job  table queery ends here
    ?>
                    <tr class="form_caption">
                        <td align="center" colspan="4"><strong>Sewing Finish Details</strong></td>
                    </tr>
                    <tr>
                        <td align="right" width="130"> <strong>Job Number</strong> :</td>
                        <td width="200"><? echo $job_number; ?></td>
                        <td align="right" width="140"><strong>Buyer Name</strong> :</td>
                        <td><? echo $buyer_short_name_arr[$row_job[csf("buyer_name")]]; ?></td>
                    </tr>
                    <tr>
                        <td align="right"><strong>Company Name</strong> :</td>
                        <td><? echo $company_short_name_arr[$row_job[csf("company_name")]]; ?></td>
                        <td align="right"><strong>Style Ref No</strong> : </td>
                        <td><? echo $row_job[csf("style_ref_no")]; ?> </td>
                    </tr>
                    <tr>
                        <td align="right"><strong>Order Qty.</strong> :</td>
                        <td><? echo $row_job[csf("po_quantity")]; ?> </td>
                        <td align="right"><strong><? if ($tna_integrated == 1) echo "TNA Actual Finish Date :"; ?></strong></td>
                        <td><? if ($tna_integrated == 1) echo $actual_finish_date; ?></td>
                    </tr>
                    <tr>
                        <td></td>
                    </tr>
                </table>

                <div style="width:100%" align="left"><b>Actual Quantity</b></div>
                <div style="width:100%">
                    <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                        <thead>
                            <tr>
                                <th width="130">Color/Size</th>
                                <th width="100">Color Qnty</th>
                        <?
                        foreach ($sizeArr_pend as $val) {
                            ?>
                                    <th width="80"><? echo $sizeArr[$val]; ?></th>
                            <?
                        }
                        ?>
                                <th width="80">Total</th>
                                <th width="">Balance</th>
                            </tr>
                        </thead>
                    </table>
                </div>

                <div style="width:100%; max-height:200px; overflow-y:scroll">
                    <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                        <?
                        $i = 0;
                        $grandTotal = 0;
                        $noColor = count($poColorArr);
                        // for($k=0;$k<$noColor;$k++)
                        foreach ($color_id_array as $color_id => $totalcolorqnty) {
                            $i++;
                            if ($i % 2 == 0)
                                $bgcolor = "#EFEFEF";
                            else
                                $bgcolor = "#FFFFFF";

                            /* if($type==3)
                              {
                              //echo "select sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown where po_break_down_id in ($po_id) and country_id in($country_name_arr) and color_number_id='".$poColorArr[$k]."'";
                              $totalcolorqnty = return_field_value("sum(order_quantity) as order_quantity","wo_po_color_size_breakdown","po_break_down_id in ($po_id) and country_id in($country_name_arr) and color_number_id='".$color_id."'","order_quantity");
                              }
                              else
                              {
                              $totalcolorqnty = return_field_value("sum(order_quantity) as order_quantity","wo_po_color_size_breakdown","po_break_down_id in ($po_id) and color_number_id='".$color_id."'","plan_cut_qnty");
                              } */
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                <td width="130"><? echo $colorArr[$color_id]; ?></td>
                                <td width="100" align="right"><? echo $totalcolorqnty; //$ColorvalueArr[$k];//[$poColorArr[$k].$poSizeArr[$j]]; ?></td>
                                    <? foreach ($sizeArr_pend as $size_id) { ?>
                                    <td width="80" align="right">&nbsp;<? echo $ColorSizeArr[$color_id . $size_id]; ?></td>
                                        <?
                                        $row_total_color_qnty += $ColorSizeArr[$color_id . $size_id];
                                        $col_total_size_qnty[$size_id] += $ColorSizeArr[$color_id . $size_id];
                                    }
                                    ?>
                                <td width="80" align="right"><? echo $row_total_color_qnty; ?></td>
                                <td width="" align="right"><? echo $balance = $totalcolorqnty - $row_total_color_qnty; ?></td>
                            </tr>
        <?
        $row_total_color_qnty = 0;
    }
    ?>
                        <tfoot>
                            <tr>
                                <th colspan="2">Total</th>
                                <?
                                foreach ($sizeArr_pend as $val) {
                                    $grandTotal += $col_total_size_qnty[$val];
                                    ?>
                                    <th width="80" align="right"><? echo $col_total_size_qnty[$val]; ?></th>
                                <? } ?>
                                <th><? echo $grandTotal; ?></th>
                                <th><? //echo $grandTotal;  ?></th>
                            </tr>
                        </tfoot>

                    </table>
                </div>

                <div style="width:100%" align="left"><b>Pending Quantity</b></div>
                <div style="width:100%">
                    <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                        <thead>
                            <tr>
                                <th width="130">Color/Size</th>
                        <?
                        foreach ($sizeArr_pend as $val) {
                            ?>
                                    <th width="80"><? echo $sizeArr[$val]; ?></th>
                            <?
                        }
                        ?>
                                <th>Total</th>
                            </tr>
                        </thead>
                    </table>
                </div>

                <div style="width:100%; max-height:200px; overflow-y:scroll">
                    <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                            <?
                            $i = 0;
                            $grandTotal = 0;
                            $row_total_color_qnty = 0;
                            $col_total_size_qnty = array();
                            $noColor = count($poColorArr);
                            foreach ($color_id_array as $color_id => $totalcolorqnty) {
                                $i++;
                                if ($i % 2 == 0)
                                    $bgcolor = "#EFEFEF";
                                else
                                    $bgcolor = "#FFFFFF";
                                ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_2<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_2<? echo $i; ?>">
                                <td width="130"><? echo $colorArr[$color_id]; ?></td>
                                    <? foreach ($sizeArr_pend as $size_id) { ?>
                                    <td width="80" align="right">&nbsp;<?
                                        $balance_qnty = $qnty_array[$color_id][$size_id] - $ColorSizeArr[$color_id . $size_id];
                                        echo $balance_qnty;
                                        ?></td>
            <?
            $row_total_color_qnty += $balance_qnty;
            $col_total_size_qnty[$size_id] += $balance_qnty;
        }
        ?>
                                <td align="right"><? echo $row_total_color_qnty; ?></td>
                            </tr>
            <?
            $row_total_color_qnty = 0;
        }
        ?>
                        <tfoot>
                            <tr>
                                <th>Total</th>
        <?
        foreach ($sizeArr_pend as $val) {
            $grandTotal += $col_total_size_qnty[$val];
            ?>
                                    <th width="80" align="right"><? echo $col_total_size_qnty[$val]; ?></th>
        <? } ?>
                                <th><? echo $grandTotal; ?></th>
                            </tr>
                        </tfoot>

                    </table>
                </div>
            </fieldset>
        </div>
        <?
        exit();
    }


    if ($action == "iron_output_popup_prev") {
        echo load_html_head_contents("Iron Output Details", "../../../../", 1, 1, $unicode, '', '');

        $expData = explode('_', $job_number);
        $job_number = $expData[0];
        $po_id = $expData[1];
        $colorArr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
        $sizeArr = return_library_array("select id, size_name from  lib_size", 'id', 'size_name');

        $country_id_arr = array_unique(explode(",", $country));
        $country_name_arr = "";
        foreach ($country_id_arr as $row_id) {
            if ($country_name_arr == "")
                $country_name_arr = $row_id;
            else
                $country_name_arr .= "," . $row_id;
        }//echo $country_name_arr;

        if ($type == 3) {
            $sql = "select c.size_number_id,c.color_number_id,sum(b.production_qnty) as production_qnty, c.order_quantity as color_qnty
			from pro_garments_production_mst a, wo_po_color_size_breakdown c left join pro_garments_production_dtls b on b.color_size_break_down_id=c.id
			where a.id=b.mst_id  and a.po_break_down_id in ($po_id) and c.country_id in($country_name_arr) and a.production_type=7 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by c.color_number_id,c.size_number_id, c.order_quantity";
        } else {
            $sql = "select c.size_number_id,c.color_number_id,sum(b.production_qnty) as production_qnty, c.order_quantity as color_qnty
			from pro_garments_production_mst a, wo_po_color_size_breakdown c left join pro_garments_production_dtls b on b.color_size_break_down_id=c.id
			where a.id=b.mst_id  and a.po_break_down_id in ($po_id) and a.production_type=7 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by c.color_number_id,c.size_number_id, c.order_quantity";
        }

        //echo $sql;
        $sqlResult = sql_select($sql);
        $poColorArr = $poSizeArr = $ColorSizeArr = array();
        $ColorvalueArr = array();
        foreach ($sqlResult as $row) {
            $index = $row[csf("color_number_id")] . $row[csf("size_number_id")];
            if (!in_array($row[csf("size_number_id")], $poSizeArr))
                $poSizeArr[] = $row[csf("size_number_id")];
            if (!in_array($row[csf("color_number_id")], $poColorArr))
                $poColorArr[] = $row[csf("color_number_id")];
            $ColorvalueArr[$row[csf("color_number_id")]] += $row[csf("color_qnty")];
            $ColorSizeArr[$index] += $row[csf("production_qnty")];
        }
        $noSize = count($poSizeArr);
        $width = 450 + ($noSize * 80);
        $row_total_color_qnty = 0;
        $col_total_size_qnty = array();
        ?>

        <div style="width:100%" align="center">
            <fieldset style="width:<? echo $width; ?>px">
                <table width="600">
    <?
    if ($type == 3) {

        $job_sql = sql_select("select a.id, a.job_no,a.buyer_name,a.company_name,a.style_ref_no,sum(c.order_quantity) as po_quantity,sum(c.plan_cut_qnty) as plan_cut from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id in ($po_id) and c.country_id in($country_name_arr) group by a.id, a.job_no,a.buyer_name,a.company_name,a.style_ref_no");
    } else {
        $job_sql = sql_select("select a.id, a.job_no,a.buyer_name,a.company_name,a.style_ref_no,sum(b.po_quantity) as po_quantity,b.plan_cut from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in ($po_id) group by a.id, a.job_no,a.buyer_name,a.company_name,a.style_ref_no,b.plan_cut");
    }
    //a.job_no='$job_number' group by a.job_no
    foreach ($job_sql as $row_job)
        ;  // Master Job  table queery ends here
    ?>
                    <tr class="form_caption">
                        <td align="center" colspan="4"><strong>Iron Output Details</strong></td>
                    </tr>
                    <tr>
                        <td align="right" width="130"> <strong>Job Number</strong> :</td>
                        <td width="200"><? echo $job_number; ?></td>
                        <td align="right"  width="130"><strong>Buyer Name</strong> :</td>
                        <td><? echo $buyer_short_name_arr[$row_job[csf("buyer_name")]]; ?></td>
                    </tr>
                    <tr>
                        <td align="right"><strong>Company Name</strong> :</td>
                        <td><? echo $company_short_name_arr[$row_job[csf("company_name")]]; ?></td>
                        <td align="right"><strong>Style Ref No</strong> : </td>
                        <td><? echo $row_job[csf("style_ref_no")]; ?> </td>
                    </tr>
                    <tr>
                        <td align="right"><strong>Order Qnty</strong> :</td>
                        <td><?
    $tot_color_qnty += $ColorvalueArr[$poColorArr[$k]];
    echo $row_job[csf("po_quantity")];
    ?> </td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td colspan="4" height="15"></td>
                    </tr>
                </table>
                <div style="width:100%" align="left"><b>Actual Quantity</b></div>
                <div style="width:100%">
                    <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                        <thead>
                            <tr>
                                <th width="150">Color/Size</th>
                                <th width="100">Color Qnty</th>
    <? foreach ($poSizeArr as $val) { ?>
                                    <th width="80"><? echo $sizeArr[$val]; ?></th>
                                <? } ?>
                                <th width="80">Total</th>
                                <th width="">Balance</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <div style="width:100%; max-height:470px; overflow-y:scroll">
                    <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
    <?
    $i = 0;
    $grandTotal = 0;
    $noColor = count($poColorArr);
    for ($k = 0; $k < $noColor; $k++) {
        $i++;
        if ($i % 2 == 0)
            $bgcolor = "#EFEFEF";
        else
            $bgcolor = "#FFFFFF";
        ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                <td width="150"><? echo $colorArr[$poColorArr[$k]]; ?></td>
                                <td width="100" align="right"><?
                            echo $ColorvalueArr[$poColorArr[$k]];
                            $tot_color_qnty += $ColorvalueArr[$poColorArr[$k]]; //[$poColorArr[$k].$poSizeArr[$j]];
                            ?></td>
        <? for ($j = 0; $j < $noSize; $j++) { ?>
                                    <td width="80" align="right"><? echo $ColorSizeArr[$poColorArr[$k] . $poSizeArr[$j]]; ?></td>
            <?
            $row_total_color_qnty += $ColorSizeArr[$poColorArr[$k] . $poSizeArr[$j]];
            $col_total_size_qnty[$poSizeArr[$j]] += $ColorSizeArr[$poColorArr[$k] . $poSizeArr[$j]];
        }
        ?>
                                <td width="80" align="right"><? echo $row_total_color_qnty; ?></td>
                                <td width="" align="right"><? echo $balance = $ColorvalueArr[$poColorArr[$k]] - $row_total_color_qnty; ?></td>
                            </tr>
                    <?
                    $row_total_color_qnty = 0;
                }
                ?>
                        <tfoot>
                            <tr>
                                <th></th>
                                <th width="80" align="right"><? echo $tot_color_qnty; ?></th>
                <?
                foreach ($poSizeArr as $val) {
                    $grandTotal += $col_total_size_qnty[$val];
                    ?>
                                    <th width="80" align="right"><? echo $col_total_size_qnty[$val]; ?></th>
    <? } ?>
                                <th><? echo $grandTotal; ?></th>
                                <th><? //echo $grandTotal;  ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                                <?
                                $qnty_array = array();
                                $sizeArr_pend = array();
                                $color_id_array = array();
                                if ($type == 3) {
                                    //echo "select color_number_id, size_number_id, sum(order_quantity) as order_quantity from wo_po_color_size_breakdown where po_break_down_id in ($po_id) and country_id in($country_name_arr) group by color_number_id, size_number_id order by color_number_id, size_number_id";
                                    $colorData = sql_select("select color_number_id, size_number_id, sum(order_quantity) as order_quantity from wo_po_color_size_breakdown where po_break_down_id in ($po_id) and country_id in($country_name_arr) group by color_number_id, size_number_id order by color_number_id, size_number_id");
                                } else {
                                    $colorData = sql_select("select color_number_id, size_number_id, sum(order_quantity) as order_quantity from wo_po_color_size_breakdown where po_break_down_id in ($po_id) group by color_number_id, size_number_id order by color_number_id, size_number_id");
                                    ///echo "select color_number_id, size_number_id, sum(order_quantity) as order_quantity from wo_po_color_size_breakdown where po_break_down_id in ($po_id) group by color_number_id, size_number_id order by color_number_id, size_number_id";
                                }

                                foreach ($colorData as $row) {
                                    $qnty_array[$row[csf('color_number_id')]][$row[csf('size_number_id')]] += $row[csf('order_quantity')];
                                    $sizeArr_pend[$row[csf('size_number_id')]] = $row[csf('size_number_id')];
                                    $color_id_array[$row[csf('color_number_id')]] = $row[csf('color_number_id')];
                                }
                                ?>
                <div style="width:100%" align="left"><b>Pending Quantity</b></div>
                <div style="width:100%">
                    <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                        <thead>
                            <tr>
                                <th width="130">Color/Size</th>
                        <?
                        foreach ($sizeArr_pend as $val) {
                            ?>
                                    <th width="80"><? echo $sizeArr[$val]; ?></th>
                                <? } ?>
                                <th>Total</th>
                            </tr>
                        </thead>
                    </table>
                </div>

                <div style="width:100%; max-height:200px; overflow-y:scroll">
                    <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                        <?
                        $i = 0;
                        $grandTotal = 0;
                        $row_total_color_qnty = 0;
                        $col_total_size_qnty = array();
                        $noColor = count($poColorArr);
                        foreach ($color_id_array as $color_id) {
                            $i++;
                            if ($i % 2 == 0)
                                $bgcolor = "#EFEFEF";
                            else
                                $bgcolor = "#FFFFFF";
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_2<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_2<? echo $i; ?>">
                                <td width="130"><? echo $colorArr[$color_id]; ?></td>
        <? foreach ($sizeArr_pend as $size_id) { ?>
                                    <td width="80" align="right">&nbsp;<?
            $balance_qnty = $qnty_array[$color_id][$size_id] - $ColorSizeArr[$color_id . $size_id];
            echo $balance_qnty;
            ?></td>
                <?
                $row_total_color_qnty += $balance_qnty;
                $col_total_size_qnty[$size_id] += $balance_qnty;
            }
            ?>
                                <td align="right"><? echo $row_total_color_qnty; ?></td>
                            </tr>
            <?
            $row_total_color_qnty = 0;
        }
        ?>
                        <tfoot>
                            <tr>
                                <th>Total</th>
        <?
        foreach ($sizeArr_pend as $val) {
            $grandTotal += $col_total_size_qnty[$val];
            ?>
                                    <th width="80" align="right"><? echo $col_total_size_qnty[$val]; ?></th>
        <? } ?>
                                <th><? echo $grandTotal; ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </fieldset>
        </div>
        <?
        exit();
    }

    if ($action == "iron_output_popup") {
        echo load_html_head_contents("Iron Output Details", "../../../../", 1, 1, $unicode, '', '');

        $expData = explode('_', $job_number);
        $job_number = $expData[0];
        $po_id = $expData[1];
        $tna_integrated = $expData[2];
        $actual_finish_date = $expData[3];
        //echo $country;
        $country_id_arr = array_unique(explode(",", $country));
        $country_name_arr = "";
        foreach ($country_id_arr as $row_id) {
            if ($country_name_arr == "")
                $country_name_arr = $row_id;
            else
                $country_name_arr .= "," . $row_id;
        }//echo $country_name_arr;

        $colorArr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
        $sizeArr = return_library_array("select id, size_name from  lib_size", 'id', 'size_name');

        /* $sql = "select a.id,c.size_number_id,c.color_number_id,sum(b.production_qnty) as production_qnty
          from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c
          where a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id in ($po_id) and a.production_type=1 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.id,c.size_number_id,c.color_number_id"; */
        if ($type == 3) {
            $sql = "select c.size_number_id,c.color_number_id,sum(b.production_qnty) as production_qnty
			from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c
			where a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id in ($po_id) and c.country_id in($country_name_arr) and a.production_type=7 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.status_active=1 and c.status_active=1 group by c.size_number_id,c.color_number_id order by c.color_number_id,c.size_number_id";
        } else {
            $sql = "select c.size_number_id,c.color_number_id,sum(b.production_qnty) as production_qnty
			from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c
			where a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id in ($po_id) and a.production_type=7 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by c.size_number_id,c.color_number_id order by c.color_number_id,c.size_number_id";
        }

        //echo $sql;//die;
        $sqlResult = sql_select($sql);
        $poColorArr = $poSizeArr = $ColorSizeArr = array();
        //$ColorvalueArr=array();
        foreach ($sqlResult as $row) {
            $index = $row[csf("color_number_id")] . $row[csf("size_number_id")];
            if (!in_array($row[csf("size_number_id")], $poSizeArr))
                $poSizeArr[] = $row[csf("size_number_id")];
            if (!in_array($row[csf("color_number_id")], $poColorArr))
                $poColorArr[] = $row[csf("color_number_id")];
            $ColorSizeArr[$index] += $row[csf("production_qnty")];
            //$ColorvalueArr[]+=$row[csf("plan_cut_qnty")];
            //$mst_id=$row[csf("id")];
            /* $totalcolorqnty = return_field_value("sum(c.plan_cut_qnty) as plan_cut_qnty"," pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c"," a.id=$mst_id and a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id in ($po_id) and a.production_type=1 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by c.color_number_id","plan_cut_qnty"); */
        }


        $row_total_color_qnty = 0;
        $col_total_size_qnty = array();

        $qnty_array = array();
        $sizeArr_pend = array();
        $color_id_array = array();
        if ($type == 3) {
            $colorData = sql_select("select color_number_id, size_number_id, sum(order_quantity) as order_quantity from wo_po_color_size_breakdown where po_break_down_id in ($po_id) and country_id in($country_name_arr) group by color_number_id, size_number_id order by color_number_id, size_number_id");
        } else {
            $colorData = sql_select("select color_number_id, size_number_id, sum(order_quantity) as order_quantity from wo_po_color_size_breakdown where po_break_down_id in ($po_id) group by color_number_id, size_number_id order by color_number_id, size_number_id");
            //$colorData = sql_select("sum(plan_cut_qnty) as plan_cut_qnty","wo_po_color_size_breakdown","po_break_down_id in ($po_id) and color_number_id='".$poColorArr[$k]."'","plan_cut_qnty");
        }

        foreach ($colorData as $row) {
            $qnty_array[$row[csf('color_number_id')]][$row[csf('size_number_id')]] += $row[csf('order_quantity')];
            $sizeArr_pend[$row[csf('size_number_id')]] = $row[csf('size_number_id')];
            $color_id_array[$row[csf('color_number_id')]] += $row[csf('order_quantity')];
        }

        $noSize = count($sizeArr_pend);
        $width = 430 + ($noSize * 80);
        ?>

        <div style="width:100%" align="center">
            <fieldset style="width:<? echo $width; ?>px">
                <table width="600">
    <?
    if ($type == 3) {
        $job_sql = sql_select("select a.job_no,a.buyer_name,a.company_name,a.style_ref_no,sum(c.order_quantity) as po_quantity,sum(c.plan_cut_qnty) as plan_cut from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and c.po_break_down_id=b.id and a.job_no='$job_number' and b.id in($po_id) and c.country_id in($country_name_arr) and a.is_deleted=0 and a.status_active=1 and c.status_active=1 and c.status_active=1 group by a.job_no,a.buyer_name,a.company_name,a.style_ref_no");
    } else {
        $job_sql = sql_select("select a.job_no,a.buyer_name,a.company_name,a.style_ref_no,b.po_quantity,b.plan_cut from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.job_no='$job_number' and b.id in($po_id) and a.is_deleted=0 and a.status_active=1 ");
    }
    foreach ($job_sql as $row_job)
    {
          // Master Job  table queery ends here
        
    }
    ?>
                    <tr class="form_caption">
                        <td align="center" colspan="4"><strong>Iron Finish Details</strong></td>
                    </tr>
                    <tr>
                        <td align="right" width="130"> <strong>Job Number</strong> :</td>
                        <td width="200"><? echo $job_number; ?></td>
                        <td align="right" width="140"><strong>Buyer Name</strong> :</td>
                        <td><? echo $buyer_short_name_arr[$row_job[csf("buyer_name")]]; ?></td>
                    </tr>
                    <tr>
                        <td align="right"><strong>Company Name</strong> :</td>
                        <td><? echo $company_short_name_arr[$row_job[csf("company_name")]]; ?></td>
                        <td align="right"><strong>Style Ref No</strong> : </td>
                        <td><? echo $row_job[csf("style_ref_no")]; ?> </td>
                    </tr>
                    <tr>
                        <td align="right"><strong>Order Qty.</strong> :</td>
                        <td><? echo $row_job[csf("po_quantity")]; ?> </td>
                        <td align="right"><strong><? if ($tna_integrated == 1) echo "TNA Actual Finish Date :"; ?></strong></td>
                        <td><? if ($tna_integrated == 1) echo $actual_finish_date; ?></td>
                    </tr>
                    <tr>
                        <td></td>
                    </tr>
                </table>

                <div style="width:100%" align="left"><b>Actual Quantity</b></div>
                <div style="width:100%">
                    <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                        <thead>
                            <tr>
                                <th width="130">Color/Size</th>
                                <th width="100">Color Qnty</th>
                        <?
                        foreach ($sizeArr_pend as $val) {
                            ?>
                                    <th width="80"><? echo $sizeArr[$val]; ?></th>
                            <?
                        }
                        ?>
                                <th width="80">Total</th>
                                <th width="">Balance</th>
                            </tr>
                        </thead>
                    </table>
                </div>

                <div style="width:100%; max-height:200px; overflow-y:scroll">
                    <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                            <?
                            $i = 0;
                            $grandTotal = 0;
                            $noColor = count($poColorArr);
                            // for($k=0;$k<$noColor;$k++)
                            foreach ($color_id_array as $color_id => $totalcolorqnty) {
                                $i++;
                                if ($i % 2 == 0)
                                    $bgcolor = "#EFEFEF";
                                else
                                    $bgcolor = "#FFFFFF";

                                /* if($type==3)
                                  {
                                  //echo "select sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown where po_break_down_id in ($po_id) and country_id in($country_name_arr) and color_number_id='".$poColorArr[$k]."'";
                                  $totalcolorqnty = return_field_value("sum(order_quantity) as order_quantity","wo_po_color_size_breakdown","po_break_down_id in ($po_id) and country_id in($country_name_arr) and color_number_id='".$color_id."'","order_quantity");
                                  }
                                  else
                                  {
                                  $totalcolorqnty = return_field_value("sum(order_quantity) as order_quantity","wo_po_color_size_breakdown","po_break_down_id in ($po_id) and color_number_id='".$color_id."'","plan_cut_qnty");
                                  } */
                                ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                <td width="130"><? echo $colorArr[$color_id]; ?></td>
                                <td width="100" align="right"><? echo $totalcolorqnty; //$ColorvalueArr[$k];//[$poColorArr[$k].$poSizeArr[$j]];   ?></td>
        <? foreach ($sizeArr_pend as $size_id) { ?>
                                    <td width="80" align="right">&nbsp;<? echo $ColorSizeArr[$color_id . $size_id]; ?></td>
            <?
            $row_total_color_qnty += $ColorSizeArr[$color_id . $size_id];
            $col_total_size_qnty[$size_id] += $ColorSizeArr[$color_id . $size_id];
        }
        ?>
                                <td width="80" align="right"><? echo $row_total_color_qnty; ?></td>
                                <td width="" align="right"><? echo $balance = $totalcolorqnty - $row_total_color_qnty; ?></td>
                            </tr>
                                    <?
                                    $row_total_color_qnty = 0;
                                }
                                ?>
                        <tfoot>
                            <tr>
                                <th colspan="2">Total</th>
    <?
    foreach ($sizeArr_pend as $val) {
        $grandTotal += $col_total_size_qnty[$val];
        ?>
                                    <th width="80" align="right"><? echo $col_total_size_qnty[$val]; ?></th>
    <? } ?>
                                <th><? echo $grandTotal; ?></th>
                                <th><? //echo $grandTotal;  ?></th>
                            </tr>
                        </tfoot>

                    </table>
                </div>

                <div style="width:100%" align="left"><b>Pending Quantity</b></div>
                <div style="width:100%">
                    <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                        <thead>
                            <tr>
                                <th width="130">Color/Size</th>
    <?
    foreach ($sizeArr_pend as $val) {
        ?>
                                    <th width="80"><? echo $sizeArr[$val]; ?></th>
                                    <?
                                }
                                ?>
                                <th>Total</th>
                            </tr>
                        </thead>
                    </table>
                </div>

                <div style="width:100%; max-height:200px; overflow-y:scroll">
                    <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                        <?
                        $i = 0;
                        $grandTotal = 0;
                        $row_total_color_qnty = 0;
                        $col_total_size_qnty = array();
                        $noColor = count($poColorArr);
                        foreach ($color_id_array as $color_id => $totalcolorqnty) {
                            $i++;
                            if ($i % 2 == 0)
                                $bgcolor = "#EFEFEF";
                            else
                                $bgcolor = "#FFFFFF";
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_2<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_2<? echo $i; ?>">
                                <td width="130"><? echo $colorArr[$color_id]; ?></td>
        <? foreach ($sizeArr_pend as $size_id) { ?>
                                    <td width="80" align="right">&nbsp;<?
            $balance_qnty = $qnty_array[$color_id][$size_id] - $ColorSizeArr[$color_id . $size_id];
            echo $balance_qnty;
            ?></td>
                <?
                $row_total_color_qnty += $balance_qnty;
                $col_total_size_qnty[$size_id] += $balance_qnty;
            }
            ?>
                                <td align="right"><? echo $row_total_color_qnty; ?></td>
                            </tr>
            <?
            $row_total_color_qnty = 0;
        }
        ?>
                        <tfoot>
                            <tr>
                                <th>Total</th>
        <?
        foreach ($sizeArr_pend as $val) {
            $grandTotal += $col_total_size_qnty[$val];
            ?>
                                    <th width="80" align="right"><? echo $col_total_size_qnty[$val]; ?></th>
        <? } ?>
                                <th><? echo $grandTotal; ?></th>
                            </tr>
                        </tfoot>

                    </table>
                </div>
            </fieldset>
        </div>
        <?
        exit();
    }

    if ($action == "finish_completed_popup") {
        echo load_html_head_contents("Finish Complete Details", "../../../../", 1, 1, $unicode, '', '');

        $expData = explode('_', $job_number);
        $job_number = $expData[0];
        $po_id = $expData[1];
        $tna_integrated = $expData[2];
        $actual_finish_date = $expData[3];
        //echo $country;
        $country_id_arr = array_unique(explode(",", $country));
        $country_name_arr = "";
        foreach ($country_id_arr as $row_id) {
            if ($country_name_arr == "")
                $country_name_arr = $row_id;
            else
                $country_name_arr .= "," . $row_id;
        }//echo $country_name_arr;

        $colorArr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
        $sizeArr = return_library_array("select id, size_name from  lib_size", 'id', 'size_name');

        /* $sql = "select a.id,c.size_number_id,c.color_number_id,sum(b.production_qnty) as production_qnty
          from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c
          where a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id in ($po_id) and a.production_type=1 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.id,c.size_number_id,c.color_number_id"; */
        if ($type == 3) {
            $sql = "select c.size_number_id,c.color_number_id,sum(b.production_qnty) as production_qnty
			from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c
			where a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id in ($po_id) and c.country_id in($country_name_arr) and a.production_type=8 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.status_active=1 and c.status_active=1 group by c.size_number_id,c.color_number_id order by c.color_number_id,c.size_number_id";
        } else {
            $sql = "select c.size_number_id,c.color_number_id,sum(b.production_qnty) as production_qnty
			from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c
			where a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id in ($po_id) and a.production_type=8 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by c.size_number_id,c.color_number_id order by c.color_number_id,c.size_number_id";
        }

       // echo $sql;//die;
        $sqlResult = sql_select($sql);
        $poColorArr = $poSizeArr = $ColorSizeArr = array();
        //$ColorvalueArr=array();
        foreach ($sqlResult as $row) {
            $index = $row[csf("color_number_id")] . $row[csf("size_number_id")];
            if (!in_array($row[csf("size_number_id")], $poSizeArr))
                $poSizeArr[] = $row[csf("size_number_id")];
            if (!in_array($row[csf("color_number_id")], $poColorArr))
                $poColorArr[] = $row[csf("color_number_id")];
            $ColorSizeArr[$index] += $row[csf("production_qnty")];
            //$ColorvalueArr[]+=$row[csf("plan_cut_qnty")];
            //$mst_id=$row[csf("id")];
            /* $totalcolorqnty = return_field_value("sum(c.plan_cut_qnty) as plan_cut_qnty"," pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c"," a.id=$mst_id and a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id in ($po_id) and a.production_type=1 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by c.color_number_id","plan_cut_qnty"); */
        }


        $row_total_color_qnty = 0;
        $col_total_size_qnty = array();

        $qnty_array = array();
        $sizeArr_pend = array();
        $color_id_array = array();
        if ($type == 3) {
            $colorData = sql_select("select color_number_id, size_number_id, sum(order_quantity) as order_quantity from wo_po_color_size_breakdown where po_break_down_id in ($po_id) and country_id in($country_name_arr) group by color_number_id, size_number_id order by color_number_id, size_number_id");
        } else {
            $colorData = sql_select("select color_number_id, size_number_id, sum(order_quantity) as order_quantity from wo_po_color_size_breakdown where po_break_down_id in ($po_id) group by color_number_id, size_number_id order by color_number_id, size_number_id");
            //$colorData = sql_select("sum(plan_cut_qnty) as plan_cut_qnty","wo_po_color_size_breakdown","po_break_down_id in ($po_id) and color_number_id='".$poColorArr[$k]."'","plan_cut_qnty");
        }

        foreach ($colorData as $row) {
            $qnty_array[$row[csf('color_number_id')]][$row[csf('size_number_id')]] += $row[csf('order_quantity')];
            $sizeArr_pend[$row[csf('size_number_id')]] = $row[csf('size_number_id')];
            $color_id_array[$row[csf('color_number_id')]] += $row[csf('order_quantity')];
        }

        $noSize = count($sizeArr_pend);
        $width = 430 + ($noSize * 80);
        ?>

        <div style="width:100%" align="center">
            <fieldset style="width:<? echo $width; ?>px">
                <table width="600">
    <?
    if ($type == 3) {
        $job_sql = sql_select("select a.job_no,a.buyer_name,a.company_name,a.style_ref_no,sum(c.order_quantity) as po_quantity,sum(c.plan_cut_qnty) as plan_cut from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and c.po_break_down_id=b.id and a.job_no='$job_number' and b.id in($po_id) and c.country_id in($country_name_arr) and a.is_deleted=0 and a.status_active=1 and c.status_active=1 and c.status_active=1 group by a.job_no,a.buyer_name,a.company_name,a.style_ref_no");
    } else {
        $job_sql = sql_select("select a.job_no,a.buyer_name,a.company_name,a.style_ref_no,b.po_quantity,b.plan_cut from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.job_no='$job_number' and b.id in($po_id) and a.is_deleted=0 and a.status_active=1 ");
    }
    foreach ($job_sql as $row_job)
        ;  // Master Job  table queery ends here
    ?>
                    <tr class="form_caption">
                        <td align="center" colspan="4"><strong>Finishing Details</strong></td>
                    </tr>
                    <tr>
                        <td align="right" width="130"> <strong>Job Number</strong> :</td>
                        <td width="200"><? echo $job_number; ?></td>
                        <td align="right" width="140"><strong>Buyer Name</strong> :</td>
                        <td><? echo $buyer_short_name_arr[$row_job[csf("buyer_name")]]; ?></td>
                    </tr>
                    <tr>
                        <td align="right"><strong>Company Name</strong> :</td>
                        <td><? echo $company_short_name_arr[$row_job[csf("company_name")]]; ?></td>
                        <td align="right"><strong>Style Ref No</strong> : </td>
                        <td><? echo $row_job[csf("style_ref_no")]; ?> </td>
                    </tr>
                    <tr>
                        <td align="right"><strong>Order Qty.</strong> :</td>
                        <td><? echo $row_job[csf("po_quantity")]; ?> </td>
                        <td align="right"><strong><? if ($tna_integrated == 1) echo "TNA Actual Finish Date :"; ?></strong></td>
                        <td><? if ($tna_integrated == 1) echo $actual_finish_date; ?></td>
                    </tr>
                    <tr>
                        <td></td>
                    </tr>
                </table>

                <div style="width:100%" align="left"><b>Actual Quantity</b></div>
                <div style="width:100%">
                    <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                        <thead>
                            <tr>
                                <th width="130">Color/Size</th>
                                <th width="100">Color Qnty</th>
                        <?
                        foreach ($sizeArr_pend as $val) {
                            ?>
                                    <th width="80"><? echo $sizeArr[$val]; ?></th>
                            <?
                        }
                        ?>
                                <th width="80">Total</th>
                                <th width="">Balance</th>
                            </tr>
                        </thead>
                    </table>
                </div>

                <div style="width:100%; max-height:200px; overflow-y:scroll">
                    <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                            <?
                            $i = 0;
                            $grandTotal = 0;
                            $noColor = count($poColorArr);
                            // for($k=0;$k<$noColor;$k++)
                            foreach ($color_id_array as $color_id => $totalcolorqnty) {
                                $i++;
                                if ($i % 2 == 0)
                                    $bgcolor = "#EFEFEF";
                                else
                                    $bgcolor = "#FFFFFF";

                                /* if($type==3)
                                  {
                                  //echo "select sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown where po_break_down_id in ($po_id) and country_id in($country_name_arr) and color_number_id='".$poColorArr[$k]."'";
                                  $totalcolorqnty = return_field_value("sum(order_quantity) as order_quantity","wo_po_color_size_breakdown","po_break_down_id in ($po_id) and country_id in($country_name_arr) and color_number_id='".$color_id."'","order_quantity");
                                  }
                                  else
                                  {
                                  $totalcolorqnty = return_field_value("sum(order_quantity) as order_quantity","wo_po_color_size_breakdown","po_break_down_id in ($po_id) and color_number_id='".$color_id."'","plan_cut_qnty");
                                  } */
                                ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                <td width="130"><? echo $colorArr[$color_id]; ?></td>
                                <td width="100" align="right"><? echo $totalcolorqnty; //$ColorvalueArr[$k];//[$poColorArr[$k].$poSizeArr[$j]];   ?></td>
        <? foreach ($sizeArr_pend as $size_id) { ?>
                                    <td width="80" align="right">&nbsp;<? echo $ColorSizeArr[$color_id . $size_id]; ?></td>
            <?
            $row_total_color_qnty += $ColorSizeArr[$color_id . $size_id];
            $col_total_size_qnty[$size_id] += $ColorSizeArr[$color_id . $size_id];
        }
        ?>
                                <td width="80" align="right"><? echo $row_total_color_qnty; ?></td>
                                <td width="" align="right"><? echo $balance = $totalcolorqnty - $row_total_color_qnty; ?></td>
                            </tr>
                                    <?
                                    $row_total_color_qnty = 0;
                                }
                                ?>
                        <tfoot>
                            <tr>
                                <th colspan="2">Total</th>
    <?
    foreach ($sizeArr_pend as $val) {
        $grandTotal += $col_total_size_qnty[$val];
        ?>
                                    <th width="80" align="right"><? echo $col_total_size_qnty[$val]; ?></th>
                        <? } ?>
                                <th><? echo $grandTotal; ?></th>
                                <th><? //echo $grandTotal;  ?></th>
                            </tr>
                        </tfoot>

                    </table>
                </div>

                <div style="width:100%" align="left"><b>Pending Quantity</b></div>
                <div style="width:100%">
                    <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                        <thead>
                            <tr>
                                <th width="130">Color/Size</th>
                                <?
                                foreach ($sizeArr_pend as $val) {
                                    ?>
                                    <th width="80"><? echo $sizeArr[$val]; ?></th>
                                <?
                            }
                            ?>
                                <th>Total</th>
                            </tr>
                        </thead>
                    </table>
                </div>

                <div style="width:100%; max-height:200px; overflow-y:scroll">
                    <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
    <?
    $i = 0;
    $grandTotal = 0;
    $row_total_color_qnty = 0;
    $col_total_size_qnty = array();
    $noColor = count($poColorArr);
    foreach ($color_id_array as $color_id => $totalcolorqnty) {
        $i++;
        if ($i % 2 == 0)
            $bgcolor = "#EFEFEF";
        else
            $bgcolor = "#FFFFFF";
        ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_2<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_2<? echo $i; ?>">
                                <td width="130"><? echo $colorArr[$color_id]; ?></td>
            <? foreach ($sizeArr_pend as $size_id) { ?>
                                    <td width="80" align="right">&nbsp;<?
                $balance_qnty = $qnty_array[$color_id][$size_id] - $ColorSizeArr[$color_id . $size_id];
                echo $balance_qnty;
                ?></td>
                <?
                $row_total_color_qnty += $balance_qnty;
                $col_total_size_qnty[$size_id] += $balance_qnty;
            }
            ?>
                                <td align="right"><? echo $row_total_color_qnty; ?></td>
                            </tr>
            <?
            $row_total_color_qnty = 0;
        }
        ?>
                        <tfoot>
                            <tr>
                                <th>Total</th>
        <?
        foreach ($sizeArr_pend as $val) {
            $grandTotal += $col_total_size_qnty[$val];
            ?>
                                    <th width="80" align="right"><? echo $col_total_size_qnty[$val]; ?></th>
        <? } ?>
                                <th><? echo $grandTotal; ?></th>
                            </tr>
                        </tfoot>

                    </table>
                </div>
            </fieldset>
        </div>
        <?
        exit();
    }

    if ($action == "finish_completed_popup_prev") {
        echo load_html_head_contents("Finish Complete Details", "../../../../", 1, 1, $unicode, '', '');

        $expData = explode('_', $job_number);
        $job_number = $expData[0];
        $po_id = $expData[1];
        $tna_integrated = $expData[2];
        $actual_finish_date = $expData[3];

        $colorArr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
        $sizeArr = return_library_array("select id, size_name from  lib_size", 'id', 'size_name');
        $country_id_arr = array_unique(explode(",", $country));
        $country_name_arr = "";
        foreach ($country_id_arr as $row_id) {
            if ($country_name_arr == "")
                $country_name_arr = $row_id;
            else
                $country_name_arr .= "," . $row_id;
        }//echo $country_name_arr;
        if ($type == 3) {
            $sql = "select  c.size_number_id,c.color_number_id,sum(b.production_qnty) as production_qnty, c.order_quantity as color_qnty
		from pro_garments_production_mst a, wo_po_color_size_breakdown c left join pro_garments_production_dtls b on b.color_size_break_down_id=c.id
		where a.id=b.mst_id  and a.po_break_down_id in ($po_id) and c.country_id in($country_name_arr) and a.production_type=8 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by c.color_number_id,c.size_number_id, c.order_quantity";
        } else {
            $sql = "select  c.size_number_id,c.color_number_id,sum(b.production_qnty) as production_qnty, c.order_quantity as color_qnty
		from pro_garments_production_mst a, wo_po_color_size_breakdown c left join pro_garments_production_dtls b on b.color_size_break_down_id=c.id
		where a.id=b.mst_id  and a.po_break_down_id in ($po_id) and a.production_type=8 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by c.color_number_id,c.size_number_id, c.order_quantity";
        }


        //echo $sql;
        $sqlResult = sql_select($sql);
        $poColorArr = $poSizeArr = $ColorSizeArr = array();
        $ColorvalueArr = array();
        foreach ($sqlResult as $row) {
            $index = $row[csf("color_number_id")] . $row[csf("size_number_id")];
            if (!in_array($row[csf("size_number_id")], $poSizeArr))
                $poSizeArr[] = $row[csf("size_number_id")];
            if (!in_array($row[csf("color_number_id")], $poColorArr))
                $poColorArr[] = $row[csf("color_number_id")];
            $ColorvalueArr[$row[csf("color_number_id")]] += $row[csf("color_qnty")];
            $ColorSizeArr[$index] += $row[csf("production_qnty")];
        }
        $noSize = count($poSizeArr);
        $width = 450 + ($noSize * 80);
        $row_total_color_qnty = 0;
        $col_total_size_qnty = array();
        ?>

        <div style="width:100%" align="center">
            <fieldset style="width:<? echo $width; ?>px">
                <table width="600">
    <?
    if ($type == 3) {
        $job_sql = sql_select("select a.id, a.job_no,a.buyer_name,a.company_name,a.style_ref_no,sum(c.order_quantity) as po_quantity from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst  and a.job_no=c.job_no_mst and b.id=c.po_break_down_id and b.id in ($po_id) and c.country_id in($country_name_arr) group by a.id, a.job_no,a.buyer_name,a.company_name,a.style_ref_no");
    } else {
        $job_sql = sql_select("select a.id, a.job_no,a.buyer_name,a.company_name,a.style_ref_no,sum(b.po_quantity) as po_quantity from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in ($po_id) group by a.id, a.job_no,a.buyer_name,a.company_name,a.style_ref_no");
    }
    //$job_sql= sql_select("select a.id, a.job_no,a.buyer_name,a.company_name,a.style_ref_no,sum(b.po_quantity) as po_quantity from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in ($po_id) group by a.id, a.job_no,a.buyer_name,a.company_name,a.style_ref_no");//a.job_no='$job_number' group by a.job_no
    foreach ($job_sql as $row_job)
        ;  // Master Job  table queery ends here
    ?>
                    <tr class="form_caption">
                        <td align="center" colspan="4"><strong>Finish Complete Details</strong></td>
                    </tr>
                    <tr>
                        <td align="right" width="130"> <strong>Job Number</strong> :</td>
                        <td width="200"><? echo $job_number; ?></td>
                        <td align="right"  width="140"><strong>Buyer Name</strong> :</td>
                        <td><? echo $buyer_short_name_arr[$row_job[csf("buyer_name")]]; ?></td>
                    </tr>
                    <tr>
                        <td align="right"><strong>Company Name</strong> :</td>
                        <td><? echo $company_short_name_arr[$row_job[csf("company_name")]]; ?></td>
                        <td align="right"><strong>Style Ref No</strong> : </td>
                        <td><? echo $row_job[csf("style_ref_no")]; ?> </td>
                    </tr>
                    <tr>
                        <td align="right"><strong>Order Qnty</strong> :</td>
                        <td><?
                        $tot_color_qnty += $ColorvalueArr[$poColorArr[$k]];
                        echo $row_job[csf("po_quantity")];
                        ?> </td>
                        <td align="right"><strong><? if ($tna_integrated == 1) echo "TNA Actual Finish Date"; ?></strong> :</td>
                        <td><? if ($tna_integrated == 1) echo $actual_finish_date; ?></td>
                    </tr>
                    <tr>
                        <td colspan="4" height="15"></td>
                    </tr>
                </table>
                <div style="width:100%" align="left"><b>Actual Quantity</b></div>
                <div style="width:100%">
                    <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                        <thead>
                            <tr>
                                <th width="150">Color/Size</th>
                                <th width="100">Color Qnty</th>
    <? foreach ($poSizeArr as $val) { ?>
                                    <th width="80"><? echo $sizeArr[$val]; ?></th>
                        <? } ?>
                                <th width="80">Total</th>
                                <th width="">Balance</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <div style="width:100%; max-height:470px; overflow-y:scroll">
                    <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                                <?
                                $i = 0;
                                $grandTotal = 0;
                                $noColor = count($poColorArr);
                                for ($k = 0; $k < $noColor; $k++) {
                                    $i++;
                                    if ($i % 2 == 0)
                                        $bgcolor = "#EFEFEF";
                                    else
                                        $bgcolor = "#FFFFFF";
                                    ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                <td width="150"><? echo $colorArr[$poColorArr[$k]]; ?></td>
                                <td width="100" align="right"><?
                    echo $ColorvalueArr[$poColorArr[$k]];
                    $tot_color_qnty += $ColorvalueArr[$poColorArr[$k]]; //[$poColorArr[$k].$poSizeArr[$j]];
                    ?></td>
                    <? for ($j = 0; $j < $noSize; $j++) { ?>
                                    <td width="80" align="right"><? echo $ColorSizeArr[$poColorArr[$k] . $poSizeArr[$j]]; ?></td>
                        <?
                        $row_total_color_qnty += $ColorSizeArr[$poColorArr[$k] . $poSizeArr[$j]];
                        $col_total_size_qnty[$poSizeArr[$j]] += $ColorSizeArr[$poColorArr[$k] . $poSizeArr[$j]];
                    }
                    ?>
                                <td width="80" align="right"><? echo $row_total_color_qnty; ?></td>
                                <td width="" align="right"><? echo $balance = $ColorvalueArr[$poColorArr[$k]] - $row_total_color_qnty; ?></td>
                            </tr>
                    <?
                    $row_total_color_qnty = 0;
                }
                ?>
                        <tfoot>
                            <tr>
                                <th></th>
                                <th width="80" align="right"><? echo $tot_color_qnty; ?></th>
                                <?
                                foreach ($poSizeArr as $val) {
                                    $grandTotal += $col_total_size_qnty[$val];
                                    ?>
                                    <th width="80" align="right"><? echo $col_total_size_qnty[$val]; ?></th>
                                <? } ?>
                                <th><? echo $grandTotal; ?></th>
                                <th><? //echo $grandTotal;    ?></th>
                            </tr>
                        </tfoot>

                    </table>
                </div>
                        <?
                        $qnty_array = array();
                        $sizeArr_pend = array();
                        $color_id_array = array();
                        if ($type == 3) {
                            //echo "select color_number_id, size_number_id, sum(order_quantity) as order_quantity from wo_po_color_size_breakdown where po_break_down_id in ($po_id) and country_id in($country_name_arr) group by color_number_id, size_number_id order by color_number_id, size_number_id";
                            $colorData = sql_select("select color_number_id, size_number_id, sum(order_quantity) as order_quantity from wo_po_color_size_breakdown where po_break_down_id in ($po_id) and country_id in($country_name_arr) group by color_number_id, size_number_id order by color_number_id, size_number_id");
                        } else {
                            $colorData = sql_select("select color_number_id, size_number_id, sum(order_quantity) as order_quantity from wo_po_color_size_breakdown where po_break_down_id in ($po_id) group by color_number_id, size_number_id order by color_number_id, size_number_id");
                            ///echo "select color_number_id, size_number_id, sum(order_quantity) as order_quantity from wo_po_color_size_breakdown where po_break_down_id in ($po_id) group by color_number_id, size_number_id order by color_number_id, size_number_id";
                        }

                        foreach ($colorData as $row) {
                            $qnty_array[$row[csf('color_number_id')]][$row[csf('size_number_id')]] += $row[csf('order_quantity')];
                            $sizeArr_pend[$row[csf('size_number_id')]] = $row[csf('size_number_id')];
                            $color_id_array[$row[csf('color_number_id')]] = $row[csf('color_number_id')];
                        }
                        ?>
                <div style="width:100%" align="left"><b>Pending Quantity</b></div>
                <div style="width:100%">
                    <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                        <thead>
                            <tr>
                                <th width="130">Color/Size</th>
                            <?
                            foreach ($sizeArr_pend as $val) {
                                ?>
                                    <th width="80"><? echo $sizeArr[$val]; ?></th>
                        <? } ?>
                                <th>Total</th>
                            </tr>
                        </thead>
                    </table>
                </div>

                <div style="width:100%; max-height:200px; overflow-y:scroll">
                    <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                                <?
                                $i = 0;
                                $grandTotal = 0;
                                $row_total_color_qnty = 0;
                                $col_total_size_qnty = array();
                                $noColor = count($poColorArr);
                                foreach ($color_id_array as $color_id) {
                                    $i++;
                                    if ($i % 2 == 0)
                                        $bgcolor = "#EFEFEF";
                                    else
                                        $bgcolor = "#FFFFFF";
                                    ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_2<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_2<? echo $i; ?>">
                                <td width="130"><? echo $colorArr[$color_id]; ?></td>
            <? foreach ($sizeArr_pend as $size_id) { ?>
                                    <td width="80" align="right">&nbsp;<?
                $balance_qnty = $qnty_array[$color_id][$size_id] - $ColorSizeArr[$color_id . $size_id];
                echo $balance_qnty;
                ?></td>
                <?
                $row_total_color_qnty += $balance_qnty;
                $col_total_size_qnty[$size_id] += $balance_qnty;
            }
            ?>
                                <td align="right"><? echo $row_total_color_qnty; ?></td>
                            </tr>
            <?
            $row_total_color_qnty = 0;
        }
        ?>
                        <tfoot>
                            <tr>
                                <th>Total</th>
    <?
    foreach ($sizeArr_pend as $val) {
        $grandTotal += $col_total_size_qnty[$val];
        ?>
                                    <th width="80" align="right"><? echo $col_total_size_qnty[$val]; ?></th>
                    <? } ?>
                                <th><? echo $grandTotal; ?></th>
                            </tr>
                        </tfoot>

                    </table>
                </div>
            </fieldset>
        </div>
    <?
    exit();
}

if ($action == "buyer_inspection_popup") {
    echo load_html_head_contents("Buyer Inspection Details", "../../../../", 1, 1, $unicode, '', '');

    $expData = explode('_', $job_number);
    $job_number = $expData[0];
    $po_id = $expData[1];
    $tna_integrated = $expData[2];
    $actual_finish_date = $expData[3];

    $country_id_arr = array_unique(explode(",", $country));
    $country_name_arr = "";
    foreach ($country_id_arr as $row_id) {
        if ($country_name_arr == "")
            $country_name_arr = $row_id;
        else
            $country_name_arr .= "," . $row_id;
    }
    ?>

        <div style="width:100%" align="center">
            <fieldset style="width:700px">
                <table width="600">
    <?
    $job_sql = sql_select("select job_no,buyer_name,company_name,style_ref_no from wo_po_details_master where job_no='$job_number'");
    foreach ($job_sql as $row_job)
        ;  // Master Job  table queery ends here
    ?>
                    <tr class="form_caption">
                        <td align="center" colspan="4"><strong>Buyer Inspection Details</strong></td>
                    </tr>
                    <tr>
                        <td align="right" width="150"> <strong>Job Number</strong> :</td>
                        <td width="200"><? echo $job_number; ?></td>
                        <td align="right"  width="130"><strong>Buyer Name</strong> :</td>
                        <td><? echo $buyer_short_name_arr[$row_job[csf("buyer_name")]]; ?></td>
                    </tr>
                    <tr>
                        <td align="right"><strong>Company Name</strong> :</td>
                        <td><? echo $company_short_name_arr[$row_job[csf("company_name")]]; ?></td>
                        <td align="right"><strong>Style Ref No</strong> : </td>
                        <td><? echo $row_job[csf("style_ref_no")]; ?> </td>
                    </tr>
                        <?
                        if ($tna_integrated == 1) {
                            echo '<tr><td align="right"><strong>TNA Actual Finish Date</strong> :</td><td>' . $actual_finish_date . '</td></tr>';
                        }
                        ?>
                    <tr>
                        <td colspan="4" height="15"></td>
                    </tr>
                </table>
                <div style="width:100%">
                    <p><b>QC Passed</b></p>
                    <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                        <thead>
                            <tr>
                                <th width="35">SL</th>
                                <th width="120">Inspection Date</th>
                                <th width="120">Inspection Qnty</th>
                                <th width="120">Inspection Status</th>
                                <th width="">Remarks</th>
                            </tr>
                        </thead>
                    </table>
                </div>

                <div style="width:100%; max-height:150px; overflow-y:scroll">
                    <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
    <?
    $i = 0;
    if ($type == 3) {
        $sql = "select inspection_date,inspection_status,inspection_cause,comments,sum(inspection_qnty) as inspection_qnty
						from pro_buyer_inspection
						where po_break_down_id in ($po_id) and country_id in($country_name_arr) and status_active=1 and is_deleted=0 group by inspection_date,inspection_status,inspection_cause,comments";
    } else {
        $sql = "select inspection_date,inspection_status,inspection_cause,comments,sum(inspection_qnty) as inspection_qnty
						from pro_buyer_inspection
						where po_break_down_id in ($po_id) and status_active=1 and is_deleted=0 group by inspection_date,inspection_status,inspection_cause,comments";
    }
    //echo $sql;
    $fabric_sql = sql_select($sql);
    $total_qnty = $total_pass_qnty = 0;
    foreach ($fabric_sql as $row) {

        if ($i % 2 == 0)
            $bgcolor = "#EFEFEF";
        else
            $bgcolor = "#FFFFFF";
        if ($row[csf("inspection_status")] == 1) {
            $i++;
            ?>
                                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_p<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_p<? echo $i; ?>">
                                    <td width="35" align="center"><? echo $i; ?></td>
                                    <td width="120" align="center"><? echo change_date_format($row[csf("inspection_date")]); ?></td>
                                    <td width="120" align="right"><? echo $row[csf("inspection_qnty")]; ?></td>
                                    <td width="120" align="center"><p><? echo $inspection_status[$row[csf("inspection_status")]]; ?></p></td>
                                    <td><p><? echo $row[csf("comments")]; ?>&nbsp;</p></td>
                                </tr>
                                <?
                                $total_pass_qnty += $row[csf("inspection_qnty")];
                            }
                        }
                        ?>
                        <tfoot>
                            <tr>
                                <th></th><th>Total</th>
                                <th><? echo $total_pass_qnty; ?></th>
                                <th></th><th></th>
                            </tr>
                        </tfoot>

                    </table>
                </div>

                <br />

                <div style="width:100%">
                    <p><b>QC Failed Or Re-Checked</b></p>
                    <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                        <thead>
                            <tr>
                                <th width="35">SL</th>
                                <th width="120">Inspection Date</th>
                                <th width="120">Inspection Qnty</th>
                                <th width="120">Inspection Status</th>
                                <th width="">Remarks</th>
                            </tr>
                        </thead>
                    </table>
                </div>

                <div style="width:100%; max-height:150px; overflow-y:scroll">
                    <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
        <?
        $i = 0;
        foreach ($fabric_sql as $row) {

            if ($i % 2 == 0)
                $bgcolor = "#EFEFEF";
            else
                $bgcolor = "#FFFFFF";
            if ($row[csf("inspection_status")] != 1) {
                $i++;
                ?>
                                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_r<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_r<? echo $i; ?>">
                                    <td width="35" align="center"><? echo $i; ?></td>
                                    <td width="120" align="center"><? echo change_date_format($row[csf("inspection_date")]); ?></td>
                                    <td width="120" align="right"><? echo $row[csf("inspection_qnty")]; ?></td>
                                    <td width="120" align="center"><p><? echo $inspection_status[$row[csf("inspection_status")]]; ?></p></td>
                                    <td><p><? echo $row[csf("comments")]; ?>&nbsp;</p></td>
                                </tr>
            <?
            $total_qnty += $row[csf("inspection_qnty")];
        }
    }
    ?>
                        <tfoot>
                            <tr>
                                <th></th><th>Total</th>
                                <th><? echo $total_qnty; ?></th>
                                <th></th><th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </fieldset>
        </div>
    <?
    exit();
}

if ($action == "ex_factory_popup") {
    echo load_html_head_contents("Ship Quantity Details", "../../../../", 1, 1, $unicode, '', '');

    $expData = explode('_', $job_number);
    $job_number = $expData[0];
    $po_id = $expData[1];
    $tna_integrated = $expData[2];
    $actual_finish_date = $expData[3];
    //echo $type;
    $country_id_arr = array_unique(explode(",", $country));
    $country_name_arr = "";
    foreach ($country_id_arr as $row_id) {
        if ($country_name_arr == "")
            $country_name_arr = $row_id;
        else
            $country_name_arr .= "," . $row_id;
    } //echo $country_name_arr;
    ?>

        <div style="width:100%" align="center">
            <fieldset style="width:700px">
                <table width="600">
    <?
    $job_sql = sql_select("select job_no,buyer_name,company_name,style_ref_no from wo_po_details_master where job_no='$job_number'");
    foreach ($job_sql as $row_job)
        ;  // Master Job  table queery ends here
    ?>
                    <tr class="form_caption">
                        <td align="center" colspan="4"><strong>Shipment Details</strong></td>
                    </tr>
                    <tr>
                        <td align="right" width="150"> <strong>Job Number</strong> :</td>
                        <td width="200"><? echo $job_number; ?></td>
                        <td align="right"  width="130"><strong>Buyer Name</strong> :</td>
                        <td><? echo $buyer_short_name_arr[$row_job[csf("buyer_name")]]; ?></td>
                    </tr>
                    <tr>
                        <td align="right"><strong>Company Name</strong> :</td>
                        <td><? echo $company_short_name_arr[$row_job[csf("company_name")]]; ?></td>
                        <td align="right"><strong>Style Ref No</strong> : </td>
                        <td><? echo $row_job[csf("style_ref_no")]; ?> </td>
                    </tr>
                        <?
                        if ($tna_integrated == 1) {
                            echo '<tr><td align="right"><strong>TNA Actual Finish Date</strong> :</td><td>' . $actual_finish_date . '</td></tr>';
                        }
                        ?>
                    <tr>
                        <td colspan="4" height="15"></td>
                    </tr>
                </table>

                <div style="width:100%">
                    <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                        <thead>
                            <tr>
                                <th width="35">SL</th>
                                <th width="120">Ex-Fac. Date</th>
                                <th width="120">Ex-Fac. Qnty</th>
                                <th width="120">Challan No</th>
                                <th width="">Remarks</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <div style="width:100%; max-height:270px; overflow-y:scroll">
                    <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
    <?
    $i = 0;
    if ($type == 3) {
        $sql = "select country_id,max(challan_no) as challan_no,max(remarks) as remarks,ex_factory_date,sum(ex_factory_qnty) as ex_factory_qnty
						from pro_ex_factory_mst
						where po_break_down_id in ($po_id) and country_id in($country_name_arr) and status_active=1 and is_deleted=0 group by country_id,ex_factory_date";
    } else {
        $sql = "select country_id,max(challan_no) as challan_no,ex_factory_date,sum(ex_factory_qnty) as ex_factory_qnty,max(remarks) as remarks
						from pro_ex_factory_mst
						where po_break_down_id in ($po_id) and status_active=1 and is_deleted=0 group by ex_factory_date,country_id";
    }

    //echo $sql;
    $fabric_sql = sql_select($sql);
    $total_qnty = 0;
    foreach ($fabric_sql as $row) {
        $i++;
        if ($i % 2 == 0)
            $bgcolor = "#EFEFEF";
        else
            $bgcolor = "#FFFFFF";
        ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                <td width="35"><? echo $i; ?></td>
                                <td width="120"><? echo change_date_format($row[csf("ex_factory_date")]); ?></td>
                                <td width="120" align="right"><? echo $row[csf("ex_factory_qnty")]; ?></td>
                                <td width="120"><p><? echo $row[csf("challan_no")]; ?>&nbsp;</p></td>
                                <td><p><? echo $row[csf("remarks")]; ?>&nbsp;</p></td>
                            </tr>
        <?
        $total_qnty += $row[csf("ex_factory_qnty")];
    }
    ?>
                        <tfoot>
                            <tr>
                                <th></th><th>Total</th>
                                <th><? echo $total_qnty; ?></th>
                                <th></th><th></th>
                            </tr>
                        </tfoot>

                    </table>
                </div>
            </fieldset>
        </div>
    <?
    exit();
}



if ($action == "actual_shipment_popup") {
    echo load_html_head_contents("Actual Shipment Details", "../../../../", 1, 1, $unicode, '', '');

    $expData = explode('_', $job_number);
    $job_number = $expData[0];
    $po_id = $expData[1];
    //echo $type;
    $country_id_arr = array_unique(explode(",", $country));
    $country_name_arr = "";
    foreach ($country_id_arr as $row_id) {
        if ($country_name_arr == "")
            $country_name_arr = $row_id;
        else
            $country_name_arr .= "," . $row_id;
    } //echo $country_name_arr;
    ?>

        <div style="width:100%" align="center">
            <fieldset style="width:800px">
                <table width="600">
    <?
    $job_sql = sql_select("select job_no,buyer_name,company_name,style_ref_no from wo_po_details_master where job_no='$job_number'");
    foreach ($job_sql as $row_job)
        ;
    ?>
                    <tr class="form_caption">
                        <td align="center" colspan="4"><strong>Actual Shipment Details</strong></td>
                    </tr>
                    <tr>
                        <td align="right" width="130"> <strong>Job Number</strong> :</td>
                        <td width="200"><? echo $job_number; ?></td>
                        <td align="right"  width="130"><strong>Buyer Name</strong> :</td>
                        <td><? echo $buyer_short_name_arr[$row_job[csf("buyer_name")]]; ?></td>
                    </tr>
                    <tr>
                        <td align="right"><strong>Company Name</strong> :</td>
                        <td><? echo $company_short_name_arr[$row_job[csf("company_name")]]; ?></td>
                        <td align="right"><strong>Style Ref No</strong> : </td>
                        <td><? echo $row_job[csf("style_ref_no")]; ?> </td>
                    </tr>
                    <tr>
                        <td colspan="4" height="15"></td>
                    </tr>
                </table>

                <div style="width:100%">
                    <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                        <thead>
                            <tr>
                                <th width="35">SL</th>
                                <th width="100">Order Number</th>
                                <th width="100">Order Qty</th>
                                <th width="100">Shipment date</th>
                                <th width="100">Invoice No</th>
                                <th width="100">Actual Ship date</th>
                                <th width="100">Invoice Qty</th>
                                <th width="">Invoice Value</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <div style="width:100%; max-height:270px; overflow-y:scroll">
                    <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
    <?
    $i = 0;
    if ($type == 3) {
        $sql = "select a.po_breakdown_id,b.pub_shipment_date,c.invoice_no,a.current_invoice_qnty,a.current_invoice_value, c.actual_shipment_date
					from com_export_invoice_ship_dtls a, wo_po_break_down b,com_export_invoice_ship_mst c
					where a.po_breakdown_id in ($po_id) and c.country_id in($country_name_arr) and a.po_breakdown_id=b.id and a.mst_id=c.id and c.is_deleted=0 and c.status_active=1 and a.current_invoice_qnty>0";
    } else {
        $sql = "select a.po_breakdown_id,b.pub_shipment_date,c.invoice_no,a.current_invoice_qnty,a.current_invoice_value, c.actual_shipment_date
					from com_export_invoice_ship_dtls a, wo_po_break_down b,com_export_invoice_ship_mst c
					where a.po_breakdown_id in ($po_id) and a.po_breakdown_id=b.id and a.mst_id=c.id and c.is_deleted=0 and c.status_active=1 and a.current_invoice_qnty>0";
    }

    //echo $sql;die;

    $fabric_sql = sql_select($sql);
    $total_qnty = $total_value = 0;
    foreach ($fabric_sql as $row) {
        $i++;
        if ($i % 2 == 0)
            $bgcolor = "#EFEFEF";
        else
            $bgcolor = "#FFFFFF";
        ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                <td width="35"><? echo $i; ?></td>
            <?
            if ($type == 3) {
                $poSQL = sql_select("select b.po_number,c.order_quantity as order_qnty from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and  a.job_no=c.job_no_mst and b.id=c.po_break_down_id and c.country_id in($country_name_arr)  and b.id=" . $row[csf("po_breakdown_id")]);
            } else {
                $poSQL = sql_select("select b.po_number,b.po_quantity as order_qnty from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id=" . $row[csf("po_breakdown_id")]);
            }

            foreach ($poSQL as $res)
                ;
            ?>
                                <td width="100"><? echo $res[csf("po_number")]; ?></td>
                                <td width="100" align="right"><? echo $res[csf("order_qnty")]; ?></td>
                                <td width="100"><? echo change_date_format($row[csf("pub_shipment_date")]); ?></td>
                                <td width="100"><? echo $row[csf("invoice_no")]; ?></td>
                                <td width="100"><? echo change_date_format($row[csf("actual_shipment_date")]); ?>&nbsp;</td>
                                <td width="100" align="right"><? echo $row[csf("current_invoice_qnty")]; ?></td>
                                <td align="right"><? echo $row[csf("current_invoice_value")]; ?></td>
                            </tr>
        <?
        $total_qnty += $row[csf("current_invoice_qnty")];
        $total_value += $row[csf("current_invoice_value")];
    }
    ?>
                        <tfoot>
                            <tr>
                                <th colspan="5"></th><th>Total</th>
                                <th><? echo $total_qnty; ?></th>
                                <th><? echo $total_value; ?></th>
                            </tr>
                        </tfoot>

                    </table>
                </div>
            </fieldset>
        </div>
                        <?
                        exit();
                    }


if ($action == "submit_date_popup") {
	echo load_html_head_contents("Submit Date Details", "../../../../", 1, 1, $unicode, '', '');

	$expData = explode('_', $job_number);
	$job_number = $expData[0];
	$po_id = $expData[1];
	?>

<div style="width:100%" align="center">
<fieldset style="width:650px">
<div class="form_caption" align="center">
<strong>Document Submission Details</strong>
</div><br />
<div style="width:100%">
<table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
	<thead>
		<tr>
			<th width="35">SL</th>
			<th width="150">Invoice No</th>
			<th width="150">Bill No</th>
			<th width="150">Submission Date</th>
			<th width="">Submission Type</th>
		</tr>
	</thead>
</table>
</div>
<div style="width:100%; max-height:270px; overflow-y:scroll">
<table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
<?
$i = 0;

$sql = "select d.submit_date,d.submit_type,a.invoice_no,d.bank_ref_no
	from com_export_invoice_ship_mst a, com_export_invoice_ship_dtls b, com_export_doc_submission_invo c, com_export_doc_submission_mst d
	where b.po_breakdown_id in ($po_id) and a.id=b.mst_id and a.id=c.invoice_id and c.doc_submission_mst_id=d.id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and b.current_invoice_qnty>0";
//echo $sql;die ;
$sqlRes = sql_select($sql);
$total_qnty = $total_value = 0;
foreach ($sqlRes as $row) {
$i++;
if ($i % 2 == 0)
$bgcolor = "#EFEFEF";
else
$bgcolor = "#FFFFFF";
?>
		<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
			<td width="35"><? echo $i; ?></td>
			<td width="150"><? echo $row[csf("invoice_no")]; ?></td>
			<td width="150"><? echo $row[csf("bank_ref_no")]; ?></td>
			<td width="150"><? echo change_date_format($row[csf("submit_date")]); ?></td>
			<td><? echo $submission_type[$row[csf("submit_type")]]; ?></td>
		</tr>
	<? } ?>
</table>
</div>
</fieldset>
</div>
	<?
	exit();
}


if ($action == "proceed_realize_popup") {
	echo load_html_head_contents("Proceed Realize Details", "../../../../", 1, 1, $unicode, '', '');

	$expData = explode('_', $job_number);
	$job_number = $expData[0];
	$po_id = $expData[1];
	?>

<div style="width:100%" align="center">
<fieldset style="width:680px">
<div class="form_caption" align="center">
<strong>Proceed Realization Details</strong>
</div><br />
<div style="width:100%">
<table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
	<thead>
		<tr>
			<th width="35">SL</th>
			<th width="100">Bill No</th>
			<th width="250">Invoice No</th>
			<th width="100">Realized Amnt</th>
			<th width="100">Short Realized Amnt</th>
			<th>Realized Date</th>
		</tr>
	</thead>
</table>
</div>
<div style="width:100%; max-height:270px; overflow-y:scroll">
<table cellpadding="0" width="100%" cellspacing="0" border="1" rules="all" class="rpt_table">
<?
$i = 1;
$tot_realized_amnt = 0;
$tot_short_realized_amnt = 0;
$sql = "select a.bank_ref_no, a.id from com_export_doc_submission_mst a, com_export_doc_submission_invo b, com_sales_contract_order_info c where a.id=b.doc_submission_mst_id and b.lc_sc_id=c.com_sales_contract_id and b.is_lc=2 and c.wo_po_break_down_id in ($po_id) AND a.status_active =1 AND b.is_deleted=0 AND b.status_active =1 AND c.is_deleted=0 AND c.status_active =1 group by a.id, a.bank_ref_no order by a.bank_ref_no";
//echo $sql;
$result = sql_select($sql);
foreach ($result as $row) {
$invoice_no = "";
$sql_inv = "select a.invoice_no from com_export_invoice_ship_mst a, com_export_doc_submission_invo b where a.id=b.invoice_id and b.doc_submission_mst_id='" . $row[csf("id")] . "' AND a.is_deleted=0  AND a.status_active =1 AND b.is_deleted=0 AND b.status_active =1 group by a.id, a.invoice_no order by a.invoice_no";
$res_inv = sql_select($sql_inv);
foreach ($res_inv as $row_inv) {
if ($invoice_no == "")
$invoice_no = $row_inv[csf("invoice_no")];
else
$invoice_no .= ", " . $row_inv[csf("invoice_no")];
}
$sql_real = "select a.received_date,
		sum(case when b.type=1 then b.document_currency end) as realized_value,
		sum(case when b.type=0 then b.document_currency end) as short_realized_value
		from com_export_proceed_realization a, com_export_proceed_rlzn_dtls b where a.id=b.mst_id and a.invoice_bill_id='" . $row[csf("id")] . "' and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.id, a.received_date order by a.received_date";
$res_real = sql_select($sql_real);
foreach ($res_real as $row_real) {
if ($i % 2 == 0)
$bgcolor = "#EFEFEF";
else
$bgcolor = "#FFFFFF";
?>
			<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_s<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_s<? echo $i; ?>">
				<td width="35"><? echo $i; ?></td>
				<td width="100"><? echo $row[csf("bank_ref_no")]; ?></td>
				<td width="250"><p><? echo $invoice_no; ?></p></td>
				<td width="100" align="right"><?
			echo number_format($row_real[csf("realized_value")], 2);
			$tot_realized_amnt += $row_real[csf("realized_value")];
			?></td>
				<td width="100" align="right"><?
			echo number_format($row_real[csf("short_realized_value")], 2);
			$tot_short_realized_amnt += $row_real[csf("short_realized_value")];
			?></td>
				<td><? echo change_date_format($row_real[csf("received_date")]); ?></td>
			</tr>
					<?
					$i++;
				}
			}
			$i = 1;
			$sql2 = "select a.bank_ref_no, a.id from com_export_doc_submission_mst a, com_export_doc_submission_invo b, com_export_lc_order_info c where a.id=b.doc_submission_mst_id and b.lc_sc_id=c.com_export_lc_id and b.is_lc=1 and c.wo_po_break_down_id in ($po_id) AND a.is_deleted=0  AND a.status_active=1 AND b.is_deleted=0 AND b.status_active=1 AND c.is_deleted=0 AND c.status_active=1 group by a.id,a.bank_ref_no order by a.bank_ref_no";
			$result2 = sql_select($sql2);
			foreach ($result2 as $row2) {
				if ($i % 2 == 0)
					$bgcolor = "#EFEFEF";
				else
					$bgcolor = "#FFFFFF";

				$invoice_no = "";
				$sql_inv = "select a.invoice_no from com_export_invoice_ship_mst a, com_export_doc_submission_invo b where a.id=b.invoice_id and b.doc_submission_mst_id='" . $row2[csf("id")] . "' AND a.is_deleted=0 AND a.status_active=1 AND b.is_deleted=0 AND b.status_active =1 group by a.id, a.invoice_no order by a.invoice_no";
				$res_inv = sql_select($sql_inv);
				foreach ($res_inv as $row_inv) {
					if ($invoice_no == "")
						$invoice_no = $row_inv[csf("invoice_no")];
					else
						$invoice_no .= ", " . $row_inv[csf("invoice_no")];
				}
				$sql_real = "select a.received_date,
	sum(case when b.type=1 then b.document_currency end) as realized_value,
	sum(case when b.type=0 then b.document_currency end) as short_realized_value
	from com_export_proceed_realization a, com_export_proceed_rlzn_dtls b where a.id=b.mst_id and a.invoice_bill_id='" . $row2[csf("id")] . "' and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.id, a.received_date order by a.received_date";
				$res_real = sql_select($sql_real);
				foreach ($res_real as $row_real) {
					if ($i % 2 == 0)
						$bgcolor = "#EFEFEF";
					else
						$bgcolor = "#FFFFFF";
					?>
			<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_l<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_l<? echo $i; ?>">
				<td width="35"><? echo $i; ?></td>
				<td width="100"><? echo $row2[csf("bank_ref_no")]; ?></td>
				<td width="250"><p><? echo $invoice_no; ?></p></td>
				<td width="100" align="right"><?
					echo number_format($row_real[csf("realized_value")], 2);
					$tot_realized_amnt += $row_real[csf("realized_value")];
					?></td>
				<td width="100" align="right"><?
					echo number_format($row_real[csf("short_realized_value")], 2);
					$tot_short_realized_amnt += $row_real[csf("short_realized_value")];
					?></td>
				<td><? echo change_date_format($row_real[csf("received_date")]); ?></td>
			</tr>
<?
$i++;
}
}
?>
	<tfoot>
	<th colspan="3">Total</th>
	<th><? echo number_format($tot_realized_amnt, 2); ?></th>
	<th><? echo number_format($tot_short_realized_amnt, 2); ?></th>
	<th>&nbsp;</th>
	</tfoot>
</table>
</div>
</fieldset>
</div>
	<?
	exit();
}

if ($action == "order_number_popup") {
	echo load_html_head_contents("Order Number Details", "../../../../", 1, 1, $unicode, '', '');

	$expData = explode('_', $job_number);
	$job_number = $expData[0];
	$po_id = $expData[1];
	?>

<div style="width:100%" align="center">
<fieldset style="width:820px">
<div class="form_caption" align="center"><strong>Order Details</strong></div><br />
<div style="width:100%">
<table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
	<thead>
		<tr>
			<th width="35">SL</th>
			<th width="120">Order Number</th>
			<th width="90">PO Recv. Date</th>
			<th width="90">Pub Ship. Date</th>
			<th width="70">Lead Time</th>
			<th width="100">PO Quantity</th>
			<th width="100">Plan Cut</th>
			<th width="80">Unit Prince</th>
			<th width="">Total Price</th>
		</tr>
	</thead>
</table>
</div>
<div style="width:100%; max-height:270px; overflow-y:scroll">
<table cellpadding="0" width="100%" cellspacing="0" border="1" rules="all" class="rpt_table">
<?
$i = 1;
$job_po_qnty = 0;
$job_plan_qnty = 0;
$job_total_price = 0;
if ($db_type == 0)
$select_date_diff = "DATEDIFF(pub_shipment_date,po_received_date)";
else
$select_date_diff = "trunc(pub_shipment_date-po_received_date)";
$jobs_sql = sql_select("select id, job_no_mst, pub_shipment_date, po_received_date, po_number, po_quantity, unit_price, plan_cut, po_total_price, $select_date_diff as lead_time from wo_po_break_down where job_no_mst='$job_number' and id in ($po_id)");

foreach ($jobs_sql as $row2) {
if ($i % 2 == 0)
$bgcolor = "#EFEFEF";
else
$bgcolor = "#FFFFFF";
?>
		<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_l<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_l<? echo $i; ?>">
			<td width="35"><? echo $i; ?></td>
			<td width="120"><p><? echo $row2[csf("po_number")]; ?></p></td>
			<td width="90" align="center"><? echo change_date_format($row2[csf("po_received_date")]); ?></td>
			<td width="90" align="center"><? echo change_date_format($row2[csf("pub_shipment_date")]); ?></td>
			<td width="70" align="right"><? echo $row2[csf("lead_time")]; ?>&nbsp;&nbsp;</td>
			<td width="100" align="right"><? echo $row2[csf("po_quantity")]; ?>&nbsp;</td>
			<td width="100" align="right"><? echo $row2[csf("plan_cut")]; ?>&nbsp;</td>
			<td width="80" align="right"><? echo number_format($row2[csf("unit_price")], 2); ?>&nbsp;</td>
			<td align="right"><? echo number_format($row2[csf("po_total_price")], 2); ?>&nbsp;</td>
		</tr>
<?
$job_po_qnty += $row2[csf("po_quantity")];
$job_plan_qnty += $row2[csf("plan_cut")];
$job_total_price += $row2[csf("po_total_price")];
$i++;
}
?>
	<tfoot>
	<th colspan="5">Total</th>
	<th><? echo number_format($job_po_qnty, 2); ?></th>
	<th><? echo number_format($job_plan_qnty, 2); ?></th>
	<th>&nbsp;</th>
	<th><? echo number_format($job_total_price, 2); ?></th>
	</tfoot>
</table>
</div>
</fieldset>
</div>
	<?
	exit();
}

if ($action == "trims_rec_popup") {
    echo load_html_head_contents("Trims Receive Details", "../../../../", 1, 1, $unicode, '', '');
    extract($_REQUEST);
	$po_id=str_replace("'","",$po_id);
    //echo $po_id;STYLE_REF_NO
    //$style_ref_no=return_library_array( "select job_no, style_ref_no from wo_po_details_master", "job_no", "style_ref_no");
    $poDataResult = sql_select("select b.id,a.job_no, a.total_set_qnty as ratio, b.po_quantity,b.unit_price,b.po_number,a.style_ref_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in($po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
    $job_no = ''; $style_ref_no = ''; $po_number = array();
    foreach ($poDataResult as $key => $value) {
        $job_no = $value[csf('job_no')];
        $style_ref_no = $value[csf('style_ref_no')];
        $po_number[$value[csf('po_number')]] = $value[(csf('po_number'))];

    }
    ?>

    <div style="width:100%" align="center">
        <fieldset style="width:1170px">
            <div class="form_caption" align="center"><strong>Trims Details</strong></div><br />
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                    <tr>
                        <th width="80">Job No</th>
                        <td width="80"><? echo $job_no; ?></td>
                        <th width="80">Style Ref</th>
                        <td width="150"><? echo $style_ref_no; ?></td>
                        <th width="80">Order No.</th>
                        <td><? echo implode(', ', $po_number); ?></td>
                    </tr>
                </thead>
            </table><br>
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                    <tr>
                        <th rowspan="2" width="30">SL</th>
                        <th rowspan="2">Trims Name</th>
                        <th rowspan="2" width="60">UOM</th>
                        <th colspan="2">Required [As Pre-Cost/Budget]</th>
                        <th colspan="6">Work Order Info</th>
                        <th colspan="3">Receive Info</th>
                        <th colspan="2">Issue Info</th>
                    </tr>
                    <tr>
                        <th width="70">Quantity</th>
                        <th width="70">Value</th>
                        <th width="70">Quantity</th>
                        <th width="70">Value</th>
                        <th width="70">WO No.</th>
                        <th width="70">Supplier Name</th>
                        <th width="70">WO Date</th>
                        <th width="70">Delivery Date</th>
                        <th width="70">Quantity</th>
                        <th width="70">Value</th>
                        <th width="70">Balance</th>
                        <th width="70">Quantity</th>
                        <th width="87">Balance</th>
                    </tr>
            </thead>
            </table>
        <div style="width:100%; max-height:300px; overflow-y:scroll">
            <table cellpadding="0" width="100%" cellspacing="0" border="1" rules="all" class="rpt_table">
            <?
    		$condition= new condition();
    		if(str_replace("'","",$po_id)!='')
    	 	{
    			$condition->po_id("in($po_id)");
    		}
    		$condition->init();
    		$trims= new trims($condition);
    		//echo $trims->getQuery(); die;
    		$trims_req_qty_arr=$trims->getQtyArray_by_orderAndItemid();
    		$trims_req_amt_arr=$trims->getAmountArray_by_orderAndItemid();
            $i = 1;
            $tot_req_qnty = 0;
            $tot_recv_qnty = 0;
            $tot_iss_qnty = 0;
            $tot_recv_bl_qnty = 0;
            $tot_iss_bl_qnty = 0;
			$item_group_sql=sql_select("select id, item_name, conversion_factor from lib_item_group");
			$trim_group=$trim_conversion_factor=array();
			foreach($item_group_sql as $row)
			{
				$trim_group[$row[csf("id")]]=$row[csf("item_name")];
				$trim_conversion_factor[$row[csf("id")]]=$row[csf("conversion_factor")];
			}
			unset($item_group_sql);
            //$trim_group = return_library_array("select id, item_name from lib_item_group", 'id', 'item_name');
            $costing_per_id_library = return_library_array("select job_no, costing_per from wo_pre_cost_mst", "job_no", "costing_per");
            $supplier_library = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');

            $poDataResult = sql_select("select b.id,a.job_no, a.total_set_qnty as ratio, b.po_quantity,b.unit_price,b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in($po_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
            foreach ($poDataResult as $row) {
                $poDataArr[$row[csf('id')]]['po_number'] = $row[csf('po_number')];
                $poDataArr[$row[csf('id')]]['job_no'] = $row[csf('job_no')];
                $poDataArr[$row[csf('id')]]['ratio'] = $row[csf('ratio')];
                $poDataArr[$row[csf('id')]]['po_quantity'] = $row[csf('po_quantity')];
                $poDataArr[$row[csf('id')]]['unit_price'] = $row[csf('unit_price')];
            }


            $sql = "select a.booking_date,a.pay_mode,a.supplier_id,a.booking_no,a.job_no,b.wo_qnty,b.rate,b.trim_group,b.po_break_down_id,b.delivery_date,b.amount from wo_booking_mst a, wo_booking_dtls b where  b.po_break_down_id in($po_id) and a.booking_no=b.booking_no and a.booking_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
            $trimsDataResult = sql_select($sql);
            foreach ($trimsDataResult as $row) {
        		if($row[csf('pay_mode')]==1 || $row[csf('pay_mode')]==2) //Import/Credit
        		{
        			$com_supplier=$supplier_library[$row[csf('supplier_id')]];
        		}
        		else
        		{
        			$com_supplier=$company_short_name_arr[$row[csf('supplier_id')]];
        		}
                $trimsDataArr[$row[csf('po_break_down_id')]][$row[csf('trim_group')]]['delivery_date'] = $row[csf('delivery_date')];
                $trimsDataArr[$row[csf('po_break_down_id')]][$row[csf('trim_group')]]['supplier_id'] = $com_supplier;
                $trimsDataArr[$row[csf('po_break_down_id')]][$row[csf('trim_group')]]['booking_date'] = $row[csf('booking_date')];
                $trimsDataArr[$row[csf('po_break_down_id')]][$row[csf('trim_group')]]['booking_no'] = $row[csf('booking_no')];
                $trimsDataArr[$row[csf('po_break_down_id')]][$row[csf('trim_group')]]['job_no'] = $row[csf('job_no')];
                $trimsDataArr[$row[csf('po_break_down_id')]][$row[csf('trim_group')]]['wo_qnty'] += $row[csf('wo_qnty')]*$trim_conversion_factor[$row[csf('trim_group')]];
                $trimsDataArr[$row[csf('po_break_down_id')]][$row[csf('trim_group')]]['rate'] = $row[csf('rate')];
                $trimsDataArr[$row[csf('po_break_down_id')]][$row[csf('trim_group')]]['amount'] += $row[csf('amount')];
            }



            $sql = "select b.item_group_id,c.quantity as receive_qnty, b.amount,c.po_breakdown_id as order_id from inv_receive_master a,inv_trims_entry_dtls b,order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and c.entry_form=24  and a.entry_form=24 and c.trans_type=1 and c.po_breakdown_id in($po_id) and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0";
            $result = sql_select($sql);
            foreach ($result as $row) {
                $recDataArr[$row[csf('order_id')]][$row[csf('item_group_id')]]['receive_qnty'] += $row[csf('receive_qnty')]*$trim_conversion_factor[$row[csf('item_group_id')]];
                $recDataArr[$row[csf('order_id')]][$row[csf('item_group_id')]]['amount'] += $row[csf('amount')];
            }

            $sql = "select b.item_group_id,c.quantity as issue_qnty,c.po_breakdown_id as order_id,b.amount from inv_trims_issue_dtls b,order_wise_pro_details c where b.id=c.dtls_id and c.entry_form=25  and  c.trans_type=2 and c.po_breakdown_id in($po_id) and b.status_active = 1 and b.is_deleted = 0";
            $result = sql_select($sql);
            foreach ($result as $row) {
                $issueDataArr[$row[csf('order_id')]][$row[csf('item_group_id')]]['issue_qnty'] += $row[csf('issue_qnty')]*$trim_conversion_factor[$row[csf('item_group_id')]];
                $issueDataArr[$row[csf('order_id')]][$row[csf('item_group_id')]]['amount'] += $row[csf('amount')];
            }
            $trims_dtls_array = array();
            $sql = "select avg(a.rate) as rate,a.trim_group, a.cons_uom, avg(b.cons) as cons,b.po_break_down_id from wo_pre_cost_trim_cost_dtls a, wo_pre_cost_trim_co_cons_dtls b where a.id=b.wo_pre_cost_trim_cost_dtls_id and b.po_break_down_id in($po_id) and a.status_active=1 and a.is_deleted=0 group by a.trim_group, a.cons_uom,b.po_break_down_id";
            //echo $sql; die;
            $result = sql_select($sql);
            foreach ($result as $key => $row) {
                $dzn_qnty = 0;
                if ($costing_per_id_library[$poDataArr[$row[csf('po_break_down_id')]]['job_no']] == 1) {
                    $dzn_qnty = 12;
                } else if ($costing_per_id_library[$poDataArr[$row[csf('po_break_down_id')]]['job_no']] == 3) {
                    $dzn_qnty = 12 * 2;
                } else if ($costing_per_id_library[$poDataArr[$row[csf('po_break_down_id')]]['job_no']] == 4) {
                    $dzn_qnty = 12 * 3;
                } else if ($costing_per_id_library[$poDataArr[$row[csf('po_break_down_id')]]['job_no']] == 5) {
                    $dzn_qnty = 12 * 4;
                } else {
                    $dzn_qnty = 1;
                }
                $dzn_qnty = $poDataArr[$row[csf('po_break_down_id')]]['ratio'] * $dzn_qnty;
                $req_qnty = $trims_req_qty_arr[$row[csf('po_break_down_id')]][$row[csf('trim_group')]];
                $req_value = $trims_req_amt_arr[$row[csf('po_break_down_id')]][$row[csf('trim_group')]];
                $booking_qty = $trimsDataArr[$row[csf("po_break_down_id")]][$row[csf('trim_group')]]['wo_qnty'];
                $booking_amu = $trimsDataArr[$row[csf("po_break_down_id")]][$row[csf('trim_group')]]['amount'];

                $trims_dtls_array[$row[csf("trim_group")]]['cons_uom'] = $row[csf('cons_uom')];
                $trims_dtls_array[$row[csf("trim_group")]]['req_qnty'] += $req_qnty;
                $trims_dtls_array[$row[csf("trim_group")]]['req_value'] += $req_value;
                $trims_dtls_array[$row[csf("trim_group")]]['booking_qty'] += $booking_qty;
                $trims_dtls_array[$row[csf("trim_group")]]['booking_amu'] += $booking_amu;
                $trims_dtls_array[$row[csf("trim_group")]]['booking_no'] = $trimsDataArr[$row[csf("po_break_down_id")]][$row[csf('trim_group')]]['booking_no'];
                $trims_dtls_array[$row[csf("trim_group")]]['supplier_id'] = $trimsDataArr[$row[csf("po_break_down_id")]][$row[csf('trim_group')]]['supplier_id'];
                $trims_dtls_array[$row[csf("trim_group")]]['booking_date'] = $trimsDataArr[$row[csf("po_break_down_id")]][$row[csf('trim_group')]]['booking_date'];
                $trims_dtls_array[$row[csf("trim_group")]]['delivery_date'] = $trimsDataArr[$row[csf("po_break_down_id")]][$row[csf('trim_group')]]['delivery_date'];
                $trims_dtls_array[$row[csf("trim_group")]]['receive_qnty'] += $recDataArr[$row[csf('po_break_down_id')]][$row[csf('trim_group')]]['receive_qnty'];
                $trims_dtls_array[$row[csf("trim_group")]]['receive_amu'] += $recDataArr[$row[csf('po_break_down_id')]][$row[csf('trim_group')]]['amount'];
                $trims_dtls_array[$row[csf("trim_group")]]['receive_balance'] += $req_qnty - $recDataArr[$row[csf('po_break_down_id')]][$row[csf('trim_group')]]['receive_qnty'];
                $trims_dtls_array[$row[csf("trim_group")]]['issue_qnty'] += $issueDataArr[$row[csf('po_break_down_id')]][$row[csf('trim_group')]]['issue_qnty'];
                $trims_dtls_array[$row[csf("trim_group")]]['issue_balance'] += $recDataArr[$row[csf('po_break_down_id')]][$row[csf('trim_group')]]['receive_qnty']-$issueDataArr[$row[csf('po_break_down_id')]][$row[csf('trim_group')]]['issue_qnty'];
            }
            /*echo '<pre>';
            print_r($trims_dtls_array); die;*/
            $tot_recv_qnty=$tot_req_value=$tot_recv_rec_amu=0;
            foreach ($trims_dtls_array as $key=>$data) {
                if ($i % 2 == 0)
                    $bgcolor = "#E9F3FF";
                else
                    $bgcolor = "#FFFFFF";
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
                    <td width="30"><? echo $i; ?></td>
                    <td><? echo $trim_group[$key]; ?></td>
                    <td width="60" align="center"><? echo $unit_of_measurement[$data['cons_uom']]; ?></td>
                    <td width="70" align="right"><? echo number_format($data['req_qnty'], 2); ?></td>
                    <td width="70" align="right"><? echo number_format($data['req_value'], 2); ?></td>
                    <td width="70" align="right"><? echo number_format($data['booking_qty'], 2); ?></td>
                    <td width="70" align="right"><? echo number_format($data['booking_amu'], 2); ?></td>
                    <td width="70"><? echo $data['booking_no']; ?></td>
                    <td width="70"><? echo $data['supplier_id']; ?></td>
                    <td width="70"><? echo $data['booking_date']; ?></td>
                    <td width="70"><? echo $data['delivery_date']; ?></td>
                    <td width="70" align="right"><? echo number_format($data['receive_qnty'], 2) ?></td>
                    <td width="70" align="right"><? echo number_format($data['receive_amu'], 2) ?></td>
                    <td width="70" align="right"><? echo number_format($data['receive_balance'], 2);?></td>
                    <td width="70" align="right"><? echo number_format($data['issue_qnty'], 2);?></td>
                    <td width="70" align="right"><? echo number_format($data['issue_balance'], 2); ?></td>
                </tr>
                <?
                $tot_booking_qnty += $data['booking_qty'];
                $tot_booking_amu += $data['booking_amu'];
                $tot_req_qnty += $data['req_qnty'];
                $tot_req_value += $data['req_value'];

                $tot_recv_qnty += $data['receive_qnty'];
                $tot_recv_bl_qnty += $data['receive_balance'];
                $tot_recv_rec_amu += $data['receive_amu'];

                $tot_iss_qnty += $data['issue_qnty'];
                $tot_iss_bl_qnty += $data['issue_balance'];
                $i++;
            }
            ?>
            <tfoot>
            <th colspan="3">Total</th>
            <th><? echo number_format($tot_req_qnty, 2); ?></th>
            <th><? echo number_format($tot_req_value, 2); ?></th>
            <th><? echo number_format($tot_booking_qnty, 2); ?></th>
            <th><? echo number_format($tot_booking_amu, 2); ?></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th><? echo number_format($tot_recv_qnty, 2); ?></th>
            <th><? echo number_format($tot_recv_rec_amu, 2); ?></th>
            <th><? echo number_format($tot_recv_bl_qnty, 2); ?></th>
            <th><? echo number_format($tot_iss_qnty, 2); ?></th>
            <th><? echo number_format($tot_iss_bl_qnty, 2); ?></th>
            </tfoot>
        </table>
        </div>
        </fieldset>
        </div>
        <?
        exit();
}


if ($action == "update_tna_progress_comment") {
        echo load_html_head_contents("TNA", "../../../../", 1, 1, $unicode, '', '');
        extract($_REQUEST);

        if ($db_type == 0)
            $blank_date = "0000-00-00";
        else
            $blank_date = "";

        $tna_task_arr = return_library_array("select task_name, task_short_name from lib_tna_task", 'task_name', 'task_short_name');

        $tna_task_id = array();
        $plan_start_array = array();
        $plan_finish_array = array();
        $actual_start_array = array();
        $actual_finish_array = array();

        $notice_start_array = array();
        $notice_finish_array = array();

        //$task_sql= sql_select("select a.task_number,a.task_start_date,a.task_finish_date,a.actual_start_date,a.actual_finish_date,a.notice_date_start,a.notice_date_end from tna_process_mst a, lib_tna_task b where a.task_number=b.task_name and a.template_id=$template_id and a.po_number_id=$po_id order by b.task_sequence_no asc");

        $task_sql = sql_select("select a.task_number,a.task_start_date,a.task_finish_date,a.actual_start_date,a.actual_finish_date,a.notice_date_start,a.notice_date_end from tna_process_mst a, lib_tna_task b where a.task_number=b.task_name and a.po_number_id=$po_id and b.status_active=1 and b.is_deleted=0 order by b.task_sequence_no asc");
        foreach ($task_sql as $row_task) {
            $tna_task_id[] = $row_task[csf("task_number")];

            $plan_start_array[$row_task[csf("task_number")]] = $row_task[csf("task_start_date")];
            $plan_finish_array[$row_task[csf("task_number")]] = $row_task[csf("task_finish_date")];

            $actual_start_array[$row_task[csf("task_number")]] = $row_task[csf("actual_start_date")];
            $actual_finish_array[$row_task[csf("task_number")]] = $row_task[csf("actual_finish_date")];

            $notice_start_array[$row_task[csf("task_number")]] = $row_task[csf("notice_date_start")];
            $notice_finish_array[$row_task[csf("task_number")]] = $row_task[csf("notice_date_end")];
        }



        $comments_array = array();
        $responsible_array = array();
        //$res_comm_sql= sql_select("select task_id, comments, responsible from tna_progress_comments where tamplate_id=$template_id and order_id=$po_id");

        $res_comm_sql = sql_select("select task_id, comments, responsible from tna_progress_comments where order_id=$po_id");
        foreach ($res_comm_sql as $row_res_comm) {
            $comments_array[$row_res_comm[csf("task_id")]] = $row_res_comm[csf("comments")];
            $responsible_array[$row_res_comm[csf("task_id")]] = $row_res_comm[csf("responsible")];
        }

        $execution_time_array = array();
        //$execution_time_sql= sql_select("select tna_task_id, execution_days from tna_task_template_details where task_template_id=$template_id");
        $execution_time_sql = sql_select("select for_specific, tna_task_id, execution_days from tna_task_template_details");
        foreach ($execution_time_sql as $row_execution_time) {
            $execution_time_array[$row_execution_time[csf("for_specific")]][$row_execution_time[csf("tna_task_id")]] = $row_execution_time[csf("execution_days")];
        }

       $lead_time = return_library_array("select task_template_id,lead_time from tna_task_template_details group by task_template_id,lead_time", "task_template_id", "lead_time");



		?>


        <fieldset style="width:1010px">
            <div class="form_caption" align="center"><strong>TNA Progress Comment</strong></div>
            <table style="margin-top:10px" width="1000" border="1" rules="all" class="rpt_table">
                <?php
$sql = "select b.company_name,b.buyer_name,a.po_number,b.job_no,b.style_ref_no,b.gmts_item_id,a.po_received_date,a.shipment_date,set_smv,(po_quantity*total_set_qnty) as po_qty_pcs from wo_po_break_down a,wo_po_details_master b where a.id=$po_id and a.job_no_mst=b.job_no ";
$result = sql_select($sql);

///////////////////////////

$color = return_library_array("select a.po_break_down_id ,b.color_name, a.color_number_id from wo_po_color_size_breakdown a, lib_color b where a.color_number_id=b.id and a.po_break_down_id=" . $po_id . " and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.po_break_down_id ,b.color_name, a.color_number_id order by b.color_name", "color_number_id", "color_name");

$booking_no = return_field_value("booking_no", "wo_booking_dtls", "po_break_down_id=" . $po_id . " and status_active=1 and is_deleted=0", "booking_no");

$imbillishment_cost = return_field_value("rate", "wo_pre_cost_embe_cost_dtls", "job_no='" . $result[0][csf('job_no')] . "' and status_active=1 and is_deleted=0", "rate");
$is_imblishment = $imbillishment_cost ? "Yes" : "No";

$costing_per_arr = return_library_array("select job_no, costing_per from wo_pre_cost_mst where job_no='" . $result[0][csf('job_no')] . "'", "job_no", "costing_per");
$set_item_ratio_arr = return_library_array("select gmts_item_id, set_item_ratio from wo_po_details_mas_set_details where job_no='" . $result[0][csf('job_no')] . "'", "gmts_item_id", "set_item_ratio");

$sql_po_qty_fab_data = sql_select("select sum(c.plan_cut_qnty) as order_quantity,c.item_number_id,c.size_number_id,c.color_number_id  from  wo_po_color_size_breakdown c where  c.po_break_down_id=" . $po_id . " and c.status_active=1  group by c.item_number_id,c.size_number_id,c.color_number_id");
foreach ($sql_po_qty_fab_data as $row) {
	$key = $row[csf(item_number_id)] . $row[csf(size_number_id)] . $row[csf(color_number_id)];
	$sql_po_qty_fab_arr[$key] += $row[csf(order_quantity)];
}

$sql = "select id, job_no,item_number_id, body_part_id, fab_nature_id, color_type_id, fabric_description, avg_cons, fabric_source, rate, amount,avg_finish_cons,status_active from wo_pre_cost_fabric_cost_dtls where job_no='" . $result[0][csf('job_no')] . "' and status_active=1 and is_deleted=0";
$data_array = sql_select($sql);

$req_qty = 0;
foreach ($data_array as $row) {

	$set_item_ratio = $set_item_ratio_arr[$row[csf('item_number_id')]];

	$fab_dtls_data = sql_select("select po_break_down_id,color_number_id,gmts_sizes,cons,requirment from wo_pre_cos_fab_co_avg_con_dtls where pre_cost_fabric_cost_dtls_id=" . $row[csf("id")] . " and po_break_down_id=" . $po_id . " and cons !=0 ");

	foreach ($fab_dtls_data as $fab_dtls_data_row) {
		$dzn_qnty = 0;
		if ($costing_per_arr[$result[0][csf('job_no')]] == 1) {
			$dzn_qnty = 12;
		} else if ($costing_per_arr[$result[0][csf('job_no')]] == 3) {
			$dzn_qnty = 12 * 2;
		} else if ($costing_per_arr[$result[0][csf('job_no')]] == 4) {
			$dzn_qnty = 12 * 3;
		} else if ($costing_per_arr[$result[0][csf('job_no')]] == 5) {
			$dzn_qnty = 12 * 4;
		} else {
			$dzn_qnty = 1;
		}

		$key = $result[0][csf('gmts_item_id')] . $fab_dtls_data_row[csf('gmts_sizes')] . $fab_dtls_data_row[csf('color_number_id')];
		$po_qty_fab = $sql_po_qty_fab_arr[$key];
		$req_qty += ($po_qty_fab / ($dzn_qnty * $set_item_ratio)) * $fab_dtls_data_row[csf("cons")];
	}
}

/////////////////////////////

$buyer_id = "";
foreach ($result as $row) {
	$buyer_id = $row[csf('buyer_name')];
	?>
                    <thead>
                        <tr>
                            <td width="130">Company</td>
                            <td width="196"><? echo $company_short_name_arr[$row[csf('company_name')]]; ?></td>
                            <td width="130">Buyer</td>
                            <td width="186"><? echo $buyer_short_name_arr[$row[csf('buyer_name')]]; ?></td>
                            <td>Job Number</td>
                            <td><? echo $row[csf('job_no')]; ?></td>
                        </tr>
                        <tr>
                            <td>Order No</td>
                            <td><b><? echo $row[csf('po_number')]; ?></b></td>
                            <td>Style Ref.</td>
                            <td><? echo $row[csf('style_ref_no')]; ?></td>
                            <td>Booking Number</td>
                            <td><? echo $booking_no; ?></td>
                        </tr>
                        <tr>
                            <td>Item Name</td>
                            <td><? echo $garments_item[$row[csf('gmts_item_id')]]; ?></td>
                            <td>Embellishment</td>
                            <td><b><? echo $is_imblishment;  ?></b></td>
                            <td>SMV</td>
                            <td><b><? echo $row[csf('set_smv')]; ?></b></td>
                        </tr>
                        <tr>

                            <td>Order Recv. Date</td>
                            <td><? echo change_date_format($row[csf('po_received_date')]); ?></td>
                            <td>Ship Date</td>
                            <td><b><? echo change_date_format($row[csf('shipment_date')]); ?></b></td>
                            <td>Lead Time</td>
                            <td><b>
								<?
								$template_id = str_replace("'", "", $template_id);
                                if (str_replace("'", "", $tna_process_type) == 1) {
                                    $lead_timee = $lead_time[$template_id];
                                } else {
                                    $lead_timee = $template_id;
                                }
                                  echo $lead_timee+1
                                ?></b>
                            </td>
                        </tr>

                        <tr>
                            <td>Quantity (PCS)</td>
                            <td><b><?php echo $row[csf('po_qty_pcs')]; ?></b></td>
                            <td>Finish Req. (KG)</td>
                            <td><b><?php echo number_format($req_qty, 2); ?></b></td>
                            <td>Number of Color</td>
                            <td><b><?php echo count($color); ?></b></td>
                        </tr>

                    </thead>
                    <?php
}
?>
            </table>
            <table style="margin-top:5px" cellpadding="0" width="1000" class="rpt_table" rules="all" border="1">
                <thead bgcolor="#CCCCCC">
                <th width="50">Task No</th>
                <th width="150">Task Name</th>
                <th width="60">Allowed Days</th>
                <th width="80">Plan Start Date</th>
                <th width="80">Plan Finish Date</th>
                <th width="80">Actual Start Date</th>
                <th width="80">Actual Finish Date</th>
                <th width="80">Start Delay/ Early By</th>
                <th width="80">Finish Delay/ Early By</th>
                <th width="100">Responsible</th>
                <th>Comments</th>
                </thead>
            </table>



            <table cellpadding="0" width="1000" cellspacing="0" border="1" rules="all" class="rpt_table">
                <?
                $i = 1;
                foreach ($tna_task_id as $key) {
                    if ($i % 2 == 0)
                        $trcolor = "#E9F3FF";
                    else
                        $trcolor = "#FFFFFF";

                    $bgcolor1 = "";
                    $bgcolor = "";

                    if ($plan_start_array[$key] != $blank_date) {
                        if (strtotime($notice_start_array[$key]) <= strtotime(date("Y-m-d", time())) && strtotime(date("Y-m-d", time())) <= strtotime($plan_start_array[$key]))
                            $bgcolor = "#FFFF00";
                        else if (strtotime($plan_start_array[$key]) < strtotime(date("Y-m-d", time())))
                            $bgcolor = "#FF0000";
                        else
                            $bgcolor = "";
                    }

                    if ($plan_finish_array[$key] != $blank_date) {
                        if (strtotime($notice_finish_array[$key]) <= strtotime(date("Y-m-d", time())) && strtotime(date("Y-m-d", time())) <= strtotime($plan_finish_array[$key]))
                            $bgcolor1 = "#FFFF00";
                        else if (strtotime($plan_finish_array[$key]) < strtotime(date("Y-m-d", time())))
                            $bgcolor1 = "#FF0000";
                        else
                            $bgcolor1 = "";
                    }

                    if ($actual_start_array[$key] != $blank_date)
                        $bgcolor = "";
                    if ($actual_finish_array[$key] != $blank_date)
                        $bgcolor1 = "";

                    // Delay / Early............

                    $bgcolor5 = "";
                    $bgcolor6 = "";
                    $delay = "";
                    $early = "";

                    if ($actual_start_array[$key] != $blank_date) {
                        $start_diff1 = datediff("d", $actual_start_array[$key], $plan_start_array[$key]);
                        //$finish_diff1 = datediff( "d", $actual_finish_array[$key], $plan_finish_array[$key]);
                        if ($actual_finish_array[$key] == "" || $actual_finish_array[$key] == "0000-00-00") {

                            $finish_diff1 = datediff("d", date("Y-m-d"), $plan_finish_array[$key]);
                        } else {
                            $finish_diff1 = datediff("d", $actual_finish_array[$key], $plan_finish_array[$key]);
                        }


                        $start_diff = $start_diff1 - 1;
                        $finish_diff = $finish_diff1 - 1;

                        if ($start_diff < 0) {
                            $bgcolor5 = "#2A9FFF"; //Blue
                            $start = "(Delay)";
                        }
                        if ($start_diff > 0) {
                            $bgcolor5 = "";
                            $start = "(Early)";
                        }
                        if ($finish_diff < 0) {
                            if ($actual_finish_array[$key] == "" || $actual_finish_array[$key] == "0000-00-00") {
                                $bgcolor6 = "#FF0000";
                            } else {
                                $bgcolor6 = "#2A9FFF";
                            }
                            $finish = "(Delay)";
                        }
                        if ($finish_diff > 0) {
                            $bgcolor6 = "";
                            $finish = "(Early)";
                        }
                    } else {
                        if (date("Y-m-d") > date("Y-m-d", strtotime($plan_start_array[$key]))) {
                            $start_diff1 = datediff("d", $plan_start_array[$key], date("Y-m-d"));
                            $start_diff = $start_diff1 - 1;
                            $bgcolor5 = "#FF0000";  //Red
                            $start = "(Delay)";
                        }
                        if (date("Y-m-d") > date("Y-m-d", strtotime($plan_finish_array[$key]))) {
                            $finish_diff1 = datediff("d", $plan_finish_array[$key], date("Y-m-d"));
                            $finish_diff = $finish_diff1 - 1;
                            $bgcolor6 = "#FF0000";
                            $finish = "(Delay)";
                        }
                        if (date("Y-m-d") <= date("Y-m-d", strtotime($plan_start_array[$key]))) {
                            $start_diff = "";
                            $bgcolor5 = "";
                            $start = "(Ac. Start Dt. Not Found)";
                        }
                        if (date("Y-m-d") <= date("Y-m-d", strtotime($plan_finish_array[$key]))) {
                            $finish_diff = "";
                            $bgcolor6 = "";
                            $finish = "(Ac. Finish Dt. Not Found)";
                        }
                    }
                    ?>
                    <tr bgcolor="<? echo $trcolor; ?>" id="tr_<? echo $i; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')">
                        <td align="center" width="50"><? echo $i; ?></td>
                        <td width="150"><? echo $tna_task_arr[$key]; ?></td>
                        <td align="center" width="60"><? echo datediff("d", $plan_start_array[$key], $plan_finish_array[$key]); //$execution_time_array[$buyer_id][$key];   ?></td>
                        <td align="center" width="80"><? echo change_date_format($plan_start_array[$key]); ?>&nbsp;</td>
                        <td align="center" width="80"><? echo change_date_format($plan_finish_array[$key]); ?>&nbsp;</td>
                        <td align="center" width="80" bgcolor="<? echo $bgcolor; ?>">
                    <?
                    if ($actual_start_array[$key] == "0000-00-00" || $actual_start_array[$key] == "")
                        echo "&nbsp;";
                    else
                        echo change_date_format($actual_start_array[$key]);
                    ?>
                        </td>
                        <td align="center" width="80" bgcolor="<? echo $bgcolor1; ?>">
            <?
            if ($actual_finish_array[$key] == "0000-00-00" || $actual_finish_array[$key] == "")
                echo "&nbsp;";
            else
                echo change_date_format($actual_finish_array[$key]);
            ?>
                        </td>
                        <td align="center" width="80" bgcolor="<? echo $bgcolor5; ?>">
            <?
            echo abs($start_diff) . " " . $start;
            ?>
                        </td>
                        <td align="center" width="80" bgcolor="<? echo $bgcolor6; ?>">
            <?
            echo abs($finish_diff) . " " . $finish;
            ?>
                        </td>
                        <td width="100"><p><?php echo $responsible_array[$key]; ?>&nbsp;</p></td>
                        <td><p><?php echo $comments_array[$key]; ?>&nbsp;</p></td>
                    </tr>
            <?
            $i++;
        }
        ?>
            </table>
        </fieldset>
    <?
    exit();
}

if ($action == "show_image") {
    echo load_html_head_contents("Set Entry", "../../../../", 1, 1, $unicode);
    extract($_REQUEST);
    //echo "select image_location  from common_photo_library  where master_tble_id='$job_no' and form_name='knit_order_entry' and is_deleted=0 and file_type=1";
    $data_array = sql_select("select image_location  from common_photo_library  where master_tble_id='$job_no' and form_name='knit_order_entry' and is_deleted=0 and file_type=1");
    ?>
        <table width="1000">
            <tr>
    <?
    foreach ($data_array as $row) {
        ?>
                    <td><img src='../../../../<? echo $row[csf('image_location')]; ?>' height='250' width='300' /></td>
        <?
    }
    ?>
            </tr>
        </table>

    <?
    exit();
}

if ($action == "daysInHand_popup") {
    echo load_html_head_contents("Days In Hand Details", "../../../../", 1, 1, $unicode, '', '');

    $expData = explode('_', $job_number);
    $job_number = $expData[0];
    $po_id = $expData[1];

    $exFactory_arr = array();
    $exFactory_qnty_arr = array();
    $data_arr = sql_select("select po_break_down_id, country_id, sum(ex_factory_qnty) as ex_factory_qnty from pro_ex_factory_mst where status_active=1 and is_deleted=0 and po_break_down_id in ($po_id) and country_id in($country) group by po_break_down_id, country_id");
    foreach ($data_arr as $row) {
        $exFactory_arr[$row[csf('po_break_down_id')]][$row[csf('country_id')]] = $row[csf('ex_factory_qnty')];
    }

    $sql = "select b.id, b.po_number, a.job_no, a.buyer_name, a.company_name, a.style_ref_no, c.country_id, sum(c.order_quantity) as po_quantity from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and b.id in ($po_id) and c.country_id in($country) and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.id, a.job_no, a.company_name, a.buyer_name, a.style_ref_no,b.po_number,c.country_id";
    $dataArray = sql_select($sql);
    ?>

        <fieldset style="width:510px; margin-left:15px">
            <table width="500">
                <tr class="form_caption">
                    <td align="center" colspan="4"><strong>Days In Hand Details</strong></td>
                </tr>
                <tr>
                    <td align="right" width="130"> <strong>Job Number</strong> :</td>
                    <td width="150"><? echo $dataArray[0][csf("job_no")]; ?></td>
                    <td align="right"  width="130"><strong>Buyer Name</strong> :</td>
                    <td><? echo $buyer_short_name_arr[$dataArray[0][csf("buyer_name")]]; ?></td>
                </tr>
                <tr>
                    <td align="right"><strong>Company Name</strong> :</td>
                    <td><? echo $company_short_name_arr[$dataArray[0][csf("company_name")]]; ?></td>
                    <td align="right"><strong>Style Ref No</strong> : </td>
                    <td><? echo $dataArray[0][csf("style_ref_no")]; ?> </td>
                </tr>
                <tr>
                    <td align="right"><strong>PO No.</strong> :</td>
                    <td><? echo $dataArray[0][csf("po_number")]; ?></td>
                    <td colspan="2" height="15"></td>
                </tr>
            </table>
            <table cellpadding="0" width="500" class="rpt_table" rules="all" border="1">
                <thead>
                    <tr>
                        <th width="40">SL</th>
                        <th width="110">Country Name</th>
                        <th width="100">Country Qty</th>
                        <th width="100">Shipment Qty</th>
                        <th width="">Balance Qty</th>
                    </tr>
                </thead>
            </table>
            <div style="width:500px; max-height:250px; overflow-y:scroll">
                <table cellpadding="0" width="480" class="rpt_table" rules="all" border="1">
        <?
        $i = 0;
        foreach ($dataArray as $row) {
            $i++;
            if ($i % 2 == 0)
                $bgcolor = "#EFEFEF";
            else
                $bgcolor = "#FFFFFF";

            $shipment_qnty = $exFactory_arr[$row[csf('id')]][$row[csf('country_id')]];
            ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            <td width="40"><? echo $i; ?></td>
                            <td width="110"><? echo $country_library[$row[csf('country_id')]]; ?></td>
                            <td width="100" align="right"><?
        echo $row[csf('po_quantity')];
        $tot_order_qnty += $row[csf('po_quantity')];
        ?></td>
                            <td width="100" align="right"><?
        echo $shipment_qnty;
        $tot_ship_qnty += $shipment_qnty;
        ?>&nbsp;</td>
                            <td align="right"><? echo $balance = $row[csf('po_quantity')] - $shipment_qnty; ?></td>
                        </tr>
        <?
    }
    ?>
                    <tfoot>
                        <tr>
                            <th>&nbsp;</th>
                            <th>Total</th>
                            <th width="100" align="right"><? echo $tot_order_qnty; ?></th>
                            <th width="100" align="right"><? echo $tot_ship_qnty; ?></th>
                            <th><? echo $tot_order_qnty - $tot_ship_qnty; ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </fieldset>
                <?
                exit();
            }


            if ($action == 'OrderPopup') {
                echo load_html_head_contents("Order Wise Production Report", "../../../../", 1, 1, $unicode, '', '');
                $po_break_down_id = $_REQUEST['po_break_down_id'];
                $item_id = $_REQUEST['item_id'];
                $company_name = str_replace("'", "", $_REQUEST['company_name']);

                $color_variable_setting = return_field_value("ex_factory", "variable_settings_production", "company_name='$company_name' and variable_list=1 and status_active=1 and is_deleted=0", "ex_factory");
                $ex_fact_qty_arr = array();
                if ($color_variable_setting == 2 || $color_variable_setting == 3) {
                    $sql_exfect = "select c.color_number_id, c.size_number_id, sum(b.production_qnty) as production_qnty from pro_ex_factory_mst a,pro_ex_factory_dtls b, wo_po_color_size_breakdown c where a.id=b.mst_id and b.color_size_break_down_id=c.id and a.po_break_down_id in ($po_break_down_id) and a.item_number_id=$item_id and a.status_active=1 and a.is_deleted=0 group by  c.color_number_id, c.size_number_id";
                    $sql_result_exfact = sql_select($sql_exfect);
                    foreach ($sql_result_exfact as $row) {
                        $ex_fact_qty_arr[$row[csf("color_number_id")]][$row[csf("size_number_id")]] = $row[csf("production_qnty")];
                    }
                }
                //var_dump($ex_fact_qty_arr);
                ?>
        <div id="data_panel" align="center" style="width:100%">
            <script>
                function new_window()
                {
                    var w = window.open("Surprise", "#");
                    var d = w.document.open();
                    d.write(document.getElementById('details_reports').innerHTML);
                    d.close();
                }
            </script>
            <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onclick="new_window()" />
        </div>

        <div style="width:700px" align="center" id="details_reports">
            <legend>Color And Size Wise Summary</legend>
            <table id="tbl_id" class="rpt_table" width="" border="1" rules="all" >
                <thead>
                    <tr>
                        <th width="100">Buyer</th>
                        <th width="100">Job Number</th>
                        <th width="100">Style Name</th>
                        <th width="300">Order Number</th>
                        <th width="100">Ship Date</th>
                        <th width="100">Item Name</th>
                        <th width="100">Order Qty.</th>
                    </tr>
                </thead>
            <?
            $buyer_short_library = return_library_array("select id,short_name from lib_buyer", "id", "short_name");
            if ($db_type == 0) {
                $sql = "select a.job_no_mst,group_concat(distinct(a.po_number)) as po_number,a.pub_shipment_date, sum(a.po_quantity) as po_quantity,a.packing,b.set_break_down, b.company_name, b.order_uom, b.buyer_name, b.style_ref_no,c.set_item_ratio
					from wo_po_break_down a, wo_po_details_master b, wo_po_details_mas_set_details c
					where a.job_no_mst=b.job_no and b.job_no=c.job_no and a.id in ($po_break_down_id) and c.gmts_item_id=$item_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
            } else {
                $sql = "select a.job_no_mst, LISTAGG(a.po_number, ',') WITHIN GROUP (ORDER BY a.id) as po_number,max(a.pub_shipment_date) as pub_shipment_date, sum(a.po_quantity) as po_quantity,a.packing,b.set_break_down, b.company_name, b.order_uom, b.buyer_name, b.style_ref_no,c.set_item_ratio
					from wo_po_break_down a, wo_po_details_master b, wo_po_details_mas_set_details c
					where a.job_no_mst=b.job_no and b.job_no=c.job_no and a.id in ($po_break_down_id) and c.gmts_item_id=$item_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.job_no_mst, a.packing,b.set_break_down, b.company_name, b.order_uom, b.buyer_name, b.style_ref_no,c.set_item_ratio";
            }
            //echo $sql;die;
            $resultRow = sql_select($sql);

            $cons_embr = return_field_value("sum(cons_dzn_gmts) as cons_dzn_gmts", "wo_pre_cost_embe_cost_dtls", "job_no='" . $resultRow[0][csf("job_no_mst")] . "' and status_active=1 and is_deleted=0", "cons_dzn_gmts");
            ?>
                <tr>
                    <td><? echo $buyer_short_library[$resultRow[0][csf("buyer_name")]]; ?></td>
                    <td><p><? echo $resultRow[0][csf("job_no_mst")]; ?></p></td>
                    <td><p><? echo $resultRow[0][csf("style_ref_no")]; ?></p></td>
                    <td><p><? echo implode(",", array_unique(explode(",", $resultRow[0][csf("po_number")]))); ?></p></td>
                    <td><? echo change_date_format($resultRow[0][csf("pub_shipment_date")]); ?></td>
                    <td><? echo $garments_item[$item_id]; ?></td>
                    <td><? echo $resultRow[0][csf("po_quantity")] * $resultRow[0][csf("set_item_ratio")]; ?></td>
                </tr>
            <?
            $prod_sewing_sql = sql_select("SELECT sum(alter_qnty) as alter_qnty, sum(reject_qnty) as reject_qnty from pro_garments_production_mst where production_type=5 and po_break_down_id in ($po_break_down_id) and item_number_id=$item_id and is_deleted=0 and status_active=1");
            foreach ($prod_sewing_sql as $sewingRow)
                ;
            ?>
                <tr>
                    <td colspan="2">Total Alter Sewing Qty : <b><? echo $sewingRow[csf("alter_qnty")]; ?></b></td>
                    <td colspan="2">Total Reject Sewing Qty : <b><? echo $sewingRow[csf("reject_qnty")]; ?></b></td>
                    <td colspan="2">Pack Assortment: <b><? echo $packing[$resultRow[csf("packing")]]; ?></b></td>
                </tr>
            </table>
            <?
            $size_Arr_library = return_library_array("select id,size_name from lib_size", "id", "size_name");
            $color_Arr_library = return_library_array("select id,color_name from lib_color", "id", "color_name");

            $color_library = array();
            $size_library = array();
            $color_library_plan = array();
            $dataQty = array();
            $colorSizeData = sql_select("select color_number_id, size_number_id, order_quantity, plan_cut_qnty, excess_cut_perc from wo_po_color_size_breakdown where po_break_down_id in ($po_break_down_id) and item_number_id=$item_id and status_active=1 and is_deleted=0");
            foreach ($colorSizeData as $csRow) {
                if ($csRow[csf('color_number_id')] > 0) {
                    $color_library[$csRow[csf('color_number_id')]] += $csRow[csf('order_quantity')];
                    $color_library_plan[$csRow[csf('color_number_id')]] += $csRow[csf('plan_cut_qnty')];
                }

                if ($csRow[csf('size_number_id')] > 0) {
                    $size_library[$csRow[csf('size_number_id')]] = $csRow[csf('size_number_id')];
                }

                $dataQty[$csRow[csf('color_number_id')]][$csRow[csf('size_number_id')]][1] += $csRow[csf('order_quantity')];
                $dataQty[$csRow[csf('color_number_id')]][$csRow[csf('size_number_id')]][2] += $csRow[csf('plan_cut_qnty')];
                $dataQty[$csRow[csf('color_number_id')]][$csRow[csf('size_number_id')]][3] += $csRow[csf('excess_cut_perc')];
                $dataQty[$csRow[csf('color_number_id')]][$csRow[csf('size_number_id')]][4] += 1;
            }

            $prodDataQty = array();
            if ($db_type == 0) {
                $prod_sql = sql_select("SELECT d.color_number_id, d.size_number_id,
				IFNULL(sum(CASE WHEN c.production_type ='1' THEN c.production_qnty  ELSE 0 END),0) AS cutting_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='2' AND a.embel_name=1 THEN c.production_qnty ELSE 0 END),0) AS printing_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='3' AND a.embel_name=1 THEN c.production_qnty ELSE 0 END),0) AS printreceived_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='2' AND a.embel_name=2 THEN c.production_qnty ELSE 0 END),0) AS emb_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='3' AND a.embel_name=2 THEN c.production_qnty ELSE 0 END),0) AS embreceived_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='2' AND a.embel_name=3 THEN c.production_qnty ELSE 0 END),0) AS wash_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='3' AND a.embel_name=3 THEN c.production_qnty ELSE 0 END),0) AS washreceived_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='2' AND a.embel_name=4 THEN c.production_qnty ELSE 0 END),0) AS sp_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='3' AND a.embel_name=4 THEN c.production_qnty ELSE 0 END),0) AS spreceived_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='4' THEN c.production_qnty ELSE 0 END),0) AS sewingin_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='5' THEN c.production_qnty ELSE 0 END),0) AS sewingout_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='6' THEN c.production_qnty ELSE 0 END),0) AS finishin_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='7' THEN c.production_qnty ELSE 0 END),0) AS iron_qnty,
				IFNULL(sum(CASE WHEN c.production_type ='8' THEN c.production_qnty ELSE 0 END),0) AS finish_qnty
			from
				pro_garments_production_mst a, pro_garments_production_dtls c,wo_po_color_size_breakdown d
			where
				a.id=c.mst_id and d.po_break_down_id in (" . $po_break_down_id . ") and a.item_number_id='$item_id' and c.color_size_break_down_id=d.id and c.status_active=1 $location $floor group by d.color_number_id, d.size_number_id");
            } else {
                $prod_sql = sql_select("SELECT d.color_number_id, d.size_number_id,
				NVL(sum(CASE WHEN c.production_type ='1' THEN c.production_qnty  ELSE 0 END),0) AS cutting_qnty,
				NVL(sum(CASE WHEN c.production_type ='2' AND a.embel_name=1 THEN c.production_qnty ELSE 0 END),0) AS printing_qnty,
				NVL(sum(CASE WHEN c.production_type ='3' AND a.embel_name=1 THEN c.production_qnty ELSE 0 END),0) AS printreceived_qnty,
				NVL(sum(CASE WHEN c.production_type ='2' AND a.embel_name=2 THEN c.production_qnty ELSE 0 END),0) AS emb_qnty,
				NVL(sum(CASE WHEN c.production_type ='3' AND a.embel_name=2 THEN c.production_qnty ELSE 0 END),0) AS embreceived_qnty,
				NVL(sum(CASE WHEN c.production_type ='2' AND a.embel_name=3 THEN c.production_qnty ELSE 0 END),0) AS wash_qnty,
				NVL(sum(CASE WHEN c.production_type ='3' AND a.embel_name=3 THEN c.production_qnty ELSE 0 END),0) AS washreceived_qnty,
				NVL(sum(CASE WHEN c.production_type ='2' AND a.embel_name=4 THEN c.production_qnty ELSE 0 END),0) AS sp_qnty,
				NVL(sum(CASE WHEN c.production_type ='3' AND a.embel_name=4 THEN c.production_qnty ELSE 0 END),0) AS spreceived_qnty,
				NVL(sum(CASE WHEN c.production_type ='4' THEN c.production_qnty ELSE 0 END),0) AS sewingin_qnty,
				NVL(sum(CASE WHEN c.production_type ='5' THEN c.production_qnty ELSE 0 END),0) AS sewingout_qnty,
				NVL(sum(CASE WHEN c.production_type ='6' THEN c.production_qnty ELSE 0 END),0) AS finishin_qnty,
				NVL(sum(CASE WHEN c.production_type ='7' THEN c.production_qnty ELSE 0 END),0) AS iron_qnty,
				NVL(sum(CASE WHEN c.production_type ='8' THEN c.production_qnty ELSE 0 END),0) AS finish_qnty
			from
				pro_garments_production_mst a, pro_garments_production_dtls c, wo_po_color_size_breakdown d
			where
				a.id=c.mst_id and d.po_break_down_id in (" . $po_break_down_id . ") and a.item_number_id='$item_id' and c.color_size_break_down_id=d.id and c.status_active=1 and a.status_active=1 $location $floor group by d.color_number_id, d.size_number_id");
            }

            foreach ($prod_sql as $row) {
                $prodDataQty[$row[csf('color_number_id')]][$row[csf('size_number_id')]]['cutting_qnty'] = $row[csf('cutting_qnty')];
                $prodDataQty[$row[csf('color_number_id')]][$row[csf('size_number_id')]]['printing_qnty'] = $row[csf('printing_qnty')];
                $prodDataQty[$row[csf('color_number_id')]][$row[csf('size_number_id')]]['printreceived_qnty'] = $row[csf('printreceived_qnty')];
                $prodDataQty[$row[csf('color_number_id')]][$row[csf('size_number_id')]]['emb_qnty'] = $row[csf('emb_qnty')];
                $prodDataQty[$row[csf('color_number_id')]][$row[csf('size_number_id')]]['embreceived_qnty'] = $row[csf('embreceived_qnty')];
                $prodDataQty[$row[csf('color_number_id')]][$row[csf('size_number_id')]]['wash_qnty'] = $row[csf('wash_qnty')];
                $prodDataQty[$row[csf('color_number_id')]][$row[csf('size_number_id')]]['washreceived_qnty'] = $row[csf('washreceived_qnty')];
                $prodDataQty[$row[csf('color_number_id')]][$row[csf('size_number_id')]]['sp_qnty'] = $row[csf('sp_qnty')];
                $prodDataQty[$row[csf('color_number_id')]][$row[csf('size_number_id')]]['spreceived_qnty'] = $row[csf('spreceived_qnty')];
                $prodDataQty[$row[csf('color_number_id')]][$row[csf('size_number_id')]]['sewingin_qnty'] = $row[csf('sewingin_qnty')];
                $prodDataQty[$row[csf('color_number_id')]][$row[csf('size_number_id')]]['sewingout_qnty'] = $row[csf('sewingout_qnty')];
                $prodDataQty[$row[csf('color_number_id')]][$row[csf('size_number_id')]]['finishin_qnty'] = $row[csf('finishin_qnty')];
                $prodDataQty[$row[csf('color_number_id')]][$row[csf('size_number_id')]]['iron_qnty'] = $row[csf('iron_qnty')];
                $prodDataQty[$row[csf('color_number_id')]][$row[csf('size_number_id')]]['finish_qnty'] = $row[csf('finish_qnty')];
            }
            // var_dump($color_library1);
            // echo "<br>";
            //  print_r($size_library1);]

            /* $color_library=sql_select("select distinct(color_number_id) as color_number_id from wo_po_color_size_breakdown where po_break_down_id in ($po_break_down_id) and color_mst_id!=0 and status_active=1");
              $size_library=sql_select("select distinct(size_number_id) as size_number_id from wo_po_color_size_breakdown where po_break_down_id in ($po_break_down_id) and size_number_id!=0 and status_active=1"); */
            $count = count($size_library);
            $width = $count * 70 + 350;
            ?>
            <div style="color:#FF0000; font-size:14px; font-weight:bold; float:left; width:700px">This Pop-Up Will Be Perfect When Order, Production and All Procedure Follow Color or Color & Size Level.</div>
            <table id="tblDtls_id" class="rpt_table" width="<? echo $width; ?>" border="1" rules="all" >
                <thead>
                    <tr>
                        <th width="100">Color Name</th>
                        <th width="170">Production Type</th>
                <?
                foreach ($size_library as $sizeId => $val) {
                    ?>
                            <th width="80"><? echo $size_Arr_library[$sizeId]; ?></th>
                    <?
                }
                ?>
                        <th width="60">Total</th>
                    </tr>
                </thead>
                <?
                foreach ($color_library as $colorId => $totalorderqnty) {
                    if ($color_variable_setting == 2 || $color_variable_setting == 3)
                        $row_span = 17;
                    else
                        $row_span = 16;
                    ?>
                    <tr>
                        <td rowspan="<? echo $row_span; ?>"><? echo $color_Arr_library[$colorId]; ?></td>
                    <?
                    $bgcolor1 = "#E9F3FF";
                    $bgcolor2 = "#FFFFFF";
                    ?>
                    </tr>
                    <tr bgcolor="<? echo $bgcolor1; ?>">
                        <td><b>Order Quantity</b></td>
                    <?
                    foreach ($size_library as $sizeId => $sizeRes) {
                        ?>
                            <td><? echo $dataQty[$colorId][$sizeId][1]; ?></td>
                        <?
                    }
                    ?>
                        <td><? echo $totalorderqnty; ?></td>
                    </tr>
                    <tr bgcolor="<? echo $bgcolor2; ?>">
                        <td><b>Plan To Cut (AVG <? echo number_format($dataQty[$colorId][$sizeId][3] / $dataQty[$colorId][$sizeId][4], 2); ?>)% </b></td>
                    <?
                    foreach ($size_library as $sizeId => $sizeRes) {
                        ?>
                            <td title="Excess Cut <? echo $dataQty[$colorId][$sizeId][3]; ?>%"><? echo $dataQty[$colorId][$sizeId][2]; ?></td>
                        <?
                    }
                    ?>
                        <td><? echo $color_library_plan[$colorId]; ?></td>
                    </tr>
                    <?
                    $total_cutting = 0;
                    $total_sew_in = 0;
                    $total_sew_out = 0;
                    $total_fin_in = 0;
                    $total_fin_out = 0;
                    $total_iron_out = 0;
                    $total_exfact_qnty = 0;
                    $total_print_issue = 0;
                    $total_print_rcv = 0;
                    $total_embro_issue = 0;
                    $total_embro_rcv = 0;
                    $total_sp_issue = 0;
                    $total_sp_rcv = 0;
                    $total_wash_issue = 0;
                    $total_wash_rcv = 0;
                    $cutting_html = '';
                    $sewin_html = '';
                    $sewout_html = '';
                    $finisin_html = '';
                    $finisout_html = '';
                    $iron_html = '';
                    $exfact_html = '';
                    $printiss_html = '';
                    $printrcv_html = '';
                    $embroiss_html = '';
                    $embrorcv_html = '';
                    $spiss_html = '';
                    $sprcv_html = '';
                    $washiss_html = '';
                    $washrcv_html = '';
                    foreach ($size_library as $sizeId => $sizeRes) {
                        $cutting_qnty = $prodDataQty[$colorId][$sizeId]['cutting_qnty'];
                        $printing_qnty = $prodDataQty[$colorId][$sizeId]['printing_qnty'];
                        $printreceived_qnty = $prodDataQty[$colorId][$sizeId]['printreceived_qnty'];
                        $emb_qnty = $prodDataQty[$colorId][$sizeId]['emb_qnty'];
                        $embreceived_qnty = $prodDataQty[$colorId][$sizeId]['embreceived_qnty'];
                        $wash_qnty = $prodDataQty[$colorId][$sizeId]['wash_qnty'];
                        $washreceived_qnty = $prodDataQty[$colorId][$sizeId]['washreceived_qnty'];
                        $sp_qnty = $prodDataQty[$colorId][$sizeId]['sp_qnty'];
                        $spreceived_qnty = $prodDataQty[$colorId][$sizeId]['spreceived_qnty'];
                        $sewingin_qnty = $prodDataQty[$colorId][$sizeId]['sewingin_qnty'];
                        $sewingout_qnty = $prodDataQty[$colorId][$sizeId]['sewingout_qnty'];
                        $finishin_qnty = $prodDataQty[$colorId][$sizeId]['finishin_qnty'];
                        $iron_qnty = $prodDataQty[$colorId][$sizeId]['iron_qnty'];
                        $finish_qnty = $prodDataQty[$colorId][$sizeId]['finish_qnty'];

                        $resRow[csf($col)] = $dataQty[$colorId][$sizeId][2];
                        if ($cutting_qnty == 0)
                            $bgCol = "bgcolor='#FF0000'";
                        else if ($cutting_qnty < $resRow[csf($col)])
                            $bgCol = "bgcolor='#FFFF00'";
                        else if ($cutting_qnty >= $resRow[csf($col)])
                            $bgCol = "bgcolor='#00FF00'";
                        $cutting_html .= '<td ' . $bgCol . '>' . $cutting_qnty . '</td>';
                        $total_cutting += $cutting_qnty;

                        if ($cons_embr > 0) {
                            if ($printing_qnty == 0)
                                $bgCol = "bgcolor='#FF0000'";
                            else if ($printing_qnty < $resRow[csf($col)])
                                $bgCol = "bgcolor='#FFFF00'";
                            else if ($printing_qnty >= $resRow[csf($col)])
                                $bgCol = "bgcolor='#00FF00'";
                        } else
                            $bgCol = '';
                        $printiss_html .= '<td ' . $bgCol . '>' . $printing_qnty . '</td>';
                        $total_print_issue += $printing_qnty;

                        if ($cons_embr > 0) {
                            if ($printreceived_qnty == 0)
                                $bgCol = "bgcolor='#FF0000'";
                            else if ($printreceived_qnty < $resRow[csf($col)])
                                $bgCol = "bgcolor='#FFFF00'";
                            else if ($printreceived_qnty >= $resRow[csf($col)])
                                $bgCol = "bgcolor='#00FF00'";
                        } else
                            $bgCol = '';

                        $printrcv_html .= '<td ' . $bgCol . '>' . $printreceived_qnty . '</td>';
                        $total_print_rcv += $printreceived_qnty;

                        if ($cons_embr > 0) {
                            if ($emb_qnty == 0)
                                $bgCol = "bgcolor='#FF0000'";
                            else if ($emb_qnty < $resRow[csf($col)])
                                $bgCol = "bgcolor='#FFFF00'";
                            else if ($emb_qnty >= $resRow[csf($col)])
                                $bgCol = "bgcolor='#00FF00'";
                        } else
                            $bgCol = '';
                        $embroiss_html .= '<td ' . $bgCol . '>' . $emb_qnty . '</td>';
                        $total_embro_issue += $emb_qnty;

                        if ($cons_embr > 0) {
                            if ($embreceived_qnty == 0)
                                $bgCol = "bgcolor='#FF0000'";
                            else if ($embreceived_qnty < $resRow[csf($col)])
                                $bgCol = "bgcolor='#FFFF00'";
                            else if ($embreceived_qnty >= $resRow[csf($col)])
                                $bgCol = "bgcolor='#00FF00'";
                        } else
                            $bgCol = '';

                        $embrorcv_html .= '<td ' . $bgCol . '>' . $embreceived_qnty . '</td>';
                        $total_embro_rcv += $embreceived_qnty;

                        if ($cons_embr > 0) {
                            if ($sp_qnty == 0)
                                $bgCol = "bgcolor='#FF0000'";
                            else if ($sp_qnty < $resRow[csf($col)])
                                $bgCol = "bgcolor='#FFFF00'";
                            else if ($sp_qnty >= $resRow[csf($col)])
                                $bgCol = "bgcolor='#00FF00'";
                        } else
                            $bgCol = '';
                        $spiss_html .= '<td ' . $bgCol . '>' . $sp_qnty . '</td>';
                        $total_sp_issue += $sp_qnty;

                        if ($cons_embr > 0) {
                            if ($spreceived_qnty == 0)
                                $bgCol = "bgcolor='#FF0000'";
                            else if ($spreceived_qnty < $resRow[csf($col)])
                                $bgCol = "bgcolor='#FFFF00'";
                            else if ($spreceived_qnty >= $resRow[csf($col)])
                                $bgCol = "bgcolor='#00FF00'";
                        } else
                            $bgCol = '';

                        $sprcv_html .= '<td ' . $bgCol . '>' . $spreceived_qnty . '</td>';
                        $total_sp_rcv += $spreceived_qnty;

                        if ($cons_embr > 0) {
                            if ($wash_qnty == 0)
                                $bgCol = "bgcolor='#FF0000'";
                            else if ($wash_qnty < $resRow[csf($col)])
                                $bgCol = "bgcolor='#FFFF00'";
                            else if ($wash_qnty >= $resRow[csf($col)])
                                $bgCol = "bgcolor='#00FF00'";
                        } else
                            $bgCol = '';
                        $washiss_html .= '<td ' . $bgCol . '>' . $wash_qnty . '</td>';
                        $total_wash_issue += $wash_qnty;

                        if ($cons_embr > 0) {
                            if ($washreceived_qnty == 0)
                                $bgCol = "bgcolor='#FF0000'";
                            else if ($washreceived_qnty < $resRow[csf($col)])
                                $bgCol = "bgcolor='#FFFF00'";
                            else if ($washreceived_qnty >= $resRow[csf($col)])
                                $bgCol = "bgcolor='#00FF00'";
                        } else
                            $bgCol = '';

                        $washrcv_html .= '<td ' . $bgCol . '>' . $washreceived_qnty . '</td>';
                        $total_wash_rcv += $washreceived_qnty;

                        if ($sewingin_qnty == 0)
                            $bgCol = "bgcolor='#FF0000'";
                        else if ($sewingin_qnty < $resRow[csf($col)])
                            $bgCol = "bgcolor='#FFFF00'";
                        else if ($sewingin_qnty >= $resRow[csf($col)])
                            $bgCol = "bgcolor='#00FF00'";
                        $sewin_html .= '<td ' . $bgCol . '>' . $sewingin_qnty . '</td>';
                        $total_sew_in += $sewingin_qnty;

                        if ($sewingout_qnty == 0)
                            $bgCol = "bgcolor='#FF0000'";
                        else if ($sewingout_qnty < $resRow[csf($col)])
                            $bgCol = "bgcolor='#FFFF00'";
                        else if ($sewingout_qnty >= $resRow[csf($col)])
                            $bgCol = "bgcolor='#00FF00'";
                        $sewout_html .= '<td ' . $bgCol . '>' . $sewingout_qnty . '</td>';
                        $total_sew_out += $sewingout_qnty;

                        /* if($prodRow[csf("finishin_qnty")]==0)$bgCol="bgcolor='#FF0000'";
                          else if($prodRow[csf("finishin_qnty")] < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'";
                          else if($prodRow[csf("finishin_qnty")] >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";
                          $finisin_html .='<td '.$bgCol.'>'.$prodRow[csf("finishin_qnty")].'</td>';
                          $total_fin_in+=$prodRow[csf("finishin_qnty")]; */

                        if ($finish_qnty == 0)
                            $bgCol = "bgcolor='#FF0000'";
                        else if ($finish_qnty < $resRow[csf($col)])
                            $bgCol = "bgcolor='#FFFF00'";
                        else if ($finish_qnty >= $resRow[csf($col)])
                            $bgCol = "bgcolor='#00FF00'";
                        $finisout_html .= '<td ' . $bgCol . '>' . $finish_qnty . '</td>';
                        $total_fin_out += $finish_qnty;

                        if ($iron_qnty == 0)
                            $bgCol = "bgcolor='#FF0000'";
                        else if ($iron_qnty < $resRow[csf($col)])
                            $bgCol = "bgcolor='#FFFF00'";
                        else if ($iron_qnty >= $resRow[csf($col)])
                            $bgCol = "bgcolor='#00FF00'";
                        $iron_html .= '<td ' . $bgCol . '>' . $iron_qnty . '</td>';
                        $total_iron_out += $iron_qnty;

                        //if($prodRow[csf("iron_qnty")]==0)$bgCol="bgcolor='#FF0000'";
                        //else if($prodRow[csf("iron_qnty")] < $resRow[csf($col)]) $bgCol="bgcolor='#FFFF00'";
                        //else if($prodRow[csf("iron_qnty")] >= $resRow[csf($col)]) $bgCol="bgcolor='#00FF00'";
                        if ($color_variable_setting == 2 || $color_variable_setting == 3) {
                            $bgCol == "bgcolor='#FFFFFF'";
                            $exfact_html .= '<td>' . $ex_fact_qty_arr[$colorId][$sizeId] . '&nbsp;</td>';

                            $total_exfact_qnty += $ex_fact_qty_arr[$colorId][$sizeId];
                        }
                    }// end size foreach loop
                    ?>
                    <tr bgcolor="<? echo $bgcolor1; ?>">
                        <td><b>Cutting</b></td>
                        <? echo $cutting_html; ?>
                        <td><? echo $total_cutting; ?></td>
                    </tr>
                    <tr bgcolor="<? echo $bgcolor2; ?>">
                        <td><b>Print Issue</b></td>
                        <? echo $printiss_html; ?>
                        <td><? echo $total_print_issue; ?></td>
                    </tr>
                    <tr bgcolor="<? echo $bgcolor1; ?>">
                        <td><b>Print Received</b></td>
                        <? echo $printrcv_html; ?>
                        <td><? echo $total_print_rcv; ?></td>
                    </tr>
                    <tr bgcolor="<? echo $bgcolor2; ?>">
                        <td><b>Embro Issue</b></td>
                    <? echo $embroiss_html; ?>
                        <td><? echo $total_embro_issue; ?></td>
                    </tr>
                    <tr bgcolor="<? echo $bgcolor1; ?>">
                        <td><b>Embro Received</b></td>
        <? echo $embrorcv_html; ?>
                        <td><? echo $total_embro_rcv; ?></td>
                    </tr>
                    <tr bgcolor="<? echo $bgcolor2; ?>">
                        <td><b>Issue For Special Works</b></td>
                    <? echo $spiss_html; ?>
                        <td><? echo $total_sp_issue; ?></td>
                    </tr>
                    <tr bgcolor="<? echo $bgcolor1; ?>">
                        <td><b>Recv. From Special Works</b></td>
        <? echo $sprcv_html; ?>
                        <td><? echo $total_sp_rcv; ?></td>
                    </tr>
                    <tr bgcolor="<? echo $bgcolor2; ?>">
                        <td><b>Sewing Input</b></td>
            <? echo $sewin_html; ?>
                        <td><? echo $total_sew_in; ?></td>
                    </tr>
                    <tr bgcolor="<? echo $bgcolor1; ?>">
                        <td><b>Sewing Output</b></td>
        <? echo $sewout_html; ?>
                        <td><? echo $total_sew_out; ?></td>
                    </tr>
                    <tr bgcolor="<? echo $bgcolor2; ?>">
                        <td><b>Issue For Wash</b></td>
        <? echo $washiss_html; ?>
                        <td><? echo $total_wash_issue; ?></td>
                    </tr>
                    <tr bgcolor="<? echo $bgcolor1; ?>">
                        <td><b>Recv. From Wash</b></td>
        <? echo $washrcv_html; ?>
                        <td><? echo $total_wash_rcv; ?></td>
                    </tr>
                    <tr bgcolor="<? echo $bgcolor2; ?>">
                        <td><b>Iron Output</b></td>
        <? echo $iron_html; ?>
                        <td><? echo $total_iron_out; ?></td>
                    </tr>
                    <tr bgcolor="<? echo $bgcolor1; ?>">
                        <td><b>Finishing Output</b></td>
        <? echo $finisout_html; ?>
                        <td><? echo $total_fin_out; ?></td>
                    </tr>
        <?
        if ($color_variable_setting == 2 || $color_variable_setting == 3) {
            ?>
                        <tr>
                            <td><b>Ex-Factory Qty.</b></td>
            <? echo $exfact_html; ?>
                            <td><? echo $total_exfact_qnty; ?>&nbsp;</td>
                        </tr>
            <?
        }
        ?>
        <?
    }// end color foreach loop
    ?>


            </table>
        </div>


    <?
    exit();
}// end if condition
//######################################## ALL POP UP Here END ################################
//#############################################################################################
?>
