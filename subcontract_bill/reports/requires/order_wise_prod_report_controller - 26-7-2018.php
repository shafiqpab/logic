<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
//--------------------------------------------------------------------------------------------------------------------
if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_id", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );   	 
	exit();
}

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_id", 120, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select Location--", $selected, "load_drop_down( 'requires/order_wise_prod_report_controller', this.value, 'load_drop_down_floor', 'floor_td' );",0 );
	exit();     	 
}

if ($action=="load_drop_down_floor")
{
	echo create_drop_down( "cbo_floor_id", 120, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' order by floor_name","id,floor_name", 1, "-- Select Floor--", $selected, "",0 );  
	exit();   	 
}


if($action=="order_no_popup")
{
	echo load_html_head_contents("PO Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script type="text/javascript">
		function js_set_value(id)
		{ 
			document.getElementById('selected_id').value=id;
			parent.emailwindow.hide();
		}
	</script>
	<input type="hidden" id="selected_id" name="selected_id" /> 
	<?
	$buyer = str_replace("'","",$cbo_buyer_name);
	$location = str_replace("'","",$cbo_location_id);
	$cbo_floor = str_replace("'","",$cbo_floor_id);

    if($db_type==0) 
	{
		$year_field="year(a.insert_date) as year"; 
	}
    else if($db_type==2) 
	{
		$year_field="to_char(a.insert_date,'YYYY') as year";
	}
    else 
	{
		$year_field="";
	}

	if(trim($location)==0) $sub_location_name_cond=""; else $sub_location_name_cond=" and a.location_id=$location";
	if(trim($buyer)==0) $sub_buyer_name_cond=""; else $sub_buyer_name_cond=" and a.party_id=$buyer";
	
	$sql="select distinct b.id, b.job_no_mst as job_no ,a.party_id as buyer_name, b.order_no as po_number, a.job_no_prefix_num as job_prefix, $year_field from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c where b.job_no_mst=a.subcon_job and a.id=c.mst_id and b.id=c.order_id and a.company_id=$company_id $sub_buyer_name_cond $sub_location_name_cond and a.is_deleted =0 group by b.id, a.party_id, b.job_no_mst, b.order_no, a.job_no_prefix_num, a.company_id, a.insert_date";
	
	$buyer=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
	?>
	<table width="500" border="1" rules="all" class="rpt_table">
        <thead>
            <tr>
                <th width="30">SL</th>
                <th width="100">Order Number</th>
                <th width="50">Job no</th>
                <th width="80">Buyer</th>
                <th width="40">Year</th>
            </tr>
        </thead>
    </table>
    <div style="max-height:340px; overflow:auto;">
        <table id="table_body2" width="500" border="1" rules="all" class="rpt_table">
			<? 
			$rows=sql_select($sql);
            $i=1;
            foreach($rows as $data)
            {
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onclick="js_set_value('<? echo $data[csf('id')].'_'.$data[csf('po_number')]; ?>')" style="cursor:pointer;">
                    <td width="30"><? echo $i; ?></td>
                    <td width="100"><p><? echo $data[csf('po_number')]; ?></p></td>
                    <td width="50"><p><? echo $data[csf('job_prefix')]; ?></p></td>
                    <td width="80"><p><? echo $buyer[$data[csf('buyer_name')]]; ?></p></td>
                    <td width="40" align="center"><p><? echo $data[csf('year')]; ?></p></td>
				</tr>
				<? $i++; 
			} ?>
		</table>
	</div>
	<script> setFilterGrid("table_body2",-1); </script>
	<?
	exit();
}



if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$company_short_library=return_library_array( "select id,company_short_name from lib_company", "id", "company_short_name"  );
 	$buyer_short_library=return_library_array( "select id,short_name from lib_buyer", "id", "short_name"  );
	$line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"  ); 
 	$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name"  );

 	$txt_order_no=str_replace("'","",$txt_order_no);
	if ($txt_order_no!='') $order_no_cond=" and c.order_no like '%$txt_order_no%'"; else $order_no_cond="";

	$type = str_replace("'","",$cbo_type);
	if(str_replace("'","",$cbo_company_id)==0)$company_name=""; else $company_name=" and a.company_id=$cbo_company_id";
	if(str_replace("'","",$cbo_buyer_id)==0)$buyer_name="";else $buyer_name=" and a.party_id=$cbo_buyer_id";
	
	if(str_replace("'","",$cbo_location_id)==0)$location="";else $location=" and a.location_id 	=$cbo_location_id";
	if(str_replace("'","",$cbo_floor_id)==0)$floor="";else $floor=" and b.floor_id=$cbo_floor_id";
	
	if(str_replace("'","",trim($txt_date_from))=="" || str_replace("'","",trim($txt_date_to))=="")$txt_date="";
	else $txt_date=" and b.production_date between $txt_date_from and $txt_date_to";
	
	$fromDate = change_date_format( str_replace("'","",trim($txt_date_from)) );
	$toDate = change_date_format( str_replace("'","",trim($txt_date_to)) );
		
		
	if($type==2 || $type==4) //------------------------------------Show Date Location Floor & Line Wise $type==2
	{
		$groupByCond = "group by a.id,c.location,c.floor_id order by a.id,c.location,c.floor_id";
	}
	else //--------------------------------------------Show Order Wise  $type==1
	{
		$groupByCond = "group by a.id order by a.pub_shipment_date,a.id";
	}
		ob_start();
	?>
    <div style="width:1850px"> 
        <table width="1000"  cellspacing="0" >
            <tr class="form_caption" style="border:none;">
                <td align="center" style="border:none;font-size:16px; font-weight:bold" >
                    <? 
                        if($type==1) echo "Order Wise Production Report";
                        else if($type==2) echo "Order Location & Floor Wise Production Report";
                        else if($type==3) echo "Order Country Wise Production Report";
                        else echo "Order Country Location & Floor Wise Production Report";
                    ?>    
                </td>
            </tr>
            <tr style="border:none;">
                <td align="center" style="border:none; font-size:14px;">
                    Company Name : <? echo $company_library[str_replace("'","",$cbo_company_id)]; ?>                                
                </td>
            </tr>
            <tr style="border:none;">
                <td align="center" style="border:none;font-size:12px; font-weight:bold">
                <? echo "From $fromDate To $toDate" ;?>
                </td>
            </tr>
        </table>
        <div style="float:left; width:750px">
            <table width="730" cellspacing="0" border="1" class="rpt_table" rules="all" id="" >
                <thead>
                    <tr>
                        <th width="40">Sl.</th>    
                        <th width="110">Buyer Name</th>
                        <th width="80">Order Qty.(Pcs)</th>
                        <th width="80">PO Value</th>
                        <th width="80">Total Cut Qty</th>
                        <th width="80">Total Sew Qty</th>
                        <th width="80">Delivery Quantity</th>
                        <th width="">Delivery %</th>
                    </tr>
                </thead>
            </table>
            <div style="max-height:425px; width:730px" >
                <table cellspacing="0" border="1" class="rpt_table"  width="730" rules="all" id="" >
					<?  $total_po_quantity=0;$total_po_value=0;$total_cut=0;$total_sew_out=0;$total_ex_factory=0;
						$i=1;
						$exfactory_sql="select a.party_id, sum(CASE WHEN d.order_id=c.id THEN d.delivery_qty ELSE 0 END) as delivery_quantity from subcon_ord_mst a, subcon_ord_dtls c, subcon_delivery_dtls d where c.id=d.order_id and a.subcon_job=c.job_no_mst and a.is_deleted=0 and a.status_active=1 and c.is_deleted=0 and c.status_active=1 $company_name $buyer_name $location $floor $txt_date $order_no_cond group by a.party_id";
						
						$exfactory_sql_result=sql_select($exfactory_sql);
						$exfactory_arr=array(); 
						foreach($exfactory_sql_result as $resRow)
						{
							$exfactory_arr[$resRow[csf("party_id")]] = $resRow[csf("delivery_quantity")];
						}

						$pro_date_sql="SELECT a.party_id, sum(c.order_quantity) as po_quantity, sum(c.amount) as po_total_price from subcon_ord_mst a, subcon_ord_dtls c, lib_buyer d where c.job_no_mst=a.subcon_job and a.party_id=d.id and a.status_active=1 $company_name $buyer_name $location $floor $txt_date $order_no_cond group by a.party_id order by a.party_id ASC";
						
						$pro_date_sql_result=sql_select($pro_date_sql);	
						foreach($pro_date_sql_result as $pro_date_sql_row)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$production_mst_sql= "SELECT sum(CASE WHEN production_type ='1' THEN production_qnty ELSE 0 END) AS cutting_qnty, sum(CASE WHEN production_type ='2' THEN production_qnty ELSE 0 END) AS sewingout_qnty from subcon_ord_mst a, subcon_gmts_prod_dtls b, subcon_ord_dtls c where c.job_no_mst=a.subcon_job and c.id=b.order_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and a.party_id=".$pro_date_sql_row[csf("party_id")]." $company_name $location $floor $txt_date $order_no_cond";
                            
							$production_mst_sql_result=sql_select($production_mst_sql);
                            foreach($production_mst_sql_result as $row)
                            {
								?>
                                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
                                    <td width="40"><? echo $i;?></td>
                                    <td width="110"><? echo $buyer_short_library[$pro_date_sql_row[csf("party_id")]]; ?></td>
                                    <td width="80" align="right"><? echo number_format($pro_date_sql_row[csf("po_quantity")]);?>&nbsp;</td>
                                    <td width="80" align="right"><? echo number_format($pro_date_sql_row[csf("po_total_price")],2);?>&nbsp;</td>
                                    <td width="80" align="right"><? echo number_format($row[csf("cutting_qnty")]); ?>&nbsp;</td>
                                    <td width="80" align="right"><? echo number_format($row[csf("sewingout_qnty")]); ?>&nbsp;</td>
                                    <td width="80" align="right"><? echo number_format($exfactory_arr[$pro_date_sql_row[csf("party_id")]]); ?>&nbsp;</td>
                                    <? $ex_gd_status = ($exfactory_arr[$pro_date_sql_row[csf("party_id")]]/$pro_date_sql_row[csf("po_quantity")])*100; ?>
                                    <td width="" align="right"><? echo  number_format($ex_gd_status,2)." %"; ?>&nbsp;</td>
                                </tr>	
                                <?		
                                    $total_po_quantity+=$pro_date_sql_row[csf("po_quantity")];
                                    $total_po_value+=$pro_date_sql_row[csf("po_total_price")];
                                    $total_cut+=$row[csf("cutting_qnty")];
                                    $total_sew_out+=$row[csf("sewingout_qnty")];
                                    $total_ex_factory+=$exfactory_arr[$pro_date_sql_row[csf("party_id")]];
						   } 
                            $i++;
                        }
                        ?>
                    </table>
                    <table border="1" class="tbl_bottom"  width="730" rules="all" id="" >
                        <tr> 
                            <td width="40">&nbsp;</td> 
                            <td width="110" align="right">Total</td> 
                            <td width="80" id="tot_po_quantity"><? echo number_format($total_po_quantity); ?>&nbsp;</td> 
                            <td width="80" id="tot_po_value"><? echo number_format($total_po_value); ?>&nbsp;</td> 
                            <td width="80" id="tot_cutting"><? echo number_format($total_cut); ?>&nbsp;</td>
                            <td width="80" id="tot_sew_out"><? echo number_format($total_sew_out); ?>&nbsp;</td>   
                            <td width="80"><? echo number_format($total_ex_factory); ?>&nbsp;</td >
                            <? $total_ex_status = ($total_ex_factory/$total_po_quantity)*100; ?>
                            <td width=""><? echo number_format($total_ex_status,2); ?>&nbsp;</td>
                        </tr>
                    </table>
                 </div>
                 </div>
                <div style="clear:both"></div>
                <br />
                <div>


                    <table width="1760" cellspacing="0" border="1" class="rpt_table" rules="all" id="table_header_1">
                        <thead>
                            <tr>
                                <th width="40">SL</th>    
                                <th width="100">Order Number</th>
                                <th width="100">Buyer Name</th>
                                <th width="100">Job Number</th>
                                <th width="100">Style Name</th>
                                <th width="150">Item Name</th>
                                <th width="80">Order Qty.</th>
                                <th width="150">Order Value</th>
                                <th width="80">Plan Delivery Date</th>
                                <th width="80">Delivery Date</th>
                                <th width="80">Delay By</th>
                                <th width="80">Early By</th>
                                <th width="80">Total Cut Qty</th>
                                <th width="80">Actual Exc. Cut %</th>
                                <th width="80">Total Sew Qty</th>
                                <th width="80">Total Delivery</th>
                                <th width="80">Shortage/ Excess</th>
                                <th width="80">Status</th>
                                <th width="">Remarks</th>
                            </tr>
                        </thead>
                    </table>


                    <div style="max-height:425px; width:1780px" id="scroll_body">
                        <table border="1" class="rpt_table"  width="1760" rules="all" id="table_body" >
					<? 

					  	$totaL_delivery_sql="select a.party_id, c.id, sum(CASE WHEN d.order_id=c.id THEN d.delivery_qty ELSE 0 END) as delivery_quantity from subcon_ord_mst a, subcon_ord_dtls c,subcon_delivery_dtls d where c.id=d.order_id and a.subcon_job=c.job_no_mst and a.is_deleted=0 and a.status_active=1 and c.is_deleted=0 and c.status_active=1 $company_name $buyer_name $location $floor $txt_date $order_no_cond group by a.party_id,c.id";
						
						$totaL_delivery_sql_result=sql_select($totaL_delivery_sql);
						$totaL_delivery=array(); 
						foreach($totaL_delivery_sql_result as $resRow)
						{
							$totaL_delivery[$resRow[csf("party_id")]][$resRow[csf("id")]] = $resRow[csf("delivery_quantity")];
						}

						$po_wise_gmts_item=array();
						$gmts_item_id_arr=sql_select("select order_id, log_concat(distinct(gmts_item_id)) as gmts_item_id from subcon_gmts_prod_dtls where status_active=1 group by order_id");
						foreach($gmts_item_id_arr as $gmtRow)
						{
							$po_wise_gmts_item[$gmtRow[csf("order_id")]]= $gmtRow[csf("gmts_item_id")];
						}


						if($db_type==0)  
					    {
					       $cust_style_ref = "group_concat(c.cust_style_ref) as cust_style_ref";
					    }
					    else
					    {
					       $cust_style_ref = "listagg(cast(c.cust_style_ref as varchar2(4000)),',') within group (order by c.cust_style_ref) as cust_style_ref";
					    }


						$order_sql="SELECT a.party_id,a.job_no_prefix_num,c.id, c.order_no,c.delivery_date, $cust_style_ref, sum(c.order_quantity) as order_quantity, sum(c.amount) as order_value from subcon_ord_mst a, subcon_ord_dtls c, lib_buyer d where c.job_no_mst=a.subcon_job and a.party_id=d.id and a.status_active=1 $company_name $buyer_name $location $floor $txt_date $order_no_cond group by a.party_id, c.id, c.order_no, a.job_no_prefix_num, c.delivery_date order by a.party_id ASC";

								$order_sql_result=sql_select($order_sql);
								   $i=0; $k=0;
								   foreach($order_sql_result as $orderRes)
								   {
									   $i++;
									   if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									   
										?>
										<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>" style="height:20px">
											
											<td width="40" ><? echo $i; ?></td>  

											<td width="100">
												<p>
												<? echo $orderRes[csf("order_no")]; ?>
												</p>
											</td>

											<td width="100"><? echo $buyer_short_library[$orderRes[csf("party_id")]]; ?></td>
											
											<td width="100" align="center">
												<p>
												<? echo $orderRes[csf("job_no_prefix_num")];?>
												</p>
											</td>

											<td width="100">
												<p>
													<? echo implode(",", array_unique(explode(",", $orderRes[csf("cust_style_ref")]))); ?>
												</p>
											</td>
											<td width="150"><p><? echo $garments_item[$po_wise_gmts_item[$orderRes[csf("id")]]];?></p></td>
											
											<td width="80" align="right">
												<? 
												$order_quantities = $orderRes[csf("order_quantity")];
												echo number_format($order_quantities); 
												$total_ord_quantity+=trim($order_quantities); 
												?>
												&nbsp;
											</td>

											<td width="150" align="right">
												<? 
												$order_values = $orderRes[csf("order_value")];
												echo number_format($order_values); 
												$total_order_values+=trim($order_values); 
												?>
												&nbsp;
											</td>

											<?
											   $sqlEx = sql_select("select MAX(a.delivery_date) AS ex_fac_date, sum(b.delivery_qty) AS ex_fac_qnty from subcon_delivery_mst a, subcon_delivery_dtls b where a.id=b.mst_id and b.order_id=".$orderRes[csf("id")]." ");

											   $last_delivered_date=$sqlEx[0][csf('ex_fac_date')];
											   $delivered_qty=$sqlEx[0][csf('ex_fac_qnty')];
											   
											   /*$orderRes[ex_fac_date]=$sqlEx[0][csf('ex_fac_date')];
											   $orderRes[ex_fac_qnty]=$sqlEx[0][csf('ex_fac_qnty')];
												$ex_factory_date = $orderRes[csf("ex_fac_date")];
													$date=date("Y-m-d");$color="";$days_remian="";
														$days_remian=datediff("d",$date,$orderRes[csf("production_date")]);
														$days_remians=datediff("d",$ex_factory_date,$orderRes[csf("production_date")]);
														if($orderRes[csf("production_date")] > $date) 
														{
															$color="green";
														}
														else if($orderRes[csf("production_date")] >= $ex_factory_date) 
														{ 
															$color="green";
														}
														else if($orderRes[csf("production_date")] < $date || $ex_factory_date=="") 
														{
															$color="red";
														}														
														else if($orderRes[csf("production_date")] >= $date && $days_remian<=5 ) 
														{
															$color="orange";
														}
														else if($orderRes[csf("production_date")] < $ex_factory_date) 
														{ 
															$color="#2A9FFF";
														}	*/	


											$date=date("d-m-Y"); $days_remain=""; $days_early="";

									//calculating early delivery days

									if(($date < change_date_format($orderRes[csf("delivery_date")])) || ($last_delivered_date < change_date_format($orderRes[csf("delivery_date")])))
									{
										if($delivered_qty >= $order_quantities)
										{
											$days_early=datediff("d",change_date_format($orderRes[csf("delivery_date")]),$last_delivered_date)-1;
											if($days_early<0){$days_early=$days_early*(-1);}
										}
									}
									

									//calculating Delay delivery days

									if($delivered_qty >= $order_quantities)
									{
										if(($orderRes[csf("delivery_date")] == $last_delivered_date) || ($orderRes[csf("delivery_date")] > $last_delivered_date))
										{
											$days_remain="";
										}
									}
									else
									{

										if($date < change_date_format($orderRes[csf("delivery_date")]))
										{
											$days_remain="";
										}
										else
										{
											if($delivered_qty < $order_quantities)
											{
												$days_remain=datediff("d",change_date_format($orderRes[csf("delivery_date")]),$date)-1;
											}
											else if($delivered_qty >= $order_quantities)
											{
												$days_remain=datediff("d",change_date_format($orderRes[csf("delivery_date")]),$last_delivered_date)-1;
											}
										}
									}
																		 
								?>
												
												<td width="80" bgcolor="<? echo $color; ?>">
													<? echo change_date_format($orderRes[csf("delivery_date")]);  ?>
												</td>

                                                <td width="80" bgcolor="<? echo $color; ?>">
                                                	<? 
                                                		if(!($last_delivered_date=="" || $last_delivered_date=="0000-00-00")) 
                                                		echo change_date_format($last_delivered_date);
                                                	?>
                                                </td>

                                                <td width="80" align="center" title="<? echo $days_remain; ?>">
                                                	<? echo $days_remain;  ?>
                                                </td>

                                                <td width="80" align="center" title="<? echo $days_early; ?>">
													<? echo $days_early; ?>
												</td>

                                   <?											
											$production_mst_sql= "SELECT sum(CASE WHEN production_type ='1' THEN production_qnty ELSE 0 END) AS cutting_qnty, sum(CASE WHEN production_type ='2' THEN production_qnty ELSE 0 END) AS sewingout_qnty from subcon_ord_mst a, subcon_gmts_prod_dtls b, subcon_ord_dtls c where c.job_no_mst=a.subcon_job and c.id=b.order_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and a.party_id=".$orderRes[csf("party_id")]." and c.id=".$orderRes[csf("id")]." $company_name $location $floor $txt_date $order_no_cond";
                            
											$production_mst_sql_result=sql_select($production_mst_sql);
				                            foreach($production_mst_sql_result as $proRes)
				                            {
											
												$actual_exces_cut = $proRes[csf("cutting_qnty")];
							
												if($actual_exces_cut < $orderRes[csf("order_quantity")])$actual_exces_cut=""; 
												else $actual_exces_cut=number_format( (($actual_exces_cut-$orderRes[csf("order_quantity")])/$orderRes[csf("order_quantity")])*100,2)."%";
									?>
												 
												<td width="80" align="right"><? echo number_format($proRes[csf("cutting_qnty")]); $total_cutt+=$proRes[csf("cutting_qnty")]; ?>&nbsp;</td>

												<td width="80" align="right" <? //if(round($actual_exces_cut) > round($orderRes[csf("excess_cut")])) echo "bgcolor='#FF0000'"; ?>>

													<? echo $actual_exces_cut; ?>
												</td>

												<td width="80" align="right"><? echo number_format($proRes[csf("sewingout_qnty")]); $total_sew+=$proRes[csf("sewingout_qnty")]; ?>&nbsp;</td>
												

												<td width="80" align="right">
												<? 
													if($totaL_delivery[$orderRes[csf("party_id")]][$orderRes[csf("id")]] != "")
													{
														$tot_delivery=$totaL_delivery[$orderRes[csf("party_id")]][$orderRes[csf("id")]];
													}
													else
													{
														$tot_delivery=0;
													} 
													echo number_format($tot_delivery);

													$total_out_out+=trim($tot_delivery);
												?>
												&nbsp;
												</td>

												<? $shortage = $orderRes[csf("order_quantity")]-$tot_delivery; ?>
												
												<td width="80" align="right"><? echo number_format($shortage); $total_shortage+=$shortage; ?>&nbsp;</td>
												
												<td width="80">
													<? //echo $shipment_status[$orderRes[csf("shiping_status")]]; ?>
													&nbsp;
												</td>
												
												<td width="">&nbsp;</td>

										<?  }   ?>

                                             </tr>
										<?
								   }
								  ?>  
                                </table>	
                                <table border="1" class="tbl_bottom"  width="1760" rules="all" id="report_table_footer_1" >
                                    <tr>
                                        <td width="40"></td>
                                        <td width="100"></td>
                                        <td width="100"></td>
                                        <td width="100"></td>
                                        <td width="100"></td>
                                        <td width="150">Total</td>
                                        <td width="80" id="total_ord_quantity" align="right"><? echo number_format($total_ord_quantity); ?>&nbsp;</td>
                                        <td width="150" id="total_ord_values" align="right"><? echo number_format($total_order_values); ?>&nbsp;</td>
                                        <td width="80"></td>
                                        <td width="80"></td>
                                        <td width="80"></td>
                                        <td width="80"></td>
                                        <td width="80" align="right"><? echo number_format($total_cutt); ?>&nbsp;</td>
                                        <td width="80"></td>
                                        <td width="80" align="right"><? echo number_format($total_sew); ?>&nbsp;</td>
                                        <td width="80" align="right"><? echo number_format($total_out_out); ?>&nbsp;</td>
                                        <td width="80" align="right"><? echo number_format($total_shortage); ?>&nbsp;</td>
                                        <td width="80"></td>
                                        <td width=""></td>
                                     </tr>
                             </table>
                            </div>
			  </div>
        <br /><br />		
        </div><!-- end main div -->
		<?
	$html = ob_get_contents();
	ob_clean();
	$new_link=create_delete_report_file( $html, 1, 1, "../../../" );
	
	echo "$html";
	exit();	
}
?>