<?
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$_SESSION['page_permission']=$permission;
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );

if ($action == "load_drop_down_knitting_com") 
{
	$data = explode("_", $data);
	$company_id = $data[1];
	if ($data[0] == 1) {
		echo create_drop_down("cbo_knitting_company", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "--Select Knit Company--", "", "", "");
	} else if ($data[0] == 3) {
		echo create_drop_down("cbo_knitting_company", 120, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=20 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id,supplier_name", 1, "--Select Knit Company--", 0, "");
	} else {
		echo create_drop_down("cbo_knitting_company", 120, $blank_array, "", 1, "--Select Knit Company--", 0, "");
	}
	exit();
}

if ($action=="load_drop_down_buyer")
{
	if($data!=0)
	{
	echo create_drop_down( "cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" ,0); 
	}
	else
	{
		echo create_drop_down( "cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy where buy.status_active =1 and buy.is_deleted=0  $buyer_cond order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "",0,"" );
	}
	exit();
}

if($action=="report_generate")
{	
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	 
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$cbo_knitting_source=str_replace("'","",$cbo_knitting_source);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_knitting_company=str_replace("'","",$cbo_knitting_company);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$txt_booking_no=str_replace("'","",$txt_booking_no);
	$txt_job_no=str_replace("'","",$txt_job_no);
	$txt_program_no=str_replace("'","",$txt_program_no);
	
	if($cbo_knitting_source>0){$knitting_source_cond="and a.knitting_source=$cbo_knitting_source";}else{$knitting_source_cond="";}
	if($cbo_buyer_name>0){$buyer_name_cond="and a.buyer_id =$cbo_buyer_name";}else{$buyer_name_cond="";}
	if($cbo_company_name>0){$company_name_cond="and b.company_id =$cbo_company_name";}else{$company_name_cond="";}

	/*if($txt_job_no!="")
	{
		$jobs=trim($txt_job_no);
		 $txt_jobNo="'".$jobs."'";
		 // change this query
		$po_id_arr = return_library_array("select job_no_mst, id from wo_po_break_down where job_no_mst=$txt_jobNo", 'job_no_mst', 'id');
		
		
		$sqls=sql_select("select job_no_mst, id from wo_po_break_down where job_no_mst=$txt_jobNo");
		$orderID="";
		foreach($sqls as $k=>$val)
		{
			$j=$val[csf("job_no_mst")];
			$job="'".$j."'";
			$orderID=$po_id_arr[$job]=$val[csf("id")];
		}
		
		$job_no_cond="and c.po_breakdown_id="."'".$orderID."'";
		//$job_no_cond=$orderID;
	}
	else
	{
		  $job_no_cond="";
	}*/

	if($txt_date_from!="" && $txt_date_to!="")
	{
		$str_cond_date="and a.program_date between '$txt_date_from' and  '$txt_date_to ' ";
	}
	else
	{
		$str_cond_date="";
	}
	if($cbo_knitting_company>0)
	{
		 $knit_comp_cond="and a.knitting_party='$cbo_knitting_company' ";
	}
	else
	{
		 $knit_comp_cond="";
	}
	
	if($txt_program_no!="")
	{
		$program_cond="and a.id='$txt_program_no'";
	}
	else
	{
		$program_cond="";
	}
	
	if($txt_booking_no!="")
	{
		$booking_cond=" and b.booking_no like '%$txt_booking_no%'";
	}
	else
	{
		$booking_cond="";
	}
	
	ob_start();
	// php start
	?>
    	<style type="text/css">
            .block_div { 
				width:auto;
				height:auto;
				text-wrap:normal;
				vertical-align:bottom;
				display: block;
				position: !important; 
				-webkit-transform: rotate(-90deg);
				-moz-transform: rotate(-90deg);					
            }
    	</style>
	<?
	// php end
	if($report_format==2) // Program Wise Button
	{
		?>
		<fieldset style="width:1400px;">
			<table width="1400">
				<tr>
					<td align="center" width="100%" colspan="12" class="form_caption" style="font-size:18px;">Booking And Plan Wise Fabric Status Report</td>
				</tr>
				<tr>
					<td align="center" width="100%" colspan="12" class="form_caption"><? echo $company_library[str_replace("'","",$cbo_company_name)]; ?></td>
				</tr>
			</table>
			<?
				$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
				$party_library = return_library_array("select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=20 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id", "supplier_name");
				$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
				$style_library = return_library_array("select job_no, style_ref_no from wo_po_details_master", "job_no", "style_ref_no");
				$construction_arr = return_library_array("select id, construction from lib_yarn_count_determina_mst", "id", "construction");
				$store_array=return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');
				$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
			?>
			<style>
				.breakAll{
					word-break:break-all;
					word-wrap: break-word;
				}
				.inline { 
				    display: inline-block; 
				}
			</style>

			<!-- Program  Info and Color Type Summery Start -->
			<div> 
				<!-- Program  Info Start -->
				<div class='inline' style="width: 990px; float:left;"">
			    	<div style="width: 990px;">
			    		<!-- Program  Info Start-->			    					  	
						<?
						$program_info_sql="SELECT a.id as program_no, a.knitting_source, a.knitting_party, a.color_id, b.booking_no, a.program_qnty, b.determination_id, b.body_part_id, b.color_type_id, b.yarn_desc as pre_cost_dtls_id, b.buyer_id 
						from ppl_planning_info_entry_dtls a, ppl_planning_entry_plan_dtls b 
						where a.mst_id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $str_cond_date $knit_comp_cond $program_cond $booking_cond
						group by a.id, a.knitting_source, a.knitting_party, a.color_id, b.booking_no, b.determination_id, b.body_part_id, b.color_type_id, b.yarn_desc, b.buyer_id, a.program_qnty";
						// echo $program_info_sql;die;
						$program_info_result=sql_select($program_info_sql);
						$program_arr=array(); $booking_no_arr=array(); $plan_qty_inhouse_arr=array();
						$plan_qty_outside_arr=array();$program_qty_arr=array();
						foreach ($program_info_result as $key => $value) 
						{
							$program_arr[$value[csf('program_no')]]=$value[csf('program_no')];
							$program_qty_arr[$value[csf('program_no')]]=$value[csf('program_qnty')];
							$booking_no_arr[$value[csf('booking_no')]]=$value[csf('booking_no')];
							if ($value[csf('knitting_source')]==1) 
							{
								$plan_qty_inhouse_arr[$value[csf('program_no')]]['plan_qty_inhouse']=$value[csf('program_qnty')];
								$company_party=$company_library[$row[csf("knitting_party")]];
							}
							else
							{
								$plan_qty_outside_arr[$value[csf('program_no')]]['plan_qty_outside']=$value[csf('program_qnty')];
								$company_party=$row[csf("knitting_party")];
							}
						}				
						// echo "<pre>";print_r($program_qty_arr);die;
						$booking_no_cond=implode(",", array_unique($booking_no_arr));
						if ($booking_no_arr!="") 
						{
							$booking_info_sql="SELECT b.booking_no, b.color_type, b.fabric_color_id, b.pre_cost_fabric_cost_dtls_id as pre_cost_dtls_id, b.process_loss_percent, b.grey_fab_qnty as booking_qty, b.fin_fab_qnty, b.po_break_down_id, b.job_no, b.construction
							from wo_booking_dtls b where b.booking_no='$booking_no_cond' and b.status_active=1 and b.is_deleted=0";
							//echo $booking_info_sql;
							$booking_info_result=sql_select($booking_info_sql);
							$process_loss_arr=array();$booking_qty_arr=array();$job_no_arr=array();
							$color_type_summary_arr=array();
							foreach ($booking_info_result as $key => $value) 
							{
								$process_loss_arr[$value[csf('booking_no')]][$value[csf('color_type')]][$value[csf('fabric_color_id')]][$value[csf('pre_cost_dtls_id')]]=$value[csf('process_loss_percent')];

								$booking_qty_arr[$value[csf('booking_no')]][$value[csf('fabric_color_id')]]+=$value[csf('booking_qty')];
								$job_no_arr[$value[csf('booking_no')]]=$value[csf('job_no')];

								
								$color_type_summary_arr[$value[csf('booking_no')]][$value[csf('color_type')]][$value[csf('construction')]]['construction']=$value[csf('construction')];
								$color_type_summary_arr[$value[csf('booking_no')]][$value[csf('color_type')]][$value[csf('construction')]]['booking_qty']+=$value[csf('booking_qty')];
								$color_type_summary_arr[$value[csf('booking_no')]][$value[csf('color_type')]][$value[csf('construction')]]['fin_fab_qnty']+=$value[csf('fin_fab_qnty')];
							}

							//echo "<pre>";print_r($process_loss_arr);die;
							// echo "<pre>";print_r($booking_qty_arr);die;
						}
						?>
						<table width="990" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" style="float:left;">
							<thead>
								<tr>
									<th colspan="11">Program  Info</th>
								</tr>
								<tr>
									<th width="90">Buyer</th>
									<th width="80">Job No</th>
									<th width="80">Style</th>
									<th width="100">Booking No</th>
									<th width="70">Booking Qty</th>
									<th width="70">Prog Grey Qty</th>
									<th width="70">Prog Fin Qty</th>
									<th width="100">Prog Company</th>
									<th width="70">Program No</th>
									<th width="100">Color</th>
									<th>Fabric</th>
								</tr>
							</thead>
						</table>
						<div style="width:995px; float:left; max-height:300px; overflow-y:scroll" id="scroll_body">
						<table width="977" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_body_show" style="float:left;">
							<tbody>
							<?
							$i=1;
							foreach($program_info_result as $row)
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

								$process_loss=$process_loss_arr[$row[csf('booking_no')]][$row[csf('color_type_id')]][$row[csf('color_id')]][$row[csf('pre_cost_dtls_id')]];
								$booking_qty=$booking_qty_arr[$row[csf('booking_no')]][$row[csf('color_id')]];
								$job_no=$job_no_arr[$row[csf('booking_no')]];
								?>						
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>');" id="tr_1nd<? echo $i; ?>">
									<td width="90"><p><? echo $buyer_library[$row[csf("buyer_id")]]; ?></p></td>
									<td width="80" align="center"><p><? echo $job_no; ?></p></td>
									<td width="80" align="center"><p><? echo $style_library[$job_no]; ?></p></td>
									<td width="100" align="center" ><p><? echo $row[csf("booking_no")]; ?></p></td>
									<td width="70" align="right" title="Booking qty, Booking No:<? echo $row[csf('booking_no')].',Color:'.$row[csf('color_id')]; ?>"><p><? echo number_format($booking_qty,2); ?></p></td> 
									<td width="70" align="right" title="Praogram qty"><p><? echo number_format($row[csf("program_qnty")],2);
									?></p></td>
									<td width="70" align="right" title="Grey Qty-(Grey Qty*process_loss)/100"><p><? 
									echo number_format($row[csf("program_qnty")]-($row[csf("program_qnty")]*$process_loss)/100,2); ?></p></td>
									<td width="100"><p><? echo $company_party=($row[csf('knitting_source')]==1) ? $company_library[$row[csf("knitting_party")]] : $party_library[$row[csf("knitting_party")]]; ?></p></td>
									<td width="70" align="center"><p><? echo $row[csf("program_no")]; ?></p></td>
									<td width="100" align="center"><p><? echo $row[csf("color_id")]; ?></p></td>
									<td class="breakAll"><p><? echo $construction_arr[$row[csf("determination_id")]]; ?></p></td>
								</tr>
								<?
								$total_program_qnty+=$row[csf("program_qnty")];
								$i++;
							}
							?>
							</tbody>
						</table>
						</div>						
						<!-- Program  Info End-->
			    	</div>
				</div>
			    <!-- Program  Info End -->

				<!-- Summary Start -->
			    <div class='inline' style="width: 330px; float: left; margin-left: 20px;">
			    	<div style="width: 330px;">
			    		<!-- Summary Start-->
						<table width="330" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" style="float:left;">
							<thead>
								<tr>
									<th colspan="4">Color Type Summery (Booking)</th>
								</tr>
								<tr>
									<th width="90">Color Type</th>
									<th width="80">Fabrication</th>
									<th width="80">Grey Qty</th>
									<th>Finish Qty</th>
								</tr>
							</thead>
						</table>

						<div style="width:335px; float:left; max-height:300px; overflow-y:scroll" id="scroll_body_summary">
						<table width="317" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_body_show" style="float:left;">
							<tbody>
							<?
							$i=1;
							foreach($color_type_summary_arr as $booking_no => $booking_no_arr)
							{
								foreach ($booking_no_arr as $color_type_id => $color_type_val) 
								{
									foreach ($color_type_val as $construction_ids => $row) 
									{
										if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
										?>						
										<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>');" id="tr_2nd<? echo $i; ?>">
											<td width="90"><p><? echo $color_type[$color_type_id]; ?></p></td>
											<td width="80" align="center"><p><? echo $row['construction']; ?></p></td>
											<td width="80" align="right"><p><? echo number_format($row['booking_qty'],2); ?></p></td>
											<td class="breakAll" align="right"><p><? echo number_format($row['fin_fab_qnty'],2); ?></p></td>
										</tr>
										<?
										$total_booking_qty+=$row["booking_qty"];
										$total_fin_fab_qnty+=$row["fin_fab_qnty"];
										$i++;
									}									
								}								
							}
							?>
							</tbody>
						</table>
						</div>
						<table width="317" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="report_table_footer" align="left">
							<tfoot>
								<tr style="background-color:#CCCCCC;">
									<th width="90"></th>
									<th width="80" align="right">Total</th>
									<th width="80" align="right"><? echo number_format($total_fin_fab_qnty,2); ?></th>
									<th align="right"><? echo number_format($total_booking_qty,2); ?></th>
								</tr>
							</tfoot>
						</table>
						<!-- Summary End-->
			    	</div>
			    </div>
			    <!-- Summary End -->
			</div>
			<!-- Program  Info and Color Type Summery End -->
			
			<div style="width:995px; float:left;">
				<!-- Yarn Info Start-->
				<?
				$all_program_no_arr = array_filter($program_arr);
				if(count($all_program_no_arr) > 0)
				{
					$all_program_no_cond_id = implode(",", $all_program_no_arr);
					$programCond = $program_no_cond = "";
					if($db_type==2 && count($all_program_no_arr)>999)
					{
						/*$all_program_no_cond_id_chunk=array_chunk($all_program_no_arr,999) ;
						foreach($all_program_no_cond_id_chunk as $chunk_prog)
						{
							$chunk_prog_val=implode(",",$chunk_prog);
							$programCond.=" a.dtls_id in($chunk_prog_val) or ";
						}
						$program_no_cond.=" and (".chop($programCond,'or ').")";*/

						$all_program_no_cond_id_chunk=array_chunk($all_program_no_arr, 999);
						foreach($all_program_no_cond_id_chunk as $chunk_prog)
						{
							$chunk_prog_val=implode(",", $chunk_prog);
							if(!$program_no_cond)$program_no_cond.=" and ( c.barcode_no in($chunk_prog_val) ";
							else $program_no_cond.=" or  c.barcode_no in($chunk_prog_val) ";
						}
						$program_no_cond.=")";
					}
					else
					{
						$program_no_cond=" and a.dtls_id in($all_program_no_cond_id)";
					}
					
					$yarn_info_sql="SELECT sum(b.yarn_qnty) as requisition_qty, b.knit_id as program_no, b.requisition_no, d.knit_dye_source, sum(c.cons_quantity) as issue_qty, a.determination_id, d.id 
					from ppl_planning_entry_plan_dtls a, ppl_yarn_requisition_entry b, inv_transaction c, inv_issue_master d
					where b.requisition_no=c.requisition_no and c.transaction_type=2 and a.dtls_id=b.knit_id and b.prod_id=c.prod_id and c.mst_id=d.id and c.receive_basis=3 and d.issue_basis=3 and d.item_category=1 and d.entry_form=3 $program_no_cond and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 
					group by b.knit_id, b.requisition_no, d.knit_dye_source, a.determination_id, d.id";
					// echo $yarn_info_sql;
					$yarn_info_result=sql_select($yarn_info_sql);
					$yarn_info_data_arr=array();$issue_id_arr=array();
					foreach ($yarn_info_result as $key => $value) 
					{
						$yarn_info_data_arr[$value[csf("determination_id")]]["determination_id"]=$value[csf("determination_id")];
						$yarn_info_data_arr[$value[csf("determination_id")]]["id"]=$value[csf("id")];
						$yarn_info_data_arr[$value[csf("determination_id")]]["knit_dye_source"]=$value[csf("knit_dye_source")];
						$yarn_info_data_arr[$value[csf("determination_id")]]["program_no"]=$value[csf("program_no")];
						$yarn_info_data_arr[$value[csf("determination_id")]]["issue_qty"]+=$value[csf("issue_qty")];
						$yarn_info_data_arr[$value[csf("determination_id")]]["requisition_qty"]+=$value[csf("requisition_qty")];
						$issue_id_arr[$value[csf("id")]]=$value[csf("id")];
					}
					$all_issue_id_arr = array_filter($issue_id_arr);
					if(count($all_issue_id_arr) > 0)
					{
						$all_issue_ids = implode(",", $all_issue_id_arr);
						$issueIdCond = $issue_ids_cond = "";
						if($db_type==2 && count($all_issue_id_arr)>999)
						{
							$all_issue_id_cond_chunk=array_chunk($all_issue_id_arr, 999);
							foreach($all_issue_id_cond_chunk as $chunk_prog)
							{
								$chunk_prog_val=implode(",", $chunk_prog);
								if(!$issue_ids_cond)$issue_ids_cond.=" and ( a.issue_id in($chunk_prog_val) ";
								else $issue_ids_cond.=" or  a.issue_id in($chunk_prog_val) ";
							}
							$issue_ids_cond.=")";
						}
						else
						{
							$issue_ids_cond=" and a.issue_id in($all_issue_ids)";
						}

						$yarn_issue_rtn_sql="SELECT a.issue_id, sum(b.cons_quantity) as return_qty, a.booking_id from inv_receive_master a, inv_transaction b
						where a.id=b.mst_id and a.entry_form=9 and b.transaction_type=4 $issue_ids_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.issue_id, a.booking_id";
						$yarn_issue_rtn_result=sql_select($yarn_issue_rtn_sql);
						$yarn_issue_retn_arr=array();
						foreach ($yarn_issue_rtn_result as $key => $row) 
						{
							$yarn_issue_retn_arr[$row[csf("issue_id")]]+=$row[csf("return_qty")];
						}
						// echo "<pre>";print_r($yarn_issue_retn_arr);die;
					}
				}
				
				?>
				
				<table width="840" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" style="float:left; margin-top: 10px;">
					<thead>
						<tr>
							<th colspan="11">Yarn Info</th>
						</tr>
						<tr>
							<th width="100">Construction</th>
							<th width="80">Plan Qty Inhouse</th>
							<th width="80">Plan Qty Outside</th>
							<th width="80">Total Plan Qty</th>
							<th width="80">Requisition Qty</th>
							<th width="80">Yarn Issued Inside</th>
							<th width="80">Yarn Issued Outside</th>
							<th width="80">Total Yarn Issued</th>
							<th width="80">Yarn issue Returned</th>
							<th>Net Issue</th>
						</tr>
					</thead>
				</table>
				
				<div style="width:845px; float:left; max-height:300px; overflow-y:scroll" id="scroll_body2">
				<table width="827" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_body_show2" style="float:left;">
					<tbody>
					<?
					$i=1;
					$total_requisition_qty=$total_yarn_issued_inside=$total_yarn_issued_outside=$total_yarn_issued=$total_yarn_issue_retn_qty=$total_net_issue=0;
					foreach ($yarn_info_data_arr as $construction_key => $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";		
						$plan_qty_inhouse=$plan_qty_inhouse_arr[$row["program_no"]]['plan_qty_inhouse'];			
						$plan_qty_outside=$plan_qty_outside_arr[$row["program_no"]]['plan_qty_outside'];
						if($row["knit_dye_source"]==1)
						{
							$yarn_issued_inside=$row["issue_qty"]; 
						}
						else
						{ 
							$yarn_issued_outside=$row["issue_qty"]; 
						}
						$yarn_issue_retn_qty=$yarn_issue_retn_arr[$row["id"]];
						?>						
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_3nd<? echo $i; ?>','<? echo $bgcolor; ?>');" id="tr_3nd<? echo $i; ?>">
							<td width="100" title="<? echo $construction_key; ?>"><? echo $construction_arr[$construction_key]; ?></td>
							<td width="80" align="right"><p><? echo number_format($plan_qty_inhouse,2); ?></p></td>
							<td width="80" align="right" ><p><? echo number_format($plan_qty_outside,2); ?></p></td>
							<td width="80" align="right"><p><? echo number_format($plan_qty_inhouse+$plan_qty_outside,2); ?></p></td> 
							<td width="80" align="right" ><p><? echo number_format($row["requisition_qty"],2); ?></p></td>
							<td width="80" align="right"><p><? echo number_format($yarn_issued_inside,2); ?></p></td>
							<td width="80" align="right"><p><? echo number_format($yarn_issued_outside,2); ?></p></td>
							<td width="80" align="right"><p><? echo number_format($yarn_issued_inside+$yarn_issued_outside,2); ?></p></td>
							<td width="80" align="right"><p><? echo number_format($yarn_issue_retn_qty,2); ?></p></td>
							<td class="breakAll" align="right"><p><? 
							$net_issue=($yarn_issued_inside+$yarn_issued_outside)-$yarn_issue_retn_qty;
							echo number_format($net_issue,2); ?></p></td>
						</tr>
						<?						
						$total_requisition_qty+=$row["requisition_qty"];
						$total_yarn_issued_inside+=$yarn_issued_inside;
						$total_yarn_issued_outside+=$yarn_issued_outside;
						$total_yarn_issued+=$yarn_issued_inside+$yarn_issued_outside;
						$total_yarn_issue_retn_qty+=$yarn_issue_retn_qty;
						$total_net_issue+=$net_issue;
						$i++;
					}
					?>
					</tbody>
				</table>

				<table width="827" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="report_table_footer" align="left">
					<tfoot>
						<tr style="background-color:#CCCCCC;">
							<th width="100"></th>
							<th width="80"></th>
							<th width="80"></th>
							<th width="80" align="right">Total</th>
							<th width="80" align="right"><? echo number_format($total_requisition_qty,2) ?></th>
							<th width="80" align="right"><? echo number_format($total_yarn_issued_inside,2) ?></th>
							<th width="80" align="right"><? echo number_format($total_yarn_issued_outside,2) ?></th>
							<th width="80" align="right"><? echo number_format($total_yarn_issued,2) ?></th>
							<th width="80" align="right"><? echo number_format($total_yarn_issue_retn_qty,2); ?></th>
							<th align="right"><? echo number_format($total_net_issue,2); ?></th>	
						</tr>
					</tfoot>
				</table>	
				</div>
				<!-- Yarn Info End-->

				<!-- Knitting Production Info Start-->
				<?
				$all_program_no_arr = array_filter($program_arr);
				if(count($all_program_no_arr) > 0)
				{
					$all_program_no_cond_id = implode(",", $all_program_no_arr);
					$programCond = $program_booking_cond = "";
					if($db_type==2 && count($all_program_no_arr)>999)
					{
						$all_program_no_cond_id_chunk=array_chunk($all_program_no_arr, 999);
						foreach($all_program_no_cond_id_chunk as $chunk_prog)
						{
							$chunk_prog_val=implode(",", $chunk_prog);
							if(!$program_booking_cond)$program_booking_cond.=" and ( a.booking_id in($chunk_prog_val) ";
							else $program_booking_cond.=" or  a.booking_id in($chunk_prog_val) ";
						}
						$program_booking_cond.=")";
					}
					else
					{
						$program_booking_cond=" and a.booking_id in($all_program_no_cond_id)";
					}
					$production_info_sql="SELECT a.id, a.knitting_source, a.booking_id as program_no, b.prod_id, sum(c.qnty) as production_qty, c.barcode_no, d.detarmination_id, e.construction
					from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, product_details_master d, lib_yarn_count_determina_mst e
					where a.id=b.mst_id and a.id=c.mst_id and b.id=c.dtls_id and b.prod_id=d.id and d.detarmination_id=e.id $program_booking_cond and a.entry_form=2 and a.receive_basis=2
					group by a.id, a.knitting_source, a.booking_id, b.prod_id, c.barcode_no, d.detarmination_id, e.construction";
					// echo $production_info_sql;die;
					$production_info_result=sql_select($production_info_sql);
					$production_arr=array();$production_barcode_arr=array();
					foreach ($production_info_result as $key => $value) 
					{
						$production_arr[$value[csf("construction")]]["id"].=$value[csf("id")].',';
						$production_arr[$value[csf("construction")]]["knitting_source"]=$value[csf("knitting_source")];
						$production_arr[$value[csf("construction")]]["program_no"]=$value[csf("program_no")];
						$production_arr[$value[csf("construction")]]["production_qty"]+=$value[csf("production_qty")];
						$production_arr[$value[csf("construction")]]["detarmination"]=$value[csf("detarmination_id")];
						$production_arr[$value[csf("construction")]]["prod_id"]=$value[csf("prod_id")];
						$production_arr[$value[csf("construction")]]["construction"]=$value[csf("construction")];

						$production_barcode_arr[$value[csf("barcode_no")]]=$value[csf("barcode_no")];
						$production_id_arr[$value[csf("id")]]=$value[csf("id")];
						$prod_id_arr[$value[csf("prod_id")]]=$value[csf("prod_id")];
					}
					//echo "<pre>";print_r($production_arr);die;
					$all_prod_ids= array_chunk($prod_id_arr, 999);
					$all_prod_ids_cond=" and(";
					foreach($all_prod_ids as $prod_ids)
					{
						if($all_prod_ids_cond==" and(") $all_prod_ids_cond.=" a.id in(". implode(',', $prod_ids).")"; else $all_prod_ids_cond.="  or a.id in(". implode(',', $prod_ids).")";
					}
					$all_prod_ids_cond.=")";
					$fabric_desc_arr = return_library_array("select a.id, a.item_description from product_details_master a where a.item_category_id=13 $all_prod_ids_cond", "id", "item_description");

					$all_production_id_arr = array_filter($production_id_arr);
					if(count($all_production_id_arr) > 0)
					{
						$all_production_ids = implode(",", $all_production_id_arr);
						$production_ids_cond = "";
						if($db_type==2 && count($all_production_id_arr)>999)
						{
							$all_issue_id_cond_chunk=array_chunk($all_production_id_arr, 999);
							foreach($all_issue_id_cond_chunk as $chunk_prog)
							{
								$chunk_prog_val=implode(",", $chunk_prog);
								if(!$production_ids_cond)$production_ids_cond.=" and ( a.booking_id in($chunk_prog_val) ";
								else $production_ids_cond.=" or  a.booking_id in($chunk_prog_val) ";
							}
							$production_ids_cond.=")";
						}
						else
						{
							$production_ids_cond=" and b.grey_sys_id in($all_production_ids)";
						}

						/*$delivery_sql="SELECT a.id as delivery_id, b.grey_sys_id, b.product_id, sum(b.current_delivery) as delivery_qty
						from pro_grey_prod_delivery_mst a, pro_grey_prod_delivery_dtls b
						where a.id=b.mst_id $production_ids_cond and a.entry_form=56 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
						group by a.id, b.grey_sys_id, b.product_id";*/

						$delivery_sql="SELECT a.id as delivery_id, b.grey_sys_id, b.product_id, sum(b.current_delivery) as delivery_qty, e.construction
						from pro_grey_prod_delivery_mst a, pro_grey_prod_delivery_dtls b, product_details_master d, lib_yarn_count_determina_mst e
						where a.id=b.mst_id  and b.product_id=d.id and d.detarmination_id=e.id  and d.detarmination_id=e.id $production_ids_cond 
						and a.entry_form=56 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
						group by a.id, b.grey_sys_id, b.product_id, e.construction";
						//echo $delivery_sql;
						$delivery_sql_result=sql_select($delivery_sql);
						$delivery_qty_arr=array();
						foreach ($delivery_sql_result as $key => $row) 
						{
							$delivery_qty_arr[$row[csf("construction")]]+=$row[csf("delivery_qty")];
						}
						// echo "<pre>";print_r($delivery_qty_arr);die;
					}
				}
				?>
				<table width="685" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" style="float:left; margin-top: 10px;">
					<thead>
						<tr>
							<th colspan="8">Knitting Production Info</th>
						</tr>
						<tr>
							<th width="100">Construction</th>
							<th width="80">Program Qty</th>
							<th width="80">Knitting Qty inhouse</th>
							<th width="80">Knitting Qty Outside</th>
							<th width="80">Total Knit Qty</th>
							<th width="80">Knit. Balance</th>
							<th width="80">Knit. Del. To Store Qty</th>
							<th>Delivery Balance</th>
						</tr>
					</thead>
				</table>
				
				<div style="width:685px; float:left; max-height:300px; overflow-y:scroll" id="scroll_body3">
				<table width="667" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_body_show3" style="float:left;">
					<tbody>
					<?
					$i=1;
					$total_production_inside_qty=$total_production_outside_qty=$total_all_knit_qty=$total_knit_balance=$total_knit_del_to_store_qty=$total_delivery_balance_qty=0;
					foreach($production_arr as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
						if($row["knitting_source"]==1)
						{
							$production_inside_qty=$row["production_qty"]; 
						}
						else
						{ 
							$production_outside_qty=$row["production_qty"]; 
						}
						$total_knit_qty=$production_inside_qty+$production_outside_qty;
						$fabric_desc=$fabric_desc_arr[$row["prod_id"]];
						$construction=explode(",", $fabric_desc);
						?>						
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_4nd<? echo $i; ?>','<? echo $bgcolor; ?>');" id="tr_4nd<? echo $i; ?>">
							<td width="100" title="<? echo $row["detarmination"]; ?>"><p><? 
							echo $row["construction"]; //$construction[0]; ?></p></td>
							<td width="80" align="right"><p><? echo number_format($program_qty_arr[$row["program_no"]],2); ?></p></td>
							<td width="80" align="right"><p><? echo number_format($production_inside_qty,2); ?></p></td>
							<td width="80" align="right" ><p><? echo number_format($production_outside_qty,2); ?></p></td>
							<td width="80" align="right"><p><? echo number_format($total_knit_qty,2); ?></p></td> 
							<td width="80" align="right" ><p><? echo number_format($program_qty_arr[$row["program_no"]]-$production_inside_qty,2); ?></p></td>
							<td width="80" align="right" title="<? echo $row["id"]; ?>"><p><? echo number_format($delivery_qty_arr[$row["construction"]],2); ?></p></td>
							<td class="breakAll" align="right"><p><? echo number_format($total_knit_qty-$delivery_qty_arr[$row["construction"]],2); ?></p></td>
						</tr>
						<?
						$total_production_inside_qty+=$production_inside_qty;
						$total_production_outside_qty+=$production_outside_qty;
						$total_all_knit_qty+=$total_knit_qty;
						$total_knit_balance+=$program_qty_arr[$row["program_no"]]-$production_inside_qty;
						$total_knit_del_to_store_qty+=$delivery_qty_arr[$row["construction"]];
						$total_delivery_balance_qty+=$total_knit_qty-$delivery_qty_arr[$row["construction"]];
						$i++;
					}
					?>
					</tbody>
				</table>

				<table width="667" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="report_table_footer" align="left">
					<tfoot>
						<tr style="background-color:#CCCCCC;">
							<th width="100"></th>
							<th width="80" align="right">Total</th>
							<th width="80" align="right"><? echo number_format($total_production_inside_qty,2); ?></th>
							<th width="80" align="right"><? echo number_format($total_production_outside_qty,2); ?></th>
							<th width="80" align="right"><? echo number_format($total_all_knit_qty,2); ?></th>
							<th width="80" align="right"><? echo number_format($total_knit_balance,2); ?></th>
							<th width="80" align="right"><? echo number_format($total_knit_del_to_store_qty,2); ?></th>
							<th align="right"><? echo number_format($total_delivery_balance_qty,2); ?></th>
						</tr>
					</tfoot>
				</table>	
				</div>
				<!-- Knitting Production Info End-->

				<!-- Grey Fabric Store Info Start-->
				<?
				$all_production_barcode_arr = array_filter($production_barcode_arr);
				if(count($all_production_barcode_arr) > 0)
				{
					$all_barcode_no = implode(",", $all_production_barcode_arr);
					$barcode_no_Cond = $barcode_no_cond = "";
					if($db_type==2 && count($all_production_barcode_arr)>999)
					{
						$all_production_barcode_chunk=array_chunk($all_production_barcode_arr, 999);
						foreach($all_production_barcode_chunk as $chunk_barcode)
						{
							$chunk_barcode_val=implode(",", $chunk_barcode);
							if(!$barcode_no_cond)$barcode_no_cond.=" and ( c.barcode_no in($chunk_barcode_val) ";
							else $barcode_no_cond.=" or  c.barcode_no in($chunk_barcode_val) ";
						}
						$barcode_no_cond.=")";
					}
					else
					{
						$barcode_no_cond=" and c.barcode_no in($all_barcode_no)";
					}

					// only main query is Knit Grey Fabric Roll Receive
					$grey_fabric_recv_info_sql="SELECT a.store_id,a.knitting_source, b.prod_id, c.qnty, c.booking_no as program_no, c.barcode_no, c.po_breakdown_id
					from inv_receive_master a, inv_transaction b, pro_roll_details c
					where a.id=b.mst_id and b.pi_wo_batch_no=c.dtls_id and a.entry_form=58 and c.entry_form=58  and b.transaction_type=1 and b.booking_without_order=0 and c.booking_without_order=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $barcode_no_cond";
					//echo $grey_fabric_recv_info_sql;die;
					$grey_fabric_recv_info_result=sql_select($grey_fabric_recv_info_sql);
					$grey_fabric_recv_data_arr=array();$recv_barcode_no_arr=array();
					foreach ($grey_fabric_recv_info_result as $key => $row) 
					{
						$grey_fabric_recv_data_arr[$row[csf('prod_id')]]['knitting_source']=$row[csf('knitting_source')];
						$grey_fabric_recv_data_arr[$row[csf('prod_id')]]['prod_id']=$row[csf('prod_id')];
						$grey_fabric_recv_data_arr[$row[csf('prod_id')]]['program_no']=$row[csf('program_no')];
						$grey_fabric_recv_data_arr[$row[csf('prod_id')]]['recv_qnty']+=$row[csf('qnty')];
						$grey_fabric_recv_data_arr[$row[csf('prod_id')]]['storeIds'].=$row[csf('store_id')].',';
						$grey_fabric_recv_data_arr[$row[csf('prod_id')]]['barcodeNo'].=$row[csf('barcode_no')].',';
						
						$recv_barcode_no_arr[$row[csf('barcode_no')]].=$row[csf('barcode_no')].',';
					}

					$grey_fabric_issue_sql="SELECT a.knit_dye_source, b.prod_id, c.po_breakdown_id, c.barcode_no, c.qnty as issue_qty, b.store_id
					from inv_issue_master a, inv_transaction b, pro_roll_details c, order_wise_pro_details d
					where a.id=b.mst_id and a.id=c.mst_id and b.id=d.trans_id and d.dtls_id=c.dtls_id and a.entry_form=61 and c.is_returned=0 and d.entry_form=61 and c.entry_form=61 and b.booking_without_order=0 $barcode_no_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
					$grey_fabric_issue_info_result=sql_select($grey_fabric_issue_sql);
					$grey_fabric_issue_data_arr=array();$issue_barcode_no_arr=array();
					foreach ($grey_fabric_issue_info_result as $key => $row) 
					{
						$grey_fabric_issue_data_arr[$row[csf('barcode_no')]]['issue_knitting_source']=$row[csf('knit_dye_source')];
						$grey_fabric_issue_data_arr[$row[csf('barcode_no')]]['issue_barcode']+=$row[csf('issue_qty')];
					}
					//echo "<pre>";print_r($grey_fabric_issue_data_arr);
				}

				?>
				<table width="920" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" style="float:left; margin-top: 10px;">
					<thead>
						<tr>
							<th colspan="11">Grey Fabric Store Info</th>
						</tr>
						<tr>
							<th width="100">Construction</th>
							<th width="80">Program Qty</th>
							<th width="80">Received Qty Inhouse</th>
							<th width="80">Received Qty Outside</th>
							<th width="80">Total Receive</th>
							<th width="80">Receive Balance</th>
							<th width="80">Grey Issue Inside</th>
							<th width="80">Grey Issue Outside</th>
							<th width="80">Total Issue</th>
							<th width="80">Grey In hand</th>
							<th>Store Name</th>
						</tr>
					</thead>
				</table>
				
				<div style="width:925px; float:left; max-height:300px; overflow-y:scroll" id="scroll_body4">
				<table width="907" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_body_show4" style="float:left;">
					<tbody>
					<?
					$i=1;
					$total_recv_inside_qty=$total_recv_outside_qty=$total_total_recv_qty=$total_receive_balance=$total_issue_inside_qty=$total_issue_outside_qty=$total_total_issue_qty=$total_grey_in_hand=0;
					foreach($grey_fabric_recv_data_arr as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
						if($row["knitting_source"]==1)
						{
							$recv_inside_qty=$row["recv_qnty"]; 
						}
						else
						{ 
							$recv_outside_qty=$row["recv_qnty"]; 
						}
						$total_recv_qty=$recv_inside_qty+$recv_outside_qty;
						$program_qty=$program_qty_arr[$row["program_no"]];
						$receive_balance=$program_qty-$total_recv_qty;
						$fabric_desc=$fabric_desc_arr[$row["prod_id"]];
						$construction=explode(",", $fabric_desc);
						$recv_barcode=chop($row["barcodeNo"],',');
						$recv_barcode_arr=explode(',', $recv_barcode);
						// echo "<pre>";print_r($grey_fabric_issue_data_arr);
						$issue_qty=0;$issue_knitting_source=0;
						foreach ($recv_barcode_arr as $key => $recv_barcode) 
						{
							$issue_qty+=$grey_fabric_issue_data_arr[$recv_barcode]['issue_barcode'];
							$issue_knitting_source=$grey_fabric_issue_data_arr[$recv_barcode]['issue_knitting_source'];
							//echo $issue_qty.'<br>';
						}
						// echo $issue_knitting_source.'<br>';
						if($issue_knitting_source==1)
						{
							$issue_inside_qty=$issue_qty; 
						}
						else
						{ 
							$issue_outside_qty=$issue_qty; 
						}
						$total_issue_qty=$issue_inside_qty-$issue_outside_qty;
						$grey_in_hand=$total_recv_qty-$total_issue_qty;

						$recv_storeIds=chop($row["storeIds"],',');
						$recv_storeIds_arr=array_unique(explode(',', $recv_storeIds));
						$store="";
						foreach ($recv_storeIds_arr as $key => $storeId) // store
						{
							if ($store=="") 
							{
								$store=$store_array[$storeId];
							}
							else
							{
								$store.=','.$store_array[$storeId];
							}
						}
						?>						
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_5nd<? echo $i; ?>','<? echo $bgcolor; ?>');" id="tr_5nd<? echo $i; ?>">
							<td width="100"><? echo $construction[0]; ?></td>
							<td width="80" align="center"><p><? echo number_format($program_qty_arr[$row["program_no"]],2); ?></p></td>
							<td width="80" align="right"><p><? echo number_format($recv_inside_qty,2); ?></p></td>
							<td width="80" align="right"><p><? echo number_format($recv_outside_qty,2); ?></p></td>
							<td width="80" align="right"><p><? echo number_format($total_recv_qty,2); ?></p></td> 
							<td width="80" align="right" title="<? echo 'Recv barcode:'.$recv_barcode; ?>"><p><? echo number_format($receive_balance,2); ?></p></td>
							<td width="80" align="right"><p><? echo number_format($issue_inside_qty,2); ?></p></td>
							<td width="80" align="right"><p><? echo number_format($issue_outside_qty,2); ?></p></td>
							<td width="80" align="right"><p><? echo number_format($total_issue_qty,2); ?></p></td>
							<td width="80" align="right"><p><? echo number_format($grey_in_hand,2); ?></p></td>
							<td class="breakAll"><p><? echo $store; ?></p></td>
						</tr>
						<?
						$total_recv_inside_qty+=$recv_inside_qty;
						$total_recv_outside_qty+=$recv_outside_qty;
						$total_total_recv_qty+=$total_recv_qty;
						$total_receive_balance+=$receive_balance;
						$total_issue_inside_qty+=$issue_inside_qty;
						$total_issue_outside_qty+=$issue_outside_qty;
						$total_total_issue_qty+=$total_issue_qty;
						$total_grey_in_hand+=$grey_in_hand;
						$i++;
					}
					?>
					</tbody>
				</table>

				<table width="907" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="report_table_footer" align="left">
					<tfoot>
						<tr style="background-color:#CCCCCC;">
							<th width="100"></th>
							<th width="80" align="right">Total</th>
							<th width="80" align="right"><? echo number_format($total_recv_inside_qty,2); ?></th>
							<th width="80" align="right"><? echo number_format($total_recv_outside_qty,2); ?></th>
							<th width="80" align="right"><? echo number_format($total_total_recv_qty,2); ?></th>
							<th width="80" align="right"><? echo number_format($total_receive_balance,2); ?></th>
							<th width="80" align="right"><? echo number_format($total_issue_inside_qty,2); ?></th>
							<th width="80" align="right"><? echo number_format($total_issue_outside_qty,2); ?></th>
							<th width="80" align="right"><? echo number_format($total_total_issue_qty,2); ?></th>
							<th width="80" align="right"><? echo number_format($total_grey_in_hand,2); ?></th>
							<th></th>						
						</tr>
					</tfoot>
				</table>	
				</div>
				<!-- Grey Fabric Store Info End-->

				<!-- Batch & Dyeing Production Info (Unload And Shade Matched) Start-->
				<?				
				if ($barcode_no_cond!="") 
				{
					$batch_sql="SELECT a.id as batch_id, a.batch_no, sum(c.batch_qnty) as batch_qnty, a.booking_no
					from pro_batch_create_mst a, pro_batch_create_dtls c where a.id=c.mst_id $barcode_no_cond and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id, a.batch_no, a.booking_no";
					// echo $batch_sql;
					$batch_sql_result=sql_select($batch_sql);
					$batch_id_arr = array(); $batch_booking_no_arr = array();
					foreach ($batch_sql_result as $key => $value) 
					{
						$batch_id_arr[$value[csf("batch_id")]]=$value[csf("batch_id")];
						$batch_booking_no_arr[$value[csf("batch_id")]]=$value[csf("booking_no")];
					}
					$all_batch_id_arr = array_filter($batch_id_arr);
					if(count($all_batch_id_arr) > 0)
					{
						$all_batch_id = implode(",", $all_batch_id_arr);
						$batchIdCond=$batch_id_cond=$batchIdCond2=$batch_id_cond2=$batchIdCond3=$batch_id_cond3="";
						if($db_type==2 && count($all_batch_id_arr)>999)
						{
							/*$all_batch_id_chunk=array_chunk($all_batch_id_arr,999) ;
							foreach($all_batch_id_chunk as $chunk_batch_id)
							{
								$chunk_batch_id_val=implode(",",$chunk_batch_id);
								$batchIdCond.=" and a.id in($chunk_batch_id_val) or ";
								$batchIdCond2.=" and b.batch_id in($chunk_batch_id_val) or ";
								$batchIdCond3.=" and b.pi_wo_batch_no in($chunk_batch_id_val) or ";
							}
							$batch_id_cond.=" and (".chop($batchIdCond,'or ').")";
							$batch_id_cond2.=" and (".chop($batchIdCond2,'or ').")";
							$batch_id_cond3.=" and (".chop($batchIdCond3,'or ').")";*/

							$all_batch_id_chunk=array_chunk($all_batch_id_arr, 999);
							foreach($all_batch_id_chunk as $chunk_batch_id)
							{
								$chunk_batch_id_val=implode(",", $chunk_batch_id);
								if(!$batch_id_cond)$batch_id_cond.=" and ( a.id in($chunk_batch_id_val) ";
								else $batch_id_cond.=" or  a.id in($chunk_batch_id_val) ";
								if(!$batch_id_cond2)$batch_id_cond2.=" and ( b.batch_id in($chunk_batch_id_val) ";
								else $batch_id_cond2.=" or b.batch_id in($chunk_batch_id_val) ";
								if(!$batch_id_cond3)$batch_id_cond3.=" and ( b.pi_wo_batch_no in($chunk_batch_id_val) ";
								else $batch_id_cond3.=" or b.pi_wo_batch_no in($chunk_batch_id_val) ";
							}
							$batch_id_cond.=")";
							$batch_id_cond2.=")";
							$batch_id_cond3.=")";
						}
						else
						{
							$batch_id_cond=" and a.id in($all_batch_id)";
							$batch_id_cond2=" and b.batch_id in($all_batch_id)";
							$batch_id_cond3=" and b.pi_wo_batch_no in($all_batch_id)";
						}

						$dyeing_batch_sql="SELECT a.id as batch_id, a.batch_no, a.color_id, b.item_description, a.extention_no, b.batch_qnty, a.booking_no
						from pro_batch_create_mst a, pro_batch_create_dtls b 
						where a.id=b.mst_id $batch_id_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.batch_no";
						// echo $dyeing_batch_sql;
						$dyeing_batch_result=sql_select($dyeing_batch_sql);
						$dyeing_batch_data_arr=array();$dyeing_batch_color_arr=array();
						foreach ($dyeing_batch_result as $key => $row)
						{
							$dyeing_batch_data_arr[$row[csf('batch_id')]][$row[csf('color_id')]][$row[csf('extention_no')]]['batchQnty']+=$row[csf('batch_qnty')];
							$dyeing_batch_data_arr[$row[csf('batch_id')]][$row[csf('color_id')]][$row[csf('extention_no')]]['batchNo']=$row[csf('batch_no')];
							$dyeing_batch_data_arr[$row[csf('batch_id')]][$row[csf('color_id')]][$row[csf('extention_no')]]['itemDescription'].=$row[csf('item_description')].'*';
							$dyeing_batch_data_arr[$row[csf('batch_id')]][$row[csf('color_id')]][$row[csf('extention_no')]]['bookingNo']=$row[csf('booking_no')];
							$dyeing_batch_color_arr[$row[csf('batch_id')]]=$row[csf('color_id')];
						}

						$batch_production_sql="SELECT b.id as subpmstid, b.batch_id, c.production_qty as production_qty, b.process_id, b.result, c.load_unload_id, b.entry_form, b.batch_ext_no as extention_no 
						from  pro_fab_subprocess b, pro_fab_subprocess_dtls c
						where b.id=c.mst_id and b.process_id='31' and c.load_unload_id=2 and b.result=1
						$batch_id_cond2 and b.status_active=1 and b.is_deleted=0 order by b.batch_id";
						// echo $batch_production_sql;
						$batch_production_result=sql_select($batch_production_sql);
						$production_qty_data_arr=array();
						foreach ($batch_production_result as $key => $row)
						{
							$color=$dyeing_batch_color_arr[$row[csf('batch_id')]];
							$production_qty_data_arr[$row[csf('batch_id')]][$color][$row[csf('extention_no')]]['production_qty']+=$row[csf('production_qty')];
						}				
					}
				}
				?>				
				<table width="635" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" style="float:left; margin-top: 10px;">
					<thead>
						<tr>
							<th colspan="8">Batch & Dyeing Production Info (Unload And Shade Matched)</th>
						</tr>
						<tr>
							<th width="70">Colour</th>
							<th width="100">Construction</th>
							<th width="70">Batch No</th>
							<th width="100">Booking No</th>
							<th width="30">Ext</th>
							<th width="80">Batch Qty</th>
							<th width="80">Dyeing Qty</th>
							<th>Dyeing Balance</th>
						</tr>
					</thead>
				</table>
				
				<div style="width:635px; float:left; max-height:300px; overflow-y:scroll" id="scroll_body5">
				<table width="617" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_body_show5" style="float:left;">
					<tbody>
					<?
					$i=1;
					$total_batch_qnty=$total_production_qty=$total_dyeing_balance=0;
					foreach ($dyeing_batch_data_arr as $batch_id_key => $batch_id_val)
					{
						foreach ($batch_id_val as $color_id_key => $batch_color_id) 
						{
							foreach ($batch_color_id as $extention_key => $row) 
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

								$construction=array_filter(explode('*', $row['itemDescription']));
								$cons_var="";
								foreach ($construction as $key => $value) 
								{
									$construction2=explode(',', $value);
									$cons_var.=($cons_var=="")?$construction2[0]:",".$construction2[0];
								}
								$unique_cons_var=implode(",", array_unique(explode(",", $cons_var)));
								$production_qty=$production_qty_data_arr[$batch_id_key][$color_id_key][$extention_key]['production_qty'];
								?>						
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_6nd<? echo $i; ?>','<? echo $bgcolor; ?>');" id="tr_6nd<? echo $i; ?>">
									<td width="70"><p><? echo $color_arr[$color_id_key]; ?></p></td>
									<td width="100" align="center"><p><? echo $unique_cons_var; ?></p></td>
									<td width="70" align="center" title="<? echo $batch_id_key; ?>"><p><? echo $row['batchNo']; ?></p></td>
									<td width="100" align="center"><p><? echo $row['bookingNo']; ?></p></td>
									<td width="30" align="center"><p><? echo $extention_key; ?></p></td>
									<td width="80" align="right" ><p><? echo number_format($row['batchQnty'],2); ?></p></td>
									<td width="80" align="right"><p><? echo number_format($production_qty,2); ?></p></td>
									<td class="breakAll" align="right"><p><? echo number_format($row['batch_qnty']-$row['production_qty'],2); ?></p></td>
								</tr>
								<?
								$total_batch_qnty+=$row['batchQnty'];
								$total_production_qty+=$production_qty;
								$total_dyeing_balance+=$row['batch_qnty']-$row['production_qty'];
								$i++;
							}
							
						}						
					}
					?>
					</tbody>
				</table>

				<table width="617" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="report_table_footer" align="left">
					<tfoot>
						<tr style="background-color:#CCCCCC;">
							<th width="70"></th>
							<th width="100"></th>
							<th width="70"></th>
							<th width="100"></th>
							<th width="30" align="right">Total</th>
							<th width="80" align="right"><? echo number_format($total_batch_qnty,2); ?></th>
							<th width="80" align="right"><? echo number_format($total_production_qty,2); ?></th>
							<th align="right"><? echo number_format($total_dyeing_balance,2); ?></th>						
						</tr>
					</tfoot>
				</table>	
				</div>
				<!-- Batch & Dyeing Production Info (Unload And Shade Matched) End-->

				<!-- Finish Process Info (Process will Display By Batch Sequence) Start-->
				<?
				if ($batch_id_cond!="")
				{
					$batch_sql="SELECT a.id as batch_id, a.batch_no, a.color_id, b.item_description, a.extention_no, b.batch_qnty as batch_qnty
					from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  
					$batch_id_cond  order by a.batch_no";
					// echo $batch_sql;die;
					$batch_sql_result=sql_select($batch_sql);
					$finish_process_data_arr=array();$finish_process_data_arr=array();$process_id_arr=array();$process_wise_qty_arr=array();$batch_color_arr=array();
					foreach ($batch_sql_result as $key => $row)
					{
						$finish_process_data_arr[$row[csf('batch_id')]][$row[csf('color_id')]][$row[csf('extention_no')]]['batchQnty']+=$row[csf('batch_qnty')];
						$finish_process_data_arr[$row[csf('batch_id')]][$row[csf('color_id')]][$row[csf('extention_no')]]['batchNo']=$row[csf('batch_no')];
						$finish_process_data_arr[$row[csf('batch_id')]][$row[csf('color_id')]][$row[csf('extention_no')]]['itemDescription'].=$row[csf('item_description')].'*';
						$batch_color_arr[$row[csf('batch_id')]]=$row[csf('color_id')];
					}

					$process_sql="SELECT b.id as subpmstid, b.batch_id, c.production_qty as production_qty, b.process_id, b.result,  c.load_unload_id, b.entry_form, b.batch_ext_no as extention_no from  pro_fab_subprocess b, pro_fab_subprocess_dtls c
					where b.id=c.mst_id and b.process_id!='31' and c.load_unload_id!=2 
					$batch_id_cond2 and b.status_active=1 and b.is_deleted=0 order by b.batch_id";
					// echo $process_sql;
					$process_sql_result=sql_select($process_sql);
					$process_count_arr=array();$check_mastId_arr=array();
					foreach ($process_sql_result as $key => $row)
					{
						$color=$batch_color_arr[$row[csf('batch_id')]];
						$finish_process_data_arr[$row[csf('batch_id')]][$color][$row[csf('extention_no')]]['production_qty']+=$row[csf('production_qty')];
						$process_wise_qty_arr[$row[csf('batch_id')]][$color][$row[csf('extention_no')]][$row[csf('process_id')]]["process_qty"]+=$row[csf('production_qty')];

						if ($row[csf('process_id')]!="") 
						{
							$process_id_arr[$row[csf('process_id')]]=$row[csf('process_id')];
						}
						if (!in_array($row[csf('subpmstid')], $check_mastId_arr)) 
						{
							$check_mastId_arr[$row[csf('subpmstid')]]=$row[csf('subpmstid')];
							$process_count_arr[$row[csf('batch_id')]][$row[csf('process_id')]]++;
						}
					}
					$count_process=count($process_id_arr);
					//echo "<pre>";print_r($process_count_arr);echo "<pre>";die;
				}
				
				$table_width=480;
				$div_width=495;
				$table_width2=478;
				?>
				<div style="width:<? echo $table_width+20+($count_process*80); ?>px; float:left;">
					<table width="<? echo $table_width+($count_process*80); ?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" style="float:left; margin-top: 10px;">
						<thead>
							<tr>
								<th colspan="<? echo 5+($count_process); ?>">Finish Process Info (Process will Display By Batch Sequence)</th>
							</tr>
							<tr>
								<th rowspan="2" width="70">Colour</th>
								<th rowspan="2" width="100">Construction</th>
								<th rowspan="2" width="70">Batch No</th>
								<th rowspan="2" width="30">Ext</th>
								<th rowspan="2" width="80">Batch Qty</th>
								<?
			                    foreach($process_id_arr as $process)
			                    {
			                    	?>
			                    	<th width="80" title="<? echo $process; ?>" style="word-wrap:break-word; word-break: break-all;"><? echo $conversion_cost_head_array[$process]; //$entry_form?></th>
			                    	<?
			                    }  
			                    ?>
							</tr>
							<tr>
								<?
			                    foreach($process_id_arr as $process)
			                    {
			                    	?>
			                    	<th width="80" style="word-wrap:break-word; word-break: break-all;"><? echo 'Finish Product';?></th>
			                    	<?
			                    }  
			                    ?>
							</tr>
						</thead>
					</table>
					
					<div style="width:<? echo $div_width+($count_process*80); ?>px; float:left; max-height:300px; overflow-y:scroll" id="scroll_body6">
					<table width="<? echo $table_width2+($count_process*80); ?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_body_show6" style="float:left;">
						<tbody>
						<?
						$i=1;
						$total_batchQnty=0;
						foreach ($finish_process_data_arr as $batch_no_key => $batch_id_val)
						{
							foreach ($batch_id_val as $color_id_key => $batch_color_id) 
							{
								foreach ($batch_color_id as $extention_key => $row) 
								{
									if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									$construction=array_filter(explode('*', $row['itemDescription']));
									$cons_var="";
									foreach ($construction as $key => $value) 
									{
										$construction2=explode(',', $value);
										$cons_var.=($cons_var=="")?$construction2[0]:",".$construction2[0];
									}
									$unique_cons_var=implode(",", array_unique(explode(",", $cons_var)));
									?>						
									<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_7nd<? echo $i; ?>','<? echo $bgcolor; ?>');" id="tr_7nd<? echo $i; ?>">
										<td width="70"><p><? echo $color_arr[$color_id_key]; ?></p></td>
										<td width="100" align="center"><p><? echo $unique_cons_var; ?></p></td>
										<td width="70" align="center"><p><? echo $row['batchNo']; ?></p></td>
										<td width="30" align="center"><p><? echo $extention_key; ?></p></td>
										<td width="80" align="right" ><p><? echo number_format($row['batchQnty'],2); ?></p></td>
										<?

					                    foreach($process_id_arr as $process)
					                    {
					                    	$count=$process_count_arr[$batch_no_key][$process];
					                    	$process_qty=$process_wise_qty_arr[$batch_no_key][$color_id_key][$extention_key][$process]["process_qty"];

					                    	$total_process_qty_arr[$process]+=$process_wise_qty_arr[$batch_no_key][$color_id_key][$extention_key][$process]["process_qty"];
					                    	?>
					                    	<td width="80" title="<? echo "batch_id:".$batch_no_key.',color_id:'.$color_id_key.',extention:'.$extention_key.',process_id:'.$process; ?>" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($process_qty,2); if ($count>0) { echo " (".$count.")"; } ?></td>
					                    	<?
					                    } 
					                    ?>
									</tr>
									<?
									$total_batchQnty+=$row['batchQnty'];
									$i++;
								}								
							}
						}
						?>
						</tbody>
					</table>
					</div>
					<table width="<? echo $table_width2+($count_process*80); ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="report_table_footer" align="left">
						<tfoot>
							<tr style="background-color:#CCCCCC;">
								<th width="70"></th>
								<th width="100"></th>
								<th width="70"></th>
								<th width="30" align="right">Total</th>
								<th width="80" align="right"><? echo number_format($total_batchQnty,2); ?></th>
								<?
			                    foreach($process_id_arr as $process)
			                    {
			                    	$process_qty_total = $total_process_qty_arr[$process];
			                    	?>
			                    	<th width="80" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($process_qty_total,2); ?></th>
			                    	<?
			                    } 
			                    ?>		
							</tr>
						</tfoot>
					</table>
				</div>
				<!-- Finish Process Info (Process will Display By Batch Sequence) End-->

				<!-- Finish Fabric Production Info Start-->
				<?
				if ($batch_id_cond!="")
				{
					$finish_fab_production_sql="SELECT a.id as batch_id, a.batch_no, a.color_id, a.extention_no, b.item_description, sum(b.batch_qnty) as batch_qnty
					from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id $batch_id_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.batch_no, a.color_id, a.extention_no, b.item_description order by a.batch_no";// and A.BATCH_NO='AGUN2020'
					// echo $finish_fab_production_sql;//die;
					$finish_fab_production_sql_result=sql_select($finish_fab_production_sql);
					$finish_process_data_arr=array();
					foreach ($finish_fab_production_sql_result as $key => $row)
					{
						$finish_process_data_arr[$row[csf('batch_id')]][$row[csf('color_id')]][$row[csf('extention_no')]]['batch_qnty']+=$row[csf('batch_qnty')];
						$finish_process_data_arr[$row[csf('batch_id')]][$row[csf('color_id')]][$row[csf('extention_no')]]['batchId']=$row[csf('batch_id')];
						$finish_process_data_arr[$row[csf('batch_id')]][$row[csf('color_id')]][$row[csf('extention_no')]]['batchNo']=$row[csf('batch_no')];
						$finish_process_data_arr[$row[csf('batch_id')]][$row[csf('color_id')]][$row[csf('extention_no')]]['itemDescription'].=$row[csf('item_description')].'*';
					}
					// echo "<pre>";print_r($finish_process_data_arr);echo "<pre>";die;

					// Finish Fabric Production Entry
					$finish_prod_sql="SELECT b.batch_id, b.color_id, sum(b.receive_qnty) as finish_qty from inv_receive_master a, pro_finish_fabric_rcv_dtls b 
					where a.id=b.mst_id and a.entry_form=7 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $batch_id_cond2 group by b.batch_id, b.color_id";
					// echo $finish_prod_sql;
					$finish_prod_sql_result=sql_select($finish_prod_sql);
					$finish_prod_data_arr=array();
					foreach ($finish_prod_sql_result as $key => $row)
					{
						$finish_prod_data_arr[$row[csf('batch_id')]][$row[csf('color_id')]]['finish_qty']+=$row[csf('finish_qty')];
					}

					// Finish Fabric Delivery to Store
					$fin_fab_delivery_sql="SELECT b.batch_id, b.color_id, sum(b.current_delivery) as finish_delivery
					from pro_grey_prod_delivery_mst a, pro_grey_prod_delivery_dtls b 
					where a.id=b.mst_id and a.entry_form=54 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $batch_id_cond2  group by b.batch_id, b.color_id";
					// echo $fin_fab_delivery_sql;
					$fin_fab_delivery_result=sql_select($fin_fab_delivery_sql);
					$fin_fab_delivery_data_arr=array();
					foreach ($fin_fab_delivery_result as $key => $row)
					{
						$fin_fab_delivery_data_arr[$row[csf('batch_id')]][$row[csf('color_id')]]['finish_delivery_qty']+=$row[csf('finish_delivery')];
					}
				}
				?>
				<table width="665" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" style="float:left; margin-top: 10px;">
					<thead>
						<tr>
							<th colspan="9">Finish Fabric Production Info</th>
						</tr>
						<tr>
							<th width="70">Colour</th>
							<th width="100">Construction</th>
							<th width="70">Batch No</th>
							<th width="30">Ext</th>
							<th width="80">Batch Qty</th>
							<th width="80">Finish Prod. Qty</th>
							<th width="80">Batch Wise Prod. Balance</th>
							<th width="80">Del. To Store Qty</th>
							<th>Batch Wise Del. Balance</th>
						</tr>
					</thead>
				</table>
				
				<div style="width:665px; float:left; max-height:300px; overflow-y:scroll" id="scroll_body7">
				<table width="647" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_body_show7" style="float:left;">
					<tbody>
					<?
					$i=1;
					$total_batch_qnty=$total_finish_prod_qty=$total_batch_wise_prod_balance=$total_finish_delivery_qty=$total_batch_wise_del_balance=0;
					foreach ($finish_process_data_arr as $batch_key => $bactch_id_val)
					{
						foreach ($bactch_id_val as $color_id_key => $batch_color_id) 
						{
							foreach ($batch_color_id as $extention_key => $row) 
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								$construction=array_filter(explode('*', $row['itemDescription']));
								$cons_var="";
								foreach ($construction as $key => $value) 
								{
									$construction2=explode(',', $value);
									$cons_var.=($cons_var=="")?$construction2[0]:",".$construction2[0];
								}
								$unique_cons_var=implode(",", array_unique(explode(",", $cons_var)));
								$finish_prod_qty=$finish_prod_data_arr[$row['batchId']][$color_id_key]['finish_qty'];
								$batch_wise_prod_balance=$row['batch_qnty']-$finish_prod_qty;

								$finish_delivery_qty=$fin_fab_delivery_data_arr[$row['batchId']][$color_id_key]['finish_delivery_qty'];
								$batch_wise_del_balance=$row['batch_qnty']-$finish_delivery_qty;
								?>						
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_8nd<? echo $i; ?>','<? echo $bgcolor; ?>');" id="tr_8nd<? echo $i; ?>">
									<td width="70"><p><? echo $color_arr[$color_id_key]; ?></p></td>
									<td width="100" align="center"><p><? echo $unique_cons_var; ?></p></td>
									<td width="70" align="center"><p><? echo $row['batchNo']; ?></p></td>
									<td width="30" align="center"><p><? echo $extention_key; ?></p></td>
									<td width="80" align="right" ><p><? echo number_format($row['batch_qnty'],2); ?></p></td>
									<td width="80" align="right"><p><? echo number_format($finish_prod_qty,2); ?></p></td> 
									<td width="80" align="right" ><p><? echo number_format($batch_wise_prod_balance,2); ?></p></td>
									<td width="80" align="right"><p><? echo number_format($finish_delivery_qty,2); ?></p></td>
									<td class="breakAll" align="right"><p><? echo number_format($batch_wise_del_balance,2); ?></p></td>
								</tr>
								<?
								$total_batch_qnty+=$row['batch_qnty'];
								$total_finish_prod_qty+=$finish_prod_qty;
								$total_batch_wise_prod_balance+=$batch_wise_prod_balance;
								$total_finish_delivery_qty+=$finish_delivery_qty;
								$total_batch_wise_del_balance+=$batch_wise_del_balance;
								$i++;
							}
						}
					}
					?>
					</tbody>
				</table>
				<table width="647" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="report_table_footer" align="left">
					<tfoot>
						<tr style="background-color:#CCCCCC;">
							<th width="70"></th>
							<th width="100"></th>
							<th width="70"></th>
							<th width="30" align="right">Total</th>
							<th width="80" align="right"><? echo number_format($total_batch_qnty,2); ?></th>
							<th width="80" align="right"><? echo number_format($total_finish_prod_qty,2); ?></th>
							<th width="80" align="right"><? echo number_format($total_batch_wise_prod_balance,2); ?></th>
							<th width="80" align="right"><? echo number_format($total_finish_delivery_qty,2); ?></th>
							<th><? echo number_format($total_batch_wise_del_balance,2); ?></th>						
						</tr>
					</tfoot>
				</table>	
				</div>
				<!-- Finish Fabric Production Info End-->

				<!-- Finish Fabric Store Info Start-->
				<?
				if ($batch_id_cond3!="")
				{
					// Finish Fabric Production Entry
					/*"select sum(case when c.entry_form in (7,37,66,68) then c.quantity else 0 end) as receive_qnty,
					sum(case when c.entry_form in(14,15,134) and c.trans_type=5 then c.quantity else 0 end) as rec_trns_qnty,
					sum(case when c.entry_form in (18,71) then c.quantity else 0 end) as issue_qnty,
					sum(case when c.entry_form in (126,52) then c.quantity else 0 end) as issue_ret_qnty,
					sum(case when c.entry_form in(14,15,134) and c.trans_type=6 then c.quantity else 0 end) as issue_trns_qnty
					from  inv_transaction b, order_wise_pro_details c
					where b.id=c.trans_id and B.TRANSACTION_TYPE=1 and B.BOOKING_WITHOUT_ORDER=0 and B.PI_WO_BATCH_NO=11649 and c.ENTRY_FORM=37";*/

					// Finish Fabric receive
					$receive_sql="SELECT b.pi_wo_batch_no as batch_id, c.color_id, b.store_id, sum(c.quantity) as receive_qnty
					from  inv_transaction b, order_wise_pro_details c
					where b.id=c.trans_id and b.transaction_type=1 and b.booking_without_order=0 and c.entry_form=37  and b.item_category=2 $batch_id_cond3
					group by b.pi_wo_batch_no, b.store_id, c.color_id";
					// echo $receive_sql;
					$receive_sql_result=sql_select($receive_sql);
					$receive_data_arr=array();
					foreach ($receive_sql_result as $key => $row)
					{
						$receive_data_arr[$row[csf('batch_id')]][$row[csf('color_id')]]['receiveQnty']+=$row[csf('receive_qnty')];
						$receive_data_arr[$row[csf('batch_id')]][$row[csf('color_id')]]['storeId']=$row[csf('store_id')];
					}

					// Finish Fabric Transfer Sql
					// inventory\finish_fabric\requires\finish_fabric_transfer_controller.php
					$fin_fab_tranf_sql="SELECT b.pi_wo_batch_no as batch_id, d.color_id, sum(c.quantity) as trans_out_qnty 
					from inv_transaction b, order_wise_pro_details c, inv_item_transfer_dtls d 
					where b.id=c.trans_id and b.id=d.trans_id and c.trans_id=d.trans_id and c.dtls_id = d.id and c.trans_type=6 and b.transaction_type=6 and b.item_category=2 $batch_id_cond3
					and b.status_active =1 and b.is_deleted =0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and b.pi_wo_batch_no> 0 and d.active_dtls_id_in_transfer =1
					group by  b.pi_wo_batch_no, d.color_id";// and b.pi_wo_batch_no='11649'
					// echo $fin_fab_tranf_sql;
					$fin_fab_tranf_result=sql_select($fin_fab_tranf_sql);
					$fin_fab_tranf_data_arr=array();
					foreach ($fin_fab_tranf_result as $key => $row)
					{
						$fin_fab_tranf_data_arr[$row[csf('batch_id')]][$row[csf('color_id')]]['trans_out_qnty']+=$row[csf('trans_out_qnty')];
					}

					// Finish Fabric Issue Sql
					$fin_fab_issue_sql="SELECT b.pi_wo_batch_no as batch_id, d.color_id, sum(b.cons_quantity) as issue_qnty 
					from inv_transaction b, inv_finish_fabric_issue_dtls c, order_wise_pro_details d 
					where b.id=c.trans_id and b.id=d.trans_id and b.status_active=1 and b.is_deleted=0 and b.item_category=2 and b.transaction_type=2 and c.status_active=1 and c.is_deleted=0
					$batch_id_cond3
					group by b.pi_wo_batch_no, d.color_id";// and b.pi_wo_batch_no='11649'
					// echo $fin_fab_issue_sql;
					$fin_fab_issue_result=sql_select($fin_fab_issue_sql);
					$fin_fab_issue_data_arr=array();
					foreach ($fin_fab_issue_result as $key => $row)
					{
						$fin_fab_issue_data_arr[$row[csf('batch_id')]][$row[csf('color_id')]]['issue_qnty']+=$row[csf('issue_qnty')];
					}
				}
				?>
				<table width="930" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" style="float:left; margin-top: 10px;">
					<thead>
						<tr>
							<th colspan="12">Finish Fabric Store Info</th>
						</tr>
						<tr>
							<th width="70">Colour</th>
							<th width="100">Construction</th>
							<th width="70">Batch No</th>
							<th width="30">Ext</th>
							<th width="80">Batch Qty</th>
							<th width="110">Rcvd Store Name</th>
							<th width="80">Received Qty</th>
							<th width="80">Received Balance</th>
							<th width="80">Transferred Qty</th>
							<th width="80">Issue to Cutting</th>
							<th width="80">Yet To Issue</th>
							<th>Stock/Left Over Qty</th>
						</tr>
					</thead>
				</table>
				
				<div style="width:935px; float:left; max-height:300px; overflow-y:scroll" id="scroll_body8">
				<table width="917" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_body_show8" style="float:left;">
					<tbody>
					<?
					$i=1;
					foreach ($finish_process_data_arr as $batch_id_key => $bactch_id_val)
					{
						foreach ($bactch_id_val as $color_id_key => $batch_color_id) 
						{
							foreach ($batch_color_id as $extention_key => $row) 
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
								$receive_qty=$receive_data_arr[$batch_id_key][$color_id_key]['receiveQnty'];
								$received_balance=$row['batch_qnty']-$receive_qty;
								$store_name=$store_array[$receive_data_arr[$batch_id_key][$color_id_key]['storeId']];
								$transf_out_qty=$fin_fab_tranf_data_arr[$batch_id_key][$color_id_key]['trans_out_qnty'];
								$issue_qty=$fin_fab_issue_data_arr[$batch_id_key][$color_id_key]['issue_qnty'];
								$yet_to_issue=$row['batch_qnty']-$issue_qty;
								$left_over_qty=$receive_qty-$issue_qty;
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_9nd<? echo $i; ?>','<? echo $bgcolor; ?>');" id="tr_9nd<? echo $i; ?>">
									<td width="70"><p><? echo $color_arr[$color_id_key]; ?></p></td>
									<td width="100" align="center"><p><? echo $unique_cons_var; ?></p></td>
									<td width="70" align="center"><p><? echo $row['batchNo']; ?></p></td>
									<td width="30" align="center"><p><? echo $extention_key; ?></p></td>
									<td width="80" align="right" ><p><? echo number_format($row['batch_qnty'],2); ?></p></td>
									<td width="110" align="center"><p><? echo $store_name; ?></p></td> 
									<td width="80" align="right" ><p><? echo number_format($receive_qty,2); ?></p></td>
									<td width="80" align="right"><p><? echo number_format($received_balance,2); ?></p></td>
									<td width="80" align="right"><p><? echo number_format($transf_out_qty,2); ?></p></td>
									<td width="80" align="right"><p><? echo number_format($issue_qty,2); ?></p></td>
									<td width="80" align="right"><p><? echo number_format($yet_to_issue,2); ?></p></td>
									<td class="breakAll" align="right"><p><? echo number_format($left_over_qty,2); ?></p></td>
								</tr>
								<?
								$total_batch_qnty+=$row['batch_qnty'];
								$total_receive_qty+=$receive_qty;
								$total_received_balance+=$received_balance;
								$total_transf_out_qty+=$transf_out_qty;
								$total_issue_qty+=$issue_qty;
								$total_yet_to_issue+=$yet_to_issue;
								$total_left_over_qty+=$left_over_qty;
								$i++;
							}
						}						
					}
					?>
					</tbody>
				</table>
				<table width="917" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="report_table_footer" align="left">
					<tfoot>
						<tr style="background-color:#CCCCCC;">
							<th width="70"></th>
							<th width="100"></th>
							<th width="70"></th>
							<th width="30" align="right">Total</th>
							<th width="80" align="right"><? echo number_format($total_batch_qnty,2); ?></th>
							<th width="110"></th>
							<th width="80" align="right"><? echo number_format($total_receive_qty,2); ?></th>
							<th width="80" align="right"><? echo number_format($total_received_balance,2); ?></th>
							<th width="80" align="right"><? echo number_format($total_transf_out_qty,2); ?></th>
							<th width="80" align="right"><? echo number_format($total_issue_qty,2); ?></th>
							<th width="80" align="right"><? echo number_format($total_yet_to_issue,2); ?></th>
							<th align="right"><? echo number_format($total_left_over_qty,2); ?></th>					
						</tr>
					</tfoot>
				</table>	
				</div>
				<!-- Finish Fabric Store Info End-->
			</div>
		</fieldset>
		<?
	}
	if($report_format==1) // Booking Wise Button
	{
		?>
		<fieldset style="width:1400px;">
			<table width="1400">
				<tr>
					<td align="center" width="100%" colspan="12" class="form_caption" style="font-size:18px;">Booking And Plan Wise Fabric Status Report</td>
				</tr>
				<tr>
					<td align="center" width="100%" colspan="12" class="form_caption"><? echo $company_library[str_replace("'","",$cbo_company_name)]; ?></td>
				</tr>
			</table>
			<?
				$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
				$party_library = return_library_array("select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=20 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id", "supplier_name");
				$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
				$style_library = return_library_array("select job_no, style_ref_no from wo_po_details_master", "job_no", "style_ref_no");
				$construction_arr = return_library_array("select id, construction from lib_yarn_count_determina_mst", "id", "construction");
				$store_array=return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');
				$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
			?>
			<style>
				.breakAll{
					word-break:break-all;
					word-wrap: break-word;
				}
				.inline { 
				    display: inline-block; 
				}
			</style>

			<!-- Program  Info and Color Type Summery Start -->
			<div> 
				<!-- Program  Info Start -->
				<div class='inline' style="width: 990px; float:left;"">
			    	<div style="width: 990px;">
			    		<!-- Booking and Program Info Start-->			    					  	
						<?
						$program_info_sql="SELECT a.id as program_no, a.knitting_source, a.knitting_party, a.color_id, b.booking_no, a.program_qnty, b.determination_id, b.body_part_id, b.color_type_id, b.yarn_desc as pre_cost_dtls_id, b.buyer_id 
						from ppl_planning_info_entry_dtls a, ppl_planning_entry_plan_dtls b 
						where a.mst_id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $str_cond_date $knit_comp_cond $program_cond $booking_cond $company_name_cond
						group by a.id, a.knitting_source, a.knitting_party, a.color_id, b.booking_no, b.determination_id, b.body_part_id, b.color_type_id, b.yarn_desc, b.buyer_id, a.program_qnty order by b.determination_id";
						// echo $program_info_sql;die;
						$program_info_result=sql_select($program_info_sql);
						$program_arr=array(); $booking_no_arr=array(); 
						//$plan_qty_inhouse_arr=array();$plan_qty_outside_arr=array();
						$program_qty_arr=array();$program_data_arr=array();
						$plan_qty_inhouse_arr2=array(); $plan_qty_outside_arr2=array();
						foreach ($program_info_result as $key => $value) 
						{
							$program_data_arr[$value[csf('program_no')]]['programNo']=$value[csf('program_no')];
							$program_data_arr[$value[csf('program_no')]]['programQnty']=$value[csf('program_qnty')];
							$program_data_arr[$value[csf('program_no')]]['buyer_id']=$value[csf('buyer_id')];
							$program_data_arr[$value[csf('program_no')]]['booking_no']=$value[csf('booking_no')];
							$program_data_arr[$value[csf('program_no')]]['color_id']=$value[csf('color_id')];
							$program_data_arr[$value[csf('program_no')]]['color_type_id']=$value[csf('color_type_id')];
							$program_data_arr[$value[csf('program_no')]]['pre_cost_dtls_id']=$value[csf('pre_cost_dtls_id')];
							$program_data_arr[$value[csf('program_no')]]['knitting_source']=$value[csf('knitting_source')];
							$program_data_arr[$value[csf('program_no')]]['knitting_party']=$value[csf('knitting_party')];
							$program_data_arr[$value[csf('program_no')]]['determination_id']=$value[csf('determination_id')];

							$program_arr[$value[csf('program_no')]]=$value[csf('program_no')];
							$program_qty_arr[$value[csf('program_no')]]=$value[csf('program_qnty')];
							$booking_no_arr[$value[csf('booking_no')]]=$value[csf('booking_no')];
							if ($value[csf('knitting_source')]==1) 
							{
								//$plan_qty_inhouse_arr[$value[csf('program_no')]]['plan_qty_inhouse']+=$value[csf('program_qnty')];

								$plan_qty_inhouse_arr2[$value[csf('determination_id')]]['plan_qty_inhouse']+=$value[csf('program_qnty')];
								$company_party=$company_library[$row[csf("knitting_party")]];
							}
							else
							{
								//$plan_qty_outside_arr[$value[csf('program_no')]]['plan_qty_outside']+=$value[csf('program_qnty')];
								$plan_qty_outside_arr2[$value[csf('determination_id')]]['plan_qty_outside']+=$value[csf('program_qnty')];

								$company_party=$row[csf("knitting_party")];
							}
							$plan_qty_in_prod[$value[csf('determination_id')]]['plan_qty_prod']+=$value[csf('program_qnty')];
						}				
						// echo "<pre>";print_r($program_qty_arr);die;
						$booking_no_cond=implode(",", array_unique($booking_no_arr));
						if ($booking_no_arr!="") 
						{
							$booking_info_sql="SELECT b.booking_no, b.color_type, b.fabric_color_id, b.pre_cost_fabric_cost_dtls_id as pre_cost_dtls_id, b.process_loss_percent, b.grey_fab_qnty as booking_qty, b.fin_fab_qnty, b.po_break_down_id, b.job_no, b.construction
							from wo_booking_dtls b where b.booking_no='$booking_no_cond' and b.status_active=1 and b.is_deleted=0";
							//echo $booking_info_sql;
							$booking_info_result=sql_select($booking_info_sql);
							$process_loss_arr=array();$booking_qty_arr=array();$job_no_arr=array();
							$color_type_summary_arr=array();
							foreach ($booking_info_result as $key => $value) 
							{
								$process_loss_arr[$value[csf('booking_no')]][$value[csf('color_type')]][$value[csf('fabric_color_id')]][$value[csf('pre_cost_dtls_id')]]=$value[csf('process_loss_percent')];

								$booking_qty_arr[$value[csf('booking_no')]][$value[csf('fabric_color_id')]]+=$value[csf('booking_qty')];
								$job_no_arr[$value[csf('booking_no')]]=$value[csf('job_no')];

								
								$color_type_summary_arr[$value[csf('booking_no')]][$value[csf('color_type')]][$value[csf('construction')]]['construction']=$value[csf('construction')];
								$color_type_summary_arr[$value[csf('booking_no')]][$value[csf('color_type')]][$value[csf('construction')]]['booking_qty']+=$value[csf('booking_qty')];
								$color_type_summary_arr[$value[csf('booking_no')]][$value[csf('color_type')]][$value[csf('construction')]]['fin_fab_qnty']+=$value[csf('fin_fab_qnty')];
							}

							//echo "<pre>";print_r($process_loss_arr);die;
							// echo "<pre>";print_r($booking_qty_arr);die;
						}
						?>
						<table width="990" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" style="float:left;">
							<thead>
								<tr>
									<th colspan="11">Booking and Program Info</th>
								</tr>
								<tr>
									<th width="90">Buyer</th>
									<th width="80">Job No</th>
									<th width="80">Style</th>
									<th width="100">Booking No</th>
									<th width="70">Booking Qty</th>
									<th width="70">Prog Grey Qty</th>
									<th width="70">Prog Fin Qty</th>
									<th width="100">Prog Company</th>
									<th width="70">Program No</th>
									<th width="100">Color</th>
									<th>Fabric</th>
								</tr>
							</thead>
						</table>
						<div style="width:995px; float:left; max-height:300px; overflow-y:scroll" id="scroll_body">
						<table width="977" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_body_show" style="float:left;">
							<tbody>
							<?
							$i=1;
							$total_booking_qty=$total_program_qty=$total_process_loss_qty=0;
							foreach($program_data_arr as $row)
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

								$process_loss=$process_loss_arr[$row['booking_no']][$row['color_type_id']][$row['color_id']][$row['pre_cost_dtls_id']];
								$booking_qty=$booking_qty_arr[$row['booking_no']][$row['color_id']];
								$job_no=$job_no_arr[$row['booking_no']];
								?>						
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>');" id="tr_1nd<? echo $i; ?>">
									<td width="90"><p><? echo $buyer_library[$row["buyer_id"]]; ?></p></td>
									<td width="80" align="center"><p><? echo $job_no; ?></p></td>
									<td width="80" align="center"><p><? echo $style_library[$job_no]; ?></p></td>
									<td width="100" align="center" ><p><? echo $row["booking_no"]; ?></p></td>
									<td width="70" align="right" title="Booking qty, Booking No:<? echo $row['booking_no'].',Color:'.$row['color_id']; ?>"><p><? echo number_format($booking_qty,2); ?></p></td> 
									<td width="70" align="right" title="Praogram qty"><p><? echo number_format($row['programQnty'],2);
									?></p></td>
									<td width="70" align="right" title="Grey Qty-(Grey Qty*process_loss)/100"><p><? 
									echo number_format($row["programQnty"]-($row["programQnty"]*$process_loss)/100,2); ?></p></td>
									<td width="100"><p><? echo $company_party=($row['knitting_source']==1) ? $company_library[$row["knitting_party"]] : $party_library[$row["knitting_party"]]; ?></p></td>
									<td width="70" align="center"><p><? echo $row['programNo']; ?></p></td>
									<td width="100" align="center"><p><? echo $color_arr[$row['color_id']]; ?></p></td>
									<td class="breakAll"><p><? echo $construction_arr[$row["determination_id"]]; ?></p></td>
								</tr>
								<?
								$total_booking_qty+=$booking_qty;
								$total_program_qty+=$row['programQnty'];
								$total_process_loss_qty+=$row["programQnty"]-($row["programQnty"]*$process_loss)/100;
								$i++;
							}
							?>
							</tbody>
						</table>
						</div>
						<table width="977" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="report_table_footer" align="left">
							<tfoot>
								<tr style="background-color:#CCCCCC;">
									<th width="90"></th>
									<th width="80"></th>
									<th width="80"></th>
									<th width="100" align="right"></th>
									<th width="70" align="right"> Total :<?// echo number_format($total_booking_qty,2); ?></th>
									<th width="70" align="right"><? echo number_format($total_program_qty,2); ?></th>
									<th width="70" align="right"><? echo number_format($total_process_loss_qty,2) ?></th>
									<th width="100"></th>
									<th width="70"></th>
									<th width="100"></th>
									<th></th>
								</tr>
							</tfoot>
						</table>												
						<!-- Booking and Program Info End-->
			    	</div>
				</div>
			    <!-- Program  Info End -->

				<!-- Summary Start -->
			    <div class='inline' style="width: 330px; float: left; margin-left: 20px;">
			    	<div style="width: 330px;">
			    		<!-- Summary Start-->
						<table width="330" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" style="float:left;">
							<thead>
								<tr>
									<th colspan="4">Color Type Summery (Booking)</th>
								</tr>
								<tr>
									<th width="90">Color Type</th>
									<th width="80">Fabrication</th>
									<th width="80">Grey Qty</th>
									<th>Finish Qty</th>
								</tr>
							</thead>
						</table>

						<div style="width:335px; float:left; max-height:300px; overflow-y:scroll" id="scroll_body_summary">
						<table width="317" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_body_show" style="float:left;">
							<tbody>
							<?
							$i=1;$total_booking_qty=$total_fin_fab_qnty=0;
							foreach($color_type_summary_arr as $booking_no => $booking_no_arr)
							{
								foreach ($booking_no_arr as $color_type_id => $color_type_val) 
								{
									foreach ($color_type_val as $construction_ids => $row) 
									{
										if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
										?>						
										<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>');" id="tr_2nd<? echo $i; ?>">
											<td width="90"><p><? echo $color_type[$color_type_id]; ?></p></td>
											<td width="80" align="center"><p><? echo $row['construction']; ?></p></td>
											<td width="80" align="right"><p><? echo number_format($row['booking_qty'],2); ?></p></td>
											<td class="breakAll" align="right"><p><? echo number_format($row['fin_fab_qnty'],2); ?></p></td>
										</tr>
										<?
										$total_booking_qty+=$row["booking_qty"];
										$total_fin_fab_qnty+=$row["fin_fab_qnty"];
										$i++;
									}									
								}								
							}
							?>
							</tbody>
						</table>
						</div>
						<table width="317" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="report_table_footer" align="left">
							<tfoot>
								<tr style="background-color:#CCCCCC;">
									<th width="90"></th>
									<th width="80" align="right">Total</th>
									<th width="80" align="right"><? echo number_format($total_booking_qty,2); ?></th>
									<th align="right"><? echo number_format($total_fin_fab_qnty,2); ?></th>
								</tr>
							</tfoot>
						</table>
						<!-- Summary End-->
			    	</div>
			    </div>
			    <!-- Summary End -->
			</div>
			<!-- Program  Info and Color Type Summery End -->
			
			<div style="width:995px; float:left;">
				<!-- Yarn Info Start-->
				<?
				$all_program_no_arr = array_filter($program_arr);
				if(count($all_program_no_arr) > 0)
				{
					$all_program_no_cond_id = implode(",", $all_program_no_arr);
					$programCond = $program_no_cond = "";
					if($db_type==2 && count($all_program_no_arr)>999)
					{
						$all_program_no_cond_id_chunk=array_chunk($all_program_no_arr, 999);
						foreach($all_program_no_cond_id_chunk as $chunk_prog)
						{
							$chunk_prog_val=implode(",", $chunk_prog);
							if(!$program_no_cond)$program_no_cond.=" and ( a.dtls_id in($chunk_prog_val) ";
							else $program_no_cond.=" or a.dtls_id in($chunk_prog_val) ";
						}
						$program_no_cond.=")";
					}
					else
					{
						$program_no_cond=" and a.dtls_id in($all_program_no_cond_id)";
					}
					//ppl_planning_entry_plan_dtls a, ppl_yarn_requisition_entry b
					$yarn_requisition_sql="SELECT sum(b.yarn_qnty) as requisition_qty, b.knit_id as program_no, b.requisition_no, a.determination_id
					from ppl_planning_entry_plan_dtls a, ppl_yarn_requisition_entry b
					where a.dtls_id=b.knit_id $program_no_cond and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0
					group by b.knit_id, b.requisition_no, a.determination_id";
					//echo $yarn_requisition_sql; // and a.dtls_id in(101533)
					$yarn_requisition_result=sql_select($yarn_requisition_sql);
					$yarn_requisition_data_arr=array();$requisition_no_arr=array();
					foreach ($yarn_requisition_result as $key => $value) 
					{
						$yarn_requisition_data_arr[$value[csf("determination_id")]]["determination_id"]=$value[csf("determination_id")];
						$yarn_requisition_data_arr[$value[csf("determination_id")]]["program_no"]=$value[csf("program_no")];
						$yarn_requisition_data_arr[$value[csf("determination_id")]]["requisition_qty"]+=$value[csf("requisition_qty")];
						$yarn_requisition_data_arr[$value[csf("determination_id")]]["requisition_no"].=$value[csf("requisition_no")].',';
						$requisition_no_arr[$value[csf("requisition_no")]]=$value[csf("requisition_no")];
					}
					// echo "<pre>";print_r($yarn_requisition_data_arr);
					$all_requisition_arr = array_filter($requisition_no_arr);
					if(count($all_requisition_arr) > 0)
					{
						$all_requisition_no = implode(",", $all_requisition_arr);
						$requisition_no_cond = "";
						if($db_type==2 && count($all_requisition_arr)>999)
						{
							$all_requisition_no_cond_chunk=array_chunk($all_requisition_arr, 999);
							foreach($all_requisition_no_cond_chunk as $requ_no)
							{
								$chunk_requ_no=implode(",", $requ_no);
								if(!$requisition_no_cond)$requisition_no_cond.=" and ( b.requisition_no in($chunk_requ_no) ";
								$requisition_no_cond.=" or  b.requisition_no in($chunk_requ_no) ";
							}
							$requisition_no_cond.=")";
						}
						else
						{
							$requisition_no_cond=" and b.requisition_no in($all_requisition_no)";
						}
					}
					//echo $requisition_no_cond; die;
					
					//inv_transaction c, inv_issue_master d
					/*$yarn_issue_sql="SELECT c.requisition_no, sum(c.cons_quantity) as issue_qty, d.id, d.knit_dye_source 
					from inv_transaction c, inv_issue_master d
					where c.transaction_type=2 and c.mst_id=d.id and c.receive_basis=3 and d.issue_basis=3 and d.item_category=1 and d.entry_form=3 $program_no_cond and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.dtls_id in(101533)
					group by c.requisition_no, d.id, d.knit_dye_source";
					echo $yarn_issue_sql;
					$yarn_issue_result=sql_select($yarn_issue_sql);
					$yarn_issue_data_arr=array();$issue_id_arr=array();
					$yarn_issue_inhouse_data_arr=array();$yarn_issue_qty_out_data_arr=array();
					foreach ($yarn_issue_result as $key => $value) 
					{
						$yarn_issue_data_arr[$value[csf("requisition_no")]]["id"]=$value[csf("id")];
						$yarn_issue_data_arr[$value[csf("requisition_no")]]["knit_dye_source"]=$value[csf("knit_dye_source")];
						if ($value[csf('knit_dye_source')]==1) 
						{
							$yarn_issue_inhouse_data_arr[$value[csf("requisition_no")]]["y_issue_inhouse_qty"]+=$value[csf("issue_qty")];
						}
						else
						{
							$yarn_issue_qty_out_data_arr[$value[csf("requisition_no")]]["y_issue_out_qty"]+=$value[csf("issue_qty")];
						}
						$issue_id_arr[$value[csf("id")]]=$value[csf("id")];
					}*/

					$yarn_info_sql="SELECT sum(b.yarn_qnty) as requisition_qty, b.knit_id as program_no, b.requisition_no, d.knit_dye_source, sum(c.cons_quantity) as issue_qty, a.determination_id, d.id 
					from ppl_planning_entry_plan_dtls a, ppl_yarn_requisition_entry b, inv_transaction c, inv_issue_master d
					where b.requisition_no=c.requisition_no and c.transaction_type=2 and a.dtls_id=b.knit_id and b.prod_id=c.prod_id and c.mst_id=d.id and c.receive_basis=3 and d.issue_basis=3 and d.item_category=1 and d.entry_form=3 $program_no_cond $requisition_no_cond and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0
					group by b.knit_id, b.requisition_no, d.knit_dye_source, a.determination_id, d.id";
					// echo $yarn_info_sql;  //and a.dtls_id in(101533)
					$yarn_info_result=sql_select($yarn_info_sql);
					$yarn_info_data_arr=array();$issue_id_arr=array();$yarn_issue_id_arr=array();
					$yarn_issue_inhouse_data_arr=array();$yarn_issue_qty_out_data_arr=array();
					foreach ($yarn_info_result as $key => $value) 
					{
						$yarn_info_data_arr[$value[csf("determination_id")]]["knit_dye_source"]=$value[csf("knit_dye_source")];
						if ($value[csf('knit_dye_source')]==1) 
						{
							$yarn_issue_inhouse_data_arr[$value[csf("determination_id")]]["y_issue_inhouse_qty"]+=$value[csf("issue_qty")];
						}
						else
						{
							$yarn_issue_qty_out_data_arr[$value[csf("determination_id")]]["y_issue_out_qty"]+=$value[csf("issue_qty")];
						}
						$yarn_issue_id_arr[$value[csf("determination_id")]]["id"]=$value[csf("id")];
						$issue_id_arr[$value[csf("id")]]=$value[csf("id")];
					}

					$all_issue_id_arr = array_filter($issue_id_arr);
					if(count($all_issue_id_arr) > 0)
					{
						$all_issue_ids = implode(",", $all_issue_id_arr);
						$issue_ids_cond = "";
						if($db_type==2 && count($all_issue_id_arr)>999)
						{
							$all_issue_id_cond_chunk=array_chunk($all_issue_id_arr, 999);
							foreach($all_issue_id_cond_chunk as $chunk_prog)
							{
								$chunk_prog_val=implode(",", $chunk_prog);
								if(!$issue_ids_cond)$issue_ids_cond.=" and ( a.issue_id in($chunk_prog_val) ";
								else $issue_ids_cond.=" or  a.issue_id in($chunk_prog_val) ";
							}
							$issue_ids_cond.=")";
						}
						else
						{
							$issue_ids_cond=" and a.issue_id in($all_issue_ids)";
						}

						$yarn_issue_rtn_sql="SELECT a.issue_id, sum(b.cons_quantity) as return_qty, a.booking_id from inv_receive_master a, inv_transaction b
						where a.id=b.mst_id and a.entry_form=9 and b.transaction_type=4 $issue_ids_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.issue_id, a.booking_id";
						// echo $yarn_issue_rtn_sql;
						$yarn_issue_rtn_result=sql_select($yarn_issue_rtn_sql);
						$yarn_issue_retn_arr=array();
						foreach ($yarn_issue_rtn_result as $key => $row) 
						{
							$yarn_issue_retn_arr[$row[csf("issue_id")]]+=$row[csf("return_qty")];
						}
						// echo "<pre>";print_r($yarn_issue_retn_arr);die;
					}
				}
				?>
				
				<table width="840" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" style="float:left; margin-top: 10px;">
					<thead>
						<tr>
							<th colspan="11">Yarn Info</th>
						</tr>
						<tr>
							<th width="100">Construction</th>
							<th width="80">Plan Qty Inhouse</th>
							<th width="80">Plan Qty Outside</th>
							<th width="80">Total Plan Qty</th>
							<th width="80">Requisition Qty</th>
							<th width="80">Yarn Issued Inside</th>
							<th width="80">Yarn Issued Outside</th>
							<th width="80">Total Yarn Issued</th>
							<th width="80">Yarn issue Returned</th>
							<th>Net Issue</th>
						</tr>
					</thead>
				</table>
				
				<div style="width:845px; float:left; max-height:300px; overflow-y:scroll" id="scroll_body2">
				<table width="827" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_body_show2" style="float:left;">
					<tbody>
					<?
					$i=1;
					$total_requisition_qty=$total_yarn_issued_inside=$total_yarn_issued_outside=$total_yarn_issued=$total_yarn_issue_retn_qty=$total_net_issue=$total_plan_qty_inhouse=$total_plan_qty_outside=$total_plan_qty=0;
					foreach ($yarn_requisition_data_arr as $determination_id_key => $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";		
						$plan_qty_inhouse=$plan_qty_inhouse_arr2[$determination_id_key]['plan_qty_inhouse'];
						$plan_qty_outside=$plan_qty_outside_arr2[$determination_id_key]['plan_qty_outside'];
						$yarn_issued_inside=$yarn_issue_inhouse_data_arr[$row["determination_id"]]['y_issue_inhouse_qty'];
						$yarn_issued_outside=$yarn_issue_qty_out_data_arr[$row["determination_id"]]['y_issue_out_qty'];
						// $yarn_issue_retn_qty=$yarn_issue_retn_arr[$row["id"]];
						$yarn_issue_retn_qty=$yarn_issue_retn_arr[$yarn_issue_id_arr[$row["determination_id"]]['id']];
						?>						
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_3nd<? echo $i; ?>','<? echo $bgcolor; ?>');" id="tr_3nd<? echo $i; ?>">
							<td width="100" title="<? echo $determination_id_key; ?>"><? echo $construction_arr[$determination_id_key]; ?></td>
							<td width="80" align="right"><p><? echo number_format($plan_qty_inhouse,2); ?></p></td>
							<td width="80" align="right" ><p><? echo number_format($plan_qty_outside,2); ?></p></td>
							<td width="80" align="right"><p><? echo number_format($plan_qty_inhouse+$plan_qty_outside,2); ?></p></td> 
							<td width="80" align="right" title="<? echo $row["requisition_no"]; ?>"><p><? echo number_format($row["requisition_qty"],2); ?></p></td>
							<td width="80" align="right"><p><? echo number_format($yarn_issued_inside,2); ?></p></td>
							<td width="80" align="right"><p><? echo number_format($yarn_issued_outside,2); ?></p></td>
							<td width="80" align="right"><p><? echo number_format($yarn_issued_inside+$yarn_issued_outside,2); ?></p></td>
							<td width="80" align="right" title="<? echo 'issue_id:'.$yarn_issue_id_arr[$row["determination_id"]]['id']; ?>"><p><? echo number_format($yarn_issue_retn_qty,2); ?></p></td>
							<td class="breakAll" align="right"><p><? 
							$net_issue=($yarn_issued_inside+$yarn_issued_outside)-$yarn_issue_retn_qty;
							echo number_format($net_issue,2); ?></p></td>
						</tr>
						<?						
						$total_plan_qty_inhouse+=$plan_qty_inhouse;
						$total_plan_qty_outside+=$plan_qty_outside;
						$total_plan_qty+=$plan_qty_inhouse+$plan_qty_outside;
						$total_requisition_qty+=$row["requisition_qty"];
						$total_yarn_issued_inside+=$yarn_issued_inside;
						$total_yarn_issued_outside+=$yarn_issued_outside;
						$total_yarn_issued+=$yarn_issued_inside+$yarn_issued_outside;
						$total_yarn_issue_retn_qty+=$yarn_issue_retn_qty;
						$total_net_issue+=$net_issue;
						$i++;
					}
					?>
					</tbody>
				</table>
				</div>
				<table width="827" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="report_table_footer" align="left">
					<tfoot>
						<tr style="background-color:#CCCCCC;">
							<th width="100" align="right">Total</th>
							<th width="80" align="right"><? echo number_format($total_plan_qty_inhouse,2) ?></th>
							<th width="80" align="right"><? echo number_format($total_plan_qty_outside,2) ?></th>
							<th width="80" align="right"><? echo number_format($total_plan_qty,2) ?></th>
							<th width="80" align="right"><? echo number_format($total_requisition_qty,2) ?></th>
							<th width="80" align="right"><? echo number_format($total_yarn_issued_inside,2) ?></th>
							<th width="80" align="right"><? echo number_format($total_yarn_issued_outside,2) ?></th>
							<th width="80" align="right"><? echo number_format($total_yarn_issued,2) ?></th>
							<th width="80" align="right"><? echo number_format($total_yarn_issue_retn_qty,2); ?></th>
							<th align="right"><? echo number_format($total_net_issue,2); ?></th>	
						</tr>
					</tfoot>
				</table>	
				
				<!-- Yarn Info End-->

				<!-- Knitting Production Info Start-->
				<?
				$all_program_no_arr = array_filter($program_arr);
				if(count($all_program_no_arr) > 0)
				{
					$all_program_no_cond_id = implode(",", $all_program_no_arr);
					$programCond = $program_booking_cond = "";
					if($db_type==2 && count($all_program_no_arr)>999)
					{
						$all_program_no_cond_id_chunk=array_chunk($all_program_no_arr, 999);
						foreach($all_program_no_cond_id_chunk as $chunk_prog)
						{
							$chunk_prog_val=implode(",", $chunk_prog);
							if(!$program_booking_cond)$program_booking_cond.=" and ( a.booking_id in($chunk_prog_val) ";
							else $program_booking_cond.=" or  a.booking_id in($chunk_prog_val) ";
						}
						$program_booking_cond.=")";
					}
					else
					{
						$program_booking_cond=" and a.booking_id in($all_program_no_cond_id)";
					}
					$production_info_sql="SELECT a.id, a.knitting_source, a.booking_id as program_no, b.prod_id, sum(c.qnty) as production_qty, c.barcode_no, d.detarmination_id, e.construction
					from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, product_details_master d, lib_yarn_count_determina_mst e
					where a.id=b.mst_id and a.id=c.mst_id and b.id=c.dtls_id and b.prod_id=d.id and d.detarmination_id=e.id $program_booking_cond and a.entry_form=2 and a.receive_basis=2
					group by a.id, a.knitting_source, a.booking_id, b.prod_id, c.barcode_no, d.detarmination_id, e.construction";
					// echo $production_info_sql;die; // and a.booking_id in(9600)
					$production_info_result=sql_select($production_info_sql);
					$production_arr=array();$production_barcode_arr=array();
					foreach ($production_info_result as $key => $value) 
					{
						$production_arr[$value[csf("construction")]]["id"].=$value[csf("id")].',';
						$production_arr[$value[csf("construction")]]["knitting_source"]=$value[csf("knitting_source")];
						$production_arr[$value[csf("construction")]]["program_no"].=$value[csf("program_no")].',';
						$production_arr[$value[csf("construction")]]["production_qty"]+=$value[csf("production_qty")];
						$production_arr[$value[csf("construction")]]["detarmination"]=$value[csf("detarmination_id")];
						$production_arr[$value[csf("construction")]]["prod_id"]=$value[csf("prod_id")];
						$production_arr[$value[csf("construction")]]["construction"]=$value[csf("construction")];

						$production_barcode_arr[$value[csf("barcode_no")]]=$value[csf("barcode_no")];
						$production_id_arr[$value[csf("id")]]=$value[csf("id")];
						$prod_id_arr[$value[csf("prod_id")]]=$value[csf("prod_id")];
					}
					//echo "<pre>";print_r($production_arr);die;
					$all_prod_ids= array_chunk($prod_id_arr, 999);
					$all_prod_ids_cond=" and(";
					foreach($all_prod_ids as $prod_ids)
					{
						if($all_prod_ids_cond==" and(") $all_prod_ids_cond.=" a.id in(". implode(',', $prod_ids).")"; else $all_prod_ids_cond.="  or a.id in(". implode(',', $prod_ids).")";
					}
					$all_prod_ids_cond.=")";
					$fabric_desc_arr = return_library_array("select a.id, a.item_description from product_details_master a where a.item_category_id=13 $all_prod_ids_cond", "id", "item_description");

					$all_production_id_arr = array_filter($production_id_arr);
					if(count($all_production_id_arr) > 0)
					{
						$all_production_ids = implode(",", $all_production_id_arr);
						$production_ids_cond = "";
						if($db_type==2 && count($all_production_id_arr)>999)
						{
							$all_issue_id_cond_chunk=array_chunk($all_production_id_arr, 999);
							foreach($all_issue_id_cond_chunk as $chunk_prog)
							{
								$chunk_prog_val=implode(",", $chunk_prog);
								if(!$production_ids_cond)$production_ids_cond.=" and ( a.booking_id in($chunk_prog_val) ";
								else $production_ids_cond.=" or  a.booking_id in($chunk_prog_val) ";
							}
							$production_ids_cond.=")";
						}
						else
						{
							$production_ids_cond=" and b.grey_sys_id in($all_production_ids)";
						}

						/*$delivery_sql="SELECT a.id as delivery_id, b.grey_sys_id, b.product_id, sum(b.current_delivery) as delivery_qty
						from pro_grey_prod_delivery_mst a, pro_grey_prod_delivery_dtls b
						where a.id=b.mst_id $production_ids_cond and a.entry_form=56 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
						group by a.id, b.grey_sys_id, b.product_id";*/

						$delivery_sql="SELECT a.id as delivery_id, b.grey_sys_id, b.product_id, sum(b.current_delivery) as delivery_qty, e.construction
						from pro_grey_prod_delivery_mst a, pro_grey_prod_delivery_dtls b, product_details_master d, lib_yarn_count_determina_mst e
						where a.id=b.mst_id  and b.product_id=d.id and d.detarmination_id=e.id  and d.detarmination_id=e.id $production_ids_cond 
						and a.entry_form=56 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
						group by a.id, b.grey_sys_id, b.product_id, e.construction";
						//echo $delivery_sql;
						$delivery_sql_result=sql_select($delivery_sql);
						$delivery_qty_arr=array();
						foreach ($delivery_sql_result as $key => $row) 
						{
							$delivery_qty_arr[$row[csf("construction")]]+=$row[csf("delivery_qty")];
						}
						// echo "<pre>";print_r($delivery_qty_arr);die;
					}
				}
				?>
				<table width="685" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" style="float:left; margin-top: 10px;">
					<thead>
						<tr>
							<th colspan="8">Knitting Production Info</th>
						</tr>
						<tr>
							<th width="100">Construction</th>
							<th width="80">Program Qty</th>
							<th width="80">Knitting Qty inhouse</th>
							<th width="80">Knitting Qty Outside</th>
							<th width="80">Total Knit Qty</th>
							<th width="80">Knit. Balance</th>
							<th width="80">Knit. Del. To Store Qty</th>
							<th>Delivery Balance</th>
						</tr>
					</thead>
				</table>
				
				<div style="width:685px; float:left; max-height:300px; overflow-y:scroll" id="scroll_body3">
				<table width="667" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_body_show3" style="float:left;">
					<tbody>
					<?
					$i=1;
					$total_production_inside_qty=$total_production_outside_qty=$total_all_knit_qty=$total_knit_balance=$total_knit_del_to_store_qty=$total_delivery_balance_qty=$total_prod_plan_qty=0;
					foreach($production_arr as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
						if($row["knitting_source"]==1)
						{
							$production_inside_qty=$row["production_qty"]; 
						}
						else
						{ 
							$production_outside_qty=$row["production_qty"]; 
						}
						$total_knit_qty=$production_inside_qty+$production_outside_qty;
						$fabric_desc=$fabric_desc_arr[$row["prod_id"]];
						$construction=explode(",", $fabric_desc);
						//$program_qty=number_format($program_qty_arr[$row["program_no"]],2);

						$program_no=chop($row["program_no"],',');
						$all_program_no_arr=array_unique(explode(',', $program_no));
						//echo "<pre>";print_r($all_program_no_arr);
						$prod_program_qty=0;
						foreach ($all_program_no_arr as $key => $program) 
						{
							$prod_program_qty+=$program_qty_arr[$program];
							//echo $program.'<br>';
						}
						?>						
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_4nd<? echo $i; ?>','<? echo $bgcolor; ?>');" id="tr_4nd<? echo $i; ?>">
							<td width="100" title="<? echo $row["detarmination"]; ?>"><p><? 
							echo $row["construction"]; //$construction[0]; ?></p></td>
							<td width="80" align="right"><p><? echo number_format($plan_qty_in_prod[$row["detarmination"]]['plan_qty_prod'],2); ?></p></td>
							<td width="80" align="right"><p><? echo number_format($production_inside_qty,2); ?></p></td>
							<td width="80" align="right" ><p><? echo number_format($production_outside_qty,2); ?></p></td>
							<td width="80" align="right"><p><? echo number_format($total_knit_qty,2); ?></p></td> 
							<td width="80" align="right" ><p><? echo number_format($plan_qty_in_prod[$row["detarmination"]]['plan_qty_prod']-$production_inside_qty,2); ?></p></td>
							<td width="80" align="right" title="<? echo $row["id"]; ?>"><p><? echo number_format($delivery_qty_arr[$row["construction"]],2); ?></p></td>
							<td class="breakAll" align="right"><p><? echo number_format($total_knit_qty-$delivery_qty_arr[$row["construction"]],2); ?></p></td>
						</tr>
						<?
						$total_prod_plan_qty+=$plan_qty_in_prod[$row["detarmination"]]['plan_qty_prod'];
						$total_production_inside_qty+=$production_inside_qty;
						$total_production_outside_qty+=$production_outside_qty;
						$total_all_knit_qty+=$total_knit_qty;
						$total_knit_balance+=$plan_qty_in_prod[$row["detarmination"]]['plan_qty_prod']-$production_inside_qty;
						$total_knit_del_to_store_qty+=$delivery_qty_arr[$row["construction"]];
						$total_delivery_balance_qty+=$total_knit_qty-$delivery_qty_arr[$row["construction"]];
						$i++;
					}
					?>
					</tbody>
				</table>
				</div>
				<table width="667" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="report_table_footer" align="left">
					<tfoot>
						<tr style="background-color:#CCCCCC;">
							<th width="100" align="right">Total</th>
							<th width="80" align="right"><? echo number_format($total_prod_plan_qty,2); ?></th>
							<th width="80" align="right"><? echo number_format($total_production_inside_qty,2); ?></th>
							<th width="80" align="right"><? echo number_format($total_production_outside_qty,2); ?></th>
							<th width="80" align="right"><? echo number_format($total_all_knit_qty,2); ?></th>
							<th width="80" align="right"><? echo number_format($total_knit_balance,2); ?></th>
							<th width="80" align="right"><? echo number_format($total_knit_del_to_store_qty,2); ?></th>
							<th align="right"><? echo number_format($total_delivery_balance_qty,2); ?></th>
						</tr>
					</tfoot>
				</table>	
				
				<!-- Knitting Production Info End-->

				<!-- Grey Fabric Store Info Start-->
				<?
				$all_production_barcode_arr = array_filter($production_barcode_arr);
				if(count($all_production_barcode_arr) > 0)
				{
					$all_barcode_no = implode(",", $all_production_barcode_arr);
					$barcode_no_Cond = $barcode_no_cond = "";
					if($db_type==2 && count($all_production_barcode_arr)>999)
					{
						$all_production_barcode_chunk=array_chunk($all_production_barcode_arr, 999);
						foreach($all_production_barcode_chunk as $chunk_barcode)
						{
							$chunk_barcode_val=implode(",", $chunk_barcode);
							if(!$barcode_no_cond)$barcode_no_cond.=" and ( c.barcode_no in($chunk_barcode_val) ";
							else $barcode_no_cond.=" or  c.barcode_no in($chunk_barcode_val) ";
						}
						$barcode_no_cond.=")";
					}
					else
					{
						$barcode_no_cond=" and c.barcode_no in($all_barcode_no)";
					}

					// only main query is Knit Grey Fabric Roll Receive
					$grey_fabric_recv_info_sql="SELECT a.store_id,a.knitting_source, b.prod_id, c.qnty, c.booking_no as program_no, c.barcode_no, c.po_breakdown_id, d.determination_id,
					case when a.knitting_source=1 then c.qnty else 0 end as in_hous_qnty, case when a.knitting_source=3 then c.qnty else 0 end as out_bound_qnty
					from inv_receive_master a, inv_transaction b, pro_roll_details c, ppl_planning_entry_plan_dtls d
					where a.id=b.mst_id and b.pi_wo_batch_no=c.dtls_id and c.booking_no=d.dtls_id and a.entry_form=58 and c.entry_form=58  and b.transaction_type=1 and b.booking_without_order=0 and c.booking_without_order=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $barcode_no_cond";
					//echo $grey_fabric_recv_info_sql;die;
					$grey_fabric_recv_info_result=sql_select($grey_fabric_recv_info_sql);
					$grey_fabric_recv_data_arr=array();$recv_barcode_no_arr=array();
					foreach ($grey_fabric_recv_info_result as $key => $row) 
					{
						$grey_fabric_recv_data_arr[$row[csf('determination_id')]]['knitting_source']=$row[csf('knitting_source')];
						$grey_fabric_recv_data_arr[$row[csf('determination_id')]]['prod_id']=$row[csf('prod_id')];
						$grey_fabric_recv_data_arr[$row[csf('determination_id')]]['determination_id']=$row[csf('determination_id')];
						$grey_fabric_recv_data_arr[$row[csf('determination_id')]]['programNo'].=$row[csf('program_no')].',';
						$grey_fabric_recv_data_arr[$row[csf('determination_id')]]['recv_qnty']+=$row[csf('qnty')];
						$grey_fabric_recv_data_arr[$row[csf('determination_id')]]['in_hous_qnty']+=$row[csf('in_hous_qnty')];
						$grey_fabric_recv_data_arr[$row[csf('determination_id')]]['out_bound_qnty']+=$row[csf('out_bound_qnty')];
						$grey_fabric_recv_data_arr[$row[csf('determination_id')]]['storeIds'].=$row[csf('store_id')].',';
						$grey_fabric_recv_data_arr[$row[csf('determination_id')]]['barcodeNo'].=$row[csf('barcode_no')].',';
						
						$recv_barcode_no_arr[$row[csf('barcode_no')]].=$row[csf('barcode_no')].',';
					}

					$grey_fabric_issue_sql="SELECT a.knit_dye_source, b.prod_id, c.po_breakdown_id, c.barcode_no, c.qnty as issue_qty, b.store_id,
					case when a.knit_dye_source=1 then c.qnty else 0 end as in_hous_issue_qty, case when a.knit_dye_source=3 then c.qnty else 0 end as out_bound_issue_qnty
					from inv_issue_master a, inv_transaction b, pro_roll_details c, order_wise_pro_details d
					where a.id=b.mst_id and a.id=c.mst_id and b.id=d.trans_id and d.dtls_id=c.dtls_id and a.entry_form=61 and c.is_returned=0 and d.entry_form=61 and c.entry_form=61 and b.booking_without_order=0 $barcode_no_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
					// echo $grey_fabric_issue_sql;die;
					$grey_fabric_issue_info_result=sql_select($grey_fabric_issue_sql);
					$grey_fabric_issue_data_arr=array();$issue_barcode_no_arr=array();
					foreach ($grey_fabric_issue_info_result as $key => $row) 
					{
						$grey_fabric_issue_data_arr[$row[csf('barcode_no')]]['issue_knitting_source']=$row[csf('knit_dye_source')];
						$grey_fabric_issue_data_arr[$row[csf('barcode_no')]]['issue_barcode']+=$row[csf('issue_qty')];
						$grey_fabric_issue_data_arr[$row[csf('barcode_no')]]['in_hous_issue_qty']+=$row[csf('in_hous_issue_qty')];
						$grey_fabric_issue_data_arr[$row[csf('barcode_no')]]['out_bound_issue_qnty']+=$row[csf('out_bound_issue_qnty')];
					}
					//echo "<pre>";print_r($grey_fabric_issue_data_arr);
				}

				?>
				<table width="920" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" style="float:left; margin-top: 10px;">
					<thead>
						<tr>
							<th colspan="11">Grey Fabric Store Info</th>
						</tr>
						<tr>
							<th width="100">Construction</th>
							<th width="80">Program Qty</th>
							<th width="80">Received Qty Inhouse</th>
							<th width="80">Received Qty Outside</th>
							<th width="80">Total Receive</th>
							<th width="80">Receive Balance</th>
							<th width="80">Grey Issue Inside</th>
							<th width="80">Grey Issue Outside</th>
							<th width="80">Total Issue</th>
							<th width="80">Grey In hand</th>
							<th>Store Name</th>
						</tr>
					</thead>
				</table>
				
				<div style="width:925px; float:left; max-height:300px; overflow-y:scroll" id="scroll_body4">
				<table width="907" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_body_show4" style="float:left;">
					<tbody>
					<?
					$i=1;
					$total_recv_inside_qty=$total_recv_outside_qty=$total_total_recv_qty=$total_receive_balance=$total_issue_inside_qty=$total_issue_outside_qty=$total_total_issue_qty=$total_grey_in_hand=$total_prog_qty=0;
					$recv_inside_qty=$recv_outside_qty=$total_recv_qty=0;
					foreach($grey_fabric_recv_data_arr as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
						/*if($row["knitting_source"]==1)
						{
							$recv_inside_qty=$row["recv_qnty"]; 
						}
						else
						{ 
							$recv_outside_qty=$row["recv_qnty"]; 
						}*/
						$recv_inside_qty=$row["in_hous_qnty"];
						$recv_outside_qty=$row["out_bound_qnty"];

						$total_recv_qty=$recv_inside_qty+$recv_outside_qty;
						$program_qty=$program_qty_arr[$row["determination_id"]];
						$receive_balance=$plan_qty_in_prod[$row["determination_id"]]['plan_qty_prod']-$total_recv_qty;
						$fabric_desc=$fabric_desc_arr[$row["prod_id"]];
						$construction=explode(",", $fabric_desc);

						//$programNo=chop($row["programNo"],',');
						//explode(',', chop($row["programNo"],','))
						//array_unique(explode(',', chop($row["programNo"],',')))
						$programNo=implode(',', array_unique(explode(',', chop($row["programNo"],','))));

						$recv_barcode=chop($row["barcodeNo"],',');
						$recv_barcode_arr=array_unique(explode(',', $recv_barcode));
						// echo "<pre>";print_r($grey_fabric_issue_data_arr);
						$issue_qty=0;$issue_knitting_source=0;
						$issue_inside_qty=$issue_outside_qty=0;
						foreach ($recv_barcode_arr as $key => $barcode) 
						{
							$issue_qty+=$grey_fabric_issue_data_arr[$barcode]['issue_barcode'];
							$issue_inside_qty+=$grey_fabric_issue_data_arr[$barcode]['in_hous_issue_qty'];
							$issue_outside_qty+=$grey_fabric_issue_data_arr[$barcode]['out_bound_issue_qnty'];
							$issue_knitting_source=$grey_fabric_issue_data_arr[$barcode]['issue_knitting_source'];							

							// echo $barcode.'<br>';
						}
						// echo $recv_barcode.'<br>';
						// echo $issue_knitting_source.'<br>';
						/*if($issue_knitting_source==1)
						{
							$issue_inside_qty=$issue_qty; 
						}
						else
						{ 
							$issue_outside_qty=$issue_qty; 
						}*/
						

						$total_issue_qty=$issue_inside_qty-$issue_outside_qty;
						$grey_in_hand=$total_recv_qty-$total_issue_qty;

						$recv_storeIds=chop($row["storeIds"],',');
						$recv_storeIds_arr=array_unique(explode(',', $recv_storeIds));
						$store="";
						foreach ($recv_storeIds_arr as $key => $storeId) // store
						{
							if ($store=="") 
							{
								$store=$store_array[$storeId];
							}
							else
							{
								$store.=','.$store_array[$storeId];
							}
						}
						?>						
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_5nd<? echo $i; ?>','<? echo $bgcolor; ?>');" id="tr_5nd<? echo $i; ?>">
							<td width="100"><? echo $construction_arr[$row["determination_id"]]; //$construction[0]; ?></td>
							<td width="80" align="right"><p><? echo number_format($plan_qty_in_prod[$row["determination_id"]]['plan_qty_prod'],2); ?></p></td>
							<td width="80" align="right"><p><? echo number_format($recv_inside_qty,2); ?></p></td>
							<td width="80" align="right"><p><? echo number_format($recv_outside_qty,2); ?></p></td>
							<td width="80" align="right"><p><? echo number_format($total_recv_qty,2); ?></p></td> 
							<td width="80" align="right" title="<? echo 'Recv barcode:'.$recv_barcode; ?>"><p><? echo number_format($receive_balance,2); ?></p></td>
							<td width="80" align="right"><p><? echo number_format($issue_inside_qty,2); ?></p></td>
							<td width="80" align="right"><p><? echo number_format($issue_outside_qty,2); ?></p></td>
							<td width="80" align="right"><p><? echo number_format($total_issue_qty,2); ?></p></td>
							<td width="80" align="right" title="<? echo $programNo; ?>"><p><a  href="##" onClick="grey_store_wise_pop_up('<? echo $programNo; ?>','<? echo $row["determination_id"]; ?>', 'grey_feb_store')"><? echo number_format($grey_in_hand, 2, '.', ''); ?></a></p></td>
							<td class="breakAll"><p><? echo $store; ?></p></td>
						</tr>
						<?
						$total_prog_qty+=$plan_qty_in_prod[$row["determination_id"]]['plan_qty_prod'];
						$total_recv_inside_qty+=$recv_inside_qty;
						$total_recv_outside_qty+=$recv_outside_qty;
						$total_total_recv_qty+=$total_recv_qty;
						$total_receive_balance+=$receive_balance;
						$total_issue_inside_qty+=$issue_inside_qty;
						$total_issue_outside_qty+=$issue_outside_qty;
						$total_total_issue_qty+=$total_issue_qty;
						$total_grey_in_hand+=$grey_in_hand;
						$i++;
					}
					?>
					</tbody>
				</table>	
				</div>
				<table width="907" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="report_table_footer" align="left">
					<tfoot>
						<tr style="background-color:#CCCCCC;">
							<th width="100">Total</th>
							<th width="80" align="right"><? echo number_format($total_prog_qty,2); ?></th>
							<th width="80" align="right"><? echo number_format($total_recv_inside_qty,2); ?></th>
							<th width="80" align="right"><? echo number_format($total_recv_outside_qty,2); ?></th>
							<th width="80" align="right"><? echo number_format($total_total_recv_qty,2); ?></th>
							<th width="80" align="right"><? echo number_format($total_receive_balance,2); ?></th>
							<th width="80" align="right"><? echo number_format($total_issue_inside_qty,2); ?></th>
							<th width="80" align="right"><? echo number_format($total_issue_outside_qty,2); ?></th>
							<th width="80" align="right"><? echo number_format($total_total_issue_qty,2); ?></th>
							<th width="80" align="right"><? echo number_format($total_grey_in_hand,2); ?></th>
							<th></th>						
						</tr>
					</tfoot>
				</table>
				<!-- Grey Fabric Store Info End-->

				<!-- Batch & Dyeing Production Info (Unload And Shade Matched) Start-->
				<?				
				if ($barcode_no_cond!="") 
				{
					$batch_sql="SELECT a.id as batch_id, a.batch_no, sum(c.batch_qnty) as batch_qnty, a.booking_no
					from pro_batch_create_mst a, pro_batch_create_dtls c where a.id=c.mst_id $barcode_no_cond and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id, a.batch_no, a.booking_no";
					// echo $batch_sql;
					$batch_sql_result=sql_select($batch_sql);
					$batch_id_arr = array(); $batch_booking_no_arr = array();
					foreach ($batch_sql_result as $key => $value) 
					{
						$batch_id_arr[$value[csf("batch_id")]]=$value[csf("batch_id")];
						$batch_booking_no_arr[$value[csf("batch_id")]]=$value[csf("booking_no")];
					}
					$all_batch_id_arr = array_filter($batch_id_arr);
					if(count($all_batch_id_arr) > 0)
					{
						$all_batch_id = implode(",", $all_batch_id_arr);
						$batchIdCond=$batch_id_cond=$batchIdCond2=$batch_id_cond2=$batchIdCond3=$batch_id_cond3="";
						if($db_type==2 && count($all_batch_id_arr)>999)
						{
							/*$all_batch_id_chunk=array_chunk($all_batch_id_arr,999) ;
							foreach($all_batch_id_chunk as $chunk_batch_id)
							{
								$chunk_batch_id_val=implode(",",$chunk_batch_id);
								$batchIdCond.=" and a.id in($chunk_batch_id_val) or ";
								$batchIdCond2.=" and b.batch_id in($chunk_batch_id_val) or ";
								$batchIdCond3.=" and b.pi_wo_batch_no in($chunk_batch_id_val) or ";
							}
							$batch_id_cond.=" and (".chop($batchIdCond,'or ').")";
							$batch_id_cond2.=" and (".chop($batchIdCond2,'or ').")";
							$batch_id_cond3.=" and (".chop($batchIdCond3,'or ').")";*/

							$all_batch_id_chunk=array_chunk($all_batch_id_arr, 999);
							foreach($all_batch_id_chunk as $chunk_batch_id)
							{
								$chunk_batch_id_val=implode(",", $chunk_batch_id);
								if(!$batch_id_cond)$batch_id_cond.=" and ( a.id in($chunk_batch_id_val) ";
								else $batch_id_cond.=" or  a.id in($chunk_batch_id_val) ";
								if(!$batch_id_cond2)$batch_id_cond2.=" and ( b.batch_id in($chunk_batch_id_val) ";
								else $batch_id_cond2.=" or b.batch_id in($chunk_batch_id_val) ";
								if(!$batch_id_cond3)$batch_id_cond3.=" and ( b.pi_wo_batch_no in($chunk_batch_id_val) ";
								else $batch_id_cond3.=" or b.pi_wo_batch_no in($chunk_batch_id_val) ";
							}
							$batch_id_cond.=")";
							$batch_id_cond2.=")";
							$batch_id_cond3.=")";
						}
						else
						{
							$batch_id_cond=" and a.id in($all_batch_id)";
							$batch_id_cond2=" and b.batch_id in($all_batch_id)";
							$batch_id_cond3=" and b.pi_wo_batch_no in($all_batch_id)";
						}

						$dyeing_batch_sql="SELECT a.id as batch_id, a.batch_no, a.color_id, b.item_description, a.extention_no, b.batch_qnty, a.booking_no
						from pro_batch_create_mst a, pro_batch_create_dtls b 
						where a.id=b.mst_id $batch_id_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.batch_no";
						// echo $dyeing_batch_sql;
						$dyeing_batch_result=sql_select($dyeing_batch_sql);
						$dyeing_batch_data_arr=array();$dyeing_batch_color_arr=array();
						foreach ($dyeing_batch_result as $key => $row)
						{
							$dyeing_batch_data_arr[$row[csf('batch_id')]][$row[csf('color_id')]][$row[csf('extention_no')]]['batchQnty']+=$row[csf('batch_qnty')];
							$dyeing_batch_data_arr[$row[csf('batch_id')]][$row[csf('color_id')]][$row[csf('extention_no')]]['batchNo']=$row[csf('batch_no')];
							$dyeing_batch_data_arr[$row[csf('batch_id')]][$row[csf('color_id')]][$row[csf('extention_no')]]['itemDescription'].=$row[csf('item_description')].'*';
							$dyeing_batch_data_arr[$row[csf('batch_id')]][$row[csf('color_id')]][$row[csf('extention_no')]]['bookingNo']=$row[csf('booking_no')];
							$dyeing_batch_color_arr[$row[csf('batch_id')]]=$row[csf('color_id')];
						}

						$batch_production_sql="SELECT b.id as subpmstid, b.batch_id, c.production_qty as production_qty, b.process_id, b.result, c.load_unload_id, b.entry_form, b.batch_ext_no as extention_no 
						from  pro_fab_subprocess b, pro_fab_subprocess_dtls c
						where b.id=c.mst_id and b.process_id='31' and c.load_unload_id=2 and b.result=1
						$batch_id_cond2 and b.status_active=1 and b.is_deleted=0 order by b.batch_id";
						// echo $batch_production_sql;
						$batch_production_result=sql_select($batch_production_sql);
						$production_qty_data_arr=array();
						foreach ($batch_production_result as $key => $row)
						{
							$color=$dyeing_batch_color_arr[$row[csf('batch_id')]];
							$production_qty_data_arr[$row[csf('batch_id')]][$color][$row[csf('extention_no')]]['production_qty']+=$row[csf('production_qty')];
						}				
					}
				}
				?>				
				<table width="635" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" style="float:left; margin-top: 10px;">
					<thead>
						<tr>
							<th colspan="8">Batch & Dyeing Production Info (Unload And Shade Matched)</th>
						</tr>
						<tr>
							<th width="70">Colour</th>
							<th width="100">Construction</th>
							<th width="70">Batch No</th>
							<th width="100">Booking No</th>
							<th width="30">Ext</th>
							<th width="80">Batch Qty</th>
							<th width="80">Dyeing Qty</th>
							<th>Dyeing Balance</th>
						</tr>
					</thead>
				</table>
				
				<div style="width:635px; float:left; max-height:300px; overflow-y:scroll" id="scroll_body5">
				<table width="617" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_body_show5" style="float:left;">
					<tbody>
					<?
					$i=1;
					$total_batch_qnty=$total_production_qty=$total_dyeing_balance=0;
					foreach ($dyeing_batch_data_arr as $batch_id_key => $batch_id_val)
					{
						foreach ($batch_id_val as $color_id_key => $batch_color_id) 
						{
							foreach ($batch_color_id as $extention_key => $row) 
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

								$construction=array_filter(explode('*', $row['itemDescription']));
								$cons_var="";
								foreach ($construction as $key => $value) 
								{
									$construction2=explode(',', $value);
									$cons_var.=($cons_var=="")?$construction2[0]:",".$construction2[0];
								}
								$unique_cons_var=implode(",", array_unique(explode(",", $cons_var)));
								$production_qty=$production_qty_data_arr[$batch_id_key][$color_id_key][$extention_key]['production_qty'];
								?>						
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_6nd<? echo $i; ?>','<? echo $bgcolor; ?>');" id="tr_6nd<? echo $i; ?>">
									<td width="70"><p><? echo $color_arr[$color_id_key]; ?></p></td>
									<td width="100" align="center"><p><? echo $unique_cons_var; ?></p></td>
									<td width="70" align="center" title="<? echo $batch_id_key; ?>"><p><? echo $row['batchNo']; ?></p></td>
									<td width="100" align="center"><p><? echo $row['bookingNo']; ?></p></td>
									<td width="30" align="center"><p><? echo $extention_key; ?></p></td>
									<td width="80" align="right" ><p><? echo number_format($row['batchQnty'],2); ?></p></td>
									<td width="80" align="right"><p><? echo number_format($production_qty,2); ?></p></td>
									<td class="breakAll" align="right"><p><? echo number_format($row['batch_qnty']-$row['production_qty'],2); ?></p></td>
								</tr>
								<?
								$total_batch_qnty+=$row['batchQnty'];
								$total_production_qty+=$production_qty;
								$total_dyeing_balance+=$row['batch_qnty']-$row['production_qty'];
								$i++;
							}
							
						}						
					}
					?>
					</tbody>
				</table>	
				</div>

				<table width="617" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="report_table_footer" align="left">
					<tfoot>
						<tr style="background-color:#CCCCCC;">
							<th width="70"></th>
							<th width="100"></th>
							<th width="70"></th>
							<th width="100"></th>
							<th width="30" align="right">Total</th>
							<th width="80" align="right"><? echo number_format($total_batch_qnty,2); ?></th>
							<th width="80" align="right"><? echo number_format($total_production_qty,2); ?></th>
							<th align="right"><? echo number_format($total_dyeing_balance,2); ?></th>						
						</tr>
					</tfoot>
				</table>
				<!-- Batch & Dyeing Production Info (Unload And Shade Matched) End-->

				<!-- Finish Process Info (Process will Display By Batch Sequence) Start-->
				<?
				if ($batch_id_cond!="")
				{
					$batch_sql="SELECT a.id as batch_id, a.batch_no, a.color_id, b.item_description, a.extention_no, b.batch_qnty as batch_qnty
					from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  
					$batch_id_cond  order by a.batch_no";
					// echo $batch_sql;die;
					$batch_sql_result=sql_select($batch_sql);
					$finish_process_data_arr=array();$finish_process_data_arr=array();$process_id_arr=array();$process_wise_qty_arr=array();$batch_color_arr=array();
					foreach ($batch_sql_result as $key => $row)
					{
						$finish_process_data_arr[$row[csf('batch_id')]][$row[csf('color_id')]][$row[csf('extention_no')]]['batchQnty']+=$row[csf('batch_qnty')];
						$finish_process_data_arr[$row[csf('batch_id')]][$row[csf('color_id')]][$row[csf('extention_no')]]['batchNo']=$row[csf('batch_no')];
						$finish_process_data_arr[$row[csf('batch_id')]][$row[csf('color_id')]][$row[csf('extention_no')]]['itemDescription'].=$row[csf('item_description')].'*';
						$batch_color_arr[$row[csf('batch_id')]]=$row[csf('color_id')];
					}

					$process_sql="SELECT b.id as subpmstid, b.batch_id, c.production_qty as production_qty, b.process_id, b.result,  c.load_unload_id, b.entry_form, b.batch_ext_no as extention_no from  pro_fab_subprocess b, pro_fab_subprocess_dtls c
					where b.id=c.mst_id and b.process_id!='31' and c.load_unload_id!=2 
					$batch_id_cond2 and b.status_active=1 and b.is_deleted=0 order by b.batch_id";
					// echo $process_sql;
					$process_sql_result=sql_select($process_sql);
					$process_count_arr=array();$check_mastId_arr=array();
					foreach ($process_sql_result as $key => $row)
					{
						$color=$batch_color_arr[$row[csf('batch_id')]];
						$finish_process_data_arr[$row[csf('batch_id')]][$color][$row[csf('extention_no')]]['production_qty']+=$row[csf('production_qty')];
						$process_wise_qty_arr[$row[csf('batch_id')]][$color][$row[csf('extention_no')]][$row[csf('process_id')]]["process_qty"]+=$row[csf('production_qty')];

						if ($row[csf('process_id')]!="") 
						{
							$process_id_arr[$row[csf('process_id')]]=$row[csf('process_id')];
						}
						if (!in_array($row[csf('subpmstid')], $check_mastId_arr)) 
						{
							$check_mastId_arr[$row[csf('subpmstid')]]=$row[csf('subpmstid')];
							$process_count_arr[$row[csf('batch_id')]][$row[csf('process_id')]]++;
						}
					}
					$count_process=count($process_id_arr);
					//echo "<pre>";print_r($process_count_arr);echo "<pre>";die;
				}
				
				$table_width=480;
				$div_width=495;
				$table_width2=478;
				?>
				<div style="width:<? echo $table_width+20+($count_process*80); ?>px; float:left;">
					<table width="<? echo $table_width+($count_process*80); ?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" style="float:left; margin-top: 10px;">
						<thead>
							<tr>
								<th colspan="<? echo 5+($count_process); ?>">Finish Process Info (Process will Display By Batch Sequence)</th>
							</tr>
							<tr>
								<th rowspan="2" width="70">Colour</th>
								<th rowspan="2" width="100">Construction</th>
								<th rowspan="2" width="70">Batch No</th>
								<th rowspan="2" width="30">Ext</th>
								<th rowspan="2" width="80">Batch Qty</th>
								<?
			                    foreach($process_id_arr as $process)
			                    {
			                    	?>
			                    	<th width="80" title="<? echo $process; ?>" style="word-wrap:break-word; word-break: break-all;"><? echo $conversion_cost_head_array[$process]; //$entry_form?></th>
			                    	<?
			                    }  
			                    ?>
							</tr>
							<tr>
								<?
			                    foreach($process_id_arr as $process)
			                    {
			                    	?>
			                    	<th width="80" style="word-wrap:break-word; word-break: break-all;"><? echo 'Finish Product';?></th>
			                    	<?
			                    }  
			                    ?>
							</tr>
						</thead>
					</table>
					
					<div style="width:<? echo $div_width+($count_process*80); ?>px; float:left; max-height:300px; overflow-y:scroll" id="scroll_body6">
					<table width="<? echo $table_width2+($count_process*80); ?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_body_show6" style="float:left;">
						<tbody>
						<?
						$i=1;
						$total_batchQnty=0;
						foreach ($finish_process_data_arr as $batch_no_key => $batch_id_val)
						{
							foreach ($batch_id_val as $color_id_key => $batch_color_id) 
							{
								foreach ($batch_color_id as $extention_key => $row) 
								{
									if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									$construction=array_filter(explode('*', $row['itemDescription']));
									$cons_var="";
									foreach ($construction as $key => $value) 
									{
										$construction2=explode(',', $value);
										$cons_var.=($cons_var=="")?$construction2[0]:",".$construction2[0];
									}
									$unique_cons_var=implode(",", array_unique(explode(",", $cons_var)));
									?>						
									<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_7nd<? echo $i; ?>','<? echo $bgcolor; ?>');" id="tr_7nd<? echo $i; ?>">
										<td width="70"><p><? echo $color_arr[$color_id_key]; ?></p></td>
										<td width="100" align="center"><p><? echo $unique_cons_var; ?></p></td>
										<td width="70" align="center"><p><? echo $row['batchNo']; ?></p></td>
										<td width="30" align="center"><p><? echo $extention_key; ?></p></td>
										<td width="80" align="right" ><p><? echo number_format($row['batchQnty'],2); ?></p></td>
										<?

					                    foreach($process_id_arr as $process)
					                    {
					                    	$count=$process_count_arr[$batch_no_key][$process];
					                    	$process_qty=$process_wise_qty_arr[$batch_no_key][$color_id_key][$extention_key][$process]["process_qty"];

					                    	$total_process_qty_arr[$process]+=$process_wise_qty_arr[$batch_no_key][$color_id_key][$extention_key][$process]["process_qty"];
					                    	?>
					                    	<td width="80" title="<? echo "batch_id:".$batch_no_key.',color_id:'.$color_id_key.',extention:'.$extention_key.',process_id:'.$process; ?>" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($process_qty,2); if ($count>0) { echo " (".$count.")"; } ?></td>
					                    	<?
					                    } 
					                    ?>
									</tr>
									<?
									$total_batchQnty+=$row['batchQnty'];
									$i++;
								}								
							}
						}
						?>
						</tbody>
					</table>
					</div>
					<table width="<? echo $table_width2+($count_process*80); ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="report_table_footer" align="left">
						<tfoot>
							<tr style="background-color:#CCCCCC;">
								<th width="70"></th>
								<th width="100"></th>
								<th width="70"></th>
								<th width="30" align="right">Total</th>
								<th width="80" align="right"><? echo number_format($total_batchQnty,2); ?></th>
								<?
			                    foreach($process_id_arr as $process)
			                    {
			                    	$process_qty_total = $total_process_qty_arr[$process];
			                    	?>
			                    	<th width="80" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($process_qty_total,2); ?></th>
			                    	<?
			                    } 
			                    ?>		
							</tr>
						</tfoot>
					</table>
				</div>
				<!-- Finish Process Info (Process will Display By Batch Sequence) End-->

				<!-- Finish Fabric Production Info Start-->
				<?
				if ($batch_id_cond!="")
				{
					$finish_fab_production_sql="SELECT a.id as batch_id, a.batch_no, a.color_id, a.extention_no, b.item_description, sum(b.batch_qnty) as batch_qnty
					from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id $batch_id_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.batch_no, a.color_id, a.extention_no, b.item_description order by a.batch_no";// and A.BATCH_NO='AGUN2020'
					// echo $finish_fab_production_sql;//die;
					$finish_fab_production_sql_result=sql_select($finish_fab_production_sql);
					$finish_process_data_arr=array();
					foreach ($finish_fab_production_sql_result as $key => $row)
					{
						$finish_process_data_arr[$row[csf('batch_id')]][$row[csf('color_id')]][$row[csf('extention_no')]]['batch_qnty']+=$row[csf('batch_qnty')];
						$finish_process_data_arr[$row[csf('batch_id')]][$row[csf('color_id')]][$row[csf('extention_no')]]['batchId']=$row[csf('batch_id')];
						$finish_process_data_arr[$row[csf('batch_id')]][$row[csf('color_id')]][$row[csf('extention_no')]]['batchNo']=$row[csf('batch_no')];
						$finish_process_data_arr[$row[csf('batch_id')]][$row[csf('color_id')]][$row[csf('extention_no')]]['itemDescription'].=$row[csf('item_description')].'*';
					}
					// echo "<pre>";print_r($finish_process_data_arr);echo "<pre>";die;

					// Finish Fabric Production Entry
					$finish_prod_sql="SELECT b.batch_id, b.color_id, sum(b.receive_qnty) as finish_qty from inv_receive_master a, pro_finish_fabric_rcv_dtls b 
					where a.id=b.mst_id and a.entry_form=7 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $batch_id_cond2 group by b.batch_id, b.color_id";
					// echo $finish_prod_sql;
					$finish_prod_sql_result=sql_select($finish_prod_sql);
					$finish_prod_data_arr=array();
					foreach ($finish_prod_sql_result as $key => $row)
					{
						$finish_prod_data_arr[$row[csf('batch_id')]][$row[csf('color_id')]]['finish_qty']+=$row[csf('finish_qty')];
					}

					// Finish Fabric Delivery to Store
					$fin_fab_delivery_sql="SELECT b.batch_id, b.color_id, sum(b.current_delivery) as finish_delivery
					from pro_grey_prod_delivery_mst a, pro_grey_prod_delivery_dtls b 
					where a.id=b.mst_id and a.entry_form=54 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $batch_id_cond2  group by b.batch_id, b.color_id";
					// echo $fin_fab_delivery_sql;
					$fin_fab_delivery_result=sql_select($fin_fab_delivery_sql);
					$fin_fab_delivery_data_arr=array();
					foreach ($fin_fab_delivery_result as $key => $row)
					{
						$fin_fab_delivery_data_arr[$row[csf('batch_id')]][$row[csf('color_id')]]['finish_delivery_qty']+=$row[csf('finish_delivery')];
					}
				}
				?>
				<table width="665" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" style="float:left; margin-top: 10px;">
					<thead>
						<tr>
							<th colspan="9">Finish Fabric Production Info</th>
						</tr>
						<tr>
							<th width="70">Colour</th>
							<th width="100">Construction</th>
							<th width="70">Batch No</th>
							<th width="30">Ext</th>
							<th width="80">Batch Qty</th>
							<th width="80">Finish Prod. Qty</th>
							<th width="80">Batch Wise Prod. Balance</th>
							<th width="80">Del. To Store Qty</th>
							<th>Batch Wise Del. Balance</th>
						</tr>
					</thead>
				</table>
				
				<div style="width:665px; float:left; max-height:300px; overflow-y:scroll" id="scroll_body7">
				<table width="647" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_body_show7" style="float:left;">
					<tbody>
					<?
					$i=1;
					$total_batch_qnty=$total_finish_prod_qty=$total_batch_wise_prod_balance=$total_finish_delivery_qty=$total_batch_wise_del_balance=0;
					foreach ($finish_process_data_arr as $batch_key => $bactch_id_val)
					{
						foreach ($bactch_id_val as $color_id_key => $batch_color_id) 
						{
							foreach ($batch_color_id as $extention_key => $row) 
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								$construction=array_filter(explode('*', $row['itemDescription']));
								$cons_var="";
								foreach ($construction as $key => $value) 
								{
									$construction2=explode(',', $value);
									$cons_var.=($cons_var=="")?$construction2[0]:",".$construction2[0];
								}
								$unique_cons_var=implode(",", array_unique(explode(",", $cons_var)));
								$finish_prod_qty=$finish_prod_data_arr[$row['batchId']][$color_id_key]['finish_qty'];
								$batch_wise_prod_balance=$row['batch_qnty']-$finish_prod_qty;

								$finish_delivery_qty=$fin_fab_delivery_data_arr[$row['batchId']][$color_id_key]['finish_delivery_qty'];
								$batch_wise_del_balance=$row['batch_qnty']-$finish_delivery_qty;
								?>						
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_8nd<? echo $i; ?>','<? echo $bgcolor; ?>');" id="tr_8nd<? echo $i; ?>">
									<td width="70"><p><? echo $color_arr[$color_id_key]; ?></p></td>
									<td width="100" align="center"><p><? echo $unique_cons_var; ?></p></td>
									<td width="70" align="center"><p><? echo $row['batchNo']; ?></p></td>
									<td width="30" align="center"><p><? echo $extention_key; ?></p></td>
									<td width="80" align="right" ><p><? echo number_format($row['batch_qnty'],2); ?></p></td>
									<td width="80" align="right"><p><? echo number_format($finish_prod_qty,2); ?></p></td> 
									<td width="80" align="right" ><p><? echo number_format($batch_wise_prod_balance,2); ?></p></td>
									<td width="80" align="right"><p><? echo number_format($finish_delivery_qty,2); ?></p></td>
									<td class="breakAll" align="right"><p><? echo number_format($batch_wise_del_balance,2); ?></p></td>
								</tr>
								<?
								$total_batch_qnty+=$row['batch_qnty'];
								$total_finish_prod_qty+=$finish_prod_qty;
								$total_batch_wise_prod_balance+=$batch_wise_prod_balance;
								$total_finish_delivery_qty+=$finish_delivery_qty;
								$total_batch_wise_del_balance+=$batch_wise_del_balance;
								$i++;
							}
						}
					}
					?>
					</tbody>
				</table>
				</div>
				<table width="647" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="report_table_footer" align="left">
					<tfoot>
						<tr style="background-color:#CCCCCC;">
							<th width="70"></th>
							<th width="100"></th>
							<th width="70"></th>
							<th width="30" align="right">Total</th>
							<th width="80" align="right"><? echo number_format($total_batch_qnty,2); ?></th>
							<th width="80" align="right"><? echo number_format($total_finish_prod_qty,2); ?></th>
							<th width="80" align="right"><? echo number_format($total_batch_wise_prod_balance,2); ?></th>
							<th width="80" align="right"><? echo number_format($total_finish_delivery_qty,2); ?></th>
							<th><? echo number_format($total_batch_wise_del_balance,2); ?></th>						
						</tr>
					</tfoot>
				</table>
				<!-- Finish Fabric Production Info End-->

				<!-- Finish Fabric Store Info Start-->
				<?
				if ($batch_id_cond3!="")
				{
					// Finish Fabric Production Entry
					/*"select sum(case when c.entry_form in (7,37,66,68) then c.quantity else 0 end) as receive_qnty,
					sum(case when c.entry_form in(14,15,134) and c.trans_type=5 then c.quantity else 0 end) as rec_trns_qnty,
					sum(case when c.entry_form in (18,71) then c.quantity else 0 end) as issue_qnty,
					sum(case when c.entry_form in (126,52) then c.quantity else 0 end) as issue_ret_qnty,
					sum(case when c.entry_form in(14,15,134) and c.trans_type=6 then c.quantity else 0 end) as issue_trns_qnty
					from  inv_transaction b, order_wise_pro_details c
					where b.id=c.trans_id and B.TRANSACTION_TYPE=1 and B.BOOKING_WITHOUT_ORDER=0 and B.PI_WO_BATCH_NO=11649 and c.ENTRY_FORM=37";*/

					// Finish Fabric receive
					$receive_sql="SELECT b.pi_wo_batch_no as batch_id, c.color_id, b.store_id, sum(c.quantity) as receive_qnty
					from  inv_transaction b, order_wise_pro_details c
					where b.id=c.trans_id and b.transaction_type=1 and b.booking_without_order=0 and c.entry_form=37  and b.item_category=2 $batch_id_cond3
					group by b.pi_wo_batch_no, b.store_id, c.color_id";
					// echo $receive_sql;
					$receive_sql_result=sql_select($receive_sql);
					$receive_data_arr=array();
					foreach ($receive_sql_result as $key => $row)
					{
						$receive_data_arr[$row[csf('batch_id')]][$row[csf('color_id')]]['receiveQnty']+=$row[csf('receive_qnty')];
						$receive_data_arr[$row[csf('batch_id')]][$row[csf('color_id')]]['storeId'].=$row[csf('store_id')].',';
					}
					// echo "<pre>";print_r($receive_data_arr);
					// Finish Fabric Transfer Sql
					// inventory\finish_fabric\requires\finish_fabric_transfer_controller.php
					$fin_fab_tranf_sql="SELECT b.pi_wo_batch_no as batch_id, d.color_id, sum(c.quantity) as trans_out_qnty 
					from inv_transaction b, order_wise_pro_details c, inv_item_transfer_dtls d 
					where b.id=c.trans_id and b.id=d.trans_id and c.trans_id=d.trans_id and c.dtls_id = d.id and c.trans_type=6 and b.transaction_type=6 and b.item_category=2 $batch_id_cond3
					and b.status_active =1 and b.is_deleted =0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and b.pi_wo_batch_no> 0 and d.active_dtls_id_in_transfer =1
					group by  b.pi_wo_batch_no, d.color_id";// and b.pi_wo_batch_no='11649'
					// echo $fin_fab_tranf_sql;
					$fin_fab_tranf_result=sql_select($fin_fab_tranf_sql);
					$fin_fab_tranf_data_arr=array();
					foreach ($fin_fab_tranf_result as $key => $row)
					{
						$fin_fab_tranf_data_arr[$row[csf('batch_id')]][$row[csf('color_id')]]['trans_out_qnty']+=$row[csf('trans_out_qnty')];
					}

					// Finish Fabric Issue Sql
					$fin_fab_issue_sql="SELECT b.pi_wo_batch_no as batch_id, d.color_id, sum(b.cons_quantity) as issue_qnty 
					from inv_transaction b, inv_finish_fabric_issue_dtls c, order_wise_pro_details d 
					where b.id=c.trans_id and b.id=d.trans_id and b.status_active=1 and b.is_deleted=0 and b.item_category=2 and b.transaction_type=2 and c.status_active=1 and c.is_deleted=0
					$batch_id_cond3
					group by b.pi_wo_batch_no, d.color_id";// and b.pi_wo_batch_no='11649'
					// echo $fin_fab_issue_sql;
					$fin_fab_issue_result=sql_select($fin_fab_issue_sql);
					$fin_fab_issue_data_arr=array();
					foreach ($fin_fab_issue_result as $key => $row)
					{
						$fin_fab_issue_data_arr[$row[csf('batch_id')]][$row[csf('color_id')]]['issue_qnty']+=$row[csf('issue_qnty')];
					}
				}
				?>
				<table width="930" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" style="float:left; margin-top: 10px;">
					<thead>
						<tr>
							<th colspan="12">Finish Fabric Store Info</th>
						</tr>
						<tr>
							<th width="70">Colour</th>
							<th width="100">Construction</th>
							<th width="70">Batch No</th>
							<th width="30">Ext</th>
							<th width="80">Batch Qty</th>
							<th width="110">Rcvd Store Name</th>
							<th width="80">Received Qty</th>
							<th width="80">Received Balance</th>
							<th width="80">Transferred Qty</th>
							<th width="80">Issue to Cutting</th>
							<th width="80">Yet To Issue</th>
							<th>Stock/Left Over Qty</th>
						</tr>
					</thead>
				</table>
				
				<div style="width:935px; float:left; max-height:300px; overflow-y:scroll" id="scroll_body8">
				<table width="917" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_body_show8" style="float:left;">
					<tbody>
					<?
					$i=1;
					foreach ($finish_process_data_arr as $batch_id_key => $bactch_id_val)
					{
						foreach ($bactch_id_val as $color_id_key => $batch_color_id) 
						{
							foreach ($batch_color_id as $extention_key => $row) 
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
								$receive_qty=$receive_data_arr[$batch_id_key][$color_id_key]['receiveQnty'];
								$received_balance=$row['batch_qnty']-$receive_qty;
								$store_ids=$receive_data_arr[$batch_id_key][$color_id_key]['storeId'];
								$fin_recv_storeIds=chop($store_ids,',');
								$fin_recv_storeIds_arr=array_unique(explode(',', $fin_recv_storeIds));
								$store_name="";
								foreach ($fin_recv_storeIds_arr as $key => $storeId) // store
								{
									if ($store_name=="") 
									{
										$store_name=$store_array[$storeId];
									}
									else
									{
										$store_name.=','.$store_array[$storeId];
									}
								}
								$transf_out_qty=$fin_fab_tranf_data_arr[$batch_id_key][$color_id_key]['trans_out_qnty'];
								$issue_qty=$fin_fab_issue_data_arr[$batch_id_key][$color_id_key]['issue_qnty'];
								$yet_to_issue=$row['batch_qnty']-$issue_qty;
								$left_over_qty=$receive_qty-$issue_qty;
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_9nd<? echo $i; ?>','<? echo $bgcolor; ?>');" id="tr_9nd<? echo $i; ?>">
									<td width="70"><p><? echo $color_arr[$color_id_key]; ?></p></td>
									<td width="100" align="center"><p><? echo $unique_cons_var; ?></p></td>
									<td width="70" align="center"><p><? echo $row['batchNo']; ?></p></td>
									<td width="30" align="center"><p><? echo $extention_key; ?></p></td>
									<td width="80" align="right" ><p><? echo number_format($row['batch_qnty'],2); ?></p></td>
									<td width="110" align="center"><p><? echo $store_name; ?></p></td> 
									<td width="80" align="right"><p>
										<a  href="##" onClick="fin_store_wise_pop_up('<? echo $batch_id_key; ?>','<? echo $color_id_key; ?>', 'finish_store')"><? echo number_format($receive_qty, 2, '.', ''); ?></a></p>
									</td>
									<td width="80" align="right"><p><? echo number_format($received_balance,2); ?></p></td>
									<td width="80" align="right"><p><? echo number_format($transf_out_qty,2); ?></p></td>
									<td width="80" align="right"><p><? echo number_format($issue_qty,2); ?></p></td>
									<td width="80" align="right"><p><? echo number_format($yet_to_issue,2); ?></p></td>
									<td class="breakAll" align="right"><p><? echo number_format($left_over_qty,2); ?></p></td>
								</tr>
								<?
								$total_batch_qnty+=$row['batch_qnty'];
								$total_receive_qty+=$receive_qty;
								$total_received_balance+=$received_balance;
								$total_transf_out_qty+=$transf_out_qty;
								$total_issue_qty+=$issue_qty;
								$total_yet_to_issue+=$yet_to_issue;
								$total_left_over_qty+=$left_over_qty;
								$i++;
							}
						}						
					}
					?>
					</tbody>
				</table>	
				</div>
				<table width="917" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="report_table_footer" align="left">
					<tfoot>
						<tr style="background-color:#CCCCCC;">
							<th width="70"></th>
							<th width="100"></th>
							<th width="70"></th>
							<th width="30" align="right">Total</th>
							<th width="80" align="right"><? echo number_format($total_batch_qnty,2); ?></th>
							<th width="110"></th>
							<th width="80" align="right"><? echo number_format($total_receive_qty,2); ?></th>
							<th width="80" align="right"><? echo number_format($total_received_balance,2); ?></th>
							<th width="80" align="right"><? echo number_format($total_transf_out_qty,2); ?></th>
							<th width="80" align="right"><? echo number_format($total_issue_qty,2); ?></th>
							<th width="80" align="right"><? echo number_format($total_yet_to_issue,2); ?></th>
							<th align="right"><? echo number_format($total_left_over_qty,2); ?></th>					
						</tr>
					</tfoot>
				</table>
				<!-- Finish Fabric Store Info End-->
			</div>
		</fieldset>
		<?
	}
	
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

if($action=="booking_popup")
{
	echo load_html_head_contents("Booking Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	
	<script>
	function js_set_value(str)
	{
		$("#hide_booking_no").val(str);
		parent.emailwindow.hide(); 
	}
	</script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset style="width:980px;">
            <table width="770" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Buyer</th>
                    <th width="100">Booking No</th>
                    <th width="80">Style Desc.</th>
                    <th width="200">Date Range</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th> 				
                    <input type="hidden" name="hide_booking_no" id="hide_booking_no" value="" />
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        	 <? 
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$company_id $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
							?>
                        </td>                 
                        <td><input name="txt_booking_prifix" id="txt_booking_prifix" class="text_boxes" style="width:100px"></td>
                        <td><input name="txt_style_desc" id="txt_style_desc" class="text_boxes" style="width:80px"></td>
                        <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">
                          <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
                         </td> 	
                         <td align="center">
                 			<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company_id; ?>'+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_booking_prifix').value+'_'+document.getElementById('txt_style_desc').value, 'create_booking_search_list_view', 'search_div', 'booking_and_plan_wise_fabric_status_report_controller','setFilterGrid(\'table_body_booking\',1)')" style="width:100px;" />              
                        </td>
                    </tr>
                    <tr>
                        <td  align="center" height="40" valign="middle" colspan="6">
                        <? 
                        echo create_drop_down( "cbo_year_selection", 70, $year,"", 1, "-- Select --", date('Y'), "",0 );		
                        ?>
                        <? echo load_month_buttons();  ?>
                        </td>
                    </tr>
            	</tbody>
           	</table>
            <div style="margin-top:15px" id="search_div"></div>
		</fieldset>
	</form>
    </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit(); 
}

if ($action=="create_booking_search_list_view")
{
	$data=explode('_',$data);
	// print_r($data);die;
	$style_desc=$data[7];
	if ($data[0]!=0) $company="  a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	
	if ($data[1]!=0){$buyer=" and a.buyer_id='$data[1]'";}
	else{$buyer="";}
	
	if($db_type==0)
	 {
		  // $booking_year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$data[4]";
		  $booking_year_cond=" and YEAR(a.insert_date)=$data[4]";
		  if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' 
		  and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $booking_date =""; 
     }
	if($db_type==2)
	 {
		  $booking_year_cond=" and to_char(a.insert_date,'YYYY')=$data[4]";
		  if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."'
		  and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
	 }
	if($data[6]==4 || $data[6]==0)
	{
		if (str_replace("'","",$data[5])!="") $booking_cond=" and a.booking_no like '%$data[5]%'  $booking_year_cond  "; else $booking_cond="";
		if (str_replace("'","",$data[6])!="") $style_des_cond=" and c.style_ref_no like '%$data[6]%'  $booking_year_cond "; else $style_des_cond="";
	}
 	//echo $style_des_cond;die;
	
	

	/*$po_array=array();
	$sql_po= sql_select("select a.booking_no_prefix_num, a.booking_no,a.po_break_down_id from wo_non_ord_samp_booking_mst a  where $company $buyer $booking_date and booking_type=4  and   status_active=1  and 	is_deleted=0 order by booking_no");
	foreach($sql_po as $row)
	{
		$po_id=explode(",",$row[csf("po_break_down_id")]);
		$po_number_string="";
		foreach($po_id as $key=> $value )
		{
			$po_number_string.=$po_number[$value].",";
		}
		$po_array[$row[csf("po_break_down_id")]]=rtrim($po_number_string,",");
	}*/
	$style_library=return_library_array( "select id,style_ref_no from sample_development_mst", "id", "style_ref_no"  );
    $approved=array(0=>"No",1=>"Yes");
    $is_ready=array(0=>"No",1=>"Yes",2=>"No"); 
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$suplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
	$arr=array (2=>$comp,3=>$buyer_arr,4=>$item_category,5=>$fabric_source,6=>$suplier,7=>$style_library,9=>$approved,10=>$is_ready);
	$sql= " SELECT a.booking_no_prefix_num, a.booking_no,a.booking_date,a.company_id,a.buyer_id,a.item_category,a.fabric_source,a.supplier_id,a.is_approved,a.ready_to_approved,a.pay_mode, a.booking_type, b.style_id, c.style_ref_no
	from wo_booking_mst a, wo_booking_dtls b, wo_po_details_master c
	where a.booking_no=b.booking_no and a.job_no=c.job_no and b.job_no=c.job_no and $company". set_user_lavel_filtering(' and a.buyer_id','buyer_id')." $buyer $booking_date $booking_cond $style_des_cond and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
	group by a.booking_no_prefix_num, a.booking_no,a.booking_date,a.company_id,a.buyer_id,a.item_category,a.fabric_source,a.supplier_id,a.is_approved,a.ready_to_approved,a.pay_mode, a.booking_type,b.style_id, c.style_ref_no order by booking_no"; 
	// echo $sql;die;
	?>
   <table class="rpt_table scroll" width="870" cellpadding="0" cellspacing="0" border="1" rules="all" id="">
       <thead>
       	<tr>
            <th width="40">Sl</th> 
            <th width="80">Booking No</th>  
            <th width="80">Booking Type</th>  
            <th width="80">Booking Date</th>           	 
            <th width="100">Buyer</th>
            <th width="120">Fabric Nature</th>
            <th width="80">Fabric Source</th>
            <th width="80">Pay Mode</th>
            <th width="100">Supplier</th>
            <th width="97">Style</th>
        </tr>
        </thead>
     </table>
		<div style="width:870px; max-height:280px; overflow-y:scroll" id="list_container_batch" align="left">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="852" class="rpt_table" id="table_body_booking">
                <tbody>
                    <? 
                    $i=1;
                    $sql_data=sql_select($sql);
                    foreach($sql_data as $row){
                        if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";    
                    ?>
                    <tr bgcolor="<? echo $bgcolor;?>" onClick="js_set_value('<? echo $row[csf('booking_no')]  ?>')" style="cursor:pointer">
                        <td width="40"><? echo $i;?></td> 
                        <td width="80"><? echo $row[csf('booking_no_prefix_num')];?></td>  
                        <td width="80"><? echo $booking_type[$row[csf('booking_type')]];?></td>  
                        <td width="80"><? echo date("d-m-Y",strtotime($row[csf('booking_date')]));?></td>           	 
                        <td width="100"><? echo $buyer_arr[$row[csf('buyer_id')]];?></td>
                        <td width="120"><? echo $item_category[$row[csf('item_category')]];?></td>
                        <td width="80"><? echo $fabric_source[$row[csf('fabric_source')]];?></td>
                        <td width="80">
                        <? echo $pay_mode[$row[csf('pay_mode')]];?>
                        </td>
                        <td width="100">
                        <? 
                        if($row[csf('pay_mode')]==3 || $row[csf('pay_mode')]==5){
                            echo $comp[$row[csf('supplier_id')]];
                        }
                        else{
                            echo $suplier[$row[csf('supplier_id')]];
                        }
                        ?>
                        </td>
                        <td width="" style="word-wrap: break-word;word-break: break-all;"><? echo $row[csf('style_ref_no')];?></td>

                    </tr>
                    <?
                    $i++;
                     }
                    ?>
                </tbody>
            </table>
        </div>
    <?
}

if ($action == "finish_store") 
{
    echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');
    $store_array=return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');
    $color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
    extract($_REQUEST);
    ?>
    <script>
        var tableFilters = {
            col_operation: {
                id: ["value_batch_total_id"],
                col: [4],
                operation: ["sum"],
                write_method: ["innerHTML"]
            }
        }
        $(document).ready(function (e) {
            setFilterGrid('table_body_popup', -1, tableFilters);
        });

        function print_window()
        {
            document.getElementById('scroll_body').style.overflow = "auto";
            document.getElementById('scroll_body').style.maxHeight = "none";

            var w = window.open("Surprise", "#");
            var d = w.document.open();
            d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
                    '<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');

            d.close();
            document.getElementById('scroll_body').style.overflowY = "scroll";
            document.getElementById('scroll_body').style.maxHeight = "230px";
        }
    </script>   
    <div style="width:675px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
    <fieldset style="width:670px; margin-left:7px">
        <div id="report_container">

        	<?
			if ($batch_id!="")
			{
				// finish fabric batch sql
				$batch_sql="SELECT a.id as batch_id, a.batch_no, a.color_id, b.item_description, a.extention_no, b.batch_qnty as batch_qnty
				from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  
				and a.id in($batch_id) order by a.batch_no";
				// echo $batch_sql;die;
				$batch_sql_result=sql_select($batch_sql);
				$finish_process_data_arr=array();$finish_process_data_arr=array();$process_id_arr=array();$process_wise_qty_arr=array();$batch_color_arr=array();
				foreach ($batch_sql_result as $key => $row)
				{
					$finish_process_data_arr[$row[csf('batch_id')]][$row[csf('color_id')]][$row[csf('extention_no')]]['batchQnty']+=$row[csf('batch_qnty')];
					$finish_process_data_arr[$row[csf('batch_id')]][$row[csf('color_id')]][$row[csf('extention_no')]]['batchNo']=$row[csf('batch_no')];
					$finish_process_data_arr[$row[csf('batch_id')]][$row[csf('color_id')]][$row[csf('extention_no')]]['itemDescription'].=$row[csf('item_description')].'*';
					$batch_color_arr[$row[csf('batch_id')]]=$row[csf('color_id')];
				}
				
				$fin_fab_recv_issue_sql="SELECT b.pi_wo_batch_no as batch_id, c.color_id, b.store_id, sum(c.quantity) as receive_qnty, 0 as issue_qnty
				from inv_transaction b, order_wise_pro_details c 
				where b.id=c.trans_id and b.transaction_type=1 and b.booking_without_order=0 and c.entry_form=37 and b.item_category=2 and b.pi_wo_batch_no in($batch_id)
				group by b.pi_wo_batch_no, b.store_id, c.color_id
				union all 
				SELECT b.pi_wo_batch_no as batch_id, d.color_id, b.store_id, 0 as receive_qnty, sum(b.cons_quantity) as issue_qnty
				from inv_transaction b, inv_finish_fabric_issue_dtls c, order_wise_pro_details d 
				where b.id=c.trans_id and b.id=d.trans_id and b.status_active=1 and b.is_deleted=0 and b.item_category=2 and b.transaction_type=2 and c.status_active=1 and c.is_deleted=0 and b.pi_wo_batch_no in($batch_id)
				group by b.pi_wo_batch_no, d.color_id, b.store_id";
				$fin_fab_recv_issue_result=sql_select($fin_fab_recv_issue_sql);
				$store_id_data_arr=array();$store_wise_recv_qty_arr=array();
				$store_wise_issue_qty_arr=array();
				foreach ($fin_fab_recv_issue_result as $key => $row)
				{
					$store_wise_recv_qty_arr[$row[csf('batch_id')]][$row[csf('color_id')]][$row[csf('store_id')]]["receive_qnty"]+=$row[csf('receive_qnty')];
					$store_wise_issue_qty_arr[$row[csf('batch_id')]][$row[csf('color_id')]][$row[csf('store_id')]]["issue_qnty"]+=$row[csf('issue_qnty')];
					if ($row[csf('store_id')]!="") 
					{
						$store_id_data_arr[$row[csf('store_id')]]=$row[csf('store_id')];
					}
				}
				$count_store=count($store_id_data_arr);
			}

			// Finish Fabric Delivery to Store popup sql
			$fin_fab_delivery_popup_sql="SELECT b.batch_id, b.color_id, sum(b.current_delivery) as finish_delivery
			from pro_grey_prod_delivery_mst a, pro_grey_prod_delivery_dtls b 
			where a.id=b.mst_id and a.entry_form=54 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.batch_id in($batch_id) group by b.batch_id, b.color_id";
			// echo $fin_fab_delivery_sql;
			$fin_fab_delivery_popup_result=sql_select($fin_fab_delivery_popup_sql);
			$fin_fab_delivery_data_popup_arr=array();
			foreach ($fin_fab_delivery_popup_result as $key => $row)
			{
				$fin_fab_delivery_data_popup_arr[$row[csf('batch_id')]][$row[csf('color_id')]]['finish_delivery_qty']+=$row[csf('finish_delivery')];
			}
			// echo "<pre>";print_r($delivery_qty_arr);die;
			// Finish Fabric Delivery to Store popup sql end
			
			$table_width=680;
			$div_width=698;
			$table_width2=680;
			?>
			<div style="width:<? echo $table_width+20+($count_store*80); ?>px; float:left;">
				<table width="<? echo $table_width+($count_store*80); ?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" style="float:left; margin-top: 10px;">
					<thead>
						<tr>
							<th rowspan="2" width="40">SL</th>
							<th rowspan="2" width="70">Batch No</th>
							<th rowspan="2" width="40">Ext</th>
							<th rowspan="2" width="70">Colour</th>
							<th rowspan="2" width="100">Construction</th>
							<th rowspan="2" width="80">Batch Qty</th>
							<th rowspan="2" width="80">Delivery Qty</th>
							<?
		                    foreach($store_id_data_arr as $store_idv)
		                    {
		                    	?>
		                    	<th colspan="2" width="80" title="<? echo $store_idv; ?>" style="word-wrap:break-word; word-break: break-all;"><? echo $store_array[$store_idv]; //$entry_form?></th>
		                    	<?
		                    }  
		                    ?>
						</tr>
						<tr>
							<?
		                    foreach($store_id_data_arr as $store_idv)
		                    {
		                    	?>
		                    	<th width="80" style="word-wrap:break-word; word-break: break-all;"><? echo 'Rcv Qty';?></th>
		                    	<th width="80" style="word-wrap:break-word; word-break: break-all;"><? echo 'Issue Qty';?></th>
		                    	<?
		                    }  
		                    ?>
						</tr>
					</thead>
				</table>
				
				<div style="width:<? echo $div_width+($count_store*80); ?>px; float:left; max-height:300px; overflow-y:scroll" id="scroll_body">
				<table width="<? echo $table_width2+($count_store*80); ?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_body_show6" style="float:left;">
					<tbody>
					<?
					$i=1;
					$total_batchQnty=0;$total_fin_fab_delivery_qty=0;
					foreach ($finish_process_data_arr as $batch_no_key => $batch_id_val)
					{
						foreach ($batch_id_val as $color_id_key => $batch_color_id) 
						{
							foreach ($batch_color_id as $extention_key => $row) 
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								$construction=array_filter(explode('*', $row['itemDescription']));
								$cons_var="";
								foreach ($construction as $key => $value) 
								{
									$construction2=explode(',', $value);
									$cons_var.=($cons_var=="")?$construction2[0]:",".$construction2[0];
								}
								$unique_cons_var=implode(",", array_unique(explode(",", $cons_var)));
								$fin_fab_delivery_qty=$fin_fab_delivery_data_popup_arr[$batch_no_key][$color_id_key]['finish_delivery_qty']
								?>						
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>');" id="tr_2nd<? echo $i; ?>">
									<td width="40" align="center"><p><? echo $i; ?></p></td>
									<td width="70" align="center" style="word-wrap:break-word; word-break: break-all;"><p><? echo $row['batchNo']; ?></p></td>
									<td width="40" align="center"><p><? echo $extention_key; ?></p></td>
									<td width="70"><p><? echo $color_arr[$color_id_key]; ?></p></td>
									<td width="100" align="center"><p><? echo $unique_cons_var; ?></p></td>
									<td width="80" align="right" ><p><? echo number_format($row['batchQnty'],2); ?></p></td>
									<td width="80" align="right"><p><? echo $fin_fab_delivery_qty; ?></p></td>

									<?

				                    foreach($store_id_data_arr as $store_idv)
				                    {
				                    	$store_wise_recv_qty=$store_wise_recv_qty_arr[$batch_no_key][$color_id_key][$store_idv]["receive_qnty"];
				                    	$store_wise_issue_qty=$store_wise_issue_qty_arr[$batch_no_key][$color_id_key][$store_idv]["issue_qnty"];

				                    	$total_fin_recv_qty_arr[$store_idv]+=$store_wise_recv_qty_arr[$batch_no_key][$color_id_key][$store_idv]["receive_qnty"];
				                    	$total_fin_issue_qty_arr[$store_idv]+=$store_wise_issue_qty_arr[$batch_no_key][$color_id_key][$store_idv]["issue_qnty"];
				                    	?>
				                    	<td width="80" title="<? echo "batch_id:".$batch_no_key.',color_id:'.$color_id_key.',extention:'.$extention_key.',stor_id:'.$store_idv; ?>" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($store_wise_recv_qty,2); ?></td>
				                    	<td width="80" title="<? echo "batch_id:".$batch_no_key.',color_id:'.$color_id_key.',extention:'.$extention_key.',stor_id:'.$store_idv; ?>" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($store_wise_issue_qty,2); ?></td>
				                    	<?
				                    } 
				                    ?>
								</tr>
								<?
								$total_batchQnty+=$row['batchQnty'];
								$total_fin_fab_delivery_qty+=$fin_fab_delivery_qty;
								$i++;
							}								
						}
					}
					?>
					</tbody>
				</table>
				</div>
				<table width="<? echo $table_width2+($count_store*80); ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="report_table_footer" align="left">
					<tfoot>
						<tr style="background-color:#CCCCCC;">
							<th width="40"></th>
							<th width="70"></th>
							<th width="40"></th>
							<th width="70"></th>
							<th width="100" align="right">Total</th>
							<th width="80" align="right"><? echo number_format($total_batchQnty,2); ?></th>
							<th width="80" align="right"><? echo number_format($total_fin_fab_delivery_qty,2); ?></th>
							<?
		                    foreach($store_id_data_arr as $store_idv)
		                    {
		                    	$fin_recv_qty_total = $total_fin_recv_qty_arr[$store_idv];
		                    	$fin_issue_qty_total = $total_fin_issue_qty_arr[$store_idv];
		                    	?>
		                    	<th width="80" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($fin_recv_qty_total,2); ?></th>
		                    	<th width="80" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($fin_issue_qty_total,2); ?></th>
		                    	<?
		                    } 
		                    ?>		
						</tr>
					</tfoot>
				</table>
			</div>
        </div>
    </fieldset>  

    <?
    exit();
}

if ($action == "grey_feb_store") 
{
    echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');
    $store_array=return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');
    $color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
    $construction_arr = return_library_array("select id, construction from lib_yarn_count_determina_mst", "id", "construction");
    $party_library = return_library_array("select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=20 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id", "supplier_name");
    $company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
    extract($_REQUEST);
    ?>
    <script>
        var tableFilters = {
            col_operation: {
                id: ["value_batch_total_id"],
                col: [4],
                operation: ["sum"],
                write_method: ["innerHTML"]
            }
        }
        $(document).ready(function (e) {
            setFilterGrid('table_body_popup', -1, tableFilters);
        });

        function print_window()
        {
            document.getElementById('scroll_body').style.overflow = "auto";
            document.getElementById('scroll_body').style.maxHeight = "none";

            var w = window.open("Surprise", "#");
            var d = w.document.open();
            d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
                    '<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');

            d.close();
            document.getElementById('scroll_body').style.overflowY = "scroll";
            document.getElementById('scroll_body').style.maxHeight = "230px";
        }
    </script>   
    <div style="width:675px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
    <fieldset style="width:670px; margin-left:7px">
        <div id="report_container">

        	<?
			// =======================================Start
			if ($programNo!="") 
			{
			    $programNo_arry = explode(",","'".$programNo."'");
			    $program_no="";
			    foreach ($programNo_arry as $key => $program) 
			    {
			        if ($program_no=="") 
			        {
			            $program_no.= $program;
			        }
			        else 
			        {
			            $program_no.= "','".$program;
			        }
			    }
			    // echo $program_no;
				// only main query is Knit Grey Fabric Roll Receive
				// echo $programNo;die;
				$program_arr=explode(',', $programNo);
				$all_program_no_arr = array_filter($program_arr);
				if(count($all_program_no_arr) > 0)
				{
					$all_program_no_cond_id = implode(",", $all_program_no_arr);
					$programCond = $program_booking_cond = "";
					if($db_type==2 && count($all_program_no_arr)>999)
					{
						$all_program_no_cond_id_chunk=array_chunk($all_program_no_arr, 999);
						foreach($all_program_no_cond_id_chunk as $chunk_prog)
						{
							$chunk_prog_val=implode(",", $chunk_prog);
							if(!$program_booking_cond)$program_booking_cond.=" and ( a.booking_id in($chunk_prog_val) ";
							else $program_booking_cond.=" or  a.booking_id in($chunk_prog_val) ";
						}
						$program_booking_cond.=")";
					}
					else
					{
						$program_booking_cond=" and a.booking_id in($all_program_no_cond_id)";
					}
				}
				$production_info_sql="SELECT a.id, a.booking_id as program_non, c.barcode_no
				from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
				where a.id=b.mst_id and a.id=c.mst_id and b.id=c.dtls_id $program_booking_cond and a.entry_form=2 and a.receive_basis=2
				group by a.id, a.booking_id, c.barcode_no";
				// echo $production_info_sql;die; // and a.booking_id in(9600)
				$production_info_result=sql_select($production_info_sql);
				$production_barcode_arr=array();
				foreach ($production_info_result as $key => $value) 
				{
					$production_barcode_arr[$value[csf("barcode_no")]]=$value[csf("barcode_no")];
				}

				$all_production_barcode_arr = array_filter($production_barcode_arr);
				if(count($all_production_barcode_arr) > 0)
				{
					$all_barcode_no = implode(",", $all_production_barcode_arr);
					$barcode_no_Cond = $barcode_no_cond = "";
					if($db_type==2 && count($all_production_barcode_arr)>999)
					{
						$all_production_barcode_chunk=array_chunk($all_production_barcode_arr, 999);
						foreach($all_production_barcode_chunk as $chunk_barcode)
						{
							$chunk_barcode_val=implode(",", $chunk_barcode);
							if(!$barcode_no_cond)$barcode_no_cond.=" and ( c.barcode_no in($chunk_barcode_val) ";
							else $barcode_no_cond.=" or  c.barcode_no in($chunk_barcode_val) ";
						}
						$barcode_no_cond.=")";
					}
					else
					{
						$barcode_no_cond=" and c.barcode_no in($all_barcode_no)";
					}

					$grey_recv_popup_sql="SELECT a.recv_number as sys_no, b.transaction_type, a.store_id, c.booking_no as program_no, d.determination_id, c.qnty as recv_qty, 0 as issue_qty, a.entry_form, e.color_id, e.knitting_party, e.knitting_source
					from inv_receive_master a, inv_transaction b, pro_roll_details c, ppl_planning_entry_plan_dtls d, ppl_planning_info_entry_dtls e
					where a.id=b.mst_id and b.pi_wo_batch_no=c.dtls_id and c.booking_no=d.dtls_id and c.booking_no=e.id and c.booking_no=d.dtls_id and e.id=d.dtls_id
					and a.entry_form=58 and c.entry_form=58 and b.transaction_type=1 and b.booking_without_order=0 and c.booking_without_order=0 and b.transaction_type=1
					and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.booking_no in($program_no) $barcode_no_cond
					UNION ALL
					SELECT a.issue_number as sys_no, b.transaction_type, b.store_id, c.booking_no as program_no, e.determination_id, 0 as recv_qty, c.qnty as issue_qty, a.entry_form, f.color_id, f.knitting_party, f.knitting_source
					from inv_issue_master a, inv_transaction b, pro_roll_details c, order_wise_pro_details d, ppl_planning_entry_plan_dtls e, ppl_planning_info_entry_dtls f
					where a.id=b.mst_id and a.id=c.mst_id and b.id=d.trans_id and d.dtls_id=c.dtls_id and c.booking_no=e.dtls_id and c.booking_no=f.id and f.id=e.dtls_id
					and a.entry_form=61 and c.is_returned=0 and d.entry_form=61 and c.entry_form=61 and b.booking_without_order=0 and b.transaction_type=2
					and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  
					and c.booking_no in($program_no) $barcode_no_cond";
				}
				else{
					echo "No Data Found";
					disconnect($con);die;
				}				
				// echo $grey_recv_popup_sql;die;
				$grey_recv_popup_result=sql_select($grey_recv_popup_sql);
				$grey_fabric_recv_data_arr=array(); $store_id_data_arr=array();$store_wise_data_arr=array();
				$sys_no_arr=array();
				foreach ($grey_recv_popup_result as $key => $row) 
				{					
					$grey_fabric_recv_data_arr[$row[csf('program_no')]][$row[csf('determination_id')]]['color_id']=$row[csf('color_id')];
					$grey_fabric_recv_data_arr[$row[csf('program_no')]][$row[csf('determination_id')]]['knitting_party']=$row[csf('knitting_party')];
					$grey_fabric_recv_data_arr[$row[csf('program_no')]][$row[csf('determination_id')]]['knitting_source']=$row[csf('knitting_source')];

					$store_wise_data_arr[$row[csf('program_no')]][$row[csf('determination_id')]][$row[csf('store_id')]][$row[csf('transaction_type')]]['sys_no']=$row[csf('sys_no')];
					$store_wise_data_arr[$row[csf('program_no')]][$row[csf('determination_id')]][$row[csf('store_id')]][$row[csf('transaction_type')]]['recv_qty']+=$row[csf('recv_qty')];
					$store_wise_data_arr[$row[csf('program_no')]][$row[csf('determination_id')]][$row[csf('store_id')]][$row[csf('transaction_type')]]['issue_qty']+=$row[csf('issue_qty')];

					if ($row[csf('store_id')]!="") 
					{
						$store_id_data_arr[$row[csf('store_id')]]=$row[csf('store_id')];
					}
				}
				$count_store=count($store_id_data_arr);
			}
			// echo "<pre>";print_r($grey_fabric_recv_data_arr);die;
			// =======================================End
			
			$table_width=1020;
			$div_width=1038;
			$table_width2=1020;
			?>
			<div style="width:<? echo $table_width+20+($count_store*80); ?>px; float:left;">
				<table width="<? echo $table_width+($count_store*80); ?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" style="float:left; margin-top: 10px;">
					<thead>
						<tr>
							<th rowspan="2" width="40">SL</th>
							<th rowspan="2" width="70">Prog No</th>
							<th rowspan="2" width="100">Prog Company</th>
							<th rowspan="2" width="80">Construction</th>
							<th rowspan="2" width="70">Colour</th>
							<?
		                    foreach($store_id_data_arr as $key => $store)	
		                    {
		                    		?>
		                    	<th colspan="4" width="320" title="<? echo $store; ?>" style="word-wrap:break-word; word-break: break-all;"><? echo $store_array[$store]; //$entry_form?></th>
		                    	<?	                    	
		                    }  
		                    ?>
		                    <th rowspan="2" width="80">Stock Qty</th>
						</tr>
						<tr>
							<?
		                    foreach($store_id_data_arr as $key => $store_idv)	
		                    {
			                    	?>
			                    	<th width="80" style="word-wrap:break-word; word-break: break-all;"><? echo 'Rcv No';?></th>
			                    	<th width="80" style="word-wrap:break-word; word-break: break-all;"><? echo 'Rcv Qty';?></th>
			                    	<th width="80" style="word-wrap:break-word; word-break: break-all;"><? echo 'Issue No';?></th>
			                    	<th width="80" style="word-wrap:break-word; word-break: break-all;"><? echo 'Issue Qty';?></th>
			                    	<?
		                	}
		                    ?>
						</tr>
					</thead>
				</table>
				
				<div style="width:<? echo $div_width+($count_store*80); ?>px; float:left; max-height:300px; overflow-y:scroll" id="scroll_body">
				<table width="<? echo $table_width2+($count_store*80); ?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_body_show6" style="float:left;">
					<tbody>
					<?
					$i=1; $total_stock_qty=0;
					foreach ($grey_fabric_recv_data_arr as $prog_key => $prog_val)
					{
						foreach ($prog_val as $determination_key => $row) 
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							?>						
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>');" id="tr_2nd<? echo $i; ?>">
								<td width="40" align="center"><p><? echo $i; ?></p></td>
								<td width="70" align="center" style="word-wrap:break-word; word-break: break-all;"><p><? echo $prog_key; ?></p></td>
								<td width="100" align="center"><p><?
								echo $company_party=($row['knitting_source']==1) ? $company_library[$row["knitting_party"]] : $party_library[$row["knitting_party"]];
								?></p></td>
								<td width="80" align="center"><p><? echo $construction_arr[$determination_key]; ?></p></td>
								<td width="70"><p><? echo $color_arr[$row["color_id"]]; ?></p></td>
								<?
								$stock_qty=0;
			                    foreach($store_id_data_arr as $store_key => $store)	
			                    {
		                    		$store_wise_recv_no=$store_wise_data_arr[$prog_key][$determination_key][$store][1]['sys_no'];
		                    		$store_wise_recv_qty=$store_wise_data_arr[$prog_key][$determination_key][$store][1]['recv_qty'];
		                    		$store_wise_issue_no=$store_wise_data_arr[$prog_key][$determination_key][$store][2]['sys_no'];
		                    		$store_wise_issue_qty=$store_wise_data_arr[$prog_key][$determination_key][$store][2]['issue_qty'];

			                    	$total_fin_recv_qty_arr[$store]+=$store_wise_data_arr[$prog_key][$determination_key][$store][1]["recv_qty"];
			                    	$total_fin_issue_qty_arr[$store]+=$store_wise_data_arr[$prog_key][$determination_key][$store][2]["issue_qty"];

			                    	$stock_qty=$store_wise_recv_qty-$store_wise_issue_qty;
			                    	?>
			                    	<td width="80" title="<? echo ""; ?>" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo $store_wise_recv_no; ?></td>
			                    	<td width="80" title="<? echo ""; ?>" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($store_wise_recv_qty,2); ?></td>
			                    	<td width="80" title="<? echo ""; ?>" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo $store_wise_issue_no; ?></td>
			                    	<td width="80" title="<? echo ""; ?>" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($store_wise_issue_qty,2); ?></td>
			                    	<?
			                    } 
			                    ?>
			                    <td width="80" align="right" ><p><? echo number_format($stock_qty,2); ?></p></td>
							</tr>
							<?
							$total_stock_qty+=$stock_qty;
							$i++;						
						}
					}
					?>
					</tbody>
				</table>
				</div>
				<table width="<? echo $table_width2+($count_store*80); ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="report_table_footer" align="left">
					<tfoot>
						<tr style="background-color:#CCCCCC;">
							<th width="40"></th>
							<th width="70"></th>
							<th width="100"></th>
							<th width="80"></th>
							<th width="70" align="right">Total</th>
							<?
							$total_stock_qty=0;
		                    foreach($store_id_data_arr as $key => $store)	
		                    {
		                    	$fin_recv_qty_total = $total_fin_recv_qty_arr[$store];
		                    	$fin_issue_qty_total = $total_fin_issue_qty_arr[$store];
		                    	?>
		                    	<th width="80" align="right" style="word-wrap:break-word; word-break: break-all;"></th>
		                    	<th width="80" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($fin_recv_qty_total,2); ?></th>
		                    	<th width="80" align="right" style="word-wrap:break-word; word-break: break-all;"></th>
		                    	<th width="80" align="right" style="word-wrap:break-word; word-break: break-all;"><? echo number_format($fin_issue_qty_total,2); ?></th>
		                    	<?
		                    }
		                    ?>	
		                    <th width="80"><? echo number_format($total_stock_qty,2); ?></th>	
						</tr>
					</tfoot>
				</table>
			</div>
        </div>
    </fieldset>  

    <?
    exit();
}
disconnect($con);
?>
