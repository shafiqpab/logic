<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_buyer")
{ 
	list($company,$type)=explode("_",$data);
	if($type==1)
	{
		echo create_drop_down( "cbo_customer_name", 100, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $company, "$load_function");
	}
	else
	{
		echo create_drop_down( "cbo_customer_name", 100, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", "", "" );
	}	
	exit();	 
}

if ($action=="load_drop_down_location") {	
	echo create_drop_down( 'cbo_location_name', 150, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", $selected, "" );
	exit();
}

if($action=="generate_report")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	////////////////////////////////////////////////
	$cbo_company_id=str_replace("'","", $cbo_company_id);
	$cbo_location_name=str_replace("'","", $cbo_location_name);
	$cbo_customer_source=str_replace("'","", $cbo_customer_source);
	$cbo_customer_name=str_replace("'","", $cbo_customer_name);
	$cbo_section_id=str_replace("'","", $cbo_section_id);
	$txt_date_from=str_replace("'","", $txt_date_from);
	$txt_date_to=str_replace("'","", $txt_date_to);
	$report_type=str_replace("'","", $report_type);
	$cbo_ItemGroup_id=str_replace("'","", $cbo_ItemGroup_id);
	$txt_style=str_replace("'","", $txt_style);
	
	if($cbo_section_id){$where_con.=" and d.section_id='$cbo_section_id'";} 
	if($cbo_customer_source){$where_con.=" and a.within_group='$cbo_customer_source'";} 
	if($cbo_customer_name){$where_con.=" and a.party_id='$cbo_customer_name'";} 
	if($cbo_location_name){$where_con.=" and a.location_id = $cbo_location_name";}
	if($cbo_ItemGroup_id){$where_con.=" and c.item_group_id = $cbo_ItemGroup_id";}
	if($txt_style){$where_con.=" and c.buyer_style_ref = '$txt_style'";}

	if($cbo_ItemGroup_id){$item_group.=" and c.item_group_id = $cbo_ItemGroup_id";}

	//////////////////////////////////////////////////////////////////
	$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
	$buyerArr = return_library_array("select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0 ","id","buyer_name");
	$trimsGroupArr = return_library_array("select id, item_name from lib_item_group where item_category=4 and status_active=1","id","item_name");
	$color_name_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0","id","color_name");
	$size_name_arr=return_library_array( "select id, size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name");
	$machine_noArr = return_library_array("select id, machine_no from lib_machine_name where status_active=1 and is_deleted=0","id","machine_no");
	$location_arr = return_library_array("select id, location_name from lib_location where status_active=1 and is_deleted=0","id","location_name");
	$lib_item_group_arr = return_library_array("select id, item_name from lib_item_group where status_active=1 and is_deleted=0","id","item_name");

	//////////////////////////////////////////////////////////////////
	
	if($txt_date_from!="" and $txt_date_to!="")
	{	
		$where_con.=" and a.production_date between '$txt_date_from' and '$txt_date_to'";
	}

	$width=1400;
	ob_start();
	if ($report_type==1) {

        //master query From receive
        $order_sql="SELECT a.id,a.receive_date,a.exchange_rate,a.currency_id,a.delivery_date,a.party_id,b.buyer_buyer,b.section,b.sub_section,b.job_no_mst,b.order_no,b.booked_uom,b.order_uom, a.trims_ref,b.buyer_style_ref,b.item_group,a.within_group,b.rate as rate_domestic
		from subcon_ord_mst a, subcon_ord_dtls b
		where a.subcon_job=b.job_no_mst and a.entry_form=255 and a.company_id =$cbo_company_id  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";

		$order_sql_result = sql_select($order_sql);
		$order_array=array();
		$job_order_rec_arr=array();
		$booked_sammary_array=array();
        $rate_array=array();
		foreach($order_sql_result as $row)
		{
			$order_array[$row[csf('id')]][$row[csf('order_no')]][$row[csf('item_group')]]['buyer_buyer']=$row[csf('buyer_buyer')]; 
			$order_array[$row[csf('id')]][$row[csf('order_no')]][$row[csf('item_group')]]['rate_domestic']=$row[csf('rate_domestic')]; 
			$order_array[$row[csf('id')]][$row[csf('order_no')]][$row[csf('item_group')]]['receive_date']=$row[csf('receive_date')]; 
			$job_order_rec_arr[$row[csf("id")]]['rate_domestic']=$row[csf("rate_domestic")];


			$order_array_summary[$row[csf('id')]][$row[csf('item_group')]]['rate_domestic']=$row[csf('rate_domestic')]; 
            $order_rec_arr[] = $row[csf('id')];
		}
		// echo "<pre>";
		// print_r($job_order_rec_arr)."wayasel";


         //production query
	    $trims_order_sql=" SELECT a.trims_production,a.job_id,a.location_id,a.party_id,a.production_date,a.received_id,d.section_id,b.qc_qty,b.job_dtls_id,b.uom as product_uom,b.machine_id,c.conv_factor,c.job_quantity,c.uom ,c.buyer_po_no,c.item_description,c.job_no_mst,c.break_id,d.order_no,c.book_con_dtls_id,c.buyer_style_ref,sum(b.production_qty) as production_qty,c.item_group_id,a.within_group
		from trims_production_mst a,trims_production_dtls b,trims_job_card_dtls c ,trims_job_card_mst d
		where  a.id=b.mst_id and c.id=b.job_dtls_id and c.mst_id=d.id and  a.entry_form=269 and  d.entry_form=257  and a.company_id =$cbo_company_id  and  a.status_active=1 and b.status_active=1 and c.status_active=1 $where_con
		group by a.trims_production,a.job_id,a.location_id,a.party_id,a.production_date,a.received_id,d.section_id,b.job_dtls_id,c.conv_factor,c.job_quantity,c.uom,c.buyer_po_no,c.item_description,c.job_no_mst,b.qc_qty,c.break_id,b.uom,b.machine_id,d.order_no,c.book_con_dtls_id,c.buyer_style_ref,c.item_group_id,a.within_group";

		// echo $trims_order_sql;
			$result = sql_select($trims_order_sql);
			$prod_date_array=array();
			foreach($result as $row)
			{
					$prod_date_array[$row[csf('item_group_id')]][$row[csf('order_no')]][$row[csf('buyer_style_ref')]]['production_qty']+=$row[csf('production_qty')];
					$prod_date_array[$row[csf('item_group_id')]][$row[csf('order_no')]][$row[csf('buyer_style_ref')]]['section_id']=$row[csf('section_id')];
					$prod_date_array[$row[csf('item_group_id')]][$row[csf('order_no')]][$row[csf('buyer_style_ref')]]['item_group_id']=$row[csf('item_group_id')];
					$prod_date_array[$row[csf('item_group_id')]][$row[csf('order_no')]][$row[csf('buyer_style_ref')]]['party_id']=$row[csf('party_id')];
					$prod_date_array[$row[csf('item_group_id')]][$row[csf('order_no')]][$row[csf('buyer_style_ref')]]['buyer_style_ref']=$row[csf('buyer_style_ref')];
					$prod_date_array[$row[csf('item_group_id')]][$row[csf('order_no')]][$row[csf('buyer_style_ref')]]['order_no']=$row[csf('order_no')];
					$prod_date_array[$row[csf('item_group_id')]][$row[csf('order_no')]][$row[csf('buyer_style_ref')]]['within_group']=$row[csf('within_group')];
					$prod_date_array[$row[csf('item_group_id')]][$row[csf('order_no')]][$row[csf('buyer_style_ref')]]['received_id']=$row[csf('received_id')];
			}

        // echo "<pre>";
		// print_r($prod_date_array)."wayasel";die;

        //delevary query
        $trims_del_sql="SELECT a.received_id,b.item_group,b.delevery_qty,a.trims_del from trims_delivery_mst a,trims_delivery_dtls b where  a.id=b.mst_id and a.entry_form=208  and   a.company_id =$cbo_company_id  and  a.status_active=1 and b.status_active=1"; 
		// echo $trims_del_sql; die;
		$del_result = sql_select($trims_del_sql);
	    $date_delevery_array=array();
	    foreach($del_result as $row)
	    {
	   	 	$date_delevery_array[$row[csf('received_id')]][$row[csf('item_group')]]['delevery_qty']+=$row[csf('delevery_qty')];
	   	 	$date_delevery_array[$row[csf('received_id')]][$row[csf('item_group')]]['trims_del']=$row[csf('trims_del')];
        }

		?>	
		<div align="center" style="height:auto; width:<? echo $width+20;?>px; margin:0 auto; padding:0;">
	    	<table width="<? echo $width;?>" cellpadding="0" cellspacing="0" id="caption" align="center">
					<thead class="form_caption" >
						<tr>
							<td colspan="11" align="center" style="font-size:20px;"><? echo $companyArr[$cbo_company_id]; ?></td>
						</tr>
						<tr>
							<td colspan="11" align="center" style="font-size:14px; font-weight:bold" ><? echo $report_title; ?></td>
						</tr>
						<tr>
							<td colspan="11" align="center" style="font-size:14px; font-weight:bold">
								<? echo " From : ".change_date_format($txt_date_from) ." To : ". change_date_format($txt_date_to) ;?>
							</td>
						</tr>
					</thead>
				</table>
	            <table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="<? echo $width;?>" rules="all" id="rpt_table_header" align="left">
					<thead>
	                    <th width="35">SL</th>
	                    <th width="100">Party Name</th>
	                    <th width="100">Buyer</th>
	                    <th width="100">Style</th>
	                    <th width="100">W/O No</th>

	                    <th width="100">W/O Rcv Date</th>
	                    <th width="100">Item Name</th>
	                    <th width="100">Challan No</th>
	                    <th width="100">Prod. Qty</th>
	                    <th width="100">Prod. Value</th>

	                	<th width="100">Delv. Qty</th>
	                    <th width="100">Delv. Value</th>	                   
					</thead>
				</table>
	        <div style="width:<? echo $width+18;?>px; max-height:250px; float:left; overflow-y:scroll;" id="scroll_body">
	        	<table border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body_id" width="<? echo $width;?>" rules="all" align="left">
	            <? 
					$i=1;
					$trims_del_qty=0;$trims_prod_qty=$delevery_value=0;
					$item_summery_arr=array();

						    foreach($prod_date_array as $item_group_id=>$order_data)
							{
								foreach($order_data as $order_no_id=>$style_data)
								{
									foreach($style_data as $job_no_mst_id=>$row)
									{
										
										$delevery_value=$date_delevery_array[$row['received_id']][$row['item_group_id']]['delevery_qty']*$order_array[$row['received_id']][$row['order_no']][$row['item_group_id']]['rate_domestic'];
										$item_summery_arr[$row['item_group_id']]['production_qty']+=$row['production_qty']*$order_array[$row['received_id']][$row['order_no']][$row['item_group_id']]['rate_domestic'];
										$item_summery_arr[$row['item_group_id']]['delevery_value']+=$delevery_value;
										$item_summery_arr[$row['item_group_id']]['delevery_qty']+=$date_delevery_array[$row['received_id']][$row['item_group_id']]['delevery_qty'];
										//echo $order_array[$row['received_id']][$row['order_no']][$row['item_group_id']]['rate_domestic'];
										?>
						                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
						                	<td width="35"  align="center">
												<? echo $i;?>
											</td>
						                    <td width="100" style="word-break: break-all;" align="left">
												<? echo $party = ($row['within_group'] == 1) ? $companyArr[$row['party_id']] : $buyerArr[$row['party_id']]; ?>
											</td>
                                            <td width="100" style="word-break: break-all;" align="left">
												<? echo $buyerArr[$order_array[$row['received_id']][$row['order_no']][$row['item_group_id']]['buyer_buyer']];?>
											</td>
						                    <td width="100" style="word-break: break-all;" align="center">
												<? echo $row['buyer_style_ref'];?>
											</td>
						                    <td width="100" style="word-break: break-all;" align="center">
												<? echo  $row['order_no']; ;?>
											</td>

						                    <td width="100" style="word-break: break-all;" align="center">
												<? echo change_date_format( $order_array[$row['received_id']][$row['order_no']][$row['item_group_id']]['receive_date']);?>
											</td>
						                    <td width="100" style="word-break: break-all;" align="left">
												<? echo  $lib_item_group_arr[$row['item_group_id']];  ?>
											</td>
						                    <td width="100" style="word-break: break-all;" align="center">
												<? echo $date_delevery_array[$row['received_id']][$row['item_group_id']]['trims_del']; ?>
											</td>
						                    <td width="100" style="word-break: break-all;" align="right">
												<? echo number_format($row['production_qty'],4);?>
											</td>
						                    <td width="100" style="word-break: break-all;" align="right">
												<?php echo number_format($row['production_qty']*$order_array[$row['received_id']][$row['order_no']][$row['item_group_id']]['rate_domestic'],4)?>
											</td>

						                    <td width="100" style="word-break: break-all;" align="right">
												<?php echo number_format($date_delevery_array[$row['received_id']][$row['item_group_id']]['delevery_qty'],4); ?>
											</td>
						                    <td width="100" style="word-break: break-all;" align="right">
												<? echo number_format($delevery_value,4); ?>
											</td>						                   
						                </tr>
						                <? 
											$i++;
											$prod_total+=$row['production_qty'];
											$del_total+=$date_delevery_array[$row['received_id']][$row['item_group_id']]['delevery_qty'];
                                            $trims_prod_qty+=$row['production_qty']*$order_array[$row['received_id']][$row['order_no']][$row['item_group_id']]['rate_domestic'];
                                            $trims_del_qty+=$delevery_value;                                       
									}
								}
							}
							// echo '<pre>';print_r($item_summery_arr);
					?>
	       		 </table>
	        </div>
	        <table width="<? echo $width;?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body_footer" align="left">
				<tfoot>
	                <th width="35"></th>
	                <th width="100"></th>
	                <th width="100"></th>
	                <th width="100"></th>
	                <th width="100"></th>
	                <th width="100"></th>
	                <th width="100"></th>
	                <th width="100">Total</th>
	                <th width="100" id="prod_total"><?=number_format($prod_total,4)?></th>
	                <th width="100" id="value_prod_bal_qty"><?= number_format($trims_prod_qty,4)?></th>
	                <th width="100" id="del_total"><?=number_format($del_total,4)?></th>
	                <th width="100"  id="value_trims_del_qty"><?=number_format($trims_del_qty,4)?></th>
				</tfoot>
			</table>
	    </div>
	    <div align="left" style="height:auto; width:500px; margin:0 auto; padding:0;">
	    	<table width="500" cellpadding="0" cellspacing="0" id="caption" align="left">
				<thead class="form_caption" >
					<tr>
						<td colspan="6" align="center" style="font-size:14px; font-weight:bold" ><? echo "Summary"; ?></td>
					</tr>
				</thead>
			</table>
	        <table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="800" rules="all" id="rpt_table_header" align="left">
				<thead>
	                <th width="35">SL</th>
	                <th width="150">Trims Group</th>

	                <th width="150">Prod. Qnty</th>
	                <th width="150">Prod. Value</th>

	                <th width="150">Delv. Qnty</th>
	                <th width="150">Delv. Value</th>
				</thead>
			</table>
			<table border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body_id" width="800" rules="all" align="left">
			<? 

            $ord_dtls_ids = implode(',', $order_rec_arr);

			 $sammary_sql=" SELECT a.received_id,c.item_group_id,b.production_qty ,d.order_no from trims_production_mst a,trims_production_dtls b,trims_job_card_dtls c ,trims_job_card_mst d where  a.id=b.mst_id and c.id=b.job_dtls_id and c.mst_id=d.id and  a.entry_form=269 and  d.entry_form=257  and a.company_id =$cbo_company_id  and  a.status_active=1 and b.status_active=1 and c.status_active=1 $where_con";
			//  echo $sammary_sql;
			$sammary_result = sql_select($sammary_sql);
	        $sammary_array=array();
	        foreach($sammary_result as $row)
	        {
	       	 	$sammary_array[$row[csf('item_group_id')]]['received_id']=$row[csf('received_id')];
	       	 	$sammary_array[$row[csf('item_group_id')]]['item_group_id']=$row[csf('item_group_id')];
				$sammary_array[$row[csf('item_group_id')]]['production_qty']+=$row[csf('production_qty')];
				$sammary_array[$row[csf('item_group_id')]]['order_no']=$row[csf('order_no')];
	 		}
			// echo "<pre>"; print_r($sammary_array); die;
			 $t=1;$total_product_qty=$total_product_val=0;$total_del_qty=$total_del_val=0;
			if(count($sammary_array)>0){
					foreach($sammary_array as $rec_data=>$row)
					{                            
							$sub_total_product_qty=$sub_total_product_val=0;$sub_total_del_qty=$sub_total_del_val=0;
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $t; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $t; ?>">
								<td width="35"  align="center">
									<? echo $t;?>
								</td>
								<td width="150" align="center">
									<? echo $lib_item_group_arr[$row['item_group_id']];?>
								</td>

								<td width="150" align="right">
									<? echo number_format( $row['production_qty'],4);?>
								</td>
								<td width="150" align="right">
									<? echo number_format( $item_summery_arr[$row['item_group_id']]['production_qty'],4);?>
								</td>

								<td width="150" align="right">
									<? echo number_format($item_summery_arr[$row['item_group_id']]['delevery_qty'],4);?>
								</td>                    
								<td width="150" align="right">
									<? echo number_format($item_summery_arr[$row['item_group_id']]['delevery_value'],4);?>
								</td>                    
							</tr>
							<? 
							$t++;
							$sub_total_product_val+= $row['production_qty'];
							$sub_total_product_qty+= $item_summery_arr[$row['item_group_id']]['production_qty'];
							$sub_total_del_val+= $item_summery_arr[$row['item_group_id']]['delevery_qty'];
							$sub_total_del_qty+= $item_summery_arr[$row['item_group_id']]['delevery_value'];
							$total_product_qty+= $item_summery_arr[$row['item_group_id']]['production_qty'];
							$total_product_val+= $row['production_qty'];
							$total_del_qty+= $item_summery_arr[$row['item_group_id']]['delevery_value'];
							$total_del_val+= $item_summery_arr[$row['item_group_id']]['delevery_qty'];
						
							?>
							<tr style="background-color:#CCC">
								<td colspan="2" align="right"><b> Sub Total</b></td>
								<td align="right"><b><? echo number_format($sub_total_product_val);?></b></td>
								<td align="right"><b><? echo number_format($sub_total_product_qty,4);?></b></td>
								<td align="right"><b><? echo number_format($sub_total_del_val);?></b></td>
								<td align="right"><b><? echo number_format($sub_total_del_qty,4);?></b></td>
							</tr>
							<?          
						}           
							?>
							<tr style="background-color:#CCC">
								<td colspan="2" align="right"><b>Grand Total</b></td>
								<td align="right"><b><? echo number_format($total_product_val);?></b></td>
								<td align="right"><b><? echo number_format($total_product_qty,4);?></b></td>
								<td align="right"><b><? echo number_format($total_del_val);?></b></td>
								<td align="right"><b><? echo number_format($total_del_qty,4);?></b></td>
							</tr>
						</table>
					</div>
				<?
				}
	 }
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
    echo "$html**$filename**$report_type";
    exit();
	
}

?>