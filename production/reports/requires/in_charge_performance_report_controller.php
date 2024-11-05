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
	    // oci_commit($con);

		   $sql_batch_dyeing="select a.id as batch_id,a.batch_no,a.re_dyeing_from,a.extention_no,a.batch_against,f.process_end_date,f.id,f.result,f.in_charge_mst_id,f.reason_mst_id ,f.load_unload_id,c.first_name,f.system_no from  pro_batch_create_mst a ,pro_fab_subprocess f,lib_employee c where a.id=f.batch_id and f.in_charge_mst_id=c.id and f.entry_form=35 and  a.batch_against in(1,2) and f.result=1 and f.load_unload_id=2 and f.status_active=1 and f.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and a.status_active=1 and a.working_company_id=$cbo_company_id $in_charge_cond $batch_cond_batch $prod_date_cond order by f.process_end_date,f.id asc";
		$sql_batch_dyeing_result=sql_select($sql_batch_dyeing);
		foreach ($sql_batch_dyeing_result as $row) 
		{
			$first_batch_ids='';//system_no	
			$all_batch_NoArr[$row[csf('batch_no')]]=$row[csf('batch_no')];
			if($row[csf('extention_no')]=='' || $row[csf('extention_no')]==0) //First batch and Shade Match
			 { 
				
				

				if($row[csf('result')]==1) //Shade Match
				{
					$first_batch_nos=$row[csf('batch_no')];
					$prod_date_arr[$row[csf('in_charge_mst_id')]][$row[csf('process_end_date')]]['first_batch'].=$first_batch_nos.',';
					$prod_date_arr[$row[csf('in_charge_mst_id')]][$row[csf('process_end_date')]]['first_batch_id'].=$row[csf('batch_id')].',';
					$batch_idArr[$row[csf('batch_id')]]=$row[csf('batch_id')];
					//$batch_idArr[$row[csf('re_dyeing_from')]]=$row[csf('re_dyeing_from')];
					$batch_NoArr[$row[csf('batch_id')]]=$row[csf('batch_no')];
					$prod_date_arr[$row[csf('in_charge_mst_id')]][$row[csf('process_end_date')]]['extention_no']=$row[csf('extention_no')];

					$prod_date_arr[$row[csf('in_charge_mst_id')]][$row[csf('process_end_date')]]['fnc_system_no'].=$row[csf('system_no')].',';
					

					$prod_in_charge_arr[$row[csf('in_charge_mst_id')]]=$row[csf('first_name')];
		
					$In_charge_batch_Arr[$row[csf('batch_id')]]=$row[csf('in_charge_mst_id')];
					$batch_noProd_dateArr[$row[csf('batch_no')]]=$row[csf('process_end_date')];
					$FunctionalNoProd_dateArr[$row[csf('batch_no')]]=$row[csf('system_no')];
					$FunctionalNoWisebatchProd_dateArr[$row[csf('system_no')]].=$row[csf('batch_no')].',';

					$FunctionalNoProd_date2Arr[$row[csf('batch_id')]]=$row[csf('system_no')];
					$FunctionalNoProd_date3Arr[$row[csf('batch_id')]][$row[csf('in_charge_mst_id')]]=$row[csf('system_no')];
					$FunctionalNoWisebatchProd_date2Arr[$row[csf('system_no')]].=$row[csf('batch_id')].',';
					
					$FunctionalNoProd_date3Arr[$row[csf('batch_id')]][$row[csf('in_charge_mst_id')]]=$row[csf('system_no')];
					$FunctionalNoWisebatchProd_date3Arr[$row[csf('system_no')]][$row[csf('in_charge_mst_id')]].=$row[csf('batch_id')].',';
				}
			 }
			 else
			 {
				 if($row[csf('extention_no')] && $row[csf('result')]==1)
				 {
				 	//$first_batch_nos='';
					 $first_batch_nos_re=$row[csf('batch_no')];
					$prod_date_arr[$row[csf('in_charge_mst_id')]][$row[csf('process_end_date')]]['first_batch'].=$first_batch_nos_re.',';
					$prod_date_arr[$row[csf('in_charge_mst_id')]][$row[csf('process_end_date')]]['first_batch_re'].=$first_batch_nos_re.',';
					$prod_date_arr[$row[csf('in_charge_mst_id')]][$row[csf('process_end_date')]]['first_batch_id'].=$row[csf('batch_id')].',';
					$batch_idArr[$row[csf('batch_id')]]=$row[csf('batch_id')];
					//$batch_idArr[$row[csf('re_dyeing_from')]]=$row[csf('re_dyeing_from')];
					$batch_NoArr[$row[csf('batch_id')]]=$row[csf('batch_no')];
					$batch_ReDyingArr[$row[csf('batch_id')]]=$row[csf('batch_no')];
					$batch_ReDyingExtArr[$row[csf('batch_id')]]=$row[csf('extention_no')];
					$prod_date_arr[$row[csf('in_charge_mst_id')]][$row[csf('process_end_date')]]['extention_no']=$row[csf('extention_no')];
					$prod_date_arr[$row[csf('in_charge_mst_id')]][$row[csf('process_end_date')]]['fnc_system_no'].=$row[csf('system_no')].',';
					
					$prod_in_charge_arr[$row[csf('in_charge_mst_id')]]=$row[csf('first_name')];
					$In_charge_batch_Arr[$row[csf('batch_id')]]=$row[csf('in_charge_mst_id')];
					$batch_noProd_dateArr[$row[csf('batch_no')]]=$row[csf('process_end_date')];
					//$Re_FunctionalNoProd_dateArr[$row[csf('batch_no')]]=$row[csf('system_no')];
					//$FunctionalNoWisebatchProd_dateArr[$row[csf('system_no')]].=$row[csf('batch_no')].',';

					$FunctionalNoProd_date2Arr[$row[csf('batch_id')]]=$row[csf('system_no')];
					$FunctionalNoWisebatchProd_date2Arr[$row[csf('system_no')]].=$row[csf('batch_id')].',';
					
					$FunctionalNoProd_date3Arr[$row[csf('batch_id')]][$row[csf('in_charge_mst_id')]]=$row[csf('system_no')];
					$FunctionalNoWisebatchProd_date3Arr[$row[csf('system_no')]][$row[csf('in_charge_mst_id')]].=$row[csf('batch_id')].',';
				 }
			 }
			 
		}
		 // echo "<pre>";print_r($batch_idArr);die;

		  fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 73, 1,$batch_idArr, $empty_arr); // batch Id insert
		  
		
		  $sql_reason= "select id, reason_type from lib_re_process_reason_entry where status_active=1 and section=2";
		  $sql_reason_result=sql_select($sql_reason);
		   foreach ($sql_reason_result as $row) 
		  {
			 $reason_batch_Arr[$row[csf('id')]]=$row[csf('reason_type')];
		  }
	  
	  
		$batch_no_cond=where_con_using_array($all_batch_NoArr,1,'a.batch_no');
		   $sql_batch_dyeing_load_reason_min="select a.id,a.batch_no,a.extention_no,a.re_dyeing_from,f.system_no,f.in_charge_mst_id,f.result,(f.process_end_date) as  process_end_date from  pro_batch_create_mst a ,pro_fab_subprocess f where  a.id=f.batch_id    and f.load_unload_id=2 and f.status_active=1 and f.is_deleted=0    and a.status_active=1 and a.working_company_id=$cbo_company_id $batch_no_cond "; 

		$sql_load_dyeing_result_reasonMin=sql_select($sql_batch_dyeing_load_reason_min);
		foreach ($sql_load_dyeing_result_reasonMin as $row) 
		 {
			$In_charge=$row[csf('in_charge_mst_id')];
			$min_prod_date=$row[csf('process_end_date')];
			$batch_no=$row[csf('id')];
			$extention_no=$row[csf('extention_no')];
			if($row[csf('extention_no')] && $row[csf('result')]==1)
			{
			$FunctionalNoProd_date3Arr[$row[csf('id')]][$row[csf('in_charge_mst_id')]]=$row[csf('system_no')];
			$FunctionalNoWisebatchProd_date3Arr[$row[csf('system_no')]][$row[csf('in_charge_mst_id')]].=$row[csf('id')].',';
			}

			$reprocess_batch_no_idArr[$batch_no][$extention_no]['in_charge']=$In_charge; 
			$reprocess_batch_no_idArr[$batch_no][$extention_no]['prod_date']=$min_prod_date; 
			$reprocessProdDate_batch_no_idArr[$batch_no]['prod_date']=$min_prod_date;
			$reprocessProdDate_batch_no_idArr[$batch_no]['in_charge']=$In_charge;
			
			$load_ProdDate_batch_no_idArr[$row[csf('id')]]=$batch_no;
			
		 }
		 // print_r($reprocessChkProdDate_batch_no_idArr);
		

	      $sql_batch_dyeing_load_reason="select a.id as new_batch_id,a.extention_no,a.re_dyeing_from as re_batch_id,a.batch_no,a.extention_no,a.batch_against,f.in_charge_mst_id,f.result,f.reason_mst_id,f.system_no ,f.load_unload_id,f.process_end_date from  pro_batch_create_mst a ,pro_fab_subprocess f where a.id=f.batch_id   and f.load_unload_id=1 and f.status_active=1 and f.is_deleted=0    and a.status_active=1 and a.working_company_id=$cbo_company_id $batch_no_cond  order by f.process_end_date asc";

		  $sql_load_dyeing_result_reason=sql_select($sql_batch_dyeing_load_reason);
		  $reprocess_batch_idArr=array();
		  foreach ($sql_load_dyeing_result_reason as $row) 
		 {
			
			$ResaonId=$row[csf('reason_mst_id')]; 
			$extention_no=$row[csf('extention_no')]; 
			$batch_no=$row[csf('new_batch_id')]; 
			$batch_no_chk=$row[csf('batch_no')]; 
			$reason_typeId=$reason_batch_Arr[$ResaonId];
			
			//$In_charge=$reprocess_batch_no_idArr[$batch_no][$extention_no]['in_charge']; 
			$min_prod_date=$reprocessProdDate_batch_no_idArr[$row[csf('re_batch_id')]]['prod_date'];
			$in_charge=$reprocessProdDate_batch_no_idArr[$row[csf('re_batch_id')]]['in_charge']; 
			if($reason_typeId==1)
			{	
				$FunctionalNoProd=$FunctionalNoProd_dateArr[$row[csf('batch_no')]];
				$Fnc_no_reason_batch_no_idArr[$FunctionalNoProd]=$reason_typeId; 
			}
			

			$batch_ReDyingExt=$row[csf('extention_no')];
			//if($row[csf('new_batch_id')])
			$reprocess_no_Chk=$reprocessChkProdDate_batch_no_idArr[$batch_no_chk][$extention_no];
			if($row[csf('new_batch_id')]==$reprocess_no_Chk)
			{
				
			}
			//echo $reprocessChkProdDate_batch_no_idArr[$row[csf('new_batch_id')]].'<br>';
			$unload_ProdDate_batch_no_idArr[$row[csf('new_batch_id')]]=$batch_no;
			// echo $row[csf('new_batch_id')].'='.$In_charge.'='.$min_prod_date.'<br>';

			
			
			if($reason_typeId==1) //In Charge
			{
				//$reprocess_batch_idArr[$In_charge][$min_prod_date].=$row[csf('batch_no')].',';
				//$reprocess_batch_idArr2[$In_charge][$min_prod_date].=$row[csf('re_batch_id')].','; 
				//$reprocess_batch_idArr22[$row[csf('batch_no')]]['reason_incharge']=$reason_typeId;
				//$reprocess_batch_idArr22[$row[csf('batch_no')]]['re_batch_id'].=$row[csf('re_batch_id')].',';
				 
				$reason_typeRe_batch_idArr2[$in_charge][$min_prod_date][$row[csf('re_batch_id')]]=$reason_typeId;

				
				 
				 
				
			}
			else
			{
				$In_charge=$In_charge_batch_Arr[$row[csf('re_batch_id')]];
				$others_reprocess_batch_idArr[$In_charge][$min_prod_date].=$row[csf('batch_no')].',';
				$others_reprocess_batch_idArr2[$In_charge][$min_prod_date].=$row[csf('re_batch_id')].',';

				//$reason_not_unload_typeRe_batch_idArr2[$in_charge][$row[csf('new_batch_id')]]=$row[csf('new_batch_id')];
			}
		 }
		   // echo "<pre>";
		  //  print_r($Fnc_no_reason_batch_no_idArr);  
  
		// ======================================= Recipe Info Start ==================================
	       $sql_batch_recipe="select b.id as recipe_mst_id,b.dyeing_re_process,a.id as new_batch_id,a.re_dyeing_from as re_batch_id,a.batch_no,a.extention_no,a.batch_against,f.result,f.reason_mst_id ,f.load_unload_id,f.process_end_date,b.recipe_date,f.in_charge_mst_id from  pro_batch_create_mst a ,pro_recipe_entry_mst b ,pro_fab_subprocess f where   a.id=b.batch_id  and a.id=f.batch_id   and f.batch_id=b.batch_id    and b.entry_form=60  and f.load_unload_id=2   and f.status_active=1 and f.is_deleted=0  and b.dyeing_re_process=1   and a.status_active=1 and a.working_company_id=$cbo_company_id $batch_cond_batch  $prod_date_cond  order by f.process_end_date asc";//$recipe_date_cond
		$sql_batch_recipe_result=sql_select($sql_batch_recipe);
		$recipeDateWise_batch_Arr=array();
	  foreach ($sql_batch_recipe_result as $row) 
	   {
			if($row[csf('batch_no')])
			{
			$recipeDateWise_batch_Arr[$row[csf('in_charge_mst_id')]][$row[csf('process_end_date')]][$row[csf('new_batch_id')]].=$row[csf('new_batch_id')].',';
			 //$OtherrecipeDateWise_batch_Arr[$row[csf('process_end_date')]][$row[csf('new_batch_id')]].=$row[csf('new_batch_id')].',';
			}
	   }
	   // ======================================= Recipe RFT Info Start ==================================
	 
		   $sql_batch_recipe_rft="select b.id as recip_id,a.batch_against,b.id as recipe_mst_id,b.dyeing_re_process,a.id as new_batch_id,a.re_dyeing_from as re_batch_id,a.batch_no,a.extention_no,a.batch_against,f.result,f.reason_mst_id ,f.load_unload_id,f.process_end_date,b.recipe_date,f.in_charge_mst_id,f.system_no from  pro_batch_create_mst a ,pro_recipe_entry_mst b ,pro_fab_subprocess f  where   a.id=b.batch_id  and a.id=f.batch_id   and f.batch_id=b.batch_id  and b.dyeing_re_process=1  and b.entry_form=60  and f.load_unload_id=2  and f.result in(1,2)  and f.status_active=1 and f.is_deleted=0    and a.status_active=1 and a.working_company_id=$cbo_company_id $batch_cond_batch  $prod_date_cond  order by f.process_end_date,b.id asc";
	   $sql_batch_recipe_result_rft=sql_select($sql_batch_recipe_rft);
	   $rft_recipeDateWise_batch_Arr=array();   $rft_recipeDateWise_batchNo_Arr=array();
	 foreach ($sql_batch_recipe_result_rft as $row) 
	  {
		 //$topping_FunctionalNo=$FunctionalNoProd_date3Arr[$rft_batchId][$in_charge_id];
			 $functional_batchWiseRecipeArr[$row[csf('in_charge_mst_id')]][$row[csf('process_end_date')]][$row[csf('system_no')]].=$row[csf('recipe_mst_id')].',';

			 $recipe_count_batchWiseRecipeArr[$row[csf('in_charge_mst_id')]][$row[csf('process_end_date')]][$row[csf('new_batch_id')]]+=1;

			 $rft_recipeDateWise_batch_Arr[$row[csf('in_charge_mst_id')]][$row[csf('process_end_date')]][$row[csf('new_batch_id')]].=$row[csf('recipe_mst_id')].',';
			 $topping_OK_recipeDateWise_batch_Arr[$row[csf('in_charge_mst_id')]][$row[csf('process_end_date')]][$row[csf('new_batch_id')]].=$row[csf('new_batch_id')].',';
			 
			 $rft_recipeDateWise_batchNo_Arr[$row[csf('in_charge_mst_id')]][$row[csf('process_end_date')]][$row[csf('new_batch_id')]].=$row[csf('new_batch_id')].',';
			 $other_rft_recipeDateWise_batch_Arr[$row[csf('process_end_date')]][$row[csf('new_batch_id')]].=$row[csf('new_batch_id')].',';
			 $other_rft_recipeDateWise_batchNo_Arr[$row[csf('process_end_date')]][$row[csf('new_batch_id')]].=$row[csf('new_batch_id')].',';
			 $recipeWiseBatchNoDateWise_batch_Arr[$row[csf('in_charge_mst_id')]][$row[csf('process_end_date')]][$row[csf('recipe_mst_id')]].=$row[csf('new_batch_id')].',';
 
	  }

	// print_r($rft_recipeDateWise_batch_Arr);

		execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (73)");
    	oci_commit($con);disconnect($con);

		//$colspan = 2;
		$tbl_width = 1300;
		ob_start();
		?>
		<div style="width:<? echo $tbl_width + 20; ?>px; margin-left:40px;">
		<table cellpadding="0" cellspacing="0" width="<? echo $tbl_width; ?>">
			<tr>
				<td colspan="11" align="center" width="100%" class="form_caption" style="font-size:18px"><b><? echo $report_title; ?></b></td>
			</tr>
			<tr>
				<td align="center" colspan="11" width="100%"  class="form_caption" style="font-size:16px"><b><? echo $company_arr[str_replace("'", "", $cbo_company_name)]; ?></b></td>
			</tr>
			<tr>
				<td align="center" colspan="11" width="100%"  class="form_caption" style="font-size:16px"><b><? if (str_replace("'", "", $txt_date_from) != "") echo "From " . str_replace("'", "", $txt_date_from);
					if (str_replace("'", "", $txt_date_to) != "") echo " To " . str_replace("'", "", $txt_date_to); ?></b></td>
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
						<th width="120">Unload Batch</th>
						<th width="100"> OK Batch</th>
						<th width="100">Unload <br>Performance %</th>
						<th width="150">Not Ok Batch</th>
						<th width="100">Topping Batch</th>
						<th width="100">Topping<br> RFT Batch</th>
						<th width="100">Topping RFT %</th>
						<th width="150">Topping Ok <br>Batch</th>
						<th width="">Topping <br>Not Ok Batch</th>
						 
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
								$first_batch_re=rtrim($row['first_batch_re'],',');
								$first_batch_id=rtrim($row['first_batch_id'],',');
								 //echo $first_batch_id.'<br>';
								$first_re_batchArr=array_unique(explode(",",$first_batch_re));
								$first_Id_batchArr=array_unique(explode(",",$first_batch_id));								
									
								$first_batchArr=array_unique(explode(",",$first_batch));
								$no_of_unload_batch=count($first_batchArr);
								$not_ok_lots="";$no_of_ok_batchArr_tot=""; 
								 // echo $first_batch_id.'<br>';
								$no_of_ok_batchArr=array();$recipe_count_batch=0;$recipebatchArr=array();
								foreach($first_Id_batchArr as $fbatch)
								{
									$in_charge=$reason_typeRe_batch_idArr2[$in_charge_id][$prod_date][$fbatch];
									// $in_charge=$reprocess_batch_no_idArr[$fbatch]['in_charge'];
									  
										if($in_charge==1)
										{
											$no_of_ok_batchArr[$fbatch]=$fbatch;
											 //echo $in_charge.'='.$fbatch.'<br>';
										}
										$fnc_no_batch=$FunctionalNoProd_date3Arr[$fbatch][$in_charge_id];
										$recipebatch=rtrim($functional_batchWiseRecipeArr[$in_charge_id][$prod_date][$fnc_no_batch],',');
										 $rft_recipe_batchArr=array_unique(explode(",",$recipebatch));
										 // count($rft_recipe_batchArr).'<br>';
										$recipebatchArr[$recipebatch]=$recipebatch;
										// echo $fnc_no_batch.'='.$recipebatch.'<br>';

										//$recipe_count_batch+=$recipe_count_batchWiseRecipeArr[$in_charge_id][$prod_date][$fbatch];
								}
								   //print_r($recipebatchArr);
							  //echo count($recipebatchArr);
							  $recipebatchArr_all=implode(",",$recipebatchArr);
							 // echo $recipebatchArr_all.', ';
							  $tot_rft_recipe_batchArr=array_unique(explode(",",$recipebatchArr_all));
								
								$tot_batch_count=count($first_Id_batchArr);
								$tot_multi_recipe_count=count($tot_rft_recipe_batchArr);
								 //  echo $tot_multi_recipe_count.'='.$tot_batch_count.'<br>';;
							
								if(count($no_of_ok_batchArr)>0) 
								{
									$for_no_of_okBatchArr=array_diff($first_Id_batchArr,$no_of_ok_batchArr);
									//print_r($first_Id_batchArr); 
									
								}
								else
								{
									$for_no_of_okBatchArr=$first_Id_batchArr;
									//echo "B";
								}
							// echo "<pre>";
							 // print_r($for_no_of_okBatchArr);
								$rft_recipeDateWise_all="";$topping_not_ok_lot_recipe_all="";$tot_rft_recipe_batch_count=0;
								$topping_OK_batchArr=$topping_not_OK_batchArr=array();$topping_recipeDateWise_batchTot=0;
								 $rft_ok_batchArr=array();
								 asort($first_Id_batchArr);   
								foreach($first_Id_batchArr as $rft_batchId)
								{
										$recipeDateWise_batch=rtrim($recipeDateWise_batch_Arr[$in_charge_id][$prod_date][$rft_batchId],',');
																	
									if($recipeDateWise_batch!="") //For topping batch
									{
										$recipeDateWise_batchArr=array_unique(explode(",",$recipeDateWise_batch));
										$topping_recipeDateWise_batchTot+=count($recipeDateWise_batchArr);
									
										 foreach($recipeDateWise_batchArr as $toppingBNo)
										 {
											$topping_OK_batchArr[$toppingBNo]=$toppingBNo;
										 }
									}
									
									$rft_recipeDateWiseAll=chop($rft_recipeDateWise_batch_Arr[$in_charge_id][$prod_date][$rft_batchId],',');
									$topping_OK_rft_recipeDateWiseAll=chop($topping_OK_recipeDateWise_batch_Arr[$in_charge_id][$prod_date][$rft_batchId],',');

									
									$rft_recipeDateWiseArr=explode(",",$rft_recipeDateWiseAll);
									
									$topping_OK_rft_recipeDateWiseArr=array_unique(explode(",",$topping_OK_rft_recipeDateWiseAll));//for Topping NOt OK
									if(count($rft_recipeDateWiseArr)==1 && $rft_recipeDateWiseAll!="") //Signle Recipe found
									{
										$rft_recipeDateWise_all= implode(",",($topping_OK_rft_recipeDateWiseArr)); //Topping OK Lot
										foreach($topping_OK_rft_recipeDateWiseArr as $tokBatch)
										{
												//  echo $tokBatch.'A<br>';
											$rft_ok_batchArr[$tokBatch]=$tokBatch;
										}	
									}
									else
									{
										if(count($topping_OK_rft_recipeDateWiseArr)>0)
										{
												foreach($topping_OK_rft_recipeDateWiseArr as $toppingNotBNo)
												{
													// echo $toppingNotBNo.'B<br>';
													if($toppingNotBNo!="")
													{
														$topping_not_OK_batchArr[$toppingNotBNo]=$toppingNotBNo;
													}
													
												}
										}								
									}
									
								}
								//echo "<pre>";
								//print_r($topping_OK_batchArr);
								//echo "<pre>";
								//============Batch Id===================

								$topping_not_ok_lot_recipe_all=implode(",",$topping_not_OK_batchArr);
								$topping_not_ok_lot_recipe_all=ltrim($topping_not_ok_lot_recipe_all,',');
								$topping_not_ok_lot_recipe_allArr=explode(",",$topping_not_ok_lot_recipe_all);
								//if($first_batch_re) $unload_ttl_msg="";else  $unload_ttl_msg=$first_batch;
								//===========Not OK batch count==============
								$topping_not_OK_batchMsgArr=array(); $ToppingNOT_OkBatchChk_Arr=array(); 
								asort($topping_not_ok_lot_recipe_allArr);   
								foreach($topping_not_ok_lot_recipe_allArr as $tbatchId)
								{
										$tbatch=$batch_NoArr[$tbatchId];
										$FunctionalNo=$FunctionalNoProd_date3Arr[$tbatchId][$in_charge_id];
										$FunctionalNoWiseBatch=rtrim($FunctionalNoWisebatchProd_date3Arr[$FunctionalNo][$in_charge_id],',');
									 	//echo $FunctionalNoWiseBatch.'='.$FunctionalNo.'<br>';
										$FunctionalNoWiseBatchArr=explode(",",$FunctionalNoWiseBatch);
									if($FunctionalNoWiseBatch!=="" && $ToppingNOT_OkBatchChk_Arr[$FunctionalNo][$in_charge_id]=="")
									{
										$top_ok_not=0; 
										if(count($FunctionalNoWiseBatchArr)>1 && $tbatch!="") //count($FunctionalNoWiseBatchArr).'<br>';
										{
											$exten_no=$batch_ReDyingExtArr[$tbatchId];
											if($exten_no)
											{
												$tbatch_ttl=$tbatch.'( multi)';
											}
											else
											{
												$tbatch_ttl=$tbatch.'(multi)';
											}
											$topping_not_OK_batchMsgArr[$tbatch_ttl]=$tbatch_ttl;
											
										}
										else
										{
											if($tbatch!="")
											{
												$tbatch_ttl=$tbatch;
												$topping_not_OK_batchMsgArr[$tbatch_ttl].=$tbatch_ttl.',';
												 // echo $tbatch_ttl.'B'.$FunctionalNo.'<br>';
											}
											
										}
										$ToppingNOT_OkBatchChk_Arr[$FunctionalNo][$in_charge_id]=$FunctionalNo;
									}
								}
 
							//===============Topping  OK Lot====================
							$rft_recipeDateWise_all="";
							//$rft_recipeDateWise_allArr=implode(",",$rft_ok_batchArr);
							$tOKbatch_ttl="";$tot_rft_recipe_batch_count=0;
							$topping_OK_batchMsgArr=array();
							asort($rft_ok_batchArr);
								foreach($rft_ok_batchArr as $tOkbatchId) //===========OK batch count===========
								{
									$tOkbatch=$batch_NoArr[$tOkbatchId];
									$tOKFunctionalNo=$FunctionalNoProd_date3Arr[$tOkbatchId][$in_charge_id];
									$Top_OK_FunctionalNoWiseBatch=rtrim($FunctionalNoWisebatchProd_date3Arr[$tOKFunctionalNo][$in_charge_id],',');
									$OK_FunctionalNoWiseBatchArr=explode(",",$Top_OK_FunctionalNoWiseBatch);
									if($Top_OK_FunctionalNoWiseBatch!=="" && $Topping_OkBatchChk_Arr[$tOKFunctionalNo][$in_charge_id]=="")
									{
										if(count($OK_FunctionalNoWiseBatchArr)>1 && $tOkbatch!="")  
										{
											$exten_no=$batch_ReDyingExtArr[$tOkbatchId];
											if($exten_no)
											{
												$tOKbatch_ttl=$tOkbatch.'( multi)';
											}
											else
											{
												$tOKbatch_ttl=$tOkbatch.'(multi)';
											}
											$topping_OK_batchMsgArr[$tOKbatch_ttl]=$tOKbatch_ttl;
											$tot_rft_recipe_batch_count++;
										}
										 else
										{
											if($tOkbatch!="")
											{
												$tOKbatch_ttl=$tOkbatch;
												$topping_OK_batchMsgArr[$tOKbatch_ttl].=$tOKbatch_ttl.',';
												$tot_rft_recipe_batch_count++;
											}
										}
										$Topping_OkBatchChk_Arr[$tOKFunctionalNo][$in_charge_id]=$tOKFunctionalNo;
									}
								}
								$rft_recipeDateWise_all='';
								$rft_recipeDateWise_all2=implode(",",$topping_OK_batchMsgArr);
								$rft_recipeDateWise_all=chop($rft_recipeDateWise_all2,","); 
								$rft_recipeDateWise_all=str_replace(",,",",",$rft_recipeDateWise_all);
								//======Topping batch count==========
								$topping_batchMsgArr=array();$Not_ok_lotChk_Arr=array();	$tOPbatch_ttl="";$topping_tot_count=0;
								asort($topping_OK_batchArr);
								foreach($topping_OK_batchArr as $topingbatchId)  
								{
									$topingbatch=$batch_NoArr[$topingbatchId];
									$top_FunctionalNo=$FunctionalNoProd_date3Arr[$topingbatchId][$in_charge_id];
									$Top_FunctionalNoWiseBatch=rtrim($FunctionalNoWisebatchProd_date3Arr[$top_FunctionalNo][$in_charge_id],',');
									 	//echo $Top_FunctionalNoWiseBatch.'='.$top_FunctionalNo.'<br>';
									$top_FunctionalNoWiseBatchArr=explode(",",$Top_FunctionalNoWiseBatch);
									if($Top_FunctionalNoWiseBatch!="" && $ToppingBatchChk_Arr[$top_FunctionalNo][$in_charge_id]=="")
									{	
									if(count($top_FunctionalNoWiseBatchArr)>1 && $topingbatch!="")  
										{
											
											$exten_no=$batch_ReDyingExtArr[$topingbatchId];
											if($exten_no)
											{
												$tOPbatch_ttl=$topingbatch.'( multi)';
											}
											else
											{ 
												$tOPbatch_ttl=$topingbatch.'(multi)';
											}
											$topping_batchMsgArr[$tOPbatch_ttl]=$tOPbatch_ttl;
											 $topping_tot_count++;
											//echo $tOPbatch_ttl.',';
										}
										 else
										{
											if($topingbatch!="")
											{
												$tOPbatch_ttl=$topingbatch;
												$topping_batchMsgArr[$tOPbatch_ttl].=$tOPbatch_ttl.',';
												$topping_tot_count++;
												//echo $tOPbatch_ttl.'-B';
											}
										}
										$ToppingBatchChk_Arr[$top_FunctionalNo][$in_charge_id]=$top_FunctionalNo;
									}
								}
									//print_r($topping_batchMsgArr);
									$toppingbatchTTL=implode(",",$topping_batchMsgArr);
									$toppingbatchTTL=str_replace(",,",",",$toppingbatchTTL);
									//======Not OK Lot  count==========
								$not_Ok_lot_batchMsgArr=array();	$NOT_Ok_lot_batch_ttl="";$not_lot_tot_count=0;
								asort($no_of_ok_batchArr);$not_ok_FunctionalNoWiseBatch="";
								foreach($no_of_ok_batchArr as $Not_ok_batchId)  
								{
									$Not_ok_batch=$batch_NoArr[$Not_ok_batchId];
									$not_lot_FunctionalNo="";
									$not_lot_FunctionalNo=$FunctionalNoProd_date3Arr[$Not_ok_batchId][$in_charge_id];
									$not_ok_FunctionalNoWiseBatch=rtrim($FunctionalNoWisebatchProd_date3Arr[$not_lot_FunctionalNo][$in_charge_id],',');
									
								$Not_ok_FunctionalNoWiseBatchArr=explode(",",$not_ok_FunctionalNoWiseBatch);
								if($not_ok_FunctionalNoWiseBatch!="" && $Not_ok_lotChk_Arr[$not_lot_FunctionalNo][$in_charge_id]=="")
								{
									if(count($Not_ok_FunctionalNoWiseBatchArr)>1 && $Not_ok_batch!="")  
										{
											$not_lot_batch_ttl='';
											$exten_no=$batch_ReDyingExtArr[$Not_ok_batchId];
											if($exten_no)
											{
												$not_lot_batch_ttl=$Not_ok_batch.'( multi)';
											}
											else
											{
												$not_lot_batch_ttl=$Not_ok_batch.'(multi)';
											}
											$not_Ok_lot_batchMsgArr[$not_lot_batch_ttl]=$not_lot_batch_ttl;
											// $not_lot_tot_count++;
											//echo $tOPbatch_ttl.',';
										}
										 else
										{
											
											if($Not_ok_batch!="")
											{
												$not_lot_batch_ttl="";
												$not_lot_batch_ttl=$Not_ok_batch;
												$not_Ok_lot_batchMsgArr[$not_lot_batch_ttl].=$not_lot_batch_ttl.',';
												//$not_lot_tot_count++;
										// if($in_charge_id==354 ) echo $in_charge_id.'='.$not_lot_batch_ttl.'<br>';
											}
										}
										$Not_ok_lotChk_Arr[$not_lot_FunctionalNo][$in_charge_id]=$not_lot_FunctionalNo;
									}
								}
									//print_r($topping_batchMsgArr);
									 
									$NOT_OK_Lot_batch_TTL=implode(",",$not_Ok_lot_batchMsgArr);
									$NOT_OK_Lot_batchTTL="";
									if($NOT_OK_Lot_batch_TTL!="")
									{
										$NOT_OK_Lot_batchTTL=str_replace(",,",",",$NOT_OK_Lot_batch_TTL);
									}
									
										//======No Of Unload count==========
								$no_of_unload_batchMsgArr=array();	$no_of_unload_tot_count=0;
								 
								foreach($first_Id_batchArr as $first_bno)  
								{  //batch_NoArr
									$batch_no=$batch_NoArr[$first_bno];
									$first_bno_FunctionalNo=$FunctionalNoProd_date3Arr[$first_bno][$in_charge_id];
									$first_FunctionalNoWiseBatch=rtrim($FunctionalNoWisebatchProd_date3Arr[$first_bno_FunctionalNo][$in_charge_id],',');

									

									$first_FunctionalNoWiseBatchArr=explode(",",$first_FunctionalNoWiseBatch);
									if($first_FunctionalNoWiseBatch!="" &&  $first_bno_FunctionalNoChk_Arr[$first_bno_FunctionalNo][$in_charge_id]=="")
									{
										
										if(count($first_FunctionalNoWiseBatchArr)>1 && $first_bno!="")  
										{
											$exten_no=$batch_ReDyingExtArr[$first_bno];
											if($exten_no)
											{
												$first_batch_ttl=$batch_no.'( multi)';
											}
											else
											{
												$first_batch_ttl=$batch_no.'(multi)';
											}
											$no_of_unload_batchMsgArr[$first_batch_ttl]=$first_batch_ttl;
											  $no_of_unload_tot_count++;
											 // echo $exten_no.'='.$first_bno.'='.$first_batch_ttl.'<br>';
										}
										 else
										{
											
											if($first_bno!="")
											{
												$first_batch_ttl=$batch_no;
												$no_of_unload_batchMsgArr[$first_batch_ttl].=$first_batch_ttl.',';
												 $no_of_unload_tot_count++;
												 //echo $m_first_FunctionalNoWiseBatch.'-B';
											}
										}
										$first_bno_FunctionalNoChk_Arr[$first_bno_FunctionalNo][$in_charge_id]=$first_bno_FunctionalNo;
									}
									
								}
									//print_r($topping_batchMsgArr);
									$no_of_unload_batchMsg_TTL=implode(",",$no_of_unload_batchMsgArr);
									$no_of_unload_batchMsg_TTL=str_replace(",,",",",$no_of_unload_batchMsg_TTL);
									//$for_no_of_okBatchArr
									$no_of_OK_batchMsgArr=array();$no_of_OK_Batch_count=0;
									asort($for_no_of_okBatchArr);
									foreach($for_no_of_okBatchArr as $no_ok_id)  
									{
										//$in_charge_chk=$reason_typeRe_batch_idArr2[$in_charge_id][$prod_date][$no_ok_id];
										

										
									//	echo $reason_not_unload.'='.$in_charge_id.'='.$no_ok_id.'<br>';

										$no_ok=$batch_NoArr[$no_ok_id];
										$ok_bno_FunctionalNo=$FunctionalNoProd_date3Arr[$no_ok_id][$in_charge_id];
										$ok_bno_FunctionalNoWiseBatch=rtrim($FunctionalNoWisebatchProd_date3Arr[$ok_bno_FunctionalNo][$in_charge_id],',');
									
										$reson_fnc_no=$Fnc_no_reason_batch_no_idArr[$ok_bno_FunctionalNo];
									//	echo $ok_bno_FunctionalNo.'='.$reson_fnc_no.'='.$no_ok_id.'<br>';
										$OK_FunctionalNoWiseBatchArr=explode(",",$ok_bno_FunctionalNoWiseBatch);
										if($reson_fnc_no!=1 && $ok_bno_FunctionalNoWiseBatch!="" &&  $OK_bno_FunctionalNoChk_Arr[$ok_bno_FunctionalNo][$in_charge_id]=="")
										{
											if(count($OK_FunctionalNoWiseBatchArr)>1 && $no_ok!="")  
											{
												$exten_no=$batch_ReDyingExtArr[$no_ok_id];
												if($exten_no)
												{
													$OK_batch_ttl=$no_ok.'( multi)';
												}
												else
												{
													$OK_batch_ttl=$no_ok.'(multi)';
												}
												$no_of_OK_batchMsgArr[$OK_batch_ttl]=$OK_batch_ttl;
												  $no_of_OK_Batch_count++;
												//if($no_ok=='23-4982') echo $ok_bno_FunctionalNo.'='.$no_ok.'='.$no_ok_id.','; 
											}
											 else
											{
												
												if($no_ok!="")
												{
													$OK_batch_ttl=$no_ok;
													$no_of_OK_batchMsgArr[$OK_batch_ttl].=$OK_batch_ttl.',';
													 $no_of_OK_Batch_count++;
													// echo $tOPbatch_ttl.'-B';
												}
											}
											$OK_bno_FunctionalNoChk_Arr[$ok_bno_FunctionalNo][$in_charge_id]=$ok_bno_FunctionalNo;
										}
									}
								 	//print_r($no_of_OK_batchMsgArr);
									$no_of_OK_batchMsg_ttl=implode(",",$no_of_OK_batchMsgArr);
									$no_of_OK_batchMsg_ttl=str_replace(",,",",",$no_of_OK_batchMsg_ttl);
									//$reason_typeRe_batch_idArr2[$In_charge][$min_prod_date][$row[csf('re_batch_id')]]
									if(count($no_of_ok_batchArr)>0) //Resaon Type Inchare found//re dying must
									{
										$no_of_ok_batchTot=$no_of_OK_Batch_count;
										$no_of_ok_batchMsg_TTL=$no_of_OK_batchMsg_ttl;
										// echo "A";
									}
									else
									{
										 $no_of_ok_batchTot=$no_of_unload_tot_count;
										 $no_of_ok_batchMsg_TTL=$no_of_unload_batchMsg_TTL;
										//  echo "B";
									}
									$topping_not_OK_batchMsgArr=implode(", ",$topping_not_OK_batchMsgArr);
									 $topping_not_OK_batchMsgArr_all=str_replace(",,",",",$topping_not_OK_batchMsgArr);
							?>
							 
							 
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i.$in_charge_id; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i.$in_charge_id; ?>" valign="top">
								<td width="50" align="center" ><? echo $i;  ?></td>
								<td width="100" align="center" title="<?=$first_batch_re;?>"><? echo date('d-M-Y',strtotime($prod_date)); ?></td>
								<td width="120" align="center" title="<?=chop($no_of_unload_batchMsg_TTL,',');?>"><?   echo $no_of_unload_tot_count;//$no_of_unload_batch; ?></td>
								<td width="100" align="center" title="<?=chop($no_of_ok_batchMsg_TTL,',');?>"><?   
								
								echo $no_of_ok_batchTot;
								 ?></td>
								<td width="100"  align="center"title="OK Batch/Unload Batch*100">
											<?  echo fn_number_format(($no_of_ok_batchTot/$no_of_unload_tot_count)*100,2); ?></td>
								<td width="150" align="center"><p style="word-break:break-all"><?  echo chop($NOT_OK_Lot_batchTTL,',');//$not_ok_lots; ?></p></td>
								<td width="100" align="center" title="<?=chop($toppingbatchTTL,',');?>"><? echo $topping_tot_count;//$topping_recipeDateWise_batchTot; ?></td>
								<?
							 	if($tot_multi_recipe_count>$tot_batch_count)
								 {
									$rft_recipeDateWise_all="";$tot_rft_recipe_batch_count="";$rft_recipeDateWise_all="";
									   
								 }
								 else{
									$rft_recipeDateWise_all=$rft_recipeDateWise_all;$tot_rft_recipe_batch_count=$tot_rft_recipe_batch_count;$rft_recipeDateWise_all=$rft_recipeDateWise_all;
									 
								 }
							 ?>

								<td width="100" align="center" title="<? echo $rft_recipeDateWise_all; ?>"><? echo $tot_rft_recipe_batch_count; ?></td>
								<td width="100" align="center" title="Topping RFT/Topping Batch*100"><? echo  fn_number_format(($tot_rft_recipe_batch_count/$topping_tot_count)*100,2); ?></td>
							 
								<td width="150" align="center"><p style="word-break:break-all"><? echo $rft_recipeDateWise_all; ?></p></td>
								<td width="" align="center"><p style="word-break:break-all"><? echo  chop($topping_not_OK_batchMsgArr_all,","); ?></p></td>
									
							</tr>
							<?
								$i++;
								$tot_no_of_unload_batch += $no_of_unload_tot_count;
								$tot_no_of_ok_batchTot += $no_of_ok_batchTot;
								$tot_topping_recipeDateWise_batchTot += $topping_tot_count;
								$tot_tot_rft_recipe_batch_count += $tot_rft_recipe_batch_count;

							
						 }
						
						?>
					</tbody>
					 
						<tr style="text-align: center; background:#CCC;font-size:16px;">
							<td   align="right" colspan="2"><b>Total</b></td>
							<td width="80" align="center"><b><? echo number_format($tot_no_of_unload_batch, 2, '.', ''); ?></b></td>
							<td width="80" align="center"><b><? echo number_format($tot_no_of_ok_batchTot, 2, '.', ''); ?></b></td>
							<td width="80" title="No Of Ok/No Of Unload Batch*100"><b><? echo fn_number_format(($tot_no_of_ok_batchTot/$tot_no_of_unload_batch)*100, 2, '.', ''); ?></b></td>
							<td width="80">&nbsp;</td>
							<td width="100"><b><? echo number_format($tot_topping_recipeDateWise_batchTot, 2, '.', ''); ?></b></td>
							<td width="80"><b><? echo number_format($tot_tot_rft_recipe_batch_count, 2, '.', ''); ?></b></td>
							 
							<td width="80"  title="Topping RFT/Topping Batch*100"><b><? echo fn_number_format(($tot_tot_rft_recipe_batch_count/$tot_topping_recipeDateWise_batchTot)*100, 2, '.', ''); ?></b></td>
							<td width="150">&nbsp;</td>
							<td width="">&nbsp;</td>
                            
                            </tr>
							 
						 
					 
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