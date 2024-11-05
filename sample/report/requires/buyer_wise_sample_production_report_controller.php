<? 
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Buyer wise Sample production Report.
Created by		:	Zakaria joy
Creation date 	: 	21-09-2023
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		: Gbl temp id: 134
*/
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
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
    echo create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- Select --", $selected, "" );        
    exit();
}
if($action=="sample_item_popup")
{
	echo load_html_head_contents("Remarks", "../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	?>	
    <script>	 
	function toggle( x, origColor ) {
		var newColor = 'yellow';
		if ( x.style ) {
			x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
		}
	}	
	function js_set_value( strcon )
	{
		$('#txt_sample_item').val( strcon );
		parent.emailwindow.hide();
	}
		  
	</script>
     <input type="hidden" id="txt_sample_item" />
    <?
    if($data==1){
        $arr=array(1=>$sample_type);
        echo  create_list_view ("list_view", "Sample Name,Sample Type", "200,150","420","350",0, "select  sample_name, sample_type, id, 1 as type from lib_sample where is_deleted=0 and status_active=1", "js_set_value", "sample_name,id,type", "", 1, "0,sample_type", $arr , "sample_name,sample_type", "", 'setFilterGrid("list_view",-1);','0,0','',"");
        exit();
    }
    else{
        $arr=array();
        echo  create_list_view ("list_view", "Item Name", "420","420","350",0, "select id,item_name, 2 as type from lib_garment_item where is_deleted=0 and status_active=1", "js_set_value", "item_name,id,type", "", 1, "0", $arr , "item_name", "", 'setFilterGrid("list_view",-1);','0,0','',"");
        exit();
    }
	
}
if($action=="report_generate")
{ 
    $process = array( &$_POST );
    extract(check_magic_quote_gpc( $process )); 
    $company_name       = str_replace("'", "", $cbo_company_name);
    $buyer_name         = str_replace("'", "", $cbo_buyer_name);
    $sample_year        = str_replace("'", "", $cbo_year);     
    $sample_id          = str_replace("'", "", $txt_sample_id);
    $item_id            = str_replace("'", "", $txt_item_id);
    $cbo_basis            = str_replace("'", "", $cbo_basis);
    $date_from          = str_replace("'", "", $txt_date_from);
    $date_to            = str_replace("'", "", $txt_date_to);

    $buyer_arr=return_library_array( "select id, buyer_name from lib_buyer where status_active=1 and is_deleted=0",'id','buyer_name');
    $sample_arr=return_library_array( "select id, sample_name from lib_sample where status_active=1 and is_deleted=0",'id','sample_name');
    $item_arr=return_library_array( "select id, item_name from lib_garment_item where status_active=1 and is_deleted=0",'id','item_name');
    $company_arr=return_library_array( "select id, company_name from lib_company where status_active=1 and is_deleted=0",'id','company_name');

    $year_cond="";
    if($db_type==2)
    {
        $year_cond=($sample_year)? " and  to_char(a.insert_date,'YYYY')=$sample_year" : " ";
    }
    else
    {
        $year_cond=($sample_year)? " and year(a.insert_date)=$sample_year" : " ";
    }

    if(str_replace("'","",$cbo_company_name)==0) $company_name=""; else $company_name=" and a.company_id=$cbo_company_name";
    if(str_replace("'","",$cbo_buyer_name)==0) $buyer_name="";else $buyer_name=" and a.buyer_name=$cbo_buyer_name";
    if(str_replace("'","",$sample_id)=='') $sample_name="";else $sample_name=" and b.sample_name=$sample_id";
    if(str_replace("'","",$item_id)=='') $item_name="";else $item_name=" and b.gmts_item_id=$item_id";
    
    
    if($db_type==0) $yearCond="YEAR(a.insert_date)"; else if($db_type==2) $yearCond="to_char(a.insert_date,'YYYY')";
    if($cbo_basis==2){
        if(str_replace("'","",trim($txt_date_from))=="" || str_replace("'","",trim($txt_date_to))=="") $txt_date="";
        else $txt_date=" and d.sewing_date between $txt_date_from and $txt_date_to";     
        $sql="SELECT a.id,a.quotation_id,a.dealing_marchant,a.buyer_ref, a.requisition_number_prefix_num, $yearCond as year, a.bh_merchant, a.buyer_name, a.sample_stage_id, a.style_ref_no, a.season, b.sample_name, b.gmts_item_id, sum(b.sample_prod_qty) as req_qty, min(a.requisition_date) as min_date , max(a.requisition_date) as max_date from sample_development_mst a,sample_development_dtls b, sample_sewing_output_mst c , sample_sewing_output_dtls d  where a.id=b.sample_mst_id and c.id=d.sample_sewing_output_mst_id and d.entry_form_id in (127,128,130,131,337,338) and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.sample_development_id=a.id and a.entry_form_id in(117,203,449) and b.entry_form_id in(117,203,449) and a.status_active=1 and b.is_deleted=0 and b.status_active=1  $company_name $buyer_name $year_cond $txt_date $sample_name $item_name group by a.id,a.quotation_id,a.dealing_marchant, a.buyer_ref,a.requisition_number_prefix_num, a.insert_date, a.bh_merchant, a.buyer_name, a.sample_stage_id, a.style_ref_no, a.season, b.sample_name, b.gmts_item_id order by a.requisition_number_prefix_num DESC";
    }
    elseif($cbo_basis==3){
        if(str_replace("'","",trim($txt_date_from))=="" || str_replace("'","",trim($txt_date_to))=="") $txt_date="";
        else $txt_date=" and d.delivery_date between $txt_date_from and $txt_date_to";
        $sql="SELECT a.id,a.quotation_id,a.dealing_marchant,a.buyer_ref, a.requisition_number_prefix_num, $yearCond as year, a.bh_merchant, a.buyer_name, a.sample_stage_id, a.style_ref_no, a.season, b.sample_name, b.gmts_item_id, sum(b.sample_prod_qty) as req_qty from sample_development_mst a,sample_development_dtls b, sample_ex_factory_mst c,sample_ex_factory_dtls d  where a.id=b.sample_mst_id and c.id=d.sample_ex_factory_mst_id and c.entry_form_id in(132) and d.entry_form_id in(132) and c.status_active=1 and d.status_active=1 and a.id=d.sample_development_id and a.entry_form_id in(117,203,449) and b.entry_form_id in(117,203,449) and a.status_active=1 and b.is_deleted=0 and b.status_active=1  $company_name $buyer_name $year_cond $txt_date $sample_name $item_name group by a.id,a.quotation_id,a.dealing_marchant, a.buyer_ref,a.requisition_number_prefix_num, a.insert_date, a.bh_merchant, a.buyer_name, a.sample_stage_id, a.style_ref_no, a.season, b.sample_name, b.gmts_item_id order by a.requisition_number_prefix_num DESC";
    }
    else{
        if(str_replace("'","",trim($txt_date_from))=="" || str_replace("'","",trim($txt_date_to))=="") $txt_date="";
        else $txt_date=" and a.requisition_date between $txt_date_from and $txt_date_to";
        $sql="SELECT a.id,a.quotation_id,a.dealing_marchant,a.buyer_ref, a.requisition_number_prefix_num, $yearCond as year, a.bh_merchant, a.buyer_name, a.sample_stage_id, a.style_ref_no, a.season, b.sample_name, b.gmts_item_id, sum(b.sample_prod_qty) as req_qty from sample_development_mst a,sample_development_dtls b where a.id=b.sample_mst_id and a.entry_form_id in(117,203,449) and b.entry_form_id in(117,203,449) and a.status_active=1 and b.is_deleted=0 and b.status_active=1  $company_name $buyer_name $year_cond $txt_date $sample_name $item_name group by a.id,a.quotation_id,a.dealing_marchant, a.buyer_ref,a.requisition_number_prefix_num, a.insert_date, a.bh_merchant, a.buyer_name, a.sample_stage_id, a.style_ref_no, a.season, b.sample_name, b.gmts_item_id order by a.requisition_number_prefix_num DESC";
    }
    //echo $sql; die;
    
    $fromDate   = change_date_format( str_replace("'","",trim($txt_date_from)));
    $toDate     = change_date_format( str_replace("'","",trim($txt_date_to)));
                
    

    
    
    // =============================================== main qery ================================================         
    $sql_res=sql_select($sql);
    if(count($sql_res)==0)
    {
        ?>
        <div style="font-weight: bold;color: red;text-align: center;font-size: 20px;">Data Not Found! Please Try Again.</div>
        <?
        die();
    }
    $sample_mst_id_arr = array();
    $main_data_arr=array();
    foreach ($sql_res as $val) 
    {
        $sample_mst_id_arr[$val[csf('id')]] = $val[csf('id')];
        $main_data_arr[$val[csf('buyer_name')]][$val[csf('sample_name')]][$val[csf('gmts_item_id')]]['req_qty']+=$val[csf('req_qty')];
        $buyer_wise_qty[$val[csf('buyer_name')]]['req_qty']+=$val[csf('req_qty')];
        $buyer_sample_wise_qty[$val[csf('buyer_name')]][$val[csf('sample_name')]]['req_qty']+=$val[csf('req_qty')];
    }
    $con = connect();
	execute_query("DELETE from gbl_temp_engine where user_id = ".$user_name." and ref_from in (1) and entry_form=134");

    fnc_tempengine("gbl_temp_engine", $user_name, 134, 1, $sample_mst_id_arr, $empty_arr);

    // ======================================= production qnty =======================================
    $prod_date_cond = str_replace("a.requisition_date", "b.sewing_date", $txt_date);
    $prod_sql="SELECT a.sample_development_id as id, b.sample_name, b.item_number_id as item_id, b.entry_form_id, sum(b.qc_pass_qty) as qc_pass_qty, sum(b.reject_qty) as reject, d.buyer_name from sample_sewing_output_mst a, sample_sewing_output_dtls b, gbl_temp_engine c, sample_development_mst d where a.id=b.sample_sewing_output_mst_id and b.entry_form_id in (127,128,130,131,337,338) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.ref_val=a.sample_development_id and c.ref_from=1 and c.entry_form=134 and c.user_id=$user_name and a.sample_development_id=d.id group by  a.sample_development_id, b.sample_name, b.item_number_id, b.entry_form_id, d.buyer_name"; 
    //echo $prod_sql;die();
    $prod_sql_res = sql_select($prod_sql);
    $production_data = array();
    foreach($prod_sql_res as $val)
    {
        $production_data[$val[csf("buyer_name")]][$val[csf("sample_name")]][$val[csf("item_id")]][$val[csf("entry_form_id")]]['good_qty'] += $val[csf("qc_pass_qty")];
        $production_data[$val[csf("buyer_name")]][$val[csf("sample_name")]][$val[csf("item_id")]][$val[csf("entry_form_id")]]['rej_qty'] += $val[csf("reject")];
        $buyer_wise_production[$val[csf("buyer_name")]][$val[csf("entry_form_id")]]['good_qty']+=$val[csf("qc_pass_qty")];
        $buyer_wise_production[$val[csf("buyer_name")]][$val[csf("entry_form_id")]]['rej_qty']+=$val[csf("reject")];
        $buyer_sample_wise_production[$val[csf("buyer_name")]][$val[csf("sample_name")]][$val[csf("entry_form_id")]]['good_qty']+=$val[csf("qc_pass_qty")];
        $buyer_sample_wise_production[$val[csf("buyer_name")]][$val[csf("sample_name")]][$val[csf("entry_form_id")]]['rej_qty']+=$val[csf("reject")];
    }
    
    // ================================== delivery =====================================
    $delv_qty_arr=array(); 
    $delv_qty_mkt_arr=array(); 
    
    $delv_qty_sql=sql_select("SELECT b.sample_development_id, b.sample_name, b.gmts_item_id, sum(b.ex_factory_qty) as qc_pass_qty, c.buyer_name from sample_ex_factory_mst a, sample_ex_factory_dtls b, sample_development_mst c, gbl_temp_engine d where a.id=b.sample_ex_factory_mst_id and  a.entry_form_id in(132) and b.entry_form_id in(132) and a.status_active=1 and b.status_active=1 and c.id=b.sample_development_id and b.sample_development_id=d.ref_val and d.ref_from=1 and d.entry_form=134 and d.user_id=$user_name group by  b.sample_development_id, b.sample_name, b.gmts_item_id,c.buyer_name ");

    foreach ($delv_qty_sql as  $result)
    {
        $delv_qty_arr[$result[csf('buyer_name')]][$result[csf('sample_name')]][$result[csf('gmts_item_id')]]+=$result[csf('qc_pass_qty')];
        $buyer_wise_delv[$result[csf("buyer_name")]]['delv_qty']+=$result[csf("qc_pass_qty")];
        $buyer_sample_wise_delv[$result[csf("buyer_name")]][$result[csf('sample_name')]]['delv_qty']+=$result[csf("qc_pass_qty")];
    }

    $delv_qty_sql=sql_select("SELECT b.sample_development_id, b.sample_name, b.gmts_item_id, sum(b.ex_factory_qty) as qc_pass_qty, c.buyer_name from sample_ex_factory_mst a, sample_ex_factory_dtls b, sample_development_mst c, gbl_temp_engine d where a.id=b.sample_ex_factory_mst_id and  a.entry_form_id in(396) and b.entry_form_id in(396) and a.status_active=1 and b.status_active=1 and c.id=b.sample_development_id and b.sample_development_id=d.ref_val and d.ref_from=1 and d.entry_form=134 and d.user_id=$user_name group by  b.sample_development_id, b.sample_name, b.gmts_item_id, c.buyer_name ");    

    foreach ($delv_qty_sql as  $result)
    {
        $delv_qty_mkt_arr[$result[csf('buyer_name')]][$result[csf('sample_name')]][$result[csf('gmts_item_id')]]+=$result[csf('qc_pass_qty')];
        $buyer_wise_delv[$result[csf("buyer_name")]]['delv_mkt_qty']+=$result[csf("qc_pass_qty")];
        $buyer_sample_wise_delv[$result[csf("buyer_name")]][$result[csf('sample_name')]]['delv_mkt_qty']+=$result[csf("qc_pass_qty")];
    }
    
    ob_start();
    ?>
    <div style="padding: 10px; width: 810px;">
        <table cellspacing="0" cellpadding="0" border="1" class="rpt_table" width="780" rules="all" id="table_header" align="left">
            <thead>
                <tr>
                    <td colspan="7" style="font-size: large; font-weight: bolder; text-align: center;"><?= $company_arr[str_replace("'","",$cbo_company_name)] ?></td>
                </tr>
                <tr>
                    <td colspan="7" style="font-size: medium; font-weight: lighter; text-align: center;">Buyer wise sample Production Report</td>
                </tr>
                <tr>
                    <td colspan="7" style="font-size: medium; font-weight: lighter; text-align: center;">Date: <?= $fromDate.' To '.$toDate?></td>
                </tr>
                <tr>
                    <th width="328">Buyer/Sample Name/Garments Item</th>
                    <th width="80">Req. Qty.</th>
                    <th width="80">Cutting Qty.</th>
                    <th width="80">Sewing Qty.</th>
                    <th width="80">Rej. Qty</th>
                    <th width="80">Delivery To MKT.</th>
                    <th width="80">Delivery Qty.</th>
                </tr>
            </thead>
        </table>
        <div style="max-height:320px; overflow-y:auto; width:810px; float: left;" id="scroll_body">
            <table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="780" rules="all" id="table_body" align="left"> 
                <tbody>
                <?
                    foreach($main_data_arr as $buyer_id=>$buyer_data){ ?>
                        <tr bgcolor= "yellow" >
                            <td width="300" style="font-weight:bold; text-transform: uppercase;"><?= $buyer_arr[$buyer_id] ?></td>
                            <td width="80" align="right"><?= $buyer_wise_qty[$buyer_id]['req_qty'] ?></td>
                            <td width="80" align="right"><?= fn_number_format($buyer_wise_production[$buyer_id][127]['good_qty']) ?></td>
                            <td width="80" align="right"><?= fn_number_format($buyer_wise_production[$buyer_id][130]['good_qty']) ?></td>
                            <td width="80" align="right"><?= fn_number_format($buyer_wise_production[$buyer_id][130]['rej_qty']) ?></td>
                            <td width="80" align="right"><?= fn_number_format($buyer_wise_delv[$buyer_id]['delv_mkt_qty']) ?></td>
                            <td width="80" align="right"><?= fn_number_format($buyer_wise_delv[$buyer_id]['delv_qty']) ?></td>
                        </tr>
                        <? foreach($buyer_data as $sample_id=>$sample_data){ ?>
                            <tr>
                                <td width="300" style="padding-left:10px"><b><?= $sample_arr[$sample_id] ?></b></td>
                                <td width="80" align="right"><strong><?= $buyer_sample_wise_qty[$buyer_id][$sample_id]['req_qty'] ?></strong></td>
                                <td width="80" align="right"><strong><?= fn_number_format($buyer_sample_wise_production[$buyer_id][$sample_id][127]['good_qty']) ?></strong></td>
                                <td width="80" align="right"><strong><?= fn_number_format($buyer_sample_wise_production[$buyer_id][$sample_id][130]['good_qty']) ?></strong></td>
                                <td width="80" align="right"><strong><?= fn_number_format($buyer_sample_wise_production[$buyer_id][$sample_id][130]['rej_qty']) ?></strong></td>
                                <td width="80" align="right"><strong><?= fn_number_format($buyer_sample_wise_delv[$buyer_id][$sample_id]['delv_mkt_qty']) ?></strong></td>
                                <td width="80" align="right"><strong><?= fn_number_format($buyer_sample_wise_delv[$buyer_id][$sample_id]['delv_qty']) ?></strong></td>
                            </tr>
                            <? foreach($sample_data as $item_id=>$item_data){ ?>
                                <tr>
                                    <td width="300" style="padding-left:20px"><?= $item_arr[$item_id] ?></td>
                                    <td width="80" align="right"><?= $item_data['req_qty']  ?></td>
                                    <td width="80" align="right"><?= fn_number_format($production_data[$buyer_id][$sample_id][$item_id][127]['good_qty']) ?></td>
                                    <td width="80" align="right"><?= fn_number_format($production_data[$buyer_id][$sample_id][$item_id][130]['good_qty']) ?></td>
                                    <td width="80" align="right"><?= fn_number_format($production_data[$buyer_id][$sample_id][$item_id][130]['rej_qty']) ?></td>
                                    <td width="80" align="right"><?= fn_number_format($delv_qty_mkt_arr[$buyer_id][$sample_id][$item_id]) ?></td>
                                    <td width="80" align="right"><?= fn_number_format($delv_qty_arr[$buyer_id][$sample_id][$item_id]) ?></td>
                                </tr>
                            <? } 
                        } 
                        $total_req_qty+=$buyer_wise_qty[$buyer_id]['req_qty'];
                        $total_cutting_qty+=$buyer_wise_production[$buyer_id][127]['good_qty'];
                        $total_sewing_qty+=$buyer_wise_production[$buyer_id][130]['good_qty'];
                        $total_sewing_rej_qty+=$buyer_wise_production[$buyer_id][130]['rej_qty'];
                        $total_delv_mkt_qty+=$buyer_wise_delv[$buyer_id]['delv_mkt_qty'];
                        $total_delv_qty+=$buyer_wise_delv[$buyer_id]['delv_qty'];
                    }  ?>
                </tbody>        
            </table>
        </div>
        <table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="780" rules="all" id="table_bottom" align="left"> 
            <tfoot>
                <tr>
                <tr>
                    <th width="325">Grand Total</th>
                    <th width="80"><?= fn_number_format($total_req_qty) ?></th>
                    <th width="80"><?= fn_number_format($total_cutting_qty) ?></th>
                    <th width="80"><?= fn_number_format($total_sewing_qty) ?></th>
                    <th width="80"><?= fn_number_format($total_sewing_rej_qty) ?></th>
                    <th width="80"><?= fn_number_format($total_delv_mkt_qty) ?></th>
                    <th width="80"><?= fn_number_format($total_delv_qty) ?></th>
                </tr>
                </tr>
            </tfoot>
        </table>
    </div>
    <?

    $con = connect();
    execute_query("DELETE from gbl_temp_engine where user_id = ".$user_name." and ref_from in (1) and entry_form=134");
    oci_commit($con);
    disconnect($con);
    $html = ob_get_contents();
    ob_clean(); 
    foreach (glob("*.xls") as $filename) {
        @unlink($filename);
    }
    
    $name=time();
    $filename=$user_name."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w') or die('can not open');    
    $is_created = fwrite($create_new_doc,$html) or die('can not write');
    echo "$html****$filename";
    exit();
}

if($action=='req_qty_popup')   
{   
    extract($_REQUEST); 
    echo load_html_head_contents("Sample Prod. Qty", "../../", 1, 1,$unicode,'','');
    $data_ex = explode("_", $data);
    $mst_id = $data_ex[0];
    $smpl_id = $data_ex[1];
    $itm_id = $data_ex[2];

    $sql= "SELECT a.sample_name, a.sample_color,b.size_id,sum(b.bh_qty+b.plan_qty +b.dyeing_qty+ b.test_qty +b.self_qty) as total_qty from sample_development_dtls a,sample_development_size b where a.sample_mst_id=b.mst_id and a.id=b.dtls_id and a.entry_form_id in(117,449,203) and  a.status_active=1 and a.is_deleted=0 and a.sample_mst_id=$mst_id and a.sample_name=$smpl_id and a.gmts_item_id=$itm_id group by a.sample_name, a.sample_color,b.size_id order by b.size_id"; //and a.entry_form_id in(117,203)
     //echo $sql;die;
   
    $sql_sel=sql_select($sql);
     
    $color_size_wise_qnty=array();
    $size_all_arr=array();
    $sample_data = array();

    foreach($sql_sel as $val)
    {
        $color_size_wise_qnty[$val[csf("sample_color")]][$val[csf("size_id")]]["order_quantity"]+=$val[csf("total_qty")];
        $size_all_arr[$val[csf("size_id")]]=$val[csf("size_id")];
        $sample_data[$val[csf("sample_name")]][$val[csf("sample_color")]] = $val[csf("sample_color")];
    }
    // echo "<pre>";
    // print_r($sample_data);
    // echo "</pre>";
    $table_width = 290+(count($size_all_arr)*50);
                
 ?>
    <fieldset style="width: 98%">
    <legend> Sample Req. Qty.</legend>
     <div style="width:<? echo $table_width;?>px; margin-top:10px">
        <table cellspacing="0" width="<? echo $table_width;?>" class="rpt_table" cellpadding="0" border="1" rules="all">
            <thead>
                <th width="30" >SL</th>
                <th width="100" >Sample Name</th>
                <th width="100" >Color Name</th>
                <?
                    foreach($size_all_arr as $key=>$val)
                    {
                        ?>
                        <th width="50"><? echo $size_arr[$key] ;?></th>
                        <?
                    }
                ?>
                <th width="60">Total Qty</th>
            </thead>  
            <tbody>
        
            <?
            $i=1;
            $gr_size_total=array();
            foreach ($sample_data as $sample_id => $color_data) 
            {
                foreach ($color_data as $color_id => $row) 
                {    
                    $total_sizeqnty=0;            
                    ?>                         
                    <tr>
                        <td width="30" align="center"><? echo $i; ?></td>
                        <td width="100" align="center"><? echo  $sample_name_arr[$sample_id]; ?></td>
                        <td width="100" align="center"><? echo  $color_arr[$color_id]; ?></td>                        
                        <?
                            foreach($size_all_arr as $key=>$val)
                            {
                                ?>
                                <td width="50" align="right"><? echo $color_size_wise_qnty[$color_id][$key]['order_quantity'] ;?></td>
                                <?
                                $total_sizeqnty += $color_size_wise_qnty[$color_id][$key]['order_quantity'];
                                $gr_size_total[$key] += $color_size_wise_qnty[$color_id][$key]['order_quantity'];
                            }
                        ?>
                        <td width="60" align="right"><? echo  $total_sizeqnty; ?></td>
                    </tr>
         
                     <?
                     $i++;
                 }
             }
         ?>
         </tbody>
         <tfoot>
                <tr>
                    <th colspan="3" ></td>
                    <?
                    $total_qnty = 0;
                        foreach($size_all_arr as $size_key=>$val)
                        {
                            ?>
                            <th  align="right"><? echo $gr_size_total[$size_key] ;?></th>
                            <?
                            $total_qnty += $gr_size_total[$size_key];
                        }
                    ?>
                    <th align="right">  <? echo $total_qnty; ?></td>
                </tr>
            </tfoot>        
        </table>
     </div>
    </fieldset>
     
    <?
    exit();
}

if($action=='cut_qty_popup')   
{   
    extract($_REQUEST); 
    echo load_html_head_contents("Sample Prod. Qty", "../../", 1, 1,$unicode,'','');
    $data_ex    = explode("_", $data);
    $mst_id     = $data_ex[0];
    $smpl_id    = $data_ex[1];
    $itm_id     = $data_ex[2];
    $date_from  = $data_ex[3];
    $date_to    = $data_ex[4];
    $date_cond = "";
    if($date_from !="" && $date_to !="")
    {
        $date_cond = " and b.sewing_date between '$date_from' and '$date_to'";
    }
    // $sql= "SELECT a.sample_name, a.sample_color,b.size_id,sum(b.bh_qty+b.plan_qty +b.dyeing_qty+ b.test_qty +b.self_qty) as total_qty from sample_development_dtls a,sample_development_size b where a.sample_mst_id=b.mst_id and a.id=b.dtls_id and a.entry_form_id in(117,203) and  a.status_active=1 and a.is_deleted=0 and a.sample_mst_id=$mst_id and a.sample_name=$smpl_id and a.gmts_item_id=$itm_id group by a.sample_name, a.sample_color,b.size_id order by a.sample_color,b.size_id";
    $sql="SELECT b.sample_name,b.sewing_date,c.color_id, c.size_id,sum(c.size_pass_qty) as qty from sample_sewing_output_mst a, sample_sewing_output_dtls b,sample_sewing_output_colorsize c where a.id=b.sample_sewing_output_mst_id and b.id=c.sample_sewing_output_dtls_id and b.entry_form_id=127 and c.entry_form_id=127 and a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.sample_development_id=$mst_id and b.item_number_id=$itm_id and b.sample_name=$smpl_id $date_cond group by b.sample_name,b.sewing_date,c.color_id, c.size_id order by c.size_id";
    // echo $sql;die;
   
    $sql_sel=sql_select($sql);
     
    $color_size_wise_qnty=array();
    $size_all_arr=array();
    $sample_data = array();

    foreach($sql_sel as $val)
    {
        $color_size_wise_qnty[$val[csf("sewing_date")]][$val[csf("color_id")]][$val[csf("size_id")]]["order_quantity"]+=$val[csf("qty")];
        $size_all_arr[$val[csf("size_id")]]=$val[csf("size_id")];
        $sample_data[$val[csf("sewing_date")]][$val[csf("sample_name")]][$val[csf("color_id")]] = $val[csf("color_id")];
    }
    // echo "<pre>";
    // print_r($sample_data);
    // echo "</pre>";
    $table_width = 370+(count($size_all_arr)*50);
                
 ?>
    <fieldset style="width: 98%">
    <legend> Cutting Production Qty.</legend>
     <div style="width:<? echo $table_width;?>px; margin-top:10px">
        <table cellspacing="0" width="<? echo $table_width;?>" class="rpt_table" cellpadding="0" border="1" rules="all">
            <thead>
                <th width="30" >SL</th>
                <th width="80" >Production Date</th>
                <th width="100" >Sample Name</th>
                <th width="100" >Color Name</th>
                <?
                    foreach($size_all_arr as $key=>$val)
                    {
                        ?>
                        <th width="50"><? echo $size_arr[$key] ;?></th>
                        <?
                    }
                ?>
                <th width="60">Total Qty</th>
            </thead>  
            <tbody>
        
            <?
            $i=1;
            $gr_size_total=array();
            foreach ($sample_data as $prod_date => $date_data) 
            {
                foreach ($date_data as $sample_id => $color_data) 
                {
                    foreach ($color_data as $color_id => $row) 
                    {    
                        $total_sizeqnty=0;            
                        ?>                         
                        <tr>
                            <td width="30" align="center"><? echo $i; ?></td>
                            <td width="100" align="center"><? echo  change_date_format($prod_date); ?></td>
                            <td width="100" align="center"><? echo  $sample_name_arr[$sample_id]; ?></td>
                            <td width="100" align="center"><? echo  $color_arr[$color_id]; ?></td>                        
                            <?
                                foreach($size_all_arr as $key=>$val)
                                {
                                    ?>
                                    <td width="50" align="right"><? echo $color_size_wise_qnty[$prod_date][$color_id][$key]['order_quantity'] ;?></td>
                                    <?
                                    $total_sizeqnty += $color_size_wise_qnty[$prod_date][$color_id][$key]['order_quantity'];
                                    $gr_size_total[$key] += $color_size_wise_qnty[$prod_date][$color_id][$key]['order_quantity'];
                                }
                            ?>
                            <td width="60" align="right"><? echo  $total_sizeqnty; ?></td>
                        </tr>
             
                         <?
                         $i++;
                     }
                }
            }
         ?>
         </tbody>
         <tfoot>
                <tr>
                    <th colspan="4" ></td>
                    <?
                    $total_qnty = 0;
                        foreach($size_all_arr as $size_key=>$val)
                        {
                            ?>
                            <th  align="right"><? echo $gr_size_total[$size_key] ;?></th>
                            <?
                            $total_qnty += $gr_size_total[$size_key];
                        }
                    ?>
                    <th align="right">  <? echo $total_qnty; ?></td>
                </tr>
            </tfoot>        
        </table>
     </div>
    </fieldset>
     
    <?
    exit();
}

if($action=='sew_qty_popup')   
{   
    extract($_REQUEST); 
    echo load_html_head_contents("Sewing Prod. Qty", "../../", 1, 1,$unicode,'','');
    $data_ex = explode("_", $data);
    $mst_id = $data_ex[0];
    $smpl_id = $data_ex[1];
    $itm_id = $data_ex[2];
    $date_from  = $data_ex[3];
    $date_to    = $data_ex[4];
    $date_cond = "";
    if($date_from !="" && $date_to !="")
    {
        $date_cond = " and b.sewing_date between '$date_from' and '$date_to'";
    }
    
    $sql="SELECT b.sample_name,b.sewing_date,c.color_id, c.size_id,sum(c.size_pass_qty) as qty from sample_sewing_output_mst a, sample_sewing_output_dtls b,sample_sewing_output_colorsize c where a.id=b.sample_sewing_output_mst_id and b.id=c.sample_sewing_output_dtls_id and b.entry_form_id=130 and c.entry_form_id=130 and a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.sample_development_id=$mst_id and b.item_number_id=$itm_id and b.sample_name=$smpl_id $date_cond group by b.sample_name,b.sewing_date,c.color_id, c.size_id order by c.size_id";
    // echo $sql;die;
   
    $sql_sel=sql_select($sql);
     
    $color_size_wise_qnty=array();
    $size_all_arr=array();
    $sample_data = array();

    foreach($sql_sel as $val)
    {
        $color_size_wise_qnty[$val[csf("sewing_date")]][$val[csf("color_id")]][$val[csf("size_id")]]["order_quantity"]+=$val[csf("qty")];
        $size_all_arr[$val[csf("size_id")]]=$val[csf("size_id")];
        $sample_data[$val[csf("sewing_date")]][$val[csf("sample_name")]][$val[csf("color_id")]] = $val[csf("color_id")];
    }
    // echo "<pre>";
    // print_r($sample_data);
    // echo "</pre>";
    $table_width = 370+(count($size_all_arr)*50);
                
 ?>
    <fieldset style="width: 98%">
    <legend> Sewing Production Qty.</legend>
     <div style="width:<? echo $table_width;?>px; margin-top:10px">
        <table cellspacing="0" width="<? echo $table_width;?>" class="rpt_table" cellpadding="0" border="1" rules="all">
            <thead>
                <th width="30" >SL</th>
                <th width="80" >Production Date</th>
                <th width="100" >Sample Name</th>
                <th width="100" >Color Name</th>
                <?
                    foreach($size_all_arr as $key=>$val)
                    {
                        ?>
                        <th width="50"><? echo $size_arr[$key] ;?></th>
                        <?
                    }
                ?>
                <th width="60">Total Qty</th>
            </thead>  
            <tbody>
        
            <?
            $i=1;
            $gr_size_total=array();
            foreach ($sample_data as $prod_date => $date_data) 
            {
                foreach ($date_data as $sample_id => $color_data) 
                {
                    foreach ($color_data as $color_id => $row) 
                    {    
                        $total_sizeqnty=0;            
                        ?>                         
                        <tr>
                            <td width="30" align="center"><? echo $i; ?></td>
                            <td width="100" align="center"><? echo  change_date_format($prod_date); ?></td>
                            <td width="100" align="center"><? echo  $sample_name_arr[$sample_id]; ?></td>
                            <td width="100" align="center"><? echo  $color_arr[$color_id]; ?></td>                        
                            <?
                                foreach($size_all_arr as $key=>$val)
                                {
                                    ?>
                                    <td width="50" align="right"><? echo $color_size_wise_qnty[$prod_date][$color_id][$key]['order_quantity'] ;?></td>
                                    <?
                                    $total_sizeqnty += $color_size_wise_qnty[$prod_date][$color_id][$key]['order_quantity'];
                                    $gr_size_total[$key] += $color_size_wise_qnty[$prod_date][$color_id][$key]['order_quantity'];
                                }
                            ?>
                            <td width="60" align="right"><? echo  $total_sizeqnty; ?></td>
                        </tr>
             
                        <?
                        $i++;
                    }
                }
            }
         ?>
         </tbody>
         <tfoot>
                <tr>
                    <th colspan="4" ></td>
                    <?
                    $total_qnty = 0;
                        foreach($size_all_arr as $size_key=>$val)
                        {
                            ?>
                            <th  align="right"><? echo $gr_size_total[$size_key] ;?></th>
                            <?
                            $total_qnty += $gr_size_total[$size_key];
                        }
                    ?>
                    <th align="right">  <? echo $total_qnty; ?></td>
                </tr>
            </tfoot>        
        </table>
     </div>
    </fieldset>
     
    <?
    exit();
}

if($action=='embl_qty_popup')   
{   
    extract($_REQUEST); 
    echo load_html_head_contents("Sewing Prod. Qty", "../../", 1, 1,$unicode,'','');
    $data_ex = explode("_", $data);
    $mst_id = $data_ex[0];
    $smpl_id = $data_ex[1];
    $itm_id = $data_ex[2];
    $date_from  = $data_ex[3];
    $date_to    = $data_ex[4];
    $date_cond = "";
    if($date_from !="" && $date_to !="")
    {
        $date_cond = " and b.sewing_date between '$date_from' and '$date_to'";
    }
    
    $sql="SELECT b.sample_name,b.embel_name,b.embel_type,b.sewing_date,c.color_id, c.size_id,sum(c.size_pass_qty) as qty from sample_sewing_output_mst a, sample_sewing_output_dtls b,sample_sewing_output_colorsize c where a.id=b.sample_sewing_output_mst_id and b.id=c.sample_sewing_output_dtls_id and b.entry_form_id=128 and c.entry_form_id=128 and a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.sample_development_id=$mst_id and b.item_number_id=$itm_id and b.sample_name=$smpl_id $date_cond group by b.sample_name,b.embel_name,b.embel_type,b.sewing_date,c.color_id, c.size_id order by c.size_id";
    // echo $sql;die;
   
    $sql_sel=sql_select($sql);
     
    $color_size_wise_qnty=array();
    $size_all_arr=array();
    $sample_data = array();

    foreach($sql_sel as $val)
    {   
        $color_size_wise_qnty[$val[csf("sewing_date")]][$val[csf("embel_name")]][$val[csf("embel_type")]][$val[csf("sample_name")]][$val[csf("color_id")]][$val[csf("size_id")]]["order_quantity"]+=$val[csf("qty")];
        $size_all_arr[$val[csf("size_id")]]=$val[csf("size_id")];
        $sample_data[$val[csf("sewing_date")]][$val[csf("embel_name")]][$val[csf("embel_type")]][$val[csf("sample_name")]][$val[csf("color_id")]] = $val[csf("color_id")];
    }
    // echo "<pre>";
    // print_r($sample_data);
    // echo "</pre>";
    $table_width = 590+(count($size_all_arr)*50);
                
 ?>
    <fieldset style="width: 98%">
    <legend> Embl. Production Qty.</legend>
     <div style="width:<? echo $table_width;?>px; margin-top:10px">
        <table cellspacing="0" width="<? echo $table_width;?>" class="rpt_table" cellpadding="0" border="1" rules="all">
            <thead>
                <th width="30" >SL</th>
                <th width="100" >Production Date</th>
                <th width="100" >Emb Name</th>
                <th width="100" >Emb Type</th>
                <th width="100" >Sample Name</th>
                <th width="100" >Color Name</th>
                <?
                    foreach($size_all_arr as $key=>$val)
                    {
                        ?>
                        <th width="50"><? echo $size_arr[$key] ;?></th>
                        <?
                    }
                ?>
                <th width="60">Total Qty</th>
            </thead>  
            <tbody>
        
            <?
            $i=1;
            $gr_size_total=array();
            foreach ($sample_data as $prod_date => $date_data) 
            {
                foreach ($date_data as $embl_name_id => $embl_name_data) 
                {
                    foreach ($embl_name_data as $emble_type_id => $emble_type_data) 
                    {
                        foreach ($emble_type_data as $sample_id => $color_data) 
                        {
                            foreach ($color_data as $color_id => $row) 
                            {    
                                $total_sizeqnty=0;  
                                 
                                if($embl_name_id==1) $emb_type=$emblishment_print_type;
                                else if($embl_name_id==2) $emb_type=$emblishment_embroy_type;
                                else if($embl_name_id==3) $emb_type=$emblishment_wash_type;
                                else if($embl_name_id==4) $emb_type=$emblishment_spwork_type;
                                else if($embl_name_id==5) $emb_type=$emblishment_gmts_type;
                                else $emb_type="";         
                                ?>                         
                                <tr>
                                    <td width="30" align="center"><? echo $i; ?></td>
                                    <td width="100" align="center"><? echo  change_date_format($prod_date); ?></td>
                                    <td width="100" align="center"><? echo  $emblishment_name_array[$embl_name_id]; ?></td>
                                    <td width="100" align="center"><? echo  $emb_type[$emble_type_id]; ?></td>
                                    <td width="100" align="center"><? echo  $sample_name_arr[$sample_id]; ?></td>
                                    <td width="100" align="center"><? echo  $color_arr[$color_id]; ?></td>                        
                                    <?
                                        foreach($size_all_arr as $key=>$val)
                                        {
                                            ?>
                                            <td width="50" align="right"><? echo $color_size_wise_qnty[$prod_date][$embl_name_id][$emble_type_id][$sample_id][$color_id][$key]['order_quantity'] ;?></td>
                                            <?
                                            $total_sizeqnty += $color_size_wise_qnty[$prod_date][$embl_name_id][$emble_type_id][$sample_id][$color_id][$key]['order_quantity'];
                                            $gr_size_total[$key] += $color_size_wise_qnty[$prod_date][$embl_name_id][$emble_type_id][$sample_id][$color_id][$key]['order_quantity'];
                                        }
                                    ?>
                                    <td width="60" align="right"><? echo  $total_sizeqnty; ?></td>
                                </tr>
                     
                                 <?
                                 $i++;
                            }
                        }
                    }
                }
            }
         ?>
         </tbody>
         <tfoot>
                <tr>
                    <th colspan="6" ></td>
                    <?
                    $total_qnty = 0;
                        foreach($size_all_arr as $size_key=>$val)
                        {
                            ?>
                            <th  align="right"><? echo $gr_size_total[$size_key] ;?></th>
                            <?
                            $total_qnty += $gr_size_total[$size_key];
                        }
                    ?>
                    <th align="right">  <? echo $total_qnty; ?></td>
                </tr>
            </tfoot>        
        </table>
     </div>
    </fieldset>
     
    <?
    exit();
}

if($action=='rej_qty_popup')   
{   
    extract($_REQUEST); 
    echo load_html_head_contents("Reject Prod. Qty", "../../", 1, 1,$unicode,'','');
    $data_ex = explode("_", $data);
    $mst_id = $data_ex[0];
    $smpl_id = $data_ex[1];
    $itm_id = $data_ex[2];
    $date_from  = $data_ex[3];
    $date_to    = $data_ex[4];
    $date_cond = "";
    if($date_from !="" && $date_to !="")
    {
        $date_cond = " and b.sewing_date between '$date_from' and '$date_to'";
    }

    // $sql= "SELECT a.sample_name, a.sample_color,b.size_id,sum(b.bh_qty+b.plan_qty +b.dyeing_qty+ b.test_qty +b.self_qty) as total_qty from sample_development_dtls a,sample_development_size b where a.sample_mst_id=b.mst_id and a.id=b.dtls_id and a.entry_form_id in(117,203) and  a.status_active=1 and a.is_deleted=0 and a.sample_mst_id=$mst_id and a.sample_name=$smpl_id and a.gmts_item_id=$itm_id group by a.sample_name, a.sample_color,b.size_id order by a.sample_color,b.size_id";
    $sql="SELECT b.sample_name,c.color_id, c.size_id,sum(c.size_rej_qty) as qty from sample_sewing_output_mst a, sample_sewing_output_dtls b,sample_sewing_output_colorsize c where a.id=b.sample_sewing_output_mst_id and b.id=c.sample_sewing_output_dtls_id and b.entry_form_id=127 and c.entry_form_id=127 and a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.sample_development_id=$mst_id and b.item_number_id=$itm_id and b.sample_name=$smpl_id $date_cond group by b.sample_name,c.color_id, c.size_id order by c.size_id";
    // echo $sql;die;
   
    $sql_sel=sql_select($sql);
     
    $color_size_wise_qnty=array();
    $size_all_arr=array();
    $sample_data = array();

    foreach($sql_sel as $val)
    {
        $color_size_wise_qnty[$val[csf("color_id")]][$val[csf("size_id")]]["rej_qty"]+=$val[csf("qty")];
        $size_all_arr[$val[csf("size_id")]]=$val[csf("size_id")];
        $sample_data[$val[csf("sample_name")]][$val[csf("color_id")]] = $val[csf("color_id")];
    }
    // echo "<pre>";
    // print_r($sample_data);
    // echo "</pre>";
    $table_width = 290+(count($size_all_arr)*50);
                
 ?>
    <fieldset style="width: 98%">
    <legend> Cutting Reject Qty.</legend>
     <div style="width:<? echo $table_width;?>px; margin-top:10px">
        <table cellspacing="0" width="<? echo $table_width;?>" class="rpt_table" cellpadding="0" border="1" rules="all">
            <thead>
                <th width="30" >SL</th>
                <th width="100" >Sample Name</th>
                <th width="100" >Color Name</th>
                <?
                    foreach($size_all_arr as $key=>$val)
                    {
                        ?>
                        <th width="50"><? echo $size_arr[$key] ;?></th>
                        <?
                    }
                ?>
                <th width="60">Total Rej. Qty</th>
            </thead>  
            <tbody>
        
            <?
            $i=1;
            $gr_size_total=array();
            foreach ($sample_data as $sample_id => $color_data) 
            {
                foreach ($color_data as $color_id => $row) 
                {    
                    $total_sizeqnty=0;            
                    ?>                         
                    <tr>
                        <td width="30" align="center"><? echo $i; ?></td>
                        <td width="100" align="center"><? echo  $sample_name_arr[$sample_id]; ?></td>
                        <td width="100" align="center"><? echo  $color_arr[$color_id]; ?></td>                        
                        <?
                            foreach($size_all_arr as $key=>$val)
                            {
                                ?>
                                <td width="50" align="right"><? echo $color_size_wise_qnty[$color_id][$key]['rej_qty'] ;?></td>
                                <?
                                $total_sizeqnty += $color_size_wise_qnty[$color_id][$key]['rej_qty'];
                                $gr_size_total[$key] += $color_size_wise_qnty[$color_id][$key]['rej_qty'];
                            }
                        ?>
                        <td width="60" align="right"><? echo  $total_sizeqnty; ?></td>
                    </tr>
         
                     <?
                     $i++;
                 }
             }
         ?>
         </tbody>
         <tfoot>
                <tr>
                    <th colspan="3" ></td>
                    <?
                    $total_qnty = 0;
                        foreach($size_all_arr as $size_key=>$val)
                        {
                            ?>
                            <th  align="right"><? echo $gr_size_total[$size_key] ;?></th>
                            <?
                            $total_qnty += $gr_size_total[$size_key];
                        }
                    ?>
                    <th align="right">  <? echo $total_qnty; ?></td>
                </tr>
            </tfoot>        
        </table>
     </div>
    </fieldset>

    <!-- ==================================== ====================================-->
    <?
    $sql="SELECT b.sample_name,c.color_id, c.size_id,sum(c.size_rej_qty) as qty from sample_sewing_output_mst a, sample_sewing_output_dtls b,sample_sewing_output_colorsize c where a.id=b.sample_sewing_output_mst_id and b.id=c.sample_sewing_output_dtls_id and b.entry_form_id=130 and c.entry_form_id=130 and a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.sample_development_id=$mst_id and b.item_number_id=$itm_id and b.sample_name=$smpl_id $date_cond group by b.sample_name,c.color_id, c.size_id order by c.size_id";
    // echo $sql;die;
   
    $sql_sel=sql_select($sql);
     
    $color_size_wise_qnty=array();
    $size_all_arr=array();
    $sample_data = array();

    foreach($sql_sel as $val)
    {
        $color_size_wise_qnty[$val[csf("color_id")]][$val[csf("size_id")]]["order_quantity"]+=$val[csf("qty")];
        $size_all_arr[$val[csf("size_id")]]=$val[csf("size_id")];
        $sample_data[$val[csf("sample_name")]][$val[csf("color_id")]] = $val[csf("color_id")];
    }
    // echo "<pre>";
    // print_r($sample_data);
    // echo "</pre>";
    $table_width = 290+(count($size_all_arr)*50);
                
 ?>
    <fieldset style="width: 98%">
    <legend> Sewing Reject Qty.</legend>
     <div style="width:<? echo $table_width;?>px; margin-top:10px">
        <table cellspacing="0" width="<? echo $table_width;?>" class="rpt_table" cellpadding="0" border="1" rules="all">
            <thead>
                <th width="30" >SL</th>
                <th width="100" >Sample Name</th>
                <th width="100" >Color Name</th>
                <?
                    foreach($size_all_arr as $key=>$val)
                    {
                        ?>
                        <th width="50"><? echo $size_arr[$key] ;?></th>
                        <?
                    }
                ?>
                <th width="60">Total Qty</th>
            </thead>  
            <tbody>
        
            <?
            $i=1;
            $gr_size_total=array();
            foreach ($sample_data as $sample_id => $color_data) 
            {
                foreach ($color_data as $color_id => $row) 
                {    
                    $total_sizeqnty=0;            
                    ?>                         
                    <tr>
                        <td width="30" align="center"><? echo $i; ?></td>
                        <td width="100" align="center"><? echo  $sample_name_arr[$sample_id]; ?></td>
                        <td width="100" align="center"><? echo  $color_arr[$color_id]; ?></td>                        
                        <?
                            foreach($size_all_arr as $key=>$val)
                            {
                                ?>
                                <td width="50" align="right"><? echo $color_size_wise_qnty[$color_id][$key]['order_quantity'] ;?></td>
                                <?
                                $total_sizeqnty += $color_size_wise_qnty[$color_id][$key]['order_quantity'];
                                $gr_size_total[$key] += $color_size_wise_qnty[$color_id][$key]['order_quantity'];
                            }
                        ?>
                        <td width="60" align="right"><? echo  $total_sizeqnty; ?></td>
                    </tr>
         
                     <?
                     $i++;
                 }
             }
         ?>
         </tbody>
         <tfoot>
                <tr>
                    <th colspan="3" ></td>
                    <?
                    $total_qnty = 0;
                        foreach($size_all_arr as $size_key=>$val)
                        {
                            ?>
                            <th  align="right"><? echo $gr_size_total[$size_key] ;?></th>
                            <?
                            $total_qnty += $gr_size_total[$size_key];
                        }
                    ?>
                    <th align="right">  <? echo $total_qnty; ?></td>
                </tr>
            </tfoot>        
        </table>
     </div>
    </fieldset>  

    <!-- =================================== ================================== -->  
    <? 
    $sql="SELECT b.sample_name,c.color_id, c.size_id,sum(c.size_rej_qty) as qty from sample_sewing_output_mst a, sample_sewing_output_dtls b,sample_sewing_output_colorsize c where a.id=b.sample_sewing_output_mst_id and b.id=c.sample_sewing_output_dtls_id and b.entry_form_id=128 and c.entry_form_id=128 and a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.sample_development_id=$mst_id and b.item_number_id=$itm_id and b.sample_name=$smpl_id $date_cond group by b.sample_name,c.color_id, c.size_id order by c.size_id";
    // echo $sql;die;
   
    $sql_sel=sql_select($sql);
     
    $color_size_wise_qnty=array();
    $size_all_arr=array();
    $sample_data = array();

    foreach($sql_sel as $val)
    {
        $color_size_wise_qnty[$val[csf("color_id")]][$val[csf("size_id")]]["order_quantity"]+=$val[csf("qty")];
        $size_all_arr[$val[csf("size_id")]]=$val[csf("size_id")];
        $sample_data[$val[csf("sample_name")]][$val[csf("color_id")]] = $val[csf("color_id")];
    }
    // echo "<pre>";
    // print_r($sample_data);
    // echo "</pre>";
    $table_width = 290+(count($size_all_arr)*50);
                
 ?>
    <fieldset style="width: 98%">
    <legend> Embl Reject Qty.</legend>
     <div style="width:<? echo $table_width;?>px; margin-top:10px">
        <table cellspacing="0" width="<? echo $table_width;?>" class="rpt_table" cellpadding="0" border="1" rules="all">
            <thead>
                <th width="30" >SL</th>
                <th width="100" >Sample Name</th>
                <th width="100" >Color Name</th>
                <?
                    foreach($size_all_arr as $key=>$val)
                    {
                        ?>
                        <th width="50"><? echo $size_arr[$key] ;?></th>
                        <?
                    }
                ?>
                <th width="60">Total Qty</th>
            </thead>  
            <tbody>
        
            <?
            $i=1;
            $gr_size_total=array();
            foreach ($sample_data as $sample_id => $color_data) 
            {
                foreach ($color_data as $color_id => $row) 
                {    
                    $total_sizeqnty=0;            
                    ?>                         
                    <tr>
                        <td width="30" align="center"><? echo $i; ?></td>
                        <td width="100" align="center"><? echo  $sample_name_arr[$sample_id]; ?></td>
                        <td width="100" align="center"><? echo  $color_arr[$color_id]; ?></td>                        
                        <?
                            foreach($size_all_arr as $key=>$val)
                            {
                                ?>
                                <td width="50" align="right"><? echo $color_size_wise_qnty[$color_id][$key]['order_quantity'] ;?></td>
                                <?
                                $total_sizeqnty += $color_size_wise_qnty[$color_id][$key]['order_quantity'];
                                $gr_size_total[$key] += $color_size_wise_qnty[$color_id][$key]['order_quantity'];
                            }
                        ?>
                        <td width="60" align="right"><? echo  $total_sizeqnty; ?></td>
                    </tr>
         
                     <?
                     $i++;
                 }
             }
         ?>
         </tbody>
         <tfoot>
                <tr>
                    <th colspan="3" ></td>
                    <?
                    $total_qnty = 0;
                        foreach($size_all_arr as $size_key=>$val)
                        {
                            ?>
                            <th  align="right"><? echo $gr_size_total[$size_key] ;?></th>
                            <?
                            $total_qnty += $gr_size_total[$size_key];
                        }
                    ?>
                    <th align="right">  <? echo $total_qnty; ?></td>
                </tr>
            </tfoot>        
        </table>
     </div>
    </fieldset>
    
    <?
    exit();
}

if($action=='del_qty_popup')   
{   
    extract($_REQUEST); 
    echo load_html_head_contents("Delivery Prod. Qty", "../../", 1, 1,$unicode,'','');
    $data_ex = explode("_", $data);
    $mst_id = $data_ex[0];
    $smpl_id = $data_ex[1];
    $itm_id = $data_ex[2];
    $date_from  = $data_ex[3];
    $date_to    = $data_ex[4];
    $date_cond = "";
    if($date_from !="" && $date_to !="")
    {
        $date_cond = " and b.delivery_date between '$date_from' and '$date_to'";
    }
    
    $sql="SELECT b.sample_name,b.delivery_date,c.color_id, c.size_id,sum(c.size_pass_qty) as qty from sample_ex_factory_mst a, sample_ex_factory_dtls b,sample_ex_factory_colorsize c where a.id=b.sample_ex_factory_mst_id and a.id=c.sample_ex_factory_mst_id and b.id=c.sample_ex_factory_dtls_id and b.entry_form_id in( 132) and c.entry_form_id in( 132) and a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.sample_development_id=$mst_id and b.gmts_item_id=$itm_id and b.sample_name=$smpl_id $date_cond group by b.sample_name,b.delivery_date,c.color_id, c.size_id order by c.size_id";
    // echo $sql;die;
   
    $sql_sel=sql_select($sql);
     
    $color_size_wise_qnty=array();
    $size_all_arr=array();
    $sample_data = array();

    foreach($sql_sel as $val)
    {
        $color_size_wise_qnty[$val[csf("delivery_date")]][$val[csf("color_id")]][$val[csf("size_id")]]["order_quantity"]+=$val[csf("qty")];
        $size_all_arr[$val[csf("size_id")]]=$val[csf("size_id")];
        $sample_data[$val[csf("delivery_date")]][$val[csf("sample_name")]][$val[csf("color_id")]] = $val[csf("color_id")];
    }
    // echo "<pre>";
    // print_r($sample_data);
    // echo "</pre>";
    $table_width = 370+(count($size_all_arr)*50);
                
 ?>
    <fieldset style="width: 98%">
    <legend> Delivery Qty.</legend>
     <div style="width:<? echo $table_width;?>px; margin-top:10px">
        <table cellspacing="0" width="<? echo $table_width;?>" class="rpt_table" cellpadding="0" border="1" rules="all">
            <thead>
                <th width="30" >SL</th>
                <th width="80" >Delivery Date</th>
                <th width="100" >Sample Name</th>
                <th width="100" >Color Name</th>
                <?
                    foreach($size_all_arr as $key=>$val)
                    {
                        ?>
                        <th width="50"><? echo $size_arr[$key] ;?></th>
                        <?
                    }
                ?>
                <th width="60">Total Qty</th>
            </thead>  
            <tbody>
        
            <?
            $i=1;
            $gr_size_total=array();
            foreach ($sample_data as $del_date => $del_data) 
            {
                foreach ($del_data as $sample_id => $color_data) 
                {
                    foreach ($color_data as $color_id => $row) 
                    {    
                        $total_sizeqnty=0;            
                        ?>                         
                        <tr>
                            <td width="30" align="center"><? echo $i; ?></td>
                            <td width="100" align="center"><? echo  change_date_format($del_date); ?></td>
                            <td width="100" align="center"><? echo  $sample_name_arr[$sample_id]; ?></td>
                            <td width="100" align="center"><? echo  $color_arr[$color_id]; ?></td>                        
                            <?
                                foreach($size_all_arr as $key=>$val)
                                {
                                    ?>
                                    <td width="50" align="right"><? echo $color_size_wise_qnty[$del_date][$color_id][$key]['order_quantity'] ;?></td>
                                    <?
                                    $total_sizeqnty += $color_size_wise_qnty[$del_date][$color_id][$key]['order_quantity'];
                                    $gr_size_total[$key] += $color_size_wise_qnty[$del_date][$color_id][$key]['order_quantity'];
                                }
                            ?>
                            <td width="60" align="right"><? echo  $total_sizeqnty; ?></td>
                        </tr>
             
                        <?
                        $i++;
                    }
                }
            }
         ?>
         </tbody>
         <tfoot>
                <tr>
                    <th colspan="4" ></td>
                    <?
                    $total_qnty = 0;
                        foreach($size_all_arr as $size_key=>$val)
                        {
                            ?>
                            <th  align="right"><? echo $gr_size_total[$size_key] ;?></th>
                            <?
                            $total_qnty += $gr_size_total[$size_key];
                        }
                    ?>
                    <th align="right">  <? echo $total_qnty; ?></td>
                </tr>
            </tfoot>        
        </table>
     </div>
    </fieldset>
     
    <?
    exit();
} 

if($action=='del_mkt_qty_popup')   
{   
    extract($_REQUEST); 
    echo load_html_head_contents("Delivery Prod. Qty", "../../", 1, 1,$unicode,'','');
    $data_ex = explode("_", $data);
    $mst_id = $data_ex[0];
    $smpl_id = $data_ex[1];
    $itm_id = $data_ex[2];
    $date_from  = $data_ex[3];
    $date_to    = $data_ex[4];
    $date_cond = "";
    if($date_from !="" && $date_to !="")
    {
        $date_cond = " and b.delivery_date between '$date_from' and '$date_to'";
    }
    
    $sql="SELECT b.sample_name,b.delivery_date,c.color_id, c.size_id,sum(c.size_pass_qty) as qty from sample_ex_factory_mst a, sample_ex_factory_dtls b,sample_ex_factory_colorsize c where a.id=b.sample_ex_factory_mst_id and a.id=c.sample_ex_factory_mst_id and b.id=c.sample_ex_factory_dtls_id and b.entry_form_id in(396) and c.entry_form_id in(396) and a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.sample_development_id=$mst_id and b.gmts_item_id=$itm_id and b.sample_name=$smpl_id $date_cond group by b.sample_name,b.delivery_date,c.color_id, c.size_id order by c.size_id";
    // echo $sql;die;
   
    $sql_sel=sql_select($sql);
     
    $color_size_wise_qnty=array();
    $size_all_arr=array();
    $sample_data = array();

    foreach($sql_sel as $val)
    {
        $color_size_wise_qnty[$val[csf("delivery_date")]][$val[csf("color_id")]][$val[csf("size_id")]]["order_quantity"]+=$val[csf("qty")];
        $size_all_arr[$val[csf("size_id")]]=$val[csf("size_id")];
        $sample_data[$val[csf("delivery_date")]][$val[csf("sample_name")]][$val[csf("color_id")]] = $val[csf("color_id")];
    }
    // echo "<pre>";
    // print_r($sample_data);
    // echo "</pre>";
    $table_width = 370+(count($size_all_arr)*50);
                
 ?>
    <fieldset style="width: 98%">
    <legend> Delivery Qty.</legend>
     <div style="width:<? echo $table_width;?>px; margin-top:10px">
        <table cellspacing="0" width="<? echo $table_width;?>" class="rpt_table" cellpadding="0" border="1" rules="all">
            <thead>
                <th width="30" >SL</th>
                <th width="80" >Delivery Date</th>
                <th width="100" >Sample Name</th>
                <th width="100" >Color Name</th>
                <?
                    foreach($size_all_arr as $key=>$val)
                    {
                        ?>
                        <th width="50"><? echo $size_arr[$key] ;?></th>
                        <?
                    }
                ?>
                <th width="60">Total Qty</th>
            </thead>  
            <tbody>
        
            <?
            $i=1;
            $gr_size_total=array();
            foreach ($sample_data as $del_date => $del_data) 
            {
                foreach ($del_data as $sample_id => $color_data) 
                {
                    foreach ($color_data as $color_id => $row) 
                    {    
                        $total_sizeqnty=0;            
                        ?>                         
                        <tr>
                            <td width="30" align="center"><? echo $i; ?></td>
                            <td width="100" align="center"><? echo  change_date_format($del_date); ?></td>
                            <td width="100" align="center"><? echo  $sample_name_arr[$sample_id]; ?></td>
                            <td width="100" align="center"><? echo  $color_arr[$color_id]; ?></td>                        
                            <?
                                foreach($size_all_arr as $key=>$val)
                                {
                                    ?>
                                    <td width="50" align="right"><? echo $color_size_wise_qnty[$del_date][$color_id][$key]['order_quantity'] ;?></td>
                                    <?
                                    $total_sizeqnty += $color_size_wise_qnty[$del_date][$color_id][$key]['order_quantity'];
                                    $gr_size_total[$key] += $color_size_wise_qnty[$del_date][$color_id][$key]['order_quantity'];
                                }
                            ?>
                            <td width="60" align="right"><? echo  $total_sizeqnty; ?></td>
                        </tr>
             
                        <?
                        $i++;
                    }
                }
            }
         ?>
         </tbody>
         <tfoot>
                <tr>
                    <th colspan="4" ></td>
                    <?
                    $total_qnty = 0;
                        foreach($size_all_arr as $size_key=>$val)
                        {
                            ?>
                            <th  align="right"><? echo $gr_size_total[$size_key] ;?></th>
                            <?
                            $total_qnty += $gr_size_total[$size_key];
                        }
                    ?>
                    <th align="right">  <? echo $total_qnty; ?></td>
                </tr>
            </tfoot>        
        </table>
     </div>
    </fieldset>
     
    <?
    exit();
} 
?>