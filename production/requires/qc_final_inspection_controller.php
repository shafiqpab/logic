<?
session_start();
include('../../includes/common.php'); 
$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action == "check_batch_no") 
{
	$data = explode("**", $data);
	$sql = "select id, batch_no, company_id, booking_no from pro_batch_create_mst where batch_no='" . trim($data[1]) . "' and entry_form in(0) and is_deleted=0 and status_active=1 order by id desc";
	$data_array = sql_select($sql);
	if (count($data_array) > 0) {
		echo "1" . "_" . $data_array[0][csf('id')] . "_" . $data_array[0][csf('company_id')]. "_" . $data_array[0][csf('booking_no')];
	} else {
		echo "0_";
	}
	exit();
}

if ($action == "batch_number_popup") 
{
	echo load_html_head_contents("Batch Number Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>
    <script>
        function js_set_value(id) 
        {
            $('#hid_batch_id').val(id);
            parent.emailwindow.hide();
        }
    </script>
    </head>
    <body>
    <div align="center" style="width:800px;">
        <form name="searchbatchnofrm" id="searchbatchnofrm">
            <fieldset style="width:790px;">
                <legend>Enter search words</legend>
                <table cellpadding="0" cellspacing="0" width="770" border="1" rules="all" class="rpt_table">
                    <thead>	                    
	                    <tr>
	                        <th width="150px">Search By</th>
	                        <th width="150px">Search</th>
	                        <th width="220px" style="color:blue">Batch Date Range *</th>
	                        <th>
	                            <input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton"/>
	                            <input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes" value="<? echo $cbo_company_id; ?>">
	                            <input type="hidden" name="hid_batch_id" id="hid_batch_id" class="text_boxes" value="">
	                        </th>
	                    </tr>
                    </thead>
                    <tbody>

                    <tr>
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
                        <td align="center">
                            <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px;"> To <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px;">
                        </td>

                        <td align="center">
                            <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value, 'create_batch_search_list_view', 'search_div', 'qc_final_inspection_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;"/>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
                    </tr>
				</tbody>
                </table>
                <table width="100%" style="margin-top:5px;">
                    <tr>
                        <td colspan="4">
                            <div style="width:100%; margin-left:3px;" id="search_div" align="left"></div>
                        </td>
                    </tr>
                </table>
            </fieldset>
        </form>
    </div>
    </body>
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
	<?
	exit();
}

if ($action == "create_batch_search_list_view")
{
	list($start_date, $end_date, $company_id, $search_by, $search_string) = explode("_", $data);

	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$batch_arr = return_library_array("select id, batch_no from pro_batch_create_mst", "id", "batch_no");
	$machine_arr = return_library_array("select id, machine_no from lib_machine_name", "id", "machine_no");

	if ($search_string == "" && $start_date == "" && $end_date == "") {
		echo "<p style='color:firebrick; text-align: center; font-weight: bold;'>Batch Date Range is required</p>";
		exit;
	}

	$search_string_batch_booking = "%".trim($search_string)."%";
	if ($search_by == 1)
		$search_field = 'a.batch_no';
	else
		$search_field = 'a.booking_no';
	

	if ($db_type == 0) 
	{
		if ($start_date != "" && $end_date != "") $batch_date_con = " and a.batch_date between '" . change_date_format($start_date, "yyyy-mm-dd", "-", 1) . "' and '" . change_date_format($end_date, "yyyy-mm-dd", "-") . "'"; else $batch_date_con = "";
		$group_concat = " group_concat(b.po_id) as po_id";
	}
	else
	{
		if ($start_date != "" && $end_date != "") $batch_date_con = " and a.batch_date between '" . change_date_format($start_date, "mm-dd-yyyy", "-", 1) . "' and '" . change_date_format($end_date, "mm-dd-yyyy", "-", 1) . "'"; else $batch_date_con = "";
		$group_concat = "  listagg(cast(b.po_id AS VARCHAR2(4000)),',') within group (order by b.id) as po_id";
	}

	if ($batch_date_con != '' && $search_string != '')
	{
		$sql ="select a.id, a.batch_no,a.sales_order_no, a.extention_no, a.batch_weight, a.batch_date, a.batch_against, a.booking_no, a.color_id,$group_concat,b.is_sales,a.re_dyeing_from from pro_batch_create_mst a,pro_batch_create_dtls b where a.id=b.mst_id and a.company_id=$company_id and $search_field like '$search_string_batch_booking' and a.status_active=1 and a.is_deleted=0 and a.entry_form=0 group by a.id, a.batch_no,a.sales_order_no, a.extention_no, a.batch_weight, a.batch_date, a.batch_against, a.booking_no, a.color_id,b.is_sales,a.re_dyeing_from order by a.batch_date desc";
	}
	else
	{
		$sql ="select a.id, a.batch_no,a.sales_order_no, a.extention_no, a.batch_weight, a.batch_date, a.batch_against, a.booking_no, a.color_id,$group_concat,b.is_sales,a.re_dyeing_from from pro_batch_create_mst a,pro_batch_create_dtls b where a.id=b.mst_id and a.company_id=$company_id and $search_field like '$search_string_batch_booking' and a.status_active=1 and a.is_deleted=0 and a.entry_form=0 $batch_date_con group by a.id, a.batch_no,a.sales_order_no, a.extention_no, a.batch_weight, a.batch_date, a.batch_against, a.booking_no, a.color_id,b.is_sales,a.re_dyeing_from order by a.batch_date desc";
	}	
	//echo $sql;
	$sql_result = sql_select($sql);	

	$batch_id=array();
	foreach ($sql_result as $row) {
		$ids = implode(",", array_unique(explode(",", $row[csf("po_id")])));
		$po_ids .= $ids . ",";
		$is_sales[] = $row[csf("is_sales")];
		$batch_id[] .= $row[csf("id")];
	}
	$po_ids = rtrim($po_ids, ",");
	if($po_ids!="") $po_ids=$po_ids;else $po_ids=0;

	$sql_po = sql_select("select b.id, b.po_number from wo_po_break_down b where b.status_active=1 and b.is_deleted=0 and b.id in($po_ids)");
	$po_name_arr = array();
	foreach ($sql_po as $p_name) {
		$po_name_arr[$p_name[csf('id')]] = $p_name[csf('po_number')];
	}

	?>
    <div>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="850" class="rpt_table">
            <thead>
	            <th width="50">SL No</th>
				<th width="100">Batch No</th>
				<th width="70">Ext. No</th>
				<th width="150">PO No/FSO No</th>
				<th width="105">Booking No</th>
				<th width="90">Batch Weight</th>
				<th width="90">Batch Date</th>
				<th width="90">Batch Against</th>
				<th>Color</th>
            </thead>
        </table>
        <div style="width:870px; overflow-y:scroll; max-height:230px;" id="batch_list_view" align="center">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="850" class="rpt_table" id="tbl_list_search">
				<?
				$i = 1;
				if (!empty($sql_result)) 
				{
					foreach ($sql_result as $row) 
					{
						if ($row[csf("is_sales")] != 1) 
						{
							$order_id = array_unique(explode(",", $row[csf("po_id")]));
							$order_ids = "";
							foreach ($order_id as $order) {
								$order_ids .= $po_name_arr[$order] . ",";
							}
						} 
						else 
						{
							$order_ids = $row[csf("sales_order_no")];
						}

						if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
						
						?>
                        <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
                            onClick="js_set_value('<? echo $row[csf('id')].'_'.$row[csf('batch_no')].'_'.$row[csf("booking_no")]; ?>')">
                            <td width="50"><? echo $i; ?></td>
							<td width="100"><p><? echo $row[csf("batch_no")]; ?></p></td>
							<td width="70"><p><? echo $row[csf("extention_no")]; ?></p></td>
							<td width="150"><p><? echo trim($order_ids, ","); ?></p></td>
							<td width="105"><p><? echo $row[csf("booking_no")]; ?></p></td>
							<td width="90"><p><? echo $row[csf("batch_weight")]; ?></p></td>
							<td width="90"><p><? echo $row[csf("batch_date")]; ?></p></td>
							<td width="90"><p><? echo $batch_against[$row[csf("batch_against")]]; ?></p></td>
							<td><p><? echo $color_arr[$row[csf("color_id")]]; ?></p></td>
                        </tr>
						<?
						$i++;
					}
				} 
				else 
				{
					echo "<tr><td colspan='9'><p style='color:firebrick; text-align: center; font-weight: bold;'>No Data Found</p></td></tr>";
				}
				?>
            </table>
        </div>
    </div>
	<?
	exit();
}

if ($action == 'populate_data_from_batch') 
{
	list($batch_id, $batch_no, $company_id, $booking_no) = explode("_", $data);
	$double_dyeing=0;

	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$batch_arr = return_library_array("select id, batch_no from pro_batch_create_mst", "id", "batch_no");
	$machine_arr = return_library_array("select id, machine_no from lib_machine_name", "id", "machine_no");

	if ($db_type == 0) 
	{
		$group_concat = " group_concat(d.machine_no_id) as machine_no_id";
		$group_concat2 = " group_concat(d.id) as d.id";
	}
	else
	{
		$group_concat = "  listagg(cast(d.machine_no_id AS VARCHAR2(4000)),',') within group (order by d.machine_no_id) as machine_no_id";
		$group_concat2 = "  listagg(cast(c.id AS VARCHAR2(4000)),',') within group (order by c.id) as po_id";
	}

	$sql = "SELECT a.company_id, a.sales_order_no, a.color_id, a.dyeing_machine, a.is_sales, $group_concat from pro_batch_create_mst a, pro_batch_create_dtls b, pro_grey_prod_entry_dtls d, inv_receive_master e where a.id=b.mst_id and b.prod_id=d.prod_id and d.mst_id=e.id and a.id =$batch_id and e.entry_form in(2,22) and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 group by a.company_id, a.color_id, a.dyeing_machine, a.sales_order_no, a.is_sales";
	$sql_result =sql_select($sql);
	
	$machine_id = $sql_result[0][csf("machine_no_id")];
	$machine_ids = array_unique(explode(",", $machine_id));
	foreach ($machine_ids as $value) 
	{
		$machine_no .= $machine_arr[$value].',';
	}
	$machine_no = rtrim($machine_no, ",");

	
	foreach ($sql_result as $row) 
	{
		echo "document.getElementById('cbo_company_id').value 	= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('txt_color_id').value 	= '".$color_arr[$row[csf("color_id")]]."';\n";
		echo "document.getElementById('txt_dye_mc_id').value 	= '".$machine_arr[$row[csf("dyeing_machine")]]."';\n";
		echo "document.getElementById('txt_knit_mc_id').value 	= '".$machine_no."';\n";

		echo "document.getElementById('hidden_color_id').value 	= '".$row[csf("color_id")]."';\n";		
		echo "document.getElementById('hidden_dye_mc_id').value 	= '".$row[csf("dyeing_machine")]."';\n";	
		echo "document.getElementById('hidden_knit_mc_id').value 	= '".implode(',',$machine_ids)."';\n";		
		//echo "document.getElementById('txt_knit_mc_id').value 	= '".$machine_no."';\n";		
	}

	/*$sql = "select d.po_number, $group_concat2 from pro_batch_create_mst a, ppl_planning_entry_plan_dtls b, wo_booking_dtls c, wo_po_break_down d where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.pre_cost_fabric_cost_dtls_id and c.po_break_down_id=d.id and a.id =$batch_id
		group by d.po_number";*/		
	$sql="select c.po_number, $group_concat2 from pro_batch_create_mst a, wo_booking_dtls b, wo_po_break_down c where  a.booking_no=b.booking_no and b.po_break_down_id=c.id and a.id=$batch_id and a.batch_no='$batch_no' and a.booking_no='$booking_no' group by c.po_number";
	$sql_buyer_po = sql_select($sql);
	foreach ($sql_buyer_po as $value) {
		$po_num .= $value[csf("po_number")].',';
	}
	$po_numbers=chop($po_num,',');

	$po_id=$sql_buyer_po[0][csf("po_id")];
	$po_ids = implode(",",array_unique(explode(",", $po_id)));
	echo "document.getElementById('txt_customer_order_id').value = '".$po_numbers."';\n";
	echo "document.getElementById('hidden_po_id').value = '".$po_ids."';\n";
	exit();
}

if ($action == "sys_number_popup")
{
	echo load_html_head_contents("System Number Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>
    <script>
        function js_set_value(id) 
        {
            $('#hid_sys_id').val(id);
            parent.emailwindow.hide();
        }
    </script>
    </head>
    <body>
    <div align="center" style="width:800px;">
        <form name="searchsysnofrm" id="searchsysnofrm">
            <fieldset style="width:800px;">
                <legend>Enter search words</legend>
                <table cellpadding="0" cellspacing="0" width="800" border="1" rules="all" class="rpt_table">
                    <thead>	                    
	                    <tr>
	                        <th width="150px">Company</th>
	                        <th width="100px">Batch No</th>
	                        <th width="100px">Program No</th>
	                        <th width="100px">System No</th>
	                        <th width="180px">Date Range</th>
	                        <th>
	                            <input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton"/>
	                            <input type="hidden" name="hid_sys_id" id="hid_sys_id" class="text_boxes" value="">
	                        </th>
	                    </tr>
                    </thead>
                    <tbody>

                    <tr>
                        <td align="center">
							<?
							echo create_drop_down("cbo_company_id", 145, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "--Select Company--", $cbo_company_id);
							?>
                        </td>
                        <td align="center">
                            <input type="text" style="width:90px" class="text_boxes" name="txt_search_batch"
							id="txt_search_batch"/>
                        </td>
                        <td align="center">
                            <input type="text" style="width:90px" class="text_boxes" name="txt_search_program"
							id="txt_search_program"/>
                        </td>
                        <td align="center">
                            <input type="text" style="width:90px" class="text_boxes" name="txt_search_system"
							id="txt_search_system"/>
                        </td>
                        <td align="center">
                            <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px;"> To <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px;">
                        </td>
                        <td align="center">
                            <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_company_id').value+'_'+document.getElementById('txt_search_batch').value+'_'+document.getElementById('txt_search_program').value+'_'+document.getElementById('txt_search_system').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'create_system_search_list_view', 'search_div', 'qc_final_inspection_controller', 'setFilterGrid(\'tbl_list_systemsearch\',-1);')" style="width:100px;"/>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
                    </tr>
                </table>
                <table width="100%" style="margin-top:5px;">
                    <tr>
                        <td colspan="6">
                            <div style="width:800px;" id="search_div"></div>
                        </td>
                    </tr>
                </table>
            </fieldset>
        </form>
    </div>
    </body>
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
	<?
	exit();
}

if ($action == "create_system_search_list_view")
{
	list($company_id, $batch_no, $program_no, $system_no, $start_date, $end_date) = explode("_", $data);
	//echo $company_id.'**'.$batch_no;die;
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');

	if ($batch_no != '') $batch_cond = " and a.batch_no like '%$batch_no'"; else $batch_cond = "";
	if ($program_no != '') $program_cond = " and a.program_no like '%$program_no'"; else $program_cond = "";
	if ($system_no != '') $system_cond = " and a.system_id like '$system_no'"; else $system_cond = "";
	

	if ($db_type == 0) 
	{
		if ($start_date != "" && $end_date != "") $sys_date_con = " and a.date_finalized between '" . change_date_format($start_date, "yyyy-mm-dd", "-", 1) . "' and '" . change_date_format($end_date, "yyyy-mm-dd", "-") . "'"; else $sys_date_con = "";
	}
	else
	{
		if ($start_date != "" && $end_date != "") $sys_date_con = " and a.date_finalized between '" . change_date_format($start_date, "mm-dd-yyyy", "-", 1) . "' and '" . change_date_format($end_date, "mm-dd-yyyy", "-", 1) . "'"; else $sys_date_con = "";
	}

	if ($batch_no !='' || $program_no != '' || $system_no != '')
	{
		$sql ="select a.id, a.company_id, a.batch_no,a.batch_id, a.program_no, a.system_id, a.date_finalized from qc_final_inspection_mst a where a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 $batch_cond $program_cond $system_cond order by a.date_finalized desc";
	}
	else
	{
		$sql ="select a.id, a.company_id, a.batch_no,a.batch_id, a.program_no, a.system_id, a.date_finalized from qc_final_inspection_mst a where a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 $sys_date_con order by a.date_finalized desc";
	}	
	//echo $sql;die;
	$sql_result = sql_select($sql);	

	?>   
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table">
        <thead>
	            <th width="50">SL No</th>
				<th width="150">Company Name</th>
				<th width="150">System No</th>
				<th width="150">Batch No</th>
				<th width="150">Program No</th>
				<th width="">Date</th>
        </thead>
    </table>
    <div style="width:820px; overflow-y: scroll; max-height:230px;" id="sys_list_view">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table" id="tbl_list_systemsearch">
			<?
			$i=1;
			foreach ($sql_result as $row) 
			{
				if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";					
				?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
                    onClick="js_set_value('<? echo $row[csf('id')].'_'.$row[csf('program_no')].'_'.$row[csf('system_id')].'_'.$row[csf('batch_id')].'_'.$row[csf('batch_no')]; ?>')">
                    <td width="50" align="center"><? echo $i; ?></td>
					<td width="150" align="center"><p><? echo $company_arr[$row[csf("company_id")]]; ?></p></td>
					<td width="150" align="center"><p><? echo $row[csf("system_id")]; ?></p></td>
					<td width="150" align="center"><p><? echo $row[csf("batch_no")]; ?></p></td>
					<td width="150" align="center"><p><? echo $row[csf("program_no")]; ?></p></td>
					<td width="" align="center"><p><? echo $row[csf("date_finalized")]; ?></p></td>
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

if ($action == 'show_programme_desc_listview')
{

	list($batch_id, $batch_no, $company_id) = explode("_", $data);

	$tube_ref_data=sql_select( "SELECT b.id,b.tube_ref from qc_final_inspection_mst a, qc_final_inspection_dtls b 
	where a.id=b.mst_id and a.batch_id=$batch_id and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
	group by b.id,b.tube_ref");
	
	foreach($tube_ref_data as $val){
		if($val[csf("tube_ref")]){
		$tube_ref_arr[$val[csf("tube_ref")]].="'".$val[csf("tube_ref")]."'";
		}

	}

	$tube_cond="";
	if(count($tube_ref_arr)>0){
		$tube_refArr=implode(",",$tube_ref_arr);
		$tube_cond="and c.reference_no not in ($tube_refArr)";
	}
	
	$body_part_type=return_library_array( "select id,body_part_full_name from lib_body_part", "id", "body_part_full_name"  );
	
	
	$sql_prog = "select a.booking_no, b.program_no, b.item_description, b.body_part_id, b.width_dia_type, c.reference_no, c.reference_qty,b.color_type from pro_batch_create_mst a, pro_batch_create_dtls b left join ppl_reference_creation c on b.program_no=c.program_no where a.id=b.mst_id and b.mst_id=$batch_id and a.company_id=$company_id $tube_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.booking_no, b.program_no, b.item_description, b.body_part_id, b.width_dia_type, c.reference_no, c.reference_qty,b.color_type";
//	echo $sql_prog;
	$sql_prog_rslt = sql_select($sql_prog);	
		
	?>
	<div>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="460" class="rpt_table">
            <thead>
            	<tr>
	            	<th width="460" colspan="6">Programme</th>
	            </tr>
	            <tr>
	            	<th width="50">S/L</th>
					<th width="120">Tube Ref</th>
	            	<th width="80">Prog. No</th>		
					<th width="80">Body Part</th>							
					<th width="80">Color Type</th>				
	            	<th width="50">Qty</th>
	            </tr>				
            </thead>
        </table>
        <div style="width:460px; overflow-y: auto;" id="prog_list_view">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="460" class="rpt_table" id="tbl_prog_search">
            	<?
            	$i=1;
            	foreach ($sql_prog_rslt as $row)
            	{
	            	if ($i % 2 == 0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
	            	?>
	            	<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="programme_val('<? echo $row[csf('program_no')].'_'.$batch_id.'_'.$row[csf("booking_no")].'_'.$batch_no.'_'.$row[csf('reference_no')]; ?>')">
	                    <td width="50" align="center"><? echo $i; ?></td>
						<td width="120" align="center"><p><? echo $row[csf("reference_no")]; ?></p></td>
	                    <td width="80" align="center"><p><? echo $row[csf("program_no")]; ?></p></td>		
						<td width="80" align="center"><p><? echo $body_part_type[$row[csf("body_part_id")]]; ?></p></td>			
						<td width="80" align="center"><p><? echo  $color_type[$row[csf("color_type")]] ?></p></td>							
						<td width="50" align="right"><p>
	                    	<?
	                    	// $prog_no=$row[csf("program_no")];
	                    	// $program_qty = return_field_value("program_qnty", "ppl_planning_entry_plan_dtls", "dtls_id=$prog_no and company_id=$company_id");
	                    	// echo $program_qty; 
							echo $row[csf("reference_qty")];
	                    	?></p>
	                    </td>
	                    
	                </tr>
	                <?
	                $i++;
	            }    
	            ?>    
            </table>
        </div>    	
    </div>        	
	<?
	exit();
}

if ($action == 'show_programme_quantity_listview')
{
	list($batch_id, $program_no, $company_id, $booking_no, $batch_no,$tube_ref) = explode("_", $data);
	$data=explode("_", $data);
		
	$sql_prog_qty = "select a.booking_no, b.program_no, b.item_description, b.body_part_id, b.width_dia_type from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and b.mst_id='$batch_id' and b.program_no='$program_no' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.booking_no, b.program_no, b.item_description, b.body_part_id, b.width_dia_type";
	$sql_prog_qty_rslt = sql_select($sql_prog_qty);

	$program_qnty=return_field_value("program_qnty", "ppl_planning_entry_plan_dtls", "dtls_id='$program_no' and company_id=$company_id");

	$sql_barcode = sql_select("select po_id, barcode_no from pro_batch_create_dtls where mst_id='$batch_id' and program_no='$program_no' and status_active=1 and is_deleted=0");
	foreach ($sql_barcode as $val) {
		$barcode_no .= $val[csf("barcode_no")].',';
		$po_id .= $val[csf("po_id")].',';
	}
	$barcode_nos = chop($barcode_no,',');
	$po_ids = chop($po_id,',');
	$po_ids=implode(",", array_unique(explode(",", $po_ids)));

	$sql_res =  sql_select("select id, barcode_no from pro_qc_result_mst where barcode_no in ($barcode_nos) and entry_form=267 and status_active=1 and is_deleted=0");
	foreach ($sql_res as $val) {
		$barcode_no_qc .= $val[csf("barcode_no")].',';
	}
	$barcode_noss = chop($barcode_no_qc,',');


	$sql_qc_pass=sql_select("select sum(qc_pass_qnty) as qc_pass_qnty from pro_roll_details where po_breakdown_id in($po_ids) and barcode_no in ($barcode_noss) and entry_form=62 and status_active=1 and is_deleted=0");
	$qc_pass_qty=$sql_qc_pass[0][csf("qc_pass_qnty")];

	$diff = $program_qnty-$qc_pass_qty;
	$diff_pct=($diff*100)/$program_qnty;



	?>
	<div style="width: 200px; float: left;">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="200" class="rpt_table">
			<tr>
				<th width="100" align="left">Jobs/Prog<br><p style="margin-top: 4%;">Tube Ref</p></th>
            	<td width="70" align="right" colspan="2">
					<input type="text" name="txt_prog_id" id="txt_prog_id" class="text_boxes" value="<?=$program_no;?>" style="width:100px" disabled="disabled"/>  
					<input type="text" name="txt_tube_ref" id="txt_tube_ref" class="text_boxes" value="<?=$data[4];?>" style="width:100px" disabled="disabled"/>  									
            	</td>
			</tr>
			<tr>
            	<th width="100" align="left">Prog. Req. Qty</th>
            	<td width="70" align="right"><input type="text" id="txt_prog_qty" name="txt_prog_qty" class="text_boxes" value="<? echo $program_qnty; ?>" disabled="disabled" style="width: 60px;"/></td>
            	<td width="30" align="center">
            		<input type="text" class="text_boxes" value="<? echo 'KGS'; ?>" disabled="disabled" style="width: 25px;"/>
            	</td>
            </tr>
            <tr>
            	<th width="100" align="left">Actual Qty</th>
            	<td width="70" align="right">
					<input type="text" id="txt_actual_qty" name="txt_actual_qty" class="text_boxes" value="<? echo $qc_pass_qty; ?>" disabled="disabled" style="width: 60px;"/>
            	</td>
            	<td width="30" align="center">
            		<input type="text" class="text_boxes" value="<? echo 'KGS'; ?>" disabled="disabled" style="width: 25px;"/>            			
            	</td>
            </tr>
            <tr>
            	<th width="100" align="left">Difference</th>
            	<td width="70" align="right">
            		<input type="text" class="text_boxes" value="<? echo $diff; ?>" disabled="disabled" style="width: 60px;"/>
            	</td>
            	<td width="30" align="center">
            		<input type="text" class="text_boxes" value="<? echo $diff_pct.'%'; ?>" disabled="disabled" style="width: 25px;"/>
            	</td>
            </tr>
            <tr>
            	<th width="100" align="left">Req. Qty(BULK)</th>
            	<td width="70" align="right">
            		<input type="text" class="text_boxes" id="txt_req_qty_bulk" name="txt_req_qty_bulk" style="width: 60px;"/>
            	</td>
            	<td width="30" align="center">
            		<input type="text" class="text_boxes" value="<? echo 'MTS'; ?>" disabled="disabled" style="width: 25px;"/>
            	</td>
            </tr>
        </table>
    </div>
    <div style="width: 560px; float: left; padding-left: 20px;">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="560" class="rpt_table">
        	<tr>
            	<td width="300" align="left">
            		<input type="text" id="txt_item_description" name="txt_item_description" class="text_boxes" value='<? echo $sql_prog_qty_rslt[0][csf("item_description")]; ?>' disabled="disabled" style="width: 300px;"/>          		
            	</td>
            	<td width="100" align="left">
            		<input type="text" class="text_boxes" value='<? echo $fabric_typee[$sql_prog_qty_rslt[0][csf("width_dia_type")]]; ?>' disabled="disabled" style="width: 100px;"/>
            		<input type="hidden" name="hidden_body_part_id" id="hidden_body_part_id" value='<? echo $sql_prog_qty_rslt[0][csf("body_part_id")]; ?>'/>        		
            	</td>
            	<td width="160" align="left">
            		<input type="text" class="text_boxes" value='<? echo $body_part[$sql_prog_qty_rslt[0][csf("body_part_id")]]; ?>' disabled="disabled" style="width: 130px;"/>
            		<input type="hidden" name="hidden_width_dia_type" id="hidden_width_dia_type" value='<? echo $sql_prog_qty_rslt[0][csf("width_dia_type")]; ?>'/>
            	</td>			      	
            </tr>           
        </table>
    </div>        
    <?
    exit();
}

if ($action == 'save_update_delete')
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	if($operation==0)
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

			$checkid=sql_select("select id from qc_final_inspection_mst where id=$txt_system_id and status_active=1 and is_deleted=0");
			$id=str_replace("'",'',$txt_system_id);
			
		if(count($checkid)>0){
			
			$id_dtls = return_next_id("id", "qc_final_inspection_dtls", 1);	
			$field_array_dtls= "id, mst_id, handfeel, wash_fastness, shrinkageL, shrinkageL_pct, sewability, water_fastness, shrinkageW, shrinkageW_pct, ph_for, rub_fastness_wet, twist, twist_pct, bale_to_bale, rub_fastness_dry, skewness_warp, skewness_warp_degree, job_to_job, pilling, skewness_weft, skewness_weft_degree, shading_test_same_bale, shade_deE, shade_deL, shade_deC, shade_deH, req_width_bulk, req_width_bulk_inches, req_width_acc1, req_width_acc1_inches, req_width_acc2, req_width_acc2_inches, actual_width_bulk, actual_width_bulk_inches, actual_width_acc1, actual_width_acc1_inches, actual_width_acc2, actual_width_acc2_inches, req_dencity_bulk, req_dencity_bulk_gm2, req_dencity_acc1, req_dencity_acc1_gm2, req_dencity_acc2, req_dencity_acc2_gm2, actual_dencity_bulk, actual_dencity_bulk_gm2, actual_dencity_acc1, actual_dencity_acc1_gm2, actual_dencity_acc2, actual_dencity_acc2_gm2, phenolic_yellowing, ext_rec_l, ext_rec_l_pct, ext_rec_w, ext_rec_w_pct, carcasse_assessed, symmetry, hydrophility, print_durability, width_print, width_print_inches, shading_selvedge, pattern_height, width_selvedge, width_selvedge_inches, shedding_fibers, remarks, flammability_test, orientation_finished_orders, steam_test, bursting_str_del_fabrics, bursting_str_del_fabrics_kpa, tear_strength, orientation_delicate_fabrics, pass_fail,program_no, program_qty, tube_ref, actual_qty, inserted_by, insert_date, status_active, is_deleted";
			$data_array_dtls="(".$id_dtls.",".$txt_system_id.",".$cbo_handfeel.",".$txt_wash_fastness.",".$txt_shrinkageL.",".$txt_shrinkageL_pct.",".$cbo_sewability.",".$txt_water_fastness.",".$txt_shrinkageW.",".$txt_shrinkageW_pct.",".$cbo_ph_for.",".$txt_rub_fastness_wet.",".$txt_twist.",".$txt_twist_pct.",".$cbo_bale_to_bale.",".$txt_rub_fastness_dry.",".$txt_skewness_warp.",".$txt_skewness_warp_degree.",".$cbo_job_to_job.",".$txt_pilling.",".$txt_skewness_weft.",".$txt_skewness_weft_degree.",".$cbo_shading_test_same_bale.",".$txt_shade_deE.",".$txt_shade_deL.",".$txt_shade_deC.",".$txt_shade_deH.",".$txt_required_width_bulk.",".$txt_required_width_bulk_inches.",".$txt_required_width_acc1.",".$txt_required_width_acc1_inches.",".$txt_required_width_acc2.",".$txt_required_width_acc2_inches.",".$txt_actual_width_bulk.",".$txt_actual_width_bulk_inches.",".$txt_actual_width_acc1.",".$txt_actual_width_acc1_inches.",".$txt_actual_width_acc2.",".$txt_actual_width_acc2_inches.",".$txt_required_dencity_bulk.",".$txt_required_dencity_bulk_gm2.",".$txt_required_dencity_acc1.",".$txt_required_dencity_acc1_gm2.",".$txt_required_dencity_acc2.",".$txt_required_dencity_acc2_gm2.",".$txt_actual_dencity_bulk.",".$txt_actual_dencity_bulk_gm2.",".$txt_actual_dencity_acc1.",".$txt_actual_dencity_acc1_gm2.",".$txt_actual_dencity_acc2.",".$txt_actual_dencity_acc2_gm2.",".$txt_phenolic_yellowing.",".$txt_ext_rec_l.",".$txt_ext_rec_l_pct.",".$txt_ext_rec_w.",".$txt_ext_rec_w_pct.",".$txt_carcasse_assessed.",".$txt_symmetry.",".$cbo_hydrophility.",".$cbo_print_durability.",".$txt_width_print.",".$txt_width_print_inches.",".$cbo_shading_selvedge.",".$cbo_pattern_height.",".$txt_width_selvedge.",".$txt_width_selvedge_inches.",".$cbo_shedding_fibers.",".$txt_remarks.",".$cbo_flammability_test.",".$cbo_orientation_finished_orders.",".$txt_steam_test.",".$txt_bursting_strength_delicate_fabrics.",".$txt_bursting_strength_delicate_fabrics_kpa.",".$txt_tear_strength.",".$txt_orientation_delicate_fabrics.",".$cbo_pass_fail.",".$txt_prog_id.",".$txt_prog_qty.",".$txt_tube_ref.",".$txt_actual_qty.",".$user_id.",'".$pc_date_time."','1',0)";
	
			$rID2 = sql_insert("qc_final_inspection_dtls",$field_array_dtls,$data_array_dtls,0);
			//  echo "10**insert into qc_final_inspection_dtls ($field_array_dtls) values $data_array_dtls";die;
			if ($rID2==1) $flag=1; else $flag=0;

		}else{

			if(is_duplicate_field( "id", " qc_final_inspection_mst", "batch_id=$hidden_batch_id and status_active=1 and is_deleted=0" ) == 1)
			{
				echo "11";
				disconnect($con);
				exit();
			}


			$id = return_next_id("id", "qc_final_inspection_mst", 1);
			$field_array= "id, company_id, system_id, batch_no, batch_id, shift_id, color_id, booking_no, date_finalized, po_no, po_id, knit_mc_id, dye_mc_id, finalized_by, req_qty_bulk, inserted_by, insert_date, status_active, is_deleted";

			$data_array="(".$id.",".$cbo_company_id.",".$id.",".$txt_batch_no.",".$hidden_batch_id.",".$txt_shift_name.",".$hidden_color_id.",".$hidden_booking_no.",".$txt_finalized_date.",".$txt_customer_order_id.",".$hidden_po_id.",".$hidden_knit_mc_id.",".$hidden_dye_mc_id.",".$txt_finalized_id.",".$txt_req_qty_bulk.",".$user_id.",'".$pc_date_time."','1',0)";	
			
			$rID = sql_insert("qc_final_inspection_mst",$field_array,$data_array,0);

			// echo "10**insert into qc_final_inspection_mst ($field_array) values $data_array";die;
			
			$id_dtls = return_next_id("id", "qc_final_inspection_dtls", 1);	
			$field_array_dtls= "id, mst_id, handfeel, wash_fastness, shrinkageL, shrinkageL_pct, sewability, water_fastness, shrinkageW, shrinkageW_pct, ph_for, rub_fastness_wet, twist, twist_pct, bale_to_bale, rub_fastness_dry, skewness_warp, skewness_warp_degree, job_to_job, pilling, skewness_weft, skewness_weft_degree, shading_test_same_bale, shade_deE, shade_deL, shade_deC, shade_deH, req_width_bulk, req_width_bulk_inches, req_width_acc1, req_width_acc1_inches, req_width_acc2, req_width_acc2_inches, actual_width_bulk, actual_width_bulk_inches, actual_width_acc1, actual_width_acc1_inches, actual_width_acc2, actual_width_acc2_inches, req_dencity_bulk, req_dencity_bulk_gm2, req_dencity_acc1, req_dencity_acc1_gm2, req_dencity_acc2, req_dencity_acc2_gm2, actual_dencity_bulk, actual_dencity_bulk_gm2, actual_dencity_acc1, actual_dencity_acc1_gm2, actual_dencity_acc2, actual_dencity_acc2_gm2, phenolic_yellowing, ext_rec_l, ext_rec_l_pct, ext_rec_w, ext_rec_w_pct, carcasse_assessed, symmetry, hydrophility, print_durability, width_print, width_print_inches, shading_selvedge, pattern_height, width_selvedge, width_selvedge_inches, shedding_fibers, remarks, flammability_test, orientation_finished_orders, steam_test, bursting_str_del_fabrics, bursting_str_del_fabrics_kpa, tear_strength, orientation_delicate_fabrics, pass_fail,program_no, program_qty, actual_qty, inserted_by, insert_date, status_active, is_deleted";

			$data_array_dtls="(".$id_dtls.",".$id.",".$cbo_handfeel.",".$txt_wash_fastness.",".$txt_shrinkageL.",".$txt_shrinkageL_pct.",".$cbo_sewability.",".$txt_water_fastness.",".$txt_shrinkageW.",".$txt_shrinkageW_pct.",".$cbo_ph_for.",".$txt_rub_fastness_wet.",".$txt_twist.",".$txt_twist_pct.",".$cbo_bale_to_bale.",".$txt_rub_fastness_dry.",".$txt_skewness_warp.",".$txt_skewness_warp_degree.",".$cbo_job_to_job.",".$txt_pilling.",".$txt_skewness_weft.",".$txt_skewness_weft_degree.",".$cbo_shading_test_same_bale.",".$txt_shade_deE.",".$txt_shade_deL.",".$txt_shade_deC.",".$txt_shade_deH.",".$txt_required_width_bulk.",".$txt_required_width_bulk_inches.",".$txt_required_width_acc1.",".$txt_required_width_acc1_inches.",".$txt_required_width_acc2.",".$txt_required_width_acc2_inches.",".$txt_actual_width_bulk.",".$txt_actual_width_bulk_inches.",".$txt_actual_width_acc1.",".$txt_actual_width_acc1_inches.",".$txt_actual_width_acc2.",".$txt_actual_width_acc2_inches.",".$txt_required_dencity_bulk.",".$txt_required_dencity_bulk_gm2.",".$txt_required_dencity_acc1.",".$txt_required_dencity_acc1_gm2.",".$txt_required_dencity_acc2.",".$txt_required_dencity_acc2_gm2.",".$txt_actual_dencity_bulk.",".$txt_actual_dencity_bulk_gm2.",".$txt_actual_dencity_acc1.",".$txt_actual_dencity_acc1_gm2.",".$txt_actual_dencity_acc2.",".$txt_actual_dencity_acc2_gm2.",".$txt_phenolic_yellowing.",".$txt_ext_rec_l.",".$txt_ext_rec_l_pct.",".$txt_ext_rec_w.",".$txt_ext_rec_w_pct.",".$txt_carcasse_assessed.",".$txt_symmetry.",".$cbo_hydrophility.",".$cbo_print_durability.",".$txt_width_print.",".$txt_width_print_inches.",".$cbo_shading_selvedge.",".$cbo_pattern_height.",".$txt_width_selvedge.",".$txt_width_selvedge_inches.",".$cbo_shedding_fibers.",".$txt_remarks.",".$cbo_flammability_test.",".$cbo_orientation_finished_orders.",".$txt_steam_test.",".$txt_bursting_strength_delicate_fabrics.",".$txt_bursting_strength_delicate_fabrics_kpa.",".$txt_tear_strength.",".$txt_orientation_delicate_fabrics.",".$cbo_pass_fail.",".$txt_prog_id.",".$txt_prog_qty.",".$txt_actual_qty.",".$user_id.",'".$pc_date_time."','1',0)";

			$rID2 = sql_insert("qc_final_inspection_dtls",$field_array_dtls,$data_array_dtls,0);
			// echo "10**insert into qc_final_inspection_dtls ($field_array_dtls) values $data_array_dtls";die;
			if ($rID == 1 && $rID2==1) $flag=1; else $flag=0;
		 }

		//echo "10**insert into qc_final_inspection_dtls ($field_array_dtls) values $data_array_dtls";die;
	//  echo "10**";die;

		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");
				echo "0**".$id."**".str_replace("'",'',$hidden_batch_id)."**".str_replace("'",'',$txt_batch_no)."**".str_replace("'",'',$cbo_company_id);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "0**".$id."**".str_replace("'",'',$hidden_batch_id)."**".str_replace("'",'',$txt_batch_no)."**".str_replace("'",'',$cbo_company_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)	
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$update_id = str_replace("'",'',$update_id);		

		$field_array_update= "shift_id*date_finalized*finalized_by*req_qty_bulk*update_by*update_date*status_active*is_deleted";

		$data_array_update="".$txt_shift_name."*".$txt_finalized_date."*".$txt_finalized_id."*".$txt_req_qty_bulk."*".$user_id."*'".$pc_date_time."'*1*0";
		
		$rID=sql_update("qc_final_inspection_mst",$field_array_update,$data_array_update,"id",$update_id,0);


		$field_array_update_dtls= "handfeel*wash_fastness*shrinkageL*shrinkageL_pct*sewability*water_fastness*shrinkageW*shrinkageW_pct*ph_for*rub_fastness_wet*twist*twist_pct*bale_to_bale*rub_fastness_dry*skewness_warp*skewness_warp_degree*job_to_job*pilling*skewness_weft*skewness_weft_degree*shading_test_same_bale*shade_deE*shade_deL*shade_deC*shade_deH*req_width_bulk*req_width_bulk_inches*req_width_acc1*req_width_acc1_inches*req_width_acc2*req_width_acc2_inches*actual_width_bulk*actual_width_bulk_inches*actual_width_acc1*actual_width_acc1_inches*actual_width_acc2*actual_width_acc2_inches*req_dencity_bulk*req_dencity_bulk_gm2*req_dencity_acc1*req_dencity_acc1_gm2*req_dencity_acc2*req_dencity_acc2_gm2*actual_dencity_bulk*actual_dencity_bulk_gm2*actual_dencity_acc1*actual_dencity_acc1_gm2*actual_dencity_acc2*actual_dencity_acc2_gm2*phenolic_yellowing*ext_rec_l*ext_rec_l_pct*ext_rec_w*ext_rec_w_pct*carcasse_assessed*symmetry*hydrophility*print_durability*width_print*width_print_inches*shading_selvedge*pattern_height*width_selvedge*width_selvedge_inches*shedding_fibers*remarks*flammability_test*orientation_finished_orders*steam_test*bursting_str_del_fabrics*bursting_str_del_fabrics_kpa*tear_strength*orientation_delicate_fabrics*pass_fail*update_by*update_date*status_active*is_deleted";

		$data_array_update_dtls="".$cbo_handfeel."*".$txt_wash_fastness."*".$txt_shrinkageL."*".$txt_shrinkageL_pct."*".$cbo_sewability."*".$txt_water_fastness."*".$txt_shrinkageW."*".$txt_shrinkageW_pct."*".$cbo_ph_for."*".$txt_rub_fastness_wet."*".$txt_twist."*".$txt_twist_pct."*".$cbo_bale_to_bale."*".$txt_rub_fastness_dry."*".$txt_skewness_warp."*".$txt_skewness_warp_degree."*".$cbo_job_to_job."*".$txt_pilling."*".$txt_skewness_weft."*".$txt_skewness_weft_degree."*".$cbo_shading_test_same_bale."*".$txt_shade_deE."*".$txt_shade_deL."*".$txt_shade_deC."*".$txt_shade_deH."*".$txt_required_width_bulk."*".$txt_required_width_bulk_inches."*".$txt_required_width_acc1."*".$txt_required_width_acc1_inches."*".$txt_required_width_acc2."*".$txt_required_width_acc2_inches."*".$txt_actual_width_bulk."*".$txt_actual_width_bulk_inches."*".$txt_actual_width_acc1."*".$txt_actual_width_acc1_inches."*".$txt_actual_width_acc2."*".$txt_actual_width_acc2_inches."*".$txt_required_dencity_bulk."*".$txt_required_dencity_bulk_gm2."*".$txt_required_dencity_acc1."*".$txt_required_dencity_acc1_gm2."*".$txt_required_dencity_acc2."*".$txt_required_dencity_acc2_gm2."*".$txt_actual_dencity_bulk."*".$txt_actual_dencity_bulk_gm2."*".$txt_actual_dencity_acc1."*".$txt_actual_dencity_acc1_gm2."*".$txt_actual_dencity_acc2."*".$txt_actual_dencity_acc2_gm2."*".$txt_phenolic_yellowing."*".$txt_ext_rec_l."*".$txt_ext_rec_l_pct."*".$txt_ext_rec_w."*".$txt_ext_rec_w_pct."*".$txt_carcasse_assessed."*".$txt_symmetry."*".$cbo_hydrophility."*".$cbo_print_durability."*".$txt_width_print."*".$txt_width_print_inches."*".$cbo_shading_selvedge."*".$cbo_pattern_height."*".$txt_width_selvedge."*".$txt_width_selvedge_inches."*".$cbo_shedding_fibers."*".$txt_remarks."*".$cbo_flammability_test."*".$cbo_orientation_finished_orders."*".$txt_steam_test."*".$txt_bursting_strength_delicate_fabrics."*".$txt_bursting_strength_delicate_fabrics_kpa."*".$txt_tear_strength."*".$txt_orientation_delicate_fabrics."*".$cbo_pass_fail."*".$user_id."*'".$pc_date_time."'*1*0";

		$rID2 = sql_update("qc_final_inspection_dtls",$field_array_update_dtls,$data_array_update_dtls,"mst_id",$update_id,0);

		if ($rID == 1 && $rID2==1) $flag=1; else $flag=0;

		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");
				echo "1**".str_replace("'",'',$update_id)."**".str_replace("'",'',$hidden_batch_id)."**".str_replace("'",'',$txt_batch_no)."**".str_replace("'",'',$cbo_company_id);;
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "1**".str_replace("'",'',$update_id)."**".str_replace("'",'',$hidden_batch_id)."**".str_replace("'",'',$txt_batch_no)."**".str_replace("'",'',$cbo_company_id);;
			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)
	{
		$con = connect();
		if($db_type==0)
		{ 
			mysql_query("BEGIN");
		}

		$field_array="update_by*update_date*status_active*is_deleted";
		$data_array="'".$user_id."'*'".$pc_date_time."'*0*1";

		$rID=sql_delete("qc_final_inspection_mst",$field_array,$data_array,"id",$update_id,0);
		$dtlsrID=sql_delete("qc_final_inspection_dtls",$field_array,$data_array,"mst_id",$update_id,0);
		
		if($db_type==2)
		{	
			if($rID && $dtlsrID)
			{
				oci_commit($con);
				echo "2**".str_replace("'",'',$update_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$update_id);
			}
		}
		if($db_type==0)
		{
			if($rID && $dtlsrID)
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'",'',$update_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$update_id);
			}
		}
		disconnect($con);
		die;
	}	
}

if ($action == "create_qc_final_inspection_list_view_old")
{
	$sql = "select a.id as mst_id,b.id as dtls_id, a.batch_no, b.program_no, a.date_finalized, a.finalized_by,b.tube_ref from qc_final_inspection_mst a,qc_final_inspection_dtls b  where a.id=$data and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0";
	$sql_result = sql_select($sql);
	?>
	<div style="width:930px;">
        <table cellspacing="0" width="930"  border="1" rules="all" class="rpt_table" align="left">
            <thead>
                <tr>
                    <th width="60">SL</th>
                    <th width="200">Batch No</th>
                    <th width="200">Program No</th>
					<th width="120">Tube Ref</th>
                    <th width="150">Date Finalized</th>
                    <th width="100">Finalized By</th>
                </tr>
            </thead>
        </table>     
        <div style="width:930px; max-height:400px; overflow-y:auto;">
            <table cellspacing="0" width="930"  border="1" rules="all" align="left" class="rpt_table" id="list_view_container">
                <tbody>
                <?
                $i=1;
	            foreach($sql_result as $row)
				{
					if ($i%2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
					?>
                    <tr id="tr_<? echo $i; ?>" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="set_data('<? echo $row[csf('mst_id')]."_".$row[csf('dtls_id')]; ?>')">
                        <td width="60" align="center"><? echo $i; ?></td>
                        <td width="200" align="center"><? echo $row[csf('batch_no')]; ?></td>
                        <td width="200" align="center"><? echo $row[csf('program_no')]; ?></td>
						<td width="120" align="center"><? echo $row[csf('tube_ref')]; ?></td>
                        <td width="150" align="center"><? echo change_date_format($row[csf("date_finalized")], "dd-mm-yyyy"); ?></td>
                        <td width="100" align="center"><? echo $row[csf('finalized_by')]; ?></td>
					</tr>
					<?
					$i++;
				}	
				?>	
                <tbody>
            </table>
        </div>
    </div> 
    <?
    exit();
}
if ($action == "create_qc_final_inspection_list_view")
{
	$sql = "select a.id as mst_id,b.id as dtls_id, a.batch_no, b.program_no, a.date_finalized, a.finalized_by,b.tube_ref,c.reference_qty,a.color_id from qc_final_inspection_mst a,qc_final_inspection_dtls b left join ppl_reference_creation c on b.program_no=c.program_no where a.id=$data and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0";
	$sql_result = sql_select($sql);
	?>
	<div style="width:930px;">
        <table cellspacing="0" width="930"  border="1" rules="all" class="rpt_table" align="left">
            <thead>
                <tr>
                    <th width="60">SL</th>
					<th width="120">Tube Ref</th>
                    <th width="200">Body Part</th>
                    <th width="200">Color</th>
					
                    <th width="150">Color Type</th>
                    <th width="100">Tube/Ref.Wgt. (kg)</th>
                </tr>
            </thead>
        </table>     
        <div style="width:930px; max-height:400px; overflow-y:auto;">
            <table cellspacing="0" width="930"  border="1" rules="all" align="left" class="rpt_table" id="list_view_container">
                <tbody>
                <?
                $i=1;
	            foreach($sql_result as $row)
				{
					if ($i%2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
					?>
                    <tr id="tr_<? echo $i; ?>" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="set_data('<? echo $row[csf('mst_id')]."_".$row[csf('dtls_id')]; ?>')">
                        <td width="60" align="center"><? echo $i; ?></td>
						<td width="120" align="center"><? echo $row[csf('tube_ref')]; ?></td>
                        <td width="200" align="center"><? echo $row[csf('batch_no')]; ?></td>
                        <td width="200" align="center"><? echo $row[csf('color_id')]; ?></td>						
                        <td width="150" align="center"><? echo change_date_format($row[csf("date_finalized")], "dd-mm-yyyy"); ?></td>
                        <td width="100" align="center"><? echo $row[csf('reference_qty')]; ?></td>
					</tr>
					<?
					$i++;
				}	
				?>	
                <tbody>
            </table>
        </div>
    </div> 
    <?
    exit();
}
if ($action == 'show_qcfinal_prog_quantity_listview')
{

	$sql_prog_qty = "select id, program_no, program_qty, item_description, actual_qty, req_qty_bulk, body_part_id, width_dia_type from qc_final_inspection_mst where id='$data' and status_active=1 and is_deleted=0";
	$sql_prog_qty_rslt = sql_select($sql_prog_qty);
	
	$program_qnty=$sql_prog_qty_rslt[0][csf("program_qty")];
	$actual_qnty=$sql_prog_qty_rslt[0][csf("actual_qty")];

	$diff = $program_qnty-$actual_qnty;
	$diff_pct=($diff*100)/$program_qnty;
	?>
	<div style="width: 200px; float: left;">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="200" class="rpt_table">
        	<tr>
            	<th width="100" align="left">Prog. Req. Qty</th>
            	<td width="70" align="right">
            		<input type="text" id="txt_prog_qty" name="txt_prog_qty" class="text_boxes" value="<? echo $program_qnty; ?>" disabled="disabled" style="width: 60px;"/>
					
            	</td>
            	<td width="30" align="center">
            		<input type="text" class="text_boxes" value="<? echo 'KGS'; ?>" disabled="disabled" style="width: 25px;"/>
            	</td>
            </tr>
            <tr>
            	<th width="100" align="left">Actual Qty</th>
            	<td width="70" align="right">
					<input type="text" id="txt_actual_qty" name="txt_actual_qty" class="text_boxes" value="<? echo $actual_qnty; ?>" disabled="disabled" style="width: 60px;"/>
            	</td>
            	<td width="30" align="center">
            		<input type="text" class="text_boxes" value="<? echo 'KGS'; ?>" disabled="disabled" style="width: 25px;"/>            			
            	</td>
            </tr>
            <tr>
            	<th width="100" align="left">Difference</th>
            	<td width="70" align="right">
            		<input type="text" class="text_boxes" value="<? echo $diff; ?>" disabled="disabled" style="width: 60px;"/>
            	</td>
            	<td width="30" align="center">
            		<input type="text" class="text_boxes" value="<? echo $diff_pct.'%'; ?>" disabled="disabled" style="width: 25px;"/>
            	</td>
            </tr>
            <tr>
            	<th width="100" align="left">Req. Qty(BULK)</th>
            	<td width="70" align="right">
            		<input type="text" class="text_boxes" id="txt_req_qty_bulk" name="txt_req_qty_bulk" value="<? echo $sql_prog_qty_rslt[0][csf("req_qty_bulk")]; ?>" style="width: 60px;"/>
            	</td>
            	<td width="30" align="center">
            		<input type="text" class="text_boxes" value="<? echo 'MTS'; ?>" disabled="disabled" style="width: 25px;"/>
            	</td>
            </tr>
        </table>
    </div>
    <div style="width: 560px; float: left; padding-left: 20px;">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="560" class="rpt_table">
        	<tr>
            	<td width="300">
            		<input type="text" id="txt_item_description" name="txt_item_description" class="text_boxes" value='<? echo $sql_prog_qty_rslt[0][csf("item_description")]; ?>' disabled="disabled" style="width: 300px;"/>   		
            	</td>
            	<td width="100">
            		<input type="text" class="text_boxes" value='<? echo $fabric_typee[$sql_prog_qty_rslt[0][csf("width_dia_type")]]; ?>' disabled="disabled" style="width: 100px;"/>
            		<input type="hidden" name="hidden_body_part_id" id="hidden_body_part_id" value='<? echo $sql_prog_qty_rslt[0][csf("body_part_id")]; ?>'/>
            	</td>
            	<td width="160">
            		<input type="text" class="text_boxes" value='<? echo $body_part[$sql_prog_qty_rslt[0][csf("body_part_id")]]; ?>' disabled="disabled" style="width: 130px;"/>
            		<input type="hidden" name="hidden_width_dia_type" id="hidden_width_dia_type" value='<? echo $sql_prog_qty_rslt[0][csf("width_dia_type")]; ?>'/>
            	</td>
            </tr>           
        </table>
    </div>        
    <?
    exit();
}

if ($action == "populate_php_data_full_form")
{
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$machine_arr = return_library_array("select id, machine_no from lib_machine_name", "id", "machine_no");
	$user_name = return_field_value("user_name", "user_passwd", "id ='$user_id' and valid=1");	

	$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');

	$sql = "SELECT a.id, a.company_id, a.batch_no,a.batch_id, a.shift_id, a.color_id, a.program_no, a.date_finalized, a.po_no, a.knit_mc_id, a.dye_mc_id, a.finalized_by, a.req_qty_bulk, a.item_description, a.program_qty, a.actual_qty, a.body_part_id, a.width_dia_type, b.handfeel, b.wash_fastness, b.shrinkageL, b.shrinkageL_pct, b.sewability, b.water_fastness, b.shrinkageW, b.shrinkageW_pct, b.ph_for, b.rub_fastness_wet, b.twist, b.twist_pct, b.bale_to_bale, b.rub_fastness_dry, b.skewness_warp, b.skewness_warp_degree, b.job_to_job, b.pilling, b.skewness_weft, b.skewness_weft_degree, b.shading_test_same_bale, b.shade_deE, b.shade_deL, b.shade_deC, b.shade_deH, b.req_width_bulk, b.req_width_bulk_inches, b.req_width_acc1, b.req_width_acc1_inches, b.req_width_acc2, b.req_width_acc2_inches, b.actual_width_bulk, b.actual_width_bulk_inches, b.actual_width_acc1, b.actual_width_acc1_inches, b.actual_width_acc2, b.actual_width_acc2_inches, b.req_dencity_bulk, b.req_dencity_bulk_gm2, b.req_dencity_acc1, b.req_dencity_acc1_gm2, b.req_dencity_acc2, b.req_dencity_acc2_gm2, b.actual_dencity_bulk, b.actual_dencity_bulk_gm2, b.actual_dencity_acc1, b.actual_dencity_acc1_gm2, b.actual_dencity_acc2, b.actual_dencity_acc2_gm2, b.phenolic_yellowing, b.ext_rec_l, b.ext_rec_l_pct, b.ext_rec_w, b.ext_rec_w_pct, b.carcasse_assessed, b.symmetry, b.hydrophility, b.print_durability, b.width_print, b.width_print_inches, b.shading_selvedge, b.pattern_height, b.width_selvedge, b.width_selvedge_inches, b.shedding_fibers, b.remarks, b.flammability_test, b.orientation_finished_orders, b.steam_test, b.bursting_str_del_fabrics, b.bursting_str_del_fabrics_kpa, b.tear_strength, b.orientation_delicate_fabrics, b.pass_fail from qc_final_inspection_mst a, qc_final_inspection_dtls b where a.id=b.mst_id and b.id=$data and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";

	$sql_res = sql_select($sql);
	$machine_id = $sql_res[0][csf("knit_mc_id")];
	$machine_ids = explode(",", $machine_id);
	foreach ($machine_ids as $value) 
	{
		$machine_no .= $machine_arr[$value].',';
	}
	$machine_no = rtrim($machine_no, ",");	

	foreach ($sql_res as $row)
	{
		echo "document.getElementById('cbo_company_id').value 	= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('txt_batch_no').value 	= '".$row[csf("batch_no")]."';\n";
		echo "document.getElementById('hidden_batch_id').value 	= '".$row[csf("batch_id")]."';\n";
		echo "document.getElementById('txt_shift_name').value 	= '".$row[csf("shift_id")]."';\n";
		echo "document.getElementById('txt_color_id').value 	= '".$color_arr[$row[csf("color_id")]]."';\n";
		echo "document.getElementById('txt_dye_mc_id').value 	= '".$machine_arr[$row[csf("dye_mc_id")]]."';\n";
		echo "document.getElementById('txt_knit_mc_id').value 	= '".$machine_no."';\n";
		echo "document.getElementById('txt_prog_id').value 	= '".$row[csf("program_no")]."';\n";
		echo "document.getElementById('txt_finalized_date').value = '".change_date_format($row[csf("date_finalized")], "dd-mm-yyyy")."';\n";
		echo "document.getElementById('txt_customer_order_id').value = '".$row[csf("po_no")]."';\n";
		echo "document.getElementById('txt_data_input_id').value = '".$user_name."';\n";	
		echo "document.getElementById('txt_finalized_id').value = '".$row[csf("finalized_by")]."';\n";	
		echo "document.getElementById('txt_req_qty_bulk').value = '".$row[csf("req_qty_bulk")]."';\n";	

		/*echo "document.getElementById('txt_prog_qty').value = '".$row[csf("program_qty")]."';\n";
		echo "document.getElementById('txt_actual_qty').value = '".$row[csf("actual_qty")]."';\n";
		echo "document.getElementById('txt_item_description').value = '".$row[csf("item_description")]."';\n";
		echo "document.getElementById('hidden_body_part_id').value = '".$body_part[$row[csf("body_part_id")]]."';\n";
		echo "document.getElementById('hidden_width_dia_type').value = '".$row[csf("width_dia_type")]."';\n";*/

		echo "document.getElementById('cbo_handfeel').value = '".$row[csf("handfeel")]."';\n";
		echo "document.getElementById('txt_wash_fastness').value = '".$row[csf("wash_fastness")]."';\n";
		echo "document.getElementById('txt_shrinkageL').value = '".$row[csf("shrinkageL")]."';\n";
		echo "document.getElementById('txt_shrinkageL_pct').value = '".$row[csf("shrinkageL_pct")]."';\n";
		echo "document.getElementById('cbo_sewability').value = '".$row[csf("sewability")]."';\n";
		echo "document.getElementById('txt_water_fastness').value = '".$row[csf("water_fastness")]."';\n";
		echo "document.getElementById('txt_shrinkageW').value = '".$row[csf("shrinkageW")]."';\n";
		echo "document.getElementById('txt_shrinkageW_pct').value = '".$row[csf("shrinkageW_pct")]."';\n";
		echo "document.getElementById('cbo_ph_for').value = '".$row[csf("ph_for")]."';\n";
		echo "document.getElementById('txt_rub_fastness_wet').value = '".$row[csf("rub_fastness_wet")]."';\n";
		echo "document.getElementById('txt_twist').value = '".$row[csf("twist")]."';\n";
		echo "document.getElementById('txt_twist_pct').value = '".$row[csf("twist_pct")]."';\n";
		echo "document.getElementById('cbo_bale_to_bale').value = '".$row[csf("bale_to_bale")]."';\n";
		echo "document.getElementById('txt_rub_fastness_dry').value = '".$row[csf("rub_fastness_dry")]."';\n";
		echo "document.getElementById('txt_skewness_warp').value = '".$row[csf("skewness_warp")]."';\n";
		echo "document.getElementById('txt_skewness_warp_degree').value = '".$row[csf("skewness_warp_degree")]."';\n";
		echo "document.getElementById('cbo_job_to_job').value = '".$row[csf("job_to_job")]."';\n";
		echo "document.getElementById('txt_pilling').value = '".$row[csf("pilling")]."';\n";
		echo "document.getElementById('txt_skewness_weft').value = '".$row[csf("skewness_weft")]."';\n";
		echo "document.getElementById('txt_skewness_weft_degree').value = '".$row[csf("skewness_weft_degree")]."';\n";
		echo "document.getElementById('cbo_shading_test_same_bale').value = '".$row[csf("shading_test_same_bale")]."';\n";
		echo "document.getElementById('txt_shade_deE').value = '".$row[csf("shade_deE")]."';\n";
		echo "document.getElementById('txt_shade_deL').value = '".$row[csf("shade_deL")]."';\n";
		echo "document.getElementById('txt_shade_deC').value = '".$row[csf("shade_deC")]."';\n";
		echo "document.getElementById('txt_shade_deH').value = '".$row[csf("shade_deH")]."';\n";
		echo "document.getElementById('txt_required_width_bulk').value = '".$row[csf("req_width_bulk")]."';\n";
		echo "document.getElementById('txt_required_width_bulk_inches').value = '".$row[csf("req_width_bulk_inches")]."';\n";
		echo "document.getElementById('txt_required_width_acc1').value = '".$row[csf("req_width_acc1")]."';\n";
		echo "document.getElementById('txt_required_width_acc1_inches').value = '".$row[csf("req_width_acc1_inches")]."';\n";
		echo "document.getElementById('txt_required_width_acc2').value = '".$row[csf("req_width_acc2")]."';\n";
		echo "document.getElementById('txt_required_width_acc2_inches').value = '".$row[csf("req_width_acc2_inches")]."';\n";
		echo "document.getElementById('txt_actual_width_bulk').value = '".$row[csf("actual_width_bulk")]."';\n";
		echo "document.getElementById('txt_actual_width_bulk_inches').value = '".$row[csf("actual_width_bulk_inches")]."';\n";
		echo "document.getElementById('txt_actual_width_acc1').value = '".$row[csf("actual_width_acc1")]."';\n";
		echo "document.getElementById('txt_actual_width_acc1_inches').value = '".$row[csf("actual_width_acc1_inches")]."';\n";
		echo "document.getElementById('txt_actual_width_acc2').value = '".$row[csf("actual_width_acc2")]."';\n";
		echo "document.getElementById('txt_actual_width_acc2_inches').value = '".$row[csf("actual_width_acc2_inches")]."';\n";
		echo "document.getElementById('txt_required_dencity_bulk').value = '".$row[csf("req_dencity_bulk")]."';\n";
		echo "document.getElementById('txt_required_dencity_bulk_gm2').value = '".$row[csf("req_dencity_bulk_gm2")]."';\n";
		echo "document.getElementById('txt_required_dencity_acc1_gm2').value = '".$row[csf("req_dencity_acc1")]."';\n";
		echo "document.getElementById('txt_required_dencity_acc1_gm2').value = '".$row[csf("req_dencity_acc1_gm2")]."';\n";
		echo "document.getElementById('txt_required_dencity_acc2').value = '".$row[csf("req_dencity_acc2")]."';\n";
		echo "document.getElementById('txt_required_dencity_acc2_gm2').value = '".$row[csf("req_dencity_acc2_gm2")]."';\n";
		echo "document.getElementById('txt_actual_dencity_bulk').value = '".$row[csf("actual_dencity_bulk")]."';\n";
		echo "document.getElementById('txt_actual_dencity_bulk_gm2').value = '".$row[csf("actual_dencity_bulk_gm2")]."';\n";
		echo "document.getElementById('txt_actual_dencity_acc1').value = '".$row[csf("actual_dencity_acc1")]."';\n";
		echo "document.getElementById('txt_actual_dencity_acc1_gm2').value 	= '".$row[csf("actual_dencity_acc1_gm2")]."';\n";
		echo "document.getElementById('txt_actual_dencity_acc2').value 	= '".$row[csf("actual_dencity_acc2")]."';\n";
		echo "document.getElementById('txt_actual_dencity_acc2_gm2').value 	= '".$row[csf("actual_dencity_acc2_gm2")]."';\n";
		echo "document.getElementById('txt_phenolic_yellowing').value = '".$row[csf("phenolic_yellowing")]."';\n";
		echo "document.getElementById('txt_ext_rec_l').value = '".$row[csf("ext_rec_l")]."';\n";
		echo "document.getElementById('txt_ext_rec_l_pct').value = '".$row[csf("ext_rec_l_pct")]."';\n";
		echo "document.getElementById('txt_ext_rec_w').value = '".$row[csf("ext_rec_w")]."';\n";
		echo "document.getElementById('txt_ext_rec_w_pct').value = '".$row[csf("ext_rec_w_pct")]."';\n";
		echo "document.getElementById('txt_carcasse_assessed').value = '".$row[csf("carcasse_assessed")]."';\n";
		echo "document.getElementById('txt_symmetry').value = '".$row[csf("symmetry")]."';\n";
		echo "document.getElementById('cbo_hydrophility').value = '".$row[csf("hydrophility")]."';\n";
		echo "document.getElementById('cbo_print_durability').value = '".$row[csf("print_durability")]."';\n";
		echo "document.getElementById('txt_width_print').value = '".$row[csf("width_print")]."';\n";		
		
		echo "document.getElementById('txt_width_print_inches').value = '".$row[csf("width_print_inches")]."';\n";
		echo "document.getElementById('cbo_shading_selvedge').value = '".$row[csf("shading_selvedge")]."';\n";
		echo "document.getElementById('cbo_pattern_height').value = '".$row[csf("pattern_height")]."';\n";
		echo "document.getElementById('txt_width_selvedge').value = '".$row[csf("width_selvedge")]."';\n";
		echo "document.getElementById('txt_width_selvedge_inches').value = '".$row[csf("width_selvedge_inches")]."';\n";
		echo "document.getElementById('cbo_shedding_fibers').value = '".$row[csf("shedding_fibers")]."';\n";
		echo "document.getElementById('txt_remarks').value = '".$row[csf("remarks")]."';\n";
		echo "document.getElementById('cbo_flammability_test').value = '".$row[csf("flammability_test")]."';\n";
		echo "document.getElementById('cbo_orientation_finished_orders').value = '".$row[csf("orientation_finished_orders")]."';\n";
		echo "document.getElementById('txt_steam_test').value = '".$row[csf("steam_test")]."';\n";
		echo "document.getElementById('txt_bursting_strength_delicate_fabrics').value = '".$row[csf("bursting_str_del_fabrics")]."';\n";
		echo "document.getElementById('txt_bursting_strength_delicate_fabrics_kpa').value = '".$row[csf("bursting_str_del_fabrics_kpa")]."';\n";
		echo "document.getElementById('txt_tear_strength').value = '".$row[csf("tear_strength")]."';\n";
		echo "document.getElementById('txt_orientation_delicate_fabrics').value = '".$row[csf("orientation_delicate_fabrics")]."';\n";
		echo "document.getElementById('cbo_pass_fail').value = '".$row[csf("pass_fail")]."';\n";
		echo "document.getElementById('txt_system_id').value = '".$row[csf("id")]."';\n";
		echo "document.getElementById('update_id').value = '".$row[csf("id")]."';\n";
    	echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_qc_final_inspection_entry',1,1);\n";
	}
}

if ($action == "qc_final_inspection_print")
{
	extract($_REQUEST);
	list ($company_id, $mst_id) = explode('**', $data);
	$pass_fail_arr = array(1=>'Pass',2=>'Fail');
	$company_arr = return_library_array( "select id, company_name from lib_company", "id", "company_name" );
	$buyer_arr = return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name" );
	$color_arr = return_library_array( "select id, color_name from lib_color",'id','color_name');
	$country_arr = return_library_array( "select id, country_name from lib_country", "id", "country_name" );
	$image_location = return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$company_id'","image_location");

	$sql_qc="SELECT a.id, a.company_id, a.batch_no, a.batch_id, a.color_id, b.program_no, c.reference_no, a.date_finalized, b.program_qty, a.item_description, b.actual_qty, a.body_part_id, a.width_dia_type, a.po_no, a.booking_no, b.shrinkageL, b.shrinkageW, b.twist, b.req_width_bulk, b.req_width_bulk_inches, b.actual_width_bulk, b.actual_width_bulk_inches, b.width_print, b.width_print_inches, b.req_dencity_bulk, b.req_dencity_bulk_gm2, b.actual_dencity_bulk, b.actual_dencity_bulk_gm2, b.req_width_acc2, b.req_width_acc2_inches, b.actual_width_acc2, b.actual_width_acc2_inches, b.req_dencity_acc2, b.req_dencity_acc2_gm2, b.actual_dencity_acc2, b.actual_dencity_acc2_gm2, b.pass_fail, b.remarks from qc_final_inspection_mst a, qc_final_inspection_dtls b join ppl_reference_creation c on b.program_no=c.program_no where a.id=b.mst_id and a.company_id=$company_id and a.id=$mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$sql_qc_res = sql_select($sql_qc);
	$batch_id = $sql_qc_res[0][csf("batch_id")];
	$batch_no = $sql_qc_res[0][csf("batch_no")];
	$booking_no = $sql_qc_res[0][csf("booking_no")];
	$program_no = $sql_qc_res[0][csf("program_no")];
	$tube_ref_no = $sql_qc_res[0][csf("reference_no")];
	$program_qty =  $sql_qc_res[0][csf("program_qty")];
	$actual_qty =  $sql_qc_res[0][csf("actual_qty")];
	$buyer_unit = return_field_value("buyer_id","pro_batch_create_mst a, fabric_sales_order_mst f","a.sales_order_no=f.job_no and a.status_active=1 and a.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and a.id=$batch_id");
	$sql_reason=return_field_value("remarks","pro_fab_subprocess","batch_id=$batch_id and batch_no='$batch_no' and company_id=$company_id and entry_form=35 and load_unload_id=2 and status_active=1 and is_deleted=0");

	$sql="select c.buyer_name, c.ship_mode from pro_batch_create_mst a, wo_booking_dtls b, wo_po_details_master c where  a.booking_no=b.booking_no and b.job_no=c.job_no and a.id=$batch_id and a.batch_no='$batch_no' and a.booking_no='$booking_no' group by c.buyer_name, c.ship_mode";
	$sql_res = sql_select($sql);
	$buyer_name= $buyer_arr[$sql_res[0][csf("buyer_name")]];
	$ship_mode= $shipment_mode[$sql_res[0][csf("ship_mode")]];


	$sql_barcode = sql_select("select po_id, barcode_no from pro_batch_create_dtls where mst_id='$batch_id' and program_no='$program_no' and status_active=1 and is_deleted=0");
	foreach ($sql_barcode as $val) {
		$barcode_no .= $val[csf("barcode_no")].',';
		$po_id .= $val[csf("po_id")].',';
	}
	$barcode_nos = chop($barcode_no,',');
	$po_ids = chop($po_id,',');
	$po_ids=implode(",", array_unique(explode(",", $po_ids)));

	$sql_res =  sql_select("select id, barcode_no, comments from pro_qc_result_mst where barcode_no in ($barcode_nos) and entry_form=267 and status_active=1 and is_deleted=0");
	$comments_arr=array();
	foreach ($sql_res as $val) {
		$barcode_no_qc .= $val[csf("barcode_no")].',';
		$comments_arr[$val[csf("barcode_no")]]  = $val[csf("comments")];
	}
	$barcode_noss = chop($barcode_no_qc,',');

	//echo "select id, qc_pass_qnty from pro_roll_details where po_breakdown_id in($po_ids) and barcode_no in ($barcode_noss) and entry_form=62 and status_active=1 and is_deleted=0";


	$sql_qc_pass=sql_select("select id, qc_pass_qnty, barcode_no, roll_no from pro_roll_details where po_breakdown_id in($po_ids) and barcode_no in ($barcode_noss) and entry_form=62 and status_active=1 and is_deleted=0");
	
	?>
	<div style="width:1100px;">
        <table width="1110" cellspacing="0" cellpadding="0" border="0" style="border: 0px solid #000; border-bottom: 0px;">
	        <tr>
	            <td rowspan="2" style="width: 250px;">
					<img src="../../<? echo $image_location; ?>" height="60" width="200" style="float:left;">
	            </td>	          
	        </tr>
		</table>
		<table width="1110" cellspacing="0" cellpadding="0" border="0" align="center"style="border: 0px solid #000; border-bottom: 0px;">
			<tr>				           
	            <td style="width: 850px;font-size:x-large" align="center">
	            	<strong><? echo $company_arr[$company_id]; ?></strong>
	        	</td>
	        </tr>
	        <tr>
	            <td colspan="6" align="center">
	                <?
                    $nameArray=sql_select( "select plot_no, level_no, road_no, block_no, country_id, province, city, zip_code, email, website from lib_company where id='$company_id' and status_active=1 and is_deleted=0");
                    foreach ($nameArray as $result)
                    { 
                    ?>
                        <? echo $result[csf('plot_no')]; ?>,
                        Level No: <? echo $result[csf('level_no')]?>,
                        <? echo $result[csf('road_no')]; ?>, 
                        <? echo $result[csf('block_no')];?>, 
                       	<? echo $result[csf('city')];?>, 
                       	<? echo $result[csf('zip_code')]; ?>, 
                        <?php echo $result[csf('province')];?>, 
                        <? echo $country_arr[$result[csf('country_id')]]; ?><br> 
                        Email Address: <? echo $result[csf('email')];?> 
                        Website No: <? echo $result[csf('website')];?><?
                    }
	                ?> 
	            </td>
	        </tr>
        	<tr>
                <td colspan="6" style="font-size:20px" align="center">
                	<strong >Fabric Inspection Certificate</strong>
                </td>
            </tr>
            <tr style="width: 1110;height: 20px;"><td></td></tr>
        </table>
        <table cellspacing="0" cellpadding="0" width="1110"  border="1" rules="all" class="rpt_table" >
        	<tr style="background-color: #dda">
	         	<td colspan="7"><strong>Order Information</strong></td>
	        </tr>
	        <tr>
	         	<td style="width: 200px;"><b>Dye Job / Prog. No</b></td>$tube_ref_no
	         	<td style="width: 150px;"><p><? echo $program_no; ?></p></td>
	         	<td style="width: 300px;" colspan="2" rowspan="3"></td>
	         	<td style="width: 150px;"><b>Garmenting Unit</b></td>
	         	<td style="width: 150px;"><p><? echo $company_arr[$buyer_unit]; ?></p></td>
	         	<td style="width: 150px;" rowspan="3"></td>
	        </tr>
	        <tr>
				<td style="width: 200px;"><b>Tube Ref No</b></td>
	         	<td style="width: 150px;"><p><? echo $tube_ref_no; ?></p></td>
				 <td style="width: 300px;" colspan="2"></td>
		
	        </tr>
			<tr>
				
	         	<td style="width: 200px;"><b>Batch</b></td>
	         	<td style="width: 150px;"><p><? echo $sql_qc_res[0][csf("batch_no")]; ?></p></td>
	         	<td style="width: 150px;"><b>Shipment Mode</b></td>
	         	<td style="width: 150px;"><p><? echo $ship_mode;?></p></td>
	        </tr>
	        <tr>
	         	<td style="width: 200px;"><b>Order</b></td>
	         	<td style="width: 150px;"><p><? echo $sql_qc_res[0][csf("po_no")]; ?></p></td>
	         	<td style="width: 150px;"><b>Client / Buyer</b></td>
	         	<td style="width: 150px;"><p><? echo $buyer_name;?></p></td>
	        </tr>
	        <tr>
	         	<td style="width: 200px;"><b>Color</b></td>
	         	<td style="width: 150px;"><p><? echo $color_arr[$sql_qc_res[0][csf("color_id")]]; ?></p></td>
	         	<td style="width: 750px;" colspan="5"></td>
	        </tr>
        </table>
        <table width="1110" height="20" cellspacing="0" border="0" style="border: 1px solid #000;  border-top: 0; border-bottom: 0;">
        	<tr>
	         	<td></td>
	        </tr>
        </table>
        <table cellspacing="0" cellpadding="0" width="1110"  border="1" rules="all" class="rpt_table">
        	<tr style="background-color: #dda">
	         	<td style="width: 200px;"><strong>Fabric details / Test results</strong></td>
	         	<td style="width: 300px;" colspan="2"><strong>Fabric Type &amp; Composition</strong></td>
	         	<td style="width: 300px;" colspan="2"><strong>Open Width</strong></td>
	         	<td style="width: 300px;" colspan="2"><strong>Body Part</strong></td>
	        </tr>
	        <tr>
	         	<td style="width: 200px; border-color: #000; border-style: solid; border-width: 0 1px 0px 0px;"><b>Composition</b></td>
	         	<td style="width: 300px;" colspan="2"><p><? echo $sql_qc_res[0][csf("item_description")]; ?></p></td>
	         	<td style="width: 300px;" colspan="2"><p><? echo $fabric_typee[$sql_qc_res[0][csf("width_dia_type")]]; ?></p></td>
	         	<td style="width: 300px;" colspan="2"><p><? echo $body_part[$sql_qc_res[0][csf("body_part_id")]]; ?></p></td>
	        </tr>
	        <tr>
	         	<td style="width: 200px;"><b>Required Width-Bulk</b></td>
	         	<td style="width: 150px;"><p><? echo $sql_qc_res[0][csf("req_width_bulk")]; ?></p></td>
	         	<td style="width: 150px;"><p><? echo $sql_qc_res[0][csf("req_width_bulk_inches")]; ?></p></td>
	         	<td style="width: 150px;"><b>Reason</b></td>
	         	<td style="width: 150px;"><p><? echo $sql_reason; ?></p></td>
	         	<td style="width: 150px;"><b>Program No</b></td>
	         	<td style="width: 150px;"><p><? echo $program_no; ?></p></td>
	        </tr>
	        <tr>
	         	<td style="width: 200px;"><b>Actual width-Bulk</b></td>
	         	<td style="width: 150px;"><p><? echo $sql_qc_res[0][csf("actual_width_bulk")]; ?></p></td>
	         	<td style="width: 150px;"><p><? echo $sql_qc_res[0][csf("actual_width_bulk_inches")]; ?></p></td>
	         	<td style="width: 150px;"><b>Shrinkage L</b></td>
	         	<td style="width: 150px;"><p><? echo $sql_qc_res[0][csf("shrinkageL")]; ?></p></td>
	         	<td style="width: 150px;"><b>Prog. Req. Qty</b></td>
	         	<td style="width: 150px;"><p><? echo $sql_qc_res[0][csf("program_qty")]; ?></p></td>
	        </tr>
	        <tr>
	         	<td style="width: 200px;"><b>AOP - width print to print</b></td>
	         	<td style="width: 150px;"><p><? echo $sql_qc_res[0][csf("width_print")]; ?></p></td>
	         	<td style="width: 150px;"><p><? echo $sql_qc_res[0][csf("width_print_inches")]; ?></p></td>
	         	<td style="width: 150px;"><b>Shinkage W</b></td>
	         	<td style="width: 150px;"><p><? echo $sql_qc_res[0][csf("shrinkageW")]; ?></p></td>
	         	<td style="width: 150px;"><b>Actual Qty</b></td>
	         	<td style="width: 150px;"><p><? echo $actual_qty; ?></p></td>
	        </tr>
	        <tr>
	         	<td style="width: 200px;"><b>Required Density-Bulk</b></td>
	         	<td style="width: 150px;"><p><? echo $sql_qc_res[0][csf("req_dencity_bulk")]; ?></p></td>
	         	<td style="width: 150px;"><p><? echo $sql_qc_res[0][csf("req_dencity_bulk_gm2")]; ?></p></td>
	         	<td style="width: 150px;"><b>Twist</b></td>
	         	<td style="width: 150px;"><p><? echo $sql_qc_res[0][csf("shrinkageW")]; ?></p></td>
	         	<td style="width: 150px;"><b>Difference</b></td>
	         	<td style="width: 150px;"><p><? echo $program_qty-$actual_qty; ?></p></td>
	        </tr>
	        <tr>
	         	<td style="width: 200px;"><b>Actual Density-Bulk(R/M/L)</b></td>
	         	<td style="width: 150px;"><p><? echo $sql_qc_res[0][csf("actual_dencity_bulk")]; ?></p></td>
	         	<td style="width: 150px;"><p><? echo $sql_qc_res[0][csf("actual_dencity_bulk_gm2")]; ?></p></td>
	        </tr>
	        <tr>
	         	<td style="width: 200px;"><b>Req width - acc 2</b></td>
	         	<td style="width: 150px;"><p><? echo $sql_qc_res[0][csf("req_width_acc2")]; ?></p></td>
	         	<td style="width: 150px;"><p><? echo $sql_qc_res[0][csf("req_width_acc2_inches")]; ?></p></td>
	        </tr>
	        <tr>
	         	<td style="width: 200px;"><b>Actual width - acc 2</b></td>
	         	<td style="width: 150px;"><p><? echo $sql_qc_res[0][csf("actual_width_acc2")]; ?></p></td>
	         	<td style="width: 150px;"><p><? echo $sql_qc_res[0][csf("actual_width_acc2_inches")]; ?></p></td>
	        </tr>
	        <tr>
	         	<td style="width: 200px;"><b>Req density - acc 2</b></td>
	         	<td style="width: 150px;"><p><? echo $sql_qc_res[0][csf("req_dencity_acc2")]; ?></p></td>
	         	<td style="width: 150px;"><p><? echo $sql_qc_res[0][csf("req_dencity_acc2_gm2")]; ?></p></td>
	        </tr>
	        <tr>
	         	<td style="width: 200px;"><b>Actual density - acc 2</b></td>
	         	<td style="width: 150px;"><p><? echo $sql_qc_res[0][csf("actual_dencity_acc2")]; ?></p></td>
	         	<td style="width: 150px;"><p><? echo $sql_qc_res[0][csf("actual_dencity_acc2_gm2")]; ?></p></td>
	        </tr>
	        <tr>
	         	<td style="width: 200px;"><b>Remarks</b></td>
	         	<td style="width: 300px;" colspan="2"><p><? echo $sql_qc_res[0][csf("remarks")]; ?></p></td>
	        </tr>
        </table>
        <table width="1110" height="20" cellspacing="0" border="0" style="border: 1px solid #000;  border-top: 0; border-bottom: 0;">
        	<tr>
	         	<td></td>
	        </tr>
        </table>
        <table cellspacing="0" cellpadding="0" width="1110"  border="1" rules="all" class="rpt_table">
        	<tr style="background-color: #dda">
	         	<td colspan="7"><strong>Scanning Details</strong></td>
	        </tr>
	        <tr>
	         	<td style="width: 200px;"><b>Roll No/Barcode No</b></td>
	         	<td style="width: 150px;"><b>Qty KGS</b></td>
	         	<td style="width: 150px;"><b>QTY Meters</b></td>
	         	<td style="width: 600px;" colspan="4"><b>QC remarks</b></td>
	        </tr>
	        <?
	        $tot_qc_qty=0;$tot_qty=0;
			// 		echo "<pre>";
			// print_r($sql_qc_pass);
	        foreach ($sql_qc_pass as $row) 
	        {
	        	$i=1;
	        	?>
		        <tr>
		         	<td style="width: 200px;" align="center"><? echo $row[csf("barcode_no")]; ?></td>
		         	<td style="width: 150px;"><p><? echo $row[csf("qc_pass_qnty")]; ?></p></td>
		         	<td style="width: 150px;"><p></p></td>
		         	<td style="width: 600px;" colspan="4"><p><? echo $comments_arr[$row[csf("barcode_no")]]; ?></p></td>
		        </tr>
		        <?
		        $tot_qc_qty += $row[csf("qc_pass_qnty")];
		        $i++;
		    }
		    ?>    
	        <tr>
	         	<td style="width: 200px;" align="right"><b>Total:</b></td>
	         	<td style="width: 150px;"><p><? echo $tot_qc_qty; ?></p></td>
	         	<td style="width: 150px;"><p></p></td>
	         	<td style="width: 600px;" colspan="4"></td>
	        </tr>	       
	    </table>
	    <table width="1110" height="20" cellspacing="0" border="0" style="border: 1px solid #000;  border-top: 0; border-bottom: 0;">
        	<tr>
	         	<td></td>
	        </tr>
        </table>
        <table cellspacing="0" cellpadding="0" width="1110"  border="1" rules="all" class="rpt_table">	
        	 <tr>
	         	<td style="width: 200px;"><b>Fabric Quality</b></td>
	         	<td style="width: 900px;" colspan6><p><? echo $pass_fail_arr[$sql_qc_res[0][csf("pass_fail")]]; ?></p></td>
	         	<!-- <td style="width: 300px;" colspan="2" align="right">Approved by</td>
	         	<td style="width: 450px;" colspan="3"></td> -->
	        </tr>    
        </table>
    </div>
    <br/><br/><br/><br/>
    <div><b>Approved by</b></div>  
    <br/>
	<?
}

if ($action == "qc_final_inspection_print2")
{
	extract($_REQUEST);
	list ($company_id, $mst_id) = explode('**', $data);
	$pass_fail_arr = array(1=>'Pass',2=>'Fail');
	$company_arr = return_library_array( "select id, company_name from lib_company", "id", "company_name" );
	$buyer_arr = return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name" );
	$color_arr = return_library_array( "select id, color_name from lib_color",'id','color_name');
	$country_arr = return_library_array( "select id, country_name from lib_country", "id", "country_name" );
	$image_location = return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$company_id'","image_location");

	$sql_qc="SELECT a.id, a.company_id, a.batch_no, a.batch_id, a.color_id, b.program_no, c.reference_no, c.reference_qty,c.planned_date, a.date_finalized, b.program_qty, a.item_description, b.actual_qty, a.body_part_id, a.width_dia_type, a.po_no, a.booking_no, b.shrinkageL, b.shrinkageW, b.twist, b.req_width_bulk, b.req_width_bulk_inches, b.actual_width_bulk, b.actual_width_bulk_inches, b.width_print, b.width_print_inches, b.req_dencity_bulk, b.req_dencity_bulk_gm2, b.actual_dencity_bulk, b.actual_dencity_bulk_gm2, b.req_width_acc2, b.req_width_acc2_inches, b.actual_width_acc2, b.actual_width_acc2_inches, b.req_dencity_acc2, b.req_dencity_acc2_gm2, b.actual_dencity_acc2, b.actual_dencity_acc2_gm2, b.pass_fail, b.remarks,b.wash_fastness, b.handfeel,b.water_fastness, b.sewability, b.pilling, b.shade_deE, b.shade_deL, b.shade_deC, b.shade_deH, b.carcasse_assessed,b.ph_for,b.rub_fastness_wet,b.bale_to_bale,b.rub_fastness_dry,b.skewness_warp,b.job_to_job,b.skewness_weft,b.shading_test_same_bale,b.req_width_acc1_inches,b.actual_width_acc1_inches,b.req_dencity_acc1_gm2,b.actual_dencity_acc1_gm2,b.phenolic_yellowing,b.ext_rec_l,b.symmetry,b.hydrophility,b.shedding_fibers,b.steam_test,b.shading_selvedge,b.flammability_test,b.tear_strength,b.print_durability,b.bursting_str_del_fabrics_kpa,b.pattern_height,b.orientation_finished_orders,b.width_selvedge_inches from qc_final_inspection_mst a, qc_final_inspection_dtls b join ppl_reference_creation c on b.program_no=c.program_no where a.id=b.mst_id and a.company_id=$company_id and a.id=$mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";

		echo $sql_qc;


	$sql_qc_res = sql_select($sql_qc);

	$batch_id = $sql_qc_res[0][csf("batch_id")];
	$batch_no = $sql_qc_res[0][csf("batch_no")];
	$booking_no = $sql_qc_res[0][csf("booking_no")];
	$program_no = $sql_qc_res[0][csf("program_no")];
	$tube_ref_no = $sql_qc_res[0][csf("reference_no")];
	$tube_ref_kg = $sql_qc_res[0][csf("reference_qty")];
	$tube_ref_date = $sql_qc_res[0][csf("planned_date")];
	$program_qty =  $sql_qc_res[0][csf("program_qty")];
	$actual_qty =  $sql_qc_res[0][csf("actual_qty")];
	$dia_with =  $sql_qc_res[0][csf("width_dia_type")];


	$buyer_unit = return_field_value("buyer_id","pro_batch_create_mst a, fabric_sales_order_mst f","a.sales_order_no=f.job_no and a.status_active=1 and a.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and a.id=$batch_id");
	$sql_reason=return_field_value("remarks","pro_fab_subprocess","batch_id=$batch_id and batch_no='$batch_no' and company_id=$company_id and entry_form=35 and load_unload_id=2 and status_active=1 and is_deleted=0");

	$sql="select c.buyer_name, c.ship_mode from pro_batch_create_mst a, wo_booking_dtls b, wo_po_details_master c where  a.booking_no=b.booking_no and b.job_no=c.job_no and a.id=$batch_id and a.batch_no='$batch_no' and a.booking_no='$booking_no' group by c.buyer_name, c.ship_mode";
	$sql_res = sql_select($sql);
	$buyer_name= $buyer_arr[$sql_res[0][csf("buyer_name")]];
	$ship_mode= $shipment_mode[$sql_res[0][csf("ship_mode")]];


	$sql_barcode = sql_select("select po_id, barcode_no from pro_batch_create_dtls where mst_id='$batch_id' and program_no='$program_no' and status_active=1 and is_deleted=0");
	foreach ($sql_barcode as $val) {
		$barcode_no .= $val[csf("barcode_no")].',';
		$po_id .= $val[csf("po_id")].',';
	}
	$barcode_nos = chop($barcode_no,',');
	$po_ids = chop($po_id,',');
	$po_ids=implode(",", array_unique(explode(",", $po_ids)));

	$sql_res =  sql_select("select id, barcode_no, comments from pro_qc_result_mst where barcode_no in ($barcode_nos) and entry_form=267 and status_active=1 and is_deleted=0");
	$comments_arr=array();
	foreach ($sql_res as $val) {
		$barcode_no_qc .= $val[csf("barcode_no")].',';
		$comments_arr[$val[csf("barcode_no")]]  = $val[csf("comments")];
	}
	$barcode_noss = chop($barcode_no_qc,',');

	//echo "select id, qc_pass_qnty from pro_roll_details where po_breakdown_id in($po_ids) and barcode_no in ($barcode_noss) and entry_form=62 and status_active=1 and is_deleted=0";


	$sql_qc_pass=sql_select("select id, qc_pass_qnty, barcode_no, roll_no from pro_roll_details where po_breakdown_id in($po_ids) and barcode_no in ($barcode_noss) and entry_form=62 and status_active=1 and is_deleted=0");
	
	?>
	<div style="width:1100px;">
        <table width="1110" cellspacing="0" cellpadding="0" border="0" style="border: 0px solid #000; border-bottom: 0px;">
	        <tr>
	            <td rowspan="2" style="width: 250px;">
					<img src="../../<? echo $image_location; ?>" height="60" width="200" style="float:left;">
	            </td>	          
	        </tr>
		</table>
		<table width="1110" cellspacing="0" cellpadding="0" border="0" align="center"style="border: 0px solid #000; border-bottom: 0px;">
			<tr>				           
	            <td style="width: 850px;font-size:x-large" align="center">
	            	<strong><? echo $company_arr[$company_id]; ?></strong>
	        	</td>
	        </tr>
	        <tr>
	            <td colspan="6" align="center">
	                <?
					
                    $nameArray=sql_select( "select plot_no, level_no, road_no, block_no, country_id, province, city, zip_code, email, website from lib_company where id='$company_id' and status_active=1 and is_deleted=0");
                    foreach ($nameArray as $result)
                    { 
                    ?>
                        <? echo $result[csf('plot_no')]; ?>,
                        Level No: <? echo $result[csf('level_no')]?>,
                        <? echo $result[csf('road_no')]; ?>, 
                        <? echo $result[csf('block_no')];?>, 
                       	<? echo $result[csf('city')];?>, 
                       	<? echo $result[csf('zip_code')]; ?>, 
                        <?php echo $result[csf('province')];?>, 
                        <? echo $country_arr[$result[csf('country_id')]]; ?><br> 
                        Email Address: <? echo $result[csf('email')];?> 
                        Website No: <? echo $result[csf('website')];?><?
                    }
	                ?> 
	            </td>
	        </tr>
        	<tr>
                <td colspan="6" style="font-size:20px" align="center">
                	<strong >Fabric Inspection Certificate</strong>
                </td>
            </tr>
            <tr style="width: 1110;height: 20px;"><td></td></tr>
        </table>
        <table cellspacing="0" cellpadding="0" width="1110"  border="1" rules="all" class="rpt_table" >
        	<tr style="background-color: #dda">
	         	<td colspan="7"><strong>Order Information</strong></td>
	        </tr>
	        <tr>
			    <td style="width: 200px;"><b>Tube Ref No</b></td>
	         	<td style="width: 150px;"><p><? echo $tube_ref_no; ?></p></td>	         
	         	<td style="width: 150px;"><b>W/G</b></td>
	         	<td style="width: 150px;"><p><? echo $company_arr[$buyer_unit]; ?></p></td>
	         	
	        </tr>
	        <tr>
				<td style="width: 200px;"><b>Total Tube/Ref. [kg]</b></td>
	         	<td style="width: 150px;"><p><? echo $tube_ref_kg; ?></p></td>
				 <td style="width: 150px;"><b>Pallet No</b></td>
	         	<td style="width: 150px;"><p><? echo $company_arr[$buyer_unit]; ?></p></td>
		
	        </tr>
			<tr>
	         	<td style="width: 200px;"><b>Tube/Ref. Date</b></td>
	         	<td style="width: 150px;"><p><? echo $tube_ref_date; ?></p></td>
	         	<td style="width: 150px;"><b>Batch No.</b></td>
	         	<td style="width: 150px;"><p><? echo $sql_qc_res[0][csf("batch_no")];?></p></td>
	        </tr>
	        <tr>
	         	<td style="width: 200px;"><b>FSO No</b></td>
	         	<td style="width: 150px;"><p><? echo $sql_qc_res[0][csf("po_no")]; ?></p></td>
	         	<td style="width: 150px;"><b>Program No</b></td>
	         	<td style="width: 150px;"><p><? echo $program_no;?></p></td>
	        </tr>
	        <tr>
			    <td style="width: 200px;"><b>Booking No</b></td>
	         	<td style="width: 150px;"><p><? echo $color_arr[$sql_qc_res[0][csf("color_id")]]; ?></p></td>
	         
				 <td style="width: 150px;"><b>Dia/W. Type</b></td>
	         	<td style="width: 150px;"><p><? echo $dia_with; ?></p></td>
	        </tr>
			<tr>
		    	<td style="width: 200px;"><b>Booking Type</b></td>
	         	<td style="width: 150px;"><p><? echo $color_arr[$sql_qc_res[0][csf("color_id")]]; ?></p></td>
	         
				 <td style="width: 150px;"><b>Knitting Source</b></td>
	         	<td style="width: 150px;"><p><? echo $company_arr[$buyer_unit]; ?></p></td>
	        </tr>
			<tr>
	         	<td style="width: 200px;"><b>Buyer</b></td>
	         	<td style="width: 150px;"><p><? echo $color_arr[$sql_qc_res[0][csf("color_id")]]; ?></p></td>
				 <td style="width: 150px;"><b>Knitting Party</b></td>
	         	<td style="width: 150px;"><p><? echo $company_arr[$buyer_unit]; ?></p></td>
	        </tr>
			<tr>
		    	<td style="width: 200px;"><b>Fin. Delivery Date</b></td>
	         	<td style="width: 150px;"><p><? echo $color_arr[$sql_qc_res[0][csf("color_id")]]; ?></p></td>	         
				<td style="width: 150px;"><b>Fab.Special Process Req.</b></td>
	         	<td style="width: 150px;"><p><? echo $company_arr[$buyer_unit]; ?></p></td>
	        </tr>
			<tr>
		    	<td style="width: 200px;"><b>Dye M/C No, Date&Time</b></td>
	         	<td style="width: 150px;"><p><? echo $color_arr[$sql_qc_res[0][csf("color_id")]]; ?></p></td>
				<td style="width: 150px;"><b>Dyeing Company</b></td>
	         	<td style="width: 150px;"><p><? echo $company_arr[$buyer_unit]; ?></p></td>
	        </tr>
			<tr>
		    	<td style="width: 200px;"><b>Remarks</b></td>
	         	<td style="width: 150px;" colspan="3"><p><? echo $color_arr[$sql_qc_res[0][csf("color_id")]]; ?></p></td>
			
	        </tr>
        </table>
        <table width="1110" height="20" cellspacing="0" border="0" style="border: 1px solid #000;  border-top: 0; border-bottom: 0;">
        	<tr>
	         	<td></td>
	        </tr>
        </table>

        <table cellspacing="0" cellpadding="0" width="1110"  border="1" rules="all" class="rpt_table">
        	<tr style="background-color: #dda">
	         	<td style="width: 200px;" colspan="6" align="center"><strong>Fabric details / Test results</strong></td>	         	
	         	
	        </tr>
	        <tr>
	         	

				 <td style="width: 200px;"><b>Handfeel</b></td>
	         	<td style="width: 150px;"><p><? echo $sql_qc_res[0][csf("handfeel")]; ?></p></td>	    
	         	<td style="width: 150px;"><b>Wash Fastness</b></td>
	         	<td style="width: 150px;"><p><? echo $sql_qc_res[0][csf("wash_fastness")]; ?></p></td>
	         	<td style="width: 150px;"><b>Shrinkage-L(%)</b></td>
	         	<td style="width: 150px;"><p><? echo $sql_qc_res[0][csf("shrinkageL")]; ?></p></td>
	        </tr>
	        <tr>
	         	<td style="width: 200px;"><b>Sewability</b></td>
	         	<td style="width: 150px;"><p><? echo $sql_qc_res[0][csf("sewability")]; ?></p></td>	    
	         	<td style="width: 150px;"><b>Water Fastness</b></td>
	         	<td style="width: 150px;"><p><? echo $sql_qc_res[0][csf("water_fastness")]; ?></p></td>
	         	<td style="width: 150px;"><b>Shrinkage-W(%)</b></td>
	         	<td style="width: 150px;"><p><? echo $sql_qc_res[0][csf("shrinkageW")]; ?></p></td>
	        </tr>
	        <tr>
	         	<td style="width: 200px;"><b>PH For White/Pastel Clr</b></td>
	         	<td style="width: 150px;"><p><? echo $sql_qc_res[0][csf("ph_for")]; ?></p></td>	         
	         	<td style="width: 150px;"><b>Rub Fastness (Wet)</b></td>
	         	<td style="width: 150px;"><p><? echo $sql_qc_res[0][csf("rub_fastness_wet")]; ?></p></td>
	         	<td style="width: 150px;"><b>Twist(%)</b></td>
	         	<td style="width: 150px;"><p><? echo $sql_qc_res[0][csf("twist")]; ?></p></td>
	        </tr>
	        <tr>
	         	<td style="width: 200px;"><b>Shading Test Bale To Bale</b></td>
	         	<td style="width: 150px;"><p><? echo $sql_qc_res[0][csf("bale_to_bale")]; ?></p></td>	         
	         	<td style="width: 150px;"><b>Rub Fastness (Dry)</b></td>
	         	<td style="width: 150px;"><p><? echo $sql_qc_res[0][csf("rub_fastness_dry")]; ?></p></td>
	         	<td style="width: 150px;"><b>Skewness-Warp(deg.)</b></td>
	         	<td style="width: 150px;"><p><? echo $sql_qc_res[0][csf("txt_skewness_warp")]; ?></p></td>
	        </tr>
	        <tr>
	         	<td style="width: 200px;"><b>Shading Test Job To Job</b></td>
	         	<td style="width: 150px;"><p><? echo $sql_qc_res[0][csf("job_to_job")]; ?></p></td>	  
	         	<td style="width: 150px;"><b>Pilling</b></td>
	         	<td style="width: 150px;"><p><? echo $sql_qc_res[0][csf("pilling")]; ?></p></td>
	         	<td style="width: 150px;"><b>Skewness-Weft(deg.)</b></td>
	         	<td style="width: 150px;"><p><? echo $sql_qc_res[0][csf("skewness_weft")]; ?></p></td>
	        </tr>
	        <tr>
	         	<td style="width: 200px;"><b>Shading Test Within Same Bale</b></td>
	         	<td style="width: 150px;"><p><? echo $sql_qc_res[0][csf("shading_test_same_bale")]; ?></p></td>
	         	<td style="width: 150px;"><b>Shade (DE)</b></td>
	         	<td style="width: 150px;"><p><? echo $sql_qc_res[0][csf("shade_deE")]; ?></p></td>
	         	<td style="width: 150px;"><b>Difference</b></td>
	         	<td style="width: 150px;"><p><? echo $sql_qc_res[0][csf("pilling")]; ?></p></td>
	        </tr>
	        <tr>
	         	<td style="width: 200px;"><b>Required Width-Bulk(inch)</b></td>
	         	<td style="width: 150px;"><p><? echo $sql_qc_res[0][csf("req_width_bulk_inches")]; ?></p></td>
	         	<td style="width: 150px;"><b>Required Width-ACC1(inch)</b></td>
	         	<td style="width: 150px;"><p><? echo $sql_qc_res[0][csf("req_width_acc1_inches")]; ?></p></td>
	         	<td style="width: 150px;"><b>Required Width-ACC2(inch)</b></td>
	         	<td style="width: 150px;"><p><? echo $sql_qc_res[0][csf("req_width_acc2_inches")]; ?></p></td>
	        </tr>
	        <tr>
	         	<td style="width: 200px;"><b>Actual Width-Bulk(inch)</b></td>
	         	<td style="width: 150px;"><p><? echo $sql_qc_res[0][csf("actual_width_acc2")]; ?></p></td>
	         	<td style="width: 150px;"><b>Actual Width-ACC1(inch)</b></td>
	         	<td style="width: 150px;"><p><? echo $sql_qc_res[0][csf("actual_width_acc1_inches")]; ?></p></td>
	         	<td style="width: 150px;"><b>Actual Width-ACC2(inch)</b></td>
	         	<td style="width: 150px;"><p><? echo $sql_qc_res[0][csf("actual_width_acc2_inches")]; ?></p></td>
	        </tr>
	        <tr>
	         	<td style="width: 200px;"><b>Required Density-Bulk(g/m2)</b></td>
	         	<td style="width: 150px;"><p><? echo $sql_qc_res[0][csf("req_dencity_bulk_gm2")]; ?></p></td>
	         	<td style="width: 150px;"><b>Required Density-ACC1(g/m2)</b></td>
	         	<td style="width: 150px;"><p><? echo $sql_qc_res[0][csf("req_dencity_acc1_gm2")]; ?></p></td>
	         	<td style="width: 150px;"><b>Required Density-ACC2(g/m2)</b></td>
	         	<td style="width: 150px;"><p><? echo $sql_qc_res[0][csf("req_dencity_acc2_gm2")]; ?></p></td>
	        </tr>
	        <tr>
	         	<td style="width: 200px;"><b>Actual Density-Bulk[R/M/L],(g/m2)</b></td>
	         	<td style="width: 150px;"><p><? echo $sql_qc_res[0][csf("actual_dencity_bulk_gm2")]; ?></p></td>
	         	<td style="width: 150px;"><b>Actual Density-ACC1(g/m2)</b></td>
	         	<td style="width: 150px;"><p><? echo $sql_qc_res[0][csf("actual_dencity_acc1_gm2")]; ?></p></td>
	         	<td style="width: 150px;"><b>Actual Density-ACC2(g/m2)</b></td>
	         	<td style="width: 150px;"><p><? echo $sql_qc_res[0][csf("actual_dencity_acc2_gm2")]; ?></p></td>
	        </tr>
	        <tr>
	         	<td style="width: 200px;"><b>Phenolic Yellowing (White)</b></td>
	         	<td style="width: 150px;" ><p><? echo $sql_qc_res[0][csf("phenolic_yellowing")]; ?></p></td>
				 <td style="width: 150px;"><b>Ext & Rec (% Lycra)</b></td>
	         	<td style="width: 150px;"><p><? echo $sql_qc_res[0][csf("ext_rec_l")]; ?></p></td>
	         	<td style="width: 150px;"><b></b></td>
	         	<td style="width: 150px;"><p></p></td>
	        </tr>
			<tr>
	         	<td style="width: 200px;"><b>Carcasse Assessed</b></td>
	         	<td style="width: 150px;" ><p><? echo $sql_qc_res[0][csf("carcasse_assessed")]; ?></p></td>
				 <td style="width: 150px;"><b>Symmetry</b></td>
	         	<td style="width: 150px;"><p><? echo $sql_qc_res[0][csf("symmetry")]; ?></p></td>
	         	<td style="width: 150px;"><b></b></td>
	         	<td style="width: 150px;"><p></p></td>
	        </tr>
        </table>
		<br>
		<table cellspacing="0" cellpadding="0" width="1110"  border="1" rules="all" class="rpt_table">
        	<tr style="background-color: #dda">
	        	
				<td style="width: 200px;" colspan="2"><b>Applicable To AOP Orders</b></td>	            
	         	<td style="width: 150px;" colspan="2"><b>Applicable To Special Finished Orders</b></td>	         	
	         	<td style="width: 150px;" colspan="2"><b>Applicable To Delicate Fabrics</b></td>
	        </tr>
	        <tr>
				<td style="width: 200px;"><b>Hydrophility</b></td>
	         	<td style="width: 150px;"><p><? echo $sql_qc_res[0][csf("hydrophility")]; ?></p></td>	    
	         	<td style="width: 150px;"><b>Shedding Fibers</b></td>
	         	<td style="width: 150px;"><p><? echo $sql_qc_res[0][csf("cbo_shedding_fibers")]; ?></p></td>
	         	<td style="width: 150px;"><b>Steam Test</b></td>
	         	<td style="width: 150px;"><p><? echo $sql_qc_res[0][csf("steam_test")]; ?></p></td>
	        </tr>
	        <tr>
	         	<td style="width: 200px;"><b>Shading-Selvedge To Selvedge</b></td>
	         	<td style="width: 150px;"><p><? echo $sql_qc_res[0][csf("shading_selvedge")]; ?></p></td>	    
	         	<td style="width: 150px;"><b>Flammability Test</b></td>
	         	<td style="width: 150px;"><p><? echo $sql_qc_res[0][csf("flammability_test")]; ?></p></td>
	         	<td style="width: 150px;"><b>Tear Strength</b></td>
	         	<td style="width: 150px;"><p><? echo $sql_qc_res[0][csf("tear_strength")]; ?></p></td>
	        </tr>
	        <tr>
	         	<td style="width: 200px;"><b>Print Durability</b></td>
	         	<td style="width: 150px;"><p><? echo $sql_qc_res[0][csf("print_durability")]; ?></p></td>	         
	         	<td style="width: 150px;"><b>Orientation</b></td>
	         	<td style="width: 150px;"><p><? echo $sql_qc_res[0][csf("orientation_finished_orders")]; ?></p></td>
	         	<td style="width: 150px;"><b>Bursting Strength(kpa)</b></td>
	         	<td style="width: 150px;"><p><? echo $sql_qc_res[0][csf("bursting_str_del_fabrics_kpa")]; ?></p></td>
	        </tr>
	        <tr>
	         	<td style="width: 200px;"><b>Pattern Height</b></td>
	         	<td style="width: 150px;"><p><? echo $sql_qc_res[0][csf("pattern_height")]; ?></p></td>	         
	         	<td style="width: 150px;"><b>Remarks</b></td>
	         	<td style="width: 150px;"><p><? echo $sql_qc_res[0][csf("remarks")]; ?></p></td>
	         	<td style="width: 150px;"><b>Orientation</b></td>
	         	<td style="width: 150px;"><p><? echo $sql_qc_res[0][csf("orientation_finished_orders")]; ?></p></td>
	        </tr>
	        <tr>
	         	<td style="width: 200px;"><b>Width Print To Print(inch)</b></td>
	         	<td style="width: 150px;"><p><? echo $sql_qc_res[0][csf("width_print_inches")]; ?></p></td>	  
	         	<td style="width: 150px;"><b></b></td>
	         	<td style="width: 150px;"><p></p></td>
	         	<td style="width: 150px;"><b></b></td>
	         	<td style="width: 150px;"><p></p></td>
	        </tr>
	        <tr>
	         	<td style="width: 200px;"><b>Width Selvedge To Selvedge(inch)</b></td>
	         	<td style="width: 150px;"><p><? echo $sql_qc_res[0][csf("width_selvedge_inches")]; ?></p></td>
	         	<td style="width: 150px;"><b></b></td>
	         	<td style="width: 150px;"><p></p></td>
	         	<td style="width: 150px;"><b></b></td>
	         	<td style="width: 150px;"><p></p></td>
	        </tr>
        </table>
		<br>
		<table cellspacing="0" cellpadding="0" width="1110"  border="1" rules="all" class="rpt_table">
        	<tr style="background-color: #dda">
	        	
				<td style="width: 200px;" colspan="2"><b>Body Part and Fabric Details</b></td>	         	    
	         	<td style="width: 150px;"><b>Colour</b></td>
	         	<td style="width: 150px;"><b>Colour Range</b></td>
	         	<td style="width: 150px;"><b>Dia/ Width</b></td>
	         	<td style="width: 150px;"><b>GSM</b></td>
				<td style="width: 150px;"><b>Fin. Wgt. (Kg)</b></td>
	         	<td style="width: 150px;"><b>Fin. Wgt. (Mtr)</b></td>
	        </tr>
	        <tr>
				<td style="width: 200px;"><b>Hydrophility</b></td>
	         	<td style="width: 150px;"><p><? echo $sql_qc_res[0][csf("item_description")]."".$body_part[$sql_qc_res[0][csf("body_part_id")]]; ?></p></td>	    
	         	<td style="width: 150px;"><b>Shedding Fibers</b></td>
	         	<td style="width: 150px;"><p><? echo $sql_reason; ?></p></td>
	         	<td style="width: 150px;"><b>Steam Test</b></td>
	         	<td style="width: 150px;"><p><? echo $program_no; ?></p></td>
				<td style="width: 150px;"><b>Steam Test</b></td>
	         	<td style="width: 150px;"><p><? echo $program_no; ?></p></td>
	        </tr>
        </table>
		<br>
		<table cellspacing="0" cellpadding="0" width="1110"  border="1" rules="all" class="rpt_table">
        	<tr style="background-color: #dda">
	        	
				<td style="width: 200px;"><b>Roll No(Pattet Max Wgt. 230 kg)</b></td>	         	    
	         	<td style="width: 150px;"><b>Roll No Weight (Kg)</br>(Roll Max Wgt. 25 kg)</b></td>
	         	<td style="width: 150px;"><b>Trims Weight (Kg) </br>(Qty. In Pcs)</b></td>
	         	<td style="width: 150px;"><b>(Qty. In Size )</b></td>
	         	<td style="width: 150px;"><b>Barcode No</b></td>
				<td style="width: 150px;"><b>Hand Writing Area </br>Scanned Weight [Kg]</b></td>
	         	
	        </tr>
	        <tr>
				<td style="width: 200px;"><b>Hydrophility</b></td>
	         	<td style="width: 150px;"><p><? echo $sql_qc_res[0][csf("item_description")]."".$body_part[$sql_qc_res[0][csf("body_part_id")]]; ?></p></td>	    
	         	<td style="width: 150px;"><b>Shedding Fibers</b></td>
	         	<td style="width: 150px;"><p><? echo $sql_reason; ?></p></td>
	         	<td style="width: 150px;"><b>Steam Test</b></td>
	         	<td style="width: 150px;"><p><? echo $program_no; ?></p></td>
		
	        </tr>
        </table>
		<br>

		
    </div>
    <br/><br/><br/><br/>
    <div><b>Approved by</b></div>  
    <br/>
	<?
}
	

?>