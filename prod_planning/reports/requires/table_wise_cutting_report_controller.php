<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');

$user_name	= $_SESSION['logic_erp']['user_id'];
$data		= $_REQUEST['data'];
$action		= $_REQUEST['action'];


//--------------------------------------------------------------------------------------------------------------------
if($action=="print_button_variable_setting")
{
	$print_report_format_arr = return_library_array("select format_id,format_id from lib_report_template where template_name in($data) and module_id=4 and report_id=79 and is_deleted=0 and status_active=1","format_id","format_id");
	echo "print_report_button_setting('".implode(',',$print_report_format_arr)."');\n";
	exit();
}

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 120, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id in($data) order by location_name","id,location_name", 0, "--Select Location--", $selected, "" );
	exit();
}

if ($action=="load_drop_down_floor")
{
	$dataEx = explode("_", $data);
	echo create_drop_down( "cbo_floor_id", 120, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and company_id in($dataEx[0]) and location_id in($dataEx[1]) and production_process=1 order by floor_name","id,floor_name", 0, "--Select Floor--", $selected, "" );
	exit();
}

if($action=="style_search_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	//echo $style_id;die;

	?>
   <script>
		function js_set_value( str )
		{

			$('#txt_selected_no').val(str);
			parent.emailwindow.hide();
		}
    </script>


    <?
	$buyer=str_replace("'","",$buyer);
	$company=str_replace("'","",$company);
	//$job_year=str_replace("'","",$job_year);
	if($buyer!=0) $buyer_cond=" and a.buyer_name=$buyer"; else $buyer_cond="";
	if($db_type==0)
	{
		if($job_year!=0) $job_year_cond=" and year(a.insert_date)=$job_year"; else $job_year_cond="";
		$select_date=" year(a.insert_date)";
	}
	else if($db_type==2)
	{
		if($job_year!=0) $job_year_cond=" and to_char(a.insert_date,'YYYY')=$job_year"; else $job_year_cond="";
		$select_date=" to_char(a.insert_date,'YYYY')";
	}

	$sql = "SELECT a.id,a.style_ref_no,a.job_no,a.job_no_prefix_num,$select_date as year,b.grouping from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$company $buyer_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 group by a.id,a.style_ref_no,a.job_no,a.job_no_prefix_num,$select_date,b.grouping order by a.id DESC";
	// echo $sql; die;
	echo create_list_view("list_view", "Style Ref No,Int. Ref.,Job No,Year","100,100,50,80","400","300",0, $sql , "js_set_value", "id,job_no,style_ref_no", "", 1, "0", $arr, "style_ref_no,grouping,job_no_prefix_num,year", "","setFilterGrid('list_view',-1)","0","","") ;
	//echo "<input type='hidden' id='txt_selected_id' />";
	//echo "<input type='hidden' id='txt_selected' />";
	echo "<input type='hidden' id='txt_selected_no' />";

	?>
    <script language="javascript" type="text/javascript">
	var style_no='<? echo $txt_ref_no;?>';
	var style_id='<? echo $txt_style_ref_id;?>';
	var style_des='<? echo $txt_style_ref_no;?>';
	//alert(style_id);
	if(style_no!="")
	{
		style_no_arr=style_no.split(",");
		style_id_arr=style_id.split(",");
		style_des_arr=style_des.split(",");
		var str_ref="";
		for(var k=0;k<style_no_arr.length; k++)
		{
			str_ref=style_no_arr[k]+'_'+style_id_arr[k]+'_'+style_des_arr[k];
			js_set_value(str_ref);
		}
	}
	</script>

    <?

	exit();
}

$company_arr		= return_library_array( "select id, company_name from lib_company",'id','company_name');
$location_arr		= return_library_array( "select id, location_name from lib_location",'id','location_name');
$color_library		= return_library_array( "select id,color_name from lib_color", "id", "color_name"  ); $order_no_library	= return_library_array( "select id,po_number from wo_po_break_down", "id", "po_number"  );
$buyer_arr			= return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
// $table_arr			= return_library_array( "select id, table_no from lib_cutting_table",'id','table_no');
$floor_arr          = return_library_array( "select id, floor_name from lib_prod_floor",'id','floor_name');

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$company_name			= str_replace( "'", "", $cbo_company_name );
	$rept_type				= str_replace( "'", "", $type );
	//echo $repttype;
	$buyer_name				= str_replace( "'", "", $cbo_buyer_name );
	$job_no					= str_replace( "'", "", $txt_job_no );
	$cutting_no				= str_replace( "'", "", $txt_cutting_no );
	$from_date				= str_replace( "'", "", $txt_date_from );
	$to_date				= str_replace( "'", "", $txt_date_to );
	$working_company_id		= str_replace( "'", "", $cbo_working_company_name );
	$location_id			= str_replace( "'", "", $cbo_location_name );
	$txt_job_no_hidden		= str_replace( "'", "", $txt_job_no_hidden);
	$floor_id				= str_replace( "'", "", $cbo_floor_id);

	$sql_cond = "";
	$sql_cond .= ($company_name!="") ? " and a.company_name in($company_name)" : "";
	$sql_cond .= ($buyer_name!=0) ? " and a.buyer_name in($buyer_name)" : "";
	$sql_cond .= ($job_no!="") ? " and b.job_no='$job_no'" : "";
	// $sql_cond .= ($cutting_no!=0) ? " and b.cutting_no in($cutting_no)" : "";
	$sql_cond .= ($from_date!=0) ? " and b.entry_date between '$from_date' and '$to_date'" : "";
	$sql_cond .= ($working_company_id!="") ? " and b.working_company_id in($working_company_id)" : "";
	$sql_cond .= ($location_id!="") ? " and b.location_id in($location_id)" : "";
	$sql_cond .= ($floor_id!="") ? " and b.floor_id in($floor_id)" : "";
	if($rept_type==3)
	{

		$sql = "SELECT b.id, a.company_name,a.buyer_name,a.style_ref_no,a.style_description,b.table_no,b.job_no,b.cutting_no,b.working_company_id,b.location_id,b.entry_date,b.floor_id,c.order_cut_no,c.gmt_item_id,c.color_id,c.plies,c.roll_data,d.bundle_no,d.order_id,d.size_qty,e.table_no as table_name,e.id as table_id from wo_po_details_master a, ppl_cut_lay_mst b, ppl_cut_lay_dtls c,ppl_cut_lay_bundle d,lib_cutting_table e where a.job_no=b.job_no and b.id=c.mst_id and c.id=d.dtls_id and b.id=d.mst_id and b.table_no=e.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $sql_cond order by e.table_no";
		// echo $sql;die;
		$res = sql_select($sql);
		$data_array = array();
		$cut_id_array = array();
		$batch_id_array = array();
		$manual_batch_array = array();
		$cut_wise_array = array();
		foreach ($res as $v)
		{
			$cut_id_array[$v['ID']] = $v['ID'];
			$data_array[$v['TABLE_ID']][$v['CUTTING_NO']]['id'] = $v['ID'];
			$data_array[$v['TABLE_ID']][$v['CUTTING_NO']]['company_name'] = $v['COMPANY_NAME'];
			$data_array[$v['TABLE_ID']][$v['CUTTING_NO']]['working_company_id'] = $v['WORKING_COMPANY_ID'];
			$data_array[$v['TABLE_ID']][$v['CUTTING_NO']]['job_no'] = $v['JOB_NO'];
			$data_array[$v['TABLE_ID']][$v['CUTTING_NO']]['floor_id'] = $v['FLOOR_ID'];
			$data_array[$v['TABLE_ID']][$v['CUTTING_NO']]['location_id'] = $v['LOCATION_ID'];
			$data_array[$v['TABLE_ID']][$v['CUTTING_NO']]['entry_date'] = $v['ENTRY_DATE'];
			$data_array[$v['TABLE_ID']][$v['CUTTING_NO']]['buyer_name'] = $v['BUYER_NAME'];
			$data_array[$v['TABLE_ID']][$v['CUTTING_NO']]['style_ref_no'] = $v['STYLE_REF_NO'];
			$data_array[$v['TABLE_ID']][$v['CUTTING_NO']]['style_description'] = $v['STYLE_DESCRIPTION'];
			$data_array[$v['TABLE_ID']][$v['CUTTING_NO']]['order_cut_no'] = $v['ORDER_CUT_NO'];
			$data_array[$v['TABLE_ID']][$v['CUTTING_NO']]['gmt_item_id'] = $v['GMT_ITEM_ID'];
			$data_array[$v['TABLE_ID']][$v['CUTTING_NO']]['color_id'] = $v['COLOR_ID'];
			$data_array[$v['TABLE_ID']][$v['CUTTING_NO']]['table_name'] = $v['TABLE_NAME'];
			$data_array[$v['TABLE_ID']][$v['CUTTING_NO']]['plies'] = $v['PLIES'];
			$data_array[$v['TABLE_ID']][$v['CUTTING_NO']]['total_bundle']++;
			$data_array[$v['TABLE_ID']][$v['CUTTING_NO']]['size_qty'] += $v['SIZE_QTY'];
			$roll_data=explode("**",$v['ROLL_DATA']);
			foreach($roll_data as $key =>$val)
			{
				$roll_data_ex=explode("=",$val);
				if($roll_data_ex[6]==0)
				{
					if($roll_data_ex[5]!="")
					{
						$manual_batch_array[$v['CUTTING_NO']] .= $roll_data_ex[5].",";
					}
				}
				$batch_id_array[$roll_data_ex[6]] = $roll_data_ex[6];
				$cut_wise_array[$v['CUTTING_NO']] .= $roll_data_ex[6].",";
			}

		}
		// echo "<pre>";print_r($data_array);
		$cut_id_cond = where_con_using_array($cut_id_array,0,"mst_id");
		$size_ratio_arr = return_library_array( "select mst_id, sum(SIZE_RATIO) as SIZE_RATIO  from PPL_CUT_LAY_SIZE_DTLS where status_active=1 $cut_id_cond group by mst_id",'mst_id','SIZE_RATIO');

		$batch_id_cond = where_con_using_array($batch_id_array,0,"id");
		$batch_arr = return_library_array( "select id, BATCH_NO  from PRO_BATCH_CREATE_MST where status_active=1 $batch_id_cond",'id','BATCH_NO');

		?>

		<fieldset style="width:<? echo $div_width; ?>px;">

			<table width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" border="0"  rules="all">

				<tr style="font-weight:bold;font-size:20px">
					<td align="center" width="1450px" colspan="20">
						<?
						$com_name = str_replace( "'", "", $cbo_working_company_name );
						echo $company_arr[$com_name]."<br/>"."Table Wise Cutting Report";
						?>

					</td>
				</tr>

			</table>

			<!-- ========================== header part ======================== -->
			<table class="rpt_table" width="1720px" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
					<tr>
						<th width="40">Sl</th>
						<th width="130">Company Name</th>
						<th width="130">Working company</th>
						<th width="100">Location</th>
						<th width="80">Floor</th>
						<th width="70">Cutting Date</th>
						<th width="100">System Cut No.</th>
						<th width="50">Order Cut No.</th>
						<th width="100">Buyer Name</th>
						<th width="100">Job No</th>
						<th width="100">Style Reff</th>
						<th width="100">Style Description</th>
						<th width="100">Gmts Item</th>
						<th width="100">Color Name</th>
						<th width="60">Table No</th>
						<th width="70">Batch No</th>
						<th width="70">Total Size Ratio</th>
						<th width="70">Plies</th>
						<th width="70">Total <br>Bundle No</th>
						<th width="80">Total Cut Qty.</th>
					</tr>
				</thead>
			</table>
			<!-- ========================== body part ======================== -->
			<div style="width: 1740;overflow-y:auto;max-height:300px;">
				<table class="rpt_table"  width="1720px" cellpadding="0" cellspacing="0" border="1" rules="all" >
					<tbody>
						<?
						$i=1;
						$gr_size_ratio=0;
						$gr_plies = 0;
						$gr_bundle_qty = 0;
						$gr_cut_qty = 0;
						foreach ($data_array as $tbl_id => $tbl_data)
						{
							$table_wise_size_ratio=0;
							$table_wise_tot_plies = 0;
							$table_wise_tot_bundle_qty = 0;
							$table_wise_tot_cut_qty = 0;
							foreach ($tbl_data as $cut_no => $row)
							{
								if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								$b_id_arr = array_unique(array_filter(explode(",",$cut_wise_array[$cut_no])));
								$manual_batch_name = implode(array_unique(array_filter(explode(",",$manual_batch_array[$cut_no]))));
								$batch_name = "";
								foreach($b_id_arr as $key=>$val)
								{
									$batch_name.= ($batch_name=="") ? $batch_arr[$val]: ",".$batch_arr[$val];
								}
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<td width="40"><?=$i;?></td>
									<td width="130"><p><?=$company_arr[$row['company_name']];?></p></td>
									<td width="130"><p><?=$company_arr[$row['working_company_id']];?></p></td>
									<td width="100"><?=$location_arr[$row['location_id']];?></td>
									<td width="80"><p><?=$floor_arr[$row['floor_id']];?></p></td>
									<td width="70"><p><?= $row['entry_date'];?></p></td>
									<td width="100"><?=$cut_no;?></td>
									<td width="50"><?= $row['order_cut_no'];?></td>
									<td width="100"><p><?=$buyer_arr[$row['buyer_name']];?></p></td>
									<td width="100"><?=$row['job_no']?></td>
									<td width="100"><p><?= $row['style_ref_no'];?></p></td>
									<td width="100"><p><?=$row['style_description'];?></p></td>
									<td width="100"><p><?=$garments_item[$row['gmt_item_id']];?></p></td>
									<td width="100"><p><?= $color_library[$row['color_id']];?></p></td>
									<td align="right" width="60"><?=$row['table_name'];//$table_arr[$tbl_id];?></td>
									<td width="70"><p><?=$batch_name.$manual_batch_name;?></p></td>
									<td align="right" width="70"><?=$size_ratio_arr[$row['id']];?></td>
									<td align="right" width="70"><?=$row['plies'];?></td>
									<td align="right" width="70"><?= $row['total_bundle'];?><br></td>
									<td align="right" width="80"><?=$row['size_qty'];?></td>
								</tr>
								<?
								$i++;
								$gr_size_ratio +=$size_ratio_arr[$row['id']];
								$gr_plies += $row['plies'];
								$gr_bundle_qty +=  $row['total_bundle'];
								$gr_cut_qty += $row['size_qty'];

								$table_wise_size_ratio +=$size_ratio_arr[$row['id']];
								$table_wise_tot_plies += $row['plies'];
								$table_wise_tot_bundle_qty += $row['total_bundle'] ;
								$table_wise_tot_cut_qty +=$row['size_qty'] ;
							}
							?>
							<tr style="background: #cdcddc;text-align:right;font-weight:bold;">
								<td colspan="16">Table wise sub total</td>
								<td><?=number_format($table_wise_size_ratio,0);?></td>
								<td width="70"><?=number_format($table_wise_tot_plies,0);?></td>
								<td width="70"><?=number_format($table_wise_tot_bundle_qty,0);?></td>
								<td width="80"><?=number_format($table_wise_tot_cut_qty,0);?></td>
							</tr>
							<?
						}
						?>
					</tbody>
				</table>
			</div>
			<!-- ========================== footer part ======================== -->

			<table class="rpt_table"  width="1720px" cellpadding="0" cellspacing="0" border="1" rules="all" >
				<tfoot>
					<tr>
						<th width="40"></th>
						<th width="130"></th>
						<th width="130"></th>
						<th width="100"></th>
						<th width="80"></th>
						<th width="70"></th>
						<th width="100"></th>
						<th width="50"></th>
						<th width="100"></th>
						<th width="100"></th>
						<th width="100"></th>
						<th width="100"></th>
						<th width="100"></th>
						<th width="100"></th>
						<th width="60"></th>
						<th width="70"></th>
						<th width="70"><?=number_format($gr_size_ratio,0);?></th>
						<th width="70"><?=number_format($gr_plies,0);?></th>
						<th width="70"><?=number_format($gr_bundle_qty,0);?></th>
						<th width="80"><?=number_format($gr_cut_qty,0);?></th>
					</tr>
				</tfoot>
			</table>

		</fieldset>


		<?


		foreach (glob("*.xls") as $filename)
		{
			@unlink($filename);

		}
		$name=time().".xls";
		$create_new_excel = fopen($name, 'w');
		$report_data=ob_get_contents();
		ob_clean();
		$is_created = fwrite($create_new_excel,$report_data);
		echo $report_data."####".$name;
		exit();
	}
	if($rept_type==4)
	{

		$company_name			= str_replace( "'", "", $cbo_company_name );
		$rept_type				= str_replace( "'", "", $type );
		//echo $repttype;
		$buyer_name				= str_replace( "'", "", $cbo_buyer_name );
		$job_no					= str_replace( "'", "", $txt_job_no );
		$cutting_no				= str_replace( "'", "", $txt_cutting_no );
		$from_date				= str_replace( "'", "", $txt_date_from );
		$to_date				= str_replace( "'", "", $txt_date_to );
		$working_company_id		= str_replace( "'", "", $cbo_working_company_name );
		$location_id			= str_replace( "'", "", $cbo_location_name );
		$txt_job_no_hidden		= str_replace( "'", "", $txt_job_no_hidden);
		$floor_id				= str_replace( "'", "", $cbo_floor_id);
		$shift_id			    = str_replace( "'", "", $cbo_shift_name);


		$sql_cond = "";
		$sql_cond .= ($company_name!="") ? " and a.company_name in($company_name)" : "";
		$sql_cond .= ($buyer_name!=0) ? " and a.buyer_name in($buyer_name)" : "";
		$sql_cond .= ($job_no!="") ? " and b.job_no='$job_no'" : "";
		// $sql_cond .= ($cutting_no!=0) ? " and b.cutting_no in($cutting_no)" : "";
		$sql_cond .= ($from_date!=0) ? " and b.entry_date between '$from_date' and '$to_date'" : "";
		$sql_cond .= ($working_company_id!="") ? " and b.working_company_id in($working_company_id)" : "";
		$sql_cond .= ($location_id!="") ? " and b.location_id in($location_id)" : "";
		$sql_cond .= ($floor_id!="") ? " and b.floor_id in($floor_id)" : "";
		$sql_cond .= ($shift_id!=0) ? " and b.shift_name=$shift_id" : "";

		$sql = "SELECT b.id, a.company_name,a.buyer_name,a.style_ref_no,a.style_description,b.table_no,b.job_no,b.cutting_no,b.working_company_id,b.location_id,b.entry_date,b.shift_name,c.remarks,b.cad_marker_cons,b.floor_id,c.order_cut_no,c.gmt_item_id,c.color_id,c.plies,c.roll_data,d.bundle_no,d.size_qty,d.order_id,e.table_no as table_name,e.id as table_id,c.id as dtls_id from wo_po_details_master a, ppl_cut_lay_mst b, ppl_cut_lay_dtls c,ppl_cut_lay_bundle d,lib_cutting_table e where a.job_no=b.job_no and b.id=c.mst_id and c.id=d.dtls_id and b.id=d.mst_id and b.table_no=e.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $sql_cond  order by e.table_no";
		// echo $sql;

		$res = sql_select($sql);
		$data_array = array();
		$cut_id_array = array();
		$batch_id_array = array();
		$manual_batch_array = array();
		$cut_wise_array = array();
		foreach ($res as $v)
		{
			$cut_id_array[$v['ID']] = $v['ID'];
			$data_array[$v['ENTRY_DATE']][$v['ORDER_ID']][$v['GMT_ITEM_ID']][$v['COLOR_ID']][$v['SHIFT_NAME']][$v['ORDER_CUT_NO']]['id'] = $v['ID'];
			$data_array[$v['ENTRY_DATE']][$v['ORDER_ID']][$v['GMT_ITEM_ID']][$v['COLOR_ID']][$v['SHIFT_NAME']][$v['ORDER_CUT_NO']]['company_name'] = $v['COMPANY_NAME'];
			$data_array[$v['ENTRY_DATE']][$v['ORDER_ID']][$v['GMT_ITEM_ID']][$v['COLOR_ID']][$v['SHIFT_NAME']][$v['ORDER_CUT_NO']]['working_company_id'] = $v['WORKING_COMPANY_ID'];
			$data_array[$v['ENTRY_DATE']][$v['ORDER_ID']][$v['GMT_ITEM_ID']][$v['COLOR_ID']][$v['SHIFT_NAME']][$v['ORDER_CUT_NO']]['job_no'] = $v['JOB_NO'];


			$data_array[$v['ENTRY_DATE']][$v['ORDER_ID']][$v['GMT_ITEM_ID']][$v['COLOR_ID']][$v['SHIFT_NAME']][$v['ORDER_CUT_NO']]['location_id'] = $v['LOCATION_ID'];
			$data_array[$v['ENTRY_DATE']][$v['ORDER_ID']][$v['GMT_ITEM_ID']][$v['COLOR_ID']][$v['SHIFT_NAME']][$v['ORDER_CUT_NO']]['entry_date'] = $v['ENTRY_DATE'];
			$data_array[$v['ENTRY_DATE']][$v['ORDER_ID']][$v['GMT_ITEM_ID']][$v['COLOR_ID']][$v['SHIFT_NAME']][$v['ORDER_CUT_NO']]['buyer_name'] = $v['BUYER_NAME'];
			$data_array[$v['ENTRY_DATE']][$v['ORDER_ID']][$v['GMT_ITEM_ID']][$v['COLOR_ID']][$v['SHIFT_NAME']][$v['ORDER_CUT_NO']]['style_ref_no'] = $v['STYLE_REF_NO'];
			$data_array[$v['ENTRY_DATE']][$v['ORDER_ID']][$v['GMT_ITEM_ID']][$v['COLOR_ID']][$v['SHIFT_NAME']][$v['ORDER_CUT_NO']]['order_id'] = $v['ORDER_ID'];
			// $data_array[$v['ENTRY_DATE']][$v['ORDER_ID']][$v['GMT_ITEM_ID']][$v['COLOR_ID']][$v['SHIFT_NAME']][$v['ORDER_CUT_NO']]['size_qty'] += $v['SIZE_QTY'];
			$data_array[$v['ENTRY_DATE']][$v['ORDER_ID']][$v['GMT_ITEM_ID']][$v['COLOR_ID']][$v['SHIFT_NAME']][$v['ORDER_CUT_NO']]['cad_marker_cons'] = $v[csf('cad_marker_cons')];
			$data_array[$v['ENTRY_DATE']][$v['ORDER_ID']][$v['GMT_ITEM_ID']][$v['COLOR_ID']][$v['SHIFT_NAME']][$v['ORDER_CUT_NO']]['remarks'] = $v['REMARKS'];
			$data_array[$v['ENTRY_DATE']][$v['ORDER_ID']][$v['GMT_ITEM_ID']][$v['COLOR_ID']][$v['SHIFT_NAME']][$v['ORDER_CUT_NO']]['style_description'] = $v['STYLE_DESCRIPTION'];
			$data_array[$v['ENTRY_DATE']][$v['ORDER_ID']][$v['GMT_ITEM_ID']][$v['COLOR_ID']][$v['SHIFT_NAME']][$v['ORDER_CUT_NO']]['order_cut_no'] = $v['ORDER_CUT_NO'];
			$data_array[$v['ENTRY_DATE']][$v['ORDER_ID']][$v['GMT_ITEM_ID']][$v['COLOR_ID']][$v['SHIFT_NAME']][$v['ORDER_CUT_NO']]['gmt_item_id'] = $v['GMT_ITEM_ID'];
			$data_array[$v['ENTRY_DATE']][$v['ORDER_ID']][$v['GMT_ITEM_ID']][$v['COLOR_ID']][$v['SHIFT_NAME']][$v['ORDER_CUT_NO']]['color_id'] = $v['COLOR_ID'];
			$data_array[$v['ENTRY_DATE']][$v['ORDER_ID']][$v['GMT_ITEM_ID']][$v['COLOR_ID']][$v['SHIFT_NAME']][$v['ORDER_CUT_NO']]['remarks'] = $v['REMARKS'];
			$data_array[$v['ENTRY_DATE']][$v['ORDER_ID']][$v['GMT_ITEM_ID']][$v['COLOR_ID']][$v['SHIFT_NAME']][$v['ORDER_CUT_NO']]['dtls_id'] = $v['DTLS_ID'];

			$data_array[$v['ENTRY_DATE']][$v['ORDER_ID']][$v['GMT_ITEM_ID']][$v['COLOR_ID']][$v['SHIFT_NAME']][$v['ORDER_CUT_NO']]['plies'] = $v['PLIES'];
			$data_array[$v['ENTRY_DATE']][$v['ORDER_ID']][$v['GMT_ITEM_ID']][$v['COLOR_ID']][$v['SHIFT_NAME']][$v['ORDER_CUT_NO']]['total_bundle']++;
			$data_array[$v['ENTRY_DATE']][$v['ORDER_ID']][$v['GMT_ITEM_ID']][$v['COLOR_ID']][$v['SHIFT_NAME']][$v['ORDER_CUT_NO']]['size_qty'] += $v['SIZE_QTY'];
			$roll_data=explode("**",$v['ROLL_DATA']);
			foreach($roll_data as $key =>$val)
			{
				$roll_data_ex=explode("=",$val);
				if($roll_data_ex[6]==0)
				{
					if($roll_data_ex[5]!="")
					{
						$manual_batch_array[$v['CUTTING_NO']] .= $roll_data_ex[5].",";
					}
				}
				$batch_id_array[$roll_data_ex[6]] = $roll_data_ex[6];
				$cut_wise_array[$v['CUTTING_NO']] .= $roll_data_ex[6].",";
			}

		}
		//   echo "<pre>";print_r($data_array);
		$cut_id_cond = where_con_using_array($cut_id_array,0,"mst_id");
		$size_ratio_arr = return_library_array( "select dtls_id, sum(SIZE_RATIO) as SIZE_RATIO  from PPL_CUT_LAY_SIZE_DTLS where status_active=1 $cut_id_cond group by dtls_id",'dtls_id','SIZE_RATIO');

		$batch_id_cond = where_con_using_array($batch_id_array,0,"id");
		$batch_arr = return_library_array( "select id, BATCH_NO  from PRO_BATCH_CREATE_MST where status_active=1 $batch_id_cond",'id','BATCH_NO');

		?>

		<fieldset style="width:<? echo $div_width; ?>px;">

			<table width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" border="0"  rules="all">

				<tr style="font-weight:bold;font-size:20px">
					<td align="center" width="1450px" colspan="20">
						<?
						$com_name = str_replace( "'", "", $cbo_working_company_name );
						echo $company_arr[$com_name]."<br/>"."Table Wise Cutting Report";
						?>

					</td>
				</tr>
				<tr style="font-weight:bold;font-size:20px">
					<td align="center" width="1450px" colspan="20">
						Shift-
						<?
						$shi_name = str_replace( "'", "", $cbo_shift_name );
						if($shi_name){
						echo $shift_name[$shi_name];
						}
						else{
							echo "ALL";
						}
						?>
						<tr class="form_caption" style="border:none;">
						<td colspan="15" align="center" style="border:none;font-size:15px; font-weight:bold" >Date: <?=$from_date;?> To <?=$to_date;?></td>
					</tr>


					</td>
				</tr>

			</table>

			<!-- ========================== header part ======================== -->
			<table class="rpt_table" width="1320px" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
					<tr>
						<th width="40">Sl</th>
						<th width="70">Cutting Date</th>
						<th width="100">Buyer</th>
						<th width="100">PO NO</th>
						<th width="100">Style</th>
						<th width="100">Gmts Item</th>
						<th width="100">Color Name</th>
						<th width="100">Shift</th>
						<th width="50">Order Cut No.</th>
						<th width="70">Total Size Ratio</th>
						<th width="70">Plies</th>
						<th width="80">Total Cut Qty Pcs</th>
						<th width="80">Total Cut Qty Dzn</th>
						<th width="100">CAD Marker Cons/Dzn</th>
						<th width="80" title="Total Cut Qty Dzn*CAD Marker Cons/Dzn">Total-Kg/Yds</th>
						<th width="80">Remarks</th>

					</tr>
				</thead>
			</table>
			<!-- ========================== body part ======================== -->
			<div style="width:1340;overflow-y:auto;max-height:300px;" id="scroll_body">
				<table class="rpt_table"  width="1320px" cellpadding="0" cellspacing="0" border="1" rules="all" >
					<tbody>
						<?
						$i=1;

						foreach ($data_array as $entry_date => $entry_data)
						{
							foreach ($entry_data as $order_id => $order_data)
							{
								foreach($order_data as $gmt_item_id=>$gmt_item_data)
								{
									foreach($gmt_item_data as $color_id =>$color_data)
									{
										foreach($color_data as $shift_id=>$shift_data)
										{
											foreach($shift_data as $order_cut_no=>$row)

											{


												if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
													$b_id_arr = array_unique(array_filter(explode(",",$cut_wise_array[$cut_no])));
												$manual_batch_name = implode(array_unique(array_filter(explode(",",$manual_batch_array[$cut_no]))));
												$batch_name = "";
												foreach($b_id_arr as $key=>$val)
												{
													$batch_name.= ($batch_name=="") ? $batch_arr[$val]: ",".$batch_arr[$val];
												}
											?>
											<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
												<td width="40"><?=$i;?></td>
												<td width="70" align="center"><p><?= $row['entry_date'];?></p></td>
												<td width="100"><p><?=$buyer_arr[$row['buyer_name']];?></p></td>
												<td width="100"><p><?echo $order_no_library[$row['order_id']];?></p></td>
												<td width="100"><p><?= $row['style_ref_no'];?></p></td>
												<td width="100"><p><?=$garments_item[$row['gmt_item_id']];?></p></td>
												<td width="100"><p><?= $color_library[$row['color_id']];?></p></td>
												<td width="100" align="center"><p><?=$shift_name[$shift_id];?></p></td>
												<td width="50" align="right"><?=$row['order_cut_no'];?></td>
												<td align="right" width="70" align="right"><?=$size_ratio_arr[$row['dtls_id']];?></td>
												<td align="right" width="70" align="right"><?=$row['plies'];?></td>
												<td align="right" width="80" align="right"><?=$row['size_qty'];?></td>
												<td align="right" width="80" align="right"><?=number_format($row['size_qty']/12,2);?></td>
												<td align="right" width="100"><?=$row['cad_marker_cons'];?></td>
												<td align="right" width="80"><?=number_format($row['size_qty']/12*$row['cad_marker_cons'],2)
												;?></td>
												<td align="right" width="80"><?=$row['remarks'];?></td>
											</tr>
											<?
											$i++;
											$gr_size_ratio +=$size_ratio_arr[$row['dtls_id']];
											$gr_plies += $row['plies'];
											$gr_size_qty +=  $row['size_qty'];
											$gr_cut_dzn +=  $row['size_qty']/12;
											$gr_kg += ($row['size_qty']/12*$row['cad_marker_cons']);
									}

									}

								}

								}

								}
							?>

							<?
						}
						?>
					</tbody>
				</table>
			</div>
			<!-- ========================== footer part ======================== -->

			<table class="rpt_table"  width="1320px" cellpadding="0" cellspacing="0" border="1" rules="all" >
				<tfoot>
					<tr>
						<th width="40"><p></p></th>
						<th width="70"><p></p></th>
						<th width="100"><p></p></th>
						<th width="100"><p></p></th>
						<th width="100"><p></p></th>
						<th width="100"><p></p></th>
						<th width="100"><p></p></th>
						<th width="100"><p></p></th>
						<th width="50"><p></p></th>
						<th width="70"><p></p></th>
						<th width="70" colspan="11"><p>Total</p></th>
						<th width="80"><p><?=number_format($gr_size_qty);?></p></th>
						<th width="80"><p><?=number_format($gr_cut_dzn);?></p></th>
						<th width="100"><p></p></th>
						<th width="80"><p><?=number_format($gr_kg);?></p></th>
						<th width="80"><p></p></th>

					</tr>
				</tfoot>
			</table>

		</fieldset>


		<?


		foreach (glob("*.xls") as $filename)
		{
			@unlink($filename);

		}
		$name=time().".xls";
		$create_new_excel = fopen($name, 'w');
		$report_data=ob_get_contents();
		ob_clean();
		$is_created = fwrite($create_new_excel,$report_data);
		echo $report_data."####".$name;
		exit();

	}
}


if($action=="job_no_popup")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');

	extract($_REQUEST);
	?>
	<script>

		function js_set_value(str)
		{
			$("#hide_job_no").val(str);
			parent.emailwindow.hide();
		}
    </script>
    </head>
    <body>
    <div align="center" >
        <form name="styleRef_form" id="styleRef_form">
		<fieldset style="width:580px;">
            <table width="470" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
					<th> Company Name</th>
                    <th>Buyer</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="170">Please Enter Job No</th>
                    <th>
                        <input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');">
                        <input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
                    </th>
                </thead>
                <tbody>
                	<tr>

					<td>
                        <?
	                       echo create_drop_down( "cbo_working_company_name", 142, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0  order by company_name","id,company_name", 1, "-- Select  --", $selected, "load_drop_down( 'requires/table_wise_cutting_report_controller',this.value, 'load_drop_down_buyer', 'buyer_td' );get_php_form_data(this.value,'print_button_variable_setting','requires/table_wise_cutting_report_controller');" );// get_php_form_data(this.value,'size_wise_repeat_cut_no','requires/cut_and_lay_ratio_wise_entry_controller_urmi' );
	                     ?>
                        </td>

                        <td align="center">
                        	 <?
								echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
							?>
                        </td>
                        <td align="center">
                    	<?
                       		$search_by_arr=array(1=>"Job No",2=>"Style Ref");
							$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";
							echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
                        </td>
                        <td align="center" id="search_by_td">
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
                        </td>
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $sytle_ref_no; ?>'+'**'+'<? echo $cbo_year; ?>', 'create_job_no_search_list_view', 'search_div', 'table_wise_cutting_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
                    </td>
                    </tr>
            	</tbody>
           	</table>
            <div style="margin-top:5px" id="search_div"></div>
		</fieldset>
	</form>
    </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if($action=="create_job_no_search_list_view")
{
	// echo $data;
	$data=explode('**',$data);
	// echo "<pre>";
	// print_r($data);
	// echo "</pre>";
	$company_id=$data[0];
	$year_id=$data[5];
	//$month_id=$data[5];
	//echo $month_id;
	//	var_dump($data);
	// echo $year_id.'**';
	if($data[1]==0)
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
		$buyer_id_cond=" and a.buyer_name=$data[1]";
	}

	$search_by=$data[2];
	$search_string="%".trim($data[3])."%";
	if($search_by==2) $search_field="a.style_ref_no"; else $search_field="a.job_no";
	$year="year(a.insert_date)";
	if($db_type==0) $year_field="YEAR(a.insert_date) as year";
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";

	if($db_type==0)
	{
		if($year_id!=0) $year_cond=" and year(a.insert_date)=$year_id"; else $year_cond="";
	}
	else if($db_type==2)
	{
		$year_field_con=" and to_char(a.insert_date,'YYYY')";
		if($year_id!=0) $year_cond="$year_field_con=$year_id"; else $year_cond="";
	}

	//if($month_id!=0) $month_cond=" and month(insert_date)=$month_id"; else $month_cond="";
	$arr=array (0=>$company_arr,1=>$buyer_arr);
	// $sql= "SELECT b.id, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, $year_field from wo_po_details_master a,wo_po_break_down b where a.id=b.job_id and a.status_active=1 and a.is_deleted=0 and company_name=$company_id and  $search_field  like '$search_string' $buyer_id_cond $year_cond order by a.job_no";

	$sql= "SELECT a.id, a.job_no,  a.company_name, a.buyer_name, a.style_ref_no, $year_field from wo_po_details_master a where  a.status_active=1 and a.is_deleted=0 and company_name=$company_id and  $search_field  like '$search_string' $buyer_id_cond $year_cond order by a.job_no";
    // echo $sql;die();

	echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No", "120,130,80,60","600","240",0, $sql , "js_set_value", "id,job_no", "", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,job_no,year,style_ref_no", "",'','','') ;
	exit();
} // Job Search end


?>