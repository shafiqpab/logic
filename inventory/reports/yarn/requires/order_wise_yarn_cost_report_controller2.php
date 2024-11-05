<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../../includes/common.php');
/*require_once('../../../../includes/class.reports.php');
require_once('../../../../includes/class.yarns.php')*/;
require_once('../../../../includes/class4/class.conditions.php');
require_once('../../../../includes/class4/class.reports.php');
require_once('../../../../includes/class4/class.yarns.php');


$user_name=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );
	exit();
}

$tmplte=explode("**",$data);

if ($tmplte[0]=="viewtemplate") $template=$tmplte[1]; else $template=$lib_report_template_array[$_SESSION['menu_id']]['0'];
if ($template=="") $template=1;

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
 	if($template==1)
	{
		$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
		$buyer_short_name_library=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
		$yarn_count_details=return_library_array( "select id, yarn_count from lib_yarn_count", "id", "yarn_count");

		$costing_per_id_library=array();
		$costing_sql=sql_select("select job_no, costing_per, costing_date from wo_pre_cost_mst");
		foreach($costing_sql as $row)
		{
			$costing_per_id_library[$row[csf('job_no')]]=$row[csf('costing_per')];
		}

		$company_name= str_replace("'","",$cbo_company_name);
		$cbo_date_type= str_replace("'","",$cbo_date_type);
		$cbo_year=str_replace("'","",$cbo_year);
		if($db_type==0)
		{
			if(trim($cbo_year)!=0)
			{
				$jobYearCond="and YEAR(a.insert_date)=$cbo_year";
			}else{
				$jobYearCond="";
			}

		}
		else if($db_type==2)
		{
			if(trim($cbo_year)!=0)
			{
				$jobYearCond=" and to_char(a.insert_date,'YYYY')=$cbo_year";
			}
			else{
				$jobYearCond="";
			}
		}

		//if(str_replace("'","",$cbo_buyer_name)==0) $buyer_name="%%"; else $buyer_name=str_replace("'","",$cbo_buyer_name);
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

		$txt_search_string=str_replace("'","",$txt_search_string);
		if(trim($txt_search_string)!="") $search_string="%".trim($txt_search_string)."%"; else $search_string="%%";
		if(trim($txt_search_string)!="")
		{
			$po_id_cond=" and b.po_number like '".trim(str_replace("'","",$txt_search_string))."%'";
			$po_id_cond2=" and c.po_number like '".trim(str_replace("'","",$txt_search_string))."%'";
		}
		else
		{
			$po_id_cond="";
			$po_id_cond2="";
		}

		//echo $po_style_src_cond; die;
		$start_date=str_replace("'","",trim($txt_date_from));
		$end_date=str_replace("'","",trim($txt_date_to));
		if($cbo_date_type==2)
		{
			if($start_date!="" && $end_date!="")
			{
				//$str_cond="and b.pub_shipment_date between '$start_date' and '$end_date'";
			}


				$ref_closing_po_arr=return_library_array( "select b.inv_pur_req_mst_id as po_id,min(b.closing_date) as closing_date
				from inv_reference_closing b where  b.reference_type=163 and b.closing_status=1 and b.status_active=1 and b.is_deleted=0 and b.closing_date  between '$start_date' and '$end_date' group by b.inv_pur_req_mst_id", "po_id", "closing_date");


				foreach($ref_closing_po_arr as $po_id=>$ids){
					$poArr[$po_id]=$po_id;
				}

				 $ref_po_cond_for_in2=where_con_using_array($poArr,0,"b.po_break_down_id");
				 $ref_po_cond_for_in3=where_con_using_array($poArr,0,"a.po_breakdown_id");
				 $ref_po_cond_for_in=where_con_using_array($poArr,0,"b.id");
				 $ship_cond="and b.shiping_status=3";
		}
		else
		{
			if($start_date!="" && $end_date!="")
			{
				$str_cond="and b.pub_shipment_date between '$start_date' and '$end_date'";
			}
			else
				$str_cond="";
		}

		$txt_job_no=str_replace("'","",$txt_job_no);
		$job_no_cond="";$job_no_cond2="";
		if(trim($txt_job_no)!="")
		{
			$job_no=trim($txt_job_no);
			$job_no_cond=" and a.job_no_prefix_num=$job_no";
			$job_no_cond2=" and b.job_no like '%$job_no%'";
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
		if(trim($cbo_shipping_status)==0) $shipping_status_cond=""; else $shipping_status_cond=" and b.shiping_status='$cbo_shipping_status'";

		$dataArrayYarn=array();
		$yarn_sql="select job_no, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, sum(cons_qnty) as qty, sum(avg_cons_qnty) as qnty, sum(amount) as amnt, sum(rate*avg_cons_qnty) as amount from wo_pre_cost_fab_yarn_cost_dtls where status_active=1 and is_deleted=0 group by job_no, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id";
		$resultYarn=sql_select($yarn_sql);
		foreach($resultYarn as $yarnRow)
		{
			$dataArrayYarn[$yarnRow[csf('job_no')]].=$yarnRow[csf('count_id')]."**".$yarnRow[csf('copm_one_id')]."**".$yarnRow[csf('percent_one')]."**".$yarnRow[csf('copm_two_id')]."**".$yarnRow[csf('percent_two')]."**".$yarnRow[csf('type_id')]."**".$yarnRow[csf('qnty')]."**".$yarnRow[csf('amount')].",";
		}

		$avg_rate_array=return_library_array( "select id, avg_rate_per_unit from product_details_master where item_category_id=1", "id", "avg_rate_per_unit"  );
		$receive_array=array();
		/*$sql_receive="select prod_id, sum(order_qnty) as qty, sum(order_amount) as amnt from inv_transaction where transaction_type=1 and item_category=1 and status_active=1 and is_deleted=0 group by prod_id";
		$resultReceive = sql_select($sql_receive);
		foreach($resultReceive as $invRow)
		{
			$avg_rate=$invRow[csf('amnt')]/$invRow[csf('qty')];
			$receive_array[$invRow[csf('prod_id')]]=$avg_rate;
		}*/

		$ex_rate=76;
		$exchange_rate=set_conversion_rate( 2, date('d-m-Y'),$company_name);

		/*$yarnIssDataArray=sql_select("select a.po_breakdown_id, a.prod_id, b.id as trans_id, b.cons_rate,
					sum(CASE WHEN a.entry_form ='3' and a.trans_type=2 and a.issue_purpose!=2 THEN a.quantity ELSE 0 END) AS yarn_iss_qty,
					sum(CASE WHEN a.entry_form ='9' and a.trans_type=4 THEN a.quantity ELSE 0 END) AS yarn_iss_return_qty,
					sum(CASE WHEN a.entry_form ='11' and a.trans_type=5 THEN a.quantity ELSE 0 END) AS trans_in_qty_yarn,
					sum(CASE WHEN a.entry_form ='11' and a.trans_type=6 THEN a.quantity ELSE 0 END) AS trans_out_qty_yarn
					from order_wise_pro_details a, inv_transaction b
					where a.trans_id=b.id and a.trans_type in(2,4,5,6) and a.status_active=1 and a.is_deleted=0 and b.company_id=$company_name $ref_po_cond_for_in3
					group by a.po_breakdown_id, a.prod_id, b.id, b.cons_rate");*/

		$yarnIssDataArray= sql_select("SELECT a.id as propotion_id,a.po_breakdown_id, a.prod_id, c.dyed_type, b.id AS trans_id, b.cons_rate, CASE WHEN     a.entry_form = '3' AND a.trans_type = 2 AND a.issue_purpose != 2 THEN a.quantity ELSE 0 END AS yarn_iss_qty,CASE WHEN a.entry_form = '11' AND a.trans_type = 5 THEN a.quantity ELSE 0 END AS trans_in_qty_yarn,CASE WHEN a.entry_form = '11' AND a.trans_type = 6 THEN a.quantity ELSE 0 END AS trans_out_qty_yarn, e.dye_charge FROM  inv_transaction       b LEFT JOIN inv_mrr_wise_issue_details d ON b.id = d.issue_trans_id LEFT JOIN inv_transaction e ON e.id = d.recv_trans_id, product_details_master c, order_wise_pro_details a WHERE a.trans_id = b.id AND b.prod_id = c.id AND a.trans_type IN (2, 5, 6) AND a.status_active = 1 AND a.is_deleted = 0 AND b.company_id = $company_name $ref_po_cond_for_in3 GROUP BY a.id,a.po_breakdown_id, a.prod_id, c.dyed_type, b.id, b.cons_rate, e.dye_charge, a.quantity, a.entry_form, a.trans_type, a.issue_purpose");

		$checkIssuePkId = array();
		foreach($yarnIssDataArray as $invRow)
		{
			if($checkIssuePkId[$invRow[csf('propotion_id')]]=="")
			{
				$checkIssuePkId[$invRow[csf('propotion_id')]] = $invRow[csf('propotion_id')];
				$iss_qty=($invRow[csf('yarn_iss_qty')]+$invRow[csf('trans_in_qty_yarn')])-($invRow[csf('trans_out_qty_yarn')]);

				$dataArrayYarnIssue[$invRow[csf('po_breakdown_id')]][1]+=$iss_qty;

				$rate=0;

				if($invRow[csf('dyed_type')]==1)
				{
					$rate=($invRow[csf('cons_rate')]-$invRow[csf('dye_charge')])/$exchange_rate;
				}else
				{
					$rate=($invRow[csf('cons_rate')])/$exchange_rate;
				}

				$dataArrayYarnIssue[$invRow[csf('po_breakdown_id')]][2]+=($iss_qty*$rate);
			}

		}

		$sql_issue_return = sql_select("SELECT  a.id as propotion_id, a.po_breakdown_id, b.id AS prod_id, b.dyed_type, c.id AS trans_id, c.cons_rate, f.dye_charge, a.quantity AS yarn_iss_return_qty FROM  order_wise_pro_details  a, product_details_master    b, inv_transaction  c, inv_transaction d, inv_mrr_wise_issue_details e, inv_transaction  f WHERE  c.id = a.trans_id AND a.prod_id = b.id AND d.mst_id = c.issue_id AND d.prod_id = a.prod_id AND d.id = e.issue_trans_id AND e.recv_trans_id = f.id AND c.transaction_type = 4 AND c.item_category = 1 AND b.item_category_id = 1 AND a.trans_type = 4 AND a.entry_form = 9 AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND a.issue_purpose != 2 AND c.company_id=$company_name $ref_po_cond_for_in3 GROUP BY  a.id, a.po_breakdown_id, b.id, b.dyed_type, c.id, c.cons_rate, f.dye_charge,a.quantity");

		$checkRtnPkId = array();
		foreach($sql_issue_return as $invRow)
		{
			if($checkRtnPkId[$invRow[csf('propotion_id')]]=="")
			{
				$issue_rtn_qty = $invRow[csf('yarn_iss_return_qty')];

				$dataArrayYarnIssueReturn[$invRow[csf('po_breakdown_id')]][1]+=$issue_rtn_qty;
				$rate=0;

				if($invRow[csf('dyed_type')]==1)
				{
					$rate=($invRow[csf('cons_rate')]-$invRow[csf('dye_charge')])/$exchange_rate;
				}else
				{
					$rate=($invRow[csf('cons_rate')])/$exchange_rate;
				}

				$dataArrayYarnIssueReturn[$invRow[csf('po_breakdown_id')]][2]+=($issue_rtn_qty*$rate);
			}
		}

		//var_dump($dataArrayYarnIssue[55286][2]);


		$booking_print_arr=array();
		$booking_print_sql=sql_select("select report_id, format_id from lib_report_template where template_name='$company_name' and module_id=2 and report_id in (1,2,3) and is_deleted=0 and status_active=1");
		foreach($booking_print_sql as $print_id)
		{
			$booking_print_arr[$print_id[csf('report_id')]]=(int)$print_id[csf('format_id')];
		}

		if($db_type==0) $year_field="YEAR(a.insert_date) as year";
		else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
		else $year_field="";//defined Later

		$sql="select a.company_name, a.buyer_name, $year_field, a.job_no_prefix_num, a.job_no, a.style_ref_no, a.gmts_item_id, a.order_uom, a.total_set_qnty as ratio, b.id as po_id, b.po_number, b.po_quantity as po_qnty, b.pub_shipment_date, b.shiping_status, b.insert_date, b.po_received_date, b.plan_cut, b.grouping from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name='$company_name' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $buyer_id_cond $str_cond $year_cond $job_no_cond $po_id_cond $jobYearCond $shipping_status_cond $ref_po_cond_for_in $ship_cond order by b.pub_shipment_date, a.job_no_prefix_num, b.id";

		ob_start();

		$dataArrayWo=array();
		$sql_wo="select b.po_break_down_id, a.id, a.booking_no, a.insert_date, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.booking_no_prefix_num, a.job_no, a.is_short, a.is_approved, b.fabric_color_id, sum(b.fin_fab_qnty) as req_qnty, sum(b.grey_fab_qnty) as grey_req_qnty,a.entry_form from wo_booking_mst a, wo_booking_dtls b,wo_po_break_down c where a.booking_no=b.booking_no and b.po_break_down_id=c.id and a.item_category in(2,13) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $color_cond_search $po_id_cond2 $ref_po_cond_for_in2 $job_no_cond2 group by b.po_break_down_id, a.id, a.booking_no, a.insert_date, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.booking_no_prefix_num, a.job_no, a.is_short, a.is_approved, b.fabric_color_id,a.entry_form";

		//echo $sql_wo;

		$resultWo=sql_select($sql_wo);
		foreach($resultWo as $woRow)
		{
			$dataArrayWo[$woRow[csf('po_break_down_id')]].=$woRow[csf('id')]."**".$woRow[csf('booking_no')]."**".$woRow[csf('insert_date')]."**".$woRow[csf('item_category')]."**".$woRow[csf('fabric_source')]."**".$woRow[csf('company_id')]."**".$woRow[csf('booking_type')]."**".$woRow[csf('booking_no_prefix_num')]."**".$woRow[csf('job_no')]."**".$woRow[csf('is_short')]."**".$woRow[csf('is_approved')]."**".$woRow[csf('fabric_color_id')]."**".$woRow[csf('req_qnty')]."**".$woRow[csf('grey_req_qnty')].",";
			$bookingEntryFromArr[$woRow[csf('booking_no')]]=$woRow[csf('entry_form')];

		}
		unset($resultWo);

		//echo "<pre>";
		//print_r($dataArrayWo);

		?>
        <fieldset>
            <table width="2375">
                <tr class="form_caption">
                    <td colspan="21" align="center">Order Wise Yarn Cost Report</td>
                </tr>
            </table>
            <table style="margin-top:10px" id="table_header_1" class="rpt_table" width="2375" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                	<tr>
                        <th width="40" rowspan="2">SL</th>
                        <th width="125" rowspan="2">Main Booking</th>
                        <th width="125" rowspan="2">Sample Booking</th>
                        <th width="70" rowspan="2">Buyer</th>
                        <th width="60" rowspan="2">Job Year</th>
                        <th width="70" rowspan="2">Job No</th>
                        <th width="100" rowspan="2">Internal Ref.</th>
                        <th width="100" rowspan="2">PO No</th>
                        <th width="110" rowspan="2">Style Name</th>
                        <th width="120" rowspan="2">Garments Item</th>
                        <th width="50" rowspan="2">UOM</th>
                        <th width="100" rowspan="2">PO Quantity [Pcs]</th>
                        <th width="80" rowspan="2">Ref.Close Date</th>
                        <th width="80" rowspan="2">Shipment Date</th>
                        <th width="100" rowspan="2">Shipping Status
                            <select name="cbo_shipping_status" id="cbo_shipping_status" class="combo_boxes" style="width:85%" onchange="fn_report_generated(2);">
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
                        <th colspan="3">Yarn Info</th>
                        <th colspan="2">Budgeted/Pre-Cost</th>
                        <th rowspan="2" width="110">Booking Qty</th>
                        <th colspan="2">Actual</th>
                        <th colspan="2">Balance</th>
                    </tr>
                    <tr>
                    	<th width="70">Count</th>
                        <th width="110">Composition</th>
                        <th width="80">Type</th>
                        <th width="110">Required<br/><font style="font-size:9px; font-weight:100">(As Per Pre-Cost)</font></th>
                        <th width="110">Cost ($)</th>
                        <th width="110">Yarn Issued</th>
                        <th width="110">Actual Cost ($)</th>

                        <th width="110">Qty. <br/><font style="font-size:9px; font-weight:100">(Req. -Issue)</font></th>
                        <th>Cost ($) <br/><font style="font-size:9px; font-weight:100">(Budget - Actual)</font></th>
                    </tr>
                </thead>
            </table>
            <div style="width:2395px; max-height:400px; overflow-y:scroll" id="scroll_body">
                <table class="rpt_table" width="2375" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
                    <?
					if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
					{
						if($db_type==0)
						{
							$start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
							$end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
							//$date_cond=" and b.update_date between '".$start_date."' and '".$end_date." 23:59:59'";
						}
						else if($db_type==2)
						{
						 $start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
							$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
							//$date_cond=" and b.update_date between '".$start_date."' and '".$end_date." 11:59:59 PM'";
						}
					}

                        $nameArray=sql_select($sql);
						/*$jobArr=array();
						foreach($nameArray as $nameArrayRow)
						{
							$jobArr[]=$nameArrayRow[csf('job_no')];
						}*/
						//$yarn=new yarn($jobArr,'job');
						//$OrderWiseYarnQtyAndAmountArray = $yarn->getOrderWiseYarnQtyAndAmountArray();

						 $condition= new condition();
						 if(str_replace("'","",$company_name)>0)
						 {
						 	$condition->company_name("=$company_name");
						 }

						 $job_no=str_replace("'","",$txt_job_no);
						 if(str_replace("'","",$txt_job_no) !='')
						 {
							  $condition->job_no_prefix_num(" in($job_no)");
						 }
						 if(str_replace("'","",$cbo_buyer_name)>0)
						 {
				  			$condition->buyer_name("=$cbo_buyer_name");
						 }

						 if(str_replace("'","",$cbo_year) !=0)
						 {
				 			 $condition->job_year("$jobYearCond");
						 }

						 if( str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!='')
						 {
				  			$condition->pub_shipment_date(" between '$start_date' and '$end_date'");
			 			 }

						 if(str_replace("'","",$txt_search_string)!='')
						 {
						 	$po_number_txt = trim($txt_search_string);
							$condition->po_number("='".$po_number_txt."'");
						 }

						$condition->init();

						$yarn= new yarn($condition);
						//echo $yarn->getQuery();die;
						$OrderWiseYarnQtyAndAmountArray=$yarn->getOrderWiseYarnQtyAndAmountArray();
						//$yarn_costing_arr=$yarn->getOrderWiseYarnAmountArray();

						// print_r($yarn_costing_arr);
						$i=1; $tot_order_qnty=0; $tot_mkt_required=0; $tot_yarn_issue_qnty=0; $tot_yarn_issue_cost=0; $tot_booking_qty=0; $tot_booking_cost=0;
                        foreach($nameArray as $row)
                        {
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
							$yarn_issued=0;
							$yarn_data_array=array();

							//$job_mkt_required=$yarn->getOrderWiseYarnQty($row[csf('po_id')]);
							//$job_mkt_required_cost=$yarn->getOrderWiseYarnAmount($row[csf('po_id')]);;
							$job_mkt_required=$OrderWiseYarnQtyAndAmountArray[$row[csf('po_id')]]['qty'];
							$job_mkt_required_cost=$OrderWiseYarnQtyAndAmountArray[$row[csf('po_id')]]['amount'];
                            if($costing_per_id_library[$row[csf('job_no')]]==1) $dzn_qnty=12;
                            else if($costing_per_id_library[$row[csf('job_no')]]==3) $dzn_qnty=12*2;
                            else if($costing_per_id_library[$row[csf('job_no')]]==4) $dzn_qnty=12*3;
                            else if($costing_per_id_library[$row[csf('job_no')]]==5) $dzn_qnty=12*4;
                            else $dzn_qnty=1;

                            $dzn_qnty=$dzn_qnty*$row[csf('ratio')];
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
								$amnt=$yarnRow[7];

                                //$mkt_required=$plan_cut_qnty*($qnty/$dzn_qnty);
								//$mkt_required_cost=$plan_cut_qnty*($amnt/$dzn_qnty);
                                //$job_mkt_required+=$mkt_required;
								//$job_mkt_required_cost+=$mkt_required_cost;

								$yarn_data_array['count'][]=$yarn_count_details[$count_id];
								$yarn_data_array['type'][]=$yarn_type[$type_id];

								if($percent_two!=0)
								{
									$compos=$composition[$copm_one_id]." ".$percent_one." %"." ".$composition[$copm_two_id]." ".$percent_two." %";
								}
								else
								{
									$compos=$composition[$copm_one_id]." ".$percent_one." %"." ".$composition[$copm_two_id];
								}

								$yarn_data_array['comp'][]=$compos;
                            }

                            if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                            $buyer_name_array[$row[csf('buyer_name')]]=$buyer_short_name_library[$row[csf('buyer_name')]];

							$gmts_item='';
							$gmts_item_id=explode(",",$row[csf('gmts_item_id')]);
							foreach($gmts_item_id as $item_id)
							{
								if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
							}

							$order_qnty_in_pcs=$row[csf('po_qnty')]*$row[csf('ratio')];
							//$booking_qty=$dataArrayWo[$row[csf('po_id')]]['qty'];
							//$booking_no=implode(",",array_unique(explode(",",chop($dataArrayWo[$row[csf('po_id')]]['bn'],','))));

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
										$page=0;
										if($booking_type==4) $page=3;
										else
										{
											if($is_short==1) $page=2;
											else $page=1;
										}
										//echo $booking_print_arr[$page].'<br>'.$page; //die;
										$action_name_m=""; $action_name_s=""; $action_name_sm="";
										$print_permission_ex=explode(',',$booking_print_arr[$page]);
										foreach($print_permission_ex as $print_id)
										{
											if($booking_type==4)
											{
												$action_name_sm="show_fabric_booking_report";
											}
											else
											{
												if($is_short==1)
												{
													//echo $print_id;
													if($print_id==8) $action_name_s="show_fabric_booking_report";
													if($print_id==9) $action_name_s="show_fabric_booking_report3";
													if($print_id==10) $action_name_s="show_fabric_booking_report4";
													$entryForm=$bookingEntryFromArr[$booking_no];
												}
												else
												{
													if($print_id==1) $action_name_m="show_fabric_booking_report_gr";
													if($print_id==2) $action_name_m="show_fabric_booking_report";
													if($print_id==3) $action_name_m="show_fabric_booking_report3";
													if($print_id==4) $action_name_m="show_fabric_booking_report1";
													if($print_id==5) $action_name_m="show_fabric_booking_report2";
													if($print_id==6) $action_name_m="show_fabric_booking_report4";
													if($print_id==7) $action_name_m="show_fabric_booking_report5";
													if($print_id==28) $action_name_m="show_fabric_booking_report_akh";
													if($print_id==719) $action_name_m="show_fabric_booking_report16";
													$entryForm=$bookingEntryFromArr[$booking_no];
												}
											}
										}

										if($booking_type==4)
										{
											$sample_booking.="<a href='##' style='color:#000' onclick=\"generate_worder_report('3','".$booking_no."','".$company_id."','".$row[csf('po_id')]."','".$item_category."','".$fabric_source."','".$job_no."','".$is_approved."','".$action_name_sm."','".$entryForm."')\"><font style='font-weight:bold' color='$wo_color;'>".$booking_no."(".$system_date.")"."</font></a><br>";
											$sample_booking_excel.="<font style='font-weight:bold' color='$wo_color;'>".$booking_no."(".$system_date.")"."</font><br>";
										}
										else
										{
											if($is_short==1)
											{
												$pre="S";
												$action_name=$action_name_s;
											}
											else
											{
												$pre="M";
												$action_name=$action_name_m;
											}


											$main_booking.="<a href='##' style='color:#000' onclick=\"generate_worder_report('".$is_short."','".$booking_no."','".$company_id."','".$row[csf('po_id')]."','".$item_category."','".$fabric_source."','".$job_no."','".$is_approved."','".$action_name."','".$entryForm."')\"><font style='font-weight:bold' color='$wo_color;'>".$booking_no."(".$system_date.")".$pre."</font></a><br>";
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

							$yarn_iss_qty=$dataArrayYarnIssue[$row[csf('po_id')]][1]-$dataArrayYarnIssueReturn[$row[csf('po_id')]][1];
							$yarn_iss_cost=$dataArrayYarnIssue[$row[csf('po_id')]][2]-$dataArrayYarnIssueReturn[$row[csf('po_id')]][2];

							if(number_format($required_qnty,2,'.','') > number_format($job_mkt_required,2,'.','')) {$bgcolor_booking='#FF0000';} else{ $bgcolor_booking='';}
							if(number_format($yarn_iss_cost,2,'.','') > number_format($job_mkt_required_cost,2,'.','')) {$bgcolor_cost='#FF0000'; } else{ $bgcolor_cost='';}

                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                <td width="40"><? echo $i; ?></td>
                                <td width="125"><div style="word-wrap:break-word; width:125px"><? echo $main_booking; ?></div></td>
                                <td width="125"><div style="word-wrap:break-word; width:125px"><? echo $sample_booking; ?></div></td>
                                <td width="70"><div style="word-wrap:break-word; width:70px"><? echo $buyer_short_name_library[$row[csf('buyer_name')]]; ?></div></td>
                                <td width="60" align="center"><? echo $row[csf('year')]; ?></td>
                                <td width="70" align="center"><? echo $row[csf('job_no_prefix_num')]; ?></td>
                                <td width="100" align="center"><? echo $row[csf('grouping')]; ?></td>
                                <td width="100" style="word-break:break-all;"><? echo $row[csf('po_number')]; ?></td>
                                <td width="110" style="word-break:break-all;"><? echo $row[csf('style_ref_no')]; ?></td>
                                <td width="120" style="word-break:break-all;"><? echo $gmts_item; ?></td>
                                <td width="50" align="center"><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?></td>
                        		<td width="98" align="right" style="padding-right:2px"><? echo $order_qnty_in_pcs; ?></td>
                        		<td width="80" align="center"><? echo change_date_format($ref_closing_po_arr[$row[csf('po_id')]]); ?></td>
                                <td width="80" align="center"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
                        		<td width="100" style="word-break:break-all;"><? echo $shipment_status[$row[csf('shiping_status')]]; ?></td>
                                <td width="70">
                                    <?
										$d=1;
                                        foreach($yarn_data_array['count'] as $yarn_count_value)
                                        {
											if($d!=1)
											{
												echo "<hr/>";
											}
                                        	echo $yarn_count_value;
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
                                                    echo "<hr/>";
                                                }
                                                echo $yarn_composition_value;
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
                                                   echo "<hr/>";
                                                }

                                                echo $yarn_type_value;
                                             	$d++;
                                             }
                                        ?>
                                    </p>
                                </td>
                                <td width="108" align="right" style="padding-right:2px"><a href="#report_details" onClick="openmypage('<? echo $row[csf('po_id')]; ?>','yarn_req_popup','Yarn Req. Details')"><? echo number_format($job_mkt_required,2,'.',''); ?></a></td>
                                <td width="108" align="right" style="padding-right:2px"><? echo number_format($job_mkt_required_cost,2,'.',''); ?></td>
                                <td width="108" align="right" style="padding-right:2px" bgcolor="<? echo $bgcolor_booking;?>"><? echo number_format($required_qnty,2,'.',''); ?></td>
                                <td width="108" align="right" style="padding-right:2px"><a href="#report_details" onClick="openmypage('<? echo $row[csf('po_id')]; ?>','yarn_issue_cost','Yarn Issue Cost Details','<? echo $company_name; ?>')"><? echo number_format($yarn_iss_qty,2); ?></a></td>
                                <td width="108" align="right" style="padding-right:2px" bgcolor="<? echo $bgcolor_cost; ?>"><? echo number_format($yarn_iss_cost,2,'.',''); ?></td>
                                <td width="108" align="right" style="padding-right:2px"><? $req_bal=$job_mkt_required-$yarn_iss_qty; echo number_format($req_bal,2); ?></a></td>
                                <td align="right" style="padding-right:2px"><? $cost_bal=$job_mkt_required_cost-$yarn_iss_cost; echo number_format($cost_bal,2,'.',''); ?></td>
                            </tr>
                        <?
							$tot_order_qnty+=$order_qnty_in_pcs;
							$tot_mkt_required+=$job_mkt_required;
							$tot_booking_qty+=$booking_qty;
							$tot_required_cost+=$job_mkt_required_cost;
							$tot_yarn_iss_qty+=$yarn_iss_qty;
							$tot_yarn_iss_cost+=$yarn_iss_cost;

							$tot_req_bal_qty+=$req_bal;
							$tot_cost_bal+=$req_bal;
                            $i++;
                        }// end main query
                    ?>
                    </table>
                </div>
                <table width="2375" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                    <tfoot>
                        <tr>
                        	<th width="40">&nbsp;</th>
                            <th width="125">&nbsp;</th>
                            <th width="125">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="60">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="100">&nbsp;</th>
                            <th width="100">&nbsp;</th>
                            <th width="110">&nbsp;</th>
                            <th width="120">&nbsp;</th>
                            <th width="50">&nbsp;</th>
                            <th width="100" id="total_order_qnty"><? echo number_format($tot_order_qnty,0); ?></th>
                            <th width="80">&nbsp;</th>
                            <th width="80">&nbsp;</th>
                            <th width="100">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="110">&nbsp;</th>
                            <th width="80">&nbsp;</th>
                            <th width="110" id="value_tot_mkt_required"><? echo number_format($tot_mkt_required,2); ?></th>
                            <th width="110" id="value_tot_required_cost"><? echo number_format($tot_required_cost,2); ?></th>
                            <th width="110" id="value_tot_booking_qty"><? echo number_format($tot_booking_qty,2); ?></th>
                            <th width="110" id="value_yarn_iss_qty"><? echo number_format($tot_yarn_iss_qty,2); ?></th>
                            <th width="110" id="value_yarn_iss_cost"><? echo number_format($tot_yarn_iss_cost,2); ?></th>
                            <th width="110" id="value_req_bal_qty"><? echo number_format($tot_req_bal_qty,2); ?></th>
                            <th id="value_cost_bal_cost"><? echo number_format($tot_cost_bal,2); ?></th>
                        </tr>
                    </tfoot>
                </table>
        </fieldset>
		<?
	}

	foreach (glob(".../../../../ext_resource/tmp_report/$user_name*.xls") as $filename)
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$html=ob_get_contents();
	$name=time();
	$filename="../../../../ext_resource/tmp_report/".$user_name."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,$html);
	$filename="../../../ext_resource/tmp_report/".$user_name."_".$name.".xls";
	echo "$total_data****$filename";
	exit();
}

if($action=="report_generate_2")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

 	if($template==1)
	{
		$company_library=return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name"  );
		$buyer_short_name_library=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
		$yarn_count_details=return_library_array( "select id, yarn_count from lib_yarn_count", "id", "yarn_count");

		$costing_per_id_library=array();
		$costing_sql=sql_select("select job_no, costing_per, costing_date from wo_pre_cost_mst");
		foreach($costing_sql as $row)
		{
			$costing_per_id_library[$row[csf('job_no')]]=$row[csf('costing_per')];
		}

		$company_name= str_replace("'","",$cbo_company_name);
		$cbo_date_type= str_replace("'","",$cbo_date_type);
		$cbo_year=str_replace("'","",$cbo_year);
		if($db_type==0)
		{
			if(trim($cbo_year)!=0)
			{
				$jobYearCond="and YEAR(a.insert_date)=$cbo_year";
			}else{
				$jobYearCond="";
			}

		}
		else if($db_type==2)
		{
			if(trim($cbo_year)!=0)
			{
				$jobYearCond=" and to_char(a.insert_date,'YYYY')=$cbo_year";
			}
			else{
				$jobYearCond="";
			}
		}

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

		$txt_search_string=str_replace("'","",$txt_search_string);
		if(trim($txt_search_string)!="") $search_string="%".trim($txt_search_string)."%"; else $search_string="%%";
		if(trim($txt_search_string)!="")
		{
			$po_id_cond=" and b.po_number like '".trim(str_replace("'","",$txt_search_string))."%'";
			$po_id_cond2=" and c.po_number like '".trim(str_replace("'","",$txt_search_string))."%'";
		}
		else
		{
			$po_id_cond="";
			$po_id_cond2="";
		}

		//echo $po_style_src_cond; die;
		$start_date=str_replace("'","",trim($txt_date_from));
		$end_date=str_replace("'","",trim($txt_date_to));
		if($cbo_date_type==2)
		{
			if($start_date!="" && $end_date!="")
			{
				//$str_cond="and b.pub_shipment_date between '$start_date' and '$end_date'";
			}

			$ref_closing_po_arr=return_library_array( "select b.inv_pur_req_mst_id as po_id,min(b.closing_date) as closing_date
			from inv_reference_closing b where  b.reference_type=163 and b.closing_status=1 and b.status_active=1 and b.is_deleted=0 and b.closing_date  between '$start_date' and '$end_date' group by b.inv_pur_req_mst_id", "po_id", "closing_date");


			foreach($ref_closing_po_arr as $po_id=>$ids){
			$poArr[$po_id]=$po_id;
			}

			$ref_po_cond_for_in2=where_con_using_array($poArr,0,"b.po_break_down_id");
			$ref_po_cond_for_in3=where_con_using_array($poArr,0,"a.po_breakdown_id");
			$ref_po_cond_for_in=where_con_using_array($poArr,0,"b.id");
			$ship_cond="and b.shiping_status=3";
		}
		else
		{
			if($start_date!="" && $end_date!="")
			{
				$str_cond="and b.pub_shipment_date between '$start_date' and '$end_date'";
			}
			else
				$str_cond="";
		}

		$txt_job_no=str_replace("'","",$txt_job_no);
		$job_no_cond="";$job_no_cond2="";
		if(trim($txt_job_no)!="")
		{
			$job_no=trim($txt_job_no);
			$job_no_cond=" and a.job_no_prefix_num=$job_no";
			$job_no_cond2=" and b.job_no like '%$job_no%'";
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
		if(trim($cbo_shipping_status)==0) $shipping_status_cond=""; else $shipping_status_cond=" and b.shiping_status='$cbo_shipping_status'";

		$dataArrayYarn=array();
		$yarn_sql="select job_no, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, sum(cons_qnty) as qty, sum(avg_cons_qnty) as qnty, sum(amount) as amnt, sum(rate*avg_cons_qnty) as amount from wo_pre_cost_fab_yarn_cost_dtls where status_active=1 and is_deleted=0 group by job_no, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id";
		$resultYarn=sql_select($yarn_sql);
		foreach($resultYarn as $yarnRow)
		{
			$dataArrayYarn[$yarnRow[csf('job_no')]].=$yarnRow[csf('count_id')]."**".$yarnRow[csf('copm_one_id')]."**".$yarnRow[csf('percent_one')]."**".$yarnRow[csf('copm_two_id')]."**".$yarnRow[csf('percent_two')]."**".$yarnRow[csf('type_id')]."**".$yarnRow[csf('qnty')]."**".$yarnRow[csf('amount')].",";
		}

		$avg_rate_array=return_library_array( "select id, avg_rate_per_unit from product_details_master where item_category_id=1", "id", "avg_rate_per_unit"  );
		$receive_array=array();

		$ex_rate=76;
		$exchange_rate=set_conversion_rate( 2, date('d-m-Y'),$company_name);



		$booking_print_arr=array();
		$booking_print_sql=sql_select("select report_id, format_id from lib_report_template where template_name='$company_name' and module_id=2 and report_id in (1,2,3) and is_deleted=0 and status_active=1");
		foreach($booking_print_sql as $print_id)
		{
			$booking_print_arr[$print_id[csf('report_id')]]=(int)$print_id[csf('format_id')];
		}

		if($db_type==0) $year_field="YEAR(a.insert_date) as year";
		else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
		else $year_field="";//defined Later

		$sql="select a.company_name, a.buyer_name, $year_field, a.job_no_prefix_num, a.job_no, a.style_ref_no, a.gmts_item_id, a.order_uom, a.total_set_qnty as ratio, b.id as po_id, b.po_number, b.po_quantity as po_qnty, b.pub_shipment_date, b.shiping_status, b.insert_date, b.po_received_date, b.plan_cut, b.grouping from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name='$company_name' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $buyer_id_cond $str_cond $year_cond $job_no_cond $po_id_cond $jobYearCond $shipping_status_cond $ref_po_cond_for_in $ship_cond order by b.pub_shipment_date, a.job_no_prefix_num, b.id";

		$nameArray=sql_select($sql);

		$breakDownArr = array();
		foreach($nameArray as $row)
		{
			$breakDownArr[$row[csf('po_id')]]=$row[csf('po_id')];
		}

		$ref_po_break_cond1=where_con_using_array($breakDownArr,0,"a.po_breakdown_id");
		$ref_po_break_cond2=where_con_using_array($breakDownArr,0,"b.po_break_down_id");

		$yarnIssDataArray= sql_select("SELECT a.id as propotion_id,a.po_breakdown_id, a.prod_id, c.dyed_type, b.id AS trans_id, b.cons_rate, CASE WHEN     a.entry_form = '3' AND a.trans_type = 2 AND a.issue_purpose != 2 THEN a.quantity ELSE 0 END AS yarn_iss_qty,CASE WHEN a.entry_form = '11' AND a.trans_type = 5 THEN a.quantity ELSE 0 END AS trans_in_qty_yarn,CASE WHEN a.entry_form = '11' AND a.trans_type = 6 THEN a.quantity ELSE 0 END AS trans_out_qty_yarn, e.dye_charge FROM  inv_transaction       b LEFT JOIN inv_mrr_wise_issue_details d ON b.id = d.issue_trans_id LEFT JOIN inv_transaction e ON e.id = d.recv_trans_id, product_details_master c, order_wise_pro_details a WHERE a.trans_id = b.id AND b.prod_id = c.id AND a.trans_type IN (2, 5, 6) AND a.status_active = 1 AND a.is_deleted = 0 AND b.company_id = $company_name $ref_po_cond_for_in3 $ref_po_break_cond1 GROUP BY a.id,a.po_breakdown_id, a.prod_id, c.dyed_type, b.id, b.cons_rate, e.dye_charge, a.quantity, a.entry_form, a.trans_type, a.issue_purpose");

		$checkIssuePkId = array();
		foreach($yarnIssDataArray as $invRow)
		{
			if($checkIssuePkId[$invRow[csf('propotion_id')]]=="")
			{
				$checkIssuePkId[$invRow[csf('propotion_id')]] = $invRow[csf('propotion_id')];
				$iss_qty=($invRow[csf('yarn_iss_qty')]+$invRow[csf('trans_in_qty_yarn')])-($invRow[csf('trans_out_qty_yarn')]);

				$dataArrayYarnIssue[$invRow[csf('po_breakdown_id')]][1]+=$iss_qty;

				$rate=0;

				if($invRow[csf('dyed_type')]==1)
				{
					$rate=($invRow[csf('cons_rate')]-$invRow[csf('dye_charge')])/$exchange_rate;
				}else
				{
					$rate=($invRow[csf('cons_rate')])/$exchange_rate;
				}

				$dataArrayYarnIssue[$invRow[csf('po_breakdown_id')]][2]+=($iss_qty*$rate);
			}
		}

		$sql_issue_return = sql_select("SELECT  a.id as propotion_id, a.po_breakdown_id, b.id AS prod_id, b.dyed_type, c.id AS trans_id, c.cons_rate, f.dye_charge, a.quantity AS yarn_iss_return_qty FROM  order_wise_pro_details  a, product_details_master    b, inv_transaction  c, inv_transaction d, inv_mrr_wise_issue_details e, inv_transaction  f WHERE  c.id = a.trans_id AND a.prod_id = b.id AND d.mst_id = c.issue_id AND d.prod_id = a.prod_id AND d.id = e.issue_trans_id AND e.recv_trans_id = f.id AND c.transaction_type = 4 AND c.item_category = 1 AND b.item_category_id = 1 AND a.trans_type = 4 AND a.entry_form = 9 AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND a.issue_purpose != 2 AND c.company_id=$company_name $ref_po_cond_for_in3 $ref_po_break_cond1 GROUP BY  a.id, a.po_breakdown_id, b.id, b.dyed_type, c.id, c.cons_rate, f.dye_charge,a.quantity");

		$checkRtnPkId = array();
		foreach($sql_issue_return as $invRow)
		{
			if($checkRtnPkId[$invRow[csf('propotion_id')]]=="")
			{
				$issue_rtn_qty = $invRow[csf('yarn_iss_return_qty')];

				$dataArrayYarnIssueReturn[$invRow[csf('po_breakdown_id')]][1]+=$issue_rtn_qty;
				$rate=0;

				if($invRow[csf('dyed_type')]==1)
				{
					$rate=($invRow[csf('cons_rate')]-$invRow[csf('dye_charge')])/$exchange_rate;
				}else
				{
					$rate=($invRow[csf('cons_rate')])/$exchange_rate;
				}

				$dataArrayYarnIssueReturn[$invRow[csf('po_breakdown_id')]][2]+=($issue_rtn_qty*$rate);
			}
		}

		//var_dump($dataArrayYarnIssue[55286][2]);

		$dataArrayWo=array();
		$sql_wo="select b.po_break_down_id, a.id, a.booking_no, a.insert_date, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.booking_no_prefix_num, a.job_no, a.is_short, a.is_approved, b.fabric_color_id, sum(b.fin_fab_qnty) as req_qnty, sum(b.grey_fab_qnty) as grey_req_qnty,a.entry_form from wo_booking_mst a, wo_booking_dtls b,wo_po_break_down c where a.booking_no=b.booking_no and b.po_break_down_id=c.id and a.item_category in(2,13) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $color_cond_search $po_id_cond2 $ref_po_cond_for_in2 $ref_po_break_cond2 $job_no_cond2 group by b.po_break_down_id, a.id, a.booking_no, a.insert_date, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.booking_no_prefix_num, a.job_no, a.is_short, a.is_approved, b.fabric_color_id,a.entry_form";

		//echo $sql_wo;

		$resultWo=sql_select($sql_wo);
		foreach($resultWo as $woRow)
		{
			$dataArrayWo[$woRow[csf('po_break_down_id')]].=$woRow[csf('id')]."**".$woRow[csf('booking_no')]."**".$woRow[csf('insert_date')]."**".$woRow[csf('item_category')]."**".$woRow[csf('fabric_source')]."**".$woRow[csf('company_id')]."**".$woRow[csf('booking_type')]."**".$woRow[csf('booking_no_prefix_num')]."**".$woRow[csf('job_no')]."**".$woRow[csf('is_short')]."**".$woRow[csf('is_approved')]."**".$woRow[csf('fabric_color_id')]."**".$woRow[csf('req_qnty')]."**".$woRow[csf('grey_req_qnty')].",";
			$bookingEntryFromArr[$woRow[csf('booking_no')]]=$woRow[csf('entry_form')];

		}
		unset($resultWo);

		//echo "<pre>";
		//print_r($dataArrayWo);
		ob_start();

		?>
        <fieldset>
            <table width="2375">
                <tr class="form_caption">
                    <td colspan="21" align="center">Order Wise Yarn Cost Report</td>
                </tr>
            </table>
            <table style="margin-top:10px" id="table_header_1" class="rpt_table" width="2375" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                	<tr>
                        <th width="40">SL</th>
                        <th width="70">B/U</th>
                        <th width="125">Main Booking</th>
                        <th width="125">Sample Booking</th>
                        <th width="70">Buyer</th>
                        <th width="60">Job Year</th>
                        <th width="70">Job No</th>
                        <th width="100">Internal Ref.</th>
                        <th width="100">PO No</th>
                        <th width="110">Style Name</th>
                        <th width="120">Garments Item</th>
                        <th width="50">UOM</th>
                        <th width="100">PO Quantity [Pcs]</th>
                        <th width="80">Ref.Close Date</th>
                        <th width="80">Shipment Date</th>
                        <th width="80">Shipment Month</th>
                        <th width="100">Shipping Status</th>
                        <th width="110">Budget Required Qty</th>
                        <th width="110">Budget Cost ($)</th>
                        <th width="110">Booking Qty</th>
                        <th width="110">Yarn Issued</th>
                        <th width="110">Actual Cost ($)</th>
                        <th width="110">Balance Qty.</th>
                        <th>Balance Cost ($)</th>
                    </tr>
                </thead>
            </table>
            <div style="width:2395px; max-height:400px; overflow-y:scroll" id="scroll_body">
                <table class="rpt_table" width="2375" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
                    <?
						if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
						{
							if($db_type==0)
							{
								$start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
								$end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
								//$date_cond=" and b.update_date between '".$start_date."' and '".$end_date." 23:59:59'";
							}
							else if($db_type==2)
							{
							 $start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
								$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
								//$date_cond=" and b.update_date between '".$start_date."' and '".$end_date." 11:59:59 PM'";
							}
						}

						$condition= new condition();
						if(str_replace("'","",$company_name)>0)
						{
						 	$condition->company_name("=$company_name");
						}

						$job_no=str_replace("'","",$txt_job_no);
						if(str_replace("'","",$txt_job_no) !='')
						{
						  $condition->job_no_prefix_num(" in($job_no)");
						}
						if(str_replace("'","",$cbo_buyer_name)>0)
						{
							$condition->buyer_name("=$cbo_buyer_name");
						}

						if(str_replace("'","",$cbo_year) !=0)
						{
							 $condition->job_year("$jobYearCond");
						}

						if( str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!='')
						{
							$condition->pub_shipment_date(" between '$start_date' and '$end_date'");
						}

						if(str_replace("'","",$txt_search_string)!='')
						{
						 	$po_number_txt = trim($txt_search_string);
							$condition->po_number("='".$po_number_txt."'");
						}

						$condition->init();

						$yarn= new yarn($condition);
						//echo $yarn->getQuery();die;
						$OrderWiseYarnQtyAndAmountArray=$yarn->getOrderWiseYarnQtyAndAmountArray();
						//$yarn_costing_arr=$yarn->getOrderWiseYarnAmountArray();

						// print_r($yarn_costing_arr);
						$i=1; $tot_order_qnty=0; $tot_mkt_required=0; $tot_yarn_issue_qnty=0; $tot_yarn_issue_cost=0; $tot_booking_qty=0; $tot_booking_cost=0;
                        foreach($nameArray as $row)
                        {
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
							$yarn_issued=0;
							$yarn_data_array=array();

							$job_mkt_required=$OrderWiseYarnQtyAndAmountArray[$row[csf('po_id')]]['qty'];
							$job_mkt_required_cost=$OrderWiseYarnQtyAndAmountArray[$row[csf('po_id')]]['amount'];
                            if($costing_per_id_library[$row[csf('job_no')]]==1) $dzn_qnty=12;
                            else if($costing_per_id_library[$row[csf('job_no')]]==3) $dzn_qnty=12*2;
                            else if($costing_per_id_library[$row[csf('job_no')]]==4) $dzn_qnty=12*3;
                            else if($costing_per_id_library[$row[csf('job_no')]]==5) $dzn_qnty=12*4;
                            else $dzn_qnty=1;

                            $dzn_qnty=$dzn_qnty*$row[csf('ratio')];
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
								$amnt=$yarnRow[7];

								$yarn_data_array['count'][]=$yarn_count_details[$count_id];
								$yarn_data_array['type'][]=$yarn_type[$type_id];

								if($percent_two!=0)
								{
									$compos=$composition[$copm_one_id]." ".$percent_one." %"." ".$composition[$copm_two_id]." ".$percent_two." %";
								}
								else
								{
									$compos=$composition[$copm_one_id]." ".$percent_one." %"." ".$composition[$copm_two_id];
								}

								$yarn_data_array['comp'][]=$compos;
                            }

                            if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                            $buyer_name_array[$row[csf('buyer_name')]]=$buyer_short_name_library[$row[csf('buyer_name')]];

							$gmts_item='';
							$gmts_item_id=explode(",",$row[csf('gmts_item_id')]);
							foreach($gmts_item_id as $item_id)
							{
								if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
							}

							$order_qnty_in_pcs=$row[csf('po_qnty')]*$row[csf('ratio')];
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
										$page=0;
										if($booking_type==4) $page=3;
										else
										{
											if($is_short==1) $page=2;
											else $page=1;
										}
										//echo $booking_print_arr[$page].'<br>'.$page; //die;
										$action_name_m=""; $action_name_s=""; $action_name_sm="";
										$print_permission_ex=explode(',',$booking_print_arr[$page]);
										foreach($print_permission_ex as $print_id)
										{
											if($booking_type==4)
											{
												$action_name_sm="show_fabric_booking_report";
											}
											else
											{
												if($is_short==1)
												{
													//echo $print_id;
													if($print_id==8) $action_name_s="show_fabric_booking_report";
													if($print_id==9) $action_name_s="show_fabric_booking_report3";
													if($print_id==10) $action_name_s="show_fabric_booking_report4";
													$entryForm=$bookingEntryFromArr[$booking_no];
												}
												else
												{
													if($print_id==1) $action_name_m="show_fabric_booking_report_gr";
													if($print_id==2) $action_name_m="show_fabric_booking_report";
													if($print_id==3) $action_name_m="show_fabric_booking_report3";
													if($print_id==4) $action_name_m="show_fabric_booking_report1";
													if($print_id==5) $action_name_m="show_fabric_booking_report2";
													if($print_id==6) $action_name_m="show_fabric_booking_report4";
													if($print_id==7) $action_name_m="show_fabric_booking_report5";
													if($print_id==28) $action_name_m="show_fabric_booking_report_akh";
													if($print_id==719) $action_name_m="show_fabric_booking_report16";
													$entryForm=$bookingEntryFromArr[$booking_no];
												}
											}
										}

										if($booking_type==4)
										{
											$sample_booking.="<a href='##' style='color:#000' onclick=\"generate_worder_report('3','".$booking_no."','".$company_id."','".$row[csf('po_id')]."','".$item_category."','".$fabric_source."','".$job_no."','".$is_approved."','".$action_name_sm."','".$entryForm."')\"><font style='font-weight:bold' color='$wo_color;'>".$booking_no."(".$system_date.")"."</font></a><br>";
											$sample_booking_excel.="<font style='font-weight:bold' color='$wo_color;'>".$booking_no."(".$system_date.")"."</font><br>";
										}
										else
										{
											if($is_short==1)
											{
												$pre="S";
												$action_name=$action_name_s;
											}
											else
											{
												$pre="M";
												$action_name=$action_name_m;
											}


											$main_booking.="<a href='##' style='color:#000' onclick=\"generate_worder_report('".$is_short."','".$booking_no."','".$company_id."','".$row[csf('po_id')]."','".$item_category."','".$fabric_source."','".$job_no."','".$is_approved."','".$action_name."','".$entryForm."')\"><font style='font-weight:bold' color='$wo_color;'>".$booking_no."(".$system_date.")".$pre."</font></a><br>";
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

							$yarn_iss_qty=$dataArrayYarnIssue[$row[csf('po_id')]][1]-$dataArrayYarnIssueReturn[$row[csf('po_id')]][1];
							$yarn_iss_cost=$dataArrayYarnIssue[$row[csf('po_id')]][2]-$dataArrayYarnIssueReturn[$row[csf('po_id')]][2];

							if(number_format($required_qnty,2,'.','') > number_format($job_mkt_required,2,'.','')) {$bgcolor_booking='#FF0000';} else{ $bgcolor_booking='';}
							if(number_format($yarn_iss_cost,2,'.','') > number_format($job_mkt_required_cost,2,'.','')) {$bgcolor_cost='#FF0000'; } else{ $bgcolor_cost='';}

                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                <td width="40"><? echo $i; ?></td>
                                <td width="70"><div style="word-wrap:break-word; width:70px"><? echo $company_library[$row[csf('company_name')]];?></div></td>
                                <td width="125"><div style="word-wrap:break-word; width:125px"><? echo $main_booking; ?></div></td>
                                <td width="125"><div style="word-wrap:break-word; width:125px"><? echo $sample_booking; ?></div></td>
                                <td width="70"><div style="word-wrap:break-word; width:70px"><? echo $buyer_short_name_library[$row[csf('buyer_name')]]; ?></div></td>
                                <td width="60" align="center"><? echo $row[csf('year')]; ?></td>
                                <td width="70" align="center"><? echo $row[csf('job_no_prefix_num')]; ?></td>
                                <td width="100" align="center"><? echo $row[csf('grouping')]; ?></td>
                                <td width="100" style="word-break:break-all;"><? echo $row[csf('po_number')]; ?></td>
                                <td width="110" style="word-break:break-all;"><? echo $row[csf('style_ref_no')]; ?></td>
                                <td width="120" style="word-break:break-all;"><? echo $gmts_item; ?></td>
                                <td width="50" align="center"><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?></td>
                        		<td width="98" align="right" style="padding-right:2px"><? echo $order_qnty_in_pcs; ?></td>
                        		<td width="80" align="center"><? echo change_date_format($ref_closing_po_arr[$row[csf('po_id')]]); ?></td>
                                <td width="80" align="center"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
                                <td width="80" align="center"><? echo $month_year = date("F Y",strtotime($row[csf('pub_shipment_date')]));?></td>
                        		<td width="100" style="word-break:break-all;"><? echo $shipment_status[$row[csf('shiping_status')]]; ?></td>
                                <td width="108" align="right" style="padding-right:2px"><a href="#report_details" onClick="openmypage('<? echo $row[csf('po_id')]; ?>','yarn_req_popup','Yarn Req. Details')"><? echo number_format($job_mkt_required,2,'.',''); ?></a></td>
                                <td width="108" align="right" style="padding-right:2px"><? echo number_format($job_mkt_required_cost,2,'.',''); ?></td>
                                <td width="108" align="right" style="padding-right:2px" bgcolor="<? echo $bgcolor_booking;?>"><? echo number_format($required_qnty,2,'.',''); ?></td>
                                <td width="108" align="right" style="padding-right:2px"><a href="#report_details" onClick="openmypage('<? echo $row[csf('po_id')]; ?>','yarn_issue_cost','Yarn Issue Cost Details','<? echo $company_name; ?>')"><? echo number_format($yarn_iss_qty,2); ?></a></td>
                                <td width="108" align="right" style="padding-right:2px" bgcolor="<? echo $bgcolor_cost; ?>"><? echo number_format($yarn_iss_cost,2,'.',''); ?></td>
                                <td width="108" align="right" style="padding-right:2px"><? $req_bal=$job_mkt_required-$yarn_iss_qty; echo number_format($req_bal,2); ?></a></td>
                                <td align="right" style="padding-right:2px"><? $cost_bal=$job_mkt_required_cost-$yarn_iss_cost; echo number_format($cost_bal,2,'.',''); ?></td>
                            </tr>
                        <?
							$tot_order_qnty+=$order_qnty_in_pcs;
							$tot_mkt_required+=$job_mkt_required;
							$tot_booking_qty+=$booking_qty;
							$tot_required_cost+=$job_mkt_required_cost;
							$tot_yarn_iss_qty+=$yarn_iss_qty;
							$tot_yarn_iss_cost+=$yarn_iss_cost;

							$tot_req_bal_qty+=$req_bal;
							$tot_cost_bal+=$req_bal;
                            $i++;
                        }// end main query
                    ?>
                    </table>
                </div>
                <table width="2375" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                    <tfoot>
                        <tr>
                        	<th width="40">&nbsp;</th>
                        	<th width="70">&nbsp;</th>
                            <th width="125">&nbsp;</th>
                            <th width="125">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="60">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="100">&nbsp;</th>
                            <th width="100">&nbsp;</th>
                            <th width="110">&nbsp;</th>
                            <th width="120">&nbsp;</th>
                            <th width="50">&nbsp;</th>
                            <th width="100" id="total_order_qnty"><? echo number_format($tot_order_qnty,0); ?></th>
                            <th width="80">&nbsp;</th>
                            <th width="80">&nbsp;</th>
                            <th width="80">&nbsp;</th>
                            <th width="100">&nbsp;</th>
                            <th width="110" id="value_tot_mkt_required"><? echo number_format($tot_mkt_required,2); ?></th>
                            <th width="110" id="value_tot_required_cost"><? echo number_format($tot_required_cost,2); ?></th>
                            <th width="110" id="value_tot_booking_qty"><? echo number_format($tot_booking_qty,2); ?></th>
                            <th width="110" id="value_yarn_iss_qty"><? echo number_format($tot_yarn_iss_qty,2); ?></th>
                            <th width="110" id="value_yarn_iss_cost"><? echo number_format($tot_yarn_iss_cost,2); ?></th>
                            <th width="110" id="value_req_bal_qty"><? echo number_format($tot_req_bal_qty,2); ?></th>
                            <th id="value_cost_bal_cost"><? echo number_format($tot_cost_bal,2); ?></th>
                        </tr>
                    </tfoot>
                </table>
        </fieldset>
		<?
	}

	foreach (glob(".../../../../ext_resource/tmp_report/$user_name*.xls") as $filename)
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$html=ob_get_contents();
	$name=time();
	$filename="../../../../ext_resource/tmp_report/".$user_name."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,$html);
	$filename="../../../ext_resource/tmp_report/".$user_name."_".$name.".xls";
	echo "$total_data****$filename****$type";
	exit();
}

if($action=="yarn_issue_cost")
{
	echo load_html_head_contents("Report Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);

	$po_arr=return_library_array( "select id, po_number from wo_po_break_down", "id", "po_number");
	$receive_array=array();
	$ex_rate=76;
	$exchange_rate=set_conversion_rate( 2, date('d-m-Y'),$company_id);

	/*$sql_receive="select a.currency_id,a.receive_purpose,b.prod_id, (b.order_qnty) as qty, (b.order_amount) as amnt,b.cons_quantity,b.cons_amount from inv_receive_master a, inv_transaction b where  a.id=b.mst_id and b.transaction_type=1 and b.item_category=1 and b.status_active=1 and b.is_deleted=0 ";

	$resultReceive = sql_select($sql_receive);
	foreach($resultReceive as $invRow)
	{
		if($invRow[csf('currency_id')]==1)//Taka
		{
			$avg_rate=$invRow[csf('cons_amount')]/$invRow[csf('cons_quantity')];
			$receive_array[$invRow[csf('prod_id')]]=$avg_rate/$ex_rate;
		}
		else if($invRow[csf('currency_id')]==2)//USD
		{
			$avg_rate=$invRow[csf('amnt')]/$invRow[csf('qty')];
			$receive_array[$invRow[csf('prod_id')]]=$avg_rate;
		}
		else
		{

			$receive_array[$invRow[csf('prod_id')]]=$avg_rate;
		}
	}*/
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
	<div style="width:960px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:955px; margin-left:3px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="950" cellpadding="0" cellspacing="0">
            	<thead>
					<th colspan="11"><b>Yarn Issue</b></th>
				</thead>
				<thead>
                    <th width="90">Issue Id</th>
                    <th width="90">Booking No</th>
                    <th width="70">Challan No</th>
                    <th width="75">Issue Date</th>
                    <th width="60">Lot No</th>
                    <th width="120">Yarn Description</th>
                    <th width="80">Issue Qnty (In)</th>
                    <th width="80">Issue Qnty (Out)</th>
                    <th width="80">Total Qnty</th>
                    <th width="60">Avg Rate (USD)</th>
                    <th>Yarn Cost</th>
				</thead>
                <?
                $i=1; $total_yarn_issue_qnty=0; $total_yarn_issue_qnty_out=0; $total_yarn_cost=0;
				/*
				$sql="select a.issue_number, a.issue_date, a.challan_no, a.booking_no, a.knit_dye_source, sum(b.quantity) as issue_qnty, c.lot, c.id as prod_id, c.product_name_details, c.avg_rate_per_unit, d.id as trans_id, d.cons_rate
				from inv_issue_master a, order_wise_pro_details b, product_details_master c, inv_transaction d
				where a.id=d.mst_id and d.id=b.trans_id and b.prod_id=c.id and d.transaction_type=2 and d.item_category=1 and c.item_category_id=1 and b.trans_type=2 and b.entry_form=3 and b.po_breakdown_id=$po_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.issue_purpose!=2
				group by a.id, c.id, a.issue_number, a.issue_date, a.challan_no, a.booking_no, a.knit_dye_source, c.lot, c.product_name_details, c.avg_rate_per_unit, d.id, d.cons_rate";*/

				$sql="SELECT b.id as propotion_id,a.issue_number, a.issue_date, a.challan_no, a.booking_no, a.knit_dye_source, b.quantity AS issue_qnty, c.lot, c.id AS prod_id, c.dyed_type, c.product_name_details, c.avg_rate_per_unit, d.id AS trans_id, d.cons_rate, f.dye_charge FROM inv_issue_master a, order_wise_pro_details b, product_details_master c, inv_transaction d, inv_mrr_wise_issue_details e left join inv_transaction f on f.id=e.recv_trans_id and f.prod_id = e.prod_id WHERE     a.id = d.mst_id AND d.id = b.trans_id AND b.prod_id = c.id AND d.id=e.issue_trans_id AND d.transaction_type = 2 AND d.item_category = 1 AND c.item_category_id = 1 AND b.trans_type = 2 AND b.entry_form = 3 AND b.po_breakdown_id = $po_id AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND c.status_active = 1 AND c.is_deleted = 0 AND b.issue_purpose != 2 GROUP BY a.id, b.id, c.id, a.issue_number, a.issue_date, a.challan_no, a.booking_no, a.knit_dye_source, c.lot, c.dyed_type, c.product_name_details, c.avg_rate_per_unit, d.id, d.cons_rate, f.dye_charge,b.quantity";
				//echo $sql;

                $result=sql_select($sql);
                $checkOrdPkId = array();
				foreach($result as $row)
				{
				    if($checkOrdPkId[$row[csf('propotion_id')]]=="")
				    {
				    	if ($i%2==0)
						$bgcolor="#E9F3FF";
						else
						$bgcolor="#FFFFFF";
				    	$yarn_issued=$row[csf('issue_qnty')];
						$prod_id=$row[csf('prod_id')];
						$checkOrdPkId[$row[csf('propotion_id')]] = $row[csf('propotion_id')];
	                    ?>
	                    <tr bgcolor="<? echo  $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
	                    	<td width="90" title="Prod ID=<? echo $prod_id; ?>"><p><? echo $row[csf('issue_number')]; ?></p></td>
	                        <td width="90"><p><? echo $row[csf('booking_no')];?>&nbsp;</p></td>
	                        <td width="70"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
	                        <td width="75" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
	                        <td width="60"><p><? echo $row[csf('lot')]; ?></p></td>
	                        <td width="120"><p><? echo $row[csf('product_name_details')]; ?></p></td>
	                        <td align="right" width="80">
								<?
									if($row[csf('knit_dye_source')]!=3)
									{
										echo number_format($yarn_issued,2);
										$total_yarn_issue_qnty+=$yarn_issued;
									}
									else echo "&nbsp;";
	                            ?>
	                        </td>
	                        <td align="right" width="80">
								<?
									if($row[csf('knit_dye_source')]==3)
									{
										echo number_format($yarn_issued,2);
										$total_yarn_issue_qnty_out+=$yarn_issued;
									}
									else echo "&nbsp;";
	                            ?>
	                        </td>
	                        <td align="right" width="80">
								<?
									echo number_format($yarn_issued,2);
	                            ?>
	                        </td>
	                        <td align="right" width="60" title="<? echo $row[csf('prod_id')]; ?>">
								<?
									/*if($receive_array[$row[csf('prod_id')]]>0)
									{
										$avg_rate=$receive_array[$row[csf('prod_id')]];
										//echo "A";
									}
									else
									{
										$avg_rate=$row[csf('avg_rate_per_unit')]/$ex_rate;
										//echo "B";
									}*/

									//echo $row[csf('cons_rate')];
									if($row[csf('dyed_type')]==1)
									{
										$avg_rate=($row[csf('cons_rate')]-$row[csf('dye_charge')])/$exchange_rate;
									}
									else
									{
										$avg_rate=$row[csf('cons_rate')]/$exchange_rate;
									}

									echo number_format($avg_rate,4);
	                            ?>
	                        </td>
	                        <td align="right">
								<?
									$yarn_cost=$yarn_issued*$avg_rate;
									echo number_format($yarn_cost,2);
									$total_yarn_cost+=$yarn_cost;
	                            ?>
	                        </td>
	                    </tr>
                		<?
				    }

                	$i++;
                }
                ?>
                <tr style="font-weight:bold">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right">Total</td>
                    <td align="right"><? echo number_format($total_yarn_issue_qnty,2);?></td>
                    <td align="right"><? echo number_format($total_yarn_issue_qnty_out,2);?></td>
                    <td align="right"><? echo number_format($total_yarn_issue_qnty+$total_yarn_issue_qnty_out,2);?></td>
                    <td>&nbsp;</td>
                    <td align="right"><? echo number_format($total_yarn_cost,2);?></td>
                </tr>
                <thead>
                    <th colspan="11"><b>Yarn Return</b></th>
                </thead>
                <thead>
                	<th width="90">Return Id</th>
                    <th width="90">Booking No</th>
                    <th width="70">Challan No</th>
                    <th width="75">Return Date</th>
                    <th width="60">Lot No</th>
                    <th width="120">Yarn Description</th>
                    <th width="80">Return Qnty (In)</th>
                    <th width="80">Return Qnty (Out)</th>
                    <th width="80">Total Qnty</th>
                    <th width="60">Avg Rate (USD)</th>
                    <th>Yarn Cost</th>
               </thead>
                <?
                $total_yarn_return_qnty=0; $total_yarn_return_qnty_out=0; $total_yarn_return_cost=0;

				/*$sql="select a.recv_number, a.receive_date, a.challan_no, a.booking_no, a.knitting_source, sum(b.quantity) as returned_qnty, c.lot, c.id as prod_id, c.product_name_details, c.avg_rate_per_unit, d.id as trans_id, d.cons_rate
				from inv_receive_master a, order_wise_pro_details b, product_details_master c, inv_transaction d
				where a.id=d.mst_id and d.id=b.trans_id and b.prod_id=c.id and d.transaction_type=4 and d.item_category=1 and c.item_category_id=1 and b.trans_type=4 and b.entry_form=9 and b.po_breakdown_id=$po_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.issue_purpose!=2
				group by a.id, c.id, a.recv_number, a.receive_date, a.challan_no, a.booking_no, a.knitting_source, c.lot, c.product_name_details, c.avg_rate_per_unit, d.id, d.cons_rate";*/

 				$sql="SELECT b.id as propotion_id, a.recv_number, a.receive_date, a.challan_no, a.booking_no, a.knitting_source, b.quantity AS returned_qnty, c.lot, c.dyed_type,c.id  AS prod_id, c.product_name_details, c.avg_rate_per_unit, d.id AS trans_id, d.cons_rate, g.dye_charge FROM inv_receive_master a, order_wise_pro_details b, product_details_master c, inv_transaction  d, inv_transaction  e, inv_mrr_wise_issue_details f, inv_transaction g  WHERE a.id = d.mst_id AND d.id = b.trans_id AND b.prod_id = c.id AND e.mst_id=a.issue_id AND e.prod_id=b.prod_id AND e.id=f.issue_trans_id AND f.recv_trans_id=g.id AND d.transaction_type = 4 AND d.item_category = 1 AND c.item_category_id = 1 AND b.trans_type = 4 AND b.entry_form = 9 AND b.po_breakdown_id = $po_id AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND c.status_active = 1 AND c.is_deleted = 0 AND b.issue_purpose != 2 GROUP BY a.id, b.id, c.id, a.recv_number, a.receive_date, a.challan_no, a.booking_no, a.knitting_source, c.lot, c.dyed_type, c.product_name_details, c.avg_rate_per_unit, d.id, d.cons_rate, g.dye_charge,b.quantity";

                $result=sql_select($sql);
                $checkRtnPkId = array();
				foreach($result as $row)
				{
					if($checkRtnPkId[$row[$row[csf('propotion_id')]]]=="")
					{
						$checkRtnPkId[$row[$row[csf('propotion_id')]]]=$row[$row[csf('propotion_id')]];

						if ($i%2==0)
						$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";

	                    $yarn_returned=$row[csf('returned_qnty')];

	                    ?>
	                    <tr bgcolor="<? echo  $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
	                    	<td width="90"><p><? echo $row[csf('recv_number')]; ?></p></td>
	                        <td width="90"><p><? echo $row[csf('booking_no')];?>&nbsp;</p></td>
	                        <td width="70"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
	                        <td width="75" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
	                        <td width="60"><p><? echo $row[csf('lot')]; ?></p></td>
	                        <td width="120"><p><? echo $row[csf('product_name_details')]; ?></p></td>
	                        <td align="right" width="80">
								<?
									if($row[csf('knitting_source')]!=3)
									{
										echo number_format($yarn_returned,2);
										$total_yarn_return_qnty+=$yarn_returned;
									}
									else echo "&nbsp;";
	                            ?>
	                        </td>
	                        <td align="right" width="80">
								<?
									if($row[csf('knitting_source')]==3)
									{
										echo number_format($yarn_returned,2);
										$total_yarn_return_qnty_out+=$yarn_returned;
									}
									else echo "&nbsp;";
	                            ?>
	                        </td>
	                        <td align="right" width="80">
								<?
									echo number_format($yarn_returned,2);
	                            ?>
	                        </td>
	                        <td align="right" width="60">
								<?
									if($row[csf('dyed_type')]==1)
									{
										$avg_rate=($row[csf('cons_rate')]-$row[csf('dye_charge')])/$exchange_rate;
									}
									else{
										$avg_rate=$row[csf('cons_rate')]/$exchange_rate;
									}

									echo number_format($avg_rate,2);
	                            ?>
	                        </td>
	                        <td align="right" width="80">
								<?
									$yarn_return_cost=$yarn_returned*$avg_rate;
									echo number_format($yarn_return_cost,2);
									$total_yarn_return_cost+=$yarn_return_cost;
	                            ?>
	                        </td>
	                    </tr>
	                	<?
	                	$i++;
					}

                }
                ?>
                <tr style="font-weight:bold">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right">Total</td>
                    <td align="right"><? echo number_format($total_yarn_return_qnty,2);?></td>
                    <td align="right"><? echo number_format($total_yarn_return_qnty_out,2);?></td>
                    <td align="right"><? echo number_format($total_yarn_return_qnty+$total_yarn_return_qnty_out,2);?></td>
                    <td>&nbsp;</td>
                    <td align="right"><? echo number_format($total_yarn_return_cost,2);?></td>
                </tr>
                <thead>
                    <th colspan="11"><b>Transfer In</b></th>
                </thead>
                <thead>
                	<th width="180" colspan="2">Transfer Id</th>
                    <th width="90">From Order</th>
                    <th width="70">Challan No</th>
                    <th width="75">Transfer Date</th>
                    <th width="60">Lot No</th>
                    <th width="200" colspan="2">Yarn Description</th>
                    <th width="80">Transfer Qnty</th>
                    <th width="60">Avg Rate (USD)</th>
                    <th>Yarn Cost</th>
               	</thead>
                <?
                $i=1; $total_trans_in_qnty=0; $total_trans_in_cost=0;
				$sql="select a.transfer_system_id, a.transfer_date, a.challan_no, a.from_order_id, b.from_prod_id, sum(c.quantity) as transfer_qnty, d.product_name_details, d.lot, d.avg_rate_per_unit, b.id as dtls_id, b.rate as cons_rate
				from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, product_details_master d
				where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.item_category=1 and a.transfer_criteria=4 and c.trans_type=5 and c.entry_form=11 and c.po_breakdown_id=$po_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
				group by a.id, c.prod_id, a.transfer_system_id, a.transfer_date, a.challan_no, a.from_order_id, b.from_prod_id, d.product_name_details, d.lot, d.avg_rate_per_unit, b.id, b.rate";
                $result=sql_select($sql);
				foreach($result as $row)
				{
					if ($i%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td width="180" colspan="2"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
                        <td width="90"><p><? echo $po_arr[$row[csf('from_order_id')]]; ?></p></td>
                        <td width="70"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('transfer_date')]); ?></td>
                        <td width="60"><p><? echo $row[csf('lot')]; ?></p></td>
                        <td width="200" colspan="2"><p><? echo $row[csf('product_name_details')]; ?></p></td>
                        <td align="right" width="80"><? echo number_format($row[csf('transfer_qnty')],2); ?> </td>
                        <td align="right" width="60">
							<?
								/*if($receive_array[$row[csf('prod_id')]]>0)
								{
									$avg_rate=$receive_array[$row[csf('prod_id')]];
									//echo "A";
								}
								else
								{
									$avg_rate=$row[csf('avg_rate_per_unit')]/$ex_rate;
									//echo "B";
								}*/
								$avg_rate=$row[csf('cons_rate')]/$exchange_rate;
								echo number_format($avg_rate,2);
                            ?>
                        </td>
                        <td align="right" width="80">
							<?
								$yarn_trans_cost=$row[csf('transfer_qnty')]*$avg_rate;
								echo number_format($yarn_trans_cost,2);
                            ?>
                        </td>
                    </tr>
                <?
					$total_trans_in_qnty+=$row[csf('transfer_qnty')];
					$total_trans_in_cost+=$yarn_trans_cost;
                	$i++;
                }
                ?>
                <tr style="font-weight:bold">
                    <td colspan="2">&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td colspan="2" align="right">Total</td>
                    <td align="right"><? echo number_format($total_trans_in_qnty,2);?></td>
                    <td>&nbsp;</td>
                    <td align="right"><? echo number_format($total_trans_in_cost,2);?></td>
                </tr>
                <thead>
                    <th colspan="11"><b>Transfer Out</b></th>
                </thead>
                <thead>
                	<th width="180" colspan="2">Transfer Id</th>
                    <th width="90">To Order</th>
                    <th width="70">Challan No</th>
                    <th width="75">Transfer Date</th>
                    <th width="60">Lot No</th>
                    <th width="200" colspan="2">Yarn Description</th>
                    <th width="80">Transfer Qnty</th>
                    <th width="60">Avg Rate (USD)</th>
                    <th>Yarn Cost</th>
               	</thead>
                <?
                $i=1; $total_trans_out_qnty=0; $total_trans_out_cost=0;
				$sql="select a.transfer_system_id, a.transfer_date, a.challan_no, a.to_order_id, b.from_prod_id, sum(c.quantity) as transfer_qnty, d.product_name_details, d.lot, d.avg_rate_per_unit, b.id as dtls_id, b.rate as cons_rate
				from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, product_details_master d
				where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.item_category=1 and a.transfer_criteria=4 and c.trans_type=6 and c.entry_form=11 and c.po_breakdown_id=$po_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
				group by a.id, c.prod_id, a.transfer_system_id, a.transfer_date, a.challan_no, a.to_order_id, b.from_prod_id, d.product_name_details, d.lot, d.avg_rate_per_unit, b.id, b.rate";
                $result=sql_select($sql);
				foreach($result as $row)
				{
					if ($i%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td width="180" colspan="2"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
                        <td width="90"><p><? echo $po_arr[$row[csf('to_order_id')]]; ?></p></td>
                        <td width="70"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('transfer_date')]); ?></td>
                        <td width="60"><p><? echo $row[csf('lot')]; ?></p></td>
                        <td width="200" colspan="2"><p><? echo $row[csf('product_name_details')]; ?></p></td>
                        <td align="right" width="80"><? echo number_format($row[csf('transfer_qnty')],2); ?> </td>
                        <td align="right" width="60">
							<?
								/*if($receive_array[$row[csf('prod_id')]]>0)
								{
									$avg_rate=$receive_array[$row[csf('prod_id')]];
									//echo "A";
								}
								else
								{
									$avg_rate=$row[csf('avg_rate_per_unit')]/$ex_rate;
									//echo "B";
								}*/
								$avg_rate=$row[csf('cons_rate')]/$exchange_rate;
								echo number_format($avg_rate,2);
                            ?>
                        </td>
                        <td align="right" width="80">
							<?
								$yarn_trans_cost_out=$row[csf('transfer_qnty')]*$avg_rate;
								echo number_format($yarn_trans_cost_out,2);
                            ?>
                        </td>
                    </tr>
                <?
					$total_trans_out_qnty+=$row[csf('transfer_qnty')];
					$total_trans_out_cost+=$yarn_trans_cost_out;
                	$i++;
                }
                ?>
                <tr style="font-weight:bold">
                    <td colspan="2">&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td colspan="2" align="right">Total</td>
                    <td align="right"><? echo number_format($total_trans_out_qnty,2);?></td>
                    <td>&nbsp;</td>
                    <td align="right"><? echo number_format($total_trans_out_cost,2);?></td>
                </tr>
               	<tfoot>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                    <th align="right">Balance</th>
                    <th align="right">&nbsp;<? //echo number_format($total_yarn_issue_qnty-$total_yarn_return_qnty,2);?></th>
                    <th align="right">&nbsp;<? //echo number_format($total_yarn_issue_qnty_out-$total_yarn_return_qnty_out,2);?></th>
                    <th align="right"><? echo number_format(($total_yarn_issue_qnty+$total_yarn_issue_qnty_out+$total_trans_in_qnty)-($total_yarn_return_qnty+$total_yarn_return_qnty_out+$total_trans_out_qnty),2); ?></th>
                    <th>&nbsp;</th>
                    <th align="right"><? echo number_format(($total_yarn_cost+$total_trans_in_cost)-($total_yarn_return_cost+$total_trans_out_cost),2); ?></th>
                </tfoot>
            </table>
		</div>
	</fieldset>
	<?
	exit();
}

if($action="yarn_req_popup")
{
	echo load_html_head_contents("Report Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$yarn_count_details=return_library_array( "select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$buyer_short_name_library=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	$color_library=return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$size_library=return_library_array( "select id, size_name from lib_size", "id", "size_name");
	$costing_per_id_library=array();
	$costing_sql=sql_select("select job_no, costing_per, costing_date from wo_pre_cost_mst");
	foreach($costing_sql as $row)
	{
		$costing_per_id_library[$row[csf('job_no')]]=$row[csf('costing_per')];
	}
	$po_details_arr=array();
	$po_sql=sql_select("select b.id, sum(b.plan_cut) as plancut, b.po_number, sum(b.po_quantity) as po_quantity, a.job_no, a.total_set_qnty as ratio, a.buyer_name, a.style_ref_no, a.gmts_item_id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.id in($po_id) group by b.id, b.po_number, a.job_no, a.total_set_qnty, a.buyer_name, a.style_ref_no, a.gmts_item_id");
	$buyer_id=""; $job_no=""; $po_no=""; $style_no=""; $gmts_item=""; $po_qty=0;  $plan_cut_qty=0;
	foreach($po_sql as $row)
	{
		$plancut_qty=$row[csf('plancut')]*$row[csf('ratio')];
		$po_details_arr[$row[csf('job_no')]]+=$plancut_qty;
		$ex_item=explode(',',$row[csf('gmts_item_id')]);
		foreach($ex_item as $item_id)
		{
			if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=','.$garments_item[$item_id];
		}
		$buyer_id=$row[csf('buyer_name')]; $job_no=$row[csf('job_no')]; $po_no=$row[csf('po_number')]; $style_no=$row[csf('style_ref_no')]; $po_qty=$row[csf('po_quantity')]; $plan_cut_qty=$row[csf('plancut')];
	}
	//$job_no=$po_sql[0][csf('job_no')];
						 $condition= new condition();
						 $job_no=str_replace("'","",$job_no);
						 if(str_replace("'","",$job_no) !=''){
							  $condition->job_no(" in('$job_no')");
						 }
						 if(str_replace("'","",$po_id)!='')
						 {
							$condition->po_id(" in($po_id)");
						 }

						  $condition->init();
						$yarn= new yarn($condition);
						//echo $yarn->getQuery();die;
						//$OrderWiseYarnQtyAndAmountArray=$yarn->getOrderWiseYarnQtyAndAmountArray();
						$yarn_req_arr=$yarn->getOrderCountCompositionColorAndTypeWiseYarnQtyArray();
						$yarn_req_amt_arr=$yarn->getOrderCountCompositionColorAndTypeWiseYarnAmountArray();
						//print_r($yarn_req_arr);
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
	<div style="width:660px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:655px; margin-left:3px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="650" cellpadding="0" cellspacing="0">
				<thead>
                	<tr>
                    	<th colspan="2">Buyer : <? echo $buyer_short_name_library[$buyer_id]; ?></th>
                        <th colspan="2">Job :<? echo $job_no; ?></th>
                        <th colspan="2">PO No. : <? echo $po_no; ?></th>
                        <th>Po Qty.: <? echo $po_qty; ?></th>
                    </tr>
                	<tr>
						<th colspan="3">Gmts Item :<? echo $gmts_item; ?></th>
                        <th colspan="2">Style : <? echo $style_no; ?></th>
                        <th colspan="2">Plan Cut Qty.: <? echo $plan_cut_qty; ?></th>
                    </tr>
                	<tr>
                        <th width="40">SL</th>
                        <th width="150">Yarn Description</th>
                        <th width="70">Cons./ Dzn</th>
                        <th width="70">Avg. Cons. / Dzn</th>
                        <th width="100">Req. Qty.</th>
                        <th width="100">Rate (USD)</th>
                        <th>Amount (USD)</th>
                    </tr>
				</thead>
                <?
				//$yarn= new yarn($job_no,'job');
				 $sql="select job_no, count_id, copm_one_id,color, percent_one, copm_two_id, percent_two, type_id, sum(cons_qnty) as qty, sum(avg_cons_qnty) as qnty, sum(amount) as amnt, sum(rate) as rate, sum(rate*avg_cons_qnty) as amount from wo_pre_cost_fab_yarn_cost_dtls where status_active=1 and is_deleted=0 and job_no='$job_no' group by job_no, count_id, copm_one_id,color, percent_one, copm_two_id, percent_two, type_id";
				$result=sql_select($sql); $i=1;
				foreach($result as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$compos="";
					if($row[csf('percent_two')]!=0)
					{
						$compos=$composition[$row[csf('copm_one_id')]]." ".$row[csf('percent_one')]." %"." ".$composition[$row[csf('copm_two_id')]]." ".$row[csf('percent_two')]." %";
					}
					else
					{
						$compos=$composition[$row[csf('copm_one_id')]]." ".$row[csf('percent_one')]." %"." ".$composition[$row[csf('copm_two_id')]];
					}
					$description="";
					$description=$yarn_count_details[$row[csf('count_id')]].' '.$compos.' '.$color_library[$row[csf('color')]].' '.$yarn_type[$row[csf('type_id')]];

					if($costing_per_id_library[$row[csf('job_no')]]==1) $dzn_qnty=12;
					else if($costing_per_id_library[$row[csf('job_no')]]==3) $dzn_qnty=12*2;
					else if($costing_per_id_library[$row[csf('job_no')]]==4) $dzn_qnty=12*3;
					else if($costing_per_id_library[$row[csf('job_no')]]==5) $dzn_qnty=12*4;
					else $dzn_qnty=1;

					$req_qty=0;

                    $req_qty=$yarn_req_arr[$po_id][$row[csf('count_id')]][$row[csf('copm_one_id')]][$row[csf('color')]][$row[csf('type_id')]];
					$amount=$yarn_req_amt_arr[$po_id][$row[csf('count_id')]][$row[csf('copm_one_id')]][$row[csf('color')]][$row[csf('type_id')]];
					?>

                    <tr bgcolor="<? echo  $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td width="40" align="center"><? echo $i; ?></td>
                        <td width="150"><p><? echo $description; ?></p></td>
                        <td width="70" align="right"><? echo number_format($row[csf('qty')],4); ?></td>
                        <td width="70" align="right"><? echo number_format($row[csf('qnty')],4); ?></td>
                        <td width="100" align="right"><? echo number_format($req_qty,2); ?></td>
                        <td width="100" align="right"><? echo number_format($row[csf('rate')],4); ?></td>
                        <td align="right"><? echo number_format($amount,2); ?></td>
                    </tr>
                 <?
				 $tot_consDzn+=$row[csf('qty')];
				 $tot_avgConsDzn+=$row[csf('qnty')];
				 $tot_reqQty+=$req_qty;
				 $tot_rate+=$row[csf('rate')];
				 $tot_amount+=$amount;
				 $i++;
				}
				?>
                <tr bgcolor="#CCCCCC">
                	<td>&nbsp;</td>
                    <td align="right"><strong>Total</strong></td>
                    <td align="right"><? echo number_format($tot_consDzn,4); ?></td>
                    <td align="right"><? echo number_format($tot_avgConsDzn,4); ?></td>
                    <td align="right"><? echo number_format($tot_reqQty,2); ?></td>
                    <td align="right"><? echo number_format($tot_rate,4); ?></td>
                    <td align="right"><? echo number_format($tot_amount,2); ?></td>
                </tr>
            </table>
        </div>
    </fieldset>
    <?

	$popUpDataArray=$yarn->getOrderCountCompositionColorTypeAndConsumptionWiseYarnDataArray();
		?>
		<div style="width:860px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:855px; margin-left:3px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="850" cellpadding="0" cellspacing="0">
				<thead>
                	<tr>
                    	<th colspan="4">Buyer : <? echo $buyer_short_name_library[$buyer_id]; ?></th>
                        <th colspan="2">Job :<? echo $job_no; ?></th>
                        <th colspan="2">PO No. : <? echo $po_no; ?></th>
                        <th>Po Qty.: <? echo $po_qty; ?></th>
                    </tr>
                	<tr>
						<th colspan="5">Gmts Item :<? echo $gmts_item; ?></th>
                        <th colspan="2">Style : <? echo $style_no; ?></th>
                        <th colspan="2">Plan Cut Qty.: <? echo $plan_cut_qty; ?></th>
                    </tr>
                	<tr>
                        <th width="40">SL</th>
                        <th width="150">Yarn Description</th>
                        <th width="150">Size</th>
                        <th width="70">PO Qty</th>
                        <th width="70">Fab.Cons. / Dzn</th>
                        <th width="70">Yarn Cons. / Dzn</th>
                        <th width="100">Req. Qty.</th>
                        <th width="100">Rate (USD)</th>
                        <th>Amount (USD)</th>
                    </tr>
				</thead>
                <?
				 $tot_reqQty=0;
				 //$tot_rate+=$row[csf('rate')];
				 $tot_amount=0;
				//$yarn= new yarn($job_no,'job');
				//$sql="select job_no, count_id, copm_one_id,color, percent_one, copm_two_id, percent_two, type_id, sum(cons_qnty) as qty, sum(avg_cons_qnty) as qnty, sum(amount) as amnt, sum(rate) as rate, sum(rate*avg_cons_qnty) as amount from wo_pre_cost_fab_yarn_cost_dtls where status_active=1 and is_deleted=0 and job_no='$job_no' group by job_no, count_id, copm_one_id,color, percent_one, copm_two_id, percent_two, type_id";
				//$result=sql_select($sql);
				$i=1;
				foreach($popUpDataArray[$po_id] as $count=>$countwisevalue)
				{
				foreach($countwisevalue as $compositionid=>$compositionwisevalue)
				{
				foreach($compositionwisevalue as $percentOne=>$percentOnewisevalue)
				{
				foreach($percentOnewisevalue as $color=>$colorwisevalue)
				{
				foreach($colorwisevalue as $type=>$typewisevalue)
				{
				foreach($typewisevalue as $consumption=>$consumptionwisevalue)
				{

					//print_r($consumptionwisevalue);
					//die;
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$compos="";
					$compos=$composition[$compositionid]." ".$percentOne." %";
					$description="";
					$description=$yarn_count_details[$count].' '.$compos.' '.$color_library[$color].' '.$yarn_type[$type];
					//echo $description;


                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td width="40" align="center"><? echo $i; ?></td>
                        <td width="150"><p><? echo $description; ?></p></td>
                       <td width="150">
                       <p>
					   <?
					   ksort($consumptionwisevalue['gmtsSize']);
					   $sizeString='';
					    foreach ($consumptionwisevalue['gmtsSize'] as $sizeId){
							$sizeString.= $size_library[$sizeId].",";
						}
						echo  rtrim($sizeString,",");
					   ?>
                       </p></td>
                        <td width="70" align="right"><? echo number_format($consumptionwisevalue['planPutQnty'],4); ?></td>
                        <td width="70" align="right"><? echo number_format($consumptionwisevalue['fabCons'],4); ?></td>
                        <td width="70" align="right">
						<?
						echo number_format($consumption,4);
						echo "</br>";
						echo "(".number_format($consumptionwisevalue['yratio'],2)."%)";
						?>

                        </td>
                        <td width="100" align="right"><? echo number_format($consumptionwisevalue['qty'],2); ?></td>
                        <td width="100" align="right"><? echo number_format($consumptionwisevalue['rate'],4); ?></td>
                        <td align="right"><? echo number_format($consumptionwisevalue['amount'],2); ?></td>
                    </tr>
                 <?
				 //$tot_consDzn+=$consumptionwisevalue['planPutQnty'];
				 //$tot_avgConsDzn+=$consumptionwisevalue['qty'];
				 $tot_reqQty+=$consumptionwisevalue['qty'];
				 //$tot_rate+=$row[csf('rate')];
				 $tot_amount+=$consumptionwisevalue['amount'];
				 $i++;
				}
				}
				}
				}
				}
				}
				?>
                <tr bgcolor="#CCCCCC">
                	<td>&nbsp;</td>
                    <td align="right"><strong>Total</strong></td>
                    <td align="right"><strong>Total</strong></td>
                    <td align="right"><? //echo number_format($tot_consDzn,4); ?></td>
                    <td align="right"><? //echo number_format($tot_consDzn,4); ?></td>
                    <td align="right"><? //echo number_format($tot_avgConsDzn,4); ?></td>
                    <td align="right"><? echo number_format($tot_reqQty,2); ?></td>
                    <td align="right"><? //echo number_format($tot_rate,4); ?></td>
                    <td align="right"><? echo number_format($tot_amount,2); ?></td>
                </tr>
            </table>
        </div>
    </fieldset>
    <?


	exit();
}
?>