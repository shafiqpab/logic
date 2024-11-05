<?
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../../includes/common.php');


error_reporting(1);
ini_set('display_errors',1);

$_SESSION['page_permission']=$permission;
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

$buyer_arr=return_library_array("select id,buyer_name from  lib_buyer","id","buyer_name");
$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );     	 
	exit();
}

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$report_title=str_replace("'","",$report_title);
	$reportType=str_replace("'","",$reportType);
        $cbo_item_catgory=str_replace("'","",$cbo_item_catgory);
        
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and c.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and c.buyer_name=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)
	}
	
	$date_cond="";
	if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
	{
		if($db_type==0)
		{
			$start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
			$end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
		}
		else if($db_type==2)
		{
			$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
			$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
		}
		$date_cond=" and b.shipment_date between '$start_date' and '$end_date'";
		
	}
	
	if($cbo_item_catgory != "")
	{
		$cbo_item_cond = " and c.product_category in ($cbo_item_catgory) ";
	}
	
	ob_start();
	
		
	$i=1;
		
	$onlyJobQty_sql=sql_select("select b.job_no_mst, sum(b.po_quantity) as po_quantity,c.avg_unit_price from wo_po_break_down b ,wo_po_details_master c  
	where  c.job_no=b.job_no_mst and b.is_deleted=0 and b.status_active=1  group by b.job_no_mst,c.avg_unit_price ");
	
	$job_wise_export_arr=array();
	foreach($onlyJobQty_sql as $row)
	{
		$job_wise_export_arr[$row[csf("job_no_mst")]]['job_quantity']=$row[csf("po_quantity")];	
		$job_wise_export_arr[$row[csf("job_no_mst")]]['avg_unit_price']=$row[csf("avg_unit_price")];	
	}
	
	 $sql= "select  b.id as po_id,b.po_number,c.company_name, c.buyer_name, c.job_no_prefix_num,c.style_ref_no,c.job_no,
	sum(CASE WHEN a.entry_form!=85 THEN a.ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
	sum(CASE WHEN a.entry_form=85 THEN a.ex_factory_qnty ELSE 0 END) as ret_ex_factory_qnty,
	sum(distinct b.po_quantity) as po_quantity,
	sum(distinct b.po_quantity*c.total_set_qnty) as po_quantity_pcs,(b.unit_price/c.total_set_qnty) as unit_price,c.total_set_qnty
	 from  wo_po_details_master c,wo_po_break_down b left join pro_ex_factory_mst a on (b.id=a.po_break_down_id and a.is_deleted=0 and a.status_active=1)
	where c.job_no=b.job_no_mst and c.company_name=$cbo_company_name and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $date_cond $buyer_id_cond $cbo_item_cond   group by  c.job_no,b.id,b.po_number,c.company_name, c.buyer_name, c.job_no_prefix_num,c.style_ref_no,b.unit_price,c.total_set_qnty order by b.id";
		
		
		$sql_result=sql_select($sql);
		foreach($sql_result as $row)
		{
			$po_wise_export_arr[$row[csf("po_id")]]['po_number']=$row[csf("po_number")];	
			$po_wise_export_arr[$row[csf("po_id")]]['job']=$row[csf("job_no_prefix_num")];
			$po_wise_export_arr[$row[csf("po_id")]]['job_no']=$row[csf("job_no")];	
			$po_wise_export_arr[$row[csf("po_id")]]['style']=$row[csf("style_ref_no")];	
			$po_wise_export_arr[$row[csf("po_id")]]['buyer_name']=$row[csf("buyer_name")];
			$po_wise_export_arr[$row[csf("po_id")]]['ex_fac_qty']=$row[csf("ex_factory_qnty")]-$row[csf("ret_ex_factory_qnty")];	
			$po_wise_export_arr[$row[csf("po_id")]]['unit_price']=$row[csf("unit_price")];	
			$po_wise_export_arr[$row[csf("po_id")]]['po_quantity_pcs']=$row[csf("po_quantity_pcs")];
			$po_wise_export_arr[$row[csf("po_id")]]['po_quantity']=$row[csf("po_quantity")];	
			$po_wise_export_arr[$row[csf("po_id")]]['unit_price']=$row[csf("unit_price")];	
			$po_wise_export_arr[$row[csf("po_id")]]['total_set_qnty']=$row[csf("total_set_qnty")];
			$po_id_arr[$row[csf("po_id")]]=$row[csf("po_id")];
			
			if($all_full_job=="") $all_full_job="'".$row[csf('job_no')]."'"; else $all_full_job.=","."'".$row[csf('job_no')]."'";
		}
		
		if(empty($po_id_arr)) {echo "<h1>No Data Found In This Search Criteria.</h1>";die;}
		
		 $sqlex= "select sum(case when a.entry_form!=85 then a.ex_factory_qnty else 0 end) as ex_factory_qnty,
    sum(case when a.entry_form=85 then a.ex_factory_qnty else 0 end) as ret_ex_factory_qnty, a.po_break_down_id from pro_ex_factory_delivery_mst b, pro_ex_factory_mst a where a.delivery_mst_id =b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.source=3 and a.po_break_down_id in (".implode(",",$po_id_arr).") group by a.po_break_down_id";
	
		$ex_result=sql_select($sqlex);
		foreach($ex_result as $val)
		{
			$po_wise_export_arr[$val[csf("po_break_down_id")]]['outbound']=$val[csf("ex_factory_qnty")]-$val[csf("ret_ex_factory_qnty")];
		}
		

		$sql_pre = "select a.costing_per,b.job_no,b.cm_cost,b.freight,b.total_cost,b.comm_cost,b.commission,b.margin_pcs_set from wo_pre_cost_dtls b, wo_pre_cost_mst a
			where  a.job_no=b.job_no and b.status_active=1 and b.is_deleted=0";
			$pre_result=sql_select($sql_pre);
			foreach($pre_result as $row)
			{
				$others_cost_value=$row[csf("total_cost")]-$row[csf("cm_cost")]-$row[csf("freight")]-$row[csf("comm_cost")]-$row[csf("commission")];
				$job_wise_export_arr[$row[csf("job_no")]]['other_value']=$others_cost_value;
				$job_wise_export_arr[$row[csf("job_no")]]['costing_per']=$row[csf("costing_per")];
				$job_wise_export_arr[$row[csf("job_no")]]['margin_pcs_set']=$row[csf("margin_pcs_set")];
				$job_wise_export_arr[$row[csf("job_no")]]['cm_cost']=$row[csf("cm_cost")];	
			}
			unset($pre_result);
			
					
		?>
        <div style="width:1250px;">
                <table width="1000"  cellspacing="0"  align="center">
                    <tr>
                        <td align="center" colspan="9" class="form_caption">
                            <strong style="font-size:16px;">Company:<? echo  $company_library[$cbo_company_name] ;?></strong>
                        </td>
                    </tr>
                    <tr class="form_caption">
                        <td colspan="9" align="center" class="form_caption"> <strong style="font-size:15px;"><? echo $report_title;?></strong></td>
                    </tr>
                    <tr class="">
                        <td colspan="7" align="left" class=""> <strong style="font-size:15px;">
						<?
							if(str_replace("'","",$cbo_item_catgory)!="")
							{
								$cbo_item_catgory_arr=explode(",",str_replace("'","",$cbo_item_catgory));
								foreach($cbo_item_catgory_arr as $product_cat)
								{
									//$product_category_arr[$product_cat]=$product_category[$product_cat]
									$product_category_string.=$product_category[$product_cat].",";
								}
							}
							$product_category_string=chop($product_category_string,",");
						 	echo $product_category_string;
						 ?></strong></td>
           
                        <td colspan="2" align="center" class=""> <strong style="font-size:15px;">Month of <? echo $months[(date("m",strtotime($start_date))*1)];?></strong></td>
                    </tr>
                    </table>
               
                <table width="1070" border="1" class="rpt_table" rules="all" id="table_header_2">
                    <thead>
                        <th width="30">SL</th>
                        <th width="180">Buyer</th>
                        <th width="110">Order Qty (Pcs)</th>
                        <th width="110">Shipment Qty</th>
                        <th width="100">Short/Excess Shipment</th>
                        <th width="100">Short/Excess Shipment value</th>
                        <th width="100">Shipment FOB Value(USD)</th>
                        <th width="100">Ex-Factory CM Cost With Margin(USD)</th>
                        <th width="">Sub-contract Qty</th>
                    </thead>
                </table>
            <div style="width:1070px; overflow-y:scroll; overflow-x:hidden; max-height:300px;"  id="scroll_body" >
               <table class="rpt_table" width="1050" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
               <?
				$total_po_qty_pcs=$total_ex_fac_qty=$total_ex_fac_val=$ex_fac_cm_cost=$total_ex_fac_qty_cm_cost_mergin=$total_ex_fac_mergin=0;
			 	  $i=1;
				$buyer_wise_arr=array();
              	foreach($po_wise_export_arr as $po_id=>$row)
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$ex_fact_mergin_new_price=$job_wise_export_arr[$row[('job_no')]]['margin_pcs_set'];
					$costing_per=$job_wise_export_arr[$row[('job_no')]]['costing_per'];
					$cm_cost=$job_wise_export_arr[$row[('job_no')]]['cm_cost'];
					$order_value=$row['po_quantity']*$row['unit_price'];
						
					if($costing_per==1){$order_price_per_dzn=12;$costing_for=" DZN";}
					else if($costing_per==2){$order_price_per_dzn=1;$costing_for=" PCS";}
					else if($costing_per==3){$order_price_per_dzn=24;$costing_for=" 2 DZN";}
					else if($costing_per==4){$order_price_per_dzn=36;$costing_for=" 3 DZN";}
					else if($costing_per==5){$order_price_per_dzn=48;$costing_for=" 4 DZN";}
				
					$cm_mergin_pcs=$ex_fact_mergin_new_price;
					$cm_cost_pcs=$cm_cost/$order_price_per_dzn;
					$ex_fact_mergin=($cm_mergin_pcs+$cm_cost_pcs)*$row[('ex_fac_qty')];
					$jobQntryPcs=$job_wise_export_arr[$row[('job_no')]]['job_quantity'];
					$jobUnitPcs=$job_wise_export_arr[$row[('job_no')]]['avg_unit_price'];
					
					$buyer_wise_arr[$row['buyer_name']]['outbound']+=$po_wise_export_arr[$po_id]['outbound'];
					$buyer_wise_arr[$row['buyer_name']]['job_qty_pcs']+=$row['po_quantity_pcs'];
					$buyer_wise_arr[$row['buyer_name']]['ex_fac_qty']+=$row[('ex_fac_qty')];
					$buyer_wise_arr[$row['buyer_name']]['short_excess']+=($row['ex_fac_qty']-$row[('po_quantity_pcs')]);
					$buyer_wise_arr[$row['buyer_name']]['short_excess_value']+=($row['ex_fac_qty']-$row[('po_quantity_pcs')])*$row['unit_price'];
					$buyer_wise_arr[$row['buyer_name']]['exfactory_fob_value']+=($row[('ex_fac_qty')]*$row['unit_price']);
					$buyer_wise_arr[$row['buyer_name']]['exfactory_margin']+=$ex_fact_mergin;
					
					
				}
				
				//die;
				foreach($buyer_wise_arr as $buyer_id=>$buyer_data)
				{
					$short_excess_qty=0;
					$short_excess_value=0;
					$short_excess_qty=$buyer_data['short_excess'];
					$short_excess_value=$buyer_data['short_excess_value'];
					$exfactory_fob_value=$buyer_data['exfactory_fob_value'];
					$exfactory_margin_value=$buyer_data['exfactory_margin'];
					$exfactory_subcontact=$buyer_data['outbound'];
					
			   	?>
               		<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
							<td width="30"><? echo $i; ?></td>
							<td width="180"><? echo $buyer_arr[$buyer_id]; ?></td>
                            <td width="110"  align="right"><div style="word-break:break-all"><? echo number_format($buyer_data['job_qty_pcs'],0); ?></div></td>
                            <td width="110"  align="right" title="Shipment Qty"> <div style="word-break:break-all"> <? echo number_format($buyer_data['ex_fac_qty'],0); ?>   </div> </td>
							<td width="100" align="right" title="Job Qty-Shipment Qty"><div style="word-wrap:break-all">
								<? echo number_format($short_excess_qty,3); ?>
                            </div></td>
                            <td width="100"align="right"  title="<? ?>"><div style="word-break:break-all">
							<? 
							 	echo number_format($short_excess_value,2); 
							?>
                            </div></td>
							
							<td width="100" align="right" > <? 	echo number_format($exfactory_fob_value,2); ?> </td>
                            <td width="100" align="right"><div style="word-break:break-all" title="<?  ?>">
								<? echo number_format($exfactory_margin_value,2); ?> </div></td> 
							<td width="" align="right"><p>
							 <? echo number_format($exfactory_subcontact,2);?>
                             </p>
							</td>
                            
                   	</tr>
					<?
                        $total_po_qty_pcs+=$buyer_data['job_qty_pcs'];
                        $total_ex_fac_qty+=$buyer_data['ex_fac_qty'];
						$short_excess_qty_total+=$short_excess_qty;
						$exfactory_subcontact_total+=$exfactory_subcontact;
						$short_excess_value_total+=$short_excess_value;
						$exfactory_margin_value_total+=$exfactory_margin_value;
						$total_exfactory_fob_value+=$exfactory_fob_value;  
                        $i++;
				}
							?>
               </table>
            <table width="1050" cellspacing="0" cellpadding="0"  border="1"  class="rpt_table" rules="all" id="table_footer">
                <tfoot>
                    <tr>
                    	<th width="30">&nbsp;</th>
                        <th width="180">&nbsp;</th>
                        <th width="110" id="value_total_po_qty"><? echo number_format($total_po_qty_pcs,0); ?></th>
                        <th width="110" id="value_total_ex_fac_qty"><? echo number_format($total_ex_fac_qty,0); ?></th>
                        <th width="100" id="value_total_short_excess"><? echo number_format($short_excess_qty_total); ?></th>
                        <th width="100" id="value_total_short_excess_val"><? echo number_format($short_excess_value_total); ?></th>
                        <th width="100" id="value_total_fob"><? echo number_format($total_exfactory_fob_value); ?></th>
                        <th width="100" id="value_total_ex_fac_qty_cm_cost_mergin"><? echo number_format($exfactory_margin_value_total); ?></th>
                        <th id="value_total_ex_fac_outbound_qty"><? echo number_format($exfactory_subcontact_total); ?></th>
                    </tr>
                </tfoot>
            </table>
            </div>
            </div>
        </div>
	   
	<?
    echo signature_table(141,$cbo_company_name,"1000px",$cbo_template_id);
	
	foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename####$reportType";
	exit();
}

if($action=="ex_date_popup")
{
	echo load_html_head_contents("Report Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$ex_factory_date=str_replace("'","",$ex_factory_date);
	$ex_factory_date_ref=explode("_",$ex_factory_date);
	$exfact_date=explode("*",$ex_factory_date_ref[0]);
	//echo $ex_factory_date."***".$company_id."***".$order_id;
	$country_arr=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  );
	?>
	<div style="width:100%" align="center">
		<fieldset style="width:500px"> 
        <div class="form_caption" align="center"><strong>Ex-Factory Date Details</strong></div><br />
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                    <tr>
                        <th width="30">SL</th>
                        <th width="100">Date</th>
                        <th width="100">Challan</th>
                        <th width="100">Country</th>
                        <th width="100">Delv. Qty</th>
                        <th width="">Return Qty</th>
                     </tr>   
                </thead>
                <tbody>	 	
					<?
						$sql_res=sql_select("select b.po_break_down_id as po_id, 
						
						sum(CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END) as return_qnty 
						from  pro_ex_factory_mst b  where  b.status_active=1 and b.is_deleted=0 group by b.po_break_down_id");
						$ex_factory_qty_arr=array();
						foreach($sql_res as $row)
						{
						
							$ex_factory_qty_arr[$row[csf('po_id')]]['return_qty']=$row[csf('return_qnty')];
						}
						
						$i=1;
						if($ex_factory_date_ref[1]==2)
						{ 
							$sql_qnty="Select po_break_down_id,ex_factory_date,challan_no,country_id, 
							sum(CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
							sum(CASE WHEN entry_form=85 THEN ex_factory_qnty ELSE 0 END) as ex_factory_ret_qnty
							
							 from pro_ex_factory_mst where po_break_down_id=$order_id and status_active=1 and is_deleted=0 group by po_break_down_id,ex_factory_date,challan_no,country_id order by ex_factory_date ";
						}
						else
						{
							 $sql_qnty="Select po_break_down_id,ex_factory_date,challan_no,country_id, 
							
							sum(CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END) as ex_factory_qnty
							
							from pro_ex_factory_mst where po_break_down_id=$order_id and status_active=1 and is_deleted=0 and ex_factory_date between  '$exfact_date[0]' and '$exfact_date[1]' group by po_break_down_id,ex_factory_date,challan_no,country_id order by ex_factory_date ";
							
							/*$sql_qnty="Select c.ex_factory_date, sum(c.ex_factory_qnty) as ex_factory_qnty,c.challan_no,c.country_id 
							from wo_po_details_master a, wo_po_break_down b,  pro_ex_factory_mst c
							where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and a.company_name=$company_id and c.po_break_down_id=$order_id and c.status_active=1 and c.is_deleted=0 and c.ex_factory_date between  '$exfact_date[0]' and '$exfact_date[1]' 
							group by c.ex_factory_date,c.challan_no,c.country_id order by c.ex_factory_date ";*/
						}
						//echo $sql_qnty;
						$sql_dtls=sql_select($sql_qnty);
						foreach($sql_dtls as $row_real)
						{ 
							 if ($i%2==0) $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF"; 
							 if($ex_factory_date_ref[1]==2)
							 {
								 $return_qty=$row_real[csf("ex_factory_ret_qnty")];
							 }
							 else
							 {
								$return_qty=$ex_factory_qty_arr[$row_real[csf("po_break_down_id")]]['return_qty']; 
							 }
							  
							
							 ?>
								<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
									<td><? echo $i; ?></td> 
									<td  align="center"><? echo change_date_format($row_real[csf("ex_factory_date")]); ?></td>
                                    <td ><? echo $row_real[csf("challan_no")]; ?></td>
                                    <td ><? echo $country_arr[$row_real[csf("country_id")]]; ?></td>
									<td width="100" align="right"><? echo number_format($row_real[csf("ex_factory_qnty")]-$return_qty,2); ?>&nbsp;</td>
                                    <td width="" align="right"><? echo number_format($return_qty,2); ?>&nbsp;</td>
								</tr>
							<? 
							$total_ex_qnty+=$row_real[csf("ex_factory_qnty")];
							$total_return_ex_qnty+=$return_qty;
							$i++;
						}
                    ?>
                </tbody>
                <tfoot>
                	<tr>
                    	<th colspan="4" align="right"><strong>Total :</strong></th>
                        <th align="right"><? echo number_format($total_ex_qnty,2); ?></th>
                        <th align="right"><? echo number_format($total_return_ex_qnty,2); ?> </th>
                    </tr>
                    <tr>
                    	<th colspan="4" align="right"><strong>Total Balance:</strong></th>
                        <th align="right" colspan="2"><? echo number_format($total_ex_qnty-$total_return_ex_qnty,2); ?></th>
                        
                    </tr>
                </tfoot>
            </table>
        </fieldset>
    </div>    
    <?	
}
disconnect($con);
?>
