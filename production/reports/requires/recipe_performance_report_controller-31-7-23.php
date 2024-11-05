<?
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");

require_once('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']['user_id'];

$data = $_REQUEST['data'];
$action = $_REQUEST['action'];

if ($action=="load_drop_in_charge"){
	echo create_drop_down( "cbo_in_charge_id", 100, "select b.id, b.first_name from lib_employee b where b.in_charge like '%2%' and b.company_id=$data and b.status_active=1 and b.is_deleted=0","id,first_name", 1, "-- Select --", $selected, "","","" );
	exit();
}
if ($action == "batch_popup") 
{
	echo load_html_head_contents("Batch Info", "../../../", 1, 1, '', '1', '');
	extract($_REQUEST);
	?>
	<script>

		function js_set_value(batch_id,batch_no) 
		{
			document.getElementById('hidden_batch_id').value = batch_id;
			document.getElementById('hidden_batch_no').value = batch_no;
			parent.emailwindow.hide();
		}

	</script>
	</head>
	<body>
		<div align="center">
			<fieldset style="width:630px;margin-left:4px;">
				<form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
					<table cellpadding="0" cellspacing="0" width="600" border="1" rules="all" class="rpt_table">
						<thead>
							<th>Search By</th>
							<th>Search</th>
							<th colspan="2">Batch Date</th>
							
							<th>
								<input type="reset" name="reset" id="reset" value="Reset" style="width:100px"
								class="formbutton"/>
								<input type="hidden" name="hidden_batch_id" id="hidden_batch_id" value="">
								<input type="hidden" name="hidden_batch_no" id="hidden_batch_no" value="">
							</th>
						</thead>
						<tr class="general">
							<td align="center">
								<?
								$search_by_arr = array(1 => "Batch No", 2 => "Booking No");
								echo create_drop_down("cbo_search_by", 150, $search_by_arr, "", 0, "--Select--", "", $dd, 0);
								?>
							</td>
							<td align="center">
								<input type="text" style="width:140px" class="text_boxes" name="txt_search_common"
								id="txt_search_common"/>
							</td>
							<td align="center" colspan="2">
                            <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" readonly>To
                            <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" readonly>
                       	 </td>	

							<td align="center">
								<input type="button" name="button2" class="formbutton" value="Show"
								onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $company_name; ?>+'_'+<? echo $cbo_result; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $type; ?>, 'create_batch_search_list_view', 'search_div', 'recipe_performance_report_controller', 'setFilterGrid(\'tbl_list_search\',-1);')"
								style="width:100px;"/>
							</td>
						</tr>
					</table>
					<div id="search_div" style="margin-top:10px"></div>
				</form>
			</fieldset>
		</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}

if ($action == "create_batch_search_list_view") 
{
	$data = explode('_', $data);

	$search_string = "%" . trim($data[0]) . "%";
	$search_by = $data[1];
	$company_id = $data[2];
	$result_id = $data[3];
	$start_date = $data[4];
	$end_date = $data[5];
	$type_id = $data[6];

	if($data[0]=="" && $start_date=="" && $end_date=="")
	{
		echo "<b style='font-size:20px'> Please select date range. </b>";die;
	}

	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and a.batch_date between '".change_date_format($start_date,"yyyy-mm-dd", "-")."' and '".change_date_format($end_date,"yyyy-mm-dd", "-")."'";
		}
		else
		{
			$date_cond="and a.batch_date between '".change_date_format($start_date,'','',1)."' and '".change_date_format($end_date,'','',1)."'";	
		}
	}
	else
	{
		$date_cond="";
	}

	if ($search_by == 1)
		$search_field = 'a.batch_no';
	else
		$search_field = 'a.booking_no';

	// $batch_cond = "";
	// if ($batch_against_id != 2) $batch_cond = " and a.batch_against=$batch_against_id";
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	if($type_id==1)
	{
		$sql ="select a.id, a.batch_no,a.sales_order_no, a.extention_no, a.batch_weight, a.total_trims_weight, a.batch_date, a.batch_against, a.batch_for, a.booking_no, a.color_id,b.is_sales,a.re_dyeing_from from pro_batch_create_mst a,pro_batch_create_dtls b,pro_fab_subprocess f where a.id=b.mst_id and a.id=f.batch_id and a.company_id=$company_id and $search_field like '$search_string' and a.page_without_roll=0 and f.load_unload_id=2 and a.status_active=1 and a.entry_form=0 and a.is_deleted=0 $batch_cond $date_cond group by a.id, a.batch_no,a.sales_order_no, a.extention_no, a.batch_weight, a.total_trims_weight, a.batch_date, a.batch_against, a.batch_for, a.booking_no, a.color_id,b.is_sales,a.re_dyeing_from order by a.batch_date desc";
		$ttl_msg="Booking No";
	}
	else
	{
		 $sql ="select b.id, a.batch_no,a.sales_order_no, a.extention_no, a.batch_weight, a.total_trims_weight, a.batch_date, a.batch_against, a.batch_for, a.booking_no, a.color_id,a.is_sales,a.re_dyeing_from from pro_batch_create_mst a,pro_recipe_entry_mst b,pro_fab_subprocess f where a.id=b.batch_id and a.id=f.batch_id  and b.batch_id=f.batch_id and f.load_unload_id=2 and a.company_id=$company_id and $search_field like '$search_string' and a.page_without_roll=0 and a.status_active=1 and a.entry_form=0 and a.is_deleted=0 $batch_cond $date_cond   order by a.batch_date desc";
		$ttl_msg="Recipe No";
	}

	
	// echo $sql;
	$result = sql_select($sql);


	if(count($result)<1)
	{
		echo "<span>Data Not Found</span>";die;
	}
	$batch_id=array();
	foreach ($result as $row) {
		$ids = implode(",", array_unique(explode(",", $row[csf("po_id")])));
		$po_ids .= $ids . ",";
		$is_sales[] = $row[csf("is_sales")];
		$batch_id[] .= $row[csf("id")];
	}
	

	?>
	<style>
		table tbody tr td {
			text-align: center;
		}
	</style>
	<table class="rpt_table" id="rpt_tabletbl_list_search" rules="all" width="920" cellspacing="0" cellpadding="0"
	border="0">
	<thead>
		<tr>
			<th width="50">SL No</th>
			<th width="100">Batch No</th>
			<th width="70">Ext. No</th>
			<th width="105"><?=$ttl_msg?></th>
			<th width="80">Batch Weight</th>
			<th width="80">Total Trims Weight</th>
			<th width="80">Batch Date</th>
			<th width="80">Batch Against</th>
			<th width="85">Batch For</th>
			<th>Color</th>
		</tr>
	</thead>
	<tbody>
		<?
		$i = 1;
		foreach ($result as $row)
		{
			
				if ($row[csf("is_sales")] != 1) {
					$order_id = array_unique(explode(",", $row[csf("po_id")]));
					$order_ids = "";
					foreach ($order_id as $order) {
						$order_ids .= $po_name_arr[$order] . ",";
					}
				} else {
					$order_ids = $row[csf("sales_order_no")];
				}
				if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
				if($type_id==1)
				{
					$booking_recipe=$row[csf("booking_no")];
				}
				else{
					$booking_recipe=$row[csf("id")];
				}

				?>
				<tr onClick="js_set_value(<? echo $row[csf('id')]; ?>,'<? echo $row[csf('batch_no')]; ?>')" bgcolor="<? echo $bgcolor; ?>" style="cursor:pointer">
					<td width="50"><? echo $i; ?></td>
					<td width="100"><? echo $row[csf("batch_no")]; ?></td>
					<td width="70"><? echo $row[csf("extention_no")]; ?></td>
					
					<td width="105"><? echo $booking_recipe; ?></td>
					<td width="80"><? echo $row[csf("batch_weight")]; ?></td>
					<td width="80"><? echo $row[csf("total_trims_weight")]; ?></td>
					<td width="80"><? echo $row[csf("batch_date")]; ?></td>
					<td width="80"><? echo $batch_against[$row[csf("batch_against")]]; ?></td>
					<td width="85"><? echo $batch_for[$row[csf("batch_for")]]; ?></td>
					<td><? echo $color_arr[$row[csf("color_id")]]; ?></td>
				</tr>
				<?
				$i++;
			
		}
		?>
	</tbody>
	</table>
	<?
	exit();
}

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
								<th>Year</th>
								<th>Search By</th>
								<th>Search No</th>
								<th>
									<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
									<input type="hidden" name="hidden_job_no" id="hidden_job_no" value="">
									<input type="hidden" name="hidden_yearID" id="hidden_yearID" value="<? echo $yearID; ?>">
								</th>
							</thead>
							<tr class="general">
								<td align="center">
									<?
									echo create_drop_down("cbo_within_group", 150, $yes_no, "", 1, "--Select--", $cbo_within_group, $dd, 0);
									?>
								</td>
								<td>
									<? echo create_drop_down("cbo_year", 70, create_year_array(), "", 1, "-- All --", date("Y", time()), "", 0, ""); ?>
		                        </td>
								<td align="center">
									<?
									$serach_type_arr = array(1 => 'FSO No', 2 => 'Booking No', 3 => 'Style Ref.');
									echo create_drop_down("cbo_serach_type", 150, $serach_type_arr, "", 0, "--Select--", "", "", 0);
									?>
								</td>
								<td align="center">
									<input type="text" style="width:140px" class="text_boxes" name="txt_search_common" id="txt_search_common" placeholder="Write" />
								</td>
								<td align="center">
									<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_year').value+'_'+document.getElementById('cbo_serach_type').value, 'create_sales_order_no_search_list', 'search_div', 'knitting_consumption_report_sales_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
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
	$sales_order_no = trim($data[0]);
	$within_group 	= $data[1];
	$yearID 		=  $data[2];
	$serach_type 	=  $data[3];
	//echo $serach_type.'==';
	$location_arr 	= return_library_array("select id, location_name from lib_location", 'id', 'location_name');

	if ($db_type == 0) {
		if ($yearID != 0) $year_cond = " and YEAR(a.insert_date)=$yearID";
		else $year_cond = "";
	} else if ($db_type == 2) {
		if ($yearID != 0) $year_cond = " and to_char(a.insert_date,'YYYY')=$yearID";
		else $year_cond = "";
	}

	if ($serach_type == 1) 
	{
		$sales_order_cond   = ($sales_order_no == "") ? "" : " and a.job_no like '%$sales_order_no%'";
	} 
	else if ($serach_type == 2) 
	{
		$sales_order_cond   = ($sales_order_no == "") ? "" : " and a.sales_booking_no like '%$sales_order_no%'";
	}
	else if ($serach_type == 3) 
	{
		$sales_order_cond   = ($sales_order_no == "") ? "" : " and a.style_ref_no like '%$sales_order_no%'";
	}
	$year_field 		= ($db_type == 2) ? "to_char(a.insert_date,'YYYY') as year" : "YEAR(a.insert_date) as year";

	$sql = "SELECT a.id, $year_field, a.job_no_prefix_num, a.job_no, a.within_group, a.sales_booking_no,a.booking_date, a.buyer_id, a.style_ref_no, a.location_id from fabric_sales_order_mst a where a.status_active=1 and a.is_deleted=0  $search_field_cond $sales_order_cond $year_cond order by a.id";
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
			foreach ($result as $row) 
			{
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
	
	extract(check_magic_quote_gpc($process));
	 
	$txt_date_from = str_replace("'", "", $txt_date_from);
	$txt_date_to = str_replace("'", "", $txt_date_to);
	//$report_type = str_replace("'", "", $txt_batch_id);//cbo_in_charge_id*cbo_batch_type
	$txt_recipe_no = str_replace("'", "", $txt_recipe_no);
	$txt_batch_no = str_replace("'", "", $txt_batch_no);
	$cbo_method = str_replace("'", "", $cbo_method);
	$cbo_result = str_replace("'", "", $cbo_result);
	$cbo_company_id = str_replace("'", "", $cbo_company_name);
	$cbo_re_process = str_replace("'", "", $cbo_re_process);
	$report_type = str_replace("'", "", $report_type);
	//echo $report_type.'report_type';die;

	//txt_batch_id*txt_recipe_no*txt_batch_no*txt_date_from*txt_date_to*cbo_method*cbo_result*cbo_re_process

	//var_dump($txt_date_to);
	//in_charge_mst_id
	$method_cond="";
	if($cbo_method>0)
	{
		$method_cond=" and f.ltb_btb_id in($cbo_method)";
	}
	$result_cond="";
	if($cbo_result>0)
	{
		$result_cond=" and f.result in($cbo_result)";
	}
	$re_process_cond="";
	if($cbo_re_process>0)
	{
		$re_process_cond=" and b.dyeing_re_process in($cbo_re_process)";
	}
	$batch_no_cond="";
	if($txt_batch_no !="")
	{
		$batch_no_cond=" and a.batch_no='$txt_batch_no' ";
	}
	$recipe_no_cond="";
	if($txt_recipe_no !="")
	{
		$recipe_no_cond=" and b.id=$txt_recipe_no ";
	}

	//$txt_recipe_no

	$company_arr = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$buyer_arr = return_library_array("select id, short_name from lib_buyer", "id", "short_name");
  
	 
	if ($report_type == 1)
	{
		// =================================================================================
		 if ($db_type == 2) {

			if ($cbo_year != 0) $job_year_cond = " and to_char(f.insert_date,'YYYY')=$cbo_year";
			else $job_year_cond = "";
		} 
		$from_date = $txt_date_from;
		if (str_replace("'", "", $txt_date_to) == "") $to_date = $from_date;
		else $to_date = $txt_date_to;

		$date_con = "";$prod_date_cond="";"";$recipe_date_cond="";
		if ($from_date != "" && $to_date != "") 
		{	
				$prod_date_cond = "and f.process_end_date between '".change_date_format($from_date,'','',1)."' and '".change_date_format($txt_date_to,'','',1)."'";$recipe_date_cond = "and b.recipe_date between '".change_date_format($from_date,'','',1)."' and '".change_date_format($txt_date_to,'','',1)."'";
			 
		}

		//  $con = connect();
	    // execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (73)");
	    //  oci_commit($con);
		$recipe_cond_chk="";
		if($txt_recipe_no !="" || $cbo_re_process !=0)
		{
		 	$sql_batch_dyeing_recipe_found="select a.id as batch_id,a.batch_no,a.extention_no,a.batch_against,b.dyeing_re_process,b.id as recipe_id,f.process_end_date,f.result,b.in_charge_id,f.reason_mst_id ,f.load_unload_id,c.first_name,f.ltb_btb_id from  pro_batch_create_mst a ,pro_fab_subprocess f,pro_recipe_entry_mst b,lib_employee c where a.id=f.batch_id and b.batch_id=a.id and f.batch_id=b.batch_id and b.in_charge_id=c.id  and f.entry_form=35   and f.load_unload_id=2 and f.status_active=1 and f.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and a.status_active=1 and a.company_id=$cbo_company_id $batch_cond_batch $prod_date_cond $batch_no_cond   $re_process_cond $result_cond $method_cond $recipe_no_cond  order by a.id,f.process_end_date asc";
			$sql_batch_dyeing_result_recipe_found=sql_select($sql_batch_dyeing_recipe_found);
			foreach ($sql_batch_dyeing_result_recipe_found as $row) 
			{
				$recipe_batch_id_Arr[$row[csf('batch_id')]]=$row[csf('batch_id')];
			}
			$recipe_cond_chk=" and a.id in(".implode(",",$recipe_batch_id_Arr).")";
		}
		

	    $sql_batch_dyeing="select a.id as batch_id,a.batch_no,a.extention_no,a.batch_against,f.process_end_date,f.result,f.in_charge_mst_id,f.reason_mst_id ,f.load_unload_id,f.ltb_btb_id,c.id as recipe_id from  pro_batch_create_mst a ,pro_fab_subprocess f left join pro_recipe_entry_mst c on c.batch_id=f.batch_id and f.status_active=1 and f.is_deleted=0 where a.id=f.batch_id   and f.entry_form=35   and f.load_unload_id in(1,2) and f.status_active=1 and f.is_deleted=0   and a.status_active=1 and a.company_id=$cbo_company_id $batch_cond_batch $prod_date_cond  $result_cond $method_cond $batch_no_cond $recipe_cond_chk order by a.id,f.process_end_date asc";
		$sql_batch_dyeing_result=sql_select($sql_batch_dyeing);
		foreach ($sql_batch_dyeing_result as $row) 
		{
			if($row[csf('load_unload_id')]==2)
			{
			 
			$batch_str=$row[csf('batch_id')].'_'.$row[csf('recipe_id')];
			$batch_wise_Arr[$batch_str]['result']=$dyeing_result[$row[csf('result')]];
			$batch_wise_Arr[$batch_str]['batch_no']=$row[csf('batch_no')];
			$batch_wise_Arr[$batch_str]['extention_no']=$row[csf('extention_no')];
		
			
			$batch_wise_Arr[$batch_str]['process_end_date']=$row[csf('process_end_date')];
			}
			if($row[csf('load_unload_id')]==1) //load
			{
				$batch_str=$row[csf('batch_id')].'_'.$row[csf('recipe_id')];
				$btb_Arr_Arr[$batch_str]['ltb_btb']=$ltb_btb[$row[csf('ltb_btb_id')]];
			}
	 
		}
		  $sql_batch_dyeing_recipe="select a.id as batch_id,a.batch_no,a.extention_no,a.batch_against,b.dyeing_re_process,b.id as recipe_id,f.process_end_date,f.result,b.in_charge_id,f.reason_mst_id ,f.load_unload_id,f.ltb_btb_id from  pro_batch_create_mst a ,pro_fab_subprocess f,pro_recipe_entry_mst b  where a.id=f.batch_id and b.batch_id=a.id and f.batch_id=b.batch_id  and f.entry_form=35   and f.load_unload_id=2 and f.status_active=1 and f.is_deleted=0    and a.status_active=1 and a.company_id=$cbo_company_id $batch_cond_batch $prod_date_cond $batch_no_cond   $re_process_cond $result_cond $method_cond $recipe_no_cond order by  a.id,f.process_end_date asc";
		$sql_batch_dyeing_result_recipe=sql_select($sql_batch_dyeing_recipe);
		$in_charge_arr = return_library_array("select id, first_name from lib_employee", "id", "first_name");
		foreach ($sql_batch_dyeing_result_recipe as $row) 
		{
			$in_charge_id=$row[csf('in_charge_id')];
			$batch_str=$row[csf('batch_id')].'_'.$row[csf('recipe_id')];
			$batch_wise_Arr[$batch_str]['in_charge']=$in_charge_arr[$row[csf('in_charge_id')]];
			$batch_wise_Arr[$batch_str]['result']=$dyeing_result[$row[csf('result')]];
			$batch_wise_Arr[$batch_str]['batch_no']=$row[csf('batch_no')];
			$batch_wise_Arr[$batch_str]['extention_no']=$row[csf('extention_no')];
			$batch_wise_Arr[$batch_str]['first_name'].=$in_charge_arr[$in_charge_id].',';
			$batch_wise_Arr[$batch_str]['ltb_btb']=$ltb_btb[$row[csf('ltb_btb_id')]];
			$batch_wise_Arr[$batch_str]['process_end_date']=$row[csf('process_end_date')];
			if($row[csf('dyeing_re_process')])
			{
				$batch_wise_Arr[$batch_str]['dyeing_re_process'].=$dyeing_re_process[$row[csf('dyeing_re_process')]].',';
			}
			$batch_wise_Arr[$batch_str]['recipe_id'].=$row[csf('recipe_id')].',';
	 
		}
		

		 // echo "<pre>";print_r($batch_idArr);die;

		 // fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 73, 1,$batch_idArr, $empty_arr); // batch Id insert
		 
	    
		    //print_r($reprocess_batch_idArr);

		// ======================================= Recipe Info Start ==================================
	//     $sql_batch_recipe="select b.id as recipe_mst_id,b.dyeing_re_process,a.id as new_batch_id,a.re_dyeing_from as re_batch_id,a.batch_no,a.extention_no,a.batch_against,f.result,f.reason_mst_id ,f.load_unload_id,f.process_end_date,b.recipe_date,f.in_charge_mst_id from  pro_batch_create_mst a ,pro_recipe_entry_mst b ,pro_fab_subprocess f where   a.id=b.batch_id  and a.id=f.batch_id   and f.batch_id=b.batch_id    and b.entry_form=60  and f.load_unload_id=2   and f.status_active=1 and f.is_deleted=0  and b.dyeing_re_process=1   and a.status_active=1 and a.working_company_id=$cbo_company_id $batch_cond_batch  $recipe_date_cond order by f.process_end_date asc";
	// 	$sql_batch_recipe_result=sql_select($sql_batch_recipe);
	// 	$recipeDateWise_batch_Arr=array();
	//   foreach ($sql_batch_recipe_result as $row) 
	//    {
	// 	  $recipeDateWise_batch_Arr[$row[csf('in_charge_mst_id')]][$row[csf('process_end_date')]].=$row[csf('batch_no')].',';
	//    }
	   
	// print_r($rft_recipeDateWise_batch_Arr);

		// execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (73)");
    	//oci_commit($con); 	disconnect($con);

		//$colspan = 2;
		$tbl_width = 1030;
		ob_start();
		?>
		<style type="text/css">
			.word_wrap_break {
				word-break: break-all;
				word-wrap: break-word;
			}
		</style>
		<div style="width:<? echo $tbl_width + 20; ?>px; margin-left:40px;">
		<table cellpadding="0" cellspacing="0" width="<? echo $tbl_width; ?>">
			<tr>
				<td colspan="11" align="center" width="100%" class="form_caption" style="font-size:18px"><b><? echo $report_title; ?></b></td>
			</tr>
			<tr>
				<td align="center" colspan="11" width="100%"  class="form_caption" style="font-size:20px"><b><? echo $company_arr[str_replace("'", "", $cbo_company_name)]; ?></b></td>
			</tr>
			<tr>
				<td align="center" colspan="11" width="100%"  class="form_caption" style="font-size:16px"><b><? if (str_replace("'", "", $txt_date_from) != "") echo "From " . str_replace("'", "", $txt_date_from);
					if (str_replace("'", "", $txt_date_to) != "") echo " To " . str_replace("'", "", $txt_date_to); ?></b></td>
			</tr>
		</table>

		<fieldset style="width:<? echo $tbl_width; ?>px;margin-left:40px;">
		 
			<table cellspacing="0" cellpadding="0" border="1" align="center" style="" rules="all" id="table_search" width="<? echo $tbl_width; ?>" class="rpt_table">
			 
				<thead>
					<tr>
						<th width="50">SL</th>
						<th width="100" >Batch No.</th>
						<th width="60">Ext. No</th>
						<th width="200">Recipe No.</th>
						<th width="120">Recipe Incharge</th>
						<th width="100">Method</th>
						<th width="100">Result</th>
						<th width="150">Re-process Type</th>
						<th title="Production Date" width="">Dyeing Date</th>
						 
					</tr>
				</thead>
			</table>
			<div style=" max-height:350px; width:<? echo $tbl_width + 20; ?>px; overflow-y:scroll;" id="scroll_body">
				<table class="rpt_table" id="table_body" width="<? echo $tbl_width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all">
			 	<tbody>
					 
						<?
						 

						$i = 1;
						 
						 
						foreach($batch_wise_Arr as $batch_strData=>$row)
						{
							if ($i%2==0)  
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";
								$batch_data=explode("_",$batch_strData);
								$batch_id=$batch_data[0];
								$recipe_id=$batch_data[1];
								//$recipe_id=rtrim($row['recipe_id'],','); 
								$recipe_id_all=$recipe_id;//mplode(", ",array_unique(explode(",",$recipe_id)));
								$dyeing_re_process=rtrim($row['dyeing_re_process'],','); 
								$dyeing_re_process_all=implode(", ",array_unique(explode(",",$dyeing_re_process)));

								$recipe_name=rtrim($row['in_charge'],','); 
								$recipe_names=implode(", ",array_unique(explode(",",$recipe_name)));
			
						
							?>
							 
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" valign="top">
								<td width="50" align="center"><? echo $i;  ?></td>
								<td width="100" title="<?=$batch_id;?>" align="center"><? echo $row['batch_no']; ?></td>
								<td width="60" align="center"><? echo $row['extention_no']; ?></td>
								<td width="200" align="center"><? echo $recipe_id_all; ?></td>
								<td width="120" align="center"><? echo $recipe_names; ?> 
							    </td>
							
								<td width="100" align="center"><? echo $btb_Arr_Arr[$batch_strData]['ltb_btb']; ?></td>
								<td width="100" align="center"><? echo $row['result']; ?></td>
								<td width="150" align="center"><? echo $dyeing_re_process_all; ?></td>
							 
								<td width="" align="center"><? echo date('d-M-Y',strtotime($row['process_end_date'])); ?></td>
									
							</tr>
							<?
								$i++;
							 

							
						 }
						
						?>
					 
					 
				</tbody>	 
				</table>
				</div>  
			 
			 
		</fieldset>
	</div>
		<br>
		<?
		foreach (glob("../../../ext_resource/tmp_report/$user_id*.xls") as $filename) {
			if (@filemtime($filename) < (time() - $seconds_old))
				@unlink($filename);
		}
		$name = time();
		$filename = "../../../ext_resource/tmp_report/" . $user_id . "_" . $name . ".xls";
		$create_new_doc = fopen($filename, 'w');
		$is_created = fwrite($create_new_doc, ob_get_contents());
		$filename = "../../../ext_resource/tmp_report/" . $user_id . "_" . $name . ".xls";
		echo "$total_data####$filename";
		exit();
	}
}

//getYarnType
/*function getYarnType($yarn_type_arr, $yarnProdId)
{
	global $yarn_type;
	$yarn_type_name = '';
	$expYPId = explode(",", $yarnProdId);
	$yarnTypeIdArr = array();
	foreach ($expYPId as $key => $val) {
		$yarnTypeIdArr[$yarn_type_arr[$val]] = $yarn_type_arr[$val];
	}

	foreach ($yarnTypeIdArr as $key => $val) {
		if ($yarn_type_name == '')
			$yarn_type_name = $yarn_type[$val];
		else
			$yarn_type_name .= "," . $yarn_type[$val];
	}
	return $yarn_type_name;
}*/
?>