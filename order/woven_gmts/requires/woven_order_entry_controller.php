<?
/*--------------------------------------------Comments----------------
Version (MySql)          :  V2
Version (Oracle)         :  V1
Converted by             :  MONZU
Converted Date           :  24-05-2014
Purpose			         : 	This form will create Knit Garments Order Entry
Functionality	         :	
JS Functions	         :
Created by		         :	Monzu 
Creation date 	         : 	13-10-2012
Requirment Client        : 
Requirment By            : 
Requirment type          : 
Requirment               : 
Affected page            : 
Affected Code            :                   
DB Script                : 
Updated by 		         : 	REZA	
Update date		         : 		   
QC Performed BY	         :		
QC Date			         :	
Comments		         :From this version oracle conversion is start
----------------------------------------------------------------------*/
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];
$buyer_cond=set_user_lavel_filtering(' and buy.id','buyer_id');
$company_cond=set_user_lavel_filtering(' and comp.id','company_id');

//************************************ Start*************************************************
// Master Form*************************************Master Form*************************
function publish_shipment_date($data){
	$publish_shipment_date=return_field_value("publish_shipment_date", "variable_order_tracking", "company_name=$data  and variable_list=25  and status_active=1 and is_deleted=0");
	if($publish_shipment_date !="") return trim($publish_shipment_date); else return 1;
}

function update_period_maintained_data($data){
	/*$update_period_id=return_field_value("po_update_period","variable_order_tracking"," company_name ='$data' and variable_list=32 and is_deleted=0 and status_active=1");
	if($update_period_id==""){
		$update_period_id=0; 
	}else{
		$update_period_id=$update_period_id;
	}
	return $update_period_id;*/
	$po_update_period=0;
	$po_update_period_user_id="";
	$sql=sql_select("select po_update_period,user_id from variable_order_tracking where company_name ='$data' and variable_list=32 and is_deleted=0 and status_active=1");
	foreach($sql as $row){
		if($row[csf('po_update_period')]){
			$po_update_period=$row[csf('po_update_period')];
			$po_update_period_user_id=$row[csf('user_id')];
		}
	}
	return  array ("po_update_period"=>$po_update_period,"user_id"=>$po_update_period_user_id);
}

function po_received_date_maintained_data($data){
	$po_current_date_data=return_field_value("po_current_date","variable_order_tracking"," company_name ='$data' and variable_list=33 and is_deleted=0 and status_active=1");
	if($po_current_date_data==""){
		$po_current_date_data=0;
	}else{
		$po_current_date_data=$po_current_date_data;
	}
	return $po_current_date_data;
}

function copy_quotation($data){
	$copy_quotsql=sql_select("select variable_list, copy_quotation, cost_control_source, publish_shipment_date from variable_order_tracking where company_name=$data and variable_list in (20,47,53)  and status_active=1 and is_deleted=0");
	foreach($copy_quotsql as $row)
	{
		if($row[csf('variable_list')]==20) $copy_quotation=$row[csf('copy_quotation')];
		else if($row[csf('variable_list')]==47) $set_smv_id=$row[csf('publish_shipment_date')];
		else if($row[csf('variable_list')]==53) $cost_control_source=$row[csf('cost_control_source')];
	}
	return $copy_quotation."_".$cost_control_source."_".$set_smv_id;
}

function season_mandatory($data){
	//echo "select season_mandatory from variable_order_tracking where company_name=$data  and variable_list=44  and status_active=1 and is_deleted=0";
	$season_mandatory=return_field_value("season_mandatory", "variable_order_tracking", "company_name=$data  and variable_list=44  and status_active=1 and is_deleted=0");
	if($season_mandatory !="") return trim($season_mandatory); else return 2;
}

if ($action=="load_drop_down_season_buyer")
{
	echo create_drop_down( "cbo_season_name", 140, "select id, season_name from lib_buyer_season where buyer_id='$data' and status_active =1 and is_deleted=0 order by season_name ASC","id,season_name", 1, "-- Select Season--", "", "" );
	exit();
}

function get_company_config($data)
{
	$cbo_location_name= create_drop_down( "cbo_location_name", 140, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select --", $selected, "" );
	global $buyer_cond;
	$cbo_buyer_name= create_drop_down( "cbo_buyer_name", 140, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "get_buyer_config(this.value)" ); 
	
	$cbo_agent= create_drop_down( "cbo_agent", 140, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id and b.tag_company='$data' and a.id in (select  buyer_id from  lib_buyer_party_type where party_type in (20,21))  order by buyer_name","id,buyer_name", 1, "-- Select Agent --", $selected, "" ); 
	$cbo_client= create_drop_down( "cbo_client", 140, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id and b.tag_company='$data' and a.id in (select  buyer_id from  lib_buyer_party_type where party_type in (7))  order by buyer_name","id,buyer_name", 1, "-- Select Client --", $selected, "" );
	$publish_shipment_date=publish_shipment_date($data);
	$update_period_maintained_data=update_period_maintained_data($data);
	$po_received_date_maintained_data=po_received_date_maintained_data($data);
	$copy_quotation_data=copy_quotation($data);
	
	$excopy_quotation=explode("_",$copy_quotation_data);
	$copy_quotation=$excopy_quotation[0];
	$cost_control_source=$excopy_quotation[1];
	$style_smv_source=$excopy_quotation[2];
	$sew_company_location=$excopy_quotation[3];
	
	$season_mandatory=season_mandatory($data);
	
	echo "document.getElementById('location').innerHTML = '".$cbo_location_name."';\n";
	echo "document.getElementById('buyer_td').innerHTML = '".$cbo_buyer_name."';\n";
	echo "document.getElementById('agent_td').innerHTML = '".$cbo_agent."';\n";
	echo "document.getElementById('party_type_td').innerHTML = '".$cbo_client."';\n";
	echo "publish_shipment_date(".$publish_shipment_date.");\n";
	echo "budget_exceeds_quot('".$copy_quotation.'_'.$cost_control_source.'_'.$style_smv_source."');\n";
	echo "document.getElementById('po_update_period_maintain').value = '".$update_period_maintained_data['po_update_period']."';\n";
	echo "document.getElementById('txt_user_id').value = '".$update_period_maintained_data['user_id']."';\n";
	echo "document.getElementById('po_current_date_maintain').value = '".$po_received_date_maintained_data."';\n";
	
	if($sew_company_location=="" || $sew_company_location==0) $sew_company_location=0; else $sew_company_location=$sew_company_location;
	echo "document.getElementById('sewing_company_validate_id').value 	= '".$sew_company_location."';\n";
	
	if($style_smv_source=="" || $style_smv_source==0) $style_smv_source=0; else $style_smv_source=$style_smv_source;
	echo "document.getElementById('set_smv_id').value 		= '".$style_smv_source."';\n"; 
	echo "document.getElementById('hid_cost_source').value 		= '".$cost_control_source."';\n";
	echo "document.getElementById('is_season_must').value = '".$season_mandatory."';\n";
}

if($action=="get_company_config"){
	$action($data);
}

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 140, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select --", $selected, "" );
	exit();	 
}
if ($action=="load_drop_down_sew_location")
{
	echo create_drop_down( "cbo_working_location_id", 140, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select --", $selected, "" );	
	exit();		 
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 140, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "sub_dept_load(this.value,document.getElementById('cbo_product_department').value);check_tna_templete(this.value)" ); 
	exit();	  	 
} 

if ($action=="load_drop_down_agent")
{
	echo create_drop_down( "cbo_agent", 140, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id and b.tag_company='$data' and a.id in (select  buyer_id from  lib_buyer_party_type where party_type in (20,21))  order by buyer_name","id,buyer_name", 1, "-- Select Agent --", $selected, "" ); 
	exit();	
} 

if ($action=="load_drop_down_party_type")
{
	echo create_drop_down( "cbo_client", 140, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id and b.tag_company='$data' and a.id in (select  buyer_id from  lib_buyer_party_type where party_type in (7))  order by buyer_name","id,buyer_name", 1, "-- Select Client --", $selected, "" ); 
	exit();	 
}

if ($action=="cbo_dealing_merchant")
{
	echo create_drop_down( "cbo_dealing_merchant", 140, "select id,team_member_name from lib_mkt_team_member_info where team_id='$data' and status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-- Select Team Member --", $selected, "" );
	exit();	
}

if ($action=="cbo_factory_merchant")
{
	echo create_drop_down( "cbo_factory_merchant", 140, "select id,team_member_name from lib_mkt_team_member_info where team_id='$data' and status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-- Select Team Member --", $selected, "" );
	exit();	
}

if ($action=="load_drop_down_sub_dep")
{
	$data=explode("_",$data);
	echo create_drop_down( "cbo_sub_dept", 140, "select id,sub_department_name from lib_pro_sub_deparatment where buyer_id=$data[0] and	department_id='$data[1]' and status_active =1 and is_deleted=0 order by sub_department_name","id,sub_department_name", 1, "-- Select Sub Dep --", $selected, "" );
}

if ($action=="load_drop_down_projected_po")
{
	echo create_drop_down( "cbo_projected_po", 100, "select id,po_number from  wo_po_break_down where job_no_mst='$data'   and is_confirmed=2 and status_active =1 and is_deleted=0 order by po_number","id,po_number", 1, "-- Select --", "", "" );
}

if($action=="publish_shipment_date")
{
	$publish_shipment_date=return_field_value("publish_shipment_date", "variable_order_tracking", "company_name=$data  and variable_list=25  and status_active=1 and is_deleted=0");

	if($publish_shipment_date !="") echo trim($publish_shipment_date); else echo 1;
	die;
}

if($action=="is_of_day")
{
	$data=explode("_",$data);
	if($db_type==0)
	{
		$txt_pub_shipment_date=change_date_format($data[1],'yyyy-mm-dd','-');
		$txt_org_shipment_date=change_date_format($data[2],'yyyy-mm-dd','-');
    }
	if($db_type==2)
	{
		$txt_pub_shipment_date=change_date_format($data[1],'','-',1);
		$txt_org_shipment_date=change_date_format($data[2],'','-',1);
    }
	$txt_pub_shipment=1;
	$txt_org_shipment=1;
	//echo "select b.day_status from lib_capacity_calc_mst a,lib_capacity_calc_dtls b where a.id=b.mst_id and a.comapny_id= $data[0] and a.capacity_source=1 and a.location_id=$data[3] and b.date_calc='$txt_pub_shipment_date' and a.status_active=1 and a.is_deleted=0";
	$is_of_day=return_field_value("publish_shipment_date", "variable_order_tracking", "company_name=$data[0]  and variable_list=46  and status_active=1 and is_deleted=0");
	if($is_of_day==2){
		//$txt_pub_shipment=return_field_value("day_status", "lib_capacity_calc_dtls", "date_calc='$txt_pub_shipment_date'");
		//$txt_org_shipment=return_field_value("day_status", "lib_capacity_calc_dtls", "date_calc=$txt_org_shipment_date");
		$txt_pub_shipment_sql=sql_select("select b.day_status from lib_capacity_calc_mst a,lib_capacity_calc_dtls b where a.id=b.mst_id and a.comapny_id= $data[0] and a.capacity_source=1 and a.location_id=$data[3] and b.date_calc='$txt_pub_shipment_date' and a.status_active=1 and a.is_deleted=0");
		$txt_pub_shipment=$txt_pub_shipment_sql[0][csf('day_status')];
		$txt_org_shipment_sql=sql_select("select b.day_status from lib_capacity_calc_mst a,lib_capacity_calc_dtls b where a.id=b.mst_id and a.comapny_id= $data[0] and a.capacity_source=1 and a.location_id=$data[3] and b.date_calc='$txt_org_shipment_date' and a.status_active=1 and a.is_deleted=0");
		$txt_org_shipment=$txt_org_shipment_sql[0][csf('day_status')];
		
	}
	echo $txt_pub_shipment."_".$txt_org_shipment;
	die;
}

if($action=="update_period_maintained_data")
{
	$update_period_id=return_field_value("po_update_period","variable_order_tracking"," company_name ='$data' and variable_list=32 and is_deleted=0 and status_active=1");
	if($update_period_id=="") $update_period_id=0; else $update_period_id=$update_period_id;
	echo "document.getElementById('po_update_period_maintain').value 				= '".$update_period_id."';\n";
	exit();	
}
if($action=="po_received_date_maintained_data")
{
	$po_current_date_data=return_field_value("po_current_date","variable_order_tracking"," company_name ='$data' and variable_list=33 and is_deleted=0 and status_active=1");
	if($po_current_date_data=="") $po_current_date_data=0; else $po_current_date_data=$po_current_date_data;
	echo "document.getElementById('po_current_date_maintain').value 				= '".$po_current_date_data."';\n";
	exit();	
}

if($action=="copy_quotation")
{
	$copy_quotsql=sql_select("select variable_list, copy_quotation, cost_control_source, publish_shipment_date, season_mandatory from variable_order_tracking where company_name=$data and variable_list in (20,47,53,64)  and status_active=1 and is_deleted=0");
	$copy_quotation=$set_smv_id=$cost_control_source=$sew_company_location=0;
	foreach($copy_quotsql as $row)
	{
		if($row[csf('variable_list')]==20) $copy_quotation=$row[csf('copy_quotation')];
		else if($row[csf('variable_list')]==47) $set_smv_id=$row[csf('publish_shipment_date')];
		else if($row[csf('variable_list')]==53) $cost_control_source=$row[csf('cost_control_source')];
		else if($row[csf('variable_list')]==64) $sew_company_location=$row[csf('season_mandatory')];
	}
	echo $copy_quotation."_".$cost_control_source."_".$set_smv_id."_".$sew_company_location;
	
	exit();
}

if ($action=="order_popup")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	//echo $_SERVER['SERVER_NAME'];
?>
	<script>
	function set_checkvalue()
	{
		if(document.getElementById('chk_job_wo_po').value==0) document.getElementById('chk_job_wo_po').value=1;
		else document.getElementById('chk_job_wo_po').value=0;
	}
	
	function js_set_value( job_no )
	{
		document.getElementById('selected_job').value=job_no;
		parent.emailwindow.hide();
	}
	
    </script>

</head>

<body>
<div align="center" style="width:100%;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
    <table cellspacing="0" width="1020" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
        <thead>
            <tr>
                <th colspan="10" align="center"><? echo create_drop_down( "cbo_string_search_type", 150, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
            </tr>
            <tr>                	 
                <th width="150" class="must_entry_caption">Company Name</th>
                <th width="150">Buyer Name</th>
                <th width="80">Job No</th>
                <th width="90">Style Ref </th>
                <th width="90">Internal Ref</th>
                <th width="90">File No</th>
                <th width="90">Order No</th>
                <th width="130" colspan="2">Ship Date Range</th>
                <th><input type="checkbox" value="0" onClick="set_checkvalue()" id="chk_job_wo_po">Job Without PO</th> 
            </tr>          
        </thead>
        <tr class="general">
            <td> 
            <input type="hidden" id="selected_job">
            <input type="hidden" id="garments_nature" value="<? echo $garments_nature; ?>">
                <? 
                    echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name",1, "-- Select Company --", '',"load_drop_down( 'woven_order_entry_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
                ?>
            </td>
            <td id="buyer_td">
             <? 
                echo create_drop_down( "cbo_buyer_name", 140, $blank_array,'', 1, "-- Select Buyer --" );
            ?>	</td>
            <td><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:80px"></td>
            <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:80px"></td>
            <td><input name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:80px"></td>
            <td><input name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:80px"></td>
            <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:80px"></td>
            <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px"></td>
            <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px"></td> 
            <td align="center">
             <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('chk_job_wo_po').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('garments_nature').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('txt_internal_ref').value+'_'+document.getElementById('txt_file_no').value, 'create_po_search_list_view', 'search_div', 'woven_order_entry_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" /></td>
    	</tr>
        <tr class="general">
            <td align="center" valign="middle" colspan="10">
             <? echo create_drop_down( "cbo_year_selection", 70, $year,"", 1, "-- Select --", date('Y'), "",0 ); echo load_month_buttons();  ?>
            </td>
        </tr>
    </table>    
    <div id="search_div" align="center"></div>
    </form>
   </div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="create_po_search_list_view")
{
	//echo $data;die;
	$data=explode('_',$data);
	if ($data[0]!=0) $company=" and a.company_name='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0){
		$buyer=" and a.buyer_name='$data[1]'"; 
	}
	else{
		$buyer="";
		$bu_arr=array();
		$pri_buyer=sql_select("select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name");
		foreach($pri_buyer as $pri_buyer_row){
			$bu_arr[$pri_buyer_row[csf('id')]]=$pri_buyer_row[csf('id')];
		}
		$bu_arr_str=implode(",",$bu_arr);
		$buyer=" and a.buyer_name in ($bu_arr_str)";
	}//{ echo "Please Select Buyer First."; die; }
	
	if($db_type==0)
	{
		$year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$data[7]";
		if ($data[3]!="" &&  $data[4]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[3], "yyyy-mm-dd", "-")."' and '".change_date_format($data[4], "yyyy-mm-dd", "-")."'"; else $shipment_date ="";
	}
	else if($db_type==2)
	{
		$year_cond=" and to_char(a.insert_date,'YYYY')=$data[7]";
		if ($data[3]!="" &&  $data[4]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."'"; else $shipment_date ="";
	}
	$order_cond=""; $job_cond=""; $style_cond="";
	$style_data = strtolower($data[10]);

	if($data[8]==1)
	{
		if (str_replace("'","",$data[6])!="") $job_cond=" and a.job_no_prefix_num='$data[6]'  $year_cond";
		if (trim($data[9])!="") $order_cond=" and b.po_number='$data[9]'  ";  
		if (trim($data[10])!="") $style_cond=" and lower(a.style_ref_no)='$style_data'"; 
	}
	else if($data[8]==4 || $data[8]==0)
	{
		if (str_replace("'","",$data[6])!="") $job_cond=" and a.job_no_prefix_num like '%$data[6]%'  $year_cond"; 
		if (trim($data[9])!="") $order_cond=" and b.po_number like '%$data[9]%'  ";
		if (trim($data[10])!="") $style_cond=" and lower(a.style_ref_no) like '%$style_data%'  ";
	}
	else if($data[8]==2)
	{
		if (str_replace("'","",$data[6])!="") $job_cond=" and a.job_no_prefix_num like '$data[6]%'  $year_cond";
		if (trim($data[9])!="") $order_cond=" and b.po_number like '$data[9]%'  ";
		if (trim($data[10])!="") $style_cond=" and lower(a.style_ref_no) like '$style_data%'  ";
	}
	else if($data[8]==3)
	{
		if (str_replace("'","",$data[6])!="") $job_cond=" and a.job_no_prefix_num like '%$data[6]'  $year_cond"; 
		if (trim($data[9])!="") $order_cond=" and b.po_number like '%$data[9]'  ";
		if (trim($data[10])!="") $style_cond=" and lower(a.style_ref_no) like '%$style_data'  ";
	}
			
	$internal_ref = str_replace("'","",$data[11]);
	$file_no = str_replace("'","",$data[12]);
	if ($file_no=="") $file_no_cond=""; else $file_no_cond=" and b.file_no='".trim($file_no)."' "; 
	if ($internal_ref=="") $internal_ref_cond=""; else $internal_ref_cond=" and b.grouping='".trim($internal_ref)."' "; 
	
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$arr=array (2=>$comp,3=>$buyer_arr,9=>$item_category);
	if($db_type==0)
	{
		$date_diff_cond="DATEDIFF(pub_shipment_date,po_received_date)";
		$year_select_cond="SUBSTRING_INDEX(a.insert_date, '-', 1)";
	}
	else if($db_type==2)
	{
		$date_diff_cond="(pub_shipment_date - po_received_date)";
		$year_select_cond="to_char(a.insert_date,'YYYY')";
	}
	if ($data[2]==0)
	{
		$sql= "select a.id, a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.job_quantity, b.po_number, b.po_quantity, b.shipment_date, a.garments_nature, b.grouping, b.file_no, $date_diff_cond as date_diff, $year_select_cond as year from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.garments_nature=$data[5] and a.status_active=1 and b.status_active=1 $shipment_date $company $buyer ".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." $job_cond $order_cond $style_cond $file_no_cond $internal_ref_cond order by a.id DESC";
	}
	else
	{
		$sql= "select a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.garments_nature, $year_select_cond as year from wo_po_details_master a where a.job_no not in( select distinct job_no_mst from wo_po_break_down where status_active=1 and is_deleted=0 ) and a.garments_nature=$data[5] and a.status_active=1 ".set_user_lavel_filtering(' and a.buyer_name','buyer_id')."  and a.is_deleted=0 $company $buyer $job_cond $style_cond order by a.id DESC";
	}
	//echo $sql;
	$result=sql_select($sql);
	?>
	<div align="left" style=" margin-left:5px;margin-top:10px"> 
    	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1020" align="left" class="rpt_table" >
 			<thead>
 				<th width="30">SL</th>
                <th width="80">Company</th>
 				<th width="50">Year</th>
 				<th width="50">Job No</th>               
 				<th width="100">Buyer Name</th>
                <th width="100">Style Ref. No</th>
                <th width="80">Job Qty.</th>
                <th width="90">PO number</th> 
                <th width="80">PO Qty.</th>
 				<th width="65">Shipment Date</th>
 				<th width="70">Internal Ref</th>
 				<th width="70">File No</th>  
                <th width="85">Gmts Nature</th>             
 				<th>Lead time</th>               
 			</thead>
 		</table>
    	<div style="width:1020px; max-height:270px; overflow-y:scroll" id="container_batch" >	 
 			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1000" class="rpt_table" id="list_view">  
 				<?
 				$i=1;
 				foreach ($result as $row)
 				{  
 					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF"; 
 					?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="cursor:pointer" onClick="js_set_value('<? echo $row[csf('job_no')];?>')"> 
                        <td width="30" align="center"><? echo $i; ?>  </td> 
                        <td width="80" style="word-break:break-all"><? echo $comp[$row[csf('company_name')]]; ?></p></td> 
                        <td width="50" style="word-break:break-all" align="center"><? echo $row[csf('year')]; ?></p></td>
                        <td width="50" style="word-break:break-all" align="center"><? echo $row[csf('job_no_prefix_num')]; ?></p></td>
                        <td width="100" style="word-break:break-all"><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></p></td>
                        <td width="100" style="word-break:break-all"><? echo $row[csf('style_ref_no')]; ?></p></td>
                        <td width="80" style="word-break:break-all" align="right"><? echo $row[csf('job_quantity')]; ?></p></td>
                        <td width="90" style="word-break:break-all"><? echo $row[csf('po_number')]; ?></p></td>
                        <td width="80" style="word-break:break-all" align="right"><? echo $row[csf('po_quantity')]; ?></p></td>
                        <td width="65" style="word-break:break-all"><? echo change_date_format($row[csf('shipment_date')]); ?></p></td>
                        <td width="70" style="word-break:break-all"><? echo $row[csf('grouping')]; ?></p></td>
                        <td width="70" style="word-break:break-all"><? echo $row[csf('file_no')]; ?></p></td>
                        <td width="85" style="word-break:break-all"><? echo $item_category[$row[csf('garments_nature')]]; ?></p></td>
                        <td style="word-break:break-all" align="center"><? echo $row[csf('date_diff')]; ?></p></td>
                    </tr> 
                    <? 
                    $i++;
 				}
 				?> 
 			</table>        
 		</div>
 	</div>
    <?php
	exit();
} 

if ($action=="populate_data_from_search_popup")
{
	$data_array=sql_select("select id, garments_nature, job_no, job_no_prefix, job_no_prefix_num, copy_from, company_name, buyer_name, location_name, style_ref_no, repeat_job_no, style_description, product_dept, product_code, pro_sub_dep, currency_id, agent_name, client_id, order_repeat_no, region, product_category, team_leader, dealing_marchant, bh_merchant, packing, remarks, ship_mode, order_uom, set_break_down, gmts_item_id, total_set_qnty, set_smv, season_buyer_wise, quotation_id, job_quantity, order_uom, avg_unit_price, currency_id, total_price, factory_marchant, style_owner, design_source_id, qlty_label, working_location_id from wo_po_details_master where job_no='$data'");
 
 	$company_id=$data_array[0][csf('company_name')];
	$team_leader=$data_array[0][csf('team_leader')];
	$dealing_marchant=$data_array[0][csf('dealing_marchant')];
	$factory_marchant=$data_array[0][csf('factory_marchant')];
	
	$update_period_id=return_field_value("po_update_period","variable_order_tracking"," company_name ='$company_id' and variable_list=32 and is_deleted=0 and status_active=1");
	$po_current_date_data=return_field_value("po_current_date","variable_order_tracking"," company_name ='$company_id' and variable_list=33 and is_deleted=0 and status_active=1");
	$is_precost_found=return_field_value("job_no","wo_pre_cost_mst"," job_no ='$data' and is_deleted=0 and status_active=1");
	//echo $is_precost_found.'ddd';
	
	if($update_period_id=="") $update_period_id=0; else $update_period_id=$update_period_id;
	if($po_current_date_data=="" || $po_current_date_data==2) $po_current_date_data=0; else $po_current_date_data=$po_current_date_data;
	
	$team_arr=array(); $team_deal_arr=array(); $team_fact_arr=array();
	$tsql=sql_select("(select id, team_leader_name from lib_marketing_team where status_active =1 and is_deleted=0 and id!=$team_leader) union all (select id, team_leader_name from lib_marketing_team where is_deleted=0 and id=$team_leader) order by team_leader_name ASC");
	foreach ($tsql as $row)
	{
		$team_arr[$row[csf("id")]]=$row[csf("team_leader_name")];
	}
	unset($tsql);
	$tmdsql=sql_select("(select id, team_member_name from lib_mkt_team_member_info where team_id='$team_leader' and status_active =1 and is_deleted=0 and id!=$dealing_marchant) union all (select id, team_member_name from lib_mkt_team_member_info where team_id='$team_leader' and is_deleted=0 and id=$dealing_marchant) order by team_member_name ASC");
 	foreach ($tmdsql as $row)
	{
		$team_deal_arr[$row[csf("id")]]=$row[csf("team_member_name")];
	}
	unset($tmdsql);
	
	$tmfsql=sql_select("(select id, team_member_name from lib_mkt_team_member_info where team_id='$team_leader' and status_active =1 and is_deleted=0 and id!=$factory_marchant) union all (select id, team_member_name from lib_mkt_team_member_info where team_id='$team_leader' and is_deleted=0 and id=$factory_marchant) order by team_member_name ASC");
 	foreach ($tmfsql as $row)
	{
		$team_fact_arr[$row[csf("id")]]=$row[csf("team_member_name")];
	}
	unset($tmfsql);
	
	foreach ($data_array as $row)
	{
		$cbo_team_leader= create_drop_down( "cbo_team_leader", 140, $team_arr,"", 1, "-- Select Team --", $selected, "load_drop_down( \'requires/woven_order_entry_controller\', this.value, \'cbo_dealing_merchant\', \'div_marchant\' ); load_drop_down( \'requires/woven_order_entry_controller\', this.value, \'cbo_factory_merchant\', \'div_marchant_factory\' )" );
		$cbo_dealing_merchant= create_drop_down( "cbo_dealing_merchant", 140, $team_deal_arr,"", 1, "-- Select Team Member --", $selected, "" );
		$cbo_factory_merchant= create_drop_down( "cbo_factory_merchant", 140, $team_fact_arr,"", 1, "-- Select Team Member --", $selected, "" );
		
		$cbo_projected_po= create_drop_down( "cbo_projected_po", 110, "select id,po_number from  wo_po_break_down where job_no_mst='".$row[csf("job_no")]."'   and is_confirmed=2 and status_active =1 and is_deleted=0 order by po_number","id,po_number", 1, "-- Select --", "", "" );
		
		//$active_po_list=show_po_active_listview($row[csf("job_no")]);
		echo "document.getElementById('div_teamleader').innerHTML = '".$cbo_team_leader."';\n";
		echo "document.getElementById('div_marchant').innerHTML = '".$cbo_dealing_merchant."';\n";
		echo "document.getElementById('div_marchant_factory').innerHTML = '".$cbo_factory_merchant."';\n";
		echo "document.getElementById('projected_po_td').innerHTML = '".$cbo_projected_po."';\n";
		//echo "document.getElementById('po_list_view').innerHTML = '".$active_po_list."';\n";
		
		get_company_config($row[csf("company_name")]);
		echo "document.getElementById('garments_nature').value = '".$row[csf("garments_nature")]."';\n";  
		echo "document.getElementById('txt_job_no').value = '".$row[csf("job_no")]."';\n";
		echo "document.getElementById('hidd_job_id').value = '".$row[csf("id")]."';\n"; 
		//echo "document.getElementById('txt_style_ref').setAttribute ('value', '".$row[csf("style_ref_no")]."');\n";
		echo "document.getElementById('txt_copy_form').value = '".$row[csf("copy_from")]."';\n";
		 echo "document.getElementById('cbo_design_source_id').value = '".$row[csf("design_source_id")]."';\n";  
		echo "document.getElementById('cbo_qltyLabel').value = '".$row[csf("qlty_label")]."';\n";  
		 
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_name")]."';\n";  
		echo "document.getElementById('cbo_location_name').value = '".$row[csf("location_name")]."';\n";  
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_name")]."';\n";  
		echo "document.getElementById('txt_style_ref').value = '".$row[csf("style_ref_no")]."';\n"; 
		echo "document.getElementById('txt_repeat_job_no').value = '".$row[csf("repeat_job_no")]."';\n";  
		echo "document.getElementById('txt_style_description').value = '".$row[csf("style_description")]."';\n";  
		echo "document.getElementById('cbo_product_department').value = '".$row[csf("product_dept")]."';\n";
		echo "document.getElementById('txt_product_code').value = '".$row[csf("product_code")]."';\n";
		echo "document.getElementById('txt_bhmerchant').value = '".$row[csf("bh_merchant")]."';\n";
		echo "document.getElementById('cbo_sub_dept').value = '".$row[csf("pro_sub_dep")]."';\n";
		echo "document.getElementById('cbo_currercy').value = '".$row[csf("currency_id")]."';\n";  
		echo "document.getElementById('cbo_agent').value = '".$row[csf("agent_name")]."';\n"; 
		echo "document.getElementById('cbo_client').value = '".$row[csf("client_id")]."';\n"; 
		echo "document.getElementById('po_update_period_maintain').value = '".$update_period_id."';\n"; 
		echo "document.getElementById('po_current_date_maintain').value = '".$po_current_date_data."';\n"; 
		echo "document.getElementById('cbo_working_company_id').value = '".$row[csf("style_owner")]."';\n";  
		echo "load_drop_down( 'requires/woven_order_entry_controller', '".$row[csf('style_owner')]."', 'load_drop_down_sew_location', 'sew_location' );\n";
		echo "document.getElementById('cbo_working_location_id').value = '".$row[csf("working_location_id")]."';\n";  
		
		//echo "$('#cbo_company_name').attr('disabled',true);\n"; 
		//echo "get_company_config(".$row[csf("company_name")].");set_smv_check(".$row[csf("company_name")].");\n";
		$current_date=date('d-m-Y');
		if($po_current_date_data==1){
			echo "document.getElementById('txt_po_received_date').value = '".$current_date."';\n";
			echo "$('#txt_po_received_date').attr('disabled',true);\n";   
		}
		else{
			echo "document.getElementById('txt_po_received_date').value = '';\n"; 
			echo "$('#txt_po_received_date').attr('disabled',false);\n";   
		}
		if($is_precost_found)
		{
			echo "$('#cbo_design_source_id').attr('disabled',true);\n";   
		}
		echo "document.getElementById('txt_repeat_no').value = '".$row[csf("order_repeat_no")]."';\n";  
		echo "document.getElementById('cbo_region').value = '".$row[csf("region")]."';\n";  
		echo "document.getElementById('txt_item_catgory').value = '".$row[csf("product_category")]."';\n";  
		echo "document.getElementById('cbo_team_leader').value = '".$row[csf("team_leader")]."';\n";  
		echo "document.getElementById('cbo_dealing_merchant').value = '".$row[csf("dealing_marchant")]."';\n"; 
		echo "document.getElementById('cbo_factory_merchant').value = '".$row[csf("factory_marchant")]."';\n"; 
		echo "document.getElementById('cbo_packing').value = '".$row[csf("packing")]."';\n";  
		echo "document.getElementById('txt_remarks').value = '".$row[csf("remarks")]."';\n";  
		echo "document.getElementById('cbo_ship_mode').value = '".$row[csf("ship_mode")]."';\n";
		echo "document.getElementById('cbo_order_uom').value = '".$row[csf("order_uom")]."';\n";
		echo "document.getElementById('set_breck_down').value = '".$row[csf("set_break_down")]."';\n";
		echo "document.getElementById('item_id').value = '".$row[csf("gmts_item_id")]."';\n";
		echo "document.getElementById('tot_set_qnty').value = '".$row[csf("total_set_qnty")]."';\n";
		echo "document.getElementById('tot_smv_qnty').value = '".$row[csf("set_smv")]."';\n";
		echo "document.getElementById('txt_quotation_id').value = '".$row[csf("quotation_id")]."';\n";
		echo "document.getElementById('txt_total_job_quantity').value = '".$row[csf("job_quantity")]."';\n";
		//echo "document.getElementById('set_pcs').value = '".$unit_of_measurement[$row[csf("order_uom")]]."';\n";
		echo "document.getElementById('set_pcs').value = '".$row[csf("order_uom")]."';\n";
		echo "document.getElementById('pojected_set_pcs').value = '".$row[csf("order_uom")]."';\n";
		echo "document.getElementById('currpojected_set_pcs').value = '".$row[csf("order_uom")]."';\n";
		echo "document.getElementById('txt_avg_unit_price').value = '".$row[csf("avg_unit_price")]."';\n";
		//echo "document.getElementById('set_unit').value = '".$currency[$row[csf("currency_id")]]."';\n";
		echo "document.getElementById('set_unit').value = '".$row[csf("currency_id")]."';\n";
		echo "document.getElementById('projected_set_unit').value = '".$row[csf("currency_id")]."';\n";
		echo "document.getElementById('currprojected_set_unit').value = '".$row[csf("currency_id")]."';\n";  
		echo "document.getElementById('txt_job_total_price').value = '".$row[csf("total_price")]."';\n";
		echo "load_drop_down( 'requires/woven_order_entry_controller','".$row[csf("buyer_name")]."', 'load_drop_down_season_buyer', 'season_td') ;";
		echo "document.getElementById('cbo_season_name').value = '".$row[csf("season_buyer_wise")]."';\n";
		echo "document.getElementById('update_id').value = '".$row[csf("job_no")]."';\n";  
		//echo "set_smv_check($row[csf("company_name")])";
	}
	
	$projected_data_array=sql_select("select sum(original_po_qty) as projected_qty, sum(original_po_qty*original_avg_price) as projected_amount, (sum(original_po_qty*original_avg_price)/sum(original_po_qty)) as projected_rate from wo_po_break_down where job_no_mst='$data'");
	foreach ($projected_data_array as $row_val)
	{
	    echo "document.getElementById('txt_projected_job_quantity').value = '".$row_val[csf("projected_qty")]."';\n";
		echo "document.getElementById('txt_projected_price').value = '".number_format($row_val[csf("projected_rate")],4)."';\n";
		echo "document.getElementById('txt_project_total_price').value = '".$row_val[csf("projected_amount")]."';\n";
	}
	
	$projected_data_array=sql_select("select sum(po_quantity) as po_qty, sum(po_quantity*unit_price) as po_amount, (sum(po_quantity*unit_price)/sum(po_quantity)) as po_rate from wo_po_break_down where job_no_mst='$data' and is_confirmed=2");
	foreach ($projected_data_array as $row_val)
	{
	    echo "document.getElementById('txt_currprojected_job_qnty').value = '".$row_val[csf("po_qty")]."';\n";
		echo "document.getElementById('txt_currprojected_price').value = '".number_format($row_val[csf("po_rate")],4)."';\n";
		echo "document.getElementById('txt_currproject_total_price').value = '".$row_val[csf("po_amount")]."';\n";
	}
	
	$internal=substr(return_library_autocomplete( "select distinct internal_ref as internal_ref  from wo_order_entry_internal_ref where job_no='$data' ", "internal_ref"  ), 0, -1);
	echo "internal( '".$internal."' ) ;\n"; 
}

if ($action=="repeat_job_popup") //Repeat Job
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	//echo $_SERVER['SERVER_NAME'];
?>
	<script>
	function js_set_value( job_no )
	{
		//alert(job_no);
		document.getElementById('selected_job').value=job_no;
		parent.emailwindow.hide();
	}
    </script>
</head>
<body>
<div align="center" style="width:100%;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="600" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
            <thead>                	 
                <th width="150">Company Name</th>
                <th width="150">Buyer Name</th>
                <th width="100">Style Ref </th>
                <th>&nbsp;</th>
            </thead>
            <tr class="general">
                <td> 
                <input type="hidden" id="selected_job">
                    <? echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $cbo_company_name,"load_drop_down( 'woven_order_entry_controller', this.value, 'load_drop_down_buyer', 'buyer_td' )",1 ); ?>
            </td>
            <td id="buyer_td">
             <? echo create_drop_down( "cbo_buyer_name", 172, "select id,buyer_name from lib_buyer  where status_active =1 and is_deleted=0 order by buyer_name",'id,buyer_name', 1, "-- Select Buyer --",$cbo_buyer_name,"",1 ); ?>	</td>
           
            <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:100px" value="<? echo $txt_style_ref;?>"></td>
             <td>
             <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_style').value, 'create_job_repeat_search_list_view', 'search_div', 'woven_order_entry_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
        </tr>
        <tr>
            <td  align="center" height="40" valign="middle"><? //echo load_month_buttons(1);  ?></td>
        </tr>
     </table>
     <div id="search_div"></div>
    </form>
   </div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="create_job_repeat_search_list_view")
{
	//echo $data;die;
	$data=explode('_',$data);
	$company=$data[0];
	$buyer_id=$data[1];
	$style=$data[2];
	
	if ($company!=0) $company_name=" and a.company_name='$company'"; else { echo "Please Select Company First."; die; }
	if ($buyer_id!=0){
		$buyer=" and a.buyer_name='$buyer_id'"; 
	}
	else{
		$buyer="";
		$bu_arr=array();
		$pri_buyer=sql_select("select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company'  and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name");
		foreach($pri_buyer as $pri_buyer_row){
			$bu_arr[$pri_buyer_row[csf('id')]]=$pri_buyer_row[csf('id')];
		}
		$bu_arr_str=implode(",",$bu_arr);
		$buyer=" and a.buyer_name in ($bu_arr_str)";
	}//{ echo "Please Select Buyer First."; die; }
	//echo $buyer;
	$style_cond="";
	if (trim($style)!="") $style_cond=" and a.style_ref_no='$style'  "; //else  $style_cond=""; 
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	//$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$arr=array (2=>$comp,3=>$buyer_arr,9=>$item_category);
	
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
					
	$sql= "select a.job_no,a.buyer_name,a.style_ref_no,a.job_quantity,$year_field from wo_po_details_master a  where   a.status_active=1 and a.status_active=1 $company_name   $style_cond  order by a.job_no desc";
		//echo $sql;
	echo  create_list_view("list_view", "Job No,Year,Style Ref.", "130,70,130","500","320",0, $sql , "js_set_value", "job_no", "", 1, "0,0,0", $arr , "job_no,year,style_ref_no", "",'','0,0,0,0');
}
	

if ($action=="order_popup_for_copy")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
?>
	<script>
	function js_set_value( po_id )
	{
		document.getElementById('po_id').value=po_id;
		parent.emailwindow.hide();
	}
    </script>
</head>
<body>
<div align="center" style="width:100%;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
 <input type="hidden" id="po_id">
 <input type="hidden" id="garments_nature" value="<? echo $garments_nature; ?>">
	<table width="1000" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
        <tr>
            <td align="center" valign="top" id="search_div"> 
            <?
			$arr=array (0=>$order_status,11=>$row_status);
			if($db_type==0)
			{
				 $sql= "select is_confirmed,po_number,po_received_date,pub_shipment_date,shipment_date,po_quantity,unit_price,po_total_price,excess_cut,plan_cut,DATEDIFF(pub_shipment_date,po_received_date) as  date_diff,status_active,id from  wo_po_break_down  where   status_active=1 and is_deleted=0 and job_no_mst='$txt_job_no'"; 
			}
			
			if($db_type==2)
			{
				 $sql= "select is_confirmed,po_number,po_received_date,pub_shipment_date,shipment_date,po_quantity,unit_price,po_total_price,excess_cut,plan_cut,(pub_shipment_date-po_received_date) as  date_diff,status_active,id from  wo_po_break_down  where   status_active=1 and is_deleted=0 and job_no_mst='$txt_job_no'"; 
			}
			 
			echo  create_list_view("list_view", "Order Status,PO No,PO Recv. Date,Ship Date,Orgn. Ship Date,PO Qnty,Avg. Rate,Amount, Excess Cut %,Plan Cut Qnty,Lead Time,Status", "90,130,80,80,80,80,80,80,80,80,50","1050","220",0, $sql , "js_set_value", "id", "", 1, "is_confirmed,0,0,0,0,0,0,0,0,0,0,status_active", $arr , "is_confirmed,po_number,po_received_date,pub_shipment_date,shipment_date,po_quantity,unit_price,po_total_price,excess_cut,plan_cut,date_diff,status_active", "requires/woven_order_entry_controller",'','0,0,3,3,3,1,2,2,2,2,1') ;
			?>
            </td>
        </tr>
    </table>    
     
    </form>
   </div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

function show_po_active_listview($data)
{
	global $db_type;
	$arr=array (0=>$order_status,12=>$row_status);
	
	if($db_type==0)
	{
 	 $sql= "select is_confirmed,po_number,po_received_date,pub_shipment_date,shipment_date,po_quantity,unit_price,po_total_price,excess_cut,plan_cut,DATEDIFF(pub_shipment_date,po_received_date) as  date_diff,status_active,id from  wo_po_break_down  where   status_active=1 and is_deleted=0 and job_no_mst='$data' order by po_number ASC ";
	 
	}
	
	if($db_type==2)
	{
 	   $sql= "select is_confirmed,po_number,po_received_date,pub_shipment_date,shipment_date,po_quantity,unit_price,po_total_price,excess_cut,plan_cut,(pub_shipment_date-po_received_date) as  date_diff,(pub_shipment_date-po_received_date) as fac_date_diff,status_active,id from  wo_po_break_down  where   status_active=1 and is_deleted=0 and job_no_mst='$data' order by po_number ASC"; 
	
	}
	return  create_list_view("list_view", "Order Status,PO No,PO Recv. Date,Ship Date,Orgn. Ship Date,PO Qnty,Avg. Rate,Amount, Excess Cut %,Plan Cut Qnty,Lead Time,Lead time on Fac Rcv Date,Status", "60,110,70,70,70,80,60,80,60,80,50,50","950","220",0, $sql , "get_details_form_data", "id", "'populate_order_details_form_data'", 1, "is_confirmed,0,0,0,0,0,0,0,0,0,0,0,status_active", $arr , "is_confirmed,po_number,po_received_date,pub_shipment_date,shipment_date,po_quantity,unit_price,po_total_price,excess_cut,plan_cut,date_diff,fac_date_diff,status_active", "requires/woven_order_entry_controller",'','0,0,3,3,3,1,4,4,2,2,2,1') ;
}

if ($action=="load_drop_down_tna_task")
{
	$sql_task = "SELECT a.id,task_template_id,lead_time,material_source,total_task,tna_task_id,deadline,execution_days,notice_before,a.sequence_no,for_specific,b.task_catagory,b.task_name,b.task_sequence_no FROM  tna_task_template_details a, lib_tna_task b WHERE  a.is_deleted = 0 and a.status_active=1 and a.tna_task_id=b.id order by for_specific,lead_time";
	$result = sql_select( $sql_task ) ;
	$tna_template = array();
	$i=0;
	$k=0;
	$j=0;
	foreach( $result as $row ) 
	{
		if (!in_array($row[csf("task_template_id")],$template))
		{
			$template[]=$row[csf("task_template_id")];
			if ( $row[csf("for_specific")]==0 )
			{
				$tna_template[$i]['lead']=$row[csf('lead_time')];
				$tna_template[$i]['id']=$row[csf('task_template_id')];
				$i++;
			}
			else
			{
				if(!in_array($row[csf('for_specific')],$tna_template_spc)) { $j=0; $tna_template_spc[]=$row[csf("for_specific")]; }
				$tna_template_buyer[$row[csf('for_specific')]][$j]['lead']=$row[csf('lead_time')];
				$tna_template_buyer[$row[csf('for_specific')]][$j]['id']=$row[csf('task_template_id')];
				$j++;
			}
			$k++;
		}
	}
	$data=explode("_",$data);
	$remain_days=datediff( "d", $data[0], $data[1] );
	$template_id=get_tna_template($remain_days,$tna_template,$data[2]);
	   //echo $template_id; 
	//echo $data[0].'='.$data[1].'='.$remain_days;
	
	echo create_drop_down( "cbo_tna_task", 110, "select b.task_name,b.task_short_name from  tna_task_template_details a,lib_tna_task b where a.tna_task_id=b.task_name and task_template_id='$template_id'  and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and a.task_type=1  order by b.task_sequence_no","task_name,task_short_name", 1, "-- Select --", "", "" );
	exit();
}

if($action=="check_tna_leadtime")
{
	$data=explode("_",$data);
	$txt_po_received_date=date('Y-m-d',strtotime($data[2]));
    $txt_pub_shipment_date=date('Y-m-d',strtotime($data[3]));
    $dDiff=datediff( 'd', $txt_po_received_date, $txt_pub_shipment_date, $using_timestamps = false );
	$temp=0;
	$sql_temp=sql_select("select count(for_specific) as for_specific  from tna_task_template_details where for_specific=$data[0] and lead_time<= '$dDiff'  and status_active=1 and is_deleted=0");
	foreach($sql_temp as $row_temp){
		if($row_temp[csf('for_specific')]>0) $temp=1; else $temp=0;
	}
	
	$tna=0;
	$tna_integrated=return_field_value("tna_integrated", "variable_order_tracking", "company_name=$data[1]  and variable_list=14  and status_active=1 and is_deleted=0");
    if($tna_integrated==1) $tna=1; else $tna=0;
	
	$tna_process_type=return_field_value("tna_process_type", "variable_order_tracking", "company_name=$data[1]  and variable_list=31  and status_active=1 and is_deleted=0");
    if($tna_process_type==1) $tna_process=1; else $tna_process=0;
	
	echo $temp."_".$tna."_".$dDiff."_".$tna_process;
	die;
}

if($action=="check_tna_templete")
{
	$data=explode("_",$data);
	$temp=0;
	$sql_temp=sql_select("select count(for_specific) as for_specific from tna_task_template_details where for_specific=$data[0] and status_active=1 and is_deleted=0");
	foreach($sql_temp as $row_temp){
		if($row_temp[csf('for_specific')]>0) $temp=1; else $temp=0;
	}
	//echo $temp;	
	$tna=0;
	$tna_integrated=return_field_value("tna_integrated", "variable_order_tracking", "company_name=$data[1]  and variable_list=14  and status_active=1 and is_deleted=0");
    if($tna_integrated==1) $tna=1; else $tna=0;
	
	$tna_process_type=return_field_value("tna_process_type", "variable_order_tracking", "company_name=$data[1]  and variable_list=31  and status_active=1 and is_deleted=0");
    if($tna_process_type==1) $tna_process=1; else $tna_process=0;
	
	echo $temp."_".$tna."_".$tna_process;
	die;
}

if ($action=="show_po_active_listview")
{
	$shipmentStatusArr = array(0 => "Full Pending", 1 => "Full Pending", 2 => "Partial Delivery", 3 => "Full Delivery/Closed");
	$arr=array (0=>$order_status,12=>$row_status,13=>$shipmentStatusArr);
	
	if($db_type==0)
	{
		$sql= "select is_confirmed, po_number, po_received_date, pub_shipment_date, shipment_date, po_quantity, unit_price, po_total_price, excess_cut, plan_cut, DATEDIFF(pub_shipment_date,po_received_date) as  date_diff, DATEDIFF(pub_shipment_date,factory_received_date) as facdate_diff, status_active, id, shiping_status from wo_po_break_down where status_active=1 and is_deleted=0 and job_no_mst='$data' order by id ASC"; 
	}
	else if($db_type==2)
	{
		$sql= "select is_confirmed, po_number, po_received_date, pub_shipment_date, shipment_date, po_quantity, unit_price, po_total_price, excess_cut, plan_cut, (pub_shipment_date-po_received_date) as  date_diff, (pub_shipment_date-factory_received_date) as  facdate_diff, status_active, id, shiping_status from wo_po_break_down where status_active=1 and is_deleted=0 and job_no_mst='$data' order by id ASC"; 
	}
	echo  create_list_view("list_view", "Order Status,PO No,PO Recv. Date,Ship Date,Orgn. Ship Date,PO Qty.,Avg. Rate,Amount, Excess Cut %,Plan Cut Qty.,Lead Time,Lead time on Fac Rcv Date,Status,Ship. Status", "65,100,65,65,65,70,50,70,50,70,50,50,50","970","220",0, $sql , "get_details_form_data", "id", "'populate_order_details_form_data'", 1, "is_confirmed,0,0,0,0,0,0,0,0,0,0,0,status_active,shiping_status", $arr , "is_confirmed,po_number,po_received_date,pub_shipment_date,shipment_date,po_quantity,unit_price,po_total_price,excess_cut,plan_cut,date_diff,facdate_diff,status_active,shiping_status", "requires/woven_order_entry_controller",'','0,0,3,3,3,1,4,4,2,2,1,1') ;
	
	if($db_type==0)
	{
		$sql= "select is_confirmed, po_number, po_received_date, shipment_date, pub_shipment_date, po_quantity, unit_price, po_total_price, excess_cut, plan_cut, DATEDIFF(shipment_date,po_received_date) as date_diff, DATEDIFF(pub_shipment_date,factory_received_date) as facdate_diff, status_active, id, shiping_status from wo_po_break_down where status_active !=1 and job_no_mst='$data' order by id ASC"; 
	}
	else if($db_type==2)
	{
		$sql= "select is_confirmed, po_number, po_received_date, shipment_date, pub_shipment_date, po_quantity, unit_price, po_total_price, excess_cut, plan_cut, (shipment_date-po_received_date) as  date_diff,(pub_shipment_date-factory_received_date) as  facdate_diff, status_active, id, shiping_status from wo_po_break_down where status_active !=1 and job_no_mst='$data' order by id ASC"; 
	}
	$sqldata=sql_select($sql);
	if(count($sqldata)>0){
		echo  create_list_view("list_view", "Order Status,PO No,PO Recv. Date,Ship Date,Orgn. Ship Date,PO Qnty,Avg. Rate,Amount, Excess Cut %,Plan Cut Qnty,Lead Time,Lead time on Fac Rcv Date,Status,Ship. Status", "65,100,65,65,65,70,50,70,50,70,50,50,50","970","220",0, $sql , "get_details_form_data", "id", "'populate_order_details_form_data'", 1, "is_confirmed,0,0,0,0,0,0,0,0,0,0,0,status_active,shiping_status", $arr , "is_confirmed,po_number,po_received_date,shipment_date,pub_shipment_date,po_quantity,unit_price,po_total_price,excess_cut,plan_cut,date_diff,facdate_diff,status_active,shiping_status", "requires/woven_order_entry_controller",'','0,0,3,3,3,1,4,4,2,2,1,1') ;
	}
	exit();
}

if ($action=="show_deleted_po_active_listview"){
	$arr=array (0=>$order_status,12=>$row_status);
	if($db_type==0){
	$sql= "select is_confirmed,po_number,po_received_date,shipment_date,pub_shipment_date,po_quantity,unit_price,po_total_price,excess_cut,plan_cut,DATEDIFF(shipment_date,po_received_date) as date_diff, DATEDIFF(pub_shipment_date,factory_received_date) as facdate_diff, status_active,id from  wo_po_break_down  where   status_active !=1  and job_no_mst='$data'"; 
	}
	if($db_type==2){
	$sql= "select is_confirmed,po_number,po_received_date,shipment_date,pub_shipment_date,po_quantity,unit_price,po_total_price,excess_cut,plan_cut,(shipment_date-po_received_date) as  date_diff, (pub_shipment_date-factory_received_date) as  facdate_diff, status_active,id from  wo_po_break_down  where   status_active !=1  and job_no_mst='$data'"; 
	}
	echo  create_list_view("list_view", "Order Status,PO No,PO Recv. Date,Ship Date,Orgn. Ship Date,PO Qnty,Avg. Rate,Amount, Excess Cut %,Plan Cut Qnty,Lead Time,Status", "70,130,65,65,65,80,60,80,60,80,50,50","950","220",0, $sql , "get_details_form_data", "id", "'populate_order_details_form_data'", 1, "is_confirmed,0,0,0,0,0,0,0,0,0,0,0,status_active", $arr , "is_confirmed,po_number,po_received_date,shipment_date,pub_shipment_date,po_quantity,unit_price,po_total_price,excess_cut,plan_cut,date_diff,facdate_diff,status_active", "requires/woven_order_entry_controller",'','0,0,3,3,3,1,4,4,2,2,1,1') ;
}

if ($action=="quotation_id_popup")
{
  	echo load_html_head_contents("Woven Order Entry","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
		function js_set_value( quotation_id )
		{
			document.getElementById('selected_id').value=quotation_id;
			parent.emailwindow.hide();
		}
    </script>
    </head>
    <body>
    <div align="center" style="width:100%;" >
        <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
            <table width="860" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
                <thead>
                    <tr>
                        <th colspan="6"><? echo create_drop_down( "cbo_string_search_type", 140, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                    </tr>
                    <tr>
                        <th width="150">Company Name</th>
                        <th width="150">Buyer Name</th>
                        <th width="70">Quotation ID</th>
                        <th width="100">Style Ref.</th>
                        <th width="180">Delv. Date Range</th>
                        <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
                    </tr>
                </thead>
                <tr class="general">
                    <td> <input type="hidden" id="selected_id">
                        <? echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $cbo_company_name,"load_drop_down('order_entry_controller', this.value, 'load_drop_down_buyer_pop', 'buyer_pop_td' );",'1' ); ?>
                    </td>
                    <td id="buyer_pop_td"><? echo create_drop_down( "cbo_buyer_name", 150, $blank_array,'', 1, "-- Select Buyer --" ); ?></td>
                    <td><input type="text" style="width:70px" class="text_boxes"  name="txt_quotation_no" id="txt_quotation_no" /></td>
                    <td align="center"><input type="text" style="width:100px" class="text_boxes"  name="txt_style" id="txt_style" /></td>
                    <td>
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date">To
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To Date">
                    </td>
                    <td align="center">
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_quotation_no').value+'_'+document.getElementById('txt_style').value+'_'+'<? echo $txt_job_no; ?>', 'create_quotation_id_list_view', 'search_div', 'woven_order_entry_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
                </tr>
                <tr>
                    <td align="center" height="40" colspan="6"><? echo load_month_buttons(1); ?></td>
                </tr>
            </table>
        </form>
    </div>
    <div id="search_div"></div>
    </body> 
    <script>
		load_drop_down('woven_order_entry_controller', <? echo  $cbo_company_name ?>, 'load_drop_down_buyer', 'buyer_pop_td' );
		document.getElementById('cbo_buyer_name').value=<? echo $cbo_buyer_name; ?>;
    </script>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if($action=="create_quotation_id_list_view")
{
	$data=explode('_',$data);
	if ($data[0]!=0) $company=" and company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $buyer=" and buyer_id='$data[1]'"; else $buyer="";// else { echo "Please Select Buyer First."; die; }
	//echo $buyer."mmmm";
	if($db_type==0)
	{
		if ($data[2]!="" &&  $data[3]!="") $est_ship_date  = "and est_ship_date  between '".change_date_format($data[3], "yyyy-mm-dd", "-")."' and '".change_date_format($data[4], "yyyy-mm-dd", "-")."'"; else $est_ship_date ="";
	}
	else if($db_type==2)
	{
		if ($data[2]!="" &&  $data[3]!="") $est_ship_date  = "and est_ship_date  between '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."'"; else $est_ship_date ="";
	}
	
	$style_cond=""; $quotation_id_cond="";
	if($data[4]==1)
	{
	   if (trim($data[5])!="") $quotation_id_cond=" and id='$data[5]'";
	   if (trim($data[6])!="") $style_cond=" and style_ref='$data[6]'";
	}
	else if($data[4]==4 || $data[4]==0)
	{
	  if (trim($data[5])!="") $quotation_id_cond=" and id like '%$data[5]%' ";
	  if (trim($data[6])!="") $style_cond=" and style_ref like '%$data[6]%' ";
	}
	else if($data[4]==2)
	{
	  if (trim($data[5])!="") $quotation_id_cond=" and id like '$data[5]%' "; 
	  if (trim($data[6])!="") $style_cond=" and style_ref like '$data[6]%' ";
	}
	else if($data[4]==3)
	{
	  if (trim($data[5])!="") $quotation_id_cond=" and id like '%$data[5]' ";
	  if (trim($data[6])!="") $style_cond=" and style_ref like '%$data[6]' "; 
	}
		
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$arr=array (1=>$comp,2=>$buyer_arr,5=>$pord_dept);
	 $sql= "select id,company_id, buyer_id, style_ref,style_desc,pord_dept,offer_qnty,est_ship_date from  wo_price_quotation a where status_active=1  and is_deleted=0 $company $buyer $style_cond $quotation_id_cond order by id";
	echo  create_list_view("list_view", "Quotation ID,Company,Buyer Name,Style Ref,Style Desc.,Prod. Dept., Offer Qnty, Est Ship Date", "90,120,100,100,200,100,100","1000","320",0, $sql , "js_set_value", "id", "", 1, "0,company_id,buyer_id,0,0,pord_dept,0,0", $arr , "id,company_id,buyer_id,style_ref,style_desc,pord_dept,offer_qnty,est_ship_date", "",'','0,0,0,0,0,0,2,3') ;
	exit();
}

if ($action=="populate_data_from_search_popup_quotation")
{
	$data_array=sql_select("select a.id, a.company_id, a.buyer_id, a.style_ref, a.revised_no, a.pord_dept,a.product_code, a.style_desc, a.currency, a.agent, a.offer_qnty, a.region, a.color_range, a.incoterm, a.incoterm_place, a.machine_line, a.prod_line_hr, a.fabric_source, a.costing_per, a.quot_date, a.est_ship_date, a.factory,a.season_buyer_wise, a.remarks, a.garments_nature,a.order_uom,a.gmts_item_id,a.set_break_down,a.total_set_qnty,b.price_with_commn_pcs,i.season_buyer_wise as in_season_buyer_wise from wo_price_quotation_costing_mst b,  wo_price_quotation a left join wo_quotation_inquery i on a.inquery_id=i.id where a.id=b.quotation_id and a.id='$data'");
	foreach ($data_array as $row)
	{
		echo "load_drop_down( 'requires/woven_order_entry_controller', '".$row[csf("company_id")]."', 'load_drop_down_buyer', 'buyer_td' ); load_drop_down( 'requires/quotation_entry_controller', '".$row[csf("company_id")]."', 'load_drop_down_agent', 'agent_td' ); load_drop_down( 'requires/woven_order_entry_controller', '".$row[csf("company_id")]."', 'load_drop_down_party_type', 'party_type_td' );sub_dept_load('".$row[csf("buyer_id")]."','".$row[csf("pord_dept")]."');\n";
		echo "load_drop_down( 'requires/woven_order_entry_controller','".$row[csf("buyer_id")]."', 'load_drop_down_season_buyer', 'season_td') ;";
		
		//echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_id")]."';\n";  
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_id")]."';\n";  
		echo "document.getElementById('txt_style_ref').value = '".$row[csf("style_ref")]."';\n";
		echo "document.getElementById('txt_style_description').value = '".$row[csf("style_desc")]."';\n";
		echo "document.getElementById('cbo_product_department').value = '".$row[csf("pord_dept")]."';\n"; 
		echo "document.getElementById('txt_product_code').value = '".$row[csf("product_code")]."';\n";
		//echo "document.getElementById('cbo_sub_dept').value = '".$row[csf("pro_sub_dep")]."';\n";  
		echo "document.getElementById('cbo_currercy').value = '".$row[csf("currency")]."';\n";  
		echo "document.getElementById('cbo_agent').value = '".$row[csf("agent")]."';\n";  
		echo "document.getElementById('cbo_region').value = '".$row[csf("region")]."';\n";
		
		echo "document.getElementById('cbo_order_uom').value = '".$row[csf("order_uom")]."';\n";  
		echo "document.getElementById('item_id').value = '".$row[csf("gmts_item_id")]."';\n";  
		echo "document.getElementById('set_breck_down').value = '".$row[csf("set_break_down")]."';\n";  
		echo "document.getElementById('tot_set_qnty').value = '".$row[csf("total_set_qnty")]."';\n";  
		$season_buyer_wise=$row[csf("season_buyer_wise")];
		if(!$season_buyer_wise){
			$season_buyer_wise=$row[csf("in_season_buyer_wise")];
		}
		echo "document.getElementById('txt_quotation_id').value = '".$row[csf("id")]."';\n";
		echo "document.getElementById('txt_avg_price').value = '".$row[csf("price_with_commn_pcs")]."';\n";
		echo "document.getElementById('txt_quotation_price').value = '".$row[csf("price_with_commn_pcs")]."';\n";
		echo "document.getElementById('cbo_season_name').value = '".$season_buyer_wise."';\n";
		//echo "location_select();\n";
	}
	exit();
}

if ($action=="qc_id_popup")
{
  	echo load_html_head_contents("Quick Costing Tag popup","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	//echo $cbo_company_name;
	?>
	<script>
		function js_set_value( quotation_id )
		{
			document.getElementById('selected_id').value=quotation_id;
			parent.emailwindow.hide();
		}
    </script>
    </head>
    <body>
    <div align="center" style="width:100%;" >
    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
    <table width="750" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
        <thead>
            <tr>
                <th colspan="5"><? echo create_drop_down( "cbo_string_search_type", 140, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
            </tr>
            <tr>
                <th width="150">Buyer Name</th>
                <th width="70">Cost Sheet No</th>
                <th width="100">Style Ref.</th>
                <th width="180">Date Range</th>
                <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
            </tr>
        </thead>
        <tr class="general">
            <td id="buyer_pop_td"><? echo create_drop_down( "cbo_buyer_id", 150, $blank_array,'', 1, "-- Select Buyer --" ); ?></td>
            <td><input type="text" style="width:70px" class="text_boxes"  name="txt_quotation_no" id="txt_quotation_no" /></td>
            <td align="center"><input type="text" style="width:100px" class="text_boxes"  name="txt_style" id="txt_style" /></td>
            <td>
                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date">To
                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To Date">
            </td>
            <td align="center"><input type="hidden" id="selected_id">
            	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( <? echo $cbo_company_name; ?>+'_'+document.getElementById('cbo_buyer_id').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_quotation_no').value+'_'+document.getElementById('txt_style').value, 'create_qc_id_list_view', 'search_div', 'woven_order_entry_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
        </tr>
        <tr>
        	<td align="center" colspan="5"><? echo load_month_buttons(1); ?></td>
        </tr>
    </table>
    </form>
    	<div id="search_div"></div>
    </div>
    </body>
    <script>
		load_drop_down('order_entry_controller', <? echo  $cbo_company_name; ?>, 'load_drop_down_buyer_pop', 'buyer_pop_td' )
		document.getElementById('cbo_buyer_id').value=<? echo $cbo_buyer_name; ?>
    </script>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if($action=="create_qc_id_list_view")
{
	$data=explode('_',$data);
	//echo $data[1];
	//if ($data[0]!=0) $company=" and company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $buyer_cond=" and a.buyer_id='$data[1]'"; else $buyer_cond="";//else { echo "Please Select Buyer First."; die; }
	//echo $data[0];
	if($db_type==0)
	{
		if ($data[2]!="" &&  $data[3]!="") $est_ship_date  = "and a.delivery_date  between '".change_date_format($data[3], "yyyy-mm-dd", "-")."' and '".change_date_format($data[4], "yyyy-mm-dd", "-")."'"; else $est_ship_date ="";
	}
	else if($db_type==2)
	{
		if ($data[2]!="" &&  $data[3]!="") $est_ship_date  = "and a.delivery_date  between '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."'"; else $est_ship_date ="";
	}

	$style_cond="";
	$quotation_id_cond="";
	if($data[4]==1)
	{
		if (trim($data[5])!="") $quotation_id_cond=" and a.id='$data[5]'";
		if (trim($data[6])!="") $style_cond=" and a.style_ref='$data[6]'";
	}
	else if($data[4]==4 || $data[4]==0)
	{
		if (trim($data[5])!="") $quotation_id_cond=" and a.id like '%$data[5]%' ";
		if (trim($data[6])!="") $style_cond=" and a.style_ref like '%$data[6]%' ";
	}
	else if($data[4]==2)
	{
		if (trim($data[5])!="") $quotation_id_cond=" and a.id like '$data[5]%' ";
		if (trim($data[6])!="") $style_cond=" and a.style_ref like '$data[6]%' ";
	}
	else if($data[4]==3)
	{
		if (trim($data[5])!="") $quotation_id_cond=" and a.id like '%$data[5]' ";
		if (trim($data[6])!="") $style_cond=" and a.style_ref like '%$data[6]' ";
	}

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');

	$arr=array (2=>$buyer_arr,5=>$pord_dept);
	
	$sql_approved="select b.approval_need, b.allow_partial from approval_setup_mst a, approval_setup_dtls b where a.id=b.mst_id and b.status_active=1 and page_id=28 and a.company_id='$data[0]'  order by b.id desc";
	$result_nasscity = sql_select($sql_approved); $approved_need=2; $allow_partial=2;
	foreach($result_nasscity as $row)
	{
		$approved_need=$row[csf("approval_need")];
		$allow_partial=$row[csf("allow_partial")];
	}
	unset($result_nasscity);
	if($approved_need==1 && $allow_partial==1) $approved_need_cond="and a.approved in (1,3)"; else if($approved_need==1 && $allow_partial==2) $approved_need_cond="and a.approved in (1)"; else $approved_need_cond="";
	
	$sql= "select a.id, a.cost_sheet_no, a.buyer_id, a.style_ref, a.style_des, a.department_id, a.offer_qty, a.delivery_date, b.confirm_style from qc_mst a, qc_confirm_mst b where a.qc_no=b.cost_sheet_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $buyer_cond $style_cond $quotation_id_cond $approved_need_cond order by a.id DESC";
	//echo $sql;
	echo create_list_view("list_view", "QC ID, Cost Sheet No, Buyer Name, Style Ref, Style Desc., Prod. Dept., Offer Qty, Delivery Date", "50,70,100,100,150,100,100","800","280",0, $sql , "js_set_value", "id", "", 1, "0,0,buyer_id,0,0,department_id,0,0", $arr , "id,cost_sheet_no,buyer_id,confirm_style,style_des,department_id,offer_qty,delivery_date", "",'','0,0,0,0,0,0,2,3') ;
	exit();
}

if($action=="populate_data_from_search_popup_qc")
{
	$qcFob_arr=return_library_array( "select mst_id, tot_fob_cost from qc_tot_cost_summary where status_active=1 and is_deleted=0",'mst_id','tot_fob_cost');
	$data_array=sql_select("select a.id, a.qc_no, a.cost_sheet_no, a.buyer_id, a.season_id, a.style_ref, a.style_des, a.department_id, a.offer_qty, a.delivery_date, b.confirm_style from qc_mst a, qc_confirm_mst b where a.qc_no=b.cost_sheet_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id='$data'");
	foreach ($data_array as $row)
	{
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_id")]."';\n";
		echo "document.getElementById('txt_style_ref').value = '".$row[csf("confirm_style")]."';\n";
		echo "document.getElementById('txt_style_description').value = '".$row[csf("style_des")]."';\n";
		echo "document.getElementById('cbo_product_department').value = '".$row[csf("department_id")]."';\n";
		echo "document.getElementById('cbo_season_name').value = '".$row[csf("season_id")]."';\n";
		echo "document.getElementById('txt_quotation_id').value = '".$row[csf("qc_no")]."';\n";
		echo "document.getElementById('txt_avg_price').value = '".$qcFob_arr[$row[csf("qc_no")]]."';\n";
		echo "$('#txt_avg_price').attr('quot_cost','".$qcFob_arr[$row[csf("qc_no")]]."');\n";
		exit();
	}
}

if ($action=="ws_id_popup")
{
  	echo load_html_head_contents("Work Study Tag popup","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	//echo $cbo_company_name;
	?>
	<script>
		function js_set_value( quotation_id )
		{
			document.getElementById('selected_id').value=quotation_id;
			parent.emailwindow.hide();
		}
    </script>
    </head>
    <body>
    <div align="center" style="width:100%;" >
    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
    <table width="750" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
        <thead>
            <tr>
                <th colspan="4"><? echo create_drop_down( "cbo_string_search_type", 140, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
            </tr>
            <tr>
                <th width="150">Buyer Name</th>
                <th width="70">System ID</th>
                <th width="100">Style Ref.</th>
                <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
            </tr>
        </thead>
        <tr class="general">
            <td id="buyer_pop_td"><? echo create_drop_down( "cbo_buyer_id", 150, $blank_array,'', 1, "-- Select Buyer --" ); ?></td>
            <td><input type="text" style="width:70px" class="text_boxes"  name="txt_quotation_no" id="txt_quotation_no" /></td>
            <td align="center"><input type="text" style="width:100px" class="text_boxes"  name="txt_style" id="txt_style" /></td>
            <td align="center"><input type="hidden" id="selected_id">
            	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( <? echo $cbo_company_name; ?>+'_'+document.getElementById('cbo_buyer_id').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_quotation_no').value+'_'+document.getElementById('txt_style').value, 'create_ws_id_list_view', 'search_div', 'woven_order_entry_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
        </tr>
    </table>
    </form>
    	<div id="search_div"></div>
    </div>
    </body>
    <script>
		load_drop_down('order_entry_controller', <? echo  $cbo_company_name; ?>, 'load_drop_down_buyer_pop', 'buyer_pop_td');
		document.getElementById('cbo_buyer_id').value=<? echo $cbo_buyer_name; ?>;
		$('#cbo_buyer_id').attr('disabled',true);
    </script>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if($action=="create_ws_id_list_view")
{
	$data=explode('_',$data);
	$company=$data[0];
	$buyer_id=$data[1];
	$search_type=$data[2];
	$sysNo=$data[3];
	$styleRef=$data[4];
	
	if($search_type==1)
	{
		if (trim($styleRef)!="") $style_con=" and a.style_ref='$styleRef'";
	}
	else if($search_type==4 || $search_type==0)
	{
		if (trim($styleRef)!="") $style_con=" and a.style_ref like '%$styleRef%' ";
	}
	else if($search_type==2)
	{
		if (trim($styleRef)!="") $style_con=" and a.style_ref like '$styleRef%' ";
	}
	else if($search_type==3)
	{
		if (trim($styleRef)!="") $style_con=" and a.style_ref like '%$styleRef' ";
	}

	if ($sysNo!='') $sys_con=" and a.system_no='$sysNo'"; else $sys_con="";
	if ($buyer_id!=0) $buyer_id_con=" and a.buyer_id='$buyer_id'"; else $buyer_id_con="";
	if($db_type==0)
	{
		$group_con="group_concat(b.lib_sewing_id)  as lib_sewing_id";
		$id_group_con="group_concat(a.id)";
	}
	else
	{
		$group_con="listagg(b.lib_sewing_id,',') within group (order by b.lib_sewing_id) as lib_sewing_id";
		$id_group_con="listagg(a.id,',') within group (order by a.id)";
	}

	$sql="select a.id, a.system_no, a.extention_no, a.style_ref, a.operation_count, a.gmts_item_id, b.operator_smv, b.helper_smv, b.body_part_id, b.lib_sewing_id, c.department_code from ppl_gsd_entry_mst a, ppl_gsd_entry_dtls b, lib_sewing_operation_entry c where a.id=b.mst_id and b.lib_sewing_id=c.id and a.approved=1 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $style_con $buyer_id_con $sys_con order by a.id DESC";

	$sql_result=sql_select($sql);
	foreach($sql_result as $row)
	{
		$smv_dtls_arr[$row[csf('extention_no')]][$row[csf('style_ref')]]['operation_count']=$row[csf('operation_count')];
		$smv_dtls_arr[$row[csf('extention_no')]][$row[csf('style_ref')]]['id'].=$row[csf('id')].',';
		$smv_dtls_arr[$row[csf('extention_no')]][$row[csf('style_ref')]]['system_no'].=$row[csf('system_no')].',';
		$smv_dtls_arr[$row[csf('extention_no')]][$row[csf('style_ref')]]['lib_sewing_id'].=$row[csf('lib_sewing_id')].',';
		$code_id=$code_smv_arr[$row[csf('lib_sewing_id')]]['dcode'];
		$smv=0;
		$smv=$row[csf('operator_smv')]+$row[csf('helper_smv')];
		$smv_sewing_arr[$row[csf('id')]][$row[csf('department_code')]][$row[csf('lib_sewing_id')]]['operator_smv']+=$smv;
	}
	//print_r($smv_sewing_arr[8]);
	?>
	<table width="600" cellspacing="0" cellpadding="0" border="0" rules="all" class="rpt_table " >
        <thead>
            <tr>
                <th width="30">SL</th>
                <th width="120">Sys. ID.</th>
                <th width="50">Ext. NO</th>
                <th width="160">Style</th>
                <th width="60">Avg. Sewing SMV</th>
                <th width="60">Avg. Cuting SMV</th>
                <th width="60">Avg. Finish SMV</th>
                <th>No of Operation</th>
            </tr>
        </thead>
        <tbody id="list_view">
        <?
        $i=1;
		foreach($smv_dtls_arr as $ext_no=>$dataarr)
		{
			foreach($dataarr as $style=>$arrdata)
			{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$lib_sewing_id=rtrim($arrdata['lib_sewing_id'],',');
				$lib_sewing_ids=array_unique(explode(",",$lib_sewing_id));
	
				$finish_smv=$cut_smv=$sewing_smv=0;
	
				$sys_id=rtrim($arrdata['id'],',');
				$ids=array_filter(array_unique(explode(",",$sys_id)));
				//print_r($ids);
				$id_str=""; $k=0;
				foreach($ids as $idstr)
				{
					if($id_str=="") $id_str=$idstr; else $id_str.=','.$idstr;
	
					foreach($lib_sewing_ids as $lsid)
					{
						$finish_smv+=$smv_sewing_arr[$idstr][4][$lsid]['operator_smv'];
						$cut_smv+=$smv_sewing_arr[$idstr][7][$lsid]['operator_smv'];
						$sewing_smv+=$smv_sewing_arr[$idstr][8][$lsid]['operator_smv'];
					}
					$k++;
				}
	
				$system_no=rtrim($arrdata['system_no'],',');
				$system_no=implode(",",array_filter(array_unique(explode(",",$system_no))));
	
				$finish_smv=$finish_smv/$k;
				$cut_smv=$cut_smv/$k;
				$sewing_smv=$sewing_smv/$k;
	
				$datastr=$style;
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" style="cursor:pointer" onClick="js_set_value('<? echo $datastr; ?>')">
					<td width="30"><? echo $i;//.'='.$k ?></td>
					<td width="120" style="word-break:break-all"><? echo $system_no; ?></td>
					<td width="50" style="word-break:break-all"><? echo $ext_no; ?></td>
					<td width="160" style="word-break:break-all"><? echo $style; ?></td>
					<td width="60" align="right"><p><? echo number_format($sewing_smv,2); ?></p></td>
					<td width="60" align="right"><p><? echo number_format($cut_smv,2); ?></p></td>
					<td width="60" align="right"><p><? echo number_format($finish_smv,2); ?></p></td>
					<td><p><? echo $arrdata['operation_count']; ?></p></td>
				</tr>
				<?
				$i++;
			}
		}
        ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3">&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
            </tr>
        </tfoot>
	</table>
	<?
	exit();
}

if($action=="open_set_list_view")
{
	echo load_html_head_contents("Set Entry","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	//echo $set_smv_id.'='.$txt_style_ref;
	?>
	<script>
	function add_break_down_set_tr( i )
	{
		var unit_id= document.getElementById('unit_id').value;
		if(unit_id==1)
		{
			alert('Only One Item');
			return false;	
		}
		var row_num=$('#tbl_set_details tr').length-1;
		if (row_num!=i)
		{
			return false;
		}
		var setsmv='<? echo $set_smv_id ?>';
		//alert(setsmv);
		if(setsmv==3)
		{
			if(form_validation('smv_'+i,'Sew SMV')==false)
			{
				 $('#smv_'+i).focus(); 
				return;
			}
		}
		if (form_validation('cboitem_'+i+'*txtsetitemratio_'+i,'Gmts Items*Set Ratio')==false)
		{
			return;
		}
		else
		{
			i++;
			 $("#tbl_set_details tr:last").clone().find("input,select,a").each(function() {
				$(this).attr({
				  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
				  'name': function(_, name) { return name + i },
				  'value': function(_, value) { return value }              
				});
			  }).end().appendTo("#tbl_set_details");
			  $('#txtsetitemratio_'+i).removeAttr("onChange").attr("onChange","calculate_set_smv("+i+")");
			  $('#cboitem_'+i).removeAttr("onChange").attr("onChange","check_duplicate("+i+",this.id);check_smv_set_popup("+i+")");
			  $('#smv_'+i).removeAttr("onChange").attr("onChange","calculate_set_smv("+i+")");
			  $('#cutsmv_'+i).removeAttr("onChange").attr("onChange","calculate_set_cutsmv("+i+")");
			  $('#finsmv_'+i).removeAttr("onChange").attr("onChange","calculate_set_finsmv("+i+")");
			  $('#increaseset_'+i).removeAttr("onClick").attr("onClick","add_break_down_set_tr("+i+")");
			  $('#decreaseset_'+i).removeAttr("onClick").attr("onClick","fn_delete_down_tr("+i+",'tbl_set_details')");
			  $('#cboitem_'+i).val(''); 
			   $('#smv_'+i).val(''); 
			   $('#cutsmv_'+i).val(''); 
				$('#finsmv_'+i).val(''); 
			  set_sum_value_set( 'tot_set_qnty', 'txtsetitemratio_' );
			  set_sum_value_set( 'tot_smv_qnty', 'smvset_' );
			  set_sum_value_set( 'tot_cutsmv_qnty', 'cutsmvset_' );
			  set_sum_value_set( 'tot_finsmv_qnty', 'finsmvset_' );
		}
	}
	
	function fn_delete_down_tr(rowNo,table_id) 
	{   
		if(table_id=='tbl_set_details')
		{
			var numRow = $('table#tbl_set_details tbody tr').length; 
			if(numRow==rowNo && rowNo!=1)
			{
				$('#tbl_set_details tbody tr:last').remove();
			}
			 
			 set_sum_value_set( 'tot_set_qnty', 'txtsetitemratio_' );
			 set_sum_value_set( 'tot_smv_qnty', 'smvset_' );
			 set_sum_value_set( 'tot_cutsmv_qnty', 'cutsmvset_' );
			 set_sum_value_set( 'tot_finsmv_qnty', 'finsmvset_' );
		}
	}
	
	function check_duplicate(id,td)
	{
		var item_id=(document.getElementById('cboitem_'+id).value);
		var row_num=$('#tbl_set_details tr').length-1;
		$('#smv_'+id).val('');
		$('#cutsmv_'+id).val('');
		$('#finsmv_'+id).val('');
			
		for (var k=1;k<=row_num; k++)
		{
			if(k==id)
			{
				continue;
			}
			else
			{
				if(item_id==document.getElementById('cboitem_'+k).value)
				{
					alert("Same Gmts Item Duplication Not Allowed.");
					document.getElementById(td).value="0";
					document.getElementById(td).focus();
				}
			}
		}
	}
	
	function check_smv_set(id)
	{
		var smv=(document.getElementById('smv_'+id).value);
		var row_num=$('#tbl_set_details tr').length-1;
		//alert(item_id);
		var txt_style_ref='<? echo $txt_style_ref ?>'
		var set_smv_id='<? echo $set_smv_id ?>'
		var item_id=$('#cboitem_'+id).val();
		//alert(td);
		//get_php_form_data(company_id,'set_smv_work_study','requires/woven_order_entry_controller' );
		var response=return_global_ajax_value(txt_style_ref+"**"+item_id, 'set_smv_work_study', '', 'woven_order_entry_controller');
		var response=response.split("_");
		if(response[0]==1)
		{
			if(set_smv_id==1)
			{
				$('#smv_'+id).val(response[1]);
				$('#tot_smv_qnty').val(response[1]);
			}
		}
	}
	function check_smv_set_popup(id)
	{
		var smv=(document.getElementById('smv_'+id).value);
		var row_num=$('#tbl_set_details tr').length-1;
	
		var txt_style_ref='<? echo $txt_style_ref ?>';
		var cbo_company_name='<? echo $cbo_company_name ?>';
		var cbo_buyer_name='<? echo $cbo_buyer_name ?>';
		var item_id=$('#cboitem_'+id).val();
			//alert(cbo_company_name);
		var set_smv_id='<? echo $set_smv_id ?>';
		
		if(set_smv_id==3 || set_smv_id==8)
		{
			$('#smv_'+id).val('');
			$('#cutsmv_'+id).val('');
			$('#finsmv_'+id).val('');
			
			$('#tot_smv_qnty').val('');
			$('#tot_cutsmv_qnty').val('');
			$('#tot_finsmv_qnty').val('');
			
			$('#hidquotid_'+id).val('');
			
			var page_link="woven_order_entry_controller.php?action=open_smv_list&txt_style_ref="+txt_style_ref+"&set_smv_id="+set_smv_id+"&item_id="+item_id+"&id="+id+"&cbo_company_name="+cbo_company_name+"&cbo_buyer_name="+cbo_buyer_name;
		}
		else
		{
			return;
		}
	
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, 'SMV Pop Up', 'width=650px,height=220px,center=1,resize=1,scrolling=0','../../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var selected_smv_data=this.contentDoc.getElementById("selected_smv").value;
			var smv_data=selected_smv_data.split("_");
			var row_id=smv_data[3];
			
			$("#smv_"+row_id).val(smv_data[0]);
			$("#smv_"+row_id).attr('readonly','readonly');
			$("#cutsmv_"+row_id).val(smv_data[1]);
			$("#cutsmv_"+row_id).attr('readonly','readonly');
			$("#finsmv_"+row_id).val(smv_data[2]);
			$("#finsmv_"+row_id).attr('readonly','readonly');
			$("#hidquotid_"+row_id).val(smv_data[4]);
			calculate_set_smv(row_id);
		}	
	}

	function calculate_set_smv(i){
		var txtsetitemratio=document.getElementById('txtsetitemratio_'+i).value;
		var smv=document.getElementById('smv_'+i).value;
		var set_smv=txtsetitemratio*smv;
		document.getElementById('smvset_'+i).value=set_smv;
		
		set_sum_value_set( 'tot_set_qnty', 'txtsetitemratio_' );
		set_sum_value_set( 'tot_smv_qnty', 'smvset_' );
		
		calculate_set_cutsmv(i);
		calculate_set_finsmv(i);
	}
	
	function calculate_set_cutsmv(i){
		var txtsetitemratio=document.getElementById('txtsetitemratio_'+i).value;
		var smv=document.getElementById('cutsmv_'+i).value;
		var set_smv=txtsetitemratio*smv;
		document.getElementById('cutsmvset_'+i).value=set_smv;
		set_sum_value_set( 'tot_set_qnty', 'txtsetitemratio_' );
		set_sum_value_set( 'tot_cutsmv_qnty', 'cutsmvset_' );
	}
	
	function calculate_set_finsmv(i){
		var txtsetitemratio=document.getElementById('txtsetitemratio_'+i).value;
		var smv=document.getElementById('finsmv_'+i).value;
		var set_smv=txtsetitemratio*smv;
		document.getElementById('finsmvset_'+i).value=set_smv;
		set_sum_value_set( 'tot_set_qnty', 'txtsetitemratio_' );
		set_sum_value_set( 'tot_finsmv_qnty', 'finsmvset_' );
	}
	
	function set_sum_value_set(des_fil_id,field_id)
	{
		var rowCount = $('#tbl_set_details tr').length-1;
		if(des_fil_id=="tot_set_qnty")
		{
			math_operation( des_fil_id, field_id, '+', rowCount );
		}
		if(des_fil_id=="tot_smv_qnty")
		{
			var ddd={ dec_type:1, comma:0, currency:1}
			math_operation( des_fil_id, field_id, '+', rowCount,ddd );
		}
		if(des_fil_id=="tot_cutsmv_qnty")
		{
			var ddd={ dec_type:1, comma:0, currency:1}
			math_operation( des_fil_id, field_id, '+', rowCount,ddd );
		}
		if(des_fil_id=="tot_finsmv_qnty")
		{
			var ddd={ dec_type:1, comma:0, currency:1}
			math_operation( des_fil_id, field_id, '+', rowCount,ddd );
		}
	}
	
	function js_set_value_set()
	{
		var rowCount = $('#tbl_set_details tr').length-1;
		var set_breck_down="";
		var item_id=""
		for(var i=1; i<=rowCount; i++)
		{
			if (form_validation('cboitem_'+i+'*txtsetitemratio_'+i+'*smv_'+i,'Gmts Items*Set Ratio')==false)
			{
				return;
			}
			var smv =document.getElementById('smv_'+i).value;
			if(smv==0)
			{
				alert("Smv 0 not accepted");
				return;
			}
			if($('#hidquotid_'+i).val()=='') $('#hidquotid_'+i).val(0)
			
			if($('#cutsmv_'+i).val()=='') $('#cutsmv_'+i).val(0)
			if($('#cutsmvset_'+i).val()=='') $('#cutsmvset_'+i).val(0)
			if($('#finsmv_'+i).val()=='') $('#finsmv_'+i).val(0)
			if($('#finsmvset_'+i).val()=='') $('#finsmvset_'+i).val(0)
			if($('#printseq_'+i).val()=='') $('#printseq_'+i).val(1)
			if($('#embroseq_'+i).val()=='') $('#embroseq_'+i).val(2)
			if($('#washseq_'+i).val()=='') $('#washseq_'+i).val(3)
			if($('#spworksseq_'+i).val()=='') $('#spworksseq_'+i).val(4)
			if($('#gmtsdyingseq_'+i).val()=='') $('#gmtsdyingseq_'+i).val(5)
			if($('#aopseq_'+i).val()=='') $('#aopseq_'+i).val(6)
			
			
			if(set_breck_down=="")
			{
				set_breck_down+=$('#cboitem_'+i).val()+'_'+$('#txtsetitemratio_'+i).val()+'_'+$('#smv_'+i).val()+'_'+$('#smvset_'+i).val()+'_'+$('#complexity_'+i).val()+'_'+$('#emblish_'+i).val()+'_'+$('#cutsmv_'+i).val()+'_'+$('#cutsmvset_'+i).val()+'_'+$('#finsmv_'+i).val()+'_'+$('#finsmvset_'+i).val()+'_'+$('#printseq_'+i).val()+'_'+$('#embro_'+i).val()+'_'+$('#embroseq_'+i).val()+'_'+$('#wash_'+i).val()+'_'+$('#washseq_'+i).val()+'_'+$('#spworks_'+i).val()+'_'+$('#spworksseq_'+i).val()+'_'+$('#gmtsdying_'+i).val()+'_'+$('#gmtsdyingseq_'+i).val()+'_'+$('#hidquotid_'+i).val()+'_'+$('#aop_'+i).val()+'_'+$('#aopseq_'+i).val();
				item_id+=$('#cboitem_'+i).val();
			}
			else
			{
				set_breck_down+="__"+$('#cboitem_'+i).val()+'_'+$('#txtsetitemratio_'+i).val()+'_'+$('#smv_'+i).val()+'_'+$('#smvset_'+i).val()+'_'+$('#complexity_'+i).val()+'_'+$('#emblish_'+i).val()+'_'+$('#cutsmv_'+i).val()+'_'+$('#cutsmvset_'+i).val()+'_'+$('#finsmv_'+i).val()+'_'+$('#finsmvset_'+i).val()+'_'+$('#printseq_'+i).val()+'_'+$('#embro_'+i).val()+'_'+$('#embroseq_'+i).val()+'_'+$('#wash_'+i).val()+'_'+$('#washseq_'+i).val()+'_'+$('#spworks_'+i).val()+'_'+$('#spworksseq_'+i).val()+'_'+$('#gmtsdying_'+i).val()+'_'+$('#gmtsdyingseq_'+i).val()+'_'+$('#hidquotid_'+i).val()+'_'+$('#aop_'+i).val()+'_'+$('#aopseq_'+i).val();
				item_id+=","+$('#cboitem_'+i).val();
			}
		}
		
		document.getElementById('set_breck_down').value=set_breck_down;
		document.getElementById('item_id').value=item_id;
		parent.emailwindow.hide();
	}
	
	function open_emblishment_pop_up(i)
	{ 
		var page_link="woven_order_entry_controller.php?action=open_emblishment_list";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, pcs_or_set, 'width=620px,height=300px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var set_breck_down=this.contentDoc.getElementById("set_breck_down");
			var item_id=this.contentDoc.getElementById("item_id");
			var tot_set_qnty=this.contentDoc.getElementById("tot_set_qnty");
			var tot_smv_qnty=this.contentDoc.getElementById("tot_smv_qnty");
			document.getElementById('set_breck_down').value=set_breck_down.value;
			document.getElementById('item_id').value=item_id.value;
			document.getElementById('tot_set_qnty').value=tot_set_qnty.value;
			document.getElementById('tot_smv_qnty').value=tot_smv_qnty.value;
		}		
	}
	</script>
	</head>
	<body>
       <div id="set_details"  align="center">            
    	<fieldset>
         <?  
		 $sql_smv="select  upper(style_ref) as style_ref,gmts_item_id,total_smv from ppl_gsd_entry_mst where status_active=1 and is_deleted=0";
		 $sql_result=sql_select($sql_smv);$set_smv_arr=array();
		 foreach($sql_result as $row)
		 {
			$set_smv_arr[$row[csf('style_ref')]][$row[csf('gmts_item_id')]]+=$row[csf('total_smv')];
		 }
		 //echo "select current_approval_status from ";
		 $other_cost_approved=return_field_value("current_approval_status","co_com_pre_costing_approval","job_no='$txt_job_no' and entry_form=15 and cost_component_id=12");
		 // echo $other_cost_approved.'='.$txt_job_no.'='.$precostapproved;
		 $disabled=0;
		 if($precostapproved==0 )
		 {
			 if($other_cost_approved==1)
			 {
				 echo '<p style="color:#FF0000;">Pre Cost Others Cost Approved, Any Change not allowed.</P>';
				 $disab="disabled";
				 $disabled=1;
			 }
			 else if($precostfound >0 ){ 
				 echo "Pre Cost Found, only Sew. and Cut. SMV Change allowed";
				 $disab="";
				 $disabled=1;
			 }
			 else $disabled=0;
		 }
		 else if($precostapproved==1 ) 
		 {
			 echo '<p style="color:#FF0000;">Pre Cost Approved, Any Change not allowed.</P>';
			 $disab="disabled";
			 $disabled=1;
		 }
		 else $disab="";
		 
		 if($set_smv_id==2 || $set_smv_id==3 || $set_smv_id==8) $readonly="readonly"; else $readonly=""; //pq 2, ws 3, ws 8
		 ?>
        <form id="setdetails_1" autocomplete="off">
            <input type="hidden" id="set_breck_down" />     
            <input type="hidden" id="item_id"  />  
            <input type="hidden" id="unit_id" value="<? echo $unit_id;  ?>" />        	
            <table width="1100" cellspacing="0" class="rpt_table" border="0" id="tbl_set_details" rules="all">
                <thead>
                    <tr>
                    	<th width="150">Item</th><th width="40">Set Ratio</th><th width="40">Sew SMV/ Pcs</th><th width="40">Cut SMV/ Pcs</th><th width="40">Fin SMV/ Pcs</th><th width="80">Complexity</th><th width="100">Print</th><th width="100">Embro</th><th width="100">Wash</th><th width="100">SP. Works</th><th width="100">Gmts Dyeing</th><th width="100">AOP</th><th width=""></th>
                    </tr>
                </thead>
                <tbody>
                    <?
                    $smv_arr=array();
                    $job_no="'".$txt_job_no."'";
                    $sql_d=sql_select('Select gmts_item_id AS "gmts_item_id",set_item_ratio AS "set_item_ratio",smv_pcs AS "smv_pcs",smv_set AS "smv_set",complexity AS "complexity",embelishment AS "embelishment", cutsmv_pcs AS "cutsmv_pcs", cutsmv_set AS "cutsmv_set", finsmv_pcs AS "finsmv_pcs", finsmv_set AS "finsmv_set",printseq AS "printseq",embro AS "embro",embroseq AS "embroseq",wash AS "wash",washseq AS "washseq",spworks AS "spworks",spworksseq AS "spworksseq",gmtsdying AS "gmtsdying",gmtsdyingseq AS "gmtsdyingseq", quot_id as "quot_id", aop as "aop", aopseq as "aopseq", ws_id as "ws_id" from wo_po_details_mas_set_details where job_no='.$job_no.' order by id'); 

                    foreach($sql_d as $sql_r){
                        if($sql_r['gmts_item_id']=="") $sql_r['gmts_item_id']=0;
                        if($sql_r['set_item_ratio']=="") $sql_r['set_item_ratio']=0;
                        if($sql_r['smv_pcs']=="")
                        {
                            $sql_r['smv_pcs']=0;
                            $sql_r['smv_set']=0;
                        }
                        if($sql_r['complexity']=="") $sql_r['complexity']=0;
                        if($sql_r['embelishment']=="") $sql_r['embelishment']=0;
                        if($sql_r['cutsmv_pcs']=="")
                        {
                            $sql_r['cutsmv_pcs']=0;
                            $sql_r['cutsmv_set']=0;
                        }
                        if($sql_r['finsmv_pcs']=="")
                        {
                            $sql_r['finsmv_pcs']=0;
                            $sql_r['finsmv_set']=0;
                        }
                        if($sql_r['printseq']=="") $sql_r['printseq']=0;
                        if($sql_r['embro']=="") $sql_r['embro']=0;
                        if($sql_r['embroseq']=="") $sql_r['embroseq']=0;
                        
                        if($sql_r['wash']=="")$sql_r['wash']=0;
                        if($sql_r['washseq']=="")$sql_r['washseq']=0;
                        
                        if($sql_r['spworks']=="") $sql_r['spworks']=0;
                        if($sql_r['spworksseq']=="") $sql_r['spworksseq']=0;
                        
                        if($sql_r['gmtsdying']=="") $sql_r['gmtsdying']=0;
                        if($sql_r['gmtsdyingseq']=="")$sql_r['gmtsdyingseq']=0;
                        //if($sql_r['quot_id']=="") $sql_r['quot_id']=0;
						if($sql_r['ws_id']=="") $sql_r['ws_id']=0;
						if($sql_r['aop']=="") $sql_r['aop']=0;
                        if($sql_r['aopseq']=="") $sql_r['aopseq']=0;
                        
                        $sql_r=removenumeric($sql_r);
                        $smv_arr[]=implode("_",$sql_r);
                    }
                    $smv_srt=rtrim(implode("__",$smv_arr),"__");
                    if(count($sql_d)){
                        $set_breck_down=$smv_srt;
                    }
                    //echo count($sql_d)."hhh".$set_breck_down;
                    $data_array=explode("__",$set_breck_down);
                    if($data_array[0]=="")
                    {
                        $data_array=array();
                    }
                    if ( count($data_array)>0)
                    {
                        $i=0;
                        foreach( $data_array as $row )
                        {
                            $i++;
                            $data=explode('_',$row);
                            $tot_cutsmv_qnty+=$data[6];
                            $tot_finsmv_qnty+=$data[8];
							if ($set_smv_id==4 || $set_smv_id==6) { if($data[19]=="") $data[19]=$data[4]; }
                            ?>
                            <tr id="settr_1" align="center">
                                <td><? echo create_drop_down( "cboitem_".$i, 150, $garments_item, "",1," -- Select Item --", $data[0], "check_duplicate(".$i.",this.id );check_smv_set_popup(".$i.");",$disabled,'' ); ?></td>
                                <td><input type="text" id="txtsetitemratio_<? echo $i;?>"   name="txtsetitemratio_<? echo $i;?>" style="width:30px"  class="text_boxes_numeric" onChange="calculate_set_smv(<? echo $i;?>)" value="<? echo $data[1]; ?>" <? if ($unit_id==1){echo "readonly";} else{echo "";}?> <? if ($disabled==1){echo "disabled";}else{ echo "";} ?>  /></td>
                                <td>
                                    <input type="text" id="smv_<? echo $i;?>"   name="smv_<? echo $i;?>" style="width:30px"  class="text_boxes_numeric" onChange="calculate_set_smv(<? echo $i;?>)"  value="<? echo $data[2] ?>" <? echo $disab; ?>  /> 
                                    <input type="hidden" id="smvset_<? echo $i;?>" name="smvset_<? echo $i;?>" style="width:30px"  class="text_boxes_numeric"  value="<? echo $data[3] ?>" readonly/> 
                                </td>
                                <td>
                                    <input type="text" id="cutsmv_<? echo $i;?>"   name="cutsmv_<? echo $i;?>" style="width:30px"  class="text_boxes_numeric" onChange="calculate_set_cutsmv(<? echo $i;?>)"  value="<? echo $data[6] ?>" <? echo $disab; ?> /> 
                                    <input type="hidden" id="cutsmvset_<? echo $i;?>"   name="cutsmvset_<? echo $i;?>" style="width:30px"  class="text_boxes_numeric"  value="<? echo $data[7] ?>" readonly/> 
                                </td>
                                <td>
                                    <input type="text" id="finsmv_<? echo $i;?>"   name="finsmv_<? echo $i;?>" style="width:30px"  class="text_boxes_numeric" onChange="calculate_set_finsmv(<? echo $i;?>)"  value="<? echo $data[8] ?>" <? if ($disabled==1){echo "disabled";}else{ echo "";} echo $readonly; ?> /> 
                                    <input type="hidden" id="finsmvset_<? echo $i;?>"   name="finsmvset_<? echo $i;?>" style="width:30px"  class="text_boxes_numeric"  value="<? echo $data[9] ?>" readonly/> 
                                </td>
                                <td><? echo create_drop_down( "complexity_".$i, 80, $complexity_level, "",1," -- Select --", $data[4], "",$disabled,'' ); ?></td>
                                <td><? echo create_drop_down( "emblish_".$i, 60, $yes_no, "",1," -- Select--", $data[5], "",$disabled,'' ); ?>
                                    <input type="text" id="printseq_<? echo $i;?>"   name="printseq_<? echo $i;?>" style="width:20px"  class="text_boxes_numeric"  value="<? echo $data[10] ?>" <? if ($disabled==1){echo "disabled";}else{ echo "";} ?> /> 
                                </td>
                                <td><? echo create_drop_down( "embro_".$i, 60, $yes_no, "",1," -- Select--", $data[11], "",$disabled,'' ); ?>
                                    <input type="text" id="embroseq_<? echo $i;?>"   name="embroseq_<? echo $i;?>" style="width:20px"  class="text_boxes_numeric"  value="<? echo $data[12] ?>" <? if ($disabled==1){echo "disabled";}else{ echo "";} ?>/> 
                                </td>
                                <td><? echo create_drop_down( "wash_".$i, 60, $yes_no, "",1," -- Select--", $data[13], "",$disabled,'' ); ?>
                                    <input type="text" id="washseq_<? echo $i;?>"   name="washseq_<? echo $i;?>" style="width:20px"  class="text_boxes_numeric"  value="<? echo $data[14] ?>" <? if ($disabled==1){echo "disabled";}else{ echo "";} ?>/> 
                                </td>
                                <td><? echo create_drop_down( "spworks_".$i, 60, $yes_no, "",1," -- Select--", $data[15], "",$disabled,'' ); ?>
                                    <input type="text" id="spworksseq_<? echo $i;?>"   name="spworksseq_<? echo $i;?>" style="width:20px"  class="text_boxes_numeric"  value="<? echo $data[16] ?>" <? if ($disabled==1){echo "disabled";}else{ echo "";} ?>/> 
                                </td>
                                <td><? echo create_drop_down( "gmtsdying_".$i, 60, $yes_no, "",1," -- Select--", $data[17], "",$disabled,'' ); ?>
                                    <input type="text" id="gmtsdyingseq_<? echo $i;?>"   name="gmtsdyingseq_<? echo $i;?>" style="width:20px"  class="text_boxes_numeric"  value="<? echo $data[18] ?>" <? if ($disabled==1){echo "disabled";}else{ echo "";} ?>/> 
                                </td>
                                <td><? echo create_drop_down( "aop_".$i, 60, $yes_no, "",1," -- Select--", $data[20], "",$disabled,'' ); ?>
                                    <input type="text" id="aopseq_<? echo $i;?>"   name="aopseq_<? echo $i;?>" style="width:20px"  class="text_boxes_numeric"  value="<? echo $data[21] ?>" <? if ($disabled==1){echo "disabled";}else{ echo "";} ?>/> 
                                </td>
                                <td>
                                    <input type="hidden" id="hidquotid_<? echo $i;?>" name="hidquotid_<? echo $i;?>" style="width:30px" class="text_boxes_numeric" value="<? echo $data[19]; ?>" readonly/>
                                    <input type="button" id="increaseset_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_set_tr(<? echo $i; ?> )" <? if ($disabled==1){echo "disabled";}else{ echo "";} ?>/>
                                    <input type="button" id="decreaseset_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_delete_down_tr(<? echo $i; ?> ,'tbl_set_details' );" <? if ($disabled==1){echo "disabled";}else{ echo "";} ?>/>
                                </td> 
                            </tr>
                            <?
                        }
                    }
                    else
                    {
                        ?>
                        <tr id="settr_1" align="center">
                            <td><? echo create_drop_down( "cboitem_1", 150, $garments_item, "",1,"--Select--", 0, "check_duplicate(1,this.id );check_smv_set_popup(1);",'','' ); ?></td>
                            <td><input type="text" id="txtsetitemratio_1" name="txtsetitemratio_1" style="width:30px" class="text_boxes_numeric" onChange="calculate_set_smv(1)"  value="<? if ($unit_id==1){echo "1";} else{echo "";}?>" /></td>
                            <td>
                                <input type="text" id="smv_1" name="smv_1" style="width:30px" class="text_boxes_numeric" onChange="calculate_set_smv(1)" value="0" <? echo $readonly ?>  /> 
                                <input type="hidden" id="smvset_1" name="smvset_1" style="width:30px" class="text_boxes_numeric" /> 
                            </td>
                            <td>
                                <input type="text" id="cutsmv_1" name="cutsmv_1" style="width:30px" class="text_boxes_numeric" onChange="calculate_set_cutsmv(1)" value="0" /> 
                                <input type="hidden" id="cutsmvset_1" name="cutsmvset_1" style="width:30px" class="text_boxes_numeric" /> 
                            </td>
                            <td>
                                <input type="text" id="finsmv_1" name="finsmv_1" style="width:30px" class="text_boxes_numeric" onChange="calculate_set_finsmv(1)" value="0" /> 
                                <input type="hidden" id="finsmvset_1" name="finsmvset_1" style="width:30px" class="text_boxes_numeric" /> 
                            </td>
                            <td><? echo create_drop_down( "complexity_1", 80, $complexity_level, "",1," -- Select --", 0, "",'','' ); ?></td>
                            <td><? echo create_drop_down( "emblish_1", 60, $yes_no, "",1," -- Select --", 0, "",'','' ); ?>
                                <input type="text" id="printseq_1"   name="printseq_1" style="width:20px" class="text_boxes_numeric" value="" /> 
                            </td>
                            <td><? echo create_drop_down( "embro_1", 60, $yes_no, "",1," -- Select--", '', "",$disabled,'' ); ?>
                                <input type="text" id="embroseq_1"   name="embroseq_1" style="width:20px" class="text_boxes_numeric" value="" /> 
                            </td>
                            <td><? echo create_drop_down( "wash_1", 60, $yes_no, "",1," -- Select--", '', "",$disabled,'' ); ?>
                                <input type="text" id="washseq_1"   name="washseq_1" style="width:20px" class="text_boxes_numeric" value="" /> 
                            </td>
                            <td><? echo create_drop_down( "spworks_1", 60, $yes_no, "",1," -- Select--", '', "",$disabled,'' ); ?>
                                <input type="text" id="spworksseq_1"   name="spworksseq_1" style="width:20px" class="text_boxes_numeric" value="" /> 
                            </td>
                            <td><? echo create_drop_down( "gmtsdying_1", 60, $yes_no, "",1," -- Select--", '', "",$disabled,'' ); ?>
                                <input type="text" id="gmtsdyingseq_1"   name="gmtsdyingseq_1" style="width:20px" class="text_boxes_numeric" value="" /> 
                            </td>
                            <td><? echo create_drop_down( "aop_1", 60, $yes_no, "",1," -- Select--", '', "",$disabled,'' ); ?>
                                <input type="text" id="aopseq_1"   name="aopseq_1" style="width:20px" class="text_boxes_numeric" value="" /> 
                            </td>
                            <td>
                                <input type="hidden" id="hidquotid_1" name="hidquotid_1" style="width:30px" class="text_boxes_numeric" value="" readonly/>
                                <input type="button" id="increaseset_1" style="width:30px" class="formbutton" value="+" onClick="add_break_down_set_tr(1)" />
                                <input type="button" id="decreaseset_1" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_delete_down_tr(1 ,'tbl_set_details' );" />
                            </td> 
                        </tr>
                    <? 
                    } 
                    ?>
                </tbody>
            </table>
            <table width="1100" cellspacing="0" class="rpt_table" border="0" rules="all">
                <tfoot>
                    <tr>
                        <th width="150">Total</th>
                        <th width="40">
                            <input type="text" id="tot_set_qnty" name="tot_set_qnty" class="text_boxes_numeric" style="width:30px"  value="<? if($tot_set_qnty !=''){ echo $tot_set_qnty;} else{ echo 1;} ?>" readonly />
                        </th>
                         <th width="40">
                            <input type="text" id="tot_smv_qnty" name="tot_smv_qnty" class="text_boxes_numeric" style="width:30px"  value="<? if($tot_smv_qnty !=''){ echo $tot_smv_qnty;} else{ echo 0;} ?>" readonly />
                        </th>
                         <th width="40">
                            <input type="text" id="tot_cutsmv_qnty" name="tot_cutsmv_qnty" class="text_boxes_numeric" style="width:30px"  value="<? if($tot_cutsmv_qnty !=''){ echo $tot_cutsmv_qnty;} else{ echo 0;} ?>" readonly />
                        </th>
                        <th width="40">
                            <input type="text" id="tot_finsmv_qnty" name="tot_finsmv_qnty" class="text_boxes_numeric" style="width:30px"  value="<? if($tot_finsmv_qnty !=''){ echo $tot_finsmv_qnty;} else{ echo 0;} ?>" readonly />
                        </th>
                        <th>&nbsp;</th>
                    </tr>
                </tfoot>
            </table>
            <table width="1100" cellspacing="0" class="" border="0">
                <tr>
                    <td align="center" width="100%" class="button_container"><input type="button" class="formbutton" value="Close" onClick="js_set_value_set()"/></td> 
                </tr>
            </table>
        </form>
    </fieldset>
    </div>
    </body>   
    <script>
		set_sum_value_set( 'tot_set_qnty', 'txtsetitemratio_' );
		set_sum_value_set( 'tot_smv_qnty', 'smvset_' );
		set_sum_value_set( 'tot_cutsmv_qnty', 'cutsmvset_' );
		set_sum_value_set( 'tot_finsmv_qnty', 'finsmvset_' );
    </script>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if($action=="booking_no_with_approved_status")
{
	$data=explode("_",$data);
	if($data[1]==""){
		$sql="select booking_no,is_approved from wo_booking_mst where job_no='$data[0]' and booking_type=1 and is_short=2 and is_deleted=0 and status_active=1";
	}
	else{
		 $sql="select a.booking_no,a.is_approved from wo_booking_mst a, wo_booking_dtls b where a.job_no=b.job_no and a.booking_no=b.booking_no and  a.job_no='$data[0]' and a.booking_type=1 and a.is_short=2 and b.po_break_down_id=$data[1] and a.is_deleted=0 and a.status_active=1 group by a.booking_no,is_approved";
	}
	$approved_booking="";
	$un_approved_booking="";
	$sql_booking=sql_select($sql);
	foreach($sql_booking as $row){
		if($row[csf('is_approved')]==1){
		  $approved_booking.=$row[csf('booking_no')].", ";	
		}
		else{
		  $un_approved_booking.=$row[csf('booking_no')].", ";	
		}
	}
	echo rtrim($approved_booking ,", ")."_".rtrim($un_approved_booking , ", ");
}

if($action=="check_precost")
{
	$sql_data=sql_select("select count(a.id) as id, a.approved, c.order_uom from  wo_pre_cost_mst a, wo_pre_cost_fabric_cost_dtls b, wo_po_details_master c where a.job_no=b.job_no and a.job_no=c.job_no and a.job_no='$data' and a.is_deleted=0 and  a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.job_no, a.approved,c.order_uom");
	$id=0; $order_uom=0; $is_approved=0;
	foreach($sql_data as $row)
	{
		$id=$row[csf('id')];
		$order_uom=$row[csf('order_uom')];
		if($row[csf('approved')]==1) $is_approved=1;
	}
	echo trim($id)."_".trim($order_uom)."_".trim($is_approved);
	die;
}

if ($action=="save_update_delete_mst")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$sql=sql_select("select cost_control_source from variable_order_tracking where company_name=$cbo_company_name and variable_list=53 order by id");
	$cost_control_source=$sql[0][csf('cost_control_source')];
	
	if(str_replace("'","",$txt_quotation_id)=="") $quotation_id=0; else $quotation_id=str_replace("'","",$txt_quotation_id);
	// Insert Here----------------------------------------------------------
	if ($operation==0) 
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$id=return_next_id( "id", "wo_po_details_master", 1 ) ;
		//echo "select job_no_prefix,job_no_prefix_num from wo_po_details_master where company_name=$cbo_company_name and YEAR('Y',insert_date)=".date('Y',time())." order by job_no_prefix_num desc"; die; 
		if($db_type==0)$yearCond="and YEAR(insert_date)"; else if($db_type==2) $yearCond="and to_char(insert_date,'YYYY')";
		$new_job_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', '', date("Y",time()), 5, "select job_no_prefix,job_no_prefix_num from wo_po_details_master where company_name=$cbo_company_name $yearCond=".date('Y',time())." order by job_no_prefix_num desc ", "job_no_prefix", "job_no_prefix_num" ));
		
		$field_array="id,garments_nature,quotation_id,job_no,job_no_prefix,job_no_prefix_num,company_name,buyer_name,location_name,style_ref_no,repeat_job_no,style_description,product_dept,product_code,pro_sub_dep,currency_id,agent_name,client_id,order_repeat_no,region,product_category,team_leader,dealing_marchant,bh_merchant,packing,remarks,ship_mode,order_uom,gmts_item_id,set_break_down, total_set_qnty,set_smv,season_buyer_wise,factory_marchant,style_owner,working_location_id,design_source_id,qlty_label,is_deleted,status_active,inserted_by,insert_date";
		//txt_repeat_job_no cbo_design_source_id
		$data_array="(".$id.",".$garments_nature.",'".$quotation_id."','".$new_job_no[0]."','".$new_job_no[1]."','".$new_job_no[2]."',".$cbo_company_name.",".$cbo_buyer_name.",".$cbo_location_name.",".$txt_style_ref.",".$txt_repeat_job_no.",".$txt_style_description.",".$cbo_product_department.",".$txt_product_code.",".$cbo_sub_dept.",".$cbo_currercy.",".$cbo_agent.",".$cbo_client.",".$txt_repeat_no.",".$cbo_region.",".$txt_item_catgory.",".$cbo_team_leader.",".$cbo_dealing_merchant.",".$txt_bhmerchant.",".$cbo_packing.",".$txt_remarks.",".$cbo_ship_mode.",".$cbo_order_uom.",".$item_id.",".$set_breck_down.",".$tot_set_qnty.",".$tot_smv_qnty.",".$cbo_season_name.",".$cbo_factory_merchant.",".$cbo_working_company_id.",".$cbo_working_location_id.",".$cbo_design_source_id.",".$cbo_qltyLabel.",0,1,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		//$('#cboitem_'+i).val()+'_'+$('#txtsetitemratio_'+i).val()+'_'+$('#smv_'+i).val()+'_'+$('#smvset_'+i).val()+'_'+$('#complexity_'+i).val()+'_'+$('#emblish_'+i).val()+'_'+$('#cutsmv_'+i).val()+'_'+$('#cutsmvset_'+i).val()+'_'+$('#finsmv_'+i).val()+'_'+$('#finsmvset_'+i).val()+'_'+$('#printseq_'+i).val()+'_'+$('#embro_'+i).val()+'_'+$('#embroseq_'+i).val()+'_'+$('#wash_'+i).val()+'_'+$('#washseq_'+i).val()+'_'+$('#spworks_'+i).val()+'_'+$('#spworksseq_'+i).val()+'_'+$('#gmtsdying_'+i).val()+'_'+$('#gmtsdyingseq_'+i).val();
		
		$field_array1="id, job_id,job_no, gmts_item_id, set_item_ratio, smv_pcs, smv_set, smv_pcs_precost, smv_set_precost, complexity, embelishment, cutsmv_pcs, cutsmv_set, finsmv_pcs, finsmv_set, printseq, embro, embroseq, wash, washseq, spworks, spworksseq, gmtsdying, gmtsdyingseq, ws_id, aop, aopseq";
		$add_comma=0;
		$id1=return_next_id( "id", "wo_po_details_mas_set_details", 1 ) ;
		$set_breck_down_array=explode('__',str_replace("'",'',$set_breck_down));
		for($c=0;$c < count($set_breck_down_array);$c++)
		{
			$set_breck_down_arr=explode('_',$set_breck_down_array[$c]);
			if ($add_comma!=0) $data_array1 .=",";
			if($set_breck_down_arr[2]==0 || $set_breck_down_arr[2]==''){
				echo "SMV**";
				disconnect($con);die;
			}
			$data_array1 .="(".$id1.",".$id.",'".$new_job_no[0]."','".$set_breck_down_arr[0]."','".$set_breck_down_arr[1]."','".$set_breck_down_arr[2]."','".$set_breck_down_arr[3]."','".$set_breck_down_arr[2]."','".$set_breck_down_arr[3]."','".$set_breck_down_arr[4]."','".$set_breck_down_arr[5]."','".$set_breck_down_arr[6]."','".$set_breck_down_arr[7]."','".$set_breck_down_arr[8]."','".$set_breck_down_arr[9]."','".$set_breck_down_arr[10]."','".$set_breck_down_arr[11]."','".$set_breck_down_arr[12]."','".$set_breck_down_arr[13]."','".$set_breck_down_arr[14]."','".$set_breck_down_arr[15]."','".$set_breck_down_arr[16]."','".$set_breck_down_arr[17]."','".$set_breck_down_arr[18]."','".$set_breck_down_arr[19]."','".$set_breck_down_arr[20]."','".$set_breck_down_arr[21]."')";
			$add_comma++;
			$id1=$id1+1;
		}
		$flag=1;
		//echo "10**INSERT INTO wo_po_details_master (".$field_array.") VALUES ".$data_array.""; die;
		$rID=sql_insert("wo_po_details_master",$field_array,$data_array,0);
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		$rID1=sql_insert("wo_po_details_mas_set_details",$field_array1,$data_array1,1);
		if($rID1==1 && $flag==1) $flag=1; else $flag=0;
		$job_id=$id;
		if($cost_control_source==1)
		{
			if($quotation_id!=0)
			{
				$query="UPDATE qc_confirm_mst SET job_id=$job_id WHERE cost_sheet_id=$quotation_id and status_active=1 and is_deleted=0";
				$rIDt=execute_query($query,1);
				if($rIDt==1 && $flag==1) $flag=1; else $flag=0;
			}
		}
		//echo "10**".$rID." &&". $rID1; die;
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "0**".$new_job_no[0]."**".$rID."**".str_replace("'",'',$id);
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
				echo "0**".$new_job_no[0]."**".$rID."**".str_replace("'",'',$id);
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
	else if ($operation==1) // Update Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$sql_data=sql_select("select approved from wo_pre_cost_mst where job_no=$txt_job_no and is_deleted=0 and status_active=1");
		$isapproved=$sql_data[0][csf("approved")];
		if($isapproved==1)
		{
			echo "16**Pre Cost Approved, Any Change will be not allowed.";
			disconnect($con);die;
		}
		
		$PrevData=sql_select("select style_ref_no,gmts_item_id from wo_po_details_master where job_no=$txt_job_no");
		$PrevStyleRefNo=$PrevData[0][csf('style_ref_no')];
		$PrevGmtsItemId=$PrevData[0][csf('gmts_item_id')];
		$field_array="quotation_id*buyer_name*location_name*style_ref_no*repeat_job_no*style_description*product_dept*product_code*pro_sub_dep*currency_id*agent_name*client_id*order_repeat_no*region*product_category*team_leader*dealing_marchant*bh_merchant*packing*remarks*ship_mode*order_uom*gmts_item_id*set_break_down*total_set_qnty*set_smv*season_buyer_wise*factory_marchant*style_owner*design_source_id*qlty_label*working_location_id*style_ref_no_prev*gmts_item_id_prev*is_deleted*status_active*updated_by*update_date";
		$data_array="'".$quotation_id."'*".$cbo_buyer_name."*".$cbo_location_name."*".$txt_style_ref."*".$txt_repeat_job_no."*".$txt_style_description."*".$cbo_product_department."*".$txt_product_code."*".$cbo_sub_dept."*".$cbo_currercy."*".$cbo_agent."*".$cbo_client."*".$txt_repeat_no."*".$cbo_region."*".$txt_item_catgory."*".$cbo_team_leader."*".$cbo_dealing_merchant."*".$txt_bhmerchant."*".$cbo_packing."*".$txt_remarks."*".$cbo_ship_mode."*".$cbo_order_uom."*".$item_id."*".$set_breck_down."*".$tot_set_qnty."*".$tot_smv_qnty."*".$cbo_season_name."*".$cbo_factory_merchant."*".$cbo_working_company_id."*".$cbo_design_source_id."*".$cbo_qltyLabel."*".$cbo_working_location_id."*'".$PrevStyleRefNo."'*'".$PrevGmtsItemId."'*0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		$field_array1="id, job_no, job_id, gmts_item_id, set_item_ratio, smv_pcs, smv_set, smv_pcs_precost, smv_set_precost, complexity, embelishment, cutsmv_pcs, cutsmv_set, finsmv_pcs, finsmv_set, printseq, embro, embroseq, wash, washseq, spworks, spworksseq, gmtsdying, gmtsdyingseq, ws_id, aop, aopseq";
		$add_comma=0;
		$id1=return_next_id( "id", "wo_po_details_mas_set_details", 1 ) ;
		$set_breck_down_array=explode('__',str_replace("'",'',$set_breck_down));
		for($c=0;$c < count($set_breck_down_array);$c++)
		{
			$set_breck_down_arr=explode('_',$set_breck_down_array[$c]);
			if ($add_comma!=0) $data_array1 .=",";
			if($set_breck_down_arr[2]==0 || $set_breck_down_arr[2]==''){
				echo "SMV**";
				disconnect($con);die;
			}
			$data_array1 .="(".$id1.",".$txt_job_no.",".$hidd_job_id.",'".$set_breck_down_arr[0]."','".$set_breck_down_arr[1]."','".$set_breck_down_arr[2]."','".$set_breck_down_arr[3]."','".$set_breck_down_arr[2]."','".$set_breck_down_arr[3]."','".$set_breck_down_arr[4]."','".$set_breck_down_arr[5]."','".$set_breck_down_arr[6]."','".$set_breck_down_arr[7]."','".$set_breck_down_arr[8]."','".$set_breck_down_arr[9]."','".$set_breck_down_arr[10]."','".$set_breck_down_arr[11]."','".$set_breck_down_arr[12]."','".$set_breck_down_arr[13]."','".$set_breck_down_arr[14]."','".$set_breck_down_arr[15]."','".$set_breck_down_arr[16]."','".$set_breck_down_arr[17]."','".$set_breck_down_arr[18]."','".$set_breck_down_arr[19]."','".$set_breck_down_arr[20]."','".$set_breck_down_arr[21]."')";
			$add_comma++;
			$id1=$id1+1;
			$sewSmv+=$set_breck_down_arr[3];
			$cutSmv+=$set_breck_down_arr[7];
		}
		$flag=1;
		$rID=sql_update("wo_po_details_master",$field_array,$data_array,"job_no","".$txt_job_no."",0);
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		//echo "10**".$rID; die;
		//ALTER SESSION SET NLS_TIMESTAMP_FORMAT='DD-MON-RR HH.MI.SSXFF AM';
		$rID1=execute_query("delete from wo_po_details_mas_set_details where  job_no =".$txt_job_no."",0);
		if($rID1==1 && $flag==1) $flag=1; else $flag=0;
		$rID2=sql_insert("wo_po_details_mas_set_details",$field_array1,$data_array1,0);
		if($rID2==1 && $flag==1) $flag=1; else $flag=0;
		$rID3=execute_query("update  wo_booking_mst set is_apply_last_update=2  where  job_no =".$txt_job_no." and booking_type=1 and is_short=2 ",1);
		if($rID3==1 && $flag==1) $flag=1; else $flag=0;
		$rID4=execute_query("update  wo_pre_cost_mst set sew_smv=$tot_smv_qnty  where  job_no =".$txt_job_no."",1);
		if($rID4==1 && $flag==1) $flag=1; else $flag=0;
		
		$txt_job_no=str_replace("'","",$txt_job_no);
		$data_int=$cbo_currercy.'****'.$set_breck_down;
		$set_smv_id=str_replace("'","",$set_smv_id);
		if($set_smv_id==1 || $set_smv_id==7) fnc_smv_style_integration($db_type,$cbo_company_name,$txt_job_no,$data_int,$sewSmv,$cutSmv,1);
		$job_id=str_replace("'",'',$hidd_job_id);
		//echo "10**";
		if($cost_control_source==1)
		{
			if($quotation_id!=0)
			{
				$query="UPDATE qc_confirm_mst SET job_id=$job_id WHERE cost_sheet_id=$quotation_id and status_active=1 and is_deleted=0";// die;
				$rIDt=execute_query($query,1);
				if($rIDt==1 && $flag==1) $flag=1; else $flag=0;
			}
		}
		//echo "10**".$rID.'='.$rID1.'='.$rID2.'='.$rID3.'='.$rID4.'='.$rIDt.'='.$flag; die;
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "1**".$txt_job_no."**".$rID."**".str_replace("'",'',$hidd_job_id);
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
				echo "1**".$txt_job_no."**".$rID."**".str_replace("'",'',$hidd_job_id);
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
	else if ($operation==2)   // Delete Here----------------------------------------------------------
	{
		$con = connect();
		
		$sql_data=sql_select("select approved from wo_pre_cost_mst where job_no=$txt_job_no and is_deleted=0 and status_active=1");
		$isapproved=$sql_data[0][csf("approved")];
		if($isapproved==1)
		{
			echo "16**Pre Cost Approved, Any Change will be not allowed.";
			disconnect($con);die;
		}
		
		$sql_booking_no=sql_select("select booking_no from wo_booking_dtls where job_no=".$txt_job_no." and status_active=1 and is_deleted=0 group by booking_no");
		$booking_str="";
		foreach($sql_booking_no as $row)
		{
			if($booking_str=="") $booking_str=$row[csf('booking_no')]; else $booking_str.=', '.$row[csf('booking_no')];
		}
		
		if($booking_str!="")
		{
			echo "13**".$booking_str;
			disconnect($con);die;
		}
		$field_array="status_active*is_deleted*updated_by*update_date";
		$data_array="'0'*'1'".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		$rID=sql_delete("wo_po_details_master",$field_array,$data_array,"job_no","".$txt_job_no."",1);
		$rID1=sql_delete("wo_po_break_down",$field_array,$data_array,"job_no_mst","".$txt_job_no."",1);
		$txt_job_no=str_replace("'","",$txt_job_no);
		if($db_type==0)
		{
			if($rID && $rID1)
			{
				mysql_query("COMMIT");  
				echo "2**".$txt_job_no."**".$rID;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID1)
			{
				oci_commit($con); 
				echo "2**".$txt_job_no."**".$rID;
			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}
		disconnect($con);
	}
}

// Master Form End ***************************************** Master Form End******************************************
 
// Dtls Form ************************************************Dtls Form************************************************
if ($action=="get_excess_cut_percent")
{
	$data=explode("_",$data);
	$excess_variable=return_field_value("excut_source","variable_order_tracking"," company_name ='$data[1]' and variable_list=45 and is_deleted=0 and status_active=1");
	
	$excess_per_level=return_field_value("excut_source","variable_order_tracking"," company_name ='$data[1]' and variable_list=65 and is_deleted=0 and status_active=1");
	$editable_id=return_field_value("editable","variable_order_tracking"," company_name ='$data[1]' and variable_list=45 and excut_source=2 and is_deleted=0 and status_active=1");
	if($editable_id==0 || $editable_id=='') $editable_id=0;else $editable_id=$editable_id;
	$percentage=0;
	if($excess_variable==2 && $excess_per_level==2){
		$qry_result=sql_select( "select percentage from  lib_excess_cut_slab where comapny_id='$data[1]' and buyer_id='$data[2]' and $data[0]  between lower_limit_qty and   upper_limit_qty and status_active=1 and is_deleted=0");
		foreach ($qry_result as $row)
		{
			$percentage= $row[csf("percentage")]; 
		}
	}
	echo $excess_variable."_".$percentage."_".$editable_id;
}

if ($action=="populate_order_details_form_data")
{
	$user=$_SESSION['logic_erp']['user_id'];
	$user_id=return_field_value("is_data_level_secured","user_passwd","id=$user AND valid=1");
	$company_id=return_field_value("a.company_name","wo_po_details_master a,wo_po_break_down b","a.job_no=b.job_no_mst and b.id='$data' and a.is_deleted=0 and a.status_active=1");
	
	$result_color= sql_select("select c.order_quantity from wo_po_details_master a,wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and c.po_break_down_id=b.id and b.id='$data'   and a.is_deleted=0 and a.status_active=1  and c.is_deleted=0 and c.status_active=1");
	//echo "select c.order_quantity from wo_po_details_master a,wo_po_break_down b,wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and c.po_break_down_id=b.id and b.id='$data'   and a.is_deleted=0 and a.status_active=1  and c.is_deleted=0 and c.status_active=1";
	$tot_color_size_qty=0;
	foreach ($result_color as $row)
	{
		$tot_color_size_qty+=$row[csf("order_quantity")];
	}
	
	$result= sql_select("select a.company_name from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and b.id='$data' and a.is_deleted=0 and a.status_active=1");
	$company_id=$result[0][csf('company_name')];
	$update_period_id=return_field_value("po_update_period","variable_order_tracking"," company_name ='$company_id' and variable_list=32 and is_deleted=0 and status_active=1");
	$po_current_date_data=return_field_value("po_current_date","variable_order_tracking"," company_name ='$company_id' and variable_list=33 and is_deleted=0 and status_active=1");
	if($update_period_id=="") $update_period_id=0; else $update_period_id=$update_period_id;
	if($po_current_date_data=="" || $po_current_date_data==2) $po_current_date_data=0; else $po_current_date_data=$po_current_date_data;

	$data_array=sql_select("select id,is_confirmed,po_number,po_received_date,pub_shipment_date,shipment_date,factory_received_date,po_quantity,unit_price,po_total_price,excess_cut,plan_cut,country_name,details_remarks,delay_for,status_active,packing,grouping,projected_po_id,tna_task_from_upto,file_no,insert_date,sc_lc,with_qty,sewing_company_id,sewing_location_id from wo_po_break_down where id='$data'");
	foreach ($data_array as $row)
	{ 
		$insert_date=explode(" ",$row[csf("insert_date")]);
		$current_date=date('d-m-Y h:i:s');
		$po_insert_date=change_date_format($insert_date[0],'dd-mm-yyyy','-').' '.$insert_date[1];
		$total_time=datediff(n,$po_insert_date,$current_date);
		$total_hour=floor($total_time/60);
		//.":".$total_time%60
		echo "document.getElementById('cbo_order_status').value = '".$row[csf("is_confirmed")]."';\n"; 
		echo "document.getElementById('txt_order_status').value = '".$row[csf("is_confirmed")]."';\n"; 
		echo "document.getElementById('txt_hidden_color_qty').value = '".$tot_color_size_qty."';\n"; 
		$current_date=date('d-m-Y');
		 if($po_current_date_data==1 && $row[csf("is_confirmed")]==1)
		 {
			echo "document.getElementById('txt_po_received_date').value = '".change_date_format($row[csf("po_received_date")], "dd-mm-yyyy", "-")."';\n";
			echo "$('#txt_po_received_date').attr('disabled',true);\n";   
		 }

		else if($po_current_date_data==1 && $row[csf("is_confirmed")]==2)
		{
 			echo "document.getElementById('txt_po_received_date').value = '".change_date_format($row[csf("po_received_date")], "dd-mm-yyyy", "-")."';\n";
 			echo "$('#txt_po_received_date').attr('disabled',true);\n";
 		}


		 else
		 {
			echo "document.getElementById('txt_po_received_date').value = '".change_date_format($row[csf("po_received_date")], "dd-mm-yyyy", "-")."';\n"; 
			echo "$('#txt_po_received_date').attr('disabled',false);\n";   
		 }
		
		/*if($row[csf("is_confirmed")]==1)
		{
		echo "document.getElementById('txt_po_received_date').value = '".change_date_format($row[csf("po_received_date")], "dd-mm-yyyy", "-")."';\n";
		echo "$('#txt_po_received_date').attr('disabled',true);\n";   
		}
		else
		{
		echo "document.getElementById('txt_po_received_date').value = '".change_date_format($row[csf("po_received_date")], "dd-mm-yyyy", "-")."';\n"; 
		echo "$('#txt_po_received_date').attr('disabled',false);\n"; 	
		}*/
		echo "document.getElementById('txt_po_no').value = '".$row[csf("po_number")]."';\n";  
		//echo "document.getElementById('txt_po_received_date').value = '".change_date_format($row[csf("po_received_date")], "dd-mm-yyyy", "-")."';\n";  
		echo "document.getElementById('txt_pub_shipment_date').value = '".change_date_format($row[csf("pub_shipment_date")], "dd-mm-yyyy", "-")."';\n";  
		echo "document.getElementById('txt_org_shipment_date').value = '".change_date_format($row[csf("shipment_date")], "dd-mm-yyyy", "-")."';\n";  
		echo "document.getElementById('txt_factory_rec_date').value = '".change_date_format($row[csf("factory_received_date")], "dd-mm-yyyy", "-")."';\n";  
		echo "document.getElementById('txt_po_quantity').value = '".$row[csf("po_quantity")]."';\n";
		 echo "document.getElementById('hidden_po_qty').value = '".$row[csf("po_quantity")]."';\n";  
		echo "$('#txt_po_quantity').attr('saved_po_quantity','".$row[csf("po_quantity")]."')".";\n";
		echo "document.getElementById('txt_avg_price').value = '".$row[csf("unit_price")]."';\n";  
		echo "document.getElementById('txt_amount').value = '".$row[csf("po_total_price")]."';\n";  
		echo "document.getElementById('txt_excess_cut').value = '".$row[csf("excess_cut")]."';\n";  
		echo "document.getElementById('txt_plan_cut').value = '".$row[csf("plan_cut")]."';\n";  
		echo "document.getElementById('txt_po_datedif_hour').value = '".$total_hour."';\n";  
		//echo "document.getElementById('txt_user_id').value = '".$user_id."';\n";  
		echo "document.getElementById('txt_details_remark').value = '".$row[csf("details_remarks")]."';\n";  
		echo "document.getElementById('cbo_status').value = '".$row[csf("status_active")]."';\n";  
		echo "document.getElementById('update_id_details').value = '".$row[csf("id")]."';\n"; 
		echo "set_multiselect('cbo_delay_for','0','1','".($row[csf("delay_for")])."','0');\n"; 
		//echo "load_drop_down( 'requires/woven_order_entry_controller', '".$row[csf('po_received_date')]."'_'".$row[csf('pub_shipment_date')]."'_'.cbo_buyer_name, 'load_drop_down_tna_task', 'tna_task_td' );\n";
		echo "set_tna_task();\n"; 

		echo "document.getElementById('cbo_packing_po_level').value = '".$row[csf("packing")]."';\n";  
		echo "document.getElementById('txt_grouping').value = '".$row[csf("grouping")]."';\n"; 
		echo "document.getElementById('cbo_projected_po').value = '".$row[csf("projected_po_id")]."';\n";  
		echo "document.getElementById('cbo_tna_task').value = '".$row[csf("tna_task_from_upto")]."';\n"; 
		echo "document.getElementById('txt_file_no').value = '".$row[csf("file_no")]."';\n"; 
		echo "document.getElementById('txt_sc_lc').value = '".$row[csf("sc_lc")]."';\n"; 
		//echo "document.getElementById('cbo_working_company_id').value = '".$row[csf("sewing_company_id")]."';\n"; 
		
		//echo "load_drop_down( 'requires/woven_order_entry_controller', '".$row[csf('sewing_company_id')]."', 'load_drop_down_sew_location', 'sew_location' );\n";
		//echo "load_drop_down( 'requires/woven_order_entry_controller', company_id, 'load_drop_down_sew_location', 'sew_location' );";
	//echo "document.getElementById('cbo_working_location_id').value = '".$row[csf("sewing_location_id")]."';\n"; 
		
			if($row[csf("with_qty")]==0){
				echo "document.getElementById('with_qty').checked = true;\n"; 
			}else{
				echo "document.getElementById('with_qty').checked = false;\n"; 
			}
			echo "document.getElementById('with_qty').value = '".$row[csf("with_qty")]."';\n"; 
			echo "document.getElementById('with_qty_pop').value = '".$row[csf("with_qty")]."';\n"; 
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_order_entry_details',2);\n";  
	}
	 $qry_result=sql_select( "select id from  wo_po_color_size_breakdown where po_break_down_id='$data' and  status_active=1 and is_deleted=0");
	 $row=count($qry_result);
	 if($row>0)
	 {
				//echo "$('#txt_avg_price').attr('disabled','true')".";\n";
				//echo "$('#txt_avg_price').attr('title','Change It From Color Size Break Down')".";\n";

	 }
	 else
	 {
				//echo "$('#txt_avg_price').removeAttr('disabled')".";\n";
				//echo "$('#txt_avg_price').removeAttr('title')".";\n";
				echo "alert('Color Size Breakdown not found for this Order')".";\n";
	 }
	 $sql_data=sql_select( "select po_break_down_id, sum(production_quantity) as production_quantity from  pro_garments_production_mst where po_break_down_id=".$data." and production_type=1 and  status_active=1 and is_deleted=0 group by po_break_down_id");
	foreach($sql_data as $row_data)
	{
		if($row_data[csf('production_quantity')]>0)
		{
			echo "$('#txt_excess_cut').attr('disabled','true')".";\n";
			echo "$('#txt_excess_cut').attr('title','Cutting Qty Found')".";\n";
		}
		else
		{
			echo "$('#txt_excess_cut').removeAttr('disabled')".";\n";
			echo "$('#txt_avg_price').removeAttr('title')".";\n";
		}
	}
}


if ($action=="get_cutting_qty")
{
	$production_quantity=0;
	if($data!="")
	{
		$sql_data=sql_select( "select po_break_down_id, sum(production_quantity) as production_quantity from  pro_garments_production_mst where po_break_down_id='$data' and production_type=1 and  status_active=1 and is_deleted=0 group by po_break_down_id");
		foreach($sql_data as $row_data)
		{
			if($row_data[csf('production_quantity')]>0)
			{
			$production_quantity=$row_data[csf('production_quantity')];
			}
		}
	}
	echo trim($production_quantity);
}

if($action=="get_cutting_qty_order")
{
	$msg=array(0=>"Delete not allowed bcz following Subsequence Entry Found:");
	$sales_contact_array=array();
	$sales_contact_sql=sql_select("select a.contact_system_id,a.contract_no from com_sales_contract a, com_sales_contract_order_info b where a.id=b.com_sales_contract_id and b.wo_po_break_down_id='$data' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	foreach($sales_contact_sql as $sales_contact_row){
		$sales_contact_array[$sales_contact_row[csf('contact_system_id')]]=$sales_contact_row[csf('contract_no')];
	}
	
	if(count($sales_contact_array)>0){
		$msg[]=count($msg).'. Attached with Sales contact No :'.implode(",",$sales_contact_array);
	}
	
	$lc_array=array();
	$lc_sql=sql_select("select a.export_lc_system_id, a.export_lc_no from com_export_lc a, com_export_lc_order_info b where a.id=b.com_export_lc_id and b.wo_po_break_down_id='$data' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	foreach($lc_sql as $lc_row){
		$lc_array[$lc_row[csf('export_lc_system_id')]]=$lc_row[csf('export_lc_no')];
	}
	
	if(count($lc_array)>0){
		$msg[]=count($msg).'. Attached with Export LC No  :'.implode(",",$lc_array);
	}
	
	
	$fb_array=array();//1
	$tb_array=array();//2
	$sb_array=array();//3
	$fs_array=array();//4
	$ts_array=array();//5
	$eb_array=array();//6
	$booking_sql=sql_select("select a.booking_no, a.booking_type from  wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and b.po_break_down_id='$data' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	foreach($booking_sql as $booking_row){
		if($booking_row[csf('booking_type')]==1){
		$fb_array[$booking_row[csf('booking_no')]]=$booking_row[csf('booking_no')];
		}
		else if($booking_row[csf('booking_type')]==2){
		$tb_array[$booking_row[csf('booking_no')]]=$booking_row[csf('booking_no')];
		}
		else if($booking_row[csf('booking_type')]==3){
		$sb_array[$booking_row[csf('booking_no')]]=$booking_row[csf('booking_no')];
		}
		else if($booking_row[csf('booking_type')]==4){
		$fs_array[$booking_row[csf('booking_no')]]=$booking_row[csf('booking_no')];
		}
		else if($booking_row[csf('booking_type')]==5){
		$ts_array[$booking_row[csf('booking_no')]]=$booking_row[csf('booking_no')];
		}
		else if($booking_row[csf('booking_type')]==6){
		$eb_array[$booking_row[csf('booking_no')]]=$booking_row[csf('booking_no')];
		}
	}
	
	if(count($fb_array)>0){
		$msg[]=count($msg).'. Fabric Booking Found BK No  :'.implode(",",$fb_array);
	}
	if(count($tb_array)>0){
		$msg[]=count($msg).'. Trims Booking Found BK No  :'.implode(",",$tb_array);
	}
	if(count($sb_array)>0){
		$msg[]=count($msg).'. Service Booking Found BK No  :'.implode(",",$sb_array);
	}
	if(count($fs_array)>0){
		$msg[]=count($msg).'. Sample Fabric Booking Found BK No  :'.implode(",",$fs_array);
	}
	if(count($ts_array)>0){
		$msg[]=count($msg).'. Sample Trims Booking Found BK No  :'.implode(",",$ts_array);
	}
	if(count($eb_array)>0){
		$msg[]=count($msg).'. Emblishment Booking Found BK No  :'.implode(",",$eb_array);
	}
		
	$cutting_array=array();//1
	$printing_array=array();//2
	$printreceived_array=array();//3
	$sewingin_array=array();//4
	$sewingout_array=array();//5
	$iron_array=array();//7
	$finish_array=array();//8
	
	$sql_production=sql_select( "select production_quantity,production_type from  pro_garments_production_mst where po_break_down_id='$data'  and  status_active=1 and is_deleted=0");
	foreach($sql_production as $row_production)
	{
		if($row_production[csf('production_type')]==1){
		$cutting_array[$row_production[csf('production_type')]]+=$row_production[csf('production_quantity')];
		}
		else if($row_production[csf('production_type')]==2){
		$printing_array[$row_production[csf('production_type')]]+=$row_production[csf('production_quantity')];
		}
		else if($row_production[csf('production_type')]==3){
		$printreceived_array[$row_production[csf('production_type')]]+=$row_production[csf('production_quantity')];
		}
		else if($row_production[csf('production_type')]==4){
		$sewingin_array[$row_production[csf('production_type')]]+=$row_production[csf('production_quantity')];
		}
		else if($row_production[csf('production_type')]==5){
		$sewingout_array[$row_production[csf('production_type')]]+=$row_production[csf('production_quantity')];
		}
		else if($row_production[csf('production_type')]==7){
		$iron_array[$row_production[csf('production_type')]]+=$row_production[csf('production_quantity')];
		}
		else if($row_production[csf('production_type')]==8){
		$finish_array[$row_production[csf('production_type')]]+=$row_production[csf('production_quantity')];
		}
	}
	
	if(count($cutting_array)>0){
		$msg[]=count($msg).'. Cutting Qty Found :'.$cutting_array[1];
	}
	if(count($printing_array)>0){
		$msg[]=count($msg).'. Printing Qty Found :'.$printing_array[2];
	}
	if(count($printreceived_array)>0){
		$msg[]=count($msg).'. Print received Qty Found :'.$printreceived_array[3];
	}
	if(count($sewingin_array)>0){
		$msg[]=count($msg).'.	Sewing Input  Found :'.$sewingin_array[4];
	}
	if(count($sewingout_array)>0){
		$msg[]=count($msg).'. Sewing Output Found :'.$sewingout_array[5];
	}
	if(count($iron_array)>0){
		$msg[]=count($msg).'. Iron Qty Found :'.$iron_array[7];
	}
	if(count($finish_array)>0){
		$msg[]=count($msg).'. Finish Qty Found :'.$finish_array[8];
	}
	$dataArrayYarnIssue=array();
	$sql_yarn_iss="select a.po_breakdown_id, 
			sum(CASE WHEN a.entry_form ='3' THEN a.quantity ELSE 0 END) AS issue_qnty,
			sum(CASE WHEN a.entry_form ='9' THEN a.quantity ELSE 0 END) AS return_qnty
			from order_wise_pro_details a, product_details_master b where a.prod_id=b.id and b.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and a.issue_purpose!=2 and a.po_breakdown_id='$data' group by a.po_breakdown_id";
	$dataArrayIssue=sql_select($sql_yarn_iss);
	foreach($dataArrayIssue as $row_yarn_iss)
	{
		//$issuue=$row_yarn_iss[csf('issue_qnty')]-$row_yarn_iss[csf('return_qnty')]
		$dataArrayYarnIssue[$row_yarn_iss[csf('po_breakdown_id')]]+=$row_yarn_iss[csf('issue_qnty')];
	}
	
	if(count($dataArrayYarnIssue)>0){
		$msg[]=count($msg).'. Yarn Issue Qty Found :'.$dataArrayYarnIssue[$data];
	}
	
	$yarnAllocationArr=array(); //$yarnAllocationJobArr=array();
	$sql_yarn_allocation="select a.po_break_down_id 
			sum(a.qnty) AS allocation_qty
			from inv_material_allocation_dtls a, product_details_master b where a.item_id=b.id and b.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and a.po_break_down_id='$data' group by a.po_break_down_id";
	$dataArrayAllocation=sql_select($sql_yarn_allocation);
	foreach($dataArrayAllocation as $allocationRow)
	{
		$yarnAllocationArr[$allocationRow[csf('po_break_down_id')]]+=$allocationRow[csf('allocation_qty')];
	}
	
	if(count($yarnAllocationArr)>0){
		$msg[]=count($msg).'. Yarn Allocation Qty Found  :'.$yarnAllocationArr[$data];
	}
	
	//$trans_qnty_arr=array(); 
	$grey_receive_qnty_arr=array(); $grey_issue_qnty_arr=array(); $grey_receive_return_qnty_arr=array(); $grey_issue_return_qnty_arr=array();
							
	$dataArrayTrans=sql_select("select po_breakdown_id, 
							sum(CASE WHEN entry_form in (2,58) THEN quantity ELSE 0 END) AS grey_receive,
							sum(CASE WHEN entry_form ='45' and trans_type=3 THEN quantity ELSE 0 END) AS grey_receive_return,

							sum(CASE WHEN entry_form ='16' THEN quantity ELSE 0 END) AS grey_issue,
							sum(CASE WHEN entry_form ='61' THEN quantity ELSE 0 END) AS grey_issue_rollwise,
							sum(CASE WHEN entry_form ='51' and trans_type=4 THEN quantity ELSE 0 END) AS grey_issue_return,
							
							sum(CASE WHEN entry_form ='11' and trans_type=5 THEN quantity ELSE 0 END) AS transfer_in_qnty_yarn,
							sum(CASE WHEN entry_form ='11' and trans_type=6 THEN quantity ELSE 0 END) AS transfer_out_qnty_yarn,
							sum(CASE WHEN entry_form ='13' and trans_type=5 THEN quantity ELSE 0 END) AS transfer_in_qnty_knit,
							sum(CASE WHEN entry_form ='13' and trans_type=6 THEN quantity ELSE 0 END) AS transfer_out_qnty_knit,
							sum(CASE WHEN entry_form ='80' and trans_type=6 THEN quantity ELSE 0 END) AS trans_out_sample_knit,
							sum(CASE WHEN entry_form ='81' and trans_type=5 THEN quantity ELSE 0 END) AS trans_in_sample_knit
							from order_wise_pro_details where status_active=1 and is_deleted=0 and entry_form in(2,11,13,16,45,51,58,61,80,81) and po_breakdown_id= '$data' group by po_breakdown_id");
	foreach($dataArrayTrans as $row)
	{
		$grey_receive_qnty_arr[$row[csf('po_breakdown_id')]]=$row[csf('grey_receive')];
	}
		
	if(count($grey_receive_qnty_arr)>0){
		$msg[]=count($msg).'. Grey Production Qty Found :'.$grey_receive_qnty_arr[$data];
	}
		
	$greyPurchaseQntyArray=array(); $greyProductionQntyArray=array();
	$sql_grey_purchase="select c.po_breakdown_id, 
	sum(CASE WHEN a.receive_basis<>9 THEN c.quantity ELSE 0 END) AS grey_purchase_qnty,
	sum(CASE WHEN a.receive_basis=9 THEN c.quantity ELSE 0 END) AS grey_production_qnty
	from inv_receive_master a,pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in (22) and c.entry_form in (22) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id='$data' group by c.po_breakdown_id";//and a.receive_basis<>9 sum(c.quantity) as grey_purchase_qnty
	$dataArrayGreyPurchase=sql_select($sql_grey_purchase);
	foreach($dataArrayGreyPurchase as $greyRow)
	{
		$greyPurchaseQntyArray[$greyRow[csf('po_breakdown_id')]]=$greyRow[csf('grey_purchase_qnty')];
		$greyProductionQntyArray[$greyRow[csf('po_breakdown_id')]]=$greyRow[csf('grey_production_qnty')];
	}
	
	if(count($greyPurchaseQntyArray)>0){
		$msg[]=count($msg).'. Grey Recv. Purchase Qty Found :'.$greyPurchaseQntyArray[$data];
	}
	
	if(count($greyProductionQntyArray)>0 && $greyProductionQntyArray[$data]>0){
		$msg[]=count($msg).'. Grey Recv. Production Qty Found :'.$greyProductionQntyArray[$data];
	}
	
	$greyDeliveryArray=array();
	$sql_grey_delivery="select order_id, sum(current_delivery) as grey_delivery_qty from pro_grey_prod_delivery_dtls where entry_form in(53,56) and status_active=1 and is_deleted=0 and order_id='$data' by order_id";
	$data_grey_delivery=sql_select($sql_grey_delivery);
	foreach($data_grey_delivery as $greyDel)
	{
		$greyDeliveryArray[$greyDel[csf('order_id')]]=$greyDel[csf('grey_delivery_qty')];
	}
	
	if(count($greyDeliveryArray)>0){
		$msg[]=count($msg).'. Grey Delivery Qty Found :'.$greyDeliveryArray[$data];
	}
	//var_dump($greyDeliveryArray);
	$finDeliveryArray=array();
	$sql_fin_delivery="select a.order_id, b.color, sum(a.current_delivery) as fin_delivery_qty from pro_grey_prod_delivery_dtls a, product_details_master b where a.product_id=b.id and a.entry_form in(54,67) and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and a.order_id='$data' group by a.order_id, b.color";
	
	$data_fin_delivery=sql_select($sql_fin_delivery);
	foreach($data_fin_delivery as $finDel)
	{
		$finDeliveryArray[$finDel[csf('order_id')]]=$finDel[csf('fin_delivery_qty')];
	}

	if(count($data_fin_delivery)>0 && $data_fin_delivery[$data]>0){
		$msg[]=count($msg).'. Finish Delivery Qty Found :'.$data_fin_delivery[$data];
	}
	
	$trim_array=array();
	$trim_receive_qty_data=sql_select("select b.po_breakdown_id,b.quantity*a.rate as amount   from  inv_receive_master c,product_details_master d,inv_trims_entry_dtls a , order_wise_pro_details b where a.mst_id=c.id and a.trans_id=b.trans_id and a.prod_id=d.id and b.trans_type=1 and b.entry_form=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.po_breakdown_id='$data ");
	foreach($trim_receive_qty_data as $row)
	{
		$trim_array[$row[csf('po_breakdown_id')]]+=$row[csf('amount')];
	}
	
	if(count($trim_array)>0){
		$msg[]=count($msg).'. Trims Receive Value Found :'.$trim_array[$data];
	}
	
	if(count($msg)>1){
		echo "1**".implode("\n",$msg);
		die;
	}
		
	
	/*$production_quantity=0;
	$sql_data=sql_select( "select count(id) as id from  pro_garments_production_mst where po_break_down_id='$data'  and  status_active=1 and is_deleted=0");
	foreach($sql_data as $row_data)
	{
		if($row_data[csf('id')]>0)
		{
		$production_quantity=$row_data[csf('id')];
		}
	}
	echo trim($production_quantity);*/
	die;
}

if ($action=="save_update_delete_dtls")
{
	$color_library=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name"  );
    $size_library=return_library_array( "select id,size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name"  );
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$packing ="";
	if(str_replace("'","",$cbo_packing_po_level)==0){
		$packing = $cbo_packing;
	}
	else{
		$packing = $cbo_packing_po_level;
	}
	if (file_exists('dateretriction.php')){
		require('dateretriction.php');
	}
	 
	// added on temporary basis. CTO
	/*$off_day_ship=return_field_value("publish_shipment_date", "variable_order_tracking", "company_name=$cbo_company_name and variable_list=46");
	if( $off_day_ship==2 )
	{
		$days_status=return_field_value("day_status", "lib_capacity_calc_mst a, lib_capacity_calc_dtls b ", " b.date_calc=$txt_pub_shipment_date and comapny_id=$cbo_company_name and a.id=b.mst_id");
		if($days_status==2)
		{
		    echo "46**1"; 
			die	;
		}
		$days_statusss=return_field_value("day_status", "lib_capacity_calc_mst a, lib_capacity_calc_dtls b ", " b.date_calc=$txt_org_shipment_date and comapny_id=$cbo_company_name and a.id=b.mst_id");
		if($days_statusss==2)
		{
		    echo "46**2"; 
			die	;
		}
	}*/
	// added on temporary basis. CTO
	
	
	
	if ($operation==0)  // Insert Here
	{	
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$data_shipDate_vari="";
		$sql_shipDate_vari=sql_select("select duplicate_ship_date from variable_order_tracking where company_name=$cbo_company_name and variable_list=29");
		foreach($sql_shipDate_vari as $row_shipDate_vari)
		{
			$data_shipDate_vari=$row_shipDate_vari[csf('duplicate_ship_date')];	
		}
		if($data_shipDate_vari==1)
		{
			$txt_pub_shipment_date_cond="and pub_shipment_date=$txt_pub_shipment_date";	
		}
		else
		{
			$txt_pub_shipment_date_cond="";	
		}
		$image_mdt=return_field_value("image_mandatory", "variable_order_tracking", "company_name=$cbo_company_name and variable_list=30");
		$image=return_field_value("id", "common_photo_library", "master_tble_id=$update_id and form_name='knit_order_entry' and file_type=1");
		if($image_mdt==1 && $image=="")
		{
		    echo "24**0"; 
		disconnect($con);	die	;
		}
		if (is_duplicate_field( "po_number", "wo_po_break_down", "po_number=$txt_po_no and job_no_mst=$update_id $txt_pub_shipment_date_cond and is_deleted=0" ) == 1)
		{
			echo "11**0"; 
			disconnect($con);die;
		}
		$org_shipment_date=$txt_org_shipment_date;
		if(trim($org_shipment_date,"'")=="") $org_shipment_date=$txt_pub_shipment_date;
		
		$txt_pub_shipment_date=$txt_pub_shipment_date;
		if(trim($txt_pub_shipment_date,"'")=="") $txt_pub_shipment_date=$txt_org_shipment_date;
		
		//==============================Lead Time Validation ==============================
		$min_lead_time_control=2;
		$sql_min_lead_time_control=sql_select("select min_lead_time_control from variable_order_tracking where company_name=$cbo_company_name and variable_list=51");
		foreach($sql_min_lead_time_control as $row_min_lead_time_control){
			$min_lead_time_control=$row_min_lead_time_control[csf('min_lead_time_control')];
		}
		
		$received_date=date('Y-m-d',strtotime(str_replace("'","",$txt_po_received_date)));
        $pub_shipment_date=date('Y-m-d',strtotime(str_replace("'","",$txt_pub_shipment_date)));
        $dDiff=datediff( 'd', $received_date, $pub_shipment_date, $using_timestamps = false );
		$year=date("Y",strtotime(str_replace("'","",$org_shipment_date)));
	    $month= (int) date("m",strtotime(str_replace("'","",$org_shipment_date)));
		$min_leadtime_allocation=0;
		$sql_leadtime_vari=sql_select("select min_allocation from lib_min_lead_time_mst a, lib_min_lead_time_dtls b where a.id=b.mst_id and a.company_id=$cbo_company_name and year_id='$year' and a.month_id='$month'  and b.buyer_id=$cbo_buyer_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id");//and a.location_id=$cbo_location_name
		foreach($sql_leadtime_vari as $row_leadtime_vari){
			$min_leadtime_allocation=$row_leadtime_vari[csf('min_allocation')];	
		}
		if($dDiff < $min_leadtime_allocation && $min_lead_time_control==1){
			echo "LeadTime**0**".$min_leadtime_allocation;
			disconnect($con);die;
		}
		//====================================================================================
		//==============================capacity Validation ==============================
		$buyer_allocation_maintain=2;
		$capacity_exceed_level=0;
		$sql_capa_vari=sql_select("select buyer_allocation_maintain, capacity_exceed_level from variable_order_tracking where company_name=$cbo_company_name and variable_list=52");
		foreach($sql_capa_vari as $row_capa_vari){
			$buyer_allocation_maintain=$row_capa_vari[csf('buyer_allocation_maintain')];
			$capacity_exceed_level=$row_capa_vari[csf('capacity_exceed_level')];	
		}
		
		$capaBuyerCond=""; $poBuyerCond="";
		if($buyer_allocation_maintain==1){
			$capaBuyerCond="and a.buyer_id=$cbo_buyer_name";
			$poBuyerCond="and b.buyer_name=$cbo_buyer_name";
		}else{
			$capaBuyerCond=""; $poBuyerCond="";
		}
		//==============================capacity Validation For Working Company==============================
		$lc_company_id=str_replace("'","",$cbo_company_name);
		$w_company_id=str_replace("'","",$cbo_working_company_id);
		
		$w_buyer_allocation_maintain=2;
		$w_capacity_exceed_level=0;
		$w_capa_vari=sql_select("select buyer_allocation_maintain, capacity_exceed_level from variable_order_tracking where company_name=$cbo_working_company_id and variable_list=52");
		foreach($w_capa_vari as $row_capa_vari){
			$w_buyer_allocation_maintain=$row_capa_vari[csf('buyer_allocation_maintain')];
			$w_capacity_exceed_level=$row_capa_vari[csf('capacity_exceed_level')];	
		}
		$w_capaBuyerCond="";
		$w_poBuyerCond="";
		if($w_buyer_allocation_maintain==1){
			if($lc_company_id==$w_company_id)
			{
				//$w_capaBuyerCond="and b.buyer_id=$cbo_buyer_name";
				//$w_poBuyerCond="and b.buyer_name=$cbo_buyer_name";
			}
			else
			{
				$w_capaBuyerCond="";
				//$w_poBuyerCond="";
			}
		}else{
			$w_capaBuyerCond="";
			$w_poBuyerCond="";
		}
		//End
		$year_month_name=$month.",".$year;
		$sales_target_qty=0; $sales_target_value=0; $sales_target_mint=0;
		
		$sql_sales_target=sql_select("select sum(sales_target_qty) as sales_target_qty,  sum(sales_target_value) as sales_target_value,   sum(sales_target_mint) as sales_target_mint from wo_sales_target_mst a, wo_sales_target_dtls b where a.id=b.sales_target_mst_id and a.company_id=$cbo_company_name  $capaBuyerCond and b.year_month_name='$year_month_name' and a.status_active=1 and a.is_deleted=0  order by a.id");//and a.team_leader=$cbo_team_leader and  a.starting_year='$year'
		foreach($sql_sales_target as $row_sales_target){
			$sales_target_qty+=$row_sales_target[csf('sales_target_qty')];	;
			$sales_target_value+=$row_sales_target[csf('sales_target_value')];	
			$sales_target_mint+=$row_sales_target[csf('sales_target_mint')];
		}
		$buyer_id=str_replace("'","",$cbo_buyer_name);
		
			if($w_buyer_allocation_maintain==1){
			 $sql_allowcat="select a.company_id,b.buyer_id, b.allocation_percentage FROM lib_capacity_allocation_mst a,lib_capacity_allocation_dtls b
				 where a.id=b.mst_id AND a.company_id=$cbo_working_company_id   AND a.month_id=$month AND a.year_id=$year   and b.status_active=1 and 
			b.is_deleted=0 $w_capaBuyerCond";//$row[csf('allocation_percentage')]
			 $sql_allowcat_result=sql_select($sql_allowcat);
			 $tot_allocation_percentage=0;
				foreach($sql_allowcat_result as $row)
				{
					if($row[csf('allocation_percentage')]>0)
					{
						$tot_allocation_percentage+=$row[csf('allocation_percentage')];
						$com_buyer_allocate_arr[$row[csf('buyer_id')]]=$row[csf('buyer_id')];
						$buyer_allocate_percent_arr[$row[csf('buyer_id')]]=$row[csf('allocation_percentage')];
						$allocat_buyer_id.=$row[csf('buyer_id')].',';
					}
					if($row[csf('allocation_percentage')]>0)
					{
						$allocat_buyer_id2.=$row[csf('buyer_id')].',';
					}
					else
					{
						$unallocat_buyer_id.=$row[csf('buyer_id')].',';
					}
					
				}
				$buyer_remain_allocate_percent=100-$tot_allocation_percentage;
				//echo  $tot_allocation_percentage.'A'.$buyer_remain_allocate_percent;
				
				$allocat_buyer_ids=rtrim($allocat_buyer_id,',');
				$unallocat_buyer_ids=rtrim($unallocat_buyer_id,',');
				if($lc_company_id!=$w_company_id)
				{
					if($allocat_buyer_ids!='') $allocat_buyer_cond="and b.buyer_name not in($allocat_buyer_ids) ";else $allocat_buyer_cond='';
				}
				/*else if(($lc_company_id==$w_company_id) && ($buyer_remain_allocate_percent>0))
				{
					if($allocat_buyer_ids!='') $allocat_buyer_cond="and b.buyer_name not in($allocat_buyer_ids) ";else $allocat_buyer_cond='';
					
				}*/
				//echo $buyer_remain_allocate_percent.'='.$allocat_buyer_cond.'A';
				
				$w_poBuyerCond='';
				if($lc_company_id==$w_company_id)
				{
					$allocat_buyer_id=$com_buyer_allocate_arr[$buyer_id];
					
					if(($allocat_buyer_id!=$buyer_id) && ($buyer_remain_allocate_percent>0))
					{
						//if($buyer_id!=0) $w_poBuyerCond="and b.buyer_name not in($buyer_id) ";else $w_poBuyerCond='';
						if($allocat_buyer_ids!='') $allocat_buyer_cond="and b.buyer_name not in($allocat_buyer_ids) ";else $allocat_buyer_cond='';
					}
					else
					{
						if($buyer_id!=0) $w_poBuyerCond="and b.buyer_name in($buyer_id) ";else $w_poBuyerCond='';
					}
				}
			}
			
  
		$sql_con_capa="SELECT   sum(d.capacity_min) as capacity_min FROM  lib_capacity_calc_mst c,  lib_capacity_calc_dtls d
			WHERE c.id=d.mst_id AND c.comapny_id=$cbo_working_company_id AND c.year=$year and d.month_id=$month and  d.capacity_min>0 and c.status_active=1 and c.is_deleted=0 and c.location_id = $cbo_working_location_id";
			//echo "10**".$sql_con_capa;die;
		 	$con_capa_result=sql_select($sql_con_capa);
			foreach($con_capa_result as $row)
			{
				$tot_company_capacity_min=$row[csf('capacity_min')];
			}
		
		if($capacity_exceed_level==1){
			$sql_po=sql_select("SELECT a.po_quantity as po_quantity, a.po_total_price as po_total_price from wo_po_break_down a, wo_po_details_master b where b.job_no=a.job_no_mst and b.company_name=$cbo_company_name $poBuyerCond and a.t_month=$month and  a.t_year=$year  and a.is_confirmed=1  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0");//and  b.team_leader=$cbo_team_leader
			$po_quantity=0;
			foreach($sql_po as $row_po){
				$po_quantity+=$row_po[csf('po_quantity')];
			}
			$totPoqty=$po_quantity+str_replace("'","",$txt_po_quantity);
			if($totPoqty>$sales_target_qty){
				echo "CapaCityQty**".$totPoqty."**".$sales_target_qty;
				disconnect($con);die;
			}
		}
		
		if($capacity_exceed_level==2){
			$sql_po=sql_select("SELECT a.po_quantity as po_quantity, a.po_total_price as po_total_price from wo_po_break_down a, wo_po_details_master b where b.job_no=a.job_no_mst and b.company_name=$cbo_company_name $poBuyerCond and a.t_month=$month and  a.t_year=$year  and a.is_confirmed=1  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0");//and b.team_leader=$cbo_team_leader
			$po_total_price=0;
			foreach($sql_po as $row_po){
				$po_total_price+=$row_po[csf('po_total_price')];
			}
			$totPrice=$po_total_price + str_replace("'","",$txt_amount);
			if($totPrice > $sales_target_value){
				echo "CapaCityValue**".$totPrice."**".$sales_target_value;
				disconnect($con);die;
			}
		}
		
		if($capacity_exceed_level==3){
			$sql_po=sql_select("SELECT a.po_quantity as po_quantity, a.po_total_price as po_total_price, b.set_smv from wo_po_break_down a, wo_po_details_master b where b.job_no=a.job_no_mst and b.company_name=$cbo_company_name $poBuyerCond and a.t_month=$month and  a.t_year=$year and a.is_confirmed=1  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0");//and  b.team_leader=$cbo_team_leader
			$smv=0;
			foreach($sql_po as $row_po){
				$smv+=$row_po[csf('po_quantity')]*$row_po[csf('set_smv')];
			}
			$curr_smv=str_replace("'","",$txt_po_quantity)*str_replace("'","",$tot_smv_qnty);
			$totsmv=$smv+$curr_smv;
			if($totsmv>$sales_target_mint){
				echo "CapaCityMin**".$totsmv."**".$sales_target_mint;
				disconnect($con);die;
			}
		}
		
		if($capacity_exceed_level==4){
			$sql_po=sql_select("SELECT a.po_quantity as po_quantity, a.po_total_price as po_total_price from wo_po_break_down a, wo_po_details_master b where b.job_no=a.job_no_mst and b.company_name=$cbo_company_name $poBuyerCond and a.t_month=$month and  a.t_year=$year   and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0");// and b.team_leader=$cbo_team_leader 
			$po_quantity=0;
			foreach($sql_po as $row_po){
				$po_quantity+=$row_po[csf('po_quantity')];
			}
			$totPoqty=$po_quantity+str_replace("'","",$txt_po_quantity);
			if($totPoqty>$sales_target_qty){
				echo "CapaCityQty**".$totPoqty."**".$sales_target_qty;
				disconnect($con);die;
			}
		}
		
		if($capacity_exceed_level==5){
			$sql_po=sql_select("SELECT a.po_quantity as po_quantity, a.po_total_price as po_total_price from wo_po_break_down a, wo_po_details_master b where b.job_no=a.job_no_mst and b.company_name=$cbo_company_name $poBuyerCond  and a.t_month=$month and  a.t_year=$year  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0");//and  b.team_leader=$cbo_team_leader 
			$po_total_price=0;
			foreach($sql_po as $row_po){
				$po_total_price+=$row_po[csf('po_total_price')];
			}
			$totPrice=$po_total_price + str_replace("'","",$txt_amount);
			if($totPrice > $sales_target_value){
				echo "CapaCityValue**".$totPrice."**".$sales_target_value;
				disconnect($con);die;
			}
		}
		if($capacity_exceed_level==6){
			
			$sql_po=sql_select("SELECT a.po_quantity as po_quantity, a.po_total_price as po_total_price, b.set_smv from wo_po_break_down a, wo_po_details_master b where b.job_no=a.job_no_mst and b.company_name=$cbo_company_name $poBuyerCond  and a.t_month=$month and  a.t_year=$year    and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0");//and b.team_leader=$cbo_team_leader
			$smv=0;
			foreach($sql_po as $row_po){
				$smv+=$row_po[csf('po_quantity')]*$row_po[csf('set_smv')];
			}
			$curr_smv=str_replace("'","",$txt_po_quantity)*str_replace("'","",$tot_smv_qnty);
			$totsmv=$smv+$curr_smv;
			if($totsmv>$sales_target_mint){
				//echo "CapaCityMin";
				echo "CapaCityMin**".$totsmv."**".$sales_target_mint;
				disconnect($con);die;
			}
		}
		
		if($capacity_exceed_level==12){ //Working Company
			$pub_shipment_date=str_replace("'","",$txt_pub_shipment_date);
			if($db_type==2)
			{
			$date_from=change_date_format($pub_shipment_date,'','',1);
			$date_to=change_date_format($pub_shipment_date,'','',1);
			$dateFrom= explode("-",$date_from);
			$fromdate="01".'-'.$dateFrom[1].'-'.$dateFrom[2];
			$second_month_ldate=date("Y-M-t",strtotime($date_to));
		    $ship_last_day=change_date_format($second_month_ldate,'','',1);
			$pub_date_upto="and  a.pub_shipment_date BETWEEN '$fromdate' AND '$ship_last_day'";
			}
			else
			{
				$date_from=change_date_format($pub_shipment_date,'yyyy-mm-dd');
				$date_to=change_date_format($pub_shipment_date,'yyyy-mm-dd');
				$second_month_ldate=date("Y-m-t",strtotime($date_to));
				$dateFrom= explode("-",$date_from);
				$fromdate="01".'-'.$dateFrom[1].'-'.$dateFrom[2];
				$ship_last_day=change_date_format($second_month_ldate,'yyyy-mm-dd');
				$pub_date_upto=" and  a.pub_shipment_date BETWEEN '$fromdate' AND '$ship_last_day' ";
			}
			$sql_po="SELECT b.company_name,b.style_owner,b.buyer_name,a.po_quantity as po_quantity, a.po_total_price as po_total_price, b.set_smv from wo_po_break_down a, wo_po_details_master b where b.job_no=a.job_no_mst and b.style_owner=$cbo_working_company_id $w_poBuyerCond  $allocat_buyer_cond and a.t_month=$month and  a.t_year=$year  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.working_location_id = $cbo_working_location_id $pub_date_upto";
		
			$w_tot_prev_po_qty=0;$w_tot_prev_po_qty_same=0;
			$w_sql_po=sql_select($sql_po);
			foreach($w_sql_po as $row_po){
				$allcat_buyer_name=$buyer_allocate_percent_arr[$row_po[csf('buyer_name')]];
				//$allo_buyer_percn=$buyer_allocate_percent_arr[$buyer_id];
				//if($allo_buyer_percn=='' || $allo_buyer_percn==0) $allo_buyer_percn=0;else $allo_buyer_percn=$allo_buyer_percn;
				//$w_tot_prev_po_qty+=$row_po[csf('po_quantity')]*$row_po[csf('set_smv')];
				if($w_buyer_allocation_maintain==1)//Yes
				{
					if($lc_company_id==$w_company_id)
					{
						$allocat_buyer_id=$com_buyer_allocate_arr[$buyer_id];
						
						if(($allocat_buyer_id!=$buyer_id) && ($buyer_remain_allocate_percent>0))
						{
							$w_tot_prev_po_qty+=$row_po[csf('po_quantity')]*$row_po[csf('set_smv')];
						}
						else
						{
							$w_tot_prev_po_qty+=$row_po[csf('po_quantity')]*$row_po[csf('set_smv')];
						}
					}
					else
					{
						$w_tot_prev_po_qty+=$row_po[csf('po_quantity')]*$row_po[csf('set_smv')];
					}
				}
				else
				{
					$w_tot_prev_po_qty+=$row_po[csf('po_quantity')]*$row_po[csf('set_smv')];
				}
			}
			if($w_buyer_allocation_maintain==1)//Yes
			{
				$tot_buyer_capacity_min=$tot_company_capacity_min;
				$buyer_allocate_percent=$buyer_allocate_percent_arr[$buyer_id];
				$allocat_buyer_id=$com_buyer_allocate_arr[$buyer_id];
				
				if($lc_company_id==$w_company_id)
				{
					if(($allocat_buyer_id!=$buyer_id) && ($buyer_remain_allocate_percent>0))
					{
						/*$buyer_allocate_percent=$buyer_remain_allocate_percent;
						$total_company_capacity_min=$tot_buyer_capacity_min-$w_tot_prev_po_qty;
						$w_curr_po_qty=str_replace("'","",$txt_po_quantity)*str_replace("'","",$tot_smv_qnty);
						$w_tot_po_qty=$w_curr_po_qty;*/
						
						$buyer_allocate_percent=$buyer_remain_allocate_percent;
						$total_company_capacity_min=($tot_buyer_capacity_min*$buyer_allocate_percent)/100;
						$w_curr_po_qty=str_replace("'","",$txt_po_quantity)*str_replace("'","",$tot_smv_qnty);
						$w_tot_po_qty=$w_tot_prev_po_qty+$w_curr_po_qty;
						//echo  $w_tot_po_qty.'A'.$buyer_allocate_percent;
					}
					else
					{
						$buyer_allocate_percent=$buyer_allocate_percent_arr[$buyer_id];
						$total_company_capacity_min=($tot_buyer_capacity_min*$buyer_allocate_percent)/100;
						$w_curr_po_qty=str_replace("'","",$txt_po_quantity)*str_replace("'","",$tot_smv_qnty);
						$w_tot_po_qty=$w_tot_prev_po_qty+$w_curr_po_qty;
						//echo  $w_tot_po_qty.'b';
					}
					
				}
				else
				{
					$buyer_allocate_percent=100-$tot_allocation_percentage;
					$total_company_capacity_min=($tot_buyer_capacity_min*$buyer_allocate_percent)/100;
					$w_curr_po_qty=str_replace("'","",$txt_po_quantity)*str_replace("'","",$tot_smv_qnty);
					$w_tot_po_qty=$w_tot_prev_po_qty+$w_curr_po_qty;
				}
				
				//$total_company_capacity_min=($tot_buyer_capacity_min*$buyer_allocate_percent)/100;
				//$w_curr_po_qty=str_replace("'","",$txt_po_quantity)*str_replace("'","",$tot_smv_qnty);
				//$w_tot_po_qty=$w_tot_prev_po_qty+$w_curr_po_qty;
			}
			else
			{
				$total_company_capacity_min=$tot_company_capacity_min;
				$w_curr_po_qty=str_replace("'","",$txt_po_quantity)*str_replace("'","",$tot_smv_qnty);
				$w_tot_po_qty=$w_tot_prev_po_qty+$w_curr_po_qty;
			}
			//echo "10**".$w_tot_po_qty.'='.$total_company_capacity_min.'='.$tot_buyer_capacity_min.'='.$buyer_allocate_percent.'m';die;
			//echo "10**".$allocat_buyer_id.'='.$buyer_id.'='.$buyer_remain_allocate_percent.'='.$total_company_capacity_min;die;
			if($w_tot_po_qty>$total_company_capacity_min){
				//echo "CapaCityMin";
				echo "WorkingCapacityMin**".$w_tot_po_qty."**".$total_company_capacity_min;
				disconnect($con);die;
			}
		}
		//==============================capacity Validation ==============================
		
		$id=return_next_id("id", "wo_po_break_down", 1) ;
		//confirm and projection check for issue id 6952 
		if (str_replace("'","",$cbo_order_status)==1)
		{
			$field_array="id, job_no_mst, job_id, is_confirmed, po_number, po_received_date, pub_shipment_date, shipment_date, factory_received_date, po_quantity, unit_price, po_total_price, excess_cut, plan_cut, details_remarks, delay_for, packing, grouping, projected_po_id, tna_task_from_upto, t_year, t_month, file_no, is_deleted, status_active, inserted_by, insert_date, sc_lc, with_qty";
			$data_array="(".$id.",".$update_id.",".$hidd_job_id.",".$cbo_order_status.",".$txt_po_no.",".$txt_po_received_date.",".$txt_pub_shipment_date.",".$org_shipment_date.",".$txt_factory_rec_date.",".$txt_po_quantity.",".$txt_avg_price.",".$txt_amount.",".$txt_excess_cut.",".$txt_plan_cut.",".$txt_details_remark.",".$cbo_delay_for.",".$packing.",".$txt_grouping.",".$cbo_projected_po.",".$cbo_tna_task.",'".date("Y",strtotime(str_replace("'","",$txt_org_shipment_date)))."','".date("m",strtotime(str_replace("'","",$txt_org_shipment_date)))."',".$txt_file_no.",0,".$cbo_status.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$txt_sc_lc.",".$with_qty.")";
		}
		else
		{
			$field_array="id, job_no_mst,job_id, is_confirmed, po_number, po_received_date, pub_shipment_date, shipment_date, factory_received_date, po_quantity, unit_price, original_avg_price, po_total_price, excess_cut, plan_cut, details_remarks, delay_for, packing, grouping, projected_po_id, tna_task_from_upto, t_year, t_month, original_po_qty, file_no, is_deleted, status_active, inserted_by, insert_date, sc_lc, with_qty";
			$data_array="(".$id.",".$update_id.",".$hidd_job_id.",".$cbo_order_status.",".$txt_po_no.",".$txt_po_received_date.",".$txt_pub_shipment_date.",".$org_shipment_date.",".$txt_factory_rec_date.",".$txt_po_quantity.",".$txt_avg_price.",".$txt_avg_price.",".$txt_amount.",".$txt_excess_cut.",".$txt_plan_cut.",".$txt_details_remark.",".$cbo_delay_for.",".$packing.",".$txt_grouping.",".$cbo_projected_po.",".$cbo_tna_task.",'".date("Y",strtotime(str_replace("'","",$txt_org_shipment_date)))."','".date("m",strtotime(str_replace("'","",$txt_org_shipment_date)))."',".$txt_po_quantity.",".$txt_file_no.",0,".$cbo_status.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$txt_sc_lc.",".$with_qty.")";
		}
        //echo "5**insert into wo_po_break_down (".$field_array.") Values ".$data_array."";die;
		$rID=sql_insert("wo_po_break_down",$field_array,$data_array,0);		
//====================================================================================

      /*  if(str_replace("'","",$update_id_details)=="") //Stop it As per Rasel/CTO Consult...
		{
		$id1=return_next_id( "id", "wo_po_color_size_breakdown", 1 ) ;
		$field_array1="id,po_break_down_id,job_no_mst,color_mst_id,	size_mst_id,item_mst_id,country_mst_id, article_number, item_number_id,country_id,country_ship_date,size_number_id,color_number_id, order_quantity, order_rate,order_total ,excess_cut_perc, plan_cut_qnty,is_deleted,status_active,inserted_by,insert_date";
		$add_comma=0;
		$new_array_size=array();
		$new_array_color=array();
		$set_breck_down=explode('__',str_replace("'",'',$set_breck_down));
		if ( count($set_breck_down)>0 && $defult_color==1 && str_replace("'","",$cbo_order_status)==2)
		{
			$txt_avg_price_color=str_replace("'",'',$txt_avg_price)/str_replace("'",'',$tot_set_qnty);
			for($c=0;$c < count($set_breck_down);$c++)
			{
				$set_breck_down_arr=explode('_',$set_breck_down[$c]);
				$cbogmtsitem=$set_breck_down_arr[0];
				$cbogmtsitem_ratio=$set_breck_down_arr[1];
				$txtorderquantity=str_replace("'",'',$txt_po_quantity)*$cbogmtsitem_ratio;
				$txtorderamount=str_replace("'",'',$txt_avg_price_color)*$txtorderquantity;
				$txtorderplancut=str_replace("'",'',$txt_plan_cut)*$cbogmtsitem_ratio;
				
				if (!in_array(TBA,$new_array_color))
				 {
					  $color_id = return_id( TBA, $color_library, "lib_color", "id,color_name");  
					  $new_array_color[$color_id]=TBA;
				 }
				 else 
				 {
					 $color_id =  array_search(TBA, $new_array_color);
				 }
				 
				 if (!in_array(TBA,$new_array_size))
				 {
					  $size_id = return_id(TBA, $size_library, "lib_size", "id,size_name");   
					  $new_array_size[$size_id]=TBA;
				 }
				 else 
				 {
					$size_id =  array_search(TBA, $new_array_size); 
				 }
				 $txtarticleno="no article";
				if ($add_comma!=0) $data_array1 .=",";
				 $data_array1 .="(".$id1.",".$id.",".$update_id.",".$id1.",".$id1.",".$id1.",".$id1.",'".$txtarticleno."','".$cbogmtsitem."','245',".$txt_pub_shipment_date.",".$size_id.",".$color_id.",'".$txtorderquantity."',".$txt_avg_price_color.",'".$txtorderamount."',".$txt_excess_cut.",'".$txtorderplancut."',0,".$cbo_status.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				 $id1=$id1+1;
				 $add_comma++;
			}
		}
		}*/
		//echo "10**";
		/*if(str_replace("'","",$update_id_details)!="" && $defult_color==1 && str_replace("'","",$cbo_order_status)==2)
		{
			
		$color_mst=return_library_array( "select color_mst_id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=".$id." and status_active=1 and is_deleted=0 and color_mst_id !=0", "color_number_id", "color_mst_id" );
		 $size_mst=return_library_array( "select size_mst_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=".$id." and status_active=1 and is_deleted=0 and size_mst_id !=0", "size_number_id", "size_mst_id" );
		 $item_mst=return_library_array( "select item_mst_id,item_number_id from wo_po_color_size_breakdown where po_break_down_id=".$id." and status_active=1 and is_deleted=0 and item_mst_id !=0", "item_number_id", "item_mst_id" );
		 
		 $i=1;
		 $data_array1="";
		 $id_co=return_next_id( "id", "wo_po_color_size_breakdown", 1 ) ;
		 $field_array1="id,po_break_down_id,job_no_mst,color_mst_id, size_mst_id, item_mst_id, article_number, item_number_id,country_id,cutup_date,cutup,country_ship_date, size_number_id, color_number_id, order_quantity, order_rate, order_total,excess_cut_perc,plan_cut_qnty,country_remarks,color_order,size_order,is_deleted,status_active,inserted_by,insert_date";
		
		$sql_se_co=sql_select("select id, po_break_down_id, job_no_mst,color_mst_id,size_mst_id,item_mst_id,country_mst_id,article_number,item_number_id,country_id,cutup_date,cutup,country_ship_date,size_number_id, 	color_number_id,order_quantity,order_rate,order_total,excess_cut_perc,plan_cut_qnty,shiping_status,color_order,size_order,is_deleted,is_used,inserted_by,insert_date,updated_by,update_date,status_active,is_locked,country_remarks from wo_po_color_size_breakdown  where job_no_mst=$update_id and po_break_down_id=".$update_id_details."");
		foreach($sql_se_co as $row_se_co)
		{
			if (array_key_exists($row_se_co[csf('item_number_id')],$item_mst))
			  {
				 $item_mst_id=$item_mst[$row_se_co[csf('item_number_id')]];
			  }
			else
			  {
			     $item_mst[$row_se_co[csf('item_number_id')]]=$id_co;
				 $item_mst_id=$id_co;
			  }
			 if(array_key_exists($row_se_co[csf('color_number_id')],$color_mst))
			 {
				  $color_mst_id=$color_mst[$row_se_co[csf('color_number_id')]];	
			 }
			 
			 else
			 {
			   $color_mst[$row_se_co[csf('color_number_id')]]=$id_co;
			   $color_mst_id=$id_co;
			 }
			 
			 if(array_key_exists($row_se_co[csf('size_number_id')],$size_mst))
			 {
				 $size_mst_id=$size_mst[$row_se_co[csf('size_number_id')]];	 
			 }
		     else
			 {
				  $size_mst[$row_se_co[csf('size_number_id')]]=$id_co;
				  $size_mst_id=$id_co;
			 }
			 
			 if ($i!=1) $data_array1 .=",";
			$data_array1 .="(".$id_co.",".$id.",".$update_id.",'".$color_mst_id."','".$size_mst_id."','".$item_mst_id."','".$row_se_co[csf('article_number')]."','".$row_se_co[csf('item_number_id')]."','".$row_se_co[csf('country_id')]."','".$row_se_co[csf('cutup_date')]."','".$row_se_co[csf('cutup')]."','".$row_se_co[csf('country_ship_date')]."','".$row_se_co[csf('size_number_id')]."','".$row_se_co[csf('color_number_id')]."','".$row_se_co[csf('order_quantity')]."','".$row_se_co[csf('order_rate')]."','".$row_se_co[csf('order_total')]."','".$row_se_co[csf('excess_cut_perc')]."','".$row_se_co[csf('plan_cut_qnty')]."','".$row_se_co[csf('country_remarks')]."','".$row_se_co[csf('color_order')]."','".$row_se_co[csf('size_order')]."',0,".$row_se_co[csf('status_active')].",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			$id_co=$id_co+1;
			$i++;
		}
		}*/
		//echo $data_array1;
		//die;
		if(str_replace("'","",$update_id_details)=="")
		{
			$id1=return_next_id( "id", "wo_po_color_size_breakdown", 1 ) ;
			$field_array1="id, po_break_down_id, job_no_mst, job_id, color_mst_id, size_mst_id, item_mst_id, country_mst_id, article_number, item_number_id, country_id, country_ship_date, size_number_id, color_number_id, order_quantity, order_rate, order_total ,excess_cut_perc, plan_cut_qnty, is_deleted, status_active, inserted_by, insert_date";
			$add_comma=0;
			$new_array_size=array(); $new_array_color=array();
			$set_breck_down=explode('__',str_replace("'",'',$set_breck_down));
			if ( count($set_breck_down)>0 && $defult_color==1 && str_replace("'","",$cbo_order_status)==2)
			{
				$txt_avg_price_color=str_replace("'",'',$txt_avg_price)/str_replace("'",'',$tot_set_qnty);
				for($c=0;$c < count($set_breck_down);$c++)
				{
					$set_breck_down_arr=explode('_',$set_breck_down[$c]);
					$cbogmtsitem=$set_breck_down_arr[0];
					$cbogmtsitem_ratio=$set_breck_down_arr[1];
					$txtorderquantity=str_replace("'",'',$txt_po_quantity)*$cbogmtsitem_ratio;
					$txtorderamount=str_replace("'",'',$txt_avg_price_color)*$txtorderquantity;
					$txtorderplancut=str_replace("'",'',$txt_plan_cut)*$cbogmtsitem_ratio;
					
					/*if (!in_array(TBA,$new_array_color))
					 {
						  $color_id = return_id( TBA, $color_library, "lib_color", "id,color_name");  
						  $new_array_color[$color_id]=TBA;
					 }
					 else 
					 {
						 $color_id =  array_search(TBA, $new_array_color);
					 }*/
					$txt_tba_color = 'TBA';
					if($txt_tba_color !="")
					{
						if (!in_array($txt_tba_color,$new_array_size))
						{
							$color_id = return_id( $txt_tba_color, $color_library, "lib_color", "id,color_name","401");
							$new_array_color[$color_id]=$txt_tba_color;
						}
						else $color_id =  array_search($txt_tba_color, $new_array_color);
					}
					else
					{
						$color_id=0;
					}
					 
					 /*if (!in_array(TBA,$new_array_size))
					 {
						  $size_id = return_id(TBA, $size_library, "lib_size", "id,size_name");   
						  $new_array_size[$size_id]=TBA;
					 }
					 else 
					 {
						$size_id =  array_search(TBA, $new_array_size); 
					 }*/
					 $txtSizeName = 'TBA';
					if($txtSizeName="")
					{
						if (!in_array($txtSizeName,$new_array_size))
						{
							$size_id = return_id( $txtSizeName, $size_library, "lib_size", "id,size_name","401");
							$new_array_size[$size_id_val]=$txtSizeName;
						}
						else $size_id =  array_search($txtSizeName, $new_array_size);
					}
					else
					{
						$size_id=0;
					}

					 $txtarticleno="no article";
					if ($add_comma!=0) $data_array1 .=",";
					 $data_array1 .="(".$id1.",".$id.",".$update_id.",".$hidd_job_id.",".$id1.",".$id1.",".$id1.",".$id1.",'".$txtarticleno."','".$cbogmtsitem."','245',".$txt_pub_shipment_date.",".$size_id.",".$color_id.",'".$txtorderquantity."',".$txt_avg_price_color.",'".$txtorderamount."',".$txt_excess_cut.",'".$txtorderplancut."',0,".$cbo_status.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					 $id1=$id1+1;
					 $add_comma++;
				}
			}
		}
		
		if(str_replace("'","",$update_id_details)!="")
		{
			$color_mst=return_library_array( "select color_mst_id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=".$id." and status_active=1 and is_deleted=0 and color_mst_id !=0", "color_number_id", "color_mst_id" );
			$size_mst=return_library_array( "select size_mst_id,size_number_id from wo_po_color_size_breakdown where po_break_down_id=".$id." and status_active=1 and is_deleted=0 and size_mst_id !=0", "size_number_id", "size_mst_id" );
			$item_mst=return_library_array( "select item_mst_id,item_number_id from wo_po_color_size_breakdown where po_break_down_id=".$id." and status_active=1 and is_deleted=0 and item_mst_id !=0", "item_number_id", "item_mst_id" );
			
			$i=1;
			$data_array1="";
			$id_co=return_next_id( "id", "wo_po_color_size_breakdown", 1 ) ;
			$field_array1="id, po_break_down_id, job_no_mst, job_id, color_mst_id, size_mst_id, item_mst_id, article_number, item_number_id, country_id, cutup_date, cutup, country_ship_date, size_number_id, color_number_id, order_quantity, order_rate, order_total, excess_cut_perc, plan_cut_qnty, country_remarks, color_order, size_order, is_deleted, status_active, inserted_by, insert_date";
			
			$sql_se_co=sql_select("select id, po_break_down_id, job_no_mst, color_mst_id, size_mst_id, item_mst_id, country_mst_id, article_number, item_number_id, country_id, cutup_date, cutup, country_ship_date, size_number_id, color_number_id, order_quantity, order_rate, order_total, excess_cut_perc, plan_cut_qnty, shiping_status, color_order, size_order, is_deleted, is_used, inserted_by, insert_date, updated_by, update_date, status_active, is_locked, country_remarks from wo_po_color_size_breakdown where job_no_mst=$update_id and po_break_down_id=".$update_id_details." and is_deleted=0 and status_active=1");
			//echo "select id, po_break_down_id, job_no_mst,color_mst_id,size_mst_id,item_mst_id,country_mst_id,article_number,item_number_id,country_id,cutup_date,cutup,country_ship_date,size_number_id, 	color_number_id,order_quantity,order_rate,order_total,excess_cut_perc,plan_cut_qnty,shiping_status,color_order,size_order,is_deleted,is_used,inserted_by,insert_date,updated_by,update_date,status_active,is_locked,country_remarks from wo_po_color_size_breakdown  where job_no_mst=$update_id and po_break_down_id=".$update_id_details."";
			foreach($sql_se_co as $row_se_co)
			{
				if (array_key_exists($row_se_co[csf('item_number_id')],$item_mst))
				{
					$item_mst_id=$item_mst[$row_se_co[csf('item_number_id')]];
				}
				else
				{
					$item_mst[$row_se_co[csf('item_number_id')]]=$id_co;
					$item_mst_id=$id_co;
				}
				if(array_key_exists($row_se_co[csf('color_number_id')],$color_mst))
				{
					$color_mst_id=$color_mst[$row_se_co[csf('color_number_id')]];	
				}
				else
				{
					$color_mst[$row_se_co[csf('color_number_id')]]=$id_co;
					$color_mst_id=$id_co;
				}
				
				if(array_key_exists($row_se_co[csf('size_number_id')],$size_mst))
				{
					$size_mst_id=$size_mst[$row_se_co[csf('size_number_id')]];	 
				}
				else
				{
					$size_mst[$row_se_co[csf('size_number_id')]]=$id_co;
					$size_mst_id=$id_co;
				}
				
				if ($i!=1) $data_array1 .=",";
				$data_array1 .="(".$id_co.",".$id.",".$update_id.",".$hidd_job_id.",'".$color_mst_id."','".$size_mst_id."','".$item_mst_id."','".$row_se_co[csf('article_number')]."',".$row_se_co[csf('item_number_id')].",".$row_se_co[csf('country_id')].",'".$row_se_co[csf('cutup_date')]."','".$row_se_co[csf('cutup')]."','".$row_se_co[csf('country_ship_date')]."',".$row_se_co[csf('size_number_id')].",".$row_se_co[csf('color_number_id')].",".$row_se_co[csf('order_quantity')].",".$row_se_co[csf('order_rate')].",".$row_se_co[csf('order_total')].",'".$row_se_co[csf('excess_cut_perc')]."',".$row_se_co[csf('plan_cut_qnty')].",'".$row_se_co[csf('country_remarks')]."','".$row_se_co[csf('color_order')]."','".$row_se_co[csf('size_order')]."',0,".$row_se_co[csf('status_active')].",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				$id_co=$id_co+1;
				$i++;
			}
			$rID1=sql_insert("wo_po_color_size_breakdown",$field_array1,$data_array1,0);
		}
		//echo "10**insert into wo_po_color_size_breakdown (".$field_array1.") values ".$data_array1."";die;
		
		//============================================================================================
		
		 $sam=1;
		 $id_sm=return_next_id( "id", "wo_po_sample_approval_info", 1 ) ;
		 //$cbosampletype=return_field_value( 'id', 'lib_sample', 'sample_type=2 and status_active=1 and is_deleted=0' );
		 $sample_tag=sql_select("select tag_sample,sequ from lib_buyer_tag_sample where sequ!=0 and buyer_id=$cbo_buyer_name order by sequ");
		 $field_array_sm="id,job_no_mst,po_break_down_id,color_number_id,sample_type_id,status_active,is_deleted"; 		
		 $data_array_sample=sql_select("select a.id as po_id, b.color_number_id, min(b.id) as color_size_table_id from  wo_po_break_down a, wo_po_color_size_breakdown b where a.job_no_mst=b.job_no_mst and a.job_no_mst=$update_id and b.color_mst_id !=0 and a.id=b.po_break_down_id and  b.po_break_down_id=$id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.id,b.color_number_id order by a.id");
		 foreach($sample_tag as $sample_tag_row)
		 {
			 foreach ( $data_array_sample as $row_sam1 )
			 {
				 $dup_data=sql_select("select id from wo_po_sample_approval_info where job_no_mst=$update_id and po_break_down_id=".$row_sam1[csf('po_id')]." and color_number_id=".$row_sam1[csf('color_size_table_id')]." and sample_type_id='".$sample_tag_row[csf('tag_sample')]."' and status_active=1 and is_deleted=0");
				 list($idsm)=$dup_data;
				 if( $idsm[csf('id')] =='')
				 {
				  if ($sam!=1) $data_array_sm .=",";
				  $data_array_sm .="(".$id_sm.",".$update_id.",".$row_sam1[csf('po_id')].",".$row_sam1[csf('color_size_table_id')].",'".$sample_tag_row[csf('tag_sample')]."',1,0)";
				  $id_sm=$id_sm+1;
				  $sam=$sam+1;
				 }
			 }
		 }
		 
		 if($data_array_sm !='')
		 {
		 	$rID3=sql_insert("wo_po_sample_approval_info",$field_array_sm,$data_array_sm,1);
		 }
//============================================================================================
//============================================================================================
		 $lap=1;
		 $id_lap=return_next_id( "id", "wo_po_lapdip_approval_info", 1 ) ;
		// $cbosampletype=return_field_value( 'id', 'lib_sample', 'sample_type=2 and status_active=1 and is_deleted=0' );
		 $field_array_lap="id,job_no_mst,po_break_down_id,color_name_id,status_active,is_deleted"; 		
		 $data_array_lapdip=sql_select("select a.id as po_id, b.color_number_id, min(b.id) as color_size_table_id from  wo_po_break_down a, wo_po_color_size_breakdown b where a.job_no_mst=b.job_no_mst and a.job_no_mst=$update_id and b.color_mst_id !=0 and a.id=b.po_break_down_id and  b.po_break_down_id=$id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.id,b.color_number_id order by a.id");
		 foreach ( $data_array_lapdip as $row_lap1 )
		 {
			 $dup_lap=sql_select("select id from wo_po_lapdip_approval_info where job_no_mst=$update_id and po_break_down_id=".$row_lap1[csf('po_id')]." and color_name_id=".$row_lap1[csf('color_number_id')]."  and status_active=1 and is_deleted=0");
			 list($idlap)=$dup_lap;
			 if( $idlap[csf('id')] =='')
			 {
			  if ($lap!=1) $data_array_lap .=",";
			  $data_array_lap .="(".$id_lap.",".$update_id.",".$row_lap1[csf('po_id')].",".$row_lap1[csf('color_number_id')].",1,0)";
			  $id_lap=$id_lap+1;
			  $lap=$lap+1;
			 }
		 }
		 if($data_array_lap !='')
		 {
		 	$rID4=sql_insert("wo_po_lapdip_approval_info",$field_array_lap,$data_array_lap,1);
		 }
		//============================================================================================
		
		$return_data=update_job_mast($update_id);//define in common_functions.php
		update_cost_sheet($update_id);
		//=============================================================================================
		if($db_type==0)
		{
			if($rID )
			{
				mysql_query("COMMIT");  
				echo "0**".$rID."**".$return_data[1]."**".$return_data[2]."**".$return_data[3]."**".$return_data[4]."**".$return_data[5];
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$rID."**".$return_data[1]."**".$return_data[2]."**".$return_data[3]."**".$return_data[4]."**".$return_data[5];
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID )
			{
				oci_commit($con);  
				echo "0**".$rID."**".$return_data[1]."**".$return_data[2]."**".$return_data[3]."**".$return_data[4]."**".$return_data[5];
			}
			else
			{
				oci_rollback($con); 
				echo "10**".$rID."**".$return_data[1]."**".$return_data[2]."**".$return_data[3]."**".$return_data[4]."**".$return_data[5];
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
		if($is_precost_arr!=5 && str_replace("'","",$cbo_order_status)==1)
		{
			$sql_data=sql_select("select approved from wo_pre_cost_mst where job_no=$update_id and is_deleted=0 and status_active=1");
			$isapproved=$sql_data[0][csf("approved")];
			if($isapproved==1 || $isapproved==3)
			{
				echo "16**Pre Cost Approved, Any Change will be not allowed.";
				disconnect($con);die;
			}
		}
		
		$data_shipDate_vari="";
		$sql_shipDate_vari=sql_select("select duplicate_ship_date from variable_order_tracking where company_name=$cbo_company_name and variable_list=29");
		foreach($sql_shipDate_vari as $row_shipDate_vari)
		{
			$data_shipDate_vari=$row_shipDate_vari[csf('duplicate_ship_date')];	
		}
		if($data_shipDate_vari==1)
		{
		$txt_pub_shipment_date_cond="and pub_shipment_date=$txt_pub_shipment_date";	
		}
		else
		{
		$txt_pub_shipment_date_cond="";	
		}
		/*if (is_duplicate_field( "po_number", "wo_po_break_down", "po_number=$txt_po_no and job_no_mst=$update_id and pub_shipment_date=$txt_pub_shipment_date and po_quantity= $txt_po_quantity   and id!=$update_id_details and is_deleted=0" ) == 1)
		{
			echo "11**0"; 
			die;
		}*/
		
		if (is_duplicate_field( "po_number", "wo_po_break_down", "po_number=$txt_po_no and job_no_mst=$update_id  $txt_pub_shipment_date_cond and id!=$update_id_details and is_deleted=0 and status_active=1" ) == 1)
		{
			echo "11**0"; 
			disconnect($con);die;
		}
		
		$color_size_value=sql_select("select sum(b.order_quantity/a.total_set_qnty) as order_qnty from wo_po_details_master a, wo_po_color_size_breakdown b where a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1  and b.is_deleted=0 and b.po_break_down_id=$update_id_details");
		$color_size_qnty=$color_size_value[0][csf("order_qnty")]*1;
		if($color_size_qnty) $color_size_qnty=$color_size_qnty;else $color_size_qnty=0;
		$po_qnty=str_replace("'","",$txt_po_quantity)*1;
		if($color_size_qnty>0)
		{
			if($po_qnty<$color_size_qnty && str_replace("'","",$with_qty)==1)
			{
				echo "50**Order Quantity Not Allowed Less Then Color Size Breakdown Quantity.";disconnect($con);die;
			}
		}
		
		$org_shipment_date=$txt_org_shipment_date;
		if(trim($org_shipment_date,"'")=="")
		{
			$org_shipment_date=$txt_pub_shipment_date;
		}
		
		$txt_pub_shipment_date=$txt_pub_shipment_date;
		if(trim($txt_pub_shipment_date,"'")=="")
		{
			$txt_pub_shipment_date=$txt_org_shipment_date;
		}
		//==============================Lead Time Validation ==============================
		$min_lead_time_control=2;
		$sql_min_lead_time_control=sql_select("select min_lead_time_control from variable_order_tracking where company_name=$cbo_company_name and variable_list=51");
		foreach($sql_min_lead_time_control as $row_min_lead_time_control){
			$min_lead_time_control=$row_min_lead_time_control[csf('min_lead_time_control')];
		}
		 
		$received_date=date('Y-m-d',strtotime(str_replace("'","",$txt_po_received_date)));
        $pub_shipment_date=date('Y-m-d',strtotime(str_replace("'","",$txt_pub_shipment_date)));
        $dDiff=datediff( 'd', $received_date, $pub_shipment_date, $using_timestamps = false );
		$year=date("Y",strtotime(str_replace("'","",$org_shipment_date)));
	    $month= (int) date("m",strtotime(str_replace("'","",$org_shipment_date)));
		
		$min_leadtime_allocation=0;
		$sql_leadtime_vari=sql_select("select min_allocation from lib_min_lead_time_mst a, lib_min_lead_time_dtls b where a.id=b.mst_id and a.company_id=$cbo_company_name and year_id='$year' and a.month_id='$month'  and b.buyer_id=$cbo_buyer_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id");//and a.location_id=$cbo_location_name
		foreach($sql_leadtime_vari as $row_leadtime_vari){
			$min_leadtime_allocation=$row_leadtime_vari[csf('min_allocation')];	
		}
		if($dDiff < $min_leadtime_allocation && $min_lead_time_control==1){
			echo "LeadTime**0**".$min_leadtime_allocation;
			disconnect($con);die;
		}
		
		//=============================================================
		//==============================capacity Validation ==============================
		if(str_replace("'","",$cbo_status)==1){
			/*$curr_po_quantity=0;
			$po_total_price=0;
			$sql_curr_po=sql_select("select po_quantity,po_total_price from wo_po_break_down where id=$update_id_details");
			foreach($sql_curr_po as $row_curr_po){
				$curr_po_quantity=$row_curr_po[csf('po_quantity')];;
				$po_total_price=$row_curr_po[csf('po_total_price')];;
			}*/
			
			
			$buyer_allocation_maintain=2;
			$capacity_exceed_level=0;
			$sql_capa_vari=sql_select("select buyer_allocation_maintain, capacity_exceed_level from variable_order_tracking where company_name=$cbo_company_name and variable_list=52");
			foreach($sql_capa_vari as $row_capa_vari){
				$buyer_allocation_maintain=$row_capa_vari[csf('buyer_allocation_maintain')];
				$capacity_exceed_level=$row_capa_vari[csf('capacity_exceed_level')];	
			}
			
			
			$capaBuyerCond="";
			$poBuyerCond="";
			if($buyer_allocation_maintain==1){
				$capaBuyerCond="and a.buyer_id=$cbo_buyer_name";
				$poBuyerCond="and b.buyer_name=$cbo_buyer_name";
			}else{
				$capaBuyerCond="";
				$poBuyerCond="";
			}
			//==============================capacity Validation For Working Company==============================
			$lc_company_id=str_replace("'","",$cbo_company_name);
			$w_company_id=str_replace("'","",$cbo_working_company_id);
			$buyer_id=str_replace("'","",$cbo_buyer_name);
			
			$w_buyer_allocation_maintain=2;
			$w_capacity_exceed_level=0;
			$w_capa_vari=sql_select("select buyer_allocation_maintain, capacity_exceed_level from variable_order_tracking where company_name=$cbo_working_company_id and variable_list=52");
			foreach($w_capa_vari as $row_capa_vari){
				$w_buyer_allocation_maintain=$row_capa_vari[csf('buyer_allocation_maintain')];
				$w_capacity_exceed_level=$row_capa_vari[csf('capacity_exceed_level')];	
			}
			$w_capaBuyerCond="";
			$w_poBuyerCond="";
			if($w_buyer_allocation_maintain==1){
				if($lc_company_id==$w_company_id)
				{
					//$w_capaBuyerCond="and b.buyer_id=$cbo_buyer_name";
					//$w_poBuyerCond="and b.buyer_name=$cbo_buyer_name";
				}
				else
				{
					$w_capaBuyerCond="";
					//$w_poBuyerCond="";
				}
			
			}else{
				$w_capaBuyerCond="";
				$w_poBuyerCond="";
			}
			//End
		
			$year_month_name=$month.",".$year;
			$sales_target_qty=0;
			$sales_target_value=0;
			$sales_target_mint=0;
			$sql_sales_target=sql_select("select sum(sales_target_qty) as sales_target_qty,  sum(sales_target_value) as sales_target_value,   sum(sales_target_mint) as sales_target_mint from wo_sales_target_mst a, wo_sales_target_dtls b where a.id=b.sales_target_mst_id and a.company_id=$cbo_company_name   $capaBuyerCond and  b.year_month_name='$year_month_name' and a.status_active=1 and a.is_deleted=0  order by a.id");//and a.team_leader='$cbo_team_leader'and  a.starting_year='$year'
			foreach($sql_sales_target as $row_sales_target){
				$sales_target_qty=$row_sales_target[csf('sales_target_qty')];	;
				$sales_target_value=$row_sales_target[csf('sales_target_value')];	
				$sales_target_mint=$row_sales_target[csf('sales_target_mint')];
			}
			
			if($w_buyer_allocation_maintain==1){
			$sql_allowcat="select  b.buyer_id, b.allocation_percentage FROM lib_capacity_allocation_mst a,lib_capacity_allocation_dtls b
				 where a.id=b.mst_id AND a.company_id=$cbo_working_company_id   AND a.month_id=$month AND a.year_id=$year  and b.allocation_percentage>0 and b.status_active=1 and 
			b.is_deleted=0 $w_capaBuyerCond";
			 $sql_allowcat_result=sql_select($sql_allowcat);
				 $tot_allocation_percentage=0;
				foreach($sql_allowcat_result as $row)
				{
					$buyer_allocate_percent_arr[$row[csf('buyer_id')]]=$row[csf('allocation_percentage')];
					$com_buyer_allocate_arr[$row[csf('buyer_id')]]=$row[csf('buyer_id')];
					$tot_allocation_percentage+=$row[csf('allocation_percentage')];
					$allocat_buyer_id.=$row[csf('buyer_id')].',';
				}
				$buyer_remain_allocate_percent=100-$tot_allocation_percentage;
				if($lc_company_id!=$w_company_id)
				{
					$allocat_buyer_ids=rtrim($allocat_buyer_id,',');
					if($allocat_buyer_ids!='') $allocat_buyer_cond="and b.buyer_name not in($allocat_buyer_ids) ";else $allocat_buyer_cond='';
				}
				$w_poBuyerCond='';
				if($lc_company_id==$w_company_id)
				{
					$allocat_buyer_id=$com_buyer_allocate_arr[$buyer_id];
					
					if(($allocat_buyer_id!=$buyer_id) && ($buyer_remain_allocate_percent>0))
					{
						//if($buyer_id!=0) $w_poBuyerCond="and b.buyer_name not in($buyer_id) ";else $w_poBuyerCond='';
						if($allocat_buyer_ids!='') $allocat_buyer_cond="and b.buyer_name not in($allocat_buyer_ids) ";else $allocat_buyer_cond='';
					}
					else
					{
						if($buyer_id!=0) $w_poBuyerCond="and b.buyer_name in($buyer_id) ";else $w_poBuyerCond='';
					}
				}
				
			}
			$sql_con_capa="SELECT   sum(d.capacity_min) as capacity_min FROM  lib_capacity_calc_mst c,  lib_capacity_calc_dtls d
			WHERE c.id=d.mst_id AND c.comapny_id=$cbo_working_company_id AND c.year=$year and d.month_id=$month and  d.capacity_min>0 and c.status_active=1 and c.is_deleted=0 and c.location_id = $cbo_working_location_id";
			//echo "10**".$sql_con_capa;die;
		 	$con_capa_result=sql_select($sql_con_capa);
			foreach($con_capa_result as $row)
			{
				$tot_company_capacity_min=$row[csf('capacity_min')];
			}
			//echo "10**".$tot_company_capacity_min;die;
			if($capacity_exceed_level==1){
				$sql_po=sql_select("SELECT a.po_quantity as po_quantity, a.po_total_price as po_total_price from wo_po_break_down a, wo_po_details_master b where b.job_no=a.job_no_mst and b.company_name=$cbo_company_name $poBuyerCond  and a.t_month=$month and  a.t_year=$year and a.is_confirmed=1   and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.id !=$update_id_details");//and b.team_leader=$cbo_team_leader
				$po_quantity=0;
				foreach($sql_po as $row_po){
					$po_quantity+=$row_po[csf('po_quantity')];
				}
				$totPoqty=$po_quantity+str_replace("'","",$txt_po_quantity);
				if($totPoqty>$sales_target_qty){
					echo "CapaCityQty**".$totPoqty."**".$sales_target_qty;
					disconnect($con);die;
				}
			}
			
			if($capacity_exceed_level==2){
				$sql_po=sql_select("SELECT a.po_quantity as po_quantity, a.po_total_price as po_total_price from wo_po_break_down a, wo_po_details_master b where b.job_no=a.job_no_mst and b.company_name=$cbo_company_name $poBuyerCond and a.t_month=$month and  a.t_year=$year and a.is_confirmed=1  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.id !=$update_id_details");//and  b.team_leader=$cbo_team_leader
				$po_total_price=0;
				foreach($sql_po as $row_po){
					$po_total_price+=$row_po[csf('po_total_price')];
				}
				$totPrice=$po_total_price+str_replace("'","",$txt_amount);
				if($totPrice>$sales_target_value){
					echo "CapaCityValue**".$totPrice."**".$sales_target_value;
					disconnect($con);die;
				}
			}
			
			if($capacity_exceed_level==3){
				$sql_po=sql_select("SELECT a.po_quantity as po_quantity, a.po_total_price as po_total_price, b.set_smv from wo_po_break_down a, wo_po_details_master b where b.job_no=a.job_no_mst and b.company_name=$cbo_company_name $poBuyerCond and a.t_month=$month and  a.t_year=$year and a.is_confirmed=1  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.id !=$update_id_details");//and b.team_leader=$cbo_team_leader 
				$smv=0;
				foreach($sql_po as $row_po){
					$smv+=$row_po[csf('po_quantity')]*$row_po[csf('set_smv')];
				}
				$curr_smv=str_replace("'","",$txt_po_quantity)*str_replace("'","",$tot_smv_qnty);
				$totsmv=$smv+$curr_smv;
				if($totsmv>$sales_target_mint){
					echo "CapaCityMin**".$po_quantity."**".$sales_target_qty;
					disconnect($con);die;
				}
			}
			
			if($capacity_exceed_level==4){
				$sql_po=sql_select("SELECT a.po_quantity as po_quantity, a.po_total_price as po_total_price from wo_po_break_down a, wo_po_details_master b where b.job_no=a.job_no_mst and b.company_name=$cbo_company_name $poBuyerCond and a.t_month=$month and  a.t_year=$year  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.id !=$update_id_details");//and b.team_leader=$cbo_team_leader 
				$po_quantity=0;
				foreach($sql_po as $row_po){
					$po_quantity+=$row_po[csf('po_quantity')];
				}
				$totPoqty=$po_quantity+str_replace("'","",$txt_po_quantity);
				if($totPoqty>$sales_target_qty){
					echo "CapaCityQty**".$totPoqty."**".$sales_target_qty;
					disconnect($con);die;
				}
			}
			
			if($capacity_exceed_level==5){
				$sql_po=sql_select("SELECT a.po_quantity as po_quantity, a.po_total_price as po_total_price from wo_po_break_down a, wo_po_details_master b where b.job_no=a.job_no_mst and b.company_name=$cbo_company_name $poBuyerCond  and a.t_month=$month and  a.t_year=$year  and b.status_active=1 and b.is_deleted=0  and a.status_active=1 and a.is_deleted=0 and a.id !=$update_id_details");//and  b.team_leader=$cbo_team_leader
				$po_total_price=0;
				foreach($sql_po as $row_po){
					$po_total_price+=$row_po[csf('po_total_price')];
				}
				$totPrice=$po_total_price+str_replace("'","",$txt_amount);
				if($totPrice>$sales_target_value){
					echo "CapaCityValue**".$totPrice."**".$sales_target_value;
					disconnect($con);die;
				}
			}
			if($capacity_exceed_level==6){
				$sql_po=sql_select("SELECT a.po_quantity as po_quantity, a.po_total_price as po_total_price, b.set_smv from wo_po_break_down a, wo_po_details_master b where b.job_no=a.job_no_mst and b.company_name=$cbo_company_name $poBuyerCond  and a.t_month=$month and  a.t_year=$year  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.id !=$update_id_details");//and b.team_leader=$cbo_team_leader
				$smv=0;
				foreach($sql_po as $row_po){
					$smv+=$row_po[csf('po_quantity')]*$row_po[csf('set_smv')];
				}
				$curr_smv=str_replace("'","",$txt_po_quantity)*str_replace("'","",$tot_smv_qnty);
				$totsmv=$smv+$curr_smv;
				if($totsmv > $sales_target_mint){
					echo "CapaCityMin**".$totsmv."**".$sales_target_mint;
					disconnect($con);die;
				}
			}
			
			if($capacity_exceed_level==12){ //Working Company
			$pub_shipment_date=str_replace("'","",$txt_pub_shipment_date);
			if($db_type==2)
			{
			$date_from=change_date_format($pub_shipment_date,'','',1);
			$date_to=change_date_format($pub_shipment_date,'','',1);
			$dateFrom= explode("-",$date_from);
			$fromdate="01".'-'.$dateFrom[1].'-'.$dateFrom[2];
			$second_month_ldate=date("Y-M-t",strtotime($date_to));
		    $ship_last_day=change_date_format($second_month_ldate,'','',1);
			$pub_date_upto="and  a.pub_shipment_date BETWEEN '$fromdate' AND '$ship_last_day'";
			}
			else
			{
				$date_from=change_date_format($pub_shipment_date,'yyyy-mm-dd');
				$date_to=change_date_format($pub_shipment_date,'yyyy-mm-dd');
				$second_month_ldate=date("Y-m-t",strtotime($date_to));
				$dateFrom= explode("-",$date_from);
				$fromdate="01".'-'.$dateFrom[1].'-'.$dateFrom[2];
				$ship_last_day=change_date_format($second_month_ldate,'yyyy-mm-dd');
				$pub_date_upto=" and  a.pub_shipment_date BETWEEN '$fromdate' AND '$ship_last_day' ";
			}
			$w_sql_po=sql_select("SELECT b.company_name,b.style_owner,b.buyer_name,a.po_quantity as po_quantity, a.po_total_price as po_total_price, b.set_smv from wo_po_break_down a, wo_po_details_master b where b.job_no=a.job_no_mst and b.style_owner=$cbo_working_company_id $w_poBuyerCond $allocat_buyer_cond  and a.t_month=$month and  a.t_year=$year  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.id !=$update_id_details and b.working_location_id = $cbo_working_location_id $pub_date_upto");//and b.team_leader=$cbo_team_leader
			//echo "10**SELECT b.company_name,b.style_owner,b.buyer_name,a.po_quantity as po_quantity, a.po_total_price as po_total_price, b.set_smv from wo_po_break_down a, wo_po_details_master b where b.job_no=a.job_no_mst and b.style_owner=$cbo_working_company_id $w_poBuyerCond $allocat_buyer_cond  and a.t_month=$month and  a.t_year=$year  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and a.id !=$update_id_details and b.working_location_id = $cbo_working_location_id $pub_date_upto";die;
			
			$w_tot_prev_po_qty=0;
			foreach($w_sql_po as $row_po){
				$allcat_buyer_name=$buyer_allocate_percent_arr[$row_po[csf('buyer_name')]];
					//$w_tot_prev_po_qty+=$row_po[csf('po_quantity')]*$row_po[csf('set_smv')];
				if($w_buyer_allocation_maintain==1)//Yes
				{
					if($lc_company_id==$w_company_id)
					{
						$allocat_buyer_id=$com_buyer_allocate_arr[$buyer_id];
						
						if(($allocat_buyer_id!=$buyer_id) && ($buyer_remain_allocate_percent>0))
						{
							$w_tot_prev_po_qty+=$row_po[csf('po_quantity')]*$row_po[csf('set_smv')];
						}
						else
						{
							$w_tot_prev_po_qty+=$row_po[csf('po_quantity')]*$row_po[csf('set_smv')];
						}
					}
					else
					{
						$w_tot_prev_po_qty+=$row_po[csf('po_quantity')]*$row_po[csf('set_smv')];
					}
				}
				else
				{
					$w_tot_prev_po_qty+=$row_po[csf('po_quantity')]*$row_po[csf('set_smv')];
				}
				
			}
			$buyer_id=str_replace("'","",$cbo_buyer_name);
			if($w_buyer_allocation_maintain==1)//Yes
			{
				$tot_buyer_capacity_min=$tot_company_capacity_min;
				$buyer_allocate_percent=$buyer_allocate_percent_arr[$buyer_id];
				$allocat_buyer_id=$com_buyer_allocate_arr[$buyer_id];
				
				if($lc_company_id==$w_company_id)
				{
					if(($allocat_buyer_id!=$buyer_id) && ($buyer_remain_allocate_percent>0))
					{
						$buyer_allocate_percent=$buyer_remain_allocate_percent;
						$total_company_capacity_min=$tot_buyer_capacity_min-$w_tot_prev_po_qty;
						$w_curr_po_qty=str_replace("'","",$txt_po_quantity)*str_replace("'","",$tot_smv_qnty);
						$w_tot_po_qty=$w_tot_prev_po_qty+$w_curr_po_qty;
					}
					else
					{
						$buyer_allocate_percent=$buyer_allocate_percent_arr[$buyer_id];
						$total_company_capacity_min=($tot_buyer_capacity_min*$buyer_allocate_percent)/100;
						
						$w_curr_po_qty=str_replace("'","",$txt_po_quantity)*str_replace("'","",$tot_smv_qnty);
						$w_tot_po_qty=$w_tot_prev_po_qty+$w_curr_po_qty;
					}
				}
				else
				{
					$buyer_allocate_percent=100-$tot_allocation_percentage;
					$total_company_capacity_min=($tot_buyer_capacity_min*$buyer_allocate_percent)/100;
					
					$w_curr_po_qty=str_replace("'","",$txt_po_quantity)*str_replace("'","",$tot_smv_qnty);
					$w_tot_po_qty=$w_tot_prev_po_qty+$w_curr_po_qty;
				}
				
				//$total_company_capacity_min=($tot_buyer_capacity_min*$buyer_allocate_percent)/100;
				//$w_curr_po_qty=str_replace("'","",$txt_po_quantity)*str_replace("'","",$tot_smv_qnty);
				//$w_tot_po_qty=$w_tot_prev_po_qty+$w_curr_po_qty;
			}
			else
			{
				$total_company_capacity_min=$tot_company_capacity_min;
				$w_curr_po_qty=str_replace("'","",$txt_po_quantity)*str_replace("'","",$tot_smv_qnty);
				$w_tot_po_qty=$w_tot_prev_po_qty+$w_curr_po_qty;
			}
			//echo "10**".$w_tot_po_qty.'='.$total_company_capacity_min;die;
			if($w_tot_po_qty>$total_company_capacity_min){
				//echo "CapaCityMin";
				echo "WorkingCapacityMin**".$w_tot_po_qty."**".$total_company_capacity_min;
				disconnect($con);die;
			}
		 } //End
		}
		//==============================capacity Validation ==============================
		
		$prev_data=sql_select("SELECT is_confirmed, po_number, po_received_date, pub_shipment_date, shipment_date, factory_received_date, po_quantity, unit_price, po_total_price, excess_cut, plan_cut, details_remarks, delay_for, packing, grouping, projected_po_id, tna_task_from_upto, t_year, t_month, file_no, is_deleted, status_active, updated_by, update_date, po_number_prev, pub_shipment_date_prev FROM wo_po_break_down WHERE id=$update_id_details");
		foreach($prev_data as $rows)
		{
			$prev_po_no=$rows[csf('po_number')];
			$prev_order_status=$rows[csf('is_confirmed')];
			$prev_po_received_date=$rows[csf('po_received_date')];
			$prev_po_qty=$rows[csf('po_quantity')];
			$prev_pub_shipment_date=$rows[csf('pub_shipment_date')];
			$prev_status=$rows[csf('status_active')];
			$prev_org_shipment_date=$rows[csf('shipment_date')];
			$prev_factory_rec_date=$rows[csf('factory_received_date')];
			$prev_projected_po=$rows[csf('projected_po_id')];
			$prev_packing=$rows[csf('packing')];
			$prev_details_remark=$rows[csf('details_remarks')];
			$prev_file_no=$rows[csf('file_no')];
			$prev_avg_price=$rows[csf('unit_price')];
			$prev_excess_cut=$rows[csf('excess_cut')];
			$prev_plan_cut=$rows[csf('plan_cut')];
			$prev_status=$rows[csf('status_active')];
			$prev_updated_by=$rows[csf('updated_by')];
			$prev_update_date=$rows[csf('update_date')];
			
			$prev_pono=$rows[csf('po_number_prev')];
			$prev_pubship_date=$rows[csf('pub_shipment_date_prev')];
		}
		
		if($prev_po_no==str_replace("'","",$txt_po_no))
		{
			$pre_po_no=$prev_pono;
		}
		else $pre_po_no=$prev_po_no;
		
		if( change_date_format($prev_pub_shipment_date)==change_date_format(str_replace("'","",$txt_pub_shipment_date)))
		{
			$pre_pubship_date=$prev_pubship_date;
		}
		else $pre_pubship_date=$prev_pub_shipment_date;
		
		$field_array="is_confirmed*po_number*po_received_date*pub_shipment_date*shipment_date*factory_received_date*po_quantity*unit_price*po_total_price*excess_cut*plan_cut*details_remarks*delay_for*packing*grouping*projected_po_id*tna_task_from_upto*t_year*t_month*file_no*sc_lc*po_number_prev*pub_shipment_date_prev*with_qty*is_deleted*status_active*updated_by*update_date";
		$data_array="".$cbo_order_status."*".$txt_po_no."*".$txt_po_received_date."*".$txt_pub_shipment_date."*".$org_shipment_date."*".$txt_factory_rec_date."*".$txt_po_quantity."*".$txt_avg_price."*".$txt_amount."*".$txt_excess_cut."*".$txt_plan_cut."*".$txt_details_remark."*".$cbo_delay_for."*".$packing."*".$txt_grouping."*".$cbo_projected_po."*".$cbo_tna_task."*".date("Y",strtotime(str_replace("'","",$txt_org_shipment_date)))."*".date("m",strtotime(str_replace("'","",$txt_org_shipment_date)))."*".$txt_file_no."*".$txt_sc_lc."*'".$pre_po_no."'*'".$pre_pubship_date."'*".$with_qty_pop."*0*".$cbo_status."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		
//Log History Start.------------------------...REZA
$sql_con="is_confirmed=$cbo_order_status and po_number =$txt_po_no and job_no_mst=$update_id and po_received_date=$txt_po_received_date and pub_shipment_date=$txt_pub_shipment_date and shipment_date=$org_shipment_date and factory_received_date=$txt_factory_rec_date and po_quantity=$txt_po_quantity and unit_price=$txt_avg_price and po_total_price=$txt_amount and excess_cut=$txt_excess_cut and plan_cut=$txt_plan_cut and details_remarks=$txt_details_remark and delay_for=$cbo_delay_for and packing=$packing and grouping=$txt_grouping and projected_po_id=$cbo_projected_po and tna_task_from_upto=$cbo_tna_task and t_year=".date("Y",strtotime(str_replace("'","",$txt_org_shipment_date)))." and t_month=".date("m",strtotime(str_replace("'","",$txt_org_shipment_date)))." and file_no=$txt_file_no and id=$update_id_details and is_deleted=0";
$sql_con=str_replace("=''"," IS NULL ",$sql_con);
$is_duplicate=is_duplicate_field( "po_number", "wo_po_break_down", $sql_con );
		
		$log_id_mst = return_next_id( "id", "wo_po_update_log", 1 ) ;
		
		if($db_type==0)
		{
			$current_date = $pc_date_time;
		}
		else
		{
			$current_date = change_date_format(date("Y-m-d H:i:s",strtotime(add_time(date("H:i:s",time()),0))),'','',1);
		}
		
		$previous_po_qty=return_field_value("po_quantity","wo_po_break_down","job_no_mst=".$update_id." and id=".$update_id_details."");
		
		$log_update_date=return_field_value("update_date","wo_po_update_log","job_no=".$update_id." and po_id=".$update_id_details." order by id DESC");
		
		$log_update=date("Y-m-d", strtotime($log_update_date));
		$curr_date=date("Y-m-d", strtotime($current_date));
		
		if(($log_update=="" && $is_duplicate!=1) || ($log_update!=$curr_date && $is_duplicate!=1))
		{
			$field_array_history="id,entry_form,job_no,po_no,po_id,order_status,po_received_date,previous_po_qty,shipment_date,org_ship_date,po_status,t_year,t_month,fac_receive_date, projected_po, packing, remarks, file_no, avg_price, excess_cut_parcent, plan_cut,status,prev_update_date,prev_update_by,update_date,update_by";
			
			$data_array_history="(".$log_id_mst.",1,".$update_id.",'".$prev_po_no."',".$update_id_details.",'".$prev_order_status."','".$prev_po_received_date."','".$prev_po_qty."','".$prev_pub_shipment_date."','".$prev_org_shipment_date."','".$prev_status."','".date("Y",strtotime(str_replace("'","",$prev_org_shipment_date)))."','".date("m",strtotime(str_replace("'","",$prev_org_shipment_date)))."','".$prev_factory_rec_date."','".$prev_projected_po."','".$prev_packing."','".$prev_details_remark."','".$prev_file_no."','".$prev_avg_price."','".$prev_excess_cut."','".$prev_plan_cut."','".$prev_status."','".$prev_update_date."',".$prev_updated_by.",'".$current_date."',".$_SESSION['logic_erp']['user_id'].")";
			
			$rID3=sql_insert("wo_po_update_log",$field_array_history,$data_array_history,1);	
		
		
		}
		else if( $log_update==$curr_date)
		{
			
			$field_array_history="po_no*po_id*order_status*po_received_date*previous_po_qty*shipment_date*org_ship_date*po_status*fac_receive_date*projected_po*packing*remarks*file_no*avg_price*excess_cut_parcent*plan_cut*status*prev_update_date*prev_update_by*update_date*update_by";
			
			$data_array_history="'".$prev_po_no."'*".$update_id_details."*'".$prev_order_status."'*'".$prev_po_received_date."'*'".$prev_po_qty."'*'".$prev_pub_shipment_date."'*'".$prev_org_shipment_date."'*'".$prev_status."'*'".$prev_factory_rec_date."'*'".$prev_projected_po."'*'".$prev_packing."'*'".$prev_details_remark."'*'".$prev_file_no."'*'".$prev_avg_price."'*'".$prev_excess_cut."'*'".$prev_plan_cut."'*'".$prev_order_status."'*'".$prev_update_date."'*"."'*'".$prev_updated_by."'*"."'*'".$current_date."'*".$_SESSION['logic_erp']['user_id']."";
			
			$rID3=sql_update("wo_po_update_log",$field_array_history,$data_array_history,"po_id*update_date","".$update_id_details."*'".$log_update_date."'",1); 
		}
	
		
	//Log History end.-------------------------...REZA
	//$cbo_status;
		$status_id=str_replace("'","",$cbo_status);//Inactive/Cancel
		if($status_id>0)
		{
			//echo "10**update  wo_po_color_size_breakdown set status_active=".$status_id.",updated_by=".$_SESSION['logic_erp']['user_id'].",update_date='".$pc_date_time."'  where  po_break_down_id =$update_id_details";die;
		$rID=execute_query( "update  wo_po_color_size_breakdown set status_active=".$status_id.",updated_by=".$_SESSION['logic_erp']['user_id'].",update_date='".$pc_date_time."'  where  po_break_down_id =$update_id_details",1);
		}
		
		$rID=sql_update("wo_po_break_down",$field_array,$data_array,"id","".$update_id_details."",1);
		$rID2=execute_query( "update  wo_booking_mst set is_apply_last_update=2  where  job_no =".$update_id." and booking_type=1 and is_short=2 ",1);
		
		
		
		
//======================================================
		/*$new_array_size=array();
		$new_array_color=array();
		$color_mst=array();
		$size_mst=array();
		$item_mst=array();
		$color_size_break_down_array=explode('__',str_replace("'",'',$color_size_break_down));
		if($color_size_break_down_array[0]=="")
		{
			$color_size_break_down_array=array();
		}
		if ( count($color_size_break_down_array)>0)
		{
			for($c=0;$c < count($color_size_break_down_array);$c++)
			{
				 $color_size_break_down_arr=explode('_',$color_size_break_down_array[$c]);
				 $color_size_table_id=$color_size_break_down_arr[5];
				 $cbogmtsitem=$color_size_break_down_arr[6];
				 $txtarticleno=$color_size_break_down_arr[7];
				 $txtcolor=$color_size_break_down_arr[8];
				 $txtsize=$color_size_break_down_arr[9];
				 $txtorderquantity=$color_size_break_down_arr[10];
				 $txtorderrate=$color_size_break_down_arr[11];
				 $txtorderamount=$color_size_break_down_arr[12];
				 $txtorderexcesscut=$color_size_break_down_arr[13];
				 $txtorderplancut=$color_size_break_down_arr[14];
				 $cbostatus=$color_size_break_down_arr[15];
				 if (!in_array(str_replace("'","",$txtcolor),$new_array_color))
				 {
					  $color_id = return_id( str_replace("'","",$txtcolor), $color_library, "lib_color", "id,color_name");  
					  $new_array_color[$color_id]=str_replace("'","",$txtcolor);
				 }
				 else
				 {
					 $color_id =  array_search(str_replace("'","",$txtcolor), $new_array_color); 
				 }
				
				 if (!in_array(str_replace("'","",$txtsize),$new_array_size))
				 {
					  $size_id = return_id( str_replace("'","",$txtsize), $size_library, "lib_size", "id,size_name");   
					  $new_array_size[$size_id]=str_replace("'","",$txtsize);
				 }
				 else
				 {
					$size_id =  array_search(str_replace("'","",$txtsize), $new_array_size); 
				 }
				if($color_size_table_id!=0)
				{
					if(!in_array($cbogmtsitem,$item_mst))
					 {
						 $item_mst[$color_size_table_id]=$cbogmtsitem;
						 $item_mst_id=$color_size_table_id;
						 $color_mst= array();
					     $size_mst=array();
					 }
					 else
					 {
					   $item_mst_id=0;	 
					 }
					if(!in_array($color_id,$color_mst))
					 {
						 $color_mst[$color_size_table_id]=$color_id;
						 $color_mst_id=$color_size_table_id;
					 }
					 else
					 {
					   $color_mst_id=0;	 
					 }
					 
					 if(!in_array($size_id,$size_mst))
					 {
						 $size_mst[$color_size_table_id]=$size_id;
						 $size_mst_id=$color_size_table_id;
					 }
					 else
					 {
					   $size_mst_id=0;	 
					 }
					 
					 
					$field_array1="color_mst_id*size_mst_id*item_mst_id*article_number*item_number_id*size_number_id*color_number_id*order_quantity*order_rate*order_total*excess_cut_perc* plan_cut_qnty*is_deleted*status_active*inserted_by*insert_date";
					$data_array1="'".$color_mst_id."'*'".$size_mst_id."'*'".$item_mst_id."'*'".$txtarticleno."'*'".$cbogmtsitem."'*'".$size_id."'*'".$color_id."'*'".$txtorderquantity."'*'".$txtorderrate."'*'".$txtorderamount."'*'".$txtorderexcesscut."'*'".$txtorderplancut."'*0*'".$cbostatus."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
					$rID1=sql_update("wo_po_color_size_breakdown",$field_array1,$data_array1,"id","".$color_size_table_id."",1);
				 }
				
				if($color_size_table_id==0)
				{
					$id1=return_next_id( "id", "wo_po_color_size_breakdown", 1 ) ;
					 if(!in_array($color_id,$color_mst))
					 {
						 $color_mst[$id1]=$color_id;
						 $color_mst_id=$id1;
					 }
					 else
					 {
					   $color_mst_id=0;	 
					 }
					 if(!in_array($size_id,$size_mst))
					 {
						 $size_mst[$id1]=$size_id;
						 $size_mst_id=$id1;
					 }
					 else
					 {
					   $size_mst_id=0;	 
					 }
					 
					 if(!in_array($cbogmtsitem,$item_mst))
					 {
						 $item_mst[$id1] = $cbogmtsitem;
						 $item_mst_id=$id1;
					 }
					 else
					 {
					   $item_mst_id=0;	 
					 }
					 
					$field_array1="id,po_break_down_id,job_no_mst,color_mst_id,size_mst_id,item_mst_id,article_number, item_number_id,size_number_id,color_number_id, order_quantity, order_rate,order_total ,excess_cut_perc, plan_cut_qnty,is_deleted,status_active,inserted_by,insert_date";
					$data_array1 ="(".$id1.",".$update_id_details.",".$update_id.",".$color_mst_id.",".$size_mst_id.",".$item_mst_id.",'".$txtarticleno."','".$cbogmtsitem."','".$size_id."','".$color_id."','".$txtorderquantity."','".$txtorderrate."','".$txtorderamount."','".$txtorderexcesscut."','".$txtorderplancut."',0,'".$cbostatus."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					$rID1=sql_insert("wo_po_color_size_breakdown",$field_array1,$data_array1,1);
				 }
			}
			
		}*/
//=================================================
//=================================================
		$return_data= update_job_mast($update_id);//define in common_functions.php
		update_cost_sheet($update_id);
//==================================================
		$txt_job_no=str_replace("'","",$txt_job_no);
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "1**".$rID."**".$return_data[1]."**".$return_data[2]."**".$return_data[3]."**".$return_data[4]."**".$return_data[5];
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$rID."**".$return_data[1]."**".$return_data[2]."**".$return_data[3]."**".$return_data[4]."**".$return_data[5];
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con); 
				echo "1**".$rID."**".$return_data[1]."**".$return_data[2]."**".$return_data[3]."**".$return_data[4]."**".$return_data[5];
			}
			else{
				oci_rollback($con); 
				echo "10**".$rID."**".$return_data[1]."**".$return_data[2]."**".$return_data[3]."**".$return_data[4]."**".$return_data[5];
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)   // Delete Here
	{
		$con = connect();
		
		if (is_duplicate_field( "po_break_down_id", "pro_garments_production_mst", "po_break_down_id=$update_id_details and is_deleted=0" ) == 1)
		{
			echo "14**0"; 
			disconnect($con);die;
		}
		else if (is_duplicate_field( "order_id", "ppl_cut_lay_size", "order_id=$update_id_details and is_deleted=0" ) == 1)
		{
			echo "14**0"; 
			disconnect($con);die;
		}
		else if (is_duplicate_field( "po_breakdown_id", "pro_roll_details", "po_breakdown_id=$update_id_details and is_deleted=0 and status_active=1 and is_sales<>1 and booking_without_order=0" ) == 1)
		{
			echo "14**0"; 
			disconnect($con);die;
		}
		else if (is_duplicate_field( "po_break_down_id", "pro_bundle_mst", "po_break_down_id=$update_id_details and is_deleted=0" ) == 1)
		{
			echo "14**0"; 
			disconnect($con);die;
		}
		
		$sql_data=sql_select("select approved from wo_pre_cost_mst where job_no=$update_id and is_deleted=0 and status_active=1");
		$isapproved=$sql_data[0][csf("approved")];
		if($isapproved==1)
		{
			echo "16**Pre Cost Approved, Any Change will be not allowed.";
			disconnect($con);die;
		}
		
		$sql_booking_no=sql_select("select booking_no from wo_booking_dtls where po_break_down_id=".$update_id_details." and status_active=1 and is_deleted=0 group by booking_no");
		$booking_str="";
		foreach($sql_booking_no as $row)
		{
			if($booking_str=="") $booking_str=$row[csf('booking_no')]; else $booking_str.=', '.$row[csf('booking_no')];
		}
		
		if($booking_str!="")
		{
			echo "13**".$booking_str;
			disconnect($con);die;
		} 
		$field_array="status_active*is_deleted*updated_by*update_date";
		$data_array="'0'*'1'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		$rID=sql_delete("wo_po_break_down",$field_array,$data_array,"id","".$update_id_details."",1);
		$rID=execute_query( "update  wo_po_color_size_breakdown set status_active=0,is_deleted=1,updated_by=".$_SESSION['logic_erp']['user_id'].",update_date='".$pc_date_time."'  where  po_break_down_id =$update_id_details  ",1);
		
		$return_data=update_job_mast($update_id);//define in common_functions.php
		update_cost_sheet($update_id);
		$txt_job_no=str_replace("'","",$txt_job_no);
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "2**".$rID."**".$return_data[1]."**".$return_data[2]."**".$return_data[3]."**".$return_data[4]."**".$return_data[5];
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$rID."**".$return_data[1]."**".$return_data[2]."**".$return_data[3]."**".$return_data[4]."**".$return_data[5];
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con); 
				echo "2**".$rID."**".$return_data[1]."**".$return_data[2]."**".$return_data[3]."**".$return_data[4]."**".$return_data[5];
			}
			else{
				oci_rollback($con); 
				echo "10**".$rID."**".$return_data[1]."**".$return_data[2]."**".$return_data[3]."**".$return_data[4]."**".$return_data[5];
			}
		}
		//echo "2**".$rID."**".$return_data[1]."**".$return_data[2]."**".$return_data[3];
		disconnect($con);
		die;
	}
}

// function================

function get_tna_template__off( $remain_days, $tna_template, $buyer )
{ 
	global $tna_template_buyer;
	if(count($tna_template_buyer[$buyer])>0)
	{ 
		$n=count($tna_template_buyer[$buyer]); 
		for($i=0;$i<$n;$i++)
		{ 
			if($remain_days<=$tna_template_buyer[$buyer][$i]['lead']) 
			{
				if ($i!=0)
				{
					$up_day=$tna_template_buyer[$buyer][$i]['lead']-$remain_days;
					$low_day=$remain_days-$tna_template_buyer[$buyer][$i-1]['lead'];
					if ($up_day>=$low_day)
						return $tna_template_buyer[$buyer][$i-1]['id'];
					else
						return $tna_template_buyer[$buyer][$i]['id'];
				}
				else
				{
					return $tna_template_buyer[$buyer][$i]['id'];
				}
			}
		}
	}
	else
	{
		$n=count($tna_template); 
		for($i=0;$i<$n;$i++)
		{
			if($remain_days<=$tna_template[$i]['lead']) 
			{
				if ($i!=0)
				{
					$up_day=$tna_template[$i]['lead']-$remain_days;
					$low_day=$remain_days-$tna_template[$i-1]['lead'];
					if ($up_day>=$low_day)
						return $tna_template[$i-1]['id'];
					else

						return $tna_template[$i]['id'];
				}
				else
				{
					return $tna_template[$i]['id'];
				}
			}
		}
	}
}


function get_tna_template( $remain_days, $tna_template, $buyer ) 
{
	global $tna_template_buyer;
	if(count($tna_template_buyer[$buyer])>0)
	{ 
		$n=count($tna_template_buyer[$buyer]); 
		for($i=0;$i<$n; $i++)
		{ 
			if($remain_days<$tna_template_buyer[$buyer][$i]['lead']) 
			{
				if( $i!=0 )
					return $tna_template_buyer[$buyer][$i-1]['id'];
				else
					return "0";
				 
			}
			else if( $remain_days==$tna_template_buyer[$buyer][$i]['lead'] ) 
			{
				return $tna_template_buyer[$buyer][$i]['id'];
			}
			else if($remain_days>$tna_template_buyer[$buyer][$i]['lead'] &&  $i==$n-1) 
			{
				return $tna_template_buyer[$buyer][$i]['id'];
			}
		}
	}
	else
	{
		 
		$n=count($tna_template); 
		for($i=0;$i<$n;$i++)
		{
			if( $remain_days<$tna_template[$i]['lead']) 
			{
				if( $i!=0 )
					return $tna_template[$i-1]['id'];
				else
					return "0";
				
			}
			else if($remain_days==$tna_template[$i]['lead']) 
			{
				return $tna_template[$i]['id'];
			}
			else if($remain_days>$tna_template[$i]['lead'] &&  $i==$n-1) 
			{
				return $tna_template[$i]['id'];
			}
			 
		}
	}
}



if ($action=="actual_po_info_popup_prev")
{
	echo load_html_head_contents("Actual PO Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);

	?> 
	<script>
			var permission='<? echo $permission; ?>';
		
			function add_break_down_tr(i) 
			 {
				var row_num=$('#tbl_list_search tbody tr').length;
				if (row_num!=i)
				{
					return false;
				}
				else
				{
					i++;
				 
					 $("#tbl_list_search tr:last").clone().find("input,select").each(function() {
						$(this).attr({
						  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
						  'name': function(_, name) { return name + i },
						  'value': function(_, value) { return value }              
						});  
					  }).end().appendTo("#tbl_list_search");
					 $('#increase_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+");");
					 $('#decrease_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+")");
					 $('#poNo_'+i).val("");
					 $('#poQnty_'+i).val("");
					 $('#rowid_'+i).val("");

				}
					  
			}
			function fn_deletebreak_down_tr(rowNo) 
			{   
				
				
					var numRow = $('table#tbl_list_search tbody tr').length; 
					if(rowNo!=1)
					{
						    var permission_array=permission.split("_");
							var rowid=$('#rowid_'+rowNo).val();
							if(rowid !="" && permission_array[2]==1)
							{
							var booking=return_global_ajax_value(rowid, 'delete_row', '', 'woven_order_entry_controller');
							}
							var index=rowNo-1
						    $('#tbl_list_search tbody tr:eq('+index+')').remove();
							var numRow = $('table#tbl_list_search tbody tr').length; 
							for(i = rowNo;i <= numRow;i++)
							{
								$("#size_color_break_down_list tr:eq("+i+")").find("input,select").each(function() {
										$(this).attr({
											'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
											//'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+ i},
											'value': function(_, value) { return value }             
										}); 
								})
							}
					}
			}
					
			function fnc_acc_po_info( operation )
			{
				   var row_num = $('table#tbl_list_search tbody tr').length; 
					var data_all='&poid='+document.getElementById('hid_po_id').value+'&txt_job_no='+document.getElementById('txt_job_no').value;
					for (var i=1; i<=row_num; i++)
					{
						
						if (form_validation('poNo_'+i+'*poQnty_'+i,'PO No*PO Qty')==false)
						{
							return;
						}
						
					data_all=data_all+get_submitted_data_string('poNo_'+i+'*poQnty_'+i+'*rowid_'+i,"../../../",i);
					}
					
					var data="action=save_update_delete_accpoinfo&operation="+operation+'&total_row='+row_num+data_all;
					//alert(data);
					//return;
					freeze_window(operation);
					http.open("POST","woven_order_entry_controller.php",true);
					http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
					http.send(data);
					http.onreadystatechange = fnc_acc_po_info_reponse;
			}

			function fnc_acc_po_info_reponse()
			{
				
				if(http.readyState == 4) 
				{
				    var reponse=trim(http.responseText).split('**');
						if (reponse[0].length>2) reponse[0]=10;
						release_freezing();
						if(reponse[0]==0 || reponse[0]==1 || reponse[0]==2)
						{
							parent.emailwindow.hide();
						}
				}
			}
    </script>

	</head>

		<body>
			<div align="center">
			 <? echo load_freeze_divs ("../../../",$permission);  ?>

				<fieldset style="width:360px">
			    <form id="accpoinfo_1" autocomplete="off">
			        <table width="360" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list_search">
			            <thead>
			                <th>PO Number</th>
			                <th>PO Quantity</th>
			                <th></th>
			            </thead>
			            <tbody>
			            <?
						$data_array=sql_select("select id,acc_po_no,acc_po_qty from wo_po_acc_po_info where po_break_down_id=$po_id and job_no='$txt_job_no' and status_active=1 and is_deleted=0");
						if(count($data_array)>0)
						{
							$i=1;
							foreach( $data_array as $row)
							{
						?>
			                <tr class="general" id="tr_1">
			                    <td align="center">
			                    <input type="hidden" id="rowid_<? echo $i;?>" name="rowid_<? echo $i;?>" class="text_boxes" style="width:130px" value="<? echo $row[csf('id')] ; ?>" />
			                    <input type="text" id="poNo_<? echo $i;?>" name="poNo_<? echo $i;?>" class="text_boxes" style="width:130px" value="<? echo $row[csf('acc_po_no')] ; ?>" />
			                    </td>
			                    <td align="center"><input type="text" id="poQnty_<? echo $i;?>" name="poQnty_<? echo $i;?>" class="text_boxes_numeric" style="width:120px" value="<? echo $row[csf('acc_po_qty')] ; ?>"/></td>
			                    <td width="70">
			                        <input type="button" id="increase_<? echo $i;?>" name="increase_<? echo $i;?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(<? echo $i;?>)" />
			                        <input type="button" id="decrease_<? echo $i;?>" name="decrease_<? echo $i;?>" style="width:30px" class="formbutton" value="-" onClick="fn_deletebreak_down_tr(<? echo $i;?>);" />
			                    </td>
			                </tr>
			                <?
							$i++;
							}
						}
						else
						{
							?>
			                 <tr class="general" id="tr_1">
			                    <td align="center">
			                    <input type="hidden" id="rowid_1" name="rowid_1" class="text_boxes" style="width:130px" value="" />
			                    <input type="text" id="poNo_1" name="poNo_1" class="text_boxes" style="width:130px" value="" />
			                    </td>
			                    <td align="center"><input type="text" id="poQnty_1" name="poQnty_1" class="text_boxes_numeric" style="width:120px" value=""/></td>
			                    <td width="70">
			                        <input type="button" id="increase_1" name="increase_1" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(1)" />
			                        <input type="button" id="decrease_1" name="decrease_1" style="width:30px" class="formbutton" value="-" onClick="fn_deletebreak_down_tr(1);" />
			                    </td>
			                </tr>
			                <?
						}
							?>
			            </tbody>
			        </table>
			        <div align="center" style="margin-top:10px">
			           <?
					   if(count($data_array)>0)
						{
						echo load_submit_buttons( $permission, "fnc_acc_po_info", 1,0 ,"reset_form('accpoinfo_1','','','','')",1) ; 
						}
						else
						{
						echo load_submit_buttons( $permission, "fnc_acc_po_info", 0,0 ,"reset_form('accpoinfo_1','','','','')",1) ; 
						}
					   ?>
			            <input type="hidden" id="hid_po_id" value="<? echo $po_id; ?>" />
			             <input type="hidden" id="txt_job_no" value="<? echo $txt_job_no; ?>" />
			        </div>
			        </form>
				</fieldset>
			</div>
		</body>           
		<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
		</html>
	<?
	exit();
}
if ($action=="actual_po_info_popup")
{
	echo load_html_head_contents("Actual PO Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$colorLibArr=return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	?> 
	<script>
		var permission='<?=$permission; ?>';
			
		function add_break_down_tr(i) 
		{
			var row_num=$('#tbl_list_search tbody tr').length;
			if (row_num!=i)
			{
				return false;
			}
			else
			{
				var country_id = $('#cboCountryId_'+i).val();
				var gmtsItem_id = $('#cboGmtsItemId_'+i).val();
				var gmtscolor = $('#cbogmtscolor_'+i).val();
				var gmtssize = $('#cbogmtssize_'+i).val();
				i++;
				$("#tbl_list_search tbody tr:last").clone().find("input,select").each(function() {
					$(this).attr({
					  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
					  'name': function(_, name) { return name + i },
					  'value': function(_, value) { return value }              
					});  
				  }).end().appendTo("#tbl_list_search");
				  
				$('#poQnty_'+i).removeAttr("onBlur").attr("onBlur","fnc_poqty_cal();");
				$('#shipdate_'+i).removeAttr("class").attr("class","datepicker");
				$('#increase_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+");");
				$('#decrease_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+");");
				/*$('#poNo_'+i).val("");
				$('#rowid_'+i).val("");			
				$('#poQnty_'+i).val("");
				$('#shipdate_'+i).val("");*/
				$('#rowid_'+i).val("");		
				$('#cboCountryId_'+i).val(country_id);
				$('#cboGmtsItemId_'+i).val(gmtsItem_id);
				$('#cbogmtscolor_'+i).val(gmtscolor);
				$('#cbogmtssize_'+i).val(gmtssize);
				fnc_poqty_cal();
				set_all_onclick();
			}
		}
		
		function fn_deletebreak_down_tr(rowNo) 
		{   
			var numRow = $('table#tbl_list_search tbody tr').length; 
			if(rowNo!=1)
			{
				var permission_array=permission.split("_");
				var rowid=$('#rowid_'+rowNo).val();
				if(rowid !="" && permission_array[2]==1)
				{
					var booking=return_global_ajax_value(rowid, 'delete_row', '', 'woven_order_entry_controller');
				}
				var index=rowNo-1
				$('#tbl_list_search tbody tr:eq('+index+')').remove();
				var numRow = $('table#tbl_list_search tbody tr').length; 
				for(i = rowNo;i <= numRow;i++)
				{
					$("#size_color_break_down_list tbody tr:eq("+i+")").find("input,select").each(function() {
						$(this).attr({
							'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
							//'name': function(_, name) { var name=name.split("_"); return name[0] +"_"+ i},
							'value': function(_, value) { return value }             
						}); 
					})
				}
				set_all_onclick();
			}
		}
			
		function fnc_acc_po_info( operation )
		{
			freeze_window(operation);
			var job_no= $('#txt_job_no').val();
			var row_num = $('table#tbl_list_search tbody tr').length; 
			//var data_all='&poid='+document.getElementById('hid_po_id').value+'&txt_job_no='+document.getElementById('txt_job_no').value;
			var z=1;  
			var po_item_chk_arr=new Array();
			
			for (var i=1; i<=row_num; i++)
			{
				var po_no= $('#poNo_'+i).val();
				var CountryId= $('#cboCountryId_'+i).val();
				var GmtsItemId= $('#cboGmtsItemId_'+i).val();
				var gmtscolor= $('#cbogmtscolor_'+i).val();
				var gmtssize= $('#cbogmtssize_'+i).val();
				var shipdate= $('#shipdate_'+i).val();
				var poQnty= $('#poQnty_'+i).val();
				po_item_chk_arr.push(po_no+'#'+CountryId+'#'+GmtsItemId+'#'+gmtscolor+'#'+gmtssize+'#'+shipdate+'#'+poQnty);
			}
			//alert(po_item_chk_arr);
			function hasDuplicates(arr) {
			var counts = [];
			
			for (var i = 0; i <= arr.length; i++) {
				//alert(counts[arr[i]]);
				if (counts[arr[i]] === undefined) 
				{
					counts[arr[i]] = 1;
				} 
				else
				 {
				  return true;
				}
			}
			return false;
			}
			
			// [...]
			
			//var arr = [1, 1, 2, 3, 4];
			
			if(hasDuplicates(po_item_chk_arr)) {
			alert('Error: you have duplicates values !');
			release_freezing();
			return;
			}

			var z=1; var data_all="";
			for (var i=1; i<=row_num; i++)
			{
				if (form_validation('poNo_'+i+'*poQnty_'+i,'PO No*PO Qty')==false)
				{
					release_freezing();
					return; 
				}
				/*var po_no= $('#poNo_'+i).val();
				var cboCountryId= $('#cboCountryId_'+i).val();
				var cboGmtsItemId= $('#cboGmtsItemId_'+i).val();
				var cbogmtscolor= $('#cbogmtscolor_'+i).val();
				var cbogmtssize= $('#cbogmtssize_'+i).val();
				var shipdate= $('#shipdate_'+i).val();*/
				//po_item_val=po_no+'**'+CountryId+'**'+GmtsItemId+'**'+gmtscolor+'**'+gmtssize+'**'+shipdate;
				 
			
				//data_all=data_all+get_submitted_data_string('poNo_'+i+'*cboGmtsItemId_'+i+'*cbogmtscolor_'+i+'*poQnty_'+i+'*shipdate_'+i+'*rowid_'+i,"../../../",i);
				
				data_all+="&poNo_" + z + "='" + $('#poNo_'+i).val()+"'"+"&cboCountryId_" + z + "='" + $('#cboCountryId_'+i).val()+"'"+"&cboGmtsItemId_" + z + "='" + $('#cboGmtsItemId_'+i).val()+"'"+"&cbogmtscolor_" + z + "='" + $('#cbogmtscolor_'+i).val()+"'"+"&cbogmtssize_" + z + "='" + $('#cbogmtssize_'+i).val()+"'"+"&poQnty_" + z + "='" + $('#poQnty_'+i).val()+"'"+"&shipdate_" + z + "='" + $('#shipdate_'+i).val()+"'"+"&rowid_" + z + "='" + $('#rowid_'+i).val()+"'";
				z++;
			}
			
			var data="action=save_update_delete_accpoinfo&operation="+operation+"&total_row="+row_num+get_submitted_data_string('hid_po_id*txt_job_no',"../../../")+data_all;
			
			//var data="action=save_update_delete_accpoinfo&operation="+operation+'&total_row='+row_num+data_all;  
			//alert(data);
			//return;
			
			http.open("POST","woven_order_entry_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_acc_po_info_reponse;
		}

		function fnc_acc_po_info_reponse()
		{
			if(http.readyState == 4) 
			{
				var reponse=trim(http.responseText).split('**');
				if (reponse[0].length>2) reponse[0]=10;
				if(reponse[0]==11)
				{
					alert(reponse[1]);
					release_freezing();
					return;
				}
				if(reponse[0]==0 || reponse[0]==1 || reponse[0]==2)
				{
					var datalist=document.getElementById('hid_po_id').value+'__'+document.getElementById('txt_job_no').value;
					show_list_view( datalist,'accpo_list_view','save_up_list_view','woven_order_entry_controller','');//setFilterGrid(\'tbl_upListView\',-1)
					 var tableFilters_po = 
					{
						//col_0: "none",col_1:"none",display_all_text: " -- All --",
						col_operation: { 
							id: ["total_po_qty"],
							col: [6],
							operation: ["sum"],
							write_method: ["innerHTML"]
						}
					}
					setFilterGrid("tbl_upListView",-1,tableFilters_po);
						  
					
					$('#tbl_list_search tbody tr:not(:first)').remove();
					$('#poNo_1').val("");
					$('#rowid_1').val("");
					$('#cboCountryId_1').val(0);
					$('#cboGmtsItemId_1').val(0);
					$('#cbogmtscolor_1').val(0);
					$('#cbogmtssize_1').val(0);
					$('#poQnty_1').val("");
					$('#shipdate_1').val("");
					$('#txtTotPoQnty').val("");
					set_button_status(0, permission, 'fnc_acc_po_info',1);
					release_freezing();
					//parent.emailwindow.hide();
				}
			}
		}
		
		function fnc_poqty_cal()
		{
			var row_num = $('table#tbl_list_search tbody tr').length;
			var totQty=0;
			var poqty=$("#txt_po_qty").val()*1;
			for (var i=1; i<=row_num; i++)
			{
				if( ($("#poQnty_"+i).val()*1)>0)
				{
					totQty+=$("#poQnty_"+i).val()*1;
					console.log(totQty+'--'+poqty);
					if(totQty>poqty)
					{
						alert("Actual PO Qty Over from PO Quantity");
						$("#poQnty_"+i).val('');
						return;
					}
				}
			}		
			$("#txtTotPoQnty").val(totQty);
			
		}
		
		function get_temp_data(rowid,job_no)
		{
			//show_list_view( rowid,'accpo_list_view_tbody','tbl_list_search_tbody','woven_order_entry_controller','');
			get_php_form_data(rowid+'***'+job_no, 'populate_acc_details_data', 'woven_order_entry_controller');
		}
		function open_acc_po_break_down_popup(po_id,job_no)
		{
			var page_link='woven_order_entry_controller.php?action=open_acc_po_break_down_popup&po_id='+po_id+'&txt_job_no='+job_no;
			var title='Actual Po Breakdown';

			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=970px,height=450px,center=1,resize=1,scrolling=0','../../');
			emailwindow.onclose=function()
			{
			}
		}
    </script>
	</head>
	<body>
		<div align="center">
			<div style="display:none"><?=load_freeze_divs ("../../../",$permission); ?></div>
			<div style="font-size:16px; color:#36F">Actual Po Entry Info  <input type="button"  style="width:150px" class="formbutton" value="Breakdown View" onClick="open_acc_po_break_down_popup('<?=$po_id?>','<?=$txt_job_no?>');" /></div>
			<fieldset style="width:1000px">
		    	<form id="accpoinfo_1" autocomplete="off">
			        <table width="700" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list_search">
			            <thead>
			                <th width="90" class="must_entry_caption">PO Number</th>
			                <th width="90">Country</th>
			                <th width="90">Gmts. Item</th>
			                <th width="90">Gmts. Color</th>
			                <th width="80">Gmts. Size</th>
			                <th width="80" class="must_entry_caption">PO Qty.</th>
			                <th width="70">Ship Date</th>
			                <th>&nbsp;</th>
			            </thead>
			            <tbody id="tbl_list_search_tbody">
			            <?
						
							?>
			                 <tr class="general" id="tr_1" >
			                    <td align="center">
			                        <input type="hidden" id="rowid_1" name="rowid_1" class="text_boxes" style="width:60px" value="" />
			                        <input type="text" id="poNo_1" name="poNo_1" class="text_boxes" style="width:80px" value="" />
			                    </td>
			                    <td><?=create_drop_down( "cboCountryId_1", 90,"select id, country_name from lib_country where status_active=1 and is_deleted=0 group by id, country_name order by country_name ASC", "id,country_name", 1, "-Country-", "","" );//select a.id, a.country_name from lib_country a, wo_po_color_size_breakdown b where a.id=b.country_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.po_break_down_id='$po_id' and b.job_no_mst='$txt_job_no' group by a.id, a.country_name order by a.country_name ASC ?></td>
			                    <td align="center"><?=create_drop_down( "cboGmtsItemId_1", 90, $garments_item, 0, 1, "--Select Item--", $selected,"",0,$gmts_item); ?></td>
			                    <td align="center"><?=create_drop_down( "cbogmtscolor_1", 90, "select a.id, a.color_name,b.color_order from lib_color a, wo_po_color_size_breakdown b where a.id=b.color_number_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.po_break_down_id='$po_id' and b.job_no_mst='$txt_job_no' group by a.id, a.color_name,b.color_order order by b.color_order ASC", "id,color_name", 1, "-Select Color-", $selected,"",0,""); ?></td>
			                    <td align="center"><?=create_drop_down( "cbogmtssize_1", 80, "select a.id, a.size_name,b.size_order from lib_size a, wo_po_color_size_breakdown b where a.id=b.size_number_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.po_break_down_id='$po_id' and b.job_no_mst='$txt_job_no' group by a.id, a.size_name,b.size_order order by b.size_order ASC", "id,size_name", 1, "-Select Size-", $selected,"",0,""); ?></td>
			                    <td align="center"><input type="text" id="poQnty_1" name="poQnty_1" class="text_boxes_numeric" style="width:70px" value="" onBlur="fnc_poqty_cal();" /></td>
			                    <td align="center"><input type="text" id="shipdate_1" name="shipdate_1" class="datepicker" style="width:60px" value=""/></td>
			                    <td>
			                        <input type="button" id="increase_1" name="increase_1" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(1);" />
			                        <input type="button" id="decrease_1" name="decrease_1" style="width:30px" class="formbutton" value="-" onClick="fn_deletebreak_down_tr(1);" />
			                    </td>
			                </tr>
			                <?
						
						?>
			            </tbody>
			            <tfoot>
			            	<th>&nbsp;</th>
			                <th>&nbsp;</th>
			                <th>&nbsp;</th>
			                <th>&nbsp;</th>
			                <th>&nbsp;</th>
			                <th><input type="text" id="txtTotPoQnty" name="txtTotPoQnty" class="text_boxes_numeric" style="width:70px" value="<?=$totpoqty; ?>" disabled readonly/></th>
			                <th>&nbsp;</th>
			                <th>&nbsp;</th>
			            </tfoot>
			        </table>
			        <div align="center" style="margin-top:10px">
			           <?
					   if(count($data_array)>0)
						{
							echo load_submit_buttons( $permission, "fnc_acc_po_info", 1,0 ,"reset_form('accpoinfo_1','','','','')",1) ; 
						}
						else
						{
							echo load_submit_buttons( $permission, "fnc_acc_po_info", 0,0 ,"reset_form('accpoinfo_1','','','','')",1) ; 
						}
					   ?>
			            <input type="hidden" id="hid_po_id" value="<?=$po_id; ?>" />
			            <input type="hidden" id="txt_job_no" value="<?=$txt_job_no; ?>" />
			            <input type="hidden" id="txt_po_qty" value="<?=$po_quantity; ?>" />
			        </div>
		        </form>
			</fieldset>
		    <div id="save_up_list_view"></div>
	    </div>
    </body>
   
    <script>
		show_list_view( '<?=$po_id.'__'.$txt_job_no; ?>','accpo_list_view','save_up_list_view','woven_order_entry_controller','');
	
	</script>       
     <script>
		 var tableFilters_po = 
		{
			//col_0: "none",col_1:"none",display_all_text: " -- All --",
			col_operation: { 
				id: ["total_po_qty"],
				col: [6],
				operation: ["sum"],
				write_method: ["innerHTML"]
			}
	 	}
	</script>
    <script>
			setFilterGrid("tbl_upListView",-1,tableFilters_po);
	</script>  
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if ($action=="open_acc_po_break_down_popup")
{
	echo load_html_head_contents("Actual PO Breakdown","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$colorLibArr=return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	$sizeLibArr=return_library_array("select id, size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name");
	$countryLibArr=return_library_array("select id, country_name from lib_country", "id", "country_name");
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	 
	?> 
	<script>
		var permission='<?=$permission; ?>';
			
		function call_print_button_for_mail(mail){		
	
			var company=$('#company_name').val();
			var mail_item=78;
			//var data=return_global_ajax_value( company+'_'+mail_item, 'mail_template', '', '../../../auto_mail/setting/mail_controller');
			//console.log('mail:'+data);
			generate_report('<?=$po_id?>','<?=$txt_job_no?>');
			

	}

	function generate_report(po_id,txt_job_no,company,mail_data=0)
	{
		
			console.log(po_id+'#**#'+txt_job_no+'#**#'+mail_data);
			//freeze_window();
			
			var data="action=open_acc_po_break_down_mail&po_id="+po_id+"&txt_job_no="+txt_job_no+"&mail_data="+mail_data+"&cbo_company_name="+company;
			http.open("POST","woven_order_entry_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_generate_report_reponse;
		
	}

	function fnc_generate_report_reponse()
	{
		if(http.readyState == 4)
		{
			//release_freezing();
			$('#data_panel').html( http.responseText );
			console.log(http.responseText);
			var w = window.open("Surprise", "_blank");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="css/style_common.css" type="text/css" /><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body</html>');
			d.close();
		}
	}
		
    </script>
	</head>
	<body>
		<?php ob_start(); ?>
		<div align="center" id="print_data" style="width: 900px;">
			
			
					<table id="acc_heading" style="font-size:16px; color:#36F;border: 1px solid black;width: 190px;justify-content: center;text-align: center;">
						<tr>
							<td>Actual PO Breakdown</td>
						</tr>
					</table>
			
			
				<?php 
				//echo "SELECT b.id,b.po_number,b.shipment_date,b.po_quantity,b.plan_cut,b.pub_shipment_date,b.grouping, a.company_name,a.style_ref_no,a.buyer_name,a.working_company_id from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and b.id='$po_id' and b.job_no_mst='$txt_job_no' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ";
				$po_sql=sql_select("SELECT b.id,
									       b.po_number,
									       b.shipment_date,
									       b.po_quantity,
									       b.plan_cut,
									       b.pub_shipment_date,
									       b.GROUPING,
									       a.company_name,
									       a.style_ref_no,
									       a.buyer_name,
									       a.working_company_id
									       
									  FROM wo_po_details_master a,
									       wo_po_break_down b
									       
									 WHERE     a.job_no = b.job_no_mst
									           and b.id='$po_id' and b.job_no_mst='$txt_job_no' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ");
				$master_po_data=array();
				$set_sql="SELECT d.item_name,c.smv_pcs from wo_po_details_mas_set_details c,lib_garment_item d  WHERE d.id=c.gmts_item_id and  c.job_no='$txt_job_no'";
				//echo $set_sql;
				$set_res=sql_select($set_sql);
				$set_data=array();
				foreach ($set_res as $row) 
				{
					if(!empty($set_data['gmts_item']))
					{
						$set_data['gmts_item'].=",".$row[csf('item_name')];
					}
					else{
						$set_data['gmts_item']=$row[csf('item_name')];
					}
					

					$set_data['smv_pcs']=$row[csf('smv_pcs')];
				}
				foreach ($po_sql as $row) {
					$master_po_data[$row[csf('id')]]['po_number']=$row[csf('po_number')];
					$master_po_data[$row[csf('id')]]['shipment_date']=$row[csf('shipment_date')];
					$master_po_data[$row[csf('id')]]['po_quantity']=$row[csf('po_quantity')];
					$master_po_data[$row[csf('id')]]['po_quantity']=$row[csf('po_quantity')];
					$master_po_data[$row[csf('id')]]['plan_cut']=$row[csf('plan_cut')];
					$master_po_data[$row[csf('id')]]['pub_shipment_date']=$row[csf('pub_shipment_date')];
					$master_po_data[$row[csf('id')]]['grouping']=$row[csf('grouping')];
					$master_po_data[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
					$master_po_data[$row[csf('id')]]['company_name']=$comp[$row[csf('company_name')]];
					$master_po_data[$row[csf('id')]]['company_id']=$row[csf('company_name')];
					$master_po_data[$row[csf('id')]]['working_company']=$comp[$row[csf('working_company_id')]];
					$master_po_data[$row[csf('id')]]['buyer_name']=$buyer_arr[$row[csf('buyer_name')]];
					$master_po_data[$row[csf('id')]]['smv_pcs']=$row[csf('smv_pcs')];
					$master_po_data[$row[csf('id')]]['gmts_item']=$buyer_arr[$row[csf('gmts_item')]];
				}

				 ?>
				<table width="890" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table">
					<tr>
						<td width="90">Company:</td>
						<td width="140" ><?=$master_po_data[$po_id]['company_name']?> <input type="hidden" id="company_name" value="<?=$master_po_data[$po_id]['company_id']?>"></td>
						<td width="80">Buyer:</td>
						<td width="140"><?=$master_po_data[$po_id]['buyer_name']?></td>
						<td width="80">Style Ref:</td>
						<td width="140"><?=$master_po_data[$po_id]['style_ref_no']?></td>
						<td width="80">Product Name:</td>
						<td><?=$set_data['gmts_item']?></td>
					</tr>
					<tr>
						<td width="90">Working Company:</td>
						<td width="140"><?=$master_po_data[$po_id]['working_company']?></td>
						<td width="80">Internal Ref:</td>
						<td width="140"><?=$master_po_data[$po_id]['grouping']?></td>
						<td width="80">Job No.:</td>
						<td width="140"><?=$txt_job_no?></td>
						<td width="80">Sew SMV:</td>
						<td><?=$set_data['smv_pcs']?></td>
					</tr>
				</table>
				<br>
				<?php 
					

					$sql="select id, acc_po_no, country_id, gmts_item, gmts_color_id, gmts_size_id, acc_po_qty, acc_ship_date from wo_po_acc_po_info where po_break_down_id='$po_id' and job_no='$txt_job_no' and status_active=1 and is_deleted=0 order by acc_po_no";
					//echo $sql;
					$sql_res=sql_select($sql);
					$acc_po_color_size_arr=array();
					$acc_size_arr=array();
					$po_color_size_qnty_arr=array();
					foreach ($sql_res as $row) 
					{
						$acc_po_color_size_arr[$row[csf('acc_po_no')]][$row[csf('gmts_color_id')]]['acc_po_no']=$row[csf('acc_po_no')];
						$acc_po_color_size_arr[$row[csf('acc_po_no')]][$row[csf('gmts_color_id')]]['country_id']=$row[csf('country_id')];
						$acc_po_color_size_arr[$row[csf('acc_po_no')]][$row[csf('gmts_color_id')]]['acc_po_qty']+=$row[csf('acc_po_qty')];
						$acc_po_color_size_arr[$row[csf('acc_po_no')]][$row[csf('gmts_color_id')]]['acc_ship_date']=$row[csf('acc_ship_date')];
						$po_color_size_qnty_arr[$row[csf('acc_po_no')]][$row[csf('gmts_color_id')]][$row[csf('gmts_size_id')]]+=$row[csf('acc_po_qty')];
						$acc_size_arr[$row[csf('gmts_size_id')]]=$sizeLibArr[$row[csf('gmts_size_id')]];
					}
					$master_po_span=0;
					$acc_po_span_arr=array();
					foreach ($acc_po_color_size_arr as $acc_po => $acc_po_data) 
					{
						$acc_span=0;
						foreach ($acc_po_data as $color_id => $color_data) 
						{
							$acc_span++;
							$master_po_span++;
						}
						$acc_po_span_arr[$acc_po]=$acc_span;

					}
					$acc_size_arr=array_unique(array_filter($acc_size_arr));
				 ?>
		        <table width="950" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list_search">
		            <thead>
		                <th width="35" >SL</th>
		                <th width="80">Master PO</th>
		                <th width="70">Master PO<br>  Qty.</th>
		                <th width="70">Master PO<br>Ship Date</th>
		                <th width="80">March PO</th>
		                <th width="70">March PO<br>Qty.</th>
		                <th width="70">March PO<br>Ship Date</th>
		                <th width="80">Color</th>
		                <?
		                	foreach ($acc_size_arr as $size_id => $size_name) 
		                	{
		                		?>
		                		<th width="40"><?=$size_name?></th>

		                		<?
		                	}
		                ?>
		                
		            </thead>
		            <tbody>
		            	<?
		            		$sl=1;
		            		foreach ($acc_po_color_size_arr as $acc_po => $acc_po_data) 
		            		{
		            			$acc_po_span=0;
		            			foreach ($acc_po_data as $color_id => $color_data) 
		            			{
		            				if ($sl%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		            				?>
		            					<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $sl; ?>">
		            						<td><?=$sl++;?></td>
		            						<?php if ($sl==2): ?>
		            							
		            							<td rowspan="<?=$master_po_span;?>"><?=$master_po_data[$po_id]['po_number'];?></td>
		            							<td rowspan="<?=$master_po_span;?>"><?=number_format($master_po_data[$po_id]['po_quantity'],0);?></td>
		            							<td rowspan="<?=$master_po_span;?>"><?=change_date_format($master_po_data[$po_id]['shipment_date']);?></td>
		            						<?php endif ?>

		            						 <?php if ($acc_po_span==0): ?>

		            						 	<td rowspan="<?=$acc_po_span_arr[$acc_po];?>"><?=$acc_po;?></td>
		            						 	<td rowspan="<?=$acc_po_span_arr[$acc_po];?>"><?=$color_data['acc_po_qty'];?></td>
		            						 	<td rowspan="<?=$acc_po_span_arr[$acc_po];?>"><?=change_date_format($color_data['acc_ship_date']);$acc_po_span++;?></td>
		            						 	
		            						 <?php endif ?>
		            						 <td ><?=$colorLibArr[$color_id];?></td>

		            						   <?
								                	foreach ($acc_size_arr as $size_id => $size_name) 
								                	{
								                		?>
								                		<td width="40"><?=number_format($po_color_size_qnty_arr[$acc_po][$color_id][$size_id],0)?></td>

								                		<?
								                	}
								                ?>
		            					</tr>
		            				<?
		            			}
		            			

		            		}
		            	?>
		            </tbody>
		            
		        </table>

			    
		    
	    </div>
	    <?php 

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

	     ?>
	     <br>
	     <table style="justify-content: center;text-align: center;width: 900px;">
	     	<tr>
	     		<td>
	     			<a href="<?=$filename?>" style="text-decoration:none" id="exl">
	     					     <input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/>
	     			</a>
	     			<input type="button" onClick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/>
	     			<input class="formbutton" type="button" onClick="fnSendMail('../../../','',1,1,0,1)" value="Mail Send" style="width:80px;">
	     		</td>
	     	</tr>
	     </table>
	     <div id="data_panel" style="display: none;"></div>
	    
    </body>
   
    
     
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    <script type="text/javascript">
    	function new_window()
    	{
    		const el = document.querySelector('#scroll_body');
    		if (el) 
    		{
    		    document.getElementById('scroll_body').style.overflow="auto";
    			document.getElementById('scroll_body').style.maxHeight="none"; 

    		}
    		
    		//$(".flt").hide();
    		
    		document.getElementById('acc_heading').style.marginLeft="350px"; 
    		
	    		var w = window.open("Surprise", "#");
	    		var d = w.document.open();
	    		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	    	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print" /><title></title></head><body>'+document.getElementById('print_data').innerHTML+'</body</html>');
	    		d.close(); 
	    		if (el) 
	    		{
	    		    document.getElementById('scroll_body').style.overflowY="auto"; 
	    			document.getElementById('scroll_body').style.maxHeight="400px";
	    		}
    		document.getElementById('acc_heading').style.margin="0 auto"; 
    		//$(".flt").show();
    	}
    </script>
    </html>
    <?
    	
		//$filename=$user_id."_".$name.".xls";
		//echo "document.getElementById('exl').href='".$filename."';\n";
    exit();
}

if($action=="open_acc_po_break_down_mail")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$colorLibArr=return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	$sizeLibArr=return_library_array("select id, size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name");
	$countryLibArr=return_library_array("select id, country_name from lib_country", "id", "country_name");
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	//echo $po_id;die;
	?> 
	<script>
		var permission='<?=$permission; ?>';
			
		
    </script>
	</head>
	<body>
		<?php ob_start(); ?>
		<div align="center" id="print_data" style="width: 900px;">
			
			
					<table id="acc_heading" style="font-size:16px; color:#36F;border: 1px solid black;width: 190px;justify-content: center;text-align: center;">
						<tr>
							<td>Actual PO Breakdown</td>
						</tr>
					</table>
			
			
				<?php 
				//echo "SELECT b.id,b.po_number,b.shipment_date,b.po_quantity,b.plan_cut,b.pub_shipment_date,b.grouping, a.company_name,a.style_ref_no,a.buyer_name,a.working_company_id from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and b.id='$po_id' and b.job_no_mst='$txt_job_no' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ";
				$po_sql=sql_select("SELECT b.id,
									       b.po_number,
									       b.shipment_date,
									       b.po_quantity,
									       b.plan_cut,
									       b.pub_shipment_date,
									       b.GROUPING,
									       a.company_name,
									       a.style_ref_no,
									       a.buyer_name,
									       a.working_company_id
									       
									  FROM wo_po_details_master a,
									       wo_po_break_down b
									       
									 WHERE     a.job_no = b.job_no_mst
									           and b.id='$po_id' and b.job_no_mst='$txt_job_no' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ");
				$master_po_data=array();
				$set_sql="SELECT d.item_name,c.smv_pcs from wo_po_details_mas_set_details c,lib_garment_item d  WHERE d.id=c.gmts_item_id and  c.job_no='$txt_job_no'";
				//echo $set_sql;
				$set_res=sql_select($set_sql);
				$set_data=array();
				foreach ($set_res as $row) 
				{
					if(!empty($set_data['gmts_item']))
					{
						$set_data['gmts_item'].=",".$row[csf('item_name')];
					}
					else{
						$set_data['gmts_item']=$row[csf('item_name')];
					}
					

					$set_data['smv_pcs']=$row[csf('smv_pcs')];
				}
				foreach ($po_sql as $row) {
					$master_po_data[$row[csf('id')]]['po_number']=$row[csf('po_number')];
					$master_po_data[$row[csf('id')]]['shipment_date']=$row[csf('shipment_date')];
					$master_po_data[$row[csf('id')]]['po_quantity']=$row[csf('po_quantity')];
					$master_po_data[$row[csf('id')]]['po_quantity']=$row[csf('po_quantity')];
					$master_po_data[$row[csf('id')]]['plan_cut']=$row[csf('plan_cut')];
					$master_po_data[$row[csf('id')]]['pub_shipment_date']=$row[csf('pub_shipment_date')];
					$master_po_data[$row[csf('id')]]['grouping']=$row[csf('grouping')];
					$master_po_data[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
					$master_po_data[$row[csf('id')]]['company_name']=$comp[$row[csf('company_name')]];
					$master_po_data[$row[csf('id')]]['company_id']=$row[csf('company_name')];
					$master_po_data[$row[csf('id')]]['working_company']=$comp[$row[csf('working_company_id')]];
					$master_po_data[$row[csf('id')]]['buyer_name']=$buyer_arr[$row[csf('buyer_name')]];
					$master_po_data[$row[csf('id')]]['smv_pcs']=$row[csf('smv_pcs')];
					$master_po_data[$row[csf('id')]]['gmts_item']=$buyer_arr[$row[csf('gmts_item')]];
				}

				 ?>
				<table width="890" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table">
					<tr>
						<td width="90">Company:</td>
						<td width="140" ><?=$master_po_data[$po_id]['company_name']?> </td>
						<td width="80">Buyer:</td>
						<td width="140"><?=$master_po_data[$po_id]['buyer_name']?></td>
						<td width="80">Style Ref:</td>
						<td width="140"><?=$master_po_data[$po_id]['style_ref_no']?></td>
						<td width="80">Product Name:</td>
						<td><?=$set_data['gmts_item']?></td>
					</tr>
					<tr>
						<td width="90">Working Company:</td>
						<td width="140"><?=$master_po_data[$po_id]['working_company']?></td>
						<td width="80">Internal Ref:</td>
						<td width="140"><?=$master_po_data[$po_id]['grouping']?></td>
						<td width="80">Job No.:</td>
						<td width="140"><?=$txt_job_no?></td>
						<td width="80">Sew SMV:</td>
						<td><?=$set_data['smv_pcs']?></td>
					</tr>
				</table>
				<br>
				<?php 
					

					$sql="select id, acc_po_no, country_id, gmts_item, gmts_color_id, gmts_size_id, acc_po_qty, acc_ship_date from wo_po_acc_po_info where po_break_down_id='$po_id' and job_no='$txt_job_no' and status_active=1 and is_deleted=0 order by acc_po_no";
					//echo $sql;
					$sql_res=sql_select($sql);
					$acc_po_color_size_arr=array();
					$acc_size_arr=array();
					$po_color_size_qnty_arr=array();
					foreach ($sql_res as $row) 
					{
						$acc_po_color_size_arr[$row[csf('acc_po_no')]][$row[csf('gmts_color_id')]]['acc_po_no']=$row[csf('acc_po_no')];
						$acc_po_color_size_arr[$row[csf('acc_po_no')]][$row[csf('gmts_color_id')]]['country_id']=$row[csf('country_id')];
						$acc_po_color_size_arr[$row[csf('acc_po_no')]][$row[csf('gmts_color_id')]]['acc_po_qty']+=$row[csf('acc_po_qty')];
						$acc_po_color_size_arr[$row[csf('acc_po_no')]][$row[csf('gmts_color_id')]]['acc_ship_date']=$row[csf('acc_ship_date')];
						$po_color_size_qnty_arr[$row[csf('acc_po_no')]][$row[csf('gmts_color_id')]][$row[csf('gmts_size_id')]]+=$row[csf('acc_po_qty')];
						$acc_size_arr[$row[csf('gmts_size_id')]]=$sizeLibArr[$row[csf('gmts_size_id')]];
					}
					$master_po_span=0;
					$acc_po_span_arr=array();
					foreach ($acc_po_color_size_arr as $acc_po => $acc_po_data) 
					{
						$acc_span=0;
						foreach ($acc_po_data as $color_id => $color_data) 
						{
							$acc_span++;
							$master_po_span++;
						}
						$acc_po_span_arr[$acc_po]=$acc_span;

					}
					$acc_size_arr=array_unique(array_filter($acc_size_arr));
				 ?>
		        <table width="950" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list_search">
		            <thead>
		                <th width="35" >SL</th>
		                <th width="80">Master PO</th>
		                <th width="70">Master PO<br>  Qty.</th>
		                <th width="70">Master PO<br>Ship Date</th>
		                <th width="80">March PO</th>
		                <th width="70">March PO<br>Qty.</th>
		                <th width="70">March PO<br>Ship Date</th>
		                <th width="80">Color</th>
		                <?
		                	foreach ($acc_size_arr as $size_id => $size_name) 
		                	{
		                		?>
		                		<th width="40"><?=$size_name?></th>

		                		<?
		                	}
		                ?>
		                
		            </thead>
		            <tbody>
		            	<?
		            		$sl=1;
		            		foreach ($acc_po_color_size_arr as $acc_po => $acc_po_data) 
		            		{
		            			$acc_po_span=0;
		            			foreach ($acc_po_data as $color_id => $color_data) 
		            			{
		            				if ($sl%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		            				?>
		            					<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $sl; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $sl; ?>">
		            						<td><?=$sl++;?></td>
		            						<?php if ($sl==2): ?>
		            							
		            							<td rowspan="<?=$master_po_span;?>"><?=$master_po_data[$po_id]['po_number'];?></td>
		            							<td rowspan="<?=$master_po_span;?>"><?=number_format($master_po_data[$po_id]['po_quantity'],0);?></td>
		            							<td rowspan="<?=$master_po_span;?>"><?=change_date_format($master_po_data[$po_id]['shipment_date']);?></td>
		            						<?php endif ?>

		            						 <?php if ($acc_po_span==0): ?>

		            						 	<td rowspan="<?=$acc_po_span_arr[$acc_po];?>"><?=$acc_po;?></td>
		            						 	<td rowspan="<?=$acc_po_span_arr[$acc_po];?>"><?=$color_data['acc_po_qty'];?></td>
		            						 	<td rowspan="<?=$acc_po_span_arr[$acc_po];?>"><?=change_date_format($color_data['acc_ship_date']);$acc_po_span++;?></td>
		            						 	
		            						 <?php endif ?>
		            						 <td ><?=$colorLibArr[$color_id];?></td>

		            						   <?
								                	foreach ($acc_size_arr as $size_id => $size_name) 
								                	{
								                		?>
								                		<td width="40"><?=number_format($po_color_size_qnty_arr[$acc_po][$color_id][$size_id],0)?></td>

								                		<?
								                	}
								                ?>
		            					</tr>
		            				<?
		            			}
		            			

		            		}
		            	?>
		            </tbody>
		            
		        </table>

			    
		    
	    </div>
	    <?php 

	    	$mailBody=ob_get_contents();
			ob_clean();
			echo $mailBody;

		//Mail send------------------------------------------
		list($msil_address,$is_mail_send)=explode('**',$mail_data);
		echo $is_mail_send;
		if($is_mail_send==1)
		{
		
			require_once('../../../mailer/class.phpmailer.php');
			require_once('../../../auto_mail/setting/mail_setting.php');
			
			$mailBody = preg_replace("/<img[^>]+\>/i", " ", $mailBody); 	
			$mailToArr=array();
			if($msil_address){$mailToArr[]=$msil_address;}
				
			$mailSql = "SELECT c.TEAM_LEADER_EMAIL, d.USER_EMAIL
	  			FROM wo_po_details_master  a,  wo_pre_cost_mst b, lib_marketing_team c, USER_PASSWD d
	 			WHERE a.job_no = b.job_no  AND a.TEAM_LEADER = c.id AND b.INSERTED_BY = d.id AND a.status_active = 1  AND a.job_no=$txt_job_no";
			
			//echo $mailSql;die;
			
			$mailSqlRes=sql_select($mailSql);
			foreach($mailSqlRes as $rows)
			{
				if($rows[TEAM_MEMBER_EMAIL]){$mailToArr[]=$rows[TEAM_MEMBER_EMAIL];}
				if($rows[USER_EMAIL]){$mailToArr[]=$rows[USER_EMAIL];}
			}
			
			//$elcetronicSql = "SELECT a.SEQUENCE_NO,a.BYPASS,b.USER_EMAIL  from electronic_approval_setup a join user_passwd b on a.user_id=b.id where b.valid=1 and a.page_id=2150 and a.entry_form=46 and a.company_id=$cbo_company_name order by a.SEQUENCE_NO";
			
			$elcetronicSql = "SELECT a.BUYER_ID,a.SEQUENCE_NO,a.BYPASS,b.USER_EMAIL  from electronic_approval_setup a join user_passwd b on a.user_id=b.id where b.valid=1  AND a.page_id in(428,1717,2150) and a.company_id=$cbo_company_name order by a.SEQUENCE_NO"; //and a.page_id=2150 and a.entry_form=46
			//echo $elcetronicSql;die;
			
			$elcetronicSqlRes=sql_select($elcetronicSql);
			foreach($elcetronicSqlRes as $rows)
			{
				
				if($rows[BUYER_ID]!='')
				{
					 
					foreach(explode(',',$rows[BUYER_ID]) as $bi){
						if($rows[USER_EMAIL]!='' && $rows[BYPASS]==2 && $bi==$buyer_name_id){
							$mailToArr[100]=$rows[USER_EMAIL];break;
						}
					}
				}
				else{
				
					if($rows[SEQUENCE_NO]==1 && $rows[BYPASS]==2){
						if($rows[USER_EMAIL]){$mailToArr[100]=$rows[USER_EMAIL];}
					}
				}
				
				$elecDataArr[$rows[BYPASS]][]=$rows[USER_EMAIL];
			}
			
			//print_r($mailToArr);die;
			
			if($elecDataArr[1][0] && $mailToArr[100]==""){$mailToArr[]=$elecDataArr[1][0];}
			elseif($elecDataArr[2][0] && $mailToArr[100]==""){$mailToArr[]=$elecDataArr[2][0];}
			
			$to=implode(',',$mailToArr);
			//echo $to;die;
			 
			
			//Att file....
			$imgSql="select IMAGE_LOCATION,REAL_FILE_NAME from common_photo_library where is_deleted=0  and MASTER_TBLE_ID=$txt_job_no and file_type=1";
			$imgSqlResult=sql_select($imgSql);
			foreach($imgSqlResult as $rows)
			{
				$att_file_arr[]='../../../'.$rows[IMAGE_LOCATION].'**'.$rows[REAL_FILE_NAME];
			}
			
			$subject="Actual PO Breakdown";
			$header=mailHeader();
			echo sendMailMailer( $to, $subject, $mailBody, $from_mail,$att_file_arr );

		}
		    ?>
	     
	     
	    
    </body>
   
    
     
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    
    </script>
    </html>
    <?
    	
		//$filename=$user_id."_".$name.".xls";
		//echo "document.getElementById('exl').href='".$filename."';\n";
    exit();
}

if($action=="save_update_delete_accpoinfo_prev")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0";disconnect($con); die;}		
		 $id=return_next_id( "id", "wo_po_acc_po_info", 1 ) ;
		 $field_array="id,job_no,po_break_down_id,acc_po_no,acc_po_qty,inserted_by,insert_date";
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $poNo="poNo_".$i;
			 $poQnty="poQnty_".$i;
			 $rowid="rowid_".$i;

			if ($i!=1) $data_array .=",";
			$data_array .="(".$id.",'".$txt_job_no."','".$poid."',".$$poNo.",".$$poQnty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			$id=$id+1;
		 }
		$rID=sql_insert("wo_po_acc_po_info",$field_array,$data_array,1);
		check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "0";
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10";
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);
				echo "0";
			}
			else{
				oci_rollback($con);
				echo "10";
			}
		}
		disconnect($con);
		die;
	}
	if ($operation==1)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0";disconnect($con); die;}

		 $add_comma=0;
		 $id=return_next_id( "id", "wo_po_acc_po_info", 1 ) ;
		 $field_array="id,job_no,po_break_down_id,acc_po_no,acc_po_qty,inserted_by,insert_date";
		 $field_array_up="acc_po_no*acc_po_qty*updated_by*update_date";
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $poNo="poNo_".$i;
			 $poQnty="poQnty_".$i;
			 $rowid="rowid_".$i;
			 if(str_replace("'",'',$$rowid)!="")
			 {
				 $id_arr[]=str_replace("'",'',$$rowid);
				 $data_array_up[str_replace("'",'',$$rowid)] =explode("*",("".$$poNo."*".$$poQnty."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			 }
             if(str_replace("'",'',$$rowid)=="")
			 {
				if ($add_comma!=0) $data_array .=",";
				$data_array .="(".$id.",'".$txt_job_no."','".$poid."',".$$poNo.",".$$poQnty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				$add_comma++;
				$id=$id+1;
			 }
		 }
		// echo bulk_update_sql_statement( "wo_po_acc_po_info", "id", $field_array_up, $data_array_up, $id_arr );
		 $rID=execute_query(bulk_update_sql_statement( "wo_po_acc_po_info", "id", $field_array_up, $data_array_up, $id_arr ));
		 if($data_array !="")
		 {
			$rID=sql_insert("wo_po_acc_po_info",$field_array,$data_array,1);
		 }
		check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "1";
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10";
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);
				echo "1";
			}
			else{
				oci_rollback($con);
				echo "10";
			}
		}
		disconnect($con);
		die;
	}
	if ($operation==2)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; disconnect($con);die;}

		 $field_array_up="status_active*is_deleted*updated_by*update_date";
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $poNo="poNo_".$i;
			 $poQnty="poQnty_".$i;
			 $rowid="rowid_".$i;
			 if(str_replace("'",'',$$rowid)!="")
			 {
				 $id_arr[]=str_replace("'",'',$$rowid);
				 $data_array_up[str_replace("'",'',$$rowid)] =explode("*",("0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			 }
             
		 }
		// echo bulk_update_sql_statement( "wo_po_acc_po_info", "id", $field_array_up, $data_array_up, $id_arr );
		$rID=execute_query(bulk_update_sql_statement( "wo_po_acc_po_info", "id", $field_array_up, $data_array_up, $id_arr ));
		check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "2";
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10";
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			
			if($rID ){
				oci_commit($con);
				echo "2";
			}
			else{
				oci_rollback($con);
				echo "10";
			}
		}
		disconnect($con);
		die;
	}
}

if($action=="save_update_delete_accpoinfo")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	//$colorLibArr=return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	
	$sql_po_chk=sql_select("select id, job_no, po_break_down_id, acc_po_no, country_id, gmts_item, gmts_color_id, gmts_size_id, acc_ship_date, acc_po_qty from wo_po_acc_po_info where job_no=$txt_job_no and status_active=1 and id=1393");
	$accPoDataArr=array();
	foreach($sql_po_chk as $row)
	{
		if ($operation==0)
		{
			$accPoDataArr[$row[csf('acc_po_no')]][$row[csf('country_id')]][$row[csf('gmts_item')]][$row[csf('gmts_color_id')]][$row[csf('gmts_size_id')]][strtotime($row[csf('acc_ship_date')])]=$row[csf('acc_po_qty')];
		}
		else if ($operation==1)
		{
			$accPoDataArr[$row[csf('id')]][$row[csf('acc_po_no')]][$row[csf('country_id')]][$row[csf('gmts_item')]][$row[csf('gmts_color_id')]][$row[csf('gmts_size_id')]][strtotime($row[csf('acc_ship_date')])]=$row[csf('acc_po_qty')];
		}
	}
	unset($sql_po_chk);
	
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		
		if(check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}		
		$id=return_next_id( "id", "wo_po_acc_po_info", 1);
		$field_array="id, job_no, po_break_down_id, acc_po_no, country_id, gmts_item, gmts_color_id, gmts_size_id, acc_po_qty, acc_ship_date, inserted_by, insert_date, status_active, is_deleted";
		for ($i=1; $i<=$total_row; $i++)
		{
			$poNo="poNo_".$i;
			$cboCountryId="cboCountryId_".$i;
			$cboGmtsItemId="cboGmtsItemId_".$i;
			$cbogmtscolor="cbogmtscolor_".$i;
			$cbogmtssize="cbogmtssize_".$i;
			$poQnty="poQnty_".$i;
			$shipdate="shipdate_".$i;
			$rowid="rowid_".$i;
			$acc_poNo=str_replace("'","",$$poNo);
			$acc_countryId=str_replace("'","",$$cboCountryId);
			$acc_itemId=str_replace("'","",$$cboGmtsItemId);
			$acc_gmtscolor=str_replace("'","",$$cbogmtscolor);
			$acc_gmtssize=str_replace("'","",$$cbogmtssize);
			$acc_shipdate=str_replace("'","",$$shipdate);

			//$ship_date =  date('M/d/Y/YYYY',strtotime($acc_shipdate));

			if(str_replace("'",'',$$shipdate)!="") $ship_dateCon=date("d-M-Y",strtotime(str_replace("'",'',$$shipdate))); else $ship_dateCon="";
			$acc_po_no_chk=$accPoDataArr[$acc_poNo][$acc_countryId][$acc_itemId][$acc_gmtscolor][$acc_gmtssize][strtotime($ship_dateCon)];	
			if(($acc_po_no_chk*1)>0)
			{
				$msg="Error: You have duplicates values !";
				echo "11**".$msg;	
				check_table_status( $_SESSION['menu_id'],0);
				disconnect($con);die;
			}
		
			
			/*if(str_replace("'","",$$txtgmtscolor)!="")
			{
				if (!in_array(str_replace("'","",$$txtgmtscolor),$new_array_color))
				{
					$color_id = return_id( str_replace("'","",$$txtgmtscolor), $colorLibArr, "lib_color", "id,color_name","401");
					$new_array_color[$color_id]=str_replace("'","",$$txtgmtscolor);
				}
				else $color_id =  array_search(str_replace("'","",$$txtgmtscolor), $new_array_color);
			}
			else $color_id=0;*/
			
			if ($i!=1) $data_array .=",";
			$data_array .="(".$id.",".$txt_job_no.",".$hid_po_id.",".$$poNo.",".$$cboCountryId.",".$$cboGmtsItemId.",".$$cbogmtscolor.",".$$cbogmtssize.",".$$poQnty.",'".$ship_dateCon."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			$id=$id+1;
		}
		//echo "11**insert into wo_po_acc_po_info (".$field_array.") values ".$data_array; die;
		//check_table_status( $_SESSION['menu_id'],0); die;
		$rID=sql_insert("wo_po_acc_po_info",$field_array,$data_array,1);
		check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "0";
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);
				echo "0";
			}
			else{
				oci_rollback($con);
				echo "10";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)  // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		if(check_table_status( $_SESSION['menu_id'], 1 )==0) { echo "15**0"; disconnect($con); die;}

		$add_comma=0;
		$id=return_next_id( "id", "wo_po_acc_po_info", 1 ) ;
		$field_array="id, job_no, po_break_down_id, acc_po_no, country_id, gmts_item, gmts_color_id, gmts_size_id, acc_po_qty, acc_ship_date, inserted_by, insert_date, status_active, is_deleted";
		$field_array_up="acc_po_no*country_id*gmts_item*gmts_color_id*gmts_size_id*acc_po_qty*acc_ship_date*updated_by*update_date";
		//echo "10**";
		for ($i=1;$i<=$total_row;$i++)
		{
			$poNo="poNo_".$i;
			$cboCountryId="cboCountryId_".$i;
			$cboGmtsItemId="cboGmtsItemId_".$i;
			$cbogmtscolor="cbogmtscolor_".$i;
			$cbogmtssize="cbogmtssize_".$i;
			$poQnty="poQnty_".$i;
			$shipdate="shipdate_".$i;
			$rowid="rowid_".$i;
			
			$acc_poNo=str_replace("'","",$$poNo);
			$acc_countryId=str_replace("'","",$$cboCountryId);
			$acc_itemId=str_replace("'","",$$cboGmtsItemId);
			$acc_gmtscolor=str_replace("'","",$$cbogmtscolor);
			$acc_gmtssize=str_replace("'","",$$cbogmtssize);
			$acc_poQnty=str_replace("'","",$$poQnty);
			//$shipdate=str_replace("'","",$$shipdate);
			
			$acc_updateid=str_replace("'","",$$rowid);
			
			if(str_replace("'",'',$$shipdate)!="") $ship_dateCon=date("d-M-Y",strtotime(str_replace("'",'',$$shipdate))); else $ship_dateCon="";
			
			$acc_shipdate=str_replace("'","",$$shipdate);
			
			$acc_po_no_chk=$accPoDataArr[$acc_updateid][$acc_poNo][$acc_countryId][$acc_itemId][$acc_gmtscolor][$acc_gmtssize][strtotime($acc_shipdate)];	
			if(($acc_po_no_chk*1)>0)
			{
				$msg="Error: You have duplicates values !.";
				echo "11**".$msg;	
				check_table_status( $_SESSION['menu_id'],0);
				disconnect($con);die;
			}
			
			
			/*if(str_replace("'","",$$txtgmtscolor)!="")
			{
				if (!in_array(str_replace("'","",$$txtgmtscolor),$new_array_color))
				{
					$color_id = return_id( str_replace("'","",$$txtgmtscolor), $colorLibArr, "lib_color", "id,color_name","401");
					$new_array_color[$color_id]=str_replace("'","",$$txtgmtscolor);
				}
				else $color_id =  array_search(str_replace("'","",$$txtgmtscolor), $new_array_color);
			}
			else $color_id=0;*/
			
			if(str_replace("'",'',$$rowid)!="")
			{
				$id_arr[]=str_replace("'",'',$$rowid);
				$data_array_up[str_replace("'",'',$$rowid)] =explode("*",("".$$poNo."*".$$cboCountryId."*".$$cboGmtsItemId."*".$$cbogmtscolor."*".$$cbogmtssize."*".$$poQnty."*'".$ship_dateCon."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			}
			if(str_replace("'",'',$$rowid)=="")
			{
				if($add_comma!=0) $data_array .=",";
				$data_array .="(".$id.",".$txt_job_no.",".$hid_po_id.",".$$poNo.",".$$cboCountryId.",".$$cboGmtsItemId.",".$$cbogmtscolor.",".$$cbogmtssize.",".$$poQnty.",'".$ship_dateCon."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				$add_comma++;
				$id=$id+1;
			}
		}
		//check_table_status( $_SESSION['menu_id'],0); die;
		// echo bulk_update_sql_statement( "wo_po_acc_po_info", "id", $field_array_up, $data_array_up, $id_arr );
		$flag=1;
		$rID=execute_query(bulk_update_sql_statement( "wo_po_acc_po_info", "id", $field_array_up, $data_array_up, $id_arr ));
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		if($data_array!="")
		{
			$rID1=sql_insert("wo_po_acc_po_info",$field_array,$data_array,1);
			if($rID1==1 && $flag==1) $flag=1; else $flag=0;
		}
		//echo "10**".$rID.'='.$rID1.'='.$flag;
		//echo "10**insert into wo_po_acc_po_info (".$field_array.") values ".$data_array; check_table_status( $_SESSION['menu_id'],0); die;
		//check_table_status( $_SESSION['menu_id'],0); die;
		check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($flag==1){
				mysql_query("COMMIT");  
				echo "1";
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1){
				oci_commit($con);
				echo "1";
			}
			else{
				oci_rollback($con);
				echo "10";
			}
		}
		disconnect($con);
		die;
	}
	else if($operation==2)  //Delete Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		if(check_table_status( $_SESSION['menu_id'], 1 )==0) { echo "15**0"; disconnect($con); die;}
		$field_array_up="status_active*is_deleted*updated_by*update_date";
		for ($i=1;$i<=$total_row;$i++)
		{
			$poNo="poNo_".$i;
			$poQnty="poQnty_".$i;
			$rowid="rowid_".$i;
			if(str_replace("'",'',$$rowid)!="")
			{
				$id_arr[]=str_replace("'",'',$$rowid);
				$data_array_up[str_replace("'",'',$$rowid)] =explode("*",("0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			}
		}
		// echo bulk_update_sql_statement( "wo_po_acc_po_info", "id", $field_array_up, $data_array_up, $id_arr );
		$rID=execute_query(bulk_update_sql_statement( "wo_po_acc_po_info", "id", $field_array_up, $data_array_up, $id_arr ));
		check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{

			if($rID ){
				mysql_query("COMMIT");  
				echo "2";
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			
			if($rID ){
				oci_commit($con);
				echo "2";
			}
			else{
				oci_rollback($con);
				echo "10";
			}
		}
		disconnect($con);
		die;
	}
}

if($action=="accpo_list_view_pxt")
{
	extract($_REQUEST);
 	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
	
	$exdata=explode("__",$data);
	$colorLibArr=return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	$sizeLibArr=return_library_array("select id, size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name");
	$countryLibArr=return_library_array("select id, country_name from lib_country", "id", "country_name");
	?>
    
    <!-- <script>
	 var tableFilters_po = 
	{
		//col_0: "none",col_1:"none",display_all_text: " -- All --",
		col_operation: { 
			id: ["total_po_qty"],
			col: [6],
			operation: ["sum"],
			write_method: ["innerHTML"]
		}
 	}
	</script>-->
     <fieldset>
    <div style="width:700px;" align="center">
    <legend>Actual PO Info List View</legend>
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="700" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="110">Po No</th>
                <th width="110">Country</th>
                <th width="130">Gmts Item</th>
                <th width="100">Gmts Color</th>
                <th width="50">Gmts Size</th>
                <th width="60">PO Qty</th>
                <th>Ship Date</th>
            </thead>
     	</table>
        <div style="width:700px; overflow-y:scroll; max-height:220px;" align="center">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="680" class="rpt_table" id="tbl_upListView" >
            
            <?
				$sql="select id, acc_po_no, country_id, gmts_item, gmts_color_id, gmts_size_id, acc_po_qty, acc_ship_date from wo_po_acc_po_info where po_break_down_id='$exdata[0]' and job_no='$exdata[1]' and status_active=1 and is_deleted=0 order by acc_po_no";
				$sql_res=sql_select($sql);
				
				//print_r($mst_temp_arr);
				$i=1; $tot_qty=0;
				foreach($sql_res as $row)
				{
					if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
                    <tr id="tr_<?=$i; ?>" bgcolor="<?=$bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="get_temp_data('<?=$row[csf('id')]; ?>','<?=$exdata[1]; ?>');">
                    	<td width="30" align="center"><?=$i; ?></td>
                        <td width="110" style="word-break:break-all"><?=$row[csf('acc_po_no')]; ?></td>
                        <td width="110" style="word-break:break-all"><?=$countryLibArr[$row[csf('country_id')]]; ?></td>
                        <td width="130" style="word-break:break-all"><?=$garments_item[$row[csf('gmts_item')]]; ?></td>
                        <td width="100" style="word-break:break-all"><?=$colorLibArr[$row[csf('gmts_color_id')]]; ?></td>
                        <td width="50" style="word-break:break-all"><?=$sizeLibArr[$row[csf('gmts_size_id')]]; ?></td>
                        <td width="60" align="right"><?=$row[csf('acc_po_qty')]; ?></td>
                        <td style="word-break:break-all"><?=change_date_format($row[csf('acc_ship_date')]); ?></td>
                    </tr>
                    <?
					$i++;
					$tot_qty+=$row[csf('acc_po_qty')];
				}
			?>
           
            </table>
        </div>
        <table width="700" class="tbl_bottom"  border="1" class="rpt_table" rules="all">
			 
					<tr>
					<td width="30">&nbsp;</td>
                    <td width="110">&nbsp;</td>
                    <td width="110">&nbsp;</td>
                    <td width="130">&nbsp;</td>
                    <td width="100">&nbsp;</td>
                    <td width="50">&nbsp;</td>
          			<td width="60" id="total_po_qty" align="right"><strong><? echo number_format($tot_qty,0);?> </strong></td>
            		<td align="">&nbsp; </td>
					</tr>
				 
			</table>
            
       
        
     </div>
     </fieldset>
     <!-- <script>
			setFilterGrid("tbl_upListView",-1,tableFilters_po);
		</script>-->
    <?
	exit();
}
if($action=="accpo_list_view")
{
	extract($_REQUEST);
 	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
	
	$exdata=explode("__",$data);
	$colorLibArr=return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	$sizeLibArr=return_library_array("select id, size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name");
	$countryLibArr=return_library_array("select id, country_name from lib_country", "id", "country_name");
	?>
    
    <!-- <script>
	 var tableFilters_po = 
	{
		//col_0: "none",col_1:"none",display_all_text: " -- All --",
		col_operation: { 
			id: ["total_po_qty"],
			col: [6],
			operation: ["sum"],
			write_method: ["innerHTML"]
		}
 	}
	</script>-->
     <fieldset>
    <div style="width:700px;" align="center">
    <legend>Actual PO Info List View</legend>
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="300" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="150">Po No</th>
                
                <th >PO Qty</th>
               
            </thead>
     	</table>
        <div style="width:300px; overflow-y:scroll; max-height:220px;" align="center">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="280" class="rpt_table" id="tbl_upListView" >
            
            <?
				$sql="select id, acc_po_no, country_id, gmts_item, gmts_color_id, gmts_size_id, acc_po_qty, acc_ship_date from wo_po_acc_po_info where po_break_down_id='$exdata[0]' and job_no='$exdata[1]' and status_active=1 and is_deleted=0 order by acc_po_no";
				$sql_res=sql_select($sql);
				
				//print_r($mst_temp_arr);
				$i=1; $tot_qty=0;
				$po_wise_data=array();
				foreach($sql_res as $row)
				{
					$po_wise_data[$row[csf('acc_po_no')]]+=$row[csf('acc_po_qty')];
				}
				foreach($po_wise_data as $acc_po_no=>$acc_po_qty)
				{
					if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
                    <tr id="tr_<?=$i; ?>" bgcolor="<?=$bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="get_temp_data('<?=$acc_po_no; ?>','<?=$exdata[1]; ?>');">
                    	<td width="30" align="center"><?=$i; ?></td>
                        <td width="150" style="word-break:break-all"><?=$acc_po_no; ?></td>
                        
                        <td align="right"><?=$acc_po_qty; ?></td>
                        
                    </tr>
                    <?
					$i++;
					$tot_qty+=$acc_po_qty;
				}
			?>
           
            </table>
        </div>
        <table width="300" class="tbl_bottom"  border="1" class="rpt_table" rules="all">
			 
					<tr>
					<td width="30">&nbsp;</td>
                    <td width="150">&nbsp;</td>
                    
          			<td   align="right"><strong><? echo number_format($tot_qty,0);?> </strong></td>
            		
					</tr>
				 
			</table>
            
       
        
     </div>
     </fieldset>
     <!-- <script>
			setFilterGrid("tbl_upListView",-1,tableFilters_po);
		</script>-->
    <?
	exit();
}

if($action=="accpo_list_view_tbody")
{
	$data_array=sql_select("select id, acc_po_no, country_id, gmts_item, gmts_color_id, gmts_size_id, acc_po_qty, acc_ship_date from wo_po_acc_po_info where acc_po_no='$data' and status_active=1 and is_deleted=0");
	$sl=1;
	foreach($data_array as $row)
	{
		?>
		<tr class="general" id="tr_<?=$sl;?>" >
            <td align="center">
                <input type="hidden" id="rowid_<?=$sl;?>" name="rowid_<?=$sl;?>" class="text_boxes" style="width:60px" value="" />
                <input type="text" id="poNo_<?=$sl;?>" name="poNo_<?=$sl;?>" class="text_boxes" style="width:80px" value="" />
            </td>
            <td><?=create_drop_down( "cboCountryId_".$sl, 90,"select id, country_name from lib_country where status_active=1 and is_deleted=0 group by id, country_name order by country_name ASC", "id,country_name", 1, "-Country-", "","" );//select a.id, a.country_name from lib_country a, wo_po_color_size_breakdown b where a.id=b.country_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.po_break_down_id='$po_id' and b.job_no_mst='$txt_job_no' group by a.id, a.country_name order by a.country_name ASC ?></td>
            <td align="center"><?=create_drop_down( "cboGmtsItemId_".$sl, 90, $garments_item, 0, 1, "--Select Item--", $selected,"",0,$gmts_item); ?></td>
            <td align="center"><?=create_drop_down( "cbogmtscolor_".$sl, 90, "select a.id, a.color_name,b.color_order from lib_color a, wo_po_color_size_breakdown b where a.id=b.color_number_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.po_break_down_id='$po_id' and b.job_no_mst='$txt_job_no' group by a.id, a.color_name,b.color_order order by b.color_order ASC", "id,color_name", 1, "-Select Color-", $selected,"",0,""); ?></td>
            <td align="center"><?=create_drop_down( "cbogmtssize_".$sl, 80, "select a.id, a.size_name,b.size_order from lib_size a, wo_po_color_size_breakdown b where a.id=b.size_number_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.po_break_down_id='$po_id' and b.job_no_mst='$txt_job_no' group by a.id, a.size_name,b.size_order order by b.size_order ASC", "id,size_name", 1, "-Select Size-", $selected,"",0,""); ?></td>
            <td align="center"><input type="text" id="poQnty_<?=$sl;?>" name="poQnty_<?=$sl;?>" class="text_boxes_numeric" style="width:70px" value="" onBlur="fnc_poqty_cal();" /></td>
            <td align="center"><input type="text" id="shipdate_<?=$sl;?>" name="shipdate_<?=$sl;?>" class="datepicker" style="width:60px" value=""/></td>
            <td>
                <input type="button" id="increase_<?=$sl;?>" name="increase_<?=$sl;?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(<?=$sl;?>);" />
                <input type="button" id="decrease_<?=$sl;?>" name="decrease_<?=$sl;?>" style="width:30px" class="formbutton" value="-" onClick="fn_deletebreak_down_tr(<?=$sl;?>);" />
            </td>
        </tr>
     	<?
     	$sl++;
    }
}
if($action=="populate_acc_details_data")
{
	$data=explode("***", $data);
	$data_array=sql_select("select id, acc_po_no, country_id, gmts_item, gmts_color_id, gmts_size_id, acc_po_qty, acc_ship_date from wo_po_acc_po_info where acc_po_no='$data[0]' and job_no='$data[1]' and status_active=1 and is_deleted=0");

	$sl=1;
	foreach($data_array as $row)
	{
		if($sl>1)
		{
			echo "add_break_down_tr(".($sl-1).");\n";
		}
		echo "$('#rowid_".$sl."').val('".$row[csf("id")]."');\n";
		echo "$('#poNo_".$sl."').val('".$row[csf("acc_po_no")]."');\n";
		echo "$('#cboCountryId_".$sl."').val('".$row[csf("country_id")]."');\n";
		echo "$('#cboGmtsItemId_".$sl."').val('".$row[csf("gmts_item")]."');\n";
		echo "$('#cbogmtscolor_".$sl."').val('".$row[csf("gmts_color_id")]."');\n";
		echo "$('#cbogmtssize_".$sl."').val('".$row[csf("gmts_size_id")]."');\n";
		echo "$('#poQnty_".$sl."').val('".$row[csf("acc_po_qty")]."');\n";
		echo "$('#shipdate_".$sl."').val('".change_date_format($row[csf("acc_ship_date")])."');\n";
		
		$sl++;
	}
	if($sl>1)
	{
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_acc_po_info',1);\n";
	}
	exit();
}



if($action=="delete_row")
{
	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}
	$rID=execute_query("update wo_po_acc_po_info set status_active=0, is_deleted=1, updated_by=".$_SESSION['logic_erp']['user_id'].",update_date='".$pc_date_time."' where id=$data");
	if($db_type==0)
	{
		if($rID ){
		mysql_query("COMMIT");  
		echo "2";
		}
		else{
		mysql_query("ROLLBACK"); 
		echo "10";
		}
	}
	
	if($db_type==2 || $db_type==1 )
	{
		if($rID ){
		oci_commit($con);
		echo "2";
		}
		else{
		oci_rollback($con);
		echo "10";
		}
	}
	disconnect($con);
	die;
}

if($action=="terms_condition_popup")
{
	echo load_html_head_contents("Job Ref Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$permission=$_SESSION['page_permission'];
?>
	<script>
	var permission='<? echo $permission; ?>';
function add_break_down_tr(i) 
 { 
	var row_num=$('#tbl_termcondi_details tr').length-1;
	if (row_num!=i)
	{
		return false;
	}
	else
	{
		i++;
	 
		 $("#tbl_termcondi_details tr:last").clone().find("input,select").each(function() {
			$(this).attr({
			  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ i },
			  'name': function(_, name) { return name + i },
			  'value': function(_, value) { return value }              
			});  
		  }).end().appendTo("#tbl_termcondi_details");
		 $('#increase_'+i).removeAttr("onClick").attr("onClick","add_break_down_tr("+i+");");
		  $('#decrease_'+i).removeAttr("onClick").attr("onClick","fn_deletebreak_down_tr("+i+")");
		  $('#termscondition_'+i).val('');
		   $('#termsconditionID_'+i).val("");
		   $('#termscondition_'+i).removeAttr("onBlur").attr("onBlur","row_sequence("+i+")");
		  
		    $('#sltd_'+i).val(i);
			 //$('#sltd_'+i).html(i);
	}
		  
}

function fn_deletebreak_down_tr(rowNo) 
{   
	
	
		var numRow = $('table#tbl_termcondi_details tbody tr').length; 
		if(numRow==rowNo && rowNo!=1)
		{
			$('#tbl_termcondi_details tbody tr:last').remove();
		}
	
}

function fnc_order_entry_terms_condition( operation )
{
	    var row_num=$('#tbl_termcondi_details tr').length-1;
		var data_all="";
		for (var i=1; i<=row_num; i++)
		{
			
			if (form_validation('termscondition_'+i,'Internal Ref')==false)
			{
				return;
			}
			var internal_ref = $('#termscondition_'+i).val();
		
			data_all+=get_submitted_data_string('txt_job_no*job_insert_date*insert_date*termscondition_'+i+'*termsconditionID_'+i,"../../../",i);
				//data_all+=get_submitted_data_string('termscondition_'+i+'*termsconditionID_'+i,"../../../",i);
			// data_all+=get_submitted_data_string('txtconscomp_'+i+'*txtgsm_'+i+'*txtdiawidthtype_'+i+'*txtdiawidth_'+i+'*txtbatchqnty_'+i+'*txtprodid_'+i+'*updateiddtls_'+i+'*txtdiawidthtypeid_'+i+'*txtroll_'+i+'*txtproductionqty_'+i+'*rollid_'+i,"../../",i);
			
		}  //alert(data_all);return;
		var data="action=save_update_delete_wo_order_entry_ref&operation="+operation+'&total_row='+row_num+'&txt_job_no='+txt_job_no+data_all;
		freeze_window(operation);
		http.open("POST","woven_order_entry_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_order_entry_terms_condition_reponse;
}

function fnc_order_entry_terms_condition_reponse()
{
	
	if(http.readyState == 4) 
	{
	    var reponse=trim(http.responseText).split('**');
		//alert(reponse);
			release_freezing();
			if(reponse[0]==11)
			{
			alert("Duplicate Internal Ref Not Allow");	
			return;
			}
			if (reponse[0].length>2) reponse[0]=10;
			
			if(reponse[0]==0 || reponse[0]==1)
			{
				document.getElementById('iref').value=reponse[2];
				parent.emailwindow.hide();
			}
			
			
			////if(reponse[0]==0)
			//{
				 set_button_status(1, permission, 'fnc_order_entry_terms_condition',1);
			//}
			
			
	}
}
//Row Sequence

function row_sequence(row_id)
	{
		var row_num=$('#tbl_termcondi_details tbody tr').length-1;
		
		var txt_seq=$('#termscondition_'+row_id).val();
		//alert(row_id);
		//var seq_no=1;
		if(txt_seq=="")
		{
			return;	
		}
		
		for(var j=1; j<=row_num; j++)
		{
			if(j==row_id)
			{
				continue;
			}
			else
			{
				var txt_seq_check=$('#termscondition_'+j).val();
				//alert(txt_seq_check);
				if(txt_seq==txt_seq_check)
				{
					alert("Duplicate Seq No. "+txt_seq);
					$('#termscondition_'+row_id).val('');
					return;
				}
			}
		}
	}	
    </script>
</head>
<body>
<div align="center" style="width:100%;" >
 <? echo load_freeze_divs ("../../../",$permission);  ?>
<fieldset>
        	<form id="termscondi_1" autocomplete="off">
            <?
			if($db_type==0)
				{
					$year_cond="  insert_date as insert_date";
					$year_cond2="  YEAR(insert_date) as insert_year";
				}
				else if($db_type==2)
				{
					$year_cond="insert_date as insert_date";
					$year_cond2=" to_char(insert_date,'YYYY') as insert_year";
				}
            	$job_insert_date= return_field_value("$year_cond","wo_po_details_master","job_no=$txt_job_no","insert_date");
				$insert_date= return_field_value("$year_cond2","wo_po_details_master","job_no=$txt_job_no","insert_year");
				
				if($db_type==0)
				{
					$insert_year_cond="  YEAR(job_insert_date)=$insert_date";
				}
				else if($db_type==2)
				{
					 $insert_year_cond=" to_char(job_insert_date,'YYYY')=$insert_date";
				}
			?>
           <input type="text" id="txt_job_no" class="text_boxes" style="width:100px"  name="txt_job_no" value="<? echo str_replace("'","",$txt_job_no) ?>"/>
            <input type="hidden" id="job_insert_date" class="text_boxes" style="width:100px"  name="txt_job_no" value="<? echo str_replace("'","",$job_insert_date) ?>"/>
             <input type="hidden" id="insert_date" class="text_boxes" style="width:100px"  name="txt_job_no" value="<? echo str_replace("'","",$insert_date) ?>"/>
             <input type="hidden" id="iref" name="iref"/>
            
            <table width="350" cellspacing="0" class="rpt_table" border="0" id="tbl_termcondi_details" rules="all">
                	<thead>
                    	<tr>
                        	<th width="30">Sl</th><th width="150">Internal Ref</th><th width="80"></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
					//echo $year_cond;
						// "select $year_cond from wo_po_details_master where job_no=$txt_job_no ";
				
					$current_year=date("Y",time());
					$data_array=sql_select("select max(internal_ref) as internal_ref from   wo_order_entry_internal_ref where  $insert_year_cond");// quotation_id='$data'
					$max_ref=$data_array[0][csf('internal_ref')]+1;
					$data_array=sql_select("select id as update_id, internal_ref from   wo_order_entry_internal_ref where job_no=$txt_job_no order by id asc");// quotation_id='$data'
					
					if ( count($data_array)>0)
					{
						$i=0;
						foreach( $data_array as $row )
						{
							$i++;
							?>
                            	<tr id="settr_1" align="center">
                                    <td>
                                    <input type="text" id="sltd_<? echo $i;?>"   name="sltd_<? echo $i;?>" style="width:30px"   class="text_boxes_numeric" value="<? echo $i;?>"    /> 
                                    </td>
                                    <td>
                                    <input type="text" id="termscondition_<? echo $i;?>"   name="termscondition_<? echo $i;?>" style="width:140px"   class="text_boxes" value="<? echo $row[csf('internal_ref')]; ?>" onBlur="row_sequence(<? echo $i; ?>); "   /> 
                                    <input type="hidden" id="termsconditionID_<? echo $i;?>"  name="termsconditionID_<? echo $i;?>" style="width:50px" value="<? echo $row[csf('update_id')]?>"  />
                                    </td>
                                    <td> 
                                    <input type="button" id="increase_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(<? echo $i; ?> )" />                                   <input type="button" id="decrease_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?>);" />
                                    </td>
                                </tr>
                            <?
						}
					}
					else
					{
								$k=1;		?>
                   		 		<tr id="settr_1" align="center">
                                    <td>
                                    <input type="text" id="sltd_<? echo $k;?>"   name="sltd_<? echo $k;?>" style="width:30px"   class="text_boxes_numeric" value="<? echo $k;?>"    /> 
                                    </td>
                                    <td>
                                    <input type="text" id="termscondition_<? echo $k;?>"  onBlur="row_sequence(<? echo $k; ?>); "   name="termscondition_<? echo $k;?>" style="width:140px"   class="text_boxes" value="<? echo $max_ref;?>"    /> 
                                    <input type="hidden" id="termsconditionID_<? echo $k;?>"   name="termsconditionID_<? echo $k;?>" style="width:50px" value=""  />
                                    </td>
                                    <td>
                                    <input type="button" id="increase_<? echo $k; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(<? echo $k; ?> )" />                                    <input type="button" id="decrease_<? echo $k; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $k; ?> );" />                  </td>
                   				</tr>
                            
                    <? 
						$k++;
					} 
					?>
                </tbody>
                </table>
                <table width="350" cellspacing="0" class="" border="0">
                	<tr>
                        <td align="center" height="15" width="100%"> </td>
                    </tr>
                	<tr>
                        <td align="center" width="100%" class="button_container">
						        <?
								if ( count($data_array)>0)
									{
										echo load_submit_buttons( $permission, "fnc_order_entry_terms_condition", 1,0 ,"reset_form('termscondi_1','','','','')",1) ; 
									}
									else
									{
										echo load_submit_buttons( $permission, "fnc_order_entry_terms_condition", 0,0 ,"reset_form('termscondi_1','','','','')",1) ; 
									}
									
								?>
                        </td> 
                    </tr>
                </table>
            </form>
        </fieldset>
</div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="save_update_delete_wo_order_entry_ref")
{
$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$job=str_replace("'","",$txt_job_no);
		$insert_date=str_replace("'","",$insert_date);
		if($db_type==0)
			{
				$insert_year_cond=" and  YEAR(job_insert_date)='$insert_date'";
			}
			else if($db_type==2)
			{
				 $insert_year_cond=" and to_char(job_insert_date,'YYYY')='$insert_date'";
			}
			//echo $insert_year_cond;die;
	//	if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}'".$pc_date_time."'		
		 $id=return_next_id( "id", "wo_order_entry_internal_ref", 1 ) ;
		 $field_array="id,job_no,internal_ref,job_insert_date,insert_date";
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 
			
			 
			 $internal_ref="termscondition_".$i;
			 $internal_cond="termscondition_".$i;
			  if(is_duplicate_field( "internal_ref", "wo_order_entry_internal_ref", "internal_ref=".$$internal_cond."  $insert_year_cond" )==1)
				{
					//check_table_status( $_SESSION['menu_id'],0);
					echo "11**0"; 
					disconnect($con);die;			
				}
			
			if ($i!=1) $data_array .=",";
			$data_array .="(".$id.",".$txt_job_no.",".$$internal_ref.",".$job_insert_date.",'".$pc_date_time."')";
			$id=$id+1;
			 //echo $$internal_cond;
			//$sql="select internal_ref from wo_order_entry_internal_ref where internal_ref=".$$internal_cond." ";
			
			
		 }//echo  $sql;
		
		//$rID_de3=execute_query( "delete from wo_order_entry_internal_ref where  job_no =".$txt_job_no."",0);

		 $rID=sql_insert("wo_order_entry_internal_ref",$field_array,$data_array,1);
		// check_table_status( $_SESSION['menu_id'],0);
		
		$internal=substr(return_library_autocomplete( "select distinct internal_ref as internal_ref  from wo_order_entry_internal_ref where job_no=$txt_job_no ", "internal_ref"  ), 0, -1);
	
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "0**".$job."**".$internal;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$job."**".$internal;
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);
				echo "0**".$job."**".$internal;
			}
			else{
				oci_rollback($con);
				echo "10**".$job."**".$internal;
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
		
		$job=str_replace("'","",$txt_job_no);
		$insert_date=str_replace("'","",$insert_date);
		if($db_type==0)
			{
				$insert_year_cond=" and  YEAR(job_insert_date)='$insert_date'";
			}
			else if($db_type==2)
			{
				 $insert_year_cond=" and to_char(job_insert_date,'YYYY')='$insert_date'";
			}
		$data_array2=sql_select("select max(internal_ref) as internal_ref from   wo_order_entry_internal_ref");// quotation_id='$data'
		 $max_ref=$data_array2[0][csf('internal_ref')];
		 $id=return_next_id( "id", "wo_order_entry_internal_ref", 1 ) ;
		 $field_array="id,job_no,internal_ref,job_insert_date,insert_date";
		 $field_array_up="job_no*internal_ref*job_insert_date*insert_date";
		 $add_comma=1;
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $internal_ref="termscondition_".$i;
			 //$internal_cond="termscondition_".$i;
			 $internal_cond=str_replace("'","",$$internal_ref);
			 //echo $internal_cond.'<hr>';
			 $update_id="termsconditionID_".$i;
			 $mst_update_id=str_replace("'","",$$update_id);
			// echo $mst_update_id;die;
			if($mst_update_id!="") //and id!=$mst_update_id
			{ 
				//echo "10**select internal_ref from wo_order_entry_internal_ref where internal_ref=".$internal_cond."  $insert_year_cond ";
				if(is_duplicate_field( "internal_ref", "wo_order_entry_internal_ref", "internal_ref='".$internal_cond."' and id!=$mst_update_id  $insert_year_cond " )==1)
					{
						//check_table_status( $_SESSION['menu_id'],0);and $insert_year_cond
						echo "11**0"; 
					disconnect($con);	die;			
					}
				$id_arr[]=str_replace("'",'',$$update_id);
				$data_array_up[str_replace("'",'',$$update_id)] =explode("*",("".$txt_job_no."*'".$internal_cond."'*".$job_insert_date."*'".$pc_date_time."' "));
			}
			if($mst_update_id=="")
			{ 
			 if(is_duplicate_field( "internal_ref", "wo_order_entry_internal_ref", "internal_ref='".$internal_cond."'  $insert_year_cond" )==1)
				{
					//check_table_status( $_SESSION['menu_id'],0);
					echo "11**0"; 
					disconnect($con);die;			
				}
			
			if ($add_comma!=1) $data_array .=",";
				$data_array .="(".$id.",".$txt_job_no.",".$$internal_ref.",".$job_insert_date.",'".$pc_date_time."')";
				$id=$id+1;
				$add_comma++;
			}
			//echo "select id from wo_order_entry_internal_ref  where internal_ref=".$internal_cond." and job_no=$txt_job_no and id!=$mst_update_id ";die;
				//$rID_de3=execute_query( "delete from wo_order_entry_internal_ref where  job_no=".$txt_job_no." ",0);
		 }
		 //echo "10**".bulk_update_sql_statement("wo_order_entry_internal_ref", "id",$field_array_up,$data_array_up,$id_arr );
		 $rID=execute_query(bulk_update_sql_statement("wo_order_entry_internal_ref", "id",$field_array_up,$data_array_up,$id_arr ));
			//print_r($data_array);
		 if($data_array!="")
		 	{
		 	 $rID=sql_insert("wo_order_entry_internal_ref",$field_array,$data_array,1);
			}
		// print_r($data_array);
		// check_table_status( $_SESSION['menu_id'],0);
				$internal=substr(return_library_autocomplete( "select distinct internal_ref as internal_ref  from wo_order_entry_internal_ref where job_no=$txt_job_no", "internal_ref"  ), 0, -1);

		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "1**".$job."**".$internal;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$job."**".$internal;
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);
				echo "0**".$job."**".$internal;
			}
			else{
				oci_rollback($con);
				echo "10**".$job."**".$internal;
			}
		}
		disconnect($con);
		die;
	}  // Update End
}

/*if($action=="check_ref_no")
{
	$data=explode("**",$data);
	$sql="select id, job_no_mst,grouping from wo_po_break_down where job_no_mst='".trim($data[0])."' and grouping='$data[2]' and is_deleted=0 and status_active=1 order by id desc";
	$data_array=sql_select($sql,1);
	if(count($data_array)>0)
	{
		echo "1"."_".$data_array[0][csf('id')]."_".$data_array[0][csf('grouping')];
	}
	else
	{
		echo "0_";
	}
	exit();	
}*/

if($action=="check_internal_ref")
{
	$data=explode("**",$data);
	$sql="select id, internal_ref from wo_order_entry_internal_ref where internal_ref='".trim($data[0])."'  order by id desc";
	$data_array=sql_select($sql,1);
	if(count($data_array)>0)
	{
		echo "1"."_".$data_array[0][csf('id')]."_".$data_array[0][csf('internal_ref')];
	}
	else
	{
		echo "0_";
	}
	exit();	
}

if($action=="set_smv_work_study")
{
	$data=explode("**",$data);
	$item_id=$data[1];
	$style_id=$data[0];
	//print_r($data);
	//and style_ref='$style_id'
	  $sql_smv="select  upper(style_ref) as style_ref,gmts_item_id,total_smv from ppl_gsd_entry_mst where gmts_item_id=$item_id  and status_active=1 and is_deleted=0";
		  $sql_result=sql_select($sql_smv);$set_smv_arr=array();
		 foreach($sql_result as $row)
		 {
			$set_smv_arr[$row[csf('style_ref')]][$row[csf('gmts_item_id')]]['smv']+=$row[csf('total_smv')];
		 }
		// print_r($set_smv_arr);
	if(count($sql_result)>0)
	{
		echo "1_".$set_smv_arr[$style_id][$item_id]['smv'];
		//echo $set_smv_arr[$style_id][$item_id]['smv'];
	}
	else
	{
	 echo "0_";	
	}
	
	exit();	
}

if($action=="open_smv_list")
{
	 echo load_html_head_contents("PO Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	
	$item_id=$item_id;
	$style_id=$txt_style_ref;
	$set_smv_id=$set_smv_id;
	$row_id=$id;
	$set_smv_id=$set_smv_id;
	$cbo_buyer_name=$cbo_buyer_name;
	$cbo_company_name=$cbo_company_name;
	//echo $cbo_company_name;
		?>
	<script type="text/javascript">
      function js_set_value(id)
      { 	//alert(id);
		  document.getElementById('selected_smv').value=id;
		  parent.emailwindow.hide();
      }
    </script>
   
    </head>
    <body>
    <div align="center" style="width:100%;" >
    	<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
            <table width="400" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
                <thead>                	 
                    <th width="150">Buyer Name</th>
                    <th width="100">Style Ref </th>
                    <th>
                        <input type="hidden" id="selected_job">
                        <input type="hidden" id="item_id" value="<?  echo $item_id;?>">
                        <input type="hidden" id="row_id" value="<?  echo $row_id;?>">
                        <input type="hidden" id="company_id" value="<?  echo $cbo_company_name;?>">
                    &nbsp;</th>
                </thead>
                <tr>
                    <td id=""><? echo create_drop_down( "cbo_buyer_name", 172, "select id,buyer_name from lib_buyer  where status_active =1 and is_deleted=0 order by buyer_name",'id,buyer_name', 1, "-- Select Buyer --",$cbo_buyer_name,"",1 ); ?></td>
                    <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:100px" value="<? echo $txt_style_ref;?>" disabled></td>
                    <td align="center">
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('company_id').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('item_id').value+'_'+document.getElementById('row_id').value, 'create_item_smv_search_list_view', 'search_div', 'woven_order_entry_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
                </tr>
            </table>
            <div id="search_div"></div>   
        </form>
       </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if($action=="create_item_smv_search_list_view_____off")
{
	$data=explode('_',$data);
	$company=$data[0];
	$buyer_id=$data[1];
	$style=$data[2];
	$item_id=$data[3];
	$row_id=$data[4];
	
	//if ($company!=0) $company_con=" and a.company_id='$company'";else $company_con="";
	if ($buyer_id!=0) $buyer_id_con=" and a.buyer_id='$buyer_id'";else $buyer_id_con="";
	if ($style!="") $style_con=" and a.style_ref ='$style'";else $style_con="";
	if ($item_id!=0) $gmts_item_con=" and a.gmts_item_id='$item_id'";else $gmts_item_con="";
	if ($item_id!=0) $gmts_item_con2=" and a.gmt_item_id='$item_id'";else $gmts_item_con2="";
	?>
	<input type="hidden" id="selected_smv" name="selected_smv" /> 
	<?
	$is_old=2;
	if($is_old!=1)
	{
		/*$sewing_sql="select a.id as lib_sewing_id, a.gmt_item_id, a.bodypart_id, a.operation_name, a.department_code as dcode from lib_sewing_operation_entry a where a.is_deleted=0 $gmts_item_con2  order by a.id Desc";
		$result = sql_select($sewing_sql);
		foreach($result as $row)
		{
			$code_smv_arr[$row[csf('lib_sewing_id')]]['dcode']=$row[csf('dcode')];
			$code_smv_arr[$row[csf('lib_sewing_id')]][$row[csf('bodypart_id')]]['operation_name']=$row[csf('operation_name')];
		}*/
		// print_r($code_smv_arr);b.lib_sewing_id
		if($db_type==0) 
		{
			$group_con="group_concat(b.lib_sewing_id)  as lib_sewing_id";
			$id_group_con="group_concat(a.id)";
		}
		else 
		{
			$group_con="listagg(b.lib_sewing_id,',') within group (order by b.lib_sewing_id) as lib_sewing_id";
			$id_group_con="listagg(a.id,',') within group (order by a.id)";
		}
		
		$sql="select a.id, a.style_ref, a.extention_no, a.operation_count, a.gmts_item_id, b.operator_smv, b.helper_smv, b.body_part_id, b.lib_sewing_id, c.department_code from ppl_gsd_entry_mst a, ppl_gsd_entry_dtls b, lib_sewing_operation_entry c where a.id=b.mst_id and b.lib_sewing_id=c.id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $gmts_item_con $style_con $buyer_id_con 
		order by id DESC";
		
		$sql_result=sql_select($sql);
		foreach($sql_result as $row)	
		{
			//$operation_name=$code_smv_arr[$row[csf('lib_sewing_id')]][$row[csf('body_part_id')]]['operation_name'];
			$smv_dtls_arr[$row[csf('extention_no')]]['style_ref']=$row[csf('style_ref')];
			$smv_dtls_arr[$row[csf('extention_no')]]['operation_count']=$row[csf('operation_count')];
			$smv_dtls_arr[$row[csf('extention_no')]]['id'].=$row[csf('id')].',';
			//$smv_dtls_arr[$row[csf('id')]]['gmts_item_id']=$row[csf('gmts_item_id')];
			$smv_dtls_arr[$row[csf('extention_no')]]['lib_sewing_id'].=$row[csf('lib_sewing_id')].',';
			//$smv_dtls_arr[$row[csf('id')]]['body_part_id']=$row[csf('body_part_id')];
			//$smv_dtls_arr[$row[csf('id')]]['operation_name']=$operation_name;
			//$code_id=$code_smv_arr[$row[csf('lib_sewing_id')]]['dcode'];
			$smv=0;
			$smv=$row[csf('operator_smv')]+$row[csf('helper_smv')];
			
			$smv_sewing_arr[$row[csf('extention_no')]][$row[csf('department_code')]]['operator_smv']+=$smv;
		}
		 //print_r($smv_dtls_arr);
		?>
		<table width="600" cellspacing="0" cellpadding="0" border="0" rules="all" class="rpt_table " >
			<thead>
				<tr>
					<th width="30">SL</th>
					<th width="100">Sys. ID.</th>
					<th width="50">Sys. ID.</th>
					<th width="200">Style</th>
					<th width="60">Avg. Sewing SMV</th>
					<th width="60">Avg. Cuting SMV</th>
					<th width="60">Avg. Finish SMV</th>
					<th>No of Operation</th>
				</tr>
			</thead>
			<tbody id="list_view">
			<?
			$i=1;
			foreach($smv_dtls_arr as $ext_no=>$arrdata)	
			{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
				//$lib_sewing_id=rtrim($arrdata['lib_sewing_id'],',');
				//$lib_sewing_ids=array_unique(explode(",",$lib_sewing_id));
				
				$finish_smv=$cut_smv=$sewing_smv=0;
				$finish_smv+=$smv_sewing_arr[4]['operator_smv'];
				$cut_smv+=$smv_sewing_arr[7]['operator_smv'];
				$sewing_smv+=$smv_sewing_arr[8]['operator_smv'];
				$sys_id=rtrim($arrdata['id'],',');
				$ids=array_filter(array_unique(explode(",",$sys_id)));
				//print_r($ids);
				$id_str=""; $k=0;
				foreach($ids as $idstr)
				{
					if($id_str=="") $id_str=$idstr; else $id_str.=','.$idstr;
					$k++;
				}
				$finish_smv=$finish_smv/$k;
				$cut_smv=$cut_smv/$k;
				$sewing_smv=$sewing_smv/$k;
				
				$data=$sewing_smv."_".$cut_smv."_".$finish_smv."_".$row_id."_".$id_str;
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" style="cursor:pointer" onClick="js_set_value('<? echo $data; ?>')">
					<td width="30"><? echo $i;//.'='.$k ?></td>
					<td width="140" style="word-break:break-all"><? echo $id_str; ?></td>
					<td width="50"><p><? echo $ext_no; ?></p></td>
					<td width="160" style="word-break:break-all"><? echo $arrdata['style_ref']; ?></td>
					<td width="60" align="right"><p><? echo number_format($sewing_smv,2); ?></p></td>
					<td width="60" align="right"><p><? echo number_format($cut_smv,2); ?></p></td>
					<td width="60" align="right"><p><? echo number_format($finish_smv,2); ?></p></td>
					<td><p><? echo $arrdata['operation_count']; ?></p></td>
				</tr>
				<?	
				$i++;
			}
			?>
			</tbody>
			<tfoot>
				<tr>
					<th colspan="3">&nbsp; </th>
					<th>&nbsp; </th>
					<th>&nbsp; </th>
					<th>&nbsp; </th>
					<th>&nbsp; </th>
				</tr>
			</tfoot>
		</table>
		<?
	}
	else
	{
		$sewing_sql="select a.id as lib_sewing_id, a.gmt_item_id, a.bodypart_id,a.operation_name, a.department_code as dcode from lib_sewing_operation_entry a where a.is_deleted=0  order by a.id Desc";//$gmts_item_con2 
		$result = sql_select($sewing_sql);
		foreach($result as $row)
		{
			$code_smv_arr[$row[csf('lib_sewing_id')]]['dcode']=$row[csf('dcode')];
			$code_smv_arr[$row[csf('lib_sewing_id')]][$row[csf('bodypart_id')]]['operation_name']=$row[csf('operation_name')];
		}
		// print_r($code_smv_arr);b.lib_sewing_id
		if($db_type==0) $group_con="group_concat(b.lib_sewing_id)  as lib_sewing_id";
		else $group_con="listagg(b.lib_sewing_id,',') within group (order by b.lib_sewing_id) as lib_sewing_id";
		
		$sql="select a.id, a.system_no, a.style_ref, a.operation_count, a.gmts_item_id, b.operator_smv, b.helper_smv, b.body_part_id, b.lib_sewing_id from ppl_gsd_entry_mst a,ppl_gsd_entry_dtls b where a.id=b.mst_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $gmts_item_con $style_con  $buyer_id_con order by a.system_no";
		$sql_result=sql_select($sql);
		foreach($sql_result as $row)	
		{
			$operation_name=$code_smv_arr[$row[csf('lib_sewing_id')]][$row[csf('body_part_id')]]['operation_name'];
			$smv_dtls_arr[$row[csf('id')]]['style_ref']=$row[csf('style_ref')];
			$smv_dtls_arr[$row[csf('id')]]['operation_count']=$row[csf('operation_count')];
			$smv_dtls_arr[$row[csf('id')]]['gmts_item_id']=$row[csf('gmts_item_id')];
			$smv_dtls_arr[$row[csf('id')]]['lib_sewing_id'].=$row[csf('lib_sewing_id')].',';
			$smv_dtls_arr[$row[csf('id')]]['body_part_id']=$row[csf('body_part_id')];
			$smv_dtls_arr[$row[csf('id')]]['operation_name']=$operation_name;
			$code_id=$code_smv_arr[$row[csf('lib_sewing_id')]]['dcode'];
			
			$smv_sewing_arr[$row[csf('id')]][$code_id][$row[csf('lib_sewing_id')]]['operator_smv']+=$row[csf('operator_smv')]+$row[csf('helper_smv')];
		}
		//print_r($smv_sewing_arr);
		?>
		<table width="600" cellspacing="0" cellpadding="0" border="0" rules="all" class="rpt_table " >
			<thead>
				<tr>
					<th width="30">SL</th>
					<th width="100">Sys. ID.</th>
					<th width="200">Style</th>
					<th width="60">Sewing SMV</th>
					<th width="60">Cuting SMV</th>
					<th width="60">Finish SMV</th>
					<th>No of Operation</th>
				</tr>
			</thead>
			<tbody id="list_view">
			<?
			$i=1;
			foreach($smv_dtls_arr as $sys_no=>$row)	
			{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
				
				$body_part_id=$row[('body_part_id')];
				$operation_name=$row[('operation_name')];
				//echo $operation_name;
				$lib_sewing_id=rtrim($row['lib_sewing_id'],',');
				$lib_sewing_ids=array_unique(explode(",",$lib_sewing_id));
				
				$finish_smv=$cut_smv=$sewing_smv=0;
				foreach($lib_sewing_ids as $lsid)
				{
					//echo $code_id.'<br>';
					$finish_smv+=$smv_sewing_arr[$sys_no][4][$lsid]['operator_smv'];
					$cut_smv+=$smv_sewing_arr[$sys_no][7][$lsid]['operator_smv'];
					$sewing_smv+=$smv_sewing_arr[$sys_no][8][$lsid]['operator_smv'];
				}
				$data=$sewing_smv."_".$cut_smv."_".$finish_smv."_".$row_id."_".$sys_no;
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" style="cursor:pointer" onClick="js_set_value('<? echo $data; ?>')">
					<td width="30"><p><? echo $i; ?></p></td>
					<td width="100"><p><? echo $sys_no; ?></p></td>
					<td width="200"><p><? echo $row[('style_ref')]; ?></p></td>
					<td width="60" align="right"><p><? echo number_format($sewing_smv,2); ?></p></td>
					<td width="60" align="right"><p><? echo number_format($cut_smv,2); ?></p></td>
					<td width="60" align="right"><p><? echo number_format($finish_smv,2); ?></p></td>
					<td><p><? echo $row[csf('operation_count')]; ?></p></td>
				</tr>
				<?	
				$i++;
			}
			?>
			</tbody>
			<tfoot>
				<tr>
					<th colspan="3">&nbsp; </th>
					<th>&nbsp; </th>
					<th>&nbsp; </th>
					<th>&nbsp; </th>
					<th>&nbsp; </th>
				</tr>
			</tfoot>
		</table>
		<?
	}
	exit();	
}


if($action=="create_item_smv_search_list_view")
{
	$data=explode('_',$data);
	$company=$data[0];
	$buyer_id=$data[1];
	$style=$data[2];
	$item_id=$data[3];
	$row_id=$data[4];

	//if ($company!=0) $company_con=" and a.company_id='$company'";else $company_con="";
	if ($buyer_id!=0) $buyer_id_con=" and a.buyer_id='$buyer_id'";else $buyer_id_con="";
	if ($style!="") $style_con=" and a.style_ref ='$style'";else $style_con="";
	if ($item_id!=0) $gmts_item_con=" and a.gmts_item_id='$item_id'";else $gmts_item_con="";
	if ($item_id!=0) $gmts_item_con2=" and a.gmt_item_id='$item_id'";else $gmts_item_con2="";
	?>
	<input type="hidden" id="selected_smv" name="selected_smv" />
	<?
	/*$sewing_sql="select a.id as lib_sewing_id, a.gmt_item_id, a.bodypart_id, a.operation_name, a.department_code as dcode from lib_sewing_operation_entry a where 0=0 $gmts_item_con2  order by a.id Desc";
	$result = sql_select($sewing_sql);
	foreach($result as $row)
	{
		$code_smv_arr[$row[csf('lib_sewing_id')]]['dcode']=$row[csf('dcode')];
		$code_smv_arr[$row[csf('lib_sewing_id')]][$row[csf('bodypart_id')]]['operation_name']=$row[csf('operation_name')];
	}*/
	// print_r($code_smv_arr);b.lib_sewing_id
	if($db_type==0)
	{
		$group_con="group_concat(b.lib_sewing_id)  as lib_sewing_id";
		$id_group_con="group_concat(a.id)";
	}
	else
	{
		$group_con="listagg(b.lib_sewing_id,',') within group (order by b.lib_sewing_id) as lib_sewing_id";
		$id_group_con="listagg(a.id,',') within group (order by a.id)";
	}

	$sql="select a.id, a.system_no, a.extention_no, a.style_ref, a.operation_count, a.gmts_item_id, b.operator_smv, b.helper_smv, b.body_part_id, b.lib_sewing_id, c.department_code from ppl_gsd_entry_mst a, ppl_gsd_entry_dtls b, lib_sewing_operation_entry c where a.id=b.mst_id and b.lib_sewing_id=c.id and a.approved=1 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1$gmts_item_con $style_con $buyer_id_con
	order by id DESC";

	$sql_result=sql_select($sql);
	foreach($sql_result as $row)
	{
		//$operation_name=$code_smv_arr[$row[csf('lib_sewing_id')]][$row[csf('body_part_id')]]['operation_name'];
		$smv_dtls_arr[$row[csf('extention_no')]]['style_ref']=$row[csf('style_ref')];
		$smv_dtls_arr[$row[csf('extention_no')]]['operation_count']=$row[csf('operation_count')];
		$smv_dtls_arr[$row[csf('extention_no')]]['id'].=$row[csf('id')].',';
		$smv_dtls_arr[$row[csf('extention_no')]]['system_no'].=$row[csf('system_no')].',';
		//$smv_dtls_arr[$row[csf('id')]]['gmts_item_id']=$row[csf('gmts_item_id')];
		$smv_dtls_arr[$row[csf('extention_no')]]['lib_sewing_id'].=$row[csf('lib_sewing_id')].',';
		//$smv_dtls_arr[$row[csf('id')]]['body_part_id']=$row[csf('body_part_id')];
		//$smv_dtls_arr[$row[csf('id')]]['operation_name']=$operation_name;
$code_id=$code_smv_arr[$row[csf('lib_sewing_id')]]['dcode'];
		$smv=0;
		$smv=$row[csf('operator_smv')]+$row[csf('helper_smv')];
		//echo $row[csf('operator_smv')].'<br>'.$row[csf('helper_smv')].'<br>';

		$smv_sewing_arr[$row[csf('id')]][$row[csf('department_code')]][$row[csf('lib_sewing_id')]]['operator_smv']+=$smv;
	}
	//print_r($smv_sewing_arr[8]);
	?>
	<table width="600" cellspacing="0" cellpadding="0" border="0" rules="all" class="rpt_table " >
        <thead>
            <tr>
                <th width="30">SL</th>
                <th width="120">Sys. ID.</th>
                <th width="50">Ext. NO</th>
                <th width="160">Style</th>
                <th width="60">Avg. Sewing SMV</th>
                <th width="60">Avg. Cuting SMV</th>
                <th width="60">Avg. Finish SMV</th>
                <th>No of Operation</th>
            </tr>
        </thead>
        <tbody id="list_view">
        <?
        $i=1;
		foreach($smv_dtls_arr as $ext_no=>$arrdata)
		{
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$lib_sewing_id=rtrim($arrdata['lib_sewing_id'],',');
			$lib_sewing_ids=array_unique(explode(",",$lib_sewing_id));

			$finish_smv=$cut_smv=$sewing_smv=0;

			$sys_id=rtrim($arrdata['id'],',');
			$ids=array_filter(array_unique(explode(",",$sys_id)));
			//print_r($ids);
			$id_str=""; $k=0;
			foreach($ids as $idstr)
			{
				if($id_str=="") $id_str=$idstr; else $id_str.=','.$idstr;

				foreach($lib_sewing_ids as $lsid)
				{
					$finish_smv+=$smv_sewing_arr[$idstr][4][$lsid]['operator_smv'];
					$cut_smv+=$smv_sewing_arr[$idstr][7][$lsid]['operator_smv'];
					$sewing_smv+=$smv_sewing_arr[$idstr][8][$lsid]['operator_smv'];
				}
				$k++;
			}

			$system_no=rtrim($arrdata['system_no'],',');
			$system_no=implode(",",array_filter(array_unique(explode(",",$system_no))));

			$finish_smv=$finish_smv/$k;
			$cut_smv=$cut_smv/$k;
			$sewing_smv=$sewing_smv/$k;

			$data=$sewing_smv."_".$cut_smv."_".$finish_smv."_".$row_id."_".$id_str;
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" style="cursor:pointer" onClick="js_set_value('<? echo $data; ?>')">
                <td width="30"><? echo $i;//.'='.$k ?></td>
                <td width="120" style="word-break:break-all"><? echo $system_no; ?></td>
                <td width="50" style="word-break:break-all"><? echo $ext_no; ?></td>
                <td width="160" style="word-break:break-all"><? echo $arrdata['style_ref']; ?></td>
                <td width="60" align="right"><p><? echo number_format($sewing_smv,2); ?></p></td>
                <td width="60" align="right"><p><? echo number_format($cut_smv,2); ?></p></td>
                <td width="60" align="right"><p><? echo number_format($finish_smv,2); ?></p></td>
                <td><p><? echo $arrdata['operation_count']; ?></p></td>
			</tr>
			<?
			$i++;
		}
        ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3">&nbsp; </th>
                <th>&nbsp; </th>
                <th>&nbsp; </th>
                <th>&nbsp; </th>
                <th>&nbsp; </th>
                <th>&nbsp; </th>
            </tr>
        </tfoot>
	</table>
	<?
	exit();
}




function fnc_smv_style_integration($db_type,$cbo_company_name,$txt_job_no,$data_int,$sewSmv,$cutSmv,$page)
{
	if($page==1)
	{
		$ex_data=explode("****",$data_int);
		$currercy=str_replace("'","",$ex_data[0]);
		$set_breck_down_arr=explode('__',str_replace("'",'',$ex_data[1]));
		$item_wise_arr=array(); $itm_arr=array();
		for($c=0; $c<count($set_breck_down_arr); $c++)
		{
			$set_breck_downdata_arr=explode('_',$set_breck_down_arr[$c]);
			$itm_arr[]=$set_breck_downdata_arr[0];
			$item_wise_arr[$set_breck_downdata_arr[0]]['ratio']=$set_breck_downdata_arr[1];
			$item_wise_arr[$set_breck_downdata_arr[0]]['smv_pcs']=$set_breck_downdata_arr[2];
			$item_wise_arr[$set_breck_downdata_arr[0]]['smv_set']=$set_breck_downdata_arr[3];
			$sewSmvn+=$set_breck_downdata_arr[3];
			$cutSmvn+=$set_breck_downdata_arr[7];
		}
		$is_pre_cost="";
		//return $db_type.'_##_'.$cbo_company_name.'_##_'.$txt_job_no.'_##_'.$data_int.'_##_'.$sewSmv.'_##_'.$cutSmv.'_##_'.$page; die;
		$pre_cost_data=sql_select("select job_no, cm_cost_predefined_method_id, sew_smv, cut_smv, sew_effi_percent, cut_effi_percent, exchange_rate, machine_line, prod_line_hr, costing_per, costing_date from wo_pre_cost_mst where job_no='$txt_job_no' and is_deleted=0 and status_active=1");
		$cm_cost=0;
		//echo "select job_no, cm_cost_predefined_method_id, sew_smv, cut_smv, sew_effi_percent, cut_effi_percent, exchange_rate, machine_line, prod_line_hr, costing_per, costing_date from wo_pre_cost_mst where job_no='$txt_job_no' and is_deleted=0 and status_active=1"; die;
		$cm_cost_predefined_method_id=$pre_cost_data[0][csf("cm_cost_predefined_method_id")]*1;
		$txt_sew_smv=$sewSmvn*1;//$pre_cost_data[0][csf("sew_smv")];
		$txt_cut_smv=$cutSmvn*1;//$pre_cost_data[0][csf("cut_smv")];
		$txt_sew_efficiency_per=$pre_cost_data[0][csf("sew_effi_percent")]*1;
		$txt_cut_efficiency_per=$pre_cost_data[0][csf("cut_effi_percent")]*1;
		
		$txt_exchange_rate= $pre_cost_data[0][csf("exchange_rate")]*1;
		$txt_machine_line= $pre_cost_data[0][csf("machine_line")];
		$txt_prod_line_hr= $pre_cost_data[0][csf("prod_line_hr")];
		$cbo_costing_per= $pre_cost_data[0][csf("costing_per")];
		$costing_date= $pre_cost_data[0][csf("costing_date")];
		
		$cbo_costing_per_value=0;
		if($cbo_costing_per==1) $cbo_costing_per_value=12;
		else if($cbo_costing_per==2) $cbo_costing_per_value=1;
		else if($cbo_costing_per==3) $cbo_costing_per_value=24;
		else if($cbo_costing_per==4) $cbo_costing_per_value=36;
		else if($cbo_costing_per==5) $cbo_costing_per_value=48;
		
		$cm_cost_method_based_on=return_field_value("cm_cost_method_based_on", "variable_order_tracking", "company_name=$cbo_company_name and variable_list=22 and status_active=1 and is_deleted=0");
		if($cm_cost_method_based_on=="") $cm_cost_method_based_on=1;
		
		if($cm_cost_method_based_on==0 || $cm_cost_method_based_on==1)
		{
			if($costing_date=="" || $costing_date==0)
			{
				if($db_type==0) $txt_costing_date=change_date_format(date('d-m-Y'), "yyyy-mm-dd", "-");	
				else if($db_type==2) $txt_costing_date=change_date_format(date('d-m-Y'), "yyyy-mm-dd", "-",1);
			}
			else
			{
				if($db_type==0) $txt_costing_date=change_date_format($costing_date, "yyyy-mm-dd", "-");	
				else if($db_type==2) $txt_costing_date=change_date_format($costing_date, "yyyy-mm-dd", "-",1)	;
			}
		}
		else if($cm_cost_method_based_on==2)
		{
			$min_shipment_sql=sql_select("select job_no_mst, min(shipment_date) as min_shipment_date from wo_po_break_down where job_no_mst='$txt_job_no' and status_active=1 and is_deleted=0 group by job_no_mst");
			$min_shipment_date="";
			foreach($min_shipment_sql as $row){ $min_shipment_date=$row[csf('min_shipment_date')]; }
			if($db_type==0) $txt_costing_date=change_date_format($min_shipment_date, "yyyy-mm-dd", "-");	
			else if($db_type==2) $txt_costing_date=change_date_format($min_shipment_date, "yyyy-mm-dd", "-",1)	;
		}
		else if($cm_cost_method_based_on==3)
		{
			$max_shipment_sql=sql_select("select job_no_mst, max(shipment_date) as max_shipment_date from wo_po_break_down where job_no_mst='$txt_job_no' and status_active=1 and is_deleted=0 group by job_no_mst");
			$max_shipment_date="";
			foreach($max_shipment_sql as $row){ $max_shipment_date=$row[csf('max_shipment_date')]; }
			
			if($db_type==0) $txt_costing_date=change_date_format($max_shipment_date, "yyyy-mm-dd", "-");	
			else if($db_type==2) $txt_costing_date=change_date_format($max_shipment_date, "yyyy-mm-dd", "-",1)	;
		}
		else if($cm_cost_method_based_on==4)
		{
			$max_shipment_sql=sql_select("select job_no_mst, min(pub_shipment_date) as min_pub_shipment_date from wo_po_break_down where job_no_mst='$txt_job_no' and status_active=1 and is_deleted=0 group by job_no_mst");
			$min_pub_shipment_date="";
			foreach($max_shipment_sql as $row){ $min_pub_shipment_date=$row[csf('min_pub_shipment_date')]; }
			
			if($db_type==0) $txt_costing_date=change_date_format($min_pub_shipment_date, "yyyy-mm-dd", "-");	
			else if($db_type==2) $txt_costing_date=change_date_format($min_pub_shipment_date, "yyyy-mm-dd", "-",1)	;
		}
		else if($cm_cost_method_based_on==4)
		{
			$max_shipment_sql=sql_select("select job_no_mst, max(pub_shipment_date) as max_pub_shipment_date from wo_po_break_down where job_no_mst='$txt_job_no' and status_active=1 and is_deleted=0 group by job_no_mst");
			$max_pub_shipment_date="";
			foreach($max_shipment_sql as $row){ $max_pub_shipment_date=$row[csf('max_pub_shipment_date')]; }
			
			if($db_type==0) $txt_costing_date=change_date_format($max_pub_shipment_date, "yyyy-mm-dd", "-");	
			else if($db_type==2) $txt_costing_date=change_date_format($max_pub_shipment_date, "yyyy-mm-dd", "-",1)	;
		}
		
		$monthly_cm_expense=0; $no_factory_machine=0; $working_hour=0; $cost_per_minute=0; $depreciation_amorti=0; $operating_expn=0;
		$limit="";
		if($db_type==0) $limit="LIMIT 1"; else if($db_type==2) $limit="";
		$sqlstnd_cm="select monthly_cm_expense, no_factory_machine, working_hour, cost_per_minute, depreciation_amorti, operating_expn from lib_standard_cm_entry where company_id=$cbo_company_name and '$txt_costing_date' between applying_period_date and applying_period_to_date and status_active=1 and is_deleted=0 $limit";
		$sqlstnd_cm_arr=sql_select($sqlstnd_cm);
		foreach ($sqlstnd_cm_arr as $row)
		{
			if($row[csf("monthly_cm_expense")] !="") $monthly_cm_expense=$row[csf("monthly_cm_expense")];
			if($row[csf("no_factory_machine")] !="") $no_factory_machine=$row[csf("no_factory_machine")];
			if($row[csf("working_hour")] !="") $working_hour=$row[csf("working_hour")];
			if($row[csf("cost_per_minute")] !="") $cost_per_minute=$row[csf("cost_per_minute")];
			if($row[csf("depreciation_amorti")] !="") $depreciation_amorti=$row[csf("depreciation_amorti")];
			if($row[csf("operating_expn")] !="")$operating_expn=$row[csf("operating_expn")];
		}
		//$data=$monthly_cm_expense."_".$no_factory_machine."_".$working_hour."_".$cost_per_minute."_".$depreciation_amorti."_".$operating_expn;
		
		$sql_pre_cost_dtls="select sum(price_dzn) as price_dzn, sum(price_pcs_or_set) as price_pcs_set, sum(total_cost-cm_cost) as prev_tot_cost from wo_pre_cost_dtls where job_no='$txt_job_no' and is_deleted=0 and status_active=1 group by job_no";
		$sql_pre_cost_dtls_arr=sql_select($sql_pre_cost_dtls);
		$price_dzn=0; $cost_pcs_set=0; $prev_tot_cost=0;
		
		$price_dzn=$sql_pre_cost_dtls_arr[0][csf("price_dzn")]*1;
		$price_pcs_set=$sql_pre_cost_dtls_arr[0][csf("price_pcs_set")]*1;
		$prev_tot_cost=$sql_pre_cost_dtls_arr[0][csf("prev_tot_cost")]*1;
		
		
		if (count($pre_cost_data)>0)
		{
			execute_query( "update wo_pre_cost_mst set sew_smv='$txt_sew_smv', cut_smv='$txt_cut_smv' where job_no ='".$txt_job_no."'",1);
			if($cm_cost_predefined_method_id==1)
			{
				$txt_efficiency_wastage=100-$txt_sew_efficiency_per;
				//document.getElementById('txt_efficiency_wastage').value=txt_efficiency_wastage;
				$cm_cost=($txt_sew_smv*$cost_per_minute*$cbo_costing_per_value)+(($txt_sew_smv*$cost_per_minute*$cbo_costing_per_value)*($txt_efficiency_wastage/100));
				//alert(txt_exchange_rate)
				$cm_cost=$cm_cost/$txt_exchange_rate;
			}
			else if($cm_cost_predefined_method_id==2)
			{
				$cu=0; $su=0;
				$cut_per=$txt_cut_efficiency_per/100;
				$sew_per=$txt_sew_efficiency_per/100;
				$cu=($txt_cut_smv*trim(($cost_per_minute*1))*$cbo_costing_per_value)/($cut_per*1);
				if($cu=="") $cu=0;
				
				$su=($txt_sew_smv*trim(($cost_per_minute*1))*$cbo_costing_per_value)/($sew_per*1);
				if($su=='') $su=0;
				$cm_cost=($cu+$su)/$txt_exchange_rate;
			}
			else if($cm_cost_predefined_method_id==3)
			{
				//3. CM Cost = {(MCE/26)/NFM)*MPL)}/[{(PHL)*WH}]*Costing Per/Exchange Rate
				$per_day_cost=$monthly_cm_expense/26;
				$per_machine_cost=$per_day_cost/$no_factory_machine;
				$per_line_cost=$per_machine_cost*$txt_machine_line;
				$total_production_per_line=$txt_prod_line_hr*$working_hour;
				$per_product_cost=$per_line_cost/$total_production_per_line;
				
				$cm_cost=($per_product_cost*$cbo_costing_per_value)/$txt_exchange_rate;
			}
			else if($cm_cost_predefined_method_id==4)
			{
				$sew_per=$txt_sew_efficiency_per/100;
				$su=((trim(($cost_per_minute*1))/$sew_per)*($txt_sew_smv*$cbo_costing_per_value));
				$cm_cost=$su/$txt_exchange_rate;
			}
			
			$dec_type=0;
			if (str_replace("'","",$currercy)==1) $dec_type=4; else $dec_type=5;
			
			$cm_cost=number_format($cm_cost,6,'.','');
			$cm_cost_per=number_format((($cm_cost/$price_dzn)*100),2,'.','');
			
			$tot_cost=number_format(($prev_tot_cost+$cm_cost),6,'.','');
			$tot_cost_per=number_format((($tot_cost/$price_dzn)*100),2,'.','');
			
			$margin_dzn=number_format(($price_dzn-$tot_cost),6,'.','');
			$margin_dzn_per=number_format((100-$tot_cost_per),2,'.','');
			
			$cost_pcs_set=number_format(($tot_cost/$cbo_costing_per_value),6,'.','');
			$cost_pcs_set_percent=number_format((($cost_pcs_set/$price_pcs_set)*100),2,'.','');
			
			$margin_pcs_set=number_format(($price_pcs_set-$cost_pcs_set),6,'.','');
			$margin_pcs_set_per=number_format((100-$cost_pcs_set_percent),2,'.','');
			
			
			$field_arr_pre_cost="cm_cost*cm_cost_percent*total_cost*total_cost_percent*margin_dzn*margin_dzn_percent*cost_pcs_set*cost_pcs_set_percent*margin_pcs_set*margin_pcs_set_percent";
			$data_arr_pre_cost="'".$cm_cost."'*'".$cm_cost_per."'*'".$tot_cost."'*'".$tot_cost_per."'*'".$margin_dzn."'*'".$margin_dzn_per."'*'".$cost_pcs_set."'*'".$cost_pcs_set_percent."'*'".$margin_pcs_set."'*'".$margin_pcs_set_per."'";
			//return $data_arr_pre_cost; die;
			//'7.8279'*'18.12'*'21.1777'*'49.02'*'22.0223'*'50.98'*'1.7648'*'49.02'*'1.8352'*'50.98'
			$rID2=sql_update("wo_pre_cost_dtls",$field_arr_pre_cost,$data_arr_pre_cost,"job_no","'".$txt_job_no."'",1);
		}
		else
		{
			return;
		}
		//return $field_arr_pre_cost.'='.$data_arr_pre_cost; 
	}
}
?>
