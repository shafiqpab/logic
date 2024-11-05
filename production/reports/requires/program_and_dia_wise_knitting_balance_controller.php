<?
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');

$_SESSION['page_permission']=$permission;
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action == "load_drop_down_knitting_com")
{
	if ($data == 1)
	{
		echo create_drop_down("cbo_company_name", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 order by comp.company_name", "id,company_name", 1, "--Select Company--", "", "load_drop_down( 'requires/program_and_dia_wise_knitting_balance_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'requires/program_and_dia_wise_knitting_balance_controller', this.value, 'load_drop_down_location', 'location_td' );load_drop_down( 'requires/program_and_dia_wise_knitting_balance_controller', this.value, 'load_drop_down_floor', 'floor_td' );", "");
	}
	else if ($data == 3)
	{
		echo create_drop_down("cbo_company_name", 130, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=20 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id,supplier_name", 1, "--Select Company--", 0, "load_drop_down( 'requires/program_and_dia_wise_knitting_balance_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );load_drop_down( 'requires/program_and_dia_wise_knitting_balance_controller', this.value, 'load_drop_down_floor', 'floor_td' );");
	}
	else
	{
		echo create_drop_down("cbo_company_name", 130, $blank_array, "", 1, "--Select Company--", 0, "");
	}
	exit();
	//load_drop_down( 'requires/program_and_dia_wise_knitting_balance_controller', this.value, 'load_drop_down_location', 'location_td' );
	//load_drop_down( 'requires/program_and_dia_wise_knitting_balance_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );
	//load_drop_down( 'requires/program_and_dia_wise_knitting_balance_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  $buyer_cond order by buyer_name","id,buyer_name", 1, "- All Buyer -", $selected, "" );     	 
	exit();
}

if ($action == "load_drop_down_location") 
{
	echo create_drop_down("cbo_location_name", 152, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name", "id,location_name", 1, "-- Select Location --", 0, "load_drop_down( 'requires/program_and_dia_wise_knitting_balance_controller', this.value+'_'+".$data.", 'load_drop_down_floor_location', 'floor_td' );");
	exit();
}

//load_drop_down_floor for onchange company
if ($action == "load_drop_down_floor") 
{
	$sql = "select id, floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and company_id=".$data." and id in(select floor_id from lib_machine_name where company_id=".$data." and category_id = 1 and status_active =1 and is_deleted=0) order by floor_name";
	echo create_drop_down( "cbo_floor", 120, $sql, "id,floor_name", 1, "-- Select --", $selected, "",0 );
	exit();
}

//load_drop_down_floor for onchange location
if ($action == "load_drop_down_floor_location") 
{
	$expData = explode('_', $data);
	$sql = "select id, floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id=".$expData[0]." and company_id=".$expData[1]." and id in(select floor_id from lib_machine_name where location_id=".$expData[0]." and company_id=".$expData[1]." and category_id = 1 and status_active =1 and is_deleted=0) order by floor_name";
	echo create_drop_down( "cbo_floor", 120, $sql,"id,floor_name", 1, "-- Select --", $selected, "",0 );
	exit();
}

$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
$yarn_count_library=return_library_array( "select id, yarn_count from lib_yarn_count", "id", "yarn_count"  );
$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
$brand_library=return_library_array( "select id,brand_name from lib_brand", "id", "brand_name"  );
$location_library=return_library_array( "select id,location_name from lib_location", "id", "location_name"  );
$construction_library=return_library_array( "select id,construction from lib_yarn_count_determina_mst", "id", "construction"  );
//$floor_dtls = return_library_array("SELECT id, floor_name FROM lib_prod_floor WHERE status_active = 1 AND production_process = 2", "id", "floor_name");
$floor_dtls = return_library_array("SELECT id, floor_name FROM lib_prod_floor WHERE status_active = 1", "id", "floor_name");

if($action=="job_no_popup")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>

	function js_set_value(str)
	{
		var splitData = str.split("_");
		$("#hide_job_id").val(splitData[0]); 
		$("#hide_job_no").val(splitData[1]); 
		parent.emailwindow.hide();
	}
	</script>
	</head>
	<body>
		<div align="center">
			<form name="styleRef_form" id="styleRef_form">
				<fieldset style="width:580px;">
					<table width="570" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
						<thead>
							<th>Buyer</th>
							<th>Search By</th>
							<th id="search_by_td_up" width="170">Please Enter Job No</th>
							<th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th> 					<input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
							<input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
						</thead>
						<tbody>
							<tr>
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
									<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $cbo_month_id; ?>', 'create_job_no_search_list_view', 'search_div', 'program_and_dia_wise_knitting_balance_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
								</td>
							</tr>
						</tbody>
					</table>
					<div style="margin-top:15px" id="search_div"></div>
				</fieldset>
			</form>
		</div>
	</body>           
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit(); 
}

if($action=="create_job_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$year_id=$data[4];
	$month_id=$data[5];
	//echo $month_id;
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and buyer_name=$data[1]";
	}
	$search_by=$data[2];
	$search_string="%".trim($data[3])."%";
	if($search_by==2) $search_field="style_ref_no"; else $search_field="job_no";
	//$year="year(insert_date)";
	if($db_type==0) $year_field="YEAR(insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(insert_date,'YYYY') as year";
	else $year_field="";
	if($db_type==0)
	{
		if($year_id!=0) $year_cond=" and year(insert_date)=$year_id"; else $year_cond="";	
	}
	else if($db_type==2)
	{
		$year_field_con=" and to_char(insert_date,'YYYY')";
		if($year_id!=0) $year_cond="$year_field_con=$year_id"; else $year_cond="";	
	}
	//if($month_id!=0) $month_cond=" and month(insert_date)=$month_id"; else $month_cond="";
	$arr=array (0=>$company_arr,1=>$buyer_arr);
	$sql= "select id, job_no, job_no_prefix_num, company_name, buyer_name, style_ref_no, $year_field from wo_po_details_master where status_active=1 and is_deleted=0 and $search_field like '$search_string' $buyer_id_cond $year_cond  order by job_no"; //and company_name=$company_id
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No", "120,130,80,60","600","240",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no", "",'','0,0,0,0,0','') ;
	exit(); 
} // Job Search end

if($action=="report_generate_inhouse")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$company_name= str_replace("'","",$cbo_company_name);
	$location_name= str_replace("'","",$cbo_location_name);
	$program_no= str_replace("'","",$txt_program_no);
	$txt_booking_id= str_replace("'","",$txt_booking_id);
	$cbo_year_selection = str_replace("'","",$cbo_year_selection);
	$cbo_year_selection = substr($cbo_year_selection, -2);
	$txt_job_id = str_replace("'","",$txt_job_id);
	$txt_dia = trim(str_replace("'","",$txt_dia));
	$cbo_color = str_replace("'","",$cbo_color);
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and e.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and e.buyer_id in (".str_replace("'","",$cbo_buyer_name).")";
	}
	if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
	{
		if($db_type==0)
		{
			$start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
			$end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
		}
		else if($db_type==2)
		{
			$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
			$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
		}
		$date_cond=" and b.receive_date between '$start_date' and '$end_date'";
	}
	
	$job_no=str_replace("'","",$txt_job_no);
	$job_no_cond="";
	if ($job_no=="") 
	{
		$job_no_cond=""; 
	}
	else 
	{
		if($txt_job_id)
		{
			$job_no_cond=" and a.job_no '%".$job_no."%'";
		}
		else
		{
			$job_no_cond .=" and a.job_no '%".$job_no."%'";
			$job_no_cond .=" and a.job_no '%-".$cbo_year_selection."-%'";
		}
		
	}

	if ($program_no) $program_no_cond=" and d.id = $program_no "; else $program_no_cond="";  

	$booking_no=str_replace("'","",$txt_booking_no);
	if($booking_no == "") {
		$booking_no_cond ="";
	} 
	else 
	{
		if($txt_booking_id)
		{
			$booking_no_cond=" and e.booking_no like '%".trim($booking_no) ."%' "; 
		}else{
			$booking_no_cond=" and e.booking_no like '%".trim($booking_no) ."' and e.booking_no like '%-$cbo_year_selection-%'"; 
		}
	}

	if($txt_dia) $dia_cond = " and d.machine_dia= '$txt_dia'"; else $dia_cond = "";
	if($cbo_color) $color_cond = " and d.color_id= '$cbo_color'"; else $color_cond = "";
	if($location_name) $location_cond = " and d.location_id= '$location_name'"; else $location_cond = "";

	$sql="select  b.receive_date as production_date,d.knitting_party, d.location_id, d.program_date, e.buyer_id, e.booking_no,d.id as program_no,d.start_date,d.end_date, c.fabric_desc, c.determination_id,d.stitch_length,c.gsm_weight, d.machine_dia,d.fabric_dia,d.width_dia_type,d.color_id,d.program_qnty,d.remarks
	from wo_booking_dtls a, inv_receive_master b, ppl_planning_entry_plan_dtls c, ppl_planning_info_entry_dtls d, ppl_planning_info_entry_mst e
	where c.dtls_id = d.id and d.mst_id = e.id and b.booking_id = d.id and a.booking_no = e.booking_no and b.receive_basis = 2 and b.entry_form = 2 and d.knitting_source = 1 and d.knitting_party = $company_name $location_cond $program_no_cond $booking_no_cond $job_no_cond $buyer_id_cond $dia_cond $color_cond
	$date_cond and c.status_active = 1 and d.status_active = 1 and b.status_active = 1 and a.status_active=1
	group by  d.id ,d.start_date,d.end_date, c.fabric_desc, c.determination_id,d.stitch_length,c.gsm_weight, d.machine_dia,d.fabric_dia,d.width_dia_type,
	d.color_id,d.program_qnty,  b.receive_date,d.knitting_party, d.location_id, d.program_date, e.buyer_id, e.booking_no,d.remarks
	order by b.receive_date,d.knitting_party, d.location_id";

	$nameArray=sql_select( $sql );
	//construction_library   company_library   location_library
	foreach ($nameArray as $row) 
	{
		$program_no_arr[$row[csf("program_no")]] = $row[csf("program_no")];

		$data_array[$row[csf("production_date")]][$company_library[$row[csf("knitting_party")]]."*".$location_library[$row[csf("location_id")]]][$row[csf("program_no")]]["program_qnty"] = $row[csf("program_qnty")];

		$data_array[$row[csf("production_date")]][$company_library[$row[csf("knitting_party")]]."*".$location_library[$row[csf("location_id")]]][$row[csf("program_no")]]["program_date"] = $row[csf("program_date")];

		$data_array[$row[csf("production_date")]][$company_library[$row[csf("knitting_party")]]."*".$location_library[$row[csf("location_id")]]][$row[csf("program_no")]]["buyer_id"] = $buyer_library[$row[csf("buyer_id")]];

		$data_array[$row[csf("production_date")]][$company_library[$row[csf("knitting_party")]]."*".$location_library[$row[csf("location_id")]]][$row[csf("program_no")]]["booking_no"] = $row[csf("booking_no")];

		$data_array[$row[csf("production_date")]][$company_library[$row[csf("knitting_party")]]."*".$location_library[$row[csf("location_id")]]][$row[csf("program_no")]]["start_date"] = $row[csf("start_date")];

		$data_array[$row[csf("production_date")]][$company_library[$row[csf("knitting_party")]]."*".$location_library[$row[csf("location_id")]]][$row[csf("program_no")]]["end_date"] = $row[csf("end_date")];
		$data_array[$row[csf("production_date")]][$company_library[$row[csf("knitting_party")]]."*".$location_library[$row[csf("location_id")]]][$row[csf("program_no")]]["construction"] = $construction_library[$row[csf("determination_id")]];
		$data_array[$row[csf("production_date")]][$company_library[$row[csf("knitting_party")]]."*".$location_library[$row[csf("location_id")]]][$row[csf("program_no")]]["fabric_desc"] = $row[csf("fabric_desc")];
		$data_array[$row[csf("production_date")]][$company_library[$row[csf("knitting_party")]]."*".$location_library[$row[csf("location_id")]]][$row[csf("program_no")]]["stitch_length"] = $row[csf("stitch_length")];
		$data_array[$row[csf("production_date")]][$company_library[$row[csf("knitting_party")]]."*".$location_library[$row[csf("location_id")]]][$row[csf("program_no")]]["gsm_weight"] = $row[csf("gsm_weight")];
		$data_array[$row[csf("production_date")]][$company_library[$row[csf("knitting_party")]]."*".$location_library[$row[csf("location_id")]]][$row[csf("program_no")]]["machine_dia"] = $row[csf("machine_dia")];
		$data_array[$row[csf("production_date")]][$company_library[$row[csf("knitting_party")]]."*".$location_library[$row[csf("location_id")]]][$row[csf("program_no")]]["fabric_dia"] = $row[csf("fabric_dia")];
		$data_array[$row[csf("production_date")]][$company_library[$row[csf("knitting_party")]]."*".$location_library[$row[csf("location_id")]]][$row[csf("program_no")]]["width_dia_type"] = $row[csf("width_dia_type")];
		$data_array[$row[csf("production_date")]][$company_library[$row[csf("knitting_party")]]."*".$location_library[$row[csf("location_id")]]][$row[csf("program_no")]]["color_id"] = $row[csf("color_id")];
		$data_array[$row[csf("production_date")]][$company_library[$row[csf("knitting_party")]]."*".$location_library[$row[csf("location_id")]]][$row[csf("program_no")]]["remarks"] = $row[csf("remarks")];

	}


	$program_nos = implode(",", array_filter($program_no_arr));
	if($program_nos=="") $program_nos=0;
	$programcond = $producedProgramNoCond = ""; 
	$programcond2 = $producedProgramNoCond2 = ""; 
	$program_no_arr=explode(",",$program_nos);
	if($db_type==2 && count($program_no_arr)>999)
	{
		$program_no_chunk=array_chunk($program_no_arr,999) ;
		foreach($program_no_chunk as $chunk_arr)
		{
			$programcond.=" b.id in(".implode(",",$chunk_arr).") or ";	
			$programcond2.=" a.booking_id in(".implode(",",$chunk_arr).") or ";	
		}
				
		$producedProgramNoCond.=" and (".chop($programcond,'or ').")";			
		$producedProgramNoCond2.=" and (".chop($programcond2,'or ').")";			
		
	}
	else
	{ 	
		if($program_nos)
		{
			$producedProgramNoCond=" and b.id in($program_nos)";
			$producedProgramNoCond2=" and a.booking_id in($program_nos)";
		}   
	}

	$yarn_description_res = sql_select("select b.id as program_no,a.prod_id,c.id, c.yarn_count_id,c.lot, c.brand
	from  ppl_yarn_requisition_entry a, ppl_planning_info_entry_dtls b, product_details_master c
	where a.knit_id  = b.id and a.prod_id = c.id and b.status_active = 1 and a.status_active = 1 and c.status_active = 1 $producedProgramNoCond
	group by b.id,a.prod_id,c.id, c.yarn_count_id,c.lot, c.brand");
	foreach ($yarn_description_res as $val) 
	{
		$yarn_description_arr[$val[csf("program_no")]]["ycount"] .= $yarn_count_library[$val[csf("yarn_count_id")]].",";
		$yarn_description_arr[$val[csf("program_no")]]["ylot"] .= $val[csf("lot")].",";
		$yarn_description_arr[$val[csf("program_no")]]["brand"] .= $brand_library[$val[csf("brand")]].",";
	}

	$production_qty_arr =array();$production_qty_date_arr =array();
	//$production_qty_res = sql_select("select b.grey_receive_qnty, a.booking_id as program_no, a.receive_date from inv_receive_master a, pro_grey_prod_entry_dtls b where a.id = b.mst_id and a.entry_form = 2 and a.receive_basis = 2 and b.status_active = 1 and b.is_deleted = 0 and a.status_active = 1 and a.is_deleted = 0 $producedProgramNoCond2 ");

	$production_qty_res = sql_select("select b.grey_receive_qnty, a.booking_id as program_no, a.receive_date, c.program_qnty from inv_receive_master a, pro_grey_prod_entry_dtls b ,ppl_planning_info_entry_dtls c where a.id = b.mst_id and a.booking_id = c.id and a.entry_form = 2 and a.receive_basis = 2 and b.status_active = 1 and b.is_deleted = 0 and a.status_active = 1 and a.is_deleted = 0 $producedProgramNoCond2 ");
	foreach ($production_qty_res as $value) 
	{
		$production_qty_date_arr[$value[csf("program_no")]][$value[csf("receive_date")]] += $value[csf("grey_receive_qnty")];
		$production_qty_arr[$value[csf("program_no")]]+= $value[csf("grey_receive_qnty")];

		$top_grand_total_program +=  $value[csf("grey_receive_qnty")];

		if(strtotime($value[csf("receive_date")]) >= strtotime($start_date) && strtotime($value[csf("receive_date")]) <= strtotime($end_date))
		{
			$top_grand_today_prod +=  $value[csf("grey_receive_qnty")];
		}

		if($programChk[$value[csf("program_no")]] =="")
		{
			$programChk[$value[csf("program_no")]] = $value[csf("program_no")];
			$top_grand_total_program_qnty += $value[csf("program_qnty")];
		}

		$top_grand_total_prod +=  $value[csf("grey_receive_qnty")];
	}

	
	
	ob_start();
	?>
	<fieldset style="width:1850px;">
		<table width="1330" cellspacing="0" cellpadding="0" border="0" rules="all" style="float: left;">
			<tr class="form_caption">
				<td colspan="16" align="center" style="border:none;font-size:16px; font-weight:bold"> <? echo $report_title; ?></td>
			</tr>
			<tr class="form_caption">
				<td colspan="16" align="center" style="border:none;"><? echo $company_library[$company_name]; ?></td>
			</tr>
			<tr class="form_caption">
				<td colspan="16" align="center" style="border:none;">&nbsp;</td>
			</tr>
		</table>
		<table width="500" cellspacing="0" cellpadding="0" border="0" rules="all" style="float: left;">
			<tr class="form_caption">
				<td colspan="6" align="center" style="border:none;font-size:16px; font-weight:bold"> <? //echo $report_title; ?></td>
			</tr>
			<tr class="form_caption"> 
				<td align="center" style="border: 1px solid black;" width="80">Grand Total</td>
				<td align="center" style="border: 1px solid black;" width="80" id="top_grand_total_program"><? echo $top_grand_total_program_qnty;?></td>
				<td align="center" style="border: 1px solid black;" width="80" id="top_grand_today_prod"><? echo $top_grand_today_prod;?></td>
				<td align="center" style="border: 1px solid black;" width="80" id="top_grand_total_prod"><? echo $top_grand_total_prod;?></td>
				<td align="center" style="border: 1px solid black;" width="80" id="top_grand_bal_prod">
					<? 
					 echo $top_grand_total_prod - $top_grand_today_prod;
					?>
				</td>
				<td align="center" width="100">&nbsp;</td>
			</tr>
			<tr class="form_caption"> 
				<td align="center" width="160" colspan="2">&nbsp;</td>
				<td align="right" width="340" colspan="4" style="color: black;">Report Print Date: <? echo date("d-m-Y"); ?></td>
			</tr>
		</table>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1830" class="rpt_table" >
			<thead>
				<tr>
					<th width="80">Program Date</th>
					<th width="80">Buyer</th>
					<th width="80">Booking</th>
					<th width="80">Program No.</th>
					<th width="80">Knit Start Date</th>
					<th width="80">Knit Com Date</th>
					<th width="80">Yarn Count</th>
					<th width="80">Yarn Lot</th>
					<th width="80">Yarn Brand</th>
					<th width="100">Fabric Construction</th>
					<th width="100">Fabric Composition</th>
					<th width="80">S/L</th>
					<th width="80">F/GSM</th>
					<th width="80">M/Dia</th>
					<th width="80">F/Dia</th>
					<th width="80">Width Type</th>
					<th width="80">Fabric Color</th>
					<th width="80"><p style="word-break: break-all;word-wrap: break-word;">Program Qnty.(Kgs)</p></th>
					<th width="80">Today Knit (Kg)</th>
					<th width="80">Total Knit Qty.(Kg)</th>
					<th width="80"><p style="word-break: break-all;word-wrap: break-word;">Total Bala.Qty(Kg)</p></th>
					<th>Remarks</th>
				</tr>
			</thead>
		</table>
		<div style="width:1848px; overflow-y:scroll; max-height:450px;" id="scroll_body">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1830" class="rpt_table" id="tbl_list_search">
				<tbody>
				<?//location_library
				$production_date_arr = array();$comp_loc_arr = array();
				
				$i = 1;
				foreach ($data_array as $production_date => $production_date_data) 
				{
					foreach ($production_date_data as $company_location => $company_location_data) 
					{
						$comp_loc_arr=array();
						foreach ($company_location_data as $program_id => $row) 
						{
							
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							if($production_date_arr[$production_date]=="")
							{
								$production_date_arr[$production_date] = $production_date;
								?>
								<tr style="font-weight: bold;">
									<td colspan="22"><? echo "Production Date : ".$production_date;?></td>
								</tr>
								<?
							}
							if($comp_loc_arr[$company_location]=="")
							{
								$comp_loc_arr[$company_location] = $company_location;
								$comLocArr = explode("*", $company_location);
								?>
								<tr style="font-weight: bold;">
									<td colspan="22"><? echo $comLocArr[0]." , ". $comLocArr[1];?></td>
								</tr>
								<?
							}
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>"> 
								<td width="80"><? echo $row["program_date"];?></td>
								<td width="80"><p style="word-break: break-all;word-wrap: break-word;"><? echo $row["buyer_id"];?></p></td>
								<td width="80"><p><? echo $row["booking_no"];?></p></td>
								<td width="80"><? echo $program_id;?></td>
								<td width="80"><? echo $row["start_date"];?></td>
								<td width="80"><? echo $row["end_date"];?></td>
								<td width="80"><p style="word-break: break-all;word-wrap: break-word;"><? echo implode(",",array_unique(array_filter(explode(",", $yarn_description_arr[$program_id]["ycount"]))));?></p></td>
								<td width="80"><p style="word-break: break-all;word-wrap: break-word;"><? echo implode(",",array_unique(array_filter(explode(",", $yarn_description_arr[$program_id]["ylot"]))));?></p></td>
								<td width="80"><p style="word-break: break-all;word-wrap: break-word;"><? echo implode(",",array_unique(array_filter(explode(",", $yarn_description_arr[$program_id]["brand"]))));?></p></td>
								<td width="100"><p style="word-break: break-all;word-wrap: break-word;"><? echo $row["construction"]?></p></td>
								<td width="100"><p style="word-break: break-all;word-wrap: break-word;"><? echo $row["fabric_desc"]?></p></td>
								<td width="80"><p style="word-break: break-all;word-wrap: break-word;"><? echo $row["stitch_length"]?></p></td>
								<td width="80"><p style="word-break: break-all;word-wrap: break-word;"><? echo $row["gsm_weight"]?></p></td>
								<td width="80"><p style="word-break: break-all;word-wrap: break-word;"><? echo $row["machine_dia"]?></p></td>
								<td width="80"><p style="word-break: break-all;word-wrap: break-word;"><? echo $row["fabric_dia"]?></p></td>
								<td width="80"><p style="word-break: break-all;word-wrap: break-word;"><? echo $fabric_typee[$row["width_dia_type"]]?></p></td>
								<td width="80"><p style="word-break: break-all;word-wrap: break-word;">
									<? 
									$colors_name="";
									foreach(explode(",",$row["color_id"]) as $colorId){
										$colors_name .= $color_library[$colorId].",";
									}
									echo chop($colors_name,",");
									?></p>
								</td>
								<td width="80"><p><? echo number_format($row["program_qnty"],2,'.','');?></p></td>
								<td width="80">
									<p>
										<? 
										$today_production = $production_qty_date_arr[$program_id][$production_date];
										echo number_format($today_production,2,'.','');
										?>
									</p>
								</td>
								<td width="80">
									<p>
										<? 
											$program_production = $production_qty_arr[$program_id];
											echo number_format($program_production,2,'.','');
										?>
									</p>
									</td>
								<td width="80">
									<p>
										<? 
										$bal_production = $program_production-$today_production;
										echo number_format($bal_production,2,'.','');
										?>
									</p>
								</td>
								<td><p style="word-break: break-all;word-wrap: break-word;"><? echo $row["remarks"];?></p></td>
							</tr>

							<?
							$i++;

							$subCompLocTotProgQnty += $row["program_qnty"]; 
							$subCompLocTotTodayProduction += $today_production;
							$subCompLocTotProgram_production += $program_production;
							$subCompLocTotBal_production += $bal_production;

							$subDateProgSubTot += $row["program_qnty"]; 
							$subDateTotTodayProduction += $today_production;
							$subDateTotProgram_production += $program_production;
							$subDateTotBal_production += $bal_production;

							$grandDateProgSubTot += $row["program_qnty"]; 
							$grandDateTotTodayProduction += $today_production;
							$grandDateTotProgram_production += $program_production;
							$grandDateTotBal_production += $bal_production;
							
						}
						?>
							<tr style="background-color: #eee;">
								<td colspan="17"> Company Location Total :</td>
								<td><? echo $subCompLocTotProgQnty;?></td>
								<td><? echo $subCompLocTotTodayProduction;?></td>
								<td><? echo $subCompLocTotProgram_production;?></td>
								<td><? echo $subCompLocTotBal_production;?></td>
								<td>&nbsp;</td>
							</tr>
						<?
						$subCompLocTotProgQnty=0; $subCompLocTotTodayProduction=0;$subCompLocTotProgram_production=0;$subCompLocTotBal_production=0;
					}
					?>
						<tr style="background-color: #ccc; font-size: 13">
							<td colspan="17"> Production Total :</td>
							<td><? echo $subDateProgSubTot;?></td>
							<td><? echo $subDateTotTodayProduction;?></td>
							<td><? echo $subDateTotProgram_production;?></td>
							<td><? echo $subDateTotBal_production;?></td>
							<td>&nbsp;</td>
						</tr>
					<?
					$subDateProgSubTot=0;$subDateTotTodayProduction=0;$subDateTotProgram_production=0;$subDateTotBal_production=0;
				}
				?>
					<tr style="background-color: #ccc; font-size: 13;border-top: 2px solid black;font-weight: bold;">
						<td colspan="17"><b>Grand Total :</b></td>
						<td id="grand_program_qnty"><? echo $grandDateProgSubTot;?></td>
						<td id="grand_today_prod_qnty"><? echo $grandDateTotTodayProduction;?></td>
						<td id="grand_total_prod_qnty"><? echo $grandDateTotProgram_production;?></td>
						<td id="grand_bal_prod_qnty"><? echo $grandDateTotBal_production;?></td>
						<td>&nbsp;</td>
					</tr>
				
				</tbody>
			</table>
		</div>
	</fieldset>
	<?
	$html = ob_get_contents();
    ob_clean();
    foreach (glob("$user_id*.xls") as $filename) {
        @unlink($filename);
    }
    $name = time();
    $filename = $user_id . "_" . $name . ".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, $html);
    echo "$html**$filename";
	exit();
}

if($action=="report_generate_outbound")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$company_name= str_replace("'","",$cbo_company_name);
	$program_no= str_replace("'","",$txt_program_no);
	$txt_booking_id= str_replace("'","",$txt_booking_id);
	$cbo_year_selection = str_replace("'","",$cbo_year_selection);
	$cbo_year_selection = substr($cbo_year_selection, -2);
	$txt_job_id = str_replace("'","",$txt_job_id);
	$txt_dia = trim(str_replace("'","",$txt_dia));
	$cbo_color = str_replace("'","",$cbo_color);
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and e.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and e.buyer_id in (".str_replace("'","",$cbo_buyer_name).")";
	}
	if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
	{
		if($db_type==0)
		{
			$start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
			$end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
		}
		else if($db_type==2)
		{
			$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
			$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
		}
		$date_cond=" and b.receive_date between '$start_date' and '$end_date'";
	}
	
	$job_no=str_replace("'","",$txt_job_no);
	$job_no_cond="";
	if ($job_no=="") 
	{
		$job_no_cond=""; 
	}
	else 
	{
		if($txt_job_id)
		{
			$job_no_cond=" and a.job_no '%".$job_no."%'";
		}
		else
		{
			$job_no_cond .=" and a.job_no '%".$job_no."%'";
			$job_no_cond .=" and a.job_no '%-".$cbo_year_selection."-%'";
		}
		
	}

	if ($program_no) $program_no_cond=" and d.id = $program_no ";  else $program_no_cond="";

	$booking_no=str_replace("'","",$txt_booking_no);
	if($booking_no == "") {
		$booking_no_cond ="";
	} 
	else 
	{
		if($txt_booking_id)
		{
			$booking_no_cond=" and e.booking_no like '%".trim($booking_no) ."%' "; 
		}else{
			$booking_no_cond=" and e.booking_no like '%".trim($booking_no) ."' and e.booking_no like '%-$cbo_year_selection-%'"; 
		}
	}

	if($txt_dia) $dia_cond = " and d.machine_dia= '$txt_dia'"; else $dia_cond = "";
	if($cbo_color) $color_cond = " and d.color_id= '$cbo_color'"; else $color_cond = "";

	$sql="select  b.receive_date as production_date,d.knitting_party, d.location_id, d.program_date, e.buyer_id, e.booking_no,d.id as program_no,d.start_date,d.end_date, c.fabric_desc, c.determination_id,d.stitch_length,c.gsm_weight, d.machine_dia,d.fabric_dia,d.width_dia_type,d.color_id,d.program_qnty,d.remarks
	from wo_booking_dtls a, inv_receive_master b, ppl_planning_entry_plan_dtls c, ppl_planning_info_entry_dtls d, ppl_planning_info_entry_mst e
	where c.dtls_id = d.id and d.mst_id = e.id and b.booking_id = d.id and a.booking_no = e.booking_no and b.receive_basis = 2 and b.entry_form = 2 and d.knitting_source = 3 and d.knitting_party = $company_name $program_no_cond $booking_no_cond $job_no_cond $buyer_id_cond $dia_cond $color_cond
	$date_cond and c.status_active = 1 and d.status_active = 1 and b.status_active = 1 and a.status_active=1
	group by  d.id ,d.start_date,d.end_date, c.fabric_desc, c.determination_id,d.stitch_length,c.gsm_weight, d.machine_dia,d.fabric_dia,d.width_dia_type,
	d.color_id,d.program_qnty,  b.receive_date,d.knitting_party, d.location_id, d.program_date, e.buyer_id, e.booking_no,d.remarks
	order by b.receive_date,d.knitting_party, d.location_id";

	$nameArray=sql_select( $sql );
	//construction_library   company_library   location_library
	foreach ($nameArray as $row) 
	{
		$program_no_arr[$row[csf("program_no")]] = $row[csf("program_no")];

		$data_array[$row[csf("production_date")]][$company_library[$row[csf("knitting_party")]]][$row[csf("program_no")]]["program_qnty"] = $row[csf("program_qnty")];

		$data_array[$row[csf("production_date")]][$company_library[$row[csf("knitting_party")]]][$row[csf("program_no")]]["program_date"] = $row[csf("program_date")];

		$data_array[$row[csf("production_date")]][$company_library[$row[csf("knitting_party")]]][$row[csf("program_no")]]["buyer_id"] = $buyer_library[$row[csf("buyer_id")]];

		$data_array[$row[csf("production_date")]][$company_library[$row[csf("knitting_party")]]][$row[csf("program_no")]]["booking_no"] = $row[csf("booking_no")];

		$data_array[$row[csf("production_date")]][$company_library[$row[csf("knitting_party")]]][$row[csf("program_no")]]["start_date"] = $row[csf("start_date")];

		$data_array[$row[csf("production_date")]][$company_library[$row[csf("knitting_party")]]][$row[csf("program_no")]]["end_date"]= $row[csf("end_date")];
		$data_array[$row[csf("production_date")]][$company_library[$row[csf("knitting_party")]]][$row[csf("program_no")]]["construction"]= $construction_library[$row[csf("determination_id")]];
		$data_array[$row[csf("production_date")]][$company_library[$row[csf("knitting_party")]]][$row[csf("program_no")]]["fabric_desc"]= $row[csf("fabric_desc")];
		$data_array[$row[csf("production_date")]][$company_library[$row[csf("knitting_party")]]][$row[csf("program_no")]]["stitch_length"] = $row[csf("stitch_length")];
		$data_array[$row[csf("production_date")]][$company_library[$row[csf("knitting_party")]]][$row[csf("program_no")]]["gsm_weight"]= $row[csf("gsm_weight")];
		$data_array[$row[csf("production_date")]][$company_library[$row[csf("knitting_party")]]][$row[csf("program_no")]]["machine_dia"]= $row[csf("machine_dia")];
		$data_array[$row[csf("production_date")]][$company_library[$row[csf("knitting_party")]]][$row[csf("program_no")]]["fabric_dia"] = $row[csf("fabric_dia")];
		$data_array[$row[csf("production_date")]][$company_library[$row[csf("knitting_party")]]][$row[csf("program_no")]]["width_dia_type"] = $row[csf("width_dia_type")];
		$data_array[$row[csf("production_date")]][$company_library[$row[csf("knitting_party")]]][$row[csf("program_no")]]["color_id"] = $row[csf("color_id")];
		$data_array[$row[csf("production_date")]][$company_library[$row[csf("knitting_party")]]][$row[csf("program_no")]]["remarks"] = $row[csf("remarks")];

	}


	$program_nos = implode(",", array_filter($program_no_arr));
	if($program_nos=="") $program_nos=0;
	$programcond = $producedProgramNoCond = ""; 
	$programcond2 = $producedProgramNoCond2 = ""; 
	$program_no_arr=explode(",",$program_nos);
	if($db_type==2 && count($program_no_arr)>999)
	{
		$program_no_chunk=array_chunk($program_no_arr,999) ;
		foreach($program_no_chunk as $chunk_arr)
		{
			$programcond.=" b.id in(".implode(",",$chunk_arr).") or ";	
			$programcond2.=" a.booking_id in(".implode(",",$chunk_arr).") or ";	
		}
				
		$producedProgramNoCond.=" and (".chop($programcond,'or ').")";			
		$producedProgramNoCond2.=" and (".chop($programcond2,'or ').")";			
		
	}
	else
	{ 	
		if($program_nos)
		{
			$producedProgramNoCond=" and b.id in($program_nos)";
			$producedProgramNoCond2=" and a.booking_id in($program_nos)";
		}   
	}

	$yarn_description_res = sql_select("select b.id as program_no,a.prod_id,c.id, c.yarn_count_id,c.lot, c.brand
	from  ppl_yarn_requisition_entry a, ppl_planning_info_entry_dtls b, product_details_master c
	where a.knit_id  = b.id and a.prod_id = c.id and b.status_active = 1 and a.status_active = 1 and c.status_active = 1 $producedProgramNoCond
	group by b.id,a.prod_id,c.id, c.yarn_count_id,c.lot, c.brand");
	foreach ($yarn_description_res as $val) 
	{
		$yarn_description_arr[$val[csf("program_no")]]["ycount"] .= $yarn_count_library[$val[csf("yarn_count_id")]].",";
		$yarn_description_arr[$val[csf("program_no")]]["ylot"] .= $val[csf("lot")].",";
		$yarn_description_arr[$val[csf("program_no")]]["brand"] .= $brand_library[$val[csf("brand")]].",";
	}

	$production_qty_arr =array();$production_qty_date_arr =array();

	$production_qty_res = sql_select("select b.grey_receive_qnty, a.booking_id as program_no, a.receive_date, c.program_qnty from inv_receive_master a, pro_grey_prod_entry_dtls b ,ppl_planning_info_entry_dtls c where a.id = b.mst_id and a.booking_id = c.id and a.entry_form = 2 and a.receive_basis = 2 and b.status_active = 1 and b.is_deleted = 0 and a.status_active = 1 and a.is_deleted = 0 $producedProgramNoCond2 ");
	foreach ($production_qty_res as $value) 
	{
		$production_qty_date_arr[$value[csf("program_no")]][$value[csf("receive_date")]] += $value[csf("grey_receive_qnty")];
		$production_qty_arr[$value[csf("program_no")]]+= $value[csf("grey_receive_qnty")];

		$top_grand_total_program +=  $value[csf("grey_receive_qnty")];

		if(strtotime($value[csf("receive_date")]) >= strtotime($start_date) && strtotime($value[csf("receive_date")]) <= strtotime($end_date))
		{
			$top_grand_today_prod +=  $value[csf("grey_receive_qnty")];
		}

		if($programChk[$value[csf("program_no")]] =="")
		{
			$programChk[$value[csf("program_no")]] = $value[csf("program_no")];
			$top_grand_total_program_qnty += $value[csf("program_qnty")];
		}

		$top_grand_total_prod +=  $value[csf("grey_receive_qnty")];
	}
	
	ob_start();
	?>
	<fieldset style="width:1850px;">
		<table width="1330" cellspacing="0" cellpadding="0" border="0" rules="all" style="float: left;">
			<tr class="form_caption">
				<td colspan="16" align="center" style="border:none;font-size:16px; font-weight:bold"> <? echo $report_title; ?></td>
			</tr>
			<tr class="form_caption">
				<td colspan="16" align="center"><? echo $company_library[$company_name]; ?></td>
			</tr>
			<tr class="form_caption">
				<td colspan="16" align="center">&nbsp;</td>
			</tr>
		</table>
		<table width="500" cellspacing="0" cellpadding="0" border="0" rules="all" style="float: left;">
			<tr class="form_caption">
				<td colspan="6" align="center" style="border:none;font-size:16px; font-weight:bold"> <? //echo $report_title; ?></td>
			</tr>
			<tr class="form_caption"> 
				<td align="center" style="border: 1px solid black;" width="80">Grand Total</td>
				<td align="center" style="border: 1px solid black;" width="80" id="top_grand_total_program"><? echo $top_grand_total_program_qnty;?></td>
				<td align="center" style="border: 1px solid black;" width="80" id="top_grand_today_prod"><? echo $top_grand_today_prod;?></td>
				<td align="center" style="border: 1px solid black;" width="80" id="top_grand_total_prod"><? echo $top_grand_total_prod;?></td>
				<td align="center" style="border: 1px solid black;" width="80" id="top_grand_bal_prod">
					<? 
					 echo $top_grand_total_prod - $top_grand_today_prod;
					?>
				</td>
				<td align="center" width="100">&nbsp;</td>
			</tr>
			<tr class="form_caption"> 
				<td align="center" width="160" colspan="2">&nbsp;</td>
				<td align="right" width="340" colspan="4" style="color: black;">Report Print Date: <? echo date("d-m-Y"); ?></td>
			</tr>
		</table>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1830" class="rpt_table" >
			<thead>
				<tr>
					<th width="80">Program Date</th>
					<th width="80">Buyer</th>
					<th width="80">Booking</th>
					<th width="80">Program No.</th>
					<th width="80">Knit Start Date</th>
					<th width="80">Knit Com Date</th>
					<th width="80">Yarn Count</th>
					<th width="80">Yarn Lot</th>
					<th width="80">Yarn Brand</th>
					<th width="100">Fabric Construction</th>
					<th width="100">Fabric Composition</th>
					<th width="80">S/L</th>
					<th width="80">F/GSM</th>
					<th width="80">M/Dia</th>
					<th width="80">F/Dia</th>
					<th width="80">Width Type</th>
					<th width="80">Fabric Color</th>
					<th width="80"><p style="word-break: break-all;word-wrap: break-word;">Program Qnty.(Kgs)</p></th>
					<th width="80">Today Knit (Kg)</th>
					<th width="80">Total Knit Qty.(Kg)</th>
					<th width="80"><p style="word-break: break-all;word-wrap: break-word;">Total Bala.Qty(Kg)</p></th>
					<th>Remarks</th>
				</tr>
			</thead>
		</table>
		<div style="width:1848px; overflow-y:scroll; max-height:450px;" id="scroll_body">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1830" class="rpt_table" id="tbl_list_search">
				<tbody>
				<?//location_library
				$production_date_arr = array();$comp_loc_arr = array();
				
				$i = 1;
				foreach ($data_array as $production_date => $production_date_data) 
				{
					foreach ($production_date_data as $company => $company_data) 
					{
						$comp_loc_arr=array();
						foreach ($company_data as $program_id => $row) 
						{
							
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							if($production_date_arr[$production_date]=="")
							{
								$production_date_arr[$production_date] = $production_date;
								?>
								<tr style="font-weight: bold;">
									<td colspan="22"><? echo "Production Date : ".$production_date;?></td>
								</tr>
								<?
							}
							if($comp_loc_arr[$company]=="")
							{
								$comp_loc_arr[$company] = $company;
								?>
								<tr style="font-weight: bold;">
									<td colspan="22"><? echo $company;?></td>
								</tr>
								<?
							}
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>"> 
								<td width="80"><? echo $row["program_date"];?></td>
								<td width="80"><p style="word-break: break-all;word-wrap: break-word;"><? echo $row["buyer_id"];?></p></td>
								<td width="80"><? echo $row["booking_no"];?></td>
								<td width="80"><? echo $program_id;?></td>
								<td width="80"><? echo $row["start_date"];?></td>
								<td width="80"><? echo $row["end_date"];?></td>
								<td width="80"><p style="word-break: break-all;word-wrap: break-word;"><? echo implode(",",array_unique(array_filter(explode(",", $yarn_description_arr[$program_id]["ycount"]))));?></p></td>
								<td width="80"><p style="word-break: break-all;word-wrap: break-word;"><? echo implode(",",array_unique(array_filter(explode(",", $yarn_description_arr[$program_id]["ylot"]))));?></p></td>
								<td width="80"><p style="word-break: break-all;word-wrap: break-word;"><? echo implode(",",array_unique(array_filter(explode(",", $yarn_description_arr[$program_id]["brand"]))));?></p></td>
								<td width="100"><p style="word-break: break-all;word-wrap: break-word;"><? echo $row["construction"]?></p></td>
								<td width="100"><p style="word-break: break-all;word-wrap: break-word;"><? echo $row["fabric_desc"]?></p></td>
								<td width="80"><p style="word-break: break-all;word-wrap: break-word;"><? echo $row["stitch_length"]?></p></td>
								<td width="80"><p style="word-break: break-all;word-wrap: break-word;"><? echo $row["gsm_weight"]?></p></td>
								<td width="80"><p style="word-break: break-all;word-wrap: break-word;"><? echo $row["machine_dia"]?></p></td>
								<td width="80"><p style="word-break: break-all;word-wrap: break-word;"><? echo $row["fabric_dia"]?></p></td>
								<td width="80"><p style="word-break: break-all;word-wrap: break-word;"><? echo $fabric_typee[$row["width_dia_type"]]?></p></td>
								<td width="80">
									<p style="word-break: break-all;word-wrap: break-word;">
										<? 
										$colors_name="";
										foreach(explode(",",$row["color_id"]) as $colorId){
											$colors_name .= $color_library[$colorId].",";
										}
										echo chop($colors_name,",");
										?>	
									</p>
								</td>
								<td width="80"><p><? echo number_format($row["program_qnty"],2,'.','');?></p></td>
								<td width="80">
									<p>
										<? 
										$today_production = $production_qty_date_arr[$program_id][$production_date];
										echo number_format($today_production,2,'.','');
										?>
									</p>
								</td>
								<td width="80">
									<p>
										<? 
											$program_production = $production_qty_arr[$program_id];
											echo number_format($program_production,2,'.','');
										?>
									</p>
									</td>
								<td width="80">
									<p>
										<? 
										$bal_production = $program_production-$today_production;
										echo number_format($bal_production,2,'.','');
										?>
									</p>
								</td>
								<td><p style="word-break: break-all;word-wrap: break-word;"><? echo $row["remarks"];?></p></td>
							</tr>

							<?
							$i++;

							$subCompLocTotProgQnty += $row["program_qnty"]; 
							$subCompLocTotTodayProduction += $today_production;
							$subCompLocTotProgram_production += $program_production;
							$subCompLocTotBal_production += $bal_production;

							$subDateProgSubTot += $row["program_qnty"]; 
							$subDateTotTodayProduction += $today_production;
							$subDateTotProgram_production += $program_production;
							$subDateTotBal_production += $bal_production;

							$grandDateProgSubTot += $row["program_qnty"]; 
							$grandDateTotTodayProduction += $today_production;
							$grandDateTotProgram_production += $program_production;
							$grandDateTotBal_production += $bal_production;
							
						}
						?>
							<tr style="background-color: #eee;">
								<td colspan="17"> Company Total :</td>
								<td><? echo $subCompLocTotProgQnty;?></td>
								<td><? echo $subCompLocTotTodayProduction;?></td>
								<td><? echo $subCompLocTotProgram_production;?></td>
								<td><? echo $subCompLocTotBal_production;?></td>
								<td>&nbsp;</td>
							</tr>
						<?
						$subCompLocTotProgQnty=0; $subCompLocTotTodayProduction=0;$subCompLocTotProgram_production=0;$subCompLocTotBal_production=0;
					}
					?>
						<tr style="background-color: #ccc; font-size: 13">
							<td colspan="17"> Production Total :</td>
							<td><? echo $subDateProgSubTot;?></td>
							<td><? echo $subDateTotTodayProduction;?></td>
							<td><? echo $subDateTotProgram_production;?></td>
							<td><? echo $subDateTotBal_production;?></td>
							<td>&nbsp;</td>
						</tr>
					<?
					$subDateProgSubTot=0;$subDateTotTodayProduction=0;$subDateTotProgram_production=0;$subDateTotBal_production=0;
				}
				?>
					<tr style="background-color: #ccc; font-size: 13;border-top: 2px solid black;font-weight: bold;">
						<td colspan="17"><b>Grand Total :</b></td>
						<td id="grand_program_qnty"><? echo $grandDateProgSubTot;?></td>
						<td id="grand_today_prod_qnty"><? echo $grandDateTotTodayProduction;?></td>
						<td id="grand_total_prod_qnty"><? echo $grandDateTotProgram_production;?></td>
						<td id="grand_bal_prod_qnty"><? echo $grandDateTotBal_production;?></td>
						<td>&nbsp;</td>
					</tr>
				</tbody>
			</table>
		</div>
	</fieldset>
	<?
	$html = ob_get_contents();
    ob_clean();
    foreach (glob("$user_id*.xls") as $filename) {
        @unlink($filename);
    }
    $name = time();
    $filename = $user_id . "_" . $name . ".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, $html);
    echo "$html**$filename";
	exit();
}

if($action=="report_generate_inhouse_2")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$company_name= str_replace("'","",$cbo_company_name);
	$location_name= str_replace("'","",$cbo_location_name);
	$program_no= str_replace("'","",$txt_program_no);
	$txt_booking_id= str_replace("'","",$txt_booking_id);
	$cbo_year_selection = str_replace("'","",$cbo_year_selection);
	$cbo_year_selection = substr($cbo_year_selection, -2);
	$txt_job_id = str_replace("'","",$txt_job_id);
	$txt_dia = trim(str_replace("'","",$txt_dia));
	$cbo_color = str_replace("'","",$cbo_color);
	$cbo_floor = str_replace("'","",$cbo_floor);
	
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and e.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and e.buyer_id in (".str_replace("'","",$cbo_buyer_name).")";
	}
	if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
	{
		if($db_type==0)
		{
			$start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
			$end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
			$productionFromDate=change_date_format(str_replace("'","",$txt_prod_from_date),"yyyy-mm-dd","");
			$productionToDate=change_date_format(str_replace("'","",$txt_prod_to_date),"yyyy-mm-dd","");
		}
		else if($db_type==2)
		{
			$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
			$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
			$productionFromDate=change_date_format(str_replace("'","",$txt_prod_from_date),"","",1);
			$productionToDate=change_date_format(str_replace("'","",$txt_prod_to_date),"","",1);
		}
		$date_cond=" and b.receive_date between '$start_date' and '$end_date'";
	}
	
	$job_no=str_replace("'","",$txt_job_no);
	$job_no_cond="";
	if ($job_no=="") 
	{
		$job_no_cond=""; 
	}
	else 
	{
		if($txt_job_id)
		{
			$job_no_cond=" and a.job_no '%".$job_no."%'";
		}
		else
		{
			$job_no_cond .=" and a.job_no '%".$job_no."%'";
			$job_no_cond .=" and a.job_no '%-".$cbo_year_selection."-%'";
		}
	}

	if ($program_no) $program_no_cond=" and d.id = $program_no "; else $program_no_cond="";  

	$booking_no=str_replace("'","",$txt_booking_no);
	if($booking_no == "")
	{
		$booking_no_cond ="";
	} 
	else 
	{
		if($txt_booking_id)
		{
			$booking_no_cond=" and e.booking_no like '%".trim($booking_no) ."%' "; 
		}else{
			$booking_no_cond=" and e.booking_no like '%".trim($booking_no) ."' and e.booking_no like '%-$cbo_year_selection-%'"; 
		}
	}

	if($txt_dia) $dia_cond = " and d.machine_dia= '$txt_dia'"; else $dia_cond = "";
	if($cbo_color) $color_cond = " and d.color_id= '$cbo_color'"; else $color_cond = "";
	if($location_name) $location_cond = " and d.location_id= '$location_name'"; else $location_cond = "";

	/*$sql="select  b.receive_date as production_date,d.knitting_party, d.location_id, d.program_date, e.buyer_id, e.booking_no,d.id as program_no,d.start_date,d.end_date, c.fabric_desc, c.determination_id,d.stitch_length,c.gsm_weight, d.machine_dia,d.fabric_dia,d.width_dia_type,d.color_id,d.program_qnty,d.remarks
	from wo_booking_dtls a, inv_receive_master b, ppl_planning_entry_plan_dtls c, ppl_planning_info_entry_dtls d, ppl_planning_info_entry_mst e
	where c.dtls_id = d.id and d.mst_id = e.id and b.booking_id = d.id and a.booking_no = e.booking_no and b.receive_basis = 2 and b.entry_form = 2 and d.knitting_source = 1 and d.knitting_party = $company_name $location_cond $program_no_cond $booking_no_cond $job_no_cond $buyer_id_cond $dia_cond $color_cond
	$date_cond and c.status_active = 1 and d.status_active = 1 and b.status_active = 1 and a.status_active=1
	group by  d.id ,d.start_date,d.end_date, c.fabric_desc, c.determination_id,d.stitch_length,c.gsm_weight, d.machine_dia,d.fabric_dia,d.width_dia_type,
	d.color_id,d.program_qnty,  b.receive_date,d.knitting_party, d.location_id, d.program_date, e.buyer_id, e.booking_no,d.remarks
	order by b.receive_date,d.knitting_party, d.location_id";*/
	
	$sqlField = "d.knitting_party, d.location_id, d.program_date, e.buyer_id, e.booking_no, d.id as program_no, d.start_date, d.end_date, c.fabric_desc, c.determination_id, d.stitch_length, c.gsm_weight, d.machine_dia, d.fabric_dia, d.width_dia_type, d.color_id, d.program_qnty, d.remarks";
	$sqlTable = "wo_booking_dtls a, inv_receive_master b, ppl_planning_entry_plan_dtls c, ppl_planning_info_entry_dtls d, ppl_planning_info_entry_mst e";
	$sqlWhere = "c.dtls_id = d.id and d.mst_id = e.id and b.booking_id != d.id and a.booking_no = e.booking_no and b.receive_basis = 2 and b.entry_form = 2 and d.knitting_source = 1 and d.knitting_party = $company_name $location_cond $program_no_cond $booking_no_cond $job_no_cond $buyer_id_cond $dia_cond $color_cond and d.program_date between '".$start_date."' and '".$end_date."' and c.status_active = 1 and d.status_active = 1 and b.status_active = 1 and a.status_active=1";
	$sqlGroupBy = " group by d.id, d.start_date, d.end_date, c.fabric_desc, c.determination_id, d.stitch_length, c.gsm_weight, d.machine_dia, d.fabric_dia, d.width_dia_type, d.color_id, d.program_qnty, d.knitting_party, d.location_id, d.program_date, e.buyer_id, e.booking_no, d.remarks";
	$sqlOrderBy = " order by d.knitting_party, d.location_id";
	$sql = "SELECT ".$sqlField." FROM ".$sqlTable." WHERE ".$sqlWhere.$sqlGroupBy.$sqlOrderBy;
	//echo $sql; die;
	$nameArray=sql_select( $sql );
	foreach ($nameArray as $row) 
	{
		$program_no_arr[$row[csf("program_no")]] = $row[csf("program_no")];
	}
	
	//for production
	$sqlWhere = "c.dtls_id = d.id and d.mst_id = e.id and b.booking_id = d.id and a.booking_no = e.booking_no and b.receive_basis = 2 and b.entry_form = 2 and d.knitting_source = 1 and d.knitting_party = $company_name $location_cond $program_no_cond $booking_no_cond $job_no_cond $buyer_id_cond $dia_cond $color_cond and b.receive_date between '".$productionFromDate."' and '".$productionToDate."' and c.status_active = 1 and d.status_active = 1 and b.status_active = 1 and a.status_active=1";
	$sql2 = "SELECT ".$sqlField." FROM ".$sqlTable." WHERE ".$sqlWhere.$sqlGroupBy.$sqlOrderBy;
	//echo $sql2; die;
	$nameArray2=sql_select( $sql2 );
	foreach ($nameArray2 as $row) 
	{
		$program_no_arr[$row[csf("program_no")]] = $row[csf("program_no")];
	}

	//for floor
	$floorDataArr = array();
	if($cbo_floor != 0)
	{
		if($location_name != 0)
			$locCond = " AND b.location_id= ".$location_name."";
		else
			$locCond = "";
			
		$sqlFloor = "SELECT a.dtls_id, b.floor_id FROM ppl_planning_info_machine_dtls a, lib_machine_name b WHERE a.machine_id=b.id ".where_con_using_array($program_no_arr, '0', 'a.dtls_id')." AND a.status_active = 1 AND a.is_deleted = 0 AND b.company_id =".$company_name." ".$locCond." and b.floor_id = ".$cbo_floor." and b.category_id = 1 and b.status_active =1 and b.is_deleted=0 GROUP BY a.dtls_id, b.floor_id";
		//echo $sqlFloor;
		$resultSetFloor = sql_select($sqlFloor);
		$program_no_arr = array();
		foreach($resultSetFloor as $row)
		{
			$program_no_arr[$row[csf("dtls_id")]] = $row[csf("dtls_id")];
			$floorDataArr[$row[csf('dtls_id')]]['floor_name'] = $floor_dtls[$row[csf('floor_id')]];
		}
	}
	else
	{
		$sqlFloor = "SELECT a.dtls_id, b.floor_id FROM ppl_planning_info_machine_dtls a, lib_machine_name b WHERE a.machine_id=b.id ".where_con_using_array($program_no_arr, '0', 'a.dtls_id')." AND a.status_active = 1 AND a.is_deleted = 0 AND b.company_id =".$company_name." and b.category_id = 1 and b.status_active =1 and b.is_deleted=0 GROUP BY a.dtls_id, b.floor_id";
		//echo $sqlFloor;
		$resultSetFloor = sql_select($sqlFloor);
		foreach($resultSetFloor as $row)
		{
			$floorDataArr[$row[csf('dtls_id')]]['floor_name'] = $floor_dtls[$row[csf('floor_id')]];
		}
	}
	//for floor end
	//echo "<pre>";
	//print_r($floorDataArr); die;
	
	$sqlFinal = "SELECT d.knitting_party, d.location_id, d.program_date, e.buyer_id, e.booking_no, d.id as program_no, d.start_date, d.end_date, c.fabric_desc, c.determination_id, d.stitch_length, c.gsm_weight, d.machine_dia, d.fabric_dia, d.width_dia_type, d.color_id, d.program_qnty, d.remarks FROM ppl_planning_entry_plan_dtls c, ppl_planning_info_entry_dtls d, ppl_planning_info_entry_mst e WHERE c.dtls_id = d.id and d.mst_id = e.id and d.knitting_source = 1 and d.knitting_party = ".$company_name." and c.status_active = 1 and c.is_deleted=0 and d.status_active = 1 and d.is_deleted=0 and e.status_active = 1 and e.is_deleted=0".where_con_using_array($program_no_arr, '0', 'd.id')." ORDER BY d.id ASC";
	$sqlFinalRslt = sql_select($sqlFinal);
	$data_array = array();
	foreach ($sqlFinalRslt as $row) 
	{
		$data_array[$company_library[$row[csf("knitting_party")]]."*".$location_library[$row[csf("location_id")]]][$row[csf("program_no")]]["program_qnty"] = $row[csf("program_qnty")];
		$data_array[$company_library[$row[csf("knitting_party")]]."*".$location_library[$row[csf("location_id")]]][$row[csf("program_no")]]["program_date"] = $row[csf("program_date")];
		$data_array[$company_library[$row[csf("knitting_party")]]."*".$location_library[$row[csf("location_id")]]][$row[csf("program_no")]]["buyer_id"] = $buyer_library[$row[csf("buyer_id")]];
		$data_array[$company_library[$row[csf("knitting_party")]]."*".$location_library[$row[csf("location_id")]]][$row[csf("program_no")]]["booking_no"] = $row[csf("booking_no")];
		$data_array[$company_library[$row[csf("knitting_party")]]."*".$location_library[$row[csf("location_id")]]][$row[csf("program_no")]]["start_date"] = $row[csf("start_date")];
		$data_array[$company_library[$row[csf("knitting_party")]]."*".$location_library[$row[csf("location_id")]]][$row[csf("program_no")]]["end_date"] = $row[csf("end_date")];
		$data_array[$company_library[$row[csf("knitting_party")]]."*".$location_library[$row[csf("location_id")]]][$row[csf("program_no")]]["construction"] = $construction_library[$row[csf("determination_id")]];
		$data_array[$company_library[$row[csf("knitting_party")]]."*".$location_library[$row[csf("location_id")]]][$row[csf("program_no")]]["fabric_desc"] = $row[csf("fabric_desc")];
		$data_array[$company_library[$row[csf("knitting_party")]]."*".$location_library[$row[csf("location_id")]]][$row[csf("program_no")]]["stitch_length"] = $row[csf("stitch_length")];
		$data_array[$company_library[$row[csf("knitting_party")]]."*".$location_library[$row[csf("location_id")]]][$row[csf("program_no")]]["gsm_weight"] = $row[csf("gsm_weight")];
		$data_array[$company_library[$row[csf("knitting_party")]]."*".$location_library[$row[csf("location_id")]]][$row[csf("program_no")]]["machine_dia"] = $row[csf("machine_dia")];
		$data_array[$company_library[$row[csf("knitting_party")]]."*".$location_library[$row[csf("location_id")]]][$row[csf("program_no")]]["fabric_dia"] = $row[csf("fabric_dia")];
		$data_array[$company_library[$row[csf("knitting_party")]]."*".$location_library[$row[csf("location_id")]]][$row[csf("program_no")]]["width_dia_type"] = $row[csf("width_dia_type")];
		$data_array[$company_library[$row[csf("knitting_party")]]."*".$location_library[$row[csf("location_id")]]][$row[csf("program_no")]]["color_id"] = $row[csf("color_id")];
		$data_array[$company_library[$row[csf("knitting_party")]]."*".$location_library[$row[csf("location_id")]]][$row[csf("program_no")]]["remarks"] = $row[csf("remarks")];
		$data_array[$company_library[$row[csf("knitting_party")]]."*".$location_library[$row[csf("location_id")]]][$row[csf("program_no")]]["floor_id"] = $floorDataArr[$row[csf('program_no')]]['floor_name'];
	}

	$program_nos = implode(",", array_filter($program_no_arr));
	if($program_nos=="") $program_nos=0;
	$programcond = $producedProgramNoCond = ""; 
	$programcond2 = $producedProgramNoCond2 = ""; 
	$program_no_arr=explode(",",$program_nos);
	if($db_type==2 && count($program_no_arr)>999)
	{
		$program_no_chunk=array_chunk($program_no_arr,999) ;
		foreach($program_no_chunk as $chunk_arr)
		{
			$programcond.=" b.id in(".implode(",",$chunk_arr).") or ";	
			$programcond2.=" a.booking_id in(".implode(",",$chunk_arr).") or ";	
		}
				
		$producedProgramNoCond.=" and (".chop($programcond,'or ').")";			
		$producedProgramNoCond2.=" and (".chop($programcond2,'or ').")";			
	}
	else
	{ 	
		if($program_nos)
		{
			$producedProgramNoCond=" and b.id in($program_nos)";
			$producedProgramNoCond2=" and a.booking_id in($program_nos)";
		}   
	}

	$yarn_description_res = sql_select("select b.id as program_no,a.prod_id,c.id, c.yarn_count_id,c.lot, c.brand
	from  ppl_yarn_requisition_entry a, ppl_planning_info_entry_dtls b, product_details_master c
	where a.knit_id  = b.id and a.prod_id = c.id and b.status_active = 1 and a.status_active = 1 and c.status_active = 1 $producedProgramNoCond
	group by b.id,a.prod_id,c.id, c.yarn_count_id,c.lot, c.brand");
	foreach ($yarn_description_res as $val) 
	{
		$yarn_description_arr[$val[csf("program_no")]]["ycount"] .= $yarn_count_library[$val[csf("yarn_count_id")]].",";
		$yarn_description_arr[$val[csf("program_no")]]["ylot"] .= $val[csf("lot")].",";
		$yarn_description_arr[$val[csf("program_no")]]["brand"] .= $brand_library[$val[csf("brand")]].",";
	}

	$production_qty_arr =array();
	$production_qty_date_arr =array();
	$production_qty_res = sql_select("select b.grey_receive_qnty, a.booking_id as program_no, a.receive_date, c.program_qnty from inv_receive_master a, pro_grey_prod_entry_dtls b, ppl_planning_info_entry_dtls c where a.id = b.mst_id and a.booking_id = c.id and a.entry_form = 2 and a.receive_basis = 2 and b.status_active = 1 and b.is_deleted = 0 and a.status_active = 1 and a.is_deleted = 0 $producedProgramNoCond2 ");
	foreach ($production_qty_res as $value) 
	{
		$production_qty_arr[$value[csf("program_no")]]+= $value[csf("grey_receive_qnty")];
		$top_grand_total_program +=  $value[csf("grey_receive_qnty")];

		if(strtotime($value[csf("receive_date")]) >= strtotime($productionFromDate) && strtotime($value[csf("receive_date")]) <= strtotime($productionToDate))
		{
			$top_grand_today_prod +=  $value[csf("grey_receive_qnty")];
			$production_qty_date_arr[$value[csf("program_no")]] += $value[csf("grey_receive_qnty")];
		}

		if($programChk[$value[csf("program_no")]] =="")
		{
			$programChk[$value[csf("program_no")]] = $value[csf("program_no")];
			$top_grand_total_program_qnty += $value[csf("program_qnty")];
		}
		$top_grand_total_prod +=  $value[csf("grey_receive_qnty")];
	}

	ob_start();
	?>
	<fieldset style="width:1950px;">
		<table width="1430" cellspacing="0" cellpadding="0" border="0" rules="all" style="float: left;">
			<tr class="form_caption">
				<td colspan="16" align="center" style="border:none;font-size:16px; font-weight:bold"> <? echo $report_title; ?></td>
			</tr>
			<tr class="form_caption">
				<td colspan="16" align="center" style="border:none;"><? echo $company_library[$company_name]; ?></td>
			</tr>
			<tr class="form_caption">
				<td colspan="16" align="center" style="border:none;">&nbsp;</td>
			</tr>
		</table>
		<table width="500" cellspacing="0" cellpadding="0" border="0" rules="all" style="float: left;">
			<tr class="form_caption">
				<td colspan="6" align="center" style="border:none;font-size:16px; font-weight:bold"> <? //echo $report_title; ?></td>
			</tr>
			<tr class="form_caption"> 
				<td align="center" style="border: 1px solid black;" width="80">Grand Total</td>
				<td align="center" style="border: 1px solid black;" width="80" id="top_grand_total_program"><? echo $top_grand_total_program_qnty;?></td>
				<td align="center" style="border: 1px solid black;" width="80" id="top_grand_today_prod"><? echo $top_grand_today_prod;?></td>
				<td align="center" style="border: 1px solid black;" width="80" id="top_grand_total_prod"><? echo $top_grand_total_prod;?></td>
				<td align="center" style="border: 1px solid black;" width="80" id="top_grand_bal_prod">
					<? 
					 echo $top_grand_total_prod - $top_grand_today_prod;
					?>
				</td>
				<td align="center" width="100">&nbsp;</td>
			</tr>
			<tr class="form_caption"> 
				<td align="center" width="160" colspan="2">&nbsp;</td>
				<td align="right" width="340" colspan="4" style="color: black;">Report Print Date: <? echo date("d-m-Y"); ?></td>
			</tr>
		</table>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1930" class="rpt_table" >
			<thead>
				<tr>
					<th width="80">Program Date</th>
					<th width="80">Buyer</th>
					<th width="80">Booking</th>
					<th width="100">Floor</th>
					<th width="80">Program No.</th>
					<th width="80">Knit Start Date</th>
					<th width="80">Knit Com Date</th>
					<th width="80">Yarn Count</th>
					<th width="80">Yarn Lot</th>
					<th width="80">Yarn Brand</th>
					<th width="100">Fabric Construction</th>
					<th width="100">Fabric Composition</th>
					<th width="80">S/L</th>
					<th width="80">F/GSM</th>
					<th width="80">M/Dia</th>
					<th width="80">F/Dia</th>
					<th width="80">Width Type</th>
					<th width="80">Fabric Color</th>
					<th width="80"><p style="word-break: break-all;word-wrap: break-word;">Program Qnty.(Kgs)</p></th>
					<th width="80">Today Knit (Kg)</th>
					<th width="80">Total Knit Qty.(Kg)</th>
					<th width="80"><p style="word-break: break-all;word-wrap: break-word;">Total Bala.Qty(Kg)</p></th>
					<th>Remarks</th>
				</tr>
			</thead>
		</table>
		<div style="width:1948px; overflow-y:scroll; max-height:450px;" id="scroll_body">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1930" class="rpt_table" id="tbl_list_search">
				<tbody>
				<?
				$production_date_arr = array();
				$comp_loc_arr = array();
				$i = 1;
				foreach ($data_array as $company_location => $company_location_data) 
				{
					$comp_loc_arr=array();
					foreach ($company_location_data as $program_id => $row) 
					{
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>"> 
							<td width="80"><? echo $row["program_date"];?></td>
							<td width="80"><p style="word-break: break-all;word-wrap: break-word;"><? echo $row["buyer_id"];?></p></td>
							<td width="80"><p><? echo $row["booking_no"];?></p></td>
							<td width="100"><p><? echo $row["floor_id"];?></p></td>
							<td width="80"><? echo $program_id;?></td>
							<td width="80"><? echo $row["start_date"];?></td>
							<td width="80"><? echo $row["end_date"];?></td>
							<td width="80"><p style="word-break: break-all;word-wrap: break-word;"><? echo implode(",",array_unique(array_filter(explode(",", $yarn_description_arr[$program_id]["ycount"]))));?></p></td>
							<td width="80"><p style="word-break: break-all;word-wrap: break-word;"><? echo implode(",",array_unique(array_filter(explode(",", $yarn_description_arr[$program_id]["ylot"]))));?></p></td>
							<td width="80"><p style="word-break: break-all;word-wrap: break-word;"><? echo implode(",",array_unique(array_filter(explode(",", $yarn_description_arr[$program_id]["brand"]))));?></p></td>
							<td width="100"><p style="word-break: break-all;word-wrap: break-word;"><? echo $row["construction"]?></p></td>
							<td width="100"><p style="word-break: break-all;word-wrap: break-word;"><? echo $row["fabric_desc"]?></p></td>
							<td width="80"><p style="word-break: break-all;word-wrap: break-word;"><? echo $row["stitch_length"]?></p></td>
							<td width="80"><p style="word-break: break-all;word-wrap: break-word;"><? echo $row["gsm_weight"]?></p></td>
							<td width="80"><p style="word-break: break-all;word-wrap: break-word;"><? echo $row["machine_dia"]?></p></td>
							<td width="80"><p style="word-break: break-all;word-wrap: break-word;"><? echo $row["fabric_dia"]?></p></td>
							<td width="80"><p style="word-break: break-all;word-wrap: break-word;"><? echo $fabric_typee[$row["width_dia_type"]]?></p></td>
							<td width="80"><p style="word-break: break-all;word-wrap: break-word;">
								<? 
								$colors_name="";
								foreach(explode(",",$row["color_id"]) as $colorId){
									$colors_name .= $color_library[$colorId].",";
								}
								echo chop($colors_name,",");
								?></p>
							</td>
							<td width="80" align="right"><p><? echo number_format($row["program_qnty"],2,'.','');?></p></td>
							<td width="80" align="right">
								<p>
									<? 
									//$today_production = $production_qty_date_arr[$program_id][$production_date];
									$today_production = $production_qty_date_arr[$program_id];
									echo number_format($today_production,2,'.','');
									?>
								</p>
							</td>
							<td width="80" align="right">
								<p>
									<? 
										$program_production = $production_qty_arr[$program_id];
										echo number_format($program_production,2,'.','');
									?>
								</p>
								</td>
							<td width="80" align="right">
								<p>
									<? 
									//$bal_production = $program_production-$today_production;
									$bal_production = number_format($row["program_qnty"],2,'.','') - $program_production;
									echo number_format($bal_production,2,'.','');
									?>
								</p>
							</td>
							<td><p style="word-break: break-all;word-wrap: break-word;"><? echo $row["remarks"];?></p></td>
						</tr>
						<?
						$i++;
						$subCompLocTotProgQnty += number_format($row["program_qnty"],2,'.',''); 
						$subCompLocTotTodayProduction += number_format($today_production,2,'.','');
						$subCompLocTotProgram_production += number_format($program_production,2,'.','');
						$subCompLocTotBal_production += number_format($bal_production,2,'.','');

						$subDateProgSubTot += number_format($row["program_qnty"],2,'.',''); 
						$subDateTotTodayProduction += number_format($today_production,2,'.','');
						$subDateTotProgram_production += number_format($program_production,2,'.','');
						$subDateTotBal_production += number_format($bal_production,2,'.','');

						$grandDateProgSubTot += number_format($row["program_qnty"],2,'.',''); 
						$grandDateTotTodayProduction += number_format($today_production,2,'.','');
						$grandDateTotProgram_production += number_format($program_production,2,'.','');
						$grandDateTotBal_production += number_format($bal_production,2,'.','');
					}
				}
				?>
					<tr style="background-color: #ccc; font-size: 13;border-top: 2px solid black;font-weight: bold;">
						<td colspan="18"><b>Grand Total :</b></td>
						<td id="grand_program_qnty" align="right"><? echo number_format($grandDateProgSubTot,2,'.','');?></td>
						<td id="grand_today_prod_qnty" align="right"><? echo number_format($grandDateTotTodayProduction,2,'.','');?></td>
						<td id="grand_total_prod_qnty" align="right"><? echo number_format($grandDateTotProgram_production,2,'.','');?></td>
						<td id="grand_bal_prod_qnty" align="right"><? echo number_format($grandDateTotBal_production,2,'.','');?></td>
						<td>&nbsp;</td>
					</tr>
				</tbody>
			</table>
		</div>
	</fieldset>
	<?
	$html = ob_get_contents();
    ob_clean();
    foreach (glob("$user_id*.xls") as $filename)
	{
        @unlink($filename);
    }
    $name = time();
    $filename = $user_id . "_" . $name . ".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, $html);
    echo "$html**$filename";
	exit();
}

if($action=="report_generate_outbound_2")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$company_name= str_replace("'","",$cbo_company_name);
	$program_no= str_replace("'","",$txt_program_no);
	$txt_booking_id= str_replace("'","",$txt_booking_id);
	$cbo_year_selection = str_replace("'","",$cbo_year_selection);
	$cbo_year_selection = substr($cbo_year_selection, -2);
	$txt_job_id = str_replace("'","",$txt_job_id);
	$txt_dia = trim(str_replace("'","",$txt_dia));
	$cbo_color = str_replace("'","",$cbo_color);
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and e.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and e.buyer_id in (".str_replace("'","",$cbo_buyer_name).")";
	}
	if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
	{
		if($db_type==0)
		{
			$start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
			$end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
			$productionFromDate=change_date_format(str_replace("'","",$txt_prod_from_date),"yyyy-mm-dd","");
			$productionToDate=change_date_format(str_replace("'","",$txt_prod_to_date),"yyyy-mm-dd","");
		}
		else if($db_type==2)
		{
			$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
			$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
			$productionFromDate=change_date_format(str_replace("'","",$txt_prod_from_date),"","",1);
			$productionToDate=change_date_format(str_replace("'","",$txt_prod_to_date),"","",1);
		}
		$date_cond=" and b.receive_date between '$start_date' and '$end_date'";
	}
	
	$job_no=str_replace("'","",$txt_job_no);
	$job_no_cond="";
	if ($job_no=="") 
	{
		$job_no_cond=""; 
	}
	else 
	{
		if($txt_job_id)
		{
			$job_no_cond=" and a.job_no '%".$job_no."%'";
		}
		else
		{
			$job_no_cond .=" and a.job_no '%".$job_no."%'";
			$job_no_cond .=" and a.job_no '%-".$cbo_year_selection."-%'";
		}
		
	}

	if ($program_no) $program_no_cond=" and d.id = $program_no ";  else $program_no_cond="";

	$booking_no=str_replace("'","",$txt_booking_no);
	if($booking_no == "") {
		$booking_no_cond ="";
	} 
	else 
	{
		if($txt_booking_id)
		{
			$booking_no_cond=" and e.booking_no like '%".trim($booking_no) ."%' "; 
		}else{
			$booking_no_cond=" and e.booking_no like '%".trim($booking_no) ."' and e.booking_no like '%-$cbo_year_selection-%'"; 
		}
	}

	if($txt_dia) $dia_cond = " and d.machine_dia= '$txt_dia'"; else $dia_cond = "";
	if($cbo_color) $color_cond = " and d.color_id= '$cbo_color'"; else $color_cond = "";

	/*
	$sql="select  b.receive_date as production_date,d.knitting_party, d.location_id, d.program_date, e.buyer_id, e.booking_no,d.id as program_no,d.start_date,d.end_date, c.fabric_desc, c.determination_id,d.stitch_length,c.gsm_weight, d.machine_dia,d.fabric_dia,d.width_dia_type,d.color_id,d.program_qnty,d.remarks
	from wo_booking_dtls a, inv_receive_master b, ppl_planning_entry_plan_dtls c, ppl_planning_info_entry_dtls d, ppl_planning_info_entry_mst e
	where c.dtls_id = d.id and d.mst_id = e.id and b.booking_id = d.id and a.booking_no = e.booking_no and b.receive_basis = 2 and b.entry_form = 2 and d.knitting_source = 3 and d.knitting_party = $company_name $program_no_cond $booking_no_cond $job_no_cond $buyer_id_cond $dia_cond $color_cond
	$date_cond and c.status_active = 1 and d.status_active = 1 and b.status_active = 1 and a.status_active=1
	group by  d.id ,d.start_date,d.end_date, c.fabric_desc, c.determination_id,d.stitch_length,c.gsm_weight, d.machine_dia,d.fabric_dia,d.width_dia_type,
	d.color_id,d.program_qnty,  b.receive_date,d.knitting_party, d.location_id, d.program_date, e.buyer_id, e.booking_no,d.remarks
	order by b.receive_date,d.knitting_party, d.location_id";
	*/

	$sqlField = "d.knitting_party, d.location_id, d.program_date, e.buyer_id, e.booking_no,d.id as program_no,d.start_date,d.end_date, c.fabric_desc, c.determination_id,d.stitch_length,c.gsm_weight, d.machine_dia,d.fabric_dia,d.width_dia_type,d.color_id,d.program_qnty,d.remarks";
	$sqlTable = "wo_booking_dtls a, inv_receive_master b, ppl_planning_entry_plan_dtls c, ppl_planning_info_entry_dtls d, ppl_planning_info_entry_mst e";
	$sqlWhere = "c.dtls_id = d.id and d.mst_id = e.id and b.booking_id != d.id and a.booking_no = e.booking_no and b.receive_basis = 2 and b.entry_form = 2 and d.knitting_source = 3 and d.knitting_party = $company_name $program_no_cond $booking_no_cond $job_no_cond $buyer_id_cond $dia_cond $color_cond  and d.program_date between '".$start_date."' and '".$end_date."' and c.status_active = 1 and d.status_active = 1 and b.status_active = 1 and a.status_active=1";
	$sqlGroupBy = " GROUP BY d.id ,d.start_date,d.end_date, c.fabric_desc, c.determination_id,d.stitch_length,c.gsm_weight, d.machine_dia,d.fabric_dia,d.width_dia_type, d.color_id,d.program_qnty,  b.receive_date,d.knitting_party, d.location_id, d.program_date, e.buyer_id, e.booking_no,d.remarks";
	$sqlOrderBy = " ORDER BY d.knitting_party, d.location_id";
	$sql = "SELECT ".$sqlField." FROM ".$sqlTable." WHERE ".$sqlWhere.$sqlGroupBy.$sqlOrderBy;
	//echo $sql; die;
	$nameArray=sql_select( $sql );
	foreach ($nameArray as $row) 
	{
		$program_no_arr[$row[csf("program_no")]] = $row[csf("program_no")];
	}
	
	//for production
	$sqlWhere = "c.dtls_id = d.id and d.mst_id = e.id and b.booking_id = d.id and a.booking_no = e.booking_no and b.receive_basis = 2 and b.entry_form = 2 and d.knitting_source = 3 and d.knitting_party = $company_name $program_no_cond $booking_no_cond $job_no_cond $buyer_id_cond $dia_cond $color_cond and b.receive_date between '".$productionFromDate."' and '".$productionToDate."' and c.status_active = 1 and d.status_active = 1 and b.status_active = 1 and a.status_active=1";
	$sql2 = "SELECT ".$sqlField." FROM ".$sqlTable." WHERE ".$sqlWhere.$sqlGroupBy.$sqlOrderBy;
	//echo $sql; die;
	$nameArray2=sql_select( $sql2 );
	foreach ($nameArray2 as $row) 
	{
		$program_no_arr[$row[csf("program_no")]] = $row[csf("program_no")];
	}
	
	//for floor
	$floorDataArr = array();
	if($cbo_floor != 0)
	{
		if($location_name != 0)
			$locCond = " AND b.location_id= ".$location_name."";
		else
			$locCond = "";
			
		$sqlFloor = "SELECT a.dtls_id, b.floor_id FROM ppl_planning_info_machine_dtls a, lib_machine_name b WHERE a.machine_id=b.id ".where_con_using_array($program_no_arr, '0', 'a.dtls_id')." AND a.status_active = 1 AND a.is_deleted = 0 AND b.company_id =".$company_name." ".$locCond." and b.floor_id = ".$cbo_floor." and b.category_id = 1 and b.status_active =1 and b.is_deleted=0 GROUP BY a.dtls_id, b.floor_id";
		//echo $sqlFloor;
		$resultSetFloor = sql_select($sqlFloor);
		$program_no_arr = array();
		foreach($resultSetFloor as $row)
		{
			$program_no_arr[$row[csf("dtls_id")]] = $row[csf("dtls_id")];
			$floorDataArr[$row[csf('dtls_id')]]['floor_name'] = $floor_dtls[$row[csf('floor_id')]];
		}
	}
	else
	{
		$sqlFloor = "SELECT a.dtls_id, b.floor_id FROM ppl_planning_info_machine_dtls a, lib_machine_name b WHERE a.machine_id=b.id ".where_con_using_array($program_no_arr, '0', 'a.dtls_id')." AND a.status_active = 1 AND a.is_deleted = 0 AND b.company_id =".$company_name." and b.category_id = 1 and b.status_active =1 and b.is_deleted=0 GROUP BY a.dtls_id, b.floor_id";
		//echo $sqlFloor;
		$resultSetFloor = sql_select($sqlFloor);
		foreach($resultSetFloor as $row)
		{
			$floorDataArr[$row[csf('dtls_id')]]['floor_name'] = $floor_dtls[$row[csf('floor_id')]];
		}
	}
	//for floor end
	//echo "<pre>";
	//print_r($floorDataArr); die;
	
	$sqlFinal = "SELECT d.knitting_party, d.location_id, d.program_date, e.buyer_id, e.booking_no, d.id as program_no, d.start_date, d.end_date, c.fabric_desc, c.determination_id, d.stitch_length, c.gsm_weight, d.machine_dia, d.fabric_dia, d.width_dia_type, d.color_id, d.program_qnty, d.remarks FROM ppl_planning_entry_plan_dtls c, ppl_planning_info_entry_dtls d, ppl_planning_info_entry_mst e WHERE c.dtls_id = d.id and d.mst_id = e.id and d.knitting_source = 3 and d.knitting_party = ".$company_name." and c.status_active = 1 and c.is_deleted=0 and d.status_active = 1 and d.is_deleted=0 and e.status_active = 1 and e.is_deleted=0".where_con_using_array($program_no_arr, '0', 'd.id')." ORDER BY d.id ASC";
	$sqlFinalRslt = sql_select($sqlFinal);
	$data_array = array();
	foreach ($sqlFinalRslt as $row) 
	{
		$data_array[$company_library[$row[csf("knitting_party")]]][$row[csf("program_no")]]["program_qnty"] = $row[csf("program_qnty")];
		$data_array[$company_library[$row[csf("knitting_party")]]][$row[csf("program_no")]]["program_date"] = $row[csf("program_date")];
		$data_array[$company_library[$row[csf("knitting_party")]]][$row[csf("program_no")]]["buyer_id"] = $buyer_library[$row[csf("buyer_id")]];
		$data_array[$company_library[$row[csf("knitting_party")]]][$row[csf("program_no")]]["booking_no"] = $row[csf("booking_no")];
		$data_array[$company_library[$row[csf("knitting_party")]]][$row[csf("program_no")]]["start_date"] = $row[csf("start_date")];
		$data_array[$company_library[$row[csf("knitting_party")]]][$row[csf("program_no")]]["end_date"]= $row[csf("end_date")];
		$data_array[$company_library[$row[csf("knitting_party")]]][$row[csf("program_no")]]["construction"]= $construction_library[$row[csf("determination_id")]];
		$data_array[$company_library[$row[csf("knitting_party")]]][$row[csf("program_no")]]["fabric_desc"]= $row[csf("fabric_desc")];
		$data_array[$company_library[$row[csf("knitting_party")]]][$row[csf("program_no")]]["stitch_length"] = $row[csf("stitch_length")];
		$data_array[$company_library[$row[csf("knitting_party")]]][$row[csf("program_no")]]["gsm_weight"]= $row[csf("gsm_weight")];
		$data_array[$company_library[$row[csf("knitting_party")]]][$row[csf("program_no")]]["machine_dia"]= $row[csf("machine_dia")];
		$data_array[$company_library[$row[csf("knitting_party")]]][$row[csf("program_no")]]["fabric_dia"] = $row[csf("fabric_dia")];
		$data_array[$company_library[$row[csf("knitting_party")]]][$row[csf("program_no")]]["width_dia_type"] = $row[csf("width_dia_type")];
		$data_array[$company_library[$row[csf("knitting_party")]]][$row[csf("program_no")]]["color_id"] = $row[csf("color_id")];
		$data_array[$company_library[$row[csf("knitting_party")]]][$row[csf("program_no")]]["remarks"] = $row[csf("remarks")];
	}

	$program_nos = implode(",", array_filter($program_no_arr));
	if($program_nos=="") $program_nos=0;
	$programcond = $producedProgramNoCond = ""; 
	$programcond2 = $producedProgramNoCond2 = ""; 
	$program_no_arr=explode(",",$program_nos);
	if($db_type==2 && count($program_no_arr)>999)
	{
		$program_no_chunk=array_chunk($program_no_arr,999) ;
		foreach($program_no_chunk as $chunk_arr)
		{
			$programcond.=" b.id in(".implode(",",$chunk_arr).") or ";	
			$programcond2.=" a.booking_id in(".implode(",",$chunk_arr).") or ";	
		}
				
		$producedProgramNoCond.=" and (".chop($programcond,'or ').")";			
		$producedProgramNoCond2.=" and (".chop($programcond2,'or ').")";			
	}
	else
	{ 	
		if($program_nos)
		{
			$producedProgramNoCond=" and b.id in($program_nos)";
			$producedProgramNoCond2=" and a.booking_id in($program_nos)";
		}   
	}

	$yarn_description_res = sql_select("select b.id as program_no,a.prod_id,c.id, c.yarn_count_id,c.lot, c.brand
	from  ppl_yarn_requisition_entry a, ppl_planning_info_entry_dtls b, product_details_master c
	where a.knit_id  = b.id and a.prod_id = c.id and b.status_active = 1 and a.status_active = 1 and c.status_active = 1 $producedProgramNoCond
	group by b.id,a.prod_id,c.id, c.yarn_count_id,c.lot, c.brand");
	foreach ($yarn_description_res as $val) 
	{
		$yarn_description_arr[$val[csf("program_no")]]["ycount"] .= $yarn_count_library[$val[csf("yarn_count_id")]].",";
		$yarn_description_arr[$val[csf("program_no")]]["ylot"] .= $val[csf("lot")].",";
		$yarn_description_arr[$val[csf("program_no")]]["brand"] .= $brand_library[$val[csf("brand")]].",";
	}

	$production_qty_arr =array();
	$production_qty_date_arr =array();
	$production_qty_res = sql_select("select b.grey_receive_qnty, a.booking_id as program_no, a.receive_date, c.program_qnty from inv_receive_master a, pro_grey_prod_entry_dtls b ,ppl_planning_info_entry_dtls c where a.id = b.mst_id and a.booking_id = c.id and a.entry_form = 2 and a.receive_basis = 2 and b.status_active = 1 and b.is_deleted = 0 and a.status_active = 1 and a.is_deleted = 0 $producedProgramNoCond2 ");
	foreach ($production_qty_res as $value) 
	{
		$production_qty_arr[$value[csf("program_no")]]+= $value[csf("grey_receive_qnty")];
		$top_grand_total_program +=  $value[csf("grey_receive_qnty")];

		if(strtotime($value[csf("receive_date")]) >= strtotime($productionFromDate) && strtotime($value[csf("receive_date")]) <= strtotime($productionToDate))
		{
			$top_grand_today_prod +=  $value[csf("grey_receive_qnty")];
			$production_qty_date_arr[$value[csf("program_no")]] += $value[csf("grey_receive_qnty")];
		}

		if($programChk[$value[csf("program_no")]] =="")
		{
			$programChk[$value[csf("program_no")]] = $value[csf("program_no")];
			$top_grand_total_program_qnty += $value[csf("program_qnty")];
		}
		$top_grand_total_prod +=  $value[csf("grey_receive_qnty")];
	}
	ob_start();
	?>
	<fieldset style="width:1950px;">
		<table width="1430" cellspacing="0" cellpadding="0" border="0" rules="all" style="float: left;">
			<tr class="form_caption">
				<td colspan="16" align="center" style="border:none;font-size:16px; font-weight:bold"> <? echo $report_title; ?></td>
			</tr>
			<tr class="form_caption">
				<td colspan="16" align="center"><? echo $company_library[$company_name]; ?></td>
			</tr>
			<tr class="form_caption">
				<td colspan="16" align="center">&nbsp;</td>
			</tr>
		</table>
		<table width="500" cellspacing="0" cellpadding="0" border="0" rules="all" style="float: left;">
			<tr class="form_caption">
				<td colspan="6" align="center" style="border:none;font-size:16px; font-weight:bold"> <? //echo $report_title; ?></td>
			</tr>
			<tr class="form_caption"> 
				<td align="center" style="border: 1px solid black;" width="80">Grand Total</td>
				<td align="center" style="border: 1px solid black;" width="80" id="top_grand_total_program"><? echo $top_grand_total_program_qnty;?></td>
				<td align="center" style="border: 1px solid black;" width="80" id="top_grand_today_prod"><? echo $top_grand_today_prod;?></td>
				<td align="center" style="border: 1px solid black;" width="80" id="top_grand_total_prod"><? echo $top_grand_total_prod;?></td>
				<td align="center" style="border: 1px solid black;" width="80" id="top_grand_bal_prod">
					<? 
					 echo $top_grand_total_prod - $top_grand_today_prod;
					?>
				</td>
				<td align="center" width="100">&nbsp;</td>
			</tr>
			<tr class="form_caption"> 
				<td align="center" width="160" colspan="2">&nbsp;</td>
				<td align="right" width="340" colspan="4" style="color: black;">Report Print Date: <? echo date("d-m-Y"); ?></td>
			</tr>
		</table>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1930" class="rpt_table" >
			<thead>
				<tr>
					<th width="80">Program Date</th>
					<th width="80">Buyer</th>
					<th width="80">Booking</th>
					<th width="100">Floor</th>
					<th width="80">Program No.</th>
					<th width="80">Knit Start Date</th>
					<th width="80">Knit Com Date</th>
					<th width="80">Yarn Count</th>
					<th width="80">Yarn Lot</th>
					<th width="80">Yarn Brand</th>
					<th width="100">Fabric Construction</th>
					<th width="100">Fabric Composition</th>
					<th width="80">S/L</th>
					<th width="80">F/GSM</th>
					<th width="80">M/Dia</th>
					<th width="80">F/Dia</th>
					<th width="80">Width Type</th>
					<th width="80">Fabric Color</th>
					<th width="80"><p style="word-break: break-all;word-wrap: break-word;">Program Qnty.(Kgs)</p></th>
					<th width="80">Today Knit (Kg)</th>
					<th width="80">Total Knit Qty.(Kg)</th>
					<th width="80"><p style="word-break: break-all;word-wrap: break-word;">Total Bala.Qty(Kg)</p></th>
					<th>Remarks</th>
				</tr>
			</thead>
		</table>
		<div style="width:1948px; overflow-y:scroll; max-height:450px;" id="scroll_body">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1930" class="rpt_table" id="tbl_list_search">
				<tbody>
				<? //location_library
				$production_date_arr = array();
				$comp_loc_arr = array();
				$i = 1;
				foreach ($data_array as $company => $company_data) 
				{
					$comp_loc_arr=array();
					foreach ($company_data as $program_id => $row) 
					{
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>"> 
							<td width="80"><? echo $row["program_date"];?></td>
							<td width="80"><p style="word-break: break-all;word-wrap: break-word;"><? echo $row["buyer_id"];?></p></td>
							<td width="80"><? echo $row["booking_no"];?></td>
							<td width="100"><p><? echo $row["floor_name"];?></p></td>
							<td width="80"><? echo $program_id;?></td>
							<td width="80"><? echo $row["start_date"];?></td>
							<td width="80"><? echo $row["end_date"];?></td>
							<td width="80"><p style="word-break: break-all;word-wrap: break-word;"><? echo implode(",",array_unique(array_filter(explode(",", $yarn_description_arr[$program_id]["ycount"]))));?></p></td>
							<td width="80"><p style="word-break: break-all;word-wrap: break-word;"><? echo implode(",",array_unique(array_filter(explode(",", $yarn_description_arr[$program_id]["ylot"]))));?></p></td>
							<td width="80"><p style="word-break: break-all;word-wrap: break-word;"><? echo implode(",",array_unique(array_filter(explode(",", $yarn_description_arr[$program_id]["brand"]))));?></p></td>
							<td width="100"><p style="word-break: break-all;word-wrap: break-word;"><? echo $row["construction"]?></p></td>
							<td width="100"><p style="word-break: break-all;word-wrap: break-word;"><? echo $row["fabric_desc"]?></p></td>
							<td width="80"><p style="word-break: break-all;word-wrap: break-word;"><? echo $row["stitch_length"]?></p></td>
							<td width="80"><p style="word-break: break-all;word-wrap: break-word;"><? echo $row["gsm_weight"]?></p></td>
							<td width="80"><p style="word-break: break-all;word-wrap: break-word;"><? echo $row["machine_dia"]?></p></td>
							<td width="80"><p style="word-break: break-all;word-wrap: break-word;"><? echo $row["fabric_dia"]?></p></td>
							<td width="80"><p style="word-break: break-all;word-wrap: break-word;"><? echo $fabric_typee[$row["width_dia_type"]]?></p></td>
							<td width="80">
								<p style="word-break: break-all;word-wrap: break-word;">
									<? 
									$colors_name="";
									foreach(explode(",",$row["color_id"]) as $colorId){
										$colors_name .= $color_library[$colorId].",";
									}
									echo chop($colors_name,",");
									?>	
								</p>
							</td>
							<td width="80" align="right"><p><? echo number_format($row["program_qnty"],2,'.','');?></p></td>
							<td width="80" align="right">
								<p>
									<? 
									//$today_production = $production_qty_date_arr[$program_id][$production_date];
									$today_production = $production_qty_date_arr[$program_id];
									echo number_format($today_production,2,'.','');
									?>
								</p>
							</td>
							<td width="80" align="right">
								<p>
									<? 
										$program_production = $production_qty_arr[$program_id];
										echo number_format($program_production,2,'.','');
									?>
								</p>
								</td>
							<td width="80" align="right">
								<p>
									<? 
									//$bal_production = $program_production-$today_production;
									$bal_production = number_format($row["program_qnty"],2,'.','') - number_format($program_production,2,'.','');
									echo number_format($bal_production,2,'.','');
									?>
								</p>
							</td>
							<td><p style="word-break: break-all;word-wrap: break-word;"><? echo $row["remarks"];?></p></td>
						</tr>
						<?
						$i++;
						$subCompLocTotProgQnty += number_format($row["program_qnty"],2,'.',''); 
						$subCompLocTotTodayProduction += number_format($today_production,2,'.','');
						$subCompLocTotProgram_production += number_format($program_production,2,'.','');
						$subCompLocTotBal_production += number_format($bal_production,2,'.','');

						$subDateProgSubTot += number_format($row["program_qnty"],2,'.',''); 
						$subDateTotTodayProduction += number_format($today_production,2,'.','');
						$subDateTotProgram_production += number_format($program_production,2,'.','');
						$subDateTotBal_production += number_format($bal_production,2,'.','');

						$grandDateProgSubTot += number_format($row["program_qnty"],2,'.',''); 
						$grandDateTotTodayProduction += number_format($today_production,2,'.','');
						$grandDateTotProgram_production += number_format($program_production,2,'.','');
						$grandDateTotBal_production += number_format($bal_production,2,'.','');
					}
				}
				?>
					<tr style="background-color: #ccc; font-size: 13;border-top: 2px solid black;font-weight: bold;">
						<td colspan="18"><b>Grand Total :</b></td>
						<td id="grand_program_qnty" align="right"><? echo number_format($grandDateProgSubTot,2,'.','');?></td>
						<td id="grand_today_prod_qnty" align="right"><? echo number_format($grandDateTotTodayProduction,2,'.','');?></td>
						<td id="grand_total_prod_qnty" align="right"><? echo number_format($grandDateTotProgram_production,2,'.','');?></td>
						<td id="grand_bal_prod_qnty" align="right"><? echo number_format($grandDateTotBal_production,2,'.','');?></td>
						<td>&nbsp;</td>
					</tr>
				</tbody>
			</table>
		</div>
	</fieldset>
	<?
	$html = ob_get_contents();
    ob_clean();
    foreach (glob("$user_id*.xls") as $filename)
	{
        @unlink($filename);
    }
    $name = time();
    $filename = $user_id . "_" . $name . ".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, $html);
    echo "$html**$filename";
	exit();
}

if($action=="report_generate_inhouse_222")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$company_name= str_replace("'","",$cbo_company_name);
	$location_name= str_replace("'","",$cbo_location_name);
	$program_no= str_replace("'","",$txt_program_no);
	$txt_booking_id= str_replace("'","",$txt_booking_id);
	$cbo_year_selection = str_replace("'","",$cbo_year_selection);
	$cbo_year_selection = substr($cbo_year_selection, -2);
	$txt_job_id = str_replace("'","",$txt_job_id);
	$txt_dia = trim(str_replace("'","",$txt_dia));
	$cbo_color = str_replace("'","",$cbo_color);
	$cbo_floor = str_replace("'","",$cbo_floor);
	
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and e.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and e.buyer_id in (".str_replace("'","",$cbo_buyer_name).")";
	}
	if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
	{
		if($db_type==0)
		{
			$start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
			$end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
		}
		else if($db_type==2)
		{
			$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
			$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
		}
		$date_cond=" and b.receive_date between '$start_date' and '$end_date'";
	}
	
	$job_no=str_replace("'","",$txt_job_no);
	$job_no_cond="";
	if ($job_no=="") 
	{
		$job_no_cond=""; 
	}
	else 
	{
		if($txt_job_id)
		{
			$job_no_cond=" and a.job_no '%".$job_no."%'";
		}
		else
		{
			$job_no_cond .=" and a.job_no '%".$job_no."%'";
			$job_no_cond .=" and a.job_no '%-".$cbo_year_selection."-%'";
		}
	}

	if ($program_no) $program_no_cond=" and d.id = $program_no "; else $program_no_cond="";  

	$booking_no=str_replace("'","",$txt_booking_no);
	if($booking_no == "") {
		$booking_no_cond ="";
	} 
	else 
	{
		if($txt_booking_id)
		{
			$booking_no_cond=" and e.booking_no like '%".trim($booking_no) ."%' "; 
		}else{
			$booking_no_cond=" and e.booking_no like '%".trim($booking_no) ."' and e.booking_no like '%-$cbo_year_selection-%'"; 
		}
	}

	if($txt_dia) $dia_cond = " and d.machine_dia= '$txt_dia'"; else $dia_cond = "";
	if($cbo_color) $color_cond = " and d.color_id= '$cbo_color'"; else $color_cond = "";
	if($location_name) $location_cond = " and d.location_id= '$location_name'"; else $location_cond = "";

	/*$sql="select  b.receive_date as production_date,d.knitting_party, d.location_id, d.program_date, e.buyer_id, e.booking_no,d.id as program_no,d.start_date,d.end_date, c.fabric_desc, c.determination_id,d.stitch_length,c.gsm_weight, d.machine_dia,d.fabric_dia,d.width_dia_type,d.color_id,d.program_qnty,d.remarks
	from wo_booking_dtls a, inv_receive_master b, ppl_planning_entry_plan_dtls c, ppl_planning_info_entry_dtls d, ppl_planning_info_entry_mst e
	where c.dtls_id = d.id and d.mst_id = e.id and b.booking_id = d.id and a.booking_no = e.booking_no and b.receive_basis = 2 and b.entry_form = 2 and d.knitting_source = 1 and d.knitting_party = $company_name $location_cond $program_no_cond $booking_no_cond $job_no_cond $buyer_id_cond $dia_cond $color_cond
	$date_cond and c.status_active = 1 and d.status_active = 1 and b.status_active = 1 and a.status_active=1
	group by  d.id ,d.start_date,d.end_date, c.fabric_desc, c.determination_id,d.stitch_length,c.gsm_weight, d.machine_dia,d.fabric_dia,d.width_dia_type,
	d.color_id,d.program_qnty,  b.receive_date,d.knitting_party, d.location_id, d.program_date, e.buyer_id, e.booking_no,d.remarks
	order by b.receive_date,d.knitting_party, d.location_id";*/
	$sqlField = "d.knitting_party, d.location_id, d.program_date, e.buyer_id, e.booking_no, d.id as program_no, d.start_date, d.end_date, c.fabric_desc, c.determination_id, d.stitch_length, c.gsm_weight, d.machine_dia, d.fabric_dia, d.width_dia_type, d.color_id, d.program_qnty, d.remarks";
	$sqlTable = "wo_booking_dtls a, inv_receive_master b, ppl_planning_entry_plan_dtls c, ppl_planning_info_entry_dtls d, ppl_planning_info_entry_mst e";
	$sqlWhere = "c.dtls_id = d.id and d.mst_id = e.id and b.booking_id != d.id and a.booking_no = e.booking_no and b.receive_basis = 2 and b.entry_form = 2 and d.knitting_source = 1 and d.knitting_party = $company_name $location_cond $program_no_cond $booking_no_cond $job_no_cond $buyer_id_cond $dia_cond $color_cond and d.program_date between '".$start_date."' and '".$end_date."' and c.status_active = 1 and d.status_active = 1 and b.status_active = 1 and a.status_active=1";
	
	$sqlGroupBy = " group by d.id, d.start_date, d.end_date, c.fabric_desc, c.determination_id, d.stitch_length, c.gsm_weight, d.machine_dia, d.fabric_dia, d.width_dia_type, d.color_id, d.program_qnty, d.knitting_party, d.location_id, d.program_date, e.buyer_id, e.booking_no, d.remarks";
	$sqlOrderBy = " order by d.knitting_party, d.location_id";
	
	//for floor
	if($cbo_floor != 0)
	{
		if($location_name != 0)
			$locCond = " and location_id= ".$location_name."";
		else
			$locCond = "";
			
		$sqlTable .= ", ppl_planning_info_machine_dtls f";
		$sqlWhere .= " AND b.id = f.dtls_id AND f.machine_id IN(select id from lib_machine_name where company_id =".$company_name." ".$locCond." and floor_id = ".$cbo_floor." and category_id = 1 and status_active =1 and is_deleted=0)";
	}
	//for floor end

	$sql = "SELECT ".$sqlField." FROM ".$sqlTable." WHERE ".$sqlWhere.$sqlGroupBy.$sqlOrderBy;
	//echo $sql; die;
	$nameArray=sql_select( $sql );
	foreach ($nameArray as $row) 
	{
		$program_no_arr[$row[csf("program_no")]] = $row[csf("program_no")];

		$data_array[$row[csf("production_date")]][$company_library[$row[csf("knitting_party")]]."*".$location_library[$row[csf("location_id")]]][$row[csf("program_no")]]["program_qnty"] = $row[csf("program_qnty")];

		$data_array[$row[csf("production_date")]][$company_library[$row[csf("knitting_party")]]."*".$location_library[$row[csf("location_id")]]][$row[csf("program_no")]]["program_date"] = $row[csf("program_date")];

		$data_array[$row[csf("production_date")]][$company_library[$row[csf("knitting_party")]]."*".$location_library[$row[csf("location_id")]]][$row[csf("program_no")]]["buyer_id"] = $buyer_library[$row[csf("buyer_id")]];

		$data_array[$row[csf("production_date")]][$company_library[$row[csf("knitting_party")]]."*".$location_library[$row[csf("location_id")]]][$row[csf("program_no")]]["booking_no"] = $row[csf("booking_no")];

		$data_array[$row[csf("production_date")]][$company_library[$row[csf("knitting_party")]]."*".$location_library[$row[csf("location_id")]]][$row[csf("program_no")]]["start_date"] = $row[csf("start_date")];

		$data_array[$row[csf("production_date")]][$company_library[$row[csf("knitting_party")]]."*".$location_library[$row[csf("location_id")]]][$row[csf("program_no")]]["end_date"] = $row[csf("end_date")];
		$data_array[$row[csf("production_date")]][$company_library[$row[csf("knitting_party")]]."*".$location_library[$row[csf("location_id")]]][$row[csf("program_no")]]["construction"] = $construction_library[$row[csf("determination_id")]];
		$data_array[$row[csf("production_date")]][$company_library[$row[csf("knitting_party")]]."*".$location_library[$row[csf("location_id")]]][$row[csf("program_no")]]["fabric_desc"] = $row[csf("fabric_desc")];
		$data_array[$row[csf("production_date")]][$company_library[$row[csf("knitting_party")]]."*".$location_library[$row[csf("location_id")]]][$row[csf("program_no")]]["stitch_length"] = $row[csf("stitch_length")];
		$data_array[$row[csf("production_date")]][$company_library[$row[csf("knitting_party")]]."*".$location_library[$row[csf("location_id")]]][$row[csf("program_no")]]["gsm_weight"] = $row[csf("gsm_weight")];
		$data_array[$row[csf("production_date")]][$company_library[$row[csf("knitting_party")]]."*".$location_library[$row[csf("location_id")]]][$row[csf("program_no")]]["machine_dia"] = $row[csf("machine_dia")];
		$data_array[$row[csf("production_date")]][$company_library[$row[csf("knitting_party")]]."*".$location_library[$row[csf("location_id")]]][$row[csf("program_no")]]["fabric_dia"] = $row[csf("fabric_dia")];
		$data_array[$row[csf("production_date")]][$company_library[$row[csf("knitting_party")]]."*".$location_library[$row[csf("location_id")]]][$row[csf("program_no")]]["width_dia_type"] = $row[csf("width_dia_type")];
		$data_array[$row[csf("production_date")]][$company_library[$row[csf("knitting_party")]]."*".$location_library[$row[csf("location_id")]]][$row[csf("program_no")]]["color_id"] = $row[csf("color_id")];
		$data_array[$row[csf("production_date")]][$company_library[$row[csf("knitting_party")]]."*".$location_library[$row[csf("location_id")]]][$row[csf("program_no")]]["remarks"] = $row[csf("remarks")];
	}
	
	//for production
	$sqlTable = "wo_booking_dtls a, inv_receive_master b, ppl_planning_entry_plan_dtls c, ppl_planning_info_entry_dtls d, ppl_planning_info_entry_mst e";
	$sqlWhere = "c.dtls_id = d.id and d.mst_id = e.id and b.booking_id = d.id and a.booking_no = e.booking_no and b.receive_basis = 2 and b.entry_form = 2 and d.knitting_source = 1 and d.knitting_party = $company_name $location_cond $program_no_cond $booking_no_cond $job_no_cond $buyer_id_cond $dia_cond $color_cond $date_cond and c.status_active = 1 and d.status_active = 1 and b.status_active = 1 and a.status_active=1";
	//for floor
	if($cbo_floor != 0)
	{
		if($location_name != 0)
			$locCond = " and location_id= ".$location_name."";
		else
			$locCond = "";
			
		$sqlTable .= ", ppl_planning_info_machine_dtls f";
		$sqlWhere .= " AND b.id = f.dtls_id AND f.machine_id IN(select id from lib_machine_name where company_id =".$company_name." ".$locCond." and floor_id = ".$cbo_floor." and category_id = 1 and status_active =1 and is_deleted=0)";
	}
	//for floor end
	
	$sql2 = "SELECT ".$sqlField." FROM ".$sqlTable." WHERE ".$sqlWhere.$sqlGroupBy.$sqlOrderBy;
	//echo $sql2; die;
	$nameArray2=sql_select( $sql2 );
	foreach ($nameArray2 as $row) 
	{
		$program_no_arr[$row[csf("program_no")]] = $row[csf("program_no")];

		$data_array[$row[csf("production_date")]][$company_library[$row[csf("knitting_party")]]."*".$location_library[$row[csf("location_id")]]][$row[csf("program_no")]]["program_qnty"] = $row[csf("program_qnty")];

		$data_array[$row[csf("production_date")]][$company_library[$row[csf("knitting_party")]]."*".$location_library[$row[csf("location_id")]]][$row[csf("program_no")]]["program_date"] = $row[csf("program_date")];

		$data_array[$row[csf("production_date")]][$company_library[$row[csf("knitting_party")]]."*".$location_library[$row[csf("location_id")]]][$row[csf("program_no")]]["buyer_id"] = $buyer_library[$row[csf("buyer_id")]];

		$data_array[$row[csf("production_date")]][$company_library[$row[csf("knitting_party")]]."*".$location_library[$row[csf("location_id")]]][$row[csf("program_no")]]["booking_no"] = $row[csf("booking_no")];

		$data_array[$row[csf("production_date")]][$company_library[$row[csf("knitting_party")]]."*".$location_library[$row[csf("location_id")]]][$row[csf("program_no")]]["start_date"] = $row[csf("start_date")];

		$data_array[$row[csf("production_date")]][$company_library[$row[csf("knitting_party")]]."*".$location_library[$row[csf("location_id")]]][$row[csf("program_no")]]["end_date"] = $row[csf("end_date")];
		$data_array[$row[csf("production_date")]][$company_library[$row[csf("knitting_party")]]."*".$location_library[$row[csf("location_id")]]][$row[csf("program_no")]]["construction"] = $construction_library[$row[csf("determination_id")]];
		$data_array[$row[csf("production_date")]][$company_library[$row[csf("knitting_party")]]."*".$location_library[$row[csf("location_id")]]][$row[csf("program_no")]]["fabric_desc"] = $row[csf("fabric_desc")];
		$data_array[$row[csf("production_date")]][$company_library[$row[csf("knitting_party")]]."*".$location_library[$row[csf("location_id")]]][$row[csf("program_no")]]["stitch_length"] = $row[csf("stitch_length")];
		$data_array[$row[csf("production_date")]][$company_library[$row[csf("knitting_party")]]."*".$location_library[$row[csf("location_id")]]][$row[csf("program_no")]]["gsm_weight"] = $row[csf("gsm_weight")];
		$data_array[$row[csf("production_date")]][$company_library[$row[csf("knitting_party")]]."*".$location_library[$row[csf("location_id")]]][$row[csf("program_no")]]["machine_dia"] = $row[csf("machine_dia")];
		$data_array[$row[csf("production_date")]][$company_library[$row[csf("knitting_party")]]."*".$location_library[$row[csf("location_id")]]][$row[csf("program_no")]]["fabric_dia"] = $row[csf("fabric_dia")];
		$data_array[$row[csf("production_date")]][$company_library[$row[csf("knitting_party")]]."*".$location_library[$row[csf("location_id")]]][$row[csf("program_no")]]["width_dia_type"] = $row[csf("width_dia_type")];
		$data_array[$row[csf("production_date")]][$company_library[$row[csf("knitting_party")]]."*".$location_library[$row[csf("location_id")]]][$row[csf("program_no")]]["color_id"] = $row[csf("color_id")];
		$data_array[$row[csf("production_date")]][$company_library[$row[csf("knitting_party")]]."*".$location_library[$row[csf("location_id")]]][$row[csf("program_no")]]["remarks"] = $row[csf("remarks")];
	}

	$program_nos = implode(",", array_filter($program_no_arr));
	if($program_nos=="") $program_nos=0;
	$programcond = $producedProgramNoCond = ""; 
	$programcond2 = $producedProgramNoCond2 = ""; 
	$program_no_arr=explode(",",$program_nos);
	if($db_type==2 && count($program_no_arr)>999)
	{
		$program_no_chunk=array_chunk($program_no_arr,999) ;
		foreach($program_no_chunk as $chunk_arr)
		{
			$programcond.=" b.id in(".implode(",",$chunk_arr).") or ";	
			$programcond2.=" a.booking_id in(".implode(",",$chunk_arr).") or ";	
		}
				
		$producedProgramNoCond.=" and (".chop($programcond,'or ').")";			
		$producedProgramNoCond2.=" and (".chop($programcond2,'or ').")";			
	}
	else
	{ 	
		if($program_nos)
		{
			$producedProgramNoCond=" and b.id in($program_nos)";
			$producedProgramNoCond2=" and a.booking_id in($program_nos)";
		}   
	}

	$yarn_description_res = sql_select("select b.id as program_no,a.prod_id,c.id, c.yarn_count_id,c.lot, c.brand
	from  ppl_yarn_requisition_entry a, ppl_planning_info_entry_dtls b, product_details_master c
	where a.knit_id  = b.id and a.prod_id = c.id and b.status_active = 1 and a.status_active = 1 and c.status_active = 1 $producedProgramNoCond
	group by b.id,a.prod_id,c.id, c.yarn_count_id,c.lot, c.brand");
	foreach ($yarn_description_res as $val) 
	{
		$yarn_description_arr[$val[csf("program_no")]]["ycount"] .= $yarn_count_library[$val[csf("yarn_count_id")]].",";
		$yarn_description_arr[$val[csf("program_no")]]["ylot"] .= $val[csf("lot")].",";
		$yarn_description_arr[$val[csf("program_no")]]["brand"] .= $brand_library[$val[csf("brand")]].",";
	}

	$production_qty_arr =array();
	$production_qty_date_arr =array();
	//$production_qty_res = sql_select("select b.grey_receive_qnty, a.booking_id as program_no, a.receive_date from inv_receive_master a, pro_grey_prod_entry_dtls b where a.id = b.mst_id and a.entry_form = 2 and a.receive_basis = 2 and b.status_active = 1 and b.is_deleted = 0 and a.status_active = 1 and a.is_deleted = 0 $producedProgramNoCond2 ");

	$production_qty_res = sql_select("select b.grey_receive_qnty, a.booking_id as program_no, a.receive_date, c.program_qnty from inv_receive_master a, pro_grey_prod_entry_dtls b ,ppl_planning_info_entry_dtls c where a.id = b.mst_id and a.booking_id = c.id and a.entry_form = 2 and a.receive_basis = 2 and b.status_active = 1 and b.is_deleted = 0 and a.status_active = 1 and a.is_deleted = 0 $producedProgramNoCond2 ");
	foreach ($production_qty_res as $value) 
	{
		$production_qty_date_arr[$value[csf("program_no")]][$value[csf("receive_date")]] += $value[csf("grey_receive_qnty")];
		$production_qty_arr[$value[csf("program_no")]]+= $value[csf("grey_receive_qnty")];

		$top_grand_total_program +=  $value[csf("grey_receive_qnty")];

		if(strtotime($value[csf("receive_date")]) >= strtotime($start_date) && strtotime($value[csf("receive_date")]) <= strtotime($end_date))
		{
			$top_grand_today_prod +=  $value[csf("grey_receive_qnty")];
		}

		if($programChk[$value[csf("program_no")]] =="")
		{
			$programChk[$value[csf("program_no")]] = $value[csf("program_no")];
			$top_grand_total_program_qnty += $value[csf("program_qnty")];
		}

		$top_grand_total_prod +=  $value[csf("grey_receive_qnty")];
	}
	
	//for floor
	$sqlFloor = "SELECT a.dtls_id, b.floor_id FROM ppl_planning_info_machine_dtls a, lib_machine_name b WHERE a.machine_id=b.id ".where_con_using_array($program_no_arr, '0', 'a.dtls_id')." GROUP BY a.dtls_id, b.floor_id";
	//echo $sqlFloor;
	$resultSetFloor = sql_select($sqlFloor);
	$floorDataArr = array();
	foreach($resultSetFloor as $row)
	{
		$floorDataArr[$row[csf('dtls_id')]] = $floor_dtls[$row[csf('floor_id')]];
	}
	//echo "<pre>";
	//print_r($floorDataArr); die;
	
	ob_start();
	?>
	<fieldset style="width:1950px;">
		<table width="1430" cellspacing="0" cellpadding="0" border="0" rules="all" style="float: left;">
			<tr class="form_caption">
				<td colspan="16" align="center" style="border:none;font-size:16px; font-weight:bold"> <? echo $report_title; ?></td>
			</tr>
			<tr class="form_caption">
				<td colspan="16" align="center" style="border:none;"><? echo $company_library[$company_name]; ?></td>
			</tr>
			<tr class="form_caption">
				<td colspan="16" align="center" style="border:none;">&nbsp;</td>
			</tr>
		</table>
		<table width="500" cellspacing="0" cellpadding="0" border="0" rules="all" style="float: left;">
			<tr class="form_caption">
				<td colspan="6" align="center" style="border:none;font-size:16px; font-weight:bold"> <? //echo $report_title; ?></td>
			</tr>
			<tr class="form_caption"> 
				<td align="center" style="border: 1px solid black;" width="80">Grand Total</td>
				<td align="center" style="border: 1px solid black;" width="80" id="top_grand_total_program"><? echo $top_grand_total_program_qnty;?></td>
				<td align="center" style="border: 1px solid black;" width="80" id="top_grand_today_prod"><? echo $top_grand_today_prod;?></td>
				<td align="center" style="border: 1px solid black;" width="80" id="top_grand_total_prod"><? echo $top_grand_total_prod;?></td>
				<td align="center" style="border: 1px solid black;" width="80" id="top_grand_bal_prod">
					<? 
					 echo $top_grand_total_prod - $top_grand_today_prod;
					?>
				</td>
				<td align="center" width="100">&nbsp;</td>
			</tr>
			<tr class="form_caption"> 
				<td align="center" width="160" colspan="2">&nbsp;</td>
				<td align="right" width="340" colspan="4" style="color: black;">Report Print Date: <? echo date("d-m-Y"); ?></td>
			</tr>
		</table>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1930" class="rpt_table" >
			<thead>
				<tr>
					<th width="80">Program Date</th>
					<th width="80">Buyer</th>
					<th width="80">Booking</th>
					<th width="100">Floor</th>
					<th width="80">Program No.</th>
					<th width="80">Knit Start Date</th>
					<th width="80">Knit Com Date</th>
					<th width="80">Yarn Count</th>
					<th width="80">Yarn Lot</th>
					<th width="80">Yarn Brand</th>
					<th width="100">Fabric Construction</th>
					<th width="100">Fabric Composition</th>
					<th width="80">S/L</th>
					<th width="80">F/GSM</th>
					<th width="80">M/Dia</th>
					<th width="80">F/Dia</th>
					<th width="80">Width Type</th>
					<th width="80">Fabric Color</th>
					<th width="80"><p style="word-break: break-all;word-wrap: break-word;">Program Qnty.(Kgs)</p></th>
					<th width="80">Today Knit (Kg)</th>
					<th width="80">Total Knit Qty.(Kg)</th>
					<th width="80"><p style="word-break: break-all;word-wrap: break-word;">Total Bala.Qty(Kg)</p></th>
					<th>Remarks</th>
				</tr>
			</thead>
		</table>
		<div style="width:1948px; overflow-y:scroll; max-height:450px;" id="scroll_body">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1930" class="rpt_table" id="tbl_list_search">
				<tbody>
				<? //location_library
				$production_date_arr = array();
				$comp_loc_arr = array();
				$i = 1;
				foreach ($data_array as $production_date => $production_date_data) 
				{
					foreach ($production_date_data as $company_location => $company_location_data) 
					{
						$comp_loc_arr=array();
						foreach ($company_location_data as $program_id => $row) 
						{
							
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							if($production_date_arr[$production_date]=="")
							{
								$production_date_arr[$production_date] = $production_date;
								?>
								<tr style="font-weight: bold;">
									<td colspan="23"><? echo "Production Date : ".$production_date;?></td>
								</tr>
								<?
							}
							if($comp_loc_arr[$company_location]=="")
							{
								$comp_loc_arr[$company_location] = $company_location;
								$comLocArr = explode("*", $company_location);
								?>
								<tr style="font-weight: bold;">
									<td colspan="23"><? echo $comLocArr[0]." , ". $comLocArr[1];?></td>
								</tr>
								<?
							}
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>"> 
								<td width="80"><? echo $row["program_date"];?></td>
								<td width="80"><p style="word-break: break-all;word-wrap: break-word;"><? echo $row["buyer_id"];?></p></td>
								<td width="80"><p><? echo $row["booking_no"];?></p></td>
								<td width="100"><p><? echo $floorDataArr[$program_id];?></p></td>
								<td width="80"><? echo $program_id;?></td>
								<td width="80"><? echo $row["start_date"];?></td>
								<td width="80"><? echo $row["end_date"];?></td>
								<td width="80"><p style="word-break: break-all;word-wrap: break-word;"><? echo implode(",",array_unique(array_filter(explode(",", $yarn_description_arr[$program_id]["ycount"]))));?></p></td>
								<td width="80"><p style="word-break: break-all;word-wrap: break-word;"><? echo implode(",",array_unique(array_filter(explode(",", $yarn_description_arr[$program_id]["ylot"]))));?></p></td>
								<td width="80"><p style="word-break: break-all;word-wrap: break-word;"><? echo implode(",",array_unique(array_filter(explode(",", $yarn_description_arr[$program_id]["brand"]))));?></p></td>
								<td width="100"><p style="word-break: break-all;word-wrap: break-word;"><? echo $row["construction"]?></p></td>
								<td width="100"><p style="word-break: break-all;word-wrap: break-word;"><? echo $row["fabric_desc"]?></p></td>
								<td width="80"><p style="word-break: break-all;word-wrap: break-word;"><? echo $row["stitch_length"]?></p></td>
								<td width="80"><p style="word-break: break-all;word-wrap: break-word;"><? echo $row["gsm_weight"]?></p></td>
								<td width="80"><p style="word-break: break-all;word-wrap: break-word;"><? echo $row["machine_dia"]?></p></td>
								<td width="80"><p style="word-break: break-all;word-wrap: break-word;"><? echo $row["fabric_dia"]?></p></td>
								<td width="80"><p style="word-break: break-all;word-wrap: break-word;"><? echo $fabric_typee[$row["width_dia_type"]]?></p></td>
								<td width="80"><p style="word-break: break-all;word-wrap: break-word;">
									<? 
									$colors_name="";
									foreach(explode(",",$row["color_id"]) as $colorId){
										$colors_name .= $color_library[$colorId].",";
									}
									echo chop($colors_name,",");
									?></p>
								</td>
								<td width="80" align="right"><p><? echo number_format($row["program_qnty"],2,'.','');?></p></td>
								<td width="80" align="right">
									<p>
										<? 
										$today_production = $production_qty_date_arr[$program_id][$production_date];
										echo number_format($today_production,2,'.','');
										?>
									</p>
								</td>
								<td width="80" align="right">
									<p>
										<? 
											$program_production = $production_qty_arr[$program_id];
											echo number_format($program_production,2,'.','');
										?>
									</p>
									</td>
								<td width="80" align="right">
									<p>
										<? 
										$bal_production = $program_production-$today_production;
										echo number_format($bal_production,2,'.','');
										?>
									</p>
								</td>
								<td><p style="word-break: break-all;word-wrap: break-word;"><? echo $row["remarks"];?></p></td>
							</tr>

							<?
							$i++;

							$subCompLocTotProgQnty += $row["program_qnty"]; 
							$subCompLocTotTodayProduction += $today_production;
							$subCompLocTotProgram_production += $program_production;
							$subCompLocTotBal_production += $bal_production;

							$subDateProgSubTot += $row["program_qnty"]; 
							$subDateTotTodayProduction += $today_production;
							$subDateTotProgram_production += $program_production;
							$subDateTotBal_production += $bal_production;

							$grandDateProgSubTot += $row["program_qnty"]; 
							$grandDateTotTodayProduction += $today_production;
							$grandDateTotProgram_production += $program_production;
							$grandDateTotBal_production += $bal_production;
							
						}
						?>
							<tr style="background-color: #eee;">
								<td colspan="18"> Company Location Total :</td>
								<td align="right"><? echo number_format($subCompLocTotProgQnty,2,'.','');?></td>
								<td align="right"><? echo number_format($subCompLocTotTodayProduction,2,'.','');?></td>
								<td align="right"><? echo number_format($subCompLocTotProgram_production,2,'.','');?></td>

								<td align="right"><? echo number_format($subCompLocTotBal_production,2,'.','');?></td>
								<td>&nbsp;</td>
							</tr>
						<?
						$subCompLocTotProgQnty=0; $subCompLocTotTodayProduction=0;$subCompLocTotProgram_production=0;$subCompLocTotBal_production=0;
					}
					?>
						<tr style="background-color: #ccc; font-size: 13">
							<td colspan="18"> Production Total :</td>
							<td align="right"><? echo number_format($subDateProgSubTot,2,'.','');?></td>
							<td align="right"><? echo number_format($subDateTotTodayProduction,2,'.','');?></td>
							<td align="right"><? echo number_format($subDateTotProgram_production,2,'.','');?></td>
							<td align="right"><? echo number_format($subDateTotBal_production,2,'.','');?></td>
							<td>&nbsp;</td>
						</tr>
					<?
					$subDateProgSubTot=0;$subDateTotTodayProduction=0;$subDateTotProgram_production=0;$subDateTotBal_production=0;
				}
				?>
					<tr style="background-color: #ccc; font-size: 13;border-top: 2px solid black;font-weight: bold;">
						<td colspan="18"><b>Grand Total :</b></td>
						<td id="grand_program_qnty" align="right"><? echo number_format($grandDateProgSubTot,2,'.','');?></td>
						<td id="grand_today_prod_qnty" align="right"><? echo number_format($grandDateTotTodayProduction,2,'.','');?></td>
						<td id="grand_total_prod_qnty" align="right"><? echo number_format($grandDateTotProgram_production,2,'.','');?></td>
						<td id="grand_bal_prod_qnty" align="right"><? echo number_format($grandDateTotBal_production,2,'.','');?></td>
						<td>&nbsp;</td>
					</tr>
				
				</tbody>
			</table>
		</div>
	</fieldset>
	<?
	$html = ob_get_contents();
    ob_clean();
    foreach (glob("$user_id*.xls") as $filename) {
        @unlink($filename);
    }
    $name = time();
    $filename = $user_id . "_" . $name . ".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, $html);
    echo "$html**$filename";
	exit();
}

if($action=="report_generate_outbound_222")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$company_name= str_replace("'","",$cbo_company_name);
	$program_no= str_replace("'","",$txt_program_no);
	$txt_booking_id= str_replace("'","",$txt_booking_id);
	$cbo_year_selection = str_replace("'","",$cbo_year_selection);
	$cbo_year_selection = substr($cbo_year_selection, -2);
	$txt_job_id = str_replace("'","",$txt_job_id);
	$txt_dia = trim(str_replace("'","",$txt_dia));
	$cbo_color = str_replace("'","",$cbo_color);
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and e.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and e.buyer_id in (".str_replace("'","",$cbo_buyer_name).")";
	}
	if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
	{
		if($db_type==0)
		{
			$start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
			$end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
		}
		else if($db_type==2)
		{
			$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
			$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
		}
		$date_cond=" and b.receive_date between '$start_date' and '$end_date'";
	}
	
	$job_no=str_replace("'","",$txt_job_no);
	$job_no_cond="";
	if ($job_no=="") 
	{
		$job_no_cond=""; 
	}
	else 
	{
		if($txt_job_id)
		{
			$job_no_cond=" and a.job_no '%".$job_no."%'";
		}
		else
		{
			$job_no_cond .=" and a.job_no '%".$job_no."%'";
			$job_no_cond .=" and a.job_no '%-".$cbo_year_selection."-%'";
		}
		
	}

	if ($program_no) $program_no_cond=" and d.id = $program_no ";  else $program_no_cond="";

	$booking_no=str_replace("'","",$txt_booking_no);
	if($booking_no == "") {
		$booking_no_cond ="";
	} 
	else 
	{
		if($txt_booking_id)
		{
			$booking_no_cond=" and e.booking_no like '%".trim($booking_no) ."%' "; 
		}else{
			$booking_no_cond=" and e.booking_no like '%".trim($booking_no) ."' and e.booking_no like '%-$cbo_year_selection-%'"; 
		}
	}

	if($txt_dia) $dia_cond = " and d.machine_dia= '$txt_dia'"; else $dia_cond = "";
	if($cbo_color) $color_cond = " and d.color_id= '$cbo_color'"; else $color_cond = "";

	/*
	$sql="select  b.receive_date as production_date,d.knitting_party, d.location_id, d.program_date, e.buyer_id, e.booking_no,d.id as program_no,d.start_date,d.end_date, c.fabric_desc, c.determination_id,d.stitch_length,c.gsm_weight, d.machine_dia,d.fabric_dia,d.width_dia_type,d.color_id,d.program_qnty,d.remarks
	from wo_booking_dtls a, inv_receive_master b, ppl_planning_entry_plan_dtls c, ppl_planning_info_entry_dtls d, ppl_planning_info_entry_mst e
	where c.dtls_id = d.id and d.mst_id = e.id and b.booking_id = d.id and a.booking_no = e.booking_no and b.receive_basis = 2 and b.entry_form = 2 and d.knitting_source = 3 and d.knitting_party = $company_name $program_no_cond $booking_no_cond $job_no_cond $buyer_id_cond $dia_cond $color_cond
	$date_cond and c.status_active = 1 and d.status_active = 1 and b.status_active = 1 and a.status_active=1
	group by  d.id ,d.start_date,d.end_date, c.fabric_desc, c.determination_id,d.stitch_length,c.gsm_weight, d.machine_dia,d.fabric_dia,d.width_dia_type,
	d.color_id,d.program_qnty,  b.receive_date,d.knitting_party, d.location_id, d.program_date, e.buyer_id, e.booking_no,d.remarks
	order by b.receive_date,d.knitting_party, d.location_id";
	*/
	
	
	$sqlField = "d.knitting_party, d.location_id, d.program_date, e.buyer_id, e.booking_no,d.id as program_no,d.start_date,d.end_date, c.fabric_desc, c.determination_id,d.stitch_length,c.gsm_weight, d.machine_dia,d.fabric_dia,d.width_dia_type,d.color_id,d.program_qnty,d.remarks";
	$sqlTable = "wo_booking_dtls a, inv_receive_master b, ppl_planning_entry_plan_dtls c, ppl_planning_info_entry_dtls d, ppl_planning_info_entry_mst e";
	$sqlWhere = "c.dtls_id = d.id and d.mst_id = e.id and b.booking_id != d.id and a.booking_no = e.booking_no and b.receive_basis = 2 and b.entry_form = 2 and d.knitting_source = 3 and d.knitting_party = $company_name $program_no_cond $booking_no_cond $job_no_cond $buyer_id_cond $dia_cond $color_cond  and d.program_date between '".$start_date."' and '".$end_date."' and c.status_active = 1 and d.status_active = 1 and b.status_active = 1 and a.status_active=1";
	$sqlGroupBy = " GROUP BY d.id ,d.start_date,d.end_date, c.fabric_desc, c.determination_id,d.stitch_length,c.gsm_weight, d.machine_dia,d.fabric_dia,d.width_dia_type, d.color_id,d.program_qnty,  b.receive_date,d.knitting_party, d.location_id, d.program_date, e.buyer_id, e.booking_no,d.remarks";
	$sqlOrderBy = " ORDER BY d.knitting_party, d.location_id";

	//for floor
	if($cbo_floor != 0)
	{
		if($location_name != 0)
			$locCond = " and location_id= ".$location_name."";
		else
			$locCond = "";
			
		$sqlTable .= ", ppl_planning_info_machine_dtls f";
		$sqlWhere .= " AND b.id = f.dtls_id AND f.machine_id IN(select id from lib_machine_name where company_id =".$company_name." ".$locCond." and floor_id = ".$cbo_floor." and category_id = 1 and status_active =1 and is_deleted=0)";
	}
	//for floor end

	$sql = "SELECT ".$sqlField." FROM ".$sqlTable." WHERE ".$sqlWhere.$sqlGroupBy.$sqlOrderBy;
	//echo $sql; die;
	$nameArray=sql_select( $sql );
	foreach ($nameArray as $row) 
	{
		$program_no_arr[$row[csf("program_no")]] = $row[csf("program_no")];

		$data_array[$row[csf("production_date")]][$company_library[$row[csf("knitting_party")]]][$row[csf("program_no")]]["program_qnty"] = $row[csf("program_qnty")];

		$data_array[$row[csf("production_date")]][$company_library[$row[csf("knitting_party")]]][$row[csf("program_no")]]["program_date"] = $row[csf("program_date")];

		$data_array[$row[csf("production_date")]][$company_library[$row[csf("knitting_party")]]][$row[csf("program_no")]]["buyer_id"] = $buyer_library[$row[csf("buyer_id")]];


		$data_array[$row[csf("production_date")]][$company_library[$row[csf("knitting_party")]]][$row[csf("program_no")]]["booking_no"] = $row[csf("booking_no")];

		$data_array[$row[csf("production_date")]][$company_library[$row[csf("knitting_party")]]][$row[csf("program_no")]]["start_date"] = $row[csf("start_date")];

		$data_array[$row[csf("production_date")]][$company_library[$row[csf("knitting_party")]]][$row[csf("program_no")]]["end_date"]= $row[csf("end_date")];
		$data_array[$row[csf("production_date")]][$company_library[$row[csf("knitting_party")]]][$row[csf("program_no")]]["construction"]= $construction_library[$row[csf("determination_id")]];
		$data_array[$row[csf("production_date")]][$company_library[$row[csf("knitting_party")]]][$row[csf("program_no")]]["fabric_desc"]= $row[csf("fabric_desc")];
		$data_array[$row[csf("production_date")]][$company_library[$row[csf("knitting_party")]]][$row[csf("program_no")]]["stitch_length"] = $row[csf("stitch_length")];
		$data_array[$row[csf("production_date")]][$company_library[$row[csf("knitting_party")]]][$row[csf("program_no")]]["gsm_weight"]= $row[csf("gsm_weight")];
		$data_array[$row[csf("production_date")]][$company_library[$row[csf("knitting_party")]]][$row[csf("program_no")]]["machine_dia"]= $row[csf("machine_dia")];
		$data_array[$row[csf("production_date")]][$company_library[$row[csf("knitting_party")]]][$row[csf("program_no")]]["fabric_dia"] = $row[csf("fabric_dia")];
		$data_array[$row[csf("production_date")]][$company_library[$row[csf("knitting_party")]]][$row[csf("program_no")]]["width_dia_type"] = $row[csf("width_dia_type")];
		$data_array[$row[csf("production_date")]][$company_library[$row[csf("knitting_party")]]][$row[csf("program_no")]]["color_id"] = $row[csf("color_id")];
		$data_array[$row[csf("production_date")]][$company_library[$row[csf("knitting_party")]]][$row[csf("program_no")]]["remarks"] = $row[csf("remarks")];

	}

	//for production
	$sqlTable = "wo_booking_dtls a, inv_receive_master b, ppl_planning_entry_plan_dtls c, ppl_planning_info_entry_dtls d, ppl_planning_info_entry_mst e";
	$sqlWhere = "c.dtls_id = d.id and d.mst_id = e.id and b.booking_id = d.id and a.booking_no = e.booking_no and b.receive_basis = 2 and b.entry_form = 2 and d.knitting_source = 3 and d.knitting_party = $company_name $program_no_cond $booking_no_cond $job_no_cond $buyer_id_cond $dia_cond $color_cond $date_cond and c.status_active = 1 and d.status_active = 1 and b.status_active = 1 and a.status_active=1";
	//for floor
	if($cbo_floor != 0)
	{
		if($location_name != 0)
			$locCond = " and location_id= ".$location_name."";
		else
			$locCond = "";
			
		$sqlTable .= ", ppl_planning_info_machine_dtls f";
		$sqlWhere .= " AND b.id = f.dtls_id AND f.machine_id IN(select id from lib_machine_name where company_id =".$company_name." ".$locCond." and floor_id = ".$cbo_floor." and category_id = 1 and status_active =1 and is_deleted=0)";
	}
	//for floor end
	$sql2 = "SELECT ".$sqlField." FROM ".$sqlTable." WHERE ".$sqlWhere.$sqlGroupBy.$sqlOrderBy;
	//echo $sql; die;
	$nameArray2=sql_select( $sql2 );
	foreach ($nameArray2 as $row) 
	{
		$program_no_arr[$row[csf("program_no")]] = $row[csf("program_no")];

		$data_array[$row[csf("production_date")]][$company_library[$row[csf("knitting_party")]]][$row[csf("program_no")]]["program_qnty"] = $row[csf("program_qnty")];

		$data_array[$row[csf("production_date")]][$company_library[$row[csf("knitting_party")]]][$row[csf("program_no")]]["program_date"] = $row[csf("program_date")];

		$data_array[$row[csf("production_date")]][$company_library[$row[csf("knitting_party")]]][$row[csf("program_no")]]["buyer_id"] = $buyer_library[$row[csf("buyer_id")]];

		$data_array[$row[csf("production_date")]][$company_library[$row[csf("knitting_party")]]][$row[csf("program_no")]]["booking_no"] = $row[csf("booking_no")];

		$data_array[$row[csf("production_date")]][$company_library[$row[csf("knitting_party")]]][$row[csf("program_no")]]["start_date"] = $row[csf("start_date")];

		$data_array[$row[csf("production_date")]][$company_library[$row[csf("knitting_party")]]][$row[csf("program_no")]]["end_date"]= $row[csf("end_date")];
		$data_array[$row[csf("production_date")]][$company_library[$row[csf("knitting_party")]]][$row[csf("program_no")]]["construction"]= $construction_library[$row[csf("determination_id")]];
		$data_array[$row[csf("production_date")]][$company_library[$row[csf("knitting_party")]]][$row[csf("program_no")]]["fabric_desc"]= $row[csf("fabric_desc")];
		$data_array[$row[csf("production_date")]][$company_library[$row[csf("knitting_party")]]][$row[csf("program_no")]]["stitch_length"] = $row[csf("stitch_length")];
		$data_array[$row[csf("production_date")]][$company_library[$row[csf("knitting_party")]]][$row[csf("program_no")]]["gsm_weight"]= $row[csf("gsm_weight")];
		$data_array[$row[csf("production_date")]][$company_library[$row[csf("knitting_party")]]][$row[csf("program_no")]]["machine_dia"]= $row[csf("machine_dia")];
		$data_array[$row[csf("production_date")]][$company_library[$row[csf("knitting_party")]]][$row[csf("program_no")]]["fabric_dia"] = $row[csf("fabric_dia")];
		$data_array[$row[csf("production_date")]][$company_library[$row[csf("knitting_party")]]][$row[csf("program_no")]]["width_dia_type"] = $row[csf("width_dia_type")];
		$data_array[$row[csf("production_date")]][$company_library[$row[csf("knitting_party")]]][$row[csf("program_no")]]["color_id"] = $row[csf("color_id")];
		$data_array[$row[csf("production_date")]][$company_library[$row[csf("knitting_party")]]][$row[csf("program_no")]]["remarks"] = $row[csf("remarks")];

	}
	
	$program_nos = implode(",", array_filter($program_no_arr));
	if($program_nos=="") $program_nos=0;
	$programcond = $producedProgramNoCond = ""; 
	$programcond2 = $producedProgramNoCond2 = ""; 
	$program_no_arr=explode(",",$program_nos);
	if($db_type==2 && count($program_no_arr)>999)
	{
		$program_no_chunk=array_chunk($program_no_arr,999) ;
		foreach($program_no_chunk as $chunk_arr)
		{
			$programcond.=" b.id in(".implode(",",$chunk_arr).") or ";	
			$programcond2.=" a.booking_id in(".implode(",",$chunk_arr).") or ";	
		}
				
		$producedProgramNoCond.=" and (".chop($programcond,'or ').")";			
		$producedProgramNoCond2.=" and (".chop($programcond2,'or ').")";			
		
	}
	else
	{ 	
		if($program_nos)
		{
			$producedProgramNoCond=" and b.id in($program_nos)";
			$producedProgramNoCond2=" and a.booking_id in($program_nos)";
		}   
	}

	$yarn_description_res = sql_select("select b.id as program_no,a.prod_id,c.id, c.yarn_count_id,c.lot, c.brand
	from  ppl_yarn_requisition_entry a, ppl_planning_info_entry_dtls b, product_details_master c
	where a.knit_id  = b.id and a.prod_id = c.id and b.status_active = 1 and a.status_active = 1 and c.status_active = 1 $producedProgramNoCond
	group by b.id,a.prod_id,c.id, c.yarn_count_id,c.lot, c.brand");
	foreach ($yarn_description_res as $val) 
	{
		$yarn_description_arr[$val[csf("program_no")]]["ycount"] .= $yarn_count_library[$val[csf("yarn_count_id")]].",";
		$yarn_description_arr[$val[csf("program_no")]]["ylot"] .= $val[csf("lot")].",";
		$yarn_description_arr[$val[csf("program_no")]]["brand"] .= $brand_library[$val[csf("brand")]].",";
	}

	$production_qty_arr =array();$production_qty_date_arr =array();

	$production_qty_res = sql_select("select b.grey_receive_qnty, a.booking_id as program_no, a.receive_date, c.program_qnty from inv_receive_master a, pro_grey_prod_entry_dtls b ,ppl_planning_info_entry_dtls c where a.id = b.mst_id and a.booking_id = c.id and a.entry_form = 2 and a.receive_basis = 2 and b.status_active = 1 and b.is_deleted = 0 and a.status_active = 1 and a.is_deleted = 0 $producedProgramNoCond2 ");
	foreach ($production_qty_res as $value) 
	{
		$production_qty_date_arr[$value[csf("program_no")]][$value[csf("receive_date")]] += $value[csf("grey_receive_qnty")];
		$production_qty_arr[$value[csf("program_no")]]+= $value[csf("grey_receive_qnty")];

		$top_grand_total_program +=  $value[csf("grey_receive_qnty")];

		if(strtotime($value[csf("receive_date")]) >= strtotime($start_date) && strtotime($value[csf("receive_date")]) <= strtotime($end_date))
		{
			$top_grand_today_prod +=  $value[csf("grey_receive_qnty")];
		}

		if($programChk[$value[csf("program_no")]] =="")
		{
			$programChk[$value[csf("program_no")]] = $value[csf("program_no")];
			$top_grand_total_program_qnty += $value[csf("program_qnty")];
		}

		$top_grand_total_prod +=  $value[csf("grey_receive_qnty")];
	}
	
	//for floor
	$sqlFloor = "SELECT a.dtls_id, b.floor_id FROM ppl_planning_info_machine_dtls a, lib_machine_name b WHERE a.machine_id=b.id ".where_con_using_array($program_no_arr, '0', 'a.dtls_id')." GROUP BY a.dtls_id, b.floor_id";
	//echo $sqlFloor;
	$resultSetFloor = sql_select($sqlFloor);
	$floorDataArr = array();
	foreach($resultSetFloor as $row)
	{
		$floorDataArr[$row[csf('dtls_id')]] = $floor_dtls[$row[csf('floor_id')]];
	}
	//echo "<pre>";
	//print_r($floorDataArr); die;
	
	ob_start();
	?>
	<fieldset style="width:1950px;">
		<table width="1430" cellspacing="0" cellpadding="0" border="0" rules="all" style="float: left;">
			<tr class="form_caption">
				<td colspan="16" align="center" style="border:none;font-size:16px; font-weight:bold"> <? echo $report_title; ?></td>
			</tr>
			<tr class="form_caption">
				<td colspan="16" align="center"><? echo $company_library[$company_name]; ?></td>
			</tr>
			<tr class="form_caption">
				<td colspan="16" align="center">&nbsp;</td>
			</tr>
		</table>
		<table width="500" cellspacing="0" cellpadding="0" border="0" rules="all" style="float: left;">
			<tr class="form_caption">
				<td colspan="6" align="center" style="border:none;font-size:16px; font-weight:bold"> <? //echo $report_title; ?></td>
			</tr>
			<tr class="form_caption"> 
				<td align="center" style="border: 1px solid black;" width="80">Grand Total</td>
				<td align="center" style="border: 1px solid black;" width="80" id="top_grand_total_program"><? echo $top_grand_total_program_qnty;?></td>
				<td align="center" style="border: 1px solid black;" width="80" id="top_grand_today_prod"><? echo $top_grand_today_prod;?></td>
				<td align="center" style="border: 1px solid black;" width="80" id="top_grand_total_prod"><? echo $top_grand_total_prod;?></td>
				<td align="center" style="border: 1px solid black;" width="80" id="top_grand_bal_prod">
					<? 
					 echo $top_grand_total_prod - $top_grand_today_prod;
					?>
				</td>
				<td align="center" width="100">&nbsp;</td>
			</tr>
			<tr class="form_caption"> 
				<td align="center" width="160" colspan="2">&nbsp;</td>
				<td align="right" width="340" colspan="4" style="color: black;">Report Print Date: <? echo date("d-m-Y"); ?></td>
			</tr>
		</table>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1930" class="rpt_table" >
			<thead>
				<tr>
					<th width="80">Program Date</th>
					<th width="80">Buyer</th>
					<th width="80">Booking</th>
					<th width="100">Floor</th>
					<th width="80">Program No.</th>
					<th width="80">Knit Start Date</th>
					<th width="80">Knit Com Date</th>
					<th width="80">Yarn Count</th>
					<th width="80">Yarn Lot</th>
					<th width="80">Yarn Brand</th>
					<th width="100">Fabric Construction</th>
					<th width="100">Fabric Composition</th>
					<th width="80">S/L</th>
					<th width="80">F/GSM</th>
					<th width="80">M/Dia</th>
					<th width="80">F/Dia</th>
					<th width="80">Width Type</th>
					<th width="80">Fabric Color</th>
					<th width="80"><p style="word-break: break-all;word-wrap: break-word;">Program Qnty.(Kgs)</p></th>
					<th width="80">Today Knit (Kg)</th>
					<th width="80">Total Knit Qty.(Kg)</th>
					<th width="80"><p style="word-break: break-all;word-wrap: break-word;">Total Bala.Qty(Kg)</p></th>
					<th>Remarks</th>
				</tr>
			</thead>
		</table>
		<div style="width:1848px; overflow-y:scroll; max-height:450px;" id="scroll_body">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1830" class="rpt_table" id="tbl_list_search">
				<tbody>
				<? //location_library
				$production_date_arr = array();
				$comp_loc_arr = array();
				$i = 1;
				foreach ($data_array as $production_date => $production_date_data) 
				{
					foreach ($production_date_data as $company => $company_data) 
					{
						$comp_loc_arr=array();
						foreach ($company_data as $program_id => $row) 
						{
							
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							if($production_date_arr[$production_date]=="")
							{
								$production_date_arr[$production_date] = $production_date;
								?>
								<tr style="font-weight: bold;">
									<td colspan="22"><? echo "Production Date : ".$production_date;?></td>
								</tr>
								<?
							}
							if($comp_loc_arr[$company]=="")
							{
								$comp_loc_arr[$company] = $company;
								?>
								<tr style="font-weight: bold;">
									<td colspan="22"><? echo $company;?></td>
								</tr>
								<?
							}
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>"> 
								<td width="80"><? echo $row["program_date"];?></td>
								<td width="80"><p style="word-break: break-all;word-wrap: break-word;"><? echo $row["buyer_id"];?></p></td>
								<td width="80"><? echo $row["booking_no"];?></td>
								<td width="100"><p><? echo $floorDataArr[$program_id];?></p></td>
								<td width="80"><? echo $program_id;?></td>
								<td width="80"><? echo $row["start_date"];?></td>
								<td width="80"><? echo $row["end_date"];?></td>
								<td width="80"><p style="word-break: break-all;word-wrap: break-word;"><? echo implode(",",array_unique(array_filter(explode(",", $yarn_description_arr[$program_id]["ycount"]))));?></p></td>
								<td width="80"><p style="word-break: break-all;word-wrap: break-word;"><? echo implode(",",array_unique(array_filter(explode(",", $yarn_description_arr[$program_id]["ylot"]))));?></p></td>
								<td width="80"><p style="word-break: break-all;word-wrap: break-word;"><? echo implode(",",array_unique(array_filter(explode(",", $yarn_description_arr[$program_id]["brand"]))));?></p></td>
								<td width="100"><p style="word-break: break-all;word-wrap: break-word;"><? echo $row["construction"]?></p></td>
								<td width="100"><p style="word-break: break-all;word-wrap: break-word;"><? echo $row["fabric_desc"]?></p></td>
								<td width="80"><p style="word-break: break-all;word-wrap: break-word;"><? echo $row["stitch_length"]?></p></td>
								<td width="80"><p style="word-break: break-all;word-wrap: break-word;"><? echo $row["gsm_weight"]?></p></td>
								<td width="80"><p style="word-break: break-all;word-wrap: break-word;"><? echo $row["machine_dia"]?></p></td>
								<td width="80"><p style="word-break: break-all;word-wrap: break-word;"><? echo $row["fabric_dia"]?></p></td>
								<td width="80"><p style="word-break: break-all;word-wrap: break-word;"><? echo $fabric_typee[$row["width_dia_type"]]?></p></td>
								<td width="80">
									<p style="word-break: break-all;word-wrap: break-word;">
										<? 
										$colors_name="";
										foreach(explode(",",$row["color_id"]) as $colorId){
											$colors_name .= $color_library[$colorId].",";
										}
										echo chop($colors_name,",");
										?>	
									</p>
								</td>
								<td width="80" align="right"><p><? echo number_format($row["program_qnty"],2,'.','');?></p></td>
								<td width="80" align="right">
									<p>
										<? 
										$today_production = $production_qty_date_arr[$program_id][$production_date];
										echo number_format($today_production,2,'.','');
										?>
									</p>
								</td>
								<td width="80" align="right">
									<p>
										<? 
											$program_production = $production_qty_arr[$program_id];
											echo number_format($program_production,2,'.','');
										?>
									</p>
									</td>
								<td width="80" align="right">
									<p>
										<? 
										$bal_production = $program_production-$today_production;
										echo number_format($bal_production,2,'.','');
										?>
									</p>
								</td>
								<td><p style="word-break: break-all;word-wrap: break-word;"><? echo $row["remarks"];?></p></td>
							</tr>

							<?
							$i++;

							$subCompLocTotProgQnty += $row["program_qnty"]; 
							$subCompLocTotTodayProduction += $today_production;
							$subCompLocTotProgram_production += $program_production;
							$subCompLocTotBal_production += $bal_production;

							$subDateProgSubTot += $row["program_qnty"]; 
							$subDateTotTodayProduction += $today_production;
							$subDateTotProgram_production += $program_production;
							$subDateTotBal_production += $bal_production;

							$grandDateProgSubTot += $row["program_qnty"]; 
							$grandDateTotTodayProduction += $today_production;
							$grandDateTotProgram_production += $program_production;
							$grandDateTotBal_production += $bal_production;
							
						}
						?>
							<tr style="background-color: #eee;">
								<td colspan="18"> Company Total :</td>
								<td align="right"><? echo number_format($subCompLocTotProgQnty,2,'.','');?></td>
								<td align="right"><? echo number_format($subCompLocTotTodayProduction,2,'.','');?></td>
								<td align="right"><? echo number_format($subCompLocTotProgram_production,2,'.','');?></td>
								<td align="right"><? echo number_format($subCompLocTotBal_production,2,'.','');?></td>
								<td>&nbsp;</td>
							</tr>
						<?
						$subCompLocTotProgQnty=0; $subCompLocTotTodayProduction=0;$subCompLocTotProgram_production=0;$subCompLocTotBal_production=0;
					}
					?>
						<tr style="background-color: #ccc; font-size: 13">
							<td colspan="18"> Production Total :</td>
							<td align="right"><? echo number_format($subDateProgSubTot,2,'.','');?></td>
							<td align="right"><? echo number_format($subDateTotTodayProduction,2,'.','');?></td>
							<td align="right"><? echo number_format($subDateTotProgram_production,2,'.','');?></td>
							<td align="right"><? echo number_format($subDateTotBal_production,2,'.','');?></td>
							<td>&nbsp;</td>
						</tr>
					<?
					$subDateProgSubTot=0;$subDateTotTodayProduction=0;$subDateTotProgram_production=0;$subDateTotBal_production=0;
				}
				?>
					<tr style="background-color: #ccc; font-size: 13;border-top: 2px solid black;font-weight: bold;">
						<td colspan="18"><b>Grand Total :</b></td>
						<td id="grand_program_qnty" align="right"><? echo number_format($grandDateProgSubTot,2,'.','');?></td>
						<td id="grand_today_prod_qnty" align="right"><? echo number_format($grandDateTotTodayProduction,2,'.','');?></td>
						<td id="grand_total_prod_qnty" align="right"><? echo number_format($grandDateTotProgram_production,2,'.','');?></td>
						<td id="grand_bal_prod_qnty" align="right"><? echo number_format($grandDateTotBal_production,2,'.','');?></td>
						<td>&nbsp;</td>
					</tr>
				</tbody>
			</table>
		</div>
	</fieldset>
	<?
	$html = ob_get_contents();
    ob_clean();
    foreach (glob("$user_id*.xls") as $filename) {
        @unlink($filename);
    }
    $name = time();
    $filename = $user_id . "_" . $name . ".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, $html);
    echo "$html**$filename";
	exit();
}

if ($action == "booking_no_popup")
{
	echo load_html_head_contents("Booking Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>

		function js_set_value(str)
		{
			var splitData = str.split("_");
			//alert (splitData[1]);
			$("#hide_booking_id").val(splitData[0]); 
			$("#hide_booking_no").val(splitData[1]); 
			parent.emailwindow.hide();
		}
	</script>
	</head>
	<body>
		<div align="center">
			<form name="styleRef_form" id="styleRef_form">
				<fieldset style="width:740px;">
					<table width="740" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
						<thead>
							<th>Buyer</th>
							<th width="170">Please Enter Booking No</th>
							<th>Booking Date</th>

							<th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th>
							<input type="hidden" name="hide_booking_no" id="hide_booking_no" value="" />
							<input type="hidden" name="hide_booking_id" id="hide_booking_id" value="" />
						</thead>
						<tbody>
							<tr>
								<td align="center">
									<? 
									echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
									?>
								</td>   
								<td align="center">				
									<input type="text" style="width:150px" class="text_boxes" name="txt_booking_no" id="txt_booking_no" />	
								</td> 
								<td align="center">	
									<input type="text" style="width:70px" class="datepicker" name="txt_date_from" id="txt_date_from" readonly/> To
									<input type="text" style="width:70px" class="datepicker" name="txt_date_to" id="txt_date_to" readonly/>
								</td>     

								<td align="center">
									<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('txt_booking_no').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value, 'create_booking_no_search_list_view', 'search_div', 'program_and_dia_wise_knitting_balance_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
								</td>
							</tr>
							<tr>
								<td colspan="4" align="center" width="95%"><? echo load_month_buttons(1); ?></td>
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
if($action == "create_booking_no_search_list_view")
{
	$data=explode('**',$data);

	//if ($data[0]!=0) $company=" and company_id='$data[0]'"; 
	if ($data[1]!=0) $buyer=" and buyer_id='$data[1]'"; else $buyer="";
	if ($data[2]!=0) $booking_no=" and booking_no_prefix_num='$data[2]'"; else $booking_no='';
	if($db_type==0)
	{
		if ($data[3]!="" &&  $data[4]!="") $booking_date  = "and booking_date  between '".change_date_format($data[3], "yyyy-mm-dd", "-")."' and '".change_date_format($data[4], "yyyy-mm-dd", "-")."'"; else $booking_date ="";
	}
	if($db_type==2)
	{
		if ($data[3]!="" &&  $data[4]!="") $booking_date  = "and booking_date  between '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
	}

	$approved=array(0=>"No",1=>"Yes");
	$is_ready=array(0=>"No",1=>"Yes",2=>"No");
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$suplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
	$arr=array (2=>$comp,3=>$buyer_arr,5=>$item_category,6=>$fabric_source,7=>$suplier,8=>$approved,9=>$is_ready);
	
	$sql= "select id,booking_no_prefix_num, booking_no, booking_date, company_id, buyer_id, job_no, po_break_down_id, item_category, fabric_source, supplier_id, is_approved, ready_to_approved from wo_booking_mst where booking_type=1 $company $buyer $booking_no $booking_date and status_active=1 and is_deleted=0 order by id Desc"; 
	echo  create_list_view("list_view", "Booking No,Booking Date,Company,Buyer,Job No.,Fabric Nature,Fabric Source,Supplier,Approved,Is-Ready", "80,80,50,80,90,100,80,80,50,50","820","320",0, $sql , "js_set_value", "id,booking_no", "", 1, "0,0,company_id,buyer_id,0,item_category,fabric_source,supplier_id,is_approved,ready_to_approved", $arr , "booking_no,booking_date,company_id,buyer_id,job_no,item_category,fabric_source,supplier_id,is_approved,ready_to_approved", '','','0,3,0,0,0,0,0,0,0,0','','');
	exit(); 
}

if($action == "color_popup")
{
	echo load_html_head_contents("Booking Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>

		function js_set_value(str)
		{
			var splitData = str.split("_");
			$("#hide_color_id").val(splitData[0]); 
			$("#hide_color_name").val(splitData[1]); 
			parent.emailwindow.hide();
		}
	</script>
	</head>
	<body>
		<div align="center">
			<form name="styleRef_form" id="styleRef_form">
				<fieldset style="width:380px;">
					<table width="380" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
						<input type="hidden" name="hide_color_id" id="hide_color_id">
						<input type="hidden" name="hide_color_name" id="hide_color_name">
						<thead>
							<th width="40">Sl</th>
							<th>Color Name</th>
						</thead>
						<tbody id="color_body" style="cursor: pointer;">
							<?
							$result = sql_select("select id,color_name from lib_color where status_active = 1");
							$i=1;
							foreach ($result as $val) 
							{
								if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								?>
									<tr bgcolor="<? echo $bgcolor;?>" onClick="js_set_value('<? echo $val[csf("id")].'_'.$val[csf("color_name")];?>')">
										<td><? echo $i; ?></td>   
										<td align="center"><? echo $val[csf("color_name")];?></td>
									</tr>
								<? 
								$i++;
							}
							?>
						</tbody>
					</table>
				</fieldset>
			</form>
		</div>
	</body>  
	<script type="text/javascript">
		setFilterGrid("color_body",-1);
	</script>         
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>

	</html>
<?
exit();  
}
?>