<?php
header('Content-type:text/html; charset=utf-8');
session_start();
extract($_REQUEST);
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
require_once('../../../../includes/common.php');
 

$date=date('Y-m-d');

$user_id=$_SESSION['logic_erp']['user_id']; 
$data=$_REQUEST['data']; 
$action=$_REQUEST['action'];

/* 
if($action=="load_drop_down_buyer")
{
	if($data!=0)
	{
		echo create_drop_down( "cbo_buyer_name", 120, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in($data) $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buyer_name","id,buyer_name", 0, "-- Select Buyer --", $selected, " load_drop_down( 'requires/shipment_schedule_controller', this.value, 'load_drop_down_season', 'season_td'); load_drop_down( 'requires/shipment_schedule_controller', this.value, 'load_drop_down_brand', 'brand_td');" );
	}
	else{
		echo create_drop_down( "cbo_buyer_name", 120, "select distinct buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buyer_name","id,buyer_name", 0, "-- Select Buyer --", $selected, " load_drop_down( 'requires/shipment_schedule_controller', this.value, 'load_drop_down_season', 'season_td'); load_drop_down( 'requires/shipment_schedule_controller', this.value, 'load_drop_down_brand', 'brand_td');" );
	}
	exit();
} */

    



if($action=="report_generate")
{

    //echo $cbo_company_name." ".$cbo_buyer_name." ".$txt_style." ".$txt_job." ".$cbo_date_type." ".$txt_date_from." ".$txt_date_to; exit();
    /* <option value="1">Country Ship Date</option>
    <option value="2">Publish Shipment Date</option>
    <option value="3">Original Shipment Date</option>
    <option value="4">PO Insert date</option> */
    //echo $txt_date_to; exit();
    $companyId      = str_replace("'", "", $cbo_company_name);
    $buyerName      = str_replace("'", "", $cbo_buyer_name);
    $styleRef       = str_replace("'", "", $txt_style);
    $jobNumber      = str_replace("'", "", $txt_job);
    $categoryBy     = str_replace("'", "", $cbo_category_by);
    $year           = str_replace("'", "", $cbo_year_selection);
    $cbo_date_type  = str_replace("'", "", $cbo_date_type);
    $txt_date_from  = str_replace("'", "", $txt_date_from);
    $txt_date_to    = str_replace("'", "", $txt_date_to);

    $cond = '';
    if($companyId){ $cond    .= " and a.company_name=$companyId ";}
    if($jobNumber){ $cond    .= " and a.job_no_prefix_num=$jobNumber ";}
    if($styleRef){ $cond     .= " and a.style_ref_no='$styleRef' ";}
    if($buyerName){ $cond    .= " and a.buyer_name=$buyerName ";}
    if($year){$cond          .= " and to_char(a.insert_date, 'YYYY')=$year ";}

    if($txt_date_from != "" && $txt_date_to != ""){
        if($cbo_date_type == 1){$cond    .= " and c.country_ship_date between '$txt_date_from' and '$txt_date_to' ";} //Country Ship Date
        if($cbo_date_type == 2){$cond    .= " and b.pub_shipment_date between '$txt_date_from' and '$txt_date_to' ";} //Publish Shipment Date
        if($cbo_date_type == 3){$cond    .= " and b.shipment_date between '$txt_date_from' and '$txt_date_to' ";} //Original Shipment Date
        if($cbo_date_type == 4){$cond    .= " and b.po_received_date between '$txt_date_from' and '$txt_date_to' ";} //PO Insert date
    }

    //////////////////////////////////////////////////////////////////
	$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
	$buyerArr = return_library_array("select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0 ","id","buyer_name");
	$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0 ","id","supplier_name");
	$trimsGroupArr = return_library_array("select id, item_name from lib_item_group where item_category=4 and status_active=1","id","item_name");
	$colorNameArr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0","id","color_name");
	 
	$machine_noArr = return_library_array("select id, machine_no from lib_machine_name where status_active=1 and is_deleted=0","id","machine_no");
	$item_name_arr = return_library_array("select id, item_name from lib_item_group where item_category=4 and status_active=1 order by item_name","id","item_name");
	//////////////////////////////////////////////////////////////////
    

    $sql = "SELECT a.BUYER_NAME,a.PRODUCT_DEPT, a.COMPANY_NAME, a.STYLE_REF_NO, a.JOB_NO, a.MC_BRAND, a.GAUGE, a.JOB_QUANTITY,a.avg_unit_price,a.REMARKS, MIN(b.pub_shipment_date) as pub_shipment_date, d.SPWORKS, d.smv_pcs, (d.smv_pcs*a.JOB_QUANTITY) as knit_min, d.CUTSMV_PCS, (d.CUTSMV_PCS*a.JOB_QUANTITY) as link_min, d.FINSMV_PCS, (d.FINSMV_PCS*a.JOB_QUANTITY) finish_min, (d.smv_pcs + d.CUTSMV_PCS + d.FINSMV_PCS) as total_smv, ((d.smv_pcs + d.CUTSMV_PCS + d.FINSMV_PCS) * a.JOB_QUANTITY) as total_min, (a.avg_unit_price*a.JOB_QUANTITY) as total_revenue
                FROM WO_PO_DETAILS_MASTER A 
                    INNER JOIN WO_PO_BREAK_DOWN B ON B.job_id=A.ID AND B.STATUS_ACTIVE=1 AND B.IS_DELETED = 0
                    INNER JOIN WO_PO_COLOR_SIZE_BREAKDOWN c ON c.job_id=A.ID and b.id=c.PO_BREAK_DOWN_ID AND c.STATUS_ACTIVE=1 AND c.IS_DELETED = 0
                    LEFT JOIN WO_PO_DETAILS_MAS_SET_DETAILS D ON A.ID=D.JOB_ID 
                    WHERE A.STATUS_ACTIVE=1 AND a.IS_DELETED = 0 $cond
                group by 
                    a.BUYER_NAME, a.PRODUCT_DEPT, a.COMPANY_NAME, a.STYLE_REF_NO, a.JOB_NO, a.MC_BRAND, a.GAUGE, a.JOB_QUANTITY,a.avg_unit_price,a.REMARKS, d.SPWORKS, d.smv_pcs, d.CUTSMV_PCS, d.FINSMV_PCS";
    //echo $sql; exit();

    $data = sql_select($sql);



	$width = 2300;
    ob_start();
	?>
        <style>
            .alignRight{
                text-align: right;
            }
        </style>
        <table  width="<? echo $width;?>" border="1" class="rpt_table" rules="all">
            <thead>
                <tr>
                    <th width="100"><p>Customer/Buyer</p></th>
                    <th width="100"><p>Dept</p></th>
                    <th width="100"><p>PSL/Job</p></th>
                    <th width="100"><p>Buyer Style</p></th>
                    <th width="100"><p>M/C Gauge</p></th>
                    <th width="100"><p>Job Qty</p></th>
                    <th width="100"><p>Publish Ship Date</p></th>
                    <th width="100"><p>Attachment Type</p></th>
                    <th width="100"><p>Fob price</p></th>
                    <th width="100"><p>CM</p></th>
                    <th width="100"><p>Knit min</p></th>
                    <th width="100"><p>Knit Min(TTL)</p></th>
                    <th width="100"><p>Link Min</p></th>
                    <th width="100"><p>Link Min(TTL)</p></th>
                    <th width="100"><p>Finish Min</p></th>
                    <th width="100"><p>Fin Min (TTL)</p></th>
                    <th width="100"><p>TTL Min/pc (Total SMV)</p></th>
                    <th width="100"><p>Total Minute</p></th>
                    <th width="100"><p>SMV</p></th>
                    <th width="100"><p>Total Revenue</p></th>
                    <th width="100"><p>Total CM</p></th>
                    <th width="100"><p>CM% Fob</p></th>
                    <th width="100"><p>Remarks</p></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($data as $row)
                    {
                        ?>
                        <tr>
                            <td width="100"><p><?=$buyerArr[$row['BUYER_NAME']]?></p></td>
                            <td width="100"><p><?=$product_dept[$row['PRODUCT_DEPT']]?></p></td>
                            <td width="100"><p><?=$row['JOB_NO']?></p></td>
                            <td width="100"><p><?=$row['STYLE_REF_NO']?></p></td>
                            <td width="100"><p><?=$row['GAUGE']." ".$row['MC_BRAND']?></p></td>
                            <td width="100" class="alignRight"><p><?=$row['JOB_QUANTITY']?></p></td>
                            <td width="100"><p><?=$row['PUB_SHIPMENT_DATE']?></p></td>
                            <td width="100"><p><?=$row['SPWORKS'] == 1? "YES" : 'NO'; ?></p></td>
                            <td width="100" class="alignRight"><p><?=$row['AVG_UNIT_PRICE']?></p></td>
                            <td width="100" class="alignRight"><p><?=NULL?></p></td>
                            <td width="100" class="alignRight"><p><?=$row['SMV_PCS']?></p></td>
                            <td width="100" class="alignRight"><p><?=$row['KNIT_MIN']?></p></td>
                            <td width="100" class="alignRight"><p><?=$row['CUTSMV_PCS']?></p></td>
                            <td width="100" class="alignRight"><p><?=$row['LINK_MIN']?></p></td>
                            <td width="100" class="alignRight"><p><?=$row['FINSMV_PCS']?></p></td>
                            <td width="100" class="alignRight"><p><?=$row['FINISH_MIN']?></p></td>
                            <td width="100" class="alignRight"><p><?=$row['TOTAL_SMV']?></p></td>
                            <td width="100" class="alignRight"><p><?=$row['TOTAL_MIN']?></p></td>
                            <td width="100" class="alignRight"><p><?=NULL?></p></td>
                            <td width="100" class="alignRight"><p><?=$row['TOTAL_REVENUE']?></p></td>
                            <td width="100" class="alignRight"><p><?=NULL?></p></td>
                            <td width="100" class="alignRight"><p><?=NULL?></p></td>
                            <td width="100"><p><?=$row['REMARKS']?></p></td>
                        </tr>
                        <?php 
                        $sumJobQty += $row['JOB_QUANTITY']; 
                        $sumKnitMin += $row['SMV_PCS']; 
                        $totalKnitMin += $row['KNIT_MIN']; 
                        $sumCutMin += $row['CUTSMV_PCS']; 
                        $totalLinkMin += $row['LINK_MIN']; 
                        $sumFinishMin += $row['FINSMV_PCS']; 
                        $totalFinishMin += $row['FINISH_MIN']; 
                        $sumSMV += $row['TOTAL_SMV']; 
                        $sumMinutes += $row['TOTAL_MIN']; 
                        $sumRevenue += $row['TOTAL_REVENUE']; 
                    } 
                    ?>
            </tbody>
            <tfoot>
                <tr>
                    <th width="100"><p></p></th>
                    <th width="100"><p></p></th>
                    <th width="100"><p></p></th>
                    <th width="100"><p></p></th>
                    <th width="100"><p></p></th>
                    <th width="100"><p><?=$sumJobQty?></p></th>
                    <th width="100"><p></p></th>
                    <th width="100"><p></p></th>
                    <th width="100"><p></p></th>
                    <th width="100"><p></p></th>
                    <th width="100"><p><?=$sumKnitMin?></p></th>
                    <th width="100"><p><?=$totalKnitMin?></p></th>
                    <th width="100"><p><?=$sumCutMin?></p></th>
                    <th width="100"><p><?=$totalLinkMin?></p></th>
                    <th width="100"><p><?=$sumFinishMin?></p></th>
                    <th width="100"><p><?=$totalFinishMin?></p></th>
                    <th width="100"><p><?=$sumSMV?></p></th>
                    <th width="100"><p><?=$sumMinutes?></p></th>
                    <th width="100"><p><?=NULL?></p></th>
                    <th width="100"><p><?=$sumRevenue?></p></th>
                    <th width="100"><p><?=NULL?></p></th>
                    <th width="100"><p><?=NULL?></p></th>
                    <th width="100"><p><?=NULL?></p></th>
                </tr>
            </tfoot>
        </table>














    <?

    $html = ob_get_contents();
    ob_clean();
    //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
    foreach (glob("*.xls") as $filename) {
    //if( @filemtime($filename) < (time()-$seconds_old) )
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, $html);
    echo "$html####$filename####$rpt_type";
    exit();
}