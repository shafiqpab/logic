<?
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");
include('../../../includes/common.php');
$permission = $_SESSION['page_permission'];
$data = $_REQUEST['data'];
$action = $_REQUEST['action'];

$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');


$batch_arr = return_library_array("select id, batch_no from pro_batch_create_mst", "id", "batch_no");
$machine_name = return_library_array("select machine_no,id from  lib_machine_name where is_deleted=0", "id", "machine_no");
$yarncount = return_library_array("select id, yarn_count from  lib_yarn_count", 'id', 'yarn_count');
$brand_name = return_library_array("select id, brand_name from   lib_brand", 'id', 'brand_name');
if ($action == "load_drop_floor") {
	$data = explode('_', $data);
	$loca = $data[0];
	$com = $data[1];
	//echo "select id,floor_name from lib_prod_floor where status_active=1 and is_deleted=0 and company_id='$data[0]'  and production_process=3  order by floor_name";die;
	echo create_drop_down("cbo_floor", 135, "select id,floor_name from lib_prod_floor where status_active=1 and is_deleted=0 and company_id='$data[0]'  and production_process=3  order by floor_name", "id,floor_name", 1, "-- Select Floor --", $selected, "load_drop_down( 'requires/dyeing_production_controller', document.getElementById('cbo_service_company').value+'**'+this.value, 'load_drop_machine', 'machine_td' );");
	exit();
}
if ($action == "load_drop_sub_process") {
	echo create_drop_down("cbo_sub_process", 135, $conversion_cost_head_array, "", 0, "", $selected, "", "", $data);
}
if ($action == "load_drop_machine") {
	$floor_arr = return_library_array("select id,floor_name from  lib_prod_floor", 'id', 'floor_name');
	$data = explode('**', $data);
	$com = $data[0];
	$floor = $data[1];
	if ($db_type == 2) {


		echo create_drop_down("cbo_machine_name", 135, "select id,machine_no || '-' || brand as machine_name from lib_machine_name where category_id=2 and
			company_id=$com and floor_id=$floor and status_active=1 and is_deleted=0 and is_locked=0 order by machine_name", "id,machine_name", 1, "-- Select Machine --", $selected, "get_php_form_data(document.getElementById('cbo_service_company').value+'**'+document.getElementById('cbo_floor').value+'**'+this.value,
			'populate_data_from_machine', 'requires/dyeing_production_controller' );", "");
	} else if ($db_type == 0) {
		echo create_drop_down("cbo_machine_name", 135, "select id,concat(machine_no,'-',brand) as machine_name from lib_machine_name where category_id=2 and company_id=$com and floor_id=$floor and status_active=1 and is_deleted=0 and is_locked=0 order by machine_name", "id,machine_name", 1, "-- Select Machine --", $selected, "get_php_form_data(document.getElementById('cbo_service_company').value+'**'+document.getElementById('cbo_floor').value+'**'+this.value, 'populate_data_from_machine', 'requires/dyeing_production_controller' );", "");
	}
	exit();
}
if ($action == "populate_data_from_machine") {
	$floor_arr = return_library_array("select id,floor_name from  lib_prod_floor", 'id', 'floor_name');
	$ex_data = explode('**', $data);
	$sql_res = "select id, floor_id, machine_group from lib_machine_name where id=$ex_data[2] and category_id=2 and company_id=$ex_data[0] and  floor_id=$ex_data[1] and status_active=1 and is_deleted=0";
	//echo $sql_res;die;
	$nameArray = sql_select($sql_res);
	if(count($nameArray)>0)
	{
		foreach ($nameArray as $row) {
			echo "document.getElementById('txt_machine_no').value 			= '" . $floor_arr[$row[csf("floor_id")]] . "';\n";
			echo "document.getElementById('txt_mc_group').value 			= '" . $row[csf("machine_group")] . "';\n";
		}
	}

	exit();
}
if ($action == "batch_number_popup") {
	echo load_html_head_contents("Batch Number Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	//echo $load_unload;
	?>
    <script>
        function js_set_value(id) {
            $('#hidden_batch_id').val(id);
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
                        <th colspan="4">
							<?
							echo create_drop_down("cbo_string_search_type", 130, $string_search_type, '', 1, "-- Searching Type --");
							?>
                        </th>
                    </tr>
                    <tr>
                        <th width="150px">Batch Type</th>
                        <th width="150px">Batch No</th>
                        <th width="220px" style="color:blue">Batch Date Range *</th>
                        <th>
                            <input type="reset" name="reset" id="reset" value="Reset" style="width:100px;"
                                   class="formbutton"/>
                            <input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes"
                                   value="<? echo $cbo_company_id; ?>">
                            <input type="hidden" name="hidden_batch_id" id="hidden_batch_id" class="text_boxes"
                                   value="">
                        </th>
                    </tr>
                    </thead>
                    <tr>
                        <td align="center">
							<?
							echo create_drop_down("cbo_batch_type", 150, $order_source, "", 0, "--Select--", 1, 0, 0);
							?>
                        </td>
                        <td align="center">
                            <input type="text" style="width:140px" class="text_boxes" name="txt_search_batch"
                                   id="txt_search_batch"/>
                        </td>
                        <td align="center">
                            <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker"
                                   style="width:80px;"> To <input type="text" name="txt_date_to" id="txt_date_to"
                                                                  class="datepicker" style="width:80px;">
                        </td>

                        <td align="center">
                            <input type="button" name="button2" class="formbutton" value="Show"
                                   onClick="show_list_view (document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('cbo_batch_type').value+'_'+document.getElementById('txt_search_batch').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+'<? echo $load_unload; ?>', 'create_batch_search_list_view', 'search_div', 'dyeing_production_controller', 'setFilterGrid(\'tbl_list_search\',-1);')"
                                   style="width:100px;"/>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" align="center" height="40"
                            valign="middle"><? echo load_month_buttons(1); ?></td>
                    </tr>
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
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
	<?
}
if ($action == "create_batch_search_list_view") {
	$data = explode("_", $data);
	$start_date = $data[0];
	$end_date = $data[1];
	$company_id = $data[2];
	$batch_type = $data[3];
	$batch_no = $data[4];
	$search_type = $data[5];
	$load_unload_id = $data[6];
	if ($batch_no == "" && $start_date == "" && $end_date == "") {
		echo "<p style='color:firebrick; text-align: center; font-weight: bold;'>Batch Date Range is required</p>";
		exit;
	}

	if ($search_type == 1) {
		if ($batch_no != '') $batch_cond = " and a.batch_no='$batch_no'"; else $batch_cond = "";
	} else if ($search_type == 4 || $search_type == 0) {
		if ($batch_no != '') $batch_cond = " and a.batch_no like '%$batch_no%'"; else $batch_cond = "";
	} else if ($search_type == 2) {
		if ($batch_no != '') $batch_cond = " and a.batch_no like '$batch_no%'"; else $batch_cond = "";
	} else if ($search_type == 3) {
		if ($batch_no != '') $batch_cond = " and a.batch_no like '%$batch_no'"; else $batch_cond = "";
	}
	if ($batch_type == 0)
		$search_field_cond_batch = "and a.entry_form in (0,36)";
	else if ($batch_type == 1)
		$search_field_cond_batch = "and a.entry_form=0";
	else if ($batch_type == 2)
		$search_field_cond_batch = "and a.entry_form=36";
	else if ($batch_type == 3)
		$search_field_cond_batch = "and a.entry_form=0 and a.batch_against=2";
	//echo $search_field_cond_batch;die;
	if ($db_type == 2) {
		if ($start_date != "" && $end_date != "") $batch_date_con = " and a.batch_date between '" . change_date_format($start_date, "mm-dd-yyyy", "-", 1) . "' and '" . change_date_format($end_date, "mm-dd-yyyy", "-", 1) . "'"; else $batch_date_con = "";

		if ($batch_type == 0 || $batch_type == 2) {

			$sql_po = sql_select("select b.mst_id, a.job_no_mst,listagg(cast(a.order_no as varchar2(4000)),',') within group (order by a.order_no) as po_no from  subcon_ord_dtls a, pro_batch_create_dtls b where a.id=b.po_id and  b.status_active=1 and b.is_deleted=0 group by b.mst_id,a.job_no_mst");
		} else {
			$sql_po = sql_select("select b.mst_id, a.job_no_mst,listagg(cast(a.po_number as varchar2(4000)),',') within group (order by a.po_number) as po_no from wo_po_break_down a, pro_batch_create_dtls b where a.id=b.po_id and  b.status_active=1 and b.is_deleted=0 group by b.mst_id,a.job_no_mst");
		}
	}
	if ($db_type == 0) {
		if ($start_date != "" && $end_date != "") $batch_date_con = " and a.batch_date between '" . change_date_format($start_date, "yyyy-mm-dd", "-", 1) . "' and '" . change_date_format($end_date, "yyyy-mm-dd", "-") . "'"; else $batch_date_con = "";

		if ($batch_type == 0 || $batch_type == 2) {
			$sql_po = sql_select("select b.mst_id, a.job_no_mst,group_concat( distinct a.order_no )  as po_no from  subcon_ord_dtls a, pro_batch_create_dtls b where a.id=b.po_id and  b.status_active=1 and b.is_deleted=0 group by b.mst_id,a.job_no_mst");
		} else {
			$sql_po = sql_select("select b.mst_id, a.job_no_mst,group_concat(distinct a.po_number)  as po_no from wo_po_break_down a, pro_batch_create_dtls b where a.id=b.po_id and  b.status_active=1 and b.is_deleted=0 group by b.mst_id,a.job_no_mst");
		}
	}
	$po_num = array();
	foreach ($sql_po as $row_po_no) {
		$po_num[$row_po_no[csf('mst_id')]]['po_no'] = $row_po_no[csf('po_no')];
		$po_num[$row_po_no[csf('mst_id')]]['job_no_mst'] = $row_po_no[csf('job_no_mst')];
	}

	$sql_sales_job=array();
	$sql_sales_job=sql_select("select b.job_no as job_no_mst,b.booking_no,f.job_no as sales_order_no,f.within_group from wo_booking_mst a, wo_booking_dtls b, wo_po_break_down c, FABRIC_SALES_ORDER_MST f where a.booking_no=b.booking_no and b.booking_no=f.SALES_BOOKING_NO and b.po_break_down_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.job_no,b.booking_no,f.job_no,f.within_group");

	foreach ($sql_sales_job as $sales_job_row) {
		$sales_job_arr[$sales_job_row[csf('booking_no')]]["job_no_mst"] = $sales_job_row[csf('job_no_mst')];
		$sales_job_arr[$sales_job_row[csf('booking_no')]]["sales_order_no"] = $sales_job_row[csf('sales_order_no')];
		$sales_job_arr[$sales_job_row[csf('booking_no')]]["within_group"] = $sales_job_row[csf('within_group')];
	}

	if ($load_unload_id == 1) {
		 $sql = "select a.id, a.double_dyeing,a.entry_form,a.batch_no, a.batch_date, a.batch_weight, a.booking_no, a.extention_no, a.color_id, a.batch_against, a.re_dyeing_from, a.is_sales,a.sales_order_no,a.sales_order_id from pro_batch_create_mst a where a.batch_for in(0,1,3) and a.batch_against<>4 and a.status_active=1 and a.is_deleted=0  $search_field_cond_batch $batch_date_con $batch_cond group by  a.id, a.double_dyeing,a.entry_form,a.batch_no, a.batch_date, a.batch_weight, a.booking_no, a.extention_no, a.color_id, a.batch_against, a.re_dyeing_from, a.is_sales,a.sales_order_no,a.sales_order_id order by a.id desc";
	} else {
		$sql = "select a.id,a.double_dyeing,a.entry_form, a.batch_no, a.batch_date, a.batch_weight, a.booking_no, a.extention_no, a.color_id, a.batch_against, a.re_dyeing_from,b.system_no,a.is_sales from pro_batch_create_mst a,pro_fab_subprocess b where a.id=b.batch_id and a.batch_for in(0,1,3) and a.batch_against<>4 and a.status_active=1 and a.is_deleted=0 and b.load_unload_id=1  $search_field_cond_batch $batch_date_con $batch_cond group by a.id,a.double_dyeing,a.entry_form, a.batch_no, a.batch_date, a.batch_weight, a.booking_no, a.extention_no, a.color_id, a.batch_against, a.re_dyeing_from,b.system_no,a.is_sales order by a.id desc";
	}
		$nameArray = sql_select($sql);
		foreach ($nameArray as $row)
		{
			$multi_dyeing_arr[$row[csf('id')]]= $row[csf('double_dyeing')];
		}
	//echo $sql;//die;
 $sql_load="select b.batch_id,b.result,b.load_unload_id from pro_batch_create_mst a,pro_fab_subprocess b where a.id=b.batch_id and b.load_unload_id in(1,2) and b.entry_form=35 and b.status_active=1  $search_field_cond_batch $batch_date_con $batch_cond  order by b.id desc";
	$result_load = sql_select($sql_load);
		foreach ($result_load as $row)
		{
			$multi_dyeing=$multi_dyeing_arr[$row[csf('batch_id')]];
			if($multi_dyeing==0 || $multi_dyeing==2) $multi_dyeing=2;else $multi_dyeing=$multi_dyeing;
			if($multi_dyeing==2) //Multi is no
			{
				if($load_unload==1)
				{
					$load_unload_arr[$row[csf('batch_id')]]= $row[csf('batch_id')];
					$load_unload_result_arr[$row[csf('batch_id')]]= $row[csf('result')];
				}
				else
				{
					$load_unload_arr[$row[csf('batch_id')]]= '';
					$load_unload_result_arr[$row[csf('batch_id')]]= $row[csf('result')];
				}
			}
		}

	?>
    <div>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="850" class="rpt_table">
            <thead>
            <th width="40">SL</th>
            <th width="100">Batch No</th>
            <th width="100">Functional Batch No</th>
            <th width="80">Extention No</th>
            <th width="80">Batch Date</th>
            <th width="90">Batch Qnty</th>
            <th width="115">Job No</th>
            <th width="80">Color</th>
            <th>Po/FSO No</th>
            </thead>
        </table>
        <div style="width:870px; overflow-y:scroll; max-height:230px;" id="buyer_list_view" align="center">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="850" class="rpt_table"
                   id="tbl_list_search">
				<?
				$i = 1;

				if (!empty($nameArray)) {
					foreach ($nameArray as $selectResult) {
						if ($i % 2 == 0)
							$bgcolor = "#E9F3FF";
						else
							$bgcolor = "#FFFFFF";
						$is_sales = $selectResult[csf('is_sales')];
						$within_group=$sales_job_arr[$selectResult[csf('booking_no')]]["within_group"];
						$po_no = '';
						if ($selectResult[csf('re_dyeing_from')] == 0 || $selectResult[csf('re_dyeing_from')]>0) {

						if($load_unload_arr[$selectResult[csf('id')]]=="")
						{
							if($is_sales == 1){
								if($within_group == 1){
									$po_no = $sales_job_arr[$selectResult[csf('booking_no')]]["sales_order_no"];
									$job_no= $sales_job_arr[$selectResult[csf('booking_no')]]["job_no_mst"];
								}else{
									$po_no = $sales_job_arr[$selectResult[csf('booking_no')]]["sales_order_no"];
									$job_no= "";}
							}else{
								$po_no = implode(",", array_unique(explode(",", $po_num[$selectResult[csf('id')]]['po_no'])));
								$job_no= $po_num[$selectResult[csf('id')]]['job_no_mst'];
							}
							?>
                            <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
                                onClick="js_set_value('<? echo $selectResult[csf('id')] . '_' . $selectResult[csf('batch_no')] . '_' . $selectResult[csf('system_no')]. '_' . $is_sales. '_' . $selectResult[csf('entry_form')]; ?>')">
                                <td width="40" align="center"><? echo $i; ?></td>
                                <td width="100"><p><? echo $selectResult[csf('batch_no')]; ?></p></td>
                                <td width="100"><p><? echo $selectResult[csf('system_no')]; ?></p></td>
                                <td width="80"><p><? /*if($selectResult[csf('extention_no')]!=0)*/
										echo $selectResult[csf('extention_no')]; ?></p></td>
                                <td width="80"><? echo change_date_format($selectResult[csf('batch_date')]); ?></td>
                                <td width="90" align="right"><? echo $selectResult[csf('batch_weight')]; ?></td>
                                <td width="115"><p><? echo $job_no; ?></p></td>
                                <td width="80"><p><? echo $color_arr[$selectResult[csf('color_id')]]; ?></p></td>
                                <td><? echo $po_no; ?></td>
                            </tr>
							<?
							$i++;
							}
						} else {
							if ($load_unload_id == 1) {
								$sql_re = "select a.id,a.entry_form, a.batch_no, a.batch_date, a.batch_weight, a.booking_no, MAX(a.extention_no) as extention_no, a.color_id, a.batch_against,a.is_sales,
							a.re_dyeing_from from pro_batch_create_mst a where  a.batch_for in(0,1) and a.entry_form in(0,36,136) and a.batch_against<>4 and a.status_active=1 and
							a.is_deleted=0 and a.id='" . $selectResult[csf('re_dyeing_from')] . "'  group by a.id,a.entry_form, a.batch_no, a.batch_date, a.batch_weight, a.booking_no,a.color_id, a.batch_against, a.re_dyeing_from,a.is_sales  ";
							} else {
								$sql_re = "select a.id,a.entry_form, a.batch_no, a.batch_date, a.batch_weight, a.booking_no, MAX(a.extention_no) as extention_no, a.color_id, a.batch_against,b.system_no,a.is_sales,
							a.re_dyeing_from from pro_batch_create_mst a,pro_fab_subprocess b where a.id=b.batch_id and  a.batch_for in(0,1) and a.entry_form in(0,36,136) and a.batch_against<>4 and a.status_active=1 and
							a.is_deleted=0 and a.id='" . $selectResult[csf('re_dyeing_from')] . "'  group by a.id,a.entry_form,b.system_no,a.batch_no, a.batch_date, a.batch_weight, a.booking_no,a.color_id,b.system_no,a.batch_against,b.re_dyeing_from,a.is_sales  ";
							}
							$dataArray = sql_select($sql_re);

							foreach ($dataArray as $row) {
								$is_sales = $row[csf('is_sales')];
								$within_group=$sales_job_arr[$row[csf('booking_no')]]["within_group"];
								$po_no = '';
							if($load_unload_arr[$selectResult[csf('id')]]=="")
							{
								if ($row[csf('re_dyeing_from')] == 0) {
									if($is_sales == 1){
										if($within_group == 1){
											$po_no = $sales_job_arr[$row[csf('booking_no')]]["sales_order_no"];
											$job_no= $sales_job_arr[$row[csf('booking_no')]]["job_no_mst"];
										}else{
											$po_no = $sales_job_arr[$row[csf('booking_no')]]["sales_order_no"];
											$job_no= "";}
									}else{
										$po_no = implode(",", array_unique(explode(",", $po_num[$selectResult[csf('id')]]['po_no'])));
										$job_no= $po_num[$selectResult[csf('id')]]['job_no_mst'];
									}
									?>
                                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
                                        onClick="js_set_value('<? echo $selectResult[csf('id')] . '_' . $selectResult[csf('batch_no')] . '_' . $selectResult[csf('system_no')]. '_' . $is_sales. '_' . $selectResult[csf('entry_form')]; ?>')">
                                        <td width="40" align="center"><? echo $i; ?></td>
                                        <td width="100"><p><? echo $selectResult[csf('batch_no')]; ?></p></td>
                                        <td width="100"><p><? echo $selectResult[csf('system_no')]; ?></p></td>
                                        <td width="80">
                                            <p><? if ($selectResult[csf('extention_no')] != 0) echo $selectResult[csf('extention_no')]; ?></p>
                                        </td>
                                        <td width="80"><? echo change_date_format($selectResult[csf('batch_date')]); ?></td>
                                        <td width="90" align="right"><? echo $selectResult[csf('batch_weight')]; ?></td>

                                        <td width="115">
                                            <p><? echo $po_num[$selectResult[csf('id')]]['job_no_mst']; ?></p>
                                        </td>
                                        <td width="80"><p><? echo $color_arr[$selectResult[csf('color_id')]]; ?></p>
                                        </td>
                                        <td><? echo $po_no; ?></td>
                                    </tr>
									<?
									$i++;
									}
								}
							}
						}
					}
				} else {
					echo "<tr><td colspan='9'><p style='color:firebrick; text-align: center; font-weight: bold;'>No Data Found</p></td></tr>";
				}
				?>
            </table>
        </div>
    </div>
	<?
	exit();
}
if ($action == 'populate_data_from_batch') {
	$ex_data = explode('_', $data);
	$load_unload = $ex_data[0];
	$batch_id = $ex_data[1];
	$batch_no = $ex_data[2];
	$entry_form = $ex_data[4];
	//echo $entry_form.'DDD';
	//$is_sales = $ex_data[3];
	$double_dyeing=0;
	$batch_data =sql_select("select company_id,entry_form,double_dyeing from pro_batch_create_mst where id ='$batch_id' and is_deleted=0 and status_active=1");
	foreach ($batch_data as $row)
	{
		$company_id = $row[csf('company_id')];
		$entry_form_id= $row[csf('entry_form')];
		$double_dyeing = $row[csf('double_dyeing')];
	}
	//echo $entry_form_id.'TTTTTTTTTTTTTT';

	if($double_dyeing==0 || $double_dyeing==2) $multi_dyeing=2;else $multi_dyeing=$double_dyeing;
	//echo $company_id.'=='.$double_dyeing;
	//$company_id = return_field_value("company_id", "pro_batch_create_mst", "id ='$batch_id' and is_deleted=0 and status_active=1");

	$roll_maintained = return_field_value("fabric_roll_level", "variable_settings_production", "company_name ='$company_id' and item_category_id=50 and variable_list=3 and is_deleted=0 and status_active=1");
	$page_upto_id = return_field_value("page_upto_id", "variable_settings_production", "company_name ='$company_id' and item_category_id=50 and variable_list=3 and is_deleted=0 and status_active=1");

	$sales_job_arr=array();$load_arr=array();
	$last_load="select batch_id,result,load_unload_id from pro_fab_subprocess where load_unload_id in(1,2) and entry_form=35 and status_active=1 and batch_id=$batch_id  order by id desc";
	$last_data=sql_select($last_load,1);
	$result_id=$load_unload_id=0;
	foreach ($last_data as $row)
	{
		//if($row[csf('load_unload_id')]==2)
			//{
				 $result_id= $row[csf('result')];
				 $load_unload_id= $row[csf('load_unload_id')];
			//}
			
	}
	//echo $result_id.'DDD'.$load_unload_id;;
	//echo "select batch_id,result from pro_fab_subprocess where load_unload_id in(1,2) and entry_form=35 and status_active=1 and batch_id=$batch_id  order by id desc";
	$sql_load=sql_select("select batch_id,result,load_unload_id from pro_fab_subprocess where load_unload_id in(1,2) and entry_form=35 and status_active=1 and batch_id=$batch_id  order by id desc");
	if($multi_dyeing==2) //Multi is no
	{
		foreach ($sql_load as $row)
		{
			if($load_unload==1)
			{
				$load_unload_arr[$row[csf('batch_id')]]= $row[csf('batch_id')];
				$load_unload_result_arr[$row[csf('batch_id')]]= $row[csf('result')];
			}
			else
			{
				$load_unload_arr[$row[csf('batch_id')]]= '';
				$load_unload_result_arr[$row[csf('batch_id')]]= $row[csf('result')];
			}
		}
	}

		$sql_sales_job=sql_select("select f.buyer_id,f.po_buyer,a.booking_no,f.booking_without_order,f.job_no as sales_order_no,f.within_group from pro_batch_create_mst a,fabric_sales_order_mst f where a.sales_order_no=f.job_no and a.status_active=1 and a.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and a.id=$batch_id  group by f.buyer_id,a.booking_no,f.job_no,f.within_group,f.booking_without_order,f.po_buyer");
	//echo "select f.buyer_id,a.booking_no,f.job_no as sales_order_no,f.within_group from pro_batch_create_mst a,fabric_sales_order_mst f where a.sales_order_no=f.job_no and a.status_active=1 and a.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and a.id=$batch_id  group by f.buyer_id,a.booking_no,f.job_no,f.within_group";

	foreach ($sql_sales_job as $sales_job_row) {
		$sales_order_no=$sales_job_row[csf('sales_order_no')];
		//$sales_job_arr[$booking_no]["job_no_mst"] = $sales_job_row[csf('job_no_mst')];
		$sales_job_arr[$sales_order_no]["sales_order_no"] = $sales_job_row[csf('sales_order_no')];
		$sales_job_arr[$sales_order_no]["buyer_id"] = $sales_job_row[csf('buyer_id')];
		$sales_job_arr[$sales_order_no]["within_group"] = $sales_job_row[csf('within_group')];
		$sales_job_arr[$sales_order_no]["po_buyer"] = $sales_job_row[csf('po_buyer')];
		$sales_job_arr[$sales_order_no]["booking_without_order"] = $sales_job_row[csf('booking_without_order')];
	}

	//$ltb_btb = array(1 => 'BTB', 2 => 'LTB');
	if ($db_type == 0) $select_field1 = "order by a.id";
	else if ($db_type == 2) $select_field1 = " group by a.id,a.sales_order_no,a.double_dyeing,a.batch_no,a.batch_weight,a.color_id,a.company_id,a.working_company_id,a.process_id, a.booking_without_order,a.entry_form,a.booking_no,b.is_sales order by a.id";
	if ($db_type == 0) $select_list = "group_concat(distinct(b.po_id)) as po_id";
	else if ($db_type == 2) $select_list = " listagg(b.po_id,',') within group (order by b.po_id) as po_id";
	//echo $entry_form.'XXX';
	//$booking_id=implode(",",array_unique(explode(",",$booking_id)));
	if ($load_unload == 1) {
	//and a.id not in(select batch_id from pro_fab_subprocess where load_unload_id in(1) and entry_form=35 and status_active=1)
		if ($batch_no != '') {
			if($entry_form!=136)
			{
			$sql_re = "select a.id as id,a.double_dyeing,a.batch_no,b.is_sales, a.entry_form,a.company_id,a.working_company_id, a.batch_weight,Max(a.extention_no) as extention_no,a.process_id as process_id_batch, a.color_id, a.booking_without_order,a.booking_no, a.sales_order_no,
				sum(b.batch_qnty) as batch_qnty,  $select_list from pro_batch_create_mst a,pro_batch_create_dtls b where
				a.id='$batch_id'  and a.entry_form in(0,36) and a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1  and a.is_deleted=0  $select_field1";
			}
			else //For Trim Batch
			{
				 $sql_re = "select a.id as id,a.double_dyeing,a.batch_no,a.job_no,0 as is_sales, a.entry_form,a.company_id,a.working_company_id, a.batch_weight,Max(a.extention_no) as extention_no,a.process_id as process_id_batch, a.color_id, a.booking_without_order,a.booking_no, a.sales_order_no,
				sum(b.trims_wgt_qnty) as batch_qnty from pro_batch_create_mst a,pro_batch_trims_dtls b where
				a.id='$batch_id'  and a.entry_form in(136) and a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1  and a.is_deleted=0  group by a.id,a.double_dyeing ,a.batch_no,a.entry_form,a.company_id,a.working_company_id, a.batch_weight,a.process_id ,a.color_id,a.booking_without_order,a.job_no,a.booking_no,a.sales_order_no";
			}
		} else
		 {
			if($entry_form!=136)
			{
			$sql_re = "select a.id as id,a.double_dyeing,a.batch_no,b.is_sales,a.entry_form,a.company_id,a.working_company_id, a.batch_weight,Max(a.extention_no) as extention_no,a.process_id as process_id_batch, a.color_id, a.booking_without_order, a.booking_no,a.sales_order_no,
				sum(b.batch_qnty) as batch_qnty, $select_list  from pro_batch_create_mst a,pro_batch_create_dtls b where
				a.id='$batch_id' and a.entry_form in(0,36) and a.id=b.mst_id  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0   $select_field1";
			}
			else //For Trim Batch
			{
				$sql_re = "select a.id as id,a.double_dyeing,a.batch_no,a.job_no,0 as is_sales, a.entry_form,a.company_id,a.working_company_id, a.batch_weight,Max(a.extention_no) as extention_no,a.process_id as process_id_batch, a.color_id, a.booking_without_order,a.booking_no, a.sales_order_no,
				sum(b.trims_wgt_qnty) as batch_qnty from pro_batch_create_mst a,pro_batch_trims_dtls b where
				a.id='$batch_id'  and a.entry_form in(136) and a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1  and a.is_deleted=0  group by a.id,a.double_dyeing ,a.batch_no,a.entry_form,a.company_id,a.working_company_id, a.batch_weight,a.process_id ,a.color_id,a.booking_without_order,a.booking_no,a.sales_order_no,a.job_no";
			}
		}
	} else {
		if ($batch_no != '')
		{
			//and a.id not in(select batch_id from pro_fab_subprocess where load_unload_id in(2) and  entry_form=35 and status_active=1)
			if($entry_form!=136)
			{
			$sql_re = "select a.id as id,a.double_dyeing,a.batch_no,b.is_sales,a.entry_form,a.company_id,a.working_company_id, a.batch_weight,Max(a.extention_no) as extention_no,a.process_id as process_id_batch, a.color_id, a.booking_without_order, a.booking_no,a.sales_order_no,
				sum(b.batch_qnty) as batch_qnty,  $select_list from pro_batch_create_mst a,pro_batch_create_dtls b where
				a.id='$batch_id' and a.entry_form in(0,36) and a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1  and a.is_deleted=0  $select_field1";
			}
			else
			{
				  $sql_re = "select a.id as id,a.double_dyeing,a.batch_no,a.job_no,0 as is_sales, a.entry_form,a.company_id,a.working_company_id, a.batch_weight,Max(a.extention_no) as extention_no,a.process_id as process_id_batch, a.color_id, a.booking_without_order,a.booking_no, a.sales_order_no,
				sum(b.trims_wgt_qnty) as batch_qnty from pro_batch_create_mst a,pro_batch_trims_dtls b where
				a.id='$batch_id'  and a.entry_form in(136) and a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1  and a.is_deleted=0  group by a.id ,a.double_dyeing,a.batch_no,a.entry_form,a.company_id,a.working_company_id, a.batch_weight,a.process_id ,a.color_id,a.booking_without_order,a.booking_no,a.sales_order_no,a.job_no";
			}

		}
		else
		{
			if($entry_form!=136)
			{
				$sql_re = "select a.id as id,a.double_dyeing,a.batch_no,b.is_sales,a.entry_form,a.company_id,a.working_company_id, a.batch_weight,Max(a.extention_no) as extention_no,a.process_id as process_id_batch, a.color_id, a.booking_without_order, a.booking_no,a.sales_order_no,
				sum(b.batch_qnty) as batch_qnty, $select_list  from pro_batch_create_mst a,pro_batch_create_dtls b where
				a.id='$batch_id' and a.entry_form in(0,36) and a.id=b.mst_id  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0    $select_field1";
			}
			else //For Trim Batch
			{
				  $sql_re = "select a.id as id,a.double_dyeing,a.batch_no,a.job_no,a.is_sales, a.entry_form,a.company_id,a.working_company_id, a.batch_weight,Max(a.extention_no) as extention_no,a.process_id as process_id_batch, a.color_id, a.booking_without_order,a.booking_no, a.sales_order_no,
				sum(b.trims_wgt_qnty) as batch_qnty from pro_batch_create_mst a,pro_batch_trims_dtls b where
				a.id='$batch_id'  and a.entry_form in(136) and a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1  and a.is_deleted=0  group by a.id,a.double_dyeing ,a.batch_no,a.entry_form,a.company_id,a.working_company_id, a.batch_weight,a.process_id ,a.color_id,a.booking_without_order,a.booking_no,a.sales_order_no,a.job_no";
			}
		}
	}
	//echo $sql_re;die;
	$data_array = sql_select($sql_re);

	if ($db_type == 0) $select_f_group = "";
	else if ($db_type == 2) $select_f_group = "group by a.job_no_mst, b.buyer_name";
	if ($db_type == 0) $select_listagg = "group_concat(distinct(a.po_number)) as po_no,group_concat(distinct(a.file_no)) as file_no,group_concat(distinct(a.grouping)) as ref_no";
	else if ($db_type == 2) $select_listagg = "listagg(cast(a.po_number as varchar(500)),',') within group (order by a.po_number) as po_no,listagg(cast(a.file_no as varchar(500)),',') within group (order by a.file_no) as file_no,listagg(cast(a.grouping as varchar(500)),',') within group (order by a.grouping) as ref_no";

	if ($db_type == 0) $select_listagg_subcon = "group_concat(distinct(a.order_no)) as po_no";
	else if ($db_type == 2) $select_listagg_subcon = "listagg(cast(a.order_no as varchar2(4000)),',') within group (order by a.order_no) as po_no";

	// if ($db_type == 0) $select_listagg_subcon = "group_concat(distinct(a.order_no)) as po_no";
	// else if ($db_type == 2) $select_listagg_subcon = "listagg(cast(a.order_no as varchar2(4000)),',') within group (order by a.order_no) as po_no";

	foreach ($data_array as $row) {
	//echo $load_unload_arr[$row[csf("id")]].'XXXXXXXXXXX';
	echo "document.getElementById('hidden_double_dyeing').value 	= '" . $row[csf("double_dyeing")] . "';\n";
	echo "document.getElementById('hidden_result_id').value 	= '" .$result_id . "';\n";
	echo "document.getElementById('hidden_last_loadunload_id').value 	= '" . $load_unload_id . "';\n";

	if($load_unload_arr[$row[csf("id")]]=="")
	{
		$salesOrder = $row[csf('is_sales')];
		if($entry_form==36)
		{
			$pro_id = implode(",", array_unique(explode(",", $row[csf('po_id')])));
			$pro_cond=" and a.id in(" . $pro_id . ")";
		}
		else if($entry_form==0)
		{
			$pro_id = implode(",", array_unique(explode(",", $row[csf('po_id')])));
			$pro_cond=" and a.id in(".$pro_id.")";
		}
		else
		{
			$job_no = $row[csf('job_no')];
			$pro_cond=" and a.job_no_mst in('".$job_no."')";
		}
		$inhouse=1;
		if($entry_form_id==36) $row[csf("working_company_id")]=$row[csf("company_id")];
		else  $row[csf("working_company_id")]=$row[csf("working_company_id")];
		
		echo "document.getElementById('cbo_company_id').value 			= '" . $row[csf("company_id")] . "';\n";
		echo "document.getElementById('cbo_service_source').value 			= '" . $inhouse . "';\n";
		echo "load_drop_down( 'requires/dyeing_production_controller', " . $inhouse . "+'**'+" . $row[csf("working_company_id")] . ", 'load_drop_down_knitting_com', 'dyeing_company_td' );\n";
		echo "document.getElementById('txt_hidden_service_company').value 	= '" . $row[csf("working_company_id")] . "';\n";
		echo "document.getElementById('cbo_service_company').value 			= '" . $row[csf("working_company_id")] . "';\n";
		echo "load_drop_down( 'requires/dyeing_production_controller', '" . $row[csf("working_company_id")] . "', 'load_drop_floor', 'floor_td' );\n";
		
		echo "$('#cbo_service_source').attr('disabled',true);\n";
		echo "$('#cbo_service_company').attr('disabled',true);\n";

		echo "document.getElementById('roll_maintained').value 			= '" . $roll_maintained . "';\n";
		echo "document.getElementById('page_upto').value 				= '" . $page_upto_id . "';\n";


		echo "document.getElementById('txt_entry_form_no').value 			= '" . $row[csf("entry_form")] . "';\n";
		echo "document.getElementById('hidden_batch_id').value 			= '" . $row[csf("id")] . "';\n";
		echo "document.getElementById('txt_batch_ID').value 			= '" . $row[csf("id")] . "';\n";
		echo "document.getElementById('txt_color').value 				= '" . $color_arr[$row[csf("color_id")]] . "';\n";
		echo "document.getElementById('txt_ext_id').value 				= '" . $row[csf("extention_no")] . "';\n";

		if (($page_upto_id == 2 || $page_upto_id > 2) && $roll_maintained == 1) {
			echo "$('#txt_issue_chalan').attr('disabled',false);\n";
		}
		if ($row[csf("entry_form")] == 36) {

			$batch_type = "<b> SUBCONTRACT ORDER BATCH</b>";
			$result_job = sql_select("select $select_listagg_subcon, b.subcon_job as job_no_mst, b.party_id as buyer_name from  subcon_ord_dtls a,
				subcon_ord_mst b where a.job_no_mst=b.subcon_job  and b.status_active=1 and b.is_deleted=0 and a.status_active=1
				and a.is_deleted=0 $pro_cond group by b.subcon_job, b.party_id");
		}
		 else {
			$batch_type = "<b> SELF ORDER BATCH </b>";
			$result_job = sql_select("select $select_listagg, a.job_no_mst, b.buyer_name from wo_po_break_down a,
				wo_po_details_master b where a.job_no_mst=b.job_no  and b.status_active=1 and b.is_deleted=0 and a.status_active=1
				and a.is_deleted=0  $pro_cond $select_f_group");
		}
		echo "document.getElementById('batch_type').innerHTML 			= '" . $batch_type . "';\n";

		$process_name_batch = '';
		$process_id_array = explode(",", $row[csf("process_id_batch")]);
		foreach ($process_id_array as $val) {
			if ($process_name_batch == "") $process_name_batch = $conversion_cost_head_array[$val]; else $process_name_batch .= "," . $conversion_cost_head_array[$val];
		}
		$process_ids = explode(",", $row[csf("process_id_batch")]);
		//$procssid=array(1=>'31');
		//print_r($process_ids);
		$procssid = in_array(31, $process_ids);
		//echo $procssid;
		if ($procssid == 31) {
			echo "document.getElementById('txt_process_id').value 			= '31';\n";
		} else {
			echo "document.getElementById('txt_process_id').value 			= '0';\n";
		}

		//echo "document.getElementById('txt_process_id').value 			= '31';\n";

		//echo "document.getElementById('txt_process_name').value 			= '".$process_name_batch."';\n";

		$pro_id2 = implode(",", array_unique(explode(",", $result_job[0][csf("po_no")])));
		$file_no = implode(",", array_unique(explode(",", $result_job[0][csf("file_no")])));
		$ref_no = implode(",", array_unique(explode(",", $result_job[0][csf("ref_no")])));
		$within_group=$sales_job_arr[$row[csf('sales_order_no')]]["within_group"];

		if ($salesOrder == 1) {
			if($within_group == 1){
			$po_buyer=$sales_job_arr[$row[csf('sales_order_no')]]["po_buyer"];
			$booking_without_order=$sales_job_arr[$row[csf('sales_order_no')]]["booking_without_order"];
			if($booking_without_order==0)
			{
				$job_nos = return_field_value("b.job_no as job_no", "wo_booking_mst a,wo_booking_dtls b", "a.booking_no=b.booking_no and b.booking_no ='".$row[csf('booking_no')]."' and b.is_deleted=0 and b.status_active=1 group by b.job_no","job_no");
			}
			//echo $job_nos.'DDD';
			echo "document.getElementById('txt_buyer').value 			= '" . $buyer_arr[$po_buyer] . "';\n";
			echo "document.getElementById('txt_job_no').value 			= '" .$job_nos . "';\n";
			echo "document.getElementById('txt_order_no').value 		= '" . $row[csf('sales_order_no')] . "';\n";
			}else{
			echo "document.getElementById('txt_buyer').value 			= '" . $buyer_arr[$sales_job_arr[$row[csf('sales_order_no')]]["buyer_id"]] . "';\n";
			echo "document.getElementById('txt_job_no').value 			= '';\n";
			echo "document.getElementById('txt_order_no').value 		= '" . $row[csf('sales_order_no')] . "';\n";
			}
		}else{
			echo "document.getElementById('txt_buyer').value 			= '" . $buyer_arr[$result_job[0][csf("buyer_name")]] . "';\n";
			echo "document.getElementById('txt_job_no').value 			= '" . $result_job[0][csf("job_no_mst")] . "';\n";
			echo "document.getElementById('txt_order_no').value 		= '" . $pro_id2 . "';\n";
		}
			echo "document.getElementById('txt_file').value 			= '" . $file_no . "';\n";
			echo "document.getElementById('txt_ref').value 			= '" . $ref_no . "';\n";
		$sql_batch_d = sql_select("select id,service_source,system_no,service_company,received_chalan,issue_chalan,issue_challan_mst_id,company_id,batch_id,batch_no,process_end_date,end_hours,end_minutes,machine_id,floor_id,process_id,ltb_btb_id,remarks,dyeing_type_id,hour_load_meter from
			pro_fab_subprocess where batch_id='$batch_id' and entry_form=35 and load_unload_id=1 and status_active=1 and is_deleted=0 ");
		foreach ($sql_batch_d as $dyeing_d) {//$minute=str_pad($r_batch[csf("end_minutes")],2,'0',STR_PAD_LEFT);
			echo "document.getElementById('txt_dying_started').value = '" . change_date_format($dyeing_d[csf("process_end_date")]) . "';\n";
			echo "document.getElementById('txt_dying_end_load').value = '" . str_pad($dyeing_d[csf("end_hours")], 2, '0', STR_PAD_LEFT) . ':' . str_pad($dyeing_d[csf("end_minutes")], 2, '0', STR_PAD_LEFT) . "';\n";
			echo "$('#txt_issue_chalan').val('" . $dyeing_d[csf("issue_chalan")] . "');\n";
			echo "$('#cbo_service_source').val(" . $dyeing_d[csf("service_source")] . ");\n";
			echo "document.getElementById('txt_recevied_chalan').value	= '" . $dyeing_d[csf('received_chalan')] . "';\n";
			echo "document.getElementById('txt_system_no').value	= '" . $dyeing_d[csf('system_no')] . "';\n";
			echo "load_drop_down( 'requires/dyeing_production_controller', " . $dyeing_d[csf("service_source")] . "+'**'+" . $dyeing_d[csf("company_id")] . ", 'load_drop_down_knitting_com', 'dyeing_company_td' );\n";
			echo "$('#cbo_service_company').val(" . $dyeing_d[csf("service_company")] . ");\n";
			echo "load_drop_down( 'requires/dyeing_production_controller', '" . $dyeing_d[csf("service_company")] . "', 'load_drop_floor', 'floor_td' );\n";
			echo "document.getElementById('txt_ltb_btb').value	= '" . $ltb_btb[$dyeing_d[csf("ltb_btb_id")]] . "';\n";
			if ($load_unload == 2) {
				echo "process_check('31');\n";
			}
		}
		//exit();
		}// Check Load/Unload
	}
	if ($db_type == 0) $select_group_row1 = " order by id desc limit 0,1";
	else if ($db_type == 2) $select_group_row1 = " and  rownum>=1 order by id desc";//order by id desc limit 0,1
	if ($load_unload == 1) {

		$sql_batch = sql_select(
			"select id,batch_no,company_id,system_no,batch_id,service_source,service_company,received_chalan,issue_chalan,issue_challan_mst_id,process_end_date,load_unload_id,end_hours,end_minutes,machine_id,floor_id,process_id,
			ltb_btb_id,water_flow_meter,result,remarks,dyeing_type_id,hour_load_meter,multi_batch_load_id	from pro_fab_subprocess where batch_id='$batch_id' and entry_form=35 and load_unload_id in(1) and status_active=1 and is_deleted=0  ");
	} else if ($load_unload == 2) {
		$sql_batch = sql_select("select id,batch_no,company_id,system_no,service_source,service_company,received_chalan,issue_chalan,issue_challan_mst_id,batch_id,process_end_date,load_unload_id,end_hours,end_minutes,machine_id,floor_id,process_id,ltb_btb_id,water_flow_meter,result,remarks,dyeing_type_id,hour_unload_meter,shift_name,fabric_type,production_date from pro_fab_subprocess where batch_id='$batch_id' and entry_form=35 and load_unload_id in(2,1) and status_active=1 and is_deleted=0 $select_group_row1");
	}
	foreach ($sql_batch as $r_batch) {
		if ($load_unload == 1) //Load
		{
			if ($r_batch[csf('load_unload_id')] == 1) {
				//echo "document.getElementById('txt_update_id').value 				= '" . $r_batch[csf("id")] . "';\n";
				echo "document.getElementById('txt_update_id').value 				= '';\n";
			} else {
				echo "document.getElementById('txt_update_id').value 				= '';\n";
			}
			$process_name = '';
			$process_id_array = explode(",", $r_batch[csf("process_id")]);
			foreach ($process_id_array as $val) {
				if ($process_name == "") $process_name = $conversion_cost_head_array[$val]; else $process_name .= "," . $conversion_cost_head_array[$val];
			}

			echo "document.getElementById('cbo_company_id').value 			= '" . $r_batch[csf("company_id")] . "';\n";
			echo "document.getElementById('txt_batch_no').value 			= '" . $r_batch[csf("batch_no")] . "';\n";
			echo "document.getElementById('txt_process_start_date').value 		= '" . change_date_format($r_batch[csf("process_end_date")]) . "';\n";
			// echo "document.getElementById('txt_system_no').value 			= '".$r_batch[csf("system_no")]."';\n";
			//echo "document.getElementById('cbo_sub_process').value 				= '".$r_batch[csf("process_id")]."';\n";
			echo "document.getElementById('txt_process_id').value 				= '" . $r_batch[csf("process_id")] . "';\n";
			//echo "document.getElementById('txt_process_name').value 			= '".$process_name."';\n";
			//echo "set_multiselect('cbo_sub_process','0','1','".$r_batch[csf('process_id')]."','0');\n";
			echo "load_drop_down( 'requires/dyeing_production_controller', '" . $r_batch[csf("service_company")] . "', 'load_drop_floor', 'floor_td' );\n";
			echo "document.getElementById('cbo_ltb_btb').value	= '" . $r_batch[csf("ltb_btb_id")] . "';\n";
			echo "document.getElementById('txt_load_meter').value	= '" . $r_batch[csf("hour_load_meter")] . "';\n";
			echo "document.getElementById('txt_water_flow').value	= '" . $r_batch[csf("water_flow_meter")] . "';\n";
			echo "document.getElementById('cbo_yesno').value	= '" . $r_batch[csf("multi_batch_load_id")] . "';\n";
			$minute = str_pad($r_batch[csf("end_minutes")], 2, '0', STR_PAD_LEFT);
			$hour = str_pad($r_batch[csf("end_hours")], 2, '0', STR_PAD_LEFT);
			echo "document.getElementById('txt_start_minutes').value	= '" . $minute . "';\n";
			echo "document.getElementById('txt_start_hours').value	= '" . $hour . "';\n";
			echo "$('#txt_issue_chalan').val('" . $r_batch[csf("issue_chalan")] . "');\n";
			echo "$('#cbo_service_source').val(" . $r_batch[csf("service_source")] . ");\n";
			echo "document.getElementById('txt_recevied_chalan').value	= '" . $r_batch[csf('received_chalan')] . "';\n";
			echo "load_drop_down( 'requires/dyeing_production_controller', " . $r_batch[csf("service_source")] . "+'**'+" . $r_batch[csf("company_id")] . ", 'load_drop_down_knitting_com', 'dyeing_company_td' );\n";
			echo "$('#cbo_service_company').val(" . $r_batch[csf("service_company")] . ");\n";
			echo "document.getElementById('cbo_floor').value = '" . $r_batch[csf("floor_id")] . "';\n";

			echo "load_drop_down( 'requires/dyeing_production_controller', document.getElementById('cbo_service_company').value+'**'+" . $r_batch[csf("floor_id")] . ", 'load_drop_machine', 'machine_td' );\n";

			echo "document.getElementById('cbo_machine_name').value = '" . $r_batch[csf("machine_id")] . "';\n";
			echo "document.getElementById('txt_remarks').value	= '" . $r_batch[csf("remarks")] . "';\n";
			//echo "document.getElementById('cbo_dyeing_type').value	= '" . $r_batch[csf("dyeing_type_id")] . "';\n";
			if ($r_batch[csf("id")] != 0) {
				//echo "document.getElementById('txt_system_no').value 			= '".$r_batch[csf("system_no")]."';\n";

				echo "set_button_status(0, '" . $_SESSION['page_permission'] . "', 'fnc_pro_fab_subprocess',0);\n";
			} else {
				//echo "document.getElementById('txt_system_no').value 			= '';\n";

				echo "set_button_status(0, '" . $_SESSION['page_permission'] . "', 'fnc_pro_fab_subprocess',0);\n";
			}
		}
		else if ($load_unload == 2) //Unload
		{
			if ($r_batch[csf("load_unload_id")] == 2) {
				echo "document.getElementById('cbo_company_id').value 			= '" . $r_batch[csf("company_id")] . "';\n";
				echo "document.getElementById('txt_update_id').value 		= '';\n";
				//echo "document.getElementById('txt_update_id').value 		= '" . $r_batch[csf("id")] . "';\n";
				// echo "document.getElementById('txt_system_no').value 			= '".$row[csf("system_no")]."';\n";
				echo "document.getElementById('txt_batch_no').value 			= '" . $r_batch[csf("batch_no")] . "';\n";
				echo "document.getElementById('txt_process_end_date').value = '" . ($r_batch[csf("load_unload_id")] == 1 ? "" : change_date_format($r_batch[csf("process_end_date")])) . "';\n";
				echo "document.getElementById('txt_process_date').value = '" . ($r_batch[csf("load_unload_id")] == 1 ? "" : change_date_format($r_batch[csf("production_date")])) . "';\n";
				echo "document.getElementById('cbo_shift_name').value	= '" . $r_batch[csf("shift_name")] . "';\n";
				echo "document.getElementById('cbo_fabric_type').value	= '" . $r_batch[csf("fabric_type")] . "';\n";
				echo "document.getElementById('txt_unload_meter').value	= '" . $r_batch[csf("hour_unload_meter")] . "';\n";
				echo "document.getElementById('txt_water_flow').value	= '" . ($r_batch[csf("load_unload_id")] == 1 ? "" : $r_batch[csf("water_flow_meter")]) . "';\n";
				echo "document.getElementById('cbo_ltb_btb').value	= '" . $r_batch[csf("ltb_btb_id")] . "';\n";
			}
			//$process_name=$r_batch[csf('process_id')];
			//echo "document.getElementById('cbo_sub_process').value 		= '".$r_batch[csf("process_id")]."';\n";
			//echo "set_multiselect('cbo_sub_process','0','1','".$r_batch[csf("process_id")]."','0');\n";
			$process_name = '';
			$process_id_array = explode(",", $r_batch[csf("process_id")]);
			foreach ($process_id_array as $val) {
				if ($process_name == "") $process_name = $conversion_cost_head_array[$val]; else $process_name .= "," . $conversion_cost_head_array[$val];
			}
			//echo "load_drop_down( 'requires/dyeing_production_controller', '".$r_batch[csf("process_id")]."', 'load_drop_sub_process', 'sub_process_td' );\n";
			// echo "set_multiselect('cbo_sub_process','0','0','0','0');\n";
			//echo "set_multiselect('cbo_sub_process','0','1','".$r_batch[csf('process_id')]."','0');\n";
			//echo "document.getElementById('txt_end_minutes').value	= '".($r_batch[csf("load_unload_id")] == 1 ? "" : $r_batch[csf("end_minutes")])."';\n";
			//echo "document.getElementById('txt_end_hours').value	= '".($r_batch[csf("load_unload_id")] == 1 ? "" : $r_batch[csf("end_hours")])."';\n";
			echo "load_drop_down( 'requires/dyeing_production_controller', '" . $r_batch[csf("service_company")] . "', 'load_drop_floor', 'floor_td' );\n";
			echo "document.getElementById('txt_process_id').value 				= '" . $r_batch[csf("process_id")] . "';\n";
			echo "process_check('" . $r_batch[csf("process_id")] . "');\n";
			//echo "document.getElementById('txt_process_name').value 				= '".$process_name."';\n";
			if ($r_batch[csf("load_unload_id")] == 2) {
				$minute = str_pad($r_batch[csf("end_minutes")], 2, '0', STR_PAD_LEFT);
				$hour = str_pad($r_batch[csf("end_hours")], 2, '0', STR_PAD_LEFT);
				echo "document.getElementById('txt_end_minutes').value	= '" . $minute . "';\n";
				echo "document.getElementById('txt_end_hours').value	= '" . $hour . "';\n";
			}
			echo "$('#txt_issue_chalan').val('" . $r_batch[csf("issue_chalan")] . "');\n";
			echo "$('#cbo_service_source').val(" . $r_batch[csf("service_source")] . ");\n";
			echo "document.getElementById('txt_recevied_chalan').value	= '" . $r_batch[csf('received_chalan')] . "';\n";
			echo "load_drop_down( 'requires/dyeing_production_controller', " . $r_batch[csf("service_source")] . "+'**'+" . $r_batch[csf("company_id")] . ", 'load_drop_down_knitting_com', 'dyeing_company_td' );\n";
			echo "$('#cbo_service_company').val(" . $r_batch[csf("service_company")] . ");\n";

			echo "load_drop_down( 'requires/dyeing_production_controller', document.getElementById('cbo_service_company').value+'**'+" . $r_batch[csf("floor_id")] . ", 'load_drop_machine', 'machine_td' );\n";
			echo "document.getElementById('cbo_floor').value = '" . $r_batch[csf("floor_id")] . "';\n";
			echo "document.getElementById('cbo_machine_name').value = '" . $r_batch[csf("machine_id")] . "';\n";
			echo "document.getElementById('cbo_dyeing_type').value	= '" . $r_batch[csf("dyeing_type_id")] . "';\n";
			//echo "document.getElementById('cbo_ltb_btb').value	= '".$r_batch[csf("ltb_btb_id")]."';\n";
			if ($r_batch[csf("load_unload_id")] == 2) {
				echo "document.getElementById('cbo_result_name').value	= '" . $r_batch[csf("result")] . "';\n";
				echo "document.getElementById('txt_remarks').value	= '" . ($r_batch[csf("load_unload_id")] == 1 ? "" : $r_batch[csf("remarks")]) . "';\n";
			}
			//echo "document.getElementById('txt_remarks').value	= '".$r_batch[csf("remarks")]."';\n";
			echo "$('#cbo_service_source').attr('disabled',true);\n";
			echo "$('#cbo_service_company').attr('disabled',true);\n";
			echo "$('#cbo_machine_name').attr('disabled',true);\n";
			echo "$('#cbo_floor').attr('disabled',true);\n";
			echo "$('#cbo_dyeing_type').attr('disabled',true);\n";
			if ($r_batch[csf("load_unload_id")] == 2) {
				//echo "document.getElementById('txt_system_no').value 			= '".$r_batch[csf("system_no")]."';\n";

				echo "set_button_status(0, '" . $_SESSION['page_permission'] . "', 'fnc_pro_fab_subprocess',0);\n";
			} else {
				// echo "document.getElementById('txt_system_no').value 			= '".$r_batch[csf("system_no")]."';\n";

				echo "set_button_status(0, '" . $_SESSION['page_permission'] . "', 'fnc_pro_fab_subprocess',0);\n";
			}
		}
	}
	exit();
}
if ($action == 'populate_data_from_batch2') {

	$ex_data = explode('_', $data);
	$load_unload = $ex_data[0];
	$batch_id = $ex_data[1];
	$batch_no = $ex_data[2];
	$company = $ex_data[3];
	$entry_form = $ex_data[4];
	$company_id = return_field_value("company_id", "pro_batch_create_mst", "id ='$batch_id' and is_deleted=0 and status_active=1");
	$roll_maintained = return_field_value("fabric_roll_level", "variable_settings_production", "company_name ='$company_id' and item_category_id=50 and variable_list=3 and is_deleted=0 and status_active=1");
	$page_upto_id = return_field_value("page_upto_id", "variable_settings_production", "company_name ='$company_id' and item_category_id=50 and variable_list=3 and is_deleted=0 and status_active=1");

	$sql_sales_job=array();
	/*$sql_sales_job=sql_select("select a.buyer_id, b.job_no as job_no_mst,b.booking_no,f.job_no as sales_order_no,f.within_group from wo_booking_mst a, wo_booking_dtls b, wo_po_break_down c, fabric_sales_order_mst f where a.booking_no=b.booking_no and b.booking_no=f.sales_booking_no and b.po_break_down_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.job_no,b.booking_no,f.job_no,f.within_group,a.buyer_id");*/
	$sql_sales_job=sql_select("select f.buyer_id,a.booking_no,f.job_no as sales_order_no,f.within_group,f.po_buyer,f.booking_without_order from pro_batch_create_mst a,fabric_sales_order_mst f where a.sales_order_no=f.job_no and a.status_active=1 and a.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and a.id=$batch_id  group by f.buyer_id,a.booking_no,f.job_no,f.within_group,f.po_buyer,f.booking_without_order");
	//echo "select f.buyer_id,a.booking_no,f.job_no as sales_order_no,f.within_group from pro_batch_create_mst a,fabric_sales_order_mst f where a.sales_order_no=f.job_no and a.status_active=1 and a.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and a.id=$batch_id  group by f.buyer_id,a.booking_no,f.job_no,f.within_group";

	foreach ($sql_sales_job as $sales_job_row) {

		$sales_order_no=$sales_job_row[csf('sales_order_no')];
		$sales_job_arr[$sales_order_no]["sales_order_no"] = $sales_job_row[csf('sales_order_no')];
		$sales_job_arr[$sales_order_no]["buyer_id"] = $sales_job_row[csf('buyer_id')];
		$sales_job_arr[$sales_order_no]["within_group"] = $sales_job_row[csf('within_group')];
		$sales_job_arr[$sales_order_no]["po_buyer"] = $sales_job_row[csf('po_buyer')];
		$sales_job_arr[$sales_order_no]["booking_without_order"] = $sales_job_row[csf('booking_without_order')];
	}

	//$ltb_btb = array(1 => 'BTB', 2 => 'LTB');
	if ($db_type == 0) $select_field1 = "order by a.id";
	else if ($db_type == 2) $select_field1 = " group by a.id,a.sales_order_no,a.batch_no,a.batch_weight,a.color_id,a.company_id,a.working_company_id,a.process_id, a.booking_without_order,a.entry_form,a.is_sales,a.booking_no order by a.id";
	if ($db_type == 0) $select_list = "group_concat(distinct(b.po_id)) as po_id";
	else if ($db_type == 2) $select_list = " listagg(b.po_id,',') within group (order by b.po_id) as po_id";
	//$booking_id=implode(",",array_unique(explode(",",$booking_id)));
	//echo $entry_form.'AA';
	if ($load_unload == 1) {
		if ($batch_no != '') {
			if($entry_form!=136)
			{

			$data_array = sql_select("select a.id as id,a.batch_no,a.entry_form,a.company_id,a.working_company_id, a.batch_weight,Max(a.extention_no) as extention_no,a.process_id as process_id_batch, a.color_id, a.booking_without_order,a.is_sales,a.booking_no,a.sales_order_no,
						sum(b.batch_qnty) as batch_qnty,  $select_list from pro_batch_create_mst a,pro_batch_create_dtls b where
						a.id='$batch_id'  and a.entry_form in(0,36) and a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $select_field1");
			}
			else //Trim batch
			{
				$data_array = sql_select("select a.id as id,a.batch_no,a.entry_form,a.job_no,a.company_id,a.working_company_id, a.batch_weight,Max(a.extention_no) as extention_no,a.process_id as process_id_batch, a.color_id, a.booking_without_order,a.is_sales,a.booking_no,a.sales_order_no,
						sum(b.trims_wgt_qnty) as batch_qnty  from pro_batch_create_mst a,pro_batch_trims_dtls b where  a.id=b.mst_id and
						a.id='$batch_id'  and a.entry_form in(136)  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  group by  a.id ,a.batch_no,a.job_no,a.entry_form,a.company_id,a.working_company_id, a.batch_weight,a.process_id,a.color_id, a.booking_without_order,a.is_sales,a.booking_no,a.sales_order_no");
			}

		}

	} else {

		if ($batch_no != '') {
			if($entry_form!=136)
			{
			$data_array = sql_select("select a.id as id,a.batch_no,a.entry_form,a.company_id,a.working_company_id, a.batch_weight,Max(a.extention_no) as extention_no,a.process_id as process_id_batch, a.color_id, a.booking_without_order,a.is_sales, a.booking_no,a.sales_order_no,
						sum(b.batch_qnty) as batch_qnty,  $select_list from pro_batch_create_mst a,pro_batch_create_dtls b where
						a.id='$batch_id'  and a.entry_form in(0,36) and a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1  and a.is_deleted=0  $select_field1");
			}
			else
			{
					$data_array = sql_select("select a.id as id,a.batch_no,a.job_no,a.entry_form,a.company_id,a.working_company_id, a.batch_weight,Max(a.extention_no) as extention_no,a.process_id as process_id_batch, a.color_id, a.booking_without_order,a.is_sales,a.booking_no,a.sales_order_no,
						sum(b.trims_wgt_qnty) as batch_qnty  from pro_batch_create_mst a,pro_batch_trims_dtls b where  a.id=b.mst_id and
						a.id='$batch_id'  and a.entry_form in(136)  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  group by  a.id ,a.batch_no,a.job_no,a.entry_form,a.company_id,a.working_company_id, a.batch_weight,a.process_id,a.color_id, a.booking_without_order,a.is_sales,a.booking_no,a.sales_order_no");
			}
		}

	}

	if ($db_type == 0) $select_f_group = "";
	else if ($db_type == 2) $select_f_group = "group by a.job_no_mst, b.buyer_name";
	if ($db_type == 0) $select_listagg = "group_concat(distinct(a.po_number)) as po_no,group_concat(distinct(a.file_no)) as file_no,group_concat(distinct(a.grouping)) as ref_no";
	else if ($db_type == 2) $select_listagg = "listagg(cast(a.po_number as varchar(500)),',') within group (order by a.po_number) as po_no,listagg(cast(a.file_no as varchar(500)),',') within group (order by a.file_no) as file_no,listagg(cast(a.grouping as varchar(500)),',') within group (order by a.grouping) as ref_no";

	if ($db_type == 0) $select_listagg_subcon = "group_concat(distinct(a.order_no)) as po_no";
	else if ($db_type == 2) $select_listagg_subcon = "listagg(cast(a.order_no as varchar2(4000)),',') within group (order by a.order_no) as po_no";
	if ($db_type == 0) $select_listagg_subcon = "group_concat(distinct(a.order_no)) as po_no";
	else if ($db_type == 2) $select_listagg_subcon = "listagg(cast(a.order_no as varchar2(4000)),',') within group (order by a.order_no) as po_no";

	foreach ($data_array as $row) {
		//$pro_id = implode(",", array_unique(explode(",", $row[csf('po_id')])));
		if($entry_form==36)
		{
			$pro_id = implode(",", array_unique(explode(",", $row[csf('po_id')])));
			$pro_cond=" and a.id in(" . $pro_id . ")";
		}
		else if($entry_form==0)
		{
			$pro_id = implode(",", array_unique(explode(",", $row[csf('po_id')])));
			$pro_cond=" and a.id in(".$pro_id.")";
		}
		else //Trim Batch
		{
			$job_no = $row[csf('job_no')];
			$pro_cond=" and a.job_no_mst in('".$job_no."')";
		}

		echo "document.getElementById('cbo_company_id').value 				= '" . $row[csf("company_id")] . "';\n";
		echo "$('#cbo_company_id').attr('disabled',true);\n";
		echo "document.getElementById('roll_maintained').value 				= '" . $roll_maintained . "';\n";
		echo "document.getElementById('page_upto').value 				= '" . $page_upto_id . "';\n";


		echo "document.getElementById('txt_entry_form_no').value 			= '" . $row[csf("entry_form")] . "';\n";
		echo "document.getElementById('hidden_batch_id').value 			= '" . $row[csf("id")] . "';\n";
		echo "document.getElementById('txt_batch_ID').value 			= '" . $row[csf("id")] . "';\n";

		echo "document.getElementById('txt_hidden_service_company').value 			= '" . $row[csf("working_company_id")] . "';\n";
		echo "document.getElementById('txt_color').value 				= '" . $color_arr[$row[csf("color_id")]] . "';\n";
		echo "document.getElementById('txt_ext_id').value 				= '" . $row[csf("extention_no")] . "';\n";
		

		if (($page_upto_id == 2 || $page_upto_id > 2) && $roll_maintained == 1) {
			echo "$('#txt_issue_chalan').attr('disabled',false);\n";
		}
		if ($row[csf("entry_form")] == 36) {

			$batch_type = "<b> SUBCONTRACT ORDER BATCH</b>";
			$result_job = sql_select("select $select_listagg_subcon, b.subcon_job as job_no_mst, b.party_id as buyer_name from  subcon_ord_dtls a,
						subcon_ord_mst b where a.job_no_mst=b.subcon_job  and b.status_active=1 and b.is_deleted=0 and a.status_active=1
						and a.is_deleted=0 $pro_cond group by b.subcon_job, b.party_id");
		} else {
			$batch_type = "<b> SELF ORDER BATCH </b>";
			$result_job = sql_select("select $select_listagg, a.job_no_mst, b.buyer_name from wo_po_break_down a,
						wo_po_details_master b where a.job_no_mst=b.job_no and b.status_active=1 and b.is_deleted=0 and a.status_active=1
						and a.is_deleted=0 $pro_cond $select_f_group");

		}
		echo "document.getElementById('batch_type').innerHTML 			= '" . $batch_type . "';\n";

		$process_name_batch = '';
		$process_id_array = explode(",", $row[csf("process_id_batch")]);
		foreach ($process_id_array as $val) {
			if ($process_name_batch == "") $process_name_batch = $conversion_cost_head_array[$val]; else $process_name_batch .= "," . $conversion_cost_head_array[$val];
		}
		$process_ids = explode(",", $row[csf("process_id_batch")]);
		//$procssid=array(1=>'31');
		//print_r($process_ids);
		$procssid = in_array(31, $process_ids);
		//echo $procssid;
		if ($procssid == 31) {
			echo "document.getElementById('txt_process_id').value 			= '31';\n";
		} else {
			echo "document.getElementById('txt_process_id').value 			= '0';\n";
		}

		//echo "document.getElementById('txt_process_id').value 			= '31';\n";

		//echo "document.getElementById('txt_process_name').value 			= '".$process_name_batch."';\n";
		$is_sales = $row[csf("is_sales")];
		$within_group=$sales_job_arr[$row[csf('sales_order_no')]]["within_group"];
		$pro_id2 = implode(",", array_unique(explode(",", $result_job[0][csf("po_no")])));
		$file_no = implode(",", array_unique(explode(",", $result_job[0][csf("file_no")])));
		$ref_no = implode(",", array_unique(explode(",", $result_job[0][csf("ref_no")])));
		if ($is_sales == 1) {
			if($within_group == 1){
			$po_buyer=$sales_job_arr[$row[csf('sales_order_no')]]["po_buyer"];
			$booking_without_order=$sales_job_arr[$row[csf('sales_order_no')]]["booking_without_order"];

			if($booking_without_order==0)
			{
			$job_nos = return_field_value("job_no as job_no", "wo_booking_dtls", "booking_no ='".$row[csf('booking_no')]."' and is_deleted=0 and status_active=1 group by job_no","job_no"); }
			echo "document.getElementById('txt_buyer').value 			= '" . $buyer_arr[$po_buyer] . "';\n";
			echo "document.getElementById('txt_job_no').value 			= '" . $job_nos . "';\n";
			echo "document.getElementById('txt_order_no').value 		= '" . $row[csf('sales_order_no')] . "';\n";
			}else{
			echo "document.getElementById('txt_buyer').value 			= '" . $buyer_arr[$sales_job_arr[$row[csf('sales_order_no')]]["buyer_id"]] . "';\n";
			echo "document.getElementById('txt_job_no').value 			= '';\n";
			echo "document.getElementById('txt_order_no').value 		= '" . $row[csf('sales_order_no')] . "';\n";
			}
		}else{
			echo "document.getElementById('txt_buyer').value 			= '" . $buyer_arr[$result_job[0][csf("buyer_name")]] . "';\n";
			echo "document.getElementById('txt_job_no').value 			= '" . $result_job[0][csf("job_no_mst")] . "';\n";
			echo "document.getElementById('txt_order_no').value 		= '" . $pro_id2 . "';\n";
		}
		echo "document.getElementById('txt_file').value 			= '" . $file_no . "';\n";
		echo "document.getElementById('txt_ref').value 			= '" . $ref_no . "';\n";
		$sql_batch_d = sql_select("select id,service_source,system_no,service_company,received_chalan,issue_chalan,issue_challan_mst_id,company_id,batch_id,batch_no,process_end_date,end_hours,end_minutes,machine_id,floor_id,process_id,ltb_btb_id,remarks,hour_load_meter from
					pro_fab_subprocess where batch_id='$batch_id' and entry_form=35 and load_unload_id=1 and status_active=1 and is_deleted=0 ");
		foreach ($sql_batch_d as $dyeing_d) {//$minute=str_pad($r_batch[csf("end_minutes")],2,'0',STR_PAD_LEFT);
			echo "document.getElementById('txt_dying_started').value = '" . change_date_format($dyeing_d[csf("process_end_date")]) . "';\n";
			echo "document.getElementById('txt_dying_end_load').value = '" . str_pad($dyeing_d[csf("end_hours")], 2, '0', STR_PAD_LEFT) . ':' . str_pad($dyeing_d[csf("end_minutes")], 2, '0', STR_PAD_LEFT) . "';\n";
			echo "$('#txt_issue_chalan').val('" . $dyeing_d[csf("issue_chalan")] . "');\n";
			echo "$('#cbo_service_source').val(" . $dyeing_d[csf("service_source")] . ");\n";
			echo "document.getElementById('txt_recevied_chalan').value	= '" . $dyeing_d[csf('received_chalan')] . "';\n";
			echo "load_drop_down( 'requires/dyeing_production_controller', " . $dyeing_d[csf("service_source")] . "+'**'+" . $dyeing_d[csf("company_id")] . ", 'load_drop_down_knitting_com', 'dyeing_company_td' );\n";
			echo "$('#cbo_service_company').val(" . $dyeing_d[csf("service_company")] . ");\n";
			echo "load_drop_down( 'requires/dyeing_production_controller', '" . $dyeing_d[csf("service_company")] . "', 'load_drop_floor', 'floor_td' );\n";
			echo "document.getElementById('txt_ltb_btb').value	= '" . $ltb_btb[$dyeing_d[csf("ltb_btb_id")]] . "';\n";
			if ($load_unload == 2) {
				echo "process_check('31');\n";
			}
		}
		//exit();
	}
	if ($db_type == 0) $select_group_row1 = " order by id desc limit 0,1";
	else if ($db_type == 2) $select_group_row1 = " and  rownum>=1 order by id desc";//order by id desc limit 0,1
	if ($load_unload == 1) {

		$sql_batch = sql_select(
			"select id,batch_no,company_id,system_no,batch_id,service_source,service_company,received_chalan,issue_chalan,issue_challan_mst_id,process_end_date,load_unload_id,end_hours,end_minutes,machine_id,floor_id,process_id,
			ltb_btb_id,water_flow_meter,result,remarks,dyeing_type_id,hour_load_meter,multi_batch_load_id	from pro_fab_subprocess where batch_id='$batch_id' and entry_form=35 and load_unload_id in(1) and status_active=1 and is_deleted=0  ");
	} else if ($load_unload == 2) {
		$sql_batch = sql_select("select id,batch_no,company_id,system_no,service_source,service_company,received_chalan,issue_chalan,issue_challan_mst_id,batch_id,process_end_date,load_unload_id,end_hours,end_minutes,machine_id,floor_id,process_id,ltb_btb_id,water_flow_meter,result,remarks,dyeing_type_id,hour_unload_meter,shift_name,fabric_type,production_date from pro_fab_subprocess where batch_id='$batch_id' and entry_form=35 and load_unload_id in(2,1) and status_active=1 and is_deleted=0 $select_group_row1");
	}
	foreach ($sql_batch as $r_batch) {
		if ($load_unload == 1) //Load
		{
			if ($r_batch[csf('load_unload_id')] == 1) {
				echo "document.getElementById('txt_update_id').value 				= '" . $r_batch[csf("id")] . "';\n";
			} else {
				echo "document.getElementById('txt_update_id').value 				= '';\n";
			}
			$process_name = '';
			$process_id_array = explode(",", $r_batch[csf("process_id")]);
			foreach ($process_id_array as $val) {
				if ($process_name == "") $process_name = $conversion_cost_head_array[$val]; else $process_name .= "," . $conversion_cost_head_array[$val];
			}

			echo "document.getElementById('txt_batch_no').value 			= '" . $r_batch[csf("batch_no")] . "';\n";
			echo "document.getElementById('txt_process_start_date').value 		= '" . change_date_format($r_batch[csf("process_end_date")]) . "';\n";
			echo "document.getElementById('txt_process_id').value 				= '" . $r_batch[csf("process_id")] . "';\n";
			echo "load_drop_down( 'requires/dyeing_production_controller', '" . $r_batch[csf("service_company")] . "', 'load_drop_floor', 'floor_td' );\n";
			echo "document.getElementById('cbo_ltb_btb').value	= '" . $r_batch[csf("ltb_btb_id")] . "';\n";
			echo "document.getElementById('txt_load_meter').value	= '" . $r_batch[csf("hour_load_meter")] . "';\n";
			echo "document.getElementById('txt_water_flow').value	= '" . $r_batch[csf("water_flow_meter")] . "';\n";
			echo "document.getElementById('cbo_yesno').value	= '" . $r_batch[csf("multi_batch_load_id")] . "';\n";
			$minute = str_pad($r_batch[csf("end_minutes")], 2, '0', STR_PAD_LEFT);
			$hour = str_pad($r_batch[csf("end_hours")], 2, '0', STR_PAD_LEFT);
			echo "document.getElementById('txt_start_minutes').value	= '" . $minute . "';\n";
			echo "document.getElementById('txt_start_hours').value	= '" . $hour . "';\n";
			echo "$('#txt_issue_chalan').val('" . $r_batch[csf("issue_chalan")] . "');\n";
			echo "$('#cbo_service_source').val(" . $r_batch[csf("service_source")] . ");\n";
			echo "document.getElementById('txt_recevied_chalan').value	= '" . $r_batch[csf('received_chalan')] . "';\n";
			echo "load_drop_down( 'requires/dyeing_production_controller', " . $r_batch[csf("service_source")] . "+'**'+" . $r_batch[csf("company_id")] . ", 'load_drop_down_knitting_com', 'dyeing_company_td' );\n";
			echo "$('#cbo_service_company').val(" . $r_batch[csf("service_company")] . ");\n";
			echo "$('#cbo_service_source').attr('disabled',true);\n";
			echo "$('#cbo_service_company').attr('disabled',true);\n";

			echo "load_drop_down( 'requires/dyeing_production_controller', document.getElementById('cbo_service_company').value+'**'+" . $r_batch[csf("floor_id")] . ", 'load_drop_machine', 'machine_td' );\n";
			echo "document.getElementById('cbo_floor').value = '" . $r_batch[csf("floor_id")] . "';\n";
			echo "document.getElementById('cbo_machine_name').value = '" . $r_batch[csf("machine_id")] . "';\n";
			echo "document.getElementById('txt_remarks').value	= '" . $r_batch[csf("remarks")] . "';\n";
			echo "document.getElementById('cbo_dyeing_type').value	= '" . $r_batch[csf("dyeing_type_id")] . "';\n";
			if ($r_batch[csf("id")] != 0) {
				//echo "document.getElementById('txt_system_no').value 			= '".$r_batch[csf("system_no")]."';\n";

				echo "set_button_status(1, '" . $_SESSION['page_permission'] . "', 'fnc_pro_fab_subprocess',1);\n";
			} else {
				//echo "document.getElementById('txt_system_no').value 			= '';\n";

				echo "set_button_status(0, '" . $_SESSION['page_permission'] . "', 'fnc_pro_fab_subprocess',0);\n";
			}
		} else if ($load_unload == 2) //Unload
		{
			if ($r_batch[csf("load_unload_id")] == 2) {
				echo "document.getElementById('txt_update_id').value 		= '" . $r_batch[csf("id")] . "';\n";
				echo "document.getElementById('txt_batch_no').value 			= '" . $r_batch[csf("batch_no")] . "';\n";
				echo "document.getElementById('txt_process_end_date').value = '" . ($r_batch[csf("load_unload_id")] == 1 ? "" : change_date_format($r_batch[csf("process_end_date")])) . "';\n";
				echo "document.getElementById('txt_process_date').value = '" . ($r_batch[csf("load_unload_id")] == 1 ? "" : change_date_format($r_batch[csf("production_date")])) . "';\n";
				echo "document.getElementById('cbo_shift_name').value	= '" . $r_batch[csf("shift_name")] . "';\n";
				echo "document.getElementById('cbo_fabric_type').value	= '" . $r_batch[csf("fabric_type")] . "';\n";
				echo "document.getElementById('txt_unload_meter').value	= '" . $r_batch[csf("hour_unload_meter")] . "';\n";
				echo "document.getElementById('txt_water_flow').value	= '" . ($r_batch[csf("load_unload_id")] == 1 ? "" : $r_batch[csf("water_flow_meter")]) . "';\n";
				echo "document.getElementById('cbo_ltb_btb').value	= '" . $r_batch[csf("ltb_btb_id")] . "';\n";
			}
			$process_name = '';
			$process_id_array = explode(",", $r_batch[csf("process_id")]);
			foreach ($process_id_array as $val) {
				if ($process_name == "") $process_name = $conversion_cost_head_array[$val]; else $process_name .= "," . $conversion_cost_head_array[$val];
			}
			echo "document.getElementById('txt_process_id').value 				= '" . $r_batch[csf("process_id")] . "';\n";
			echo "process_check('" . $r_batch[csf("process_id")] . "');\n";
			//echo "document.getElementById('txt_process_name').value 				= '".$process_name."';\n";
			if ($r_batch[csf("load_unload_id")] == 2) {
				$minute = str_pad($r_batch[csf("end_minutes")], 2, '0', STR_PAD_LEFT);
				$hour = str_pad($r_batch[csf("end_hours")], 2, '0', STR_PAD_LEFT);
				echo "document.getElementById('txt_end_minutes').value	= '" . $minute . "';\n";
				echo "document.getElementById('txt_end_hours').value	= '" . $hour . "';\n";
			}
			echo "$('#txt_issue_chalan').val('" . $r_batch[csf("issue_chalan")] . "');\n";
			echo "$('#cbo_service_source').val(" . $r_batch[csf("service_source")] . ");\n";
			echo "document.getElementById('txt_recevied_chalan').value	= '" . $r_batch[csf('received_chalan')] . "';\n";
			echo "load_drop_down( 'requires/dyeing_production_controller', " . $r_batch[csf("service_source")] . "+'**'+" . $r_batch[csf("company_id")] . ", 'load_drop_down_knitting_com', 'dyeing_company_td' );\n";
			echo "$('#cbo_service_company').val(" . $r_batch[csf("service_company")] . ");\n";
			echo "document.getElementById('cbo_floor').value = '" . $r_batch[csf("floor_id")] . "';\n";
			echo "load_drop_down( 'requires/dyeing_production_controller', document.getElementById('cbo_service_company').value+'**'+" . $r_batch[csf("floor_id")] . ", 'load_drop_machine', 'machine_td' );\n";
			echo "document.getElementById('cbo_machine_name').value = '" . $r_batch[csf("machine_id")] . "';\n";
			//echo "document.getElementById('cbo_ltb_btb').value	= '".$r_batch[csf("ltb_btb_id")]."';\n";
			echo "document.getElementById('cbo_dyeing_type').value	= '" . $r_batch[csf("dyeing_type_id")] . "';\n";
			if ($r_batch[csf("load_unload_id")] == 2) {
				echo "document.getElementById('cbo_result_name').value	= '" . $r_batch[csf("result")] . "';\n";
				echo "document.getElementById('txt_remarks').value	= '" . ($r_batch[csf("load_unload_id")] == 1 ? "" : $r_batch[csf("remarks")]) . "';\n";
			}
			//echo "document.getElementById('txt_remarks').value	= '".$r_batch[csf("remarks")]."';\n";
			echo "$('#cbo_service_source').attr('disabled',true);\n";
			echo "$('#cbo_service_company').attr('disabled',true);\n";
			echo "$('#cbo_machine_name').attr('disabled',true);\n";
			echo "$('#cbo_floor').attr('disabled',true);\n";
			if ($r_batch[csf("load_unload_id")] == 2) {
				//echo "document.getElementById('txt_system_no').value 			= '".$r_batch[csf("system_no")]."';\n";

				echo "set_button_status(1, '" . $_SESSION['page_permission'] . "', 'fnc_pro_fab_subprocess',1);\n";
			} else {
				// echo "document.getElementById('txt_system_no').value 			= '".$r_batch[csf("system_no")]."';\n";

				echo "set_button_status(0, '" . $_SESSION['page_permission'] . "', 'fnc_pro_fab_subprocess',0);\n";
			}
		}
	}

	exit();
}

if ($action == 'show_fabric_desc_listview') {
	$ex_data = explode('_', $data);
	$batch_id = $ex_data[0];
	$load_unload = $ex_data[2];
	$process_id = $ex_data[3];
	$entry_form = $ex_data[4];
	$hidden_double_dyeing = $ex_data[5];
	$hidden_result_id = $ex_data[6];
	$dyeing_type_id = $ex_data[7];
	//echo $dyeing_type_id.', ';
	$double_dyeing=0;
	//echo $process_id.'XX';
	$batch_data =sql_select("select company_id,double_dyeing,process_id from pro_batch_create_mst where id ='$batch_id' and is_deleted=0 and status_active=1");
	foreach ($batch_data as $row)
	{
		$company_id = $row[csf('company_id')];
		$double_dyeing = $row[csf('double_dyeing')];
		$process_ids = $row[csf('process_id')];
	}
	//echo $company_id.'=='.$page_upto_id.'=='.$double_dyeing;
	if($double_dyeing==0 || $double_dyeing==2) $multi_dyeing=2;else $multi_dyeing=$double_dyeing;

	if ($load_unload == 1) $load_unload_cond = "and a.load_unload_id in(1)";
	else if ($load_unload == 2) $load_unload_cond = "and a.load_unload_id in(2)";
	if ($load_unload == 1) {
		//$process_ids = return_field_value("process_id", "pro_batch_create_mst", "id ='$batch_id' and is_deleted=0 and status_active=1");
		$process_ids = explode(",", $process_ids);
		$procss = in_array(31, $process_ids);
		if ($procss == "") {
			$batch_filed = "yellow";

		}
	}
	$batch_filed_read = "readonly";

	//$company_id = return_field_value("company_id", "pro_batch_create_mst", "id ='$batch_id' and is_deleted=0 and status_active=1");
	$roll_maintained = return_field_value("fabric_roll_level", "variable_settings_production", "company_name ='$company_id' and item_category_id=50 and variable_list=3 and is_deleted=0 and status_active=1");
	$page_upto_id = return_field_value("page_upto_id", "variable_settings_production", "company_name =$company_id and item_category_id=50 and variable_list=3 and is_deleted=0 and status_active=1");
	$fabricData = sql_select("select variable_list,fabric_roll_level from variable_settings_production where company_name ='$company_id' and variable_list in(3) and item_category_id=13 and is_deleted=0 and status_active=1");
	//echo "select variable_list,fabric_roll_level from variable_settings_production where company_name ='$company_id' and variable_list in(3) and item_category_id=13 and is_deleted=0 and status_active=1";
	foreach ($fabricData as $row) {
		if ($row[csf('variable_list')] == 3) {
			$roll_maintained_id = $row[csf('fabric_roll_level')];
		}
	}
	//echo $roll_maintained.'='.$roll_maintained_id;
//die;
	$fabric_desc_arr = array();

	$prodData = sql_select("select id,detarmination_id, item_description, lot,gsm,yarn_count_id,brand, dia_width, product_name_details from product_details_master where item_category_id=13");
	foreach ($prodData as $row) {
		$fabric_desc_arr[$row[csf('id')]]['desc'] = $row[csf('product_name_details')];
		$fabric_desc_arr[$row[csf('id')]]['gsm'] = $row[csf('gsm')];
		$fabric_desc_arr[$row[csf('id')]]['dia'] = $row[csf('dia_width')];
		$fabric_desc_arr[$row[csf('id')]]['detarmination_id'] = $row[csf('detarmination_id')];
	}
	$yarn_lot_arr = array();
//echo $company_id.'=='.$double_dyeing.'='.$page_upto_id.'='.$roll_maintained_id;
	if ($db_type == 0) {
		if ($roll_maintained_id == 1) {
			$yarn_lot_data = sql_select("select  b.po_id, b.prod_id, group_concat(d.yarn_lot) as yarn_lot, group_concat(d.yarn_count) as yarn_count, group_concat(d.brand_id) as brand_id
						from pro_batch_create_mst a,pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d,  inv_receive_master e
						where a.id=b.mst_id and b.roll_id=c.id and c.dtls_id=d.id and d.mst_id=e.id  and a.id=$batch_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and e.entry_form in(2,22)
						group by  b.po_id, b.prod_id");
		} else {
			$yarn_lot_data = sql_select("select  b.po_id, b.prod_id, group_concat(d.yarn_lot) as yarn_lot, group_concat(d.yarn_count) as yarn_count, group_concat(d.brand_id) as brand_id
						from pro_batch_create_mst a,pro_batch_create_dtls b, pro_grey_prod_entry_dtls d,  inv_receive_master e
						where a.id=b.mst_id and b.prod_id=d.prod_id and d.mst_id=e.id  and a.id=$batch_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and e.entry_form in(2,22)
						group by  b.po_id, b.prod_id");
		}
	} else if ($db_type == 2) {

		if ($roll_maintained_id == 1) {
			$yarn_lot_data = sql_select("select  b.po_id, b.prod_id, LISTAGG(CAST(d.yarn_lot AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY d.yarn_lot) as yarn_lot, LISTAGG(CAST(d.yarn_count AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY d.yarn_count) as yarn_count,LISTAGG(CAST(d.brand_id AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY d.brand_id) as brand_id
						from pro_batch_create_mst a,pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d,  inv_receive_master e
						where a.id=b.mst_id and b.roll_id=c.id and c.dtls_id=d.id and d.mst_id=e.id  and a.id=$batch_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and e.entry_form in(2,22)
						group by b.po_id, b.prod_id");
		} else {

			$yarn_lot_data = sql_select("select  b.po_id, b.prod_id, LISTAGG(CAST(d.yarn_lot AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY d.yarn_lot) as yarn_lot, LISTAGG(CAST(d.yarn_count AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY d.yarn_count) as yarn_count,LISTAGG(CAST(d.brand_id AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY d.brand_id) as brand_id
						from pro_batch_create_mst a,pro_batch_create_dtls b, pro_grey_prod_entry_dtls d,  inv_receive_master e
						where a.id=b.mst_id  and d.mst_id=e.id and b.prod_id=d.prod_id  and a.id=$batch_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and e.entry_form in(2,22)
						group by b.po_id, b.prod_id");
		}
	}
	foreach ($yarn_lot_data as $rows) {
		$yarn_lot = explode(",", $rows[csf('yarn_lot')]);
		$brand_id = explode(",", $rows[csf('brand_id')]);
		$yarn_count = explode(",", $rows[csf('yarn_count')]);
		$yarn_lot_arr[$rows[csf('prod_id')]][$rows[csf('po_id')]]['lot'] = implode(",", array_unique($yarn_lot));
		$yarn_lot_arr[$rows[csf('prod_id')]][$rows[csf('po_id')]]['yarn_count'] = implode(",", array_unique($yarn_count));
		$yarn_lot_arr[$rows[csf('prod_id')]][$rows[csf('po_id')]]['brand_id'] = implode(",", array_unique($brand_id));
	}
		$max_load_id = return_field_value("max(id) as maxload_id", "pro_fab_subprocess", "batch_id =$batch_id and entry_form=35  and load_unload_id in(1) and is_deleted=0 and status_active=1","maxload_id");
		$max_unload_id = return_field_value("max(id)  as maxload_id", "pro_fab_subprocess", "batch_id =$batch_id and entry_form=35  and load_unload_id in(2) and is_deleted=0 and status_active=1","maxload_id");

		if($max_load_id!="")
		{
		 $load_insert = ("select a.load_unload_id,b.production_qty,b.roll_id,b.prod_id,b.gsm,b.dia_width,b.barcode_no,b.width_dia_type from  pro_fab_subprocess a, pro_fab_subprocess_dtls b where a.id=b.mst_id and a.batch_id=$batch_id and a.entry_form=35  and a.load_unload_id in(1) and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.is_deleted=0 and b.mst_id in($max_load_id) ");
		$load_result = sql_select($load_insert);
		foreach ($load_result as $row) {
				
					$load_qty_arr[$row[csf('prod_id')]][$row[csf('barcode_no')]][$row[csf('gsm')]][$row[csf('width_dia_type')]]= $row[csf('production_qty')];
					$load_qty_arr2[$row[csf('prod_id')]][$row[csf('gsm')]][$row[csf('width_dia_type')]]= $row[csf('production_qty')];
				
				
			}
		}
		if($max_unload_id!="")
		{
		 $unload_insert = ("select a.load_unload_id,b.production_qty,b.roll_id,b.prod_id,b.gsm,b.dia_width,b.barcode_no,b.width_dia_type from  pro_fab_subprocess a, pro_fab_subprocess_dtls b where a.id=b.mst_id and a.batch_id=$batch_id and a.entry_form=35  and a.load_unload_id in(2) and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.is_deleted=0  ");//and b.mst_id in($max_unload_id)
		$unload_result = sql_select($unload_insert);
		foreach ($unload_result as $row) {
					$unload_qty_arr[$row[csf('prod_id')]][$row[csf('barcode_no')]][$row[csf('gsm')]][$row[csf('width_dia_type')]]= $row[csf('production_qty')];
					$unload_qty_arr2[$row[csf('prod_id')]][$row[csf('gsm')]][$row[csf('width_dia_type')]]= $row[csf('production_qty')];
				
			}
		}

//echo $entry_form.'XXXXXXXXXXXXXXXXXXXXX'.$page_upto_id.'='.$roll_maintained;
	if($entry_form!=136)
	{
		if (($page_upto_id == 2 || $page_upto_id > 2) && $roll_maintained == 1) {
			 $sql_insert = ("select b.production_qty,b.roll_id,b.prod_id from  pro_fab_subprocess a, pro_fab_subprocess_dtls b where a.id=b.mst_id and a.batch_id=$batch_id and a.entry_form=35  and b.roll_id>0 $load_unload_cond and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.is_deleted=0");
			$sql_insert_roll = sql_select($sql_insert);
			$inserted_roll = array();
			foreach ($sql_insert_roll as $in_row) {
				$inserted_roll[] = $in_row[csf('roll_id')];
			}
				if($dyeing_type_id==2)//CBP dyeing
				{
					
					$roll_id_cond = "";
					//if (count($inserted_roll) > 0) $roll_id_cond = "  and b.roll_id not in (" . implode(",", $inserted_roll) . ")";
					
				}
				else
				{
					if($multi_dyeing==2)//multi is No
					{
					$roll_id_cond = "";
					if (count($inserted_roll) > 0) $roll_id_cond = "  and b.roll_id not in (" . implode(",", $inserted_roll) . ")";
					}
				}

			if ($db_type == 0) $select_group = " group by b.id,b.item_description";
			else if ($db_type == 2) $select_group = "group by b.id,b.item_description,b.width_dia_type,a.entry_form,b.prod_id,b.po_id,b.roll_no,b.roll_id,b.barcode_no,c.roll_no,c.barcode_no";
			  $result = "select a.entry_form,b.width_dia_type,b.prod_id,b.po_id,b.barcode_no as batch_barcode,b.roll_no as batch_rollno,b.roll_id ,b.item_description,b.width_dia_type, sum(b.batch_qnty) as batch_qnty,c.roll_no,c.barcode_no from pro_batch_create_dtls b,pro_batch_create_mst a,pro_roll_details c where b.mst_id=$batch_id and a.id=b.mst_id and b.roll_id=c.id and  a.entry_form in(0,36) and b.status_active=1 and b.is_deleted=0  $roll_id_cond $select_group";
			$result_data = sql_select($result);

			$i = 1;
			$b_qty = 0;
			$tot_prod_qnty = 0;
			foreach ($result_data as $row) {
				if ($row[csf('entry_form')] == 36) {
					$desc = explode(",", $row[csf('item_description')]);
					$cons_comps = $desc[0];
					$gsm = $desc[1];
					$dia_width = $desc[2];
				} else {
					$cons_comps_data = explode(",", $fabric_desc_arr[$row[csf('prod_id')]]['desc']);
					$cons_comps = $cons_comps_data[0] . ' ' . $cons_comps_data[1];
					$gsm = $fabric_desc_arr[$row[csf('prod_id')]]['gsm'];
					$dia_width = $fabric_desc_arr[$row[csf('prod_id')]]['dia'];
				}
				$brand = $lot = $yarn_lot_arr[$row[csf('prod_id')]][$row[csf('po_id')]]['brand_id'];
				$lot = $yarn_lot_arr[$row[csf('prod_id')]][$row[csf('po_id')]]['lot'];
				$brand_id = explode(',', $brand);
				$brand_value = "";
				foreach ($brand_id as $val) {
					if ($val > 0) {
						if ($brand_value == '') $brand_value = $brand_name[$val]; else $brand_value .= ", " . $brand_name[$val];
					}
				}
				$y_count_id = $yarn_lot_arr[$row[csf('prod_id')]][$row[csf('po_id')]]['yarn_count'];
				if ($db_type == 0) {
					$count_id = array_unique(explode(",", $y_count_id));
				} else {
					$count_id = array_unique(explode(",", $y_count_id));
				}
				$yarn_count_value = '';
				foreach ($count_id as $val) {
					if ($val > 0) {
						if ($yarn_count_value == '') $yarn_count_value = $yarncount[$val]; else $yarn_count_value .= ", " . $yarncount[$val];
					}
				}
				
				
					//echo $load_unload.'='.$dyeing_type_id;
				if($load_unload==2)
				{
					if($dyeing_type_id==2) //CBP
					{
						$unload_prod_qnty=$unload_qty_arr[$row[csf('prod_id')]][$row[csf('batch_barcode')]][$gsm][$row[csf('width_dia_type')]];
						//$load_prod_qnty=$load_qty_arr[$row[csf('prod_id')]][$row[csf('batch_barcode')]][$gsm][$row[csf('width_dia_type')]];
						$load_prod_qnty = $row[csf('batch_qnty')]-$unload_prod_qnty;
					}
					else
					{
						$load_prod_qnty=$load_qty_arr[$row[csf('prod_id')]][$row[csf('batch_barcode')]][$gsm][$row[csf('width_dia_type')]];
					}
					
				}
				else
				{
					$load_prod_qnty = $row[csf('batch_qnty')];
				}
				
			
				if (($page_upto_id == 2 || $page_upto_id > 2) && $roll_maintained == 1) {
					//$roll_no = $fabric_roll_arr[$row[csf('roll_id')]]['roll'];

					$roll_no = $row[csf('roll_no')];
					$batch_qnty = $row[csf('batch_qnty')];
					$tot_qty += $row[csf('batch_qnty')];
					//echo $process_id.'='.$roll_maintained.'XX';
					if ($load_unload == 2) {
						//echo $load_prod_qnty.'XX';
							$readdata = "readonly";
							$prod_qnty = $load_prod_qnty;
							$tot_prod_qnty += $load_prod_qnty;
						} else {
						//echo $load_prod_qnty.'BB';
							$readdata = "";
							$prod_qnty =  $load_prod_qnty;
							$tot_prod_qnty = $load_prod_qnty;
						}
				} else {
					$roll_no = $row[csf('no_of_roll')];
					$tot_qty += $row[csf('batch_qnty')];
					//echo $load_prod_qnty.'B';
					if ($load_unload == 2) {
							$readdata = "readonly";
							$prod_qnty = $load_prod_qnty;
							$tot_prod_qnty += $load_prod_qnty;
						} else {
							$readdata = "";
							$prod_qnty = $load_prod_qnty;
							$tot_prod_qnty += $load_prod_qnty;
						}
				}
				
				if($dyeing_type_id==2) //CBP
				{
					$readdata = "";
				}
				else
				{
					$readdata =$readdata;
				}

				?>
				<tr class="general" id="row_<? echo $i; ?>">
					<td width="70" id="sl_<? echo $i; ?>">
						<? if (($page_upto_id == 2 || $page_upto_id > 2) && $roll_maintained == 1) { ?>
							<input type="checkbox" id="checkRow_<? echo $i; ?>" name="checkRow[]"
								   checked/> &nbsp; &nbsp;<? echo $i; ?>
						<? }

						?>
					</td>
					<td><input type="text" name="txtconscomp_<? echo $i; ?>" id="txtconscomp_<? echo $i; ?>" class="text_boxes" style="width:170px;" value="<? echo $cons_comps; ?>" disabled/></td>
					<td><input type="text" name="txtgsm_<? echo $i; ?>" id="txtgsm_<? echo $i; ?>" class="text_boxes"  style="width:40px;" value="<? echo trim($gsm); ?>"/></td>
					<td><input type="text" name="txtdiawidth_<? echo $i; ?>" id="txtdiawidth_<? echo $i; ?>" class="text_boxes" style="width:40px;" value="<? echo $dia_width; ?>" disabled/></td>
					<td><input type="text" name="txtdiatype_<? echo $i; ?>" id="txtdiatype_<? echo $i; ?>" class="text_boxes" style="width:70px;" value="<? echo $fabric_typee[$row[csf('width_dia_type')]]; ?>" disabled/>
					<input type="hidden" name="hiddendiatypeid_<? echo $i; ?>" id="hiddendiatypeid_<? echo $i; ?>" value="<? echo $row[csf('width_dia_type')]; ?>"  readonly/>
					</td>
					<td><input type="text" name="txtroll_<? echo $i; ?>" id="txtroll_<? echo $i; ?>" class="text_boxes" style="width:40px;" value="<? echo $row[csf('batch_rollno')];//$roll_no; ?>"/><input type="hidden"  name="rollid_<? echo $i; ?>" id="rollid_<? echo $i; ?>" style="width:50px;" value="<? echo $row[csf('roll_id')]; ?>" class="text_boxes_numeric"/>
						<input type="hidden" name="txtbarcode_<? echo $i; ?>" id="txtbarcode_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $row[csf('batch_barcode')];//$barcode;?>"  style="width:65px;" <? echo $readonly; ?> /></td>
					<td><input type="text" name="txtbatchqnty_<? echo $i; ?>" id="txtbatchqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px; background:<? echo $batch_filed; ?>" onKeyUp="calculate_production_qnty2();" value="<? echo number_format($row[csf('batch_qnty')], 2, ".", ""); ?>" <? echo $batch_filed_read ?>/>
					</td>
					<td><input type="text" name="txtprodqnty_<? echo $i; ?>" id="txtprodqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px;" onKeyUp="calculate_production_qnty();" value="<? echo number_format($prod_qnty, 2, ".", ""); ?>" <? echo $readdata ?> />
					</td>
					<td><input type="text" name="txtrate_<? echo $i; ?>" id="txtrate_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px;" value="<? echo $row[csf('rate')]; ?>" readonly/></td>
					<td><input type="text" name="txtamount_<? echo $i; ?>" id="txtamount_<? echo $i; ?>" class="text_boxes_numeric" style="width:70px;" value="<? echo $row[csf('amount')]; ?>"   readonly/></td>
					<td><input type="text" name="txtlot_<? echo $i; ?>" id="txtlot_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px;" value="<? echo $lot; ?>" readonly disabled/>
					</td>
					<td><input type="text" name="txtyarncount_<? echo $i; ?>" id="txtyarncount_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px;" value="<? echo $yarn_count_value; ?>" readonly  disabled/></td>
					<td><input type="text" name="txtbrand_<? echo $i; ?>" id="txtbrand_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px;" value="<? echo $brand_value; ?>" readonly  disabled/>
						<input type="hidden" name="txtprodid_<? echo $i; ?>" id="txtprodid_<? echo $i; ?>" value="<? echo $row[csf('prod_id')]; ?>"/>
						<input type="hidden" name="txtdeterid_<? echo $i; ?>" id="txtdeterid_<? echo $i; ?>" value="<? echo $fabric_desc_arr[$row[csf('prod_id')]]['detarmination_id']; ?>"/>
						<input type="hidden" name="updateiddtls_<? echo $i; ?>" id="updateiddtls_<? echo $i; ?>" class="text_boxes" value="<? //echo $row[csf('id')];?>" readonly/>
						<input type="hidden" name="txtremark_<? echo $i; ?>" id="txtremark_<? echo $i; ?>"  class="text_boxes" style="width:70px;"  disabled/>
					</td>

				</tr>
				<?
				$b_qty += $row[csf('batch_qnty')];
				$total_prod_qnty += $load_prod_qnty;
				$i++;
			}
			?>
			<tr>
				<td colspan="6" align="right"><b>Sum:</b> <? //echo $b_qty;
					?> </td>

				<td align="right"><b><input type="text" name="total_batch_qnty" id="total_batch_qnty" class="text_boxes_numeric" style="width:60px" value="<? echo number_format($b_qty, 2); ?> " readonly/></b></td>
				<td><input type="text" name="total_production_qnty" id="total_production_qnty" class="text_boxes_numeric" style="width:60px" value="<? echo number_format($total_prod_qnty, 2); ?> " readonly/></td>
				<td align="right"></td>
				<td align="right"><input type="text" name="total_amount" id="total_amount" class="text_boxes_numeric" style="width:70px" value="<? echo $tot_amount; ?>" readonly/></td>
			</tr>
			<?
			exit();
		}   //Roll End
		else {
			
			if($max_unload_id!="")
				{
				  $unload_insert = ("select a.load_unload_id,b.production_qty,b.roll_id,b.prod_id,b.gsm,b.dia_width,b.barcode_no,b.width_dia_type from  pro_fab_subprocess a, pro_fab_subprocess_dtls b where a.id=b.mst_id and a.batch_id=$batch_id and a.entry_form=35  and a.load_unload_id in(2) and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and a.is_deleted=0 ");
				$unload_result = sql_select($unload_insert);
				foreach ($unload_result as $row) {
							//$unload_qty_arr[$row[csf('prod_id')]][$row[csf('barcode_no')]][$row[csf('gsm')]][$row[csf('width_dia_type')]]= $row[csf('production_qty')];
							$previ_unload_qty_arr[$row[csf('prod_id')]][$row[csf('gsm')]][$row[csf('width_dia_type')]]+= $row[csf('production_qty')];
						
					}
				}
		
			$batch_id_search = sql_select("select a.batch_id from pro_fab_subprocess a,pro_fab_subprocess_dtls b where a.id=b.mst_id and a.batch_id=$batch_id and company_id=$company_id and entry_form=35 and a.status_active=1 and b.status_active=1 $load_unload_cond");
			//echo "select a.batch_id from pro_fab_subprocess a,pro_fab_subprocess_dtls b where a.id=b.mst_id and a.batch_id=$batch_id and company_id=$company_id and entry_form=35 and a.status_active=1 and b.status_active=1 $load_unload_cond";
			if($dyeing_type_id==2)//CBP dyeing
				{
				$batch_insert_cond = "";
				}
				else
				{
					if($multi_dyeing==2)//multi is No
					{
					$batch_insert_cond = "";
					if (count($batch_id_search) > 0) $batch_insert_cond = " and a.id!=" . $batch_id_search[0][csf('batch_id')] . "";
					}

				}

			if ($db_type == 0) $select_group = " group by b.id,b.item_description";
			else if ($db_type == 2) $select_group = "group by b.item_description,b.width_dia_type,a.entry_form,b.prod_id,b.po_id";
			//echo "sdsd";
			  $sql_result = "select a.entry_form,b.width_dia_type,b.prod_id,b.po_id,count(b.roll_no) as no_of_roll ,b.item_description,b.width_dia_type, sum(b.batch_qnty) as batch_qnty from pro_batch_create_dtls b,pro_batch_create_mst a,pro_roll_details c where b.mst_id=$batch_id and a.id=b.mst_id and b.roll_id=c.id and  a.entry_form in(0,36) and b.status_active=1 and b.is_deleted=0 $batch_insert_cond $select_group";
			$result=sql_select($sql_result);
			if(count($result)==0)
			{
				if ($db_type == 0) $select_group = " group by b.id,b.item_description";
				else if ($db_type == 2) $select_group = "group by b.item_description,b.width_dia_type,a.entry_form,b.prod_id,b.po_id";
				 $sql_result ="select a.entry_form,b.width_dia_type,b.prod_id,b.po_id,count(b.roll_no) as no_of_roll ,b.item_description,b.width_dia_type, sum(b.batch_qnty) as batch_qnty,0 as roll_no, 0 as barcode_no from pro_batch_create_dtls b,pro_batch_create_mst a where b.mst_id=$batch_id and a.id=b.mst_id and  a.entry_form in(0,36) and b.status_active=1 and b.is_deleted=0 $batch_insert_cond $select_group";
				$result=sql_select($sql_result);
			}

			
			if (count($result) > 0) {

				$i = 1;
				$tot_prod_qty = 0;
				$tot_batch_qty = 0;
				foreach ($result as $row) {
					//$desc=explode(",",$row[csf('item_description')]);
					if ($row[csf('entry_form')] == 36) {
						$desc = explode(",", $row[csf('item_description')]);
						//print_r($desc);
						$cons_comps = $desc[0];
						$gsm = $desc[1];
						$dia_width = $desc[2];
					} else {
						//$cons_comps='';
						$cons_comps_data = explode(",", $fabric_desc_arr[$row[csf('prod_id')]]['desc']);

						$cons_comps = $cons_comps_data[0] . ' ' . $cons_comps_data[1];
						//print_r($cons_comps_data);
						/*$z="";
						foreach($cons_comps_data as $val)
						{
							if($z!="")
							{

								$cons_comps.=$val." ";
							}
							//$z++;
						}*/
						$gsm = $fabric_desc_arr[$row[csf('prod_id')]]['gsm'];
						$dia_width = $fabric_desc_arr[$row[csf('prod_id')]]['dia'];
						//$lot=$fabric_desc_arr[$row[csf('prod_id')]]['lot'];
						//$yarn_count=$fabric_desc_arr[$row[csf('prod_id')]]['yarn_count'];
						//$brand=$fabric_desc_arr[$row[csf('prod_id')]]['brand'];

					}
					$brand = $lot = $yarn_lot_arr[$row[csf('prod_id')]][$row[csf('po_id')]]['brand_id'];
					$lot = $yarn_lot_arr[$row[csf('prod_id')]][$row[csf('po_id')]]['lot'];
					$brand_id = explode(',', $brand);
					$brand_value = "";
					foreach ($brand_id as $val) {
						if ($val > 0) {
							if ($brand_value == '') $brand_value = $brand_name[$val]; else $brand_value .= ", " . $brand_name[$val];
						}
					}
					$y_count_id = $yarn_lot_arr[$row[csf('prod_id')]][$row[csf('po_id')]]['yarn_count'];
					if ($db_type == 0) {
						$count_id = array_unique(explode(",", $y_count_id));
					} else {
						$count_id = array_unique(explode(",", $y_count_id));
					}
					//print_r( $count_id).'aziz';
					//array_unique(explode(',',$y_count));
					$yarn_count_value = '';
					foreach ($count_id as $val) {
						if ($val > 0) {
							if ($yarn_count_value == '') $yarn_count_value = $yarncount[$val]; else $yarn_count_value .= ", " . $yarncount[$val];
						}
					}
				
					if($dyeing_type_id==2)//CBP dyeing
					{
						if($load_unload == 2)
						{
							$load_prod_qnty=$load_qty_arr2[$row[csf('prod_id')]][$gsm][$row[csf('width_dia_type')]];
							//echo $row[csf('prod_id')].'='.$gsm.'='.$row[csf('width_dia_type')];
						}
						else
						{
							$unload_prod_qnty=$previ_unload_qty_arr[$row[csf('prod_id')]][$gsm][$row[csf('width_dia_type')]];
							//echo  $row[csf('batch_qnty')].'='.$unload_prod_qnty;
							$load_prod_qnty = $row[csf('batch_qnty')]-$unload_prod_qnty;
						}
						
					}
					else
					{
						if($load_unload == 2)
						{
							$load_prod_qnty=$load_qty_arr2[$row[csf('prod_id')]][$gsm][$row[csf('width_dia_type')]];
							//echo $row[csf('prod_id')].'='.$gsm.'='.$row[csf('width_dia_type')];
						}
						else
						{
							$load_prod_qnty = $row[csf('batch_qnty')];
						}
					}
						//echo $dyeing_type_id.'='.$load_prod_qnty;

					if (($page_upto_id == 2 || $page_upto_id > 2) && $roll_maintained == 1) {
						//$roll_no = $fabric_roll_arr[$row[csf('roll_id')]]['roll'];
						$roll_no = $row[csf('roll_no')];

						$batch_qnty = $row[csf('batch_qnty')];
						//$prod_qnty=$row[csf('batch_qnty')];
						//$tot_batch_qty+= $row[csf('batch_qnty')];
						//$tot_prod_qty+= $row[csf('batch_qnty')];

						if ($load_unload == 2) {
								$readdata = "readonly";
								//$batch_qnty=$row[csf('batch_qnty')];
								$prod_qnty = $row[csf('batch_qnty')];
								$tot_prod_qnty += $load_prod_qnty;
							} else {
								$readdata = "";
								$prod_qnty = $row[csf('batch_qnty')];
								$tot_prod_qnty += $load_prod_qnty;
							}
					} else {
						$roll_no = $row[csf('no_of_roll')];
						$batch_qnty = $row[csf('batch_qnty')];
						//$tot_prod_qty="";
						//$tot_batch_qty+= $row[csf('batch_qnty')];
					//echo $load_prod_qnty.'XXXX'.$process_id;
						if ($load_unload == 2) {
								$readdata = "readonly";
								//$batch_qnty=$row[csf('batch_qnty')];
								$prod_qnty = $load_prod_qnty;
								$tot_prod_qnty += $load_prod_qnty;
							} else {
								$readdata = "";
								if($dyeing_type_id==2)//CBP dyeing
								{
									$prod_qnty = $load_prod_qnty;
								}
								else
								{
									$prod_qnty = $row[csf('batch_qnty')];
								}
								//$tot_prod_qnty += $load_prod_qnty;
							}

					}
					if($dyeing_type_id==2)//CBP
					{
						$readdata = "";
					}
					else
					{
						$readdata =$readdata;
					}


					?>
					<tr class="general" id="row_<? echo $i; ?>">
						<td width="70" id="sl_<? echo $i; ?>">
							 <?
							echo $i;
							?>
							<input type="hidden" id="checkRow_<? echo $i; ?>" name="checkRow_<? echo $i; ?>" >
						</td>
						<td><input type="text" name="txtconscomp_<? echo $i; ?>" id="txtconscomp_<? echo $i; ?>" class="text_boxes" style="width:170px;" value="<? echo $cons_comps; ?>" disabled/></td>
						<td><input type="text" name="txtgsm_<? echo $i; ?>" id="txtgsm_<? echo $i; ?>" class="text_boxes" style="width:40px;" value="<? echo trim($gsm); ?>"/></td>
						<td><input type="text" name="txtdiawidth_<? echo $i; ?>" id="txtdiawidth_<? echo $i; ?>" class="text_boxes" style="width:40px;" value="<? echo $dia_width; ?>" disabled/></td>
						<td><input type="text" name="txtdiatype_<? echo $i; ?>" id="txtdiatype_<? echo $i; ?>" class="text_boxes" style="width:70px;" value="<? echo $fabric_typee[$row[csf('width_dia_type')]]; ?>" disabled/>
						<input type="hidden" name="hiddendiatypeid_<? echo $i; ?>" id="hiddendiatypeid_<? echo $i; ?>" value="<? echo $row[csf('width_dia_type')]; ?>" readonly/>
						<input type="hidden" name="txtbarcode_<? echo $i; ?>" id="txtbarcode_<? echo $i; ?>" value="<? //echo $row[csf('barcode_no')]; ?>" readonly/>

						</td>
						<td><input type="text" name="txtroll_<? echo $i; ?>" id="txtroll_<? echo $i; ?>" class="text_boxes"   style="width:40px;" value="<? echo $roll_no; ?>"/> <input type="hidden" name="rollid_<? echo $i; ?>" id="rollid_<? echo $i; ?>" style="width:50px;" value="<? echo $row[csf('roll_id')]; ?>" class="text_boxes_numeric"/>
						</td>
						<td><input type="text" name="txtbatchqnty_<? echo $i; ?>" id="txtbatchqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px; background:<? echo $batch_filed; ?>" onKeyUp="calculate_production_qnty2();" value="<? echo number_format($row[csf('batch_qnty')], 2, ".", ""); ?>" <? echo $batch_filed_read ?>/>
						</td>
						<td><input type="text" name="txtprodqnty_<? echo $i; ?>" id="txtprodqnty_<? echo $i; ?>"class="text_boxes_numeric" style="width:60px;" onKeyUp="calculate_production_qnty();" value="<? echo number_format($prod_qnty, 2, ".", ""); ?>" <? echo $readdata ?> />
                        <input type="text" name="hiddenprodqnty_<? echo $i; ?>" id="hiddenprodqnty_<? echo $i; ?>"class="text_boxes_numeric" style="width:60px;"  value="<? echo number_format($prod_qnty, 2, ".", ""); ?>" <? echo $readdata ?> />
                        </td>
						<td><input type="text" name="txtrate_<? echo $i; ?>" id="txtrate_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px;" value="<? echo $row[csf('rate')]; ?>" readonly/></td>
						<td><input type="text" name="txtamount_<? echo $i; ?>" id="txtamount_<? echo $i; ?>" class="text_boxes_numeric" style="width:70px;" value="<? echo $row[csf('amount')]; ?>" readonly/></td>
						<td><input type="text" name="txtlot_<? echo $i; ?>" id="txtlot_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px;" value="<? echo $lot; ?>" readonly  disabled/></td>
						<td><input type="text" name="txtyarncount_<? echo $i; ?>" id="txtyarncount_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px;" value="<? echo $yarn_count_value; ?>"  readonly disabled/></td>
						<td><input type="text" name="txtbrand_<? echo $i; ?>" id="txtbrand_<? echo $i; ?>"  class="text_boxes_numeric" style="width:60px;" value="<? echo $brand_value; ?>" readonly  disabled/>
							<input type="hidden" name="txtprodid_<? echo $i; ?>" id="txtprodid_<? echo $i; ?>" value="<? echo $row[csf('prod_id')]; ?>"/>
							<input type="hidden" name="txtdeterid_<? echo $i; ?>" id="txtdeterid_<? echo $i; ?>" value="<? echo $fabric_desc_arr[$row[csf('prod_id')]]['detarmination_id']; ?>"/>
							<input type="hidden" name="updateiddtls_<? echo $i; ?>" id="updateiddtls_<? echo $i; ?>" class="text_boxes" value="<? //echo $row[csf('id')];?>" readonly/>
							<input type="hidden" name="txtremark_<? echo $i; ?>" id="txtremark_<? echo $i; ?>"  class="text_boxes" style="width:70px;" value="<? //echo $row[csf('remarks')]; ?>" disabled/>
						</td>

					</tr>
					<?
					$tot_batch_qty += $row[csf('batch_qnty')];
					$tot_prod_qnty += $prod_qnty;
					//$tot_batch_qty+= $row[csf('batch_qnty')];
					$i++;
				}
			} else {
				?>

				<tr class="general" id="row_1">
					<td> 1</td>
					<td><input type="text" name="txtconscomp_1" id="txtconscomp_1" class="text_boxes" style="width:170px;" readonly disabled/></td>
					<td><input type="text" name="txtgsm_1" id="txtgsm_1" class="text_boxes" style="width:40px;" readonly  disabled/></td>
					<td><input type="text" name="txtdiawidth_1" id="txtdiawidth_1" class="text_boxes" style="width:40px;"  readonly disabled/></td>
					<td><input type="text" name="txtdiatype_1" id="txtdiatype_1" class="text_boxes" style="width:70px;" readonly disabled/></td>
					<td><input type="text" name="txtroll_1" id="txtroll_1" class="text_boxes" style="width:40px;" readonly disabled/>
					<input type="hidden" name="rollid_1" id="rollid_1" style="width:50px;" class="text_boxes_numeric"/>
					<input type="hidden" name="txtbarcode_1" id="txtbarcode_1" style="width:50px;" class="text_boxes_numeric"/>
					</td>
					<td><input type="text" name="txtbatchqnty_1" id="txtbatchqnty_1" class="text_boxes_numeric" style="width:60px;"/></td>
					<td><input type="text" name="txtprodqnty_1" id="txtprodqnty_1" class="text_boxes_numeric" style="width:60px;"/></td>
					<td><input type="text" name="txtrate_1" id="txtrate_1" class="text_boxes_numeric" style="width:60px;"  readonly/></td>
					<td><input type="text" name="txtamount_1" id="txtamount_1" class="text_boxes_numeric" style="width:70px;" readonly/></td>
					<td><input type="text" name="txtlot_1" id="txtlot_1" class="text_boxes_numeric" style="width:40px;" readonly disabled/></td>
					<td><input type="text" name="txtyarncount_1" id="txtyarncount_1" class="text_boxes_numeric" style="width:60px;" readonly disabled/></td>
					<td><input type="text" name="txtbrand_1" id="txtbrand_1" class="text_boxes_numeric" style="width:60px;" readonly disabled/>
						<input type="hidden" name="txtprodid_1" id="txtprodid_1"/>
						<input type="hidden" name="updateiddtls_1" id="updateiddtls_1" class="text_boxes" readonly/>
						<input type="hidden" name="txtremark_1" id="txtremark_1"  class="text_boxes" style="width:70px;"  disabled/>
					</td>

				</tr>
				<?

			}
			?>
			<tr>
				<td colspan="6" align="right"><b>Sum:</b> <? //echo $b_qty;?> </td>
				<td align="right"><b><input type="text" name="total_batch_qnty" id="total_batch_qnty"class="text_boxes_numeric" style="width:60px" value="<? if($tot_batch_qty>100) echo number_format($tot_batch_qty, 2);else echo $tot_batch_qty; ?> " readonly/></b></td>
				<td><input type="text" name="total_production_qnty" id="total_production_qnty" class="text_boxes_numeric" style="width:60px" value="<? if($tot_prod_qnty>100) echo number_format($tot_prod_qnty, 2);else echo $tot_prod_qnty; ?> " readonly/></td>
				<td align="right"></td>
				<td align="right"><input type="text" name="total_amount" id="total_amount" class="text_boxes_numeric" style="width:70px" value="<? echo $tot_amount; ?>" readonly/></td>
			</tr>
			<?
			exit();
		}
	}
	else //Trim Batch Start.
	{


			$batch_id_search = sql_select("select a.batch_id from pro_fab_subprocess a,pro_fab_subprocess_dtls b where a.id=b.mst_id and a.batch_id=$batch_id and company_id=$company_id and entry_form=35 and a.status_active=1 and b.status_active=1 $load_unload_cond");
				if($dyeing_type_id==2)//multi is No //CBP Dyeing
				{
					
					$batch_insert_cond = "";
					
				}
				else
				{
					if($multi_dyeing==2)//multi is No //CBP Dyeing
					{
					$batch_insert_cond = "";
					if (count($batch_id_search) > 0) $batch_insert_cond = " and a.id!=" . $batch_id_search[0][csf('batch_id')] . "";
					}
				}

			if ($db_type == 0) $select_group = " group by b.id,b.item_description";
			else if ($db_type == 2) $select_group = "group by b.item_description,b.width_dia_type,a.entry_form,b.prod_id,b.po_id";


				$sql_result ="select a.entry_form, 0 as prod_id,0 as po_id,0 as no_of_roll,b.remarks,b.item_description, 0 as width_dia_type, sum(b.trims_wgt_qnty) as batch_qnty,0 as roll_no, 0 as barcode_no from pro_batch_trims_dtls b,pro_batch_create_mst a where a.id=b.mst_id and   b.mst_id=$batch_id and a.entry_form in(136) and b.status_active=1 and b.is_deleted=0 $batch_insert_cond group by a.entry_form,b.remarks,b.item_description";
				$result=sql_select($sql_result);

		//	echo count($result).'DD';//trims_wgt_qnty,remarks,item_description
			if (count($result) > 0) {

				$i = 1;
				$tot_prod_qty = 0;
				$tot_batch_qty = 0;
				foreach ($result as $row)
				 {
					//$desc=explode(",",$row[csf('item_description')]);
					$cons_comps = $row[csf('item_description')];

						$roll_no = $row[csf('no_of_roll')];
						$batch_qnty = $row[csf('batch_qnty')];
						//$tot_prod_qty="";
						//$tot_batch_qty+= $row[csf('batch_qnty')];
						if ($load_unload == 2) {
								$readdata = "readonly";
								//$batch_qnty=$row[csf('batch_qnty')];
								$prod_qnty = $row[csf('batch_qnty')];
								$tot_prod_qnty += $row[csf('batch_qnty')];
							} else {
								$readdata = "";
								$prod_qnty = $row[csf('batch_qnty')];
								$tot_prod_qnty += $row[csf('batch_qnty')];
							}
					if($dyeing_type_id==2)//CBP
					{
						$readdata = "";
					}
					else
					{
						$readdata =$readdata;
					}
					//echo $batch_filed_read.'DD';
					?>
					<tr class="general" id="row_<? echo $i; ?>">
						<td width="70" id="sl_<? echo $i; ?>">
							<?
							if (($page_upto_id == 2 || $page_upto_id > 2) && $roll_maintained == 1) { ?>
								<input type="checkbox" id="checkRow_<? echo $i; ?>" name="checkRow_<? echo $i; ?>" checked>
								<?
								echo $i;
							} else {
								echo $i;
							}
							?>
						</td>
						<td><input type="text" name="txtconscomp_<? echo $i; ?>" id="txtconscomp_<? echo $i; ?>"  class="text_boxes" style="width:170px;" value="<? echo $cons_comps; ?>" disabled/></td>
						<td><input type="text" name="txtgsm_<? echo $i; ?>" id="txtgsm_<? echo $i; ?>" class="text_boxes" style="width:40px;" value="<? echo trim($gsm); ?>"/></td>
						<td><input type="text" name="txtdiawidth_<? echo $i; ?>" id="txtdiawidth_<? echo $i; ?>" class="text_boxes" style="width:40px;" value="<? echo $dia_width; ?>" disabled/></td>
						<td><input type="text" name="txtdiatype_<? echo $i; ?>" id="txtdiatype_<? echo $i; ?>" class="text_boxes" style="width:70px;" value="<? echo $fabric_typee[$row[csf('width_dia_type')]]; ?>" disabled/><input type="hidden" name="hiddendiatypeid_<? echo $i; ?>" id="hiddendiatypeid_<? echo $i; ?>" value="<? echo $row[csf('width_dia_type')]; ?>" readonly/>
						<input type="hidden" name="txtbarcode_<? echo $i; ?>" id="txtbarcode_<? echo $i; ?>" value="<? //echo $row[csf('barcode_no')]; ?>" readonly/>

						</td>
						<td><input type="text" name="txtroll_<? echo $i; ?>" id="txtroll_<? echo $i; ?>" class="text_boxes"   style="width:40px;" value="<? echo $roll_no; ?>"/>
						 <input type="hidden" name="rollid_<? echo $i; ?>" id="rollid_<? echo $i; ?>" style="width:50px;" value="<? echo $row[csf('roll_id')]; ?>" class="text_boxes_numeric"/>
						</td>
						<td><input type="text" name="txtbatchqnty_<? echo $i; ?>" id="txtbatchqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px; background:<? echo $batch_filed; ?>" onKeyUp="calculate_production_qnty2();" value="<? echo number_format($row[csf('batch_qnty')], 2, ".", ""); ?>" <? echo $batch_filed_read ?>/>
						</td>
						<td><input type="text" name="txtprodqnty_<? echo $i; ?>" id="txtprodqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px;" onKeyUp="calculate_production_qnty();" value="<? echo number_format($prod_qnty, 2, ".", ""); ?>" <? echo $readdata ?> /></td>
						<td><input type="text" name="txtrate_<? echo $i; ?>" id="txtrate_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px;" value="<? echo $row[csf('rate')]; ?>" readonly/></td>
						<td><input type="text" name="txtamount_<? echo $i; ?>" id="txtamount_<? echo $i; ?>" class="text_boxes_numeric" style="width:70px;" value="<? echo $row[csf('amount')]; ?>"  readonly/></td>
						<td><input type="text" name="txtlot_<? echo $i; ?>" id="txtlot_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px;" value="<? echo $lot; ?>" readonly  disabled/></td>
						<td><input type="text" name="txtyarncount_<? echo $i; ?>" id="txtyarncount_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px;" value="<? echo $yarn_count_value; ?>" readonly disabled/></td>
						<td><input type="text" name="txtbrand_<? echo $i; ?>" id="txtbrand_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px;" value="<? echo $brand_value; ?>" readonly disabled/>
					<input type="hidden" name="txtprodid_<? echo $i; ?>" id="txtprodid_<? echo $i; ?>" value="<? echo $row[csf('prod_id')]; ?>"/>
					<input type="hidden" name="txtdeterid_<? echo $i; ?>" id="txtdeterid_<? echo $i; ?>"  value="<? echo $fabric_desc_arr[$row[csf('prod_id')]]['detarmination_id']; ?>"/>
					<input type="hidden" name="updateiddtls_<? echo $i; ?>" id="updateiddtls_<? echo $i; ?>" class="text_boxes" value="<? //echo $row[csf('id')];?>" readonly/>
					<input type="hidden" name="txtremark_<? echo $i; ?>" id="txtremark_<? echo $i; ?>"  class="text_boxes" style="width:170px;" value="<? echo $row[csf('remarks')]; ?>" disabled/>
						</td>

					</tr>
					<?
					$tot_batch_qty += $row[csf('batch_qnty')];
					//$tot_batch_qty+= $row[csf('batch_qnty')];
					$i++;
				}
			} else {
				?>

				<tr class="general" id="row_1">
					<td> 1</td>
					<td><input type="text" name="txtconscomp_1" id="txtconscomp_1" class="text_boxes" style="width:170px;" readonly disabled/></td>
					<td><input type="text" name="txtgsm_1" id="txtgsm_1" class="text_boxes" style="width:40px;" readonly disabled/></td>
					<td><input type="text" name="txtdiawidth_1" id="txtdiawidth_1" class="text_boxes" style="width:40px;" readonly disabled/></td>
					<td><input type="text" name="txtdiatype_1" id="txtdiatype_1" class="text_boxes" style="width:70px;" readonly disabled/></td>
					<td><input type="text" name="txtroll_1" id="txtroll_1" class="text_boxes" style="width:40px;" readonly   disabled/>
					<input type="hidden" name="rollid_1" id="rollid_1" style="width:50px;" class="text_boxes_numeric"/>
					<input type="hidden" name="txtbarcode_1" id="txtbarcode_1" style="width:50px;" class="text_boxes_numeric"/>
					</td>
					<td><input type="text" name="txtbatchqnty_1" id="txtbatchqnty_1" class="text_boxes_numeric" style="width:60px;"/></td>
					<td><input type="text" name="txtprodqnty_1" id="txtprodqnty_1" class="text_boxes_numeric"  style="width:60px;"/></td>
					<td><input type="text" name="txtrate_1" id="txtrate_1" class="text_boxes_numeric" style="width:60px;"  readonly/></td>
					<td><input type="text" name="txtamount_1" id="txtamount_1" class="text_boxes_numeric" style="width:70px;" readonly/></td>
					<td><input type="text" name="txtlot_1" id="txtlot_1" class="text_boxes_numeric" style="width:40px;" readonly disabled/></td>
					<td><input type="text" name="txtyarncount_1" id="txtyarncount_1" class="text_boxes_numeric" style="width:60px;" readonly disabled/></td>
					<td><input type="text" name="txtbrand_1" id="txtbrand_1" class="text_boxes_numeric" style="width:60px;" readonly disabled/>
						<input type="hidden" name="txtprodid_1" id="txtprodid_1"/>
						<input type="hidden" name="updateiddtls_1" id="updateiddtls_1" class="text_boxes" readonly/>
						<input type="hidden" name="txtremark_1" id="txtremark_1"  class="text_boxes" style="width:170px;"  disabled/>
					</td>

				</tr>
				<?

			}
			?>
			<tr>
				<td colspan="6" align="right"><b>Sum:</b> <? //echo $b_qty;
					?> </td>

				<td align="right"><b><input type="text" name="total_batch_qnty" id="total_batch_qnty" class="text_boxes_numeric" style="width:60px" value="<? if($tot_batch_qty>100) echo number_format($tot_batch_qty, 2);else echo $tot_batch_qty; ?> " readonly/></b></td>
				<td><input type="text" name="total_production_qnty" id="total_production_qnty" class="text_boxes_numeric"  style="width:60px" value="<? if($tot_prod_qnty>100) echo number_format($tot_prod_qnty, 2);else echo $tot_prod_qnty;   ?> " readonly/></td>
				<td align="right"></td>
				<td align="right"><input type="text" name="total_amount" id="total_amount" class="text_boxes_numeric" style="width:70px" value="<? echo $tot_amount; ?>" readonly/></td>
			</tr>
			<?
			exit();

	}
}


if ($action == 'issue_show_fabric_desc_listview') {
	//print($data);
	$ex_data = explode('_', $data);
	$batch_id = $ex_data[0];
	$load_unload = $ex_data[2];
	$process_id = $ex_data[3];

	$company_id = return_field_value("company_id", "pro_batch_create_mst", "id ='$batch_id' and is_deleted=0 and status_active=1");
	$roll_maintained = return_field_value("fabric_roll_level", "variable_settings_production", "company_name ='$company_id' and item_category_id=50 and variable_list=3 and is_deleted=0 and status_active=1");
	$page_upto_id = return_field_value("page_upto_id", "variable_settings_production", "company_name =$company_id and item_category_id=50 and variable_list=3 and is_deleted=0 and status_active=1");

	if ($load_unload == 1) $load_unload_cond = "and a.load_unload_id in(1)";
	else if ($load_unload == 2) $load_unload_cond = "and a.load_unload_id in(2)";
	if ($load_unload == 1) {
		$process_ids = return_field_value("process_id", "pro_batch_create_mst", "id ='$batch_id' and is_deleted=0 and status_active=1");
		$process_ids = explode(",", $process_ids);
		$procss = in_array(31, $process_ids);
		if ($procss == "") {
			$batch_filed = "yellow";
			$batch_filed_read = "readonly";
		}
	}
	$fabric_desc_arr = array();
	$prodData = sql_select("select id,detarmination_id, item_description, lot,gsm,yarn_count_id,brand, dia_width from product_details_master where item_category_id=13");
	foreach ($prodData as $row) {
		$fabric_desc_arr[$row[csf('id')]]['desc'] = $row[csf('item_description')];
		$fabric_desc_arr[$row[csf('id')]]['gsm'] = $row[csf('gsm')];
		$fabric_desc_arr[$row[csf('id')]]['dia'] = $row[csf('dia_width')];
		$fabric_desc_arr[$row[csf('id')]]['detarmination_id'] = $row[csf('detarmination_id')];
	}
	$fabric_roll_arr = array();
	$prollData = sql_select("select id,roll_no,barcode_no from pro_roll_details where  status_active=1 and is_deleted=0");
	foreach ($prollData as $row) {
		$fabric_roll_arr[$row[csf('id')]]['roll'] = $row[csf('roll_no')];
	}
	$yarn_lot_arr = array();
	if ($db_type == 0) {
		$yarn_lot_data = sql_select("select   b.po_breakdown_id, a.prod_id, group_concat(distinct(a.yarn_lot)) as yarn_lot, group_concat(distinct(a.brand_id)) as brand_id,group_concat( distinct a.yarn_count,'**') AS yarn_count from pro_grey_prod_entry_dtls a, order_wise_pro_details b where  a.id=b.dtls_id and b.entry_form in(2,22) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.prod_id, b.po_breakdown_id");
	} else if ($db_type == 2) {
		$yarn_lot_data = sql_select("select  b.po_breakdown_id, a.prod_id, LISTAGG(CAST(a.yarn_lot AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY a.yarn_lot) as yarn_lot, listagg(cast(a.yarn_count as varchar2(4000)),'**') within group (order by a.yarn_count) AS yarn_count, LISTAGG(a.brand_id,',') WITHIN GROUP ( ORDER BY a.brand_id) as brand_id from pro_grey_prod_entry_dtls a, order_wise_pro_details b where a.id=b.dtls_id and b.entry_form in(2,22)   and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.prod_id, b.po_breakdown_id");
	}
	foreach ($yarn_lot_data as $rows) {
		$yarn_lot = explode(",", $rows[csf('yarn_lot')]);
		$brand_id = explode(",", $rows[csf('brand_id')]);
		$yarn_lot_arr[$rows[csf('prod_id')]][$rows[csf('po_breakdown_id')]]['lot'] = implode(", ", array_unique($yarn_lot));
		$yarn_lot_arr[$rows[csf('prod_id')]][$rows[csf('po_breakdown_id')]]['yarn_count'] = $rows[csf('yarn_count')];
		$yarn_lot_arr[$rows[csf('prod_id')]][$rows[csf('po_breakdown_id')]]['brand_id'] = implode(", ", array_unique($brand_id));
	}
	$result = sql_select("select a.entry_form,b.width_dia_type,b.prod_id,b.po_id,b.roll_no,b.roll_id ,b.item_description,b.width_dia_type, sum(b.batch_qnty) as batch_qnty from pro_batch_create_dtls b,pro_batch_create_mst a  where b.mst_id=$batch_id and a.id=b.mst_id and  a.entry_form in(0,36) and b.status_active=1 and b.is_deleted=0 group by a.entry_form,b.width_dia_type,b.prod_id,b.po_id,b.roll_no,b.roll_id ,b.item_description,b.width_dia_type");
	foreach ($result as $row) {
		if ($row[csf('entry_form')] == 36) {
			$desc = explode(",", $row[csf('item_description')]);
			//print_r($desc);
			$cons_comps = $desc[0];
			$gsm = $desc[1];
			$dia_width = $desc[2];
		} else {
			$cons_comps = '';
			$cons_comps_data = explode(",", $fabric_desc_arr[$row[csf('prod_id')]]['desc']);
			//print_r($cons_comps_data);
			$z = 0;
			foreach ($cons_comps_data as $val) {
				if ($z != 0) {
					$cons_comps .= $val . " ";
				}
				$z++;
			}
			$gsm = $fabric_desc_arr[$row[csf('prod_id')]]['gsm'];
			$dia_width = $fabric_desc_arr[$row[csf('prod_id')]]['dia'];
			//$lot=$fabric_desc_arr[$row[csf('prod_id')]]['lot'];
			//$yarn_count=$fabric_desc_arr[$row[csf('prod_id')]]['yarn_count'];
			//$brand=$fabric_desc_arr[$row[csf('prod_id')]]['brand'];

		}
		$brand = $lot = $yarn_lot_arr[$row[csf('prod_id')]][$row[csf('po_id')]]['brand_id'];
		$lot = $yarn_lot_arr[$row[csf('prod_id')]][$row[csf('po_id')]]['lot'];
		$brand_id = explode(',', $brand);
		$brand_value = "";
		foreach ($brand_id as $val) {
			if ($val > 0) {
				if ($brand_value == '') $brand_value = $brand_name[$val]; else $brand_value .= ", " . $brand_name[$val];
			}
		}
		$y_count_id = $yarn_lot_arr[$row[csf('prod_id')]][$row[csf('po_id')]]['yarn_count'];
		$count_id = array_unique(explode("**", $y_count_id));
		//print_r( $count_id).'aziz';
		//array_unique(explode(',',$y_count));
		$yarn_count_value = '';
		foreach ($count_id as $val) {
			if ($val > 0) {
				if ($yarn_count_value == '') $yarn_count_value = $yarncount[$val]; else $yarn_count_value .= ", " . $yarncount[$val];
			}
		}

		if (($page_upto_id == 2 || $page_upto_id > 2) && $roll_maintained == 1) {
			$roll_no = $fabric_roll_arr[$row[csf('roll_id')]]['roll'];
			//echo $row[csf('roll_id')];die;
			$batch_qnty = $row[csf('batch_qnty')];
			//$prod_qnty=$row[csf('batch_qnty')];
			//$tot_batch_qty+= $row[csf('batch_qnty')];
			//$tot_prod_qty+= $row[csf('batch_qnty')];

			if ($process_id == 31) {
				if ($load_unload == 2) {
					$readdata = "readonly";
					//$batch_qnty=$row[csf('batch_qnty')];
					$prod_qnty = $row[csf('batch_qnty')];
					$tot_prod_qnty += $row[csf('batch_qnty')];
				} else {
					$readdata = "";
					$prod_qnty = $row[csf('batch_qnty')];
					$tot_prod_qnty += $row[csf('batch_qnty')];
				}
			}
		} else {
			$roll_no = $row[csf('no_of_roll')];
			$batch_qnty = $row[csf('batch_qnty')];
			//$tot_prod_qty="";
			//$tot_batch_qty+= $row[csf('batch_qnty')];
			if ($process_id == 31) {
				if ($load_unload == 2) {
					$readdata = "readonly";
					//$batch_qnty=$row[csf('batch_qnty')];
					$prod_qnty = $row[csf('batch_qnty')];
					$tot_prod_qnty += $row[csf('batch_qnty')];
				} else {
					$readdata = "";
					$prod_qnty = $row[csf('batch_qnty')];
					$tot_prod_qnty += $row[csf('batch_qnty')];
				}
			} else {
				$batch_qnty = $row[csf('batch_qnty')];
				$prod_qnty = $row[csf('batch_qnty')];
				$tot_prod_qnty += $row[csf('batch_qnty')];
			}

		}
		?>

        <tr class="general" id="row_<? echo $i; ?>">
            <td width="70" id="sl_<? echo $i; ?>">
				<?
				echo $i;
				?>
            </td>
            <td><input type="text" name="txtconscomp_<? echo $i; ?>" id="txtconscomp_<? echo $i; ?>" class="text_boxes"
                       style=
                       "width:170px;" value="<? echo $cons_comps; ?>" disabled/></td>
            <td><input type="text" name="txtgsm_<? echo $i; ?>" id="txtgsm_<? echo $i; ?>" class="text_boxes" style="width:40px;" value="<? echo trim($gsm); ?>" disabled/></td>
            <td><input type="text" name="txtdiawidth_<? echo $i; ?>" id="txtdiawidth_<? echo $i; ?>" class="text_boxes"  style="width:40px;" value="<? echo
				$dia_width; ?>" disabled/></td>
            <td><input type="text" name="txtdiatype_<? echo $i; ?>" id="txtdiatype_<? echo $i; ?>" class="text_boxes" style="width:70px;"
                       value="<? echo $fabric_typee[$row[csf('width_dia_type')]]; ?>" disabled/>
					   <input type="hidden" name="hiddendiatypeid_<? echo $i; ?>" id="hiddendiatypeid_<? echo $i; ?>" value="<? echo $row[csf('width_dia_type')]; ?>"
                         readonly/></td>
            <td><input type="text" name="txtroll_<? echo $i; ?>" id="txtroll_<? echo $i; ?>" class="text_boxes" style="width:40px;" value="<? echo $roll_no; ?>" readonly disabled/>
				<input type="hidden" name="rollid_<? echo $i; ?>" id="rollid_<? echo $i; ?>" style="width:50px;" value="<? echo $row[csf('roll_id')]; ?>" class="text_boxes_numeric"/>
            </td>
            <td><input type="text" name="txtbatchqnty_<? echo $i; ?>" id="txtbatchqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px; background:<? echo $batch_filed; ?>" onKeyUp="calculate_production_qnty2();" value="<? echo number_format($row[csf('batch_qnty')], 2, ".", ""); ?>" <? echo $batch_filed_read ?> />
            </td>
            <td><input type="text" name="txtprodqnty_<? echo $i; ?>" id="txtprodqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px;" onKeyUp="calculate_production_qnty();" value="<? echo number_format($prod_qnty, 2, ".", ""); ?>" <? echo $readdata ?> /></td>
            <td><input type="text" name="txtrate_<? echo $i; ?>" id="txtrate_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px;" value="<? echo $row[csf('rate')]; ?>" readonly/></td>
            <td><input type="text" name="txtamount_<? echo $i; ?>" id="txtamount_<? echo $i; ?>"
                       class="text_boxes_numeric" style="width:70px;" value="<? echo $row[csf('amount')]; ?>" readonly/>
            </td>
            <td><input type="text" name="txtlot_<? echo $i; ?>" id="txtlot_<? echo $i; ?>" class="text_boxes_numeric"
                       style="width:40px;" value="<? echo $lot; ?>" readonly disabled/></td>
            <td><input type="text" name="txtyarncount_<? echo $i; ?>" id="txtyarncount_<? echo $i; ?>"
                       class="text_boxes_numeric" style="width:60px;" value="<? echo $yarn_count_value; ?>" readonly
                       disabled/></td>
            <td><input type="text" name="txtbrand_<? echo $i; ?>" id="txtbrand_<? echo $i; ?>"
                       class="text_boxes_numeric" style="width:60px;" value="<? echo $brand_value; ?>" readonly
                       disabled/>
                <input type="hidden" name="txtprodid_<? echo $i; ?>" id="txtprodid_<? echo $i; ?>"
                       value="<? echo $row[csf('prod_id')]; ?>"/>
                <input type="hidden" name="txtdeterid_<? echo $i; ?>" id="txtdeterid_<? echo $i; ?>"
                       value="<? echo $fabric_desc_arr[$row[csf('prod_id')]]['detarmination_id']; ?>"/>
                <input type="hidden" name="updateiddtls_<? echo $i; ?>" id="updateiddtls_<? echo $i; ?>"
                       class="text_boxes" value="<? //echo $row[csf('id')];
				?>" readonly/>
            </td>
        </tr>


		<?

		$b_qty += $row[csf('batch_qnty')];
		$i++;
	}
	?>
    <tr>
        <td colspan="6" align="right"><b>Sum:</b> <? //echo $b_qty;
			?> </td>

        <td align="right"><b><input type="text" name="total_batch_qnty" id="total_batch_qnty" class="text_boxes_numeric"
                                    style="width:60px" value="<? echo number_format($tot_batch_qty, 2); ?> " readonly/></b>
        </td>
        <td><input type="text" name="total_production_qnty" id="total_production_qnty" class="text_boxes_numeric"
                   style="width:60px" value="<? echo number_format($tot_prod_qty, 2); ?> " readonly/></td>
        <td align="right"></td>
        <td align="right"><input type="text" name="total_amount" id="total_amount" class="text_boxes_numeric"
                                 style="width:70px" value="<? echo $tot_amount; ?>" readonly/></td>
    </tr>
	<?
	exit();
}

if($action=="check_process_start_date")
{
	$fnc_bach_start_arr=array();
	$data=explode("**",$data);
	$system_no=$data[0];
	$new_process_start_date=date("Y-m-d",strtotime($data[1]));
	$sql="select min(a.process_end_date) as process_end_date,a.batch_no from pro_fab_subprocess a,pro_fab_subprocess_dtls b where a.id=b.mst_id and a.load_unload_id=1 and a.entry_form=35 and a.status_active=1 and a.system_no=$system_no group by a.batch_no order by process_end_date";
	//echo $sql;die;
	$check_data_array=sql_select($sql,1);
	$data_array=sql_select($sql);
	foreach($data_array as $row)
	{
	$fnc_bach_start_arr[$row[csf('batch_no')]]=$row[csf('batch_no')];
	
	}
	
	if(count($fnc_bach_start_arr)==1)
	{
		$process_start_date=date("Y-m-d",strtotime($check_data_array[0][csf('process_end_date')]));
		if($new_process_start_date==$process_start_date)
		{
			echo "";
		}
		else
		{
			echo date("Y-m-d",strtotime($check_data_array[0][csf('process_end_date')]));
		}
	}
	else if(count($fnc_bach_start_arr)>1)
	{
		echo date("Y-m-d",strtotime($check_data_array[0][csf('process_end_date')]));
		//echo date("Y-m-d",strtotime($data_array[0][csf('process_end_date')]));
	}
	else
	{
		echo "";
	}
	exit();
}

if($action=="check_process_production_date")
{
	$data=explode("**",$data);
	$system_no=$data[0];
	$new_process_end_date=date("Y-m-d",strtotime($data[1]));
	$fnc_bach_arr=array();
	$sql="select min(a.process_end_date) as production_date,a.batch_no from pro_fab_subprocess a,pro_fab_subprocess_dtls b where a.id=b.mst_id and a.load_unload_id=2 and a.entry_form=35 and a.status_active=1 and a.system_no=$system_no group by a.batch_no";
	//echo $sql;die;
	$check_data_array=sql_select($sql,1);
	$data_array=sql_select($sql);
	foreach($data_array as $row)
	{
	$fnc_bach_arr[$row[csf('batch_no')]]=$row[csf('batch_no')];
	}
	$tot_batch_no=count($fnc_bach_arr);
	//echo count($fnc_bach_arr).'XZZZ'.$check_data_array[0][csf('production_date')];
	
	if(count($fnc_bach_arr)!=1)
	{
		$production_date=date("Y-m-d",strtotime($check_data_array[0][csf('production_date')])).'#'.$tot_batch_no;
		if($new_process_end_date==$production_date)
		{
			echo "";
		}
		else
		{
			echo date("Y-m-d",strtotime($check_data_array[0][csf('production_date')])).'#'.$tot_batch_no;
		}
	}
	else if(count($fnc_bach_arr)>1)
	{
		echo date("Y-m-d",strtotime($check_data_array[0][csf('production_date')])).'#'.$tot_batch_no;
		//echo date("Y-m-d",strtotime($data_array[0][csf('process_end_date')]));
	}
	else
	{
		echo "";
	}
	exit();
}

/*if($action=="machine_load_status")
{
	$data=explode("**",$data);
	if ($db_type == 0) {
		$process_date = change_date_format(trim($data[0]), "yyyy-mm-dd", "-") ;
	} else {
		$process_date = change_date_format(trim($data[0]), '', '', 1);
	}


	if( $data[3] == ""){
		$update_id_cond = "";
	}else{
		$update_id_cond = " and id != $data[3]";
	}

	$sql="select batch_no  from pro_fab_subprocess where load_unload_id=1 and entry_form=35 and process_end_date ='$process_date' and  floor_id=$data[1] and machine_id=$data[2] $update_id_cond";

	$data_array=sql_select($sql,1);
	if(count($data_array)>0)
	{
		echo "1_".$data_array[0][csf('batch_no')];
	}
	else
	{
		echo "0_";
	}
	exit();
}*/


if ($action == "save_update_delete") {
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));
	
	$batch_no = str_replace("'", "", $txt_batch_no);
	$batch_id = str_replace("'", "", $txt_batch_ID);
	$batch_no_saved = return_field_value("batch_no", "pro_batch_create_mst", "id =$batch_id and is_deleted=0 and status_active=1","batch_no");
	
	if($batch_no!=$batch_no_saved)
	{
		echo "23**Please write the correct batch no";
		disconnect($con);
		die;
	}
	
	if ($operation == 0)  // Insert Here
	{
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}
		//echo "10**";die;
		$multi_dyeing = str_replace("'", "", $hidden_double_dyeing);
		$cbo_dyeing_type = str_replace("'", "", $cbo_dyeing_type);
		if($multi_dyeing=="" || $multi_dyeing==0 || $multi_dyeing==2) $multi_dyeing=2;else $multi_dyeing=$multi_dyeing;
		$field_array = "";
		$data_array = "";
		//echo "insert into PRO_FAB_DYEING_FOCUS (ID,BATCH_NO,production_date) values (1,'abc')";
		
		
		$id = return_next_id("id", "pro_fab_subprocess", 1);
		//echo $cbo_load_unload;die;multi_batch_load_id
		if (str_replace("'", '', $cbo_load_unload) == 2) {
			//if (str_replace("'", '', $txt_process_id) == 31) {
				$sql_data = "select id, batch_id from pro_fab_subprocess where  company_id=" . $cbo_company_id . " and  batch_id=" . $txt_batch_ID . " and load_unload_id=1 and entry_form=35 and is_deleted=0 and status_active=1";
				$data_array = sql_select($sql_data);
				if (count($data_array) > 0) {
					//secho "1**" . $data_array[0][csf('batch_id')];
				} else {
					echo "100**" . 'Without Load  Unload Not Allow';
					disconnect($con);
					die;
				}
			//}

			if($multi_dyeing==2) // is Multi no
			{
				if($cbo_dyeing_type==1) //CBP Dyeing
				{
					$sql_unload="select id, batch_id from pro_fab_subprocess where  company_id=".$cbo_company_id." and  batch_id=".$txt_batch_ID." and load_unload_id=2 and entry_form=35 and is_deleted=0 and status_active=1";
					$unload_data_array=sql_select($sql_unload);
					if(count($unload_data_array)>0)
					{
						echo "11**".'Duplicate Unload Found';
						disconnect($con);
						die;
					}
				}
			}
		}
		if (str_replace("'", '', $cbo_load_unload) == 1) {
		if($multi_dyeing==2 && $cbo_dyeing_type==1) // is Multi no //CBP Dyeing
			{
				$sql_load="select id, batch_id from pro_fab_subprocess where  company_id=".$cbo_company_id." and  batch_id=".$txt_batch_ID." and load_unload_id=1 and entry_form=35 and is_deleted=0 and status_active=1";
				$load_data_array=sql_select($sql_load);
				if(count($load_data_array)>0)
				{
					echo "13**".'Duplicate load Found';
					disconnect($con);
					die;
				}
			}
		}
		$page_upto_id = return_field_value("page_upto_id", "variable_settings_production", "company_name =$cbo_company_id and item_category_id=50 and variable_list=3 and is_deleted=0 and status_active=1");

		if ($cbo_load_unload == "'1'") {
			$txt_system_no = str_replace("'", "", $txt_system_no);
			$mst_update_id = str_replace("'", "", $id);
			if ($txt_system_no == "") $system_no = $mst_update_id + 1; else $system_no = $txt_system_no;

			$field_array = "id,company_id,system_no,service_source,service_company,received_chalan,issue_chalan,issue_challan_mst_id,batch_no,batch_id,batch_ext_no,process_id,ltb_btb_id,water_flow_meter,process_end_date,end_hours,end_minutes,machine_id,floor_id,load_unload_id,entry_form,multi_dyeing_id,remarks,dyeing_type_id,hour_load_meter,multi_batch_load_id,inserted_by,insert_date";
			$data_array = "(" . $id . "," . $cbo_company_id . "," . $system_no . "," . $cbo_service_source . "," . $cbo_service_company . "," . $txt_recevied_chalan . "," . $txt_issue_chalan . "," . $txt_issue_mst_id . "," . $txt_batch_no . "," . $txt_batch_ID . "," . $txt_ext_id . "," . $txt_process_id . "," . $cbo_ltb_btb . "," . $txt_water_flow . "," . $txt_process_start_date . "," . $txt_start_hours . "," . $txt_start_minutes . "," . $cbo_machine_name . "," . $cbo_floor . "," . $cbo_load_unload . ",35," . $hidden_double_dyeing . "," . $txt_remarks . "," . $cbo_dyeing_type . "," . $txt_load_meter . "," . $cbo_yesno . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

//cbo_dyeing_type
			$id_dtls = return_next_id("id", "pro_fab_subprocess_dtls", 1);
			if (($page_upto_id == 2 || $page_upto_id > 2) && str_replace("'", "", $roll_maintained) == 1) {
				$field_array_dtls = "id, mst_id,entry_page,prod_id,const_composition,gsm,dia_width,width_dia_type,batch_qty,roll_no,barcode_no,load_unload_id,roll_id,production_qty,remarks,inserted_by,insert_date";
				for ($i = 1; $i <= $total_row; $i++) {
					$checked_tr = "checkRow_" . $i;
					if (str_replace("'", "", $$checked_tr) == 1) {
						$prod_id = "txtprodid_" . $i;
						$txtconscomp = "txtconscomp_" . $i;
						$txtgsm = "txtgsm_" . $i;
						$txtdiawidth = "txtdiawidth_" . $i;
						$txtdiawidth = "txtdiatype_" . $i;
						$txtbatchqnty = "txtbatchqnty_" . $i;
						$txtroll = "txtroll_" . $i;
						$txtbarcode="txtbarcode_".$i;
						$rollid = "rollid_" . $i;
						$txtremark="txtremark_".$i;
						//$txtyarncount="txtyarncount_".$i;
						//$txtbrand="txtbrand_".$i;
						$txtproductionqty = "txtprodqnty_" . $i;
						$txtdiatypeID = "hiddendiatypeid_" . $i;
						$Itemprod_id = str_replace("'", "", $$prod_id);
						if ($data_array_dtls != "") $data_array_dtls .= ",";
						$data_array_dtls .= "(" . $id_dtls . "," . $mst_update_id . ",35," . $Itemprod_id . "," . $$txtconscomp . "," . $$txtgsm . "," . $$txtdiawidth . "," . $$txtdiatypeID . "," . $$txtbatchqnty . "," . $$txtroll . "," . $$txtbarcode . ",1," . $$rollid . "," . $$txtproductionqty . "," . $$txtremark . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
						$id_dtls = $id_dtls + 1;
					}
					//print_r($data_array_dtls);die;
				}

			} else {
				$field_array_dtls = "id, mst_id,entry_page,prod_id,const_composition,gsm,dia_width,width_dia_type,batch_qty,no_of_roll,load_unload_id,production_qty,remarks,inserted_by,insert_date";
				for ($i = 1; $i <= $total_row; $i++) {
					$prod_id = "txtprodid_" . $i;
					$txtconscomp = "txtconscomp_" . $i;
					$txtgsm = "txtgsm_" . $i;
					$txtdiawidth = "txtdiawidth_" . $i;
					$txtdiatype = "txtdiatype_" . $i;
					$txtbatchqnty = "txtbatchqnty_" . $i;
					$txtroll = "txtroll_" . $i;
					$txtremark="txtremark_".$i;
					//$txtyarncount="txtyarncount_".$i;
					//$txtbrand="txtbrand_".$i;
					$txtproductionqty = "txtprodqnty_" . $i;
					$hiddendiatypeid_ = "hiddendiatypeid_" . $i;
					$Itemprod_id = str_replace("'", "", $$prod_id);
					if ($data_array_dtls != "") $data_array_dtls .= ",";
					$data_array_dtls .= "(" . $id_dtls . "," . $mst_update_id . ",35," . $Itemprod_id . "," . $$txtconscomp . "," . $$txtgsm . "," . $$txtdiawidth . "," . $$hiddendiatypeid_ . "," . $$txtbatchqnty . "," . $$txtroll . ",1," . $$txtproductionqty . "," . $$txtremark . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
					$id_dtls = $id_dtls + 1;
					//print_r($data_array_dtls);die;
				}

			}
		}
		if ($cbo_load_unload == "'2'") {
			$system_no = str_replace("'", "", $txt_system_no);
			$result_id = str_replace("'", "", $cbo_result_name);
			if($result_id==4) //incomplete
			{
				$field_arr=",incomplete_result,incomplete_date";
				$field_data_arr=",".$result_id.",".$txt_process_end_date;
			}
			elseif($result_id==2) //Redying Shade Match
			{
				$field_arr=",redyeing_needed";
				$field_data_arr=",".$result_id;
			}
			elseif($result_id==1) //Shade Match
			{
				$field_arr=",shade_matched";
				$field_data_arr=",".$result_id;
			}
			else
			{
				$field_arr="";
				$field_data_arr="";
			}
			$field_array = "id,company_id,system_no,service_source,service_company,received_chalan,issue_chalan,issue_challan_mst_id,batch_no,batch_id,batch_ext_no,process_id,ltb_btb_id,water_flow_meter,process_end_date,end_hours,end_minutes,machine_id,floor_id,load_unload_id,result,entry_form,multi_dyeing_id,remarks,dyeing_type_id,hour_unload_meter,shift_name,fabric_type,production_date,booking_no,inserted_by,insert_date $field_arr";
			$data_array = "(" . $id . "," . $cbo_company_id . "," . $system_no . "," . $cbo_service_source . "," . $cbo_service_company . "," . $txt_recevied_chalan . "," . $txt_issue_chalan . "," . $txt_issue_mst_id . "," . $txt_batch_no . "," . $txt_batch_ID . "," . $txt_ext_id . "," . $txt_process_id . "," . $cbo_ltb_btb . "," . $txt_water_flow . "," . $txt_process_end_date . "," . $txt_end_hours . "," . $txt_end_minutes . "," . $cbo_machine_name . "," . $cbo_floor . "," . $cbo_load_unload . "," . $cbo_result_name . ",35," . $hidden_double_dyeing . "," . $txt_remarks . "," . $cbo_dyeing_type . "," . $txt_unload_meter . "," . $cbo_shift_name . "," . $cbo_fabric_type . "," . $txt_process_date . "," . $txt_booking_no . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "' ".$field_data_arr.")";
			$mst_update_id = str_replace("'", "", $id);//


			$id_dtls = return_next_id("id", "pro_fab_subprocess_dtls", 1);
			if (($page_upto_id == 2 || $page_upto_id > 2) && str_replace("'", "", $roll_maintained) == 1) {
				$field_array_dtls = "id, mst_id,entry_page,prod_id,const_composition,gsm,dia_width,width_dia_type,batch_qty,roll_no,barcode_no, load_unload_id, roll_id, production_qty,remarks, rate, amount, currency_id,exchange_rate,inserted_by,insert_date";
				for ($i = 1; $i <= $total_row; $i++) {
					$checked_tr = "checkRow_" . $i;
					if (str_replace("'", "", $$checked_tr) == 1) {
						$prod_id = "txtprodid_" . $i;
						$txtconscomp = "txtconscomp_" . $i;
						$txtgsm = "txtgsm_" . $i;
						$txtdiawidth = "txtdiawidth_" . $i;
						$txtdiawidth = "txtdiatype_" . $i;
						$txtbatchqnty = "txtbatchqnty_" . $i;
						$txtroll = "txtroll_" . $i;
						$txtbarcode="txtbarcode_".$i;
						$rollid = "rollid_" . $i;
						$txtrate = "txtrate_" . $i;
						$txtamount = "txtamount_" . $i;
						$txtremark="txtremark_".$i;
						//$txtyarncount="txtyarncount_".$i;
						//$txtbrand="txtbrand_".$i;
						$txtproductionqty = "txtprodqnty_" . $i;
						$txtdiatypeID = "hiddendiatypeid_" . $i;
						$Itemprod_id = str_replace("'", "", $$prod_id);
						if ($data_array_dtls != "") $data_array_dtls .= ",";
						$data_array_dtls .= "(" . $id_dtls . "," . $mst_update_id . ",35," . $Itemprod_id . "," . $$txtconscomp . "," . $$txtgsm . "," . $$txtdiawidth . "," . $$txtdiatypeID . "," . $$txtbatchqnty . "," . $$txtroll . "," . $$txtbarcode . ",2," . $$rollid . "," . $$txtproductionqty . "," . $$txtremark . "," . $$txtrate . "," . $$txtamount . "," . $hidden_currency . "," . $hidden_exchange_rate . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
						$id_dtls = $id_dtls + 1;
					}
					//print_r($data_array_dtls);die;
				}

			} else {
				$field_array_dtls = "id, mst_id,entry_page,prod_id,const_composition,gsm,dia_width,width_dia_type,batch_qty,no_of_roll, load_unload_id, production_qty, rate, amount, currency_id,exchange_rate,remarks ,inserted_by,insert_date";
				for ($i = 1; $i <= $total_row; $i++) {
					$prod_id = "txtprodid_" . $i;
					$txtconscomp = "txtconscomp_" . $i;
					$txtgsm = "txtgsm_" . $i;
					$txtdiawidth = "txtdiawidth_" . $i;
					$txtdiatype = "txtdiatype_" . $i;
					$txtbatchqnty = "txtbatchqnty_" . $i;
					$txtroll = "txtroll_" . $i;
					$txtrate = "txtrate_" . $i;
					$txtamount = "txtamount_" . $i;
					$txtremark="txtremark_".$i;
					//$txtlot="txtlot_".$i;
					//$txtyarncount="txtyarncount_".$i;
					//$txtbrand="txtbrand_".$i;
					$txtproductionqty = "txtprodqnty_" . $i;
					$hiddendiatypeid = "hiddendiatypeid_" . $i;
					$Itemprod_id = str_replace("'", "", $$prod_id);
					if ($data_array_dtls != "") $data_array_dtls .= ",";
					$data_array_dtls .= "(" . $id_dtls . "," . $mst_update_id . ",35," . $Itemprod_id . "," . $$txtconscomp . "," . $$txtgsm . "," . $$txtdiawidth . "," . $$hiddendiatypeid . "," . $$txtbatchqnty . "," . $$txtroll . ",2," . $$txtproductionqty . "," . $$txtrate . "," . $$txtamount . "," . $hidden_currency . "," . $hidden_exchange_rate . "," . $$txtremark . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
					$id_dtls = $id_dtls + 1;
					//print_r($data_array_dtls);die;
				}

			}

			//print_r($data_array);
		} //Unload End
		$flag=1;
		$rID = $rID2 = true;
		$rID = sql_insert("pro_fab_subprocess", $field_array, $data_array, 0);
		if ($rID) $flag = 1; else $flag = 0;
		//echo "10**insert into pro_fab_subprocess($field_array)values".$data_array;die;
		//echo "insert into pro_fab_dyeing_focus (id,batch_no,production_date) values (1,'abc','2020-Jan-01')";
			$rID2 = sql_insert("pro_fab_subprocess_dtls", $field_array_dtls, $data_array_dtls, 0);
			if ($flag == 1) {
				if ($rID2) $flag = 1; else $flag = 0;
			 }


		//echo "10**insert into pro_fab_subprocess_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
		//check_table_status( $_SESSION['menu_id'],0);
		//echo "10**".$rID."**".$rID2."**".$flag;die;
		if ($db_type == 0) {
			if ($flag == 1) {
				mysql_query("COMMIT");
				echo "0**" . $mst_update_id . "**" . str_replace("'", "", $txt_batch_ID) . "**" . $system_no . "**" . str_replace("'", "", $cbo_load_unload);
			} else {
				mysql_query("ROLLBACK");
				echo "10**" . $mst_update_id . "**" . str_replace("'", "", $txt_batch_ID);
			}
		}
		if ($db_type == 2 || $db_type == 1) {
			if ($flag == 1) {
				oci_commit($con);
				echo "0**" . $mst_update_id . "**" . str_replace("'", "", $txt_batch_ID) . "**" . $system_no . "**" . str_replace("'", "", $cbo_load_unload);
			} else {
				oci_rollback($con);
				echo "10**" . $mst_update_id . "**" . str_replace("'", "", $txt_batch_ID);
			}
		}
		disconnect($con);
		die;
	 }
	else if ($operation == 1)   // Update Here
	{
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}
		$page_upto_id = return_field_value("page_upto_id", "variable_settings_production", "company_name =$cbo_company_id and item_category_id=50 and variable_list=3 and is_deleted=0 and status_active=1");

		
		$update_id = str_replace("'", "", $txt_update_id);
		$system_no = str_replace("'", "", $txt_system_no);
		$field_array = "";
		$data_array = "";
		$id = return_next_id("id", "pro_fab_subprocess", 1);
		//echo $cbo_load_unload;die;
		if ($cbo_load_unload == "'1'") {
			$field_array_update = "service_source*service_company*received_chalan*issue_chalan*issue_challan_mst_id*batch_no*batch_id*batch_ext_no*process_id*ltb_btb_id*water_flow_meter*process_end_date*end_hours*end_minutes*machine_id*floor_id*load_unload_id*entry_form*remarks*dyeing_type_id*hour_load_meter*multi_batch_load_id*updated_by*update_date";
			$data_array_update = "" . $cbo_service_source . "*" . $cbo_service_company . "*" . $txt_recevied_chalan . "*" . $txt_issue_chalan . "*" . $txt_issue_mst_id . "*" . $txt_batch_no . "*" . $txt_batch_ID . "*" . $txt_ext_id . "*" . $txt_process_id . "*" . $cbo_ltb_btb . "*" . $txt_water_flow . "*" . $txt_process_start_date . "*" . $txt_start_hours . "*" . $txt_start_minutes . "*" . $cbo_machine_name . "*" . $cbo_floor . "*" . $cbo_load_unload . "*35*" . $txt_remarks . "*" . $cbo_dyeing_type . "*" . $txt_load_meter . "*" . $cbo_yesno . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";//

			$id_dtls = return_next_id("id", "pro_fab_subprocess_dtls", 1);
			$field_array_dtls_insert = "id, mst_id,entry_page,prod_id,const_composition,gsm,dia_width,width_dia_type,batch_qty,roll_no,barcode_no,load_unload_id,roll_id,production_qty,remarks,inserted_by,insert_date";
			if (($page_upto_id == 2 || $page_upto_id > 2) && str_replace("'", "", $roll_maintained) == 1) {

				$field_array_up = "mst_id*gsm*batch_qty*production_qty*remarks*roll_no*barcode_no*roll_id*updated_by*update_date";
				$field_array_delete = "updated_by*update_date*status_active*is_deleted";
				for ($i = 1; $i <= $total_row; $i++) {
					$checkRowTd = "checkRow_" . $i;
					$prod_id = "txtprodid_" . $i;
					$txtconscomp = "txtconscomp_" . $i;
					$txtdiawidth = "txtdiawidth_" . $i;
					$txtdiawidth = "txtdiatype_" . $i;
					$txtgsm = "txtgsm_" . $i;
					$txtroll = "txtroll_" . $i;
					$txtbarcode="txtbarcode_".$i;
					$txtremark="txtremark_".$i;
					$rollid = "rollid_" . $i;
					$txtremark="txtremark_".$i;
					$hiddendiatypeid = "hiddendiatypeid_" . $i;
					$txtprodqnty = "txtprodqnty_" . $i;
					$txtbatchqnty = "txtbatchqnty_" . $i;
					$updateiddtls = "updateiddtls_" . $i;
					$Itemprod_id = str_replace("'", "", $$prod_id);
					if (str_replace("'", "", $$checkRowTd) == 1) {
						if (str_replace("'", '', $$updateiddtls) != "") {
							//echo 'A';
							$id_arr[] = str_replace("'", '', $$updateiddtls);
							$data_array_up[str_replace("'", '', $$updateiddtls)] = explode("*", ("" . $update_id . "*" . $$txtgsm . "*" . $$txtbatchqnty . "*" . $$txtprodqnty . "*" . $$txtremark . "*" . $$txtroll . "*" . $$txtbarcode . "*" . $$rollid . "*'" . $_SESSION['logic_erp']['user_id'] . "'*'" . $pc_date_time . "'"));
						}
						if (str_replace("'", '', $$updateiddtls) == "") {
							//echo 'B';
							if ($data_array_dtls_insert != "") $data_array_dtls_insert .= ",";
							$data_array_dtls_insert .= "(" . $id_dtls . "," . $update_id . ",35," . $Itemprod_id . "," . $$txtconscomp . "," . $$txtgsm . "," . $$txtdiawidth . "," . $$hiddendiatypeid . "," . $$txtbatchqnty . "," . $$txtroll . "," . $$txtbarcode . ",1," . $$rollid . "," . $$txtprodqnty . "," . $$txtremark . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
							$id_dtls = $id_dtls + 1;
						}
					} else {
						if (str_replace("'", '', $$updateiddtls) != "") {
							//echo 'c';
							$id_arr_delete[] = str_replace("'", '', $$updateiddtls);
							$data_array_delete[str_replace("'", '', $$updateiddtls)] = explode("*", ("'" . $_SESSION['logic_erp']['user_id'] . "'*'" . $pc_date_time . "'*0*1"));
						}
					}
				}

			} else {
				//$field_array_dtls="id, mst_id, entry_page, prod_id, const_composition, gsm, dia_width, width_dia_type, batch_qty, inserted_by, insert_date";
				$field_array_up = "mst_id*gsm*batch_qty*production_qty*remarks*no_of_roll*roll_id*updated_by*update_date";
				for ($i = 1; $i <= $total_row; $i++) {
					//$checkRowTd="checkRow_".$i;
					$prod_id = "txtprodid_" . $i;
					$txtconscomp = "txtconscomp_" . $i;
					$txtgsm = "txtgsm_" . $i;
					$txtroll = "txtroll_" . $i;
					$rollid = "rollid_" . $i;
					$txtprodqnty = "txtprodqnty_" . $i;
					$hiddendiatypeid = "hiddendiatypeid_" . $i;
					$txtremark="txtremark_".$i;
					$txtdiatype = "txtdiatype_" . $i;
					$txtdiawidth = "txtdiawidth_" . $i;
					$txtbatchqnty = "txtbatchqnty_" . $i;
					$updateiddtls = "updateiddtls_" . $i;


					$Itemprod_id = str_replace("'", "", $$prod_id);
					if (str_replace("'", '', $$updateiddtls) != "") {
						$id_arr[] = str_replace("'", '', $$updateiddtls);
						$data_array_up[str_replace("'", '', $$updateiddtls)] = explode("*", ("" . $update_id . "*" . $$txtgsm . "*" . $$txtbatchqnty . "*" . $$txtprodqnty . "*" . $$txtremark . "*" . $$txtroll . "*" . $$rollid . "*'" . $_SESSION['logic_erp']['user_id'] . "'*'" . $pc_date_time . "'"));
					} else {
						if ($data_array_dtls_insert != "") $data_array_dtls_insert .= ",";
						$data_array_dtls_insert .= "(" . $id_dtls . "," . $update_id . ",35," . $Itemprod_id . "," . $$txtconscomp . "," . $$txtgsm . "," . $$txtdiawidth . "," . $$hiddendiatypeid . "," . $$txtbatchqnty . "," . $$txtroll . ",1," . $$rollid . "," . $$txtprodqnty . "," . $$txtremark . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
						$id_dtls = $id_dtls + 1;
					}
				}
			}
			//echo "10**insert into pro_fab_subprocess_dtls($field_array_dtls_insert)values".$data_array_dtls_insert;
			//echo bulk_update_sql_statement("pro_fab_subprocess_dtls", "id",$field_array_up,$data_array_up,$id_arr );die;
			//print_r($data_array_up);die;
			/*if(count($data_array_up)>0)
			{
			$rID2=execute_query(bulk_update_sql_statement("pro_fab_subprocess_dtls", "id",$field_array_up,$data_array_up,$id_arr ));
			if($rID2) $flag=1; else $flag=20;
			//echo $flag;die;
			}
			if(count($data_array_delete)>0)
			{
			$rID3=execute_query(bulk_update_sql_statement("pro_fab_subprocess_dtls", "id",$field_array_delete,$data_array_delete,$id_arr_delete ));
			if($rID3) $flag=1; else $flag=10;
			//echo $flag;die;
		}*/

		}
		if ($cbo_load_unload == "'2'") {
			$result_id = str_replace("'", "", $cbo_result_name);
			if($result_id==4) //incomplete
			{
				$field_arr="*incomplete_result*incomplete_date";
				$field_data_arr="*".$result_id."*".$txt_process_end_date;
			}
			elseif($result_id==2) //Redying Shade Match
			{
				$field_arr="*redyeing_needed";
				$field_data_arr="*".$result_id;
			}
			elseif($result_id==1) // Shade Match
			{
				$field_arr="*shade_matched";
				$field_data_arr="*".$result_id;
			}
			else
			{
				$field_arr="";
				$field_data_arr="";
			}
			$field_array_update = "service_source*service_company*received_chalan*issue_chalan*issue_challan_mst_id*batch_no*batch_id*batch_ext_no*process_id*ltb_btb_id*water_flow_meter*process_end_date*end_hours*end_minutes*machine_id*floor_id*load_unload_id*result*entry_form*remarks*dyeing_type_id*hour_unload_meter*shift_name*fabric_type*production_date*booking_no*updated_by*update_date $field_arr";
	$data_array_update = "" . $cbo_service_source . "*" . $cbo_service_company . "*" . $txt_recevied_chalan . "*" . $txt_issue_chalan . "*" . $txt_issue_mst_id . "*" . $txt_batch_no . "*" . $txt_batch_ID . "*" . $txt_ext_id . "*" . $txt_process_id . "*" . $cbo_ltb_btb . "*" . $txt_water_flow . "*" . $txt_process_end_date . "*" . $txt_end_hours . "*" . $txt_end_minutes . "*" . $cbo_machine_name . "*" . $cbo_floor . "*" . $cbo_load_unload . "*" . $cbo_result_name . "*35*" . $txt_remarks . "*" . $cbo_dyeing_type . "*" . $txt_unload_meter . "*" . $cbo_shift_name . "*" . $cbo_fabric_type . "*" . $txt_process_date . "*" . $txt_booking_no . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "' ".$field_data_arr."";


			$id_dtls = return_next_id("id", "pro_fab_subprocess_dtls", 1);
			$field_array_dtls_insert = "id, mst_id,entry_page,prod_id,const_composition,gsm,dia_width,width_dia_type,batch_qty,roll_no,barcode_no,load_unload_id,roll_id,rate,amount,production_qty,remarks,inserted_by,insert_date";
			if (($page_upto_id == 2 || $page_upto_id > 2) && str_replace("'", "", $roll_maintained) == 1) {
				$field_array_up = "mst_id*gsm*batch_qty*production_qty*roll_no*barcode_no*roll_id*rate*amount*currency_id*exchange_rate*remarks*updated_by*update_date";
				$field_array_delete = "updated_by*update_date*status_active*is_deleted";
				for ($i = 1; $i <= $total_row; $i++) {
					$checkRowTd = "checkRow_" . $i;
					$prod_id = "txtprodid_" . $i;
					$txtconscomp = "txtconscomp_" . $i;
					$txtgsm = "txtgsm_" . $i;
					$txtroll = "txtroll_" . $i;
					$txtbarcode = "txtbarcode_" . $i;
					$rollid = "rollid_" . $i;
					$txtdiawidth = "txtdiawidth_" . $i;
					$hiddendiatypeid = "hiddendiatypeid_" . $i;
					$txtprodqnty = "txtprodqnty_" . $i;
					$txtbatchqnty = "txtbatchqnty_" . $i;
					$updateiddtls = "updateiddtls_" . $i;
					$txtrate = "txtrate_" . $i;
					$txtamount = "txtamount_" . $i;
					$txtremark="txtremark_".$i;
					$Itemprod_id = str_replace("'", "", $$prod_id);
					if (str_replace("'", "", $$checkRowTd) == 1) {
						if (str_replace("'", '', $$updateiddtls) != "") {
							$id_arr[] = str_replace("'", '', $$updateiddtls);
							$data_array_up[str_replace("'", '', $$updateiddtls)] = explode("*", ("" . $update_id . "*" . $$txtgsm . "*" . $$txtbatchqnty . "*" . $$txtprodqnty . "*" . $$txtroll . "*" . $$txtbarcode . "*" . $$rollid . "*" . $$txtrate . "*" . $$txtamount . "*" . $hidden_currency . "*" . $hidden_exchange_rate . "*" . $$txtremark . "*'" . $_SESSION['logic_erp']['user_id'] . "'*'" . $pc_date_time . "'"));
						}

						if (str_replace("'", '', $$updateiddtls) == "") {
							$field_array_dtls_insert = "id, mst_id,entry_page,prod_id,const_composition,gsm,dia_width,width_dia_type,batch_qty,roll_no,load_unload_id,roll_id,rate,amount,production_qty,inserted_by,insert_date";
							if ($data_array_dtls_insert != "") $data_array_dtls_insert .= ",";
							$data_array_dtls_insert .= "(" . $id_dtls . "," . $update_id . ",35," . $Itemprod_id . "," . $$txtconscomp . "," . $$txtgsm . "," . $$txtdiawidth . "," . $$hiddendiatypeid . "," . $$txtbatchqnty . "," . $$txtroll . "," . $$txtbarcode . ",2," . $$rollid . "," . $$txtrate . "," . $$txtamount . "," . $$txtprodqnty . "," . $$txtremark . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
							$id_dtls = $id_dtls + 1;
						}

					} else {
						if (str_replace("'", '', $$updateiddtls) != "") {
							$id_arr_delete[] = str_replace("'", '', $$updateiddtls);
							$data_array_delete[str_replace("'", '', $$updateiddtls)] = explode("*", ("'" . $_SESSION['logic_erp']['user_id'] . "'*'" . $pc_date_time . "'*0*1"));
						}
					}
				}
			} else {
				//$field_array_dtls="id, mst_id, entry_page, prod_id, const_composition, gsm, dia_width, width_dia_type, batch_qty, inserted_by, insert_date";
				$field_array_up = "mst_id*gsm*batch_qty*production_qty*no_of_roll*roll_id*rate*amount*currency_id*exchange_rate*remarks*updated_by*update_date";
				for ($i = 1; $i <= $total_row; $i++) {
					$checkRowTd = "checkRow_" . $i;
					$prod_id = "txtprodid_" . $i;
					$txtconscomp = "txtconscomp_" . $i;
					$txtgsm = "txtgsm_" . $i;
					$txtroll = "txtroll_" . $i;
					$rollid = "rollid_" . $i;
					$txtprodqnty = "txtprodqnty_" . $i;
					$txtrate = "txtrate_" . $i;
					$txtamount = "txtamount_" . $i;

					$txtremark="txtremark_".$i;
					$txtdiatype = "txtdiatype_" . $i;
					$txtdiawidth = "txtdiawidth_" . $i;
					$hiddendiatypeid = "hiddendiatypeid_" . $i;
					$txtbatchqnty = "txtbatchqnty_" . $i;
					$updateiddtls = "updateiddtls_" . $i;


					$Itemprod_id = str_replace("'", "", $$prod_id);
					if (str_replace("'", "", $$updateiddtls) != "") {
						$id_arr[] = str_replace("'", '', $$updateiddtls);
						$data_array_up[str_replace("'", '', $$updateiddtls)] = explode("*", ("" . $update_id . "*" . $$txtgsm . "*" . $$txtbatchqnty . "*" . $$txtprodqnty . "*" . $$txtroll . "*" . $$rollid . "*" . $$txtrate . "*" . $$txtamount . "*" . $hidden_currency . "*" . $hidden_exchange_rate . "*" . $$txtremark . "*'" . $_SESSION['logic_erp']['user_id'] . "'*'" . $pc_date_time . "'"));
					}

					if (str_replace("'", '', $$updateiddtls) == "") {
						if ($data_array_dtls_insert != "") $data_array_dtls_insert .= ",";
						$data_array_dtls_insert .= "(" . $id_dtls . "," . $update_id . ",35," . $Itemprod_id . "," . $$txtconscomp . "," . $$txtgsm . "," . $$txtdiawidth . "," . $$hiddendiatypeid . "," . $$txtbatchqnty . "," . $$txtroll . ",2," . $$rollid . "," . $$txtrate . "," . $$txtamount . "," . $$txtprodqnty . "," . $$txtremark . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
						$id_dtls = $id_dtls + 1;
					}

				}
			}

		}
		//echo "10**insert into pro_fab_subprocess_dtls($field_array_dtls_insert)values".$data_array_dtls_insert;
		$flag = 0;
		$rID = sql_update("pro_fab_subprocess", $field_array_update, $data_array_update, "id", $update_id, 1);
		if ($rID) $flag = 1; else $flag = 0;

		if (count($data_array_up) > 0) {
			$rID2 = execute_query(bulk_update_sql_statement("pro_fab_subprocess_dtls", "id", $field_array_up, $data_array_up, $id_arr));
			//echo "10**".bulk_update_sql_statement("pro_fab_subprocess_dtls", "id", $field_array_up, $data_array_up, $id_arr);die;
			if ($rID2) $flag = 1; else $flag = 0;
		}
		if (count($data_array_delete) > 0) {
			$rID3 = execute_query(bulk_update_sql_statement("pro_fab_subprocess_dtls", "id", $field_array_delete, $data_array_delete, $id_arr_delete));
			if ($rID3) $flag = 1; else $flag = 0;
		}
		if ($data_array_dtls_insert != "") {

			if ($flag == 1) {

				$rID4 = sql_insert("pro_fab_subprocess_dtls", $field_array_dtls_insert, $data_array_dtls_insert, 0);
				if ($rID4) $flag = 1; else $flag = 0;
			}
		}
		//echo $rID.'='.$rID2.'='.$rID3.'='.$rID4;die;
		//$rID=sql_update("pro_fab_subprocess",$field_array_update,$data_array_update,"id",$update_id,1);
		//echo "insert into pro_fab_subprocess values $field_array_update,$field_array_update,'id',$update_id,0)";die;
		//check_table_status( $_SESSION['menu_id'],0);
		if ($db_type == 0) {
			if ($flag == 1) {
				mysql_query("COMMIT");

				echo "1**" . $update_id . "**" . str_replace("'", "", $txt_batch_ID) . "**" . $system_no . "**" . str_replace("'", "", $cbo_load_unload);
			} else {
				mysql_query("ROLLBACK");
				echo "10**" . $update_id;
			}
		}
		if ($db_type == 2 || $db_type == 1) {
			if ($flag == 1) {
				oci_commit($con);
				echo "1**" . $update_id . "**" . str_replace("'", "", $txt_batch_ID) . "**" . $system_no . "**" . str_replace("'", "", $cbo_load_unload);
			} else {
				oci_rollback($con);
				echo "10**" . $update_id;
			}
		}
		disconnect($con);
		die;
	} else if ($operation == 2)   // Delete Here
	{
		echo "2**" . $update_id;
		disconnect($con);
		die;
	}
}
function sql_update2($strTable,$arrUpdateFields,$arrUpdateValues,$arrRefFields,$arrRefValues,$commit)
{

	$strQuery = "UPDATE ".$strTable." SET ";
	$arrUpdateFields=explode("*",$arrUpdateFields);
	$arrUpdateValues=explode("*",$arrUpdateValues);

	if(count($arrUpdateFields)!=count($arrUpdateValues)){
		return "0";
	}

	if(is_array($arrUpdateFields))
	{
		$arrayUpdate = array_combine($arrUpdateFields,$arrUpdateValues);
		$Arraysize = count($arrayUpdate);
		$i = 1;
		foreach($arrayUpdate as $key=>$value):
			$strQuery .= ($i != $Arraysize)? $key."=".$value.", ":$key."=".$value;
			$i++;
		endforeach;
	}
	else
	{
		$strQuery .= $arrUpdateFields."=".$arrUpdateValues;
	}
	$strQuery .=" WHERE ";

	$arrRefFields=explode("*",$arrRefFields);
	$arrRefValues=explode("*",$arrRefValues);
	if(is_array($arrRefFields))
	{
		$arrayRef = array_combine($arrRefFields,$arrRefValues);
		$Arraysize = count($arrayRef);
		$i = 1;
		foreach($arrayRef as $key=>$value):
			$strQuery .= ($i != $Arraysize)? $key."=".$value." AND ":$key."=".$value."";
			$i++;
		endforeach;
	}
	else
	{
		$strQuery .= $arrRefFields."=".$arrRefValues."";
	}
	echo $strQuery ;die;
	global $con;
	if( strpos($strQuery, "WHERE")==false)  return "0";
	$stid =  oci_parse($con, $strQuery);
	$exestd=oci_execute($stid,OCI_NO_AUTO_COMMIT);
	if ($exestd)
		return "1";
	else
		return "0";

	die;
	if ( $commit==1 )
	{
		if (!oci_error($stid))
		{
			oci_commit($con);
			return "1";
		}
		else
		{
			oci_rollback($con);
			return "10";
		}
	}
	else
		return 1;
	die;
}
if ($action == "check_batch_no") {
	$data = explode("**", $data);
	$sql = "select id, entry_form,batch_no,company_id from pro_batch_create_mst where batch_no='" . trim($data[1]) . "' and entry_form in(0,36,136) and is_deleted=0 and status_active=1 order by id desc";
	$data_array = sql_select($sql, 1);
	if (count($data_array) > 0) {
		echo "1" . "_" . $data_array[0][csf('id')] . "_" . $data_array[0][csf('company_id')]. "_" . $data_array[0][csf('entry_form')];
	} else {
		echo "0_";
	}
	exit();
}
if ($action == "check_batch_no_scan") {
	$data = explode("**", $data);
	$batch_id = (int)$data[1];
	$sql = "select id, batch_no,company_id from pro_batch_create_mst where id='" . $batch_id . "' and entry_form in(0,36,136) and is_deleted=0 and status_active=1 order by id desc";
	$data_array = sql_select($sql, 1);
	echo $data_array[0][csf('batch_no')];
	/*if(count($data_array)>0)
	{
		echo "1"."_".$data_array[0][csf('id')]."_".$data_array[0][csf('company_id')];
	}
	else
	{
		echo "0_";
	}*/
	exit();
}
if ($action == "check_batch_no_load") {
	$data = explode("**", $data);
	$process = $data[3];
	//if ($process == 31) {
		$sql = "select id, batch_id from pro_fab_subprocess where  company_id='" . trim($data[0]) . "' and  batch_id='" . trim($data[2]) . "' and load_unload_id=1 and entry_form=35 and is_deleted=0 and status_active=1";
		$data_array = sql_select($sql);
		if (count($data_array) > 0) {
			echo "1" . "_" . $data_array[0][csf('batch_id')];
		} else {
			echo "0_";
		}
	//} else {
		//echo "1" . "_";
	//}
	exit();
}


if ($action == "check_batch_no_for_machine") {
	$data = explode("**", $data);

	$sql = "select  a.batch_no as batch_no,a.batch_id from pro_fab_subprocess a,pro_fab_subprocess_dtls b  where  a.id=b.mst_id and a.service_company='" . trim($data[0]) . "'  and a.machine_id='$data[3]' and a.service_source in(1) and a.load_unload_id=1 and a.entry_form=35 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1   group by  a.batch_no,a.batch_id";

	$data_array = sql_select($sql);
	$loaded_batch_id="";
	$loaded_batch_idarr = array();
	foreach ($data_array as $loaded_row) {
		$loaded_batch_idarr[$loaded_row[csf('batch_id')]] = $loaded_row[csf('batch_id')];
		$loaded_batch_no[$loaded_row[csf('batch_id')]] = $loaded_row[csf('batch_no')];
		$loaded_batch_id.=$loaded_row[csf('batch_id')].',';
	}
	
						
	if(!empty($loaded_batch_idarr)){
		
	$loaded_batch_ids=rtrim($loaded_batch_id,',');
	$BatIds=chop($loaded_batch_ids,','); $bat_cond_for_in="";
	$bat_ids=count(array_unique(explode(",",$loaded_batch_ids)));
	if($db_type==2 && $bat_ids>1000)
	{
		$bat_cond_for_in=" and (";
		$BatIdsArr=array_chunk(explode(",",$BatIds),999);
		foreach($BatIdsArr as $ids)
		{
			$ids=implode(",",$ids);
			$bat_cond_for_in.=" a.batch_id in($ids) or"; 
		}
		$bat_cond_for_in=chop($bat_cond_for_in,'or ');
		$bat_cond_for_in.=")";
	}
	else
	{
		$bat_cond_for_in=" and a.batch_id in($BatIds)";
	}
		
	$sql_batch = sql_select("select a.batch_id,a.batch_no from pro_fab_subprocess a,pro_fab_subprocess_dtls b    where  a.id=b.mst_id and a.service_company='" . trim($data[0]) . "'  and a.machine_id='$data[3]'  and a.service_source in(1) and a.load_unload_id=2 and a.entry_form=35 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1   $bat_cond_for_in  group by  a.batch_no,a.batch_id");// and a.batch_id in(".implode(",",$loaded_batch_idarr).")
	}

	foreach ($sql_batch as $rowuload) {
		$unloaded_batch_idarr[$rowuload[csf('batch_id')]] = $rowuload[csf('batch_id')];
	}

	$loadedData =array_diff($loaded_batch_idarr,$unloaded_batch_idarr);

	foreach ($loadedData as $batchId) {
		$loaded_bathc_no .= $loaded_batch_no[$batchId].",";
	}

	//echo chop($loaded_bathc_no," , "); die();

	if (count($loadedData) > 0) {

		echo "1" . "_" . chop($loaded_bathc_no," , ");
	} else {
		echo "0_";
	}
	exit();
}


if ($action == "check_for_shade_matched") {
	$data = explode("**", $data);
	//listagg(batch_id,',') within group (order by batch_id) as batch_id
	$sql_unload = "select  batch_no from pro_fab_subprocess where  company_id='" . trim($data[0]) . "' and  batch_id=" . trim($data[2]) . " and load_unload_id=2 and entry_form=35 and is_deleted=0 and result=1 and status_active=1";
	$data_array = sql_select($sql_unload, 1);
	//echo count($data_array);die;
	if (count($data_array) > 0) {
		echo "1" . "_" . $data_array[0][csf('batch_no')];
	} else {
		echo "0_";
	}
	exit();
}
if ($action == "roll_maintained_setting") { //echo $data;
	// $roll_maintained=return_field_value("fabric_roll_level","variable_settings_production","company_name ='$data' and item_category_id=2 and variable_list=3 and is_deleted=0 and status_active=1");
	$roll_maintained = return_field_value("fabric_roll_level", "variable_settings_production", "company_name ='$data' and item_category_id=50 and variable_list=3 and is_deleted=0 and status_active=1");
	$page_upto_id = return_field_value("page_upto_id", "variable_settings_production", "company_name ='$data' and item_category_id=50 and variable_list=3 and is_deleted=0 and status_active=1");

	if ($roll_maintained == "" || $roll_maintained == 2) $roll_maintained = 0; else $roll_maintained = $roll_maintained;

	echo "document.getElementById('roll_maintained').value 				= '" . $roll_maintained . "';\n";
	echo "document.getElementById('page_upto').value 				= '" . $page_upto_id . "';\n";

	exit();
}
if ($action == "check_date_time")//Un used it
{
	$data = explode("**", $data);
	$company = $data[0];
	$batch_no = $data[1];
	$batch_id = $data[2];
	$unload_date = $data[3];
	$load_date = $data[4];
	$unload_time = $data[5];
	$load_min_hr = $data[6];

	$new_date_time_unload = ($unload_date . ' ' . $unload_time . ':' . '00');
	$new_date_time_load = ($load_date . ' ' . $load_min_hr . ':' . '00');
	$total_time = datediff(n, $new_date_time_load, $new_date_time_unload);
	//echo $new_date_time_unload.'=='.$new_date_time_load;
	//echo floor($total_time/60).":".$total_time%60;

	$sql = "select id, batch_no,company_id from pro_batch_create_mst where batch_no='" . trim($data[1]) . "' and entry_form in(0,36) and is_deleted=0 and status_active=1 order by id desc";
	$data_array = sql_select($sql, 1);
	if (count($data_array) > 0) {
		echo "1" . "_" . $data_array[0][csf('id')] . "_" . $data_array[0][csf('company_id')];
	} else {
		echo "0_";
	}
	exit();
}
if ($action == "on_change_data") {
	extract($_REQUEST);
	$explode_data = explode("_", $data);
	$type = $explode_data[0];
	$company_id = $explode_data[1];
	if ($data == "1") // Loading
	{
		?>
        <div onLoad="set_hotkey();">
            <fieldset>
                <table cellpadding="0" cellspacing="2" width="100%" id="main_tbl">
                    <tr>
                        <td>Dyeing Type</td>
                        <td>
							<?
							echo create_drop_down("cbo_dyeing_type", 135, $dyeing_type_arr, "", 0, "-- Select--", 1, "", 0, "", "", "", "");
							?>
                        </td>
                    </tr>
                    <tr>
                        <td width="" id="batch_no_th">Batch No.</td>
                        <td>
                            <input type="text" name="txt_batch_no" id="txt_batch_no" class="text_boxes" style="width:122px;" placeholder="Write/Browse/Scan" onDblClick="openmypage_batchnum();" onChange="check_batch();"/>
                            <input type="hidden" name="hidden_batch_id" id="hidden_batch_id" class="text_boxes" readonly/>
							<input type="hidden" name="txt_entry_form_no" id="txt_entry_form_no" class="text_boxes" readonly/>
                            <input type="hidden" name="txt_hidden_service_company" id="txt_hidden_service_company" style="width:30px;" class="text_boxes" readonly />
							<input type="hidden" name="hidden_double_dyeing" id="hidden_double_dyeing" class="text_boxes" readonly/>
							<input type="hidden" name="hidden_result_id" id="hidden_result_id" class="text_boxes" readonly/>
						   <input type="hidden" name="hidden_last_loadunload_id" id="hidden_last_loadunload_id" class="text_boxes" readonly/>
                        </td>
                    </tr>
                    <tr>
                        <td id="company_th" width="130">Company</td>
                        <td>
							<?
							echo create_drop_down("cbo_company_id", 135, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "--Select Company--", $selected, "load_drop_down('requires/dyeing_production_controller', this.value, 'load_drop_floor', 'floor_td' );roll_maintain();", "", "", "", "", "");
							?>
                            <input type="hidden" name="txt_update_id" id="txt_update_id" style="width:100px;"
                                   class="text_boxes" readonly/>
                            <input type="hidden" name="roll_maintained" id="roll_maintained" style="width:100px;"
                                   class="text_boxes"/>
                            <input type="hidden" name="page_upto" id="page_upto" style="width:30px;"
                                   class="text_boxes"/>
                        </td>
                    </tr>
                    <tr>
                        <td class="">Issue Challan</td>
                        <td>
                            <input type="text" name="txt_issue_chalan" id="txt_issue_chalan" class="text_boxes"
                                   style="width:122px;" placeholder="Write/Browse/Scan"
                                   onDblClick="openmypage_issue_challan();" onChange="check_issue_challan();"/>
                            <input type="hidden" name="txt_issue_mst_id" id="txt_issue_mst_id" style="width:100px;"
                                   class="text_boxes" readonly/>
                            <input type="hidden" name="txt_roll_id" id="txt_roll_id" style="width:50px;"
                                   class="text_boxes" readonly/>
                            <input type="hidden" name="roll_maintained" id="roll_maintained" style="width:100px;"
                                   class="text_boxes"/>
                        </td>
                    </tr>
                    <tr>
                        <td id="service_source_caption">Service Source</td>
                        <td>
							<?
							//fnc_company_val();
							echo create_drop_down("cbo_service_source", 135, $knitting_source, "", 1, "-- Select --", $selected, "load_drop_down( 'requires/dyeing_production_controller',this.value+'**'+$('#cbo_company_id').val(),'load_drop_down_knitting_com','dyeing_company_td' );search_populate(this.value);", "", "1,3");
							?>
                        </td>

                    </tr>
                    <tr>
                        <td id="service_company_caption">Service Company</td>
                        <td id="dyeing_company_td">
							<?
							//	echo create_drop_down( "cbo_service_company", 135, $blank_array,"", 1, "-- Select --", $selected, "","","" );
							echo create_drop_down("cbo_service_company", 135, $blank_array, "", 1, "-- Select --", $selected, "", "", "");
							?>
                        </td>
                    </tr>
                    <tr>
                        <td id="search_by_th_up">Received Challan</td>
                        <td>
                            <input type="text" name="txt_recevied_chalan" id="txt_recevied_chalan" class="text_boxes"
                                   style="width:122px;"/>
                        </td>
                    </tr>
                    <tr>
                        <td id="process_td">Process</td>
                        <td>
							<?
							echo create_drop_down("txt_process_id", 135, $conversion_cost_head_array, "", 1, "--Select---", 31, "", "", "", "", "", "1,2,3,4,101,120,121,122,123,124");
							?>
                            <!--    <input type="text" name="txt_process_name" id="txt_process_name" class="text_boxes" style="width:122px;" placeholder="Double Click To Search" onDblClick="openmypage_process();" readonly />
                            <input type="hidden" name="txt_process_id" id="txt_process_id" />-->
                        </td>
                    </tr>
                    <tr>
                        <td id="ltb_ltb_caption">LTB/BTB</td>
                        <td>
							<? $ltb_btb = array(1 => 'BTB', 2 => 'LTB');
							echo create_drop_down("cbo_ltb_btb", 135, $ltb_btb, "", 1, "-- Select --", 1, "", "", "", "", "", "");
							?>
                        </td>
                    </tr>
                    <tr>
                        <td class="">Hour Load Meter</td>
                        <td>
                            <input type="text" name="txt_load_meter" class="text_boxes_numeric" id="txt_load_meter"
                                   style="width:122px;"/>
                        </td>
                    </tr>
                    <tr>
                        <td class="">Water Flow</td>
                        <td>
                            <input type="text" name="txt_water_flow" class="text_boxes_numeric" id="txt_water_flow"
                                   style="width:122px;"/>
                        </td>
                    </tr>
                    <tr>
                        <td id="process_start_date">Process Start Date</td>
                        <td>
                            <input type="text" name="txt_process_start_date" id="txt_process_start_date"
                                   class="datepicker" style="width:122px;" value="<? echo date('d-m-Y'); ?>" readonly/>
                        </td>
                    </tr>
                    <tr>
                        <td id="hour_min_td">Process Start Time</td>
                        <td>
                            <input type="text" name="txt_start_hours" id="txt_start_hours" class="text_boxes_numeric"
                                   placeholder="Hours" style="width:50px;"
                                   onBlur="fnc_move_cursor(this.value,'txt_start_hours','txt_end_date',2,23)"
                                   value="<? echo date('H'); ?>"/>
                            <input type="text" name="txt_start_minutes" id="txt_start_minutes"
                                   class="text_boxes_numeric" placeholder="Minutes" style="width:50px;"
                                   onBlur="fnc_move_cursor(this.value,'txt_start_minutes','txt_end_date',2,59)"
                                   value="<? echo date('i'); ?>"/>
                        </td>
                    </tr>
                    <tr>
                        <td id="floor_caption">Floor</td>
                        <td id="floor_td">
							<?
							echo create_drop_down("cbo_floor", 135, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 order by floor_name", "id,floor_name", 1, "-- Select Floor --", $selected, "", 0, "", "", "", "", 4);
							?>
                        </td>
                    <tr>
                    <tr>
                        <td id="machine_caption">Machine Name</td>
                        <td id="machine_td">
							<?
							echo create_drop_down("cbo_machine_name", 135, $blank_array, "", 1, "-- Select Machine --", 0, "", 0, "", "", "", "");
							?>
                        </td>
                    </tr>
                    <tr>
                        <td>Multi Batch Loading</td>
                        <td>
							<?
							echo create_drop_down("cbo_yesno", 80, $yes_no, "", 0, "-- Select--", 2, "", 0, "", "", "", "");
							?>
                        </td>
                        </tr>
                       
                </table>
            </fieldset>
            <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
        </div>
		<?
	}
	if ($data == "2") // Un-loading
	{
		?>
        <fieldset onLoad="process_check(document.getElementById('txt_process_id').value);">
            <table cellpadding="0" cellspacing="2" width="100%" id="main_tbl">
                <tr>
                        <td>Dyeing Type</td>
                        <td>
							<?
							echo create_drop_down("cbo_dyeing_type", 135, $dyeing_type_arr, "", 0, "-- Select--", 1, "", 0, "", "", "", "");
							?>
                        </td>
                    </tr>
                <tr>
                    <td width="" id="batch_no_th">Batch No.</td>
                    <td>
                        <input type="text" name="txt_batch_no" id="txt_batch_no" class="text_boxes" style="width:122px;" placeholder="Write/Browse/Scan" onDblClick="openmypage_batchnum();" onChange="check_batch();"/>
						 <input type="hidden" name="txt_entry_form_no" id="txt_entry_form_no" class="text_boxes" readonly/>
						 <input type="hidden" name="hidden_double_dyeing" id="hidden_double_dyeing" class="text_boxes" readonly/>
						 <input type="hidden" name="hidden_result_id" id="hidden_result_id" class="text_boxes" readonly/>
						 <input type="hidden" name="hidden_last_loadunload_id" id="hidden_last_loadunload_id" class="text_boxes" readonly/>
                        <input type="hidden" name="hidden_batch_id" id="hidden_batch_id" class="text_boxes" readonly/>
                        <input type="hidden" name="txt_hidden_service_company" id="txt_hidden_service_company" style="width:30px;" class="text_boxes" readonly />
                    </td>
                </tr>
                <tr>
                    <td id="company_th" width="130">Company</td>
                    <td>
						<?
						echo create_drop_down("cbo_company_id", 135, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "--Select Company--", $selected, "load_drop_down('requires/dyeing_production_controller', this.value, 'load_drop_floor', 'floor_td' );roll_maintain();");
						?>
                        <input type="hidden" name="txt_update_id" id="txt_update_id" style="width:100px;"
                               class="text_boxes" readonly/>
                        <input type="hidden" name="roll_maintained" id="roll_maintained" style="width:100px;"
                               class="text_boxes"/> <input type="hidden" name="page_upto" id="page_upto"
                                                           style="width:30px;" class="text_boxes"/>
                    </td>
                </tr>
                <tr>
                    <td class="">Issue Challan</td>
                    <td>
                        <input type="text" name="txt_issue_chalan" id="txt_issue_chalan" class="text_boxes"
                               style="width:122px;" placeholder="Write/Browse/Scan"
                               onDblClick="openmypage_issue_challan();" onChange="check_issue_challan();"/>
                        <input type="hidden" name="txt_issue_mst_id" id="txt_issue_mst_id" style="width:100px;"
                               class="text_boxes" readonly/>
                        <input type="hidden" name="txt_roll_id" id="txt_roll_id" style="width:50px;" class="text_boxes"
                               readonly/>
                        <input type="hidden" name="roll_maintained" id="roll_maintained" style="width:100px;"
                               class="text_boxes"/>
                    </td>
                </tr>
                <tr>
                    <td id="service_source_caption">Service Source</td>
                    <td>
						<?
						echo create_drop_down("cbo_service_source", 135, $knitting_source, "", 1, "-- Select --", $selected, "load_drop_down( 'requires/dyeing_production_controller',this.value+'**'+$('#cbo_company_id').val(),'load_drop_down_knitting_com','dyeing_company_td' );search_populate(this.value);", "", "1,3");
						?>
                    </td>

                </tr>
                <tr>
                    <td id="service_company_caption">Service Company</td>
                    <td id="dyeing_company_td">
						<?
						echo create_drop_down("cbo_service_company", 135, $blank_array, "", 1, "-- Select --", $selected, "", "", "");
						?>
                    </td>
                </tr>
                <tr>
                    <td id="search_by_th_up">Received Challan</td>
                    <td>
                        <input type="text" name="txt_recevied_chalan" id="txt_recevied_chalan" class="text_boxes"
                               style="width:122px;"/>
                    </td>
                </tr>
                <tr>
                    <td id="process_td">Process</td>
                    <td id="sub_process_td">
						<?
						// echo create_drop_down( "txt_process_id", 135, $conversion_cost_head_array,"", 0, "", $selected, "","","32,63,83,84" );
						echo create_drop_down("txt_process_id", 135, $conversion_cost_head_array, "", 0, "", 31, "process_check(this.value)", "", "", "", "", "1,2,3,4,101,120,121,122,123,124");
						?>
                        <!-- <input type="text" name="txt_process_name" id="txt_process_name" class="text_boxes" style="width:122px;" placeholder="Double Click To Search" onDblClick="openmypage_process();" readonly />
						<input type="hidden" name="txt_process_id" id="txt_process_id" />-->
                    </td>
                </tr>
                <tr>
                    <td>Service Booking</td>
                    <td>
                        <input type="text" name="txt_booking_no" id="txt_booking_no" class="text_boxes"
                               style="width:122px;" placeholder="Browse" onDblClick="openmypage_servicebook();"
                               readonly/>
                        <input type="hidden" name="hidden_exchange_rate" id="hidden_exchange_rate" class="text_boxes"
                               readonly/>
                        <input type="hidden" name="hidden_currency" id="hidden_currency" class="text_boxes" readonly/>
                    </td>
                </tr>
                <tr>
                    <td id="ltb_ltb_caption">LTB/BTB</td>
                    <td>
						<? $ltb_btb = array(1 => 'BTB', 2 => 'LTB');
						echo create_drop_down("cbo_ltb_btb", 135, $ltb_btb, "", 1, "-- Select --", 1, "", "", "", "", "", "");
						?>
                    </td>
                </tr>
                <tr>
                    <td class="">Hour Unload Meter</td>
                    <td>
                        <input type="text" name="txt_unload_meter" class="text_boxes_numeric" id="txt_unload_meter"
                               style="width:122px;"/>
                    </td>
                </tr>
                <tr>
                    <td class="">Water Flow</td>
                    <td>
                        <input type="text" name="txt_water_flow" id="txt_water_flow" style="width:122px;"
                               class="text_boxes_numeric"/>
                    </td>
                </tr>
                <tr>
                    <td id="production_date_td">Production Date</td>
                    <td>
                        <input type="text" name="txt_process_end_date" id="txt_process_end_date" class="datepicker"
                               style="width:122px;" readonly/>
                    </td>
                </tr>
                <tr>
                    <td id="process_end_date">Process End Date</td>
                    <td>
                        <input type="text" name="txt_process_date" id="txt_process_date" class="datepicker"
                               style="width:122px;" value="<? echo date('d-m-Y'); ?>" readonly/>
                    </td>
                </tr>
                <tr>
                    <td id="process_end_time">Process End Time</td>
                    <td>
                        <input type="text" name="txt_end_hours" id="txt_end_hours" class="text_boxes_numeric"
                               placeholder="Hours" style="width:50px;"
                               onBlur="fnc_move_cursor(this.value,'txt_end_hours','txt_end_date',2,23)"
                               value="<? echo date('H'); ?>"/>
                        <input type="text" name="txt_end_minutes" id="txt_end_minutes" class="text_boxes_numeric"
                               placeholder="Minutes" style="width:50px;"
                               onBlur="fnc_move_cursor(this.value,'txt_end_minutes','txt_end_date',2,59)"
                               value="<? echo date('i'); ?>"/>
                    </td>
                </tr>
                <td id="floor_caption">Floor</td>
                <td id="floor_td">
					<?
					echo create_drop_down("cbo_floor", 135, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 order by floor_name", "id,floor_name", 1, "-- Select Floor --", $selected, "", 0, "", "", "", "", 4);
					?>
                </td>
                <tr>
                    <td id="machine_caption">Machine Name</td>
                    <td id="machine_td">
						<?
						echo create_drop_down("cbo_machine_name", 135, $blank_array, "", 1, "-- Select Machine --", 0, "", 1);
						?>
                    </td>
                </tr>
                <tr>
                    <td id="result_caption">Result</td>
                    <td>
						<?
						echo create_drop_down("cbo_result_name", 135, $dyeing_result, "", 1, "-- Select Result --", 0, "", 0, "1,2,3,4,5,6", "", "", "", "");
						?>
                    </td>
                </tr>

                <tr>
                    <td id="result_caption">Fabric Type</td>
                    <td>
						<?
						echo create_drop_down("cbo_fabric_type", 135, $fabric_type_for_dyeing, "", 1, "-- Select --", 0, "", "", "", "", "", "");
						?>
                    </td>
                </tr>
                <tr>
                    <td>Shift Name</td>
                    <td>
						<?
						echo create_drop_down("cbo_shift_name", 135, $shift_name, "", 1, "-- Select Shift --", 0, "", 0, "", "", "", "", "");
						?>
                    </td>
                    </tr>
                    
                   
            </table>
            <input type="hidden" name="txt_process_start_date" id="txt_process_start_date"/>
            <input type="hidden" name="txt_start_minutes" id="txt_start_minutes" class="text_boxes_numeric"/>
            <input type="hidden" name="cbo_ltb_btb" id="cbo_ltb_btb" class="text_boxes_numeric"/>
            <script>
                process_check(document.getElementById('txt_process_id').value);
            </script>
        </fieldset>
		<?
	} ?>

    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>

	<?
	exit();
}
if ($action == "load_drop_down_knitting_com") {
	$data = explode("**", $data);
	//print_r($data);
	$company_id = $data[1];

	if ($data[0] == 1) {
		//$company_cond

		echo create_drop_down("cbo_service_company", 135, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "-- Select --", "", "load_drop_down('requires/dyeing_production_controller', this.value, 'load_drop_floor', 'floor_td' );load_batch_working_company(this.value);", "");
	} else if ($data[0] == 3) {
		echo create_drop_down("cbo_service_company", 135, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id,supplier_name", 1, "-- Select --", 0, "");
	} else {
		echo create_drop_down("cbo_service_company", 135, $blank_array, "", 1, "-- Select --", 0, "");
	}
	exit();
}
if ($action == "populate_data_from_data") {

	$sql = "select id, company_id, recv_number_prefix_num, dyeing_source, dyeing_company, receive_date, batch_id, process_id from inv_receive_mas_batchroll where id=$data and entry_form=63 and status_active=1 and is_deleted=0 ";
	//echo $sql;
	if ($db_type == 2) $group_concat = "listagg(roll_id ,',') within group (order by roll_id) as roll_id ";
	else if ($db_type == 0) $group_concat = "group_concat(roll_id)  as roll_id ";
	$res = sql_select($sql);
	foreach ($res as $row) {
		echo "$('#txt_issue_chalan').val('" . $row[csf("recv_number_prefix_num")] . "');\n";
		//echo "$('#cbo_company_id').val(".$row[csf("company_id")].");\n";
		//echo "$('#cbo_company_id').attr('disabled','true')".";\n";
		//echo "$('#cbo_process').val(".$row[csf("process_id")].");\n";
		//echo "$('#txt_issue_date').val('".change_date_format($row[csf("receive_date")])."');\n";
		echo "$('#cbo_service_source').val(" . $row[csf("dyeing_source")] . ");\n";
		echo "load_drop_down( 'requires/dyeing_production_controller', " . $row[csf("dyeing_source")] . "+'**'+" . $row[csf("company_id")] . ", 'load_drop_down_knitting_com', 'dyeing_company_td' );\n";
		echo "$('#cbo_service_company').val(" . $row[csf("dyeing_company")] . ");\n";

		//$batchno = return_field_value("batch_no","pro_batch_create_mst","id='".$row[csf("batch_id")]."'");
		$roll_id_concat = return_field_value("$group_concat", "pro_grey_batch_dtls", "mst_id='" . $data . "' and roll_id>0 ", "roll_id");
		$all_roll_concat = implode(",", array_unique(explode(",", $roll_id_concat)));
		echo "$('#txt_roll_id').val('" . $all_roll_concat . "');\n";
		//echo "$('#txt_batch_no').val('".$batchno."');\n";
		//echo "$('#hidden_batch_id').val(".$row[csf("batch_id")].");\n";
		echo "$('#txt_issue_mst_id').val(" . $row[csf("id")] . ");\n";
		//echo $data_array_mst=sql_select("select a.entry_form,b.width_dia_type,a.company_id,b.item_description,b.width_dia_type,b.prod_id,b.roll_no,b.roll_id, sum(b.batch_qnty) as batch_qnty from pro_batch_create_dtls b,pro_batch_create_mst a where b.roll_id in($all_roll_concat)  and a.id=b.mst_id and  a.entry_form in(0,36) and b.status_active=1 and b.is_deleted=0   group by b.id,b.item_description,b.width_dia_type,b.prod_id,a.entry_form,b.roll_id,b.roll_no,a.company_id");
	}
	exit();
}
if ($action == "check_issue_challan_no") {
	$data = explode("**", $data);
	//print_r($data);die;
	//$sql_result=sql_select("select  b.prod_id,b.width_dai_type,a.recv_number_prefix_num,b.roll_id,a.batch_id from pro_grey_batch_dtls  b, inv_receive_mas_batchroll a  where a.id=b.mst_id and b.mst_id=$data and a.entry_form=63 and b.status_active=1 and b.is_deleted=0  and a.status_active=1 and a.is_deleted=0");
	$sql = "select  a.id,a.recv_number_prefix_num from  inv_receive_mas_batchroll a  where   a.recv_number_prefix_num=$data[1]  and a.company_id=$data[0] and a.entry_form=63 and a.status_active=1 and a.is_deleted=0 ";
	$data_array = sql_select($sql, 1);
	if (count($data_array) > 0) {
		echo "1" . "_" . $data_array[0][csf('id')] . "_" . $data_array[0][csf('recv_number_prefix_num')];;
	} else {
		echo "0_";
	}
	exit();
}
if ($action == "check_issue_challan_no_scan") {
	$data = explode("**", $data);
	//print_r($data);die;
	//$sql_result=sql_select("select  b.prod_id,b.width_dai_type,a.recv_number_prefix_num,b.roll_id,a.batch_id from pro_grey_batch_dtls  b, inv_receive_mas_batchroll a  where a.id=b.mst_id and b.mst_id=$data and a.entry_form=63 and b.status_active=1 and b.is_deleted=0  and a.status_active=1 and a.is_deleted=0");
	$sql = "select  a.id,a.recv_number from  inv_receive_mas_batchroll a  where   a.recv_number='$data[1]'  and a.company_id=$data[0] and a.entry_form=63 and a.status_active=1 and a.is_deleted=0 ";
	$data_array = sql_select($sql, 1);
	if (count($data_array) > 0) {
		echo "1" . "_" . $data_array[0][csf('id')] . "_" . $data_array[0][csf('recv_number_prefix_num')];
	} else {
		echo "0_";
	}
	exit();
}

if ($action == "process_name_popup") {
	echo load_html_head_contents("Batch Number Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>
    <script>
        $(document).ready(function (e) {
            setFilterGrid('tbl_list_search', -1);
        });
        var selected_id = new Array();
        var selected_name = new Array();
        var buyer_id = '';
        var style_ref_array = new Array();
        function check_all_data() {
            var tbl_row_count = document.getElementById('tbl_list_search').rows.length;

            tbl_row_count = tbl_row_count - 1;
            for (var i = 1; i <= tbl_row_count; i++) {
                js_set_value(i);
            }
        }
        function toggle(x, origColor) {
            var newColor = 'yellow';
            if (x.style) {
                x.style.backgroundColor = ( newColor == x.style.backgroundColor ) ? origColor : newColor;
            }
        }
        function set_all() {
            var old = document.getElementById('txt_process_row_id').value;
            if (old != "") {
                old = old.split(",");
                for (var k = 0; k < old.length; k++) {
                    js_set_value(old[k])
                }
            }
        }
        function js_set_value(str) {
            toggle(document.getElementById('search' + str), '#FFFFCC');
            if (jQuery.inArray($('#txt_individual_id' + str).val(), selected_id) == -1) {
                selected_id.push($('#txt_individual_id' + str).val());
                selected_name.push($('#txt_individual' + str).val());
            }
            else {
                for (var i = 0; i < selected_id.length; i++) {
                    if (selected_id[i] == $('#txt_individual_id' + str).val()) break;
                }
                selected_id.splice(i, 1);
                selected_name.splice(i, 1);
            }
            var id = '';
            var name = '';
            for (var i = 0; i < selected_id.length; i++) {
                id += selected_id[i] + ',';
                name += selected_name[i] + ',';
            }
            id = id.substr(0, id.length - 1);
            name = name.substr(0, name.length - 1);

            $('#hidden_process_id').val(id);
            $('#hidden_process_name').val(name);
        }
    </script>
    </head>
    <body>
    <div align="center">
        <fieldset style="width:370px;margin-left:10px">
            <input type="hidden" name="hidden_process_id" id="hidden_process_id" class="text_boxes" value="">
            <input type="hidden" name="hidden_process_name" id="hidden_process_name" class="text_boxes" value="">
            <form name="searchprocessfrm_1" id="searchprocessfrm_1" autocomplete="off">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="350" class="rpt_table">
                    <thead>
                    <th width="50">SL</th>
                    <th>Process Name</th>
                    </thead>
                </table>
                <div style="width:350px; overflow-y:scroll; max-height:280px;" id="buyer_list_view" align="center">
                    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="332" class="rpt_table"
                           id="tbl_list_search">
						<?
						$i = 1;
						$process_row_id = '';
						$not_process_id_print_array = array(1, 2, 3, 4, 101, 120, 121, 122, 123, 124);
						$hidden_process_id = explode(",", $txt_process_id);
						foreach ($conversion_cost_head_array as $id => $name) {
							if (!in_array($id, $not_process_id_print_array)) {
								if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

								if (in_array($id, $hidden_process_id)) {
									if ($process_row_id == "") $process_row_id = $i; else $process_row_id .= "," . $i;
								}
								?>
                                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
                                    id="search<? echo $i; ?>" onClick="js_set_value(<? echo $i; ?>)">
                                    <td width="50" align="center"><?php echo "$i"; ?>
                                        <input type="hidden" name="txt_individual_id"
                                               id="txt_individual_id<?php echo $i ?>" value="<? echo $id; ?>"/>
                                        <input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>"
                                               value="<? echo $name; ?>"/>
                                    </td>
                                    <td><p><? echo $name; ?></p></td>
                                </tr>
								<?
								$i++;
							}
						}
						?>
                        <input type="hidden" name="txt_process_row_id" id="txt_process_row_id"
                               value="<?php echo $process_row_id; ?>"/>
                    </table>
                </div>
                <table width="350" cellspacing="0" cellpadding="0" style="border:none" align="center">
                    <tr>
                        <td align="center" height="30" valign="bottom">
                            <div style="width:100%">
                                <div style="width:50%; float:left" align="left">
                                    <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()"/>
                                    Check / Uncheck All
                                </div>
                                <div style="width:50%; float:left" align="left">
                                    <input type="button" name="close" onClick="parent.emailwindow.hide();"
                                           class="formbutton" value="Close" style="width:100px"/>
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>
            </form>
        </fieldset>
    </div>
    </body>
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    <script>
        set_all();
    </script>
    </html>
	<?
	exit();
}
if ($action == "process_name_popup_unload") {
	echo load_html_head_contents("Batch Number Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>
    <script>
        $(document).ready(function (e) {
            setFilterGrid('tbl_list_search', -1);
        });
        var selected_id = new Array();
        var selected_name = new Array();
        var buyer_id = '';
        var style_ref_array = new Array();
        function check_all_data() {
            var tbl_row_count = document.getElementById('tbl_list_search').rows.length;

            tbl_row_count = tbl_row_count - 1;
            for (var i = 1; i <= tbl_row_count; i++) {
                js_set_value(i);
            }
        }
        function toggle(x, origColor) {
            var newColor = 'yellow';
            if (x.style) {
                x.style.backgroundColor = ( newColor == x.style.backgroundColor ) ? origColor : newColor;
            }
        }
        function set_all() {
            var old = document.getElementById('txt_process_row_id').value;
            if (old != "") {
                old = old.split(",");
                for (var k = 0; k < old.length; k++) {
                    js_set_value(old[k])
                }
            }
        }
        function js_set_value(str) {
            toggle(document.getElementById('search' + str), '#FFFFCC');

            if (jQuery.inArray($('#txt_individual_id' + str).val(), selected_id) == -1) {
                selected_id.push($('#txt_individual_id' + str).val());
                selected_name.push($('#txt_individual' + str).val());
            }
            else {
                for (var i = 0; i < selected_id.length; i++) {
                    if (selected_id[i] == $('#txt_individual_id' + str).val()) break;
                }
                selected_id.splice(i, 1);
                selected_name.splice(i, 1);
            }
            var id = '';
            var name = '';
            for (var i = 0; i < selected_id.length; i++) {
                id += selected_id[i] + ',';
                name += selected_name[i] + ',';
            }
            id = id.substr(0, id.length - 1);
            name = name.substr(0, name.length - 1);

            $('#hidden_process_id').val(id);
            $('#hidden_process_name').val(name);
        }
    </script>
    </head>
    <body>
    <div align="center">
        <fieldset style="width:370px;margin-left:10px">
            <input type="hidden" name="hidden_process_id" id="hidden_process_id" class="text_boxes" value="">
            <input type="hidden" name="hidden_process_name" id="hidden_process_name" class="text_boxes" value="">
            <form name="searchprocessfrm_1" id="searchprocessfrm_1" autocomplete="off">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="350" class="rpt_table">
                    <thead>
                    <th width="50">SL</th>
                    <th>Process Name</th>
                    </thead>
                </table>
                <div style="width:350px; overflow-y:scroll; max-height:280px;" id="buyer_list_view" align="center">
                    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="332" class="rpt_table"
                           id="tbl_list_search">
						<?
						$i = 1;
						$process_row_id = '';
						$not_process_id_print_array = array();
						//echo $txt_process_id;die;
						$hidden_process_id = explode(",", $txt_process_id);
						/*$process_name='';
						$process_id_array=explode(",",$r_batch[csf("process_id")]);
						foreach($process_id_array as $val)
						{
							if($process_name=="") $process_name=$conversion_cost_head_array[$val]; else $process_name.=",".$conversion_cost_head_array[$val];
						}*/
						foreach ($conversion_cost_head_array as $id => $name) {
							if (in_array($id, $hidden_process_id)) {
								if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

								if (in_array($id, $hidden_process_id)) {
									if ($process_row_id == "") $process_row_id = $i; else $process_row_id .= "," . $i;
								}
								?>
                                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
                                    id="search<? echo $i; ?>" onClick="js_set_value(<? echo $i; ?>)">
                                    <td width="50" align="center"><?php echo "$i"; ?>
                                        <input type="hidden" name="txt_individual_id"
                                               id="txt_individual_id<?php echo $i ?>" value="<? echo $id; ?>"/>
                                        <input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>"
                                               value="<? echo $name; ?>"/>
                                    </td>
                                    <td><p><? echo $name; ?></p></td>
                                </tr>
								<?
								$i++;
							}
						}
						?>
                        <input type="hidden" name="txt_process_row_id" id="txt_process_row_id"
                               value="<?php echo $process_row_id; ?>"/>
                    </table>
                </div>
                <table width="350" cellspacing="0" cellpadding="0" style="border:none" align="center">
                    <tr>
                        <td align="center" height="30" valign="bottom">
                            <div style="width:100%">
                                <div style="width:50%; float:left" align="left">
                                    <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()"/>
                                    Check / Uncheck All
                                </div>
                                <div style="width:50%; float:left" align="left">
                                    <input type="button" name="close" onClick="parent.emailwindow.hide();"
                                           class="formbutton" value="Close" style="width:100px"/>
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>
            </form>
        </fieldset>
    </div>
    </body>
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    <script>
        set_all();
    </script>
    </html>
	<?
	exit();
}
///Issue Challan POPUP Start
if ($action == "issue_challan_popup") {

	echo load_html_head_contents("Issue Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	//echo $cbo_company_id;die;
	?>

    <script>

        function js_set_value(id) {
            $('#hidden_system_id').val(id);
            parent.emailwindow.hide();
        }

    </script>

    </head>

    <body>
    <div align="center" style="width:860px;">
        <form name="searchwofrm" id="searchwofrm">
            <fieldset style="width:860px; margin-left:2px">
                <table cellpadding="0" cellspacing="0" width="850" border="1" rules="all" class="rpt_table">
                    <thead>
                    <th>Issue Date Range</th>

                    <th>Search By</th>
                    <th id="search_by_td_up" width="100">Please Enter Issue No</th>
                    <th>Service Source</th>
                    <th>Service Company</th>
                    <th>
                        <input type="reset" name="reset" id="reset" value="Reset" style="width:100px"
                               class="formbutton"/>
                        <input type="hidden" name="hidden_system_id" id="hidden_system_id">
                    </th>
                    </thead>
                    <tr class="general">
                        <td align="center">
                            <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker"
                                   style="width:70px" readonly>To
                            <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"
                                   readonly>
                        </td>


                        <td align="center">
							<?
							$search_by_arr = array(1 => "Issue No");
							$dd = "change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";
							echo create_drop_down("cbo_search_by", 100, $search_by_arr, "", 0, "--Select--", 1, $dd, 0);
							?>
                        </td>
                        <td align="center" id="search_by_td">
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common"
                                   id="txt_search_common"/>
                        </td>
                        <td align="center">
							<?
							echo create_drop_down("cbo_service_source", 135, $knitting_source, "", 1, "-- Select --", $selected, "load_drop_down( 'dyeing_production_controller',this.value+'**'+$cbo_company_id,'load_drop_down_knitting_com','dyeing_company_td' );", "", "1,3");
							?>
                        </td>
                        <td id="dyeing_company_td">
							<?
							echo create_drop_down("cbo_service_company", 135, $blank_array, "", 1, "-- Select --", $selected, "", "", "");
							?>
                        </td>
                        <td align="center">
                            <input type="button" name="button2" class="formbutton" value="Show"
                                   onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $cbo_company_id; ?>, 'create_challan_search_list_view', 'search_div', 'dyeing_production_controller', 'setFilterGrid(\'tbl_list_search\',-1);')"
                                   style="width:100px;"/>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5" align="center" height="40"
                            valign="middle"><? echo load_month_buttons(1); ?></td>
                    </tr>
                </table>
                <div style="width:100%; margin-top:5px;" id="search_div" align="left"></div>
            </fieldset>
        </form>
    </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
	<?
}
if ($action == "create_challan_search_list_view") {
	$data = explode("_", $data);

	$search_string = "%" . trim($data[0]);
	$search_by = $data[1];
	$start_date = $data[2];
	$end_date = $data[3];
	$company_id = $data[4];

	if ($start_date != "" && $end_date != "") {
		if ($db_type == 0) {
			$date_cond = "and receive_date between '" . change_date_format(trim($start_date), "yyyy-mm-dd", "-") . "' and '" . change_date_format(trim($end_date), "yyyy-mm-dd", "-") . "'";
		} else {
			$date_cond = "and receive_date between '" . change_date_format(trim($start_date), '', '', 1) . "' and '" . change_date_format(trim($end_date), '', '', 1) . "'";
		}
	} else {
		$date_cond = "";
	}

	$search_field_cond = "";
	if (trim($data[0]) != "") {
		if ($search_by == 1) $search_field_cond = "and recv_number_prefix_num like '$search_string'";
	}

	if ($db_type == 0) {
		$year_field = "YEAR(insert_date) as year,";
	} else if ($db_type == 2) {
		$year_field = "to_char(insert_date,'YYYY') as year,";
	} else $year_field = "";//defined Later

	$sql = "select id, $year_field recv_number_prefix_num, recv_number, dyeing_source, dyeing_company, receive_date, process_id, batch_id from inv_receive_mas_batchroll where entry_form=63 and status_active=1 and is_deleted=0 and company_id=$company_id $search_field_cond $date_cond order by id";
	//echo $sql;//die;
	$result = sql_select($sql);

	$company_arr = return_library_array("select id, company_name from lib_company", 'id', 'company_name');
	$supllier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
	$batch_arr = return_library_array("select id, batch_no from pro_batch_create_mst", 'id', 'batch_no');
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="720" class="rpt_table">
        <thead>
        <th width="40">SL</th>
        <th width="70">Issue No</th>
        <th width="60">Year</th>
        <th width="120">Service Source</th>
        <th width="140">Service Company</th>
        <th width="110">Process</th>
        <th width="100">Batch</th>
        <th>Issue date</th>
        </thead>
    </table>
    <div style="width:740px; max-height:230px; overflow-y:scroll" id="list_container_batch" align="left">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="720" class="rpt_table"
               id="tbl_list_search">
			<?
			$i = 1;
			foreach ($result as $row) {
				if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

				$dye_comp = "&nbsp;";
				if ($row[csf('dyeing_source')] == 1)
					$dye_comp = $company_arr[$row[csf('dyeing_company')]];
				else
					$dye_comp = $supllier_arr[$row[csf('dyeing_company')]];
				?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
                    onClick="js_set_value('<? echo $row[csf('id')]; ?>');">
                    <td width="40"><? echo $i; ?></td>
                    <td width="70"><p>&nbsp;<? echo $row[csf('recv_number_prefix_num')]; ?></p></td>
                    <td width="60" align="center"><p><? echo $row[csf('year')]; ?></p></td>
                    <td width="120"><p><? echo $knitting_source[$row[csf('dyeing_source')]]; ?>&nbsp;</p></td>
                    <td width="140"><p><? echo $dye_comp; ?>&nbsp;</p></td>
                    <td width="110"><p><? echo $conversion_cost_head_array[$row[csf('process_id')]]; ?>&nbsp;</p></td>
                    <td width="100"><p><? echo $batch_arr[$row[csf('batch_id')]]; ?>&nbsp;</p></td>
                    <td align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
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

if ($action == "show_dtls_list_view") {

	$ex_data = explode("_", $data);
	$batch_id = $ex_data[0];
	$load_unload = $ex_data[1];
	$system_no = $ex_data[2];
	//echo $load_unload = $ex_data[2];
	$yarn_count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$color_name_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$po_id_arr = return_field_value("po_id", "pro_batch_create_dtls", "mst_id ='$batch_id' and is_deleted=0 and status_active=1");
	$company_id = return_field_value("company_id", "pro_batch_create_mst", "id ='$batch_id' and is_deleted=0 and status_active=1");
	if ($load_unload == 1) $load_unload_cond = "and a.load_unload_id in(1)";
	else if ($load_unload == 2) $load_unload_cond = "and a.load_unload_id in(2)";
	//else  $load_unload_cond="and load_unload_id in(0)";

	if ($db_type == 2) {
		$sql = "select a.id,listagg(cast(b.const_composition as varchar2(4000)),',') within group (order by b.const_composition) AS const_composition,listagg(b.gsm,',') within group (order by b.gsm ) as gsm ,listagg(cast(b.roll_no as varchar2(4000)),',') within group (order by b.roll_no) AS roll_no,listagg(b.dia_width,',') within group (order by b.dia_width ) as dia_width,listagg(b.width_dia_type,',') within group (order by b.width_dia_type ) as width_dia_type,listagg(b.roll_id,',') within group (order by b.roll_id ) as roll_id,sum(b.batch_qty) as batch_qty,sum(b.production_qty) as production_qty,sum(b.no_of_roll) as no_of_roll from pro_fab_subprocess a,pro_fab_subprocess_dtls b where a.id=b.mst_id and a.system_no=$system_no and a.entry_form=35 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $load_unload_cond  group by a.id";
	} else {
		$sql = "select a.id,group_concat(b.const_composition) AS const_composition,group_concat(b.gsm) as gsm,group_concat(b.roll_no) as roll_no ,group_concat(b.dia_width)  as dia_width,group_concat(b.width_dia_type)  as width_dia_type,group_concat(b.roll_id) as roll_id,sum(b.batch_qty) as batch_qty,sum(b.production_qty) as production_qty,sum(b.no_of_roll) as no_of_roll from pro_fab_subprocess a,pro_fab_subprocess_dtls b where a.id=b.mst_id and and a.system_no=$system_no  and a.entry_form=35 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $load_unload_cond   group by a.id";
	}
	//echo $sql;
	$result = sql_select($sql);
	$i = 1;
	$total_batch_qty = 0;
	$total_prod_qty = 0;
	?>

    <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" width="800" rules="all">
        <thead>
        <tr>
            <th>SL</th>
            <th>Cons & Composition</th>
            <th>GSM</th>
            <th>Dia/Width</th>
            <th>Dia Width Type</th>
            <th>Bacth Qty</th>
            <th>Prod. Qty</th>
        </tr>
        </thead>
        <tbody>
		<? foreach ($result as $row) {

			if ($i % 2 == 0) $bgcolor = "#E9F3FF";
			else $bgcolor = "#FFFFFF";
			//echo $load_unload;

			$roll_maintained = return_field_value("fabric_roll_level", "variable_settings_production", "company_name ='$company_id' and item_category_id=50 and variable_list=3 and is_deleted=0 and status_active=1");
			$page_upto_id = return_field_value("page_upto_id", "variable_settings_production", "company_name =$company_id and item_category_id=50 and variable_list=3 and is_deleted=0 and status_active=1");
			$total_batch_qty += $row[csf("batch_qty")];
			$total_prod_qty += $row[csf("production_qty")];
			$dia_type = '';
			$dia_type_id = array_unique(explode(",", $row[csf('width_dia_type')]));
			foreach ($dia_type_id as $dia_id) {

				if ($dia_type == "") $dia_type = $fabric_typee[$dia_id]; else $dia_type .= "," . $fabric_typee[$dia_id];
			}
			$cons_composition_cond = '';
			$cons_composition_arr = array_unique(explode(",", $row[csf('const_composition')]));
			foreach ($cons_composition_arr as $cons) {
				if ($cons_composition_cond == "") $cons_composition_cond = $cons; else $cons_composition_cond .= "," . $cons;
			}
			$gsm_cond = '';
			$gsm_cond_arr = array_unique(explode(",", $row[csf('gsm')]));
			foreach ($gsm_cond_arr as $gsm) {
				if ($gsm_cond == "") $gsm_cond = $gsm; else $gsm_cond .= "," . $gsm;
			}
			$dia_width_cond = '';
			$gsm_cond_arr = array_unique(explode(",", $row[csf('dia_width')]));
			foreach ($gsm_cond_arr as $dia) {
				if ($dia_width_cond == "") $dia_width_cond = $dia; else $dia_width_cond .= "," . $dia;
			}
			if (($page_upto_id == 2 || $page_upto_id > 2) && $roll_maintained == 1)//if($roll_maintained==1)
			{
				$roll_no = $row[csf("roll_no")];
			} else {
				$roll_no = $row[csf("no_of_roll")];
			}

			?>

            <tr bgcolor="<? echo $bgcolor; ?>"
                onClick='show_list_view("<? echo $batch_id . '_' . $load_unload . '_' . $po_id_arr . '_' . $row[csf("id")]; ?>","child_form_input_data","list_fabric_desc_container","requires/dyeing_production_controller");get_php_form_data("<? echo $row[csf("id")]; ?>","mst_id_child_form_input_data","requires/dyeing_production_controller")'
                style="cursor:pointer">
                <td width="30"><? echo $i; ?></td>
                <td width="80"><p><? echo $cons_composition_cond; ?></p></td>
                <td width="80"><p><? echo $gsm_cond; ?></p></td>
                <td width="70"><p><? echo $dia_width_cond; ?></p></td>
                <td width="130"><p><? echo $dia_type;//$row[csf("width_dia_type")] ; ?></p></td>

                <td width="80" align="right"><p><? echo $row[csf("batch_qty")]; ?></p></td>
                <td width="80" align="right"><p><? echo $row[csf("production_qty")]; ?></p></td>
            </tr>
			<? $i++;
		} ?>
        <tfoot>
        <th colspan="5" align="right">Sum</th>
        <th><? echo $total_batch_qty; ?></th>
        <th><? echo $total_prod_qty; ?></th>
        </tfoot>
        </tbody>
    </table>
	<?
	exit();

}
if ($action == "child_form_input_data") {

	//print($data);
	$ex_data = explode('_', $data);

	$batch_id = $ex_data[0];
	$load_unload = $ex_data[1];
	$po_id = $ex_data[2];
	$mst_id = $ex_data[3];
	$entry_form = $ex_data[4];
	if ($load_unload == 1) $load_unload_cond = "and a.load_unload_id in(1)";
	else if ($load_unload == 2) $load_unload_cond = "and a.load_unload_id in(2)";
	//echo "select company_id from pro_fab_subprocess where batch_id='$batch_id' and entry_form=35 and is_deleted=0 and status_active=1";
	$company_id = return_field_value("company_id", "pro_fab_subprocess", "batch_id='$batch_id' and entry_form=35 and is_deleted=0 and status_active=1");
	//echo $company_id.'dedtrtr';
	$fabric_desc_arr = array();
	$prodData = sql_select("select id,detarmination_id from product_details_master where item_category_id=13");
	//----------------------------------------------------------------------------
	//new implement
	$fabricData = sql_select("select variable_list,fabric_roll_level from variable_settings_production where company_name ='$company_id' and variable_list in(3) and item_category_id=13 and is_deleted=0 and status_active=1");
	foreach ($fabricData as $row) {
		if ($row[csf('variable_list')] == 3) {
			$roll_maintained_id = $row[csf('fabric_roll_level')];
		}

	}
	$yarn_lot_arr = array();
	if ($db_type == 0) {
		if ($roll_maintained_id == 1) {
			$yarn_lot_data = sql_select("select  b.po_id, b.prod_id, group_concat(d.yarn_lot) as yarn_lot, group_concat(d.yarn_count) as yarn_count, group_concat(d.brand_id) as brand_id
						from pro_batch_create_mst a,pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d,  inv_receive_master e
						where a.id=b.mst_id and b.roll_id=c.id and c.dtls_id=d.id and d.mst_id=e.id  and a.id=$batch_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and e.entry_form in(2,22)
						group by  b.po_id, b.prod_id");
		} else {
			$yarn_lot_data = sql_select("select  b.po_id, b.prod_id, group_concat(d.yarn_lot) as yarn_lot, group_concat(d.yarn_count) as yarn_count, group_concat(d.brand_id) as brand_id
						from pro_batch_create_mst a,pro_batch_create_dtls b, pro_grey_prod_entry_dtls d,  inv_receive_master e
						where a.id=b.mst_id and b.prod_id=d.prod_id and d.mst_id=e.id and a.id=$batch_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and e.entry_form in(2,22)
						group by  b.po_id, b.prod_id");
		}
	} else if ($db_type == 2) {

		if ($roll_maintained_id == 1) {
			$yarn_lot_data = sql_select("select  b.po_id, b.prod_id, LISTAGG(CAST(d.yarn_lot AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY d.yarn_lot) as yarn_lot, LISTAGG(CAST(d.yarn_count AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY d.yarn_count) as yarn_count,LISTAGG(CAST(d.brand_id AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY d.brand_id) as brand_id
						from pro_batch_create_mst a,pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d,  inv_receive_master e
						where a.id=b.mst_id and b.roll_id=c.id and c.dtls_id=d.id and d.mst_id=e.id and a.id=$batch_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and e.entry_form in(2,22)
						group by b.po_id, b.prod_id");
		} else {

			$yarn_lot_data = sql_select("select  b.po_id, b.prod_id, LISTAGG(CAST(d.yarn_lot AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY d.yarn_lot) as yarn_lot, LISTAGG(CAST(d.yarn_count AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY d.yarn_count) as yarn_count,LISTAGG(CAST(d.brand_id AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY d.brand_id) as brand_id
						from pro_batch_create_mst a,pro_batch_create_dtls b, pro_grey_prod_entry_dtls d,  inv_receive_master e
						where a.id=b.mst_id  and d.mst_id=e.id and b.prod_id=d.prod_id  and a.id=$batch_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and e.entry_form in(2,22)
						group by b.po_id, b.prod_id");
		}
	}
	foreach ($yarn_lot_data as $rows) {
		//echo $rows[csf('brand_id')];
		$yarn_lot = explode(",", $rows[csf('yarn_lot')]);
		$brand_id = explode(",", $rows[csf('brand_id')]);
		$yarn_count = explode(",", $rows[csf('yarn_count')]);
		$yarn_lot_arr[$rows[csf('prod_id')]][$rows[csf('po_id')]]['lot'] = implode(",", array_unique($yarn_lot));
		$yarn_lot_arr[$rows[csf('prod_id')]][$rows[csf('po_id')]]['yarn_count'] = implode(",", array_unique($yarn_count));
		$yarn_lot_arr[$rows[csf('prod_id')]][$rows[csf('po_id')]]['brand_id'] = implode(",", array_unique($brand_id));
	}

	//----------------------------------------------------------------------------
	foreach ($prodData as $row) {
		$fabric_desc_arr[$row[csf('id')]]['detarmination_id'] = $row[csf('detarmination_id')];
	}
	$sql_result = sql_select("select a.id,b.id as dtls_id,b.prod_id,b.const_composition,b.remarks,b.gsm,b.dia_width, b.width_dia_type, b.batch_qty, b.production_qty,b.barcode_no, b.roll_no, b.no_of_roll,b.roll_id,b.rate,b.amount from pro_fab_subprocess a,pro_fab_subprocess_dtls b where a.id=b.mst_id and a.id=$mst_id and a.entry_form=35 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $load_unload_cond");
	//echo "select a.id,b.id as dtls_id,b.prod_id,b.const_composition,b.remarks,b.gsm,b.dia_width, b.width_dia_type, b.batch_qty, b.production_qty,b.barcode_no, b.roll_no, b.no_of_roll,b.roll_id,b.rate,b.amount from pro_fab_subprocess a,pro_fab_subprocess_dtls b where a.id=b.mst_id and a.id=$mst_id and a.entry_form=35 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $load_unload_cond";
	//if(count($sql_result)>0)
	///{
	$roll_maintained = return_field_value("fabric_roll_level", "variable_settings_production", "company_name ='$company_id' and item_category_id=50 and variable_list=3 and is_deleted=0 and status_active=1");
	$page_upto_id = return_field_value("page_upto_id", "variable_settings_production", "company_name ='$company_id' and item_category_id=50 and variable_list=3 and is_deleted=0 and status_active=1");

	//echo $company_id.'='.$roll_maintained;
	//$subprocess_dtls=return_field_value("id ", "pro_fab_subprocess_dtls", "id=$hidden_buyer_id  and status_active=1 and is_deleted=0","job_no");
	$i = 1;
	foreach ($sql_result as $row) {
		//$desc=explode(",",$row[csf('item_description')]);


		$cons_comps = $row[csf('const_composition')];
		$gsm = $row[csf('gsm')];
		$dia_width = $row[csf('dia_width')];
		$width_dia_type = $row[csf('width_dia_type')];
		$batch_qty = $row[csf('batch_qty')];
		$production_qty = $row[csf('production_qty')];

		$roll_id = $row[csf('roll_id')];
		$prod_id = $row[csf('prod_id')];
		$update_id = $row[csf('dtls_id')];
		//if($roll_maintained==1)
		if (($page_upto_id == 2 || $page_upto_id > 2) && $roll_maintained == 1) {

			$roll_no = $row[csf('roll_no')];
			$readonly = "readonly";
			$barcode_no=$row[csf('barcode_no')];
		} else {
			$roll_no = $row[csf('no_of_roll')];
			$readonly = "";
		}
		$brand = $lot = $yarn_lot_arr[$prod_id][$po_id]['brand_id'];
		$lot = $yarn_lot_arr[$prod_id][$po_id]['lot'];
		$brand_id = explode(',', $brand);
		$brand_value = "";
		foreach ($brand_id as $val) {
			if ($val > 0) {
				if ($brand_value == '') $brand_value = $brand_name[$val]; else $brand_value .= ", " . $brand_name[$val];
			}
		}
		$y_count_id = $yarn_lot_arr[$prod_id][$po_id]['yarn_count'];
		if ($db_type == 0) {
			$count_id = array_unique(explode(",", $y_count_id));
		} else {
			$count_id = array_unique(explode(",", $y_count_id));
		}
		//print_r( $count_id).'aziz';
		//array_unique(explode(',',$y_count));
		$yarn_count_value = '';
		foreach ($count_id as $val) {
			if ($val > 0) {
				if ($yarn_count_value == '') $yarn_count_value = $yarncount[$val]; else $yarn_count_value .= ", " . $yarncount[$val];
			}
		}
		?>
        <tr class="general" id="row_<? echo $i; ?>">
            <td width="60" id="sl_<? echo $i; ?>">
				<?
				if (($page_upto_id == 2 || $page_upto_id > 2) && $roll_maintained == 1) { ?>
                    <input type="checkbox" id="checkRow_<? echo $i; ?>" name="checkRow_<? echo $i; ?>" checked>
					<?
					echo $i;
				} else {
					echo $i;
				}
				?>

                &nbsp; &nbsp;</td>
            <td><input type="text" name="txtconscomp_<? echo $i; ?>" id="txtconscomp_<? echo $i; ?>" class="text_boxes" style="width:170px;" value="<? echo $cons_comps; ?>" disabled/></td>
            <td><input type="text" name="txtgsm_<? echo $i; ?>" id="txtgsm_<? echo $i; ?>" class="text_boxes_numeric"  style="width:40px;" value="<? echo trim($gsm); ?>"/></td>
            <td><input type="text" name="txtdiawidth_<? echo $i; ?>" id="txtdiawidth_<? echo $i; ?>" class="text_boxes"  style="width:50px;" value="<? echo $dia_width; //$row[csf('width_dia_type')];
				?>" disabled/></td>
            <td><input type="text" name="txtdiatype_<? echo $i; ?>" id="txtdiatype_<? echo $i; ?>" class="text_boxes" style="width:70px;" value="<? echo $fabric_typee[$width_dia_type]; ?>" disabled/>
                <input type="hidden" name="hiddendiatypeid_<? echo $i; ?>" id="hiddendiatypeid_<? echo $i; ?>"  value="<? echo $row[csf('width_dia_type')]; ?>" readonly/>
            </td>
            <td><input type="text" name="txtroll_<? echo $i; ?>" id="txtroll_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $roll_no; ?>" style="width:40px;" <? echo $readonly; ?> />
			<input type="hidden" name="txtbarcode_<? echo $i; ?>" id="txtbarcode_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $barcode_no; ?>" style="width:40px;" <? echo $readonly; ?> />
               <input type="hidden"  name="rollid_<? echo $i; ?>"  id="rollid_<? echo $i; ?>" style="width:35px;" value="<? echo $roll_id; ?>" class="text_boxes_numeric"/>

            </td>
            <td><input type="text" name="txtbatchqnty_<? echo $i; ?>" id="txtbatchqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px;" onKeyUp="calculate_production_qnty2();" value="<? echo $batch_qty; ?>" disabled/>
                <input type="hidden" name="txtprodid_<? echo $i; ?>" id="txtprodid_<? echo $i; ?>" value="<? echo $prod_id; ?>"/>
                <input type="hidden" name="txtdeterid_<? echo $i; ?>" id="txtdeterid_<? echo $i; ?>" value="<? echo $fabric_desc_arr[$row[csf('prod_id')]]['detarmination_id']; ?>"/>
                <input type="hidden" name="updateiddtls_<? echo $i; ?>" id="updateiddtls_<? echo $i; ?>" class="text_boxes" style="width:40px;" value="<? echo $row[csf('dtls_id')]; ?>" readonly/>
            </td>
            <td><input type="text" name="txtprodqnty_<? echo $i; ?>" id="txtprodqnty_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $production_qty; ?>"
                       onKeyUp="calculate_production_qnty();" style="width:60px;"/></td>
            <td><input type="text" name="txtrate_<? echo $i; ?>" id="txtrate_<? echo $i; ?>" class="text_boxes_numeric"  style="width:60px;" value="<? echo $row[csf('rate')]; ?>" readonly/></td>
            <td><input type="text" name="txtamount_<? echo $i; ?>" id="txtamount_<? echo $i; ?>" class="text_boxes_numeric" style="width:70px;" value="<? echo $row[csf('amount')]; ?>" readonly/>
            </td>

            <td><input type="text" name="txtlot_<? echo $i; ?>" id="txtlot_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px;" value="<? echo $lot; ?>" disabled readonly/></td>
            <td><input type="text" name="txtyarncount_<? echo $i; ?>" id="txtyarncount_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px;" value="<? echo $yarn_count_value; ?>" disabled readonly/></td>
            <td><input type="text" name="txtbrand_<? echo $i; ?>" id="txtbrand_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px;" value="<? echo $brand_value; ?>" disabled   readonly/>
			<input type="hidden" name="txtremark_<? echo $i; ?>" id="txtremark_<? echo $i; ?>" class="text_boxes_numeric" style="width:60px;" value="<? echo $row[csf('remarks')]; ?>" disabled   readonly/>
			</td>
        </tr>
		<?
		//$b_qty+= $batch_qty;

		$tot_batch_qty += $batch_qty;
		$tot_production_qty += $production_qty;
		$tot_amount += $row[csf('amount')];

		$i++;

	} ?>
    <tr>
        <td colspan="6" align="right"><b>Sum:</b> <? //echo $b_qty;
			?> </td>
        <td align="right"><b><input type="text" name="total_batch_qnty" id="total_batch_qnty" class="text_boxes_numeric"
                                    style="width:60px" value="<? if($tot_batch_qty>100) echo number_format($tot_batch_qty,0);else echo $tot_batch_qty;//below of fraction 50 ?>" readonly/> </b>
        </td>
        <td><input type="text" name="total_production_qnty" id="total_production_qnty"
                   value="<? if($tot_production_qty>100) echo number_format($tot_production_qty,0);else echo $tot_production_qty; ?>" class="text_boxes_numeric" style="width:60px"
                   readonly/></td>
        <td align="right"></td>
        <td align="right"><input type="text" name="total_amount" id="total_amount" class="text_boxes_numeric"
                                 style="width:70px" value="<? echo $tot_amount; ?>" readonly/></td>

    </tr>
	<?

	exit();

}
if ($action == "mst_id_child_form_input_data") {
	$sql_result = sql_select("select a.id, a.company_id, a.service_source, a.service_company, a.length_shrinkage, a.width_shrinkage, a.spirality, a.received_chalan, a.issue_chalan, a.issue_challan_mst_id, a.process_end_date, a.production_date, a.process_start_date, a.process_id, a.end_hours, a.end_minutes, a.start_hours, a.start_minutes, a.temparature, a.stretch, a.over_feed, a.feed_in, a.pinning, a.speed_min, a.floor_id, a.machine_id, a.shift_name, a.remarks
				from pro_fab_subprocess a
				where a.id=$data and a.entry_form=35  and a.status_active=1 and a.is_deleted=0");
	$variable_production_roll = return_field_value("fabric_roll_level", "variable_settings_production", "variable_list=3 and item_category_id=50 and status_active=1 and is_deleted=0 and company_name=" . $sql_result[0][csf('company_id')] . "");
	$page_upto_id = return_field_value("page_upto_id", "variable_settings_production", "company_name =" . $sql_result[0][csf('company_id')] . " and item_category_id=50 and variable_list=3 and is_deleted=0 and status_active=1");


	echo "document.getElementById('txt_update_id').value 				= '" . $sql_result[0][csf("id")] . "';\n";
	echo "set_button_status(1, '" . $_SESSION['page_permission'] . "', 'fnc_pro_fab_subprocess',1,1);\n";
//echo "set_button_status(1, permission, 'fnc_pro_fab_subprocess',1,1);\n";
	exit();
}
if ($action == "show_dtls_batch_list_view") {
//echo $data."<br>";
	$ex_data = explode("_", $data);
	$batch_id = $ex_data[0];
	$load_unload = $ex_data[1];
	$system_no = $ex_data[2];
	$entry_form_no = $ex_data[3];
	//echo $load_unload = $ex_data[2];
	//$load_unload=$ex_data[0];
	//$batch_id=$ex_data[1];
	//$batch_no=$ex_data[2];
	//$company=$ex_data[3];

	$yarn_count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$color_name_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$po_id_arr = return_field_value("po_id", "pro_batch_create_dtls", "mst_id ='$batch_id' and is_deleted=0 and status_active=1");
	$company_id = return_field_value("company_id", "pro_batch_create_mst", "id ='$batch_id' and is_deleted=0 and status_active=1");
	if ($load_unload == 1) {
		$load_unload_cond = "and a.load_unload_id in(1)";
		$dyeing_date_con = "a.process_end_date";
		$sql = "select a.id,a.company_id,a.batch_id,a.batch_no,sum(b.batch_qty) as batch_qty,sum(b.production_qty) as production_qty, $dyeing_date_con  from pro_fab_subprocess a,pro_fab_subprocess_dtls b where a.id=b.mst_id and a.system_no=$system_no and a.entry_form=35 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $load_unload_cond  group by a.id,a.batch_id,a.batch_no,a.company_id,$dyeing_date_con order by a.id";
		//echo $sql;
	} else {

		$load_unload_cond = "and a.load_unload_id in(2)";
		$dyeing_date_con = "a.process_end_date";
		$sql = "select a.id,a.company_id,a.batch_id,a.batch_no,sum(b.batch_qty) as batch_qty,sum(b.production_qty) as production_qty, $dyeing_date_con  from pro_fab_subprocess a,pro_fab_subprocess_dtls b where a.id=b.mst_id and a.system_no=$system_no and a.entry_form=35 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $load_unload_cond  group by a.id,a.batch_id,a.batch_no,a.company_id,$dyeing_date_con order by a.id";
		//echo $sql;
	}
	//else  $load_unload_cond="and load_unload_id in(0)";
	/*	if($load_unload==1)
		{

		}
		else
		{

		}*/

	$result = sql_select($sql);
	$i = 1;
	$total_batch_qty = 0;
	$total_prod_qty = 0;
	?>

    <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" width="800" rules="all">
        <thead>
        <tr>
            <th>SL</th>
            <th>Batch No</th>
			<?
			if ($load_unload == 1) {
				?>
                <th>Process Start Date</th>
				<?
			} else {
				?>
                <th>Production Date</th>
				<?
			}
			?>

            <th>Batch Qty</th>
            <th>Prod. Qty</th>
        </tr>
        </thead>
        <tbody>
		<? foreach ($result as $row) {

			if ($i % 2 == 0) $bgcolor = "#E9F3FF";
			else $bgcolor = "#FFFFFF";
			//echo $load_unload;

			//$roll_maintained=return_field_value("fabric_roll_level","variable_settings_production","company_name ='$company_id' and item_category_id=50 and variable_list=3 and is_deleted=0 and status_active=1");
			//	$page_upto_id=return_field_value("page_upto_id","variable_settings_production","company_name =$company_id and item_category_id=50 and variable_list=3 and is_deleted=0 and status_active=1");
			$total_batch_qty += $row[csf("batch_qty")];
			$total_prod_qty += $row[csf("production_qty")];
			//$dia_type='';
			//$dia_type_id=array_unique(explode(",",$row[csf('width_dia_type')]));
			?>

            <tr bgcolor="<? echo $bgcolor; ?>"
                onClick='show_list_view("<? echo $batch_id . '_' . $load_unload . '_' . $po_id_arr . '_' . $row[csf("id")]. '_' . $entry_form_no; ?>","child_form_input_data","list_fabric_desc_container","requires/dyeing_production_controller");get_php_form_data("<? echo $load_unload . '_' . $row[csf("batch_id")] . '_' . $row[csf("batch_no")] . '_' . $row[csf("company_id")]. '_' . $entry_form_no; ?>","populate_data_from_batch2","requires/dyeing_production_controller")'
                style="cursor:pointer">
                <td width="30"><? echo $i; ?></td>
                <td width="80"><p><? echo $row[csf("batch_no")]; ?></p></td>
                <td width="80"><p><? echo change_date_format($row[csf("process_end_date")]); ?></p></td>
                <td width="80" align="right"><p><? echo number_format($row[csf("batch_qty")], 2); ?></p></td>
                <td width="80" align="right"><p><? echo number_format($row[csf("production_qty")], 2); ?></p></td>
            </tr>
			<? $i++;
		} ?>
        <tfoot>
        <th colspan="3" align="right">Sum</th>
        <th><? echo number_format($total_batch_qty, 2); ?></th>
        <th><? echo number_format($total_prod_qty, 2); ?></th>
        </tfoot>
        </tbody>
    </table>
	<?
	exit();

}
if ($action == "service_booking_popup") {
	echo load_html_head_contents("Booking Search", "../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>

    <script>
        var permission = "<? echo $_SESSION['page_permission']; ?>";
        function js_set_value(booking_no) {
            document.getElementById('selected_booking').value = booking_no;
            parent.emailwindow.hide();
        }

    </script>

    </head>

    <body>
    <div align="center" style="width:100%;">
        <form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
            <table cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all" width="900">
                <thead>
                <tr>
                    <th colspan="3"></th>
                    <th>
						<?
						echo create_drop_down("cbo_search_category", 130, $string_search_type, '', 1, "-- Search Catagory --");
						?>
                    </th>
                    <th colspan="3"></th>
                </tr>
                <tr>
                    <th width="160">Company Name</th>
                    <th width="160">Buyer Name</th>
                    <th width="120">Booking No</th>
                    <th width="200">Date Range</th>
                    <th><input type="reset" id="rst" class="formbutton" style="width:100px"
                               onClick="reset_form('searchorderfrm_1','search_div','','','')"></th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td align="center"><input type="hidden" id="selected_booking">
						<?
						echo create_drop_down("cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 and id=" . $cbo_company_id . " order by company_name", "id,company_name", 1, "-- Select Company --", '', "load_drop_down( 'dyeing_production_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );");
						?>
                    </td>
                    <td id="buyer_td" align="center">
						<?
						echo create_drop_down("cbo_buyer_name", 150, $blank_array, "", 1, "-- Select Buyer --");
						?>
                    </td>
                    <td align="center">
                        <input type="text" id="txt_booking_no" name="txt_booking_no" class="text_boxes"
                               style="width:100px" placeholder="Write Booking No">
                    </td>
                    <td align="center">
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
                    </td>
                    <td align="center">
                        <input type="button" name="button2" class="formbutton" value="Show"
                               onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_search_category').value+'_'+document.getElementById('txt_booking_no').value+'_'+'<?php echo $supplier_id . "_" . $process_id; ?>', 'create_booking_search_list_view', 'search_div', 'dyeing_production_controller', 'setFilterGrid(\'table_body\',-1)')"
                               style="width:100px;"/>
                    </td>
                </tr>
                <tr>
                    <td align="center" height="40" valign="middle" colspan="6"><? echo load_month_buttons(1); ?></td>
                </tr>
                </tbody>
            </table>

        </form>
    </div>
    <div id="search_div" style="margin-top:10px;"></div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
	<?
}


if ($action == "create_booking_search_list_view") {
	$data = explode('_', $data);
	$company_id = $data[0];
	$buyer_id = $data[1];
	$date_form = $data[2];
	$date_to = $data[3];
	$search_catgory = $data[4];
	$booking_no = $data[5];
	$supplier_id = $data[6];
	$process_id = $data[7];
	$sql_cond = "";
	if ($company_id != 0) $sql_cond = " and a.company_id='$company_id'"; else {
		echo "Please Select Company First.";
		die;
	}
	if ($buyer_id != 0) $sql_cond .= " and a.buyer_id='$buyer_id'";

	if ($db_type == 0) {
		if ($date_form != "" && $date_to != "") $sql_cond .= "and a.booking_date  between '" . change_date_format($date_form, "yyyy-mm-dd", "-") . "' and '" . change_date_format($date_to, "yyyy-mm-dd", "-") . "'";
	}
	if ($db_type == 2) {
		if ($date_form != "" && $date_to != "") $sql_cond .= "and a.booking_date  between '" . change_date_format($date_form, "yyyy-mm-dd", "-", 1) . "' and '" . change_date_format($date_to, "yyyy-mm-dd", "-", 1) . "'";
	}

	if ($booking_no != "") $sql_cond .= " and a.booking_no_prefix_num=$booking_no";

	$buyer_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');
	$comp_arr = return_library_array("select id, company_short_name from lib_company", 'id', 'company_short_name');
	$suplier_arr = return_library_array("select id, short_name from lib_supplier", 'id', 'short_name');
	$job_no_arr = return_library_array("select b.id, a.job_no_prefix_num from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst", 'id', 'job_no_prefix_num');
	$po_no_arr = return_library_array("select id, po_number from wo_po_break_down", 'id', 'po_number');

	$sql_booking = sql_select("select f.lib_yarn_count_deter_id,d.pre_cost_fabric_cost_dtls_id,sum(d.amount) as amount, sum(d.wo_qnty) as wo_qnty,d.booking_no  from wo_pre_cost_fab_conv_cost_dtls e,wo_pre_cost_fabric_cost_dtls f, wo_booking_dtls d,wo_booking_mst a, wo_po_details_master b where e.job_no=f.job_no and f.id=e.fabric_description and e.id=d.pre_cost_fabric_cost_dtls_id and d.booking_no=a.booking_no and a.job_no=b.job_no and a.booking_type=3 and a.supplier_id=$supplier_id and  a.status_active=1 and a.is_deleted=0 and d.process=$process_id $sql_cond group by d.booking_no,d.pre_cost_fabric_cost_dtls_id,f.lib_yarn_count_deter_id ");
	$booking_determination_rate = array();
	foreach ($sql_booking as $val) {
		$booking_determination_rate[$val[csf('booking_no')]][$val[csf('lib_yarn_count_deter_id')]]['wo_qnty'] += $val[csf('wo_qnty')];
		$booking_determination_rate[$val[csf('booking_no')]][$val[csf('lib_yarn_count_deter_id')]]['amount'] += $val[csf('amount')];
	}

	$sql = "select   sum(d.amount)/ sum(d.wo_qnty) as rate,a.booking_no_prefix_num, a.booking_no ,a.booking_date, a.company_id, a.buyer_id, a.job_no, a.po_break_down_id, a.item_category, a.fabric_source, a.supplier_id, b.job_no_prefix_num,a.currency_id, a.exchange_rate  from wo_booking_dtls d,wo_booking_mst a, wo_po_details_master b where d.booking_no=a.booking_no and a.job_no=b.job_no and a.booking_type=3 and a.supplier_id=$supplier_id and  a.status_active=1 and a.is_deleted=0 and d.process=$process_id $sql_cond group by a.booking_no_prefix_num, a.booking_no ,a.booking_date, a.company_id, a.buyer_id, a.job_no, a.po_break_down_id, a.item_category, a.fabric_source, a.supplier_id, b.job_no_prefix_num ,a.currency_id, a.exchange_rate order by a.booking_no";
	//echo $sql;
	?>
    <table cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all" width="970">
        <thead>
        <tr>
            <th width="40">SL</th>
            <th width="50">Booking No</th>
            <th width="70">Booking Date</th>
            <th width="60">Company</th>
            <th width="60">Buyer</th>
            <th width="60">Job No</th>
            <th width="200">PO number</th>
            <th width="120">Item Category</th>
            <th width="110">Fabric Source</th>
            <th>Supplier</th>
        </tr>
        </thead>
    </table>
    <div id="scroll_body" style="width:990px; max-height:350; overflow-y:scroll" align="center">
        <table cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all" width="970"
               id="table_body">
            <tbody>
			<?
			$sql_result = sql_select($sql);
			$i = 1;
			foreach ($sql_result as $row) {
				$determination_data = '';
				foreach ($booking_determination_rate[$row[csf("booking_no")]] as $deter_id => $deter_val) {
					$determination_data .= $deter_id . "*" . $deter_val['amount'] / $deter_val['wo_qnty'] . "**";
				}
				if ($i % 2 == 0)
					$bgcolor = "#E9F3FF";
				else
					$bgcolor = "#FFFFFF";
				?>
                <tr bgcolor="<? echo $bgcolor; ?>"
                    onClick="js_set_value('<? echo $row[csf("booking_no")] . "_" . $row[csf("currency_id")] . "_" . $row[csf("exchange_rate")] . "_" . $determination_data; ?>')"
                    style="cursor:pointer;">
                    <td width="40" align="center"><? echo $i; ?></td>
                    <td width="50" align="center"><p><? echo $row[csf("booking_no_prefix_num")]; ?>&nbsp;</p></td>
                    <td width="70" align="center">
                        <p><? if ($row[csf("booking_date")] != "" && $row[csf("booking_date")] != "0000-00-00") echo change_date_format($row[csf("booking_date")]); ?>
                            &nbsp;</p></td>
                    <td width="60"><p><? echo $comp_arr[$row[csf("company_id")]]; ?>&nbsp;</p></td>
                    <td width="60"><p><? echo $buyer_arr[$row[csf("buyer_id")]]; ?>&nbsp;</p></td>
                    <td width="60" align="center"><p><? echo $row[csf("job_no_prefix_num")]; ?>&nbsp;</p></td>
                    <td width="200"><p>
							<?
							$po_id_arr = array_unique(explode(",", $row[csf("po_break_down_id")]));
							$all_po = "";
							foreach ($po_id_arr as $po_id) {
								$all_po .= $po_no_arr[$po_id] . ",";
							}
							$all_po = chop($all_po, " , ");
							echo $all_po;
							?>&nbsp;</p></td>
                    <td width="120"><p><? echo $item_category[$row[csf("item_category")]]; ?>&nbsp;</p></td>
                    <td width="110"><p><? echo $fabric_source[$row[csf("fabric_source")]]; ?>&nbsp;</p></td>
                    <td><p><? echo $suplier_arr[$row[csf("supplier_id")]]; ?>&nbsp;</p></td>
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

if ($action == "load_drop_down_buyer") {
	echo create_drop_down("cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "");
}

if ($action == "sys_popup") {
	echo load_html_head_contents("Popup Info", "../../../", 1, 1, $unicode);
	extract($_REQUEST);


	?>

    <script>
        function js_set_value(sys_number) {
            //alert(sys_number);
            $("#hidden_sys_number").val(sys_number); // mrr number
            parent.emailwindow.hide();
        }
    </script>

    </head>

    <body>
    <div align="center" style="width:100%;">
        <form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
            <table width="780" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
                <thead>
                <tr>
                    <th>Company</th>
                    <th>Functional Batch No</th>
                    <th>Batch No</th>
                    <th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px"
                               class="formbutton"/></th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td align="center">
						<?
						echo create_drop_down("cbo_company_id", 172, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name", "id,company_name", 1, "-- Select Company --", $company, "", 0);
						?>
                    </td>

                    <td width="" align="center">
                        <input type="text" style="width:140px" class="text_boxes" name="txt_system_no"
                               id="txt_system_no" value="<? echo $system_no; ?>"/>
                    </td>
                    <td width="" align="center">
                        <input type="text" style="width:140px" class="text_boxes" name="txt_batch_no" id="txt_batch_no"
                               value="<? echo $batch_no; ?>"/>
                    </td>
                    <td align="center">
                        <input type="button" name="btn_show" class="formbutton" value="Show"
                               onClick="show_list_view ( document.getElementById('cbo_company_id').value+'_'+document.getElementById('txt_system_no').value+'_'+document.getElementById('txt_batch_no').value+'_'+'<? echo $load_unload; ?>', 'create_sys_search_list_view', 'search_div', 'dyeing_production_controller', 'setFilterGrid(\'list_view\',-1)')"
                               style="width:100px;"/>
                    </td>
                </tr>
                <tr>
                    <td align="center" height="40" valign="middle" colspan="5">
						<? //echo load_month_buttons(1);
						?>
                        <!-- Hidden field here-------->
                        <input type="hidden" id="hidden_sys_number" value="hidden_sys_number"/>
                        <input type="hidden" id="hidden_update_id" value="hidden_update_id"/>
                        <!-- ---------END------------->
                    </td>
                </tr>
                </tbody>
                </tr>
            </table>
            <br>
            <div align="center" valign="top" id="search_div"></div>
        </form>
    </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
	<?
}


if ($action == "create_sys_search_list_view") {
	$ex_data = explode("_", $data);
	$company = $ex_data[0];
	$system_no = $ex_data[1];
	$batch_no = $ex_data[2];
	$load_unload = $ex_data[3];
	//echo $load_unload;
	//echo $fromDate;die;
	$sql_cond = "";

	/*if($db_type==2)
		{
		if( $fromDate!="" || $toDate!="" ) $sql_cond .= " and in_date  between '".change_date_format($fromDate,'mm-dd-yyyy','-',1)."' and '".change_date_format($toDate,'mm-dd-yyyy','-',1)."'";
		}
	if($db_type==0)
		{
		if( $fromDate!="" || $toDate!="" ) $sql_cond .= " and in_date  between '".change_date_format($fromDate,'yyyy-mm-dd')."' and '".change_date_format($toDate,'yyyy-mm-dd')."'";
		}*/
	if (str_replace("'", "", $company) != 0) $com_cond = " and b.company_id=" . str_replace("'", "", $company) . " "; else $com_cond = "";

	if (str_replace("'", "", $system_no) != "") $sys_cond = "and b.system_no =" . str_replace("'", "", $system_no) . "  "; else  $sys_cond = "";
	if (str_replace("'", "", $batch_no) != '') $batch_no_cond = "and b.batch_no='$batch_no'  "; else  $batch_no_cond = "";
	if (str_replace("'", "", $load_unload) != '') $load_unload_cond = "and b.load_unload_id in($load_unload)  "; else  $load_unload_cond = "";

$supplier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
$company_name_arr = return_library_array("select id, company_name from  lib_company", 'id', 'company_name');

	$sql = "select a.entry_form,a.batch_for,a.batch_against,a.extention_no,b.service_source,b.service_company,b.id,b.batch_no,b.company_id,b.load_unload_id,b.batch_id,b.system_no,b.process_end_date
			from  pro_batch_create_mst a,pro_fab_subprocess b where a.id=b.batch_id and b.entry_form=35 and b.status_active=1 and b.is_deleted=0 $com_cond $sys_cond $batch_no_cond  $load_unload_cond order by b.id desc";
	//echo $sql;
	$sql2=sql_select($sql);
	foreach ($sql2 as $row) {
		if($row[csf('service_source')]==1)
		{
			$batch_comp_supp_arr[$row[csf('batch_id')]]= $company_name_arr[$row[csf('service_company')]];
		}
		else
		{
			$batch_comp_supp_arr[$row[csf('batch_id')]]= $supplier_arr[$row[csf('service_company')]];
		}
	}
	
	
	$arr = array(0 => $company_name_arr, 5 => $batch_for, 6 => $batch_against, 7 => $batch_comp_supp_arr,);
	echo create_list_view("list_view", "Company,Functional Batch No,Batch No,Process Start/Prod Date,Extention No,Batch For,Batch Against,Service Company", "150,120,150,100,80,100,80,100", "1000", "260", 0, $sql, "js_set_value", "id,system_no,batch_id,batch_no,load_unload_id,entry_form", "", 1, "company_id,0,0,0,0,batch_for,batch_against,batch_id", $arr, "company_id,system_no,batch_no,process_end_date,extention_no,batch_for,batch_against,batch_id", "", '', '0,0,0,3,0,0,0,0,0');
	exit();

}
//report========================================================================================================
if ($action == "dyeing_pro_print") {
	extract($_REQUEST);
	$data = explode('*', $data);
	//print_r ($data[1]);
	$country_arr = return_library_array("select id, country_name from lib_country", "id", "country_name");
	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$color_library = return_library_array("select id, color_name from lib_color", "id", "color_name");
	$section_library = return_library_array("select id,section_name from   lib_section", "id", "section_name");
	$deparntment_library = return_library_array("select id,department_name from   lib_department", "id", "department_name");
	$sample_library = return_library_array("select id,sample_name from   lib_sample", "id", "sample_name");
	$machine_lib_arr = return_library_array("select id, machine_no from lib_machine_name where is_deleted=0", "id", "machine_no");
	$brand_name_arr = return_library_array("select id, brand_name from   lib_brand", 'id', 'brand_name');
	$image_location = return_field_value("image_location", "common_photo_library", "file_type=1 and form_name='company_details' and master_tble_id='$data[0]'", "image_location");
	//$address=return_field_value("address","lib_location","id=$data[3]");
	//print_r($machine_lib_arr);
	if ($db_type == 0) $select_list = "group_concat(distinct(a.batch_id)) as batch_id,group_concat(distinct(a.batch_no)) as batch_no,group_concat(distinct(a.machine_id)) as machine_id,group_concat(distinct(a.fabric_type)) as fabric_type,group_concat(distinct(b.gsm)) as gsm,group_concat(distinct(b.const_composition)) as const_composition";

	else if ($db_type == 2) $select_list = " listagg(a.batch_id,',') within group (order by a.batch_id) as batch_id, listagg(a.batch_no,',') within group (order by a.batch_no) as batch_no,listagg(a.machine_id,',') within group (order by a.machine_id) as machine_id,listagg(a.fabric_type,',') within group (order by a.fabric_type) as fabric_type,listagg(b.gsm,',') within group (order by b.gsm) as gsm,listagg(b.const_composition,',') within group (order by b.const_composition) as const_composition";

	$qry_pro_fab_subpr = "select a.system_no,$select_list,sum(b.batch_qty) as batch_qty from pro_fab_subprocess a,pro_fab_subprocess_dtls b where a.entry_form=35 and a.id=b.mst_id and b.entry_page=35 and a.company_id=$data[0] and a.is_deleted=0 and a.status_active=1 and a.load_unload_id=$data[2] and b.status_active=1 and b.is_deleted=0 and a.system_no=$data[1] group by a.system_no";
	//echo $qry_pro_fab_subpr;
	$dataArr1 = sql_select($qry_pro_fab_subpr);
	//print_r($dataArr1);
	$qry_rslt = "select a.job_no,a.buyer_name,a.style_ref_no,b.po_number,c.mst_id as batch_id,d.color_id from wo_po_details_master a,wo_po_break_down b,pro_batch_create_dtls c,pro_batch_create_mst d where c.mst_id=d.id and a.job_no=b.job_no_mst and b.id=c.po_id and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
	$dataArr2 = sql_select($qry_rslt);
	$po_batch_data = array();
	foreach ($dataArr2 as $row) {
		$po_batch_data[$row[csf('batch_id')]]['style'] = $row[csf('style_ref_no')];
		$po_batch_data[$row[csf('batch_id')]]['buyer'] = $row[csf('buyer_name')];
		$po_batch_data[$row[csf('batch_id')]]['po'] = $row[csf('po_number')];
		$po_batch_data[$row[csf('batch_id')]]['color'] = $row[csf('color_id')];
		//$po_batch_data[$row[csf('batch_id')]]['machine']=$row[csf('machine_id')];
	}
	//for getting Lot and Brand
	//$yarn_lot_arr=array();
	//...............................................................................

	$fabricData = sql_select("select variable_list,fabric_roll_level from variable_settings_production where company_name ='$company_id' and variable_list in(3) and item_category_id=13 and is_deleted=0 and status_active=1");
	foreach ($fabricData as $row) {
		if ($row[csf('variable_list')] == 3) {
			$roll_maintained_id = $row[csf('fabric_roll_level')];
		}

	}
	$yarn_lot_arr = array();

	if ($db_type == 0) {
		if ($roll_maintained_id == 1) {
			$yarn_lot_data = sql_select("select  group_concat(d.yarn_lot) as yarn_lot, group_concat(d.yarn_count) as yarn_count, group_concat(d.brand_id) as brand_id
		from pro_batch_create_mst a,pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d,  inv_receive_master e
		where a.id=b.mst_id and b.roll_id=c.id and c.dtls_id=d.id and d.mst_id=e.id and a.company_id=$data[0] and a.id in(" . $dataArr1[0][csf('batch_id')] . ") and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and e.entry_form in(2,22)");
		} else {
			$yarn_lot_data = sql_select("select group_concat(d.yarn_lot) as yarn_lot, group_concat(d.yarn_count) as yarn_count, group_concat(d.brand_id) as brand_id
		from pro_batch_create_mst a,pro_batch_create_dtls b, pro_grey_prod_entry_dtls d,  inv_receive_master e
		where a.id=b.mst_id and b.prod_id=d.prod_id and d.mst_id=e.id and a.company_id=$data[0] and a.id in(" . $dataArr1[0][csf('batch_id')] . ") and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and e.entry_form in(2,22)");
		}
	} else if ($db_type == 2) {

		if ($roll_maintained_id == 1) {
			$yarn_lot_data = sql_select("select  LISTAGG(CAST(d.yarn_lot AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY d.yarn_lot) as yarn_lot, LISTAGG(CAST(d.yarn_count AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY d.yarn_count) as yarn_count,LISTAGG(CAST(d.brand_id AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY d.brand_id) as brand_id
		from pro_batch_create_mst a,pro_batch_create_dtls b, pro_roll_details c, pro_grey_prod_entry_dtls d,  inv_receive_master e
		where a.id=b.mst_id and b.roll_id=c.id and c.dtls_id=d.id and d.mst_id=e.id and a.company_id=$data[0] and a.id in(" . $dataArr1[0][csf('batch_id')] . ") and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and e.entry_form in(2,22)");
		} else {

			$yarn_lot_data = sql_select("select  LISTAGG(CAST(d.yarn_lot AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY d.yarn_lot) as yarn_lot, LISTAGG(CAST(d.yarn_count AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY d.yarn_count) as yarn_count,LISTAGG(CAST(d.brand_id AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY d.brand_id) as brand_id
		from pro_batch_create_mst a,pro_batch_create_dtls b, pro_grey_prod_entry_dtls d,  inv_receive_master e
		where a.id=b.mst_id  and d.mst_id=e.id and b.prod_id=d.prod_id and a.company_id=$data[0] and a.id in(" . $dataArr1[0][csf('batch_id')] . ") and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and e.entry_form in(2,22)");
		}
	}
	foreach ($yarn_lot_data as $rows) {
		//echo $rows[csf('brand_id')];
		//$yarn_lot=implode(',',array_unique(explode(',',$dataArr1[0][csf('gsm')])));
		$yarn_lot = implode(',', array_unique(explode(",", $rows[csf('yarn_lot')])));
		//$brand_id=implode(',',array_unique(explode(",",$rows[csf('brand_id')])));
		$yarn_count = explode(",", $rows[csf('yarn_count')]);

	}
	//................................................................................................................
	//print_r($po_batch_brand);
	$batch_ids = array_unique(explode(',', $dataArr1[0][csf('batch_id')]));
	$gsm_ids = array_unique(explode(',', $dataArr1[0][csf('gsm')]));
	$fabrics_ids = array_unique(explode(',', $dataArr1[0][csf('fabric_type')]));
	$machine_ids = array_unique(explode(',', $dataArr1[0][csf('machine_id')]));
	$brand_ids = array_unique(explode(',', $yarn_lot_data[0][csf('brand_id')]));
	$style_ref = "";
	$fabric_type = "";
	$buyer_name = "";
	$po_nmbr = "";
	$colors = "";
	$machine_no = "";
	//$lot_yarn="";
	$brands = "";


	foreach ($machine_ids as $fid) {
		if ($machine_no == "") $machine_no = $machine_lib_arr[$fid]; else $machine_no .= "," . $machine_lib_arr[$fid];

	}
	//print_r($machine_ids);
	foreach ($fabrics_ids as $fid) {
		if ($fabric_type == "") $fabric_type = $fabric_type_for_dyeing[$fid]; else $fabric_type .= "," . $fabric_type_for_dyeing[$fid];
	}
	foreach ($brand_ids as $fid) {
		if ($brand_ids == "") $brands = $brand_name_arr[$fid]; else $brands .= "," . $brand_name_arr[$fid];
	}
	//print_r($brand_ids);
	foreach ($batch_ids as $bid) {
		if ($style_ref == "") $style_ref = $po_batch_data[$bid]['style']; else $style_ref .= "," . $po_batch_data[$bid]['style'];
		if ($po_nmbr == "") $po_nmbr = $po_batch_data[$bid]['po']; else $po_nmbr .= "," . $po_batch_data[$bid]['po'];
		if ($colors == "") $colors = $color_library[$po_batch_data[$bid]['color']]; else $colors .= "," . $color_library[$po_batch_data[$bid]['color']];
		if ($buyer_name == "") $buyer_name = $buyer_library[$po_batch_data[$bid]['buyer']]; else $buyer_name .= "," . $buyer_library[$po_batch_data[$bid]['buyer']];
		//if ($machine_no=="") $machine_no=$machine_lib_arr[$po_batch_data[$bid]['machine']]; else $machine_no.=",".$machine_lib_arr[$po_batch_data[$bid]['machine']];
		//if ($lot_yarn=="") $lot_yarn=$po_batch_brand[$bid]['lot']; else $lot_yarn.=",".$po_batch_brand[$bid]['lot'];
		//if ($brands=="") $brands=$po_batch_brand[$bid]['brand']; else $brands.=",".$po_batch_brand[$bid]['brand'];
		//if ($brands=="") $brands=$brand_name_arr[$po_batch_brand[$bid]['brand']]; else $brands.=",".$brand_name_arr[$po_batch_brand[$bid]['brand']];
	}

	?>
    <div style="width:800px;" align="center">
        <table width="780" cellspacing="0" align="center" border="0">
            <tr>
                <td align="center"><img src="../../<? echo $image_location; ?>" height="50" width="60"></td>
                <td colspan="2" align="center" style="font-size:xx-large">
                    <strong><? echo $company_library[$data[0]]; ?></strong></td>
            </tr>
            <tr style="margin-bottom:20px;" class="form_caption">
                <td align="center" colspan="3">
                    &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
					<?
					//echo "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]";die;
					$nameArrayy = sql_select("select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
					foreach ($nameArrayy as $result) {
						?>
						<? echo $result[csf('plot_no')]; ?>
						<? echo $result[csf('level_no')] ?>
						<? echo $result[csf('road_no')]; ?>
						<? echo $result[csf('block_no')]; ?>
						<? echo $result[csf('city')]; ?>
						<? echo $result[csf('zip_code')]; ?>
						<? echo $result[csf('province')]; ?>
						<? echo $country_arr[$result[csf('country_id')]]; ?><br>
						<? echo $result[csf('email')]; ?>
						<? echo $result[csf('website')];
					}
					?>
                </td>
            </tr>
            <tr>
                <td colspan="6" align="right"><strong>Submit Date: ............................. </strong></td>
                <br>
            </tr>
            <tr>
                <td width="100"><strong>Buyer's Name:</strong></td>
                <td width="175px"><? echo implode(',', array_unique(explode(',', $buyer_name))); ?></td>
                <td width="100"><strong>Style No:</strong></td>
                <td width="175px"><? echo implode(',', array_unique(explode(",", $style_ref))); ?></td>
            </tr>
            <tr>
                <td width="100"><strong>Order No:</strong></td>
                <td width="175px"><? echo implode(',', array_unique(explode(",", $po_nmbr))); ?></td>
            </tr>
            <tr>
                <td width="100"><strong>Composition:</strong></td>
                <td colspan="6">
                    <p><? echo implode(',', array_unique(explode(',', $dataArr1[0][csf('const_composition')]))); ?></p>
                </td>
            </tr>
            <tr>

                <!--<td width="100"><strong>Fabric Type:</strong></td><td width="175px"><? //echo $fabric_type;
				?></td>-->
                <td width="100"><strong>G.S.M:</strong></td>
                <td width="175px"><? echo implode(',', array_unique(explode(',', $dataArr1[0][csf('gsm')]))); ?></td>
                <td width="100"><strong>Color:</strong></td>
                <td width="175px"><? echo implode(',', array_unique(explode(',', $colors))); ?></td>
            </tr>
            <tr>
                <td><strong>Lot:</strong></td>
                <td width="175px"> <? echo $yarn_lot; ?> </td>
                <td width="100"><strong>Batch Qty:</strong></td>
                <td width="175px"><? echo $dataArr1[0][csf('batch_qty')]; ?> Kg</td>
                <!--<td><strong>Brand:</strong></td> <td width="175px"> <? //echo implode(',',array_unique(explode(',',$brands)));
				?> </td>-->
            </tr>
            <tr>
                <td><strong>Functional Batch:</strong></td>
                <td width="175px"> <? echo $data[1]; ?> </td>
                <td width="100"><strong>Batch No:</strong></td>
                <td width="175px"><? echo implode(',', array_unique(explode(',', $dataArr1[0][csf('batch_no')]))); ?></td>
                <!--<td><strong>M/C No:</strong></td> <td width="175px"> <? //echo implode(',',array_unique(explode(',',$machine_no)));
				?> </td>-->
            </tr>
        </table>
        <hr/>
    </div>


	<?
	exit();

}

if ($action == 'get_load_end_hour_minute'){
	$sql_result_arr=sql_select("select end_hours,end_minutes from pro_fab_subprocess where batch_id=$data and entry_form=35 and load_unload_id =1 and status_active=1 and is_deleted=0");
	foreach ($sql_result_arr as $row) {
		echo $row[csf('end_hours')].':'.$row[csf('end_minutes')];
	}
	exit();	
}


?>