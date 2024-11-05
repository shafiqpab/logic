<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//--------------------------------------------------------------------------------------------------------------------
$tmplte=explode("**",$data);

if ($tmplte[0]=="viewtemplate") $template=$tmplte[1]; else $template=$lib_report_template_array[$_SESSION['menu_id']]['0'];
if ($template=="") $template=1;

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	$cbo_company=str_replace("'","",$cbo_company_name);
	$cbo_supplier_name=str_replace("'","",$cbo_supplier_name);
	$from_date_c=str_replace("'","",$txt_date_from);
	$to_date_c=str_replace("'","",$txt_date_to);

	$datedif  = datediff( '', $from_date_c, $to_date_c);
	$duration = $datedif/3600/24;

	$supplier_name_arr=return_library_array( "select id,supplier_name from   lib_supplier",'id','supplier_name');
	$supplier_ad_arr=return_library_array( "select id,address_1 from   lib_supplier",'id','address_1');
	$supplier_contac_arr=return_library_array( "select id,contact_person from   lib_supplier",'id','contact_person');
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	$color_library_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name");
	/*$fab_desc_arr=return_library_array("select id, fabric_description from wo_pre_cost_fabric_cost_dtls", "id", "fabric_description");*/
	$fab_desc_arr=return_library_array("select c.id, c.fabric_description from wo_pre_cost_fabric_cost_dtls c,wo_booking_dtls b where b.pre_cost_fabric_cost_dtls_id=c.id and b.job_no=c.job_no ", "id", "fabric_description");
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );

	// if($db_type==2)
	// {
	// 	$booking_date = ($from_date_c=="")?"":" and a.booking_date between '".date("j-M-Y",strtotime($from_date_c))."' and '".date("j-M-Y",strtotime($to_date_c))."'";
	// }
	// else if($db_type==0)
	// {
	// 	if( $from_date_c=="" ) $booking_date=""; else $booking_date= " and a.booking_date between '".change_date_format($from_date_c,'yyyy-mm-dd')."' and '".change_date_format($to_date_c,'yyyy-mm-dd')."'";
	// }

	$supplier_sql = ($cbo_supplier_name !=0)? " and a.supplier_id = $cbo_supplier_name":"";

	if ($cbo_company==0) $company_id =""; else $company_id =" and a.company_id=$cbo_company";
	if($template==1)
	{
		ob_start();
		?>
		<div style="width:1705px">
			<fieldset style="width:100%;">

				<table width="100%" cellpadding="0" cellspacing="0" >
                        <tr>
                            <td align="center" style="font-size:20px;">
                              <?php      
                                    echo $company_library[$cbo_company];
                              ?>
                            </td>
                        </tr>
                        
                        <tr>
                            <td align="center" style="font-size:20px">
                            	<p>(100% Export Orineted Knit Garments)</p> 
                            	<strong>Dyieng Program & Job Contract Agreement</strong>
                            </td> 
                        </tr>
                </table>

				<table align="left" style="font-size: 18px;">
					<tr>
						<td><b>Date</b></td>
						<td>:&nbsp;&nbsp;&nbsp; 
							<? if($from_date_c!="0000-00-00" || $from_date_c!="") echo change_date_format($from_date_c); else echo "".""; ?>&nbsp;To&nbsp;
							<? if($to_date_c!="0000-00-00" || $to_date_c!="") echo change_date_format($to_date_c); else echo ""; ?>
						</td>
					</tr>
					<tr>
						<td><b>Party Name</b></td>
						<td>:&nbsp;&nbsp;&nbsp;<? echo $supplier_name_arr[$cbo_supplier_name]; ?></td>
					</tr>
					<tr>
						<td><b>Party Address</b></td>
						<td>:&nbsp;&nbsp;&nbsp;<? echo $supplier_ad_arr[$cbo_supplier_name]; ?></td>
					</tr>
					<tr>
						<td><b>Atten</b></td>
						<td>:&nbsp;&nbsp;&nbsp;<? echo $supplier_contac_arr[$cbo_supplier_name]; ?></td></td>
					</tr>
				</table>
				<table class="rpt_table" width="1685" cellpadding="0" cellspacing="0" border="1" rules="all">
					<thead>
						<th width="40">Sl No</th>
						<th width="120">Booking No</th>
						<th width="90">Buyer</th>
						<th width="120">Job No</th>
						<th width="120">Style</th>
						<th width="120">Color</th>
						<th width="160">Fabric type</th>
						<th width="90">MC Dia</th>
						<th width="90">SL</th>
						<th width="90">GSM</th>
						<th width="90">F Dia</th>
						<th width="90">Qty</th>
						<th width="90">Dyeing Led Duration</th>
						<th width="100">Remarks</th>
					</thead>
				</table>
				<div style="width:1705px; max-height:250px; overflow-y:scroll" id="scroll_body">
					<table class="rpt_table" width="1685" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
						<?php
						$sql="select a.id, a.booking_no, a.buyer_id, a.job_no,b.id as dtls_id,b.fabric_color_id, b.mc_dia, b.fin_gsm, b.wo_qnty,b.fin_dia,b.remark,b.slength,b.pre_cost_fabric_cost_dtls_id,a.process as pp, c.style_ref_no from  wo_booking_mst a, wo_booking_dtls b, wo_po_details_master c where a.job_no=c.job_no and b.process=31 and a.booking_no=b.booking_no and a.booking_type=3 $company_id $booking_date $supplier_sql and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.buyer_id, a.job_no ASC ";
						//echo $sql;die;

						$sql_data = sql_select($sql); $buyer_arr=array(); $job_arr=array();
						$i=1; $k=1; $j=1;
						foreach($sql_data as $row)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							if(!in_array($row[csf('job_no')],$job_arr))
							{
								if($j!=1)
								{
									?>
									<tr class="tbl_bottom">
										<td width="40">&nbsp;</td>
										<td width="120">&nbsp;</td>
										<td width="90">&nbsp;</td>                       
										<td width="120">&nbsp;</td>
										<td width="120">&nbsp;</td>
										<td width="120">&nbsp;</td>
										<td width="160">&nbsp;</td>
										<td width="90">&nbsp;</td>
										<td width="90">&nbsp;</td>
										<td width="90">&nbsp;</td>
										<td width="90">Job Total</td>
										<td width="90" align="right"><? echo number_format($job_wo_qty,2); ?></td>
										<td width="90">&nbsp;</td>
										<td>&nbsp;</td>
									</tr>
									<? 
									unset($job_wo_qty);
								}
								if(!in_array($row[csf('buyer_id')],$buyer_arr))
								{
									if($k!=1)
									{ 
										?>
										<tr class="tbl_bottom">
											<td width="40">&nbsp;</td>
											<td width="120">&nbsp;</td>
											<td width="90">&nbsp;</td>                       
											<td width="120">&nbsp;</td>
											<td width="120">&nbsp;</td>
											<td width="120">&nbsp;</td>
											<td width="160">&nbsp;</td>
											<td width="90">&nbsp;</td>
											<td width="90">&nbsp;</td>
											<td width="90">&nbsp;</td>
											<td width="90">Buyer Total</td>
											<td width="90" align="right"><? echo number_format($buyer_wo_qty,2); ?></td>
											<td width="90">&nbsp;</td>
											<td>&nbsp;</td>
										</tr>
										<? 
										unset($job_wo_qty);
										unset($buyer_wo_qty);
									}
									$buyer_arr[]=$row[csf('buyer_id')];
									$k++; 
								}
								?>
                                    <tr bgcolor="#dddddd">
                                        <td colspan="14" align="left" ><b>Buyer Name: <? echo $buyer_name_arr[$row[csf('buyer_id')]]; ?>;</b> &nbsp;&nbsp;&nbsp;<b>Job No.: <? echo $row[csf('job_no')]; ?></b></td>
                                    </tr>
                                <?
								$job_arr[]=$row[csf('job_no')];
								$j++;
							}
							?>
							<tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_d<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_d<? echo $i; ?>">
								<td width="40" ><? echo $i;?></td>
								<td width="120" style="word-wrap:break-word; width:120px"><? echo $row[csf('booking_no')]; ?></td>
								<td width="90" style="word-wrap:break-word; width:90px"><? echo $buyer_name_arr[$row[csf('buyer_id')]]; ?></td>                       
								<td width="120" style="word-wrap:break-word; width:120px"><? echo $row[csf('job_no')]; ?></td>
								<td width="120" style="word-wrap:break-word; width:120px"><? echo $row[csf('style_ref_no')]; ?></td>
								<td width="120" style="word-wrap:break-word; width:120px"><p><? echo $color_library_arr[$row[csf('fabric_color_id')]]; ?></p></td>
								<td width="160" style="word-wrap:break-word; width:160px"><p><? echo $fab_desc_arr[$row[csf('pre_cost_fabric_cost_dtls_id')]]; ?></p></td>
								<td width="90" style="word-wrap:break-word; width:90px"><p><? echo $row[csf('mc_dia')]; ?></p></td>
								<td width="90" style="word-wrap:break-word; width:90px"><p><? echo $row[csf('slength')]; ?></p></td>
								<td width="90" style="word-wrap:break-word; width:90px"><p><? echo $row[csf('fin_gsm')]; ?></p></td>
								<td width="90" style="word-wrap:break-word; width:90px"><p><? echo $row[csf('fin_dia')]; ?></p></td>
								<td width="90" align="right"><? echo number_format($row[csf('wo_qnty')],2); ?></td>
								<td width="90" style="word-wrap:break-word; width:90px"><p><? echo $duration." Days"; ?></p></td>
								<td style="word-wrap:break-word; width:100px"><p><? echo $row[csf('remark')]; ?></p>&nbsp;</td>
							</tr>
							<? 
							$job_wo_qty+=$row[csf('wo_qnty')];
							$buyer_wo_qty+=$row[csf('wo_qnty')];
							$grand_wo_qty+=$row[csf('wo_qnty')];
							
							$i++;
						}
						?>
                        <tr class="tbl_bottom">
                            <td width="40">&nbsp;</td>
                            <td width="120">&nbsp;</td>
                            <td width="90">&nbsp;</td>                       
                            <td width="120">&nbsp;</td>
                            <td width="120">&nbsp;</td>
                            <td width="120">&nbsp;</td>
                            <td width="160">&nbsp;</td>
                            <td width="90">&nbsp;</td>
                            <td width="90">&nbsp;</td>
                            <td width="90">&nbsp;</td>
                            <td width="90">Job Total</td>
                            <td width="90" align="right"><? echo number_format($job_wo_qty,2); ?></td>
                            <td width="90">&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
						<tr class="tbl_bottom">
                            <td width="40">&nbsp;</td>
                            <td width="120">&nbsp;</td>
                            <td width="90">&nbsp;</td>                       
                            <td width="120">&nbsp;</td>
                            <td width="120">&nbsp;</td>
                            <td width="120">&nbsp;</td>
                            <td width="160">&nbsp;</td>
                            <td width="90">&nbsp;</td>
                            <td width="90">&nbsp;</td>
                            <td width="90">&nbsp;</td>
                            <td width="90">Buyer Total</td>
                            <td width="90" align="right"><? echo number_format($buyer_wo_qty,2); ?></td>
                            <td width="90">&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr class="tbl_bottom">
                            <td width="40">&nbsp;</td>
                            <td width="120">&nbsp;</td>
                            <td width="90">&nbsp;</td>                       
                            <td width="120">&nbsp;</td>
                            <td width="120">&nbsp;</td>
                            <td width="120">&nbsp;</td>
                            <td width="160">&nbsp;</td>
                            <td width="90">&nbsp;</td>
                            <td width="90">&nbsp;</td>
                            <td width="90">&nbsp;</td>
                            <td width="90">Grand Total</td>
                            <td width="90" align="right"><? echo number_format($grand_wo_qty,2); ?></td>
                            <td width="90">&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
					</table>
				</div>
			</fieldset>
		</div>
		<?
	}
	exit();	
}
//disconnect($con);
?>