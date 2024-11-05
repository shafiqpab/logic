<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../includes/common.php');
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_name=$_SESSION['logic_erp']['user_id'];
$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer where status_active=1 and is_deleted=0",'id','buyer_name');
$company_arr=return_library_array( "select id, company_name from lib_company where status_active=1 and is_deleted=0",'id','company_name');
$dealing_arr=return_library_array( "select id, team_member_name from lib_mkt_team_member_info where status_active=1 and is_deleted=0",'id','team_member_name');
$sample_name_arr=return_library_array( "select id,sample_name  from lib_sample where is_deleted=0 and status_active=1 order by sample_name", "id", "sample_name"  );
$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0","id","color_name");
$size_arr=return_library_array( "select id, size_name from lib_size where status_active=1 and is_deleted=0","id","size_name");
$season_arr=return_library_array( "select id, season_name from lib_buyer_season where status_active=1 and is_deleted=0","id","season_name");

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 110, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- Select --", $selected, "" );
	exit();
}

if($action=="report_generate")
{
    $process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
    $sample_stage = str_replace("'", "", $cbo_sample_stage);
    $company_name = str_replace("'", "", $cbo_company_name);
    $buyer_name = str_replace("'", "", $cbo_buyer_name);
    $req_no = str_replace("'", "", $txt_req_no);
    $booking_no = str_replace("'", "", $txt_booking_no);
    $style_ref = str_replace("'", "", $txt_style_ref);
    $sample_name = str_replace("'", "", $txt_sample_name);
    $txt_date_from = str_replace("'", "", $txt_date_from);
    $txt_date_to = str_replace("'", "", $txt_date_to);
    if($sample_stage != '') $condition.="and d.sample_stage_id in ($sample_stage)"; else $condition.="";
    if($company_name != '') $condition.=" and a.company_id = $company_name"; else $condition.="";
    if($buyer_name != 0) $condition.=" and d.buyer_name = $buyer_name"; else $condition.="";
    if($req_no != '') $condition.=" and d.requisition_number_prefix_num like '%$req_no%'"; else $condition.="";
    if($style_ref != '') $condition.=" and d.style_ref_no = '$style_ref'"; else $condition.="";
    if($sample_name != 0) $condition.=" and e.sample_name = $sample_name"; else $condition.="";
    if($db_type==0)
    {
        if ($txt_date_from!="" &&  $txt_date_to!="") $condition  .= " and b.DELIVERY_DATE  between '".change_date_format($txt_date_from, "yyyy-mm-dd", "-")."' and '".change_date_format($txt_date_to, "yyyy-mm-dd", "-")."'"; else $condition .="";
    }
    if($db_type==2)
    {
        if ($txt_date_from!="" &&  $txt_date_to!="") $condition  .= " and b.DELIVERY_DATE  between '".change_date_format($txt_date_from, "yyyy-mm-dd", "-",1)."' and '".change_date_format($txt_date_to, "yyyy-mm-dd", "-",1)."'"; else $condition .="";
    }

    $req_wise_booking=return_library_array( "select style_id, booking_no from wo_non_ord_samp_booking_dtls where status_active=1",'style_id','booking_no');
    $last_delivery_date=return_library_array( "select max(a.ex_factory_date) as delivery_date, b.sample_development_id from sample_ex_factory_mst a join sample_ex_factory_dtls b on a.id=b.sample_ex_factory_mst_id where a.status_active=1 group by b.sample_development_id",'sample_development_id','delivery_date');
    $buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
    $season_arr=return_library_array( "select id,season_name from lib_buyer_season  where status_active =1 and is_deleted=0 ", "id", "season_name"  );
    $sample_library=return_library_array( "select id, sample_name from lib_sample", "id", "sample_name");
    $color_arr=return_library_array( "select id,color_name from lib_color", "id","color_name" );
    $size_arr=return_library_array( "select id,size_name from lib_size", "id", "size_name" );

    //echo "SELECT a.delivery_to,b.sample_dtls_part_tbl_id,b.sample_development_id,c.sample_ex_factory_dtls_id, c.size_pass_qty, d.buyer_name, d.requisition_number, d.style_ref_no, d.season, e.sample_name, e.gmts_item_id, e.smv, e.sample_color, e.size_data from sample_ex_factory_mst a join sample_ex_factory_dtls b on a.id=b.sample_ex_factory_mst_id join sample_ex_factory_colorsize c on a.id=b.sample_ex_factory_mst_id and b.id=c.sample_ex_factory_dtls_id join sample_development_mst d on b.sample_development_id=d.id join sample_development_dtls e on d.id=e.sample_mst_id and b.sample_dtls_part_tbl_id=e.id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $condition"; die;
    
 	$booking_data = sql_select("SELECT a.delivery_to,b.sample_dtls_part_tbl_id,b.remarks,b.sample_development_id,c.sample_ex_factory_dtls_id, c.size_pass_qty, d.buyer_name, d.requisition_number, d.style_ref_no, d.season, e.sample_name, e.gmts_item_id, e.smv, e.sample_color, e.size_data from sample_ex_factory_mst a join sample_ex_factory_dtls b on a.id=b.sample_ex_factory_mst_id join sample_ex_factory_colorsize c on a.id=b.sample_ex_factory_mst_id and b.id=c.sample_ex_factory_dtls_id join sample_development_mst d on b.sample_development_id=d.id join sample_development_dtls e on d.id=e.sample_mst_id and b.sample_dtls_part_tbl_id=e.id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $condition");
	
    $latest_sewing_date = sql_select("SELECT a.sample_development_id, max(b.sewing_date) as sew_date , sum(b.qc_pass_qty) as qty,b.sample_dtls_row_id from sample_sewing_output_mst a join sample_sewing_output_dtls b on a.id = b.sample_sewing_output_mst_id where a.entry_form_id=130 group by a.sample_development_id,b.sample_dtls_row_id");
    foreach ($latest_sewing_date as $data) {
        $sewing_arr[$data[csf('sample_development_id')]][$data[csf('sample_dtls_row_id')]]['date'] = $data[csf('sew_date')];
        $sewing_arr[$data[csf('sample_development_id')]][$data[csf('sample_dtls_row_id')]]['qty'] = $data[csf('qty')];
    }
    /*echo '<pre>';
    print_r($sewing_arr); die;*/
    ?>
    <script type="text/javascript">setFilterGrid('table_body',-1);</script>
    <div>
        <table cellspacing="0" cellpadding="0" border="1" class="rpt_table" width="3630" rules="all" id="table_header" >
            <thead>
                <tr>
                    <th width="30">SL </th>
                    <th width="95">Buyer</th>
                    <th width="95">Requisition No.</th>
                    <th width="90">Style Name</th>
                    <th width="110">Season</th>
                    <th width="100">Booking No.</th>
                    <th width="100">Sample Name</th>
                    <th width="110">Garments Item</th>
                    <th width="50">SMV</th>
                    <th width="90">Colour</th>
                    <th width="90">Size</th>
                    <th width="80">Spl Sweing Date</th>
                    <th width="80">Last Delivery Date</th>
                    <th width="70">Approval Status</th>
                    <th width="80">Status Date</th>
                    <th width="105">Comments</th>
                    <th width="115">Total Sweing Oty</th>
                    <?
                        foreach ($sample_sent_to_list as $data) {
                            ?>
                            <th width="70"><? echo $data ?></th>
                            <?
                        }
                    ?>
                    <th width="90">Total Distributed Qty</th>
                    <th width="80">In-Hand Qty</th>
                    <th width="120">Remarks</th>
                </tr>
            </thead>
        </table>
        <div style="max-height:320px; overflow-y:scroll; width:3650px;" id="scroll_body">
        <table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="3630" rules="all" id="table_body">
            <tbody>
               <?
                $i=1;$tot_distributedQty=$tot_in_handQty=$tot_sewing_out=0;
                foreach ($booking_data as $key => $row)
                {
                    if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$sewing_out=$sewing_arr[$row[csf('sample_development_id')]][$row[csf('sample_dtls_part_tbl_id')]]['qty'];
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                    <td width="30"><? echo $i;?></td>
                    <td width="95"><? echo $buyer_arr[$row[csf('buyer_name')]] ?></td>
                    <td width="95"><? echo $row[csf('requisition_number')] ?></td>
                    <td width="90"><? echo $row[csf('style_ref_no')] ?></td>
                    <td width="110"><? echo $season_arr[$row[csf('season')]] ?></td>
                    <td width="100"><? echo $req_wise_booking[$row[csf('sample_development_id')]] ?></td>
                    <td width="100"><? echo $sample_library[$row[csf('sample_name')]] ?></td>
                    <td width="110"><? echo $garments_item[$row[csf('gmts_item_id')]] ?></td>
                    <td width="50"><? echo $row[csf('smv')] ?></td>
                    <td width="90"><? echo $color_arr[$row[csf('sample_color')]] ?></td>
                    <td width="90"><? echo $size_arr[$row[csf('size_data')]] ?></td>
                    <td width="80"><? echo change_date_format($sewing_arr[$row[csf('sample_development_id')]][$row[csf('sample_dtls_part_tbl_id')]]['date'],"yyyy-mm-dd", "-") ?></td>
                    <td width="80"><? echo change_date_format($last_delivery_date[$row[csf('sample_development_id')]],"yyyy-mm-dd", "-")  ?></td>
                    <td width="70">Approval Status</td>
                    <td width="80">Status Date</td>
                    <td width="105">Comments</td>
                    <td width="115" align="right"><? echo $sewing_arr[$row[csf('sample_development_id')]][$row[csf('sample_dtls_part_tbl_id')]]['qty']; ?></td>
                    <?
                        foreach ($sample_sent_to_list as $key=>$data) {
                            ?>
                            <td width="70"><? 
                                if($row[csf('delivery_to')] == $key)
                                {
                                    echo $row[csf('size_pass_qty')];
                                }
                                else
                                {
                                    echo '';
                                }
                             ?></td>
                            <?
                        }
                    ?>
                    <td width="90"  align="right"><? echo number_format($row[csf('size_pass_qty')],2); ?></td>
                    <td width="80"  align="right" title="Sew Out-Distributed Qty"><? echo number_format($sewing_out-$row[csf('size_pass_qty')],2); ?></td>
                    <td width="120"><p><? echo $row[csf('remarks')]; ?></p></td>
                </tr>
                <? 
				$tot_distributedQty+=$row[csf('size_pass_qty')];
				$tot_in_handQty+=$sewing_out-$row[csf('size_pass_qty')];
				$tot_sewing_out+=$sewing_out;
				$i++;
				} ?>
            </tbody>
            <tfoot>
            <tr>
            <th colspan="16" align="right">Total </th>
             <th  align="right"><?=number_format($tot_sewing_out,2);?> </th>
             <th colspan="<?=count($sample_sent_to_list);?>"> &nbsp; </th>
             <th align="right"><? echo number_format($tot_distributedQty,2); ?> </th>
             <th align="right"><? echo number_format($tot_in_handQty,2); ?> </th>
             <th>&nbsp;  </th>
            </tr>
            </tfoot>
        </table>
        </div>

    <?
    exit();
}

if($action=='prod_qty_popup')
{
    extract($_REQUEST);
    echo load_html_head_contents("Sample Prod. Qty", "../../", 1, 1,$unicode,'','');
 ?>
    <fieldset>
    <legend> Sample Production Qty.</legend>
     <div style="width:610px; margin-top:10px">
        <table cellspacing="0" width="100%" class="rpt_table" cellpadding="0" border="1" rules="all">
            <thead>
                <th width="30" >SL</th>
                <th width="90" >Size</th>
                <th width="80" >BH Qty</th>
                <th width="80" >Plan Qty</th>
                <th width="80" >Dyeing Qty</th>
                <th width="80" >Test Qty</th>
                <th width="80" >Self Qty</th>
                <th width="">Total Qty</th>
            </thead>

        <?
             $sql= "select b.size_id,b.bh_qty,b.plan_qty ,b.dyeing_qty, b.test_qty ,b.self_qty ,b.total_qty from sample_development_dtls a,sample_development_size b where a.id=b.dtls_id and a.entry_form_id in(117,203) and a.status_active=1 and a.is_deleted=0 and  a.status_active=1 and a.is_deleted=0 and a.id='$sample_dtls_id' order by b.id";
              //  echo $sql;die;
           //  $arr=array(0=>$size_arr);
          //   echo  create_list_view ( "list_view_1", "Size,BH Qty,Plan Qty,Dyeing Qty,Test Qty,Self,Total", "90,80,80,80,80,80,80","610","220",1, $sql, "", "","", 1, 'size_id,0,0,0,0,0,0', $arr, "size_id,bh_qty,plan_qty ,dyeing_qty, test_qty ,self_qty ,total_qty", "../requires/sample_progress_report_controller", '','');
             $sql_sel=sql_select($sql);
             $i=1;
             foreach($sql_sel as $row)
             {
                $total_bh+=$row[csf("bh_qty")];
                $total_pl+=$row[csf("plan_qty")];
                $total_dy+=$row[csf("dyeing_qty")];
                $total_test+=$row[csf("test_qty")];
                $total_self+=$row[csf("self_qty")];
                $total+=$row[csf("total_qty")];
                ?>


            <tr>
                <td width="30" align="center"><? echo $i; ?></td>
                <td width="90" align="center"><? echo  $size_arr[$row[csf("size_id")]]; ?></td>
                <td width="80" align="center"><? echo  $row[csf("bh_qty")]; ?></td>
                <td width="80" align="center"><? echo  $row[csf("plan_qty")]; ?></td>
                <td width="80" align="center"><? echo  $row[csf("dyeing_qty")]; ?></td>
                <td width="80" align="center"><? echo  $row[csf("test_qty")]; ?></td>
                <td width="80" align="center"><? echo  $row[csf("self_qty")]; ?></td>
                <td width="" align="center">   <? echo  $row[csf("total_qty")]; ?></td>
            </tr>

             <?
             $i++;
             }
         ?>
             <tr>
                <td colspan="2" >  </td>
                 <td align="center"> <? echo $total_bh; ?></td>
                <td align="center"> <? echo $total_pl; ?></td>
                <td align="center"> <? echo $total_dy; ?></td>
                <td align="center"> <? echo $total_test; ?></td>
                <td align="center"> <? echo $total_self; ?></td>
                <td align="center">  <? echo $total; ?></td>

            </tr>

        </table>
     </div>
    </fieldset>

 <?
 exit();
}

if($action=='cutting_qty_popup')
{
    extract($_REQUEST);
    echo load_html_head_contents("Cutting  Qty", "../../", 1, 1,$unicode,'','');
 ?>
    <fieldset>
    <legend> Cutting Qty.</legend>
     <div style="width:370px; margin-top:10px">
        <table cellspacing="0" width="100%" class="rpt_table" cellpadding="0" border="1" rules="all">
            <thead>
                <th width="40" >SL</th>
                <th width="110" >Size</th>
                <th width="110" >Output Qty</th>

            </thead>

        <?
            $sql="select b.size_id,sum(b.size_pass_qty) as qty from sample_sewing_output_dtls a,sample_sewing_output_colorsize b where a.id=b.sample_sewing_output_dtls_id and a.entry_form_id=127 and b.entry_form_id=127 and a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and a.sample_dtls_row_id='$sample_dtls_id' group by b.size_id";
            $sql_sel=sql_select($sql);
            $i=1;
            foreach($sql_sel as $row)
            {
                $total+=$row[csf("qty")];
                ?>


            <tr>
                <td width="40" align="center"><? echo $i; ?></td>
                <td width="110" align="center"><? echo  $size_arr[$row[csf("size_id")]]; ?></td>
                <td width="110" align="center"><? echo  $row[csf("qty")]; ?></td>
            </tr>

             <?
             $i++;
             }
         ?>
             <tr>
                 <td colspan="2"></td>
                 <td align="center">  <? echo $total; ?></td>

            </tr>

        </table>
     </div>
    </fieldset>

 <?
 exit();
}

if($action=='sewing_qty_popup')
{
	extract($_REQUEST);
	echo load_html_head_contents("Sewing Qty", "../../", 1, 1,$unicode,'','');
	if($entry_form==130) $caption_name="Output Qty"; else if($entry_form==337) $caption_name="Input Qty";
	?>
	<fieldset>
        <legend>Sewing Info</legend>
        <div style="width:370px; margin-top:10px">
            <table cellspacing="0" width="100%" class="rpt_table" cellpadding="0" border="1" rules="all">
                <thead>
                    <th width="40">SL</th>
                    <th width="110">Size</th>
                    <th width="110"><? echo $caption_name; ?></th>
                </thead>
                <?
                $sql="select b.size_id, sum(b.size_pass_qty) as qty from sample_sewing_output_dtls a, sample_sewing_output_colorsize b where a.id=b.sample_sewing_output_dtls_id and a.entry_form_id='$entry_form' and b.entry_form_id='$entry_form' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.sample_dtls_row_id='$sample_dtls_id' group by b.size_id";
                $sql_sel=sql_select($sql);
                $i=1;
                foreach($sql_sel as $row)
                {
					$total+=$row[csf("qty")];
					?>
					<tr>
                        <td width="40" align="center"><? echo $i; ?></td>
                        <td width="110" align="center"><? echo $size_arr[$row[csf("size_id")]]; ?></td>
                        <td width="110" align="center"><? echo $row[csf("qty")]; ?></td>
					</tr>
					<?
					$i++;
                }
                ?>
                <tr>
                    <td colspan="2">Total:</td>
                    <td align="center"><? echo $total; ?></td>
                </tr>
            </table>
        </div>
	</fieldset>
	<?
	exit();
}

if($action=='dyeing_qty_popup')
{
    extract($_REQUEST);
    echo load_html_head_contents("Dyeing Qty", "../../", 1, 1,$unicode,'','');
 ?>
    <fieldset>
    <legend> Dyeing Quantity</legend>
     <div style="width:370px; margin-top:10px">
        <table cellspacing="0" width="100%" class="rpt_table" cellpadding="0" border="1" rules="all">
            <thead>
                <th width="40" >SL</th>
                <th width="110" >Size</th>
                <th width="110" >Output Qty</th>

            </thead>

        <?
            $sql="select b.size_id,sum(b.size_pass_qty) as qty from sample_sewing_output_dtls a,sample_sewing_output_colorsize b where a.id=b.sample_sewing_output_dtls_id and a.entry_form_id=131 and b.entry_form_id=131 and a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and a.sample_dtls_row_id='$sample_dtls_id' and a.embel_name=5 group by b.size_id";
            $sql_sel=sql_select($sql);
            $i=1;
            foreach($sql_sel as $row)
            {
                $total+=$row[csf("qty")];
                ?>


            <tr>
                <td width="40" align="center"><? echo $i; ?></td>
                <td width="110" align="center"><? echo  $size_arr[$row[csf("size_id")]]; ?></td>
                <td width="110" align="center"><? echo  $row[csf("qty")]; ?></td>
            </tr>

             <?
             $i++;
             }
         ?>
             <tr>
                 <td colspan="2"></td>
                 <td align="center">  <? echo $total; ?></td>

            </tr>

        </table>
     </div>
    </fieldset>

 <?
 exit();
}

if($action=='wash_qty_popup')
{
    extract($_REQUEST);
    echo load_html_head_contents("Wash Qty", "../../", 1, 1,$unicode,'','');
 ?>
    <fieldset>
    <legend> Wash Quantity</legend>
     <div style="width:370px; margin-top:10px">
        <table cellspacing="0" width="100%" class="rpt_table" cellpadding="0" border="1" rules="all">
            <thead>
                <th width="40" >SL</th>
                <th width="110" >Size</th>
                <th width="110" >Output Qty</th>

            </thead>

        <?
            $sql="select b.size_id,sum(b.size_pass_qty) as qty from sample_sewing_output_dtls a,sample_sewing_output_colorsize b where a.id=b.sample_sewing_output_dtls_id and a.entry_form_id=131 and b.entry_form_id=131 and a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and a.sample_dtls_row_id='$sample_dtls_id' and a.embel_name=3 group by b.size_id";
            $sql_sel=sql_select($sql);
            $i=1;
            foreach($sql_sel as $row)
            {
                $total+=$row[csf("qty")];
                ?>


            <tr>
                <td width="40" align="center"><? echo $i; ?></td>
                <td width="110" align="center"><? echo  $size_arr[$row[csf("size_id")]]; ?></td>
                <td width="110" align="center"><? echo  $row[csf("qty")]; ?></td>
            </tr>

             <?
             $i++;
            }
         ?>
             <tr>
                 <td colspan="2"></td>
                 <td align="center">  <? echo $total; ?></td>

            </tr>

        </table>
     </div>
    </fieldset>

 <?
 exit();
}

if($action=='order_qty_popup')
{
    extract($_REQUEST);
    echo load_html_head_contents("PO Info", "../../", 1, 1,$unicode,'','');
 ?>
    <fieldset>
    <legend> Order Info</legend>
     <div style="width:470px; margin-top:10px">
        <table cellspacing="0" width="100%" class="rpt_table" cellpadding="0" border="1" rules="all">
            <thead>
                <th width="40" >SL</th>
                <th width="80" >Job No</th>
                <th width="80" >Order No.</th>
                <th width="90" >Color</th>
                <th width="90" >Qty</th>
                <th>Ship Date</th>
            </thead>

        <?
            $sql="select  a.job_no,b.po_number,b.po_quantity ,b.shipment_date ,c.color_number_id from  wo_po_details_master  a,wo_po_break_down  b,wo_po_color_size_breakdown c where a.job_no =b.job_no_mst and b.job_no_mst =c.job_no_mst and b.id=c.po_break_down_id and a.id='$order_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0    group by a.job_no,b.po_number,b.po_quantity ,b.shipment_date ,c.color_number_id";
            $sql_sel=sql_select($sql);
             $i=1;
            foreach($sql_sel as $row)
            {
                 ?>
             <tr>
                <td width="40" align="center"><? echo $i; ?></td>
                <td width="80" align="center"><? echo $row[csf("job_no")] ; ?></td>
                <td width="80" align="center"><? echo  $row[csf("po_number")]; ?></td>
                 <td align="center"><? echo  $color_arr[$row[csf("color_number_id")]]; ?></td>
                <td width="90" align="center"><? echo  $row[csf("po_quantity")]; ?></td>
                <td width="90" align="center"><? echo  $row[csf("shipment_date")]; ?></td>

            </tr>

             <?
             $i++;
            }
         ?>
             <tr>
                 <td colspan="2"></td>
                 <td align="center">  <? echo $total; ?></td>

            </tr>

        </table>
     </div>
    </fieldset>

 <?
 exit();
}

if($action=='buyer_approval_popup')
{
    extract($_REQUEST);
    echo load_html_head_contents("Approval Status", "../../", 1, 1,$unicode,'','');
    $sql=sql_select("select a.buyer_name,a.requisition_number,a.requisition_number_prefix_num,a.company_id,a.style_ref_no,b.id as dtls_id  from sample_development_mst a , sample_development_dtls b where a.id=b.sample_mst_id and b.entry_form_id in(117,203) and b.status_active=1 and b.is_deleted=0 and b.id='$sample_dtls_id'");
    foreach($sql as $value)
    {
       $requisition_info_arr[$value[csf("dtls_id")]]["comp"]=$value[csf("company_id")];
       $requisition_info_arr[$value[csf("dtls_id")]]["buyer"]=$value[csf("buyer_name")];
       $requisition_info_arr[$value[csf("dtls_id")]]["req"]=$value[csf("requisition_number")];
       $requisition_info_arr[$value[csf("dtls_id")]]["req_no"]=$value[csf("requisition_number_prefix_num")];
       $requisition_info_arr[$value[csf("dtls_id")]]["style"]=$value[csf("style_ref_no")];
    }

 ?>
    <fieldset>
    <legend> Approval Status</legend>
     <div style="width:660px; margin-top:10px">
        <table width="100%">
            <tr style="font-size: 25px;">
                <th colspan="6" align="center">Sample Approval Details</th>

            </tr>
            <tr>
                <td colspan="6" height="15"></td>
            </tr>

            <tr style="font-size: 22px;">
                 <td align="right"><b>Requisition Number</b> </td>
                 <td> &nbsp; : <? echo  $requisition_info_arr[$value[csf("dtls_id")]]["req"];?></td>
                 <td>&nbsp; </td>
                 <td>&nbsp;</td>
                 <td align="right"><b>Buyer Name </b></td>
                 <td align="left">&nbsp; : <? echo  $buyer_arr[$requisition_info_arr[$value[csf("dtls_id")]]["buyer"]];?></td>
            </tr>

            <tr style="font-size: 22px;">
                 <td align="right"><b>Company Name</b> </td>
                 <td> &nbsp; : <? echo  $company_arr[$requisition_info_arr[$value[csf("dtls_id")]]["comp"]];?></td>
                 <td>&nbsp; </td>
                 <td>&nbsp;</td>
                 <td align="right"><b>Style Ref No </b></td>
                 <td align="left">&nbsp; :<? echo  $requisition_info_arr[$value[csf("dtls_id")]]["style"];?></td>
            </tr>
       </table>
       </div>
       <div style="width:660px; margin-top:10px">
        <table cellspacing="0" width="100%" class="rpt_table" cellpadding="0" border="1" rules="all">
            <thead>

                <th width="40" >SL</th>
                <th width="80" >Req. No</th>
                <th width="100" >To Buyer</th>
                <th width="120" >Status</th>
                <th width="120" >Approval Date</th>
                 <th>Comments</th>

            </thead>


        <?
            $sql="select submitted_to_buyer ,approval_status,approval_status_date ,sample_comments  from wo_po_sample_approval_info where entry_form_id=137 and status_active=1 and is_deleted=0 and sample_dtls_id ='$sample_dtls_id' order by id ";
            $sql_sel=sql_select($sql);
            $i=1;
            foreach($sql_sel as $row)
            {
             ?>
             <tr>
                <td width="40" align="center"><? echo $i; ?></td>
                <td width="80" align="center"><? echo $requisition_info_arr[$sample_dtls_id]["req_no"];?></td>
                <td width="100" align="center"><?  echo  change_date_format($row[csf("submitted_to_buyer")]); ?></td>
                <td width="120" align="center"><?  echo  $approval_status[$row[csf("approval_status")]]; ?></td>
                <td width="120" align="center"><? echo  change_date_format($row[csf("approval_status_date")]); ?></td>
                 <td align="center"><?  echo  $row[csf("sample_comments")]; ?></td>
             </tr>

             <?
             $i++;
            }
         ?>
             <tr>
                 <td colspan="2"></td>
                 <td align="center">  <? echo $total; ?></td>

            </tr>

        </table>
     </div>
    </fieldset>

 <?
 exit();
}

if($action=='embellishment_qty_popup')
{
    extract($_REQUEST);
    echo load_html_head_contents("Embellishment Qty", "../../", 1, 1,$unicode,'','');
	if($entry_form==128) $caption_name="Receive Qty"; else if($entry_form==338) $caption_name="Issue Qty";
	?>
	<fieldset>
        <legend> Embellishment Qty.</legend>
        <div style="width:410px; margin-top:10px">
            <table cellspacing="0" width="100%" class="rpt_table" cellpadding="0" border="1" rules="all">
                <thead>
                    <th width="40">SL</th>
                    <th width="40">Embl. Name</th>
                    <th width="110">Size</th>
                    <th width="110"><? echo $caption_name; ?></th>
                </thead>
                <?
                $sql="select a.embel_name, b.size_id, b.size_pass_qty as qty from sample_sewing_output_dtls a, sample_sewing_output_colorsize b where a.id=b.sample_sewing_output_dtls_id and a.entry_form_id='$entry_form' and b.entry_form_id='$entry_form' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.sample_dtls_row_id='$sample_dtls_id' order by a.embel_name";
                $sql_sel=sql_select($sql);
                foreach($sql_sel as $values)
                {
                	$embel_arrs[$values[csf("embel_name")]][$values[csf("size_id")]]['qty']+=$values[csf("qty")];
                }

                $row_span_arr=array();
                foreach($embel_arrs as $emb_name=>$emb_data)
                {
					$row_span=0;
					foreach($emb_data as $size_id=>$row)
					{
						$row_span++;
					}
					$row_span_arr[$emb_name]=$row_span;
                }

                // print_r($row_span_arr);
                $i=1;
                foreach($embel_arrs as $emb_name=>$emb_data)
                {
					$j=0;
					foreach($emb_data as $size_id=>$row)
					{
						?>
						<tr>
							<?
                            if($j==0)
                            {
                                ?>
                                <td width="40" rowspan="<? echo $row_span_arr[$emb_name]?>" align="center"><? echo $i; ?></td>
                                <td width="40" rowspan="<? echo $row_span_arr[$emb_name]?>" align="center"><? echo $emblishment_name_array[$emb_name]; ?></td>
                                <?
                                $i++;
                            }
                            ?>
                            <td width="110" align="center"><? echo $size_arr[$size_id]; ?></td>
                            <td width="110" align="center"><? echo $row[("qty")]; ?></td>
						</tr>
						<?
						$j++;
						$total+=$row[("qty")];
						$gr_total+=$row[("qty")];
					}
					?>
					<tr>
                        <td colspan="3" align="right"><b>Total</b></td>
                        <td align="center"><b><? echo $total; $total=0; ?></b></td>
					</tr>
					<?
                }
                ?>
                <tr>
                    <td colspan="3" align="right"><b> Grand Total</b></td>
                    <td align="center"><b><? echo $gr_total; ?></b></td>
                </tr>
            </table>
        </div>
	</fieldset>
	<?
	exit();
}

if($action=='comments_view')
{
    extract($_REQUEST);
    echo load_html_head_contents("Remarks", "../../", 1, 1,$unicode,'','');
 ?>
    <fieldset>
    <legend>Cutting</legend>
        <?
             $sql= "select id, sample_sewing_output_mst_id, sample_name,sample_dtls_row_id,item_number_id, sewing_date, qc_pass_qty,reject_qty, remarks from sample_sewing_output_dtls where sample_dtls_row_id='$sample_dtls_id' and entry_form_id=127 and is_deleted=0 and status_active=1";
              // echo $sql;die;
             echo  create_list_view ( "list_view_1", "Date,Cutting Qty,Remarks", "80,100,250","500","220",1, $sql, "", "","", 1, '0,0,0', $arr, "sewing_date,qc_pass_qty,remarks", "../requires/sample_progress_report_controller", '','3,1,0');

        ?>
    </fieldset>

    <fieldset>
    <legend>Sewing Output</legend>
        <?
             $sql= "select id, sample_sewing_output_mst_id, sample_name,sample_dtls_row_id,item_number_id, sewing_date, qc_pass_qty,reject_qty, remarks from sample_sewing_output_dtls where sample_dtls_row_id='$sample_dtls_id' and entry_form_id=130 and is_deleted=0 and status_active=1";
              // echo $sql;die;
             echo  create_list_view ( "list_view_1", "Date,Sewing Qty,Remarks", "80,100,250","500","220",1, $sql, "", "","", 1, '0,0,0', $arr, "sewing_date,qc_pass_qty,remarks", "../requires/sample_progress_report_controller", '','3,1,0');

        ?>
    </fieldset>


        <?
             $sql= "select  b.delivery_date,b.sample_ex_factory_mst_id,b. sample_name,b.sample_dtls_part_tbl_id,b.gmts_item_id, b.ex_factory_qty , b.remarks from sample_ex_factory_mst a ,sample_ex_factory_dtls b where a.id=b.sample_ex_factory_mst_id and  b.sample_dtls_part_tbl_id='$sample_dtls_id' and a.entry_form_id=132 and a.is_deleted=0 and a.status_active=1 and b.entry_form_id=132 and b.is_deleted=0 and b.status_active=1";
             // echo $sql;die;
             if(count(sql_select($sql))>0)
             {
                ?>
             <fieldset>
             <legend>Delivery</legend>
                <?
                echo  create_list_view ( "list_view_1", "Date,Delivery Qty,Remarks", "80,100,250","500","220",1, $sql, "", "","", 1, '0,0,0', $arr, "delivery_date,ex_factory_qty,remarks", "../requires/sample_progress_report_controller", '','3,1,0');
             }


        ?>
             </fieldset>



        <?
             $sql= "select id, sample_sewing_output_mst_id, sample_name,sample_dtls_row_id,item_number_id, sewing_date, qc_pass_qty,reject_qty, remarks from sample_sewing_output_dtls where sample_dtls_row_id='$sample_dtls_id' and entry_form_id=128 and is_deleted=0 and status_active=1 and embel_name=1";
            if(count(sql_select($sql))>0)
            {
                ?>
                <fieldset>
                <legend>Print</legend>

                <?
             echo  create_list_view ( "list_view_1", "Date,Print Qty,Remarks", "80,100,250","500","220",1, $sql, "", "","", 1, '0,0,0', $arr, "sewing_date,qc_pass_qty,remarks", "../requires/sample_progress_report_controller", '','3,1,0');
            }

        ?>
                </fieldset>


                   <?
             $sql= "select id, sample_sewing_output_mst_id, sample_name,sample_dtls_row_id,item_number_id, sewing_date, qc_pass_qty,reject_qty, remarks from sample_sewing_output_dtls where sample_dtls_row_id='$sample_dtls_id' and entry_form_id=128 and is_deleted=0 and status_active=1 and embel_name=2";
            if(count(sql_select($sql))>0)
            {
                ?>
                <fieldset>
                <legend>Embroidery</legend>

                <?
             echo  create_list_view ( "list_view_1", "Date,Embroidery Qty,Remarks", "80,100,250","500","220",1, $sql, "", "","", 1, '0,0,0', $arr, "sewing_date,qc_pass_qty,remarks", "../requires/sample_progress_report_controller", '','3,1,0');
            }

        ?>
                </fieldset>


                 <?
             $sql= "select id, sample_sewing_output_mst_id, sample_name,sample_dtls_row_id,item_number_id, sewing_date, qc_pass_qty,reject_qty, remarks from sample_sewing_output_dtls where sample_dtls_row_id='$sample_dtls_id' and entry_form_id=131 and is_deleted=0 and status_active=1 and embel_name=3";
            if(count(sql_select($sql))>0)
            {
                ?>
                <fieldset>
                <legend>Wash</legend>

                <?
             echo  create_list_view ( "list_view_1", "Date,Wash Qty,Remarks", "80,100,250","500","220",1, $sql, "", "","", 1, '0,0,0', $arr, "sewing_date,qc_pass_qty,remarks", "../requires/sample_progress_report_controller", '','3,1,0');
            }

        ?>
                </fieldset>



                 <?
             $sql= "select id, sample_sewing_output_mst_id, sample_name,sample_dtls_row_id,item_number_id, sewing_date, qc_pass_qty,reject_qty, remarks from sample_sewing_output_dtls where sample_dtls_row_id='$sample_dtls_id' and entry_form_id=128 and is_deleted=0 and status_active=1 and embel_name=4";
            if(count(sql_select($sql))>0)
            {
                ?>
                <fieldset>
                <legend>Special Works</legend>

                <?
             echo  create_list_view ( "list_view_1", "Date,Special Works Qty,Remarks", "80,100,250","500","220",1, $sql, "", "","", 1, '0,0,0', $arr, "sewing_date,qc_pass_qty,remarks", "../requires/sample_progress_report_controller", '','3,1,0');
            }

        ?>
                </fieldset>



                 <?
             $sql= "select id, sample_sewing_output_mst_id, sample_name,sample_dtls_row_id,item_number_id, sewing_date, qc_pass_qty,reject_qty, remarks from sample_sewing_output_dtls where sample_dtls_row_id='$sample_dtls_id' and entry_form_id=131 and is_deleted=0 and status_active=1 and embel_name=5";
            if(count(sql_select($sql))>0)
            {
                ?>
                <fieldset>
                <legend>Gmts Dyeing</legend>

                <?
             echo  create_list_view ( "list_view_1", "Date,Dyeing Qty,Remarks", "80,100,250","500","220",1, $sql, "", "","", 1, '0,0,0', $arr, "sewing_date,qc_pass_qty,remarks", "../requires/sample_progress_report_controller", '','3,1,0');
            }

        ?>
                </fieldset>




<?
}

if($action=='reject_qty_view')
{
    extract($_REQUEST);
    echo load_html_head_contents("Remarks", "../../", 1, 1,$unicode,'','');
 ?>
    <fieldset>
    <legend>Cuttings Reject</legend>
        <?
             $sql= "select sample_sewing_output_mst_id, sample_name,sample_dtls_row_id,item_number_id, sum(qc_pass_qty) as qc,sum(reject_qty) as reject from sample_sewing_output_dtls where sample_dtls_row_id='$sample_dtls_id' and entry_form_id=127 and is_deleted=0 and status_active=1 group by   sample_sewing_output_mst_id, sample_name,sample_dtls_row_id,item_number_id";
             //echo $sql;die;
             $arr =array(0=>$sample_name_arr,1=>$garments_item);
              echo  create_list_view ( "list_view_1", "Sample Name,Item,Qc Qty,Reject Qty", "80,100,100,150","500","220",1, $sql, "", "","", 1, 'sample_name,item_number_id,0,0', $arr, "sample_name,item_number_id,qc,reject", "../requires/sample_progress_report_controller", '','0,0,0');

        ?>
    </fieldset>

     <fieldset>
    <legend>Embellishment Reject</legend>
        <?
             $sql= "select sample_sewing_output_mst_id, sample_name,sample_dtls_row_id,item_number_id,   sum(qc_pass_qty) as qc,sum(reject_qty) as reject  from sample_sewing_output_dtls where sample_dtls_row_id='$sample_dtls_id' and entry_form_id=128 and is_deleted=0 and status_active=1 group by  sample_sewing_output_mst_id, sample_name,sample_dtls_row_id,item_number_id  ";
              // echo $sql;die;
              $arr =array(0=>$sample_name_arr,1=>$garments_item);
             echo  create_list_view ( "list_view_1", "Sample Name,Item,Qc Qty,Reject Qty", "80,100,100,150","500","220",1, $sql, "", "","", 1, 'sample_name,item_number_id,0,0', $arr, "sample_name,item_number_id,qc,reject", "../requires/sample_progress_report_controller", '','0,0,0');

        ?>
    </fieldset>


    <fieldset>
    <legend>Sewing Reject</legend>
        <?
             $sql= "select sample_sewing_output_mst_id, sample_name,sample_dtls_row_id,item_number_id,   sum(qc_pass_qty) as qc,sum(reject_qty) as reject  from sample_sewing_output_dtls where sample_dtls_row_id='$sample_dtls_id' and entry_form_id=130 and is_deleted=0 and status_active=1 group by  sample_sewing_output_mst_id, sample_name,sample_dtls_row_id,item_number_id  ";
              // echo $sql;die;
              $arr =array(0=>$sample_name_arr,1=>$garments_item);
             echo  create_list_view ( "list_view_1", "Sample Name,Item,Qc Qty,Reject Qty", "80,100,100,150","500","220",1, $sql, "", "","", 1, 'sample_name,item_number_id,0,0', $arr, "sample_name,item_number_id,qc,reject", "../requires/sample_progress_report_controller", '','0,0,0');

        ?>
    </fieldset>

    <fieldset>
    <legend>Dyeing and Wash Reject</legend>
        <?
             $sql= "select  sample_sewing_output_mst_id, sample_name,sample_dtls_row_id,item_number_id,   sum(qc_pass_qty) as qc,sum(reject_qty) as reject  from sample_sewing_output_dtls where sample_dtls_row_id='$sample_dtls_id' and entry_form_id=131 and is_deleted=0 and status_active=1 group by  sample_sewing_output_mst_id, sample_name,sample_dtls_row_id,item_number_id  ";
             $arr =array(0=>$sample_name_arr,1=>$garments_item);
           //  echo $sql;die;
             echo  create_list_view ( "list_view_1", "Sample Name,Item,Qc Qty,Reject Qty", "80,100,100,150","500","220",1, $sql, "", "","", 1, 'sample_name,item_number_id,0,0', $arr, "sample_name,item_number_id,qc,reject", "../requires/sample_progress_report_controller", '','0,0,0,0');

        ?>
    </fieldset>


<?
}

//cutting_popup
if($action=='cutting_popup')
{
	echo load_html_head_contents("Date Wise Production Report", "../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_break_down_id;die;
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  );
	$order_library=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number"  );


	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_break_down_id","size_number_id","size_number_id");

	$sql_color_size= sql_select("SELECT a.country_id, sum(b.production_qnty) as production_qnty, c.color_number_id, c.size_number_id
	from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c
	where a.production_type=1 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
	group by   c.color_number_id,a.country_id,c.size_number_id
	order by c.color_number_id,a.country_id");

	foreach($sql_color_size as $row)
	{
		$details_data[$row[csf('color_number_id')]][$row[csf('country_id')]]=$row[csf('country_id')];
		$color_size_data[$row[csf('color_number_id')]][$row[csf('country_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
	}

	$col_width=60*count($sizearr_order);
	$table_width=330+$col_width;


?>
<script>

	function window_close()
	{
		parent.emailwindow.hide();
	}

</script>
<fieldset style="width:<? echo $table_width; ?>  margin-left:10px" >
    <div style="100%" id="report_container">

        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px; margin-top:10px;">
        	<tr>
            	<td align="center" style="text-decoration:underline; font-size:20px; font-weight:bold">Date wise cutting production</td>
            </tr>
        	<tr>
            	<td style="font-size:16px; font-weight:bold;">
                Date : <? echo change_date_format($production_date); ?>
                <br />
                Order No : <? echo $order_library[$po_break_down_id]; ?>&nbsp;&nbsp;&nbsp;&nbsp;
                </td>
            </tr>
        </table>

        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                	<th width="40" rowspan="2">SI</th>
                    <th width="100" rowspan="2">Color</th>
                    <th width="100" rowspan="2">Country Name</th>
                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
                    <th width="80" rowspan="2" >Total</th>
                </tr>
                <tr>
                <?
				$grand_tot_in=0;
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
                    <?
				}
				?>
                </tr>

            </thead>
            <tbody>
            <?
			$i=1;
			$k=1;
			//var_dump($result);die;
			foreach($details_data as $color_id=>$value)
			{
				foreach($value as $country_id=>$row)
				{
					if(!in_array($color_id,$temp_arr))
					{
						$temp_arr[]=$color_id;
						if($k!=1)
						{
							?>
							<tr bgcolor="#CCCCCC">
								<td >&nbsp;</td>
								<td>&nbsp;</td>
								<td align="right" style="font-weight:bold">Color Total:</td>
								<?
								foreach($sizearr_order as $size_id)
								{
									?>
									<td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
									<?
								}
								?>
								<td align="right"><? echo number_format($line_color_total_in,0); ?></td>
							</tr>
							<?
							$line_color_size_in = $line_color_total_in ="";
						}
						$k++;
					}

					?>
					<tr>
						<td align="center"><? echo $i;  ?></td>
                        <?
						if(!in_array($color_id,$temp_arr_color))
						{
							$temp_arr_color[]=$color_id;
							?>
							<td rowspan="<? echo count($color_size_data[$color_id]); ?>" align="center" valign="middle"><p><? echo $colorarr[$color_id];  ?></p></td>
							<?
						}
						?>
						<td ><p><? echo $country_library[$country_id];  ?></p></td>
						<?
						$color_total_in=0;
						foreach($sizearr_order as $size_id)
						{
							?>
							<td align="right"><p>
							<?
								echo number_format($color_size_data[$color_id][$country_id][$size_id],0) ;
								 $color_total_in+= $color_size_data[$color_id][$country_id][$size_id];
								 $color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
								 $line_color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
							 ?>
							</p></td>
							<?
						}
						$line_color_total_in+=$color_total_in;
						?>
						<td align="right"><p><? echo  number_format( $color_total_in,0); $grand_tot_in+=$color_total_in; ?></p></td>
					</tr>
					<?
					$i++;
				}

			}
			?>
            <tr bgcolor="#CCCCCC">
                <td >&nbsp;</td>
                <td>&nbsp;</td>
                <td align="right" style="font-weight:bold">Color Total:</td>
                <?
                foreach($sizearr_order as $size_id)
                {
                    ?>
                    <td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
                    <?
                }
                ?>
                <td align="right"><? echo number_format($line_color_total_in,0); ?></td>
            </tr>
            </tbody>
            <tfoot>
                <th >&nbsp;</th>
                <th>&nbsp;</th>
                <th >Day Total:</th>
                <?
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th ><? echo $color_size_in[$size_id]; ?></th>
                    <?
				}
				?>
                <th ><? echo $grand_tot_in; ?></th>

            </tfoot>
        </table>
        </div>
    </fieldset>
<?


}

if($action=='printing_issue_popup')
{
	echo load_html_head_contents("Date Wise Production Report", "../../", 1, 1,$unicode,'','');
 	extract($_REQUEST);
	//echo $po_break_down_id;die;
	$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
	$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
	$country_library=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  );
	$order_library=return_library_array( "select id,po_number from  wo_po_break_down", "id", "po_number"  );


	$sizearr_order=return_library_array("select size_number_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=$po_break_down_id","size_number_id","size_number_id");

	$location=str_replace("'","",$location);
	$floor_id=str_replace("'","",$floor_id);
	$sewing_line=str_replace("'","",$sewing_line);
	$location_cond=$floor_cond=$sewing_cond="";
	if($location!="") $location_cond="and a.location=$location";
	if($floor_id!="") $floor_cond=" and a.floor_id=$floor_id";
	if($sewing_line!="") $sewing_cond=" and a.sewing_line=$sewing_line";

	$sql_color_size= sql_select("SELECT a.country_id, sum(b.production_qnty) as production_qnty, c.color_number_id, c.size_number_id
	from pro_garments_production_mst a,  pro_garments_production_dtls b,  wo_po_color_size_breakdown c
	where a.production_type=2 and a.embel_name=1 and a.item_number_id=$gmts_item_id and a.production_date='$production_date' $location_cond $floor_cond $sewing_cond  and a.id=b.mst_id and b.color_size_break_down_id=c.id  and a.po_break_down_id='$po_break_down_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
	group by   c.color_number_id,a.country_id,c.size_number_id
	order by c.color_number_id,a.country_id");
	foreach($sql_color_size as $row)
	{
		$details_data[$row[csf('color_number_id')]][$row[csf('country_id')]]=$row[csf('country_id')];
		$color_size_data[$row[csf('color_number_id')]][$row[csf('country_id')]][$row[csf('size_number_id')]] +=$row[csf('production_qnty')];
	}

	$col_width=60*count($sizearr_order);
	$table_width=330+$col_width;


?>
<script>

	function window_close()
	{
		parent.emailwindow.hide();
	}

</script>
<fieldset style="width:<? echo $table_width; ?>  margin-left:10px" >
    <div style="100%" id="report_container">

        <table cellpadding="0" cellspacing="0" border="0" rules="all" width="<? echo $table_width; ?>" style="margin-bottom:10px; margin-top:10px;">
        	<tr>
            	<td align="center" style="text-decoration:underline; font-size:20px; font-weight:bold">Date wise printing production</td>
            </tr>
        	<tr>
            	<td style="font-size:16px; font-weight:bold;">
                Date : <? echo change_date_format($production_date); ?>
                <br />
                Order No : <? echo $order_library[$po_break_down_id]; ?>&nbsp;&nbsp;&nbsp;&nbsp;
                </td>
            </tr>
        </table>

        <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
            <thead>
                <tr>
                	<th width="40" rowspan="2">SI</th>
                    <th width="100" rowspan="2">Color</th>
                    <th width="100" rowspan="2">Country Name</th>
                	<th width="<? echo $col_width; ?>" colspan="<? echo count($sizearr_order); ?>">Size</th>
                    <th width="80" rowspan="2" >Total</th>
                </tr>
                <tr>
                <?
				$grand_tot_in=0;
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th width="60"><? echo $sizearr[$size_id]; ?></th>
                    <?
				}
				?>
                </tr>

            </thead>
            <tbody>
            <?
			$i=1;
			$k=1;
			//var_dump($result);die;
			foreach($details_data as $color_id=>$value)
			{
				foreach($value as $country_id=>$row)
				{
					if(!in_array($color_id,$temp_arr))
					{
						$temp_arr[]=$color_id;
						if($k!=1)
						{
							?>
							<tr bgcolor="#CCCCCC">
								<td >&nbsp;</td>
								<td>&nbsp;</td>
								<td align="right" style="font-weight:bold">Color Total:</td>
								<?
								foreach($sizearr_order as $size_id)
								{
									?>
									<td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
									<?
								}
								?>
								<td align="right"><? echo number_format($line_color_total_in,0); ?></td>
							</tr>
							<?
							$line_color_size_in = $line_color_total_in ="";
						}
						$k++;
					}

					?>
					<tr>
						<td align="center"><? echo $i;  ?></td>
                        <?
						if(!in_array($color_id,$temp_arr_color))
						{
							$temp_arr_color[]=$color_id;
							?>
							<td rowspan="<? echo count($color_size_data[$color_id]); ?>" align="center" valign="middle"><p><? echo $colorarr[$color_id];  ?></p></td>
							<?
						}
						?>
						<td ><p><? echo $country_library[$country_id];  ?></p></td>
						<?
						$color_total_in=0;
						foreach($sizearr_order as $size_id)
						{
							?>
							<td align="right"><p>
							<?
								echo number_format($color_size_data[$color_id][$country_id][$size_id],0) ;
								 $color_total_in+= $color_size_data[$color_id][$country_id][$size_id];
								 $color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
								 $line_color_size_in [$size_id]+=$color_size_data[$color_id][$country_id][$size_id];
							 ?>
							</p></td>
							<?
						}
						$line_color_total_in+=$color_total_in;
						?>
						<td align="right"><p><? echo  number_format( $color_total_in,0); $grand_tot_in+=$color_total_in; ?></p></td>
					</tr>
					<?
					$i++;
				}

			}
			?>
            <tr bgcolor="#CCCCCC">
                <td >&nbsp;</td>
                <td>&nbsp;</td>
                <td align="right" style="font-weight:bold">Color Total:</td>
                <?
                foreach($sizearr_order as $size_id)
                {
                    ?>
                    <td align="right"><? echo number_format($line_color_size_in [$size_id],0); ?></td>
                    <?
                }
                ?>
                <td align="right"><? echo number_format($line_color_total_in,0); ?></td>
            </tr>
            </tbody>
            <tfoot>
                <th >&nbsp;</th>
                <th>&nbsp;</th>
                <th >Day Total:</th>
                <?
				foreach($sizearr_order as $size_id)
				{
					?>
                	<th ><? echo $color_size_in[$size_id]; ?></th>
                    <?
				}
				?>
                <th ><? echo $grand_tot_in; ?></th>

            </tfoot>
        </table>
        </div>
    </fieldset>
<?


}

if($action=='checklist_popup')
{
    extract($_REQUEST);
    echo load_html_head_contents("Not Checklist", "../../", 1, 1,$unicode,'','');
 ?>
    <fieldset>
    <legend> <? if($type=="NO"){echo "Not";} ?> Checklist Info</legend>
     <div style="width:370px; margin-top:10px">
        <table cellspacing="0" width="100%" class="rpt_table" cellpadding="0" border="1" rules="all">
            <thead>
                <th width="50" >SL</th>
                <th width="" >Name</th>
             </thead>

        <?
             $checklist_arr=$sample_checklist_set;
             $sql= sql_select("select checklist_id,requisition_id from sample_checklist_dtls where status_active=1 and is_deleted=0 and requisition_id='$req_id'");
             if($type=="NO")
             {
                 foreach($sql as $val)
                 {
                    unset($checklist_arr[$val[csf("checklist_id")]]);
                 }
             }

             if($type=="YES")
             {
                foreach($sql as $val)
                 {
                   $checklist_arrs[$val[csf("checklist_id")]]= $checklist_arr[$val[csf("checklist_id")]];
                 }
             }

              $i=1;
              if($type=="YES"){$checklist_arr=$checklist_arrs;}
             foreach($checklist_arr as $name)
             {

                ?>
                <tr>
                    <td width="50" align="center"><? echo $i; ?></td>
                    <td width="" align="center"><strong><? echo  $name; ?> </strong></td>

                </tr>
                 <?
                 $i++;
             }
         ?>
        </table>
     </div>
    </fieldset>
	<?
    exit();
}

if($action=='refusing_popup')
{
    extract($_REQUEST);
    echo load_html_head_contents("Not Checklist", "../../", 1, 1,$unicode,'','');
 ?>
    <fieldset>
    <legend>  Refusing Cause Info</legend>
     <div style="width:370px; margin-top:10px">
        <table cellspacing="0" width="100%" class="rpt_table" cellpadding="0" border="1" rules="all">
        <?
              $sql= "select id,refusing_cause from sample_development_mst where status_active=1 and is_deleted=0 and id='$req_id' and entry_form_id in(117,203)";
              $sql_sel=sql_select($sql);
              $i=1;

                 foreach($sql_sel as $val)
                  {
                   if($val[csf("refusing_cause")]!="")
                   {
                    ?>
                    <thead>
                <th width="50" >SL</th>
                <th width="" >Cause</th>
             </thead>
                <tr>
                    <td width="50" align="center"><? echo $i; ?></td>
                    <td width="" align="center"><strong style="font-size: 16px;"><? echo  $val[csf("refusing_cause")]; ?> </strong></td>

                </tr>
                 <?
                 $i++;
                   }
                 }
         ?>
        </table>
     </div>
    </fieldset>

 <?
 exit();
}


?>