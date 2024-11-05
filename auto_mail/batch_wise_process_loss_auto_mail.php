<?
header('Content-type:text/html; charset=utf-8');
session_start();
require_once('../includes/common.php');
$user_id=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


$company_library=return_library_array( "select id, company_name from lib_company where id in(1,3)", "id", "company_name"  );
$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );

if($db_type==0)
{
    $current_date = date("Y-m-d H:i:s",strtotime(add_time(date("H:i:s",time()),0)));
    $previous_date = date('Y-m-d H:i:s', strtotime('-1 day', strtotime($current_date))); 
}
else
{
    $current_date = change_date_format(date("Y-m-d H:i:s",strtotime(add_time(date("H:i:s",time()),0))),'','',1);
	if($_REQUEST['view_date']){
		$current_date = change_date_format(date("Y-m-d H:i:s",strtotime($_REQUEST['view_date'])),'','',1);
	}
	$previous_date = change_date_format(date('Y-m-d H:i:s', strtotime('-1 day', strtotime($current_date))),'','',1);
}
 
//--------------------------------------------------------------------------------------------------------------------
$action="report_generate";
foreach($company_library as $company_id=>$company_name)
{
	
	$delivery_cond=" and a.DELEVERY_DATE between '$previous_date' and '$current_date'";
	
	if($db_type==0)
	{
		$year_field="SUBSTRING_INDEX(a.insert_date, '-', 1) as year"; 
	}
	else if($db_type==2)
	{
		$year_field="to_char(a.insert_date,'YYYY') as year";
	}



	$knit_company_cond="  and a.company_id=".$company_id." ";

	// ========================================Start===========================================
	
	$sql_dtls=sql_select("SELECT b.batch_id,sum(b.receive_qnty) as finish_qty	
	from inv_receive_master a,pro_finish_fabric_rcv_dtls b 
	where a.id=b.mst_id  and a.entry_form=7  and a.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $knit_company_cond 
	group by b.batch_id");
	$finish_data_arr=array();
	foreach($sql_dtls as $row_fin)// for Finish Production
	{
		$finish_data_arr[$row_fin[csf('batch_id')]]['finish_qty']=$row_fin[csf('finish_qty')];
	}
	
	
	
	$sql="select b.program_no as batch_id,sum(b.current_delivery) as delivery_qty from pro_grey_prod_delivery_mst a,pro_grey_prod_delivery_dtls b where  a.id=b.mst_id and a.entry_form=54 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $delivery_cond $knit_company_cond  group by b.program_no";
	//echo $sql;
	$sql_dtls=sql_select($sql);
	$batch_id_arr=array();
	$delivery_data_arr=array();
	foreach($sql_dtls as $row_del)// for Loading time
	{
		$delivery_data_arr[$row_del[csf('batch_id')]]['delivery']=$row_del[csf('delivery_qty')];
		$batch_id_arr[$row_del[csf('batch_id')]]=$row_del[csf('batch_id')];
	}

	
	// ========================================End============================================
	
	ob_start();
	$po_id_cond="";
	if($job_no!="" || str_replace("'","",$cbo_buyer_name)!=0 || $cbo_year!=0  || $start_date!="")
	{
		$po_id_cond=" $self_all_po_id";
	}

	$self_po_id_cond="";
	if($job_no!="" || str_replace("'","",$cbo_buyer_name)!=0 || $cbo_year!=0)
	{
		$self_po_id_cond=" $self_all_po_id";
	}

	$subc_po_id_cond="";
	if($job_no!="" || str_replace("'","",$cbo_buyer_name)!=0 || $cbo_year!=0 )
	{
		$subc_po_id_cond=" $subc_all_po_id";
	}

	
 	
						
	$sql_data="SELECT a.id,a.batch_no,a.floor_id,a.entry_form,a.batch_against,sum(b.batch_qnty) as batch_qty, a.batch_date,a.batch_weight, a.working_company_id,b.prod_id,b.po_id,a.color_id,a.booking_no,a.extention_no, b.item_description, a.booking_without_order  
	from pro_batch_create_mst a, pro_batch_create_dtls b 
	where a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $batch_no_cond $booking_no_cond $knit_company_cond and a.entry_form=0  ".where_con_using_array($batch_id_arr,0,'a.id')."
	group by a.batch_no,a.floor_id,a.batch_against,a.entry_form,a.id,a.batch_date,a.batch_weight, a.working_company_id,a.color_id,a.booking_no,a.extention_no,b.prod_id,b.po_id, b.item_description, a.booking_without_order  
	order by a.id";
	   //echo $sql_data;
	 // unset($batch_id_arr);


	$nameArray=sql_select($sql_data);
	$self_po_id_arr=array();
	$batch_wise_process_arr=array();
	foreach($nameArray as $row)
    {
		$batch_wise_process_arr[$row[csf('working_company_id')]][$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('id')]][$row[csf('item_description')]]['id']=$row[csf('id')];
		$batch_wise_process_arr[$row[csf('working_company_id')]][$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('id')]][$row[csf('item_description')]]['booking_no']=$row[csf('booking_no')];		
		$batch_wise_process_arr[$row[csf('working_company_id')]][$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('id')]][$row[csf('item_description')]]['color_id']=$row[csf('color_id')];
		$batch_wise_process_arr[$row[csf('working_company_id')]][$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('id')]][$row[csf('item_description')]]['batch_no']=$row[csf('batch_no')];
		$batch_wise_process_arr[$row[csf('working_company_id')]][$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('id')]][$row[csf('item_description')]]['item_description']=$row[csf('item_description')];
		$batch_wise_process_arr[$row[csf('working_company_id')]][$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('id')]][$row[csf('item_description')]]['extention_no']=$row[csf('extention_no')];
		$batch_wise_process_arr[$row[csf('working_company_id')]][$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('id')]][$row[csf('item_description')]]['po_id']=$row[csf('po_id')];
		$batch_wise_process_arr[$row[csf('working_company_id')]][$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('id')]][$row[csf('item_description')]]['working_company_id']=$row[csf('working_company_id')];
		$batch_wise_process_arr[$row[csf('working_company_id')]][$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('id')]][$row[csf('item_description')]]['working_company_id']=$row[csf('working_company_id')];

		$batch_wise_process_arr[$row[csf('working_company_id')]][$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('id')]][$row[csf('item_description')]]['batch_qty']+=$row[csf('batch_qty')];
		
		$self_po_id_arr[$row[csf('po_id')]]=$row[csf('po_id')];
	}


	// =======================Total Fabric Booking Qty (Fin.Fab.) with order Start ==============

	
		$poDataArray=sql_select("SELECT b.id,$year_field, a.buyer_name,a.job_no_prefix_num,a.style_ref_no as style
	from  wo_po_break_down b,wo_po_details_master a 
	where  a.job_no=b.job_no_mst and b.status_active!=0 and b.is_deleted=0  ".where_con_using_array($self_po_id_arr,0,'b.id')." ");// $ship_date_cond
	$self_all_po_id='';
	$job_array=array(); $all_job_id='';
	foreach($poDataArray as $row)
	{
		$job_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
		$job_array[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
		$job_array[$row[csf('id')]]['style']=$row[csf('style')];
	} //echo $all_po_id;


	
	

	$sql_booking="SELECT a.booking_no, b.fabric_color_id, b.construction, b.fin_fab_qnty, b.grey_fab_qnty 
	from wo_booking_mst a, wo_booking_dtls b
	where a.booking_no=b.booking_no  and b.booking_type=1 ".where_con_using_array($self_po_id_arr,0,'b.po_break_down_id')."  and company_id=$company_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$bookingArray=sql_select($sql_booking);
	$fab_booking_qty_arr=array();
	$grey_fab_booking_qnty_arr=array();
	foreach ($bookingArray as $value) 
	{
		$fab_booking_qty_arr[$value[csf('booking_no')]][$value[csf('fabric_color_id')]][$value[csf('construction')]]['fin_fab_qnty']+=$value[csf('fin_fab_qnty')];
		$grey_fab_booking_qnty_arr[$value[csf('booking_no')]][$value[csf('fabric_color_id')]][$value[csf('construction')]]['grey_fab_qnty']+=$value[csf('grey_fab_qnty')];
	}

	// =========================== Total Fabric Booking Qty (Fin.Fab.) with order End ===================

	ob_start();
	?>
	<div>	
		<table width="1350" cellspacing="0" cellpadding="0" border="0" rules="all" >
		    <tr class="form_caption">
		        <td colspan="30" align="center" style="border:none;font-size:16px; font-weight:bold"> <? echo $report_title; ?></td>
		    </tr>
		    <tr class="form_caption">
		        <td colspan="30" align="center"><?  if($company_id!=0) echo $company_library[$company_id];else echo $company_library[$working_company]; ?><br>
		        </b> <? echo change_date_format($previous_date); ?> </b>
		        </td>
		    </tr>
		</table>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1620" class="rpt_table">
				<?
				// =========================Booking, color row_span start====================
				foreach($batch_wise_process_arr as $working_com_id=>$workingComRows)
				{
					foreach($workingComRows as $booking_key=>$booking_value)
					{
						$booking_row_span=0;
						foreach ($booking_value as $color_id => $color_value)
						{
							$color_row_span=0;
							foreach ($color_value as $batch_id => $batch_no) 
							{
								foreach ($batch_no as $item_description => $row) 
								{
									$booking_row_span++; $color_row_span++;
								}
								$booking_rowspan_arr[$booking_key]=$booking_row_span;
								$color_rowspan_arr[$booking_key][$color_id]=$color_row_span;
							}
						}
					}
				}
				//print_r($booking_rowspan_arr);	
				// ==================Booking, color row_span end============================		

			    $i=1;
			    $grand_total_booking_qty=$grand_total_batch_qty=$grand_finish_qty=$grand_delivery_qty=$grand_total_process_loss_qty=0;
				foreach($batch_wise_process_arr as $working_com_id=>$workingComRows)
				{
					?>	
				
                    <tr style="background:#999">
                        <th width="100">Working Company</th>
                        <th width="110">Buyer</th>
                        <th width="80">Job No</th>
                        <th width="120">Style</th>	
                        <th width="110">Fabric Booking No</th>			
                        <th width="90">Color Name</th>
                        <th width="110">Total Fabric Booking Qty (Fin.Fab.)</th>
                        <th width="110">Batch No</th>
                        <th width="60">Ext.No</th>				
                        <th width="150">Fabrics Type</th>
                        <th width="80">Batch Qty. (Gray Fab)</th>
                        <th width="80"><p>Finis  Fab. Production Entry<p></th>
                        <th width="100"><p>Delivery To Store</p></th>
                        <th width="80"><p>Actul Process Loss Qty<p></th>
                        <th width="80"><p>K&D  Process Loss</p></th>
                        <th width="60"><p>Actual Process Loss %<p></th>
                        <th width="">Process Los Status</th>
                    </tr>
				<?
				
					$total_booking_qty=$total_batch_qty=$total_finish_qty=$total_delivery_qty=$total_process_loss_qty=0;
					foreach($workingComRows as $booking_key=>$booking_value)
					{
					
					$b=1; // booking row span increment
					foreach ($booking_value as $color_id => $color_value)
					{
						$c=1; // color row span increment
						foreach ($color_value as $batch_id => $batch_no) 
						{
							foreach ($batch_no as $item_description => $row) 
							{
								if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

								$po_id=rtrim($row[('po_id')],',');
							    $job_no=""; $buyer=""; $style_no='';
							    $po_id=array_unique(explode(",",$po_id));
							    foreach($po_id as $id)
							    {
									if($row[('entry_form')]==36) //SubCon
									{

									}
									else
									{
										if($job_no=="") $job_no=$job_array[$id]['job']; else $job_no.=",".$job_array[$id]['job'];
										if($buyer=="") $buyer=$buyer_library[$job_array[$id]['buyer']]; else $buyer.=",".$buyer_library[$job_array[$id]['buyer']];
										if($style_no=="") $style_no=$job_array[$id]['style']; else $style_no.=",".$job_array[$id]['style'];
									}
							    }
								//echo $buyer.'sssss';
							    $job=implode(',',array_unique(explode(",",$job_no)));
							    $buyer_name=implode(',',array_unique(explode(",",$buyer)));
							    $style_no=implode(',',array_unique(explode(",",$style_no)));
								$desc = explode(",", $row['item_description']);
								//echo $row[('booking_no')].'**'.$row[('color_id')].'**'.$desc[0].'<br>';
								$fab_book_qty = $fab_booking_qty_arr[$row[('booking_no')]][$row[('color_id')]][$desc[0]]['fin_fab_qnty'];
								$grey_fab_book_qnty = $grey_fab_booking_qnty_arr[$row[('booking_no')]][$row[('color_id')]][$desc[0]]['grey_fab_qnty'];
								if($fab_book_qty>0)
								{
									$kd_process_loss=(($grey_fab_book_qnty-$fab_book_qty)/$fab_book_qty)*100;
								} else $kd_process_loss=0;
							
								$finish_qty=$finish_data_arr[$batch_id]['finish_qty'];
								$delivery_qty=$delivery_data_arr[$batch_id]['delivery'];

								$booking_rowspan=$booking_rowspan_arr[$booking_key];
								$color_rowspan=$color_rowspan_arr[$booking_key][$color_id];
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trself_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trself_<? echo $i; ?>">
								
                                
									<td><p><? echo $company_library[$row[('working_company_id')]]; ?></p></td>
                                    
									<td><p><? echo $buyer_name; ?></p></td>
									<td><p><? echo $job; ?></p></td>
									<td style="word-break: break-all; word-wrap: break-word;"><p><? echo $style_no; ?></p></td>
									<td ><p><? echo $row[('booking_no')]; ?></p></td>
									
									
									<td ><p><? echo $color_library[$row[('color_id')]]; ?></p></td>
									<td align="right" title="<? echo $row[('booking_no')].'**'.$color_library[$row[('color_id')]].'**'.$desc[0]?>"><? echo number_format($fab_book_qty,2,'.',''); ?></td>
									
									
									<td title="Batch ID=<? echo $batch_id;?>"><p><? echo $row[('batch_no')]; ?></p></td>
									<td><p><? echo $row[('extention_no')]; ?></p></td>
									<td><p><? echo $desc[0];?></p></td>
									<td align="right"><p><?  echo number_format($row[('batch_qty')],2); ?></p></td>
									<td align="right"><p><? echo number_format($finish_qty,2); ?></p></td>
									<td align="right"><p><? echo number_format($delivery_qty,2); ?></p></td>
									<td align="right"><p><? 
									if ( $finish_qty==$delivery_qty && $finish_qty!=0 && $delivery_qty!=0) 
									{ 
										echo number_format($actul_process_loss_qty=$row[('batch_qty')]-$finish_qty,2);
									} else{ echo $actul_process_loss_qty=0; } ?>
									</p></td>
									<td align="right" title="<? echo "(grey_fab_book_qnty($grey_fab_book_qnty)-fab_book_qty($fab_book_qty))/fab_book_qty($fab_book_qty)*100" ?>"><p><? echo number_format($kd_process_loss,2,'.',''); ?></p></td>
									<td align="right"><p><? echo number_format($actul_process_loss_qty/$row[('batch_qty')]*100,2); ?></p></td>
									<?
									if($actul_process_loss_qty<$kd_process_loss)
										$process_loss_status_color="style='background-color: green;'";
									elseif($actul_process_loss_qty>$kd_process_loss)
										$process_loss_status_color="style='background-color: red;'";
								 	?>
									<td <? echo $process_loss_status_color; ?>><p>
										<? 
										if($actul_process_loss_qty<$kd_process_loss)
											$process_loss_status="Decrease";
										elseif($actul_process_loss_qty>$kd_process_loss)
											$process_loss_status="Increase";
										echo $process_loss_status;
									 	?></p>
									</td>
							    </tr>
							    <?
								$total_booking_qty+=$fab_book_qty;
								$total_batch_qty+=$row[('batch_qty')];
								$total_finish_qty+=$finish_qty;
								$total_delivery_qty+=$delivery_qty;
								$total_process_loss_qty+=$actul_process_loss_qty;
								
							    $i++; $b++; $c++;
							}
							
						}
				    }
				    
				}
				
				?>
				    <tr style="background:#777">
						<td colspan="6" align="right"><strong>Company wise Total:</strong></td>
						<td align="right"><strong><? echo number_format($total_booking_qty,2,'.',''); ?></strong></td>
						<td align="right"><strong><?  ?></strong></td>
						<td></td>
						<td></td>    
						<td></td>
						<td align="right"><strong><? echo $total_batch_qty; ?></strong></td>
						<td align="right"><strong><? echo $total_finish_qty; ?></strong></td>
						<td align="right"><strong><? echo $total_delivery_qty; ?></strong></td>
						<td align="right"><strong><? echo $total_process_loss_qty; ?></strong></td>	       
						<td></td>
						<td></td>
						<td></td>
					</tr>
				    <?
				    $grand_total_booking_qty+=$total_booking_qty;
				    $grand_total_batch_qty+=$total_batch_qty;
				    $grand_finish_qty+=$total_finish_qty;
				    $grand_delivery_qty+=$total_delivery_qty;
				    $grand_total_process_loss_qty+=$total_process_loss_qty;
				
				}
				//unset($batch_wise_process_arr);
			    ?>
				<tr>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th>Grand Total: </th>
					<th align="right"><strong><? echo number_format($grand_total_booking_qty,2,'.',''); ?></strong></th>
					<th align="right"><strong><?  ?></strong></th>  
					<th></th>
					<th></th>
					<th></th>
					<th align="right"><strong><? echo $grand_total_batch_qty; ?></strong></th> 
					<th align="right"><strong><? echo $grand_finish_qty; ?></strong></th>
					<th align="right"><strong><? echo $grand_delivery_qty; ?></strong></th>
					<th align="right"><strong><? echo $grand_total_process_loss_qty; ?></strong></th>       
					<th></th>
					<th></th>
					<th></th>
				</tr>
			</table>
		</div>	
	</div>
    <?
	$emailBody=ob_get_contents();
	ob_clean();
	
	echo $emailBody;

}

?>	
