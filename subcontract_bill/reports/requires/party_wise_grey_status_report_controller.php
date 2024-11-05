<? 
include('../../../includes/common.php');
session_start();
extract($_REQUEST);
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$date=date('Y-m-d');
$user_id=$_SESSION['logic_erp']['user_id'];

if ($action=="load_drop_down_buyer")
{ 
	echo create_drop_down( "cbo_party_id", 160, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "--Select Party--", $selected, "" );
	exit();   	 
}

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	
	$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
	$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$order_arr=return_library_array( "select id, order_no from subcon_ord_dtls", "id", "order_no");
	
	if(str_replace("'","",$cbo_party_id)==0) $party_rec_cond=""; else  $party_rec_cond=" and a.party_id=$cbo_party_id";
	if(str_replace("'","",$cbo_party_id)==0) $party_batch_cond=""; else  $party_batch_cond=" and a.party_id=$cbo_party_id";
	
	if($db_type==0)
	{
		if( $date_from==0 && $date_to==0 ) $batch_date_cond=""; else $batch_date_cond= " and d.batch_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'";
		if( $date_from==0 && $date_to==0 ) $rec_date_cond=""; else $rec_date_cond= " and a.subcon_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'";
		if($date_from=="") $rec_date_ope_cond=""; else $rec_date_ope_cond= " and a.subcon_date <'".change_date_format($date_from,'yyyy-mm-dd')."'";
		if($date_from=="") $batch_date_ope_cond=""; else $batch_date_ope_cond= " and d.batch_date <'".change_date_format($date_from,'yyyy-mm-dd')."'";
	}
	else if ($db_type==2)
	{
		if( $date_from=='' && $date_to=='' ) $batch_date_cond=""; else $batch_date_cond= " and d.batch_date between '".change_date_format($date_from,'','',1)."' and '".change_date_format($date_to,'','',1)."'";
		if( $date_from=='' && $date_to=='' ) $rec_date_cond=""; else $rec_date_cond= " and a.subcon_date between '".change_date_format($date_from,'','',1)."' and '".change_date_format($date_to,'','',1)."'";
		if($date_from=="") $rec_date_ope_cond=""; else $rec_date_ope_cond= " and a.subcon_date <'".change_date_format($date_from,'','',1)."'";
		if($date_from=="") $batch_date_ope_cond=""; else $batch_date_ope_cond= " and d.batch_date <'".change_date_format($date_from,'','',1)."'";
	}
	
	ob_start();
	?>
	<div>
		<table cellpadding="0" cellspacing="0" width="1180">
        	<tr class="form_caption" style="border:none;">
			   <td align="center" width="100%" colspan="13" style="font-size:20px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
			</tr>
			<tr class="form_caption" style="border:none;">
			   <td align="center" width="100%" colspan="13" style="font-size:16px"><strong><? echo $report_title; ?></strong></td>
			</tr>
			<tr class="form_caption" style="border:none;">
				<td align="center" width="100%" colspan="13" style="font-size:14px">
					<? if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="") echo "Grey Status: ".change_date_format(str_replace("'","",$txt_date_from),'dd-mm-yyyy')." To ".change_date_format(str_replace("'","",$txt_date_to),'dd-mm-yyyy')."" ;?>
				</td>
			</tr>
            <tr class="form_caption" style="border:none;">
			   <td align="center" width="100%" colspan="13" style="font-size:12px"><strong><? echo "Party Name : ".$buyer_arr[str_replace("'","",$cbo_party_id)]; ?></strong></td>
			</tr>
		</table>
        <?
		$ope_bal_rec=sql_select("select sum(b.quantity) as rec_opening from sub_material_mst a, sub_material_dtls b where a.company_id=$cbo_company_id and a.id=b.mst_id and b.item_category_id in (2,13) and a.trans_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=2 and b.is_deleted=0 $party_rec_cond $rec_date_ope_cond");
		//echo "select sum(b.batch_qnty) as batch_opening from subcon_ord_mst a, pro_batch_create_dtls b, subcon_ord_dtls c, pro_batch_create_mst d where a.subcon_job=c.job_no_mst and b.po_id=c.id and d.id=b.mst_id and d.entry_form=36 and a.company_id=$cbo_company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $party_batch_cond $batch_date_ope_cond";
		$ope_bal_batch=sql_select("select sum(b.batch_qnty) as batch_opening from subcon_ord_mst a, pro_batch_create_dtls b, subcon_ord_dtls c, pro_batch_create_mst d where a.subcon_job=c.job_no_mst and b.po_id=c.id and d.id=b.mst_id and d.entry_form=36 and a.company_id=$cbo_company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $party_batch_cond $batch_date_ope_cond");
		?>
        
        <table width="1070" border="0" cellpadding="0" cellspacing="0" rules="all" class="">
        	<tr bgcolor="#CCCCCC">
                <td colspan="2" align="right" ><b>Opening Balance: </b>&nbsp;&nbsp;&nbsp;</td>
                <td align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b><? $opening_balance=$ope_bal_rec[0][csf('rec_opening')]-$ope_bal_batch[0][csf('batch_opening')]; echo number_format($opening_balance,2); ?></b></td>
            </tr>
        	<tr>
            	<td valign="top" width="640">
                    <table width="640" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table">
                        <thead>
                            <tr>
                                <th colspan="6">Receive</th>
                            </tr>
                            <tr>
                                <th width="70"><div style="word-wrap:break-word; width:70px">Date</div></th>
                                <th width="160"><div style="word-wrap:break-word; width:160px">Sys. Challan</div></th>
                                <th width="100"><div style="word-wrap:break-word; width:100px">Challan</div></th>
                                <th width="120"><div style="word-wrap:break-word; width:120px">Order No</div></th>
                                <th width="120"><div style="word-wrap:break-word; width:120px">Color</div></th>
                                <th width="70"><div style="word-wrap:break-word; width:70px">Weight</div></th>
                            </tr>
                        </thead>
                    </table>
                </td>
                <td width="10"><div style="word-wrap:break-word; width:10px">&nbsp;</div></td>
                <td valign="top" width="530">
                	<table width="530" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table">
                        <thead>
                            <tr>
                                <th colspan="6">Batch</th>
                            </tr>
                            <tr>
                            	<th width="70"><div style="word-wrap:break-word; width:70px">Date</div></th>
                                <th width="90"><div style="word-wrap:break-word; width:90px">Batch</div></th>
                                <th width="30"><div style="word-wrap:break-word; width:30px">Ext.</div></th>
                                <th width="120"><div style="word-wrap:break-word; width:120px">Order No</div></th>
                                <th width="120"><div style="word-wrap:break-word; width:120px">Color</div></th>
                                <th width="100"><div style="word-wrap:break-word; width:100px">Weight</div></th>
                            </tr>
                        </thead>
                    </table>
                </td>
              </tr>
           </table>
		   
        <div style="max-height:300px; overflow-y:scroll; overflow-x:none; width:1218px" id="scroll_body">
			<table width="1170" border="0" cellspacing="0" cellpadding="0" class="" rules="all" id="table_body">
                <tr>
                    <td valign="top" width="640">
                        <table width="640" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table" >
                        	<?
							
							//$opening_balance_arr=array();
							//echo "select sum(b.quantity) as rec_opening from sub_material_mst a, sub_material_dtls b where a.company_id=$cbo_company_id and a.id=b.mst_id and b.item_category_id=13 and a.trans_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=2 and b.is_deleted=0 $party_rec_cond $rec_date_ope_cond";
							
							
							/*foreach ($ope_bal as $row)
							{
								$opening_balance_arr[$row[csf("party_id")]]=$row[csf("amount")];
							}*/
							
							$sql_rec="select a.subcon_date, a.sys_no, a.chalan_no, b.order_id, b.color_id, sum(b.quantity) as rec_weight from sub_material_mst a, sub_material_dtls b where a.company_id=$cbo_company_id and a.id=b.mst_id and b.item_category_id in (2,13) and a.trans_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=2 and b.is_deleted=0 $party_rec_cond $rec_date_cond group by a.subcon_date, a.sys_no, a.chalan_no, b.order_id, b.color_id order by a.subcon_date";
							$sql_rec_result=sql_select($sql_rec);
							$i=1;
							
							foreach ($sql_rec_result as $row)
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							?>
                                <tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" style="font-size:13px">
                                	<td width="70"><div style="word-wrap:break-word; width:70px"><? echo change_date_format($row[csf('subcon_date')]); ?>&nbsp;</div></td>
                                    <td width="160"><div style="word-wrap:break-word; width:160px"><? echo $row[csf('sys_no')]; ?>&nbsp;</div></td>
                                    <td width="100"><div style="word-wrap:break-word; width:100px"><? echo $row[csf('chalan_no')]; ?>&nbsp;</div></td>
                                    <td width="120"><div style="word-wrap:break-word; width:120px"><? echo $order_arr[$row[csf('order_id')]]; ?>&nbsp;</div></td>
                                    <td width="120"><div style="word-wrap:break-word; width:120px"><? echo $color_arr[$row[csf('color_id')]]; ?>&nbsp;</div></td>
                                    <td width="70" align="right"><div style="word-wrap:break-word; width:70px"><? echo number_format($row[csf('rec_weight')],2,'.',','); ?></div></td>
                                
                                </tr>
                            <?
							$tot_rec_qty+=$row[csf('rec_weight')];
							$i++;
							} ?>
                            
                           
                        </table>
                    </td>
                    <td width="10"><div style="word-wrap:break-word; width:10px">&nbsp;</div></td>
                    <td valign="top" width="530">
                        <table width="530" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table">
                        	<?
							$sql_batch="select d.batch_date, d.batch_no, d.extention_no, b.po_id, d.color_id, sum(b.batch_qnty) as batch_qnty from subcon_ord_mst a, pro_batch_create_dtls b, subcon_ord_dtls c, pro_batch_create_mst d where a.subcon_job=c.job_no_mst and b.po_id=c.id and d.id=b.mst_id and d.entry_form=36 and a.company_id=$cbo_company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $party_batch_cond $batch_date_cond group by d.batch_date, d.batch_no, d.extention_no, b.po_id, d.color_id order by d.batch_date";
							$sql_batch_result=sql_select($sql_batch);
							$k=1;
							foreach ($sql_batch_result as $row)
							{
								if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							?>
                                <tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('trb_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="trb_<? echo $k; ?>" style="font-size:13px">
                                	<td width="70"><div style="word-wrap:break-word; width:70px"><? echo change_date_format($row[csf('batch_date')]); ?>&nbsp;</div></td>
                                    <td width="90"><div style="word-wrap:break-word; width:90px"><? echo $row[csf('batch_no')]; ?>&nbsp;</div></td>
                                    <td width="30"><div style="word-wrap:break-word; width:30px"><? echo $row[csf('extention_no')]; ?>&nbsp;</div></td>
                                    <td width="120"><div style="word-wrap:break-word; width:120px"><? echo $order_arr[$row[csf('po_id')]]; ?>&nbsp;</div></td>
                                    <td width="120"><div style="word-wrap:break-word; width:120px"><? echo $color_arr[$row[csf('color_id')]]; ?>&nbsp;</div></td>
                                    <td width="100" align="right"><div style="word-wrap:break-word; width:100px"><? echo number_format($row[csf('batch_qnty')],2,'.',','); ?></div></td>
                                </tr>
                            <?
							$tot_batch_qty+=$row[csf('batch_qnty')];
							$k++;
							} ?>
                        </table>
                    </td>
                </tr>
            </table>
           </div>
            <div style="width:1180px" id="rpt_div_footer">
            <table width="1180" border="0" cellpadding="0" cellspacing="0" rules="all" class="">
            <tr>
                <td valign="top" width="640">
                    <table width="640" border="1" cellpadding="0" cellspacing="0" rules="all" class="tbl_bottom">
                        <tr>
                            <td width="70"><div style="word-wrap:break-word; width:70px">&nbsp;</div></td>
                            <td width="160"><div style="word-wrap:break-word; width:160px">&nbsp;</div></td>
                            <td width="100"><div style="word-wrap:break-word; width:100px">&nbsp;</div></td>
                            <td width="120"><div style="word-wrap:break-word; width:120px">&nbsp;</div></td>
                            <td width="120"><div style="word-wrap:break-word; width:120px">Total</div></td>
                            <td align="right"  width="75"><div style="word-wrap:break-word; width:70px"><? echo number_format($tot_rec_qty,2,'.',','); ?></div></td>
                        </tr>   
                    </table>
                </td>
                <td width="10"><div style="word-wrap:break-word; width:10px">&nbsp;</div></td>
                <td valign="top" width="530">
                    <table width="530" border="1" cellpadding="0" cellspacing="0" rules="all" class="tbl_bottom">
                        <tr>
                            <td width="70"><div style="word-wrap:break-word; width:70px">&nbsp;</div></td>
                            <td width="90"><div style="word-wrap:break-word; width:90px">&nbsp;</div></td>
                            <td width="30"><div style="word-wrap:break-word; width:30px">&nbsp;</div></td>
                            <td width="120"><div style="word-wrap:break-word; width:120px">&nbsp;</div></td>
                            <td width="120"><div style="word-wrap:break-word; width:120px">Total</div></td>
                            <td align="right"  width="100"><div style="word-wrap:break-word; width:100px"><? echo number_format($tot_batch_qty,2,'.',','); ?></div></td>
                        </tr>  
                    </table>
                </td>
              </tr>
           </table>
           </div>
           <div align="left" style="width:300px; background-color:#00FF66"><strong>Grey Balance : <? $grey_balance=$opening_balance+$tot_rec_qty-$tot_batch_qty; echo number_format($grey_balance,2,'.',','); ?></strong></div>
    </div>
    
	<?
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