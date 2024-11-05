<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$user_name=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$color_array=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );
$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
$supplier_details=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
$buyer_short_name_library=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
$yarn_count_details=return_library_array( "select id, yarn_count from lib_yarn_count", "id", "yarn_count");
$fabric_desc_details=return_library_array( "select job_no, group_concat(distinct(fabric_description)) as fabric_description from wo_pre_cost_fabric_cost_dtls where fab_nature_id 	=3 and fabric_source=2 group by job_no", "job_no", "fabric_description");

$product_details=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details");
$batch_details=return_library_array( "select id, batch_no from pro_batch_create_mst", "id", "batch_no");

$costing_per_id_library=array(); $costing_date_library=array();
$costing_sql=sql_select("select job_no, costing_per, costing_date from wo_pre_cost_mst");
foreach($costing_sql as $row)
{
	$costing_per_id_library[$row[csf('job_no')]]=$row[csf('costing_per')]; 
	$costing_date_library[$row[csf('job_no')]]=$row[csf('costing_date')];
}

$receive_basis=array(0=>"Independent",1=>"Fabric Booking No");
//--------------------------------------------------------------------------------------------------------------------

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 140, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );     	 
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
		$type = str_replace("'","",$cbo_type);
		$company_name= str_replace("'","",$cbo_company_name);
		
		if(str_replace("'","",$cbo_buyer_name)==0) $buyer_name="%%"; else $buyer_name=str_replace("'","",$cbo_buyer_name);
		
		$txt_search_string=str_replace("'","",$txt_search_string);
		
		if(trim($txt_search_string)!="") $search_string="%".trim($txt_search_string)."%"; else $search_string="%%";
		
		
		
		
		if($db_type==0)
		{
			$start_date = change_date_format( str_replace("'","",trim($txt_date_from)),"yyyy-mm-dd","");
			$end_date = change_date_format( str_replace("'","",trim($txt_date_to)),"yyyy-mm-dd","");
			
			$start_date_po=change_date_format(str_replace("'","",trim($txt_date_from_po)),"yyyy-mm-dd","");
			$end_date_po=change_date_format(str_replace("'","",trim($txt_date_to_po)),"yyyy-mm-dd","");
			
			//$dates_com=" and  f.process_end_date BETWEEN '$date_from' AND '$date_to' ";
		}
		else if($db_type==2)
		{
			$start_date = change_date_format( str_replace("'","",trim($txt_date_from)),'','',1);
			$end_date = change_date_format( str_replace("'","",trim($txt_date_to)),'','',1);
			$start_date_po=change_date_format(str_replace("'","",trim($txt_date_from_po)),'','',1);
			$end_date_po=change_date_format(str_replace("'","",trim($txt_date_to_po)),'','',1);
			//$dates_com="and  f.process_end_date BETWEEN '$date_from' AND '$date_to'";
		}
		
		$txt_style_number=str_replace("'","",$txt_style_number);
		if(trim($txt_style_number)!="") $style_number="%".trim($txt_style_number)."%"; else $style_number="%%";
		
		$cbo_shipping_status=str_replace("'","",$cbo_shipping_status);
		if(trim($cbo_shipping_status)==0) $shipping_status="%%"; else $shipping_status=$cbo_shipping_status;
		
		$txt_fab_color=str_replace("'","",$txt_fab_color);
		//echo $txt_fab_color;die;
		if(trim($txt_fab_color)!="") $fab_color="%".trim($txt_fab_color)."%"; else $fab_color="%%";
		
		if($start_date!="" && $end_date!="")
			$str_cond="and b.pub_shipment_date between '$start_date' and '$end_date'";
		else
			$str_cond="";
		//echo $str_cond;die;
		if($end_date_po=="") 
			$end_date_po=$start_date_po." 23:59:59"; 
		else 
			$end_date_po=$end_date_po." 23:59:59";
		
		if($start_date_po!="" && $end_date_po!="") 
			$str_cond_insert=" and b.insert_date between '".$start_date_po." 00:00:00' and '".$end_date_po."'";
		else 
			$str_cond_insert="";
		
		if($txt_fab_color=="")
		{
			$color_cond="";	
			$color_cond_prop="";	
		}
		else
		{
			$color_id=return_field_value("group_concat(id) as color_id","lib_color","color_name like '$fab_color'","color_id");
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
		
			$budget_qnty_arr=array(); $booking_data_arr=array();$dataArrayWo=array(); $finish_color_array=array();$finish_color_arr=array(); $finish_prod_qty_arr=array(); 
			$budget_qnty_sql="select avg_finish_cons,job_no from  wo_pre_cost_fabric_cost_dtls where   fab_nature_id=3 and status_active=1 and is_deleted=0";
			$dataArrayBudget=sql_select($budget_qnty_sql);
			foreach($dataArrayBudget as $row_budget)
			{
				$budget_qnty_arr[$row_budget[csf("job_no")]]['avg_cons']=$row_budget[csf("avg_finish_cons")];
				 
			} 
			 $sql_wo="select a.id,b.po_break_down_id, a.booking_no, a.insert_date,a.is_short, a.item_category, a.fabric_source, a.company_id,a.is_approved, a.booking_type, b.fabric_color_id,b.job_no, sum(b.fin_fab_qnty) as req_qnty, sum(b.grey_fab_qnty) as grey_req_qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(3)  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1    $color_cond_search group by a.id,b.po_break_down_id, a.booking_no, a.insert_date, a.item_category, a.fabric_source, a.company_id, a.booking_type,a.is_approved, b.fabric_color_id,b.job_no,a.is_short";
			$dataArray=sql_select($sql_wo);
			foreach($dataArray as $row)
			{
				//$booking_data_arr_style[$row[csf("job_no")]]['booking_no']=$row[csf("booking_no")];
				/*$booking_data_arr[$row[csf("po_break_down_id")]]['id']=$row[csf("id")];
				$booking_data_arr[$row[csf("po_break_down_id")]]['booking_no']=$row[csf("booking_no")];
				$booking_data_arr[$row[csf("po_break_down_id")]]['insert_date']=$row[csf("insert_date")];
				$booking_data_arr[$row[csf("po_break_down_id")]]['item_category']=$row[csf("item_category")];
				$booking_data_arr[$row[csf("po_break_down_id")]]['fabric_source']=$row[csf("fabric_source")];
				$booking_data_arr[$row[csf("po_break_down_id")]]['company_id']=$row[csf("company_id")];
				$booking_data_arr[$row[csf("po_break_down_id")]]['booking_type']=$row[csf("booking_type")];
				$booking_data_arr[$row[csf("po_break_down_id")]]['fabric_color_id']=$row[csf("fabric_color_id")];
				$booking_data_arr[$row[csf("po_break_down_id")]]['req_qnty']=$row[csf("req_qnty")];
				$booking_data_arr[$row[csf("po_break_down_id")]]['grey_req_qnty']=$row[csf("grey_req_qnty")];*/
				
				$dataArrayWo[$row[csf('po_break_down_id')]].=$row[csf('id')]."**".$row[csf('booking_no')]."**".$row[csf('insert_date')]."**".$row[csf('item_category')]."**".$row[csf('fabric_source')]."**".$row[csf('company_id')]."**".$row[csf('booking_type')]."**".$row[csf('job_no')]."**".$row[csf('fabric_color_id')]."**".$row[csf('req_qnty')]."**".$row[csf('grey_req_qnty')]."**".$row[csf('is_approved')]."**".$row[csf('is_short')].",";
				 
			}//print_r($dataArrayWo);
			if($type==1)
				{	
				if($db_type==2) $group_concat="LISTAGG(cast(color_id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY color_id) as color_id,po_breakdown_id";
				else if($db_type==0)  $group_concat="group_concat(color_id) as color_id";
				 $finish_color="SELECT $group_concat,po_breakdown_id FROM order_wise_pro_details WHERE entry_form in(17) and color_id<>0   $color_cond_prop group by po_breakdown_id";
				$result_data=sql_select($finish_color); 
					foreach($result_data as $row)
					{
						 $finish_color_array[$row[csf("po_breakdown_id")]]['color']=$row[csf("color_id")];
					}
				}
				else
				{
					$finish_color="SELECT color_id as color_id,po_breakdown_id FROM order_wise_pro_details WHERE entry_form in(17) and color_id<>0   $color_cond_prop group by po_breakdown_id,color_id";
					$result_data=sql_select($finish_color); 
					foreach($result_data as $row)
					{
						$finish_color_arr[$row[csf("po_breakdown_id")]]['color_id']=$row[csf("color_id")];
					}
				}
			
			
			$sql_prop=sql_select("select po_breakdown_id,color_id,sum(CASE WHEN entry_form ='17' THEN quantity ELSE 0 END) AS finish_receive,
										sum(CASE WHEN entry_form ='19' THEN quantity ELSE 0 END) AS finish_issue 
										from order_wise_pro_details where   status_active=1 and is_deleted=0  group by po_breakdown_id,color_id");
										
			foreach($sql_prop as $row)
			{
				$finish_prod_qty_arr[$row[csf("po_breakdown_id")]][$row[csf("color_id")]]['finish_receive']=$row[csf("finish_receive")];
				$finish_prod_qty_arr[$row[csf("po_breakdown_id")]][$row[csf("color_id")]]['finish_issue']=$row[csf("finish_issue")];
			}
						
			//var_dump($finish_prod_qty_arr);

		$tot_order_qnty=0; $total_budget_qnty=0; $tot_booking_qnty=0;  $tot_color_wise_req=0;  $tot_fabric_recv=0; $tot_fabric_balance=0; $tot_issue_to_cut_qnty=0; 
	    $buyer_name_array= array(); $budget_qnty_data_array=array(); $booking_qnty_array= array();$fin_fab_Requi_array= array(); $fin_fab_recei_array= array(); $issue_to_cut_array= array(); $fin_balance_array= array(); 
		if($type==1)
		{
			$table_width="2130"; $colspan="11";
			$sql="select a.company_name, a.buyer_name, a.job_no_prefix_num, a.job_no, a.style_ref_no, a.gmts_item_id, a.order_uom, a.total_set_qnty as ratio, b.id as po_id, b.po_number, b.po_quantity as po_qnty, b.pub_shipment_date, b.shiping_status, b.insert_date, b.po_received_date, b.plan_cut from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name='$company_name' and a.buyer_name like '$buyer_name' and a.style_ref_no like '$style_number' and b.po_number like '$search_string' and b.shiping_status like '$shipping_status' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $str_cond $str_cond_insert order by b.pub_shipment_date, b.id";	
		}
		else
		{
			$table_width="1880"; $colspan="8";
			// 
			if($db_type==0) $group_con="group_concat(b.id) as po_id, group_concat(b.po_number) as po_number";else  $group_con="LISTAGG(cast(b.id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY b.id) as po_id, LISTAGG(cast(b.po_number as varchar2(4000)), ',') WITHIN GROUP (ORDER BY b.id) as po_number";
			 $sql="select a.company_name, a.buyer_name, a.job_no_prefix_num, a.job_no, a.style_ref_no, a.gmts_item_id, a.order_uom, a.total_set_qnty as ratio, $group_con, sum(b.po_quantity) as po_qnty, sum(b.plan_cut) as plan_cut from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name='$company_name' and a.buyer_name like '$buyer_name' and a.style_ref_no like '$search_string'  and b.shiping_status like '$shipping_status' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $str_cond $str_cond_insert group by a.job_no,a.company_name, a.buyer_name, a.job_no_prefix_num,  a.style_ref_no, a.gmts_item_id, a.order_uom, a.total_set_qnty";
		}
		//echo $sql;die;
		$print_report_format=return_field_value("format_id","lib_report_template","template_name =".$company_name." and module_id=2 and report_id=43 and is_deleted=0 and status_active=1");
				
				$print_button=explode(",",$print_report_format);
				//print_r($print_button);die;
				$print_button_first=array_shift($print_button);
				//echo $print_button_first.'D';
				if($print_button_first==50) $precost_button="preCostRpt";
				else if($print_button_first==51) $precost_button="preCostRpt2";
				else if($print_button_first==52) $precost_button="bomRpt";
				else if($print_button_first==63) $precost_button="bomRpt2";
				else if($print_button_first==156) $precost_button="accessories_details";
				else if($print_button_first==157) $precost_button="accessories_details2";
				else if($print_button_first==158) $precost_button="preCostRptWoven";
				else if($print_button_first==159) $precost_button="bomRptWoven";
				else if($print_button_first==170) $precost_button="preCostRpt3";
				else if($print_button_first==171) $precost_button="preCostRpt4";
				else if($print_button_first==142) $precost_button="preCostRptBpkW";
				else if($print_button_first==192) $precost_button="checkListRpt";
				else if($print_button_first==197) $precost_button="bomRpt3";
				else if($print_button_first==211) $precost_button="mo_sheet";
				else if($print_button_first==221) $precost_button="fabric_cost_detail";
				else if($print_button_first==173) $precost_button="preCostRpt5";
				else if($print_button_first==238) $precost_button="summary";
				else if($print_button_first==215) $precost_button="budget3_details";
				else if($print_button_first==270) $precost_button="preCostRpt6";
				else  $precost_button="";
		ob_start();
		?>
        <fieldset style="width:<? echo $table_width+30; ?>px;">	
            <table cellpadding="0" cellspacing="0" width="<? echo $table_width; ?>">
                <tr>
                   <td align="center" width="100%" colspan="<? echo $colspan+18; ?>" style="font-size:16px"><strong><?php echo $company_library[$company_name]; ?></strong></td>
                </tr>
                <tr>
                   <td align="center" width="100%" colspan="<? echo $colspan+18; ?>" style="font-size:16px"><strong><? echo "From ". change_date_format($start_date). " To ". change_date_format($end_date);?></strong></td>
                </tr>
            </table>
            <table class="rpt_table" border="1" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="table_header_1">
                <thead>
                    <tr>
                        <th colspan="<? echo $colspan; ?>">Order Details</th>
                         <th colspan="5">Pre Cost & Booking Details</th>
                        <th colspan="6">Receive Status</th>
                       
                    </tr>
                    <tr>
                        <th width="40">SL</th>
                        <th width="80">Buyer Name</th>
                        <th width="130">Style Ref.
                            <input type="text" name="txt_style_number" onkeyup="show_inner_filter(event);" value="<? echo str_replace("'","",$txt_style_number); ?>" id="txt_style_number" class="text_boxes" style="width:100px" />
                        </th>
                        <th width="120">Order Number</th>
                        <th width="100">Job Number</th>
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
                        <?
						}
						?>
                        <th width="150">Fabric Description</th>
                        <th width="100">Budget Qnty</th>
                        <th width="125">Main Fabric Booking No</th>
                        <th width="125">Sample Fabric Booking No</th>
                        <th width="70">Booking Qnty</th>
                        
                        
                        <th width="100">Fabric Color
                        	<input type="text" name="txt_fab_color" onkeyup="show_inner_filter(event);" value="<? echo str_replace("'","",$txt_fab_color); ?>" id="txt_fab_color" class="text_boxes" style="width:85px" />
                        </th>
                        <th width="100">Required</th>
                        <th width="100">Fabric Receive</th>
                        <th width="100">Balance</th>
                        <th width="">Issue to Cutting </th>
                        
                    </tr>
                </thead>
            </table>
            <?
				$html="<table>
							 <tr>
								<th colspan='$colspan+18' align='center'>".$company_library[$company_name]."</th>
							 </tr>
							 <tr>
								<th colspan='$colspan+18' align='center'>From ". change_date_format($start_date). " To ". change_date_format($end_date)."</th>
							 </tr>
						</table>
						<table border='1' rules='all'>
							<thead>
								<tr>
									<th colspan='$colspan'>Order Details</th>
									<th colspan='5'>Pre Cost & Booking Details</th>
									<th colspan='5'>Receive Status</th>
									
								</tr>
								<tr>
									<th>SL</th>
									<th>Buyer Name</th>
									<th>Style Ref.</th>
									<th>Order Number</th>
									<th>Job Number</th>
									
									<th>Item Name</th>
									<th>Order Qnty</th>
									<th>Shipment Date</th>";
					
					if($type==1)
					{				
						$html.="<th>PO Received Date</th>
								<th>Po Entry Date</th>
								<th>Shipping Status</th>";
					}
					
					$html.="<th>Fabric Description</th>
					        <th>Budget Qnty<br/><font style='font-size:9px; font-weight:100'>(Grey Req-Yarn Issue)</font></th>
					        <th>Main Fabric Booking No</th>
							<th>Sample Fabric Booking No</th>
					        <th>Booking Qnty</th>
							
							<th>Fabric Color</th>
							<th>Required</th>
							
							<th>Fabric Receive</th>
							<th>Balance</th>
							<th>Issue to Cutting</th>
							
						</tr>
					</thead>
					";	
							
					/*$html_short="<table width='1400'>
										 <tr>
											<th colspan='14' align='center'>".$company_library[$company_name]."</th>
										 </tr>
										 <tr>
											<th colspan='14' align='center'>From ". change_date_format($start_date). " To ". change_date_format($end_date)."</th>
										 </tr>
									</table>
									<table class='rpt_table' border='1' rules='all' width='100%'>
										<thead>
											<th>SL</th>
											<th>Buyer Name</th>
											<th>Order Number</th>
											
											<th>Order Qnty.</th>
											<th>Shipment Date</th>
											<th>Main Fabric<br/> Booking No</th>
											<th>Sample Fabric<br/> Booking No</th>
											<th>Yarn Issue</th>
											<th>Grey Req<br/> (As per Booking)</th>
											<th>Grey Knitted</th>
											<th>Fabric Color</th>
											<th>Dyeing Qnty</th>
											<th>Finish Fabric Qnty</th>
											<th>Issue to Cutting</th>
										</thead>
										";*/
			
			?>
            <div style="width:<? echo $table_width+20; ?>px; overflow-y:scroll; max-height:400px" id="scroll_body">
                <table width="<? echo $table_width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body">
                <?
				$nameArray=sql_select($sql); 
				$k=1; $i=1;
				if($type==1)// $type==1 means order wise
				{
					foreach($nameArray as $row)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						
						$buyer_name_array[$row[csf('buyer_name')]]=$buyer_short_name_library[$row[csf('buyer_name')]];
					    $po_id=$row[csf('po_id')];
						
						// $po_id_data=explode(",",$row[csf('po_id')]);
						
						$order_qnty_in_pcs=$row[csf('po_qnty')]*$row[csf('ratio')];
						$plan_cut_qnty=$row[csf('plan_cut')];
						
						$gmts_item='';
						$gmts_item_id=explode(",",$row[csf('gmts_item_id')]);
						foreach($gmts_item_id as $item_id)
						{
							if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
						}
						
						$dzn_qnty=0; $balance=0; $job_mkt_required=0; $yarn_issued=0;
						if($costing_per_id_library[$row[csf('job_no')]]==1) $dzn_qnty=12;
						else if($costing_per_id_library[$row[csf('job_no')]]==3) $dzn_qnty=12*2;
						else if($costing_per_id_library[$row[csf('job_no')]]==4) $dzn_qnty=12*3;
						else if($costing_per_id_library[$row[csf('job_no')]]==5) $dzn_qnty=12*4;
						else $dzn_qnty=1;
						
						/*$budget_qnty=0; 
						$budget_qnty_sql="select avg_finish_cons from  wo_pre_cost_fabric_cost_dtls where job_no='$row[job_no]' and  fab_nature_id=3 and status_active=1 and is_deleted=0";
						$dataArrayBudget=sql_select($budget_qnty_sql);
						foreach($dataArrayBudget as $row_budget)
						{
							$avg_finish_cons_for_ord=($row_budget[csf("avg_finish_cons")]/$dzn_qnty)*$plan_cut_qnty;
							$budget_qnty+=$avg_finish_cons_for_ord;
						}*/
						
						$booking_array=array(); $color_data_array=array();
						$required_qnty=0; $main_booking=''; $sample_booking=''; $main_booking_excel=''; $sample_booking_excel='';
						 /*$sql_wo="select a.id, a.booking_no, a.insert_date, a.item_category, a.fabric_source, a.company_id, a.booking_type, b.fabric_color_id, sum(b.fin_fab_qnty) as req_qnty, sum(b.grey_fab_qnty) as grey_req_qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(3) and b.po_break_down_id=$row[po_id] and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $color_cond_search group by a.id, a.booking_no, a.insert_date, a.item_category, a.fabric_source, a.company_id, a.booking_type, b.fabric_color_id";
						 $dataArray=sql_select($sql_wo);*/
						$booking_no=$booking_data_arr[$po_id]['booking_no'];
						$book_id=$booking_data_arr[$po_id]['id'];
					
						 $req_qnty=$booking_data_arr[$po_id]['req_qnty'];
						 //$booking_type=$booking_data_arr[$po_id]['booking_type'];
						// $item_category=$booking_data_arr[$po_id]['item_category'];
						// $fabric_source=$booking_data_arr[$po_id]['fabric_source'];
						// $company_id=$booking_data_arr[$po_id]['company_id'];
						//echo $fabric_color_id=$booking_data_arr[$po_id]['fabric_color_id'];
						//$req_qnty=0;
						
							$booking_data=array_filter(explode(",",substr($dataArrayWo[$po_id],0,-1)));
							
						//echo $dataArrayWo[$po_id];
						//print_r( $booking_data); 
						//$dataArray=explode(",",substr($dataArrayWo[$po_id],0,-1));
						  //  echo $fabric_color_id.'ddd';
							
						//if(count($dataArray)>0)
						if(count($booking_data)>0)
						{
							//foreach($dataArray as $row_wo)
							//{
								//$required_qnty+=$req_qnty;//$row_wo[csf('req_qnty')];
								foreach($booking_data as $row_wo)
								{
										$woRow=explode("**",$row_wo);
										$id=$woRow[0];
										$booking_no=$woRow[1];
										$insert_date=$woRow[2];
										$item_category=$woRow[3];
										$fabric_source=$woRow[4];
										$company_id=$woRow[5];
										$booking_type=$woRow[6];
										$job_no=$woRow[7];
										$fabric_color_id=$woRow[8];
										$required_qnty=$woRow[9];
										$grey_req_qnty=$woRow[10];
										$is_approved=$woRow[11];
										
										
								//echo $fabric_color_id;
	
								if(!in_array($id,$booking_array))
								{
									//$system_date=date('d-M-Y', strtotime($row_wo[csf('insert_date')]));
									 $system_date=date('d-M-Y', strtotime($insert_date));
									if($booking_type==4)
									{
										$sample_booking.="<a href='##' style='color:#000' onclick=\"generate_worder_report('2','$booking_no','$company_id','$po_id','$item_category','$fabric_source','$job_no','$is_approved')\">
										<font style='font-weight:bold' color='$wo_color;'>$booking_no ($system_date)</font></a><br>";
										
										/*$sample_booking.="<a href='##' style='color:#000' onclick=\"generate_worder_report('2','".$booking_no."','".$company_id."','".$row[csf('$po_id')]."','".$item_category."','".$fabric_source."','".$job_no."','".$is_approved."')\"><font style='font-weight:bold' color='$wo_color;'>".$booking_no."(".$system_date.")"."</font></a><br>";*/
										
										$sample_booking_excel.="<font style='font-weight:bold' color='$wo_color;'>$booking_no ($system_date)</font><br>";
									}
									else
									{
										$main_booking.="<a href='##' style='color:#000' onclick=\"generate_worder_report('1','$booking_no','$company_id','$po_id','$item_category','$fabric_source','$job_no','$is_approved')\">
										<font style='font-weight:bold' color='$wo_color;'>$booking_no ($system_date)</font></a><br>";
										$main_booking_excel.="<font style='font-weight:bold' color='$wo_color;'>$booking_no ($system_date)</font><br>";
									}
									
									$booking_array[]=$id;
								}
								//echo $fabric_color_id;
								//print_r($color_data_array);
								if(array_key_exists($fabric_color_id, $color_data_array))
								{
									$color_data_array[$fabric_color_id]+=$required_qnty;
								}
								else
								{
									 $color_data_array[$fabric_color_id]=$required_qnty;
								}
								//echo $color_data_array[$fabric_color_id];
							}//loop end
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
						
						//$sql_finish_color="SELECT color_id FROM order_wise_pro_details WHERE entry_form in(17) and color_id<>0 and po_breakdown_id=$row[po_id] $color_cond_prop group by color_id";
						
						//$result=sql_select($sql_finish_color);
						/* $finish_color_data=explode(",",$finish_color_arr[$fabric_color_id]['po_id']);
						foreach($finish_color_data as $row_finish_color)
						{
							//echo  $row_finish_color;
							if(!array_key_exists($row_finish_color, $color_data_array))
							{
								$color_data_array[$row_finish_color]+=0;
							}
						}*/
						//echo $finish_color_array[$row[csf('po_id')]]['color'];
						$finish_color=array_unique(explode(",", $finish_color_array[$po_id]['color']));
						foreach($finish_color as $color_id)
						{
							//echo $color_id;
							if($color_id>0)
							{ 
							$color_data_array[$color_id]+=0;
							}
						}
						
						/*foreach($po_id_data as $val)
							{
								//$finish_color=array_unique(explode(",",$finish_color_arr[$val]['color_id']));
								$finish_color=$finish_color_arr[$val]['color_id'];
								if(!array_key_exists($finish_color, $color_data_array))
								{
									$color_data_array[$finish_color]+=0;
								}
							}
						*/
						$tot_order_qnty+=$order_qnty_in_pcs;
						if($required_qnty>$job_mkt_required) $bgcolor_grey_td='#FF0000'; $bgcolor_grey_td='';
						$po_entry_date=date('d-m-Y', strtotime($row[csf('insert_date')]));
						$costing_date=$costing_date_library[$row[csf('job_no')]];
						
						 $tot_color=count($color_data_array);	
						
						if($tot_color>0)
						{
							//echo $tot_color.'KKK';
							$z=1;
							foreach($color_data_array as $key=>$value)
							{
								
								if($z==1) 
								{
									$display_font_color="";
									$font_end="";
									 
								}
								else 
								{
									$display_font_color="<font style='display:none' color='$bgcolor'>";
									$font_end="</font>";
								}
								
								if($z==1)
								{ 
									$budget_qnty=($budget_qnty_arr[$row[csf("job_no")]]['avg_cons']/$dzn_qnty)*$plan_cut_qnty;
									$fabric_desc=explode(",",$fabric_desc_details[$row[csf('job_no')]]);
									$html.="<tr bgcolor='".$bgcolor."'  onclick='change_color('tr_ $i;','$bgcolor;')' id='tr_$i'>
											<td align='left'>".$i."</td>
											<td>".$buyer_short_name_library[$row[csf('buyer_name')]]."</td>
											<td align='left'>".$row[csf('style_ref_no')]."</td>
											<td align='left'>".$row[csf('po_number')]."</td>
											<td align='center'>".$row[csf('job_no_prefix_num')]."</td>
											
											<td align='left'>".$gmts_item."</td>
											<td align='right'>".$order_qnty_in_pcs."</td>
											<td align='left'>".change_date_format($row[csf('pub_shipment_date')])."</td>
											<td align='center'>".change_date_format($row[csf('po_received_date')])."</td>
											<td align='center'>".$po_entry_date."</td>
											<td>".$shipment_status[$row[csf('shiping_status')]]."</td>
											<td align='left'>".join(",<br>",array_unique($fabric_desc))."</td>
											<td align='right'>".$budget_qnty."</td>
											<td align='left'>".$main_booking_excel."</td>
											<td align='left'>".$sample_booking_excel."</td>";
									
									/*$html_short.="<tr bgcolor='".$bgcolor."'>
												<td align='left'>".$i."</td>
												<td>".$buyer_short_name_library[$row[csf('buyer_name')]]."</td>
												<td align='left'>".$row[csf('po_number')]."</td>
												
												
												
												<td align='right'>".$order_qnty_in_pcs."</td>
												<td align='left'>".change_date_format($row[csf('pub_shipment_date')])."</td>
												<td align='left'>".$main_booking_excel."</td>
												<td align='left'>".$sample_booking_excel."</td>";	*/		
									
								}
								else
								{
									$html.="<tr bgcolor='".$bgcolor."' onclick='change_color('tr_ $i;','$bgcolor;')' id='tr_$i'>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>";
											
									/*$html_short.="<tr bgcolor='".$bgcolor."'>
													<td></td>
													<td></td>
													<td></td>
													<td></td>
													<td></td>
													<td></td>
													<td></td>";		*/
								}
							?>
								<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<td width="40"><? echo $display_font_color.$i.$font_end; ?></td>
                                    <td width="80"><p><? echo $display_font_color.$buyer_short_name_library[$row[csf('buyer_name')]].$font_end; ?></p></td>
									<td width="130"><p><? echo $display_font_color.$row[csf('style_ref_no')].$font_end; ?></p></td>
                                    <td width="120"><p><? echo $display_font_color; ?><a href="##" onclick="generate_pre_cost_report('<? echo $precost_button;?>','<? echo $row[csf('po_id')]; ?>','<? echo $row[csf('company_name')]; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('style_ref_no')]; ?>','<? echo $costing_date; ?>')"><? echo $row[csf('po_number')]; ?></a><? echo $font_end; ?></p></td>
                                    <td width="100" align="center"><? echo $display_font_color.$row[csf('job_no_prefix_num')].$font_end; ?></td>
									
									
									
									
									<td width="140"><p><? echo $display_font_color.$gmts_item; ?></p></td>
									<td width="100" align="right"><? if($z==1) echo number_format($order_qnty_in_pcs,0,'.',''); ?></td>
									<td width="80" align="center"><? echo $display_font_color.change_date_format($row[csf('pub_shipment_date')]).$font_end; ?></td>
									<td width="80" align="center"><? echo $display_font_color.change_date_format($row[csf('po_received_date')]).$font_end; ?></td>
									<td width="80" align="center"><? echo $display_font_color.$po_entry_date.$font_end; ?></td>
									<td width="100" align="center"><? echo $display_font_color.$shipment_status[$row[csf('shiping_status')]].$font_end; ?></td>
                                    <td width="150"> 
										<p>
											<? $fabric_desc=explode(",",$fabric_desc_details[$row[csf('job_no')]]); echo $display_font_color.join(",<br>",array_unique($fabric_desc)).$font_end; ?>
										</p>
                                       
									</td>
                                   
                                    <td width="100" align="right">
                                    <? 
                                        if($z==1) 
                                        {
											
											if(array_key_exists($row[csf('buyer_name')], $budget_qnty_data_array))
											{
												$budget_qnty_data_array[$row[csf('buyer_name')]]+=$budget_qnty;
											}
											else
											{
												$budget_qnty_data_array[$row[csf('buyer_name')]]=$budget_qnty;
											}
                                            echo number_format($budget_qnty,2,'.','');
											$total_budget_qnty+=$budget_qnty;
                                        }
                                    ?>
                                    </td>
                                    <td width="125"><? echo $display_font_color.$main_booking.$font_end; ?></td>
									<td width="125"><? echo $display_font_color.$sample_booking.$font_end; ?></td>
									<td width="70" align="right">
										<? 
											
											 
											 $html.="<td>"; 
											 if($z==1) 
                                             {
												 if(array_key_exists($row[csf('buyer_name')], $booking_qnty_array))
												 {
													$booking_qnty_array[$row[csf('buyer_name')]]+=$required_qnty;
												 }
												 else
												 {
													$booking_qnty_array[$row[csf('buyer_name')]]=$required_qnty;
												 }
												 echo number_format($required_qnty,2,'.','');
												 $html.=number_format($required_qnty,2); 
												 $tot_booking_qnty+=$required_qnty;
                                             }
											 $html.="</td>";
										?>
									</td>
									
                                    
                                   
                                    <? $html.="<td bgcolor='#FF9BFF'>"; $html_short.="<td bgcolor='#FF9BFF'>"; ?>
                                    <td width="100" align="center" bgcolor="#FF9BFF">
                                        <p>
                                            <? 
                                                if($key==0)
                                                {
                                                    echo "-";
                                                    $html.="-"; $html_short.="-";
                                                }
                                                else
                                                { 
                                                    echo $color_array[$key]; 
                                                    $html.=$color_array[$key]; $html_short.=$color_array[$key];
                                                }
                                            
                                            ?>
                                        </p>
                                    </td>
                                    <? $html.="</td><td>"; $html_short.="</td>"; ?>
                                    <td width="100" align="right">
                                        <? 
                                            echo number_format($value,2,'.','');
                                            $html.=number_format($value,2);
                                            
                                            if(array_key_exists($row[csf('buyer_name')], $fin_fab_Requi_array))
                                            {
                                                $fin_fab_Requi_array[$row[csf('buyer_name')]]+=$value;
                                            }
                                            else
                                            {
                                                $fin_fab_Requi_array[$row[csf('buyer_name')]]=$value;
                                            }
                                            $tot_color_wise_req+=$value; 
                                        ?>
                                    </td>
                                    <? 
										/*$sql_prop=sql_select("select 
													sum(CASE WHEN entry_form ='17' THEN quantity ELSE 0 END) AS finish_receive,
													sum(CASE WHEN entry_form ='19' THEN quantity ELSE 0 END) AS finish_issue from order_wise_pro_details where po_breakdown_id=$row[po_id] and color_id=$key and status_active=1 and is_deleted=0");
										$fab_recv_qnty=$sql_prop[0][csf('finish_receive')];
										$issue_to_cut_qnty=$sql_prop[0][csf('finish_issue')];*/
										
										$fab_recv_qnty=$finish_prod_qty_arr[$row[csf("po_id")]][$key]['finish_receive'];
										$issue_to_cut_qnty=$finish_prod_qty_arr[$row[csf("po_id")]][$key]['finish_issue'];
									?>
                                    
                                    <? $html.="</td><td>"; $html_short.="</td><td>"; ?>
                                    <td width="100" align="right">
                                        <a  href="##" onclick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')]; ?>','fabric_receive','<? echo $key; ?>')"><? echo number_format($fab_recv_qnty,2,'.',''); ?></a>
                                        <?
                                            $html.=number_format($fab_recv_qnty,2);
                                            $html_short.=number_format($fab_recv_qnty,2);
                                            
                                            if(array_key_exists($row[csf('buyer_name')], $fin_fab_recei_array))
                                            {
                                                $fin_fab_recei_array[$row[csf('buyer_name')]]+=$fab_recv_qnty;
                                            }
                                            else
                                            {
                                                $fin_fab_recei_array[$row[csf('buyer_name')]]=$fab_recv_qnty;
                                            }
                                            $tot_fabric_recv+=$fab_recv_qnty;
                                        ?>
                                    </td>
                                    <? $html.="</td><td>"; ?>
                                    <td width="100" align="right">
                                        <?
                                            $fabric_balance=$value-$fab_recv_qnty;
                                            echo number_format($fabric_balance,2,'.',''); 
                                            $html.=number_format($fabric_balance,2);
                                            
                                            if(array_key_exists($row[csf('buyer_name')], $fin_balance_array))
                                            {
                                                $fin_balance_array[$row[csf('buyer_name')]]+=$fabric_balance;
                                            }
                                            else
                                            {
                                                $fin_balance_array[$row[csf('buyer_name')]]=$fabric_balance;
                                            }
                                            
                                            $tot_fabric_balance+=$fabric_balance;
                                        ?>
                                    </td>
                                    <? $html.="</td><td>"; $html_short.="</td><td>"; ?>
                                    <td width="" align="right">
                                     	<a  href="##" onclick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')]; ?>','issue_to_cut','<? echo $key; ?>')"><? echo number_format($issue_to_cut_qnty,2,'.',''); ?></a>
                                        <?
                                            $html.=number_format($issue_to_cut_qnty,2);
                                            $html_short.=number_format($issue_to_cut_qnty,2);
                                            
                                            if(array_key_exists($row[csf('buyer_name')], $issue_to_cut_array))
                                            {
                                                $issue_to_cut_array[$row[csf('buyer_name')]]+=$issue_to_cut_qnty;
                                            }
                                            else
                                            {
                                                $issue_to_cut_array[$row[csf('buyer_name')]]=$issue_to_cut_qnty;
                                            }
                                            
                                            $tot_issue_to_cut_qnty+=$issue_to_cut_qnty;
                                        ?>
                                    </td>
                                    <? $html.="</td>"; $html_short.="</td>"; ?>
									
								</tr>
							<?	
								
							$i++;
							//$k++;
							}
							$z++;
                        }
						else
						{
							$fabric_desc=explode(",",$fabric_desc_details[$row[csf('job_no')]]);
							$html.="<tr bgcolor='".$bgcolor."' onclick='change_color('tr_ $i;','$bgcolor;')' id='tr_$i'>
											<td align='left'>".$i."</td>
											<td>".$buyer_short_name_library[$row[csf('buyer_name')]]."</td>
											<td align='left'>".$row[csf('style_ref_no')]."</td>
											<td align='left'>".$row[csf('po_number')]."</td>
											<td align='center'>".$row[csf('job_no_prefix_num')]."</td>
											<td align='left'>".$gmts_item."</td>
											<td align='right'>".$order_qnty_in_pcs."</td>
											<td align='left'>".change_date_format($row[csf('pub_shipment_date')])."</td>
											<td align='center'>".change_date_format($row[csf('po_received_date')])."</td>
											<td align='center'>".$po_entry_date."</td>
											<td>".$shipment_status[$row[csf('shiping_status')]]."</td>
											<td align='left'>".join(",<br>",array_unique($fabric_desc))."</td>
											<td align='right'>".$budget_qnty."</td>
											<td align='left'>".$main_booking_excel."</td>
											<td align='left'>".$sample_booking_excel."</td>";
							
							/*$html_short.="<tr bgcolor='".$bgcolor."'>
										<td align='left'>".$i."</td>
										<td>".$buyer_short_name_library[$row[csf('buyer_name')]]."</td>
										<td align='left'>".$row[csf('po_number')]."</td>
										
										
										
										<td align='right'>".$order_qnty_in_pcs."</td>
										<td align='left'>".change_date_format($row[csf('pub_shipment_date')])."</td>
										<td align='left'>".$main_booking_excel."</td>
										<td align='left'>".$sample_booking_excel."</td>";*/
													
						?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                <td width="40"><? echo $i; ?></td>
                                <td width="80"><p><? echo $buyer_short_name_library[$row[csf('buyer_name')]]; ?></p></td>
                                <td width="130"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
                                <td width="120"><p><a href="##" onclick="generate_pre_cost_report('<? echo $precost_button;?>','<? echo $row[csf('job_no')]; ?>','<? echo $row[csf('company_name')]; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('style_ref_no')]; ?>','<? echo $costing_date; ?>')"><? echo $row[csf('po_number')]; ?></a></p></td>
                                <td width="100" align="center"><? echo $row[csf('job_no_prefix_num')]; ?></td>
                                
                                <td width="140"><p><? echo $gmts_item; ?></p></td>
                                <td width="100" align="right"><? echo number_format($order_qnty_in_pcs,0,'.',''); ?></td>
                                <td width="80" align="center"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
                                <td width="80" align="center"><? echo change_date_format($row[csf('po_received_date')]); ?></td>
                                <td width="80" align="center"><? echo $po_entry_date; ?></td>
                                <td width="100" align="center"><? echo $shipment_status[$row[csf('shiping_status')]]; ?></td>
                                <td width="150"> 
                                    <p>
                                        <? $fabric_desc=explode(",",$fabric_desc_details[$row[csf('job_no')]]); echo join(",<br>",array_unique($fabric_desc)); ?>
                                    </p>
                                </td>
                                <td width="100" align="right">
                                <? 
								$total_budget_qnty+=$budget_qnty;
								if(array_key_exists($row[csf('buyer_name')], $budget_qnty_data_array))
								{
									$budget_qnty_data_array[$row[csf('buyer_name')]]+=$budget_qnty;
								}
								else
								{
									$budget_qnty_data_array[$row[csf('buyer_name')]]=$budget_qnty;
								}
									echo number_format($budget_qnty,2,'.','');
                                ?>
                                </td>
                                <td width="125"><? echo $main_booking; ?></td>
                                <td width="125"><? echo $sample_booking; ?></td>
                                <td width="70" align="right">
									
                                </td>
                                
                                
                                
                                <td width="100">&nbsp;</td>
                                <td width="100">&nbsp;</td>
                                
                                <td width="100">&nbsp;</td>
                                <td width="100">&nbsp;</td>
                                <td width="">&nbsp;</td>
                                
                            </tr>
                        	<?	
                            	$html.="<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								
								</tr>
								";
								
								/*$html_short.="</td><td bgcolor='$bgcolor_grey_td'>".number_format($required_qnty,2)."</td>
								<td>".number_format($grey_recv_qnty,2)."</td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								</tr>
								";*/
                        $k++;
						}
					$i++;	
					}// end main query
				}// order wise end==================
				
				//style wise start =========================
				else
				{
					//echo 'DDRRR';
					
					foreach($nameArray as $row)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						//echo 'DRRR';
						$buyer_name_array[$row[csf('buyer_name')]]=$buyer_short_name_library[$row[csf('buyer_name')]];
						
						$order_qnty_in_pcs=$row[csf('po_qnty')]*$row[csf('ratio')];
						$plan_cut_qnty=$row[csf('plan_cut')];
						 $po_id_data=explode(",",$row[csf('po_id')]);
						//echo $row[csf('po_id')];
						$gmts_item='';
						$gmts_item_id=explode(",",$row[csf('gmts_item_id')]);
						foreach($gmts_item_id as $item_id)
						{
							if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
						}
						
						$dzn_qnty=0; $balance=0; $job_mkt_required=0; $yarn_issued=0;
						if($costing_per_id_library[$row[csf('job_no')]]==1) $dzn_qnty=12;
						else if($costing_per_id_library[$row[csf('job_no')]]==3) $dzn_qnty=12*2;
						else if($costing_per_id_library[$row[csf('job_no')]]==4) $dzn_qnty=12*3;
						else if($costing_per_id_library[$row[csf('job_no')]]==5) $dzn_qnty=12*4;
						else $dzn_qnty=1;
						
						/*$budget_qnty=0;
						$budget_qnty_sql="select avg_finish_cons from  wo_pre_cost_fabric_cost_dtls where job_no='$row[job_no]' and  fab_nature_id=3 and status_active=1 and is_deleted=0";
						$dataArrayBudget=sql_select($budget_qnty_sql);
						foreach($dataArrayBudget as $row_budget)
						{
							$avg_finish_cons_for_ord=($row_budget[csf("avg_finish_cons")]/$dzn_qnty)*$plan_cut_qnty;
							$budget_qnty+=$avg_finish_cons_for_ord;
						}*/
						
						
						
						
						 $main_booking=''; $sample_booking=''; $main_booking_excel=''; $sample_booking_excel=''; $booking_data='';
						/*$sql_wo="select a.id, a.booking_no, a.insert_date, a.item_category, a.fabric_source, a.company_id, a.booking_type, b.fabric_color_id, sum(b.fin_fab_qnty) as req_qnty, sum(b.grey_fab_qnty) as grey_req_qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(3) and b.po_break_down_id in ($row[po_id]) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $color_cond_search group by a.booking_no, b.fabric_color_id";
						$dataArray=sql_select($sql_wo);*/
						 $req_qnty=0;
						foreach( $po_id_data as $po_id)
						{
							//echo $po_id;
							 $req_qnty+=$booking_data_arr[$po_id]['req_qnty'];
							// echo $dataArrayWo[$po_id];
							 $booking_data.=implode(",",array_filter(explode(",",substr($dataArrayWo[$po_id],0,-1)))).",";
						}
						//print_r( $booking_data); 
						 $required_qnty=0; $booking_array=array(); $color_data_array=array();
						$dataArray=explode(",",substr($booking_data,0,-1));
						if(count($dataArray)>0)
						{
							$is_short='';
							
							foreach($dataArray as $row_wo)
							{
									
									$woRow=explode("**",$row_wo);
									$id=$woRow[0];
									$booking_no=$woRow[1];
									$insert_date=$woRow[2];
									$item_category=$woRow[3];
									$fabric_source=$woRow[4];
									$company_id=$woRow[5];
									$booking_type=$woRow[6];
									$job_no=$woRow[7];
									//echo 'ffdd';
									$fabric_color_id=$woRow[8];
									$required_qnty+=$woRow[9];
									$grey_req_qnty=$woRow[10];
									$is_approved=$woRow[11];
									$is_short=$woRow[12];
									
	
								if(!in_array($id,$booking_array))
								{
									
									/*$system_date=date('d-M-Y', strtotime($insert_date));
									//if($system_date=="") $system_date="";else $system_date=change_date_format($system_date);
									if($booking_type==4)
									{
										$sample_booking.="<a href='##' style='color:#000' onclick=\"generate_worder_report('2','$booking_no','$company_id','$po_id_data',' $item_category','$fabric_source')\">
										<font style='font-weight:bold' color='$wo_color;'>$booking_no (".$system_date.")</font></a><br>";
										$sample_booking_excel.="<font style='font-weight:bold' color='$wo_color;'>$booking_no ($system_date)</font><br>";
									}
									else
									{
										
										
										 $main_booking.="<a href='##' style='color:#000' onclick=\"generate_worder_report('1','$booking_no','$company_id','$po_id','$item_category','$fabric_source')\">
										<font style='font-weight:bold' color='$wo_color;'>$booking_no ($system_date)</font></a><br>";
										 $main_booking_excel.="<font style='font-weight:bold' color='$wo_color;'>$booking_no ($system_date)</font><br>";
									}*/
									
										if($insert_date!="")
										{
										 $system_date=date('d-M-Y', strtotime($insert_date));
										}
										else
										{
										$system_date='No Booking';	
										}
										
										if($booking_type==4)
										{  
											$sample_booking.="<a href='##' style='color:#000' onclick=\"generate_worder_report('2','".$booking_no."','".$company_id."','".$row[csf('po_id')]."','".$item_category."','".$fabric_source."','".$job_no."','".$is_approved."')\"><font style='font-weight:bold' color='$wo_color;'>".$booking_no."(".$system_date.")"."</font></a><br>";
											/*$sample_booking.="<a href='##' style='color:#000' onclick=\"generate_worder_report('3','".$booking_no."','".$company_id."','".$row[csf('po_id')]."','".$item_category."','".$fabric_source."','".$job_no."','".$is_approved."')\"><font style='font-weight:bold' color='$wo_color;'>".$booking_no."(".$system_date.")"."</font></a><br>";*/
											
											$sample_booking_excel.="<font style='font-weight:bold' color='$wo_color;'>".$booking_no."(".$system_date.")"."</font><br>";
										}
										else
										{
											if($is_short==1) $pre="S"; else $pre="M"; 
											//echo 'as';
											$main_booking.="<a href='##' style='color:#000' onclick=\"generate_worder_report('".$is_short."','".$booking_no."','".$company_id."','".$row[csf('po_id')]."','".$item_category."','".$fabric_source."','".$job_no."','".$is_approved."')\"><font style='font-weight:bold' color='$wo_color;'>".$booking_no."(".$system_date.")</font></a><br>";
											$main_booking_excel.="<font style='font-weight:bold' color='$wo_color;'>".$booking_no."(".$system_date.")</font><br>";
										}
									$booking_array[]=$id;
								}
								$color_data_array[$fabric_color_id]+=$required_qnty;
								/*if(array_key_exists($fabric_color_id, $color_data_array))
								{
									$color_data_array[$fabric_color_id]+=$required_qnty;
								}
								else
								{
									$color_data_array[$fabric_color_id]=$required_qnty;
								}*/
							}
						}
						else
						{
							
							$main_booking.="No Booking";
							if($main_booking_excel=="") $main_booking_excel.="No Booking";
							//echo $main_booking_excel;
							$sample_booking.="No Booking";
							$sample_booking_excel.="No Booking";
						}
						
						if($main_booking=="")
						{
							$main_booking.="No Booking";
							if($main_booking_excel=="") $main_booking_excel.="No Booking";
							//echo $main_booking_excel;
							
						}
						
						if($sample_booking=="") 
						{
							$sample_booking.="No Booking";
							$sample_booking_excel.="No Booking";
						}
						
					/*	$sql_finish_color="SELECT color_id FROM order_wise_pro_details WHERE entry_form in(17) and color_id<>0 and po_breakdown_id in ($row[po_id]) $color_cond_prop group by color_id";
						$result=sql_select($sql_finish_color);*/
						foreach($po_id_data as $val)
							{
								//$finish_color=array_unique(explode(",",$finish_color_arr[$val]['color_id']));
								$finish_color=$finish_color_arr[$val]['color_id'];
								if(!array_key_exists($finish_color, $color_data_array))
								{
									$color_data_array[$finish_color]+=0;
								}
							}
						
						
						$tot_order_qnty+=$order_qnty_in_pcs;
				
						if($required_qnty>$job_mkt_required) $bgcolor_grey_td='#FF0000'; $bgcolor_grey_td='';
						
						$po_entry_date=date('d-m-Y', strtotime($row[csf('insert_date')]));
						$costing_date=$costing_date_library[$row[csf('job_no')]];
						
						$tot_color=count($color_data_array);	
						
						if($tot_color>0)
						{
							$z=1;
							foreach($color_data_array as $key=>$value)
							{
								//echo $value;
								if($z==1) 
								{
									$display_font_color="";
									$font_end="";
								}
								else 
								{
									$display_font_color="<font style='display:none' color='$bgcolor'>";
									$font_end="</font>";
								}
								
								if($z==1)
								{
									$budget_qnty=($budget_qnty_arr[$row[csf("job_no")]]['avg_cons']/$dzn_qnty)*$plan_cut_qnty;
									$fabric_desc=explode(",",$fabric_desc_details[$row[csf('job_no')]]);
									$html.="<tr bgcolor='".$bgcolor."' onclick='change_color('tr_ $i;','$bgcolor;')' id='tr_$i'>
											<td align='left'>".$i."</td>
											<td>".$buyer_short_name_library[$row[csf('buyer_name')]]."</td>
											<td align='left'>".$row[csf('style_ref_no')]."</td>
											<td align='left'>".$row[csf('po_number')]."</td>
											<td align='center'>".$row[csf('job_no')]."</td>
											
											
											
											
											<td align='left'>".$gmts_item."</td>
											<td align='right'>".$order_qnty_in_pcs."</td>
											<td align='left'>View</td>
											<td align='left'>".join(",<br>",array_unique($fabric_desc))."</td>
											<td align='right'>".$budget_qnty."</td>
											<td align='left'>".$main_booking_excel."</td>
											<td align='left'>".$sample_booking_excel."</td>";
									
									/*$html_short.="<tr bgcolor='".$bgcolor."'>
												<td align='left'>".$i."</td>
												<td>".$buyer_short_name_library[$row[csf('buyer_name')]]."</td>
												
												<td align='left'>".$row[csf('po_number')]."</td>
												
												<td align='right'>".$order_qnty_in_pcs."</td>
												<td align='left'>View</td>
												<td align='left'>".$main_booking_excel."</td>
												<td align='left'>".$sample_booking_excel."</td>";	*/		
									
								}
								else
								{
									$html.="<tr bgcolor='".$bgcolor."' onclick='change_color('tr_ $i;','$bgcolor;')' id='tr_$i'>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>
											<td></td>";
											
									$html_short.="<tr bgcolor='".$bgcolor."' onclick='change_color('tr_ $i;','$bgcolor;')' id='tr_$i'>
													<td></td>
													<td></td>
													<td></td>
													<td></td>
													<td></td>
													<td></td>
													<td></td>";		
								}
								
							?>
								<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<td width="40"><? echo $display_font_color.$i.$font_end; ?></td>
                                    <td width="80"><p><? echo $display_font_color.$buyer_short_name_library[$row[csf('buyer_name')]].$font_end; ?></p></td>
									<td width="130"><p><? echo $display_font_color.$row[csf('style_ref_no')].$font_end; ?></p></td>
                                    <td width="120"><p><? echo $display_font_color.$row[csf('po_number')]. $font_end; ?></p></td>
                                    <td width="100" align="center"><? echo $display_font_color; ?><a href="##" onclick="generate_pre_cost_report('<? echo $precost_button;?>','<? echo $row[csf('job_no')]; ?>','<? echo $row[csf('company_name')]; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('style_ref_no')]; ?>','<? echo $costing_date; ?>')"><? echo $row[csf('job_no')]; ?></a><? echo $font_end; ?></td>
									
									
									
									
									<td width="140"><p><? echo $display_font_color.$gmts_item; ?></p></td>
									<td width="100" align="right"><? if($z==1) echo number_format($order_qnty_in_pcs,0,'.',''); ?></td>
									<td width="80" align="center"><? echo $display_font_color; ?><a href="##" onclick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')]; ?>','Shipment_date','')"><? echo "View"; ?></a><? echo $font_end; ?></td>
                                    <td width="150"> 
										<p>
											<? $fabric_desc=explode(",",$fabric_desc_details[$row[csf('job_no')]]); echo $display_font_color.join(",<br>",array_unique($fabric_desc)).$font_end; ?>
										</p>
                                        <? 
										?>
									</td>
                                  
                                    <td width="100" align="right">
                                    <? 
                                        if($z==1) 
                                        {
											
											if(array_key_exists($row[csf('buyer_name')], $budget_qnty_data_array))
											{
												$budget_qnty_data_array[$row[csf('buyer_name')]]+=$budget_qnty;
											}
											else
											{
												$budget_qnty_data_array[$row[csf('buyer_name')]]=$budget_qnty;
											}
                                            echo number_format($budget_qnty,2,'.','');
											$total_budget_qnty+=$budget_qnty;
                                          
                                        }
                                    ?>
                                    </td>
                                    <td width="125"><? echo $display_font_color.$main_booking.$font_end; ?></td>
									<td width="125"><? echo $display_font_color.$sample_booking.$font_end; ?></td>
									<td width="70" align="right">
										<? 
											 
											 $html.="<td>";
											 if($z==1) 
                                             {
												 if(array_key_exists($row[csf('buyer_name')], $booking_qnty_array))
												 {
													$booking_qnty_array[$row[csf('buyer_name')]]+=$required_qnty;
												 }
												 else
												 {
													$booking_qnty_array[$row[csf('buyer_name')]]=$required_qnty;
												 }
												 echo number_format($required_qnty,2,'.','');
												 $html.=number_format($required_qnty,2); 
												 $tot_booking_qnty+=$required_qnty;
                                             }
										?>
									</td>
									
                                    
                                    
                                    <? $html.="</td><td bgcolor='#FF9BFF'>"; $html_short.="<td bgcolor='#FF9BFF'>"; ?>
                                    <td width="100" align="center" bgcolor="#FF9BFF">
                                        <p>
                                            <? 
                                                if($key==0)
                                                {
                                                    echo "-";
                                                    $html.="-"; $html_short.="-";
                                                }
                                                else
                                                { 
                                                    echo $color_array[$key]; 
                                                    $html.=$color_array[$key]; $html_short.=$color_array[$key];
                                                }
                                            
                                            ?>
                                        </p>
                                    </td>
                                    <? $html.="</td><td>"; $html_short.="</td>"; ?>
                                    <td width="100" align="right">
                                        <? 
                                            echo number_format($value,2,'.','');
                                            $html.=number_format($value,2);
                                            
                                            if(array_key_exists($row[csf('buyer_name')], $fin_fab_Requi_array))
                                            {
                                                $fin_fab_Requi_array[$row[csf('buyer_name')]]+=$value;
                                            }
                                            else
                                            {
                                                $fin_fab_Requi_array[$row[csf('buyer_name')]]=$value;
                                            }
                                            $tot_color_wise_req+=$value; 
                                        ?>
                                    </td>
                                    <? 
										/*
										$sql_prop=sql_select("select 
													sum(CASE WHEN entry_form ='17' THEN quantity ELSE 0 END) AS finish_receive,
													sum(CASE WHEN entry_form ='19' THEN quantity ELSE 0 END) AS finish_issue from order_wise_pro_details where po_breakdown_id in ($row[po_id]) and color_id=$key and status_active=1 and is_deleted=0");
													
										$dye_qnty=$sql_prop[0][csf('dye_receive')];
										$fab_recv_qnty=$sql_prop[0][csf('finish_receive')];
										$issue_to_cut_qnty=$sql_prop[0][csf('finish_issue')];*/
										
										
										$issue_to_cut_qnty=0;$fab_recv_qnty=0;
										foreach($po_id_data as $val)
										{
											$fab_recv_qnty+=$finish_prod_qty_arr[$val][$key]['finish_receive'];
											$issue_to_cut_qnty+=$finish_prod_qty_arr[$val][$key]['finish_issue'];
										}
										//$issue_to_cut_qnty=$finish_prod_qty_arr[$row[csf("po_id")]][$key]['finish_issue'];
									?>
                                  
                                    <? $html.="</td><td>"; $html_short.="</td><td>"; ?>
                                    <td width="100" align="right">
                                        <a  href="##" onclick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')]; ?>','fabric_receive','<? echo $key; ?>')"><? echo number_format($fab_recv_qnty,2,'.',''); ?></a>
                                        <?
                                            $html.=number_format($fab_recv_qnty,2);
                                            $html_short.=number_format($fab_recv_qnty,2);
                                            
                                            if(array_key_exists($row[csf('buyer_name')], $fin_fab_recei_array))
                                            {
                                                $fin_fab_recei_array[$row[csf('buyer_name')]]+=$fab_recv_qnty;
                                            }
                                            else
                                            {
                                                $fin_fab_recei_array[$row[csf('buyer_name')]]=$fab_recv_qnty;
                                            }
                                            $tot_fabric_recv+=$fab_recv_qnty;
                                        ?>
                                    </td>
                                    <? $html.="</td><td>"; ?>
                                    <td width="100" align="right">
                                        <?
                                            $fabric_balance=$value-$fab_recv_qnty;
                                            echo number_format($fabric_balance,2,'.',''); 
                                            $html.=number_format($fabric_balance,2);
                                            
                                            if(array_key_exists($row[csf('buyer_name')], $fin_balance_array))
                                            {
                                                $fin_balance_array[$row[csf('buyer_name')]]+=$fabric_balance;
                                            }
                                            else
                                            {
                                                $fin_balance_array[$row[csf('buyer_name')]]=$fabric_balance;
                                            }
                                            
                                            $tot_fabric_balance+=$fabric_balance;
                                        ?>
                                    </td>
                                    <? $html.="</td><td>"; $html_short.="</td><td>"; ?>
                                    <td width="" align="right">
                                     	<a  href="##" onclick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')]; ?>','issue_to_cut','<? echo $key; ?>')"><? echo number_format($issue_to_cut_qnty,2,'.',''); ?></a>
                                        <?
                                            $html.=number_format($issue_to_cut_qnty,2);
                                            $html_short.=number_format($issue_to_cut_qnty,2);
                                            
                                            if(array_key_exists($row[csf('buyer_name')], $issue_to_cut_array))
                                            {
                                                $issue_to_cut_array[$row[csf('buyer_name')]]+=$issue_to_cut_qnty;
                                            }
                                            else
                                            {
                                                $issue_to_cut_array[$row[csf('buyer_name')]]=$issue_to_cut_qnty;
                                            }
                                            
                                            $tot_issue_to_cut_qnty+=$issue_to_cut_qnty;
											$html.="</td>";
                                        ?>
                                    </td>
                                    
									
								</tr>
							<?	
								
							$i++;
							
							}
							$z++;
							//$k++;
                        }
						else
						{ 
							$budget_qnty=($budget_qnty_arr[$row[csf("job_no")]]['avg_cons']/$dzn_qnty)*$plan_cut_qnty;
							$fabric_desc=explode(",",$fabric_desc_details[$row[csf('job_no')]]);
							$html.="<tr bgcolor='".$bgcolor."' onclick='change_color('tr_ $i;','$bgcolor;')' id='tr_$i'>
											<td align='left'>".$i."</td>
											<td>".$buyer_short_name_library[$row[csf('buyer_name')]]."</td>
											<td align='left'>".$row[csf('style_ref_no')]."</td>
											<td align='left'>".$row[csf('po_number')]."</td>
											<td align='center'>".$row[csf('job_no')]."</td>
											
											
											
											
											<td align='left'>".$gmts_item."</td>
											<td align='right'>".$order_qnty_in_pcs."</td>
											<td align='left'>View</td>
											<td align='left'>".join(",<br>",array_unique($fabric_desc))."</td>
											<td align='right'>".$budget_qnty."</td>
											<td align='left'>".$main_booking_excel."</td>
											<td align='left'>".$sample_booking_excel."</td>";
							
							/*$html_short.="<tr bgcolor='".$bgcolor."'>
										<td align='left'>".$i."</td>
										<td>".$buyer_short_name_library[$row[csf('buyer_name')]]."</td>
										<td align='left'>".$row[csf('po_number')]."</td>
										
										
										
										<td align='right'>".$order_qnty_in_pcs."</td>
										<td align='left'>View</td>
										<td align='left'>".$main_booking_excel."</td>
										<td align='left'>".$sample_booking_excel."</td>";*/
													
						?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
                                <td width="40"><? echo $i; ?></td>
                                <td width="80"><p><? echo $buyer_short_name_library[$row[csf('buyer_name')]]; ?></p></td>
                                <td width="130"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
                                <td width="120"><p><? echo $row[csf('po_number')]; ?></p></td>
                                <td width="100" align="center"><a href="##" onclick="generate_pre_cost_report('<? echo $precost_button;?>','<? echo $row[csf('job_no')]; ?>','<? echo $row[csf('company_name')]; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('style_ref_no')]; ?>','<? echo $costing_date; ?>')"><? echo $row[csf('job_no')]; ?></a></td>
                                
                                
                                
                                
                                <td width="140"><p><? echo $gmts_item; ?></p></td>
                                <td width="100" align="right"><? echo number_format($order_qnty_in_pcs,0,'.',''); ?></td>
                                <td width="80" align="center"><a  href="##" onclick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')]; ?>','Shipment_date','')"><? echo "View"; ?></a></td>
                                <td width="150"> 
                                    <p>
                                        <? $fabric_desc=explode(",",$fabric_desc_details[$row[csf('job_no')]]); echo join(",<br>",array_unique($fabric_desc)); ?>
                                    </p>
                                </td>
                                <td width="100" align="right">
									<?
									        $total_budget_qnty+=$budget_qnty;
											if(array_key_exists($row[csf('buyer_name')], $budget_qnty_data_array))
											{
												$budget_qnty_data_array[$row[csf('buyer_name')]]+=$budget_qnty;
											}
											else
											{
												$budget_qnty_data_array[$row[csf('buyer_name')]]=$budget_qnty;
											}
                                        echo number_format($budget_qnty,2,'.','');
                                        
                                    ?>
                                </td>
                                <td width="125"><? echo $main_booking; ?></td>
                                <td width="125"><? echo $sample_booking; ?></td>
                                <td width="70" align="right">
									<? 
										 $html.="<td>";
											 if($z==1) 
                                             {
                                             echo number_format($required_qnty,2,'.','');
                                             $html.=number_format($required_qnty,2); 
                                             }
										$html.="</td>";
                                    ?>
                                </td>
                                <td width="100">&nbsp;</td>
                                <td width="100">&nbsp;</td>
                                <td width="100">&nbsp;</td>
                                <td width="100">&nbsp;</td>
                                <td width="">&nbsp;</td>
                            </tr>
                        	<?	
                            	$html.="
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								</tr>
								";
								
								/*$html_short.="</td><td bgcolor='$bgcolor_grey_td'>".number_format($required_qnty,2)."</td>
								<td>".number_format($grey_recv_qnty,2)."</td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								</tr>
								";*/
                        $k++;
						}
					$i++;	
					}// end main query
				}
				?>
                </table>
            </div>
            <?
				$html.="<tfoot>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							
							<th>Total</th>
							<th align='right' id='tot_order_qnty'>".number_format($tot_order_qnty,0)."</th>
							<th></th>";
							
				if($type==1)
				{
					$html.="<th></th>
							<th></th>
							<th></th>";
				}
							
				$html.="<th></th>
				            <th align='right' id='value_tot_budget'>".number_format($total_budget_qnty,2)."</th>
				            <th></th>
							<th></th>
				            <th id=''></th>
							
							<th></th>
							<th align='right' id='value_tot_req_qty'>".number_format($tot_color_wise_req,2)."</th>
							<th align='right'>".number_format($tot_fabric_recv,2)."</th>
							<th align='right'>".number_format($tot_fabric_balance,2)."</th>
							<th align='right'>".number_format($tot_issue_to_cut_qnty,2)."</th>
							
						</tfoot>
					</table>
					<br />
					";
					
				$html_short.="<tfoot>
								<th></th>
								<th></th>
							
								
								<th>Total</th>
								<th align='right' id='value_tot_booking'>".number_format($tot_order_qnty,0)."</th>
								<th></th>
								<th></th>
								<th></th>
								<th align='right'>".number_format($tot_yarn_issue_qnty,2)."</th>
								<th align='right'>".number_format($tot_fabric_req,2)."</th>
								<th align='right'>".number_format($tot_grey_recv_qnty,2)."</th>
								<th></th
								<th align='right'>".number_format($tot_dye_qnty,2)."</th>
								<th align='right'>".number_format($tot_fabric_recv,2)."</th>
								<th align='right'>".number_format($tot_issue_to_cut_qnty,2)."</th>
							</tfoot>
						</table>
						<br />
						";
					
			?>
            <table width="<? echo $table_width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                <tfoot>
                    <tr>
                        <th width="40"></th>
                        <th width="80"></th>
                        <th width="130"></th>
                        <th width="120"></th>
                        <th width="100"></th>
                        
                        
                        
                        
                        <th width="140">Total</th>
                        <th width="100" id="tot_order_qnty"><? echo number_format($tot_order_qnty,0); ?></th>
                        <th width="80"> </th>
                        <?
						if($type==1)
						{
						?>
                            <th width="80"></th>
                            <th width="80"></th>
                            <th width="100"></th>
                        <?
						}
						?>
                        <th width="150">&nbsp;</th>
                        <th width="100" id="value_tot_budget"><? echo number_format($total_budget_qnty,2); ?></th>
                        <th width="125"></th>
                        <th width="125"></th>
                        <th width="70" id="value_tot_booking"> <? echo number_format($tot_booking_qnty,2); ?></th>
                        
                       
                        
                        <th width="100"></th>
                        <th width="100" id="value_tot_fin_rec"><? echo number_format($tot_color_wise_req,2); ?></th>
                        
                        <th width="100" id="value_tot_fini_receive"><? echo number_format($tot_fabric_recv,2); ?> </th>
                        <th width="100" id="value_tot_fini_balance"><? echo number_format($tot_fabric_balance,2); ?></th>
                        <th width="" id="value_tot_fini_cut_issue"><? echo number_format($tot_issue_to_cut_qnty,2); ?> </th>
                        
                    </tr>
                </tfoot>
            </table>
            <br />
			<?
                $html.="<table align='center'>
                        <tr valign='top'>
                        <td>
                            <table border=1 rules='all' align='center'>
                            <thead>
                            <tr align='center'>
                                <th colspan='8'>Buyer Level Summary</th>
                            </tr>
                            <tr>
                                <th>SL</th>
                                <th>Buyer Name</th>
                                <th>Budget Qnty</th>
                                <th>Booking Qnty</th>
                                
                                <th>Fin Fab Req</th>
                                <th>Fin Fab Rece</th>
                                <th>Fin Fab Balance</th>
                                <th>Issue To Cut</th>
                            </tr>
                        </thead>
                        <tbody>
                        
                ";
            ?>
            <table align="center">
                <tr valign="top">
                    <td>
                        <div id="data_panel1" align="center" style="width:100%">
                            <input type="button" value="Print" class="formbutton" name="print" id="print" onclick="new_window()" style="width:100px" />
                        </div> 
                        <div id="buyer_summary" style="border:none">
                            <table width="820" class="rpt_table" border="0" rules="all" align="center">
                                <thead>
                                    <tr align="center" id="company_id_td" style="visibility:hidden; border:none">
                                        <th colspan="8" style="border:none">
                                            <font size="3"><strong>Company Name: <?php echo $company_library[$company_name]; ?></strong></font>
                                        </th>
                                    </tr>
                                    <tr align="center" id="date_td" style="visibility:hidden;border:none">
                                         <th colspan="8" style="border:none"><font size="3"><? echo "From ". change_date_format($start_date). " To ". change_date_format($end_date);?></font></th>
                                    </tr>
                                    <tr align="center">
                                        <th colspan="8">Buyer Level Summary</th>
                                    </tr>
                                    <tr>
                                        <th width="40">SL</th>
                                        <th width="130">Buyer Name</th>
                                        <th width="100">Budget Qnty</th>
                                        <th width="100">Booking Qnty </th> 
                                        
                                        
                                        <th width="100">Fin Fab Req</th>
                                        <th width="100">Fin Fab Rece</th>
                                        <th width="100">Fin Fab Balance</th>
                                        <th>Issue To Cut</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?
								//print_r($budget_qnty_data_array);
                                $b_sl=1;
                                $buyer_number=asort($buyer_name_array);
								
                                foreach($buyer_name_array as $key=>$value)
                                {
                                    if ($b_sl%2==0)  
                                        $bgcolor="#E9F3FF";
                                    else
                                        $bgcolor="#FFFFFF";	
                                    
                                    $batch_bl=$grey_required_array[$key]-$batch_qnty_array[$key];
                                    $dye_bl=$grey_required_array[$key]-$dye_qnty_array[$key];
                                    
                                    $html.="<tr bgcolor='$bgcolor'>
                                            <td align='right'>".$b_sl."</td>
                                            <td align='right'>".$value."</td>
                                            <td align='right'>".number_format($budget_qnty_data_array[$key],2)."</td>
                                            <td align='right'>".number_format($booking_qnty_array[$key],2)."</td>
                                            
                                            
                                            <td align='right'>".number_format($fin_fab_Requi_array[$key],2)."</td>
                                            <td align='right'>".number_format($fin_fab_recei_array[$key],2)."</td>
                                            <td align='right'>".number_format($fin_balance_array[$key],2)."</td>
                                            <td align='right'>".number_format($issue_to_cut_array[$key],2)."</td>
                                            </tr>
                                        ";
                                    
                                    ?>
                                    <tr bgcolor="<? echo $bgcolor; ?>">
                                        <td width="40"><? echo $b_sl ;?></td>
                                        <td width="130"><? echo $value ;?></td>
                                        <td width="100" align="right"><? echo number_format($budget_qnty_data_array[$key],2); $budget_qnty_data_array_tot+=$budget_qnty_data_array[$key]; ?></td>
                                        <td width="100" align="right"><? echo number_format($booking_qnty_array[$key],2); $booking_qnty_array_tot+=$booking_qnty_array[$key]; ?></td>
                                        
                                        <td width="100" align="right"><? echo number_format($fin_fab_Requi_array[$key],2); $fin_fab_Requi_array_tot+=$fin_fab_Requi_array[$key]; ?></td>
                                        <td width="100" align="right"><? echo number_format($fin_fab_recei_array[$key],2); $fin_fab_recei_array_tot+=$fin_fab_recei_array[$key]; ?></td>
                                        <td width="100" align="right"><? echo number_format($fin_balance_array[$key],2); $fin_balance_array_tot+=$fin_balance_array[$key];?></td>
                                        <td align="right"><? echo number_format($issue_to_cut_array[$key],2);$issue_to_cut_tot+=$issue_to_cut_array[$key]; ?></td>
                                    </tr>
                                <?
                                $b_sl++;
                                }
                                ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th width="40" colspan="2" align="right">Total</th>
                                        <th width="100" align="right"><? echo number_format($budget_qnty_data_array_tot,2);?></th>
                                        <th width="100" align="right"><? echo number_format($booking_qnty_array_tot,2) ;?></th>
                                        
                                        
                                        <th width="100" align="right"><? echo number_format($fin_fab_Requi_array_tot,2) ;?></th>
                                        <th width="100" align="right"><? echo number_format($fin_fab_recei_array_tot,2) ;?></th>
                                        <th width="100" align="right"><? echo number_format($fin_balance_array_tot,2) ;?></th>
                                        <th width="" align="right"><? echo number_format($issue_to_cut_tot,2) ;?></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </td>
                    <?
                        $html.="</tbody>
                                <tfoot>
                                <tr>
                                <th colspan='2' align='right'>Total</th>
                                <th align='right'>".number_format($budget_qnty_data_array_tot,2)."</th>
                                <th align='right'>".number_format($booking_qnty_array_tot,2)."</th>
                                
                                
                                <th align='right'>".number_format($fin_fab_Requi_array_tot,2)."</th>
                                <th align='right'>".number_format($fin_fab_recei_array_tot,2)."</th>
                                <th align='right'>".number_format($fin_balance_array_tot,2)."</th>
                                <th align='right'>".number_format($issue_to_cut_tot,2)."</th>
                                </tr>
                                </tfoot>
                            </table>
                            </td>
                            <td width='90'></td>
                            <td>
                                <table border=1 rules='all'>
                                    <thead>
                                    <tr>
                                        <th colspan='3'>Summary in qnty and %</th>
                                    </tr>
                                    <tr bgcolor='#FFFFFF'>
                                        <th>Particulars</th>
                                        <th>Total Qnty</th>
                                        <th>% On Budget</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr bgcolor='#FFFFFF'>
                                        <td>Budget Qnty</td>
                                        <td align='right'>".number_format($budget_qnty_data_array_tot,2)."</td>
                                        <td align='right'>".number_format($budget_qnty_data_array_tot/$budget_qnty_data_array_tot*100,2)."</td>
                                    </tr>
                                    <tr bgcolor='#FFFFFF'>
                                        <td>Booking Qnty</td>
                                        <td align='right'>".number_format($booking_qnty_array_tot,2)."</td>
                                        <td align='right'>".number_format($booking_qnty_array_tot/$budget_qnty_data_array_tot*100,2)."%</td>
                                    </tr>
                                    
                                    <tr bgcolor='#FFFFFF'>
                                        <td>Receive Qnty</td>
                                        <td align='right'>".number_format($fin_fab_recei_array_tot,2)."</td>
                                        <td align='right'>".number_format($fin_fab_recei_array_tot/$budget_qnty_data_array_tot*100,2)."</td>
                                    </tr>
                                    <tr bgcolor='#FFFFFF'>
                                        <td>Issue Qnty</td>
                                        <td align='right'>".number_format($issue_to_cut_tot,2)."</td>
                                        <td align='right'>".number_format($issue_to_cut_tot/$budget_qnty_data_array_tot*100,2)."%</td>
                                    </tr>
                                    
                                    </tbody>
                                </table>
                            <td>
                            </tr>
                            </table>
                            ";
                        ?>
                    <td width="10"></td>
                    <td>
                     <div id="data_panel1" align="center" style="width:100%; height:18px">
                           
                        </div> 
                        <table width="600" class="rpt_table" border="1" rules="all">
                            <thead>
                            <tr align="center" id="company_id_td" style="visibility:hidden; border:none">
                                        <th colspan="3" style="border:none">
                                          
                                        </th>
                                    </tr>
                                    <tr align="center" id="date_td" style="visibility:hidden;border:none">
                                         <th colspan="3" style="border:none"></th>
                                    </tr>
                                <tr>
                                    <th colspan="3">Summary in qnty and %</th>
                                </tr>
                                
                                <tr bgcolor="<? echo "#FFFFFF"; ?>">
                                    <th width="300">Particulars</th>
                                    <th width="170">Total Qnty</th>
                                    <th>% On Budget</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr bgcolor="<? echo "#FFFFFF"; ?>">
                                   <td>Budget Qnty</td>
                                   <td align="right"><? echo number_format($budget_qnty_data_array_tot,2);?></td>
                                   <td align="right"> <? echo number_format($budget_qnty_data_array_tot/$budget_qnty_data_array_tot*100,2)."%"; ?> </td>
                                </tr>
                                <tr bgcolor="<? echo "#FFFFFF"; ?>">
                                   <td>Booking Qnty</td>
                                   <td align="right"> <? echo number_format($booking_qnty_array_tot,2); ?></td>
                                   <td align="right"><? echo number_format($booking_qnty_array_tot/$budget_qnty_data_array_tot*100,2)."%"; $booking_percent=number_format($booking_qnty_array_tot/$budget_qnty_data_array_tot*100,2); ?></td>
                                </tr>
                                
                                <tr bgcolor="<? echo "#FFFFFF"; ?>">
                                    <td>Receive Qnty</td>
                                   <td align="right"><? echo number_format($fin_fab_recei_array_tot,2); ?></td>
                                   <td align="right"><? echo number_format($fin_fab_recei_array_tot/$budget_qnty_data_array_tot*100,2)."%"; $fin_fab_percent=number_format($fin_fab_recei_array_tot/$budget_qnty_data_array_tot*100,2); ?></td>
                                </tr>
                                <tr bgcolor="<? echo "#FFFFFF"; ?>">
                                   <td>Issue Qnty</td>
                                   <td align="right"> <? echo number_format($issue_to_cut_tot,2); ?></td>
                                   <td align="right"><? echo number_format($issue_to_cut_tot/$budget_qnty_data_array_tot*100,2)."%"; $issue_percent=number_format($issue_to_cut_tot/$budget_qnty_data_array_tot*100,2); ?></td>
                                </tr>
                                
                            </tbody>
                       </table>
                    </td>
                    <td width="10"></td>
                    <td>
                     <div id="data_panel1" align="center" style="width:100%; height:18px">
                           
                        </div> 
                        <table width="400" border="0" style="display:none">
                            <thead>
                            <tr align="center" id="company_id_td" style="visibility:hidden; border:none">
                                        <th colspan="4" style="border:none">
                                          
                                        </th>
                                    </tr>
                                    <tr align="center" id="date_td" style="visibility:hidden;border:none">
                                         <th colspan="4" style="border:none"></th>
                                    </tr>
                                <tr>
                                    <th colspan="4">Bar Chart</th>
                                </tr>
                                
                                
                            </thead>
                            <tbody>
                              <tr bgcolor="<? echo "#FFFFFF"; ?>" >
                                   <td width="20"><img src="img.png"/></td>
                                   <td height="200" width="100" valign="bottom"><div title="<? echo "Budget Qnty: 100%";  ?>" style="width:100%; height:<? echo "100%";?>; background-color:#FF0;"></div></td>
                                   <td height="200" width="100" valign="bottom" title="<? echo "Booking Qnty: $booking_percent%";  ?>"><div title="<? echo "Booking Qnty: $booking_percent%";  ?>" style="width:100%; height:<? echo "$booking_percent%";?>; background-color:#FF0"></div></td>
                                   <td height="200" width="100" valign="bottom" title="<? echo "Receive Qnty: $fin_fab_percent%";  ?>"><div title="<? echo "Receive Qnty: $fin_fab_percent%";  ?>" style="width:100%; height:<? echo "$fin_fab_percent%";?>; background-color:#FF0"></div></td>
                                   <td height="200" width="100" valign="bottom" title="<? echo "Issue Qnty: $issue_percent%";  ?>"><div title="<? echo "Issue Qnty: $issue_percent%";  ?>" style="width:100%; height:<? echo "$issue_percent%";?>; background-color:#FF0"></div></td>
                                </tr>
                                <tr bgcolor="<? echo "#FFFFFF"; ?>">
                                <td width="20"></td>
                                   <td align="center">Budget Qnty</td>
                                   <td align="center">Booking Qnty</td>
                                   <td align="center">Receive Qnty</td>
                                   <td align="center">Issue Qnty</td>
                                </tr>
                                
                                
                            </tbody>
                       </table>
                    </td>
                </tr>
            </table>
        </fieldset>
	<?
	}
	

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
	echo "$total_data####$filename####$filename_short";
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
	<div style="width:850px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:845px; margin-left:10px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="810" cellpadding="0" cellspacing="0">
            	<thead>
					<th colspan="8"><b> Required Qnty Info</b></th>
				</thead>
				<thead>
                    <th width="40">SL</th>
                    <th width="120">Order No.</th>
                    <th width="120">Buyer Name</th>
                    <th width="90">Cons/Dzn</th>
                    <th width="110">Order Qnty</th>
                    <th width="110">Plan Cut Qnty</th>
                    <th width="110">Required Qnty</th>
                    <th>Shipment Date</th>
                </thead>
             </table>
             <div style="width:830px; max-height:320px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="810" cellpadding="0" cellspacing="0">
                    <?
                    $i=1; $tot_req_qnty=0;
					$sql="select a.buyer_name, a.job_no, a.total_set_qnty as ratio, b.po_number, b.po_quantity, b.pub_shipment_date, b.plan_cut, sum(c.cons_qnty) as qnty from wo_po_details_master a, wo_po_break_down b, wo_pre_cost_fab_yarn_cost_dtls c where a.job_no=b.job_no_mst and a.job_no=c.job_no and b.id in($order_id) and c.count_id='$yarn_count' and c.copm_one_id='$yarn_comp_type1st' and c.percent_one='$yarn_comp_percent1st' and c.copm_two_id='$yarn_comp_type2nd' and c.percent_two='$yarn_comp_percent2nd' and c.type_id='$yarn_type_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.id";
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
                        $tot_req_qnty+=$required_qnty;
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="40"><? echo $i; ?></td>
                            <td width="120"><p><? echo $row[csf('po_number')]; ?></p></td>
                            <td width="120"><p><? echo $buyer_short_name_library[$row[csf('buyer_name')]]; ?></p></td>
                            <td width="90" align="right"><p><? echo number_format($row[csf('qnty')],2); ?></p></td>
                            <td width="110" align="right"><p><? echo number_format($order_qnty,0); ?></p></td>
                            <td width="110" align="right"><p><? echo number_format($plan_cut_qnty,0); ?></p></td>
                            <td width="110" align="right"><p><? echo number_format($required_qnty,2); ?></p></td>
                            <td align="center"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
                        </tr>
                    <?
                    $i++;
                    }
                    ?>
                    <tfoot>
                        <th align="right" colspan="6">Total</th>
                        <th align="right"><? echo number_format($tot_req_qnty,2); ?></th>
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
				$sql="select a.issue_number, a.issue_date, a.challan_no, a.knit_dye_source, a.knit_dye_company, a.booking_no, sum(b.quantity) as issue_qnty, c.lot, c.yarn_type, b.id as prod_id, c.product_name_details, d.brand_id from inv_issue_master a, order_wise_pro_details b, product_details_master c, inv_transaction d where a.id=d.mst_id and d.transaction_type=2 and d.item_category=1 and d.id=b.trans_id and b.trans_type=2 and b.entry_form=3 and b.po_breakdown_id in ($order_id) and b.prod_id=c.id and c.yarn_count_id='$yarn_count' and c.yarn_comp_type1st='$yarn_comp_type1st' and c.yarn_comp_percent1st='$yarn_comp_percent1st' and c.yarn_comp_type2nd='$yarn_comp_type2nd' and c.yarn_comp_percent2nd='$yarn_comp_percent2nd' and c.yarn_type='$yarn_type_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id, b.prod_id";
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
                        <td width="105"><p><? echo $row[csf('booking_no')];?></p></td>
                        <td width="80"><p><? echo $row[csf('challan_no')]; ?></p></td>
                        <td width="70"><p><? $brand=return_field_value("brand_name","lib_brand","id='$row[brand_id]'"); echo $brand; ?></p></td>
                        <td width="60"><p><? echo $row[csf('lot')]; ?></p></td>
                        <td width="75" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
                        <td width="80"><p><? echo $yarn_type[$row[csf('yarn_type')]]; ?></p></td>
                        <td align="right" width="90">
							<? 
								if($row[csf('knit_dye_source')]==1)
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
                $sql="select a.recv_number, a.receive_date, a.challan_no, a.knitting_source, a.knitting_company, a.booking_no, sum(b.quantity) as returned_qnty, c.lot, c.yarn_type, b.id as prod_id, c.product_name_details, d.brand_id from inv_receive_master a, order_wise_pro_details b, product_details_master c, inv_transaction d where a.id=d.mst_id and d.transaction_type=4 and d.item_category=1 and d.id=b.trans_id and b.trans_type=4 and b.entry_form=9 and b.po_breakdown_id in ($order_id) and b.prod_id=c.id and c.yarn_count_id='$yarn_count' and c.yarn_comp_type1st='$yarn_comp_type1st' and c.yarn_comp_percent1st='$yarn_comp_percent1st' and c.yarn_comp_type2nd='$yarn_comp_type2nd' and c.yarn_comp_percent2nd='$yarn_comp_percent2nd' and c.yarn_type='$yarn_type_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id, b.prod_id";
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
                        <td width="105"><p><? echo $row[csf('booking_no')];?></p></td>
                        <td width="80"><p><? echo $row[csf('challan_no')]; ?></p></td>
                        <td width="70"><p><? $brand=return_field_value("brand_name","lib_brand","id='$row[brand_id]'"); echo $brand; ?></p></td>
                        <td width="60"><p><? echo $row[csf('lot')]; ?></p></td>
                        <td width="75" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                        <td width="80"><p><? echo $yarn_type[$row[csf('yarn_type')]]; ?></p></td>
                        <td align="right" width="90">
							<? 
								if($row[csf('knitting_source')]==1)
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
	
	$yarn_desc_array=explode(",",$yarn_count);

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
				$i=1; $total_yarn_issue_qnty=0; $total_yarn_issue_qnty_out=0; $yarn_desc_array_for_return=array();
				$sql_yarn_iss="select b.yarn_count_id, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type from order_wise_pro_details a, product_details_master b where a.prod_id=b.id and a.po_breakdown_id in ($order_id) and a.entry_form in(3,9) and b.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 group by b.yarn_count_id, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type";
				$dataArrayIssue=sql_select($sql_yarn_iss);
				foreach($dataArrayIssue as $row_yarn_iss)
				{
					if($row_yarn_iss['yarn_comp_percent2nd']!=0)
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
						$sql="select a.issue_number, a.issue_date, a.challan_no, a.knit_dye_source, a.knit_dye_company, a.booking_no, sum(b.quantity) as issue_qnty, c.lot, c.yarn_type, b.id as prod_id, c.product_name_details, d.brand_id from inv_issue_master a, order_wise_pro_details b, product_details_master c, inv_transaction d where a.id=d.mst_id and d.transaction_type=2 and d.item_category=1 and d.id=b.trans_id and b.trans_type=2 and b.entry_form=3 and b.po_breakdown_id in ($order_id) and b.prod_id=c.id and c.yarn_count_id='$row_yarn_iss[yarn_count_id]' and c.yarn_comp_type1st='$row_yarn_iss[yarn_comp_type1st]' and c.yarn_comp_percent1st='$row_yarn_iss[yarn_comp_percent1st]' and c.yarn_comp_type2nd='$row_yarn_iss[yarn_comp_type2nd]' and c.yarn_comp_percent2nd='$row_yarn_iss[yarn_comp_percent2nd]' and c.yarn_type='$row_yarn_iss[yarn_type]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id, b.prod_id";
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
								<td width="105"><p><? echo $row[csf('booking_no')];?></p></td>
								<td width="80"><p><? echo $row[csf('challan_no')]; ?></p></td>
								<td width="70"><p><? $brand=return_field_value("brand_name","lib_brand","id='$row[brand_id]'"); echo $brand; ?></p></td>
								<td width="60"><p><? echo $row[csf('lot')]; ?></p></td>
								<td width="75" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
								<td width="80"><p><? echo $yarn_type[$row[csf('yarn_type')]]; ?></p></td>
								<td align="right" width="90">
									<? 
										if($row[csf('knit_dye_source')]==1)
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
					}
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
				foreach($yarn_desc_array_for_return as $key=>$value)
				{
					if(!array_key_exists($key,$yarn_desc_array))
					{
						$desc=explode("__",$value);
						$yarn_count=$desc[0];
						$yarn_comp_type1st=$desc[1];
						$yarn_comp_percent1st=$desc[2];
						$yarn_comp_type2nd=$desc[3];
						$yarn_comp_percent2nd=$desc[4];
						$yarn_type_id=$desc[5];
						
						$sql="select a.recv_number, a.receive_date, a.challan_no, a.knitting_source, a.knitting_company, a.booking_no, sum(b.quantity) as returned_qnty, c.lot, c.yarn_type, b.id as prod_id, c.product_name_details, d.brand_id from inv_receive_master a, order_wise_pro_details b, product_details_master c, inv_transaction d where a.id=d.mst_id and d.transaction_type=4 and d.item_category=1 and d.id=b.trans_id and b.trans_type=4 and b.entry_form=9 and b.po_breakdown_id in ($order_id) and b.prod_id=c.id and c.yarn_count_id='$yarn_count' and c.yarn_comp_type1st='$yarn_comp_type1st' and c.yarn_comp_percent1st='$yarn_comp_percent1st' and c.yarn_comp_type2nd='$yarn_comp_type2nd' and c.yarn_comp_percent2nd='$yarn_comp_percent2nd' and c.yarn_type='$yarn_type_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id, b.prod_id";
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
								<td width="105"><p><? echo $row[csf('booking_no')];?></p></td>
								<td width="80"><p><? echo $row[csf('challan_no')]; ?></p></td>
								<td width="70"><p><? $brand=return_field_value("brand_name","lib_brand","id='$row[brand_id]'"); echo $brand; ?></p></td>
								<td width="60"><p><? echo $row[csf('lot')]; ?></p></td>
								<td width="75" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
								<td width="80"><p><? echo $yarn_type[$row[csf('yarn_type')]]; ?></p></td>
								<td align="right" width="90">
									<? 
										if($row[csf('knitting_source')]==1)
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
					}
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

if($action=="grey_receive")
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
	<div style="width:675px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:670px; margin-left:7px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="650" cellpadding="0" cellspacing="0">
				<thead>
					<th colspan="6"><b>Grey Receive Info </b></th>
				</thead>
				<thead>
                	<th width="40">SL</th>
                    <th width="130">Receive Id</th>
                    <th width="110">Receive Basis</th>
                    <th width="130">Booking No</th>
                    <th width="90">Receive Date</th>
                    <th>Receive Qnty</th>
				</thead>
             </table>
             <div style="width:667px; max-height:320px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="650" cellpadding="0" cellspacing="0">
                    <?
                    $i=1; $total_receive_qnty=0;
                    $sql="select a.recv_number, a.receive_date, a.receive_basis, a.booking_no, sum(c.quantity) as quantity from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and c.po_breakdown_id in($order_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.id";
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
                            <td width="40"><? echo $i; ?></td>
                            <td width="130"><p><? echo $row[csf('recv_number')]; ?></p></td>
                            <td width="110"><p><? echo $receive_basis[$row[csf('receive_basis')]]; ?></p></td>
                            <td width="130"><p><? echo $row[csf('booking_no')]; ?></p></td>
                            <td width="90" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                            <td align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
                        </tr>
                    <?
                    $i++;
                    }
                    ?>
                    <tfoot>
                        <th colspan="5" align="right">Total</th>
                        <th align="right"><? echo number_format($total_receive_qnty,2); ?></th>
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
                    $sql="select a.batch_no, a.batch_date, sum(b.batch_qnty) as quantity, a.color_id from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and b.po_id in($order_id) and a.status_active=1 and a.batch_against<>2 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id";
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
                    $i=1; $issue_to='';
                    $sql="select a.issue_number, a.issue_date, a.issue_purpose, a.knit_dye_source, a.knit_dye_company, a.booking_no, a.batch_no, sum(c.quantity) as quantity from inv_issue_master a, inv_grey_fabric_issue_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=16 and c.entry_form=16 and c.po_breakdown_id in($order_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id";
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
                            <td width="115"><? echo $row[csf('booking_no')]; ?></td>
                            <td width="90"><p><? echo $batch_details[$row[csf('batch_no')]]; ?></p></td>
                            <td width="80" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
                            <td width="100" align="right">
								<?
                                    if($row[csf('knit_dye_source')]==1)
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
	
	$machine_arr = return_library_array("select id, concat(machine_no,'-',brand) as machine_name from lib_machine_name","id","machine_name");
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
                    <th width="120">System Id</th>
                    <th width="75">Rec. Date</th>
                    <th width="80">Rec. Basis</th>
                    <th width="90">Batch No</th>
                    <th width="90">Dyeing Source</th>
                    <th width="100">Dyeing Company</th>
                    <th width="90">Receive Qnty</th>
                    <th width="110">Fabric Desc.</th>
                    <th>Machine Name</th>
				</thead>
             </table>
             <div style="width:877px; max-height:320px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="860" cellpadding="0" cellspacing="0">
                    <?
                    $i=1; $total_dye_qnty=0; $dye_company='';
                    $sql="select a.dye_system_id, a.recieve_basis, a.received_date, a.dyeing_source, a.dyeing_company, b.batch_id, b.machine_name, concat_ws(', ',b.febric_description,b.gsm,b.dia_width) as febric_description, sum(c.quantity) as quantity from pro_dyeing_update_mst a, pro_dyeing_update_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and c.entry_form=6 and c.po_breakdown_id in($order_id) and c.color_id='$color' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.id";
                    $result=sql_select($sql);
        			foreach($result as $row)
                    {
                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";	
                    
                        if($row[csf('dyeing_source')]==1) 
                        {
                            $dye_company=$company_library[$row[csf('dyeing_company')]]; 
                        }
                        else if($row['dyeing_source']==3) 
                        {
                            $dye_company=$supplier_details[$row[csf('dyeing_company')]];
                        }
                        else
                            $dye_company="&nbsp;";
                    
                        $total_dye_qnty+=$row[csf('quantity')];
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="30"><? echo $i; ?></td>
                            <td width="120"><p><? echo $row[csf('dye_system_id')]; ?></p></td>
                            <td width="75" align="center"><? echo change_date_format($row[csf('received_date')]); ?></td>
                            <td width="80"><? echo $receive_basis_arr[$row[csf('recieve_basis')]]; ?></td>
                            <td width="90"><p><? echo $batch_details[$row[csf('batch_id')]]; ?></p></td>
                            <td width="90"><? echo $knitting_source[$row[csf('dyeing_source')]]; ?></td>
                            <td width="100"><p><? echo $dye_company; ?></p></td>
                            <td width="90" align="right"><? echo number_format($row[csf('quantity')],2); ?></td>
                            <td width="110"><p><? echo $row[csf('febric_description')]; ?></p></td>
                            <td><p><? echo $machine_arr[$row[csf('machine_name')]]; ?></p></td>
                        </tr>
                    <?
                    $i++;
                    }
                    ?>
                    <tfoot>
                        <th colspan="7" align="right">Total</th>
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
                    <th width="90">Batch/Lot</th>
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
                     $sql="select a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, a.knitting_company,b.batch_lot,  b.prod_id, sum(c.quantity) as quantity from inv_receive_master a, inv_transaction b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.trans_id and a.entry_form=17 and c.entry_form=17 and c.po_breakdown_id in($order_id) and c.color_id='$color' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.id,a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, a.knitting_company,b.batch_lot, b.prod_id";
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
                            <td width="90"><p><? echo $row[csf('batch_lot')];//$batch_details[$row[csf('batch_id')]]; ?></p></td>
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
	<div style="width:675px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:670px; margin-left:7px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="650" cellpadding="0" cellspacing="0">
				<thead>
					<th colspan="6"><b>Issue To Cutting Info</b></th>
				</thead>
				<thead>
                	<th width="50">SL</th>
                    <th width="120">System Id</th>
                    <th width="80">Issue Date</th>
                    <th width="120">Batch /Lot</th>
                    <th width="110">Issue Qnty</th>
                    <th>Fabric Description</th>
				</thead>
             </table>
             <div style="width:667px; max-height:320px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="650" cellpadding="0" cellspacing="0">
                    <?
                    $i=1; $total_issue_to_cut_qnty=0;
                    $sql="select a.issue_number, a.issue_date, b.batch_lot, b.prod_id, sum(c.quantity) as quantity from inv_issue_master a, inv_wvn_finish_fab_iss_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=19 and c.entry_form=19 and c.po_breakdown_id in($order_id) and c.color_id='$color' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.id,a.issue_number, a.issue_date, b.batch_lot, b.prod_id";
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
                            <td width="80" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
                            <td width="120"><p><? echo $row[csf('batch_lot')]//$batch_details[$row[csf('batch_id')]]; ?></p></td>
                            <td width="110" align="right"><? echo number_format($row[csf('quantity')],2); ?></td>
                            <td><p><? echo $product_details[$row[csf('prod_id')]]; ?></p></td>
                        </tr>
                    <?
                    $i++;
                    }
                    ?>
                    <tfoot>
                        <th colspan="4" align="right">Total</th>
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

?>