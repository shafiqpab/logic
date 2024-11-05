<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../../includes/common.php');

$user_name=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );     	 
	exit();
}

$color_array=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );
$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
$supplier_details=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
$buyer_short_name_library=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
$yarn_count_details=return_library_array( "select id, yarn_count from lib_yarn_count", "id", "yarn_count");

if($db_type==0)
{
	$fabric_desc_details=return_library_array( "select job_no, group_concat(distinct(fabric_description)) as fabric_description from wo_pre_cost_fabric_cost_dtls group by job_no", "job_no", "fabric_description");
}
else
{
	$fabric_desc_details=return_library_array( "select job_no, LISTAGG(cast(fabric_description as varchar2(4000)), ',') WITHIN GROUP (ORDER BY id) as fabric_description from wo_pre_cost_fabric_cost_dtls group by job_no", "job_no", "fabric_description");
}

$product_details=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details");
$batch_details=return_library_array( "select id, batch_no from pro_batch_create_mst", "id", "batch_no");

$costing_per_id_library=array(); $costing_date_library=array();
$costing_sql=sql_select("select job_no, costing_per, costing_date from wo_pre_cost_mst");
foreach($costing_sql as $row)
{
	$costing_per_id_library[$row[csf('job_no')]]=$row[csf('costing_per')]; 
	$costing_date_library[$row[csf('job_no')]]=$row[csf('costing_date')];
}

$receive_basis=array(0=>"Independent",1=>"Fabric Booking",2=>"Knitting Plan");//--------------------------------------------------------------------------------------------------------------------

$tmplte=explode("**",$data);

if ($tmplte[0]=="viewtemplate") $template=$tmplte[1]; else $template=$lib_report_template_array[$_SESSION['menu_id']]['0'];
if ($template=="") $template=1;

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
 	if($template==1)
	{
		$type = str_replace("'","",$cbo_type);
		$company_name= str_replace("'","",$cbo_company_name);
		if(str_replace("'","",$cbo_buyer_name)==0)
		{
			if ($_SESSION['logic_erp']["data_level_secured"]==1)
			{
				if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
			}
			else
			{
				$buyer_id_cond="";
			}
		}
		else
		{
			$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";
		}
		
		$cbo_discrepancy=str_replace("'","",trim($cbo_discrepancy));
		if($cbo_discrepancy==0) $discrepancy_td_color=""; else $discrepancy_td_color="#FF4F4F";
		$txt_search_string=str_replace("'","",$txt_search_string);
		
		if(trim($txt_search_string)!="") $search_string="%".trim($txt_search_string)."%"; else $search_string="%%";
		if(trim($txt_search_string)!="")
		{
			if($type==1)
			{
				
				if($db_type==0)
				{
					$po_style_src_cond=return_field_value("group_concat(id) as po_id","wo_po_break_down","po_number like '$search_string'","po_id");
				}
				else if($db_type==2)
				{
					$po_style_src_cond=return_field_value("LISTAGG(id, ',') WITHIN GROUP (ORDER BY id) as po_id","wo_po_break_down","po_number like '$search_string'","po_id");
				}
			}
			else
			{
				if($db_type==0)
				{
					$po_style_src_cond=return_field_value("group_concat(b.id) as po_id","wo_po_details_master a,wo_po_break_down b","a.job_no=b.job_no_mst and a.style_ref_no like '$search_string'","po_id");
				}
				else if($db_type==2)
				{
					$po_style_src_cond=return_field_value("LISTAGG(b.id, ',') WITHIN GROUP (ORDER BY b.id) as po_id","wo_po_details_master a,wo_po_break_down b","a.job_no=b.job_no_mst and a.style_ref_no like '$search_string'","po_id");
				}
			}
		}
		else
		{
			$po_style_src_cond="";
		}
		
		if($po_style_src_cond!="")
		{
			$yarn_iss_po_cond=" and a.po_breakdown_id in ($po_style_src_cond)";
			$grey_purchase_po_cond=" and c.po_breakdown_id in ($po_style_src_cond)";
			$grey_delivery_po_cond=" and order_id in ($po_style_src_cond)";
			$fin_delivery_po_cond=" and a.order_id in ($po_style_src_cond)";
			$trans_po_cond=" and po_breakdown_id in ($po_style_src_cond)";
			$fin_purchase_po_cond=" and c.po_breakdown_id in ($po_style_src_cond)";
			$po_color_po_cond=" and po_breakdown_id in ($po_style_src_cond)";
			$batch_po_cond=" and b.po_id in ($po_style_src_cond)";
			$dye_po_cond=" and b.po_id in ($po_style_src_cond)";
			$wo_po_cond=" and b.po_break_down_id in ($po_style_src_cond)";
			$sql_po_cond=" and b.id in ($po_style_src_cond)";
		}
		else
		{
			$yarn_iss_po_cond="";
			$grey_purchase_po_cond="";
			$grey_delivery_po_cond="";
			$fin_delivery_po_cond="";
			$trans_po_cond="";
			$fin_purchase_po_cond="";
			$po_color_po_cond="";
			$batch_po_cond="";
			$dye_po_cond="";
			$wo_po_cond="";
			$sql_po_cond="";
		}
		
		//echo $po_style_src_cond; die;
		$start_date=str_replace("'","",trim($txt_date_from));
		$end_date=str_replace("'","",trim($txt_date_to));
		
		if($start_date!="" && $end_date!="")
		{
			$str_cond="and b.pub_shipment_date between '$start_date' and '$end_date'";
		}
		else
			$str_cond="";
			
		$start_date_rec=str_replace("'","",trim($txt_date_from_rec));
		$end_date_rec=str_replace("'","",trim($txt_date_to_rec));
		
		if($start_date_rec!="" && $end_date_rec!="")
		{
			$date_rec_cond="and b.po_received_date between '$start_date_rec' and '$end_date_rec'";
		}
		else
			$date_rec_cond="";	
		
		$txt_job_no=str_replace("'","",$txt_job_no);
		$job_no_cond="";
		if(trim($txt_job_no)!="")
		{
			$job_no=trim($txt_job_no); 
			$job_no_cond=" and a.job_no_prefix_num=$job_no";
		}
		
		$cbo_year=str_replace("'","",$cbo_year);
		if(trim($cbo_year)!=0) 
		{
			if($db_type==0) $year_cond=" and YEAR(a.insert_date)=$cbo_year";
			else if($db_type==2) $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";
			else $year_cond="";
		}
		else $year_cond="";
		
		$cbo_shipping_status=str_replace("'","",$cbo_shipping_status);
		if(trim($cbo_shipping_status)==0) $shipping_status="%%"; else $shipping_status=$cbo_shipping_status;
		
		$txt_fab_color=str_replace("'","",$txt_fab_color);
		if(trim($txt_fab_color)!="") $fab_color="%".trim($txt_fab_color)."%"; else $fab_color="%%";
		
		$start_date_po=str_replace("'","",trim($txt_date_from_po));
		$end_date_po=str_replace("'","",trim($txt_date_to_po));
		
		if($end_date_po=="") 
			$end_date_po=$start_date_po; 
		else 
			$end_date_po=$end_date_po;
		
		if($start_date_po!="" && $end_date_po!="")
		{
			if($db_type==0)
			{
				$str_cond_insert=" and b.insert_date between '".$start_date_po."' and '".$end_date_po." 23:59:59'";
			}
			else
			{
				$str_cond_insert=" and b.insert_date between '".$start_date_po."' and '".$end_date_po." 11:59:59 PM'";
			}
		}
		else
			$str_cond_insert="";
		
		if($txt_fab_color=="")
		{
			$color_cond="";	
			$color_cond_prop="";	
		}
		else
		{
			if($db_type==0)
			{
				$color_id=return_field_value("group_concat(id) as color_id","lib_color","color_name like '$fab_color'","color_id");
			}
			else
			{
				$color_id=return_field_value("LISTAGG(id, ',') WITHIN GROUP (ORDER BY id) as color_id","lib_color","color_name like '$fab_color'","color_id");
			}
			if($color_id=="") 
			{
				$color_cond_search=""; 
				$color_cond_prop=""; 
			}
			else
			{
				$color_cond_search=" and b.fabric_color_id in ($color_id)";
				$color_cond_prop=" and color_id in ($color_id)";
			}
		}
		
		$dataArrayYarn=array(); $dataArrayYarnIssue=array(); $greyPurchaseQntyArray=array();
		$yarn_sql="select job_no, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, sum(cons_qnty) as qnty, avg(rate) as rate, sum(amount) as amount from wo_pre_cost_fab_yarn_cost_dtls where status_active=1 and is_deleted=0 group by job_no, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id";
		$resultYarn=sql_select($yarn_sql);
		foreach($resultYarn as $yarnRow)
		{
			$dataArrayYarn[$yarnRow[csf('job_no')]].=$yarnRow[csf('count_id')]."**".$yarnRow[csf('copm_one_id')]."**".$yarnRow[csf('percent_one')]."**".$yarnRow[csf('copm_two_id')]."**".$yarnRow[csf('percent_two')]."**".$yarnRow[csf('type_id')]."**".$yarnRow[csf('qnty')]."**".$yarnRow[csf('amount')]."**".$yarnRow[csf('rate')].",";
		}
		
		
		$yarn_amounr_arr=array();
		$yarn_sql_am=sql_select("select po_break_down_id, sum(yarn_amount) as yarn_amount from wo_bom_process group by po_break_down_id");
		foreach($yarn_sql_am as $yarn_row_am)
		{
		$yarn_amounr_arr[$yarn_row_am[csf('po_break_down_id')]]	=$yarn_row_am[csf('yarn_amount')];
		}
		
		
		
		
		$sql_yarn_iss="select a.po_breakdown_id, b.yarn_count_id, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type, 
				sum(CASE WHEN a.entry_form ='3' THEN a.quantity ELSE 0 END) AS issue_qnty,
				sum(CASE WHEN a.entry_form ='9' THEN a.quantity ELSE 0 END) AS return_qnty
				from order_wise_pro_details a, product_details_master b where a.prod_id=b.id and b.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and a.issue_purpose!=2 $yarn_iss_po_cond group by a.po_breakdown_id, b.yarn_count_id, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type";
		$dataArrayIssue=sql_select($sql_yarn_iss);
		foreach($dataArrayIssue as $row_yarn_iss)
		{
			$dataArrayYarnIssue[$row_yarn_iss[csf('po_breakdown_id')]].=$row_yarn_iss[csf('yarn_count_id')]."**".$row_yarn_iss[csf('yarn_comp_type1st')]."**".$row_yarn_iss[csf('yarn_comp_percent1st')]."**".$row_yarn_iss[csf('yarn_comp_type2nd')]."**".$row_yarn_iss[csf('yarn_comp_percent2nd')]."**".$row_yarn_iss[csf('yarn_type')]."**".$row_yarn_iss[csf('issue_qnty')]."**".$row_yarn_iss[csf('return_qnty')].",";
		}
		
		$dataArrayWo=array();
		$sql_wo="select b.po_break_down_id, a.id, a.booking_no, a.insert_date, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.booking_no_prefix_num, a.job_no, a.is_short, a.is_approved, b.fabric_color_id, sum(b.fin_fab_qnty) as req_qnty, sum(b.grey_fab_qnty) as grey_req_qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(2,13) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $color_cond_search $wo_po_cond group by b.po_break_down_id, a.id, a.booking_no, a.insert_date, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.booking_no_prefix_num, a.job_no, a.is_short, a.is_approved, b.fabric_color_id";
		$resultWo=sql_select($sql_wo);
		foreach($resultWo as $woRow)
		{
			$dataArrayWo[$woRow[csf('po_break_down_id')]].=$woRow[csf('id')]."**".$woRow[csf('booking_no')]."**".$woRow[csf('insert_date')]."**".$woRow[csf('item_category')]."**".$woRow[csf('fabric_source')]."**".$woRow[csf('company_id')]."**".$woRow[csf('booking_type')]."**".$woRow[csf('booking_no_prefix_num')]."**".$woRow[csf('job_no')]."**".$woRow[csf('is_short')]."**".$woRow[csf('is_approved')]."**".$woRow[csf('fabric_color_id')]."**".$woRow[csf('req_qnty')]."**".$woRow[csf('grey_req_qnty')].",";
		}
		
		$tot_order_qnty=0; 
		$tot_mkt_required=0; 
		$tot_yarn_issue_qnty=0; 
		$tot_balance=0; 
		$tot_fabric_req=0; 
		$tot_grey_recv_qnty=0; 
		$tot_grey_balance=0; 
		$tot_grey_available=0; 
		$tot_grey_issue=0; 
		$tot_batch_qnty=0; 
		$tot_color_wise_req=0; 
		$tot_dye_qnty=0; 
		$tot_fabric_recv=0; 
		$tot_fabric_purchase=0; 
		$tot_fabric_balance=0; 
		$tot_issue_to_cut_qnty=0;
		$tot_fabric_available=0; 
		$tot_fabric_left_over=0; 
		$tot_fabric_left_over_excel=0; 
		$tot_fabric_recv_excel=0;
		$tot_batch_qnty_excel=0;
		$tot_grey_prod_balance=0;
		$total_grey_del_store=0;
		$tot_net_trans_knit_qnty=0;
		
		$buyer_name_array= array(); 
		$order_qty_array= array(); 
		$grey_required_array= array(); 
		$yarn_issue_array= array(); 
		$grey_issue_array= array(); 
		$fin_fab_Requi_array= array(); 
		$fin_fab_recei_array= array(); 
		$issue_to_cut_array= array(); 
		$yarn_balance_array= array(); 
		$grey_balance_array= array(); 
		$fin_balance_array= array(); 
		$knitted_array=array(); 
		$dye_qnty_array=array(); 
		$batch_qnty_array=array();

		if($type==1)
		{
			$table_width="2060"; $colspan="12";
			$sql="select a.company_name, a.buyer_name, a.job_no_prefix_num, a.job_no, a.style_ref_no, a.gmts_item_id, a.order_uom, a.total_set_qnty as ratio, b.id as po_id, b.po_number, b.po_quantity as po_qnty, b.pub_shipment_date, b.shiping_status, b.insert_date, b.po_received_date, b.plan_cut from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name='$company_name' and b.shiping_status like '$shipping_status' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $buyer_id_cond $str_cond $str_cond_insert $date_rec_cond $year_cond $job_no_cond $sql_po_cond order by b.pub_shipment_date, b.id";	
		
		}
		
		$template_id_arr=return_library_array("select po_number_id, template_id from tna_process_mst group by po_number_id, template_id","po_number_id","template_id");
		ob_start();
		?>
        <fieldset style="width:<? echo $table_width+30; ?>px;">	
            <table cellpadding="0" cellspacing="0" width="2060">
                <tr>
                   <td align="center" width="100%" colspan="<? echo $colspan+26; ?>" style="font-size:16px"><strong><?php echo $company_library[$company_name]; ?></strong></td>
                </tr>
                <tr>
                   <td align="center" width="100%" colspan="<? echo $colspan+26; ?>" style="font-size:16px"><strong><? if($start_date!="" && $end_date!="") echo "From ". change_date_format($start_date). " To ". change_date_format($end_date);?></strong></td>
                </tr>
            </table>
            <table class="rpt_table" border="1" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="table_header_1">
                <thead>
                    <tr>
                        <th rowspan="3" width="40">SL</th>
                        <th colspan="<? echo $colspan; ?>" rowspan="2">Order Details</th>
						
                        <th colspan="8">Yarn Status</th>
						 
                    </tr>
                    <tr>
                       
                       
                        <th width="70" colspan="3">Yarn Description</th>
                        <th width="100" colspan="2">Budget</th>
                        <th width="100">Fab Booking</th>
                        <th width="100" colspan="2">Actual</th>
                     
                    </tr>
                    <tr>
                        <th width="125">Main Fabric Booking No</th>
                        <th width="125">Sample Fabric Booking No</th>
                        <th width="100">Job Number</th>
                        <th width="120">Order Number</th>
                        <th width="80">Buyer Name</th>
                        <th width="130">Style Ref.</th>
                        <th width="140">Item Name</th>
                        <th width="100">Order Qnty</th>
                        <th width="80">Shipment Date</th>
                        <?
						if($type==1)
						{
						?>
                            <th width="80">PO Received Date</th>
                            <th width="80">PO Entry Date</th>
                            <th width="100">Shipping Status 
                                <select name="cbo_shipping_status" id="cbo_shipping_status" class="combo_boxes" style="width:80px" onchange="fn_report_generated(2);">
                                    <?
                                    foreach($shipment_status as $key=>$value)
                                    {
                                    ?>
                                        <option value=<? echo $key; if ($key==$cbo_shipping_status){?> selected <?php }?>><? echo "$value" ?> </option>
                                    <?
                                    }
                                    ?>
                                </select> 
                            </th>
                        <?
						}
						?>
                        <th width="70">Count</th>
                        <th width="110">Composition</th>
                        <th width="80">Type</th>
                        <th width="100">Qty<br/><font style="font-size:9px; font-weight:100">(As Per Pre-Cost)</font></th>
                        <th width="100">Cost</th>
                        <th width="100">Qty</th>
                        <th width="100">Issued Qty</th>
                        <th width="100">Cost</th>
                     
                    </tr>
                </thead>
            </table>
          
            <div style="width:<? echo $table_width+20; ?>px; overflow-y:scroll; max-height:400px" id="scroll_body">
                <table width="<? echo $table_width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body">
                <? 
				$nameArray=sql_select($sql);
				$k=1; $i=1; 
				if($type==1)
				{
					foreach($nameArray as $row)
					{
						$template_id=$template_id_arr[$row[csf('po_id')]];
						
						$order_qnty_in_pcs=$row[csf('po_qnty')]*$row[csf('ratio')];
						$plan_cut_qnty=$row[csf('plan_cut')]*$row[csf('ratio')];
						$order_qty_array[$row[csf('buyer_name')]]+=$order_qnty_in_pcs;
						
						$gmts_item='';
						$gmts_item_id=explode(",",$row[csf('gmts_item_id')]);
						foreach($gmts_item_id as $item_id)
						{
							if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
						}
						
						$dzn_qnty=0; 
						$balance=0; 
						$job_mkt_required=0; 
						$yarn_issued=0;
						
						if($costing_per_id_library[$row[csf('job_no')]]==1) $dzn_qnty=12;
						else if($costing_per_id_library[$row[csf('job_no')]]==3) $dzn_qnty=12*2;
						else if($costing_per_id_library[$row[csf('job_no')]]==4) $dzn_qnty=12*3;
						else if($costing_per_id_library[$row[csf('job_no')]]==5) $dzn_qnty=12*4;
						else $dzn_qnty=1;
						
						$dzn_qnty=$dzn_qnty*$row[csf('ratio')];

						$yarn_data_array=array(); 
						$mkt_required_array=array(); 
						$mkt_amount_array=array(); 
						$yarn_desc_array_for_popup=array(); 
						$yarn_desc_array=array(); 
						$yarn_iss_qnty_array=array(); 
						
						
						
						
						
						$s=1;
						$dataYarn=explode(",",substr($dataArrayYarn[$row[csf('job_no')]],0,-1));
						foreach($dataYarn as $yarnRow)
						{
							$yarnRow=explode("**",$yarnRow);
							$count_id=$yarnRow[0];
							$copm_one_id=$yarnRow[1];
							$percent_one=$yarnRow[2];
							$copm_two_id=$yarnRow[3];
							$percent_two=$yarnRow[4];
							$type_id=$yarnRow[5];
							$qnty=$yarnRow[6];
							$yamount=$yarnRow[7];
							
							$mkt_required=$plan_cut_qnty*($qnty/$dzn_qnty);
							$mkt_required_array[$s]=$mkt_required;
							$mkt_amount=$plan_cut_qnty*($yamount/$dzn_qnty);
							$mkt_amount_array[$s]=$mkt_amount;
							$job_mkt_required+=$mkt_required;
							
							$yarn_data_array['count'][$s]=$yarn_count_details[$count_id];
							$yarn_data_array['type'][$s]=$yarn_type[$type_id];
							
							if($percent_two!=0)
							{
								$compos=$composition[$copm_one_id]." ".$percent_one." %"." ".$composition[$copm_two_id]." ".$percent_two." %";
							}
							else
							{
								$compos=$composition[$copm_one_id]." ".$percent_one." %"." ".$composition[$copm_two_id];
							}

							$yarn_data_array['comp'][]=$compos;
							
							$yarn_desc_array[$s]=$yarn_count_details[$count_id]." ".$compos." ".$yarn_type[$type_id];
							
							$yarn_desc_for_popup=$count_id."__".$copm_one_id."__".$percent_one."__".$copm_two_id."__".$percent_two."__".$type_id;
							$yarn_desc_array_for_popup[$s]=$yarn_desc_for_popup;
							
							$s++;
						}
						
						
						
						
						$dataYarnIssue=explode(",",substr($dataArrayYarnIssue[$row[csf('po_id')]],0,-1));
						foreach($dataYarnIssue as $yarnIssueRow)
						{
							$yarnIssueRow=explode("**",$yarnIssueRow);
							$yarn_count_id=$yarnIssueRow[0];
							$yarn_comp_type1st=$yarnIssueRow[1];
							$yarn_comp_percent1st=$yarnIssueRow[2];
							$yarn_comp_type2nd=$yarnIssueRow[3];
							$yarn_comp_percent2nd=$yarnIssueRow[4];
							$yarn_type_id=$yarnIssueRow[5];
							$issue_qnty=$yarnIssueRow[6];
							$return_qnty=$yarnIssueRow[7];
							
							if($yarn_comp_percent2nd!=0)
							{
								$compostion_not_req=$composition[$yarn_comp_type1st]." ".$yarn_comp_percent1st." % ".$composition[$yarn_comp_type2nd]." ".$yarn_comp_percent2nd." %";
							}
							else
							{
								$compostion_not_req=$composition[$yarn_comp_type1st]." ".$yarn_comp_percent1st." % ".$composition[$yarn_comp_type2nd];
							}
					
							$desc=$yarn_count_details[$yarn_count_id]." ".$compostion_not_req." ".$yarn_type[$yarn_type_id];
							
							$net_issue_qnty=$issue_qnty-$return_qnty;
							$yarn_issued+=$net_issue_qnty;
							if(!in_array($desc,$yarn_desc_array))
							{
								$yarn_iss_qnty_array['not_req']+=$net_issue_qnty;
							}
							else
							{
								$yarn_iss_qnty_array[$desc]+=$net_issue_qnty;
							}
						}
						
						
						
						
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$buyer_name_array[$row[csf('buyer_name')]]=$buyer_short_name_library[$row[csf('buyer_name')]];
							
							$booking_array=array(); 
							$color_data_array=array();
							$required_qnty=0; 
							$main_booking=''; 
							$sample_booking=''; 
							$main_booking_excel=''; 
							$sample_booking_excel='';
							$dataArray=array_filter(explode(",",substr($dataArrayWo[$row[csf('po_id')]],0,-1)));
							
							if(count($dataArray)>0)
							{
								foreach($dataArray as $woRow)
								{
									$woRow=explode("**",$woRow);
									$id=$woRow[0];
									$booking_no=$woRow[1];
									$insert_date=$woRow[2];
									$item_category=$woRow[3];
									$fabric_source=$woRow[4];
									$company_id=$woRow[5];
									$booking_type=$woRow[6];
									$booking_no_prefix_num=$woRow[7];
									$job_no=$woRow[8];
									$is_short=$woRow[9];
									$is_approved=$woRow[10];
									$fabric_color_id=$woRow[11];
									$req_qnty=$woRow[12];
									$grey_req_qnty=$woRow[13];
									
									$required_qnty+=$grey_req_qnty;
		
									if(!in_array($id,$booking_array))
									{
										$system_date=date('d-M-Y', strtotime($insert_date));
										
										if($booking_type==4)
										{
											$sample_booking.="<a href='##' style='color:#000' onclick=\"generate_worder_report('3','".$booking_no."','".$company_id."','".$row[csf('po_id')]."','".$item_category."','".$fabric_source."','".$job_no."','".$is_approved."')\"><font style='font-weight:bold' color='$wo_color;'>".$booking_no."(".$system_date.")"."</font></a><br>";
											$sample_booking_excel.="<font style='font-weight:bold' color='$wo_color;'>".$booking_no."(".$system_date.")"."</font><br>";
										}
										else
										{
											if($is_short==1) $pre="S"; else $pre="M"; 
											
											$main_booking.="<a href='##' style='color:#000' onclick=\"generate_worder_report('".$is_short."','".$booking_no."','".$company_id."','".$row[csf('po_id')]."','".$item_category."','".$fabric_source."','".$job_no."','".$is_approved."')\"><font style='font-weight:bold' color='$wo_color;'>".$booking_no."(".$system_date.")".$pre."</font></a><br>";
											$main_booking_excel.="<font style='font-weight:bold' color='$wo_color;'>".$booking_no."(".$system_date.")".$pre."</font><br>";
										}
										
										$booking_array[]=$id;
									}
									$color_data_array[$fabric_color_id]+=$req_qnty;
								}
							}
							else
							{
								$main_booking.="No Booking";
								$main_booking_excel.="No Booking";
								$sample_booking.="No Booking";
								$sample_booking_excel.="No Booking";
							}
							
							if($main_booking=="")
							{
								$main_booking.="No Booking";
								$main_booking_excel.="No Booking";
							}
							
							if($sample_booking=="") 
							{
								$sample_booking.="No Booking";
								$sample_booking_excel.="No Booking";
							}
							
							
							$yarn_issue_array[$row[csf('buyer_name')]]+=$yarn_issued;
							
							$grey_required_array[$row[csf('buyer_name')]]+=$required_qnty;

							$net_trans_yarn=$trans_qnty_arr[$row[csf('po_id')]]['yarn_trans'];
							$yarn_issue_array[$row[csf('buyer_name')]]+=$net_trans_yarn;
							
							$balance=$required_qnty-($yarn_issued+$net_trans_yarn);
							
							$yarn_balance_array[$row[csf('buyer_name')]]+=$balance;
							
							$knitted_array[$row[csf('buyer_name')]]+=$grey_recv_qnty+$grey_purchase_qnty;
							
							$net_trans_knit=$trans_qnty_arr[$row[csf('po_id')]]['knit_trans'];
							$knitted_array[$row[csf('buyer_name')]]+=$net_trans_knit;
							
							$grey_balance=$required_qnty-($grey_recv_qnty+$net_trans_knit+$grey_purchase_qnty);
							$grey_prod_balance=$required_qnty-$grey_recv_qnty;
							$grey_del_store=$greyDeliveryArray[$row[csf('po_id')]];
							$total_grey_del_store+=$grey_del_store;
							
							$grey_balance_array[$row[csf('buyer_name')]]+=$grey_balance;
							
							$grey_issue_array[$row[csf('buyer_name')]]+=$grey_fabric_issue;
							
							$grey_available=$grey_recv_qnty+$grey_purchase_qnty+$net_trans_knit;
							$tot_order_qnty+=$order_qnty_in_pcs;
							$tot_mkt_required+=$job_mkt_required;
							$tot_yarn_issue_qnty+=$yarn_issued;
							$tot_fabric_req+=$required_qnty;
							$tot_balance+=$balance;
							$tot_grey_recv_qnty+=$grey_recv_qnty;
							$tot_grey_purchase_qnty+=$grey_purchase_qnty;
							$tot_grey_balance+=$grey_available;
							$tot_grey_prod_balance+=$grey_prod_balance;
							$tot_grey_issue+=$grey_fabric_issue;
							
							
							$tot_grey_available+=$grey_available;
					
							if($required_qnty>$job_mkt_required) $bgcolor_grey_td='#FF0000'; $bgcolor_grey_td='';
							
							$po_entry_date=date('d-m-Y', strtotime($row[csf('insert_date')]));
							$costing_date=$costing_date_library[$row[csf('job_no')]];
							
							
								?>
									<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
                                    	<td width="40"><? echo $display_font_color.$i.$font_end; ?></td>
                                        <td width="125"><? echo $display_font_color.$main_booking.$font_end; ?></td>
										<td width="125"><? echo $display_font_color.$sample_booking.$font_end; ?></td>
                                    	<td width="100" align="center"><? echo $display_font_color.$row[csf('job_no')].$font_end; ?></td>
										<td width="120">
                                        	<p>
												<a href='#report_details' onclick="progress_comment_popup('<? echo $row[csf('job_no')]; ?>','<? echo $row[csf('po_id')]; ?>','<? echo $template_id; ?>','<? echo $tna_process_type; ?>');"><? echo $display_font_color.$row[csf('po_number')].$font_end;  ?></a>
                                        	</p>
                                        </td>
										<td width="80"><p><? echo $display_font_color.$buyer_short_name_library[$row[csf('buyer_name')]].$font_end; ?></p></td>
										<td width="130"><p><? echo $display_font_color.$row[csf('style_ref_no')].$font_end; ?></p></td>
										<td width="140"><p><? echo $display_font_color.$gmts_item.$font_end; ?></p></td>
										<td width="100" align="right"><? echo number_format($order_qnty_in_pcs,0,'.',''); ?></td>
										<td width="80" align="center"><? echo $display_font_color.change_date_format($row[csf('pub_shipment_date')]).$font_end; ?></td>
										<td width="80" align="center"><? echo $display_font_color.change_date_format($row[csf('po_received_date')]).$font_end; ?></td>
										<td width="80" align="center"><? echo $display_font_color.$po_entry_date.$font_end; ?></td>
										<td width="100" align="center"><? echo $display_font_color.$shipment_status[$row[csf('shiping_status')]].$font_end; ?></td>
                                        <td width="70">
											<? 
												 $d=1;
												 foreach($yarn_data_array['count'] as $yarn_count_value)
												 {
													if($d!=1)
													{
														echo $display_font_color."<hr/>".$font_end;
													}
													
													echo $display_font_color.$yarn_count_value.$font_end;
													
												 $d++;
												 }
												
											?>
										</td>
										<td width="110">
											<div style="word-wrap:break-word; width:110px">
												<? 
													 $d=1;
													 foreach($yarn_data_array['comp'] as $yarn_composition_value)
													 {
														if($d!=1)
														{
															echo $display_font_color."<hr/>".$font_end;
														}
														echo $display_font_color.$yarn_composition_value.$font_end;
														
													 $d++;
													 }
													
												?>
											</div>
										</td>
										<td width="80">
											<p>
												<? 
													 $d=1;
													 foreach($yarn_data_array['type'] as $yarn_type_value)
													 {
														if($d!=1)
														{
															echo $display_font_color."<hr/>".$font_end;
															
														}
														
														echo $display_font_color.$yarn_type_value.$font_end; 
														
													 $d++;
													 }
													 
												?>
											</p>
										</td>
                                        <td width="100" align="right">
                                        <!--<a href="##" onclick="openmypage('<? echo $row[csf('po_id')]; ?>','yarn_req')"><? echo number_format(array_sum($mkt_required_array),2);?></a>-->
                                        <? echo number_format(array_sum($mkt_required_array),2,'.','');?>
											<? 
												
													
													
													/*$d=1; 
													$mkt_required_value_tot=0;
													foreach($mkt_required_array as $mkt_required_value)
													{
														if($d!=1)
														{
															echo "<hr/>";
														}
														$yarn_desc_for_popup_req=explode("__",$yarn_desc_array_for_popup[$d]);
														?>
														<a href="##" onclick="openmypage('<? echo $row[csf('po_id')]; ?>','yarn_req','<? echo $yarn_desc_for_popup_req[0]; ?>','<? echo $yarn_desc_for_popup_req[1]; ?>','<? echo $yarn_desc_for_popup_req[2]; ?>','<? echo $yarn_desc_for_popup_req[3]; ?>','<? echo $yarn_desc_for_popup_req[4]; ?>','<? echo $yarn_desc_for_popup_req[5]; ?>')"><? echo number_format($mkt_required_value,2,'.','');?></a>
													<?
													$d++;
													$mkt_required_value_tot+=$mkt_required_value;
													}*/
												
												
											?>
                                          
										</td>
                                      
                                        <td width="100" align="right">
                                        
										<? 
											//echo $yarn_amounr_arr[$row[csf('po_id')]]."<br/>";
											echo number_format(array_sum($mkt_amount_array),4,'.','');
										?>
										</td>
                                        <? 
										if(number_format(array_sum($mkt_required_array),2,'.','') < number_format($required_qnty,2,'.',''))
										{
											$bgb="#FF0000";
										} 
										else
										{ 
											$bgb="#FFFFFF";
										} 
										?>
                                        <td width="100" align="right"  bgcolor="<? echo $bgb;  ?>">
										<? 
											
												echo number_format($required_qnty,2,'.',''); 
											
										?>
										</td>
                                        <? 
										if(number_format($yarn_issued,2,'.','') > number_format($required_qnty,2,'.',''))
										{
											$bgs="#FF0000";
										} 
										else
										{ 
											$bgs="#FFFFFF";
										} 
										?>
                                        <td width="100" align="right" bgcolor="<? echo $bgs;  ?>">
                                        
											<? 
												echo number_format($yarn_issued,2,'.','');	
													/*$d=1;
													foreach($yarn_desc_array as $yarn_desc)
													{
														if($d!=1)
														{
															echo "<hr/>";
															
														}
														
														$yarn_iss_qnty=$yarn_iss_qnty_array[$yarn_desc];
														$yarn_desc_for_popup=explode("__",$yarn_desc_array_for_popup[$d]);
														
														?>
														<a href="##" onclick="openmypage('<? echo $row[csf('po_id')]; ?>','yarn_issue','<? echo $yarn_desc_for_popup[0]; ?>','<? echo $yarn_desc_for_popup[1]; ?>','<? echo $yarn_desc_for_popup[2]; ?>','<? echo $yarn_desc_for_popup[3]; ?>','<? echo $yarn_desc_for_popup[4]; ?>','<? echo $yarn_desc_for_popup[5]; ?>')"><? echo number_format($yarn_iss_qnty,2,'.','');?></a>
														<?
														
														$d++;
													}
													
													if($d!=1)
													{
														echo "<hr/>";
														
													}
													
													$yarn_desc=join(",",$yarn_desc_array);
													
													$iss_qnty_not_req=$yarn_iss_qnty_array['not_req'];*/
													?>
													<!--<a href="##" onclick="openmypage('<? echo $row[csf('po_id')]; ?>','yarn_issue_not','<? echo $yarn_desc; ?>','','','','','')"><? echo number_format($iss_qnty_not_req,2);?></a>-->
												
										</td>
                                        <td width="100" align="right">
										<? 
												echo number_format($required_qnty,2,'.',''); 
										?>
										</td>
                                    </tr>
								<?	
								$k++;
						$i++;	
					}// end main query  
				}//type ==1
				
				?>
                </table>
            </div>
           
            <table width="2060" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                <tfoot>
                    <tr>
                        <th width="40">&nbsp;</th>
                        <th width="125">&nbsp;</th>
                        <th width="125">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="120">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="130">&nbsp;</th>
                        <th width="140">Total</th>
                        <th width="100" id="value_tot_order_qnty"><? echo number_format($tot_order_qnty,0); ?></th>
                        <th width="80">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                        <th width="110">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="100" id="value_tot_mkt_required"><? echo number_format($tot_mkt_required,2); ?></th>
                        <th width="100" id="value_tot_mkt_cost"><? //echo number_format($tot_net_trans_yarn_qnty,2); ?></th>
                        <th width="100" id="value_tot_fabric_req"><? echo number_format($tot_fabric_req,2); ?></th>
                        <th width="100" id="value_tot_yarn_issue"><? echo number_format($tot_yarn_issue_qnty,2); ?></th>
                        <th width="100" id="value_tot_yarn_balance"><? echo number_format($tot_balance,2); ?></th>
                        
                        
                      
                    </tr>
                </tfoot>
            </table>
        </fieldset>
	<?
	}
	
/**
	foreach (glob("../../../ext_resource/tmp_report/$user_name*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename="../../../ext_resource/tmp_report/".$user_name."_".$name.".xls";
	$filename_short="../../../ext_resource/tmp_report/".$user_name."_".$name."short.xls";
	$create_new_doc = fopen($filename, 'w');
	$create_new_doc_short = fopen($filename_short, 'w');	
	$is_created = fwrite($create_new_doc,$html);
	$is_created_short = fwrite($create_new_doc_short,$html_short);
	$filename="../../ext_resource/tmp_report/".$user_name."_".$name.".xls";
	$filename_short="../../ext_resource/tmp_report/".$user_name."_".$name."short.xls";
	echo "$total_data####$filename####$filename_short####$html";
**/
	foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_name."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_name."_".$name.".xls";
	$filename_short=$user_name."_".$name."short.xls";
	echo "$total_data####$filename####$filename_short####$html";
	exit();
}
























if($action=="Shipment_date")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?>
<div align="center">
<fieldset style="width:670px">
	<table border="1" class="rpt_table" cellpadding="0" cellspacing="0" rules="all" width="640">
		<thead>
        	<tr>
            	<th colspan="6">Order Details</th>
            </tr>
            <tr>
                <th width="130">PO No</th>
                <th width="120">PO Qnty</th>
                <th width="90">Shipment Date</th>
                <th width="90">PO Receive Date</th>
                <th width="90">PO Entry Date</th>
                <th>Shipping Status</th>
        	</tr>
        </thead>
		<?
        $i=1; $total_order_qnty=0;
        $sql="select a.job_no, a.total_set_qnty as ratio, b.id as po_id, b.po_number, b.po_quantity as po_qnty, b.pub_shipment_date, b.shiping_status, b.insert_date, b.po_received_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in(".str_replace("'","",$order_id).") order by b.pub_shipment_date, b.id";
        $result=sql_select($sql);
        foreach($result as $row)
        {
			if ($i%2==0)  
				$bgcolor="#E9F3FF";
			else
				$bgcolor="#FFFFFF";
				
			$order_qnty=$row[csf('po_qnty')]*$row[csf('ratio')];
			$total_order_qnty+=$order_qnty;
			?>
			<tr bgcolor="<? echo $bgcolor; ?>">
                <td width="130"><p><? echo $row[csf('po_number')]; ?></p> </td>
                <td width="120" align="right"><? echo number_format($order_qnty,0);; ?></td>
                <td width="90" align="center"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
                <td width="90" align="center"><? echo change_date_format($row[csf('po_received_date')]); ?></td>
                <td width="90" align="center"><? echo date('d-m-Y', strtotime($row[csf('insert_date')])); ?></td>
				<td><? echo $shipment_status[$row[csf('shiping_status')]]; ?></td>
            </tr>
		<?
        $i++;
        }
        ?>
        <tfoot>
            <th>Total</th>
        	<th><? echo number_format($total_order_qnty,2);?></th>
            <th></th>
         	<th></th>
          	<th></th>
            <th></th>
        </tfoot>
    </table>
</fieldset>  
</div> 
<?
exit();
}

if($action=="yarn_req")
{
	echo load_html_head_contents("Report Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
?>
<script>

	function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="230px";
	}	
	
</script>	
	<div style="width:1250px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:1145px; margin-left:10px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="1210" cellpadding="0" cellspacing="0">
            	<thead>
					<th colspan="13"><b>Required Qnty Info</b></th>
				</thead>
				<thead>
                    <th width="40">SL</th>
                    <th width="120">Order No.</th>
                    <th width="120">Buyer Name</th>
                    <th width="90">Cons/Dzn</th>
                    <th width="110">Order Qnty</th>
                    <th width="110">Plan Cut Qnty</th>
                    <th width="50">Count</th>
                    <th width="100">Composition</th>
                    <th width="50">Percent</th>
                    <th width="100">Type</th>
                    <th width="110">Required Qnty</th>
                     <th width="100">Required Amount</th>
                    <th>Shipment Date</th>
                </thead>
             </table>
             <div style="width:1230px; max-height:320px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="1210" cellpadding="0" cellspacing="0">
                    <?
                    $i=1; $tot_req_qnty=0;
					$sql="select a.buyer_name, a.job_no, a.total_set_qnty as ratio, b.po_number, b.po_quantity, b.pub_shipment_date, b.plan_cut,c.count_id,c.copm_one_id,c.percent_one,c.type_id, sum(c.cons_qnty) as qnty,sum(c.amount) as amount  from wo_po_details_master a, wo_po_break_down b, wo_pre_cost_fab_yarn_cost_dtls c where a.job_no=b.job_no_mst and a.job_no=c.job_no and b.id in($order_id)  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.id, a.buyer_name, a.job_no, a.total_set_qnty, b.po_number, b.po_quantity, b.pub_shipment_date, b.plan_cut,c.count_id,c.copm_one_id,c.percent_one,c.type_id";
                    $result=sql_select($sql);
        			foreach($result as $row)
                    {
                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";	
                    
						$dzn_qnty=0; $required_qnty=0; $order_qnty=0; 
						if($costing_per_id_library[$row[csf('job_no')]]==1) $dzn_qnty=12;
						else if($costing_per_id_library[$row[csf('job_no')]]==3) $dzn_qnty=12*2;
						else if($costing_per_id_library[$row[csf('job_no')]]==4) $dzn_qnty=12*3;
						else if($costing_per_id_library[$row[csf('job_no')]]==5) $dzn_qnty=12*4;
						else $dzn_qnty=1;
						
						$order_qnty=$row[csf('po_quantity')]*$row[csf('ratio')];
						$plan_cut_qnty=$row[csf('plan_cut')];
						$required_qnty=$plan_cut_qnty*($row[csf('qnty')]/$dzn_qnty);
						$required_amount=$plan_cut_qnty*($row[csf('amount')]/$dzn_qnty);
                        $tot_req_qnty+=$required_qnty;
						 $tot_req_amount+=$required_amount;
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="40"><? echo $i; ?></td>
                            <td width="120"><p><? echo $row[csf('po_number')]; ?></p></td>
                            <td width="120"><p><? echo $buyer_short_name_library[$row[csf('buyer_name')]]; ?></p></td>
                            <td width="90" align="right"><p><? echo number_format($row[csf('qnty')],2); ?></p></td>
                            <td width="110" align="right"><p><? echo number_format($order_qnty,0); ?></p></td>
                            <td width="110" align="right"><p><? echo number_format($plan_cut_qnty,0); ?></p></td>
                            <td width="50"><? echo $yarn_count_details[$row[csf('count_id')]]; ?></td>
                            <td width="100"><? echo $composition[$row[csf('copm_one_id')]]; ?></td>
                            <td width="50"><? echo $row[csf('percent_one')]; ?></td>
                            <td width="100"><? echo $yarn_type[$row[csf('type_id')]]; ?></td>
                            <td width="110" align="right"><p><? echo number_format($required_qnty,2); ?></p></td>
                            <td width="110" align="right"><p><? echo number_format($tot_req_amount,2); ?></p></td>
                            <td align="center"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
                        </tr>
                    <?
                    $i++;
                    }
                    ?>
                    <tfoot>
                        <th align="right" colspan="10">Total</th>
                        <th align="right"><? echo number_format($tot_req_qnty,2); ?></th>
                        <th align="right"><? echo number_format($tot_req_amount,2); ?></th>
                        <th>&nbsp;</th>
                    </tfoot>
                </table>
            </div>	
        </div>
	</fieldset>   
<?
exit();
}

if($action=="yarn_issue")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$brand_array=return_library_array( "select id, brand_name from lib_brand", "id", "brand_name"  );
?>
<script>

	function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="230px";
	}	
	
</script>	
	<div style="width:870px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:865px; margin-left:3px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="860" cellpadding="0" cellspacing="0">
            	<thead>
					<th colspan="10"><b>Yarn Issue</b></th>
				</thead>
				<thead>
                    <th width="105">Issue Id</th>
                    <th width="90">Issue To</th>
                    <th width="105">Booking No</th>
                    <th width="80">Challan No</th>
                    <th width="70">Brand</th>
                    <th width="60">Lot No</th>
                    <th width="75">Issue Date</th>
                    <th width="80">Yarn Type</th>
                    <th width="90">Issue Qnty (In)</th>
                    <th>Issue Qnty (Out)</th>
				</thead>
                <?
                $i=1; $total_yarn_issue_qnty=0; $total_yarn_issue_qnty_out=0;
				$sql="select a.issue_number, a.issue_date, a.challan_no, a.knit_dye_source, a.knit_dye_company, a.booking_no, sum(b.quantity) as issue_qnty, c.lot, c.yarn_type, c.id as prod_id, c.product_name_details, d.brand_id from inv_issue_master a, order_wise_pro_details b, product_details_master c, inv_transaction d where a.id=d.mst_id and d.transaction_type=2 and d.item_category=1 and c.item_category_id=1 and d.id=b.trans_id and b.trans_type=2 and b.entry_form=3 and b.po_breakdown_id in ($order_id) and b.prod_id=c.id and c.yarn_count_id='$yarn_count' and c.yarn_comp_type1st='$yarn_comp_type1st' and c.yarn_comp_percent1st='$yarn_comp_percent1st' and c.yarn_comp_type2nd='$yarn_comp_type2nd' and c.yarn_comp_percent2nd='$yarn_comp_percent2nd' and c.yarn_type='$yarn_type_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.issue_purpose!=2 group by a.id, c.id, a.issue_number, a.issue_date, a.challan_no, a.knit_dye_source, a.knit_dye_company, a.booking_no, c.lot, c.yarn_type, c.product_name_details, d.brand_id";
                $result=sql_select($sql);
				foreach($result as $row)
				{
					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";	
				
					if($row[csf('knit_dye_source')]==1) 
					{
						$issue_to=$company_library[$row[csf('knit_dye_company')]]; 
					}
					else if($row['knit_dye_source']==3) 
					{
						$issue_to=$supplier_details[$row[csf('knit_dye_company')]];
					}
					else
						$issue_to="&nbsp;";
						
                    $yarn_issued=$row[csf('issue_qnty')];
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td width="105"><p><? echo $row[csf('issue_number')]; ?></p></td>
                        <td width="90"><p><? echo $issue_to; ?></p></td>
                        <td width="105"><p><? echo $row[csf('booking_no')];?>&nbsp;</p></td>
                        <td width="80"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
                        <td width="70"><p><? echo $brand_array[$row[csf('brand_id')]]; ?>&nbsp;</p></td>
                        <td width="60"><p><? echo $row[csf('lot')]; ?></p></td>
                        <td width="75" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
                        <td width="80"><p><? echo $yarn_type[$row[csf('yarn_type')]]; ?></p></td>
                        <td align="right" width="90">
							<? 
								if($row[csf('knit_dye_source')]!=3)
								{
									echo number_format($yarn_issued,2);
									$total_yarn_issue_qnty+=$yarn_issued;
								}
								else echo "&nbsp;";
                            ?>
                        </td>
                        <td align="right">
							<? 
								if($row[csf('knit_dye_source')]==3)
								{ 
									echo number_format($yarn_issued,2); 
									$total_yarn_issue_qnty_out+=$yarn_issued;
								}
								else echo "&nbsp;";
                            ?>
                        </td>
                    </tr>
                <?
                $i++;
                }
                ?>
                <tr style="font-weight:bold">
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td align="right">Total</td>
                    <td align="right"><? echo number_format($total_yarn_issue_qnty,2);?></td>
                    <td align="right"><? echo number_format($total_yarn_issue_qnty_out,2);?></td>
                </tr>
                <tr style="font-weight:bold">
                    <td align="right" colspan="9">Issue Total</td>
                    <td align="right"><? echo number_format($total_yarn_issue_qnty+$total_yarn_issue_qnty_out,2);?></td>
                </tr>
                <thead>
                    <th colspan="10"><b>Yarn Return</b></th>
                </thead>
                <thead>
                	<th width="105">Return Id</th>
                    <th width="90">Return From</th>
                    <th width="105">Booking No</th>
                    <th width="80">Challan No</th>
                    <th width="70">Brand</th>
                    <th width="60">Lot No</th>
                    <th width="75">Return Date</th>
                    <th width="80">Yarn Type</th>
                    <th width="90">Return Qnty (In)</th>
                    <th>Return Qnty (Out)</th>
               </thead>
                <?
                $total_yarn_return_qnty=0; $total_yarn_return_qnty_out=0;
                $sql="select a.recv_number, a.receive_date, a.challan_no, a.knitting_source, a.knitting_company, a.booking_no, sum(b.quantity) as returned_qnty, c.lot, c.yarn_type, c.id as prod_id, c.product_name_details, d.brand_id from inv_receive_master a, order_wise_pro_details b, product_details_master c, inv_transaction d where a.id=d.mst_id and d.transaction_type=4 and c.item_category_id=1 and d.item_category=1 and d.id=b.trans_id and b.trans_type=4 and b.entry_form=9 and b.po_breakdown_id in ($order_id) and b.prod_id=c.id and c.yarn_count_id='$yarn_count' and c.yarn_comp_type1st='$yarn_comp_type1st' and c.yarn_comp_percent1st='$yarn_comp_percent1st' and c.yarn_comp_type2nd='$yarn_comp_type2nd' and c.yarn_comp_percent2nd='$yarn_comp_percent2nd' and c.yarn_type='$yarn_type_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.issue_purpose!=2 group by a.id, c.id, a.recv_number, a.receive_date, a.challan_no, a.knitting_source, a.knitting_company, a.booking_no, c.lot, c.yarn_type, c.product_name_details, d.brand_id";
                $result=sql_select($sql);
				foreach($result as $row)
				{
					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";	
				
					if($row[csf('knitting_source')]==1) 
					{
						$return_from=$company_library[$row[csf('knitting_company')]]; 
					}
					else if($row['knitting_source']==3) 
					{
						$return_from=$supplier_details[$row[csf('knitting_company')]];
					}
					else
						$return_from="&nbsp;";
						
                    $yarn_returned=$row[csf('returned_qnty')];
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td width="105"><p><? echo $row[csf('recv_number')]; ?></p></td>
                        <td width="90"><p><? echo $return_from; ?></p></td>
                        <td width="105"><p><? echo $row[csf('booking_no')]; ?>&nbsp;</p></td>
                        <td width="80"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
                        <td width="70"><p><? echo $brand_array[$row[csf('brand_id')]]; ?>&nbsp;</p></td>
                        <td width="60"><p><? echo $row[csf('lot')]; ?></p></td>
                        <td width="75" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                        <td width="80"><p><? echo $yarn_type[$row[csf('yarn_type')]]; ?></p></td>
                        <td align="right" width="90">
							<? 
								if($row[csf('knitting_source')]!=3)
								{
									echo number_format($yarn_returned,2);
									$total_yarn_return_qnty+=$yarn_returned;
								}
								else echo "&nbsp;";
                            ?>
                        </td>
                        <td align="right">
							<? 
								if($row[csf('knitting_source')]==3)
								{ 
									echo number_format($yarn_returned,2); 
									$total_yarn_return_qnty_out+=$yarn_returned;
								}
								else echo "&nbsp;";
                            ?>
                        </td>
                    </tr>
                <?
                $i++;
                }
                ?>
                <tr style="font-weight:bold">
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td align="right">Balance</td>
                    <td align="right"><? echo number_format($total_yarn_issue_qnty-$total_yarn_return_qnty,2);?></td>
                    <td align="right"><? echo number_format($total_yarn_issue_qnty_out-$total_yarn_return_qnty_out,2);?></td>
                </tr>
                <tfoot>    
                    <tr>
                        <th align="right" colspan="9">Total Balance</th>
                        <th align="right"><? echo number_format(($total_yarn_issue_qnty+$total_yarn_issue_qnty_out)-($total_yarn_return_qnty+$total_yarn_return_qnty_out),2);?></th>
                    </tr>
                </tfoot>
            </table>	
		</div>
	</fieldset>  
<?
exit();
}

if($action=="yarn_issue_not")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$brand_array=return_library_array( "select id, brand_name from lib_brand", "id", "brand_name"  );
	$yarn_desc_array=explode(",",$yarn_count);
	//print_r($yarn_desc_array);
?>
<script>

	function print_window()
	{
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
	}	
	
</script>	
	<div style="width:970px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:970px; margin-left:3px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="960" cellpadding="0" cellspacing="0">
            	<thead>
					<th colspan="10"><b>Yarn Issue</b></th>
				</thead>
				<thead>
                    <th width="105">Issue Id</th>
                    <th width="90">Issue To</th>
                    <th width="105">Booking No</th>
                    <th width="70">Challan No</th>
                    <th width="75">Issue Date</th>
                    <th width="70">Brand</th>
                    <th width="60">Lot No</th>
                    <th width="180">Yarn Description</th>
                    <th width="90">Issue Qnty (In)</th>
                    <th>Issue Qnty (Out)</th>
				</thead>
                <?
				$i=1; $total_yarn_issue_qnty=0; $total_yarn_issue_qnty_out=0; $yarn_desc_array_for_return=array();
				$sql_yarn_iss="select b.yarn_count_id, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type from order_wise_pro_details a, product_details_master b where a.prod_id=b.id and a.po_breakdown_id in ($order_id) and a.entry_form in(3,9) and b.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and a.issue_purpose!=2 group by b.yarn_count_id, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type";
				$dataArrayIssue=sql_select($sql_yarn_iss);
				foreach($dataArrayIssue as $row_yarn_iss)
				{
					if($row_yarn_iss[csf('yarn_comp_percent2nd')]!=0)
					{
						$compostion_not_req=$composition[$row_yarn_iss[csf('yarn_comp_type1st')]]." ".$row_yarn_iss[csf('yarn_comp_percent1st')]." %"." ".$composition[$row_yarn_iss[csf('yarn_comp_type2nd')]]." ".$row_yarn_iss[csf('yarn_comp_percent2nd')]." %";
					}
					else
					{
						$compostion_not_req=$composition[$row_yarn_iss[csf('yarn_comp_type1st')]]." ".$row_yarn_iss[csf('yarn_comp_percent1st')]." %"." ".$composition[$row_yarn_iss[csf('yarn_comp_type2nd')]];
					}
			
					$desc=$yarn_count_details[$row_yarn_iss[csf('yarn_count_id')]]." ".$compostion_not_req." ".$yarn_type[$row_yarn_iss[csf('yarn_type')]];
					
					$yarn_desc_for_return=$row_yarn_iss[csf('yarn_count_id')]."__".$row_yarn_iss[csf('yarn_comp_type1st')]."__".$row_yarn_iss[csf('yarn_comp_percent1st')]."__".$row_yarn_iss[csf('yarn_comp_type2nd')]."__".$row_yarn_iss[csf('yarn_comp_percent2nd')]."__".$row_yarn_iss[csf('yarn_type')];
					
					$yarn_desc_array_for_return[$desc]=$yarn_desc_for_return;
					
					if(!in_array($desc,$yarn_desc_array))
					{
						$sql="select a.issue_number, a.issue_date, a.challan_no, a.knit_dye_source, a.knit_dye_company, a.booking_no, sum(b.quantity) as issue_qnty, c.lot, c.yarn_type, c.id as prod_id, c.product_name_details, d.brand_id from inv_issue_master a, order_wise_pro_details b, product_details_master c, inv_transaction d where a.id=d.mst_id and d.transaction_type=2 and d.item_category=1 and d.id=b.trans_id and b.trans_type=2 and b.entry_form=3 and b.po_breakdown_id in ($order_id) and b.prod_id=c.id and c.yarn_count_id='".$row_yarn_iss[csf('yarn_count_id')]."' and c.yarn_comp_type1st='".$row_yarn_iss[csf('yarn_comp_type1st')]."' and c.yarn_comp_percent1st='".$row_yarn_iss[csf('yarn_comp_percent1st')]."' and c.yarn_comp_type2nd='".$row_yarn_iss[csf('yarn_comp_type2nd')]."' and c.yarn_comp_percent2nd='".$row_yarn_iss[csf('yarn_comp_percent2nd')]."' and c.yarn_type='".$row_yarn_iss[csf('yarn_type')]."' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.issue_purpose!=2 group by a.id, c.id, a.issue_number, a.issue_date, a.challan_no, a.knit_dye_source, a.knit_dye_company, a.booking_no, c.lot, c.yarn_type, c.product_name_details, d.brand_id";
						$result=sql_select($sql);
						foreach($result as $row)
						{
							if ($i%2==0)  
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";	
						
							if($row[csf('knit_dye_source')]==1) 
							{
								$issue_to=$company_library[$row[csf('knit_dye_company')]]; 
							}
							else if($row['knit_dye_source']==3) 
							{
								$issue_to=$supplier_details[$row[csf('knit_dye_company')]];
							}
							else
								$issue_to="&nbsp;";
								
							$yarn_issued=$row[csf('issue_qnty')];
							
							?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td width="105"><p><? echo $row[csf('issue_number')]; ?></p></td>
								<td width="90"><p><? echo $issue_to; ?></p></td>
								<td width="105"><p><? echo $row[csf('booking_no')]; ?>&nbsp;</p></td>
								<td width="70"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
                                <td width="75" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
                                <td width="70"><p><? echo $brand_array[$row[csf('brand_id')]]; ?>&nbsp;</p></td>
								<td width="60"><p><? echo $row[csf('lot')]; ?></p></td>
								<td width="180"><p><? echo $row[csf('product_name_details')]; ?></p></td>
								<td align="right" width="90">
									<? 
										if($row[csf('knit_dye_source')]!=3)
										{
											echo number_format($yarn_issued,2,'.','');
											$total_yarn_issue_qnty+=$yarn_issued;
										}
										else echo "&nbsp;";
									?>
								</td>
								<td align="right">
									<? 
										if($row[csf('knit_dye_source')]==3)
										{ 
											echo number_format($yarn_issued,2,'.',''); 
											$total_yarn_issue_qnty_out+=$yarn_issued;
										}
										else echo "&nbsp;";
									?>
								</td>
							</tr>
						<?
						$i++;
						}
					}
				}
                ?>
                <tr style="font-weight:bold">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right">Total</td>
                    <td align="right"><? echo number_format($total_yarn_issue_qnty,2,'.',''); ?></td>
                    <td align="right"><? echo number_format($total_yarn_issue_qnty_out,2,'.',''); ?></td>
                </tr>
                <tr style="font-weight:bold">
                    <td align="right" colspan="9">Issue Total</td>
                    <td align="right"><? echo number_format($total_yarn_issue_qnty+$total_yarn_issue_qnty_out,2,'.',''); ?></td>
                </tr>
                <thead>
                    <th colspan="10"><b>Yarn Return</b></th>
                </thead>
                <thead>
                	<th width="105">Return Id</th>
                    <th width="90">Return From</th>
                    <th width="105">Booking No</th>
                    <th width="70">Challan No</th>
                    <th width="75">Return Date</th>
                    <th width="70">Brand</th>
                    <th width="60">Lot No</th>
                    <th width="180">Yarn Description</th>
                    <th width="90">Return Qnty (In)</th>
                    <th>Return Qnty (Out)</th>
               </thead>
                <?
				$total_yarn_return_qnty=0; $total_yarn_return_qnty_out=0;
				foreach($yarn_desc_array_for_return as $key=>$value)
				{
					if(!in_array($key,$yarn_desc_array))
					{
						$desc=explode("__",$value);
						$yarn_count=$desc[0];
						$yarn_comp_type1st=$desc[1];
						$yarn_comp_percent1st=$desc[2];
						$yarn_comp_type2nd=$desc[3];
						$yarn_comp_percent2nd=$desc[4];
						$yarn_type_id=$desc[5];
						
						$sql="select a.recv_number, a.receive_date, a.challan_no, a.knitting_source, a.knitting_company, a.booking_no, sum(b.quantity) as returned_qnty, c.lot, c.yarn_type, c.id as prod_id, c.product_name_details, c.brand from inv_receive_master a, order_wise_pro_details b, product_details_master c, inv_transaction d where a.id=d.mst_id and d.transaction_type=4 and d.item_category=1 and d.id=b.trans_id and b.trans_type=4 and b.entry_form=9 and b.po_breakdown_id in ($order_id) and b.prod_id=c.id and c.yarn_count_id='$yarn_count' and c.yarn_comp_type1st='$yarn_comp_type1st' and c.yarn_comp_percent1st='$yarn_comp_percent1st' and c.yarn_comp_type2nd='$yarn_comp_type2nd' and c.yarn_comp_percent2nd='$yarn_comp_percent2nd' and c.yarn_type='$yarn_type_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.issue_purpose!=2 group by a.id, c.id, a.recv_number, a.receive_date, a.challan_no, a.knitting_source, a.knitting_company, a.booking_no, c.lot, c.yarn_type, c.product_name_details, c.brand";
						$result=sql_select($sql);
						foreach($result as $row)
						{
							if ($i%2==0)  
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";	
						
							if($row[csf('knitting_source')]==1) 
							{
								$return_from=$company_library[$row[csf('knitting_company')]]; 
							}
							else if($row['knitting_source']==3) 
							{
								$return_from=$supplier_details[$row[csf('knitting_company')]];
							}
							else
								$return_from="&nbsp;";
								
							$yarn_returned=$row[csf('returned_qnty')];
							?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td width="105"><p><? echo $row[csf('recv_number')]; ?></p></td>
								<td width="90"><p><? echo $return_from; ?></p></td>
								<td width="105"><p><? echo $row[csf('booking_no')]; ?>&nbsp;</p></td>
								<td width="70"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
                                <td width="75" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
								<td width="70"><p><? echo $brand_array[$row[csf('brand')]]; ?>&nbsp;</p></td>
								<td width="60"><p><? echo $row[csf('lot')]; ?></p></td>
								<td width="180"><p><? echo $row[csf('product_name_details')]; ?></p></td>
								<td align="right" width="90">
									<? 
										if($row[csf('knitting_source')]!=3)
										{
											echo number_format($yarn_returned,2,'.','');
											$total_yarn_return_qnty+=$yarn_returned;
										}
										else echo "&nbsp;";
									?>
								</td>
								<td align="right">
									<? 
										if($row[csf('knitting_source')]==3)
										{ 
											echo number_format($yarn_returned,2,'.',''); 
											$total_yarn_return_qnty_out+=$yarn_returned;
										}
										else echo "&nbsp;";
									?>
								</td>
							</tr>
						<?
						$i++;
						}
					}
				}
                ?>
                <tr style="font-weight:bold">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right">Balance</td>
                    <td align="right"><? echo number_format($total_yarn_issue_qnty-$total_yarn_return_qnty,2,'.',''); ?></td>
                    <td align="right"><? echo number_format($total_yarn_issue_qnty_out-$total_yarn_return_qnty_out,2,'.',''); ?></td>
                </tr>
                <tfoot>    
                    <tr>
                        <th align="right" colspan="9">Total Balance</th>
                        <th align="right"><? echo number_format(($total_yarn_issue_qnty+$total_yarn_issue_qnty_out)-($total_yarn_return_qnty+$total_yarn_return_qnty_out),2);?></th>
                    </tr>
                </tfoot>
            </table>	
		</div>
	</fieldset>  
<?
exit();
}

if($action=="grey_receive")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	
	$machine_arr = return_library_array("select id, machine_no from lib_machine_name","id","machine_no");
?>
<script>

	var tableFilters = {
						   col_operation: {
						   id: ["value_receive_qnty_in","value_receive_qnty_out","value_receive_qnty_tot"],
						   col: [7,8,9],
						   operation: ["sum","sum","sum"],
						   write_method: ["innerHTML","innerHTML","innerHTML"]
						}
					}
	$(document).ready(function(e) {
		setFilterGrid('tbl_list_search',-1,tableFilters);
	});
		
	function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		$('#tbl_list_search tr:first').hide(); 
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="230px";
		
		$('#tbl_list_search tr:first').show();
	}	
	
</script>	
	<div style="width:1037px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:1037px;">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="1020" cellpadding="0" cellspacing="0">
				<thead>
					<th colspan="12"><b>Grey Receive Info</b></th>
				</thead>
				<thead>
                	<th width="30">SL</th>
                    <th width="115">Receive Id</th>
                    <th width="95">Receive Basis</th>
                    <th width="110">Product Details</th>
                    <th width="100">Booking / Program No</th>
                    <th width="60">Machine No</th>
                    <th width="75">Production Date</th>
                    <th width="80">Inhouse Production</th>
                    <th width="80">Outside Production</th>
                    <th width="80">Production Qnty</th>
                    <th width="70">Challan No</th>
                    <th>Kniting Com.</th>
				</thead>
             </table>
             <div style="width:1038px; max-height:330px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="1020" cellpadding="0" cellspacing="0" id="tbl_list_search">
                    <?
                    $i=1; $total_receive_qnty=0;
					$product_arr=return_library_array( "select id,product_name_details from product_details_master where item_category_id=13",'id','product_name_details'); 
					
                    $sql="select a.recv_number, a.receive_date, a.receive_basis, a.booking_no, a.knitting_source, a.challan_no, a.knitting_company, b.machine_no_id, b.prod_id, sum(c.quantity) as quantity from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and c.po_breakdown_id in($order_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.id, a.recv_number, a.receive_date, a.receive_basis, a.booking_no, a.knitting_source, a.challan_no, a.knitting_company, b.machine_no_id, b.prod_id";
                    $result=sql_select($sql);
        			foreach($result as $row)
                    {
                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";	
                    
                        $total_receive_qnty+=$row[csf('quantity')];
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="30"><? echo $i; ?></td>
                            <td width="115"><p><? echo $row[csf('recv_number')]; ?></p></td>
                            <td width="95"><p><? echo $receive_basis[$row[csf('receive_basis')]]; ?></p></td>
                            <td width="110"><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
                            <td width="100"><p><? echo $row[csf('booking_no')]; ?>&nbsp;</p></td>
                            <td width="60"><p>&nbsp;<? echo $machine_arr[$row[csf('machine_no_id')]]; ?></p></td>
                            <td width="75" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                            <td align="right" width="80">
								<? 
                                	if($row[csf('knitting_source')]!=3)
									{
										echo number_format($row[csf('quantity')],2,'.','');
										$total_receive_qnty_in+=$row[csf('quantity')];
									}
									else echo "&nbsp;";
                                ?>
                            </td>
                            <td align="right" width="80">
								<? 
                                	if($row[csf('knitting_source')]==3)
									{
										echo number_format($row[csf('quantity')],2,'.','');
										$total_receive_qnty_out+=$row[csf('quantity')];
									}
									else echo "&nbsp;";
                                ?>
                            </td>
                            <td align="right" width="80"><? echo number_format($row[csf('quantity')],2,'.',''); ?></td>
                            <td width="70"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
                            <td><p><? if ($row[csf('knitting_source')]==1) echo $company_library[$row[csf('knitting_company')]]; else if ($row[csf('knitting_source')]==3) echo $supplier_details[$row[csf('knitting_company')]]; ?></p></td>
                        </tr>
                    <?
                    $i++;
                    }
                    ?>
                 </table>
            </div>
            <table border="1" class="rpt_table" rules="all" width="1020" cellpadding="0" cellspacing="0">  
                <tfoot>
                    <th width="30">&nbsp;</th>
                    <th width="115">&nbsp;</th>
                    <th width="95">&nbsp;</th>
                    <th width="110">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="60">&nbsp;</th>
                    <th width="75" align="right">Total</th>
                    <th width="80" align="right" id="value_receive_qnty_in"><? echo number_format($total_receive_qnty_in,2,'.',''); ?></th>
                    <th width="80" align="right" id="value_receive_qnty_out"><? echo number_format($total_receive_qnty_out,2,'.',''); ?></th>
                    <th width="80" align="right" id="value_receive_qnty_tot"><? echo number_format($total_receive_qnty,2,'.',''); ?></th>
                    <th width="70">&nbsp;</th>
                    <th>&nbsp;</th>
                </tfoot>
            </table>	
        </div>
	</fieldset>   
<?
exit();
}

if($action=="grey_delivery_to_store")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	
	$machine_arr = return_library_array("select id, machine_no from lib_machine_name","id","machine_no");
?>
<script>

	var tableFilters = {
						   col_operation: {
						   id: ["value_delivery_qnty"],
						   col: [7],
						   operation: ["sum"],
						   write_method: ["innerHTML"]
						}
					}
	$(document).ready(function(e) {
		setFilterGrid('tbl_list_search',-1,tableFilters);
	});
		
	function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		$('#tbl_list_search tr:first').hide(); 
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="230px";
		
		$('#tbl_list_search tr:first').show();
	}	
	
</script>	
	<div style="width:720px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:720px;">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="720" cellpadding="0" cellspacing="0">
				<thead>
					<th colspan="8"><b>Grey Delivery To Store Info</b></th>
				</thead>
				<thead>
                	<th width="30">SL</th>
                    <th width="115">Challan No</th>
                    <th width="75">Delivery Date</th>
                    <th width="115">Production ID</th>
                    <th width="180">Product Details</th>
                    <th width="50">GSM</th>
                    <th width="50">Dia</th>
                    <th>Delivery Qnty</th>
				</thead>
             </table>
             <div style="width:740px; max-height:330px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="720" cellpadding="0" cellspacing="0" id="tbl_list_search">
                    <?
                    $i=1; $total_receive_qnty=0;
					$product_arr=return_library_array( "select id,product_name_details from product_details_master where item_category_id=13",'id','product_name_details'); 
					
                   // $sql="select a.recv_number, a.receive_date, a.receive_basis, a.booking_no, a.knitting_source, a.challan_no, a.knitting_company, b.machine_no_id, b.prod_id, sum(c.quantity) as quantity from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and c.po_breakdown_id in($order_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.id, a.recv_number, a.receive_date, a.receive_basis, a.booking_no, a.knitting_source, a.challan_no, a.knitting_company, b.machine_no_id, b.prod_id";
				   
				   //select order_id, sum(current_delivery) as grey_delivery_qty from pro_grey_prod_delivery_dtls where entry_form in(53,56) and status_active=1 and is_deleted=0 $grey_delivery_po_cond group by order_id
				    $sql="select a.sys_number_prefix_num, a.sys_number, a.delevery_date, b.grey_sys_number, b.product_id, b.construction, b.composition, b.gsm, b.dia, sum(b.current_delivery) as delivery_qty from pro_grey_prod_delivery_mst a, pro_grey_prod_delivery_dtls b where a.id=b.mst_id and a.entry_form in (53,56) and b.order_id in ($order_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.sys_number_prefix_num, a.sys_number, a.delevery_date, b.grey_sys_number, b.product_id, b.construction, b.composition, b.gsm, b.dia";
                    $result=sql_select($sql);
        			foreach($result as $row)
                    {
                        if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
                    
                        $total_delivery_qnty+=$row[csf('delivery_qty')];
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="30"><? echo $i; ?></td>
                            <td width="115"><p><? echo $row[csf('sys_number')]; ?></p></td>
                            <td width="75"><p><? echo change_date_format($row[csf('delevery_date')]); ?></p></td>
                            <td width="115"><p><? echo $row[csf('grey_sys_number')]; ?></p></td>
                            <td width="180"><p><? echo $product_arr[$row[csf('product_id')]]; ?>&nbsp;</p></td>
                            <td width="50"><? echo $row[csf('gsm')]; ?></td>
                            <td width="50"><? echo $row[csf('dia')]; ?></td>
                            <td align="right"><? echo number_format($row[csf('delivery_qty')],2,'.',''); ?></td>
                        </tr>
                    <?
                    $i++;
                    }
                    ?>
                 </table>
            </div>
            <table border="1" class="rpt_table" rules="all" width="720" cellpadding="0" cellspacing="0">  
                <tfoot>
                    <th width="30">&nbsp;</th>
                    <th width="115">&nbsp;</th>
                    <th width="75">&nbsp;</th>
                    <th width="115">&nbsp;</th>
                    <th width="180">&nbsp;</th>
                    <th width="50">&nbsp;</th>
                    <th width="50" align="right">Total</th>
                    <th align="right" id="value_delivery_qnty"><? echo number_format($total_delivery_qnty,2,'.',''); ?></th>
                </tfoot>
            </table>	
        </div>
	</fieldset>   
<?
exit();
}


if($action=="grey_purchase")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?>
<script>

	function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="230px";
	}	
	
</script>	
	<div style="width:1037px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:1037px; margin-left:2px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="1020" cellpadding="0" cellspacing="0">
				<thead>
					<th colspan="11"><b>Grey Receive / Purchase Info</b></th>
				</thead>
				<thead>
                	<th width="30">SL</th>
                    <th width="125">Receive Id</th>
                    <th width="95">Receive Basis</th>
                     <th width="150">Product Details</th>
                    <th width="110">Booking/PI/ Production No</th>
                    <th width="75">Production Date</th>
                    <th width="80">Inhouse Production</th>
                    <th width="80">Outside Production</th>
                    <th width="80">Production Qnty</th>
                    <th width="65">Challan No</th>
                    <th>Kniting Com.</th>
				</thead>
             </table>
             <div style="width:1037px; max-height:330px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="1020" cellpadding="0" cellspacing="0">
                    <?
                    $i=1; $total_receive_qnty=0;
					$product_arr=return_library_array( "select id,product_name_details from product_details_master where item_category_id=13",'id','product_name_details');
					
                    $sql="select a.recv_number, a.receive_date, a.receive_basis, a.booking_no, a.knitting_source, a.challan_no, a.knitting_company, b.prod_id, sum(c.quantity) as quantity from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.receive_basis<>9 and a.entry_form=22 and c.entry_form=22 and c.po_breakdown_id in($order_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.id, a.recv_number, a.receive_date, a.receive_basis, a.booking_no, a.knitting_source, a.challan_no, a.knitting_company, b.prod_id";
                    $result=sql_select($sql);
        			foreach($result as $row)
                    {
                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";	
                    
                        $total_receive_qnty+=$row[csf('quantity')];
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="30"><? echo $i; ?></td>
                            <td width="125"><p><? echo $row[csf('recv_number')]; ?></p></td>
                            <td width="95"><p><? echo $receive_basis_arr[$row[csf('receive_basis')]]; ?></p></td>
                            <td width="150"><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
                            <td width="110"><p><? echo $row[csf('booking_no')]; ?>&nbsp;</p></td>
                            <td width="75" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                            <td align="right" width="80">
								<? 
                                	if($row[csf('knitting_source')]!=3)
									{
										echo number_format($row[csf('quantity')],2,'.','');
										$total_receive_qnty_in+=$row[csf('quantity')];
									}
									else echo "&nbsp;";
                                ?>
                            </td>
                            <td align="right" width="80">
								<? 
                                	if($row[csf('knitting_source')]==3)
									{
										echo number_format($row[csf('quantity')],2,'.','');
										$total_receive_qnty_out+=$row[csf('quantity')];
									}
									else echo "&nbsp;";
                                ?>
                            </td>
                            <td align="right" width="80"><? echo number_format($row[csf('quantity')],2,'.',''); ?></td>
                            <td width="65"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
                            <td><p><? if ($row[csf('knitting_source')]==1) echo $company_library[$row[csf('knitting_company')]]; else if ($row[csf('knitting_source')]==3) echo $supplier_details[$row[csf('knitting_company')]]; ?>&nbsp;</p></td>
                        </tr>
                    <?
                    $i++;
                    }
                    ?>
                    <tfoot>
                        <th colspan="6" align="right">Total</th>
                        <th align="right"><? echo number_format($total_receive_qnty_in,2,'.',''); ?></th>
                        <th align="right"><? echo number_format($total_receive_qnty_out,2,'.',''); ?></th>
                        <th align="right"><? echo number_format($total_receive_qnty,2,'.',''); ?></th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                    </tfoot>
                </table>
            </div>	
        </div>
	</fieldset>   
<?
exit();
}

if($action=="batch_qnty")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$ex_data=explode('_',$order_id);
?>
<script>

	function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="230px";
	}	
	
</script>	
	<div style="width:675px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:670px; margin-left:7px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="650" cellpadding="0" cellspacing="0">
				<thead>
					<th colspan="5"><b>Batch Info</b></th>
				</thead>
				<thead>
                	<th width="50">SL</th>
                    <th width="100">Batch Date</th>
                    <th width="170">Batch No</th>
                    <th width="150">Batch Color</th>
                    <th>Batch Qnty</th>
				</thead>
             </table>
             <div style="width:667px; max-height:320px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="650" cellpadding="0" cellspacing="0">
                    <?
                    $i=1; $total_batch_qnty=0;
                    $sql="select a.batch_no, a.batch_date, a.color_id, sum(b.batch_qnty) as quantity from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and b.po_id in($ex_data[0]) and a.color_id='$ex_data[1]' and a.status_active=1 and a.batch_against<>2 and a.entry_form=0 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.batch_no, a.batch_date, a.color_id";
                    $result=sql_select($sql);
        			foreach($result as $row)
                    {
                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";	
                    
                        $total_batch_qnty+=$row[csf('quantity')];
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="50"><? echo $i; ?></td>
                            <td width="100" align="center"><? echo change_date_format($row[csf('batch_date')]); ?></td>
                            <td width="170"><p><? echo $row[csf('batch_no')]; ?></p></td>
                            <td width="150"><p><? echo $color_array[$row[csf('color_id')]]; ?></p></td>
                            <td align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
                        </tr>
                    <?
                    $i++;
                    }
                    ?>
                    <tfoot>
                        <th colspan="4" align="right">Total</th>
                        <th align="right"><? echo number_format($total_batch_qnty,2); ?></th>
                    </tfoot>
                </table>
            </div>	
        </div>
	</fieldset>   
<?
exit();
}

if($action=="grey_issue")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?>
<script>

	function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="230px";
	}	
	
</script>	
	<div style="width:885px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:880px; margin-left:3px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="860" cellpadding="0" cellspacing="0">
                <thead>
                	<tr>
                        <th colspan="9"><b>Grey Issue Info</b></th>
                    </tr>
                    <tr>
                        <th width="40">SL</th>
                        <th width="120">Issue Id</th>
                        <th width="100">Issue Purpose</th>
                        <th width="100">Issue To</th>
                        <th width="115">Booking No</th>
                        <th width="90">Batch No</th>
                        <th width="80">Issue Date</th>
                        <th width="100">Issue Qnty (In)</th>
                        <th>Issue Qnty (Out)</th>
                    </tr>
				</thead>
             </table>
             <div style="width:877px; max-height:320px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="860" cellpadding="0" cellspacing="0">
                    <?
					/*$dataArrayTrans=sql_select("select po_breakdown_id, 
								sum(CASE WHEN entry_form ='2' THEN quantity ELSE 0 END) AS grey_receive,
								sum(CASE WHEN entry_form ='16' THEN quantity ELSE 0 END) AS grey_issue,
								sum(CASE WHEN entry_form ='61' THEN quantity ELSE 0 END) AS grey_issue_rollwise,
								sum(CASE WHEN entry_form ='11' and trans_type=5 THEN quantity ELSE 0 END) AS transfer_in_qnty_yarn,
								sum(CASE WHEN entry_form ='11' and trans_type=6 THEN quantity ELSE 0 END) AS transfer_out_qnty_yarn,
								sum(CASE WHEN entry_form ='13' and trans_type=5 THEN quantity ELSE 0 END) AS transfer_in_qnty_knit,
								sum(CASE WHEN entry_form ='13' and trans_type=6 THEN quantity ELSE 0 END) AS transfer_out_qnty_knit
								from order_wise_pro_details where status_active=1 and is_deleted=0 and entry_form in(2,11,13,16,61) $trans_po_cond group by po_breakdown_id");
		foreach($dataArrayTrans as $row)
		{
			$trans_qnty_arr[$row[csf('po_breakdown_id')]]['yarn_trans']=$row[csf('transfer_in_qnty_yarn')]-$row[csf('transfer_out_qnty_yarn')];
			$trans_qnty_arr[$row[csf('po_breakdown_id')]]['knit_trans']=$row[csf('transfer_in_qnty_knit')]-$row[csf('transfer_out_qnty_knit')];
			
			$grey_receive_qnty_arr[$row[csf('po_breakdown_id')]]=$row[csf('grey_receive')];
			$grey_issue_qnty_arr[$row[csf('po_breakdown_id')]]=$row[csf('grey_issue')]+$row[csf('grey_issue_rollwise')];
		}*/
					
                    $i=1; $issue_to='';
                    $sql="select a.issue_number, a.issue_date, a.issue_purpose, a.knit_dye_source, a.knit_dye_company, a.booking_no, a.batch_no, sum(c.quantity) as quantity from inv_issue_master a, inv_grey_fabric_issue_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=16 and c.entry_form=16 and c.po_breakdown_id in($order_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id,  a.issue_number, a.issue_date, a.issue_purpose, a.knit_dye_source, a.knit_dye_company, a.booking_no, a.batch_no";
                    $result=sql_select($sql);
        			foreach($result as $row)
                    {
                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";	
                    
                        if($row[csf('knit_dye_source')]==1) 
                        {
                            $issue_to=$company_library[$row[csf('knit_dye_company')]]; 
                        }
                        else if($row['knit_dye_source']==3) 
                        {
                            $issue_to=$supplier_details[$row[csf('knit_dye_company')]];
                        }
                        else
                            $issue_to="&nbsp;";
                    
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="40"><? echo $i; ?></td>
                            <td width="120"><p><? echo $row[csf('issue_number')]; ?></p></td>
                            <td width="100"><? echo $yarn_issue_purpose[$row[csf('issue_purpose')]]; ?></td>
                            <td width="100"><p><? echo $issue_to; ?></p></td>
                            <td width="115"><? echo $row[csf('booking_no')]; ?>&nbsp;</td>
                            <td width="90"><p><? echo $batch_details[$row[csf('batch_no')]]; ?>&nbsp;</p></td>
                            <td width="80" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
                            <td width="100" align="right">
								<?
                                    if($row[csf('knit_dye_source')]!=3)
                                    {
                                        echo number_format($row[csf('quantity')],2);
                                        $total_issue_qnty+=$row[csf('quantity')];
                                    }
                                    else echo "&nbsp;";
                                ?>
                            </td>
                            <td align="right">
                                <?
                                    if($row[csf('knit_dye_source')]==3)
                                    {
                                        echo number_format($row[csf('quantity')],2);
                                        $total_issue_qnty_out+=$row[csf('quantity')];
                                    }
                                    else echo "&nbsp;";
                                ?>
                            </td>
                        </tr>
                    <?
                    $i++;
                    }
                    ?>
                    <tfoot>
                    	<tr>
                            <th colspan="7" align="right">Total</th>
                            <th align="right"><? echo number_format($total_issue_qnty,2); ?></th>
                            <th align="right"><? echo number_format($total_issue_qnty_out,2); ?></th>
                        </tr>
                        <tr>
                            <th colspan="7" align="right">Grand Total</th>
                            <th align="right" colspan="2"><? echo number_format($total_issue_qnty+$total_issue_qnty_out,2); ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>	
        </div>
	</fieldset>   
<?
exit();
}

if($action=="dye_qnty")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	
	$machine_arr = return_library_array("select id, machine_no as machine_name from lib_machine_name","id","machine_name");
?>
<script>

	function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="230px";
	}	
	
</script>	
	<div style="width:885px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:880px; margin-left:3px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="860" cellpadding="0" cellspacing="0">
				<thead>
					<th colspan="10"><b>Dyeing Info</b></th>
				</thead>
				<thead>
                	<th width="30">SL</th>
                    <th width="70">System Id</th>
                    <th width="80">Process End Date</th>
                    <th width="100">Batch No</th>
                    <th width="70">Dyeing Source</th>
                    <th width="120">Dyeing Company</th>
                    <th width="90">Receive Qnty</th>
                    <th width="190">Fabric Description</th>
                    <th>Machine Name</th>
				</thead>
             </table>
             <div style="width:877px; max-height:320px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="860" cellpadding="0" cellspacing="0">
                    <?
					$i=1; $total_dye_qnty=0; $dye_company='';
					$sql="select a.batch_no, b.item_description as febric_description, sum(b.batch_qnty) as quantity, c.id, c.company_id, c.process_end_date, c.machine_id from pro_batch_create_mst a,pro_batch_create_dtls b, pro_fab_subprocess c where a.id=b.mst_id and a.id=c.batch_id and a.color_id='$color' and c.load_unload_id=2 and c.entry_form=35 and b.po_id in($order_id) and a.batch_against<>2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id, a.batch_no, b.item_description, c.id, c.company_id, c.process_end_date, c.machine_id";
                    $result=sql_select($sql);
        			foreach($result as $row)
                    {
                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";	
                    
						$dye_company=$company_library[$row[csf('company_id')]]; 
                        $total_dye_qnty+=$row[csf('quantity')];
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="30"><? echo $i; ?></td>
                            <td width="70"><p><? echo $row[csf('id')]; ?></p></td>
                            <td width="80" align="center"><? echo change_date_format($row[csf('process_end_date')]); ?>&nbsp;</td>
                            <td width="100"><p><? echo $row[csf('batch_no')];//$batch_details[$row[csf('batch_id')]]; ?></p></td>
                            <td width="70"><? echo "Inhouse";//echo $knitting_source[$row[csf('dyeing_source')]]; ?></td>
                            <td width="120"><p><? echo $dye_company; ?></p></td>
                            <td width="90" align="right"><? echo number_format($row[csf('quantity')],2); ?></td>
                            <td width="190"><p><? echo $row[csf('febric_description')]; ?></p></td>
                            <td><p>&nbsp;<? echo $machine_arr[$row[csf('machine_id')]]; ?></p></td>
                        </tr>
                    <?
                    $i++;
                    }
                    ?>
                    <tfoot>
                        <th colspan="6" align="right">Total</th>
                        <th align="right"><? echo number_format($total_dye_qnty,2); ?></th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                    </tfoot>
                </table>
            </div>	
        </div>
	</fieldset>  
<?
exit();
}

if($action=="fabric_receive")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?>
<script>

	function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="230px";
	}	
	
</script>	
	<div style="width:885px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:880px; margin-left:3px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="860" cellpadding="0" cellspacing="0">
				<thead>
					<th colspan="9"><b>Fabric Receive Info</b></th>
				</thead>
				<thead>
                	<th width="30">SL</th>
                    <th width="120">System Id</th>
                    <th width="75">Rec. Date</th>
                    <th width="80">Rec. Basis</th>
                    <th width="90">Batch No</th>
                    <th width="90">Dyeing Source</th>
                    <th width="100">Dyeing Company</th>
                    <th width="90">Receive Qnty</th>
                    <th>Fabric Description</th>
				</thead>
             </table>
             <div style="width:877px; max-height:320px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="860" cellpadding="0" cellspacing="0">
                    <?
                    $i=1;
                    $total_fabric_recv_qnty=0; $dye_company='';
                    $sql="select a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, a.knitting_company, b.batch_id, b.prod_id, sum(c.quantity) as quantity from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=7 and c.entry_form=7 and c.po_breakdown_id in($order_id) and c.color_id='$color' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.id, a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, a.knitting_company, b.batch_id, b.prod_id";
                    $result=sql_select($sql);
        			foreach($result as $row)
                    {
                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";	
                    
                        if($row[csf('knitting_source')]==1) 
                        {

                            $dye_company=$company_library[$row[csf('knitting_company')]]; 
                        }
                        else if($row['knitting_source']==3) 
                        {
                            $dye_company=$supplier_details[$row[csf('knitting_company')]];
                        }
                        else
                            $dye_company="&nbsp;";
                    
                        $total_fabric_recv_qnty+=$row[csf('quantity')];
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="30"><? echo $i; ?></td>
                            <td width="120"><p><? echo $row[csf('recv_number')]; ?></p></td>
                            <td width="75" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                            <td width="80"><? echo $receive_basis_arr[$row[csf('receive_basis')]]; ?></td>
                            <td width="90"><p><? echo $batch_details[$row[csf('batch_id')]]; ?></p></td>
                            <td width="90"><? echo $knitting_source[$row[csf('knitting_source')]]; ?></td>
                            <td width="100"><p><? echo $dye_company; ?></p></td>
                            <td width="90" align="right"><? echo number_format($row[csf('quantity')],2); ?></td>
                            <td><p><? echo $product_details[$row[csf('prod_id')]]; ?></p></td>
                        </tr>
                    <?
                    $i++;
                    }
                    ?>
                    <tfoot>
                        <th colspan="7" align="right">Total</th>
                        <th align="right"><? echo number_format($total_fabric_recv_qnty,2); ?></th>
                        <th>&nbsp;</th>
                    </tfoot>
                </table>
            </div>	
        </div>
	</fieldset>   
<?
exit();
}

if($action=="fabric_purchase")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?>
<script>

	function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="230px";
	}	
	
</script>	
	<div style="width:885px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:880px; margin-left:3px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="860" cellpadding="0" cellspacing="0">
				<thead>
					<th colspan="9"><b>Fabric Receive Info</b></th>
				</thead>
				<thead>
                	<th width="30">SL</th>
                    <th width="120">System Id</th>
                    <th width="75">Rec. Date</th>
                    <th width="80">Rec. Basis</th>
                    <th width="90">Batch No</th>
                    <th width="90">Dyeing Source</th>
                    <th width="100">Dyeing Company</th>
                    <th width="90">Receive Qnty</th>
                    <th>Fabric Description</th>
				</thead>
             </table>
             <div style="width:877px; max-height:320px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="860" cellpadding="0" cellspacing="0">
                    <?
                    $i=1;
                    $total_fabric_recv_qnty=0; $dye_company='';
                    $sql="select a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, a.knitting_company, b.batch_id, b.prod_id, sum(c.quantity) as quantity from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(37,68) and c.entry_form in(37,68) and c.po_breakdown_id in($order_id) and c.color_id='$color' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.id, a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, a.knitting_company, b.batch_id, b.prod_id";// and a.receive_basis<>9
                    $result=sql_select($sql);
        			foreach($result as $row)
                    {
                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";	
                    
                        if($row[csf('knitting_source')]==1) 
                        {
                            $dye_company=$company_library[$row[csf('knitting_company')]]; 
                        }
                        else if($row['knitting_source']==3) 
                        {
                            $dye_company=$supplier_details[$row[csf('knitting_company')]];
                        }
                        else
                            $dye_company="&nbsp;";
                    
                        $total_fabric_recv_qnty+=$row[csf('quantity')];
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="30"><? echo $i; ?></td>
                            <td width="120"><p><? echo $row[csf('recv_number')]; ?></p></td>
                            <td width="75" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                            <td width="80"><? echo $receive_basis_arr[$row[csf('receive_basis')]]; ?></td>
                            <td width="90"><p><? echo $batch_details[$row[csf('batch_id')]]; ?></p></td>
                            <td width="90"><? echo $knitting_source[$row[csf('knitting_source')]]; ?></td>
                            <td width="100"><p><? echo $dye_company; ?></p></td>
                            <td width="90" align="right"><? echo number_format($row[csf('quantity')],2); ?></td>
                            <td><p><? echo $product_details[$row[csf('prod_id')]]; ?></p></td>
                        </tr>
                    <?
                    $i++;
                    }
                    ?>
                    <tfoot>
                        <th colspan="7" align="right">Total</th>
                        <th align="right"><? echo number_format($total_fabric_recv_qnty,2); ?></th>
                        <th>&nbsp;</th>
                    </tfoot>
                </table>
            </div>	
        </div>
	</fieldset>   
<?
exit();
}

if($action=="finish_delivery_to_store")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$ex_data=explode('_',$order_id);
	$color_arr = return_library_array("select id, color_name from lib_color","id","color_name");
?>
<script>

	var tableFilters = {
						   col_operation: {
						   id: ["value_delivery_qnty"],
						   col: [8],
						   operation: ["sum"],
						   write_method: ["innerHTML"]
						}
					}
	$(document).ready(function(e) {
		setFilterGrid('tbl_list_search',-1,tableFilters);
	});
		
	function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		$('#tbl_list_search tr:first').hide(); 
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="230px";
		
		$('#tbl_list_search tr:first').show();
	}	
	
</script>	
	<div style="width:720px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:720px;">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="720" cellpadding="0" cellspacing="0">
				<thead>
					<th colspan="9"><b>Finish Delivery To Store Info</b></th>
				</thead>
				<thead>
                	<th width="30">SL</th>
                    <th width="115">Challan No</th>
                    <th width="70">Delivery Date</th>
                    <th width="115">Production ID</th>
                    <th width="160">Product Details</th>
                    <th width="50">GSM</th>
                    <th width="50">Dia</th>
                    <th width="70">Color</th>
                    <th>Delivery Qnty</th>
				</thead>
             </table>
             <div style="width:740px; max-height:330px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="720" cellpadding="0" cellspacing="0" id="tbl_list_search">
                    <?
                    $i=1; $total_delivery_fin_qnty=0;
					
				    $sql="select a.sys_number_prefix_num, a.sys_number, a.delevery_date, b.grey_sys_number, b.product_id, b.construction, b.composition, b.gsm, b.dia, sum(b.current_delivery) as delivery_qty, c.product_name_details, c.color from pro_grey_prod_delivery_mst a, pro_grey_prod_delivery_dtls b, product_details_master c where a.id=b.mst_id and b.product_id=c.id and a.entry_form=54 and b.order_id in ($ex_data[0]) and c.color='$ex_data[1]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.sys_number_prefix_num, a.sys_number, a.delevery_date, b.grey_sys_number, b.product_id, b.construction, b.composition, b.gsm, b.dia, c.product_name_details, c.color";
                    $result=sql_select($sql);
        			foreach($result as $row)
                    {
                        if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="30"><? echo $i; ?></td>
                            <td width="115"><p><? echo $row[csf('sys_number')]; ?></p></td>
                            <td width="70"><p><? echo change_date_format($row[csf('delevery_date')]); ?></p></td>
                            <td width="115"><p><? echo $row[csf('grey_sys_number')]; ?></p></td>
                            <td width="160"><p><? echo $row[csf('product_name_details')]; ?>&nbsp;</p></td>
                            <td width="50"><? echo $row[csf('gsm')]; ?></td>
                            <td width="50"><? echo $row[csf('dia')]; ?></td>
                            <td width="70"><? echo $color_arr[$row[csf('color')]]; ?></td>
                            <td align="right"><? echo number_format($row[csf('delivery_qty')],2,'.',''); ?></td>
                        </tr>
                    <?
					$total_delivery_fin_qnty+=$row[csf('delivery_qty')];
                    $i++;
                    }
                    ?>
                 </table>
            </div>
            <table border="1" class="rpt_table" rules="all" width="720" cellpadding="0" cellspacing="0">  
                <tfoot>
                    <th width="30">&nbsp;</th>
                    <th width="115">&nbsp;</th>
                    <th width="70">&nbsp;</th>
                    <th width="115">&nbsp;</th>
                    <th width="160">&nbsp;</th>
                    <th width="50">&nbsp;</th>
                    <th width="50">&nbsp;</th>
                    <th width="70" align="right">Total</th>
                    <th align="right" id="value_delivery_qnty"><? echo number_format($total_delivery_fin_qnty,2,'.',''); ?></th>
                </tfoot>
            </table>	
        </div>
	</fieldset>   
<?
exit();
}

if($action=="issue_to_cut")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?>
<script>

	function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="230px";
	}	
	
</script>	
	<div style="width:740px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:740px; margin-left:7px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="730" cellpadding="0" cellspacing="0">
				<thead>
					<th colspan="7"><b>Issue To Cutting Info</b></th>
				</thead>
				<thead>
                	<th width="50">SL</th>
                    <th width="120">Issue No</th>
                    <th width="100">Challan No</th>
                    <th width="80">Issue Date</th>
                    <th width="120">Batch No</th>
                    <th width="110">Issue Qnty</th>
                    <th>Fabric Description</th>
				</thead>
             </table>
             <div style="width:738px; max-height:320px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="720" cellpadding="0" cellspacing="0">
                    <?
					
                    $i=1; $total_issue_to_cut_qnty=0;
                    $sql="select a.issue_number, a.issue_date, b.batch_id, b.prod_id, sum(c.quantity) as quantity, a.challan_no from inv_issue_master a, inv_finish_fabric_issue_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in (18,71) and c.entry_form in (18,71) and c.po_breakdown_id in($order_id) and c.color_id='$color' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.id, a.issue_number, a.issue_date, a.challan_no, b.batch_id, b.prod_id";
                    $result=sql_select($sql);
        			foreach($result as $row)
                    {
                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";	
                    
                        $total_issue_to_cut_qnty+=$row[csf('quantity')];
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="50"><? echo $i; ?></td>
                            <td width="120"><p><? echo $row[csf('issue_number')]; ?></p></td>
                            <td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>
                            <td width="80" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
                            <td width="120"><p><? echo $batch_details[$row[csf('batch_id')]]; ?></p></td>
                            <td width="110" align="right"><? echo number_format($row[csf('quantity')],2); ?></td>
                            <td><p><? echo $product_details[$row[csf('prod_id')]]; ?></p></td>
                        </tr>
                    <?
                    $i++;
                    }
                    ?>
                    <tfoot>
                        <th colspan="5" align="right">Total</th>
                        <th align="right"><? echo number_format($total_issue_to_cut_qnty,2); ?></th>
                        <th>&nbsp;</th>
                    </tfoot>
                </table>
            </div>	
        </div>
	</fieldset>   
<?
exit();
}

if($action=="yarn_trans")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$po_arr=return_library_array( "select id, po_number from wo_po_break_down", "id", "po_number"  );
?>
<script>

	function print_window()
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
	}	
	
</script>	
	<div style="width:675px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:670px; margin-left:7px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="650" cellpadding="0" cellspacing="0">
				<thead>
                	<tr>
                    	<th colspan="6">Transfer In</th>
                    </tr>
                    <tr>
                    	<th width="40">SL</th>
                        <th width="115">Transfer Id</th>
                        <th width="80">Transfer Date</th>
                        <th width="100">From Order</th>
                        <th width="170">Item Description</th>
                        <th>Transfer Qnty</th>
                    </tr>
				</thead>
                <?
                $i=1; $total_trans_in_qnty=0;
				$sql="select a.transfer_system_id, a.transfer_date, a.challan_no, a.from_order_id, b.from_prod_id, sum(c.quantity) as transfer_qnty, d.product_name_details from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, product_details_master d where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.item_category=1 and a.transfer_criteria=4 and c.trans_type=5 and c.entry_form=11 and c.po_breakdown_id in ($order_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id, c.prod_id, a.transfer_system_id, a.transfer_date, a.challan_no, a.from_order_id, b.from_prod_id, d.product_name_details";
                $result=sql_select($sql);
				foreach($result as $row)
				{
					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";	
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td width="40"><? echo $i; ?></td>
                    	<td width="115"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('transfer_date')]); ?></td>
                        <td width="100"><p><? echo $po_arr[$row[csf('from_order_id')]]; ?></p></td>
                        <td width="170"><p><? echo $row[csf('product_name_details')]; ?></p></td>
                        <td align="right"><? echo number_format($row[csf('transfer_qnty')],2); ?> </td>
                    </tr>
                <?
					$total_trans_in_qnty+=$row[csf('transfer_qnty')];
                $i++;
                }
                ?>
                <tr style="font-weight:bold">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right">Total</td>
                    <td align="right"><? echo number_format($total_trans_in_qnty,2);?></td>
                </tr>
                <thead>
                	<tr>
                    	<th colspan="6">Transfer Out</th>
                    </tr>
                    <tr>
                    	<th width="40">SL</th>
                        <th width="115">Transfer Id</th>
                        <th width="80">Transfer Date</th>
                        <th width="100">To Order</th>
                        <th width="170">Item Description</th>
                        <th>Transfer Qnty</th>
                    </tr>
				</thead>
                <?
                $total_trans_out_qnty=0;
				$sql="select a.transfer_system_id, a.transfer_date, a.challan_no, a.to_order_id, b.from_prod_id, sum(c.quantity) as transfer_qnty, d.product_name_details from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, product_details_master d where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.item_category=1 and a.transfer_criteria=4 and c.trans_type=6 and c.entry_form=11 and c.po_breakdown_id in ($order_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id, c.prod_id, a.transfer_system_id, a.transfer_date, a.challan_no, a.to_order_id, b.from_prod_id, d.product_name_details";
                $result=sql_select($sql);
				foreach($result as $row)
				{
					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";	
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td width="40"><? echo $i; ?></td>
                    	<td width="115"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('transfer_date')]); ?></td>
                        <td width="100"><p><? echo $po_arr[$row[csf('to_order_id')]]; ?></p></td>
                        <td width="170"><p><? echo $row[csf('product_name_details')]; ?></p></td>
                        <td align="right"><? echo number_format($row[csf('transfer_qnty')],2); ?> </td>
                    </tr>
                <?
					$total_trans_out_qnty+=$row[csf('transfer_qnty')];
                $i++;
                }
                ?>
                <tr style="font-weight:bold">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right">Total</td>
                    <td align="right"><? echo number_format($total_trans_out_qnty,2); ?></td>
                </tr>
                <tfoot>
                	<th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th>Net Transfer</th>
                    <th><? echo number_format($total_trans_in_qnty-$total_trans_out_qnty,2);?></th>
                </tfoot>
            </table>	
		</div>
	</fieldset>  
<?
exit();
}

if($action=="knit_trans")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$po_arr=return_library_array( "select id, po_number from wo_po_break_down", "id", "po_number"  );
?>
<script>

	function print_window()
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
	}	
	
</script>	
	<div style="width:675px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:670px; margin-left:7px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="650" cellpadding="0" cellspacing="0">
				<thead>
                	<tr>
                    	<th colspan="6">Transfer In</th>
                    </tr>
                    <tr>
                    	<th width="40">SL</th>
                        <th width="115">Transfer Id</th>
                        <th width="80">Transfer Date</th>
                        <th width="100">From Order</th>
                        <th width="170">Item Description</th>
                        <th>Transfer Qnty</th>
                    </tr>
				</thead>
                <?
                $i=1; $total_trans_in_qnty=0;
				$sql="select a.transfer_system_id, a.transfer_date, a.challan_no, a.from_order_id, b.from_prod_id, sum(c.quantity) as transfer_qnty, d.product_name_details from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, product_details_master d where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.item_category=13 and a.transfer_criteria=4 and c.trans_type=5 and c.entry_form=13 and c.po_breakdown_id in ($order_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id, c.prod_id, a.transfer_system_id, a.transfer_date, a.challan_no, a.from_order_id, b.from_prod_id, d.product_name_details";
                $result=sql_select($sql);
				foreach($result as $row)
				{
					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";	
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td width="40"><? echo $i; ?></td>
                    	<td width="115"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('transfer_date')]); ?></td>
                        <td width="100"><p><? echo $po_arr[$row[csf('from_order_id')]]; ?></p></td>
                        <td width="170"><p><? echo $row[csf('product_name_details')]; ?></p></td>
                        <td align="right"><? echo number_format($row[csf('transfer_qnty')],2); ?> </td>
                    </tr>
                <?
					$total_trans_in_qnty+=$row[csf('transfer_qnty')];
                $i++;
                }
                ?>
                <tr style="font-weight:bold">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right">Total</td>
                    <td align="right"><? echo number_format($total_trans_in_qnty,2);?></td>
                </tr>
                <thead>
                	<tr>
                    	<th colspan="6">Transfer Out</th>
                    </tr>
                    <tr>
                    	<th width="40">SL</th>
                        <th width="115">Transfer Id</th>
                        <th width="80">Transfer Date</th>
                        <th width="100">To Order</th>
                        <th width="170">Item Description</th>
                        <th>Transfer Qnty</th>
                    </tr>
				</thead>
                <?
                $total_trans_out_qnty=0;
				$sql="select a.transfer_system_id, a.transfer_date, a.challan_no, a.to_order_id, b.from_prod_id, d.product_name_details, sum(c.quantity) as transfer_qnty from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, product_details_master d where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.item_category=13 and a.transfer_criteria=4 and c.trans_type=6 and c.entry_form=13 and c.po_breakdown_id in ($order_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id, c.prod_id, a.transfer_system_id, a.transfer_date, a.challan_no, a.to_order_id, b.from_prod_id, d.product_name_details";
                $result=sql_select($sql);
				foreach($result as $row)
				{
					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";	
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td width="40"><? echo $i; ?></td>
                    	<td width="115"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('transfer_date')]); ?></td>
                        <td width="100"><p><? echo $po_arr[$row[csf('to_order_id')]]; ?></p></td>
                        <td width="170"><p><? echo $row[csf('product_name_details')]; ?></p></td>
                        <td align="right"><? echo number_format($row[csf('transfer_qnty')],2); ?> </td>
                    </tr>
                <?
					$total_trans_out_qnty+=$row[csf('transfer_qnty')];
                $i++;
                }
                ?>
                <tr style="font-weight:bold">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right">Total</td>
                    <td align="right"><? echo number_format($total_trans_out_qnty,2); ?></td>
                </tr>
                <tfoot>
                	<th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th>Net Transfer</th>
                    <th><? echo number_format($total_trans_in_qnty-$total_trans_out_qnty,2);?></th>
                </tfoot>
            </table>	
		</div>
	</fieldset>  
<?
exit();
}

if($action=="finish_trans")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$po_arr=return_library_array( "select id, po_number from wo_po_break_down", "id", "po_number"  );
?>
<script>

	function print_window()
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
	}	
	
</script>	
	<div style="width:675px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:670px; margin-left:7px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="650" cellpadding="0" cellspacing="0">
				<thead>
                	<tr>
                    	<th colspan="6">Transfer In</th>
                    </tr>
                    <tr>
                    	<th width="40">SL</th>
                        <th width="115">Transfer Id</th>
                        <th width="80">Transfer Date</th>
                        <th width="100">From Order</th>
                        <th width="170">Item Description</th>
                        <th>Transfer Qnty</th>
                    </tr>
				</thead>
                <?
                $i=1; $total_trans_in_qnty=0;
				$sql="select a.transfer_system_id, a.transfer_date, a.challan_no, a.from_order_id, b.from_prod_id, d.product_name_details, sum(c.quantity) as transfer_qnty from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, product_details_master d where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.item_category=2 and a.transfer_criteria=4 and c.trans_type=5 and c.entry_form=15 and c.po_breakdown_id in ($order_id) and c.color_id='$color' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id, c.prod_id, a.transfer_system_id, a.transfer_date, a.challan_no, a.from_order_id, b.from_prod_id, d.product_name_details";
                $result=sql_select($sql);
				foreach($result as $row)
				{
					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";	
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td width="40"><? echo $i; ?></td>
                    	<td width="115"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('transfer_date')]); ?></td>
                        <td width="100"><p><? echo $po_arr[$row[csf('from_order_id')]]; ?></p></td>
                        <td width="170"><p><? echo $row[csf('product_name_details')]; ?></p></td>
                        <td align="right"><? echo number_format($row[csf('transfer_qnty')],2); ?> </td>
                    </tr>
                <?
					$total_trans_in_qnty+=$row[csf('transfer_qnty')];
                $i++;
                }
                ?>
                <tr style="font-weight:bold">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right">Total</td>
                    <td align="right"><? echo number_format($total_trans_in_qnty,2);?></td>
                </tr>
                <thead>
                	<tr>
                    	<th colspan="6">Transfer Out</th>
                    </tr>
                    <tr>
                    	<th width="40">SL</th>
                        <th width="115">Transfer Id</th>
                        <th width="80">Transfer Date</th>
                        <th width="100">To Order</th>
                        <th width="170">Item Description</th>
                        <th>Transfer Qnty</th>
                    </tr>
				</thead>
                <?
                $total_trans_out_qnty=0;
				$sql="select a.transfer_system_id, a.transfer_date, a.challan_no, a.to_order_id, b.from_prod_id, d.product_name_details, sum(c.quantity) as transfer_qnty from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, product_details_master d where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.item_category=2 and a.transfer_criteria=4 and c.trans_type=6 and c.entry_form=15 and c.po_breakdown_id in ($order_id) and c.color_id='$color' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id, c.prod_id, a.transfer_system_id, a.transfer_date, a.challan_no, a.to_order_id, b.from_prod_id, d.product_name_details";
                $result=sql_select($sql);
				foreach($result as $row)
				{
					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";	
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td width="40"><? echo $i; ?></td>
                    	<td width="115"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('transfer_date')]); ?></td>
                        <td width="100"><p><? echo $po_arr[$row[csf('to_order_id')]]; ?></p></td>
                        <td width="170"><p><? echo $row[csf('product_name_details')]; ?></p></td>
                        <td align="right"><? echo number_format($row[csf('transfer_qnty')],2); ?> </td>
                    </tr>
                <?
					$total_trans_out_qnty+=$row[csf('transfer_qnty')];
                $i++;
                }
                ?>
                <tr style="font-weight:bold">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right">Total</td>
                    <td align="right"><? echo number_format($total_trans_out_qnty,2); ?></td>
                </tr>
                <tfoot>
                	<th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th>Net Transfer</th>
                    <th><? echo number_format($total_trans_in_qnty-$total_trans_out_qnty,2);?></th>
                </tfoot>
            </table>	
		</div>
	</fieldset>  
<?
exit();
}

?>