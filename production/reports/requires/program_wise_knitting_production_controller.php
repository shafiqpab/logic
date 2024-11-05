<?php 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" )
	header("location:login.php");
require_once('../../../includes/common.php');
$user_id=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );

if ($action == "load_drop_down_floor") {
	echo create_drop_down("cbo_floor_id", 130, "select a.id, a.floor_name from lib_prod_floor a, lib_machine_name b where a.id=b.floor_id and b.category_id=1 and b.company_id=$data and b.status_active=1 and b.is_deleted=0 and a.production_process=2 $location_cond group by a.id, a.floor_name order by a.floor_name", "id,floor_name", 1, "-- Select Floor --", 0, "", "");
	exit();
}

if($action=="sales_order_no_search_popup")
{
	echo load_html_head_contents("Sales Order No Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(job_no)
		{
			document.getElementById('hidden_job_no').value=job_no;
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
								<input type="hidden" name="hidden_yearID" id="hidden_yearID" value="<? echo $yearID; ?>">
							</th> 
						</thead>
						<tr class="general">
							<td align="center">	
								<?
								echo create_drop_down( "cbo_within_group", 150, $yes_no,"",1, "--Select--", $cbo_within_group,$dd,0 );
								?>
							</td> 
							<td align="center">	
								<?
								$serach_type_arr=array(1=>'Sales Order No',2=>'Fab. Booking No');
								echo create_drop_down( "cbo_serach_type", 150, $serach_type_arr,"",0, "--Select--","","",0 );
								?>
							</td>           
							<td align="center">				
								<input type="text" style="width:140px" class="text_boxes"  name="txt_search_common" id="txt_search_common" placeholder="Write" />	
							</td> 						
							<td align="center">
								<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('hidden_yearID').value+'_'+document.getElementById('cbo_serach_type').value, 'create_sales_order_no_search_list', 'search_div', 'program_wise_knitting_production_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
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

if($action=="create_sales_order_no_search_list")
{
	$data 			= explode('_',$data);
	$sales_order_no = trim($data[0]);
	$within_group 	= $data[1];
	$yearID 		=  $data[2];
	$serach_type 	=  $data[3];
	//echo $serach_type.'==';
	$location_arr 	= return_library_array("select id, location_name from lib_location",'id','location_name');

	if($db_type==0)
	{
		if($yearID!=0) $year_cond=" and YEAR(a.insert_date)=$yearID"; else $year_cond="";
	}
	else if($db_type==2)
	{
		if($yearID!=0) $year_cond=" and to_char(a.insert_date,'YYYY')=$yearID";  else $year_cond="";
	}

	$within_group_cond  = ($within_group == 0)?"":" and a.within_group=$within_group";
	if($serach_type==1)
	{
		$sales_order_cond   = ($sales_order_no == "")?"":" and a.job_no like '%$sales_order_no%'";
	}
	else if($serach_type==2)
	{
		$sales_order_cond   = ($sales_order_no == "")?"":" and a.sales_booking_no like '%$sales_order_no%'";
	}
	$year_field 		= ($db_type == 2)? "to_char(a.insert_date,'YYYY') as year":"YEAR(a.insert_date) as year";

	$sql = "select a.id, $year_field, a.job_no_prefix_num, a.job_no, a.within_group, a.sales_booking_no,a.booking_date, a.buyer_id, a.style_ref_no, a.location_id from fabric_sales_order_mst a where a.status_active=1 and a.is_deleted=0 $within_group_cond $search_field_cond $sales_order_cond $year_cond order by a.id";
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
			$i=1;
			foreach ($result as $row)
			{  
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

				if($row[csf('within_group')]==1){
					$buyer=$company_arr[$row[csf('buyer_id')]]; 
				}else{
					$buyer=$buyer_arr[$row[csf('buyer_id')]];
				}
				$sales_order_no = $row[csf('job_no')];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $sales_order_no; ?>');">
					<td width="40" align="center"><? echo $i; ?></td>
					<td width="90" align="center"><p>&nbsp;<? echo $row[csf('job_no_prefix_num')]; ?></p></td>
					<td width="110" align="center"><p>&nbsp;<? echo $row[csf('job_no')]; ?></p></td>
					<td width="120" align="center"><p><? echo $row[csf('sales_booking_no')]; ?></p></td>
					<td width="80" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
					<td width="60" align="center"><p><? echo $row[csf('year')]; ?></p></td>
					<td width="80" align="center"><? echo $yes_no[$row[csf('within_group')]]; ?></td>
					<td width="70" align="center" style="word-break: break-all; "><? echo $buyer; ?></td>					
					<td width="110" align="center"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
					<td><p><? echo $location_arr[$row[csf('location_id')]]; ?></p></td>
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

$tmplte=explode("**",$data);
if ($tmplte[0]=="viewtemplate")
	$template=$tmplte[1];
else
	$template=$lib_report_template_array[$_SESSION['menu_id']]['0'];
if ($template=="")
	$template=1;

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	$cbo_type=str_replace("'","",$cbo_type);
	$report_type=str_replace("'","",$report_type);
	$cbo_floor_id=str_replace("'","",$cbo_floor_id);
	
	//for year
	$cbo_year=str_replace("'","",$cbo_year);
	if($db_type==0)
	{
		if($cbo_year !=0 )
		{
			$job_year_cond = " AND YEAR(e.insert_date) = ".$cbo_year."";
			$job_year_cond2 = " AND YEAR(f.insert_date) = ".$cbo_year."";
		}
		else
		{
			$job_year_cond = '';
			$job_year_cond2 = '';
		}
	}
	else if($db_type==2)
	{
		if($cbo_year!=0)
		{
			$job_year_cond = " AND TO_CHAR(e.insert_date,'YYYY') = ".$cbo_year."";
			$job_year_cond2 = " AND TO_CHAR(f.insert_date,'YYYY') = ".$cbo_year."";
		}
		else
		{
			$job_year_cond = '';
			$job_year_cond2 = '';
		}
	}
	
	//within group
	$cbo_within_group = str_replace("'","",$cbo_within_group);
	$within_group_cond = ($cbo_within_group != 0)?" AND d.within_group = ".$cbo_within_group."" : '';
	
	//for sales order
	$sales_order_no = str_replace("'","",$txt_sales_order);
	$sales_order_cond = ($sales_order_no != '')?" AND d.job_no LIKE '%$sales_order_no%' " : '';
	$sales_order_cond_subcon = ($sales_order_no != '')?" AND d.job_no_mst LIKE '%$sales_order_no%' " : '';
	
	//for booking type
	$cbo_booking_type = str_replace("'","",$cbo_booking_type);
	if($cbo_booking_type > 0)
	{
		if($cbo_booking_type == 89 || $cbo_booking_type == 90)
		{
			$entry_form_cond = " AND e.booking_type = 4 ";
			$entry_form_cond2 = " AND f.booking_type = 4 ";
		}
		else
		{
			$entry_form_cond = " AND e.entry_form = ".$cbo_booking_type."";
			$entry_form_cond2 = " AND f.entry_form_id = ".$cbo_booking_type."";
		}
		
		$entry_form_cond3 = " AND a.id = 0";
	}
	else
	{
		$entry_form_cond = "";
		$entry_form_cond2 = "";
		$entry_form_cond3 = "";
	}

	//for knitting source
	$cbo_knitting_source = str_replace("'","",$cbo_knitting_source);
	if($cbo_knitting_source == 0)
	{
		$knitting_source_cond = '';
	}
	else
	{
		$knitting_source_cond = " AND a.knitting_source = ".$cbo_knitting_source."";
	}

	if ($cbo_floor_id == 0) $floor_id_cond = '';
	else $floor_id_cond = " and b.floor_id=$cbo_floor_id";

	//for production date
	$txt_date=str_replace("'","",$txt_date);
	$date_cond = '';
	$rcv_no_con = '';
	$date_con_subcon = '';
	if($sales_order_no == '')
	{
		$rcv_date = '';
		if($db_type==0)
		{
			if($txt_date != '')
			{
				$date_cond = " AND a.receive_date <= '".change_date_format(trim($txt_date), "yyyy-mm-dd", "-")."'";
				$date_con_subcon = " AND a.product_date <= '".change_date_format(trim($txt_date), "yyyy-mm-dd", "-")."'";
				$rcv_date = " AND a.receive_date = '".change_date_format(trim($txt_date), "yyyy-mm-dd", "-")."'";
			}
		}
		else
		{
			if($txt_date != '')
			{
				$date_cond = " AND a.receive_date <= '".change_date_format(trim($txt_date),'','',1)."'";
				$date_con_subcon = " AND a.product_date <= '".change_date_format(trim($txt_date),'','',1)."'";
				$rcv_date = " AND a.receive_date = '".change_date_format(trim($txt_date),'','',1)."'";
			}
		}
		
		//for booking no
		/*$sql = "SELECT a.recv_number AS RECV_NUMBER, a.recv_number_prefix_num AS RECV_NUMBER_PREFIX_NUM, a.booking_no AS BOOKING_NO FROM inv_receive_master a WHERE a.entry_form = 2 AND a.item_category = 13 AND a.receive_basis = 2 AND a.status_active = 1 AND a.is_deleted = 0 AND a.company_id = ".$cbo_company_name." ".$rcv_date."";
		$sqlRslt = sql_select($sql);
		$rcvNoArr = array();
		foreach($sqlRslt as $row)
		{
			$rcvNoArr[$row['RECV_NUMBER']] = $row['RECV_NUMBER'];
		}*/
		
		$sql = "SELECT d.sales_booking_no AS SALES_BOOKING_NO
			FROM inv_receive_master a
				INNER JOIN pro_grey_prod_entry_dtls b ON a.id = b.mst_id
				INNER JOIN order_wise_pro_details c ON b.id = c.dtls_id
				INNER JOIN fabric_sales_order_mst d ON c.po_breakdown_id = d.id
				LEFT JOIN wo_booking_mst e ON d.sales_booking_no = e.booking_no AND e.status_active = 1
			AND e.is_deleted = 0
			WHERE a.entry_form = 2 
				AND a.item_category = 13
				AND a.receive_basis = 2
				AND a.status_active = 1
				AND a.is_deleted = 0
				AND a.company_id = ".$cbo_company_name."
				AND b.status_active = 1
				AND b.is_deleted = 0
				AND c.entry_form = 2
				AND c.trans_type = 1
				AND c.status_active = 1
				AND c.is_deleted = 0
				".$rcv_date."
				".$sales_order_cond."
				".$job_year_cond."
				".$knitting_source_cond."
				".$entry_form_cond."
				".$within_group_cond."
			UNION ALL (
			SELECT d.sales_booking_no AS SALES_BOOKING_NO
			FROM inv_receive_master a
				INNER JOIN pro_grey_prod_entry_dtls b ON a.id = b.mst_id
				INNER JOIN order_wise_pro_details c ON b.id = c.dtls_id
				INNER JOIN fabric_sales_order_mst d ON c.po_breakdown_id = d.id
				INNER JOIN wo_non_ord_samp_booking_mst f ON d.sales_booking_no = f.booking_no
			WHERE a.entry_form = 2 
				AND a.item_category = 13
				AND a.receive_basis = 2
				AND a.status_active = 1
				AND a.is_deleted = 0
				AND a.company_id = ".$cbo_company_name."
				AND b.status_active = 1
				AND b.is_deleted = 0
				AND c.entry_form = 2
				AND c.trans_type = 1
				AND c.status_active = 1
				AND c.is_deleted = 0
				AND f.status_active = 1
				AND f.is_deleted = 0
				".$rcv_date."
				".$sales_order_cond."
				".$job_year_cond2."
				".$knitting_source_cond."
				".$entry_form_cond2."
				".$within_group_cond."
			)
		";
		//echo $sql; die;
		$sqlRslt = sql_select($sql);
		$rcvNoArr = array();
		foreach($sqlRslt as $row)
		{
			$rcvNoArr[$row['SALES_BOOKING_NO']] = $row['SALES_BOOKING_NO'];
		}
		//echo "<pre>";
		//print_r($rcvNoArr); die;
		
		if(empty($rcvNoArr))
		{
			echo get_empty_data_msg();
			die;
		}
		else
		{
			$rcv_no_con = where_con_using_array($rcvNoArr,1,'d.sales_booking_no');
		}
	}
	
	//main query
	$sqlInhouse = "
		SELECT a. id AS ID, a.company_id AS COMPANY_ID, a.receive_basis AS RECEIVE_BASIS, a.receive_date AS RECEIVE_DATE, a.booking_no AS PROGRAM_NO, a.knitting_source AS KNITTING_SOURCE, a.knitting_company AS KNITTING_COMPANY, a.remarks AS REMARKS,
		b.ID AS DTLS_ID, b.prod_id AS PROD_ID, b.febric_description_id AS FEBRIC_DESCRIPTION_ID, b.body_part_id AS BODY_PART_ID, b.gsm AS GSM, b.width AS WIDTH, b.yarn_lot AS YARN_LOT, b.yarn_count AS YARN_COUNT, b.stitch_length AS STITCH_LENGTH, b.brand_id AS BRAND_ID, b.machine_no_id AS MACHINE_NO_ID, b.color_id AS COLOR_ID, b.color_range_id AS COLOR_RANGE_ID, b.grey_receive_qnty AS GREY_RECEIVE_QNTY, b.reject_fabric_receive AS REJECT_QTY, b.yarn_prod_id AS YARN_PROD_ID, b.FLOOR_ID,
		c.po_breakdown_id AS PO_BREAKDOWN_ID, c.is_sales AS IS_SALES,
		d.job_no AS JOB_NO, d.sales_booking_no AS SALES_BOOKING_NO, d.within_group AS WITHIN_GROUP, d.buyer_id AS BUYER_ID, d.CUSTOMER_BUYER,
		NVL(e.booking_type, 1) AS BOOKING_TYPE, e.entry_form AS ENTRY_FORM, 1 AS IS_ORDER
		FROM inv_receive_master a
			INNER JOIN pro_grey_prod_entry_dtls b ON a.id = b.mst_id
			INNER JOIN order_wise_pro_details c ON b.id = c.dtls_id
			INNER JOIN fabric_sales_order_mst d ON c.po_breakdown_id = d.id
			LEFT JOIN wo_booking_mst e ON d.sales_booking_no = e.booking_no AND e.status_active = 1
			AND e.is_deleted = 0
		WHERE a.entry_form = 2 
			AND a.item_category = 13
			AND a.receive_basis = 2
			AND a.status_active = 1
			AND a.is_deleted = 0
			AND a.company_id = ".$cbo_company_name."
			AND b.status_active = 1
			AND b.is_deleted = 0
			AND c.entry_form = 2
			AND c.trans_type = 1
			AND c.status_active = 1
			AND c.is_deleted = 0
			".$date_cond."
			".$sales_order_cond."
			".$job_year_cond."
			".$knitting_source_cond."
			".$entry_form_cond."
			".$within_group_cond."
			".$rcv_no_con."
			".$floor_id_cond."
		UNION ALL (
		SELECT a. id AS ID, a.company_id AS COMPANY_ID, a.receive_basis AS RECEIVE_BASIS, a.receive_date AS RECEIVE_DATE, a.booking_no AS PROGRAM_NO, a.knitting_source AS KNITTING_SOURCE, a.knitting_company AS KNITTING_COMPANY, a.remarks AS REMARKS,
		b.ID AS DTLS_ID, b.prod_id AS PROD_ID, b.febric_description_id AS FEBRIC_DESCRIPTION_ID, b.body_part_id AS BODY_PART_ID, b.gsm AS GSM, b.width AS WIDTH, b.yarn_lot AS YARN_LOT, b.yarn_count AS YARN_COUNT, b.stitch_length AS STITCH_LENGTH, b.brand_id AS BRAND_ID, b.machine_no_id AS MACHINE_NO_ID, b.color_id AS COLOR_ID, b.color_range_id AS COLOR_RANGE_ID, b.grey_receive_qnty AS GREY_RECEIVE_QNTY, b.reject_fabric_receive AS REJECT_QTY, b.yarn_prod_id AS YARN_PROD_ID, b.FLOOR_ID,
		c.po_breakdown_id AS PO_BREAKDOWN_ID, c.is_sales AS IS_SALES,
		d.job_no AS JOB_NO, d.sales_booking_no AS SALES_BOOKING_NO, d.within_group AS WITHIN_GROUP, d.buyer_id AS BUYER_ID, d.CUSTOMER_BUYER,
		NVL(f.booking_type, 1) AS BOOKING_TYPE, f.entry_form_id AS ENTRY_FORM, 2 AS IS_ORDER
		FROM inv_receive_master a
			INNER JOIN pro_grey_prod_entry_dtls b ON a.id = b.mst_id
			INNER JOIN order_wise_pro_details c ON b.id = c.dtls_id
			INNER JOIN fabric_sales_order_mst d ON c.po_breakdown_id = d.id
			INNER JOIN wo_non_ord_samp_booking_mst f ON d.sales_booking_no = f.booking_no
		WHERE a.entry_form = 2 
			AND a.item_category = 13
			AND a.receive_basis = 2
			AND a.status_active = 1
			AND a.is_deleted = 0
			AND a.company_id = ".$cbo_company_name."
			AND b.status_active = 1
			AND b.is_deleted = 0
			AND c.entry_form = 2
			AND c.trans_type = 1
			AND c.status_active = 1
			AND c.is_deleted = 0
			AND f.status_active = 1
			AND f.is_deleted = 0
			".$date_cond."
			".$sales_order_cond."
			".$job_year_cond2."
			".$knitting_source_cond."
			".$entry_form_cond2."
			".$within_group_cond."
			".$rcv_no_con."
			".$floor_id_cond."
		)
		ORDER BY RECEIVE_DATE, PROGRAM_NO ASC
	";
	/*
	//don't delete
	UNION ALL (
		SELECT a. id AS ID, a.company_id AS COMPANY_ID, a.receive_basis AS RECEIVE_BASIS, a.receive_date AS RECEIVE_DATE, a.booking_no AS PROGRAM_NO, a.knitting_source AS KNITTING_SOURCE, a.knitting_company AS KNITTING_COMPANY, a.remarks AS REMARKS,
		b.ID AS DTLS_ID, b.prod_id AS PROD_ID, b.febric_description_id AS FEBRIC_DESCRIPTION_ID, b.body_part_id AS BODY_PART_ID, b.gsm AS GSM, b.width AS WIDTH, b.yarn_lot AS YARN_LOT, b.yarn_count AS YARN_COUNT, b.stitch_length AS STITCH_LENGTH, b.brand_id AS BRAND_ID, b.machine_no_id AS MACHINE_NO_ID, b.color_id AS COLOR_ID, b.color_range_id AS COLOR_RANGE_ID, b.grey_receive_qnty AS GREY_RECEIVE_QNTY, b.reject_fabric_receive AS REJECT_QTY, b.yarn_prod_id AS YARN_PROD_ID,
		c.po_breakdown_id AS PO_BREAKDOWN_ID, c.is_sales AS IS_SALES,
		d.job_no AS JOB_NO, d.sales_booking_no AS SALES_BOOKING_NO, d.within_group AS WITHIN_GROUP, d.buyer_id AS BUYER_ID,
		999 AS BOOKING_TYPE, null AS ENTRY_FORM, 1 AS IS_ORDER
		FROM inv_receive_master a
			INNER JOIN pro_grey_prod_entry_dtls b ON a.id = b.mst_id
			INNER JOIN order_wise_pro_details c ON b.id = c.dtls_id
			INNER JOIN fabric_sales_order_mst d ON c.po_breakdown_id = d.id
		WHERE a.entry_form = 2 
			AND a.item_category = 13
			AND a.receive_basis = 2
			AND a.status_active = 1
			AND a.is_deleted = 0
			AND a.company_id = ".$cbo_company_name."
			AND b.status_active = 1
			AND b.is_deleted = 0
			AND c.entry_form = 2
			AND c.trans_type = 1
			AND c.status_active = 1
			AND c.is_deleted = 0
			".$date_cond."
			".$sales_order_cond."
			".$knitting_source_cond."
			".$entry_form_cond3."
			".$within_group_cond."
		)
	*/
	
	//echo $sqlInhouse; die;
	$sqlInhouseRslt = sql_select($sqlInhouse);
	$knittingCompanyIdArr = array();
	$programNoArr = array();
	$salesBookingNoArr = array();
	$yarnProductIdArr = array();
	$febricDescriptionIdArr = array();
	$machineIdArr = array();
	$colorIdArr = array();
	$yarnBrandIdArr = array();
	$yarnCountArr = array();
	$jobNoArr = array();
	foreach($sqlInhouseRslt as $row)
	{
		//for supplier
		$knittingCompanyIdArr[$row['KNITTING_COMPANY']] = $row['KNITTING_COMPANY'];

		//for program no
		if($row['PROGRAM_NO']*1 != 0)
		{
			$programNoArr[$row['PROGRAM_NO']] = $row['PROGRAM_NO'];
		}
		
		//for job no
		$jobNoArr[$row['SALES_BOOKING_NO']] = $row['JOB_NO'];
		
		//for sales booking no
		$salesBookingNoArr[$row['SALES_BOOKING_NO']] = $row['SALES_BOOKING_NO'];
		
		//for yarn product id
		$exp_yarn_prod_id = explode(",", $row['YARN_PROD_ID']);
		foreach($exp_yarn_prod_id as $key=>$val)
		{
			if($val*1 != 0 )
			{
				$yarnProductIdArr[$val] = $val;
			}
		}
		
		//for composition and construction
		$febricDescriptionIdArr[$row['FEBRIC_DESCRIPTION_ID']] = $row['FEBRIC_DESCRIPTION_ID'];
		
		//for machine
		$machineIdArr[$row['MACHINE_NO_ID']] = $row['MACHINE_NO_ID'];
		
		//for color
		$expClr = explode(',',$row['COLOR_ID']);
		foreach($expClr as $clr)
		{
			if($clr*1 != 0)
			{
				$colorIdArr[$clr] = $clr;
			}
		}
		
		//for yarn brand
		$yarnBrandIdArr[$row['BRAND_ID']] = $row['BRAND_ID'];
		
		//for yarn count
		$yarnCountArr[$row['YARN_COUNT']] = $row['YARN_COUNT'];
	}
	//echo "<pre>";
	//print_r($yarnProductIdArr);
	
	
	
	if($cbo_booking_type == 0 && ($cbo_knitting_source == 0 || $cbo_knitting_source ==2))
	{
		$const_comp_arr=return_library_array( "select id, const_comp from lib_subcon_charge", "id", "const_comp");
		//ubcon_ord_mst e //and e.subcon_job=b.job_no and e.subcon_job=d.job_no_mst 
		$sqlInhouseSubcontract = "
			SELECT a.company_id AS COMPANY_ID, 999 AS RECEIVE_BASIS, a.product_date AS RECEIVE_DATE, null AS BOOKING_NO, 999 AS BOOKING_TYPE, 1 AS IS_ORDER, null AS ENTRY_FORM, a.remarks AS REMARKS, b.id AS DTLS_ID, b.cons_comp_id AS PROD_ID, 0 AS FEBRIC_DESCRIPTION_ID, 
			b.gsm AS GSM, b.dia_width AS WIDTH, b.yarn_lot AS YARN_LOT, b.yrn_count_id AS YARN_COUNT, b.stitch_len AS STITCH_LENGTH, b.brand AS BRAND_ID, b.machine_dia AS MACHINE_DIA, b.machine_gg AS MACHINE_GG, b.machine_id AS MACHINE_NO_ID, nvl(b.color_id,0) AS COLOR_ID, b.color_range AS COLOR_RANGE_ID, b.order_id AS PO_BREAKDOWN_ID, d.order_no AS ORDER_NOS, d.job_no_mst AS JOB_NO, null AS SALES_BOOKING_NO, b.reject_qnty AS REJECT_QTY, 0 AS IS_SALES, a.party_id AS UNIT_ID, 0 AS WITHIN_GROUP, 2 AS KNITTING_SOURCE, a.knitting_company AS KNITTING_COMPANY, a.party_id AS BUYER_ID 
			FROM subcon_production_mst a 
				INNER JOIN subcon_production_dtls b ON a.id = b.mst_id
				INNER JOIN lib_machine_name c ON b.machine_id = c.id
				INNER JOIN subcon_ord_dtls d ON b.job_no = d.job_no_mst AND b.order_id = d.id 
			WHERE a.product_type = 2 
				AND a.status_active = 1
				AND a.is_deleted = 0
				AND a.company_id = ".$cbo_company_name."
				AND d.status_active = 1
				AND d.is_deleted=0 
				".$date_con_subcon."
				".$sales_order_cond_subcon."
		";
		//echo $sqlInhouseSubcontract;
		
		/*
		".$date_con_sub."
			$job_no_cond
			$order_no_cond
			$job_year_sub_cond
			$sales_order_cond
			$booking_no_cond
			$within_group_cond
		*/
		 
		//echo $sql_inhouse_sub;die;//$sub_company_cond $subcompany_working_cond
		/*if($cbo_booking_type==0)
		{
		$nameArray_inhouse_subcon=sql_select( $sql_inhouse_sub);
		}*/
		$sqlInhouseSubcontractRslt=sql_select($sqlInhouseSubcontract);
	}

	//for supplier
	//$supplier_arr = return_library_array( "select id, short_name from lib_supplier WHERE 1=1 ".where_con_using_array($knittingCompanyIdArr, '0', 'id'), "id", "short_name");
	$supplier_arr = return_library_array( "select id, short_name from lib_supplier", "id", "short_name");
	//echo "<pre>";
	//print_r($supplier_arr);

	//for program information
	$programArr = array();
	$sqlProgram = "SELECT id AS ID, program_qnty AS PROGRAM_QNTY, color_range AS COLOR_RANGE, stitch_length AS STITCH_LENGTH, machine_dia AS MACHINE_DIA, machine_gg AS MACHINE_GG, remarks AS REMARKS FROM ppl_planning_info_entry_dtls WHERE status_active = 1 AND is_deleted = 0 ".where_con_using_array($programNoArr,0,'id');
	$sqlProgramRslt = sql_select($sqlProgram);
	foreach($sqlProgramRslt as $row)
	{
		$programArr[$row['ID']]['PROGRAM_QNTY'] = $row['PROGRAM_QNTY'];
		$programArr[$row['ID']]['COLOR_RANGE'] = $row['COLOR_RANGE'];
		$programArr[$row['ID']]['STITCH_LENGTH'] = $row['STITCH_LENGTH']; 
		$programArr[$row['ID']]['MACHINE_DIA'] = $row['MACHINE_DIA'];
		$programArr[$row['ID']]['MACHINE_GG'] = $row['MACHINE_GG']; 
		$programArr[$row['ID']]['REMARKS'] = $row['REMARKS']; 
	}	
	//echo "<pre>";
	//print_r($programArr);

	//for Yarn Issue
	
	$yarn_issue="SELECT a.KNIT_ID, b.CONS_QUANTITY from PPL_YARN_REQUISITION_ENTRY a, INV_TRANSACTION b
	where a.REQUISITION_NO=b.REQUISITION_NO and a.PROD_ID=b.PROD_ID and b.ITEM_CATEGORY=1 and b.TRANSACTION_TYPE=2 and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 ".where_con_using_array($programNoArr,0,'KNIT_ID');
	// echo $yarn_issue;
	$yarn_issue_rst = sql_select($yarn_issue);
	$yarn_issue_qty_arr = array();
	foreach($yarn_issue_rst as $row)
	{
		$yarn_issue_qty_arr[$row['KNIT_ID']] += $row['CONS_QUANTITY'];
	}	
	// echo "<pre>"; print_r($yarn_issue_qty_arr);
	
	//for buyer
	$sqlBuyer = "SELECT booking_no AS BOOKING_NO, buyer_id AS BUYER_ID FROM wo_booking_mst WHERE status_active=1 AND is_deleted=0 ".where_con_using_array($salesBookingNoArr,1,'booking_no')." UNION ALL SELECT booking_no AS BOOKING_NO, buyer_id AS BUYER_ID FROM wo_non_ord_samp_booking_mst WHERE status_active = 1 AND is_deleted = 0".where_con_using_array($salesBookingNoArr,1,'booking_no');
	//echo $sqlBuyer;
	$sqlBuyerRslt = sql_select($sqlBuyer);
	$buyerArr = array();
	foreach ($sqlBuyerRslt as $row)
	{
		$buyerArr[$row['BOOKING_NO']] = $buyer_arr[$row['BUYER_ID']];
	}
	//echo "<pre>";
	//print_r($bookingArr);
	
	//for yarn type
	$yarnTypeArr = return_library_array( "SELECT id, yarn_type FROM product_details_master WHERE 1=1 ".where_con_using_array($yarnProductIdArr, '0', 'id'), "id", "yarn_type");
	
	$sqlYarn = "SELECT id AS ID, lot AS LOT, brand AS BRAND, yarn_count_id AS YARN_COUNT_ID, yarn_type AS YARN_TYPE, yarn_comp_type1st AS YARN_COMP_TYPE1ST, yarn_comp_percent1st AS YARN_COMP_PERCENT1ST, yarn_comp_type2nd AS YARN_COMP_TYPE2ND, yarn_comp_percent2nd AS YARN_COMP_PERCENT2ND FROM product_details_master WHERE 1=1 ".where_con_using_array($yarnProductIdArr, '0', 'id')." group by id, lot, brand, supplier_id, yarn_count_id, yarn_type, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd";
	$sqlYarnRslt = sql_select($sqlYarn);
	$yarnCompositionArr = array();
	foreach($sqlYarnRslt as $row)
	{
		$composition_string = '';
		$composition_string = $composition[$row['YARN_COMP_TYPE1ST']]." ".$row['YARN_COMP_PERCENT1ST']."%";
		if ($row['YARN_COMP_TYPE2ND'] != 0)
		{
			$composition_string .=" ".$composition[$row['YARN_COMP_TYPE2ND']]." ".$row['YARN_COMP_PERCENT2ND'] . "%";
		}
		$yarnCompositionArr[$row['ID']] = $composition_string;
	}
	//echo "<pre>";
	//print_r($yarnCompositionArr);

	//for composition and construction		
	$composition_arr = array();
	$construction_arr = array();
	//$sqlFabric = "select a.id, a.construction, b.type_id as yarn_type,b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id ".where_con_using_array($febricDescriptionIdArr, '0', 'a.id');
	$sqlFabric = "select a.id, a.construction, b.type_id as yarn_type,b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	//echo $sqlFabric;
	$sqlFabricRslt=sql_select($sqlFabric);
	if(count($sqlFabricRslt)>0)
	{
		foreach( $sqlFabricRslt as $row )
		{
			if(array_key_exists($row[csf('id')], $composition_arr))
			{
				$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
			}
			else
			{
				$composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
			}

			$construction_arr[$row[csf('id')]]=$row[csf('construction')];
			$yarn_type_arr[$row[csf('id')]]=$yarn_type[$row[csf('yarn_type')]];
		}
	}
	//echo "<pre>";
	//print_r($construction_arr);
	
	//for machine
	$machineArr = array();
	$sqlMachine = "select id AS ID, machine_no AS MACHINE_NO, dia_width AS DIA_WIDTH, gauge AS GAUGE, brand AS BRAND FROM lib_machine_name WHERE category_id=1 AND status_active=1 AND is_deleted=0 AND machine_no IS NOT NULL ".where_con_using_array($machineIdArr, '0', 'id');
	$sqlMachineRslt = sql_select($sqlMachine);
	foreach($sqlMachineRslt as $row)
	{
		$machineArr[$row['ID']]['MACHINE_NO']=$row['MACHINE_NO'];
		$machineArr[$row['ID']]['DIA_WIDTH']=$row['DIA_WIDTH'];
		$machineArr[$row['ID']]['GAUGE']=$row['GAUGE'];
		$machineArr[$row['ID']]['BRAND']=$row['BRAND'];
	}
	//echo "<pre>";
	//print_r($machineArr);
	
	//for color
	//$color_details = return_library_array( "select id, color_name from lib_color where 1=1 ".where_con_using_array($colorIdArr, '0', 'id'), "id", "color_name");
	$color_details = return_library_array( "select id, color_name from lib_color", "id", "color_name");
	//echo "<pre>";
	//print_r($color_details);
	
	//for yarn brand
	//$brand_details=return_library_array( "select id, brand_name from lib_brand where 1=1 ".where_con_using_array($yarnBrandIdArr, '0', 'id'), "id", "brand_name"  );
	$brand_details=return_library_array( "select id, brand_name from lib_brand", "id", "brand_name"  );
	
	//for yarn count
	//$yarn_count_details = return_library_array( "select id, yarn_count from lib_yarn_count where 1=1 ".where_con_using_array($yarnCountArr, '0', 'id'), "id", "yarn_count"  );
	$yarn_count_details = return_library_array( "select id, yarn_count from lib_yarn_count", "id", "yarn_count"  );
	$floor_arr=return_library_array( "select id, floor_name from   lib_prod_floor", "id", "floor_name");
	$bookingTypeaArr = array('118'=>'Main','108'=>'Partial','88'=>'Short','89'=>'Sample With Order','90'=>'Sample Without Order');

	//for prog. wise balance button
	if($report_type == 1)
	{
		$dataArr = array();
		foreach($sqlInhouseRslt as $row)
		{
			//for knitting company
			$inhouseOutbound = 1;
			if($row['KNITTING_SOURCE'] == 1)
			{
				$knitting_company = $company_arr[$row['KNITTING_COMPANY']];
			}
			else if($row['KNITTING_SOURCE'] == 3)
			{
				$knitting_company = $supplier_arr[$row['KNITTING_COMPANY']];
				$inhouseOutbound = 2;
			}
			else
			{
				$knitting_company = "&nbsp;";
			}
			
			$dataArr[$inhouseOutbound][$row['PROGRAM_NO']]['KNITTING_COMPANY'] = $knitting_company;
			$dataArr[$inhouseOutbound][$row['PROGRAM_NO']]['RECEIVE_DATE'] = date('d-m-Y', strtotime($row['RECEIVE_DATE']));
			$dataArr[$inhouseOutbound][$row['PROGRAM_NO']]['MACHINE_NO_ID'] .= ($inhouseOutbound==1?$machineArr[$row['MACHINE_NO_ID']]['MACHINE_NO']:'').',';
			$dataArr[$inhouseOutbound][$row['PROGRAM_NO']]['BRAND_ID'] = ($inhouseOutbound==1?$machineArr[$row['MACHINE_NO_ID']]['BRAND']:'');
			$dataArr[$inhouseOutbound][$row['PROGRAM_NO']]['MACHINE_DIA_GG'] = $programArr[$row['PROGRAM_NO']]['MACHINE_DIA']." X ".$programArr[$row['PROGRAM_NO']]['MACHINE_GG'];

			//for body part
			if($row['BODY_PART_ID']*1 != 0)
			{
				$dataArr[$inhouseOutbound][$row['PROGRAM_NO']]['BODY_PART_ID'][$row['BODY_PART_ID']] = $body_part[$row['BODY_PART_ID']];
			}
			$dataArr[$inhouseOutbound][$row['PROGRAM_NO']]['JOB_NO'] = $row['JOB_NO'];
			$dataArr[$inhouseOutbound][$row['PROGRAM_NO']]['SALES_BOOKING_NO'] = $row['SALES_BOOKING_NO'];
			$dataArr[$inhouseOutbound][$row['PROGRAM_NO']]['WITHIN_GROUP'] = ($row['WITHIN_GROUP']==1?'Yes':'No');
			$dataArr[$inhouseOutbound][$row['PROGRAM_NO']]['UNIT_NAME'] = ($row['WITHIN_GROUP']==1?$company_arr[$row['BUYER_ID']]:$company_arr[$row['COMPANY_ID']]);
			$dataArr[$inhouseOutbound][$row['PROGRAM_NO']]['BUYER_NAME'] = ($row['WITHIN_GROUP']==1?$buyerArr[$row['SALES_BOOKING_NO']]:$buyer_arr[$row['BUYER_ID']]);
			
			//for booking type
			$bookingType = '';
			if($row['BOOKING_TYPE'] == 4)
			{
				if($row['IS_ORDER'] == 1)
				{
					$bookingType = 'Sample With Order';
				}
				else
				{
					$bookingType = 'Sample Without Order';
				}
			}
			else
			{
				$bookingType = $bookingTypeaArr[$row['ENTRY_FORM']];
			}			
			
			$dataArr[$inhouseOutbound][$row['PROGRAM_NO']]['BOOKING_TYPE'] = $bookingType;

			//for YARN_COUNT
			$expYarn_count = explode(',',$row['YARN_COUNT']);
			foreach($expYarn_count as $yCount)
			{
				if($yCount*1 != 0)
				{
					$dataArr[$inhouseOutbound][$row['PROGRAM_NO']]['YARN_COUNT'][$yCount] = $yarn_count_details[$yCount];
				}
			}
			//$dataArr[$inhouseOutbound][$row['PROGRAM_NO']]['YARN_COUNT'][$row['YARN_COUNT']] = $yarn_count_details[$row['YARN_COUNT']];

			//for BRAND
			$expBrand = explode(',',$row['BRAND_ID']);
			foreach($expBrand as $brandId)
			{
				if($brandId*1 != 0)
				{
					$dataArr[$inhouseOutbound][$row['PROGRAM_NO']]['YARN_BRAND_ID'][$brandId] = $brand_details[$brandId];
				}
			}
			//$dataArr[$inhouseOutbound][$row['PROGRAM_NO']]['YARN_BRAND_ID'][$row['BRAND_ID']] = $brand_details[$row['BRAND_ID']];

			//for BRAND
			$expYarn_prod = explode(',',$row['YARN_PROD_ID']);
			foreach($expYarn_prod as $yarn_prod)
			{
				if($yarn_prod*1 != 0)
				{
					$dataArr[$inhouseOutbound][$row['PROGRAM_NO']]['YARN_COMPOSITION'][$yarn_prod] = $yarnCompositionArr[$yarn_prod];
				}
			}
			//$dataArr[$inhouseOutbound][$row['PROGRAM_NO']]['YARN_COMPOSITION'] = $yarnCompositionArr[$row['YARN_PROD_ID']];
			$dataArr[$inhouseOutbound][$row['PROGRAM_NO']]['YARN_TYPE'] = getYarnType($yarnTypeArr, $row['YARN_PROD_ID']);
			$dataArr[$inhouseOutbound][$row['PROGRAM_NO']]['YARN_LOT'][$row['YARN_LOT']] = $row['YARN_LOT'];
			
			//for coomposition
			$dataArr[$inhouseOutbound][$row['PROGRAM_NO']]['COMPOSITION'][$row['FEBRIC_DESCRIPTION_ID']] = $composition_arr[$row['FEBRIC_DESCRIPTION_ID']];
			//for construction
			$dataArr[$inhouseOutbound][$row['PROGRAM_NO']]['CONSTRUCTION'][$row['FEBRIC_DESCRIPTION_ID']] = $construction_arr[$row['FEBRIC_DESCRIPTION_ID']];
			
			//for color
			$expColor = explode(',',$row['COLOR_ID']);
			foreach($expColor as $clr)
			{
				if($clr*1 != 0)
				{
					$dataArr[$inhouseOutbound][$row['PROGRAM_NO']]['COLOR_ID'][$clr] = $color_details[$clr];
				}
			}
			$dataArr[$inhouseOutbound][$row['PROGRAM_NO']]['COLOR_RANGE_ID'] = $color_range[$row['COLOR_RANGE_ID']];
			$dataArr[$inhouseOutbound][$row['PROGRAM_NO']]['STITCH_LENGTH'] = $row['STITCH_LENGTH'];
			$dataArr[$inhouseOutbound][$row['PROGRAM_NO']]['DIA'] = $row['WIDTH'];
			$dataArr[$inhouseOutbound][$row['PROGRAM_NO']]['GSM'] = $row['GSM'];
			$dataArr[$inhouseOutbound][$row['PROGRAM_NO']]['PROGRAM_QNTY'] = $programArr[$row['PROGRAM_NO']]['PROGRAM_QNTY'];
			
			//for production qty
			$rcvDate = date('d-m-Y', strtotime($row['RECEIVE_DATE']));
			if(strtotime($rcvDate) == strtotime($txt_date))
			{
				$dataArr[$inhouseOutbound][$row['PROGRAM_NO']]['CURRENT_PRODUCTION_QNT'] += $row['GREY_RECEIVE_QNTY'];
			}
			else
			{
				$dataArr[$inhouseOutbound][$row['PROGRAM_NO']]['PREVIOUS_PRODUCTION_QNT'] += $row['GREY_RECEIVE_QNTY'];
			}
			$dataArr[$inhouseOutbound][$row['PROGRAM_NO']]['REJECT_QTY'] += $row['REJECT_QTY'];
			
			//for remarks
			$remarksArr = array();
			if($row['REMARKS'] != '')
				$remarksArr[0]= $row['REMARKS'];
			
			if($programArr[$row['PROGRAM_NO']]['REMARKS'] != '')
				$remarksArr[1]= $programArr[$row['PROGRAM_NO']]['REMARKS'];
			
			$dataArr[$inhouseOutbound][$row['PROGRAM_NO']]['REMARKS'] = implode(', ', $remarksArr);
			$dataArr[$inhouseOutbound][$row['PROGRAM_NO']]['FLOOR_ID'] .=$floor_arr[$row['FLOOR_ID']].',';
			$dataArr[$inhouseOutbound][$row['PROGRAM_NO']]['CUST_BUYER'] = $buyer_arr[$row['CUSTOMER_BUYER']];

		}
		ksort($dataArr);
		// echo "<pre>"; print_r($dataArr);
		$tbl_width = 3190;
		ob_start();
		?>
		<fieldset style="width:<? echo $tbl_width+20; ?>px;">
            <table cellpadding="0" cellspacing="0" width="<? echo $tbl_width; ?>">
                <tr>
                    <td align="center" width="100%" colspan="32" class="form_caption" style="font-size:18px"><? echo $report_title; ?></td>
                </tr>
                <tr>
                    <td align="center" width="100%" colspan="32" class="form_caption" style="font-size:16px"><? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></td>
                </tr>
                <tr> 
                    <td align="center" width="100%" colspan="32" class="form_caption" style="font-size:12px" ><strong><? echo "Date ".str_replace("'","",$txt_date); ?></strong></td>
                </tr>
            </table>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_width; ?>" class="rpt_table">
                <thead>
                    <tr>
                        <th width="30">SL</th>
                        <th width="60">WG<br />(Yes/No)</th>
						<th width="100">Knitting Company</th>
                        <th width="90">M/C No.</th>
                        <th width="70">M/C Brand </th>
                        <th width="70">Production Date</th>
                        <th width="60">M/C Dia &  Gauge</th>
                        <th width="70">Unit  Name</th>
                        <th width="70">Floor</th>
                        <th width="70">Buyer</th>
                        <th width="70">Cust Buyer</th>
                        <th width="140">Program/ Sales Order No</th>
						<th width="100">Body Part</th>
                        <th width="140">Booking Type</th>
                        <th width="140">Booking No</th>
                        <th width="70">Yarn Count</th>
                        <th width="80">Brand</th>
                        <th width="150">Yarn Composition</th>
						<th width="80">Yarn Type</th>
                        <th width="80">Lot</th>
                        <th width="100">Construction</th>
                        <th width="150">Composition</th>
                        <th width="130">Color</th>
                        <th width="100">Color Range</th>
                        <th width="60">Stich</th>
                        <th width="60">Dia</th>
                        <th width="60">GSM</th>
                        <th width="100">Program Qty</th>
                        <th width="100">Yarn Issue</th>
                        <th width="100">Previous production Qty</th>
                        <th width="100">Current production Qty</th>
                        <th width="100">Total Production</th>
                        <th width="100">Balance Qty</th>
                        <th width="80">Reject Qty</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
            </table>
            <div style="width:<?= $tbl_width + 20; ?>px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;" id="scroll_body">
                <table width="<?= $tbl_width; ?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_head">    
	                <tbody>
						<?php
						$sl = 0;
						$grandTotalProgramQty = 0;
						$grandTotalPreviousProductionQty = 0;
						$grandTotalCurrentProductionQty = 0;
						$grandTotalProductionQty = 0;
						$grandTotalBalanceQty = 0;
						$grandTotalRejectQty = 0;
						$grandTotalYarn_issue_qty=0;
						foreach($dataArr as $isInhouse=>$isInhouseArr)
						{
							$totalProgramQty = 0;
							$totalPreviousProductionQty = 0;
							$totalCurrentProductionQty = 0;
							$totalProductionQty = 0;
							$totalBalanceQty = 0;
							$totalRejectQty = 0;
							$totalYarn_issue_qty=0;
							foreach($isInhouseArr as $programNo=>$row)
							{
								//for total production qty
								$productionQty = number_format($row['PREVIOUS_PRODUCTION_QNT'],2,'.','')+number_format($row['CURRENT_PRODUCTION_QNT'],2,'.','');
								$balanceQty = number_format($row['PROGRAM_QNTY'],2,'.','')-$productionQty;
									
								$is_print = 0;
								if($sales_order_no != '')
								{
									if($productionQty*1 != 0)
									{
										$is_print = 1;
									}
								}
								else
								{
									if($row['CURRENT_PRODUCTION_QNT']*1 != 0)
									{
										$is_print = 1;
									}
								}
								
								if($is_print != 0)
								{
									if($check[$isInhouse] != $isInhouse)
									{
										$check[$isInhouse] = $isInhouse;
										?>
										<tr bgcolor="#CCCCCC">
											<td colspan="35"><?php echo ($isInhouse==1?'In-House':'Outbound Subcontract'); ?></td>
										</tr>
										<?php
									}

									$sl++;
									if ($sl%2 == 0)
										$bgcolor = "#E9F3FF";
									else
										$bgcolor = "#FFFFFF";
									
									//total calculating
									$totalProgramQty += number_format($row['PROGRAM_QNTY'],2,'.','');
									$totalYarn_issue_qty += number_format($yarn_issue_qty_arr[$programNo],2,'.','');
									$totalPreviousProductionQty += number_format($row['PREVIOUS_PRODUCTION_QNT'],2,'.','');
									$totalCurrentProductionQty += number_format($row['CURRENT_PRODUCTION_QNT'],2,'.','');
									$totalProductionQty += $productionQty;
									$totalBalanceQty += $balanceQty;
									$totalRejectQty += number_format($row['REJECT_QTY'],2,'.','');
									
									//grand total calculating
									$grandTotalProgramQty += number_format($row['PROGRAM_QNTY'],2,'.','');
									$grandTotalYarn_issue_qty += number_format($yarn_issue_qty_arr[$programNo],2,'.','');
									$grandTotalPreviousProductionQty += number_format($row['PREVIOUS_PRODUCTION_QNT'],2,'.','');
									$grandTotalCurrentProductionQty += number_format($row['CURRENT_PRODUCTION_QNT'],2,'.','');
									$grandTotalProductionQty += $productionQty;
									$grandTotalBalanceQty += $balanceQty;
									$grandTotalRejectQty += number_format($row['REJECT_QTY'],2,'.','');
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $sl; ?>" valign="middle" height="25">
										<td width="30" align="center"><?php echo $sl; ?></td>
										<td width="60" align="center"><p><?php echo $row['WITHIN_GROUP']; ?></p></td>
										<td width="100"><p><?php echo $row['KNITTING_COMPANY']; ?></p></td>
										<td width="90" align="center"><p><?php echo implode(",", array_unique(explode(",", chop($row['MACHINE_NO_ID'],",")))); ?></p></td>
										<td width="70" align="center"><p><?php echo $row['BRAND_ID']; ?></p></td>
										<td width="70" align="center"><p><?php echo $row['RECEIVE_DATE']; ?></p></td>
										<td width="60" align="center"><p><?php echo $row['MACHINE_DIA_GG']; ?></p></td>
										<td width="70"><p><?php echo $row['UNIT_NAME']; ?></p></td>
										<td width="70"><p><?php echo implode(",", array_unique(explode(",", chop($row['FLOOR_ID'],",")))); ?></p></td>
										<td width="70"><p><?php echo $row['BUYER_NAME']; ?></p></td>
										<td width="70"><p><?php echo $row['CUST_BUYER']; ?></p></td>
										<td width="140" align="center"><strong>P:</strong><p><?php echo $programNo; ?><br><strong>S:</strong><p><?php echo $row['JOB_NO']; ?></p></td>
										<td width="100"><p><?php echo implode(', ',$row['BODY_PART_ID']); ?></p></td>
										<td width="140"><p><?php echo $row['BOOKING_TYPE']; ?></p></td>
										<td width="140"><p><?php echo $row['SALES_BOOKING_NO']; ?></p></td>
										<td width="70"><p><?php echo implode(', ',$row['YARN_COUNT']); ?></p></td>
										<td width="80"><p><?php echo implode(', ',$row['YARN_BRAND_ID']); ?></p></td>
										<td width="150"><p><?php echo implode(', ',$row['YARN_COMPOSITION']); ?></p></td>
										<td width="80"><p><?php echo $row['YARN_TYPE']; ?></p></td>
										<td width="80"><p><?php echo implode(', ',$row['YARN_LOT']); ?></p></td>
										<td width="100"><p><?php echo implode(', ',$row['CONSTRUCTION']); ?></p></td>
										<td width="150"><p><?php echo implode(', ',$row['COMPOSITION']); ?></p></td>
										<td width="130"><p><?php echo implode(', ',$row['COLOR_ID']); ?></p></td>
										<td width="100"><p><?php echo $row['COLOR_RANGE_ID']; ?></p></td>
										<td width="60" align="center"><p><?php echo $row['STITCH_LENGTH']; ?></p></td>
										<td width="60" align="center"><p><?php echo $row['DIA']; ?></p></td>
										<td width="60" align="center"><p><?php echo $row['GSM']; ?></p></td>
										<td width="100" align="right"><p><?php echo number_format($row['PROGRAM_QNTY'],2); ?></p></td>
										<td width="100" align="right"><? echo number_format($yarn_issue_qty_arr[$programNo],2); ?></p></td>
										<td width="100" align="right"><p><?php echo number_format($row['PREVIOUS_PRODUCTION_QNT'],2);//($sales_order_no == '' ? number_format($row['PREVIOUS_PRODUCTION_QNT'],2) : ''); ?></p></td>
										<td width="100" align="right"><p><?php echo number_format($row['CURRENT_PRODUCTION_QNT'],2);//($sales_order_no == '' ? number_format($row['CURRENT_PRODUCTION_QNT'],2) : ''); ?></p></td>
										<td width="100" align="right"><p><?php echo number_format($productionQty,2); ?></p></td>
										<td width="100" align="right"><p><?php echo number_format($balanceQty,2); ?></p></td>
										<td width="80" align="right"><p><?php echo number_format($row['REJECT_QTY'],2); ?></p></td>
										<td><p><?php echo $row['REMARKS']; ?></p></td>
									</tr>
									<?php
								}
							}
							if($check[$isInhouse] == $isInhouse)
							{
								$check[$isInhouse] = $isInhouse;
								?>
								<tr bgcolor="#CCCCCC">
									<td colspan="27" align="right"><?php echo ($isInhouse==1?'In-House':'Outbound Subcontract'); ?></td>
									<td align="right"><?php echo number_format($totalProgramQty,2); ?></td>
									<td align="right"><?php echo number_format($totalYarn_issue_qty,2); ?></td>
									<td align="right"><?php echo ($sales_order_no == '' ? number_format($totalPreviousProductionQty,2) : ''); ?></td>
									<td align="right"><?php echo ($sales_order_no == '' ? number_format($totalCurrentProductionQty,2) : ''); ?></td>
									<td align="right"><?php echo number_format($totalProductionQty,2); ?></td>
									<td align="right"><?php echo number_format($totalBalanceQty,2); ?></td>
									<td align="right"><?php echo number_format($totalRejectQty,2); ?></td>
									<td></td>
								</tr>
								<?php
							}
						}
						?>
	                </tbody>
	                <tfoot>
	                	<tr>
	                        <td colspan="27" align="right">Grand Total</td>
	                        <td align="right"><?php echo number_format($grandTotalProgramQty,2); ?></td>
	                        <td align="right"><?php echo number_format($grandTotalYarn_issue_qty,2); ?></td>
	                        <td align="right"><?php echo ($sales_order_no == '' ? number_format($grandTotalPreviousProductionQty,2) : ''); ?></td>
	                        <td align="right"><?php echo ($sales_order_no == '' ? number_format($grandTotalCurrentProductionQty,2) : ''); ?></td>
	                        <td align="right"><?php echo number_format($grandTotalProductionQty,2); ?></td>
	                        <td align="right"><?php echo number_format($grandTotalBalanceQty,2); ?></td>
	                        <td align="right"><?php echo number_format($grandTotalRejectQty,2); ?></td>
	                        <td></td>
	                    </tr>
	                </tfoot>
	            </table>
            </div>
        </fieldset>
		<?php
    }
	
	//for Prod. Against FSO button
	if($report_type == 2)	
	{
		$dataArr = array();
		$sql = "SELECT a.id AS PROGRAM_NO, a.program_qnty AS PROGRAM_QNTY, a.knitting_source AS KNITTING_SOURCE, a.knitting_party AS KNITTING_PARTY, a.color_range AS COLOR_RANGE_ID, a.stitch_length AS STITCH_LENGTH, a.machine_dia AS MACHINE_DIA, a.machine_gg AS MACHINE_GG, a.color_id AS COLOR_ID, a.remarks AS REMARKS, b.company_id AS COMPANY_ID, b.buyer_id AS BUYER_ID, B.CUSTOMER_BUYER, b.booking_no AS SALES_BOOKING_NO, b.po_id AS PO_ID, b.body_part_id AS BODY_PART_ID, b.determination_id AS DETERMINATION_ID, b.fabric_desc AS FABRIC_DESC, b.gsm_weight AS GSM, b.dia AS WIDTH, b.width_dia_type AS WIDTH_DIA_TYPE, b.yarn_desc AS YARN_DESC, b.color_type_id AS COLOR_TYPE_ID, b.within_group AS WITHIN_GROUP FROM PPL_PLANNING_info_ENTRY_DTLS a, PPL_PLANNING_ENTRY_PLAN_DTLS b WHERE a.id = b.dtls_id AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 AND b.is_sales = 1 ".where_con_using_array($salesBookingNoArr,1,'b.booking_no')." AND a.id NOT IN(".implode(',', $programNoArr).")";
		//echo $sql;
		$sqlRslt = sql_select($sql);
		foreach($sqlRslt as $row)
		{
			//for knitting company
			$inhouseOutbound = 1;
			if($row['KNITTING_SOURCE'] == 1)
			{
				$knitting_company = $company_arr[$row['KNITTING_PARTY']];
			}
			else if($row['KNITTING_SOURCE'] == 3)
			{
				$knitting_company = $supplier_arr[$row['KNITTING_PARTY']];
				$inhouseOutbound = 2;
			}
			else
			{
				$knitting_company = "&nbsp;";
			}
			
			$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']]['KNITTING_COMPANY'] = $knitting_company;
			$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']]['RECEIVE_DATE'] = '';
			$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']]['MACHINE_NO_ID'] = '';
			$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']]['BRAND_ID'] = '';
			$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']]['MACHINE_DIA_GG'] = $row['MACHINE_DIA']." X ".$row['MACHINE_GG'];

			//for body part
			if($row['BODY_PART_ID']*1 != 0)
			{
				$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']]['BODY_PART_ID'][$row['BODY_PART_ID']] = $body_part[$row['BODY_PART_ID']];
			}
			$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']]['JOB_NO'] = $jobNoArr[$row['SALES_BOOKING_NO']];
			$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']]['SALES_BOOKING_NO'] = $row['SALES_BOOKING_NO'];
			$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']]['WITHIN_GROUP'] = ($row['WITHIN_GROUP']==1?'Yes':'No');
			$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']]['UNIT_NAME'] = ($row['WITHIN_GROUP']==1?$company_arr[$row['BUYER_ID']]:$company_arr[$row['COMPANY_ID']]);
			$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']]['BUYER_NAME'] = ($row['WITHIN_GROUP']==1?$buyerArr[$row['SALES_BOOKING_NO']]:$buyer_arr[$row['BUYER_ID']]);
			$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']]['CUST_BUYER_NAME'] = $buyer_arr[$row['CUSTOMER_BUYER']];
			
			//for booking type
			$bookingType = '';
			/*if($row['BOOKING_TYPE'] == 4)
			{
				if($row['IS_ORDER'] == 1)
				{
					$bookingType = 'Sample With Order';
				}
				else
				{
					$bookingType = 'Sample Without Order';
				}
			}
			else
			{
				$bookingType = $bookingTypeaArr[$row['ENTRY_FORM']];
			}*/			
			
			$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']]['BOOKING_TYPE'] = $bookingType;
			$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']]['YARN_COUNT'][$row['YARN_COUNT']] = '';
			$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']]['YARN_BRAND_ID'][$row['BRAND_ID']] = '';
			$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']]['YARN_COMPOSITION'] = '';
			$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']]['YARN_TYPE'] = '';
			$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']]['YARN_LOT'][$row['YARN_LOT']] = '';
			
			//for coomposition
			//$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']]['COMPOSITION'][$row['FEBRIC_DESCRIPTION_ID']] = $composition_arr[$row['DETERMINATION_ID']];
			$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']]['COMPOSITION'][$row['DETERMINATION_ID']] = $composition_arr[$row['DETERMINATION_ID']];
			//for construction
			//$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']]['CONSTRUCTION'][$row['FEBRIC_DESCRIPTION_ID']] = $construction_arr[$row['DETERMINATION_ID']];
			$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']]['CONSTRUCTION'][$row['DETERMINATION_ID']] = $construction_arr[$row['DETERMINATION_ID']];
			
			//for color
			$expColor = explode(',',$row['COLOR_ID']);
			foreach($expColor as $clr)
			{
				if($clr*1 != 0)
				{
					$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']]['COLOR_ID'][$clr] = $color_details[$clr];
				}
			}
			$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']]['COLOR_RANGE_ID'] = $color_range[$row['COLOR_RANGE_ID']];
			$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']]['STITCH_LENGTH'] = $row['STITCH_LENGTH'];
			$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']]['DIA'] = $row['WIDTH'];
			$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']]['GSM'] = $row['GSM'];
			$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']]['PROGRAM_QNTY'] = $row['PROGRAM_QNTY'];
			
			//for production qty
			$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']]['CURRENT_PRODUCTION_QNT'] = 0;
			$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']]['PREVIOUS_PRODUCTION_QNT'] = 0;
			$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']]['REJECT_QTY'] = 0;
			$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']]['REMARKS'] = $row['REMARKS'];
		}
		//echo "<pre>";
		//print_r($dataArr); die;

		foreach($sqlInhouseRslt as $row)
		{
			//for knitting company
			$inhouseOutbound = 1;
			if($row['KNITTING_SOURCE'] == 1)
			{
				$knitting_company = $company_arr[$row['KNITTING_COMPANY']];
			}
			else if($row['KNITTING_SOURCE'] == 3)
			{
				$knitting_company = $supplier_arr[$row['KNITTING_COMPANY']];
				$inhouseOutbound = 2;
			}
			else
			{
				$knitting_company = "&nbsp;";
			}
			
			$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']]['KNITTING_COMPANY'] = $knitting_company;
			$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']]['RECEIVE_DATE'] = date('d-m-Y', strtotime($row['RECEIVE_DATE']));
			$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']]['MACHINE_NO_ID'] .= ($inhouseOutbound==1?$machineArr[$row['MACHINE_NO_ID']]['MACHINE_NO']:'').',';
			$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']]['BRAND_ID'] = ($inhouseOutbound==1?$machineArr[$row['MACHINE_NO_ID']]['BRAND']:'');
			$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']]['MACHINE_DIA_GG'] = $programArr[$row['PROGRAM_NO']]['MACHINE_DIA']." X ".$programArr[$row['PROGRAM_NO']]['MACHINE_GG'];

			//for body part
			if($row['BODY_PART_ID']*1 != 0)
			{
				$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']]['BODY_PART_ID'][$row['BODY_PART_ID']] = $body_part[$row['BODY_PART_ID']];
			}
			$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']]['JOB_NO'] = $row['JOB_NO'];
			$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']]['SALES_BOOKING_NO'] = $row['SALES_BOOKING_NO'];
			$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']]['WITHIN_GROUP'] = ($row['WITHIN_GROUP']==1?'Yes':'No');
			$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']]['UNIT_NAME'] = ($row['WITHIN_GROUP']==1?$company_arr[$row['BUYER_ID']]:$company_arr[$row['COMPANY_ID']]);
			$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']]['BUYER_NAME'] = ($row['WITHIN_GROUP']==1?$buyerArr[$row['SALES_BOOKING_NO']]:$buyer_arr[$row['BUYER_ID']]);
			$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']]['CUST_BUYER_NAME'] =$buyer_arr[$row['CUSTOMER_BUYER']];
			$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']]['FLOOR_ID'] .=$floor_arr[$row['FLOOR_ID']].',';
			
			//for booking type
			$bookingType = '';
			if($row['BOOKING_TYPE'] == 4)
			{
				if($row['IS_ORDER'] == 1)
				{
					$bookingType = 'Sample With Order';
				}
				else
				{
					$bookingType = 'Sample Without Order';
				}
			}
			else
			{
				$bookingType = $bookingTypeaArr[$row['ENTRY_FORM']];
			}			
			
			$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']]['BOOKING_TYPE'] = $bookingType;
			$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']]['YARN_COUNT'][$row['YARN_COUNT']] = $yarn_count_details[$row['YARN_COUNT']];
			$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']]['YARN_BRAND_ID'][$row['BRAND_ID']] = $brand_details[$row['BRAND_ID']];
			$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']]['YARN_COMPOSITION'] = $yarnCompositionArr[$row['YARN_PROD_ID']];
			$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']]['YARN_TYPE'] = getYarnType($yarnTypeArr, $row['YARN_PROD_ID']);
			$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']]['YARN_LOT'][$row['YARN_LOT']] = $row['YARN_LOT'];
			
			//for coomposition
			$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']]['COMPOSITION'][$row['FEBRIC_DESCRIPTION_ID']] = $composition_arr[$row['FEBRIC_DESCRIPTION_ID']];
			//for construction
			$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']]['CONSTRUCTION'][$row['FEBRIC_DESCRIPTION_ID']] = $construction_arr[$row['FEBRIC_DESCRIPTION_ID']];
			
			//for color
			$expColor = explode(',',$row['COLOR_ID']);
			foreach($expColor as $clr)
			{
				if($clr*1 != 0)
				{
					$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']]['COLOR_ID'][$clr] = $color_details[$clr];
				}
			}
			$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']]['COLOR_RANGE_ID'] = $color_range[$row['COLOR_RANGE_ID']];
			$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']]['STITCH_LENGTH'] = $row['STITCH_LENGTH'];
			$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']]['DIA'] = $row['WIDTH'];
			$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']]['GSM'] = $row['GSM'];
			$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']]['PROGRAM_QNTY'] = $programArr[$row['PROGRAM_NO']]['PROGRAM_QNTY'];
			
			//for production qty
			$rcvDate = date('d-m-Y', strtotime($row['RECEIVE_DATE']));
			if(strtotime($rcvDate) == strtotime($txt_date))
			{
				$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']]['CURRENT_PRODUCTION_QNT'] += $row['GREY_RECEIVE_QNTY'];
			}
			else
			{
				$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']]['PREVIOUS_PRODUCTION_QNT'] += $row['GREY_RECEIVE_QNTY'];
			}
			$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']]['REJECT_QTY'] += $row['REJECT_QTY'];
			
			//for remarks
			$remarksArr = array();
			if($row['REMARKS'] != '')
				$remarksArr[0]= $row['REMARKS'];
			
			if($programArr[$row['PROGRAM_NO']]['REMARKS'] != '')
				$remarksArr[1]= $programArr[$row['PROGRAM_NO']]['REMARKS'];
			
			$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']]['REMARKS'] = implode(', ', $remarksArr);
		}
		ksort($dataArr);
		//echo "<pre>";
		//print_r($dataArr);
		$tbl_width = 2820;
		ob_start();
		?>
		<fieldset style="width:<? echo $tbl_width+20; ?>px;">
            <table cellpadding="0" cellspacing="0" width="<? echo $tbl_width; ?>">
                <tr>
                    <td align="center" width="100%" colspan="32" class="form_caption" style="font-size:18px"><? echo $report_title; ?></td>
                </tr>
                <tr>
                    <td align="center" width="100%" colspan="32" class="form_caption" style="font-size:16px"><? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></td>
                </tr>
                <tr> 
                    <td align="center" width="100%" colspan="32" class="form_caption" style="font-size:12px" ><strong><? echo "Date ".str_replace("'","",$txt_date); ?></strong></td>
                </tr>
            </table>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_width; ?>" class="rpt_table" id="table_head" >
                <thead>
                    <tr>
                        <th width="30">SL</th>
                        <th width="60">WG<br />(Yes/No)</th>
						<th width="100">Knitting Company</th>
                        <th width="90">M/C No.</th>
                        <th width="70">M/C Brand </th>
                        <th width="60">M/C Dia &  Gauge</th>
                        <th width="70">Unit  Name</th>
                        <th width="70">Floor</th>
                        <th width="70">Buyer</th>
                        <th width="70">Cust. Buyer</th>
                        <th width="140">Program/ Sales Order No</th>
						<th width="100">Body Part</th>
                        <th width="140">Booking Type</th>
                        <th width="140">Booking No</th>
                        <th width="70">Yarn Count</th>
                        <th width="80">Brand</th>
                        <th width="150">Yarn Composition</th>
						<th width="80">Yarn Type</th>
                        <th width="80">Lot</th>
                        <th width="100">Construction</th>
                        <th width="150">Composition</th>
                        <th width="130">Color</th>
                        <th width="100">Color Range</th>
                        <th width="60">Stich</th>
                        <th width="60">Dia</th>
                        <th width="60">GSM</th>
                        <th width="100">Program Qty</th>
                        <th width="100">Previous production Qty</th>
                        <th width="100">Current production Qty</th>
                        <th width="100">Total Production</th>
                        <th width="100">Balance Qty</th>
                        <th width="80">Reject Qty</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
				<?php
                $sl = 0;
                $grandTotalProgramQty = 0;
                $grandTotalPreviousProductionQty = 0;
                $grandTotalCurrentProductionQty = 0;
                $grandTotalProductionQty = 0;
                $grandTotalBalanceQty = 0;
                $grandTotalRejectQty = 0;
                
                foreach($dataArr as $isInhouse=>$isInhouseArr)
                {
                    $totalProgramQty = 0;
                    $totalPreviousProductionQty = 0;
                    $totalCurrentProductionQty = 0;
                    $totalProductionQty = 0;
                    $totalBalanceQty = 0;
                    $totalRejectQty = 0;
                    ?>
                    <tr bgcolor="#CCCCCC">
                        <td colspan="33"><?php echo ($isInhouse==1?'In-House':'Outbound Subcontract'); ?></td>
                    </tr>
                    <?php
                    foreach($isInhouseArr as $salesOrder=>$salesOrderArr)
                    {
                        foreach($salesOrderArr as $programNo=>$row)
                        {
                            //foreach($programNoArr as $prodDate=>$row)
                            //{
                                $sl++;
                                if ($sl%2 == 0)
                                    $bgcolor = "#E9F3FF";
                                else
                                    $bgcolor = "#FFFFFF";
                                
                                //for total production qty
                                $productionQty = number_format($row['PREVIOUS_PRODUCTION_QNT'],2,'.','')+number_format($row['CURRENT_PRODUCTION_QNT'],2,'.','');
                                $balanceQty = number_format($row['PROGRAM_QNTY'],2,'.','')-$productionQty;
                                
                                //total calculating
                                $totalProgramQty += number_format($row['PROGRAM_QNTY'],2,'.','');
                                $totalPreviousProductionQty += number_format($row['PREVIOUS_PRODUCTION_QNT'],2,'.','');
                                $totalCurrentProductionQty += number_format($row['CURRENT_PRODUCTION_QNT'],2,'.','');
                                $totalProductionQty += $productionQty;
                                $totalBalanceQty += $balanceQty;
                                $totalRejectQty += number_format($row['REJECT_QTY'],2,'.','');
                                
                                //grand total calculating
                                $grandTotalProgramQty += number_format($row['PROGRAM_QNTY'],2,'.','');
                                $grandTotalPreviousProductionQty += number_format($row['PREVIOUS_PRODUCTION_QNT'],2,'.','');
                                $grandTotalCurrentProductionQty += number_format($row['CURRENT_PRODUCTION_QNT'],2,'.','');
                                $grandTotalProductionQty += $productionQty;
                                $grandTotalBalanceQty += $balanceQty;
                                $grandTotalRejectQty += number_format($row['REJECT_QTY'],2,'.','');
                                ?>
                                <tr bgcolor="<?php echo $bgcolor;?>" valign="middle" height="25">
                                    <td align="center"><?php echo $sl; ?></td>
                                    <td align="center"><?php echo $row['WITHIN_GROUP']; ?></td>
                                    <td><?php echo $row['KNITTING_COMPANY']; ?></td>
                                    <td align="center"><?php echo implode(",", array_unique(explode(",", chop($row['MACHINE_NO_ID'],",")))); ?></td>
                                    <td align="center"><?php echo $row['BRAND_ID']; ?></td>
                                    <td align="center"><?php echo $row['MACHINE_DIA_GG']; ?></td>
                                    <td><?php echo $row['UNIT_NAME']; ?></td>
                                    <td><?php echo implode(",", array_unique(explode(",", chop($row['FLOOR_ID'],",")))); ?></td>
                                    <td><?php echo $row['BUYER_NAME']; ?></td>
                                    <td><?php echo $row['CUST_BUYER_NAME']; ?></td>
                                    <td align="center"><strong>P:</strong><?php echo $programNo; ?><br><strong>S:</strong><?php echo $row['JOB_NO']; ?></td>
                                    <td><?php echo implode(', ',$row['BODY_PART_ID']); ?></td>
                                    <td><?php echo $row['BOOKING_TYPE']; ?></td>
                                    <td><?php echo $row['SALES_BOOKING_NO']; ?></td>
                                    <td><?php echo implode(', ',$row['YARN_COUNT']); ?></td>
                                    <td><?php echo implode(', ',$row['YARN_BRAND_ID']); ?></td>
                                    <td><?php echo $row['YARN_COMPOSITION']; ?></td>
                                    <td><?php echo $row['YARN_TYPE']; ?></td>
                                    <td><?php echo implode(', ',$row['YARN_LOT']); ?></td>
                                    <td><?php echo implode(', ',$row['CONSTRUCTION']); ?></td>
                                    <td><?php echo implode(', ',$row['COMPOSITION']); ?></td>
                                    <td><?php echo implode(', ',$row['COLOR_ID']); ?></td>
                                    <td><?php echo $row['COLOR_RANGE_ID']; ?></td>
                                    <td align="center"><?php echo $row['STITCH_LENGTH']; ?></td>
                                    <td align="center"><?php echo $row['DIA']; ?></td>
                                    <td align="center"><?php echo $row['GSM']; ?></td>
                                    <td align="right"><?php echo number_format($row['PROGRAM_QNTY'],2); ?></td>
                                    <td align="right"><?php echo ($sales_order_no == '' ? number_format($row['PREVIOUS_PRODUCTION_QNT'],2) : ''); ?></td>
                                    <td align="right"><?php echo ($sales_order_no == '' ? number_format($row['CURRENT_PRODUCTION_QNT'],2) : ''); ?></td>
                                    <td align="right"><?php echo number_format($productionQty,2); ?></td>
                                    <td align="right"><?php echo number_format($balanceQty,2); ?></td>
                                    <td align="right"><?php echo number_format($row['REJECT_QTY'],2); ?></td>
                                    <td><?php echo $row['REMARKS']; ?></td>
                                </tr>
                                <?php
                            //}
                        }
                    }
                    ?>
                    <tr bgcolor="#CCCCCC">
                        <td colspan="26" align="right"><?php echo ($isInhouse==1?'In-House':'Outbound Subcontract'); ?></td>
                        <td align="right"><?php echo number_format($totalProgramQty,2); ?></td>
                        <td align="right"><?php echo ($sales_order_no == '' ? number_format($totalPreviousProductionQty,2) : ''); ?></td>
                        <td align="right"><?php echo ($sales_order_no == '' ? number_format($totalCurrentProductionQty,2) : ''); ?></td>
                        <td align="right"><?php echo number_format($totalProductionQty,2); ?></td>
                        <td align="right"><?php echo number_format($totalBalanceQty,2); ?></td>
                        <td align="right"><?php echo number_format($totalRejectQty,2); ?></td>
                        <td></td>
                    </tr>
                    <?php
                }
            ?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="26" align="right">Grand Total</td>
					<td align="right"><?php echo number_format($grandTotalProgramQty,2); ?></td>
					<td align="right"><?php echo ($sales_order_no == '' ? number_format($grandTotalPreviousProductionQty,2) : ''); ?></td>
					<td align="right"><?php echo ($sales_order_no == '' ? number_format($grandTotalCurrentProductionQty,2) : ''); ?></td>
					<td align="right"><?php echo number_format($grandTotalProductionQty,2); ?></td>
					<td align="right"><?php echo number_format($grandTotalBalanceQty,2); ?></td>
					<td align="right"><?php echo number_format($grandTotalRejectQty,2); ?></td>
                    <td></td>
				</tr>
			</tfoot>
		</table>
		</fieldset>
		<?php
	}
	if($report_type == 2020)	
	{
		$dataArr = array();
		foreach($sqlInhouseRslt as $row)
		{
			//for knitting company
			$inhouseOutbound = 1;
			if($row['KNITTING_SOURCE'] == 1)
			{
				$knitting_company = $company_arr[$row['KNITTING_COMPANY']];
			}
			else if($row['KNITTING_SOURCE'] == 3)
			{
				$knitting_company = $supplier_arr[$row['KNITTING_COMPANY']];
				$inhouseOutbound = 2;
			}
			else
			{
				$knitting_company = "&nbsp;";
			}
			
			$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']][$row['RECEIVE_DATE']]['KNITTING_COMPANY'] = $knitting_company;
			$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']][$row['RECEIVE_DATE']]['RECEIVE_DATE'] = date('d-m-Y', strtotime($row['RECEIVE_DATE']));
			$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']][$row['RECEIVE_DATE']]['MACHINE_NO_ID'] = ($inhouseOutbound==1?$machineArr[$row['MACHINE_NO_ID']]['MACHINE_NO']:'');
			$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']][$row['RECEIVE_DATE']]['BRAND_ID'] = ($inhouseOutbound==1?$machineArr[$row['MACHINE_NO_ID']]['BRAND']:'');
			$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']][$row['RECEIVE_DATE']]['MACHINE_DIA_GG'] = $programArr[$row['PROGRAM_NO']]['MACHINE_DIA']." X ".$programArr[$row['PROGRAM_NO']]['MACHINE_GG'];

			//for body part
			if($row['BODY_PART_ID']*1 != 0)
			{
				$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']][$row['RECEIVE_DATE']]['BODY_PART_ID'][$row['BODY_PART_ID']] = $body_part[$row['BODY_PART_ID']];
			}
			$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']][$row['RECEIVE_DATE']]['JOB_NO'] = $row['JOB_NO'];
			$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']][$row['RECEIVE_DATE']]['SALES_BOOKING_NO'] = $row['SALES_BOOKING_NO'];
			$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']][$row['RECEIVE_DATE']]['WITHIN_GROUP'] = ($row['WITHIN_GROUP']==1?'Yes':'No');
			$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']][$row['RECEIVE_DATE']]['UNIT_NAME'] = ($row['WITHIN_GROUP']==1?$company_arr[$row['BUYER_ID']]:$company_arr[$row['COMPANY_ID']]);
			$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']][$row['RECEIVE_DATE']]['BUYER_NAME'] = ($row['WITHIN_GROUP']==1?$buyerArr[$row['SALES_BOOKING_NO']]:$buyer_arr[$row['BUYER_ID']]);
			
			//for booking type
			$bookingType = '';
			if($row['BOOKING_TYPE'] == 4)
			{
				if($row['IS_ORDER'] == 1)
				{
					$bookingType = 'Sample With Order';
				}
				else
				{
					$bookingType = 'Sample Without Order';
				}
			}
			else
			{
				$bookingType = $bookingTypeaArr[$row['ENTRY_FORM']];
			}			
			
			$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']][$row['RECEIVE_DATE']]['BOOKING_TYPE'] = $bookingType;
			$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']][$row['RECEIVE_DATE']]['YARN_COUNT'][$row['YARN_COUNT']] = $yarn_count_details[$row['YARN_COUNT']];
			$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']][$row['RECEIVE_DATE']]['YARN_BRAND_ID'][$row['BRAND_ID']] = $brand_details[$row['BRAND_ID']];
			$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']][$row['RECEIVE_DATE']]['YARN_COMPOSITION'] = $yarnCompositionArr[$row['YARN_PROD_ID']];
			$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']][$row['RECEIVE_DATE']]['YARN_TYPE'] = getYarnType($yarnTypeArr, $row['YARN_PROD_ID']);
			$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']][$row['RECEIVE_DATE']]['YARN_LOT'][$row['YARN_LOT']] = $row['YARN_LOT'];
			
			//for coomposition
			$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']][$row['RECEIVE_DATE']]['COMPOSITION'][$row['FEBRIC_DESCRIPTION_ID']] = $composition_arr[$row['FEBRIC_DESCRIPTION_ID']];
			//for construction
			$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']][$row['RECEIVE_DATE']]['CONSTRUCTION'][$row['FEBRIC_DESCRIPTION_ID']] = $construction_arr[$row['FEBRIC_DESCRIPTION_ID']];
			
			//for color
			$expColor = explode(',',$row['COLOR_ID']);
			foreach($expColor as $clr)
			{
				if($clr*1 != 0)
				{
					$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']][$row['RECEIVE_DATE']]['COLOR_ID'][$clr] = $color_details[$clr];
				}
			}
			$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']][$row['RECEIVE_DATE']]['COLOR_RANGE_ID'] = $color_range[$row['COLOR_RANGE_ID']];
			$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']][$row['RECEIVE_DATE']]['STITCH_LENGTH'] = $row['STITCH_LENGTH'];
			$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']][$row['RECEIVE_DATE']]['DIA'] = $row['WIDTH'];
			$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']][$row['RECEIVE_DATE']]['GSM'] = $row['GSM'];
			$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']][$row['RECEIVE_DATE']]['PROGRAM_QNTY'] = $programArr[$row['PROGRAM_NO']]['PROGRAM_QNTY'];
			
			//for production qty
			$rcvDate = date('d-m-Y', strtotime($row['RECEIVE_DATE']));
			if(strtotime($rcvDate) == strtotime($txt_date))
			{
				$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']][$row['RECEIVE_DATE']]['CURRENT_PRODUCTION_QNT'] += $row['GREY_RECEIVE_QNTY'];
			}
			else
			{
				$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']][$row['RECEIVE_DATE']]['PREVIOUS_PRODUCTION_QNT'] += $row['GREY_RECEIVE_QNTY'];
			}
			$dataArr[$inhouseOutbound][$row['SALES_BOOKING_NO']][$row['PROGRAM_NO']][$row['RECEIVE_DATE']]['REJECT_QTY'] += $row['REJECT_QTY'];
		}
		ksort($dataArr);
		//echo "<pre>";
		//print_r($dataArr);
		$tbl_width = 2680;
		ob_start();
		?>
		<fieldset style="width:<? echo $tbl_width+20; ?>px;">
            <table cellpadding="0" cellspacing="0" width="<? echo $tbl_width; ?>">
                <tr>
                    <td align="center" width="100%" colspan="32" class="form_caption" style="font-size:18px"><? echo $report_title; ?></td>
                </tr>
                <tr>
                    <td align="center" width="100%" colspan="32" class="form_caption" style="font-size:16px"><? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></td>
                </tr>
                <tr> 
                    <td align="center" width="100%" colspan="32" class="form_caption" style="font-size:12px" ><strong><? echo "Date ".str_replace("'","",$txt_date); ?></strong></td>
                </tr>
            </table>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_width; ?>" class="rpt_table" id="table_head" >
                <thead>
                    <tr>
                        <th width="30">SL</th>
                        <th width="60">WG<br />(Yes/No)</th>
						<th width="100">Knitting Company</th>
                        <th width="90">M/C No.</th>
                        <th width="70">M/C Brand </th>
                        <th width="60">M/C Dia &  Gauge</th>
                        <th width="70">Unit  Name</th>
                        <th width="70">Buyer</th>
                        <th width="140">Program/ Sales Order No</th>
						<th width="100">Body Part</th>
                        <th width="140">Booking Type</th>
                        <th width="140">Booking No</th>
                        <th width="70">Yarn Count</th>
                        <th width="80">Brand</th>
                        <th width="150">Yarn Composition</th>
						<th width="80">Yarn Type</th>
                        <th width="80">Lot</th>
                        <th width="100">Construction</th>
                        <th width="150">Composition</th>
                        <th width="130">Color</th>
                        <th width="100">Color Range</th>
                        <th width="60">Stich</th>
                        <th width="60">Dia</th>
                        <th width="60">GSM</th>
                        <th width="100">Program Qty</th>
                        <th width="100">Previous production Qty</th>
                        <th width="100">Current production Qty</th>
                        <th width="100">Total Production</th>
                        <th width="100">Balance Qty</th>
                        <th width="80">Reject Qty</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
				<?php
                $sl = 0;
                $grandTotalProgramQty = 0;
                $grandTotalPreviousProductionQty = 0;
                $grandTotalCurrentProductionQty = 0;
                $grandTotalProductionQty = 0;
                $grandTotalBalanceQty = 0;
                $grandTotalRejectQty = 0;
                
                foreach($dataArr as $isInhouse=>$isInhouseArr)
                {
                    $totalProgramQty = 0;
                    $totalPreviousProductionQty = 0;
                    $totalCurrentProductionQty = 0;
                    $totalProductionQty = 0;
                    $totalBalanceQty = 0;
                    $totalRejectQty = 0;
                    ?>
                    <tr bgcolor="#CCCCCC">
                        <td colspan="31"><?php echo ($isInhouse==1?'In-House':'Outbound Subcontract'); ?></td>
                    </tr>
                    <?php
                    foreach($isInhouseArr as $salesOrder=>$salesOrderArr)
                    {
                        foreach($salesOrderArr as $programNo=>$programNoArr)
                        {
                            foreach($programNoArr as $prodDate=>$row)
                            {
                                $sl++;
                                if ($sl%2 == 0)
                                    $bgcolor = "#E9F3FF";
                                else
                                    $bgcolor = "#FFFFFF";
                                
                                //for total production qty
                                $productionQty = number_format($row['PREVIOUS_PRODUCTION_QNT'],2,'.','')+number_format($row['CURRENT_PRODUCTION_QNT'],2,'.','');
                                $balanceQty = number_format($row['PROGRAM_QNTY'],2,'.','')-$productionQty;
                                
                                //total calculating
                                $totalProgramQty += number_format($row['PROGRAM_QNTY'],2,'.','');
                                $totalPreviousProductionQty += number_format($row['PREVIOUS_PRODUCTION_QNT'],2,'.','');
                                $totalCurrentProductionQty += number_format($row['CURRENT_PRODUCTION_QNT'],2,'.','');
                                $totalProductionQty += $productionQty;
                                $totalBalanceQty += $balanceQty;
                                $totalRejectQty += number_format($row['REJECT_QTY'],2,'.','');
                                
                                //grand total calculating
                                $grandTotalProgramQty += number_format($row['PROGRAM_QNTY'],2,'.','');
                                $grandTotalPreviousProductionQty += number_format($row['PREVIOUS_PRODUCTION_QNT'],2,'.','');
                                $grandTotalCurrentProductionQty += number_format($row['CURRENT_PRODUCTION_QNT'],2,'.','');
                                $grandTotalProductionQty += $productionQty;
                                $grandTotalBalanceQty += $balanceQty;
                                $grandTotalRejectQty += number_format($row['REJECT_QTY'],2,'.','');
                                ?>
                                <tr bgcolor="<?php echo $bgcolor;?>" valign="middle" height="25">
                                    <td align="center"><?php echo $sl; ?></td>
                                    <td align="center"><?php echo $row['WITHIN_GROUP']; ?></td>
                                    <td><?php echo $row['KNITTING_COMPANY']; ?></td>
                                    <td align="center"><?php echo $row['MACHINE_NO_ID']; ?></td>
                                    <td align="center"><?php echo $row['BRAND_ID']; ?></td>
                                    <td align="center"><?php echo $row['MACHINE_DIA_GG']; ?></td>
                                    <td><?php echo $row['UNIT_NAME']; ?></td>
                                    <td><?php echo $row['BUYER_NAME']; ?></td>
                                    <td align="center"><strong>P:</strong><?php echo $programNo; ?><br><strong>S:</strong><?php echo $row['JOB_NO']; ?></td>
                                    <td><?php echo implode(', ',$row['BODY_PART_ID']); ?></td>
                                    <td><?php echo $row['BOOKING_TYPE']; ?></td>
                                    <td><?php echo $row['SALES_BOOKING_NO']; ?></td>
                                    <td><?php echo implode(', ',$row['YARN_COUNT']); ?></td>
                                    <td><?php echo implode(', ',$row['YARN_BRAND_ID']); ?></td>
                                    <td><?php echo $row['YARN_COMPOSITION']; ?></td>
                                    <td><?php echo $row['YARN_TYPE']; ?></td>
                                    <td><?php echo implode(', ',$row['YARN_LOT']); ?></td>
                                    <td><?php echo implode(', ',$row['CONSTRUCTION']); ?></td>
                                    <td><?php echo implode(', ',$row['COMPOSITION']); ?></td>
                                    <td><?php echo implode(', ',$row['COLOR_ID']); ?></td>
                                    <td><?php echo $row['COLOR_RANGE_ID']; ?></td>
                                    <td align="center"><?php echo $row['STITCH_LENGTH']; ?></td>
                                    <td align="center"><?php echo $row['DIA']; ?></td>
                                    <td align="center"><?php echo $row['GSM']; ?></td>
                                    <td align="right"><?php echo number_format($row['PROGRAM_QNTY'],2); ?></td>
                                    <td align="right"><?php echo ($sales_order_no == '' ? number_format($row['PREVIOUS_PRODUCTION_QNT'],2) : ''); ?></td>
                                    <td align="right"><?php echo ($sales_order_no == '' ? number_format($row['CURRENT_PRODUCTION_QNT'],2) : ''); ?></td>
                                    <td align="right"><?php echo number_format($productionQty,2); ?></td>
                                    <td align="right"><?php echo number_format($balanceQty,2); ?></td>
                                    <td align="right"><?php echo number_format($row['REJECT_QTY'],2); ?></td>
                                    <td><?php echo $row['REMARKS']; ?></td>
                                </tr>
                                <?php
                            }
                        }
                    }
                    ?>
                    <tr bgcolor="#CCCCCC">
                        <td colspan="24" align="right"><?php echo ($isInhouse==1?'In-House':'Outbound Subcontract'); ?></td>
                        <td align="right"><?php echo number_format($totalProgramQty,2); ?></td>
                        <td align="right"><?php echo ($sales_order_no == '' ? number_format($totalPreviousProductionQty,2) : ''); ?></td>
                        <td align="right"><?php echo ($sales_order_no == '' ? number_format($totalCurrentProductionQty,2) : ''); ?></td>
                        <td align="right"><?php echo number_format($totalProductionQty,2); ?></td>
                        <td align="right"><?php echo number_format($totalBalanceQty,2); ?></td>
                        <td align="right"><?php echo number_format($totalRejectQty,2); ?></td>
                        <td></td>
                    </tr>
                    <?php
                }
            ?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="24" align="right">Grand Total</td>
					<td align="right"><?php echo number_format($grandTotalProgramQty,2); ?></td>
					<td align="right"><?php echo ($sales_order_no == '' ? number_format($grandTotalPreviousProductionQty,2) : ''); ?></td>
					<td align="right"><?php echo ($sales_order_no == '' ? number_format($grandTotalCurrentProductionQty,2) : ''); ?></td>
					<td align="right"><?php echo number_format($grandTotalProductionQty,2); ?></td>
					<td align="right"><?php echo number_format($grandTotalBalanceQty,2); ?></td>
					<td align="right"><?php echo number_format($grandTotalRejectQty,2); ?></td>
                    <td></td>
				</tr>
			</tfoot>
		</table>
		</fieldset>
		<?php
	}
	foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
			@unlink($filename);
	}
	
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
	echo $total_data."####".$filename;
	disconnect($con);
	exit();
}

//getYarnType
function getYarnType($yarn_type_arr, $yarnProdId)
{
	global $yarn_type;
	$yarn_type_name='';
	$expYPId = explode(",",$yarnProdId);
	$yarnTypeIdArr = array();
	foreach($expYPId as $key=>$val)
	{
		$yarnTypeIdArr[$yarn_type_arr[$val]] = $yarn_type_arr[$val];
	}
	
	foreach($yarnTypeIdArr as $key=>$val)
	{
		if($yarn_type_name == '')
			$yarn_type_name=$yarn_type[$val];
		else
			$yarn_type_name.=",".$yarn_type[$val];
	}
	return $yarn_type_name;
}
?>