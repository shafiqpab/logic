<?
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");

require_once('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']['user_id'];

$data = $_REQUEST['data'];
$action = $_REQUEST['action'];

if ($action == "load_drop_down_cust_buyer") 
{
	if ($data == 0) 
	{
		echo create_drop_down("cbo_cust_buyer_id", 100, $blank_array, "", 1, "--Select Cust Buyer--", 0, "");
	}
	else  
	{
		echo create_drop_down("cbo_cust_buyer_id", 100, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90,80)) order by buy.buyer_name", "id,buyer_name", 1, "-- Select Cust Buyer --", $selected, "", 0);
	}
	exit();
}

$company_arr 	= return_library_array("select id, company_name from lib_company", "id", "company_name");
$buyer_arr 		= return_library_array("select id, short_name from lib_buyer", "id", "short_name");
$floor_details  = return_library_array("select id, floor_name from lib_prod_floor", "id", "floor_name");
$location_arr 	= return_library_array("select id, location_name from lib_location", 'id', 'location_name');
$dealing_marArr = return_library_array("select id, team_member_name from lib_mkt_team_member_info", 'id', 'team_member_name');
$teamldr_arr 	= return_library_array("select id, team_leader_name from lib_marketing_team", 'id', 'team_leader_name');
$teamName_arr	= return_library_array("select id, team_name from lib_marketing_team", 'id', 'team_name');
$buyerArr 		= return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
$userArr 		= return_library_array("select id, user_name from user_passwd", 'id', 'user_name');


if ($action == "sales_order_no_search_popup") 
{
	echo load_html_head_contents("Sales Order No Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(job_no) {
			document.getElementById('hidden_job_no').value = job_no;
			parent.emailwindow.hide();
		}
	</script>
	</head>

	<body>
		<div align="center">
			<form name="styleRef_form" id="styleRef_form">
				<fieldset style="width:0px;">
					<table cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
						<table cellpadding="0" cellspacing="0" width="600" border="1" rules="all" class="rpt_table">
							<thead>
								<th>Within Group</th>
								<th>Search By</th>
								<th>Search No</th>
								<th>
									<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
									<input type="hidden" name="hidden_job_no" id="hidden_job_no" value="">
								</th>
							</thead>
							<tr class="general">
								<td align="center">
									<?
									echo create_drop_down("cbo_within_group", 150, $yes_no, "", 0, "--Select--", 2, "", 1);
									?>
								</td>
								<td align="center">
									<?
									$serach_type_arr = array(1 => 'Sales Order No', 2 => 'Fab. Booking No');
									echo create_drop_down("cbo_serach_type", 150, $serach_type_arr, "", 0, "--Select--", "", "", 0);
									?>
								</td>
								<td align="center">
									<input type="text" style="width:140px" class="text_boxes" name="txt_search_common" id="txt_search_common" placeholder="Write" />
								</td>
								<td align="center">
									<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_serach_type').value+'_'+<?=$companyID;?>, 'create_sales_order_no_search_list', 'search_div', 'daily_sales_order_report_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
								</td>
							</tr>
						</table>
						<div style="margin-top:15px" id="search_div"></div>
					</table>
				</fieldset>
			</form>
		</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>

	</html>
	<?
	exit();
}

if ($action == "create_sales_order_no_search_list") 
{
	$data 			= explode('_', $data);
    //var_dump($data);
	$sales_order_no = trim($data[0]);
	$within_group 	= $data[1];
	$serach_type 	=  $data[2];
	$company_id 	=  $data[3];
	//echo $serach_type.'==';
	$location_arr 	= return_library_array("select id, location_name from lib_location", 'id', 'location_name');


	$within_group_cond  = ($within_group == 0) ? "" : " and a.within_group=$within_group";
	if ($serach_type == 1) {
		$sales_order_cond   = ($sales_order_no == "") ? "" : " and a.job_no like '%$sales_order_no%'";
	} else if ($serach_type == 2) {
		$sales_order_cond   = ($sales_order_no == "") ? "" : " and a.sales_booking_no like '%$sales_order_no%'";
	}
	$year_field 		= ($db_type == 2) ? "to_char(a.insert_date,'YYYY') as year" : "YEAR(a.insert_date) as year";

	$sql = "select a.id, $year_field, a.job_no_prefix_num, a.job_no, a.within_group, a.sales_booking_no,a.booking_date, a.buyer_id, a.style_ref_no, a.location_id from fabric_sales_order_mst a where a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 $within_group_cond $search_field_cond $sales_order_cond order by a.id";
	$result = sql_select($sql);
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="950" class="rpt_table">
		<thead>
			<th width="40">SL</th>
			<th width="90">Sales Order ID</th>
			<th width="110">Sales Order No</th>
			<th width="120">Booking No</th>
			<th width="80">Booking date</th>
			<th width="60">Year</th>
			<th width="80">Within Group</th>
			<th width="70">Buyer/Unit</th>
			<th width="110">Style Ref.</th>
			<th>Location</th>
		</thead>
	</table>
	<div style="width:950px; max-height:260px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="3" border="1" rules="all" width="930" class="rpt_table" id="tbl_list_search">
			<?
			$i = 1;
			foreach ($result as $row) {
				if ($i % 2 == 0) $bgcolor = "#E9F3FF";
				else $bgcolor = "#FFFFFF";

				if ($row[csf('within_group')] == 1) {
					$buyer = $company_arr[$row[csf('buyer_id')]];
				} else {
					$buyer = $buyer_arr[$row[csf('buyer_id')]];
				}
				$sales_order_no = $row[csf('job_no')];
			?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $sales_order_no; ?>');">
					<td width="40" align="center"><? echo $i; ?></td>
					<td width="90" align="center">
						<p>&nbsp;<? echo $row[csf('job_no_prefix_num')]; ?></p>
					</td>
					<td width="110" align="center">
						<p>&nbsp;<? echo $row[csf('job_no')]; ?></p>
					</td>
					<td width="120" align="center">
						<p><? echo $row[csf('sales_booking_no')]; ?></p>
					</td>
					<td width="80" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
					<td width="60" align="center">
						<p><? echo $row[csf('year')]; ?></p>
					</td>
					<td width="80" align="center"><? echo $yes_no[$row[csf('within_group')]]; ?></td>
					<td width="70" align="center" style="word-break: break-all; "><? echo $buyer; ?></td>
					<td width="110" align="center">
						<p><? echo $row[csf('style_ref_no')]; ?></p>
					</td>
					<td>
						<p><? echo $location_arr[$row[csf('location_id')]]; ?></p>
					</td>
				</tr>
			<?
				$i++;
			}
			?>
		</table>
	</div>
    <?
	exit();
}

if ($action == "report_generate") 
{
	
	$process = array(&$_POST);
	//var_dump($process);
	extract(check_magic_quote_gpc($process));
	$cbo_company_name 	  = str_replace("'", "", $cbo_company_name);
	$cbo_sales_order_type = str_replace("'", "", $cbo_sales_order_type);
	$cbo_cust_buyer_id    = str_replace("'", "", $cbo_cust_buyer_id);
	$sales_order_no 	  = str_replace("'", "", $txt_sales_order);
    $booking_no 		  = str_replace("'", "", $txt_booking_no);
    $date_type 			  = str_replace("'", "", $cbo_date_category);
	$date_from 			  = str_replace("'", "", $txt_date_from);
	$date_to 			  = str_replace("'", "", $txt_date_to);
	$report_type 		  = str_replace("'", "", $report_type);

	$search_field_cond="";
    if ($sales_order_no == "") $search_field_cond .= ""; else $search_field_cond .= " and a.job_no like '%$sales_order_no%'";
    if ($booking_no == "") $search_field_cond .= ""; else $search_field_cond .= " and a.sales_booking_no like '%$booking_no%'";
    if ($cbo_sales_order_type == 0) $search_field_cond .= ""; else $search_field_cond .= " and a.sales_order_type=$cbo_sales_order_type";
    if ($cbo_cust_buyer_id == 0) $search_field_cond .= ""; else $search_field_cond .= " and a.customer_buyer=$cbo_cust_buyer_id";
	
	if($date_type!=0)
	{
		if($date_from!="" && $date_to!="")
		{
			switch ($date_type) 
			{
			  	case 1:
			    	$search_field_cond .= " and a.insert_date between '$date_from' and '$date_to 11:59:59 PM'";
			    	break;
			  	case 2:
			    	$search_field_cond .= " and a.booking_date between '$date_from' and '$date_to'";
			    	break;
			  	case 3:
			    	$search_field_cond .= " and a.delivery_start_date between '$date_from' and '$date_to'";
			    	break;
			  	default:
			    	$search_field_cond .= " and a.delivery_date between '$date_from' and '$date_to'";
			}
		}
	}

	
	$ordByCond=="";

	if($date_type!=0 || $date_type==0)
	{
		switch ($date_type) 
		{
			case 2:
				$ordByCond = " a.booking_date ASC";
				break;
			case 3:
				$ordByCond = " a.delivery_start_date ASC";
				break;
			case 4:
				$ordByCond = " a.delivery_date ASC";
				break;
			default:
				$ordByCond = " a.insert_date ASC";
		}
		
	}

	$sql = "SELECT a.id,a.company_id, a.location_id, a.team_leader, a.job_no, a.sales_order_type, a.sales_booking_no, a.booking_id, a.booking_date, a.buyer_id, a.customer_buyer,a.dealing_marchant, a.style_ref_no, SUM((COALESCE(b.finish_qty,0))+(COALESCE(b.pp_qnty,0))+(COALESCE(b.mtl_qnty,0))+(COALESCE(b.fpt_qnty,0))+(COALESCE(b.gpt_qnty,0))) AS finish_qty,SUM(COALESCE(b.grey_qty,0)) AS grey_qty, a.delivery_start_date, a.delivery_date, a.inserted_by, a.insert_date, a.within_group 
	from  fabric_sales_order_mst a, fabric_sales_order_dtls b  
	where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$cbo_company_name $search_field_cond
	group by a.id, a.company_id, a.location_id, a.team_leader, a.job_no, a.sales_order_type, a.sales_booking_no, a.booking_id, a.booking_date,
	a.buyer_id, a.customer_buyer, a.dealing_marchant,a.style_ref_no, a.delivery_start_date, a.delivery_date, a.inserted_by, a.insert_date, a.within_group 
	order by $ordByCond";

	//echo $sql;//die;
	$result = sql_select($sql);

    ob_start();	
	?>
    <!-- ===================================== DETAILS PART START ===================================== -->
    <fieldset>
    	<div style="margin:0 auto;">		
	        <table width="2030" cellpadding="0" cellspacing="0"> 
	            <tr class="form_caption">
	            	<td colspan="17" align="center" style="font-weight: 600;font-size: 18px;">Daily Sales Order Report.</td> 
	            </tr>
	            <tr class="form_caption">
	            	<td colspan="17" align="center" style="font-weight: 600;font-size: 18px;"><? echo $company_arr[$cbo_company_name]; ?></td> 
	            </tr>
	            <tr class="form_caption">
	            	<td colspan="17" align="center" style="font-weight: 600;font-size: 15px;"><? echo "Date:  ".change_date_format($date_from)." to ".change_date_format($date_to); ?></td> 
	            </tr>
	        </table>
	    </div>
		<div>
			<table width="2030" cellspacing="0" border="1" class="rpt_table" rules="all" id="" align="left">
				<thead>
					<tr>
						<th width="30" >SL</th>
						<th width="150">Company Name</th>
						<th width="100">Location</th>
						<th width="100">Team <br>Name</th>
						<th width="100">Team <br>Laeader</th>
						<th width="100">Dealing <br> Merchant</th>
						<th width="100">Customer</th>
						<th width="100">Cust. <br>Buyer</th>
						<th width="100">Style</th>
						<th width="100">Sales <br>/Booking No</th>
						<th width="150">FSO No</th>
						<th width="100">Sales <br>Order Type</th>
						<th width="100">Confirm <br>Finish Qty</th>
						<th width="100">Required <br>Grey Qty</th>
						<th width="100">Booking <br>Date</th>
						<th width="100">Delivery <br>Start Date</th>
						<th width="100">Delivery <br>End Date</th>
						<th width="100">Lead Time <br>(days):</th>
						<th width="100">Insert By</th>
						<th width="100">Insert Date</th>
					</tr>	
								   
				</thead>
			</table>
			<div style="max-height:400px; overflow-y:scroll; width:2050px" id="scroll_body">
				<table cellspacing="0" cellpadding="0" border="1" class="rpt_table"  width="2030" rules="all"  align="left">
					<tbody id="table_body">
						<?
						$i=1;
						foreach ($result as $row) 
						{							
							$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF" ;
							?>
							<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>" style="font-size:11px">
								<td width="30" align="center"><p><?=$i;?></p></td>
								<td width="150" align="center"><p><?=$company_arr[$row[csf('company_id')]];?></p></td>
								<td width="100" align="center"><p><?=$location_arr[$row[csf('location_id')]];?></p></td>
								<td width="100" align="center"><p><?=$teamldr_arr[$row[csf('team_leader')]];?></p></td>
								<td width="100" align="center"><p><?=$teamName_arr[$row[csf('team_leader')]];?></p></td>
								<td width="100" align="center"><p><?=$dealing_marArr[$row[csf('dealing_marchant')]];?></p></td>
								<td width="100" align="center"><p><?=$buyer_arr[$row[csf('buyer_id')]];?></p></td>
								<td width="100" align="center"><p><?=$buyerArr[$row[csf('customer_buyer')]];?></p></td>
								<td width="100" align="center"><p><?=$row[csf('style_ref_no')];?></p></td>
								<td width="100" align="center"><p><?=$row[csf('sales_booking_no')];?></p></td>
								<td width="150" align="center">
									<p>
										<?="<a href='##' onclick=\"generate_report(" . $row[csf('company_id')] . ",'" . $row[csf('booking_id')] . "','" . $row[csf('sales_booking_no')] . "','" . $row[csf('job_no')] . "','" . $row[csf('within_group')] . "' )\">".$row[csf('job_no')]."</a>";?>
									</p>
								</td>
								<td width="100" align="center"><p><?=$sales_order_type_arr[$row[csf('sales_order_type')]];?></p></td>
								<td width="100" align="right"><p><?=number_format($row[csf('finish_qty')],2);?></p></td>
								<td width="100" align="right"><p><?=number_format($row[csf('grey_qty')],2);?></p></td>
								<td width="100" align="center"><p><?=change_date_format($row[csf('booking_date')]);?></p></td>
								<td width="100" align="center"><p><?=change_date_format($row[csf('delivery_start_date')]);?></p></td>
								<td width="100" align="center"><p><?=change_date_format($row[csf('delivery_date')]);?></p></td>
								<td width="100" align="center"><p><?=datediff("d", $row[csf('booking_date')], $row[csf('delivery_date')]);?></p></td>
								<td width="100" align="center"><p><?=$userArr[$row[csf('inserted_by')]];?></p></td>
								<td width="100" align="center"><p><?=change_date_format($row[csf('insert_date')]);?></p></td>
							</tr>
							<?
							$i++;
							$total_finish_qty += $row[csf('finish_qty')];
							$total_grey_qty   += $row[csf('grey_qty')];
						}
						?>
					</tbody>										
				</table>										  
			</div>
			<table width="2030" cellspacing="0" border="1" class="rpt_table" rules="all" id="" align="left">
				<tfoot>
					<tr>
					<th width="30"></th>
					<th width="150"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="150"></th>
					<th width="100">Total:</th>
					<th width="100"><?=number_format($total_finish_qty,2);?></th>
					<th width="100"><?=number_format($total_grey_qty,2);?></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					<th width="100"></th>
					</tr>
				</tfoot>
			</table>	
			
		</div>	
	 </fieldset> 
	 <script type="text/javascript">
	 	
	 	setFilterGrid("table_body",-1);
	 </script>
    <?
    unset($data_array);
    foreach (glob("$user_id*.xls") as $filename) {
    @unlink($filename);
    }
    //---------end------------//	
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename";
	exit();	 
}