<?
/*-------------------------------------------- Comments -----------------------
Purpose         :   This Report Will Create Knit Garments FSO Report.
Functionality   :
JS Functions    :
Created by      :   Abu Sayed
Creation date   :   13-12-2022
Updated by      :
Update date     :
QC Performed BY :
QC Date         :
Comments        :
*/
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action == "fso_popup")
{
	echo load_html_head_contents("FSO Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	//if($recieve_basis==1) $width=1045; else $width=1055;
	$width = 1055;
	?>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	<script>
		function js_set_value(id, booking_no)
		{
			$('#hidden_fso_id').val(id);
			$('#hidden_fso_no').val(booking_no);
			parent.emailwindow.hide();
		}
		function search_populate(str)
		{
			if (str == 1)
			{
				document.getElementById('search_by_td').innerHTML = "Booking No Search";
				$('#txt_search_common').val('');
			}
			else if (str == 2)
			{
				document.getElementById('search_by_td').innerHTML = "FSO No Search";
				$('#txt_search_common').val('');
			}
			else if (str == 3)
			{
				document.getElementById('search_by_td').innerHTML = "Style Ref No Search";
				$('#txt_search_common').val('');
			}

		}
	</script>
    </head>
    <body>
        <div align="center" style="width:600x;">
            <form name="searchwofrm" id="searchwofrm" autocomplete=off>
                <fieldset style="width:600px;">
                    <legend>Enter search words</legend>
                    <table cellpadding="0" cellspacing="0" border="1" rules="all" width="600" class="rpt_table">
                        <thead>
                            <th width="150">Within Group</th>
                            <th width="150">Search By</th>
                            <th id="search_by_td" width="140">Booking No Search</th>
                            <th>
                                <input type="reset" name="reset" id="reset" value="Reset" style="width:70px" class="formbutton"/>
                                <input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes" value="<?=$cbo_company_id; ?>">
                                <input type="hidden" name="hidden_fso_id" id="hidden_fso_id" class="text_boxes" value="">
                                <input type="hidden" name="hidden_fso_no" id="hidden_fso_no" class="text_boxes" value="">
                            </th>
                        </thead>
                        <tr class="general">
                            <td>
								<?
								echo create_drop_down("cbo_within_group", 150, $yes_no, "", 1, "--Select--", "", $dd, 0);
								?>
							</td>
                            <td>
								<?
								$search_by_arr = array(1 => "Booking No", 2 => "FSO No", 3 => "Style Ref No");
								$fnc_name = "search_populate(this.value)";
								echo create_drop_down("cbo_search_by", 150, $search_by_arr, "", 0, "--Select--", $selected, $fnc_name, 0);
								?>
							</td>
                            <td id="search_by_td">
								<input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common"/>
							</td>
                            <td><input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_company_id').value, 'create_fso_search_list_view', 'search_div', 'fso_report_controller', 'setFilterGrid(\'tbl_list_search\',-1);');" style="width:70px;"/>
                            </td>
                        </tr>
                    </table>

                </fieldset>
            </form>
            <div id="search_div" align="left"></div>
        </div>
    </body>
    </html>
    <?
    exit();
}

if ($action == "create_fso_search_list_view")
{
	$data = explode("_", $data);

	$within_group = $data[0];
	$search_by = $data[1];
	$search_string = "%" . trim($data[2]) . "%";
	$company_id = $data[3];
	//var_dump($data);

	$company_arr = return_library_array("select id,company_short_name from lib_company", 'id', 'company_short_name');
	$buyer_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');
	$location_arr = return_library_array("select id, location_name from lib_location", 'id', 'location_name');

	$search_field_cond = '';
	if ($search_string != "") {
		if ($search_by == 1) {
			$search_field_cond = " and sales_booking_no like '%" . $search_string . "'";
		} else if ($search_by == 2) {
			$search_field_cond = " and job_no like '%" . $search_string . "'";
		} else {
			$search_field_cond = " and style_ref_no like '" . $search_string . "%'";
		}
	}

	if ($within_group == 0) $within_group_cond = ""; else $within_group_cond = " and within_group=$within_group";

	if ($db_type == 0) $year_field = "YEAR(insert_date) as year";
	else if ($db_type == 2) $year_field = "to_char(insert_date,'YYYY') as year";
	else $year_field = "";//defined Later

	$sql = "select id, $year_field, job_no_prefix_num, job_no, within_group, sales_order_type, sales_booking_no, booking_date, buyer_id, style_ref_no, location_id from fabric_sales_order_mst where entry_form=109 and status_active=1 and is_deleted=0 and company_id=$company_id $within_group_cond $search_field_cond order by id desc";
	//echo $sql;//die;
	$result = sql_select($sql);

	?>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1000" class="rpt_table">
			<thead>
				<th width="30">SL</th>
				<th width="100">Buyer</th>
				<th width="100">Year</th>
				<th width="150">Booking No</th>
				<th width="100">Within Group</th>
				<th width="150">Sales Order No</th>
				<th width="100">Booking date</th>
				<th width="150">Style Ref.</th>
				<th width="">Location</th>
			</thead>
		</table>
		<div style="width:1000px; max-height:280px; overflow-y:scroll" id="list_container_batch" align="left">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="980" class="rpt_table"
			id="tbl_list_search">
			<?
			$i = 1;
			if(!empty($result))
			{
				foreach ($result as $row)
				{
					if ($row[csf('within_group')] == 1)
						$buyer = $company_arr[$row[csf('buyer_id')]];
					else
						$buyer = $buyer_arr[$row[csf('buyer_id')]];

					?>
					<tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none; cursor:pointer"
						onClick="js_set_value(<?=$row[csf('id')]; ?>,'<?=$row[csf('job_no')]; ?>');">
						<td width="30"><?=$i; ?></td>
						<td width="100" align="left" style="word-break:break-all"><? echo $buyer; ?>&nbsp;</td>
						<td width="100" align="center" style="word-break:break-all"><? echo $row[csf('year')]; ?>&nbsp;</td>
						<td width="150" align="center" style="word-break:break-all"><? echo $row[csf('sales_booking_no')]; ?>&nbsp;</td>
						<td width="100" align="center" style="word-break:break-all"><? echo $yes_no[$row[csf('within_group')]]; ?>&nbsp;</td>
						<td width="150" style="word-break:break-all"><? echo $row[csf('job_no')]; ?>&nbsp;</td>
						<td width="100" style="word-break:break-all"><? echo change_date_format($row[csf('booking_date')]); ?>&nbsp;</td>
						<td width="150" align="center" style="word-break:break-all"><? echo $row[csf('style_ref_no')]; ?>&nbsp;</td>
						<td width="" style="word-break:break-all"><? echo $location_arr[$row[csf('location_id')]]; ?>&nbsp;</td>
					</tr>
					<?
					$i++;
				}
			}

			?>
		</table>
	</div>
	<?
	exit();
}

if ($action=="report_generate")
{
    extract($_REQUEST);
    $cbo_company_id=str_replace("'","",$cbo_company_id);
    $cbo_booking_type=str_replace("'","",$cbo_fab_booking_type);
    $txt_date_from=str_replace("'","",$txt_date_from);
    $txt_date_to=str_replace("'","",$txt_date_to);
    $txt_fso_no_id=str_replace("'","",$txt_fso_no_id);
	$txt_fso_no=str_replace("'","",$txt_fso_no);
	$search_type=str_replace("'","",$search_type);
	// var_dump($cbo_booking_type);die;

    $companyArr = return_library_array("select id,company_name from lib_company ","id","company_name");
    $buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
	$colorArr = return_library_array( "select id, color_name from lib_color", "id", "color_name");
	$sample_library=return_library_array( "select id,sample_name from lib_sample", "id", "sample_name"  );
	$style_library=return_library_array( "select id,style_ref_no from sample_development_mst", "id", "style_ref_no"  );
	$pro_sub_dept_arr=return_library_array( "select id,sub_department_name from lib_pro_sub_deparatment where status_active =1 and is_deleted=0", "id", "sub_department_name"  );

    $fabric_booking_type = array(118 => "Main Fabric Booking",108=>'Partial Fabric Booking', 88 => "Short Fabric Booking", 89 => "Sample Fabric Booking - With Order", 0 => 'Sample Fabric Booking - Without Order');

	if($txt_fso_no_id!='')
	{
		if ($txt_fso_no_id=="") $fsoCond=""; else $fsoCond=" and a.id in ( $txt_fso_no_id )";
	}
	else
	{
		if ($txt_fso_no=="") $fsoCond=""; else $fsoCond=" and a.job_no like '%$txt_fso_no%'";
	}

	if($search_type ==1)
	{
		if($txt_date_from!="" && $txt_date_to!="") $fso_date_cond=" and trunc(a.insert_date) BETWEEN TO_DATE('$txt_date_from') AND TO_DATE('$txt_date_to')"; else $fso_date_cond="";
	}
	else
	{
		if($txt_date_from!="" && $txt_date_to!="") $booking_date_fsocond=" and a.booking_date between '$txt_date_from' and '$txt_date_to'"; else $booking_date_fsocond="";
	}

   //die;
	if($type==0)//show Button
	{
		if($cbo_company_id) $company_cond=" and company_id in($cbo_company_id)"; else $company_cond="";
		if($search_type ==2)
		{
			if($txt_date_from!="" && $txt_date_to!="") $booking_date_cond=" and booking_date between '$txt_date_from' and '$txt_date_to'"; else $booking_date_cond="";
		}


		$cbo_booking_type_arr=array_unique(array_filter(explode(",",$cbo_booking_type)));
		//var_dump($cbo_booking_type_arr);die;
		$allBookingId = array();
		foreach ($cbo_booking_type_arr as $cbo_booking_type)
		{

			if($cbo_booking_type == 1)  //Main Fabric Booking
			{
				$type_cond = " and booking_type=1 and is_short=2 and entry_form in (118,86)";

				$get_booking = "SELECT id, booking_no from  wo_booking_mst where is_deleted=0 and status_active=1  $company_cond $type_cond $booking_date_cond ";
				//echo $get_booking;
			}
			else if($cbo_booking_type == 2) //Partial Fabric Booking
			{
				$type_cond = " and booking_type=1 and is_short=2 and entry_form=108";
				$get_booking = "SELECT id, booking_no from  wo_booking_mst where is_deleted=0 and status_active=1  $company_cond $type_cond $booking_date_cond ";
				//echo $get_booking; //die;
			}
			else if($cbo_booking_type == 3) //Short Fabric Booking
			{
				$type_cond = " and booking_type=1 and is_short=1 and entry_form=88";
				$get_booking = "SELECT id, booking_no from  wo_booking_mst where is_deleted=0 and status_active=1  $company_cond $type_cond $booking_date_cond ";
				//echo $get_booking;// die;
			}
			else if($cbo_booking_type == 4) //Sample Fabric Booking - With Order
			{
				$type_cond = " and booking_type=4 and is_short=2 and entry_form=89";
				$get_booking = "SELECT id, booking_no from  wo_booking_mst where is_deleted=0 and status_active=1  $company_cond $type_cond $booking_date_cond ";
				//echo $get_booking; die;

			}
			else if($cbo_booking_type == 5) //Sample Fabric Booking - Without Order
			{
				$type_cond = " and booking_type=4 and (entry_form_id is null or entry_form_id =0)";
				$get_booking = "SELECT id, booking_no, booking_date from wo_non_ord_samp_booking_mst where is_deleted=0 and status_active=1  $company_cond $booking_date_cond $type_cond";
				//echo $get_booking; die;

			}
			$rslt_booking = sql_select($get_booking);
			foreach ($rslt_booking as $row)
			{
				$allBookingId[$row[csf('id')]]=$row[csf('id')];
			}

			$rsltsmn_booking = sql_select($get_smn_booking);
			foreach ($rsltget_booking as $val)
			{
				$allSmnBookingId[$val[csf('id')]]=$val[csf('id')];
			}

		}

		//var_dump($allBookingId);die;
		$allBookingId = array_filter($allBookingId);
		if(!empty($allBookingId))
		{
			$con = connect();
			execute_query("DELETE FROM TMP_BOOKING_ID WHERE USERID = ".$user_id." AND TYPE = 3");
			oci_commit($con);

			$con = connect();
			foreach($allBookingId as $bookingId)
			{
				execute_query("INSERT INTO TMP_BOOKING_ID(BOOKING_ID,USERID,TYPE) VALUES(".$bookingId.", ".$user_id.",3)");
				oci_commit($con);
			}
		}
		//die;

		/* echo '<pre>';
		print_r($approve_data_arr); die;*/
		if(!empty($cbo_booking_type_arr))
		{
			$sqlFsoMain = "SELECT a.id,a.job_no, a.job_no_prefix_num, a.company_id, a.within_group, a.booking_id, a.sales_booking_no, a.booking_date, a.customer_buyer, a.style_ref_no, a.season, a.insert_date as fso_date, a.fabric_composition, a.booking_without_order, a.buyer_id, b.fabric_desc, b.color_id, b.color_type_id, b.grey_qty as fso_qnty, b.cons_uom as booking_uom, b.grey_qnty_by_uom as booking_qnty, b.process_loss,b.order_uom as fso_uom, b.body_part_id, b.determination_id
			from fabric_sales_order_mst a, fabric_sales_order_dtls b,tmp_booking_id c where a.id=b.mst_id $fsoCond and a.company_id=$cbo_company_id $fso_date_cond $booking_date_fsocond and  a.booking_id=c.booking_id and c.userid=$user_id and c.type=3 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 order by a.job_no";
		}
		else
		{
			$sqlFsoMain = "SELECT a.id,a.job_no, a.job_no_prefix_num, a.company_id, a.within_group, a.booking_id, a.sales_booking_no, a.booking_date, a.customer_buyer, a.style_ref_no, a.season, a.insert_date as fso_date, a.fabric_composition, a.booking_without_order, a.buyer_id, b.fabric_desc, b.color_id, b.color_type_id, b.grey_qty as fso_qnty, b.cons_uom as booking_uom, b.grey_qnty_by_uom as booking_qnty, b.process_loss,b.order_uom as fso_uom, b.body_part_id, b.determination_id
			from fabric_sales_order_mst a, fabric_sales_order_dtls b where a.id=b.mst_id $fsoCond and a.company_id=$cbo_company_id $fso_date_cond $booking_date_fsocond and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 order by a.job_no";
		}

		//echo $sqlFsoMain;die;//and a.job_no in('UG-FSOE-22-00005','UG-FSOE-22-00002')

		$rsltFsoMain = sql_select($sqlFsoMain);

		$smnBookingIdChk = array();
		$bookingNoChk = array();
		$all_smnbooking_id_arr = array();
		$all_booking_id_arr = array();
		foreach ($rsltFsoMain as $row)
		{
			if($row[csf('booking_without_order')] == 1)
			{
				if($smnBookingIdChk[$row[csf('booking_id')]] == "")
				{
					$smnBookingIdChk[$row[csf('booking_id')]] = $row[csf('booking_id')];
					$all_smnbooking_id_arr[$row[csf("booking_id")]] = $row[csf("booking_id")];
				}
			}
			else
			{
				if($bookingIdChk[$row[csf('booking_id')]] == "")
				{
					$bookingIdChk[$row[csf('booking_id')]] = $row[csf('booking_id')];
					$all_booking_id_arr[$row[csf("booking_id")]] = $row[csf("booking_id")];
				}
			}

		}
		$all_smnbooking_id_arr = array_filter($all_smnbooking_id_arr);
		if(!empty($all_smnbooking_id_arr))
		{
			$con = connect();
			execute_query("DELETE FROM TMP_BOOKING_ID WHERE USERID = ".$user_id." AND TYPE=1");
			oci_commit($con);

			$con = connect();
			foreach($all_smnbooking_id_arr as $bookingId)
			{
				execute_query("INSERT INTO TMP_BOOKING_ID(BOOKING_ID,USERID,TYPE) VALUES(".$bookingId.", ".$user_id.",1)");
				oci_commit($con);
			}
		}
		//die;
		$all_booking_id_arr = array_filter($all_booking_id_arr);
		if(!empty($all_booking_id_arr))
		{
			$con = connect();
			execute_query("DELETE FROM TMP_BOOKING_ID WHERE USERID = ".$user_id." AND TYPE=2");
			oci_commit($con);

			$con = connect();
			foreach($all_booking_id_arr as $bookingId)
			{
				execute_query("INSERT INTO TMP_BOOKING_ID(BOOKING_ID,USERID,TYPE) VALUES(".$bookingId.", ".$user_id.",2)");
				oci_commit($con);
			}
		}
		//die;

		$revisesmnInfoArr = array();
		$revise_approved_smn = sql_select("SELECT a.id, a.booking_no, max(b.approved_no) as approved_no, max(b.approved_date) as approved_date from wo_non_ord_samp_booking_mst a, approval_history b, tmp_booking_id c where a.id=b.mst_id and  a.id=c.booking_id and c.userid=$user_id and c.type=1 and b.entry_form=9 group by a.id, a.booking_no");

		foreach ($revise_approved_smn as $row)
		{
			$revisesmnInfoArr[$row[csf('id')]]['approved_no'] = $row[csf('approved_no')];
			if($row[csf('approved_no')]>1)
			{
				$revisesmnInfoArr[$row[csf('id')]]['approved_date'] = $row[csf('approved_date')];
			}

		}
		$reviseInfoArr = array();


		$revise_approved = sql_select("SELECT a.id, a.booking_no, max(b.approved_no) as approved_no, max(b.approved_date) as approved_date from wo_booking_mst a, approval_history b, tmp_booking_id c where a.id=b.mst_id and a.id=c.booking_id and c.userid=$user_id and c.type=2 group by a.id, a.booking_no ");

		foreach ($revise_approved as $row)
		{
			$reviseInfoArr[$row[csf('id')]]['approved_no'] = $row[csf('approved_no')];
			if($row[csf('approved_no')]>1)
			{
				$reviseInfoArr[$row[csf('id')]]['approved_date'] = $row[csf('approved_date')];
			}

		}
		//var_dump($reviseInfoArr);


		$smnInfo= sql_select("SELECT a.id, a.booking_no, b.style_id, b.style_des, b.sample_type, b.lib_yarn_count_deter_id, b.color_type_id, b.body_part, b.fabric_color from  wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b, tmp_booking_id c where a.booking_no=b.booking_no and a.id=c.booking_id and c.userid=$user_id and c.type=1 and b.status_active=1 and b.is_deleted=0 and a.booking_type=4 and a.status_active=1 and a.is_deleted=0 and ( a.entry_form_id is null or a.entry_form_id =0 )");
		$smnInfoArr = array();
		foreach ($smnInfo as $row)
		{

			$group_key = $row[csf('body_part')].'*'.$row[csf('fabric_color')].'*'.$row[csf('color_type_id')].'*'.$row[csf('lib_yarn_count_deter_id')];
			$smnInfoArr[$row[csf('id')]][$group_key]['style_id'] = $row[csf('style_id')];
			$smnInfoArr[$row[csf('id')]][$group_key]['style_des'] = $row[csf('style_des')];
			$smnInfoArr[$row[csf('id')]][$group_key]['sample_type'] = $row[csf('sample_type')];
		}
		unset($smnInfo);
		// echo "<pre>";
		// print_r($smnInfoArr);
		// echo "</pre>";


		$smnbookingInfo= sql_select("SELECT a.id, a.buyer_id from  wo_non_ord_samp_booking_mst a, tmp_booking_id b where a.id=b.booking_id and b.userid=$user_id and b.type=1 and a.status_active=1 and a.is_deleted=0");
		$smnbookingInfoArr = array();
		foreach ($smnbookingInfo as $row)
		{
			$smnbookingInfoArr[$row[csf('id')]]['buyer_id'] = $row[csf('buyer_id')];
		}
		unset($smnbookingInfo);


		$jobInfo= sql_select("SELECT a.id, a.booking_no, b.style_ref_no, b.style_description, b.product_dept, b.pro_sub_dep from  wo_booking_mst a,wo_po_details_master b, tmp_booking_id c where a.job_no=b.job_no and a.id=c.booking_id and c.userid=$user_id and c.type=2 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0");
		$jobInfoArr = array();
		foreach ($jobInfo as $row)
		{
			$jobInfoArr[$row[csf('id')]]['style_ref_no'] = $row[csf('style_ref_no')];
			$jobInfoArr[$row[csf('id')]]['style_description'] = $row[csf('style_description')];
			$jobInfoArr[$row[csf('id')]]['product_dept'] = $row[csf('product_dept')];
			$jobInfoArr[$row[csf('id')]]['pro_sub_dep'] = $row[csf('pro_sub_dep')];
		}
		unset($jobInfo);

		$bookingInfo= sql_select("SELECT a.id, a.buyer_id from  wo_booking_mst a, tmp_booking_id b where a.id=b.booking_id and b.userid=$user_id and b.type=2 and a.status_active=1 and a.is_deleted=0");
		$bookingInfoArr = array();
		foreach ($bookingInfo as $row)
		{
			$bookingInfoArr[$row[csf('id')]]['buyer_id'] = $row[csf('buyer_id')];
		}
		unset($bookingInfo);



		$r_id333=execute_query("DELETE FROM TMP_BOOKING_ID WHERE USERID=$user_id AND TYPE = 3");
		if($r_id333)
		{
			oci_commit($con);
		}
		$r_id111=execute_query("DELETE FROM TMP_BOOKING_ID WHERE USERID=$user_id AND TYPE = 1");
		if($r_id111)
		{
			oci_commit($con);
		}
		$r_id222=execute_query("DELETE FROM TMP_BOOKING_ID WHERE USERID=$user_id AND TYPE = 2");
		if($r_id222)
		{
			oci_commit($con);
		}
		?>

		<?

		ob_start();
		$tblWidth = "2540";
		$divWidth = "2560";
		?>

		 <style type="text/css">
            table tr th, table tr td{word-wrap: break-word;word-break: break-all;}
        </style>
		 <div style="width:<?php echo $divWidth; ?>">
		 	<fieldset style="width:<? echo $divWidth; ?>;">
			 <table width="<? echo $tblWidth;?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" >
					<tr class="form_caption" style="border:none;">
						<td colspan="22" align="center" style="border:none;font-size:14px; font-weight:bold" ><? echo $report_title; ?></td>
					</tr>
					<tr style="border:none;">
						<td colspan="22" align="center" style="border:none; font-size:16px; font-weight:bold"><? echo $companyArr[$cbo_company_id]; ?></td>
					</tr>
					<tr style="border:none;">
						<td colspan="22" align="center" style="border:none;font-size:12px; font-weight:bold">
							<? if ($txt_date_from != "" || $txt_date_to != "") echo "From " . change_date_format($txt_date_from) . " To " . change_date_format($txt_date_to) . ""; ?>
						</td>
					</tr>
				</table>
				<table width="<? echo $tblWidth;?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" >
					<thead>
						<tr style="font-size:13px">
							<th width="30">SL.</th>
							<th width="150">LC Company</th>
							<th width="150">Buyer</th>
							<th width="150">Reference</th>
							<th width="110">F. BookingDate</th>
							<th width="100">Revised No</th>
							<th width="100">Revised Date</th>
							<th width="100">Within Group</th>
							<th width="150">FSO No</th>
							<th width="100">FSO Date</th>
							<th width="100">Season</th>
							<th width="100">Style No</th>
							<th width="100">Style Description</th>
							<th width="100">Prod. Dept</th>
							<th width="100">Prod. Sub Dept</th>
							<th width="100">Sample Type</th>
							<th width="100">Color</th>
							<th width="100">Color Type</th>
							<th width="150">Composition</th>
							<th width="100">Booking UOM</th>
							<th width="100">Booking QTY</th>
							<th width="100">Process Loss %</th>
							<th width="60">FSO UOM</th>
							<th width="90">FSO Qty</th>
						</tr>
					</thead>
				</table>
				<div style="width:<?php echo $divWidth; ?>px; overflow-y:scroll; max-height:350px" id="scroll_body" >
				<table width="<?php echo $tblWidth; ?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">

						<? $i=1;

						$buyer =  $style_ref = $style_des = $sample_type = $product_dept_name = $pro_sub_dept_name ='';
						foreach ($rsltFsoMain as $row)
						{

							if ($row[csf('within_group')] == 1)
							{
								if($row[csf('booking_without_order')] == 1)
								{
									$group_key = $row[csf('body_part_id')].'*'.$row[csf('color_id')].'*'.$row[csf('color_type_id')].'*'.$row[csf('determination_id')];

									$buyer = $smnbookingInfoArr[$row[csf('booking_id')]]['buyer_id'];
									$style_ref = $style_library[$smnInfoArr[$row[csf('booking_id')]][$group_key]['style_id']];
									$style_des = $smnInfoArr[$row[csf('booking_id')]][$group_key]['style_des'];
									$sample_type = $sample_library[$smnInfoArr[$row[csf('booking_id')]][$group_key]['sample_type']];
								}
								else
								{
									$buyer = $bookingInfoArr[$row[csf('booking_id')]]['buyer_id'];
									$style_ref = $jobInfoArr[$row[csf('booking_id')]]['style_ref_no'];
									$style_des = $jobInfoArr[$row[csf('booking_id')]]['style_description'];
									$product_dept_name = $product_dept[$jobInfoArr[$row[csf('booking_id')]]['product_dept']];
									$pro_sub_dept_name = $pro_sub_dept_arr[$jobInfoArr[$row[csf('booking_id')]]['pro_sub_dep']];



								}
							}
							else
							{
								$buyer = $row[csf('buyer_id')];
								$style_ref = $row[csf('style_ref_no')];
							}

							?>
							<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
								<td width="30"><? echo $i; ?></td>
								<td width="150"><? echo $companyArr[$row[csf('company_id')]]; ?></td>
								<td width="150"><? echo $buyerArr[$buyer]; ?></td>
								<td width="150"><? echo $row[csf('sales_booking_no')]; ?></td>
								<td width="110"><? if($row[csf('booking_date')]) echo  date("d/m/Y", strtotime($row[csf('booking_date')]));?>&nbsp;</td>
								<td width="100">
									<?
									if($row[csf('booking_without_order')] == 1)
									{
										$revisesmn_approved_no = $revisesmnInfoArr[$row[csf('booking_id')]]['approved_no'];
										if($revisesmn_approved_no>1)
										{
											echo $revisesmn_approved_no-1;
										}
									}
									else
									{
										$revise_approved_no = $reviseInfoArr[$row[csf('booking_id')]]['approved_no'];
										if($revise_approved_no>1)
										{
											echo $revise_approved_no-1;
										}
									}

									?>
								</td>
								<td width="100">
									<?

									if($row[csf('booking_without_order')] == 1)
									{
										$smnapproved_date = $revisesmnInfoArr[$row[csf('booking_id')]]['approved_date'];
										if($smnapproved_date)
										{
											echo date("d/m/Y", strtotime($smnapproved_date));
											//echo  change_date_format($smnapproved_date);
										}
									}
									else
									{
										$approved_date = $reviseInfoArr[$row[csf('booking_id')]]['approved_date'];
										if($approved_date)
										{
											echo date("d/m/Y", strtotime($approved_date));
											//echo change_date_format($approved_date);
										}
									}
									?> &nbsp;
								</td>
								<td width="100"><? echo $yes_no[$row[csf('within_group')]]; ?></td>
								<td width="150"><? echo $row[csf('job_no')]; ?></td>
								<td width="100"><? if($row[csf('fso_date')]) echo  date("d/m/Y", strtotime($row[csf('fso_date')]));?>&nbsp;</td>
								<td width="100"><? if($row[csf('season')] > 0 || $row[csf('season')] !='Select Season') echo $row[csf('season')]; ?></td>
								<td width="100" title="<? echo $group_key;?>"><? echo $style_ref;?></td>
								<td width="100"><? echo $style_des;?></td>
								<td width="100"><? echo $product_dept_name;?></td>
								<td width="100"><? echo $pro_sub_dept_name;?></td>
								<td width="100"><? echo $sample_type;?></td>
								<td width="100"><? echo $colorArr[$row[csf('color_id')]]; ?></td>
								<td width="100"><? echo $color_type[$row[csf('color_type_id')]]; ?></td>
								<td width="150"><? echo $row[csf('fabric_desc')]; ?></td>
								<td width="100"><? echo $unit_of_measurement[$row[csf('booking_uom')]]; ?></td>
								<td width="100" align="right"><? echo number_format($row[csf('booking_qnty')],2, '.', ''); ?></td>
								<td width="100"><?
									if($row[csf('process_loss')])
									{
										echo $row[csf('process_loss')];
									}
									else
									{
										echo "0";
									}

									?>
								</td>
								<td width="60"><? echo $unit_of_measurement[$row[csf('fso_uom')]]; ?></td>
								<td width="90" align="right"><? echo number_format($row[csf('fso_qnty')],2, '.', ''); ?></td>
							</tr>

						<? $i++;
						} ?>

				</table>
				</div>
			</fieldset>
		</div>
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
	echo "$total_data####$filename";

	exit();
}