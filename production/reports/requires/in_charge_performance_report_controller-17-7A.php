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
	$report_type = str_replace("'", "", $report_type);//cbo_in_charge_id*cbo_batch_type
	$cbo_in_charge_id = str_replace("'", "", $cbo_in_charge_id);
	$batch_type = str_replace("'", "", $cbo_batch_type);
	$cbo_company_id = str_replace("'", "", $cbo_company_name);

	//var_dump($txt_date_to);
	//in_charge_mst_id
	$in_charge_cond="";
	if($cbo_in_charge_id>0)
	{
		$in_charge_cond=" and f.in_charge_mst_id in($cbo_in_charge_id)";
	}

	$company_arr = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$buyer_arr = return_library_array("select id, short_name from lib_buyer", "id", "short_name");
  

if($batch_type==0)
	$batch_cond_batch="and a.entry_form in (0,36)";
	else if($batch_type==1)
	$batch_cond_batch="and a.entry_form=0 ";
	else if($batch_type==2)
	$batch_cond_batch="and a.entry_form=36";
	else 
	$batch_cond_batch="";

	 
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

		 $con = connect();
	    execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (73)");
	   //  oci_commit($con);

		       $sql_batch_dyeing="select a.id as batch_id,a.batch_no,a.extention_no,a.batch_against,f.process_end_date,f.result,f.in_charge_mst_id,f.reason_mst_id ,f.load_unload_id,c.first_name from  pro_batch_create_mst a ,pro_fab_subprocess f,lib_employee c where a.id=f.batch_id and f.in_charge_mst_id=c.id and f.entry_form=35 and  a.batch_against in(1) and f.result=1 and f.load_unload_id=2 and f.status_active=1 and f.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and a.status_active=1 and a.working_company_id=$cbo_company_id $in_charge_cond $batch_cond_batch $prod_date_cond order by f.process_end_date asc";
		$sql_batch_dyeing_result=sql_select($sql_batch_dyeing);

		 
		foreach ($sql_batch_dyeing_result as $row) 
		{
			$first_batch_ids='';	
			if($row[csf('extention_no')]=='' || $row[csf('extention_no')]==0) //First batch and Shade Match
			 { 
				
				if($row[csf('result')]==1) //Shade Match
				{
					$first_batch_nos=$row[csf('batch_no')];
					$prod_date_arr[$row[csf('in_charge_mst_id')]][$row[csf('process_end_date')]]['first_batch'].=$first_batch_nos.',';
					$batch_idArr[$row[csf('batch_id')]]=$row[csf('batch_id')];
					$prod_date_arr[$row[csf('in_charge_mst_id')]][$row[csf('process_end_date')]]['extention_no']=$row[csf('extention_no')];
					$prod_in_charge_arr[$row[csf('in_charge_mst_id')]]=$row[csf('first_name')];
		
					$In_charge_batch_Arr[$row[csf('batch_id')]]=$row[csf('in_charge_mst_id')];
					$batch_noProd_dateArr[$row[csf('batch_no')]]=$row[csf('process_end_date')];
				}
			 }
		
		
			 
		}
		 // echo "<pre>";print_r($batch_idArr);die;

		  fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 73, 1,$batch_idArr, $empty_arr); // batch Id insert
		 
	   /*$sql_batch_dyeing_load="select a.re_dyeing_from as re_batch_id,a.batch_no,a.extention_no,a.batch_against,c.reason_type,f.process_end_date,f.result,f.reason_mst_id ,f.load_unload_id,f.process_end_date,f.batch_id,c.reason from  pro_batch_create_mst a ,pro_fab_subprocess f,lib_re_process_reason_entry c,gbl_temp_engine g where a.id=f.batch_id and f.reason_mst_id=c.id and a.re_dyeing_from=g.ref_val  and f.batch_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from=73    and f.entry_form=35 and  a.batch_against in(2) and f.load_unload_id=1 and f.status_active=1 and f.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and a.status_active=1 and a.working_company_id=$cbo_company_id $batch_cond_batch  order by f.process_end_date asc";*/
		  $sql_reason= "select id, reason_type from lib_re_process_reason_entry where status_active=1 and section=2";
		  $sql_reason_result=sql_select($sql_reason);
		   foreach ($sql_reason_result as $row) 
		  {
			 $reason_batch_Arr[$row[csf('id')]]=$row[csf('reason_type')];
		  }
	  
	      $sql_batch_dyeing_load="select a.id as new_batch_id,a.re_dyeing_from as re_batch_id,a.batch_no,a.extention_no,a.batch_against,f.result,f.reason_mst_id ,f.load_unload_id,f.process_end_date,f.batch_id from  pro_batch_create_mst a ,pro_fab_subprocess f,gbl_temp_engine g  where   a.id=f.batch_id  and a.re_dyeing_from=g.ref_val  and g.user_id = ".$user_id." and g.ref_from=1 and g.entry_form=73 and f.entry_form=35 and  a.batch_against in(2) and f.load_unload_id=1 and f.status_active=1 and f.is_deleted=0    and a.status_active=1 and a.working_company_id=$cbo_company_id $batch_cond_batch  order by f.process_end_date asc";
		  $sql_load_dyeing_result=sql_select($sql_batch_dyeing_load);
		foreach ($sql_load_dyeing_result as $row) 
		 {
			$new_batch_Arr[$row[csf('new_batch_id')]]=$row[csf('new_batch_id')];
		 }
		 fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 73, 2,$new_batch_Arr, $empty_arr); // batch Id 2insert

		 $sql_batch_dyeing_load_reason="select a.id as new_batch_id,a.re_dyeing_from as re_batch_id,a.batch_no,a.extention_no,a.batch_against,f.result,f.reason_mst_id ,f.load_unload_id,f.process_end_date from  pro_batch_create_mst a ,pro_fab_subprocess f,gbl_temp_engine g where a.id=f.batch_id   and a.id=g.ref_val  and f.batch_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from=2 and g.entry_form=73  and f.entry_form=35 and  a.batch_against in(2) and f.load_unload_id=1 and f.status_active=1 and f.is_deleted=0    and a.status_active=1 and a.working_company_id=$cbo_company_id $batch_cond_batch  order by f.process_end_date asc";
		  $sql_load_dyeing_result_reason=sql_select($sql_batch_dyeing_load_reason);
		  $reprocess_batch_idArr=array();
		  foreach ($sql_load_dyeing_result_reason as $row) 
		 {
			  $reason_typeId=$reason_batch_Arr[$row[csf('reason_mst_id')]];
			 $min_prod_date=$batch_noProd_dateArr[$row[csf('batch_no')]];
			// echo  $reason_typeId.'DDDDDDD';
			if($reason_typeId==1) //In Charge
			{
				$In_charge=$In_charge_batch_Arr[$row[csf('re_batch_id')]];
				$reprocess_batch_idArr[$In_charge][$min_prod_date].=$row[csf('batch_no')].',';
			}
		 }
		    //print_r($reprocess_batch_idArr);

		// ======================================= Recipe Info Start ==================================
	    $sql_batch_recipe="select b.id as recipe_mst_id,b.dyeing_re_process,a.id as new_batch_id,a.re_dyeing_from as re_batch_id,a.batch_no,a.extention_no,a.batch_against,f.result,f.reason_mst_id ,f.load_unload_id,f.process_end_date,b.recipe_date,f.in_charge_mst_id from  pro_batch_create_mst a ,pro_recipe_entry_mst b ,pro_fab_subprocess f where   a.id=b.batch_id  and a.id=f.batch_id   and f.batch_id=b.batch_id    and b.entry_form=60  and f.load_unload_id=2   and f.status_active=1 and f.is_deleted=0  and b.dyeing_re_process=1   and a.status_active=1 and a.working_company_id=$cbo_company_id $batch_cond_batch  $recipe_date_cond order by f.process_end_date asc";
		$sql_batch_recipe_result=sql_select($sql_batch_recipe);
		$recipeDateWise_batch_Arr=array();
	  foreach ($sql_batch_recipe_result as $row) 
	   {
		  $recipeDateWise_batch_Arr[$row[csf('in_charge_mst_id')]][$row[csf('process_end_date')]].=$row[csf('batch_no')].',';
	   }
	   // ======================================= Recipe RFT Info Start ==================================
	     $sql_batch_recipe_rft="select b.id as recipe_mst_id,b.dyeing_re_process,a.id as new_batch_id,a.re_dyeing_from as re_batch_id,a.batch_no,a.extention_no,a.batch_against,f.result,f.reason_mst_id ,f.load_unload_id,f.process_end_date,b.recipe_date,f.in_charge_mst_id from  pro_batch_create_mst a ,pro_recipe_entry_mst b ,pro_fab_subprocess f,gbl_temp_engine g where   a.id=b.batch_id  and a.id=f.batch_id   and f.batch_id=b.batch_id  and f.batch_id=g.ref_val and a.id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from=1 and g.entry_form=73    and b.entry_form=60  and f.load_unload_id=2  and f.result=1 and b.dyeing_re_process in(1) and f.status_active=1 and f.is_deleted=0    and a.status_active=1 and a.working_company_id=$cbo_company_id $batch_cond_batch  order by f.process_end_date asc";
	   $sql_batch_recipe_result_rft=sql_select($sql_batch_recipe_rft);
	   $rft_recipeDateWise_batch_Arr=array();   $rft_recipeDateWise_batchNo_Arr=array();
	 foreach ($sql_batch_recipe_result_rft as $row) 
	  {
		 $rft_recipeDateWise_batch_Arr[$row[csf('in_charge_mst_id')]][$row[csf('process_end_date')]][$row[csf('batch_no')]].=$row[csf('recipe_mst_id')].',';
		 $rft_recipeDateWise_batchNo_Arr[$row[csf('in_charge_mst_id')]][$row[csf('process_end_date')]][$row[csf('batch_no')]].=$row[csf('batch_no')].',';
	  }

	// print_r($rft_recipeDateWise_batch_Arr);

		 execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (73)");
    	oci_commit($con); 	disconnect($con);

		//$colspan = 2;
		$tbl_width = 1250;
		ob_start();
		?>
		<div style="width:<? echo $tbl_width + 20; ?>px; margin-left:40px;">
		<table cellpadding="0" cellspacing="0" width="<? echo $tbl_width; ?>">
			<tr>
				<td colspan="11" align="center" width="100%" class="form_caption" style="font-size:18px"><? echo $report_title; ?></td>
			</tr>
			<tr>
				<td align="center" colspan="11" width="100%"  class="form_caption" style="font-size:16px"><? echo $company_arr[str_replace("'", "", $cbo_company_name)]; ?></td>
			</tr>
			<tr>
				<td align="center" colspan="11" width="100%"  class="form_caption" style="font-size:12px"><strong><? if (str_replace("'", "", $txt_date_from) != "") echo "From " . str_replace("'", "", $txt_date_from);
					if (str_replace("'", "", $txt_date_to) != "") echo " To " . str_replace("'", "", $txt_date_to); ?></strong></td>
			</tr>
		</table>

		<fieldset style="width:<? echo $tbl_width + 20; ?>px;margin-left:40px;">
		<?
	 
		foreach($prod_date_arr as $in_charge_id=>$in_chargeData)
		{
			
		 
		?>
			<table cellspacing="0" cellpadding="0" border="1" align="center" style="margin: 5px;" rules="all" width="<? echo $tbl_width; ?>" class="rpt_table">
			<caption title="<?=$in_charge_id;?>"><b><?=$prod_in_charge_arr[$in_charge_id];?></b> </caption>
				<thead>
					<tr>
						<th width="50">SL</th>
						<th width="100" title="Production Date">Date</th>
						<th width="120">No. of Unload <br>Batch[Excluding <br>Re-Dyeing Batch]</th>
						<th width="100">No. of OK Batch</th>
						<th width="100">Unload <br>Performance %</th>
						<th width="150">Not Ok Lot</th>
						<th width="100">Topping Batch</th>
						<th width="100">Topping<br> RFT Batch</th>
						<th width="100">Topping RFT %</th>
						<th width="100">Topping Ok <br>Lot Number</th>
						<th width="">Topping <br>Not Ok Lot</th>
						 
					</tr>
				</thead>
			 
					<tbody id="scroll_body">
						<?
						 

						$i = 1;
						$tot_no_of_ok_batchTot = 0;	$tot_no_of_unload_batch=0;$tot_topping_recipeDateWise_batchTot=0;$tot_tot_rft_recipe_batch_count=0;
						// echo "<pre>";print_r($data_arr);
						 
						foreach($in_chargeData as $prod_date=>$row)
						{
							if ($i%2==0)  
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";
								$first_batch=rtrim($row['first_batch'],',');
								$first_batchArr=array_unique(explode(",",$first_batch));
								$no_of_unload_batch=count($first_batchArr);

								//echo $first_batch.', ';
								 
								$no_of_ok_batch="";$not_ok_lots="";$no_of_ok_batchArr_tot="";
								$no_of_ok_batch=rtrim($reprocess_batch_idArr[$in_charge_id][$prod_date],',');
								//echo $no_of_ok_batch.'=<br>';
								if($no_of_ok_batch!="")
								{
									$no_of_ok_batchArr=array_unique(explode(",",$no_of_ok_batch));
									$no_of_ok_batchArr_tot=count($no_of_ok_batchArr);
									$not_ok_lots=implode(",",array_unique(explode(",",$no_of_ok_batch)));
								}
					
								$recipeDateWise_batch=rtrim($recipeDateWise_batch_Arr[$in_charge_id][$prod_date],',');
								$topping_recipeDateWise_batchTot="";
								if($recipeDateWise_batch!="")
								{
									$recipeDateWise_batchArr=array_unique(explode(",",$recipeDateWise_batch));
									$topping_recipeDateWise_batchTot=count($recipeDateWise_batchArr);
								}
								$rft_recipeDateWise_all="";$topping_not_ok_lot_recipe_all="";$tot_rft_recipe_batch_count=0;
								foreach($first_batchArr as $rft_batch)
								{
									$rft_recipeDateWiseAll=chop($rft_recipeDateWise_batch_Arr[$in_charge_id][$prod_date][$rft_batch],',');
									$rft_BatchNoAll=chop($rft_recipeDateWise_batchNo_Arr[$in_charge_id][$prod_date][$rft_batch],',');
									//$rft_BatchNos=implode(",",array_unique(explode(",",$rft_BatchNoAll)));

									$rft_recipeDateWiseArr=array_unique(explode(",",$rft_recipeDateWiseAll));
									$rft_recipeDateWiseArrAll=implode(",",array_unique(explode(",",$rft_BatchNoAll)));
									if($rft_recipeDateWiseAll!="")
									{
										//echo count($rft_recipeDateWiseArr).'='.$rft_recipeDateWiseAll.'<br>';
									}
									

									if(count($rft_recipeDateWiseArr)==1 && $rft_recipeDateWiseAll!="") //Signle Recipe found
									{
										//echo $rft_recipeDateWiseAll.'=T<br>';
										if($rft_recipeDateWise_all=='') $rft_recipeDateWise_all=$rft_recipeDateWiseArrAll;else $rft_recipeDateWise_all.=",".$rft_recipeDateWiseArrAll;
										$tot_rft_recipe_batch_count=1;

										 //echo $tot_rft_recipe_batch_count.'=A';
										
									}
									else
									{
										if($topping_not_ok_lot_recipe_all=='') $topping_not_ok_lot_recipe_all=$rft_recipeDateWiseArrAll;else $topping_not_ok_lot_recipe_all.=",".$rft_recipeDateWiseArrAll;
										//$tot_rft_recipe_batch_count=0;
									}
									 
								} 
								

							?>
							 
							 
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i.$in_charge_id; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i.$in_charge_id; ?>" valign="top">
								<td width="50" align="center"><? echo $i;  ?></td>
								<td width="100" align="center"><? echo date('d-M-Y',strtotime($prod_date)); ?></td>
								<td width="120" align="center" title="<?=$first_batch;?>"><? echo $no_of_unload_batch; ?></td>
								<td width="100" align="center" title="<?=$no_of_ok_batchArr_tot;?>"><? echo $no_of_ok_batchTot=$no_of_unload_batch-$no_of_ok_batchArr_tot; ?></td>
								<td width="100"  align="center"title="No. of OK Batch/No. of Unload Batch [Excluding Re-Dyeing Batch]*100">
											<? echo fn_number_format(($no_of_ok_batchTot/$no_of_unload_batch)*100,2); ?></td>
								<td width="150" align="center"><? echo $not_ok_lots; ?></td>
								<td width="100" align="center"><? echo $topping_recipeDateWise_batchTot; ?></td>
								<td width="100" align="center" title="RFT Recipe Id=<? echo $rft_recipeDateWise_all; ?>"><? echo $tot_rft_recipe_batch_count; ?></td>
								<td width="100" align="center" title="Topping RFT/Topping Batch*100"><? echo  fn_number_format(($tot_rft_recipe_batch_count/$topping_recipeDateWise_batchTot)*100,2);; ?></td>
							 
								<td width="100" align="center"><? echo $rft_recipeDateWise_all; ?></td>
								<td width="" align="center"><? echo $topping_not_ok_lot_recipe_all; ?></td>
									
							</tr>
							<?
								$i++;
								$tot_no_of_unload_batch += $no_of_unload_batch;
								$tot_no_of_ok_batchTot += $no_of_ok_batchTot;
								$tot_topping_recipeDateWise_batchTot += $topping_recipeDateWise_batchTot;
								$tot_tot_rft_recipe_batch_count += $tot_rft_recipe_batch_count;

							
						 }
						
						?>
					</tbody>
					 
						<tr style="text-align: center; background:#CCF;font-size:12px;">
							<td   align="right" colspan="2"><b>Total</b></td>
							<td width="80" align="center"><? echo number_format($tot_no_of_unload_batch, 2, '.', ''); ?></td>
							<td width="80" align="center"><? echo number_format($tot_no_of_ok_batchTot, 2, '.', ''); ?></td>
							<td width="80" title="No Of Ok/No Of Unload Batch*100"><? echo fn_number_format(($tot_no_of_ok_batchTot/$tot_no_of_unload_batch)*100, 2, '.', ''); ?></td>
							<td width="80">&nbsp;</td>
							<td width="100"><? echo number_format($tot_topping_recipeDateWise_batchTot, 2, '.', ''); ?></td>
							<td width="80"><? echo number_format($tot_tot_rft_recipe_batch_count, 2, '.', ''); ?></td>
							 
							<td width="80"><? echo fn_number_format(($tot_tot_rft_recipe_batch_count/$tot_topping_recipeDateWise_batchTot)*100, 2, '.', ''); ?></td>
							<td width="100">&nbsp;</td>
							<td width="">&nbsp;</td
							 
						 
					 
				</table>
			 
			<?
		}
			?>
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