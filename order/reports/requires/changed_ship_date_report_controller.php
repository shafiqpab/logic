<?
session_start();
require_once('../../../includes/common.php');
require_once('../../../includes/class4/class.conditions.php');
require_once('../../../includes/class4/class.reports.php');
require_once('../../../includes/class4/class.trims.php');

extract($_REQUEST);
if ($_SESSION['logic_erp']['user_id'] == "") {
    header("location:login.php");
    die;
}

$date = date('Y-m-d');

if ($action == "load_drop_down_buyer") {
    echo create_drop_down("cbo_buyer_name", 110, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "load_drop_down( 'requires/changed_ship_date_report_controller', this.value, 'load_drop_down_season', 'season_td');");
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

$buyer_short_name_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
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
    $txt_internal_ref = str_replace("'", "", $txt_internal_ref);
    $txt_order_no = str_replace("'", "", $txt_order_no);
    $type_search = str_replace("'", "", $cbo_search_by);
    $team_name_arr= return_library_array("select id,team_leader_name from lib_marketing_team where project_type=1 and team_type in (0,1,2) and status_active =1 and is_deleted=0 order by team_leader_name", "id", "team_leader_name");
    $user_name_arr=return_library_array( "select id, user_name from user_passwd ",'id','user_name');
    $location_name_arr=return_library_array( "select id, location_name from lib_location ",'id','location_name');


    if ($job_no == "") $job_no_cond = "";
    else $job_no_cond = " and a.job_no_prefix_num in ($job_no) ";

    if ($txt_internal_ref=="") $internal_ref_cond=""; else $internal_ref_cond=" and b.grouping='".trim($txt_internal_ref)."' "; 

    if ($txt_order_no=="") $order_no_cond=""; else $order_no_cond=" and b.po_number='".trim($txt_order_no)."' ";




    if (str_replace("'", "", $cbo_search_by) == 1){
        if (str_replace("'", "", trim($txt_date_from)) == "" && str_replace("'", "", trim($txt_date_to)) == "")
            $date_cond = "";
        else
            $date_cond = " and c.country_ship_date between $txt_date_from and $txt_date_to";
    }
    else {
        if (str_replace("'", "", trim($txt_date_from)) == "" && str_replace("'", "", trim($txt_date_to)) == "")
            $date_cond = "";
        else
            $date_cond = " and a.insert_date between $txt_date_from and $txt_date_to";
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

    ob_start();
    ?>
    <div align="center">

       
        <table cellpadding="0" cellspacing="0" width="">
            <tr>
                <td align="center" class="form_caption" width="100%" colspan="18"><b><?php echo $company_short_name_arr[str_replace("'", "", $cbo_company_name)]; ?></b></td>
            </tr>
        </table>
        <table width="1570" id="table_header_1" border="1" class="rpt_table" rules="all">
                        <thead>
                            <tr>
                                <th width="40">SL</th>
                                <th width="100">Location</th>
                                <th width="100">Team Member</th>
                                <th width="100">Buyer</th>
                                <th width="100">Style</th>
                                <th width="100">Order No</th>
                                <th width="100">Job No</th>
                                <th width="80">Internal Ref:</th>
                                <th width="100">Item</th>  
                                <th width="80">Insert Date</th>  
                                <th width="80">Last Country Ship Date</th>
                                <th width="80">Current Country Ship Date</th>                        
                                <th width="50">SMV</th>                            
                                <th width="100">Order Quantity</th>
                                <th width="60">UOM</th>
                                <th width="100">Order Qnty (Pcs)</th>
                                <th width="100">Order Status</th>
                                <th width="">Insert By</th>
                            </tr>
                        </thead>
                    </table>
                    <div style="width:1590" align="left" id="scroll_body">
                        <table width="1570" border="1" class="rpt_table" rules="all" id="table_body">
                            <?
                            $i=1;
                    $jobSql =sql_select("select a.job_no_prefix_num, a.job_no,a.location_name, a.company_name, a.buyer_name,a.inserted_by, a.agent_name, a.style_ref_no, a.job_quantity, a.product_category, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant, listagg(cast(b.id as varchar2(4000)),',') within group (order by b.id) as id, sum(b.po_quantity*a.total_set_qnty) as po_quantity, sum(b.plan_cut*a.total_set_qnty) as plan_cut, MIN(b.pub_shipment_date) as pub_shipment_date, MIN(b.po_received_date) as po_received_date, trunc(MIN(b.pub_shipment_date)-SYSDATE) date_diff_1, trunc(MIN(b.shipment_date)-SYSDATE) date_diff_2, sum(b.po_total_price) as po_total_price, max(b.shiping_status) as shiping_status,listagg(cast(b.grouping as varchar2(4000)),',') within group (order by b.id) as grouping,listagg(cast(b.file_no as varchar2(4000)),',') within group (order by b.id) as file_no,max(b.insert_date) as insert_date,a.set_smv,b.details_remarks,c.country_ship_date,c.country_ship_date_prev,b.is_confirmed,b.po_number from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst  and a.job_no=c.job_no_mst and b.job_no_mst=c.job_no_mst  and a.status_active=1 and b.status_active=1 and c.status_active=1 $internal_ref_cond $date_cond $searchCond $job_no_cond $order_no_cond and c.country_ship_date_prev is not null group by a.job_no, a.job_no_prefix_num,a.set_smv, a.company_name, a.buyer_name, a.agent_name, a.style_ref_no, a.job_quantity, a.product_category, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant,b.details_remarks,c.country_ship_date,c.country_ship_date_prev,a.inserted_by,b.is_confirmed,a.location_name,b.po_number order by a.job_no desc");
                    foreach ($jobSql as $row){
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                <td width="40" bgcolor="<? echo $color; ?>"> <? echo $i; ?></td>
                                <td width="100" align="center"><p><? echo $location_name_arr[$row[csf('location_name')]]; ?></td>
                                <td width="100"><p><? echo $team_name_arr[$row[csf('team_leader')]]; ?></p></td>
                                <td width="100"><p><? echo $buyer_short_name_arr[$row[csf('buyer_name')]]; ?></p></td>
                                <td width="100"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
                                <td width="100"><p><? echo $row[csf('po_number')]; ?></p></td>
                                <td width="100" align="center"><p><? echo $row[csf('job_no')]; ?></td>
                                <td width="80"><p><? echo implode(",",array_filter(array_unique(explode(",",$row[csf('grouping')])))); ?>&nbsp;</p></td>
                                <td width="100"><p><? echo $garments_item[$row[csf('gmts_item_id')]]; ?></p></td>
                                <td width="80" align="center"><? echo change_date_format($row[csf('insert_date')]); ?></td>
                                <td width="80" align="center"><? echo change_date_format($row[csf('country_ship_date_prev')]); ?></td>
                                <td width="80" align="center"><? echo change_date_format($row[csf('country_ship_date')]); ?></td>
                                <td width="50" align="center"><p><? echo $row[csf('set_smv')]; ?></p></td>
                                <td width="100" align="right"><? echo number_format(($row[csf('po_quantity')] * $row[csf('total_set_qnty')]), 0); ?></td> 
                                <td width="60" align="center"><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?></td>
                                <td width="100" align="right"><?
                                    $poQtyPcs = $row[csf('po_quantity')] * $row[csf('total_set_qnty')];
                                    echo $poQtyPcs;
                                    ?></td>
                                
                                <td align="center" width="100"><p><? echo $order_status[$row[csf('is_confirmed')]];?>&nbsp;</p></td>
                                <td align="center" width=""><p><? echo $user_name_arr[$row[csf('inserted_by')]]; ?>&nbsp;</p></td>
                            </tr>
                            <?
                            $i++;
                            $tot_po_qnty+=$poQtyPcs;
                        }
                    ?>
                    </table>
                    <table border="1" class="rpt_table"  width="1570" rules="all" id="report_table_footer" >
                            <tfoot>
                                <th width="40">&nbsp;</th>
                                <th width="100">&nbsp;</th>
                                <th width="100">&nbsp;</th>
                                <th width="100">&nbsp;</th>
                                <th width="100">&nbsp;</th>
                                <th width="100">&nbsp;</th>
                                <th width="100">&nbsp;</th>
                                <th width="80">&nbsp;</th>
                                <th width="100">&nbsp;</th>  
                                <th width="80">&nbsp;</th>  
                                <th width="80">&nbsp;</th>
                                <th width="80">&nbsp;</th>                        
                                <th width="50">&nbsp;</th>                            
                                <th width="100"><? echo $tot_po_qnty;?></th>
                                <th width="60">&nbsp;</th>
                                <th width="100"><? echo $tot_po_qnty;?></th>
                                <th width="100">&nbsp;</th>
                                <th width="">&nbsp;</th>  
                            </tfoot>
                        </table>
                </div> 
        </div>
        <?
        $html = ob_get_contents();
        ob_clean();
        $new_link = create_delete_report_file($html, 1, 1, "../../");

        echo "$html**".$garmentBtn;
        exit();
    }






//######################################## ALL POP UP Here START ##############################
//#############################################################################################

   



?>	
