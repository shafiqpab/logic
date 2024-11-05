<? 
include('../../../includes/common.php');
session_start();
extract($_REQUEST);
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$date=date('Y-m-d');

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
	$user_name_arr=return_library_array( "select id, user_name from user_passwd", "id", "user_name");	
	
	if(str_replace("'","",$cbo_party_id)==0) $party_batch_cond=""; else  $party_batch_cond=" and a.party_id=$cbo_party_id";
	
	if($db_type==0)
	{
		if( $date_from==0 && $date_to==0 ) $batch_date_cond=""; else $batch_date_cond= " and d.batch_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'";
	}
	else if ($db_type==2)
	{
		if( $date_from=='' && $date_to=='' ) $batch_date_cond=""; else $batch_date_cond= " and d.batch_date between '".change_date_format($date_from,'','',1)."' and '".change_date_format($date_to,'','',1)."'";
	}
	
	ob_start();
	?>
	<div>
	 <fieldset style="width:1710px;">
		<table cellpadding="0" cellspacing="0" width="1470">
			<tr  class="form_caption" style="border:none;">
			   <td align="center" width="100%" colspan="17" style="font-size:20px"><strong><? echo $report_title; ?></strong></td>
			</tr>
			<tr class="form_caption" style="border:none;">
			   <td align="center" width="100%" colspan="17" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
			</tr>
			<tr class="form_caption" style="border:none;">
				<td align="center" width="100%" colspan="17" style="font-size:12px">
					<? if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="") echo "From ".change_date_format(str_replace("'","",$txt_date_from),'dd-mm-yyyy')." To ".change_date_format(str_replace("'","",$txt_date_to),'dd-mm-yyyy')."" ;?>
				</td>
			</tr>
		</table>
		<table width="1810" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table">
			<thead>
                <tr>
                    <th width="40">SL</th>
                    <th width="150">Party</th>
                    <th width="70">Batch Date</th>
                    <th width="70">Batch CreateTime</th>
                    <th width="100">Batch No</th>
					<th width="100">Insert By</th>
                    <th width="50">Batch Ext.</th>
                    <th width="70">PO NO</th>
                    <th width="100">Batch Qty (KG)</th>
                    <th width="100">Batch Color</th> 
                    <th width="100">Fabrication </th> 
                    <th width="70">Recipe Date</th>
                    <th width="70">Dyes Chem Issue Date</th> 
                    <th width="100">Dyes Chem Cost</th> 
                    <th width="70">Dyeing Prod. Date</th> 
                    <th width="70">Finishing Date</th>
                    <th width="70">Delivery Date</th>
                    <th width="70">Bill Date</th>                       
                    <th width="100">Bill Qty</th>
                    <th width="100">Bill Amount</th>
                    <th>Remarks</th>
                </tr>
			</thead>
		</table>
        <div style="max-height:300px; overflow-y:scroll; width:1810px" id="scroll_body">
			<table width="1792" border="1" class="rpt_table" rules="all" id="table_body">
            <?
				$recipe_array=array();
				$recipe_sql="select batch_id, max(recipe_date) as recipe_date from pro_recipe_entry_mst where company_id=$cbo_company_id and status_active=1 and is_deleted=0 group by batch_id";
				$recipe_sql_result=sql_select($recipe_sql);
				foreach ($recipe_sql_result as $row)
				{
					$recipe_array[$row[csf('batch_id')]]=$row[csf('recipe_date')];
				}
				
				$dyeCham_array=array();
				$dyeCham_sql="select b.batch_id, max(a.transaction_date) as dye_iss_date, SUM(a.cons_amount) as cons_amount from inv_transaction a, dyes_chem_issue_dtls b where a.id=b.trans_id AND b.item_category in (5,6,7) AND b.is_deleted=0 AND b.status_active=1 group by b.batch_id";
				$dyeCham_sql_result=sql_select($dyeCham_sql);
				foreach ($dyeCham_sql_result as $row)
				{
					$dyeCham_array[$row[csf('batch_id')]]['date']=$row[csf('dye_iss_date')];
					$dyeCham_array[$row[csf('batch_id')]]['qty']=$row[csf('cons_amount')];
				}
				
				$dyeProd_array=array();
				$dyeProd_sql="select batch_id, max(production_date) as dyeing_date from pro_fab_subprocess where company_id=$cbo_company_id and status_active=1 and is_deleted=0 and load_unload_id=2 group by batch_id";
				$dyeProd_sql_result=sql_select($dyeProd_sql);
				foreach($dyeProd_sql_result as $row)
				{
					$dyeProd_array[$row[csf('batch_id')]]=$row[csf('dyeing_date')];
					//$dyeProd_array[$row[csf('batch_id')]]['qty']=$row[csf('cons_amount')];
				}
				
				$finProd_array=array();
				$finProd_sql="select b.batch_id, max(a.product_date) as finishing_date from  subcon_production_mst a, subcon_production_dtls b where a.entry_form=292 and a.id=b.mst_id and a.company_id=$cbo_company_id and a.status_active=1 and a.is_deleted=0 and a.product_type=4 group by b.batch_id";
				$finProd_sql_result=sql_select($finProd_sql);
				foreach($finProd_sql_result as $row)
				{
					$finProd_array[$row[csf('batch_id')]]=$row[csf('finishing_date')];
				}
				
				$delivery_array=array();
				$delivery_sql="select b.batch_id, max(a.delivery_date) as delivery_date from subcon_delivery_mst a, subcon_delivery_dtls b where a.id=b.mst_id and a.company_id=$cbo_company_id and a.status_active=1 and a.is_deleted=0 and a.process_id=4 group by b.batch_id";
				$delivery_sql_result=sql_select($delivery_sql);
				foreach($delivery_sql_result as $row)
				{
					$delivery_array[$row[csf('batch_id')]]=$row[csf('delivery_date')];
				}
				
				$batch_deliveryId_arr=return_library_array( "select id, batch_id from subcon_delivery_dtls", "id", "batch_id");
				$bill_array=array();
				$bill_sql="select b.delivery_id, max(a.bill_date) as bill_date, sum(b.delivery_qty) as bill_qty, sum(b.amount) as bill_amount from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.id=b.mst_id and a.company_id=$cbo_company_id and a.status_active=1 and a.is_deleted=0 and a.process_id=4 and a.party_source=2 group by b.delivery_id";
				$bill_sql_result=sql_select($bill_sql);
				foreach($bill_sql_result as $row)
				{
					$batch_all_id="";
					$delivery_id_ex=array_unique(explode("_",$row[csf('delivery_id')]));
					foreach($delivery_id_ex as $id)
					{
						if($batch_all_id=="") $batch_all_id=$batch_deliveryId_arr[$id]; else $batch_all_id.=','.$batch_deliveryId_arr[$id];
					}
					$batch_id_ex=array_unique(explode(",",$batch_all_id));
					foreach($batch_id_ex as $batch_id)
					{
						$bill_array[$batch_id]['bill_date']=$row[csf('bill_date')];
						$bill_array[$batch_id]['bill_qty']+=$row[csf('bill_qty')];
						$bill_array[$batch_id]['bill_amount']+=$row[csf('bill_amount')];
					}
				}
				//var_dump($bill_array);
			
				$sql_batch="select a.party_id,b.item_description,c.order_no, d.id, d.batch_no, d.extention_no, d.batch_date,d.insert_date, d.color_id, (b.batch_qnty) as batch_qnty,d.inserted_by  from subcon_ord_mst a, pro_batch_create_dtls b, subcon_ord_dtls c, pro_batch_create_mst d where a.subcon_job=c.job_no_mst and b.po_id=c.id and d.id=b.mst_id and d.entry_form=36 and a.company_id=$cbo_company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0  $party_batch_cond $batch_date_cond order by d.id";
				//group by a.party_id, d.id, d.batch_no,d.insert_date, d.extention_no, d.batch_date, d.color_id order by d.batch_date
			
				$sql_batch_result=sql_select($sql_batch);
				foreach ($sql_batch_result as $row)
				{
					$insert_date=explode(" ",$row[csf('insert_date')]);
					//echo $row[csf('insert_date')];
					$subcon_batch_arr[$row[csf('id')]]['insert_date']=$insert_date[1];
					$subcon_batch_arr[$row[csf('id')]]['id']=$row[csf('id')];
					$subcon_batch_arr[$row[csf('id')]]['batch_qnty']+=$row[csf('batch_qnty')];
					$subcon_batch_arr[$row[csf('id')]]['party_id']=$row[csf('party_id')];
					$subcon_batch_arr[$row[csf('id')]]['batch_date']=$row[csf('batch_date')];
					$subcon_batch_arr[$row[csf('id')]]['desc'].=$row[csf('item_description')].',';
					$subcon_batch_arr[$row[csf('id')]]['order_no'].=$row[csf('order_no')].',';
					$subcon_batch_arr[$row[csf('id')]]['batch_no']=$row[csf('batch_no')];
					$subcon_batch_arr[$row[csf('id')]]['inserted_by']=$row[csf('inserted_by')];
					$subcon_batch_arr[$row[csf('id')]]['extention_no']=$row[csf('extention_no')];
					$subcon_batch_arr[$row[csf('id')]]['color_id']=$row[csf('color_id')];
					$subcon_batch_arr[$row[csf('id')]]['extention_no']=$row[csf('extention_no')];
					$subcon_batch_arr[$row[csf('id')]]['extention_no']=$row[csf('extention_no')];
					$subcon_batch_arr[$row[csf('id')]]['extention_no']=$row[csf('extention_no')];
				}
				$i=1;
				foreach ($subcon_batch_arr as $batch_id=>$row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$dyescham_cost=0;
					$dyescham_cost=$dyeCham_array[$batch_id]['qty']/$row[('batch_qnty')];
				//	echo $row[('insert_date')].'DS';
					$insert_time=$row[('insert_date')];
					//print_r($insert_time);
					$item_desc=rtrim($row[('desc')],',');
					$item_descs=implode(",",array_unique(explode(" ",$item_desc)));
					$order_no=rtrim($row[('order_no')],',');
					$order_nos=implode(",",array_unique(explode(" ",$order_no)));
					?>
                    <tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                         <td width="40"><? echo $i; ?></td>
                         <td width="150"><p><? echo $buyer_arr[$row[('party_id')]]; ?></p></td>
                         <td width="70"><p><? echo change_date_format($row[('batch_date')]); ?></p></td>
                         <td width="70"><p><?  echo $insert_time; ?></p></td>
                         <td width="100"><p><? echo $row[('batch_no')]; ?></p></td>
						 <td width="100"><p><? echo $user_name_arr[$row[('inserted_by')]]; ?></p></td>
                         <td width="50"><p><? echo $row[('extention_no')]; ?></p></td>
                         <td width="70"><p><? echo $order_nos; ?></p></td>
                         <td width="100" align="right"><p><? echo number_format($row[('batch_qnty')],2,'.',','); ?></p></td>
                         <td width="100"><p><? echo $color_arr[$row[('color_id')]]; ?></p></td>
                         <td width="100"><p><? echo $item_descs; ?></p></td>
                         <td width="70"><p><? echo change_date_format($recipe_array[$row[('id')]]); ?></p></td>
                         <td width="70"><p><? echo change_date_format($dyeCham_array[$row[('id')]]['date']); ?></p></td>
                         <td width="100" align="right"><p><? echo number_format($dyescham_cost,2,'.',','); ?></p></td>
                         <td width="70"><p><? echo change_date_format($dyeProd_array[$row[('id')]]); ?></p></td>
                         <td width="70"><p><? echo change_date_format($finProd_array[$row[('id')]]); ?></p></td>
                         <td width="70"><p><? echo change_date_format($delivery_array[$row[('id')]]); ?></p></td>
                         <td width="70"><p><? echo change_date_format($bill_array[$row[('id')]]['bill_date']); ?></p></td>
                         <td width="100" align="right"><? echo number_format($bill_array[$row[('id')]]['bill_qty'],2,'.',','); ?></td>
                         <td width="100" align="right"><? echo number_format($bill_array[$row[('id')]]['bill_amount'],2,'.',','); ?></td>
                         <?
						 	$remarks="";
						 	if(number_format($row[('batch_qnty')],2,'.',',') > number_format($bill_array[$row[('id')]]['bill_qty'],2,'.',','))
							{
								$remarks="Pending";
								$remarks_color="#FF0000";
							}
							else
							{
								$remarks="Ok";
								$remarks_color=$bgcolor;
							}
						 ?>
                         <td bgcolor="<? echo $remarks_color;  ?>"><? echo $remarks; ?></td>
                    </tr>
                    <?	
					$tot_batchQty+=$row[('batch_qnty')];
					$tot_dyeCham_cost+=$dyescham_cost;
					$tot_billQty+=$bill_array[$row[('id')]]['bill_qty'];
					$tot_billAmount+=$bill_array[$row[('id')]]['bill_amount'];
                    $i++;
				}
			?>
            </table>
            </div>
            <table width="1810" border="1" class="rpt_table" rules="all">
            	<tr class="tbl_bottom">
                    <td width="40">&nbsp;</td>
                    <td width="150">&nbsp;</td>
                    <td width="70">&nbsp;</td>
                    <td width="70">&nbsp;</td>
                    <td width="100">&nbsp;</td>
					<td width="100">&nbsp;</td>
                    <td width="50"></td>
                    <td width="70"><strong>Total</strong></td> 
                    <td width="100" id="tot_batch_qty" align="right"><? echo number_format($tot_batchQty,2,'.',','); ?></td>
                    <td width="100">&nbsp;</td> 
                    <td width="100">&nbsp;</td> 
                    <td width="70">&nbsp;</td>
                    <td width="70">&nbsp;</td> 
                    <td width="100" id="tot_dye_cham_cost" align="right"><? echo number_format($tot_dyeCham_cost,2,'.',','); ?></td> 
                    <td width="70">&nbsp;</td> 
                    <td width="70">&nbsp;</td>
                    <td width="70">&nbsp;</td>
                    <td width="70">&nbsp;</td>                       
                    <td width="100" id="tot_bill_qty" align="right"><? echo number_format($tot_billQty,2,'.',','); ?></td>
                    <td width="100" id="tot_bill_amount" align="right"><? echo number_format($tot_billAmount,2,'.',','); ?></td>
                    <td></td>
                </tr>
            </table>
         </fieldset>
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