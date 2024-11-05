<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

$item_category_without_general=array_diff($item_category,$general_item_category);
$genarel_item_arr=array(4=>"Accessories",8=>"General Item");
$item_category_with_gen=$item_category_without_general+$genarel_item_arr;
ksort($item_category_with_gen);

if($action=="lib_max_btb_limit_data")
{
	$nameArray=sql_select( "select id, max_btb_limit, cost_heads_status from variable_settings_commercial where company_name='$data' and variable_list=6" );
	echo $nameArray[0][csf("max_btb_limit")]."_".$nameArray[0][csf("cost_heads_status")];
	exit();
}

//------------------------------------------Load Drop Down on Change---------------------------------------------//
if ($action=="load_supplier_dropdown")
{
	//echo $data;
	$data = explode('_',$data);

	if ($data[1]==0)
	{
		//echo create_drop_down( "cbo_supplier_id", 165, $blank_array,'', 1, '----Select----',0,0,0);
		echo create_drop_down( "cbo_supplier_id",165,"select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data[0]' and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by c.supplier_name",'id,supplier_name', 1, '----Select----',0,0,0);
	}
	else if($data[1]==1)
	{
		echo create_drop_down( "cbo_supplier_id",165,"select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data[0]' and b.party_type =2 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by c.supplier_name",'id,supplier_name', 1, '----Select----',0,0,0);
	}
	else if($data[1]==2 || $data[1]==3 || $data[1]==13 || $data[1]==14)
	{
		echo create_drop_down( "cbo_supplier_id", 165,"select c.supplier_name, c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data[0]' and b.party_type =9 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by c.supplier_name",'id,supplier_name', 1, '----Select----',0,0,0);
	}
	else if($data[1]==4)
	{
		echo create_drop_down( "cbo_supplier_id", 165,"select c.supplier_name,c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data[0]' and b.party_type in(4,5) and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by c.supplier_name",'id,supplier_name', 1, '----Select----',0,0,0);

	}
	else if($data[1]==5 || $data[1]==6 || $data[1]==7)
	{
		echo create_drop_down( "cbo_supplier_id", 165,"select c.supplier_name,c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data[0]' and b.party_type=3 group by c.id, c.supplier_name order by c.supplier_name",'id,supplier_name', 1, '----Select----',0,0,0);
	}
	else if($data[1]==8)
	{
		echo create_drop_down( "cbo_supplier_id", 165,"select c.supplier_name,c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data[0]' and b.party_type = 7 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by c.supplier_name",'id,supplier_name', 1, '----Select----',0,0,0);
	}
	else if($data[1]==9 || $data[1]==10)
	{
		echo create_drop_down( "cbo_supplier_id", 165,"select c.supplier_name,c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data[0]' and b.party_type = 6 group by c.id, c.supplier_name order by c.supplier_name",'id,supplier_name', 1, '----Select----',0,0,0);
	}
	else if($data[1]==11)
	{
		echo create_drop_down( "cbo_supplier_id", 165,"select c.supplier_name,c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data[0]' and b.party_type = 8 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by c.supplier_name",'id,supplier_name', 1, '----Select----',0,0,0);
	}
	else if($data[1]==12 || $data[1]==24 || $data[1]==25)
	{
		echo create_drop_down( "cbo_supplier_id", 165,"select c.supplier_name,c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data[0]' and b.party_type in(20,21,22,23,24,30,31,32,35,36,37,38,39) and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name",'id,supplier_name', 1, '----Select----',0,0,0);
	}
	else if($data[1]==32)
	{
		echo create_drop_down( "cbo_supplier_id", 165,"select c.supplier_name,c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data[0]' and b.party_type in(92) and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by c.supplier_name",'id,supplier_name', 1, '----Select----',0,0,0);
	}
	else if($data[1]==110)
	{
		echo create_drop_down( "cbo_supplier_id", 165,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name",'id,company_name', 1, '----Select----',0,0,0);
	}
	else
	{
		echo create_drop_down( "cbo_supplier_id", 165,"select DISTINCT(c.supplier_name),c.id from  lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data[0]' and b.party_type = 7 and c.status_active=1 and c.is_deleted=0 order by c.supplier_name",'id,supplier_name', 1, '-- Select Supplier --',0,'',0);
	}

	exit();
}
if($action=="load_supplier_dropdown_new")
{
	
	$data = explode('_',$data);

	if ($data[1]==0)
	{
		//echo create_drop_down( "cbo_supplier_id", 165, $blank_array,'', 1, '----Select----',0,0,0);
		$sql = "SELECT c.supplier_name, c.id FROM lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c   WHERE    c.id = b.supplier_id	 AND a.supplier_id = b.supplier_id	AND a.tag_company = '$data[0]'	AND c.status_active = 1	 AND c.is_deleted = 0GROUP BY c.id, c.supplier_name UNION ALL SELECT c.supplier_name, c.id	FROM lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c , com_btb_lc_master_details d   WHERE    c.id = b.supplier_id AND a.supplier_id = b.supplier_id	 AND d.supplier_id = c.id		 AND a.tag_company = '$data[0]' AND c.status_active IN( 1,3)	 AND c.is_deleted = 0 GROUP BY c.id, c.supplier_name ORDER BY supplier_name";

		echo create_drop_down( "cbo_supplier_id",165,"$sql",'id,supplier_name', 1, '----Select----',0,0,0);
	}
	else if($data[1]==1)
	{
		$sql = "SELECT c.supplier_name, c.id FROM lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c   WHERE    c.id = b.supplier_id	 AND a.supplier_id = b.supplier_id	AND a.tag_company = '$data[0]' and b.party_type =2	AND c.status_active = 1	 AND c.is_deleted = 0GROUP BY c.id, c.supplier_name UNION ALL SELECT c.supplier_name, c.id	FROM lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c , com_btb_lc_master_details d   WHERE    c.id = b.supplier_id AND a.supplier_id = b.supplier_id	 AND d.supplier_id = c.id	AND a.tag_company = '$data[0]' and b.party_type = 2 AND c.status_active IN( 1,3)	 AND c.is_deleted = 0 GROUP BY c.id, c.supplier_name ORDER BY supplier_name";
       
		echo create_drop_down( "cbo_supplier_id",165,"select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data[0]' and b.party_type =2 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by c.supplier_name",'id,supplier_name', 1, '----Select----',0,0,0);
	}
	else if($data[1]==2 || $data[1]==3 || $data[1]==13 || $data[1]==14)
	{
		$sql = "SELECT c.supplier_name, c.id FROM lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c   WHERE    c.id = b.supplier_id	 AND a.supplier_id = b.supplier_id	AND a.tag_company = '$data[0]' and b.party_type =9	AND c.status_active = 1	 AND c.is_deleted = 0GROUP BY c.id, c.supplier_name UNION ALL SELECT c.supplier_name, c.id	FROM lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c , com_btb_lc_master_details d   WHERE    c.id = b.supplier_id AND a.supplier_id = b.supplier_id	 AND d.supplier_id = c.id	AND a.tag_company = '$data[0]' and b.party_type = 9 AND c.status_active IN( 1,3)	 AND c.is_deleted = 0 GROUP BY c.id, c.supplier_name ORDER BY supplier_name";
       
		echo create_drop_down( "cbo_supplier_id", 165,"$sql",'id,supplier_name', 1, '----Select----',0,0,0);
	}
	else if($data[1]==4)
	{
		$sql = "SELECT c.supplier_name, c.id FROM lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c   WHERE    c.id = b.supplier_id	 AND a.supplier_id = b.supplier_id	AND a.tag_company = '$data[0]' and b.party_type IN(4,5)	AND c.status_active = 1	 AND c.is_deleted = 0GROUP BY c.id, c.supplier_name UNION ALL SELECT c.supplier_name, c.id	FROM lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c , com_btb_lc_master_details d   WHERE    c.id = b.supplier_id AND a.supplier_id = b.supplier_id	 AND d.supplier_id = c.id	AND a.tag_company = '$data[0]' and b.party_type IN(4,5) AND c.status_active IN( 1,3)	 AND c.is_deleted = 0 GROUP BY c.id, c.supplier_name ORDER BY supplier_name";

		echo create_drop_down( "cbo_supplier_id", 165,"$sql",'id,supplier_name', 1, '----Select----',0,0,0);

	}
	else if($data[1]==5 || $data[1]==6 || $data[1]==7)
	{
		$sql = "SELECT c.supplier_name, c.id FROM lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c   WHERE    c.id = b.supplier_id	 AND a.supplier_id = b.supplier_id	AND a.tag_company = '$data[0]' and b.party_type IN(3)	AND c.status_active = 1	 AND c.is_deleted = 0GROUP BY c.id, c.supplier_name UNION ALL SELECT c.supplier_name, c.id	FROM lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c , com_btb_lc_master_details d   WHERE    c.id = b.supplier_id AND a.supplier_id = b.supplier_id	 AND d.supplier_id = c.id	AND a.tag_company = '$data[0]' and b.party_type IN(3) AND c.status_active IN( 1,3)	 AND c.is_deleted = 0 GROUP BY c.id, c.supplier_name ORDER BY supplier_name";

		echo create_drop_down( "cbo_supplier_id", 165,"$sql",'id,supplier_name', 1, '----Select----',0,0,0);
	}
	else if($data[1]==8)
	{
		$sql = "SELECT c.supplier_name, c.id FROM lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c   WHERE    c.id = b.supplier_id	 AND a.supplier_id = b.supplier_id	AND a.tag_company = '$data[0]' and b.party_type IN(7)	AND c.status_active = 1	 AND c.is_deleted = 0GROUP BY c.id, c.supplier_name UNION ALL SELECT c.supplier_name, c.id	FROM lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c , com_btb_lc_master_details d   WHERE    c.id = b.supplier_id AND a.supplier_id = b.supplier_id	 AND d.supplier_id = c.id	AND a.tag_company = '$data[0]' and b.party_type IN(7) AND c.status_active IN( 1,3)	 AND c.is_deleted = 0 GROUP BY c.id, c.supplier_name ORDER BY supplier_name";
		echo create_drop_down( "cbo_supplier_id", 165,"$sql",'id,supplier_name', 1, '----Select----',0,0,0);
	}
	else if($data[1]==9 || $data[1]==10)
	{
		$sql = "SELECT c.supplier_name, c.id FROM lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c   WHERE    c.id = b.supplier_id	 AND a.supplier_id = b.supplier_id	AND a.tag_company = '$data[0]' and b.party_type IN(6)	AND c.status_active = 1	 AND c.is_deleted = 0GROUP BY c.id, c.supplier_name UNION ALL SELECT c.supplier_name, c.id	FROM lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c , com_btb_lc_master_details d   WHERE    c.id = b.supplier_id AND a.supplier_id = b.supplier_id	 AND d.supplier_id = c.id	AND a.tag_company = '$data[0]' and b.party_type IN(6) AND c.status_active IN( 1,3)	 AND c.is_deleted = 0 GROUP BY c.id, c.supplier_name ORDER BY supplier_name";

		echo create_drop_down( "cbo_supplier_id", 165,"$sql",'id,supplier_name', 1, '----Select----',0,0,0);
	}
	else if($data[1]==11)
	{
		$sql = "SELECT c.supplier_name, c.id FROM lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c   WHERE    c.id = b.supplier_id	 AND a.supplier_id = b.supplier_id	AND a.tag_company = '$data[0]' and b.party_type IN(8)	AND c.status_active = 1	 AND c.is_deleted = 0GROUP BY c.id, c.supplier_name UNION ALL SELECT c.supplier_name, c.id	FROM lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c , com_btb_lc_master_details d   WHERE    c.id = b.supplier_id AND a.supplier_id = b.supplier_id	 AND d.supplier_id = c.id	AND a.tag_company = '$data[0]' and b.party_type IN(8) AND c.status_active IN( 1,3)	 AND c.is_deleted = 0 GROUP BY c.id, c.supplier_name ORDER BY supplier_name";

		echo create_drop_down( "cbo_supplier_id", 165,"$sql",'id,supplier_name', 1, '----Select----',0,0,0);
	}
	else if($data[1]==12 || $data[1]==24 || $data[1]==25)
	{
		$sql = "SELECT c.supplier_name, c.id FROM lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c   WHERE    c.id = b.supplier_id	 AND a.supplier_id = b.supplier_id	AND a.tag_company = '$data[0]' and b.party_type IN(20,21,22,23,24,30,31,32,35,36,37,38,39)	AND c.status_active = 1	 AND c.is_deleted = 0GROUP BY c.id, c.supplier_name UNION ALL SELECT c.supplier_name, c.id	FROM lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c , com_btb_lc_master_details d   WHERE    c.id = b.supplier_id AND a.supplier_id = b.supplier_id	 AND d.supplier_id = c.id	AND a.tag_company = '$data[0]' and b.party_type IN(20,21,22,23,24,30,31,32,35,36,37,38,39) AND c.status_active IN( 1,3)	 AND c.is_deleted = 0 GROUP BY c.id, c.supplier_name ORDER BY supplier_name";

		echo create_drop_down( "cbo_supplier_id", 165,"$sql",'id,supplier_name', 1, '----Select----',0,0,0);
	}
	else if($data[1]==32)
	{
		$sql = "SELECT c.supplier_name, c.id FROM lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c   WHERE    c.id = b.supplier_id	 AND a.supplier_id = b.supplier_id	AND a.tag_company = '$data[0]' and b.party_type IN(92)	AND c.status_active = 1	 AND c.is_deleted = 0GROUP BY c.id, c.supplier_name UNION ALL SELECT c.supplier_name, c.id	FROM lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c , com_btb_lc_master_details d   WHERE    c.id = b.supplier_id AND a.supplier_id = b.supplier_id	 AND d.supplier_id = c.id	AND a.tag_company = '$data[0]' and b.party_type IN(92) AND c.status_active IN( 1,3)	 AND c.is_deleted = 0 GROUP BY c.id, c.supplier_name ORDER BY supplier_name";

		echo create_drop_down( "cbo_supplier_id", 165,"$sql",'id,supplier_name', 1, '----Select----',0,0,0);
	}
	else if($data[1]==110)
	{
		$sql = "SELECT c.supplier_name, c.id FROM lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c   WHERE    c.id = b.supplier_id	 AND a.supplier_id = b.supplier_id	AND a.tag_company = '$data[0]' 	AND c.status_active = 1	 AND c.is_deleted = 0GROUP BY c.id, c.supplier_name UNION ALL SELECT c.supplier_name, c.id	FROM lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c , com_btb_lc_master_details d   WHERE    c.id = b.supplier_id AND a.supplier_id = b.supplier_id	 AND d.supplier_id = c.id	AND a.tag_company = '$data[0]'  AND c.status_active IN( 1,3)	 AND c.is_deleted = 0 GROUP BY c.id, c.supplier_name ORDER BY supplier_name";

		echo create_drop_down( "cbo_supplier_id", 165,"$sql",'id,company_name', 1, '----Select----',0,0,0);
	}
	else
	{
		$sql = "SELECT c.supplier_name, c.id FROM lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c   WHERE    c.id = b.supplier_id	 AND a.supplier_id = b.supplier_id	AND a.tag_company = '$data[0]' and b.party_type IN(7)	AND c.status_active = 1	 AND c.is_deleted = 0GROUP BY c.id, c.supplier_name UNION ALL SELECT c.supplier_name, c.id	FROM lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c , com_btb_lc_master_details d   WHERE    c.id = b.supplier_id AND a.supplier_id = b.supplier_id	 AND d.supplier_id = c.id	AND a.tag_company = '$data[0]' and b.party_type IN(7) AND c.status_active IN( 1,3)	 AND c.is_deleted = 0 GROUP BY c.id, c.supplier_name ORDER BY supplier_name";

		echo create_drop_down( "cbo_supplier_id", 165,"$sql",'id,supplier_name', 1, '-- Select Supplier --',0,'',0);
	}

	exit();
}
if($action=="print_button_variable_setting")
{
    $print_report_format=0;
    $print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=5 and report_id=115 and is_deleted=0 and status_active=1");
   	$printButton=explode(',',$print_report_format);

	foreach($printButton as $id){

		if($id==678)$buttonHtml.='<input type="button" style="width:80px;" id="btn_consignment"  onClick="fnc_letter_print(4)" class="formbutton printReport" name="btn_consignment" value="Letter Print" />&nbsp;';
		if($id==233)$buttonHtml.='<input id="btn_print_letter" class="formbutton printReport" type="button" style="width:80px" onclick="fn_print_letter(1)" name="btn_print_letter" value="Print letter">&nbsp;';

		if($id==234)$buttonHtml.='<input type="button" style="width:80px;" id="btn_print_letter2"  onClick="fn_print_letter(2)"   class="formbutton printReport" name="btn_print_letter2" value="Print letter 2" />&nbsp;';

		if($id==240)$buttonHtml.='<input type="button" style="width:80px;" id="btn_print_letter3"  onClick="fn_print_letter(3)" class="formbutton printReport" name="id_print_to_button2" value="Print letter3" />&nbsp;';
		
		
		if($id==679)$buttonHtml.='<input type="button" class="formbutton" id="btn_print_letter" value="Print letter4" style="width:100px;" onClick="fn_print_letter(4)" />&nbsp;';
		if($id==697)$buttonHtml.='<input type="button" class="formbutton" id="btn_print_letter5" value="Print letter5" style="width:100px;" onClick="fn_print_letter(6)" />&nbsp;';
		if($id==680)$buttonHtml.='<input type="button" class="formbutton" id="btn_print_letter" value="EXIM" style="width:100px;" onClick="fn_application_form()" />&nbsp;';

		if($id==681)$buttonHtml.=' <input type="button" class="formbutton" id="btn_print_letter" value="BRAC-CF7" style="width:100px;" onClick="fn_application_form_new(11)">&nbsp;';
		if($id==682)$buttonHtml.='<input type="button" class="formbutton" id="btn_print_letter" value="IFIC-CF7" style="width:100px;" onClick="fn_application_form_new(12)" >&nbsp;';
		if($id==683)$buttonHtml.='<input type="button" class="formbutton" id="btn_print_letter" value="ONE-CF7" style="width:100px;" onClick="fn_application_form_new(13)" >&nbsp;';
		if($id==686)$buttonHtml.='<input type="button" class="formbutton" id="btn_print_letter" value="BRAC-LCA" style="width:100px;" onClick="fn_application_form_new(15)" >&nbsp;';		
		if($id==687)$buttonHtml.='<input type="button" class="formbutton" id="btn_print_letter" value="IFIC-LCA" style="width:100px;" onClick="fn_application_form_new(16)" >&nbsp;';	
		
		if($id==684) $buttonHtml.='<input type="button" class="formbutton" id="btn_print_letter" value="DBBL-CF7" style="width:100px;" onClick="fn_application_form_new(14)" >&nbsp;';
		if($id==685)$buttonHtml.=' <input type="button" class="formbutton" id="btn_undertaking_form" value="Undertaking Letter" style="width:110px;" onClick="fn_undertaking_letter()" >&nbsp;';
		if($id==692)$buttonHtml.='<input type="button" class="formbutton" id="btn_print_letter" value="Eastern-CF7" style="width:100px;" onClick="fn_application_form_new(17)" >&nbsp;';
		if($id==693)$buttonHtml.='<input type="button" class="formbutton" id="btn_print_letter" value="JAMUNA-CF7" style="width:100px;" onClick="fn_application_form_new(18)" >&nbsp;';
		if($id==695)$buttonHtml.='<input type="button" class="formbutton" id="btn_print_letter" value="UCB-CF7" style="width:100px;" onClick="fn_application_form_new(19)" >&nbsp;';
		if($id==698)$buttonHtml.='<input type="button" class="formbutton" id="btn_print_letter" value="PRIME-LCA " style="width:100px;" onClick="fn_application_form_new(20)" >&nbsp;';
		if($id==699)$buttonHtml.='<input type="button" class="formbutton" id="btn_print_letter" value="CITY-CF7" style="width:100px;" onClick="fn_application_form_new(21)" >&nbsp;';  
		if($id==700)$buttonHtml.='<input type="button" class="formbutton" id="btn_print_letter" value="Bank Asia-LCA" style="width:100px;" onClick="fn_application_form_new(22)" >&nbsp;';
		if($id==701)$buttonHtml.='<input type="button" class="formbutton" id="btn_print_letter" value="SIBL-LCA" style="width:100px;" onClick="fn_application_form_new(23)" >&nbsp;';
		if($id==702)$buttonHtml.='<input type="button" class="formbutton" id="btn_print_letter" value="MTBL-CF7" style="width:100px;" onClick="fn_application_form_new(24)" >&nbsp;';
		if($id==703)$buttonHtml.='<input type="button" class="formbutton" id="btn_print_letter" value="Dhaka-LCA" style="width:100px;" onClick="fn_application_form_new(25)" >&nbsp;';
		if($id==704)$buttonHtml.='<input type="button" class="formbutton" id="btn_print_letter" value="PUBALI-CF7" style="width:100px;" onClick="fn_application_form_new(26)" >&nbsp;';
		if($id==705)$buttonHtml.='<input type="button" class="formbutton" id="btn_print_letter" value="HSBC" style="width:100px;" onClick="fn_application_form_new(27)" >&nbsp;';
		if($id==470)$buttonHtml.='<input type="button" class="formbutton" id="btn_print_letter" value="SIBL-CF7" style="width:100px;" onClick="fn_application_form_new(28)" >&nbsp;';
		if($id==700)$buttonHtml.='<input type="button" class="formbutton" id="btn_print_letter" value="Bank Asia-LC" style="width:100px;" onClick="fn_application_form_new(29)" >&nbsp;';
		if($id==703)$buttonHtml.='<input type="button" class="formbutton" id="btn_print_letter" value="Dhaka-CF7" style="width:100px;" onClick="fn_application_form_new(30)" >&nbsp;';
		if($id==692)$buttonHtml.='<input type="button" class="formbutton" id="btn_print_letter" value="EBL-LCA" style="width:100px;" onClick="fn_application_form_new(31)" >&nbsp;';
		if($id==698)$buttonHtml.='<input type="button" class="formbutton" id="btn_print_letter" value="PRIME-CF7" style="width:100px;" onClick="fn_application_form_new(32)" >&nbsp;';
		if($id==713)$buttonHtml.='<input type="button" class="formbutton" id="btn_print_letter" value="IBBL-CF7" style="width:100px;" onClick="fn_application_form_new(33)" >&nbsp;';
		if($id==358)$buttonHtml.='<input type="button" class="formbutton" id="btn_print_letter" value="Pubali LCA" style="width:100px;" onClick="fn_application_form_new(34)" >&nbsp;';
		
		if($id==361)$buttonHtml.='<input type="button" class="formbutton" id="btn_print_letter" value="SCB LCA" style="width:100px;" onClick="fn_application_form_new(35)" >&nbsp;';
		if($id==374)$buttonHtml.='<input type="button" class="formbutton" id="btn_print_letter" value="Jamuna-LCA" style="width:100px;" onClick="fn_application_form_new(36)" >&nbsp;';
		if($id==375)$buttonHtml.='<input type="button" class="formbutton" id="btn_print_letter" value="NCC-CF7" style="width:100px;" onClick="fn_application_form_new(37)" >&nbsp;';
		if($id==376)$buttonHtml.='<input type="button" class="formbutton" id="btn_print_letter" value="NCC-LCA" style="width:100px;" onClick="fn_application_form_new(38)" >&nbsp;';
		if($id==378)$buttonHtml.='<input type="button" class="formbutton" id="btn_print_letter" value="UCB-CF7 2" style="width:100px;" onClick="fn_application_form_new(39)" >&nbsp;';
		if($id==410)$buttonHtml.='<input type="button" class="formbutton" id="btn_print_letter" value="EXIM LCA" style="width:100px;" onClick="fn_application_form_new(40)" >&nbsp;';
		if($id==411)$buttonHtml.='<input type="button" class="formbutton" id="btn_print_letter" value="Premier LCA" style="width:100px;" onClick="fn_application_form_new(41)" >&nbsp;';
		if($id==412)$buttonHtml.='<input type="button" class="formbutton" id="btn_print_letter" value="Premier CF7" style="width:100px;" onClick="fn_application_form_new(42)" >&nbsp;';
		if($id==413)$buttonHtml.='<input type="button" class="formbutton" id="btn_print_letter" value="SB CF7" style="width:100px;" onClick="fn_application_form_new(43)" >&nbsp;';
		if($id==436)$buttonHtml.='<input type="button" class="formbutton" id="btb_print_letter" value="Al-Arafa Islami Bank" style="width:120px;" onClick="fn_application_form_new(44)" />&nbsp;';
		if($id==457)$buttonHtml.='<input type="button" class="formbutton" id="btb_print_letter" value="Agrani CF7" style="width:100px;" onClick="fn_application_form_new(45)" />&nbsp;';
		if($id==458)$buttonHtml.='<input type="button" class="formbutton" id="btb_print_letter" value="Agrani LCA" style="width:100px;" onClick="fn_application_form_new(46)" />&nbsp;';
		if($id==459)$buttonHtml.='<input type="button" class="formbutton" id="btb_print_letter" value="SEB LCA" style="width:100px;" onClick="fn_application_form_new(47)" />&nbsp;';
		if($id==461)$buttonHtml.='<input type="button" class="formbutton" id="btb_print_letter" value="FSIBL CF7" style="width:100px;" onClick="fn_application_form_new(48)" />&nbsp;';
		if($id==462)$buttonHtml.='<input type="button" class="formbutton" id="btb_print_letter" value="FSIBL LCA" style="width:100px;" onClick="fn_application_form_new(49)" />&nbsp;';
		if($id==464)$buttonHtml.='<input type="button" class="formbutton" id="btb_print_letter" value="DBBL LCA" style="width:100px;" onClick="fn_application_form_new(50)" />&nbsp;';
		if($id==467)$buttonHtml.='<input type="button" class="formbutton" id="btb_print_letter" value="City LCA" style="width:100px;" onClick="fn_application_form_new(51)" />&nbsp;';
		if($id==468)$buttonHtml.='<input type="button" class="formbutton" id="btb_print_letter" value="UCB LCA" style="width:100px;" onClick="fn_application_form_new(52)" />&nbsp;';
		if($id==469)$buttonHtml.='<input type="button" class="formbutton" id="btb_print_letter" value="Al-Arafa LCA" style="width:100px;" onClick="fn_application_form_new(53)" />&nbsp;';
		if($id==471)$buttonHtml.='<input type="button" class="formbutton" id="btb_print_letter" value="SIBL-CF7 2" style="width:100px;" onClick="fn_application_form_new(54)" />&nbsp;';
		if($id==474)$buttonHtml.='<input type="button" class="formbutton" id="btb_print_letter" value="MTBL LCA" style="width:100px;" onClick="fn_application_form_new(55)" />&nbsp;';
		if($id==799)$buttonHtml.='<input type="button" class="formbutton" id="btb_print_letter" value="IKDL-SIBL" style="width:100px;" onClick="fn_application_form_new(56)" />&nbsp;';
		if($id==851)$buttonHtml.='<input type="button" class="formbutton" id="btb_print_letter" value="Midland LC-A" style="width:100px;" onClick="fn_application_form_new(57)" />&nbsp;';
		
		if($id==694)$buttonHtml.='<input type="button" style="width:120px;" id="lc_opening_later"  onClick="fn_print_letter(5)" class="formbutton printReport" name="lc_opening_later" value="LC Opening Letter 2" />&nbsp;';
		if($id==314)$buttonHtml.='<input type="button" style="width:80px;" id="btn_print_tt"  onClick="fn_print_letter(7)" class="formbutton printReport" name="btn_print_tt" value="TT of Brack" />&nbsp;';
		if($id==718)$buttonHtml.='<input type="button" style="width:80px;" id="btn_print_tt"  onClick="fn_print_letter(8)" class="formbutton printReport" name="btn_print_tt" value="TT/FDD Letter" />&nbsp;';
		if($id==720)$buttonHtml.='<input type="button" style="width:110px;" id="btn_print_tt"  onClick="fn_print_letter(9)" class="formbutton printReport" name="btn_print_tt" value="LC Opening Letter" />&nbsp;';
		if($id==721)$buttonHtml.='<input type="button" style="width:80px;" id="btn_print_tt"  onClick="fn_print_letter(10)" class="formbutton printReport" name="btn_print_tt" value="FTT Letter" />&nbsp;';
		if($id==731)$buttonHtml.='<input type="button" style="width:80px;" id="btn_print_tt"  onClick="fn_print_letter(11)" class="formbutton printReport" name="btn_print_tt" value="BTB REQ" />&nbsp;';
		if($id==344)$buttonHtml.='<input type="button" class="formbutton" id="btn_print_letter6" value="Print letter6" style="width:100px;" onClick="fn_print_letter(12)" />&nbsp;';
		if($id==760)$buttonHtml.='<input type="button" class="formbutton" id="btn_print_letter_chem" value="BTB CHEM" style="width:100px;" onClick="fn_print_letter(13)" />&nbsp;';
		if($id==357)$buttonHtml.='<input type="button" class="formbutton" id="btb_print_letter_lc_forwarding" value="LC Forwarding" style="width:100px;" onClick="fn_print_letter(14)" />&nbsp;';
		if($id==368)$buttonHtml.='<input type="button" class="formbutton" id="btb_print_letter_lc_fakir" value="Print letter 7" style="width:100px;" onClick="fn_print_letter(15)" />&nbsp;';
		if($id==369)$buttonHtml.='<input type="button" class="formbutton" id="btb_print_letter_lc_fakir" value="Print letter 8" style="width:100px;" onClick="fn_print_letter(16)" />&nbsp;';
		if($id==372)$buttonHtml.='<input type="button" class="formbutton" id="btb_print_letter_ftt" value="Print letter 9" style="width:100px;" onClick="fn_print_letter(17)" />&nbsp;';
		if($id==391)$buttonHtml.='<input type="button" class="formbutton" id="btb_print_letter" value="TT/FDD Letter 2" style="width:100px;" onClick="fn_print_letter(18)" />&nbsp;';
		if($id==392)$buttonHtml.='<input type="button" class="formbutton" id="btb_print_letter" value="LC Forwarding 2" style="width:100px;" onClick="fn_print_letter(19)" />&nbsp;';
		if($id==418)$buttonHtml.='<input type="button" class="formbutton" id="btb_print_letter" value="FTT App." style="width:100px;" onClick="fn_print_letter(20)" />&nbsp;';
		if($id==435)$buttonHtml.='<input type="button" class="formbutton" id="btb_print_letter" value="Print letter 10" style="width:100px;" onClick="fn_print_letter(21)" />&nbsp;';		
		if($id==463)$buttonHtml.='<input type="button" style="width:120px;" id="btn_print_tt"  onClick="fn_print_letter(22)" class="formbutton printReport" name="btn_print_tt" value="LC Opening Letter 3" />&nbsp;';
		if($id==465)$buttonHtml.='<input type="button" style="width:100px;" id="btn_print_pwa" title="power of attorney"  onClick="fn_print_letter(23)" class="formbutton printReport" name="btn_print_pwa" value="PWA" />&nbsp;';
		if($id==241)$buttonHtml.='<input type="button" class="formbutton" id="btb_print_letter" value="Print letter 11" style="width:100px;" onClick="fn_print_letter(24)" />&nbsp;';
		if($id==493)$buttonHtml.='<input type="button" class="formbutton" id="btb_print_letter" value="LC Forwarding 3" style="width:100px;" onClick="fn_print_letter(25)" />&nbsp;';
		if($id==496)$buttonHtml.='<input type="button" class="formbutton" id="btb_print_letter" value="LC Forwarding 4" style="width:100px;" onClick="fn_print_letter(26)" />&nbsp;';
		if($id==497)$buttonHtml.='<input type="button" class="formbutton" id="btb_print_letter" value="LC Forwarding 5" style="width:100px;" onClick="fn_print_letter(27)" />&nbsp;';
		if($id==499)$buttonHtml.='<input type="button" class="formbutton" id="btb_print_letter" value="FTT Letter 2" style="width:100px;" onClick="fn_print_letter(28)" />&nbsp;';
		if($id==500)$buttonHtml.='<input type="button" class="formbutton" id="btb_print_letter" value="FDD Letter" style="width:100px;" onClick="fn_print_letter(29)" />&nbsp;';		
		if($id==822)$buttonHtml.='<input type="button" class="formbutton" id="btb_print_letter" value="Letter Local" style="width:100px;" onClick="fn_print_letter(31)" />&nbsp;';
		if($id==823)$buttonHtml.='<input type="button" class="formbutton" id="btb_print_letter" value="Letter Foreign" style="width:100px;" onClick="fn_print_letter(32)" />&nbsp;';
		if($id==824)$buttonHtml.='<input type="button" class="formbutton" id="btb_print_letter" value="Letter TT" style="width:100px;" onClick="fn_print_letter(33)" />&nbsp;';			
		if($id==836)$buttonHtml.='<input type="button" class="formbutton" id="btb_print_letter" value="Print Letter 12" style="width:100px;" onClick="fn_print_letter(34)" />&nbsp;';	
		if($id==404)$buttonHtml.='<input type="button" class="formbutton" id="btb_print_letter" value="Print B21" style="width:100px;" onClick="fn_print_letter(35)" />&nbsp;';
	    if($id==426)$buttonHtml.='<input type="button" class="formbutton" id="btb_print_letter" value="Print B23" style="width:100px;" onClick="fn_print_letter(36)" />&nbsp;';	
		if($id==582)$buttonHtml.='<input type="button" class="formbutton" id="btb_print_letter" value="ONE-CF7" style="width:100px;" onClick="fn_print_letter(37)" />&nbsp;';
		if($id==905)$buttonHtml.='<input type="button" class="formbutton" id="btb_print_letter" value="LC Forwarding 7" style="width:100px;" onClick="fn_print_letter(38)" />&nbsp;';	
			
	}

   	echo "document.getElementById('button_data_panel').innerHTML = '".$buttonHtml."';\n";
    exit();
}

?>



<?
if ($action=="load_drop_down_file_number")
{
	$sql="select distinct(internal_file_no) as internal_file_no from com_export_lc where beneficiary_name=$data and status_active=1 and is_deleted=0  union select distinct(internal_file_no) as internal_file_no from com_sales_contract where beneficiary_name=$data and status_active=1 and is_deleted=0 order by internal_file_no";
	echo create_drop_down( "txt_search_text", 150,$sql,"internal_file_no,internal_file_no", 1, "-- Select --", 1,"");
	exit();
}





if($action=='file_upload')
{
	extract($_REQUEST);
	$data_array="";
	$id=return_next_id( "id","common_photo_library", 1 ) ;
	for($i=0;$i<count($_FILES['file']);$i++)
	{
		$filename = time(). $_FILES['file'][name][$i]; 
		$location = "../../../file_upload/".$filename;
		if(move_uploaded_file( $_FILES['file']['tmp_name'][$i], $location))
		{ 
			if($data_array!="") $data_array.=",";
			$data_array .="(".$id.",".$mst_id.",'BTBMargin LC','file_upload/".$filename."','2','".$filename."')";
		}
		else
		{ 
			echo 0; 
		}
		$id++; 
	}
		
		
	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}
	
	$field_array="id,master_tble_id,form_name,image_location,file_type,real_file_name";
	$rID=sql_insert("common_photo_library",$field_array,$data_array,1);
	if($db_type==0)
	{
		if($rID)
		{
			mysql_query("COMMIT");
			echo "0**".$new_system_id[0]."**".$id_mst;
		}
		else
		{
			mysql_query("ROLLBACK");
			echo "10**".$id_mst;
		}
	}
	else if($db_type==2 || $db_type==1 )
	{
		if($rID)
		{
			oci_commit($con);
			echo "0**".$new_system_id[0]."**".$id_mst;
		}
		else
		{
			oci_rollback($con);
			echo "10**".$id_mst;
		}
	}
	disconnect($con);
	die;
}

// ==================================================================================

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	if ($operation==0)  //Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$txt_lc_number=trim(str_replace("'","",$txt_bank_code)).trim(str_replace("'","",$txt_lc_year)).trim(str_replace("'","",$txt_category)).trim(str_replace("'","",$txt_lc_serial));

		if($txt_lc_number!="")
		{
			if (is_duplicate_field( "lc_number", "com_btb_lc_master_details", "lc_number='$txt_lc_number' and importer_id=$cbo_importer_id and issuing_bank_id=$cbo_issuing_bank and supplier_id=$cbo_supplier_id and status_active=1 and is_deleted=0") == 1)
			{
				echo "11**Duplicate L/C Number"; disconnect($con);die;
			}
			$ref_closing_status = return_field_value("ref_closing_status","com_btb_lc_master_details","status_active=1 and is_deleted=0 and lc_number=$txt_lc_number","ref_closing_status");
			if($ref_closing_status==1){
				echo "50**Reference is closed. No operation is allwoed";disconnect($con);die;
			}
		}

		if(str_replace("'", '',$cbo_lc_currency_id)==1)
		{
			$txt_lc_value=number_format(str_replace("'", '',$txt_lc_value),$dec_place[4],'.','');
			$txt_pi_value=number_format(str_replace("'", '',$txt_pi_value),$dec_place[4],'.','');
		}
		else
		{
			$txt_lc_value=number_format(str_replace("'", '',$txt_lc_value),$dec_place[5],'.','');
			$txt_pi_value=number_format(str_replace("'", '',$txt_pi_value),$dec_place[5],'.','');
		}

		$id=return_next_id( "id", "com_btb_lc_master_details", 1 );

		$tolarence_value = (($txt_tolerance*$txt_lc_value)/100);
		$txt_max_lc_value = $txt_lc_value + $tolarence_value;
		$txt_min_lc_value = $txt_lc_value - $tolarence_value;

		if(str_replace("'","",$cbo_lc_type_id)==1)
			$prefix="BTB";
		else if(str_replace("'","",$cbo_lc_type_id)==2)
			$prefix="MRGN";
		else if(str_replace("'","",$cbo_lc_type_id)==3)
			$prefix="FUND";
		else if(str_replace("'","",$cbo_lc_type_id)==4)
			$prefix="TT";
		else if(str_replace("'","",$cbo_lc_type_id)==5)
			$prefix="FTT";
		else if(str_replace("'","",$cbo_lc_type_id)==6)
			$prefix="FDD";

		if($db_type==0) $year_cond="YEAR(insert_date)";
		else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
		else $year_cond="";//defined Later

		$new_contact_system_id=explode("*",return_mrr_number( str_replace("'","",$cbo_importer_id), '', $prefix, date("Y",time()), 5, "select btb_prefix,btb_prefix_number from com_btb_lc_master_details where importer_id=$cbo_importer_id and lc_type_id=$cbo_lc_type_id and status_active=1 and $year_cond=".date('Y',time())." order by id desc ", "btb_prefix", "btb_prefix_number" ));

		$field_array="id,btb_prefix,btb_prefix_number,btb_system_id,lc_number,bank_code,lc_year,lc_category,lc_serial,supplier_id,importer_id,application_date,lc_date,last_shipment_date,lc_expiry_date,lc_type_id,lc_value,max_lc_value,min_lc_value,currency_id,issuing_bank_id,item_category_id,tenor,tolerance,inco_term_id,inco_term_place,delivery_mode_id,etd_date,insurance_company_name,lca_no,lcaf_no,imp_form_no,psi_company,cover_note_no,cover_note_date,maturity_from_id,margin,origin,bonded_warehouse,item_basis_id,garments_qty,credit_to_be_advised,partial_shipment,transhipment,shipping_mark,doc_presentation_days,port_of_loading,port_of_discharge,remarks,pi_id,pi_value,payterm_id,uom_id,ud_no,ud_date,credit_advice_id,confirming_bank,inserted_by,insert_date,status_active,is_deleted,pi_entry_form,upas_rate,advising_bank,advising_bank_address,lc_reference_no,lc_expiry_days";

		$data_array="(".$id.",'".$new_contact_system_id[1]."',".$new_contact_system_id[2].",'".$new_contact_system_id[0]."','".$txt_lc_number."',".$txt_bank_code.",".$txt_lc_year.",".$txt_category.",".$txt_lc_serial.",".$cbo_supplier_id.",".$cbo_importer_id.",".$application_date.",".$txt_lc_date.",".$txt_last_shipment_date.",".$txt_lc_expiry_date.",".$cbo_lc_type_id.",".$txt_lc_value.",".$txt_max_lc_value.",".$txt_min_lc_value.",".$cbo_lc_currency_id.",".$cbo_issuing_bank.",".$cbo_item_category_id.",".$txt_tenor.",".$txt_tolerance.",".$cbo_inco_term_id.",".$txt_inco_term_place.",".$cbo_delevery_mode.",".$txt_etd_date.",".$txt_insurance_company.",".$txt_lca_no.",".$txt_lcaf_no.",".$txt_imp_form_no.",".$txt_psi_company.",".$txt_cover_note_no.",".$txt_cover_note_date.",".$cbo_maturit_from_id.",".$txt_margin_deposit.",".$cbo_origin_id.",".$cbo_bond_warehouse_id.",".$cbo_lc_basis_id.",".$txt_gmt_qnty.",".$cbo_credit_advice_id.",".$cbo_partial_ship_id.",".$cbo_transhipment_id.",".$txt_shiping_mark.",".$txt_doc_perc_days.",".$txt_port_loading.",".$txt_port_discharge.",".$txt_remarks.",".$txt_hidden_pi_id.",'".$txt_pi_value."',".$cbo_payterm_id.",".$cbo_gmt_uom_id.",".$txt_ud_no.",".$txt_ud_date.",".$cbo_add_confirm_id.",".$txt_conf_bank.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_status.",0,".$txt_hidden_pi_item.",".$txt_upas_rate.",".$cbo_adv_bank.",".$txt_adv_bank_address.",".$txt_lc_reference_no.",".$txt_expiry_days.")";

		$flag=1;
		//----------Insert Data in  com_btb_lc_pi Table----------------------------------------
		if(str_replace("'","",$txt_hidden_pi_id)!="")
		{
			$data_array2="";
			$field_array2="id, com_btb_lc_master_details_id, pi_id, inserted_by, insert_date";
			$tag_pi=explode(',',str_replace("'","",$txt_hidden_pi_id));
			$id_lbtb_lc_pi=return_next_id( "id","com_btb_lc_pi", 1 );
			for($i=0; $i<count($tag_pi); $i++)
			{
				if($i==0) $add_comma=""; else $add_comma=",";
				$data_array2.="$add_comma(".$id_lbtb_lc_pi.",".$id.",".$tag_pi[$i].",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				$id_lbtb_lc_pi++;
			}

			$rID2=sql_insert("com_btb_lc_pi",$field_array2,$data_array2,0);
			if($flag==1)
			{
				if($rID2) $flag=1; else $flag=0;
			}
		}

		// echo "0**"."insert into com_btb_lc_master_details (".$field_array.") values ".$data_array;die;
		$rID=sql_insert("com_btb_lc_master_details",$field_array,$data_array,1);
		if($flag==1)
		{
			if($rID) $flag=1; else $flag=0;
		}
		// echo "10**".$rID;die;
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");
				echo "0**".str_replace("'", '', $id)."**".$new_contact_system_id[0];
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "5**"."0"."**".$new_contact_system_id[0];
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "0**".str_replace("'", '', $id)."**".$new_contact_system_id[0];
			}
			else
			{
				oci_rollback($con);
				echo "5**"."0"."**".$new_contact_system_id[0];
			}
		}
		disconnect($con);
		die;

	}
	else if ($operation==1)   // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$status=str_replace("'","",$cbo_status);
		if($status!=1 && str_replace("'","",$update_id)>0)
		{
			//$inv_sql=sql_select("select import_invoice_id from com_import_invoice_dtls where status_active=1 and is_deleted=0 and btb_lc_id=$update_id");
			$inv_id=return_field_value("import_invoice_id","com_import_invoice_dtls","status_active=1 and is_deleted=0 and btb_lc_id=$update_id","import_invoice_id");
			if($inv_id>0)
			{
				echo "40**Invoice Found, Status Change Not Allow.";disconnect($con);die;
			}

			// $ref_closing_status = return_field_value("ref_closing_status","com_btb_lc_master_details","status_active=1 and is_deleted=0 and lc_number=$txt_lc_number","ref_closing_status");
			// if($ref_closing_status==1){
			// 	echo "50**Reference is closed. No operation is allwoed";die;
			// }
		}
		
		
		$txt_lc_number=trim(str_replace("'","",$txt_bank_code)).trim(str_replace("'","",$txt_lc_year)).trim(str_replace("'","",$txt_category)).trim(str_replace("'","",$txt_lc_serial));
		
		if($txt_lc_number!="")
		{
			if (is_duplicate_field( "lc_number", "com_btb_lc_master_details", "lc_number='$txt_lc_number' and importer_id=$cbo_importer_id and issuing_bank_id=$cbo_issuing_bank and supplier_id=$cbo_supplier_id and id!=$update_id and status_active=1 and is_deleted=0" ) == 1)
			{
				echo "11**Duplicate L/C Number";disconnect($con); die;
			}
		}

		$lc_acceptance=return_field_value("id","com_import_invoice_dtls","btb_lc_id=$update_id and status_active=1 and is_deleted=0","id");
		if($lc_acceptance!="")
		{
			$prev_btb=sql_select("select payterm_id, maturity_from_id from com_btb_lc_master_details where id=$update_id and status_active=1");
			$prev_pay_tarm=$prev_btb[0][csf("payterm_id")];
			$prev_maturity_from=$prev_btb[0][csf("maturity_from_id")];
			$current_pay_tarm=str_replace("'","",$cbo_payterm_id);
			$current_maturity_from=str_replace("'","",$cbo_maturit_from_id);
			if($current_pay_tarm!=$prev_pay_tarm)
			{
				echo "35**Acceptance Found, Pay Term Change Not Allow";disconnect($con);die;
			}

			if($current_maturity_from!=$prev_maturity_from)
			{
				echo "35**Acceptance Found, Maturity From Change Not Allow";disconnect($con);die;
			}

		}


		if(str_replace("'", '',$cbo_lc_currency_id)==1)
		{
			$txt_lc_value=number_format(str_replace("'", '',$txt_lc_value),$dec_place[4],'.','');
			$txt_pi_value=number_format(str_replace("'", '',$txt_pi_value),$dec_place[4],'.','');
		}
		else
		{
			$txt_lc_value=number_format(str_replace("'", '',$txt_lc_value),$dec_place[5],'.','');
			$txt_pi_value=number_format(str_replace("'", '',$txt_pi_value),$dec_place[5],'.','');
		}

		$tolarence_value = (($txt_tolerance*$txt_lc_value)/100);
		$txt_max_lc_value = $txt_lc_value + $tolarence_value;
		$txt_min_lc_value = $txt_lc_value - $tolarence_value;

		$field_array="lc_number*bank_code*lc_year*lc_category*lc_serial*supplier_id*application_date*lc_date*last_shipment_date*lc_expiry_date*lc_value*max_lc_value*min_lc_value*currency_id*issuing_bank_id*item_category_id*tenor*tolerance*inco_term_id*inco_term_place*delivery_mode_id*etd_date*insurance_company_name*lca_no*lcaf_no*imp_form_no*psi_company*cover_note_no*cover_note_date*maturity_from_id*margin*origin*bonded_warehouse*item_basis_id*garments_qty*credit_to_be_advised*partial_shipment*transhipment*shipping_mark*doc_presentation_days*port_of_loading*port_of_discharge*remarks*pi_id*pi_value*payterm_id*uom_id*ud_no*ud_date*credit_advice_id*confirming_bank*updated_by*update_date*status_active*is_deleted*pi_entry_form*upas_rate*advising_bank*advising_bank_address*lc_reference_no*lc_expiry_days";
		$data_array="'".$txt_lc_number."'*".$txt_bank_code."*".$txt_lc_year."*".$txt_category."*".$txt_lc_serial."*".$cbo_supplier_id."*".$application_date."*".$txt_lc_date."*".$txt_last_shipment_date."*".$txt_lc_expiry_date."*".$txt_lc_value."*".$txt_max_lc_value."*".$txt_min_lc_value."*".$cbo_lc_currency_id."*".$cbo_issuing_bank."*".$cbo_item_category_id."*".$txt_tenor."*".$txt_tolerance."*".$cbo_inco_term_id."*".$txt_inco_term_place."*".$cbo_delevery_mode."*".$txt_etd_date."*".$txt_insurance_company."*".$txt_lca_no."*".$txt_lcaf_no."*".$txt_imp_form_no."*".$txt_psi_company."*".$txt_cover_note_no."*".$txt_cover_note_date."*".$cbo_maturit_from_id."*".$txt_margin_deposit."*".$cbo_origin_id."*".$cbo_bond_warehouse_id."*".$cbo_lc_basis_id."*".$txt_gmt_qnty."*".$cbo_credit_advice_id."*".$cbo_partial_ship_id."*".$cbo_transhipment_id."*".$txt_shiping_mark."*".$txt_doc_perc_days."*".$txt_port_loading."*".$txt_port_discharge."*".$txt_remarks."*".$txt_hidden_pi_id."*'".$txt_pi_value."'*".$cbo_payterm_id."*".$cbo_gmt_uom_id."*".$txt_ud_no."*".$txt_ud_date."*".$cbo_add_confirm_id."*".$txt_conf_bank."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_status."*0*".$txt_hidden_pi_item."*".$txt_upas_rate."*".$cbo_adv_bank."*".$txt_adv_bank_address."*".$txt_lc_reference_no."*".$txt_expiry_days."";

		if(str_replace("'","",$txt_hidden_pi_id)!="")
		{
			$data_array2="";
			$tag_pi=explode(',',str_replace("'","",$txt_hidden_pi_id));

			$field_array2="id, com_btb_lc_master_details_id, pi_id, inserted_by, insert_date";
			for($i=0; $i<count($tag_pi); $i++)
			{
				if($id_lbtb_lc_pi=="") {$id_lbtb_lc_pi=return_next_id( "id","com_btb_lc_pi", 1 ); }
				if($i==0) $add_comma=""; else $add_comma=",";
				$data_array2.="$add_comma(".$id_lbtb_lc_pi.",".$update_id.",".$tag_pi[$i].",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				$id_lbtb_lc_pi++;
			}
		}
		
		$rID=$rID2=$delete=true;
		$delete=execute_query( "delete from com_btb_lc_pi where com_btb_lc_master_details_id = $update_id",0);
		$rID=sql_update("com_btb_lc_master_details",$field_array,$data_array,"id",$update_id,0);
		$rID2=sql_insert("com_btb_lc_pi",$field_array2,$data_array2,0);
		
		//echo "6**$rID=$rID2=$delete";oci_rollback($con);disconnect($con);die;

		if($db_type==0)
		{
			if($rID==1 && $rID2==1 && $delete==1)
			{
				mysql_query("COMMIT");
				echo "1**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_system_id);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "6**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_system_id);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID==1 && $rID2==1 && $delete==1)
			{
				oci_commit($con);
				echo "1**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_system_id);
			}
			else
			{
				oci_rollback($con);
				echo "6**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_system_id);
			}
		}
		disconnect($con);
		die;

	}
	else if ($operation==2)   // Delete Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$acceptanc_check=return_field_value( "btb_lc_id","com_import_invoice_dtls","btb_lc_id=$update_id and status_active=1 and is_deleted=0","btb_lc_id" );
		if($acceptanc_check)
		{
			echo "35**Import Document Acceptance Found, Delete Not Allow.";disconnect($con);die;
		}

		$field_array="updated_by*update_date*status_active*is_deleted";
	    $data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";

		$rID=$delete_pi=$delete_ammand=$delete_lc_sc=true;
		$rID=sql_update("com_btb_lc_master_details",$field_array,$data_array,"id","".$update_id."",0);
		$delete_pi=sql_update("com_btb_lc_pi",$field_array,$data_array,"com_btb_lc_master_details_id","".$update_id."",0);
		$delete_ammand=execute_query( "delete from com_btb_lc_amendment where btb_id = $update_id",0);
		$delete_lc_sc=execute_query( "delete from com_btb_export_lc_attachment where import_mst_id = $update_id",0);


		if($db_type==0)
		{
			if($rID && $delete_pi && $delete_ammand && $delete_lc_sc)
			{
				mysql_query("COMMIT");
				echo "2**".$rID;
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "7**".$rID;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $delete_pi && $delete_ammand && $delete_lc_sc)
			{
				oci_commit($con);
				echo "2**".$rID;
			}
			else
			{
				oci_rollback($con);
				echo "7**".$rID;
			}
		}
		disconnect($con);
	}
}

///Save Item Details Table

if ($action=="save_update_delete_dtls")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	if($operation==0 || $operation==1)
	{
		
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$all_lc_id_arr=$all_sc_id_arr=array();
		for($i=1;$i<=$total_row;$i++)
		{
			$txtlcscid="txtLcScid_".$i;
			$txtlcscflag="txtlcscflagId_".$i;
			if(str_replace("'","",$$txtlcscflag)==0)
			{
				$all_lc_id_arr[str_replace("'","",$$txtlcscid)]=str_replace("'","",$$txtlcscid);
			}
			else
			{
				$all_sc_id_arr[str_replace("'","",$$txtlcscid)]=str_replace("'","",$$txtlcscid);
			}
			
		}
		$sql_lc_sc=""; 
		if(count( $all_lc_id_arr)>0)
		{
			$sql_lc_sc="select ID, INTERNAL_FILE_NO, LC_YEAR, 0 as TYPE from com_export_lc where id in(".implode(",",$all_lc_id_arr).")";
			
		}
		if($sql_lc_sc!= "") $sql_lc_sc.="union all";
		if(count( $all_sc_id_arr)>0)
		{
			$sql_lc_sc.="select ID, INTERNAL_FILE_NO, SC_YEAR as LC_YEAR, 1 as TYPE from com_sales_contract where id in(".implode(",",$all_sc_id_arr).")";
		}
		$sql_lc_sc_result=sql_select($sql_lc_sc);
		foreach($sql_lc_sc_result as $val)
		{
			$ls_sc_data[$val["ID"]][$val["TYPE"]]["INTERNAL_FILE_NO"]=$val["INTERNAL_FILE_NO"];
			$ls_sc_data[$val["ID"]][$val["TYPE"]]["LC_YEAR"]=$val["LC_YEAR"];
		}

		$id=return_next_id( "id","com_btb_export_lc_attachment", 1 ) ;
		
		$delete=execute_query( "delete from com_btb_export_lc_attachment where import_mst_id = $update_id",0);
		$field_array="id,import_mst_id,lc_sc_id,is_lc_sc,current_distribution,is_deleted,status_active";
		$file_check=$file_year_check=array();
		for($i=1;$i<=$total_row;$i++)
		{
			$txtlcscid="txtLcScid_".$i;
			$txtlcscflag="txtlcscflagId_".$i;
			$txtcurdistribution="txtcurdistribution_".$i;
			$cbostatus="cbostatus_".$i;
			
			$file_check[$ls_sc_data[str_replace("'","",$$txtlcscid)][str_replace("'","",$$txtlcscflag)]["INTERNAL_FILE_NO"]]=$ls_sc_data[str_replace("'","",$$txtlcscid)][str_replace("'","",$$txtlcscflag)]["INTERNAL_FILE_NO"];
			if(count($file_check)>1)
			{
				echo "40** File Mix Not Allow";oci_rollback($con);disconnect($con);die;
			}
			
			$file_year_check[$ls_sc_data[str_replace("'","",$$txtlcscid)][str_replace("'","",$$txtlcscflag)]["LC_YEAR"]]=$ls_sc_data[str_replace("'","",$$txtlcscid)][str_replace("'","",$$txtlcscflag)]["LC_YEAR"];
			if(count($file_year_check)>1)
			{
				echo "40** File Year Mix Not Allow";oci_rollback($con);disconnect($con);die;
			}
			
			if(str_replace("'","",$$txtlcscid)!='')
			{
				if($data_array!="") $data_array.=",";
				$data_array .="(".$id.",".$update_id.",'".str_replace("'","",$$txtlcscid)."','".str_replace("'","",$$txtlcscflag)."','".str_replace("'","",$$txtcurdistribution)."',0,'".str_replace("'","",$$cbostatus)."')";
				$id=$id+1;
			}
		}

		$rID=sql_insert("com_btb_export_lc_attachment",$field_array,$data_array,1);

		if($operation==0) $msg=5; else $msg=6;

		if($db_type==0)
		{
			if($rID && $delete)
			{
				mysql_query("COMMIT");
				echo $operation."**".$rID;
			}
			else
			{
				mysql_query("ROLLBACK");
				echo $msg."**".$rID;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $delete)
			{
				oci_commit($con);
				echo $operation."**".$rID;
			}
			else
			{
				oci_rollback($con);
				echo $msg."**".$rID;
			}
		}
		disconnect($con);
		die;
	}
}

//--------------------------------------------Start Pi Details List----------------------------------------------------------------//
if( $action == 'show_pi_details_list' )
{
	$data = explode('_',$data);

	$pi_mst_id = $data[0];
	$txt_item_category_id = $data[2];
	$pi_entry_form = $data[1];
	//$pi_mst_id = explode('*',$pi_mst_id);
	$size_library = return_library_array('SELECT id,size_name FROM lib_size','id','size_name');
	$color_library = return_library_array('SELECT id,color_name FROM lib_color','id','color_name');
	?>
    <table class="rpt_table" width="100%" cellspacing="1" rules="all">
    <?
    // echo $pi_entry_form."__".$txt_item_category_id; die;
    switch($pi_entry_form)
	{
		case 165:			//Yarn
			$sql = "SELECT b.pi_number,a.item_category_id, a.id, a.color_id, a.count_name, a.yarn_composition_item1, a.yarn_composition_percentage1, a.yarn_composition_item2, a.yarn_composition_percentage2, a.yarn_type, a.uom, a.quantity, a.amount as gross_amount, a.net_pi_rate as rate, a.net_pi_amount as amount 
			FROM com_pi_item_details a, com_pi_master_details b 
			WHERE b.id = a.pi_id and a.pi_id in($pi_mst_id) and a.amount>0 AND a.status_active = 1 AND a.is_deleted = 0 ORDER BY a.id ASC";
			$data_array=sql_select($sql);

			$yarn_count = return_library_array('SELECT id,yarn_count FROM lib_yarn_count','id','yarn_count');

			?>
            <thead>
                <tr>
                    <th width="9%">PI No</th>
                    <th width="9%"> Item Category</th>
                    <th width="10%">Color</th>
                    <th width="6%">Count</th>
                    <th colspan="4" width="27%">Composition</th>
                    <th width="10%">Yarn Type</th>
                    <th width="5%">UOM</th>
                    <th width="8%">Quantity</th>
                    <th width="6%">Rate</th>
                    <th>Amount</th>
                </tr>
            </thead>
			</table>
			<table class="rpt_table" width="100%" cellspacing="1" rules="all" id="pi_details_list">
				<tbody>
				<?
				$i = 0;
				foreach($data_array as $row)
				{
					$i++;
					if( $i % 2 == 0 ) $bgcolor="#E9F3FF";
					else $bgcolor = "#FFFFFF";//get_php_form_data( theemail.value, "populate_data_from_search_popup", "requires/pi_controller" );
					?>
					<tr bgcolor="<? echo $bgcolor; ?>">
						<td width="9%"><? echo $row[csf('pi_number')]; ?></td>
						<td width="9%"><? echo $item_category[$row[csf('item_category_id')]]; ?></td>
						<td width="10%"><? echo $color_library[$row[csf('color_id')]]; ?></td>
						<td width="6%"><? echo $yarn_count[$row[csf('count_name')]]; ?></td>
						<td width="10%"><? echo $composition[$row[csf('yarn_composition_item1')]]; ?>&nbsp;</td>
						<td width="5%" align="right"><? echo $row[csf('yarn_composition_percentage1')]; ?>%</td>
						<td width="6%"><? echo $composition[$row[csf('yarn_composition_item2')]]; ?>&nbsp;</td>
						<td width="6%" align="right"><? if($row[csf('yarn_composition_percentage2')]!=0) echo $row[csf('yarn_composition_percentage2')]."%"; ?>&nbsp;</td>
						<td width="10%">
							<? if( $row[csf('yarn_type')] != 0 ) echo $yarn_type[$row[csf('yarn_type')]]; ?>&nbsp;
						</td>
						<td width="5%">
							<? if( $row[csf('uom')] != 0 ) echo $unit_of_measurement[$row[csf('uom')]]; ?>
						</td>
						<td width="8%"  align="right"><? echo number_format($row[csf('quantity')],2);  $total_quantity += $row[csf('quantity')];?></td>
						<td width="6%" align="right"><? echo $row[csf('rate')]; ?></td>
						<td align="right"><? echo number_format($row[csf('amount')],4);  $total_ammount += $row[csf('amount')];?></td>
					</tr>
					<?
				}
				?>
				<tr class="tbl_bottom">
					<td colspan="10">Sum</td>
					<td><? echo number_format($total_quantity,2); $total_quantity = 0;?></td>
					<td></td>
					<td><? echo number_format($total_ammount,4); $total_ammount = 0;?></td>
				</tr>
			</tbody>
			<?

			break;
		case 166:		//item category 2,3,13,14
			$data_array=sql_select("SELECT b.pi_number,a.item_category_id, a.color_id, a.fabric_composition, a.fabric_construction, a.dia_width,a.fab_weight, a.weight, a.gsm, a.uom, a.quantity, a.net_pi_rate as rate, a.net_pi_amount as amount FROM com_pi_item_details a, com_pi_master_details b WHERE b.id = a.pi_id and a.pi_id in($pi_mst_id) and a.quantity>0 and a.status_active = 1 AND a.is_deleted = 0 ORDER BY a.id ASC");

			//$color_library = return_library_array('SELECT id,color_name FROM lib_color','id','color_name');
				?>
				<thead>
					<tr>
						<th>PI No</th>
						<th> Item Category</th>
						<th>Construction</th>
						<th>Composition</th>
						<th>Color</th>
						<th><?if($data_array[0][csf('item_category_id')]==2 || $data_array[0][csf('item_category_id')]==13){echo "GSM";}else{echo "Weight";}?></th>
						<th>Width</th>
						<th>UOM</th>
						<th>Quantity</th>
						<th>Rate</th>
						<th>Amount</th>
					</tr>
				</thead>
				<tbody>
				<?


					$i = 0;
					foreach($data_array as $row)
					{
						$i++;
						if( $i % 2 == 0 ) $bgcolor="#E9F3FF";
						else $bgcolor = "#FFFFFF";
					?>
						<tr bgcolor="<? echo $bgcolor; ?>"  height="25">
							<td><? echo $row[csf('pi_number')]; ?></td>
							<td><? echo $item_category[$row[csf('item_category_id')]]; ?></td>
							<td><? echo $row[csf('fabric_construction')]; ?></td>
							<td><? echo $row[csf('fabric_composition')]; ?></td>
							<td><? echo $color_library[$row[csf('color_id')]]; ?></td>
							<td><?if($row[csf('item_category_id')]==2 || $data_array[0][csf('item_category_id')]==13){echo $row[csf('gsm')];}
								elseif($row[csf('item_category_id')]==3){echo $row[csf('fab_weight')];}
								else{echo $row[csf('weight')];} ?></td>
							<td><? echo $row[csf('dia_width')]; ?></td>
							<td><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
							<td align="right"><? echo number_format($row[csf('quantity')],2); $total_quantity += $row[csf('quantity')];?></td>
							<td align="right"><? echo number_format($row[csf('rate')],4); ?></td>
							<td align="right"><? echo number_format($row[csf('amount')],4); $total_ammount += $row[csf('amount')]; ?></td>
						</tr>
					<?
					}
					?>
					<tr class="tbl_bottom" height="25">
						<td colspan = "8" align="right">Sum</td>
						<td><? echo number_format($total_quantity,2); $total_quantity = 0;?></td>
						<td>&nbsp;</td>
						<td><? echo number_format($total_ammount,4); $total_ammount = 0;?></td>
					</tr>
				</tbody>
				<?
				break;
		case 167:			//Accessories
			if($txt_item_category_id==4 || $txt_item_category_id==2 || $txt_item_category_id==3)
			{
				?>
				<thead>
					<tr>
						<th>PI No</th>
						<th> Item Category</th>
						<th>Item Group</th>
						<th>Item Description</th>
						<th>Gmts Color</th>
						<th>Gmts Size</th>
						<th>UOM</th>
						<th>Quantity</th>
						<th>Rate</th>
						<th>Amount</th>
					</tr>
				</thead>
				<tbody>
					<?
					//and b.entry_form = 167
					$data_array=sql_select("SELECT b.pi_number,a.item_category_id, a.item_group, a.item_description, a.color_id, a.size_id, a.uom, a.quantity, a.net_pi_rate as rate, a.net_pi_amount as amount FROM com_pi_item_details a, com_pi_master_details b WHERE b.id = a.pi_id and a.pi_id in($pi_mst_id)   AND a.status_active = 1 AND a.is_deleted = 0 ORDER BY a.id ASC");

					$item_group_library = return_library_array('SELECT id, item_name FROM lib_item_group','id','item_name');
					$i = 0;
					foreach($data_array as $row)
					{
						$i++;
						if( $i % 2 == 0 ) $bgcolor="#E9F3FF";
						else $bgcolor = "#FFFFFF";
					?>
						<tr bgcolor="<? echo $bgcolor; ?>"  height="25" >
							<td><? echo $row[csf('pi_number')]; ?></td>
							<td><? echo $item_category[$row[csf('item_category_id')]]; ?></td>
							<td><? echo $item_group_library[$row[csf('item_group')]]; ?></td>
							<td><? echo $row[csf('item_description')]; ?></td>
							<td><? echo $color_library[$row[csf('color_id')]]; ?></td>
							<td><? echo $size_library[$row[csf('size_id')]]; ?></td>
							<td><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
							<td align="right"><? echo number_format($row[csf('quantity')],2); $total_quantity += $row[csf('quantity')];?></td>
							<td align="right"><? echo number_format($row[csf('rate')],4); ?></td>
							<td align="right"><? echo number_format($row[csf('amount')],4); $total_ammount += $row[csf('amount')]; ?></td>
						</tr>
					<?
					}
					?>
					<tr class="tbl_bottom" height="25">
						<td  colspan = "7" align="right">Sum</td>
						<td><? echo number_format($total_quantity,2); $total_quantity = 0;?></td>
						<td></td>
						<td><? echo number_format($total_ammount,4); $total_ammount = 0;?></td>
					</tr>
			</tbody>
				<?
			}
			else{

				?>
				<thead>
					<tr>
						<th>PI No</th>
						<th> Item Category</th>
						<th>Item Group</th>
						<th>Item Description</th>
						<th>Gmts Color</th>
						<th>Gmts Size</th>
						<th>UOM</th>
						<th>Quantity</th>
						<th>Rate</th>
						<th>Amount</th>
					</tr>
				</thead>
				<tbody>
					<?
					$data_array=sql_select("SELECT b.pi_number,a.item_category_id, a.item_group, a.item_description, a.color_id, a.size_id, a.uom, a.quantity, a.net_pi_rate as rate, a.net_pi_amount as amount FROM com_pi_item_details a, com_pi_master_details b WHERE b.id = a.pi_id and a.pi_id in($pi_mst_id) and b.entry_form = 167  AND a.status_active = 1 AND a.is_deleted = 0 ORDER BY a.id ASC");

					$item_group_library = return_library_array('SELECT id, item_name FROM lib_item_group','id','item_name');
					$i = 0;
					foreach($data_array as $row)
					{
						$i++;
						if( $i % 2 == 0 ) $bgcolor="#E9F3FF";
						else $bgcolor = "#FFFFFF";
					?>
						<tr bgcolor="<? echo $bgcolor; ?>"  height="25" >
							<td><? echo $row[csf('pi_number')]; ?></td>
							<td><? echo $item_category[$row[csf('item_category_id')]]; ?></td>
							<td><? echo $item_group_library[$row[csf('item_group')]]; ?></td>
							<td><? echo $row[csf('item_description')]; ?></td>
							<td><? echo $color_library[$row[csf('color_id')]]; ?></td>
							<td><? echo $size_library[$row[csf('size_id')]]; ?></td>
							<td><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
							<td align="right"><? echo number_format($row[csf('quantity')],2); $total_quantity += $row[csf('quantity')];?></td>
							<td align="right"><? echo number_format($row[csf('rate')],4); ?></td>
							<td align="right"><? echo number_format($row[csf('amount')],4); $total_ammount += $row[csf('amount')]; ?></td>
						</tr>
					<?
					}
					?>
					<tr class="tbl_bottom" height="25">
						<td  colspan = "7" align="right">Sum</td>
						<td><? echo number_format($total_quantity,2); $total_quantity = 0;?></td>
						<td></td>
						<td><? echo number_format($total_ammount,4); $total_ammount = 0;?></td>
					</tr>
			</tbody>
				<?
			}
			break;
		case 168:		//Services
			?>
			<thead>
				<tr>
					<th>PI No</th>
					<th> Item Category</th>
					<th>Service Type</th>
					<th>Item Description</th>
					<th>UOM</th>
					<th>Quantity</th>
					<th>Rate</th>
					<th>Amount</th>
				</tr>
			</thead>
			<tbody>
			<?
			 $data_array=sql_select("SELECT b.pi_number,a.item_category_id,a.id,a.pi_id,a.item_description,a.uom,a.quantity, a.net_pi_rate as rate, a.net_pi_amount as amount, a.service_type,a.status_active FROM com_pi_item_details a, com_pi_master_details b WHERE b.id = a.pi_id and a.pi_id in($pi_mst_id) AND a.status_active = 1 AND a.is_deleted = 0 ORDER BY a.id ASC");

			$i = 0;
			foreach($data_array as $row)
			{
				$i++;
				if( $i % 2 == 0 ) $bgcolor="#E9F3FF";
				else $bgcolor = "#FFFFFF";
			?>
				<tr bgcolor="<? echo $bgcolor; ?>"  height="25" >
					<td><? echo $row[csf('pi_number')]; ?></td>
					<td><? echo $item_category[$row[csf('item_category_id')]]; ?></td>
					<td><? echo $service_type[$row[csf('service_type')]]; ?></td>
					<td><? echo ($row[csf('item_description')]); ?></td>
					<td><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
					<td align="right"><? echo number_format($row[csf('quantity')],2);  $total_quantity += $row[csf('quantity')];?></td>
					<td align="right"><? echo $row[csf('rate')]; ?></td>
					<td align="right"><? echo number_format($row[csf('amount')],4);  $total_ammount += $row[csf('amount')];  ?></td>
				</tr>
			<?
			}
			?>

			<tr class="tbl_bottom">
				<td  colspan = "4" align="right">Sum</td>
				<td><? echo number_format($total_quantity,2); $total_quantity = 0;?></td>
				<td>&nbsp;</td>
				<td><? echo number_format($total_ammount,4); $total_ammount = 0;?></td>
			</tr>
			</tbody>
			<?
			break;
		case 169:
			?>
			<thead>
				<tr>
					<th>PI No</th>
					<th> Item Category</th>
					<th>Lot No</th>
					<th>Count</th>
					<th>Yarn Description</th>
					<th>Color</th>
					<th>Color Range</th>
					<th>UOM</th>
					<th>Quantity</th>
					<th>Rate</th>
					<th>Amount</th>
				</tr>
			</thead>
			<tbody>
			<?
			 $data_array=sql_select("SELECT b.pi_number,a.item_category_id,a.id,a.pi_id,a.item_description,a.uom,a.quantity, a.net_pi_rate as rate, a.net_pi_amount as amount, a.service_type,a.status_active, a.lot_no,a.yarn_color,a.color_range, a.count_name FROM com_pi_item_details a, com_pi_master_details b WHERE b.id = a.pi_id and a.pi_id in($pi_mst_id) AND a.status_active = 1 AND a.is_deleted = 0 ORDER BY a.id ASC");
			 $color_library = return_library_array('SELECT id,color_name FROM lib_color','id','color_name');
			 $count_library = return_library_array('SELECT id,yarn_count FROM lib_yarn_count','id','yarn_count');
			 $i = 0;
			 foreach($data_array as $row)
			 {
				$i++;
				if( $i % 2 == 0 ) $bgcolor="#E9F3FF";
				else $bgcolor = "#FFFFFF";
			 ?>
				<tr bgcolor="<? echo $bgcolor; ?>">
					<td><? echo $row[csf('pi_number')]; ?></td>
					<td><? echo $item_category[$row[csf('item_category_id')]]; ?></td>
					<td><? echo $row[csf('lot_no')]; ?>&nbsp;</td>
					<td><? echo $count_library[$row[csf('count_name')]]; ?>&nbsp;</td>
					<td><? echo $row[csf('item_description')]; ?></td>
					<td><? echo $color_library[$row[csf('yarn_color')]]; ?>&nbsp;</td>
					<td><? echo $color_range[$row[csf('color_range')]]; ?>&nbsp;</td>
					<td><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
					<td align="right"><? echo number_format($row[csf('quantity')],2);  $total_quantity += $row[csf('quantity')];?></td>
					<td align="right"><? echo $row[csf('rate')]; ?></td>
					<td align="right"><? echo number_format($row[csf('amount')],4);  $total_ammount += $row[csf('amount')];  ?></td>
				</tr>
			 <?
			 }
			 ?>

			<tr class="tbl_bottom">
				<td colspan = "7" align="right">Sum</td>
				<td><? echo number_format($total_quantity,2); $total_quantity = 0;?></td>
				<td>&nbsp;</td>
				<td><? echo number_format($total_ammount,4); $total_ammount = 0;?></td>
			</tr>
			</tbody>
			<?
			break;
		case 170:
			if($txt_item_category_id==74)
			{
				?>
                <thead>
                    <tr>
                        <th>PI No</th>
                        <th>Item Category</th>
                        <th>Gmts Color</th>
                        <th>AOP Color</th>
                        <th>GSM</th>
                        <th>Body part</th>
                        <th>UOM</th>
                        <th>Quantity</th>
                        <th>Rate</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                <?
                 $data_array=sql_select("SELECT b.pi_number,a.item_category_id,a.id,a.pi_id,a.item_description,a.uom,a.quantity, a.net_pi_rate as rate, a.net_pi_amount as amount, a.service_type,a.status_active, a.embell_name,a.embell_type,a.color_id, a.item_color, a.gmts_item_id, a.gsm, a.body_part_id FROM com_pi_item_details a, com_pi_master_details b WHERE b.id = a.pi_id and a.pi_id in($pi_mst_id) AND a.status_active = 1 AND a.is_deleted = 0 ORDER BY a.id ASC");
                 $color_library = return_library_array('SELECT id,color_name FROM lib_color','id','color_name');
                 $i = 0;
                 foreach($data_array as $row)
                 {
                    $i++;
                    if( $i % 2 == 0 ) $bgcolor="#E9F3FF";
                    else $bgcolor = "#FFFFFF";

                    $emb_arr=array();
                    if($row[csf('embell_name')]==1) $emb_arr=$emblishment_print_type;
                    else if($row[csf('embell_name')]==2) $emb_arr=$emblishment_embroy_type;
                    else if($row[csf('embell_name')]==3) $emb_arr=$emblishment_wash_type;
                    else if($row[csf('embell_name')]==4) $emb_arr=$emblishment_spwork_type;
                    else $emb_arr=$blank_array;
                 ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" >
                        <td><? echo $row[csf('pi_number')]; ?></td>
                        <td><? echo $item_category[$row[csf('item_category_id')]]; ?></td>
                        <td><? echo $color_library[$row[csf('color_id')]]; ?>&nbsp;</td>
                        <td><? echo $color_library[$row[csf('item_color')]]; ?>&nbsp;</td>
                        <td><? echo $row[csf('gsm')]; ?>&nbsp;</td>
                        <td><? echo $body_part[$row[csf('body_part_id')]]; ?>&nbsp;</td>
                        <td><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
                        <td align="right"><? echo number_format($row[csf('quantity')],2);  $total_quantity += $row[csf('quantity')];?></td>
                        <td align="right"><? echo $row[csf('rate')]; ?></td>
                        <td align="right"><? echo number_format($row[csf('amount')],4);  $total_ammount += $row[csf('amount')];  ?></td>
                    </tr>
                 <?
                 }
                 ?>

                <tr class="tbl_bottom">
                    <td colspan = "7" align="right">Sum</td>
                    <td><? echo number_format($total_quantity,2); $total_quantity = 0;?></td>
                    <td>&nbsp;</td>
                    <td><? echo number_format($total_ammount,4); $total_ammount = 0;?></td>
                </tr>
                </tbody>
                <?
			}
			else
			{
				?>
                <thead>
                    <tr>
                        <th>PI No</th>
                        <th> Item Category</th>
                        <th>Gmts Item</th>
                        <th>Embellishment Name</th>
                        <th>Embellishment Type</th>
                        <th>Gmts Color</th>
                        <th>UOM</th>
                        <th>Quantity</th>
                        <th>Rate</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                <?
                 $data_array=sql_select("SELECT b.pi_number,a.item_category_id,a.id,a.pi_id,a.item_description,a.uom,a.quantity, a.net_pi_rate as rate, a.net_pi_amount as amount, a.service_type,a.status_active, a.embell_name,a.embell_type,a.color_id, a.gmts_item_id FROM com_pi_item_details a, com_pi_master_details b WHERE b.id = a.pi_id and a.pi_id in($pi_mst_id) AND a.status_active = 1 AND a.is_deleted = 0 ORDER BY a.id ASC");
                 $color_library = return_library_array('SELECT id,color_name FROM lib_color','id','color_name');
                 $i = 0;
                 foreach($data_array as $row)
                 {
                    $i++;
                    if( $i % 2 == 0 ) $bgcolor="#E9F3FF";
                    else $bgcolor = "#FFFFFF";
					
					if($row[csf('embell_name')]==1) $wash_type_dtls=$emblishment_print_type;
					else if($row[csf('embell_name')]==2) $wash_type_dtls=$emblishment_embroy_type;
					else if($row[csf('embell_name')]==3) $wash_type_dtls=$emblishment_wash_type;
					else if($row[csf('embell_name')]==4) $wash_type_dtls=$emblishment_spwork_type;
					else if($row[csf('embell_name')]==5) $wash_type_dtls=$emblishment_gmts_type;
                 	?>
                    <tr bgcolor="<? echo $bgcolor; ?>" >
                        <td><? echo $row[csf('pi_number')]; ?></td>
                        <td><? echo $item_category[$row[csf('item_category_id')]]; ?></td>
                        <td><? echo $garments_item[$row[csf('gmts_item_id')]]; ?>&nbsp;</td>
                        <td><? echo $emblishment_name_array[$row[csf('embell_name')]]; ?>&nbsp;</td>
                        <td><? echo $wash_type_dtls[$row[csf('embell_type')]]; ?>&nbsp;</td>
                        <td><? echo $color_library[$row[csf('color_id')]]; ?>&nbsp;</td>
                        <td><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
                        <td align="right"><? echo number_format($row[csf('quantity')],2);  $total_quantity += $row[csf('quantity')];?></td>
                        <td align="right"><? echo $row[csf('rate')]; ?></td>
                        <td align="right"><? echo number_format($row[csf('amount')],4);  $total_ammount += $row[csf('amount')];  ?></td>
                    </tr>
                 	<?
                 }
                 ?>

                <tr class="tbl_bottom">
                    <td colspan = "7" align="right">Sum</td>
                    <td><? echo number_format($total_quantity,2); $total_quantity = 0;?></td>
                    <td>&nbsp;</td>
                    <td><? echo number_format($total_ammount,4); $total_ammount = 0;?></td>
                </tr>
                </tbody>
                <?
			}

			break;
		case 171:
		?>
		<thead>
			<tr>
				<th>PI No</th>
				<th> Item Category</th>
				<th>Test For</th>
				<th>Remarks</th>
				<th>Color</th>
				<th>Test Item</th>
				<th>Amount</th>
			</tr>
		</thead>
		<tbody>
		<?
		 //and b.entry_form = 171
		 $data_array=sql_select("SELECT b.pi_number,a.item_category_id,a.id,a.pi_id,a.item_description,a.uom,a.quantity, a.net_pi_rate as rate, a.net_pi_amount as amount, a.remarks,a.status_active, a.test_for,a.test_item_id,a.color_id, a.gmts_item_id FROM com_pi_item_details a, com_pi_master_details b WHERE b.id = a.pi_id and a.pi_id in($pi_mst_id) AND a.status_active = 1 AND a.is_deleted = 0 ORDER BY a.id ASC");
		 $color_library = return_library_array('SELECT id,color_name FROM lib_color','id','color_name');
		 $test_item_arr=return_library_array( "SELECT id,test_item FROM lib_lab_test_rate_chart",'id','test_item');
		 $i = 0;
		 foreach($data_array as $row)
		 {
			$i++;
			if( $i % 2 == 0 ) $bgcolor="#E9F3FF";
			else $bgcolor = "#FFFFFF";

		 ?>
			<tr bgcolor="<? echo $bgcolor; ?>" >
				<td><? echo $row[csf('pi_number')]; ?></td>

				<td><? echo $item_category[$row[csf('item_category_id')]]; ?></td>
				<td><? echo $test_for[$row[csf('test_for')]]; ?></td>
				<td><? echo $row[csf('remarks')]; ?>&nbsp;</td>
				<td><? echo $color_library[$row[csf('color_id')]]; ?>&nbsp;</td>
				<?
				 $test_item='';
						$test_item_ids=array_unique(explode(",",$row[csf('test_item_id')]));
						foreach($test_item_ids as $test_item_id)
						{
							$test_item.=$test_item_arr[$test_item_id].",";
						}
						$test_item=chop($test_item,',');
						?>
				<td><? echo $test_item; ?>&nbsp;</td>
				<td align="right"><? echo number_format($row[csf('amount')],4);  $total_ammount += $row[csf('amount')];  ?></td>
			</tr>
		 <?
		 }
		 ?>

		<tr class="tbl_bottom">
			<td colspan = "5" align="right">Sum</td>
			<td><? echo number_format($total_ammount,4); $total_ammount = 0;?></td>
		</tr>
		</tbody>
		<?
		break;
		case 227:
		case 172:		//Others
			?>
			<thead>
				<tr>
					<th>PI No</th>
					<th> Item Category</th>
					<th>Item Group</th>
					<th>Item Description</th>
					<th>UOM</th>
					<th>Quantity</th>
					<th>Rate</th>
					<th>Amount</th>
				</tr>
			</thead>
			<tbody>
				<?
				 $data_array=sql_select("SELECT  b.pi_number,a.item_category_id,a.item_group, a.item_description, a.uom, a.quantity, a.net_pi_rate as rate, a.net_pi_amount as amount FROM com_pi_item_details a, com_pi_master_details b WHERE b.id = a.pi_id and a.pi_id in($pi_mst_id)  AND a.status_active = 1 AND a.is_deleted = 0 ORDER BY a.id ASC");

				$item_group_library = return_library_array('SELECT id, item_name FROM lib_item_group','id','item_name');
				$i = 0;
				foreach($data_array as $row)
				{
					$i++;
					if( $i % 2 == 0 ) $bgcolor="#E9F3FF";
					else $bgcolor = "#FFFFFF";
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" height="25" >
						<td><? echo $row[csf('pi_number')]; ?></td>
						<td><? echo $item_category[$row[csf('item_category_id')]]; ?></td>
						<td><? echo $item_group_library[$row[csf('item_group')]]; ?></td>
						<td><? echo $row[csf('item_description')]; ?></td>
						<td><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
						<td  align="right"><? echo number_format($row[csf('quantity')],2); $total_quantity += $row[csf('quantity')];?></td>
						<td  align="right"><? echo number_format($row[csf('rate')],4); ?></td>
						<td  align="right"><? echo number_format($row[csf('amount')],4); $total_ammount += $row[csf('amount')]; ?></td>
					</tr>
				<?
				}
				?>
				<tr class="tbl_bottom" height="25">
					<td  colspan = "5" align="right">Sum</td>
					<td><? echo number_format($total_quantity,2); $total_quantity = 0;?></td>
					<td></td>
					<td><? echo number_format($total_ammount,4); $total_ammount = 0;?></td>
				</tr>
			 </tbody>
			<?
			break;
	}

	switch($cbo_item_category_id_old)
	{
		case 1:			//Yarn
			$sql = "SELECT b.pi_number, a.id, a.color_id, a.count_name, a.yarn_composition_item1, a.yarn_composition_percentage1, a.yarn_composition_item2,a.yarn_composition_percentage2, a.yarn_type,a.uom,a.quantity,a.net_pi_rate as rate, a.net_pi_amount as amount FROM com_pi_item_details a, com_pi_master_details b WHERE b.id = a.pi_id and a.pi_id in($pi_mst_id)  AND a.status_active = 1 AND a.is_deleted = 0 ORDER BY a.id ASC";
			$data_array=sql_select($sql);

			$yarn_count = return_library_array('SELECT id,yarn_count FROM lib_yarn_count','id','yarn_count');

			?>
            <thead>
                <tr>
                    <th>PI No</th>
                    <th>Color</th>
                    <th>Count</th>
                    <th colspan="4">Composition</th>
                    <th>Yarn Type</th>
                    <th>UOM</th>
                    <th>Quantity</th>
                    <th>Rate</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
				<?
				$i = 0;
				foreach($data_array as $row)
				{
					$i++;
					if( $i % 2 == 0 ) $bgcolor="#E9F3FF";
					else $bgcolor = "#FFFFFF";//get_php_form_data( theemail.value, "populate_data_from_search_popup", "requires/pi_controller" );
					?>
					<tr bgcolor="<? echo $bgcolor; ?>">
						<td><? echo $row[csf('pi_number')]; ?></td>
						<td width="100"><? echo $color_library[$row[csf('color_id')]]; ?></td>
						<td width="85"><? echo $yarn_count[$row[csf('count_name')]]; ?></td>
						<td width="90"><? echo $composition[$row[csf('yarn_composition_item1')]]; ?></td>
						<td width="40" align="right"><? echo $row[csf('yarn_composition_percentage1')]; ?>%</td>
						<td width="90"><? echo $composition[$row[csf('yarn_composition_item2')]]; ?>&nbsp;</td>
						<td width="40" align="right"><? if($row[csf('yarn_composition_percentage2')]!=0) echo $row[csf('yarn_composition_percentage2')]."%"; ?>&nbsp;</td>
						<td width="120">
							<? if( $row[csf('yarn_type')] != 0 ) echo $yarn_type[$row[csf('yarn_type')]]; ?>
						</td>
						<td width="60">
							<? if( $row[csf('uom')] != 0 ) echo $unit_of_measurement[$row[csf('uom')]]; ?>
						</td>
						<td width="100"  align="right"><? echo number_format($row[csf('quantity')],2);  $total_quantity += $row[csf('quantity')];?></td>
						<td width="60" align="right"><? echo $row[csf('rate')]; ?></td>
						<td width="110" align="right"><? echo number_format($row[csf('amount')],4);  $total_ammount += $row[csf('amount')];?></td>
					</tr>
					<?
				}
				?>
				<tr class="tbl_bottom">
					<td colspan="9">Sum</td>
					<td><? echo number_format($total_quantity,2); $total_quantity = 0;?></td>
					<td></td>
					<td><? echo number_format($total_ammount,4); $total_ammount = 0;?></td>
				</tr>
			</tbody>
			<?

			break;
		case 2:			//Knit Fabric
		case 13:		//grey fabric Knit Fabric
		case 110:		//Knit Fabric Import
			?>
			<thead>
				<tr>
					<th>PI No</th>
					<th>Construction</th>
					<th>Composition</th>
					<th>Color</th>
					<th>GSM</th>
					<th>Dia/Width</th>
					<th>UOM</th>
					<th>Quantity</th>
					<th>Rate</th>
					<th>Amount</th>
				</tr>
			</thead>
			<tbody>
				<?
				$data_array=sql_select("SELECT b.pi_number, a.fabric_composition, a.fabric_construction, a.color_id, a.gsm,a.dia_width, a.dia_width, a.uom, a.quantity, a.net_pi_rate as rate, a.net_pi_amount as amount FROM com_pi_item_details a, com_pi_master_details b WHERE b.id = a.pi_id and a.pi_id in($pi_mst_id) and a.quantity>0 AND a.status_active = 1 AND a.is_deleted = 0 ORDER BY a.id ASC");
				$i = 0;
				foreach($data_array as $row)
				{
					$i++;
					if( $i % 2 == 0 ) $bgcolor="#E9F3FF";
					else $bgcolor = "#FFFFFF";//get_php_form_data( theemail.value, "populate_data_from_search_popup", "requires/pi_controller" );
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" height="25">
						<td><? echo $row[csf('pi_number')]; ?></td>
						<td><? echo $row[csf('fabric_construction')]; ?></td>
						<td><? echo $row[csf('fabric_composition')]; ?></td>
						<td><? echo $color_library[$row[csf('color_id')]]; ?></td>
						<td><? echo $row[csf('gsm')]; ?></td>
						<td><? echo $row[csf('dia_width')]; ?></td>
						<td><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
						<td align="right"><? echo number_format($row[csf('quantity')],2); $total_quantity += $row[csf('quantity')]; ?></td>
						<td align="right"><? echo number_format($row[csf('rate')],2); ?></td>
						<td align="right"><? echo number_format($row[csf('amount')],4); $total_ammount += $row[csf('amount')]; ?></td>
					</tr>
				<?
				}
				?>
				<tr class="tbl_bottom">
					<td colspan="7">Sum</td>
					<td><? echo number_format($total_quantity,2); $total_quantity = 0;?></td>
					<td>&nbsp;</td>
					<td><? echo number_format($total_ammount,4); $total_ammount = 0;?></td>
				</tr>
			</tbody>
			 <?
			break;
			case 3:			//Woven Fabric
			case 14:		//Grey Fabric Woven
				?>
				<thead>
					<tr>
						<th>PI No</th>
						<th>Construction</th>
						<th>Composition</th>
						<th>Color</th>
						<th>Weight</th>
						<th>Width</th>
						<th>UOM</th>
						<th>Quantity</th>
						<th>Rate</th>
						<th>Amount</th>
					</tr>
				</thead>
				<tbody>
				<?
					$data_array=sql_select("SELECT b.pi_number, a.color_id, a.fabric_composition, a.fabric_construction, a.dia_width, a.weight, a.uom, a.quantity, a.net_pi_rate as rate, a.net_pi_amount as amount FROM com_pi_item_details a, com_pi_master_details b WHERE b.id = a.pi_id and a.pi_id in($pi_mst_id) and a.quantity>0 and a.status_active = 1 AND a.is_deleted = 0 ORDER BY a.id ASC");

					//$color_library = return_library_array('SELECT id,color_name FROM lib_color','id','color_name');

					$i = 0;
					foreach($data_array as $row)
					{
						$i++;
						if( $i % 2 == 0 ) $bgcolor="#E9F3FF";
						else $bgcolor = "#FFFFFF";
					?>
						<tr bgcolor="<? echo $bgcolor; ?>"  height="25">
							<td><? echo $row[csf('pi_number')]; ?></td>
							<td><? echo $row[csf('fabric_construction')]; ?></td>
							<td><? echo $row[csf('fabric_composition')]; ?></td>
							<td><? echo $color_library[$row[csf('color_id')]]; ?></td>
							<td><? echo $row[csf('weight')]; ?></td>
							<td><? echo $row[csf('dia_width')]; ?></td>
							<td><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
							<td align="right"><? echo number_format($row[csf('quantity')],2); $total_quantity += $row[csf('quantity')];?></td>
							<td align="right"><? echo number_format($row[csf('rate')],4); ?></td>
							<td align="right"><? echo number_format($row[csf('amount')],4); $total_ammount += $row[csf('amount')]; ?></td>
						</tr>
					<?
					}
					?>
					<tr class="tbl_bottom" height="25">
						<td colspan = "7" align="right">Sum</td>
						<td><? echo number_format($total_quantity,2); $total_quantity = 0;?></td>
						<td>&nbsp;</td>
						<td><? echo number_format($total_ammount,4); $total_ammount = 0;?></td>
					</tr>
				</tbody>
				<?
				break;
		case 8:			//Spare Parts
		case 9:			//Machinaries
		case 10:		//Other Capital Items
		case 11:		//Stationaries
		case 33:		//Others
		?>
		<thead>
			<tr>
				<th>PI No</th>
				<th>Item Group</th>
				<th>Item Description</th>
				<th>UOM</th>
				<th>Quantity</th>
				<th>Rate</th>
				<th>Amount</th>
			</tr>
		</thead>
		<tbody>
			<?
			 $data_array=sql_select("SELECT b.pi_number, a.item_group, a.item_description, a.uom, a.quantity, a.net_pi_rate as rate, a.net_pi_amount as amount FROM com_pi_item_details a, com_pi_master_details b WHERE b.id = a.pi_id and a.pi_id in($pi_mst_id)  AND a.status_active = 1 AND a.is_deleted = 0 ORDER BY a.id ASC");

			$item_group_library = return_library_array('SELECT id, item_name FROM lib_item_group','id','item_name');
			$i = 0;
			foreach($data_array as $row)
			{
				$i++;
				if( $i % 2 == 0 ) $bgcolor="#E9F3FF";
				else $bgcolor = "#FFFFFF";
			?>
				<tr bgcolor="<? echo $bgcolor; ?>" height="25" >
					<td><? echo $row[csf('pi_number')]; ?></td>
					<td><? echo $item_group_library[$row[csf('item_group')]]; ?></td>
					<td><? echo $row[csf('item_description')]; ?></td>
					<td><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
					<td  align="right"><? echo number_format($row[csf('quantity')],2); $total_quantity += $row[csf('quantity')];?></td>
					<td  align="right"><? echo number_format($row[csf('rate')],4); ?></td>
					<td  align="right"><? echo number_format($row[csf('amount')],4); $total_ammount += $row[csf('amount')]; ?></td>
				</tr>
			<?
			}
			?>
			<tr class="tbl_bottom" height="25">
				<td  colspan = "4" align="right">Sum</td>
				<td><? echo number_format($total_quantity,2); $total_quantity = 0;?></td>
				<td></td>
				<td><? echo number_format($total_ammount,4); $total_ammount = 0;?></td>
			</tr>
		 </tbody>
		<?
		break;
		case 4:			//Accessories
			?>
			<thead>
				<tr>
					<th>PI No</th>
					<th>Item Group</th>
					<th>Item Description</th>
					<th>Gmts Color</th>
					<th>Gmts Size</th>
					<th>UOM</th>
					<th>Quantity</th>
					<th>Rate</th>
					<th>Amount</th>
				</tr>
			</thead>
			<tbody>
				<?
				 $data_array=sql_select("SELECT b.pi_number, a.item_group, a.item_description, a.color_id, a.size_id, a.uom, a.quantity, a.net_pi_rate as rate, a.net_pi_amount as amount FROM com_pi_item_details a, com_pi_master_details b WHERE b.id = a.pi_id and a.pi_id in($pi_mst_id)  AND a.status_active = 1 AND a.is_deleted = 0 ORDER BY a.id ASC");

				$item_group_library = return_library_array('SELECT id, item_name FROM lib_item_group','id','item_name');
				$i = 0;
				foreach($data_array as $row)
				{
					$i++;
					if( $i % 2 == 0 ) $bgcolor="#E9F3FF";
					else $bgcolor = "#FFFFFF";
				?>
					<tr bgcolor="<? echo $bgcolor; ?>"  height="25" >
						<td><? echo $row[csf('pi_number')]; ?></td>
						<td><? echo $item_group_library[$row[csf('item_group')]]; ?></td>
						<td><? echo $row[csf('item_description')]; ?></td>
						<td><? echo $color_library[$row[csf('color_id')]]; ?></td>
						<td><? echo $size_library[$row[csf('size_id')]]; ?></td>
						<td><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
						<td align="right"><? echo number_format($row[csf('quantity')],2); $total_quantity += $row[csf('quantity')];?></td>
						<td align="right"><? echo number_format($row[csf('rate')],4); ?></td>
						<td align="right"><? echo number_format($row[csf('amount')],4); $total_ammount += $row[csf('amount')]; ?></td>
					</tr>
				<?
				}
				?>
				<tr class="tbl_bottom" height="25">
					<td  colspan = "6" align="right">Sum</td>
					<td><? echo number_format($total_quantity,2); $total_quantity = 0;?></td>
					<td></td>
					<td><? echo number_format($total_ammount,4); $total_ammount = 0;?></td>
				</tr>
		 </tbody>
		<?
			break;
		case 5:			//Chemicals
		case 6:			//Dyes
		case 7:			//Auxilary Chemicals
		case 15:
		case 16:
		case 17:
		case 18:
		case 19:
		case 20:
		case 21:
		case 22:
		case 23:
			?>
			<thead>
				<tr>
					<th>PI No</th>
					<th>Item Group</th>
					<th>Item Description</th>
					<th>UOM</th>
					<th>Quantity</th>
					<th>Rate</th>
					<th>Amount</th>
				</tr>
			</thead>
			<tbody>
			<?
				$data_array=sql_select("SELECT b.pi_number ,a.item_group, a.item_description, a.uom, a.quantity, a.net_pi_rate as rate, a.net_pi_amount as amount FROM com_pi_item_details a, com_pi_master_details b WHERE b.id = a.pi_id and a.pi_id in($pi_mst_id) AND a.status_active = 1 AND a.is_deleted = 0 ORDER BY a.id ASC");

				$item_group_library = return_library_array('SELECT id, item_name FROM lib_item_group','id','item_name');

				$i = 0;
				foreach($data_array as $row)
				{
					$i++;
					if( $i % 2 == 0 ) $bgcolor="#E9F3FF";
					else $bgcolor = "#FFFFFF";
				?>
					<tr bgcolor="<? echo $bgcolor; ?>">
						<td><? echo $row[csf('pi_number')]; ?></td>
						<td><? echo $item_group_library[$row[csf('item_group')]]; ?>&nbsp;</td>
						<td><? echo $row[csf('item_description')]; ?>&nbsp;</td>
						<td><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
						<td align="right"><? echo number_format($row[csf('quantity')],2);  $total_quantity += $row[csf('quantity')];?></td>
						<td align="right"><? echo $row[csf('rate')]; ?></td>
						<td align="right"><? echo number_format($row[csf('amount')],4);  $total_ammount += $row[csf('amount')]; ?></td>
					</tr>
				<?
				}
				?>
				<tr class="tbl_bottom" height="25">
					<td  colspan = "4" align="right">Sum</td>
					<td><? echo number_format($total_quantity,2); $total_quantity = 0;?></td>
					<td></td>
					<td><? echo number_format($total_ammount,4); $total_ammount = 0;?></td>
				</tr>
		   </tbody>


			<?
			break;
		case 12:		//Services
			?>
			<thead>
				<tr>
					<th>PI No</th>
					<th>Service Type</th>
					<th>Item Description</th>
					<th>UOM</th>
					<th>Quantity</th>
					<th>Rate</th>
					<th>Amount</th>
				</tr>
			</thead>
			<tbody>
			<?
			 $data_array=sql_select("SELECT b.pi_number,a.id,a.pi_id,a.item_description,a.uom,a.quantity, a.net_pi_rate as rate, a.net_pi_amount as amount, a.service_type,a.status_active FROM com_pi_item_details a, com_pi_master_details b WHERE b.id = a.pi_id and a.pi_id in($pi_mst_id) AND a.status_active = 1 AND a.is_deleted = 0 ORDER BY a.id ASC");

			$i = 0;
			foreach($data_array as $row)
			{
				$i++;
				if( $i % 2 == 0 ) $bgcolor="#E9F3FF";
				else $bgcolor = "#FFFFFF";
			?>
				<tr bgcolor="<? echo $bgcolor; ?>"  height="25" >
					<td><? echo $row[csf('pi_number')]; ?></td>
					<td><? echo $service_type[$row[csf('service_type')]]; ?></td>
					<td><? echo ($row[csf('item_description')]); ?></td>
					<td><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
					<td align="right"><? echo number_format($row[csf('quantity')],2);  $total_quantity += $row[csf('quantity')];?></td>
					<td align="right"><? echo $row[csf('rate')]; ?></td>
					<td align="right"><? echo number_format($row[csf('amount')],4);  $total_ammount += $row[csf('amount')];  ?></td>
				</tr>
			<?
			}
			?>

			<tr class="tbl_bottom">
				<td  colspan = "4" align="right">Sum</td>
				<td><? echo number_format($total_quantity,2); $total_quantity = 0;?></td>
				<td>&nbsp;</td>
				<td><? echo number_format($total_ammount,4); $total_ammount = 0;?></td>
			</tr>
		</tbody>
		<?
		break;
		case 24:
			?>
			<thead>
				<tr>
					<th>PI No</th>
					<th>Lot No</th>
					<th>Count</th>
					<th>Yarn Description</th>
					<th>Color</th>
					<th>Color Range</th>
					<th>UOM</th>
					<th>Quantity</th>
					<th>Rate</th>
					<th>Amount</th>
				</tr>
			</thead>
			<tbody>
			<?
			 $data_array=sql_select("SELECT b.pi_number,a.id,a.pi_id,a.item_description,a.uom,a.quantity, a.net_pi_rate as rate, a.net_pi_amount as amount, a.service_type,a.status_active, a.lot_no,a.yarn_color,a.color_range, a.count_name FROM com_pi_item_details a, com_pi_master_details b WHERE b.id = a.pi_id and a.pi_id in($pi_mst_id) AND a.status_active = 1 AND a.is_deleted = 0 ORDER BY a.id ASC");
			 $color_library = return_library_array('SELECT id,color_name FROM lib_color','id','color_name');
			 $count_library = return_library_array('SELECT id,yarn_count FROM lib_yarn_count','id','yarn_count');
			 $i = 0;
			 foreach($data_array as $row)
			 {
				$i++;
				if( $i % 2 == 0 ) $bgcolor="#E9F3FF";
				else $bgcolor = "#FFFFFF";
			 ?>
				<tr bgcolor="<? echo $bgcolor; ?>">
					<td><? echo $row[csf('pi_number')]; ?></td>
					<td><? echo $row[csf('lot_no')]; ?>&nbsp;</td>
					<td><? echo $count_library[$row[csf('count_name')]]; ?>&nbsp;</td>
					<td><? echo $row[csf('item_description')]; ?></td>
					<td><? echo $color_library[$row[csf('yarn_color')]]; ?>&nbsp;</td>
					<td><? echo $color_range[$row[csf('color_range')]]; ?>&nbsp;</td>
					<td><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
					<td align="right"><? echo number_format($row[csf('quantity')],2);  $total_quantity += $row[csf('quantity')];?></td>
					<td align="right"><? echo $row[csf('rate')]; ?></td>
					<td align="right"><? echo number_format($row[csf('amount')],4);  $total_ammount += $row[csf('amount')];  ?></td>
				</tr>
			 <?
			 }
			 ?>

            <tr class="tbl_bottom">
                <td colspan = "7" align="right">Sum</td>
                <td><? echo number_format($total_quantity,2); $total_quantity = 0;?></td>
                <td>&nbsp;</td>
                <td><? echo number_format($total_ammount,4); $total_ammount = 0;?></td>
            </tr>
            </tbody>
            <?
            break;
		case 25:
			?>
			<thead>
				<tr>
					<th>PI No</th>
					<th>Gmts Item</th>
					<th>Embellishment Name</th>
					<th>Embellishment Type</th>
					<th>Gmts Color</th>
					<th>UOM</th>
					<th>Quantity</th>
					<th>Rate</th>
					<th>Amount</th>
				</tr>
			</thead>
			<tbody>
			<?
			 $data_array=sql_select("SELECT b.pi_number,a.id,a.pi_id,a.item_description,a.uom,a.quantity, a.net_pi_rate as rate, a.net_pi_amount as amount, a.service_type,a.status_active, a.embell_name,a.embell_type,a.color_id, a.gmts_item_id FROM com_pi_item_details a, com_pi_master_details b WHERE b.id = a.pi_id and a.pi_id in($pi_mst_id) AND a.status_active = 1 AND a.is_deleted = 0 ORDER BY a.id ASC");
			 $color_library = return_library_array('SELECT id,color_name FROM lib_color','id','color_name');
			 $i = 0;
			 foreach($data_array as $row)
			 {
				$i++;
				if( $i % 2 == 0 ) $bgcolor="#E9F3FF";
				else $bgcolor = "#FFFFFF";

				$emb_arr=array();
				if($row[csf('embell_name')]==1) $emb_arr=$emblishment_print_type;
				else if($row[csf('embell_name')]==2) $emb_arr=$emblishment_embroy_type;
				else if($row[csf('embell_name')]==3) $emb_arr=$emblishment_wash_type;
				else if($row[csf('embell_name')]==4) $emb_arr=$emblishment_spwork_type;
				else $emb_arr=$blank_array;
			 ?>
				<tr bgcolor="<? echo $bgcolor; ?>" >
					<td><? echo $row[csf('pi_number')]; ?></td>
					<td><? echo $garments_item[$row[csf('gmts_item_id')]]; ?>&nbsp;</td>
					<td><? echo $emblishment_name_array[$row[csf('embell_name')]]; ?>&nbsp;</td>
					<td><? echo $emb_arr[$row[csf('embell_type')]]; ?>&nbsp;</td>
					<td><? echo $color_library[$row[csf('color_id')]]; ?>&nbsp;</td>
					<td><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
					<td align="right"><? echo number_format($row[csf('quantity')],2);  $total_quantity += $row[csf('quantity')];?></td>
					<td align="right"><? echo $row[csf('rate')]; ?></td>
					<td align="right"><? echo number_format($row[csf('amount')],4);  $total_ammount += $row[csf('amount')];  ?></td>
				</tr>
			 <?
			 }
			 ?>

			<tr class="tbl_bottom">
				<td colspan = "6" align="right">Sum</td>
				<td><? echo number_format($total_quantity,2); $total_quantity = 0;?></td>
				<td>&nbsp;</td>
				<td><? echo number_format($total_ammount,4); $total_ammount = 0;?></td>
			</tr>
            </tbody>
            <?
            break;
	}
	?>
	</table>
	<?
	exit();
}

//-------------------------------------------End Pi Details List---------------------------------------------------------------------//


if ($action=="set_value_pi_select")
{
	//$pi_value = return_field_value("sum(net_pi_amount)","com_pi_item_details","pi_id in($data) and status_active=1 and is_deleted=0");
	$pi_value = return_field_value("sum(net_total_amount)","com_pi_master_details","id in($data) and status_active=1 and is_deleted=0");
	$nameArray=sql_select( "select id, pi_number, supplier_id, last_shipment_date, currency_id, import_pi, pi_for  from com_pi_master_details where id in($data)" );

	//echo "select id,pi_number,supplier_id,last_shipment_date,currency_id,import_pi from com_pi_master_details where id in($data)";
	foreach ($nameArray as $inf)
	{
		if($inf[csf('import_pi')] == 1)
		{
			echo "load_drop_down( 'requires/btb_margin_lc_controller',".$inf[csf('supplier_id')].", 'load_drop_down_importer', 'supplier_td');\n";
		}

		if($inf[csf("currency_id")]==1)
			$txt_pi_value=number_format($pi_value,$dec_place[4],'.','');
		else
			$txt_pi_value=number_format($pi_value,$dec_place[5],'.','');

		echo "document.getElementById('cbo_supplier_id').value = '".$inf[csf("supplier_id")]."';\n";
		echo "document.getElementById('txt_last_shipment_date').value = '".change_date_format($inf[csf("last_shipment_date")])."';\n";
		echo "document.getElementById('txt_pi_value').value = '".$txt_pi_value."';\n";
		echo "document.getElementById('txt_lc_value').value = '".$txt_pi_value."';\n";
		echo "document.getElementById('cbo_pi_currency_id').value = '".$inf[csf("currency_id")]."';\n";
		echo "document.getElementById('cbo_lc_currency_id').value = '".$inf[csf("currency_id")]."';\n";
		echo "document.getElementById('cbo_supplier_id').disabled=true\n";
		echo "document.getElementById('txt_pi_value').disabled=true\n";
		echo "document.getElementById('cbo_pi_currency_id').disabled=true\n";
		echo "document.getElementById('cbo_lc_currency_id').disabled=true\n";
		if($inf[csf("pi_for")]!=0 && $inf[csf("pi_for")]!='' )
		{
			echo "document.getElementById('cbo_lc_type_id').value = '".$inf[csf("pi_for")]."';\n";
		}
	}
	exit();
}

if ($action=="load_drop_down_importer")
{
	echo create_drop_down( "cbo_supplier_id", 165,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.id = '$data' order by comp.company_name",'id,company_name', 1, '----Select----',0,0,0);
}

if ($action=="pi_popup")
{
	echo load_html_head_contents("PI Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//$attach_approval_pi=return_field_value( "attach_approval_pi","variable_settings_commercial","company_name='$cbo_importer_id' and variable_list=21","attach_approval_pi" );
	if($db_type==0)
	{
		$approval_status="select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$cbo_importer_id' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format(date('d-m-Y'),'yyyy-mm-dd')."' and company_id='$cbo_importer_id')) and page_id=18 and status_active=1 and is_deleted=0";
	}
	else
	{
		$approval_status="select approval_need, allow_partial from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$cbo_importer_id' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format(date('d-m-Y'), "", "",1)."' and company_id='$cbo_importer_id')) and page_id=18 and status_active=1 and is_deleted=0";
	}
	$approval_status_result=sql_select($approval_status);
	if($approval_status_result[0][csf("approval_need")]==1) $app_need_setup=$approval_status_result[0][csf("approval_need")]; else $app_need_setup=0;
	$pi_allow_partial=$approval_status_result[0][csf("allow_partial")];
	//echo $app_need_setup."=".$pi_allow_partial;die;
	?>

    <script>

	 var btb_id='<? echo $btb_id; ?>';

	 var attach_approval_pi='<? echo $app_need_setup; ?>';
	 var pi_allow_partial='<? echo $pi_allow_partial; ?>';
	 var payTerm="";
	 var tenor="";
	 var selected_id = new Array(); selected_name = new Array();
	 var supplier_id_arr_chk = new Array; var entry_form_arr = new Array;var currency_arr = new Array;

		function check_all_data(is_checked)
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value( i );
			}
		}

		function toggle( x, origColor )
		{
			var newColor = 'yellow';
			if ( x.style )
			{
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function set_all()
		{
			var old=document.getElementById('txt_pi_row_id').value;
			if(old!="")
			{
				old=old.split(",");
				for(var i=0; i<old.length; i++)
				{
					js_set_value( old[i] )
				}
			}
		}

		function js_set_value( str)
		{
			var refClosingStatus=$('#refClosingStatus_' + str).val();
			if(refClosingStatus==1)
			{
				alert("This PI Already Closed");return;
			}

			if(btb_id!="")
			{
				var data=$('#txt_individual_id' + str).val()+"**"+btb_id;
				if(document.getElementById('search' + str).style.backgroundColor=='yellow')
				{
					var pi_no=$('#search' + str).find("td:eq(1)").text();
					var response = return_global_ajax_value( data, 'check_used_or_not', '', 'btb_margin_lc_controller');
					response=response.split("**");
					if(response[0]==1)
					{
						alert("Bellow Invoice Found Against PI- "+pi_no+". So You can't Detach it.\n Invoice No: "+response[1]);
						return false;
					}
				}
			}

			//=========Supplier and Entry Form Mixing validation Start==========
			var any_selected = $('#txt_selected_id').val();
			if(any_selected=="")
			{
				supplier_id_arr_chk = [];
				entry_form_arr = [];
				selected_id = [];
				selected_name =[];
			}
			//alert(supplier_id_arr_chk+"="+selected_id);
			var supplier_id = $('#supplierChk_' + str).val();
			if(supplier_id_arr_chk.length==0)
			{
				supplier_id_arr_chk.push( supplier_id );
			}
			else if( jQuery.inArray( supplier_id, supplier_id_arr_chk )==-1 &&  supplier_id_arr_chk.length>0)
			{
				alert("Supplier Mixed is Not Allowed");
				return;
			}
			
			var currencyId = $('#currencyId_' + str).val();
			if(currency_arr.length==0)
			{
				currency_arr.push( currencyId );
			}
			else if( jQuery.inArray( currencyId, currency_arr )==-1 &&  currency_arr.length>0)
			{
				alert("Currency Mixed is Not Allowed");
				return;
			}

			var entry_form = $('#entryForm_' + str).val();
			var item_category_id = $('#itemCategory_' + str).val();

			if(payTerm=="") payTerm = $('#payTerm_' + str).val();
			if(tenor=="") tenor = $('#tenor_' + str).val();

			/*
			//###### according to issue id 190 category mix allow #####//
			if(entry_form_arr.length==0)
			{
				entry_form_arr.push( entry_form );
			}
			else if( jQuery.inArray( entry_form, entry_form_arr )==-1 &&  entry_form_arr.length>0)
			{
				alert("Entry Form Mixed is Not Allowed");
				return;
			}*/


			//===================End ========================


			if( attach_approval_pi == 1 )
			{
				var approvalStatus=$('#approvalStatus_' + str).val();
				if(pi_allow_partial==1)
				{
					if(approvalStatus!=1 && approvalStatus!=3)
					{
						alert("Please PI Approve First.");return;
					}
				}
				else
				{
					if(approvalStatus!=1)
					{
						alert("Please PI Approve First.");return;
					}
				}
			}

			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );

			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual_id' + str).val() );
				selected_name.push( $('#txt_individual' + str).val() );

			}
			else
			{
				for( var i = 0; i < selected_id.length; i++ )
				{
					if( selected_id[i] == $('#txt_individual_id' + str).val() )
					break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}
			var id =''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );

			$('#txt_selected_id').val( id );
			$('#txt_selected').val( name );


			if(id=="")
			{
				$('#txt_pi_entry_form').val('');
				$('#txt_item_category').val('');
				$('#payTerm').val('');
				$('#tenor').val('');

			}else{
				$('#txt_pi_entry_form').val(entry_form);
				$('#txt_item_category').val(item_category_id);
				$('#payTerm').val(payTerm);
				$('#tenor').val(tenor);
			}



		}

		function reset_hide_field(type)
		{
			$('#txt_selected_id').val( '' );
			$('#txt_selected').val( '' );
			if(type==1)
			{
				$('#search_div').html( '' );
			}
		}

		function openmypage_file(i)
		{
			var mst_id=$('#txt_individual_id'+i).val();
			var page_link='btb_margin_lc_controller.php?action=show_file&mst_id='+mst_id;
			var title="Image View";
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=640px,height=330px,center=1,resize=0,scrolling=0','../')
		}
		
		function open_print_btn_popup(data)
		{
			//alert(data);
			var title = 'Show Print Options';
			var page_link = 'btb_margin_lc_controller.php?action=print_button_variable&print_data='+data;
			emailwindow=dhtmlmodal.open('ShowPrint', 'iframe', page_link, title, 'width=650px,height=100px,center=1,resize=1,scrolling=0','');
			emailwindow.onclose=function()
			{
				
			}
		}

    </script>

	</head>

	<body>
	<div style="width:1280px;" >
		<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
			<fieldset style="width:1220px; margin-left: 40px">
				<table style="margin-top:10px" width="1210" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all">
					<thead>
						<th>Importer</th>
						<th>Category</th>
						<th>Supplier</th>
						<th>PI System ID</th>
						<th>PI Number</th>
						<th>Internal File No</th>
						<th>LC/SC No</th>
						<th>Approval Type</th>
						<th>Date Range</th>
						<th><input type="reset" name="button" class="formbutton" value="Reset" onClick="reset_hide_field(1)" style="width:70px;"></th>

						<input type="hidden" name="txt_selected_id" id="txt_selected_id" value="" />
						<input type="hidden" name="txt_selected"  id="txt_selected" value="" />
						<input type="hidden" name="txt_pi_entry_form"  id="txt_pi_entry_form" value="" />
						<input type="hidden" name="txt_item_category"  id="txt_item_category" value="" />
						<input type="hidden" name="payTerm"  id="payTerm" value="" />
						<input type="hidden" name="tenor"  id="tenor" value="" />
					</thead>
					<tr class="general">
						<td align="center">
							<?
								echo create_drop_down( "cbo_company_id", 140,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name",'id,company_name', 1, '----Select----',$cbo_importer_id,"",1);
							?>
						</td>
						<td align="center">
							<?
							if (!empty($item_category_id)) {
								$disabled=1;
							}else{
								$disabled=0;
							}
								echo create_drop_down( "cbo_item_category", 140, $item_category_with_gen,'', 1, '----Select----',$item_category_id,"load_drop_down( 'btb_margin_lc_controller',document.getElementById('cbo_company_id').value+'_'+this.value, 'load_supplier_dropdown', 'supplier_td' );",$disabled,'','','','74,72,79,73,71,77,78,75,76');
							
							?>
						</td>
						<td align="center" id="supplier_td">
							<? echo create_drop_down( "cbo_supplier_id", 165,$blank_array,'', 1, '----Select----',0,0,0); ?>
						</td>
						<td id="search_by_td">
						<input type="text" style="width:90px" class="text_boxes"  name="txt_search_system" id="txt_search_system" />
						
						</td>
						<td align="center">
						<input type="text" name="txt_pi_no" id="txt_pi_no" class="text_boxes" style="width:100px">
						</td>
						<td align="center">
						<input type="text" name="txt_internal_file_no" id="txt_internal_file_no" class="text_boxes" style="width:100px">
						</td>
						<td align="center">
						<input type="text" name="txt_lc_sc_no" id="txt_lc_sc_no" class="text_boxes" style="width:100px">
						</td>
						<td align="center">
						<?
						echo create_drop_down( "cbo_approval_type", 70, $yes_no,"", 1, "-- Select--", $app_need_setup, "","","" );
						?>
						</td>
						<td align="center">
						<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px">To
						<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px">
						</td>
						<td align="center">
						<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_pi_no').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_item_category').value+'_'+document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_supplier_id').value+'_'+'<? echo $txt_hidden_pi_id; ?>'+'_'+document.getElementById('cbo_approval_type').value+'_'+document.getElementById('txt_internal_file_no').value+'_'+document.getElementById('txt_lc_sc_no').value+'_'+document.getElementById('txt_search_system').value, 'create_pi_search_list_view', 'search_div', 'btb_margin_lc_controller', 'setFilterGrid(\'tbl_list_search\',-1)');reset_hide_field(0);set_all();" style="width:70px;" />
						</td>
					</tr>
					<tr>
						<td colspan="9" align="center" height="40" valign="middle"><? echo load_month_buttons(1);  ?></td>
					</tr>
				</table>
			</fieldset>
			<div style="margin-top:10px" id="search_div"></div>
		</form>
	</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	<script>
		load_drop_down( 'btb_margin_lc_controller',<?  echo $cbo_importer_id; ?>+'_'+document.getElementById('cbo_item_category').value, 'load_supplier_dropdown', 'supplier_td' );
	</script>
	</html>
	<?
}

if($action=="create_pi_search_list_view")
{
	$data=explode('_',$data);
	$cbo_approval_type=$data[7];
	
	if ($data[0]!="") $pi_number="%".$data[0]."%"; else $pi_number = '%%';
	if ($data[8]!="") $internal_file="and a.internal_file_no like '%".$data[8]."%'"; else $internal_file = '';
	if ($data[9]!="") $lc_sc=" and a.lc_sc_no like '%".$data[9]."%'"; else $lc_sc = '';
	if ($data[10]!="") $system_id="%".$data[10]."%"; else $system_id = '%%';


	if($db_type==0)
	{
		if ($data[1]!="" && $data[2]!="") $pi_date = "and pi_date between '".change_date_format($data[1], "yyyy-mm-dd", "-")."' and '".change_date_format($data[2], "yyyy-mm-dd", "-")."'"; else $pi_date ="";
	}
	else if($db_type==2)
	{
		if($data[1]!="" && $data[2]!="") $pi_date ="and pi_date between '".change_date_format($data[1],'','',1)."' and '".change_date_format($data[2],'','',1)."'";
		else $pi_date="";
	}

	$item_category_id =$data[3];
	if($data[4]!=0) $importer_id =$data[4]; else $importer_id='%%';
	if($data[5]!=0) $supplier_id =$data[5]; else $supplier_id='%%';


	$all_pi_id=$data[6];
	$hidden_pi_id=explode(",",$all_pi_id);
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$supplier=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');

	$import_pi_cond='';
	if($item_category_id==110)
	{
		$item_category_id=10;
		$import_pi_cond=" and a.import_pi=1 and a.within_group=1";
	}

	$item_category_cond="";
	if($item_category_id>0)
	{
		if($item_category_id==8) $item_category_cond = " and b.item_category_id in(select category_id from lib_item_category_list where category_type=1) and b.ITEM_PROD_ID>0";
		else $item_category_cond = " and b.item_category_id = $item_category_id ";
	}
	

	if($db_type==0)
	{
		$approval_status="select approval_need, allow_partial from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$importer_id' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format(date('d-m-Y'),'yyyy-mm-dd')."' and company_id='$importer_id')) and page_id=18 and status_active=1 and is_deleted=0";
	}
	else
	{
		$approval_status="select approval_need, allow_partial from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$importer_id' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format(date('d-m-Y'), "", "",1)."' and company_id='$importer_id')) and page_id=18 and status_active=1 and is_deleted=0";
	}
	$approval_status=sql_select($approval_status);
	$approval_status_cond="";
	if($approval_status[0][csf('approval_need')]==1)
	{
		$approval_status_cond= "and a.approved = 1";
	}
	if ($approval_status[0][csf('allow_partial')]==1)
	{
		$approval_status_cond= "and a.approved in( 1,3) ";
	}

	$approved_cond="";
	if($cbo_approval_type>0)
	{
		if($cbo_approval_type==1) $approved_cond .= " and a.approved = 1"; else $approved_cond .= " and a.approved <> 1";
	}


	$nameArray = sql_select("SELECT pi_source_btb_lc FROM variable_settings_commercial where company_name=$importer_id and variable_list=25 and is_deleted = 0 AND status_active = 1");

	if( $cbo_approval_type == 1)
	{
		if($all_pi_id=="")
		{
			if ($nameArray[0][csf("pi_source_btb_lc")] == 2)
			{
				$sql= "SELECT a.id, a.pi_number, a.priority_id, a.pi_date,  a.importer_id, a.supplier_id, a.last_shipment_date, a.hs_code, a.pi_basis_id, a.net_total_amount, a.import_pi, a.approved, a.entry_form, a.item_category_id, a.ref_closing_status, a.pay_term, a.tenor, a.approved_date,a.internal_file_no,a.lc_sc_no, a.currency_id,a.export_pi_id
				from com_pi_master_details a, com_pi_item_details b, commercial_office_note_dtls c, commercial_office_note_mst d, approval_history e
				where a.id=b.pi_id and b.pi_id=c.pi_id and d.id=c.mst_id and a.id=e.mst_id and a.supplier_id like '".$supplier_id."' and a.importer_id like '".$importer_id."' $item_category_cond and a.pi_number like '$pi_number' and a.id like '$system_id' $internal_file $lc_sc $pi_date $import_pi_cond $approved_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and d.status_active=1 and d.is_approved=1 and a.id not in(select pi_id from com_btb_lc_pi where status_active=1 and is_deleted=0) and e.entry_form=27 and e.current_approval_status=1
				group by a.id, a.pi_number, a.priority_id, a.pi_date, a.importer_id, a.supplier_id, a.last_shipment_date, a.hs_code, a.pi_basis_id, a.net_total_amount, a.import_pi, a.approved, a.entry_form, a.item_category_id, a.ref_closing_status, a.pay_term, a.tenor, a.approved_date,a.internal_file_no,a.lc_sc_no, a.currency_id,a.export_pi_id
				order by a.approved_date desc";
			}
			else
			{
				$sql= "SELECT a.id, a.pi_number, a.priority_id, a.pi_date,  a.importer_id, a.supplier_id, a.last_shipment_date, a.hs_code, a.pi_basis_id, a.net_total_amount, a.import_pi, a.approved, a.entry_form, a.item_category_id, a.ref_closing_status, a.pay_term, a.tenor, a.approved_date,a.internal_file_no,a.lc_sc_no, a.currency_id,a.export_pi_id
				from com_pi_master_details a, com_pi_item_details b, approval_history c
				where a.id = b.pi_id and a.id=c.mst_id and a.supplier_id like '".$supplier_id."' and a.importer_id like '".$importer_id."' $item_category_cond and a.pi_number like '$pi_number' and a.id like '$system_id' $internal_file $lc_sc $pi_date $import_pi_cond $approved_cond and a.status_active = 1 and a.is_deleted =0 and b.status_active=1 and b.is_deleted=0  and a.id not in(select pi_id from com_btb_lc_pi where status_active=1 and is_deleted=0) and c.entry_form=27 and c.current_approval_status=1
				group by a.id, a.pi_number, a.priority_id, a.pi_date, a.importer_id, a.supplier_id, a.last_shipment_date, a.hs_code, a.pi_basis_id, a.net_total_amount, a.import_pi, a.approved, a.entry_form, a.item_category_id, a.ref_closing_status, a.pay_term, a.tenor, a.approved_date,a.internal_file_no,a.lc_sc_no, a.currency_id ,a.export_pi_id
				order by a.approved_date desc";
			}
		}
		else
		{
			if ($nameArray[0][csf("pi_source_btb_lc")] == 2)
			{
				$sql= "SELECT a.id, a.pi_number, a.priority_id, a.pi_date,  a.importer_id, a.supplier_id, a.last_shipment_date, a.hs_code, a.pi_basis_id, a.net_total_amount, a.import_pi, a.approved, a.entry_form, a.item_category_id, a.ref_closing_status, a.pay_term, a.tenor, a.approved_date,a.internal_file_no,a.lc_sc_no, a.currency_id,a.export_pi_id
				from com_pi_master_details a, com_pi_item_details b, commercial_office_note_dtls c, commercial_office_note_mst d, approval_history e
				where a.id=b.pi_id and d.id=c.mst_id and b.pi_id=c.pi_id and a.id=e.mst_id and a.supplier_id like '".$supplier_id."' and a.importer_id like '".$importer_id."' $item_category_cond and a.pi_number like '$pi_number' and a.id like '$system_id' $internal_file $lc_sc $pi_date $import_pi_cond $approved_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and d.status_active=1 and d.is_approved=1 and a.id not in(select pi_id from com_btb_lc_pi where status_active=1 and is_deleted=0 and pi_id not in($all_pi_id) ) and e.entry_form=27 and e.current_approval_status=1
				group by a.id, a.pi_number, a.priority_id, a.pi_date,  a.importer_id, a.supplier_id, a.last_shipment_date, a.hs_code, a.pi_basis_id, a.net_total_amount, a.import_pi, a.approved,a.entry_form, a.item_category_id, a.ref_closing_status, a.pay_term, a.tenor, a.approved_date,a.internal_file_no,a.lc_sc_no, a.currency_id ,a.export_pi_id
				order by a.approved_date desc";
			}
			else
			{
				$sql= "SELECT a.id, a.pi_number, a.priority_id, a.pi_date,  a.importer_id, a.supplier_id, a.last_shipment_date, a.hs_code, a.pi_basis_id, a.net_total_amount, a.import_pi, a.approved, a.entry_form, a.item_category_id, a.ref_closing_status, a.pay_term, a.tenor, a.approved_date,a.internal_file_no,a.lc_sc_no, a.currency_id,a.export_pi_id
				from com_pi_master_details a, com_pi_item_details b, approval_history c
				where a.id = b.pi_id and a.id=c.mst_id and a.supplier_id like '".$supplier_id."' and a.importer_id like '".$importer_id."' $item_category_cond and a.pi_number like '$pi_number' and a.id like '$system_id' $internal_file $lc_sc $pi_date $import_pi_cond $approved_cond and a.status_active = 1 and a.is_deleted =0 and b.status_active=1 and b.is_deleted=0 and a.id not in(select pi_id from com_btb_lc_pi where status_active=1 and is_deleted=0 and pi_id not in($all_pi_id) ) and c.entry_form=27 and c.current_approval_status=1
				group by a.id, a.pi_number, a.priority_id, a.pi_date,  a.importer_id, a.supplier_id, a.last_shipment_date, a.hs_code, a.pi_basis_id, a.net_total_amount, a.import_pi, a.approved,a.entry_form, a.item_category_id, a.ref_closing_status, a.pay_term, a.tenor, a.approved_date,a.internal_file_no,a.lc_sc_no, a.currency_id ,a.export_pi_id
				order by a.approved_date desc";
			}
		}
	}
	else
	{
		if($all_pi_id=="")
		{
			if ($nameArray[0][csf("pi_source_btb_lc")] == 2)
			{
				$sql= "SELECT a.id, a.pi_number, a.priority_id, a.pi_date,  a.importer_id, a.supplier_id, a.last_shipment_date, a.hs_code, a.pi_basis_id, a.net_total_amount, a.import_pi, a.approved, a.entry_form, a.item_category_id, a.ref_closing_status, a.pay_term, a.tenor,a.internal_file_no,a.lc_sc_no, a.currency_id,a.export_pi_id
				from com_pi_master_details a, com_pi_item_details b, commercial_office_note_dtls c, commercial_office_note_mst d
				where a.id=b.pi_id and b.pi_id=c.pi_id and d.id=c.mst_id and a.supplier_id like '".$supplier_id."' and a.importer_id like '".$importer_id."' $item_category_cond and a.pi_number like '$pi_number' and a.id like '$system_id' $internal_file $lc_sc $pi_date $import_pi_cond $approved_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and d.status_active=1 and d.is_approved=1 and a.id not in(select pi_id from com_btb_lc_pi where status_active=1 and is_deleted=0)
				group by a.id, a.pi_number, a.priority_id, a.pi_date, a.importer_id, a.supplier_id, a.last_shipment_date, a.hs_code, a.pi_basis_id, a.net_total_amount, a.import_pi, a.approved, a.entry_form, a.item_category_id, a.ref_closing_status, a.pay_term, a.tenor,a.internal_file_no,a.lc_sc_no, a.currency_id ,a.export_pi_id
				order by a.pi_number";
			}
			else
			{
				$sql= "SELECT a.id, a.pi_number, a.priority_id, a.pi_date,  a.importer_id, a.supplier_id, a.last_shipment_date, a.hs_code, a.pi_basis_id, a.net_total_amount, a.import_pi, a.approved, a.entry_form, a.item_category_id, a.ref_closing_status, a.pay_term, a.tenor,a.internal_file_no,a.lc_sc_no, a.currency_id,a.export_pi_id
				from com_pi_master_details a, com_pi_item_details b
				where a.id = b.pi_id and a.supplier_id like '".$supplier_id."' and a.importer_id like '".$importer_id."' $item_category_cond and a.pi_number like '$pi_number' and a.id like '$system_id' $internal_file $lc_sc $pi_date $import_pi_cond $approved_cond and a.status_active = 1 and a.is_deleted =0 and b.status_active=1 and b.is_deleted=0 and a.id not in(select pi_id from com_btb_lc_pi where status_active=1 and is_deleted=0)
				group by a.id, a.pi_number, a.priority_id, a.pi_date,  a.importer_id, a.supplier_id, a.last_shipment_date, a.hs_code, a.pi_basis_id, a.net_total_amount, a.import_pi, a.approved, a.entry_form, a.item_category_id, a.ref_closing_status, a.pay_term, a.tenor,a.internal_file_no,a.lc_sc_no, a.currency_id ,a.export_pi_id
				order by a.pi_number";
			}
		}
		else
		{
			if ($nameArray[0][csf("pi_source_btb_lc")] == 2)
			{
				$sql= "SELECT a.id, a.pi_number, a.priority_id, a.pi_date,  a.importer_id, a.supplier_id, a.last_shipment_date, a.hs_code, a.pi_basis_id, a.net_total_amount, a.import_pi, a.approved, a.entry_form, a.item_category_id, a.ref_closing_status, a.pay_term, a.tenor,a.internal_file_no,a.lc_sc_no, a.currency_id,a.export_pi_id
				from com_pi_master_details a, com_pi_item_details b, commercial_office_note_dtls c, commercial_office_note_mst d
				where a.id=b.pi_id and d.id=c.mst_id and b.pi_id=c.pi_id and a.supplier_id like '".$supplier_id."' and a.importer_id like '".$importer_id."' $item_category_cond and a.pi_number like '$pi_number' and a.id like '$system_id' $internal_file $lc_sc $pi_date $import_pi_cond $approved_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and d.status_active=1 and d.is_approved=1 and a.id not in(select pi_id from com_btb_lc_pi where status_active=1 and is_deleted=0  and pi_id not in($all_pi_id) )
				group by a.id, a.pi_number, a.priority_id, a.pi_date,  a.importer_id, a.supplier_id, a.last_shipment_date, a.hs_code, a.pi_basis_id, a.net_total_amount, a.import_pi, a.approved,a.entry_form, a.item_category_id, a.ref_closing_status, a.pay_term, a.tenor,a.internal_file_no,a.lc_sc_no, a.currency_id ,a.export_pi_id
				order by a.pi_number";
			}
			else
			{
				$sql= "SELECT a.id, a.pi_number, a.priority_id, a.pi_date,  a.importer_id, a.supplier_id, a.last_shipment_date, a.hs_code, a.pi_basis_id, a.net_total_amount, a.import_pi, a.approved, a.entry_form, a.item_category_id, a.ref_closing_status, a.pay_term, a.tenor,a.internal_file_no,a.lc_sc_no, a.currency_id,a.export_pi_id
				from com_pi_master_details a, com_pi_item_details b
				where a.id = b.pi_id and a.supplier_id like '".$supplier_id."' and a.importer_id like '".$importer_id."' $item_category_cond and a.pi_number like '$pi_number' and a.id like '$system_id' $internal_file $lc_sc $pi_date $import_pi_cond $approved_cond and a.status_active = 1 and a.is_deleted =0 and b.status_active=1 and b.is_deleted=0 and a.id not in(select pi_id from com_btb_lc_pi where status_active=1 and is_deleted=0 and pi_id not in($all_pi_id) )
				group by a.id, a.pi_number, a.priority_id, a.pi_date,  a.importer_id, a.supplier_id, a.last_shipment_date, a.hs_code, a.pi_basis_id, a.net_total_amount, a.import_pi, a.approved,a.entry_form, a.item_category_id, a.ref_closing_status, a.pay_term, a.tenor,a.internal_file_no,a.lc_sc_no, a.currency_id ,a.export_pi_id
				order by a.pi_number";
			}
		}
	}

	$max_approved_date = return_library_array("select mst_id, max(approved_date) as approved_date from approval_history where entry_form=27 group by mst_id","mst_id","approved_date");
	$approval_cause_arr = return_library_array("select booking_id, approval_cause from fabric_booking_approval_cause where entry_form=27 and status_active=1 and is_deleted=0","booking_id","approval_cause");

	$data_file=sql_select("select image_location, master_tble_id from common_photo_library where form_name='proforma_invoice' and is_deleted=0 and file_type=2");
	$file_arr=array();
	foreach($data_file as $row)
	{
		$file_arr[$row[csf('master_tble_id')]]['file']=$row[csf('image_location')];
	}
	unset($data_file);

	// echo $sql;
	$nameArray=sql_select( $sql );
	foreach ($nameArray as $val) {
		$pi_Ids.=$val[csf('id')].',';
	}
	// $pi_Ids = implode(',',array_flip(array_flip(explode(',', rtrim($pi_Ids,',')))));
	$pi_Ids = array_flip(explode(',', rtrim($pi_Ids,',')));
	$tot_rows=count($pi_Ids);
	if (!empty($pi_Ids))
	{     
		$pi_id_cond = '';
		if($db_type==2 && $tot_rows>1000)
		{
			$piIds = array_keys($pi_Ids);
			$piIdArr = array_chunk($piIds,999);
			foreach($piIdArr as $ids)
			{
				$ids = implode(',',$ids);
				$pi_id_cond .= " and a.id in($ids) ";
			}
		}
		else
		{
			$piIds = implode(',',array_keys($pi_Ids));
			$pi_id_cond = " and a.id in ($piIds) ";
		}
	}
	// and a.id in($pi_Ids)
	$sql_lc_sc="SELECT a.id as pi_id, c.job_no, c.booking_no, e.bank_file_no, e.internal_file_no
	from com_pi_master_details a, com_pi_item_details b, wo_booking_dtls c, com_export_lc_order_info d, com_export_lc e
	where a.id=b.pi_id and b.work_order_no=c.booking_no and c.po_break_down_id=d.wo_po_break_down_id and d.com_export_lc_id=e.id and a.importer_id=$importer_id $pi_id_cond and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and e.status_active=1
	group by a.id, c.job_no, c.booking_no,e.bank_file_no, e.internal_file_no
	union all
	SELECT a.id as pi_id, c.job_no,c.booking_no, e.bank_file_no, e.internal_file_no
	from com_pi_master_details a, com_pi_item_details b, wo_booking_dtls c, com_sales_contract_order_info d, com_sales_contract e
	where a.id=b.pi_id and b.work_order_no=c.booking_no and c.po_break_down_id=d.wo_po_break_down_id and d.com_sales_contract_id=e.id and a.importer_id=$importer_id $pi_id_cond and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and e.status_active=1
	group by a.id, c.job_no, c.booking_no, e.bank_file_no, e.internal_file_no";
	// echo $sql_lc_sc;die;
	$sql_lc_sc_res=sql_select($sql_lc_sc);
	$bank_file_arr=array();$internal_file_arr=array();
	foreach ($sql_lc_sc_res as $val) {
		if ($val[csf('bank_file_no')] != ''){
			$bank_file_arr[$val[csf('pi_id')]]['bank_file_no'].=$val[csf('bank_file_no')].',';
		}		
		if ($val[csf('internal_file_no')] != ''){
			$internal_file_arr[$val[csf('pi_id')]]['internal_file_no'].=$val[csf('internal_file_no')].',';
		}		
	}	

	
	ob_start();
	$html='<div>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1585" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="110">PI No</th>
                <th width="50">PI System ID</th>
                <th width="60">Image/File</th>
                <th width="60">PI Date</th>
                <th width="100">Item Category</th>
                <th width="70">Importer</th>
                <th width="130">Supplier</th>
                <th width="100">Internal File No</th>
                <th width="100">Bank File No</th>
                <th width="130">LC/SC No</th>
                <th width="75">Last Ship Date</th>
                <th width="60">HS Code</th>
                <th width="60">Approved</th>
                <th width="80">Last Aproved</th>
                <th width="100">Aproved Remarks</th>
                <th width="60">Priority</th>
                <th width="100">PI Basis</th>
                <th>PI Value</th>
            </thead>
		</table>
		<div style="width:1585px; max-height:220px; overflow-y:scroll">
        	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1567" class="rpt_table" id="tbl_list_search">';
	?>
	

    <div>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1585" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="110">PI No</th>
                <th width="50">PI System ID</th>
                <th width="60">Image/File</th>
                <th width="60">PI Date</th>
                <th width="100">Item Category</th>
                <th width="70">Importer</th>
                <th width="130">Supplier</th>
                <th width="100">Internal File No</th>
                <th width="100">Bank File No</th>
                <th width="130">LC/SC No</th>
                <th width="75">Last Ship Date</th>
                <th width="60">HS Code</th>
                <th width="60">Approved</th>
                <th width="80">Last Aproved</th>
                <th width="100">Aproved Remarks</th>
                <th width="60">Priority</th>
                <th width="100">PI Basis</th>
                <th>PI Value</th>
            </thead>
		</table>
		<div style="width:1585px; max-height:220px; overflow-y:scroll">
        	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1567" class="rpt_table" id="tbl_list_search">
                <?
                 $i=1; $pi_row_id="";                
				 $priority_array=array(1=>"Normal",2=>"Urgent",3=>"Critical");
                 foreach ($nameArray as $selectResult)
                 {
                    if ($i%2==0) $bgcolor="#E9F3FF";
                    else $bgcolor="#FFFFFF";

					if (number_format($selectResult[csf('net_total_amount')],2,'.','')>0.00)
					{
						if(in_array($selectResult[csf('id')],$hidden_pi_id))
						{
							if($pi_row_id=="") $pi_row_id=$i; else $pi_row_id.=",".$i;
						}

						if($selectResult[csf('import_pi')]==1)
						{
							$supplier_name=$comp[$selectResult[csf('supplier_id')]];
						}
						else
						{
							$supplier_name=$supplier[$selectResult[csf('supplier_id')]];
						}
						if($selectResult[csf('internal_file_no')]=='')
						{
							$internal_file_no=implode(', ',array_unique(explode(',', rtrim($internal_file_arr[$selectResult[csf('id')]]['internal_file_no'],','))));
						}
						else
						{
							$internal_file_no=$selectResult[csf('internal_file_no')];
						}
						?>
						<tr height="20" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)">
							<td width="30" align="center"><? echo "$i"; ?>
							
							</td>
							<td width="110" title="<?  echo implode(', ',array_unique(explode(',', rtrim($internal_file_arr[$selectResult[csf('id')]]['internal_file_no'],',')))); ?>"><p><? echo $selectResult[csf('pi_number')];?></p></td>
							<td width="50" ><p><? echo $selectResult[csf('id')];?></p></td>
							<td width="60"><p>
								<?
								/*$file_name=$file_arr[$selectResult[csf('id')]]['file'];
								if( $file_name != '')
								{
									?>
								<input type="button" class="image_uploader" id="fileno_<? echo $i;?>" style="width:40px" value="File" onClick="openmypage_file(<? echo $i; ?>)"/>
									<?
								}*/
								?>
								</p></td>
							<td width="60"><? echo change_date_format($selectResult[csf('pi_date')]);?></td>
							<td width="100" title="<? echo $selectResult[csf('item_category_id')]; ?>"><p><? echo $item_category_with_gen[$selectResult[csf('item_category_id')]]; ?></p></td>
							<td width="70"><p><? echo $comp[$selectResult[csf('importer_id')]]; ?></p></td>
							<td width="130"><p><? echo $supplier_name;//$supplier[$selectResult[csf('supplier_id')]]; ?></p></td>
							<td width="100"><p><? echo $internal_file_no; ?></p></td>
							<td width="100"><p><? echo implode(',',array_unique(explode(',', rtrim($bank_file_arr[$selectResult[csf('id')]]['bank_file_no'],',')))); ?></p></td>
							<td width="130"><p><? echo $selectResult[csf('lc_sc_no')]; ?></p></td>
							<td width="75"><? echo change_date_format($selectResult[csf('last_shipment_date')]); ?></td>
							<td width="60"><p><? echo $selectResult[csf('hs_code')]; ?></p></td>
							<td width="60" align="center"><p><? if($selectResult[csf('approved')]==1 || $selectResult[csf('approved')]==3) $aprove=1; else $aprove=2; echo $yes_no[$aprove]; ?></p></td>
						<td width="80" style="word-break:break-all;"><P><? if( $cbo_approval_type == 1) echo $selectResult[csf('approved_date')]; else echo $max_approved_date[$selectResult[csf('id')]]; ?></P></td>
							<td width="100" style="word-break:break-all;"><P><? echo $approval_cause_arr[$selectResult[csf('id')]]; ?></P></td>
							<td width="60" style="word-break:break-all;"><P><? echo $priority_array[$selectResult[csf('priority_id')]]; ?></P></td>
							<td width="100"><p><? echo $pi_basis[$selectResult[csf('pi_basis_id')]]; ?></p></td>
							<td align="right"><? echo number_format($selectResult[csf('net_total_amount')],2,'.',''); ?>&nbsp;</td>
						</tr>
						<?
						$html.='<tr height="20" bgcolor="'. $bgcolor.'" style="text-decoration:none; cursor:pointer" id="search'.$i.'" onClick="js_set_value('. $i.')">
							<td width="30" align="center">'.$i.'
							<input type="hidden" name="txt_individual_id" id="txt_individual_id'.$i.'" value="'.$selectResult[csf('id')].'"/>
							<input type="hidden" name="txt_individual" id="txt_individual'.$i.'" value="'.$selectResult[csf('pi_number')].'"/>
							<input type="hidden" name="approvalStatus[]" id="approvalStatus_'.$i.'" value="'.$selectResult[csf('approved')].'"/>
							<input type="hidden" name="supplierChk[]" id="supplierChk_'.$i.'" value="'.$selectResult[csf('import_pi')].'_'.$selectResult[csf('supplier_id')].'"/>
							<input type="hidden" name="entryForm[]" id="entryForm_'.$i.'" value="'.$selectResult[csf('entry_form')].'"/>
							<input type="hidden" name="itemCategory[]" id="itemCategory_'.$i.'" value="'.$selectResult[csf('item_category_id')].'"/>
							<input type="hidden" name="refClosingStatus[]" id="refClosingStatus_'.$i.'" value="'.$selectResult[csf('ref_closing_status')].'"/>
							<input type="hidden" name="payTerm[]" id="payTerm_'.$i.'" value="'.$selectResult[csf('pay_term')].'"/>
							<input type="hidden" name="tenor[]" id="tenor_'.$i.'" value="'.$selectResult[csf('tenor')].'"/>
							<input type="hidden" name="currencyId[]" id="currencyId_'.$i.'" value="'.$selectResult[csf('currency_id')].'"/>
							</td>';

							if($selectResult[csf('export_pi_id')]=='' || $selectResult[csf('export_pi_id')]<0){
								$export_pids = 0;
							}
							else{
								$export_pids = $selectResult[csf('export_pi_id')];
							}
		
							$total_data = $selectResult[csf('importer_id')].'*'.$selectResult[csf('id')].'*'.$selectResult[csf('entry_form')].'*'.$selectResult[csf('item_category_id')].'*'.$export_pids;

							$html.='<td width="110" style="color:blue;" title="'.implode(', ',array_unique(explode(',', rtrim($internal_file_arr[$selectResult[csf('id')]]['internal_file_no'],',')))).'"  onClick="open_print_btn_popup(\''.$total_data.'\')" <p><u>'.$selectResult[csf('pi_number')].'</u></p></td>

							<td width="50" ><p>'.$selectResult[csf('id')].'</p></td>
							<td width="60"><p>';
								$file_name=$file_arr[$selectResult[csf('id')]]['file'];
								if( $file_name != '')
								{
									$html.='<input type="button" class="image_uploader" id="fileno_'.$i.'" style="width:40px" value="File" onClick="openmypage_file('.$i.')"/>';
								}
								$html.='</p></td>
							<td width="60">'.change_date_format($selectResult[csf('pi_date')]).'</td>
							<td width="100" title="'.$selectResult[csf('item_category_id')].'"><p>'.$item_category_with_gen[$selectResult[csf('item_category_id')]].'</p></td>
							<td width="70"><p>'.$comp[$selectResult[csf('importer_id')]].'</p></td>
							<td width="130"><p>'.$supplier_name.'</p></td>
							<td width="100"><p>'.$internal_file_no.'</p></td>
							<td width="100"><p>'.implode(',',array_unique(explode(',', rtrim($bank_file_arr[$selectResult[csf('id')]]['bank_file_no'],',')))).'</p></td>
							<td width="130"><p>'.$selectResult[csf('lc_sc_no')].'</p></td>
							<td width="75">'.change_date_format($selectResult[csf('last_shipment_date')]).'</td>
							<td width="60"><p>'.$selectResult[csf('hs_code')].'</p></td>
							<td width="60" align="center"><p>';
							if($selectResult[csf('approved')]==1 || $selectResult[csf('approved')]==3) $aprove=1; else $aprove=2; 
							$html.=$yes_no[$aprove].'</p></td>
							<td width="80" style="word-break:break-all;"><P>';
							if( $cbo_approval_type == 1) $html.=$selectResult[csf('approved_date')]; else $html.=$max_approved_date[$selectResult[csf('id')]];
							$html.='</P></td>
							<td width="100" style="word-break:break-all;"><P>'.$approval_cause_arr[$selectResult[csf('id')]].'</P></td>
							<td width="60" style="word-break:break-all;"><P>'.$priority_array[$selectResult[csf('priority_id')]].'</P></td>
							<td width="100"><p>'.$pi_basis[$selectResult[csf('pi_basis_id')]].'</p></td>
							<td align="right">'.number_format($selectResult[csf('net_total_amount')],2,'.','').'&nbsp;</td>
						</tr>';
						$i++;
					}
                }
                ?>
               	<input type="hidden" name="txt_pi_row_id" id="txt_pi_row_id" value="<? echo $pi_row_id; ?>"/>
            </table>
        </div>
        <table width="1280" cellspacing="0" cellpadding="0" border="1" align="center">
            <tr>
                <td align="center" height="30" valign="bottom">
                    <div style="width:100%">
                        <div style="width:45%; float:left" align="left">
                            <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data(this.checked)" /> Check / Uncheck All
                        </div>
                        <div style="width:53%; float:left" align="left">
                            <input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
                        </div>
                    </div>
                </td>
            </tr>
        </table>
	</div>
    <?
	$html.='<input type="hidden" name="txt_pi_row_id" id="txt_pi_row_id" value="'.$pi_row_id.'"/>
            </table>
        </div>
        <table width="1280" cellspacing="0" cellpadding="0" border="1" align="center">
            <tr>
                <td align="center" height="30" valign="bottom">
                    <div style="width:100%">
                        <div style="width:45%; float:left" align="left">
                            <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data(this.checked)" /> Check / Uncheck All
                        </div>
                        <div style="width:53%; float:left" align="left">
                            <input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
                        </div>
                    </div>
                </td>
            </tr>
        </table>
	</div>';
	foreach (glob("$user_id*.xls") as $filename) 
	{
		@unlink($filename);
		//if( @filemtime($filename) < (time()-$seconds_old) )
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
	ob_end_clean();
	
	echo $html; 
	?>
    &nbsp; <a href="<? echo $filename; ?>" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>&nbsp;
    <?
	exit();
}


if($action=="show_file")
{
	echo load_html_head_contents("Invoice File","../../", 1, 1, $unicode);
    extract($_REQUEST);
	$data_array=sql_select("select IMAGE_LOCATION, REAL_FILE_NAME from common_photo_library where master_tble_id='$mst_id' and form_name='proforma_invoice' and is_deleted=0 and file_type=2");
	?>
	<style type="text/css">
		li { list-style: none; font-size: 9pt; margin-top: 0px; margin-left: 7px; float: left; width: 89px;}
	</style>
    <table width="100%">
        <tr>
        	<td width="100%" height="250" style="vertical-align: top;">
	        <?
	        foreach ($data_array as $row)
	        {
	        	?>
	        	<li>
	        		<a href="../../../<? echo $row['IMAGE_LOCATION']; ?>" target="_new">
	        		<img src="../../../file_upload/blank_file.png" height="97" width="89"></a>
	        		<br>
	        		<p style="width: 89px; word-break: break-all; margin-top: 1px;"><? echo $row['REAL_FILE_NAME']; ?></p>
	        	</li>
	        	<?
	        }
	        ?>
        	</td>
        </tr>
    </table>
    <?
}

if($action=="check_used_or_not")
{
	$data=explode("**",$data);
	$pi_id=$data[0];
	$btb_id=$data[1];

	$sql="select a.invoice_no from com_import_invoice_mst a, com_import_invoice_dtls b where a.id=b.import_invoice_id and b.btb_lc_id=$btb_id and b.pi_id=$pi_id and b.status_active=1 and b.is_deleted=0 and b.current_acceptance_value>0 group by a.id, a.invoice_no";
	$data_array=sql_select($sql);
	$invoice_no='';
	if(count($data_array)>0)
	{
		foreach($data_array as $row)
		{
			if($invoice_no=="") $invoice_no=$row[csf('invoice_no')]; else $invoice_no.=", ".$row[csf('invoice_no')];
		}
		echo "1**".$invoice_no;
	}
	else
	{
		echo "0**";
	}
	exit();
}

if($action=="btb_lc_search")
{
	echo load_html_head_contents("BTB L/C Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>

	<script>
		function js_set_value(id)
		{
			var data = id.split("__");
			$('#hidden_btb_id').val(id);
			$('#hidden_ref_closing_status').val(data[2]);
			if(data[2]==1)
			{
				alert("This reference is closed. No operation is allowed");
			}
			parent.emailwindow.hide();
		}

    </script>

	</head>

	<body>
	<div align="center" style="width:1000px;">
		<form name="searchscfrm"  id="searchscfrm">
			<fieldset style="width:100%; margin-left:15px">
				<legend>Enter search words</legend>
					<table cellpadding="0" cellspacing="0" border="1" rules="all" width="1070" class="rpt_table">
						<thead>
							<th>Item Category</th>
							<th class="must_entry_caption">Company</th>
							<th>Supplier</th>
							<th>L/C Date</th>
							<th>System Id</th>
							<th>LC No</th>
							<th>PI No</th>
							<th>PI ID</th>
							<th>
								<input type="reset" name="reset" id="reset" value="Reset" style="width:80px" class="formbutton" />
								<input type="hidden" name="id_field" id="id_field" value="" />
							</th>
						</thead>
						<tr class="general">
							<td>
								<? echo create_drop_down( "cbo_item_category_id", 140, $item_category_with_gen,'', 1, '--Select--',0,"load_drop_down( 'btb_margin_lc_controller',document.getElementById('txt_company_id').value+'_'+this.value,'load_supplier_dropdown','supplier_td' );",0,'','','','72,79,73,71,77,78,75,76'); ?>
							</td>
							<td>
							<?
									echo create_drop_down( "txt_company_id",150,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name",'id,company_name', 1, 'Select',$cbo_importer_id,"load_drop_down( 'btb_margin_lc_controller',this.value+'_'+document.getElementById('cbo_item_category_id').value,'load_supplier_dropdown','supplier_td' );",1);
								?>
							</td>
							<td align="center" id="supplier_td">
							<? echo create_drop_down( "cbo_supplier_id", 165,$blank_array,'', 1, '----Select----',0,0,0); ?>
							</td>
							<td>
								<input type="text" name="btb_start_date" id="btb_start_date" class="datepicker" style="width:40px;" />To
								<input type="text" name="btb_end_date" id="btb_end_date" class="datepicker" style="width:40px;" />
							</td>
							<td id="search_by_td">
								<input type="text" style="width:90px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
								<input type="hidden" id="hidden_btb_id" />
								<input type="hidden" id="hidden_ref_closing_status" />
							</td>
							<td >
								<input type="text" style="width:90px" class="text_boxes"  name="txt_lc_no" id="txt_lc_no" />
							</td>
							<td >
								<input type="text" style="width:90px" class="text_boxes"  name="txt_pi_no" id="txt_pi_no" />
							</td>
							<td >
								<input type="text" style="width:90px" class="text_boxes"  name="pi_system_id" id="pi_system_id" />
							</td>
							<td>
								<input type="button" id="search_button" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_company_id').value+'**'+document.getElementById('cbo_item_category_id').value+'**'+document.getElementById('cbo_supplier_id').value+'**'+document.getElementById('btb_start_date').value+'**'+document.getElementById('btb_end_date').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_lc_no').value+'**'+document.getElementById('txt_pi_no').value+'**'+document.getElementById('pi_system_id').value, 'create_btb_search_list_view', 'search_div', 'btb_margin_lc_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:80px;" />
							</td>
						</tr>
				</table>
				<table width="100%" style="margin-top:5px" align="center">
						<tr>
							<td colspan="5" id="search_div" align="center"></td>
						</tr>
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

if($action=="create_btb_search_list_view")
{

	$data=explode('**',$data);
	//print_r($data);die;
	$company_id = $data[0];
	$item_category_id = $data[1];
	$supplier_id = $data[2];
	$lc_start_date = $data[3];
	$lc_end_date = $data[4];
	$system_id = $data[5];
	$lc_num = trim($data[6]);
	$pi_num = trim($data[7]);
	$pi_id = trim($data[8]);

	if($company_id==0)
	{
		echo 'Select Importer';die;
	}

	if ($company_id!=0) $company=$company_id;

	$sql_cond="";

	if ($supplier_id > 0) $sql_cond =" and a.supplier_id = $supplier_id ";
	if ($system_id !='') $sql_cond .=" and a.btb_system_id like '%".$system_id."'";
	if ($lc_num !='') $sql_cond .=" and a.lc_number like '%".$lc_num."'";
	if ($pi_num !='') $sql_cond .=" and c.pi_number like '%".$pi_num."'";
	if ($pi_id !='') $sql_cond .=" and  b.pi_id like '%".$pi_id."'";

	if($lc_start_date!='' && $lc_end_date!='')
	{
		if($db_type==0)
		{
			$sql_cond .= "and a.lc_date between '".change_date_format($lc_start_date,'yyyy-mm-dd')."' and '".change_date_format($lc_end_date,'yyyy-mm-dd')."'";
		}
		else if($db_type==2)
		{
			$sql_cond .= "and a.lc_date between '".change_date_format($lc_start_date,'','',1)."' and '".change_date_format($lc_end_date,'','',1)."'";
		}
	}
	$category_cond="";$category_cond2="";
	if ($item_category_id > 0) 
	{
		$category_cond .=" and a.pi_entry_form = '".$category_wise_entry_form[$item_category_id]."'";
		
		if($item_category_id==8) $category_cond.=" and c.item_category_id in(select category_id from lib_item_category_list where category_type=1)";
		else $category_cond.=" and c.item_category_id = '$item_category_id'";
		
		//$category_cond .=" and c.item_category_id = '".$item_category_id."'";
		//$category_cond2 .=" and a.item_category_id = '".$item_category_id."'";
		
		if($item_category_id==8) $category_cond2=" and a.item_category_id in(select category_id from lib_item_category_list where category_type=1)";
		else $category_cond2=" and a.item_category_id = '$item_category_id'";
	}
	


	if($db_type==0) $year_field="YEAR(a.insert_date) as year,";
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year,";
	else $year_field="";//defined Later

	//item_basis_id

	$sql = "SELECT a.id, b.pi_id, $year_field a.btb_prefix_number, a.btb_system_id, a.lc_number, a.supplier_id, a.application_date, a.last_shipment_date, a.lc_date, a.lc_value, c.item_category_id, a.importer_id, a.ref_closing_status
	FROM com_btb_lc_master_details a, com_btb_lc_pi b, com_pi_master_details c
	WHERE a.id=b.com_btb_lc_master_details_id and b.pi_id=c.id and a.importer_id = '".$company."' and a.is_deleted = 0 and a.item_basis_id=1 $sql_cond $category_cond
	group by a.id, b.pi_id, a.insert_date, a.btb_prefix_number, a.btb_system_id, a.lc_number, a.supplier_id, a.application_date, a.last_shipment_date, a.lc_date, a.lc_value, c.item_category_id, a.importer_id, a.ref_closing_status
	UNION ALL
	SELECT a.id, b.pi_id, $year_field a.btb_prefix_number, a.btb_system_id, a.lc_number, a.supplier_id, a.application_date, a.last_shipment_date, a.lc_date, a.lc_value, c.item_category_id, a.importer_id, a.ref_closing_status
	FROM com_btb_lc_master_details a, com_btb_lc_pi b, com_pi_master_details c
	WHERE a.id=b.com_btb_lc_master_details_id and b.pi_id=c.id and a.importer_id = '".$company."' and a.is_deleted = 0 and a.item_basis_id=2 $sql_cond $category_cond2
	group by a.id, b.pi_id, a.insert_date, a.btb_prefix_number, a.btb_system_id, a.lc_number, a.supplier_id, a.application_date, a.last_shipment_date, a.lc_date, a.lc_value, c.item_category_id, a.importer_id, a.ref_closing_status
	order by id desc";
	
	/*$sql = "SELECT a.id, $year_field a.btb_prefix_number, a.btb_system_id, a.lc_number, a.supplier_id, a.application_date, a.last_shipment_date, a.lc_date, a.lc_value, c.item_category_id, a.importer_id, a.ref_closing_status
	FROM com_btb_lc_master_details a
	left join  com_btb_lc_pi b on a.id=b.com_btb_lc_master_details_id
	left join com_pi_master_details c on b.pi_id=c.id 
	WHERE a.importer_id = '".$company."' and a.is_deleted = 0 and a.item_basis_id=1 $sql_cond $category_cond
	group by a.id, a.insert_date, a.btb_prefix_number, a.btb_system_id, a.lc_number, a.supplier_id, a.application_date, a.last_shipment_date, a.lc_date, a.lc_value, c.item_category_id, a.importer_id, a.ref_closing_status
	UNION ALL
	SELECT a.id, $year_field a.btb_prefix_number, a.btb_system_id, a.lc_number, a.supplier_id, a.application_date, a.last_shipment_date, a.lc_date, a.lc_value, c.item_category_id, a.importer_id, a.ref_closing_status
	FROM com_btb_lc_master_details a
	left join  com_btb_lc_pi b on a.id=b.com_btb_lc_master_details_id
	left join com_pi_master_details c on b.pi_id=c.id 
	WHERE a.importer_id = '".$company."' and a.is_deleted = 0 and a.item_basis_id=2 $sql_cond $category_cond2
	group by a.id, a.insert_date, a.btb_prefix_number, a.btb_system_id, a.lc_number, a.supplier_id, a.application_date, a.last_shipment_date, a.lc_date, a.lc_value, c.item_category_id, a.importer_id, a.ref_closing_status
	order by id desc";*/

	//echo $sql;//die; item_category_id,

	$supplier_lib=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$exportPiSuppArr = array();
	$exportPiSupp = sql_select("select c.import_pi, a.id from com_btb_lc_master_details a , com_btb_lc_pi b , com_pi_master_details c where a.id = b.com_btb_lc_master_details_id and b.pi_id = c.id");
	foreach ($exportPiSupp as $value)
	{
		$exportPiSuppArr[$value[csf("id")]] = $value[csf("import_pi")];
	}
	unset($exportPiSupp);
	//$arr=array(0=>$item_category,3=>$supplier_lib);COM_PI_MASTER_DETAILS
	//echo create_list_view("list_view", "Item Category,Year,System Id,Supplier,L/C Number,L/C Date,L/C Value,Application Date,Last Ship Date", "110,55,65,150,150,80,100,100,100","980","320",0, $sql , "js_set_value", "id", "",1,"item_category_id,0,0,supplier_id,0,0,0,0,0", $arr , "item_category_id,year,btb_prefix_number,supplier_id,lc_number,lc_date,lc_value,application_date,last_shipment_date","",'','0,0,0,0,0,3,2,3,3') ;
	?>
	<table width="990" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all">
        <thead>
            <th width="40">SL</th>
            <th width="110">Item Category</th>
            <th width="50">PI ID</th>
            <th width="55">Year</th>
            <th width="65">System Id</th>
            <th width="150">Supplier</th>
            <th width="150">L/C Number</th>
            <th width="80">L/C Date</th>
            <th width="100">L/C Value</th>
            <th width="100">Application Date</th>
            <th>Last Ship Date</th>
        </thead>
     </table>
     <div style="width:990px; overflow-y:scroll; max-height:280px">
     	<table width="970" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" id="list_view">
		<?
			//echo $sql;
			$data_array=sql_select($sql); $i = 1;
			//print_r($data_array);die;
            foreach($data_array as $row)
            {
                if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

				$supplier='';
				if($row[csf('item_category_id')]==110)
				{
					$supplier=$comp[$row[csf('supplier_id')]];
				}
				else
				{
					$supplier=$supplier_lib[$row[csf('supplier_id')]];
				}

				if($exportPiSuppArr[$row[csf('id')]] == 1){
					$supplier=$comp[$row[csf('supplier_id')]];
				}

				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer;" id="search<? echo $i;?>" onClick="js_set_value( '<? echo $row[csf('id')]."__".$row[csf('item_category_id')]."__".$row[csf('ref_closing_status')]; ?>');">
                	<td width="40"><? echo $i; ?></td>
					<td width="110"><? echo $item_category_with_gen[$row[csf('item_category_id')]]; ?></td>
					<td width="50" align="center"><? echo $row[csf('pi_id')]; ?></td>
					<td width="55" align="center"><? echo $row[csf('year')]; ?></td>
                    <td width="65"><? echo $row[csf('btb_prefix_number')]; ?></td>
					<td width="150"><p><? echo $supplier; ?></p></td>
                    <td width="150"><p><? echo $row[csf('lc_number')]; ?></p></td>
                    <td width="80" align="center"><p><? echo change_date_format($row[csf('lc_date')]); ?></p></td>
                    <td width="100" align="right"><? echo number_format($row[csf('lc_value')],2); ?>&nbsp;</td>
					<td width="100" align="center"><? echo change_date_format($row[csf('application_date')]); ?>&nbsp;</td>
                    <td align="center"><? echo change_date_format($row[csf('last_shipment_date')]); ?>&nbsp;</td>
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


if($action=='populate_data_from_btb_lc')
{
	//==========================for supplier array=========================
	$exportPiSupp = sql_select("select c.import_pi, a.id from com_btb_lc_master_details a , com_btb_lc_pi b , com_pi_master_details c where a.id = b.com_btb_lc_master_details_id and b.pi_id = c.id");
	foreach ($exportPiSupp as $value)
	{
		$exportPiSuppArr[$value[csf("id")]] = $value[csf("import_pi")];
	}
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	//====================================================================

	//==========================TO SHOW File No=========================
	$LcScArr=sql_select("SELECT a.IMPORT_MST_ID, b.INTERNAL_FILE_NO  FROM com_btb_export_lc_attachment a, com_export_lc b where a.LC_SC_ID=b.id and b.STATUS_ACTIVE=1 and a.IMPORT_MST_ID=$data union all SELECT a.IMPORT_MST_ID, b.INTERNAL_FILE_NO FROM com_btb_export_lc_attachment a, com_sales_contract b where a.LC_SC_ID=b.id and b.STATUS_ACTIVE=1 and a.IMPORT_MST_ID=$data");
	if(!empty($LcScArr)){
		$InternalFileNoArr=array();
		foreach($LcScArr as $val){
			$InternalFileNoArr[$val["IMPORT_MST_ID"]]["INTERNAL_FILE_NO"]=$val["INTERNAL_FILE_NO"];
		}
	}
	//====================================================================

	
	$data_array=sql_select("select id,btb_prefix,btb_prefix_number,btb_system_id,bank_code,lc_year, 	lc_category,lc_serial,supplier_id,importer_id,application_date,lc_date,last_shipment_date,lc_expiry_date,lc_type_id,lc_value,max_lc_value,min_lc_value,currency_id,issuing_bank_id,item_category_id,tenor,tolerance,inco_term_id,inco_term_place,delivery_mode_id,etd_date,insurance_company_name,lca_no,lcaf_no,imp_form_no,psi_company,cover_note_no,cover_note_date,maturity_from_id,margin,origin,bonded_warehouse,item_basis_id,garments_qty,credit_to_be_advised,partial_shipment,transhipment,shipping_mark,doc_presentation_days,port_of_loading,port_of_discharge,remarks,pi_id,pi_value,payterm_id,uom_id,ud_no,ud_date,credit_advice_id,confirming_bank,inserted_by,insert_date,status_active, pi_entry_form,upas_rate,advising_bank,advising_bank_address,lc_reference_no,lc_expiry_days from com_btb_lc_master_details where id='$data'");

	foreach ($data_array as $row)
	{
		echo "document.getElementById('txt_system_id').value 			= '".$row[csf("btb_system_id")]."';\n";
		echo "document.getElementById('txt_bank_code').value 			= '".$row[csf("bank_code")]."';\n";
		echo "document.getElementById('txt_lc_year').value 				= '".$row[csf("lc_year")]."';\n";
		echo "document.getElementById('txt_category').value 			= '".$row[csf("lc_category")]."';\n";
		echo "document.getElementById('txt_lc_serial').value 			= '".$row[csf("lc_serial")]."';\n";
		echo "document.getElementById('update_id').value 				= '".$row[csf("id")]."';\n";
		echo "document.getElementById('cbo_importer_id').value			= '".$row[csf("importer_id")]."';\n";
		echo "$('#cbo_importer_id').attr('disabled','true')".";\n";
        echo "$('#cbo_item_category_id').attr('disabled','true')".";\n";
		echo "document.getElementById('cbo_item_category_id').value 	= '".$row[csf("item_category_id")]."';\n";
		//echo "document.getElementById('txt_hidden_pi_item').value 		= '".$row[csf("item_category_id")]."';\n";

		echo "load_drop_down( 'requires/btb_margin_lc_controller', document.getElementById('cbo_importer_id').value+'_'+document.getElementById('cbo_item_category_id').value, 'load_supplier_dropdown_new', 'supplier_td' );\n";


		if($exportPiSuppArr[$row[csf("id")]] == 1)
		{
			echo '$("#cbo_supplier_id option[value!=\'0\']").remove();'."\n";
        	echo '$("#cbo_supplier_id").append("<option selected value=\''.$row[csf("supplier_id")].'\'>'.$comp[$row[csf("supplier_id")]].'</option>");'."\n";
        	echo "$('#cbo_supplier_id').attr('disabled','true')".";\n";
		}else{
			echo "document.getElementById('cbo_supplier_id').value 			= '".$row[csf("supplier_id")]."';\n";
			echo "$('#cbo_supplier_id').attr('disabled','true')".";\n";
		}

		echo "document.getElementById('application_date').value 		= '".change_date_format($row[csf("application_date")])."';\n";
		echo "document.getElementById('txt_lc_date').value 				= '".change_date_format($row[csf("lc_date")])."';\n";
		echo "document.getElementById('txt_last_shipment_date').value 	= '".change_date_format($row[csf("last_shipment_date")])."';\n";
		echo "document.getElementById('txt_lc_expiry_date').value 		= '".change_date_format($row[csf("lc_expiry_date")])."';\n";
		echo "document.getElementById('cbo_lc_type_id').value 			= '".$row[csf("lc_type_id")]."';\n";
		echo "$('#cbo_lc_type_id').attr('disabled','true')".";\n";
		echo "document.getElementById('cbo_lc_basis_id').value 			= '".$row[csf("item_basis_id")]."';\n";

		echo "active_inactive(".$row[csf("item_basis_id")].");\n";

		if($row[csf("etd_date")]=="0000-00-00" || $row[csf("etd_date")]=="") $etd_date=""; else $etd_date=change_date_format($row[csf("etd_date")]);
		if($row[csf("cover_note_date")]=="0000-00-00" || $row[csf("cover_note_date")]=="") $cover_note_date=""; else $cover_note_date=change_date_format($row[csf("cover_note_date")]);
		if($row[csf("ud_date")]=="0000-00-00" || $row[csf("ud_date")]=="") $ud_date=""; else $ud_date=change_date_format($row[csf("ud_date")]);

		echo "document.getElementById('txt_lc_value').value 			= '".$row[csf("lc_value")]."';\n";
		echo "document.getElementById('cbo_lc_currency_id').value 		= '".$row[csf("currency_id")]."';\n";
		echo "document.getElementById('cbo_pi_currency_id').value 		= '".$row[csf("currency_id")]."';\n";
		
		echo "$('#cbo_lc_currency_id').attr('disabled','true')".";\n";
		echo "$('#cbo_pi_currency_id').attr('disabled','true')".";\n";
		echo "document.getElementById('cbo_issuing_bank').value			= '".$row[csf("issuing_bank_id")]."';\n";
		echo "document.getElementById('cbo_item_category_id').value		= '".$row[csf("item_category_id")]."';\n";
		echo "document.getElementById('txt_tenor').value				= '".$row[csf("tenor")]."';\n";
		echo "document.getElementById('txt_tolerance').value			= '".$row[csf("tolerance")]."';\n";
		echo "document.getElementById('cbo_inco_term_id').value 		= '".$row[csf("inco_term_id")]."';\n";
		echo "document.getElementById('txt_inco_term_place').value 		= '".$row[csf("inco_term_place")]."';\n";
		echo "document.getElementById('cbo_delevery_mode').value 		= '".$row[csf("delivery_mode_id")]."';\n";
		echo "document.getElementById('txt_etd_date').value 			= '".$etd_date."';\n";
		echo "document.getElementById('txt_insurance_company').value 	= '".$row[csf("insurance_company_name")]."';\n";
		echo "document.getElementById('txt_lca_no').value 				= '".$row[csf("lca_no")]."';\n";
		echo "document.getElementById('txt_lcaf_no').value 				= '".$row[csf("lcaf_no")]."';\n";
		echo "document.getElementById('txt_imp_form_no').value 			= '".$row[csf("imp_form_no")]."';\n";
		echo "document.getElementById('txt_psi_company').value 			= '".$row[csf("psi_company")]."';\n";
		echo "document.getElementById('txt_cover_note_no').value 		= '".$row[csf("cover_note_no")]."';\n";
		echo "document.getElementById('txt_cover_note_date').value 		= '".$cover_note_date."';\n";
		echo "document.getElementById('cbo_maturit_from_id').value 		= '".$row[csf("maturity_from_id")]."';\n";
		echo "document.getElementById('txt_margin_deposit').value 		= '".$row[csf("margin")]."';\n";
		echo "document.getElementById('cbo_origin_id').value 			= '".$row[csf("origin")]."';\n";
		echo "document.getElementById('cbo_bond_warehouse_id').value 	= '".$row[csf("bonded_warehouse")]."';\n";
		echo "document.getElementById('txt_gmt_qnty').value 			= '".$row[csf("garments_qty")]."';\n";
		echo "document.getElementById('cbo_credit_advice_id').value 	= '".$row[csf("credit_to_be_advised")]."';\n";
		echo "document.getElementById('cbo_partial_ship_id').value 		= '".$row[csf("partial_shipment")]."';\n";
		echo "document.getElementById('cbo_transhipment_id').value 		= '".$row[csf("transhipment")]."';\n";
		echo "document.getElementById('txt_shiping_mark').value 		= '".$row[csf("shipping_mark")]."';\n";
		echo "document.getElementById('txt_doc_perc_days').value 		= '".$row[csf("doc_presentation_days")]."';\n";
		echo "document.getElementById('txt_port_loading').value 		= '".$row[csf("port_of_loading")]."';\n";
		echo "document.getElementById('txt_port_discharge').value 		= '".$row[csf("port_of_discharge")]."';\n";
		echo "document.getElementById('txt_remarks').value 				= '".$row[csf("remarks")]."';\n";
		echo "document.getElementById('txt_hidden_pi_id').value 		= '".$row[csf("pi_id")]."';\n";
		echo "document.getElementById('txt_pi_value').value 			= '".$row[csf("pi_value")]."';\n";
		echo "document.getElementById('cbo_payterm_id').value 			= '".$row[csf("payterm_id")]."';\n";
		echo "document.getElementById('cbo_gmt_uom_id').value 			= '".$row[csf("uom_id")]."';\n";
		echo "document.getElementById('txt_ud_no').value 				= '".$row[csf("ud_no")]."';\n";
		echo "document.getElementById('txt_ud_date').value 				= '".$ud_date."';\n";
		echo "document.getElementById('cbo_add_confirm_id').value 	    = '".$row[csf("credit_advice_id")]."';\n";
		echo "document.getElementById('txt_conf_bank').value 			= '".$row[csf("confirming_bank")]."';\n";
		echo "document.getElementById('cbo_status').value 				= '".$row[csf("status_active")]."';\n";
		echo "document.getElementById('txt_upas_rate').value 			= '".$row[csf("upas_rate")]."';\n";
		echo "document.getElementById('cbo_adv_bank').value 			= '".$row[csf("advising_bank")]."';\n";
		echo "document.getElementById('txt_adv_bank_address').value 	= '".$row[csf("advising_bank_address")]."';\n";
		echo "document.getElementById('txt_lc_reference_no').value 	    = '".$row[csf("lc_reference_no")]."';\n";
		echo "document.getElementById('txt_expiry_days').value 	    	= '".$row[csf("lc_expiry_days")]."';\n";
		echo "document.getElementById('txt_internal_file_no').value 	    	= '".$InternalFileNoArr[$row["ID"]]["INTERNAL_FILE_NO"]."';\n";


		if($row[csf("pi_id")]!="")
		{
			if($db_type==0)
			{
				$pi_no=return_field_value("group_concat(pi_number)","com_pi_master_details","id in(".$row[csf("pi_id")].") and status_active=1 and is_deleted=0");
			}
			else
			{
				//$pi_no=return_field_value("LISTAGG(cast(pi_number as varchar2(4000)), ',') WITHIN GROUP (ORDER BY id) as pi_number","com_pi_master_details","id in(".$row[csf("pi_id")].") and status_active=1 and is_deleted=0","pi_number");
				$pi_no = return_field_value("rtrim(xmlagg(xmlelement(e,pi_number,',').extract('//text()') order by pi_number).GetClobVal(),',') AS pi_number ", "com_pi_master_details", " id in(".$row[csf("pi_id")].") and status_active=1 and is_deleted=0", "pi_number");
            	$pi_no = $pi_no->load();
			}
		}
		else
		{
			$pi_no="";
		}

		echo "document.getElementById('txt_pi').value 				= '".$pi_no."';\n";

		$lc_amnd=return_field_value("count(id) as id","com_btb_lc_amendment","btb_id=$data and is_original=0 and status_active=1 and is_deleted=0","id");
		if($lc_amnd>0)
		{
			echo "disable_enable_fields('txt_last_shipment_date*txt_lc_expiry_date*cbo_delevery_mode*cbo_inco_term_id*txt_inco_term_place*cbo_delevery_mode*txt_port_of_loading*txt_port_of_discharge*cbo_payterm_id*txt_tenor*cbo_lc_basis_id*txt_pi*txt_remarks',1);\n";
		}
		else
		{
			echo "disable_enable_fields('txt_last_shipment_date*txt_lc_expiry_date*cbo_delevery_mode*cbo_inco_term_id*txt_inco_term_place*cbo_delevery_mode*txt_port_of_loading*txt_port_of_discharge*cbo_payterm_id*txt_tenor*cbo_lc_basis_id*txt_pi*txt_remarks',0);\n";
		}
		$lc_acceptance=return_field_value("id","com_import_invoice_dtls","btb_lc_id=$data and status_active=1 and is_deleted=0","id");
		if($lc_acceptance!="")
		{
			echo "$('#cbo_payterm_id').attr('disabled','true')".";\n";
			echo "$('#cbo_maturit_from_id').attr('disabled','true')".";\n";
			echo "$('#cbo_issuing_bank').attr('disabled','true')".";\n";
		}
		else
		{
			echo "$('#cbo_payterm_id').attr('disabled','false')".";\n";
			echo "$('#cbo_maturit_from_id').attr('disabled','false')".";\n";
			echo "$('#cbo_issuing_bank').attr('disabled','false')".";\n";
		}
		
		echo "document.getElementById('txt_hidden_pi_item').value 		= '".$row[csf("pi_entry_form")]."';\n";
		//echo "document.getElementById('txt_hidden_pi_item').value 		= '1';\n";
		//echo "$('#txt_hidden_pi_item').attr('disabled','true')".";\n";
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_btb_mst',1);\n";

		exit();
	}
}


if($action=="lc_popup")
{
	echo load_html_head_contents("LC/SC Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $txt_hidden_pi_id;die;
	if($cboBuyerID>0){
		$buyer_cond=" and buy.id=$cboBuyerID";
	}
	?>

	 <script>

	 var selected_id = new Array(); var  selected_name = new Array(); var file_no_arr = new Array(); var file_year_arr = new Array();

		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ )
			{
				$('#tr_'+i).trigger('click');
			}
		}

		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}


		function js_set_value( str ) {
			if (str!="") str=str.split("_");

			if (str[2]==0 && str[6]==1 && (str[8]!=1 && str[8]!=3) )
			{
				alert("Approval Necessity Setup Yes. Please Approved First.");
				return;
			}
			if (str[2]==1 && str[7]==1 && (str[8]!=1 && str[8]!=3))
			{
				alert("Approval Necessity Setup Yes. Please Approved First.");
				return;
			}

			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFCC' );

			var id_type=str[1]+"_"+str[2];
			
			
			if(file_no_arr.length==0)
			{
				file_no_arr.push( str[4] );
			}
			else if( jQuery.inArray( str[4], file_no_arr )==-1)
			{
				alert("File Mix Not Allow");
				return;
			}
			
			if(file_year_arr.length==0)
			{
				file_year_arr.push( str[5] );
			}
			else if( jQuery.inArray( str[5], file_year_arr )==-1)
			{
				alert("File Year Mix Not Allow");
				return;
			}
			
			if( jQuery.inArray( id_type, selected_id ) == -1 ) {
				selected_id.push( id_type );
				selected_name.push( str[3] );

			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == id_type ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}
			var id =''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + '*';
			}
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );

			$('#txt_selected_id').val( id );
			$('#txt_selected').val( name );
		}

		function reset_hide_field(type)
		{
			$('#txt_selected_id').val('');
			$('#txt_selected').val('');
			if(type==1)
			{
				$('#search_div').html( '' );
			}
		}

		function load_search_td(searchByType,companyId)
		{
			if(searchByType==3)
			{
				load_drop_down('btb_margin_lc_controller',companyId, 'load_drop_down_file_number', 'searchText' );
			}
			else
			{
				var x = document.createElement("INPUT");
			    x.setAttribute("type", "text");
			    x.setAttribute("id", "txt_search_text");
			    x.setAttribute("class", "text_boxes");
			    //x.setAttribute("width", "170");
			    $('#searchText').html( x );
			}
		}

		function fn_show_lc_sc()
		{
			if($("#pi_ref_check").is(":checked"))
			{
				var pi_check=1;
			}
			else
			{
				var pi_check=0;
			}
			/*return;*/
			show_list_view ('<? echo $company_id; ?>'+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_text').value+'**'+document.getElementById('cbo_buyer_name').value+'**'+'<? echo $lc_sc; ?>'+'**'+pi_check+'**'+'<? echo $txt_hidden_pi_id; ?>'+'**'+'<? echo $txt_hidden_pi_item; ?>', 'create_lc_search_list_view', 'search_div', 'btb_margin_lc_controller', 'setFilterGrid(\'tbl_list_search\',-1)');
			reset_hide_field(0);
		}
    </script>

	</head>

	<body>
	<div align="center" style="width:100%;" >
		<form name="searchpofrm"  id="searchpofrm">
			<fieldset style="width:820px">
				<table style="margin-top:5px;" width="680" cellspacing="0" border="1" rules="all" cellpadding="0" class="rpt_table">
					<thead>
						<th>Buyer</th>
						<th>Search By</th>
						<th>Search</th>
						<th><input type="checkbox" id="pi_ref_check" name="pi_ref_check" checked />&nbsp;LC From PI&nbsp;<input type="reset" name="button" class="formbutton" value="Reset" onClick="reset_hide_field(1)" style="width:60px;"></th>
					</thead>
					<tr class="general">
						<td align="center">
							<?
								echo create_drop_down( "cbo_buyer_name", 150, "select buy.id, buy.buyer_name from lib_buyer buy where buy.status_active=1 and buy.is_deleted=0 $buyer_cond order by buy.buyer_name","id,buyer_name",1, "-- Select--",0,"",0 );
							?>
						</td>
						<td align="center">
							<?
								$arr=array(1=>'L/C',2=>'S/C',3=>'File No.');
								echo create_drop_down( "cbo_search_by", 150, $arr,"",1, "--- Select ---", '0',"load_search_td(this.value,$company_id)",0 );
							?>

						</td>
						<td align="center" id="searchText">
							<input type="text" name="txt_search_text" id="txt_search_text" class="text_boxes" />
						</td>
						<td align="center">
							<input type="button" name="button" class="formbutton" value="Show" onClick="fn_show_lc_sc()" style="width:100px;" />
							<input type="hidden" name="txt_selected" id="txt_selected" class="text_boxes" readonly />
							<input type="hidden" name="txt_selected_id" id="txt_selected_id" class="text_boxes" readonly />
						</td>
				</tr>
			</table>
			<table width="100%" style="margin-top:5px">
				<tr>
					<td id="search_div"></td>
				</tr>
				<tr>
					<td align="center"><input type="hidden" name="close" onClick="parent.emailwindow.hide();"  class="formbutton" value="Close" /></td>
				</tr>
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


if($action=="create_lc_search_list_view")
{
	$data=explode('**',$data);
	$company=$data[0];
	$search_by = $data[1];
	$search_string =trim($data[2]);
	if($data[3]==0) $buyer_name="%%"; else $buyer_name=$data[3];
	$lc_sc=$data[4];
	$lc_from_pi=$data[5];
	$txt_hidden_pi_id=$data[6];
	$txt_hidden_pi_item=$data[7];
	//echo $lc_from_pi."**".$txt_hidden_pi_id."**".$txt_hidden_pi_item;die;

	$company_details=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$buyer_details=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');


	$lc_id='';
	$sc_id='';
	if($lc_sc!="")
	{
		$lc_sc_id=explode(",",$lc_sc);
		for($s=0;$s<count($lc_sc_id); $s++)
		{
			$sc_lc_all=explode("__",$lc_sc_id[$s]);
			$id=$sc_lc_all[0];
			$type=$sc_lc_all[1];
			if($type==0)
			{
				if($lc_id=="") $lc_id=$id; else $lc_id.=",".$id;
			}
			else
			{
				if($sc_id=="") $sc_id=$id; else $sc_id.=",".$id;
			}
		}
	}

	if($lc_id=="")
	{
		$lc_id_cond='';
	}
	else
	{
		$lc_id_cond="and a.id not in($lc_id)";
	}

	if($sc_id=="")
	{
		$sc_id_cond='';
	}
	else
	{
		$sc_id_cond="and b.id not in($sc_id)";
	}

	// Approval Necessity Setup part
	if ($db_type==2) $app_nes_setup_date=change_date_format(date('d-m-Y'), "", "",1);
    else if ($db_type==0) $app_nes_setup_date=change_date_format(date('d-m-Y'),'yyyy-mm-dd');
    $approval_status_lc="select approval_need, allow_partial from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$company' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '$app_nes_setup_date' and company_id='$company')) and page_id=46 and status_active=1 and is_deleted=0";
    $app_need_setup_lc=sql_select($approval_status_lc);
    $approval_need_lc=$app_need_setup_lc[0][csf("approval_need")];

	$approval_status_sc="select approval_need, allow_partial from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$company' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '$app_nes_setup_date' and company_id='$company')) and page_id=47 and status_active=1 and is_deleted=0";
    $app_need_setup_sc=sql_select($approval_status_sc);
    $approval_need_sc=$app_need_setup_sc[0][csf("approval_need")];	


	if($lc_from_pi==1)
	{
		if($txt_hidden_pi_item!=165 && $txt_hidden_pi_item!=166 && $txt_hidden_pi_item!=167)
		{
			echo "No LC Found Against This Reference"; die;
		}

		/*if($txt_hidden_pi_item==165)
		{
			$order_sql="select c.id as po_id from com_pi_item_details a, wo_non_order_info_dtls b, wo_po_break_down c
			where a.work_order_dtls_id=b.id and b.job_no=c.job_no_mst and a.status_active=1 and b.status_active=1 and a.pi_id in($txt_hidden_pi_id)";
		}
		elseif($txt_hidden_pi_item==166)
		{
			$order_sql="select b.po_break_down_id as po_id from com_pi_item_details a, wo_booking_dtls b
			where a.work_order_no=b.booking_no and b.booking_type=1 and a.status_active=1 and b.status_active=1 and a.pi_id in($txt_hidden_pi_id)";
		}
		else
		{
			$order_sql="select b.po_break_down_id as po_id from com_pi_item_details a, wo_booking_dtls b
			where a.work_order_no=b.booking_no and a.work_order_dtls_id=b.id and b.booking_type=2 and a.status_active=1 and b.status_active=1 and a.pi_id in($txt_hidden_pi_id)";
		}
		//echo $order_sql;die;
		$order_sql_result=sql_select($order_sql);
		if(count($order_sql_result)<1)
		{
			echo "No LC Found Against This Reference"; die;
		}
		$pi_order_id=array();
		foreach($order_sql_result as $row)
		{
			$pi_order_id[$row[csf("po_id")]]=$row[csf("po_id")];
		}
		if($search_string!="") $search_cond_lc=" and a.internal_file_no ='$search_string'";
		if($search_string!="") $search_cond_sc=" and b.internal_file_no ='$search_string'";


		$sql="select b.id as sc_lc_id, b.contract_no as sc_lc_no, b.contract_date as lc_sc_date, b.beneficiary_name as company_name, b.buyer_name as buyer_name, b.contract_value as sc_lc_value, '1' as type
		from com_sales_contract b, com_sales_contract_order_info c
		where b.id=c.com_sales_contract_id and b.beneficiary_name='$company' and b.buyer_name like '$buyer_name' $search_cond_sc and b.status_active=1 and b.is_deleted=0 and c.wo_po_break_down_id in(".implode(",",$pi_order_id).") $sc_id_cond
		group by b.id, b.contract_no, b.contract_date, b.beneficiary_name, b.buyer_name, b.contract_value
		union all
		select a.id as sc_lc_id, a.export_lc_no as sc_lc_no, a.lc_date as lc_sc_date, a.beneficiary_name as company_name, a.buyer_name as buyer_name, a.lc_value as sc_lc_value, '0' as type
		from com_export_lc a, com_export_lc_order_info c
		where a.id=c.com_export_lc_id and a.beneficiary_name='$company' and a.buyer_name like '$buyer_name' $search_cond_lc and a.status_active=1 and a.is_deleted=0 and c.wo_po_break_down_id in(".implode(",",$pi_order_id).") $lc_id_cond
		group by a.id, a.export_lc_no, a.lc_date, a.beneficiary_name, a.buyer_name, a.lc_value";*/

		if($search_string!="") $search_cond_lc=" and a.internal_file_no ='$search_string'";
		if($search_string!="") $search_cond_sc=" and b.internal_file_no ='$search_string'";

		$sql="SELECT a.id as sc_lc_id, a.export_lc_no as sc_lc_no, a.lc_date as lc_sc_date, a.beneficiary_name as company_name, a.buyer_name as buyer_name, a.lc_value as sc_lc_value, '0' as type, a.internal_file_no, a.lc_year, a.approved
		from com_export_lc a, com_pi_master_details c
		where c.lc_sc_id=a.id and c.is_lc_sc=1 and a.beneficiary_name='$company' and a.buyer_name like '$buyer_name' $search_cond_lc and a.status_active=1 and a.is_deleted=0 and c.id in($txt_hidden_pi_id) $lc_id_cond
		group by a.id, a.export_lc_no, a.lc_date, a.beneficiary_name, a.buyer_name, a.lc_value, a.internal_file_no, a.lc_year, a.approved
		union all
		SELECT b.id as sc_lc_id, b.contract_no as sc_lc_no, b.contract_date as lc_sc_date, b.beneficiary_name as company_name, b.buyer_name as buyer_name, b.contract_value as sc_lc_value, '1' as type, b.internal_file_no, b.sc_year as lc_year, b.approved
		from com_sales_contract b, com_pi_master_details c
		where c.lc_sc_id=b.id and c.is_lc_sc=2 and b.beneficiary_name='$company' and b.buyer_name like '$buyer_name' $search_cond_sc and b.status_active=1 and b.is_deleted=0 and c.id in($txt_hidden_pi_id) $sc_id_cond
		group by b.id, b.contract_no, b.contract_date, b.beneficiary_name, b.buyer_name, b.contract_value, b.internal_file_no, b.sc_year, b.approved";
		//echo $sql;// die;
		$sql_results=sql_select($sql);
		if(count($sql_results)<1)
		{
			echo "No LC Found Against This Reference"; die;
		}
	}
	else
	{
		if($search_by==0)
		{
			$sql="select a.id as sc_lc_id, a.export_lc_no as sc_lc_no, a.lc_date as lc_sc_date, a.beneficiary_name as company_name, a.buyer_name as buyer_name, a.lc_value as sc_lc_value, '0' as type, a.internal_file_no, a.lc_year, a.approved from com_export_lc a where a.beneficiary_name='$company' and a.buyer_name like '$buyer_name' and a.export_lc_no like '%$search_string%' and a.status_active=1 and a.is_deleted=0 $lc_id_cond group by a.id, a.export_lc_no, a.lc_date, a.beneficiary_name, a.buyer_name, a.lc_value, a.internal_file_no, a.lc_year, a.approved
			union all
			select b.id as sc_lc_id, b.contract_no as sc_lc_no, b.contract_date as lc_sc_date, b.beneficiary_name as company_name, b.buyer_name as buyer_name, b.contract_value as sc_lc_value, '1' as type, b.internal_file_no, b.sc_year as lc_year, b.approved from com_sales_contract b where b.beneficiary_name='$company' and b.buyer_name like '$buyer_name' and b.contract_no like '%$search_string%' and b.status_active=1 and b.is_deleted=0 $sc_id_cond group by b.id, b.contract_no, b.contract_date, b.beneficiary_name, b.buyer_name, b.contract_value, b.internal_file_no, b.sc_year, b.approved";
		}
		else if($search_by==1)
		{
			$sql="select a.id as sc_lc_id, a.export_lc_no as sc_lc_no, a.lc_date as lc_sc_date, a.beneficiary_name as company_name, a.buyer_name as buyer_name, a.lc_value as sc_lc_value, '0' as type, a.internal_file_no, a.lc_year, a.approved from com_export_lc a where a.beneficiary_name='$company' and a.buyer_name like '$buyer_name' and a.export_lc_no like '%$search_string%' and a.status_active=1 and a.is_deleted=0 $lc_id_cond group by a.id, a.export_lc_no, a.lc_date, a.beneficiary_name, a.buyer_name, a.lc_value, a.internal_file_no, a.lc_year, a.approved";
		}
		else if($search_by==2)
		{
			$sql="select b.id as sc_lc_id, b.contract_no as sc_lc_no, b.contract_date as lc_sc_date, b.beneficiary_name as company_name, b.buyer_name as buyer_name, b.contract_value as sc_lc_value, '1' as type, b.internal_file_no, b.sc_year as lc_year, b.approved from com_sales_contract b where b.beneficiary_name='$company' and b.buyer_name like '$buyer_name' and b.contract_no like '%$search_string%' and b.status_active=1 and b.is_deleted=0 $sc_id_cond group by b.id, b.contract_no, b.contract_date, b.beneficiary_name, b.buyer_name, b.contract_value, b.internal_file_no, b.sc_year, b.approved";
		}
		else if($search_by==3)
		{
			$sql="select b.id as sc_lc_id, b.contract_no as sc_lc_no, b.contract_date as lc_sc_date, b.beneficiary_name as company_name, b.buyer_name as buyer_name, b.contract_value as sc_lc_value, '1' as type, b.internal_file_no, b.sc_year as lc_year, b.approved from com_sales_contract b where b.beneficiary_name='$company' and b.buyer_name like '$buyer_name' and b.internal_file_no ='$search_string' and b.status_active=1 and b.is_deleted=0 $sc_id_cond group by b.id, b.contract_no, b.contract_date, b.beneficiary_name, b.buyer_name, b.contract_value, b.internal_file_no, b.sc_year, b.approved
			union all
			select a.id as sc_lc_id, a.export_lc_no as sc_lc_no, a.lc_date as lc_sc_date, a.beneficiary_name as company_name, a.buyer_name as buyer_name, a.lc_value as sc_lc_value, '0' as type, a.internal_file_no, a.lc_year, a.approved from com_export_lc a where a.beneficiary_name='$company' and a.buyer_name like '$buyer_name' and a.internal_file_no ='$search_string' and a.status_active=1 and a.is_deleted=0 $lc_id_cond group by a.id, a.export_lc_no, a.lc_date, a.beneficiary_name, a.buyer_name, a.lc_value, a.internal_file_no, a.lc_year, a.approved";
		}
	}

	//echo $sql;
	$lc_sc_type_array=array (0=>"LC",1=>"SC");
	$arr=array (2=>$lc_sc_type_array,3=>$company_details,4=>$buyer_details);
	$table_width=820;
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?= $table_width; ?>" class="rpt_table" >
        <thead>
            <th width="40">SL</th>
            <th width="150">LC/SC No</th>
            <th width="80">LC/SC Date</th>
            <th width="80">Type</th>
            <th width="150">Beneficiary</th>
            <th width="150">Buyer</th>
            <th >LC/SC Value</th>
        </thead>
    </table>
    <div style="width:<?= $table_width+20; ?>px; overflow-y:scroll; max-height:280px;" id="buyer_list_view" align="center">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?= $table_width; ?>" class="rpt_table" id="tbl_list_search" >
        <?
            $sql_result=sql_select($sql);
			$i=1; 
            foreach($sql_result as $row)
            {
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="tr_<? echo $i;?>" onClick="js_set_value('<? echo $i."_".$row[csf('sc_lc_id')]."_".$row[csf('type')]."_".$row[csf('sc_lc_no')]."_".$row[csf('internal_file_no')]."_".$row[csf('lc_year')]."_".$approval_need_lc."_".$approval_need_sc."_".$row[csf('approved')];?>')">
					<td width="40" align="center"><? echo $i; ?></td>	
					<td width="150"><p><? echo $row[csf('sc_lc_no')]; ?></p></td>
					<td width="80"><p><? echo change_date_format($row[csf('lc_sc_date')]); ?></p></td>
					<td width="80"><p><? echo $lc_sc_type_array[$row[csf('type')]]; ?></p></td>
					<td width="150"><p><? echo $company_details[$row[csf('company_name')]]; ?></p></td>
					<td width="150"><p><? echo $buyer_details[$row[csf('buyer_name')]]; ?></p></td>
					<td align="right"><p><? echo number_format($row[csf('sc_lc_value')],2); ?></p></td>
				</tr>
				<?
				$i++;
            }
        ?>
        </table>		
    </div>

	<div style="width:100%">
		<div style="width:50%; float:left" align="left">
			<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
		</div>
	<div style="width:50%; float:left" align="left">
		<input type="button" name="close" id="close"  onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
	</div>

	<?
	//echo create_list_view("tbl_list_search", "LC/SC No,LC/SC Date,Type,Beneficiary,Buyer,LC/SC Value", "150,80,80,150,150,150","820","200",0, $sql , "js_set_value", "sc_lc_id,type,sc_lc_no,internal_file_no,lc_year", "", 1, "0,0,type,company_name,buyer_name,0", $arr , "sc_lc_no,lc_sc_date,type,company_name,buyer_name,sc_lc_value", "","",'0,3,0,0,0,2','',1) ;

   exit();
}


if($action=="lc_list_for_attach")
{
	$data=explode("**",$data);
	$explode_data=explode(",",$data[1]);
	$cbo_pi_currency_id=$data[2];
	$txt_lc_date=$data[3];
	$cbo_importer_id=$data[4];
	$lc_id=""; $sc_id="";
	for($s=0;$s<count($explode_data);$s++)
	{
		$ls_sc=explode("_",$explode_data[$s]);
		$ls_sc_type=$ls_sc[1];
		if($ls_sc_type==0)
		{
			if($lc_id=="") $lc_id=$ls_sc[0]; else $lc_id.=",".$ls_sc[0];
		}
		else
		{
			if($sc_id=="") $sc_id=$ls_sc[0]; else $sc_id.=",".$ls_sc[0];
		}
	}

	if($lc_id!='') $lc_query = "SELECT id as sc_lc_id, export_lc_no as lc_sc_no, buyer_name, lc_value as value, '0' as type,import_btb, max_btb_limit FROM com_export_lc WHERE id in($lc_id)";
	if($sc_id!='') $sc_query = "SELECT id as sc_lc_id, contract_no as lc_sc_no, buyer_name, contract_value as value, '1' as type,0 as import_btb, max_btb_limit FROM com_sales_contract WHERE id in($sc_id)";

	if($lc_id!='' && $sc_id!='') $union="union all"; else $union="";

	$sql="$lc_query $union $sc_query";

	$lc_sc_sql=sql_select($sql);
	$count_row=count($lc_sc_sql);
	$table_row=$data[0];

	$buyer_library = return_library_array('SELECT id, buyer_name FROM lib_buyer','id','buyer_name');
	$comp_library = return_library_array('SELECT id, company_name FROM lib_company','id','company_name');
	if($count_row>0)
	{
		foreach($lc_sc_sql as $row_lc)
		{
			$cumulative_value=0;
			if($row_lc[csf("type")]=='0')
			{
				$fag = 'L/C';
				//$cumulative_value = return_field_value("sum(current_distribution)","com_btb_export_lc_attachment","lc_sc_id='".$row_lc[csf("sc_lc_id")]."' and is_lc_sc=0 and status_active=1 and is_deleted=0");
				$cumulative_sql = sql_select("select a.id as btb_id, a.currency_id, a.lc_date, a.importer_id, sum(b.current_distribution) as current_distribution 
				from com_btb_lc_master_details a, com_btb_export_lc_attachment b 
				where a.id=b.import_mst_id and b.lc_sc_id='".$row_lc[csf("sc_lc_id")]."' and b.is_lc_sc=0 and b.status_active=1 and b.is_deleted=0
				group by a.id, a.currency_id, a.lc_date, a.importer_id");
			}
			else
			{
				$fag = 'S/C';
				$cumulative_sql = sql_select("select a.id as btb_id, a.currency_id, a.lc_date, a.importer_id, sum(b.current_distribution) as current_distribution 
				from com_btb_lc_master_details a, com_btb_export_lc_attachment b 
				where a.id=b.import_mst_id and b.lc_sc_id='".$row_lc[csf("sc_lc_id")]."' and b.is_lc_sc=1 and b.status_active=1 and b.is_deleted=0
				group by a.id, a.currency_id, a.lc_date, a.importer_id");
			}
			
			foreach($cumulative_sql as $row)
			{
				if($row[csf("currency_id")]==1)
				{
					$exchange_rate=set_conversion_rate(2, $row[csf("lc_date")],$row[csf("importer_id")]);
					$cumulative_value+=$row[csf("current_distribution")]/$exchange_rate;
				}
				else
				{
					$cumulative_value+=$row[csf("current_distribution")];
				}
			}
			
			$exchange_rate=1;
			if($cbo_pi_currency_id==1)
			{
				$exchange_rate=set_conversion_rate(2, $txt_lc_date,$cbo_importer_id);
			}
			

			if($row_lc[csf("import_btb")] == 1)
			{
				$comp_buyer = $comp_library[$row_lc[csf("buyer_name")]];
			}else
			{
				$comp_buyer = $buyer_library[$row_lc[csf("buyer_name")]];
			}

			$table_row++;
			?>
			<tr class="general" id="tr_<? echo $table_row; ?>">
				<td>
				<input type="text" name="txtlcsc_<? echo $table_row; ?>" id="txtlcsc_<? echo $table_row; ?>" class="text_boxes" style="width:100px"  onDblClick= "openmypage(3,<? echo $table_row; ?>)" value="<? echo $row_lc[csf("lc_sc_no")]; ?>" readonly= "readonly" placeholder="Double For Search" />
				 <input type="hidden" name="txtLcScid_<? echo $table_row; ?>" id="txtLcScid_<? echo $table_row; ?>" class="text_boxes" style="width:100px" value="<? echo $row_lc[csf("sc_lc_id")]; ?>" readonly= "readonly" />
                 <input type="hidden" name="txtLcScidType_<? echo $table_row; ?>" id="txtLcScidType_<? echo $table_row; ?>" class="text_boxes" style="width:100px" value="<? echo $row_lc[csf("type")]; ?>" readonly= "readonly" />
                 <input type="hidden" name="maxBtbLimit_<? echo $table_row; ?>" id="maxBtbLimit_<? echo $table_row; ?>" class="text_boxes" style="width:100px" value="<? echo $row_lc[csf("max_btb_limit")]; ?>" readonly= "readonly" />
				</td>
				<td><input type="text" name="txtbuyer_<? echo $table_row; ?>" id="txtbuyer_<? echo $table_row; ?>" class="text_boxes" style="width:120px;" value="<? echo $comp_buyer;//$buyer_library[$row_lc[csf("buyer_name")]]; ?>" readonly= "readonly" /><input type="hidden" name="cboBuyerID" id="cboBuyerID" value="<? echo $row_lc[csf("buyer_name")] ?>" > </td>
				<td><input type="text" name="txtlcscflag_<? echo $table_row; ?>" id="txtlcscflag_<? echo $table_row; ?>" class="text_boxes" style="width:70px;" value="<? echo $fag; ?>" readonly= "readonly" />
                	<input type="hidden" name="txtlcscflagId_<? echo $table_row; ?>" id="txtlcscflagId_<? echo $table_row; ?>" class="text_boxes" style="width:100px" value="<? echo $row_lc[csf("type")]; ?>" readonly= "readonly" />
                </td>
				<td><input type="text" name="txtlcscvalue_<? echo $table_row; ?>" id="txtlcscvalue_<? echo $table_row; ?>" class="text_boxes_numeric" style="width:120px;" value="<? echo $row_lc[csf("value")]; ?>" readonly/></td>
				<td>
                <input type="text" name="txtcurdistribution[]" id="txtcurdistribution_<? echo $table_row; ?>" class="text_boxes_numeric" style="width:120px" value="" onKeyUp="distribution_value(0,<? echo $table_row; ?>)" disabled="disabled" />
                <input type="hidden" name="hidecurdistribution_<? echo $table_row; ?>" id="hidecurdistribution_<? echo $table_row; ?>" class="text_boxes_numeric" style="width:120px" value="" readonly />
                </td>
				<td>
				<input type="text" name="txtcumudistribution_<? echo $table_row; ?>" id="txtcumudistribution_<? echo $table_row; ?>" class="text_boxes_numeric" style="width:120px" value="<? echo number_format($cumulative_value,2,'.','');?>"  readonly="readonly" />
				<input type="hidden" name="hiddencumudistribution_<? echo $table_row; ?>" id="hiddencumudistribution_<? echo $table_row; ?>" class="text_boxes_numeric" style="width:120px" value="<? echo $cumulative_value;?>" readonly />
                <input type="hidden" name="hiddenExchangeRate_<? echo $table_row; ?>" id="hiddenExchangeRate_<? echo $table_row; ?>" class="text_boxes_numeric" style="width:120px" value="<? echo $exchange_rate;?>" readonly />
				</td>
				<td><input type="text" name="txtoccupied_<? echo $table_row; ?>" id="txtoccupied_<? echo $table_row; ?>" class="text_boxes_numeric" style="width:100px" value="" readonly= "readonly"/></td>
				<td>
					<?
						 echo create_drop_down( "cbostatus_".$table_row, 100, $row_status,"", 0, "", 1, "" );
					?>
				</td>
				<td width="65">
					<input type="button" id="increase_<? echo $table_row; ?>" name="increase_<? echo $table_row; ?>" style="width:25px" class="formbuttonplasminus" value="+" onClick="fn_add_row_lc(<? echo $table_row; ?>)" />
					<input type="button" id="decrease_<? echo $table_row; ?>" name="decrease_<? echo $table_row; ?>" style="width:25px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $table_row; ?>);" />
                    <input type="hidden" name="txtcaltype_<? echo $table_row; ?>" id="txtcaltype_<? echo $table_row; ?>" class="text_boxes" style="width:100px" value="2" readonly= "readonly" />
			   </td>
			</tr>
			<?

		}//end foreach
	}
	else
	{
		if($table_row==0)
		{
		?>
            <tr class="general" id="tr_1">
                <td>
                <input type="text" name="txtlcsc_1" id="txtlcsc_1" class="text_boxes" style="width:100px"  onDblClick= "openmypage(3,1)" readonly= "readonly" placeholder="Double For Search" />
                <input type="hidden" name="txtLcScid_1" id="txtLcScid_1" class="text_boxes" style="width:100px" value="" readonly= "readonly" />
                <input type="hidden" name="txtLcScidType_1" id="txtLcScidType_1" class="text_boxes" style="width:100px" value="" readonly= "readonly" />
                <input type="hidden" name="maxBtbLimit_1" id="maxBtbLimit_1" class="text_boxes" style="width:100px" value="0" readonly= "readonly" />
                </td>
                <td><input type="text" name="txtbuyer_1" id="txtbuyer_1" class="text_boxes" style="width:120px;" readonly= "readonly" /></td>
                <td><input type="text" name="txtlcscflag_1" id="txtlcscflag_1" class="text_boxes" style="width:70px;" readonly= "readonly" /></td>
                <td>
                <input type="text" name="txtlcscvalue_1" id="txtlcscvalue_1" class="text_boxes_numeric" style="width:120px;" readonly= "readonly"/>
                <input type="hidden" name="txtlcscflagId_1" id="txtlcscflagId_1" class="text_boxes" style="width:100px" value="" readonly= "readonly" />
                </td>
                <td>
                <input type="text" name="txtcurdistribution[]" id="txtcurdistribution_1" class="text_boxes_numeric" style="width:120px" onKeyUp="distribution_value(0,1)" disabled="disabled" />
                <input type="hidden" name="hidecurdistribution_1" id="hidecurdistribution_1" class="text_boxes_numeric" style="width:120px" readonly/>
                </td>
                <td>
                <input type="text" name="txtcumudistribution_1" id="txtcumudistribution_1" class="text_boxes_numeric" style="width:120px"   readonly="readonly" />
                <input type="hidden" name="hiddencumudistribution_1" id="hiddencumudistribution_1" class="text_boxes_numeric" style="width:120px" readonly />
                <input type="hidden" name="hiddenExchangeRate_1" id="hiddenExchangeRate_1" class="text_boxes_numeric" style="width:120px" value="1" readonly />
                </td>
                <td><input type="text" name="txtoccupied_1" id="txtoccupied_1"  style="width:100px" class="text_boxes_numeric" readonly= "readonly"/></td>

                <td>
                    <?
                         echo create_drop_down( "cbostatus_1", 100, $row_status,"", 0, "", 1, "" );
                    ?>
                </td>
                <td width="65">
                    <input type="button" id="increase_1" name="increase_1" style="width:25px" class="formbuttonplasminus" value="+" onClick="fn_add_row_lc(0)" />
                    <input type="button" id="decrease_1" name="decrease_1" style="width:25px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(0);" />
                    <input type="hidden" name="txtcaltype_1" id="txtcaltype_1" class="text_boxes" style="width:100px" value="0" readonly= "readonly" />
               </td>
            </tr>
		<?
		}
	}

	exit();
}


if($action=="show_lc_listview")
{
	$update_id = $data;
	$sql="select a.currency_id, a.lc_date, a.importer_id, b.lc_sc_id, b.is_lc_sc, b.current_distribution, b.status_active from com_btb_lc_master_details a, com_btb_export_lc_attachment b where a.id=b.import_mst_id and b.import_mst_id=$update_id and b.is_deleted=0 and b.status_active=1";
	$lc_sc_sql=sql_select($sql);
	$count_row=count($lc_sc_sql);

	$table_row=0;
	$buyer_library = return_library_array('SELECT id, buyer_name FROM lib_buyer','id','buyer_name');
	$comp_library = return_library_array('SELECT id, company_name FROM lib_company','id','company_name');

	if($count_row>0)
	{
		foreach($lc_sc_sql as $row_lc)
		{
			if($row_lc[csf("is_lc_sc")]==0)
			{
				$cumulative_value = return_field_value("sum(current_distribution)","com_btb_export_lc_attachment","lc_sc_id='".$row_lc[csf("lc_sc_id")]."' and status_active=1 and is_deleted=0 and is_lc_sc=0");
				$hide_cumulative_value= return_field_value("sum(current_distribution)","com_btb_export_lc_attachment","lc_sc_id='".$row_lc[csf("lc_sc_id")]."' and import_mst_id<>$update_id and status_active=1 and is_deleted=0 and is_lc_sc=0");
				$sc_lc = 'L/C';

				$sql_sc_lc="select export_lc_no as lc_sc_no, buyer_name, lc_value as value,import_btb from com_export_lc where id='".$row_lc[csf("lc_sc_id")]."'";
			}
			else
			{
				$cumulative_value = return_field_value("sum(current_distribution)","com_btb_export_lc_attachment","lc_sc_id='".$row_lc[csf("lc_sc_id")]."' and status_active=1 and is_deleted=0 and is_lc_sc=1");
				$hide_cumulative_value = return_field_value("sum(current_distribution)","com_btb_export_lc_attachment","lc_sc_id='".$row_lc[csf("lc_sc_id")]."' and import_mst_id<>$update_id and status_active=1 and is_deleted=0 and is_lc_sc=1");
				$sc_lc = 'S/C';

				$sql_sc_lc="select contract_no as lc_sc_no, buyer_name, contract_value as value, 0 as import_btb from com_sales_contract where id='".$row_lc[csf("lc_sc_id")]."'";
			}
			$exchange_rate=1;
			if($row_lc[csf("currency_id")]==1)
			{
				$exchange_rate=set_conversion_rate(2, $row_lc[csf("lc_date")],$row_lc[csf("importer_id")]);
				$cumulative_value=$cumulative_value/$exchange_rate;
			}
			

			$query_res=sql_select($sql_sc_lc);

			if($query_res[0][csf("import_btb")] == 1)
			{
				$comp_buyer =  $comp_library[$query_res[0][csf("buyer_name")]];
			}
			else
			{
				$comp_buyer = $buyer_library[$query_res[0][csf("buyer_name")]];
			}

			$table_row++;
			?>
			<tr class="general" id="tr_<? echo $table_row; ?>">
				<td>
				<input type="text" name="txtlcsc_<? echo $table_row; ?>" id="txtlcsc_<? echo $table_row; ?>" class="text_boxes" style="width:100px"  onDblClick= "openmypage(3,<? echo $table_row; ?>)" value="<? echo $query_res[0][csf("lc_sc_no")]; ?>" readonly= "readonly" placeholder="Double For Search" />
				 <input type="hidden" name="txtLcScid_<? echo $table_row; ?>" id="txtLcScid_<? echo $table_row; ?>" class="text_boxes" style="width:100px" value="<? echo $row_lc[csf("lc_sc_id")]; ?>" readonly= "readonly" />
				</td>
				<td><input type="text" name="txtbuyer_<? echo $table_row; ?>" id="txtbuyer_<? echo $table_row; ?>" class="text_boxes" style="width:120px;" value="<? echo $comp_buyer;//$buyer_library[$query_res[0][csf("buyer_name")]]; ?>" readonly= "readonly" />
				<input type="hidden" name="cboBuyerID" id="cboBuyerID" value="<? echo $query_res[0][csf("buyer_name")] ?>" >
			   </td>
				<td><input type="text" name="txtlcscflag_<? echo $table_row; ?>" id="txtlcscflag_<? echo $table_row; ?>" class="text_boxes" style="width:70px;" value="<? echo $sc_lc; ?>" readonly= "readonly" />
					<input type="hidden" name="txtlcscflagId_<? echo $table_row; ?>" id="txtlcscflagId_<? echo $table_row; ?>" class="text_boxes" style="width:100px" value="<? echo $row_lc[csf("is_lc_sc")]; ?>" readonly= "readonly" />
				</td>
				<td><input type="text" name="txtlcscvalue_<? echo $table_row; ?>" id="txtlcscvalue_<? echo $table_row; ?>" class="text_boxes_numeric" style="width:120px;" value="<? echo $query_res[0][csf("value")]; ?>" readonly= "readonly"/></td>
				<td>
                <input type="text" name="txtcurdistribution[]" id="txtcurdistribution_<? echo $table_row; ?>" class="text_boxes_numeric" style="width:120px" value="<? echo $row_lc[csf("current_distribution")]; ?>" disabled="disabled" onKeyUp="distribution_value(0,<? echo $table_row; ?>)"/>
                <input type="hidden" name="hidecurdistribution_<? echo $table_row; ?>" id="hidecurdistribution_<? echo $table_row; ?>" class="text_boxes_numeric" style="width:120px" value="<? echo $row_lc[csf("current_distribution")]; ?>" readonly/>
                </td>
				<td>
				<input type="text" name="txtcumudistribution_<? echo $table_row; ?>" id="txtcumudistribution_<? echo $table_row; ?>" class="text_boxes_numeric" style="width:120px" value="<? echo number_format($cumulative_value,2,'.','');?>" readonly/>
				<input type="hidden" name="hiddencumudistribution_<? echo $table_row; ?>" id="hiddencumudistribution_<? echo $table_row; ?>" class="text_boxes_numeric" style="width:120px" value="<? echo $hide_cumulative_value;?>" />
                <input type="hidden" name="hiddenExchangeRate_<? echo $table_row; ?>" id="hiddenExchangeRate_<? echo $table_row; ?>" class="text_boxes_numeric" style="width:120px" value="<? echo $exchange_rate;?>" readonly />
				</td>
				<td>
                <input type="text" name="txtoccupied_<? echo $table_row; ?>" id="txtoccupied_<? echo $table_row; ?>" class="text_boxes_numeric" style="width:100px" value="" readonly= "readonly"/>
                </td>
				<td>
					<?
						 echo create_drop_down( "cbostatus_".$table_row, 100, $row_status,"", 0, "", $row_lc[csf("status_active")], "" );
					?>
				</td>
				<td width="65">
					<input type="button" id="increase_<? echo $table_row; ?>" name="increase_<? echo $table_row; ?>" style="width:25px" class="formbuttonplasminus" value="+" onClick="fn_add_row_lc(<? echo $table_row; ?>)" />
					<input type="button" id="decrease_<? echo $table_row; ?>" name="decrease_<? echo $table_row; ?>" style="width:25px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $table_row; ?>);" />
                    <input type="hidden" name="txtcaltype_<? echo $table_row; ?>" id="txtcaltype_<? echo $table_row; ?>" class="text_boxes" style="width:100px" value="1" readonly= "readonly" />
			   </td>
			</tr>
			<?

		}//end foreach
	}
	else
	{
		?>
		<tr class="general" id="tr_1">
			<td>
			<input type="text" name="txtlcsc_1" id="txtlcsc_1" class="text_boxes" style="width:100px"  onDblClick= "openmypage(3,1)" readonly= "readonly" placeholder="Double For Search" />
			<input type="hidden" name="txtLcScid_1" id="txtLcScid_1" class="text_boxes" style="width:100px" value="" readonly= "readonly" />
			<input type="hidden" name="txtLcScidType_1" id="txtLcScidType_1" class="text_boxes" style="width:100px" value="" readonly= "readonly" />
			</td>
			<td><input type="text" name="txtbuyer_1" id="txtbuyer_1" class="text_boxes" style="width:120px;" readonly= "readonly" />
			<input type="hidden" name="cboBuyerID" id="cboBuyerID" value="" >
			</td>
			<td>
            <input type="text" name="txtlcscflag_1" id="txtlcscflag_1" class="text_boxes" style="width:70px;" readonly= "readonly" />
            <input type="hidden" name="txtlcscflagId_1" id="txtlcscflagId_1" class="text_boxes" style="width:100px" value="" readonly= "readonly" />
            </td>
			<td><input type="text" name="txtlcscvalue_1" id="txtlcscvalue_1" class="text_boxes_numeric" style="width:120px;" readonly= "readonly"/></td>
			<td>
            <input type="text" name="txtcurdistribution[]" id="txtcurdistribution_1" class="text_boxes_numeric" style="width:120px" disabled="disabled" onKeyUp="distribution_value(0,1)"/>
            <input type="hidden" name="hidecurdistribution_1" id="hidecurdistribution_1" class="text_boxes_numeric" style="width:120px" value="" readonly/>
            </td>
			<td>
			<input type="text" name="txtcumudistribution_1" id="txtcumudistribution_1" class="text_boxes_numeric" style="width:120px" readonly/>
			<input type="hidden" name="hiddencumudistribution_1" id="hiddencumudistribution_1" class="text_boxes_numeric" style="width:120px" readonly/>
            <input type="hidden" name="hiddenExchangeRate_1" id="hiddenExchangeRate_1" class="text_boxes_numeric" style="width:120px" value="<? ?>" readonly />
			</td>
			<td><input type="text" name="txtoccupied_1" id="txtoccupied_1"  style="width:100px" class="text_boxes_numeric" readonly/></td>

			<td>
				<?
					 echo create_drop_down( "cbostatus_1", 100, $row_status,"", 0, "", 1, "" );
				?>
			</td>
			<td width="65">
				<input type="button" id="increase_1" name="increase_1" style="width:25px" class="formbuttonplasminus" value="+" onClick="fn_add_row_lc()" />
				<input type="button" id="decrease_1" name="decrease_1" style="width:25px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(1);" />
                <input type="hidden" name="txtcaltype_1" id="txtcaltype_1" class="text_boxes" style="width:100px" value="0" readonly= "readonly" />
		   </td>
		</tr>
		<?
	}

	exit();
}

if ($action == 'show_without_lc_number') 
{
	$bank_arr = return_library_array('SELECT id, bank_name FROM lib_bank','id','bank_name');
	$sql = "SELECT id as ID, btb_system_id as BTB_SYSTEM_ID, importer_id as IMPORTER_ID, issuing_bank_id as ISSUING_BANK_ID, lc_value as LC_VALUE, ref_closing_status as REF_CLOSING_STATUS 
	FROM com_btb_lc_master_details 
	WHERE importer_id = '".$data."' and is_deleted = 0 and lc_number is null";
	// echo $sql;die;
	$data_array = sql_select($sql);

	?>
	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="350">
		<thead>
			<th width="20" align="center">SL</th>
			<th width="110">System ID</th>
			<th width="100">Issuing Bank</th>
			<th>L/C Value</th>
		</thead>
	</table>
	<div style="width:350px; max-height:230px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="332" class="rpt_table" id="tbl_list_search_without_lc">
			<?
			$i = 1;
			foreach ($data_array as $row) 
			{
				if ($i % 2 == 0)
					$bgcolor = "#E9F3FF";
				else
					$bgcolor = "#FFFFFF";

				?>
				<tr bgcolor="<? echo $bgcolor; ?>"
					onClick='fnc_set_form_data("<? echo $row['ID'].'**'.$row['REF_CLOSING_STATUS'];?>")'
					style="cursor:pointer">
					<td width="20" align="center"><? echo $i; ?></td>
					<td width="110"><? echo $row['BTB_SYSTEM_ID']; ?></td>
					<td width="100"><? echo $bank_arr[$row['ISSUING_BANK_ID']]; ?></td>
					<td align="right">
						<? echo $row['LC_VALUE']; ?>
					</td>
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

if($action=="undertaking_letter_print")
{
	$data=explode("**",$data);
	//print_r($data);die;
	//echo load_html_head_contents("BTB Import Lc Letter","../../", 1, 1, $unicode,'','');
	$sql_bank_info=sql_select("select id, contact_person, bank_name, branch_name, address from lib_bank ");
	foreach($sql_bank_info as $row)
	{
		$bank_dtls_arr[$row[csf("id")]]["contact_person"]=$row[csf("contact_person")];
		$bank_dtls_arr[$row[csf("id")]]["bank_name"]=$row[csf("bank_name")];
		$bank_dtls_arr[$row[csf("id")]]["branch_name"]=$row[csf("branch_name")];
		$bank_dtls_arr[$row[csf("id")]]["address"]=$row[csf("address")];
	}
	$sql_lc=sql_select("SELECT id,export_lc_no,tenor FROM com_btb_lc_master_details ");
	foreach($sql_lc as $row)
	{
		$export_lc_no_arr[$row[csf("id")]]["export_lc_no"]=$row[csf("export_lc_no")];
		$export_lc_no_arr[$row[csf("id")]]["tenor"]=$row[csf("tenor")];
	}
	$sql_sc=sql_select("SELECT id,contract_no,tenor FROM com_sales_contract ");
	foreach($sql_sc as $row)
	{
		$export_sc_no_arr[$row[csf("id")]]["contract_no"]=$row[csf("contract_no")];
		$export_sc_no_arr[$row[csf("id")]]["tenor"]=$row[csf("tenor")];
	}

	//echo $data."jahid";die;
	if($db_type==2)
	{
		$sql_com="select a.btb_system_id, a.application_date, a.importer_id, a.item_category_id, a.origin, a.issuing_bank_id, a.currency_id, a.supplier_id, a.lc_value, a.pi_value, a.margin, listagg(cast(b.is_lc_sc || '__' || b.lc_sc_id as varchar(4000)),',') within group (order by b.is_lc_sc,b.lc_sc_id) as lc_sc,a.last_shipment_date  from com_btb_lc_master_details a, com_btb_export_lc_attachment b where a.id=b.import_mst_id and  a.id=$data[2] and a.is_deleted = 0 AND a.status_active = 1 group by a.btb_system_id, a.application_date, a.importer_id, a.item_category_id, a.origin, a.issuing_bank_id, a.currency_id, a.supplier_id, a.lc_value, a.margin, a.pi_value,a.last_shipment_date";
	}
	elseif($db_type==0)
	{
		$sql_com="select a.btb_system_id, a.application_date, a.importer_id, a.item_category_id, a.origin, a.issuing_bank_id, a.currency_id, a.supplier_id, a.lc_value, a.pi_value, a.margin, group_concat(concat(b.is_lc_sc, '__', b.lc_sc_id)) as lc_sc, a.last_shipment_date  from com_btb_lc_master_details a, com_btb_export_lc_attachment b where a.id=b.import_mst_id and  a.id=$data[2] and a.is_deleted = 0 AND a.status_active = 1 group by a.btb_system_id, a.application_date, a.importer_id, a.item_category_id, a.supplier_id, a.origin, a.issuing_bank_id, a.currency_id, a.lc_value, a.margin, a.pi_value, a.last_shipment_date";
	}
	//echo $sql_com;
	$result=sql_select($sql_com);

	$sql_company_address = "select id, company_name, plot_no, level_no, road_no, block_no, province, city, zip_code, bang_bank_reg_no, country_id, irc_no from lib_company where id=".$result[0][csf('importer_id')];

	$company_address_result= sql_select($sql_company_address);
	foreach ($company_address_result as $row) {
		$company["id"] = $row[csf("id")];
		$company["company_name"] = $row[csf("company_name")];
		$company["plot_no"] = $row[csf("plot_no")];
		$company["level_no"] = $row[csf("level_no")];
		$company["road_no"] = $row[csf("road_no")];
		$company["block_no"] = $row[csf("block_no")];
		$company["province"] = $row[csf("province")];
		$company["city"] = $row[csf("city")];
		$company["zip_code"] = $row[csf("zip_code")];
		$company["bang_bank_reg_no"] = $row[csf("bang_bank_reg_no")];
		$company["irc_no"] = $row[csf("irc_no")];
		$company["country_id"] = $row[csf("country_id")];
	}

	//print_r($company);

	$company_name = return_field_value("company_name","lib_company","id=".$result[0][csf("importer_id")],"company_name");
	$supplier_name = return_field_value("supplier_name","lib_supplier","id=".$result[0][csf("supplier_id")],"supplier_name");
	$supplier_address_1 = return_field_value("address_1","lib_supplier","id=".$result[0][csf("supplier_id")],"address_1");
	$supplier_address_2 = return_field_value("address_2","lib_supplier","id=".$result[0][csf("supplier_id")],"address_2");
	$country_name = return_field_value("country_name"," lib_country","id=".$result[0][csf("origin")],"country_name");
	$importer_country_name = return_library_array("select id,country_name from lib_country","id","country_name");

	$pi_beneficiary_arr=return_library_array( "select id, beneficiary from com_pi_master_details where id in(".$result[0][csf("pi_id")].")  AND status_active = 1 AND is_deleted = 0",'id','beneficiary');

	if ($pi_beneficiary_arr[$result[0][csf("pi_id")]]=="") $supplier_name = $supplier_name;
	else $supplier_name= $pi_beneficiary_arr[$result[0][csf("pi_id")]];

	?>
	<style type="text/css">
		.chapter_ref, .para_ref{
			border: 1px solid #444;
			width: 120px;
			padding: 5px;
			text-align: center;
			float: left;
		}
		.app_ref, .pg{
			border: 1px solid #444;
			width: 120px;
			padding: 5px;
			text-align: center;
			float: right;
		}
		.pg{
			width: 20px!important;
		}
		.heading{
			margin-top: 15px;
			text-align: center;
		}
		.subject_line{
			text-align: center;
		}
		.details_body{
			text-align: justify;
		}
		.signature_heading{
			text-align: right;
			margin-top: 15px;
		}
		span.name_of_importer, span.address_of_importer, span.registration_of_importer{
			border-bottom: 1px solid #555555;
			display: block;
			padding: 8px 3px;
		}
		.date{
			float: left;
			width: 49%;
			margin-top: 25px;
		}
		.signature{
			float: right;
			width: 49%;
			text-align: right;
			margin-top: 25px;
		}
		.signature span{
			text-decoration: overline;

		}
	</style>
    <table width="700" cellpadding="0" align="left" cellspacing="0" border="0">
        <tr>
        	<td colspan="3" height="60"></td>
        </tr>
        <tr>
            <td width="25"></td>
            <td width="650" align="left">
				<div class="chapter_ref">
					<strong class="chapter">See Chapter 7</strong>
				</div>
				<div class="para_ref">
					<strong class="para">Para 27 (1)</strong>
				</div><? //echo $result[0][csf("btb_system_id")]; ?>
				<div class="pg">
					<strong>15</strong>
				</div>
				<div class="app_ref">
					<strong class="app">App. 5</strong>
				</div>
			</td>
            <td width="25" ></td>
        </tr>
        <tr>
            <td width="25" ></td>
            <td width="650" align="left"><div class="heading"><strong class="b_bank_name">BANGLADESH BANK</strong><br/><strong class="department">FOREIGN EXCHANGE POLICY DEPARTMENT</strong></div>

			<? //echo change_date_format($result[0][csf("application_date")]); ?> </td>
            <td width="25" ></td>
        </tr>
        <tr>
        	<td colspan="3" height="10"></td>
        </tr>
        <tr>
            <td width="25" ></td>
            <td width="650" align="left">
			<div class="subject_line"><strong class="form_heading"><u class="">FORM OF UNDERTAKING</u></strong></div>
            <?
				//echo $bank_dtls_arr[$result[0][csf("issuing_bank_id")]]["contact_person"]."<br>".$bank_dtls_arr[$result[0][csf("issuing_bank_id")]]["bank_name"]."<br>".$bank_dtls_arr[$result[0][csf("issuing_bank_id")]]["branch_name"]."<br>".$bank_dtls_arr[$result[0][csf("issuing_bank_id")]]["address"];
			?>

            </td>
            <td width="25" ></td>
        </tr>
        <tr>
        	<td colspan="3" height="15"></td>
        </tr>
        <tr>
            <td width="25" ></td>
            <td width="650" align="left">
			<div class="details_body">
				<p>
					(To be 	furnished by the importer for making advance remittances for permissible imports of goods and services).<br/> In consideration of Bangladesh Bank permitting me/us an advance remittance of <u> <? echo $currency[$result[0][csf("currency_id")]]." ". number_format($result[0][csf("pi_value")],2,'.','');?> </u> to <u> <? echo $supplier_name; ?> </u> I/we hereby undertake that the amount so remitted by me/us will be used solely for the purpose of payment for the goods/services described below to be imported into Bangladesh form <strong><? echo $country_name; ?></strong> in accordance with the regulations in force regarding such imports. I/We declare that the goods/services will be imported by me/us on or about <u class="ship_date"> <? echo $result[0][csf("last_shipment_date")]; ?> </u> and I/we undertake to produce to the Bangladesh Bank documentary evidence in respect of goods/services so imported including the authenticated copy of the Customs Bill of Entry for goods and supplier's invoice in original. I/We further declare that the amount paid by me/us in advance will be deducted from the invoice value of the goods(CFT)/services imported and that the deduction will be shown on the invoice.
				</p>
			</div>

            </td>
            <td width="25" ></td>
        </tr>
        <tr>
        	<td colspan="3" height="5"></td>
        </tr>
        <tr>
            <td width="25" ></td>
            <td width="650" align="left">
			<table cellpadding="0" align="left" cellspacing="0" border="1">
				<tr>
					<td rowspan="2" align="center">Name and address of the supplier</td>
					<td colspan="2" align="center">Invoice Value</td>
					<td colspan="2" align="center">Description</td>
					<td colspan="2" align="center">Country of origin</td>
					<td colspan="2" align="center">Particulars of L.C. Authorisation Form</td>
				</tr>
				<tr>
					<td align="center">Goods</td>
					<td align="center">Services</td>
					<td align="center">Goods</td>
					<td align="center">Services</td>
					<td align="center">Goods</td>
					<td align="center">Services</td>
					<td align="center">Goods</td>
					<td align="center">Services</td>
				</tr>
				<tr>
					<td  align="center"><? echo $supplier_name; ?> <br/> <? echo $supplier_address_1." ".$supplier_address_2; ?></td>
					<td  align="center"><? echo $currency[$result[0][csf("currency_id")]]."&nbsp;".number_format($result[0][csf("pi_value")],2,'.','') ; ?></td>
					<td  align="center"></td>
					<td  align="center"><? echo $item_category[$result[0][csf("item_category_id")]];?></td>
					<td  align="center"></td>
					<td  align="center"><? echo $country_name; ?></td>
					<td  align="center"></td>
					<td  align="center"></td>
					<td  align="center"></td>
				</tr>
			</table>

            </td>
            <td width="25" ></td>
        </tr>
        <tr>
            <td width="25" ></td>
            <td width="650" align="left">
            <p class="signature_heading">Signature and Stamp of the Importer</p>
            </td>
            <td width="25" ></td>
        </tr>
        <tr>
        	<td colspan="3" height="10"></td>
        </tr>

        <tr>
            <td width="25" ></td>
            <td width="650" align="left">
				<table  cellpadding="0" align="left" cellspacing="0" border="0" width="650">
					<tr>
						<td width="250">Name of the Importer: </td>
						<td><span class="name_of_importer"><? echo $company_name;?></span></td>
					</tr>
					<tr>
						<td width="250">Address: </td>
						<td>
							<span class="address_of_importer"><? echo $company["plot_no"].", ".$company["level_no"].", ".$company["road_no"].", ".$company["city"].", ".$importer_country_name[$company["country_id"]];?></span>
						</td>
					</tr>
					<tr>
						<td width="250">Registration Number with CCI & E:</td>
						<td><span class="registration_of_importer"> <? echo $company["irc_no"];?></span></td>
					</tr>
				</table>
            </td>
            <td width="25" ></td>
        </tr>
        <tr>
        	<td colspan="3" height="15"></td>
        </tr>
        <tr>
            <td width="25" ></td>
            <td width="650" align="left">
            <div class="date">
				Date: .........................
			</div>
			<div class="signature">
				<span>Signature and Stamp of the</span><br/>Authorised Dealer
			</div>
            </td>
            <td width="25" ></td>
        </tr>

    </table>
    <?
	exit();

}

if($action=="ffd_form_letter_print")
{
	$data=explode("**",$data);
	//print_r($data);die;
	//echo load_html_head_contents("BTB Import Lc Letter","../../", 1, 1, $unicode,'','');
	$sql_bank_info=sql_select("select id, contact_person, bank_name, branch_name, address from lib_bank ");
	foreach($sql_bank_info as $row)
	{
		$bank_dtls_arr[$row[csf("id")]]["contact_person"]=$row[csf("contact_person")];
		$bank_dtls_arr[$row[csf("id")]]["bank_name"]=$row[csf("bank_name")];
		$bank_dtls_arr[$row[csf("id")]]["branch_name"]=$row[csf("branch_name")];
		$bank_dtls_arr[$row[csf("id")]]["address"]=$row[csf("address")];
	}
	$sql_lc=sql_select("SELECT id,export_lc_no,tenor FROM com_btb_lc_master_details ");
	foreach($sql_lc as $row)
	{
		$export_lc_no_arr[$row[csf("id")]]["export_lc_no"]=$row[csf("export_lc_no")];
		$export_lc_no_arr[$row[csf("id")]]["tenor"]=$row[csf("tenor")];
	}
	$sql_sc=sql_select("SELECT id,contract_no,tenor FROM com_sales_contract ");
	foreach($sql_sc as $row)
	{
		$export_sc_no_arr[$row[csf("id")]]["contract_no"]=$row[csf("contract_no")];
		$export_sc_no_arr[$row[csf("id")]]["tenor"]=$row[csf("tenor")];
	}

	//echo $data."jahid";die;
	if($db_type==2)
	{
		$sql_com="select a.btb_system_id, a.application_date, a.importer_id, a.item_category_id, a.origin, a.issuing_bank_id, a.currency_id, a.supplier_id, a.lc_value, a.pi_value, a.margin, listagg(cast(b.is_lc_sc || '__' || b.lc_sc_id as varchar(4000)),',') within group (order by b.is_lc_sc,b.lc_sc_id) as lc_sc  from com_btb_lc_master_details a, com_btb_export_lc_attachment b where a.id=b.import_mst_id and  a.id=$data[2] and a.is_deleted = 0 AND a.status_active = 1 group by a.btb_system_id, a.application_date, a.importer_id, a.item_category_id, a.origin, a.issuing_bank_id, a.currency_id, a.supplier_id, a.lc_value, a.margin, a.pi_value";
	}
	elseif($db_type==0)
	{
		$sql_com="select a.btb_system_id, a.application_date, a.importer_id, a.item_category_id, a.origin, a.issuing_bank_id, a.currency_id, a.supplier_id, a.lc_value, a.pi_value, a.margin, group_concat(concat(b.is_lc_sc, '__', b.lc_sc_id)) as lc_sc  from com_btb_lc_master_details a, com_btb_export_lc_attachment b where a.id=b.import_mst_id and  a.id=$data[2] and a.is_deleted = 0 AND a.status_active = 1 group by a.btb_system_id, a.application_date, a.importer_id, a.item_category_id, a.supplier_id, a.origin, a.issuing_bank_id, a.currency_id, a.lc_value, a.margin, a.pi_value";
	}
	//echo $sql_com;
	$result=sql_select($sql_com);

	$sql_company_address = "select id, company_name, plot_no, level_no, road_no, block_no, province, city, zip_code, bang_bank_reg_no, country_id from lib_company where id=".$result[0][csf('importer_id')];

	$company_address_result= sql_select($sql_company_address);
	foreach ($company_address_result as $row) {
		$company["id"] = $row[csf("id")];
		$company["company_name"] = $row[csf("company_name")];
		$company["plot_no"] = $row[csf("plot_no")];
		$company["level_no"] = $row[csf("level_no")];
		$company["road_no"] = $row[csf("road_no")];
		$company["block_no"] = $row[csf("block_no")];
		$company["province"] = $row[csf("province")];
		$company["city"] = $row[csf("city")];
		$company["zip_code"] = $row[csf("zip_code")];
		$company["bang_bank_reg_no"] = $row[csf("bang_bank_reg_no")];
		$company["country_id"] = $row[csf("country_id")];
	}

	//print_r($company);

	$company_name = return_field_value("company_name","lib_company","id=".$result[0][csf("importer_id")],"company_name");
	$supplier_name = return_field_value("supplier_name","lib_supplier","id=".$result[0][csf("supplier_id")],"supplier_name");
	$country_name = return_field_value("country_name"," lib_country","id=".$result[0][csf("origin")],"country_name");
	$importer_country_name = return_library_array("select id,country_name from lib_country","id","country_name");

	$pi_beneficiary_arr=return_library_array( "select id, beneficiary from com_pi_master_details where id in(".$result[0][csf("pi_id")].")  AND status_active = 1 AND is_deleted = 0",'id','beneficiary');

	if ($pi_beneficiary_arr[$result[0][csf("pi_id")]]=="") $supplier_name = $supplier_name;
	else $supplier_name= $pi_beneficiary_arr[$result[0][csf("pi_id")]];

	?>
	<style type="text/css">
		.chapter_ref, .para_ref{
			border: 1px solid #444;
			width: 120px;
			padding: 5px;
			text-align: center;
			float: left;
		}
		.app_ref, .pg{
			border: 1px solid #444;
			width: 120px;
			padding: 5px;
			text-align: center;
			float: right;
		}
		.pg{
			width: 20px!important;
		}
		.heading{
			margin-top: 15px;
			text-align: center;
		}
		.subject_line{
			text-align: center;
		}
		.details_body{
			text-align: justify;
		}
		.signature_heading{
			text-align: right;
			margin-top: 15px;
		}
		.name_of_importer span, .address_of_importer span, .registration_of_importer span{
			border-bottom: 1px solid #555555;
			display: block;
			width: 62%;
			float: right;
		}
		.name_of_importer, .address_of_importer, .registration_of_importer{
			margin-top: 15px;

		}
		.date{
			float: left;
			width: 49%;
			margin-top: 25px;
		}
		.signature{
			float: right;
			width: 49%;
			text-align: right;
			margin-top: 25px;
		}
		.signature span{
			text-decoration: overline;

		}
	</style>
    <table width="700" cellpadding="0" align="left" cellspacing="0" border="0">
        <tr>
        	<td colspan="3" height="60"></td>
        </tr>
        <tr>
            <td width="25"></td>
            <td width="650" align="left">
				<div class="chapter_ref">
					<strong class="chapter">See Chapter 7</strong>
				</div>
				<div class="para_ref">
					<strong class="para">Para 27 (1)</strong>
				</div><? //echo $result[0][csf("btb_system_id")]; ?>
				<div class="pg">
					<strong>15</strong>
				</div>
				<div class="app_ref">
					<strong class="app">App. 5</strong>
				</div>
			</td>
            <td width="25" ></td>
        </tr>
        <tr>
            <td width="25" ></td>
            <td width="650" align="left"><div class="heading"><strong class="b_bank_name">BANGLADESH BANK</strong><br/><strong class="department">FOREIGN EXCHANGE POLICY DEPARTMENT</strong></div>

			<? //echo change_date_format($result[0][csf("application_date")]); ?> </td>
            <td width="25" ></td>
        </tr>
        <tr>
        	<td colspan="3" height="10"></td>
        </tr>
        <tr>
            <td width="25" ></td>
            <td width="650" align="left">
			<div class="subject_line"><strong class="form_heading"><u class="">FORM OF UNDERTAKING</u></strong></div>
            <?
				//echo $bank_dtls_arr[$result[0][csf("issuing_bank_id")]]["contact_person"]."<br>".$bank_dtls_arr[$result[0][csf("issuing_bank_id")]]["bank_name"]."<br>".$bank_dtls_arr[$result[0][csf("issuing_bank_id")]]["branch_name"]."<br>".$bank_dtls_arr[$result[0][csf("issuing_bank_id")]]["address"];
			?>

            </td>
            <td width="25" ></td>
        </tr>
        <tr>
        	<td colspan="3" height="15"></td>
        </tr>
        <tr>
            <td width="25" ></td>
            <td width="650" align="left">
			<div class="details_body">
				<p>
					(To be 	furnished by the importer for making advance remittances for permissible imports of goods and services).<br/> In consideration of Bangladesh Bank permitting me/us an advance remittance of ...<? echo $currency[$result[0][csf("currency_id")]]." ". number_format($result[0][csf("pi_value")],2,'.','');?>... to ...<? echo $supplier_name; ?>... I/we hereby undertake that the amount so remitted by me/us will be used solely for the purpose of payment for the goods/services described below to be imported into Bangladesh form <? echo $country_name; ?> in accordance with the regulations in force regarding such imports. I/We declare that the goods/services will be imported by me/us on or about ___________ and I/we undertake to produce to the Bangladesh Bank documentary evidence in respect of goods/services so imported including the authenticated copy of the Customs Bill of Entry for goods and supplier's invoice in original. I/We further declare that the amount paid by me/us in advance will be deducted from the invoice value of the goods(CFT)/services imported and that the deduction will be shown on the invoice.
				</p>
			</div>

            </td>
            <td width="25" ></td>
        </tr>
        <tr>
        	<td colspan="3" height="5"></td>
        </tr>
        <tr>
            <td width="25" ></td>
            <td width="650" align="left">
			<table cellpadding="0" align="left" cellspacing="0" border="1">
				<tr>
					<td rowspan="2" align="center">Name and address of the supplier</td>
					<td colspan="2" align="center">Invoice Value</td>
					<td colspan="2" align="center">Description</td>
					<td colspan="2" align="center">Country of origin</td>
					<td colspan="2" align="center">Particulars of L.C. Authorisation Form</td>
				</tr>
				<tr>
					<td align="center">Goods</td>
					<td align="center">Services</td>
					<td align="center">Goods</td>
					<td align="center">Services</td>
					<td align="center">Goods</td>
					<td align="center">Services</td>
					<td align="center">Goods</td>
					<td align="center">Services</td>
				</tr>
				<tr>
					<td  align="center"><? echo $supplier_name; ?></td>
					<td  align="center"><? echo $currency[$result[0][csf("currency_id")]]."&nbsp;".number_format($result[0][csf("pi_value")],2,'.','') ; ?></td>
					<td  align="center"></td>
					<td  align="center"><? echo $item_category[$result[0][csf("item_category_id")]];?></td>
					<td  align="center"></td>
					<td  align="center"><? echo $country_name; ?></td>
					<td  align="center"></td>
					<td  align="center"></td>
					<td  align="center"></td>
				</tr>
			</table>

            </td>
            <td width="25" ></td>
        </tr>
        <tr>
            <td width="25" ></td>
            <td width="650" align="left">
            <p class="signature_heading">Signature and Stamp of the Importer</p>
            </td>
            <td width="25" ></td>
        </tr>
        <tr>
        	<td colspan="3" height="10"></td>
        </tr>

        <tr>
            <td width="25" ></td>
            <td width="650" align="left">
            <div class="name_of_importer">Name of the Importer: <span><? echo $company_name;?></span></div>
            <div class="address_of_importer">Address: <span>Plot: <? echo $company["plot_no"].", Road: ".$company["road_no"].", ".$company["city"].", ".$importer_country_name[$company["country_id"]];?></span></div>
            <div class="registration_of_importer">Registration Number with CCI & E: <span><? echo $company["bang_bank_reg_no"];?></span></div>
            </td>
            <td width="25" ></td>
        </tr>
        <tr>
        	<td colspan="3" height="15"></td>
        </tr>
        <tr>
            <td width="25" ></td>
            <td width="650" align="left">
            <div class="date">
				Date: .........................
			</div>
			<div class="signature">
				<span>Signature and Stamp of the</span><br/>Authorised Dealer
			</div>
            </td>
            <td width="25" ></td>
        </tr>

    </table>
    <?
	exit();

}

if ($action=="btb_import_lc_dynamic_letter")
{
	$data=explode("**",$data);
	$country_lib=return_library_array( "select id, country_name from lib_country",'id','country_name');
	$supplier_lib=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$buyer_lib=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	//Sales Contact Lien------------- listagg(cast(b.HS_CODE as varchar(4000)),',') within group(order by b.id) as ITEM_HS_CODE
	
	$data_array=sql_select("select a.APPLICATION_DATE, a.IMPORTER_ID, a.SUPPLIER_ID, a.LC_VALUE, a.PI_VALUE, a.ISSUING_BANK_ID, a.PAYTERM_ID, a.TENOR, a.CURRENCY_ID, a.PI_ENTRY_FORM, a.LAST_SHIPMENT_DATE, a.LC_EXPIRY_DATE, a.COVER_NOTE_NO, a.COVER_NOTE_DATE, a.LCAF_NO, a.INCO_TERM_ID, a.PARTIAL_SHIPMENT, a.TRANSHIPMENT, listagg(cast(b.PI_ID as varchar(4000)),',') within group(order by b.PI_ID) as PI_ID, p.IS_LC_SC, listagg(cast(p.LC_SC_ID as varchar(4000)),',') within group (order by p.LC_SC_ID) as LC_SC_ID, a.ORIGIN 
	from COM_BTB_LC_PI b, COM_BTB_LC_MASTER_DETAILS a left join com_btb_export_lc_attachment p on p.import_mst_id=a.id
	where a.id=b.COM_BTB_LC_MASTER_DETAILS_ID and a.id='$data[1]' and a.status_active=1 and b.status_active=1
	group by a.APPLICATION_DATE, a.IMPORTER_ID, a.SUPPLIER_ID, a.LC_VALUE, a.PI_VALUE, a.ISSUING_BANK_ID, a.PAYTERM_ID, a.TENOR, a.CURRENCY_ID, a.PI_ENTRY_FORM,
	a.LAST_SHIPMENT_DATE, a.LC_EXPIRY_DATE, a.COVER_NOTE_NO, a.COVER_NOTE_DATE, a.LCAF_NO, a.INCO_TERM_ID, a.PARTIAL_SHIPMENT, a.TRANSHIPMENT, p.IS_LC_SC, a.ORIGIN");
	foreach ($data_array as $row)
	{
		$application_date	= change_date_format($row["APPLICATION_DATE"]);
		$importer_id	= $row["IMPORTER_ID"];
		$supplier_id	= $row["SUPPLIER_ID"];
		$lc_value	= $row["LC_VALUE"];
		$pi_value	= $row["PI_VALUE"];
		$issuing_bank_id	= $row["ISSUING_BANK_ID"];
		
		$payterm_id	= $pay_term[$row["PAYTERM_ID"]];
		$tenor	= $row["TENOR"];
		$currency_id	= $currency[$row["CURRENCY_ID"]];
		$pi_entry_form	= $category_name_entry_form_wiseArr[$row["PI_ENTRY_FORM"]];
		$last_shipment_date	= change_date_format($row["LAST_SHIPMENT_DATE"]);
		$lc_expiry_date	= change_date_format($row["LC_EXPIRY_DATE"]);
		$cover_note_no	= $row["COVER_NOTE_NO"];
		$cover_note_date	= change_date_format($row["COVER_NOTE_DATE"]);
		$lcaf_no	= $row["LCAF_NO"];
		$inco_term_id	= $incoterm[$row["INCO_TERM_ID"]];
		$partial_shipment	= $yes_no[$row["PARTIAL_SHIPMENT"]];
		$transhipment	= $yes_no[$row["TRANSHIPMENT"]];
		$pi_id	= $row["PI_ID"];
		$origin	= $country_lib[$row["ORIGIN"]];
		
		$is_lc_sc	= $row["IS_LC_SC"];
		$lc_sc_id	= $row["LC_SC_ID"];
	}
	
	$lc_sc_no=$lc_sc_date=$lc_sc_value=$buyer_ids=$local_commision=$forein_commission=$amendment_no=$lc_sc_shipment_date=$lc_sc_expiry_date="";
	if($lc_sc_id!="")
	{
		if($is_lc_sc==0)
		{
			$lc_sc_sql="SELECT a.ID, a.EXPORT_LC_NO as LC_SC_NO, a.LC_DATE as LC_SC_DATE, a.LC_VALUE as LC_SC_VALUE, a.BUYER_NAME, a.LOCAL_COMN_VALUE, a.FOREIGN_COMN_VALUE, a.LAST_SHIPMENT_DATE, a.EXPIRY_DATE
			FROM COM_EXPORT_LC a, COM_BTB_EXPORT_LC_ATTACHMENT b
			where a.id=b.LC_SC_ID and a.STATUS_ACTIVE=1 and b.STATUS_ACTIVE=1 and a.id in($lc_sc_id)
			group by a.ID, a.EXPORT_LC_NO, a.LC_DATE, a.LC_VALUE, a.BUYER_NAME, a.LOCAL_COMN_VALUE, a.FOREIGN_COMN_VALUE, a.LAST_SHIPMENT_DATE, a.EXPIRY_DATE";
		}
		else
		{
			$lc_sc_sql="SELECT a.ID, a.CONTRACT_NO as LC_SC_NO, a.CONTRACT_DATE as LC_SC_DATE, a.CONTRACT_VALUE as LC_SC_VALUE, a.BUYER_NAME, a.LOCAL_COMN_VALUE, a.FOREIGN_COMN_VALUE, a.LAST_SHIPMENT_DATE, a.EXPIRY_DATE
			FROM COM_SALES_CONTRACT a, COM_BTB_EXPORT_LC_ATTACHMENT b
			where a.id=b.LC_SC_ID and a.STATUS_ACTIVE=1 and b.STATUS_ACTIVE=1 and a.id in($lc_sc_id)
			group by a.ID, a.CONTRACT_NO, a.CONTRACT_DATE, a.CONTRACT_VALUE, a.BUYER_NAME, a.LOCAL_COMN_VALUE, a.FOREIGN_COMN_VALUE, a.LAST_SHIPMENT_DATE, a.EXPIRY_DATE";
		}
		
		//echo $is_lc_sc."=".$lc_sc_sql;
		
		$lc_sc_sql_result=sql_select($lc_sc_sql);$lc_sc_id_arr=array();
		foreach($lc_sc_sql_result as $row)
		{
			$lc_sc_no.=$row["LC_SC_NO"].",";
			$lc_sc_date.=change_date_format($row["LC_SC_DATE"]).",";
			$lc_sc_shipment_date.=change_date_format($row["LAST_SHIPMENT_DATE"]).",";
			if($row["EXPIRY_DATE"]){$lc_sc_expiry_date.=change_date_format($row["EXPIRY_DATE"]).",";}
			$lc_sc_value+=$row["LC_SC_VALUE"];
			$buyer_ids.=$buyer_lib[$row["BUYER_NAME"]].",";
			$local_commision+=$row["LOCAL_COMN_VALUE"];
			$forein_commission+=$row["FOREIGN_COMN_VALUE"];
			$amendment_no.=$row["AMENDMENT_NO"].",";
			$lc_sc_id_arr[$row["ID"]]=$row["ID"];
		}
		
		$lc_sc_no=chop($lc_sc_no,",");$lc_sc_date=chop($lc_sc_date,",");$lc_sc_shipment_date=chop($lc_sc_shipment_date,",");$lc_sc_expiry_date=chop($lc_sc_expiry_date,",");
		$buyer_ids=chop($buyer_ids,",");
		$netFob=($lc_sc_value-($local_commision+$forein_commission));
		$btbEntitle=$netFob * 0.75;
		$btb_lc_oppend=0;
		if(count($lc_sc_id_arr)>0)
		{
			$btb_lc_oppend_sql=sql_select("select sum(a.LC_VALUE) as LC_VALUE 
			from COM_BTB_LC_MASTER_DETAILS a, com_btb_export_lc_attachment p
			where p.import_mst_id=a.id and a.id <> '$data[1]' and a.status_active=1 and p.status_active=1 and p.LC_SC_ID in(".implode(",",$lc_sc_id_arr).") and p.IS_LC_SC=$is_lc_sc");
			$btb_lc_oppend=$btb_lc_oppend_sql[0]["LC_VALUE"];
		}
		$btb_balance=$btbEntitle-$btb_lc_oppend;
		$balance_after_propose=$btb_balance-$lc_value;
	}
	
	$amendment_sql=sql_select("select max(AMENDMENT_NO) as AMENDMENT_NO from COM_BTB_LC_AMENDMENT where STATUS_ACTIVE=1 and BTB_ID=$data[1]");
	$amendment_no=chop($amendment_no,",");
	
	$pi_sql=sql_select("select a.ID, a.PI_NUMBER, a.PI_DATE, listagg(cast(b.HS_CODE as varchar(4000)),',') within group(order by b.ID) as HS_CODE  from COM_PI_MASTER_DETAILS a, COM_PI_ITEM_DETAILS b where a.id=b.pi_id and a.status_active=1 and b.status_active=1 and a.id in($pi_id) group by a.ID, a.PI_NUMBER, a.PI_DATE");
	$all_pi_no=$all_pi_date=$all_pi_hs_code="";
	foreach($pi_sql as $val)
	{
		$all_pi_no.=$val["PI_NUMBER"].",";
		$all_pi_date.=change_date_format($val["PI_DATE"]).",";
		if($val["HS_CODE"]!="") $all_pi_hs_code.=$val["HS_CODE"].",";
	}
	$all_pi_no=chop($all_pi_no,",");
	$all_pi_date=chop($all_pi_date,",");
	$all_pi_hs_code=chop($all_pi_hs_code,",");;
	$data_array5=sql_select("select company_name, country_id, city from lib_company b where id=$importer_id");
	foreach ($data_array5 as $row5)
	{
		$company_name	= $row5[csf("company_name")];
		$country_id		= $row5[csf("country_id")];
		$city			= $row5[csf("city")];
	}

	//bank information retriving here
	$data_array1=sql_select("select id, bank_name, branch_name, contact_person, address from lib_bank where id='$issuing_bank_id'");
	foreach ($data_array1 as $row1)
	{
		$bank_name		= $row1[csf("bank_name")];
		$branch_name	= $row1[csf("branch_name")];
		$contact_person	= $row1[csf("contact_person")];
		$address		= $row1[csf("address")];
	}

	//letter body is retriving here
	$data_array2=sql_select("select letter_body from dynamic_letter where letter_type='$data[0]'");
	foreach ($data_array2 as $row2)
	{
		$letter_body = $row2[csf("letter_body")]->load();
	}

	$raw_data=str_replace("__APPLICATIONDATE__",$application_date,$letter_body);
	$raw_data=str_replace("__CONTACTPERSON__",$contact_person,$raw_data);
	$raw_data=str_replace("__BANKNAME__",$bank_name,$raw_data);
	$raw_data=str_replace("__BRANCHNAME__",$branch_name,$raw_data);
	$raw_data=str_replace("__ADDRESS__",$address,$raw_data);
	$raw_data=str_replace("__CURRENCY__",$currency_id,$raw_data);
	$raw_data=str_replace("__PIAMOUNT__",number_format($lc_value,2),$raw_data);
	$raw_data=str_replace("__SUPPLIER__",$supplier_lib[$supplier_id],$raw_data);
	$raw_data=str_replace("__ITEMCATEGORY__",$pi_entry_form,$raw_data);
	$raw_data=str_replace("__TENOR__",$tenor,$raw_data);
	$raw_data=str_replace("__PAYTERM__",$payterm_id,$raw_data);
	$raw_data=str_replace("__PINUMBER__",$all_pi_no,$raw_data);
	$raw_data=str_replace("__PIDATE__",$all_pi_date,$raw_data);
	$raw_data=str_replace("__LASTSHIPMENTDATE__",$last_shipment_date,$raw_data);
	$raw_data=str_replace("__LCEXPIRYDATE__",$lc_expiry_date,$raw_data);
	$raw_data=str_replace("__COVERNOTENO__",$cover_note_no,$raw_data);

	$raw_data=str_replace("__COVERNOTEDATE__",$cover_note_date,$raw_data);
	$raw_data=str_replace("__HSCODE__",$all_pi_hs_code,$raw_data);
	$raw_data=str_replace("__LCAFNO__",$lcaf_no,$raw_data);
	$raw_data=str_replace("__INCOTERM__",$inco_term_id,$raw_data);
	$raw_data=str_replace("__PARTIALSHIPMENT__",$partial_shipment,$raw_data);

	$raw_data=str_replace("__TRANSHIPMENT__",$transhipment,$raw_data);
	$raw_data=str_replace("__COMPANYNAME__",$company_name,$raw_data);
	$raw_data=str_replace("__EXPLCNUMBER__",$lc_sc_no,$raw_data);
	$raw_data=str_replace("__SCNUMBER__",$lc_sc_no,$raw_data);
	$raw_data=str_replace("__EXPLCDATE__",$lc_sc_date,$raw_data);
	$raw_data=str_replace("__SCDATE__",$lc_sc_date,$raw_data);
	$raw_data=str_replace("__LCSCSHIPMENTDATE__",$lc_sc_shipment_date,$raw_data);
	$raw_data=str_replace("__LCSCEXPIRYDATE__",$lc_sc_expiry_date,$raw_data);
	$raw_data=str_replace("__EXPLCAMENDNUMBER__",$amendment_no,$raw_data);
	$raw_data=str_replace("__SCAMENDNUMBER__",$amendment_no,$raw_data);
	$raw_data=str_replace("__EXPLCVALUE__",number_format($lc_sc_value,2),$raw_data);
	$raw_data=str_replace("__SCVALUE__",number_format($lc_sc_value,2),$raw_data);
	$raw_data=str_replace("__BUYER__",$buyer_ids,$raw_data);
	$raw_data=str_replace("__LCLOCALCOMMISION__",number_format($local_commision,2),$raw_data);
	$raw_data=str_replace("__SCLOCALCOMMISION__",number_format($local_commision,2),$raw_data);
	$raw_data=str_replace("__LCFOREGINCOMMISION__",number_format($forein_commission,2),$raw_data);
	$raw_data=str_replace("__SCFOREGINCOMMISION__",number_format($forein_commission,2),$raw_data);
	$raw_data=str_replace("__NETFOB__",number_format($netFob,2),$raw_data);
	$raw_data=str_replace("__BTBENTITLE__",number_format($btbEntitle,2),$raw_data);
	$raw_data=str_replace("__BTBLCOPENDVALUE__",number_format($btb_lc_oppend,2),$raw_data);
	$raw_data=str_replace("__BTBBALANCE__",number_format($btb_balance,2),$raw_data);
	$raw_data=str_replace("__PROPOSEDVALUE__",number_format($lc_value,2),$raw_data);
	$raw_data=str_replace("__BALANCEAFTRPROPOSED__",number_format($balance_after_propose,2),$raw_data);
	$raw_data=str_replace("__ORIGIN__",$origin,$raw_data);
	
	
	?>
    <img src="../../file_upload/167558987611111111111111.JPG" />
	<?
	echo $raw_data;
	exit();
}



if($action=="btb_import_lc_letter")
{
	//echo load_html_head_contents("BTB Import Lc Letter","../../", 1, 1, $unicode,'','');
	$sql_bank_info=sql_select("select id, contact_person, bank_name, branch_name, address from lib_bank ");
	foreach($sql_bank_info as $row)
	{
		$bank_dtls_arr[$row[csf("id")]]["contact_person"]=$row[csf("contact_person")];
		$bank_dtls_arr[$row[csf("id")]]["bank_name"]=$row[csf("bank_name")];
		$bank_dtls_arr[$row[csf("id")]]["branch_name"]=$row[csf("branch_name")];
		$bank_dtls_arr[$row[csf("id")]]["address"]=$row[csf("address")];
	}
	$sql_lc=sql_select("SELECT id,export_lc_no,tenor FROM com_export_lc ");
	foreach($sql_lc as $row)
	{
		$export_lc_no_arr[$row[csf("id")]]["export_lc_no"]=$row[csf("export_lc_no")];
		$export_lc_no_arr[$row[csf("id")]]["tenor"]=$row[csf("tenor")];
	}
	$sql_sc=sql_select("SELECT id,contract_no,tenor FROM com_sales_contract ");
	foreach($sql_sc as $row)
	{
		$export_sc_no_arr[$row[csf("id")]]["contract_no"]=$row[csf("contract_no")];
		$export_sc_no_arr[$row[csf("id")]]["tenor"]=$row[csf("tenor")];
	}

	//echo $data."jahid";die;
	if($db_type==2)
	{
		$sql_com="select a.btb_system_id, a.application_date, a.importer_id, a.item_category_id, a.origin, a.issuing_bank_id, a.currency_id, a.supplier_id, a.lc_value, a.margin, listagg(cast(b.is_lc_sc || '__' || b.lc_sc_id as varchar(4000)),',') within group (order by b.is_lc_sc,b.lc_sc_id) as lc_sc  from com_btb_lc_master_details a, com_btb_export_lc_attachment b where a.id=b.import_mst_id and  a.id=$data and a.is_deleted = 0 AND a.status_active = 1 group by a.btb_system_id, a.application_date, a.importer_id, a.item_category_id, a.origin, a.issuing_bank_id, a.currency_id, a.supplier_id, a.lc_value, a.margin";
	}
	elseif($db_type==0)
	{
		$sql_com="select a.btb_system_id, a.application_date, a.importer_id, a.item_category_id, a.origin, a.issuing_bank_id, a.currency_id, a.supplier_id, a.lc_value, a.margin, group_concat(concat(b.is_lc_sc, '__', b.lc_sc_id)) as lc_sc  from com_btb_lc_master_details a, com_btb_export_lc_attachment b where a.id=b.import_mst_id and  a.id=$data and a.is_deleted = 0 AND a.status_active = 1 group by a.btb_system_id, a.application_date, a.importer_id, a.item_category_id, a.supplier_id, a.origin, a.issuing_bank_id, a.currency_id, a.lc_value, a.margin";
	}
	//echo $sql_com;
	$result=sql_select($sql_com);

	$company_name = return_field_value("company_name","lib_company","id=".$result[0][csf("importer_id")],"company_name");
	$supplier_name = return_field_value("supplier_name","lib_supplier","id=".$result[0][csf("supplier_id")],"supplier_name");
	$country_name = return_field_value("country_name"," lib_country","id=".$result[0][csf("origin")],"country_name");

	$pi_beneficiary_arr=return_library_array( "select id, beneficiary from com_pi_master_details where id in(".$result[0][csf("pi_id")].")  AND status_active = 1 AND is_deleted = 0",'id','beneficiary');

	if ($pi_beneficiary_arr[$result[0][csf("pi_id")]]=="") $supplier_name = $supplier_name;
	else $supplier_name= $pi_beneficiary_arr[$result[0][csf("pi_id")]];

	?>

    <table width="700" cellpadding="0" align="left" cellspacing="0" border="0">
        <tr>
        	<td colspan="3" height="110"></td>
        </tr>
        <tr>
            <td width="25"></td>
            <td width="650" align="left">Ref : <? echo $result[0][csf("btb_system_id")]; ?></td>
            <td width="25" ></td>
        </tr>
        <tr>
            <td width="25" ></td>
            <td width="650" align="left">Dated : <? echo change_date_format($result[0][csf("application_date")]); ?> </td>
            <td width="25" ></td>
        </tr>
        <tr>
        	<td colspan="3" height="20"></td>
        </tr>
        <tr>
            <td width="25" ></td>
            <td width="650" align="left">
            <?
				echo $bank_dtls_arr[$result[0][csf("issuing_bank_id")]]["contact_person"]."<br>".$bank_dtls_arr[$result[0][csf("issuing_bank_id")]]["bank_name"]."<br>".$bank_dtls_arr[$result[0][csf("issuing_bank_id")]]["branch_name"]."<br>".$bank_dtls_arr[$result[0][csf("issuing_bank_id")]]["address"];
			?>

            </td>
            <td width="25" ></td>
        </tr>
        <tr>
        	<td colspan="3" height="30"></td>
        </tr>
        <tr>
            <td width="25" ></td>
            <td width="650" align="left">
            Subject: Application for opening letter of credit under back to back facility for <? echo $currency[$result[0][csf("currency_id")]]."&nbsp;".number_format($result[0][csf("lc_value")],2,'.','')." @ ".number_format($result[0][csf("margin")]); ?>% for importing <? echo $item_category[$result[0][csf("item_category_id")]];?> from <? echo $country_name; ?> against Export LC/Sales Contract NO-
			<?
			$lc_sc_ref=explode(",",$result[0][csf("lc_sc")]);
			$lc_sc_no="";
			$tenor_first="";
			foreach($lc_sc_ref as $row)
			{
				$lc_sc_arr=explode("__",$row);
				if($lc_sc_arr[0]==0)
				{
					$lc_sc_no.=$export_lc_no_arr[$lc_sc_arr[1]]["export_lc_no"].", ";
					if($tenor_first=="") $tenor_first=$export_lc_no_arr[$lc_sc_arr[1]]["tenor"];
				}
				else
				{
					$lc_sc_no.=$export_sc_no_arr[$lc_sc_arr[1]]["contract_no"].", ";
					if($tenor_first=="") $tenor_first=$export_sc_no_arr[$lc_sc_arr[1]]["tenor"];
				}
			}
			$lc_sc_no=chop($lc_sc_no," , ");
			echo $lc_sc_no;
			?> .
            </td>
            <td width="25" ></td>
        </tr>
        <tr>
        	<td colspan="3" height="20"></td>
        </tr>
        <tr>
            <td width="25" ></td>
            <td width="650" align="left"> Dear Sir, </td>
            <td width="25" ></td>
        </tr>
        <tr>
            <td width="25" ></td>
            <td width="650" align="left">
            Please issue a letter of credit as mentioned in the subject line by the amount of <? echo $currency[$result[0][csf("currency_id")]]."&nbsp;".number_format($result[0][csf("lc_value")],2,'.','')." @ ".number_format($result[0][csf("margin")]); ?>% favoring to <? echo $supplier_name;?> for <? echo $item_category[$result[0][csf("item_category_id")]];?>. We have provided necessary documents/papers herewith as required.
            </td>
            <td width="25" ></td>
        </tr>
        <tr>
        	<td colspan="3" height="15"></td>
        </tr>
        <tr>
            <td width="25" ></td>
            <td width="650" align="left">
            We are hereby giving undertaking that we shall make payment all the liabilities of this LC if we fail to export the goods within <? echo $tenor_first; ?> days against above  Export LC.
            </td>
            <td width="25" ></td>
        </tr>
        <tr>
        	<td colspan="3" height="15"></td>
        </tr>

        <tr>
            <td width="25" ></td>
            <td width="650" align="left">
            You are authorized to debit our CD account for agreed margin and charges.
            </td>
            <td width="25" ></td>
        </tr>
        <tr>
        	<td colspan="3" height="15"></td>
        </tr>
        <tr>
            <td width="25" ></td>
            <td width="650" align="left">
            We appreciate your prompt action on the matter.
            </td>
            <td width="25" ></td>
        </tr>
        <tr>
        	<td colspan="3" height="15"></td>
        </tr>
        <tr>
            <td width="25" ></td>
            <td width="650" align="left">
            Thanking You,
            </td>
            <td width="25" ></td>
        </tr>
        <tr>
        	<td colspan="3" height="80"></td>
        </tr>
        <tr>
            <td width="25" ></td>
            <td width="650" align="left">
			<?

			echo $company_name;

			?></td>
            <td width="25" ></td>
        </tr>
       <tr>
        	<td colspan="3" height="50"></td>
       </tr>
    </table>
    <?
	exit();

}

/*########  This print Button Created for Microfiber   ##########*/
if($action=="btb_import_lc_letter2")
{
	//echo load_html_head_contents("BTB Import Lc Letter","../../", 1, 1, $unicode,'','');
	$sql_bank_info=sql_select("select id, contact_person, bank_name, branch_name, address from lib_bank ");
	foreach($sql_bank_info as $row)
	{
		$bank_dtls_arr[$row[csf("id")]]["contact_person"]=$row[csf("contact_person")];
		$bank_dtls_arr[$row[csf("id")]]["bank_name"]=$row[csf("bank_name")];
		$bank_dtls_arr[$row[csf("id")]]["branch_name"]=$row[csf("branch_name")];
		$bank_dtls_arr[$row[csf("id")]]["address"]=$row[csf("address")];
	}

	if($db_type==2)
	{
		$sql_com="SELECT a.id,a.btb_system_id, a.application_date, a.importer_id, a.item_category_id, a.origin, a.issuing_bank_id, a.currency_id, a.supplier_id,a.lc_value,a.margin,a.lc_date,a.tenor,a.last_shipment_date,a.lc_expiry_date,a.upas_rate,a.cover_note_no,a.cover_note_date from com_btb_lc_master_details a where a.id=$data and a.is_deleted = 0 AND a.status_active = 1 group by a.id,a.btb_system_id, a.application_date, a.importer_id, a.item_category_id, a.origin, a.issuing_bank_id, a.currency_id, a.supplier_id, a.lc_value, a.margin,a.lc_date,a.tenor,a.last_shipment_date,a.lc_expiry_date,a.upas_rate,a.cover_note_no,a.cover_note_date";
	}
	elseif($db_type==0)
	{
		$sql_com="SELECT a.id,a.btb_system_id, a.application_date, a.importer_id, a.item_category_id, a.origin, a.issuing_bank_id, a.currency_id, a.supplier_id, a.lc_value, a.margin,a.lc_date,a.tenor,a.last_shipment_date,a.lc_expiry_date,a.upas_rate,a.cover_note_no,a.cover_note_date from com_btb_lc_master_details a where a.id=$data and a.is_deleted = 0 AND a.status_active = 1 group by a.id,a.btb_system_id, a.application_date, a.importer_id, a.item_category_id, a.supplier_id, a.origin, a.issuing_bank_id, a.currency_id, a.lc_value, a.margin,a.lc_date,a.tenor,a.last_shipment_date,a.lc_expiry_date,a.upas_rate,a.cover_note_no,a.cover_note_date";
	}
	//echo $sql_com;
	$result=sql_select($sql_com);
	$sql_pi=sql_select("SELECT a.pi_number, a.pi_date,a.hs_code,b.com_btb_lc_master_details_id,rtrim(xmlagg(xmlelement(e,c.item_category_id,',').extract('//text()') order by c.item_category_id).GetClobVal(),',') AS item_category_ids from com_pi_master_details a ,com_btb_lc_pi b,com_pi_item_details c where a.id=b.pi_id and com_btb_lc_master_details_id=$data and a.id=c.pi_id and c.status_active=1 and a.status_active=1 and b.status_active=1 group by a.pi_number, a.pi_date,a.hs_code,b.com_btb_lc_master_details_id");


	if($result[0][csf("tenor")]>=180)
	{//echo $result[0][csf("tenor")]; die;
		$upas_string="UPAS L/C for";
		$upas_string2=$result[0][csf("tenor")] ." days from thr date of negotiation (UPAS LC at sight)";
	}
	else
	{
		$upas_string="L/C for";

		$upas_string2="L/C tenor will be At Sight/Deferred";
	}

	$pi_number_date_arr=array();
	foreach($sql_pi as $row)
	{
		if($db_type==2) $row[csf("item_category_ids")] = $row[csf("item_category_ids")]->load();
		$pi_number_date_arr[$row[csf("com_btb_lc_master_details_id")]]["pi_number_date"].=$row[csf("pi_number")]." dated.".change_date_format($row[csf("pi_date")]).",";
		//$pi_number_date_arr[$row[csf("com_btb_lc_master_details_id")]]["pi_date"].=change_date_format($row[csf("pi_date")]).",";
		$pi_number_date_arr[$row[csf("com_btb_lc_master_details_id")]]["hs_code"].=$row[csf("hs_code")].",";
		$pi_number_date_arr[$row[csf("com_btb_lc_master_details_id")]]["item_category_ids"].=$row[csf("item_category_ids")].",";
	}
	//print_r($pi_number_date_arr);
	$sl=1;
	?>
    <table width="700" cellpadding="0" align="left" cellspacing="0" border="0">
        <tr>
            <td width="25" ></td>
            <td width="650" align="left">Date : <? echo change_date_format($result[0][csf("lc_date")]); ?> </td>
            <td width="25" ></td>
        </tr>
        <tr>
            <td width="25" ></td>
            <td width="650" align="left">To, </td>
            <td width="25" ></td>
        </tr>
        <tr>
            <td width="25" ></td>
            <td width="650" align="left">
            <?
				echo $bank_dtls_arr[$result[0][csf("issuing_bank_id")]]["contact_person"]."<br>".$bank_dtls_arr[$result[0][csf("issuing_bank_id")]]["bank_name"]."<br>".$bank_dtls_arr[$result[0][csf("issuing_bank_id")]]["branch_name"]."<br>".$bank_dtls_arr[$result[0][csf("issuing_bank_id")]]["address"];
			?>

            </td>
            <td width="25" ></td>
        </tr>
        <tr>
        	<td colspan="3" height="10"></td>
        </tr>
        <tr>
            <td width="25" ></td>
            <td width="650" align="left">
            Sub: <strong>Request for opening of <? echo $upas_string." ". $currency[$result[0][csf("currency_id")]]."&nbsp;".number_format($result[0][csf("lc_value")],2,'.','');?> to Import of  "<?
            								$itemCategory="";
											$l=1;
											$cat_id_arr=array_unique(explode(",",chop($pi_number_date_arr[$result[0][csf("id")]]['item_category_ids'],",")));
											//print_r($cat_id_arr);
											foreach($cat_id_arr as $cat_id)
											{
												if($l!=1) $itemCategory .=", ";
												$itemCategory .=$item_category[$cat_id];
												$l++;
											}
											echo $itemCategory; ?>" Proforma Invoice No. <? echo chop ($pi_number_date_arr[$result[0][csf("id")]]["pi_number_date"],",") ;?>.</strong>
            </td>
            <td width="25" ></td>
        </tr>
        <tr>
        	<td colspan="3" height="20"></td>
        </tr>
        <tr>
            <td width="25" ></td>
            <td width="650" align="left"> Dear Sir, </td>
            <td width="25" ></td>
        </tr>
        <tr>
            <td width="25" ></td>
            <td width="650" align="left">
            	We are enclosing herewith an L/C application along with all necessary papers & documents to open <strong><? echo $result[0][csf("tenor")];?></strong> days UPAS L/C for <strong><? echo $currency[$result[0][csf("currency_id")]]."&nbsp;".number_format($result[0][csf("lc_value")],2,'.','');?></strong> L/C has to be transmitted by swift.
            </td>
            </td>
            <td width="25" ></td>
        </tr>
        <tr>
        	<td colspan="3" height="15"></td>
        </tr>
        <tr>
            <td width="25" ></td>
            <td width="650" align="left">
            Necessary clause :
            </td>
            <td width="25" ></td>
        </tr>
        <tr>
            <td width="25" ></td>
            <td width="650" align="left">
            <? echo $sl; ?>. Shipment Date : <? if($result[0][csf("last_shipment_date")]=="0000-00-00" || $result[0][csf("last_shipment_date")]=="") $last_shipment_date=""; else $last_shipment_date=change_date_format($result[0][csf("last_shipment_date")]);
            echo $last_shipment_date;?>
            </td>
            <td width="25" ></td>
        </tr>
        <tr>
            <td width="25" ></td>
            <td width="650" align="left">
            <? echo ++$sl; ?>. Expiry Date : <? if($result[0][csf("lc_expiry_date")]=="0000-00-00" || $result[0][csf("lc_expiry_date")]=="") $lc_expiry_date=""; else $lc_expiry_date=change_date_format($result[0][csf("lc_expiry_date")]);
            echo $lc_expiry_date;?>
            </td>
            <td width="25" ></td>
        </tr>
        <tr>
            <td width="25" ></td>
            <td width="650" align="left">
            <? echo ++$sl; ?>. <? echo $upas_string2;?>
            </td>
            <td width="25" ></td>
        </tr>
        <tr>
            <td width="25" ></td>
            <td width="650" align="left">
            <? echo ++$sl; ?>.Tran-shipment will be allowed
            </td>
            <td width="25" ></td>
        </tr>
        <tr>
            <td width="25" ></td>
            <td width="650" align="left">
            <? echo ++$sl; ?>.Partial Shipment will be allowed
            </td>
            <td width="25" ></td>
        </tr>
        <? 	if($result[0][csf("cover_note_no")]!='')
			{
				if($result[0][csf("cover_note_date")]=="0000-00-00" || $result[0][csf("cover_note_date")]=="") $cover_note_date=""; else $cover_note_date=change_date_format($result[0][csf("cover_note_date")]);
				?>
				<tr>
		            <td width="25" ></td>
		            <td width="650" align="left">
		            <? echo ++$sl; ?>.Cover note no.<? echo $result[0][csf("cover_note_no")];?> Date :<? echo $cover_note_date;?>
		            </td>
		            <td width="25" ></td>
		        </tr>
				<?
			}
		?>
        <tr>
            <td width="25" ></td>
            <td width="650" align="left">
            <? echo ++$sl; ?>.H.S. Code:<? echo chop ($pi_number_date_arr[$result[0][csf("id")]]["hs_code"],",") ;?>
            </td>
            <td width="25" ></td>
        </tr>
        <?
	       	if($result[0][csf("tenor")]>=180)
	        {
	        	?>
	        	<tr>
	            <td width="25" ></td>
	            <td width="650" align="left">
	            <? echo ++$sl; ?>.UPAS Rate: <strong><? echo $result[0][csf("upas_rate")] ." % + ". $result[0][csf("tenor")] ;?></strong> days LIBOR.
	            </td>
	            <td width="25" ></td>
	        </tr>
		<?
        }
        ?>
        <tr>
            <td width="25" ></td>
            <td width="650" align="left">
            <? echo ++$sl; ?>.Discrepancy Charge :Nil
            </td>
            <td width="25" ></td>
        </tr>
        <tr>
            <td width="25" ></td>
            <td width="650" align="left">
            <? echo ++$sl; ?>.On maturity payment made in equivalent USD is acceptable.
            </td>
            <td width="25" ></td>
        </tr>
        <tr>
            <td width="25" ></td>
            <td width="650" align="left">
            <? echo ++$sl; ?>.Charges: All charges including inside Bangladesh are on our account & all charges including outside Bangladesh are on beneficiary's account.
            </td>
            <td width="25" ></td>
        </tr>
        <tr>
        	<td colspan="3" height="15"></td>
        </tr>
    	<tr>
            <td width="25" ></td>
            <td width="650" align="left">
            We would therefore request you to kindly open the above L/C as early possible.
            </td>
            <td width="25" ></td>
        </tr>
        <tr>
        	<td colspan="3" height="20"></td>
        </tr>
        <tr>
            <td width="25" ></td>
            <td width="650" align="left">
            Thanking You,
            </td>
            <td width="25" ></td>
        </tr>
        <tr>
        	<td colspan="3" height="80"></td>
        </tr>
        <tr>
            <td width="25" ></td>
            <td width="650" align="left">
			<?
			echo $company_name;
			?></td>
            <td width="25" ></td>
        </tr>
       <tr>
        	<td colspan="3" height="50"></td>
       </tr>
    </table>
    <?
	exit();
}

if($action=="btb_import_lc_letter34")
{
	$sql_bank_info=sql_select("select id, contact_person, bank_name, branch_name, address from lib_bank ");
	foreach($sql_bank_info as $row)
	{
		$bank_dtls_arr[$row[csf("id")]]["contact_person"]=$row[csf("contact_person")];
		$bank_dtls_arr[$row[csf("id")]]["bank_name"]=$row[csf("bank_name")];
		$bank_dtls_arr[$row[csf("id")]]["branch_name"]=$row[csf("branch_name")];
		$bank_dtls_arr[$row[csf("id")]]["address"]=$row[csf("address")];
	}

	$is_lc_sc_sql=sql_select("select lc_sc_id, is_lc_sc from com_btb_export_lc_attachment where import_mst_id=$data and is_deleted=0 and status_active=1");
	if(empty($is_lc_sc_sql)) {echo "May be attachment not complete yet";die;}
	foreach($is_lc_sc_sql as $row)
	{
		if($row[csf('is_lc_sc')]==0){
			$sql_lc=sql_select("SELECT id,export_lc_no,lc_date,lc_value,currency_name FROM com_export_lc  where id=".$row[csf('lc_sc_id')]);
			foreach($sql_lc as $lc_row)
			{
				$export_lc_sc_no_arr[$lc_row[csf("id")]]=$lc_row[csf("export_lc_no")]." DT :".$lc_row[csf("lc_date")]." Value: ".$currency[$lc_row[csf("currency_name")]]." ".$currency_symbolArr[$lc_row[csf("currency_name")]]." ".number_format($lc_row[csf("lc_value")],2);

				$export_lc_sc_no_new_arr[$lc_row[csf("id")]]=$lc_row[csf("export_lc_no")]." DT :".$lc_row[csf("lc_date")];
				// $lc_sc_value+=$lc_row[csf("lc_value")];
			}
		}
		else{
			$sql_sc=sql_select("SELECT id,contract_no,contract_date,contract_value,currency_name FROM com_sales_contract where id=".$row[csf('lc_sc_id')]);
			foreach($sql_sc as $sc_row)
			{
				$export_lc_sc_no_arr[$sc_row[csf("id")]]=$sc_row[csf("contract_no")]." DT :".$sc_row[csf("contract_date")]." Value: ".$currency[$sc_row[csf("currency_name")]]." ".$currency_symbolArr[$sc_row[csf("currency_name")]]." ".number_format($sc_row[csf("contract_value")],4);

				$export_lc_sc_no_new_arr[$sc_row[csf("id")]]=$sc_row[csf("contract_no")]." DT :".$sc_row[csf("contract_date")];
				// $lc_sc_value+=$sc_row[csf("contract_value")];
			}
		}
	}

	if($db_type==2)
	{
		$sql_com="SELECT a.id,a.btb_system_id, a.application_date, a.importer_id, a.item_category_id, a.origin, a.issuing_bank_id, a.currency_id, a.supplier_id,a.lc_value,a.margin,a.lc_date,a.tenor,a.last_shipment_date,a.lc_expiry_date,a.upas_rate,a.cover_note_no,a.cover_note_date, a.payterm_id, a.maturity_from_id  from com_btb_lc_master_details a where a.id=$data and a.is_deleted = 0 AND a.status_active = 1 group by a.id,a.btb_system_id, a.application_date, a.importer_id, a.item_category_id, a.origin, a.issuing_bank_id, a.currency_id, a.supplier_id, a.lc_value, a.margin,a.lc_date,a.tenor,a.last_shipment_date,a.lc_expiry_date,a.upas_rate,a.cover_note_no,a.cover_note_date,a.payterm_id, a.maturity_from_id";
	}
	elseif($db_type==0)
	{
		$sql_com="SELECT a.id,a.btb_system_id, a.application_date, a.importer_id, a.item_category_id, a.origin, a.issuing_bank_id, a.currency_id, a.supplier_id, a.lc_value, a.margin,a.lc_date,a.tenor,a.last_shipment_date,a.lc_expiry_date,a.upas_rate,a.cover_note_no,a.cover_note_date,a.payterm_id, a.maturity_from_id from com_btb_lc_master_details a where a.id=$data and a.is_deleted = 0 AND a.status_active = 1 group by a.id,a.btb_system_id, a.application_date, a.importer_id, a.item_category_id, a.supplier_id, a.origin, a.issuing_bank_id, a.currency_id, a.lc_value, a.margin,a.lc_date,a.tenor,a.last_shipment_date,a.lc_expiry_date,a.upas_rate,a.cover_note_no,a.cover_note_date,a.payterm_id, a.maturity_from_id";
	}
	// echo $sql_com;
	$result=sql_select($sql_com);

	$supplier_name = return_field_value("supplier_name","lib_supplier","id=".$result[0][csf("supplier_id")],"supplier_name");
	$supplier_address_1 = return_field_value("address_1","lib_supplier","id=".$result[0][csf("supplier_id")],"address_1");
	$supplier_address_2 = return_field_value("address_2","lib_supplier","id=".$result[0][csf("supplier_id")],"address_2");

	$sql_pi=sql_select("SELECT a.pi_number, a.pi_date,a.hs_code,b.com_btb_lc_master_details_id,rtrim(xmlagg(xmlelement(e,c.item_category_id,',').extract('//text()') order by c.item_category_id).GetClobVal(),',') AS item_category_ids from com_pi_master_details a ,com_btb_lc_pi b,com_pi_item_details c where a.id=b.pi_id and com_btb_lc_master_details_id=$data and a.id=c.pi_id and c.status_active=1 and a.status_active=1 and b.status_active=1 group by a.pi_number, a.pi_date,a.hs_code,b.com_btb_lc_master_details_id");

	$pi_number_date_arr=array();
	foreach($sql_pi as $row)
	{
		$pi_number_date_arr[$row[csf("com_btb_lc_master_details_id")]]["pi_number"].=$row[csf("pi_number")]." DT.".change_date_format($row[csf("pi_date")]).",";
	}
	//print_r($pi_number_date_arr);
	$sl=1;
	?>
    <table width="700" cellpadding="0" align="left" cellspacing="0" border="0">
        <tr>
            <td width="25" ></td>
            <td width="650" align="left">Ref No.:<?=$result[0][csf("btb_system_id")]?> </td>
            <td width="25" ></td>
        </tr>
		<tr>
            <td width="25" ></td>
            <td width="650" align="left">Date:<?=change_date_format($result[0][csf("application_date")])?> </td>
            <td width="25" ></td>
        </tr>
		<tr height="40px">
           
        </tr>
		<tr>
            <td width="25" ></td>
            <td width="650" align="left">To </td>
            <td width="25" ></td>
        </tr>
        <tr>
            <td width="25" ></td>
            <td width="650" align="left">
            <?
				echo $bank_dtls_arr[$result[0][csf("issuing_bank_id")]]["contact_person"]."<br>".$bank_dtls_arr[$result[0][csf("issuing_bank_id")]]["bank_name"]."<br>".$bank_dtls_arr[$result[0][csf("issuing_bank_id")]]["branch_name"]."<br>".$bank_dtls_arr[$result[0][csf("issuing_bank_id")]]["address"];
			?>

            </td>
            <td width="25" ></td>
        </tr>
		<tr height="40px">
           
        </tr>
        <tr>
            <td width="25" ></td>
            <td width="650" align="left"><strong>Sub: Request for opening of Back to Back L/C for <?=$currency[$result[0][csf("currency_id")]]." ".$currency_symbolArr[$result[0][csf("currency_id")]]." ".number_format($result[0][csf("lc_value")],4)?> under Export S/C or LC NO.<? echo implode(', ',$export_lc_sc_no_arr);?></strong>					
            </td>
            <td width="25" ></td>
        </tr>
        <tr>
        	<td colspan="3" height="20"></td>
        </tr>
        <tr>
            <td width="25" ></td>
            <td width="650" align="left"> Dear Sir, </td>
            <td width="25" ></td>
        </tr>
        <tr>
            <td width="25" ></td>
            <td width="650" align="left">
			We would like to request you to kindly arrange to open <strong>Back To Back L/C</strong> against the Lien mentioned S/C or LC No: <strong><? echo implode(', ',$export_lc_sc_no_new_arr);?></strong>. for Value <strong> <?=$currency[$result[0][csf("currency_id")]]." ".$currency_symbolArr[$result[0][csf("currency_id")]]." ".number_format($result[0][csf("lc_value")],4)?> </strong> in favor of, <strong><?=$supplier_name.". ".$supplier_address_1?> . under P.I No:<?=rtrim($pi_number_date_arr[$row[csf("com_btb_lc_master_details_id")]]["pi_number"],",") ?> </strong>
            </td>
            </td>
            <td width="25" ></td>
        </tr>
        <tr>
        	<td colspan="3" height="15"></td>
        </tr>
        <tr>
            <td width="25" ></td>
            <td width="650" align="left">
			Please add in the L/C:
            </td>
            <td width="25" ></td>
        </tr>
        <tr>
            <td width="25" ></td>
            <td width="650" align="left">
				<?
				    if($result[0][csf("payterm_id")]==1){
					    echo "1.  ".$pay_term[$result[0][csf("payterm_id")]]." from the date of ".$maturity_from[$result[0][csf("maturity_from_id")]];
					}else if($result[0][csf("payterm_id")]==2){
						echo "1.  ".$result[0][csf("TENOR")]." days ".$pay_term[$result[0][csf("payterm_id")]]." from the date of ".$maturity_from[$result[0][csf("maturity_from_id")]];
					}else{
						echo "1.  ".$pay_term[$result[0][csf("payterm_id")]]."from the date of ".$maturity_from[$result[0][csf("maturity_from_id")]];

					}
				?> <br>
				2. Payment should be in US Dollar. <br>
				3. Shipment & Expiry Date: <?=change_date_format($result[0][csf("last_shipment_date")])." & ".change_date_format($result[0][csf("lc_expiry_date")]);?>

			</td>
            <td width="25" ></td>
        </tr>
        <tr>
        	<td colspan="3" height="15"></td>
        </tr>
    	<tr>
            <td width="25" ></td>
            <td width="650" align="left">Your earliest action will be highly appreciated in this regard </td>
            <td width="25" ></td>
        </tr>
        <tr>
        	<td colspan="3" height="20"></td>
        </tr>
        <tr>
            <td width="25" ></td>
            <td width="650" align="left"> Thanking You. </td>
            <td width="25" ></td>
        </tr>
        <tr>
        	<td colspan="3" height="80"></td>
        </tr>
        <tr>
            <td width="25" ></td>
            <td width="650" align="left">-----------------------</td>
            <td width="25" ></td>
        </tr>
		<tr>
            <td width="25" ></td>
            <td width="650" align="left"><b> Authorized Signature</b></td>
            <td width="25" ></td>
        </tr>
       <tr>
        	<td colspan="3" height="50"></td>
       </tr>
    </table>
    <?
	exit();
}


if($action=="btb_import_lc_letter3")
{
	//echo load_html_head_contents("BTB Import Lc Letter","../../", 1, 1, $unicode,'','');
	$sql_bank_info=sql_select("select id, contact_person, bank_name, branch_name, address,designation from lib_bank ");
	foreach($sql_bank_info as $row)
	{
		$bank_dtls_arr[$row[csf("id")]]["contact_person"]=$row[csf("contact_person")];
		$bank_dtls_arr[$row[csf("id")]]["bank_name"]=$row[csf("bank_name")];
		$bank_dtls_arr[$row[csf("id")]]["branch_name"]=$row[csf("branch_name")];
		$bank_dtls_arr[$row[csf("id")]]["address"]=$row[csf("address")];
		$bank_dtls_arr[$row[csf("id")]]["designation"]=$row[csf("designation")];
	}

	$sql_lc=sql_select("SELECT id,export_lc_no,tenor FROM com_export_lc ");
	foreach($sql_lc as $row)
	{
		$export_lc_no_arr[$row[csf("id")]]["export_lc_no"]=$row[csf("export_lc_no")];
		$export_lc_no_arr[$row[csf("id")]]["tenor"]=$row[csf("tenor")];
	}
	$sql_sc=sql_select("SELECT id,contract_no,tenor FROM com_sales_contract ");
	foreach($sql_sc as $row)
	{
		$export_sc_no_arr[$row[csf("id")]]["contract_no"]=$row[csf("contract_no")];
		$export_sc_no_arr[$row[csf("id")]]["tenor"]=$row[csf("tenor")];
	}

	//echo $data."TIPU";die;
	if($db_type==2)
	{
		$sql_com="SELECT a.id,a.btb_system_id, a.application_date, a.importer_id, a.item_category_id, a.origin, a.issuing_bank_id, a.currency_id, a.supplier_id,a.lc_value,a.margin,a.lc_date,a.tenor,a.last_shipment_date,a.lc_expiry_date,a.upas_rate,a.cover_note_no,a.cover_note_date, a.lcaf_no
		from com_btb_lc_master_details a
		where a.id=$data and a.is_deleted = 0 AND a.status_active = 1 group by a.id,a.btb_system_id, a.application_date, a.importer_id, a.item_category_id, a.origin, a.issuing_bank_id, a.currency_id, a.supplier_id, a.lc_value, a.margin,a.lc_date,a.tenor,a.last_shipment_date,a.lc_expiry_date,a.upas_rate,a.cover_note_no,a.cover_note_date, a.lcaf_no";
	}
	elseif($db_type==0)
	{
		$sql_com="SELECT a.id,a.btb_system_id, a.application_date, a.importer_id, a.item_category_id, a.origin, a.issuing_bank_id, a.currency_id, a.supplier_id, a.lc_value, a.margin,a.lc_date,a.tenor,a.last_shipment_date,a.lc_expiry_date,a.upas_rate,a.cover_note_no,a.cover_note_date, a.lcaf_no
		from com_btb_lc_master_details a
		where a.id=$data and a.is_deleted = 0 AND a.status_active = 1 group by a.id,a.btb_system_id, a.application_date, a.importer_id, a.item_category_id, a.supplier_id, a.origin, a.issuing_bank_id, a.currency_id, a.lc_value, a.margin,a.lc_date,a.tenor,a.last_shipment_date,a.lc_expiry_date,a.upas_rate,a.cover_note_no,a.cover_note_date, a.lcaf_no";
	}
	//echo $sql_com;
	$result=sql_select($sql_com);
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$company_name = return_field_value("company_name","lib_company","id=".$result[0][csf("importer_id")],"company_name");
	$supplier_name = return_field_value("supplier_name","lib_supplier","id=".$result[0][csf("supplier_id")],"supplier_name");
	$country_name = return_field_value("country_name"," lib_country","id=".$result[0][csf("origin")],"country_name");
	$designation = return_field_value("custom_designation","lib_designation","id=".$bank_dtls_arr[$result[0][csf("issuing_bank_id")]]["designation"],"custom_designation");

	$com_btb_lc_id = $result[0][csf('id')];

	
	// echo "SELECT a.import_mst_id, b.contract_no as lc_sc_no,b.buyer_name,null as import_btb, b.internal_file_no, b.contract_value as lc_sc_val , c.lc_date, b.contract_date, a.is_lc_sc
	// from  com_btb_export_lc_attachment a, com_sales_contract b, com_export_lc c
	// where a.lc_sc_id=c.id and a.lc_sc_id=b.id and a.status_active=1 and a.is_lc_sc=1 and a.import_mst_id=$data
	// union all select a.import_mst_id, b.export_lc_no as lc_sc_no,b.buyer_name,b.import_btb, b.internal_file_no, b.lc_value as lc_sc_val, b.lc_date, null as lc_null_date, a.is_lc_sc
	// from com_btb_export_lc_attachment a, com_export_lc b
	// where a.lc_sc_id=b.id and a.status_active=1 and a.is_lc_sc=0 and a.import_mst_id=$data";

	$sql_pi=sql_select("SELECT a.import_mst_id, b.contract_no as lc_sc_no,b.buyer_name,null as import_btb, b.internal_file_no, b.contract_value as lc_sc_val , c.lc_date, b.contract_date, a.is_lc_sc
		from  com_btb_export_lc_attachment a, com_sales_contract b, com_export_lc c
		where a.lc_sc_id=c.id and a.lc_sc_id=b.id and a.status_active=1 and a.is_lc_sc=1 and a.import_mst_id=$data
		union all select a.import_mst_id, b.export_lc_no as lc_sc_no,b.buyer_name,b.import_btb, b.internal_file_no, b.lc_value as lc_sc_val, b.lc_date, null as lc_null_date, a.is_lc_sc
		from com_btb_export_lc_attachment a, com_export_lc b
		where a.lc_sc_id=b.id and a.status_active=1 and a.is_lc_sc=0 and a.import_mst_id=$data");
		$sql_lc__arr=array();
		foreach($sql_pi as $row)
		{
			$sql_lc__arr[$row[csf("import_mst_id")]]["lc_sc_no"]=$row[csf("lc_sc_no")].",";
			$sql_lc__arr[$row[csf("import_mst_id")]]["internal_file_no"]=$row[csf("internal_file_no")].",";
			$sql_lc__arr[$row[csf("import_mst_id")]]["lc_sc_val"]=$row[csf("lc_sc_val")].",";
			$sql_lc__arr[$row[csf("import_mst_id")]]["lc_date"]=$row[csf("lc_date")].",";
			$sql_lc__arr[$row[csf("import_mst_id")]]["contract_date"]=$row[csf("contract_date")].",";
			$sql_lc__arr[$row[csf("import_mst_id")]]["is_lc_sc"]=$row[csf("is_lc_sc")];
			$sql_lc__arr[$row[csf("import_mst_id")]]["buyer_name"]=$row[csf("buyer_name")].",";
			$sql_lc__arr[$row[csf("import_mst_id")]]["import_btb"]=$row[csf("import_btb")];
		}
		/*echo '<pre>';
		print_r($sql_lc__arr);*/

	$sql_pi=sql_select("SELECT a.pi_number, a.pi_date,a.hs_code,b.com_btb_lc_master_details_id, rtrim(xmlagg(xmlelement(e,c.item_category_id,',').extract('//text()') order by c.item_category_id).GetClobVal(),',') AS item_category_ids
		from com_pi_master_details a ,com_btb_lc_pi b,com_pi_item_details c
		where a.id=b.pi_id and com_btb_lc_master_details_id=$data and a.id=c.pi_id and c.status_active=1 and a.status_active=1 and b.status_active=1 group by a.pi_number, a.pi_date,a.hs_code,b.com_btb_lc_master_details_id");
	if($result[0][csf("tenor")]>=180)
	{
		$upas_string="UPAS L/C for";
		$upas_string2=$result[0][csf("tenor")] ." days from thr date of negotiation (UPAS LC at sight)";
	}
	else
	{
		$upas_string="L/C for";

		$upas_string2="L/C tenor will be At Sight/Deferred";
	}

	$pi_number_date_arr=array();
	foreach($sql_pi as $row)
	{
		if($db_type==2) $row[csf("item_category_ids")] = $row[csf("item_category_ids")]->load();
		$pi_number_date_arr[$row[csf("com_btb_lc_master_details_id")]]["pi_number_date"].=$row[csf("pi_number")]." dated.".change_date_format($row[csf("pi_date")]).",";

		$total_pi_arr[$row[csf("com_btb_lc_master_details_id")]]["total_pi"].=$row[csf("pi_number")];

		$pi_number_date_arr[$row[csf("com_btb_lc_master_details_id")]]["hs_code"].=$row[csf("hs_code")].",";
		$pi_number_date_arr[$row[csf("com_btb_lc_master_details_id")]]["item_category_ids"].=$row[csf("item_category_ids")].",";
	}

	$pi_num_exp=explode(",",chop($total_pi_arr[$result[0][csf("id")]]['total_pi'],","));
	$pi_num_count = count($pi_num_exp);
	/*echo '<pre>';
	print_r($test2);*/

	$itemCategory="";
	$l=1;
	$cat_id_arr=array_unique(explode(",",chop($pi_number_date_arr[$result[0][csf("id")]]['item_category_ids'],",")));
	//print_r($cat_id_arr);
	foreach($cat_id_arr as $cat_id)
	{
		if($l!=1) $itemCategory .=", ";
		$itemCategory .=$item_category[$cat_id];
		$l++;
	}
	$itemCategory; ?><? chop ($pi_number_date_arr[$result[0][csf("id")]]["pi_number_date"],",") ;
	?>

		<style type="text/css">
			.a4size {
	           width: 21cm;
	           height: 26.7cm;
	           font-family: Cambria, Georgia, serif;
	        }
	        @media print {
	        .a4size{ font-family: Cambria;font-size: 18px;margin: 80px 100PX 54px 25px;size: A4 portrait;
	            }
	        }
		</style>
		<div class="a4size">
		    <table width="794" cellpadding="0" cellspacing="0" border="0" >
		        <tr>
		            <td width="25" ></td>
		            <td width="650" align="left">DATE : <? echo change_date_format($result[0][csf("application_date")]); ?> </td>
		            <td width="25" ></td>
		        </tr>
		        <tr>
		            <td width="25" ></td>
		            <td width="650" align="left">INTERNAL REF : <? echo chop($sql_lc__arr[$result[0][csf("id")]]["internal_file_no"],","); ?> </td>
		            <td width="25" ></td>
		        </tr>
		        <tr>
		            <td width="25"></td>
		            <td width="650" align="left">SYSTEM REF : <? echo $result[0][csf("btb_system_id")]; ?></td>
		            <td width="25" ></td>
		        </tr>
		        <tr>
		        	<td colspan="3" height="20">&nbsp;</td>
		        </tr>
		        <tr>
		        	<td colspan="3" height="20">&nbsp;</td>
		        </tr>
		        <br>
		        <tr>
		            <td width="25"></td>
		            <td width="650" align="left">TO</td>
		            <td width="25" ></td>
		        </tr>
		        <tr>
		            <td width="25" ></td>
		            <td width="650" align="left">
		            <?
						echo 'THE '.strtoupper($designation)."<br>".strtoupper($bank_dtls_arr[$result[0][csf("issuing_bank_id")]]["bank_name"]."<br>".$bank_dtls_arr[$result[0][csf("issuing_bank_id")]]["address"]);
					?>

		            </td>
		            <td width="25" ></td>
		        </tr>
		        <tr>
		        	<td colspan="3" height="30"></td>
		        </tr>
		        <tr>
		        	<td colspan="3" height="30"></td>
		        </tr>

		        <tr>
		            <td width="25" ></td>
		            <td width="650" align="left">
		            <strong><u>SUBJECT:</u></strong> REQUEST FOR OPENING OF BTB L/C AT <? echo number_format($result[0][csf("tenor")]); ?> DAYS SIGHT TENOR <? echo $currency[$result[0][csf("currency_id")]]."&nbsp;".def_number_format($result[0][csf("lc_value")],2); ?> TO IMPORT <? echo strtoupper($itemCategory);?> FROM <?php echo strtoupper($country_name); ?> AGAINST <? echo ($sql_lc__arr[$result[0][csf("id")]]["is_lc_sc"]==0) ? "EXPORT L/C NO: " : "SALES CONTRACT NO: "; echo chop($sql_lc__arr[$result[0][csf("id")]]["lc_sc_no"],","); ?> DATE <? echo ($sql_lc__arr[$result[0][csf("id")]]["is_lc_sc"]==1) ? change_date_format(chop($sql_lc__arr[$result[0][csf("id")]]["contract_date"],",")) : change_date_format(chop($sql_lc__arr[$result[0][csf("id")]]["lc_date"],","));?> TOTAL CONTRACT VALUE <? echo $currency[$result[0][csf("currency_id")]]."&nbsp;".def_number_format(chop($sql_lc__arr[$result[0][csf("id")]]["lc_sc_val"],","),2); ?>
					 .
		            </td>
		            <td width="25" ></td>
		        </tr>
		        <tr>
		        	<td colspan="3" height="20"></td>
		        </tr>
		        <tr>
		        	<td colspan="3" height="20"></td>
		        </tr>
		        <tr>
		            <td width="25" ></td>
		            <td width="650" align="left"> DEAR SIR, </td>
		            <td width="25" ></td>
		        </tr>
		        <tr>
		            <td width="25" ></td>
		            <td width="650" align="left">
		            WE INTENDED TO OPEN L/C THROUGH YOUR PROVIDED BANK ON THE FOLLOWING TERMS AND CONDITIONS
		            </td>
		            <td width="25" ></td>
		        </tr>
		    </table>
		    <table width="794" cellpadding="0" cellspacing="0" border="0">
		        <tr>
		        	<td colspan="3" height="15"></td>
		        </tr>
		         <tr>
		            <td width="25" >&nbsp;</td>
		            <td width="330">ITEM OF IMPORT</td>
		            <td width="15" >:</td>
		            <td width="330"><? echo strtoupper($itemCategory);?></td>

		        </tr>
		        <tr>
		            <td width="25" >&nbsp;</td>
		            <td width="330">AMOUNT OF L/C</td>
		            <td width="15" >:</td>
		            <td width="330"><? echo $currency[$result[0][csf("currency_id")]]."&nbsp;".def_number_format($result[0][csf("lc_value")],2); ?></td>
		        </tr>
		        <tr>
		            <td width="25" >&nbsp;</td>
		            <td width="330">MARGIN</td>
		            <td width="15" >:</td>
		            <td width="330"><? echo number_format($result[0][csf("margin")]);?>%</td>
		        </tr>
		        <tr>
		            <td width="25" >&nbsp;</td>
		            <td width="330">NAME OF SUPPLIER</td>
		            <td width="15" >:</td>
		            <td width="330"><? echo strtoupper($supplier_name);?></td>
		        </tr>
		        <tr>
		            <td width="25" >&nbsp;</td>
		            <td width="330">COUNTRY OF ORIGIN</td>
		            <td width="15" >:</td>
		            <td width="330"><? echo strtoupper($country_name);?></td>
		        </tr>
		        <tr>
		            <td width="25" >&nbsp;</td>
		            <td width="330">H.S CODE</td>
		            <td width="15" >:</td>
		            <td width="330"><? echo $pi_number_date_arr[$result[0][csf("id")]]["hs_code"]; ?></td>
		        </tr>
		        <tr>
		            <td width="25" >&nbsp;</td>
		            <td width="330">INDENT /PRO FORMA INVOICE</td>
		            <td width="15" >:</td>
		            <td width="330">
		            	<?
		            		if ($pi_num_count == 1)
		            		{
		            			echo strtoupper($sql_pi[0][csf("pi_number")]).' DT. '.strtoupper(change_date_format($sql_pi[0][csf('pi_date')],","));
		            		}
		            		else{
								$pi_num_count = sprintf("%02d", $pi_num_count); // add leading zero to single digit number
		            			echo 'AS PER PI ('.$pi_num_count.')';
		            		}
		            	?>
		            </td>
		        </tr>
		        <tr>
		            <td width="25" >&nbsp;</td>
		            <td width="330">END OF USE ITEM</td>
		            <td width="15" >:</td>
		            <td width="330">FOR READY MADE GARMENTS INDUSTRY</td>
		        </tr>
				<tr>
		            <td width="25" >&nbsp;</td>
		            <td width="330">Buyer Name</td>
		            <td width="15" >:</td>
		            <td width="330">
						<? 
						if($sql_lc__arr[$result[0][csf("id")]]["is_lc_sc"]==0)
						{
							if($sql_lc__arr[$result[0][csf("id")]]["import_btb"] == 1)
							{
								$import_btb_buyer_comp =   $comp[chop($sql_lc__arr[$result[0][csf("id")]]["buyer_name"],",")];
							}
							else
							{
								$import_btb_buyer_comp =  $buyer_arr[chop($sql_lc__arr[$result[0][csf("id")]]["buyer_name"],",")];
							}
						}
						elseif($sql_lc__arr[$result[0][csf("id")]]["is_lc_sc"]==1)
						{
							$import_btb_buyer_comp =  $buyer_arr[chop($sql_lc__arr[$result[0][csf("id")]]["buyer_name"],",")];
						}
						echo  $import_btb_buyer_comp;
						?>
					</td>
		        </tr>			


		        <tr>
		        	<td colspan="3" height="15"></td>
		        </tr>
		        <tr>
		        	<td colspan="3" height="15"></td>
		        </tr>
		    </table>
		    <table width="794" cellpadding="0" cellspacing="0" border="0">
				    <tr>
		        		<td colspan="3" height="15"></td>
		        	</tr>

		        <tr>
		        	<td colspan="3" height="15"></td>
		        </tr>
		        <tr>
		            <td width="25" ></td>
		            <td width="650" align="left">
		            WE ALSO AGREE AND UNDERTAKE TO THE FOLLOWING OTHER TERMS AND CONDITIONS.<br>
		            </td>
		            <td width="25" ></td>
		        </tr>
		        <tr>
		        	<td colspan="3" height="15"></td>
		        </tr>

		        <tr>
		            <td width="25" ></td>
		            <td width="650" align="left">
		            1. YOU MAY REGISTER THE FOLLOWING LCA FORM NO. <? echo $result[0][csf("lcaf_no")]; ?> WITH BANGLADESH BANK REGISTRATION UNIT PRIOR TO OPENING OF THE LC.
		            </td>
		            <td width="25" ></td>
		        </tr>
		        <tr>
		        	<td colspan="3" height="10"></td>
		        </tr>
		        <tr>
		            <td width="25" ></td>
		            <td width="650" align="left">
		            2. THE SUPPLIER IS GENUINE. IN THE EVENT OF OTHER FAILURE TO SUPPLY THE ITEM UNDER PROPOSED IMPORT, WE REMAIN RESPONSIBLE FOR REPATRIATION OF THE L/C AMOUNT AND BEAR ALL RELEVANT COSTS.
		            </td>
		            <td width="25" ></td>
		        </tr>
				<tr>
		        	<td colspan="3" height="10"></td>
		        </tr>
		        <tr>
		            <td width="25" ></td>
		            <td width="650" align="left">
		            3. MUSHOK 6.3 must be presented along with original documents. 
		            </td>
		            <td width="25" ></td>
		        </tr>
		        <tr>
		        	<td colspan="3" height="15"></td>
		        </tr>
		        <tr>
		        	<td colspan="3" height="15"></td>
		        </tr>
		        <tr>
		        	<td colspan="3" height="15"></td>
		        </tr>
		        <tr>
		            <td width="25" ></td>
		            <td width="650" align="left">
		            KINDLY ARRANGE OPENING OF THE L/C  ON PROPOSED TERMS AND CONDITIONS.
		            <br><br>
		            THANKING YOU,
		            </td>
		            <td width="25" ></td>
		        </tr>
		        <tr>
		        	<td colspan="3" height="70"></td>
		        </tr>
		        <tr>
		            <td width="25" ></td>
		            <td width="650" align="left">
					<?

					echo strtoupper($company_name);

					?></td>
		            <td width="25" ></td>
		        </tr>
		    </table>
		</div>
    <?
	exit();
}

if($action=="lc_opening_later")
{
	//echo load_html_head_contents("BTB Import Lc Letter","../../", 1, 1, $unicode,'','');
	$sql_bank_info=sql_select("select id, contact_person, bank_name, branch_name, address,designation from lib_bank ");
	foreach($sql_bank_info as $row)
	{
		$bank_dtls_arr[$row[csf("id")]]["contact_person"]=$row[csf("contact_person")];
		$bank_dtls_arr[$row[csf("id")]]["bank_name"]=$row[csf("bank_name")];
		$bank_dtls_arr[$row[csf("id")]]["branch_name"]=$row[csf("branch_name")];
		$bank_dtls_arr[$row[csf("id")]]["address"]=$row[csf("address")];
		$bank_dtls_arr[$row[csf("id")]]["designation"]=$row[csf("designation")];
	}

	//echo $data."TIPU";die;
	
	$sql_com="SELECT a.id,a.btb_system_id, a.application_date, a.importer_id, a.item_category_id, a.issuing_bank_id, a.currency_id, a.supplier_id, a.lc_value
	from com_btb_lc_master_details a
	where a.id=$data and a.is_deleted = 0 AND a.status_active = 1 group by a.id,a.btb_system_id, a.application_date, a.importer_id, a.item_category_id, a.issuing_bank_id, a.currency_id, a.supplier_id, a.lc_value";

	//echo $sql_com;
	$result=sql_select($sql_com);

	$supplier_name_arr = return_library_array('SELECT id, supplier_name FROM lib_supplier','id','supplier_name');
	$designation = return_field_value("custom_designation","lib_designation","id=".$bank_dtls_arr[$result[0][csf("issuing_bank_id")]]["designation"],"custom_designation");
	//$com_btb_lc_id = $result[0][csf('id')];

	$sql_pi=sql_select("SELECT a.import_mst_id, b.contract_no as lc_sc_no, b.internal_file_no, b.contract_value as lc_sc_val , c.lc_date, b.contract_date, a.is_lc_sc, b.bank_file_no 
	from  com_btb_export_lc_attachment a, com_sales_contract b, com_export_lc c
	where a.lc_sc_id=c.id and a.lc_sc_id=b.id and a.status_active=1 and a.is_lc_sc=1 and a.import_mst_id=$data
	union all select a.import_mst_id, b.export_lc_no as lc_sc_no, b.internal_file_no, b.lc_value as lc_sc_val, b.lc_date, null as lc_null_date, a.is_lc_sc, b.bank_file_no 
	from com_btb_export_lc_attachment a, com_export_lc b
	where a.lc_sc_id=b.id and a.status_active=1 and a.is_lc_sc=0 and a.import_mst_id=$data");
	
	$sql_lc_arr=array();
	foreach($sql_pi as $row)
	{
		if ($row[csf("is_lc_sc")]==1) 
		{
			$sql_lc_arr[$row[csf("import_mst_id")]]["sc_no"].=$row[csf("lc_sc_no")].",";
			$sql_lc_arr[$row[csf("import_mst_id")]]["sc_date"].=$row[csf("lc_date")].",";
			$sql_lc_arr[$row[csf("import_mst_id")]]["sc_bank_file"].=$row[csf("bank_file_no")].",";
		}
		else{
			$sql_lc_arr[$row[csf("import_mst_id")]]["lc_no"].=$row[csf("lc_sc_no")].",";
			$sql_lc_arr[$row[csf("import_mst_id")]]["lc_date"].=$row[csf("lc_date")].",";
			$sql_lc_arr[$row[csf("import_mst_id")]]["lc_bank_file"].=$row[csf("bank_file_no")].",";
		}
		
		//$sql_lc_arr[$row[csf("import_mst_id")]]["internal_file_no"]=$row[csf("internal_file_no")].",";
		//$sql_lc_arr[$row[csf("import_mst_id")]]["lc_sc_val"]=$row[csf("lc_sc_val")].",";
		//$sql_lc_arr[$row[csf("import_mst_id")]]["contract_date"]=$row[csf("contract_date")].",";
		//$sql_lc_arr[$row[csf("import_mst_id")]]["is_lc_sc"]=$row[csf("is_lc_sc")];
	}
	/*echo '<pre>';
	print_r($sql_lc_arr);*/

	$sql_pi=sql_select("SELECT a.pi_number, a.pi_date,a.hs_code,b.com_btb_lc_master_details_id, rtrim(xmlagg(xmlelement(e,c.item_category_id,',').extract('//text()') order by c.item_category_id).GetClobVal(),',') AS item_category_ids
	from com_pi_master_details a ,com_btb_lc_pi b,com_pi_item_details c
	where a.id=b.pi_id and com_btb_lc_master_details_id=$data and a.id=c.pi_id and c.status_active=1 and a.status_active=1 and b.status_active=1 group by a.pi_number, a.pi_date,a.hs_code,b.com_btb_lc_master_details_id");

	$pi_number_date_arr=array();
	foreach($sql_pi as $row)
	{
		if($db_type==2) $row[csf("item_category_ids")] = $row[csf("item_category_ids")]->load();
		$total_pi_arr[$row[csf("com_btb_lc_master_details_id")]]["total_pi"].=$row[csf("pi_number")].",";
		$pi_number_date_arr[$row[csf("com_btb_lc_master_details_id")]]["item_category_ids"].=$row[csf("item_category_ids")].",";
	}

	$pi_num_exp=explode(",",chop($total_pi_arr[$result[0][csf("id")]]['total_pi'],","));
	$pi_num_count = count($pi_num_exp);
	/*echo '<pre>';
	print_r($test2);*/

	// =======For Header and Footer Start=========
	$country_arr = return_library_array("select id,country_name from lib_country where is_deleted=0","id","country_name");
	$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id='".$result[0][csf("importer_id")]."'");
	$adderess='';
	foreach ($nameArray as $company_add)
	{
		if($company_add[csf('plot_no')]!=''){ $adderess.= " Plot No: ".$company_add[csf('plot_no')]; }
		if($company_add[csf('road_no')]!=''){ $adderess.=" Road No:  ".$company_add[csf('road_no')]; }
		if($company_add[csf('block_no')]!=''){ $adderess.= " Block No:  ".$company_add[csf('block_no')];}
		if($company_add[csf('city')]!=''){ $adderess.=" City No:  ".$company_add[csf('city')];}
		if($company_add[csf('zip_code')]!=''){ $adderess.=" Zip Code:  ".$company_add[csf('zip_code')]; }
		if($company_add[csf('country_id')]!=''){ $adderess.=" Country:  ".$country_arr[$company_add[csf('country_id')]]; }
	}
	$factory_arr = sql_select("select id,address from lib_location where company_id='".$result[0][csf("importer_id")]."' and is_deleted=0");
	$factory_add='';
	foreach($factory_arr as $factory){
		if($factory_add!='' && $factory[csf('address')] !=''){ $factory_add.= ", ".$factory[csf('address')]; }else{ $factory_add.= $factory[csf('address')]; }
	}
	$company_id=$result[0][csf("importer_id")];
	$data_array=sql_select("select image_location  from common_photo_library  where master_tble_id='$company_id' and form_name='company_details' and is_deleted=0 and file_type=1");
	// =======For Header and Footer End=========

	$itemCategory="";
	$l=1;
	$cat_id_arr=array_unique(explode(",",chop($pi_number_date_arr[$result[0][csf("id")]]['item_category_ids'],",")));
	//print_r($cat_id_arr);
	foreach($cat_id_arr as $cat_id)
	{
		if($l!=1) $itemCategory .=", ";
		$itemCategory .=$item_category[$cat_id];
		$l++;
	}
	$itemCategory; ?>

		<style type="text/css">
			.a4size {
	           width: 21cm;
	           /* height: 26.7cm; */
	           height: 23cm;
	           font-family: Bookman Old Style;
	        }
	        @media print {
	        /* .a4size{ font-family: Bookman Old Style;font-size: 18px;margin: 80px 100PX 54px 25px;
	            } */
	        /* size: A4 portrait; */
	        }
	        .parent {
			  display: flex;
			  flex-direction:row;
			  margin-left: 28px;
			  margin-top: 60px;
			  padding-top: 60px;
			}

			.column {
			  flex: 1 1 0px;
			  margin-right: 30px;
			}
			.headfooter{
				margin: 0px;
			  padding: 0px;
			}
			hr {
				display: block;
				height: 2px;
				/* background: transparent; */
				width: 100%;
				border: none;
				border-top: solid 2px #aaa;
			}
		</style>
		<div style="width:794" class="headfooter" align="center">
			<?

				foreach($data_array as $img_row)
				{
			?>
						<img src='../../<? echo $img_row[csf('image_location')]; ?>' height='70'  align="center" />
						<?
				}
				?>
		</div>
		<div class="a4size">
		    <table width="794" cellpadding="0" cellspacing="0" border="0" >
		        <div class="parent" >
		        	<? $date = $result[0][csf("application_date")]; 
    				echo date('M d, Y',strtotime($date));?>
	          		<div class="column" align="center">
	          			<?
	          			if (chop($sql_lc_arr[$result[0][csf("id")]]["sc_bank_file"],",")!="") 
	          			{
	          				echo chop($sql_lc_arr[$result[0][csf("id")]]["sc_bank_file"],",").', ';
	          			}
	          			if (chop($sql_lc_arr[$result[0][csf("id")]]["lc_bank_file"],",")!="") 
	          			{
	          				echo chop($sql_lc_arr[$result[0][csf("id")]]["lc_bank_file"],","); 
	          			}
	          			?>
	          		</div>
	          	</div>
		        <tr>
		        	<td colspan="3" height="20">&nbsp;</td>
		        </tr>
		        <tr>
		        	<td colspan="3" height="20">&nbsp;</td>
		        </tr>
		        <br>
		        <tr>
		            <td width="25"></td>
		            <td width="650" align="left">To</td>
		            <td width="25" ></td>
		        </tr>
		        <tr>
		            <td width="25" ></td>
		            <td width="650" align="left">
		            <?
						echo 'The Manager<br>MITS<br>'.ucwords($bank_dtls_arr[$result[0][csf("issuing_bank_id")]]["bank_name"]."<br>".$bank_dtls_arr[$result[0][csf("issuing_bank_id")]]["address"]);
					?>.
		            </td>
		            <td width="25" ></td>
		        </tr>
		        <tr>
		        	<td colspan="3" height="30"></td>
		        </tr>
		        <tr>
		        	<td colspan="3" height="30"></td>
		        </tr>

		        <tr>
		            <td width="25" ></td>
		            <td width="650" align="justify">
		            <strong>Sub:  Request for opening Back to Back L/C for <? echo $currency[$result[0][csf("currency_id")]]."&nbsp;".def_number_format($result[0][csf("lc_value")],2); ?> Beneficiary: <? echo ucwords($supplier_name_arr[$result[0][csf("supplier_id")]]); ?> against <? 

		            if (chop($sql_lc_arr[$result[0][csf("id")]]["sc_no"],",")!="") 
		            {
		             	echo "Sales Contract# ".chop($sql_lc_arr[$result[0][csf("id")]]["sc_no"],",");
		             	echo " Date "; $scDate=chop($sql_lc_arr[$result[0][csf("id")]]["sc_date"],",");
		             	$scDateArr=array_unique(explode(",",$scDate));
		             	$sc_all_date ="";
		             	foreach ($scDateArr as $key => $value) 
		             	{
		             		if ($sc_all_date=="") 
					        {
					            $sc_all_date.= change_date_format($value);
					        }
					        else 
					        {
					            $sc_all_date.= ', '.change_date_format($value);
					        }
		             	}
		             	echo $sc_all_date;
		            }

		            if (chop($sql_lc_arr[$result[0][csf("id")]]["lc_no"],",")!="") 
		            {
		            	echo " Master LC# ".chop($sql_lc_arr[$result[0][csf("id")]]["lc_no"],",");
		            	echo " Date "; $lcDate=chop($sql_lc_arr[$result[0][csf("id")]]["lc_date"],",");
		            	$lcDateArr=array_unique(explode(",",$lcDate));
		             	$lc_all_date ="";
		             	foreach ($lcDateArr as $key => $value) 
		             	{
		             		if ($lc_all_date=="") 
					        {
					            $lc_all_date.= change_date_format($value);
					        }
					        else 
					        {
					            $lc_all_date.= ', '.change_date_format($value);
					        }
		             	}
		             	echo $lc_all_date;
		        	}?>
		            </strong></td>
		            <td width="25" ></td>
		        </tr>
		        <tr>
		        	<td colspan="3" height="20"></td>
		        </tr>
		        <tr>
		        	<td colspan="3" height="20"></td>
		        </tr>
		        <tr>
		            <td width="25" ></td>
		            <td width="650" align="left"> Dear Sir, </td>
		            <td width="25" ></td>
		        </tr>
		        <tr>
		            <td width="25" ></td>
		            <td width="650" align="justify">
		            We would like to open aforesaid Back to Back LC to Import <? echo ucwords($itemCategory);?> against the said Export LC. The relevant papers/documents are enclosed herewith for your necessary action please.
		            </td>
		            <td width="25" ></td>
		        </tr>
		    </table>
		    <table width="794" cellpadding="0" cellspacing="0" border="0">
			    <tr>
	        		<td colspan="3" height="15"></td>
	        	</tr>

		        <tr>
		        	<td colspan="3" height="15"></td>
		        </tr>
		        <tr>
		            <td width="25" ></td>
		            <td width="650" align="left">
		            We would request you to do the needful at the earliest.
		            <br><br><br>
		            Thank you<br><br>
		            Sincerely yours,
		            </td>
		            <td width="25" ></td>
		        </tr>
		        <tr>
		        	<td colspan="3" height="70"></td>
		        </tr>
		    </table>
		    <table width="794" cellpadding="0" cellspacing="0" border="0">
		    	<tr>
		            <td style="padding-left: 28px;" width="397" align="left">--------------------------</td>
		            <td width="397" align="left">--------------------------</td>
		        </tr>
		    	<tr>
		            <td style="padding-left: 28px;" width="397" align="left">Authorized Signature</td>
		            <td width="397" align="left">Authorized Signature</td>
		        </tr>
		    </table>
		</div>
		<?

	?>

	<footer style="width:794; font-size:80%;">
	<hr>
	<div align="center">
	<p class="headfooter" >Corporate Office: <? echo $adderess; ?></p>
	<p class="headfooter">Factory: <? echo $factory_add;?></p>
	</div>
	</footer>
	<?
	exit();
}

if($action=="btb_import_lc_letter4")
{
	//echo load_html_head_contents("BTB Import Lc Letter","../../", 1, 1, $unicode,'','');
	$sql_bank_info=sql_select("select id, contact_person, bank_name, branch_name, address,designation from lib_bank ");
	foreach($sql_bank_info as $row)
	{
		$bank_dtls_arr[$row[csf("id")]]["contact_person"]=$row[csf("contact_person")];
		$bank_dtls_arr[$row[csf("id")]]["bank_name"]=$row[csf("bank_name")];
		$bank_dtls_arr[$row[csf("id")]]["branch_name"]=$row[csf("branch_name")];
		$bank_dtls_arr[$row[csf("id")]]["address"]=$row[csf("address")];
		$bank_dtls_arr[$row[csf("id")]]["designation"]=$row[csf("designation")];
	}

	$sql_lc=sql_select("SELECT id,export_lc_no,tenor FROM com_export_lc ");
	foreach($sql_lc as $row)
	{
		$export_lc_no_arr[$row[csf("id")]]["export_lc_no"]=$row[csf("export_lc_no")];
		$export_lc_no_arr[$row[csf("id")]]["tenor"]=$row[csf("tenor")];
	}
	$sql_sc=sql_select("SELECT id,contract_no,tenor FROM com_sales_contract ");
	foreach($sql_sc as $row)
	{
		$export_sc_no_arr[$row[csf("id")]]["contract_no"]=$row[csf("contract_no")];
		$export_sc_no_arr[$row[csf("id")]]["tenor"]=$row[csf("tenor")];
	}

	//echo $data."TIPU";die;
	if($db_type==2)
	{
		$sql_com="SELECT a.id,a.btb_system_id, a.application_date, a.importer_id, a.item_category_id, a.origin, a.issuing_bank_id, a.currency_id, a.supplier_id,a.lc_value,a.margin,a.lc_date,a.tenor,a.last_shipment_date,a.lc_expiry_date,a.upas_rate,a.cover_note_no,a.cover_note_date, a.lcaf_no, a.lc_type_id
		from com_btb_lc_master_details a
		where a.id=$data and a.is_deleted = 0 AND a.status_active = 1 group by a.id,a.btb_system_id, a.application_date, a.importer_id, a.item_category_id, a.origin, a.issuing_bank_id, a.currency_id, a.supplier_id, a.lc_value, a.margin,a.lc_date,a.tenor,a.last_shipment_date,a.lc_expiry_date,a.upas_rate,a.cover_note_no,a.cover_note_date, a.lcaf_no, a.lc_type_id";
	}
	elseif($db_type==0)
	{
		$sql_com="SELECT a.id,a.btb_system_id, a.application_date, a.importer_id, a.item_category_id, a.origin, a.issuing_bank_id, a.currency_id, a.supplier_id, a.lc_value, a.margin,a.lc_date,a.tenor,a.last_shipment_date,a.lc_expiry_date,a.upas_rate,a.cover_note_no,a.cover_note_date, a.lcaf_no, a.lc_type_id
		from com_btb_lc_master_details a
		where a.id=$data and a.is_deleted = 0 AND a.status_active = 1 group by a.id,a.btb_system_id, a.application_date, a.importer_id, a.item_category_id, a.supplier_id, a.origin, a.issuing_bank_id, a.currency_id, a.lc_value, a.margin,a.lc_date,a.tenor,a.last_shipment_date,a.lc_expiry_date,a.upas_rate,a.cover_note_no,a.cover_note_date, a.lcaf_no, a.lc_type_id";
	}
	//echo $sql_com;
	$result=sql_select($sql_com);

	$company_name = return_field_value("company_name","lib_company","id=".$result[0][csf("importer_id")],"company_name");
	$supplier_name = return_field_value("supplier_name","lib_supplier","id=".$result[0][csf("supplier_id")],"supplier_name");
	$country_name = return_field_value("country_name"," lib_country","id=".$result[0][csf("origin")],"country_name");
	$designation = return_field_value("custom_designation","lib_designation","id=".$bank_dtls_arr[$result[0][csf("issuing_bank_id")]]["designation"],"custom_designation");

	$com_btb_lc_id = $result[0][csf('id')];

	$sql_pi_query ="select a.import_mst_id,b.contract_no as lc_sc_no,b.internal_file_no,b.contract_value as lc_sc_val,b.contract_date as contract_date from com_btb_export_lc_attachment a, com_sales_contract b where  a.lc_sc_id = b.id and a.status_active=1 and a.is_lc_sc=1 and a.import_mst_id=$data
	union all select a.import_mst_id,b.export_lc_no as lc_sc_no,b.internal_file_no, b.lc_value as lc_sc_val, b.lc_date as contract_date from com_btb_export_lc_attachment a, com_export_lc b
			where a.lc_sc_id=b.id and a.status_active=1 and a.is_lc_sc=0 and a.import_mst_id=$data";
	//echo $sql_pi_query;
	$sql_pi=sql_select($sql_pi_query);

		$sql_lc__arr=array();
		foreach($sql_pi as $row)
		{
			$sql_lc__arr[$row[csf("import_mst_id")]]["lc_sc_no"]=$row[csf("lc_sc_no")].",";
			$sql_lc__arr[$row[csf("import_mst_id")]]["internal_file_no"]=$row[csf("internal_file_no")].",";
			$sql_lc__arr[$row[csf("import_mst_id")]]["lc_sc_val"]=$row[csf("lc_sc_val")].",";
			$sql_lc__arr[$row[csf("import_mst_id")]]["lc_date"]=$row[csf("lc_date")].",";
			$sql_lc__arr[$row[csf("import_mst_id")]]["contract_date"]=$row[csf("contract_date")].",";
		}
		/*echo '<pre>';
		print_r($sql_lc__arr);*/

	$sql_pi=sql_select("SELECT a.pi_number, a.pi_date,a.hs_code,b.com_btb_lc_master_details_id, rtrim(xmlagg(xmlelement(e,c.item_category_id,',').extract('//text()') order by c.item_category_id).GetClobVal(),',') AS item_category_ids
		from com_pi_master_details a ,com_btb_lc_pi b,com_pi_item_details c
		where a.id=b.pi_id and com_btb_lc_master_details_id=$data and a.id=c.pi_id and c.status_active=1 and a.status_active=1 and b.status_active=1 group by a.pi_number, a.pi_date,a.hs_code,b.com_btb_lc_master_details_id");
	if($result[0][csf("tenor")]>=180)
	{
		$upas_string="UPAS L/C for";
		$upas_string2=$result[0][csf("tenor")] ." days from thr date of negotiation (UPAS LC at sight)";
	}
	else
	{
		$upas_string="L/C for";

		$upas_string2="L/C tenor will be At Sight/Deferred";
	}

	$pi_number_date_arr=array();
	foreach($sql_pi as $row)
	{
		if($db_type==2) $row[csf('item_category_ids')] = $row[csf('item_category_ids')]->load();
		$pi_number_date_arr[$row[csf("com_btb_lc_master_details_id")]]["pi_number_date"].=$row[csf("pi_number")]." dated.".change_date_format($row[csf("pi_date")]).",";

		$total_pi_arr[$row[csf("com_btb_lc_master_details_id")]]["total_pi"].=$row[csf("pi_number")].",";

		$pi_number_date_arr[$row[csf("com_btb_lc_master_details_id")]]["hs_code"].=$row[csf("hs_code")].",";
		$pi_number_date_arr[$row[csf("com_btb_lc_master_details_id")]]["item_category_ids"].=$row[csf("item_category_ids")].",";
	}

	$pi_num_exp=explode(",",chop($total_pi_arr[$result[0][csf("id")]]['total_pi'],","));
	$pi_num_count = count($pi_num_exp);
	/*echo '<pre>';
	print_r($test2);*/

	$itemCategory="";
	$l=1;
	$cat_id_arr=array_unique(explode(",",chop($pi_number_date_arr[$result[0][csf("id")]]['item_category_ids'],",")));
	//print_r($cat_id_arr);
	foreach($cat_id_arr as $cat_id)
	{
		if($l!=1) $itemCategory .=", ";
		$itemCategory .=$item_category[$cat_id];
		$l++;
	}
	$itemCategory; ?><? chop ($pi_number_date_arr[$result[0][csf("id")]]["pi_number_date"],",");

	?>

		<style type="text/css">
		body{
			margin:0;
			padding: 0;
		}
			.a4size {
	           width: 21cm;
	           height: 15.7cm;
			   font-family: Cambria, Georgia, serif;
			   padding-top: 3cm;
			   padding-left: 2cm;
	        }
	        @media print {
	        .a4size{ font-family: Cambria;font-size: 18px;margin: 80px 100PX 54px 25px;size: A4 portrait;
	            }
	        }
		</style>
		<div class="a4size">
		    <table width="794" cellpadding="0" cellspacing="0" border="0" >
		    	<tr>
		        	<td colspan="3" height="20">&nbsp;</td>
		        </tr>
		        <tr>
		        	<td colspan="3" height="20">&nbsp;</td>
		        </tr>
		        <tr>
		            <td width="25" ></td>
		            <td width="650" align="left">Date : <? $date = $result[0][csf("application_date")]; echo date('F d,Y',strtotime($date));?> </td>
		            <td width="25" ></td>
		        </tr>
		        <tr>
		        	<td colspan="3" height="20">&nbsp;</td>
		        </tr>
		        <tr>
		        	<td colspan="3" height="20">&nbsp;</td>
		        </tr>
		        <br>
		        <tr>
		            <td width="25"></td>
		            <td width="650" align="left">To</td>
		            <td width="25" ></td>
		        </tr>
		        <tr>
		            <td width="25" ></td>
		            <td width="650" align="left">
		            <?
						echo 'The '.ucfirst($designation)."<br>".ucwords($bank_dtls_arr[$result[0][csf("issuing_bank_id")]]["bank_name"])."<br>".ucwords($bank_dtls_arr[$result[0][csf("issuing_bank_id")]]["address"]);
					?>

		            </td>
		            <td width="25" ></td>
		        </tr>
		        <tr>
		        	<td colspan="3" height="30"></td>
		        </tr>
		        <tr>
		        	<td colspan="3" height="30"></td>
		        </tr>

		        <tr>
		            <td width="25" ></td>
		            <td width="650" align="left" >
		            <strong>Sub: <u>Request for Opening of <?echo $lc_type[$result[0][csf('lc_type_id')]];?> for <? echo $currency[$result[0][csf("currency_id")]]."&nbsp;".def_number_format($result[0][csf("lc_value")],2); ?></u></strong>
		            </td>
		            <td width="25" ></td>
		        </tr>
		        <tr>
		        	<td colspan="3" height="20"></td>
		        </tr>
		        <tr>
		        	<td colspan="3" height="20"></td>
		        </tr>
		        <tr>
		            <td width="25" ></td>
		            <td width="650" align="left"> Dear Sir, </td>
		            <td width="25" ></td>
		        </tr>
		        <tr>
		            <td width="25" ></td>
		            <td width="650" align="left">
		            We would like to request you to open <?echo $lc_type[$result[0][csf('lc_type_id')]];?> for <? echo $currency[$result[0][csf("currency_id")]]."&nbsp;".def_number_format($result[0][csf("lc_value")],2); ?> in favor of <? echo strtoupper($supplier_name);?> against Export LC/Sales ContractNo.
					<?
					$lc_sc_number = chop($sql_lc__arr[$result[0][csf("id")]]["lc_sc_no"],",");
					if($lc_sc_number!=""){
					echo $lc_sc_number;?>
					dated <? echo date('F d,Y',strtotime($sql_lc__arr[$result[0][csf("id")]]["contract_date"]));
					}
					?>
		            </td>
		            <td width="25" ></td>
		        </tr>
		    </table>
		    <table width="794" cellpadding="0" cellspacing="0" border="0">
				    <tr>
		        		<td colspan="3" height="15"></td>
		        	</tr>

		        <tr>
		        	<td colspan="3" height="15"></td>
		        </tr>
		        <tr>
		            <td width="25" ></td>
		            <td width="650" align="left">
		            We would highly appreciate your prompt action on the matter.<br>
		            </td>
		            <td width="25" ></td>
		        </tr>
		        <tr>
		        	<td colspan="3" height="15"></td>
		        </tr>
		        <tr>
		        	<td colspan="3" height="15"></td>
		        </tr>
		        <tr>
		            <td width="25" ></td>
		            <td width="650" align="left">
		            Yours truly,
		            </td>
		            <td width="25" ></td>
		        </tr>
		        <tr>
		        	<td colspan="3" height="70"></td>
		        </tr>
		        <tr>
		            <td width="25" ></td>
		            <td width="650" align="left">
		            ----------------------------
		            </td>
		            <td width="25" ></td>
		        </tr>
		        <tr>
		            <td width="25" ></td>
		            <td width="650" align="left">
		            Authorized Signature
		            </td>
		            <td width="25" ></td>
		        </tr>
		    </table>
		</div>
    <?
	exit();
}

if ($action==='btb_import_lc_letter5')
{
	$buyer_arr =return_library_array('SELECT id, buyer_name FROM lib_buyer','id','buyer_name');
	$supplier_arr = return_library_array('SELECT id, supplier_name FROM lib_supplier','id','supplier_name');
	$company_arr = return_library_array('SELECT id, company_name FROM lib_company','id','company_name');

	$sql_bank_info = sql_select("SELECT ID, CONTACT_PERSON, BANK_NAME, BRANCH_NAME, ADDRESS from lib_bank ");
	$bank_dtls_arr=array();
	foreach($sql_bank_info as $row)
	{
		$bank_dtls_arr[$row['ID']]['BANK_NAME']=$row['BANK_NAME'];
		$bank_dtls_arr[$row['ID']]['BRANCH_NAME']=$row['BRANCH_NAME'];
		$bank_dtls_arr[$row['ID']]['ADDRESS']=$row['ADDRESS'];
	}
	$exportPiSupp = sql_select("select c.import_pi, a.id from com_btb_lc_master_details a , com_btb_lc_pi b , com_pi_master_details c where a.id = b.com_btb_lc_master_details_id and b.pi_id = c.id");
	foreach ($exportPiSupp as $value)
	{
		$exportPiSuppArr[$value[csf("id")]] = $value[csf("import_pi")];
	}
	
	// BTB Part
	$sql_btb="SELECT ID, BTB_SYSTEM_ID, LC_DATE, APPLICATION_DATE, IMPORTER_ID, ITEM_CATEGORY_ID, ISSUING_BANK_ID, CURRENCY_ID, SUPPLIER_ID, LC_VALUE, MATURITY_FROM_ID, LAST_SHIPMENT_DATE, LC_EXPIRY_DATE, TENOR, PI_ID
	from com_btb_lc_master_details
	where id=$data and is_deleted=0 and status_active=1";
	$sql_btb_res=sql_select($sql_btb);
	$btb_id = $sql_btb_res[0]['ID'];
	$importer_name = $company_arr[$sql_btb_res[0]['IMPORTER_ID']];
	if($exportPiSuppArr[$sql_btb_res[0]['ID']] == 1)
	{
		$supplier_name = $company_arr[$sql_btb_res[0]['SUPPLIER_ID']];
	}else{
		$supplier_name = $supplier_arr[$sql_btb_res[0]['SUPPLIER_ID']];
	}
	if ($sql_btb_res[0]['MATURITY_FROM_ID']==1)
		$maturityFrom = "(".$sql_btb_res[0]['TENOR']." days from the date of Acceptance)";
	else if ($sql_btb_res[0]['MATURITY_FROM_ID']==3)
		$maturityFrom = "(UNDER EDF)";
	else if ($sql_btb_res[0]['MATURITY_FROM_ID']==4)
		$maturityFrom = "(".$sql_btb_res[0]['TENOR']." days from the date of BL)";
	else if ($sql_btb_res[0]['MATURITY_FROM_ID']==5)
		$maturityFrom = "(".$sql_btb_res[0]['TENOR']." days from the date of Delivery)";
	else $maturityFrom='';

	// PI Part
	$pi_ids=$sql_btb_res[0]['PI_ID'];
	$sql_pi_res=sql_select("SELECT PI_NUMBER, PI_DATE, ITEM_CATEGORY_ID from com_pi_master_details where id in($pi_ids) and status_active=1 and is_deleted=0");

	$category_name='';
	$pi_data_arr=array();
	foreach($sql_pi_res as $row)
	{
		$pi_number.=$row['PI_NUMBER'].', ';
		$pi_date.=change_date_format($row['PI_DATE']).', ';		
		if ($row['ITEM_CATEGORY_ID']==2 || $row['ITEM_CATEGORY_ID']==3) $category_name.='Fabric, ';
		else if ($row['ITEM_CATEGORY_ID']==12 || $row['ITEM_CATEGORY_ID']==24) $category_name.='Dyeing, ';
		else if ($row['ITEM_CATEGORY_ID']==25) $category_name.='Accessories, ';
		else $category_name.=$item_category[$row['ITEM_CATEGORY_ID']].', ';
	}

	// LC SC Part
	$sql_lcSc=sql_select("SELECT a.IMPORT_MST_ID, b.contract_no as LC_SC_NO, b.contract_date as LC_SC_DATE, b.BUYER_NAME, a.IS_LC_SC
	from  com_btb_export_lc_attachment a, com_sales_contract b
	where a.lc_sc_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.is_lc_sc=1 and a.import_mst_id=$data
	union all 
	select a.IMPORT_MST_ID, b.export_lc_no as LC_SC_NO, b.lc_date as LC_SC_DATE, b.BUYER_NAME, a.IS_LC_SC 
	from com_btb_export_lc_attachment a, com_export_lc b
	where a.lc_sc_id=b.id and a.status_active=1 and a.is_deleted=0 and a.is_lc_sc=0 and b.status_active=1 and b.is_deleted=0 and a.import_mst_id=$data");	
	$lc_sc_arr=array();
	foreach($sql_lcSc as $row)
	{
		$lc_sc_arr[$row['IMPORT_MST_ID']]['LC_SC_NO'].=$row['LC_SC_NO'].', ';
		$lc_sc_arr[$row['IMPORT_MST_ID']]['LC_SC_DATE'].=change_date_format($row['LC_SC_DATE']).', ';
		$lc_sc_arr[$row['IMPORT_MST_ID']]['BUYER_NAME'].= $buyer_arr[$row['BUYER_NAME']].', ';
	}
	//echo '<pre>';print_r($lc_sc_arr);

	$lc_sc_no   = rtrim($lc_sc_arr[$btb_id]['LC_SC_NO'],', ');
	$lc_sc_date = rtrim($lc_sc_arr[$btb_id]['LC_SC_DATE'],', ');
	$lc_sc_buyer = rtrim($lc_sc_arr[$btb_id]['BUYER_NAME'],', ');
	?>
	<table width="700" cellpadding="0" align="left" cellspacing="0" border="0">
		<tr><td colspan="5" height="100"></td></tr>
		<tr>
			<td width="25"></td>
			<td width="650" colspan="3">Ref No : <?= $sql_btb_res[0]['BTB_SYSTEM_ID']; ?></td>
			<td width="25"></td>
		</tr>
		<tr>
			<td width="25"></td>
			<td width="650" colspan="3">Dated&nbsp;&nbsp;&nbsp;: <?= date("F d, Y", strtotime($sql_btb_res[0]['LC_DATE'])); ?></td>
			<td width="25"></td>
		</tr>
		<tr>
			<td width="25"></td>
			<td width="650" colspan="3">The Manager</td>
			<td width="25"></td>
		</tr>
		<tr>
			<td width="25"></td>
			<td width="650" colspan="3"><?= $bank_dtls_arr[$sql_btb_res[0]['ISSUING_BANK_ID']]['BANK_NAME']; ?></td>
			<td width="25"></td>
		</tr>
		<tr>
			<td width="25"></td>
			<td width="650" colspan="3"><?= $bank_dtls_arr[$sql_btb_res[0]['ISSUING_BANK_ID']]['BRANCH_NAME']; ?></td>
			<td width="25"></td>
		</tr>
		<tr>
			<td width="25"></td>
			<td width="650" colspan="3"><?= $bank_dtls_arr[$sql_btb_res[0]['ISSUING_BANK_ID']]['ADDRESS']; ?></td>
			<td width="25"></td>
		</tr>
		<tr><td colspan="5" height="15"></td></tr>
		<tr>
			<td width="25"></td>
			<td width="650" colspan="3"><strong>Sub:&nbsp;Issuance of Back-to-Back (BTB) L/C for <?= $currency[$sql_btb_res[0]['CURRENCY_ID']]; ?>&nbsp;<?= number_format($sql_btb_res[0]['LC_VALUE'],2); ?> against Export S/C No. </strong><?= $lc_sc_no; ?> Dated: <?= $lc_sc_date; ?>&nbsp;<strong><?= $maturityFrom; ?></strong></td>
			<td width="25"></td>
		</tr>
		<tr><td colspan="5" height="15"></td></tr>
		<tr>
			<td width="25"></td>
			<td width="650" colspan="3">Dear Sir,</td>
			<td width="25"></td>
		</tr>
		<tr><td colspan="5" height="10"></td></tr>
		<tr>
			<td width="25"></td>
			<td width="650" colspan="3">We would request you to please issue Back-to-Back (BTB) L/C as detailed below:-</td>
			<td width="25"></td>
		</tr>
		<tr><td colspan="5" height="15"></td></tr>
		<tr>
			<td width="25"></td>
			<td width="260">Shipment date</td>
			<td width="20"><strong>:</strong></td>
			<td width="370"><?= change_date_format($sql_btb_res[0]['LAST_SHIPMENT_DATE']); ?></td>
			<td width="25"></td>
		</tr>
		<tr>
			<td width="25"></td>
			<td width="260">Expiry date</td>
			<td width="20"><strong>:</strong></td>
			<td width="370"><?= change_date_format($sql_btb_res[0]['LC_EXPIRY_DATE']); ?></td>
			<td width="25"></td>
		</tr>
		<tr>
			<td width="25"></td>
			<td width="260">Buyer</td>
			<td width="20"><strong>:</strong></td>
			<td width="370"><?= $lc_sc_buyer; ?></td>
			<td width="25"></td>
		</tr>
		<tr>
			<td width="25"></td>
			<td width="260">Beneficiary</td>
			<td width="20"><strong>:</strong></td>
			<td width="370">Ourselves</td>
			<td width="25"></td>
		</tr>
		<tr>
			<td width="25"></td>
			<td width="260">Validity of the L/C</td>
			<td width="20"><strong>:</strong></td>
			<td width="370"></td>			
			<td width="25"></td>
		</tr>
		<tr>
			<td width="25"></td>
			<td colspan="4"><hr></td>
		</tr>
		<tr>
			<td width="25"></td>
			<td width="260">Maximum Extent of BTB L/C will be</td>
			<td width="20"><strong>:</strong></td>
			<td width="370">75%</td>
			<td width="25"></td>
		</tr>				
		<tr>
			<td width="25"></td>
			<td width="650" colspan="3">issued</td>
			<td width="25"></td>
		</tr>
		<tr>
			<td width="25"></td>
			<td width="260">Present value of BTB L/C</td>
			<td width="20"><strong>:</strong></td>
			<td width="370"><span><?= $currency[$sql_btb_res[0]['CURRENCY_ID']]; ?></span>&nbsp;<span style="font-weight: bold;"><?= number_format($sql_btb_res[0]['LC_VALUE'],2); ?></span></td>			
			<td width="25"></td>
		</tr>
		<tr>
			<td width="25"></td>
			<td width="260">Applicant of BTB L/C</td>
			<td width="20"><strong>:</strong></td>
			<td width="370">Ourselves</td>			
			<td width="25"></td>
		</tr>
		<tr>
			<td width="25"></td>
			<td width="260">Beneficiary</td>
			<td width="20"><strong>:</strong></td>
			<td width="370"><?= $supplier_name; ?></td>			
			<td width="25"></td>
		</tr>
		<tr>
			<td width="25"></td>
			<td width="260">Item</td>
			<td width="20"><strong>:</strong></td>
			<td width="370"><?= implode(', ',array_unique(explode(', ',rtrim($category_name,', ')))); ?></td>
			<td width="25"></td>
		</tr>
		<tr><td colspan="5" height="15"></td></tr>
		<tr>
			<td width="25"></td>
			<td width="650" colspan="3">The following papers are enclosed for issuance of BTB L/C by order of ourselves:-</td>
			<td width="25"></td>
		</tr>
		<tr>
			<td width="25"></td>
			<td width="650" colspan="3">L/C Application</td>
			<td width="25"></td>
		</tr>
		<tr>
			<td width="25"></td>
			<td width="650" colspan="3">Original L/C as mentioned above.</td>
			<td width="25"></td>
		</tr>
		<tr>
			<td width="25"></td>
			<td width="650" colspan="3">Pro-forma Invoice No. <?= rtrim($pi_number,', '); ?>&nbsp;dated:<?= implode(', ',array_unique(explode(', ',rtrim($pi_date,', ')))); ?>&nbsp;issued by beneficiary.</td>
			<td width="25"></td>
		</tr>
		<tr><td colspan="5" height="15"></td></tr>
		<tr>
			<td width="25"></td>
			<td width="650" colspan="3">Please debit our CD Account for the purpose of realization of your charges under intimation to us.</td>
			<td width="25"></td>
		</tr>
		<tr><td colspan="5" height="15"></td></tr>

		<tr>
			<td width="25"></td>
			<td width="650" colspan="3">Please also provide us with a copy of L/C immediately after issuance of the same.</td>
			<td width="25"></td>
		</tr>
		<tr><td colspan="5" height="15"></td></tr>
		<tr>
			<td width="25"></td>
			<td width="650" colspan="3">Thanks & Regards, </td>
			<td width="25"></td>
		</tr>
		<tr>
			<td width="25"></td>
			<td width="650" colspan="3">Very truly yours</td>
			<td width="25"></td>
		</tr>
		<tr><td colspan="5" height="100"></td></tr>
		<tr>
			<td width="25"></td>
			<td width="650" colspan="3">AUTHORIZED SIGNATURE</td>
			<td width="25"></td>
		</tr>
	</table>
	<?
	exit();
}

if ($action==='btb_import_lc_tt')
{
	/*$buyer_arr =return_library_array('SELECT id, buyer_name FROM lib_buyer','id','buyer_name');
	$supplier_arr = return_library_array('SELECT id, supplier_name FROM lib_supplier','id','supplier_name');
	$company_arr = return_library_array('SELECT id, company_name FROM lib_company','id','company_name');

	$sql_bank_info = sql_select("SELECT ID, CONTACT_PERSON, BANK_NAME, BRANCH_NAME, ADDRESS from lib_bank ");
	$bank_dtls_arr=array();
	foreach($sql_bank_info as $row)
	{
		$bank_dtls_arr[$row['ID']]['BANK_NAME']=$row['BANK_NAME'];
		$bank_dtls_arr[$row['ID']]['BRANCH_NAME']=$row['BRANCH_NAME'];
		$bank_dtls_arr[$row['ID']]['ADDRESS']=$row['ADDRESS'];
	}

	// BTB Part
	
	$btb_id = $sql_btb_res[0]['ID'];
	$importer_name = $company_arr[$sql_btb_res[0]['IMPORTER_ID']];
	$supplier_name = $supplier_arr[$sql_btb_res[0]['SUPPLIER_ID']];

	if ($sql_btb_res[0]['MATURITY_FROM_ID']==1)
		$maturityFrom = "(".$sql_btb_res[0]['TENOR']." days from the date of Acceptance)";
	else if ($sql_btb_res[0]['MATURITY_FROM_ID']==3)
		$maturityFrom = "(UNDER EDF)";
	else if ($sql_btb_res[0]['MATURITY_FROM_ID']==4)
		$maturityFrom = "(".$sql_btb_res[0]['TENOR']." days from the date of BL)";
	else if ($sql_btb_res[0]['MATURITY_FROM_ID']==5)
		$maturityFrom = "(".$sql_btb_res[0]['TENOR']." days from the date of Delivery)";
	else $maturityFrom='';

	// PI Part
	$pi_ids=$sql_btb_res[0]['PI_ID'];
	$sql_pi_res=sql_select("SELECT PI_NUMBER, PI_DATE, ITEM_CATEGORY_ID from com_pi_master_details where id in($pi_ids) and status_active=1 and is_deleted=0");

	$category_name='';
	$pi_data_arr=array();
	foreach($sql_pi_res as $row)
	{
		$pi_number.=$row['PI_NUMBER'].', ';
		$pi_date.=change_date_format($row['PI_DATE']).', ';		
		if ($row['ITEM_CATEGORY_ID']==2 || $row['ITEM_CATEGORY_ID']==3) $category_name.='Fabric, ';
		else if ($row['ITEM_CATEGORY_ID']==12 || $row['ITEM_CATEGORY_ID']==24) $category_name.='Dyeing, ';
		else if ($row['ITEM_CATEGORY_ID']==25) $category_name.='Accessories, ';
		else $category_name.=$item_category[$row['ITEM_CATEGORY_ID']].', ';
	}

	// LC SC Part
	
	//echo '<pre>';print_r($lc_sc_arr);

	*/
	$company_arr = return_library_array('SELECT id, company_name FROM lib_company','id','company_name');
	$country_arr = return_library_array('SELECT id, country_name FROM lib_country','id','country_name');
	$back_sql=sql_select("select a.ID, a.SWIFT_CODE, b.ACCOUNT_TYPE, b.ACCOUNT_NO from LIB_BANK a, LIB_BANK_ACCOUNT b where a.id=b.account_id");
	$bank_data=array();
	foreach($back_sql as $row)
	{
		$bank_data[$row["ID"]]["SWIFT_CODE"]=$row["SWIFT_CODE"];
		if($row["ACCOUNT_TYPE"]==6) $bank_data[$row["ID"]]["ACCOUNT_NO"]=$row["ACCOUNT_NO"];
	}
	$supplier_sql = sql_select('SELECT id, supplier_name, address_1 FROM lib_supplier');
	foreach($supplier_sql as $row)
	{
		$supplier_arr[$row[csf("id")]]["supplier_name"]=$row[csf("supplier_name")];
		$supplier_arr[$row[csf("id")]]["address_1"]=$row[csf("address_1")];
	}
	
	$sql_btb="SELECT ID, BTB_SYSTEM_ID, LC_DATE, APPLICATION_DATE, IMPORTER_ID, ITEM_CATEGORY_ID, ISSUING_BANK_ID, CURRENCY_ID, SUPPLIER_ID, LC_VALUE, MATURITY_FROM_ID, LAST_SHIPMENT_DATE, LC_EXPIRY_DATE, TENOR, PI_ID, INCO_TERM_ID, INCO_TERM_PLACE, ORIGIN, ISSUING_BANK_ID, COVER_NOTE_NO, COVER_NOTE_DATE
	from com_btb_lc_master_details
	where id=$data and is_deleted=0 and status_active=1";
	$sql_btb_res=sql_select($sql_btb);
	$btb_id = $sql_btb_res[0]['ID'];
	$lc_value = $sql_btb_res[0]['LC_VALUE'];
	$last_ship_date = $sql_btb_res[0]['LAST_SHIPMENT_DATE'];
	$expire_date = $sql_btb_res[0]['LC_EXPIRY_DATE'];
	$inco_term_place = $incoterm[$sql_btb_res[0]['INCO_TERM_ID']]." ".$sql_btb_res[0]['INCO_TERM_PLACE'];
	$importer_name = $company_arr[$sql_btb_res[0]['IMPORTER_ID']];
	$supplier_name = $supplier_arr[$sql_btb_res[0]['SUPPLIER_ID']]["supplier_name"];
	$supplier_address = $supplier_arr[$sql_btb_res[0]['SUPPLIER_ID']]["address_1"];
	$origin_name = $country_arr[$sql_btb_res[0]['ORIGIN']];
	$cover_note_no = $sql_btb_res[0]['COVER_NOTE_NO'];
	$cover_note_date = $sql_btb_res[0]['COVER_NOTE_DATE'];
	$issuing_bank_id = $sql_btb_res[0]['ISSUING_BANK_ID'];
	$swift_code=$bank_data[$issuing_bank_id]["SWIFT_CODE"];
	$erq_no=$bank_data[$issuing_bank_id]["ACCOUNT_NO"];
	
	$pi_ids=$sql_btb_res[0]['PI_ID'];
	$sql_pi_res=sql_select("SELECT a.PI_NUMBER, a.PI_DATE, a.ITEM_CATEGORY_ID, a.HS_CODE, a.PI_VALIDITY_DATE, listagg(cast(b.HS_CODE as varchar(4000)),',') within group(order by b.id) as ITEM_HS_CODE, sum(b.QUANTITY) as QUANTITY 
	from com_pi_master_details a, com_pi_item_details b 
	where a.id=b.pi_id and a.status_active=1 and b.status_active=1 and a.id in($pi_ids)
	group by a.PI_NUMBER, a.PI_DATE, a.ITEM_CATEGORY_ID, a.HS_CODE, a.PI_VALIDITY_DATE");

	$category_name='';
	$pi_data_arr=array();
	foreach($sql_pi_res as $row)
	{
		$pi_number.=$row['PI_NUMBER'].', ';
		$pi_date.=change_date_format($row['PI_DATE']).', ';	
		$hs_code.=$row['HS_CODE'].', ';	
		if($row['ITEM_HS_CODE']!="") $item_hs_code.=$row['ITEM_HS_CODE'].', ';
		$pi_qnty+=$row['QUANTITY'];
		if($category_check[$row['ITEM_CATEGORY_ID']]=="")
		{
			$category_check[$row['ITEM_CATEGORY_ID']]=$row['ITEM_CATEGORY_ID'];
			if ($row['ITEM_CATEGORY_ID']==2 || $row['ITEM_CATEGORY_ID']==3) $category_name.='Fabric, ';
			else if ($row['ITEM_CATEGORY_ID']==12 || $row['ITEM_CATEGORY_ID']==24) $category_name.='Dyeing, ';
			else if ($row['ITEM_CATEGORY_ID']==25) $category_name.='Accessories, ';
			else $category_name.=$item_category[$row['ITEM_CATEGORY_ID']].', ';
		}
	}
	$category_name=chop($category_name,", ");
	
	$sql_lcSc=sql_select("SELECT a.IMPORT_MST_ID, b.contract_no as LC_SC_NO, b.contract_date as LC_SC_DATE, b.BUYER_NAME, a.IS_LC_SC
	from  com_btb_export_lc_attachment a, com_sales_contract b
	where a.lc_sc_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.is_lc_sc=1 and a.import_mst_id=$data
	union all 
	select a.IMPORT_MST_ID, b.export_lc_no as LC_SC_NO, b.lc_date as LC_SC_DATE, b.BUYER_NAME, a.IS_LC_SC 
	from com_btb_export_lc_attachment a, com_export_lc b
	where a.lc_sc_id=b.id and a.status_active=1 and a.is_deleted=0 and a.is_lc_sc=0 and b.status_active=1 and b.is_deleted=0 and a.import_mst_id=$data");	
	$lc_sc_arr=array();
	foreach($sql_lcSc as $row)
	{
		$lc_sc_arr[$row['IMPORT_MST_ID']]['LC_SC_NO'].=$row['LC_SC_NO'].', ';
		$lc_sc_arr[$row['IMPORT_MST_ID']]['LC_SC_DATE'].=change_date_format($row['LC_SC_DATE']).', ';
		$lc_sc_arr[$row['IMPORT_MST_ID']]['BUYER_NAME'].= $buyer_arr[$row['BUYER_NAME']].', ';
	}
	$lc_sc_no   = rtrim($lc_sc_arr[$btb_id]['LC_SC_NO'],', ');
	$lc_sc_date = rtrim($lc_sc_arr[$btb_id]['LC_SC_DATE'],', ');
	$lc_sc_buyer = rtrim($lc_sc_arr[$btb_id]['BUYER_NAME'],', ');
	?>
    <table width="700" cellpadding="0" align="left" cellspacing="0" border="0">
		<tr><td colspan="5" height="50"></td></tr>
		<tr>
			<td width="50">Date: </td>
			<td width="650" colspan="3"><? echo date('d-m-Y');?></td>
		</tr>
		<tr>
			<td width="50" valign="top">To</td>
			<td width="650" colspan="3">The Manager<br>BRAC Bank Limited<br>Gulshan Branch<br>Dhaka-1212</td>
		</tr>
        <tr><td colspan="4">&nbsp;</td></tr>
		<tr>
			<td width="50">Sub:</td>
			<td width="650" colspan="3">Request to remit under TT of USD <? echo number_format($lc_value,2);?> against our Purchase contact no. <? echo $lc_sc_no; ?> Date: <? echo $lc_sc_date; ?></td>
		</tr>
        <tr><td colspan="4">&nbsp;</td></tr>
		<tr>
			<td width="700" colspan="4">Dear Sir,</td>
		</tr>
		<tr>
			<td width="50"></td>
			<td width="650" colspan="3">Reference to above mentioned Purchase Contact; you are requested to remit USD <? echo number_format($lc_value,2);?> against our following supplier. Details information is furnished below for your kind and neceaasry action.</td>
		</tr>
        <tr><td colspan="4">&nbsp;</td></tr>
		<tr>
			<td width="50"></td>
			<td width="150" style="border: 1px solid black;">Supplier</td>
			<td width="500" colspan="2" align="center" style="border: 1px solid black;"><? echo $supplier_name; ?></td>
		</tr>
        <tr>
			<td width="50"></td>
			<td width="150" style="border: 1px solid black;">Supplier's Bank Adddress</td>
			<td width="500" colspan="2" align="center" style="border: 1px solid black;"><? //echo $supplier_address; ?></td>
		</tr>
        <tr>
			<td width="50"></td>
			<td width="150" style="border: 1px solid black;">Supplier's Bank Account</td>
			<td width="500" colspan="2" style="border: 1px solid black;"></td>
		</tr>
        <tr>
			<td width="50"></td>
			<td width="150" style="border: 1px solid black;">Swift Code</td>
			<td width="500" colspan="2" align="center" style="border: 1px solid black;"><? echo $swift_code; ?></td>
		</tr>
        <tr>
			<td width="50"></td>
			<td width="150" style="border: 1px solid black;">LCAF Value</td>
			<td width="500" colspan="2" align="center" style="border: 1px solid black;"><? echo number_format($lc_value,2);?></td>
		</tr>
        <tr>
			<td width="50"></td>
			<td width="150" style="border: 1px solid black;">Last shipment Date.</td>
			<td width="500" colspan="2" align="center" style="border: 1px solid black;"><? if($last_ship_date!="") echo change_date_format($last_ship_date);?></td>
		</tr>
        <tr>
			<td width="50"></td>
			<td width="150" style="border: 1px solid black;">PI Expiry Date.	</td>
			<td width="500" colspan="2" align="center" style="border: 1px solid black;"><? if($expire_date!="") echo change_date_format($expire_date);?></td>
		</tr>
        <tr>
			<td width="50"></td>
			<td width="150" style="border: 1px solid black;">Freight Term</td>
			<td width="500" colspan="2" align="center" style="border: 1px solid black;"><? echo $inco_term_place; ?></td>
		</tr>
        <tr>
			<td width="50"></td>
			<td width="150" style="border: 1px solid black;">HS Code	</td>
			<td width="500" colspan="2"  align="center" style="border: 1px solid black;"><? echo $item_hs_code; ?></td>
		</tr>
        <tr>
			<td width="50"></td>
			<td width="150" style="border: 1px solid black;">Quantity</td>
			<td width="500" colspan="2"  align="center" style="border: 1px solid black;"><? echo number_format($pi_qnty,2); ?></td>
		</tr>
        <tr>
			<td width="50"></td>
			<td width="150" style="border: 1px solid black;">Commodity Description</td>
			<td width="500" colspan="2"  align="center" style="border: 1px solid black;"><? echo $category_name; ?></td>
		</tr>
        <tr>
			<td width="50"></td>
			<td width="150" style="border: 1px solid black;">Country of origin</td>
			<td width="500" colspan="2"  align="center" style="border: 1px solid black;"><? echo $origin_name; ?></td>
		</tr>
        <tr>
			<td width="50"></td>
			<td width="150" style="border: 1px solid black;">ERQ A/C no.</td>
			<td width="500" colspan="2"  align="center" style="border: 1px solid black;"><? echo $erq_no; ?></td>
		</tr>
        <tr>
			<td width="50"></td>
			<td width="150" style="border: 1px solid black;">Insurance Cover no.</td>
			<td width="300" style="border: 1px solid black;"><? echo $cover_note_no; ?></td>
            <td style="border: 1px solid black;">Date : <? if($cover_note_date!="") echo change_date_format($cover_note_date);?></td>
		</tr>
        <tr><td colspan="4">&nbsp;</td></tr>
        <tr>
			<td width="50"></td>
			<td width="650" colspan="3">We also undertake that, in case of no shipment or partial shipment is made within the expiry of PI / Contact, full amount or remaining amount of FTT (whicher is applicable) will be returned bank by the supplier to your end. </td>
		</tr>
        <tr><td colspan="4">&nbsp;</td></tr>
        <tr><td colspan="4">&nbsp;</td></tr>
        <tr>
			<td width="50"></td>
			<td width="650" colspan="3">Thanking You</td>
		</tr>
        <tr>
			<td width="50"></td>
			<td width="650" colspan="3">Regards,</td>
		</tr>
        <tr><td colspan="4">&nbsp;</td></tr>
        <tr><td colspan="4">&nbsp;</td></tr>
        <tr>
			<td width="50"></td>
			<td width="650" colspan="3">Authorized Signature.</td>
		</tr>
	</table>
	<?
	exit();
}

if ($action==='btb_import_lc_tt_fdd')
{
	$company_arr = return_library_array('SELECT id, company_name FROM lib_company','id','company_name');
	$company_arr_short=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$back_sql=sql_select("select a.ID, a.SWIFT_CODE, b.ACCOUNT_TYPE, b.ACCOUNT_NO from LIB_BANK a, LIB_BANK_ACCOUNT b where a.id=b.account_id");

	$bank_data=array();
	foreach($back_sql as $row)
	{
		$bank_data[$row["ID"]]["SWIFT_CODE"]=$row["SWIFT_CODE"];
		if($row["ACCOUNT_TYPE"]==6) $bank_data[$row["ID"]]["ACCOUNT_NO"]=$row["ACCOUNT_NO"];
	}
	$supplier_sql = sql_select('SELECT id, supplier_name, address_1 FROM lib_supplier');
	foreach($supplier_sql as $row)
	{
		$supplier_arr[$row[csf("id")]]["supplier_name"]=$row[csf("supplier_name")];
		$supplier_arr[$row[csf("id")]]["address_1"]=$row[csf("address_1")];
	}
	$sql_bank_info = sql_select("SELECT ID, BANK_NAME, BRANCH_NAME, ADDRESS from lib_bank ");
	$bank_dtls_arr=array();
	foreach($sql_bank_info as $row)
	{
		$bank_dtls_arr[$row['ID']]['BANK_NAME']=$row['BANK_NAME'];
		$bank_dtls_arr[$row['ID']]['BRANCH_NAME']=$row['BRANCH_NAME'];
		$bank_dtls_arr[$row['ID']]['ADDRESS']=$row['ADDRESS'];
	}
	$sql_btb="SELECT ID, BTB_SYSTEM_ID, BTB_PREFIX_NUMBER, LC_DATE, APPLICATION_DATE, IMPORTER_ID, ITEM_CATEGORY_ID, ISSUING_BANK_ID, CURRENCY_ID, SUPPLIER_ID, LC_VALUE, MATURITY_FROM_ID, LAST_SHIPMENT_DATE, LC_EXPIRY_DATE, TENOR, PI_ID, INCO_TERM_ID, INCO_TERM_PLACE, ORIGIN, COVER_NOTE_NO, COVER_NOTE_DATE, LC_TYPE_ID
	from com_btb_lc_master_details
	where id=$data and is_deleted=0 and status_active=1";
	$sql_btb_res=sql_select($sql_btb);
	$btb_id = $sql_btb_res[0]['ID'];
	$lc_value = $sql_btb_res[0]['LC_VALUE'];
	$last_ship_date = $sql_btb_res[0]['LAST_SHIPMENT_DATE'];
	$expire_date = $sql_btb_res[0]['LC_EXPIRY_DATE'];
	$inco_term_place = $incoterm[$sql_btb_res[0]['INCO_TERM_ID']]." ".$sql_btb_res[0]['INCO_TERM_PLACE'];
	$importer_name = $company_arr[$sql_btb_res[0]['IMPORTER_ID']];
	$supplier_name = $supplier_arr[$sql_btb_res[0]['SUPPLIER_ID']]["supplier_name"];
	$supplier_address = $supplier_arr[$sql_btb_res[0]['SUPPLIER_ID']]["address_1"];
	$origin_name = $country_arr[$sql_btb_res[0]['ORIGIN']];
	$cover_note_no = $sql_btb_res[0]['COVER_NOTE_NO'];
	$cover_note_date = $sql_btb_res[0]['COVER_NOTE_DATE'];
	$issuing_bank_id = $sql_btb_res[0]['ISSUING_BANK_ID'];
	$swift_code=$bank_data[$issuing_bank_id]["SWIFT_CODE"];
	$erq_no=$bank_data[$issuing_bank_id]["ACCOUNT_NO"];
	$bank_name=$bank_dtls_arr[$sql_btb_res[0]['ISSUING_BANK_ID']]['BANK_NAME'];
	$bank_branch=$bank_dtls_arr[$sql_btb_res[0]['ISSUING_BANK_ID']]['BRANCH_NAME'];
	$bank_address=	$bank_dtls_arr[$sql_btb_res[0]['ISSUING_BANK_ID']]['ADDRESS'];
	$lc_type_value= $lc_type[$sql_btb_res[0]['LC_TYPE_ID']];
	$currency_name      = $currency[$sql_btb_res[0]["CURRENCY_ID"]];
	$ref				= $sql_btb_res[0]["BTB_SYSTEM_ID"];
	$ref_no				= $sql_btb_res[0]["BTB_PREFIX_NUMBER"];

	$pi_ids=$sql_btb_res[0]['PI_ID'];
	$sql_pi_res=sql_select("SELECT a.PI_NUMBER, a.PI_DATE, a.ITEM_CATEGORY_ID, a.HS_CODE, a.PI_VALIDITY_DATE, listagg(cast(b.HS_CODE as varchar(4000)),',') within group(order by b.id) as ITEM_HS_CODE, sum(b.QUANTITY) as QUANTITY 
	from com_pi_master_details a, com_pi_item_details b 
	where a.id=b.pi_id and a.status_active=1 and b.status_active=1 and a.id in($pi_ids)
	group by a.PI_NUMBER, a.PI_DATE, a.ITEM_CATEGORY_ID, a.HS_CODE, a.PI_VALIDITY_DATE");

	$category_name='';
	$pi_data_arr=array();
	foreach($sql_pi_res as $row)
	{
		$pi_number.=$row['PI_NUMBER'].', ';
		$pi_date.=change_date_format($row['PI_DATE']).', ';	
		$hs_code.=$row['HS_CODE'].', ';	
		if($row['ITEM_HS_CODE']!="") $item_hs_code.=$row['ITEM_HS_CODE'].', ';
		$pi_qnty+=$row['QUANTITY'];
		if($category_check[$row['ITEM_CATEGORY_ID']]=="")
		{
			$category_check[$row['ITEM_CATEGORY_ID']]=$row['ITEM_CATEGORY_ID'];
			if ($row['ITEM_CATEGORY_ID']==2 || $row['ITEM_CATEGORY_ID']==3) $category_name.='Fabric, ';
			else if ($row['ITEM_CATEGORY_ID']==12 || $row['ITEM_CATEGORY_ID']==24) $category_name.='Dyeing, ';
			else if ($row['ITEM_CATEGORY_ID']==25) $category_name.='Accessories, ';
			else $category_name.=$item_category[$row['ITEM_CATEGORY_ID']].', ';
		}
	}
	$category_name=chop($category_name,", ");
	
	$sql_lcSc=sql_select("SELECT a.IMPORT_MST_ID, b.contract_no as LC_SC_NO, b.contract_date as LC_SC_DATE, b.BUYER_NAME, a.IS_LC_SC
	from  com_btb_export_lc_attachment a, com_sales_contract b
	where a.lc_sc_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.is_lc_sc=1 and a.import_mst_id=$data
	union all 
	select a.IMPORT_MST_ID, b.export_lc_no as LC_SC_NO, b.lc_date as LC_SC_DATE, b.BUYER_NAME, a.IS_LC_SC 
	from com_btb_export_lc_attachment a, com_export_lc b
	where a.lc_sc_id=b.id and a.status_active=1 and a.is_deleted=0 and a.is_lc_sc=0 and b.status_active=1 and b.is_deleted=0 and a.import_mst_id=$data");	
	$lc_sc_arr=array();
	foreach($sql_lcSc as $row)
	{
		$lc_sc_arr[$row['IMPORT_MST_ID']]['LC_SC_NO'].=$row['LC_SC_NO'].', ';
		$lc_sc_arr[$row['IMPORT_MST_ID']]['LC_SC_DATE'].=change_date_format($row['LC_SC_DATE']).', ';
		// $lc_sc_arr[$row['IMPORT_MST_ID']]['BUYER_NAME'].= $buyer_arr[$row['BUYER_NAME']].', ';
	}
	$lc_sc_no   = rtrim($lc_sc_arr[$btb_id]['LC_SC_NO'],', ');
	$lc_sc_date = rtrim($lc_sc_arr[$btb_id]['LC_SC_DATE'],', ');
	// $lc_sc_buyer = rtrim($lc_sc_arr[$btb_id]['BUYER_NAME'],', ');
	?>
    <table width="750" cellpadding="0" align="left" cellspacing="0" border="0">
		<tr><td colspan="4" height="170"></td></tr>
		<tr>
		<td width="25"></td>
		<td  colspan="3">Ref. No.: <? echo $ref;?></td>
		</tr>
		<tr>
			<td width="25"> </td>
			<td width="675" colspan="3">Date: <? echo date('d-m-Y');?><? //echo $lc_sc_date; ?></td>
		</tr>
		<tr><td colspan="4" height="25"></td></tr>
		<tr>
			<td width="25"></td>

			<td width="675" colspan="3">The Sr. Vice President. </br>
			<? echo $bank_name;?></br>
			<? echo $bank_branch;?></br>
			<? echo $bank_address;?></br>
			</td>
		</tr>
        <tr><td colspan="4" height="60">&nbsp;</td></tr>
		<tr>
			<td width="25"></td>
			<td width="675" colspan="3">Sub: Request of make advance payment by <? echo $lc_type_value; ?> for <? echo $currency_name .' '.number_format($lc_value,2); ?> Against Export L/C No.<? echo $lc_sc_no; ?> dated: <? echo $lc_sc_date; ?> which our ERQ A/C NO. <? echo $erq_no; ?>.
			</td>

		</tr>
        <tr><td colspan="4">&nbsp;</td></tr>
		<tr>
			<td width="25"></td>
			<td width="675" colspan="3">Dear Sir,</td>
		</tr>
		<tr>
			<td width="25"></td>
			<td width="675" colspan="3">We would request you to please make advance payment by <? echo $lc_type_value; ?> for <? echo $currency_name .' '.number_format($lc_value,2);?> Which pro-forma invoice no. <? echo $pi_number; ?> dt. <? echo $pi_date; ?></td>

		</tr>

        <tr><td colspan="4"  height="50">&nbsp;</td></tr>
        <tr>
			<td width="25"></td>
			<td width="675" colspan="3">Please provide us this facility for our business convenience. </td>
		</tr>

        <tr><td colspan="4"  height="100">&nbsp;</td></tr>
        <tr>
			<td width="25"></td>
			<td width="675" colspan="3">Thanks & Regards, </br>
			Very truly yours,
			</td>
		</tr>
        <tr><td colspan="4"  height="100">&nbsp;</td></tr>
        <tr>
			<td width="25"></td>
			<td width="675" colspan="3">Authorized Signature</td>
		</tr>
	</table>
	<?
	exit();
}

if ($action=="file_popup")
{

  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	//echo $lien_bank;die;
	?>
	<script>
		function js_set_value(str)
		{
			$("#hide_file_no").val(str);
			parent.emailwindow.hide();
		}
		function set_caption(id)
		{
		if(id==1)  document.getElementById('search_by_td_up').innerHTML='Enter File No';
		if(id==2)  document.getElementById('search_by_td_up').innerHTML='Enter Buyer Name';
		if(id==3)  document.getElementById('search_by_td_up').innerHTML='Enter Lein Bank';
	    if(id==4)  document.getElementById('search_by_td_up').innerHTML='Enter SC/LC';
		}
	</script>
	</head>
	<body>
	    <div style="width:530px">
		    <form name="search_order_frm"  id="search_order_frm">
			    <fieldset style="width:530px">
			        <table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center" width="100%">
			            <thead>
			            	<th>Year</th>
			                <th>Search By</th>
			                <th id="search_by_td_up">Enter File No</th>
			                <th>
				                <input type="hidden" name="txt_company_id" id="txt_company_id" value="<?  echo $company_id; ?>"/>
				               
				                <input type="hidden" name="txt_sclc_id" id="txt_sclc_id" value="<? //echo ?>"/>
				                <input type="hidden" name="txt_selected_file" id="txt_selected_file" value=""/>
			                </th>
			            </thead>
			            <tbody>
			                <tr class="general">
			                	<td>
			                    <?
								$sql=sql_select("select lc_year as lc_sc_year from com_export_lc where beneficiary_name='$company_id' and status_active=1 and is_deleted=0  union all select sc_year as lc_sc_year from com_sales_contract where beneficiary_name='$company_id' and status_active=1 and is_deleted=0");
								foreach($sql as $row)
								{
									$lc_sc_year[$row[csf("lc_sc_year")]]=$row[csf("lc_sc_year")];
								}
								echo create_drop_down( "cbo_year", 100,$lc_sc_year,"", 1, "-- Select --",$cbo_year);
								?>
			                    </td>
			                    <td>
			                    <?
								$sarch_by_arr=array(1=>"File No",2=>"Buyer",3=>"Lien Bank",4=>"SC/LC");
								echo create_drop_down( "cbo_search_by", 130,$sarch_by_arr,"", 0, "-- Select Search --", 1,"load_drop_down( 'pi_controller_urmi',document.getElementById('txt_company_id').value+'_'+this.value, 'load_drop_down_search', 'search_by_td' );set_caption(this.value)");
								?>
			                    </td>
			                    <td align="center" id="search_by_td">
			                    	<input type="text" name="txt_search_common" id="txt_search_common" class="text_boxes" style="width:160px" autocomplete=off />
			                    </td>
			                    <td>
			                    	<input type="button" name="show" id="show" onClick="show_list_view(document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+<? echo $company_id; ?>+'_'+document.getElementById('cbo_year').value+'_'+'<? echo $is_lc_sc; ?>'+'_'+'<? echo $lc_sc_id; ?>','search_file_info','search_div_file','pi_controller_urmi','setFilterGrid(\'list_view\',-1)')" class="formbutton" style="width:100px;" value="Show" />
			                    </td>
			                </tr>
			            </tbody>
			        </table>
			        <table width="100%">
			            <tr>
			                <td>
			                	<div style="width:560px; margin-top:5px" id="search_div_file" align="left"></div>
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
	exit();
}

if ($action=="search_file_info")
{
	$ex_data = explode("_",$data);
	// print_r($ex_data);die;
	$cbo_search_by = $ex_data[0];
	$txt_search_common = $ex_data[1];
	$company_id = $ex_data[2];
	//$buyer_id = $ex_data[3];
	//$lien_bank_id = $ex_data[4];
	$cbo_year = $ex_data[3];
	$is_lc_sc = str_replace("'","",$ex_data[4]);
	$lc_sc_id = str_replace("'","",$ex_data[5]);
	//echo $cbo_year; die;
	//if($buyer_id!=0) $buy_query="and buyer_name='$buyer_id'"; else  $buy_query="";
	//if($lien_bank_id!=0) $lien_bank_id="and lien_bank='$lien_bank_id'"; else  $lien_bank_id="";
	if($cbo_year!=0)
	{
		$year_cond_sc="and sc_year='$cbo_year'";
		$year_cond_lc="and lc_year='$cbo_year'";
	}
	else
	{
		$year_cond_sc="";
		$year_cond_lc="";
	}
	//$year_cond_sc="and sc_year='".date("Y")."'";
	//$year_cond_lc="and lc_year='".date("Y")."'";
	//echo $lien_bank_id;die;

	//if($txt_search_common==0)$txt_search_common="";

    $txt_search_common = trim($txt_search_common);
    $search_cond ="";$search_cond_lc="";$search_cond_sc="";
    if($txt_search_common!="")
    {
        if($cbo_search_by==1)
        {
            $search_cond .= " and internal_file_no like '%$txt_search_common%'";
        }
        else if($cbo_search_by==2)
        {
            $search_cond .= " and buyer_name='$txt_search_common'";
        }
        else if($cbo_search_by==3)
        {
            $search_cond .= " and lien_bank='$txt_search_common'";
        }
        else if($cbo_search_by==4)
        {
            $search_cond_lc .= " and export_lc_no='$txt_search_common'";
            $search_cond_sc .= " and contract_no='$txt_search_common'";
        }
    }
    //echo $cbo_search_by."**".$txt_search_common; die;
    //echo $cbo_search_by."**".$search_cond_lc."**".$search_cond_sc; die;
    if($db_type == 0)
    {
		$sql = "select a.id,a.beneficiary_name, a.internal_file_no, a.lien_bank, a.buyer_name ,  a.lc_sc_year , group_concat(a.export_lc_no) as export_lc_no,a.is_lc_sc
		from (
		select id,beneficiary_name, internal_file_no, lien_bank, buyer_name , lc_year as lc_sc_year, group_concat(export_lc_no) as export_lc_no, 'export' as type, 1 as is_lc_sc
		from com_export_lc
		where beneficiary_name='$company_id' and status_active=1 and is_deleted=0 $buy_query $lien_bank_id $year_cond_lc $search_cond $search_cond_lc
		group by id,internal_file_no, lc_year, beneficiary_name, buyer_name , lien_bank
		union all
		select id,beneficiary_name, internal_file_no, lien_bank, buyer_name, sc_year as lc_sc_year,group_concat(contract_no) as export_lc_no, 'import' as type, 2 as is_lc_sc
		from com_sales_contract
		where beneficiary_name='$company_id'
		and status_active=1 and is_deleted=0 $buy_query $lien_bank_id $year_cond_sc $search_cond $search_cond_sc
		group by id,internal_file_no, sc_year, beneficiary_name, buyer_name , lien_bank
		) a
		group by a.id,a.beneficiary_name, a.internal_file_no, a.lien_bank, a.buyer_name , a.lc_sc_year,a.is_lc_sc";
    }
    else
    {
    	/*$sql = "select a.id,a.beneficiary_name, a.internal_file_no, a.lien_bank, a.buyer_name ,  a.lc_sc_year , rtrim(xmlagg(xmlelement(e,a.export_lc_no,',').extract('//text()') order by a.export_lc_no).GetClobVal(),',') AS export_lc_no
		from (
		select id,beneficiary_name, internal_file_no, lien_bank, buyer_name , lc_year as lc_sc_year, listagg(cast(export_lc_no as varchar(4000)),',') within group(order by export_lc_no) as export_lc_no, 'export' as type
		from com_export_lc
		where beneficiary_name='$company_id' and status_active=1 and is_deleted=0 $buy_query $lien_bank_id $year_cond_lc $search_cond $search_cond_lc
		group by id,internal_file_no, lc_year, beneficiary_name, buyer_name , lien_bank
		union all
		select id,beneficiary_name, internal_file_no, lien_bank, buyer_name, sc_year as lc_sc_year,listagg(cast(contract_no as varchar(4000)),',') within group(order by contract_no) as export_lc_no, 'import' as type
		from com_sales_contract
		where beneficiary_name='$company_id'
		and status_active=1 and is_deleted=0 $buy_query $lien_bank_id $year_cond_sc $search_cond $search_cond_sc
		group by id,internal_file_no, sc_year, beneficiary_name, buyer_name , lien_bank
		) a
		group by a.id,a.beneficiary_name, a.internal_file_no, a.lien_bank, a.buyer_name , a.lc_sc_year";*/
		
		$sql = "SELECT a.id, a.beneficiary_name, a.internal_file_no, a.lien_bank, a.buyer_name ,  a.lc_sc_year , listagg(cast(a.export_lc_no as varchar2(4000)),',') within group (order by a.export_lc_no) as export_lc_no ,a.is_lc_sc from (
		select id,beneficiary_name, internal_file_no, lien_bank, buyer_name , lc_year as lc_sc_year,  listagg(cast(export_lc_no as varchar2(4000)),',') within group (order by export_lc_no) as export_lc_no, 'export' as type, 1 as is_lc_sc
		from com_export_lc
		where beneficiary_name='$company_id' and status_active=1 and is_deleted=0 $buy_query $lien_bank_id $year_cond_lc $search_cond $search_cond_lc
		group by id,internal_file_no, lc_year, beneficiary_name, buyer_name , lien_bank
		union all
		select id,beneficiary_name, internal_file_no, lien_bank, buyer_name, sc_year as lc_sc_year, listagg(cast(contract_no as varchar2(4000)),',') within group (order by contract_no) as export_lc_no, 'import' as type, 2 as is_lc_sc
		from com_sales_contract		
		where beneficiary_name='$company_id'
		and status_active=1 and is_deleted=0 $buy_query $lien_bank_id $year_cond_sc $search_cond $search_cond_sc
		group by id,internal_file_no, sc_year, beneficiary_name, buyer_name , lien_bank
		) a
		group by a.id,a.beneficiary_name, a.internal_file_no, a.lien_bank, a.buyer_name , a.lc_sc_year, a.is_lc_sc
		order by a.id desc";
		// echo $sql;
    }
    $lein_bank_arr=return_library_array( "select bank_name,id from lib_bank where is_deleted=0  and status_active=1 and lien_bank=1 order by bank_name",'id','bank_name');
	$buyer_name_arr=return_library_array( "select id,buyer_name from lib_buyer where is_deleted=0  and status_active=1 order by buyer_name",'id','buyer_name');
	//echo $sql;
	?>
   <div style="width:560px">
    <form name="display_file"  id="display_file">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center" width="100%">
            <thead>
                <th width="60">Sl NO.</td>
                <th width="80">File NO</td>
                <th width="80">Year</td>
                <th width="130"> Buyer</td>
                <th width="100"> Lien Bank</td>
                <th >SC/LC No.</td>
            </thead>
            </table>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center" width="100%" id="list_view">
            <tbody>
            <?
			$sql_results=sql_select($sql);
			$i=1;
			//echo count($sql_results);
			foreach($sql_results as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; 
				else $bgcolor="#FFFFFF";
				//echo $row[csf("internal_file_no")];die;
				//if($db_type==2) $row[csf('export_lc_no')] = $row[csf('export_lc_no')]->load();
				if($is_lc_sc==$row[csf('is_lc_sc')] && $lc_sc_id==$row[csf('id')]){$bgcolor="#FFFF00";}else{$bgcolor=$bgcolor;};
				?>
                <tr bgcolor="<? echo $bgcolor; ?>"  onclick="js_set_value('<? echo $row[csf('internal_file_no')].'_'.$row[csf('is_lc_sc')].'_'.$row[csf('id')].'_'.$row[csf('export_lc_no')].'_'.$row[csf('lc_sc_year')];?>')" id="search<? echo $row[csf("id")]; ?>" style="cursor:pointer">
                    <td align="center" width="60"> <? echo $i;?></td>
                    <td align="center" width="80"><p><? echo $row[csf("internal_file_no")];  ?></p></td>
                    <td align="center" width="80"><p><? echo $row[csf("lc_sc_year")];  ?></p></td>
                    <td width="130"><p><? echo $buyer_name_arr[$row[csf("buyer_name")]];  ?></p></td>
                    <td width="100"><p><? echo $lein_bank_arr[$row[csf("lien_bank")]];  ?></p></td>
                    <td><p><? echo $row[csf("export_lc_no")];  ?></p></td>
                </tr>
                <?
				$i++;
			}
			?>
            </tbody>

            <input type="hidden" id="hide_file_no" name="hide_file_no"  />
        </table>
    </form>
    <script>setFilterGrid('list_view',-1)</script>
    </div>
    <?
	exit();
}

if ($action==='btb_import_lc_tt_fdd2')
{
	$currency_sign_arr=array(1=>'৳',2=>'$',3=>'€',4=>'€',5=>'$',6=>'£',7=>'¥');
	$company_arr = return_library_array('SELECT id, company_name FROM lib_company','id','company_name');
	$buyer_arr =return_library_array('SELECT id, buyer_name FROM lib_buyer','id','buyer_name');
	$supplier_arr = return_library_array('SELECT id, supplier_name FROM lib_supplier','id','supplier_name');
	
	$sql_bank_info = sql_select("SELECT ID, BANK_NAME, BRANCH_NAME, ADDRESS from lib_bank ");
	$bank_dtls_arr=array();
	foreach($sql_bank_info as $row)
	{
		$bank_dtls_arr[$row['ID']]['BANK_NAME']=$row['BANK_NAME'];
		$bank_dtls_arr[$row['ID']]['BRANCH_NAME']=$row['BRANCH_NAME'];
		$bank_dtls_arr[$row['ID']]['ADDRESS']=$row['ADDRESS'];
	}
	$sql_btb="SELECT ID, LC_DATE, IMPORTER_ID, ITEM_CATEGORY_ID, ISSUING_BANK_ID, CURRENCY_ID, SUPPLIER_ID, LC_VALUE, PI_ID, LC_TYPE_ID, LCAF_NO, REMARKS
	from com_btb_lc_master_details
	where id=$data and is_deleted=0 and status_active=1";
	$sql_btb_res=sql_select($sql_btb);
	$btb_id = $sql_btb_res[0]['ID'];
	$lc_value = $sql_btb_res[0]['LC_VALUE'];
	$importer_name = $company_arr[$sql_btb_res[0]['IMPORTER_ID']];
	$supplier_name = $supplier_arr[$sql_btb_res[0]['SUPPLIER_ID']];
	$lcaf_no = $sql_btb_res[0]['LCAF_NO'];
	$remarks = $sql_btb_res[0]['REMARKS'];
	$bank_name=$bank_dtls_arr[$sql_btb_res[0]['ISSUING_BANK_ID']]['BANK_NAME'];
	$bank_branch=$bank_dtls_arr[$sql_btb_res[0]['ISSUING_BANK_ID']]['BRANCH_NAME'];
	$bank_address=	$bank_dtls_arr[$sql_btb_res[0]['ISSUING_BANK_ID']]['ADDRESS'];
	$lc_type_value= $lc_type[$sql_btb_res[0]['LC_TYPE_ID']];
	$currency_name      = $currency[$sql_btb_res[0]["CURRENCY_ID"]];
	$currency_sign      = $currency_sign_arr[$sql_btb_res[0]["CURRENCY_ID"]];

	$pi_ids=$sql_btb_res[0]['PI_ID'];
	$sql_pi_res=sql_select("SELECT a.PI_NUMBER, a.PI_DATE, a.ITEM_CATEGORY_ID
	from com_pi_master_details a
	where a.status_active=1 and a.id in($pi_ids) ");
	$category_name=$$pi_number=$pi_date='';
	foreach($sql_pi_res as $row)
	{
		$pi_number.=$row['PI_NUMBER'].', ';
		$pi_date.=change_date_format($row['PI_DATE']).', ';	
		if($category_check[$row['ITEM_CATEGORY_ID']]=="")
		{
			$category_check[$row['ITEM_CATEGORY_ID']]=$row['ITEM_CATEGORY_ID'];
			$category_name.=$item_category[$row['ITEM_CATEGORY_ID']].', ';
		}
	}
	$category_name=chop($category_name,", ");
	$pi_number=chop($pi_number,", ");
	$pi_date=chop($pi_date,", ");
	
	$sql_lcSc=sql_select("SELECT a.import_mst_id, b.contract_no as LC_SC_NO, b.contract_date as LC_SC_DATE, b.BUYER_NAME, a.IS_LC_SC, b.last_shipment_date as SHIPMENT_DATE, b.contract_value as LC_SC_VALUE, b.expiry_date as EXPIRY_DATE
	from  com_btb_export_lc_attachment a, com_sales_contract b
	where a.lc_sc_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.is_lc_sc=1 and a.import_mst_id=$data
	union all 
	select  a.import_mst_id,b.export_lc_no as LC_SC_NO, b.lc_date as LC_SC_DATE, b.BUYER_NAME, a.IS_LC_SC , b.last_shipment_date as SHIPMENT_DATE, b.lc_value as LC_SC_VALUE, b.expiry_date as EXPIRY_DATE
	from com_btb_export_lc_attachment a, com_export_lc b
	where a.lc_sc_id=b.id and a.status_active=1 and a.is_deleted=0 and a.is_lc_sc=0 and b.status_active=1 and b.is_deleted=0 and a.import_mst_id=$data");	

	?>
	<style>
		.wrd_brk{word-break: break-all;}
		.center{text-align: center;}
		.right{text-align: right;}
	</style>
    <table width="750" cellpadding="0" align="left" cellspacing="0" border="0">
		<tr><td colspan="4" height="170"></td></tr>
		<tr>
			<td width="25"> </td>
			<td width="675" colspan="3">Date: <? echo date('F d, Y');?></td>
		</tr>
		<tr><td colspan="4" height="25"></td></tr>
		<tr>
			<td width="25"></td>

			<td width="675" colspan="3">To </br>The Head of the Branch</br>
			<? echo $bank_name;?></br>
			<? echo $bank_branch;?></br>
			<? echo $bank_address;?></br>
			</td>
		</tr>
        <tr><td colspan="4" height="60">&nbsp;</td></tr>
		<tr>
			<td width="25"></td>
			<td width="675" colspan="3">Sub: <u>Request for <? echo $lc_type_value.' '.$currency_name.' '.$currency_sign.''.number_format($lc_value,2);?></u>
			</td>
		</tr>
        <tr><td colspan="4">&nbsp;</td></tr>
		<tr>
			<td width="25"></td>
			<td width="675" colspan="3">Dear Sir,</td>
		</tr>
		<tr><td colspan="4">&nbsp;</td></tr>
		<tr>
			<td width="25"></td>
			<td width="675" colspan="3">We would like to inform you that we need to <? echo $lc_type_value; ?> for <?=$currency_name.' '.$currency_sign.''.number_format($lc_value,2);?>. Against Export L/C No-as below:-
		</tr>
		<tr>
			<td width="25"></td>
			<td width="675" colspan="3">
				<table width="675" cellpadding="0" cellspacing="0" class="rpt_table" border="1">
					<thead>
						<tr>
							<th width="30">SL</th>
							<th width="150">Export LC/SC No</th>
							<th width="80">LC/SC Date</th>
							<th width="80">Shipment Date</th>
							<th width="80">Expiry</th>
							<th width="100">Value</th>
							<th>Buyer</th>
						</tr>
					</thead>
					<tbody>
						<?
							$i=1;
							foreach($sql_lcSc as $row)
							{
								?>
									<tr>
										<td class="wrd_brk center"><?=$i;?></td>
										<td class="wrd_brk "><?=$row['LC_SC_NO'];?></td>
										<td class="wrd_brk center"><?=change_date_format($row['LC_SC_DATE']);?></td>
										<td class="wrd_brk center"><?=change_date_format($row['SHIPMENT_DATE']);?></td>
										<td class="wrd_brk center"><?=change_date_format($row['EXPIRY_DATE']);?></td>
										<td class="wrd_brk right"><?=$currency_sign.''.number_format($row['LC_SC_VALUE'],2);?></td>
										<td class="wrd_brk "><?=$buyer_arr[$row['BUYER_NAME']];?></td>
									</tr>
								<?
								$total_lc_sc_value+=$row['LC_SC_VALUE'];
								$i++;
							}
						?>
					</tbody>
					<tfoot>
						<tr>
							<th colspan="5" class="right">Total</th>
							<th class="wrd_brk right"><?=$currency_sign.''.number_format($total_lc_sc_value,2);?></th>
							<th></th>
						</tr>
					</tfoot>
				</table>
			</td>
		</tr>
        <tr><td colspan="4"  height="10">&nbsp;</td></tr>
        <tr>
			<td width="25"></td>
			<td width="675" colspan="3">For import of <?=$category_name;?> in favor of <?=$supplier_name;?> . As per Proforma Invoice No: <?=$pi_number;?> Date- <?=$pi_date; ?> & LCAF NO: <?=$lcaf_no.' '.$remarks;?></td>
		</tr>
        <tr><td colspan="4"  height="50">&nbsp;</td></tr>
        <tr>
			<td width="25"></td>
			<td width="675" colspan="3">Your prompt action will be highly appreciated.</td>
		</tr>
	</table>
	<?
	exit();
}

if ($action==='btb_import_lc_open')
{
	$country_library=return_library_array( "select id, country_name from lib_country", "id", "country_name"  );	
	$company_arr = return_library_array('SELECT id, company_name FROM lib_company','id','company_name');
	$company_arr_short=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$back_sql=sql_select("select a.ID, a.SWIFT_CODE, b.ACCOUNT_TYPE, b.ACCOUNT_NO from LIB_BANK a, LIB_BANK_ACCOUNT b where a.id=b.account_id");

	$bank_data=array();
	foreach($back_sql as $row)
	{
		$bank_data[$row["ID"]]["SWIFT_CODE"]=$row["SWIFT_CODE"];
		if($row["ACCOUNT_TYPE"]==6) $bank_data[$row["ID"]]["ACCOUNT_NO"]=$row["ACCOUNT_NO"];
	}
	$supplier_sql = sql_select('SELECT id, supplier_name, address_1 FROM lib_supplier');
	foreach($supplier_sql as $row)
	{
		$supplier_arr[$row[csf("id")]]["supplier_name"]=$row[csf("supplier_name")];
		$supplier_arr[$row[csf("id")]]["address_1"]=$row[csf("address_1")];
	}
	$sql_bank_info = sql_select("SELECT ID, BANK_NAME,BANK_SHORT_NAME, BRANCH_NAME, ADDRESS, DESIGNATION from lib_bank ");
	$bank_dtls_arr=array();
	foreach($sql_bank_info as $row)
	{
		$bank_dtls_arr[$row['ID']]['BANK_NAME']=$row['BANK_NAME'];
		$bank_dtls_arr[$row['ID']]['BRANCH_NAME']=$row['BRANCH_NAME'];
		$bank_dtls_arr[$row['ID']]['ADDRESS']=$row['ADDRESS'];
		$bank_dtls_arr[$row['ID']]['DESIGNATION']=$row['DESIGNATION'];
		$bank_dtls_arr[$row['ID']]['BANK_SHORT_NAME']=$row['BANK_SHORT_NAME'];
	}
	$sql_btb="SELECT ID, BTB_SYSTEM_ID, BTB_PREFIX_NUMBER, LC_DATE, APPLICATION_DATE, IMPORTER_ID, ITEM_CATEGORY_ID, ISSUING_BANK_ID, CURRENCY_ID, SUPPLIER_ID, LC_VALUE, MATURITY_FROM_ID, LAST_SHIPMENT_DATE, LC_EXPIRY_DATE, TENOR, PI_ID, INCO_TERM_ID, INCO_TERM_PLACE, ORIGIN, COVER_NOTE_NO, COVER_NOTE_DATE, LC_TYPE_ID
	from com_btb_lc_master_details
	where id=$data and is_deleted=0 and status_active=1";
	// echo $sql_btb;die;
	$sql_btb_res=sql_select($sql_btb);
	$btb_id = $sql_btb_res[0]['ID'];
	$lc_value = $sql_btb_res[0]['LC_VALUE'];
	$last_ship_date = $sql_btb_res[0]['LAST_SHIPMENT_DATE'];
	$expire_date = $sql_btb_res[0]['LC_EXPIRY_DATE'];
	$inco_term_place = $incoterm[$sql_btb_res[0]['INCO_TERM_ID']]." ".$sql_btb_res[0]['INCO_TERM_PLACE'];
	$importer_name = $company_arr[$sql_btb_res[0]['IMPORTER_ID']];
	$supplier_name = $supplier_arr[$sql_btb_res[0]['SUPPLIER_ID']]["supplier_name"];
	$supplier_address = $supplier_arr[$sql_btb_res[0]['SUPPLIER_ID']]["address_1"];
	$origin_name = $country_arr[$sql_btb_res[0]['ORIGIN']];
	$cover_note_no = $sql_btb_res[0]['COVER_NOTE_NO'];
	$cover_note_date = $sql_btb_res[0]['COVER_NOTE_DATE'];
	$issuing_bank_id = $sql_btb_res[0]['ISSUING_BANK_ID'];
	$swift_code=$bank_data[$issuing_bank_id]["SWIFT_CODE"];
	$erq_no=$bank_data[$issuing_bank_id]["ACCOUNT_NO"];
	$bank_name=$bank_dtls_arr[$sql_btb_res[0]['ISSUING_BANK_ID']]['BANK_NAME'];
	$bank_short_name=$bank_dtls_arr[$sql_btb_res[0]['ISSUING_BANK_ID']]['BANK_SHORT_NAME'];
	$bank_branch=$bank_dtls_arr[$sql_btb_res[0]['ISSUING_BANK_ID']]['BRANCH_NAME'];
	$bank_address=	$bank_dtls_arr[$sql_btb_res[0]['ISSUING_BANK_ID']]['ADDRESS'];
	$lc_type_value= $lc_type[$sql_btb_res[0]['LC_TYPE_ID']];
	$currency_name      = $currency[$sql_btb_res[0]["CURRENCY_ID"]];
	$ref				= $company_arr_short[$sql_btb_res[0]["IMPORTER_ID"]];
	$ref_no				= $sql_btb_res[0]["BTB_PREFIX_NUMBER"];
	$bank_designation = return_field_value("custom_designation","lib_designation","id=".$bank_dtls_arr[$sql_btb_res[0]["ISSUING_BANK_ID"]]["DESIGNATION"],"custom_designation");
  	//  echo $ref;die;
	$pi_ids=$sql_btb_res[0]['PI_ID'];
	$sql_pi_res=sql_select("SELECT a.PI_NUMBER, a.PI_DATE, a.ITEM_CATEGORY_ID, a.HS_CODE, a.PI_VALIDITY_DATE, listagg(cast(b.HS_CODE as varchar(4000)),',') within group(order by b.id) as ITEM_HS_CODE, sum(b.QUANTITY) as QUANTITY 
	from com_pi_master_details a, com_pi_item_details b 
	where a.id=b.pi_id and a.status_active=1 and b.status_active=1 and a.id in($pi_ids)
	group by a.PI_NUMBER, a.PI_DATE, a.ITEM_CATEGORY_ID, a.HS_CODE, a.PI_VALIDITY_DATE");

	$category_name='';
	$pi_data_arr=array();
	foreach($sql_pi_res as $row)
	{
		if($pi_number!=''){$pi_number.=', '.$row['PI_NUMBER'];}else{$pi_number.=$row['PI_NUMBER'];};
		if($pi_date!=''){$pi_date.=', '.change_date_format($row['PI_DATE']);}else{$pi_date.=change_date_format($row['PI_DATE']);}
		if($hs_code!=''){$hs_code.=', '.$row['HS_CODE'];}else{$hs_code.=$row['HS_CODE'];}
		if($row['ITEM_HS_CODE']!="") $item_hs_code.=$row['ITEM_HS_CODE'].', ';
		$pi_qnty+=$row['QUANTITY'];
		if($category_check[$row['ITEM_CATEGORY_ID']]=="")
		{
			$category_check[$row['ITEM_CATEGORY_ID']]=$row['ITEM_CATEGORY_ID'];
			if ($row['ITEM_CATEGORY_ID']==2 || $row['ITEM_CATEGORY_ID']==3) $category_name.='Fabric, ';
			else if ($row['ITEM_CATEGORY_ID']==12 || $row['ITEM_CATEGORY_ID']==24) $category_name.='Dyeing, ';
			else if ($row['ITEM_CATEGORY_ID']==25) $category_name.='Accessories, ';
			else $category_name.=$item_category[$row['ITEM_CATEGORY_ID']].', ';
		}
	}
	$category_name=chop($category_name,", ");
	
	$sql_lcSc=sql_select("SELECT a.IMPORT_MST_ID, b.contract_no as LC_SC_NO, b.contract_date as LC_SC_DATE, b.contract_value as LC_SC_VALUE, b.BUYER_NAME, a.IS_LC_SC
	from  com_btb_export_lc_attachment a, com_sales_contract b
	where a.lc_sc_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.is_lc_sc=1 and a.import_mst_id=$data
	union all 
	select a.IMPORT_MST_ID, b.export_lc_no as LC_SC_NO, b.lc_date as LC_SC_DATE, b.lc_value as LC_SC_VALUE, b.BUYER_NAME, a.IS_LC_SC 
	from com_btb_export_lc_attachment a, com_export_lc b
	where a.lc_sc_id=b.id and a.status_active=1 and a.is_deleted=0 and a.is_lc_sc=0 and b.status_active=1 and b.is_deleted=0 and a.import_mst_id=$data");	
	$lc_sc_arr=array();
	foreach($sql_lcSc as $row)
	{
		$lc_sc_arr[$row['IMPORT_MST_ID']]['LC_SC_NO'] =$row['LC_SC_NO'];
		$lc_sc_arr[$row['IMPORT_MST_ID']]['LC_SC_DATE'] =change_date_format($row['LC_SC_DATE']);
		$lc_sc_arr[$row['IMPORT_MST_ID']]['LC_SC_VALUE'] =$row['LC_SC_VALUE'];
		// $lc_sc_arr[$row['IMPORT_MST_ID']]['BUYER_NAME'].= $buyer_arr[$row['BUYER_NAME']].', ';
		if($lc_sc_no!=''){$lc_sc_no .= ", ".$lc_sc_arr[$btb_id]['LC_SC_NO'];}else{$lc_sc_no =$lc_sc_arr[$btb_id]['LC_SC_NO'];};
		if($lc_sc_date!=''){$lc_sc_date .= ", ".$lc_sc_arr[$btb_id]['LC_SC_DATE'];}else{$lc_sc_date = $lc_sc_arr[$btb_id]['LC_SC_DATE'];}
		if($lc_sc_value!=''){$lc_sc_value .= ", ".$lc_sc_arr[$btb_id]['LC_SC_VALUE'];}else{$lc_sc_value =$lc_sc_arr[$btb_id]['LC_SC_VALUE'];}
	}

	// $lc_sc_buyer = rtrim($lc_sc_arr[$btb_id]['BUYER_NAME'],', ');
	$sql_lib_location=sql_select("SELECT ID, LOCATION_NAME,CONTACT_NO,EMAIL,COUNTRY_ID,REMARK from lib_location where company_id='".$sql_btb_res[0]['IMPORTER_ID']."' and status_active=1 ");
	?>
		<div style="width:750;">
		<h1 align="center" style="font-size:300%; margin-bottom:0;padding-bottom:0;"><?=$importer_name;?></h1>
		<div align="center" style="border:1px solid;"><? echo 'Factory: '.$sql_lib_location[0]['LOCATION_NAME'].',</br>'.$country_library[$sql_lib_location[0]['COUNTRY_ID']].', Phone: '.$sql_lib_location[0]['CONTACT_NO'].', Fax# '.$sql_lib_location[0]['REMARK'].', E-mail: '.$sql_lib_location[0]['EMAIL']; ?> 
		</div>
		</div>
    <table width="750" cellpadding="0" align="left" cellspacing="0" border="0">
		<tr><td colspan="4" height="50"></td></tr>
		<tr>
		<td width="25"></td>
		<td  colspan="3">Ref. No.: <? echo $ref."/".$bank_short_name."/".$lc_sc_no ;?></td>
		</tr>
		<tr>
			<td width="25"> </td>
			<td width="675" colspan="3">Date: <? echo date('d-m-Y');?><? //echo $lc_sc_date; ?></td>

		</tr>
		<tr><td colspan="4" height="50"></td></tr>
		<tr>
			<td width="25" valign="top"></td>

			<td width="675" colspan="3"><strong>THE <? echo $bank_designation; ?> & HEAD OF BRANCH </strong></br>
			<? echo $bank_name;?></br>
			<? echo $bank_branch;?></br>
			<? echo $bank_address;?></br>
			</td>
		</tr>
        <tr><td colspan="4" height="50">&nbsp;</td></tr>
		<tr>
			<td width="25"></td>
			<td width="675" colspan="3">Sub: <strong>Issuance of Back to Back Letter of Credit Local/Foreign for </strong><? echo $currency_name .' '.number_format($lc_value,2); ?> (US Dollar <? echo number_to_words(number_format($lc_value,2, '.', ''), '', 'Cent'); ?> only in favor of <?=$supplier_name;?> against supplier P/I # <? echo $pi_number; ?>) against Export LC/ Contract #  <? echo $lc_sc_no; ?> DT: <? echo $lc_sc_date." Value: ".$currency_name .' '.$lc_sc_value; ?>.
			</td>

		</tr>
        <tr><td colspan="4" height="50">&nbsp;</td></tr>
		<tr>
			<td width="25"></td>
			<td width="675" colspan="3">Dear Sir,</td>
		</tr>
		<tr>
			<td width="25"></td>
			<td width="675" colspan="3">Refer to the above you are requested to please arrange to open Local/ Foreign back to back LC for amount <? echo $currency_name .' '.number_format($lc_value,2); ?> only to procure  the raw materials against our Export LC/ Contract #  <? echo $lc_sc_no; ?> DT: <? echo $lc_sc_date." Value: ".$currency_name .' '.$lc_sc_value; ?>.
		</tr>

        <tr><td colspan="4"  height="25">&nbsp;</td></tr>
        <tr>
			<td width="25"></td>
			<td width="675" colspan="3">Kindly  arrange  to  open  the  Local/  Foreign  back to back LC  at  your  earliest and please send the LC  through  SWIFT. </td>
		</tr>
		<tr><td colspan="4"  height="25">&nbsp;</td></tr>
        <tr>
			<td width="25"></td>
			<td width="675" colspan="3">Your early response on this matter will be highly appreciated.</td>
		</tr>
        <tr><td colspan="4"  height="100">&nbsp;</td></tr>
        <tr>
			<td width="25"></td>
			<td width="675" colspan="3">Thanking you.</br>
								Yours faithfully,	
			</td>
		</tr>
		<tr><td colspan="4"  height="100">&nbsp;</td></tr>
        <tr>
			<td width="25"></td>
			<td width="675" colspan="3">Authorized Signature.
			</td>
		</tr>
		<tr><td colspan="4"  height="50">&nbsp;</td></tr>
        <tr>
			<td width="25"></td>
			<td width="675" colspan="3">Enclosed  :-</br>
			01. Back to Back LC Application Form,</br>
			02. Proforma Invoice,</br>
			03. LCAF,</br>
			04. Marine Cover Note
			</td>
		</tr>
	</table>
	<?
	exit();
}

if ($action==='btb_import_lc_ftt')
{
	$company_arr = return_library_array('SELECT id, company_name FROM lib_company','id','company_name');
	$company_arr_short=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$back_sql=sql_select("select a.ID, a.SWIFT_CODE, b.ACCOUNT_TYPE, b.ACCOUNT_NO from LIB_BANK a, LIB_BANK_ACCOUNT b where a.id=b.account_id");

	$bank_data=array();
	foreach($back_sql as $row)
	{
		$bank_data[$row["ID"]]["SWIFT_CODE"]=$row["SWIFT_CODE"];
		if($row["ACCOUNT_TYPE"]==6) $bank_data[$row["ID"]]["ACCOUNT_NO_ERQ"]=$row["ACCOUNT_NO"];
		if($row["ACCOUNT_TYPE"]==10) $bank_data[$row["ID"]]["ACCOUNT_NO_CD"]=$row["ACCOUNT_NO"];
	}
	$supplier_sql = sql_select('SELECT id, supplier_name, address_1 FROM lib_supplier');
	foreach($supplier_sql as $row)
	{
		$supplier_arr[$row[csf("id")]]["supplier_name"]=$row[csf("supplier_name")];
		$supplier_arr[$row[csf("id")]]["address_1"]=$row[csf("address_1")];
	}
	$sql_bank_info = sql_select("SELECT ID, BANK_NAME,BANK_SHORT_NAME, BRANCH_NAME, ADDRESS from lib_bank ");
	$bank_dtls_arr=array();
	foreach($sql_bank_info as $row)
	{
		$bank_dtls_arr[$row['ID']]['BANK_NAME']=$row['BANK_NAME'];
		$bank_dtls_arr[$row['ID']]['BRANCH_NAME']=$row['BRANCH_NAME'];
		$bank_dtls_arr[$row['ID']]['ADDRESS']=$row['ADDRESS'];
		$bank_dtls_arr[$row['ID']]['BANK_SHORT_NAME']=$row['BANK_SHORT_NAME'];
	}
	$sql_btb="SELECT ID, BTB_SYSTEM_ID, BTB_PREFIX_NUMBER, LC_DATE, APPLICATION_DATE, IMPORTER_ID, ITEM_CATEGORY_ID, ISSUING_BANK_ID, CURRENCY_ID, SUPPLIER_ID, LC_VALUE, MATURITY_FROM_ID, LAST_SHIPMENT_DATE, LC_EXPIRY_DATE, TENOR, PI_ID, INCO_TERM_ID, INCO_TERM_PLACE, ORIGIN, COVER_NOTE_NO, COVER_NOTE_DATE, LC_TYPE_ID, ADVISING_BANK, ADVISING_BANK_ADDRESS, LCA_NO
	from com_btb_lc_master_details
	where id=$data and is_deleted=0 and status_active=1";
	$sql_btb_res=sql_select($sql_btb);
	$btb_id = $sql_btb_res[0]['ID'];
	$lc_value = $sql_btb_res[0]['LC_VALUE'];
	$last_ship_date = $sql_btb_res[0]['LAST_SHIPMENT_DATE'];
	$expire_date = $sql_btb_res[0]['LC_EXPIRY_DATE'];
	$inco_term_place = $incoterm[$sql_btb_res[0]['INCO_TERM_ID']]." ".$sql_btb_res[0]['INCO_TERM_PLACE'];
	$importer_name = $company_arr[$sql_btb_res[0]['IMPORTER_ID']];
	$supplier_name = $supplier_arr[$sql_btb_res[0]['SUPPLIER_ID']]["supplier_name"];
	$supplier_address = $supplier_arr[$sql_btb_res[0]['SUPPLIER_ID']]["address_1"];
	$origin_name = $country_arr[$sql_btb_res[0]['ORIGIN']];
	$cover_note_no = $sql_btb_res[0]['COVER_NOTE_NO'];
	$cover_note_date = $sql_btb_res[0]['COVER_NOTE_DATE'];
	$issuing_bank_id = $sql_btb_res[0]['ISSUING_BANK_ID'];
	$swift_code=$bank_data[$issuing_bank_id]["SWIFT_CODE"];
	$erq_no=$bank_data[$issuing_bank_id]["ACCOUNT_NO_ERQ"];
	$cd_no=$bank_data[$issuing_bank_id]["ACCOUNT_NO_CD"];
	$bank_name=$bank_dtls_arr[$sql_btb_res[0]['ISSUING_BANK_ID']]['BANK_NAME'];
	$bank_branch=$bank_dtls_arr[$sql_btb_res[0]['ISSUING_BANK_ID']]['BRANCH_NAME'];
	$bank_address=	$bank_dtls_arr[$sql_btb_res[0]['ISSUING_BANK_ID']]['ADDRESS'];
	$bank_short_name=	$bank_dtls_arr[$sql_btb_res[0]['ISSUING_BANK_ID']]['BANK_SHORT_NAME'];
	$lc_type_value= $lc_type[$sql_btb_res[0]['LC_TYPE_ID']];
	$currency_name      = $currency[$sql_btb_res[0]["CURRENCY_ID"]];
	$ref				= $company_arr_short[$sql_btb_res[0]["IMPORTER_ID"]];
	$advising_bank		= $sql_btb_res[0]['ADVISING_BANK'];
	$advising_bank_issue = $sql_btb_res[0]['ADVISING_BANK_ADDRESS'];
	$lca_no = $sql_btb_res[0]['LCA_NO'];
	$ref_no				= $sql_btb_res[0]["BTB_PREFIX_NUMBER"];

	$pi_ids=$sql_btb_res[0]['PI_ID'];
	$sql_pi_res=sql_select("SELECT a.PI_NUMBER, a.PI_DATE, a.ITEM_CATEGORY_ID, a.HS_CODE, a.PI_VALIDITY_DATE, listagg(cast(b.HS_CODE as varchar(4000)),',') within group(order by b.id) as ITEM_HS_CODE, sum(b.QUANTITY) as QUANTITY 
	from com_pi_master_details a, com_pi_item_details b 
	where a.id=b.pi_id and a.status_active=1 and b.status_active=1 and a.id in($pi_ids)
	group by a.PI_NUMBER, a.PI_DATE, a.ITEM_CATEGORY_ID, a.HS_CODE, a.PI_VALIDITY_DATE");

	$category_name='';
	$pi_data_arr=array();
	foreach($sql_pi_res as $row)
	{
		if($pi_number!=''){$pi_number.=', '.$row['PI_NUMBER'];}else{$pi_number.=$row['PI_NUMBER'];};
		if($pi_date!=''){$pi_date.=', '.change_date_format($row['PI_DATE']);}else{$pi_date.=change_date_format($row['PI_DATE']);}
		if($hs_code!=''){$hs_code.=', '.$row['HS_CODE'];}else{$hs_code.=$row['HS_CODE'];}	
		if($row['ITEM_HS_CODE']!="") $item_hs_code.=$row['ITEM_HS_CODE'].', ';
		$pi_qnty+=$row['QUANTITY'];
		if($category_check[$row['ITEM_CATEGORY_ID']]=="")
		{
			$category_check[$row['ITEM_CATEGORY_ID']]=$row['ITEM_CATEGORY_ID'];
			if ($row['ITEM_CATEGORY_ID']==2 || $row['ITEM_CATEGORY_ID']==3) $category_name.='Fabric, ';
			else if ($row['ITEM_CATEGORY_ID']==12 || $row['ITEM_CATEGORY_ID']==24) $category_name.='Dyeing, ';
			else if ($row['ITEM_CATEGORY_ID']==25) $category_name.='Accessories, ';
			else $category_name.=$item_category[$row['ITEM_CATEGORY_ID']].', ';
		}
	}
	$category_name=chop($category_name,", ");
	
	$sql_lcSc=sql_select("SELECT a.IMPORT_MST_ID, b.contract_no as LC_SC_NO,b.contract_value as lC_VALUE, b.contract_date as LC_SC_DATE, b.BUYER_NAME, a.IS_LC_SC
	from  com_btb_export_lc_attachment a, com_sales_contract b
	where a.lc_sc_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.is_lc_sc=1 and a.import_mst_id=$data
	union all 
	select a.IMPORT_MST_ID, b.export_lc_no as LC_SC_NO,b.lc_value as lC_VALUE, b.lc_date as LC_SC_DATE, b.BUYER_NAME, a.IS_LC_SC 
	from com_btb_export_lc_attachment a, com_export_lc b
	where a.lc_sc_id=b.id and a.status_active=1 and a.is_deleted=0 and a.is_lc_sc=0 and b.status_active=1 and b.is_deleted=0 and a.import_mst_id=$data");	
	$lc_sc_arr=array();
	foreach($sql_lcSc as $row)
	{
		$lc_sc_arr[$row['IMPORT_MST_ID']]['LC_SC_NO'].=$row['LC_SC_NO'].', ';
		$lc_sc_arr[$row['IMPORT_MST_ID']]['lC_VALUE'].=$row[csf('lC_VALUE')].', ';
		$lc_sc_arr[$row['IMPORT_MST_ID']]['LC_SC_DATE'].=change_date_format($row['LC_SC_DATE']).', ';
		// $lc_sc_arr[$row['IMPORT_MST_ID']]['BUYER_NAME'].= $buyer_arr[$row['BUYER_NAME']].', ';
	}
	$lc_sc_no    = rtrim($lc_sc_arr[$btb_id]['LC_SC_NO'],', ');
	$lc_sc_value = rtrim($lc_sc_arr[$btb_id]['lC_VALUE'],', ');
	$lc_sc_date  = rtrim($lc_sc_arr[$btb_id]['LC_SC_DATE'],', ');
	// $lc_sc_buyer = rtrim($lc_sc_arr[$btb_id]['BUYER_NAME'],', ');
	$sql_lib_location=sql_select("SELECT ID, LOCATION_NAME,CONTACT_NO,EMAIL,COUNTRY_ID,REMARK from lib_location where company_id='".$sql_btb_res[0]['IMPORTER_ID']."' and status_active=1 ");
	?>
	<div style="width:750;">
		<h1 align="center" style="font-size:300%; margin-bottom:0;padding-bottom:0;"><?=$importer_name;?></h1>
		<div align="center" style="border:1px solid;">
			<? echo 'Factory: '.$sql_lib_location[0]['LOCATION_NAME'].',</br>'.$country_library[$sql_lib_location[0]['COUNTRY_ID']].', Phone: '.$sql_lib_location[0]['CONTACT_NO'].', Fax# '.$sql_lib_location[0]['REMARK'].', E-mail: '.$sql_lib_location[0]['EMAIL']; ?> 
		</div>
	</div>
    <table width="750" cellpadding="0" align="left" cellspacing="0" border="0">
		<tr><td colspan="4" height="50"></td></tr>
		<tr>
			<td width="25"></td>
			<td  colspan="3">Ref. No.: <? echo $ref."/".$bank_short_name."/".$lc_sc_no ;?></td>
		</tr>
		<tr>
			<td width="25"> </td>
			<td width="675" colspan="3">Date: <? echo date('d-m-Y');?><? //echo $lc_sc_date; ?></td>

		</tr>
		<tr><td colspan="4" height="50"></td></tr>
		<tr>
			<td width="25" valign="top"></td>

			<td width="675" colspan="3">The Manager </br>
			<? echo $bank_name;?></br>
			<? echo $bank_branch;?></br>
			<? echo $bank_address;?></br>
			</td>
		</tr>
        <tr><td colspan="4" height="50">&nbsp;</td></tr>
		<tr>
			<td width="25"></td>
			<td width="675" colspan="3">Sub: Issuance of FTT for <? echo $currency_name .''.number_format($lc_value,2); ?> In favor of <? echo $supplier_name; ?>.
			</td>
		</tr>
        <tr><td colspan="4">&nbsp;</td></tr>
		<tr>
			<td width="25"></td>
			<td width="675" colspan="3">Dear Sir,</td>
		</tr>
		<tr>
			<td width="25"></td>
			<td width="675" colspan="3">You are requested to issue FTT for  <? echo $currency_name .''.number_format($lc_value,2); ?> (US Dollar <? echo number_to_words(number_format($lc_value,2, '.', ''), 'USD', 'Cent'); ?> ) only in favor of <? echo $supplier_name; ?> Bank Name: <? echo $advising_bank." and ".$advising_bank_issue; ?> for supply of garments accessories as per Invoice No: <? echo $pi_number; ?> Date: <? echo $lc_sc_date; ?> under Export L/C / Contract no.: <? echo $lc_sc_no;?> DT.: <? echo $lc_sc_date; ?> <? echo $currency_name .''.number_format($lc_sc_value,2); ?> & LCA no. <? echo $lca_no;?> by debating our ERQ A/C No: <strong><? echo $erq_no; ?></strong> at your Branch. Charges if any by debating our CD A/C No: <strong><? echo $cd_no; ?></strong>, H.S Code: <? echo $hs_code; ?>.
			</td>
		</tr>
        <tr><td colspan="4"  height="50">&nbsp;</td></tr>
        <tr>
			<td width="25"></td>
			<td width="675" colspan="3"><strong>ALL Bank charges on Our Account. </strong></td>
		</tr>
        <tr><td colspan="4"  height="50">&nbsp;</td></tr>
        <tr>
			<td width="25"></td>
			<td width="675" colspan="3">Your co-operation in this matter will be highly appreciated.</td>
		</tr>

        <tr><td colspan="4"  height="100">&nbsp;</td></tr>
        <tr>
			<td width="25"></td>
			<td width="675" colspan="3">Thanking You.</br>
			Yours faithfully,
			</br></br>
			<strong>For <?=$importer_name;?></strong>
			</td>
		</tr>
        <tr><td colspan="4"  height="100">&nbsp;</td></tr>
        <tr>
			<td width="25"></td>
			<td width="675" colspan="3">Authorized Signature</br>
			Encloses :-</br>
			01. Proforma Invoice.</br>
			02. LCAF Form no.</br>
			</td>
		</tr>
	</table>
	<?
	exit();
}

if ($action==='btb_req')
{
	$company_arr = return_library_array('SELECT id, company_name FROM lib_company','id','company_name');
	$country_arr = return_library_array('SELECT id, country_name FROM lib_country','id','country_name');
	$company_arr_short=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	// $back_sql=sql_select("select a.ID, a.SWIFT_CODE, b.ACCOUNT_TYPE, b.ACCOUNT_NO from LIB_BANK a, LIB_BANK_ACCOUNT b where a.id=b.account_id");

	// $bank_data=array();
	// foreach($back_sql as $row)
	// {
	// 	$bank_data[$row["ID"]]["SWIFT_CODE"]=$row["SWIFT_CODE"];
	// 	if($row["ACCOUNT_TYPE"]==6) $bank_data[$row["ID"]]["ACCOUNT_NO_ERQ"]=$row["ACCOUNT_NO"];
	// 	if($row["ACCOUNT_TYPE"]==10) $bank_data[$row["ID"]]["ACCOUNT_NO_CD"]=$row["ACCOUNT_NO"];
	// }
	$supplier_sql = sql_select('SELECT id, supplier_name, address_1 FROM lib_supplier');
	foreach($supplier_sql as $row)
	{
		$supplier_arr[$row[csf("id")]]["supplier_name"]=$row[csf("supplier_name")];
		$supplier_arr[$row[csf("id")]]["address_1"]=$row[csf("address_1")];
	}
	$sql_bank_info = sql_select("SELECT ID, BANK_NAME, BRANCH_NAME, ADDRESS from lib_bank ");
	$bank_dtls_arr=array();
	foreach($sql_bank_info as $row)
	{
		$bank_dtls_arr[$row['ID']]['BANK_NAME']=$row['BANK_NAME'];
		$bank_dtls_arr[$row['ID']]['BRANCH_NAME']=$row['BRANCH_NAME'];
		$bank_dtls_arr[$row['ID']]['ADDRESS']=$row['ADDRESS'];
	}
	$sql_btb="SELECT ID, LC_DATE, APPLICATION_DATE, IMPORTER_ID, ITEM_CATEGORY_ID, ISSUING_BANK_ID, CURRENCY_ID, LC_VALUE, MATURITY_FROM_ID, LAST_SHIPMENT_DATE, LC_EXPIRY_DATE, TENOR, PI_ID,  INCO_TERM_PLACE, ORIGIN, COVER_NOTE_NO, COVER_NOTE_DATE, PI_VALUE,SUPPLIER_ID, MARGIN,REMARKS
	from com_btb_lc_master_details
	where id=$data and is_deleted=0 and status_active=1";
	// INCO_TERM_ID,LC_TYPE_ID, ADVISING_BANK, ADVISING_BANK_ADDRESS, LCA_NO,BTB_SYSTEM_ID, BTB_PREFIX_NUMBER,
	$sql_btb_res=sql_select($sql_btb);
	$btb_id = $sql_btb_res[0]['ID'];
	$lc_value = $sql_btb_res[0]['LC_VALUE'];
	$last_ship_date = $sql_btb_res[0]['LAST_SHIPMENT_DATE'];
	$expire_date = $sql_btb_res[0]['LC_EXPIRY_DATE'];
	$importer_name = $company_arr[$sql_btb_res[0]['IMPORTER_ID']];
	$origin_name = $country_arr[$sql_btb_res[0]['ORIGIN']];
	$cover_note_no = $sql_btb_res[0]['COVER_NOTE_NO'];
	$cover_note_date = $sql_btb_res[0]['COVER_NOTE_DATE'];
	$bank_name=$bank_dtls_arr[$sql_btb_res[0]['ISSUING_BANK_ID']]['BANK_NAME'];
	$bank_branch=$bank_dtls_arr[$sql_btb_res[0]['ISSUING_BANK_ID']]['BRANCH_NAME'];
	$bank_address=	$bank_dtls_arr[$sql_btb_res[0]['ISSUING_BANK_ID']]['ADDRESS'];
	$lc_type_value= $lc_type[$sql_btb_res[0]['LC_TYPE_ID']];
	$currency_name      = $currency[$sql_btb_res[0]["CURRENCY_ID"]];
	$ref				= $company_arr_short[$sql_btb_res[0]["IMPORTER_ID"]];
	$application_date	= $sql_btb_res[0]["APPLICATION_DATE"];
	$pi_value	= $sql_btb_res[0]["PI_VALUE"];
	$margin_deposit	= $sql_btb_res[0]["MARGIN"];
	$remarks	= $sql_btb_res[0]["REMARKS"];
	$supplier_name = $supplier_arr[$sql_btb_res[0]['SUPPLIER_ID']]["supplier_name"];
	// $inco_term_place = $incoterm[$sql_btb_res[0]['INCO_TERM_ID']]." ".$sql_btb_res[0]['INCO_TERM_PLACE'];
	// $supplier_address = $supplier_arr[$sql_btb_res[0]['SUPPLIER_ID']]["address_1"];
	// $issuing_bank_id = $sql_btb_res[0]['ISSUING_BANK_ID'];
	// $swift_code=$bank_data[$issuing_bank_id]["SWIFT_CODE"];
	// $erq_no=$bank_data[$issuing_bank_id]["ACCOUNT_NO_ERQ"];
	// $cd_no=$bank_data[$issuing_bank_id]["ACCOUNT_NO_CD"];
	// $advising_bank		= $sql_btb_res[0]['ADVISING_BANK'];
	// $advising_bank_issue = $sql_btb_res[0]['ADVISING_BANK_ADDRESS'];
	// $lca_no = $sql_btb_res[0]['LCA_NO'];
	// $ref_no				= $sql_btb_res[0]["BTB_PREFIX_NUMBER"];
	

	$pi_ids=$sql_btb_res[0]['PI_ID'];
	$sql_pi_res=sql_select("SELECT a.PI_NUMBER, a.PI_DATE, a.ITEM_CATEGORY_ID, a.HS_CODE, a.PI_VALIDITY_DATE, listagg(cast(b.HS_CODE as varchar(4000)),',') within group(order by b.id) as ITEM_HS_CODE, sum(b.QUANTITY) as QUANTITY 
	from com_pi_master_details a, com_pi_item_details b 
	where a.id=b.pi_id and a.status_active=1 and b.status_active=1 and a.id in($pi_ids)
	group by a.PI_NUMBER, a.PI_DATE, a.ITEM_CATEGORY_ID, a.HS_CODE, a.PI_VALIDITY_DATE");

	$category_name='';
	$pi_data_arr=array();
	foreach($sql_pi_res as $row)
	{
		if($pi_number!=''){$pi_number.=', '.$row['PI_NUMBER'];}else{$pi_number.=$row['PI_NUMBER'];};
		if($pi_date!=''){$pi_date.=', '.change_date_format($row['PI_DATE']);}else{$pi_date.=change_date_format($row['PI_DATE']);}
		if($hs_code!=''){$hs_code.=', '.$row['HS_CODE'];}else{$hs_code.=$row['HS_CODE'];}	
		if($row['ITEM_HS_CODE']!="") $item_hs_code.=$row['ITEM_HS_CODE'].', ';
		$pi_qnty+=$row['QUANTITY'];
		if($category_check[$row['ITEM_CATEGORY_ID']]=="")
		{
			$category_check[$row['ITEM_CATEGORY_ID']]=$row['ITEM_CATEGORY_ID'];
			if ($row['ITEM_CATEGORY_ID']==2 || $row['ITEM_CATEGORY_ID']==3) $category_name.='Fabric, ';
			else if ($row['ITEM_CATEGORY_ID']==12 || $row['ITEM_CATEGORY_ID']==24) $category_name.='Dyeing, ';
			else if ($row['ITEM_CATEGORY_ID']==25) $category_name.='Accessories, ';
			else $category_name.=$item_category[$row['ITEM_CATEGORY_ID']].', ';
		}
	}
	$category_name=chop($category_name,", ");
	
	$sql_lcSc=sql_select("SELECT a.LC_SC_ID, a.IMPORT_MST_ID, b.contract_no as LC_SC_NO, b.contract_date as LC_SC_DATE, b.contract_value as LC_SC_VALUE, b.BUYER_NAME,b.bank_file_no, a.IS_LC_SC, sum(c.ATTACHED_QNTY) as LC_SC_ATTACHED_QNTY, b.LAST_SHIPMENT_DATE, b.EXPIRY_DATE
	from  com_btb_export_lc_attachment a, com_sales_contract b, com_sales_contract_order_info c
	where a.lc_sc_id=b.id and b.id=c.com_sales_contract_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.is_lc_sc=1 and a.import_mst_id=$data group by a.LC_SC_ID,a.IMPORT_MST_ID,b.contract_no, b.contract_date, b.contract_value,b.BUYER_NAME,b.bank_file_no, a.IS_LC_SC, b.last_shipment_date, b.expiry_date
	union all 
	select a.LC_SC_ID,a.IMPORT_MST_ID, b.export_lc_no as LC_SC_NO, b.lc_date as LC_SC_DATE, b.lc_value as LC_SC_VALUE, b.BUYER_NAME,b.bank_file_no, a.IS_LC_SC ,sum(c.ATTACHED_QNTY) as LC_SC_ATTACHED_QNTY, b.LAST_SHIPMENT_DATE, b.EXPIRY_DATE
	from com_btb_export_lc_attachment a, com_export_lc b,com_export_lc_order_info c
	where a.lc_sc_id=b.id and a.status_active=1 and a.is_deleted=0 and a.is_lc_sc=0 and b.status_active=1 and b.is_deleted=0 and a.import_mst_id=$data group by a.LC_SC_ID,a.IMPORT_MST_ID,b.export_lc_no, b.lc_date, b.lc_value,b.BUYER_NAME,b.bank_file_no, a.IS_LC_SC, b.last_shipment_date, b.expiry_date");

	$lc_sc_arr=array();
	foreach($sql_lcSc as $row)
	{
		$lc_sc_arr[$row['IMPORT_MST_ID']]['LC_SC_NO'].=$row['LC_SC_NO'].', ';
		$lc_sc_arr[$row['IMPORT_MST_ID']]['LC_SC_DATE'].=change_date_format($row['LC_SC_DATE']).', ';
		$lc_sc_arr[$row['IMPORT_MST_ID']]['LC_SC_VALUE'] =$row['LC_SC_VALUE'];
		$lc_sc_arr[$row['IMPORT_MST_ID']]['bank_file_no'].=$row['BANK_FILE_NO'].', ';
		$lc_sc_arr[$row['IMPORT_MST_ID']]['BUYER_NAME'].= $row['BUYER_NAME'];
		$lc_sc_arr[$row['IMPORT_MST_ID']]['LC_SC_ATTACHED_QNTY']= $row['LC_SC_ATTACHED_QNTY'];
		$lc_sc_id= $row['LC_SC_ID'];
		if($lc_sc_value!=''){$lc_sc_value .= ", ".number_format($lc_sc_arr[$btb_id]['LC_SC_VALUE'],2);}else{$lc_sc_value =number_format($lc_sc_arr[$btb_id]['LC_SC_VALUE'],2);}
		$lc_sc_arr[$row['IMPORT_MST_ID']]['LAST_SHIPMENT_DATE'].=change_date_format($row['LAST_SHIPMENT_DATE']).', ';
		if($row['EXPIRY_DATE']!=""){$lc_sc_arr[$row['IMPORT_MST_ID']]['EXPIRY_DATE'].=change_date_format($row['EXPIRY_DATE']).', ';}
	}
	// echo "<pre>";print_r($lc_sc_arr);die;

	$lc_sc_no   = rtrim($lc_sc_arr[$btb_id]['LC_SC_NO'],', ');
	$lc_sc_date = rtrim($lc_sc_arr[$btb_id]['LC_SC_DATE'],', ');
	$lc_sc_last_shipment_date= rtrim($lc_sc_arr[$btb_id]['LAST_SHIPMENT_DATE'],', ');
	$lc_sc_expiry_date = rtrim($lc_sc_arr[$btb_id]['EXPIRY_DATE'],', ');

	$lc_sc_bank_file = rtrim($lc_sc_arr[$btb_id]['bank_file_no'],', ');
	$lc_sc_buyer = rtrim($lc_sc_arr[$btb_id]['BUYER_NAME']);
	$lc_sc_attached_qnty = rtrim($lc_sc_arr[$btb_id]['LC_SC_ATTACHED_QNTY']);
	$buyer_country_sql=sql_select("select id, country_id from lib_buyer where id=$lc_sc_buyer");
	$buyer_country_name=$country_arr[$buyer_country_sql[0]["COUNTRY_ID"]];

	$all_mst_id_sql=sql_select("select id,import_mst_id from com_btb_export_lc_attachment where lc_sc_id=$lc_sc_id and is_deleted=0 and status_active=1");
	// echo "<pre>";print_r($sql);die;
	$mst_id='';
	foreach($all_mst_id_sql as $row){
		if($mst_id !=''){$mst_id .= " ,".$row['IMPORT_MST_ID'];}else{$mst_id .= $row['IMPORT_MST_ID'];}
	}
	$sql_lc_total=sql_select("SELECT sum(LC_VALUE) as total_lc_value from com_btb_lc_master_details where id in($mst_id) and is_deleted=0 and status_active=1");
	$total_lc_value=$sql_lc_total[0]['TOTAL_LC_VALUE'];
	?>

    <table width="750" cellpadding="0" align="left" cellspacing="0" border="0">
		<tr><td colspan="4" height="150"></td></tr>
		<tr>
			<td width="25"> </td>
			<td width="675" colspan="3">Date: <? echo $application_date;?></td>
		</tr>
		<tr>
		<td width="25"></td>
		<td  colspan="3">Ref. No.: <? echo $lc_sc_bank_file;?></td>
		</tr>
		<tr><td colspan="4" height="50"></td></tr>
		<tr>
			<td width="25" valign="top"></td>
			<td width="675" colspan="3">To,</br>
			The Manager, </br>
			<? echo $bank_name;?></br>
			<? echo $bank_branch;?></br>
			<? echo $bank_address;?></br>
			</td>
		</tr>
        <tr><td colspan="4" height="50">&nbsp;</td></tr>
		<tr>
			<td width="25"></td>
			<td width="675" colspan="3">Subject: Opening of Back to Back L/C for <strong><? echo $currency_name .' '.number_format($pi_value,2); ?></strong>  in favor of <strong> <? echo $supplier_name; ?></strong><? echo " (".$category_name."-".$origin_name.") "; ?>Under Export Contract no. <strong><? echo $lc_sc_no;?> </strong>Dated: <? echo $lc_sc_date; ?>, value <? echo $currency_name .' '.$lc_sc_value; ?> Quantity <? echo number_format($lc_sc_attached_qnty,0)." ".$remarks; ?> at Valid for shipment <? echo $lc_sc_last_shipment_date;?> and expiry <? echo $lc_sc_expiry_date;?> for export to <? echo $buyer_country_name; ?>.
			</td>
		</tr>
        <tr><td colspan="4">&nbsp;</td></tr>
		<tr>
			<td width="25"></td>
			<td width="675" colspan="3" valign="middle" height="30"><strong>Dear Sir,</strong> </td>
		</tr>
		<tr>
			<td width="25"></td>
			<td width="675" colspan="3">
			<p>We enclosed herewith one set of L/C application duly filled in signed along with proforma invoice accepted by us. Insurance cover note with money receipt & other documents for opening of back to back L/C.</p>
			<p>The proposed import is fully covered by current import policy order & exchange Control Regulations.</p>
			<p>We shall remain bond to follow the govt. orders, directives governing & controlling import under Back to Back L/C.</p>
			<p>We shall be held solely responsible for any miss declaration regarding the commodity and entitlement to import under the proposed L/C.</p>
			<p>We confirm that the export L/C’s value covers the required back to back L/C value in <? echo $margin_deposit; ?>% margin. Total L/C value <strong><? echo $currency_name .' '.number_format($total_lc_value,2).'.'; ?></strong></p>
			</td>
		</tr>
        <tr><td colspan="4"  height="100">&nbsp;</td></tr>
        <tr>
			<td width="25"></td>
			<td width="675" colspan="3">
			Thanking You	</br>	
			Yours faithfully,
			</td>
		</tr>
	</table>
	<?
	exit();
}

if($action=="btb_import_lc_letter6")
{
	//echo load_html_head_contents("BTB Letter","../../", 1, 1, $unicode,'','');
	$currency_sign_arr=array(1=>'৳',2=>'$',3=>'€',4=>'€',5=>'$',6=>'£',7=>'¥');
	$sql_bank_info=sql_select("select id, contact_person, bank_name, branch_name, address,designation from lib_bank ");
	foreach($sql_bank_info as $row)
	{
		$bank_dtls_arr[$row[csf("id")]]["contact_person"]=$row[csf("contact_person")];
		$bank_dtls_arr[$row[csf("id")]]["bank_name"]=$row[csf("bank_name")];
		$bank_dtls_arr[$row[csf("id")]]["branch_name"]=$row[csf("branch_name")];
		$bank_dtls_arr[$row[csf("id")]]["address"]=$row[csf("address")];
		$bank_dtls_arr[$row[csf("id")]]["designation"]=$row[csf("designation")];
	}

	// echo $data;die;
	$sql_com="SELECT a.id,a.btb_system_id, a.application_date, a.importer_id, a.item_category_id, a.origin, a.issuing_bank_id, a.currency_id, a.supplier_id, a.lc_value, a.margin,a.lc_date,a.tenor,a.last_shipment_date,a.lc_expiry_date,a.upas_rate,a.cover_note_no,a.cover_note_date, a.lcaf_no,a.pi_id,a.payterm_id,remarks
	from com_btb_lc_master_details a
	where a.id=$data and a.is_deleted = 0 AND a.status_active = 1 group by a.id,a.btb_system_id, a.application_date, a.importer_id, a.item_category_id, a.origin, a.issuing_bank_id, a.currency_id, a.supplier_id, a.lc_value, a.margin,a.lc_date,a.tenor,a.last_shipment_date,a.lc_expiry_date,a.upas_rate,a.cover_note_no,a.cover_note_date, a.lcaf_no,a.pi_id,a.payterm_id,remarks";

	//echo $sql_com;
	$result=sql_select($sql_com);

	$country_array = return_library_array("select id,country_name from lib_country where is_deleted=0","id","country_name");
	$company_name = return_field_value("company_name","lib_company","id=".$result[0][csf("importer_id")],"company_name");
	$supplier_name = return_field_value("supplier_name","lib_supplier","id=".$result[0][csf("supplier_id")],"supplier_name");
	$supplier_address = return_field_value("address_1","lib_supplier","id=".$result[0][csf("supplier_id")],"address_1");
	$country_name = return_field_value("country_name"," lib_country","id=".$result[0][csf("origin")],"country_name");
	$designation = return_field_value("custom_designation","lib_designation","id=".$bank_dtls_arr[$result[0][csf("issuing_bank_id")]]["designation"],"custom_designation");
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1 and master_tble_id='".$result[0][csf("importer_id")]."'",'master_tble_id','image_location');

	$address = sql_select("SELECT id,plot_no,level_no,road_no,block_no,country_id,city,zip_code,email,contact_no from lib_company where id = ".$result[0][csf('importer_id')]." and is_deleted = 0 and status_active = 1 ");
	$company_add=array();
	foreach($address as $row){
		$company_add['plot_no'] = $row[csf('plot_no')];
		$company_add['level_no'] = $row[csf('level_no')];
		$company_add['road_no'] = $row[csf('road_no')];
		$company_add['block_no'] = $row[csf('block_no')];
		$company_add['country_id'] = $row[csf('country_id')];
		$company_add['city'] = $row[csf('city')];
		$company_add['zip_code'] = $row[csf('zip_code')];
	}
	$company_address='';
	if($company_add['plot_no']!=''){$company_address.="Visiting Address : ".$company_add['plot_no'];}
	if($company_add['level_no']!=''){$company_address.=$company_add['level_no'];}
	if($company_add['road_no']!=''){$company_address.=", ".$company_add['road_no'];}
	if($company_add['block_no']!=''){$company_address.=", ".$company_add['block_no'];}
	if($company_add['city']!=''){$company_address.=", ".$company_add['city'];}
	if($company_add['zip_code']!=''){$company_address.=", ".$company_add['zip_code'];}
	if($company_add['country_id']!=''){$company_address.=", ".$country_array[$company_add['country_id']];}
	

	$sql_pi=sql_select("SELECT a.import_mst_id, b.contract_no as lc_sc_no, b.internal_file_no, b.contract_value as lc_sc_val , b.contract_date as sc_lc_date, a.is_lc_sc
	from  com_btb_export_lc_attachment a, com_sales_contract b
	where a.lc_sc_id=b.id and a.status_active=1 and a.is_lc_sc=1 and a.import_mst_id=$data
	union all 
	select a.import_mst_id, b.export_lc_no as lc_sc_no, b.internal_file_no, b.lc_value as lc_sc_val, b.lc_date as sc_lc_date, a.is_lc_sc
	from com_btb_export_lc_attachment a, com_export_lc b
	where a.lc_sc_id=b.id and a.status_active=1 and a.is_lc_sc=0 and a.import_mst_id=$data");
		$sql_lc__arr=array();
		foreach($sql_pi as $row)
		{
			$sql_lc__arr[$row[csf("import_mst_id")]]["lc_sc_no"]=$row[csf("lc_sc_no")].",";
			$sql_lc__arr[$row[csf("import_mst_id")]]["internal_file_no"]=$row[csf("internal_file_no")].",";
			$sql_lc__arr[$row[csf("import_mst_id")]]["lc_sc_val"]=$row[csf("lc_sc_val")].",";
			$sql_lc__arr[$row[csf("import_mst_id")]]["sc_lc_date"]=$row[csf("sc_lc_date")].",";
			// $sql_lc__arr[$row[csf("import_mst_id")]]["contract_date"]=$row[csf("contract_date")].",";
			$sql_lc__arr[$row[csf("import_mst_id")]]["is_lc_sc"]=$row[csf("is_lc_sc")];
		}
		/*echo '<pre>';
		print_r($sql_lc__arr);*/
		$pi_number_arr=sql_select( "SELECT id, pi_number,pi_date,item_category_id from com_pi_master_details where id in(".$result[0][csf("pi_id")].")  AND status_active = 1 AND is_deleted = 0");

		if(count($pi_number_arr)>1){
			foreach($pi_number_arr as $row){
				$pi_numbers .= $row[csf('pi_number')].", ";
				$pi_date .= change_date_format($row[csf('pi_date')]).", ";
			}
			$pi_numbers = chop($pi_numbers,", ");
			$pi_date = chop($pi_date,", ");
			$itemCategory = $item_category[$pi_number_arr[0][csf('item_category_id')]];

		}
		else
		{
			$pi_numbers = $pi_number_arr[0][csf('pi_number')];
			$pi_date = change_date_format($pi_number_arr[0][csf('pi_date')]);
			$itemCategory = $item_category[$pi_number_arr[0][csf('item_category_id')]];

		}
		$factory_arr = sql_select("SELECT id as ID,address as ADDRESS,remark as REMARKS,contact_no as CONTACT_NO,email as EMAIL from lib_location where company_id='".$result[0][csf("importer_id")]."' and is_deleted=0");
		$factory_add=$factory_tel_fax_number=$factory_mobile_number=$factory_email_number='';
		foreach($factory_arr as $factory)
		{
			if($factory_add!='' && $factory['ADDRESS'] !=''){ $factory_add.= ", ".$factory['ADDRESS']; }else{ $factory_add.= $factory['ADDRESS']; }
			if($factory_tel_fax_number!='' && $factory['REMARKS'] !=''){ $factory_tel_fax_number.= ", ".$factory['REMARKS']; }else{ $factory_tel_fax_number.= $factory['REMARKS']; }
			if($factory_mobile_number!='' && $factory['CONTACT_NO'] !=''){ $factory_mobile_number.= ", +88-".$factory['CONTACT_NO']; }else{ $factory_mobile_number.= "+88-".$factory['CONTACT_NO']; }
			if($factory_email_number!='' && $factory['EMAIL'] !=''){ $factory_email_number.= ", ".$factory['EMAIL']; }else{ $factory_email_number.= $factory['EMAIL']; }
		}
	?>

		<style type="text/css">
			#footer {
				margin:100px 0px 0px 25px;
			}
			.a4size {
	           width: 21cm;
	           height: 26.7cm;
	           font-family: Cambria, Georgia, serif;
	        }
	        @media print {
	        .a4size{ font-family: Cambria;font-size: 18px;margin: 30px 100PX 54px 25px;size: A4 portrait;
	            }
			#footer {
				position: absolute;
				bottom: 0;
				width: 100%;
				}
	        }
		</style>
		<div class="a4size">
		    <table width="794" cellpadding="0" cellspacing="0" border="0" >
		        <tr>
		            <td width="25" ></td>
		            <td width="650" align="center">
					<img  src="../../<? echo $imge_arr[$result[0][csf('importer_id')]]; ?>" height='75' width='300' /> </td>
		            <td width="25" ></td>
		        </tr>
				<tr>
		        	<td colspan="3" height="30"></td>
		        </tr>
		        <tr>
		            <td width="25" ></td>
		            <td width="650" align="left"><? echo date("l, d F, Y",strtotime($result[0][csf("application_date")])); ?> </td>
		            <td width="25" ></td>
		        </tr>
		        <tr>
		        	<td colspan="3" height="20">&nbsp;</td>
		        </tr>
		        <br>
		        <tr>
		            <td width="25"></td>
		            <td width="650" align="left">To</td>
		            <td width="25" ></td>
		        </tr>
		        <tr>
		            <td width="25" ></td>
		            <td width="650" align="left">
		            <?
						echo 'The '.$designation."<br>".$bank_dtls_arr[$result[0][csf("issuing_bank_id")]]["bank_name"]."<br>".$bank_dtls_arr[$result[0][csf("issuing_bank_id")]]["address"];
					?>
		            </td>
		            <td width="25" ></td>
		        </tr>
		        <tr>
		        	<td colspan="3" height="30"></td>
		        </tr>
		        <tr>
		            <td width="25" ></td>
		            <td width="650" align="left">
		            <u>Sub – Opening BBLC <? echo $currency[$result[0][csf("currency_id")]]." ".$currency_sign_arr[$result[0][csf("currency_id")]].":&nbsp;".def_number_format($result[0][csf("lc_value")],2); ?> against SC/LC No # <? echo chop($sql_lc__arr[$result[0][csf("id")]]["lc_sc_no"],","); ?>, Date <? echo change_date_format(chop($sql_lc__arr[$result[0][csf("id")]]["sc_lc_date"],","));?>, Amount – <? echo $currency[$result[0][csf("currency_id")]]." ".$currency_sign_arr[$result[0][csf("currency_id")]]."&nbsp;".def_number_format(chop($sql_lc__arr[$result[0][csf("id")]]["lc_sc_val"],","),2); ?></u>
					 .
		            </td>
		            <td width="25" ></td>
		        </tr>
		        <tr>
		        	<td colspan="3" height="20"></td>
		        </tr>
		        <tr>
		        	<td colspan="3" height="20"></td>
		        </tr>
		        <tr>
		            <td width="25" ></td>
		            <td width="650" align="left"> Dear Sir, </td>
		            <td width="25" ></td>
		        </tr>
				<tr>
		        	<td colspan="3" height="15"></td>
		        </tr>
		        <tr>
		            <td width="25" ></td>
		            <td width="650" align="left">
		            We request you to open back to back L/C <? echo $currency[$result[0][csf("currency_id")]]." ".$currency_sign_arr[$result[0][csf("currency_id")]].":&nbsp;".def_number_format($result[0][csf("lc_value")],2); ?> as per enclosed P/I against the above L/C submitted to your counter a lien for <? echo $itemCategory;?> from – <? echo $supplier_name;?>, <?echo $supplier_address;?> for the manufacturing of garments from our factory –
		            </td>
		            <td width="25" ></td>
		        </tr>
				<tr>
		        	<td colspan="3" height="15"></td>
		        </tr>
		        <tr>
		            <td width="25" ></td>
		            <td width="650" align="left">
					Enclosed P/I # <? echo $pi_numbers;?> Date – <? echo $pi_date;?> with below terms & conditions –
		            </td>
		            <td width="25" ></td>
		        </tr>
		    </table>
		    <table width="794" cellpadding="0" cellspacing="0" border="0">
		        <tr>
		        	<td colspan="3" height="15"></td>
		        </tr>
		         <tr>
		            <td width="70" >&nbsp;</td>
		            <td width="30"># </td>
		            <td width="590"><? if($result[0][csf("payterm_id")]==2){echo number_format($result[0][csf("tenor")])." days from the date of Acceptance.";}else if($result[0][csf("payterm_id")]==1){echo "At sight against irrevocable L/C.";}  ?></td>
		        </tr>
		        <tr>
		            <td width="70" >&nbsp;</td>
		            <td width="30"># </td>
		            <td width="590">Maturity date will be counted from the date of acceptance by Managing Director of <? echo $company_name;?></td>
		        </tr>
		        <tr>
		            <td width="70" >&nbsp;</td>
		            <td width="30"># </td>
		            <td width="590">Invoice and Delivery Challan should be enclosed during Bank Negotiation and all documents authenticate signature by Managing Director of <? echo $company_name;?></td>
		        </tr>
		        <?
					if($result[0][csf("item_category_id")]==1)
					{
						?>
							<tr>
								<td width="70" >&nbsp;</td>
								<td width="30"># </td>
								<td width="590">Cash Incentive will be drawn by <? echo $company_name;?></td>
							</tr>
						<?
					}
					if($result[0][csf("remarks")]!='')
					{
						?>
							<tr>
								<td width="70" >&nbsp;</td>
								<td width="30"># </td>
								<td width="590"><? echo $result[0][csf("remarks")];?></td>
							</tr>
						<?
					}
			    ?>
		        <tr>
		        	<td colspan="3" height="30"></td>
		        </tr>

		    </table>
		    <table width="794" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td colspan="3" height="15"></td>
				</tr>
		        <tr>
		            <td width="25" ></td>
		            <td width="650" align="left">
		            Thanking You.<br>
		            </td>
		            <td width="25" ></td>
		        </tr>
		        <tr>
		            <td width="25" ></td>
		            <td width="650" align="left">
		            <? echo $company_name;?><br>
		            </td>
		            <td width="25" ></td>
		        </tr>
		        <tr>
		        	<td colspan="3" height="190"></td>
		        </tr>
		        <tr>
		            <td width="25" ></td>
		            <td width="650" align="left">
					<div style="width:180px;border-top: 2px solid;">Authorized Signature</div>
		            </td>
		            <td width="25" ></td>
		        </tr>
		    </table>
			<div id="footer"><? echo "Factory Address : ".$factory_add."<br>Tel - ".$factory_tel_fax_number.", Mobile - ".$factory_mobile_number."<br>E-mail - ".$factory_email_number;?>
			</div>
		</div>

    <?
	exit();
}

if($action=="btn_print_letter_chem")
{
	//echo load_html_head_contents("BTB Letter","../../", 1, 1, $unicode,'','');
	$back_sql=sql_select("select a.ID, b.ACCOUNT_TYPE, b.ACCOUNT_NO from LIB_BANK a, LIB_BANK_ACCOUNT b where a.id=b.account_id");

	$bank_data=array();
	foreach($back_sql as $row)
	{
		if($row["ACCOUNT_TYPE"]==6) $bank_data[$row["ID"]]["ACCOUNT_NO_ERQ"]=$row["ACCOUNT_NO"];
	}
	$currency_sign_arr=array(1=>'৳',2=>'$',3=>'€',4=>'€',5=>'$',6=>'£',7=>'¥');
	$sql_bank_info=sql_select("select id, contact_person, bank_name, branch_name, address,designation from lib_bank ");
	foreach($sql_bank_info as $row)
	{
		$bank_dtls_arr[$row[csf("id")]]["contact_person"]=$row[csf("contact_person")];
		$bank_dtls_arr[$row[csf("id")]]["bank_name"]=$row[csf("bank_name")];
		$bank_dtls_arr[$row[csf("id")]]["branch_name"]=$row[csf("branch_name")];
		$bank_dtls_arr[$row[csf("id")]]["address"]=$row[csf("address")];
		$bank_dtls_arr[$row[csf("id")]]["designation"]=$row[csf("designation")];
	}

	// echo $data;die;
	$sql_com="SELECT a.id,a.btb_system_id, a.application_date, a.importer_id, a.item_category_id, a.origin, a.issuing_bank_id, a.currency_id, a.supplier_id, a.lc_value, a.margin,a.lc_date,a.tenor,a.last_shipment_date,a.lc_expiry_date,a.upas_rate,a.cover_note_no,a.cover_note_date, a.lcaf_no,a.pi_id,a.tenor,a.margin as MARGIN
	from com_btb_lc_master_details a
	where a.id=$data and a.is_deleted = 0 AND a.status_active = 1 group by a.id,a.btb_system_id, a.application_date, a.importer_id, a.item_category_id, a.origin, a.issuing_bank_id, a.currency_id, a.supplier_id, a.lc_value, a.margin,a.lc_date,a.tenor,a.last_shipment_date,a.lc_expiry_date,a.upas_rate,a.cover_note_no,a.cover_note_date, a.lcaf_no,a.pi_id";

	//echo $sql_com;
	$result=sql_select($sql_com);

	$country_array = return_library_array("select id,country_name from lib_country where is_deleted=0","id","country_name");
	$company_name = return_field_value("company_name","lib_company","id=".$result[0][csf("importer_id")],"company_name");
	$supplier_name = return_field_value("supplier_name","lib_supplier","id=".$result[0][csf("supplier_id")],"supplier_name");
	$supplier_address = return_field_value("address_1","lib_supplier","id=".$result[0][csf("supplier_id")],"address_1");
	$country_name = return_field_value("country_name"," lib_country","id=".$result[0][csf("origin")],"country_name");
	$designation = return_field_value("custom_designation","lib_designation","id=".$bank_dtls_arr[$result[0][csf("issuing_bank_id")]]["designation"],"custom_designation");
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1 and master_tble_id='".$result[0][csf("importer_id")]."'",'master_tble_id','image_location');

	$address = sql_select("SELECT id,plot_no,level_no,road_no,block_no,country_id,city,zip_code,email,contact_no from lib_company where id = ".$result[0][csf('importer_id')]." and is_deleted = 0 and status_active = 1 ");
	$company_add=array();
	foreach($address as $row){
		$company_add['plot_no'] = $row[csf('plot_no')];
		$company_add['level_no'] = $row[csf('level_no')];
		$company_add['road_no'] = $row[csf('road_no')];
		$company_add['block_no'] = $row[csf('block_no')];
		$company_add['country_id'] = $row[csf('country_id')];
		$company_add['city'] = $row[csf('city')];
		$company_add['zip_code'] = $row[csf('zip_code')];
	}
	$company_address='';
	if($company_add['plot_no']!=''){$company_address.="Visiting Address : ".$company_add['plot_no'];}
	if($company_add['level_no']!=''){$company_address.=$company_add['level_no'];}
	if($company_add['road_no']!=''){$company_address.=", ".$company_add['road_no'];}
	if($company_add['block_no']!=''){$company_address.=", ".$company_add['block_no'];}
	if($company_add['city']!=''){$company_address.=", ".$company_add['city'];}
	if($company_add['zip_code']!=''){$company_address.=", ".$company_add['zip_code'];}
	if($company_add['country_id']!=''){$company_address.=", ".$country_array[$company_add['country_id']];}
	

	$sql_pi=sql_select("SELECT a.import_mst_id, b.contract_no as lc_sc_no, b.internal_file_no, b.contract_value as lc_sc_val , c.lc_date, b.contract_date, a.is_lc_sc
	from  com_btb_export_lc_attachment a, com_sales_contract b, com_export_lc c
	where a.lc_sc_id=c.id and a.lc_sc_id=b.id and a.status_active=1 and a.is_lc_sc=1 and a.import_mst_id=$data
	union all 
	select a.import_mst_id, b.export_lc_no as lc_sc_no, b.internal_file_no, b.lc_value as lc_sc_val, b.lc_date, null as lc_null_date, a.is_lc_sc
	from com_btb_export_lc_attachment a, com_export_lc b
	where a.lc_sc_id=b.id and a.status_active=1 and a.is_lc_sc=0 and a.import_mst_id=$data");
		$sql_lc__arr=array();
		foreach($sql_pi as $row)
		{
			$sql_lc__arr[$row[csf("import_mst_id")]]["lc_sc_no"]=$row[csf("lc_sc_no")].",";
			$sql_lc__arr[$row[csf("import_mst_id")]]["internal_file_no"]=$row[csf("internal_file_no")].",";
			$sql_lc__arr[$row[csf("import_mst_id")]]["lc_sc_val"]=$row[csf("lc_sc_val")].",";
			$sql_lc__arr[$row[csf("import_mst_id")]]["lc_date"]=$row[csf("lc_date")].",";
			$sql_lc__arr[$row[csf("import_mst_id")]]["contract_date"]=$row[csf("contract_date")].",";
			$sql_lc__arr[$row[csf("import_mst_id")]]["is_lc_sc"]=$row[csf("is_lc_sc")];
		}
		/*echo '<pre>';
		print_r($sql_lc__arr);*/
		$pi_number_arr=sql_select( "SELECT id, pi_number,pi_date,item_category_id from com_pi_master_details where id in(".$result[0][csf("pi_id")].")  AND status_active = 1 AND is_deleted = 0");

		if(count($pi_number_arr)>1){
			foreach($pi_number_arr as $row){
				$pi_numbers .= $row[csf('pi_number')].", ";
				$pi_date .= change_date_format($row[csf('pi_date')]).", ";
			}
			$pi_numbers = chop($pi_numbers,", ");
			$pi_date = chop($pi_date,", ");
			$itemCategory = $item_category[$pi_number_arr[0][csf('item_category_id')]];

		}
		else
		{
			$pi_numbers = $pi_number_arr[0][csf('pi_number')];
			$pi_date = change_date_format($pi_number_arr[0][csf('pi_date')]);
			$itemCategory = $item_category[$pi_number_arr[0][csf('item_category_id')]];

		}
		$factory_arr = sql_select("select id,address from lib_location where company_id='".$result[0][csf("importer_id")]."' and is_deleted=0");
		$factory_add='';
		foreach($factory_arr as $factory){
			if($factory_add!='' && $factory[csf('address')] !=''){ $factory_add.= ", ".$factory[csf('address')]; }else{ $factory_add.= $factory[csf('address')]; }
		}
		$company_name=str_replace(" n "," & ",$company_name);

	?>

		<style type="text/css">
			.a4size {
	           width: 21cm;
	           height: 26.7cm;
	           font-family: Cambria, Georgia, serif;
	        }
	        @media print {
	        .a4size{ font-family: Cambria;font-size: 18px;margin: 30px 100PX 54px 25px;size: A4 portrait;
	            }
	        }
		</style>
		<div class="a4size">
		    <table width="794" cellpadding="0" cellspacing="0" border="0" >
				<tr>
		        	<td colspan="3" height="120"></td>
		        </tr>
		        <tr>
		            <td width="25" ></td>
		            <td width="650" align="left"><? echo "Date: ".$result[0][csf("application_date")]; ?> </td>
		            <td width="25" ></td>
		        </tr>
		        <tr>
		        	<td colspan="3" height="20">&nbsp;</td>
		        </tr>
		        <br>
		        <tr>
		            <td width="25"></td>
		            <td width="650" align="left"><strong>To,</strong></td>
		            <td width="25" ></td>
		        </tr>
		        <tr>
		            <td width="25" ></td>
		            <td width="650" align="left">
		            <?
						echo "<strong>The Manager</strong><br>".$bank_dtls_arr[$result[0][csf("issuing_bank_id")]]["bank_name"]."<br>".$bank_dtls_arr[$result[0][csf("issuing_bank_id")]]["branch_name"]."<br>".$bank_dtls_arr[$result[0][csf("issuing_bank_id")]]["address"];
					?>
		            </td>
		            <td width="25" ></td>
		        </tr>
		        <tr>
		        	<td colspan="3" height="30"></td>
		        </tr>
		        <tr>
		            <td width="25" ></td>
		            <td width="650" align="left">
					<strong>Subject: </strong>Request to opening of import L/C for value <strong><? echo $currency[$result[0][csf("currency_id")]]."&nbsp;".number_format($result[0][csf("lc_value")],2); ?></strong> at <?echo $result[0]["MARGIN"];?>% cash margin for importing of <strong>Chemicals</strong> for our 100% export oriented readymade garments industry and rest amount will be adjusted at the time of payment release.
		            </td>
		            <td width="25" ></td>
		        </tr>
		        <tr>
		        	<td colspan="3" height="20"></td>
		        </tr>
		        <tr>
		        	<td colspan="3" height="20"></td>
		        </tr>
		        <tr>
		            <td width="25" ></td>
		            <td width="650" align="left"> Dear Sir, </td>
		            <td width="25" ></td>
		        </tr>
				<tr>
		        	<td colspan="3" height="15"></td>
		        </tr>
		        <tr>
		            <td width="25" ></td>
		            <td width="650" align="left">
					Reference to the above, we would like to inform you that we have to import some <strong>Chemicals</strong> value <strong><? echo $currency[$result[0][csf("currency_id")]]."&nbsp;".number_format($result[0][csf("lc_value")],2); ?></strong> at <?echo $result[0]["MARGIN"];?>% cash margin and rest amount  we will adjusted at the time of shipping documents retirement by cash. 
		            </td>
		            <td width="25" ></td>
		        </tr>
				<tr>
		        	<td colspan="3" height="15"></td>
		        </tr>
		        <tr>
		            <td width="25" ></td>
		            <td width="650" align="left">
					You are, therefore, requested to please open the above mention LC  in favour of <strong><? echo $supplier_name;?> , <?echo $supplier_address;?>. Pi no: <? echo $pi_numbers;?> DT: <? echo $pi_date;?></strong>
		            </td>
		            <td width="25" ></td>
		        </tr>
				<tr>
		        	<td colspan="3" height="15"></td>
		        </tr>
		        <tr>
		            <td width="25" ></td>
		            <td width="650" align="left">
					Please debit the margin from our ERQ account no. <? echo $bank_data[$result[0][csf("issuing_bank_id")]]["ACCOUNT_NO_ERQ"];?> in the name of  <? echo $company_name;?>.
		            </td>
		            <td width="25" ></td>
		        </tr>
				<tr>
		        	<td colspan="3" height="15"></td>
		        </tr>
		        <tr>
		            <td width="25" ></td>
		            <td width="650" align="left">
					So please take necessary action and oblige thereby.
		            </td>
		            <td width="25" ></td>
		        </tr>
				<tr>
		        	<td colspan="3" height="15"></td>
		        </tr>
		        <tr>
		            <td width="25" ></td>
		            <td width="650" align="left">
					Thanking you for your kind co-operation.
		            </td>
		            <td width="25" ></td>
		        </tr>
				<tr>
					<td colspan="3" height="100"></td>
				</tr>
		        <tr>
		            <td width="25" ></td>
		            <td width="650" align="left">
					Thanking you<br>
					Yours faithfully,
		            </td>
		            <td width="25" ></td>
		        </tr>


		    </table>
		</div>

    <?
	exit();
}

if ($action==='btb_print_letter_forwarding')
{
	$company_arr = return_library_array('SELECT id, company_name FROM lib_company','id','company_name');
	$country_arr = return_library_array('SELECT id, country_name FROM lib_country','id','country_name');
	// $company_arr_short=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$currency_sign_arr=array(1=>'৳',2=>'$',3=>'€',4=>'€',5=>'$',6=>'£',7=>'¥');

	$supplier_sql = sql_select('SELECT id, supplier_name, address_1, country_id FROM lib_supplier');
	foreach($supplier_sql as $row)
	{
		$supplier_arr[$row[csf("id")]]["supplier_name"]=$row[csf("supplier_name")];
		$supplier_arr[$row[csf("id")]]["address_1"]=$row[csf("address_1")];
		$supplier_arr[$row[csf("id")]]["country"]=$row[csf("country_id")];
	}
	$sql_bank_info = sql_select("SELECT ID, BANK_NAME, BRANCH_NAME, ADDRESS from lib_bank ");
	$bank_dtls_arr=array();
	foreach($sql_bank_info as $row)
	{
		$bank_dtls_arr[$row['ID']]['BANK_NAME']=$row['BANK_NAME'];
		$bank_dtls_arr[$row['ID']]['BRANCH_NAME']=$row['BRANCH_NAME'];
		$bank_dtls_arr[$row['ID']]['ADDRESS']=$row['ADDRESS'];
	}
	$sql_btb="SELECT ID, LC_DATE, APPLICATION_DATE, IMPORTER_ID, ITEM_CATEGORY_ID, ISSUING_BANK_ID, CURRENCY_ID, LC_VALUE, MATURITY_FROM_ID, LAST_SHIPMENT_DATE, LC_EXPIRY_DATE, TENOR, PI_ID,  INCO_TERM_PLACE, ORIGIN, COVER_NOTE_NO, COVER_NOTE_DATE, PI_VALUE,SUPPLIER_ID, MARGIN,REMARKS
	from com_btb_lc_master_details
	where id=$data and is_deleted=0 and status_active=1";
	// INCO_TERM_ID,LC_TYPE_ID, ADVISING_BANK, ADVISING_BANK_ADDRESS, LCA_NO,BTB_SYSTEM_ID, BTB_PREFIX_NUMBER,
	$sql_btb_res=sql_select($sql_btb);
	$btb_id = $sql_btb_res[0]['ID'];
	$lc_value = $sql_btb_res[0]['LC_VALUE'];
	$last_ship_date = $sql_btb_res[0]['LAST_SHIPMENT_DATE'];
	$expire_date = $sql_btb_res[0]['LC_EXPIRY_DATE'];
	$importer_name = $company_arr[$sql_btb_res[0]['IMPORTER_ID']];
	// $origin_name = $country_arr[$sql_btb_res[0]['ORIGIN']];
	$cover_note_no = $sql_btb_res[0]['COVER_NOTE_NO'];
	$cover_note_date = $sql_btb_res[0]['COVER_NOTE_DATE'];
	$bank_name=$bank_dtls_arr[$sql_btb_res[0]['ISSUING_BANK_ID']]['BANK_NAME'];
	$bank_branch=$bank_dtls_arr[$sql_btb_res[0]['ISSUING_BANK_ID']]['BRANCH_NAME'];
	$bank_address=	$bank_dtls_arr[$sql_btb_res[0]['ISSUING_BANK_ID']]['ADDRESS'];
	$lc_type_value= $lc_type[$sql_btb_res[0]['LC_TYPE_ID']];
	$currency_name      = $currency[$sql_btb_res[0]["CURRENCY_ID"]];
	$currency_sign      = $currency_sign_arr[$sql_btb_res[0]["CURRENCY_ID"]];
	// $ref				= $company_arr_short[$sql_btb_res[0]["IMPORTER_ID"]];
	$application_date	= $sql_btb_res[0]["APPLICATION_DATE"];
	$pi_value	= $sql_btb_res[0]["PI_VALUE"];
	$margin_deposit	= $sql_btb_res[0]["MARGIN"];
	$remarks	= $sql_btb_res[0]["REMARKS"];
	$supplier_name = $supplier_arr[$sql_btb_res[0]['SUPPLIER_ID']]["supplier_name"];
	$supplier_country = $country_arr[$supplier_arr[$sql_btb_res[0]['SUPPLIER_ID']]["country"]];
		
	$sql_lcSc=sql_select("SELECT a.LC_SC_ID, a.IMPORT_MST_ID, b.contract_no as LC_SC_NO, b.contract_date as LC_SC_DATE, b.contract_value as LC_SC_VALUE, b.BUYER_NAME,b.bank_file_no, a.IS_LC_SC, sum(c.ATTACHED_QNTY) as LC_SC_ATTACHED_QNTY
	from  com_btb_export_lc_attachment a, com_sales_contract b, com_sales_contract_order_info c
	where a.lc_sc_id=b.id and b.id=c.com_sales_contract_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.is_lc_sc=1 and a.import_mst_id=$data group by a.LC_SC_ID,a.IMPORT_MST_ID,b.contract_no, b.contract_date, b.contract_value,b.BUYER_NAME,b.bank_file_no, a.IS_LC_SC
	union all 
	select a.LC_SC_ID,a.IMPORT_MST_ID, b.export_lc_no as LC_SC_NO, b.lc_date as LC_SC_DATE, b.lc_value as LC_SC_VALUE, b.BUYER_NAME,b.bank_file_no, a.IS_LC_SC ,sum(c.ATTACHED_QNTY) as LC_SC_ATTACHED_QNTY
	from com_btb_export_lc_attachment a, com_export_lc b,com_export_lc_order_info c
	where a.lc_sc_id=b.id and a.status_active=1 and a.is_deleted=0 and a.is_lc_sc=0 and b.status_active=1 and b.is_deleted=0 and a.import_mst_id=$data group by a.LC_SC_ID,a.IMPORT_MST_ID,b.export_lc_no, b.lc_date, b.lc_value,b.BUYER_NAME,b.bank_file_no, a.IS_LC_SC");

	$lc_sc_arr=array();
	foreach($sql_lcSc as $row)
	{
		$lc_sc_arr[$row['IMPORT_MST_ID']]['LC_SC_NO'].=$row['LC_SC_NO'].', ';
		$lc_sc_arr[$row['IMPORT_MST_ID']]['LC_SC_DATE'].=change_date_format($row['LC_SC_DATE']).', ';
		$lc_sc_arr[$row['IMPORT_MST_ID']]['LC_SC_VALUE'] =$row['LC_SC_VALUE'];
		$lc_sc_arr[$row['IMPORT_MST_ID']]['bank_file_no'].=$row['BANK_FILE_NO'].', ';
		$lc_sc_arr[$row['IMPORT_MST_ID']]['BUYER_NAME'].= $row['BUYER_NAME'];
		$lc_sc_arr[$row['IMPORT_MST_ID']]['LC_SC_ATTACHED_QNTY']= $row['LC_SC_ATTACHED_QNTY'];
		$lc_sc_id= $row['LC_SC_ID'];
		if($lc_sc_value!=''){$lc_sc_value .= ", ".number_format($lc_sc_arr[$btb_id]['LC_SC_VALUE'],2);}else{$lc_sc_value =number_format($lc_sc_arr[$btb_id]['LC_SC_VALUE'],2);}
	}
	// echo "<pre>";print_r($lc_sc_arr);die;

	$lc_sc_no   = rtrim($lc_sc_arr[$btb_id]['LC_SC_NO'],', ');
	$lc_sc_date = rtrim($lc_sc_arr[$btb_id]['LC_SC_DATE'],', ');

	?>

    <table width="750" cellpadding="0" align="left" cellspacing="0" border="0">
		<tr><td colspan="4" height="150"></td></tr>
		<tr>
			<td width="25"> </td>
			<td width="675" colspan="3">Date: <? echo date('d.m.Y',strtotime($application_date));?></td>
		</tr>
		<tr><td colspan="4" height="50"></td></tr>
		<tr>
			<td width="25" valign="top"></td>
			<td width="675" colspan="3">To</br>
			The Manager, </br>
			<? echo $bank_name;?></br>
			<? echo $bank_address;?></br>
			</td>
		</tr>
        <tr><td colspan="4" height="50">&nbsp;</td></tr>
		<tr>
			<td width="25"></td>
			<td width="675" colspan="3">
			<strong>Subject:</strong> Request for opening of Back-to-Back L/C for <strong><? echo $currency_name .' '.$currency_sign.''.number_format($lc_value,2); ?></strong> in favor of <strong><? echo $supplier_name.', '.$supplier_country; ?></strong> Against Sales Contract No. /Export LC No.: <? echo $lc_sc_no;?> DT. <? echo $lc_sc_date; ?>.
			</td>
		</tr>
        <tr><td colspan="4">&nbsp;</td></tr>
		<tr>
			<td width="25"></td>
			<td width="675" colspan="3" valign="middle" height="30">Dear Sir,</td>
		</tr>
		<tr>
			<td width="25"></td>
			<td width="675" colspan="3">
			<p>With reference to the above we are pleased to enclose herewith 01 (One) set of L/C application form duly filled in and signed along with Pro-forma invoice accepted by us for opening of Back-to-Back L/C.</p>
			<p>Therefore, you are requested to kindly open the above-mentioned L/C to execute the order in time. </p>
			</td>
		</tr>
        <tr><td colspan="4"  height="100">&nbsp;</td></tr>
        <tr>
			<td width="25"></td>
			<td width="675" colspan="3">
			Thanking You </br>	<br>
			Your Faithfully
			</td>
		</tr>
	</table>
	<?
	exit();
}

if ($action==='btb_print_letter_forwarding2')
{
	extract($_REQUEST);
	$data=explode('*',$data);
	$btb_id=$data[0];
	$format_type=$data[1];
	$currency_sign_arr=array(1=>'৳',2=>'$',3=>'€',4=>'€',5=>'$',6=>'£',7=>'¥');
	$company_arr = return_library_array('SELECT id, company_name FROM lib_company','id','company_name');
	$buyer_arr =return_library_array('SELECT id, buyer_name FROM lib_buyer','id','buyer_name');
	$supplier_arr = return_library_array('SELECT id, supplier_name FROM lib_supplier','id','supplier_name');
	
	$sql_bank_info = sql_select("SELECT ID, BANK_NAME, BRANCH_NAME, ADDRESS from lib_bank ");
	$bank_dtls_arr=array();
	foreach($sql_bank_info as $row)
	{
		$bank_dtls_arr[$row['ID']]['BANK_NAME']=$row['BANK_NAME'];
		$bank_dtls_arr[$row['ID']]['BRANCH_NAME']=$row['BRANCH_NAME'];
		$bank_dtls_arr[$row['ID']]['ADDRESS']=$row['ADDRESS'];
	}

	$sql_btb="SELECT ID, LC_DATE, IMPORTER_ID, ITEM_CATEGORY_ID, ISSUING_BANK_ID, CURRENCY_ID, SUPPLIER_ID, LC_VALUE, PI_ID, LC_TYPE_ID, LCAF_NO, LAST_SHIPMENT_DATE, LC_EXPIRY_DATE, PAYTERM_ID, TENOR, COVER_NOTE_NO, TOLERANCE, INSURANCE_COMPANY_NAME, GARMENTS_QTY
	from com_btb_lc_master_details
	where id=$btb_id and is_deleted=0 and status_active=1";
	$sql_btb_res=sql_select($sql_btb);
	$btb_id = $sql_btb_res[0]['ID'];
	$lc_value = $sql_btb_res[0]['LC_VALUE'];
	$importer_name = $company_arr[$sql_btb_res[0]['IMPORTER_ID']];
	$supplier_name = $supplier_arr[$sql_btb_res[0]['SUPPLIER_ID']];
	$lcaf_no = $sql_btb_res[0]['LCAF_NO'];
	$last_shipment_date = change_date_format($sql_btb_res[0]['LAST_SHIPMENT_DATE']);
	$lc_expiry_date = change_date_format($sql_btb_res[0]['LC_EXPIRY_DATE']);
	$payterm_id = $sql_btb_res[0]['PAYTERM_ID'];
	$tenor = $sql_btb_res[0]['TENOR'];
	$tolerance = $sql_btb_res[0]['TOLERANCE'];
	$cover_note_no = $sql_btb_res[0]['COVER_NOTE_NO'];
	$insurance_company = $sql_btb_res[0]['INSURANCE_COMPANY_NAME'];
	$garments_qty = $sql_btb_res[0]['GARMENTS_QTY'];
	$bank_name=$bank_dtls_arr[$sql_btb_res[0]['ISSUING_BANK_ID']]['BANK_NAME'];
	$bank_branch=$bank_dtls_arr[$sql_btb_res[0]['ISSUING_BANK_ID']]['BRANCH_NAME'];
	$bank_address=	$bank_dtls_arr[$sql_btb_res[0]['ISSUING_BANK_ID']]['ADDRESS'];
	$lc_type_value= $lc_type[$sql_btb_res[0]['LC_TYPE_ID']];
	$currency_name      = $currency[$sql_btb_res[0]["CURRENCY_ID"]];
	$currency_sign      = $currency_sign_arr[$sql_btb_res[0]["CURRENCY_ID"]];

	$pi_ids=$sql_btb_res[0]['PI_ID'];
	$sql_pi_res=sql_select("SELECT a.PI_NUMBER, a.PI_DATE, a.ITEM_CATEGORY_ID
	from com_pi_master_details a
	where a.status_active=1 and a.id in($pi_ids) ");
	$category_name=$$pi_number=$pi_date='';
	foreach($sql_pi_res as $row)
	{
		$pi_number.=$row['PI_NUMBER'].', ';
		$pi_date.=change_date_format($row['PI_DATE']).', ';	
		if($category_check[$row['ITEM_CATEGORY_ID']]=="")
		{
			$category_check[$row['ITEM_CATEGORY_ID']]=$row['ITEM_CATEGORY_ID'];
			$category_name.=$item_category[$row['ITEM_CATEGORY_ID']].', ';
		}
	}
	$category_name=chop($category_name,", ");
	$pi_number=chop($pi_number,", ");
	$pi_date=chop($pi_date,", ");
	
	$sql_lcSc=sql_select("SELECT a.import_mst_id, b.contract_no as LC_SC_NO, b.contract_date as LC_SC_DATE, b.BUYER_NAME, a.IS_LC_SC, b.last_shipment_date as SHIPMENT_DATE, b.contract_value as LC_SC_VALUE, b.expiry_date as EXPIRY_DATE
	from  com_btb_export_lc_attachment a, com_sales_contract b
	where a.lc_sc_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.is_lc_sc=1 and a.import_mst_id=$btb_id
	union all 
	select  a.import_mst_id,b.export_lc_no as LC_SC_NO, b.lc_date as LC_SC_DATE, b.BUYER_NAME, a.IS_LC_SC , b.last_shipment_date as SHIPMENT_DATE, b.lc_value as LC_SC_VALUE, b.expiry_date as EXPIRY_DATE
	from com_btb_export_lc_attachment a, com_export_lc b
	where a.lc_sc_id=b.id and a.status_active=1 and a.is_deleted=0 and a.is_lc_sc=0 and b.status_active=1 and b.is_deleted=0 and a.import_mst_id=$btb_id");	

	?>
	<style>
		.wrd_brk{word-break: break-all;}
		.center{text-align: center;}
		.right{text-align: right;}
	</style>
    <table width="750" cellpadding="0" align="left" cellspacing="0" border="0">
		<tr><td colspan="4" height="170"></td></tr>
		<tr>
			<td width="25"> </td>
			<td width="675" colspan="3">Date: <? echo date('F d, Y');?></td>
		</tr>
		<tr><td colspan="4" height="25"></td></tr>
		<tr>
			<td width="25"></td>

			<td width="675" colspan="3">To </br>The Head of the Branch</br>
			<? echo $bank_name;?></br>
			<? echo $bank_branch;?></br>
			<? echo $bank_address;?></br>
			</td>
		</tr>
        <tr><td colspan="4" height="60">&nbsp;</td></tr>
		<tr>
			<td width="25"></td>
			<td width="675" colspan="3">Sub: Request for Opening Back To Back L/C for <? echo $currency_name.' '.$currency_sign.''.number_format($lc_value,2);?>
			</td>
		</tr>
        <tr><td colspan="4">&nbsp;</td></tr>
		<tr>
			<td width="25"></td>
			<td width="675" colspan="3">Dear Sir,</td>
		</tr>
		<tr><td colspan="4">&nbsp;</td></tr>
		<tr>
			<td width="25"></td>
			<td width="675" colspan="3">We would like to inform you that we need to <? echo $lc_type_value; ?> for <?=$currency_name.' '.$currency_sign.''.number_format($lc_value,2);?>. Against Export L/C No-as below:-
		</tr>
		<tr>
			<td width="25"></td>
			<td width="675" colspan="3">
				<table width="675" cellpadding="0" cellspacing="0" class="rpt_table" border="1">
					<thead>
						<tr>
							<th width="30">SL</th>
							<th width="150">Export LC/SC No</th>
							<th width="80">LC/SC Date</th>
							<th width="80">Shipment Date</th>
							<th width="80">Expiry</th>
							<th width="100">Value</th>
							<th>Buyer</th>
						</tr>
					</thead>
					<tbody>
						<?
							$i=1;
							foreach($sql_lcSc as $row)
							{
								?>
									<tr>
										<td class="wrd_brk center"><?=$i;?></td>
										<td class="wrd_brk "><?=$row['LC_SC_NO'];?></td>
										<td class="wrd_brk center"><?=change_date_format($row['LC_SC_DATE']);?></td>
										<td class="wrd_brk center"><?=change_date_format($row['SHIPMENT_DATE']);?></td>
										<td class="wrd_brk center"><?=change_date_format($row['EXPIRY_DATE']);?></td>
										<td class="wrd_brk right"><?=$currency_sign.''.number_format($row['LC_SC_VALUE'],2);?></td>
										<td class="wrd_brk "><?=$buyer_arr[$row['BUYER_NAME']];?></td>
									</tr>
								<?
								$total_lc_sc_value+=$row['LC_SC_VALUE'];
								$i++;
							}
						?>
					</tbody>
					<tfoot>
						<tr>
							<th colspan="5" class="right">Total</th>
							<th class="wrd_brk right"><?=$currency_sign.''.number_format($total_lc_sc_value,2);?></th>
							<th></th>
						</tr>
					</tfoot>
				</table>
			</td>
		</tr>
        <tr><td colspan="4"  height="10">&nbsp;</td></tr>
        <tr>
			<td width="25"></td>
			<td width="675" colspan="3">For import of <?=$category_name;?> in favor of <?=$supplier_name;?> . As per Proforma Invoice No: <?=$pi_number;?> Date- <?=$pi_date; ?></td>
		</tr>
        <tr><td colspan="4"  height="10">&nbsp;</td></tr>
        <tr>
			<td width="25"></td>
			<td width="675" colspan="3">
				<?
				if($format_type==1)
				{
					?>
						&#10146;&nbsp;Payment at maturity in <?=$currency_name;?><br>
						&#10146;&nbsp;Latest Date  of Shipment – <?=$last_shipment_date;?><br>
						&#10146;&nbsp;Date And Place of Expiry- <?=$lc_expiry_date;?><br>
						&#10146;&nbsp;Tolerances – <?=$tolerance;?>% (+/-) <br>
						&#10146;&nbsp;LC: <?=$pay_term[$payterm_id];?><br>
						<? if($payterm_id==2){ ?>&#10146;&nbsp;Tenor: <?=$tenor;?> Days<br> <? } ?>
						&#10146;&nbsp;LCAF: <?=$lcaf_no;?><br>
						&#10146;&nbsp;Insurance cover note No: <?=$cover_note_no;?><br>
						&#10146;&nbsp;<?=$insurance_company;?> <br>
						&#10146;&nbsp;Shipping Marks: <?=$importer_name;?> must be mention in rolls.<br>
					<?
				}
				else
				{
					?>
						&#10146;&nbsp;Garments Quantity- <?=$garments_qty;?>,PCS ( 5%+/-)<br>
						&#10146;&nbsp;Payment at maturity in <?=$currency_name;?><br>
						<?
							if($payterm_id==2 && $tenor!='')
							{
								?>
									&#10146;&nbsp;Lc Tenor <?=$tenor;?> days from date of Acceptance<br>
								<?
							}
						?>
						&#10146;&nbsp;Latest Date  of Shipment – <?=$last_shipment_date;?><br>
						&#10146;&nbsp;Date And Place of Expiry- <?=$lc_expiry_date;?><br>
						&#10146;&nbsp;Cash incentive will be claimed by <?=$importer_name;?> <br>
					<?
				}
				?>

			</td>
		</tr>
        <tr><td colspan="4"  height="50">&nbsp;</td></tr>
        <tr>
			<td width="25"></td>
			<td width="675" colspan="3">Your prompt action will be highly appreciated.</td>
		</tr>
        <tr><td colspan="4"  height="10">&nbsp;</td></tr>
        <tr>
			<td width="25"></td>
			<td width="675" colspan="3">Thanking You</td>
		</tr>
	</table>
	<?
	exit();
}

if ($action==='btb_print_letter_forwarding3')
{
	$company_arr = return_library_array('SELECT id, company_name FROM lib_company','id','company_name');
	$country_arr = return_library_array('SELECT id, country_name FROM lib_country','id','country_name');
	$supplier_arr = return_library_array('SELECT id, supplier_name FROM lib_supplier','id','supplier_name');
	$designation_arr = return_library_array('SELECT id, custom_designation from lib_designation where status_active=1 ','id','custom_designation');
	$currency_sign_arr=array(1=>'৳',2=>'$',3=>'€',4=>'€',5=>'$',6=>'£',7=>'¥');

	$sql_btb="SELECT ID, IMPORTER_ID, ISSUING_BANK_ID, CURRENCY_ID, LC_VALUE, PAYTERM_ID, TENOR, PI_ID, SUPPLIER_ID
	from com_btb_lc_master_details
	where id=$data and is_deleted=0 and status_active=1";

	$sql_btb_res=sql_select($sql_btb);
	$btb_id = $sql_btb_res[0]['ID'];
	$lc_value = $sql_btb_res[0]['LC_VALUE'];
	$issuing_bank_id=$sql_btb_res[0]['ISSUING_BANK_ID'];
	$pay_term_name  = $pay_term[$sql_btb_res[0]["PAYTERM_ID"]];
	$currency_name  = $currency[$sql_btb_res[0]["CURRENCY_ID"]];
	$currency_sign  = $currency_sign_arr[$sql_btb_res[0]["CURRENCY_ID"]];
	$supplier_name = $supplier_arr[$sql_btb_res[0]['SUPPLIER_ID']];
	$payterm_id	= $sql_btb_res[0]["PAYTERM_ID"];
	$pi_ids=$sql_btb_res[0]['PI_ID'];
	if($sql_btb_res[0]['TENOR']){$tenor=$sql_btb_res[0]['TENOR']." day's ";}else{$tenor="";}

	$sql_bank_info = sql_select("SELECT ID, BANK_NAME, DESIGNATION, BRANCH_NAME, ADDRESS from lib_bank where id = $issuing_bank_id ");

	$sql_lcSc=sql_select("SELECT a.LC_SC_ID,a.IMPORT_MST_ID, b.contract_no as LC_SC_NO, b.contract_date as LC_SC_DATE
	from  com_btb_export_lc_attachment a, com_sales_contract b
	where a.lc_sc_id=b.id and a.status_active=1 and b.status_active=1  and a.is_lc_sc=1 and a.import_mst_id=$data 
	union all 
	select a.LC_SC_ID,a.IMPORT_MST_ID, b.export_lc_no as LC_SC_NO, b.lc_date as LC_SC_DATE
	from com_btb_export_lc_attachment a, com_export_lc b
	where a.lc_sc_id=b.id and a.is_lc_sc=0 and a.status_active=1 and b.status_active=1 and a.import_mst_id=$data ");
	foreach($sql_lcSc as $row)
	{
		$lc_sc_info.=$row['LC_SC_NO'].' dtd '.change_date_format($row['LC_SC_DATE'])."<br>";
	}
	$lc_sc_info = rtrim($lc_sc_info,'<br>');

	$sql_pi_res=sql_select("SELECT a.PI_NUMBER, a.PI_DATE, a.NET_TOTAL_AMOUNT from com_pi_master_details a where a.status_active=1 and a.id in($pi_ids) ");
	foreach($sql_pi_res as $row)
	{
		$pi_number.=$row['PI_NUMBER'].' # '.$row['PI_DATE']."<br>";
		$pi_amount.=$currency_sign." ".number_format($row['NET_TOTAL_AMOUNT'],2)."<br>";
		$tot_pi_amount+=$row['NET_TOTAL_AMOUNT'];
	}
	$pi_number=rtrim($pi_number,'<br>');
	$pi_amount=rtrim($pi_amount,'<br>');
	?>

    <table width="750" cellpadding="0" align="left" cellspacing="0" border="0">
		<tr><td colspan="4" height="120"></td></tr>
		<tr>
			<td width="25"> </td>
			<td width="675" colspan="3">Ref : </td>
		</tr>
		<tr>
			<td width="25"> </td>
			<td width="675" colspan="3">Date: <?=date('d M Y');?></td>
		</tr>
		<tr><td colspan="4" height="50"></td></tr>
		<tr>
			<td width="25" valign="top"></td>
			<td width="675" colspan="3">
			<?=$designation_arr[$sql_bank_info[0]["DESIGNATION"]];?></br>
			<?=$sql_bank_info[0]["BANK_NAME"];?></br>
			<?=$sql_bank_info[0]["ADDRESS"];?>
			</td>
		</tr>
        <tr><td colspan="4" height="50">&nbsp;</td></tr>
		<tr>
			<td width="25"></td>
			<td width="675" colspan="3">
			Sub: Request to open BB LC for <strong><?=$currency_name .' '.$currency_sign.''.number_format($lc_value,2); ?></strong> <?=$pay_term_name." ".$tenor;?> Basis favoring <strong><?=$supplier_name; ?></strong> We will purchase from China Supplier the Garments raw- materials against below <strong>P/Inv.<?if($payterm_id==2){ echo "";}else{echo "Under E.D.F";}?> </strong>
			</td>
		</tr>
        <tr><td colspan="4">&nbsp;</td></tr>
		<tr>
			<td width="25"></td>
			<td width="675" colspan="3" valign="middle" height="30">Dear Sir,</td>
		</tr>
		<tr>
			<td width="25"></td>
			<td width="675" colspan="3">
			With reference to the above, we are pleased to furnish you below Valid Export LC / Valid Sales Contract  # &  P/Inv details  and  necessary paper & documents for your kind perusal to opening BB LC for <strong><?=$currency_name .' '.$currency_sign.''.number_format($lc_value,2); ?></strong> <?=$pay_term_name." ".$tenor;?> Basis favoring <strong><?=$supplier_name; ?></strong> We will purchase from China Supplier the Garments raw- materials against below P/Inv. 
			</td>
		</tr>
        <tr><td colspan="4" height="20">&nbsp;</td></tr>
		<tr>
			<td width="25"></td>
			<td width="325"><strong><u>Valid Sales Export LC# & Date</u></strong></td>
			<td width="250"><strong><u>P/Inv # Date</u></strong></td>
			<td width="100" align="center"><strong><u>P/Inv Value</u></strong></td>
		</tr>
		<tr>
			<td width="25"></td>
			<td width="325"><strong><?=$lc_sc_info;?></strong></td>
			<td width="250"><strong><?=$pi_number;?></strong></td>
			<td width="100" align="right"><strong><?=$pi_amount;?></strong></td>
		</tr>
		<tr>
			<td width="25"></td>
			<td width="325" ></td>
			<td width="250" ></td>
			<td width="100" align="right" style="border-top: 2px solid black;"><strong><?=$currency_sign." ".number_format($tot_pi_amount,2);?></strong></td>
		</tr>
        <tr><td colspan="4"  height="30">&nbsp;</td></tr>
		<tr>
			<td width="25"></td>
			<td width="675" colspan="3">We therefore request  you to open above BB LC  through swift as per  enclosed  P/Inv & other paper / documents  at your earliest convenience. </td>
		</tr>
        <tr><td colspan="4"  height="100">&nbsp;</td></tr>
        <tr>
			<td width="25"></td>
			<td width="675" colspan="3">
			Thanking you. </br>	<br>
			Yours truly.
			</td>
		</tr>
        <tr><td colspan="4"  height="20">&nbsp;</td></tr>
        <tr>
			<td width="25"></td>
			<td width="675" align="right" colspan="3">Encl: As above. </td>
		</tr>
	</table>
	<?
	exit();
}

if ($action==='btb_print_letter_forwarding4')
{
	echo load_html_head_contents("LC Forwarding Letter 4", "../../", 1, 1,'', '', '');

	$company_arr = return_library_array('SELECT id, company_name FROM lib_company','id','company_name');
	$supplier_arr = return_library_array('SELECT id, supplier_name FROM lib_supplier','id','supplier_name');
	$designation_arr = return_library_array('SELECT id, custom_designation from lib_designation where status_active=1 ','id','custom_designation');
	$currency_sign_arr=array(1=>'৳',2=>'$',3=>'€',4=>'€',5=>'$',6=>'£',7=>'¥');

	$sql_btb="SELECT ID, BTB_SYSTEM_ID, IMPORTER_ID, APPLICATION_DATE, ISSUING_BANK_ID, CURRENCY_ID, LC_VALUE, PI_ID, SUPPLIER_ID, LC_TYPE_ID, LCAF_NO
	from com_btb_lc_master_details
	where id=$data and is_deleted=0 and status_active=1";

	$sql_btb_res=sql_select($sql_btb);
	$btb_id = $sql_btb_res[0]['ID'];
	$importer_name = $company_arr[$sql_btb_res[0]['IMPORTER_ID']];
	$btb_system_id = $sql_btb_res[0]['BTB_SYSTEM_ID'];
	$application_date = $sql_btb_res[0]['APPLICATION_DATE'];
	$lc_value = $sql_btb_res[0]['LC_VALUE'];
	$lcaf_no = $sql_btb_res[0]['LCAF_NO'];
	$issuing_bank_id=$sql_btb_res[0]['ISSUING_BANK_ID'];
	$lc_name  = $lc_type[$sql_btb_res[0]["LC_TYPE_ID"]];
	$currency_name  = $currency[$sql_btb_res[0]["CURRENCY_ID"]];
	$currency_sign  = $currency_sign_arr[$sql_btb_res[0]["CURRENCY_ID"]];
	$supplier_name = $supplier_arr[$sql_btb_res[0]['SUPPLIER_ID']];
	$pi_ids=$sql_btb_res[0]['PI_ID'];

	$sql_bank_info = sql_select("SELECT ID, BANK_NAME, DESIGNATION, BRANCH_NAME, ADDRESS from lib_bank where id = $issuing_bank_id ");

	$sql_pi_res=sql_select("SELECT a.PI_NUMBER, a.PI_DATE, a.ITEM_CATEGORY_ID from com_pi_master_details a where a.status_active=1 and a.id in($pi_ids) ");
	foreach($sql_pi_res as $row)
	{
		$pi_number.=$row['PI_NUMBER'].', ';
		$pi_date.=$row['PI_DATE'].",";
	}
	$pi_number=rtrim($pi_number,', ');
	$pi_date=rtrim($pi_date,', ');
	$pi_category=$item_category[$sql_pi_res[0]["ITEM_CATEGORY_ID"]];
	?>
    <table width="750" cellpadding="0" align="left" cellspacing="0" border="0">
		<tr><td colspan="4" height="120"></td></tr>
		<tr>
			<td width="25"> </td>
			<td width="675" colspan="3">Ref : <?=$btb_system_id;?></td>
		</tr>
		<tr>
			<td width="25"> </td>
			<td width="675" colspan="3" align="right">Date: <?=change_date_format($application_date);?></td>
		</tr>
		<tr><td colspan="4" height="50"></td></tr>
		<tr>
			<td width="25" valign="top"></td>
			<td width="675" colspan="3">
				To,<br>
				The <?=$designation_arr[$sql_bank_info[0]["DESIGNATION"]];?></br>
				<?=$sql_bank_info[0]["BANK_NAME"];?></br>
				<?=$sql_bank_info[0]["ADDRESS"];?>
			</td>
		</tr>
        <tr><td colspan="4" height="50">&nbsp;</td></tr>
		<tr>
			<td width="25"></td>
			<td width="675" colspan="3">
			<strong>Sub: Request for issuance a <?=$lc_name;?> for <?=$currency_name .' '.$currency_sign.''.number_format($lc_value,2); ?> against LCAF No: <?=$lcaf_no;?></strong>
			</td>
		</tr>
        <tr><td colspan="4">&nbsp;</td></tr>
		<tr>
			<td width="25"></td>
			<td width="675" colspan="3" valign="middle" height="30">Dear Sir,</td>
		</tr>
		<tr>
			<td width="25"></td>
			<td width="675" colspan="3">
				With reference to the above, we would like to inform you that, we intend to import <strong><?=$pi_category;?></strong> for 100% Export Oriented Garments Industry. We do hereby authorize to debit <strong><?=$currency_name .' '.$currency_sign.''.number_format($lc_value,2); ?></strong> from our ERQ Account maintaining with you and issue a <strong><?=$lc_name;?></strong> in favor of <strong><?=$supplier_name; ?></strong>. <br>
				We are submitting here with the LCAF No: <strong><?=$lcaf_no;?></strong> Proforma Invoice No: <strong><?=$pi_number;?></strong> Dated: <strong><?=$pi_date;?></strong> 
			</td>
		</tr>
        <tr><td colspan="4"  height="20">&nbsp;</td></tr>
		<tr>
			<td width="25"></td>
			<td width="675" colspan="3">
				Upon released the goods, we will submit the Bill of Entry for your kind information and if any necessary charges for this purpose may be debited from our ERQ Account maintaining with you.
			</td>
		</tr>
        <tr><td colspan="4"  height="20">&nbsp;</td></tr>
		<tr>
			<td width="25"></td>
			<td width="675" colspan="3">
				Therefore, you are requested to allow us to import the said <strong><?=$pi_category;?></strong> against <strong><?=$lc_name;?></strong>.<br>
				Thanking you for your kind co-operation
			</td>
		</tr>
        <tr><td colspan="4"  height="100">&nbsp;</td></tr>
        <tr>
			<td width="25"></td>
			<td width="675" colspan="3">
				Yours faithfully </br>
				For <?=$importer_name;?>.
			</td>
		</tr>
        <tr><td colspan="4"  height="40">&nbsp;</td></tr>
        <tr>
			<td width="25"></td>
			<td width="675" colspan="3">
				-------------------------------<br>
				MANAGING DIRECTOR.
			</td>
		</tr>
	</table>
	<?
	exit();
}

if ($action==='btb_print_letter_forwarding5')
{
	echo load_html_head_contents("LC Forwarding Letter 5", "../../", 1, 1,'', '', '');

	$company_arr = return_library_array('SELECT id, company_name FROM lib_company','id','company_name');
	$supplier_arr = return_library_array('SELECT id, supplier_name FROM lib_supplier','id','supplier_name');
	$designation_arr = return_library_array('SELECT id, custom_designation from lib_designation where status_active=1 ','id','custom_designation');
	$currency_sign_arr=array(1=>'৳',2=>'$',3=>'€',4=>'€',5=>'$',6=>'£',7=>'¥');

	$sql_btb="SELECT ID, BTB_SYSTEM_ID, IMPORTER_ID, APPLICATION_DATE, ISSUING_BANK_ID, CURRENCY_ID, LC_VALUE, PI_ID, SUPPLIER_ID, LC_TYPE_ID, LCAF_NO
	from com_btb_lc_master_details
	where id=$data and is_deleted=0 and status_active=1";

	$sql_btb_res=sql_select($sql_btb);
	$btb_id = $sql_btb_res[0]['ID'];
	$importer_name = $company_arr[$sql_btb_res[0]['IMPORTER_ID']];
	$btb_system_id = $sql_btb_res[0]['BTB_SYSTEM_ID'];
	$application_date = $sql_btb_res[0]['APPLICATION_DATE'];
	$lc_value = $sql_btb_res[0]['LC_VALUE'];
	$lcaf_no = $sql_btb_res[0]['LCAF_NO'];
	$issuing_bank_id=$sql_btb_res[0]['ISSUING_BANK_ID'];
	$lc_name  = $lc_type[$sql_btb_res[0]["LC_TYPE_ID"]];
	$currency_name  = $currency[$sql_btb_res[0]["CURRENCY_ID"]];
	$currency_sign  = $currency_sign_arr[$sql_btb_res[0]["CURRENCY_ID"]];
	$supplier_name = $supplier_arr[$sql_btb_res[0]['SUPPLIER_ID']];
	$pi_ids=$sql_btb_res[0]['PI_ID'];

	$sql_bank_info = sql_select("SELECT ID, BANK_NAME, DESIGNATION, BRANCH_NAME, ADDRESS from lib_bank where id = $issuing_bank_id ");

	$sql_lcSc=sql_select("SELECT a.LC_SC_ID,a.IMPORT_MST_ID, b.contract_no as LC_SC_NO, b.contract_date as LC_SC_DATE
	from  com_btb_export_lc_attachment a, com_sales_contract b
	where a.lc_sc_id=b.id and a.status_active=1 and b.status_active=1  and a.is_lc_sc=1 and a.import_mst_id=$data 
	union all 
	select a.LC_SC_ID,a.IMPORT_MST_ID, b.export_lc_no as LC_SC_NO, b.lc_date as LC_SC_DATE
	from com_btb_export_lc_attachment a, com_export_lc b
	where a.lc_sc_id=b.id and a.is_lc_sc=0 and a.status_active=1 and b.status_active=1 and a.import_mst_id=$data ");
	foreach($sql_lcSc as $row)
	{
		$lc_sc_no.=$row['LC_SC_NO'].", ";
		$lc_sc_date.=change_date_format($row['LC_SC_DATE']).", ";
	}
	$lc_sc_no = rtrim($lc_sc_no,', ');
	$lc_sc_date = rtrim($lc_sc_date,', ');

	$sql_pi_res=sql_select("SELECT a.PI_NUMBER, a.PI_DATE, a.ITEM_CATEGORY_ID from com_pi_master_details a where a.status_active=1 and a.id in($pi_ids) ");
	foreach($sql_pi_res as $row)
	{
		$pi_number.=$row['PI_NUMBER'].', ';
		$pi_date.=$row['PI_DATE'].",";
	}
	$pi_number=rtrim($pi_number,', ');
	$pi_date=rtrim($pi_date,', ');
	$pi_category=$item_category[$sql_pi_res[0]["ITEM_CATEGORY_ID"]];
	?>

    <table width="750" cellpadding="0" align="left" cellspacing="0" border="0">
		<tr><td colspan="4" height="120"></td></tr>
		<tr>
			<td width="25"> </td>
			<td width="675" colspan="3">Ref : <?=$btb_system_id;?></td>
		</tr>
		<tr>
			<td width="25"> </td>
			<td width="675" colspan="3" align="right">Date: <?=change_date_format($application_date);?></td>
		</tr>
		<tr><td colspan="4" height="50"></td></tr>
		<tr>
			<td width="25" valign="top"></td>
			<td width="675" colspan="3">
				To,<br>
				The <?=$designation_arr[$sql_bank_info[0]["DESIGNATION"]];?></br>
				<?=$sql_bank_info[0]["BANK_NAME"];?></br>
				<?=$sql_bank_info[0]["ADDRESS"];?>
			</td>
		</tr>
        <tr><td colspan="4" height="50">&nbsp;</td></tr>
		<tr>
			<td width="25"></td>
			<td width="675" colspan="3">
			<strong>Sub: Request for issuance a <?=$lc_name;?> for <?=$currency_name .' '.$currency_sign.''.number_format($lc_value,2); ?> against LCAF No: <?=$lcaf_no;?> Under Export Contract: <?=$lc_sc_no;?> Dt: <?=$lc_sc_date;?></strong>
			</td>
		</tr>
        <tr><td colspan="4">&nbsp;</td></tr>
		<tr>
			<td width="25"></td>
			<td width="675" colspan="3" valign="middle" height="30">Dear Sir,</td>
		</tr>
		<tr>
			<td width="25"></td>
			<td width="675" colspan="3">
				With reference to the above, we would like to inform you that, we intend to import <strong><?=$pi_category;?></strong> for 100% Export Oriented Garments Industry. We do hereby authorize to debit <strong><?=$currency_name .' '.$currency_sign.''.number_format($lc_value,2); ?></strong> from our ERQ Account maintaining with you and issue a <strong><?=$lc_name;?></strong> in favor of <strong><?=$supplier_name; ?></strong>. <br>
				We are submitting here with the LCAF No: <strong><?=$lcaf_no;?></strong> Proforma Invoice No: <strong><?=$pi_number;?></strong> Dated: <strong><?=$pi_date;?></strong> 
			</td>
		</tr>
        <tr><td colspan="4"  height="20">&nbsp;</td></tr>
		<tr>
			<td width="25"></td>
			<td width="675" colspan="3">
				Upon released the goods, we will submit the Bill of Entry for your kind information and if any necessary charges for this purpose may be debited from our ERQ Account maintaining with you.
			</td>
		</tr>
        <tr><td colspan="4"  height="20">&nbsp;</td></tr>
		<tr>
			<td width="25"></td>
			<td width="675" colspan="3">
				Therefore, you are requested to allow us to import the said <strong><?=$pi_category;?></strong> against <strong><?=$lc_name;?></strong>.<br>
				Thanking you for your kind co-operation
			</td>
		</tr>
        <tr><td colspan="4"  height="100">&nbsp;</td></tr>
        <tr>
			<td width="25"></td>
			<td width="675" colspan="3">
				Yours faithfully </br>
				For <?=$importer_name;?>.
			</td>
		</tr>
        <tr><td colspan="4"  height="40">&nbsp;</td></tr>
        <tr>
			<td width="25"></td>
			<td width="675" colspan="3">
				-------------------------------<br>
				MANAGING DIRECTOR.
			</td>
		</tr>
	</table>
	<?
	exit();
}


if($action=="btb_import_lc_letter7")
{
	//echo load_html_head_contents("BTB Letter","../../", 1, 1, $unicode,'','');
	$currency_sign_arr=array(1=>'৳',2=>'$',3=>'€',4=>'€',5=>'$',6=>'£',7=>'¥');
	$sql_bank_info=sql_select("select id, contact_person, bank_name, branch_name, address,designation from lib_bank ");
	foreach($sql_bank_info as $row)
	{
		$bank_dtls_arr[$row[csf("id")]]["contact_person"]=$row[csf("contact_person")];
		$bank_dtls_arr[$row[csf("id")]]["bank_name"]=$row[csf("bank_name")];
		$bank_dtls_arr[$row[csf("id")]]["branch_name"]=$row[csf("branch_name")];
		$bank_dtls_arr[$row[csf("id")]]["address"]=$row[csf("address")];
		$bank_dtls_arr[$row[csf("id")]]["designation"]=$row[csf("designation")];
	}

	// echo $data;die;
	$sql_com="SELECT a.id,a.btb_system_id, a.application_date, a.importer_id, a.item_category_id, a.origin, a.issuing_bank_id, a.currency_id, a.supplier_id, a.lc_value, a.margin,a.lc_date,a.tenor,a.last_shipment_date,a.lc_expiry_date,a.upas_rate,a.cover_note_no,a.cover_note_date, a.lcaf_no,a.pi_id,a.payterm_id
	from com_btb_lc_master_details a
	where a.id=$data and a.is_deleted = 0 AND a.status_active = 1 group by a.id,a.btb_system_id, a.application_date, a.importer_id, a.item_category_id, a.origin, a.issuing_bank_id, a.currency_id, a.supplier_id, a.lc_value, a.margin,a.lc_date,a.tenor,a.last_shipment_date,a.lc_expiry_date,a.upas_rate,a.cover_note_no,a.cover_note_date, a.lcaf_no,a.pi_id,a.payterm_id";

	//echo $sql_com;
	$result=sql_select($sql_com);

	$country_array = return_library_array("SELECT id,country_name from lib_country ","id","country_name");
	$comp_library = return_library_array('SELECT id, company_name from lib_company','id','company_name');
	$company_name = return_field_value("company_name","lib_company","id=".$result[0][csf("importer_id")],"company_name");
	$supplier_name = return_field_value("supplier_name","lib_supplier","id=".$result[0][csf("supplier_id")],"supplier_name");
	$supplier_address = return_field_value("address_1","lib_supplier","id=".$result[0][csf("supplier_id")],"address_1");
	$country_name = return_field_value("country_name"," lib_country","id=".$result[0][csf("origin")],"country_name");
	$designation = return_field_value("custom_designation","lib_designation","id=".$bank_dtls_arr[$result[0][csf("issuing_bank_id")]]["designation"],"custom_designation");
	$address = sql_select("SELECT id,plot_no,level_no,road_no,block_no,country_id,city,zip_code,email,contact_no from lib_company");
	$company_address=array();
	foreach($address as $row){
		if($row[csf('plot_no')]!=''){$company_address[$row[csf('id')]].=$row[csf('plot_no')];}
		if($row[csf('level_no')]!=''){$company_address[$row[csf('id')]].=$row[csf('level_no')];}
		if($row[csf('road_no')]!=''){$company_address[$row[csf('id')]].=", ".$row[csf('road_no')];}
		if($row[csf('block_no')]!=''){$company_address[$row[csf('id')]].=", ".$row[csf('block_no')];}
		if($row[csf('city')]!=''){$company_address[$row[csf('id')]].=", ".$row[csf('city')];}
		if($row[csf('zip_code')]!=''){$company_address[$row[csf('id')]].=", ".$row[csf('zip_code')];}
		if($row[csf('country_id')]!=''){$company_address[$row[csf('id')]].=", ".$country_array[$company_add['country_id']];}
	}

	$buyer_result = sql_select('SELECT id as ID, buyer_name as BUYER_NAME,address_1 as ADDRESS_1 from lib_buyer');
	$buyer_info=array();
	foreach($buyer_result as $row)
	{
		$buyer_info['ID']['buyer_name']=$row['buyer_name'];
		$buyer_info['ID']['address']=$row['address_1'];
	}
	$ref_arr=explode('-',$result[0][csf("btb_system_id")]);

	$sql_pi=sql_select("SELECT a.import_mst_id, b.contract_no as lc_sc_no, b.contract_value as lc_sc_val , b.contract_date as sc_lc_date, 0 as import_btb, b.buyer_name
	from  com_btb_export_lc_attachment a, com_sales_contract b
	where a.lc_sc_id=b.id and a.status_active=1 and a.is_lc_sc=1 and a.import_mst_id=$data
	union all 
	select a.import_mst_id, b.export_lc_no as lc_sc_no, b.lc_value as lc_sc_val, b.lc_date as sc_lc_date, b.import_btb as import_btb, b.buyer_name
	from com_btb_export_lc_attachment a, com_export_lc b
	where a.lc_sc_id=b.id and a.status_active=1 and a.is_lc_sc=0 and a.import_mst_id=$data");
	$sql_lc__arr=array();
	foreach($sql_pi as $row)
	{
		$sql_lc__arr[$row[csf("import_mst_id")]]["lc_sc_no"]=$row[csf("lc_sc_no")].",";
		$sql_lc__arr[$row[csf("import_mst_id")]]["lc_sc_val"]=$row[csf("lc_sc_val")].",";
		$sql_lc__arr[$row[csf("import_mst_id")]]["sc_lc_date"]=$row[csf("sc_lc_date")].",";
		if($row[csf("import_btb")]==1)
		{
			$sql_lc__arr[$row[csf("import_mst_id")]]["buyer_name"]=$comp_library[$row[csf("buyer_name")]].",";
			$sql_lc__arr[$row[csf("import_mst_id")]]["buyer_address"]=$company_address[$row[csf("buyer_name")]].",";
		}else{
			$sql_lc__arr[$row[csf("import_mst_id")]]["buyer_name"]=$buyer_info[$row[csf("buyer_name")]].",";
			$sql_lc__arr[$row[csf("import_mst_id")]]["buyer_address"]=$buyer_info[$row[csf("buyer_name")]].",";
		}
	}
	$pi_number_arr=sql_select( "SELECT id, pi_number,pi_date,item_category_id from com_pi_master_details where id in(".$result[0][csf("pi_id")].")  AND status_active = 1 AND is_deleted = 0");

	if(count($pi_number_arr)>1){
		foreach($pi_number_arr as $row){
			$pi_numbers .= $row[csf('pi_number')].", ";
			$pi_date .= change_date_format($row[csf('pi_date')]).", ";
		}
		$pi_numbers = chop($pi_numbers,", ");
		$pi_date = chop($pi_date,", ");
		$itemCategory = $item_category[$pi_number_arr[0][csf('item_category_id')]];

	}
	else
	{
		$pi_numbers = $pi_number_arr[0][csf('pi_number')];
		$pi_date = change_date_format($pi_number_arr[0][csf('pi_date')]);
		$itemCategory = $item_category[$pi_number_arr[0][csf('item_category_id')]];

	}
	$factory_arr = sql_select("select id,address from lib_location where company_id='".$result[0][csf("importer_id")]."' and is_deleted=0");
	$factory_add='';
	foreach($factory_arr as $factory){
		if($factory_add!='' && $factory[csf('address')] !=''){ $factory_add.= ", ".$factory[csf('address')]; }else{ $factory_add.= $factory[csf('address')]; }
	}
	?>

		<style type="text/css">
			.a4size {
	           width: 21cm;
	           height: 26.7cm;
	           font-family: Cambria, Georgia, serif;
	        }
	        @media print {
	        .a4size{ font-family: Cambria;font-size: 18px;margin: 30px 100PX 54px 25px;size: A4 portrait;
	            }
	        }
			.wrd_brk{word-break: break-all;}
			.left{text-align: left;}
			.center{text-align: center;}
			.right{text-align: right;}
		</style>
		<?
			if($result[0][csf("payterm_id")]==1)
			{
				?>
					<div class="a4size">
						<table width="794" cellpadding="0" cellspacing="0" border="0" >
							<tr>
								<td colspan="3" height="30"></td>
							</tr>
							<tr>
								<td width="25" ></td>
								<!-- <td width="550" >Ref.:<?=$ref_arr[0]."/EBL-".$ref_arr[3]."/20".$ref_arr[2];?></td> -->
								<td width="550" >Ref.: <?=$result[0][csf("btb_system_id")];?></td>
								<td width="100" class="right"><? echo $result[0][csf("lc_date")]; ?></td>
								<td width="25"></td>
							</tr>
						</table>
						<table width="794" cellpadding="0" cellspacing="0" border="0" >
							<tr>
								<td colspan="3" height="20">&nbsp;</td>
							</tr>
							<br>
							<tr>
								<td width="25" ></td>
								<td width="650" align="left">
								<?
									echo 'To '.$designation."<br>".$bank_dtls_arr[$result[0][csf("issuing_bank_id")]]["bank_name"]."<br>".$bank_dtls_arr[$result[0][csf("issuing_bank_id")]]["address"];
								?>
								</td>
								<td width="25" ></td>
							</tr>
							<tr>
								<td colspan="3" height="30"></td>
							</tr>
							<tr>
								<td width="25" ></td>
								<td width="650" align="left">
								Sub: Opening of Back to Back L/C at sight under EDF Facility for <? echo $currency[$result[0][csf("currency_id")]]." ".$currency_sign_arr[$result[0][csf("currency_id")]]."&nbsp;".def_number_format($result[0][csf("lc_value")],2); ?> in favouring <? echo $supplier_name;?>, <?echo $supplier_address;?>, under Contract No. <? echo chop($sql_lc__arr[$result[0][csf("id")]]["lc_sc_no"],","); ?> Dated. <? echo change_date_format(chop($sql_lc__arr[$result[0][csf("id")]]["sc_lc_date"],","));?> for <? echo $currency[$result[0][csf("currency_id")]]." ".$currency_sign_arr[$result[0][csf("currency_id")]]."&nbsp;".def_number_format(chop($sql_lc__arr[$result[0][csf("id")]]["lc_sc_val"],","),2); ?> Shipment date <? echo change_date_format($result[0][csf("last_shipment_date")]);?>.
								</td>
								<td width="25" ></td>
							</tr>
							<tr>
								<td colspan="3" height="20"></td>
							</tr>
							<tr>
								<td colspan="3" height="20"></td>
							</tr>
							<tr>
								<td width="25" ></td>
								<td width="650" align="left"> Dear Sir, </td>
								<td width="25" ></td>
							</tr>
							<tr>
								<td colspan="3" height="15"></td>
							</tr>
							<tr>
								<td width="25" ></td>
								<td width="650" align="left">
								We enclose one set of L/C application duly filled in and signed alongwith Proforma Invoice accepted by us and other documents for opening of EDF L/C. </br>
								The Proposed import is fully covered by Current Import Policy Order and Exchange Control Regulations. 
								We shall remain bound to follow the Government Orders, directives governing and controlling Import under Back to Back L/C.
								We shall be held solely responsible for any miss-declaration regarding the commodity and entitlement to import under the proposed L/C. </br>
								We are fully aware of the terms and conditions as mentioned overleaf of the L/C application and we unconditionally and irrevocably agree to abide by those terms and conditions.
								We authorise you to debit our account in adjustment of any debit owing to you by us including purchase of foreign currency under WES from our account/PAD account for payment of usance import bills on due dates in case of our failure to export and submit negotiable export documents in time. </br>
								We undertake to adjust our liabilities if any arise, from our own resources or from subsequent export proceeds.
								</td>
								<td width="25" ></td>
							</tr>
							<tr>
								<td colspan="3" height="15"></td>
							</tr>
						</table>
						
						<table width="794" cellpadding="0" cellspacing="0" border="0">
							<tr>
								<td colspan="3" height="15"></td>
							</tr>
							<tr>
								<td width="25" ></td>
								<td width="650" align="left">
								Thanking You.<br>
								</td>
								<td width="25" ></td>
							</tr>
							<tr>
								<td colspan="3" height="25"></td>
							</tr>
							<tr>
								<td width="25" ></td>
								<td width="650" align="left">
								Yours faithfully,
								</td>
								<td width="25" ></td>
							</tr>
							<tr>
								<td width="25" ></td>
								<td width="650" align="left">
								<? echo "For ".$company_name;?><br>
								</td>
								<td width="25" ></td>
							</tr>
							<tr>
								<td colspan="3" height="100"></td>
							</tr>
							<tr>
								<td width="25" ></td>
								<td width="650" align="left">
								<div style="width:180px;border-top: 2px dashed;">(FAKIR KAMRUZZAMAN)</div>
								</td>
								<td width="25" ></td>
							</tr>
							<tr>
								<td width="25" ></td>
								<td width="650" align="left">
								<div style="width:180px;">MANAGING DIRECTOR.</div>
								</td>
								<td width="25" ></td>
							</tr>
							<tr>
								<td width="25" ></td>
								<td width="650" align="left">
								<div style="width:80px;border-bottom: 1px solid;">Enclosures:</div>
									1. L/C application form duly filled up and signed by us. </br>
									2. Beneficiary’s Proforma Invoice No. <?echo $pi_numbers;?>, Dt. <?echo $pi_date;?>
								</td>
								<td width="25" ></td>
							</tr>
						</table>
					</div>
				<?
			}
			else
			{
				?>
					<div class="a4size">
						<table width="794" cellpadding="0" cellspacing="0" border="0" >
							<tr>
								<td colspan="3" height="30"></td>
							</tr>
							<tr>
								<td width="25" ></td>
								<td width="650" >Ref.: <?=$result[0][csf("btb_system_id")];?></td>
								<td width="25"></td>
							</tr>
							<tr>
								<td width="25" ></td>
								<td width="650" class="right">Dated: <? echo change_date_format($result[0][csf("lc_date")]); ?></td>
								<td width="25"></td>
							</tr>
							<tr>
								<td colspan="3" height="20">&nbsp;</td>
							</tr>
							<br>
							<tr>
								<td width="25" ></td>
								<td width="650" align="left">
								<?
									echo 'To '.$designation."<br>".$bank_dtls_arr[$result[0][csf("issuing_bank_id")]]["bank_name"]."<br>".$bank_dtls_arr[$result[0][csf("issuing_bank_id")]]["address"];
								?>
								</td>
								<td width="25" ></td>
							</tr>
							<tr>
								<td colspan="3" height="30"></td>
							</tr>
							<tr>
								<td width="25" ></td>
								<td width="650" align="left">
								Sub: Opening of Back to Back L/C for <? echo $currency[$result[0][csf("currency_id")]]." ".$currency_sign_arr[$result[0][csf("currency_id")]]."&nbsp;".def_number_format($result[0][csf("lc_value")],2); ?> in favouring <? echo $supplier_name;?>, <?echo $supplier_address;?>, under Contract No. <? echo chop($sql_lc__arr[$result[0][csf("id")]]["lc_sc_no"],","); ?> Dated. <? echo change_date_format(chop($sql_lc__arr[$result[0][csf("id")]]["sc_lc_date"],","));?> for <? echo $currency[$result[0][csf("currency_id")]]." ".$currency_sign_arr[$result[0][csf("currency_id")]]."&nbsp;".def_number_format(chop($sql_lc__arr[$result[0][csf("id")]]["lc_sc_val"],","),2); ?> Shipment date <? echo change_date_format($result[0][csf("last_shipment_date")]);?>.
								</td>
								<td width="25" ></td>
							</tr>
							<tr>
								<td colspan="3" height="20"></td>
							</tr>
							<tr>
								<td colspan="3" height="20"></td>
							</tr>
							<tr>
								<td width="25" ></td>
								<td width="650" align="left"> Dear Sir, </td>
								<td width="25" ></td>
							</tr>
							<tr>
								<td colspan="3" height="15"></td>
							</tr>
							<tr>
								<td width="25" ></td>
								<td width="650" align="left">
								We enclose one set of L/C application duly filled in and signed along with Proforma Invoice accepted by us and other documents for opening of  L/C.<br>
								The Proposed import is fully covered by Current Import Policy Order and Exchange Control Regulations. 
								We shall remain bound to follow the Government Orders, directives governing and controlling Import under Back to Back L/C.<br>
								We shall be held solely responsible for any miss-declaration regarding the commodity and entitlement to import under the proposed L/C.<br>
								We are fully aware of the terms and conditions as mentioned overleaf of the L/C application and we unconditionally and irrevocably agree to abide by those terms and conditions.
								We authorise you to debit our account in adjustment of any debit owing to you by us including purchase of foreign currency under WES from our account/PAD account for payment of usance import bills on due dates in case of our failure to export and submit negotiable export documents in time.<br>
								We undertake to adjust our liabilities if any arise, from our own resources or from subsequent export proceeds.
								</td>
								<td width="25" ></td>
							</tr>
							<tr>
								<td colspan="3" height="15"></td>
							</tr>
						</table>
						
						<table width="794" cellpadding="0" cellspacing="0" border="0">
							<tr>
								<td colspan="3" height="15"></td>
							</tr>
							<tr>
								<td width="25" ></td>
								<td width="650" align="left">
								Thanking You.<br>
								</td>
								<td width="25" ></td>
							</tr>
							<tr>
								<td colspan="3" height="25"></td>
							</tr>
							<tr>
								<td width="25" ></td>
								<td width="650" align="left">
								Yours faithfully,
								</td>
								<td width="25" ></td>
							</tr>
							<tr>
								<td width="25" ></td>
								<td width="650" align="left">
								<? echo "For ".$company_name;?><br>
								</td>
								<td width="25" ></td>
							</tr>
							<tr>
								<td colspan="3" height="100"></td>
							</tr>
							<tr>
								<td width="25" ></td>
								<td width="650" align="left">
								<div style="width:180px;border-top: 2px dashed;">(FAKIR KAMRUZZAMAN).</div>
								</td>
								<td width="25" ></td>
							</tr>
							<tr>
								<td width="25" ></td>
								<td width="650" align="left">
								<div style="width:180px;">MANAGING DIRECTOR.</div>
								</td>
								<td width="25" ></td>
							</tr>
							<tr>
								<td width="25" ></td>
								<td width="650" align="left">
								<div style="width:80px;border-bottom: 1px solid;">Enclosures:</div>
									1. L/C application form duly filled up and signed by us. </br>
									2. Beneficiary’s Proforma Invoice No. <?echo $pi_numbers;?>, Dt. <?echo $pi_date;?>
								</td>
								<td width="25" ></td>
							</tr>
						</table>
					</div>
				<?
			}
		?>
    <?
	exit();
}

if($action=="btb_import_lc_letter8")
{
	//echo load_html_head_contents("BTB Letter","../../", 1, 1, $unicode,'','');
	$currency_sign_arr=array(1=>'৳',2=>'$',3=>'€',4=>'€',5=>'$',6=>'£',7=>'¥');
	$sql_bank_info=sql_select("select id, contact_person, bank_name, branch_name, address,designation from lib_bank ");
	foreach($sql_bank_info as $row)
	{
		$bank_dtls_arr[$row[csf("id")]]["contact_person"]=$row[csf("contact_person")];
		$bank_dtls_arr[$row[csf("id")]]["bank_name"]=$row[csf("bank_name")];
		$bank_dtls_arr[$row[csf("id")]]["branch_name"]=$row[csf("branch_name")];
		$bank_dtls_arr[$row[csf("id")]]["address"]=$row[csf("address")];
		$bank_dtls_arr[$row[csf("id")]]["designation"]=$row[csf("designation")];
	}

	// echo $data;die;
	$sql_com="SELECT a.id,a.btb_system_id, a.application_date, a.importer_id, a.item_category_id, a.origin, a.issuing_bank_id, a.currency_id, a.supplier_id, a.lc_value, a.margin,a.lc_date,a.tenor,a.last_shipment_date,a.lc_expiry_date,a.upas_rate,a.cover_note_no,a.cover_note_date, a.lcaf_no,a.pi_id,a.payterm_id
	from com_btb_lc_master_details a
	where a.id=$data and a.is_deleted = 0 AND a.status_active = 1 group by a.id,a.btb_system_id, a.application_date, a.importer_id, a.item_category_id, a.origin, a.issuing_bank_id, a.currency_id, a.supplier_id, a.lc_value, a.margin,a.lc_date,a.tenor,a.last_shipment_date,a.lc_expiry_date,a.upas_rate,a.cover_note_no,a.cover_note_date, a.lcaf_no,a.pi_id,a.payterm_id";

	//echo $sql_com;
	$result=sql_select($sql_com);

	$country_array = return_library_array("SELECT id,country_name from lib_country ","id","country_name");
	$comp_library = return_library_array('SELECT id, company_name from lib_company','id','company_name');
	$company_name = return_field_value("company_name","lib_company","id=".$result[0][csf("importer_id")],"company_name");
	$supplier_name = return_field_value("supplier_name","lib_supplier","id=".$result[0][csf("supplier_id")],"supplier_name");
	$supplier_address = return_field_value("address_1","lib_supplier","id=".$result[0][csf("supplier_id")],"address_1");
	$country_name = return_field_value("country_name"," lib_country","id=".$result[0][csf("origin")],"country_name");
	$designation = return_field_value("custom_designation","lib_designation","id=".$bank_dtls_arr[$result[0][csf("issuing_bank_id")]]["designation"],"custom_designation");
	$address = sql_select("SELECT id,plot_no,level_no,road_no,block_no,country_id,city,zip_code,email,contact_no from lib_company");
	$company_address=array();
	foreach($address as $row){
		if($row[csf('plot_no')]!=''){$company_address[$row[csf('id')]].=$row[csf('plot_no')];}
		if($row[csf('level_no')]!=''){$company_address[$row[csf('id')]].=$row[csf('level_no')];}
		if($row[csf('road_no')]!=''){$company_address[$row[csf('id')]].=", ".$row[csf('road_no')];}
		if($row[csf('block_no')]!=''){$company_address[$row[csf('id')]].=", ".$row[csf('block_no')];}
		if($row[csf('city')]!=''){$company_address[$row[csf('id')]].=", ".$row[csf('city')];}
		if($row[csf('zip_code')]!=''){$company_address[$row[csf('id')]].=", ".$row[csf('zip_code')];}
		if($row[csf('country_id')]!=''){$company_address[$row[csf('id')]].=", ".$country_array[$company_add['country_id']];}
	}

	$buyer_result = sql_select('SELECT id as ID, buyer_name as BUYER_NAME,address_1 as ADDRESS_1 from lib_buyer');
	$buyer_info=array();
	foreach($buyer_result as $row)
	{
		$buyer_info['ID']['buyer_name']=$row['buyer_name'];
		$buyer_info['ID']['address']=$row['address_1'];
	}
	$ref_arr=explode('-',$result[0][csf("btb_system_id")]);

	$sql_pi=sql_select("SELECT a.import_mst_id, b.contract_no as lc_sc_no, b.contract_value as lc_sc_val , b.contract_date as sc_lc_date, 0 as import_btb, b.buyer_name
	from  com_btb_export_lc_attachment a, com_sales_contract b
	where a.lc_sc_id=b.id and a.status_active=1 and a.is_lc_sc=1 and a.import_mst_id=$data
	union all 
	select a.import_mst_id, b.export_lc_no as lc_sc_no, b.lc_value as lc_sc_val, b.lc_date as sc_lc_date, b.import_btb as import_btb, b.buyer_name
	from com_btb_export_lc_attachment a, com_export_lc b
	where a.lc_sc_id=b.id and a.status_active=1 and a.is_lc_sc=0 and a.import_mst_id=$data");
	$sql_lc__arr=array();
	foreach($sql_pi as $row)
	{
		$sql_lc__arr[$row[csf("import_mst_id")]]["lc_sc_no"]=$row[csf("lc_sc_no")].",";
		$sql_lc__arr[$row[csf("import_mst_id")]]["lc_sc_val"]=$row[csf("lc_sc_val")].",";
		$sql_lc__arr[$row[csf("import_mst_id")]]["sc_lc_date"]=$row[csf("sc_lc_date")].",";
		if($row[csf("import_btb")]==1)
		{
			$sql_lc__arr[$row[csf("import_mst_id")]]["buyer_name"]=$comp_library[$row[csf("buyer_name")]].",";
			$sql_lc__arr[$row[csf("import_mst_id")]]["buyer_address"]=$company_address[$row[csf("buyer_name")]].",";
		}else{
			$sql_lc__arr[$row[csf("import_mst_id")]]["buyer_name"]=$buyer_info[$row[csf("buyer_name")]].",";
			$sql_lc__arr[$row[csf("import_mst_id")]]["buyer_address"]=$buyer_info[$row[csf("buyer_name")]].",";
		}
	}
	$pi_number_arr=sql_select( "SELECT id, pi_number,pi_date,item_category_id from com_pi_master_details where id in(".$result[0][csf("pi_id")].")  AND status_active = 1 AND is_deleted = 0");

	if(count($pi_number_arr)>1){
		foreach($pi_number_arr as $row){
			$pi_numbers .= $row[csf('pi_number')].", ";
			$pi_date .= change_date_format($row[csf('pi_date')]).", ";
		}
		$pi_numbers = chop($pi_numbers,", ");
		$pi_date = chop($pi_date,", ");
		$itemCategory = $item_category[$pi_number_arr[0][csf('item_category_id')]];

	}
	else
	{
		$pi_numbers = $pi_number_arr[0][csf('pi_number')];
		$pi_date = change_date_format($pi_number_arr[0][csf('pi_date')]);
		$itemCategory = $item_category[$pi_number_arr[0][csf('item_category_id')]];

	}
	$factory_arr = sql_select("select id,address from lib_location where company_id='".$result[0][csf("importer_id")]."' and is_deleted=0");
	$factory_add='';
	foreach($factory_arr as $factory){
		if($factory_add!='' && $factory[csf('address')] !=''){ $factory_add.= ", ".$factory[csf('address')]; }else{ $factory_add.= $factory[csf('address')]; }
	}
	?>

		<style type="text/css">
			.a4size {
	           width: 21cm;
	           height: 26.7cm;
	           font-family: Cambria, Georgia, serif;
	        }
	        @media print {
	        .a4size{ font-family: Cambria;font-size: 18px;margin: 30px 100PX 54px 25px;size: A4 portrait;
	            }
	        }
			.wrd_brk{word-break: break-all;}
			.left{text-align: left;}
			.center{text-align: center;}
			.right{text-align: right;}
		</style>
		<?
			if($result[0][csf("payterm_id")]==1)
			{
				?>
					<div class="a4size">
						<table width="794" cellpadding="0" cellspacing="0" border="0" >
							<tr>
								<td colspan="3" height="30"></td>
							</tr>
							<tr>
								<td width="25" ></td>
								<!-- <td width="550" >Ref.:<?=$ref_arr[0]."/DBBL-".$ref_arr[3]."/20".$ref_arr[2];?></td> -->
								<td width="550" >Ref.: <?=$result[0][csf("btb_system_id")];?></td>
								<td width="100" class="right"><? echo $result[0][csf("lc_date")]; ?></td>
								<td width="25"></td>
							</tr>
						</table>
						<table width="794" cellpadding="0" cellspacing="0" border="0" >
							<tr>
								<td colspan="3" height="20">&nbsp;</td>
							</tr>
							<br>
							<tr>
								<td width="25" ></td>
								<td width="650" align="left">
								<?
									echo 'To '.$designation."<br>".$bank_dtls_arr[$result[0][csf("issuing_bank_id")]]["bank_name"]."<br>".$bank_dtls_arr[$result[0][csf("issuing_bank_id")]]["branch_name"]."<br>".$bank_dtls_arr[$result[0][csf("issuing_bank_id")]]["address"];
								?>
								</td>
								<td width="25" ></td>
							</tr>
							<tr>
								<td colspan="3" height="30"></td>
							</tr>
							<tr>
								<td width="25" ></td>
								<td width="650" align="left">
								Sub: Opening of Back to Back L/C at sight under EDF Facility for <? echo $currency[$result[0][csf("currency_id")]]." ".$currency_sign_arr[$result[0][csf("currency_id")]]."&nbsp;".def_number_format($result[0][csf("lc_value")],2); ?> in favouring <? echo $supplier_name;?>, <?echo $supplier_address;?>, under Contract No. <? echo chop($sql_lc__arr[$result[0][csf("id")]]["lc_sc_no"],","); ?> Dated. <? echo change_date_format(chop($sql_lc__arr[$result[0][csf("id")]]["sc_lc_date"],","));?> for <? echo $currency[$result[0][csf("currency_id")]]." ".$currency_sign_arr[$result[0][csf("currency_id")]]."&nbsp;".def_number_format(chop($sql_lc__arr[$result[0][csf("id")]]["lc_sc_val"],","),2); ?> Shipment date <? echo change_date_format($result[0][csf("last_shipment_date")]);?>.
								</td>
								<td width="25" ></td>
							</tr>
							<tr>
								<td colspan="3" height="20"></td>
							</tr>
							<tr>
								<td colspan="3" height="20"></td>
							</tr>
							<tr>
								<td width="25" ></td>
								<td width="650" align="left"> Dear Sir, </td>
								<td width="25" ></td>
							</tr>
							<tr>
								<td colspan="3" height="15"></td>
							</tr>
							<tr>
								<td width="25" ></td>
								<td width="650" align="left">
									We enclose one set of L/C application duly filled in and signed alongwith Proforma Invoice accepted by us and other documents for opening of Back to Back L/C at sight under EDF Facility. <br>
									The Proposed import is fully covered by Current Import Policy Order and Exchange Control Regulations. 
									We shall remain bound to follow the Government Orders, directives governing and controlling Import under Back to Back L/C. <br>
									We shall be held solely responsible for any miss-declaration regarding the commodity and entitlement to import under the proposed L/C. <br>
									We are fully aware of the terms and conditions as mentioned overleaf of the L/C application and we unconditionally and irrevocably agree to abide by those terms and conditions. <br>
									We authorise you to debit our account in adjustment of any debit owing to you by us including purchase of foreign currency under WES from our account/PAD account for payment of usance import bills on due dates in case of our failure to export and submit negotiable export documents in time. <br>
									We undertake to adjust our liabilities if any arise, from our own resources or from subsequent export proceeds.
								</td>
								<td width="25" ></td>
							</tr>
							<tr>
								<td colspan="3" height="15"></td>
							</tr>
						</table>
						
						<table width="794" cellpadding="0" cellspacing="0" border="0">
							<tr>
								<td colspan="3" height="15"></td>
							</tr>
							<tr>
								<td width="25" ></td>
								<td width="650" align="left">
								Thanking You.<br>
								</td>
								<td width="25" ></td>
							</tr>
							<tr>
								<td colspan="3" height="25"></td>
							</tr>
							<tr>
								<td width="25" ></td>
								<td width="650" align="left">
								Yours faithfully,
								</td>
								<td width="25" ></td>
							</tr>
							<tr>
								<td width="25" ></td>
								<td width="650" align="left">
								<? echo "For ".$company_name;?><br>
								</td>
								<td width="25" ></td>
							</tr>
							<tr>
								<td colspan="3" height="100"></td>
							</tr>
							<tr>
								<td width="25" ></td>
								<td width="650" align="left">
								<div style="width:180px;border-top: 2px dashed;">(FAKIR KAMRUZZAMAN)</div>
								</td>
								<td width="25" ></td>
							</tr>
							<tr>
								<td width="25" ></td>
								<td width="650" align="left">
								<div style="width:180px;">MANAGING DIRECTOR.</div>
								</td>
								<td width="25" ></td>
							</tr>
							<tr>
								<td width="25" ></td>
								<td width="650" align="left">
								<div style="width:80px;border-bottom: 1px solid;">Enclosures:</div>
									1. L/C application form duly filled up and signed by us. </br>
									2. Beneficiary’s Proforma Invoice No. <?echo $pi_numbers;?>, Dt. <?echo $pi_date;?>
								</td>
								<td width="25" ></td>
							</tr>
						</table>
					</div>
				<?
			}
			else
			{
				?>
					<div class="a4size">
						<table width="794" cellpadding="0" cellspacing="0" border="0" >
							<tr>
								<td colspan="3" height="30"></td>
							</tr>
							<tr>
								<td width="25" ></td>
								<td width="650" >Ref.: <?=$result[0][csf("btb_system_id")];?></td>
								<td width="25"></td>
							</tr>
							<tr>
								<td width="25" ></td>
								<td width="650" class="right">Dated: <? echo change_date_format($result[0][csf("lc_date")]); ?></td>
								<td width="25"></td>
							</tr>
							<tr>
								<td colspan="3" height="20">&nbsp;</td>
							</tr>
							<br>
							<tr>
								<td width="25" ></td>
								<td width="650" align="left">
								<?
									echo 'To '.$designation."<br>".$bank_dtls_arr[$result[0][csf("issuing_bank_id")]]["bank_name"]."<br>".$bank_dtls_arr[$result[0][csf("issuing_bank_id")]]["branch_name"].$bank_dtls_arr[$result[0][csf("issuing_bank_id")]]["address"];
								?>
								</td>
								<td width="25" ></td>
							</tr>
							<tr>
								<td colspan="3" height="30"></td>
							</tr>
							<tr>
								<td width="25" ></td>
								<td width="650" align="left">
								Sub: Opening of Back to Back L/C for <? echo $currency[$result[0][csf("currency_id")]]." ".$currency_sign_arr[$result[0][csf("currency_id")]]."&nbsp;".def_number_format($result[0][csf("lc_value")],2); ?> in favouring <? echo $supplier_name;?>, <?echo $supplier_address;?>, under Contract No. <? echo chop($sql_lc__arr[$result[0][csf("id")]]["lc_sc_no"],","); ?> Dated. <? echo change_date_format(chop($sql_lc__arr[$result[0][csf("id")]]["sc_lc_date"],","));?> for <? echo $currency[$result[0][csf("currency_id")]]." ".$currency_sign_arr[$result[0][csf("currency_id")]]."&nbsp;".def_number_format(chop($sql_lc__arr[$result[0][csf("id")]]["lc_sc_val"],","),2); ?> Shipment date <? echo change_date_format($result[0][csf("last_shipment_date")]);?>.
								</td>
								<td width="25" ></td>
							</tr>
							<tr>
								<td colspan="3" height="20"></td>
							</tr>
							<tr>
								<td colspan="3" height="20"></td>
							</tr>
							<tr>
								<td width="25" ></td>
								<td width="650" align="left"> Dear Sir, </td>
								<td width="25" ></td>
							</tr>
							<tr>
								<td colspan="3" height="15"></td>
							</tr>
							<tr>
								<td width="25" ></td>
								<td width="650" align="left">
									We enclose one set of L/C application duly filled in and signed along with Proforma Invoice accepted by us and other documents for opening of Back to Back L/C. <br>
									The Proposed import is fully covered by Current Import Policy Order and Exchange Control Regulations. 
									We shall remain bound to follow the Government Orders, directives governing and controlling Import under Back to Back L/C. <br>
									We shall be held solely responsible for any miss-declaration regarding the commodity and entitlement to import under the proposed L/C. <br>
									We are fully aware of the terms and conditions as mentioned overleaf of the L/C application and we unconditionally and irrevocably agree to abide by those terms and conditions. <br>
									We authorise you to debit our account in adjustment of any debit owing to you by us including purchase of foreign currency under WES from our account/PAD account for payment of usance import bills on due dates in case of our failure to export and submit negotiable export documents in time. <br>
									We undertake to adjust our liabilities if any arise, from our own resources or from subsequent export proceeds. <br>
								</td>
								<td width="25" ></td>
							</tr>
							<tr>
								<td colspan="3" height="15"></td>
							</tr>
						</table>
						
						<table width="794" cellpadding="0" cellspacing="0" border="0">
							<tr>
								<td colspan="3" height="15"></td>
							</tr>
							<tr>
								<td width="25" ></td>
								<td width="650" align="left">
								Thanking You.<br>
								</td>
								<td width="25" ></td>
							</tr>
							<tr>
								<td colspan="3" height="25"></td>
							</tr>
							<tr>
								<td width="25" ></td>
								<td width="650" align="left">
								Yours faithfully,
								</td>
								<td width="25" ></td>
							</tr>
							<tr>
								<td width="25" ></td>
								<td width="650" align="left">
								<? echo "For ".$company_name;?><br>
								</td>
								<td width="25" ></td>
							</tr>
							<tr>
								<td colspan="3" height="100"></td>
							</tr>
							<tr>
								<td width="25" ></td>
								<td width="650" align="left">
								<div style="width:180px;border-top: 2px dashed;">(FAKIR KAMRUZZAMAN)</div>
								</td>
								<td width="25" ></td>
							</tr>
							<tr>
								<td width="25" ></td>
								<td width="650" align="left">
								<div style="width:180px;">MANAGING DIRECTOR.</div>
								</td>
								<td width="25" ></td>
							</tr>
							<tr>
								<td width="25" ></td>
								<td width="650" align="left">
								<div style="width:80px;border-bottom: 1px solid;">Enclosures:</div>
									1. L/C application form duly filled up and signed by us. </br>
									2. Beneficiary’s Proforma Invoice No. <?echo $pi_numbers;?>, Dt. <?echo $pi_date;?>
								</td>
								<td width="25" ></td>
							</tr>
						</table>
					</div>
				<?
			}
		?>
    <?
	exit();
}

if($action=="btb_import_lc_letter9")
{
	//echo load_html_head_contents("BTB Letter","../../", 1, 1, $unicode,'','');
	$currency_sign_arr=array(1=>'৳',2=>'$',3=>'€',4=>'€',5=>'$',6=>'£',7=>'¥');
	$sql_bank_info=sql_select("select id, contact_person, bank_name, branch_name, address,designation from lib_bank ");
	foreach($sql_bank_info as $row)
	{
		$bank_dtls_arr[$row[csf("id")]]["contact_person"]=$row[csf("contact_person")];
		$bank_dtls_arr[$row[csf("id")]]["bank_name"]=$row[csf("bank_name")];
		$bank_dtls_arr[$row[csf("id")]]["branch_name"]=$row[csf("branch_name")];
		$bank_dtls_arr[$row[csf("id")]]["address"]=$row[csf("address")];
		$bank_dtls_arr[$row[csf("id")]]["designation"]=$row[csf("designation")];
	}

	// echo $data;die;
	$sql_com="SELECT a.id as ID, a.application_date as APPLICATION_DATE, a.importer_id as IMPORTER_ID, a.origin as ORIGIN, a.issuing_bank_id as ISSUING_BANK_ID, a.currency_id as CURRENCY_ID, a.supplier_id as SUPPLIER_ID,a.lc_type_id as LC_TYPE_ID, a.lc_value as LC_VALUE, a.margin as MARGIN,a.pi_id as PI_ID,pi_value as PI_VALUE,a.advising_bank as ADVISING_BANK,a.advising_bank_address as ADVISING_BANK_ADDRESS
	from com_btb_lc_master_details a
	where a.id=$data and a.is_deleted = 0 AND a.status_active = 1";

	//echo $sql_com;
	$result=sql_select($sql_com);

	$country_array = return_library_array("select id,country_name from lib_country where is_deleted=0","id","country_name");
	$company_name = return_field_value("company_name","lib_company","id=".$result[0]["IMPORTER_ID"],"company_name");
	$supplier_name = return_field_value("supplier_name","lib_supplier","id=".$result[0]["SUPPLIER_ID"],"supplier_name");
	// $supplier_address = return_field_value("address_1","lib_supplier","id=".$result[0][csf("supplier_id")],"address_1");
	// $country_name = return_field_value("country_name"," lib_country","id=".$result[0][csf("origin")],"country_name");
	$designation = return_field_value("custom_designation","lib_designation","id=".$bank_dtls_arr[$result[0]["ISSUING_BANK_ID"]]["designation"],"custom_designation");
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1 and master_tble_id='".$result[0]["IMPORTER_ID"]."'",'master_tble_id','image_location');

	$address = sql_select("SELECT id,plot_no,level_no,road_no,block_no,country_id,city,zip_code,email,contact_no from lib_company where id = ".$result[0]['IMPORTER_ID']." and is_deleted = 0 and status_active = 1 ");
	$company_add=array();
	foreach($address as $row){
		$company_add['plot_no'] = $row[csf('plot_no')];
		$company_add['level_no'] = $row[csf('level_no')];
		$company_add['road_no'] = $row[csf('road_no')];
		$company_add['block_no'] = $row[csf('block_no')];
		$company_add['country_id'] = $row[csf('country_id')];
		$company_add['city'] = $row[csf('city')];
		$company_add['zip_code'] = $row[csf('zip_code')];
	}
	$company_address='';
	if($company_add['plot_no']!=''){$company_address.="Visiting Address : ".$company_add['plot_no'];}
	if($company_add['level_no']!=''){$company_address.=$company_add['level_no'];}
	if($company_add['road_no']!=''){$company_address.=", ".$company_add['road_no'];}
	if($company_add['block_no']!=''){$company_address.=", ".$company_add['block_no'];}
	if($company_add['city']!=''){$company_address.=", ".$company_add['city'];}
	if($company_add['zip_code']!=''){$company_address.=", ".$company_add['zip_code'];}
	if($company_add['country_id']!=''){$company_address.=", ".$country_array[$company_add['country_id']];}
	
	$pi_number_arr=sql_select( "SELECT id as ID, pi_number as PI_NUMBER,pi_date as PI_DATE from com_pi_master_details where id in(".$result[0]["PI_ID"].")  AND status_active = 1 AND is_deleted = 0");

	if(count($pi_number_arr)>1){
		foreach($pi_number_arr as $row){
			$pi_numbers .= $row['PI_NUMBER'].", ";
			$pi_date .= change_date_format($row['PI_DATE']).", ";
		}
		$pi_numbers = chop($pi_numbers,", ");
		$pi_date = chop($pi_date,", ");
	}
	else
	{
		$pi_numbers = $pi_number_arr[0]['PI_NUMBER'];
		$pi_date = change_date_format($pi_number_arr[0]['PI_DATE']);		
	}
	$sql_pi=sql_select("SELECT a.import_mst_id, b.contract_no as lc_sc_no, b.contract_value as lc_sc_val , b.contract_date as sc_lc_date, 0 as import_btb, b.buyer_name
	from  com_btb_export_lc_attachment a, com_sales_contract b
	where a.lc_sc_id=b.id and a.status_active=1 and a.is_lc_sc=1 and a.import_mst_id=$data
	union all 
	select a.import_mst_id, b.export_lc_no as lc_sc_no, b.lc_value as lc_sc_val, b.lc_date as sc_lc_date, b.import_btb as import_btb, b.buyer_name
	from com_btb_export_lc_attachment a, com_export_lc b
	where a.lc_sc_id=b.id and a.status_active=1 and a.is_lc_sc=0 and a.import_mst_id=$data");
	$sql_lc__arr=array();
	foreach($sql_pi as $row)
	{
		$sql_lc__arr[$row[csf("import_mst_id")]]["lc_sc_no"]=$row[csf("lc_sc_no")].",";
		$sql_lc__arr[$row[csf("import_mst_id")]]["lc_sc_val"]=$row[csf("lc_sc_val")].",";
		$sql_lc__arr[$row[csf("import_mst_id")]]["lc_sc_date"]=$row[csf("sc_lc_date")].",";
		// if($row[csf("import_btb")]==1)
		// {
		// 	$sql_lc__arr[$row[csf("import_mst_id")]]["buyer_name"]=$comp_library[$row[csf("buyer_name")]].",";
		// 	$sql_lc__arr[$row[csf("import_mst_id")]]["buyer_address"]=$company_address[$row[csf("buyer_name")]].",";
		// }else{
		// 	$sql_lc__arr[$row[csf("import_mst_id")]]["buyer_name"]=$buyer_info[$row[csf("buyer_name")]].",";
		// 	$sql_lc__arr[$row[csf("import_mst_id")]]["buyer_address"]=$buyer_info[$row[csf("buyer_name")]].",";
		// }
	}
	$bank_sql=sql_select("SELECT a.id as ID, b.account_type as ACCOUNT_TYPE, b.account_no as ACCOUNT_NO from LIB_BANK a, LIB_BANK_ACCOUNT b where a.id=b.account_id and b.account_type=6");
	$bank_data=array();
	foreach($bank_sql as $row)
	{
		$bank_data[$row["ID"]]["ACCOUNT_NO"]=$row["ACCOUNT_NO"];
	}

	$factory_arr = sql_select("SELECT id as ID,address as ADDRESS,remark as REMARKS,contact_no as CONTACT_NO,email as EMAIL from lib_location where company_id='".$result[0]["IMPORTER_ID"]."' and is_deleted=0");

	$factory_add=$factory_tel_fax_number=$factory_mobile_number=$factory_email_number='';
	foreach($factory_arr as $factory)
	{
		if($factory_add!='' && $factory['ADDRESS'] !=''){ $factory_add.= ", ".$factory['ADDRESS']; }else{ $factory_add.= $factory['ADDRESS']; }
		if($factory_tel_fax_number!='' && $factory['REMARKS'] !=''){ $factory_tel_fax_number.= ", ".$factory['REMARKS']; }else{ $factory_tel_fax_number.= $factory['REMARKS']; }
		if($factory_mobile_number!='' && $factory['CONTACT_NO'] !=''){ $factory_mobile_number.= ", +88-".$factory['CONTACT_NO']; }else{ $factory_mobile_number.= "+88-".$factory['CONTACT_NO']; }
		if($factory_email_number!='' && $factory['EMAIL'] !=''){ $factory_email_number.= ", ".$factory['EMAIL']; }else{ $factory_email_number.= $factory['EMAIL']; }
	}
	?>

	<style type="text/css">
		#footer {
			margin:100px 0px 0px 25px;
		}
		.a4size {
			width: 21cm;
			height: 26.7cm;
			font-family: Cambria, Georgia, serif;
		}
		@media print {
			.a4size{ 
				font-family: Cambria;font-size: 18px;margin: 30px 100PX 54px 25px;size: A4 portrait;
			}
			#footer {
				position: absolute;
				bottom: 0;
				width: 100%;
			}
		}
	</style>
	<div class="a4size">
		<table width="794" cellpadding="0" cellspacing="0" border="0" >
			<tr>
				<td width="25" ></td>
				<td width="650" align="center">
				<img  src="../../<? echo $imge_arr[$result[0]['IMPORTER_ID']]; ?>" height='75' width='300' /> </td>
				<td width="25" ></td>
			</tr>
			<tr>
				<td colspan="3" height="30"></td>
			</tr>
			<tr>
				<td width="25" ></td>
				<td width="650" align="left"><? echo date("l, d F, Y",strtotime($result[0]["APPLICATION_DATE"])); ?> </td>
				<td width="25" ></td>
			</tr>
			<tr>
				<td colspan="3" height="20">&nbsp;</td>
			</tr>
			<br>
			<tr>
				<td width="25"></td>
				<td width="650" align="left">To</td>
				<td width="25" ></td>
			</tr>
			<tr>
				<td width="25" ></td>
				<td width="650" align="left">
				<?
					echo 'The '.$designation."<br>".$bank_dtls_arr[$result[0]["ISSUING_BANK_ID"]]["bank_name"]."<br>".$bank_dtls_arr[$result[0]["ISSUING_BANK_ID"]]["address"];
				?>
				</td>
				<td width="25" ></td>
			</tr>
			<tr>
				<td colspan="3" height="30"></td>
			</tr>
			<tr>
				<td width="25" ></td>
				<td width="650" align="left">
				Sub –Request payment <?echo $lc_type[$result[0]["LC_TYPE_ID"]];?> for <? echo $currency_sign_arr[$result[0]["CURRENCY_ID"]]."&nbsp;".def_number_format($result[0]["PI_VALUE"],2); ?> in favour of <? echo $supplier_name; ?>.
				</td>
				<td width="25" ></td>
			</tr>
			<tr>
				<td colspan="3" height="20"></td>
			</tr>
			<tr>
				<td colspan="3" height="20"></td>
			</tr>
			<tr>
				<td width="25" ></td>
				<td width="650" align="left"> Dear Sir, </td>
				<td width="25" ></td>
			</tr>
			<tr>
				<td colspan="3" height="15"></td>
			</tr>
			<tr>
				<td width="25" ></td>
				<td width="650" align="left">
					We request you to payment a <?echo $lc_type[$result[0]["LC_TYPE_ID"]];?> according to attach Proforma Invoice.<br>
					Enclosed the proforma invoice No: <?echo $pi_numbers;?> date: <?echo $pi_date;?>
				</td>
				<td width="25" ></td>
			</tr>
			<tr>
				<td colspan="3" height="15"></td>
			</tr>
			<tr>
				<td width="25" ></td>
				<td width="650" align="left">
					BANK NAME : <?echo $result[0]["ADVISING_BANK"];
					$acc_swift=explode(',',$result[0]["ADVISING_BANK_ADDRESS"]);?><br>
					ACCOUNT NO.:  <?echo $acc_swift[0];?><br>
					SWIFT NO: <?echo $acc_swift[1];?>
				</td>
				<td width="25" ></td>
			</tr>
			<tr>
				<td colspan="3" height="20"></td>
			</tr>
			<tr>
				<td width="25" ></td>
				<td width="650" align="left">
					<strong>Note:</strong> The above said amount will be Debit from our Account No: <? echo $bank_data[$result[0]["ISSUING_BANK_ID"]]["ACCOUNT_NO"]?><br>
					Will be open <?echo $lc_type[$result[0]["LC_TYPE_ID"]];?> against Export SC/LC No # <?echo rtrim($sql_lc__arr[$result[0][csf("id")]]["lc_sc_no"],',');?>, Date –<?echo rtrim($sql_lc__arr[$result[0][csf("id")]]["lc_sc_date"],',');?>, 
					Amount – <?echo $currency[$result[0][csf("currency_id")]].''.$currency_sign_arr[$result[0]["CURRENCY_ID"]].' '.rtrim($sql_lc__arr[$result[0][csf("id")]]["lc_sc_val"],',');?>
				</td>
				<td width="25" ></td>
			</tr>
		</table>	  
		<table width="794" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td colspan="3" height="15"></td>
			</tr>
			<tr>
				<td width="25" ></td>
				<td width="650" align="left">
				Thanking You.<br>
				</td>
				<td width="25" ></td>
			</tr>
			<tr>
				<td width="25" ></td>
				<td width="650" align="left">
				<? echo $company_name;?><br>
				</td>
				<td width="25" ></td>
			</tr>
			<tr>
				<td colspan="3" height="190"></td>
			</tr>
			<tr>
				<td width="25" ></td>
				<td width="650" align="left">
				<div style="width:180px;border-top: 2px solid;">Authorized Signature</div>
				</td>
				<td width="25" ></td>
			</tr>
		</table>
		<div id="footer"><? echo "Factory Address : ".$factory_add."<br>Tel - ".$factory_tel_fax_number.", Mobile - ".$factory_mobile_number."<br>E-mail - ".$factory_email_number;?>
		</div>
	</div>
    <?
	exit();
}

if ($action==='btb_import_lc_letter10')
{
	echo load_html_head_contents("BTB Letter","../../", 1, 1, $unicode,'','');
	$data=explode("**",$data);
	$currency_sign_arr=array(1=>'৳',2=>'$',3=>'€',4=>'€',5=>'$',6=>'£',7=>'¥');
	$company_arr = return_library_array('SELECT id, company_name FROM lib_company','id','company_name');
	$company_arr_short=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$country_array = return_library_array("select id,country_name from lib_country where is_deleted=0","id","country_name");
	$back_sql=sql_select("select a.ID, b.ACCOUNT_TYPE, b.ACCOUNT_NO from LIB_BANK a, LIB_BANK_ACCOUNT b where a.id=b.account_id and b.account_type in(6,10) and b.COMPANY_ID=$data[1]");

	$bank_data=array();
	foreach($back_sql as $row)
	{
		if($row["ACCOUNT_TYPE"]==6) $bank_data[$row["ID"]]["ACCOUNT_NO_ERQ"]=$row["ACCOUNT_NO"];
		if($row["ACCOUNT_TYPE"]==10) $bank_data[$row["ID"]]["ACCOUNT_NO_CD"]=$row["ACCOUNT_NO"];
	}
	$supplier_sql = sql_select('SELECT id, supplier_name, address_1, short_name FROM lib_supplier');
	foreach($supplier_sql as $row)
	{
		$supplier_arr[$row[csf("id")]]["supplier_name"]=$row[csf("supplier_name")];
		$supplier_arr[$row[csf("id")]]["address_1"]=$row[csf("address_1")];
		$supplier_arr[$row[csf("id")]]["short_name"]=$row[csf("short_name")];
	}
	$sql_bank_info = sql_select("SELECT ID, BANK_NAME,BANK_SHORT_NAME, BRANCH_NAME, ADDRESS from lib_bank ");
	$bank_dtls_arr=array();
	foreach($sql_bank_info as $row)
	{
		$bank_dtls_arr[$row['ID']]['BANK_NAME']=$row['BANK_NAME'];
		$bank_dtls_arr[$row['ID']]['BRANCH_NAME']=$row['BRANCH_NAME'];
		$bank_dtls_arr[$row['ID']]['ADDRESS']=$row['ADDRESS'];
		$bank_dtls_arr[$row['ID']]['BANK_SHORT_NAME']=$row['BANK_SHORT_NAME'];
	}
	$sql_btb="SELECT ID, LC_DATE, APPLICATION_DATE, IMPORTER_ID, ISSUING_BANK_ID, CURRENCY_ID, SUPPLIER_ID, LC_VALUE, LC_EXPIRY_DATE, TENOR, PI_ID,  ORIGIN, LC_TYPE_ID, ADVISING_BANK, ADVISING_BANK_ADDRESS, LCAF_NO, COVER_NOTE_NO
	from com_btb_lc_master_details
	where id=$data[0] and is_deleted=0 and status_active=1";
	$sql_btb_res=sql_select($sql_btb);
	$btb_id = $sql_btb_res[0]['ID'];
	$lc_value = $sql_btb_res[0]['LC_VALUE'];
	$application_date = $sql_btb_res[0]['APPLICATION_DATE'];
	$expire_date = $sql_btb_res[0]['LC_EXPIRY_DATE'];
	$importer_name = $company_arr[$sql_btb_res[0]['IMPORTER_ID']];
	$supplier_name = $supplier_arr[$sql_btb_res[0]['SUPPLIER_ID']]["supplier_name"];
	$supplier_address = $supplier_arr[$sql_btb_res[0]['SUPPLIER_ID']]["address_1"];
	$supplier_short_name = $supplier_arr[$sql_btb_res[0]['SUPPLIER_ID']]["short_name"];
	$origin_name = $country_arr[$sql_btb_res[0]['ORIGIN']];
	$issuing_bank_id = $sql_btb_res[0]['ISSUING_BANK_ID'];
	$swift_code=$bank_data[$issuing_bank_id]["SWIFT_CODE"];
	$erq_no=$bank_data[$issuing_bank_id]["ACCOUNT_NO_ERQ"];
	$cd_no=$bank_data[$issuing_bank_id]["ACCOUNT_NO_CD"];
	$bank_name=$bank_dtls_arr[$sql_btb_res[0]['ISSUING_BANK_ID']]['BANK_NAME'];
	$bank_branch=$bank_dtls_arr[$sql_btb_res[0]['ISSUING_BANK_ID']]['BRANCH_NAME'];
	$bank_address=	$bank_dtls_arr[$sql_btb_res[0]['ISSUING_BANK_ID']]['ADDRESS'];
	$bank_short_name=	$bank_dtls_arr[$sql_btb_res[0]['ISSUING_BANK_ID']]['BANK_SHORT_NAME'];
	$lc_type_value= $lc_type[$sql_btb_res[0]['LC_TYPE_ID']];
	$currency_name      = $currency[$sql_btb_res[0]["CURRENCY_ID"]];
	$currency_name_sign = $currency_sign_arr[$sql_btb_res[0]["CURRENCY_ID"]];
	$ref				= $company_arr_short[$sql_btb_res[0]["IMPORTER_ID"]];
	$advising_bank		= $sql_btb_res[0]['ADVISING_BANK'];
	$advising_bank_address = $sql_btb_res[0]['ADVISING_BANK_ADDRESS'];
	$lcaf_no = $sql_btb_res[0]['LCAF_NO'];
	$cover_note_no = $sql_btb_res[0]['COVER_NOTE_NO'];

	$pi_ids=$sql_btb_res[0]['PI_ID'];

	$sql_pi_res=sql_select("SELECT a.PI_NUMBER, a.PI_DATE, a.HS_CODE
	from com_pi_master_details a, com_pi_item_details b 
	where a.id=b.pi_id and a.status_active=1 and b.status_active=1 and a.id in($pi_ids) ");

	$category_name='';
	$pi_data_arr=array();
	foreach($sql_pi_res as $row)
	{
		if($pi_number!=''){$pi_number.=','.$row['PI_NUMBER'];}else{$pi_number.=$row['PI_NUMBER'];};
		if($pi_date!=''){$pi_date.=','.change_date_format($row['PI_DATE']);}else{$pi_date.=change_date_format($row['PI_DATE']);}
		if($hs_code!=''){$hs_code.=','.$row['HS_CODE'];}else{$hs_code.=$row['HS_CODE'];}	
	}
	$pi_number=implode(", ",array_unique(explode(",",$pi_number)));
	$pi_date=implode(", ",array_unique(explode(",",$pi_date)));
	$hs_code=implode(", ",array_unique(explode(",",$hs_code)));

	$sql_lcSc=sql_select("SELECT a.IMPORT_MST_ID, b.contract_no as LC_SC_NO,b.contract_value as lC_VALUE, b.contract_date as LC_SC_DATE, a.IS_LC_SC
	from  com_btb_export_lc_attachment a, com_sales_contract b
	where a.lc_sc_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.is_lc_sc=1 and a.import_mst_id=$data[0]
	union all 
	select a.IMPORT_MST_ID, b.export_lc_no as LC_SC_NO,b.lc_value as lC_VALUE, b.lc_date as LC_SC_DATE, a.IS_LC_SC 
	from com_btb_export_lc_attachment a, com_export_lc b
	where a.lc_sc_id=b.id and a.status_active=1 and a.is_deleted=0 and a.is_lc_sc=0 and b.status_active=1 and b.is_deleted=0 and a.import_mst_id=$data[0]");	
	$lc_sc_arr=array();
	foreach($sql_lcSc as $row)
	{
		$lc_sc_arr[$row['IMPORT_MST_ID']]['LC_SC_NO'].=$row['LC_SC_NO'].', ';
		$lc_sc_arr[$row['IMPORT_MST_ID']]['lC_VALUE'].=$row[csf('lC_VALUE')].', ';
		$lc_sc_arr[$row['IMPORT_MST_ID']]['LC_SC_DATE'].=change_date_format($row['LC_SC_DATE']).', ';
	}
	$lc_sc_no    = rtrim($lc_sc_arr[$btb_id]['LC_SC_NO'],', ');
	$lc_sc_value = rtrim($lc_sc_arr[$btb_id]['lC_VALUE'],', ');
	$lc_sc_date  = rtrim($lc_sc_arr[$btb_id]['LC_SC_DATE'],', ');

	$address = sql_select("SELECT ID,PLOT_NO,LEVEL_NO,ROAD_NO,BLOCK_NO,COUNTRY_ID,CITY,ZIP_CODE from lib_company where id = ".$sql_btb_res[0]['IMPORTER_ID']." and is_deleted = 0 and status_active = 1 ");
	$company_address='';
	foreach($address as $row){
		if($row['PLOT_NO']!=''){$company_address.=$row['PLOT_NO'];}
		if($row['LEVEL_NO']!=''){$company_address.=" ".$row['LEVEL_NO'];}
		if($row['ROAD_NO']!=''){$company_address.=", ".$row['ROAD_NO'];}
		if($row['BLOCK_NO']!=''){$company_address.=", ".$row['BLOCK_NO'];}
		if($row['CITY']!=''){$company_address.=", ".$row['CITY'];}
		if($row['ZIP_CODE']!=''){$company_address.=", ".$row['ZIP_CODE'];}
		if($row['COUNTRY_ID']!=''){$company_address.=", ".$country_array[$row['COUNTRY_ID']];}
	}
	$group_add = sql_select("SELECT ID,ADDRESS,CONTACT_NO,EMAIL from lib_group where is_deleted = 0 and status_active = 1 order by id desc");
	?>
	<style>
		body{width:800px;}
	</style>
	<div style="width:750;">
		<h1 align="center" style="font-size:300%; margin-bottom:0;padding-bottom:0;"><?=$importer_name;?></h1>
		<div align="center" style="border:1px solid; padding: 0px 30px">
			<? echo '<b>Factory: </b>'.$company_address.'.<b> Corporate Office: </b>'.$group_add[0]['ADDRESS'].', Phone: '.$group_add[0]['CONTACT_NO'].', E-mail: '.$group_add[0]['EMAIL']; ?> 
		</div>
	</div>
    <table width="750" cellpadding="0" align="left" cellspacing="0" border="0">
		<tr><td colspan="4" height="50"></td></tr>
		<tr>
			<td width="25"></td>
			<td  colspan="3">
				<div style="clear: both; width:100%;">
					<div style="float: left; width:600px;" >Ref: <? echo $ref."/".$bank_short_name."/".$lc_type_value."/".$supplier_short_name."/".date('Y') ;?></div>
					<div style="float: right; width:75px;">Date: <? echo change_date_format($application_date);?></div>
				</div>
			</td>
		</tr>
		<tr><td colspan="4" height="50"></td></tr>
		<tr>
			<td width="25" valign="top"></td>
			<td width="675" colspan="3">
				To</br>
				The Manager </br>
				<? echo $bank_name;?></br>
				<? echo $bank_branch;?></br>
				<? echo $bank_address;?></br>
			</td>
		</tr>
        <tr><td colspan="4" height="50">&nbsp;</td></tr>
		<tr>
			<td width="25"></td>
			<td width="675" colspan="3"><b>Subject: Issuance <?=$lc_type_value;?> for <? echo $currency_name.''.$currency_name_sign.' '.number_format($lc_value,2); ?> In favor of <? echo $supplier_name; ?>.</b>
			</td>
		</tr>
        <tr><td colspan="4">&nbsp;</td></tr>
		<tr>
			<td width="25"></td>
			<td width="675" colspan="3">Dear Sir,</td>
		</tr>
		<tr>
			<td width="25"></td>
			<td width="675" colspan="3">
				You are requested to issue <?=$lc_type_value;?> for <? echo $currency_name.''.$currency_name_sign.' '.number_format($lc_value,2); ?> (US Dollar <? echo number_to_words(number_format($lc_value,2, '.', ''), '', 'Cent'); ?> ) only in favor of <? echo $supplier_name.' '.$supplier_address; ?> Sales Contract No.: <b><? echo $lc_sc_no;?></b> Dated <? echo $lc_sc_date.' '.$currency_name.''.$currency_name_sign.' '.number_format($lc_sc_value,2); ?>.
			</td>
		</tr>
        <tr><td colspan="4"  height="30">&nbsp;</td></tr>
        <tr>
			<td width="25"></td>
			<td width="675" colspan="3">The Above Particulars of which are given below: </td>
		</tr>
        <tr><td colspan="4"  height="10">&nbsp;</td></tr>
        <tr>
			<td width="25"></td>
			<td width="10" valign="top">1) </td>
			<td width="665" colspan="2">Bank Name: <b><?echo $advising_bank;?></b>, Address: <?echo $advising_bank_address;?></td>
		</tr>
        <tr>
			<td width="25"></td>
			<td width="10" valign="top">2) </td>
			<td width="665" colspan="2">Invoice No: <b><?echo $pi_number;?></b> Date: <?echo $pi_date;?></td>
		</tr>
        <tr>
			<td width="25"></td>
			<td width="10" valign="top">3) </td>
			<td width="675" colspan="2">by debiting our ERQ A/C# <b><? echo $erq_no; ?></b> at your Branch. Charges if any by debiting our CD A/C# <b><? echo $cd_no; ?></b>.</td>
		</tr>
        <tr>
			<td width="25"></td>
			<td width="10" valign="top">4) </td>
			<td width="665" colspan="3">H.S. CODE# <b><? echo $hs_code; ?></b></td>
		</tr>
		<tr>
			<td width="25"></td>
			<td width="10" valign="top">5) </td>
			<td width="665" colspan="3">LCAF No.: <b><? echo $lcaf_no;?></b></td>
		</tr>
		<tr>
			<td width="25"></td>
			<td width="10" valign="top">6) </td>
			<td width="665" colspan="3">Cover Note No: <b><? echo $cover_note_no;?></b></td>
		</tr>

        <tr><td colspan="4"  height="20">&nbsp;</td></tr>
		</tr>
			<td width="25"></td>
			<td width="675" colspan="3"><b>All Bank charges on our/ Beneficiary account.</b></td>
		</tr>
        <tr><td colspan="4"  height="20">&nbsp;</td></tr>
		</tr>
			<td width="25"></td>
			<td width="675" colspan="3">Your co-operation in this matter will be highly appreciated.</td>
		</tr>
		<tr><td colspan="4"  height="50">&nbsp;</td></tr>
        <tr>
			<td width="25"></td>
			<td width="675" colspan="3">Thanking You.</br>
			<strong>For <?=$importer_name;?></strong>
			</td>
		</tr>
        <tr><td colspan="4"  height="100">&nbsp;</td></tr>
        <tr>
			<td width="25"></td>
			<td width="675" colspan="3"><div style="border-top: 1px solid black;width:150px;text-align:center;">Authorized Signature</div></br>
			</td>
		</tr>
	</table>
	<?
	exit();
}

if ($action==='btb_import_lc_letter11')
{
	$buyer_arr =return_library_array('SELECT id, buyer_name FROM lib_buyer','id','buyer_name');
	$supplier_arr = return_library_array('SELECT id, supplier_name FROM lib_supplier','id','supplier_name');
	$company_arr = return_library_array('SELECT id, company_name FROM lib_company','id','company_name');

	$sql_bank_info = sql_select("SELECT ID, CONTACT_PERSON, BANK_NAME, BRANCH_NAME, ADDRESS from lib_bank ");
	$bank_dtls_arr=array();
	foreach($sql_bank_info as $row)
	{
		$bank_dtls_arr[$row['ID']]['BANK_NAME']=$row['BANK_NAME'];
		$bank_dtls_arr[$row['ID']]['BRANCH_NAME']=$row['BRANCH_NAME'];
		$bank_dtls_arr[$row['ID']]['ADDRESS']=$row['ADDRESS'];
	}
	$cd_no=return_library_array("SELECT a.id, b.account_no from LIB_BANK a, LIB_BANK_ACCOUNT b where a.id=b.account_id and b.account_type=10",'id','account_no');

	$exportPiSupp = sql_select("select c.import_pi, a.id from com_btb_lc_master_details a , com_btb_lc_pi b , com_pi_master_details c where a.id = b.com_btb_lc_master_details_id and b.pi_id = c.id");
	foreach ($exportPiSupp as $value)
	{
		$exportPiSuppArr[$value[csf("id")]] = $value[csf("import_pi")];
	}
	
	// BTB Part
	$sql_btb="SELECT ID, BTB_SYSTEM_ID, LC_DATE, APPLICATION_DATE, IMPORTER_ID, ITEM_CATEGORY_ID, ISSUING_BANK_ID, CURRENCY_ID, SUPPLIER_ID, LC_VALUE, MATURITY_FROM_ID, LAST_SHIPMENT_DATE, LC_EXPIRY_DATE, TENOR, PI_ID,LC_CATEGORY
	from com_btb_lc_master_details
	where id=$data and is_deleted=0 and status_active=1";
	$sql_btb_res=sql_select($sql_btb);
	$btb_id = $sql_btb_res[0]['ID'];
	$importer_name = $company_arr[$sql_btb_res[0]['IMPORTER_ID']];

	if ($sql_btb_res[0]['MATURITY_FROM_ID']==1)
		$maturityFrom =  $sql_btb_res[0]['TENOR']." day’s from the date of Acceptance";
	else if ($sql_btb_res[0]['MATURITY_FROM_ID']==3)
		$maturityFrom = "(UNDER EDF)";
	else if ($sql_btb_res[0]['MATURITY_FROM_ID']==4)
		$maturityFrom = $sql_btb_res[0]['TENOR']." day’s from the date of BL";
	else if ($sql_btb_res[0]['MATURITY_FROM_ID']==5)
		$maturityFrom = $sql_btb_res[0]['TENOR']." day’s from the date of Delivery";
	else $maturityFrom='';

	// PI Part
	$pi_ids=$sql_btb_res[0]['PI_ID'];
	$sql_pi_res=sql_select("SELECT PI_NUMBER, PI_DATE, ITEM_CATEGORY_ID,HS_CODE from com_pi_master_details where id in($pi_ids) and status_active=1 and is_deleted=0");

	$category_name='';$hs_code='';
	$pi_data_arr=array();
	foreach($sql_pi_res as $row)
	{
		$pi_number.=$row['PI_NUMBER'].', ';
		$pi_date.=change_date_format($row['PI_DATE']).', ';	
		if($row['HS_CODE']){$hs_code.=$row['HS_CODE'].', ';}
		if ($row['ITEM_CATEGORY_ID']==2 || $row['ITEM_CATEGORY_ID']==3) $category_name.='Fabric, ';
		else if ($row['ITEM_CATEGORY_ID']==12 || $row['ITEM_CATEGORY_ID']==24) $category_name.='Dyeing, ';
		else if ($row['ITEM_CATEGORY_ID']==25) $category_name.='Accessories, ';
		else $category_name.=$item_category[$row['ITEM_CATEGORY_ID']].', ';
	}

	// LC SC Part
	$sql_lcSc=sql_select("SELECT a.LC_SC_ID,a.IMPORT_MST_ID, b.contract_no as LC_SC_NO, b.contract_date as LC_SC_DATE, b.BUYER_NAME,b.contract_value as LC_SC_VALUE, a.IS_LC_SC, sum(c.attached_qnty) as ATTACHED_QNTY, e.order_uom as ORDER_UOM
	from  com_btb_export_lc_attachment a, com_sales_contract b, com_sales_contract_order_info c,wo_po_break_down d, wo_po_details_master e
	where a.lc_sc_id=b.id and b.id=c.com_sales_contract_id and c.wo_po_break_down_id=d.id and d.job_id=e.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and a.is_lc_sc=1 and a.import_mst_id=$data
	group by a.lc_sc_id,a.import_mst_id, b.contract_no, b.contract_date, b.buyer_name,b.contract_value, a.is_lc_sc, e.order_uom 
	union all 
	select a.LC_SC_ID,a.IMPORT_MST_ID, b.export_lc_no as LC_SC_NO, b.lc_date as LC_SC_DATE, b.BUYER_NAME,b.lc_value as LC_SC_VALUE, a.IS_LC_SC, sum(c.attached_qnty) as ATTACHED_QNTY, e.order_uom as ORDER_UOM
	from com_btb_export_lc_attachment a, com_export_lc b, com_export_lc_order_info c,wo_po_break_down d, wo_po_details_master e
	where a.lc_sc_id=b.id and b.id=c.com_export_lc_id and c.wo_po_break_down_id=d.id and d.job_id=e.id  and a.status_active=1 and a.is_deleted=0 and a.is_lc_sc=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.import_mst_id=$data
	group by a.lc_sc_id,a.import_mst_id, b.export_lc_no, b.lc_date, b.buyer_name,b.lc_value, a.is_lc_sc, e.order_uom  ");	
	$lc_sc_arr=array();
	foreach($sql_lcSc as $row)
	{
		if($lc_sc_id_check[$row['LC_SC_ID']][$row['IS_LC_SC']]=="")
		{
			$lc_sc_id_check[$row['LC_SC_ID']][$row['IS_LC_SC']]=$row['LC_SC_ID'];
			$lc_sc_arr[$row['IMPORT_MST_ID']]['LC_SC_NO'].=$row['LC_SC_NO'].', ';
			$lc_sc_arr[$row['IMPORT_MST_ID']]['LC_SC_DATE'].=change_date_format($row['LC_SC_DATE']).', ';
			$lc_sc_arr[$row['IMPORT_MST_ID']]['BUYER_NAME'].= $buyer_arr[$row['BUYER_NAME']].', ';
			$lc_sc_value+= $row['LC_SC_VALUE'];
			if($row['IS_LC_SC']==0)
			{
				$lc_id.=$row['LC_SC_ID'].', ';
			}
			elseif($row['IS_LC_SC']==1)
			{
				$sc_id.=$row['LC_SC_ID'].', ';
			}
		}
		$lc_sc_arr[$row['IMPORT_MST_ID']]['ORDER_UOM'].= $row['ORDER_UOM'].',';
		$lc_sc_attached_qnty+= $row['ATTACHED_QNTY'];
		
	}
	// echo '<pre>';print_r($lc_sc_arr);

	$lc_sc_no   = rtrim($lc_sc_arr[$btb_id]['LC_SC_NO'],', ');
	$lc_sc_date = rtrim($lc_sc_arr[$btb_id]['LC_SC_DATE'],', ');
	$lc_sc_buyer = rtrim($lc_sc_arr[$btb_id]['BUYER_NAME'],', ');
	$order_uom_arr = array_unique(explode(",",chop($lc_sc_arr[$btb_id]['ORDER_UOM'],',')));
	if(count($order_uom_arr)<2)
	{
		$order_uom=$unit_of_measurement[$order_uom_arr[0]];
	}
	$lc_id = rtrim($lc_id,', ');
	$sc_id = rtrim($sc_id,', ');

	$previous_btb_value=0;
	if($lc_id!='')
	{
		$sql_lc=sql_select("SELECT b.id,b.LC_VALUE
		from com_btb_export_lc_attachment a, com_btb_lc_master_details b
		where  a.lc_sc_id in ($lc_id) and a.import_mst_id=b.id and a.status_active=1 and a.is_deleted=0 and a.is_lc_sc=0 and b.status_active=1 and b.is_deleted=0 and a.import_mst_id<$data group by b.id,b.lc_value");
		foreach($sql_lc as $row)
		{
			$previous_btb_value+= $row['LC_VALUE'];
		}
	}

	if($sc_id!='')
	{
		$sql_sc=sql_select("SELECT b.id,b.LC_VALUE
		from  com_btb_export_lc_attachment a, com_btb_lc_master_details b
		where a.lc_sc_id in ($sc_id) and a.import_mst_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.is_lc_sc=1 and a.import_mst_id<$data group by b.id,b.lc_value");
		foreach($sql_sc as $row)
		{
			$previous_btb_value+= $row['LC_VALUE'];
		}
	}

	?>
	<table width="700" cellpadding="0" align="left" cellspacing="0" border="0">
		<tr><td colspan="5" height="100"></td></tr>
		<tr>
			<td ></td>
			<td colspan="3">Ref No : <?= $sql_btb_res[0]['BTB_SYSTEM_ID']; ?></td>
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td colspan="3">Dated&nbsp;&nbsp;&nbsp;: <?= date("F d, Y", strtotime($sql_btb_res[0]['LC_DATE'])); ?></td>
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td colspan="3">The Manager & Head of the Branch</td>
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td colspan="3"><?= $bank_dtls_arr[$sql_btb_res[0]['ISSUING_BANK_ID']]['BANK_NAME']; ?></td>
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td colspan="3"><?= $bank_dtls_arr[$sql_btb_res[0]['ISSUING_BANK_ID']]['BRANCH_NAME']; ?></td>
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td colspan="3"><?= $bank_dtls_arr[$sql_btb_res[0]['ISSUING_BANK_ID']]['ADDRESS']; ?></td>
			<td ></td>
		</tr>
		<tr><td colspan="5" height="15"></td></tr>
		<tr>
			<td ></td>
			<td colspan="3"><strong>Sub:&nbsp;Request for opening Back-to-Back (BTB) L/C for <?= $currency[$sql_btb_res[0]['CURRENCY_ID']]; ?>&nbsp;<?=number_format($sql_btb_res[0]['LC_VALUE'],2); ?> favoring/Supplier Name: <?=$supplier_arr[$sql_btb_res[0]['SUPPLIER_ID']];?> against Export S/C No. <?= $lc_sc_no; ?> Dated: <?= $lc_sc_date; ?></td>
			<td width="25"></td>
		</tr>
		<tr><td colspan="5" height="15"></td></tr>
		<tr>
			<td ></td>
			<td colspan="3">Muhtaram,</td>
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td colspan="3">Assalamu Alaikum,</td>
			<td ></td>
		</tr>
		<tr><td colspan="5" height="10"></td></tr>
		<tr>
			<td ></td>
			<td colspan="3">We would request you to please issue Back-to-Back (BTB) L/C as detailed below:-</td>
			<td ></td>
		</tr>
		<tr><td colspan="5" height="15"></td></tr>
		<tr>
			<td width="25"></td>
			<td width="280">Shipment date</td>
			<td width="20"><strong>:</strong></td>
			<td width="350"><?= change_date_format($sql_btb_res[0]['LAST_SHIPMENT_DATE']); ?></td>
			<td width="25"></td>
		</tr>
		<tr>
			<td ></td>
			<td >Expiry date</td>
			<td ><strong>:</strong></td>
			<td ><?= change_date_format($sql_btb_res[0]['LC_EXPIRY_DATE']); ?></td>
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td >Buyer</td>
			<td ><strong>:</strong></td>
			<td ><?= $lc_sc_buyer; ?></td>
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td >Beneficiary</td>
			<td ><strong>:</strong></td>
			<td >Ourselves</td>
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td >Type of the L/C</td>
			<td ><strong>:</strong></td>
			<td ><?=$maturityFrom;?></td>			
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td colspan="4"><hr></td>
		</tr>
		<tr>
			<td ></td>
			<td >BTB L/C will be  enjoyed</td>
			<td ><strong>:</strong></td>
			<td >75%</td>
			<td ></td>
		</tr>				
		<tr>
			<td ></td>
			<td >Present value of BTB L/C</td>
			<td ><strong>:</strong></td>
			<td ><?= $currency[$sql_btb_res[0]['CURRENCY_ID']].' '.number_format($sql_btb_res[0]['LC_VALUE'],2); ?></td>			
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td >The applicant of BTB L/C</td>
			<td ><strong>:</strong></td>
			<td >Ourselves</td>			
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td >EXPORT LC/BTMEA QTY</td>
			<td ><strong>:</strong></td>
			<td ><?=number_format($lc_sc_attached_qnty,2)." ".$order_uom;?></td>			
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td >Item</td>
			<td ><strong>:</strong></td>
			<td ><?= implode(', ',array_unique(explode(', ',rtrim($category_name,', ')))); ?></td>
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td >H.S CODE</td>
			<td ><strong>:</strong></td>
			<td ><?= implode(', ',array_unique(explode(', ',rtrim($hs_code,', ')))); ?></td>
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td >Export LC/SC Value</td>
			<td ><strong>:</strong></td>
			<td ><?= number_format($lc_sc_value,2);?></td>
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td >Previous BTB LC open against s/c or L/C</td>
			<td ><strong>:</strong></td>
			<td ><?= number_format($previous_btb_value,2);?></td>
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td >Present &  Previous BTB LC value open</td>
			<td ><strong>:</strong></td>
			<td ><?= number_format($sql_btb_res[0]['LC_VALUE']+$previous_btb_value,2);  ?></td>
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td >BTB LC Balance Value</td>
			<td ><strong>:</strong></td>
			<td ><?=number_format($lc_sc_value-$sql_btb_res[0]['LC_VALUE']-$previous_btb_value,2); ?></td>
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td >BTB Percentage</td>
			<td ><strong>:</strong></td>
			<td ><?= number_format(((($lc_sc_value-$sql_btb_res[0]['LC_VALUE']-$previous_btb_value)/$lc_sc_value)*100),2); ?>%</td>
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td >LC Code</td>
			<td ><strong>:</strong></td>
			<td ><?= $sql_btb_res[0]['LC_CATEGORY']; ?></td>
			<td ></td>
		</tr>
		<tr><td colspan="5" height="15"></td></tr>
		<tr>
			<td ></td>
			<td colspan="3">The following papers are enclosed for the opening of BTB L/C by request of ourselves:-</td>
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td colspan="3">L/C Application</td>
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td colspan="3">Original L/C as mentioned above.</td>
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td colspan="3">Pro-forma Invoice No. <?= rtrim($pi_number,', '); ?>&nbsp;dated:<?= implode(', ',array_unique(explode(', ',rtrim($pi_date,', ')))); ?>&nbsp;issued by beneficiary.</td>
			<td ></td>
		</tr>
		<tr><td colspan="5" height="15"></td></tr>
		<tr>
			<td ></td>
			<td colspan="3">Please debit our CD Account- <?=$cd_no[$sql_btb_res[0]['ISSUING_BANK_ID']];?> for the realization of your charges under intimation to us.</td>
			<td ></td>
		</tr>
		<tr><td colspan="5" height="15"></td></tr>

		<tr>
			<td ></td>
			<td colspan="3">Please also provide us with a copy of L/C immediately after issuance of the same.</td>
			<td ></td>
		</tr>
		<tr><td colspan="5" height="15"></td></tr>
		<tr>
			<td ></td>
			<td colspan="3">Ma-Assalam </td>
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td colspan="3">Yours faithfully</td>
			<td ></td>
		</tr>
		<tr><td colspan="5" height="100"></td></tr>
		<tr>
			<td ></td>
			<td colspan="3">AUTHORIZED SIGNATURE</td>
			<td ></td>
		</tr>
	</table>
	<?
	exit();
}

if ($action==='btb_import_lc_letter31')
{
	$buyer_arr =return_library_array('SELECT id, buyer_name FROM lib_buyer','id','buyer_name');
	$supplier_arr = return_library_array('SELECT id, supplier_name FROM lib_supplier','id','supplier_name');
	$company_arr = return_library_array('SELECT id, company_name FROM lib_company','id','company_name');
	$user_library=return_library_array( "select id, user_full_name from user_passwd", "id", "user_full_name"  );
	$pi_source_array=array(3=>"Local");

	$user_ip = $_SERVER['REMOTE_ADDR'];

	$sql_bank_info = sql_select("SELECT ID, CONTACT_PERSON, BANK_NAME, BRANCH_NAME, ADDRESS from lib_bank ");
	$bank_dtls_arr=array();
	foreach($sql_bank_info as $row)
	{
		$bank_dtls_arr[$row['ID']]['BANK_NAME']=$row['BANK_NAME'];
		$bank_dtls_arr[$row['ID']]['BRANCH_NAME']=$row['BRANCH_NAME'];
		$bank_dtls_arr[$row['ID']]['ADDRESS']=$row['ADDRESS'];
	}
	$cd_no=return_library_array("SELECT a.id, b.account_no from LIB_BANK a, LIB_BANK_ACCOUNT b where a.id=b.account_id and b.account_type=10",'id','account_no');

	$exportPiSupp = sql_select("select c.import_pi, a.id from com_btb_lc_master_details a , com_btb_lc_pi b , com_pi_master_details c where a.id = b.com_btb_lc_master_details_id and b.pi_id = c.id");
	foreach ($exportPiSupp as $value)
	{
		$exportPiSuppArr[$value[csf("id")]] = $value[csf("import_pi")];
	}
	
	// BTB Part
	 $sql_btb="SELECT ID, BTB_SYSTEM_ID, LC_DATE, APPLICATION_DATE, IMPORTER_ID, ITEM_CATEGORY_ID, ISSUING_BANK_ID, CURRENCY_ID, SUPPLIER_ID, LC_VALUE, MATURITY_FROM_ID, LAST_SHIPMENT_DATE, LC_EXPIRY_DATE, TENOR, PI_ID, LC_CATEGORY, INSERTED_BY,PAYTERM_ID,UOM_ID,LC_TYPE_ID,INCO_TERM_PLACE,PORT_OF_LOADING,APPLICATION_DATE,PORT_OF_DISCHARGE
	from com_btb_lc_master_details 
	where id=$data and is_deleted=0 and status_active=1";
	$sql_btb_res=sql_select($sql_btb);
	$btb_id = $sql_btb_res[0]['ID'];
	$importer_name = $company_arr[$sql_btb_res[0]['IMPORTER_ID']];

	if ($sql_btb_res[0]['MATURITY_FROM_ID']==1)
		$maturityFrom =  $pay_term[$sql_btb_res[0]['PAYTERM_ID']]."/".$sql_btb_res[0]['TENOR']." day’s from the date of Acceptance";
	else if ($sql_btb_res[0]['MATURITY_FROM_ID']==3)
		$maturityFrom = "(UNDER EDF)";
	else if ($sql_btb_res[0]['MATURITY_FROM_ID']==4)
		$maturityFrom = $pay_term[$sql_btb_res[0]['PAYTERM_ID']]."/".$sql_btb_res[0]['TENOR']." day’s from the date of BL";
	else if ($sql_btb_res[0]['MATURITY_FROM_ID']==5)
		$maturityFrom = $pay_term[$sql_btb_res[0]['PAYTERM_ID']]."/".$sql_btb_res[0]['TENOR']." day’s from the date of Delivery";
	else $maturityFrom='';
	$pi_ids=$sql_btb_res[0]['PI_ID'];
	// PI Part
	$sql_pi_res=sql_select("SELECT A.PI_NUMBER,a.PI_DATE,a.ITEM_CATEGORY_ID,b.UOM,a.HS_CODE,a.SOURCE,sum(b.quantity) as QTY FROM com_pi_master_details a, com_pi_item_details b WHERE a.id = b.pi_id  and a.id in($pi_ids) and a.status_active=1 and a.is_deleted=0  Group by a.PI_NUMBER, a.PI_DATE, a.ITEM_CATEGORY_ID,b.UOM,a.HS_CODE,a.SOURCE");

	$category_name='';$hs_code='';
	$pi_data_arr=array();
	$umo_array=array();
	foreach($sql_pi_res as $row)
	{
		$pi_number.=$row['PI_NUMBER'].', ';
		$pi_source=$row['SOURCE'];
		$pi_qty+=$row['QTY'];

		$umo_array[$row['UOM']]=$row['UOM'];
		$umo_val=$umo_array[$row['UOM']];

		$pi_date.=change_date_format($row['PI_DATE']).', ';	
		if($row['HS_CODE']){$hs_code.=$row['HS_CODE'].', ';}
		if ($row['ITEM_CATEGORY_ID']==2 || $row['ITEM_CATEGORY_ID']==3) $category_name.='Fabric, ';
		else if ($row['ITEM_CATEGORY_ID']==12 || $row['ITEM_CATEGORY_ID']==24) $category_name.='Dyeing, ';
		else if ($row['ITEM_CATEGORY_ID']==25) $category_name.='Accessories, ';
		else $category_name.=$item_category[$row['ITEM_CATEGORY_ID']].', ';
	}

	 $sql="select a.currency_id, a.lc_date, a.importer_id, b.lc_sc_id, b.is_lc_sc, b.current_distribution, b.status_active from com_btb_lc_master_details a, com_btb_export_lc_attachment b where a.id=b.import_mst_id and b.import_mst_id=$data and b.is_deleted=0 and b.status_active=1";
	$lc_sc_sql=sql_select($sql);
	
	foreach($lc_sc_sql as $row){
		if($row[csf('is_lc_sc')]==0){
			//  $lc_sc="SELECT a.export_lc_no as lc_sc_no, a.buyer_name, a.lc_value as lc_sc_val,a.import_btb, 1 as ratio, a.lc_date as sc_lc_date, b.ATTACHED_QNTY as lc_qty from com_export_lc a left join com_export_lc_order_info b on a.id=b.com_export_lc_id and b.status_active=1 where a.id='".$row[csf("lc_sc_id")]."' and a.status_active=1";

			$lc_sc = "select  wm.total_set_qnty as ratio, ci.attached_qnty as lc_qty,a.export_lc_no as lc_sc_no, a.buyer_name, a.lc_value as lc_sc_val, a.import_btb, a.lc_date as sc_lc_date
			from  com_export_lc a, wo_po_break_down wb, wo_po_details_master wm, com_export_lc_order_info ci where wb.job_no_mst = wm.job_no and wb.id=ci.wo_po_break_down_id and a.id=ci.com_export_lc_id and ci.com_export_lc_id='".$row[csf("lc_sc_id")]."' and ci.status_active = '1' and ci.is_deleted = '0' order by ci.id";
			$sc_lc = 'L/C';
		}else{
			//  $lc_sc="SELECT a.contract_no as lc_sc_no, a.buyer_name, a.contract_value as lc_sc_val, 0 as import_btb, a.contract_date as sc_lc_date, b.ATTACHED_QNTY as lc_qty from com_sales_contract a left join com_sales_contract_order_info b on a.id=b.com_sales_contract_id and b.status_active=1 where a.id='".$row[csf("lc_sc_id")]."' and a.status_active=1";

			$lc_sc = "SELECT a.contract_no as lc_sc_no, a.buyer_name,a.contract_date as sc_lc_date, a.contract_value as lc_sc_val, wm.total_set_qnty as ratio, ci.attached_qnty as lc_qty 
			from com_sales_contract a, wo_po_break_down wb, wo_po_details_master wm, com_sales_contract_order_info ci 
			where wb.job_id = wm.id and wb.id=ci.wo_po_break_down_id and a.id=ci.com_sales_contract_id and ci.com_sales_contract_id='".$row[csf("lc_sc_id")]."' and ci.status_active = '1' and ci.is_deleted = '0' and ci.is_sales=0
			order by ci.id";
			$sc_lc = 'S/C';
		}
		
	}

	$sql_lc__arr=array();
	$lc_sc_val_data=sql_select($lc_sc);

	foreach($lc_sc_val_data as $row)
	{
		$lc_sc_no=$row[csf('lc_sc_no')];
		$lc_date=$row[csf('sc_lc_date')];
		$lc_qty+=$row[csf('lc_qty')]*$row[csf('ratio')];
	}
				

	if(count($order_uom_arr)<2)
	{
		$order_uom=$unit_of_measurement[$order_uom_arr[0]];
	}
	$lc_id = rtrim($lc_id,', ');
	$sc_id = rtrim($sc_id,', ');

	$previous_btb_value=0;
	if($lc_id!='')
	{
		$sql_lc=sql_select("SELECT b.id,b.LC_VALUE
		from com_btb_export_lc_attachment a, com_btb_lc_master_details b
		where  a.lc_sc_id in ($lc_id) and a.import_mst_id=b.id and a.status_active=1 and a.is_deleted=0 and a.is_lc_sc=0 and b.status_active=1 and b.is_deleted=0 and a.import_mst_id<$data group by b.id,b.lc_value");
		foreach($sql_lc as $row)
		{
			$previous_btb_value+= $row['LC_VALUE'];
		}
	}

	if($sc_id!='')
	{
		$sql_sc=sql_select("SELECT b.id,b.LC_VALUE
		from  com_btb_export_lc_attachment a, com_btb_lc_master_details b
		where a.lc_sc_id in ($sc_id) and a.import_mst_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.is_lc_sc=1 and a.import_mst_id<$data group by b.id,b.lc_value");
		foreach($sql_sc as $row)
		{
			$previous_btb_value+= $row['LC_VALUE'];
		}
	}

	// echo $lc_sc_no."-----------";

	?>
	<table width="700" cellpadding="0" align="left" cellspacing="0" border="0">
		<tr>
			<td></td>
			<td colspan="2" style="padding-top: 0;">User ID:<?= $user_library[$sql_btb_res[0]['INSERTED_BY']]; ?> </td>
			<td align="right">USER IP:<?=$user_ip?></td>			
	    </tr>
		<tr><td colspan="5" height="100"></td></tr>
		<tr>
			<td ></td>
			<td colspan="3">Ref No : <?= $sql_btb_res[0]['BTB_SYSTEM_ID']; ?></td>
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td colspan="3">Dated&nbsp;&nbsp;&nbsp;: <?= date("F d, Y", strtotime($sql_btb_res[0]['APPLICATION_DATE'])); ?></td>
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td colspan="3">The Manager / Head of the Branch</td>
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td colspan="3"><?= $bank_dtls_arr[$sql_btb_res[0]['ISSUING_BANK_ID']]['BANK_NAME']; ?></td>
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td colspan="3"><?= $bank_dtls_arr[$sql_btb_res[0]['ISSUING_BANK_ID']]['BRANCH_NAME']; ?></td>
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td colspan="3"><?= $bank_dtls_arr[$sql_btb_res[0]['ISSUING_BANK_ID']]['ADDRESS']; ?></td>
			<td ></td>
		</tr>
		<tr><td colspan="5" height="15"></td></tr>
		<tr>
			<td ></td>
			<td colspan="3"><strong>Sub:&nbsp;Request for opening of <?=$pi_source_array[$pi_source]?> Back-to-Back (BTB) L/C for <?= $currency[$sql_btb_res[0]['CURRENCY_ID']]; ?>&nbsp;<?=number_format($sql_btb_res[0]['LC_VALUE'],2); ?> against Export <?=$sc_lc?> No. <?=$lc_sc_no ?> Dated: <?= rtrim(change_date_format($lc_date),","); ?>  Export Qty:<?=$lc_qty?> <?= $unit_of_measurement[$sql_btb_res[0]['UOM_ID']]; ?></td>
			<td width="25"></td>
		</tr>
		<tr><td colspan="5" height="15"></td></tr>
		<tr>
			<td ></td>
			<td colspan="3">Muhtaram,</td>
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td colspan="3">Assalamu Alaikum,</td>
			<td ></td>
		</tr>
		<tr><td colspan="5" height="10"></td></tr>
		<tr>
			<td ></td>
			<td colspan="3">We would request you to please issue Back-to-Back (BTB) L/C as detailed below:-</td>
			<td ></td>
		</tr>
		<tr><td colspan="5" height="15"></td></tr>
		<tr>
			<td width="25"></td>
			<td width="280">Shipment date</td>
			<td width="20"><strong>:</strong></td>
			<td width="350"><?= change_date_format($sql_btb_res[0]['LAST_SHIPMENT_DATE']); ?></td>
			<td width="25"></td>
		</tr>
		<tr>
			<td ></td>
			<td >Expiry date</td>
			<td ><strong>:</strong></td>
			<td ><?= change_date_format($sql_btb_res[0]['LC_EXPIRY_DATE']); ?></td>
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td >BTB L/C Value</td>
			<td ><strong>:</strong></td>
			<td ><?= $currency[$sql_btb_res[0]['CURRENCY_ID']].' '.number_format($sql_btb_res[0]['LC_VALUE'],2); ?></td>			
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td >BTB L/C Qty</td>	
			<td ><strong>:</strong></td>
			<td ><?=$item_category[$sql_btb_res[0]['ITEM_CATEGORY_ID']]?>/<?=$pi_qty?> 
				<?	
			    // $unique_uom = array_unique($umo_val);
			   if(count($umo_array) ==1){
				echo "/".$unit_of_measurement[$umo_val];
				}?>
			</td>
		</tr>
		<tr>
			<td ></td>
			<td >Description of Goods:</td>
			<td ><strong>:</strong></td>
			<td ><?=$item_category[$sql_btb_res[0]['ITEM_CATEGORY_ID']]?> details as per PI No <?=$sql_pi_res[0]['PI_NUMBER'];?> Date <?=$sql_pi_res[0]['PI_DATE'];?></td>
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td >Supplier Name:</td>
			<td ><strong>:</strong></td>
			<td ><?=$supplier_arr[$sql_btb_res[0]['SUPPLIER_ID']];?></td>
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td >Type of the L/C</td>
			<td ><strong>:</strong></td>
			<td ><?=$maturityFrom;?></td>			
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td >Place of Receipt</td>
			<td ><strong>:</strong></td>
			<td ><?=$sql_btb_res[0]['PORT_OF_LOADING']?></td>
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td >Place of Delivery</td>
			<td ><strong>:</strong></td>
			<td ><?=$sql_btb_res[0]['PORT_OF_DISCHARGE']?></td>
			<td ></td> 
		</tr>
		<tr><td colspan="5" height="15"></td></tr>
		<tr>
			<td ></td>
			<td colspan="3">* Documents Required: Commercial Invoice, Packing List, Delivery challan, Truck Receipt, CO, B/C(Cash Incentive avail by applicant), Mushok 6.3, BTMA(GSP and Cash Incentive facility) etc. must be mention in the documents required column.</td>
			<td ></td>
		</tr>
		<tr><td colspan="5" height="15"></td></tr>
		<tr>
			<td ></td>
			<td colspan="3">*Additional condition: Export LC/Contract no., Date, Qty., Applicant IRC No.: 260326120396020, TIN: 224840827408, BIN no. 002236693-0204, LC issuing Bank BIN, Beneficiary's BIN no. and H.S. Code no. must be quoted in all shipping documents.
			</td>
			<td ></td>
		</tr>
		<tr><td colspan="5" height="15"></td></tr>
		<tr>
			<td ></td>
			<td colspan="3">Please debit our CD Account- for the realization of your charges under intimation to us and supply us related the vouchers.</td>
			<td ></td>
		</tr>
		<tr><td colspan="5" height="15"></td></tr>
		<tr>
			<td ></td>
			<td colspan="3">So, considering our application and documents you are requested to open <b><?=$lc_type[$sql_btb_res[0]['LC_TYPE_ID']];?></b> for <b><?= $currency[$sql_btb_res[0]['CURRENCY_ID']].' '.number_format($sql_btb_res[0]['LC_VALUE'],2); ?></b> to execute the export order and provide us with a copy of L/C immediately after issuance of the same.
			</td>
			<td ></td>
		</tr>
		<tr><td colspan="5" height="15"></td></tr>

		<tr>
			<td ></td>
			<td colspan="3">We are very much grateful to your’s sincere and continuous co-operation</td>
			<td ></td>
		</tr>
		<tr><td colspan="5" height="15"></td></tr>
		<tr>
			<td ></td>
			<td colspan="3">Ma-Assalam </td>
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td colspan="3">Yours faithfully</td>
			<td ></td>
		</tr>
		<tr><td colspan="5" height="50"></td></tr>
		<tr>
			<td ></td>
			<td colspan="3">MD/Chairman/Director</td>
			<td ></td>
		</tr>
	</table>
	<?
	exit();
}

if ($action==='btb_import_lc_letter32')
{
	$buyer_arr =return_library_array('SELECT id, buyer_name FROM lib_buyer','id','buyer_name');
	$supplier_arr = return_library_array('SELECT id, supplier_name FROM lib_supplier','id','supplier_name');
	$company_arr = return_library_array('SELECT id, company_name FROM lib_company','id','company_name');
	$user_library=return_library_array( "select id, user_full_name from user_passwd", "id", "user_full_name"  );
	$pi_source_array=array(1=>"Foreign");

	$user_ip = $_SERVER['REMOTE_ADDR'];

	$sql_bank_info = sql_select("SELECT ID, CONTACT_PERSON, BANK_NAME, BRANCH_NAME, ADDRESS from lib_bank ");
	$bank_dtls_arr=array();
	foreach($sql_bank_info as $row)
	{
		$bank_dtls_arr[$row['ID']]['BANK_NAME']=$row['BANK_NAME'];
		$bank_dtls_arr[$row['ID']]['BRANCH_NAME']=$row['BRANCH_NAME'];
		$bank_dtls_arr[$row['ID']]['ADDRESS']=$row['ADDRESS'];
	}
	$cd_no=return_library_array("SELECT a.id, b.account_no from LIB_BANK a, LIB_BANK_ACCOUNT b where a.id=b.account_id and b.account_type=10",'id','account_no');

	$exportPiSupp = sql_select("select c.import_pi, a.id from com_btb_lc_master_details a , com_btb_lc_pi b , com_pi_master_details c where a.id = b.com_btb_lc_master_details_id and b.pi_id = c.id");
	foreach ($exportPiSupp as $value)
	{
		$exportPiSuppArr[$value[csf("id")]] = $value[csf("import_pi")];
	}
	
	// BTB Part
	 $sql_btb="SELECT ID, BTB_SYSTEM_ID, LC_DATE, APPLICATION_DATE, IMPORTER_ID, ITEM_CATEGORY_ID, ISSUING_BANK_ID, CURRENCY_ID, SUPPLIER_ID, LC_VALUE, MATURITY_FROM_ID, LAST_SHIPMENT_DATE, LC_EXPIRY_DATE, TENOR, PI_ID, LC_CATEGORY, INSERTED_BY,PAYTERM_ID,UOM_ID,LC_TYPE_ID,INCO_TERM_PLACE,PORT_OF_LOADING,PORT_OF_DISCHARGE
	from com_btb_lc_master_details
	where id=$data and is_deleted=0 and status_active=1";
	$sql_btb_res=sql_select($sql_btb);
	$btb_id = $sql_btb_res[0]['ID'];
	$importer_name = $company_arr[$sql_btb_res[0]['IMPORTER_ID']];

	if ($sql_btb_res[0]['MATURITY_FROM_ID']==1)
		$maturityFrom =  $pay_term[$sql_btb_res[0]['PAYTERM_ID']]."/".$sql_btb_res[0]['TENOR']." day’s from the date of Acceptance";
	else if ($sql_btb_res[0]['MATURITY_FROM_ID']==3)
		$maturityFrom = "(UNDER EDF)";
	else if ($sql_btb_res[0]['MATURITY_FROM_ID']==4)
		$maturityFrom = $pay_term[$sql_btb_res[0]['PAYTERM_ID']]."/".$sql_btb_res[0]['TENOR']." day’s from the date of BL";
	else if ($sql_btb_res[0]['MATURITY_FROM_ID']==5)
		$maturityFrom = $pay_term[$sql_btb_res[0]['PAYTERM_ID']]."/".$sql_btb_res[0]['TENOR']." day’s from the date of Delivery";
	else $maturityFrom='';

	// PI Part
	$pi_ids=$sql_btb_res[0]['PI_ID'];
	$sql_pi_res=sql_select("SELECT A.PI_NUMBER,a.PI_DATE,a.ITEM_CATEGORY_ID,a.HS_CODE,b.UOM,a.SOURCE,sum(b.quantity) as QTY FROM com_pi_master_details a, com_pi_item_details b WHERE a.id = b.pi_id  and a.id in($pi_ids) and a.status_active=1 and a.is_deleted=0  Group by a.PI_NUMBER, a.PI_DATE, a.ITEM_CATEGORY_ID,a.HS_CODE,a.SOURCE,b.UOM");

	// $sql_pi_res[0]['PI_DATE'];
	// $sql_pi_res[0]['PI_NUMBER'];

	$category_name='';$hs_code='';
	$pi_data_arr=array();
	$umo_array=array();
	foreach($sql_pi_res as $row)
	{
		// $pi_number.=$row['PI_NUMBER'].', ';

		$pi_source=$row['SOURCE'];
		$pi_total_qty+=$row['QTY'];
		$umo_array[$row['UOM']]=$row['UOM'];
		$umo_val=$umo_array[$row['UOM']];
		// $pi_date.=change_date_format($row['PI_DATE']).', ';	
		if($row['HS_CODE']){$hs_code.=$row['HS_CODE'].', ';}
		if ($row['ITEM_CATEGORY_ID']==2 || $row['ITEM_CATEGORY_ID']==3) $category_name.='Fabric, ';
		else if ($row['ITEM_CATEGORY_ID']==12 || $row['ITEM_CATEGORY_ID']==24) $category_name.='Dyeing, ';
		else if ($row['ITEM_CATEGORY_ID']==25) $category_name.='Accessories, ';
		else $category_name.=$item_category[$row['ITEM_CATEGORY_ID']].', ';
	}

	$sql="select a.currency_id, a.lc_date, a.importer_id, b.lc_sc_id, b.is_lc_sc, b.current_distribution, b.status_active from com_btb_lc_master_details a, com_btb_export_lc_attachment b where a.id=b.import_mst_id and b.import_mst_id=$data and b.is_deleted=0 and b.status_active=1";
	$lc_sc_sql=sql_select($sql);
	
	foreach($lc_sc_sql as $row){
		// if($row[csf('is_lc_sc')]==0){
		// 	 $lc_sc="SELECT a.export_lc_no as lc_sc_no, a.buyer_name, a.lc_value as lc_sc_val,a.import_btb, a.lc_date as sc_lc_date, b.ATTACHED_QNTY as lc_qty from com_export_lc a left join com_export_lc_order_info b on a.id=b.com_export_lc_id and b.status_active=1 where a.id='".$row[csf("lc_sc_id")]."'";
		// 	$sc_lc = 'L/C';

		// }else{
		// 	 $lc_sc="SELECT a.contract_no as lc_sc_no, a.buyer_name, a.contract_value as lc_sc_val, 0 as import_btb, a.contract_date as sc_lc_date, b.ATTACHED_QNTY as lc_qty from com_sales_contract a left join com_sales_contract_order_info b on a.id=b.com_sales_contract_id and b.status_active=1 where a.id='".$row[csf("lc_sc_id")]."'";
		// 	$sc_lc = 'S/C';
		// }
		if($row[csf('is_lc_sc')]==0){
			$lc_sc = "select  wm.total_set_qnty as ratio, ci.attached_qnty as lc_qty,a.export_lc_no as lc_sc_no, a.buyer_name, a.lc_value as lc_sc_val, a.import_btb, a.lc_date as sc_lc_date
			from  com_export_lc a, wo_po_break_down wb, wo_po_details_master wm, com_export_lc_order_info ci where wb.job_no_mst = wm.job_no and wb.id=ci.wo_po_break_down_id and a.id=ci.com_export_lc_id and ci.com_export_lc_id='".$row[csf("lc_sc_id")]."' and ci.status_active = '1' and ci.is_deleted = '0' order by ci.id";
			$sc_lc = 'L/C';
		}else{
			$lc_sc = "SELECT a.contract_no as lc_sc_no, a.buyer_name,a.contract_date as sc_lc_date, a.contract_value as lc_sc_val, wm.total_set_qnty as ratio, ci.attached_qnty as lc_qty 
			from com_sales_contract a, wo_po_break_down wb, wo_po_details_master wm, com_sales_contract_order_info ci 
			where wb.job_id = wm.id and wb.id=ci.wo_po_break_down_id and a.id=ci.com_sales_contract_id and ci.com_sales_contract_id='".$row[csf("lc_sc_id")]."' and ci.status_active = '1' and ci.is_deleted = '0' and ci.is_sales=0
			order by ci.id";
			$sc_lc = 'S/C';
		}
	}

	$sql_lc__arr=array();
	$lc_sc_val_data=sql_select($lc_sc);


	foreach($lc_sc_val_data as $row)
	{
		$lc_sc_no=$row[csf('lc_sc_no')];
		$lc_date=$row[csf('sc_lc_date')];
		$lc_qty+=$row[csf('lc_qty')]*$row[csf('ratio')];
	}

	$order_uom_arr = array_unique(explode(",",chop($lc_sc_arr[$btb_id]['ORDER_UOM'],',')));
	if(count($order_uom_arr)<2)
	{
		$order_uom=$unit_of_measurement[$order_uom_arr[0]];
	}
	$lc_id = rtrim($lc_id,', ');
	$sc_id = rtrim($sc_id,', ');

	$previous_btb_value=0;
	if($lc_id!='')
	{
		$sql_lc=sql_select("SELECT b.id,b.LC_VALUE
		from com_btb_export_lc_attachment a, com_btb_lc_master_details b
		where  a.lc_sc_id in ($lc_id) and a.import_mst_id=b.id and a.status_active=1 and a.is_deleted=0 and a.is_lc_sc=0 and b.status_active=1 and b.is_deleted=0 and a.import_mst_id<$data group by b.id,b.lc_value");
		foreach($sql_lc as $row)
		{
			$previous_btb_value+= $row['LC_VALUE'];
		}
	}

	if($sc_id!='')
	{
		$sql_sc=sql_select("SELECT b.id,b.LC_VALUE
		from  com_btb_export_lc_attachment a, com_btb_lc_master_details b
		where a.lc_sc_id in ($sc_id) and a.import_mst_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.is_lc_sc=1 and a.import_mst_id<$data group by b.id,b.lc_value");

		foreach($sql_sc as $row)
		{
			$previous_btb_value+= $row['LC_VALUE'];
		}
	}

	?>
	<table width="700" cellpadding="0" align="left" cellspacing="0" border="0">
		<tr>
			<td></td>
			<td colspan="2" style="padding-top: 0;">User ID:<?= $user_library[$sql_btb_res[0]['INSERTED_BY']]; ?> </td>
			<td align="right">USER IP:<?=$user_ip?></td>			
	    </tr>
		<tr><td colspan="5" height="100"></td></tr>
		<tr>
			<td ></td>
			<td colspan="3">Ref No : <?= $sql_btb_res[0]['BTB_SYSTEM_ID']; ?></td>
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td colspan="3">Dated&nbsp;&nbsp;&nbsp;: <?= date("F d, Y", strtotime($sql_btb_res[0]['APPLICATION_DATE'])); ?></td>
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td colspan="3">The Manager / Head of the Branch</td>
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td colspan="3"><?= $bank_dtls_arr[$sql_btb_res[0]['ISSUING_BANK_ID']]['BANK_NAME']; ?></td>
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td colspan="3"><?= $bank_dtls_arr[$sql_btb_res[0]['ISSUING_BANK_ID']]['BRANCH_NAME']; ?></td>
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td colspan="3"><?= $bank_dtls_arr[$sql_btb_res[0]['ISSUING_BANK_ID']]['ADDRESS']; ?></td>
			<td ></td>
		</tr>
		<tr><td colspan="5" height="15"></td></tr>
		<tr>
			<td ></td>
			<td colspan="3"><strong>Sub:&nbsp;Request for opening of <?=$pi_source_array[$pi_source]?> Back-to-Back (BTB) L/C for <?= $currency[$sql_btb_res[0]['CURRENCY_ID']]; ?>&nbsp;<?=number_format($sql_btb_res[0]['LC_VALUE'],2); ?> against Export <?=$sc_lc?> No. <?= $lc_sc_no; ?> Dated: <?= rtrim(change_date_format($lc_date),","); ?>  Export Qty:<?=$lc_qty?> <?= $unit_of_measurement[$sql_btb_res[0]['UOM_ID']]; ?></td>
			<td width="25"></td>
		</tr>
		<tr><td colspan="5" height="15"></td></tr> 
		<tr>
			<td ></td>
			<td colspan="3">Muhtaram,</td>
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td colspan="3">Assalamu Alaikum,</td>
			<td ></td>
		</tr>
		<tr><td colspan="5" height="10"></td></tr>
		<tr>
			<td ></td>
			<td colspan="3">We would request you to please issue Back-to-Back (BTB) L/C as detailed below:-</td>
			<td ></td>
		</tr>
		<tr><td colspan="5" height="15"></td></tr>
		<tr>
			<td width="25"></td>
			<td width="280">Shipment date</td>
			<td width="20"><strong>:</strong></td>
			<td width="350"><?= change_date_format($sql_btb_res[0]['LAST_SHIPMENT_DATE']); ?></td>
			<td width="25"></td>
		</tr>
		<tr>
			<td ></td>
			<td >Expiry date</td>
			<td ><strong>:</strong></td>
			<td ><?= change_date_format($sql_btb_res[0]['LC_EXPIRY_DATE']); ?></td>
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td >BTB L/C Value</td>
			<td ><strong>:</strong></td>
			<td ><?= $currency[$sql_btb_res[0]['CURRENCY_ID']].' '.number_format($sql_btb_res[0]['LC_VALUE'],2); ?></td>			
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td >BTB L/C Qty</td>	
			<td ><strong>:</strong></td>
			<td ><?=$item_category[$sql_btb_res[0]['ITEM_CATEGORY_ID']]?>/<?=$pi_total_qty?>	
		     <?	
			// $unique_uom = array_unique($umo_val);
			if(count($umo_array) ==1){
				echo "/".$unit_of_measurement[$umo_val];
				}?></td>
		</tr>
		<tr>
			<td ></td>
			<td >Description of Goods:</td>
			<td ><strong>:</strong></td>
			<td ><?=$item_category[$sql_btb_res[0]['ITEM_CATEGORY_ID']]?> details as per PI No <?=$sql_pi_res[0]['PI_NUMBER'];?> Date <?=$sql_pi_res[0]['PI_DATE'];?></td>
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td >Supplier Name:</td>
			<td ><strong>:</strong></td>
			<td ><?=$supplier_arr[$sql_btb_res[0]['SUPPLIER_ID']];?></td>
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td >Type of the L/C</td>
			<td ><strong>:</strong></td>
			<td ><?=$maturityFrom;?></td>			
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td >Place of Receipt</td>
			<td ><strong>:</strong></td>
			<td ><?=$sql_btb_res[0]['PORT_OF_LOADING']?></td>
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td >Place of Delivery</td>
			<td ><strong>:</strong></td>
			<td ><?=$sql_btb_res[0]['PORT_OF_DISCHARGE']?></td>
			<td ></td> 
		</tr>
		<tr><td colspan="5" height="15"></td></tr>
		<tr>
			<td ></td>
			<td colspan="3">*** Documents  Required: Commercial Invoice, Details Packing List,   Bill  of Loading,  Shipment Advice, CO etc. must be mention in the documents required column.</td>
			<td ></td>
		</tr>
		<tr><td colspan="5" height="15"></td></tr>
		<tr>
			<td ></td>
			<td colspan="3">Additional condition: Export LC/Contract no,Date, Qty,Applicant IRC No: 260326120396020, TIN:  224840827408, Bond license no: 492/Cus-Bond PBW/1999 date: 03.01.1999, BIN no. 002236693-0204, Insurance  cover note no, LC issuing Bank BIN, and H.S.Code no. must be quoted in all shipping documents.
			</td>
			<td ></td>
		</tr>
		<tr><td colspan="5" height="15"></td></tr>
		<tr>
			<td ></td>
			<td colspan="3">Please debit our CD Account-for the realization of your charges under intimation to us and supply us related the vouchers.</td>
			<td ></td>
		</tr>
		<tr><td colspan="5" height="15"></td></tr>
		<tr>
			<td ></td>
			<td colspan="3">So, considering our application and documents you are requested to open <b><?=$lc_type[$sql_btb_res[0]['LC_TYPE_ID']];?></b> for <b><?= $currency[$sql_btb_res[0]['CURRENCY_ID']].' '.number_format($sql_btb_res[0]['LC_VALUE'],2); ?></b> to execute the export  order and provide us with a copy of L/C immediately after issuance of the same.
			</td>
			<td ></td>
		</tr>
		<tr><td colspan="5" height="15"></td></tr>

		<tr>
			<td ></td>
			<td colspan="3">We are very much grateful to your’s sincere and continuous co-operation</td>
			<td ></td>
		</tr>
		<tr><td colspan="5" height="15"></td></tr>
		<tr>
			<td ></td>
			<td colspan="3">Ma-Assalam </td>
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td colspan="3">Yours faithfully</td>
			<td ></td>
		</tr>
		<tr><td colspan="5" height="50"></td></tr>
		<tr>
			<td ></td>
			<td colspan="3">MD/Chairman/Director</td>
			<td ></td>
		</tr>
	</table>
	<?
	exit();
}

if ($action==='btb_import_lc_letter33')
{
	$buyer_arr =return_library_array('SELECT id, buyer_name FROM lib_buyer','id','buyer_name');
	$supplier_arr = return_library_array('SELECT id, supplier_name FROM lib_supplier','id','supplier_name');
	$company_arr = return_library_array('SELECT id, company_name FROM lib_company','id','company_name');
	$user_library=return_library_array( "select id, user_full_name from user_passwd", "id", "user_full_name"  );
	$pi_source_array=array(1=>"Local",2=>"Foreign");
	$pifor_array=array(1=>"BTB",2=>"Margin LC",3=>"Fund Buildup",4=>"TT",5=>"FTT",6=>"FDD/RTGS");


	$user_ip = $_SERVER['REMOTE_ADDR'];

	$sql_bank_info = sql_select("SELECT ID, CONTACT_PERSON, BANK_NAME, BRANCH_NAME, ADDRESS from lib_bank ");
	$bank_dtls_arr=array();
	foreach($sql_bank_info as $row)
	{
		$bank_dtls_arr[$row['ID']]['BANK_NAME']=$row['BANK_NAME'];
		$bank_dtls_arr[$row['ID']]['BRANCH_NAME']=$row['BRANCH_NAME'];
		$bank_dtls_arr[$row['ID']]['ADDRESS']=$row['ADDRESS'];
	}
	$cd_no=return_library_array("SELECT a.id, b.account_no from LIB_BANK a, LIB_BANK_ACCOUNT b where a.id=b.account_id and b.account_type=10",'id','account_no');

	$exportPiSupp = sql_select("select c.import_pi, a.id from com_btb_lc_master_details a , com_btb_lc_pi b , com_pi_master_details c where a.id = b.com_btb_lc_master_details_id and b.pi_id = c.id");
	foreach ($exportPiSupp as $value)
	{
		$exportPiSuppArr[$value[csf("id")]] = $value[csf("import_pi")];
	}
	
	// BTB Part
	 $sql_btb="SELECT ID, BTB_SYSTEM_ID, LC_DATE, APPLICATION_DATE, IMPORTER_ID, ITEM_CATEGORY_ID, ISSUING_BANK_ID, CURRENCY_ID, SUPPLIER_ID, LC_VALUE, MATURITY_FROM_ID, LAST_SHIPMENT_DATE, LC_EXPIRY_DATE, TENOR, PI_ID, LC_CATEGORY, INSERTED_BY,PAYTERM_ID,UOM_ID,LC_TYPE_ID,INCO_TERM_PLACE,PORT_OF_LOADING,PORT_OF_DISCHARGE
	from com_btb_lc_master_details
	where id=$data and is_deleted=0 and status_active=1";
	$sql_btb_res=sql_select($sql_btb);
	$btb_id = $sql_btb_res[0]['ID'];
	$importer_name = $company_arr[$sql_btb_res[0]['IMPORTER_ID']];

	if ($sql_btb_res[0]['MATURITY_FROM_ID']==1)
		$maturityFrom =  $pay_term[$sql_btb_res[0]['PAYTERM_ID']]."/".$sql_btb_res[0]['TENOR']." day’s from the date of Acceptance";
	else if ($sql_btb_res[0]['MATURITY_FROM_ID']==3)
		$maturityFrom = "(UNDER EDF)";
	else if ($sql_btb_res[0]['MATURITY_FROM_ID']==4)
		$maturityFrom = $pay_term[$sql_btb_res[0]['PAYTERM_ID']]."/".$sql_btb_res[0]['TENOR']." day’s from the date of BL";
	else if ($sql_btb_res[0]['MATURITY_FROM_ID']==5)
		$maturityFrom = $pay_term[$sql_btb_res[0]['PAYTERM_ID']]."/".$sql_btb_res[0]['TENOR']." day’s from the date of Delivery";
	else $maturityFrom='';

	// PI Part
	$pi_ids=$sql_btb_res[0]['PI_ID'];
	$sql_pi_res=sql_select("SELECT A.PI_NUMBER,a.PI_FOR,a.PI_DATE,a.ITEM_CATEGORY_ID,b.UOM,a.HS_CODE,a.SOURCE,sum(b.quantity) as QTY FROM com_pi_master_details a, com_pi_item_details b WHERE a.id = b.pi_id  and a.id in($pi_ids) and a.status_active=1 and a.is_deleted=0  Group by a.PI_NUMBER,a.PI_FOR, a.PI_DATE, a.ITEM_CATEGORY_ID,a.HS_CODE,a.SOURCE,b.UOM");

	$category_name='';$hs_code='';
	$pi_data_arr=array();$umo_array=array();
	foreach($sql_pi_res as $row)
	{
		$pi_number.=$row['PI_NUMBER'].', ';
		$pi_qty+=$row['QTY'];	
		$umo_array[$row['UOM']]=$row['UOM'];
		  $umo_val=$umo_array[$row['UOM']];
		$pi_source=$row['SOURCE'];
		$pi_for=$row['PI_FOR'];
		$pi_date.=change_date_format($row['PI_DATE']).', ';	
		if($row['HS_CODE']){$hs_code.=$row['HS_CODE'].', ';}
		if ($row['ITEM_CATEGORY_ID']==2 || $row['ITEM_CATEGORY_ID']==3) $category_name.='Fabric, ';
		else if ($row['ITEM_CATEGORY_ID']==12 || $row['ITEM_CATEGORY_ID']==24) $category_name.='Dyeing, ';
		else if ($row['ITEM_CATEGORY_ID']==25) $category_name.='Accessories, ';
		else $category_name.=$item_category[$row['ITEM_CATEGORY_ID']].', ';
	}

	$sql="select a.currency_id, a.lc_date, a.importer_id, b.lc_sc_id, b.is_lc_sc, b.current_distribution, b.status_active from com_btb_lc_master_details a, com_btb_export_lc_attachment b where a.id=b.import_mst_id and b.import_mst_id=$data and b.is_deleted=0 and b.status_active=1";
	$lc_sc_sql=sql_select($sql);
	
	foreach($lc_sc_sql as $row){
		if($row[csf('is_lc_sc')]==0){
			$lc_sc = "select  wm.total_set_qnty as ratio, ci.attached_qnty as lc_qty,a.export_lc_no as lc_sc_no, a.buyer_name, a.lc_value as lc_sc_val, a.import_btb, a.lc_date as sc_lc_date
			from  com_export_lc a, wo_po_break_down wb, wo_po_details_master wm, com_export_lc_order_info ci where wb.job_no_mst = wm.job_no and wb.id=ci.wo_po_break_down_id and a.id=ci.com_export_lc_id and ci.com_export_lc_id='".$row[csf("lc_sc_id")]."' and ci.status_active = '1' and ci.is_deleted = '0' order by ci.id";
			$sc_lc = 'L/C';
		}else{
			$lc_sc = "SELECT a.contract_no as lc_sc_no, a.buyer_name,a.contract_date as sc_lc_date, a.contract_value as lc_sc_val, wm.total_set_qnty as ratio, ci.attached_qnty as lc_qty 
			from com_sales_contract a, wo_po_break_down wb, wo_po_details_master wm, com_sales_contract_order_info ci 
			where wb.job_id = wm.id and wb.id=ci.wo_po_break_down_id and a.id=ci.com_sales_contract_id and ci.com_sales_contract_id='".$row[csf("lc_sc_id")]."' and ci.status_active = '1' and ci.is_deleted = '0' and ci.is_sales=0
			order by ci.id";
			$sc_lc = 'S/C';
		}
	}

	$sql_lc__arr=array();
	$lc_sc_val_data=sql_select($lc_sc);

	foreach($lc_sc_val_data as $row)
	{
		$lc_sc_no=$row[csf('lc_sc_no')];
		$lc_date=$row[csf('sc_lc_date')];
		$lc_qty+=$row[csf('lc_qty')]*$row[csf('ratio')];
	}

	$order_uom_arr = array_unique(explode(",",chop($lc_sc_arr[$btb_id]['ORDER_UOM'],',')));
	if(count($order_uom_arr)<2)
	{
		$order_uom=$unit_of_measurement[$order_uom_arr[0]];
	}
	$lc_id = rtrim($lc_id,', ');
	$sc_id = rtrim($sc_id,', ');

	$previous_btb_value=0;
	if($lc_id!='')
	{
		$sql_lc=sql_select("SELECT b.id,b.LC_VALUE
		from com_btb_export_lc_attachment a, com_btb_lc_master_details b
		where  a.lc_sc_id in ($lc_id) and a.import_mst_id=b.id and a.status_active=1 and a.is_deleted=0 and a.is_lc_sc=0 and b.status_active=1 and b.is_deleted=0 and a.import_mst_id<$data group by b.id,b.lc_value");
		foreach($sql_lc as $row)
		{
			$previous_btb_value+= $row['LC_VALUE'];
		}
	}

	if($sc_id!='')
	{
		$sql_sc=sql_select("SELECT b.id,b.LC_VALUE
		from  com_btb_export_lc_attachment a, com_btb_lc_master_details b
		where a.lc_sc_id in ($sc_id) and a.import_mst_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.is_lc_sc=1 and a.import_mst_id<$data group by b.id,b.lc_value");
		foreach($sql_sc as $row)
		{
			$previous_btb_value+= $row['LC_VALUE'];
		}
	}

	?>
	<table width="700" cellpadding="0" align="left" cellspacing="0" border="0">
		<tr>
			<td></td>
			<td colspan="2" style="padding-top: 0;">User ID:<?= $user_library[$sql_btb_res[0]['INSERTED_BY']]; ?> </td>
			<td align="right">USER IP:<?=$user_ip?></td>			
	    </tr>
		<tr><td colspan="5" height="100"></td></tr>
		<tr>
			<td ></td>
			<td colspan="3">Ref No : <?= $sql_btb_res[0]['BTB_SYSTEM_ID']; ?></td>
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td colspan="3">Dated&nbsp;&nbsp;&nbsp;: <?= date("F d, Y", strtotime($sql_btb_res[0]['APPLICATION_DATE'])); ?></td>
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td colspan="3">The Manager / Head of the Branch</td>
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td colspan="3"><?= $bank_dtls_arr[$sql_btb_res[0]['ISSUING_BANK_ID']]['BANK_NAME']; ?></td>
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td colspan="3"><?= $bank_dtls_arr[$sql_btb_res[0]['ISSUING_BANK_ID']]['BRANCH_NAME']; ?></td>
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td colspan="3"><?= $bank_dtls_arr[$sql_btb_res[0]['ISSUING_BANK_ID']]['ADDRESS']; ?></td>
			<td ></td>
		</tr>
		<tr><td colspan="5" height="15"></td></tr>
		<tr>
			<td ></td>
			<td colspan="3"><strong>Sub:&nbsp;Request for issue/Transfer of <?=$pifor_array[$pi_for]?> Back-to-Back (BTB) L/C for <?= $currency[$sql_btb_res[0]['CURRENCY_ID']]; ?>&nbsp;<?=number_format($sql_btb_res[0]['LC_VALUE'],2); ?> against Export <?=$sc_lc?> No. <?=$lc_sc_no; ?> Dated: <?= $lc_date ?>  Export Qty:<?=$lc_qty?> <?= $unit_of_measurement[$sql_btb_res[0]['UOM_ID']]; ?></td>
			<td width="25"></td>
		</tr>
		<tr><td colspan="5" height="15"></td></tr>
		<tr>
			<td ></td>
			<td colspan="3">Muhtaram,</td>
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td colspan="3">Assalamu Alaikum,</td>
			<td ></td>
		</tr>
		<tr><td colspan="5" height="10"></td></tr>
		<tr>
			<td ></td>
			<td colspan="3">We would request you to please issue Back-to-Back (BTB) L/C as detailed below:-</td>
			<td ></td>
		</tr>
		<tr><td colspan="5" height="15"></td></tr>
		<tr>
			<td width="25"></td>
			<td width="280">Shipment date</td>
			<td width="20"><strong>:</strong></td>
			<td width="350"><?= change_date_format($sql_btb_res[0]['LAST_SHIPMENT_DATE']); ?></td>
			<td width="25"></td>
		</tr>
		<tr>
			<td ></td>
			<td >Expiry date</td>
			<td ><strong>:</strong></td>
			<td ><?= change_date_format($sql_btb_res[0]['LC_EXPIRY_DATE']); ?></td>
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td >BTB L/C Value</td>
			<td ><strong>:</strong></td>
			<td ><?= $currency[$sql_btb_res[0]['CURRENCY_ID']].' '.number_format($sql_btb_res[0]['LC_VALUE'],2); ?></td>			
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td >BTB L/C Qty</td>	
			<td ><strong>:</strong></td>
			<td ><?=$item_category[$sql_btb_res[0]['ITEM_CATEGORY_ID']]?>/<?=$pi_qty?> <?	
			// $unique_uom = array_unique($umo_val);
			if(count($umo_array) ==1){
				echo "/".$unit_of_measurement[$umo_val];
				}?></td>
		</tr>
		<tr>
			<td ></td>
			<td >Description of Goods:</td>
			<td ><strong>:</strong></td>
			<td ><?=$item_category[$sql_btb_res[0]['ITEM_CATEGORY_ID']]?> details as per PI No <?=$sql_pi_res[0]['PI_NUMBER'];?> Date <?=$sql_pi_res[0]['PI_DATE'];?></td>
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td >Supplier Name:</td>
			<td ><strong>:</strong></td>
			<td ><?=$supplier_arr[$sql_btb_res[0]['SUPPLIER_ID']];?></td>
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td >Type of the L/C</td>
			<td ><strong>:</strong></td>
			<td ><?=$maturityFrom;?></td>			
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td >Place of Receipt</td>
			<td ><strong>:</strong></td>
			<td ><?=$sql_btb_res[0]['PORT_OF_LOADING']?></td>
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td >Place of Delivery</td>
			<td ><strong>:</strong></td>
			<td ><?=$sql_btb_res[0]['PORT_OF_DISCHARGE']?></td>
			<td ></td>
		</tr>
		<tr><td colspan="5" height="15"></td></tr>
		<tr>
			<td ></td>
			<td colspan="3">** Documents Required: Commercial Invoice, Details Packing List, Bill of Loading, Shipment Advice, CO etc. must be mention in the documents required column.</td>
			<td ></td>
		</tr>
		<tr><td colspan="5" height="15"></td></tr>
		<tr>
			<td ></td>
			<td colspan="3">**Additional condition: Export Lc/Contract no., Date, Qty., Applicant IRC No.: 260326120396020, TIN: 224840827408, Bond license no.: 492/Cus-Bond PBW/1999 date: 03.01.1999, BIN no. 002236693-0204, Insurance cover note no., Lc issuing Bank BIN, and H.S. Code no. must be quoted in all shipping documents..
			</td>
			<td ></td>
		</tr>
		<tr><td colspan="5" height="15"></td></tr>
		<tr>
			<td ></td>
			<td colspan="3">Please debit our ERQ/FC/CD Account- for the realization of your charges under intimation to us and supply us related the vouchers.</td>
			<td ></td>
		</tr>
		<tr><td colspan="5" height="15"></td></tr>
		<tr>
			<td ></td>
			<td colspan="3">So, considering our application and papers you are requested to issue/transfer  <b><?=$pifor_array[$pi_for]?></b> for <b><?= $currency[$sql_btb_res[0]['CURRENCY_ID']].' '.number_format($sql_btb_res[0]['LC_VALUE'],2); ?></b> to execute the export order and provide us with a copy of TT immediately after issuance of the same.
			</td>
			<td ></td>
		</tr>
		<tr><td colspan="5" height="15"></td></tr>

		<tr>
			<td ></td>
			<td colspan="3">We are very much grateful to your’s sincere and continuous co-operation</td>
			<td ></td>
		</tr>
		<tr><td colspan="5" height="15"></td></tr>
		<tr>
			<td ></td>
			<td colspan="3">Ma-Assalam </td>
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td colspan="3">Yours faithfully</td>
			<td ></td>
		</tr>
		<tr><td colspan="5" height="50"></td></tr>
		<tr>
			<td ></td>
			<td colspan="3">MD/Chairman/Director</td>
			<td ></td>
		</tr>
	</table>
	<?
	exit();
}

if ($action==='btb_import_lc_letter35')
{
	$buyer_arr =return_library_array('SELECT id, buyer_name FROM lib_buyer','id','buyer_name');
	$supplier_arr = return_library_array('SELECT id, supplier_name FROM lib_supplier','id','supplier_name');
	$company_arr = return_library_array('SELECT id, company_name FROM lib_company','id','company_name');
	$user_library=return_library_array( "select id, user_full_name from user_passwd", "id", "user_full_name"  );
	$erc_no_arr = return_library_array('SELECT id, erc_no FROM lib_company','id','erc_no');
	$irc_no_arr = return_library_array('SELECT id, irc_no FROM lib_company','id','irc_no');
	$bin_no_arr = return_library_array('SELECT id, bin_no FROM lib_company','id','bin_no');
	$vat_number_arr = return_library_array('SELECT id, vat_number FROM lib_company','id','vat_number');
	$rex_no_arr = return_library_array('SELECT id, rex_no FROM lib_company','id','rex_no');

	$sql_bank_info = sql_select("SELECT a.ID, a.CONTACT_PERSON, a.BANK_NAME, a.BRANCH_NAME, a.ADDRESS, b.ACCOUNT_NO,b.COMPANY_ID from lib_bank a, LIB_BANK_ACCOUNT b where a.id=b.account_id and a.status_active=1 and b.status_active=1 and b.ACCOUNT_TYPE=10");
	$bank_dtls_arr=array();$bank_data=array();
	foreach($sql_bank_info as $row)
	{
		$bank_dtls_arr[$row['ID']]['BANK_NAME']=$row['BANK_NAME'];
		$bank_dtls_arr[$row['ID']]['BRANCH_NAME']=$row['BRANCH_NAME'];
		$bank_dtls_arr[$row['ID']]['ADDRESS']=$row['ADDRESS'];
		$bank_data[$row["ID"]][$row['COMPANY_ID']]["ACCOUNT_NO"]=$row["ACCOUNT_NO"];
	}

	$exportPiSupp = sql_select("select c.import_pi, a.id from com_btb_lc_master_details a , com_btb_lc_pi b , com_pi_master_details c where a.id = b.com_btb_lc_master_details_id and b.pi_id = c.id");
	foreach ($exportPiSupp as $value)
	{
		$exportPiSuppArr[$value[csf("id")]] = $value[csf("import_pi")];
	}
	
	// BTB Part
	 $sql_btb="SELECT ID, BTB_SYSTEM_ID, LC_DATE, APPLICATION_DATE, IMPORTER_ID, ITEM_CATEGORY_ID, ISSUING_BANK_ID, CURRENCY_ID, SUPPLIER_ID, LC_VALUE, MATURITY_FROM_ID, LAST_SHIPMENT_DATE, LC_EXPIRY_DATE, TENOR, PI_ID, LC_CATEGORY, INSERTED_BY,PAYTERM_ID,UOM_ID,LC_TYPE_ID,INCO_TERM_PLACE,PORT_OF_LOADING,APPLICATION_DATE,PORT_OF_DISCHARGE, PI_VALUE, LC_EXPIRY_DATE, INCO_TERM_ID,GARMENTS_QTY, ADVISING_BANK, ADVISING_BANK_ADDRESS
	from com_btb_lc_master_details  
	where id=$data and is_deleted=0 and status_active=1";
	$sql_btb_res=sql_select($sql_btb);
	$btb_id = $sql_btb_res[0]['ID'];
	$advising_bank = $sql_btb_res[0]['ADVISING_BANK'];
	$advising_bank_address = $sql_btb_res[0]['ADVISING_BANK_ADDRESS'];
	$importer_name = $company_arr[$sql_btb_res[0]['IMPORTER_ID']];

	if ($sql_btb_res[0]['MATURITY_FROM_ID']==1)
		$maturityFrom =  $pay_term[$sql_btb_res[0]['PAYTERM_ID']]."/".$sql_btb_res[0]['TENOR']." day’s from the date of Acceptance";
	else if ($sql_btb_res[0]['MATURITY_FROM_ID']==3)
		$maturityFrom = "(UNDER EDF)";
	else if ($sql_btb_res[0]['MATURITY_FROM_ID']==4)
		$maturityFrom = $pay_term[$sql_btb_res[0]['PAYTERM_ID']]."/".$sql_btb_res[0]['TENOR']." day’s from the date of BL";
	else if ($sql_btb_res[0]['MATURITY_FROM_ID']==5)
		$maturityFrom = $pay_term[$sql_btb_res[0]['PAYTERM_ID']]."/".$sql_btb_res[0]['TENOR']." day’s from the date of Delivery";
	else $maturityFrom='';
	$pi_ids=$sql_btb_res[0]['PI_ID'];
	// PI Part
	$sql_pi_res=sql_select("SELECT A.PI_NUMBER,a.PI_DATE,a.ITEM_CATEGORY_ID,b.UOM,a.HS_CODE,a.SOURCE,sum(b.quantity) as QTY FROM com_pi_master_details a, com_pi_item_details b WHERE a.id = b.pi_id  and a.id in($pi_ids) and a.status_active=1 and a.is_deleted=0  Group by a.PI_NUMBER, a.PI_DATE, a.ITEM_CATEGORY_ID,b.UOM,a.HS_CODE,a.SOURCE");

	$category_name='';$hs_code='';
	$pi_data_arr=array();
	$umo_array=array();
	foreach($sql_pi_res as $row)
	{   $pi_dates=change_date_format($row['PI_DATE']);
		$pi_number .="<tr><td><b>{$row['PI_NUMBER']}</b></td><td> DATE: {$pi_dates}</td></tr>".'';
		$pi_source=$row['SOURCE'];
		$pi_qty+=$row['QTY'];
		$umo_array[$row['UOM']]=$row['UOM'];
		$umo_val=$umo_array[$row['UOM']];

		$pi_date.=change_date_format($row['PI_DATE']).', ';	
		if($row['HS_CODE']){$hs_code.=$row['HS_CODE'].',';}
		if ($row['ITEM_CATEGORY_ID']==2 || $row['ITEM_CATEGORY_ID']==3) $category_name.='Fabric, ';
		else if ($row['ITEM_CATEGORY_ID']==12 || $row['ITEM_CATEGORY_ID']==24) $category_name.='Dyeing, ';
		else if ($row['ITEM_CATEGORY_ID']==25) $category_name.='Accessories, ';
		else $category_name.=$item_category[$row['ITEM_CATEGORY_ID']].', ';
	}

		$sql_lcSc=sql_select("SELECT a.LC_SC_ID,a.IMPORT_MST_ID, b.contract_no as LC_SC_NO, b.contract_date as LC_SC_DATE, b.contract_value as LC_SC_VALUE, a.IS_LC_SC, b.EXPIRY_DATE, b.last_shipment_date
		from  com_btb_export_lc_attachment a, com_sales_contract b
		where a.lc_sc_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and a.is_lc_sc=1 and a.import_mst_id=$data
		union all 
		select a.LC_SC_ID,a.IMPORT_MST_ID, b.export_lc_no as LC_SC_NO, b.lc_date as LC_SC_DATE, b.lc_value as LC_SC_VALUE, a.IS_LC_SC, b.EXPIRY_DATE, b.last_shipment_date
		from com_btb_export_lc_attachment a, com_export_lc b
		where a.lc_sc_id=b.id and a.status_active=1 and a.is_deleted=0 and a.is_lc_sc=0 and b.status_active=1 and b.is_deleted=0 and a.import_mst_id=$data");	

		$lc_sc_arrr=array();
		$lc_sc_date_arr=array();
		foreach($sql_lcSc as $row)
		{
			$lc_sc_arrr[$row['IMPORT_MST_ID']]['LC_SC_NO'].=$row['LC_SC_NO'].', ';
			$lc_sc_arrr[$row['IMPORT_MST_ID']]['LC_SC_DATE'].=change_date_format($row['LC_SC_DATE']).', ';
			$lc_sc_date_arr[$row['IMPORT_MST_ID']]['LAST_SHIPMENT_DATE'].=change_date_format($row['LAST_SHIPMENT_DATE']).', ';
			$lc_sc_date_arr[$row['IMPORT_MST_ID']]['EXPIRY_DATE'].=change_date_format($row['EXPIRY_DATE']).', ';
			$lc_sc_val+= $row['LC_SC_VALUE'];
		}

		$lc_sc_no   = rtrim($lc_sc_arrr[$btb_id]['LC_SC_NO'],', ');
	    $lc_date = rtrim($lc_sc_arrr[$btb_id]['LC_SC_DATE'],', ');
	    $last_shipment_date = rtrim($lc_sc_date_arr[$btb_id]['LAST_SHIPMENT_DATE'],', ');
	    $expiry_date = rtrim($lc_sc_date_arr[$btb_id]['EXPIRY_DATE'],', ');


	if(count($order_uom_arr)<2)
	{
		$order_uom=$unit_of_measurement[$order_uom_arr[0]];
	}
	$lc_id = rtrim($lc_id,', ');
	$sc_id = rtrim($sc_id,', ');

	$previous_btb_value=0;
	if($lc_id!='')
	{
		$sql_lc=sql_select("SELECT b.id,b.LC_VALUE
		from com_btb_export_lc_attachment a, com_btb_lc_master_details b
		where  a.lc_sc_id in ($lc_id) and a.import_mst_id=b.id and a.status_active=1 and a.is_deleted=0 and a.is_lc_sc=0 and b.status_active=1 and b.is_deleted=0 and a.import_mst_id<$data group by b.id,b.lc_value");
		foreach($sql_lc as $row)
		{
			$previous_btb_value+= $row['LC_VALUE'];
		}
	}

	if($sc_id!='')
	{
		$sql_sc=sql_select("SELECT b.id,b.LC_VALUE
		from  com_btb_export_lc_attachment a, com_btb_lc_master_details b
		where a.lc_sc_id in ($sc_id) and a.import_mst_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.is_lc_sc=1 and a.import_mst_id<$data group by b.id,b.lc_value");
		foreach($sql_sc as $row)
		{
			$previous_btb_value+= $row['LC_VALUE'];
		}
	}

	if($vat_number_arr[$sql_btb_res[0]['IMPORTER_ID']]!=""){
		$bin_no="/ ".$bin_no_arr[$sql_btb_res[0]['IMPORTER_ID']];
	}else{
		$bin_no=$bin_no_arr[$sql_btb_res[0]['IMPORTER_ID']];
	}

	?>
	<table width="850" cellpadding="0" align="left" cellspacing="0" border="0">
		<tr style="height: 130px;"></tr>
		<tr>
			<td ></td>
			<td colspan="3">Reference no : <?= $sql_btb_res[0]['BTB_SYSTEM_ID']; ?></td>
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td colspan="3">Date : <?= date("F d, Y", strtotime($sql_btb_res[0]['APPLICATION_DATE'])); ?></td>
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td colspan="3">The Manager</td>
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td colspan="3"><?= $bank_dtls_arr[$sql_btb_res[0]['ISSUING_BANK_ID']]['BANK_NAME']; ?></td>
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td colspan="3"><?= $bank_dtls_arr[$sql_btb_res[0]['ISSUING_BANK_ID']]['BRANCH_NAME']; ?></td>
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td colspan="3"><?= $bank_dtls_arr[$sql_btb_res[0]['ISSUING_BANK_ID']]['ADDRESS']; ?></td>
			<td ></td>
		</tr>
		<tr><td colspan="5" height="15"></td></tr>
		<tr>
			<td ></td>
			<td colspan="3">Subject:&nbsp; Request to open Local Back to Back for <b><?= $currency[$sql_btb_res[0]['CURRENCY_ID']]." $"; ?>&nbsp;<?=number_format($sql_btb_res[0]['LC_VALUE'],2); ?></b> to Import <b><?=$item_category[$sql_btb_res[0]['ITEM_CATEGORY_ID']]?></b> From. <b><?=$supplier_arr[$sql_btb_res[0]['SUPPLIER_ID']]; ?></b></td>
			<td width="25"></td>
		</tr>
		<tr><td colspan="5" height="15"></td></tr>
		<tr>
			<td ></td>
			<td colspan="3">Dear Sir</td>
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td colspan="3">With reference to the above we would like to request you to open Back to Back LC as per following particulars:</td>
			<td ></td>
		</tr>
		<tr><td colspan="5" height="15"></td></tr>
		<tr>
			<td width="25"></td>
			<td width="280">1. &nbsp; &nbsp;Export LC / Contract No.</td>
			<td width="20"><strong>:</strong></td>
			<td width="500"> <b><?= $lc_sc_no; ?></b></td> 
			<td width="25"></td>
		</tr>
		<tr>
			<td width="25"></td>
			<td width="280">2. &nbsp; &nbsp;Date of Export LC / Contract.</td>
			<td width="20"><strong>:</strong></td>
			<td width="500"><?= $lc_date; ?></td>
			<td width="25"></td>
		</tr>
		<tr>
			<td width="25"></td>
			<td width="280">3. &nbsp; &nbsp;Export LC / Contract Value.</td>
			<td width="20"><strong>:</strong></td>
			<td width="500"><?= "$ ".$lc_sc_val."&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;=".$sql_btb_res[0]['GARMENTS_QTY']." ".$unit_of_measurement[$sql_btb_res[0]['UOM_ID']]; ?></td>
			<td width="25"></td>
		</tr>
		<tr>
			<td width="25"></td>
			<td width="280">4. &nbsp; &nbsp;Latest date of shipment.</td>
			<td width="20"><strong>:</strong></td>
			<td width="500"><?= $last_shipment_date; ?></td>
			<td width="25"></td>
		</tr>
		<tr>
			<td width="25"></td>
			<td width="280">5. &nbsp; &nbsp;Expiry date.</td>
			<td width="20"><strong>:</strong></td>
			<td width="500"><?= $expiry_date; ?></td>
			<td width="25"></td>
		</tr>
		<tr>
			<td width="25"></td>
			<td width="280">6. &nbsp; &nbsp;BTB opened.</td>
			<td width="20"><strong>:</strong></td>
			<td width="500"> </td>
			<td width="25"></td>
		</tr>
		<tr>
			<td width="25"></td>
			<td width="280">7. &nbsp; &nbsp;Value of BTB Proposed.</td>
			<td width="20"><strong>:</strong></td>
			<td width="500"><b><?= "$ ".$sql_btb_res[0]['PI_VALUE']; ?></b></td>
			<td width="25"></td>
		</tr>
		<tr style="vertical-align: top;">
			<td width="25"></td>
			<td width="280">8. &nbsp; &nbsp;Invoice No. of proposed BTB.</td>
			<td width="20"><strong>:</strong></td>
			<td width="500"><b> <table width="500"><?= rtrim($pi_number,""); ?></table></b></td>
			<td width="25"></td>
		</tr>
		<tr>
			<td width="25"></td>
			<td width="280">9. &nbsp; &nbsp;Goods under proposed BTB.</td>
			<td width="20"><strong>:</strong></td>
			<td width="500"><b><?= $item_category[$sql_btb_res[0]['ITEM_CATEGORY_ID']]; ?></b></td>
			<td width="25"></td>
		</tr>
		<tr>
			<td width="25"></td>
			<td width="280">10.&nbsp; &nbsp;Name of the Beneficiary.</td>
			<td width="20"><strong>:</strong></td>
			<td width="500"><b><?= $supplier_arr[$sql_btb_res[0]['SUPPLIER_ID']]; ?></b></td>
			<td width="25"></td>
		</tr>
		<tr>
			<td width="25"></td>
			<td width="280">11.&nbsp; &nbsp;Beneficiary's Bank.</td>
			<td width="20"><strong>:</strong></td>
			<td width="500"><b><?=  $advising_bank; ?></b></td>
			<td width="25"></td>
		</tr>
		<tr>
			<td width="25"></td>
			<td width="280"></td>
			<td width="20"><strong></strong></td>
			<td width="500"><?= $advising_bank_address; ?></td>
			<td width="25"></td>
		</tr>
		<tr>
			<td width="25"></td>
			<td width="280">12.&nbsp; &nbsp;BTB shipment Date.</td>
			<td width="20"><strong>:</strong></td>
			<td width="500"><b><?= change_date_format($sql_btb_res[0]['LAST_SHIPMENT_DATE']); ?></b></td>
			<td width="25"></td>
		</tr><tr>
			<td width="25"></td>
			<td width="280">13.&nbsp; &nbsp;BTB Expiry Date.</td>
			<td width="20"><strong>:</strong></td>
			<td width="500"><b><?= change_date_format($sql_btb_res[0]['LC_EXPIRY_DATE']); ?></b></td>
			<td width="25"></td>
		</tr><tr>
			<td width="25"></td>
			<td width="280">14.&nbsp; &nbsp;Currency.</td>
			<td width="20"><strong>:</strong></td>
			<td width="500"><?= $currency[$sql_btb_res[0]['CURRENCY_ID']]; ?></td>
			<td width="25"></td>
		</tr><tr>
			<td width="25"></td>
			<td width="280">15.&nbsp; &nbsp;Incoterms.</td>
			<td width="20"><strong>:</strong></td>
			<td width="500"><?= $incoterm[$sql_btb_res[0]['INCO_TERM_ID']]; ?></td>
			<td width="25"></td>
		</tr>
		<tr>
			<td width="25"></td>
			<td width="280">16.&nbsp; &nbsp;H. S. Code.</td>
			<td width="20"><strong>:</strong></td>
			<td width="500"><?= rtrim($hs_code,","); ?></td>
			<td width="25"></td>
		</tr>
		<tr>
			<td width="25"></td>
			<td width="280">17.&nbsp; &nbsp;Utilized LC Limit in FC.</td>
			<td width="20"><strong>:</strong></td>
			<td width="500"></td>
			<td width="25"></td>
		</tr>
		<tr>
			<td width="25"></td>
			<td width="280">18.&nbsp; &nbsp;Credit Limit in FC.</td>
			<td width="20"><strong>:</strong></td>
			<td width="500"></td>
			<td width="25"></td>
		</tr>
		<tr>
			<td width="25"></td>
			<td width="280">19.&nbsp; &nbsp;ERC No.</td>
			<td width="20"><strong>:</strong></td>
			<td width="500"><?= $erc_no_arr[$sql_btb_res[0]['IMPORTER_ID']]; ?></td>
			<td width="25"></td>
		</tr>
		<tr>
			<td width="25"></td>
			<td width="280">20.&nbsp; &nbsp;IRC No.</td>
			<td width="20"><strong>:</strong></td>
			<td width="500"><?= $irc_no_arr[$sql_btb_res[0]['IMPORTER_ID']]; ?></td>
			<td width="25"></td>
		</tr>
		<tr> 
			<td width="25"></td>
			<td width="280">21.&nbsp; &nbsp;VAT / BIN No.</td>
			<td width="20"><strong>:</strong></td>
			<td width="500"><?= $vat_number_arr[$sql_btb_res[0]['IMPORTER_ID']]." ".$bin_no; ?></td>
			<td width="25"></td>
		</tr><tr>
			<td width="25"></td>
			<td width="280">22.&nbsp; &nbsp;BOND LICENSE NO.</td>
			<td width="20"><strong>:</strong></td>
			<td width="500"><?= $rex_no_arr[$sql_btb_res[0]['IMPORTER_ID']]; ?></td>
			<td width="25"></td>
		</tr><tr>
			<td width="25"></td>
			<td width="280">23.&nbsp; &nbsp;Charges / Commission.</td>
			<td width="20"><strong>:</strong></td>
			<td width="500"><?= "On Our CD A /C No.<b>".$bank_data[$sql_btb_res[0]['ISSUING_BANK_ID']][$sql_btb_res[0]['IMPORTER_ID']]["ACCOUNT_NO"]."</b>"; ?></td>
			<td width="25"></td>
		</tr>
		<tr><td colspan="5" height="15"></td></tr>
		
		<tr>
			<td ></td>
			<td colspan="3">So please arrange establishment of the LC at your earliest.</td>
			<td ></td>
		</tr>
		<tr><td colspan="5" height="20"></td></tr>
		<tr>
			<td ></td>
			<td colspan="3">Yours faithfully</td>
			<td ></td>
		</tr>
		<tr><td colspan="5" height="20"></td></tr>
		<tr>
			<td ></td>
			<td colspan="3"><b></b></td>
			<td ></td>
		</tr>
	</table>
	<?
	exit();
}
if ($action==='btb_import_lc_letter36')
{
	
	$sql = "SELECT	importer_id, issuing_bank_id, lc_value, lc_date, currency_id, tenor, supplier_id, item_category_id, pi_id, port_of_loading, port_of_discharge, delivery_mode_id, last_shipment_date, lc_expiry_date, inco_term_place, doc_presentation_days, origin,inco_term_id, insurance_company_name, cover_note_no, cover_note_date, MATURITY_FROM_ID, payterm_id, tolerance, lca_no,lcaf_no, lc_number, lc_date, btb_system_id, pi_id, partial_shipment, transhipment, garments_qty, uom_id, advising_bank, advising_bank_address, remarks, lc_type_id,LC_EXPIRY_DAYS
	from com_btb_lc_master_details
	where id='$data' ";
    //echo $sql;
    $data_array = sql_select($sql);
    $all_pi_ids = $data_array[0][csf("pi_id")];
	
	//echo $data_array[0][csf("lc_type_id")];

    $is_lc_sc_sql = sql_select("select lc_sc_id, is_lc_sc from com_btb_export_lc_attachment where import_mst_id=$data and is_deleted=0 and status_active=1");
    if (empty($is_lc_sc_sql) && $data_array[0][csf("lc_type_id")] !=2) {
        echo "May be attachment not complete yet";
        die;
    }
    foreach ($is_lc_sc_sql as $row) {
        if ($row[csf('is_lc_sc')] == 0) {
            $sql_lc = sql_select("SELECT id,export_lc_no, lc_date FROM com_export_lc  where id=" . $row[csf('lc_sc_id')]);
            foreach ($sql_lc as $lc_row) {
                $export_lc_sc_no_arr[$lc_row[csf("id")]] = $lc_row[csf("export_lc_no")] . " DT: " . date('d.m.Y',strtotime($lc_row[csf("lc_date")]));
            }
        } else {
            $sql_sc = sql_select("SELECT id,contract_no,contract_date FROM com_sales_contract where id=" . $row[csf('lc_sc_id')]);
            foreach ($sql_sc as $sc_row) {
                $export_lc_sc_no_arr[$sc_row[csf("id")]] = $sc_row[csf("contract_no")] . " DT: " . date('d.m.Y',strtotime($sc_row[csf("contract_date")]));
            }
        }
        $lc_sc_id_arr[] = $row[csf('lc_sc_id')];
    }


    $order_pcs_qty = return_field_value("sum(b.order_quantity) as order_quantity", "com_export_lc_order_info a,wo_po_color_size_breakdown b", "b.po_break_down_id=a.wo_po_break_down_id and a.com_export_lc_id in(" . implode(',', $lc_sc_id_arr) . ")", "order_quantity");


    //--------------lib
    $currency_sign_arr = array(1 => '৳', 2 => '$', 3 => '€', 4 => '€', 5 => '$', 6 => '£', 7 => '¥');
    $company_name = return_field_value("company_name", "lib_company", "id=" . $data_array[0][csf("importer_id")], "company_name");
    $bang_bank_reg_no = return_field_value("bang_bank_reg_no", "lib_company", "id=" . $data_array[0][csf("importer_id")], "bang_bank_reg_no");

    $country_array = return_library_array("select id,country_name from lib_country where is_deleted=0", "id", "country_name");
    $address = sql_select("SELECT id,plot_no,level_no,road_no,block_no,country_id,city,zip_code,irc_no,tin_number,vat_number,bang_bank_reg_no,bin_no,business_nature from lib_company where id = " . $data_array[0][csf('importer_id')] . "");
    foreach ($address as $row) {
        $company_add[$row[csf('id')]]['plot_no'] = $row[csf('plot_no')];
        $company_add[$row[csf('id')]]['level_no'] = $row[csf('level_no')];
        $company_add[$row[csf('id')]]['road_no'] = $row[csf('road_no')];
        $company_add[$row[csf('id')]]['block_no'] = $row[csf('block_no')];
        $company_add[$row[csf('id')]]['country_id'] = $row[csf('country_id')];
        $company_add[$row[csf('id')]]['city'] = $row[csf('city')];
        $company_add[$row[csf('id')]]['zip_code'] = $row[csf('zip_code')];
        $company_add[$row[csf('id')]]['irc_no'] = $row[csf('irc_no')];
        $company_add[$row[csf('id')]]['tin_number'] = $row[csf('tin_number')];
        $company_add[$row[csf('id')]]['vat_number'] = $row[csf('vat_number')];
        $company_add[$row[csf('id')]]['bang_bank_reg_no'] = $row[csf('bang_bank_reg_no')];
        $company_add[$row[csf('id')]]['bin_no'] = $row[csf('bin_no')];
    }
    $business_nature=explode(',',$address[0][csf('business_nature')]);
	foreach($business_nature as $row)
	{
		$business_nature_info=$business_nature_arr[$row].',';
	}
    //print_r($company_add);

    $branch = return_field_value("branch_name", "lib_bank", "id=" . $data_array[0][csf("issuing_bank_id")], "branch_name");
    $bank_name = return_field_value("bank_name", "lib_bank", "id=" . $data_array[0][csf("issuing_bank_id")], "bank_name");
    $currency_sign = $currency_sign_arr[$data_array[0][csf("currency_id")]];
    $supplier_name = return_field_value("supplier_name", "lib_supplier", "id=" . $data_array[0][csf("supplier_id")], "supplier_name");
    $supplier_add = return_field_value("address_1", "lib_supplier", "id=" . $data_array[0][csf("supplier_id")], "address_1");

	$pi_number_arr=return_library_array( "SELECT id, pi_number from com_pi_master_details where id in(".$data_array[0][csf("pi_id")].")  AND status_active = 1 AND is_deleted = 0",'id','pi_number');
	$hs_code_arr=return_library_array( "SELECT id, HS_CODE from com_pi_master_details where id in(".$data_array[0][csf("pi_id")].")  AND status_active = 1 AND is_deleted = 0",'id','HS_CODE');
	$pi_date_arr=return_library_array( "SELECT id, pi_date from com_pi_master_details where id in(".$data_array[0][csf("pi_id")].")  AND status_active = 1 AND is_deleted = 0",'id','pi_date');
	// $pi_date = return_field_value("pi_date","com_pi_master_details"," status_active = 1 AND is_deleted = 0 AND id=".$data_array[0][csf("pi_id")],"pi_date");
	$pi_cate_arr=return_library_array( "SELECT id, item_category_id from com_pi_master_details where id in(".$data_array[0][csf("pi_id")].")  AND status_active = 1 AND is_deleted = 0",'id','item_category_id');

    $pi_numbers;
	if(count($pi_number_arr)>1){
		foreach($pi_number_arr as $key=>$row){
            $pi_numbers .= $row.'&nbsp;DT: '.date('d.m.Y',strtotime($pi_date_arr[$key])).", ";
		}
        $pi_numbers=rtrim($pi_numbers,", ");
        /* $pi_numbers .= " Pls Check Attached LC Forwarding Letter.";
        $pi_date=''; */
	}
	else
	{
		$pi_numbers = $pi_number_arr[$data_array[0][csf("pi_id")]].'&nbsp;DT: '.date('d.m.Y',strtotime($pi_date_arr[$data_array[0][csf("pi_id")]]));
	}

    if(count($hs_code_arr)>1){
        foreach($hs_code_arr as $row){
            $hs_code .= $row.",";
        }
	}
	else
	{
		$hs_code = $hs_code_arr[$data_array[0][csf("pi_id")]];
	}

    if(count($pi_cate_arr)>1){
		foreach($pi_cate_arr as $row){
			$pi_category .= $item_category[$row].",";
		}
		$pi_category=implode(", ",array_unique(explode(",",chop($pi_category,','))));		
	}
	else
	{
		$pi_category= $item_category[$pi_cate_arr[$data_array[0][csf("pi_id")]]];
	}

    if ($data_array[0][csf("last_shipment_date")] != '') {
        $last_shipment_date = date('d.m.Y', strtotime($data_array[0][csf("last_shipment_date")]));
    } else {
        $last_shipment_date = '';
    }


    if ($data_array[0][csf("lc_expiry_date")] != '') {
        $lc_expiry_date = date('d.m.Y', strtotime($data_array[0][csf("lc_expiry_date")]));
    } else {
        $lc_expiry_date = '';
    }
    $origin = return_field_value("country_name", " lib_country", "id=" . $data_array[0][csf("origin")], "country_name");
    $inco_term_id = $incoterm[$data_array[0][csf("inco_term_id")]];


    if ($data_array[0][csf("cover_note_date")] != '') {
        $cover_note_date = date('d.m.Y', strtotime($data_array[0][csf("cover_note_date")]));
    } else {
        $cover_note_date = '';
    }

    if ($data_array[0][csf("payterm_id")] == 1) {
        $pay_term_cond = $pay_term[$data_array[0][csf("payterm_id")]];
    } else {
        $pay_term_cond = $data_array[0][csf("tenor")] . "Day's";
    }
    if ($data_array[0][csf("doc_presentation_days")] == "") {
        $doc_presentation_days = "";
    } else {
        $doc_presentation_days = $data_array[0][csf("doc_presentation_days")] . "Days";
    }

    $nature = return_field_value("business_nature", "lib_company", "id=" . $data_array[0][csf("importer_id")], "business_nature");
    $business_nature_arr = explode(",", $nature);
    $business_nature;
    if (count($business_nature_arr) > 0) {
        foreach ($business_nature_arr as $row) {
            if ($row == 2) {
                $business_nature .= "Knit ";
            } elseif ($row == 3) {
                $business_nature .= "Woven ";
            } elseif ($row == 4) {
                $business_nature .= "Trims ";
            } elseif ($row == 5) {
                $business_nature .= "Print ";
            } elseif ($row == 6) {
                $business_nature .= "Embroidery ";
            } elseif ($row == 7) {
                $business_nature .= "Wash ";
            } elseif ($row == 8) {
                $business_nature .= "Yarn Dyeing ";
            } elseif ($row == 9) {
                $business_nature .= "AOP ";
            } elseif ($row == 100) {
                $business_nature .= "Sweater ";
            }
        }
    }
    $currency_id = $data_array[0][csf("currency_id")];
    //$mcurrency, $dcurrency;
    $dcurrency = "";
    if ($currency_id == 1) {
        $mcurrency = 'Taka';
        $dcurrency = 'Paisa';
    }
    if ($currency_id == 2) {
        $mcurrency = 'USD';
        $dcurrency = 'CENTS';
    }
    if ($currency_id == 3) {
        $mcurrency = 'EURO';
        $dcurrency = 'CENTS';
    }
     ?>


    <style>
        body{
            margin:0;padding:0; font-size: 90%;
            background: url("../application_form/form_image/image/sibl_cf7.jpg") ;
            background-size:21.59cm 30.56cm;
            background-repeat: no-repeat;
        }
        *{font-weight: bold;}
		#position1{position: absolute;margin-top: 83px;margin-left: 240px;}
        #position2{position: absolute;margin-top: 300px;margin-left: 75px;}
        #position3{position: absolute;margin-top: 303px;margin-left: 310px; width:350px;}
        #position4{position: absolute;margin-top: 365px;margin-left: 75px; }
        #position5{position: absolute;margin-top: 365px;margin-left: 310px; width:360px;}
        #position6{position: absolute;margin-top: 393px;margin-left: 160px;}
         #position7{position: absolute;margin-top: 410px;margin-left: 300px;} 
		 #position23{position: absolute;margin-top: 410px;margin-left: 499px;}
		 #position24{position: absolute;margin-top: 800px;margin-left: 680px;}
		 #position25{position: absolute;margin-top: 800px;margin-left: 190px;width: 200px;}
		 #position26{position: absolute;margin-top: 800px;margin-left: 370px; width: 250px;}

        #position8{position: absolute;margin-top: 460px;margin-left: 75px; width:460px;}
        #position9{position: absolute;margin-top: 1170px;margin-left: 75px;}
        #position10{position: absolute;margin-top: 475px;margin-left: 545px;}
        #position11{position: absolute;margin-top: 515px;margin-left: 590px;} 
        /* #position12{position: absolute;margin-top: 540px;margin-left: 500px;}   */
        #position13{position: absolute;margin-top: 725px;margin-left: 160px;}  
        #position14{position: absolute;margin-top: 725px;margin-left: 330px;}  
        /* #position15{position: absolute;margin-top: 760px;margin-left: 150px;}  
        #position16{position: absolute;margin-top: 760px;margin-left: 400px;}  
        #position17{position: absolute;margin-top: 770px;margin-left: 400px;}   */
        #position18{position: absolute;margin-top: 1090px;margin-left: 75px;}  
        #position19{position: absolute;margin-top: 1090px;margin-left: 310px;}  
         #position20{position: absolute;margin-top: 1090px;margin-left: 550px;}  
        /* #position21{position: absolute;margin-top: 910px;margin-left: 50px;}    */
        #position22{ transform: rotate(90deg) translate(120%, 0);transform-origin: top right; }
    </style>
    <body>
		 
        <div id="position1"><?=$branch;?></div>
        <div id="position2"><?=$supplier_name;?></div>
        <div id="position3"><? echo $supplier_add; ?></div>
        <div id="position4"><?=$company_name;?></div>
        <div id="position5">
            <? echo $company_add[$data_array[0][csf("importer_id")]]['plot_no'] . "," . $company_add[$data_array[0][csf("importer_id")]]['level_no'] . ", " . $company_add[$data_array[0][csf("importer_id")]]['road_no'] . ",<br/> " . $company_add[$data_array[0][csf("importer_id")]]['city'] . ", " . $country_array[$company_add[$data_array[0][csf("importer_id")]]['country_id']]; ?>
        </div>
        <div id="position6"><? echo $currency_sign.' '.number_format($data_array[0][csf('lc_value')], 2); ?></div>
        <!-- <div id="position7"><? echo $mcurrency.' '.number_to_words(number_format($data_array[0][csf('lc_value')], 2),'', $dcurrency); ?></div> -->
		<div  id="position7"> ✔ </div>
		<div  id="position23"> ✔ CPT </div>
		<div  id="position24"> ✔ </div>
		<div  id="position25"> <? echo $supplier_add; ?> </div>
		<div  id="position26">  <? echo $company_add[$data_array[0][csf("importer_id")]]['plot_no'] . "," . $company_add[$data_array[0][csf("importer_id")]]['level_no'] . ", " . $company_add[$data_array[0][csf("importer_id")]]['road_no'] . ",<br/> " . $company_add[$data_array[0][csf("importer_id")]]['city'] . ", " . $country_array[$company_add[$data_array[0][csf("importer_id")]]['country_id']]; ?> </div>
		<div  id="position9">
		 □ 14 days free destination
		<br>
		 □ GP-2 ,BTBA cash incentive enjoy Knit design Ltd.
		</div>
		
        <div id="position8"><? echo "1: " . $pi_category;?> For 100% Export Oriented <? echo  chop($business_nature_info,',');?> Ready made Garments Industry</div>
        <!-- <div id="position9"><? echo 'As per PI no: '.$pi_numbers.' '.$pi_date;?></div> -->
        <div id="position10"><? echo $origin; ?></div>
        <div id="position11"><? echo $data_array[0][csf("lcaf_no")] ; ?></div>
        <div id="position18"><? echo $last_shipment_date; ?></div>
        <div id="position19"><? echo $lc_expiry_date; ?></div>
      <div id="position20"><? echo  $data_array[0][csf("LC_EXPIRY_DAYS")]; ?></div> 
       
        <div id="position22"> </div>
    </body>
 <?
    exit();
}

if ($action==='btb_import_lc_letter37')
{
	
	$sql = "SELECT	importer_id, issuing_bank_id, lc_value, lc_date, currency_id, tenor, supplier_id, item_category_id, pi_id, port_of_loading, port_of_discharge, delivery_mode_id, last_shipment_date, lc_expiry_date, inco_term_place, doc_presentation_days, origin,inco_term_id, insurance_company_name, cover_note_no, cover_note_date, MATURITY_FROM_ID, payterm_id, tolerance, lca_no,lcaf_no, lc_number,btb_system_id, pi_id, partial_shipment, transhipment, garments_qty, uom_id, advising_bank, advising_bank_address, remarks, lc_type_id,lc_expiry_days,confirming_bank
	from com_btb_lc_master_details
	where id='$data' ";
    //echo $sql;
    $data_array = sql_select($sql);
    $all_pi_ids = $data_array[0][csf("pi_id")];
	
	//echo $data_array[0][csf("lc_type_id")];

    $is_lc_sc_sql = sql_select("select lc_sc_id, is_lc_sc from com_btb_export_lc_attachment where import_mst_id=$data and is_deleted=0 and status_active=1");
    // if (empty($is_lc_sc_sql) && $data_array[0][csf("lc_type_id")] !=2) {
    //     echo "May be attachment not complete yet";
    //     die;
    // }
	$export_lc_sc_no_arr;
	$export_lc_sc_date_arr;
	foreach($is_lc_sc_sql as $row)
	{
		if($row[csf('is_lc_sc')]==0){
			$sql_lc=sql_select("SELECT id,export_lc_no, lc_date FROM com_export_lc  where id=".$row[csf('lc_sc_id')]);
			foreach($sql_lc as $lc_row)
			{
				$export_lc_sc_no_arr[$lc_row[csf("id")]].=$lc_row[csf("export_lc_no")];
				$export_lc_sc_date_arr[$lc_row[csf("id")]].=$lc_row[csf("lc_date")];
			}
		}
		else{
			
			$sql_sc=sql_select("SELECT id,contract_no,CONTRACT_DATE as contrct_date FROM com_sales_contract where id=".$row[csf('lc_sc_id')]);
			foreach($sql_sc as $sc_row)
			{
				$export_lc_sc_no_arr[$sc_row[csf("id")]].=$sc_row[csf("contract_no")];
				$export_lc_sc_date_arr[$sc_row[csf("id")]].=$sc_row[csf("contrct_date")];
			}
		}
		
		$lc_sc_id_arr[]=$row[csf('lc_sc_id')];
	
	}


    $order_pcs_qty = return_field_value("sum(b.order_quantity) as order_quantity", "com_export_lc_order_info a,wo_po_color_size_breakdown b", "b.po_break_down_id=a.wo_po_break_down_id and a.com_export_lc_id in(" . implode(',', $lc_sc_id_arr) . ")", "order_quantity");


    //--------------lib
    $currency_sign_arr = array(1 => '৳', 2 => '$', 3 => '€', 4 => '€', 5 => '$', 6 => '£', 7 => '¥');
    $company_name = return_field_value("company_name", "lib_company", "id=" . $data_array[0][csf("importer_id")], "company_name");
    $bang_bank_reg_no = return_field_value("bang_bank_reg_no", "lib_company", "id=" . $data_array[0][csf("importer_id")], "bang_bank_reg_no");

    $country_array = return_library_array("select id,country_name from lib_country where is_deleted=0", "id", "country_name");
    $address = sql_select("SELECT id,plot_no,level_no,road_no,block_no,country_id,city,zip_code,irc_no,tin_number,vat_number,bang_bank_reg_no,bin_no,business_nature from lib_company where id = " . $data_array[0][csf('importer_id')] . "");
    foreach ($address as $row) {
        $company_add[$row[csf('id')]]['plot_no'] = $row[csf('plot_no')];
        $company_add[$row[csf('id')]]['level_no'] = $row[csf('level_no')];
        $company_add[$row[csf('id')]]['road_no'] = $row[csf('road_no')];
        $company_add[$row[csf('id')]]['block_no'] = $row[csf('block_no')];
        $company_add[$row[csf('id')]]['country_id'] = $row[csf('country_id')];
        $company_add[$row[csf('id')]]['city'] = $row[csf('city')];
        $company_add[$row[csf('id')]]['zip_code'] = $row[csf('zip_code')];
        $company_add[$row[csf('id')]]['irc_no'] = $row[csf('irc_no')];
        $company_add[$row[csf('id')]]['tin_number'] = $row[csf('tin_number')];
        $company_add[$row[csf('id')]]['vat_number'] = $row[csf('vat_number')];
        $company_add[$row[csf('id')]]['bang_bank_reg_no'] = $row[csf('bang_bank_reg_no')];
        $company_add[$row[csf('id')]]['bin_no'] = $row[csf('bin_no')];
    }
    $business_nature=explode(',',$address[0][csf('business_nature')]);
	foreach($business_nature as $row)
	{
		$business_nature_info=$business_nature_arr[$row].',';
	}
    //print_r($company_add);

    $branch = return_field_value("branch_name", "lib_bank", "id=" . $data_array[0][csf("issuing_bank_id")], "branch_name");
    $bank_name = return_field_value("bank_name", "lib_bank", "id=" . $data_array[0][csf("issuing_bank_id")], "bank_name");
    $currency_sign = $currency_sign_arr[$data_array[0][csf("currency_id")]];
    $supplier_name = return_field_value("supplier_name", "lib_supplier", "id=" . $data_array[0][csf("supplier_id")], "supplier_name");
    $supplier_add = return_field_value("address_1", "lib_supplier", "id=" . $data_array[0][csf("supplier_id")], "address_1");

	$pi_number_arr=return_library_array( "SELECT id, pi_number from com_pi_master_details where id in(".$data_array[0][csf("pi_id")].")  AND status_active = 1 AND is_deleted = 0",'id','pi_number');
	$hs_code_arr=return_library_array( "SELECT id, HS_CODE from com_pi_master_details where id in(".$data_array[0][csf("pi_id")].")  AND status_active = 1 AND is_deleted = 0",'id','HS_CODE');
	$pi_date_arr=return_library_array( "SELECT id, pi_date from com_pi_master_details where id in(".$data_array[0][csf("pi_id")].")  AND status_active = 1 AND is_deleted = 0",'id','pi_date');
	// $pi_date = return_field_value("pi_date","com_pi_master_details"," status_active = 1 AND is_deleted = 0 AND id=".$data_array[0][csf("pi_id")],"pi_date");
	$pi_cate_arr=return_library_array( "SELECT id, item_category_id from com_pi_master_details where id in(".$data_array[0][csf("pi_id")].")  AND status_active = 1 AND is_deleted = 0",'id','item_category_id');

    $pi_numbers;
	if(count($pi_number_arr)>1){
		foreach($pi_number_arr as $key=>$row){
            $pi_numbers .= $row;
			$pi_date .='&nbsp;DT: '.date('d.m.Y',strtotime($pi_date_arr[$key])).", ";
		}
        $pi_numbers=rtrim($pi_numbers,", ");
        /* $pi_numbers .= " Pls Check Attached LC Forwarding Letter.";
        $pi_date=''; */
	}
	else
	{
		$pi_numbers = $pi_number_arr[$data_array[0][csf("pi_id")]];
		$pi_date .='&nbsp;DT: '.date('d.m.Y',strtotime($pi_date_arr[$data_array[0][csf("pi_id")]]));
	}

    if(count($hs_code_arr)>1){
        foreach($hs_code_arr as $row){
            $hs_code .= $row.",";
        }
	}
	else
	{
		$hs_code = $hs_code_arr[$data_array[0][csf("pi_id")]];
	}

    if(count($pi_cate_arr)>1){
		foreach($pi_cate_arr as $row){
			$pi_category .= $item_category[$row].",";
		}
		$pi_category=implode(", ",array_unique(explode(",",chop($pi_category,','))));		
	}
	else
	{
		$pi_category= $item_category[$pi_cate_arr[$data_array[0][csf("pi_id")]]];
	}

    if ($data_array[0][csf("last_shipment_date")] != '') {
        $last_shipment_date = date('d.m.Y', strtotime($data_array[0][csf("last_shipment_date")]));
    } else {
        $last_shipment_date = '';
    }


    if ($data_array[0][csf("lc_expiry_date")] != '') {
        $lc_expiry_date = date('d.m.Y', strtotime($data_array[0][csf("lc_expiry_date")]));
    } else {
        $lc_expiry_date = '';
    }
    $origin = return_field_value("country_name", " lib_country", "id=" . $data_array[0][csf("origin")], "country_name");
    $inco_term_id = $incoterm[$data_array[0][csf("inco_term_id")]];


    if ($data_array[0][csf("cover_note_date")] != '') {
        $cover_note_date = date('d.m.Y', strtotime($data_array[0][csf("cover_note_date")]));
    } else {
        $cover_note_date = '';
    }

    if ($data_array[0][csf("payterm_id")] == 1) {
        $pay_term_cond = $pay_term[$data_array[0][csf("payterm_id")]];
    } else {
        $pay_term_cond = $data_array[0][csf("tenor")] . "Day's";
    }
    if ($data_array[0][csf("doc_presentation_days")] == "") {
        $doc_presentation_days = "";
    } else {
        $doc_presentation_days = $data_array[0][csf("doc_presentation_days")] . "Days";
    }
	if ($data_array[0][csf("confirming_bank")] == "") {
        $confirming_bank = "";
    } else {
        $confirming_bank = $data_array[0][csf("confirming_bank")] ;
    }
	if ($data_array[0][csf("lc_date")] == "") {
        $lc_date = "";
    } else {
        $lc_date = $data_array[0][csf("lc_date")] ;
    }

    $nature = return_field_value("business_nature", "lib_company", "id=" . $data_array[0][csf("importer_id")], "business_nature");
    $business_nature_arr = explode(",", $nature);
    $business_nature;
    if (count($business_nature_arr) > 0) {
        foreach ($business_nature_arr as $row) {
            if ($row == 2) {
                $business_nature .= "Knit ";
            } elseif ($row == 3) {
                $business_nature .= "Woven ";
            } elseif ($row == 4) {
                $business_nature .= "Trims ";
            } elseif ($row == 5) {
                $business_nature .= "Print ";
            } elseif ($row == 6) {
                $business_nature .= "Embroidery ";
            } elseif ($row == 7) {
                $business_nature .= "Wash ";
            } elseif ($row == 8) {
                $business_nature .= "Yarn Dyeing ";
            } elseif ($row == 9) {
                $business_nature .= "AOP ";
            } elseif ($row == 100) {
                $business_nature .= "Sweater ";
            }
        }
    }
    $currency_id = $data_array[0][csf("currency_id")];
    //$mcurrency, $dcurrency;
    $dcurrency = "";
    if ($currency_id == 1) {
        $mcurrency = 'Taka';
        $dcurrency = 'Paisa';
    }
    if ($currency_id == 2) {
        $mcurrency = 'USD';
        $dcurrency = 'CENTS';
    }
    if ($currency_id == 3) {
        $mcurrency = 'EURO';
        $dcurrency = 'CENTS';
    }
     ?>

    <style>
         body{
            margin:0;padding:0; font-size: 90%;
            background: url("../application_form/form_image/image/one_cf7_onebank.jpg") ;
            background-size:21.59cm 30.56cm;
            background-repeat: no-repeat;
        } 
        *{font-weight: bold;}
		#position1{position: absolute;margin-top: 60px;margin-left: 90px;}
        #position2{position: absolute;margin-top: 150px;margin-left: 200px;}
        #position3{position: absolute;margin-top: 170px;margin-left: 200px; width:350px;}
        #position4{position: absolute;margin-top: 250px;margin-left: 200px;}
        #position5{position: absolute;margin-top: 270px;margin-left: 200px; }
        #position6{position: absolute;margin-top: 350px;margin-left: 100px;}
		#position7{position: absolute;margin-top: 330px;margin-left: 250px;}
        #position8{position: absolute;margin-top: 400px;margin-left: 250px; width:460px;}
        #position9{position: absolute;margin-top: 480px;margin-left: 360px;}
		#position21{position: absolute;margin-top: 480px;margin-left: 680px;}
        #position10{position: absolute;margin-top: 530px;margin-left: 180px;}
        #position11{position: absolute;margin-top: 530px;margin-left: 600px;} 
        #position12{position: absolute;margin-top: 560px;margin-left: 200px;}   
        #position13{position: absolute;margin-top: 560px;margin-left: 360px;}  
        #position14{position: absolute;margin-top: 560px;margin-left: 650px;}  
        #position15{position: absolute;margin-top: 610px;margin-left: 230px;}  
        #position16{position: absolute;margin-top: 610px;margin-left: 600px;}  
        #position17{position: absolute;margin-top: 710px;margin-left: 750px;}    
        #position18{position: absolute;margin-top: 1115px;margin-left: 275px; } 
		#position19{position: absolute;margin-top: 1090px;margin-left: 275px;}  
         #position20{position: absolute;margin-top: 710px;margin-left: 250px;}  
      
    </style>
    <body>
		 
        <div id="position1"><?=$branch;?></div>
        <div id="position2"><?=$company_name;?></div>
		<div id="position3">
            <? echo $company_add[$data_array[0][csf("importer_id")]]['plot_no'] . "," . $company_add[$data_array[0][csf("importer_id")]]['level_no'] . ", " . $company_add[$data_array[0][csf("importer_id")]]['road_no'] . ",<br/> " . $company_add[$data_array[0][csf("importer_id")]]['city'] . ", " . $country_array[$company_add[$data_array[0][csf("importer_id")]]['country_id']]; ?>
        </div>
		<div id="position4"><?=$supplier_name;?></div>
        <div id="position5"><? echo $supplier_add; ?></div>
        
        
        <div id="position6"><? echo $currency_sign.' '.number_format($data_array[0][csf('lc_value')], 2); ?></div>
        <div id="position7"><? echo $mcurrency.' '.number_to_words(number_format($data_array[0][csf('lc_value')], 2),'', $dcurrency); ?></div>
		
        <div id="position8"><? echo  $pi_category;?> </div>
        <div id="position9"><? echo  $pi_numbers;?></div>
		 <div id="position21"><?echo  $pi_date;?></div> 
        <div id="position10"><? echo "N/A"?></div>
        <div id="position11"><? echo "527769880403" ?></div>
        <div id="position12"><? echo "BANGLADESH" ?></div>
		<div id="position13"><? echo "260326120456020" ?></div>
		<div id="position14"><? echo "002207576-0403" ?></div>
		<div id="position15"><? echo "BENEFICIARY'S FACTORY" ?></div>
		<div id="position16"><? echo "OPENER'S FACTORY." ?></div>
        <div id="position17"><? echo $lc_expiry_date; ?></div>
		<div id="position20"><? echo change_date_format($lc_date) ?></div>
		
       
        <div id="position18">
			BANK:&nbsp;<? echo $confirming_bank;?> 
		</div>
		<div id="position19"> 
		Export LC No: <?echo  implode(', ',$export_lc_sc_no_arr) ?>	DT:<? echo change_date_format(implode(', ',$export_lc_sc_date_arr));?>

		</div>
     </body>
   <?
    exit();
}
if ($action==='btb_import_lc_letter38')
{
	$sql = "SELECT	importer_id, issuing_bank_id, lc_value, lc_date, currency_id, tenor, supplier_id, item_category_id, pi_id, port_of_loading, port_of_discharge, delivery_mode_id, last_shipment_date, lc_expiry_date, inco_term_place, doc_presentation_days, origin,inco_term_id, insurance_company_name, cover_note_no, cover_note_date, MATURITY_FROM_ID, payterm_id, tolerance, lca_no,lcaf_no, lc_number,btb_system_id, pi_id, partial_shipment, transhipment, garments_qty, uom_id, advising_bank, advising_bank_address, remarks, lc_type_id,lc_expiry_days,confirming_bank,application_date
	from com_btb_lc_master_details
	where id='$data' ";
	$pay_term_txt = ($sql_btb_res[0]['PAYTERM_ID'] == 1)?$pay_term[$sql_btb_res[0]['PAYTERM_ID']]." / EDF":$pay_term[$sql_btb_res[0]['PAYTERM_ID']];
	$pay_term_txt = ($sql_btb_res[0]['UPAS_RATE'])?$pay_term_txt." UPAS Rate ".$sql_btb_res[0]['UPAS_RATE']."% ":$pay_term_txt;
    //echo $sql;
	$data_array = sql_select($sql);
	foreach($data_array as $row)
	{  $application_date=$row[csf('application_date')];
		$issuing_bank_id=$row[csf('issuing_bank_id')];
		$pay_term_txt=$row[csf('payterm_id')];
	
		$lc_value=$row[csf('lc_value')];
		$pi_ids=$row[csf('pi_id')];
		$last_shipment_date=$row[csf('last_shipment_date')];
		$lc_expiry_date=$row[csf('lc_expiry_date')];

	}
	
	$sql_bank_info = sql_select("SELECT ID, BANK_NAME, BRANCH_NAME, ADDRESS from lib_bank where id=$issuing_bank_id");
	$bank_name=$sql_bank_info[0]["BANK_NAME"];
	$bank_branch=$sql_bank_info[0]["BRANCH_NAME"];
	$bank_address=$sql_bank_info[0]["ADDRESS"];
	
	$sql_pi_res=sql_select("SELECT a.PI_NUMBER, a.PI_DATE, a.ITEM_CATEGORY_ID,TOTAL_AMOUNT from com_pi_master_details a where a.status_active=1 and a.id in($pi_ids) ");
	foreach($sql_pi_res as $row)
	{
		$category_name=$item_category[$row['ITEM_CATEGORY_ID']];
		
	}
	$pi_number=rtrim($pi_number,', ');
	$pi_date=rtrim($pi_date,', ');
	$sql_lcSc=sql_select("SELECT a.LC_SC_ID,a.IMPORT_MST_ID, b.contract_no as LC_SC_NO, b.contract_date as LC_SC_DATE
	from  com_btb_export_lc_attachment a, com_sales_contract b
	where a.lc_sc_id=b.id and a.status_active=1 and b.status_active=1  and a.is_lc_sc=1 and a.import_mst_id=$data 
	union all 
	select a.LC_SC_ID,a.IMPORT_MST_ID, b.export_lc_no as LC_SC_NO, b.lc_date as LC_SC_DATE
	from com_btb_export_lc_attachment a, com_export_lc b
	where a.lc_sc_id=b.id and a.is_lc_sc=0 and a.status_active=1 and b.status_active=1 and a.import_mst_id=$data ");
	foreach($sql_lcSc as $row)
	{
		$lc_sc_info.=$row['LC_SC_NO'].' Dt- '.change_date_format($row['LC_SC_DATE'])."<br>";
	}
	$lc_sc_info = rtrim($lc_sc_info,'<br>');
	$supplier_name = return_field_value("supplier_name", "lib_supplier", "id=" . $data_array[0][csf("supplier_id")], "supplier_name");
    $supplier_add = return_field_value("address_1", "lib_supplier", "id=" . $data_array[0][csf("supplier_id")], "address_1");

	?>

	<table>
   <tr>
	<td>Date : &nbsp;<?echo $application_date?></td>
   </tr>
   <tr style="height: 20px;"></tr>
   <tr><td>To,</td></tr>
   <tr><td>The Manager</td></tr>
   <tr><td><? echo $bank_name?></td></tr>
   <tr><td><? echo $bank_branch?></td></tr>
   <tr><td><? echo $bank_address?></td></tr>
   <tr style="height: 15px;"></tr>
   <tr><td>Subject:  Request for opening Back To Back L/C under  <? if ($pay_term_txt==1){echo "<b>EDF Facilities/".$pay_term[$pay_term_txt]."</b>";} else{ echo "<b>".$pay_term[$pay_term_txt]."</b>"."(<b>120</b> days from BL Date)";}?>&nbsp;of&nbsp;<b><?echo $category_name?></b> for <b>USD-<?echo $lc_value?></b> </td></tr>
   <tr>
	<td>against Export LC / Contract No: <b><?echo $lc_sc_info?></b></td>
   </tr>
   <tr style="height: 10px;"></tr>
   <tr> <td>Dear Sir,</td>
   </tr>
   <tr>
	<td>You are requested to please arrange to open a Back To Back L/C under <? if ($pay_term_txt==1){echo "<b>EDF Facilities/".$pay_term[$pay_term_txt]."</b>";} else{ echo "<b>".$pay_term[$pay_term_txt]."</b>"."(<b>120</b> days from BL Date)";}?>&nbsp;of&nbsp;<b><?echo $category_name?> <br></b> for <b>USD-<?echo $lc_value?></b>.The details of which are as follows: -</td>
   </tr>
   
	</table>
		<br><br>
	<table>
		<tr>
			<td width="40">1</td>
			<td width="200">Name of the Beneficiary </td>
			<td>:</td>
			<td><?echo $supplier_name?></td>
	   </tr>
	    <tr>
			<td width="40">&nbsp;</td>
			<td width="200">with address</td>
			<td>:</td>
			<td><?echo $supplier_add; ?></td>
	   </tr>
	  <tr style="height: 20px;"></tr>
	  <tr>
		 <td width="40">2</td>
			<td width="200">Amount</td>
			<td>:</td>
			<td><b>USD-<?echo $lc_value?></b></td>
	  </tr>
	  <tr>
	    <td width="40">3</td>
		<td width="200">Description of Goods</td>
		<td>:</td>
		<td><b><?echo $category_name?></b></td>
	  </tr>
	  <tr>
		<td width="40">4</td>
		<td width="200">Shipment validity</td>
		<td>:</td>
		<td><?echo change_date_format($last_shipment_date)?></td>
	  </tr>
	  <tr>
		<td width="40">5</td>
		<td width="200">Expiry</td>
		<td>:</td>
		<td><?echo change_date_format($lc_expiry_date)?></td>
	 </tr>
	  <tr>
		<td width="40">6</td>
		<td width="200">Documents Required</td>
		<td>:</td>
		<td>i) Invoice, ii) Delivery Challan, iii)Packing list</td>
	  </tr>
	  <tr style="height: 15px;"></tr>
	
	  <tr>
		<td colspan="5">Necessary Proforma Invoice and L/C application have been furnished herewith duly filled in and signed by <br> us for doing the needful at your end.</td>
	  </tr>
	  <tr style="height: 25px;"></tr>
	  <tr>
	  <td  colspan="5">Thanking you.</td>
	  </tr>
	   <tr>
		<td  colspan="5">Yours faithfully,</td>
	   </tr>

	 </table>
	 <table border="1" style="border-collapse: collapse;margin-left:400px " width="300" >
		<?
		foreach($sql_pi_res as $row)
		{	?><tr>

             <td style="border: none;"><?php echo $row['PI_NUMBER']." DATE ".change_date_format($row['PI_DATE']); ?></td>

				<td><?echo $row['TOTAL_AMOUNT']?></td>
			</tr>
			<?
			$total_ammount+=$row['TOTAL_AMOUNT'];
		}
		?>
		<tr>
			<td align="right"><b>Total=</b></td>
			<td><?echo $total_ammount?></td>
		</tr>
	 </table>

	<?
    exit();
}

if ($action==='lc_opening_letter3')
{	
	$company_arr = return_library_array('SELECT id, company_name FROM lib_company','id','company_name');

	$sql_btb="SELECT ID, BTB_SYSTEM_ID, LC_VALUE,ISSUING_BANK_ID from com_btb_lc_master_details where id=$data and status_active=1";
	// echo $sql_btb;die;
	$sql_btb_res=sql_select($sql_btb);
	$btb_id = $sql_btb_res[0]['ID'];
	$lc_value = $sql_btb_res[0]['LC_VALUE'];
	$bank_id = $sql_btb_res[0]['ISSUING_BANK_ID'];

	$sql_bank_info = sql_select("SELECT ID, BANK_NAME, BRANCH_NAME, ADDRESS, DESIGNATION from lib_bank where id=$bank_id");
	$bank_name=$sql_bank_info[0]["BANK_NAME"];
	$bank_branch=$sql_bank_info[0]["BRANCH_NAME"];
	$bank_address=$sql_bank_info[0]["ADDRESS"];
	$bank_designation_id=$sql_bank_info[0]["DESIGNATION"];

	$bank_designation = return_field_value("custom_designation","lib_designation","id=$bank_designation_id","custom_designation");
  	//  echo $ref;die;
		
	$sql_lcSc=sql_select("SELECT a.IMPORT_MST_ID, b.contract_no as LC_SC_NO, b.contract_date as LC_SC_DATE, b.contract_value as LC_SC_VALUE, b.BUYER_NAME, a.IS_LC_SC, b.internal_file_no as INTERNAL_FILE_NO
	from  com_btb_export_lc_attachment a, com_sales_contract b
	where a.lc_sc_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.is_lc_sc=1 and a.import_mst_id=$data
	union all 
	select a.IMPORT_MST_ID, b.export_lc_no as LC_SC_NO, b.lc_date as LC_SC_DATE, b.lc_value as LC_SC_VALUE, b.BUYER_NAME, a.IS_LC_SC, b.internal_file_no as INTERNAL_FILE_NO 
	from com_btb_export_lc_attachment a, com_export_lc b
	where a.lc_sc_id=b.id and a.status_active=1 and a.is_deleted=0 and a.is_lc_sc=0 and b.status_active=1 and b.is_deleted=0 and a.import_mst_id=$data");	
	$lc_sc_arr=array();
	foreach($sql_lcSc as $row)
	{
		$lc_sc_arr[$row['IMPORT_MST_ID']]['LC_SC_NO'] =$row['LC_SC_NO'];
		$lc_sc_arr[$row['IMPORT_MST_ID']]['LC_SC_DATE'] =change_date_format($row['LC_SC_DATE']);
		$lc_sc_arr[$row['IMPORT_MST_ID']]['LC_SC_VALUE'] =$row['LC_SC_VALUE'];
		$lc_sc_arr[$row['IMPORT_MST_ID']]['FILE_NO'] =$row['INTERNAL_FILE_NO'];

		if($lc_sc_no!=''){$lc_sc_no .= ", ".$lc_sc_arr[$btb_id]['LC_SC_NO'];}else{$lc_sc_no =$lc_sc_arr[$btb_id]['LC_SC_NO'];};
		if($lc_sc_date!=''){$lc_sc_date .= ", ".$lc_sc_arr[$btb_id]['LC_SC_DATE'];}else{$lc_sc_date = $lc_sc_arr[$btb_id]['LC_SC_DATE'];}
		if($lc_sc_value!=''){$lc_sc_value .= ", ".$lc_sc_arr[$btb_id]['LC_SC_VALUE'];}else{$lc_sc_value =$lc_sc_arr[$btb_id]['LC_SC_VALUE'];}
		if($lc_sc_file!=''){$lc_sc_file .= ", ".$lc_sc_arr[$btb_id]['LC_SC_VALUE'];}else{$lc_sc_file =$lc_sc_arr[$btb_id]['FILE_NO'];}
	}

	?>
    <table width="750" cellpadding="0" align="left" cellspacing="0" border="0">
		<tr><td colspan="4" height="100"></td></tr>
		<tr>
			<td width="25"> </td>
			<td width="675" colspan="3"><b>DATED: <? echo date('d.m.Y');?></b></td>

		</tr>
		<tr><td colspan="4" height="50"></td></tr>
		<tr>
			<td width="25" valign="top"></td>
			<td width="675" colspan="3">
				<strong>THE <? echo strtoupper($bank_designation); ?></strong></br>
				<? echo $bank_name;?></br>
				<? echo $bank_branch;?></br>
				<? echo $bank_address;?></br>
			</td>
		</tr>
        <tr><td colspan="4" height="50">&nbsp;</td></tr>
		<tr>
			<td width="25"></td>
			<td width="675" colspan="3">
			RE:  REQUEST FOR OPENING OF BB L/C FOR <b><? echo $currency_name .' '.number_format($lc_value,2); ?></b> AGAINST LIEN ON EXPORT CONTRACT NO. <b><? echo $lc_sc_no; ?></b> DATE: <b><? echo $lc_sc_date." ".$currency_name .' '.$lc_sc_value; ?></b> B. CONTROL (<b><? echo implode(", ",array_unique(explode(", ",$lc_sc_file))); ?></b>). 
			</td>

		</tr>
		<tr>
			<td width="25"></td>
			<td width="675" colspan="3"><hr></td>
		</tr>
        <tr><td colspan="4" height="50">&nbsp;</td></tr>
		<tr>
			<td width="25"></td>
			<td width="675" colspan="3">MUHTARAM<br>ASSALAMU ALAKUM</td>
		</tr>
		<tr><td colspan="4" height="25">&nbsp;</td></tr>
		<tr>
			<td width="25"></td>
			<td width="675" colspan="3">
			REFERENCE TO THE ABOVE, WE WOULD REQUEST YOUR HONOUR TO OPEN BACK TO BACK L/C FOR BB L/C <b><? echo $currency_name .' '.number_format($lc_value,2); ?></b> AGAINST LIEN ON EXPORT CONTRACT NO. <b><? echo $lc_sc_no; ?></b> DATE: <b><? echo $lc_sc_date." ".$currency_name .' '.$lc_sc_value; ?></b> B. CONTROL (<b><? echo implode(", ",array_unique(explode(", ",$lc_sc_file))); ?></b>). NECESSARY PARTICULARS IN SUPPORT OF OPENING OF THIS BACK TO BACK L/C ARE FURNISHED HEREUNDER:
		</tr>
        <tr><td colspan="4"  height="20">&nbsp;</td></tr>
        <tr>
			<td width="25"></td>
			<td width="675" colspan="3">
				<table width="650" cellpadding="0" align="left" cellspacing="0" border="0">
					<tr>
						<td width="50"></td>
						<td width="500"></td>
						<td align="center" width="50">YES</td>
						<td align="center">NO</td>
					</tr>
					<tr>
						<td align="center">1.</td>
						<td>ORIGINAL COPY OF EXPORT CONTRACT</td>
						<td align="center">&#9723;</td>
						<td align="center">&#9723;</td>
					</tr>
					<tr>
						<td align="center">2.</td>
						<td>2(TWO) SETS OF PROFORMA INVOICE/INDENT</td>
						<td align="center">&#9723;</td>
						<td align="center">&#9723;</td>
					</tr>
					<tr>
						<td align="center">3.</td>
						<td>AGREEMENT AND APPLICATION FORM OF IMPORT L/C</td>
						<td align="center">&#9723;</td>
						<td align="center">&#9723;</td>
					</tr>
					<tr>
						<td align="center">4.</td>
						<td>LETTER OF CREDIT AUTHORISATION &IMPORT PERMISSION</td>
						<td align="center">&#9723;</td>
						<td align="center">&#9723;</td>
					</tr>
					<tr>
						<td align="center">5.</td>
						<td>INSURANCE COPY</td>
						<td align="center">&#9723;</td>
						<td align="center">&#9723;</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr><td colspan="4"  height="25">&nbsp;</td></tr>
        <tr>
			<td width="25"></td>
			<td width="675" colspan="3">IT MAY ALSO BE MENTIONED HERE THAT THE NECESSARY L/C OPENING COMMISSION ON OTHER CHARGES MAY BE DEBITED FROM OUR CURRENT ACCOUNT NUMBER CA-</td>
		</tr>
        <tr><td colspan="4"  height="50">&nbsp;</td></tr>
        <tr>
			<td width="25"></td>
			<td width="675" colspan="3">
				MA-ASSALAM <br>
				YOURS TRULY <br>
				FOR COMFIT COMPOSITE KNIT LTD	
			</td>
		</tr>
		<tr><td colspan="4"  height="100">&nbsp;</td></tr>
        <tr>
			<td width="25"></td>
			<td width="675" colspan="3"><u>Authorized Signature.</u>
			</td>
		</tr>
		<tr><td colspan="4"  height="50">&nbsp;</td></tr>
	</table>
	<?
	exit();
}

if ($action==='btb_import_lc_pwa')
{	
	$company_arr = return_library_array('SELECT id, company_name FROM lib_company','id','company_name');
	$contract_person_arr = return_library_array('SELECT id, contract_person FROM lib_company','id','contract_person');
	$supplier_arr = return_library_array('SELECT id, supplier_name FROM lib_supplier','id','supplier_name');

	$sql_btb="SELECT IMPORTER_ID,SUPPLIER_ID,APPLICATION_DATE,LC_TYPE_ID,CURRENCY_ID, LC_VALUE,ISSUING_BANK_ID from com_btb_lc_master_details where id=$data and status_active=1";
	// echo $sql_btb;die;
	$sql_btb_res=sql_select($sql_btb);
	$importer_id = $sql_btb_res[0]['IMPORTER_ID'];
	$supplier_id = $sql_btb_res[0]['SUPPLIER_ID'];
	$application_date = $sql_btb_res[0]['APPLICATION_DATE'];
	$lc_type_id = $sql_btb_res[0]['LC_TYPE_ID'];
	$currency_id = $sql_btb_res[0]['CURRENCY_ID'];
	$lc_value = $sql_btb_res[0]['LC_VALUE'];
	$bank_id = $sql_btb_res[0]['ISSUING_BANK_ID'];

	$sql_bank_info = sql_select("SELECT ID, BANK_NAME, BRANCH_NAME, ADDRESS from lib_bank where id=$bank_id");
	$bank_name=$sql_bank_info[0]["BANK_NAME"];
	$bank_branch=$sql_bank_info[0]["BRANCH_NAME"];
	$bank_address=$sql_bank_info[0]["ADDRESS"];
	
	?>
    <table width="750" cellpadding="0" align="left" cellspacing="0" border="0">
		<tr><td colspan="2" height="150"></td></tr>
		<tr>
			<td width="25" valign="top"></td>
			<td width="675" align="center"><u>ক্ষমতা অর্পণ পত্র</u></td>
		</tr>
        <tr><td colspan="4" height="20">&nbsp;</td></tr>
		<tr>
			<td width="25"></td>
			<td width="675" colspan="2">
				আমি <?=$contract_person_arr[$importer_id];?>, ব্যবস্থাপনা পরিচালক, <?=$company_arr[$importer_id];?> এই মর্মে ঘোষণা করিতেছি যে, আমি নিম্নবর্ণিত ঋণপত্র খোলার জন্য <?=$bank_name;?>, <?=$bank_branch;?>, <?=$bank_address;?>- এ আবেদন করিতেছি।
			</td>
		</tr>
		<tr><td colspan="2"  height="20">&nbsp;</td></tr>
		<tr>
			<td width="25"></td>
			<td width="675" colspan="2">
				ঋণপত্রের বেনিফিসিয়ারীঃ- <?=$supplier_arr[$supplier_id];?>
			</td>
		</tr>
		<tr><td colspan="2"  height="20">&nbsp;</td></tr>
		<tr>
			<td width="25"></td>
			<td width="675" colspan="2">
				ঋণপত্রের ধরণঃ- <?=$lc_type[$lc_type_id];?> 
			</td>
		</tr>
		<tr><td colspan="2"  height="20">&nbsp;</td></tr>
		<tr>
			<td width="25"></td>
			<td width="675" colspan="2">
				ঋণপত্রের পরিমাণঃ- মাঃডঃ <?=$currency[$currency_id]." ".number_format($lc_value,2);?>
			</td>
		</tr>
		<tr><td colspan="2"  height="20">&nbsp;</td></tr>
		<tr>
			<td width="25"></td>
			<td width="675" colspan="2">
			আমরা এই মর্মে অংঙ্গিকার করছি যে,উল্লেখিত ঋণপএ/ঋণপএসমুহের বিপরীতে আমদানীকৃ্ত মালামাল এর মূল্য পরিশোধের নিমিত্তে ব্যাংক কর্তৃক জারিকৃ্ত সব নিয়ম কানুন/বিধি বিধান মেনে চলবো। আরো অংঙ্গিকার করছি যে রপ্তানি ব্যর্থতা জনিত কারনে অথবা অন্য যে কোন কারনে উল্লেখিত আমদানি মূল্য সুদ সহ পরিষদে বাধ্য থাকবো।তাছাড়া <?=$bank_name;?>, <?=$bank_branch;?>, <?=$bank_address;?> শাখা কে আমাদের ডিমান্ড লোণ শিষ্ট করে অথবা আমাদের যে কোন হিসাব বিকলেন করে পরিশোধ করতে বাদ্য থাকিব। এ বিষয় আমাদের অনিহা/অস্বীকৃতি বা ওজর আপত্তি থাকবেনা, ব্যাংক ধার্য্যকৃ্ত সময়ের মধ্যে সুদসহ পরিশোধ করতে বাধ্য থাকিব।
			</td>
		</tr>
		<tr><td colspan="2"  height="20">&nbsp;</td></tr>
		<tr>
			<td width="25"></td>
			<td width="675" colspan="2">
				আমি অদ্য <?=change_date_format($application_date);?> ইং তারিখে স্বেচ্ছায় ও স্বজ্ঞানে এই অংঙ্গিকার/ক্ষমতা অর্পণ পত্রে স্বাক্ষর করলাম।
			</td>
		</tr>
		<tr><td colspan="2"  height="40">&nbsp;</td></tr>
		<tr>
			<td width="25"></td>
			<td width="675" colspan="2">
				প্রতিষ্ঠানের নামঃ- <?=$company_arr[$importer_id];?>
			</td>
		</tr>
		<tr><td colspan="2"  height="20">&nbsp;</td></tr>
		<tr>
			<td width="25"></td>
			<td width="675" colspan="2">
				অনুমোদিত স্বাক্ষরঃ-  
			</td>
		</tr>
		<tr><td colspan="2"  height="50">&nbsp;</td></tr>
	</table>
	<?
	exit();
}


if ($action==='btb_import_lc_letter12')
{
	$supplierArr = return_library_array('SELECT id, supplier_name FROM lib_supplier','id','supplier_name');
	$companyArr = return_library_array('SELECT id, company_name FROM lib_company','id','company_name');
	$designationArr = return_library_array("SELECT id,custom_designation from lib_designation where status_active=1 ","id","custom_designation");
	$currency_sign_arr=array(1=>'৳',2=>'$',3=>'€',4=>'€',5=>'$',6=>'£',7=>'¥');

	$sql_bank_info = sql_select("SELECT ID, BANK_NAME, BRANCH_NAME, ADDRESS, DESIGNATION from lib_bank ");
	$bank_dtls_arr=array();
	foreach($sql_bank_info as $row)
	{
		$bank_dtls_arr[$row['ID']]['BANK_NAME']=$row['BANK_NAME'];
		$bank_dtls_arr[$row['ID']]['BRANCH_NAME']=$row['BRANCH_NAME'];
		$bank_dtls_arr[$row['ID']]['ADDRESS']=$row['ADDRESS'];
		$bank_dtls_arr[$row['ID']]['DESIGNATION']=$row['DESIGNATION'];
	}
	
	$sql_btb="SELECT ID, BTB_SYSTEM_ID, LC_DATE, APPLICATION_DATE, IMPORTER_ID, ISSUING_BANK_ID, CURRENCY_ID, SUPPLIER_ID, LC_VALUE, PI_ID,MARGIN,TENOR
	from com_btb_lc_master_details
	where id=$data and is_deleted=0 and status_active=1";
	$sql_btb_res=sql_select($sql_btb);
	$btb_id = $sql_btb_res[0]['ID'];
	if ($sql_btb_res[0]['TENOR']!="" && $sql_btb_res[0]['TENOR']>0){ $tenorDays =  $sql_btb_res[0]['TENOR']." DAYS "; }
	$dcurrency_arr=array(1=>'Paisa',2=>'CENTS',3=>'CENTS',);
	$mcurrency = $currency[$data_array[0][csf("currency_id")]];
	$dcurrency = $dcurrency_arr[$data_array[0][csf("currency_id")]];

	// PI Part
	$pi_ids=$sql_btb_res[0]['PI_ID'];
	$sql_pi_res=sql_select("SELECT ITEM_CATEGORY_ID from com_pi_master_details where id in($pi_ids) and status_active=1");

	foreach($sql_pi_res as $row)
	{
		$category_name=$item_category[$row['ITEM_CATEGORY_ID']];
	}

	// LC SC Part
	$sql_lcSc=sql_select("SELECT a.LC_SC_ID,a.IMPORT_MST_ID, b.contract_no as LC_SC_NO, b.contract_date as LC_SC_DATE, b.contract_value as LC_SC_VALUE, a.IS_LC_SC
	from  com_btb_export_lc_attachment a, com_sales_contract b
	where a.lc_sc_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and a.is_lc_sc=1 and a.import_mst_id=$data
	union all 
	select a.LC_SC_ID,a.IMPORT_MST_ID, b.export_lc_no as LC_SC_NO, b.lc_date as LC_SC_DATE, b.lc_value as LC_SC_VALUE, a.IS_LC_SC
	from com_btb_export_lc_attachment a, com_export_lc b
	where a.lc_sc_id=b.id and a.status_active=1 and a.is_deleted=0 and a.is_lc_sc=0 and b.status_active=1 and b.is_deleted=0 and a.import_mst_id=$data");	
	$lc_sc_arr=array();
	foreach($sql_lcSc as $row)
	{
		$lc_sc_arr[$row['IMPORT_MST_ID']]['LC_SC_NO'].=$row['LC_SC_NO'].', ';
		$lc_sc_arr[$row['IMPORT_MST_ID']]['LC_SC_DATE'].=change_date_format($row['LC_SC_DATE']).', ';
		$lc_sc_value+= $row['LC_SC_VALUE'];
	}
	// echo '<pre>';print_r($lc_sc_arr);

	$lc_sc_no   = rtrim($lc_sc_arr[$btb_id]['LC_SC_NO'],', ');
	$lc_sc_date = rtrim($lc_sc_arr[$btb_id]['LC_SC_DATE'],', ');

	?>
	<table width="700" cellpadding="0" align="left" cellspacing="0" border="0">
		<tr><td colspan="5" height="100"></td></tr>
		<tr>
			<td ></td>
			<td colspan="3"><b>DATE:&nbsp;&nbsp;<?=$sql_btb_res[0]['APPLICATION_DATE']; ?></b></td>
			<td ></td>
		</tr>
		<tr><td colspan="5" height="30"></td></tr>
		<tr>
			<td ></td>
			<td colspan="3">The <?=$designationArr[$bank_dtls_arr[$sql_btb_res[0]['ISSUING_BANK_ID']]['DESIGNATION']]; ?></td>
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td colspan="3"><?= $bank_dtls_arr[$sql_btb_res[0]['ISSUING_BANK_ID']]['BANK_NAME']; ?></td>
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td colspan="3"><?= $bank_dtls_arr[$sql_btb_res[0]['ISSUING_BANK_ID']]['BRANCH_NAME']; ?></td>
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td colspan="3"><?= $bank_dtls_arr[$sql_btb_res[0]['ISSUING_BANK_ID']]['ADDRESS']; ?></td>
			<td ></td>
		</tr>
		<tr><td colspan="5" height="30"></td></tr>
		<tr>
			<td ></td>
			<td colspan="3" style="text-align: justify">
			<u>Subject : Request for Opening L/C <?=$tenorDays;?> FROM THE DATE OF ACCEPT FOR <?=$category_name;?> 
			The Name of Company <b><?=$supplierArr[$sql_btb_res[0]['SUPPLIER_ID']]."&nbsp;".$currency_sign_arr[$sql_btb_res[0]['CURRENCY_ID']]."&nbsp;".number_format($sql_btb_res[0]['LC_VALUE'],2); ?> </b>On <?=$sql_btb_res[0]['MARGIN'];?>% Margin Basis. <b>EXPORT LC NO:</b> <?= $lc_sc_no;?> DT: <?=$lc_sc_date;?>.</u>
            </td>
			<td width="25"></td>
		</tr>
		<tr><td colspan="5" height="15"></td></tr>
		<tr>
			<td ></td>
			<td colspan="3">Dear Sir,</td>
			<td ></td>
		</tr>
		<tr><td colspan="5" height="10"></td></tr>
		<tr>
			<td ></td>
			<td colspan="3" style="text-align: justify">
				With reference to the above, we would like to inform you that we want to open A Letter of Credit for <b><?=$supplierArr[$sql_btb_res[0]['SUPPLIER_ID']]."&nbsp;".$currency_sign_arr[$sql_btb_res[0]['CURRENCY_ID']]."&nbsp;".number_format($sql_btb_res[0]['LC_VALUE'],2)."&nbsp;".$currency[$sql_btb_res[0]['CURRENCY_ID']]."&nbsp;".number_to_words(number_format($sql_btb_res[0]['LC_VALUE'],2), $mcurrency, $dcurrency);?></b>.
			</td>
			<td ></td>
		</tr>
		<tr><td colspan="5" height="15"></td></tr>				
		<tr>
			<td ></td>
			<td colspan="3" style="text-align: justify">
			Please take necessary arrangement to Open L/C for the above mentioned amount at your earliest by debiting our Account name <b>“<?=$companyArr[$sql_btb_res[0]['IMPORTER_ID']];?>”</b> For all related Charges.
			</td>
			<td ></td>
		</tr>
		<tr><td colspan="5" height="15"></td></tr>				
		<tr>
			<td ></td>
			<td colspan="3">Thanking You,</td>
			<td ></td>
		</tr>
		<tr><td colspan="5" height="100"></td></tr>
		<tr>
			<td ></td>
			<td colspan="3">………………………………</td>
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td colspan="3">AUTHORIZED SIGNATURE</td>
			<td ></td>
		</tr>
	</table>
	<?
	exit();
}

if($action==='btb_import_lc_letter13')
{
	$supplierArr = return_library_array('SELECT id, supplier_name FROM lib_supplier','id','supplier_name');
	$companyArr = return_library_array('SELECT id, company_name FROM lib_company','id','company_name');
	$ircArr = return_library_array('SELECT id, IRC_NO FROM lib_company','id','IRC_NO');
	$designationArr = return_library_array("SELECT id,custom_designation from lib_designation where status_active=1 ","id","custom_designation");
	$currency_sign_arr=array(1=>'৳',2=>'$',3=>'€',4=>'€',5=>'$',6=>'£',7=>'¥');

	$sql_bank_info = sql_select("SELECT ID, BANK_NAME, BRANCH_NAME, ADDRESS, DESIGNATION, SWIFT_CODE from lib_bank ");
	$bank_dtls_arr=array();
	foreach($sql_bank_info as $row)
	{
		$bank_dtls_arr[$row['ID']]['BANK_NAME']=$row['BANK_NAME'];
		$bank_dtls_arr[$row['ID']]['BRANCH_NAME']=$row['BRANCH_NAME'];
		$bank_dtls_arr[$row['ID']]['ADDRESS']=$row['ADDRESS'];
		$bank_dtls_arr[$row['ID']]['DESIGNATION']=$row['DESIGNATION'];
		$bank_dtls_arr[$row['ID']]['SWIFT_CODE']=$row['SWIFT_CODE'];
	}
	
	$sql_btb="SELECT ID, BTB_SYSTEM_ID, LC_DATE, APPLICATION_DATE, IMPORTER_ID, ISSUING_BANK_ID, CURRENCY_ID, SUPPLIER_ID, LC_VALUE, PI_ID,MARGIN,lcaf_no,ADVISING_BANK,ADVISING_BANK_ADDRESS
	from com_btb_lc_master_details
	where id=$data and is_deleted=0 and status_active=1";
	// echo $sql_btb;
	$sql_btb_res=sql_select($sql_btb);
	$btb_id = $sql_btb_res[0]['ID'];

	$dcurrency_arr=array(1=>'Paisa',2=>'CENTS',3=>'CENTS',);
	// $mcurrency = $currency[$data_array[0][csf("currency_id")]];
	// $dcurrency = $dcurrency_arr[$data_array[0][csf("currency_id")]];

	// PI Part
	$pi_ids=$sql_btb_res[0]['PI_ID'];
	$sql_pi_res=sql_select("SELECT ITEM_CATEGORY_ID,BENEFICIARY,HS_CODE,PI_NUMBER,PI_DATE from com_pi_master_details where id in($pi_ids) and status_active=1");
	$pi_beneficiary="";
	foreach($sql_pi_res as $row)
	{
		$pi_info.=$row['PI_NUMBER']." DT.".$row['PI_DATE'].", ";
		$category_name=$item_category[$row['ITEM_CATEGORY_ID']];
		if($row['BENEFICIARY']){$pi_beneficiary.=$row['BENEFICIARY'].", ";}
		if($row['HS_CODE']){$pi_hs_code.=$row['HS_CODE'].", ";}
	}

	// LC SC Part
	$sql_lcSc=sql_select("SELECT a.LC_SC_ID,a.IMPORT_MST_ID, b.contract_no as LC_SC_NO, b.contract_date as LC_SC_DATE, b.contract_value as LC_SC_VALUE, a.IS_LC_SC
	from  com_btb_export_lc_attachment a, com_sales_contract b
	where a.lc_sc_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and a.is_lc_sc=1 and a.import_mst_id=$data
	union all 
	select a.LC_SC_ID,a.IMPORT_MST_ID, b.export_lc_no as LC_SC_NO, b.lc_date as LC_SC_DATE, b.lc_value as LC_SC_VALUE, a.IS_LC_SC
	from com_btb_export_lc_attachment a, com_export_lc b
	where a.lc_sc_id=b.id and a.status_active=1 and a.is_deleted=0 and a.is_lc_sc=0 and b.status_active=1 and b.is_deleted=0 and a.import_mst_id=$data");	
	$lc_sc_arr=array();
	foreach($sql_lcSc as $row)
	{
		$lc_sc_arr[$row['IMPORT_MST_ID']]['LC_SC_NO'].=$row['LC_SC_NO'].', ';
		$lc_sc_arr[$row['IMPORT_MST_ID']]['LC_SC_DATE'].=change_date_format($row['LC_SC_DATE']).', ';
		$lc_sc_value+= $row['LC_SC_VALUE'];
	}
	// echo '<pre>';print_r($lc_sc_arr);

	$lc_sc_no   = rtrim($lc_sc_arr[$btb_id]['LC_SC_NO'],', ');
	$lc_sc_date = rtrim($lc_sc_arr[$btb_id]['LC_SC_DATE'],', ');

	?>
	<table width="700" cellpadding="0" align="left" cellspacing="0" border="0">
		<tr><td colspan="5" height="100"></td></tr>
		<tr>
			<td ></td>
			<td ><?=$companyArr[$sql_btb_res[0]['IMPORTER_ID']];?></td>
			<td colspan="2" align="right">DATE:&nbsp;&nbsp;<?=change_date_format($sql_btb_res[0]['APPLICATION_DATE']); ?></td>
			<td ></td>
		</tr>
		<tr><td colspan="5" height="30"></td></tr>
		<tr>
			<td ></td>
			<td colspan="3">T0</td>
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td colspan="3">The <?
				echo $designationArr[$bank_dtls_arr[$sql_btb_res[0]['ISSUING_BANK_ID']]['DESIGNATION']]."<br>".$bank_dtls_arr[$sql_btb_res[0]['ISSUING_BANK_ID']]['BANK_NAME']."<br>".$bank_dtls_arr[$sql_btb_res[0]['ISSUING_BANK_ID']]['BRANCH_NAME']."<br>".$bank_dtls_arr[$sql_btb_res[0]['ISSUING_BANK_ID']]['ADDRESS'];
				?></td>
			<td ></td>
		</tr>
		<tr><td colspan="5" height="30"></td></tr>
		<tr>
			<td ></td>
			<td colspan="3" style="text-align: justify">
			Sub: Request for Issuance TT for US <?=$currency[$sql_btb_res[0]['CURRENCY_ID']].''.$currency_sign_arr[$sql_btb_res[0]['CURRENCY_ID']]."&nbsp;".number_format($sql_btb_res[0]['LC_VALUE'],2);?> Infavour of PERFECT <?=$supplierArr[$sql_btb_res[0]['SUPPLIER_ID']];?>. Against CONTRACT NO. <?= $lc_sc_no;?> DT. <?=$lc_sc_date;?>
            </td>
			<td width="25"></td>
		</tr>
		<tr><td colspan="5" height="15"></td></tr>
		<tr>
			<td ></td>
			<td colspan="3">Dear Sir,</td>
			<td ></td>
		</tr>
		<tr><td colspan="5" height="10"></td></tr>
		<tr>
			<td ></td>
			<td colspan="3" style="text-align: justify">
				We would like to draw your kind attention that, we have to import some Goods from DEPZ. On urgent which supplier is not agreed to supply trough L/C. they are asking for FDD. Payment. basis 
			</td>
			<td ></td>
		</tr>
		<tr><td colspan="5" height="15"></td></tr>				
		<tr>
			<td ></td>
			<td colspan="3" height="30">PAYMENT DETAILS: </td>
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td colspan="3" height="30">BENEFICIARY: <?=rtrim($pi_beneficiary,", ")?></td>
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td colspan="3" height="30">BANK: <?=$sql_btb_res[0]["ADVISING_BANK"];?> </td>
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td colspan="3" height="30">ADD: <?=$sql_btb_res[0]["ADVISING_BANK_ADDRESS"];?> </td>
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td colspan="3" height="30">ACCOUNT NO. </td>
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td colspan="3" height="30">SWIFT CODE: <?=$bank_dtls_arr[$sql_btb_res[0]['ISSUING_BANK_ID']]['SWIFT_CODE'];?></td>
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td colspan="3" height="30">PROFORMA INVOICE NO <?=rtrim($pi_info,", ");?>, LCAF NO. <?=$data_array[0]["LCAF_NO"];?>, IRC NO. <?=$ircArr[$sql_btb_res[0]['IMPORTER_ID']];?>, H.S. CODE NO. <?=$pi_hs_code;?></td>
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td colspan="3" height="30">BILL CONSIGNED TO <?=$bank_dtls_arr[$sql_btb_res[0]['ISSUING_BANK_ID']]['BANK_NAME'];?> MENTION INTO TT </td>
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td colspan="3" height="30">INSURANCE COVER NOTE NO.</td>
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td colspan="3" height="30">This is for your kind information & takes necessary action in this regard</td>
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td colspan="3" height="30">Thanking you</td>
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td colspan="3" height="30">Yours faithfully,</td>
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td colspan="3" height="30">Date:<?=date('d-m-Y')?></td>
			<td ></td>
		</tr>

	</table>
	<?
	exit();
}

if($action=='btb_import_lc_letter14')
{
	$supplierArr = return_library_array('SELECT id, supplier_name FROM lib_supplier','id','supplier_name');
	$companyArr = return_library_array('SELECT id, company_name FROM lib_company','id','company_name');
	$designationArr = return_library_array("SELECT id,custom_designation from lib_designation where status_active=1 ","id","custom_designation");
	$currency_sign_arr=array(1=>'৳',2=>'$',3=>'€',4=>'€',5=>'$',6=>'£',7=>'¥');

	$sql_bank_info = sql_select("SELECT ID, BANK_NAME, BRANCH_NAME, ADDRESS, DESIGNATION from lib_bank ");
	$bank_dtls_arr=array();
	foreach($sql_bank_info as $row)
	{
		$bank_dtls_arr[$row['ID']]['BANK_NAME']=$row['BANK_NAME'];
		$bank_dtls_arr[$row['ID']]['BRANCH_NAME']=$row['BRANCH_NAME'];
		$bank_dtls_arr[$row['ID']]['ADDRESS']=$row['ADDRESS'];
		$bank_dtls_arr[$row['ID']]['DESIGNATION']=$row['DESIGNATION'];
	}
	
	$sql_btb="SELECT ID, BTB_SYSTEM_ID, LC_DATE, APPLICATION_DATE, IMPORTER_ID, ISSUING_BANK_ID, CURRENCY_ID, SUPPLIER_ID, LC_VALUE, PI_ID,MARGIN,lcaf_no,ADVISING_BANK,ADVISING_BANK_ADDRESS
	from com_btb_lc_master_details
	where id=$data and is_deleted=0 and status_active=1";
	// echo $sql_btb;
	$sql_btb_res=sql_select($sql_btb);
	$btb_id = $sql_btb_res[0]['ID'];

	$dcurrency_arr=array(1=>'Paisa',2=>'CENTS',3=>'CENTS',);
	// $mcurrency = $currency[$data_array[0][csf("currency_id")]];
	// $dcurrency = $dcurrency_arr[$data_array[0][csf("currency_id")]];

	// PI Part
	$pi_ids=$sql_btb_res[0]['PI_ID'];
	$sql_pi_res=sql_select("SELECT ITEM_CATEGORY_ID,BENEFICIARY,HS_CODE,PI_NUMBER,PI_DATE from com_pi_master_details where id in($pi_ids) and status_active=1");
	$pi_beneficiary="";
	foreach($sql_pi_res as $row)
	{
		$pi_info.=$row['PI_NUMBER']." DT.".$row['PI_DATE'].", ";
		$category_name=$item_category[$row['ITEM_CATEGORY_ID']];
		if($row['BENEFICIARY']){$pi_beneficiary.=$row['BENEFICIARY'].", ";}
		if($row['HS_CODE']){$pi_hs_code.=$row['HS_CODE'].", ";}
	}
	// LC SC Part
	$sql_lcSc=sql_select("SELECT a.LC_SC_ID,a.IMPORT_MST_ID, b.contract_no as LC_SC_NO, b.contract_date as LC_SC_DATE, b.contract_value as LC_SC_VALUE, a.IS_LC_SC
	from  com_btb_export_lc_attachment a, com_sales_contract b
	where a.lc_sc_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and a.is_lc_sc=1 and a.import_mst_id=$data
	union all 
	select a.LC_SC_ID,a.IMPORT_MST_ID, b.export_lc_no as LC_SC_NO, b.lc_date as LC_SC_DATE, b.lc_value as LC_SC_VALUE, a.IS_LC_SC
	from com_btb_export_lc_attachment a, com_export_lc b
	where a.lc_sc_id=b.id and a.status_active=1 and a.is_deleted=0 and a.is_lc_sc=0 and b.status_active=1 and b.is_deleted=0 and a.import_mst_id=$data");	
	$lc_sc_arr=array();
	foreach($sql_lcSc as $row)
	{
		$lc_sc_arr[$row['IMPORT_MST_ID']]['LC_SC_NO'].=$row['LC_SC_NO'].', ';
		$lc_sc_arr[$row['IMPORT_MST_ID']]['LC_SC_DATE'].=change_date_format($row['LC_SC_DATE']).', ';

		$lc_sc_value+= $row['LC_SC_VALUE'];
	}
	// echo '<pre>';print_r($lc_sc_arr);

	$lc_sc_no   = rtrim($lc_sc_arr[$btb_id]['LC_SC_NO'],', ');
	$lc_sc_date = rtrim($lc_sc_arr[$btb_id]['LC_SC_DATE'],', ');

	?>
	<table width="700" cellpadding="0" align="left" cellspacing="0" border="0">
		<tr><td colspan="5" height="100"></td></tr>
		<tr>
			<td ></td>
			<td ><?=$companyArr[$sql_btb_res[0]['IMPORTER_ID']];?></td>
			<td colspan="2" align="right">DATE:&nbsp;&nbsp;<?=$sql_btb_res[0]['APPLICATION_DATE']; ?></td>
			<td ></td>
		</tr>
		<tr><td colspan="5" height="30"></td></tr>
		<tr>
			<td ></td>
			<td colspan="3">T0</td>
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td colspan="3">The <?
				echo $designationArr[$bank_dtls_arr[$sql_btb_res[0]['ISSUING_BANK_ID']]['DESIGNATION']]."<br>".$bank_dtls_arr[$sql_btb_res[0]['ISSUING_BANK_ID']]['BANK_NAME']."<br>".$bank_dtls_arr[$sql_btb_res[0]['ISSUING_BANK_ID']]['BRANCH_NAME']."<br>".$bank_dtls_arr[$sql_btb_res[0]['ISSUING_BANK_ID']]['ADDRESS'];
				?></td>
			<td ></td>
		</tr>
		<tr><td colspan="5" height="30"></td></tr>
		<tr>
			<td ></td>
			<td colspan="3" style="text-align: justify">
			Sub: Request for Issuance FDD for <?=$currency[$sql_btb_res[0]['CURRENCY_ID']].''.$currency_sign_arr[$sql_btb_res[0]['CURRENCY_ID']]."&nbsp;".number_format($sql_btb_res[0]['LC_VALUE'],2);?> Infavour of PERFECT <?=$supplierArr[$sql_btb_res[0]['SUPPLIER_ID']];?>. Against CONTRACT NO. <?= $lc_sc_no;?> DT. <?=$lc_sc_date;?>
            </td>
			<td width="25"></td>
		</tr>
		<tr><td colspan="5" height="15"></td></tr>
		<tr>
			<td ></td>
			<td colspan="3">Dear Sir,</td>
			<td ></td>
		</tr>
		<tr><td colspan="5" height="10"></td></tr>
		<tr>
			<td ></td>
			<td colspan="3" style="text-align: justify">
			We would like to draw your kind attention that, we have to import some Goods from CEPZ On urgent basis which supplier is not agreed to supply trough L/C. they are asking for FDD. Payment. 
			</td>
			<td ></td>
		</tr>
		<tr><td colspan="5" height="15"></td></tr>				
		<tr>
			<td ></td>
			<td colspan="3">INFORMATION OF THE BENEFICIARY BANK ACCOUNT/Payment details. </td>
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td colspan="3">ACCOUNT NAME: <?=$supplierArr[$sql_btb_res[0]['SUPPLIER_ID']];?>  </td>
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td colspan="3">ACCOUNT NUMBER:   </td>
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td colspan="3">BANK'S NAME: <?=$sql_btb_res[0]["ADVISING_BANK"];?> </td>
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td colspan="3">ADDRESS: <?=$sql_btb_res[0]["ADVISING_BANK_ADDRESS"];?> </td>
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td colspan="3">SWIFT CODE: <?=$bank_dtls_arr[$sql_btb_res[0]['ISSUING_BANK_ID']]['SWIFT_CODE'];?></td>
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td colspan="3">PROFORMA INVOICE NO. <?=rtrim($pi_info,", ");?></td>
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td colspan="3">LCAF NO. <?=$sql_btb_res[0]["LCAF_NO"];?>, IRC NO. BA-0222025, H.S. CODE NO. 5903.20.10/6217.10.00 </td>
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td colspan="3" height="30">BILL CONSIGNED TO <?=$bank_dtls_arr[$sql_btb_res[0]['ISSUING_BANK_ID']]['BANK_NAME'];?> MENTION INTO TT </td>
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td colspan="3">INSURANCE COVER NOTE NO.</td>
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td colspan="3">This is for your kind information & takes necessary action in this regard</td>
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td colspan="3">Thanking you</td>
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td colspan="3">Yours faithfully,</td>
			<td ></td>
		</tr>
	</table>
	<?
	exit();
}

if($action=='btb_import_lc_letter15')
{
	$supplierArr = return_library_array('SELECT id, supplier_name FROM lib_supplier','id','supplier_name');
	$companyArr = return_library_array('SELECT id, company_name FROM lib_company','id','company_name');
	$designationArr = return_library_array("SELECT id,custom_designation from lib_designation where status_active=1 ","id","custom_designation");
	$currency_sign_arr=array(1=>'৳',2=>'$',3=>'€',4=>'€',5=>'$',6=>'£',7=>'¥');

	$sql_bank_info = sql_select("SELECT ID, BANK_NAME, BRANCH_NAME, ADDRESS, DESIGNATION from lib_bank ");
	$bank_dtls_arr=array();
	foreach($sql_bank_info as $row)
	{
		$bank_dtls_arr[$row['ID']]['BANK_NAME']=$row['BANK_NAME'];
		$bank_dtls_arr[$row['ID']]['BRANCH_NAME']=$row['BRANCH_NAME'];
		$bank_dtls_arr[$row['ID']]['ADDRESS']=$row['ADDRESS'];
		$bank_dtls_arr[$row['ID']]['DESIGNATION']=$row['DESIGNATION'];
	}


	
	
	$sql_btb="SELECT ID, BTB_SYSTEM_ID, LC_DATE, APPLICATION_DATE, IMPORTER_ID, ISSUING_BANK_ID, CURRENCY_ID, SUPPLIER_ID, LC_VALUE, PI_ID, MARGIN, ADVISING_BANK, ADVISING_BANK_ADDRESS, PAYTERM_ID, LC_TYPE_ID, TENOR,UPAS_RATE
	from com_btb_lc_master_details
	where id=$data and is_deleted=0 and status_active=1";
	// echo $sql_btb;
	$sql_btb_res=sql_select($sql_btb);
	$btb_id = $sql_btb_res[0]['ID'];

	$dcurrency_arr=array(1=>'Paisa',2=>'CENTS',3=>'CENTS',);

	// PI Part
	$pi_ids=$sql_btb_res[0]['PI_ID'];
	$sql_pi_res=sql_select("SELECT ITEM_CATEGORY_ID, BENEFICIARY, HS_CODE, PI_NUMBER, PI_DATE, SOURCE from com_pi_master_details where id in($pi_ids) and status_active=1");
	$pi_beneficiary="";
	foreach($sql_pi_res as $row)
	{
		$pi_info.=$row['PI_NUMBER']." Date: ".$row['PI_DATE'].", ";
		if($cate_check[$row['ITEM_CATEGORY_ID']]=="")
		{
			$cate_check[$row['ITEM_CATEGORY_ID']]=$row['ITEM_CATEGORY_ID'];
			$category_name.=$item_category[$row['ITEM_CATEGORY_ID']].", ";
		}
		
		if($row['BENEFICIARY']){$pi_beneficiary.=$row['BENEFICIARY'].", ";}
		if($row['HS_CODE']){$pi_hs_code.=$row['HS_CODE'].", ";}
		$pi_source=$row['SOURCE'];
	}
	// LC SC Part
	$sql_lcSc=sql_select("SELECT a.LC_SC_ID,a.IMPORT_MST_ID, b.contract_no as LC_SC_NO, b.contract_date as LC_SC_DATE, b.contract_value as LC_SC_VALUE, a.IS_LC_SC, b.INTERNAL_FILE_NO
	from  com_btb_export_lc_attachment a, com_sales_contract b
	where a.lc_sc_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and a.is_lc_sc=1 and a.import_mst_id=$data
	union all 
	select a.LC_SC_ID,a.IMPORT_MST_ID, b.export_lc_no as LC_SC_NO, b.lc_date as LC_SC_DATE, b.lc_value as LC_SC_VALUE, a.IS_LC_SC, b.INTERNAL_FILE_NO
	from com_btb_export_lc_attachment a, com_export_lc b
	where a.lc_sc_id=b.id and a.status_active=1 and a.is_deleted=0 and a.is_lc_sc=0 and b.status_active=1 and b.is_deleted=0 and a.import_mst_id=$data");	
	$lc_sc_arr=array();
	foreach($sql_lcSc as $row)
	{
		$lc_sc_arr[$row['IMPORT_MST_ID']]['LC_SC_NO'].=$row['LC_SC_NO'].', ';
		$lc_sc_arr[$row['IMPORT_MST_ID']]['LC_SC_DATE'].=change_date_format($row['LC_SC_DATE']).', ';
		$lc_sc_value+= $row['LC_SC_VALUE'];
		$int_file_no= $row['INTERNAL_FILE_NO'];
	}
	// echo '<pre>';print_r($lc_sc_arr);

	$lc_sc_no   = rtrim($lc_sc_arr[$btb_id]['LC_SC_NO'],', ');
	$lc_sc_date = rtrim($lc_sc_arr[$btb_id]['LC_SC_DATE'],', ');

	?>
    <img src="../../<? echo return_field_value("header_location","template_pad","company_id ='".$sql_btb_res[0]['IMPORTER_ID']."' and is_deleted=0 and status_active=1"); ?>" style="width:100%"  />
	<table id="tbl_body" width="100%" cellpadding="0" align="left" cellspacing="0" border="0">
		<tr><td colspan="3" height="30"></td></tr>

        <tr>
			<td></td>
			<td>Ref : <? $btb_data= explode("-",$sql_btb_res[0]['BTB_SYSTEM_ID']); echo $btb_data[2]."-".$btb_data[3];?></td>
            <td></td>
		</tr>
		<? if($int_file_no){ ?>
		<tr>
			<td width="50"></td>
			<td>File No : <?=$int_file_no;?></td>
            <td width="70"></td>
		</tr>
		<? } ?>
        <tr>
			<td colspan="3" height="10">&nbsp;</td>
		</tr>
        <tr>
			<td></td>
			<td>Date : <?= change_date_format($sql_btb_res[0]['APPLICATION_DATE']);?></td>
            <td></td>
		</tr>
		<tr><td colspan="3" height="15"></td></tr>
		<tr>
			<td ></td>
			<td>TO</td>
            <td></td>
		</tr>
		<tr>
			<td ></td>
			<td><?=$designationArr[$bank_dtls_arr[$sql_btb_res[0]['ISSUING_BANK_ID']]['DESIGNATION']];?></td>
			<td></td>
		</tr>
        <tr>
			<td ></td>
			<td><?
				echo $bank_dtls_arr[$sql_btb_res[0]['ISSUING_BANK_ID']]['BANK_NAME']."<br>".$bank_dtls_arr[$sql_btb_res[0]['ISSUING_BANK_ID']]['BRANCH_NAME'].", ".$bank_dtls_arr[$sql_btb_res[0]['ISSUING_BANK_ID']]['ADDRESS'];
				?></td>
			<td></td>
		</tr>
		<tr><td colspan="3" height="20"></td></tr>
		<tr>
			<td ></td>
			<td style="text-align: justify; font-size:22px; font-weight:bold;">
			<?php
				$pay_term_txt = ($sql_btb_res[0]['PAYTERM_ID'] == 1)?$pay_term[$sql_btb_res[0]['PAYTERM_ID']]." / EDF":$pay_term[$sql_btb_res[0]['PAYTERM_ID']];
				$pay_term_txt = ($sql_btb_res[0]['UPAS_RATE'])?$pay_term_txt." UPAS Rate ".$sql_btb_res[0]['UPAS_RATE']."% ":$pay_term_txt;

				$source[1]="Foreign";
				
			?>
			Sub: Request to open <?=$pay_term_txt;?> <?=$lc_type[$sql_btb_res[0]['LC_TYPE_ID']];?> for <?=$currency[$sql_btb_res[0]['CURRENCY_ID']]."&nbsp;".number_format($sql_btb_res[0]['LC_VALUE'],2);?>
            </td>
			<td width="25"></td>
		</tr>
		<tr><td colspan="3" height="20"></td></tr>
		<tr>
			<td ></td>
			<td colspan="3">Dear Sir,</td>
			<td ></td>
		</tr>
		<tr><td colspan="3" height="10"></td></tr>				
		<tr>
			<td ></td>
			<td style="text-align:justify;" colspan="3">With the reference to the caption subject you are requested to open the <b> <?=$lc_type[$sql_btb_res[0]['LC_TYPE_ID']];?> For <?=$sql_btb_res[0]['TENOR'];?> Days </b> in favour of <?=$supplierArr[$sql_btb_res[0]['SUPPLIER_ID']];?> for <?=$currency[$sql_btb_res[0]['CURRENCY_ID']]."&nbsp;".number_format($sql_btb_res[0]['LC_VALUE'],2);  ?> as per supplier's  proforma Invoice No: <?=rtrim($pi_info,", "); ?> for the import of <?= $source[$pi_source]. " ". chop($category_name,",");  if($lc_sc_no) echo " Under the Export LC/Contract No- " . $lc_sc_no." Dated : ".$lc_sc_date;?> <br> Under following documents are enclosed here: </td>
			<td ></td>
		</tr>
        <tr><td colspan="3" height="20"></td></tr>
		<tr>
			<td ></td>
			<td style="padding-left:20px;">1. LC application form (CF-7)..............01 original copy.</td>
			<td ></td>
		</tr>
		<tr>
			<td ></td>
			<td style="padding-left:20px;">2. Proforma invoice .............................01 original copy + 02 photocopies.</td>
			<td ></td>
		</tr>
   
        <tr><td colspan="3" height="30"></td></tr>
		<tr>
			<td ></td>
			<td>So, you are requested to take the necessary steps to open above L/C at your earliest.</td>
			<td ></td>
		</tr>
        <tr>
			<td ></td>
			<td>Your cordial co-operation will be highly appreciated.</td>
			<td ></td>
		</tr>
        <tr><td colspan="3" height="100"></td></tr>
		<tr>
			<td ></td>
			<td colspan="3" style="font-weight:bold; font-size:22px;">Thanking you</td>
			<td ></td>
		</tr>
		<tr><td colspan="3" height="80"></td></tr>
	</table>
	<img src="../../<? echo return_field_value("FOOTER_LOCATION","template_pad","company_id ='".$sql_btb_res[0]['IMPORTER_ID']."' and is_deleted=0 and status_active=1"); ?>" style="width:100%"  />
    
   <style>
   	#tbl_body{font-size:18px!important;}
	@media print{
		#tbl_body{font-size:20px!important;}	
	}
	
		@media screen{
		#tbl_body{font-size:20px!important;}	
	}
   </style>
    
	<?
	exit();
}

if($action=="print_button_variable")
{ 
    echo load_html_head_contents("Print Button Options", "../../../", 1, 1,'','','');
    extract($_REQUEST);
    list($company_id, $sys_id, $entryForm, $cbo_item_category_id, $export_pi_id) = explode('*', $print_data);
    ?>

    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    <script>
        function fnc_pi_approval_mst( operation )
        {
			//alert(operation);
            var pi_approval_mst_values = $("#pi_approval_mst_values").val();
            var approval_mst_value = pi_approval_mst_values.split("*");
            var company_id =  approval_mst_value[0];
            var sys_id = approval_mst_value[1];
            var entry_form = approval_mst_value[2];
            var cbo_item_category_id = approval_mst_value[3];
            var export_pi_id = approval_mst_value[4];
            var cbo_goods_rcv_status = 2;
            var cbo_pi_basis_id = 1;
            var is_approved = '';

            if(sys_id=="")
            {
                alert("Something went wrong");
                return;
            }
            // print
            if(operation==1)
            {
                if((cbo_item_category_id==74 || cbo_item_category_id==102 || cbo_item_category_id==103 || cbo_item_category_id==104)  && export_pi_id>0)
                {
                    alert("This Category Not Allow Without Export PI");return;
                }
                
                if((cbo_item_category_id==102 || cbo_item_category_id==103 || cbo_item_category_id==104 || cbo_item_category_id==31 || cbo_item_category_id==115) && cbo_goods_rcv_status==1)
                {
                    alert("After Goods Receive Status Not Allow For This Category");return;
                }

                var good_rece_data_source_arr = JSON.parse('<? echo json_encode($good_receive_data_source_arr); ?>');
                if(cbo_item_category_id==25 && cbo_goods_rcv_status==1 && good_rece_data_source_arr[cbo_importer_id]!=1)
                {
                    alert("After Goods Receive Status Only Allow For Varriable Setting After Good Receive Data Source always Work Order This Category.");return;
                }

                if(operation!=4)
                {
                    if(is_approved==1 || is_approved==3)
                    {
                        alert("PI is Approved. So Change Not Allowed");
                        return;
                    }
                }

                if(cbo_pi_basis_id==2 && cbo_goods_rcv_status==1)
                {
                    alert("Goods Rcv Status (After Goods Rcv) Not Allow For PI Basis (Independent)");
                    return;
                }
                   
                if( cbo_item_category_id == "1")
                {
                    entry_form = "165";
                }
                else if( cbo_item_category_id == "2" ||  cbo_item_category_id == "3" ||  cbo_item_category_id == "13" ||  cbo_item_category_id == "14")
                {
                    entry_form = "166";
                }
                else if( cbo_item_category_id == "4")
                {
                    entry_form = "167";
                }
                else if( cbo_item_category_id == "12")
                {
                    entry_form = "168";
                }
                else if( cbo_item_category_id == "24")
                {
                    entry_form = "169";
                }
                else if( cbo_item_category_id == "25" || cbo_item_category_id == "102" || cbo_item_category_id == "103")
                {
                    entry_form = "170";
                }
                else if( cbo_item_category_id == "30")
                {
                    entry_form = "197";
                }
                else if( cbo_item_category_id == "31")
                {
                    entry_form = "171";
                }
                else if( cbo_item_category_id == "5" ||  cbo_item_category_id == "6" ||  cbo_item_category_id == "7" ||  cbo_item_category_id == "23")
                {
                    entry_form = "227";
                }
                else
                {
                    entry_form = "172";
                } 
                print_report(company_id+'*'+sys_id+'*'+entry_form+'*'+cbo_item_category_id, "print", "../../../commercial/import_details/requires/pi_print_urmi");
                return;
              
            }
            // print-2
            if(operation==2){
                if(cbo_item_category_id==3)
                {
                    print_report(company_id+'*'+sys_id+'*'+cbo_item_category_id, "print_wf", "../../../commercial/import_details/requires/pi_print_urmi");
                }
                else
                {
                    alert("Only Woven Fabrics Item Print Allowed.");
                    return;
                }
            }
            // print-3
            if(operation==3)
            {
                if(cbo_item_category_id==12)
                {
                    print_report(company_id+'*'+sys_id+'*'+cbo_item_category_id, "print_sf", "../../../commercial/import_details/requires/pi_print_urmi");
                }
                else
                {
                    alert("Only Services Fabrics Item Print Allowed.");
                    return;
                }
            }
            // PI-print
            if(operation==4){
                if(cbo_item_category_id==4)
                {
                    print_report(company_id+'*'+sys_id+'*'+cbo_item_category_id, "print_pi", "../../../commercial/import_details/requires/pi_print_urmi");
                }
                else
                {
                    alert("Only Accessories Item Print Allowed.");
                    return;
                }
            }
            // Print-5
            if(operation==5){
                if(cbo_item_category_id==4)
                {
                    print_report(company_id+'*'+sys_id+'*'+cbo_item_category_id, "print_f", "../../../commercial/import_details/requires/pi_print_urmi");
                }
                else
                {
                    alert("Only Accessories Item Print Allowed.");
                    return;
                }
            }
        } 
    </script>

    <?
    $buttonHtml='';
    $print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$company_id."' and module_id=5 and report_id=183 and is_deleted=0 and status_active=1");
   	$printButton=explode(',',$print_report_format);
    $buttonHtml.='<div align="center">';
        foreach($printButton as $id){
            if($id==86)$buttonHtml.='
            <input type="hidden" name="printBtn4" id="pi_approval_mst_values" value="'.$print_data.'"/>
            <input type="button" name="printBtn4" id="printBtn4" value="Print" onClick="fnc_pi_approval_mst(1)" style="width:100px" class="formbutton"/>';

            if($id==116)$buttonHtml.='<input type="button" name="printBtn2" id="printBtn2" value="Print 2" onClick="fnc_pi_approval_mst(2)" style="width:100px" class="formbutton">';
            if($id==85)$buttonHtml.='<input type="button" name="printBtn3" id="printBtn3" value="Print 3" onClick="fnc_pi_approval_mst(3)" style="width:100px" class="formbutton">';	
            if($id==751)$buttonHtml.='<input type="button" name="printBtn" id="printBtn" value="PI-Print" onClick="fnc_pi_approval_mst(4)" style="width:100px" class="formbutton" />';	
            if($id==479)$buttonHtml.='<input type="button" name="printBtn" id="printBtn" value="Acc." onClick="fnc_pi_approval_mst(5)" style="width:100px" class="formbutton" />';
        }
    $buttonHtml.='</div>';
    echo $buttonHtml;
    exit();
} 

?>




?>




