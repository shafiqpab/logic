<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');


if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id = $_SESSION['logic_erp']["user_id"];
//========== user credential start ========
$userCredential = sql_select("SELECT unit_id as company_id,company_location_id,store_location_id,item_cate_id,supplier_id FROM user_passwd where id=$user_id");
$company_id = $userCredential[0][csf('company_id')];
$supplier_id = $userCredential[0][csf('supplier_id')];
$store_location_id = $userCredential[0][csf('store_location_id')];
$item_cate_id = $userCredential[0][csf('item_cate_id')];
$company_location_id = $userCredential[0][csf('company_location_id')];

if ($company_id !='') {
    $company_credential_cond = " and comp.id in($company_id)";
}

if ($company_location_id !='') {
    $company_location_credential_cond = " and id in($company_location_id)";
}

if ($store_location_id !='') {
    $store_location_credential_cond = " and a.id in($store_location_id)";
}
if($item_cate_id !='') {
    $item_cate_credential_cond = $item_cate_id ;
}
else
{
	 $item_cate_credential_cond="".implode(",",array_flip($general_item_category))."";
}
if ($supplier_id !='') {
    $supplier_credential_cond = "and a.id in($supplier_id)";
}

//========== user credential end ==========

//============================================================================

if($db_type==2 || $db_type==1 )
{
	$mrr_date_check="and to_char(insert_date,'YYYY')=".date('Y',time())."";
	$concat="";
	$concat_coma="||";
}
else if ($db_type==0)
{
	$mrr_date_check="and year(insert_date)=".date('Y',time())."";
	$concat="concat";
	$concat_coma=",";

}


if($action=="com_wise_all_data")
{
	$location_data=sql_select("select ID, LOCATION_NAME from lib_location where company_id=$data $company_location_credential_cond and status_active =1 and is_deleted=0 order by location_name");
	$location_arr=array();
	foreach($location_data as $row)
	{
		$location_arr[$row["ID"]]=$row["LOCATION_NAME"];
	}
	unset($location_data);
	$js_location_arr= json_encode($location_arr);
	$loan_party_data=sql_select("select a.ID, a.SUPPLIER_NAME from lib_supplier a, lib_supplier_tag_company b
	where a.id=b.supplier_id and b.tag_company=$data and a.status_active=1 and a.is_deleted=0 and a.id in(select supplier_id from lib_supplier_party_type where party_type=91) order by supplier_name");
	$loan_party_arr=array();
	foreach($loan_party_data as $row)
	{
		$loan_party_arr[$row["ID"]]=$row["SUPPLIER_NAME"];
	}
	unset($loan_party_data);
	$js_loan_party_arr= json_encode($loan_party_arr);
	
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=6 and report_id=182 and is_deleted=0 and status_active=1");
	
	
	echo $js_location_arr."**".$js_loan_party_arr."**".$print_report_format;
	die();
}


/*if ($action=="load_drop_down_location")
{
	//$data=explode("_",$data);
	echo create_drop_down( "cbo_location", 152, "select id,location_name from lib_location where company_id='$data' $company_location_credential_cond and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", 0, "load_drop_down('requires/general_item_issue_controller', this.value+'_'+$data, 'load_drop_down_store','store_td');store_select();" );
	exit();
}*/

if ($action == "load_drop_down_division_popup") 
{
	echo create_drop_down("cbo_division_name", 90, "select id,division_name from lib_division where company_id=$data and is_deleted=0  and status_active=1", "id,division_name", 1, "-- Select --", $selected, "load_drop_down( 'general_item_issue_controller', this.value, 'load_drop_down_department_popup','department_td_popup');");
	exit();
}
if ($action == "load_drop_down_department_popup") {
	echo create_drop_down("cbo_department_name", 90, "select id,department_name from lib_department where division_id=$data and is_deleted=0 and status_active=1", "id,department_name", 1, "-- Select --", $selected, "load_drop_down( 'general_item_issue_controller', this.value, 'load_drop_down_section_popup','section_td_popup');");
	exit();
}

if ($action == "load_drop_down_section_popup") {
	echo create_drop_down("cbo_section_name", 90, "select id,section_name from lib_section where department_id=$data and is_deleted=0 and status_active=1", "id,section_name", 1, "-- Select --", $selected, "load_drop_down( 'general_item_issue_controller', this.value, 'load_drop_down_sub_section_popup','sub_section_td_popup');");
	exit();
}
if ($action == "load_drop_down_sub_section_popup") {
	$array = array(0 => "None");
	echo create_drop_down("cbo_sub_section_name", 90, $array, "", 1, "-- Select --", 1);
	exit();
}

if ($action=="load_drop_down_store")
{
	$data=explode("_",$data);
	echo create_drop_down( "cbo_store_name", 152, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id='$data[1]' and a.location_id = $data[0] and b.category_type in(4,8,9,10,11,15,16,17,18,19,20,21,22,32,33,34,35,36,37,38,39,40,41,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,89,90,91,92,93,94,99) and a.status_active=1 and a.is_deleted=0 $store_location_credential_cond group by a.id, a.store_name order by a.store_name","id,store_name", 1,"--Select store--",0,"load_drop_down('requires/general_item_issue_controller', this.value+'_'+$data[1], 'load_drop_floor','floor_td');");
	exit();
}

if($action == "load_drop_floor")
{
	$data = explode("_", $data);
	$store_id=$data[0];
	$company_id = $data[1];
	echo create_drop_down( "cbo_floor", "152", "select b.floor_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.floor_id and b.store_id='$store_id' and a.company_id='$company_id' and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0  group by b.floor_id,a.floor_room_rack_name  order by a.floor_room_rack_name","floor_id,floor_room_rack_name", 1, "--Select Floor--", 0, "load_drop_down('requires/general_item_issue_controller', this.value+'_'+$company_id+'_'+$store_id, 'load_drop_room','room_td');",1 );
}

if($action == "load_drop_room")
{
	$data = explode("_", $data);
	$floor_id=$data[0];
	$company_id = $data[1];
	$store_id = $data[2];
	$room_id = $data[3];

	echo create_drop_down( "cbo_room", 152, "select b.room_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.room_id and b.floor_id='$floor_id' and b.store_id=$store_id and a.company_id='$company_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.room_id,a.floor_room_rack_name order by a.floor_room_rack_name","room_id,floor_room_rack_name", 1, "--Select Room--", $room_id, "load_drop_down('requires/general_item_issue_controller', this.value+'_'+$company_id+'_'+$store_id, 'load_drop_rack','rack_td');",1 );
}

if($action == "load_drop_rack")
{
	$data = explode("_", $data);
	$room_id=$data[0];
	$company_id = $data[1];
	$store_id = $data[2];
	$rack_id = $data[3];
	echo create_drop_down( "txt_rack", 152, "select b.rack_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.rack_id and b.room_id='$room_id' and b.store_id=$store_id and a.company_id='$company_id' and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.rack_id,a.floor_room_rack_name order by a.floor_room_rack_name","rack_id,floor_room_rack_name", 1, "--Select Rack--", $rack_id, "load_drop_down('requires/general_item_issue_controller', this.value+'_'+$company_id+'_'+$store_id, 'load_drop_shelf','shelf_td');",1 );
}

if($action == "load_drop_shelf")
{
	$data = explode("_", $data);
	$rack=$data[0];
	$company_id = $data[1];
	$store_id = $data[2];
	$shelf_id = $data[3];
	echo create_drop_down( "txt_shelf", 152, "select b.shelf_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.shelf_id and b.rack_id='$rack' and b.store_id=$store_id and a.company_id='$company_id' and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.shelf_id,a.floor_room_rack_name order by a.floor_room_rack_name","shelf_id,floor_room_rack_name", 1, "--Select Shelf--", $shelf_id, "load_drop_down('requires/general_item_issue_controller', this.value+'_'+$company_id+'_'+$store_id, 'load_drop_bin','bin_td');",1 );
}

if($action == "load_drop_bin")
{
	$data = explode("_", $data);
	$shelf=$data[0];
	$company_id = $data[1];
	$store_id = $data[2];
	$bin_id = $data[3];
	echo create_drop_down( "cbo_bin", 152, "select b.bin_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.bin_id and b.shelf_id=$shelf and b.store_id='$store_id' and a.company_id='$company_id' and a.company_id='$company_id' and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.bin_id,a.floor_room_rack_name order by a.floor_room_rack_name","bin_id,floor_room_rack_name", 1, "--Select Bin--", $bin_id, "",1 );
}


/*if($action == "load_drop_room")
{
	$data = explode("_", $data);
	$store_id=$data[0];
	$company_id = $data[1];

	echo create_drop_down( "cbo_room", "152", "select b.room_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.room_id and b.store_id='$store_id' and a.company_id='$company_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.room_id,a.floor_room_rack_name order by a.floor_room_rack_name","room_id,floor_room_rack_name", 1, "--Select Room--", 0, "" );
}

if($action == "load_drop_rack")
{
	$data = explode("_", $data);
	$store_id=$data[0];
	$company_id = $data[1];
	echo create_drop_down( "txt_rack", '152', "select b.rack_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.rack_id and b.store_id='$store_id' and a.company_id='$company_id' and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.rack_id,a.floor_room_rack_name order by a.floor_room_rack_name","rack_id,floor_room_rack_name", 1, "--Select Rack--", 0, "" );
}

if($action == "load_drop_shelf")
{
	$data = explode("_", $data);
	$store_id=$data[0];
	$company_id = $data[1];
	echo create_drop_down( "txt_shelf", '152', "select b.shelf_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.shelf_id and b.store_id='$store_id' and a.company_id='$company_id' and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.shelf_id,a.floor_room_rack_name order by a.floor_room_rack_name","shelf_id,floor_room_rack_name", 1, "--Select Shelf--", 0, "" );
}

if($action == "load_drop_bin")
{
	$data = explode("_", $data);
	$store_id=$data[0];
	$company_id = $data[1];
	echo create_drop_down( "cbo_bin", '152', "select b.bin_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.bin_id and b.store_id='$store_id' and a.company_id='$company_id' and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.bin_id,a.floor_room_rack_name order by a.floor_room_rack_name","bin_id,floor_room_rack_name", 1, "--Select Bin--", 0, "" );
}
load_drop_down('requires/general_item_issue_controller', this.value+'_'+$data[1], 'load_drop_rack','rack_td');load_drop_down('requires/general_item_issue_controller', this.value+'_'+$data[1], 'load_drop_shelf','shelf_td');load_drop_down('requires/general_item_issue_controller', this.value+'_'+$data[1], 'load_drop_bin','bin_td');
if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location", 152, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", 0, "load_floor();load_room_rack_self_bin('requires/grey_fabric_receive_controller*13', 'store','store_td', $('#cbo_company_id').val(), this.value);" );//load_drop_down( 'requires/grey_fabric_receive_controller',document.getElementById('cbo_company_id').value+'_'+this.value, 'load_drop_down_floor', 'cbo_floor' );
	exit();
}
*/


if ($action=="load_drop_down_location_popup")
{

	echo create_drop_down( "cbo_location_name", 90, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' $company_location_credential_cond order by location_name","id,location_name",1, "-- Select --", $selected, "",0 );
	exit();
}

if ($action=="load_drop_down_location_asetpopup")
{

	echo create_drop_down( "cbo_location", 170, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' $company_location_credential_cond order by location_name","id,location_name",1, "-- Select --", $selected, "",0 );
	exit();
}

/*if ($action=="load_drop_down_store")
{
 	echo create_drop_down( "cbo_store_name", 152, $blank_array,"", 1, "-- Select Store --", 0, "", 1 );
	exit();
}*/



if ($action=="load_drop_down_loan_party")
{
	echo create_drop_down( "cbo_loan_party", 170, "select a.id, a.supplier_name from lib_supplier a, lib_supplier_tag_company b
	where a.id=b.supplier_id and b.tag_company=$data and a.status_active=1 and a.is_deleted=0 and a.id in(select supplier_id from lib_supplier_party_type where party_type=91) order by supplier_name","id,supplier_name", 1, "- Select Loan Party -", $selected, "","","" );
	exit();
}

//load drop down knitting company
if($action == "load_drop_down_issue_to")
{
	$exDataArr = explode("**", $data);
	$issue_source = $exDataArr[0];
	$company = $exDataArr[1];
	$issuePurpose = $exDataArr[2];
	if ($company == "" || $company == 0) $company_cod = ""; else $company_cod = " and id=$company";

	if ($issue_source == 1)
		echo create_drop_down("cbo_issue_to", 170, "select id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name", "id,company_name", 1, "-- Select --", "", "");

	else if ($issue_source == 3 && $issuePurpose == 1)
		echo create_drop_down("cbo_issue_to", 170, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type in(1,9,20) and a.status_active=1 group by a.id, a.supplier_name order by a.supplier_name", "id,supplier_name", 1, "-- Select --", 0, "", 0);
	else if ($issue_source == 3 && $issuePurpose == 2)
		echo create_drop_down("cbo_issue_to", 170, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type in(1,9,21,24) and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id,supplier_name", 1, "-- Select --", 0, "", 0);
	else if ($issue_source == 3 && $issuePurpose == 15)
		echo create_drop_down("cbo_issue_to", 170, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_party_type b where a.id=b.buyer_id and b.party_type in(7) and a.status_active=1 group by a.id, a.buyer_name order by a.buyer_name", "id,buyer_name", 1, "-- Select --", 0, "", 0);
	else if ($issue_source == 3)
		echo create_drop_down("cbo_issue_to", 170, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id,supplier_name", 1, "-- Select --", 0, "", 0);
	else if ($issue_source == 0)
		echo create_drop_down("cbo_issue_to", 170, $blank_array, "", 1, "-- Select --", $selected, "", "", "");
	exit();

}
if($action=="load_drop_down_issue_to_new")
{
	$exDataArr = explode("**", $data);
	$issue_source = $exDataArr[0];
	$company = $exDataArr[1];
	$issuePurpose = $exDataArr[2];
	if ($company == "" || $company == 0) $company_cod = ""; else $company_cod = " and id=$company";

	if ($issue_source == 1)
		echo create_drop_down("cbo_issue_to", 170, "select id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name", "id,company_name", 1, "-- Select --", "", "");

	else if ($issue_source == 3 && $issuePurpose == 1)
	
		echo create_drop_down("cbo_issue_to", 170, "SELECT DISTINCT c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$company' and b.party_type in(1,9,20) and c.status_active IN(1) and c.is_deleted=0 group by c.id, c.supplier_name   UNION ALL  SELECT DISTINCT c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c, inv_receive_master d where c.id=b.supplier_id and a.supplier_id = b.supplier_id and c.id=d.supplier_id and a.tag_company='$company'  and b.party_type  in(1,9,20) and c.status_active IN(1,3) and c.is_deleted=0 group by c.id, c.supplier_name    order by supplier_name", "id,supplier_name", 1, "-- Select --", 0, "", 0);
		
	else if ($issue_source == 3 && $issuePurpose == 2)
		echo create_drop_down("cbo_issue_to", 170, "SELECT DISTINCT c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$company' and b.party_type in(1,9,21,24) and c.status_active IN(1) and c.is_deleted=0 group by c.id, c.supplier_name   UNION ALL  SELECT DISTINCT c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c, inv_receive_master d where c.id=b.supplier_id and a.supplier_id = b.supplier_id and c.id=d.supplier_id and a.tag_company='$company'  and b.party_type  in(1,9,21,24) and c.status_active IN(1,3) and c.is_deleted=0 group by c.id, c.supplier_name    order by supplier_name", "id,supplier_name", 1, "-- Select --", 0, "", 0);
		
	else if ($issue_source == 3 && $issuePurpose == 15)
	
		echo create_drop_down("cbo_issue_to", 170, "SELECT DISTINCT c.buyer_name,c.id 
		from LIB_BUYER_TAG_COMPANY a,LIB_BUYER_PARTY_TYPE b, lib_buyer c where c.id=b.BUYER_ID and a.BUYER_ID = b.BUYER_ID and a.tag_company='$company' and b.party_type in(7) and c.status_active IN(1) and c.is_deleted=0 group by c.id, c.BUYER_NAME   UNION ALL  SELECT DISTINCT c.BUYER_NAME,c.id from LIB_BUYER_TAG_COMPANY a,LIB_BUYER_PARTY_TYPE b, lib_buyer c, inv_receive_master d where c.id=b.BUYER_ID and a.BUYER_ID = b.BUYER_ID and c.id=d.BUYER_ID and a.tag_company='$company'  and b.party_type  in(7) and c.status_active IN(1,3) and c.is_deleted=0 group by c.id, c.buyer_name    order by BUYER_NAME", "id,buyer_name", 1, "-- Select --", 0, "", 0);

		//echo create_drop_down("cbo_issue_to", 170, "SELECT DISTINCT c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$company' and b.party_type in(7) and c.status_active IN(1) and c.is_deleted=0 group by c.id, c.supplier_name   UNION ALL  SELECT DISTINCT c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c, inv_receive_master d where c.id=b.supplier_id and a.supplier_id = b.supplier_id and c.id=d.supplier_id and a.tag_company='$company'  and b.party_type  in(7) and c.status_active IN(1,3) and c.is_deleted=0 group by c.id, c.supplier_name    order by supplier_name", "id,buyer_name", 1, "-- Select --", 0, "", 0);
	else if ($issue_source == 3)
		echo create_drop_down("cbo_issue_to", 170, "SELECT DISTINCT c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$company'  and c.status_active IN(1) and c.is_deleted=0 group by c.id, c.supplier_name   UNION ALL  SELECT DISTINCT c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c, inv_receive_master d where c.id=b.supplier_id and a.supplier_id = b.supplier_id and c.id=d.supplier_id and a.tag_company='$company'  and c.status_active IN(1,3) and c.is_deleted=0 group by c.id, c.supplier_name    order by supplier_name", "id,supplier_name", 1, "-- Select --", 0, "", 0);
	else if ($issue_source == 0)
		echo create_drop_down("cbo_issue_to", 170, $blank_array, "", 1, "-- Select --", $selected, "", "", "");
	exit();
}
//load drop down Location of Issue to company
if($action == "load_drop_down_issue_to_location")
{
	$exDataArr = explode("**", $data);
	//$issue_source = $exDataArr[0];
	$company = $exDataArr[0];
	//$issuePurpose = $exDataArr[2];
	if ($company == "" || $company == 0) $company_cod = ""; else $company_cod = " and company_id=$company";
	//echo "select id,location_name from lib_location where status_active=1 and is_deleted=0 $company_cod order by location_name";
	//if ($issue_source == 1)
		echo create_drop_down("cbo_location_issue_to", 170, "select id,location_name from lib_location where status_active=1 and is_deleted=0 $company_cod $company_location_credential_cond  order by location_name", "id,location_name", 1, "-- Select --", 0, "");

	//else if ($issue_source == 0)
		//echo create_drop_down("cbo_location_issue_to", 170, $blank_array, "", 1, "-- Select --", $selected, "", "", "");
	exit();

}

if ($action=="load_drop_down_itemgroupPop")
{
	echo create_drop_down( "cbo_item_group", 180, "select id,item_name from lib_item_group where item_category=$data and status_active=1 and is_deleted=0 order by item_name","id,item_name", 1, "-- Select --", 0, "","" );
	exit();
}
if ($action=="load_drop_down_store_up")
{
	$data=explode("**",$data);
	//echo "select a.id,a.store_name from lib_store_location a, lib_store_location_category b where a.id=b.store_location_id and b.category_type=$data[1] $store_location_credential_cond and a.status_active=1 and a.is_deleted=0 and a.company_id=$data[0] group by a.id,a.store_name order by a.store_name";die;
	echo create_drop_down( "cbo_store_name", 180, "select a.id,a.store_name from lib_store_location a, lib_store_location_category b where a.id=b.store_location_id and b.category_type=$data[1] $store_location_credential_cond and a.status_active=1 and a.is_deleted=0 and a.company_id=$data[0] group by a.id,a.store_name order by a.store_name","id,store_name", 1, "Select Store", 0, "","" );
	exit();
}

if ($action=="load_drop_down_floor")
{
	$data=explode("_",$data);
	$company_id=$data[0];
	$location_id=$data[1];
	$machine_category=$data[2];
	if($location_id==0 || $location_id=="") $location_cond=""; else $location_cond=" and b.location_id=$location_id";
	if($machine_category==0 || $machine_category=="") $category_cond=""; else $category_cond=" and b.category_id=$machine_category";

	//echo "select a.id, a.floor_name from lib_prod_floor a, lib_machine_name b where a.id=b.floor_id and b.company_id=$company_id and b.status_active=1 and b.is_deleted=0 $location_cond $category_cond  group by a.id, a.floor_name order by a.floor_name";die;

	echo create_drop_down( "cbo_issue_floor", 150, "select a.id, a.floor_name from lib_prod_floor a, lib_machine_name b where a.id=b.floor_id and b.company_id=$company_id and b.status_active=1 and b.is_deleted=0 $location_cond $category_cond  group by a.id, a.floor_name order by a.floor_name","id,floor_name", 1, "-- Select Floor --", 0, "load_drop_down( 'requires/general_item_issue_controller',document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_machine_category').value+'_'+this.value, 'load_drop_machine', 'machine_td' );load_drop_down( 'requires/general_item_issue_controller', this.value+'_'+$company_id+'_'+$location_id, 'load_drop_down_sewing_line_floor', 'sewing_line_td' );load_drop_down( 'requires/general_item_issue_controller',document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_location').value+'_'+this.value, 'load_drop_down_table', 'table_no_td' );valid_floor(1);","" );
  exit();
}

//  line drop down
if($action=="load_drop_down_sewing_line_floor")
{
	$explode_data = explode("_",$data);
	//print_r($explode_data);
	//echo "select id,line_name from lib_sewing_line where  is_deleted=0 and status_active=1 and floor_name!=0 and floor_name = $explode_data[0] and company_name=$explode_data[1] and location_name=$explode_data[2] order by line_name";//die;
	echo create_drop_down( "cbo_sewing_line", 150, "select id,line_name from lib_sewing_line where  is_deleted=0 and status_active=1 and floor_name!=0 and floor_name = $explode_data[0] and company_name=$explode_data[1] and location_name=$explode_data[2] order by line_name","id,line_name", 1, "--- Select ---", $selected, "valid_line();",0,0 );
	exit();

}

if($action=="load_drop_down_table")
{
	$explode_data = explode("_",$data);
	$sql="select id, table_name from lib_table_entry where status_active=1 and company_name=$explode_data[0] and location_name=$explode_data[1] and floor_name=$explode_data[2] order by table_name";
	//echo $sql;
	echo create_drop_down( "cbo_table_no", 150, $sql,"id,table_name", 1, "--- Select ---", $selected, "",0,0 );
	exit();

}


if ($action=="load_drop_machine")
{
	$data=explode("_",$data);
	$company_id=$data[0];
	$machine_category=$data[1];
	$floor_id=$data[2];
	if($machine_category==0 || $machine_category=="") $machine_cond=""; else $machine_cond=" and category_id=$machine_category";
	if($floor_id==0 || $floor_id=="") $floor_cond=""; else $floor_cond=" and floor_id=$floor_id";

	echo create_drop_down( "cbo_machine_name", 150, "select id, machine_no as machine_name from lib_machine_name where  company_id=$company_id and status_active=1 and is_deleted=0 and is_locked=0 $floor_cond $machine_cond order by machine_no","id,machine_name", 1, "-- Select Machine --", 0, "valid_machine(1);","" );
	exit();
}

if ($action=="load_drop_down_division")
{	
	// echo "select a.id,a.department_name from  lib_department a, lib_division b where b.id=a.division_id and a.status_active =1 and a.is_deleted=0 and b.company_id='$data' order by department_name";die;
	echo create_drop_down( "cbo_division", 152, "select b.id,b.division_name from  lib_division b where  b.company_id='$data' and status_active=1 order by division_name","id,division_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/general_item_issue_controller', this.value, 'load_drop_down_department', 'department_td' );","","","","","","","","","onchange_void","" );
	exit();
}


//load drop down company department
if ($action=="load_drop_down_department")
{
	//echo "select a.id,a.department_name from  lib_department a where a.status_active =1 and a.is_deleted=0 and  a.division_id='$data' order by a.department_name";die;
	echo create_drop_down( "cbo_department", 152, "select a.id,a.department_name from  lib_department a where a.status_active =1 and a.is_deleted=0 and  a.division_id='$data' order by a.department_name","id,department_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/general_item_issue_controller', this.value, 'load_drop_down_section', 'section_td' );",0 );
	exit();
}

//load drop down company section
if ($action=="load_drop_down_section")
{
	echo create_drop_down( "cbo_section", 152, "select id,section_name from lib_section where status_active =1 and is_deleted=0 and department_id='$data' order by section_name","id,section_name", 1, "-- Select --", $selected, "",0 );
	exit();
}

if ($action=="load_room_rack_self_bin")
{
	$explodeData = explode('*', $data);
	$explodeData[11] = 'storeUpdateUptoDisable()';
	$data=implode('*', $explodeData);
	load_room_rack_self_bin("requires/general_item_issue_controller",$data);
}

//load drop down store

/*if ($action=="load_drop_down_store")
{
	//echo create_drop_down( "cbo_store_name", 130, "select a.id,a.store_name from lib_store_location a, lib_store_location_category b where a.id=b.store_location_id and b.category_type in (8,9,10,11) and a.status_active=1 and a.is_deleted=0 and FIND_IN_SET($data,a.company_id) group by a.id order by a.store_name","id,store_name", 1, "-- Select --", "", "","" );

	echo create_drop_down( "cbo_store_name", 130, "select a.id,a.store_name from lib_store_location a, lib_store_location_category b where a.id=b.store_location_id and b.category_type in ( $item_cate_credential_cond ) and a.status_active=1 and a.is_deleted=0 and a.company_id=$data $store_location_credential_cond group by a.id,a.store_name order by a.store_name","id,store_name", 1, "-- Select --", $selected, "","" );
	exit();
}*/

//load drop down item group
if ($action=="load_drop_down_itemgroup")
{
	//load_drop_down( 'requires/general_item_issue_controller', this.value, 'load_drop_down_uom', 'uom_td' );
	echo create_drop_down( "cbo_item_group", 150, "select id,item_name from lib_item_group where item_category=$data and status_active=1 and is_deleted=0 order by item_name","id,item_name", 1, "-- Select --", 0, "","" );
	exit();
}

//load drop down uom
if ($action=="load_drop_down_uom")
{
	$trim_group_arr = return_library_array("select id, trim_uom from lib_item_group","id","trim_uom");
	if($data==0) $uom=0; else $uom=$trim_group_arr[$data];
	echo create_drop_down( "cbo_uom", 150, $unit_of_measurement, "", 1, "-- Select --", $uom , "", 1);
	exit();
}

if ($action=="item_description_popup")
{
	echo load_html_head_contents("Item popup", "../../../", 1, 1,'','1','');
	extract($_REQUEST);
	$item_cat=str_replace("'","",$item_cat);

	//echo $item_cat.jahid;die;

?>
<script>
	function js_set_value(item_description)
	{
  		 $("#item_description_all").val(item_description);
  		//$("#item_description_all").val('lktoilix sdoi;f il;of opod loiioo;potg09p pgsaos 1205 050');
 		parent.emailwindow.hide();
	}
	function open_itemCode_popup()
	{
		if( form_validation('cbo_item_category','Item Category Name')==false )
		{
			return;
		}
		var cbo_item_category = $("#cbo_item_category").val();
		var page_link="general_item_issue_controller.php?action=item_code_popup&cbo_item_category="+cbo_item_category;
		var title="Item Code Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=250px,center=1,resize=1,scrolling=0','../../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var item_cote_all=this.contentDoc.getElementById("item_id").value;//alert(item_description_all);
			var splitArr = item_cote_all.split("_");
			$("#hide_product_id").val(splitArr[0]);
			$("#txt_item_code").val(splitArr[1]);
		}
	}
</script>
</head>

<body>
<div align="center" style="width:100%;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	<table width="950" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
            <thead>
                <tr>
                    <th width="230" class="must_entry_caption">Item Category</th>
                    <th width="230">Item Group</th>
                    <th width="180" style="display:none">Store Name</th>
                    <th width="130">Product Id</th>
                    <th width="130">Item Code</th>
                    <th width="130">Item Name</th>
                    <th ><input type="reset" name="re_button" id="re_button" value="Reset" style="width:90px" class="formbutton"  /></th>
                </tr>
            </thead>
            <tbody>
                <tr class="general">
                    <td>
                        <?

						//echo create_drop_down( "cbo_item_category", 180, $item_category,"", 1, "-- Select --", $item_cat, "load_drop_down( 'general_item_issue_controller', this.value, 'load_drop_down_itemgroupPop', 'item_group_td' );load_drop_down( 'general_item_issue_controller', $company_id+'**'+this.value, 'load_drop_down_store_up', 'store_td' );", 0,$item_cat);
						//echo $item_cate_credential_cond; load_drop_down( 'general_item_issue_controller', $company_id+'**'+this.value, 'load_drop_down_store_up', 'store_td' );
						echo create_drop_down( "cbo_item_category", 180, $general_item_category,"", 1, "-- Select --", 0, "load_drop_down( 'general_item_issue_controller', this.value, 'load_drop_down_itemgroupPop', 'item_group_td' );", 0,"$item_cate_credential_cond" );

                        ?>
                    </td>
                    <td width="" align="center" id="item_group_td">
                    	<?
                            //$search_by = array(1=>'Return Number');
							//$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";
							echo create_drop_down( "cbo_item_group", 180, $blank_array, "", 1, "-- Select --", 0, "", 0,"" );
                        ?>
                    </td>
                    <td align="center" id="store_td"  style="display:none">
                        <?
							//$company_id=str_replace("'","",$company_id);
							echo create_drop_down( "cbo_store_name", 180, $blank_array, "", 1, "-- Select --", 0, "", 0,"" );
                        ?>
                    </td>
                    <td align="center">
                        <input type="text" id="txt_product_id" name="txt_product_id" style="width:100px;" class="text_boxes">
                    </td>
                    <td align="center">
                        <input type="text" id="txt_item_code" name="txt_item_code" style="width:100px;" class="text_boxes" placeholder="Browse Or Write" onDblClick="open_itemCode_popup();">
                        <input type="hidden" id="hide_product_id" name="hide_product_id" >
                    </td>
                    <td align="center">
                        <input type="text" id="txt_item_description" name="txt_item_description" style="width:100px;" class="text_boxes" placeholder="Write" >
                    </td>
                    <td align="center">
                        <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_item_category').value+'_'+document.getElementById('cbo_item_group').value+'_'+document.getElementById('cbo_store_name').value+'_'+<? echo $company_id; ?>+'_'+document.getElementById('txt_item_code').value+'_'+document.getElementById('txt_product_id').value+'_'+<? echo $cbo_store_name; ?>+'_'+'<? echo $variable_lot; ?>'+'_'+document.getElementById('txt_item_description').value, 'create_item_search_list_view', 'search_div', 'general_item_issue_controller', 'setFilterGrid(\'tbl_serial\',-1)')" style="width:90px;" />
                    </td>
            </tr>
            </tbody>
        </table>
        <br>
        <div align="center" valign="top" id="search_div"> </div>
        </form>
   </div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
      <?

}

if ($action=="create_item_search_list_view")
{
	$ex_data = explode("_",$data);
    $item_category_id = $ex_data[0];
    $item_group = $ex_data[1];
    $store_name = $ex_data[2];
    $company = $ex_data[3];
	$item_code_name = str_replace("'","",$ex_data[4]);
	$txt_prod_id = str_replace("'","",$ex_data[5]);
	$store_id = $ex_data[6];
	$variable_lot = $ex_data[7];
	$item_description = $ex_data[8];
	$sql_rackWiseBalanceShow=sql_select("select id, rack_balance from variable_settings_inventory where company_name=$company and item_category_id=8 and variable_list=21 and status_active=1 and is_deleted=0");
	$varriable_setting_rack_self_maintain=$sql_rackWiseBalanceShow[0][csf('rack_balance')];
	if ($varriable_setting_rack_self_maintain==1) $table_width=1560;
	else $table_width=1260;
	?>
    <table width="<? echo $table_width; ?>" cellspacing="0" cellpadding="0" border="1" class="rpt_table" id="" rules="all" >
        <thead>
			<tr>
				<th colspan="<?=($varriable_setting_rack_self_maintain==1)?20:15;?>"><?php echo create_drop_down("cbo_string_search_type", 130, $string_search_type, '', 1, "-- Searching Type --",4); ?></th>
			</tr>
            <tr>
                <th width="30">SL</th>
                <th width="60">Prod Id</th>
                <th width="80">Current Stock</th>
                <th width="70">Re-Order level</th>
                <th width="80">Item Category</th>
                <th width="100">Item Group</th>
                <th width="80">Sub Group</th>
                <th width="80">Item Code</th>
                <th width="80">Item Number</th>
                <th width="80">Item Size</th>
                <th width="80">Model</th>
                <th width="180">Description</th>
                <th width="40">UOM</th>
                <th width="110">Store Name</th>
                <?
	            if ($varriable_setting_rack_self_maintain==1)
	            {	
	            	?>
	                <th width="60">Floor</th>
	                <th width="60">Room</th>
	                <th width="60">Rack</th>
	                <th width="60">Shelf</th>
	                <th width="60">Bin/Box</th>
	                <?
	            }
	            ?> 
                <th>Lot</th>                
            </tr>
        </thead>
    </table>
    <div style="width:<? echo $table_width; ?>px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;" id="scroll_body">
    <table width="<? echo $table_width-20; ?>" cellspacing="0" cellpadding="0" border="1" class="rpt_table" id="tbl_serial" rules="all">
        <tbody>
            <?
            $entry_cond="";
			if(str_replace("'","",$item_category_id)==4) $entry_cond="and b.entry_form=20";
            if ($item_category_id!=0) $item_category_sql=" and a.item_category=$item_category_id and b.item_category_id=$item_category_id"; else { echo "Please Select item category."; die; };
			//echo $item_category_sql."=".$item_category_id."=".$variable_lot;die;
            if( $item_group!=0 )  $item_group=" and b.item_group_id='$item_group'"; else $item_group="";
            if( $store_name!=0 )  $store_name=" and a.store_id='$store_name'"; else $store_name="";
			if( $item_code_name!="" )  $item_code_cond=" and b.item_code='$item_code_name'"; else $item_code_cond="";
			if( $txt_prod_id!="" )  $prod_cond=" and b.id='$txt_prod_id'"; else $prod_cond="";
			if( $store_id>0 )  $store_cond=" and a.store_id='$store_id'"; else $store_cond="";
			if($company)  $store_cond.=" and b.company_id='$company'";
			if( $item_description!="" )  $item_description_cond=" and b.item_description like '%$item_description%'"; else $item_description_cond="";
			//echo $store_cond.jahid;die;

			/*.............. new dev.................*/
			if($item_category_id==22 && $variable_lot==1)
			{
				$sql="SELECT  b.id as id, (sum(case when a.transaction_type in(1,4,5) then a.cons_quantity else 0 end) - sum(case when a.transaction_type in(2,3,6) then a.cons_quantity else 0 end)) as balance_stock, sum(case when a.transaction_type in(1,4,5) then a.balance_qnty else 0 end) as receive, sum(case when a.transaction_type in(2,3,6) then a.balance_qnty else 0 end) as issue, b.current_stock, b.item_category_id, b.item_group_id,b.sub_group_name, b.item_size, b.model, b.item_number, $concat(b.item_description $concat_coma ',' $concat_coma b.item_size) as des, a.store_id as store_id, b.item_code, b.brand_name, b.origin, b.model, a.floor_id, a.room, a.rack, a.self, a.bin_box, b.unit_of_measure as order_uom, a.batch_lot, b.re_order_label
				from  inv_transaction a, product_details_master b
				where a.prod_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,3) and b.is_deleted=0 $item_category_sql $item_group $store_name $item_code_cond $entry_cond $prod_cond  $store_cond $item_description_cond
				group by a.store_id,b.id,b.item_category_id,b.item_group_id,b.sub_group_name,b.item_description, b.item_number, b.item_size,b.current_stock,b.item_code,b.brand_name,b.origin,b.model, a.floor_id, a.room, a.rack, a.self, a.bin_box, b.unit_of_measure, a.batch_lot, b.re_order_label
				having sum(case when a.transaction_type in(1,4,5) then a.cons_quantity else 0 end) - sum(case when a.transaction_type in(2,3,6) then a.cons_quantity else 0 end)>0";
			}
			else
			{
				$sql="SELECT  b.id as id, (sum(case when a.transaction_type in(1,4,5) then a.cons_quantity else 0 end) - sum(case when a.transaction_type in(2,3,6) then a.cons_quantity else 0 end)) as balance_stock, sum(case when a.transaction_type in(1,4,5) then a.balance_qnty else 0 end) as receive, sum(case when a.transaction_type in(2,3,6) then a.balance_qnty else 0 end) as issue, b.current_stock, b.item_category_id, b.item_group_id,b.sub_group_name, b.item_size, b.model, b.item_number, $concat(b.item_description $concat_coma ',' $concat_coma b.item_size) as des, a.store_id as store_id,b.item_code,b.brand_name,b.origin,b.model, a.floor_id, a.room, a.rack, a.self, a.bin_box, b.unit_of_measure as order_uom, null as batch_lot, b.re_order_label
				from  inv_transaction a, product_details_master b
				where a.prod_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,3) and b.is_deleted=0 $item_category_sql $item_group $store_name $item_code_cond $entry_cond $prod_cond  $store_cond $item_description_cond
				group by a.store_id,b.id,b.item_category_id,b.item_group_id,b.sub_group_name,b.item_description, b.item_number, b.item_size,b.current_stock,b.item_code,b.brand_name,b.origin,b.model, a.floor_id, a.room, a.rack, a.self, a.bin_box, b.unit_of_measure, b.re_order_label
				having sum(case when a.transaction_type in(1,4,5) then a.cons_quantity else 0 end) - sum(case when a.transaction_type in(2,3,6) then a.cons_quantity else 0 end)>0";
			}
			
			//echo $sql;
			$itemgroup_arr = return_library_array("select id,item_name from lib_item_group where item_category not in (1,2,3,5,6,7,12,13,14) and status_active=1 and is_deleted=0",'id','item_name');
            $store_arr = return_library_array("select id,store_name from lib_store_location where company_id=$company and status_active=1 and is_deleted=0 order by store_name",'id','store_name');
            $floor_room_rack_arr = return_library_array("select floor_room_rack_id,floor_room_rack_name from lib_floor_room_rack_mst where company_id=$company and status_active=1 and is_deleted=0 order by floor_room_rack_name",'floor_room_rack_id','floor_room_rack_name');
            $arr=array(0=>$item_category,1=>$itemgroup_arr,3=>$store_arr);
            $result=sql_select($sql);
            $i=1;
            foreach($result as $row)
            {
				if ($i%2==0)$bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($row[csf('balance_stock')]<$row[csf('re_order_label')]) $bgcolor="#FF0000";
				?>
				<input type="hidden" id="item_description_all" value="" style=" width:300px;" />
				<tr bgcolor="<? echo $bgcolor; ?>"  onClick='js_set_value("<? echo $row[csf('id')]; ?>*<? echo $row[csf('des')] ;?>*<? echo number_format($row[csf('balance_stock')],4,'.','');  //echo ($row[csf('receive')]-$row[csf('issue')]) ; ?>*<? echo $row[csf('item_category_id')] ; ?>*<? echo $row[csf('item_group_id')] ; ?>*<? echo $row[csf('store_id')] ; ?>*<? echo $row[csf('brand_name')] ; ?>*<? echo $row[csf('origin')] ; ?>*<? echo $row[csf('model')] ; ?>*<? echo $row[csf('floor_id')] ; ?>*<? echo $row[csf('room')] ; ?>*<? echo $row[csf('rack')] ; ?>*<? echo $row[csf('self')] ; ?>*<? echo $row[csf('bin_box')] ; ?>*<? echo $row[csf('order_uom')] ; ?>*<? echo $row[csf('batch_lot')] ; ?>*<? echo $row[csf('re_order_label')] ; ?>")' id="" style="cursor:pointer">
					<td width="30" align="center"><? echo $i;  ?></td>
					<td align="center" width="60"><? echo $row[csf('id')]; ?></td>
					<td align="right" width="80"><?  echo number_format($row[csf('balance_stock')],2); ?>&nbsp;</td>
                    <td align="right" width="70"><?  echo number_format($row[csf('re_order_label')],2); ?>&nbsp;</td>
					<td width="80" ><? echo $item_category[$row[csf('item_category_id')]]; ?></td>
					<td width="100"><? echo $itemgroup_arr[$row[csf('item_group_id')]] ; ?></td>
					<td width="80"><? echo $row[csf('sub_group_name')] ; ?></td>
					<td width="80"><? echo $row[csf('item_code')] ; ?></td>
					<td width="80"><? echo $row[csf('item_number')] ; ?></td>
                    <td width="80"><? echo $row[csf('item_size')] ; ?></td>
                    <td width="80"><? echo $row[csf('model')] ; ?></td>
					<td width="180"><? echo $row[csf('des')] ; ?></td>
					<td width="40"><? echo $unit_of_measurement[$row[csf('order_uom')]] ; ?></td>
					<td width="110"><? echo $store_arr[$row[csf('store_id')]] ; ?></td>
					<?
		            if ($varriable_setting_rack_self_maintain==1)
		            {	
		            	?>
						<td width="60"><? echo $floor_room_rack_arr[$row[csf('floor_id')]] ; ?></td>
						<td width="60"><? echo $floor_room_rack_arr[$row[csf('room')]] ; ?></td>
						<td width="60"><? echo $floor_room_rack_arr[$row[csf('rack')]] ; ?></td>
						<td width="60"><? echo $floor_room_rack_arr[$row[csf('self')]] ; ?></td>
						<td width="60"><? echo $floor_room_rack_arr[$row[csf('bin_box')]] ; ?></td>
						<?
					}
					?>	
                    <td><? echo $row[csf('batch_lot')] ; ?></td>					
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


if ($action=="create_item_search_list_view_req")
{
	?>
    <script>
	function js_set_value(item_description)
	{
  		 $("#item_description_all").val(item_description);
  		//$("#item_description_all").val('lktoilix sdoi;f il;of opod loiioo;potg09p pgsaos 1205 050');
 		parent.emailwindow.hide();
	}
	</script>
    <?
	
	extract($_REQUEST);
	echo load_html_head_contents("Item popup", "../../../", 1, 1,'','1','');
    $item_category_id = str_replace("'","",$item_category_id);
    $store_name = str_replace("'","",$stores);
    $company = str_replace("'","",$company_id);
	$txt_prod_id = str_replace("'","",$prod_id);
	$variable_lot =  str_replace("'","",$variable_lot);
	$sql_rackWiseBalanceShow=sql_select("select id, rack_balance from variable_settings_inventory where company_name=$company and item_category_id=8 and variable_list=21 and status_active=1 and is_deleted=0");
	$varriable_setting_rack_self_maintain=$sql_rackWiseBalanceShow[0][csf('rack_balance')];
	if ($varriable_setting_rack_self_maintain==1) $table_width=1560;
	else $table_width=1260;
	?>
    <table width="<? echo $table_width; ?>" cellspacing="0" cellpadding="0" border="1" class="rpt_table" id="" rules="all" >
        <thead>
			<tr>
				<th colspan="<?=($varriable_setting_rack_self_maintain==1)?20:15;?>"><?php echo create_drop_down("cbo_string_search_type", 130, $string_search_type, '', 1, "-- Searching Type --",4); ?></th>
			</tr>
            <tr>
                <th width="30">SL</th>
                <th width="60">Prod Id</th>
                <th width="80">Current Stock</th>
                <th width="70">Re-Order level</th>
                <th width="80">Item Category</th>
                <th width="100">Item Group</th>
                <th width="80">Sub Group</th>
                <th width="80">Item Code</th>
                <th width="80">Item Number</th>
                <th width="80">Item Size</th>
                <th width="80">Model</th>
                <th width="180">Description</th>
                <th width="40">UOM</th>
                <th width="110">Store Name</th>
                <?
	            if ($varriable_setting_rack_self_maintain==1)
	            {	
	            	?>
	                <th width="60">Floor</th>
	                <th width="60">Room</th>
	                <th width="60">Rack</th>
	                <th width="60">Shelf</th>
	                <th width="60">Bin/Box</th>
	                <?
	            }
	            ?> 
                <th>Lot</th>                
            </tr>
        </thead>
    </table>
    <div style="width:<? echo $table_width; ?>px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;" id="scroll_body">
    <table width="<? echo $table_width-20; ?>" cellspacing="0" cellpadding="0" border="1" class="rpt_table" id="tbl_serial" rules="all">
        <tbody>
            <?
            $entry_cond="";
			if(str_replace("'","",$item_category_id)==4) $entry_cond="and b.entry_form=20";
            if ($item_category_id!=0) $item_category_sql=" and a.item_category=$item_category_id and b.item_category_id=$item_category_id"; else { echo "Please Select item category."; die; };
            if( $store_name!=0 )  $store_name=" and a.store_id='$store_name'"; else $store_name="";
			if( $txt_prod_id!="" )  $prod_cond=" and b.id='$txt_prod_id'"; else $prod_cond="";
			if($company)  $store_cond.=" and b.company_id='$company'";
			//echo $store_cond.jahid;die;
			/*.............. new dev.................*/
			if($item_category_id==22 && $variable_lot==1)
			{
				$sql="SELECT  b.id as id, (sum(case when a.transaction_type in(1,4,5) then a.cons_quantity else 0 end) - sum(case when a.transaction_type in(2,3,6) then a.cons_quantity else 0 end)) as balance_stock, sum(case when a.transaction_type in(1,4,5) then a.balance_qnty else 0 end) as receive, sum(case when a.transaction_type in(2,3,6) then a.balance_qnty else 0 end) as issue, b.current_stock, b.item_category_id, b.item_group_id,b.sub_group_name, b.item_size, b.model, b.item_number, $concat(b.item_description $concat_coma ',' $concat_coma b.item_size) as des, a.store_id as store_id, b.item_code, b.brand_name, b.origin, b.model, a.floor_id, a.room, a.rack, a.self, a.bin_box, b.unit_of_measure as order_uom, a.batch_lot, b.re_order_label
				from  inv_transaction a, product_details_master b
				where a.prod_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,3) and b.is_deleted=0 $item_category_sql $item_group $store_name $item_code_cond $entry_cond $prod_cond  $store_cond $item_description_cond
				group by a.store_id,b.id,b.item_category_id,b.item_group_id,b.sub_group_name,b.item_description, b.item_number, b.item_size,b.current_stock,b.item_code,b.brand_name,b.origin,b.model, a.floor_id, a.room, a.rack, a.self, a.bin_box, b.unit_of_measure, a.batch_lot, b.re_order_label
				having sum(case when a.transaction_type in(1,4,5) then a.cons_quantity else 0 end) - sum(case when a.transaction_type in(2,3,6) then a.cons_quantity else 0 end)>0";
			}
			else
			{
				$sql="SELECT  b.id as id, (sum(case when a.transaction_type in(1,4,5) then a.cons_quantity else 0 end) - sum(case when a.transaction_type in(2,3,6) then a.cons_quantity else 0 end)) as balance_stock, sum(case when a.transaction_type in(1,4,5) then a.balance_qnty else 0 end) as receive, sum(case when a.transaction_type in(2,3,6) then a.balance_qnty else 0 end) as issue, b.current_stock, b.item_category_id, b.item_group_id,b.sub_group_name, b.item_size, b.model, b.item_number, $concat(b.item_description $concat_coma ',' $concat_coma b.item_size) as des, a.store_id as store_id,b.item_code,b.brand_name,b.origin,b.model, a.floor_id, a.room, a.rack, a.self, a.bin_box, b.unit_of_measure as order_uom, null as batch_lot, b.re_order_label
				from  inv_transaction a, product_details_master b
				where a.prod_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,3) and b.is_deleted=0 $item_category_sql $item_group $store_name $item_code_cond $entry_cond $prod_cond  $store_cond $item_description_cond
				group by a.store_id,b.id,b.item_category_id,b.item_group_id,b.sub_group_name,b.item_description, b.item_number, b.item_size,b.current_stock,b.item_code,b.brand_name,b.origin,b.model, a.floor_id, a.room, a.rack, a.self, a.bin_box, b.unit_of_measure, b.re_order_label
				having sum(case when a.transaction_type in(1,4,5) then a.cons_quantity else 0 end) - sum(case when a.transaction_type in(2,3,6) then a.cons_quantity else 0 end)>0";
			}
			
			//echo $sql;
			$itemgroup_arr = return_library_array("select id,item_name from lib_item_group where item_category not in (1,2,3,5,6,7,12,13,14) and status_active=1 and is_deleted=0",'id','item_name');
            $store_arr = return_library_array("select id,store_name from lib_store_location where company_id=$company and status_active=1 and is_deleted=0 order by store_name",'id','store_name');
            $floor_room_rack_arr = return_library_array("select floor_room_rack_id,floor_room_rack_name from lib_floor_room_rack_mst where company_id=$company and status_active=1 and is_deleted=0 order by floor_room_rack_name",'floor_room_rack_id','floor_room_rack_name');
            $arr=array(0=>$item_category,1=>$itemgroup_arr,3=>$store_arr);
            $result=sql_select($sql);
            $i=1;
            foreach($result as $row)
            {
				if ($i%2==0)$bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($row[csf('balance_stock')]<$row[csf('re_order_label')]) $bgcolor="#FF0000";
				?>
				<input type="hidden" id="item_description_all" value="" style=" width:300px;" />
				<tr bgcolor="<? echo $bgcolor; ?>"  onClick='js_set_value("<? echo $row[csf('id')]; ?>*<? echo $row[csf('des')] ;?>*<? echo number_format($row[csf('balance_stock')],4,'.','');  //echo ($row[csf('receive')]-$row[csf('issue')]) ; ?>*<? echo $row[csf('item_category_id')] ; ?>*<? echo $row[csf('item_group_id')] ; ?>*<? echo $row[csf('store_id')] ; ?>*<? echo $row[csf('brand_name')] ; ?>*<? echo $row[csf('origin')] ; ?>*<? echo $row[csf('model')] ; ?>*<? echo $row[csf('floor_id')] ; ?>*<? echo $row[csf('room')] ; ?>*<? echo $row[csf('rack')] ; ?>*<? echo $row[csf('self')] ; ?>*<? echo $row[csf('bin_box')] ; ?>*<? echo $row[csf('order_uom')] ; ?>*<? echo $row[csf('batch_lot')] ; ?>*<? echo $row[csf('re_order_label')] ; ?>")' id="" style="cursor:pointer">
					<td width="30" align="center"><? echo $i;  ?></td>
					<td align="center" width="60"><? echo $row[csf('id')]; ?></td>
					<td align="right" width="80"><?  echo number_format($row[csf('balance_stock')],2); ?>&nbsp;</td>
                    <td align="right" width="70"><?  echo number_format($row[csf('re_order_label')],2); ?>&nbsp;</td>
					<td width="80" ><? echo $item_category[$row[csf('item_category_id')]]; ?></td>
					<td width="100"><? echo $itemgroup_arr[$row[csf('item_group_id')]] ; ?></td>
					<td width="80"><? echo $row[csf('sub_group_name')] ; ?></td>
					<td width="80"><? echo $row[csf('item_code')] ; ?></td>
					<td width="80"><? echo $row[csf('item_number')] ; ?></td>
                    <td width="80"><? echo $row[csf('item_size')] ; ?></td>
                    <td width="80"><? echo $row[csf('model')] ; ?></td>
					<td width="180"><? echo $row[csf('des')] ; ?></td>
					<td width="40"><? echo $unit_of_measurement[$row[csf('order_uom')]] ; ?></td>
					<td width="110"><? echo $store_arr[$row[csf('store_id')]] ; ?></td>
					<?
		            if ($varriable_setting_rack_self_maintain==1)
		            {	
		            	?>
						<td width="60"><? echo $floor_room_rack_arr[$row[csf('floor_id')]] ; ?></td>
						<td width="60"><? echo $floor_room_rack_arr[$row[csf('room')]] ; ?></td>
						<td width="60"><? echo $floor_room_rack_arr[$row[csf('rack')]] ; ?></td>
						<td width="60"><? echo $floor_room_rack_arr[$row[csf('self')]] ; ?></td>
						<td width="60"><? echo $floor_room_rack_arr[$row[csf('bin_box')]] ; ?></td>
						<?
					}
					?>	
                    <td><? echo $row[csf('batch_lot')] ; ?></td>					
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


if($action=="item_code_popup")
{
	echo load_html_head_contents("Item popup", "../../../", 1, 1,'','1','');
	extract($_REQUEST);
	?>
    <script>
	function js_set_value(str)
	{
  		$("#item_id").val(str);
 		parent.emailwindow.hide();
	}
	</script>
    <input type="hidden" id="item_id" name="item_id">
    <?
	$cbo_item_category=str_replace("'","",$cbo_item_category);
	$sql="select id, product_name_details, item_code from product_details_master where item_category_id='$cbo_item_category'";
	//echo $sql="selece id, product_name_details, item_code from product_details_master where item_category_id='$cbo_item_category'";
	echo create_list_view ( "list_view","Item Description,Item Code", "200","390","200",0, $sql, "js_set_value", "id,item_code", "", 1, "0,0", $arr, "product_name_details,item_code", "0,0", 'setFilterGrid("list_view",-1);');
}








if($action=="serial_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	//echo $txt_received_id; die;
	//echo $current_prod_id; die;

 	$serialStringID = str_replace("'","",$serialStringID);
 	//$serialStringNo = str_replace("'","",$serialStringNo);
	$txt_received_id = str_replace("'","",$txt_received_id);
	$current_prod_id = str_replace("'","",$current_prod_id);

 	?>
	<script>
	var selected_id = new Array();
	var selected_no = new Array();


	var serialNoArr="<? echo $serialStringID; ?>";
 	var chk_selected_no = new Array();
	var chk_selected_id = new Array();
	if(serialNoArr!=""){chk_selected_no=serialNoArr.split(",");}



	function check_all_data()
	{
		var tbl_row_count = document.getElementById( 'hidden_all_id' ).value.split(",");
 		//tbl_row_count = tbl_row_count-1;
		for( var i = 0; i < tbl_row_count.length; i++ ) {
 			if( jQuery.inArray( $('#txt_serial_id' + tbl_row_count[i]).val(), chk_selected_id ) != -1 )
			js_set_value( tbl_row_count[i] );
		}
	}

	function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
				//x.style.backgroundColor = ( $serialStringID != "")? newColor : origColor;
			}
		}

	function js_set_value( str ) { //alert(str);
		toggle( document.getElementById( 'search' + str ), '#FFFFCC' );

		if( jQuery.inArray( $('#txt_serial_id' + str).val(), selected_id ) == -1 ) {
			selected_id.push( $('#txt_serial_id' + str).val() );
			selected_no.push( $('#txt_serial_no' + str).val() );
 		}
		else {
			for( var i = 0; i < selected_id.length; i++ ) {
				if( selected_id[i] == $('#txt_serial_id' + str).val() ) break;
			}
			selected_id.splice( i, 1 );
			selected_no.splice( i, 1 );
		}
		var id = '';	var no = '';
		for( var i = 0; i < selected_id.length; i++ ) {
			id += selected_id[i] + ',';
			no += selected_no[i] + ',';
		}
		id = id.substr( 0, id.length - 1 );
		no = no.substr( 0, no.length - 1 );
  		$('#txt_string_id').val( id );
		$('#txt_string_no').val( no );
	}

	function fn_onClosed()
	{
		var txt_string = $('#txt_string').val();
		if(txt_string==""){ alert("Please Select The Serial"); return;}
		parent.emailwindow.hide();
	}

	</script>
	</head>
	<body>
	<div align="center" style="width:100%;" >
	<form name="searchlcfrm_1" id="searchlcfrm_1" autocomplete="off">
    	<table width="300" cellspacing="0" cellpadding="0" border="0" class="rpt_table" id="tbl_header" >
				<thead>
					<tr>
						<th width="300">Serial No</th>
 					</tr>
				</thead>
        </table>
        <div style="width:300px; min-height:220px">
		<table width="300" cellspacing="0" cellpadding="0" border="0" class="rpt_table" id="tbl_serial" style="overflow:scroll; min-height:200px" >
 				<tbody>
                	<?
						$i=1;
						$sql="select id,serial_no from inv_serial_no_details where prod_id=$current_prod_id and is_issued=0";
						//echo $sql;die;
						$result = sql_select($sql);
						$count=count($result );
						foreach($result as $row)
						{
							if ($i%2==0)$bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							if($new_data=="") $new_data=$row[csf("id")]; else $new_data .=",".$row[csf("id")];
						?>
							<tr bgcolor="<? echo $bgcolor; ?>"  onClick="js_set_value(<? echo $row[csf("id")]; ?>)" id="search<? echo $row[csf("id")]; ?>" style="cursor:pointer">
								<td  width="300">
									<? echo trim($row[csf("serial_no")]); ?>
									<input type="hidden" id="txt_serial_id<? echo $row[csf("id")]; ?>" value="<? echo $row[csf("id")]; ?>" >
                                    <input type="hidden" id="txt_serial_no<? echo $row[csf("id")]; ?>" value="<? echo $row[csf("serial_no")]; ?>" >
								</td>
									<?

									if($count==$i)
									{
									?>
                                    <input type="hidden" id="hidden_all_id" value="<? echo $new_data; ?>" >
                                    <? } ?>
							</tr>
					<?

							$i++;
						}

				?>
				</tbody>
			</table>
            </div>
            <div><input type="button" name="btn_close" class="formbutton" style="width:100px" value="Close" onClick="fn_onClosed()" /></div>
            <!-- Hidden field here-->
			<input type="hidden" id="txt_string_id" value="" />
            <input type="hidden" id="txt_string_no" value="" />
			<!-- -END-->
			</form>
	   </div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>

    <script>
	//alert(serialNoArr);
		if( serialNoArr!="" )
		{
			serialNoArr=serialNoArr.split(",");
			for(var k=0;k<serialNoArr.length; k++)
			{
				js_set_value(serialNoArr[k] );
				//alert(serialNoArr[k]);
			}
		}
	</script>
	</html>
	<?
}
if($action=="order_popup")
{
echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
extract($_REQUEST);
$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer where status_active=1 and is_deleted=0 order by buyer_name",'id','buyer_name');
?>
	
<script>
	$(document).ready(function(e) {
		$("#txt_search_common").focus();
	});

	function search_populate(str)
	{
		if(str==0)
		{
			document.getElementById('search_by_th_up').innerHTML="Order No";
			document.getElementById('search_by_td').innerHTML='<input	type="text"	name="txt_search_common" style="width:230px " class="text_boxes" id="txt_search_common"	value=""  />';
		}
		else if(str==1)
		{
			document.getElementById('search_by_th_up').innerHTML="Style Ref. Number";
			document.getElementById('search_by_td').innerHTML='<input	type="text"	name="txt_search_common" style="width:230px " class="text_boxes" id="txt_search_common"	value=""  />';
		}
		else if(str==2)
		{
			var buyer_name = '<option value="0">--- Select Buyer ---</option>';
			<?
			foreach($buyer_arr as $key=>$val)
			{
				echo "buyer_name += '<option value=\"$key\">".($val)."</option>';";
			}
			?>
			document.getElementById('search_by_th_up').innerHTML="Select Buyer Name";
			document.getElementById('search_by_td').innerHTML='<select	name="txt_search_common" style="width:230px " class="combo_boxes" id="txt_search_common">'+ buyer_name +'</select>';
		}
		else if(str==3) {
			document.getElementById('search_by_th_up').innerHTML="Reference No";
			document.getElementById('search_by_td').innerHTML='<input type="text" name="txt_search_common" style="width:230px " class="text_boxes" id="txt_search_common" value="" />';
		}
		else if(str==4) {
			document.getElementById('search_by_th_up').innerHTML="File No";
			document.getElementById('search_by_td').innerHTML='<input type="text" name="txt_search_common" style="width:230px " class="text_boxes" id="txt_search_common" value="" />';
		}
	}
	

	//function js_set_value(id,po_no,seq_no)
	//{
		//$("#hidden_string").val(id+"_"+po_no);
   		//parent.emailwindow.hide();
 	//}
	
	function fnc_close()
	{
		var save_string='';	 var tot_trims_qnty=''; var po_id_array = new Array(); var po_no='';
		
		$("#tbl_po_list").find('tbody tr').not(':first').each(function()
		{
			var txtPoId=$(this).find('input[name="txtPoId[]"]').val();
			var txtPoName=$(this).find('input[name="txtPoName[]"]').val();
			var txtRecvQnty=$(this).find('input[name="txtPropQnty[]"]').val();
			var txtGmtsQnty=$(this).find('input[name="txtGmtsQnty[]"]').val();
			
			tot_trims_qnty=tot_trims_qnty*1+txtRecvQnty*1;
			
			if(txtRecvQnty*1>0)
			{
				if(save_string=="")
				{
					save_string=txtPoId+"_"+txtRecvQnty+"_"+txtGmtsQnty;
				}
				else
				{
					save_string+=","+txtPoId+"_"+txtRecvQnty+"_"+txtGmtsQnty;
				}
				
				if(jQuery.inArray( txtPoId, po_id_array) == -1 ) 
				{
					po_id_array.push(txtPoId);
					if(po_no=="") po_no=txtPoName; else po_no+=","+txtPoName;
				}
			}
		});
		
		if(save_string!="")
		{
			$('#save_string').val( save_string );
			$('#tot_rcv_qnty').val( tot_trims_qnty.toFixed(2));
			$('#all_po_id').val( po_id_array );
			$('#all_po_no').val( po_no );
		}
		parent.emailwindow.hide();
	}
	
</script>
</head>
<body>
<div align="center" style="width:100%;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	<table width="750" cellspacing="0" cellpadding="0" class="rpt_table" align="center" border="1" rules="all">
    		<tr>
        		<td align="center" width="100%">
            		<table  cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
                   		<thead>
                        	<th width="130">Search By</th>
                        	<th  width="180" align="center" id="search_by_th_up">Enter Order Number</th>
                        	<th width="200">Shipment Date Range</th>
                        	<th width="80">
                            <input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" />
                            <input type="hidden" name="save_string" id="save_string" class="text_boxes" value="">
                            <input type="hidden" name="tot_rcv_qnty" id="tot_rcv_qnty" class="text_boxes" value="">
                            <input type="hidden" name="all_po_id" id="all_po_id" class="text_boxes" value="">
                            <input type="hidden" name="all_po_no" id="all_po_no" class="text_boxes" value="">
                            </th>
                    	</thead>
        				<tr>
                    		<td width="130">
							<?
							$searchby_arr=array(0=>'Order No',1=>'Style Ref. Number',2=>'Buyer Name',3=>'Ref No',4=>'File No');
							echo create_drop_down( "txt_search_by", 130, $searchby_arr,"", 1, "-- Select --", $selected, "search_populate(this.value)",0 );
  							?>
                    		</td>
                   			<td width="180" align="center" id="search_by_td">
								<input type="text" style="width:230px" class="text_boxes"  name="txt_search_common" id="txt_search_common" onKeyDown="if (event.keyCode == 13) document.getElementById('btn_show').click()" />
            				</td>
                    		<td align="center">
                            	<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px"> To
					  			<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
					 		</td>
            		 		<td align="center">
                     			<input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_by').value+'*'+document.getElementById('txt_search_common').value+'*'+document.getElementById('txt_date_from').value+'*'+document.getElementById('txt_date_to').value+'*'+<? echo $company; ?>+'*'+'<? echo $txt_order_ref; ?>', 'create_po_search_list_view', 'search_div', 'general_item_issue_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:100px;" />
                            </td>
        				</tr>
             		</table>
          		</td>
        	</tr>
            <tr>
            	<td height="20" valign="middle"><?php echo load_month_buttons(1); ?></td>
            </tr>
    </table>
    <div id="search_div"></div>
    </form>
	</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_po_search_list_view")
{
 	$ex_data = explode("*",$data);
	$txt_search_by = $ex_data[0];
	$txt_search_common = $ex_data[1];
	$txt_date_from = $ex_data[2];
	$txt_date_to = $ex_data[3];
	$company = $ex_data[4];
	$order_ref = $ex_data[5];
	
	//echo $order_ref;
	$order_ref_arr=explode(",",$order_ref);
	$order_qnty_arr=array();
	foreach($order_ref_arr as $ord_val)
	{
		$ord_val_arr=explode("_",$ord_val);
		$order_qnty_arr[$ord_val_arr[0]][1]=$ord_val_arr[1];
		$order_qnty_arr[$ord_val_arr[0]][2]=$ord_val_arr[2];
	}

	$sql_cond="";
	if(trim($txt_search_common)!="")
	{
		if(trim($txt_search_by)==0)
			$sql_cond = " and b.po_number like '%".trim($txt_search_common)."%'";
		else if(trim($txt_search_by)==1)
			$sql_cond = " and a.style_ref_no like '%".trim($txt_search_common)."%'";
		else if(trim($txt_search_by)==2)
			$sql_cond = " and a.buyer_name=trim('$txt_search_common')";
		else if(trim($txt_search_by)==3)
			$sql_cond = " and b.grouping like '%".trim($txt_search_common)."%'";
		else if(trim($txt_search_by)==4)
			$sql_cond = " and b.file_no like '%".trim($txt_search_common)."%'";
 	}
	if ($txt_date_from!='' &&  $txt_date_to!='') {
		if($db_type==0) {
			$sql_cond .= "and b.shipment_date between '".change_date_format($txt_date_from, "yyyy-mm-dd", "-")."' and '".change_date_format($txt_date_to, "yyyy-mm-dd", "-")."'";
		} else {
			$sql_cond .= "and b.shipment_date between '".change_date_format($txt_date_from, "yyyy-mm-dd", "-",1)."' and '".change_date_format($txt_date_to, "yyyy-mm-dd", "-",1)."'";
		}
	}

	// if($txt_date_from!="" || $txt_date_to!="") $sql_cond .= " and b.shipment_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
	if(trim($company)!="") $sql_cond .= " and a.company_name='$company'";

 	$sql = "select b.id,a.order_uom,a.buyer_name,a.company_name,a.total_set_qnty,a.set_break_down, a.job_no,a.style_ref_no,a.gmts_item_id,a.location_name,b.shipment_date,b.pub_shipment_date,b.po_number,b.po_quantity , b.plan_cut, b.grouping, b.file_no
	from wo_po_details_master a, wo_po_break_down b
	where a.id = b.job_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sql_cond";
	//echo $sql;die;
	$result = sql_select($sql);
 	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	?>
    <div style="width:1020px;">
     	<table cellspacing="0" width="100%" border="1" class="rpt_table" rules="all">
            <thead>
                <th width="30">SL</th>
                <th width="70">Shipment Date</th>
                <th width="120">Order No</th>
                <th width="100">Job</th>
                <th width="120">Buyer</th>
                <th width="120">Style</th>
                <th width="80">Ref No</th>
                <th width="80">File No</th>
                <th width="80">Order Qnty</th>
                <th width="90">Issue Qnty</th>
                <th>Gmts. Qnty</th>
            </thead>
     	</table>
     </div>
     <div style="width:1020px; max-height:220px;overflow-y:scroll;" >
        <table cellspacing="0" width="1000" class="rpt_table" id="tbl_po_list" border="1" rules="all">
			<?
			$i=1;
            foreach( $result as $row )
            {
                if ($i%2==0)  $bgcolor="#E9F3FF";
                else $bgcolor="#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" >
					<td width="30" align="center"><? echo $i; ?>
                    <input type="hidden" name="txtPoId[]" id="txtPoId_<? echo $i; ?>" value="<? echo $row[csf("id")];?>">
                    <input type="hidden" name="txtPoName[]" id="txtPoName_<? echo $i; ?>" value="<? echo $row[csf("po_number")];?>">
                    </td>
					<td width="70" align="center"><p><? echo change_date_format($row[csf("pub_shipment_date")]);?></p></td>
					<td width="120" align="center"><p><? echo $row[csf("po_number")]; ?></p></td>
					<td width="100" align="center"><p><? echo $row[csf("job_no")]; ?></p></td>
					<td width="120"><p><? echo $buyer_arr[$row[csf("buyer_name")]];  ?></p></td>
					<td width="120"><p><? echo $row[csf("style_ref_no")]; ?></p></td>
					<td width="80"><p><? echo $row[csf("grouping")]; ?></p></td>
					<td width="80"><p><? echo $row[csf("file_no")]; ?></p></td>
					<td width="80" align="right"><p><? echo $row[csf("po_quantity")];?> </p></td>
					<td width="90" align="center"><input type="text" id="txtPropQnty_<?=$i;?>" name="txtPropQnty[]" class="text_boxes_numeric" style="width:70px" value="<?= $order_qnty_arr[$row[csf("id")]][1];?>" /> </td>
                    <td align="center"><input type="text" id="txtGmtsQnty_<?=$i;?>" name="txtGmtsQnty[]" class="text_boxes_numeric" style="width:70px" value="<?= $order_qnty_arr[$row[csf("id")]][2];?>" /> </td>
				</tr>
				<?
				$i++;
            }
   			?>
		</table>
            
		</div>
        <table width="1000">
             <tr>
                <td align="center" >
                    <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
                </td>
            </tr>
        </table>
	<?
exit();
}

//data save update delete here------------------------------//
if($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$txt_prod_id=str_replace("'","",$current_prod_id);
    $req_id=str_replace("'", '', $hidden_issue_req_id);
	if($req_id=="") $req_id=0;
	$txt_issue_qnty = str_replace("'","",$txt_issue_qnty);
    /*$cbo_floor=(str_replace("'", "", $cbo_floor)=='')?0:$cbo_floor;
   	$cbo_room=(str_replace("'", "", $cbo_room)=='')?0:$cbo_room;
   	$txt_rack=(str_replace("'", "", $txt_rack)=='')?0:$txt_rack;
   	$txt_shelf=(str_replace("'", "", $txt_shelf)=='')?0:$txt_shelf;
   	$cbo_bin=(str_replace("'", "", $cbo_bin)=='')?0:$cbo_bin;*/
	$variable_store_wise_rate=return_field_value("auto_transfer_rcv","variable_settings_inventory","company_name=$cbo_company_id and variable_list=47 and item_category_id=8 and status_active=1 and is_deleted=0","auto_transfer_rcv");
	if($variable_store_wise_rate != 1) $variable_store_wise_rate=2;
	
	if($req_id > 0)
	{
		$requisition_company_id = return_field_value("company_id", "inv_item_issue_requisition_mst", "id=$req_id", "company_id");
		if($requisition_company_id != str_replace("'", "", $cbo_company_id))
		{
			echo "20**Company must be same of Requisition Company";die;
		}
		$trans_id=str_replace("'","",$update_id);
		$up_cond="";
		if($trans_id!="") $up_cond=" and b.id <> $trans_id";
		$prev_req_rcv=sql_select("select sum(b.cons_quantity) as rcv_qnty from inv_issue_master a, inv_transaction b where a.id=b.mst_id and a.entry_form=21 and b.transaction_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.req_id=$req_id and b.prod_id=$current_prod_id $up_cond");
		$prev_req_qnty=$prev_req_rcv[0][csf("rcv_qnty")];
		
		$sql_req=sql_select("select sum(req_qty) as req_qty from inv_itemissue_requisition_dtls where mst_id=$req_id and product_id=$current_prod_id and status_active=1 and is_deleted=0 ");
		$cu_req_qnty=($sql_req[0][csf("req_qty")]-$prev_req_qnty)*1;
		$issu_qnty=str_replace("'","",$txt_issue_qnty)*1;
		//echo "10** $issu_qnty = $cu_req_qnty";die;
		if($issu_qnty>$cu_req_qnty)
		{
			echo "20**Issue Quantity Not Allow Over Requisition Quantity \n Requisition Balance Quantity=$cu_req_qnty";die;
		}
	}

    $max_recv_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$txt_prod_id and transaction_type in (1,4,5) and status_active = 1", "max_date");
    if($max_recv_date != "")
   	{
		$max_recv_date = strtotime($max_recv_date);
		$issue_date = strtotime(str_replace("'", "", $txt_issue_date));
		if ($issue_date < $max_recv_date)
	    {
            echo "20**Issue Date Can not Be Less Than Last Receive Date Of This Item";
            die;
		}
   	}

   	$sqlCon="";	
	$store_update_upto=str_replace("'","",$store_update_upto);
	if($store_update_upto > 1)
	{
		if($store_update_upto==6)
		{
			if(str_replace("'","",$cbo_floor)!=0){$sqlCon= " and floor_id=$cbo_floor" ;}
			if(str_replace("'","",$cbo_room)!=0){$sqlCon.= " and room=$cbo_room" ;}
			if(str_replace("'","",$txt_rack)!=0){$sqlCon.= " and rack=$txt_rack" ;}
			if(str_replace("'","",$txt_shelf)!=0){$sqlCon.= " and self=$txt_shelf" ;}
			if(str_replace("'","",$cbo_bin)!=0){$sqlCon.= " and bin_box=$cbo_bin" ;}
		}
		else if($store_update_upto==5)
		{
			if(str_replace("'","",$cbo_floor)!=0){$sqlCon= " and floor_id=$cbo_floor" ;}
			if(str_replace("'","",$cbo_room)!=0){$sqlCon.= " and room=$cbo_room" ;}
			if(str_replace("'","",$txt_rack)!=0){$sqlCon.= " and rack=$txt_rack" ;}
			if(str_replace("'","",$txt_shelf)!=0){$sqlCon.= " and self=$txt_shelf" ;}
			$cbo_bin=0;
		}
		else if($store_update_upto==4)
		{
			if(str_replace("'","",$cbo_floor)!=0){$sqlCon= " and floor_id=$cbo_floor" ;}
			if(str_replace("'","",$cbo_room)!=0){$sqlCon.= " and room=$cbo_room" ;}
			if(str_replace("'","",$txt_rack)!=0){$sqlCon.= " and rack=$txt_rack" ;}
			$cbo_bin=0;$txt_shelf=0;
		}
		else if($store_update_upto==3)
		{
			if(str_replace("'","",$cbo_floor)!=0){$sqlCon= " and floor_id=$cbo_floor" ;}
			if(str_replace("'","",$cbo_room)!=0){$sqlCon.= " and room=$cbo_room" ;}
			$cbo_bin=0;$txt_shelf=0;$txt_rack=0;
		}
		else if($store_update_upto==2)
		{
			if(str_replace("'","",$cbo_floor)!=0){$sqlCon= " and floor_id=$cbo_floor" ;}
			$cbo_bin=0;$txt_shelf=0;$txt_rack=0;$cbo_room=0;
		}
	}
	else
	{
		$cbo_bin=0;$txt_shelf=0;$txt_rack=0;$cbo_room=0;$cbo_floor=0;
	}
	
	$variable_lot=str_replace("'","",$variable_lot);
	if($variable_lot==1  && str_replace("'","",$cbo_item_category)==22)
	{
		$sqlCon.= " and batch_lot=$txt_lot" ;
	}
   	
   

	if( $operation==0 ) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		//table lock here
		//if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		//---------------Check Duplicate product in Same return number ------------------------//
		$txt_prod_id=str_replace("'","",$current_prod_id);
		$duplicate = is_duplicate_field("b.id","inv_issue_master a, inv_transaction b","a.id=b.mst_id and a.id=$txt_system_id and b.prod_id=$txt_prod_id and b.transaction_type=2 and b.status_active=1 and b.is_deleted=0");
		if($duplicate==1 && str_replace("'","",$txt_system_no)!="")
		{
			//check_table_status( $_SESSION['menu_id'],0);
			echo "20**Duplicate Product is Not Allow in Same Return Number.";
			disconnect($con);
			die;
		}

		$global_stock_qnty=return_field_value("current_stock as current_stock","product_details_master","status_active in(1,3) and id=$txt_prod_id","current_stock");
		
		//######### this stock item store floor room rack self level ########//
		$store_stock_qnty=return_field_value("sum((case when transaction_type in(1,4,5) then cons_quantity else 0 end) -(case when transaction_type in(2,3,6) then cons_quantity else 0 end)) as balance_stock","inv_transaction","status_active=1 and prod_id=$txt_prod_id and store_id=$cbo_store_name  $sqlCon","balance_stock");
		
		//######### this stock item store level and calculate rate ########//
		$store_stock_sql="select sum((case when transaction_type in(1,4,5) then cons_quantity else 0 end)-(case when transaction_type in(2,3,6) then cons_quantity else 0 end)) as BALANCE_STOCK, sum((case when transaction_type in(1,4,5) then store_amount else 0 end)-(case when transaction_type in(2,3,6) then store_amount else 0 end)) as BALANCE_AMT 
		from inv_transaction 
		where status_active=1 and prod_id=$txt_prod_id and store_id=$cbo_store_name";
		//echo "20**$store_stock_sql";disconnect($con);die;
		$store_stock_sql_result=sql_select($store_stock_sql);
		$store_item_rate=0;
		if($store_stock_sql_result[0]["BALANCE_AMT"]!=0 && $store_stock_sql_result[0]["BALANCE_STOCK"]!=0)
		{
			$store_item_rate=$store_stock_sql_result[0]["BALANCE_AMT"]/$store_stock_sql_result[0]["BALANCE_STOCK"];
		}
		
		if(number_format(str_replace("'","",$txt_issue_qnty),4,'.','')>number_format($store_stock_qnty,4,'.','') || number_format(str_replace("'","",$txt_issue_qnty),4,'.','')>number_format($global_stock_qnty,4,'.',''))
		{
			echo "17**Issue Quantity Exceeds The Current Stock Quantity";
			//check_table_status( $_SESSION['menu_id'],0);
			disconnect($con);
			die;
		}
		
		
		//product master table information
		$sql = sql_select("select avg_rate_per_unit,current_stock,stock_value from product_details_master where id=$txt_prod_id");
		$avg_rate=$stock_qnty=$stock_value=0;
		foreach($sql as $result)
		{
			$avg_rate = $result[csf("avg_rate_per_unit")];
			$stock_qnty = $result[csf("current_stock")];
			$stock_value = $result[csf("stock_value")];
		}
		
		if(number_format($avg_rate,10,'.','')==0)
		{
			echo "20**Rate Not Found.";disconnect($con);die;
		}

 		//issue master table entry here START---------------------------------------//
 		if( str_replace("'","",$txt_system_no) == "" ) //new insert
		{
			$id = return_next_id_by_sequence("INV_ISSUE_MASTER_PK_SEQ", "inv_issue_master", $con);
			$new_mrr_number = explode("*", return_next_id_by_sequence("INV_ISSUE_MASTER_PK_SEQ", "inv_issue_master",$con,1,str_replace("'","",$cbo_company_id),'GIS',21,date("Y",time())));

			$field_array_master="id,issue_number_prefix, issue_number_prefix_num, issue_number, issue_purpose,loan_party, entry_form, company_id, issue_date, challan_no, req_no,knit_dye_source,knit_dye_company,knit_dye_location,remarks,attention, req_id, inserted_by, insert_date";
			$data_array_master="(".$id.",'".$new_mrr_number[1]."','".$new_mrr_number[2]."','".$new_mrr_number[0]."',".$cbo_issue_purpose.",".$cbo_loan_party.", 21,".$cbo_company_id.",".$txt_issue_date.",".$txt_challan_no.",".$txt_issue_req_no.",".$cbo_issue_source.",".$cbo_issue_to.",".$cbo_location_issue_to.",".$txt_remarks.",".$txt_attention.",'".$req_id."','".$user_id."','".$pc_date_time."')";
 		}
		else //update
		{
			$new_mrr_number[0]=str_replace("'","",$txt_system_no);
			$id=str_replace("'","",$txt_system_id);
			$field_array_master="issue_purpose*loan_party*issue_date*challan_no*req_no*knit_dye_source*knit_dye_company*knit_dye_location*remarks*attention*req_id*updated_by*update_date";
			$data_array_master="".$cbo_issue_purpose."*".$cbo_loan_party."*".$txt_issue_date."*".$txt_challan_no."*".$txt_issue_req_no."*".$cbo_issue_source."*".$cbo_issue_to."*".$cbo_location_issue_to."*".$txt_remarks."*".$txt_attention."*'".$req_id."'*'".$user_id."'*'".$pc_date_time."'";
 		}
		//issue master table entry here END---------------------------------------//

		

		//inventory TRANSACTION table data entry START----------------------------------------------------------//
		//$transaction_type=array(1=>"Receive",2=>"Issue",3=>"Receive Return",4=>"Issue Return");
		
 		$issue_stock_value = $avg_rate*$txt_issue_qnty;
		$issue_store_value = $store_item_rate*$txt_issue_qnty;
		//$transactionID = return_next_id("id", "inv_transaction", 1); order_id, ,".$txt_order_id."
		$transactionID = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
		$field_array_trans_insert = "id,mst_id,company_id,prod_id,item_category,transaction_type,transaction_date,store_id,cons_uom,cons_quantity,cons_rate,cons_amount,production_floor,line_id,machine_id,item_return_qty,machine_category,floor_id,room,rack,self,bin_box,location_id,department_id,section_id,division_id,remarks,inserted_by,insert_date,batch_lot,raw_issue_challan,table_no_id,store_rate,store_amount";
 		$data_array_trans_insert = "(".$transactionID.",".$id.",".$cbo_company_id.",".$txt_prod_id.",".$cbo_item_category.",2,".$txt_issue_date.",".$cbo_store_name.",".$cbo_uom.",".$txt_issue_qnty.",".number_format($avg_rate,10,'.','').",".number_format($issue_stock_value,8,'.','').",".$cbo_issue_floor.",".$cbo_sewing_line.",".$cbo_machine_name.",".$txt_return_qty.",".$cbo_machine_category.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_bin.",".$cbo_location.",".$cbo_department.",".$cbo_section.",".$cbo_division.",".$txt_remarks_dtls.",'".$user_id."','".$pc_date_time."',".$txt_lot.",".$txt_entry_no.",".$cbo_table_no.",".number_format($store_item_rate,10,'.','').",".number_format($issue_store_value,8,'.','').")";
		//echo $field_array."<br>".$data_array;die;

		//inventory TRANSACTION table data entry  END----------------------------------------------------------//

		//if LIFO/FIFO then START -----------------------------------------//
		$field_array_lifu_fifu = "id,recv_trans_id,issue_trans_id,entry_form,prod_id,issue_qnty,item_return_qty,rate,amount,inserted_by,insert_date";
		$update_array_lifu_fifu = "balance_qnty*balance_amount*updated_by*update_date";
		$cons_rate=0;
		$data_array_lifu_fifu="";
		$updateID_array_lifu_fifu=array();
		$update_data_lifu_fifu=array();
		$issueQnty = $txt_issue_qnty;
		//$mrrWiseIsID = return_next_id("id", "inv_mrr_wise_issue_details", 1);

		if($db_type==0)
		{
			$returnString=return_field_value("concat(store_method,'_',allocation)","variable_settings_inventory","company_name=$cbo_company_id and variable_list=17 and item_category_id=$cbo_item_category and status_active=1 and is_deleted=0");
		}
		else
		{
			$returnString=return_field_value("(store_method || '_' || allocation) as store_data","variable_settings_inventory","company_name=$cbo_company_id and variable_list=17 and item_category_id=$cbo_item_category and status_active=1 and is_deleted=0","store_data");
		}

		$expString = explode("_",$returnString);
		$isLIFOfifo = $expString[0];
		$check_allocation = $expString[1];

		if($isLIFOfifo==2) $cond_lifofifo=" DESC"; else $cond_lifofifo=" ASC";
		$sql = sql_select("select id,cons_rate,balance_qnty,balance_amount from inv_transaction where prod_id=$txt_prod_id and store_id=$cbo_store_name and balance_qnty>0 and transaction_type in (1,4,5) and item_category=$cbo_item_category order by id $cond_lifofifo");
		foreach($sql as $result)
		{
			$mrrWiseIsID = return_next_id_by_sequence("INV_MRR_WISE_ISSUE_PK_SEQ", "inv_mrr_wise_issue_details", $con);
			$recv_trans_id = $result[csf("id")]; // this row will be updated
			$balance_qnty = $result[csf("balance_qnty")];
			$balance_amount = $result[csf("balance_amount")];
			$cons_rate = $result[csf("cons_rate")];
			$issueQntyBalance = $balance_qnty-$issueQnty; // minus issue qnty
			$issueStockBalance = $balance_amount-($issueQnty*$cons_rate);
			if($issueQntyBalance>=0)
			{
				$amount = $issueQnty*$cons_rate;
				//for insert
				if($data_array_lifu_fifu!="") $data_array_lifu_fifu .= ",";
				$data_array_lifu_fifu .= "(".$mrrWiseIsID.",".$recv_trans_id.",".$transactionID.",21,".$txt_prod_id.",".$issueQnty.",".$txt_return_qty.",".number_format($cons_rate,10,'.','').",".number_format($amount,8,'.','').",'".$user_id."','".$pc_date_time."')";
				//for update
				$updateID_array_lifu_fifu[]=$recv_trans_id;
				$update_data_lifu_fifu[$recv_trans_id]=explode("*",("".$issueQntyBalance."*".$issueStockBalance."*'".$user_id."'*'".$pc_date_time."'"));
				break;
			}
			else if($issueQntyBalance<0)
			{
				$issueQntyBalance  = $issueQnty-$balance_qnty;
				$issueQnty = $balance_qnty;
				$amount = $issueQnty*$cons_rate;
				//for insert
				if($data_array_lifu_fifu!="") $data_array_lifu_fifu .= ",";
				$data_array_lifu_fifu .= "(".$mrrWiseIsID.",".$recv_trans_id.",".$transactionID.",21,".$txt_prod_id.",".$balance_qnty.",".$txt_return_qty.",".number_format($cons_rate,10,'.','').",".number_format($amount,8,'.','').",'".$user_id."','".$pc_date_time."')";
				//for update
				$updateID_array_lifu_fifu[]=$recv_trans_id;
				$update_data_lifu_fifu[$recv_trans_id]=explode("*",("0*0*'".$user_id."'*'".$pc_date_time."'"));
				$issueQnty = $issueQntyBalance;
			}
			//$mrrWiseIsID++;
		}//end foreach
 		// LIFO/FIFO then END-----------------------------------------------//

		$txt_order_ref=str_replace("'","",$txt_order_ref);
		$order_wise_data_array="";
		if($txt_order_ref!="")
		{
			$txt_order_ref_arr=explode(",",$txt_order_ref);
			$order_wise_field_arr="id,trans_id,trans_type,entry_form,po_breakdown_id,prod_id,quantity,rate,amount,gmts_quantity,inserted_by,insert_date,status_active,is_deleted";
			$orderWiseID = return_next_id("id", "order_wise_general_details", 1);
			foreach($txt_order_ref_arr as $val)
			{
				$val_arr=explode("_",$val);
				if($order_wise_data_array!="") $order_wise_data_array .= ",";
				$ord_amount=$avg_rate*$val_arr[1];
				$order_wise_data_array .= "(".$orderWiseID.",".$transactionID.",2,21,".$val_arr[0].",".$txt_prod_id.",".$val_arr[1].",".number_format($store_item_rate,10,'.','').",".number_format($ord_amount,8,'.','').",'".$val_arr[2]."','".$user_id."','".$pc_date_time."',1,0)";
				$orderWiseID++;
			}
		}

		

 		//product master table data UPDATE START----------------------//
  		$currentStock   = $stock_qnty-$txt_issue_qnty;
  		$StockValue	 	= 0;
  		if ( $currentStock != 0 ){
  			$StockValue	 	= $stock_value-($txt_issue_qnty*$avg_rate);
  		}

  		$field_array_product	= "last_issued_qnty*item_return_qty*current_stock*stock_value*updated_by*update_date";
		$data_array_product	= "".$txt_issue_qnty."*".$txt_return_qty."*".$currentStock."*".number_format($StockValue,8,'.','')."*'".$user_id."'*'".$pc_date_time."'";
		
		if($variable_store_wise_rate == 1)
		{
			$sql_store = sql_select("select id, rate as avg_rate_per_unit, cons_qty as current_stock, amount as stock_value from inv_store_wise_gen_qty_dtls where status_active=1 and prod_id=$txt_prod_id and category_id=$cbo_item_category and store_id=$cbo_store_name and company_id=$cbo_company_id");
			$store_up_id=0;
			if(count($sql_store)<1)
			{
				echo "20**No Data Found.";disconnect($con);die;
			}
			elseif(count($sql_store)>1)
			{
				echo "20**Duplicate Product is Not Allow in Same REF Number.";disconnect($con);die;
			}
			else
			{
				$store_presentStock=$store_presentStockValue=$store_presentAvgRate=0;
				foreach($sql_store as $result)
				{
					$store_up_id=$result[csf("id")];
					$store_presentStock	=$result[csf("current_stock")];
					$store_presentStockValue =$result[csf("stock_value")];
					$store_presentAvgRate	=$result[csf("avg_rate_per_unit")];
				}
				
				$field_array_store="last_issued_qnty*cons_qty*amount*updated_by*update_date";
				$currentStock_store		=$store_presentStock-$txt_issue_qnty;
				$currentValue_store		=$store_presentStockValue-$issue_store_value;
				$data_array_store= "".$txt_issue_qnty."*".$currentStock_store."*".number_format($currentValue_store,8,'.','')."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'";
			}
		}
		
		//------------------ product_details_master END--------------//
		$rID=$transID=$prodUpdate=$mrrWiseIssueID=$upTrID=$order_rID=$storeRID=$serialUpdate=true;
		if( str_replace("'","",$txt_system_no) == "" ) //new insert
		{
			$rID = sql_insert("inv_issue_master",$field_array_master,$data_array_master,1);
 		}
		else //update
		{
			$rID=sql_update("inv_issue_master",$field_array_master,$data_array_master,"id",$id,1);
 		}
		$transID = sql_insert("inv_transaction",$field_array_trans_insert,$data_array_trans_insert,1);
		$prodUpdate = sql_update("product_details_master",$field_array_product,$data_array_product,"id",$txt_prod_id,1);
		
		if($data_array_lifu_fifu!="")
		{
			$mrrWiseIssueID=sql_insert("inv_mrr_wise_issue_details",$field_array_lifu_fifu,$data_array_lifu_fifu,1);
		}
		//transaction table stock update here------------------------//
		if(count($updateID_array_lifu_fifu)>0)
		{
 			$upTrID=execute_query(bulk_update_sql_statement("inv_transaction","id",$update_array_lifu_fifu,$update_data_lifu_fifu,$updateID_array_lifu_fifu),1);
		}
		
		if($order_wise_data_array!="")
		{
			$order_rID = sql_insert("order_wise_general_details",$order_wise_field_arr,$order_wise_data_array,1);
		}
		
		if($store_up_id>0 && $variable_store_wise_rate == 1)
		{
			$storeRID=sql_update("inv_store_wise_gen_qty_dtls",$field_array_store,$data_array_store,"id",$store_up_id,1);
		}

 		$txt_serial_id 	= trim(str_replace("'","",$txt_serial_id));
 		if($txt_serial_id!="")
		{
			if( strpos(trim($txt_serial_no), ",")>0)
			{
				$se_data=explode(",",str_replace("'","",$txt_serial_no));
				if( (count($se_data)<=str_replace("'","",$txt_issue_qnty)))
				{
					$field_array_serial="issue_trans_id*is_issued*updated_by*update_date";
					$txt_serial_id_arr=explode(",",$txt_serial_id);
					if(count($txt_serial_id_arr)>0)
					{
						foreach($txt_serial_id_arr as $serial_id)
						{
							$update_data_serial[$serial_id]=explode("*",("".$transactionID."*1*'".$user_id."'*'".$pc_date_time."'"));
						}
					}
					$serialUpdate=execute_query(bulk_update_sql_statement("inv_serial_no_details","id",$field_array_serial,$update_data_serial,$txt_serial_id_arr),1);
				}
				else
				{
					echo "50";disconnect($con);die;
				}
			}
			else
			{
				$field_array_serial="issue_trans_id*is_issued*updated_by*update_date";
				$txt_serial_id_arr=explode(",",$txt_serial_id);
				if(count($txt_serial_id_arr)>0)
				{
					foreach($txt_serial_id_arr as $serial_id)
					{
						$update_data_serial[$serial_id]=explode("*",("".$transactionID."*1*'".$user_id."'*'".$pc_date_time."'"));
					}
				}
				$serialUpdate=execute_query(bulk_update_sql_statement("inv_serial_no_details","id",$field_array_serial,$update_data_serial,$txt_serial_id_arr),1);
			}
		}
		
		
		//echo "10**".$rID." && ".$transID." && ".$mrrWiseIssueID." && ".$upTrID." && ".$prodUpdate." && ".$storeRID." && ".$serialUpdate." && ".$store_up_id;oci_rollback($con);disconnect($con);die;
		//mysql_query("ROLLBACK");die;

		//release lock table   oci_commit($con); oci_rollback($con);
		//check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID && $transID && $mrrWiseIssueID && $upTrID && $prodUpdate && $serialUpdate && $order_rID)
			{
				mysql_query("COMMIT");
				echo "0**".$new_mrr_number[0]."**".$id;
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".$new_mrr_number[0]."**".$id;
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($rID && $transID && $mrrWiseIssueID && $upTrID && $prodUpdate && $serialUpdate && $order_rID)
			{
				oci_commit($con);
				echo "0**".$new_mrr_number[0]."**".$id;
			}
			else
			{
				oci_rollback($con);
				echo "10**".$new_mrr_number[0]."**".$id;
			}
		}
		//check_table_status( $_SESSION['menu_id'],0);
		disconnect($con);
		die;
	}
	else if ($operation==1) // Update Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		//table lock here
		//if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		//check update id
		if( str_replace("'","",$update_id) == "" ||  str_replace("'","",$txt_system_no)=="" )
		{
			echo "10";
			//check_table_status( $_SESSION['menu_id'],0);
			disconnect($con);
			die();
		}
		
		

		if( str_replace("'","",$update_id) != "" ||  str_replace("'","",$txt_system_no)!="" ) 
        {
            $mrr_sql=sql_select("select a.id as mrr_id, a.recv_number,a.recv_number_prefix_num, a.booking_id, b.prod_id, b.cons_quantity, b.order_rate
            from inv_receive_master a, inv_transaction b
            where a.id=b.mst_id and b.transaction_type=4 and a.entry_form=27 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.issue_id=$txt_system_id and b.prod_id=$txt_prod_id");
            if(count($mrr_sql)>0)
            {
                $next_opt_check=1;
                $mrr_data=array();
                foreach($mrr_sql as $row)
                {
                    $mrr_data[$row[csf("prod_id")]]["quantity"]+=$row[csf("cons_quantity")];
                    //$mrr_data[$row[csf("prod_id")]]["rate"]=$row[csf("order_rate")];
                    $mrr_data[$row[csf("prod_id")]]["rcv_no"][$row[csf("recv_number")]]=$row[csf("recv_number_prefix_num")];
                }

                foreach($mrr_data as $prod_id=>$prod_mrr_val)
                {
                    //if($wo_data[$prod_id]["quantity"]<$prod_mrr_val["quantity"])
                    $rcv_no = implode(",", $mrr_data[$prod_id]["rcv_no"]);
                    if( str_replace("'","",$txt_issue_qnty) < $prod_mrr_val["quantity"] )
                    {
                        echo "11**Receive Number Found(".$rcv_no."), Issue Quantity Not Allow Less Then Return Quantity,  \n So Update/Delete Not Possible.**$update_check"; disconnect($con); die();
                    }
                }
            }
        }


		//variable_list=17 is_allocated,  item_category_id=1 is yarn--------------------
		if($db_type==0)
		{
			$returnString=return_field_value("concat(store_method,'_',allocation)","variable_settings_inventory","company_name=$cbo_company_id and variable_list=17 and item_category_id=$cbo_item_category and status_active=1 and is_deleted=0");
		}
		else
		{
			$returnString=return_field_value("(store_method || '_' || allocation) as store_data","variable_settings_inventory","company_name=$cbo_company_id and variable_list=17 and item_category_id=$cbo_item_category and status_active=1 and is_deleted=0","store_data");
		}
		$expString = explode("_",$returnString);
		$isLIFOfifo = $expString[0];
		$check_allocation = $expString[1];
		//****************************************** BEFORE ENTRY ADJUST START *****************************************//
		//product master table information
		//before stock update
		$sql = sql_select( "select a.id,a.avg_rate_per_unit,a.current_stock,a.stock_value, b.cons_quantity,b.item_return_qty, b.cons_amount, b.store_amount from product_details_master a, inv_transaction b where a.id=b.prod_id and b.id=$update_id and b.transaction_type=2 and b.status_active=1 and b.is_deleted=0");

		$before_prod_id=$before_issue_qnty=$before_stock_qnty=$before_stock_value=$before_prod_rate=0;
		foreach($sql as $result)
		{
			$before_prod_id 	= $result[csf("id")];
 			$before_stock_qnty = $result[csf("current_stock")];
			$before_prod_rate = $result[csf("avg_rate_per_unit")];
			$before_stock_value = $result[csf("stock_value")];
			$before_issue_qnty = $result[csf("cons_quantity")];
			$before_return_qty = $result[csf("item_return_qty")];
			$before_issue_value = $result[csf("cons_amount")];
			$before_store_amount = $result[csf("store_amount")];
		}
		//current product ID
		$txt_prod_id = str_replace("'","",$current_prod_id);
		$txt_issue_qnty = str_replace("'","",$txt_issue_qnty);
		
		$sql = sql_select("select avg_rate_per_unit,current_stock,stock_value from product_details_master where id=$txt_prod_id");
		$curr_avg_rate=$curr_stock_qnty=$curr_stock_value=0;
		foreach($sql as $result)
		{
			$curr_avg_rate 	   = $result[csf("avg_rate_per_unit")];
			$curr_stock_qnty 	 = $result[csf("current_stock")];
			$curr_stock_value 	= $result[csf("stock_value")];
		}
		
		$max_transaction_id = return_field_value("max(id) as max_trans_id", "inv_transaction", "prod_id=$before_prod_id and transaction_type in(1,4,5) and status_active = 1", "max_trans_id");
		if($max_transaction_id > str_replace("'","",$update_id))
		{
			echo "20**Next Transaction Found, Update Not Allow";disconnect($con);die;
		}
		
		if(number_format($curr_avg_rate,10,".","")==0)
		{
			echo "20**Rate Not Found.";disconnect($con);die;
		}
		
		//weighted and average rate START here------------------------//
		//product master table data UPDATE START----------------------//
		
		if($before_prod_id==$txt_prod_id)
		{
			$adj_stock_qnty = $curr_stock_qnty+$before_issue_qnty-$txt_issue_qnty; // CurrentStock + Before Issue Qnty - Current Issue Qnty
 			//$adj_stock_val  = $curr_stock_value+$before_issue_value-($txt_issue_qnty*$curr_avg_rate); // CurrentStockValue + Before Issue Value - Current Issue Value
 			$adj_stock_val=0;
 			if ( $adj_stock_qnty != 0 ) {
 				$adj_stock_val  = $adj_stock_qnty*$curr_avg_rate; 				
 			}

 			$update_array_prod	= "last_issued_qnty*item_return_qty*current_stock*stock_value*updated_by*update_date"; //*allocated_qnty*available_qnty
 			$data_array_prod		= "".$txt_issue_qnty."*".$txt_return_qty."*".$adj_stock_qnty."*".number_format($adj_stock_val,8,'.','')."*'".$user_id."'*'".$pc_date_time."'";
			
			//$adj_avgrate	= number_format($adj_stock_val/$adj_stock_qnty,$dec_place[3],'.','');
			
			$global_stock_qnty=return_field_value("current_stock as current_stock","product_details_master","status_active in(1,3) and id=$before_prod_id","current_stock");
			$global_stock_qnty=$global_stock_qnty+$before_issue_qnty;
			$stock_qnty=return_field_value("sum((case when transaction_type in(1,4,5) then cons_quantity else 0 end)-(case when transaction_type in(2,3,6) then cons_quantity else 0 end)) as balance_stock","inv_transaction","status_active=1 and prod_id=$before_prod_id and store_id=$cbo_store_name $sqlCon","balance_stock");
			$latest_current_stock=$stock_qnty+$before_issue_qnty;
			//now current stock
			$curr_avg_rate 		= $curr_avg_rate;
			$curr_stock_qnty 	= $adj_stock_qnty;
			$curr_stock_value 	= $adj_stock_val;
		}
		else
		{
			$updateID_array = $update_data = array();
			$latest_current_stock=return_field_value("sum((case when transaction_type in(1,4,5) then cons_quantity else 0 end)-(case when transaction_type in(2,3,6) then cons_quantity else 0 end)) as balance_stock","inv_transaction","status_active=1 and prod_id=$txt_prod_id and store_id=$cbo_store_name  $sqlCon","balance_stock");
			//$latest_current_stock=return_field_value("current_stock as current_stock","product_details_master","status_active=1 and is_deleted=0 and id=$txt_prod_id","current_stock");
			//before product adjust
			$adj_before_stock_qnty 	= $before_stock_qnty+$before_issue_qnty; // CurrentStock + Before Issue Qnty
			$updateID_array_prod[]=$before_prod_id;
			//$adj_before_avgrate	   = number_format($adj_before_stock_val/$adj_before_stock_qnty,$dec_place[3],'.','');
			$adj_before_stock_val=0;
			if ($adj_before_stock_qnty != 0 ){
				$adj_before_stock_val  = $before_stock_value+$before_issue_value; // CurrentStockValue + Before Issue Value
				$adj_before_avgrate	   = $before_prod_rate;				
			} 

			$update_array_prod	= "last_issued_qnty*item_return_qty*current_stock*stock_value*updated_by*update_date";
			$data_array_prod[$before_prod_id]=explode("*",("".$before_issue_qnty."*".$before_return_qty."*".$adj_before_stock_qnty."*".number_format($adj_before_stock_val,8,'.','')."*'".$user_id."'*'".$pc_date_time."'"));
			
			
			
 			//current product adjust
			$adj_curr_stock_qnty  = $curr_stock_qnty-$txt_issue_qnty; // CurrentStock + Before Issue Qnty
			//$adj_curr_stock_val   = $curr_stock_value-($txt_issue_qnty*$curr_avg_rate); // CurrentStockValue + Before Issue Value
			$adj_curr_stock_val=0;
			$updateID_array_prod[]=$txt_prod_id;
			if ( $adj_curr_stock_qnty != 0 ){
				$adj_curr_stock_val  = $adj_curr_stock_qnty*$curr_avg_rate;
				
			} 

			$update_array_prod	= "last_issued_qnty*item_return_qty*current_stock*stock_value*updated_by*update_date";
			$data_array_prod[$txt_prod_id]=explode("*",("".$txt_issue_qnty."*".$txt_return_qty."*".$adj_curr_stock_qnty."*".number_format($adj_curr_stock_val,8,'.','')."*'".$user_id."'*'".$pc_date_time."'"));
			//$adj_curr_avgrate	 = number_format($adj_curr_stock_val/$adj_curr_stock_qnty,$dec_place[3],'.','');
			//for current product-------------
			
			//now current stock
			$curr_avg_rate 		= $curr_avg_rate;
			$curr_stock_qnty 	= $adj_curr_stock_qnty;
			$curr_stock_value 	= $adj_curr_stock_val;
			$global_stock_qnty=return_field_value("current_stock as current_stock","product_details_master","status_active in(1,3) and id=$txt_prod_id","current_stock");
		}
		
		//$stock_qnty=return_field_value("sum(case when transaction_type in(1,4,5) then cons_quantity else 0 end) - sum(case when transaction_type in(2,3,6) then cons_quantity else 0 end) as balance_stock","inv_transaction","status_active=1 and prod_id=$txt_prod_id and store_id=$cbo_store_name","balance_stock");
		if(str_replace("'","",$txt_issue_qnty)>$latest_current_stock || str_replace("'","",$txt_issue_qnty)>$global_stock_qnty)
		{
			echo "17**Issue Quantity Exceeds The Current Stock Quantity";disconnect($con);die;
		}
		
  		//------------------ product_details_master END--------------//
		//----------Store wise table start here-------------------------//
		$up_conds="";
		if(str_replace("'","",$update_id)) $up_conds=" and id <> $update_id";
		$store_stock_sql="select sum((case when transaction_type in(1,4,5) then cons_quantity else 0 end)-(case when transaction_type in(2,3,6) then cons_quantity else 0 end)) as BALANCE_STOCK, sum((case when transaction_type in(1,4,5) then store_amount else 0 end)-(case when transaction_type in(2,3,6) then store_amount else 0 end)) as BALANCE_AMT 
		from inv_transaction 
		where status_active=1 and prod_id=$txt_prod_id and store_id=$cbo_store_name $up_conds";
		//echo "20**$store_stock_sql";disconnect($con);die;
		$store_stock_sql_result=sql_select($store_stock_sql);
		$store_item_rate=0;
		if($store_stock_sql_result[0]["BALANCE_AMT"]!=0 && $store_stock_sql_result[0]["BALANCE_STOCK"]!=0)
		{
			$store_item_rate=$store_stock_sql_result[0]["BALANCE_AMT"]/$store_stock_sql_result[0]["BALANCE_STOCK"];
		}
		
		$issue_store_value=$txt_issue_qnty*$store_item_rate;
		if($variable_store_wise_rate == 1)
		{
			$sql_store = sql_select("select id, rate as avg_rate_per_unit, cons_qty as current_stock, amount as stock_value from inv_store_wise_gen_qty_dtls where status_active=1 and prod_id=$txt_prod_id and category_id=$cbo_item_category and store_id=$cbo_store_name and company_id=$cbo_company_id");
			$store_up_id=0;
			if(count($sql_store)<1)
			{
				echo "20**No Data Found.";disconnect($con);die;
			}
			elseif(count($sql_store)>1)
			{
				echo "20**Duplicate Product is Not Allow in Same REF Number.";disconnect($con);die;
			}
			else
			{
				$store_presentStock=$store_presentStockValue=$store_presentAvgRate=0;
				foreach($sql_store as $result)
				{
					$store_up_id=$result[csf("id")];
					$store_presentStock	=$result[csf("current_stock")];
					$store_presentStockValue =$result[csf("stock_value")];
					$store_presentAvgRate	=$result[csf("avg_rate_per_unit")];
				}
				$adj_beforeStock_store			=$store_presentStock+$before_issue_qnty;
				$adj_beforeStockValue_store		=$store_presentStockValue+$before_store_amount;
				
				$field_array_store="last_issued_qnty*cons_qty*amount*updated_by*update_date";
				$currentStock_store		=$adj_beforeStock_store-$txt_issue_qnty;
				$currentValue_store		=$adj_beforeStockValue_store-$issue_store_value;
				$data_array_store= "".$txt_issue_qnty."*".$currentStock_store."*".number_format($currentValue_store,8,'.','')."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'";
			}
		}
		

		//issue master update START--------------------------------------//
		$field_array_update_issue="issue_purpose*loan_party*issue_date*challan_no*req_no*remarks*attention*req_id*updated_by*update_date";
		$data_array_update_issue="".$cbo_issue_purpose."*".$cbo_loan_party."*".$txt_issue_date."*".$txt_challan_no."*".$txt_issue_req_no."*".$txt_remarks."*".$txt_attention."*'".$req_id."'*'".$user_id."'*'".$pc_date_time."'";

		//issue master update END---------------------------------------//

		//inventory TRANSACTION table data UPDATE START----------------------------------------------------------//
		//$transaction_type=array(1=>"Receive",2=>"Issue",3=>"Receive Return",4=>"Issue Return");
		$txt_issue_qnty = str_replace("'","",$txt_issue_qnty);
		$avg_rate = $curr_avg_rate; // asign current rate
 		$issue_stock_value = $avg_rate*$txt_issue_qnty;

		$field_array_again = "prod_id*item_category*transaction_type*transaction_date*cons_uom*cons_quantity*cons_rate*cons_amount*production_floor*line_id*machine_id*item_return_qty*machine_category*floor_id*room*rack*self*bin_box*location_id*department_id*section_id*division_id*remarks*updated_by*update_date*batch_lot*raw_issue_challan*table_no_id*store_rate*store_amount";
 		$data_array_again = "".$txt_prod_id."*".$cbo_item_category."*2*".$txt_issue_date."*".$cbo_uom."*".$txt_issue_qnty."*".number_format($avg_rate,10,'.','')."*".number_format($issue_stock_value,8,'.','')."*".$cbo_issue_floor."*".$cbo_sewing_line."*".$cbo_machine_name."*".$txt_return_qty."*".$cbo_machine_category."*".$cbo_floor."*".$cbo_room."*".$txt_rack."*".$txt_shelf."*".$cbo_bin."*".$cbo_location."*".$cbo_department."*".$cbo_section."*".$cbo_division."*".$txt_remarks_dtls."*'".$user_id."'*'".$pc_date_time."'*".$txt_lot."*".$txt_entry_no."*".$cbo_table_no."*".number_format($store_item_rate,10,'.','')."*".number_format($issue_store_value,8,'.','')."";
 		
		//inventory TRANSACTION table data UPDATE  END----------------------------------------------------------//
		
		//----------Store wise table end here-------------------------//
 		//transaction table START--------------------------//

		
		$sql = sql_select("select a.id,a.balance_qnty,a.balance_amount,b.issue_qnty,b.rate,b.amount from inv_transaction a, inv_mrr_wise_issue_details b where a.id=b.recv_trans_id and b.issue_trans_id=$update_id and b.entry_form=21");

		$updateID_array_trans = array();
		$update_data_trans = array();
		foreach($sql as $result)
		{
			$adjBalance = $result[csf("balance_qnty")]+$result[csf("issue_qnty")];
			$adjAmount = $result[csf("balance_amount")]+$result[csf("amount")];
			$updateID_array_trans[]=$result[csf("id")];
			$update_data_trans[$result[csf("id")]]=explode("*",("".$adjBalance."*".$adjAmount."*'".$user_id."'*'".$pc_date_time."'"));

			$trans_data_array[$result[csf("id")]]['qnty']=$adjBalance;
			$trans_data_array[$result[csf("id")]]['amnt']=$adjAmount;
		}


		//****************************************** NEW ENTRY START *****************************************//

		//if LIFO/FIFO then START -----------------------------------------//
		$field_array_mrr = "id,recv_trans_id,issue_trans_id,entry_form,prod_id,issue_qnty,item_return_qty,rate,amount,inserted_by,insert_date";
		$update_array_tran = "balance_qnty*balance_amount*updated_by*update_date";
		$update_array_trans = "balance_qnty*balance_amount*updated_by*update_date";
		$cons_rate=0;
		$issueQnty = $txt_issue_qnty;

		if($isLIFOfifo==2) $cond_lifofifo=" DESC"; else $cond_lifofifo=" ASC";
		$sql_trans = "select id, cons_rate, balance_qnty, balance_amount from inv_transaction where prod_id=$txt_prod_id and store_id=$cbo_store_name and balance_qnty>0 and transaction_type in (1,4,5) and item_category=$cbo_item_category 
		union all
		select a.id, b.rate cons_rate, b.issue_qnty as balance_qnty, b.amount as balance_amount from inv_transaction a, inv_mrr_wise_issue_details b where a.id=b.recv_trans_id and b.issue_trans_id=$update_id and b.entry_form=21 and a.item_category=$cbo_item_category
		order by id $cond_lifofifo";
		//echo "10**$sql_trans";die;
		$sql = sql_select($sql_trans);
		$mrr_bal_trans_data=array();
		foreach($sql as $val)
		{
			$mrr_bal_trans_data[$val[csf("id")]]["ID"]=$val[csf("id")];
			$mrr_bal_trans_data[$val[csf("id")]]["CONS_RATE"]=$val[csf("cons_rate")];
			$mrr_bal_trans_data[$val[csf("id")]]["BALANCE_QNTY"]+=$val[csf("balance_qnty")];
			$mrr_bal_trans_data[$val[csf("id")]]["BALANCE_AMOUNT"]+=$val[csf("balance_amount")];
		}
		
		$p=1;

 		foreach($mrr_bal_trans_data as $result)
		{
			$mrrWiseIsID = return_next_id_by_sequence("INV_MRR_WISE_ISSUE_PK_SEQ", "inv_mrr_wise_issue_details", $con);
			$issue_trans_id = $result[csf("id")]; // this row will be updated
			$balance_qnty = $result[csf("balance_qnty")];
			$balance_amount = $result[csf("balance_amount")];
			
			$cons_rate = $result[csf("cons_rate")];
			$issueQntyBalance = $balance_qnty-$issueQnty; // minus issue qnty
			$issueStockBalance = $balance_amount-($issueQnty*$cons_rate);
			if($issueQntyBalance>=0)
			{
				if($p>1)
				{
					$updateID_array_tran_up[]=$issue_trans_id; 
					$update_data_tran_up[$issue_trans_id]=explode("*",("".$result[csf("balance_qnty")]."*".$result[csf("balance_amount")]."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
				}
				else
				{
					$amount = $issueQnty*$cons_rate;
					//for insert
					if($update_data_mrr_insert!="") $update_data_mrr_insert .= ",";
					$update_data_mrr_insert .= "(".$mrrWiseIsID.",".$issue_trans_id.",".$update_id.",21,".$txt_prod_id.",".$issueQnty.",".$txt_return_qty.",".number_format($cons_rate,10,'.','').",".number_format($amount,8,'.','').",'".$user_id."','".$pc_date_time."')";
					//for update
					$updateID_array_tran_up[]=$issue_trans_id;
					$update_data_tran_up[$issue_trans_id]=explode("*",("".$issueQntyBalance."*".$issueStockBalance."*'".$user_id."'*'".$pc_date_time."'"));
				}
				$p++;
				//break;
			}
			else if($issueQntyBalance<0)
			{
				$issueQntyBalance  = $issueQnty-$balance_qnty;
				$issueQnty = $balance_qnty;
				$amount = $issueQnty*$cons_rate;

				//for insert
				if($update_data_mrr_insert!="") $update_data_mrr_insert .= ",";
				$update_data_mrr_insert .= "(".$mrrWiseIsID.",".$issue_trans_id.",".$update_id.",21,".$txt_prod_id.",".$issueQnty.",".$txt_return_qty.",".number_format($cons_rate,10,'.','').",".number_format($amount,8,'.','').",'".$user_id."','".$pc_date_time."')";
				//echo "20**".$data_array;die;
				//for update
				$updateID_array_tran_up[]=$issue_trans_id;
				$update_data_tran_up[$issue_trans_id]=explode("*",("0*0*'".$user_id."'*'".$pc_date_time."'"));
				$issueQnty = $issueQntyBalance;
			}
			//$mrrWiseIsID++;
		}

		$txt_order_ref=str_replace("'","",$txt_order_ref);
		$order_wise_data_array="";
		if($txt_order_ref!="")
		{
			$txt_order_ref_arr=explode(",",$txt_order_ref);
			$order_wise_field_arr="id,trans_id,trans_type,entry_form,po_breakdown_id,prod_id,quantity,rate,amount,gmts_quantity,inserted_by,insert_date,status_active,is_deleted";
			$orderWiseID = return_next_id("id", "order_wise_general_details", 1);
			foreach($txt_order_ref_arr as $val)
			{
				$val_arr=explode("_",$val);
				if($order_wise_data_array!="") $order_wise_data_array .= ",";
				$ord_amount=$avg_rate*$val_arr[1];
				$order_wise_data_array .= "(".$orderWiseID.",".$update_id.",2,21,".$val_arr[0].",".$txt_prod_id.",".$val_arr[1].",".number_format($avg_rate,10,'.','').",".number_format($ord_amount,8,'.','').",'".$val_arr[2]."','".$user_id."','".$pc_date_time."',1,0)";
				$orderWiseID++;
			}
		}




		$query1=$query2=$query3=$rID=$transID=$mrrWiseIssueID=$upTrID=$serialUpdate=$serialDelete=$storeRID=true;
		if($before_prod_id==$txt_prod_id)
		{
 			$query1 = sql_update("product_details_master",$update_array_prod,$data_array_prod,"id",$before_prod_id,0);
		}
		else
		{
			$query1=execute_query(bulk_update_sql_statement("product_details_master","id",$update_array_prod,$data_array_prod,$updateID_array_prod),0);
		}
		
		if(count($update_data_trans)>0)
		{
			 $updateIDArray = implode(",",$update_data_trans);
			 $query3 = execute_query("DELETE FROM inv_mrr_wise_issue_details WHERE issue_trans_id=$update_id and entry_form=21",0);
		}
		if(trim(str_replace("'","",$txt_system_id))!="")
		{
			$rID=sql_update("inv_issue_master",$field_array_update_issue,$data_array_update_issue,"id",$txt_system_id,1);
		}
		$transID = sql_update("inv_transaction",$field_array_again,$data_array_again,"id",$update_id,0);
		if($update_data_mrr_insert!="")
		{
			$mrrWiseIssueID=sql_insert("inv_mrr_wise_issue_details",$field_array_mrr,$update_data_mrr_insert,0);
		}
		//transaction table stock update here------------------------//
		if(count($updateID_array_tran_up)>0)
		{
 			$upTrID=execute_query(bulk_update_sql_statement("inv_transaction","id",$update_array_tran,$update_data_tran_up,$updateID_array_tran_up),0);
		}
		$order_rID = $order_rID_del = true;
		if($order_wise_data_array!="")
		{
			$order_rID_del = execute_query("DELETE FROM order_wise_general_details WHERE trans_id=$update_id and entry_form=21",0);
			$order_rID = sql_insert("order_wise_general_details",$order_wise_field_arr,$order_wise_data_array,1);
		}

		$txt_serial_id 	= trim(str_replace("'","",$txt_serial_id));
 		if($txt_serial_id!="")
		{
			$field_array_serial="issue_trans_id*is_issued*updated_by*update_date";
			$before_serial_id=trim(str_replace("'","",$before_serial_id));$txt_serial_id=trim(str_replace("'","",$txt_serial_id));$update_id=trim(str_replace("'","",$update_id));
			if( strpos(trim($txt_serial_no), ",")>0)
			{
				$se_data=explode(",",str_replace("'","",$txt_serial_no));
				if( (count($se_data)<=str_replace("'","",$txt_issue_qnty)))
				{
					if($before_serial_id !="")
					{
						$txt_before_serial_id_arr=explode(",",$before_serial_id);
						if(count($txt_before_serial_id_arr)>0)
						{
							foreach($txt_before_serial_id_arr as $serial_id)
							{
								$update_data_before_serial[$serial_id]=explode("*",("0*0*'".$user_id."'*'".$pc_date_time."'"));
							}
							$serialDelete=execute_query(bulk_update_sql_statement("inv_serial_no_details","id",$field_array_serial,$update_data_before_serial,$txt_before_serial_id_arr),1);
						}
					}
					$txt_serial_id_arr=explode(",",$txt_serial_id);
					if(count($txt_serial_id_arr)>0)
					{
						foreach($txt_serial_id_arr as $serial_id)
						{
							$update_data_serial[$serial_id]=explode("*",("".$update_id."*1*'".$user_id."'*'".$pc_date_time."'"));
						}
					}
					$serialUpdate=execute_query(bulk_update_sql_statement("inv_serial_no_details","id",$field_array_serial,$update_data_serial,$txt_serial_id_arr),1);
				}
				else
				{
					echo "50";disconnect($con);die;
				}
			}
			else
			{

				if($before_serial_id !="")
				{
					$txt_before_serial_id_arr=explode(",",$before_serial_id);
					if(count($txt_before_serial_id_arr)>0)
					{
						foreach($txt_before_serial_id_arr as $serial_id)
						{
							$update_data_before_serial[$serial_id]=explode("*",("0*0*'".$user_id."'*'".$pc_date_time."'"));
						}
						$serialDelete=execute_query(bulk_update_sql_statement("inv_serial_no_details","id",$field_array_serial,$update_data_before_serial,$txt_before_serial_id_arr),1);
					}
				}
				//echo $serialDelete;die;
				$txt_serial_id_arr=explode(",",$txt_serial_id);
				if(count($txt_serial_id_arr)>0)
				{
					foreach($txt_serial_id_arr as $serial_id)
					{
						$update_data_serial[$serial_id]=explode("*",("".$update_id."*1*'".$user_id."'*'".$pc_date_time."'"));
					}
				}
				$serialUpdate=execute_query(bulk_update_sql_statement("inv_serial_no_details","id",$field_array_serial,$update_data_serial,$txt_serial_id_arr),1);
			}
		}
		
		if($store_up_id>0 && $variable_store_wise_rate == 1)
		{
			$storeRID=sql_update("inv_store_wise_gen_qty_dtls",$field_array_store,$data_array_store,"id",$store_up_id,1);
		}

		//$query1 $transID $mrrWiseIssueID
		//echo "10**".$query1 ."&&". $query2 ."&&". $query3 ."&&". $rID ."&&". $transID ."&&". $mrrWiseIssueID ."&&". $upTrID ."&&". $serialUpdate  ."&&". $serialDelete."&&". $order_rID_del  ."&&". $order_rID  ."&&". $storeRID;oci_rollback($con);disconnect($con);die;

		//release lock table
		//check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($query1 &&  $query2 && $query3 && $rID && $transID && $mrrWiseIssueID && $upTrID && $serialUpdate  && $serialDelete && $order_rID_del  && $order_rID  && $storeRID)
			{
				mysql_query("COMMIT");
				echo "1**".str_replace("'","",$txt_system_no)."**".str_replace("'","",$txt_system_id);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$txt_system_no)."**".str_replace("'","",$txt_system_id);
			}
		}
		//$query1=$query2=$query3=$rID=$transID=$mrrWiseIssueID=$upTrID=$serialUpdate=$serialDelete
		if($db_type==2 || $db_type==1 )
		{
			if($query1 && $query2 && $query3 && $rID && $transID && $mrrWiseIssueID && $upTrID && $serialUpdate && $serialDelete && $order_rID_del && $order_rID && $storeRID)
			{
				oci_commit($con);
				echo "1**".str_replace("'","",$txt_system_no)."**".str_replace("'","",$txt_system_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_system_no)."**".str_replace("'","",$txt_system_id);
			}
		}
		//check_table_status( $_SESSION['menu_id'],0);
		disconnect($con);
		die;
 	}
	else if ($operation==2) // Delete Here----------------------------------------------------------
	{

		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		// master table delete here---------------------------------------
		$mst_id = str_replace("'","",$txt_system_id);
		if($mst_id=="" || $mst_id==0)
		{
			echo "16**Delete not allowed. Problem occurred";disconnect($con); die;
		}
		else
		{
			$update_id = str_replace("'","",$update_id);
			$product_id = str_replace("'","",$current_prod_id);
			if( str_replace("'","",$update_id) == "" )
			{
				echo "16**Delete not allowed. Problem occurred";disconnect($con); die;
			}
			//echo "10**select id from inv_transaction where transaction_type in(2,3,6) and prod_id=$product_id and status_active=1 and is_deleted=0 and id >$update_id"; die;
			$chk_next_transaction=return_field_value("id","inv_transaction","transaction_type in(1,2,3,4,5,6) and prod_id=$product_id and status_active=1 and is_deleted=0 and id > $update_id ","id");
			if($chk_next_transaction !="")
			{
				echo "17**Delete not allowed.This item is used in another transaction"; disconnect($con);die;
			}
			else
			{
				$update_field_trans="balance_qnty*balance_amount*updated_by*update_date";
				$sql_mrr=sql_select("select b.id as trans_id, a.id as mrr_tbl_id, a.recv_trans_id, a.issue_trans_id, a.issue_qnty, a.rate, a.amount, b.balance_qnty, b.balance_amount 
				from inv_mrr_wise_issue_details a, inv_transaction b 
				where a.recv_trans_id=b.id and a.issue_trans_id=$update_id and a.prod_id=$product_id and a.status_active=1 and a.is_deleted=0 ");
				foreach($sql_mrr as $row)
				{
					$balance_qnty=$row[csf("balance_qnty")]+$row[csf("issue_qnty")];
					$balance_amount=$row[csf("balance_amount")]+$row[csf("amount")];
					$updateID_array_tran[]=$row[csf("trans_id")];
					$update_data_tran_up[$row[csf("trans_id")]]=explode("*",("'".$balance_qnty."'*'".$balance_amount."'*'".$user_id."'*'".$pc_date_time."'"));
					//$mrr_table_id[$row[csf("id")]]=$row[csf("id")];
					$mrr_table_id[$row[csf("mrr_tbl_id")]]=$row[csf("mrr_tbl_id")];
				}
				

				$sql = sql_select("select a.prod_id,a.cons_quantity,a.cons_rate,a.cons_amount, a.store_amount,b.current_stock,b.stock_value from inv_transaction a, product_details_master b  where a.status_active=1 and a.id=$update_id and a.prod_id=b.id");

				$before_prod_id=$before_receive_qnty=$before_rate=$beforeAmount=$before_brand="";
				$beforeStock=$beforeStockValue=0;
				foreach( $sql as $row)
				{
					$before_prod_id 		= $row[csf("prod_id")];
					$before_receive_qnty 	= $row[csf("cons_quantity")]; //stock qnty
					$before_rate 			= $row[csf("cons_rate")];
					$beforeAmount			= $row[csf("cons_amount")]; //stock value
					$beforeStoreAmount		= $row[csf("store_amount")];
					$beforeStock			= $row[csf("current_stock")];
					$beforeStockValue		= $row[csf("stock_value")];
					//$beforeAvgRate			=$row[csf("avg_rate_per_unit")];
				}
				//stock value minus here---------------------------//
				$adj_beforeStock			=$beforeStock+$before_receive_qnty;
				$adj_beforeStockValue=0;
				//$adj_beforeAvgRate			=number_format(($adj_beforeStockValue/$adj_beforeStock),$dec_place[3],'.','');
				if ( $adj_beforeStock != 0 ){
					$adj_beforeStockValue	=$beforeStockValue+$beforeAmount;					
				}
				
				if($variable_store_wise_rate == 1)
				{
					$sql_store = sql_select("select id, rate as avg_rate_per_unit, cons_qty as current_stock, amount as stock_value from inv_store_wise_gen_qty_dtls where status_active=1 and prod_id=$before_prod_id and category_id=$cbo_item_category and store_id=$cbo_store_name and company_id=$cbo_company_id");
					$store_up_id=0;
					if(count($sql_store)<1)
					{
						echo "20**No Data Found.";disconnect($con);die;
					}
					elseif(count($sql_store)>1)
					{
						echo "20**Duplicate Product is Not Allow in Same REF Number.";disconnect($con);die;
					}
					else
					{
						$store_presentStock=$store_presentStockValue=$store_presentAvgRate=$store_before_receive_qnty=0;
						foreach($sql_store as $result)
						{
							$store_up_id=$result[csf("id")];
							$store_presentStock	=$result[csf("current_stock")];
							$store_presentStockValue =$result[csf("stock_value")];
							$store_presentAvgRate	=$result[csf("avg_rate_per_unit")];
						}
						$currentStock_store		=$store_presentStock+$before_receive_qnty;
						$currentValue_store		=$store_presentStockValue+$beforeStoreAmount;
						
						$field_array_store="last_issued_qnty*cons_qty*amount*updated_by*update_date";
						$data_array_store= "".$before_receive_qnty."*".$currentStock_store."*".number_format($currentValue_store,8,'.','')."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'";
					}
				}

				$field_array_product="current_stock*stock_value*updated_by*update_date";
				$data_array_product = "".$adj_beforeStock."*".number_format($adj_beforeStockValue,8,'.','')."*'".$user_id."'*'".$pc_date_time."'";

				/*$field_array_product="current_stock*stock_value*updated_by*update_date";
				$data_array_product = "".$adj_beforeStock."*".number_format($adj_beforeStockValue,$dec_place[4],'.','')."*'".$user_id."'*'".$pc_date_time."'";*/
				$sql_mst = sql_select("select id from inv_transaction where status_active=1 and is_deleted=0 and transaction_type=2 and mst_id=$mst_id");
				if(count($sql_mst)==1)
				{
					$field_array_mst="updated_by*update_date*status_active*is_deleted";
					$data_array_mst="".$user_id."*'".$pc_date_time."'*0*1";

					$rID5=sql_update("inv_issue_master",$field_array_mst,$data_array_mst,"id",$mst_id,1);
					$resetLoad=1;
				}
				else
				{
					$rID5=1;
					$resetLoad=2;
				}

				$field_array_trans="updated_by*update_date*status_active*is_deleted";
				$data_array_trans="".$user_id."*'".$pc_date_time."'*0*1";

				

				$field_array_mrr="updated_by*update_date*status_active*is_deleted";
				$data_array_mrr="".$user_id."*'".$pc_date_time."'*0*1";
				$rID=$rID2=$rID3=$rID4=$storeRID=true;
				$rID=sql_update("inv_transaction",$field_array_trans,$data_array_trans,"id",$update_id,0);
				$rID2=sql_update("product_details_master",$field_array_product,$data_array_product,"id",$product_id,0);
				if(count($updateID_array_tran)>0)
				{
					$rID4=execute_query(bulk_update_sql_statement("inv_transaction","id",$update_field_trans,$update_data_tran_up,$updateID_array_tran),0);
					$mrr_table_ids=implode(",",$mrr_table_id);
					$rID3=sql_multirow_update("inv_mrr_wise_issue_details",$field_array_mrr,$data_array_mrr,"id",$mrr_table_ids,0);
					//$statusChange=sql_multirow_update("inv_transaction",$field_array_trans,$data_array_trans,"id",$trans_ids,1);
				}
				
				if($store_up_id>0 && $variable_store_wise_rate == 1)
				{
					$storeRID=sql_update("inv_store_wise_gen_qty_dtls",$field_array_store,$data_array_store,"id",$store_up_id,1);
				}
			}
		}
		//echo "2**$rID**$rID2**$rID3**$rID4**$rID5**$storeRID";die;

		if($db_type==0)
		{
			if($rID && $rID2 && $rID3 && $rID4 && $storeRID)
			{
				mysql_query("COMMIT");
				echo "2**".str_replace("'","",$txt_system_no)."**".str_replace("'","",$txt_system_id)."**".$resetLoad;
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$txt_system_no)."**".str_replace("'","",$txt_system_id)."**".$resetLoad;
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2 && $rID3 && $rID4 && $storeRID)
			{
				oci_commit($con);
				echo "2**".str_replace("'","",$txt_system_no)."**".str_replace("'","",$txt_system_id)."**".$resetLoad;
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_system_no)."**".str_replace("'","",$txt_system_id)."**".$resetLoad;
			}
		}
		disconnect($con);
		die;
	}
}
if($action=="search_by_drop_down")
{
	echo create_drop_down( "cbo_item_category", 150, $general_item_category,"", 1, "-- Select --", 0, "", 1,"" );
}

if($action=="mrr_popup")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(sys_id)
		{
			var id = sys_id.split("_");
			$("#hidden_sys_id").val(id[0]); // mrr number
			$("#hidden_posted_in_account").val(id[1]); 
			parent.emailwindow.hide();
		}


	</script>
	</head>
	<body>
	<div align="center" style="width:100%;" >
	<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
		<table width="780" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
				<thead>
					<tr>
						<th width="100">Search By</th>
						<th width="250" align="center" id="search_by_td_up">Enter Issue No</th>
                        <th width="100">Year</th>
						<th width="200">Date Range</th>
						<th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>
					</tr>
				</thead>
				<tbody>
					<tr class="general">
						<td>
							<?
								$search_by = array(1=>'Issue No',2=>'Req No',3=>'Challan No',4=>'Item Category');
								$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../../') ";
								echo create_drop_down( "cbo_search_by", 100, $search_by,"",0, "--Select--", "",$dd,0 );
							?>
						</td>
						<td width="" align="center" id="search_by_td">
							<input type="text" style="width:230px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
						</td>
                        <td><? echo create_drop_down( "cbo_year", 80, $year,"", 1,"-All-", date('Y'), "",0,"" ); ?></td>
						<td align="center">
							<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" />
							<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date" />
						</td>
						<td align="center">
							<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>+'_'+'<? echo $item_cat; ?>'+'_'+document.getElementById('cbo_year').value, 'create_mrr_search_list_view', 'search_div', 'general_item_issue_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
						</td>
				</tr>
				<tr>
					<td align="center" height="40" valign="middle" colspan="5">
						<? echo load_month_buttons(1);  ?>
					<!-- Hidden field here -->
						<input type="hidden" id="hidden_sys_id" value="hidden_sys_id" />
						<input type="hidden" id="hidden_posted_in_account" value="hidden_posted_in_account" />
						<!-- END  -->
					</td>
				</tr>
				</tbody>
			</tr>
			</table>
			<br>
			<div align="center" valign="top" id="search_div"> </div>
			</form>
	</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}

if($action=="create_mrr_search_list_view")
{
	$ex_data = explode("_",$data);
 	$txt_search_by = $ex_data[0];
	$txt_search_common = trim($ex_data[1]);
	$fromDate = $ex_data[2];
	$toDate = $ex_data[3];
	$company = $ex_data[4];
	$item_cat = $ex_data[5];
	$cbo_year = trim(str_replace("'","",$ex_data[6]));
 	$company_arr = return_library_array("select id, company_name from lib_company",'id','company_name');
 	$store_arr = return_library_array("select id, store_name from lib_store_location where is_deleted=0",'id','store_name');
	$user_arr= return_library_array("select id, user_name from user_passwd", "id", "user_name");

	if ($txt_search_common =="" && ($fromDate =="" && $toDate=="")){
		echo "<p style='color:red; font-size:16px;'>Please select date range or Issue/Req/Challan/Category Number</p>";die;
	}

 	$sql_cond="";
	if($fromDate!="" && $toDate!="" )
	{
		if($db_type==0)
		{
			$sql_cond .= " and a.issue_date  between '".change_date_format($fromDate,'yyyy-mm-dd')."' and '".change_date_format($toDate,'yyyy-mm-dd')."'";
		}
		else
		{
			$sql_cond .= " and a.issue_date  between '".change_date_format($fromDate,'yyyy-mm-dd','',-1)."' and '".change_date_format($toDate,'yyyy-mm-dd','',-1)."'";
		}
	}
 	if($company!="" && $company*1!=0) $sql_cond .= " and a.company_id='$company'";


 	if($txt_search_common!="" || $txt_search_common!=0)
	{
		if($txt_search_by==1)
		{
			$sql_cond .= " and a.issue_number like '%$txt_search_common%'";
		}
		else if($txt_search_by==2)
		{
			$sql_cond .= " and a.req_no like '%$txt_search_common%'";
 		}
		else if($txt_search_by==3)
		{
			$sql_cond .= " and a.challan_no like '%$txt_search_common%'";
		}
	}
	//echo "SELECT unit_id as company_id, store_location_id, item_cate_id, supplier_id FROM user_passwd where id=$user_id";die;
	$userCredential = sql_select("SELECT unit_id as company_id, store_location_id, item_cate_id, supplier_id FROM user_passwd where id=$user_id");
	//$cre_company_id = $userCredential[0][csf('company_id')];
	$cre_supplier_id = $userCredential[0][csf('supplier_id')];
	$cre_store_location_id = $userCredential[0][csf('store_location_id')];
	$cre_item_cate_id = $userCredential[0][csf('item_cate_id')];

	$credientian_cond="";
	//if($cre_company_id>0) $credientian_cond=" and a.company_id in($cre_company_id)";
	//if($cre_supplier_id!="") $credientian_cond.=" and a.supplier_id in($cre_supplier_id)";
	if($cre_store_location_id!="") $credientian_cond.=" and b.store_id in($cre_store_location_id)";
	if($cre_item_cate_id!="") $credientian_cond.=" and b.item_category in($cre_item_cate_id)";



	/*$sql = "select a.id, a.issue_number,a.issue_basis,a.issue_purpose,a.entry_form,a.company_id,a.location_id,a.supplier_id,a.store_id,a.issue_date
			from inv_issue_master a, inv_transaction b
			where a.id=b.mst_id and a.status_active=1 and a.entry_form=21 $sql_cond $credientian_cond
			group by a.id, a.issue_number,a.issue_basis,a.issue_purpose,a.entry_form,a.company_id,a.location_id,a.supplier_id,a.store_id,a.issue_date
			order by a.issue_number";*/
	
	if($cbo_year) $sql_cond.=" and to_char(a.insert_date,'YYYY')='$cbo_year'";
	if($db_type==0)
	{
		$sql = "SELECT a.id, a.issue_number,a.issue_basis,a.issue_purpose,a.entry_form,a.company_id,a.location_id,a.supplier_id,a.store_id,a.issue_date, group_concat(b.item_category) as item_cat_id, sum(b.cons_quantity) as cons_quantity,a.is_posted_account,a.req_no, a.inserted_by
		from inv_issue_master a, inv_transaction b
		where a.id=b.mst_id and a.status_active=1 and b.status_active=1 and a.entry_form=21 and b.transaction_type=2 $sql_cond $credientian_cond
		group by a.id, a.issue_number,a.issue_basis,a.issue_purpose,a.entry_form,a.company_id,a.location_id,a.supplier_id,a.store_id,a.issue_date,a.is_posted_account,a.req_no, a.inserted_by
		order by a.issue_number desc";
	}
	else
	{
		$sql = "SELECT a.id, a.issue_number,a.issue_basis,a.issue_purpose,a.entry_form,a.company_id,a.location_id,a.supplier_id,a.store_id,a.issue_date,a.is_posted_account,a.req_no, a.inserted_by, listagg(cast(b.item_category as varchar(4000)),',') within group (order by b.item_category) as item_cat_id, sum(b.cons_quantity) as cons_quantity
		from inv_issue_master a, inv_transaction b
		where a.id=b.mst_id and a.status_active=1 and b.status_active=1 and a.entry_form=21 and b.transaction_type=2 $sql_cond $credientian_cond
		group by a.id, a.issue_number,a.issue_basis,a.issue_purpose,a.entry_form,a.company_id,a.location_id,a.supplier_id,a.store_id,a.issue_date,a.is_posted_account,a.req_no, a.inserted_by
		order by a.issue_number desc";
	}

	//echo $sql;// die;
	$result = sql_select( $sql );
	?>
    	<div>
            <div style="width:820px;">
                <table cellspacing="0" cellpadding="0" width="820" class="rpt_table" rules="all" border="1">
                    <thead>
                        <th width="50">SL</th>
                        <th width="120">Issue No</th>
                        <th width="120">Item Category</th>
                        <th width="80">Date</th>
                        <th width="120">Purpose</th>
                        <th width="80">Req No</th>
                        <th width="100">Insert User</th>
                        <th >Issue Qnty</th>
                    </thead>
                </table>
            </div>
            <div style="width:820px;overflow-y:scroll; min-height:200px; max-height:210px;" id="search_div" >
                <table cellspacing="0" cellpadding="0" width="800" class="rpt_table" id="list_view"  rules="all" border="1">
        	<?
            $i=1;
            foreach( $result as $row )
			{
                if ($i%2==0)
                    $bgcolor="#E9F3FF";
                else
                    $bgcolor="#FFFFFF";

				//$issuQnty = return_field_value("sum(cons_quantity) as cons_quantity","inv_transaction","mst_id=".$row[csf("id")]." and transaction_type=2 and item_category not in (1,2,3,5,6,7,12,13,14) group by mst_id","cons_quantity");
				$issuQnty =	$row[csf("cons_quantity")];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"  onclick="js_set_value('<? echo $row[csf("id")].'_'.$row[csf("is_posted_account")];?>');">
					<td width="50" align="center"><? echo $i; ?></td>
					<td width="120"><p><? echo $row[csf("issue_number")];?></p></td>
					<td width="120"><p>
					<?
					$item_cat_arr=array_unique(explode(",",$row[csf("item_cat_id")]));
					$all_item_cat="";
					foreach($item_cat_arr as $cat_id)
					{
						$all_item_cat.=$item_category[$cat_id].",";
					}
					$all_item_cat=chop($all_item_cat,",");
					echo $all_item_cat;
					?></p></td>
					<td width="80"><p><? echo $row[csf("issue_date")]; ?></p></td>
					<td width="120"><p><? echo $general_issue_purpose[$row[csf("issue_purpose")]]; ?></p></td>
					<td width="80"><p><? echo $row[csf("req_no")]; ?></p></td>
					<td width="100"><p><? echo $user_arr[$row[csf("inserted_by")]]; ?></p></td>
					<td  align="right"><p><? echo $issuQnty; ?></p></td>
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


if($action=="populate_data_from_data")
{


	$company_array = return_library_array("select id, company_name from lib_company", "id", "id,company_name");
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier", "id", "id,supplier_name");

	$sql = "select id, issue_number, issue_purpose, loan_party, company_id, issue_date, challan_no, req_no, knit_dye_source, knit_dye_company,knit_dye_location, remarks, attention, req_id
			from inv_issue_master
			where id='$data' and entry_form=21 and status_active=1 and is_deleted=0";
	//echo $sql;die;
	$res = sql_select($sql);
	//print_r($company_array); die;
	foreach($res as $row)
	{
		echo "$('#txt_system_no').val('".$row[csf("issue_number")]."');\n";
		echo "$('#txt_system_id').val(".$row[csf("id")].");\n";
		echo "$('#cbo_company_id').val(".$row[csf("company_id")].");\n";
  		echo "$('#cbo_issue_purpose').val('".$row[csf("issue_purpose")]."');\n";
  		echo "$('#cbo_loan_party').val('".$row[csf("loan_party")]."');\n";
 		echo "$('#txt_issue_date').val('".change_date_format($row[csf("issue_date")])."');\n";
 		echo "$('#txt_issue_req_no').val('".$row[csf("req_no")]."');\n";
		echo "$('#hidden_issue_req_id').val('".$row[csf("req_id")]."');\n";
 		echo "$('#txt_challan_no').val('".$row[csf("challan_no")]."');\n";
  		echo "$('#cbo_issue_source').val('".$row[csf("knit_dye_source")]."');\n";
  		echo "load_drop_down( 'requires/general_item_issue_controller', '".$row[csf("knit_dye_source")]."'+'**'+".$row[csf("company_id")]."+'**'+'".$row[csf("issue_purpose")]."', 'load_drop_down_issue_to_new', 'issue_to_td' );\n";
  		echo "$('#cbo_issue_to').val('".$row[csf("knit_dye_company")]."');\n";
  		echo "$('#cbo_location_issue_to').val('".$row[csf("knit_dye_location")]."');\n";
 		echo "$('#txt_remarks').val('".$row[csf("remarks")]."');\n";
 		echo "$('#txt_attention').val('".$row[csf("attention")]."');\n";
		//clear child form
		echo "$('#tbl_child').find('select,input').val('');\n";
  	}
	exit();
}

if($action=="show_dtls_list_view")
{
	$ex_data = explode("**",$data);
	$issue_number_id = $ex_data[0];

	$cond="";
	if($issue_number_id!="") $cond .= " and a.id='$issue_number_id'";

	$location_arr=return_library_array("select id,location_name from lib_location",'id','location_name');
	$department_arr=return_library_array("select id,department_name from lib_department",'id','department_name');
	$section_arr=return_library_array("select id,section_name from lib_section",'id','section_name');
	$store_arr=return_library_array( "select id, store_name from lib_store_location where is_deleted=0",'id','store_name');
	//$po_no_arr=return_library_array( "select id, po_number from wo_po_break_down",'id','po_number');
 	$group_arr=return_library_array( "select id,item_name from lib_item_group where item_category not in (1,2,3,5,6,7,12,13,14) and status_active=1 and is_deleted=0",'id','item_name');
	$machine_arr = return_library_array("select id, machine_no from lib_machine_name","id","machine_no");
	$floor_arr = return_library_array("select id, floor_name from  lib_prod_floor","id","floor_name");

	$sql = "select a.issue_number, b.id, b.store_id, b.cons_uom, b.cons_quantity, b.machine_category, b.machine_id, b.prod_id, b.location_id, b.department_id, b.section_id, c.item_category_id, $concat(c.item_description $concat_coma ',' $concat_coma c.item_size) as item_description, c.item_group_id, c.item_code, b.order_id,b.production_floor
	from inv_issue_master a, inv_transaction b, product_details_master c
	where a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=2 and a.entry_form=21 and a.status_active=1 and b.status_active=1 and c.status_active in(1,3) $cond";
	$result = sql_select($sql);

	$trans_ids_arr=array();
	foreach ($result as $row){
		$trans_ids_arr[$row[csf('id')]]=$row[csf('id')];
	}

	$con = connect();
	execute_query("delete from gbl_temp_engine where user_id=$user_id and entry_form=58 and ref_from in(1)");
    oci_commit($con);
	fnc_tempengine("gbl_temp_engine", $user_id, 58, 1, $trans_ids_arr, $empty_arr);
	$sql_serial_res=sql_select("SELECT a.ID, a.SERIAL_NO, a.ISSUE_TRANS_ID from gbl_temp_engine g, inv_serial_no_details a where g.ref_val=a.issue_trans_id and g.user_id=$user_id and g.entry_form=58 and g.ref_from=1 and a.status_active=1");
	$serial_res_arr=array();
	foreach($sql_serial_res as $row)
	{
		$serial_res_arr[$row["ISSUE_TRANS_ID"]]['id'].=$row["ID"].',';
		$serial_res_arr[$row["ISSUE_TRANS_ID"]]['serial_no'].=$row["SERIAL_NO"].',';
	}
	unset($sql_serial_res);

	$sql_order_res=sql_select("SELECT a.TRANS_ID, a.PO_BREAKDOWN_ID, b.PO_NUMBER from gbl_temp_engine g, order_wise_general_details a, wo_po_break_down b where g.ref_val=a.trans_id and a.po_breakdown_id=b.id and a.status_active=1 and a.entry_form=21 and b.is_deleted=0 and g.user_id=$user_id and g.entry_form=58 and g.ref_from=1");
	$order_arr=array();
	foreach($sql_order_res as $row)
	{
		$order_arr[$row["TRANS_ID"]]['po_number'].=$row["PO_NUMBER"].',';
	}
	unset($sql_order_res);

	execute_query("delete from gbl_temp_engine where user_id=$user_id and entry_form=58 and ref_from in(1)");
    oci_commit($con);disconnect($con);
	$i=1;
	$total_qnty=0;
	?>
    <table class="rpt_table" border="1" cellpadding="2" cellspacing="0" style="width:1000px" rules="all">
        <thead>
            <tr>
                <th>SL</th>
                <th>Category</th>
                <th>Group</th>
                <th>Description</th>
                <th>Item Code</th>
                <th>Store</th>
                <th>Issue To Floor</th>
                <th>Issue Qnty</th>
                <th>UOM</th>
                <th>Serial No</th>
                <th>Machine Categ.</th>
                <th>Machine No</th>
                <th>Buyer Order</th>
                <th>Loc./Dept./Sec.</th>
            </tr>
        </thead>
        <tbody>
        <? foreach($result as $row){

            if ($i%2==0)$bgcolor="#E9F3FF";
            else $bgcolor="#FFFFFF";

            $serialNo=rtrim($serial_res_arr[$row[csf("id")]]['id'],',');
            $serialID=rtrim($serial_res_arr[$row[csf("id")]]['serial_no'],',');

            $total_qnty +=	$row[csf("cons_quantity")];
			
			$order_num="";
			$order_num=rtrim($order_arr[$row["TRANS_ID"]]['po_number'],',');
			
        	?>
            <tr bgcolor="<? echo $bgcolor; ?>" onClick='load_drop_down("requires/general_item_issue_controller", document.getElementById("cbo_issue_to").value, "load_drop_down_division", "division_td" );get_php_form_data("<? echo $row[csf("id")];?>","child_form_input_data","requires/general_item_issue_controller");' style="cursor:pointer" >
                <td width="30"><? echo $i; ?></td>
                <td width="100"><p><? echo $item_category[$row[csf("item_category_id")]]; ?></p></td>
                <td width="100"><p><? echo $group_arr[$row[csf("item_group_id")]]; ?></p></td>
                <td width="100"><p><? echo $row[csf("item_description")]; ?></p></td>
                <td width="50"><p><? echo $row[csf("item_code")]; ?></p></td>
                <td width="90"><p><? echo $store_arr[$row[csf("store_id")]]; ?></p></td>
                <td width="90"><p><? echo $floor_arr[$row[csf("production_floor")]]; ?></p></td>
                <td width="80" align="right"><p><? echo number_format($row[csf("cons_quantity")],2); ?></p></td>
                <td width="50"><p><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></p></td>
                <td width="50"><p><? echo $serialNo; ?></p></td>
                <td width="50"><p><? echo $machine_category[$row[csf("machine_category")]]; ?></p></td>
                <td width="50"><p><? echo $machine_arr[$row[csf("machine_id")]]; ?></p></td>
                <td width="80"><p><? echo $order_num; ?></p></td>
                <td><p><? echo $location_arr[$row[csf("location_id")]].', '.$department_arr[$row[csf("department_id")]].', '.$section_arr[$row[csf("section_id")]]; ?></p></td>
           </tr>
        <? $i++; } ?>
        <tfoot>
            <th colspan="7" align="right">Total :</th>
            <th><? echo number_format($total_qnty,2); ?></th>
            <th colspan="7">&nbsp;</th>
        </tfoot>
        </tbody>
    </table>
    <?
	exit();
}

if($action=="child_form_input_data")
{
	$rcv_dtls_id = $data;

	/*$sql = "select b.id, b.location_id, b.company_id, c.id as prod_id, c.item_description, c.item_category_id, c.item_group_id,b.item_return_qty, b.cons_quantity, c.current_stock, b.store_id, b.cons_uom, b.order_id, b.floor_id, b.machine_id, b.machine_category, b.location_id, b.department_id, b.section_id, b.room, b.rack, b.self, b.bin_box
	,c.brand_name,c.origin
	from inv_transaction b, product_details_master c
	where b.prod_id=c.id and b.id='$rcv_dtls_id' and b.transaction_type=2 and b.item_category not in (1,2,3,5,6,7,12,13,14)"; */

	/*...............new dev..........................*/



	$sql = "SELECT b.id, b.location_id, b.company_id, c.id as prod_id, c.item_description, c.item_category_id, c.item_group_id,b.item_return_qty, b.cons_quantity, c.current_stock, b.store_id, b.cons_uom, b.order_id, b.floor_id, b.line_id, b.machine_id, b.machine_category, b.location_id, b.department_id, b.section_id, b.room, b.rack, b.self, b.bin_box ,b.remarks ,c.brand_name,c.origin,c.model,b.division_id, b.production_floor, b.batch_lot, b.raw_issue_challan, b.table_no_id, c.re_order_label
	from inv_transaction b, product_details_master c
	where b.prod_id=c.id and b.id='$rcv_dtls_id' and b.transaction_type=2 and b.item_category not in (1,2,3,5,6,7,12,13,14)";
	// echo $sql;die;
	$result = sql_select($sql);

	foreach($result as $row)
	{
		echo "$('#txt_item_desc').val('".$row[csf("item_description")]."');\n";
		echo "$('#cbo_item_category').val(".$row[csf("item_category_id")].");\n";
		echo "$('#cbo_item_group').val(".$row[csf("item_group_id")].");\n";
		echo "$('#txt_issue_qnty').val(".$row[csf("cons_quantity")].");\n";
		echo "$('#txt_return_qty').val(".$row[csf("item_return_qty")].");\n";
		echo "$('#hidden_p_issue_qnty').val(".$row[csf("cons_quantity")].");\n";
 		echo "$('#cbo_location').val(".$row[csf("location_id")].");\n";
		echo "$('#txt_lot').val('".$row[csf("batch_lot")]."');\n";
		echo "$('#txt_entry_no').val('".$row[csf("raw_issue_challan")]."');\n";
		echo "$('#txt_re_order_level').val('".$row[csf("re_order_label")]."');\n";

 		echo "$('#txt_remarks_dtls').val('".$row[csf("remarks")]."');\n";
 		echo "$('#cbo_item_category').attr('disabled', true);\n";
 		echo "$('#txt_item_desc').attr('disabled', true);\n";
 		echo "$('#cbo_item_group').attr('disabled', true);\n";
		echo "$('#cbo_location').attr('disabled', true);\n";
		echo "$('#txt_lot').attr('disabled', true);\n";

		//echo "load_room_rack_self_bin('requires/general_item_issue_controller*4_8_9_10_11_15_16_17_18_19_20_21_22_32_33_34_35_36_37_38_39_40_41_44_45_46_47_48_49_50_51_52_53_54_55_56_57_58_59_60_61_62_63_64_65_66_67_68_69_70_89_90_91_92_93_94_99', 'store','store_td', '".$row[csf('company_id')]."','".$row[csf('location_id')]."',this.value);\n";
		echo "load_drop_down('requires/general_item_issue_controller', ".$row[csf("location_id")]."+'_'+".$row[csf('company_id')].", 'load_drop_down_store','store_td');\n";
		//echo "document.getElementById('cbo_store_name').value 				= '".$row[csf("store_id")]."';\n";

		echo "$('#cbo_store_name').val(".$row[csf("store_id")].");\n";
		echo "$('#cbo_store_name').attr('disabled', true);\n";
 		echo "$('#cbo_uom').val(".$row[csf("cons_uom")].");\n";

		$currnet_stock=return_field_value("sum(case when transaction_type in(1,4,5) then cons_quantity else 0 end) - sum(case when transaction_type in(2,3,6) then cons_quantity else 0 end) as balance_stock","inv_transaction","status_active=1 and prod_id='".$row[csf("prod_id")]."' and store_id='".$row[csf("store_id")]."'","balance_stock");

		echo "$('#txt_current_stock').val(".($currnet_stock+$row[csf("cons_quantity")]).");\n";

		//$serialNo=return_field_value("group_concat(serial_no)","inv_serial_no_details","issue_trans_id=".$row[csf("id")]);
		//$serialID=return_field_value("group_concat(id)","inv_serial_no_details","issue_trans_id=".$row[csf("id")]);
		if($db_type==0)
		{
			$serialNo=return_field_value("group_concat(serial_no)","inv_serial_no_details","issue_trans_id=".$row[csf("id")]);
			$serialID=return_field_value("group_concat(id)","inv_serial_no_details","issue_trans_id=".$row[csf("id")]);
		}
		else
		{
			$serialNo=return_field_value("LISTAGG(CAST(serial_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY serial_no) as sr","inv_serial_no_details","issue_trans_id=".$row[csf("id")],"sr");
			$serialID=return_field_value("LISTAGG(CAST(id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY id) as id","inv_serial_no_details","issue_trans_id=".$row[csf("id")],"id");
		}
		echo "$('#txt_serial_no').val('".$serialNo."');\n";
		echo "$('#txt_serial_id').val('".$serialID."');\n";
		echo "$('#before_serial_id').val('".$serialID."');\n";
		echo "$('#cbo_machine_category').val(".$row[csf("machine_category")].");\n";

		//echo "load_drop_down( 'requires/general_item_issue_controller',".$row[csf("company_id")]."+'_'+".$row[csf("location_id")].", 'load_drop_down_floor', 'floor_td' );\n";

		if($row[csf("production_floor")] == '')
		{
			$row[csf("production_floor")]=0;
		}
		
		//echo "load_room_rack_self_bin('requires/general_item_issue_controller', 'floor','floor_td', '".$row[csf('company_id')]."','".$row[csf('location_id')]."','".$row[csf('store_id')]."',this.value);\n";
		echo "load_drop_down( 'requires/general_item_issue_controller',".$row[csf("company_id")]."+'_'+".$row[csf("location_id")]."+'_'+".$row[csf("machine_category")].", 'load_drop_down_floor', 'issue_floor_td' );\n";
		echo "$('#cbo_issue_floor').val(".$row[csf("production_floor")].");\n";
		echo "load_drop_down( 'requires/general_item_issue_controller',".$row[csf("company_id")]."+'_'+".$row[csf("location_id")]."+'_'+".$row[csf("production_floor")].", 'load_drop_down_table', 'table_no_td' );\n";
		echo "$('#cbo_table_no').val(".$row[csf("table_no_id")].");\n";
		
		
		
		//load_drop_down( 'requires/general_item_issue_controller', this.value+'_'+$company_id+'_'+$location_id, 'load_drop_down_sewing_line_floor', 'sewing_line_td' );
		echo "load_drop_down( 'requires/general_item_issue_controller',".$row[csf("production_floor")]."+'_'+".$row[csf("company_id")]."+'_'+".$row[csf("location_id")].", 'load_drop_down_sewing_line_floor', 'sewing_line_td' );\n";
		echo "$('#cbo_sewing_line').val(".$row[csf("line_id")].");\n";

		
		echo "load_drop_down( 'requires/general_item_issue_controller',".$row[csf("company_id")]."+'_'+".$row[csf("machine_category")]."+'_'+".$row[csf("production_floor")].", 'load_drop_machine', 'machine_td' );\n";
		//echo __LINE__.'--'.$row[csf("machine_category")]."_".$row[csf("production_floor")]; die;
		echo "$('#cbo_machine_name').val(".$row[csf("machine_id")].");\n";
		
		$sql_ord=sql_select("select a.PO_BREAKDOWN_ID, a.QUANTITY, a.GMTS_QUANTITY, b.PO_NUMBER from ORDER_WISE_GENERAL_DETAILS a, WO_PO_BREAK_DOWN b where a.po_breakdown_id=b.id and a.status_active=1 and b.is_deleted=0 and a.entry_form=21 and a.trans_id='".$row[csf("id")]."' ");
		$order_ids=$order_num=$save_string="";$tot_ord_qnty=0;
		foreach($sql_ord as $val)
		{
			$order_ids .=$val["PO_BREAKDOWN_ID"].",";
			$order_num .=$val["PO_NUMBER"].",";
			$save_string .=$val["PO_BREAKDOWN_ID"]."_".$val["QUANTITY"]."_".$val["GMTS_QUANTITY"].",";
			$tot_ord_qnty +=$val["QUANTITY"];
		}
		echo "$('#txt_order_id').val('".chop($order_ids,",")."');\n";
		//$buyer_order=return_field_value("po_number","wo_po_break_down","id=".$row[csf("order_id")]);
		echo "$('#txt_buyer_order').val('".chop($order_num,",")."');\n";
		echo "$('#txt_order_ref').val('".chop($save_string,",")."');\n";
		echo "$('#txt_order_tot_qnty').val('".$tot_ord_qnty."');\n";
				
		echo "$('#cbo_division').val(".$row[csf("division_id")].");\n";
		if($row[csf("division_id")])
		{
			echo "load_drop_down( 'requires/general_item_issue_controller', '".$row[csf("division_id")]."', 'load_drop_down_department', 'department_td' );\n";
		}
		echo "$('#cbo_department').val(".$row[csf("department_id")].");\n";
		if ($row[csf("department_id")]!="") 
		{
			echo "load_drop_down( 'requires/general_item_issue_controller', ".$row[csf("department_id")].", 'load_drop_down_section', 'section_td' );\n";
		}
		echo "$('#cbo_section').val(".$row[csf("section_id")].");\n";
		echo "disabled_enable();\n";

		/*echo "load_room_rack_self_bin('requires/general_item_issue_controller', 'room','room_td', '".$row[csf('company_id')]."','".$row[csf('location_id')]."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."',this.value);\n";
 		echo "$('#cbo_room').val(".$row[csf("room")].");\n";
		echo "fn_room_rack_self_box();\n";

		echo "load_room_rack_self_bin('requires/general_item_issue_controller', 'rack','rack_td', '".$row[csf('company_id')]."','".$row[csf('location_id')]."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."',this.value);\n";
		echo "$('#txt_rack').val('".$row[csf("rack")]."');\n";
		echo "fn_room_rack_self_box();\n";


		echo "load_room_rack_self_bin('requires/general_item_issue_controller', 'shelf','shelf_td', '".$row[csf('company_id')]."','".$row[csf('location_id')]."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."','".$row[csf('rack')]."',this.value);\n";
 		echo "$('#txt_shelf').val(".$row[csf("self")].");\n";
		echo "fn_room_rack_self_box();\n";

		echo "load_room_rack_self_bin('requires/general_item_issue_controller', 'bin','bin_td', '".$row[csf('company_id')]."','".$row[csf('location_id')]."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."','".$row[csf('rack')]."','".$row[csf('self')]."',this.value);\n";
		echo "$('#cbo_bin').val(".$row[csf("bin_box")].");\n";*/

		echo "load_drop_down('requires/general_item_issue_controller', ".$row[csf("store_id")]."+'_'+".$row[csf('company_id')].", 'load_drop_floor','floor_td');\n";
		echo "document.getElementById('cbo_floor').value 					= '".$row[csf("floor_id")]."';\n";
		if($row[csf("floor_id")])
		{
			echo "load_drop_down('requires/general_item_issue_controller', ".$row[csf("floor_id")]."+'_'+".$row[csf('company_id')]."+'_'+".$row[csf("store_id")]."+'_'+".$row[csf("room")].", 'load_drop_room','room_td');\n";
		}
		echo "document.getElementById('cbo_room').value 					= '".$row[csf("room")]."';\n";
		if($row[csf("room")])
		{
			echo "load_drop_down('requires/general_item_issue_controller', ".$row[csf("room")]."+'_'+".$row[csf('company_id')]."+'_'+".$row[csf("store_id")]."+'_'+".$row[csf("rack")].", 'load_drop_rack','rack_td');\n";
		}
		echo "document.getElementById('txt_rack').value 					= '".$row[csf("rack")]."';\n";
		
		if($row[csf("rack")])
		{
			echo "load_drop_down('requires/general_item_issue_controller', ".$row[csf("rack")]."+'_'+".$row[csf('company_id')]."+'_'+".$row[csf("store_id")]."+'_'+".$row[csf("self")].", 'load_drop_shelf','shelf_td');\n";
		}
		echo "document.getElementById('txt_shelf').value 					= '".$row[csf("self")]."';\n";
		if($row[csf("self")])
		{
			echo "load_drop_down('requires/general_item_issue_controller', ".$row[csf("self")]."+'_'+".$row[csf('company_id')]."+'_'+".$row[csf("store_id")]."+'_'+".$row[csf("bin_box")].", 'load_drop_bin','bin_td');\n";
		}
		echo "document.getElementById('cbo_bin').value 						= '".$row[csf("bin_box")]."';\n";
		echo "$('#cbo_floor').attr('disabled','disabled');\n";
		echo "$('#cbo_room').attr('disabled','disabled');\n";
		echo "$('#txt_rack').attr('disabled','disabled');\n";
		echo "$('#txt_shelf').attr('disabled','disabled');\n";
		echo "$('#cbo_bin').attr('disabled','disabled');\n";
		

		echo "$('#current_prod_id').val(".$row[csf("prod_id")].");\n";
        echo "$('#txt_brand').val('".$row[csf("brand_name")]."');\n";
        echo "$('#cbo_origin').val(".$row[csf("origin")].");\n";
        echo "$('#txt_model').val('".$row[csf("model")]."');\n";//new dev
		echo "$('#update_id').val(".$row[csf("id")].");\n";
		echo "$('#txt_issue_req_no').attr('disabled', true);\n";
		echo "$('#txt_challan_no').attr('disabled', false);\n";
		echo "$('#txt_remarks').attr('disabled', false);\n";
		echo "$('#txt_attention').attr('disabled', false);\n";
		
		echo "set_button_status(1, permission, 'fnc_general_item_issue_entry',1,1);\n";
		//echo "$('#tbl_master').find('input,select').attr('disabled', false);\n";
	}
	exit();
}
//################################################# function Here #########################################//

//function for domestic rate find--------------//
//parameters rate,ile cost,exchange rate,conversion factor
function return_domestic_rate($rate,$ile_cost,$exchange_rate,$conversion_factor){
	$rate_ile=$rate+$ile_cost;
	$rate_ile_exchange=$rate_ile*$exchange_rate;
	$doemstic_rate=$rate_ile_exchange/$conversion_factor;
	return $doemstic_rate;
}

if ($action=="general_item_issue_print")
{
    extract($_REQUEST);
	$data=explode('*',$data);
	// echo "<pre>";
	// print_r ($data);
	$company=$data[0];
	$location=$data[4];

	$sql=" select id,company_id, issue_number,issue_purpose,issue_date, req_no, challan_no, knit_dye_source, knit_dye_company, remarks,loan_party from inv_issue_master where id='$data[1]'";
	$dataArray=sql_select($sql);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$country_arr = return_library_array( "select id, country_name from lib_country", "id", "country_name"  );
	$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name"  );
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$floor_arr = return_library_array("select id, floor_name from  lib_prod_floor","id","floor_name");
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
	$com_dtls = fnc_company_location_address($company, $location, 2);
	?>
	<div style="width:1000px;">
    <table width="980" cellspacing="0" border="0">
        <tr>
            <td colspan="2" rowspan="3">
			<img src="../../../<? echo $com_dtls[2]; ?>" height="60" width="200" style="float:left;">
			</td>
			<td colspan="4" align="center" style="font-size:22px">
            <strong><? echo $com_dtls[0]; ?></strong>
            </td>
        </tr>
        <tr class="form_caption">
        	<td colspan="4" align="center" style="font-size:14px">
				<? 
					echo $com_dtls[1];
					/*$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
					foreach ($nameArray as $result)
					{
					?>
						Plot No: <? echo $result[csf('plot_no')]; ?>
						Level No: <? echo $result[csf('level_no')]?>
						Road No: <? echo $result[csf('road_no')]; ?>
						Block No: <? echo $result[csf('block_no')];?>
						City No: <? echo $result[csf('city')];?>
						Zip Code: <? echo $result[csf('zip_code')]; ?>
						Province No: <? echo $result[csf('province')];?>
						Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br>
						Email Address: <? echo $result[csf('email')];?>
						Website No: <? echo $result[csf('website')];
					}*/
                ?>
            </td>
        </tr>
        <tr>
            <td colspan="2" align="center" style="font-size:18px"><strong><u>General Item Issue challan</u></strong></td>
            <td colspan="2" align="right" id="barcode_img_id"></td>
        </tr>
        <tr>
        	<td width="85"><strong>System ID</strong></td>
            <td width="125px"><strong>: </strong><? echo $dataArray[0][csf('issue_number')]; ?></td>
            <td width="100"><strong>Issue Purpose</strong></td>
            <td width="175px"><strong>: </strong><? echo $general_issue_purpose[$dataArray[0][csf('issue_purpose')]]; ?></td>
            <td width="110"><strong>Loan Party</strong></td>
            <td><strong>: </strong><? echo $supplier_library[$dataArray[0][csf('loan_party')]]; ?></td>
        </tr>
        <tr>
            <td><strong>Issue Date</strong></td>
            <td><strong>: </strong><? echo change_date_format($dataArray[0][csf('issue_date')]); ?></td>
            <td><strong>Issue Req. No</strong></td>
            <td><strong>: </strong><? echo $dataArray[0][csf('req_no')]; ?></td>
            <td><strong>Challan No</strong></td>
            <td width="175px"><strong>: </strong><? echo $dataArray[0][csf('challan_no')]; ?></td>
        </tr>
         <tr>
			<td><strong>Issue Company</strong></td>
			<td><strong>: </strong>
				<?
					if($dataArray[0][csf('knit_dye_source')]==1){
						echo $company_library[$dataArray[0][csf('knit_dye_company')]];
					}
					else {
						echo $supplier_library[$dataArray[0][csf('knit_dye_company')]];
					}
				?>
			</td>
           	<td><strong>Remarks</strong></td>
           	<td colspan="5" ><strong>: </strong><? echo $dataArray[0][csf('remarks')]; ?></td>
       </tr>
    </table>
	<div style="width:100%;">
    <table cellspacing="0" width="980"  border="1" rules="all" class="rpt_table" >
        <thead bgcolor="#dddddd" style="font-size:13px">
            <th width="30">SL</th>
            <th width="80">Item Category</th>
            <th width="80">Item Group</th>
            <th width="120">Item Description</th>
            <th width="60">Item Size</th>
            <th width="60">Store</th>
            <th width="60">Issue Qty</th>
            <th width="40">UOM</th>
            <th width="80">Serial No</th>
            <th width="70">Machine Categ.</th>
            <th width="70">Floor</th>
            <th width="50">Machine No</th>
            <th width="60">Buyer Order</th>
            <th width="60">Loc./Dept./Sec.</th>
            <th width="60">Lot</th>
        </thead>
        <tbody style="font-size:11px">
	<?
	//$mrr_no=$dataArray[0][csf('issue_number')];
	$cond="";
	if($data[1]!="") $cond .= " and a.id='$data[1]'";
	$location_arr=return_library_array("select id,location_name from lib_location",'id','location_name');
	$department_arr=return_library_array("select id,department_name from lib_department",'id','department_name');
	$section_arr=return_library_array("select id,section_name from lib_section",'id','section_name');
	$store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');
	$po_number_arr=return_library_array( "select id, po_number from wo_po_break_down where is_deleted=0",'id','po_number');
 	$group_arr=return_library_array( "select id,item_name from lib_item_group where item_category not in (1,2,3,4,5,6,7,12,13,14) and status_active=1 and is_deleted=0",'id','item_name');
	$machine_arr = return_library_array("select id, machine_no from lib_machine_name","id","machine_no");

	$i=1;
	$sql = "select a.issue_number, b.id, b.store_id, b.cons_uom, b.cons_quantity, b.floor_id, b.machine_category, b.machine_id, b.prod_id, b.location_id, b.department_id, b.section_id, b.order_id, c.item_category_id, c.item_description, c.item_group_id, c.item_size,b.batch_lot 
	from inv_issue_master a, inv_transaction b, product_details_master c 
	where a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=2 and a.entry_form=21 and b.status_active=1 and b.is_deleted=0 and c.status_active in(1,3) and c.is_deleted=0 $cond";
	$sql_result = sql_select($sql);

	foreach($sql_result as $row)
	{
		if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		if($db_type==0)
		{
			$serialNo=return_field_value("group_concat(serial_no)","inv_serial_no_details","issue_trans_id=".$row[csf("id")]);
			$serialID=return_field_value("group_concat(id)","inv_serial_no_details","issue_trans_id=".$row[csf("id")]);
		}
		else
		{
			$serialNo=return_field_value("LISTAGG(CAST(serial_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY serial_no) as sr","inv_serial_no_details","issue_trans_id=".$row[csf("id")],"sr");
			$serialID=return_field_value("LISTAGG(CAST(id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY id) as id","inv_serial_no_details","issue_trans_id=".$row[csf("id")],"id");
		}

		$cons_quantity=$row[csf('cons_quantity')];
		$cons_quantity_sum += $cons_quantity;
	?>
		<tr bgcolor="<? echo $bgcolor; ?>">
			<td align="center"><? echo $i; ?></td>
			<td><? echo $item_category[$row[csf("item_category_id")]]; ?></td>
			<td><? echo $group_arr[$row[csf("item_group_id")]]; ?></td>
			<td><? echo $row[csf("item_description")]; ?></td>
            <td><? echo $row[csf("item_size")]; ?></td>
			<td align="center"><? echo $store_arr[$row[csf("store_id")]]; ?></td>
			<td align="right"><? echo number_format($row[csf("cons_quantity")],2); ?></td>
			<td align="center"><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></td>
			<td><? echo $serialNo; ?></td>
			<td><? echo $machine_category[$row[csf("machine_category")]]; ?></td>
			<td ><? echo $floor_arr[$row[csf("floor_id")]]; ?></td>
			<td align="center"><? echo $machine_arr[$row[csf("machine_id")]]; ?></td>
			<td align="center"><? echo $po_number_arr[$row[csf("order_id")]]; ?></td>
			<td><? echo $location_arr[$row[csf("location_id")]].', '.$department_arr[$row[csf("department_id")]].', '.$section_arr[$row[csf("section_id")]]; ?></td>
			<td align="center"><? echo $row[csf("batch_lot")]; ?></td>

		</tr>
		<? $i++; } ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="6" align="right">Total :</td>
            <td align="right"><? echo number_format($cons_quantity_sum,2); ?></td>
            <td colspan="8">&nbsp;</td>
        </tr>
    </tfoot>
    </table>
        <br>
		 <?
            echo signature_table(12, $data[0], "900px");
         ?>
	</div>
	</div>
     <script type="text/javascript" src="../../../js/jquery.js"></script>
      <script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
     <script>

	function generateBarcode( valuess ){

			var value = valuess;//$("#barcodeValue").val();
		 // alert(value)
			var btype = 'code39';//$("input[name=btype]:checked").val();
			var renderer ='bmp';// $("input[name=renderer]:checked").val();

			var settings = {
			  output:renderer,
			  bgColor: '#FFFFFF',
			  color: '#000000',
			  barWidth: 1,
			  barHeight: 30,
			  moduleSize:5,
			  posX: 10,
			  posY: 20,
			  addQuietZone: 1
			};
			$("#barcode_img_id").html('11');
			 value = {code:value, rect: false};

			$("#barcode_img_id").show().barcode(value, btype, settings);

		}

	 generateBarcode('<? echo $data[2]; ?>');


	 </script>


	<?
	exit();
}


if ($action=="general_item_issue_print_2")
{
    extract($_REQUEST);
	$data=explode('*',$data);
	$company=$data[0];
	$location=$data[4];

	$sql=" select id,company_id, issue_number,issue_purpose,issue_date, req_no, challan_no, knit_dye_source, knit_dye_company, remarks,loan_party,knit_dye_location from inv_issue_master where id='$data[1]'";
	//echo $sql;
	$dataArray=sql_select($sql);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$country_arr = return_library_array( "select id, country_name from lib_country", "id", "country_name"  );
	$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name"  );
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$floor_arr = return_library_array("select id, floor_name from  lib_prod_floor","id","floor_name");
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
	
	
	
	$location_arr=return_library_array( "select id, location_name from lib_location", "id", "location_name"  );

	function company_location_address($com_id, $loc_id=0, $short=0)
	{
		$comp_logo = return_field_value("image_location", "common_photo_library", "file_type=1 and form_name='company_details' and master_tble_id='$com_id'", "image_location");
		$country_full_name = return_library_array("SELECT id,country_name from lib_country", "id", "country_name");
		$company_info_dtls=array();
		if($loc_id!='' && $loc_id!=0){
			$location_adress= return_field_value("address","lib_location","company_id=$com_id and id=$loc_id ","address");
			$company_name= return_field_value("company_name","lib_company","id=$com_id","company_name");
			$company_info_dtls[0]	= $company_name;
			$company_info_dtls[1] 	= $location_adress;
			$company_info_dtls[2] 	= $comp_logo;
		}
		else
		{
			$sql = "SELECT * FROM lib_company WHERE id = $com_id  AND is_deleted=0 AND status_active=1 ORDER BY company_name ASC";
			$result = sql_select( $sql );
			foreach( $result as $row  )
			{
				if($short==1){
					if($row[csf("level_no")])		$level_no	= $row[csf("level_no")].', ';
					if($row[csf("plot_no")])		$plot_no 	= $row[csf("plot_no")].', ';
					if($row[csf("road_no")]) 		$road_no 	= $row[csf("road_no")].', ';
					if($row[csf("block_no")]!='')	$block_no 	= $row[csf("block_no")].', ';
					if($row[csf("zip_code")]!='')	$zip_code 	= ' -'.$row[csf("zip_code")].', ';
					if($row[csf("city")]!='') $city 			= ($zip_code!="")? $row[csf("city")]:$row[csf("city")]." ,";
					if($row[csf("country_id")]!='')	$country 	= $country_full_name[$row[csf("country_id")]].', ';
					if($row[csf("contact_no")]!='')	$contact_no = $row[csf("contact_no")].'.';
				}
				else if($short==2){
					if($row[csf("level_no")])$level_no 		= "".$row[csf("level_no")].', ';
					if($row[csf("plot_no")])$plot_no 		= "".$row[csf("plot_no")].', ';
					if($row[csf("road_no")]) $road_no 		= "".$row[csf("road_no")].', ';
					if($row[csf("block_no")]!='')$block_no 	= "".$row[csf("block_no")].', ';
					if($row[csf("zip_code")]!='')$zip_code 	= ' -'.$row[csf("zip_code")].' , ';
					if($row[csf("city")]!='') $city 		= ($zip_code!="")?"".$row[csf("city")]:"".$row[csf("city")]." ,";
					if($row[csf("country_id")]!='')$country = $country_full_name[$row[csf("country_id")]].', ';
					if($row[csf("contact_no")]!='')	$contact_no = "".$row[csf("contact_no")].'.';
				}else{
					$company_info_dtls[0]	= 0;
					$company_info_dtls[1] 	= "Error Found.";
				}
				$company_info_dtls[0]	= $row[csf("company_name")];
				$company_info_dtls[1] 	= rtrim($level_no.$plot_no.$road_no.$block_no.$city.$zip_code.$country.$contact_no,", ");
				$company_info_dtls[2] 	= $comp_logo;
			}
		}
		return $company_info_dtls;
	}

	$com_dtls = company_location_address($company, $location, 2);
	
	?>
	<div style="width:1000px;">
    <table width="980" cellspacing="0" border="0">
        <tr>
            <td colspan="2" rowspan="3" width="200">
			<img src="../../../<? echo $com_dtls[2]; ?>" height="60" width="200" style="float:left;">
			</td>
			<td colspan="3"  style="font-size:22px;justify-content: center;text-align: center;" >
            	<strong style="justify-content: center;text-align: center;"><? echo $com_dtls[0]; ?></strong>
            </td>
            <td width="300"></td>
        </tr>
        <tr class="form_caption">
        	<td colspan="3"  style="font-size:14px;justify-content: center;text-align: center;">
				<? 
					echo $com_dtls[1];
                ?>
            </td>
            <td width="300"></td>
        </tr>
        <tr>
            <td colspan="3"  style="font-size:18px;justify-content: center;text-align: center;"><strong style="justify-content: center;text-align: center;"><u>General Item Issue challan</u></strong></td>
            <td  align="right" id="barcode_img_id" width="300"></td>
        </tr>
        <tr>
        	<td colspan="6">&nbsp;</td>
        </tr>
        
       
        <tr >
        	<td width="120"><strong>System ID</strong></td>
            <td width="150"><strong>: </strong><? echo $dataArray[0][csf('issue_number')]; ?></td>
            <td width="100"><strong>Issue Req. No </strong></td>
            <td width="175"><strong>: <? echo $dataArray[0][csf('req_no')]; ?></strong></td>
            <td width="110"><strong>Issue Date</strong></td>
            <td><strong>: </strong><? echo change_date_format($dataArray[0][csf('issue_date')]); ?></td>
        </tr>
        <tr>
            <td><strong>Challan No</strong></td>
            <td><strong>: </strong><? echo $dataArray[0][csf('challan_no')]; ?></td>
            <td><strong>Issue Purpose</strong></td>
            <td><strong>: <? echo $general_issue_purpose[$dataArray[0][csf('issue_purpose')]]; ?></strong> </td>
            <td><strong>Location</strong></td>
           	<td><strong>:</strong><? echo $location_arr[$dataArray[0][csf('knit_dye_location')]]; ?></td>
            
        </tr>
         <tr>
			<td><strong>Issue Company</strong></td>
			<td colspan="3"><strong>: </strong>
				<?
					if($dataArray[0][csf('knit_dye_source')]==1){
						echo $company_library[$dataArray[0][csf('knit_dye_company')]];
					}
					else {
						echo $supplier_library[$dataArray[0][csf('knit_dye_company')]];
					}
				?>
			</td>
           
           	<td><strong>Loan Party</strong></td>
            <td><strong>: </strong><? echo $supplier_library[$dataArray[0][csf('loan_party')]]; ?></td>
       </tr>
         <tr>
           	<td><strong>Remarks</strong></td>
           	<td colspan="7" ><strong>: </strong><? echo $dataArray[0][csf('remarks')]; ?></td>
       </tr>
    </table>
    <br>
   
	<div style="width:100%;">
    <table cellspacing="0" width="1000"  border="1" rules="all" class="rpt_table" >
        <thead bgcolor="#dddddd" style="font-size:13px">
            <th width="30">SL</th>
            <th width="80">Item Category</th>
            <th width="80">Item Group</th>
            <th width="120">Item Description</th>
            <th width="60">Item Size</th>
            <th width="60">Store</th>
            <th width="60">Issue Qty</th>
            <th width="40">UOM</th>
            <th width="80">Serial No</th>
            <th width="70">Machine Categ.</th>
            <th width="70">Floor</th>
            <th width="50">Machine No</th>
            <th width="60">Buyer Order</th>
            <th width="60">Div./Dept./Sec.</th>
            <th width="60">Lot</th>
        </thead>
        <tbody style="font-size:11px">
	<?
	//$mrr_no=$dataArray[0][csf('issue_number')];
	$cond="";
	if($data[1]!="") $cond .= " and a.id='$data[1]'";
	$location_arr=return_library_array("select id,location_name from lib_location",'id','location_name');
	$department_arr=return_library_array("select id,department_name from lib_department",'id','department_name');
	$section_arr=return_library_array("select id,section_name from lib_section",'id','section_name');
	$store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');
	$po_number_arr=return_library_array( "select id, po_number from wo_po_break_down where is_deleted=0",'id','po_number');
 	$machine_arr = return_library_array("select id, machine_no from lib_machine_name","id","machine_no");
	$division_arr=return_library_array("select id,division_name from lib_division",'id','division_name');
	
	
	$group_arr=return_library_array( "select id,item_name from lib_item_group where item_category not in (1,2,3,4,5,6,7,12,13,14) and status_active=1 and is_deleted=0",'id','item_name');

	$i=1;
	$sql = "SELECT a.issue_number, b.id, b.store_id, b.cons_uom, b.cons_quantity, b.floor_id, b.machine_category, b.machine_id, b.prod_id, b.location_id, b.department_id, b.section_id, b.order_id, c.item_category_id, c.item_description, c.item_group_id, c.item_size,b.division_id, b.batch_lot,production_floor, a.inserted_by as INSERTED_BY from inv_issue_master a, inv_transaction b, product_details_master c where a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=2 and a.entry_form=21 and b.status_active=1 and b.is_deleted=0 and c.status_active in(1,3) and c.is_deleted=0 $cond";
	//echo $sql;
	$sql_result = sql_select($sql);
	$issue_by=$sql_result[0]["INSERTED_BY"];

	foreach($sql_result as $row)
	{
		if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		if($db_type==0)
		{
			$serialNo=return_field_value("group_concat(serial_no)","inv_serial_no_details","issue_trans_id=".$row[csf("id")]);
			$serialID=return_field_value("group_concat(id)","inv_serial_no_details","issue_trans_id=".$row[csf("id")]);
		}
		else
		{
			$serialNo=return_field_value("LISTAGG(CAST(serial_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY serial_no) as sr","inv_serial_no_details","issue_trans_id=".$row[csf("id")],"sr");
			$serialID=return_field_value("LISTAGG(CAST(id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY id) as id","inv_serial_no_details","issue_trans_id=".$row[csf("id")],"id");
		}

		$cons_quantity=$row[csf('cons_quantity')];
		$cons_quantity_sum += $cons_quantity;
	?>
		<tr bgcolor="<? echo $bgcolor; ?>">
			<td align="center"><? echo $i; ?></td>
			<td><? echo $item_category[$row[csf("item_category_id")]]; ?></td>
			<td><? echo $group_arr[$row[csf("item_group_id")]]; ?></td>
			<td><? echo $row[csf("item_description")]; ?></td>
            <td><? echo $row[csf("item_size")]; ?></td>
			<td align="center"><? echo $store_arr[$row[csf("store_id")]]; ?></td>
			<td align="right"><? echo number_format($row[csf("cons_quantity")],2); ?></td>
			<td align="center"><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></td>
			<td><? echo $serialNo; ?></td>
			<td><? echo $machine_category[$row[csf("machine_category")]]; ?></td>
			<td ><? echo $floor_arr[$row[csf("production_floor")]]; ?></td>
			<td align="center"><? echo $machine_arr[$row[csf("machine_id")]]; ?></td>
			<td align="center"><? echo $po_number_arr[$row[csf("order_id")]]; ?></td>
			<td><? echo $division_arr[$row[csf('division_id')]].', '.$department_arr[$row[csf("department_id")]].', '.$section_arr[$row[csf("section_id")]]; ?></td>
			<td align="center"><? echo $row[csf("batch_lot")]; ?></td>
        </tr>
		<? $i++; } ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="6" align="right">Total :</td>
            <td align="right"><? echo number_format($cons_quantity_sum,2); ?></td>
            <td colspan="8">&nbsp;</td>
        </tr>
    </tfoot>
    </table>
        <br>
		 <?
            echo signature_table(12, $data[0], "900px","","",$issue_by);
         ?>
	</div>
	</div>
     <script type="text/javascript" src="../../../js/jquery.js"></script>
      <script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
     <script>

	function generateBarcode( valuess ){

			var value = valuess;//$("#barcodeValue").val();
		 // alert(value)
			var btype = 'code39';//$("input[name=btype]:checked").val();
			var renderer ='bmp';// $("input[name=renderer]:checked").val();

			var settings = {
			  output:renderer,
			  bgColor: '#FFFFFF',
			  color: '#000000',
			  barWidth: 1,
			  barHeight: 30,
			  moduleSize:5,
			  posX: 10,
			  posY: 20,
			  addQuietZone: 1
			};
			$("#barcode_img_id").html('11');
			 value = {code:value, rect: false};

			$("#barcode_img_id").show().barcode(value, btype, settings);

		}

	 generateBarcode('<? echo $data[2]; ?>');


	 </script>


	<?
	exit();
}

if ($action=="general_item_issue_print_3")
{
    extract($_REQUEST);
	$data=explode('*',$data);
	$company=$data[0];
	$location=$data[4];
	$system_no = $data[2];

	
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$country_arr = return_library_array( "select id, country_name from lib_country", "id", "country_name"  );
	$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name"  );
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$floor_arr = return_library_array("select id, floor_name from  lib_prod_floor","id","floor_name");
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");

	$location_arr=return_library_array("select id,location_name from lib_location",'id','location_name');
	$department_arr=return_library_array("select id,department_name from lib_department",'id','department_name');
	$section_arr=return_library_array("select id,section_name from lib_section",'id','section_name');
	$store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');
	$po_number_arr=return_library_array( "select id, po_number from wo_po_break_down where is_deleted=0",'id','po_number');
 	$machine_arr = return_library_array("select id, machine_no from lib_machine_name","id","machine_no");
	$division_arr=return_library_array("select id,division_name from lib_division",'id','division_name');
	$sewing_line_arr = return_library_array("select id,line_name from lib_sewing_line","id","line_name");

	$sql="SELECT a.id as ID, a.issue_number as ISSUE_NUMBER, a.issue_date as ISSUE_DATE, 	a.issue_basis as ISSUE_BASIS, a.challan_no as CHALLAN_NO,a.issue_purpose as ISSUE_PURPOSE,a.req_no as REQ_NO, b.store_id as STORE_ID, a.knit_dye_source as KNIT_DYE_SOURCE, a.knit_dye_company as KNIT_DYE_COMPANY, a.knit_dye_location as KNIT_DYE_LOCATION, a.location_id as LOCATION_ID, a.attention as ATTENTION, a.remarks as REMARKS from inv_issue_master a, inv_transaction b where a.id=b.mst_id and a.id='$data[1]' and b.transaction_type=2 and a.entry_form=21 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";

	//echo $sql;
	$dataArray=sql_select($sql);

	$location='';
	if($dataArray[0]['KNIT_DYE_SOURCE']==1)
	{
		$caption="Location";
		$issueTo=$company_library[$dataArray[0]['KNIT_DYE_COMPANY']];
		$location=$location_arr[$dataArray[0]['KNIT_DYE_LOCATION']];
		//$location=return_field_value("location_name","lib_location","id='".$dataArray[0]['LOCATION_ID']."'");
	}
	else
	{
		$caption="Address";
		$supplierData=sql_select("SELECT address_1 as ADDRESS_1, address_2 as ADDRESS_2, supplier_name as SUPPLIER_NAME from lib_supplier where id='".$dataArray[0]['KNIT_DYE_COMPANY']."'");
		$issueTo=$supplierData[0]['SUPPLIER_NAME'];
		$location=$supplierData[0]['ADDRESS_1'];
		if($location=="") $location=$supplierData[0]['ADDRESS_2']; else $location.=", ".$supplierData[0]['ADDRESS_2'];
	}
	
	//for gate pass
	$sql_get_pass = "SELECT a.id as ID, a.sys_number as SYS_NUMBER, a.basis as BASIS, a.company_id as COMPANY_ID, a.get_pass_no as GET_PASS_NO, a.department_id as DEPARTMENT_ID, a.attention as ATTENTION, a.sent_by as SENT_BY, a.within_group as WITHIN_GROUP, a.sent_to as SENT_TO, a.challan_no as CHALLAN_NO, a.out_date as OUT_DATE, a.time_hour as TIME_HOUR, a.time_minute as TIME_MINUTE, a.returnable as RETURNABLE, a.delivery_as as DELIVERY_AS, a.est_return_date as EST_RETURN_DATE, a.inserted_by as INSERTED_BY, a.carried_by as CARRIED_BY, a.location_id as LOCATION_ID, a.com_location_id as COM_LOCATION_ID, a.vhicle_number as VHICLE_NUMBER, a.location_name as LOCATION_NAME, a.remarks as REMARKS, a.do_no as DO_NO, a.mobile_no as MOBILE_NO, a.issue_id as ISSUE_ID, a.returnable_gate_pass_reff as RETURNABLE_GATE_PASS_REFF, a.delivery_company as DELIVERY_COMPANY, a.issue_purpose as ISSUE_PURPOSE,a.security_lock_no as SECURITY_LOCK_NO,a.driver_name as DRIVER_NAME,a.driver_license_no as DRIVER_LICENSE_NO, b.quantity as QUANTITY, b.no_of_bags as NO_OF_BAGS FROM inv_gate_pass_mst a, inv_gate_pass_dtls b WHERE a.id = b.mst_id AND a.company_id ='$data[0]' AND a.basis = 5 AND a.status_active = 1 AND a.is_deleted = 0 AND a.challan_no LIKE '".$system_no."%'";
   // echo $sql_get_pass;
   $sql_get_pass_rslt = sql_select($sql_get_pass);
	$is_gate_pass = 0;
	$is_gate_out = 0;
	$gate_pass_id = '';
	$gatePassDataArr = array();
	foreach($sql_get_pass_rslt as $row)
	{
		$exp = explode(',', $row['CHALLAN_NO']);
		foreach($exp as $key=>$val)
		{
			if($val == $system_no)
			{
				$is_gate_pass = 1;
				$gate_pass_id = $row['ID'];
				
				$row['OUT_DATE'] = ($row['OUT_DATE']!=''?date('d-m-Y', strtotime($row['OUT_DATE'])):'');
				$row['EST_RETURN_DATE'] = ($row['EST_RETURN_DATE']!=''?date('d-m-Y', strtotime($row['EST_RETURN_DATE'])):'');
				$row['EST_RETURN_DATE'] = ($row['EST_RETURN_DATE']!=''?date('d-m-Y', strtotime($row['EST_RETURN_DATE'])):'');
				
				if($row['WITHIN_GROUP'] == 1)
				{
					//$row['SENT_TO'] = ($row['BASIS']==50?$buyer_dtls_arr[$row['SENT_TO']]:$supplier_dtls_arr[$row['SENT_TO']]);
					$row['SENT_TO'] = $company_library[$row['SENT_TO']];
					$row['LOCATION_NAME'] = $location_arr[$row['LOCATION_ID']];
				}
				
				//for gate pass info
				$gatePassDataArr[$row['CHALLAN_NO']]['gate_pass_id'] = $row['SYS_NUMBER'];
				$gatePassDataArr[$row['CHALLAN_NO']]['from_company'] = $company_library[$row['COMPANY_ID']];
				$gatePassDataArr[$row['CHALLAN_NO']]['from_location'] =$location_arr[ $row['COM_LOCATION_ID']];
				$gatePassDataArr[$row['CHALLAN_NO']]['gate_pass_date'] = date('d-m-Y', strtotime($row['OUT_DATE']));
				$gatePassDataArr[$row['CHALLAN_NO']]['returnable'] = $yes_no[$row['RETURNABLE']];
				$gatePassDataArr[$row['CHALLAN_NO']]['est_return_date'] = $row['EST_RETURN_DATE'];
				
				$gatePassDataArr[$row['CHALLAN_NO']]['to_company'] = $row['SENT_TO'];
				$gatePassDataArr[$row['CHALLAN_NO']]['to_location'] = $row['LOCATION_NAME'];
				$gatePassDataArr[$row['CHALLAN_NO']]['delivery_kg'] += $row['QUANTITY'];
				$gatePassDataArr[$row['CHALLAN_NO']]['delivery_bag'] += $row['NO_OF_BAGS'];
				
				$gatePassDataArr[$row['CHALLAN_NO']]['department'] = $department_arr[$row['DEPARTMENT_ID']];
				$gatePassDataArr[$row['CHALLAN_NO']]['attention'] = $row['ATTENTION'];
				$gatePassDataArr[$row['CHALLAN_NO']]['issue_purpose'] = $row['ISSUE_PURPOSE'];
				$gatePassDataArr[$row['CHALLAN_NO']]['remarks'] = $row['REMARKS'];
				$gatePassDataArr[$row['CHALLAN_NO']]['carried_by'] = $row['CARRIED_BY'];
				$gatePassDataArr[$row['CHALLAN_NO']]['vhicle_number'] = $row['VHICLE_NUMBER'];
				$gatePassDataArr[$row['CHALLAN_NO']]['mobile_no'] = $row['MOBILE_NO'];
				$gatePassDataArr[$row['CHALLAN_NO']]['security_lock_no'] = $row['SECURITY_LOCK_NO'];
				$gatePassDataArr[$row['CHALLAN_NO']]['driver_name'] = $row['DRIVER_NAME'];
				$gatePassDataArr[$row['CHALLAN_NO']]['driver_license_no'] = $row['DRIVER_LICENSE_NO'];
			}
		}
	}
	
	//for gate out
	if($gate_pass_id != '')
	{
		$sql_gate_out="SELECT out_date as OUT_DATE, out_time as OUT_TIME from inv_gate_out_scan where status_active = 1 and is_deleted = 0 and inv_gate_pass_mst_id='".$gate_pass_id."'";
		$sql_gate_out_rslt = sql_select($sql_gate_out);
		if(!empty($sql_gate_out_rslt))
		{
			foreach($sql_gate_out_rslt as $row)
			{
				$is_gate_out = 1;
				$gatePassDataArr[$system_no]['out_date'] = date('d-m-Y', strtotime($row['OUT_DATE']));
				$gatePassDataArr[$system_no]['out_time'] = $row['OUT_TIME'];
			}
		}
	}

	function company_location_address($com_id, $loc_id=0, $short=0)
	{
		$comp_logo = return_field_value("image_location", "common_photo_library", "file_type=1 and form_name='company_details' and master_tble_id='$com_id'", "image_location");
		$country_full_name = return_library_array("SELECT id,country_name from lib_country", "id", "country_name");
		$company_info_dtls=array();
		if($loc_id!='' && $loc_id!=0){
			$location_adress= return_field_value("address","lib_location","company_id=$com_id and id=$loc_id ","address");
			$company_name= return_field_value("company_name","lib_company","id=$com_id","company_name");
			$company_info_dtls[0]	= $company_name;
			$company_info_dtls[1] 	= $location_adress;
			$company_info_dtls[2] 	= $comp_logo;
		}
		else
		{
			$sql = "SELECT * FROM lib_company WHERE id = $com_id  AND is_deleted=0 AND status_active=1 ORDER BY company_name ASC";
			$result = sql_select( $sql );
			foreach( $result as $row  )
			{
				if($short==1){
					if($row[csf("level_no")])		$level_no	= $row[csf("level_no")].', ';
					if($row[csf("plot_no")])		$plot_no 	= $row[csf("plot_no")].', ';
					if($row[csf("road_no")]) 		$road_no 	= $row[csf("road_no")].', ';
					if($row[csf("block_no")]!='')	$block_no 	= $row[csf("block_no")].', ';
					if($row[csf("zip_code")]!='')	$zip_code 	= ' -'.$row[csf("zip_code")].', ';
					if($row[csf("city")]!='') $city 			= ($zip_code!="")? $row[csf("city")]:$row[csf("city")]." ,";
					if($row[csf("country_id")]!='')	$country 	= $country_full_name[$row[csf("country_id")]].', ';
					if($row[csf("contact_no")]!='')	$contact_no = $row[csf("contact_no")].'.';
				}
				else if($short==2){
					if($row[csf("level_no")])$level_no 		= "".$row[csf("level_no")].', ';
					if($row[csf("plot_no")])$plot_no 		= "".$row[csf("plot_no")].', ';
					if($row[csf("road_no")]) $road_no 		= "".$row[csf("road_no")].', ';
					if($row[csf("block_no")]!='')$block_no 	= "".$row[csf("block_no")].', ';
					if($row[csf("zip_code")]!='')$zip_code 	= ' -'.$row[csf("zip_code")].', ';
					if($row[csf("city")]!='') $city 		= ($zip_code!="")?"".$row[csf("city")]:"".$row[csf("city")].", ";
					if($row[csf("country_id")]!='')$country = $country_full_name[$row[csf("country_id")]].'.';
					//if($row[csf("contact_no")]!='')	$contact_no = "".$row[csf("contact_no")].'.';
				}else{
					$company_info_dtls[0]	= 0;
					$company_info_dtls[1] 	= "Error Found.";
				}
				$company_info_dtls[0]	= $row[csf("company_name")];
				$company_info_dtls[1] 	= rtrim($level_no.$plot_no.$road_no.$block_no.$city.$zip_code.$country.$contact_no,", ");
				$company_info_dtls[2] 	= $comp_logo;
			}
		}
		return $company_info_dtls;
	}

	$com_dtls = company_location_address($company, $location, 2);
	
	?>
	<div style="width:1000px;">
    <table width="1000" cellspacing="0" border="0">
        <tr>
            <td colspan="2" rowspan="3" width="200">
			<img src="../../../<? echo $com_dtls[2]; ?>" height="60" width="200" style="float:left;">
			</td>
			<td colspan="4"  style="font-size:xx-large;justify-content: center;text-align: center;" >
            	<strong style="justify-content: center;text-align: center;"><? echo $com_dtls[0]; ?></strong>
            </td>
			<td colspan="2" rowspan="2" align="right" width="150"><?php echo ($is_gate_pass==1?"<br><span style=\"color:#F00;font-weight:bold;\">Gate Pass Done</span>":'').($is_gate_out==1?"<br><span style=\"color:#F00;font-weight:bold;\">Gate Out Done</span>":''); ?></td>
           
        </tr>
        <tr class="form_caption">
        	<td colspan="4"  style="font-size:14px;justify-content: center;text-align: center;">
				<? 
					echo $com_dtls[1];
                ?>
            </td>
        </tr>
        <tr>
            <td colspan="4"  style="font-size:x-large;justify-content: center;text-align: center;"><strong style="justify-content: center;text-align: center;"><u>GENERAL ITEM DELIVERY CHALLAN</u></strong></td>
        </tr>
        
    </table>
    <br>

	<table cellspacing="0" width="1000" align="center" border="1" rules="all" class="">
			<tr>
				<td width="90"><strong>Source:</strong></td> <td width="175px"><? echo $knitting_source[$dataArray[0]['KNIT_DYE_SOURCE']]; ?></td>
				<td width="90"><strong>Store Name:</strong></td><td width="175px"><? echo $store_library[$dataArray[0]['STORE_ID']];?></td>
				<td width="95"><strong>Issue No:</strong></td><td width="175px"><? echo $dataArray[0]['ISSUE_NUMBER']; ?></td>
			</tr>
			<tr>
				<td ><strong>Issue To:</strong></td> <td ><? echo $issueTo; ?></td>
				<td><strong>Issue Purpose:</strong></td><td ><? echo $general_issue_purpose[$dataArray[0]['ISSUE_PURPOSE']]; ?></td>
				<td ><strong>Issue Date:</strong></td><td ><? echo change_date_format($dataArray[0]['ISSUE_DATE']);?></td>
			</tr>
			<tr>
				<td ><strong>Address:</strong></td> <td><? echo chop($location,', '); ?></td>
				<td><strong>Attention:</strong></td><td ><? echo $dataArray[0]['ATTENTION']; ?></td>
				<td ><strong>Store Req. No:</strong></td><td ><? echo $dataArray[0]['REQ_NO'];?></td>
			</tr>
			<tr>
				<td><strong>Remarks :</strong></td><td colspan="5"><? echo $dataArray[0]['REMARKS']; ?></td>
			</tr>
			<tr>
				<td colspan="6"  rowspan="2" align="center" id="barcode_img_id" height="50" valign="middle" style="border-left:hidden;border-right:hidden;border-bottom:hidden;"></td>
			</tr>
		</table>
		<br>
   
	<div style="width:100%;">
    <table cellspacing="0" width="1000"  border="1" rules="all" class="rpt_table" >
        <thead bgcolor="#dddddd" style="font-size:13px">
            <th width="30">SL</th>
            <th width="50">Product id</th>
            <th width="80">Item Category</th>
            <th width="120">Item Group</th>
            <th width="60">Item Sub Group</th>
            <th width="130">Item Description</th>
            <th width="60">Item Size</th>
            <th width="40">Issue Qty</th>
            <th width="40">UOM</th>
            <th width="70">Floor</th>
            <th width="70">Line No</th>
            <th width="50">Machine No</th>
            <th width="60">Buyer Order</th>
            <th  width="60">Div./Dept.</th>
            <th  width="60">Lot</th>
        </thead>
    <tbody style="font-size:13px">
	<?
	//$mrr_no=$dataArray[0][csf('issue_number')];
	$cond="";
	if($data[1]!="") $cond .= " and a.id='$data[1]'";
	
	$group_arr=return_library_array( "select id,item_name from lib_item_group where item_category not in (1,2,3,4,5,6,7,12,13,14) and status_active=1 and is_deleted=0",'id','item_name');

	$i=1;
	$sql = "select a.issue_number, b.id, b.store_id, b.cons_uom, b.cons_quantity, b.floor_id, b.machine_category, b.machine_id, b.prod_id, b.location_id, b.department_id, b.section_id, b.order_id,b.division_id,b.line_id,b.production_floor, c.item_category_id, c.item_description, c.item_group_id, c.item_size,c.sub_group_name,b.batch_lot from inv_issue_master a, inv_transaction b, product_details_master c where a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=2 and a.entry_form=21 and b.status_active=1 and b.is_deleted=0 and c.status_active in(1,3) and c.is_deleted=0 $cond";
	//echo $sql;
	$sql_result = sql_select($sql);


	foreach($sql_result as $row)
	{
		if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		if($db_type==0)
		{
			$serialNo=return_field_value("group_concat(serial_no)","inv_serial_no_details","issue_trans_id=".$row[csf("id")]);
			$serialID=return_field_value("group_concat(id)","inv_serial_no_details","issue_trans_id=".$row[csf("id")]);
		}
		else
		{
			$serialNo=return_field_value("LISTAGG(CAST(serial_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY serial_no) as sr","inv_serial_no_details","issue_trans_id=".$row[csf("id")],"sr");
			$serialID=return_field_value("LISTAGG(CAST(id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY id) as id","inv_serial_no_details","issue_trans_id=".$row[csf("id")],"id");
		}

		$cons_quantity=$row[csf('cons_quantity')];
		$cons_quantity_sum += $cons_quantity;
	?>
		<tr bgcolor="<? echo $bgcolor; ?>">
			<td align="center"><? echo $i; ?></td>
			<td align="center" style="word-break:break-all;"><? echo $row[csf("prod_id")]; ?></td>
			<td align="center" style="word-break:break-all;"><? echo $item_category[$row[csf("item_category_id")]]; ?></td>
			<td align="center" style="word-break:break-all;"><? echo $group_arr[$row[csf("item_group_id")]]; ?></td>
			<td align="center" style="word-break:break-all;"><? echo $row[csf("sub_group_name")]; ?></td>
			<td align="center" style="word-break:break-all;"><? echo $row[csf("item_description")]; ?></td>
			<td align="center" style="word-break:break-all;"><? echo $row[csf("item_size")]; ?></td>
			<td align="center" style="word-break:break-all;"><? echo number_format($row[csf("cons_quantity")],2); ?></td>
			<td align="center" style="word-break:break-all;"><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></td>
			<td align="center" style="word-break:break-all;"><? echo $floor_arr[$row[csf("production_floor")]]; ?></td>
			<td align="center" style="word-break:break-all;"><? echo $sewing_line_arr[$row[csf("line_id")]]; ?></td>
			<td align="center" style="word-break:break-all;"><? echo $machine_arr[$row[csf("machine_id")]]; ?></td>
			<td align="center" style="word-break:break-all;"><? echo $po_number_arr[$row[csf("order_id")]]; ?></td>
			<td align="center" style="word-break:break-all;"><? echo chop($division_arr[$row[csf('division_id')]].', '.$department_arr[$row[csf("department_id")]],', '); ?></td>
			<td align="center" style="word-break:break-all;"><? echo$row[csf("batch_lot")]; ?></td>

		</tr>
		<? $i++; } ?>
    </tbody>
    </table>
	</div>
	<br>
	<div style="width:1010px;clear:both;">
	For mishandling or other reason no claim is acceptable in any stage, once the Goods is received in good condition and quantity and out from factory premise.
	</div>
	<div style="width:100%;">
			<br>
			<table align="right" cellspacing="0" width="1000" border="1" rules="all" class="rpt_table" >
				<tbody>
					<tr>
						<td colspan="3" align="center" valign="middle" style="font-size:25px;"><strong>&lt;&lt;Gate Pass&gt;&gt;</strong></td>
						<td colspan="3" align="center" valign="middle" id="gate_pass_barcode_img_id" height="50"></td>
					</tr>
					<tr>
						<td width="150"><strong>From Company:</strong></td>
						<td width="150"><?php echo $gatePassDataArr[$system_no]['from_company']; ?></td>
						<td width="150"><strong>To Company:</strong></td>
						<td width="150"><?php echo $gatePassDataArr[$system_no]['to_company']; ?></td>
						<td width="150"><strong>Carried By:</strong></td>
						<td width="150"><?php echo $gatePassDataArr[$system_no]['carried_by']; ?></td>
					</tr>						
					<tr>
						<td ><strong>From Location:</strong></td>
						<td ><?php echo $gatePassDataArr[$system_no]['from_location']; ?></td>
						<td ><strong>To Location:</strong></td>
						<td ><?php echo $gatePassDataArr[$system_no]['to_location']; ?></td>
						<td ><strong>Driver Name:</strong></td>
						<td ><?php echo $gatePassDataArr[$system_no]['driver_name']; ?></td>
					</tr>
					<tr>
						<td><strong>Gate Pass ID:</strong></td>
						<td><?php echo $gatePassDataArr[$system_no]['gate_pass_id']; ?></td>
						<td rowspan="2"><strong>Delivery Qnty</strong></td>
						<td rowspan="2"><?php echo $gatePassDataArr[$system_no]['delivery_kg']; ?></td>
						<!-- <td align="center"><strong>Kg</strong></td>
						<td align="center"><strong>Bag</td> -->
						<td ><strong>Vehicle Number:</strong></td>
						<td ><?php echo $gatePassDataArr[$system_no]['vhicle_number']; ?></td>
					</tr>						
					<tr>
						<td ><strong>Gate Pass Date:</strong></td>
						<td ><?php echo $gatePassDataArr[$system_no]['gate_pass_date']; ?></td>
						<!-- <td align="center"><?php echo $gatePassDataArr[$system_no]['delivery_kg']; ?></td>
						<td align="center"><?php echo $gatePassDataArr[$system_no]['delivery_bag']; ?></td> -->
						<td ><strong>Driver License No.:</strong></td>
						<td ><?php echo $gatePassDataArr[$system_no]['driver_license_no']; ?></td>
					</tr>						
					<tr>
						<td ><strong>Out Date:</strong></td>
						<td ><?php echo $gatePassDataArr[$system_no]['out_date']; ?></td>
						<td ><strong>Dept. Name:</strong></td>
						<td ><?php echo $gatePassDataArr[$system_no]['department']; ?></td>
						<td ><strong>Mobile No.:</strong></td>
						<td ><?php echo $gatePassDataArr[$system_no]['mobile_no']; ?></td>
					</tr>						
					<tr>
						<td ><strong>Out Time:</strong></td>
						<td ><?php echo $gatePassDataArr[$system_no]['out_time']; ?></td>
						<td ><strong>Attention:</strong></td>
						<td ><?php echo $gatePassDataArr[$system_no]['attention']; ?></td>
						<td ><strong>Sequrity Lock No.:</strong></td>
						<td ><?php echo $gatePassDataArr[$system_no]['security_lock_no']; ?></td>
					</tr>						
					<tr>
						<td ><strong>Returnable:</strong></td>
						<td ><?php echo $gatePassDataArr[$system_no]['returnable']; ?></td>
						<td ><strong>Purpose:</strong></td>
						<td colspan="3"><?php echo $gatePassDataArr[$system_no]['issue_purpose']; ?></td>
					</tr>						
					<tr>
						<td ><strong>Est. Return Date:</strong></td>
						<td ><?php echo $gatePassDataArr[$system_no]['est_return_date']; ?></td>
						<td ><strong>Remarks:</strong></td>
						<td colspan="3"><?php echo $gatePassDataArr[$system_no]['remarks']; ?></td>
					</tr>						
				</tbody>	
			</table>
			<br>
			<div>
				<br> &nbsp; &nbsp;
			</div>
			<table cellspacing="0" width="1000"  border="1" rules="all" class="rpt_table">
				<thead bgcolor="#dddddd" style="font-size:13px">
					<th width="30">SL</th>
					<th width="50">Product id</th>
					<th width="80">Item Category</th>
					<th width="120">Item Group</th>
					<th width="60">Item Sub Group</th>
					<th width="130">Item Description</th>
					<th width="60">Item Size</th>
					<th width="40">Issue Qty</th>
					<th width="40">UOM</th>
					<th width="70">Floor</th>
					<th width="70">Line No</th>
					<th width="50">Machine No</th>
					<th width="60">Buyer Order</th>
					<th  width="60">Div./Dept.</th>
					<th  width="60">Lot</th>
				</thead>
			<tbody style="font-size:13px">
			<?
			//$mrr_no=$dataArray[0][csf('issue_number')];
			$cond="";
			if($data[1]!="") $cond .= " and a.id='$data[1]'";
			
			$group_arr=return_library_array( "select id,item_name from lib_item_group where item_category not in (1,2,3,4,5,6,7,12,13,14) and status_active=1 and is_deleted=0",'id','item_name');

			$i=1;
			$sql = "select a.issue_number, b.id, b.store_id, b.cons_uom, b.cons_quantity, b.floor_id, b.machine_category, b.machine_id, b.prod_id, b.location_id, b.department_id, b.section_id, b.order_id,b.division_id,b.line_id,b.production_floor, c.item_category_id, c.item_description, c.item_group_id, c.item_size,c.sub_group_name,b.batch_lot from inv_issue_master a, inv_transaction b, product_details_master c where a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=2 and a.entry_form=21 and b.status_active=1 and b.is_deleted=0 and c.status_active in(1,3) and c.is_deleted=0 $cond";
			//echo $sql;
			$sql_result = sql_select($sql);


			foreach($sql_result as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($db_type==0)
				{
					$serialNo=return_field_value("group_concat(serial_no)","inv_serial_no_details","issue_trans_id=".$row[csf("id")]);
					$serialID=return_field_value("group_concat(id)","inv_serial_no_details","issue_trans_id=".$row[csf("id")]);
				}
				else
				{
					$serialNo=return_field_value("LISTAGG(CAST(serial_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY serial_no) as sr","inv_serial_no_details","issue_trans_id=".$row[csf("id")],"sr");
					$serialID=return_field_value("LISTAGG(CAST(id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY id) as id","inv_serial_no_details","issue_trans_id=".$row[csf("id")],"id");
				}

				$cons_quantity=$row[csf('cons_quantity')];
				$cons_quantity_sum += $cons_quantity;
			?>
				<tr bgcolor="<? echo $bgcolor; ?>">
					<td align="center"><? echo $i; ?></td>
					<td align="center" style="word-break:break-all;"><? echo $row[csf("prod_id")]; ?></td>
					<td align="center" style="word-break:break-all;"><? echo $item_category[$row[csf("item_category_id")]]; ?></td>
					<td align="center" style="word-break:break-all;"><? echo $group_arr[$row[csf("item_group_id")]]; ?></td>
					<td align="center" style="word-break:break-all;"><? echo $row[csf("sub_group_name")]; ?></td>
					<td align="center" style="word-break:break-all;"><? echo $row[csf("item_description")]; ?></td>
					<td align="center" style="word-break:break-all;"><? echo $row[csf("item_size")]; ?></td>
					<td align="center" style="word-break:break-all;"><? echo number_format($row[csf("cons_quantity")],2); ?></td>
					<td align="center" style="word-break:break-all;"><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></td>
					<td align="center" style="word-break:break-all;"><? echo $floor_arr[$row[csf("production_floor")]]; ?></td>
					<td align="center" style="word-break:break-all;"><? echo $sewing_line_arr[$row[csf("line_id")]]; ?></td>
					<td align="center" style="word-break:break-all;"><? echo $machine_arr[$row[csf("machine_id")]]; ?></td>
					<td align="center" style="word-break:break-all;"><? echo $po_number_arr[$row[csf("order_id")]]; ?></td>
					<td align="center" style="word-break:break-all;"><? echo chop($division_arr[$row[csf('division_id')]].', '.$department_arr[$row[csf("department_id")]],', '); ?></td>
					<td align="center" style="word-break:break-all;"><? echo$row[csf("batch_lot")]; ?></td>

				</tr>
				<? $i++; } ?>
			</tbody>
			</table>
			
			<?
			  echo signature_table(12, $data[0], "1000px");
			?>
		</div>
	</div>
     <script type="text/javascript" src="../../../js/jquery.js"></script>
      <script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
     <script>

	function generateBarcode( valuess )
	{

		var value = valuess;//$("#barcodeValue").val();
		// alert(value)
		var btype = 'code39';//$("input[name=btype]:checked").val();
		var renderer ='bmp';// $("input[name=renderer]:checked").val();

		var settings = {
			output:renderer,
			bgColor: '#FFFFFF',
			color: '#000000',
			barWidth: 1,
			barHeight: 30,
			moduleSize:5,
			posX: 10,
			posY: 20,
			addQuietZone: 1
		};
		$("#barcode_img_id").html('11');
			value = {code:value, rect: false};

		$("#barcode_img_id").show().barcode(value, btype, settings);

	}

	generateBarcode('<? echo $data[2]; ?>');

	 //for gate pass barcode
	function generateBarcodeGatePass(valuess)
	{
		var zs = '<?php echo $x; ?>';
		var value = valuess;//$("#barcodeValue").val();
		var btype = 'code39';//$("input[name=btype]:checked").val();
		var renderer = 'bmp';// $("input[name=renderer]:checked").val();
		var settings = {
			output: renderer,
			bgColor: '#FFFFFF',
			color: '#000000',
			barWidth: 1,
			barHeight: 30,
			moduleSize: 5,
			posX: 10,
			posY: 20,
			addQuietZone: 1
		};
		value = {code: value, rect: false};
		$("#gate_pass_barcode_img_id").show().barcode(value, btype, settings);
	}
	if('<? echo $gatePassDataArr[$system_no]['gate_pass_id']; ?>' != '')
	{
		generateBarcodeGatePass('<? echo strtoupper($gatePassDataArr[$system_no]['gate_pass_id']); ?>');
	}
	</script>
	<?
	exit();
}

if ($action=="general_item_issue_print_4")
{
    extract($_REQUEST);
	$data=explode('*',$data);
	$comapy_id=$data[0];
	$mst_id=$data[1];
	$system_no = $data[2];

	$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$user_arr=return_library_array( "select id, user_full_name from user_passwd", "id", "user_full_name");
	$store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');
	$division_arr=return_library_array("select id,division_name from lib_division",'id','division_name');
	$department_arr=return_library_array("select id,department_name from lib_department",'id','department_name');
	$section_arr=return_library_array("select id,section_name from lib_section",'id','section_name');
	$group_arr=return_library_array( "select id,item_name from lib_item_group where item_category not in (1,2,3,4,5,6,7,12,13,14) and status_active=1 and is_deleted=0",'id','item_name');
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$comapy_id'","image_location");	
	

	$sql_mst="SELECT a.id as ID, a.issue_number as ISSUE_NUMBER, a.issue_date as ISSUE_DATE, a.issue_basis as ISSUE_BASIS, a.challan_no as CHALLAN_NO, a.issue_purpose as ISSUE_PURPOSE, a.req_no as REQ_NO, a.remarks as REMARKS, a.inserted_by as INSERTED_BY from inv_issue_master a where a.id='$mst_id' and a.entry_form=21 and a.status_active=1 and a.is_deleted=0";
	//echo $sql_mst;	
	$dataArray=sql_select($sql_mst);

	$issue_by=$dataArray[0]['INSERTED_BY'];
	$issue_req_no=$dataArray[0]['REQ_NO'];
	$sql_itemIssueReq="SELECT a.id as REQ_ID, a.itemissue_req_sys_id as REQ_NO, a.indent_date as INDENT_DATE, a.store_id as STORE_ID, a.division_id as DIVISION_ID, a.department_id as DEPARTMENT_ID, a.section_id as SECTION_ID, a.inserted_by as INSERTED_BY, b.req_qty as ITEM_ISSUE_REQ_QTY, b.product_id as PRODUCT_ID from inv_item_issue_requisition_mst a, inv_itemissue_requisition_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and a.itemissue_req_sys_id='$issue_req_no' and b.status_active=1 and b.is_deleted=0";
	$sql_itemIssueReq_res=sql_select($sql_itemIssueReq);
	$req_id=$sql_itemIssueReq_res[0]['REQ_ID'];
	$requisition_by=$user_arr[$sql_itemIssueReq_res[0]['INSERTED_BY']];
	$item_issue_req_qty_arr=array();
	foreach ($sql_itemIssueReq_res as $row)
	{
		$item_issue_req_qty_arr[$row['PRODUCT_ID']]=$row['ITEM_ISSUE_REQ_QTY'];
	}

	$cond="";
	if($mst_id !="") $cond = " and a.id='$mst_id'";				
	$sql = "SELECT a.issue_number as ISSUE_NUMBER, b.id as TRANS_ID, b.store_id as STORE_ID, b.cons_uom as CONS_UOM, b.cons_quantity as ISSUE_QTY, b.remarks as REMARKS, c.id as PROD_ID, c.item_code as ITEM_CODE, c.item_category_id as ITEM_CATEGORY_ID, c.item_description as ITEM_DESCRIPTION, c.item_group_id as ITEM_GROUP_ID, c.item_size as ITEM_SIZE, c.sub_group_name as SUB_GROUP_NAME ,b.batch_lot as BATCH_LOT  from inv_issue_master a, inv_transaction b, product_details_master c where a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=2 and a.entry_form=21 and b.status_active=1 and b.is_deleted=0 and c.status_active in(1,3) and c.is_deleted=0 $cond";
	// echo $sql;
	$sql_result = sql_select($sql);
	foreach ($sql_result as $row) 
	{
		$product_id.=$row['PROD_ID'].',';
		$store_id.=$row['STORE_ID'].',';
	}
	$product_ids=implode(',', array_flip(array_flip(explode(',',rtrim($product_id,',')))));
	$store_ids=implode(',', array_flip(array_flip(explode(',',rtrim($store_id,',')))));

	$sql_stock=sql_select("SELECT prod_id as PROD_ID, sum(case when transaction_type in(1,4,5) then cons_quantity else 0 end) - sum(case when transaction_type in(2,3,6) then cons_quantity else 0 end) as BALANCE_STOCK from inv_transaction where prod_id in($product_ids) and store_id in($store_ids) and status_active=1 and is_deleted=0 group by prod_id");
	$stock_qty_arr=array();
	foreach ($sql_stock as $row) {
		$stock_qty_arr[$row['PROD_ID']]=$row['BALANCE_STOCK'];
	}

	$cumilative_issue=sql_select("select a.issue_date as ISSUE_DATE, b.id as TRANS_ID, b.prod_id as PROD_ID, b.cons_quantity as ISSUE_QTY from inv_issue_master a, inv_transaction b where a.id=b.mst_id and b.transaction_type=2 and a.entry_form=21 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.req_no='$issue_req_no'");
	$cumilative_issue_arr=array();
	foreach ($cumilative_issue as $row) {
		$cumilative_issue_arr[$row['PROD_ID']][$row['TRANS_ID']]['ISSUE_QTY']=$row['ISSUE_QTY'];
		$cumilative_issue_arr[$row['PROD_ID']][$row['TRANS_ID']]['ISSUE_DATE']=$row['ISSUE_DATE'];
	}
	
	

	?>
	<div style="width:1000px;">
    <table width="1000" cellspacing="0" border="0">
        <tr>
            <td colspan="2" rowspan="2" width="200">
				<img src="../../../<? echo $image_location; ?>" height="60" width="200" style="float:left;">
			</td>
			<td colspan="6" style="font-size:xx-large; justify-content: center;text-align: center;" >
            	<strong style="justify-content: center; text-align: center;"><? echo $company_arr[$comapy_id]; ?></strong>
            </td>           
        </tr>
        <tr>
            <td colspan="6" style="font-size:x-large; justify-content: center; text-align: center;"><strong style="justify-content: center; text-align: center; "><u>General Item Issue Challan</u></strong></td>
        </tr>
        
    </table>
    <br>
    <table cellspacing="0" width="1000" align="center" border="0">
    	<tr>
			<td width="90"></td><td width="175px"></td>
			<td width="90"><strong>Issue ID:</strong></td><td width="175px"><strong><? echo $dataArray[0]['ISSUE_NUMBER']; ?></strong></td>
			<td width="95"><strong>Issue Date:</strong></td><td width="175px"><strong><? echo change_date_format($dataArray[0]['ISSUE_DATE']); ?></strong></td>
		</tr>
    </table>

	<table cellspacing="0" width="1000" align="center" border="1" rules="all" class="">		
		<tr>
			<td width="90"><strong>Req No:</strong></td><td width="175px"><? echo $issue_req_no; ?></td>
			<td width="90"><strong>Req Date:</strong></td><td width="175px"><? echo change_date_format($sql_itemIssueReq_res[0]['INDENT_DATE']); ?></td>
			<td width="95"><strong>Issuing Store:</strong></td><td width="175px"><? echo $store_arr[$sql_itemIssueReq_res[0]['STORE_ID']]; ?></td>
		</tr>
		<tr>
			<td ><strong>Division:</strong></td><td><? echo $division_arr[$sql_itemIssueReq_res[0]['DIVISION_ID']]; ?></td>
			<td><strong>Department:</strong></td><td ><? echo $department_arr[$sql_itemIssueReq_res[0]['DEPARTMENT_ID']]; ?></td>
			<td ><strong>Section:</strong></td><td><? echo $section_arr[$sql_itemIssueReq_res[0]['SECTION_ID']];?></td>
		</tr>
		<tr>
			<td><strong>Remarks:</strong></td><td colspan="5"><? echo $dataArray[0]['REMARKS']; ?></td>
		</tr>
	</table>
	<table cellspacing="0" width="1000" align="center" border="0">
    	<tr>
			<td colspan="6" align="center"><strong>Line Items</strong></td>
		</tr>
    </table>
   
	<div style="width:100%;">
	    <table cellspacing="0" width="1000"  border="1" rules="all" class="rpt_table" >
	        <thead bgcolor="#dddddd" style="font-size:13px">
	            <th width="30">SL</th>
	            <th width="100">Item Code</th>
	            <th width="100">Item Group</th>
	            <th width="130">Item Description</th>
	            <th width="40">UOM</th>
	            <th width="60">Req. Qty</th>
	            <th width="60">Issue Qty</th>
	            <th width="60">Req. Bal Qty</th>            
	            <th width="70">Stock Qty</th>
	            <th width="130">Cumm. Issue Details</th>
	            <th  width="80">Remarks</th>
	            <th  width="60">Lot</th>
	        </thead>
    		<tbody style="font-size:13px">
				<?				

				$i=1;
				foreach($sql_result as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; 
					else $bgcolor="#FFFFFF";					
					$req_qty=$item_issue_req_qty_arr[$row['PROD_ID']];
					$issue_qty=$row['ISSUE_QTY'];
					$issue_qty_sum += $issue_qty;					
					//$stock_qty=$stock_qty_arr[$row['PROD_ID']]+$issue_qty;
					$stock_qty=$stock_qty_arr[$row['PROD_ID']];
					$cumm_issue_details="";
					$cumm_issue_qty=0;
					foreach ($cumilative_issue_arr[$row['PROD_ID']] as $value) {
						$cumm_issue_details.='Dt>'.$value['ISSUE_DATE'].'&nbsp;Qty>'.$value['ISSUE_QTY'].'<br>';
						$cumm_issue_qty+=$value['ISSUE_QTY'];
					}
					$req_bal_qty=$req_qty-$cumm_issue_qty;
					?>
					<tr bgcolor="<? echo $bgcolor; ?>">
						<td align="center"><? echo $i; ?></td>
						<td align="left" style="word-break:break-all;"><? echo $row["ITEM_CODE"]; ?></td>
						<td align="left" style="word-break:break-all;"><? echo $group_arr[$row["ITEM_GROUP_ID"]]; ?></td>
						<td align="left" style="word-break:break-all;"><? echo $row["ITEM_DESCRIPTION"]; ?></td>
						<td align="center" style="word-break:break-all;"><? echo $unit_of_measurement[$row["CONS_UOM"]]; ?></td>
						<td align="right" style="word-break:break-all;"><? echo number_format($req_qty,2); ?></td>
						<td align="right" style="word-break:break-all;"><? echo number_format($issue_qty,2); ?></td>
						<td align="right" style="word-break:break-all;" title="Req. Qty(<? echo number_format($req_qty,2); ?>) - Cumm. Issue Qty(<? echo number_format($cumm_issue_qty,2); ?>)"><? echo number_format($req_bal_qty,2); ?></td>
						<td align="right" style="word-break:break-all;"><? echo number_format($stock_qty,2); ?></td>
						<td align="left" style="word-break:break-all;"><? echo $cumm_issue_details; ?></td>
						<td align="left" style="word-break:break-all;"><? echo $row["REMARKS"]; ?></td>
						<td align="left" style="word-break:break-all;"><? echo $row["BATCH_LOT"]; ?></td>
					</tr>
					<? 
					$i++; 
				} 
				?>
    		</tbody>
    	</table>
    	<br>
    	<?
		$nameArray_approved=sql_select("SELECT b.approved_by as APPROVED_BY, min(b.approved_date) as APPROVED_DATE from approval_history b where b.mst_id=$req_id and b.entry_form in(26,56) group by mst_id, approved_by order by approved_date");
		if(count($nameArray_approved)>0)
		{
 			?>
	        <table  width="1000" class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all">
	            <thead bgcolor="#dddddd" style="font-size:15px">
	                <tr style="border:1px solid black;">
						<th width="40%" style="border:1px solid black;">Requisition By</th>
						<th width="30%" style="border:1px solid black;">Req. Approved By</th>
						<th width="30%" style="border:1px solid black;">Req. Approved Date</th>
	                </tr>
	            </thead>
	            <tbody>
		            <?
					$i=1;
					foreach($nameArray_approved as $row)
					{
						?>
		           		<tr style="border:1px solid black;">
							<td width="40%" style="border:1px solid black;text-align:center"><? echo $requisition_by;?></td>
							<td width="30%" style="border:1px solid black;text-align:center"><? echo $user_arr[$row['APPROVED_BY']];?></td>
							<td width="30%" style="border:1px solid black;text-align:center"><? echo date ("d-m-Y h:i:s",strtotime($row['APPROVED_DATE']));?></td>				
		                </tr>
		                <?
						$i++;
					}
					?>
	            </tbody>
	        </table>
            <br>
	    	<? 
	    }
		
		$issue_return_sql="select a.ID as MST_ID, a.RECV_NUMBER, a.RECEIVE_DATE, a.CHALLAN_NO, a.REMARKS, c.ITEM_GROUP_ID, c.ITEM_CODE, c.ITEM_DESCRIPTION, c.UNIT_OF_MEASURE, b.ID as TRANS_ID, b.CONS_QUANTITY  
		from inv_receive_master a, inv_transaction b, product_details_master c 
		where a.id=b.mst_id and b.prod_id=c.id and a.entry_form=27 and b.transaction_type=4 and a.status_active=1 and b.status_active=1 and c.status_active in(1,3) and b.issue_id=$mst_id";
		$issue_return_sql_result=sql_select($issue_return_sql);
		if(count($issue_return_sql_result)>0)
		{
 			?>
	        <table  width="1000" class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all">
	            <thead bgcolor="#dddddd" style="font-size:13px">
	                <tr style="border:1px solid black;">
						<th width="50">SL</th>
                        <th width="100">Item Code</th>
						<th width="120">Item Group</th>
                        <th width="150">Item Description</th>
                        <th width="80">UOM</th>
						<th width="120">Return Id</th>
                        <th width="80">Return Date</th>
                        <th width="80">Challan No</th>
                        <th width="120">Remarks</th>
                        <th>Return Qty</th>
	                </tr>
	            </thead>
	            <tbody>
		            <?
					$i=1;
					foreach($issue_return_sql_result as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; 
						else $bgcolor="#FFFFFF";
						?>
		           		<tr bgcolor="<? echo $bgcolor; ?>"  style="font-size:13px">
							<td align="center"><? echo $i;?></td>
							<td align="center"><? echo $row["ITEM_CODE"];?></td>
                            <td><? echo $group_arr[$row["ITEM_GROUP_ID"]];?></td>
                            <td><? echo $row["ITEM_DESCRIPTION"];?></td>
                            <td align="center"><? echo $unit_of_measurement[$row["UNIT_OF_MEASURE"]];?></td>
                            <td><? echo $row["RECV_NUMBER"];?></td>
                            <td align="center"><? echo change_date_format($row["RECEIVE_DATE"]);?></td>
                            <td><? echo $row["CHALLAN_NO"];?></td>
                            <td><? echo $row["REMARKS"];?></td>	
                            <td align="right"><? echo $row["CONS_QUANTITY"];?></td>			
		                </tr>
		                <?
						$i++;
					}
					?>
	            </tbody>
	        </table>
	    	<? 
	    }
	    ?>    
	    <br>
    	<?  echo signature_table(12, $data[0], "1000px", '', '', $issue_by); ?>
	</div>
    <script type="text/javascript" src="../../../js/jquery.js"></script>
	<?
	exit();
}

if ($action=="general_item_issue_print_5")
{
    extract($_REQUEST);
	$data=explode('*',$data);
	$company=$data[0];
	$location=$data[4];
	$system_no = $data[2];

	$company_library=return_library_array( "SELECT id, company_name from lib_company", "id", "company_name"  );
	$country_arr = return_library_array( "SELECT id, country_name from lib_country", "id", "country_name"  );
	$buyer_lib=return_library_array( "SELECT id, buyer_name from  lib_buyer", "id", "buyer_name"  );
	$supplier_lib=return_library_array( "SELECT id, supplier_name from  lib_supplier", "id", "supplier_name"  );
	$store_library=return_library_array( "SELECT id, store_name from  lib_store_location", "id", "store_name"  );
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
	$location_arr=return_library_array("SELECT id,location_name from lib_location",'id','location_name');
	$color_lib=return_library_array( "select id,color_name from lib_color", "id", "color_name");

	$sql="SELECT a.id as ID, a.issue_number as ISSUE_NUMBER, a.issue_date as ISSUE_DATE, a.issue_basis as ISSUE_BASIS, a.challan_no as CHALLAN_NO,a.issue_purpose as ISSUE_PURPOSE,a.req_no as REQ_NO, b.store_id as STORE_ID, a.knit_dye_source as KNIT_DYE_SOURCE, a.knit_dye_company as KNIT_DYE_COMPANY, a.knit_dye_location as KNIT_DYE_LOCATION, a.location_id as LOCATION_ID, a.req_no as REQ_NO, a.loan_party as LOAN_PARTY, a.remarks 
	from inv_issue_master a,inv_transaction b
	where a.id='$data[1]' and b.transaction_type=2 and a.id=b.mst_id and b.status_active=1 ";

	// echo $sql;
	$dataArray=sql_select($sql);

	$location='';
	if($dataArray[0]['KNIT_DYE_SOURCE']==1)
	{
		$caption="Location";
		$issueTo=$company_library[$dataArray[0]['KNIT_DYE_COMPANY']];
		$location=$location_arr[$dataArray[0]['KNIT_DYE_LOCATION']];
	}
	else
	{
		$caption="Address";
		$supplierData=sql_select("SELECT address_1 as ADDRESS_1, address_2 as ADDRESS_2, supplier_name as SUPPLIER_NAME from lib_supplier where id='".$dataArray[0]['KNIT_DYE_COMPANY']."'");
		$issueTo=$supplierData[0]['SUPPLIER_NAME'];
		$location=$supplierData[0]['ADDRESS_1'];
		if($location=="") $location=$supplierData[0]['ADDRESS_2']; else $location.=", ".$supplierData[0]['ADDRESS_2'];
	}

	$com_dtls = fnc_company_location_address($company, $location, 2);
	?>
	<style>
		.wrd_brk{word-break: break-all;}
	</style>
	<div style="width:1000px;">
    <table width="1000" cellspacing="0" border="0">
        <tr>
            <td colspan="2" rowspan="3" width="200">
			<img src="../../../<?=$com_dtls[2]; ?>" height="60" width="200" style="float:left;">
			</td>
			<td colspan="4"  style="font-size:xx-large;justify-content: center;text-align: center;" >
            	<strong style="justify-content: center;text-align: center;"><?=$com_dtls[0]; ?></strong>
            </td>
			<td colspan="2" rowspan="2" align="right" width="150"></td>           
        </tr>
        <tr class="form_caption">
        	<td colspan="4"  style="font-size:14px;justify-content: center;text-align: center;">
				<?=$com_dtls[1];?>
            </td>
        </tr>
        <tr>
            <td colspan="4"  style="font-size:x-large;justify-content: center;text-align: center;"><strong style="justify-content: center;text-align: center;">General Item Issue challan</strong></td>
        </tr>
        
    </table>
    <br>

	<table cellspacing="0" width="1000" align="center" border="1" rules="all" class="">
		<tr>
			<td width="90"><strong>System ID: </strong></td> 
			<td width="175px"><?=$dataArray[0]['ISSUE_NUMBER']; ?></td>
			<td width="90"><strong>Issue Purpose: </strong></td> 
			<td width="175px"><?=$general_issue_purpose[$dataArray[0]['ISSUE_PURPOSE']]; ?></td>			
			<td width="95"><strong>Loan Party:</strong></td>
			<td width="175px"><?=$supplier_lib[$dataArray[0]['LOAN_PARTY']]; ?></td>
		</tr>
		<tr>
			<td ><strong>Issue Date:</strong></td>
			<td ><?=change_date_format($dataArray[0]['ISSUE_DATE']);?></td>
			<td ><strong>Issue Req. No:</strong></td> 
			<td ><?=$dataArray[0]['REQ_NO'] ?></td>
			<td ><strong>Challan No:</strong></td> 
			<td ><?=$dataArray[0]['CHALLAN_NO'] ?></td>
			
		</tr>
		<tr>
			<td ><strong>Issue To:</strong></td> 
			<td ><?=$issueTo; ?></td>
			<td ><strong>Issue Location:</strong></td> 
			<td><?=chop($location,', '); ?></td>
			<td><strong>Store Name :</strong></td>
			<td ><?=$store_library[$dataArray[0]['STORE_ID']]; ?></td>
		</tr>
		<tr>
			<td ><strong>Remarks:</strong></td> 
			<td colspan="5"><?=$dataArray[0]['REMARKS']; ?></td>
		</tr>
	</table>
	<br>
   
	<div style="width:100%;">
    <table cellspacing="0" width="1000"  border="1" rules="all" class="rpt_table" >
        <thead bgcolor="#dddddd" style="font-size:13px">
			<tr>
				<th width="30">SL</th>
				<th width="80">ITEM CATEGORY</th>
				<th width="80">ITEM GROUP</th>
				<th width="100">ITEM DESCRIPTION</th>
				<th width="60">ITEM SIZE</th>
				<th width="60">BUYER NAME</th>
				<th width="60">STYLE REF.</th>
				<th width="60">BUYER ORDER</th>
				<th width="60">COLOR</th>
				<th width="40">UOM</th>
				<th width="40">ISSUE QTY</th>
				<th width="40">PREV. ISSUE QTY</th>
				<th width="40">ORDER QTY</th>
				<th width="40">STOCK BALANCE</th>
				<th >REMARKS</th>
			</tr>
        </thead>
		<tbody style="font-size:13px">
			<?

				$cond="";
				if($data[1]!="") $cond .= " and a.id='$data[1]'";
				
				$group_arr=return_library_array( "SELECT id,item_name from lib_item_group where item_category not in (1,2,3,5,6,7,12,13,14) and status_active=1 and is_deleted=0",'id','item_name');

				$sql = "SELECT a.issue_number, b.id, b.cons_uom, b.cons_quantity, b.prod_id, b.order_id, b.remarks, c.item_category_id, c.item_description, c.item_group_id, c.item_size, c.item_color, d.quantity, e.id as po_id, e.po_number, e.po_quantity, f.style_ref_no, f.buyer_name, f.id as job_id 
				from inv_issue_master a, inv_transaction b, product_details_master c, order_wise_general_details d, wo_po_break_down e, wo_po_details_master f 
				where a.id=b.mst_id and b.prod_id=c.id and b.id=d.trans_id and d.po_breakdown_id=e.id and c.id=d.prod_id and e.job_id=f.id and b.transaction_type=2 and a.entry_form=21 and b.status_active=1 and c.status_active in(1,3) and d.status_active=1 and e.status_active=1 and f.status_active=1 $cond";
				// echo $sql;
				$sql_result = sql_select($sql);
				foreach($sql_result as $row)
				{
					$all_data_arr[$row["JOB_ID"]][]=$row;
					$trans_id.=$row["ID"].",";
					$po_id.=$row["PO_ID"].",";
					$prod_id.=$row["PROD_ID"].",";
				}
				$trans_id=rtrim($trans_id,",");
				$po_id=rtrim($po_id,",");
				$prod_id=rtrim($prod_id,",");
				$prv_iss_sql = "SELECT po_breakdown_id, prod_id, sum(quantity) as quantity
				from  order_wise_general_details 
				where trans_type=2 and entry_form=21 and trans_id not in ($trans_id) and po_breakdown_id in($po_id) and prod_id in ($prod_id) and status_active=1 
				group by po_breakdown_id, prod_id";
				// echo $prv_iss_sql;
				$prv_iss_result = sql_select($prv_iss_sql);
				foreach($prv_iss_result as $row)
				{
					$prv_iss_arr[$row["PO_BREAKDOWN_ID"]][$row["PROD_ID"]]=$row["QUANTITY"];
				}
				$i=1;
				foreach($all_data_arr as $job_id=>$job_data_arr)
				{
					$tot_cons_quantity=$tot_po_quantity=$tot_prv_cons_quantity=0;
					foreach($job_data_arr as $row )
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<?=$bgcolor; ?>">
							<td class="wrd_brk" align="center"><?=$i; ?></td>
							<td class="wrd_brk" align="center" ><?=$item_category[$row[csf("item_category_id")]]; ?></td>
							<td class="wrd_brk" align="center" ><?=$group_arr[$row[csf("item_group_id")]]; ?></td>
							<td class="wrd_brk" ><?=$row[csf("item_description")]; ?></td>
							<td class="wrd_brk" align="center" ><?=$row[csf("item_size")]; ?></td>
							<td class="wrd_brk" ><?=$buyer_lib[$row[csf("buyer_name")]]; ?></td>
							<td class="wrd_brk" align="center" ><?=$row[csf("style_ref_no")]; ?></td>
							<td class="wrd_brk" align="center" ><?=$row[csf("po_number")]; ?></td>
							<td class="wrd_brk" align="center" ><?=$color_lib[$row[csf("item_color")]]; ?></td>
							<td class="wrd_brk" align="center" ><?=$unit_of_measurement[$row[csf("cons_uom")]]; ?></td>
							<td class="wrd_brk" align="right" ><?=number_format($row[csf("quantity")],2); ?></td>
							<td class="wrd_brk" align="right" ><?=number_format($prv_iss_arr[$row["PO_ID"]][$row["PROD_ID"]],2); ?></td>
							<td class="wrd_brk" align="right" ><?=number_format($row[csf("po_quantity")],2); ?></td>
							<td class="wrd_brk" align="right" ><?=number_format($row[csf("po_quantity")]-$row[csf("quantity")]-$prv_iss_arr[$row["PO_ID"]][$row["PROD_ID"]],2); ?></td>
							<td class="wrd_brk" ><?=$row[csf("remarks")]; ?></td>
						</tr>
						<? 
						$tot_po_quantity+= $row[csf('po_quantity')];
						$tot_cons_quantity += $row[csf('quantity')];
						$tot_prv_cons_quantity += $prv_iss_arr[$row["PO_ID"]][$row["PROD_ID"]];
						$grand_po_quantity += $row[csf('po_quantity')];
						$grand_cons_quantity += $row[csf('quantity')];
						$grand_prv_cons_quantity += $prv_iss_arr[$row["PO_ID"]][$row["PROD_ID"]];
						$i++;
					}
					?>
						<tr>
							<td align="right" colspan="10">Style Sub Total </td>
							<td align="right"><?=number_format($tot_cons_quantity,2);?></td>
							<td align="right"><?=number_format($tot_prv_cons_quantity,2);?></td>
							<td align="right"><?=number_format($tot_po_quantity,2);?></td>
							<td align="right"><?=number_format($tot_po_quantity-$tot_cons_quantity-$tot_prv_cons_quantity,2);?></td>
							<td></td>
						</tr>
					<?
				} 
			?>
		</tbody>
		<tfoot>
			<tr>
				<th align="right" colspan="10">Grand Total </th>
				<th><?=number_format($grand_cons_quantity,2);?></th>
				<th><?=number_format($grand_prv_cons_quantity,2);?></th>
				<th><?=number_format($grand_po_quantity,2);?></th>
				<th><?=number_format($grand_po_quantity-$grand_cons_quantity-$grand_prv_cons_quantity,2);?></th>
				<th></th>
			</tr>
		</tfoot>
    </table>
	</div>
	<br>

	<div> <?=signature_table(12, $data[0], "1000px"); ?> </div>
    <script type="text/javascript" src="../../../js/jquery.js"></script>
	<?
	exit();
}

if ($action=="item_issue_requisition_popup_search")
{
    echo load_html_head_contents("Item Issue Requisition search From", "../../../", 1, 1,'','1','');
    extract($_REQUEST);

	?>
	<script>

        function hidden_item_value(id)
        {
           // alert ($("#hidden_approval_necessity_setup").val());
            $('#hidden_item_issue_id').val(id);
			var ref = id.split("_");
			
			if (ref[8]==1 && ref[6] !=1)
            {
                alert("Please Approve Requisition First...");
                return;
            }
            else if (ref[9]==1 && ref[6] !=1 && ref[6] !=3)
            {
                alert("Please Approve Requisition First...");
                return;
            }

			parent.emailwindow.hide();
			
        }

        function item_issue_requisition_popup()
        {

        	if (form_validation('cbo_company_id','Company')==false)
			{
				alert('Pls, Select Company.');
				return;
			}
            show_list_view ( document.getElementById('cbo_company_id').value + '**' + document.getElementById('txt_date_from').value  + '**' + document.getElementById('txt_date_to').value + '**' + document.getElementById('txt_required_date').value + '**' + document.getElementById('cbo_location_name').value + '**' + document.getElementById('cbo_division_name').value + '**' + document.getElementById('cbo_department_name').value + '**' + document.getElementById('cbo_section_name').value + '**' + document.getElementById('cbo_sub_section_name').value + '**' + document.getElementById('cbo_delivery_point').value + '**' + document.getElementById('txt_system_id').value+ '**' + document.getElementById('cbo_year').value, 'items_search_list_view', 'search_div', 'general_item_issue_controller', 'setFilterGrid(\'list_view\',-1)');
            // show_list_view ( document.getElementById('cbo_company_id').value+'**'+document.getElementById('txt_indent_date').value+'**'+document.getElementById('txt_required_date').value+'**'+document.getElementById('txt_remarks').value+'**'+document.getElementById('txt_manual_requisition_no').value+'**'+document.getElementById('cbo_location_name').value+'**'+document.getElementById('cbo_division_name').value+'**'+document.getElementById('cbo_department_name').value+'**'+document.getElementById('cbo_section_name').value+'**'+document.getElementById('cbo_sub_section_name').value+'**'+document.getElementById('cbo_delivery_point').value+'**'+document.getElementById('txt_system_id').value, 'items_search_list_view', 'search_div', 'general_item_issue_controller', 'setFilterGrid(\'list_view\',-1)');
        }
        function fnc_sub_section()
         {
            $('#cbo_sub_section_name').css('display','none');
         }
    </script>
	</head>
	<body>
	    <div align="center" style="width:1230px;">
	        <form name="searchitemreqfrm" id="searchitemreqfrm">
	            <fieldset style="width:1230px; margin-left:3px">
	            <legend>Search</legend>
	                <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
	                    <thead>
	                        <th class="must_entry_caption">Company</th>
                            <th>Indent No.</th>
                            <th>Requisition Year</th>
                            <th width="160">Indent Date Range</th>
                            <th align="right">Required Date</th>
                            <th align="right">Location</th>
                            <th align="right">Division</th>
                            <th align="right">Department</th>
                            <th align="right">Section</th>
                            <th align="right">Sub Section</th>
                            <th align="right">Delivery Point</th>
	                        <th><input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" /><input type="hidden" name="id_field" id="id_field" value="" />		</th>
	                    </thead>
	                    <tbody>
	                    <tr>
	                    	<td>
								<?
									$company="select comp.id,comp.company_name from lib_company comp where  comp.status_active=1 and comp.is_deleted=0  order by company_name";
									echo create_drop_down("cbo_company_id",100,$company,"id,company_name",1,"--select--",$cbo_company_id,"load_drop_down( 'general_item_issue_controller', this.value, 'load_drop_down_location_popup','location_td_popup');load_drop_down( 'general_item_issue_controller', this.value, 'load_drop_down_division_popup','division_td_popup');",1);
								?>
	                  		</td>
                            <td><input type="text" name="txt_system_id" id="txt_system_id" class="text_boxes" style="width:70px" ></td>
                            <td>
								<?php
									echo create_drop_down( "cbo_year", 90,$year,"", 1, "-- Select --",date("Y"),"");
								?>
			                </td>
                      		<td>
								  <!-- <input type="text" name="txt_indent_date" id="txt_indent_date" class="datepicker" style="width:70px" > -->
								<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px"> To
								<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px">
							</td>
                      		<!-- <td><input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:70px" ></td>
							<td><input type="text" name="txt_manual_requisition_no" id="txt_manual_requisition_no" class="text_boxes" style="width:70px"></td> -->
                            <td><input type="text" name="txt_required_date" id="txt_required_date" class="datepicker" style="width:70px" readonly></td>
                            <td id="location_td_popup">
								<?php
									echo create_drop_down( "cbo_location_name", 90,$blank_array,"id,location_name", 1, "-- Select --",0,"");
								?>
			                </td>
				            <td  id="division_td_popup">
							   <?php
									echo create_drop_down( "cbo_division_name", 90,$blank_array,"", 1, "-- Select --" );
				               ?>
				            </td>
                            <td width="70" id="department_td_popup">
								<?php
                       				 echo create_drop_down( "cbo_department_name", 90,$blank_array,"", 1, "-- Select --" );
                   				?>
				            </td>
                             <td id="section_td_popup">
                             	<?
									echo create_drop_down( "cbo_section_name", 90,$blank_array,"", 1, "-- Select --",'' );
								?>
				            </td>
                            <td  id="sub_section_td_popup">
								<?php
									echo create_drop_down( "cbo_sub_section_name", 90,$blank_array,"", 1, "-- Select --" );
	                			?>
				            </td>
                            <td><input type="text" name="cbo_delivery_point" id="cbo_delivery_point" style="width:80px" class="text_boxes"></td>
	                		<td>
	                			<input type="hidden" id="hidden_item_issue_id" />
                            	<input type="hidden" id="hidden_item_cost_center" />
                            	<input type="hidden" id="hidden_itemissue_req_sys_id" />
                            	<input type="button" id="search_button" class="formbutton" value="Show" onClick="item_issue_requisition_popup()" style="width:100px;" />
	                  		</td>
	                    </tr>
	                    </tbody>
						<tfoot>
							<tr>
								<td align="center" height="25" valign="middle" colspan="12" style="background-image: -moz-linear-gradient(bottom, rgb(136,170,283) 7%, rgb(194,220,255) 10%, rgb(136,170,283) 96%);">
								<? echo load_month_buttons(1);  ?>
								</td>
							</tr>
						</tfoot> 
	                </table>
	               <div style="width:100%; margin-top:10px;" id="search_div" align="center"></div>
	            </fieldset>
	        </form>
	    </div>
	</body>
    <script>
    set_all_onclick();
    var cbo_company_id=$("#cbo_company_id").val();
    load_drop_down( 'general_item_issue_controller', cbo_company_id, 'load_drop_down_location_popup','location_td_popup');
    load_drop_down( 'general_item_issue_controller', cbo_company_id, 'load_drop_down_division_popup','division_td_popup');
    </script>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	    <?
	exit();

}


if($action=="items_search_list_view")
{
	$data=explode('**',$data);
	// $remarks_no=$data[3];
	// $requisition_no=$data[4];
	// $delivery=$data[10];
	// $indent_no=trim($data[11]);
	//var_dump($data);die;
	// if($data[0]!=0){ $company_id=" and company_id = $data[0]";}else{ echo "Select Company"; die;}
	// if($data[3]!=''){ $remarks=" and remarks like '$remarks_no%'";}else{ echo "";}
	// if($data[4]!=''){ $manual_requisition_no=" and manual_requisition_no like '$requisition_no%'";}else{ echo "";}
	// if($data[5]!=0){ $location_id=" and location_id = $data[5]";}else{ echo "";}
	// if($data[6]!=0){ $division_id=" and division_id = $data[6]";}else{ echo "";}
	// if($data[7]!=0){ $department_id=" and department_id = $data[7]";}else{ echo "";}
	// if($data[8]!=0){ $section_id=" and section_id = $data[8]";}else{ echo "";}
	// if($data[9]!=0){ $sub_section_id=" and sub_section_id = $data[9]";}else{ echo "";}
	// if($data[10]!=''){ $delivery_id=" and delivery_point like '$delivery%'";}else{ echo "";}
	// if($data[11]!=''){ $ind_id=" and itemissue_req_sys_id like '%$indent_no'";}else{ echo "";}
	$delivery = $data[9];
	$indent_no = trim($data[10]);
	//var_dump($data);die;
	if ($data[0] != 0) 
	{
		$company_id = " and a.company_id = $data[0]";
	} 
	else 
	{
		echo "Select Company";
		die;
	}
	
	$location_id=$division_id =$department_id =$section_id =$sub_section_id =$delivery_id =$ind_id ="";
	if ($data[4] != 0) $location_id = " and a.location_id = $data[4]"; 
	if ($data[5] != 0) $division_id = " and a.division_id = $data[5]";
	if ($data[6] != 0) $department_id = " and a.department_id = $data[6]";
	if ($data[7] != 0) $section_id = " and a.section_id = $data[7]";
	if ($data[8] != 0) $sub_section_id = " and a.sub_section_id = $data[8]";
	if ($data[9] != '') $delivery_id = " and a.delivery_point like '$delivery%'";
	if ($data[10] != '') $ind_id = " and a.itemissue_req_sys_id like '%$indent_no'";
	
	//echo $data[11].tss;die;
	
	
	//$date=change_date_format($data[1],'mm-dd-yyyy');
	//if($data[1]!=0){ $indent_date=" and indent_date = $data[1]";}else{ $indent_date=""; }
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$section_library = return_library_array("select id, section_name from lib_section", "id", "section_name");
	$department = return_library_array("select id, department_name from lib_department", "id", "department_name");
	$location = return_library_array("select id, location_name from lib_location", "id", "location_name");
	$division = return_library_array("select id, division_name from lib_division", "id", "division_name");
	$section_library = return_library_array("select id, section_name from lib_section", "id", "section_name");
	$user_library = return_library_array("select id, user_name from user_passwd", "id", "user_name");

	// $date = $data[1];
	$re_date = $data[3];
	$indent_date = $require_date = "";
	if ($data[1] != "" && $data[2] != "")
	{
		if ($db_type == 0) 
		{
			$indent_date = "and a.indent_date between '" . change_date_format($data[1], 'yyyy-mm-dd') . "' and '" . change_date_format($data[2], 'yyyy-mm-dd') . "'";
		} 
		else if ($db_type == 2) 
		{
			$indent_date = "and a.indent_date between '" . change_date_format($data[1], '', '', 1) . "' and '" . change_date_format($data[2], '', '', 1) . "'";
		}
	}

	if ($data[3] != "") 
	{
		if ($db_type == 0) 
		{
			$require_date = "and a.required_date ='" . change_date_format($re_date, 'yyyy-mm-dd') . "'";
		} 
		else if ($db_type == 2) 
		{
			$require_date = "and a.required_date ='" . change_date_format($re_date, '', '', 1) . "'";
		}
	}
	
	if($data[11]) $require_date .=" and to_char(a.insert_date,'YYYY')='$data[11]'";

	if ($db_type == 0) $year_field = "YEAR(a.insert_date) as year";
	else if ($db_type == 2) $year_field = "to_char(a.insert_date,'YYYY') as year";
	else $year_field = "";

	$approval_need=$allow_partial="";
	$sql_app_res=sql_select("select approval_need, allow_partial from approval_setup_mst a, approval_setup_dtls b where a.id=b.mst_id and a.company_id=$data[0] and b.page_id=56 and a.status_active=1 and b.status_active=1 order by a.setup_date desc fetch first 1 rows only");
	foreach ($sql_app_res as $row)
	{
		$approval_need=$row[csf('approval_need')];
		$allow_partial=$row[csf('allow_partial')];
	}

	$sql="SELECT a.ID, a.ITEMISSUE_REQ_SYS_ID, a.COMPANY_ID, a.INDENT_DATE, a.REQUIRED_DATE, a.LOCATION_ID, a.DIVISION_ID, a.DEPARTMENT_ID, a.SECTION_ID, a.SUB_SECTION_ID, a.DELIVERY_POINT, a.REMARKS, a.MANUAL_REQUISITION_NO, a.IS_APPROVED, a.STORE_ID, a.INSERTED_BY, sum(b.REQ_QTY) as REQ_QTY 
	from inv_item_issue_requisition_mst a, inv_itemissue_requisition_dtls b 
	where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_id $indent_date $require_date $location_id $division_id $department_id $section_id $sub_section_id $delivery_id $ind_id
	group by a.id, a.itemissue_req_sys_id, a.company_id, a.indent_date, a.required_date, a.location_id, a.division_id, a.department_id, a.section_id, a.sub_section_id, a.delivery_point, a.remarks, a.manual_requisition_no, a.is_approved, a.store_id, a.inserted_by 
	order by itemissue_req_sys_id desc";
	
	//echo $sql; //die;
	
	$nameArray=sql_select($sql);
	
	$issue_sql=" select a.REQ_ID, sum(b.CONS_QUANTITY) as CONS_QUANTITY from INV_ISSUE_MASTER a, INV_TRANSACTION b 
	where a.id=b.mst_id and a.entry_form=21 and a.status_active=1 and b.status_active=1 and a.REQ_ID>0 and b.TRANSACTION_TYPE=2 group by a.REQ_ID";
	$issue_sql_result=sql_select($issue_sql);
	$issue_data=array();
	foreach($issue_sql_result as $row)
	{
		$issue_data[$row["REQ_ID"]]=$row["CONS_QUANTITY"];
	}
	
	//$arr=array (0=>$company_arr,4=>$location,5=>$division,6=>$department,7=>$section_library,8=>$user_library,9=>$is_approved);
	//echo  create_list_view("list_view", "Company,Indent No.,Indent date,Required Date,Location,Division,Department,Section,Req Insert User, Approval Status,Delivery Point", "150,100,80,100,100,80,80,80,100,100","1130","320",0, $sql, "hidden_item_value", "id,itemissue_req_sys_id,indent_date,location_id,department_id,section_id,is_approved,store_id", "", 1, "company_id,0,0,0,location_id,division_id,department_id,section_id,inserted_by,is_approved", $arr , "company_id,itemissue_req_sys_id,indent_date,required_date,location_id,division_id,department_id,section_id,inserted_by,is_approved,delivery_point", "",'','0,0,3,3,0,0,0');


	?>
 	<div style="width:1130px;">
	    <table width="1130" cellspacing="0" cellpadding="0" border="0" rules="all" class="rpt_table" align="left">
	    	<thead>
				<tr>
					<th width="30">SL</th>
					<th width="150">Company</th>
                    <th width="100">Indent No.</th>
					<th width="80">Indent date.</th>
                    <th width="100">Required date</th>
                    <th width="100">Location</th>
                    <th width="80">Division</th>
                    <th width="80">Department</th>
					<th width="80">Section</th>
                    <th width="100">Req Insert User</th>
                    <th width="100">Approval Status</th>
                    <th>Delivery Point</th>
				</tr>
			</thead>
	    </table>
		<div id="" style="max-height:363px; width:1130px; overflow-y:scroll" >
		    <table width="1110" cellspacing="0" cellpadding="0" border="0" rules="all" id="list_view" class="rpt_table" align="left">
				<tbody>
		        <?
				$item_group=return_library_array("select id,item_name from lib_item_group",'id','item_name');
		        $i=1;
				foreach ($nameArray as $selectResult)
			   	{
					$issue_bal=$selectResult['REQ_QTY']-$issue_data[$selectResult['ID']];
					if($issue_bal>0)
					{
						$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="hidden_item_value('<? echo $selectResult['ID'];?>_<? echo $selectResult['ITEMISSUE_REQ_SYS_ID'];?>_<? echo $selectResult['INDENT_DATE'];?>_<? echo $selectResult['LOCATION_ID'];?>_<? echo $selectResult['DEPARTMENT_ID'];?>_<? echo $selectResult['SECTION_ID'];?>_<? echo $selectResult['IS_APPROVED'];?>_<? echo $selectResult['STORE_ID'];?>_<? echo $approval_need;?>_<? echo $allow_partial;?>_<? echo $selectResult['DIVISION_ID'];?>_<? echo $selectResult['COMPANY_ID'];?>')" >
							<td width="30"><? echo $i; ?></td>
							<td width="150"><? echo $company_arr[$selectResult["COMPANY_ID"]];?></td>
							<td width="100" align="center"><? echo $selectResult["ITEMISSUE_REQ_SYS_ID"];?></td>
							<td width="80" align="center"><? echo change_date_format($selectResult["INDENT_DATE"]);?></td>
							<td width="100" align="center"><? echo change_date_format($selectResult["REQUIRED_DATE"]);?></td>
							<td width="100"><? echo $location[$selectResult["LOCATION_ID"]];?></td>
							<td width="80"><? echo $division[$selectResult["DIVISION_ID"]];?></td>
							<td width="80"><? echo $department[$selectResult["DEPARTMENT_ID"]];?></td>
							<td width="80"><? echo $section_library[$selectResult["SECTION_ID"]];?></td>
							<td width="100"><? echo $user_library[$selectResult["INSERTED_BY"]];?></td>
							<td width="100"><? if ($selectResult["IS_APPROVED"]==1 || $selectResult["IS_APPROVED"]==3) echo "Yes"; else echo "No"; ?></td>
							<td><? echo $selectResult["DELIVERY_POINT"];?></td>
						</tr>
						<? 
						$i++;
					}
				}
				?>
		        </tbody>
			</table>
		</div>
    </div>
	<?
	exit();

}


if($action=="check_reqn_no")
{
	//echo $data;

	$sql = sql_select("select id,company_id from inv_item_issue_requisition_mst where status_active=1 and is_deleted=0 and itemissue_req_sys_id='$data' ");
    if(count($sql)>0) echo $sql[0][csf('company_id')]."**".$sql[0][csf('id')];
	else{ echo 0; }
	exit();
}

if ($action=="show_item_issue_listview")
{
	$data=explode('_',$data);
	$req_cond="";
	if(is_numeric($data[0]))
	{
		$req_cond=" and b.mst_id='$data[0]'";
	}
	else
	{
		$req_cond=" and a.itemissue_req_sys_id='$data[0]'";
	}

	/*$sql="select a.id as RID, a.itemissue_req_sys_id as ITEMISSUE_REQ_SYS_ID, a.division_id as DIVISION_ID, a.department_id as DEPARTMENT_ID, b.mst_id as MST_ID, b.req_qty as REQ_QTY, b.item_group as ITEM_GROUP, b.item_description as ITEM_DESCRIPTION, b.product_id as PRODUCT_ID
	from inv_item_issue_requisition_mst a, inv_itemissue_requisition_dtls b 
	where b.mst_id=a.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $req_cond";*/

	$sql="SELECT a.id as RID, b.id, a.itemissue_req_sys_id as ITEMISSUE_REQ_SYS_ID, a.division_id as DIVISION_ID, a.department_id as DEPARTMENT_ID, b.mst_id as MST_ID, b.req_qty as REQ_QTY, b.item_group as ITEM_GROUP, b.item_description as ITEM_DESCRIPTION, b.product_id as PRODUCT_ID, sum(d.cons_quantity) as ISSUE_QNTY, b.remarks as DTLS_REMARKS
	from inv_item_issue_requisition_mst a, inv_itemissue_requisition_dtls b 
	left join inv_issue_master c on b.mst_id=c.req_id and c.entry_form=21 and c.is_deleted=0 and c.status_active=1
	left join inv_transaction d on c.id=d.mst_id and d.prod_id=b.product_id and d.transaction_type=2 and d.is_deleted=0 and d.status_active=1
	where b.mst_id=a.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $req_cond 
	group by a.id, b.id, a.itemissue_req_sys_id, a.division_id, a.department_id, b.mst_id, b.req_qty, b.item_group, b.item_description, b.product_id, b.remarks order by id Asc ";
	// echo $sql;
	$nameArray=sql_select( $sql );
	$prod_id_arr=array();
	foreach($nameArray as $row)
	{
		if($row['PRODUCT_ID']){
			$prod_id_arr[$row['PRODUCT_ID']] = $row['PRODUCT_ID'];
		}
	}

	$store_cond="";
	if($data[2]) $store_cond=" and store_id=$data[2]";
	
	$con = connect();
	execute_query("delete from gbl_temp_engine where user_id=$user_id and entry_form=58 and ref_from in(2)");
    oci_commit($con);

	$stock_arr=array();
	$prod_info_arr=array();
	if (count($prod_id_arr)>0){
		fnc_tempengine("gbl_temp_engine", $user_id, 58, 2, $prod_id_arr, $empty_arr);
		$sql_trans_res=sql_select("SELECT a.PROD_ID, sum((case when a.transaction_type in(1,4,5) then a.cons_quantity else 0 end)-(case when a.transaction_type in(2,3,6) then a.cons_quantity else 0 end)) as BALANCE_QNTY from gbl_temp_engine g, inv_transaction a where g.ref_val=a.prod_id and a.status_active=1 and a.is_deleted=0 $store_cond and g.user_id=$user_id and g.entry_form=58 and g.ref_from=2 group by a.PROD_ID");
		foreach($sql_trans_res as $row)
		{
			$stock_arr[$row["PROD_ID"]]=$row["BALANCE_QNTY"];
		}
		unset($sql_trans_res);

		$sql_prod_res=sql_select("SELECT a.ID, a.ITEM_CATEGORY_ID, a.ITEM_CODE from gbl_temp_engine g, product_details_master a where g.ref_val=a.id and a.status_active=1 and a.is_deleted=0 and g.user_id=$user_id and g.entry_form=58 and g.ref_from=2");
		foreach($sql_prod_res as $row)
		{
			$prod_info_arr[$row["ID"]]['item_code']=$row["ITEM_CODE"];
			$prod_info_arr[$row["ID"]]['item_category_id']=$row["ITEM_CATEGORY_ID"];	
		}
		unset($sql_prod_res);
	}	

	execute_query("delete from gbl_temp_engine where user_id=$user_id and entry_form=58 and ref_from in(2)");
    oci_commit($con);disconnect($con);
	
	?>
 	<div style="width:545px;">
	    <table width="525" cellspacing="0" cellpadding="0" border="0" rules="all" class="rpt_table" align="left">
	    	<thead>
				<tr>
					<th width="30">SL</th>
					<th width="80">Item Group</th>
					<th width="95">Item Description</th>
                    <th width="60">Item Code</th>
                    <th width="65">Req. Qty.</th>
                    <th width="65">Stock Qty</th>
                    <th width="65">Issue Qty</th>
                    <th title="(Req. Qty - Issue Qty)">Issue Balance Qty.</th>
				</tr>
			</thead>
	    </table>
		<div id="" style="max-height:363px; width:543px; overflow-y:scroll" >
		    <table width="525" cellspacing="0" cellpadding="0" border="0" rules="all"  class="rpt_table" align="left">
				<tbody>
		        <?
				$item_group=return_library_array("select id,item_name from lib_item_group",'id','item_name');
		        $i=1;
				foreach ($nameArray as $selectResult)
			   	{
					$issue_bal=$selectResult["REQ_QTY"]-$selectResult["ISSUE_QNTY"];
					if($issue_bal>0)
					{
						$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
						/*get_php_form_data('<? echo $selectResult['PRODUCT_ID'];?>+**+<? echo $selectResult['REQ_QTY'];?>+**+<? echo $selectResult['RID'];?>+**+<? echo $selectResult['DIVISION_ID'];?>+**+<? echo $selectResult['DEPARTMENT_ID'];?>+**+<? echo $data[1];?>+**+<? echo $data[2];?>+**+<? echo $selectResult['DTLS_REMARKS'];?>','populate_item_details_form_data_dtls','requires/general_item_issue_controller');change_color('search<? echo $i; ?>','<? echo $bgcolor; ?>') $prod_category_arr[$val["ID"]]*/
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="set_requisition_data('<? echo $selectResult['PRODUCT_ID'].'**'.$selectResult['REQ_QTY'].'**'.$selectResult['RID'].'**'.$selectResult['DIVISION_ID'].'**'.$selectResult['DEPARTMENT_ID'].'**'.$data[1].'**'.$data[2].'**'.$selectResult['DTLS_REMARKS'].'**'.$prod_info_arr[$selectResult['PRODUCT_ID']]['item_category_id'];?>');change_color('search<? echo $i; ?>','<? echo $bgcolor; ?>')" >
							<td width="30"><? echo $i; ?></td>
							<td width="80" style="word-break:break-all"><? echo $item_group[$selectResult["ITEM_GROUP"]];?></td>
							<td width="95" style="word-break:break-all"><? echo $selectResult["ITEM_DESCRIPTION"];?></td>
							<td width="60" style="word-break:break-all"><? echo $prod_info_arr[$selectResult["PRODUCT_ID"]]['item_code'];?></td>
							<td width="65" align="right"><? echo number_format($selectResult["REQ_QTY"],2);?></td>
							<td width="65" align="right"><? echo number_format($stock_arr[$selectResult['PRODUCT_ID']],2);?></td>
							<td width="65" align="right"><? echo number_format($selectResult["ISSUE_QNTY"],2);?></td>
							<td align="right"><? echo number_format($issue_bal,2);?></td>
						</tr>
						<? 
						$i++;
					}
				}
				?>
		        </tbody>
			</table>
		</div>
    </div>
	<?
}

if($action=="populate_item_details_form_data_dtls")
{
	$ex_data = explode("**",$data);
	//echo $ex_data[3]."=".$ex_data[4];die;

	$qnty=sql_select("select sum(a.cons_quantity) as Q from inv_transaction a , inv_issue_master b where a.prod_id=$ex_data[0] and a.mst_id=b.id and b.req_id=$ex_data[2] and a.transaction_type=2 and a.status_active=1");
	$store_item_stock = return_library_array("select a.id, sum((case when p.transaction_type in(1,4,5) then p.cons_quantity else 0 end)-(case when p.transaction_type in(2,3,6) then p.cons_quantity else 0 end)) as bal_qnty
	from inv_transaction p, product_details_master a
	where p.prod_id=a.id and a.id=$ex_data[0] and p.store_id=$ex_data[6] and a.status_active in(1,3) and p.status_active=1 and p.is_deleted=0
	group by  a.id", 'id', 'bal_qnty');


	$total_qnty=$qnty[0]['Q'];

	$data_ar=sql_select("select id,item_group_id,sub_group_code,item_description,unit_of_measure,current_stock,item_category_id,item_size from product_details_master where id='$ex_data[0]'");
	foreach ($data_ar as $info)
	{
		$stock_qty=number_format($store_item_stock[$info[csf("id")]],2,'.','');
		if($stock_qty=='')$stock_qty=0; else $stock_qty=$stock_qty;
		echo "document.getElementById('cbo_item_group').value 			= '".$info[csf("item_group_id")]."';\n";
		echo "document.getElementById('cbo_item_category').value 		= '".$info[csf("item_category_id")]."';\n";
		echo "document.getElementById('txt_item_desc').value 			= '".$info[csf("item_description")].",".$info[csf("item_size")]."';\n";
		echo "document.getElementById('cbo_uom').value 					= '".$info[csf("unit_of_measure")]."';\n";
		echo "document.getElementById('txt_current_stock').value 		= '".$stock_qty."';\n";
		echo "document.getElementById('current_prod_id').value 			= '".$info[csf("id")]."';\n";
		echo "document.getElementById('cbo_division').value 			= ".$ex_data[3].";\n";
		echo "load_drop_down( 'requires/general_item_issue_controller', ".$ex_data[3].", 'load_drop_down_department', 'department_td' );\n";
		echo "document.getElementById('cbo_department').value 			= ".$ex_data[4].";\n";
		echo "document.getElementById('txt_remarks_dtls').value 			= '".$ex_data[7]."';\n";
		//echo "document.getElementById('hidden_req_qnty').value 			= '".$ex_data[1]."';\n";
		//echo "document.getElementById('total_issued_qnty').value 		= '".$total_qnty."';\n";
		//echo "document.getElementById('cbo_store_name').value 			= '0';\n";
		//echo "document.getElementById('txt_current_stock').value 		= '';\n";
		echo "disabled_enable();\n";

	}


 //echo "load_drop_down( 'requires/general_item_issue_controller',"."document.getElementById('cbo_company_id').value"." + '**' + $ex_data[0], 'load_drop_down_store_for_item', 'store_td' );";

exit();

}

if($action=="chk_issue_requisition_variabe")
{
	
    $sql =  sql_select("select user_given_code_status,id from variable_settings_inventory where company_name = $data and variable_list = 24 and ITEM_CATEGORY_ID=8 and is_deleted = 0 and status_active = 1");
	$return_data="";
    if(count($sql)>0)
	{
		$return_data=$sql[0][csf('user_given_code_status')];
	}
	else
	{ 
		$return_data=0; 
	}
	
	$variable_inventory=return_field_value("store_method","variable_settings_inventory","company_name=$data and item_category_id=8 and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1");
	
	$variable_lot=return_field_value("auto_transfer_rcv","variable_settings_inventory","company_name=$data and variable_list=32 and status_active=1 and is_deleted=0");
	
	echo $return_data."__".$variable_inventory."__".$variable_lot;
	die;
}

if ($action=="load_drop_down_store_for_item")
{

    $data=explode("**",$data);
	$company_id=$data[0];
	$prod_id=$data[1];
	//echo create_drop_down( "cbo_store_name", 130, "select a.id,a.store_name from lib_store_location a, lib_store_location_category b where a.id=b.store_location_id and b.category_type in (8,9,10,11) and a.status_active=1 and a.is_deleted=0 and FIND_IN_SET($data,a.company_id) group by a.id order by a.store_name","id,store_name", 1, "-- Select --", "", "","" );
	$store_res =  sql_select("select a.store_id  from inv_transaction a where prod_id = $prod_id and company_id = $company_id and is_deleted = 0  and status_active = 1");
        foreach($store_res as $row)
        {
            $store_ids .= $row[csf('store_id')].",";
        }

        $store_ids = implode(",",array_unique(explode(",", chop($store_ids, ','))));
        if($store_ids){
           // $store_ids = $store_ids;
		    $store_ids = "and a.id in($store_ids)";
        }else{
            $store_ids = "";
        }
	echo create_drop_down( "cbo_store_name", 130, "select a.id,a.store_name from lib_store_location a, lib_store_location_category b where a.id=b.store_location_id and b.category_type in ( $item_cate_credential_cond ) and a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id $store_location_credential_cond $store_ids group by a.id,a.store_name order by a.store_name","id,store_name", 1, "-- Select --", $selected, "get_php_form_data(this.value+'__'+document.getElementById('current_prod_id').value, 'populate_store_prod_data', 'requires/general_item_issue_controller');","" );
	exit();
}


if($action=="populate_store_prod_data")
{
	$data_ref=explode("__",$data);
	$store_id=$data_ref[0];
	$prod_id=$data_ref[1];
	if($prod_id>0 && $store_id>0)
	{
		$store_stock_sql=sql_select("select sum((case when transaction_type in(1,4,5) then cons_quantity else 0 end)-(case when transaction_type in(2,3,6) then cons_quantity else 0 end)) as balance_qnty from inv_transaction where status_active=1 and prod_id=$prod_id and store_id=$store_id");
		echo "document.getElementById('txt_current_stock').value 			= '".$store_stock_sql[0][csf("balance_qnty")]."';\n";

	}
	else
	{
		echo "document.getElementById('txt_current_stock').value 			= '0';\n";
	}
	exit();
}



if ($action == "load_drop_down_category") 
{
	$new_conn=integration_params(3);
	$asset_category_result=sql_select("SELECT ID, ASSET_TYPE_ID, ASSET_CATEGORY_NAME, STATUS_ACTIVE FROM LIB_FAM_ASSET_CATEGORY_TYPE WHERE IS_DELETED=0 ORDER BY ASSET_TYPE_ID,ID",'',$new_conn);
	//echo "<pre>";print_r($asset_category_result);die;
	$fams_asset_category_arr=array();
	foreach($asset_category_result as $row){
		$fams_asset_category_arr[$row['ASSET_TYPE_ID']][$row['ID']]=$row['ASSET_CATEGORY_NAME'];
	}
	unset($asset_category_result);
	echo create_drop_down("cbo_category", 170, $fams_asset_category_arr[$data], "", 1, "--- Select ---", $selected, "", "", "", "", "", "", "4", "", "");
    exit();
}


if ($action == "search_asset_entry") 
{
    echo load_html_head_contents("Popup Info", "../../../", 1, 1, $unicode, 1);
    extract($_REQUEST);
	$new_conn=integration_params(3);
	$asset_type	= return_library_array("select id, asset_type, asset_type_rename from lib_fam_asset_type where status_active =1 and is_deleted=0 order by id", "id", "asset_type_rename",$new_conn);
	//echo "<pre>";print_r($asset_type);die;
	
	

    ?>
    <script>
		var companyName= <? echo $cbo_company_name ?>;
        function js_set_value(id) 
		{
            document.getElementById('hidden_system_number').value = id;
            parent.emailwindow.hide();
        }

    </script>
    </head>
    <body>
        <div align="center" style="width:100%;" >
            <form name="searchorderfrm_2"  id="searchorderfrm_2" autocomplete="off">
                <table width="1070" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
                    <thead>
                        <tr>
                            <th width="170" class="must_entry_caption">Company Name</th>
                            <th width="170">Location</th>
                            <th width="110">Asset Type</th>
                            <th width="170">Category</th>
                            <th width="80">Entry No</th>
                            <th width="80">Asset No</th> 
                            <th width="210" align="center" >Date Range</th>
                            <th width="80"><input type="reset" name="re_button" id="re_button" value="Reset" style="width:70px" class="formbutton"  /></th>           
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <?php
                                echo create_drop_down("cbo_company_name", 170, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name", "id,company_name", 1, "-- Select Company --", $cbo_company_name, "load_drop_down( 'general_item_issue_controller', this.value, 'load_drop_down_location_asetpopup', 'src_location_td');", "", "", "", "", "", "", "", "");
                                ?>
                            </td>
                            <td id="src_location_td">
                                <?php
                                echo create_drop_down("cbo_location", 170, $blank_array, "", 1, "-- Select Location --", $selected, "", "", "", "", "", "", "", "", "");
                                ?>
                            </td>
                            <td>
                                <?php
                                echo create_drop_down("cbo_aseet_type", 110, $asset_type, "", 1, "--- Select ---", $selected, "load_drop_down( 'general_item_issue_controller', this.value, 'load_drop_down_category', 'src_category_td' );", "", "", "", "", "", "", "", "");
                                ?>
                            </td>
                            <td id="src_category_td">
                                <?php
                                echo create_drop_down("cbo_category", 170, $blank_array, "", 1, "--- Select ---", $selected, "", "", "", "", "", "", "", "", "");
                                ?>
                            </td>
                            <td>
                                <input type="text" name="txt_entry_no" id="txt_entry_no" style="width:80px;" class="text_boxes">
                            </td>
                            <td>
                                <input type="text" name="asset_number" id="asset_number" style="width:80px;" class="text_boxes">
                            </td>
                            <td align="">
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:66px" placeholder="From" readonly/>-
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:66px" placeholder="To" readonly/>
                            </td>  

                            <td align="center">
                                <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view(document.getElementById('asset_number').value + '_' + document.getElementById('cbo_company_name').value + '_' + document.getElementById('cbo_location').value + '_' + document.getElementById('cbo_aseet_type').value + '_' + document.getElementById('cbo_category').value + '_' + document.getElementById('txt_date_from').value + '_' + document.getElementById('txt_date_to').value + '_' + document.getElementById('txt_entry_no').value, 'show_searh_active_listview', 'searh_list_view', 'general_item_issue_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" />		
                            </td>
                        </tr>
                        <tr>                  
                            <td align="center" height="40" valign="middle" colspan="8">
                                <?php echo load_month_buttons(1); ?>
                                <!-- Hidden field here-->
                                <input type="hidden" id="hidden_system_number" value="" />
                                <!--END-->
                            </td>
                        </tr>  
                    </tbody>
                </table> 

            </form>
            <div align="center" valign="top" id="searh_list_view"> </div>
        </div>

    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    <script>
		//if(companyName>0)  load_drop_down( 'asset_acquisition_unite_price_change_controller',companyName, 'load_drop_down_location', 'src_location_td');
    </script>
    </html>
    <?php
}


if ($action == "show_searh_active_listview") 
{
	$ex_data = explode("_", $data);
	$new_conn=integration_params(3);
	//echo $new_conn.test;die;
	$company_location = return_library_array("select id,location_name from lib_location where status_active =1 and is_deleted=0", "id", "location_name");
	$store_library 		= return_library_array("select id,store_name from lib_store_location where status_active=1 and is_deleted=0 and company_id='$ex_data[1]'", "id", "store_name");
	$asset_category_result=sql_select("SELECT ID, ASSET_TYPE_ID, ASSET_CATEGORY_NAME, STATUS_ACTIVE FROM LIB_FAM_ASSET_CATEGORY_TYPE WHERE IS_DELETED=0 ORDER BY ASSET_TYPE_ID,ID",'',$new_conn);
	//echo "<pre>";print_r($asset_category_result);die;
	$fams_asset_category_arr=array();
	foreach($asset_category_result as $row){
		$fams_asset_category_arr[$row['ASSET_TYPE_ID']][$row['ID']]=$row['ASSET_CATEGORY_NAME'];
	}
	unset($asset_category_result);
	
	if ( trim($ex_data[0]) == 0)
		$asset_number = "";
	else
		$asset_number = " and c.asset_no LIKE '%" . trim($ex_data[0]) . "'";
		
	if ($ex_data[1] == 0)
		$company_id = "";
	else
		$company_id = " and a.company_id='" . $ex_data[1] . "'";
		
	if ($ex_data[2] == 0)
		$location = "";
	else
		$location = " and a.location='" . $ex_data[2] . "'";
		
	if ($ex_data[3] == 0)
		$aseet_type = "";
	else
		$aseet_type = " and a.asset_type='" . $ex_data[3] . "'";
		
	if ($ex_data[4] == 0)
		$category = "";
	else
		$category = " and a.asset_category='" . $ex_data[4] . "'";
	
	$txt_date_from = $ex_data[5];
	$txt_date_to = $ex_data[6];
	
	if ( trim($ex_data[7]," ") == "")
		$entry_no_cond = "";
	else
		$entry_no_cond = " and a.entry_no LIKE '%" . trim($ex_data[7]) . "'";
	
	
	
	
	if ($ex_data[1] == 0) { echo "Please Company first"; die; }
	
	if ($db_type == 0) 
	{//for mysql
		if ($txt_date_from != "" || $txt_date_to != "") {
			$tran_date = " and a.purchase_date between '" . change_date_format($txt_date_from, 'yyyy-mm-dd') . "' and '" . change_date_format($txt_date_to, 'yyyy-mm-dd') . "'";
		}
		$sql = "SELECT  a.id, a.entry_no, c.asset_no, a.location, a.asset_type, a.asset_category, a.store, a.purchase_date, a.qty  FROM fam_acquisition_mst a, fam_acquisition_sl_dtls c  WHERE a.id=c.mst_id AND a.status_active=1 AND a.is_deleted=0 AND c.status_active=1 AND c.is_deleted=0 $category $aseet_type $location $company_id $asset_number $entry_no_cond $tran_date order by a.id,c.asset_no";
	}
	
	if ($db_type == 2) {//for oracal
		if ($txt_date_from != "" && $txt_date_to != "") {
			$tran_date = " and a.purchase_date between '" . change_date_format($txt_date_from, '', '', 1) . "' and '" . change_date_format($txt_date_to, '', '', 1) . "'";
		}
		$sql = "SELECT  a.id, a.entry_no, c.asset_no, a.location, a.asset_type, a.asset_category, a.store, a.purchase_date, a.qty  FROM fam_acquisition_mst a, fam_acquisition_sl_dtls c  WHERE a.id=c.mst_id AND a.status_active=1 AND a.is_deleted=0  AND c.status_active=1 AND c.is_deleted=0 $category $aseet_type $location $company_id $asset_number $entry_no_cond $tran_date order by a.id,c.asset_no";
	}
	$prev_asset_no=return_library_array("select raw_issue_challan from inv_transaction where status_active=1 and transaction_type=2 and item_category in(".implode(",",array_flip($general_item_category)).") and raw_issue_challan is not null","raw_issue_challan","raw_issue_challan");
	$result = sql_select($sql,'',$new_conn);
	//echo "<pre>";print_r($result);die;
	?>
    <table class="rpt_table" rules="all" width="978" cellspacing="0" cellpadding="0" border="0">
        <thead>
            <tr>
                <th width="50">SL No</th>
                <th width="150">Entry No</th>
                <th width="130">Asset No</th>
                <th width="150">Location</th>
                <th width="90">Type</th>
                <th width="90">Category</th>
                <th width="120">Store</th>
                <th width="90">Purchase Date</th>
                <th>Qty</th>
            </tr>
        </thead>
    </table> 
    <div style="max-height:300px; width:976px; overflow-y:scroll">
    <table class="rpt_table" id="list_view" rules="all" width="958" height="" cellspacing="0" cellpadding="0" border="0">
    <tbody>
        <? 
        foreach($result as $row)
        {
			if($prev_asset_no[$row[csf('entry_no')]]=="")
			{
				$asset_category = $fams_asset_category_arr[$row[csf('asset_type')]];
				$i++;
				$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
				?>
				<tr onClick="js_set_value('<? echo $row[csf('entry_no')];?>')" style="cursor:pointer" id="tr_<? echo $i; ?>" height="20" bgcolor="<? echo $bgcolor;?>">
					<td width="50"><? echo $i; ?></td>
					<td width="150" align="left"><p><? echo $row[csf('entry_no')];?></p></td>
					<td width="130" align="left"><p><? echo $row[csf('asset_no')];?></p></td>
					<td width="150" align="left"><p><? echo $company_location[$row[csf('location')]];?></p></td>
					<td width="90" align="left"><p><? echo $asset_type[$row[csf('asset_type')]];?></p></td>
					<td width="90" align="left"><p><? echo $asset_category[$row[csf('asset_category')]];?></p></td>
					<td width="120" align="left"><p><? echo $store_library[$row[csf('store')]];?></p></td>
					<td width="90" align="left"><p><? echo change_date_format($row[csf("purchase_date")], "dd-mm-yyyy", "-");?></p></td>
					<td align="right"><p><? echo $row[csf('qty')];?></p></td>
				</tr>
				<?
			}
        }
        ?>
    </tbody>
    </table>
    </div>
	<?
    exit;
}

if($action=="print_button_variable_setting")
{
    $print_report_format=0;
    $print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=6 and report_id=182 and is_deleted=0 and status_active=1");
   	$printButton=explode(',',$print_report_format);

	foreach($printButton as $id)
	{
		if($id==143)$buttonHtml.='<input id="print_button_1" type="button" class="formbutton_disabled" style="width:80px" onClick="fnc_general_item_issue_entry(4)" name="print" value="Print">';
		if($id==66)$buttonHtml.='<input id="print_button_2" type="button" class="formbutton_disabled" style="width:80px" onClick="fnc_general_item_issue_entry(5)" name="print" value="Print 2">';
		if($id==85)$buttonHtml.='<input id="print_button_3" type="button" class="formbutton_disabled" style="width:80px" onClick="fnc_general_item_issue_entry(6)" name="print" value="Print 3">';
		if($id==160)$buttonHtml.='<input id="print_button_4" type="button" class="formbutton_disabled" style="width:80px" onClick="fnc_general_item_issue_entry(7)" name="print_4" value="Print 4">';
		if($id==129)$buttonHtml.='<input id="print_button_5" type="button" class="formbutton_disabled" style="width:80px" onClick="fnc_general_item_issue_entry(8)" name="print_5" value="Print 5">';
		
	}

   echo "document.getElementById('button_data_panel').innerHTML = '".$buttonHtml."';\n";
   exit();
}

?>
