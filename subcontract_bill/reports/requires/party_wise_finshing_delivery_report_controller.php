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
	$user_arr=return_library_array( "select id, user_full_name from user_passwd", "id", "user_full_name");
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
	$batch_arr=return_library_array( "select id, batch_no from pro_batch_create_mst where entry_form=36", "id", "batch_no");
	$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$order_arr=return_library_array( "select id, order_no from subcon_ord_dtls", "id", "order_no");
	
	if(str_replace("'","",$cbo_party_id)==0) $party_del_cond=""; else $party_del_cond=" and a.party_id=$cbo_party_id";
	
	if($db_type==0)
	{
		if( $date_from==0 && $date_to==0 ) $del_date_cond=""; else $del_date_cond=" and a.delivery_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'";
	}
	else if ($db_type==2)
	{
		if( $date_from=='' && $date_to=='' ) $del_date_cond=""; else $del_date_cond=" and a.delivery_date between '".change_date_format($date_from,'','',1)."' and '".change_date_format($date_to,'','',1)."'";
	}
	
	ob_start();
	?>
	<div align="center">
		<table cellpadding="0" cellspacing="0" width="967">
        	<tr class="form_caption" style="border:none;">
			   <td align="center" width="100%" colspan="10" style="font-size:20px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
			</tr>
			<tr class="form_caption" style="border:none;">
			   <td align="center" width="100%" colspan="10" style="font-size:16px"><strong><? echo $report_title; ?></strong></td>
			</tr>
			<tr class="form_caption" style="border:none;">
				<td align="center" width="100%" colspan="10" style="font-size:14px">
					<? if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="") echo "Finishing Delivery: ".change_date_format(str_replace("'","",$txt_date_from),'dd-mm-yyyy')." To ".change_date_format(str_replace("'","",$txt_date_to),'dd-mm-yyyy')."" ;?>
				</td>
			</tr>
		</table>
        <table width="1070" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table">
        	<thead>
            	<th width="30">SL</th>
                <th width="110">DL. Invc. ID</th>
                <th width="70">DL. Date</th>
                <th width="70">D. Challan No</th>
                <th width="80">Batch No</th>
                <th width="100">Order No</th>
                <th width="100">Color</th>
                <th width="80">Delivery Qty ( KG)</th>
                <th width="50">%</th>
				<th width="100">Grey qnty</th>
                <th width="100" style="word-break: break-all;">User</th>
                <th>Remarks</th>
            </thead>
        </table>
        <div style="max-height:300px; overflow-y:scroll; width:1067px" id="scroll_body" align="left">
        <table width="1050" border="1" cellspacing="0" cellpadding="0" class="rpt_table" rules="all" id="table_body">
			<? $sql_del="Select a.id, a.delivery_no, a.challan_no, a.delivery_date, a.party_id, b.order_id, b.batch_id, b.color_id, b.remarks, sum(b.gray_qty) as gray_qty, sum(b.delivery_qty) as delivery_qty,a.inserted_by,a.updated_by from subcon_delivery_mst a, subcon_delivery_dtls b where a.id=b.mst_id and a.process_id=4 and a.company_id=$cbo_company_id and a.status_active=1 and a.is_deleted=0 $party_del_cond $del_date_cond group by a.id, a.delivery_no, a.challan_no, a.delivery_date, a.party_id, b.order_id, b.batch_id, b.color_id, b.remarks,a.inserted_by,a.updated_by order by a.party_id,a.challan_no ASC";
			//echo $sql_del;
			$sql_del_result=sql_select($sql_del);
			$i=1;  $party_array=array();  $k=1; $tot_del_qty=0;
			foreach($sql_del_result as $rowSum)
			{
				$tot_del_qty+=$rowSum[csf("delivery_qty")];
			}
			
			foreach($sql_del_result as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$user_name='';
				if(empty($row[csf('updated_by')]))
				{
					//$user_name='Inserted by: '.$user_arr[$row[csf('inserted_by')]];
					$user_name=$user_arr[$row[csf('inserted_by')]];
				}
				else{
					//$user_name='Inserted by: '.$user_arr[$row[csf('inserted_by')]].",Updated by: ".$user_arr[$row[csf('updated_by')]];
					$user_name=$user_arr[$row[csf('updated_by')]];
				}
				

				if (!in_array($row[csf("party_id")],$party_array) )
				{
					if($k!=1)
					{
					?>
						<tr class="tbl_bottom">
							<td colspan="7" align="right"><b>Party Total:</b></td>
							<td align="right"><b><? echo number_format($party_tot_del_qty,2); ?></b></td>
							<td align="center"><b><? echo number_format($party_del_per,2).'%'; ?></b></td>
							<td align="right"><b><? echo number_format($party_tot_grey_qty,2); ?></b></td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
						</tr>
					<?
						unset($party_tot_del_qty);
						unset($party_del_per);
					}
					?>
						<tr bgcolor="#dddddd">
							<td colspan="12" align="left" ><b>Party Name: <? echo $buyer_arr[$row[csf("party_id")]]; ?></b></td>
							
						</tr>
					<?
					$party_array[]=$row[csf('party_id')];  
					$k++;
				}
			?>
				<tr bgcolor="<? echo $bgcolor;  ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" style="font-size:13px">
                	<td width="30"><? echo $i; ?></td>
					<td width="110"><div style="word-wrap:break-word; width:110px"><? echo $row[csf('delivery_no')]; ?></div></td>
					<td width="70">&nbsp;<? echo change_date_format($row[csf('delivery_date')]); ?></td>
					<td width="70" align="center"><? echo $row[csf('challan_no')]; ?></td>
					<td width="80"><div style="word-wrap:break-word; width:80px"><? echo $batch_arr[$row[csf('batch_id')]]; ?>&nbsp;</div></td>
                    <td width="100"><div style="word-wrap:break-word; width:100px"><? echo $order_arr[$row[csf('order_id')]]; ?>&nbsp;</div></td>
					<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $color_arr[$row[csf('color_id')]]; ?>&nbsp;</div></td>
                    <td width="80" align="right"><? echo number_format($row[csf('delivery_qty')],2,'.',','); ?></td>
                    <td width="50" align="center"><? //echo $tot_del_qty; ?>&nbsp;</td>					
                    <td width="100" align="right" style="word-break: break-all;"><?echo number_format($row[csf('gray_qty')],2,'.',',');  ?>&nbsp;</td>
					<td width="100" align="center" style="word-break: break-all;"><?=$user_name ?>&nbsp;</td>
					<td><p>&nbsp;<? echo $row[csf('remarks')]; ?></p></td>
				</tr>
				<?
                $party_tot_del_qty+=$row[csf('delivery_qty')];
				$party_tot_grey_qty+=$row[csf('gray_qty')];
				$party_del_per=($party_tot_del_qty/$tot_del_qty)*100;
				$tot_grey_qty+=$row[csf('gray_qty')];
                $i++;
			} ?>
            <tr class="tbl_bottom">
                <td colspan="7" align="right"><b>Party Total:</b></td>
                <td align="right"><b><? echo number_format($party_tot_del_qty,2); ?></b></td>
                <td align="center"><b><? echo number_format($party_del_per,2).'%'; ?></b></td>
                <td  align="right"><? echo number_format($party_tot_grey_qty,2);$party_tot_grey_qty=0; ?></td>
				<td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
        </table> 
        </div>
        <table width="1067" border="1" cellpadding="0" cellspacing="0" rules="all" class="tbl_bottom">
        	<tr>
            	<td width="30">&nbsp;</td>
                <td width="110">&nbsp;</td>
                <td width="70">&nbsp;</td>
                <td width="70">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="100">Grand Total:</td>
                <td width="80" align="right"><? echo number_format($tot_del_qty,2,'.',','); ?></td>
                <td width="50"><? echo '100%'; ?></td>
                <td width="100"><? echo number_format($tot_grey_qty,2,'.',','); ?></td>
				<td width="100"></td>
                <td>&nbsp;</td>
            </tr>
        </table>          
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