<? 
include('../../../includes/common.php');
session_start();
extract($_REQUEST);
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$date=date('Y-m-d');
$user_id=$_SESSION['logic_erp']['user_id'];
$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name");
$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");

if ($action=="load_drop_down_buyer")
{ 
	echo create_drop_down( "cbo_party_id", 140, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "--Select Party--", $selected, "" );
	exit();   	 
}

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	$party_id=str_replace("'","",$cbo_party_id);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	
	ob_start();

	if($type==1)
	{
		?>
        <div>
         <fieldset style="width:930px;">
            <table cellpadding="0" cellspacing="0" width="910">
                <tr  class="form_caption" style="border:none;">
                   <td align="center" width="100%" colspan="10" style="font-size:20px"><strong><? echo $report_title; ?></strong></td>
                </tr>
                <tr class="form_caption" style="border:none;">
                   <td align="center" width="100%" colspan="10" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
                </tr>
                <tr class="form_caption" style="border:none;">
                    <td align="center" width="100%" colspan="10" style="font-size:12px">
                        <? if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="") echo "From ".change_date_format(str_replace("'","",$txt_date_from),'dd-mm-yyyy')." To ".change_date_format(str_replace("'","",$txt_date_to),'dd-mm-yyyy')."" ;?>
                    </td>
                </tr>
            </table>
            <table width="910" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table">
                <thead>
                    <th width="40">SL</th>
                    <th width="80">Prod. Date</th>
                    <th width="130">Party Name</th>
                    <th width="120">Batch No</th>
                    <th width="30">Ext.</th>                            
                    <th width="120">Color</th>
                    <th width="100">Batch Qty</th>
                    <th width="100">Finish /Prod Qty.</th>
                    <th width="100">Loss/ Gain Qty</th>
                    <th>Loss/ Gain %</th>
                </thead>
            </table>
        <div style="max-height:300px; overflow-y:scroll; width:930px" id="scroll_body" >
            <table width="910" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body">
            <?
                if(str_replace("'","",$cbo_party_id)==0) $party_cond=""; else  $party_cond=" and a.party_id=$cbo_party_id";
    
                if($db_type==0)
                {
                    if( $date_from==0 && $date_to==0 ) $prod_date_cond=""; else $prod_date_cond= " and a.product_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'";
                }
                else if($db_type==2)
                {
                    if( $date_from==0 && $date_to==0 ) $prod_date_cond=""; else $prod_date_cond= " and a.product_date between '".change_date_format($date_from,'','',1)."' and '".change_date_format($date_to,'','',1)."'";
                }
                	
				$batch_arr=array();
				$sql_batch="select a.id, a.batch_no, a.extention_no, sum(b.batch_qnty) as batch_qnty from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.batch_no, a.extention_no";
				$sql_batch_result=sql_select($sql_batch);
				foreach ($sql_batch_result as $row)
				{
					$batch_arr[$row[csf('id')]]['batch_no']=$row[csf('batch_no')];
					$batch_arr[$row[csf('id')]]['extention_no']=$row[csf('extention_no')];
					$batch_arr[$row[csf('id')]]['batch_qnty']=$row[csf('batch_qnty')];
				}
				$sql_dtls="select a.product_date, a.party_id, b.batch_id, b.color_id, sum(b.product_qnty) as product_qnty
                from subcon_production_mst a, subcon_production_dtls b where a.id=b.mst_id and a.company_id=$cbo_company_id and a.product_type=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $party_cond $prod_date_cond group by a.product_date, a.party_id, b.batch_id, b.color_id order by a.product_date, a.party_id Desc";
				$sql_dtls_result=sql_select($sql_dtls); $i=1;
				foreach ($sql_dtls_result as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    $loss_gain_qty=0;
					$loss_gain_per=0;
					$batch_qty=$batch_arr[$row[csf('batch_id')]]['batch_qnty'];
					$loss_gain_qty=$batch_qty-$row[csf('product_qnty')];
					$loss_gain_per=($loss_gain_qty/$batch_qty)*100;
                    ?>
                    <tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                         <td width="40"><? echo $i; ?></td>
                         <td width="80"><? echo '&nbsp;'.change_date_format($row[csf('product_date')]); ?></td>
                         <td width="130"><? echo $buyer_arr[$row[csf('party_id')]]; ?></td>
                         <td width="120"><? echo $batch_arr[$row[csf('batch_id')]]['batch_no']; ?></td>
                         <td width="30"><? echo $batch_arr[$row[csf('batch_id')]]['extention_no']; ?></td>
                         <td width="120" align="center" ><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
                         <td width="100" align="right" ><? echo number_format($batch_qty,2,'.',''); ?></td>
                         <td width="100" align="right" ><? echo number_format($row[csf('product_qnty')],2,'.',''); ?></td>
                         <td width="100" align="right" ><? echo number_format($loss_gain_qty,2,'.',''); ?></td>
                         <td align="right"><? echo number_format($loss_gain_per,2,'.','').'%'; ?></td>
                    </tr>
                    <?	
					$grand_batch_qnty+=$batch_qty;
					$grand_product_qnty+=$row[csf('product_qnty')];
					$grand_loss_gain_qty+=$loss_gain_qty;
                    $i++;
                }
                ?>
                <tr class="tbl_bottom">
                    <td colspan="6" align="right"><b>Total:</b></td>
                    <td align="right"><b><? echo number_format($grand_batch_qnty,2); ?></b></td>
                    <td align="right"><b><? echo number_format($grand_product_qnty,2); ?></b></td>
                    <td align="right"><b><? echo number_format($grand_loss_gain_qty,2); ?></b></td>
                    <td align="right"><b><? //echo number_format($bal_row,2); ?></b></td>
                </tr>
            </table>
            </div>
            </fieldset>
            </div>
        <?
	}
	
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
    echo "$html**$filename"; 
    exit();
}
?>