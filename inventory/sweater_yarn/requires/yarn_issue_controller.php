<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if ($_SESSION['logic_erp']['user_id'] == "") {
	header("location:login.php");
	die;
}
$permission = $_SESSION['page_permission'];

$data = $_REQUEST['data'];
$action = $_REQUEST['action'];
$user_store_ids = $_SESSION['logic_erp']['store_location_id'];
$user_supplier_ids = $_SESSION['logic_erp']['supplier_id'];
$user_comp_location_ids = $_SESSION['logic_erp']['company_location_id'];

//load drop down supplier done
if ($action == "load_drop_down_supplier") {
	if($user_supplier_ids) $user_supplier_cond = " and c.id in ($user_supplier_ids)";else $user_supplier_cond = "";
	echo create_drop_down("cbo_supplier", 162, "select c.supplier_name,c.id from lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data' $user_supplier_cond and b.party_type=2 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by c.supplier_name", "id,supplier_name", 1, "-- Select --", 0, "", 1);
	exit();
}
if ($action == "load_drop_down_floor") {

	echo create_drop_down("cbo_floor", 152, "select FLOOR_ROOM_RACK_ID, FLOOR_ROOM_RACK_NAME from LIB_FLOOR_ROOM_RACK_MST where COMPANY_ID = '$data'", "FLOOR_ROOM_RACK_ID,FLOOR_ROOM_RACK_NAME", 1, "-- Select --", 0, "", 1);
	exit();
}
if ($action == "load_drop_down_room") {

	echo create_drop_down("cbo_room", 152, "select FLOOR_ROOM_RACK_ID, FLOOR_ROOM_RACK_NAME from LIB_FLOOR_ROOM_RACK_MST where COMPANY_ID = '$data'", "FLOOR_ROOM_RACK_ID,FLOOR_ROOM_RACK_NAME", 1, "-- Select --", 0, "", 1);
	exit();
}
if ($action == "load_drop_down_rack") {

	echo create_drop_down("txt_rack", 152, "select FLOOR_ROOM_RACK_ID, FLOOR_ROOM_RACK_NAME from LIB_FLOOR_ROOM_RACK_MST where COMPANY_ID = '$data'", "FLOOR_ROOM_RACK_ID,FLOOR_ROOM_RACK_NAME", 1, "-- Select --", 0, "", 1);
	exit();
}
if ($action == "load_drop_down_shelf") {

	echo create_drop_down("txt_shelf", 152, "select FLOOR_ROOM_RACK_ID, FLOOR_ROOM_RACK_NAME from LIB_FLOOR_ROOM_RACK_MST where COMPANY_ID = '$data'", "FLOOR_ROOM_RACK_ID,FLOOR_ROOM_RACK_NAME", 1, "-- Select --", 0, "", 1);
	exit();
}


if($action=="upto_variable_settings")
{
    $sql =  sql_select("select store_method from variable_settings_inventory where company_name = $data and item_category_id=1 and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1");
	$return_data="";
    if(count($sql)>0)
	{
		$return_data=$sql[0][csf('store_method')];
	}
	else
	{
		$return_data=0;
	}

	echo $return_data;
	die;
}

if ($action == "load_drop_down_supplier_com") {
	echo create_drop_down("cbo_supplier", 162, "select a.id, a.company_name from lib_company a where a.status_active=1 order by a.company_name", "id,company_name", 1, "-- Select --", 0, "", 1);
	exit();
}

if ($action == "load_drop_down_supplier_loan") {
	echo create_drop_down("cbo_loan_party", 170, "select c.supplier_name,c.id from lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data' and b.party_type=91 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by c.supplier_name", "id,supplier_name", 1, "-- Select --", 0, "", 0);
	exit();
}

if ($action=="load_drop_down_purpose")
{
	if($data==5) 
	{
		$arr_field_index="1,3,4,5,26,29,30,47,80";
		$select_field="";
	}
	elseif($data==6) 
	{
		$arr_field_index="1"; 
		$select_field="1";
	}
	elseif($data==9) 
	{
		$arr_field_index="7,12,15,38,46,50,51"; 
		$select_field="";
	}
	else 
	{
		$arr_field_index="8,80";
		$select_field="";
	}
	echo create_drop_down("cbo_issue_purpose", 170, $yarn_issue_purpose,"", 1, "-- Select Purpose --", $select_field, "", 0,"$arr_field_index");	 
	exit();
}

//load drop down company location done
if ($action == "load_drop_down_location") {
	$dataArr = explode("_", $data);
	$company_id=$dataArr[0];
	$knitting_source=$dataArr[1];
	if($knitting_source==1)
	{
		if($user_comp_location_ids) $user_comp_location_cond = " and id in ($user_comp_location_ids)"; else $user_comp_location_cond = "";

		echo create_drop_down("cbo_location_id", 170, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$company_id' $user_comp_location_cond order by location_name", "id,location_name", 1, "-- Select Location --", $selected, "", 0);
	}
	else
	{
		echo create_drop_down("cbo_location_id", 170, $blank_array, "", 1, "-- Select Location --", $selected, "", 0,'','','','','','',"cbo_location_id");
	}
	exit();
}

//load drop down rack self done
if ($action=="load_room_rack_self_bin")
{
	load_room_rack_self_bin("requires/yarn_issue_controller",$data);
}

//load drop down buyer done
if ($action == "load_drop_down_buyer") {
	$data = explode("_", $data);
	//if ($data[1] == 1) $party = "1,3,21,90,30"; else $party = "80";
	$party = "1,3,21,90,30";

	echo create_drop_down("cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in ($party)) group by buy.id, buy.buyer_name order by buy.buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "", $data[1]);
	exit();

}

//load drop down knitting company done
if ($action == "load_drop_down_knit_com") {
	$exDataArr = explode("**", $data);
	$knit_source = $exDataArr[0];
	$company = $exDataArr[1];
	$issuePurpose = $exDataArr[2];
	if ($company == "" || $company == 0) $company_cod = ""; else $company_cod = " and id=$company";

	if ($knit_source == 1)
		echo create_drop_down("cbo_knitting_company", 170, "select id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name", "id,company_name", 1, "-- Select --", "", "load_drop_down( 'requires/yarn_issue_controller', this.value+'_'+$knit_source, 'load_drop_down_location', 'location_td' );");
	else if ($knit_source == 3 && $issuePurpose == 1)
		echo create_drop_down("cbo_knitting_company", 170, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type in(1,9,20) and a.status_active=1 group by a.id, a.supplier_name order by a.supplier_name", "id,supplier_name", 1, "-- Select --", 0, "load_drop_down( 'requires/yarn_issue_controller', this.value+'_'+$knit_source, 'load_drop_down_location', 'location_td' );", 0);
	else if ($knit_source == 3 && $issuePurpose == 2)
		echo create_drop_down("cbo_knitting_company", 170, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type in(1,9,21,24) and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id,supplier_name", 1, "-- Select --", 0, "load_drop_down( 'requires/yarn_issue_controller', this.value+'_'+$knit_source, 'load_drop_down_location', 'location_td' );", 0);
	else if ($knit_source == 3)
		echo create_drop_down("cbo_knitting_company", 170, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id,supplier_name", 1, "-- Select --", 0, "load_drop_down( 'requires/yarn_issue_controller', this.value+'_'+$knit_source, 'load_drop_down_location', 'location_td' );", 0);
	else if ($knit_source == 0)
		echo create_drop_down("cbo_knitting_company", 170, $blank_array, "", 1, "-- Select --", $selected, "", "", "");
	exit();
}

//job_no_popup done
if($action=="job_no_popup")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	 
	<script>
		function js_set_value(str)
		{
			var splitData = str.split("_");
			//alert (str);
			$("#hide_job_id").val(splitData[0]); 
			$("#hide_job_no").val(splitData[1]);
			$("#hide_buyer_id").val(splitData[2]); 
			$("#hide_sty_ref").val(splitData[3]);  
			parent.emailwindow.hide();
		}
	
	</script>
	</head>
	<body>
	<div align="center">
		<form name="styleRef_form" id="styleRef_form">
			<fieldset style="width:600px;">
				<table width="600" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
					<thead>
						<th width="150">Buyer</th>
						<th width="70">Year</th>
						<th width="150">Search By</th>
						<th id="search_by_td_up" width="150">Please Enter Job No</th>
						<th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:80px;"></th> 
						<input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
						<input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
						<input type="hidden" name="hide_buyer_id" id="hide_buyer_id" value="" />
						<input type="hidden" name="hide_sty_ref" id="hide_sty_ref" value="" />
					</thead>
					<tbody>
						<tr class="general">
							<td align="center">
								 <? 
									echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
								?>
							</td>
							<td><? echo create_drop_down( "cbo_year", 60, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" ); ?></td>                 
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
								<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('cbo_year').value, 'create_job_no_search_list_view', 'search_div', 'yarn_issue_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:80px;" />
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
//create_job_no_search_list_view done
if($action=="create_job_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$year_id=$data[4];
	
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	if(trim($data[3])=="" && $data[1] == 0) { echo "Please Enter Buyer Name";die;}
	if($data[1]> 0)
	{
		$buyer_id_cond=" and buyer_name=$data[1]";
	}
	
	$search_by=$data[2];
	$search_string="%".trim($data[3])."%";

	if($search_by==2) $search_field="style_ref_no"; else $search_field="job_no";
	
	$arr=array (0=>$company_arr,1=>$buyer_arr);
	if($db_type==0) $insert_year="year(insert_date)";	
	if($db_type==2) $insert_year="to_char(insert_date,'yyyy')";
	
	
	if($db_type==0)
	{
		if($data[4]!=0) $year_cond=" and YEAR(insert_date)=$data[4]"; else $year_cond="";
	}
	else if($db_type==2)
	{
		$year_field_con=" and to_char(insert_date,'YYYY')";
		if($data[4]!=0) $year_cond=" $year_field_con='$data[4]'"; else $year_cond="";
	}
	
	//echo $year_cond[4];die;
	
	$sql= "select id, job_no, job_no_prefix_num, company_name, buyer_name, style_ref_no, $insert_year as year from wo_po_details_master where status_active=1 and is_deleted=0 and company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $year_cond order by job_no";
		
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No", "120,130,80,60","600","240",0, $sql , "js_set_value", "id,job_no,buyer_name,style_ref_no", "", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no", "",'','0,0,0,0,0') ;
	
   exit(); 
}

//job_no_popup done
if($action=="booking_no_popup")
{
	echo load_html_head_contents("Booking No Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	 
	<script>
		function js_set_value(str)
		{
			var splitData = str.split("_");
			//alert (splitData[0]+"=="+splitData[1]);
			$("#hide_booking_no").val(splitData[1]); 
			$("#hide_booking_id").val(splitData[0]);
			parent.emailwindow.hide();
		}
	
	</script>
	</head>
	<body>
	<div align="center">
    	<input type="hidden" name="hide_booking_no" id="hide_booking_no" value="" />
		<input type="hidden" name="hide_booking_id" id="hide_booking_id" value="" />
		<form name="styleRef_form" id="styleRef_form">
			<fieldset style="width:750px;">
				<table width="750" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
					<thead>
						<th width="200">Service Type</th>
						<th width="200">Booking No</th>
						<th width="200">Booking Date Range</th>
						<th>
                        <input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;">
						
                        </th>
					</thead>
					<tbody>
						<tr class="general">
							<td>
								<? 
									echo create_drop_down( "cbo_service_type", 160, $yarn_issue_purpose,"", 1, "-- Select --", $issue_purpose, "",1,'7,8,12,15,38,46,50,51');
								?>
							</td>
							<td><input type="text" style="width:150px" class="text_boxes_numeric" name="txt_booking_no" id="txt_booking_no" placeholder="Numeric Field" /></td>                 
							<td align="center">	
							<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date" readonly/>&nbsp;&nbsp; To &nbsp;&nbsp; 
							<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To Date" readonly/>	
							</td> 	
							<td align="center">
								<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_service_type').value+'**'+document.getElementById('txt_booking_no').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value, 'create_booking_search_list_view', 'search_div', 'yarn_issue_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
							</td>
						</tr>
                        <tr>
                            <td align="center" height="40" valign="middle" colspan="4"><? echo load_month_buttons(1); ?></td>
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
//create_job_no_search_list_view done
if($action=="create_booking_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$service_type=$data[1];
	$booking_no=trim(str_replace("'","",$data[2]));
	$date_form=$data[3];
	$date_to=$data[4];
	
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	if($service_type==8)
	{
		$sql_conds="";
		if($booking_no!="") $sql_conds .=" and wo_number_prefix_num='$booking_no'";
		if($date_form !="" && $date_to !="")
		{
			if($db_type==0) $sql_conds .=" and wo_date between '".change_date_format($date_form, 'yyyy-mm-dd')."' and '".change_date_format($date_to, 'yyyy-mm-dd')."'";
			else $sql_conds .=" and wo_date between '".change_date_format($date_form, '', '', 1)."' and '".change_date_format($date_to, '', '', 1)."'";
		}
		
		if($db_type==0) $insert_year="year(insert_date)";	
		if($db_type==2) $insert_year="to_char(insert_date,'yyyy')";
		
		
		$sql= "select id, wo_number as ydw_no, wo_number_prefix_num as yarn_dyeing_prefix_num, company_name as company_id, wo_date as booking_date, pay_mode, supplier_id, $insert_year as year from wo_non_order_info_mst where status_active=1 and is_deleted=0 and entry_form=284 and wo_basis_id=3 and company_name=$company_id $sql_conds order by yarn_dyeing_prefix_num desc";
		//echo $sql;
		$sql_result=sql_select($sql);
		$factory_arr=array();
		foreach($sql_result as $row)
		{
			if($row[csf("pay_mode")] == 5)
			{
				$factory_arr[$row[csf("id")]]=$company_arr[$row[csf("supplier_id")]];
			}
			else
			{
				$factory_arr[$row[csf("id")]]=$supplier_arr[$row[csf("supplier_id")]];
			}
		}
		//echo $sql;die;
		$arr=array (0=>$company_arr,4=>$pay_mode,5=>$factory_arr);	
		echo create_list_view("tbl_list_search", "Company,Year,Booking No,Booking Date,Pay Mode,Supplier", "130,70,70,80,110","700","280",0, $sql , "js_set_value", "id,ydw_no", "", 1, "company_id,0,0,0,pay_mode,id", $arr , "company_id,year,yarn_dyeing_prefix_num,booking_date,pay_mode,id", "",'','0,0,0,3,0,0') ;
	}
	else
	{
		$sql_conds="";
		if($booking_no!="") $sql_conds .=" and yarn_dyeing_prefix_num='$booking_no'";
		if($date_form !="" && $date_to !="")
		{
			if($db_type==0) $sql_conds .=" and booking_date between '".change_date_format($date_form, 'yyyy-mm-dd')."' and '".change_date_format($date_to, 'yyyy-mm-dd')."'";
			else $sql_conds .=" and booking_date between '".change_date_format($date_form, '', '', 1)."' and '".change_date_format($date_to, '', '', 1)."'";
		}
		
		if($db_type==0) $insert_year="year(insert_date)";	
		if($db_type==2) $insert_year="to_char(insert_date,'yyyy')";
		
		
		$sql= "select id, ydw_no, yarn_dyeing_prefix_num, company_id, booking_date, pay_mode, supplier_id, $insert_year as year from wo_yarn_dyeing_mst where status_active=1 and is_deleted=0 and entry_form=335 and company_id=$company_id and service_type=$service_type $sql_conds order by yarn_dyeing_prefix_num desc";
		$sql_result=sql_select($sql);
		$factory_arr=array();
		foreach($sql_result as $row)
		{
			if($row[csf("pay_mode")] == 3 || $row[csf("pay_mode")] == 5)
			{
				$factory_arr[$row[csf("id")]]=$company_arr[$row[csf("supplier_id")]];
			}
			else
			{
				$factory_arr[$row[csf("id")]]=$supplier_arr[$row[csf("supplier_id")]];
			}
		}
		//echo $sql;die;
		$arr=array (0=>$company_arr,4=>$pay_mode,5=>$factory_arr);	
		echo create_list_view("tbl_list_search", "Company,Year,Booking No,Booking Date,Pay Mode,Factory", "130,70,70,80,110","700","280",0, $sql , "js_set_value", "id,ydw_no", "", 1, "company_id,0,0,0,pay_mode,id", $arr , "company_id,year,yarn_dyeing_prefix_num,booking_date,pay_mode,id", "",'','0,0,0,3,0,0') ;
	}
	
	
   exit(); 
}


if ($action == "show_booking_item_list_view") 
{
	$ex_data = explode("__", $data);
	$booking_no = $ex_data[1];
	$booking_id = $ex_data[0];
	$company = $ex_data[2];
	$basis = $ex_data[3];
	$store_id = $ex_data[4];
	$issue_purpose = $ex_data[5];
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$composition_arr=return_library_array("select id,composition_name from lib_composition_array",'id','composition_name');
	$brand_arr=return_library_array("select id, brand_name from lib_brand",'id','brand_name');
	if($issue_purpose==8)
	{
		$sql = "select a.id, b.job_no, a.id as job_no_id, c.id as prod_id, c.product_name_details, c.color, c.lot, c.detarmination_id, c.unit_of_measure, c.yarn_comp_type1st, c.yarn_count_id, c.yarn_type, c.brand, c.brand_supplier, c.supplier_id, c.is_supp_comp, c.company_id, b.cons_quantity as yarn_qnty
		from wo_non_order_info_mst a, inv_transaction b, product_details_master c
		where a.wo_number=b.job_no and b.prod_id=c.id and b.transaction_type in(1,4,5) and a.id='$booking_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ";
		//echo $sql;die;
		$result = sql_select($sql);
		$all_job_no="";$all_prod_id=array();
		foreach($result as $row)
		{
			if($job_check[$row[csf("job_no")]]=="")
			{
				$job_check[$row[csf("job_no")]]=$row[csf("job_no")];
				$all_job_no.="'".$row[csf("job_no")]."',";
			}
			
			$all_prod_id[$row[csf("prod_id")]]=$row[csf("prod_id")];
		}
		$all_job_no=chop($all_job_no,",");
		if($all_job_no!="")
		{
			$stock_sql="select prod_id, job_no, sum((case when transaction_type in(1,4,5) then cons_quantity else 0 end)-(case when transaction_type in(2,3,6) then cons_quantity else 0 end)) as bal_qnty
			from inv_transaction 
			where status_active=1 and is_deleted=0 and store_id=$store_id and job_no in($all_job_no) and prod_id in(".implode(",",$all_prod_id).")
			group by prod_id, job_no";
			//echo $stock_sql;die;
			$stock_result=sql_select($stock_sql);
			$stock_data=array();
			foreach($stock_result as $row)
			{
				$stock_data[$row[csf("prod_id")]][$row[csf("job_no")]]=$row[csf("bal_qnty")];
			}
			//print_r($stock_data);
			$prev_book_rcv=sql_select("select prod_id, job_no, sum(a.cons_quantity) as prev_rcv_qnty 
			from inv_transaction a
			where a.job_no in($all_job_no) and a.pi_wo_dtls_id=$booking_id and a.prod_id in(".implode(",",$all_prod_id).") and a.status_active=1 and a.is_deleted=0 and a.item_category=1 and a.entry_form in(277) group by prod_id, job_no");
			$prev_book_rcv_data=array();
			foreach($prev_book_rcv as $row)
			{
				$prev_book_rcv_data[$row[csf("prod_id")]][$row[csf("job_no")]]=$row[csf("prev_rcv_qnty")];
			}
		}
		//$yes_no_req=count($result);
		$i = 1;
		?>
		<fieldset style="width:340px;">
			<table width="100%" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
					<th>SL</th>
					<th>Job/Wo</th>
					<th>Product</th>
					<th>Lot No</th>
					<th>Color</th>
					<th>WO Qnty</th>
				</thead>
				<tbody>
					<?
					foreach ($result as $key => $val) 
					{
						if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
						$job_no_ref=explode("-",$val[csf("job_no")]);
						$stock_qnty=$stock_data[$val[csf("prod_id")]][$val[csf("job_no")]];
						$prev_qnty=$prev_book_rcv_data[$val[csf("prod_id")]][$val[csf("job_no")]];
						$cu_wo_qnt=$val[csf("yarn_qnty")]-$prev_qnty;
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="set_form_data('<? echo $val[csf("prod_id")]."__".$val[csf("product_name_details")]."__".$unit_of_measurement[$val[csf("unit_of_measure")]]."__".$val[csf("lot")]."__".$color_arr[$val[csf("color")]]."__".$count_arr[$val[csf("yarn_count_id")]]."__".$yarn_type[$val[csf("yarn_type")]]."__".$brand_arr[$val[csf("brand")]]."__".$val[csf("brand_supplier")]."__".$stock_qnty."__".$composition_arr[$val[csf("yarn_comp_type1st")]]."__".$val[csf("yarn_comp_type1st")]."__".$val[csf("unit_of_measure")]."__".$val[csf("brand")]."__".$val[csf("supplier_id")]."__".$val[csf("job_no")]."__".$val[csf("job_no_id")]."__".$cu_wo_qnt."__".$val[csf("is_supp_comp")]."__".$val[csf("company_id")]; ?>');"
							style="cursor:pointer">
							<td width="20" align="center" title="<? echo $stock_qnty; ?>"><? echo $i; ?></td>
							<td width="30" align="center"><p><? echo $job_no_ref[2]*1; ?></p></td>
							<td width="120"><? echo $val[csf("product_name_details")]; ?></td>
							<td width="50" align="center"><p><? echo $val[csf("lot")]; ?></p></td>
							<td width="60"><? echo $color_arr[$val[csf("color")]]; ?></td>
							<td align="right" title="<? echo $stock_qnty; ?>"><? echo number_format($val[csf("yarn_qnty")],2); ?></td>
						</tr>
						<?
						$i++;
					}
					?>
				</tbody>
			</table>
		</fieldset>
		<?
	}
	else
	{
		$sql = "select a.id, a.job_no, a.job_no_id, b.id as prod_id, b.product_name_details, b.color, b.lot, b.detarmination_id, b.unit_of_measure, b.yarn_comp_type1st, b.yarn_count_id, b.yarn_type, b.brand, b.brand_supplier, b.supplier_id, b.is_supp_comp, b.company_id, a.yarn_wo_qty as yarn_qnty
		from wo_yarn_dyeing_dtls a, product_details_master b
		where a.product_id=b.id and a.mst_id='$booking_id' and a.status_active=1 and a.is_deleted=0";
		$result = sql_select($sql);
		$all_job_no="";$all_prod_id=array();
		foreach($result as $row)
		{
			if($job_check[$row[csf("job_no")]]=="")
			{
				$job_check[$row[csf("job_no")]]=$row[csf("job_no")];
				$all_job_no.="'".$row[csf("job_no")]."',";
			}
			
			$all_prod_id[$row[csf("prod_id")]]=$row[csf("prod_id")];
		}
		$all_job_no=chop($all_job_no,",");
		if($all_job_no!="")
		{
			$stock_sql="select prod_id, job_no, sum((case when transaction_type in(1,4,5) then cons_quantity else 0 end)-(case when transaction_type in(2,3,6) then cons_quantity else 0 end)) as bal_qnty
			from inv_transaction 
			where status_active=1 and is_deleted=0 and store_id=$store_id and job_no in($all_job_no) and prod_id in(".implode(",",$all_prod_id).")
			group by prod_id, job_no";
			//echo $stock_sql;
			$stock_result=sql_select($stock_sql);
			$stock_data=array();
			foreach($stock_result as $row)
			{
				$stock_data[$row[csf("prod_id")]][$row[csf("job_no")]]=$row[csf("bal_qnty")];
			}
			//print_r($stock_data);
			$prev_book_rcv=sql_select("select prod_id, job_no, sum(a.cons_quantity) as prev_rcv_qnty 
			from inv_transaction a
			where a.job_no in($all_job_no) and a.pi_wo_dtls_id=$booking_id and a.prod_id in(".implode(",",$all_prod_id).") and a.status_active=1 and a.is_deleted=0 and a.item_category=1 and a.entry_form in(277) group by prod_id, job_no");
			$prev_book_rcv_data=array();
			foreach($prev_book_rcv as $row)
			{
				$prev_book_rcv_data[$row[csf("prod_id")]][$row[csf("job_no")]]=$row[csf("prev_rcv_qnty")];
			}
		}
		//$yes_no_req=count($result);
		$i = 1;
		?>
		<fieldset style="width:340px;">
			<table width="100%" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
					<th>SL</th>
					<th>Job</th>
					<th>Product</th>
					<th>Lot No</th>
					<th>Color</th>
					<th>WO Qnty</th>
				</thead>
				<tbody>
					<?
					foreach ($result as $key => $val) 
					{
						if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
						$job_no_ref=explode("-",$val[csf("job_no")]);
						$stock_qnty=$stock_data[$val[csf("prod_id")]][$val[csf("job_no")]];
						$prev_qnty=$prev_book_rcv_data[$val[csf("prod_id")]][$val[csf("job_no")]];
						$cu_wo_qnt=$val[csf("yarn_qnty")]-$prev_qnty;
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="set_form_data('<? echo $val[csf("prod_id")]."__".$val[csf("product_name_details")]."__".$unit_of_measurement[$val[csf("unit_of_measure")]]."__".$val[csf("lot")]."__".$color_arr[$val[csf("color")]]."__".$count_arr[$val[csf("yarn_count_id")]]."__".$yarn_type[$val[csf("yarn_type")]]."__".$brand_arr[$val[csf("brand")]]."__".$val[csf("brand_supplier")]."__".$stock_qnty."__".$composition_arr[$val[csf("yarn_comp_type1st")]]."__".$val[csf("yarn_comp_type1st")]."__".$val[csf("unit_of_measure")]."__".$val[csf("brand")]."__".$val[csf("supplier_id")]."__".$val[csf("job_no")]."__".$val[csf("job_no_id")]."__".$cu_wo_qnt."__".$val[csf("is_supp_comp")]."__".$val[csf("company_id")]; ?>');"
							style="cursor:pointer">
							<td width="20" align="center" title="<? echo $stock_qnty; ?>"><? echo $i; ?></td>
							<td width="30" align="center"><p><? echo $job_no_ref[2]*1; ?></p></td>
							<td width="120"><? echo $val[csf("product_name_details")]; ?></td>
							<td width="50" align="center"><p><? echo $val[csf("lot")]; ?></p></td>
							<td width="60"><? echo $color_arr[$val[csf("color")]]; ?></td>
							<td align="right" title="<? echo $stock_qnty; ?>"><? echo number_format($val[csf("yarn_qnty")],2); ?></td>
						</tr>
						<?
						$i++;
					}
					?>
				</tbody>
			</table>
		</fieldset>
		<?
	}
	
	exit();
}
	
if ($action == "show_job_item_list_view") {
	$ex_data = explode("__", $data);
	$job_no = $ex_data[0];
	$storeId = $ex_data[1];
	$company = $ex_data[2];
	$basis = $ex_data[3];
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$composition_arr=return_library_array("select id,composition_name from lib_composition_array",'id','composition_name');
	$brand_arr=return_library_array("select id, brand_name from lib_brand",'id','brand_name');
	
	if ($job_no != '') {
		$sql = "select a.job_no, b.id as prod_id, b.product_name_details, b.color, b.lot, b.detarmination_id, b.unit_of_measure, b.yarn_comp_type1st, b.yarn_count_id, b.yarn_type, b.brand, b.brand_supplier, b.supplier_id, b.is_supp_comp, b.company_id, a.floor_id,
		a.room,
		a.rack,
		a.self, sum((case when a.transaction_type in(1,4,5) then a.cons_quantity else 0 end)-(case when a.transaction_type in(2,3,6) then a.cons_quantity else 0 end)) as yarn_qnty 
		from inv_transaction a, product_details_master b
		where a.prod_id=b.id and a.job_no='$job_no' and a.store_id=$storeId and a.company_id='$company' and a.status_active=1 and a.is_deleted=0 and a.item_category=1 and a.entry_form in(248,249,277,381,382) 
		group by a.job_no, b.id, b.product_name_details, b.color, b.lot, b.detarmination_id, b.unit_of_measure, b.yarn_comp_type1st, b.yarn_count_id, b.yarn_type, b.brand, b.brand_supplier, b.supplier_id, b.is_supp_comp, b.company_id,  a.floor_id,
		a.room,
		a.rack,
		a.self";
		
	}
	// echo $sql;
	$result = sql_select($sql);
	//$yes_no_req=count($result);
	$i = 1;
	?>
	<fieldset style="width:330px;">
		<table width="100%" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
			<thead>
				<th>SL</th>
				<th>Product</th>
				<th>Lot No</th>
				<th>Color</th>
				<th>Qnty</th>
			</thead>
			<tbody>
				<?
				foreach ($result as $key => $val) {
					if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>"
						onClick="set_form_data('<? echo $val[csf("prod_id")]."__".$val[csf("product_name_details")]."__".$unit_of_measurement[$val[csf("unit_of_measure")]]."__".$val[csf("lot")]."__".$color_arr[$val[csf("color")]]."__".$count_arr[$val[csf("yarn_count_id")]]."__".$yarn_type[$val[csf("yarn_type")]]."__".$brand_arr[$val[csf("brand")]]."__".$val[csf("brand_supplier")]."__".$val[csf("yarn_qnty")]."__".$composition_arr[$val[csf("yarn_comp_type1st")]]."__".$val[csf("yarn_comp_type1st")]."__".$val[csf("unit_of_measure")]."__".$val[csf("brand")]."__".$val[csf("supplier_id")]."________".$val[csf("is_supp_comp")]."__".$val[csf("company_id")]."__".$val[csf("floor_id")]."__".$val[csf("room")]."__".$val[csf("rack")]."__".$val[csf("self")]; ?>');"
						style="cursor:pointer">
						<td width="20"><? echo $i; ?></td>
						<td width="130"><? echo $val[csf("product_name_details")]; ?></td>
						<td width="60"><p><? echo $val[csf("lot")]; ?></p></td>
						<td width="60"><? echo $color_arr[$val[csf("color")]]; ?></td>
						<td align="right"><? echo number_format($val[csf("yarn_qnty")],2); ?></td>
					</tr>
					<?
					$i++;
				}
				?>
			</tbody>
		</table>
	</fieldset>
	<?
	exit();
}


if($action=="lot_ration_popup")
{
	echo load_html_head_contents("Cutting Info","../../../", 1, 1, '','1','');
	extract($_REQUEST);
	?>
		<script>
			function js_set_cutting_value(strCon ) 
			{
				
			document.getElementById('update_mst_id').value=strCon;
			parent.emailwindow.hide();
			}
		</script>
	</head>
	<body>
	<div align="center" style="width:100%; overflow-y:hidden;" >
	
	
	<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
				<table width="950" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
				<thead>
					<tr>                	 
						<th width="140">Company name</th>
						<th width="130">System No</th>
						<th width="130">Job No</th>
						<th width="130" style="display:none">Order No</th>
						<th width="250">Date Range</th>
						<th width="120"><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>           
					</tr>
				</thead>
				<tbody>
					  <tr class="general">                    
							<td>
								  <? 
									   echo create_drop_down( "cbo_company_name", 150, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 order by company_name","id,company_name", 0, "-- Select Company --",$company_id, "",1);
								 ?>
							</td>
						  
							<td align="center" >
									<input type="text" id="txt_cut_no" name="txt_cut_no" style="width:120px"  class="text_boxes_numeric"/>
									<input type="hidden" id="update_mst_id" name="update_mst_id" />
							</td>
							<td align="center">
								   <input name="txt_job_search" id="txt_job_search" class="text_boxes_numeric" style="width:120px"  />
							</td>
							<td align="center" style="display:none">
								   <input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:120px"  />
							</td>
							<td align="center" width="250">
								   <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:100px" placeholder="From Date" />
								   <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:100px" placeholder="To Date" />
							</td>
							<td align="center">
								   <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_cut_no').value+'_'+document.getElementById('txt_job_search').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_order_search').value+'_'+<? echo $cbo_store_name; ?>, 'lot_ration_search_list_view', 'search_div', 'yarn_issue_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />				
							</td>
					 </tr>
					 <tr>                  
						<td align="center" height="40" valign="middle" colspan="6">
							<? echo load_month_buttons(1);  ?>
						</td>
					</tr>   
				</tbody>
			 </tr>         
		  </table> 
		 <div align="center" valign="top" id="search_div"> </div>  
	  </form>
	</div>    
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}

if($action=="lot_ration_search_list_view")
{
	$ex_data = explode("_",$data);
	$company = $ex_data[0];	
	$cutting_no = $ex_data[1];
	$job_no = $ex_data[2];
	$from_date = $ex_data[3];
	$to_date = $ex_data[4];
	$cut_year= $ex_data[5];
	$order_no= $ex_data[6];
	$storeId= $ex_data[7];
	if($db_type==2) { $year_cond=" and extract(year from a.insert_date)=$cut_year"; $year=" extract(year from a.insert_date) as year ";}
	if($db_type==0) {$year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$cut_year"; $year=" SUBSTRING_INDEX(a.insert_date, '-', 1) as year ";}
	
	if(str_replace("'","",$company)==0) $conpany_cond=""; else $conpany_cond="and a.company_id=".str_replace("'","",$company)."";
	if(str_replace("'","",$cutting_no)=="") $cut_cond=""; else $cut_cond="and a.cut_num_prefix_no='".str_replace("'","",$cutting_no)."'  $year_cond";
	if(str_replace("'","",$job_no)=="") $job_cond=""; else $job_cond="and b.job_no_prefix_num='".str_replace("'","",$job_no)."'";
	//if(str_replace("'","",$order_no)=="") $order_cond=""; else $order_cond="and c.po_number like '%".trim($order_no)."%' ";
	
	if( $from_date!="" && $to_date!="" )
	{
		if($db_type==0)
		{
			$sql_cond= " and entry_date  between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";
		}
		if($db_type==2)
		{
			$sql_cond= " and entry_date  between '".change_date_format($from_date,'yyyy-mm-dd','-',1)."' and '".change_date_format($to_date,'yyyy-mm-dd','-',1)."'";
		}
	}
	
	//$sql_order="select a.id,a.cut_num_prefix_no, a.table_no, a.job_no, a.batch_id, a.entry_date, a.marker_length, a.marker_width, a.fabric_width, c.po_number, d.order_id,d.color_id,$year FROM ppl_cut_lay_mst a,wo_po_details_master b,wo_po_break_down c,ppl_cut_lay_dtls d where a.id=d.mst_id and a.job_no=b.job_no and b.job_no=c.job_no_mst and c.id=d.order_id and a.entry_form=99 $conpany_cond $cut_cond $job_cond $sql_cond $order_cond order by id";
	
	$sql_order="select a.id, a.cut_num_prefix_no, a.table_no, a.job_no, a.batch_id, a.entry_date, a.cad_marker_cons, a.marker_width, a.fabric_width, c.color_id, c.marker_qty, c.order_cut_no, $year 
	FROM ppl_cut_lay_mst a, wo_po_details_master b, ppl_cut_lay_dtls c 
	where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.mst_id=a.id and a.job_no=b.job_no and a.entry_form=253 and a.store_id=$storeId $conpany_cond $cut_cond $job_cond $sql_cond order by id";
	//echo $sql_order;die;
	$table_no_arr=return_library_array( "select id,table_no from lib_cutting_table",'id','table_no');
	$color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
	
	$arr=array(4=>$color_arr);//,4=>$order_number_arr,5=>$color_arr,Order NO,Color
	echo create_list_view("list_view", "System No,Year,Order Cut No,Job No,Color,Ratio Qty,Cons/Dzn(Lbs),Entry Date","100,70,60,90,200,100,110,120","950","270",0, $sql_order , "js_set_cutting_value", "id,job_no,cut_num_prefix_no", "", 1, "0,0,0,0,color_id,0,0,0,0", $arr, "cut_num_prefix_no,year,order_cut_no,job_no,color_id,marker_qty,cad_marker_cons,entry_date", "","setFilterGrid('list_view',-1)","0,0,0,0,0,1,2,3") ;
	exit();
}
	
if ($action == "load_php_mst_job_data") 
{
	$sql = "select job_no, buyer_name, style_ref_no from wo_po_details_master where job_no='$data' and status_active=1 and is_deleted=0";
	
	$result = sql_select($sql);
	foreach ($result as $row) {
		
		echo "$('#txt_buyer_job_no').val('" . $row[csf("job_no")] . "');\n";
		echo "$('#cbo_buyer_name').val(" . $row[csf("buyer_name")] . ");\n";
		echo "$('#txt_style_no').val('" . $row[csf("style_ref_no")] . "');\n";
	}
	exit();
} 

if ($action == "show_lot_item_list_view") {
	$ex_data = explode("__", $data);
	$job_no = $ex_data[0];
	$storeId = $ex_data[1];
	$company = $ex_data[2];
	$basis = $ex_data[3];
	$lot_ration_id = $ex_data[4];
	
	if($basis==5 || $basis==9 || $basis==10)
	{
		$lot_prev_iss_sql=sql_select("select a.prod_id, sum(a.cons_quantity) as cons_quantity from inv_transaction a where a.job_no='$job_no' and a.store_id=$storeId and a.company_id='$company' and a.status_active=1 and a.is_deleted=0 and a.item_category=1 and a.entry_form in(277) and a.transaction_type=2 and a.receive_basis=6 group by a.prod_id");
	}
	else
	{
		$lot_prev_iss_sql=sql_select("select a.prod_id, sum(a.cons_quantity) as cons_quantity from inv_transaction a where a.pi_wo_batch_no=$lot_ration_id and a.store_id=$storeId and a.status_active=1 and a.is_deleted=0 and a.item_category=1 and a.entry_form in(277) and a.transaction_type=2 and a.receive_basis=6 group by a.prod_id");
	}
	
	$lot_prev_iss_arr=array();
	foreach($lot_prev_iss_sql as $row)
	{
		$lot_prev_iss_arr[$row[csf("prod_id")]]=$row[csf("cons_quantity")];
	}
	
	//echo $lot_ration_id;die;

	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$composition_arr=return_library_array("select id,composition_name from lib_composition_array",'id','composition_name');
	$brand_arr=return_library_array("select id, brand_name from lib_brand",'id','brand_name');
	
	$sql_transaction = sql_select("select a.prod_id, a.job_no, a.store_id, sum((case when a.transaction_type in(1,4,5) then a.cons_quantity else 0 end)-(case when a.transaction_type in(2,3,6) then a.cons_quantity else 0 end)) as yarn_qnty 
	from inv_transaction a
	where a.job_no='$job_no' and a.store_id=$storeId and a.company_id='$company' and a.status_active=1 and a.is_deleted=0 and a.item_category=1 and a.entry_form in(248,249,277,381,382) 
	group by a.prod_id, a.job_no, a.store_id");
	
	$stock_data=array();
	foreach ($sql_transaction as $row) {
		$stock_data[$row[csf("store_id")]][$row[csf("prod_id")]][$row[csf("job_no")]]+=$row[csf("yarn_qnty")];
	}
	

	if ($job_no != '') {
		$sql = "select a.id as lot_id, a.job_no, a.store_id, c.id as prod_id, c.product_name_details, c.color, c.lot, c.detarmination_id, c.unit_of_measure, c.yarn_comp_type1st, c.yarn_count_id, c.yarn_type, c.brand, c.brand_supplier, c.supplier_id, c.is_supp_comp, c.company_id, sum(b.alocated_qty) as yarn_qnty
		from ppl_cut_lay_mst a, ppl_cut_lay_prod_dtls b, product_details_master c 
		where a.id=b.mst_id and b.prod_id=c.id and a.id='$lot_ration_id' and a.company_id='$company' and a.store_id=$storeId and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
		group by a.id, a.job_no, a.store_id, c.id, c.product_name_details, c.color, c.lot, c.detarmination_id, c.unit_of_measure, c.yarn_comp_type1st, c.yarn_count_id, c.yarn_type, c.brand, c.brand_supplier, c.supplier_id, c.is_supp_comp, c.company_id";
	}
	//echo $sql;//die;
	$result = sql_select($sql);
//$yes_no_req=count($result);
	$i = 1;
	?>
	<fieldset style="width:330px;">
		<table width="100%" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
			<thead>
				<th>SL</th>
				<th>Product</th>
				<th>Lot No</th>
				<th>Color</th>
				<th>Qnty</th>
			</thead>
			<tbody>
				<?
				foreach ($result as $key => $val) 
				{
					if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
					$stock_qnty=$stock_data[$val[csf("store_id")]][$val[csf("prod_id")]][$val[csf("job_no")]];
					$lot_bal_qnty=$val[csf("yarn_qnty")]-$lot_prev_iss_arr[$val[csf("prod_id")]];
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick="set_form_data('<? echo $val[csf("prod_id")]."__".$val[csf("product_name_details")]."__".$unit_of_measurement[$val[csf("unit_of_measure")]]."__".$val[csf("lot")]."__".$color_arr[$val[csf("color")]]."__".$count_arr[$val[csf("yarn_count_id")]]."__".$yarn_type[$val[csf("yarn_type")]]."__".$brand_arr[$val[csf("brand")]]."__".$val[csf("brand_supplier")]."__".$stock_qnty."__".$composition_arr[$val[csf("yarn_comp_type1st")]]."__".$val[csf("yarn_comp_type1st")]."__".$val[csf("unit_of_measure")]."__".$val[csf("brand")]."__".$val[csf("supplier_id")]."______".$lot_bal_qnty."__".$val[csf("is_supp_comp")]."__".$val[csf("company_id")]; ?>');" style="cursor:pointer">
						<td width="20"><? echo $i; ?></td>
						<td width="130"><? echo $val[csf("product_name_details")]; ?></td>
						<td width="60"><p><? echo $val[csf("lot")]; ?></p></td>
						<td width="60"><? echo $color_arr[$val[csf("color")]]; ?></td>
						<td align="right" title="<?= $lot_bal_qnty; ?>"><? echo number_format($val[csf("yarn_qnty")],2); ?></td>
					</tr>
					<?
					$i++;
				}
				?>
			</tbody>
		</table>
	</fieldset>
	<?
	exit();
}

//data save update delete here------------------------------//
if ($action == "save_update_delete") 
{
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));
	$con = connect();
	if ($db_type == 0) mysql_query("BEGIN");

	$max_recv_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$txt_prod_id and transaction_type in (1,4,5) and store_id = $cbo_store_name  and status_active = 1", "max_date");
	$max_recv_date = date("Y-m-d", strtotime($max_recv_date));
	$issue_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_issue_date)));
	if ($issue_date < $max_recv_date)
	{
		echo "20**Issue Date Can not Be Less Than Last Receive Date Of This Lot";
		die;
	}

	// check variable settings if allocation is available or not
	//$variable_set_allocation = return_field_value("allocation", "variable_settings_inventory", "company_name=$cbo_company_id and variable_list=18 and item_category_id = 1");
	//---------------Check Duplicate product in Same return number ------------------------//
	
	$trans_id=str_replace("'","",$update_id);
	$up_cond="";
	if($trans_id!="") $up_cond=" and b.id <> $trans_id";
	$duplicate = is_duplicate_field("b.id", "inv_issue_master a, inv_transaction b", "a.id=b.mst_id and a.entry_form=277 and b.entry_form=277 and a.issue_number=$txt_system_no and b.prod_id=$txt_prod_id and b.transaction_type=2 and a.status_active=1 and b.status_active=1 $up_cond");
	if ($duplicate == 1 && str_replace("'", "", $txt_system_no) != "") {
		echo "20**Duplicate Product is Not Allow in Same Issue Number.";
		disconnect($con);
		die;
	}
	$storeId=str_replace("'","",$cbo_store_name);
	$job_no=str_replace("'","",$txt_buyer_job_no);
	$company=str_replace("'","",$cbo_company_id);
	$up_cond="";
	if($trans_id!="") $up_cond=" and a.id <> $trans_id";
	$sql_transaction = sql_select("select sum((case when a.transaction_type in(1,4,5) then a.cons_quantity else 0 end)-(case when a.transaction_type in(2,3,6) then a.cons_quantity else 0 end)) as yarn_qnty 
	from inv_transaction a
	where a.job_no='$job_no' and a.store_id=$storeId and a.company_id='$company' and a.prod_id=$txt_prod_id and a.status_active=1 and a.is_deleted=0 and a.item_category=1 and a.entry_form in(248,249,277,381,382) $up_cond");
	$store_stock_quantity=$sql_transaction[0][csf("yarn_qnty")]*1;
	$issue_quantity=str_replace("'","",$txt_issue_qnty)*1;
	
	//product master table information
	$sql = sql_select("select supplier_id, avg_rate_per_unit, current_stock, stock_value, allocated_qnty, available_qnty from product_details_master where id=$txt_prod_id and item_category_id=1");
	$avg_rate = $stock_qnty = $stock_value = $allocated_qnty = $available_qnty = 0;
	$supplier_id_for_tran = '';
	foreach ($sql as $result) {
		$avg_rate = $result[csf("avg_rate_per_unit")];
		$stock_qnty = $result[csf("current_stock")];
		$stock_value = $result[csf("stock_value")];
		$allocated_qnty = $result[csf("allocated_qnty")];
		$available_qnty = $result[csf("available_qnty")];
		$supplier_id_for_tran = $result[csf("supplier_id")];
	}
	$prev_iss_qnty=0;
	if($trans_id!="")
	{
		$prev_issue_sql=sql_select("select cons_quantity from inv_transaction where status_active=1 and id=$trans_id");
		$prev_iss_qnty=$prev_issue_sql[0][csf("cons_quantity")];
	}
	if(str_replace("'","",$cbo_basis)==5 || str_replace("'","",$cbo_basis)==9 || str_replace("'","",$cbo_basis)==10)
	{
		$job_lot_id=str_replace("'","",$hide_job_id);
		$allocated_qnty_balance=$allocated_qnty;
		$available_qnty_balance=$available_qnty-$issue_quantity;
		$lot_prev_iss=sql_select("select sum(a.cons_quantity) as cons_quantity from inv_transaction a where a.job_no='$job_no' and a.store_id=$storeId and a.company_id='$company' and a.prod_id=$txt_prod_id and a.status_active=1 and a.is_deleted=0 and a.item_category=1 and a.entry_form in(277) and a.transaction_type=2 and a.receive_basis in(5,9,10) $up_cond");

		$lot_ratio_sql=sql_select("select sum(b.alocated_qty) as alocated_qty  from ppl_cut_lay_mst a,  ppl_cut_lay_prod_dtls b
		where a.status_active=1 and b.status_active=1 and a.id=b.mst_id and a.entry_form=253 and a.job_no='$job_no' and a.store_id=$storeId and a.company_id=$company and b.prod_id=$txt_prod_id");

		$lot_ratio_qnty=($lot_ratio_sql[0][csf("alocated_qty")]-$lot_prev_iss[0][csf("cons_quantity")])*1;
		$store_cu_stock=$store_stock_quantity-$lot_ratio_qnty; // ## store stock - pending allocation qnty;
		//echo "31**".$issue_quantity .">". $cu_available_qnty .">".$store_cu_stock; die();  Available Qnty = $cu_available_qnty \n
		 
		if($issue_quantity > $store_cu_stock)
		{
			echo "31**Issue quantity can not exceed available quantity or store stock quantity. \n Issue qnty = $issue_quantity \n job against store stock = $store_stock_quantity \n  cumilative allocation = $lot_ratio_qnty \n Available (store stock - cumilative allocation) = $store_cu_stock ";die;
		}
	}
	elseif(str_replace("'","",$cbo_basis)==6)
	{
		$lot_prev_iss=sql_select("select sum(a.cons_quantity) as cons_quantity from inv_transaction a where a.pi_wo_batch_no=$txt_lot_ratio_id and a.prod_id=$txt_prod_id and a.status_active=1 and a.is_deleted=0 and a.item_category=1 and a.entry_form in(277) and a.transaction_type=2 and a.receive_basis=6 $up_cond");
		$lot_ratio_sql=sql_select("select sum(b.alocated_qty) as alocated_qty  from ppl_cut_lay_mst a,  ppl_cut_lay_prod_dtls b
		where a.status_active=1 and b.status_active=1 and a.id=b.mst_id and a.entry_form=253 and a.id=$txt_lot_ratio_id and b.prod_id=$txt_prod_id");
		$lot_ratio_qnty=($lot_ratio_sql[0][csf("alocated_qty")]-$lot_prev_iss[0][csf("cons_quantity")])*1;
		$job_lot_id=str_replace("'","",$txt_lot_ratio_id);
		$allocated_qnty_balance=$allocated_qnty-$issue_quantity;
		$available_qnty_balance=$available_qnty;
		if( ($issue_quantity > $store_stock_quantity) || ($issue_quantity > $lot_ratio_qnty) )
		{
			echo "31**Issue quantity can not exceed allocated quantity or store stock quantity.";die;
		}
	}
	//echo "31**test";die;
	if ($operation == 0) // Insert Here----------------------------------------------------------
	{
		//yarn issue master table entry here START---------------------------------------//
		if (str_replace("'", "", $txt_system_no) == "") //new insert cbo_ready_to_approved
		{
			if ($db_type == 0) $year_cond = "YEAR(insert_date)";
			else if ($db_type == 2) $year_cond = "to_char(insert_date,'YYYY')";
			else $year_cond = "";//defined Later

			$id = return_next_id_by_sequence("INV_ISSUE_MASTER_PK_SEQ", "inv_issue_master", $con);
			$new_mrr_number = explode("*", return_next_id_by_sequence("INV_ISSUE_MASTER_PK_SEQ", "inv_issue_master",$con,1,$cbo_company_id,'SYIS',277,date("Y",time()),1 ));

			$field_array_mst = "id, issue_number_prefix, issue_number_prefix_num, issue_number, issue_basis, issue_purpose, entry_form, item_category, company_id, location_id, supplier_id, store_id, buyer_id, buyer_job_no, style_ref, issue_date, sample_type, knit_dye_source, knit_dye_company, challan_no, loan_party, remarks,ready_to_approve, inserted_by, insert_date";
			
			$data_array_mst = "(" . $id . ",'" . $new_mrr_number[1] . "','" . $new_mrr_number[2] . "','" . $new_mrr_number[0] . "'," . $cbo_basis . "," . $cbo_issue_purpose . ",277,1," . $cbo_company_id . "," . $cbo_location_id . "," . $cbo_supplier . "," . $cbo_store_name . "," . $cbo_buyer_name . "," . $txt_buyer_job_no . "," . $txt_style_no . "," . $txt_issue_date . "," . $cbo_sample_type . "," . $cbo_knitting_source . "," . $cbo_knitting_company . "," . $txt_challan_no . "," . $cbo_loan_party . "," . $txt_remarks . "," . $cbo_ready_to_approved . ",'" . $user_id . "','" . $pc_date_time . "')";
		} 
		else //update
		{
			$new_mrr_number[0] = str_replace("'", "", $txt_system_no);
			$id = return_field_value("id", "inv_issue_master", "issue_number=$txt_system_no");
			$field_array_mst = "issue_purpose*location_id*supplier_id*store_id*buyer_id*buyer_job_no*style_ref*issue_date*sample_type*knit_dye_source*knit_dye_company*challan_no*loan_party*remarks*ready_to_approve*updated_by*update_date";
			$data_array_mst = "" . $cbo_issue_purpose . "*" . $cbo_location_id . "*" . $cbo_supplier . "*" . $cbo_store_name . "*" . $cbo_buyer_name . "*" . $txt_buyer_job_no . "*" . $txt_style_no . "*" . $txt_issue_date . "*" . $cbo_sample_type . "*" . $cbo_knitting_source . "*" . $cbo_knitting_company . "*" . $txt_challan_no . "*" . $cbo_loan_party . "*" . $txt_remarks . "*" . $cbo_ready_to_approved . "*'" . $user_id . "'*'" . $pc_date_time . "'";
			$id = str_replace("'", "", $update_id_mst);
		}
		//yarn issue master table entry here END---------------------------------------//
		
		//inventory TRANSACTION table data entry START----------------------------------------------------------//
		//$transaction_type=array(1=>"Receive",2=>"Issue",3=>"Receive Return",4=>"Issue Return");
		$txt_issue_qnty = str_replace("'", "", $txt_issue_qnty);
		$issue_stock_value = $avg_rate * $txt_issue_qnty;
		$transactionID = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
		$field_array_trans = "id,mst_id,requisition_no,pi_wo_batch_no,receive_basis,company_id,supplier_id,prod_id,dyeing_color_id,item_category,transaction_type,transaction_date,store_id,brand_id,cons_uom,cons_quantity,return_qnty,item_return_qty,cons_rate,cons_amount,no_of_bags,cone_per_bag,weight_per_bag,weight_per_cone,room,rack,self,floor_id,using_item,job_no,buyer_id,style_ref_no,inserted_by,insert_date,entry_form,booking_no,pi_wo_dtls_id";
		
		$data_array_trans = "(" . $transactionID . "," . $id . "," . $txt_lot_ratio . ",'" . $job_lot_id . "'," . $cbo_basis . "," . $cbo_company_id . "," . $cbo_supplier . "," . $txt_prod_id . "," . $cbo_dyeing_color . ",1,2," . $txt_issue_date . "," . $cbo_store_name . "," . $cbo_brand_id . "," . $cbo_uom_id . "," . $txt_issue_qnty . "," . $txt_returnable_qty . "," . $txt_returnable_qty . "," . $avg_rate . "," . $issue_stock_value . "," . $txt_no_bag . "," . $txt_no_cone . "," . $txt_weight_per_bag . "," . $txt_weight_per_cone . "," . $cbo_room . "," . $txt_rack . "," . $txt_shelf . "," . $cbo_floor . "," . $cbo_item . "," . $txt_buyer_job_no . "," . $cbo_buyer_name . "," . $txt_style_no . ",'" . $user_id . "','" . $pc_date_time . "',277," . $txt_booking_no . "," . $hide_booking_id . ")";
		
		
		
		//weighted and average rate START here------------------------//
		//product master table data UPDATE START----------------------//
		$currentStock = $stock_qnty - $issue_quantity;
		$StockValue = $stock_value - ($txt_issue_qnty * $avg_rate);
		$field_array_prod = "last_issued_qnty*current_stock*stock_value*allocated_qnty*available_qnty*updated_by*update_date";//*avg_rate_per_unit*
		$data_array_prod = "" . $txt_issue_qnty . "*" . $currentStock . "*" . number_format($StockValue, $dec_place[4], '.', '') . "*" . $allocated_qnty_balance . "*" . $available_qnty_balance . "*'" . $user_id . "'*'" . $pc_date_time . "'";
		//*".$allocated_qnty_balance."*".$available_qnty_balance."$avgRate."*".
		//$prodUpdate 	= sql_update("product_details_master",$field_array_prod,$data_array_prod,"id",$txt_prod_id,0);

		//------------------ product_details_master END--------------//
		//weighted and average rate END here-------------------------//


		//inventory TRANSACTION table data entry  END----------------------------------------------------------//
		//if LIFO/FIFO then START -----------------------------------------//
		$field_array = "id,recv_trans_id,issue_trans_id,entry_form,prod_id,issue_qnty,rate,amount,inserted_by,insert_date";
		$update_array = "balance_qnty*balance_amount*updated_by*update_date";
		$cons_rate = 0;
		$data_array = "";
		$updateID_array = array();
		$update_data = array();
		$issueQnty = $txt_issue_qnty;
		// check variable settings issue method(LIFO/FIFO)
		$isLIFOfifo = '';
		$check_allocation = '';
		$sql_variable = sql_select("select store_method, allocation, variable_list from variable_settings_inventory where company_name=$cbo_company_id and variable_list in(17) and item_category_id=1 and status_active=1 and is_deleted=0");
		foreach ($sql_variable as $row) {
			$isLIFOfifo = $row[csf('store_method')];
		}

		if ($isLIFOfifo == 2) $cond_lifofifo = " DESC"; else $cond_lifofifo = " ASC";

		// Trans type: 1=>"Receive",4=>"Issue Return",5=>"Item Transfer Receive"
		$sql = sql_select("select id, cons_rate, balance_qnty, balance_amount from inv_transaction where prod_id=$txt_prod_id and store_id=$cbo_store_name and balance_qnty>0 and transaction_type in (1,4,5) and item_category=1 and status_active=1 order by transaction_date,id $cond_lifofifo");
		foreach ($sql as $result) {
			$recv_trans_id = $result[csf("id")]; // this row will be updated
			$balance_qnty = $result[csf("balance_qnty")];
			$balance_amount = $result[csf("balance_amount")];
			$cons_rate = $result[csf("cons_rate")];
			$issueQntyBalance = $balance_qnty - $issueQnty; // minus issue qnty
			$issueStockBalance = $balance_amount - ($issueQnty * $cons_rate);
			if ($issueQntyBalance >= 0) {
				$amount = $issueQnty * $cons_rate;
				//for insert
				$mrrWiseIsID = return_next_id_by_sequence("INV_MRR_WISE_ISSUE_PK_SEQ", "inv_mrr_wise_issue_details", $con);
				if ($data_array != "") $data_array .= ",";
				$data_array .= "(" . $mrrWiseIsID . "," . $recv_trans_id . "," . $transactionID . ",277," . $txt_prod_id . "," . $issueQnty . "," . $cons_rate . "," . $amount . ",'" . $user_id . "','" . $pc_date_time . "')";
				//for update
				$updateID_array[] = $recv_trans_id;
				$update_data[$recv_trans_id] = explode("*", ("" . $issueQntyBalance . "*" . $issueStockBalance . "*'" . $user_id . "'*'" . $pc_date_time . "'"));
				break;
			} else if ($issueQntyBalance < 0) {
				//$issueQntyBalance = $balance_qnty+$issueQntyBalance; // adjust issue qnty
				//$issueQntyBalance = $issueQntyBalance-$balance_qnty;
				$issueQntyBalance = $issueQnty - $balance_qnty;
				$issueQnty = $balance_qnty;
				$amount = $issueQnty * $cons_rate;

				//for insert
				$mrrWiseIsID = return_next_id_by_sequence("INV_MRR_WISE_ISSUE_PK_SEQ", "inv_mrr_wise_issue_details", $con);
				if ($data_array != "") $data_array .= ",";
				$data_array .= "(" . $mrrWiseIsID . "," . $recv_trans_id . "," . $transactionID . ",277," . $txt_prod_id . "," . $balance_qnty . "," . $cons_rate . "," . $amount . ",'" . $user_id . "','" . $pc_date_time . "')";
				//for update
				$updateID_array[] = $recv_trans_id;
				$update_data[$recv_trans_id] = explode("*", ("0*0*'" . $user_id . "'*'" . $pc_date_time . "'"));
				$issueQnty = $issueQntyBalance;
			}
		}//end foreach
		// LIFO/FIFO then END-----------------------------------------------//
		$rID = $transID = $mrrWiseIssueID = $upTrID = $prodUpdate = true;
		//echo "20**".$rID." && ".$transID." && ".$prodUpdate." && ".$proportQ;
		if (str_replace("'", "", $txt_system_no) == "") {
			$rID = sql_insert("inv_issue_master", $field_array_mst, $data_array_mst, 0);
		} else {
			$rID = sql_update("inv_issue_master", $field_array_mst, $data_array_mst, "id", $id, 0);
		}
		
		$transID = sql_insert("inv_transaction", $field_array_trans, $data_array_trans, 0);
		
		if ($data_array != "") {
			$mrrWiseIssueID = sql_insert("inv_mrr_wise_issue_details", $field_array, $data_array, 0);
		}

		//transaction table stock update here------------------------//
		if (count($updateID_array) > 0) {
			$upTrID = execute_query(bulk_update_sql_statement("inv_transaction", "id", $update_array, $update_data, $updateID_array));
		}
		
		$prodUpdate = sql_update("product_details_master", $field_array_prod, $data_array_prod, "id", $txt_prod_id, 0);

		//echo "10**INSERT INTO inv_transaction (".$field_array_trans.") VALUES ".$data_array_trans.""; die;
		//echo "10** $rID = $transID = $mrrWiseIssueID = $upTrID = $prodUpdate";oci_rollback($con);die;
		if ($db_type == 0) {
			if ($rID && $transID && $prodUpdate && $mrrWiseIssueID && $upTrID) {
				mysql_query("COMMIT");
				echo "0**" . $new_mrr_number[0] . "**" . $id;
			} else {
				mysql_query("ROLLBACK");
				echo "10**" . $new_mrr_number[0] . "**" . $id;
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($rID && $transID && $prodUpdate && $mrrWiseIssueID && $upTrID) {
				oci_commit($con);
				echo "0**" . $new_mrr_number[0] . "**" . $id;
			} else {
				oci_rollback($con);
				echo "10**0";
			}
		}
		disconnect($con);
		die;
	} 
	else if ($operation == 1) // Update Here----------------------------------------------------------
	{
		$isLIFOfifo = '';
		$sql_variable = sql_select("select store_method,allocation,variable_list from variable_settings_inventory where company_name=$cbo_company_id and variable_list in(17) and item_category_id=1 and status_active=1 and is_deleted=0");
		foreach ($sql_variable as $row) {
			$isLIFOfifo = $row[csf('store_method')];
		}

		//****************************************** BEFORE ENTRY ADJUST START *****************************************//
		//product master table information

		$sql = sql_select("select a.id,a.avg_rate_per_unit,a.current_stock,a.stock_value, b.cons_quantity, b.cons_amount,a.allocated_qnty,a.available_qnty from product_details_master a, inv_transaction b where a.id=b.prod_id and b.id=$update_id and a.item_category_id=1 and b.item_category=1 and b.transaction_type=2 and b.status_active=1");
		$before_prod_id = $before_issue_qnty = $before_stock_qnty = $before_stock_value = 0;
		foreach ($sql as $result) {
			$before_prod_id = $result[csf("id")];
			$before_stock_qnty = $result[csf("current_stock")];
			$before_stock_value = $result[csf("stock_value")];
			//before quantity and stock value
			$before_avg_rate = $result[csf("avg_rate_per_unit")];
			$before_issue_qnty = $result[csf("cons_quantity")];
			$before_issue_value = $result[csf("cons_amount")];
			$before_allocated_qnty = $result[csf("allocated_qnty")];
			$before_available_qnty = $result[csf("available_qnty")];
		}
		//current product ID
		$txt_prod_id = str_replace("'", "", $txt_prod_id);
		$txt_issue_qnty = str_replace("'", "", $txt_issue_qnty);
		
		$sql = sql_select("select supplier_id, avg_rate_per_unit,current_stock,stock_value,allocated_qnty,available_qnty from product_details_master where id=$txt_prod_id and item_category_id=1");
		$curr_avg_rate = $curr_stock_qnty = $curr_stock_value = $allocated_qnty = $available_qnty = 0;
		$supplier_id_for_tran = '';
		foreach ($sql as $result) {
			$curr_avg_rate = $result[csf("avg_rate_per_unit")];
			$curr_stock_qnty = $result[csf("current_stock")];
			$curr_stock_value = $result[csf("stock_value")];
			$allocated_qnty = $result[csf("allocated_qnty")];
			$available_qnty = $result[csf("available_qnty")];
			$supplier_id_for_tran = $result[csf("supplier_id")];
		}
		$issue_stock_value=$txt_issue_qnty*$curr_avg_rate;
		$field_array_mst = "issue_purpose*location_id*supplier_id*store_id*buyer_id*buyer_job_no*style_ref*issue_date*sample_type*knit_dye_source*knit_dye_company*challan_no*loan_party*remarks*ready_to_approve*updated_by*update_date";
		$data_array_mst = "" . $cbo_issue_purpose . "*" . $cbo_location_id . "*" . $cbo_supplier . "*" . $cbo_store_name . "*" . $cbo_buyer_name . "*" . $txt_buyer_job_no . "*" . $txt_style_no . "*" . $txt_issue_date . "*" . $cbo_sample_type . "*" . $cbo_knitting_source . "*" . $cbo_knitting_company . "*" . $txt_challan_no . "*" . $cbo_loan_party . "*" . $txt_remarks . "*" . $cbo_ready_to_approved . "*'" . $user_id . "'*'" . $pc_date_time . "'";
		
		$field_array_trans = "requisition_no*pi_wo_batch_no*supplier_id*prod_id*dyeing_color_id*transaction_date*store_id*brand_id*cons_uom*cons_quantity*return_qnty*item_return_qty*cons_rate*cons_amount*no_of_bags*cone_per_bag*weight_per_bag*weight_per_cone*floor_id*room*rack*self*using_item*job_no*buyer_id*style_ref_no*updated_by*update_date*booking_no*pi_wo_dtls_id";
		$data_array_trans = "" . $txt_lot_ratio . "*" . $job_lot_id . "*" . $cbo_supplier . "*" . $txt_prod_id . "*" . $cbo_dyeing_color . "*" . $txt_issue_date . "*" . $cbo_store_name . "*" . $cbo_brand_id . "*" . $cbo_uom_id . "*" . $txt_issue_qnty . "*" . $txt_returnable_qty . "*" . $txt_returnable_qty . "*" . $curr_avg_rate . "*" . $issue_stock_value . "*" . $txt_no_bag . "*" . $txt_no_cone . "*" . $txt_weight_per_bag . "*" . $txt_weight_per_cone . "*" . $cbo_floor . "*" . $cbo_room . "*" . $txt_rack . "*" . $txt_shelf . "*" . $cbo_item . "*" . $txt_buyer_job_no . "*" . $cbo_buyer_name . "*" . $txt_style_no . "*'" . $user_id . "'*'" . $pc_date_time . "'*" . $txt_booking_no . "*" . $hide_booking_id . "";
		
		if($before_prod_id==$txt_prod_id)
		{
			$currentStock = (($curr_stock_qnty+$before_issue_qnty) - $txt_issue_qnty);
			$currentStockValue = $currentStock*$curr_avg_rate;
			if(str_replace("'","",$cbo_basis)==5)
			{
				$cu_allocated_qnty_balance=$allocated_qnty_balance;
				$cu_available_qnty_balance=$available_qnty_balance+$before_issue_qnty;
			}
			else
			{
				$cu_allocated_qnty_balance=$allocated_qnty_balance+$before_issue_qnty;
				$cu_available_qnty_balance=$available_qnty_balance;
			}
			$field_array_prod = "last_issued_qnty*current_stock*stock_value*allocated_qnty*available_qnty*updated_by*update_date";
			$data_array_prod = "" . $txt_issue_qnty . "*" . $currentStock . "*" . number_format($currentStockValue, $dec_place[4], '.', '') . "*" . $cu_allocated_qnty_balance . "*" . $cu_available_qnty_balance . "*'" . $user_id . "'*'" . $pc_date_time . "'";
		}
		else
		{
			$prevProdStock=$before_stock_qnty+$before_issue_qnty;
			$prevProdStockValue=$prevProdStock*$before_avg_rate;
			if(str_replace("'","",$cbo_basis)==5)
			{
				$before_allocated_qnty_balance=$before_allocated_qnty;
				$before_available_qnty_balance=$before_available_qnty+$before_issue_qnty;
			}
			else
			{
				$before_allocated_qnty_balance=$before_allocated_qnty+$before_issue_qnty;
				$before_available_qnty_balance=$before_available_qnty;
			}
			
			$field_array_prod = "last_issued_qnty*current_stock*stock_value*allocated_qnty*available_qnty*updated_by*update_date";
			$prodID_array[] = $before_prod_id;
			$data_array_prod[$before_prod_id] = explode("*", ("" . $before_issue_qnty . "*" . $prevProdStock . "*" . number_format($prevProdStockValue, $dec_place[4], '.', '') . "*" . $before_allocated_qnty_balance . "*" . $before_available_qnty_balance . "*'" . $user_id . "'*'" . $pc_date_time  . "'"));
			//$data_array_prod_before = "" . $before_issue_qnty . "*" . $prevProdStock . "*" . number_format($prevProdStockValue, $dec_place[4], '.', '') . "*" . $before_allocated_qnty_balance . "*" . $before_available_qnty_balance . "*'" . $user_id . "'*'" . $pc_date_time . "'";
			
			$currentStock = ($curr_stock_qnty - $txt_issue_qnty);
			$currentStockValue = $currentStock*$curr_avg_rate;
			//$field_array_prod = "last_issued_qnty*current_stock*stock_value*allocated_qnty*available_qnty*updated_by*update_date";
			$prodID_array[] = $txt_prod_id;
			$data_array_prod[$txt_prod_id] = explode("*", ("" . $txt_issue_qnty . "*" . $currentStock . "*" . number_format($currentStockValue, $dec_place[4], '.', '') . "*" . $allocated_qnty_balance . "*" . $available_qnty_balance . "*'" . $user_id . "'*'" . $pc_date_time  . "'"));
			//$data_array_prod = "" . $txt_issue_qnty . "*" . $currentStock . "*" . number_format($currentStockValue, $dec_place[4], '.', '') . "*" . $allocated_qnty_balance . "*" . $available_qnty_balance . "*'" . $user_id . "'*'" . $pc_date_time . "'";
			
		}
		
		//transaction table balance START--------------------------//
		$trans_data_array = array();
		$update_array_trans = "balance_qnty*balance_amount*updated_by*update_date";
		$sql = sql_select("select a.id, a.balance_qnty, a.balance_amount, b.issue_qnty, b.rate, b.amount, b.recv_trans_id, b.issue_trans_id from inv_transaction a, inv_mrr_wise_issue_details b where a.id=b.recv_trans_id and b.issue_trans_id=$update_id and b.entry_form=277 and a.item_category=1");
		$updateID_array_trans = array();
		$update_data_trans = array();
		foreach ($sql as $result) {
			$adjBalance = $result[csf("balance_qnty")] + $result[csf("issue_qnty")];
			$adjAmount = $result[csf("balance_amount")] + $result[csf("amount")];
			$updateID_array_trans[] = $result[csf("id")];
			$update_data_trans[$result[csf("id")]] = explode("*", ("" . $adjBalance . "*" . $adjAmount . "*'" . $user_id . "'*'" . $pc_date_time . "'"));

			$trans_data_array[$result[csf("id")]]['qnty'] = $adjBalance;
			$trans_data_array[$result[csf("id")]]['amnt'] = $adjAmount;
		}
		
		//if LIFO/FIFO then START -----------------------------------------//
		$field_array = "id,recv_trans_id,issue_trans_id,entry_form,prod_id,issue_qnty,rate,amount,inserted_by,insert_date";
		$update_array = "balance_qnty*balance_amount*updated_by*update_date";
		$cons_rate = 0;
		$data_array = "";
		$updateID_array = array();
		$update_data = array();
		$issueQnty = $txt_issue_qnty;


		if ($isLIFOfifo == 2) $cond_lifofifo = " DESC"; else $cond_lifofifo = " ASC";
		if ($before_prod_id == $txt_prod_id) $balance_cond = " and( balance_qnty>0 or id in($recv_trans_id))";
		else $balance_cond = " and balance_qnty>0";
		$sql = sql_select("select id, cons_rate, balance_qnty, balance_amount from inv_transaction where prod_id=$txt_prod_id and store_id=$cbo_store_name $balance_cond and transaction_type in (1,4,5) and item_category=1 order by transaction_date, id $cond_lifofifo");
		foreach ($sql as $result) {
			$issue_trans_id = $result[csf("id")]; // this row will be updated
			if ($trans_data_array[$issue_trans_id]['qnty'] == "") {
				$balance_qnty = $result[csf("balance_qnty")];
				$balance_amount = $result[csf("balance_amount")];
			} else {
				$balance_qnty = $trans_data_array[$issue_trans_id]['qnty'];
				$balance_amount = $trans_data_array[$issue_trans_id]['amnt'];
			}

			$cons_rate = $result[csf("cons_rate")];
			$issueQntyBalance = $balance_qnty - $issueQnty; // minus issue qnty
			$issueStockBalance = $balance_amount - ($issueQnty * $cons_rate);
			if ($issueQntyBalance >= 0) {
				$amount = $issueQnty * $cons_rate;
				//for insert
				$mrrWiseIsID = return_next_id_by_sequence("INV_MRR_WISE_ISSUE_PK_SEQ", "inv_mrr_wise_issue_details", $con);
				if ($data_array != "") $data_array .= ",";
				$data_array .= "(" . $mrrWiseIsID . "," . $issue_trans_id . "," . $update_id . ",277," . $txt_prod_id . "," . $issueQnty . "," . $cons_rate . "," . $amount . ",'" . $user_id . "','" . $pc_date_time . "')";
				//for update
				$updateID_array[] = $issue_trans_id;
				$update_data[$issue_trans_id] = explode("*", ("" . $issueQntyBalance . "*" . $issueStockBalance . "*'" . $user_id . "'*'" . $pc_date_time . "'"));
				break;
			} else if ($issueQntyBalance < 0) {
				$issueQntyBalance = $issueQnty - $balance_qnty;
				$issueQnty = $balance_qnty;
				$amount = $issueQnty * $cons_rate;

				//for insert
				$mrrWiseIsID = return_next_id_by_sequence("INV_MRR_WISE_ISSUE_PK_SEQ", "inv_mrr_wise_issue_details", $con);
				if ($data_array != "") $data_array .= ",";
				$data_array .= "(" . $mrrWiseIsID . "," . $issue_trans_id . "," . $update_id . ",277," . $txt_prod_id . "," . $issueQnty . "," . $cons_rate . "," . $amount . ",'" . $user_id . "','" . $pc_date_time . "')";
				//echo "20**".$data_array;die;
				//for update
				$updateID_array[] = $issue_trans_id;
				$update_data[$issue_trans_id] = explode("*", ("0*0*'" . $user_id . "'*'" . $pc_date_time . "'"));
				$issueQnty = $issueQntyBalance;
			}
		}//end foreach
		// LIFO/FIFO then END-----------------------------------------------//txt_prod_id
		$query1 = $query2 = $query3 = $rID = $transID = $mrrWiseIssueID = $upTrID = true;
		if($before_prod_id==$txt_prod_id) {
			$query1 = sql_update("product_details_master", $field_array_prod, $data_array_prod, "id", $txt_prod_id, 0);
		} else {
			$query1 = execute_query(bulk_update_sql_statement("product_details_master", "id", $field_array_prod, $data_array_prod, $prodID_array));
		}
		
		if (count($updateID_array_trans) > 0) {
			$query2 = execute_query(bulk_update_sql_statement("inv_transaction", "id", $update_array_trans, $update_data_trans, $updateID_array_trans));
			$query3 = execute_query("DELETE FROM inv_mrr_wise_issue_details WHERE issue_trans_id=$update_id and entry_form=277");
		}
		$rID = sql_update("inv_issue_master", $field_array_mst, $data_array_mst, "id", $update_id_mst, 0);
		$transID = sql_update("inv_transaction", $field_array_trans, $data_array_trans, "id", $update_id, 1);
		if ($data_array != "") {
			$mrrWiseIssueID = sql_insert("inv_mrr_wise_issue_details", $field_array, $data_array, 0);
		}
		
		if (count($updateID_array) > 0) {
			$upTrID = execute_query(bulk_update_sql_statement("inv_transaction", "id", $update_array, $update_data, $updateID_array));
		}
		$id=str_replace("'","",$update_id_mst);
		//echo "10**$query1 = $query2 = $query3 = $rID = $transID = $mrrWiseIssueID = $upTrID";die;
		if ($db_type == 0) {
			if ($query1 && $query2 && $query3 && $rID && $transID && $mrrWiseIssueID && $upTrID) {
				mysql_query("COMMIT");
				echo "1**" . str_replace("'", "", $txt_system_no) . "**" . $id;
			} else {
				mysql_query("ROLLBACK");
				//mysql_query("ROLLBACK TO $savepoint");
				echo "10**" . str_replace("'", "", $txt_system_no) . "**" . $id;
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($query1 && $query2 && $query3 && $rID && $transID && $mrrWiseIssueID && $upTrID) {
				oci_commit($con);
				echo "1**" . str_replace("'", "", $txt_system_no) . "**" . $id;
			} else {
				oci_rollback($con);
				echo "10**" . str_replace("'", "", $txt_system_no) . "**" . $id;
			}
		}
		//check_table_status($_SESSION['menu_id'], 0);
		disconnect($con);
		die;
	} else if ($operation == 2) // Not Used Delete Here----------------------------------------------------------
	{
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}
		
		if(str_replace("'","",$cbo_basis)==6)
		{
			$knit_prod_sql=sql_select("SELECT a.id, a.PI_WO_BATCH_NO 
			from INV_TRANSACTION a, PPL_CUT_LAY_MST b, PRO_GARMENTS_PRODUCTION_MST c 
			where a.PI_WO_BATCH_NO=b.id and b.CUTTING_NO=C.CUT_NO and a.receive_basis=6 and a.entry_form=277 and a.transaction_type=2 and a.item_category=1 and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.PI_WO_BATCH_NO=$job_lot_id");
			//echo "10**".count($knit_prod_sql);die;
			if(count($knit_prod_sql)>0 )
			{
				echo "31**Issue to Knitting Floor Found So Delete Not Allow.";die;
			}
		}
		
		//$return_result = sql_select("SELECT a.recv_number,b.order_qnty FROM inv_receive_master a, inv_transaction b WHERE a.id=b.mst_id and a.item_category=1 and b.item_category=1 and b.prod_id=$txt_prod_id and a.issue_id=$update_id_mst and b.company_id=$cbo_company_id and b.transaction_type=4 and b.transaction_type=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
//
//		if(!empty($return_result))
//		{
//			foreach ($return_result as $return_row)
//			{
//				$returnString .= ",".$return_row[csf('recv_number')]." -> ".$return_row[csf('order_qnty')];
//			}
//			disconnect($con);
//			exit("30**".$returnString);
//		}
		
		
		$update_array_trans = "balance_qnty*balance_amount*updated_by*update_date";
		$sql = sql_select("select a.id, a.balance_qnty, a.balance_amount, b.issue_qnty, b.rate, b.amount, b.recv_trans_id, b.issue_trans_id from inv_transaction a, inv_mrr_wise_issue_details b where a.id=b.recv_trans_id and b.issue_trans_id=$update_id and b.entry_form=277 and a.item_category=1");
		$updateID_array_trans = array();
		$update_data_trans = array();
		foreach ($sql as $result) {
			$adjBalance = $result[csf("balance_qnty")] + $result[csf("issue_qnty")];
			$adjAmount = $result[csf("balance_amount")] + $result[csf("amount")];
			$before_issue_qnty=$result[csf("issue_qnty")];
			$updateID_array_trans[] = $result[csf("id")];
			$update_data_trans[$result[csf("id")]] = explode("*", ("" . $adjBalance . "*" . $adjAmount . "*'" . $user_id . "'*'" . $pc_date_time . "'"));

			$trans_data_array[$result[csf("id")]]['qnty'] = $adjBalance;
			$trans_data_array[$result[csf("id")]]['amnt'] = $adjAmount;
		}
		
		$sql = sql_select("select supplier_id, avg_rate_per_unit,current_stock,stock_value,allocated_qnty,available_qnty from product_details_master where id=$txt_prod_id and item_category_id=1");
		$curr_avg_rate = $curr_stock_qnty = $curr_stock_value = $allocated_qnty = $available_qnty = 0;
		$supplier_id_for_tran = '';
		foreach ($sql as $result) {
			$curr_avg_rate = $result[csf("avg_rate_per_unit")];
			$curr_stock_qnty = $result[csf("current_stock")];
			$curr_stock_value = $result[csf("stock_value")];
			$allocated_qnty = $result[csf("allocated_qnty")];
			$available_qnty = $result[csf("available_qnty")];
			$supplier_id_for_tran = $result[csf("supplier_id")];
		}
		
		$currentStock = (($curr_stock_qnty+$before_issue_qnty));
		$currentStockValue = $currentStock*$curr_avg_rate;
		if(str_replace("'","",$cbo_basis)==5)
		{
			$cu_allocated_qnty_balance=$allocated_qnty_balance;
			$cu_available_qnty_balance=$available_qnty_balance+$before_issue_qnty;
		}
		else
		{
			$cu_allocated_qnty_balance=$allocated_qnty_balance+$before_issue_qnty;
			$cu_available_qnty_balance=$available_qnty_balance;
		}
		$field_array_prod = "last_issued_qnty*current_stock*stock_value*allocated_qnty*available_qnty*updated_by*update_date";
		$data_array_prod = "" . $txt_issue_qnty . "*" . $currentStock . "*" . number_format($currentStockValue, $dec_place[4], '.', '') . "*" . $cu_allocated_qnty_balance . "*" . $cu_available_qnty_balance . "*'" . $user_id . "'*'" . $pc_date_time . "'";
		
		$field_array_status = "updated_by*update_date*status_active*is_deleted";
		$data_array_status = $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*0*1";
		
		$rID = $transID = $query1 = $query2 = $query3 = true;
		$sql_trans=sql_select("select id from inv_transaction where status_active=1 and transaction_type=2 and mst_id=$update_id_mst");
		if(count($sql_trans)==1)
		{
			$rID = sql_update("inv_issue_master", $field_array_status, $data_array_status, "id", $update_id_mst, 0);
		}
		$transID = sql_update("inv_transaction", $field_array_status, $data_array_status, "id", $update_id, 0);
		$query1 = sql_update("product_details_master", $field_array_prod, $data_array_prod, "id", $txt_prod_id, 0);
		if (count($updateID_array_trans) > 0) {
			$query2 = execute_query(bulk_update_sql_statement("inv_transaction", "id", $update_array_trans, $update_data_trans, $updateID_array_trans));
			$query3 = execute_query("DELETE FROM inv_mrr_wise_issue_details WHERE issue_trans_id=$update_id and entry_form=277");
		}
		$id=str_replace("'","",$update_id_mst);
		if ($db_type == 0) {
			if ($query1 && $query2 && $query3 && $rID && $transID) {
				mysql_query("COMMIT");
				echo "2**" . str_replace("'", "", $txt_system_no) . "**" . $id;
			} else {
				mysql_query("ROLLBACK");
				//mysql_query("ROLLBACK TO $savepoint");
				echo "10**" . str_replace("'", "", $txt_system_no) . "**" . $id;
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($query1 && $query2 && $query3 && $rID && $transID) {
				oci_commit($con);
				echo "2**" . str_replace("'", "", $txt_system_no) . "**" . $id;
			} else {
				oci_rollback($con);
				echo "10**" . str_replace("'", "", $txt_system_no) . "**" . $id;
			}
		}
		//check_table_status($_SESSION['menu_id'], 0);
		disconnect($con);
		die;
		
		/*

		//echo "SELECT a.recv_number,b.order_qnty FROM inv_receive_master a, inv_transaction b WHERE a.id=b.mst_id and a.issue_id=$update_id_mst and a.booking_no=$txt_req_no and b.transaction_type=4 and b.transaction_type=4";

		$return_result = sql_select("SELECT a.recv_number,b.order_qnty FROM inv_receive_master a, inv_transaction b WHERE a.id=b.mst_id and a.issue_id=$update_id_mst and a.booking_no=$txt_req_no and b.transaction_type=4 and b.transaction_type=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");

		if(!empty($return_result))
		{
			foreach ($return_result as $return_row) {
				$returnString .= ",".$return_row[csf('recv_number')]." -> ".$return_row[csf('order_qnty')];
			}
			exit("30**".$returnString);
		}


		if (str_replace("'", "", $txt_system_no) != "") //new insert cbo_ready_to_approved
		{
			$check_in_gate_pass = return_field_value("sys_number", "inv_gate_pass_mst", "challan_no=$txt_system_no and status_active=1 and is_deleted=0", "sys_number");
			if ($check_in_gate_pass != "") {
				echo "20**Gate Pass found.\nGate Pass ID = $check_in_gate_pass";
				die;
			}
		}

		//check update id
		if (str_replace("'", "", $update_id) == "" || str_replace("'", "", $txt_system_no) == "") {
			echo "15";
			disconnect($con);
			exit();
		}

		//product master table information
		//before stock update
		$sql = sql_select("select a.id,a.avg_rate_per_unit,a.current_stock,a.stock_value, b.cons_quantity, b.cons_amount,a.allocated_qnty,a.available_qnty from product_details_master a, inv_transaction b where a.id=b.prod_id and b.id=$update_id and a.item_category_id=1 and b.item_category=1 and b.transaction_type=2");
		$before_prod_id = $before_issue_qnty = $before_stock_qnty = $before_stock_value = 0;
		foreach ($sql as $result) {
			$before_prod_id = $result[csf("id")];
			$before_stock_qnty = $result[csf("current_stock")];
			$before_avg_rate_per_unit = $result[csf("avg_rate_per_unit")];

			$before_issue_qnty = $result[csf("cons_quantity")];
			$before_issue_value = $result[csf("cons_amount")];
			$before_allocated_qnty = $result[csf("allocated_qnty")];
			$before_available_qnty = $result[csf("available_qnty")];
		}

		//weighted and average rate START here------------------------//
		//product master table data UPDATE START----------------------//
		$field_array_prod = "current_stock*stock_value*allocated_qnty*available_qnty*updated_by*update_date";
		$adj_stock_qnty = $before_stock_qnty + $before_issue_qnty;
		$adj_stock_val = $adj_stock_qnty * $before_avg_rate_per_unit;
		$allocated_qnty_balance = 0;
		$available_qnty_balance = 0;

		if ($variable_set_allocation == 1) {
			if (str_replace("'", "", $cbo_basis) == 3 && (str_replace("'", "", $cbo_issue_purpose) == 1 || str_replace("'", "", $cbo_issue_purpose) == 4)) {
				$allocated_qnty_balance = $before_allocated_qnty + $before_issue_qnty;
				$available_qnty_balance = $before_available_qnty;
			} else if ((str_replace("'", "", $cbo_basis) == 1) && (str_replace("'", "", $cbo_issue_purpose) == 2 || str_replace("'", "", $cbo_issue_purpose) == 15 || str_replace("'", "", $cbo_issue_purpose) == 38 || str_replace("'", "", $cbo_issue_purpose) == 46 || str_replace("'", "", $cbo_issue_purpose) == 7)) {
				if (str_replace("'", "", $txt_entry_form) == 42 || str_replace("'", "", $txt_entry_form) == 94 || str_replace("'", "", $txt_entry_form) == 114) {
					$allocated_qnty_balance = $before_allocated_qnty;
					$available_qnty_balance = $before_available_qnty + $before_issue_qnty;
				} else {
					$allocated_qnty_balance = $before_allocated_qnty + $before_issue_qnty;
					$available_qnty_balance = $before_available_qnty;
				}
			} else {
				$allocated_qnty_balance = $before_allocated_qnty;
				$available_qnty_balance = $before_available_qnty + $before_issue_qnty;
			}
		} else {
			$allocated_qnty_balance = $before_allocated_qnty;
			$available_qnty_balance = $before_available_qnty + $before_issue_qnty;
		}

		$data_array_prod = "" . $adj_stock_qnty . "*" . $adj_stock_val . "*" . $allocated_qnty_balance . "*" . $available_qnty_balance . "*'" . $user_id . "'*'" . $pc_date_time . "'";

		$trans_data_array = array();
		$update_array_trans = "balance_qnty*balance_amount*updated_by*update_date";
		$sql = sql_select("select a.id,a.balance_qnty,a.balance_amount,b.issue_qnty,b.rate,b.amount from inv_transaction a, inv_mrr_wise_issue_details b where a.id=b.recv_trans_id and b.issue_trans_id=$update_id and b.entry_form=3 and a.item_category=1");
		$updateID_array_trans = array();
		$update_data_trans = array();
		foreach ($sql as $result) {
			$adjBalance = $result[csf("balance_qnty")] + $result[csf("issue_qnty")];
			$adjAmount = $result[csf("balance_amount")] + $result[csf("amount")];
			$updateID_array_trans[] = $result[csf("id")];
			$update_data_trans[$result[csf("id")]] = explode("*", ("" . $adjBalance . "*" . $adjAmount . "*'" . $user_id . "'*'" . $pc_date_time . "'"));
		}
		// echo "10**<pre>";
		// print_r($data_array_prod);
		// echo "10**ok";die;
		$query1 = sql_update("product_details_master", $field_array_prod, $data_array_prod, "id", $before_prod_id, 0);

		if (!empty($update_data_trans)) {
			$query2 = execute_query(bulk_update_sql_statement("inv_transaction", "id", $update_array_trans, $update_data_trans, $updateID_array_trans));
		} else {
			$query2 = 1;
		}
		$query3 = execute_query("DELETE FROM inv_mrr_wise_issue_details WHERE issue_trans_id=$update_id and entry_form=3");
		$query4 = execute_query("DELETE FROM order_wise_pro_details WHERE trans_id=$update_id and entry_form=3");
		$field_array_status = "updated_by*update_date*status_active*is_deleted";
		$data_array_status = $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*0*1";
		$changeStatus = sql_update("inv_transaction", $field_array_status, $data_array_status, "id", $update_id, 1);

		//echo $query1."&&".$query2."&&".$query3."&&".$query4."&&".$changeStatus;
		if ($db_type == 0) {
			if ($query1 && $query2 && $query3 && $query4 && $changeStatus) {
				mysql_query("COMMIT");
				echo "2**" . str_replace("'", "", $txt_system_no);
			} else {
				mysql_query("ROLLBACK");
				echo "10**" . str_replace("'", "", $txt_system_no);
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($query1 && $query2 && $query3 && $query4 && $changeStatus) {
				oci_commit($con);
				echo "2**" . str_replace("'", "", $txt_system_no);
			} else {
				oci_rollback($con);
				echo "10**" . str_replace("'", "", $txt_system_no);
			}
		}
		disconnect($con);
		die;*/
	}
}

if ($action == "populate_data_from_data") 
{
	$sql = "select id, issue_number, issue_basis, issue_purpose, entry_form, item_category, company_id, location_id, supplier_id, store_id, buyer_id, buyer_job_no, style_ref, issue_date, sample_type, knit_dye_source, knit_dye_company, challan_no, loan_party, lap_dip_no, gate_pass_no, item_color, color_range, remarks, ready_to_approve, loan_party, is_approved
	from inv_issue_master
	where id='$data' and entry_form=277";
//echo $sql;
	$res = sql_select($sql);
	foreach ($res as $row) {
		
		echo "active_inactive(".$row[csf("issue_basis")].");\n";
		echo "$('#cbo_company_id').val(" . $row[csf("company_id")] . ");\n";
		echo"load_drop_down( 'requires/yarn_issue_controller', ".$row[csf("company_id")]."+'_'+1, 'load_drop_down_buyer', 'buyer_td_id' );\n";
		echo"load_drop_down( 'requires/yarn_issue_controller', ".$row[csf("company_id")].", 'load_drop_down_supplier', 'supplier' );\n";
		echo"load_room_rack_self_bin('requires/yarn_issue_controller*1', 'store','store_td', this.value,'','','','','','','','');\n";
		echo "$('#cbo_basis').val(" . $row[csf("issue_basis")] . ");\n";
		echo "$('#cbo_issue_purpose').val(" . $row[csf("issue_purpose")] . ");\n";
		echo "$('#txt_issue_date').val('" . change_date_format($row[csf("issue_date")]) . "');\n";
		echo "$('#cbo_knitting_source').val(" . $row[csf("knit_dye_source")] . ");\n";
		if ($row[csf("knit_dye_source")] != 0) {
			echo "load_drop_down( 'requires/yarn_issue_controller', " . $row[csf("knit_dye_source")] . "+'**'+" . $row[csf("company_id")] . ", 'load_drop_down_knit_com', 'knitting_company_td' );\n";
		}
		echo"load_drop_down( 'requires/yarn_issue_controller', ".$row[csf("knit_dye_company")]."+'_'+".$row[csf("knit_dye_source")].", 'load_drop_down_location', 'location_td' );\n";
		echo "$('#cbo_location_id').val(" . $row[csf("location_id")] . ");\n";
		echo "$('#cbo_knitting_company').val(" . $row[csf("knit_dye_company")] . ");\n";
		//echo "$('#cbo_supplier').val(" . $row[csf("supplier_id")] . ");\n";
		echo "$('#txt_challan_no').val('" . $row[csf("challan_no")] . "');\n";
		echo "$('#cbo_loan_party').val(" . $row[csf("loan_party")] . ");\n";
		echo "$('#cbo_sample_type').val(" . $row[csf("sample_type")] . ");\n";
		//echo "$('#cbo_buyer_name').val(" . $row[csf("buyer_id")] . ");\n";
		//echo "$('#txt_style_no').val('" . $row[csf("style_ref")] . "');\n";
		//echo "$('#txt_buyer_job_no').val('" . $row[csf("buyer_job_no")] . "');\n";
		echo "$('#txt_remarks').val('" . $row[csf("remarks")] . "');\n";
		echo "$('#cbo_ready_to_approved').val(" . $row[csf("ready_to_approve")] . ");\n";
		//clear child form
		//echo "$('#tbl_child').find('select,input').val('');\n";
		if($row[csf("is_approved")]==3){
			$is_approved=1;
		}else{
			$is_approved=$row[csf("is_approved")];
		}
		if ($is_approved == 1) {
			echo "$('#approved').text('Approved');\n";
		} else {
			echo "$('#approved').text('');\n";
		}
	}
	exit();
}
// show_dtls_list_view done
if ($action == "show_dtls_list_view") {
	$ex_data = explode("**", $data);
	$up_id = $ex_data[0];

	$cond = "";
	if ($up_id != "") $cond .= " and a.id='$up_id'";
	$store_arr = return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');
	$yarn_count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$color_name_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	$supplier_arr = return_library_array("select id,short_name from lib_supplier", 'id', 'short_name');

	$sql = "select b.requisition_no, a.challan_no, c.yarn_count_id, c.yarn_comp_type1st, c.yarn_comp_percent1st, c.yarn_comp_type2nd, c.yarn_comp_percent2nd, c.yarn_type, c.color, c.lot, b.id, b.store_id, b.cons_uom, b.cons_quantity, b.cons_amount, b.rack, b.self, b.supplier_id
	from inv_issue_master a, inv_transaction b, product_details_master c
	where a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=2 and b.item_category=1 and a.entry_form=277 and b.status_active=1 and b.is_deleted=0 $cond";
//echo $sql;
	$result = sql_select($sql);
	$i = 1;
	$total_qnty = 0;
	?>
	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" width="970" rules="all">
		<thead>
			<tr>
				<th>SL</th>
				<th>Challan No</th>
				<th>Lot No</th>
				<th>Supplier</th>
				<th>Yarn Count</th>
				<th>Composition</th>
				<th>Yarn Type</th>
				<th>Color</th>
				<th>Store</th>
				<th>Issue Qnty</th>
				<th>UOM</th>
				<th>Lot Ratio No</th>
				<th>Rack</th>
				<th>Shelf</th>
			</tr>
		</thead>
		<tbody>
			<? foreach ($result as $row) {

				if ($i % 2 == 0) $bgcolor = "#E9F3FF";
				else $bgcolor = "#FFFFFF";

				$composition_string = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')];
				if ($row[csf('yarn_comp_type2nd')] != 0) $composition_string .= " " . $composition[$row[csf('yarn_comp_type2nd')]] . " " . $row[csf('yarn_comp_percent2nd')];

				$total_qnty += $row[csf("cons_quantity")];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>"
					onClick='get_php_form_data("<? echo $row[csf("id")]; ?>","child_form_input_data","requires/yarn_issue_controller"); disable_fields();'
					style="cursor:pointer">
					<td width="30"><?php echo $i; ?></td>
					<td width="70"><p><?php echo $row[csf("challan_no")]; ?>&nbsp;</p></td>
					<td width="70"><p><?php echo $row[csf("lot")]; ?></p></td>
					<td width="70"><p><?php echo $supplier_arr[$row[csf("supplier_id")]]; ?></p></td>
					<td width="70"><p><?php echo $yarn_count_arr[$row[csf("yarn_count_id")]]; ?></p></td>
					<td width="120"><p><?php echo $composition_string; ?></p></td>
					<td width="70"><p><?php echo $yarn_type[$row[csf("yarn_type")]]; ?></p></td>
					<td width="80"><p><?php echo $color_name_arr[$row[csf("color")]]; ?></p></td>
					<td width="80"><p><?php echo $store_arr[$row[csf("store_id")]]; ?></p></td>
					<td width="70" align="right"><p><?php echo number_format($row[csf("cons_quantity")], 2, '.', ''); ?></p>
					</td>
					<td width="50"><p><?php echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></p></td>
					<td width="60"><p><?php echo $row[csf("requisition_no")]; ?>&nbsp;</p></td>
					<td width="50"><p><?php echo $row[csf("rack")]; ?>&nbsp;</p></td>
					<td><p><?php echo $row[csf("self")]; ?>&nbsp;</p></td>
				</tr>
				<? $i++;
			} ?>
			<tfoot>
				<th colspan="9" align="right">Sum</th>
				<th><?php echo number_format($total_qnty,2); ?></th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
			</tfoot>
		</tbody>
	</table>
	<?
	exit();
}

// child_form_input_data done
if ($action == "child_form_input_data") {
	$rcv_dtls_id = $data;
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$brand_arr=return_library_array("select id, brand_name from lib_brand",'id','brand_name');
	
	$sql = "select a.company_id, a.issue_basis, a.issue_purpose, b.requisition_no, b.pi_wo_batch_no, b.id, b.receive_basis, b.store_id, b.supplier_id, b.cons_uom, b.cons_quantity, b.return_qnty, b.item_return_qty, b.no_of_bags, b.cone_per_bag, b.weight_per_bag, b.weight_per_cone, b.dyeing_color_id, b.room, b.rack, b.self, b.floor_id, b.using_item, b.job_no, b.buyer_id, b.style_ref_no, c.current_stock, c.allocated_qnty, c.available_qnty, c.yarn_count_id, c.yarn_comp_type1st, c.yarn_comp_percent1st, c.yarn_comp_type2nd, c.yarn_comp_percent2nd, c.yarn_type, c.color, c.brand, c.lot, c.id as prod_id, b.booking_no, b.pi_wo_dtls_id, c.is_supp_comp 
	from inv_issue_master a, inv_transaction b, product_details_master c 
	where a.id=b.mst_id and b.prod_id=c.id and b.id='$rcv_dtls_id' and b.transaction_type=2 and b.item_category=1";
	$result = sql_select($sql);
	foreach ($result as $row) 
	{
		/*echo "$('#txt_req_no').val(" . $row[csf("requisition_no")] . ");\n";
		if ($row[csf("issue_basis")] == 3) {
			echo "show_list_view('" . $row[csf("requisition_no")] . "," . $row[csf("company_id")] . "','show_req_list_view','requisition_item','requires/yarn_issue_controller','');\n";
		}*/
		echo "load_drop_down( 'requires/yarn_issue_controller', ".$row[csf("company_id")]."+'_'+1, 'load_drop_down_buyer', 'buyer_td_id' );\n";
		echo "$('#txt_buyer_job_no').val('" . $row[csf("job_no")] . "');\n";
		echo "$('#txt_lot_ratio').val('" . $row[csf("requisition_no")] . "');\n";
		if($row[csf("receive_basis")]==5)
		{
			echo "$('#hide_job_id').val(" . $row[csf("pi_wo_batch_no")] . ");\n";
			echo "show_list_view('" . $row[csf("job_no")] . "'+'__'+" . $row[csf("store_id")] . "+'__'+" . $row[csf("company_id")] . "+'__'+" . $row[csf("receive_basis")] . "+'__'+" . $row[csf("pi_wo_batch_no")] . ", 'show_job_item_list_view', 'requisition_item', 'requires/yarn_issue_controller', '');\n";
			
		}
		else if($row[csf("receive_basis")]==6)
		{
			echo "$('#txt_lot_ratio_id').val(" . $row[csf("pi_wo_batch_no")] . ");\n";
			echo "show_list_view('" . $row[csf("job_no")] . "'+'__'+" . $row[csf("store_id")] . "+'__'+" . $row[csf("company_id")] . "+'__'+" . $row[csf("receive_basis")] . "+'__'+" . $row[csf("pi_wo_batch_no")] . ", 'show_lot_item_list_view', 'requisition_item', 'requires/yarn_issue_controller', '');\n";
		}
		else
		{
			echo "$('#hide_job_id').val(" . $row[csf("pi_wo_batch_no")] . ");\n";
			echo "show_list_view('".$row[csf("pi_wo_dtls_id")]."'+'__'+'".$row[csf("booking_no")]."'+'__'+".$row[csf("company_id")]."+'__'+". $row[csf("receive_basis")]."+'__'+".$row[csf("store_id")]."+'__'+".$row[csf("issue_purpose")].", 'show_booking_item_list_view', 'requisition_item', 'requires/yarn_issue_controller', '');\n";
		}
		
		echo "$('#cbo_buyer_name').val(" . $row[csf("buyer_id")] . ");\n";
		echo "$('#txt_style_no').val('" . $row[csf("style_ref_no")] . "');\n";
		echo "$('#txt_lot_no').val('" . $row[csf("lot")] . "');\n";
		echo "$('#txt_prod_id').val(" . $row[csf("prod_id")] . ");\n";
		
		echo "$('#txt_issue_qnty').val(" . $row[csf("cons_quantity")] . ");\n";
		echo "$('#txt_returnable_qty').val(" . $row[csf("return_qnty")] . ");\n";
		$sql_transaction = sql_select("select sum((case when a.transaction_type in(1,4,5) then a.cons_quantity else 0 end)-(case when a.transaction_type in(2,3,6) then a.cons_quantity else 0 end)) as yarn_qnty 
		from inv_transaction a
		where a.job_no='".$row[csf("job_no")]."' and a.store_id=".$row[csf("store_id")]." and a.company_id='".$row[csf("company_id")]."' and a.prod_id=".$row[csf("prod_id")]." and a.status_active=1 and a.is_deleted=0 and a.item_category=1 and a.entry_form in(248,249,277,381,382)");
		$stock_qnty = $sql_transaction[0][csf("yarn_qnty")]+$row[csf("cons_quantity")];
		echo "$('#txt_current_stock').val(" . $stock_qnty . ");\n";
		$sql_booking = sql_select("select a.yarn_wo_qty as wo_yarn_qnty  from wo_yarn_dyeing_dtls a where a.job_no='".$row[csf("job_no")]."' and a.mst_id='".$row[csf("pi_wo_dtls_id")]."' and a.product_id=".$row[csf("prod_id")]." and a.status_active=1 and a.is_deleted=0");
		$prev_book_rcv=sql_select("select sum(a.cons_quantity) as prev_rcv_qnty 
		from inv_transaction a
		where a.job_no='".$row[csf("job_no")]."' and a.pi_wo_dtls_id=".$row[csf("pi_wo_dtls_id")]." and a.prod_id=".$row[csf("prod_id")]." and a.status_active=1 and a.is_deleted=0 and a.item_category=1 and a.entry_form in(248) and id <> ".$row[csf("id")]."");
		$booking_qnty = $sql_booking[0][csf("wo_yarn_qnty")]-$prev_book_rcv[0][csf("prev_rcv_qnty")];
		if($row[csf("is_supp_comp")]==2)
		{
			echo"load_drop_down( 'requires/yarn_issue_controller', ".$row[csf("company_id")].", 'load_drop_down_supplier_com', 'supplier' );\n";
		}
		else
		{
			echo"load_drop_down( 'requires/yarn_issue_controller', ".$row[csf("company_id")].", 'load_drop_down_supplier', 'supplier' );\n";
		}
		
		
		echo "$('#txt_wo_qnty').val(" . $booking_qnty . ");\n";
		echo "$('#txt_no_bag').val(" . $row[csf("no_of_bags")] . ");\n";
		echo "$('#cbo_supplier').val(" . $row[csf("supplier_id")] . ");\n";
		echo "$('#txt_no_cone').val(" . $row[csf("cone_per_bag")] . ");\n";
		echo "$('#txt_weight_per_bag').val(" . $row[csf("weight_per_bag")] . ");\n";
		echo "$('#txt_weight_per_cone').val(" . $row[csf("weight_per_cone")] . ");\n";

		echo "load_room_rack_self_bin('requires/yarn_issue_controller*1', 'store','store_td', '".$row[csf('company_id')]."',this.value,'', '','','','','','fn_empty_lot(this.value);');\n";
		echo "$('#cbo_store_name').val(" . $row[csf("store_id")] . ");\n";
		echo "$('#cbo_store_name').attr('disabled',true);\n";
		
		$composition_string = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')];
		
		echo "$('#txt_composition').val('" . $composition_string . "');\n";
		echo "$('#txt_composition_id').val(" . $row[csf("yarn_comp_type1st")] . ");\n";
		
		echo "$('#cbo_yarn_type').val('" . $yarn_type[$row[csf("yarn_type")]] . "');\n";
		echo "$('#cbo_color').val('" . $color_arr[$row[csf("color")]] . "');\n";
		echo "$('#cbo_brand').val('" . $brand_arr[$row[csf("brand")]] . "');\n";
		echo "$('#cbo_brand_id').val(" . $row[csf("brand")] . ");\n";
		echo "$('#cbo_dyeing_color').val(" . $row[csf("dyeing_color_id")] . ");\n";
		echo "$('#cbo_uom').val('" . $unit_of_measurement[$row[csf("cons_uom")]] . "');\n";
		echo "$('#cbo_uom_id').val(" . $row[csf("cons_uom")] . ");\n";
		echo "$('#cbo_yarn_count').val('" . $count_arr[$row[csf("yarn_count_id")]] . "');\n";
		echo "$('#txt_booking_no').val('".$row[csf("booking_no")]."');\n";
		echo "$('#hide_booking_id').val('".$row[csf("pi_wo_dtls_id")]."');\n";
		
		if($row[csf("floor_id")]>0)
		{
			echo "load_room_rack_self_bin('requires/yarn_issue_controller', 'floor','floor_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."',this.value);\n";
			// echo "$('#cbo_floor').attr('disabled')";
			// echo "document.getElementById('cbo_floor').disabled = True";
		}
		echo "$('#cbo_floor').val(" . $row[csf("floor_id")] . ");\n";
		if($row[csf("room")]>0)
		{
			echo "load_room_rack_self_bin('requires/yarn_issue_controller', 'room','room_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."',this.value);\n";
		}
		echo "$('#cbo_room').val(" . $row[csf("room")] . ");\n";
		if($row[csf("rack")]>0)
		{
			echo "load_room_rack_self_bin('requires/yarn_issue_controller', 'rack','rack_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."',this.value);\n";
		}
		echo "$('#txt_rack').val(" . $row[csf("rack")] . ");\n";
		if($row[csf("self")]>0)
		{
			echo "load_room_rack_self_bin('requires/yarn_issue_controller', 'shelf','shelf_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."','".$row[csf('rack')]."',this.value);\n";
		}
		echo "$('#txt_shelf').val(" . $row[csf("self")] . ");\n";
		echo "$('#cbo_item').val(" . $row[csf("using_item")] . ");\n";

		
		//update id here
		echo "$('#update_id').val(" . $row[csf("id")] . ");\n";
		echo "set_button_status(1, permission, 'fnc_yarn_issue_entry',1);\n";
	}
	exit();
}

if ($action == "mrr_popup") {
	echo load_html_head_contents("Popup Info", "../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(sys_number) {
			$("#hidden_sys_number").val(sys_number); // mrr number
			parent.emailwindow.hide();
		}
	</script>

</head>
<body>
	<div align="center" style="width:100%;">
		<form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
			<table width="880" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
				<thead>
					<tr>
						<th width="150">Supplier</th>
						<th width="150">Search By</th>

						<th width="250" align="center" id="search_by_td_up">Enter Issue Number</th>
						<th width="200">Date Range</th>
						<th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"/></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>
								<?
								echo create_drop_down("cbo_supplier", 150, "select c.supplier_name,c.id from lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and b.party_type=2 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name", "id,supplier_name", 1, "-- Select --", 0, "", 0);
								?>
							</td>
							<td>
								<?
								//$search_by = array(1 => 'Issue No', 2 => 'Challan No', 3 => 'In House', 4 => 'Out Bound Subcontact', 5 => 'Job No', 6 => 'Wo No', 7 => 'Buyer');
								$search_by = array(1 => 'Issue No', 2 => 'Challan No');
								$dd = "change_search_event(this.value, '0*0*1*1*0*0*1', '0*0*select id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name*select c.id, c.supplier_name from lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and b.party_type=2 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name*0*0*select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$company $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name', '../../../') ";
								echo create_drop_down("cbo_search_by", 150, $search_by, "", 0, "--Select--", "", $dd, 0);
								?>
							</td>
							<td width="" align="center" id="search_by_td">
								<input type="text" style="width:230px" class="text_boxes" name="txt_search_common"
								id="txt_search_common"/>
							</td>
							<td align="center">
								<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date"/>
								<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date"/>
							</td>
							<td align="center">
								<input type="button" name="btn_show" class="formbutton" value="Show"
								onClick="show_list_view ( document.getElementById('cbo_supplier').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>, 'create_mrr_search_list_view', 'search_div', 'yarn_issue_controller', 'setFilterGrid(\'list_view\',-1)')"
								style="width:100px;"/>
							</td>
						</tr>
						<tr>
							<td align="center" height="40" valign="middle" colspan="5">
								<? echo load_month_buttons(1); ?>
								<!-- Hidden field here-->
								<input type="hidden" id="hidden_sys_number" value=""/>
								<!-- END -->
							</td>
						</tr>
					</tbody>
				</table>
				<div align="center" valign="top" id="search_div"></div>
			</form>
		</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>

	</html>
	<?
	exit();
}

if ($action == "create_mrr_search_list_view") 
{
	$ex_data = explode("_", $data);
	$supplier = $ex_data[0];
	$txt_search_by = $ex_data[1];
	$txt_search_common = trim($ex_data[2]);
	$fromDate = $ex_data[3];
	$toDate = $ex_data[4];
	$company = $ex_data[5];
	$sql_cond = "";
	
	if ($fromDate != "" && $toDate != "") {
		if ($db_type == 0) {
			$sql_cond .= " and issue_date between '" . change_date_format($fromDate, 'yyyy-mm-dd') . "' and '" . change_date_format($toDate, 'yyyy-mm-dd') . "'";
		} else {
			$sql_cond .= " and issue_date between '" . change_date_format($fromDate, '', '', 1) . "' and '" . change_date_format($toDate, '', '', 1) . "'";
		}
	}
	
	if ($supplier != "" && $supplier * 1 != 0) $sql_cond .= " and a.supplier_id='$supplier'";
	if ($company != "" && $company * 1 != 0) $sql_cond .= " and a.company_id='$company'";
	
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
	$company_arr = return_library_array("select id, company_name from lib_company", 'id', 'company_name');
	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
	$store_arr = return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');
	$within_group_arr = return_library_array("select id, within_group from fabric_sales_order_mst", 'id', 'within_group');
	
	if ($txt_search_common != "" || $txt_search_common != 0) {
		if ($txt_search_by == 1) {
			$sql_cond .= " and a.issue_number like '%$txt_search_common%'";
		} else if ($txt_search_by == 2) {
			$sql_cond .= " and a.challan_no like '%$txt_search_common%'";
		} else if ($txt_search_by == 3) {
			$sql_cond .= " and a.knit_dye_source=1 and a.knit_dye_company='$txt_search_common'";
		} else if ($txt_search_by == 4) {
			$sql_cond .= " and a.knit_dye_source=2 and a.knit_dye_company='$txt_search_common'";
		} else if ($txt_search_by == 5) {
			$sql_cond .= " and a.buyer_job_no like '%$txt_search_common%'";
		} else if ($txt_search_by == 6) {
			$sql_cond .= " and a.booking_no like '%$txt_search_common%'";
		} else if ($txt_search_by == 7) {
			$sql_cond .= " and a.buyer_id = '$txt_search_common'";
		}
	}
	
	if ($db_type == 0) $year_field = "YEAR(a.insert_date) as year,";
	else if ($db_type == 2) $year_field = "to_char(a.insert_date,'YYYY') as year,";
	else $year_field = "";
	
	if($user_store_ids) $user_store_cond = " and a.store_id in ($user_store_ids)"; else $user_store_cond = "";
	
	if ($db_type == 0) {
		$sql = "select a.id, a.issue_number_prefix_num, a.issue_number, a.issue_basis, a.issue_purpose, a.entry_form, a.item_category, a.company_id, a.location_id, a.supplier_id, a.store_id, a.buyer_id, a.buyer_job_no, a.style_ref, a.booking_id, a.booking_no, a.issue_date, a.sample_type, a.knit_dye_source, a.knit_dye_company, a.challan_no, a.loan_party, a.lap_dip_no, a.gate_pass_no, a.item_color, a.color_range, a.remarks, a.ready_to_approve, a.loan_party,a.is_posted_account , $year_field a.is_approved,sum(b.cons_quantity) issue_quantity from inv_issue_master a,inv_transaction b where a.id=b.mst_id and b.transaction_type=2 and b.item_category=1 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.entry_form=277 $sql_cond $user_store_cond group by a.id order by a.issue_number";
	}else{
		$sql = "select a.id, a.issue_number_prefix_num, a.issue_number, a.issue_basis, a.issue_purpose, a.entry_form, a.item_category, a.company_id, a.location_id, a.supplier_id, a.store_id, a.buyer_id, a.buyer_job_no, a.style_ref, a.booking_id, a.booking_no, a.issue_date, a.sample_type, a.knit_dye_source, a.knit_dye_company, a.challan_no, a.loan_party, a.lap_dip_no, a.gate_pass_no, a.item_color, a.color_range, a.remarks, a.ready_to_approve, a.loan_party,a.is_posted_account , $year_field a.is_approved,sum(b.cons_quantity) issue_quantity 
		from inv_issue_master a, inv_transaction b 
		where a.id=b.mst_id and b.transaction_type=2 and b.item_category=1 and a.status_active=1 and a.entry_form=277 $sql_cond $user_store_cond group by a.id, a.issue_number_prefix_num, a.issue_number, a.issue_basis, a.issue_purpose, a.entry_form, a.item_category, a.company_id, a.location_id, a.supplier_id, a.store_id, a.buyer_id, a.buyer_job_no, a.style_ref, a.booking_id, a.booking_no, a.issue_date, a.sample_type, a.knit_dye_source, a.knit_dye_company, a.challan_no, a.loan_party, a.lap_dip_no, a.gate_pass_no, a.item_color, a.color_range, a.remarks, a.ready_to_approve, a.loan_party,a.insert_date,a.is_posted_account,a.is_approved 
		order by a.id desc";
	}
	//echo $sql;
	$result = sql_select($sql);
	?>
	<div style="margin-top:5px">
	<div style="width:1020px;">
		<table cellspacing="0" cellpadding="0" width="100%" class="rpt_table" border="1" rules="all">
			<thead>
				<th width="30">SL</th>
				<th width="60">Issue No</th>
				<th width="50">Year</th>
				<th width="70">Date</th>
				<th width="100">Purpose</th>
				<th width="70">Challan No</th>
				<th width="60">Issue Qnty</th>
				<th width="110">Booking No</th>
				<th width="100">knitting Comp.</th>
				<th width="115">Buyer</th>
				<th width="85">Job No.</th>
				<th width="90">Store</th>
				<th>Ready to Approve</th>
			</thead>
		</table>
	</div>
	<div style="width:1020px;overflow-y:scroll; max-height:210px;" id="search_div">
		<table cellspacing="0" cellpadding="0" width="1002" class="rpt_table" id="list_view" border="1" rules="all">
			<?php
			$i = 1;
			foreach ($result as $row) {
				if ($i % 2 == 0)
					$bgcolor = "#E9F3FF";
				else
					$bgcolor = "#FFFFFF";

				if ($row[csf("issue_basis")] == 4) {
					if ($within_group_arr[$row[csf("booking_id")]] == 1) {
						$buyer = $company_arr[$row[csf("buyer_id")]];
					} else {
						$buyer = $buyer_arr[$row[csf("buyer_id")]];
					}
				} else {
					$buyer = $buyer_arr[$row[csf("buyer_id")]];
				}
				if($row[csf("is_approved")]==3){
					$is_approved=1;
				}else{
					$is_approved=$row[csf("is_approved")];
				}
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
					onclick="js_set_value('<? echo $row[csf("issue_number")]; ?>,<? echo $is_approved; ?>,<? echo $row[csf("id")]; ?>,<? echo $within_group_arr[$row[csf("booking_id")]] . "," . $row[csf("buyer_id")] . "," . $buyer . "," . $row[csf("is_posted_account")]; ?>');">
					<td width="30"><?php echo $i; ?></td>
					<td width="60"><p><?php echo $row[csf("issue_number_prefix_num")]; ?></p></td>
					<td width="50"><p><?php echo $row[csf("year")]; ?></p></td>
					<td width="70"><p><?php echo change_date_format($row[csf("issue_date")]); ?></p></td>
					<td width="100"><p><?php echo $yarn_issue_purpose[$row[csf("issue_purpose")]]; ?></p></td>
					<td width="70"><p><?php echo $row[csf("challan_no")]; ?>&nbsp;</p></td>
					<td width="60" align="right"><?php echo number_format($row[csf("issue_quantity")], 2, '.', ''); ?></p></td>
					<td width="110"><p><?php echo $row[csf("booking_no")]; ?></p></td>
					<td width="100">
						<p>
							<?php
							if ($row[csf("knit_dye_source")] == 1) $knit_com = $company_arr[$row[csf("knit_dye_company")]];
							else $knit_com = $supplier_arr[$row[csf("knit_dye_company")]];
							echo $knit_com;
							?>
						</p>
					</td>
					<td width="115"><p><?php echo $buyer; ?>&nbsp;</p></td>
					<td width="85"><p><?php echo $row[csf("buyer_job_no")]; ?>&nbsp;</p></td>
					<td width="90"><p><?php echo $store_arr[$row[csf("store_id")]]; ?></p></td>
					<td>
						<p><?php echo $yes_no[$row[csf("ready_to_approve")]]; //if($row[csf("ready_to_approve")]!=1) else echo "";
						?>&nbsp;</p></td>
					</tr>
					<?php
					$i++;
				}
				?>
			</table>
		</div>
	</div>
	<?
	exit();
}

if ($action == "yarn_issue_print")
{
	extract($_REQUEST);
	echo load_html_head_contents("Yarn Issue Challan Print", "../../../", 1, 1, '', '', '');
	$data = explode('*', $data);
	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$supplier_library = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
	//$other_party_arr=return_library_array( "select id, other_party_name from  lib_other_party",'id','other_party_name');
	$store_arr = return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');
	$color_name_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	if($db_type==0)
	{
		$sql = "select a.id, a.issue_number, a.issue_date, a.issue_basis, a.issue_purpose, a.knit_dye_source, a.knit_dye_company, a.location_id, a.loan_party, a.challan_no, a.gate_pass_no, a.remarks, group_concat( b.job_no) as job_no, group_concat(b.buyer_id) as buyer_id, group_concat(b.style_ref_no) as style_ref_no 
		from inv_issue_master a, inv_transaction b 
		where a.id=b.mst_id and b.transaction_type=2 and a.entry_form=277 and b.entry_form=277 and b.status_active=1 and a.id='$data[1]'
		group by a.id, a.issue_number, a.issue_date, a.issue_basis, a.issue_purpose, a.knit_dye_source, a.knit_dye_company, a.location_id, a.loan_party, a.challan_no, a.gate_pass_no, a.remarks";
	}
	else
	{
		$sql = "select a.id, a.issue_number, a.issue_date, a.issue_basis, a.issue_purpose, a.knit_dye_source, a.knit_dye_company, a.location_id, a.loan_party, a.challan_no, a.gate_pass_no, a.remarks, listagg(cast(b.job_no as varchar2(4000)),',') within group (order by b.job_no) as job_no, listagg(cast(b.buyer_id as varchar2(4000)),',') within group (order by b.buyer_id) as buyer_id, listagg(cast(b.style_ref_no as varchar2(4000)),',') within group (order by b.style_ref_no) as style_ref_no, listagg(cast(b.requisition_no as varchar2(4000)),',') within group (order by b.requisition_no) as requisition_no 
		from inv_issue_master a, inv_transaction b 
		where a.id=b.mst_id and b.transaction_type=2 and a.entry_form=277 and b.entry_form=277 and b.status_active=1 and a.id='$data[1]'
		group by a.id, a.issue_number, a.issue_date, a.issue_basis, a.issue_purpose, a.knit_dye_source, a.knit_dye_company, a.location_id, a.loan_party, a.challan_no, a.gate_pass_no, a.remarks";
	}
	//echo $sql;die;

	$dataArray = sql_select($sql);
	$copyNo = "";
	for ($x = 1; $x <= 3; $x++)
	{

		if($x==1)
		{
			$copyNo ="<span style='font-size:x-large;'>1<sup>st</sup> Copy</span>";
		} else if($x==2){
			$copyNo ="<span style='font-size:x-large;'>2<sup>nd</sup> Copy</span>";
		} else {
			$copyNo ="<span style='font-size:x-large;'>3<sup>rd</sup> Copy</span>";
		}
		?>
		<div style="width:1020px; page-break-after:always;">
			<table width="1000" cellspacing="0" align="right" border="0" style="margin-right:-20px;">
				<tr class="form_caption">
					<td align="left" width="50"></td>
					<td colspan="5" align="center">
						<strong style="font-size:18px"><? echo $company_library[$data[0]]; ?></strong><br>
						<?
						echo show_company($data[0], '', array('city'));
						?>
					</td>
					<td colspan="2" style="color:black; font-weight:bold; text-align:center"><? echo $copyNo;?></td>
				</tr>
				<tr>
                	<td>&nbsp;</td>
					<td colspan="5" align="center" style="font-size:16px"><strong><u><? echo $data[2];?></u></strong></td>
					<td colspan="2">&nbsp;</td>
				</tr>
				<tr>
					<td width="110"><strong>Issue No :</strong></td>
					<td width="140"><? echo $dataArray[0][csf('issue_number')]; ?></td>
					<td width="110"><strong>Issue Date :</strong></td>
					<td width="140"><? echo change_date_format($dataArray[0][csf('issue_date')]);?></td>
                    <td width="110"><strong>Issue Purpose :</strong></td>
                    <td width="140"><? echo $yarn_issue_purpose[$dataArray[0][csf('issue_purpose')]];?></td>
					<td width="110"><strong>Knitting Source :</strong></td>
					<td><? echo $knitting_source[$dataArray[0][csf('knit_dye_source')]]; ?></td>
				</tr>
                <tr>
                    <td><strong>Issue To :</strong></td>
                    <td>
                        <?
                         if ($dataArray[0][csf('knit_dye_source')] == 1)
                            echo $company_library[$dataArray[0][csf('knit_dye_company')]];
                        else
                            echo $supplier_library[$dataArray[0][csf('knit_dye_company')]];
                        ?>
                    </td>
                    <td><strong>Location : </strong></td> 
                    <td><? echo return_field_value("location_name", "lib_location","id=".$dataArray[0][csf('location_id')], "location_name"); ?></td>
                    <td><strong>Gate Pass No. : </strong></td> 
                    <td>&nbsp;</td>
                    <td><strong>Lot Ratio No : </strong></td> 
                    <td><? echo implode(",",array_unique(explode(",",$dataArray[0][csf('requisition_no')]))); ?></td>
                </tr>
                <tr>
                    <td><strong>Buyer :</strong></td>
                    <td>
					<? 
					$buyer_id=implode(",",array_unique(explode(",",$dataArray[0][csf('buyer_id')])));
					$buyer_name = return_field_value("buyer_name", "lib_buyer","id=$buyer_id", "buyer_name");
					echo $buyer_name; 
					?>
                    </td>
                    <td><strong>Job No : </strong></td> 
                    <td><? echo implode(",",array_unique(explode(",",$dataArray[0][csf('job_no')]))); ?></td>
                    <td><strong>Style No : </strong></td> 
                    <td><? echo implode(",",array_unique(explode(",",$dataArray[0][csf('style_ref_no')]))); ?></td>
                    <td><strong>Remarks :</strong></td>
					<td><p><? echo $dataArray[0][csf('remarks')]; ?></p></td>
                </tr>	
            </table>
            <br>
            <table style="margin-right:-40px;" cellspacing="0" width="1000" border="1" rules="all" class="rpt_table">
                <thead style="font-size:13px">
                    <th width="30">SL</th>
                    <th width="60">Lot No</th>
                    <th width="120">Y. Type</th>
                    <th width="180">Item Details</th>
                    <th width="120">Yarn Color</th>
                    <th width="120">Yarn Supp</th>
                    <th width="80"><b>Issue Qty</b></th>
                    <th width="60"><b>UOM</b></th>
                    <th width="80">Bag & Cone</th>
                    <th>Store</th>
                </thead>
                <!--<tbody>-->
                <?
                $i = 1;
                $dtls_sql="select c.id as prod_id, c.lot, c.yarn_count_id, c.yarn_comp_type1st, c.yarn_comp_percent1st, c.yarn_type, c.color, c.product_name_details, b.id as trans_id, b.requisition_no, b.cons_uom, b.cons_quantity, b.return_qnty as returnable_qnty, b.store_id, b.no_of_bags, b.cone_per_bag, b.weight_per_bag, b.weight_per_cone, b.supplier_id
                from inv_issue_master a, inv_transaction b, product_details_master c  
                where a.id=b.mst_id and b.prod_id=c.id and a.entry_form=277 and b.entry_form=277 and b.transaction_type=2 and b.status_active=1 and b.is_deleted=0 and a.id=$data[1]";
                //echo $dtls_sql;die;
                $sql_result = sql_select($dtls_sql);
				//echo "<pre>";print_r($sql_result);//die;
                foreach ($sql_result as $row) 
                {
                    if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="font-size:13px">
                        <td><? echo $i; ?></td>
                        <td><p><? echo $row[csf('lot')]; ?>&nbsp;</p></td>
                        <td><p><? echo $yarn_type[$row[csf('yarn_type')]]; ?>&nbsp;</p></td>
                        <td><p><? echo $row[csf('product_name_details')];?>&nbsp;</p></td>
                        <td><p><? echo $color_name_arr[$row[csf('color')]]; ?>&nbsp;</p></td>
                        <td><p><? echo $supplier_library[$row[csf('supplier_id')]]; ?>&nbsp;</p></td>
                        <td align="right"><? echo number_format($row[csf('cons_quantity')], 3, '.', ''); ?></td>
                        <td align="center"><p><? echo $unit_of_measurement[$row[csf('cons_uom')]]; ?>&nbsp;</p></td>
                        <td align="center"><p><? echo 'N:' .$row[csf('no_of_bags')] . '<br>W:' . $row[csf('cone_per_bag')] ?>&nbsp;</p></td>
                        <td><p><? echo $store_arr[$row[csf('store_id')]]; ?></p></td>
                    </tr>
                    <?
                    $uom_unit = "LBS";
                    $uom_gm = "oz";
                    $tot_cons_quantity += $row[csf('cons_quantity')];
                    $tot_bag += $row[csf('no_of_bags')];
                    $tot_w_cone += $row[csf('cone_per_bag')];
                    $i++;
                }
                ?>
                <!--</tbody>
                <tfoot>-->
                	<tr bgcolor="#CCCCCC" style="font-size:13px">
                        <td align="right" colspan="6"><b>Total</b></td>
                        <td align="right"><b><? echo $format_total_amount = number_format($tot_cons_quantity, 3, '.', ''); ?></b></td>
                        <td align="right">&nbsp;</td>
                        <td align="center"><? echo 'N:' . number_format($tot_bag, 2);
                        echo '<br>W:' . number_format($tot_w_cone, 2); ?></td>
                        <td align="right">&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="10" align="left"><b>In  Word: <? echo number_to_words($format_total_amount, $uom_unit, $uom_gm); ?></b></td>
                    </tr>
                <!--</tfoot>-->
            </table>
            <?
			$tot_cons_quantity = $tot_bag = $tot_w_cone=0; 
			//echo "<br><br><br><br><br><br><br><br>";
			echo signature_table(277, $data[0], "1030px",'',0); 
			?>
		</div>
		<?
	}
	exit();
}