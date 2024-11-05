<?
include('../../../../includes/common.php');
session_start();
extract($_REQUEST);
if ($_SESSION['logic_erp']['user_id'] == "") {
	header("location:login.php");
	die;
}
$date = date('Y-m-d');

$user_id = $_SESSION['logic_erp']['user_id'];

$data = $_REQUEST['data'];
$action = $_REQUEST['action'];


$userCredential = sql_select("SELECT brand_id, single_user_id FROM user_passwd where id=$user_id");
$userBuyerCredential = sql_select("SELECT buyer_id, single_user_id FROM user_passwd where id=$user_id");
$userbrand_id = $userCredential[0][csf('brand_id')];
$userbuyer_id = $userBuyerCredential[0][csf('buyer_id')];
$single_user_id = $userCredential[0][csf('single_user_id')];
$single_buyer_user_id = $userBuyerCredential[0][csf('single_user_id')];

$userbrand_idCond = "";
$filterBrandId = "";
if ($userbrand_id != '' && $single_user_id == 1) {
	$userbrand_idCond = "and id in ($userbrand_id)";
	$filterBrandId = $userbrand_id;
}

$userbuyer_idCond = "";
$filterBuyerId = "";
if ($userbuyer_id != '' && $single_buyer_user_id == 1) {
	$userbuyer_idCond = "and b.id in ($userbuyer_id)";
	$filterBuyerId = $userbuyer_id;
}

if ($action == "load_drop_down_buyer") {
	if ($data != 0) {
		echo create_drop_down("cbo_buyer_name", 130, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "");
		load_drop_down('requires/shipment_schedule_controller', this . value, 'load_drop_down_brand', 'brand_td');
		exit();
	} else {
		echo create_drop_down("cbo_buyer_name", 130, "select distinct buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "");
		load_drop_down('requires/shipment_schedule_controller', this . value, 'load_drop_down_brand', 'brand_td');
		exit();
	}
}
if ($action == "load_drop_down_brand") {
	echo create_drop_down("cbo_brand_name", 70, "select id, brand_name from lib_buyer_brand where buyer_id in($data) and status_active =1 and is_deleted=0 $userbrand_idCond order by brand_name ASC", "id,brand_name", 1, "--Select--", "", "");
	exit();
}
if ($action == "load_drop_down_team_leader") {
	echo create_drop_down("cbo_team_leader", 100, "select id,team_leader_name from lib_marketing_team  where id='$data' and status_active=1 and is_deleted=0 order by team_leader_name", "id,team_leader_name", 1, "-Team Leader-", $selected, "");
	exit();
}
if ($action == "buyer_popup") {
	echo load_html_head_contents("Buyer Info", "../../../../", 1, 1, '', '', '');
	extract($_REQUEST);
?>
	<script>
		var selected_id = new Array;
		var selected_name = new Array;

		function check_all_data() {
			var tbl_row_count = document.getElementById('tbl_list_search').rows.length;
			tbl_row_count = tbl_row_count - 1;

			for (var i = 1; i <= tbl_row_count; i++) {
				$('#tr_' + i).trigger('click');
			}
		}

		function toggle(x, origColor) {
			var newColor = 'yellow';
			if (x.style) {
				x.style.backgroundColor = (newColor == x.style.backgroundColor) ? origColor : newColor;
			}
		}

		function js_set_value(str) {

			if (str != "") str = str.split("_");

			toggle(document.getElementById('tr_' + str[0]), '#FFFFCC');

			if (jQuery.inArray(str[1], selected_id) == -1) {
				selected_id.push(str[1]);
				selected_name.push(str[2]);
			} else {
				for (var i = 0; i < selected_id.length; i++) {
					if (selected_id[i] == str[1]) break;
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

			$('#hide_party_id').val(id);
			$('#hide_party_name').val(name);
		}
	</script>
	<input type="hidden" name="hide_party_name" id="hide_party_name" value="" />
	<input type="hidden" name="hide_party_id" id="hide_party_id" value="" />
	<?


	$permitted_buyer_id = return_field_value("buyer_id", "user_passwd", "id='" . $user_id . "'");
	if ($permitted_buyer_id) {
		$buyerCon = " and id in($permitted_buyer_id)";
	}
	$sql = "select buy.id, buy.buyer_name from lib_buyer buy where buy.status_active =1 and buy.is_deleted=0 $buyerCon order by buyer_name";


	echo create_list_view("tbl_list_search", "Buyer Name", "380", "380", "270", 0, $sql, "js_set_value", "id,buyer_name", "", 1, "0", $arr, "buyer_name", "", 'setFilterGrid("tbl_list_search",-1);', '0', '', 1);

	exit();
}

if ($action == "load_drop_down_season") {
	echo create_drop_down("cbo_season", 80, "select id, season_name from lib_buyer_season where buyer_id in ($data) and status_active =1 and is_deleted=0 order by season_name ASC", "id,season_name", 1, "-- Select Season--", "", "");
	exit();
}

if ($action == "load_drop_down_team_member") {
	echo create_drop_down("cbo_team_member", 120, "select id,team_member_name 	 from lib_mkt_team_member_info  where team_id='$data' and status_active=1 and is_deleted=0 order by team_member_name", "id,team_member_name", 1, "-Select Team Member-", $selected, "");
	exit();
}
if($action=="style_search_popup")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
		<script>
		var selected_id = new Array;
		var selected_name = new Array;
		var selected_pid = new Array;
		var selected_poname = new Array;

		function check_all_data() {
			var tbl_row_count = document.getElementById('tbl_list_search').rows.length;
			tbl_row_count = tbl_row_count-1;

			for (var i = 1; i <= tbl_row_count; i++) {
				$('#tr_' + i).trigger('click');
			}
		}

		function toggle(x, origColor) {
			var newColor = 'yellow';
			if (x.style) {
				x.style.backgroundColor = (newColor == x.style.backgroundColor) ? origColor : newColor;
			}
		}

		function js_set_value(str) {

			if (str != "") str = str.split("_");

			toggle(document.getElementById('tr_' + str[0]), '#FFFFCC');

			if (jQuery.inArray(str[1], selected_id) == -1) {
				selected_id.push(str[1]);
				selected_name.push(str[2]);
				selected_pid.push(str[3]);
				selected_poname.push(str[4]);
			} else {
				for (var i = 0; i < selected_id.length; i++) {
					if (selected_id[i] == str[1]) break;
				}
				selected_id.splice(i, 1);
				selected_name.splice(i, 1);
				selected_pid.splice(i, 1);
				selected_poname.splice(i, 1);
			}
			var id = '';
			var name = '';
			var pid = '';
			var poname = '';
			for (var i = 0; i < selected_id.length; i++) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
				pid += selected_pid[i] + ',';
				poname += selected_poname[i] + ',';
			}

			id = id.substr(0, id.length - 1);
			name = name.substr(0, name.length - 1);
			pid = pid.substr(0, pid.length - 1);
			poname = poname.substr(0, poname.length - 1);


			$('#hide_style_id').val(id);
			$('#hide_style_no').val(name);
			$('#hide_po_id').val(pid);
			$('#hide_po_name').val(poname);
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
                    <th id="search_by_td_up" width="170">Please Enter No</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th>
					<input type="hidden" name="hide_style_id" id="hide_style_id" value="" />
                    <input type="hidden" name="hide_style_no" id="hide_style_no" value="" />
					<input type="hidden" name="hide_po_id" id="hide_po_id" value="" />
                    <input type="hidden" name="hide_po_name" id="hide_po_name" value="" />
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
						if($type_id==1){
							$search_by_arr=array(1=>"Order No",2=>"Style Ref");
							$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--",1,$dd,0 );
						}else{
							$search_by_arr=array(1=>"Order No",2=>"Style Ref");
							$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--",2,$dd,0 );
						}
						?>
                        </td>     
                        <td align="center" id="search_by_td">				
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" value="<? echo $txt_job_no?>"/>
                        </td> 	
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>', 'create_job_no_search_list_view', 'search_div', 'shipment_schedule_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
                    </td>
                    </tr>
            	</tbody>
           	</table>
            <div style="margin-top:15px" id="search_div"></div>
		</fieldset>
	</form>
    </div>
    </body>           
    <script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit(); 
}
if($action=="create_job_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$year_id=$data[4];
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
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
	$search_string=trim($data[3]);
	$search_cond="";
	if($search_string!="")
	{
		if($search_by==2) $search_cond=" and a.style_ref_no='$data[3]'";
		else if($search_by==1) $search_cond = "and b.po_number='$data[3]'";
	}
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
		$job_cond="id,style_ref_no,pid,po_number";
	}
	$arr=array (0=>$company_arr,1=>$buyer_arr);
	$sql= "select b.id as pid,b.po_number,a.id, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, $year_field from wo_po_details_master a, wo_po_break_down b where a.id=b.job_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name in ($company_id) $search_cond $buyer_id_cond $year_cond order by a.job_no desc ";
	
		echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No,Po Number", "120,130,80,60,100","700","240",0, $sql , "js_set_value", "$job_cond", "", 1, "company_name,buyer_name,0,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no,po_number", "",'setFilterGrid("tbl_list_search",-1);','0,0,0,0,0,0','',1);

	exit(); 
} 
if ($action == "report_generate") {
	$company_name = str_replace("'", "", $cbo_company_name);
	$style_owner = str_replace("'", "", $cbo_style_owner);
	$buyer_name = str_replace("'", "", $cbo_buyer_name);
	$team_name = str_replace("'", "", $cbo_team_name);
	$team_member = str_replace("'", "", $cbo_team_member);
	$search_by = str_replace("'", "", $cbo_search_by);
	$search_string = str_replace("'", "", $txt_search_string);
	$search_strings = str_replace("'", "", $txt_hidden_string);
	$search_pidStrings = str_replace("'", "", $txt_po_hidden_string);
	$txt_file = str_replace("'", "", $txt_file);
	$txt_ref = str_replace("'", "", $txt_ref);
	$date_from = str_replace("'", "", $txt_date_from);
	$date_to = str_replace("'", "", $txt_date_to);
	$category_by = str_replace("'", "", $cbo_category_by);
	$year_id = str_replace("'", "", $cbo_year);
	$rpt_type = str_replace("'", "", $rpt_type);
	$cbo_season = str_replace("'", "", $cbo_season);
	$ordstatus = str_replace("'", "", $cbo_ordstatus);
	$cbo_brand_name = str_replace("'", "", $cbo_brand_name);
	$cbo_season_year = str_replace("'", "", $cbo_season_year);
	$cbo_team_leader = str_replace("'", "", $cbo_team_leader);

	//
	//if($data[0]==0) $company_name="%%"; else $company_name=$data[0];
	//if($data[1]==0) $buyer_name="%%"; else $buyer_name=$data[1];
	/*if($company_name==0 && $style_owner==0 && $buyer_name==0 && $date_from=="" && $date_to=="" )
    {
        echo "<span style='color:red; font-weight:bold; font-size:20px; text-align:center'>Please select Company or Style Owner or Buyer first.";
        die;
    }*/
	if ($cbo_season != 0) $season_cond = "and a.season_buyer_wise=$cbo_season ";
	else $season_cond = "";
	if ($cbo_brand_name > 0) $brand_id_cond = "and a.brand_id in ($cbo_brand_name)";
	else $brand_id_cond = "";
	if ($cbo_season_year > 0) $season_yr_cond = "and a.season_year in ($cbo_season_year)";
	else $season_yr_cond = "";
	if ($cbo_team_leader > 0) $team_leader_cond = "and a.team_leader in ($cbo_team_leader)";
	else $team_leader_cond = "";
	// $season_yr_cond="and a.brand_id=$cbo_brand_name ";
	//season_year

	if ($buyer_name == 0) {
		if ($_SESSION['logic_erp']["data_level_secured"] == 1) {
			if ($_SESSION['logic_erp']["buyer_id"] != "") {
				$buyer_id_cond = " and a.buyer_name in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
				$buyer_id_cond2 = " and a.buyer_id in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
			} else {
				$buyer_id_cond = "";
				$buyer_id_cond2 = "";
			}
		} else {
			$buyer_id_cond = "";
			$buyer_id_cond2 = "";
		}
	} else {
		$buyer_id_cond = " and a.buyer_name in ($buyer_name) "; //.str_replace("'","",$cbo_buyer_name)
		$buyer_id_cond2 = " and a.buyer_id in ($buyer_name)";
	}

	if (trim($date_from) != "") $start_date = $date_from;
	if (trim($date_to) != "") $end_date = $date_to;

	$cbo_order_status2 = 2;
	if ($$cbo_order_status2 == 2) $cbo_order_status = "%%";
	else $cbo_order_status = "$cbo_order_status2";
	if (trim($team_name) == "0") $team_leader = "%%";
	else $team_leader = "$team_name";
	if (trim($team_member) == "0") $dealing_marchant = "%%";
	else $dealing_marchant = "$team_member";
	//if(trim($company_name)=="0") $company_name="%%"; else $company_name="$company_name";
	//if(trim($data[8])!="") $pocond="and b.id in(".str_replace("'",'',$data[8]).")"; else  $pocond="";
	if (trim($txt_file) != "") $file_cond = "and b.file_no=$txt_file";
	else $file_cond = "";
	if (trim($txt_ref) != "") $ref_cond = "and b.grouping='$txt_ref'";
	else $ref_cond = "";
	if (trim($ordstatus) != 0) $ordstatusCond = "and b.is_confirmed='$ordstatus'";
	else $ordstatusCond = "";
	if (trim($style_owner) == 0) $style_owner_cond = "";
	else $style_owner_cond = "and a.style_owner='$style_owner'";
	if ($company_name != 0) $company_name_cond = "and a.company_name in($company_name) ";
	else $company_name_cond = "";
	if ($company_name != 0) $company_name_cond2 = "and a.company_id in($company_name) ";
	else $company_name_cond2 = "";
	if ($db_type == 0) {
		$start_date = change_date_format($date_from, 'yyyy-mm-dd', '-');
		$end_date = change_date_format($date_to, 'yyyy-mm-dd', '-');
	} else if ($db_type == 2) {
		$start_date = change_date_format($date_from, 'yyyy-mm-dd', '-', 1);
		$end_date = change_date_format($date_to, 'yyyy-mm-dd', '-', 1);
	}

	//$cbo_category_by=$data[7]; $caption_date='';
	if ($category_by == 1) {
		if ($start_date != "" && $end_date != "") $date_cond = "and b.pub_shipment_date between '$start_date' and '$end_date'";
		else $date_cond = "";
	} else if ($category_by == 2) {
		if ($start_date != "" && $end_date != "") $date_cond = " and b.po_received_date between '$start_date' and '$end_date'";
		else $date_cond = "";
	} else if ($category_by == 3) {
		if ($start_date != "" && $end_date != "") {
			if ($db_type == 0) $date_cond = " and b.insert_date between '" . $start_date . "' and '" . $end_date . " 23:59:59'";
			else if ($db_type == 2) $date_cond = " and b.insert_date between '" . $start_date . "' and '" . $end_date . " 11:59:59 PM'";
		} else $date_cond = "";
	}
	//echo $date_cond;

	if ($search_by == 1) {
		if($search_pidStrings!=''){
			if ($search_pidStrings == "") $search_string_cond = "";
			else $search_string_cond = " and b.id in($search_pidStrings)";
		}else{
			if ($search_string == "") $search_string_cond = "";
			else $search_string_cond = " and b.po_number like '%$search_string%'";
		}
	} else if ($search_by == 2) {
		if($search_strings!=''){
			if ($search_strings == "") $search_string_cond = "";
			else $search_string_cond = " and a.id in($search_strings)";
		}else{
			if ($search_string == "") $search_string_cond = "";
			else $search_string_cond = " and a.style_ref_no in('$search_string')";
		}
		
	}
	if ($db_type == 0) {
		if ($year_id != 0) $year_cond = " and YEAR(a.insert_date)=$year_id";
		else $year_cond = "";
	} else if ($db_type == 2) {
		if ($year_id != 0) $year_cond = " and to_char(a.insert_date,'YYYY')=$year_id";
		else $year_cond = "";
	}
	$user_name_arr = return_library_array("select id, user_name from user_passwd", 'id', 'user_name');
	if ($rpt_type == 1) {
		$buyer_short_name_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');
		$bank_name_arr = return_library_array("select id, bank_name from lib_bank", 'id', 'bank_name');
		$company_short_name_arr = return_library_array("select id,company_short_name from lib_company", 'id', 'company_short_name');
		$buyer_wise_season_arr = return_library_array("select id, season_name from lib_buyer_season where status_active =1 and is_deleted=0", 'id', 'season_name');
		$company_team_name_arr = return_library_array("select id,team_name from lib_marketing_team", 'id', 'team_name');
		$company_team_member_name_arr = return_library_array("select id,team_member_name from  lib_mkt_team_member_info", 'id', 'team_member_name');
		$imge_arr = return_library_array("select master_tble_id, image_location from common_photo_library where file_type=1", 'master_tble_id', 'image_location');
		$cm_for_shipment_schedule_arr = return_library_array("select job_no,cm_for_sipment_sche from  wo_pre_cost_dtls", 'job_no', 'cm_for_sipment_sche');
		$days_to_realized_arr = return_library_array("select id, delivery_buffer_days from lib_buyer", 'id', 'delivery_buffer_days');
		

		$sql_res = sql_select("select b.po_break_down_id as po_id,
		sum(CASE WHEN b.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
		sum(CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END) as ex_factory_return_qnty
		from  pro_ex_factory_delivery_mst a,  pro_ex_factory_mst b  where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_name_cond2 $buyer_id_cond2 group by b.po_break_down_id");
		$ex_factory_qty_arr = array();
		foreach ($sql_res as $row) {
			$ex_factory_qty_arr[$row[csf('po_id')]]['del_qty'] = $row[csf('ex_factory_qnty')];
			$ex_factory_qty_arr[$row[csf('po_id')]]['return_qty'] = $row[csf('ex_factory_return_qnty')];
		}

		ob_start();
	?>
		<div align="center">
			<div align="center">
				<table>
					<tr valign="top">
						<td valign="top">
							<h3 align="left" id="accordion_h2" class="accordion_h" onClick="accordion_menu( this.id,'content_summary1_panel', '')"> -Summary Panel</h3>
							<div id="content_summary1_panel">
								<fieldset>
									<table width="750" cellspacing="0" cellpadding="0" class="rpt_table" border="1" rules="all">
										<thead>
											<th width="50">SL</th>
											<th width="130">Company Name</th>
											<th width="200">Buyer Name</th>
											<th width="130">Quantity</th>
											<th width="100">Value</th>
											<th width="50">Value %</th>
											<th width="130"><strong>Full Shipped</strong></th>
											<th width="130"><strong>Partial Shipped</strong></th>
											<th width="130"><strong>Running</strong></th>
											<th><strong>Ex-factory Percentage</strong></th>
										</thead>
										<tbody>
											<?
											$i = 1;
											$total_po = 0;
											$total_price = 0;
											$po_qnty_array = array();
											$po_value_array = array();
											$po_full_shiped_array = array();
											$po_full_shiped_value_array = array();
											$po_partial_shiped_array = array();
											$po_partial_shiped_value_array = array();
											$po_running_array = array();
											$po_running_value_array = array();
											$data_array = sql_select("select a.company_name,a.buyer_name,sum(b.po_quantity*a.total_set_qnty) as po_quantity, sum(b.po_total_price) as po_total_price   from wo_po_details_master a, wo_po_break_down b   where a.job_no=b.job_no_mst $company_name_cond $buyer_id_cond and a.team_leader like '$team_leader' and a.dealing_marchant like '$dealing_marchant' $style_owner_cond $date_cond $pocond $search_string_cond $file_cond $ordstatusCond $ref_cond $year_cond $team_leader_cond $brand_id_cond $season_yr_cond $season_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.company_name,a.buyer_name");

											foreach ($data_array as $row) {
												if ($i % 2 == 0) $bgcolor = "#E9F3FF";
												else $bgcolor = "#FFFFFF";
												//$data_array_po=sql_select("select a.company_name, b.id, sum(b.po_quantity*a.total_set_qnty) as po_quantity , b.shiping_status  from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst   and a.company_name =$row[company_name] and a.buyer_name =$row[buyer_name] and a.team_leader like '$team_leader' and a.dealing_marchant like '$dealing_marchant' $date_cond $pocond and a.status_active=1 and b.status_active=1 ");
												$data_array_po = sql_select("select a.company_name, b.id, (b.po_quantity*a.total_set_qnty) as po_quantity , b.shiping_status  from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst   and a.company_name =" . $row[csf('company_name')] . " and a.buyer_name =" . $row[csf('buyer_name')] . " and a.team_leader like '$team_leader' and a.dealing_marchant like '$dealing_marchant' $style_owner_cond $date_cond $pocond $search_string_cond $file_cond $ordstatusCond $ref_cond $year_cond  $brand_id_cond $season_yr_cond $team_leader_cond $season_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
												$full_shiped = 0;
												$partial_shiped = 0;
												foreach ($data_array_po as $row_po) {
													$ex_factory_del_qty = $ex_factory_qty_arr[$row_po[csf('id')]]['del_qty'];
													$ex_factory_return_qty = $ex_factory_qty_arr[$row_po[csf('id')]]['return_qty'];
													$ex_factory_qnty = $ex_factory_del_qty - $ex_factory_return_qty;
													if ($row_po[csf('shiping_status')] == 3) {
														$full_shiped += $ex_factory_qnty;
													}
													if ($row_po[csf('shiping_status')] == 2) {
														$partial_shiped += $ex_factory_qnty;
													}
												}
											?>
												<tr bgcolor="<? echo $bgcolor; ?>">
													<td width="50"><? echo $i; ?></td>
													<td width="130" style="word-break:break-all"><? echo $company_short_name_arr[$row[csf('company_name')]]; ?></td>
													<td width="200" style="word-break:break-all"><? echo $buyer_short_name_arr[$row[csf('buyer_name')]]; ?></td>
													<td width="130" align="right">
														<?
														echo number_format($row[csf('po_quantity')], 0);
														$total_po += $row[csf('po_quantity')];
														if (array_key_exists($row[csf('company_name')], $po_qnty_array)) {
															$po_qnty_array[$row[csf('company_name')]] += $row[csf('po_quantity')];
														} else {
															$po_qnty_array[$row[csf('company_name')]] = $row[csf('po_quantity')];
														}
														?>
													</td>
													<td width="100" align="right">
														<?
														echo number_format($row[csf('po_total_price')], 2);
														$total_price += $row[csf('po_total_price')];
														if (array_key_exists($row[csf('company_name')], $po_value_array)) {
															$po_value_array[$row[csf('company_name')]] += $row[csf('po_total_price')];
														} else {
															$po_value_array[$row[csf('company_name')]] = $row[csf('po_total_price')];
														}
														?><input type="hidden" id="value_<? echo $i; ?>" value="<? echo $row[csf('po_total_price')]; ?>" />
													</td>
													<td width="50" id="value_percent_<? echo $i; ?>" align="right"></td>
													<td width="130" align="right">
														<?
														echo number_format($full_shiped, 0);
														$full_shipped_total += $full_shiped;
														if (array_key_exists($row[csf('company_name')], $po_full_shiped_array)) {
															$po_full_shiped_array[$row[csf('company_name')]] += $full_shiped;
														} else {
															$po_full_shiped_array[$row[csf('company_name')]] = $full_shiped;
														}
														?>
													</td>
													<td width="130" align="right">
														<?
														echo number_format($partial_shiped, 0);
														$partial_shipped_total += $partial_shiped;
														if (array_key_exists($row[csf('company_name')], $po_partial_shiped_array)) {
															$po_partial_shiped_array[$row[csf('company_name')]] += $partial_shiped;
														} else {
															$po_partial_shiped_array[$row[csf('company_name')]] = $partial_shiped;
														}
														?>
													</td>
													<td width="130" align="right">
														<?
														$runing = $row[csf('po_quantity')] - ($full_shiped + $partial_shiped);
														echo number_format($runing, 0);
														$running_shipped_total += $runing;
														if (array_key_exists($row[csf('company_name')], $po_running_array)) {
															$po_running_array[$row[csf('company_name')]] += $runing;
														} else {
															$po_running_array[$row[csf('company_name')]] = $runing;
														}
														?>
													</td>
													<td align="right"><? $status = (($full_shiped + $partial_shiped) / $row[csf('po_quantity')]) * 100;
																		$full_shipped_total_percent += $status;
																		echo number_format($status, 2); ?></td>
												</tr>
											<?
												$i++;
											}
											?>
										</tbody>
										<tfoot>
											<th width="50"></th>
											<th width="130"></th>
											<th width="200"></th>
											<th width="130"><? echo number_format($total_po, 0); ?></th>
											<th width="100"><? echo number_format($total_price, 2); ?> <input type="hidden" id="total_value" value="<? echo $total_price; ?>" /></th>
											<th width="50"></th>
											<th width="130"><? echo number_format($full_shipped_total, 0); ?></th>
											<th width="130"><? echo number_format($partial_shipped_total, 0); ?></th>
											<th width="130"><? echo number_format($running_shipped_total, 0); ?></th>
											<th><input type="hidden" id="tot_row" value="<? echo $i; ?>" /></th>
										</tfoot>
									</table>
								</fieldset>
							</div>
						</td>
						<td valign="top">
							<h3 align="left" id="accordion_h3" class="accordion_h" onClick="accordion_menu( this.id,'content_summary2_panel', '')"> -Summary Panel</h3>
							<div id="content_summary2_panel">
								<fieldset>
									<table width="800" border="1" class="rpt_table" rules="all">
										<thead>
											<th>Company Name</th>
											<th>Particular Name</th>
											<th>Total Amount</th>
											<th>Full Shipped </th>
											<th>Partial Shipped </th>
											<th>Running </th>
											<th>Ex-factory Percentage</th>
										</thead>
										<?
										$comp_po_total = 0;
										$comp_po_total_value = 0;
										$total_full_shiped_qnty = 0;
										$total_par_qnty = 0;
										$total_run_qnty = 0;
										$total_full_shiped_val = 0;
										$total_par_val = 0;
										$total_run_val = 0;
										foreach ($po_qnty_array as $key => $value) {
										?>
											<tr>
												<td rowspan="2" align="center"><? echo $company_short_name_arr[$key]; //echo $company_name; 
																				?></td>
												<td align="center">PO Quantity</td>
												<td align="right"><? echo number_format($value + $po_qnty_array_projec[$key], 0);
																	$comp_po_total = $comp_po_total + $value + $po_qnty_array_projec[$key]; ?></td>
												<td align="right"><? echo number_format($po_full_shiped_array[$key], 0);
																	$total_full_shiped_qnty += $po_full_shiped_array[$key]; ?></td>
												<td align="right"><? echo number_format($po_partial_shiped_array[$key], 0);
																	$total_par_qnty += $po_partial_shiped_array[$key]; ?></td>
												<td align="right"><? echo number_format($po_running_array[$key], 0);
																	$total_run_qnty += $po_running_array[$key]; ?> </td>
												<td align="right"><? $ex_factory_per = (($po_full_shiped_array[$key] + $po_partial_shiped_array[$key]) / ($value)) * 100;
																	echo number_format($ex_factory_per, 2) . ' %'; ?></td>
											</tr>
											<tr bgcolor="white">
												<td align="center">LC Value</td>
												<td align="right"><? echo number_format($po_value_array[$key], 2);
																	$comp_po_total_value = $comp_po_total_value + $po_value_array[$key]; ?></td>
												<td align="right"><? $full_shiped_value = ($po_value_array[$key] / $value) * $po_full_shiped_array[$key];
																	echo number_format($full_shiped_value, 2);
																	$total_full_shiped_val += $full_shiped_value; ?></td>
												<td align="right"><? $full_partial_shipeddd_value = ($po_value_array[$key] / $value) * $po_partial_shiped_array[$key];
																	echo number_format($full_partial_shipeddd_value, 2);
																	$total_par_val += $full_partial_shipeddd_value; ?></td>
												<td align="right"><? $full_running_value = ($po_value_array[$key] / $value) * $po_running_array[$key];
																	echo number_format($full_running_value, 2);
																	$total_run_val += $full_running_value; ?></td>
												<td align="right"><? $ex_factory_per_value = (($full_shiped_value + $full_partial_shipeddd_value) / ($po_value_array[$key])) * 100;
																	echo number_format($ex_factory_per_value, 2) . ' %'; ?></td>
											</tr>
										<?
										}
										?>
										<tfoot>
											<tr>
												<th align="center" rowspan="2"> Total:</th>
												<th align="center">Qnty Total:</th>
												<th align="right"><? echo number_format($comp_po_total, 0); ?></th>
												<th align="right"><? echo number_format($total_full_shiped_qnty, 2); ?></th>
												<th align="right"><? echo number_format($total_par_qnty, 2); ?></th>
												<th align="right"><? echo number_format($total_run_qnty, 2); ?></th>
												<th align="right"><? //echo number_format($ex_factory_per_value,2).' %'; 
																	?></th>
											</tr>
											<tr bgcolor="#999999">
												<th align="center">Value Total:</th>
												<th align="right"><? echo number_format($comp_po_total_value, 2); ?></th>
												<th align="right"><? echo number_format($total_full_shiped_val, 2); ?></th>
												<th align="right"><? echo number_format($total_par_val, 2); ?></th>
												<th align="right"><? echo number_format($total_run_val, 2); ?></th>
												<th align="right"><? //echo number_format($ex_factory_per_value,2).' %'; 
																	?></th>
											</tr>
										</tfoot>
									</table>
								</fieldset>
							</div>
						</td>
						<td valign="top">
							<h3 align="left" id="accordion_h4" class="accordion_h" onClick="accordion_menu( this.id,'content_summary3_panel', '')"> -Shipment Performance Summary</h3>
							<div id="content_summary3_panel">
							</div>
						</td>
					</tr>
				</table>
				<h3 style="width:100%;" align="left" id="accordion_h4" class="accordion_h" onClick="accordion_menu( this.id,'content_report_panel', '')"> -Report Panel</h3>
				<div id="content_report_panel">
					<?
					$actual_po_no_arr = array();
					if ($db_type == 0) {
						$actual_po_sql = sql_select("Select po_break_down_id, group_concat(acc_po_no) as acc_po_no from wo_po_acc_po_info where status_active=1 and is_deleted=0 group by po_break_down_id");
					} else {
						$actual_po_sql = sql_select("Select po_break_down_id, listagg(cast(acc_po_no as varchar(4000)),',') within group(order by acc_po_no) as acc_po_no from  wo_po_acc_po_info where status_active=1 and is_deleted=0 group by po_break_down_id");
					}

					foreach ($actual_po_sql as $row) {
						$actual_po_no_arr[$row[csf('po_break_down_id')]] = $row[csf('acc_po_no')];
					}
					unset($actual_po_sql);
					//die;
					$sql_lc_result = sql_select("select a.wo_po_break_down_id, a.com_export_lc_id,b.internal_file_no,b.pay_term,b.tenor  from com_export_lc_order_info a, com_export_lc b where a.com_export_lc_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.wo_po_break_down_id,a.com_export_lc_id,b.internal_file_no,b.pay_term,b.tenor ");
					foreach ($sql_lc_result as $row) {
						$lc_id_arr[$row[csf('wo_po_break_down_id')]] = $row[csf('com_export_lc_id')];
						$export_lc_arr[$row[csf('wo_po_break_down_id')]]['file_no'] = $row[csf('internal_file_no')];
						$export_lc_arr[$row[csf('wo_po_break_down_id')]]['pay_term'] = $pay_term[$row[csf('pay_term')]];
						$export_lc_arr[$row[csf('wo_po_break_down_id')]]['tenor'] = $row[csf('tenor')];
					}
					unset($sql_lc_result);
					$sql_sc_result = sql_select("select a.wo_po_break_down_id, b.contract_no,b.internal_file_no,b.pay_term,b.tenor,b.lien_bank  from com_sales_contract_order_info a, com_sales_contract b where a.com_sales_contract_id=b.id and a.status_active=1 and 	a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.wo_po_break_down_id,b.contract_no,b.internal_file_no,b.pay_term,b.tenor,b.lien_bank ");
					foreach ($sql_sc_result as $row) {
						$sc_number_arr[$row[csf('wo_po_break_down_id')]] .= $row[csf('contract_no')] . ',';
						$sc_bank_arr[$row[csf('wo_po_break_down_id')]] .= $row[csf('lien_bank')] . ',';
						$export_sc_arr[$row[csf('wo_po_break_down_id')]]['file_no'] = $row[csf('internal_file_no')];
						$export_sc_arr[$row[csf('wo_po_break_down_id')]]['pay_term'] = $pay_term[$row[csf('pay_term')]];
						$export_sc_arr[$row[csf('wo_po_break_down_id')]]['tenor'] = $row[csf('tenor')];
					}
					unset($sql_sc_result);

					if ($search_by == 1) {
					?>
						<table width="4200" id="table_header_1" border="1" class="rpt_table" rules="all" align="left">
							<thead>
								<tr>
									<th width="50">SL</th>
									<th width="70">Company</th>
									<th width="70">Style Owner</th>
									<th width="70">Job No</th>
									<th width="60">Year</th>
									<th width="70">Buyer</th>
									<th width="110">PO No</th>
									<th width="100">Actual PO No</th>
									<th width="100">Ref No</th>
									<th width="100">Season</th>
									<th width="50">Agent</th>
									<th width="70">Order Status</th>
									<th width="70">Prod. Catg</th>
									<th width="40">Img</th>
									<th width="90">Style Ref</th>
									<th width="80">Gauge</th>
									<th width="150">Item</th>
									<th width="200">Fab. Description</th>
									<th width="70">Ship Date</th>
									<th width="70">PO Rec. Date</th>
									<th width="50">Days in Hand</th>
									<th width="90">Order Qnty(Pcs)</th>
									<th width="90">Order Qnty</th>
									<th width="40">Uom</th>
									<th width="50">Per Unit Price</th>
									<th width="100">Order Value</th>
									<th width="100">Lien Bank</th>
									<th width="100">LC/SC No</th>
									<th width="90">Ex. LC Amendment No(Last)</th>
									<th width="80"> Int.File No </th>
									<th width="80">Pay Term </th>
									<th width="80">Tenor </th>
									<th width="90">Ex-Fac Qnty </th>
									<th width="70">Last Ex-Fac Date</th>
									<th width="70"> Days To Realized</th>
									<th width="90">Short/Access Qnty</th>
									<th width="120">Short/Access Value</th>
									<th width="100">Yarn Req</th>
									<th width="100">CM </th>
									<th width="100">Shipping Status</th>
									<th width="150"> Team Member</th>
									<th width="150">Team Name</th>
									<th width="100">File No</th>
									<th width="40">Id</th>
									<th width="100">User Name</th>
									<th width="">Remarks</th>
								</tr>
							</thead>
						</table>
						<div style="max-height:400px; overflow-y:scroll; float:left; width:4220px;" id="scroll_body">
							<table width="4220" border="1" class="rpt_table" rules="all" id="table_body" align="left">
								<?
								if ($db_type == 0) $fab_dec_cond = "group_concat(fabric_description)";
								else if ($db_type == 2) $fab_dec_cond = "listagg(cast(fabric_description as varchar2(4000)),',') within group (order by fabric_description)";
								$fabric_arr = array();
								$fab_sql = sql_select("select job_no, item_number_id, $fab_dec_cond as fabric_description from wo_pre_cost_fabric_cost_dtls where status_active=1 and is_deleted=0 group by job_no, item_number_id");
								foreach ($fab_sql as $row) {
									$fabric_arr[$row[csf('job_no')]][$row[csf('item_number_id')]] = $row[csf('fabric_description')];
								}
								//var_dump($fabric_arr);die;

								$i = 1;
								$order_qnty_pcs_tot = 0;
								$order_qntytot = 0;
								$oreder_value_tot = 0;
								$total_ex_factory_qnty = 0;
								$total_short_access_qnty = 0;
								$total_short_access_value = 0;
								$yarn_req_for_po_total = 0;
								if ($db_type == 0) {
									$lc_number_arr = return_library_array("select a.wo_po_break_down_id, group_concat(b.export_lc_no) as export_lc_no  from com_export_lc_order_info a, com_export_lc b where a.com_export_lc_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.wo_po_break_down_id ", 'wo_po_break_down_id', 'export_lc_no');
									$lc_bank_arr = return_library_array("select a.wo_po_break_down_id, group_concat(b.lien_bank) as lien_bank  from com_export_lc_order_info a, com_export_lc b where a.com_export_lc_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.wo_po_break_down_id ", 'wo_po_break_down_id', 'lien_bank');
								}
								if ($db_type == 2) {
									$lc_number_arr = return_library_array("select a.wo_po_break_down_id, LISTAGG(b.export_lc_no,',') WITHIN GROUP (ORDER BY b.export_lc_no)  export_lc_no  from com_export_lc_order_info a, com_export_lc b where a.com_export_lc_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.wo_po_break_down_id ", 'wo_po_break_down_id', 'export_lc_no');
									$lc_bank_arr = return_library_array("select a.wo_po_break_down_id, LISTAGG(b.lien_bank,',') WITHIN GROUP (ORDER BY b.lien_bank)  lien_bank  from com_export_lc_order_info a, com_export_lc b where a.com_export_lc_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.wo_po_break_down_id ", 'wo_po_break_down_id', 'lien_bank');
								}

								$data_array_group = sql_select("select b.grouping from wo_po_details_master a, wo_po_break_down b LEFT JOIN pro_ex_factory_mst c on b.id = c.po_break_down_id where  a.job_no=b.job_no_mst $company_name_cond $buyer_id_cond and a.team_leader like '$team_leader' and a.dealing_marchant like '$dealing_marchant' $style_owner_cond $date_cond $pocond and a.status_active=1 and b.status_active=1 $search_string_cond $file_cond $ordstatusCond $ref_cond $season_cond group by b.grouping");
								foreach ($data_array_group as $row_group) {
									$gorder_qnty_pcs_tot = 0;
									$gorder_qntytot = 0;
									$goreder_value_tot = 0;
									$gtotal_ex_factory_qnty = 0;
									$gtotal_short_access_qnty = 0;
									$gtotal_short_access_value = 0;
									$gyarn_req_for_po_total = 0;

									if ($db_type == 0) {
										$data_array = sql_select("select a.job_no_prefix_num, a.job_no, YEAR(a.insert_date) as year, a.company_name, a.style_owner, a.buyer_name, a.agent_name, a.style_ref_no, a.job_quantity, a.product_category, a.job_no, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant, a.season, a.gauge, b.id, b.inserted_by, b.is_confirmed, b.po_number, b.file_no, b.grouping, b.po_quantity, b.pub_shipment_date, b.po_received_date, DATEDIFF(b.pub_shipment_date,'$date') date_diff_1, DATEDIFF(b.shipment_date,'$date') date_diff_2, b.unit_price, b.po_total_price, b.details_remarks, b.shiping_status, b.file_no, sum(c.ex_factory_qnty) as ex_factory_qnty, MAX(c.ex_factory_date) as ex_factory_date, DATEDIFF(b.pub_shipment_date, MAX(c.ex_factory_date)) date_diff_3, DATEDIFF(b.shipment_date, MAX(c.ex_factory_date)) date_diff_4,a.yarn_quality from wo_po_details_master a, wo_po_break_down b LEFT JOIN pro_ex_factory_mst c on b.id = c.po_break_down_id and c.status_active=1 and c.is_deleted=0 where  a.job_no=b.job_no_mst $company_name_cond  $buyer_id_cond and a.team_leader like '$team_leader' and b.grouping='" . $row_group[csf('grouping')] . "' and a.dealing_marchant like '$dealing_marchant' $style_owner_cond $date_cond $pocond $year_cond and a.status_active=1 and b.status_active=1 $search_string_cond $file_cond $ordstatusCond $ref_cond $brand_id_cond $season_yr_cond $team_leader_cond $season_cond  group by b.id, b.inserted_by order by b.pub_shipment_date,a.job_no_prefix_num,b.id,a.yarn_quality");
									} else if ($db_type == 2) {
										$date = date('d-m-Y');
										if ($row_group[csf('grouping')] != "") $grouping = "and b.grouping='" . $row_group[csf('grouping')] . "'";
										if ($row_group[csf('grouping')] == "") $grouping = "and b.grouping IS NULL";

										$data_array = sql_select("SELECT a.job_no_prefix_num, a.job_no, to_char(a.insert_date,'YYYY') as year, a.company_name, a.style_owner, a.buyer_name, a.agent_name, a.style_ref_no, a.job_quantity, a.product_category, a.job_no, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant, a.season, a.season_buyer_wise, a.gauge, b.id, b.is_confirmed, b.inserted_by, b.po_number, b.po_quantity, b.shipment_date, b.pub_shipment_date, b.po_received_date, (b.pub_shipment_date - to_date('$date','dd-mm-yyyy')) date_diff_1, (b.shipment_date - to_date('$date','dd-mm-yyyy')) date_diff_2, b.unit_price, b.po_total_price, b.details_remarks, b.shiping_status, b.file_no, b.grouping, sum(c.ex_factory_qnty) as ex_factory_qnty, MAX(c.ex_factory_date) as ex_factory_date, (b.pub_shipment_date - MAX(c.ex_factory_date)) date_diff_3, (b.shipment_date - MAX(c.ex_factory_date)) date_diff_4 ,a.yarn_quality from wo_po_details_master a, wo_po_break_down b LEFT JOIN pro_ex_factory_mst c on b.id = c.po_break_down_id and c.status_active=1 and c.is_deleted=0 where  a.job_no=b.job_no_mst $company_name_cond  $buyer_id_cond and a.team_leader like '$team_leader'  $grouping and a.dealing_marchant like '$dealing_marchant' $style_owner_cond $date_cond $pocond $year_cond and a.status_active=1 and b.status_active=1 $search_string_cond  $file_cond $ordstatusCond $ref_cond $brand_id_cond $season_yr_cond $team_leader_cond $season_cond group by a.job_no_prefix_num, a.job_no, a.insert_date, a.company_name, a.style_owner, a.buyer_name, a.agent_name, a.style_ref_no, a.job_quantity, a.product_category, a.job_no, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant, a.season, a.season_buyer_wise, a.gauge, b.id, b.is_confirmed, b.po_number, b.po_quantity, b.shipment_date, b.pub_shipment_date, b.po_received_date, b.unit_price, b.po_total_price, b.details_remarks, b.shiping_status,b.file_no,b.grouping, b.inserted_by,a.yarn_quality order by b.pub_shipment_date,a.job_no_prefix_num,b.id");
									}
									$lc_amendment_arr = array();
									$last_amendment_arr = sql_select("SELECT amendment_no,export_lc_no,export_lc_id  FROM com_export_lc_amendment where amendment_no<>0 and status_active=1 and is_deleted=0 and is_original=0");
									foreach ($last_amendment_arr as $data) {
										$lc_amendment_arr[trim($data[csf('export_lc_id')])] = $data[csf('amendment_no')];
									}

									foreach ($data_array as $row) {
										//echo $lc_id_arr[$row[csf('id')]];
										if ($i % 2 == 0) $bgcolor = "#E9F3FF";
										else $bgcolor = "#FFFFFF";

										$cons = 0;
										$costing_per_pcs = 0;
										$data_array_yarn_cons = sql_select("select yarn_cons_qnty from  wo_pre_cost_sum_dtls where  job_no='" . $row[csf('job_no')] . "'");
										$data_array_costing_per = sql_select("select costing_per from  wo_pre_cost_mst where  job_no='" . $row[csf('job_no')] . "'");
										list($costing_per) = $data_array_costing_per;
										if ($costing_per[csf('costing_per')] == 1) $costing_per_pcs = 1 * 12;
										else if ($costing_per[csf('costing_per')] == 2) $costing_per_pcs = 1 * 1;
										else if ($costing_per[csf('costing_per')] == 3) $costing_per_pcs = 2 * 12;
										else if ($costing_per[csf('costing_per')] == 4) $costing_per_pcs = 3 * 12;
										else if ($costing_per[csf('costing_per')] == 5) $costing_per_pcs = 4 * 12;

										$yarn_req_for_po = 0;
										foreach ($data_array_yarn_cons as $row_yarn_cons) {
											$cons = $row_yarn_cons[csf('yarn_cons_qnty')];
											$yarn_req_for_po = ($row_yarn_cons[csf('yarn_cons_qnty')] / $costing_per_pcs) * $row[csf('po_quantity')];
										}

										//--Calculation Yarn Required-------
										//--Color Determination-------------
										//==================================
										$shipment_performance = 0;
										if ($row[csf('shiping_status')] == 1 && $row[csf('date_diff_1')] > 10) {
											$color = "";
											$number_of_order['yet'] += 1;
											$shipment_performance = 0;
										}

										if ($row[csf('shiping_status')] == 1 && ($row[csf('date_diff_1')] <= 10 && $row[csf('date_diff_1')] >= 0)) {
											$color = "orange";
											$number_of_order['yet'] += 1;
											$shipment_performance = 0;
										}
										if ($row[csf('shiping_status')] == 1 &&  $row[csf('date_diff_1')] < 0) {
											$color = "red";
											$number_of_order['yet'] += 1;
											$shipment_performance = 0;
										}
										//=====================================
										if ($row[csf('shiping_status')] == 2 && $row[csf('date_diff_1')] > 10) $color = "";
										if ($row[csf('shiping_status')] == 2 && ($row[csf('date_diff_1')] <= 10 && $row[csf('date_diff_1')] >= 0)) $color = "orange";
										if ($row[csf('shiping_status')] == 2 &&  $row[csf('date_diff_1')] < 0) $color = "red";

										if ($row[csf('shiping_status')] == 2 &&  $row[csf('date_diff_2')] >= 0) {
											$number_of_order['ontime'] += 1;
											$shipment_performance = 1;
										}
										if ($row[csf('shiping_status')] == 2 &&  $row[csf('date_diff_2')] < 0) {
											$number_of_order['after'] += 1;
											$shipment_performance = 2;
										}
										//========================================
										if ($row[csf('shiping_status')] == 3 && $row[csf('date_diff_3')] >= 0) $color = "green";
										if ($row[csf('shiping_status')] == 3 &&  $row[csf('date_diff_3')] < 0) $color = "#2A9FFF";
										if ($row[csf('shiping_status')] == 3 && $row[csf('date_diff_4')] >= 0) {
											$number_of_order['ontime'] += 1;
											$shipment_performance = 1;
										}
										if ($row[csf('shiping_status')] == 3 &&  $row[csf('date_diff_4')] < 0) {
											$number_of_order['after'] += 1;
											$shipment_performance = 2;
										}
										$days_to_realized="";
										if($row[csf('ex_factory_date')]!="" && $days_to_realized_arr[$row['BUYER_NAME']]!="")
										{
										$days_to_realized = add_date($row[csf('ex_factory_date')], $days_to_realized_arr[$row['BUYER_NAME']]);
										}
									
								?>
										<tr bgcolor="<? echo $bgcolor; ?>" style="vertical-align:middle" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
											<td width="50" bgcolor="<? echo $color; ?>"> <? echo $i; ?> </td>
											<td width="70" style="word-break:break-all"><? echo $company_short_name_arr[$row[csf('company_name')]]; ?></td>
											<td width="70" style="word-break:break-all"><? echo $company_short_name_arr[$row[csf('style_owner')]]; ?></td>
											<td width="70" style="word-break:break-all"><? echo $row[csf('job_no_prefix_num')]; ?></td>
											<td width="60" style="word-break:break-all"><? echo $row[csf('year')]; ?></td>
											<td width="70" style="word-wrap:break-word;"><? echo $buyer_short_name_arr[$row[csf('buyer_name')]]; ?></td>
											<td width="110" style="word-break:break-all"><? echo $row[csf('po_number')]; ?></td>
											<td width="100" style="word-break:break-all"><? echo $actual_po_no_arr[$row[csf('id')]]; ?></td>
											<td width="100" style="word-break:break-all"><? echo $row[csf('grouping')]; ?></td>
											<td width="100" style="word-break:break-all"><? echo $buyer_wise_season_arr[$row[csf('season_buyer_wise')]]; ?></td>
											<td width="50" style="word-break:break-all"><? echo $buyer_short_name_arr[$row[csf('agent_name')]]; ?></td>
											<td width="70" style="word-break:break-all"><? echo $order_status[$row[csf('is_confirmed')]]; ?></td>
											<td width="70" style="word-break:break-all"><? echo $product_category[$row[csf('product_category')]]; ?></td>
											<td width="40" style="word-break:break-all" onclick="openmypage_image('requires/shipment_schedule_controller.php?action=show_image&job_no=<? echo $row[csf("job_no")] ?>','Image View')"><img src='../../../<? echo $imge_arr[$row[csf('job_no')]]; ?>' height='25' width='30' /></td>
											<td width="90" style="word-break:break-all"><? echo $row[csf('style_ref_no')]; ?></td>
											<td width="80" style="word-break:break-all"><? echo $gauge_arr[$row[csf('gauge')]]; ?></td>
											<td width="150" style="word-break:break-all">
												<? $gmts_item_id = explode(',', $row[csf('gmts_item_id')]);
												$fabric_description = "";
												for ($j = 0; $j <= count($gmts_item_id); $j++) {
													if ($fabric_description == "") $fabric_description = $fabric_arr[$row[csf('job_no')]][$gmts_item_id[$j]];
													else $fabric_description .= ',' . $fabric_arr[$row[csf('job_no')]][$gmts_item_id[$j]];
													echo $garments_item[$gmts_item_id[$j]];
												}
												?></td>
											<td width="200" style="word-break:break-all">
												<?
												$fabric_des = "";
												$fabric_des = implode(",", array_unique(explode(",", $fabric_description)));
												//echo $fabric_des;//$fabric_des;
												echo $row[csf('yarn_quality')];
												?></td>
											<td width="70" style="word-break:break-all"><? echo change_date_format($row[csf('pub_shipment_date')], 'dd-mm-yyyy', '-'); ?></td>
											<td width="70" style="word-break:break-all"><? echo change_date_format($row[csf('po_received_date')], 'dd-mm-yyyy', '-'); ?></td>
											<td width="50" bgcolor="<? echo $color; ?>" style="word-break:break-all">
												<?
												if ($row[csf('shiping_status')] == 1 || $row[csf('shiping_status')] == 2) echo $row[csf('date_diff_1')];
												if ($row[csf('shiping_status')] == 3) echo $row[csf('date_diff_3')];
												?></td>
											<td width="90" align="right" style="word-break:break-all">
												<?
												echo number_format(($row[csf('po_quantity')] * $row[csf('total_set_qnty')]), 0);
												$order_qnty_pcs_tot = $order_qnty_pcs_tot + ($row[csf('po_quantity')] * $row[csf('total_set_qnty')]);
												$gorder_qnty_pcs_tot = $gorder_qnty_pcs_tot + ($row[csf('po_quantity')] * $row[csf('total_set_qnty')]);
												?></td>
											<td width="90" align="right" style="word-break:break-all">
												<?
												echo number_format($row[csf('po_quantity')], 0);
												$order_qntytot = $order_qntytot + $row[csf('po_quantity')];
												$gorder_qntytot = $gorder_qntytot + $row[csf('po_quantity')];
												?></td>
											<td width="40" style="word-break:break-all"><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?></td>
											<td width="50" align="right" style="word-break:break-all"><? echo number_format($row[csf('unit_price')], 2); ?></td>
											<td width="100" align="right" style="word-break:break-all">
												<?
												echo number_format($row[csf('po_total_price')], 2);
												$oreder_value_tot = $oreder_value_tot + $row[csf('po_total_price')];
												$goreder_value_tot = $goreder_value_tot + $row[csf('po_total_price')];
												?></td>
											<td width="100" align="center" style="word-break:break-all">
												<?
												unset($bank_id_arr);
												unset($bank_string_arr);
												if ($lc_bank_arr[$row[csf('id')]] != "") {
													$bank_id_arr = array_unique(explode(",", $lc_bank_arr[$row[csf('id')]]));
													foreach ($bank_id_arr as $bank_id) {
														$bank_string_arr[] = $bank_name_arr[$bank_id];
													}
													echo implode(",", $bank_string_arr);
												}
												$sc_bank = rtrim($sc_bank_arr[$row[csf('id')]], ',');
												if ($sc_bank != "") {
													$bank_id_arr = array_unique(explode(",", $sc_bank));
													foreach ($bank_id_arr as $bank_id) {
														$bank_string_arr[] = $bank_name_arr[$bank_id];
													}
													echo implode(",", $bank_string_arr);
												}
												?>


											<td width="100" align="center" style="word-break:break-all">
												<?
												if ($lc_number_arr[$row[csf('id')]] != "") {
													echo "LC: " . $lc_number_arr[$row[csf('id')]];
													$lc_no = $lc_number_arr[$row[csf('id')]];
												}
												$sc_number = rtrim($sc_number_arr[$row[csf('id')]], ',');
												$sc_numbers = implode(",", array_unique(explode(",", $sc_number)));
												if ($sc_numbers != "") {
													echo " SC: " . $sc_numbers;
												}
												?>
											</td>
											<td width="90" align="center" style="word-break:break-all">
												<? if ($lc_number_arr[$row[csf('id')]] != "") {
													echo $lc_amendment_arr[$lc_id_arr[$row[csf('id')]]];
												}
												?>
											</td>
											<td width="80" align="center" style="word-break:break-all">
												<?
												if ($export_lc_arr[$row[csf('id')]]['file_no'] != '') echo $export_lc_arr[$row[csf('id')]]['file_no'];
												if ($export_sc_arr[$row[csf('id')]]['file_no'] != '') echo $export_sc_arr[$row[csf('id')]]['file_no'];
												?>
											</td>
											<td width="80" align="center" style="word-break:break-all"><?
																										if ($export_lc_arr[$row[csf('id')]]['pay_term'] != "") echo $export_lc_arr[$row[csf('id')]]['pay_term'];
																										if ($export_sc_arr[$row[csf('id')]]['pay_term'] != "") echo $export_sc_arr[$row[csf('id')]]['pay_term'];
																										?></td>
											<td width="80" align="center" style="word-break:break-all"><?
																										if ($export_lc_arr[$row[csf('id')]]['tenor'] != "") echo $export_lc_arr[$row[csf('id')]]['tenor'];
																										if ($export_sc_arr[$row[csf('id')]]['tenor'] != "") echo $export_sc_arr[$row[csf('id')]]['tenor'];
																										?></td>
											<td width="90" align="center" style="word-break:break-all">
												<?
												$ex_factory_del_qty = $ex_factory_qty_arr[$row[csf('id')]]['del_qty'];
												$ex_factory_return_qty = $ex_factory_qty_arr[$row[csf('id')]]['return_qty'];
												$ex_factory_qnty = $ex_factory_del_qty - $ex_factory_return_qty;

												//$ex_factory_qnty=$ex_factory_qty_arr[$row[csf("id")]];
												?>
												<a href="##" onClick="last_ex_factory_popup('ex_factory_popup','<? echo $row[csf('job_no')]; ?>', '<? echo $row[csf('id')]; ?>','750px')"><? echo  number_format($ex_factory_qnty, 0); ?></a>
												<?

												$total_ex_factory_qnty = $total_ex_factory_qnty + $ex_factory_qnty;
												$gtotal_ex_factory_qnty = $gtotal_ex_factory_qnty + $ex_factory_qnty;;
												if ($shipment_performance == 0) {
													$po_qnty['yet'] += ($row[csf('po_quantity')] * $row[csf('total_set_qnty')]);
													$po_value['yet'] += 100;
												} else if ($shipment_performance == 1) {
													$po_qnty['ontime'] += $ex_factory_qnty;
													$po_value['ontime'] += ((100 * $ex_factory_qnty) / ($row[csf('po_quantity')] * $row[csf('total_set_qnty')]));
													$po_qnty['yet'] += (($row[csf('po_quantity')] * $row[csf('total_set_qnty')]) - $ex_factory_qnty);
												} else if ($shipment_performance == 2) {
													$po_qnty['after'] += $ex_factory_qnty;
													$po_value['after'] += ((100 * $ex_factory_qnty) / ($row[csf('po_quantity')] * $row[csf('total_set_qnty')]));
													$po_qnty['yet'] += (($row[csf('po_quantity')] * $row[csf('total_set_qnty')]) - $ex_factory_qnty);
												}
												?>
											</td>
											<td width="70" align="center" style="word-break:break-all"><a href="##" onClick="last_ex_factory_popup('last_ex_factory_Date','<? echo $row[csf('job_no')]; ?>', '<? echo $row[csf('id')]; ?>','750px')"><? echo change_date_format($row[csf('ex_factory_date')]); ?></a></td>
											<td width="70" align="center" style="word-break:break-all"><?=change_date_format($days_to_realized);?></td>
											<td width="90" align="center" style="word-break:break-all">
												<?
												$short_access_qnty = ($row[csf('po_quantity')] - $ex_factory_qnty);
												echo number_format($short_access_qnty, 0);
												$total_short_access_qnty = $total_short_access_qnty + $short_access_qnty;
												$gtotal_short_access_qnty = $gtotal_short_access_qnty + $short_access_qnty;;
												?>
											</td>
											<td width="120" align="center" style="word-break:break-all">
												<?
												$short_access_value = $short_access_qnty * $row[csf('unit_price')];
												echo number_format($short_access_value, 2);
												$total_short_access_value = $total_short_access_value + $short_access_value;
												$gtotal_short_access_value = $gtotal_short_access_value + $short_access_value;
												?>
											</td>
											<td width="100" align="right" title="<? echo "Cons:" . $cons . "Costing per:" . $costing_per[csf('costing_per')]; ?>" style="word-break:break-all">
												<?
												echo number_format($yarn_req_for_po, 2);
												$yarn_req_for_po_total = $yarn_req_for_po_total + $yarn_req_for_po;
												$gyarn_req_for_po_total = $gyarn_req_for_po_total + $yarn_req_for_po;
												?>
											</td>
											<td width="100" align="right" style="word-break:break-all"><? echo number_format(($cm_for_shipment_schedule_arr[$row[csf('job_no')]] / $costing_per_pcs) * $row[csf('po_quantity')], 2); ?></td>
											<td width="100" align="center" style="word-break:break-all"><? echo $shipment_status[$row[csf('shiping_status')]]; ?></td>
											<td width="150" align="center" style="word-break:break-all"><? echo $company_team_member_name_arr[$row[csf('dealing_marchant')]]; ?></td>
											<td width="150" align="center" style="word-break:break-all"><? echo $company_team_name_arr[$row[csf('team_leader')]]; ?></td>
											<td width="100" style="word-break:break-all"><? echo $row[csf('file_no')]; ?></td>
											<td width="40" style="word-break:break-all"><? echo $row[csf('id')]; ?></td>
											<td width="100" style="word-break:break-all"><? echo $user_name_arr[$row[csf('inserted_by')]]; ?></td>
											<td width="" style="word-break:break-all;"><? echo $row[csf('details_remarks')]; ?></td>
										</tr>
									<?
										$i++;
									}
									?>
									<tr bgcolor="#CCCCCC" style="vertical-align:middle" height="25">
										<td align="center">Total: </td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td align="right"><span style="color:#CCCCCC;">'</span><? echo number_format($gorder_qnty_pcs_tot, 0); ?></td>
										<td align="right"><span style="color:#CCCCCC;">'</span><? echo number_format($gorder_qntytot, 0); ?></td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>

										<td align="right"><span style="color:#CCCCCC;">'</span><? echo number_format($goreder_value_tot, 2); ?></td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td align="right"><span style="color:#CCCCCC;">'</span><? echo number_format($gtotal_ex_factory_qnty, 0); ?></td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td align="right"><span style="color:#CCCCCC;">'</span> <? echo number_format($gtotal_short_access_qnty, 0); ?></td>
										<td align="right"><span style="color:#CCCCCC;">'</span> <? echo number_format($gtotal_short_access_value, 0); ?></td>
										<td align="right"><span style="color:#CCCCCC;">'</span><? echo number_format($gyarn_req_for_po_total, 2); ?></td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</th>

									</tr>
								<?
								}
								?>
							</table>
						</div>
						<table width="4200" id="report_table_footer" border="1" class="rpt_table" rules="all" align="left">
							<tfoot>
								<tr>
									<th width="50">&nbsp;</th>
									<th width="70">&nbsp;</th>
									<th width="70">&nbsp;</th>
									<th width="70">&nbsp;</th>
									<th width="60">&nbsp;</th>
									<th width="70">&nbsp;</th>
									<th width="110">&nbsp;</th>
									<th width="100">&nbsp;</th>
									<th width="100">&nbsp;</th>
									<th width="100">&nbsp;</th>
									<th width="50">&nbsp;</th>
									<th width="70">&nbsp;</th>
									<th width="70">&nbsp;</th>
									<th width="40">&nbsp;</th>
									<th width="90">&nbsp;</th>
									<th width="80">&nbsp;</th>
									<th width="150">&nbsp;</th>
									<th width="200">&nbsp;</th>
									<th width="70">&nbsp;</th>
									<th width="70">&nbsp;</th>
									<th width="50">&nbsp;</th>
									<th width="90" id="total_order_qnty_pcs" align="right"><? echo number_format($order_qnty_pcs_tot, 0); ?></th>
									<th width="90" id="total_order_qnty" align="right"><? echo number_format($order_qntytot, 0); ?></th>
									<th width="40">&nbsp;</th>
									<th width="50">&nbsp;</th>

									<th width="100" id="value_total_order_value" align="right"><? echo number_format($oreder_value_tot, 2); ?></th>
									<th width="100">&nbsp;</th>
									<th width="100">&nbsp;</th>
									<th width="90">&nbsp;</th>
									<th width="80">&nbsp;</th>
									<th width="80">&nbsp;</th>
									<th width="80">&nbsp;</th>
									<th width="90" id="total_ex_factory_qnty" align="right"><? echo number_format($total_ex_factory_qnty, 0); ?></th>
									<th width="70">&nbsp;</th>
									<th width="70">&nbsp;</th>
									<th width="90" id="total_short_access_qnty" align="right"><? echo number_format($total_short_access_qnty, 0); ?></th>
									<th width="120" id="value_total_short_access_value" align="right"><? echo number_format($total_short_access_value, 0); ?></th>
									<th width="100" id="value_yarn_req_tot" align="right"><? echo number_format($yarn_req_for_po_total, 2); ?></th>
									<th width="100">&nbsp;</th>
									<th width="100">&nbsp;</th>
									<th width="150">&nbsp;</th>
									<th width="150">&nbsp;</th>
									<th width="100" &nbsp;></th>
									<th width="40">&nbsp;</th>
									<th width="100">&nbsp;</th>
									<th width="">&nbsp;</th>
								</tr>
							</tfoot>
						</table>
					<?
					} else 
					{
					 ?>
						<table width="4000" id="table_header_1" border="1" class="rpt_table" rules="all">
							<thead>
								<tr>
									<th width="50">SL</th>
									<th width="70">Company</th>
									<th width="70">Style Owner</th>
									<th width="70">Job No</th>
									<th width="60">Year</th>
									<th width="50">Buyer</th>
									<th width="110">PO No</th>
									<th width="100">Actual PO No</th>
									<th width="100">Ref No</th>
									<th width="100">Season</th>
									<th width="50">Agent</th>
									<th width="70">Order Status</th>
									<th width="70">Prod. Catg</th>
									<th width="40">Img</th>
									<th width="90">Style Ref</th>
									<th width="70">Gauge</th>
									<th width="150">Item</th>
									<th width="200">Fab. Description</th>
									<th width="70">Ship Date</th>
									<th width="70">PO Rec. Date</th>
									<th width="50">Days in Hand</th>
									<th width="90">Order Qty(Pcs)</th>
									<th width="90">Order Qty</th>
									<th width="40">Uom</th>
									<th width="50">Per Unit Price</th>
									<th width="100">Order Value</th>
									<th width="100">LC/SC No</th>
									<th width="90">Ex. LC Amendment No(Last)</th>

									<th width="80">Int. File No </th>
									<th width="80">Pay Term </th>
									<th width="80">Tenor</th>

									<th width="90">Ex-Fac Qty </th>
									<th width="70">Last Ex-Fac Date</th>
									<th width="70"> Days To Realized</th>
									<th width="90">Short/Access Qty</th>
									<th width="120">Short/Access Value</th>
									<th width="100">Yarn Req</th>
									<th width="100">CM </th>
									<th width="100">Shipping Status</th>
									<th width="150"> Team Member</th>
									<th width="150">Team Name</th>
									<th width="100">File No</th>
									<th width="120">Id</th>
									<th width="100">User Name</th>
									<th>Remarks</th>
								</tr>
							</thead>
						</table>
						<div style="max-height:400px; overflow-y:scroll; width:4020px" align="left" id="scroll_body">
							<table width="4000" border="1" class="rpt_table" rules="all" id="table_body">
								<?
											$yarn_cons_arr = return_library_array("select job_no, yarn_cons_qnty from  wo_pre_cost_sum_dtls where status_active=1 and is_deleted=0", "job_no", "yarn_cons_qnty");
											$costing_per_arr = return_library_array("select job_no, costing_per from  wo_pre_cost_mst where status_active=1 and is_deleted=0", "job_no", "costing_per");

											$ex_fact_sql = sql_select("select a.job_no, MAX(c.ex_factory_date) as ex_factory_date, sum(CASE WHEN c.entry_form!=85 THEN c.ex_factory_qnty ELSE 0 END) as ex_factory_qnty, sum(CASE WHEN c.entry_form=85 THEN c.ex_factory_qnty ELSE 0 END) as ex_factory_return_qnty from wo_po_details_master a, wo_po_break_down b,pro_ex_factory_mst c where  a.job_no=b.job_no_mst and b.id = c.po_break_down_id and c.status_active=1 and c.is_deleted=0 $company_name_cond  $buyer_id_cond and a.team_leader like '$team_leader' and a.dealing_marchant like '$dealing_marchant' $pocond $year_cond $team_leader_cond $brand_id_cond $season_yr_cond and a.status_active=1 and b.status_active=1 $search_string_cond $file_cond $ordstatusCond $ref_cond group by a.job_no");
											$ex_fact_data = array();
											foreach ($ex_fact_sql as $row) {
												$ex_fact_data[$row[csf("job_no")]]["ex_factory_qnty"] = $row[csf("ex_factory_qnty")] - $row[csf("ex_factory_return_qnty")];
												$ex_fact_data[$row[csf("job_no")]]["ex_factory_date"] = $row[csf("ex_factory_date")];
											}

											if ($db_type == 0) {
												$fab_dec_cond = "group_concat(fabric_description)";
											} else if ($db_type == 2) {
												$fab_dec_cond = "listagg(cast(fabric_description as varchar2(4000)),',') within group (order by fabric_description)";
											}
											$fabric_arr = array();
											$fab_sql = sql_select("select job_no, item_number_id, $fab_dec_cond as fabric_description from wo_pre_cost_fabric_cost_dtls where status_active=1 and is_deleted=0 group by job_no, item_number_id");
											foreach ($fab_sql as $row) {
												$fabric_arr[$row[csf('job_no')]][$row[csf('item_number_id')]] = $row[csf('fabric_description')];
											}
											//var_dump($fabric_arr);die;

											$i = 1;
											$order_qnty_pcs_tot = 0;
											$order_qntytot = 0;
											$oreder_value_tot = 0;
											$total_ex_factory_qnty = 0;
											$total_short_access_qnty = 0;
											$total_short_access_value = 0;
											$yarn_req_for_po_total = 0;
											if ($db_type == 0) {
												$lc_number_arr = return_library_array("select a.wo_po_break_down_id, group_concat(b.export_lc_no) as export_lc_no  from com_export_lc_order_info a, com_export_lc b where a.com_export_lc_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.wo_po_break_down_id ", 'wo_po_break_down_id', 'export_lc_no');

												$sc_number_arr = return_library_array("select a.wo_po_break_down_id, group_concat(b.contract_no) as contract_no from com_sales_contract_order_info a, com_sales_contract b where a.com_sales_contract_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.wo_po_break_down_id ", 'wo_po_break_down_id', 'contract_no');
											}
											if ($db_type == 2) {
												$lc_number_arr = return_library_array("select a.wo_po_break_down_id, LISTAGG(b.export_lc_no,',') WITHIN GROUP (ORDER BY b.export_lc_no)  export_lc_no  from com_export_lc_order_info a, com_export_lc b where a.com_export_lc_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.wo_po_break_down_id ", 'wo_po_break_down_id', 'export_lc_no');

												$sc_number_arr = return_library_array("select a.wo_po_break_down_id, LISTAGG(b.contract_no) WITHIN GROUP (ORDER BY b.contract_no) contract_no from com_sales_contract_order_info a, com_sales_contract b where a.com_sales_contract_id=b.id and a.status_active=1 and 	a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.wo_po_break_down_id ", 'wo_po_break_down_id', 'contract_no');
											}
											$data_array_group = sql_select("select b.grouping from wo_po_details_master a, wo_po_break_down b LEFT JOIN pro_ex_factory_mst c on b.id = c.po_break_down_id where  a.job_no=b.job_no_mst $company_name_cond $buyer_id_cond and a.team_leader like '$team_leader' and a.dealing_marchant like '$dealing_marchant' $style_owner_cond $date_cond $pocond and a.status_active=1 and b.status_active=1 $search_string_cond $file_cond $ordstatusCond $ref_cond  group by b.grouping");
											foreach ($data_array_group as $row_group) {
												$gorder_qnty_pcs_tot = 0;
												$gorder_qntytot = 0;
												$goreder_value_tot = 0;
												$gtotal_ex_factory_qnty = 0;
												$gtotal_short_access_qnty = 0;
												$gtotal_short_access_value = 0;
												$gyarn_req_for_po_total = 0;

												if ($db_type == 0) {
													$data_array = sql_select("select a.job_no_prefix_num, a.job_no, YEAR(a.insert_date) as year, a.company_name, a.style_owner, a.buyer_name, a.agent_name, a.style_ref_no, a.gauge, a.job_quantity, a.product_category, a.job_no, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant, a.season, group_concat(b.id) as id, group_concat(b.po_number) as po_number, group_concat(b.is_confirmed) as is_confirmed, sum(b.po_quantity) as po_quantity, max(b.shipment_date) as shipment_date, max(b.pub_shipment_date) as pub_shipment_date, max(b.po_received_date) as po_received_date , max(DATEDIFF(b.pub_shipment_date,'$date')) date_diff_1, max(DATEDIFF(b.shipment_date,'$date')) date_diff_2, sum(b.po_total_price) as po_total_price, max(b.details_remarks) as details_remarks, group_concat(b.shiping_status) as shiping_status, max(b.file_no) as file_no,max(b.inserted_by) as inserted_by
									from wo_po_details_master a, wo_po_break_down b,a.yarn_quality
									where  a.job_no=b.job_no_mst $company_name_cond  $buyer_id_cond and a.team_leader like '$team_leader' and b.grouping='" . $row_group[csf('grouping')] . "' and a.dealing_marchant like '$dealing_marchant' $style_owner_cond $date_cond $pocond $year_cond and a.status_active=1 and b.status_active=1 $search_string_cond  $file_cond $ordstatusCond $ref_cond $brand_id_cond $season_yr_cond $team_leader_cond 
									group by a.job_no_prefix_num, a.job_no, a.insert_date, a.company_name, a.style_owner, a.buyer_name, a.agent_name, a.style_ref_no, a.gauge, a.job_quantity, a.product_category, a.job_no, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader,a.yarn_quality, a.dealing_marchant, a.season
									order by a.style_ref_no");
												}
												if ($db_type == 2) {
													$date = date('d-m-Y');
													if ($row_group[csf('grouping')] != "") $grouping = "and b.grouping='" . $row_group[csf('grouping')] . "'";
													if ($row_group[csf('grouping')] == "") $grouping = "and b.grouping IS NULL";

													$data_array = sql_select("select a.job_no_prefix_num, a.job_no, to_char(a.insert_date,'YYYY') as year, a.company_name, a.style_owner, a.buyer_name, a.agent_name, a.style_ref_no, a.gauge, a.job_quantity, a.product_category, a.job_no, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant, a.season, listagg(cast(b.id as varchar2(4000)),',') within group (order by b.id) as id, listagg(cast(b.po_number as varchar2(4000)),',') within group (order by b.po_number) as po_number, listagg(cast(b.is_confirmed as varchar2(4000)),',') within group (order by b.is_confirmed) as is_confirmed, sum(b.po_quantity) as po_quantity, max(b.shipment_date) as shipment_date, max(b.pub_shipment_date) as pub_shipment_date, max(b.po_received_date) as po_received_date , max(b.pub_shipment_date - to_date('$date','dd-mm-yyyy')) date_diff_1,  max(b.shipment_date - to_date('$date','dd-mm-yyyy')) date_diff_2, sum(b.po_total_price) as po_total_price, max(b.details_remarks) as details_remarks, listagg(cast(b.shiping_status as varchar2(4000)),',') within group (order by b.shiping_status) as shiping_status, max(b.file_no) as file_no,max(b.grouping) as grouping,max(b.inserted_by) as inserted_by,a.yarn_quality
									from wo_po_details_master a, wo_po_break_down b
									where  a.job_no=b.job_no_mst $company_name_cond  $buyer_id_cond and a.team_leader like '$team_leader' $grouping  and a.dealing_marchant like '$dealing_marchant' $style_owner_cond $date_cond $pocond  $brand_id_cond $team_leader_cond $season_yr_cond $year_cond and a.status_active=1 and b.status_active=1 $search_string_cond $file_cond $ordstatusCond $ref_cond
									group by a.job_no_prefix_num, a.job_no, a.insert_date, a.company_name, a.style_owner, a.buyer_name, a.agent_name, a.style_ref_no, a.gauge, a.job_quantity, a.product_category, a.job_no, a.gmts_item_id, a.total_set_qnty, a.order_uom, a.team_leader, a.dealing_marchant, a.season,a.yarn_quality
									order by a.style_ref_no");
												}

												foreach ($data_array as $row) {
													if ($i % 2 == 0) $bgcolor = "#E9F3FF";
													else $bgcolor = "#FFFFFF";

													$ex_factory_qnty = $ex_fact_data[$row[csf('job_no')]]["ex_factory_qnty"];
													$ex_factory_date = $ex_fact_data[$row[csf('job_no')]]["ex_factory_date"];

													// $days_to_realized = add_date($ex_fact_data[$row[csf('job_no')]]["ex_factory_date"], $days_to_realized_arr[$row['BUYER_NAME']]);

													$days_to_realized="";
													if($ex_factory_date!="" && $days_to_realized_arr[$row['BUYER_NAME']]!="")
													{
													$days_to_realized = add_date($ex_factory_date , $days_to_realized_arr[$row['BUYER_NAME']]);
													}
												


													$date_diff_3 = datediff("d", $ex_factory_date, $row[csf('pub_shipment_date')]);
													$date_diff_4 = datediff("d", $ex_factory_date, $row[csf('shipment_date')]);

													$cons = 0;
													$costing_per_pcs = 0;
													$data_array_yarn_cons = $yarn_cons_arr[$row[csf('job_no')]];
													$data_array_costing_per = $costing_per_arr[$row[csf('job_no')]];
													if ($data_array_costing_per == 1) $costing_per_pcs = 1 * 12;
													else if ($data_array_costing_per == 2) $costing_per_pcs = 1 * 1;
													else if ($data_array_costing_per == 3) $costing_per_pcs = 2 * 12;
													else if ($data_array_costing_per == 4) $costing_per_pcs = 3 * 12;
													else if ($data_array_costing_per == 5) $costing_per_pcs = 4 * 12;

													$yarn_req_for_po = ($data_array_yarn_cons / $costing_per_pcs) * $row[csf('po_quantity')];



													//--Calculation Yarn Required-------
													//--Color Determination-------------
													//==================================
													$shiping_status_arr = explode(",", $row[csf('shiping_status')]);
													$shiping_status_arr = array_unique($shiping_status_arr);
													if (count($shiping_status_arr) > 1) $shiping_status = 2;
													else $shiping_status = $shiping_status_arr[0];


													$shipment_performance = 0;
													if ($shiping_status == 1 && $row[csf('date_diff_1')] > 10) {
														$color = "";
														$number_of_order['yet'] += 1;
														$shipment_performance = 0;
													}

													if ($shiping_status && ($row[csf('date_diff_1')] <= 10 && $row[csf('date_diff_1')] >= 0)) {
														$color = "orange";
														$number_of_order['yet'] += 1;
														$shipment_performance = 0;
													}
													if ($shiping_status == 1 &&  $row[csf('date_diff_1')] < 0) {
														$color = "red";
														$number_of_order['yet'] += 1;
														$shipment_performance = 0;
													}
													//=====================================
													if ($shiping_status == 2 && $row[csf('date_diff_1')] > 10) $color = "";
													if ($shiping_status == 2 && ($row[csf('date_diff_1')] <= 10 && $row[csf('date_diff_1')] >= 0)) $color = "orange";
													if ($shiping_status == 2 &&  $row[csf('date_diff_1')] < 0) $color = "red";
													if ($shiping_status == 2 &&  $row[csf('date_diff_2')] >= 0) {
														$number_of_order['ontime'] += 1;
														$shipment_performance = 1;
													}
													if ($shiping_status == 2 &&  $row[csf('date_diff_2')] < 0) {
														$number_of_order['after'] += 1;
														$shipment_performance = 2;
													}
													//========================================
													if ($shiping_status == 3 && $date_diff_3 >= 0) $color = "green";
													if ($shiping_status == 3 &&  $date_diff_3 < 0) $color = "#2A9FFF";

													if ($shiping_status == 3 && $date_diff_4 >= 0) {
														$number_of_order['ontime'] += 1;
														$shipment_performance = 1;
													}
													if ($shiping_status == 3 &&  $date_diff_4 < 0) {
														$number_of_order['after'] += 1;
														$shipment_performance = 2;
													}
													$actual_po = "";
													$ex_po_id = explode(",", $row[csf('id')]);
													foreach ($ex_po_id as $poId) {
														if ($actual_po == "") $actual_po = $actual_po_no_arr[$row[csf('id')]];
														else $actual_po .= ',' . $actual_po_no_arr[$row[csf('id')]];
													}
												
											?>
										<tr bgcolor="<? echo $bgcolor; ?>" style="vertical-align:middle" height="25" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
											<td width="50" bgcolor="<? echo $color; ?>"> <? echo $i; ?> </td>
											<td width="70" style="word-break:break-all"><? echo $company_short_name_arr[$row[csf('company_name')]]; ?></td>
											<td width="70" style="word-break:break-all"><? echo $company_short_name_arr[$row[csf('style_owner')]]; ?></td>
											<td width="70">
												<p><? echo $row[csf('job_no_prefix_num')]; ?></p>
											</td>
											<td width="60">
												<p><? echo $row[csf('year')]; ?></p>
											</td>
											<td width="50" style="word-break:break-all"><? echo $buyer_short_name_arr[$row[csf('buyer_name')]]; ?></td>
											<td width="110" style="word-break:break-all"><? echo implode(",", array_unique(explode(",", $row[csf('po_number')]))); ?></td>
											<td width="100" style="word-break:break-all"><? echo implode(",", array_unique(explode(",", $actual_po))); ?></td>
											<td width="100" style="word-break:break-all"><? echo $row[csf('grouping')]; ?></td>
											<td width="100" style="word-break:break-all"><? echo $row[csf('season')]; ?></td>
											<td width="50" style="word-break:break-all"><? echo $buyer_short_name_arr[$row[csf('agent_name')]]; ?></td>
											<td width="70" style="word-break:break-all"><a href="##" onClick="order_status('order_status_popup', '<? echo $row[csf('id')]; ?>','750px')">View</a></td>
											<td width="70" style="word-break:break-all"><? echo $product_category[$row[csf('product_category')]]; ?></td>
											<td width="40" onclick="openmypage_image('requires/shipment_schedule_controller.php?action=show_image&job_no=<? echo $row[csf("job_no")] ?>','Image View')"><img src='../../../<? echo $imge_arr[$row[csf('job_no')]]; ?>' height='25' width='30' /></td>
											<td width="90" style="word-break:break-all"><? echo $row[csf('style_ref_no')]; ?></td>
											<td width="70" style="word-break:break-all"><?= $gauge_arr[$row[csf('gauge')]]; ?></td>
											<td width="150" style="word-break:break-all">
												<? $gmts_item_id = explode(',', $row[csf('gmts_item_id')]);
												$fabric_description = "";
												for ($j = 0; $j <= count($gmts_item_id); $j++) {
													if ($fabric_description == "") $fabric_description = $fabric_arr[$row[csf('job_no')]][$gmts_item_id[$j]];
													else $fabric_description .= ',' . $fabric_arr[$row[csf('job_no')]][$gmts_item_id[$j]];
													echo $garments_item[$gmts_item_id[$j]];
												}
												?></td>
											<td width="200" style="word-break:break-all">
												<?
												$fabric_des = "";
												$fabric_des = implode(",", array_unique(explode(",", $fabric_description)));

												echo $row[csf('yarn_quality')];
												// echo $fabric_des;

												//$fabric_des;
												?></td>
											<td width="70" align="center" style="word-break:break-all"><? if ($row[csf('pub_shipment_date')] != "" && $row[csf('pub_shipment_date')] != "0000-00-00") echo change_date_format($row[csf('pub_shipment_date')]); ?>&nbsp;</td>
											<td width="70" align="center" style="word-break:break-all"><? if ($row[csf('po_received_date')] != "" && $row[csf('po_received_date')] != "0000-00-00") echo change_date_format($row[csf('po_received_date')]); ?>&nbsp;</td>
											<td width="50" bgcolor="<? echo $color; ?>" align="center" style="word-break:break-all">
												<?
												if ($shiping_status == 1 || $shiping_status == 2) echo $row[csf('date_diff_1')];
												if ($shiping_status == 3) echo $date_diff_3;
												?></td>
											<td width="90" align="right" style="word-break:break-all">
												<?
												echo number_format(($row[csf('po_quantity')] * $row[csf('total_set_qnty')]), 0);
												$order_qnty_pcs_tot = $order_qnty_pcs_tot + ($row[csf('po_quantity')] * $row[csf('total_set_qnty')]);
												$gorder_qnty_pcs_tot = $gorder_qnty_pcs_tot + ($row[csf('po_quantity')] * $row[csf('total_set_qnty')]);
												?></td>
											<td width="90" align="right" style="word-break:break-all">
												<?
												echo number_format($row[csf('po_quantity')], 0);
												$order_qntytot = $order_qntytot + $row[csf('po_quantity')];
												$gorder_qntytot = $gorder_qntytot + $row[csf('po_quantity')];
												?></td>
											<td width="40" style="word-break:break-all"><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?></td>
											<td width="50" align="right" style="word-break:break-all"><? $unit_price = $row[csf('po_total_price')] / $row[csf('po_quantity')];
																										echo number_format($unit_price, 2); ?></td>
											<td width="100" align="right" style="word-break:break-all">
												<?
												echo number_format($row[csf('po_total_price')], 2);
												$oreder_value_tot = $oreder_value_tot + $row[csf('po_total_price')];
												$goreder_value_tot = $goreder_value_tot + $row[csf('po_total_price')];
												?></td>
											<td width="100" align="center" style="word-break:break-all">
												<?
												if ($lc_number_arr[$row[csf('id')]] != "") echo "LC: " . $lc_number_arr[$row[csf('id')]];
												if ($sc_number_arr[$row[csf('id')]] != "") echo " SC: " . $sc_number_arr[$row[csf('id')]];
												?>
											</td>
											<td width="90" align="center" style="word-break:break-all">
												<? if ($lc_number_arr[$row[csf('id')]] != "") echo $lc_amendment_arr[$lc_id_arr[$row[csf('id')]]]; ?>
											</td>
											<td width="80" align="center" style="word-break:break-all"><? echo $export_lc_arr[$row[csf('id')]]['file_no']; ?></td>
											<td width="80" align="center" style="word-break:break-all"><? echo $export_lc_arr[$row[csf('id')]]['pay_term']; ?></td>
											<td width="80" align="center" style="word-break:break-all"><? echo $export_lc_arr[$row[csf('id')]]['tenor']; ?></td>
											<td width="90" align="right" style="word-break:break-all"><a href="##" onClick="last_ex_factory_popup('ex_factory_popup','<? echo $row[csf('job_no')]; ?>', '<? echo $row[csf('id')]; ?>','750px')"><? echo  number_format($ex_factory_qnty, 0); ?></a>
												<?
												//echo  number_format( $ex_factory_qnty,0);
												$total_ex_factory_qnty = $total_ex_factory_qnty + $ex_factory_qnty;
												$gtotal_ex_factory_qnty = $gtotal_ex_factory_qnty + $ex_factory_qnty;;
												if ($shipment_performance == 0) {
													$po_qnty['yet'] += ($row[csf('po_quantity')] * $row[csf('total_set_qnty')]);
													$po_value['yet'] += 100;
												} else if ($shipment_performance == 1) {
													$po_qnty['ontime'] += $ex_factory_qnty;
													$po_value['ontime'] += ((100 * $ex_factory_qnty) / ($row[csf('po_quantity')] * $row[csf('total_set_qnty')]));
													$po_qnty['yet'] += (($row[csf('po_quantity')] * $row[csf('total_set_qnty')]) - $ex_factory_qnty);
												} else if ($shipment_performance == 2) {
													$po_qnty['after'] += $ex_factory_qnty;
													$po_value['after'] += ((100 * $ex_factory_qnty) / ($row[csf('po_quantity')] * $row[csf('total_set_qnty')]));
													$po_qnty['yet'] += (($row[csf('po_quantity')] * $row[csf('total_set_qnty')]) - $ex_factory_qnty);
												}
												?></td>
											<td width="70" align="center" style="word-break:break-all"><a href="##" onClick="last_ex_factory_popup('last_ex_factory_Date','<? echo $row[csf('job_no')]; ?>', '<? echo $row[csf('id')]; ?>','750px')"><? if ($ex_factory_date != "" && $ex_factory_date != "0000-00-00") echo change_date_format($ex_factory_date); ?>&nbsp;</a></td>
											<td width="70" align="center" style="word-break:break-all"><?=change_date_format($days_to_realized);?></td>
											<td width="90" align="right" style="word-break:break-all">
												<?
												$short_access_qnty = ($row[csf('po_quantity')] - $ex_factory_qnty);
												echo number_format($short_access_qnty, 0);
												$total_short_access_qnty = $total_short_access_qnty + $short_access_qnty;
												$gtotal_short_access_qnty = $gtotal_short_access_qnty + $short_access_qnty;;
												?>
											</td>
											<td width="120" align="right" style="word-break:break-all">
												<?
												$short_access_value = $short_access_qnty * $unit_price;
												echo number_format($short_access_value, 2);
												$total_short_access_value = $total_short_access_value + $short_access_value;
												$gtotal_short_access_value = $gtotal_short_access_value + $short_access_value;
												?>
											</td>
											<td width="100" align="right" title="<? echo "Cons:" . $data_array_yarn_cons . "Costing per:" . $data_array_costing_per; ?>" style="word-break:break-all">
												<?
												echo number_format($yarn_req_for_po, 2);
												$yarn_req_for_po_total = $yarn_req_for_po_total + $yarn_req_for_po;
												$gyarn_req_for_po_total = $gyarn_req_for_po_total + $yarn_req_for_po;
												?>
											</td>
											<td width="100" align="right" style="word-break:break-all"><? echo number_format(($cm_for_shipment_schedule_arr[$row[csf('job_no')]] / $costing_per_pcs) * $row[csf('po_quantity')], 2); ?></td>
											<td width="100" align="center" style="word-break:break-all"><? echo $shipment_status[$shiping_status]; ?></td>
											<td width="150" align="center" style="word-break:break-all"><? echo $company_team_member_name_arr[$row[csf('dealing_marchant')]]; ?></td>
											<td width="150" align="center" style="word-break:break-all"><? echo $company_team_name_arr[$row[csf('team_leader')]]; ?></td>
											<td width="100" style="word-break:break-all"><? echo $row[csf('file_no')]; ?></td>
											<td width="120" style="word-break:break-all"><? echo implode(",", array_unique(explode(",", $row[csf('id')]))); ?></td>
											<td width="100" style="word-break:break-all"><? echo $user_name_arr[$row[csf('inserted_by')]]; ?></td>
											<td style="word-break:break-all"><? echo $row[csf('details_remarks')]; ?></td>
										</tr>
									<?
										$i++;
									}
									?>
									<tr bgcolor="#CCCCCC" style="vertical-align:middle" height="25">
										<td width="50" align="center">Total:</td>
										<td width="70">&nbsp;</td>
										<td width="70">&nbsp;</td>
										<td width="70">&nbsp;</td>
										<td width="60">&nbsp;</td>
										<td width="50">&nbsp;</td>
										<td width="110">&nbsp;</td>
										<td width="100">&nbsp;</td>
										<td width="100">&nbsp;</td>
										<td width="100">&nbsp;</td>
										<td width="50">&nbsp;</td>
										<td width="70">&nbsp;</td>
										<td width="70">&nbsp;</td>
										<td width="40">&nbsp;</td>
										<td width="90">&nbsp;</td>
										<td width="70">&nbsp;</td>
										<td width="150">&nbsp;</td>
										<td width="200">&nbsp;</td>
										<td width="70">&nbsp;</td>
										<td width="70">&nbsp;</td>
										<td width="50">&nbsp;</td>
										<td width="90" align="right"><span style="color:#CCCCCC;">'</span><? echo number_format($gorder_qnty_pcs_tot, 0); ?></td>
										<td width="90" align="right"><span style="color:#CCCCCC;">'</span><? echo number_format($gorder_qntytot, 0); ?></td>
										<td width="40">&nbsp;</td>
										<td width="50">&nbsp;</td>
										<td width="100" align="right"><span style="color:#CCCCCC;">'</span><? echo number_format($goreder_value_tot, 2); ?></td>
										<td width="100">&nbsp;</td>
										<td width="90">&nbsp;</td>

										<td width="80">&nbsp;</td>
										<td width="80">&nbsp;</td>
										<td width="80">&nbsp;</td>

										<td width="90" align="right"><span style="color:#CCCCCC;">'</span><? echo number_format($gtotal_ex_factory_qnty, 0); ?></td>
										<td width="70">&nbsp;</td>
										<td width="70">&nbsp;</td>
										<td width="90" align="right"><span style="color:#CCCCCC;">'</span> <? echo number_format($gtotal_short_access_qnty, 0); ?></td>
										<td width="120" align="right"><span style="color:#CCCCCC;">'</span> <? echo number_format($gtotal_short_access_value, 0); ?></td>
										<td width="100" align="right"><span style="color:#CCCCCC;">'</span><? echo number_format($gyarn_req_for_po_total, 2); ?></td>
										<td width="100">&nbsp;</td>
										<td width="100">&nbsp;</td>
										<td width="150">&nbsp;</td>
										<td width="150">&nbsp;</td>
										<td width="100">&nbsp;</td>
										<td width="120">&nbsp;</td>
										<td width="100">&nbsp;</td>
										<td>&nbsp;</th>
									</tr>
								<?
								}
								?>
							</table>
						</div>
						<table width="4000" id="report_table_footer" border="1" class="rpt_table" rules="all">
							<tfoot>
								<tr>
									<th width="50">&nbsp;</th>
									<th width="70">&nbsp;</th>
									<th width="70">&nbsp;</th>
									<th width="70">&nbsp;</th>
									<th width="60">&nbsp;</th>
									<th width="50">&nbsp;</th>
									<th width="110">&nbsp;</th>
									<th width="100">&nbsp;</th>
									<th width="100">&nbsp;</th>
									<th width="100">&nbsp;</th>
									<th width="50">&nbsp;</th>
									<th width="70">&nbsp;</th>
									<th width="70">&nbsp;</th>
									<th width="40">&nbsp;</th>
									<th width="90">&nbsp;</th>
									<th width="70">&nbsp;</th>
									<th width="150">&nbsp;</th>
									<th width="200">&nbsp;</th>
									<th width="70">&nbsp;</th>
									<th width="70">&nbsp;</th>
									<th width="50">&nbsp;</th>
									<th width="90" id="total_order_qnty_pcs" align="right"><? echo number_format($order_qnty_pcs_tot, 0); ?></th>
									<th width="90" id="total_order_qnty" align="right"><? echo number_format($order_qntytot, 0); ?></th>
									<th width="40">&nbsp;</th>
									<th width="50">&nbsp;</th>
									<th width="100" id="value_total_order_value" align="right"><? echo number_format($oreder_value_tot, 2); ?></th>
									<th width="100">&nbsp;</th>
									<th width="90">&nbsp;</th>
									<th width="80">&nbsp;</th>
									<th width="80">&nbsp;</th>
									<th width="80">&nbsp;</th>

									<th width="90" id="total_ex_factory_qnty" align="right"><? echo number_format($total_ex_factory_qnty, 0); ?></th>
									<th width="70">&nbsp;</th>
									<th width="70">&nbsp;</th>
									<th width="90" id="total_short_access_qnty" align="right"><? echo number_format($total_short_access_qnty, 0); ?></th>
									<th width="120" id="value_total_short_access_value" align="right"><? echo number_format($total_short_access_value, 0); ?></th>
									<th width="100" id="value_yarn_req_tot" align="right"><? echo number_format($yarn_req_for_po_total, 2); ?></th>
									<th width="100">&nbsp;</th>
									<th width="100">&nbsp;</th>
									<th width="150">&nbsp;</th>
									<th width="150">&nbsp;</th>
									<th width="100">&nbsp;</th>
									<th width="120">&nbsp;</th>
									<th width="100">&nbsp;</th>
									<th>&nbsp;</th>
								</tr>
							</tfoot>
						</table>
					<?
					}
					?>
					<div id="shipment_performance" style="visibility:hidden">
						<fieldset>
							<table width="600" border="1" cellpadding="0" cellspacing="1" class="rpt_table" rules="all">
								<thead>
									<tr>
										<th colspan="4">
											<font size="4">Shipment Performance</font>
										</th>
									</tr>
									<tr>
										<th>Particulars</th>
										<th>No of PO</th>
										<th>PO Qnty</th>
										<th> %</th>
									</tr>
								</thead>
								<tr bgcolor="#E9F3FF">
									<td>On Time Shipment</td>
									<td><? echo $number_of_order['ontime']; ?></td>
									<td align="right"><? echo number_format($po_qnty['ontime'], 0); ?></td>
									<td align="right"><? echo number_format(((100 * $po_qnty['ontime']) / $order_qnty_pcs_tot), 2); ?></td>
								</tr>
								<tr bgcolor="#FFFFFF">
									<td> Delivery After Shipment Date</td>
									<td><? echo $number_of_order['after']; ?></td>
									<td align="right"><? echo number_format($po_qnty['after'], 0); ?></td>
									<td align="right"><? echo number_format(((100 * $po_qnty['after']) / $order_qnty_pcs_tot), 2); ?></td>
								</tr>
								<tr bgcolor="#E9F3FF">
									<td>Yet To Shipment </td>
									<td><? echo $number_of_order['yet']; ?></td>
									<td align="right"><? echo number_format($po_qnty['yet'], 0); ?></td>
									<td align="right"><? echo number_format(((100 * $po_qnty['yet']) / $order_qnty_pcs_tot), 2); ?></td>
								</tr>

								<tr bgcolor="#E9F3FF">
									<td> </td>
									<td></td>
									<td align="right"><? echo number_format($po_qnty['yet'] + $po_qnty['ontime'] + $po_qnty['after'], 0); ?></td>
									<td align="right"><? echo number_format(((100 * $po_qnty['yet']) / $order_qnty_pcs_tot) + ((100 * $po_qnty['after']) / $order_qnty_pcs_tot) + ((100 * $po_qnty['ontime']) / $order_qnty_pcs_tot), 2); ?></td>
								</tr>
							</table>
						</fieldset>
					</div>
				</div>
			</div>
		</div>
	<?
	} else if ($rpt_type == 2) {
		ob_start();
		$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
		$Dealing_marcent_arr = return_library_array("select id, team_member_name from lib_mkt_team_member_info", 'id', 'team_member_name');
		$company_name_arr = return_library_array("select id,company_name from lib_company", 'id', 'company_name');
		$company_short_name_arr = return_library_array("select id,company_short_name from lib_company", 'id', 'company_short_name');
	?>
		<div style="width:1600px">
			<table width="1600" cellpadding="0" cellspacing="0" id="caption" align="left">
				<tr>
					<td align="center" width="100%" class="form_caption" colspan="14"><strong style="font-size:18px"><? echo $company_name_arr[$company_name]; ?></strong></td>
				</tr>
				<tr>
					<td align="center" width="100%" class="form_caption" colspan="14"><strong style="font-size:18px"><? echo $report_title; ?></strong></td>
				</tr>
				<tr>
					<td align="center" width="100%" class="form_caption" colspan="14"><strong style="font-size:14px">From <? echo change_date_format($start_date); ?> To <? echo change_date_format($end_date); ?> </strong></td>
				</tr>
			</table>
			<br />
			<table width="1580" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header" align="left">
				<thead>
					<tr>
						<th width="40">SL</th>
						<th width="100">Company</th>
						<th width="70">Style Owner</th>
						<th width="100">Buyer</th>
						<th width="110">Style No</th>
						<th width="110">PO No</th>
						<th width="100">Dealing Merchant</th>
						<th width="130">Item Description</th>
						<th width="70">GSM</th>
						<th width="250">Fabrication</th>
						<th width="100">Order Qnty(Pcs)</th>
						<th width="70"><? if ($category_by == 1) echo "Ship Date";
										elseif ($category_by == 2) echo "PO Receive Date"; ?></th>
						<th width="50">Unit Price</th>
						<th width="100">FOB Price</th>
						<th width="100">Remarks</th>
						<th>User Name</th>
					</tr>
				</thead>
			</table>
			<div style="width:1600px; overflow-y:scroll; max-height:300px;font-size:12px; overflow-x:hidden;" id="scroll_body" align="left">
				<table width="1580" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left">
					<tbody>
						<?
						if ($category_by == 1) {
							if ($start_date != "" && $end_date != "") $date_cond = " and b.shipment_date between '$start_date' and '$end_date'";
							else $date_cond = "";
						} else {
							if ($start_date != "" && $end_date != "") $date_cond = " and b.po_received_date between '$start_date' and '$end_date'";
							else $date_cond = "";
						}

						//$fabrication_sql=sql_select("select listagg(cast(a.fabric_description as varchar2(4000)),',') within group (order by a.fabric_description) as fabric_description, b.po_break_down_id from wo_pre_cost_fabric_cost_dtls a,  wo_pre_cos_fab_co_avg_con_dtls b,  where a.job_no=b.job_no and  a.status_active=1 and a.is_deleted=0 and b.cons>0 and c.company_name=$company_name group by b.po_break_down_id");

						$fabrication_sql = sql_select("select a.fabric_description, b.po_break_down_id,a.gsm_weight from wo_pre_cost_fabric_cost_dtls a,
				wo_pre_cos_fab_co_avg_con_dtls b where a.id=b.pre_cost_fabric_cost_dtls_id  and  a.status_active=1 and a.is_deleted=0 and b.cons>0");
						$fabrication_data_arr = array();
						foreach ($fabrication_sql as $row) {
							$fabrication_data_arr[$row[csf("po_break_down_id")]] .= $row[csf("fabric_description")] . ",";
							$gsm_data_arr[$row[csf("po_break_down_id")]] .= $row[csf("gsm_weight")] . ",";
						}
						//$file_cond  $ref_cond $rpt_type
						$main_sql = "select a.company_name, a.style_owner, a.id as job_id, a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no, a.gmts_item_id, a.remarks,
				b.id as po_id, b.po_number,b.inserted_by, b.po_received_date, b.shipment_date, (a.total_set_qnty*b.po_quantity) as po_quantity_pcs,
				(b.unit_price/a.total_set_qnty) as unit_price_pcs,a.dealing_marchant
				from  wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and
				b.status_active=1 and b.is_deleted=0 $company_name_cond $style_owner_cond $date_cond $search_string_cond $brand_id_cond $season_yr_cond   $year_cond $buyer_id_cond $season_cond $file_cond $ref_cond $team_leader_cond
				and a.team_leader like '$team_leader' and a.dealing_marchant like '$dealing_marchant' order by a.buyer_name, a.id, b.id";
						//echo $main_sql; die;
						$main_result = sql_select($main_sql);
						$k = 1;
						$m = 1;
						$temp_arr_buyer = array();
						foreach ($main_result as $row) {
							$po_total_price = 0;
							if (!in_array($row[csf("buyer_name")], $temp_arr_buyer)) {
								$temp_arr_buyer[] = $row[csf("buyer_name")];
								if ($k != 1) {
						?>
									<tr bgcolor="#CCCCCC">
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td align="right">Sub Total:&nbsp;</td>
										<td align="right"><? echo number_format($buyer_tot_qnty, 2); ?></td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td align="right"><? echo number_format($buyer_tot_price, 2); ?></td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
									</tr>
							<?
									unset($buyer_tot_qnty);
									unset($buyer_tot_price);
								}
								$k++;
							}
							if ($m % 2 == 0) $bgcolor = "#E9F3FF";
							else $bgcolor = "#FFFFFF";
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $m; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $m; ?>">
								<td width="40" align="center"><? echo $m; ?>&nbsp;</td>
								<td width="100" style="word-break:break-all"><? echo $company_short_name_arr[$row[csf("company_name")]]; ?>&nbsp;</td>
								<td width="70" style="word-break:break-all"><? echo $company_short_name_arr[$row[csf("style_owner")]]; ?>&nbsp;</td>
								<td width="100" style="word-break:break-all"><? echo $buyer_arr[$row[csf("buyer_name")]]; ?>&nbsp;</td>
								<td width="110" style="word-break:break-all"><? echo $row[csf("style_ref_no")]; ?>&nbsp;</td>
								<td width="110" style="word-break:break-all"><? echo $row[csf("po_number")]; ?>&nbsp;</td>
								<td width="100" style="word-break:break-all"><? echo $Dealing_marcent_arr[$row[csf("dealing_marchant")]]; ?>&nbsp;</td>
								<td width="130" style="word-break:break-all">
									<?
									$garments_item_arr = explode(",", $row[csf("gmts_item_id")]);
									$all_garments_item = "";
									foreach ($garments_item_arr as $garments_item_id) {
										$all_garments_item .= $garments_item[$garments_item_id] . " , ";
									}
									$all_garments_item = chop($all_garments_item, " , ");
									echo $all_garments_item;
									?>&nbsp;</td>
								<td width="70" style="word-break:break-all">
									<?
									$gsm_data = implode(", ", array_unique(explode(",", chop($gsm_data_arr[$row[csf("po_id")]], " , "))));
									echo $gsm_data;

									//$gsm_data_arr[$row[csf("po_break_down_id")]];echo $row[csf("po_number")]; 
									?>&nbsp;</td>
								<td width="250" style="word-break:break-all"><? $fabrication_data = implode(", ", array_unique(explode(",", chop($fabrication_data_arr[$row[csf("po_id")]], " , "))));
																				echo $fabrication_data; ?>&nbsp;</td>
								<td width="100" align="right"><? echo number_format($row[csf("po_quantity_pcs")], 2);
																$buyer_tot_qnty += $row[csf("po_quantity_pcs")];
																$total_po_qnty += $row[csf("po_quantity_pcs")]; ?></td>
								<td width="70" align="center" style="word-break:break-all">
									<?
									if ($category_by == 1) {
										if ($row[csf("shipment_date")] != "" && $row[csf("shipment_date")] != "0000-00-00") echo change_date_format($row[csf("shipment_date")]);
									} else if ($category_by == 2) {
										if ($row[csf("po_received_date")] != "" && $row[csf("po_received_date")] != "0000-00-00") echo change_date_format($row[csf("po_received_date")]);
									}
									?>
									&nbsp;</td>
								<td width="50" align="right"><? echo number_format($row[csf("unit_price_pcs")], 2) ?></td>
								<td width="100" align="right"><? $po_total_price = $row[csf("po_quantity_pcs")] * $row[csf("unit_price_pcs")];
																echo number_format($po_total_price, 2);
																$buyer_tot_price += $po_total_price;
																$total_po_price += $po_total_price; ?></td>
								<td width="100" style="word-break:break-all"><? echo $row[csf("remarks")]; ?></td>
								<td style="word-break:break-all"><? echo $user_name_arr[$row[csf('inserted_by')]]; ?></td>
							</tr>
						<?
							$m++;
						}
						?>
						<tr bgcolor="#DDDDDD">
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td align="right">Sub Total:&nbsp;</td>
							<td align="right"><? echo number_format($buyer_tot_qnty, 2); ?></td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td align="right"><? echo number_format($buyer_tot_price, 2); ?></td>
							<td width="100">&nbsp;</td>
							<td>&nbsp;</td>
						</tr>
					</tbody>
				</table>
			</div>
			<table width="1580" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left">
				<tr bgcolor="#CCCCCC">
					<td width="40">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="70">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="110">&nbsp;</td>
					<td width="110">&nbsp;</td>
					<td width="100">&nbsp;</td>
					<td width="130">&nbsp;</td>
					<td width="70">&nbsp;</td>
					<td width="250" align="right">Grand Total:&nbsp;</td>
					<td width="100" align="right"><? echo number_format($total_po_qnty, 2); ?></td>
					<td width="70">&nbsp;</td>
					<td width="50">&nbsp;</td>
					<td width="100" align="right"><? echo number_format($total_po_price, 2); ?></td>
					<td width="100">&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
			</table>
		</div>
	<?
	} else if ($rpt_type == 3) {
		ob_start();
		$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
		$season_arr = return_library_array("select id, season_name from lib_buyer_season", 'id', 'season_name');
		$company_name_arr = return_library_array("select id,company_name from lib_company", 'id', 'company_name');
		$color_arr = return_library_array("select id, color_name from lib_color", "id", "color_name");
		$size_arr = return_library_array("select id, size_name from lib_size", "id", "size_name");
		$country_arr = return_library_array("select id,country_name from lib_country", "id", "country_name");
		$imge_arr = return_library_array("select master_tble_id, image_location from  common_photo_library where file_type=1", 'master_tble_id', 'image_location');
	?>
		<div style="width:1500px">
			<table width="1500" cellpadding="0" cellspacing="0" id="caption" align="left">
				<tr>
					<td align="center" width="100%" class="form_caption" colspan="17"><strong style="font-size:18px"><? echo $company_name_arr[$company_name]; ?></strong></td>
				</tr>
				<tr>
					<td align="center" width="100%" class="form_caption" colspan="17"><strong style="font-size:16px"><? echo $report_title . ' [Size Wise]'; ?></strong></td>
				</tr>
				<? if ($buyer_name != 0) { ?>
					<tr>
						<td align="center" width="100%" class="form_caption" colspan="17"><strong style="font-size:14px"><? echo 'BUYER NAME: ' . $buyer_arr[$buyer_name]; ?></strong></td>
					</tr>
				<? } ?>
				<tr>
					<td align="center" width="100%" class="form_caption" colspan="17"><strong style="font-size:12px">From <? echo change_date_format($start_date); ?> To <? echo change_date_format($end_date); ?> </strong></td>
				</tr>
			</table>
			<br />
			<?
			if ($category_by == 1) {
				if ($start_date != "" && $end_date != "") $date_cond = " and c.country_ship_date between '$start_date' and '$end_date'";
				else $date_cond = "";
			} else {
				if ($start_date != "" && $end_date != "") $date_cond = " and b.po_received_date between '$start_date' and '$end_date'";
				else $date_cond = "";
			}

			$main_sql = "select a.company_name, a.style_owner, a.id as job_id, a.job_no_prefix_num, a.job_no, a.buyer_name, a.total_set_qnty, a.style_ref_no, a.season_buyer_wise, a.style_description, a.ship_mode, b.id as po_id, b.po_number, c.country_ship_date, c.country_id, c.color_number_id, c.size_number_id, c.order_quantity, c.order_rate, c.order_total,c.size_order
				from  wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
				$company_name_cond $style_owner_cond $date_cond $brand_id_cond $season_yr_cond $team_leader_cond $search_string_cond $year_cond $buyer_id_cond $season_cond $file_cond $ref_cond and a.team_leader like '$team_leader' and a.dealing_marchant like '$dealing_marchant' order by c.size_order";
			//echo $main_sql; die;
			$main_result = sql_select($main_sql);
			$all_size_arr = array();
			$po_country_color_arr = array();
			$po_country_color_size_arr = array();
			$tot_rows = 0;
			$poIds = '';
			foreach ($main_result as $row) {
				$tot_rows++;
				$poIds .= $row[csf("po_id")] . ",";
				$all_size_arr[$row[csf("size_number_id")]] = $row[csf("size_number_id")];
				$po_country_color_arr[$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_ship_date")]][$row[csf("country_id")]][$row[csf("color_number_id")]] = $row[csf("style_ref_no")] . '***' . $row[csf("season_buyer_wise")] . '***' . $row[csf("style_description")] . '***' . $row[csf("ship_mode")] . '***' . $row[csf("po_number")] . '***' . $row[csf("style_owner")]; //

				$po_country_color_size_arr[$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_ship_date")]][$row[csf("country_id")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]['qty'] += $row[csf("order_quantity")];
				//$po_country_color_size_arr[$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_ship_date")]][$row[csf("country_id")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]['rate']+=$row[csf("order_rate")];
				$po_country_color_size_arr[$row[csf("job_no")]][$row[csf("po_id")]][$row[csf("country_ship_date")]][$row[csf("country_id")]][$row[csf("color_number_id")]][$row[csf("size_number_id")]]['amount'] += $row[csf("order_total")];
			}
			unset($main_result); // die;

			$poIds = chop($poIds, ',');
			$poIds_yarn_cond = "";
			$poIds_insp_cond = "";
			if ($db_type == 2 && $tot_rows > 1000) {
				$poIds_yarn_cond = " and (";
				$poIds_insp_cond = " and (";

				$poIdsArr = array_chunk(explode(",", $poIds), 999);
				foreach ($poIdsArr as $ids) {
					$ids = implode(",", $ids);
					$poIds_yarn_cond .= " b.po_break_down_id in($ids) or ";
					$poIds_insp_cond .= " po_break_down_id in($ids) or ";
				}
				$poIds_yarn_cond = chop($poIds_yarn_cond, 'or ');
				$poIds_yarn_cond .= ")";

				$poIds_insp_cond = chop($poIds_insp_cond, 'or ');
				$poIds_insp_cond .= ")";
			} else {
				$poIds_yarn_cond = " and b.po_break_down_id in ($poIds)";
				$poIds_insp_cond = " and po_break_down_id in ($poIds)";
			}

			$fabrication_data_arr = array();
			$fabrication_sql = sql_select("select a.fabric_description, b.po_break_down_id from wo_pre_cost_fabric_cost_dtls a, wo_pre_cos_fab_co_avg_con_dtls b where a.id=b.pre_cost_fabric_cost_dtls_id and a.status_active=1 and a.is_deleted=0 and b.cons>0 $poIds_yarn_cond");
			$fabrication_data_arr = array();
			foreach ($fabrication_sql as $row) {
				$fabrication_data_arr[$row[csf("po_break_down_id")]] .= $row[csf("fabric_description")] . ",";
			}
			unset($fabrication_sql);

			$insp_date_arr = array();
			$insp_sql = sql_select("select po_break_down_id, country_id, max(inspection_date) as inspection_date from pro_buyer_inspection where status_active=1 and is_deleted=0 $poIds_insp_cond group by po_break_down_id, country_id");
			foreach ($insp_sql as $row) {
				$insp_date_arr[$row[csf("po_break_down_id")]][$row[csf("country_id")]] = $row[csf("inspection_date")];
			}
			unset($insp_sql);

			$size_count = count($all_size_arr);
			$width = (80 * $size_count) + 1550;
			$job_count_arr = array();
			$po_count_arr = array();
			$date_count_arr = array();
			$country_count_arr = array();

			foreach ($po_country_color_arr as $job => $job_data) {
				$jobc = 0;
				foreach ($job_data as $po_id => $po_data) {
					$poc = 0;
					foreach ($po_data as $ship_date => $date_data) {
						$datec = 0;
						foreach ($date_data as $country_id => $country_data) {
							$countryc = 0;
							foreach ($country_data as $color_id => $other_val) {
								$jobc++;
								$poc++;
								$datec++;
								$countryc++;
								$job_count_arr[$job] = $jobc;
								$po_count_arr[$job][$po_id] = $poc;
								$date_count_arr[$job][$po_id][$ship_date] = $datec;
								$country_count_arr[$job][$po_id][$ship_date][$country_id] = $countryc;
							}
						}
					}
				}
			}

			?>
			<table width="<? echo $width; ?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
				<thead>
					<tr>
						<th width="30" rowspan="2">SL</th>
						<th width="100" rowspan="2">STYLE OWNER</th>
						<th width="100" rowspan="2">IMAGE</th>
						<th width="100" rowspan="2">JOB NO</th>
						<th width="100" rowspan="2">STYLE</th>
						<th width="80" rowspan="2">SEASON</th>
						<th width="100" rowspan="2">STYLE DESCRIPTION</th>
						<th width="70" rowspan="2">SHIP MODE</th>

						<th width="100" rowspan="2">PO NO.</th>
						<th width="130" rowspan="2">YARN COMPOSITION</th>
						<th width="70" rowspan="2">SHIP DATE</th>
						<th width="100" rowspan="2">COUNTRY</th>
						<th width="90" rowspan="2">COLOR</th>
						<th colspan="<? echo $size_count; ?>">SIZE QTY</th>
						<th width="90" rowspan="2">COLOR QTY</th>
						<th width="80" rowspan="2">AVG. FOB ($)</th>
						<th width="100" rowspan="2">AMOUNT ($)</th>
						<th rowspan="2">INSPECTION DATE</th>
					</tr>
					<tr>
						<?
						foreach ($all_size_arr as $size_id) {
						?>
							<th width="80"><? echo '&nbsp;' . $size_arr[$size_id]; ?></th>
						<?
						}
						?>
					</tr>
				</thead>
			</table>
			<div style="width:<? echo $width + 20; ?>px; overflow-y:scroll; max-height:380px;font-size:12px; overflow-x:hidden;" id="scroll_body" align="left">
				<table width="<? echo $width; ?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
					<tbody>
						<?
						$style_size_sum_arr = array();
						$size_sum_arr = array();
						$style_size_sumamt_arr = array();
						$size_sumamt_arr = array();
						$temp_arr_job = array();
						$k = 1;
						$m = 1;
						foreach ($po_country_color_arr as $job => $job_data) {
							$jobcount = $job_count_arr[$job];
							$jobr = 1;
							foreach ($job_data as $po_id => $po_data) {
								$pocount = $po_count_arr[$job][$po_id];
								$por = 1;
								foreach ($po_data as $ship_date => $date_data) {
									$datecount = $date_count_arr[$job][$po_id][$ship_date];
									$sdater = 1;
									foreach ($date_data as $country_id => $country_data) {
										$countrycount = $country_count_arr[$job][$po_id][$ship_date][$country_id];
										$countryr = 1;
										foreach ($country_data as $color_id => $other_val) {
											if ($m % 2 == 0) $bgcolor = "#E9F3FF";
											else $bgcolor = "#FFFFFF";
											$ex_val = explode('***', $other_val);
											$style_ref_no = '';
											$season_buyer_wise = 0;
											$style_description = '';
											$ship_mode = 0;
											$po_number = '';

											$style_ref_no = $ex_val[0];
											$season_buyer_wise = $ex_val[1];
											$style_description = $ex_val[2];
											$ship_mode = $ex_val[3];
											$po_number = $ex_val[4];
											$style_owner = $ex_val[5];
											if (!in_array($job, $temp_arr_job)) {
												$temp_arr_job[] = $job;
												if ($k != 1) {
						?>
													<tr bgcolor="#CCCCCC">
														<td>&nbsp;</td>
														<td>&nbsp;</td>
														<td>&nbsp;</td>
														<td>&nbsp;</td>
														<td>&nbsp;</td>
														<td>&nbsp;</td>
														<td>&nbsp;</td>
														<td>&nbsp;</td>
														<td>&nbsp;</td>
														<td align="right">Style Total :</td>
														<td>&nbsp;</td>
														<td>&nbsp;</td>
														<td>&nbsp;</td>
														<?
														$subcolor_qty = 0;
														$subcolor_amt = 0;
														foreach ($all_size_arr as $size_id) {
															$subsize_qty = 0;
															$subsize_amount = 0;
															$subsize_qty = $style_size_sum_arr[$size_id];
															$subsize_amount = $style_size_sumamt_arr[$size_id];
														?>
															<td align="right" style='word-break:break-all'><? if ($subsize_qty != "") echo number_format($subsize_qty, 2);
																											else echo ''; ?></td>
														<?
															$subcolor_qty += $subsize_qty;
															$subcolor_amt += $subsize_amount;
														}
														?>
														<td align="right"><? echo number_format($subcolor_qty, 2); ?></td>
														<td>&nbsp;</td>
														<td align="right"><? echo number_format($subcolor_amt, 2); ?></td>
														<td>&nbsp;</td>
													</tr>
											<?
													unset($style_size_sum_arr);
													unset($style_size_sumamt_arr);
												}
												$k++;
											}
											//echo $jobcount.'='.$pocount.'='.$datecount.'='.$countrycount.'='.$colorcount.'<br>';
											?>
											<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $m; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $m; ?>">
												<? if ($jobr == 1) { ?>
													<td width="30" align="center" rowspan="<? echo $jobcount; ?>"><? echo $m; ?></td>
													<td width="100" align="center" rowspan="<? echo $jobcount; ?>" style='word-break:break-all'><? echo $company_name_arr[$style_owner]; ?></td>
													<td width="100" rowspan="<? echo $jobcount; ?>" onclick="openmypage_image('requires/shipment_schedule_controller.php?action=show_image&job_no=<? echo $job; ?>','Image View')"><img src='../../../<? echo $imge_arr[$job]; ?>' height='25' width='80' /></td>
													<td width="100" rowspan="<? echo $jobcount; ?>">
														<p><? echo $job; ?></p>
													</td>
													<td width="100" rowspan="<? echo $jobcount; ?>" style='word-break:break-all'><? echo $style_ref_no; ?></td>
													<td width="80" rowspan="<? echo $jobcount; ?>" style='word-break:break-all'><? echo $season_arr[$season_buyer_wise]; ?></td>
													<td width="100" rowspan="<? echo $jobcount; ?>" style='word-break:break-all'><? echo $style_description; ?></td>
													<td width="70" rowspan="<? echo $jobcount; ?>"><? echo $shipment_mode[$ship_mode]; ?></td>
												<? $m++;
												}
												if ($por == 1) { ?>
													<td width="100" rowspan="<? echo $pocount; ?>" style='word-break:break-all'><? echo $po_number; ?></td>
													<td width="130" rowspan="<? echo $pocount; ?>" style='word-break:break-all'>
														<p><? $fabrication_data = implode(", ", array_unique(explode(",", chop($fabrication_data_arr[$po_id], " , "))));
															echo $fabrication_data; ?>&nbsp;</p>
													</td>
												<? }
												if ($sdater == 1) { ?>
													<td width="70" rowspan="<? echo $datecount; ?>"><? echo change_date_format($ship_date); ?></td>
												<? }
												if ($countryr == 1) { ?>
													<td width="100" rowspan="<? echo $countrycount; ?>" style='word-break:break-all'><? echo $country_arr[$country_id]; ?></td>
												<? } ?>
												<td width="90" style='word-break:break-all'><? echo $color_arr[$color_id]; ?></td>
												<?
												$color_qty = 0;
												$color_amt = 0;
												foreach ($all_size_arr as $size_id) {
													$size_qty = 0;
													$size_amount = 0;
													$size_qty = $po_country_color_size_arr[$job][$po_id][$ship_date][$country_id][$color_id][$size_id]['qty'];
													$size_amount = $po_country_color_size_arr[$job][$po_id][$ship_date][$country_id][$color_id][$size_id]['amount'];
												?>
													<td width="80" align="right" style='word-break:break-all'><? if ($size_qty != '') echo number_format($size_qty, 2);
																												else echo ""; ?></td>
												<?
													$color_qty += $size_qty;
													$color_amt += $size_amount;
													$size_sum_arr[$size_id] += $size_qty;
													$style_size_sum_arr[$size_id] += $size_qty;
													$style_size_sumamt_arr[$size_id] += $size_amount;
												}
												?>
												<td width="90" align="right"><? echo number_format($color_qty, 2); ?></td>
												<td width="80" align="right"><? $color_fob = 0;
																				$color_fob = $color_amt / $color_qty;
																				echo number_format($color_fob, 2); ?></td>
												<td width="100" align="right"><? $gcolor_amt += $color_amt;
																				echo number_format($color_amt, 2); ?></td>
												<td style='word-break:break-all'>
													<?
													$inspection_date = '';
													$inspection_date = $insp_date_arr[$po_id][$country_id];
													echo change_date_format($inspection_date); ?>&nbsp;
												</td>
											</tr>
						<?
											$jobr++;
											$por++;
											$sdater++;
											$countryr++;
											$k++;
										}
									}
								}
							}
						}
						?>
						<tr bgcolor="#CCCCCC">
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td align="right">Style Total :</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<?
							$subcolor_qty = 0;
							$subcolor_amt = 0;
							foreach ($all_size_arr as $size_id) {
								$subsize_qty = 0;
								$subsize_amount = 0;
								$subsize_qty = $style_size_sum_arr[$size_id];
								$subsize_amount = $style_size_sumamt_arr[$size_id];
							?>
								<td align="right" style='word-break:break-all'><? if ($subsize_qty != "") echo number_format($subsize_qty, 2);
																				else echo ""; ?></td>
							<?
								$subcolor_qty += $subsize_qty;
								$subcolor_amt += $subsize_amount;
							}
							?>
							<td align="right"><? echo number_format($subcolor_qty, 2); ?></td>
							<td>&nbsp;</td>
							<td align="right"><? echo number_format($subcolor_amt, 2); ?></td>
							<td>&nbsp;</td>
						</tr>
					</tbody>
				</table>
			</div>
			<table width="<? echo $width; ?>" class="tbl_bottom" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
				<thead>
					<tr bgcolor="#CCCCCC">
						<td width="30">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="80">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="70">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="130">Buyer Total :</td>
						<td width="70">&nbsp;</td>
						<td width="100">&nbsp;</td>
						<td width="90">&nbsp;</td>
						<?
						$gcolor_qty = 0;
						foreach ($all_size_arr as $size_id) {
							$gsize_qty = 0;
							$gsize_qty = $size_sum_arr[$size_id];
						?>
							<td width="80" align="right"><? echo number_format($gsize_qty, 2); ?></td>
						<?
							$gcolor_qty += $gsize_qty;
						}
						?>
						<td width="90" align="right"><? echo number_format($gcolor_qty, 2); ?></td>
						<td width="80">&nbsp;</td>
						<td width="100" align="right"><? echo number_format($gcolor_amt, 2); ?></td>
						<td>&nbsp;</td>
					</tr>
				</thead>
			</table>
		</div>
	<?
	}

	foreach (glob("$user_id*.xls") as $filename) {
		if (@filemtime($filename) < (time() - $seconds_old))
			@unlink($filename);
	}
	//---------end------------//
	$name = time();
	$filename = $user_id . "_" . $name . ".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, ob_get_contents());
	$filename = $user_id . "_" . $name . ".xls";
	echo "$total_data####$filename####$rpt_type";
	disconnect($con);
	exit();
}

if ($action == "show_image") {
	echo load_html_head_contents("Set Entry", "../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	//echo "select image_location  from common_photo_library  where master_tble_id='$job_no' and form_name='knit_order_entry' and is_deleted=0 and file_type=1";
	$data_array = sql_select("select image_location  from common_photo_library  where master_tble_id='$job_no' and form_name='knit_order_entry' and is_deleted=0 and file_type=1");
	?>
	<table>
		<tr>
			<?
			foreach ($data_array as $row) {
			?>
				<td><img src='../../../../<? echo $row[csf('image_location')]; ?>' height='250' width='300' /></td>
			<?
			}
			?>
		</tr>
	</table>
<?
	exit();
}

if ($action == "last_ex_factory_Date") {
	echo load_html_head_contents("Last Ex-Factory Details", "../../../../", 1, 1, $unicode, '', '');
	extract($_REQUEST);
	//echo $id;//$job_no;
	$buyerArr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
	$po_arr = "select a.buyer_name, a.style_ref_no, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in ($id) ";
	$sql_po = sql_select($po_arr);
?>
	<script>
		function generate_print_report(action, company_id, sys_number, ex_factory_date) {
			var report_title = "Garments Delivery Entry";
			print_report(company_id + '*' + sys_number + '*' + ex_factory_date + '*' + report_title + '*' + 5, "ExFactoryPrintSonia", "../../../../production/requires/garments_delivery_entry_controller")
		}
	</script>
	<div style="width:100%" align="center">
		<fieldset style="width:550px">
			<div class="form_caption" align="center"><strong>Last Ex-Factory Details</strong></div><br />
			<div style="width:100%">
				<table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
					<thead>
						<tr>
							<th colspan="2">Buyer: <? echo $buyerArr[$sql_po[0][csf('buyer_name')]]; ?></th>
							<th colspan="2">Style : <? echo $sql_po[0][csf('style_ref_no')]; ?></th>
							<th colspan="2">Po: <? echo $sql_po[0][csf('po_number')]; ?></th>
						</tr>
						<tr>
							<th width="35">SL</th>
							<th width="90">Ex-fac. Date</th>
							<th width="120">Challan No.</th>
							<th width="100">Ex-Fact. Qnty.</th>
							<th width="100">Ex-Fact. Return Qnty.</th>
							<th>Trans. Com.</th>
						</tr>
					</thead>
				</table>
			</div>
			<div style="width:100%; max-height:400px;">
				<table cellpadding="0" width="100%" cellspacing="0" border="1" rules="all" class="rpt_table">
					<?
					$i = 1;
					$job_po_qnty = 0;
					$job_plan_qnty = 0;
					$job_total_price = 0;
					/* $ex_fac_sql="SELECT id, ex_factory_date, ex_factory_qnty, challan_no, transport_com,
				CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END as ex_factory_qnty,
				CASE WHEN entry_form=85 THEN ex_factory_qnty ELSE 0 END as ex_factory_return_qnty
				from pro_ex_factory_mst where po_break_down_id in($id) and status_active=1 and is_deleted=0";*/

					$ex_fac_sql = ("select a.company_id, a.id, a.sys_number, b.ex_factory_date, b.challan_no, b.transport_com,
		CASE WHEN b.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END as ex_factory_qnty,
		CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END as ex_factory_return_qnty
		from  pro_ex_factory_delivery_mst a, pro_ex_factory_mst b where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.po_break_down_id in($id) ");
					//echo $ex_fac_sql;
					$sql_dtls = sql_select($ex_fac_sql);

					foreach ($sql_dtls as $row_real) {
						if ($i % 2 == 0) $bgcolor = "#EFEFEF";
						else $bgcolor = "#FFFFFF";
					?>
						<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_l<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="35"><? echo $i; ?></td>
							<td width="90"><? echo change_date_format($row_real[csf("ex_factory_date")]); ?></td>
							<td width="120"><a href="#" onClick="generate_print_report('<? echo 'ExFactoryPrintSonia'; ?>','<? echo $row_real[csf('company_id')]; ?>','<? echo $row_real[csf('id')]; ?>','<? echo change_date_format($row_real[csf("ex_factory_date")]); ?>')"><? echo $row_real[csf("challan_no")]; ?></a></td>
							<td width="100" align="right"><? echo $row_real[csf("ex_factory_qnty")]; ?></td>
							<td width="100" align="right"><? echo $row_real[csf("ex_factory_return_qnty")]; ?></td>
							<td><? echo $row_real[csf("transport_com")]; ?></td>
						</tr>
					<?
						$rec_qnty += $row_real[csf("ex_factory_qnty")];
						$return_qnty += $row_real[csf("ex_factory_return_qnty")];
						$i++;
					}
					?>
					<tfoot>
						<tr>
							<th colspan="3">Total</th>
							<th><? echo number_format($rec_qnty, 2); ?></th>
							<th><? echo number_format($return_qnty, 2); ?></th>
							<th>&nbsp;</th>
						</tr>

						<tr>
							<th colspan="3">Total Balance</th>
							<th colspan="3" align="right"><? echo number_format($rec_qnty - $return_qnty, 2); ?></th>
						</tr>
					</tfoot>
				</table>
			</div>
		</fieldset>
	</div>
	<div style="display:none" id="data_panel"></div>
	<script type="text/javascript" src="../../../../js/jquery.js"></script>
	<script type="text/javascript" src="../../../../js/jquerybarcode.js"></script>
	<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
<?
	exit();
}
//Ex-Factory Delv. and Return
if ($action == "ex_factory_popup") {
	//require_once('../../../../includes/common.php');
	echo load_html_head_contents("Ex-Factory Details", "../../../../", 1, 1, $unicode, '', '');

	extract($_REQUEST);
	//echo $id;//$job_no;
	$buyerArr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
	$po_arr = "select a.buyer_name, a.style_ref_no, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in ($id) ";
	$sql_po = sql_select($po_arr);
?>
	<script>
		function generate_print_report(action, company_id, sys_number, ex_factory_date) {
			var report_title = "Garments Delivery Entry";
			print_report(company_id + '*' + sys_number + '*' + ex_factory_date + '*' + report_title + '*' + 5, "ExFactoryPrintSonia", "../../../../production/requires/garments_delivery_entry_controller")
		}
	</script>
	<div style="width:100%" align="center">
		<fieldset style="width:500px">
			<div class="form_caption" align="center"><strong>Ex-Factory Details</strong></div><br />
			<div style="width:100%">
				<table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
					<thead>
						<tr>
							<th colspan="2">Buyer: <? echo $buyerArr[$sql_po[0][csf('buyer_name')]]; ?></th>
							<th>Style : <? echo $sql_po[0][csf('style_ref_no')]; ?></th>
							<th colspan="2">Po: <? echo $sql_po[0][csf('po_number')]; ?></th>
						</tr>
						<tr>
							<th width="35">SL</th>
							<th width="90">Ex-fac. Date</th>
							<th width="120">System /Challan no</th>
							<th width="100">Ex-Fact. Del.Qty.</th>
							<th>Ex-Fact.Return Qty.</th>
						</tr>
					</thead>
				</table>
			</div>
			<div style="width:100%; max-height:400px;">
				<table cellpadding="0" width="100%" cellspacing="0" border="1" rules="all" class="rpt_table">
					<?
					$i = 1;
					//$ex_fac_sql="SELECT id, ex_factory_date, ex_factory_qnty, challan_no, transport_com from pro_ex_factory_mst where po_break_down_id in($id) and status_active=1 and is_deleted=0";
					//echo $ex_fac_sql;

					$exfac_sql = ("select a.company_id, a.id, a.sys_number, b.ex_factory_date, b.challan_no,
		CASE WHEN b.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END as ex_factory_qnty,
		CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END as ex_factory_return_qnty
		from  pro_ex_factory_delivery_mst a, pro_ex_factory_mst b where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.po_break_down_id in($id) ");
					$sql_dtls = sql_select($exfac_sql);

					foreach ($sql_dtls as $row_real) {
						if ($i % 2 == 0) $bgcolor = "#EFEFEF";
						else $bgcolor = "#FFFFFF";
					?>
						<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_l<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="35"><? echo $i; ?></td>
							<td width="90"><? echo change_date_format($row_real[csf("ex_factory_date")]); ?></td>
							<td width="120"><a href="#" onClick="generate_print_report('<? echo 'ExFactoryPrintSonia'; ?>','<? echo $row_real[csf('company_id')]; ?>','<? echo $row_real[csf('id')]; ?>','<? echo change_date_format($row_real[csf("ex_factory_date")]); ?>')"><? echo $row_real[csf("sys_number")]; ?></a></td>
							<td width="100" align="right"><? echo $row_real[csf("ex_factory_qnty")]; ?></td>
							<td align="right"><? echo $row_real[csf("ex_factory_return_qnty")]; ?></td>
						</tr>
					<?
						$rec_qnty += $row_real[csf("ex_factory_qnty")];
						$rec_return_qnty += $row_real[csf("ex_factory_return_qnty")];
						$i++;
					}
					?>
					<tfoot>
						<tr>
							<th colspan="3">Total</th>
							<th><? echo number_format($rec_qnty, 2); ?></th>
							<th><? echo number_format($rec_return_qnty, 2); ?></th>
						</tr>
						<tr>
							<th colspan="3">Total Balance</th>
							<th colspan="2" align="right"><? echo number_format($rec_qnty - $rec_return_qnty, 2); ?></th>
						</tr>
					</tfoot>
				</table>
			</div>
		</fieldset>
	</div>
	<div style="display:none" id="data_panel"></div>
	<script type="text/javascript" src="../../../../js/jquery.js"></script>
	<script type="text/javascript" src="../../../../js/jquerybarcode.js"></script>
	<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
<?
	exit();
}

if ($action == "order_status_popup") {
	echo load_html_head_contents("Order Status", "../../../../", 1, 1, $unicode, '', '');
	extract($_REQUEST);
	//echo $id;//$job_no;
?>
	<div style="width:100%" align="center">
		<fieldset style="width:500px">
			<div class="form_caption" align="center"><strong>Order Status</strong></div><br />
			<div style="width:100%">
				<table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
					<thead>
						<tr>
							<th width="50">SL</th>
							<th width="150">Order No</th>
							<th>Status</th>
						</tr>
					</thead>
				</table>
			</div>
			<div style="width:100%; max-height:400px;">
				<table cellpadding="0" width="100%" cellspacing="0" border="1" rules="all" class="rpt_table">
					<?
					$i = 1;
					$job_po_qnty = 0;
					$job_plan_qnty = 0;
					$job_total_price = 0;
					$order_sql = "SELECT id, po_number, is_confirmed from wo_po_break_down where id in($id) and status_active=1 and is_deleted=0";
					$sql_dtls = sql_select($order_sql);

					foreach ($sql_dtls as $row_real) {
						if ($i % 2 == 0) $bgcolor = "#EFEFEF";
						else $bgcolor = "#FFFFFF";
					?>
						<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_l<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="50"><? echo $i; ?></td>
							<td width="150"><? echo $row_real[csf("po_number")]; ?></td>
							<td><? echo $order_status[$row_real[csf("is_confirmed")]]; ?></td>
						</tr>
					<?
						$i++;
					}
					?>
				</table>
			</div>
		</fieldset>
	</div>
<?
	exit();
}
?>