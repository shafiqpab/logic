<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];
$user_id=$_SESSION['logic_erp']['user_id'];
$buyer_cond=set_user_lavel_filtering(' and buy.id','buyer_id');
$company_cond=set_user_lavel_filtering(' and comp.id','company_id');
$cons_for_arr = array(1=>"Merketing",2=>"Budget",3=>"Production");
$search_by_arr=array(1=>'Inquery',2=>'Sample',3=>'Job');

if($action=="generate_cad_la_consting")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
		<script>
			function js_set_value(mrr)
			{
		 		$("#hidden_system_number").val(mrr);
				parent.emailwindow.hide();
			}
		</script>
	</head>

	<body>
	<div align="center" style="width:100%;" >
	<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	<table width="1180" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center" rules="all">
        <thead>
            <tr>
                <th colspan="9" style="display: none;"><? echo create_drop_down( "cbo_string_search_type", 160, $string_search_type,'', 1, "--Searching Type--" ); ?></th>
            </tr>
            <tr>
                <th width="150" class="must_entry_caption">Company Name</th>
                <th width="120">Buyer Name</th>
                <th width="120">Brand</th>
                <th width="100">Season</th>
                <th width="80">Season Year</th>
                <th width="80">Cons For</th>
				<th width="80">Search By</th>
                <th width="80">System NO.</th>
                <th width="100">Master Style Ref</th>
                <th width="70">Costing Date </th>
                <th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:80px" class="formbutton"  /></th>
            </tr>
        </thead>
        <tbody>
            <tr class="general">
                <td><? echo create_drop_down( "cbo_company_name", 150, "select comp.id,comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $company, "load_drop_down( 'consumption_la_costing_controller', this.value, 'load_drop_down_buyer_popup', 'buyer_td' );"); ?></td>
                <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" ); ?></td>
                <td id="brand_td"><? echo create_drop_down( "cbo_brand", 120, $blank_array,"", 1, "- Select- ", "", "" ); ?></td>
                <td id="season_td"><? echo create_drop_down( "cbo_season_name", 100, $blank_array,"", 1, "- Select- ", "", "" ); ?></td>
                <td><? echo create_drop_down( "season_year", 70, $year,"", 1, "- Select- ", "", "" ); ?></td>
                <td><? echo create_drop_down( "cbo_cons_for", 80, $cons_for_arr,"", 1, "- Select- ", "", "" ); ?></td>
				<td><? echo create_drop_down( "cbo_style_source", 80, $search_by_arr,"", 1, "- Select- ", 1,""); ?></td>
                <td><input type="text" style="width:70px" class="text_boxes"  name="txt_system_no" id="txt_system_no" /></td>
                <td><input type="text" style="width:90px" class="text_boxes"  name="txt_master_style" id="txt_master_style" /></td>
                <!-- <td><? echo create_drop_down( "cbo_year", 70, $year,"", 1, "- Select- ", date('Y'), "" ); ?></td>                
                <td><input type="text" style="width:80px" class="text_boxes"  name="txt_requst_no" id="txt_requst_no" /></td> -->
                <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="Date" /></td>
                <td><input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_system_no').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_master_style').value+'_'+document.getElementById('cbo_brand').value+'_'+document.getElementById('cbo_season_name').value+'_'+document.getElementById('season_year').value+'_'+document.getElementById('cbo_cons_for').value+'_'+document.getElementById('cbo_style_source').value, 'create_consumption_search_list_view', 'search_div', 'consumption_la_costing_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:80px;" />
                </td>
            </tr>
            <tr>
                <td align="center" valign="middle" colspan="7"><input type="hidden" id="hidden_system_number" value="" /></td>
            </tr>
        </tbody>
    </table>
    <div align="center" valign="top" id="search_div"> </div>
    </form>
	</div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if($action == "populate_data_from_consumption")
{
	//$ex_data = explode("_",$data);

	//if($ex_data[0]!=""){
		$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
		$season_arr=return_library_array("select id,season_name from lib_buyer_season","id","season_name");
		$brand_arr=return_library_array("select id,brand_name from lib_buyer_brand","id","brand_name");
		$system_data = sql_select("SELECT  a.id as system_id, a.system_no, a.inquiry_id, a.la_costing_date, a.merch_style, a.style_des, a.pattern_master, a.bom_no, a.comments, a.mclastmod_date , a.cons_for , a.mail_send_date,  b.company_id, b.buyer_id, b.season_buyer_wise, b.season_year, b.brand_id, b.style_refernce, b.fabrication, b.color from consumption_la_costing_mst a join wo_quotation_inquery b on a.inquiry_id=b.id where a.id='$data' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id");
		foreach ($system_data as $row) {		
			echo "document.getElementById('txt_system_id').value = '".$row[csf("system_no")]."';\n";
			echo "document.getElementById('inquery_id').value = '".$row[csf("inquiry_id")]."';\n";
			echo "document.getElementById('txt_style_ref').value = '".$row[csf("style_refernce")]."';\n";
			echo "document.getElementById('txt_buyer_name').value = '".$buyer_arr[$row[csf("buyer_id")]]."';\n";
			echo "document.getElementById('txt_season').value = '".$season_arr[$row[csf("season_buyer_wise")]]."';\n";
			echo "document.getElementById('txt_season_year').value = '".$row[csf("season_year")]."';\n";
			echo "document.getElementById('txt_brand_name').value = '".$brand_arr[$row[csf("brand_id")]]."';\n";
			echo "document.getElementById('txt_fabrication').value = '".$row[csf("fabrication")]."';\n";
			echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_id")]."';\n";		
			echo "document.getElementById('txt_consumption_date').value = '".change_date_format($row[csf("la_costing_date")],"dd-mm-yyyy","-")."';\n";
			echo "document.getElementById('txt_mclastmod_date').value = '".change_date_format($row[csf("mclastmod_date")],"dd-mm-yyyy","-")."';\n";
			echo "document.getElementById('txt_merch_style').value = '".$row[csf("merch_style")]."';\n";
			echo "document.getElementById('txt_style_desc').value = '".$row[csf("style_des")]."';\n";
			echo "document.getElementById('txt_pattern_master').value = '".$row[csf("pattern_master")]."';\n";
			echo "document.getElementById('txt_bom_no').value = '".$row[csf("bom_no")]."';\n";
			echo "document.getElementById('txt_boby_wash_color').value = '".$row[csf("color")]."';\n";
			echo "document.getElementById('txt_comments').value = '".$row[csf("comments")]."';\n";
			echo "document.getElementById('update_id').value = '".$row[csf("system_id")]."';\n";
			echo "document.getElementById('cbo_cons_for').value = '".$row[csf("cons_for")]."';\n";
			echo "document.getElementById('text_mail_send_date').value = '".$row[csf("mail_send_date")]."';\n";
			echo "$('#txt_style_ref').attr('disabled',true);\n";
			//echo "$('#txt_style_ref').attr('ondblclick', '').unbind('click');\n";
			echo "$('#txt_style_ref').removeAttr('ondblclick');\n";
		}

	//}
	
}

if($action == "populate_data_from_req_consumption")
{
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$season_arr=return_library_array("select id,season_name from lib_buyer_season","id","season_name");
	$brand_arr=return_library_array("select id,brand_name from lib_buyer_brand","id","brand_name");
	
	$system_data = sql_select("SELECT  a.id as system_id, a.system_no, a.inquiry_id, a.la_costing_date, a.merch_style, a.style_des, a.pattern_master, a.bom_no, a.comments, a.mclastmod_date , a.cons_for , a.mail_send_date,a.search_by , b.company_id, b.buyer_name, b.season_buyer_wise, b.season_year, b.brand_id, b.style_ref_no, c.determination_id from consumption_la_costing_mst a join sample_development_mst b on a.inquiry_id=b.id join sample_development_fabric_acc c on b.id=c.sample_mst_id and a.inquiry_id=c.sample_mst_id where a.system_no='$data' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by a.id");

	//$inquery_data = sql_select("SELECT  a.requisition_number,a.buyer_name, a.season, a.season_year, a.brand_id, a.company_id, a.season_buyer_wise, a.style_ref_no,a.id, b.fabric_description,b.determination_id from sample_development_mst a,sample_development_fabric_acc b  where a.id=b.sample_mst_id and a.is_deleted=0 and a.entry_form_id=449 and a.requisition_number='$data' order by a.id DESC ");
	$deterArr=array();
	foreach ($system_data as $rows) {
		$deterArr['determination_id'].=$rows[csf("determination_id")].",";
	}
	$deter_ids=chop($deterArr['determination_id'],",");

	foreach ($system_data as $row) {		
		echo "document.getElementById('txt_system_id').value = '".$row[csf("system_no")]."';\n";
		echo "document.getElementById('inquery_id').value = '".$row[csf("inquiry_id")]."';\n";
		echo "document.getElementById('txt_style_ref').value = '".$row[csf("style_ref_no")]."';\n";
		echo "document.getElementById('txt_buyer_name').value = '".$buyer_arr[$row[csf("buyer_name")]]."';\n";
		echo "document.getElementById('txt_season').value = '".$season_arr[$row[csf("season_buyer_wise")]]."';\n";
		echo "document.getElementById('txt_season_year').value = '".$row[csf("season_year")]."';\n";
		echo "document.getElementById('txt_brand_name').value = '".$brand_arr[$row[csf("brand_id")]]."';\n";
		echo "document.getElementById('txt_fabrication').value = '".$deter_ids."';\n";
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_id")]."';\n";		
		echo "document.getElementById('txt_consumption_date').value = '".change_date_format($row[csf("la_costing_date")],"dd-mm-yyyy","-")."';\n";
		echo "document.getElementById('txt_mclastmod_date').value = '".change_date_format($row[csf("mclastmod_date")],"dd-mm-yyyy","-")."';\n";
		echo "document.getElementById('txt_merch_style').value = '".$row[csf("merch_style")]."';\n";
		echo "document.getElementById('txt_style_desc').value = '".$row[csf("style_des")]."';\n";
		echo "document.getElementById('txt_pattern_master').value = '".$row[csf("pattern_master")]."';\n";
		echo "document.getElementById('txt_bom_no').value = '".$row[csf("bom_no")]."';\n";
		echo "document.getElementById('txt_comments').value = '".$row[csf("comments")]."';\n";
		echo "document.getElementById('update_id').value = '".$row[csf("system_id")]."';\n";
		echo "document.getElementById('cbo_cons_for').value = '".$row[csf("cons_for")]."';\n";
		echo "document.getElementById('cbo_style_source').value = '".$row[csf("search_by")]."';\n";
		echo "document.getElementById('text_mail_send_date').value = '".$row[csf("mail_send_date")]."';\n";
		echo "$('#txt_style_ref').attr('disabled',true);\n";
		//echo "$('#txt_style_ref').attr('ondblclick', '').unbind('click');\n";
		echo "$('#txt_style_ref').removeAttr('ondblclick');\n";
	}
}

if($action == "populate_data_from_job_consumption")
{
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$season_arr=return_library_array("select id,season_name from lib_buyer_season","id","season_name");
	$brand_arr=return_library_array("select id,brand_name from lib_buyer_brand","id","brand_name");
	
	$system_data = sql_select("SELECT  a.id as system_id, a.system_no, a.inquiry_id, a.la_costing_date, a.merch_style, a.style_des, a.pattern_master, a.bom_no, a.comments, a.mclastmod_date, a.cons_for, a.mail_send_date, a.search_by, b.company_name, b.buyer_name, b.season_buyer_wise, b.season_year, b.brand_id, b.style_ref_no, a.fabrication_id as determination_id from consumption_la_costing_mst a join wo_po_details_master b on a.inquiry_id=b.id where a.system_no='$data' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  order by a.id");

	//$inquery_data = sql_select("SELECT  a.requisition_number,a.buyer_name, a.season, a.season_year, a.brand_id, a.company_id, a.season_buyer_wise, a.style_ref_no,a.id, b.fabric_description,b.determination_id from sample_development_mst a,sample_development_fabric_acc b  where a.id=b.sample_mst_id and a.is_deleted=0 and a.entry_form_id=449 and a.requisition_number='$data' order by a.id DESC ");
	$deterArr=array();
	foreach ($system_data as $rows) {
		$deterArr['determination_id'].=$rows[csf("determination_id")].",";
	}
	$deter_ids=chop($deterArr['determination_id'],",");

	foreach ($system_data as $row) {		
		echo "document.getElementById('txt_system_id').value = '".$row[csf("system_no")]."';\n";
		echo "document.getElementById('inquery_id').value = '".$row[csf("inquiry_id")]."';\n";
		echo "document.getElementById('txt_style_ref').value = '".$row[csf("style_ref_no")]."';\n";
		echo "document.getElementById('txt_buyer_name').value = '".$buyer_arr[$row[csf("buyer_name")]]."';\n";
		echo "document.getElementById('txt_season').value = '".$season_arr[$row[csf("season_buyer_wise")]]."';\n";
		echo "document.getElementById('txt_season_year').value = '".$row[csf("season_year")]."';\n";
		echo "document.getElementById('txt_brand_name').value = '".$brand_arr[$row[csf("brand_id")]]."';\n";
		echo "document.getElementById('txt_fabrication').value = '".$deter_ids."';\n";
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_name")]."';\n";		
		echo "document.getElementById('txt_consumption_date').value = '".change_date_format($row[csf("la_costing_date")],"dd-mm-yyyy","-")."';\n";
		echo "document.getElementById('txt_mclastmod_date').value = '".change_date_format($row[csf("mclastmod_date")],"dd-mm-yyyy","-")."';\n";
		echo "document.getElementById('txt_merch_style').value = '".$row[csf("merch_style")]."';\n";
		echo "document.getElementById('txt_style_desc').value = '".$row[csf("style_des")]."';\n";
		echo "document.getElementById('txt_pattern_master').value = '".$row[csf("pattern_master")]."';\n";
		echo "document.getElementById('txt_bom_no').value = '".$row[csf("bom_no")]."';\n";
		echo "document.getElementById('txt_comments').value = '".$row[csf("comments")]."';\n";
		echo "document.getElementById('update_id').value = '".$row[csf("system_id")]."';\n";
		echo "document.getElementById('cbo_cons_for').value = '".$row[csf("cons_for")]."';\n";
		echo "document.getElementById('cbo_style_source').value = '".$row[csf("search_by")]."';\n";
		echo "document.getElementById('text_mail_send_date').value = '".$row[csf("mail_send_date")]."';\n";
		echo "$('#txt_style_ref').attr('disabled',true);\n";
		//echo "$('#txt_style_ref').attr('ondblclick', '').unbind('click');\n";
		echo "$('#txt_style_ref').removeAttr('ondblclick');\n";
	}
}

if($action == "create_consumption_search_list_view")
{
	$ex_data = explode("_",$data);
	$txt_buyer = $ex_data[0];
	$system_no = $ex_data[1];
	$system_date = $ex_data[2];
	$company = $ex_data[3];
	$master_style = $ex_data[5];
	$brand = $ex_data[6];
	$season = $ex_data[7];
	$season_year = $ex_data[8];
	$cons = $ex_data[9];
	$search_by = $ex_data[10];
	$single_user_cond="";

	if($company==0) $company_name_cond=""; else $company_name_cond=" and a.company_id=$company";
	if($search_by==1){
		if($txt_buyer==0){
			if($_SESSION['logic_erp']['buyer_id'] != '' && $_SESSION['logic_erp']['buyer_id'] !=0)
			{
				$buyer_name_cond="and b.buyer_id in(" . $_SESSION['logic_erp']['buyer_id'] . ")";
			}
			else{
				$buyer_name_cond="";
			}
		}
		else{
			$buyer_name_cond="and b.buyer_id=$txt_buyer";
		}
	}else{
		if($txt_buyer==0){
			if($_SESSION['logic_erp']['buyer_name'] != '' && $_SESSION['logic_erp']['buyer_name'] !=0)
			{
				$buyer_name_cond="and b.buyer_name in(" . $_SESSION['logic_erp']['buyer_name'] . ")";
			}
			else{
				$buyer_name_cond="";
			}
		}
		else{
			$buyer_name_cond="and b.buyer_name=$txt_buyer";
		}
	}
	if($_SESSION['logic_erp']['single_user'] != 0 && $_SESSION['logic_erp']['single_user'] != '')
	{
		$single_user_cond = " and a.inserted_by=".$_SESSION['logic_erp']['user_id']."";
	}
	if($search_by==1){
		if($master_style=='') $master_style_cond=""; else $master_style_cond="and b.style_refernce like '%".$master_style."%' ";
	}else{
		if($master_style=='') $master_style_cond=""; else $master_style_cond="and b.style_ref_no like '%".$master_style."%' ";
	}
	if( $system_date!="" )  $system_date_cond.= " and a.la_costing_date='".change_date_format($inq_date,'yyyy-mm-dd',"-",1)."'";
	if(str_replace("'","",$system_no)!="")  $system_no_cond="and  system_no_prefix_num like '%".str_replace("'","",$system_no)."' ";
	if($season!=0) $query_cond.=" and b.season_buyer_wise=$season";
	if($season_year!=0) $query_cond.=" and b.season_year=$season_year";
	if($cons!=0) $query_cond.=" and a.cons_for=$cons";
	if($brand ==0){
		if($_SESSION['logic_erp']['brand_id'] != '' && $_SESSION['logic_erp']['brand_id'] !=0){
			$query_cond.=" and b.brand_id in(" .$_SESSION['logic_erp']['brand_id']. ")";
		}
		else $query_cond.="";
	}
	else $query_cond.=" and b.brand_id=$brand";
	
	$season_buyer_wise_arr = return_library_array("select id,season_name from  lib_buyer_season ","id","season_name");
	$buyer_arr = return_library_array("select id,buyer_name from  lib_buyer ","id","buyer_name");
	$company_arr = return_library_array("select id,company_name from lib_company","id","company_name");
	$brand_arr = return_library_array("select id,brand_name from lib_buyer_brand","id","brand_name");

	if($search_by==1){
		$arr=array(0=>$company_arr,1=>$buyer_arr,2=>$brand_arr,7=>$season_buyer_wise_arr,9=>$cons_for_arr);
		$sql = "SELECT a.id as system_id, a.company_id, a.system_no, a.la_costing_date, a.cons_for,  b.style_refernce, b.buyer_id, b.brand_id, b.season_buyer_wise, b.season,b.season_year, b.color from consumption_la_costing_mst a join wo_quotation_inquery b on a.inquiry_id=b.id where a.entry_form=653 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_name_cond $buyer_name_cond $system_date_cond $system_no_cond $master_style_cond $query_cond $single_user_cond order by a.id desc";

		echo create_list_view("list_view", "Company Name, Buyer Name, Brand, System NO, Master Style Ref.,Body/ Wash Color,Costing Date, Season, Season Year, Cons For","150,120,100,120,100,90,60,60,80,80","1020","260",0, $sql , "js_set_value", "system_id,system_no", "", 1, "company_id,buyer_id,brand_id,0,0,0,0,season_buyer_wise,0,cons_for", $arr, "company_id,buyer_id,brand_id,system_no,style_refernce,color,la_costing_date,season_buyer_wise,season_year,cons_for", "",'','0') ;
	}else if($search_by==2){
		$arr=array(0=>$company_arr,1=>$buyer_arr,2=>$brand_arr,6=>$season_buyer_wise_arr,8=>$cons_for_arr);
		$sql = "SELECT a.id as system_id, a.company_id, a.system_no, a.la_costing_date, a.cons_for,a.search_by,  b.style_ref_no, b.buyer_name, b.brand_id, b.season_buyer_wise, b.season,b.season_year from consumption_la_costing_mst a join sample_development_mst b on a.inquiry_id=b.id where a.entry_form=653 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_name_cond $buyer_name_cond $system_date_cond $system_no_cond $master_style_cond $query_cond $single_user_cond order by a.id desc";

		echo create_list_view("list_view", "Company Name, Buyer Name, Brand, System NO, Master Style Ref.,Costing Date, Season, Season Year, Cons For","150,120,100,120,100,60,60,80,80","930","260",0, $sql , "js_set_value", "system_id,system_no,search_by", "", 1, "company_id,buyer_name,brand_id,0,0,0,season_buyer_wise,0,cons_for", $arr, "company_id,buyer_name,brand_id,system_no,style_ref_no,la_costing_date,season_buyer_wise,season_year,cons_for", "",'','0') ;
	} else if($search_by==3){
		$arr=array(0=>$company_arr,1=>$buyer_arr,2=>$brand_arr,6=>$season_buyer_wise_arr,8=>$cons_for_arr);
		$sql = "SELECT a.id as system_id, a.company_id, a.system_no, a.la_costing_date, a.cons_for,a.search_by, b.style_ref_no, b.buyer_name, b.brand_id, b.season_buyer_wise, b.season, b.season_year from consumption_la_costing_mst a join wo_po_details_master b on a.inquiry_id=b.id where a.entry_form=653 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_name_cond $buyer_name_cond $system_date_cond $system_no_cond $master_style_cond $query_cond $single_user_cond order by a.id desc";

		echo create_list_view("list_view", "Company Name, Buyer Name, Brand, System NO, Master Style Ref.,Costing Date, Season, Season Year, Cons For","150,120,100,120,100,60,60,80,80","930","260",0, $sql , "js_set_value", "system_id,system_no,search_by", "", 1, "company_id,buyer_name,brand_id,0,0,0,season_buyer_wise,0,cons_for", $arr, "company_id,buyer_name,brand_id,system_no,style_ref_no,la_costing_date,season_buyer_wise,season_year,cons_for", "",'','0') ;
	}
	
	?>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="generate_buyer_inquery")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	//$season_buyer_wise_arr = return_library_array("select id,season_name from  lib_buyer_season","id","season_name");
	//$brandArr = return_library_array("select id,brand_name from  lib_buyer_brand ","id","brand_name");
	?>
		<script>
			function js_set_value(mrr)
			{
		 		$("#hidden_issue_number").val(mrr);
				parent.emailwindow.hide();
			}
		</script>
	</head>

	<body>
	<div align="center" style="width:100%;" >
	<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	<table width="1010" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
        <thead>
            <tr>
                <th colspan="10"><? echo create_drop_down( "cbo_string_search_type", 160, $string_search_type,'', 1, "--Searching Type--" ); ?></th>
            </tr>
            <tr>
                <th width="150" class="must_entry_caption">Company Name</th>
                <th width="120">Buyer Name</th>
                <th width="100">Brand</th>
                               
                <th width="100">Season</th>
                <th width="80">Season Year</th>
                <th width="80">Year</th>
                <th width="100">M.Style Ref.</th>
				<? if($cbo_style_source==1) { ?>
                <th width="100" id="search_td">Buyer Inquery No</th>
                <th width="100" id="search_td_date">Inquiry Date </th>
				<? } else if($cbo_style_source==2) { ?>
				<th width="100" id="search_td">Sample Req No</th>
                <th width="100" id="search_td_date">Req Date </th>
				<? } else { ?>
				<th width="100" id="search_td">Job No</th>
                <th width="100" id="search_td_date">Ship Date</th>
				<? } ?>
                <th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:80px" class="formbutton"  /></th>
            </tr>
        </thead>
        <tbody>
            <tr class="general">
                <td><? echo create_drop_down( "cbo_company_name", 150, "select comp.id,comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", "", "load_drop_down( 'consumption_la_costing_controller', this.value, 'load_drop_down_buyer_popup', 'buyer_td' );"); ?></td>
                <td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" ); ?></td>
                <td id="brand_td"><? echo create_drop_down( "cbo_brand", 100, $blank_array,"", 1, "- Select- ", "", "" ); ?></td>           
                <td id="season_td"><? echo create_drop_down( "cbo_season_name", 100, $blank_array,"", 1, "- Select- ", "", "" ); ?></td>
                <td><? echo create_drop_down( "season_year", 70, $year,"", 1, "- Select- ", "", "" ); ?></td>
                <td><? echo create_drop_down( "cbo_year", 70, $year,"", 1, "- Select- ", date('Y'), "" ); ?></td>
                <td><input type="text" style="width:100px" class="text_boxes"  name="txt_style" id="txt_style" /></td>
                <td><input type="text" style="width:80px" class="text_boxes"  name="txt_requst_no" id="txt_requst_no" /></td>
                <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="Date" /></td>
                <td><input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_year').value+'_'+document.getElementById('txt_requst_no').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_season_name').value+'_'+document.getElementById('season_year').value+'_'+document.getElementById('cbo_brand').value+'_'+'<?=$cbo_style_source; ?>', 'create_inquery_search_list_view', 'search_div', 'consumption_la_costing_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:80px;" />
                </td>
            </tr>
            <tr>
                <td align="center" valign="middle" colspan="10"><input type="hidden" id="hidden_issue_number" value="" /></td>
            </tr>
        </tbody>
    </table>
    <div align="center" valign="top" id="search_div"> </div>
    </form>
	</div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if($action=="create_inquery_search_list_view")
{
	$ex_data = explode("_",$data);
	$txt_buyer = $ex_data[0];
	$txt_style = $ex_data[1];
	$inq_date = $ex_data[2];
	$company = $ex_data[3];
	$insertYear = $ex_data[4];
	$mrrNo = $ex_data[5];
	$searchStr = $ex_data[6];
	$season = $ex_data[7];
	$season_year = $ex_data[8];
	$brand = $ex_data[9];
	$search_by_field = $ex_data[10];
	$query_cond="";

	$season_buyer_wise_arr = return_library_array("select id,season_name from  lib_buyer_season ","id","season_name");
	$buyer_arr = return_library_array("select id,buyer_name from  lib_buyer ","id","buyer_name");
	$company_arr = return_library_array("select id,company_name from lib_company","id","company_name");
	$brand_arr = return_library_array("select id,brand_name from lib_buyer_brand","id","brand_name");

    if($company==0) $company_name=""; else $company_name=" and company_id=$company";
	if($search_by_field==1)//Inquiry
	{
		if($txt_buyer==0){
			if($_SESSION['logic_erp']['buyer_id'] !='' && $_SESSION['logic_erp']['buyer_id'] !=0)
			{
				$buyer_name=" and buyer_id in(" . $_SESSION['logic_erp']['buyer_id'] . ")";
			}
			else $buyer_name="";
		}
		else $buyer_name="and buyer_id=$txt_buyer";
		
		if($season!=0) $query_cond.=" and season_buyer_wise=$season";
		if($season_year!=0) $query_cond.=" and season_year=$season_year";
		if($brand==0)
		{
			if($_SESSION['logic_erp']['brand_id'] !='' && $_SESSION['logic_erp']['brand_id'] !=0){
				$query_cond.= " and ( brand_id in(" . $_SESSION['logic_erp']['brand_id'] . ") or brand_id in(0) )";
			}
			else $query_cond.="";
		}
		else $query_cond.=" and brand_id=$brand";
	
		$year_cond=" and to_char(insert_date,'YYYY')=$insertYear";
		if( $inq_date!="" )  $inquery_date.= " and inquery_date='".change_date_format($inq_date,'yyyy-mm-dd',"-",1)."'";
	
		$sql_cond=''; $inquery_id_cond=''; $request_no='';
		if($searchStr==1)
		{
			if(str_replace("'","",$txt_style)!="")  $sql_cond="and  style_refernce='".str_replace("'","",$txt_style)."'";
			if (trim($mrrNo)!="")  $inquery_id_cond=" and system_number_prefix_num='$mrrNo'  $year_cond";
		}
		else if($searchStr==4 || $searchStr==0)
		{
			if(str_replace("'","",$txt_style)!="")  $sql_cond="and  style_refernce like '%".str_replace("'","",$txt_style)."%' ";
			if (trim($mrrNo)!="") $inquery_id_cond=" and system_number_prefix_num like '%$mrrNo%' $year_cond";
		}
		else if($searchStr==2)
		{
			if(str_replace("'","",$txt_style)!="")  $sql_cond="and  style_refernce like '".str_replace("'","",$txt_style)."%' ";
			if (trim($mrrNo)!="") $inquery_id_cond=" and system_number_prefix_num like '$mrrNo%' $year_cond";
		}
		else if($searchStr==3)
		{
			if(str_replace("'","",$txt_style)!="")  $sql_cond="and  style_refernce like '%".str_replace("'","",$txt_style)."' ";
			if (trim($mrrNo)!="") $inquery_id_cond=" and system_number_prefix_num like '%$mrrNo' $year_cond";
		}
	
		$sql = "SELECT system_number_prefix_num, system_number, buyer_request, season, season_year, brand_id, company_id, buyer_id, season_buyer_wise, inquery_date, style_refernce, status_active, extract(year from insert_date) as year ,id, color from wo_quotation_inquery where is_deleted=0 and entry_form=434  and id not in (SELECT inquiry_id  from consumption_la_costing_mst where status_active=1 and is_deleted=0 and inquiry_id!=0 $company_name group by inquiry_id having count(inquiry_id) >= 3) $company_name $buyer_name $sql_cond $inquery_id_cond $request_no $inquery_date $year_cond $query_cond order by id DESC ";
		//echo $sql;
		foreach (sql_select($sql) as $row) {
			$inquery_id_arr[$row[csf('id')]] = $row[csf('id')];
		}
		
		$la_casting_data=sql_select("SELECT id, inquiry_id, cons_for from CONSUMPTION_LA_COSTING_MST where status_active=1 and is_deleted=0 and cons_for is not null".where_con_using_array($inquery_id_arr,0,'inquiry_id'));

		foreach ($la_casting_data as $row) {
			$cons_for_data[$row[csf('inquiry_id')]][] =$cons_for_arr[$row[csf('cons_for')]];
		}
		
		foreach ($cons_for_data as $inq_id=>$cons_arr) {
			$inquery_cons[$inq_id] = implode(", ", $cons_arr);
		}
		$arr=array(0=>$company_arr,1=>$buyer_arr,2=>$brand_arr,9=>$season_buyer_wise_arr,11=>$row_status,12=>$inquery_cons);
		echo create_list_view("list_view", "Company Name,Buyer Name,Brand,Inquery ID,Year,Buyer Inquery No,Master Style Ref., Body/ Wash Color, Inquery Date,Season,Season  Year, Status,Cons For","120,100,80,70,50,70,120,90,90,40,40,100","1100","260",0, $sql , "js_set_value", "system_number,id", "", 1, "company_id,buyer_id,brand_id,0,0,0,0,0,0,season_buyer_wise,0,status_active,id", $arr, "company_id,buyer_id,brand_id,system_number_prefix_num,year,buyer_request,style_refernce,color,inquery_date,season_buyer_wise,season_year,status_active,id", "",'','0') ;
	}
	else if($search_by_field==2)//Sample
	{
		if($txt_buyer==0){
			if($_SESSION['logic_erp']['buyer_id'] !='' && $_SESSION['logic_erp']['buyer_id'] !=0)
			{
				$buyer_name=" and buyer_name in(" . $_SESSION['logic_erp']['buyer_id'] . ")";
			}
			else $buyer_name="";
		}
		else $buyer_name="and buyer_name=$txt_buyer";
		
		if($season!=0) $query_cond.=" and season_buyer_wise=$season";
		if($season_year!=0) $query_cond.=" and season_year=$season_year";
		if($brand==0)
		{
			if($_SESSION['logic_erp']['brand_id'] !='' && $_SESSION['logic_erp']['brand_id'] !=0){
				$query_cond.= " and ( brand_id in(" . $_SESSION['logic_erp']['brand_id'] . ") or brand_id in(0) )";
			}
			else $query_cond.="";
		}
		else $query_cond.=" and brand_id=$brand";
	
		$year_cond=" and to_char(insert_date,'YYYY')=$insertYear";
		if( $inq_date!="" )  $inquery_date.= " and requisition_date='".change_date_format($inq_date,'yyyy-mm-dd',"-",1)."'";
		$sql_cond=''; $inquery_id_cond=''; $request_no='';
		
		if($searchStr==1)
		{
			if(str_replace("'","",$txt_style)!="")  $sql_cond="and  style_ref_no='".str_replace("'","",$txt_style)."'";
			if (trim($mrrNo)!="")  $inquery_id_cond=" and requisition_number_prefix_num='$mrrNo'  $year_cond";
		}
		else if($searchStr==4 || $searchStr==0)
		{
			if(str_replace("'","",$txt_style)!="")  $sql_cond="and  style_ref_no like '%".str_replace("'","",$txt_style)."%' ";
			if (trim($mrrNo)!="") $inquery_id_cond=" and requisition_number_prefix_num like '%$mrrNo%' $year_cond";
		}
		else if($searchStr==2)
		{
			if(str_replace("'","",$txt_style)!="")  $sql_cond="and  style_ref_no like '".str_replace("'","",$txt_style)."%' ";
			if (trim($mrrNo)!="") $inquery_id_cond=" and requisition_number_prefix_num like '$mrrNo%' $year_cond";
		}
		else if($searchStr==3)
		{
			if(str_replace("'","",$txt_style)!="")  $sql_cond="and style_ref_no like '%".str_replace("'","",$txt_style)."' ";
			if (trim($mrrNo)!="") $inquery_id_cond=" and requisition_number_prefix_num like '%$mrrNo' $year_cond";
		}
		
		$sql = "SELECT requisition_number_prefix_num, requisition_number, buyer_name, season, season_year, brand_id, company_id, season_buyer_wise, requisition_date, style_ref_no, status_active, extract(year from insert_date) as year ,id from sample_development_mst where is_deleted=0 and entry_form_id=449 and id not in (SELECT inquiry_id  from consumption_la_costing_mst where status_active=1 and is_deleted=0 and inquiry_id!=0 $company_name group by inquiry_id having count(inquiry_id) >= 3) $company_name $buyer_name $sql_cond $inquery_id_cond $request_no $req_date $year_cond $query_cond order by id DESC ";

		foreach (sql_select($sql) as $row) {
			$inquery_id_arr[$row[csf('id')]] = $row[csf('id')];
		}
		$la_casting_data=sql_select("SELECT id, inquiry_id, cons_for from CONSUMPTION_LA_COSTING_MST where status_active=1 and is_deleted=0 and cons_for is not null".where_con_using_array($inquery_id_arr,0,'inquiry_id'));

		foreach ($la_casting_data as $row) {
			$cons_for_data[$row[csf('inquiry_id')]][] =$cons_for_arr[$row[csf('cons_for')]];
		}
		
		foreach ($cons_for_data as $inq_id=>$cons_arr) {
			$inquery_cons[$inq_id] = implode(", ", $cons_arr);
		}
		$arr=array(0=>$company_arr,1=>$buyer_arr,2=>$brand_arr,7=>$season_buyer_wise_arr,9=>$row_status,10=>$inquery_cons);
		echo create_list_view("list_view", "Company Name,Buyer Name,Brand,Req ID,Year,Master Style Ref., Requisition Date,Season,Season  Year, Status,Cons For","120,100,80,70,50,70,120,90,90,40,40,100","1000","260",0, $sql , "js_set_value", "requisition_number,id", "", 1, "company_id,buyer_name,brand_id,0,0,0,0,season_buyer_wise,0,status_active,id", $arr, "company_id,buyer_name,brand_id,requisition_number_prefix_num,year,style_ref_no,requisition_date,season_buyer_wise,season_year,status_active,id", "",'','0') ;
	}
	else if($search_by_field==3)//Job
	{
		if($company==0) $companyCond=""; else $companyCond=" and a.company_name=$company";
		if($txt_buyer==0){
			if($_SESSION['logic_erp']['buyer_id'] !='' && $_SESSION['logic_erp']['buyer_id'] !=0)
			{
				$buyer_name=" and a.buyer_name in(" . $_SESSION['logic_erp']['buyer_id'] . ")";
			}
			else $buyer_name="";
		}
		else $buyer_name="and a.buyer_name=$txt_buyer";
		
		if($season!=0) $query_cond.=" and a.season_buyer_wise=$season";
		if($season_year!=0) $query_cond.=" and a.season_year=$season_year";
		if($brand==0)
		{
			if($_SESSION['logic_erp']['brand_id'] !='' && $_SESSION['logic_erp']['brand_id'] !=0){
				$query_cond.= " and ( a.brand_id in(" . $_SESSION['logic_erp']['brand_id'] . ") or a.brand_id in(0) )";
			}
			else $query_cond.="";
		}
		else $query_cond.=" and a.brand_id=$brand";
	
		$year_cond=" and to_char(a.insert_date,'YYYY')=$insertYear";
		if( $inq_date!="" )  $inquery_date.= " and b.pub_shipment_date='".change_date_format($inq_date,'yyyy-mm-dd',"-",1)."'";
		$sql_cond=''; $inquery_id_cond=''; $request_no='';
		
		if($searchStr==1)
		{
			if(str_replace("'","",$txt_style)!="")  $sql_cond="and a.style_ref_no='".str_replace("'","",$txt_style)."'";
			if (trim($mrrNo)!="")  $inquery_id_cond=" and a.job_no_prefix_num='$mrrNo'  $year_cond";
		}
		else if($searchStr==4 || $searchStr==0)
		{
			if(str_replace("'","",$txt_style)!="")  $sql_cond="and a.style_ref_no like '%".str_replace("'","",$txt_style)."%' ";
			if (trim($mrrNo)!="") $inquery_id_cond=" and a.job_no_prefix_num like '%$mrrNo%' $year_cond";
		}
		else if($searchStr==2)
		{
			if(str_replace("'","",$txt_style)!="")  $sql_cond="and a.style_ref_no like '".str_replace("'","",$txt_style)."%' ";
			if (trim($mrrNo)!="") $inquery_id_cond=" and a.job_no_prefix_num like '$mrrNo%' $year_cond";
		}
		else if($searchStr==3)
		{
			if(str_replace("'","",$txt_style)!="")  $sql_cond="and a.style_ref_no like '%".str_replace("'","",$txt_style)."' ";
			if (trim($mrrNo)!="") $inquery_id_cond=" and a.job_no_prefix_num like '%$mrrNo' $year_cond";
		}
		
		$sql = "SELECT a.id, a.job_no_prefix_num, a.job_no, a.buyer_name, a.season_buyer_wise, a.season_year, a.brand_id, a.company_name, b.pub_shipment_date, a.style_ref_no, extract(year from a.insert_date) as year from wo_po_details_master a, wo_po_break_down b where a.id=b.job_id and a.garments_nature=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id not in (SELECT inquiry_id from consumption_la_costing_mst where status_active=1 and is_deleted=0 and inquiry_id!=0 $company_name group by inquiry_id having count(inquiry_id) >= 3) $companyCond $buyer_name $sql_cond $inquery_id_cond $request_no $req_date $year_cond $query_cond group by a.id, a.job_no_prefix_num, a.job_no, a.buyer_name, a.season_buyer_wise, a.season_year, a.brand_id, a.company_name, b.pub_shipment_date, a.style_ref_no, a.insert_date order by a.id DESC ";

		foreach (sql_select($sql) as $row) {
			$inquery_id_arr[$row[csf('id')]] = $row[csf('id')];
		}
		$la_casting_data=sql_select("SELECT id, inquiry_id, cons_for from CONSUMPTION_LA_COSTING_MST where status_active=1 and is_deleted=0 and cons_for is not null".where_con_using_array($inquery_id_arr,0,'inquiry_id'));

		foreach ($la_casting_data as $row) {
			$cons_for_data[$row[csf('inquiry_id')]][] =$cons_for_arr[$row[csf('cons_for')]];
		}
		
		foreach ($cons_for_data as $inq_id=>$cons_arr) {
			$inquery_cons[$inq_id] = implode(", ", $cons_arr);
		}
		$arr=array(0=>$company_arr,1=>$buyer_arr,2=>$brand_arr,7=>$season_buyer_wise_arr,9=>$row_status,10=>$inquery_cons);
		echo create_list_view("list_view", "Company Name,Buyer Name,Brand,Job ID,Year,M.Style Ref., Ship Date,Season,Season Year,Cons For","120,100,80,70,50,70,120,90,90,40,100","1000","260",0, $sql , "js_set_value", "job_no,id", "", 1, "company_name,buyer_name,brand_id,0,0,0,0,season_buyer_wise,0,id", $arr, "company_name,buyer_name,brand_id,job_no_prefix_num,year,style_ref_no,pub_shipment_date,season_buyer_wise,season_year,id", "",'','0') ;
	}
	?>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="populate_data_from_data")
{
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$season_arr=return_library_array("select id,season_name from lib_buyer_season","id","season_name");
	$brand_arr=return_library_array("select id,brand_name from lib_buyer_brand","id","brand_name");
	$inquery_data = sql_select("SELECT  id, company_id, buyer_id, season_buyer_wise, season_year, brand_id, style_refernce, fabrication, style_description,color from wo_quotation_inquery where id='$data' and entry_form=434 and status_active=1 and is_deleted=0 order by id");

	foreach ($inquery_data as $row) {		
		echo "document.getElementById('inquery_id').value = '".$row[csf("id")]."';\n";
		echo "document.getElementById('txt_style_ref').value = '".$row[csf("style_refernce")]."';\n";
		echo "document.getElementById('txt_buyer_name').value = '".$buyer_arr[$row[csf("buyer_id")]]."';\n";
		echo "document.getElementById('txt_season').value = '".$season_arr[$row[csf("season_buyer_wise")]]."';\n";
		echo "document.getElementById('txt_season_year').value = '".$row[csf("season_year")]."';\n";
		echo "document.getElementById('txt_brand_name').value = '".$brand_arr[$row[csf("brand_id")]]."';\n";
		echo "document.getElementById('txt_fabrication').value = '".$row[csf("fabrication")]."';\n";
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('txt_style_desc').value = '".$row[csf("style_description")]."';\n";
		echo "document.getElementById('txt_boby_wash_color').value = '".$row[csf("color")]."';\n";
	}
}

if($action=="populate_req_data_from_data")
{
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$season_arr=return_library_array("select id,season_name from lib_buyer_season","id","season_name");
	$brand_arr=return_library_array("select id,brand_name from lib_buyer_brand","id","brand_name");
	$inquery_data = sql_select("SELECT  a.requisition_number,a.buyer_name, a.season, a.season_year, a.brand_id, a.company_id, a.season_buyer_wise, a.style_ref_no,a.id, b.fabric_description,b.determination_id from sample_development_mst a,sample_development_fabric_acc b  where a.id=b.sample_mst_id and a.is_deleted=0 and a.entry_form_id=449 and a.requisition_number='$data' order by a.id DESC ");
	$deterArr=array();
	foreach ($inquery_data as $rows) {
		$deterArr['determination_id'].=$rows[csf("determination_id")].",";
	}
	$deter_ids=chop($deterArr['determination_id'],",");
	foreach ($inquery_data as $row) {		
		echo "document.getElementById('inquery_id').value = '".$row[csf("id")]."';\n";
		echo "document.getElementById('txt_style_ref').value = '".$row[csf("style_ref_no")]."';\n";
		echo "document.getElementById('txt_buyer_name').value = '".$buyer_arr[$row[csf("buyer_name")]]."';\n";
		echo "document.getElementById('txt_season').value = '".$season_arr[$row[csf("season_buyer_wise")]]."';\n";
		echo "document.getElementById('txt_season_year').value = '".$row[csf("season_year")]."';\n";
		echo "document.getElementById('txt_brand_name').value = '".$brand_arr[$row[csf("brand_id")]]."';\n";
		echo "document.getElementById('txt_fabrication').value = '".$deter_ids."';\n";
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_id")]."';\n";
		//echo "document.getElementById('txt_style_desc').value = '".$row[csf("fabric_description")]."';\n";
	}
}

if($action=="populate_job_data_from_data")
{
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$season_arr=return_library_array("select id,season_name from lib_buyer_season","id","season_name");
	$brand_arr=return_library_array("select id,brand_name from lib_buyer_brand","id","brand_name");
	$inquery_data = sql_select("SELECT a.id, a.job_no_prefix_num, a.job_no, a.buyer_name, a.season_buyer_wise, a.season_year, a.brand_id, a.company_name, a.style_ref_no, a.style_description from wo_po_details_master a where a.garments_nature=3 and a.status_active=1 and a.is_deleted=0  and a.job_no='$data' order by a.id DESC ");
	
	foreach ($inquery_data as $row) {		
		echo "document.getElementById('inquery_id').value = '".$row[csf("id")]."';\n";
		echo "document.getElementById('txt_style_ref').value = '".$row[csf("style_ref_no")]."';\n";
		echo "document.getElementById('txt_buyer_name').value = '".$buyer_arr[$row[csf("buyer_name")]]."';\n";
		echo "document.getElementById('txt_season').value = '".$season_arr[$row[csf("season_buyer_wise")]]."';\n";
		echo "document.getElementById('txt_season_year').value = '".$row[csf("season_year")]."';\n";
		echo "document.getElementById('txt_brand_name').value = '".$brand_arr[$row[csf("brand_id")]]."';\n";
		//echo "document.getElementById('txt_fabrication').value = '".$deter_ids."';\n";
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_name")]."';\n";
		echo "document.getElementById('txt_style_desc').value = '".$row[csf("style_description")]."';\n";
	}
	exit();
}

if($action == "show_fabrication_list")
{
	extract($_REQUEST);
	$inquery_id=str_replace("'","",$inquery_id);
	$fabrication_id=str_replace("'","",$txt_fabrication);
	$update_id=str_replace("'","",$update_id);
	
	$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");
	$lib_body_part=return_library_array( "select body_part_full_name,id from lib_body_part", "id", "body_part_full_name");
	$yarn_count_determina_dtls=sql_select("SELECT mst_id, copmposition_id, percent, count_id, type_id from lib_yarn_count_determina_dtls where is_deleted=0 and mst_id in ($fabrication_id) order by id");			
							
	if (count($yarn_count_determina_dtls)>0)
	{
		foreach( $yarn_count_determina_dtls as $row )
		{
			if(array_key_exists($row[csf('mst_id')],$composition_arr))
			{
				$composition_arr[$row[csf('mst_id')]]=$composition_arr[$row[csf('mst_id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ".$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].",";
			}
			else
			{
				$composition_arr[$row[csf('mst_id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ".$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]];
			}
		}
	}
	if($update_id=='')
	{
		$yarn_count_determina_mst= sql_select("SELECT a.id as yarn_count_id, a.fab_nature_id, a.type, a.construction, a.gsm_weight, a.weight_type, a.design, a.fabric_ref, a.color_range_id, a.rd_no, a.inserted_by, a.status_active, a.full_width ,a.cutable_width, a.shrinkage_l, a.shrinkage_w from  lib_yarn_count_determina_mst a where a.is_deleted=0 and a.id in(".$fabrication_id.") order by a.id ASC");// and a.entry_form=426
		$save_update=0;
	}
	else{
		$yarn_count_determina_mst= sql_select("SELECT a.id, a.fab_nature_id, a.type, a.construction, a.gsm_weight, a.weight_type, a.design, a.fabric_ref, a.color_range_id, a.rd_no, a.full_width ,a.cutable_width, b.id as dtls_id, b.body_part_id, b.effi_per, b.fabric_cons, b.shrinkage_l, b.shrinkage_w, b.nested_pieces, b.bundles, b.yarn_count_id, b.cuttable_width,b.size_ratio,b.remarks from  lib_yarn_count_determina_mst a join consumption_la_costing_dtls b on b.yarn_count_id=a.id where a.is_deleted=0  a.id in(".$fabrication_id.") and b.mst_id=$update_id order by a.id ASC");//and a.entry_form=426 and
		$save_update=1;
	}
	
	$i=1;
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="820" class="rpt_table" id="fabric_dtls_tbl">
    	<thead>
    		<tr>
				<th width="50" rowspan="2">Fab RD No</th>
	    		<th width="50" rowspan="2">Ref No</th>
	    		<th width="350" rowspan="2">Fabric Description</th>
	    		<th width="60" rowspan="2">Fabric Usage</th>
	    		<th width="50" rowspan="2">Full Width</th>
	    		<th width="50" rowspan="2">Cuttable Width</th>
	    		<th width="60" colspan="2">Shrinkage</th>
    		</tr>
    		<tr>
    			<th width="30">L%</th>
    			<th width="30">W%</th>
    		</tr>
    	</thead>
    	<? 

    	if($update_id=='' && str_replace("'","",$cbo_style_source)==1)
		{
			$save_update=0;
    		$fabrication_id_arr = explode(",", $fabrication_id);
    		foreach ($fabrication_id_arr as $fid) { 			
				$yarn_count_determina_mst= sql_select("SELECT a.id as yarn_count_id, a.fab_nature_id, a.type, a.construction, a.gsm_weight, a.weight_type, a.design, a.fabric_ref, a.color_range_id, a.rd_no, a.inserted_by, a.status_active, a.full_width ,a.cutable_width, a.shrinkage_l, a.shrinkage_w from  lib_yarn_count_determina_mst a where a.is_deleted=0 and a.id in(".$fid.") order by a.id ASC");// and a.entry_form=426
			
				foreach ($yarn_count_determina_mst as $row) {
		    		$fabricationData ='';
		    		$fabricationData=$row[csf('type')].', '.$row[csf('construction')].', '.$row[csf('design')].', '.$row[csf('gsm_weight')].', '.$fabric_weight_type[$row[csf('weight_type')]].', '.$color_range[$row[csf('color_range_id')]].', '.$composition_arr[$row[csf('id')]];
			    	?> 	
					<tr id="tr_<?= $i?>" bgcolor="#E9F3FF">
						<td width="50"><input style="width: 50px" class="text_boxes" type="text" id="txtrdno_<?= $i?>" value="<?= $row[csf('rd_no')] ?>" onClick="fnc_load_fabric_dtls(0);change_color_tr('<?= $i?>','#E9F3FF')" readonly></td>
						<td width="50"><input style="width: 50px" class="text_boxes" type="text" id="fabricref_<?= $i?>" value="<?= $row[csf('fabric_ref')] ?>" disabled></td>
						<td title="<?= $fabricationData ?>"><input style="width: 350px" class="text_boxes" type="text" id="fabricdes_<?= $i?>" name="" value="<?= $fabricationData ?>" disabled></td>
						<td width="60">
							<input style="width: 60px" class="text_boxes" type="text" id="fabricusage_<?= $i?>" onDblClick="open_body_part_popup(<?= $i; ?>)" readonly placeholder="Browse" value="<?= $lib_body_part[$row[csf('body_part_id')]]?>"  >
							<input type="hidden" id="fabricusageid_<?= $i?>" value="<?= $row[csf('body_part_id')] ?>">
						</td>
						<td width="50"><input style="width: 50px" class="text_boxes" type="text" id="txtfullwidth_<?= $i?>" placeholder="Write" value="<?= $row[csf('full_width')] ?>" disabled></td>
						<td width="50"><input style="width: 50px" class="text_boxes" type="text" id="txtcuttablewidth_<?= $i?>" placeholder="Write" value="<?= $row[csf('cutable_width')] ?>" disabled>
							<input type="hidden" id="updateiddtls_<?= $i?>" value="<?= $row[csf('dtls_id')]?>">
							<input type="hidden" id="yarncountid_<?= $i?>" value="<?= $row[csf('yarn_count_id')]?>">
						</td>
						<td width="30"><input style="width: 30px" class="text_boxes" type="text" id="txtshrinkagel_<?= $i?>" value="<?= $row[csf('shrinkage_l')] ?>" disabled></td>
						<td width="30"><input style="width: 30px" class="text_boxes" type="text" id="txtshrinkagew_<?= $i?>" placeholder="Write" value="<?= $row[csf('shrinkage_w')] ?>" disabled></td>
					</tr>
					<? 
				}
				$i++;
			}
		}
		else if($update_id=='' && str_replace("'","",$cbo_style_source)==3)
		{
			$save_update=0;
			?> 	
			<tr id="tr_1" bgcolor="#E9F3FF">
				<td width="50"><input style="width: 50px" class="text_boxes" type="text" id="txtrdno_1" value="" onClick="fnc_fabric_decription_popup(1); change_color_tr('1','#E9F3FF')" readonly></td>
				<td width="50"><input style="width: 50px" class="text_boxes" type="text" id="fabricref_1" value="" disabled></td>
				<td title=""><input style="width: 350px" class="text_boxes" type="text" id="fabricdes_1" name="" value="" disabled></td>
				<td width="60">
					<input style="width: 60px" class="text_boxes" type="text" id="fabricusage_1" onDblClick="open_body_part_popup(1);" readonly placeholder="Browse" value="" >
					<input type="hidden" id="fabricusageid_1" value="">
				</td>
				<td width="50"><input style="width: 50px" class="text_boxes" type="text" id="txtfullwidth_1" placeholder="Write" value="" disabled></td>
				<td width="50"><input style="width: 50px" class="text_boxes" type="text" id="txtcuttablewidth_1" placeholder="Write" value="" disabled>
					<input type="hidden" id="updateiddtls_1" value="">
					<input type="hidden" id="yarncountid_1" value="">
				</td>
				<td width="30"><input style="width: 30px" class="text_boxes" type="text" id="txtshrinkagel_1" value="" disabled></td>
				<td width="30"><input style="width: 30px" class="text_boxes" type="text" id="txtshrinkagew_1" placeholder="Write" value="" disabled></td>
			</tr>
			<? 
		}
		else if($update_id=='' && str_replace("'","",$cbo_style_source)==2)
		{
			$yarn_count_determina_mst= sql_select("SELECT a.id as yarn_count_id, a.fab_nature_id, a.type, a.construction, a.gsm_weight, a.weight_type, a.design, a.fabric_ref, a.color_range_id, a.rd_no, a.full_width ,a.cutable_width, a.shrinkage_l, a.shrinkage_w from  lib_yarn_count_determina_mst a  where a.is_deleted=0 and a.id in(".$fabrication_id.") order by a.id ASC");
			$save_update=0;
			foreach ($yarn_count_determina_mst as $row) {
	    		$fabricationData ='';
	    		$fabricationData=$row[csf('type')].', '.$row[csf('construction')].', '.$row[csf('design')].', '.$row[csf('gsm_weight')].', '.$fabric_weight_type[$row[csf('weight_type')]].', '.$color_range[$row[csf('color_range_id')]].', '.$composition_arr[$row[csf('yarn_count_id')]];
		    	?> 	
				<tr id="tr_<?= $i?>" bgcolor="#E9F3FF">
					<td width="50"><input style="width: 50px" class="text_boxes" type="text" id="txtrdno_<?= $i?>" value="<?= $row[csf('rd_no')] ?>"onClick="fnc_load_fabric_dtls(0); change_color_tr('<?= $i?>','#E9F3FF')" readonly></td>
					<td width="50"><input style="width: 50px" class="text_boxes" type="text" id="fabricref_<?= $i?>" value="<?= $row[csf('fabric_ref')] ?>" disabled></td>
					<td title="<?= $fabricationData ?>" ><input style="width: 350px" class="text_boxes" type="text" id="fabricdes_<?= $i?>" value="<?= $fabricationData ?>" disabled></td>
					<td width="60">
					<input style="width: 60px" class="text_boxes" type="text" id="fabricusage_<?= $i?>" onDblClick="open_body_part_popup(<?= $i; ?>)" readonly placeholder="Browse" value=""  >
					<input type="hidden" id="fabricusageid_<?= $i?>" value="">
				</td>
					<td width="50"><input style="width: 50px" class="text_boxes" type="text" id="txtfullwidth_<?= $i?>" value="<?= $row[csf('full_width')] ?>" disabled></td>
					<td width="50"><input style="width: 50px" class="text_boxes" type="text" id="txtcuttablewidth_<?= $i?>" value="<?= $row[csf('cutable_width')] ?>" disabled>
						<input type="hidden" id="updateiddtls_<?= $i?>" value="">
						<input type="hidden" id="yarncountid_<?= $i?>" value="<?= $row[csf('yarn_count_id')]?>">
					</td>
					<td width="30"><input style="width: 30px" class="text_boxes" type="text" id="txtshrinkagel_<?= $i?>" value="<?= $row[csf('shrinkage_l')] ?>" disabled></td>
					<td width="30"><input style="width: 30px" class="text_boxes" type="text" id="txtshrinkagew_<?= $i?>" placeholder="Write" value="<?= $row[csf('shrinkage_w')] ?>" disabled></td>				
				</tr>
				<? $i++;
			}
		}
		else{
			$yarn_count_determina_mst= sql_select("SELECT a.id, a.fab_nature_id, a.type, a.construction, a.gsm_weight, a.weight_type, a.design, a.fabric_ref, a.color_range_id, a.rd_no, a.full_width ,a.cutable_width, a.shrinkage_l, a.shrinkage_w, b.id as dtls_id, b.body_part_id, b.effi_per, b.fabric_cons, b.nested_pieces, b.bundles, b.yarn_count_id, b.cuttable_width,b.size_ratio,b.remarks from  lib_yarn_count_determina_mst a join consumption_la_costing_dtls b on b.yarn_count_id=a.id where a.is_deleted=0 and a.entry_form=426 and a.id in(".$fabrication_id.") and b.mst_id=$update_id order by b.id ASC");
			$save_update=1;
			foreach ($yarn_count_determina_mst as $row) {
	    		$fabricationData ='';
	    		$fabricationData=$row[csf('type')].', '.$row[csf('construction')].', '.$row[csf('design')].', '.$row[csf('gsm_weight')].', '.$fabric_weight_type[$row[csf('weight_type')]].', '.$color_range[$row[csf('color_range_id')]].', '.$composition_arr[$row[csf('id')]];
		    	?> 	
				<tr id="tr_<?= $row[csf('dtls_id')] ?>" bgcolor="#E9F3FF">
					<td width="50"><input style="width: 50px" class="text_boxes" type="text" id="txtrdno_<?= $i?>" value="<?= $row[csf('rd_no')] ?>" onClick="fnc_load_fabric_dtls(<?= $row[csf('dtls_id')] ?>); change_color_tr('<?= $row[csf('dtls_id')] ?>','#E9F3FF')" readonly></td>
					<td width="50"><input style="width: 50px" class="text_boxes" type="text" id="fabricref_<?= $i?>" value="<?= $row[csf('fabric_ref')] ?>" disabled></td>
					<td title="<?= $fabricationData ?>" ><input style="width: 350px" class="text_boxes" type="text" id="fabricdes_<?= $i?>" value="<?= $fabricationData ?>" disabled></td>
					<td width="60">
						<input style="width: 60px" class="text_boxes" type="text" id="fabricusage_<?= $i?>" onDblClick="open_body_part_popup(<?= $i; ?>)" readonly placeholder="Browse" value="<?= $lib_body_part[$row[csf('body_part_id')]]?>"  >
						<input type="hidden" id="fabricusageid_<?= $i?>" value="<?= $row[csf('body_part_id')] ?>">
					</td>
					<td width="50"><input style="width: 50px" class="text_boxes" type="text" id="txtfullwidth_<?= $i?>" value="<?= $row[csf('full_width')] ?>" disabled></td>
					<td width="50"><input style="width: 50px" class="text_boxes" type="text" id="txtcuttablewidth_<?= $i?>" value="<?= $row[csf('cutable_width')] ?>" disabled>
						<input type="hidden" id="updateiddtls_<?= $i?>" value="<?= $row[csf('dtls_id')]?>">
						<input type="hidden" id="yarncountid_<?= $i?>" value="<?= $row[csf('yarn_count_id')]?>">
					</td>
					<td width="30"><input style="width: 30px" class="text_boxes" type="text" id="txtshrinkagel_<?= $i?>" value="<?= $row[csf('shrinkage_l')] ?>" disabled></td>
					<td width="30"><input style="width: 30px" class="text_boxes" type="text" id="txtshrinkagew_<?= $i?>" placeholder="Write" value="<?= $row[csf('shrinkage_w')] ?>" disabled></td>				
				</tr>
				<? $i++;
			}
			
		}
		?>
	</table>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="820" class="rpt_table">       
        <tr>
            <td align="center" valign="middle" style="max-height:820px; min-height:15px;" id="size_color_breakdown11">
                <? echo load_submit_buttons( $permission, "fnc_consumption_entry", $save_update,0 ,"reset_form('consumption_form','','')",1); ?>                        
                <input class="formbutton" type="button" onClick="fnSendMail('../../','',1,0,0,1,0)" value="Mail Send" style="width:80px;">
                <input class="formbutton" type="button" onClick="generate_report()" value="Print" style="width:80px;">
        	</td>
       </tr>
    </table>
	<?
	exit();
}

if ($action=="remarks_popup")
{
	echo load_html_head_contents("Remarks","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=$data;
	?>
    <script>
		$( document ).ready(function() {
			document.getElementById("description").value='<? echo $data; ?>';
		});
    </script>
    <body>
		<div align="center" style="width:100%;">
        <? echo load_freeze_divs ("../../",$permission,1); ?>
        <form>
			<fieldset style="width:450px;">
            <table align="center" cellspacing="0" width="450" class="rpt_table" border="1" rules="all" id="size_tbl" >
                <tr id="row_1">
                    <td width="150" align="center" >
                    	<textarea name="description" id="description" class="text_area" style="width:430px; height:100px;" maxlength="500" title="Maximum 500 Character"></textarea>
                    </td>
                </tr>
            </table>

            <table align="center" cellspacing="0" width="450" class="rpt_table" border="1" rules="all" id="" >
                <tr>
                    <td align="center">
                    	<input type="hidden" name="hidden_appv_cause" id="hidden_appv_cause" class="text_boxes /">
                        <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="parent.emailwindow.hide();" style="width:100px" />
                    </td>
                </tr>
            </table>
            </fieldset>
            </form>
        </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    <script>
		$("#description" ).focus();
	</script>
    </html>
    <?
	exit();
}

if ($action=="load_drop_down_buyer_popup"){
	if($data != 0)
	{
		echo create_drop_down( "cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 $buyer_cond and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "load_drop_down( 'consumption_la_costing_controller', this.value, 'load_drop_down_season_buyer', 'season_td');load_drop_down( 'consumption_la_costing_controller', this.value, 'load_drop_down_brand', 'brand_td');" );
		exit();
	}
	else{
		echo create_drop_down( "cbo_buyer_name", 120, "select distinct buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "load_drop_down( 'consumption_la_costing_controller', this.value, 'load_drop_down_season_buyer', 'season_td');load_drop_down( 'consumption_la_costing_controller', this.value, 'load_drop_down_brand', 'brand_td');" );
		exit();
	}
}

if($action=="body_part_popup")
{
	echo load_html_head_contents("Item Group Select","../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	?>
	<script>
	function js_set_value(id, name,type)
	{
		document.getElementById('gid').value=id;
		document.getElementById('gname').value=name;
		document.getElementById('gtype').value=type;
		parent.emailwindow.hide();
	}
	</script>
	</head>
	<body>
        <div align="center" style="width:100%;">
        <input type="hidden" id="gid" name="gid"/>
        <input type="hidden" id="gname" name="gname"/>
        <input type="hidden" id="gtype" name="gtype"/>
        <?
        $sql_tgroup=sql_select( "select body_part_full_name,body_part_short_name,body_part_type,id from lib_body_part where  is_deleted=0  and  status_active=1 order by body_part_short_name");
        ?>
        <table width="420" cellspacing="0" class="rpt_table" border="0" rules="all">
            <thead>
            <th width="40">SL</th><th width="300">Item Group</th><th>Type</th>
            </thead>
        </table>
        <table width="420" cellspacing="0" class="rpt_table" border="0" rules="all" id="item_table">
            <tbody>
            <?
            $i=1;
            foreach($sql_tgroup as $row_tgroup)
			{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
				<tr onClick="js_set_value(<? echo $row_tgroup[csf('id')]; ?>, '<? echo $row_tgroup[csf('body_part_full_name')]; ?>', '<? echo $row_tgroup[csf('body_part_type')]; ?>')" bgcolor="<? echo $bgcolor; ?>" style="cursor: pointer;"  >
					<td width="40"><? echo $i; ?></td><td width="300"><? echo $row_tgroup[csf('body_part_full_name')]; ?></td><td width=""><? echo $body_part_type[$row_tgroup[csf('body_part_type')]]; ?></td>
				</tr>
				<?
				$i++;
            }
            ?>
            </tbody>
        </table>
        </div>
	</body>
	<script>
	setFilterGrid('item_table',-1)
	</script>
	</html>
	<?
	exit();
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	if($operation==0){
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		if($db_type==0)
		{
			$new_booking_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'CAD', date("Y",time()), 5, "select system_no_prefix, system_no_prefix_num from consumption_la_costing_mst where company_id=$cbo_company_name and entry_form=653 and YEAR(insert_date)=".date('Y',time())." order by id desc ", "system_no_prefix", "system_no_prefix_num" ));
		}
		else if($db_type==2)
		{
			$new_booking_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'CAD', date("Y",time()), 5, "select system_no_prefix, system_no_prefix_num from consumption_la_costing_mst where company_id=$cbo_company_name and entry_form=653 and to_char(insert_date,'YYYY')=".date('Y',time())." order by id desc ", "system_no_prefix", "system_no_prefix_num" ));
		}
		$id=return_next_id( "id", "consumption_la_costing_mst", 1 );
		$field_array="id, system_no_prefix, system_no_prefix_num, system_no, la_costing_date, inquiry_id, company_id, fabrication_id, merch_style, comments, style_des, pattern_master, bom_no, mclastmod_date, cons_for, search_by, entry_form, status_active, is_deleted, inserted_by, insert_date";
		$data_array ="(".$id.",'".$new_booking_no[1]."',".$new_booking_no[2].",'".$new_booking_no[0]."',".$txt_consumption_date.",".$inquery_id.",".$cbo_company_name.",".$txt_fabrication.",".$txt_merch_style.",".$txt_comments.",".$txt_style_desc.",".$txt_pattern_master.",".$txt_bom_no.",".$txt_mclastmod_date.",".$cbo_cons_for.",".$cbo_style_source.",653,1,0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		//echo "10** insert into consumption_la_costing_mst (".$field_array.") values ".$data_array; die;
		$rID=sql_insert("consumption_la_costing_mst",$field_array,$data_array,0);

		/*Dtls Part*/
		$id_dtls=return_next_id( "id", "consumption_la_costing_dtls", 1 );
		$field_array1="id,mst_id,body_part_id,yarn_count_id,status_active,is_deleted,inserted_by,insert_date";
		for ($i=1;$i<=$total_row;$i++){
			$fabricusageid="fabricusageid_".$i;			
			$yarncountid="yarncountid_".$i;
			if ($i!=1) $data_array1 .=",";
			$data_array1 .="(".$id_dtls.",".$id.",".$$fabricusageid.",".$$yarncountid.",1,0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			$id_dtls=$id_dtls+1;
		}
		//echo "10** insert into consumption_la_costing_dtls (".$field_array1.") values ".$data_array1; die;
		$rID1=sql_insert("consumption_la_costing_dtls",$field_array1,$data_array1,0);

		if($db_type==0){
			if($rID && $rID1){
				mysql_query("COMMIT");
				echo "0**".$new_booking_no[0].'**'.$id;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$new_booking_no[0].'**'.$id;
			}
		}
		if($db_type==2 || $db_type==1 ){
			if($rID && $rID1){
				oci_commit($con);
				echo "0**".$new_booking_no[0].'**'.$id;
			}
			else{
				oci_rollback($con);
				echo "10**".$new_booking_no[0].'**'.$id;
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
		$update_id=str_replace("'","",$update_id);
		$system_no=return_library_array( "select system_no,id from consumption_la_costing_mst", "id", "system_no");
		$field_array="la_costing_date*merch_style*comments*style_des*pattern_master*bom_no*mclastmod_date*cons_for*updated_by*update_date";
		$data_array ="".$txt_consumption_date."*".$txt_merch_style."*".$txt_comments."*".$txt_style_desc."*".$txt_pattern_master."*".$txt_bom_no."*".$txt_mclastmod_date."*".$cbo_cons_for."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		$rID=sql_update("consumption_la_costing_mst",$field_array,$data_array,"id","".$update_id."",0);

		$field_array1="body_part_id*updated_by*update_date";
		for ($i=1;$i<=$total_row;$i++){
			$fabricusageid="fabricusageid_".$i;
			$updateiddtls="updateiddtls_".$i;
			$data_array1[str_replace("'",'',$$updateiddtls)] =explode("*",("".$$fabricusageid."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			$id_arr[]=str_replace("'",'',$$updateiddtls);
		}
		//echo "10**".bulk_update_sql_statement( "consumption_la_costing_dtls", "id", $field_array1, $data_array1, $id_arr ); die;
		$rID1=execute_query(bulk_update_sql_statement( "consumption_la_costing_dtls", "id", $field_array1, $data_array1, $id_arr ));
		//echo "10**".$rID.'--'.$rID1; die;
		if($db_type==0){
			if($rID && $rID1){
				mysql_query("COMMIT");
				echo "1**".$system_no[$update_id].'**'.$update_id;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$system_no[$update_id].'**'.$update_id;
			}
		}
		if($db_type==2 || $db_type==1 ){
			if($rID && $rID1){
				oci_commit($con);
				echo "1**".$system_no[$update_id].'**'.$update_id;
			}
			else{
				oci_rollback($con);
				echo "10**".$system_no[$update_id].'**'.$update_id;
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation ==2)
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$system_no=return_library_array( "select system_no,id from consumption_la_costing_mst", "id", "system_no");
		$update_id=str_replace("'","",$update_id);
		$field_array="status_active*is_deleted*updated_by*update_date";
		$data_array ="0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		$rID=sql_update("consumption_la_costing_mst",$field_array,$data_array,"id","".$update_id."",0);
		$rID1=sql_update("consumption_la_costing_dtls",$field_array,$data_array,"mst_id","".$update_id."",0);
		if($db_type==0){
			if($rID && $rID1){
				mysql_query("COMMIT");
				echo "2**".$system_no[$update_id].'**'.$update_id;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$system_no[$update_id].'**'.$update_id;
			}
		}
		if($db_type==2 || $db_type==1 ){
			if($rID && $rID1){
				oci_commit($con);
				echo "2**".$system_no[$update_id].'**'.$update_id;
			}
			else{
				oci_rollback($con);
				echo "10**".$system_no[$update_id].'**'.$update_id;
			}
		}
		disconnect($con);
		die;
	}
}

if($action == 'save_update_delete_fabric_dtls')
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$fabric_id= str_replace("'","",$fabric_dtls_id);
	if($operation==0){
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$id=return_next_id( "id", "consumption_la_fabric_dtls", 1 );
		$field_array="id, dtls_id, full_width, cuttable_width, effi_per, size_ratio, bundles_qty, bundles_cons, fabric_cons, wastageper, final_cons, shrinkage_l, shrinkage_w, nested_pieces, remarks, status_active, is_deleted, inserted_by, insert_date";
		for ($i=1;$i<=$total_row;$i++){
			$fabricfullwidth="fabricfullwidth_".$i;			
			$fabriccutablewidth="fabriccutablewidth_".$i;
			$txteffiper="txteffiper_".$i;
			$txtsizeratio="txtsizeratio_".$i;
			$txtbundleqty="txtbundleqty_".$i;
			$txtbundleconsyds="txtbundleconsyds_".$i;
			$txtconsydsdzn="txtconsydsdzn_".$i;
			$txtwastageper="txtwastageper_".$i;
			$txtfinalcons="txtfinalcons_".$i;
			
			$shrinkagelength="shrinkagelength_".$i;
			$shrinkagewidth="shrinkagewidth_".$i;
			$nestedpieces="nestedpieces_".$i;
			$txtremarks="txtremarks_".$i;
			if ($i!=1) $data_array .=",";
			$data_array .="(".$id.",".$fabric_id.",".$$fabricfullwidth.",".$$fabriccutablewidth.",".$$txteffiper.",".$$txtsizeratio.",".$$txtbundleqty.",".$$txtbundleconsyds.",".$$txtconsydsdzn.",".$$txtwastageper.",".$$txtfinalcons.",".$$shrinkagelength.",".$$shrinkagewidth.",".$$nestedpieces.",".$$txtremarks.",1,0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			$id=$id+1;
		}
		//echo "10** insert into consumption_la_fabric_dtls (".$field_array.") values ".$data_array; die;
		$rID=sql_insert("consumption_la_fabric_dtls",$field_array,$data_array,0);

		if($db_type==0){
			if($rID){
				mysql_query("COMMIT");
				echo "0**".$fabric_id;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$fabric_id;
			}
		}
		if($db_type==2 || $db_type==1 ){
			if($rID){
				oci_commit($con);
				echo "0**".$fabric_id;
			}
			else{
				oci_rollback($con);
				echo "10**".$fabric_id;
			}
		}
		disconnect($con);
		die;
	}
	else if($operation==1)
	{
		$con = connect();
 		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$field_array_up="full_width*cuttable_width*effi_per*size_ratio*bundles_qty*bundles_cons*fabric_cons*wastageper*final_cons*shrinkage_l*shrinkage_w*nested_pieces*remarks*status_active*is_deleted*updated_by*update_date";
		$field_array="id, dtls_id, full_width, cuttable_width, effi_per, size_ratio, bundles_qty, bundles_cons, fabric_cons, wastageper, final_cons, shrinkage_l, shrinkage_w, nested_pieces, remarks, status_active, is_deleted, inserted_by, insert_date";
		$id=return_next_id( "id","consumption_la_fabric_dtls", 1 );
		$previous_fabric_sql="SELECT id as ID from consumption_la_fabric_dtls where dtls_id='".$fabric_id."' and status_active=1 and is_deleted=0";
		foreach(sql_select( $previous_fabric_sql) as $vals)
		{
			$previous_fabric_arr[$vals['ID']]=$vals[$vals['ID']];
		}
		for ($i=1;$i<=$total_row;$i++){
			$fabricfullwidth="fabricfullwidth_".$i;			
			$fabriccutablewidth="fabriccutablewidth_".$i;
			$txteffiper="txteffiper_".$i;
			$txtsizeratio="txtsizeratio_".$i;
			$txtbundleqty="txtbundleqty_".$i;
			$txtbundleconsyds="txtbundleconsyds_".$i;
			$txtconsydsdzn="txtconsydsdzn_".$i;
			$txtwastageper="txtwastageper_".$i;
			$txtfinalcons="txtfinalcons_".$i;
			$shrinkagelength="shrinkagelength_".$i;
			$shrinkagewidth="shrinkagewidth_".$i;
			$nestedpieces="nestedpieces_".$i;
			$txtremarks="txtremarks_".$i;
			$updatefabricdtlsid="updatefabricdtlsid_".$i;
			if(str_replace("'",'',$$updatefabricdtlsid)!="")
			{
				if( in_array( str_replace("'",'',$$updatefabricdtlsid) ,$previous_fabric_arr))
				{
					unset($previous_fabric_arr[str_replace("'",'',$$updatefabricdtlsid)]);
				}
                $id_arr[]=str_replace("'",'',$$updatefabricdtlsid);
				$data_array_up[str_replace("'",'',$$updatefabricdtlsid)] =explode("*",("".$$fabricfullwidth."*".$$fabriccutablewidth."*".$$txteffiper."*".$$txtsizeratio."*".$$txtbundleqty."*".$$txtbundleconsyds."*".$$txtconsydsdzn."*".$$txtwastageper."*".$$txtfinalcons."*".$$shrinkagelength."*".$$shrinkagewidth."*".$$nestedpieces."*".$$txtremarks."*1*0*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			}
			if(str_replace("'",'',$$updatefabricdtlsid)=="")
			{
				if ($i!=1) $data_array .=",";
				$data_array .="(".$id.",".$fabric_id.",".$$fabricfullwidth.",".$$fabriccutablewidth.",".$$txteffiper.",".$$txtsizeratio.",".$$txtbundleqty.",".$$txtbundleconsyds.",".$$txtconsydsdzn.",".$$txtwastageper.",".$$txtfinalcons.",".$$shrinkagelength.",".$$shrinkagewidth.",".$$nestedpieces.",".$$txtremarks.",1,0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				$id=$id+1;
			}			
		}
		$previous_fabric_ids=implode(",", $previous_fabric_arr);
		$delete_fabric_dtls=execute_query("UPDATE consumption_la_fabric_dtls set status_active=0 , is_deleted=1, updated_by=".$_SESSION['logic_erp']['user_id']." , update_date='".$pc_date_time."' where id in( $previous_fabric_ids)");
		//echo "10**".bulk_update_sql_statement( "consumption_la_fabric_dtls", "id", $field_array_up, $data_array_up, $id_arr ); die;
		$rID_up=execute_query(bulk_update_sql_statement( "consumption_la_fabric_dtls", "id", $field_array_up, $data_array_up, $id_arr ));
		if($data_array !="")
		{
			$rID=sql_insert("consumption_la_fabric_dtls",$field_array,$data_array,0);
		}
		if($db_type==0){
			if($rID_up){
				mysql_query("COMMIT");
				echo "0**".$fabric_id;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$fabric_id;
			}
		}
		if($db_type==2 || $db_type==1 ){
			if($rID_up){
				oci_commit($con);
				echo "0**".$fabric_id;
			}
			else{
				oci_rollback($con);
				echo "10**".$fabric_id;
			}
		}
		disconnect($con);
		die;
	}
	else if($operation==2)
	{
		$con = connect();
 		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$field_array="status_active*is_deleted*updated_by*update_date";
		$data_array ="0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		$rID=sql_update("consumption_la_fabric_dtls",$field_array,$data_array,"dtls_id","".$fabric_id."",0);
		if($db_type==0){
			if($rID){
				mysql_query("COMMIT");
				echo "0**".$fabric_id;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$fabric_id;
			}
		}
		if($db_type==2 || $db_type==1 ){
			if($rID){
				oci_commit($con);
				echo "0**".$fabric_id;
			}
			else{
				oci_rollback($con);
				echo "10**".$fabric_id;
			}
		}
		disconnect($con);
		die;

	}
}

if($action == "consumption_report")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$update_id = str_replace("'","",$update_id);
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$season_arr=return_library_array("select id,season_name from lib_buyer_season","id","season_name");
	$brand_arr=return_library_array("select id,brand_name from lib_buyer_brand","id","brand_name");
	$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");
	$lib_body_part=return_library_array( "select body_part_full_name,id from lib_body_part", "id", "body_part_full_name");
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$system_data = sql_select("SELECT  a.id as system_id, a.system_no, a.inquiry_id, a.la_costing_date, a.merch_style, a.style_des, a.pattern_master, a.bom_no, a.comments, a.cons_for, a.fabrication_id, a.mclastmod_date , b.company_id, b.buyer_id, b.season_buyer_wise, b.season_year, b.brand_id, b.style_refernce, b.fabrication, b.color from consumption_la_costing_mst a join wo_quotation_inquery b on a.inquiry_id=b.id where a.id='$update_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id");
	$master_attribute = array('system_no', 'inquiry_id', 'la_costing_date', 'merch_style', 'style_des', 'pattern_master', 'bom_no', 'comments', 'company_id', 'buyer_id', 'season_buyer_wise', 'season_year', 'brand_id', 'style_refernce','fabrication_id','mclastmod_date','color','cons_for');
	foreach ($system_data as $row) {
		foreach ($master_attribute as $attr) {
			$$attr = $row[csf($attr)];
		}
	}
	$yarn_count_determina_dtls=sql_select("SELECT mst_id, copmposition_id, percent, count_id, type_id from lib_yarn_count_determina_dtls where is_deleted=0 and mst_id in ($fabrication_id) order by id");												
	if (count($yarn_count_determina_dtls)>0)
	{
		foreach( $yarn_count_determina_dtls as $row )
		{
			if(array_key_exists($row[csf('mst_id')],$composition_arr))
			{
				$composition_arr[$row[csf('mst_id')]]=$composition_arr[$row[csf('mst_id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ".$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].",";
			}
			else
			{
				$composition_arr[$row[csf('mst_id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ".$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]];
			}
		}
	}

	$yarn_count_and_costing_data= sql_select("SELECT a.id, a.fab_nature_id, a.type, a.construction, a.gsm_weight, a.weight_type, a.design, a.fabric_ref, a.color_range_id, a.rd_no, a.full_width as fabric_fullwidth, a.cutable_width as fabric_cutwidth, a.shrinkage_l as yarnsl, a.shrinkage_w as yarnsw, b.id as dtls_id, b.body_part_id, c.id as fabric_dtls_id, c.effi_per, c.fabric_cons, c.shrinkage_l, c.shrinkage_w, c.nested_pieces, c.bundles_qty, c.bundles_cons, c.full_width, c.cuttable_width, c.size_ratio, c.wastageper, c.final_cons, c.remarks from lib_yarn_count_determina_mst a join consumption_la_costing_dtls b on b.yarn_count_id=a.id join consumption_la_fabric_dtls c on b.id=c.dtls_id where a.is_deleted=0 and a.status_active=1 and a.entry_form=426 and a.id in(".$fabrication_id.") and b.mst_id=$update_id and c.status_active=1 and c.is_deleted=0 order by b.id ASC");
	$fabricationData ='';    		
	foreach ($yarn_count_and_costing_data as $row) {
		$fabricationData=$row[csf('type')].', '.$row[csf('construction')].', '.$row[csf('design')].', '.$row[csf('gsm_weight')].', '.$fabric_weight_type[$row[csf('weight_type')]].', '.$color_range[$row[csf('color_range_id')]].', '.$composition_arr[$row[csf('id')]];
		$fabric_attribute = array('rd_no','fabric_ref','body_part_id','fabric_fullwidth','fabric_cutwidth','yarnsl','yarnsw');
		foreach ($fabric_attribute as $f_attr) {
			$la_costing_date_arr[$row[csf('dtls_id')]][$f_attr] = $row[csf($f_attr)];
		}
		$la_costing_date_arr[$row[csf('dtls_id')]]['description'] = $fabricationData;
		
		$fabric_dtls_attribute= array('effi_per', 'fabric_cons', 'shrinkage_l', 'shrinkage_w', 'nested_pieces', 'bundles_qty','bundles_cons', 'full_width', 'cuttable_width', 'size_ratio', 'remarks', 'wastageper', 'final_cons');
		foreach ($fabric_dtls_attribute as $fdtls_attr) {
			$la_costing_date_arr[$row[csf('dtls_id')]]['fabric_dtls_data'][$row[csf('fabric_dtls_id')]][$fdtls_attr] = $row[csf($fdtls_attr)];
		}
	}

	$company_des=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$company_id");
	?>

	<table width="930px">
		<tr><th style="text-align: center;"><? echo $company_arr[$company_id] ?></th></tr>
		<tr><th style="text-align: center;">Consumption [CAD] For LA Costing</th></tr>		
	</table>
	<table width="930px">
		<tr>
			<th width="150" align="left">System No.</th>
			<th width="20">:</th>
			<td width="150"><? echo $system_no ?></td>
			<th width="150" align="left">M.Style Ref</th>
			<th width="20">:</th>
			<td width="150"><? echo $style_refernce ?></td>
			<th width="150"align="left">Costing Date</th>
			<th width="20">:</th>
			<td><? echo change_date_format($la_costing_date,'yyyy-mm-dd','-'); ?></td>

		</tr>
		<tr>
			<th align="left">Buyer Name</th>
			<th>:</th>
			<td><? echo $buyer_arr[$buyer_id] ?></td>
			<th align="left">Merch Style</th>
			<th>:</th>
			<td><? echo $merch_style ?></td>
			<th align="left">Mc Last Mod</th>
			<th>:</th>
			<td><? echo change_date_format($mclastmod_date,'yyyy-mm-dd','-'); ?></td>
		</tr>
		<tr>
			<th align="left">Season</th>
			<th>:</th>
			<td><? echo $season_arr[$season_buyer_wise] ?></td>
			<th align="left">Season Year</th>
			<th>:</th>
			<td><? echo $season_year; ?></td>
			<th align="left">Brand</th>
			<th>:</th>
			<td><? echo $brand_arr[$brand_id] ?></td>
		</tr>
		<tr>
			<th align="left">Style Desc.</th>
			<th>:</th>
			<td><? echo $style_des ?></td>
			<th align="left">Pattern Master Name</th>
			<th>:</th>
			<td><? echo $pattern_master; ?></td>
			<th align="left">BOM No</th>
			<th>:</th>
			<td><? echo $bom_no ?></td>
		</tr>
		<tr>
			<th align="left">Body/Wash Color</th>
			<th>:</th>
			<td><? echo $color ?></td>
			<th align="left">Cons For</th>
			<th>:</th>
			<td><? echo $cons_for_arr[$cons_for] ?></td>
		</tr>
		<tr>
			<th align="left">Comments</th>
			<th>:</th>
			<td colspan="7"><? echo $comments ?></td>
		</tr>
	</table>
	<? foreach ($la_costing_date_arr as $data) {
	$k=1;
	$i=1;
	if($i==1){
	?>
	<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="margin-top: 10px" rules="all" width="930px">
	<thead>
		<tr>
			<th width="80" rowspan="2">Fab RD No</th>
			<th width="80" rowspan="2">Ref No</th>
			<th width="400" rowspan="2">Fabric Description</th>
			<th width="80" rowspan="2">Fabric Usage</th>
			<th width="60" rowspan="2">Full Width</th>
			<th width="80" rowspan="2">Cuttable Width</th>
			<th width="40" colspan="2">Shrinkage</th>
		</tr>
		<tr>
			<th width="40">L%</th>
			<th width="40">W%</th>
		</tr>			
	</thead>  	 	
	<tr>
		<td style="word-break:break-all" width="80" align="center"><?= $data['rd_no'] ?></td>
		<td style="word-break:break-all" width="80" align="center"><?= $data['fabric_ref'] ?></td>
		<td style="word-break:break-all" title="<?= $description ?>" width="400" align="left"><?= $data['description'] ?></td>
		<td style="word-break:break-all" width="80" align="left"><?= $lib_body_part[$data['body_part_id']]?></td>
		<td width="60" align="center"><?= $data['fabric_fullwidth'] ?></td>
		<td width="80" align="center"><?= $data['fabric_cutwidth'] ?></td>
		<td width="20" align="center"><?= $data['yarnsl'] ?></td>
		<td width="20" align="center"><?= $data['yarnsw'] ?></td>
	</tr>
	</table>	
	<? } ?>
	<? foreach ($data['fabric_dtls_data'] as $f_data) { 
		if($k==1){ ?>
			<table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="margin-top: 10px" rules="all" width="930px">
			<thead>
				<tr>
					<th width="80" rowspan="2">Full Width</th>
					<th width="80" rowspan="2">Cuttable Width</th>
					<th width="80" rowspan="2">Efficiency %</th>
					<th width="80" rowspan="2">Size Ratio</th>
					<th width="60" rowspan="2">Bundles Qty.</th>
					<th width="60" rowspan="2">Bundles Cons Yds</th>
					<th width="60" rowspan="2">Cons Yds / Dzn</th>
                    <th width="60" rowspan="2">Wastage %</th>
                    <th width="60" rowspan="2">Final Cons [Yds]</th>
					<th width="60" colspan="2">Shrinkage</th>
					<th width="60" rowspan="2">Nested Pieces</th>
					<th rowspan="2">Comments</th>
				</tr>
				<tr>
					<th width="30">L</th>
					<th width="30">W</th>
				</tr>		
			</thead>
		<? } ?>
			<tr>
				<th width="80" style="word-break:break-all"><?= $f_data['full_width'] ?></th>
				<th width="80" style="word-break:break-all"><?= $f_data['cuttable_width'] ?></th>
				<th width="80" style="word-break:break-all"><?= $f_data['effi_per'] ?></th>
				<th width="80" style="word-break:break-all"><?= $f_data['size_ratio'] ?></th>
				<th width="60" style="word-break:break-all"><?= $f_data['bundles_qty'] ?></th>
				<th width="60" style="word-break:break-all"><?= $f_data['bundles_cons'] ?></th>
				<th width="60" style="word-break:break-all"><?= $f_data['fabric_cons'] ?></th>
                <th width="60" style="word-break:break-all"><?= $f_data['wastageper'] ?></th>
                <th width="60" style="word-break:break-all"><?= $f_data['final_cons'] ?></th>
				<th width="30" style="word-break:break-all"><?= $f_data['shrinkage_l'] ?></th>
				<th width="30" style="word-break:break-all"><?= $f_data['shrinkage_w'] ?></th>
				<th width="60" style="word-break:break-all"><?= $f_data['nested_pieces'] ?></th>
				<th style="word-break:break-all"><?= $f_data['remarks'] ?></th>
			</tr>
		<? $k++; }  ?>
			</table>
		<?
		$i++;
	} 
	?>
	</table>
	<?
	echo signature_table(109, $company_id, "930px");
}

if ($action=="load_drop_down_season_buyer")
{
	if ($_SESSION['logic_erp']['buyer_id'] && $_SESSION['logic_erp']["data_level_secured"] == 1 && $data==0) {
		$buser_cond ="buyer_id in(" . $_SESSION['logic_erp']['buyer_id'] . ")";
	} else {
		$buser_cond = "buyer_id='$data'";
	}
	echo create_drop_down( "cbo_season_name", 100, "select id, season_name from lib_buyer_season where $buser_cond and status_active =1 and is_deleted=0 order by season_name ASC","id,season_name", 1, "-Season-", $selected, "" );
	exit();
}
if ($action=="load_drop_down_brand")
{
	if ($_SESSION['logic_erp']['buyer_id'] && $_SESSION['logic_erp']["data_level_secured"] == 1 && $data==0) {
		$buser_cond = "buyer_id in(" . $_SESSION['logic_erp']['buyer_id'] . ")";
	} else {
		$buser_cond = "buyer_id='$data'";
	}
	echo create_drop_down( "cbo_brand", 120, "select id, brand_name from lib_buyer_brand brand where $buser_cond and status_active =1 and is_deleted=0 $brand_cond order by brand_name ASC","id,brand_name", 1, "-Brand-", $selected, "" );
	exit();
}

if($action == "load_php_dtls_form")
{
	$fabric_dtls= sql_select("SELECT id as DTLS_ID, effi_per as EFFI_PER, fabric_cons as FABRIC_CONS, wastageper as WASTAGEPER, final_cons as FINAL_CONS, shrinkage_l as SHRINKAGE_L, shrinkage_w as SHRINKAGE_W, nested_pieces as NESTED_PIECES, bundles_qty as BUNDLES_QTY, bundles_cons as BUNDLES_CONS, full_width as FULL_WIDTH, cuttable_width as CUTTABLE_WIDTH, size_ratio as SIZE_RATIO, remarks as REMARKS from consumption_la_fabric_dtls where status_active=1 and is_deleted=0 and dtls_id=$data");
	if(count($fabric_dtls)>0)
	{
		$save_update=1;
		$i=1;
		foreach ($fabric_dtls as $row) { ?>
			<tr>
                <td><input style="width: 60px" class="text_boxes" type="text" id="fabricfullwidth_<?=$i?>" value="<?= $row['FULL_WIDTH'] ?>"></td>
                <td><input style="width: 60px" class="text_boxes" type="text" id="fabriccutablewidth_<?=$i?>" value="<?= $row['CUTTABLE_WIDTH'] ?>"></td>
                <td><input style="width: 50px" class="text_boxes" type="text" id="txteffiper_<?=$i?>" value="<?= $row['EFFI_PER'] ?>"></td>
                <td><input style="width: 110px" class="text_area" type="text" id="txtsizeratio_<?=$i?>" value="<?= $row['SIZE_RATIO'] ?>"></td>
                <td><input style="width: 40px" class="text_boxes" type="text" id="txtbundleqty_<?=$i?>" onChange="calculate_cons(<?=$i?>)" value="<?= $row['BUNDLES_QTY'] ?>"></td>
                <td><input style="width: 40px" class="text_boxes" type="text" id="txtbundleconsyds_<?=$i?>" onChange="calculate_cons(<?=$i?>)" value="<?= $row['BUNDLES_CONS'] ?>"></td>
                <td><input style="width: 40px" class="text_boxes" type="text" id="txtconsydsdzn_<?=$i?>"  readonly="" value="<?=$row['FABRIC_CONS'] ?>"></td>
                
                <td><input style="width: 40px" class="text_boxes_numeric" type="text" name="txtwastageper_<?=$i?>" id="txtwastageper_<?=$i?>" placeholder="Write" onChange="calculate_cons(<?=$i?>);" value="<?=$row['WASTAGEPER'] ?>" ></td>
                <td><input style="width: 40px" class="text_boxes_numeric" type="text" name="txtfinalcons_<?=$i?>" id="txtfinalcons_<?=$i?>" readonly value="<?=$row['FINAL_CONS'] ?>"></td>
                
                <td><input style="width: 30px" class="text_boxes" type="text" id="shrinkagelength_<?=$i?>" value="<?= $row['SHRINKAGE_L'] ?>"></td>
                <td><input style="width: 30px" class="text_boxes" type="text" id="shrinkagewidth_<?=$i?>" value="<?= $row['SHRINKAGE_W'] ?>"></td>
                <td><input style="width: 30px" class="text_boxes" type="text" id="nestedpieces_<?=$i?>" value="<?= $row['NESTED_PIECES'] ?>"></td>
                <td>
                <input style="width: 140px" class="text_boxes" type="text" onDblClick="remarks_popup(<?=$i?>)" id="txtremarks_<?=$i?>" value="<?= $row['REMARKS'] ?>">
                <input type="hidden" id="updatefabricdtlsid_<?=$i?>" value="<?= $row['DTLS_ID'] ?>">
                </td>
                <td>
					<input type="button" id="increase_<?=$i?>" name="increase_<?=$i?>" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr(<?=$i?>);" />
					<input type="button" id="decrease_<?=$i?>" name="decrease_<?=$i?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<?=$i?>);" />
				</td>
            </tr>
		<? $i++; }
	}
	exit();
}

if($action == "check_same_cons")
{
	extract($_REQUEST);
	$data_arr = explode("**", $data);
	if($data_arr[2] == '')
	{
		$consting_sql=sql_select("SELECT id from consumption_la_costing_mst where inquiry_id=$data_arr[0] and cons_for = $data_arr[1] and status_active=1 and is_deleted=0");
	}
	else{
		$consting_sql=sql_select("SELECT id from consumption_la_costing_mst where inquiry_id=$data_arr[0] and cons_for = $data_arr[1] and status_active=1 and is_deleted=0 and id not in ($data_arr[2])");
	}
	echo count($consting_sql); die;
}



if($action == "auto_mail")
{
	extract($_REQUEST);

	list($update_id,$email,$mail_body)=explode('__',$data);
	include('../../../auto_mail/setting/mail_setting.php');
	
	$update_id = str_replace("'","",$update_id);
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$season_arr=return_library_array("select id,season_name from lib_buyer_season","id","season_name");
	$brand_arr=return_library_array("select id,brand_name from lib_buyer_brand","id","brand_name");
	$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");
	$lib_body_part=return_library_array( "select body_part_full_name,id from lib_body_part", "id", "body_part_full_name");
	$cons_for_arr = array(1=>"Merketing",2=>"Budget",3=>"Production");
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$system_data = sql_select("SELECT  a.id as system_id, a.system_no, a.inquiry_id, a.la_costing_date, a.merch_style, a.style_des, a.pattern_master, a.bom_no, a.comments, a.cons_for, a.fabrication_id, a.mclastmod_date , b.company_id, b.buyer_id, b.season_buyer_wise, b.season_year, b.brand_id, b.style_refernce, b.fabrication, b.color,a.INSERTED_BY from consumption_la_costing_mst a join wo_quotation_inquery b on a.inquiry_id=b.id where a.id='$update_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id");
	
	$master_attribute = array('system_no', 'inquiry_id', 'la_costing_date', 'merch_style', 'style_des', 'pattern_master', 'bom_no', 'comments', 'company_id', 'buyer_id', 'season_buyer_wise', 'season_year', 'brand_id', 'style_refernce','fabrication_id','mclastmod_date','color','cons_for');
	foreach ($system_data as $row) {
		$company_id=$row[csf('company_id')];
		$buyer_id=$row[csf('buyer_id')];
		$brand_id=$row[csf('brand_id')];
		$inquiry_id=$row[csf('inquiry_id')];
		$style_refernce=$row[csf('style_refernce')];
		$fabrication=$row[csf('fabrication')];
		$INSERTED_BY=$row[csf('INSERTED_BY')];
		$cons_for=$row[csf('cons_for')];
		foreach ($master_attribute as $attr) {
			$$attr = $row[csf($attr)];
		}
	}
	
	
	$yarn_count_determina_dtls=sql_select("SELECT mst_id, copmposition_id, percent, count_id, type_id from lib_yarn_count_determina_dtls where is_deleted=0 and mst_id in ($fabrication_id) order by id");												
	if (count($yarn_count_determina_dtls)>0)
	{
		foreach( $yarn_count_determina_dtls as $row )
		{
			if(array_key_exists($row[csf('mst_id')],$composition_arr))
			{
				$composition_arr[$row[csf('mst_id')]]=$composition_arr[$row[csf('mst_id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ".$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].",";
			}
			else
			{
				$composition_arr[$row[csf('mst_id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ".$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]];
			}
		}
	}

	$yarn_count_and_costing_data= sql_select("SELECT a.id, a.fab_nature_id, a.type, a.construction, a.gsm_weight, a.weight_type, a.design, a.fabric_ref, a.color_range_id, a.rd_no, a.full_width as fabric_fullwidth, a.cutable_width as fabric_cutwidth, b.id as dtls_id, b.body_part_id, c.id as fabric_dtls_id, c.effi_per, c.fabric_cons, c.shrinkage_l, c.shrinkage_w, c.nested_pieces, c.bundles_qty, c.bundles_cons, c.full_width, c.cuttable_width, c.size_ratio, c.remarks from lib_yarn_count_determina_mst a join consumption_la_costing_dtls b on b.yarn_count_id=a.id join consumption_la_fabric_dtls c on b.id=c.dtls_id where a.is_deleted=0 and a.status_active=1 and a.entry_form=426 and a.id in(".$fabrication_id.") and b.mst_id=$update_id and c.status_active=1 and c.is_deleted=0 order by b.id ASC");
	$fabricationData ='';    		
	foreach ($yarn_count_and_costing_data as $row) {
		$fabricationData=$row[csf('type')].', '.$row[csf('construction')].', '.$row[csf('design')].', '.$row[csf('gsm_weight')].', '.$fabric_weight_type[$row[csf('weight_type')]].', '.$color_range[$row[csf('color_range_id')]].', '.$composition_arr[$row[csf('id')]];
		$fabric_attribute = array('rd_no','fabric_ref','body_part_id','fabric_fullwidth','fabric_cutwidth');
		foreach ($fabric_attribute as $f_attr) {
			$la_costing_date_arr[$row[csf('dtls_id')]][$f_attr] = $row[csf($f_attr)];
		}
		$la_costing_date_arr[$row[csf('dtls_id')]]['description'] = $fabricationData;
		
		$fabric_dtls_attribute= array('effi_per', 'fabric_cons', 'shrinkage_l', 'shrinkage_w', 'nested_pieces', 'bundles_qty','bundles_cons', 'full_width', 'cuttable_width', 'size_ratio', 'remarks');
		foreach ($fabric_dtls_attribute as $fdtls_attr) {
			$la_costing_date_arr[$row[csf('dtls_id')]]['fabric_dtls_data'][$row[csf('fabric_dtls_id')]][$fdtls_attr] = $row[csf($fdtls_attr)];
		}
	}

	$company_des=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$company_id");
	
	
	//-----------------------	
	 $sql_team_mail="
	SELECT c.CAD_USER_NAME,d.USER_EMAIL, b.TEAM_LEADER_EMAIL  FROM WO_QUOTATION_INQUERY a,  LIB_MARKETING_TEAM b,   LIB_MKT_TEAM_MEMBER_INFO c,  USER_PASSWD d WHERE a.INSERT_BY = c.USER_TAG_ID  AND b.id = c.TEAM_ID   AND c.USER_TAG_ID = d.id  AND a.id = $inquiry_id";
	// echo $sql_team_mail;die;
	$sql_team_mail_result=sql_select($sql_team_mail);
	$toArr=array();
	foreach($sql_team_mail_result as $rows){
		if($rows['USER_EMAIL']){$toArr[$rows['USER_EMAIL']]=$rows['USER_EMAIL'];}
		if($rows['TEAM_LEADER_EMAIL']){$toArr[$rows['TEAM_LEADER_EMAIL']]=$rows['TEAM_LEADER_EMAIL'];}
		$CAD_USER_NAME=$rows['CAD_USER_NAME'];
	}
	
	if($CAD_USER_NAME!=''){$whereCon=" or d.id in(".$CAD_USER_NAME.")";}
	$sql_team_mail="SELECT d.USER_EMAIL from USER_PASSWD d WHERE d.id = $INSERTED_BY  $whereCon";
	//echo $sql_team_mail;die;
	$sql_team_mail_result=sql_select($sql_team_mail);
	foreach($sql_team_mail_result as $rows){
		if($rows['USER_EMAIL']){$toArr[$rows['USER_EMAIL']]=$rows['USER_EMAIL'];}
	}
	
	if($email){$toArr[$email]=$email;}
	
	
	 //print_r($toArr);die;
	
	ob_start();
	   
	?>    
    <table width="100%">
        <tr><th style="text-align: center;"><? echo $company_arr[$company_id] ?></th></tr>
        <tr><th style="text-align: center;">Consumption [CAD] For LA Costing</th></tr>		
    </table>
    <table width="100%">
        <tr>
            <th align="left">System No.</th>
            <th>:</th>
            <td><? echo $system_no ?></td>
            <th align="left">Master Style Ref</th>
            <th>:</th>
            <td><? echo $style_refernce ?></td>
            <th align="left">Costing Date</th>
            <th>:</th>
            <td><? echo change_date_format($la_costing_date,'yyyy-mm-dd','-'); ?></td>

        </tr>
        <tr>
            <th align="left">Buyer Name</th>
            <th>:</th>
            <td><? echo $buyer_arr[$buyer_id] ?></td>
            <th align="left">Merch Style</th>
            <th>:</th>
            <td><? echo $merch_style ?></td>
            <th align="left">Mc Last Mod</th>
            <th>:</th>
            <td><? echo change_date_format($mclastmod_date,'yyyy-mm-dd','-'); ?></td>
        </tr>
        <tr>
            <th align="left">Season</th>
            <th>:</th>
            <td><? echo $season_arr[$season_buyer_wise] ?></td>
            <th align="left">Season Year</th>
            <th>:</th>
            <td><? echo $season_year; ?></td>
            <th align="left">Brand</th>
            <th>:</th>
            <td><? echo $brand_arr[$brand_id] ?></td>
        </tr>
        <tr>
            <th align="left">Style Desc.</th>
            <th>:</th>
            <td><? echo $style_des ?></td>
            <th align="left">Pattern Master Name</th>
            <th>:</th>
            <td><? echo $pattern_master; ?></td>
            <th align="left">BOM No</th>
            <th>:</th>
            <td><? echo $bom_no ?></td>
        </tr>
        <tr>
            <th align="left">Body/Wash Color</th>
            <th>:</th>
            <td><? echo $color ?></td>
            <th align="left">Cons For</th>
            <th>:</th>
            <td><? echo $cons_for_arr[$cons_for] ?></td>
        </tr>
        <tr>
            <th align="left">Comments</th>
            <th>:</th>
            <td colspan="7"><? echo $comments ?></td>
        </tr>
    </table>
    

    
    <? foreach ($la_costing_date_arr as $data) {
    $k=1;
    $i=1;
    if($i==1){
    ?>
    <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="margin-top: 10px" rules="all" width="100%">
    <thead>
        <tr>
            <th width="80">Fab RD No</th>
            <th width="80">Ref No</th>
            <th width="400">Fabric Description</th>
            <th width="80">Fabric Usage</th>
            <th width="60">Full Width</th>
            <th width="80">Cuttable Width</th>
        </tr>			
    </thead>  	 	
    <tr>
        <td width="80" align="center"><?= $data['rd_no'] ?></td>
        <td width="80" align="center"><?= $data['fabric_ref'] ?></td>
        <td title="<?= $description ?>" width="400" align="left"><?= $data['description'] ?></td>
        <td width="80" align="left"><?= $lib_body_part[$data['body_part_id']]?></td>
        <td width="60" align="center"><?= $data['fabric_fullwidth'] ?></td>
        <td width="80" align="center"><?= $data['fabric_cutwidth'] ?></td>
    </tr>
    </table>	
    <? } ?>
    <? foreach ($data['fabric_dtls_data'] as $f_data) { 
        if($k==1){ ?>
            <table class="rpt_table" border="1" cellpadding="1" cellspacing="1" style="margin-top: 10px" rules="all" width="100%">
            <thead>
                <tr>
                    <th width="80" rowspan="2">Full Width</th>
                    <th width="80" rowspan="2">Cuttable Width</th>
                    <th width="80" rowspan="2">Efficiency %</th>
                    <th width="80" rowspan="2">Size Ratio</th>
                    <th width="60" rowspan="2">Bundles Qty.</th>
                    <th width="60" rowspan="2">Bundles Cons Yds</th>
                    <th width="60" rowspan="2">Cons Yds / Dzn</th>
                    <th width="60" colspan="2">Shrinkage</th>
                    <th width="60" rowspan="2">Nested Pieces</th>
                    <th width="200" rowspan="2">Comments</th>
                </tr>
                <tr>
                    <th width="30">L</th>
                    <th width="30">W</th>
                </tr>		
            </thead>
        <? } ?>
            <tr>
                <th width="80"><?= $f_data['full_width'] ?></th>
                <th width="80"><?= $f_data['cuttable_width'] ?></th>
                <th width="80"><?= $f_data['effi_per'] ?></th>
                <th width="80"><?= $f_data['size_ratio'] ?></th>
                <th width="60"><?= $f_data['bundles_qty'] ?></th>
                <th width="60"><?= $f_data['bundles_cons'] ?></th>
                <th width="60"><?= $f_data['fabric_cons'] ?></th>
                <th width="30"><?= $f_data['shrinkage_l'] ?></th>
                <th width="30"><?= $f_data['shrinkage_w'] ?></th>
                <th width="60"><?= $f_data['nested_pieces'] ?></th>
                <th width="200"><?= $f_data['remarks'] ?></th>
            </tr>
        <? $k++; }  ?>
            </table>
        <?
        $i++;
    } 
    ?>
    </table>
    <?
    echo signature_table(109, $company_id, "850px");

    $message=ob_get_contents();
    ob_clean();

    
    
//Att file............................................
    $imgSql="select FILE_TYPE,IMAGE_LOCATION,REAL_FILE_NAME, MASTER_TBLE_ID, FORM_NAME from common_photo_library where form_name in('cad_entry') and is_deleted=0  ".where_con_using_array(array($update_id),1,'MASTER_TBLE_ID')."";
    // echo $imgSql;die;
    $imgSqlResult=sql_select($imgSql);
    foreach($imgSqlResult as $rows){
        $att_file_arr[]='../../'.$rows['IMAGE_LOCATION'].'**'.$rows['REAL_FILE_NAME'];
    }
    
    //.......................................end;
    
    
    
    
    $to='';
    $sql = "SELECT a.BRAND_IDS,a.BUYER_IDS,c.EMAIL_ADDRESS FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id  and a.mail_item=b.MAIL_ITEM_MST and a.mail_item=58 and b.mail_user_setup_id=c.id and a.company_id =".$company_id."  and   A.IS_DELETED=0 and A.STATUS_ACTIVE=1 AND a.MAIL_TYPE=5 and b.IS_DELETED=0 and b.STATUS_ACTIVE=1  and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";
     //echo $sql;die;
    
    
    //$mail_sql=sql_select($sql);
    $receverMailArr=array();
    foreach($mail_sql as $row)
    {
        $buyerArr=explode(',',$row['BUYER_IDS']);
        $brandArr=explode(',',$row['BRAND_IDS']);
        foreach($buyerArr as $buyerid){
            foreach($brandArr as $brandid){
                $receverMailArr[$buyerid][$brandid][$row['EMAIL_ADDRESS']]=$row['EMAIL_ADDRESS'];
            }
        }
        
    }

    $to = implode(',',array_unique($toArr));


    $subject="Consumption Entry [CAD] For LA Costing";
    $header=mailHeader();
    if($to!=""){
        echo sendMailMailer( $to, $subject, $message, $from_mail,$att_file_arr );
        
        $con = connect();
        $rID=sql_update("consumption_la_costing_mst",'mail_send_date',"'".$pc_date_time."'","id","".$update_id."",0,0);
        
        if($rID==1){
            oci_commit($con);
        }
        else{
            oci_rollback($con);
        }
        
        disconnect($con);
        die;
    }
    else{echo "Mail Not Send";}
	exit();
}

if($action=="fabric_description_popup")
{
	echo load_html_head_contents("Fabric Description Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>

	<script>
		function js_set_value(data)
		{
			var data=data.split('_');
			if(data[1] == 2)
			{
				var fabric_yarn_description=return_global_ajax_value(data[0]+'**'+data[1], 'fabric_yarn_description', '', 'consumption_la_costing_controller');
				var fabric_yarn_description_arr=fabric_yarn_description.split("**");
				var fabric_description=trim(data[2])+' '+trim(fabric_yarn_description_arr[0]);
				document.getElementById('fab_des_id').value=data[0];
				document.getElementById('fab_nature_id').value=data[1];
				document.getElementById('construction').value=trim(data[2]);
				document.getElementById('fab_gsm').value=trim(data[3]);
				document.getElementById('process_loss').value=trim(data[4]);
				document.getElementById('fab_desctiption').value=trim(fabric_description);
				document.getElementById('fab_desctiption_title').value=trim(fabric_description+','+fabric_yarn_description_arr[2]);
				document.getElementById('composition').value=trim(fabric_yarn_description_arr[0]);
				var yarn =fabric_yarn_description_arr[1].split("_");
				if(yarn[1]*1==0 || yarn[1]==""){
					alert("Composition not set in yarn count determination");
					return;
				}
				document.getElementById('yarn_desctiption').value=trim(fabric_yarn_description_arr[1]);
				parent.emailwindow.hide();
			}
			if(data[1] == 3)
			{
				var fabric_yarn_description=return_global_ajax_value(data[0]+'**'+data[1], 'fabric_yarn_description', '', 'consumption_la_costing_controller');
				var fabric_yarn_description_arr=fabric_yarn_description.split("**");
				
				var rdFabref="";
				if(trim(data[7])!="" && trim(data[8])!="") rdFabref=trim(data[7])+', '+trim(data[8])+',';
				else if(trim(data[7])!="" && trim(data[8])=="") rdFabref=trim(data[7])+',';
				else if(trim(data[7])=="" && trim(data[8])!="") rdFabref=trim(data[8])+',';
				
				var fabric_description=trim(rdFabref)+' '+trim(data[4])+' '+trim(data[2])+' '+trim(data[5])+' '+trim(fabric_yarn_description_arr[0]);
				
				document.getElementById('fab_des_id').value=data[0];
				document.getElementById('hiddrdno').value=trim(data[7]);
				document.getElementById('hiddfabref').value=trim(data[8]);
				document.getElementById('fab_data').value=trim(data[9])+'_'+trim(data[10])+'_'+trim(data[11])+'_'+trim(data[12]);
				document.getElementById('weight_type').value=trim(data[6]);
				document.getElementById('fab_desctiption').value=trim(fabric_description);
				document.getElementById('fab_desctiption_title').value=trim(fabric_description+','+fabric_yarn_description_arr[2]);
				/*document.getElementById('composition').value=trim(fabric_yarn_description_arr[0]);
				var yarn =fabric_yarn_description_arr[1].split("_");
				if(yarn[1]*1==0 || yarn[1]==""){
					alert("Composition not set in yarn count determination");
					return;
				}
				document.getElementById('yarn_desctiption').value=trim(fabric_yarn_description_arr[1]);*/
				parent.emailwindow.hide();
			}
			
		}
		function toggle( x, origColor )
		{
			var newColor = 'yellow';
			document.getElementById(x).style.backgroundColor = ( newColor == document.getElementById(x).style.backgroundColor )? origColor : newColor;
		}
		</script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
                <thead>
                    <tr>
                    	<th colspan="4" align="center"><? echo create_drop_down( "cbo_string_search_type", 100, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                    </tr>
                    <tr>
                    	<th>RD No</th>
                        <th>Construction</th>
                        <th>Ounce/Weight</th>
                        <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="general">
                    	<td align="center"><input type="text" style="width:80px" class="text_boxes" name="txt_rdno" id="txt_rdno" /></td>
                        <td align="center"><input type="text" style="width:130px" class="text_boxes" name="txt_construction" id="txt_construction" /></td>
                        <td align="center">	<input type="text" style="width:130px" class="text_boxes" name="txt_gsm_weight" id="txt_gsm_weight" /></td>
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo 3; ?>'+'**'+'<? echo $libyarncountdeterminationid; ?>'+'**'+document.getElementById('txt_construction').value+'**'+document.getElementById('txt_gsm_weight').value+'**'+document.getElementById('cbo_string_search_type').value+'**'+document.getElementById('txt_rdno').value, 'fabric_description_popup_search_list_view', 'search_div', 'consumption_la_costing_controller', 'setFilterGrid(\'list_view\',-1)'); toggle( 'tr_'+'<? echo $libyarncountdeterminationid; ?>', '#FFFFCC');" style="width:100px;" />
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

if($action=="fabric_description_popup_search_list_view")
{
	extract($_REQUEST);
	list($fabric_nature,$libyarncountdeterminationid,$construction,$gsm_weight,$string_search_type,$rdno)=explode('**',$data);
	$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count"  );
	$search_con='';
	$user_arr=return_library_array( "select user_full_name,id from user_passwd", "id", "user_full_name");
	if($string_search_type==1)
	{
		if($construction!='') {$search_con .= " and a.construction='".trim($construction)."'";}
		if($gsm_weight!='') {$search_con .= " and a.gsm_weight='".trim($gsm_weight)."'";}
		if($rdno!='') {$search_con .= " and a.rd_no='".trim($rdno)."'";}
	}
	else if($string_search_type==2)
	{
		if($construction!='') {$search_con .= " and a.construction like ('".trim($construction)."%')";}
		if($gsm_weight!='') {$search_con .= " and a.gsm_weight like ('".trim($gsm_weight)."%')";}
		if($rdno!='') {$search_con .= " and a.rd_no like ('".trim($rdno)."%')";}
	}
	else if($string_search_type==3)
	{
		if($construction!='') {$search_con .= " and a.construction like ('%".trim($construction)."')";}
		if($gsm_weight!='') {$search_con .= " and a.gsm_weight like ('%".trim($gsm_weight)."')";}
		if($rdno!='') {$search_con .= " and a.rd_no like ('%".trim($rdno)."')";}
	}
	else if($string_search_type==4 || $string_search_type==0)
	{
		if($construction!='') {$search_con .= " and a.construction like ('%".trim($construction)."%')";}
		if($gsm_weight!='') {$search_con .= " and a.gsm_weight like ('%".trim($gsm_weight)."%')";}
		if($rdno!='') {$search_con .= " and a.rd_no like ('%".trim($rdno)."%')";}
	}

?>
</head>
<body>

    <!-- <div> -->
        <form>
            <input type="hidden" id="fab_des_id" name="fab_des_id" />
            <input type="hidden" id="fab_nature_id" name="fab_des_id" />
            <input type="hidden" id="hiddrdno" name="hiddrdno" />
            <input type="hidden" id="hiddfabref" name="hiddfabref" />
            <input type="hidden" id="fab_data" name="fab_data" />
            <input type="hidden" id="process_loss" name="process_loss" />
            <input type="hidden" id="fab_desctiption" name="fab_desctiption" />
            <input type="hidden" id="fab_desctiption_title" name="fab_desctiption_title" />
            <input type="hidden" id="yarn_desctiption" name="yarn_desctiption" />
            <input type="hidden" id="weight_type" name="weight_type" />
        </form>
<?
	$composition_arr=array();
	$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");
	$lib_group_short=return_library_array( "select id,group_short_name from lib_group where id=1 and status_active=1", "id", "group_short_name");
	$group_short_name=$lib_group_short[1];
	$arr=array (0=>$item_category, 3=>$color_range,6=>$composition,8=>$lib_yarn_count,9=>$yarn_type);
	if($fabric_nature == 2)
	{
		$sql="select a.fab_nature_id,a.construction,a.gsm_weight,a.color_range_id,a.stich_length,a.process_loss,b.copmposition_id,b.percent,b.count_id,b.type_id,a.id,b.id as bid,a.shrinkage_l,a.shrinkage_w from  lib_yarn_count_determina_mst a,  lib_yarn_count_determina_dtls b where a.id=b.mst_id and  b.is_deleted=0 and  b.is_deleted=0 and a.entry_form=184 order by a.id,b.id";
		$table_width='1030';
		$table_width2='1050';
	}
	if($fabric_nature == 3)
	{
		$sql="select a.fab_nature_id,a.construction,a.gsm_weight,a.color_range_id,a.stich_length,a.process_loss,b.copmposition_id,b.percent,b.count_id,b.type_id,a.id,b.id as bid,a.shrinkage_l,a.shrinkage_w from  lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and  a.is_deleted=0 and a.entry_form=426 order by a.id,b.id";
		$table_width='1130';
		$table_width2='1150';
	}
	
	$data_array=sql_select($sql);
	$sysCodeArr=array();
	if (count($data_array)>0)
	{
		foreach( $data_array as $row )
		{
			$compo_per="";
			if(($row[csf('percent')]*1)>0) $compo_per=$row[csf('percent')]."% "; else $compo_per="";
			if(array_key_exists($row[csf('id')],$composition_arr))
			{
				$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$compo_per.$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].",";
			}
			else
			{
				$composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$compo_per.$lib_yarn_count[$row[csf('count_id')]]." ".$yarn_type[$row[csf('type_id')]].",";
			}
			$sys_code=$group_short_name.'-'.$row[csf('id')];
			$sysCodeArr[$row[csf('id')]]=$sys_code;
			
		}
	}
?>
    <table class="rpt_table" width="<? echo $table_width?>" cellspacing="0" cellpadding="0" border="0" rules="all" style="position: sticky; top: 0;" >
        <thead>
        	<? if($fabric_nature == 2){        		
        		?>
            <tr>
                <th style="word-wrap: break-word;word-break: break-all;" width="25">SL No</th>                
                <th style="word-wrap: break-word;word-break: break-all;" width="80">Sequence<br>No</th>
                <th style="word-wrap: break-word;word-break: break-all;" width="100">Fab<br>Nature</th>
                <th style="word-wrap: break-word;word-break: break-all;" width="100">Construction</th>
                <th style="word-wrap: break-word;word-break: break-all;" width="100">Ounce/<br>Weight</th>
                <th style="word-wrap: break-word;word-break: break-all;" width="100">Color<br>Range</th>
                <th style="word-wrap: break-word;word-break: break-all;" width="90">Stich<br>Length</th>
                <th style="word-wrap: break-word;word-break: break-all;" width="50">Process<br>Loss</th>
                <th style="word-wrap: break-word;word-break: break-all;">Composition</th>
            </tr>
            <? }
            else if($fabric_nature == 3){  ?>
        	<tr>
        		<th style="word-wrap: break-word;word-break: break-all;" width="25">SL No</th>
	            <th style="word-wrap: break-word;word-break: break-all;" width="80">Fab<br>Nature</th>
                <th style="word-wrap: break-word;word-break: break-all;" width="80">RD No</th>
	            <th style="word-wrap: break-word;word-break: break-all;" width="80">Fabric<br>Ref</th>
	            <th style="word-wrap: break-word;word-break: break-all;" width="70">Type</th>
	            <th style="word-wrap: break-word;word-break: break-all;" width="100">Construction</th>
	            <th style="word-wrap: break-word;word-break: break-all;" width="80">Design</th>
	            <th style="word-wrap: break-word;word-break: break-all;" width="50">Ounce/<br>Weight</th>
	            <th style="word-wrap: break-word;word-break: break-all;" width="50">Weight<br>Type</th>
	            <th style="word-wrap: break-word;word-break: break-all;" width="50">Color<br>Range</th>
	            <th style="word-wrap: break-word;word-break: break-all;" width="50">Full<br>Width</th>
	            <th style="word-wrap: break-word;word-break: break-all;" width="50">Cutable<br>Width</th>
	            <th style="word-wrap: break-word;word-break: break-all;" width="55">Shrinkage<br>L</th>
	            <th style="word-wrap: break-word;word-break: break-all;" width="55">Shrinkage<br>W</th>
	            <th style="word-wrap: break-word;word-break: break-all;" width="190">Composition</th>          
        	</tr>
            <? } ?>
       </thead>
   </table>
   <table id="list_view" class="rpt_table" width="<? echo $table_width; ?>" height="" cellspacing="0" cellpadding="0" border="1" rules="all" style="max-height:300px; overflow-y:scroll">
        <tbody>
	<?
	if($fabric_nature == 2){
		$sql_data=sql_select("select a.fab_nature_id, a.construction, a.gsm_weight, a.color_range_id, a.stich_length, a.process_loss, a.id, a.sequence_no from  lib_yarn_count_determina_mst a,  lib_yarn_count_determina_dtls b where a.id=b.mst_id and  a.fab_nature_id= '$fabric_nature' and  a.is_deleted=0 and a.status_active=1 and  b.is_deleted=0 and  b.is_deleted=0 and entry_form=184 $search_con group by a.id,a.fab_nature_id,a.construction,a.gsm_weight,a.color_range_id,a.stich_length,a.process_loss,a.sequence_no order by a.id");
		$i=1;
		foreach($sql_data as $row)
		{
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			?>
	            <tr id="tr_<? echo $row[csf('id')] ?>" bgcolor="<? echo $bgcolor; ?>" height="20" style="cursor:pointer; word-break:break-all;" onClick="js_set_value('<? echo $row[csf('id')]."_".$row[csf('fab_nature_id')]."_".$row[csf('construction')]."_".$row[csf('gsm_weight')]."_".$row[csf('process_loss')]."____"; ?>')">
	                <td style="word-wrap: break-word;word-break: break-all;" width="25"><? echo $i; ?></td>
	                <td style="word-wrap: break-word;word-break: break-all;" width="80" align="left"><? echo $row[csf('sequence_no')]; ?></td>
	                <td style="word-wrap: break-word;word-break: break-all;" width="100" align="left"><? echo $item_category[$row[csf('fab_nature_id')]]; ?></td>
	                <td style="word-wrap: break-word;word-break: break-all;" width="100" align="left"><? echo $row[csf('construction')]; ?></td>
	                <td style="word-wrap: break-word;word-break: break-all;" width="100" align="right"><? echo $row[csf('gsm_weight')]; ?></td>
	                <td style="word-wrap: break-word;word-break: break-all;" width="100" align="left"><? echo $color_range[$row[csf('color_range_id')]]; ?></td>
	                <td style="word-wrap: break-word;word-break: break-all;" width="90" align="right"><? echo $row[csf('stich_length')]; ?></td>
	                <td style="word-wrap: break-word;word-break: break-all;" width="50" align="right"><? echo $row[csf('process_loss')]; ?></td>
	                <td style="word-wrap: break-word;word-break: break-all;"><? echo $composition_arr[$row[csf('id')]]; ?></td>
	            </tr>

			<?
		    $i++;
	    }
	}
	if($fabric_nature == 3){
		$sql_data=sql_select("select a.id,a.fab_nature_id, a.type, a.construction, a.gsm_weight, a.weight_type, a.design, a.fabric_ref, a.color_range_id, a.rd_no, a.inserted_by, a.status_active, a.full_width, a.cutable_width, a.shrinkage_l, a.shrinkage_w from  lib_yarn_count_determina_mst a where a.is_deleted=0 and a.status_active=1 and a.entry_form=426 $search_con order by a.id");
		$i=1;
		foreach($sql_data as $row)
		{
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			?>
	            <tr id="tr_<?=$row[csf('id')] ?>" bgcolor="<?=$bgcolor; ?>" height="20" style="cursor:pointer; word-break:break-all;" onClick="js_set_value('<?=$row[csf('id')]."_".$row[csf('fab_nature_id')]."_".$row[csf('construction')]."_".$row[csf('gsm_weight')]."_".$row[csf('type')]."_".$row[csf('design')]."_".$row[csf('weight_type')]."_".$row[csf('rd_no')]."_".$row[csf('fabric_ref')]."_".$row[csf('full_width')]."_".$row[csf('cutable_width')]."_".$row[csf('shrinkage_l')]."_".$row[csf('shrinkage_w')]; ?>')">
	                <td width="25" align="center"><?=$i; ?></td>
	                <td width="80" style="word-break:break-all"><?=$item_category[$row[csf('fab_nature_id')]]; ?></td>
                    <td width="80" style="word-break:break-all"><?=$row[csf('rd_no')]; ?></td>
	                <td width="80" style="word-break:break-all"><?=$row[csf('fabric_ref')]; ?></td>
	                <td width="70" style="word-break:break-all"><?=$row[csf('type')]; ?></td>
	                <td width="100" style="word-break:break-all"><?=$row[csf('construction')]; ?></td>
	                <td width="80" style="word-break:break-all"><?=$row[csf('design')]; ?></td>
	                <td width="50" style="word-break:break-all"><?=$row[csf('gsm_weight')]; ?></td>
	                <td width="50" style="word-break:break-all"><?=$fabric_weight_type[$row[csf('weight_type')]]; ?></td>
	                <td width="50" style="word-break:break-all"><?=$color_range[$row[csf('color_range_id')]]; ?></td>
	                <td width="50" style="word-break:break-all"><?=$row[csf('full_width')]; ?></td>
	                <td width="50" style="word-break:break-all"><?=$row[csf('cutable_width')]; ?></td>
	                <td width="55" style="word-break:break-all"><?=$row[csf('shrinkage_l')]; ?></td>
	                <td width="55" style="word-break:break-all"><?=$row[csf('shrinkage_w')]; ?></td>
	                <td width="190" style="word-break:break-all"><?=$composition_arr[$row[csf('id')]]; ?></td>
	            </tr>
			<?
		    $i++;
	    }
	}	
    ?>
        </tbody>
    </table>

</body>
</html>
<?
exit();
}

if($action =="fabric_yarn_description")
{
	$fab_description=""; $yarn_description="";
	$data_arr = explode("**", $data);
	$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");
	$shinkage='';
	if($data_arr[1] == 2)
	{
		$sql="select a.fab_nature_id,a.construction,a.gsm_weight,a.color_range_id,a.stich_length,a.process_loss,b.copmposition_id,b.percent,b.count_id,b.type_id,a.id,a.shrinkage_l,a.shrinkage_w from  lib_yarn_count_determina_mst a,  lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.id=$data_arr[0] and  a.is_deleted=0 and a.entry_form=184 order by a.id,b.id";

		$data_array=sql_select($sql);
		if (count($data_array)>0)
		{
			foreach( $data_array as $row )
			{
				$compo_per="";
				if(($row[csf('percent')]*1)>0) $compo_per=$row[csf('percent')]."% "; else $compo_per="";
				if($fab_description!="")
				{
					$fab_description=$fab_description." ".$composition[$row[csf('copmposition_id')]]." ".$compo_per;
				}
				else
				{
					$fab_description=$composition[$row[csf('copmposition_id')]]." ".$compo_per;
				}

				if($shinkage!="")
				{
					$shinkage=$shinkage.",".$row[csf('shrinkage_l')].",".$row[csf('shrinkage_w')];
				}
				else
				{
					$shinkage=$row[csf('shrinkage_l')].",".$row[csf('shrinkage_w')];
				}

				if($yarn_description!="")
				{
					$yarn_description=$yarn_description."__".$row[csf('count_id')]."_".$row[csf('copmposition_id')]."_100_".$row[csf('type_id')]."_".$row[csf('percent')];
				}
				else
				{
					$yarn_description=$row[csf('count_id')]."_".$row[csf('copmposition_id')]."_100_".$row[csf('type_id')]."_".$row[csf('percent')];
				}
			}
		}
	}
	if($data_arr[1] == 3)
	{
		$sql="SELECT a.id,a.fab_nature_id, a.type, a.construction, a.gsm_weight, a.weight_type, a.design, a.fabric_ref, a.color_range_id, a.inserted_by, a.status_active, b.copmposition_id, b.percent, b.count_id, b.type_id,a.shrinkage_l,a.shrinkage_w from  lib_yarn_count_determina_mst a join  lib_yarn_count_determina_dtls b on a.id=b.mst_id where a.id=$data_arr[0] and  a.is_deleted=0 and a.entry_form=426 order by a.id,b.id";
		$data_array=sql_select($sql);
		if (count($data_array)>0)
		{
			foreach( $data_array as $row )
			{
				$compo_per="";
				if(($row[csf('percent')]*1)>0) $compo_per=$row[csf('percent')]."% "; else $compo_per="";
				if($fab_description!="")
				{
					$fab_description=$fab_description." ".$composition[$row[csf('copmposition_id')]]." ".$compo_per;
				}
				else
				{
					$fab_description=$composition[$row[csf('copmposition_id')]]." ".$compo_per;
				}

				if($shinkage!="")
				{
					$shinkage=$shinkage.",".$row[csf('shrinkage_l')].",".$row[csf('shrinkage_w')];
				}
				else
				{
					$shinkage=$row[csf('shrinkage_l')].",".$row[csf('shrinkage_w')];
				}

				if($yarn_description!="")
				{
					$yarn_description=$yarn_description."__".$row[csf('count_id')]."_".$row[csf('copmposition_id')]."_100_".$row[csf('type_id')]."_".$row[csf('percent')];
				}
				else
				{
					$yarn_description=$row[csf('count_id')]."_".$row[csf('copmposition_id')]."_100_".$row[csf('type_id')]."_".$row[csf('percent')];
				}
			}
		}
	}
	echo $fab_description."**".$yarn_description."**".$shinkage;

}