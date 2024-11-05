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
Updated by 		         : 		
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

//************************************ Start*************************************************
$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name"  );

// Master Form*************************************Master Form*************************

function publish_shipment_date($data){
	$publish_shipment_date=return_field_value("publish_shipment_date", "variable_order_tracking", "company_name=$data  and variable_list=25  and status_active=1 and is_deleted=0");
	if($publish_shipment_date !=""){
	  return trim($publish_shipment_date);	
	}
	else{
		return 1;
	}
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
	$copy_quotation=return_field_value("copy_quotation", "variable_order_tracking", "company_name=$data  and variable_list=20  and status_active=1 and is_deleted=0");
	if($copy_quotation !="")
	{
	  return trim($copy_quotation);	
	}
	else
	{
		return 2;
	}
}

function season_mandatory($data){
	//echo "select season_mandatory from variable_order_tracking where company_name=$data  and variable_list=44  and status_active=1 and is_deleted=0";
	$season_mandatory=return_field_value("season_mandatory", "variable_order_tracking", "company_name=$data  and variable_list=44  and status_active=1 and is_deleted=0");
	if($season_mandatory !="")
	{
	  return trim($season_mandatory);	
	}
	else
	{
		return 2;
	}
}

if ($action=="cbo_factory_merchant")
{
	echo create_drop_down( "cbo_factory_merchant", 150, "select id,team_member_name from lib_mkt_team_member_info where team_id='$data' and status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-- Select Team Member --", $selected, "" );
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
                <th width="150">Company Name</th>
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
             <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('chk_job_wo_po').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('garments_nature').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('txt_internal_ref').value+'_'+document.getElementById('txt_file_no').value, 'create_po_search_list_view', 'search_div', 'woven_order_entry_controller_update', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" /></td>
    	</tr>
        <tr class="general">
            <td align="center" valign="middle" colspan="10"><? echo load_month_buttons(1); ?></td>
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
	if($db_type==2)
	{
	$year_cond=" and to_char(a.insert_date,'YYYY')=$data[7]";
	if ($data[3]!="" &&  $data[4]!="") $shipment_date = "and b.pub_shipment_date between '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."'"; else $shipment_date ="";
	}
	$order_cond="";
	$job_cond=""; 
	$style_cond="";
	
	if($data[8]==1)
	{
	  if (str_replace("'","",$data[6])!="") $job_cond=" and a.job_no_prefix_num='$data[6]'  $year_cond";
	  if (trim($data[9])!="") $order_cond=" and b.po_number='$data[9]'  "; //else  $order_cond=""; 
	  if (trim($data[10])!="") $style_cond=" and a.style_ref_no='$data[10]'  "; //else  $style_cond=""; 
	}
	
	if($data[8]==4 || $data[8]==0)
	{
	  if (str_replace("'","",$data[6])!="") $job_cond=" and a.job_no_prefix_num like '%$data[6]%'  $year_cond"; //else  $job_cond=""; 
	  if (trim($data[9])!="") $order_cond=" and b.po_number like '%$data[9]%'  ";
	  if (trim($data[10])!="") $style_cond=" and a.style_ref_no like '%$data[10]%'  "; //else  $style_cond=""; 
	}
	
	if($data[8]==2)
	{
	  if (str_replace("'","",$data[6])!="") $job_cond=" and a.job_no_prefix_num like '$data[6]%'  $year_cond"; //else  $job_cond=""; 
	  if (trim($data[9])!="") $order_cond=" and b.po_number like '$data[9]%'  ";
	  if (trim($data[10])!="") $style_cond=" and a.style_ref_no like '$data[10]%'  "; //else  $style_cond=""; 
	}
	
	if($data[8]==3)
	{
	  if (str_replace("'","",$data[6])!="") $job_cond=" and a.job_no_prefix_num like '%$data[6]'  $year_cond"; //else  $job_cond=""; 
	  if (trim($data[9])!="") $order_cond=" and b.po_number like '%$data[9]'  ";
	  if (trim($data[10])!="") $style_cond=" and a.style_ref_no like '%$data[10]'  "; //else  $style_cond=""; 
	}
			
	$internal_ref = str_replace("'","",$data[11]);
	$file_no = str_replace("'","",$data[12]);
	if ($file_no=="") $file_no_cond=""; else $file_no_cond=" and b.file_no='".trim($file_no)."' "; 
	if ($internal_ref=="") $internal_ref_cond=""; else $internal_ref_cond=" and b.grouping='".trim($internal_ref)."' "; 
	/*if($file_no!="" || $internal_ref!="")
	{
	$sql_po=sql_select("select b.id from  wo_po_break_down b where   b.status_active=1 and b.is_deleted=0  $file_no_cond  $internal_ref_cond");
	 $po_id_data=$sql_po[0][csf('id')];
	}
	if($po_id_data!="" || $po_id_data!=0) $po_data_cond=" and b.id='$po_id_data' "; else $po_data_cond="";*/
	
	
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$arr=array (2=>$comp,3=>$buyer_arr,9=>$item_category);
	if ($data[2]==0)
	{
		if($db_type==0)
		{
	 		$sql= "select a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.job_quantity, b.po_number, b.po_quantity, b.shipment_date, a.garments_nature, b.grouping, b.file_no, DATEDIFF(pub_shipment_date,po_received_date) as date_diff, SUBSTRING_INDEX(a.`insert_date`, '-', 1) as year from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.garments_nature=$data[5] and a.status_active=1 and b.status_active=1 $shipment_date $company $buyer $job_cond $order_cond $style_cond $file_no_cond $internal_ref_cond ".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." order by b.id DESC";
		}
	 	else if($db_type==2)
		{
	 		$sql= "select a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.job_quantity, b.po_number, b.po_quantity, b.shipment_date, a.garments_nature, b.grouping, b.file_no, (pub_shipment_date - po_received_date) as  date_diff, to_char(a.insert_date,'YYYY') as year from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.garments_nature=$data[5] and a.status_active=1 and b.status_active=1 $shipment_date $company $buyer ".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." $job_cond $order_cond $style_cond $file_no_cond $internal_ref_cond order by b.id DESC";
		}
		//echo $sql;
		 echo  create_list_view("list_view", "Job No,Year,Company,Buyer Name,Style Ref. No,Job Qty.,PO number,PO Quantity,Shipment Date,Gmts Nature,Ref no, File No,Lead time", "40,30,120,100,100,70,90,70,60,90,70,70,50","1020","320",0, $sql , "js_set_value", "job_no", "", 1, "0,0,company_name,buyer_name,0,0,0,0,0,garments_nature,0,0,0", $arr , "job_no_prefix_num,year,company_name,buyer_name,style_ref_no,job_quantity,po_number,po_quantity,shipment_date,garments_nature,grouping,file_no,date_diff", "",'','0,0,0,0,0,1,0,1,3,0,0,0,0');
	}
	else
	{
		$arr=array (2=>$comp,3=>$buyer_arr,5=>$item_category);
		if($db_type==0)
		{
			$sql= "select a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.garments_nature, SUBSTRING_INDEX(a.`insert_date`, '-', 1) as year from wo_po_details_master a where a.job_no not in( select distinct job_no_mst from wo_po_break_down where status_active=1 and is_deleted=0 ) and a.garments_nature=$data[5] and a.status_active=1 ".set_user_lavel_filtering(' and a.buyer_name','buyer_id')."   and a.is_deleted=0 $company $buyer $job_cond $style_cond  order by a.id DESC";
		}
		else if($db_type==2)
		{
			$sql= "select a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.garments_nature, to_char(a.insert_date,'YYYY') as year from wo_po_details_master a where a.job_no not in( select distinct job_no_mst from wo_po_break_down where status_active=1 and is_deleted=0 ) and a.garments_nature=$data[5] and a.status_active=1 ".set_user_lavel_filtering(' and a.buyer_name','buyer_id')."  and a.is_deleted=0 $company $buyer $job_cond $style_cond order by a.id DESC";
		}
		echo  create_list_view("list_view", "Job No,Year,Company,Buyer Name,Style Ref. No,Gmts Nature", "90,60,120,100,100,90","1000","320",0, $sql , "js_set_value", "job_no", "", 1, "0,0,company_name,buyer_name,0,garments_nature", $arr , "job_no_prefix_num,year,company_name,buyer_name,style_ref_no,garments_nature", "",'','0,0,0,0,0,0');
	}
	exit();
}

function get_company_config($data){
	$cbo_location_name= create_drop_down( "cbo_location_name", 150, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select --", $selected, "" );
	global $buyer_cond;
	$cbo_buyer_name= create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "get_buyer_config(this.value)" ); 
	
	$cbo_agent= create_drop_down( "cbo_agent", 150, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id and b.tag_company='$data' and a.id in (select  buyer_id from  lib_buyer_party_type where party_type in (20,21))  order by buyer_name","id,buyer_name", 1, "-- Select Agent --", $selected, "" ); 
	$cbo_client= create_drop_down( "cbo_client", 150, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id and b.tag_company='$data' and a.id in (select  buyer_id from  lib_buyer_party_type where party_type in (7))  order by buyer_name","id,buyer_name", 1, "-- Select Client --", $selected, "" );
	$publish_shipment_date=publish_shipment_date($data);
	//$update_period_maintained_data=update_period_maintained_data($data);
	$po_received_date_maintained_data=po_received_date_maintained_data($data);
	$copy_quotation=copy_quotation($data);
	$season_mandatory=season_mandatory($data);
	
	echo "document.getElementById('location').innerHTML = '".$cbo_location_name."';\n";
	echo "document.getElementById('buyer_td').innerHTML = '".$cbo_buyer_name."';\n";
	echo "document.getElementById('agent_td').innerHTML = '".$cbo_agent."';\n";
	echo "document.getElementById('party_type_td').innerHTML = '".$cbo_client."';\n";
	//echo "publish_shipment_date(".$publish_shipment_date.");\n";
	//echo "budget_exceeds_quot(".$copy_quotation.");\n";
	//echo "document.getElementById('po_update_period_maintain').value = '".$update_period_maintained_data['po_update_period']."';\n";
	//echo "document.getElementById('txt_user_id').value = '".$update_period_maintained_data['user_id']."';\n";
	//echo "document.getElementById('po_current_date_maintain').value = '".$po_received_date_maintained_data."';\n";
	
	$smv_id=return_field_value("publish_shipment_date","variable_order_tracking","company_name =$data and variable_list=47 and is_deleted=0 and status_active=1");
	
	/*$sew_company_location=return_field_value("season_mandatory","variable_order_tracking","company_name =$data and variable_list=64 and is_deleted=0 and status_active=1");
	if($sew_company_location=="" || $sew_company_location==0) $sew_company_location=0; else $sew_company_location=$sew_company_location;
	echo "document.getElementById('sewing_company_validate_id').value 	= '".$sew_company_location."';\n";
	*/
	if($smv_id=="" || $smv_id==0) $smv_id=0; else $smv_id=$smv_id;
	echo "document.getElementById('set_smv_id').value 	= '".$smv_id."';\n";
	
	//echo "document.getElementById('is_season_must').value 	= '".$season_mandatory."';\n";
}

if($action=="get_company_config"){
	$action($data);
}

if ($action=="populate_data_from_search_popup")
{
	$data_array=sql_select("select id, garments_nature, job_no, job_no_prefix, job_no_prefix_num, company_name, buyer_name, location_name, style_ref_no, style_description, product_dept, product_code, pro_sub_dep, currency_id, agent_name, client_id, order_repeat_no, region, product_category, team_leader, dealing_marchant, bh_merchant, packing, remarks, ship_mode, order_uom, set_break_down, gmts_item_id, total_set_qnty, set_smv, season_buyer_wise, quotation_id, job_quantity, order_uom, avg_unit_price, currency_id, total_price, factory_marchant from wo_po_details_master where job_no='$data'");
	foreach ($data_array as $row)
	{
		//echo "load_drop_down( 'requires/woven_order_entry_controller_update', '".$row[csf("company_name")]."', 'load_drop_down_location', 'location' ); load_drop_down( 'requires/woven_order_entry_controller', '".$row[csf("company_name")]."', 'load_drop_down_buyer', 'buyer_td' ); load_drop_down( 'requires/woven_order_entry_controller_update', '".$row[csf("company_name")]."', 'load_drop_down_agent', 'agent_td' );load_drop_down( 'requires/woven_order_entry_controller_update', '".$row[csf("company_name")]."', 'load_drop_down_party_type', 'party_type_td' );sub_dept_load('".$row[csf("buyer_name")]."','".$row[csf("product_dept")]."');\n";
		//echo "load_drop_down( 'requires/woven_order_entry_controller_update', '".$row[csf("team_leader")]."', 'cbo_dealing_merchant', 'div_marchant' ) ;\n";
		//echo "load_drop_down( 'requires/woven_order_entry_controller_update', '".$row[csf("team_leader")]."', 'cbo_factory_merchant', 'div_marchant_factory' ) ;\n";
		
		$cbo_dealing_merchant= create_drop_down( "cbo_dealing_merchant", 150, "select id,team_member_name from lib_mkt_team_member_info where team_id='".$row[csf("team_leader")]."'and status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-- Select Team Member --", $selected, "" );
		
		$cbo_factory_merchant= create_drop_down( "cbo_factory_merchant", 150, "select id,team_member_name from lib_mkt_team_member_info where team_id='".$row[csf("team_leader")]."' and status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-- Select Team Member --", $selected, "" );
		$cbo_projected_po= create_drop_down( "cbo_projected_po", 110, "select id,po_number from  wo_po_break_down where job_no_mst='".$row[csf("job_no")]."'   and is_confirmed=2 and status_active =1 and is_deleted=0 order by po_number","id,po_number", 1, "-- Select --", "", "" );
		
		//$active_po_list=show_po_active_listview($row[csf("job_no")]);
		
		echo "document.getElementById('div_marchant').innerHTML = '".$cbo_dealing_merchant."';\n";
		echo "document.getElementById('div_marchant_factory').innerHTML = '".$cbo_factory_merchant."';\n";
		echo "document.getElementById('projected_po_td').innerHTML = '".$cbo_projected_po."';\n";
		get_company_config($row[csf("company_name")]);
		//echo "publish_shipment_date('".$row[csf("company_name")]."');\n";
		echo "document.getElementById('garments_nature').value = '".$row[csf("garments_nature")]."';\n";  
		echo "document.getElementById('txt_job_no').value = '".$row[csf("job_no")]."';\n";  
		echo "document.getElementById('hidd_job_id').value = '".$row[csf("id")]."';\n";
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_name")]."';\n";  
		echo "document.getElementById('cbo_location_name').value = '".$row[csf("location_name")]."';\n";  
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_name")]."';\n";  
		echo "document.getElementById('txt_style_ref').value = '".$row[csf("style_ref_no")]."';\n";  
		echo "document.getElementById('txt_style_description').value = '".$row[csf("style_description")]."';\n";  
		echo "document.getElementById('cbo_product_department').value = '".$row[csf("product_dept")]."';\n";
		echo "document.getElementById('txt_product_code').value = '".$row[csf("product_code")]."';\n";
		echo "document.getElementById('txt_bhmerchant').value = '".$row[csf("bh_merchant")]."';\n";
		echo "document.getElementById('cbo_sub_dept').value = '".$row[csf("pro_sub_dep")]."';\n";
		echo "document.getElementById('cbo_currercy').value = '".$row[csf("currency_id")]."';\n";  
		echo "document.getElementById('cbo_agent').value = '".$row[csf("agent_name")]."';\n"; 
		echo "document.getElementById('cbo_client').value = '".$row[csf("client_id")]."';\n"; 
		
		 
		echo "document.getElementById('txt_repeat_no').value = '".$row[csf("order_repeat_no")]."';\n";  
		echo "document.getElementById('cbo_region').value = '".$row[csf("region")]."';\n";  
		echo "document.getElementById('txt_item_catgory').value = '".$row[csf("product_category")]."';\n";  
		echo "document.getElementById('cbo_team_leader').value = '".$row[csf("team_leader")]."';\n";  
		echo "document.getElementById('cbo_dealing_merchant').value = '".$row[csf("dealing_marchant")]."';\n";  
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
		echo "document.getElementById('txt_avg_unit_price').value = '".$row[csf("avg_unit_price")]."';\n";
		//echo "document.getElementById('set_unit').value = '".$currency[$row[csf("currency_id")]]."';\n";
		echo "document.getElementById('cbo_factory_merchant').value = '".$row[csf("factory_marchant")]."';\n";
		echo "document.getElementById('set_unit').value = '".$row[csf("currency_id")]."';\n";
		echo "document.getElementById('projected_set_unit').value = '".$row[csf("currency_id")]."';\n";  
		echo "document.getElementById('txt_job_total_price').value = '".$row[csf("total_price")]."';\n";
		
		 if($_SESSION['logic_erp']['data_arr'][122][$row[csf("company_name")]][txt_excess_cut][is_disable]==1)
		 {
			 echo "$('#txt_excess_cut').attr('disabled','true')".";\n";
		 }
		 else
		 {
			 echo "$('#txt_excess_cut').removeAttr('disabled')".";\n";
		 }
		
		//echo "load_drop_down( 'requires/woven_order_entry_controller_update','".$row[csf("buyer_name")]."', 'load_drop_down_season_buyer', 'season_td') ;";
		 $seasion_td=create_drop_down( "cbo_season_name", 150, "select id, season_name from lib_buyer_season where buyer_id='".$row[csf("buyer_name")]."' and status_active =1 and is_deleted=0 order by season_name ASC","id,season_name", 1, "-- Select Season--", "", "" );
		echo "document.getElementById('season_td').innerHTML = '".$seasion_td."';\n";
		echo "document.getElementById('cbo_season_name').value = '".$row[csf("season_buyer_wise")]."';\n";
		//echo "document.getElementById('cbo_season_name').value = '".$row[csf("season_buyer_wise")]."';\n";
		echo "document.getElementById('update_id').value = '".$row[csf("job_no")]."';\n";  
	}
	
	$projected_data_array=sql_select("select sum(original_po_qty) as projected_qty,sum(original_po_qty*original_avg_price) as projected_amount  ,(sum(original_po_qty*original_avg_price)/sum(original_po_qty)) as projected_rate 
 from wo_po_break_down where job_no_mst='$data' and is_confirmed=2");
		foreach ($projected_data_array as $row_val)
	{
	    echo "document.getElementById('txt_projected_job_quantity').value = '".$row_val[csf("projected_qty")]."';\n";
		echo "document.getElementById('txt_projected_price').value = '".number_format($row_val[csf("projected_rate")],4)."';\n";
		echo "document.getElementById('txt_project_total_price').value = '".$row_val[csf("projected_amount")]."';\n";
	}
	$smv_id=return_field_value("publish_shipment_date","variable_order_tracking","company_name ='".$row[csf("company_name")]."' and variable_list=47 and is_deleted=0 and status_active=1");
	if($smv_id=="" || $smv_id==0) $smv_id=0; else $smv_id=$smv_id;
	echo "document.getElementById('set_smv_id').value 	= '".$smv_id."';\n";
	exit();
}

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 150, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select --", $selected, "" );
	exit();	 
}

if ($action=="load_drop_down_season_buyer")
{
	$datas=explode('_',$data);
	//echo create_drop_down( "cbo_season_name", 150, "select a.id,a.season_name from lib_buyer_season a,variable_order_tracking b where a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and a.buyer_id='$datas[0]' and b.company_name='$datas[1]' and b.season_mandatory=1 and b.variable_list=44","id,season_name", 1, "-- Select Season --", $selected, "" );
	echo create_drop_down( "cbo_season_name", 150, "select id, season_name from lib_buyer_season where buyer_id='$data' and status_active =1 and is_deleted=0 order by season_name ASC","id,season_name", 1, "-- Select Season--", "", "" );
	exit();
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "load_drop_down( 'requires/woven_order_entry_controller_update', this.value, 'load_drop_down_season_buyer', 'season_td');sub_dept_load(this.value,document.getElementById('cbo_product_department').value)" );   
	exit();	 
} 

if ($action=="load_drop_down_agent")
{
	echo create_drop_down( "cbo_agent", 150, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id and b.tag_company='$data' and a.id in (select  buyer_id from  lib_buyer_party_type where party_type in (20,21))  order by buyer_name","id,buyer_name", 1, "-- Select Agent --", $selected, "" );  
	 exit();	 
} 

if ($action=="load_drop_down_party_type")
{
	echo create_drop_down( "cbo_client", 150, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id and b.tag_company='$data' and a.id in (select  buyer_id from  lib_buyer_party_type where party_type in (7))  order by buyer_name","id,buyer_name", 1, "-- Select Client --", $selected, "" );  
	exit();	 
}

if ($action=="cbo_dealing_merchant")
{
	echo create_drop_down( "cbo_dealing_merchant", 150, "select id,team_member_name from lib_mkt_team_member_info where team_id='$data' and status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-- Select Team Member --", $selected, "" );
	exit();
}

if ($action=="load_drop_down_sub_dep")
{
	$data=explode("_",$data);
	echo create_drop_down( "cbo_sub_dept", 150, "select id,sub_department_name from lib_pro_sub_deparatment where buyer_id=$data[0] and	department_id='$data[1]' and status_active =1 and is_deleted=0 order by sub_department_name","id,sub_department_name", 1, "-- Select Sub Dep --", $selected, "" );
	exit();
}

if ($action=="load_drop_down_projected_po")
{
	echo create_drop_down( "cbo_projected_po", 100, "select id,po_number from  wo_po_break_down where job_no_mst='$data'   and is_confirmed=2 and status_active =1 and is_deleted=0 order by po_number","id,po_number", 1, "-- Select --", "", "" );
	exit();
}

if ($action=="load_drop_down_tna_task")
{
	$sql_task = "SELECT a.id,task_template_id,lead_time,material_source,total_task,tna_task_id,deadline,execution_days,notice_before,a.sequence_no,for_specific,b.task_catagory,b.task_name FROM  tna_task_template_details a, lib_tna_task b WHERE  a.is_deleted = 0 and a.status_active=1 and a.tna_task_id=b.id order by for_specific,lead_time";
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
	echo create_drop_down( "cbo_tna_task", 90, "select a.id, concat(a.sequence_no,'-',b.task_short_name) as task_short_name ,a.tna_task_id from  tna_task_template_details a,lib_tna_task b where a.tna_task_id=b.id and task_template_id='$template_id'  and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 order by a.sequence_no","id,task_short_name", 1, "-- Select --", "", "" );
	exit();
}

if($action=="publish_shipment_date")
{
	$publish_shipment_date=return_field_value("publish_shipment_date", "variable_order_tracking", "company_name=$data  and variable_list=25  and status_active=1 and is_deleted=0");
	if($publish_shipment_date !="")
	{
	  echo trim($publish_shipment_date);	
	}
	else
	{
		echo 1;
	}
	die;
}

if ($action=="show_po_active_listview")
{
	$arr=array (0=>$order_status,12=>$row_status);
	
	if($db_type==0)
	{
		$sql= "select is_confirmed, po_number, po_received_date, pub_shipment_date, shipment_date, po_quantity, unit_price, po_total_price, excess_cut, plan_cut, DATEDIFF(pub_shipment_date,po_received_date) as  date_diff, DATEDIFF(pub_shipment_date,factory_received_date) as facdate_diff, status_active, id from wo_po_break_down where status_active=1 and is_deleted=0 and job_no_mst='$data' order by po_number ASC"; 
	}
	else if($db_type==2)
	{
		$sql= "select is_confirmed, po_number, po_received_date, pub_shipment_date, shipment_date, po_quantity, unit_price, po_total_price, excess_cut, plan_cut, (pub_shipment_date-po_received_date) as  date_diff, (pub_shipment_date-factory_received_date) as  facdate_diff, status_active, id from wo_po_break_down where status_active=1 and is_deleted=0 and job_no_mst='$data' order by po_number ASC"; 
	}
	echo  create_list_view("list_view", "Order Status,PO No,PO Recv. Date,Ship Date,Orgn. Ship Date,PO Qnty,Avg. Rate,Amount, Excess Cut %,Plan Cut Qnty,Lead Time,Lead time on Fac Rcv Date,Status", "70,110,65,65,65,80,60,80,60,80,50,50","950","220",0, $sql , "get_details_form_data", "id", "'populate_order_details_form_data'", 1, "is_confirmed,0,0,0,0,0,0,0,0,0,0,0,status_active", $arr , "is_confirmed,po_number,po_received_date,pub_shipment_date,shipment_date,po_quantity,unit_price,po_total_price,excess_cut,plan_cut,date_diff,facdate_diff,status_active", "requires/woven_order_entry_controller_update",'','0,0,3,3,3,1,4,4,2,2,1,1') ;
	
	/*if($db_type==0)
	{
		$sql= "select is_confirmed, po_number, po_received_date, shipment_date, pub_shipment_date, po_quantity, unit_price, po_total_price, excess_cut, plan_cut, DATEDIFF(shipment_date,po_received_date) as date_diff, DATEDIFF(pub_shipment_date,factory_received_date) as facdate_diff, status_active, id from wo_po_break_down where status_active !=1 and job_no_mst='$data'"; 
	}
	else if($db_type==2)
	{
		$sql= "select is_confirmed, po_number, po_received_date, shipment_date, pub_shipment_date, po_quantity, unit_price, po_total_price, excess_cut, plan_cut, (shipment_date-po_received_date) as  date_diff,(pub_shipment_date-factory_received_date) as  facdate_diff, status_active, id from wo_po_break_down where status_active !=1 and job_no_mst='$data'"; 
	}
	$sqldata=sql_select($sql);
	if(count($sqldata)>0)
	{
		echo  create_list_view("list_view", "Order Status,PO No,PO Recv. Date,Ship Date,Orgn. Ship Date,PO Qnty,Avg. Rate,Amount, Excess Cut %,Plan Cut Qnty,Lead Time,Lead time on Fac Rcv Date,Status", "70,110,65,65,65,80,60,80,60,80,50,50","950","220",0, $sql , "get_details_form_data", "id", "'populate_order_details_form_data'", 1, "is_confirmed,0,0,0,0,0,0,0,0,0,0,0,status_active", $arr , "is_confirmed,po_number,po_received_date,shipment_date,pub_shipment_date,po_quantity,unit_price,po_total_price,excess_cut,plan_cut,date_diff,facdate_diff,status_active", "requires/woven_order_entry_controller_update",'','0,0,3,3,3,1,4,4,2,2,1,1') ;
	}*/
	exit();
}

if ($action=="show_deleted_po_active_listview")
{
	$arr=array (0=>$order_status,12=>$row_status);
	
	/*if($db_type==0)
	{
		$sql= "select is_confirmed, po_number, po_received_date, pub_shipment_date, shipment_date, po_quantity, unit_price, po_total_price, excess_cut, plan_cut, DATEDIFF(pub_shipment_date,po_received_date) as  date_diff, DATEDIFF(pub_shipment_date,factory_received_date) as facdate_diff, status_active, id from wo_po_break_down where status_active=0 and is_deleted=1 and job_no_mst='$data' order by po_number ASC"; 
	}
	else if($db_type==2)
	{
		$sql= "select is_confirmed, po_number, po_received_date, pub_shipment_date, shipment_date, po_quantity, unit_price, po_total_price, excess_cut, plan_cut, (pub_shipment_date-po_received_date) as  date_diff, (pub_shipment_date-factory_received_date) as  facdate_diff, status_active, id from wo_po_break_down where status_active=0 and is_deleted=1 and job_no_mst='$data' order by po_number ASC"; 
	}
	echo  create_list_view("list_view", "Order Status,PO No,PO Recv. Date,Ship Date,Orgn. Ship Date,PO Qnty,Avg. Rate,Amount, Excess Cut %,Plan Cut Qnty,Lead Time,Lead time on Fac Rcv Date,Status", "70,110,65,65,65,80,60,80,60,80,50,50","950","220",0, $sql , "get_details_form_data", "id", "'populate_order_details_form_data'", 1, "is_confirmed,0,0,0,0,0,0,0,0,0,0,0,status_active", $arr , "is_confirmed,po_number,po_received_date,pub_shipment_date,shipment_date,po_quantity,unit_price,po_total_price,excess_cut,plan_cut,date_diff,facdate_diff,status_active", "requires/woven_order_entry_controller",'','0,0,3,3,3,1,4,4,2,2,1,1') ;*/
	
	if($db_type==0)
	{
		$sql= "select is_confirmed, po_number, po_received_date, shipment_date, pub_shipment_date, po_quantity, unit_price, po_total_price, excess_cut, plan_cut, DATEDIFF(shipment_date,po_received_date) as date_diff, DATEDIFF(pub_shipment_date,factory_received_date) as facdate_diff, status_active, id from wo_po_break_down where status_active !=1 and job_no_mst='$data'"; 
	}
	else if($db_type==2)
	{
		$sql= "select is_confirmed, po_number, po_received_date, shipment_date, pub_shipment_date, po_quantity, unit_price, po_total_price, excess_cut, plan_cut, (shipment_date-po_received_date) as  date_diff,(pub_shipment_date-factory_received_date) as  facdate_diff, status_active, id from wo_po_break_down where status_active !=1 and job_no_mst='$data'"; 
	}
	$sqldata=sql_select($sql);
	if(count($sqldata)>0){
		echo  create_list_view("list_view", "Order Status,PO No,PO Recv. Date,Ship Date,Orgn. Ship Date,PO Qnty,Avg. Rate,Amount, Excess Cut %,Plan Cut Qnty,Lead Time,Lead time on Fac Rcv Date,Status", "70,110,65,65,65,80,60,80,60,80,50,50","950","220",0, $sql , "get_details_form_data", "id", "'populate_order_details_form_data'", 1, "is_confirmed,0,0,0,0,0,0,0,0,0,0,0,status_active", $arr , "is_confirmed,po_number,po_received_date,shipment_date,pub_shipment_date,po_quantity,unit_price,po_total_price,excess_cut,plan_cut,date_diff,facdate_diff,status_active", "requires/woven_order_entry_controller",'','0,0,3,3,3,1,4,4,2,2,1,1') ;
	}
	exit();
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
	<table width="900" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center" rules="all">
    	<tr>
        	<td align="center" width="100%">
            	<table  cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center" rules="all">
                    
                    <thead> 
                         <tr>
                        	<th  colspan="6">
                              <?
                               echo create_drop_down( "cbo_string_search_type", 140, $string_search_type,'', 1, "-- Searching Type --" );
                              ?>
                            </th>
                     </tr>
                       <tr>               	 
                        <th width="150">Company Name</th>
                        <th width="150">Buyer Name</th>
                        <th width="100">Quotation ID</th>
                        <th width="100">Style Reff.</th>
                        <th width="200">Date Range</th>
                        <th></th> 
                       </tr>          
                    </thead>
        			<tr class="general">
                    	<td> <input type="hidden" id="selected_id">
							<? 
								echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", '',"load_drop_down('woven_order_entry_controller_update', this.value, 'load_drop_down_buyer', 'buyer_td' );" );
							?>
                        </td>
                   	<td id="buyer_td">
                     <? 
						echo create_drop_down( "cbo_buyer_name", 172, $blank_array,'', 1, "-- Select Buyer --" );
					?>	
                    </td>
                     <td >  
                        <input type="text" style="width:70px" class="text_boxes"  name="txt_quotation_no" id="txt_quotation_no"  />	
                    </td>
                     <td  align="center">				
                        <input type="text" style="width:100px" class="text_boxes"  name="txt_style" id="txt_style"  />			
                    </td>
                    <td>
                      <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">
					  <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
					 </td> 
            		 <td align="center">
                     <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_quotation_no').value+'_'+document.getElementById('txt_style').value, 'create_quotation_id_list_view', 'search_div', 'woven_order_entry_controller_update', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
        		</tr>
             </table>
          </td>
        </tr>
        <tr>
            <td  align="center" height="40" valign="middle">
            <? 
			echo create_drop_down( "cbo_year_selection", 70, $year,"", 1, "-- Select --", date('Y'), "",0 );		
			?>
			<? echo load_month_buttons();  ?>
            </td>
            </tr>
        <tr>
            <td align="center"valign="top" id="search_div"> 
	
            </td>
        </tr>
    </table>    
     
    </form>
   </div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_quotation_id_list_view")
{
	$data=explode('_',$data);
	if ($data[0]!=0) $company=" and company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $buyer=" and buyer_id='$data[1]'"; $buyer="";// else { echo "Please Select Buyer First."; die; }
	
	if($db_type==0)
	{
	if ($data[2]!="" &&  $data[3]!="") $est_ship_date  = "and est_ship_date  between '".change_date_format($data[3], "yyyy-mm-dd", "-")."' and '".change_date_format($data[4], "yyyy-mm-dd", "-")."'"; else $est_ship_date ="";
	}
	
	if($db_type==2)
	{
	if ($data[2]!="" &&  $data[3]!="") $est_ship_date  = "and est_ship_date  between '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."'"; else $est_ship_date ="";
	}
	
	$style_cond="";
	$quotation_id_cond="";
	if($data[4]==1)
		{
		   if (trim($data[5])!="") $quotation_id_cond=" and id='$data[5]'";
		   if (trim($data[6])!="") $style_cond=" and style_ref='$data[6]'";
		}
	
	if($data[4]==4 || $data[4]==0)
		{
		  if (trim($data[5])!="") $quotation_id_cond=" and id like '%$data[5]%' ";
		  if (trim($data[6])!="") $style_cond=" and style_ref like '%$data[6]%' ";
		}
	
	if($data[4]==2)
		{
		  if (trim($data[5])!="") $quotation_id_cond=" and id like '$data[5]%' "; 
		  if (trim($data[6])!="") $style_cond=" and style_ref like '$data[6]%' ";
		}
	
	if($data[4]==3)
		{
		  if (trim($data[5])!="") $quotation_id_cond=" and id like '%$data[5]' ";
		  if (trim($data[6])!="") $style_cond=" and style_ref like '%$data[6]' "; 
		}
		
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$arr=array (1=>$comp,2=>$buyer_arr,5=>$pord_dept);
	$sql= "select id,company_id, buyer_id, style_ref,style_desc,pord_dept,offer_qnty,est_ship_date from  wo_price_quotation a where status_active=1  and is_deleted=0 $company $buyer $style_cond $quotation_id_cond order by id";
	echo  create_list_view("list_view", "Quotation ID,Company,Buyer Name,Style Ref,Style Desc.,Prod. Dept., Offer Qnty, Est Ship Date", "90,120,100,100,200,100,100","1000","320",0, $sql , "js_set_value", "id", "", 1, "0,company_id,buyer_id,0,0,pord_dept,0,0", $arr , "id,company_id,buyer_id,style_ref,style_desc,pord_dept,offer_qnty,est_ship_date", "",'','0,0,0,0,0,0,2,3') ;
}

if ($action=="populate_data_from_search_popup_quotation")
{
	$data_array=sql_select("select a.id, a.company_id, a.buyer_id, a.style_ref, a.revised_no, a.pord_dept,a.product_code, a.style_desc, a.currency, a.agent, a.offer_qnty, a.region, a.color_range, a.incoterm, a.incoterm_place, a.machine_line, a.prod_line_hr, a.fabric_source, a.costing_per, a.quot_date, a.est_ship_date, a.factory,a.season_buyer_wise, a.remarks, a.garments_nature,a.order_uom,a.gmts_item_id,a.set_break_down,a.total_set_qnty,b.price_with_commn_pcs from wo_price_quotation a, wo_price_quotation_costing_mst b where a.id=b.quotation_id and a.id='$data'");
	foreach ($data_array as $row)
	{
		echo "load_drop_down( 'requires/woven_order_entry_controller_update', '".$row[csf("company_id")]."', 'load_drop_down_buyer', 'buyer_td' ); load_drop_down( 'requires/quotation_entry_controller', '".$row[csf("company_id")]."', 'load_drop_down_agent', 'agent_td' ); load_drop_down( 'requires/woven_order_entry_controller_update', '".$row[csf("company_id")]."', 'load_drop_down_party_type', 'party_type_td' );sub_dept_load('".$row[csf("buyer_id")]."','".$row[csf("pord_dept")]."');\n";
		
		echo "document.getElementById('cbo_company_name').value = '".$row[csf("company_id")]."';\n";  
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
		echo "document.getElementById('txt_quotation_id').value = '".$row[csf("id")]."';\n";
		echo "document.getElementById('txt_avg_price').value = '".$row[csf("price_with_commn_pcs")]."';\n";
		echo "document.getElementById('cbo_season_name').value = '".$row[csf("season_buyer_wise")]."';\n";
		//echo "location_select();\n";
	}
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
		  $sql=sql_select("select cm_cost_method from variable_order_tracking where company_name=$cbo_company_name and variable_list=22 and status_active=1 and is_deleted=0");
		 $cm_cost_method=$sql[0][csf('cm_cost_method')]*1;
		 //echo $cm_cost_method.'='.$cbo_company_name; die;
		  
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
		 /*if($precostapproved==0)
		 {
			 if($other_cost_approved==1)
			 {
				 echo '<p style="color:#FF0000;">Pre Cost Others Cost Approved, Any Change not allowed.</P>';
				 $disab="disabled";
				 $disabled=1;
			 }
			 else if($precostfound >0 && $cm_cost_method>0){ 
				 echo "Pre Cost Found, only Sew. and Cut. SMV Change allowed";
				 $disab="";
				 $disabled=1;
			 }
			 else { $disabled=0; $disab=""; }
		 }
		 else if($precostapproved==1 && $cm_cost_method>0) 
		 {
			 echo '<p style="color:#FF0000;">Pre Cost Approved, Any Change not allowed.</P>';
			 $disab="disabled";
			 $disabled=1;
		 }
		 else $disab="";*/
		 
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
                                <td><? echo create_drop_down( "cboitem_".$i, 150, get_garments_item_array(2), "",1," -- Select Item --", $data[0], "check_duplicate(".$i.",this.id );check_smv_set_popup(".$i.");",$disabled,'' ); ?></td>
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
                            <td><? echo create_drop_down( "cboitem_1", 150, get_garments_item_array(2), "",1,"--Select--", 0, "check_duplicate(1,this.id );check_smv_set_popup(1);",'','' ); ?></td>
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

if($action=="open_set_list_view1")
{
echo load_html_head_contents("Set Entry","../../../", 1, 1, $unicode);
extract($_REQUEST);


 
?>
<script>

//else{$("#smv_1").removeAttr('disabled');}

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
		  $('#smv_'+i).removeAttr("onChange").attr("onChange","calculate_set_smv("+i+")");
		  $('#cutsmv_'+i).removeAttr("onChange").attr("onChange","calculate_set_cutsmv("+i+")");
		  $('#finsmv_'+i).removeAttr("onChange").attr("onChange","calculate_set_finsmv("+i+")");
		  $('#increaseset_'+i).removeAttr("onClick").attr("onClick","add_break_down_set_tr("+i+")");
		  $('#decreaseset_'+i).removeAttr("onClick").attr("onClick","fn_delete_down_tr("+i+",'tbl_set_details')");
		  $('#cboitem_'+i).val(''); 
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
		if($('#cutsmv_'+i).val()==''){
		$('#cutsmv_'+i).val(0)
		}
		if($('#cutsmvset_'+i).val()==''){
		$('#cutsmvset_'+i).val(0)
		}
		if($('#finsmv_'+i).val()==''){
		$('#finsmv_'+i).val(0)
		}
		if($('#finsmvset_'+i).val()==''){
		$('#finsmvset_'+i).val(0)
		}
		if(set_breck_down=="")
		{
			set_breck_down+=$('#cboitem_'+i).val()+'_'+$('#txtsetitemratio_'+i).val()+'_'+$('#smv_'+i).val()+'_'+$('#smvset_'+i).val()+'_'+$('#complexity_'+i).val()+'_'+$('#emblish_'+i).val()+'_'+$('#cutsmv_'+i).val()+'_'+$('#cutsmvset_'+i).val()+'_'+$('#finsmv_'+i).val()+'_'+$('#finsmvset_'+i).val();
			item_id+=$('#cboitem_'+i).val();
		}
		else
		{
			set_breck_down+="__"+$('#cboitem_'+i).val()+'_'+$('#txtsetitemratio_'+i).val()+'_'+$('#smv_'+i).val()+'_'+$('#smvset_'+i).val()+'_'+$('#complexity_'+i).val()+'_'+$('#emblish_'+i).val()+'_'+$('#cutsmv_'+i).val()+'_'+$('#cutsmvset_'+i).val()+'_'+$('#finsmv_'+i).val()+'_'+$('#finsmvset_'+i).val();
			item_id+=","+$('#cboitem_'+i).val();
		}
	}
	document.getElementById('set_breck_down').value=set_breck_down;
	document.getElementById('item_id').value=item_id;
	parent.emailwindow.hide();
}

function open_emblishment_pop_up(i){ 
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
		 $cm_cost_method=return_field_value("cm_cost_method", "variable_order_tracking", "company_name=$cbo_company_name and variable_list=22 and status_active=1 and is_deleted=0");
		 
		 $sql=sql_select("select cm_cost_method from variable_order_tracking where company_name=$cbo_company_name and variable_list=22 and status_active=1 and is_deleted=0");
		 $cm_cost_method=$sql[0][csf('cm_cost_method')];
		 echo $cm_cost_method.'='.$cbo_company_name; die;
		 $disabled=0;
		 if($precostfound >0 ){ 
			 //echo "Pre Cost Found, Any Change will be not allowed";
			 $disabled=1;
		 }
		 else{
			 $disabled=0;
		 }
		 ?>
        	<form id="setdetails_1" autocomplete="off">
            <input type="hidden" id="set_breck_down" />     
            <input type="hidden" id="item_id" />  
            <input type="hidden" id="unit_id" value="<? echo $unit_id;  ?>" />        	
            <table width="640" cellspacing="0" class="rpt_table" border="0" id="tbl_set_details" rules="all">
                	<thead>
                    	<tr>
                        	<th width="230">Item</th><th width="40">Set Ratio</th><th width="40">Sew SMV/ Pcs</th><th width="40">Cut SMV/ Pcs</th><th width="40">Fin SMV/ Pcs</th><th width="80">Complexity</th><th width="80">Embellishment</th><th width=""></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
					$smv_arr=array();
					$sql_d=sql_select("Select gmts_item_id,set_item_ratio,smv_pcs,smv_set,complexity,embelishment, cutsmv_pcs, cutsmv_set, finsmv_pcs, finsmv_set from wo_po_details_mas_set_details where job_no='$txt_job_no' order by id");
					foreach($sql_d as $sql_r){
						if($sql_r[csf('gmts_item_id')]==""){
							$sql_r[csf('gmts_item_id')]=0;
						}
						if($sql_r[csf('set_item_ratio')]==""){
							$sql_r[csf('set_item_ratio')]=0;
						}
						if($sql_r[csf('smv_pcs')]==""){
							$sql_r[csf('smv_pcs')]=0;
							$sql_r[csf('smv_set')]=0;
						}
						if($sql_r[csf('complexity')]==""){
							$sql_r[csf('complexity')]=0;
						}
						if($sql_r[csf('embelishment')]==""){
							$sql_r[csf('embelishment')]=0;
						}
						if($sql_r[csf('cutsmv_pcs')]==""){
							$sql_r[csf('cutsmv_pcs')]=0;
							$sql_r[csf('cutsmv_set')]=0;
						}
						if($sql_r[csf('finsmv_pcs')]==""){
							$sql_r[csf('finsmv_pcs')]=0;
							$sql_r[csf('finsmv_set')]=0;
						}
						$sql_r=removenumeric($sql_r);
						$smv_arr[]=implode("_",$sql_r);
					}
					$smv_srt=rtrim(implode("__",$smv_arr),"__");
					if(count($sql_d)){
						$set_breck_down=$smv_srt;
					}
					//echo $set_breck_down;
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
							?>
                            	<tr id="settr_1" align="center">
                                    <td>
									<? 
										echo create_drop_down( "cboitem_".$i, 230, $garments_item, "",1," -- Select Item --", $data[0], "check_duplicate(".$i.",this.id )",$disabled,'' ); 
									?>
                                    </td>
                                    <td>
                                    <input type="text" id="txtsetitemratio_<? echo $i;?>"   name="txtsetitemratio_<? echo $i;?>" style="width:30px"  class="text_boxes_numeric" onChange="calculate_set_smv(<? echo $i;?>)"  value="<? echo $data[1] ?>" <? if ($unit_id==1){echo "readonly";} else{echo "";}?> <? if ($disabled==1){echo "disabled";}else{ echo "";} ?>  /> 
                                    </td>
                                    <td>
                                    <input type="text" id="smv_<? echo $i;?>"   name="smv_<? echo $i;?>" style="width:30px"  class="text_boxes_numeric" onChange="calculate_set_smv(<? echo $i;?>)"  value="<? echo $data[2] ?>" <? if ($disabled==1){echo "disabled";}else{ echo "";} ?> /> 
                                    <input type="hidden" id="smvset_<? echo $i;?>"   name="smvset_<? echo $i;?>" style="width:30px"  class="text_boxes_numeric"  value="<? echo $data[3] ?>" readonly/> 
                                    </td>
                                    <td>
                                    <input type="text" id="cutsmv_<? echo $i;?>"   name="cutsmv_<? echo $i;?>" style="width:30px"  class="text_boxes_numeric" onChange="calculate_set_cutsmv(<? echo $i;?>)"  value="<? echo $data[6] ?>" <? if ($disabled==1){echo "disabled";}else{ echo "";} ?> /> 
                                    <input type="hidden" id="cutsmvset_<? echo $i;?>"   name="cutsmvset_<? echo $i;?>" style="width:30px"  class="text_boxes_numeric"  value="<? echo $data[7] ?>" readonly/> 
                                    </td>
                                    <td>
                                    <input type="text" id="finsmv_<? echo $i;?>"   name="finsmv_<? echo $i;?>" style="width:30px"  class="text_boxes_numeric" onChange="calculate_set_finsmv(<? echo $i;?>)"  value="<? echo $data[8] ?>" <? if ($disabled==1){echo "disabled";}else{ echo "";} ?> /> 
                                    <input type="hidden" id="finsmvset_<? echo $i;?>"   name="finsmvset_<? echo $i;?>" style="width:30px"  class="text_boxes_numeric"  value="<? echo $data[9] ?>" readonly/> 
                                    </td>
                                    <td>
                                     <? 
										echo create_drop_down( "complexity_".$i, 80, $complexity_level, "",1," -- Select --", $data[4], "",$disabled,'' ); 
									 ?>
                                    </td>
                                    <td>
                                     <? 
										echo create_drop_down( "emblish_".$i, 80, $yes_no, "",1," -- Select--", $data[5], "",$disabled,'' ); 
									 ?>
                                   
                                    </td>
                                    <td>
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
                                   <td>
									<? 
									echo create_drop_down( "cboitem_1", 230, $garments_item, "",1,"--Select--", 0, "check_duplicate(1,this.id )",'','' ); 
									?>
                                    </td>
                                    <td>
                                    <input type="text" id="txtsetitemratio_1" name="txtsetitemratio_1" style="width:30px" class="text_boxes_numeric" onChange="calculate_set_smv(1)"  value="<?  if ($unit_id==1){echo "1";} else{echo "";}?>" <? if ($unit_id==1){echo "readonly";} else{echo "";}?> /> 
                                    </td>
                                    <td>
                                    <input type="text" id="smv_1" name="smv_1" style="width:30px" class="text_boxes_numeric" onChange="calculate_set_smv(1)"  value="0"  /> 
                                    <input type="hidden" id="smvset_1" name="smvset_1" style="width:30px" class="text_boxes_numeric"   value="0"  /> 
                                    </td>
                                    <td>
                                    <input type="text" id="cutsmv_1" name="cutsmv_1" style="width:30px" class="text_boxes_numeric" onChange="calculate_set_cutsmv(1)"  value="0"  /> 
                                    <input type="hidden" id="cutsmvset_1" name="cutsmvset_1" style="width:30px" class="text_boxes_numeric"   value="0"  /> 
                                    </td>
                                     <td>
                                    <input type="text" id="finsmv_1" name="finsmv_1" style="width:30px" class="text_boxes_numeric" onChange="calculate_set_finsmv(1)"  value="0"  /> 
                                    <input type="hidden" id="finsmvset_1" name="finsmvset_1" style="width:30px" class="text_boxes_numeric"   value="0"  /> 
                                    </td>
                                    <td>
                                   
                                    <? 
										echo create_drop_down( "complexity_1", 80, $complexity_level, "",1," -- Select --", 0, "",'','' ); 
									?>
                                    </td>
                                    <td>
                                     <? 
										echo create_drop_down( "emblish_1", 80, $yes_no, "",1," -- Select --", 0, "",'','' ); 
									?>
                                    </td>
                                    <td>
                                    <input type="button" id="increaseset_1" style="width:30px" class="formbutton" value="+" onClick="add_break_down_set_tr(1)" />
                                    <input type="button" id="decreaseset_1" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_delete_down_tr(1 ,'tbl_set_details' );" />
                                    </td> 
                                </tr>
                    <? 
					} 
					?>
                </tbody>
                </table>
                <table width="640" cellspacing="0" class="rpt_table" border="0" rules="all">
                <tfoot>
                    	<tr>
                            <th width="230">Total</th>
                            <th  width="40">
                                <input type="text" id="tot_set_qnty" name="tot_set_qnty" class="text_boxes_numeric" style="width:30px"  value="<? if($tot_set_qnty !=''){ echo $tot_set_qnty;} else{ echo 1;} ?>" readonly />
                            </th>
                             <th  width="40">
                                <input type="text" id="tot_smv_qnty" name="tot_smv_qnty" class="text_boxes_numeric" style="width:30px"  value="<? if($tot_smv_qnty !=''){ echo $tot_smv_qnty;} else{ echo 0;} ?>" readonly />
                            </th>
                             <th  width="40">
                                <input type="text" id="tot_cutsmv_qnty" name="tot_cutsmv_qnty" class="text_boxes_numeric" style="width:30px"  value="<? if($tot_cutsmv_qnty !=''){ echo $tot_cutsmv_qnty;} else{ echo 0;} ?>" readonly />
                            </th>
                            <th  width="40">
                                <input type="text" id="tot_finsmv_qnty" name="tot_finsmv_qnty" class="text_boxes_numeric" style="width:30px"  value="<? if($tot_finsmv_qnty !=''){ echo $tot_finsmv_qnty;} else{ echo 0;} ?>" readonly />
                            </th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
                <table width="560" cellspacing="0" class="" border="0">
                	<tr>
                        <td align="center" width="100%" class="button_container">
						        <input type="button" class="formbutton" value="Close" onClick="js_set_value_set()"/> 
                        </td> 
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

var fileParmission1='<? echo $_SESSION['logic_erp']['data_arr'][122][$cbo_company_name][txt_Sewing_SMV][is_disable];?>';
var fileParmission2='<? echo $_SESSION['logic_erp']['data_arr'][122][$cbo_company_name][txt_Cutting_SMV][is_disable];?>';
var fileParmission3='<? echo $_SESSION['logic_erp']['data_arr'][122][$cbo_company_name][txt_Finish_SMV][is_disable];?>';
var rowCount = $('#tbl_set_details tr').length-1;


for(var i=1;i<=rowCount;i++){
	if(fileParmission1==1){document.getElementById('smv_'+i).disabled =true;}
	else{document.getElementById('smv_'+i).disabled =false;}
	
	if(fileParmission2==1){document.getElementById('cutsmv_'+i).disabled =true;}
	else{document.getElementById('cutsmv_'+i).disabled =false;}
	
	if(fileParmission3==1){document.getElementById('finsmv_'+i).disabled =true;}
	else{document.getElementById('finsmv_'+i).disabled =false;}
	
	
	
}

</script>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>

<?

}

if($action=="booking_no_with_approved_status")
{
	
	$data=explode("_",$data);
	//echo $data[0];
	//echo $data[1];
	if($data[1]=="")
	{
		$sql="select booking_no,is_approved from wo_booking_mst where job_no='$data[0]' and booking_type=1 and is_short=2 and is_deleted=0 and status_active=1";
	}
	else
	{
		$sql="select a.booking_no,a.is_approved from wo_booking_mst a, wo_booking_dtls b where a.job_no=b.job_no and  a.job_no='$data[0]' and a.booking_type=1 and a.is_short=2 and b.po_break_down_id=$data[1] and a.is_deleted=0 and a.status_active=1 group by a.booking_no,is_approved";
	}
	$approved_booking="";
	$un_approved_booking="";
	$sql_booking=sql_select($sql);
	foreach($sql_booking as $row)
	{
		if($row[csf('is_approved')]==1)
		{
		  $approved_booking.=$row[csf('booking_no')].", ";	
		}
		else
		{
		  $un_approved_booking.=$row[csf('booking_no')].", ";	
		}
	}
	echo rtrim($approved_booking ,", ")."_".rtrim($un_approved_booking , ", ");
}

 
// Dtls Form ************************************************Dtls Form************************************************
if ($action=="get_excess_cut_percent")
{
	$data=explode("_",$data);
	 $qry_result=sql_select( "select slab_rang_start,slab_rang_end,excess_percent from  var_prod_excess_cutting_slab where company_name='$data[1]' and variable_list=2 and status_active=1 and is_deleted=0");
	 foreach ($qry_result as $row)
	 {
		 if ( $data[0]>=$row[csf("slab_rang_start")] && $data[0]<=$row[csf("slab_rang_end")] )
		 {
			 echo $row[csf("excess_percent")]; die;
		 }
	 }
	 echo "0"; die;
}

if ($action=="populate_order_details_form_data")
{
	$data_array=sql_select("select id, job_no_mst, is_confirmed, po_number, po_received_date, pub_shipment_date, shipment_date, factory_received_date, po_quantity, unit_price, po_total_price, excess_cut, plan_cut, country_name, details_remarks, delay_for, status_active, packing, grouping, projected_po_id, extended_ship_date, tna_task_from_upto, file_no, sc_lc from wo_po_break_down where id='$data'");
	
	foreach ($data_array as $row)
	{ 
		echo "document.getElementById('cbo_order_status').value = '".$row[csf("is_confirmed")]."';\n";  
		echo "document.getElementById('txt_po_no').value = '".$row[csf("po_number")]."';\n";  
		echo "document.getElementById('txt_po_received_date').value = '".change_date_format($row[csf("po_received_date")], "dd-mm-yyyy", "-")."';\n";  
		echo "document.getElementById('txt_pub_shipment_date').value = '".change_date_format($row[csf("pub_shipment_date")], "dd-mm-yyyy", "-")."';\n";  
		echo "document.getElementById('txt_org_shipment_date').value = '".change_date_format($row[csf("shipment_date")], "dd-mm-yyyy", "-")."';\n";  
		echo "document.getElementById('txt_factory_rec_date').value = '".change_date_format($row[csf("factory_received_date")], "dd-mm-yyyy", "-")."';\n";  
		echo "document.getElementById('txt_po_quantity').value = '".$row[csf("po_quantity")]."';\n";  
		echo "document.getElementById('txt_avg_price').value = '".$row[csf("unit_price")]."';\n";  
		echo "document.getElementById('txt_amount').value = '".$row[csf("po_total_price")]."';\n";  
		echo "document.getElementById('txt_excess_cut').value = '".$row[csf("excess_cut")]."';\n";  
		echo "document.getElementById('txt_plan_cut').value = '".$row[csf("plan_cut")]."';\n";
		echo "document.getElementById('chk_extended_ship_date').value = '".change_date_format($row[csf("extended_ship_date")], "dd-mm-yyyy", "-")."';\n";  
		//echo "document.getElementById('cbo_po_country').value = '".$row[csf("country_name")]."';\n";  
		echo "document.getElementById('txt_details_remark').value = '".$row[csf("details_remarks")]."';\n";  
		echo "document.getElementById('cbo_status').value = '".$row[csf("status_active")]."';\n";  
		echo "document.getElementById('update_id_details').value = '".$row[csf("id")]."';\n"; 
		echo "set_multiselect('cbo_delay_for','0','1','".($row[csf("delay_for")])."','0');\n"; 
		//echo "load_drop_down( 'requires/woven_order_entry_controller', '".$row[csf('po_received_date')]."'_'".$row[csf('pub_shipment_date')]."'_'.cbo_buyer_name, 'load_drop_down_tna_task', 'tna_task_td' );\n";
		//echo "set_tna_task();\n"; 
		if($row[csf("is_confirmed")]==1)
		{
			echo "$('#cbo_order_status').attr('disabled','true')".";\n";
		}
		else echo "$('#cbo_order_status').removeAttr('disabled')".";\n";
		echo "$('#txt_po_received_date').attr('disabled','true')".";\n";
		//echo "$('#txt_org_shipment_date').attr('disabled','true')".";\n";
		echo "$('#cbo_delay_for').attr('disabled','true')".";\n";
		
		echo "document.getElementById('cbo_packing_po_level').value = '".$row[csf("packing")]."';\n";  
		echo "document.getElementById('txt_grouping').value = '".$row[csf("grouping")]."';\n"; 
		echo "document.getElementById('cbo_projected_po').value = '".$row[csf("projected_po_id")]."';\n";  
		echo "document.getElementById('cbo_tna_task').value = '".$row[csf("tna_task_from_upto")]."';\n"; 
		echo "document.getElementById('txt_file_no').value = '".$row[csf("file_no")]."';\n"; 
		echo "document.getElementById('txt_sc_lc').value = '".$row[csf("sc_lc")]."';\n"; 
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_order_entry_details',2);\n";  
	}
	 $qry_result=sql_select( "select id from wo_po_color_size_breakdown where po_break_down_id='$data' and  status_active=1 and is_deleted=0");
	 $row=count($qry_result);
	 if($row>0)
	 {
		echo "$('#txt_avg_price').attr('disabled','true')".";\n";
		echo "$('#txt_avg_price').attr('title','Change It From Color Size Break Down')".";\n";

	 }
	 else
	 {
		echo "$('#txt_avg_price').removeAttr('disabled')".";\n";
		echo "$('#txt_avg_price').removeAttr('title')".";\n";
	 }
	// if($isapproved==1 || $isapproved==3) { echo "$('#txt_avg_price').attr('disabled','true')".";\n"; } else { echo "$('#txt_avg_price').removeAttr('disabled')".";\n"; }// Issue Id ISD-20-32706
	 echo "set_field_level_access($('#cbo_company_name').val());\n";
}

if ($action=="save_update_delete_mst")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if(trim(str_replace("'","",$txt_job_no))!="")
	{
		$sql_data=sql_select("select approved from wo_pre_cost_mst where job_no=$txt_job_no and is_deleted=0 and status_active=1");
		$isapproved=$sql_data[0][csf("approved")];
		
		if($isapproved==1 || $isapproved==3)
		{
			echo "50**";
			disconnect($con);
			die;
		}
	}
	
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
		if($db_type==0)
		{
		$new_job_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', '', date("Y",time()), 5, "select job_no_prefix,job_no_prefix_num from wo_po_details_master where company_name=$cbo_company_name and YEAR(insert_date)=".date('Y',time())." order by job_no_prefix_num desc ", "job_no_prefix", "job_no_prefix_num" ));
		}
		if($db_type==2)
		{
		$new_job_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', '', date("Y",time()), 5, "select job_no_prefix,job_no_prefix_num from wo_po_details_master where company_name=$cbo_company_name and to_char(insert_date,'YYYY')=".date('Y',time())." order by job_no_prefix_num desc ", "job_no_prefix", "job_no_prefix_num" ));
		}
		
		$field_array="id,garments_nature,quotation_id,job_no,job_no_prefix,job_no_prefix_num,company_name,buyer_name,location_name,style_ref_no,style_description,product_dept,product_code,pro_sub_dep,currency_id,agent_name,client_id,order_repeat_no,region,product_category,team_leader,dealing_marchant,bh_merchant,factory_marchant,packing,remarks,ship_mode,order_uom,gmts_item_id,set_break_down, total_set_qnty,set_smv,season_buyer_wise,is_deleted,status_active,inserted_by,insert_date";
		
		$data_array="(".$id.",".$garments_nature.",".$txt_quotation_id.",'".$new_job_no[0]."','".$new_job_no[1]."','".$new_job_no[2]."',".$cbo_company_name.",".$cbo_buyer_name.",".$cbo_location_name.",".$txt_style_ref.",".$txt_style_description.",".$cbo_product_department.",".$txt_product_code.",".$cbo_sub_dept.",".$cbo_currercy.",".$cbo_agent.",".$cbo_client.",".$txt_repeat_no.",".$cbo_region.",".$txt_item_catgory.",".$cbo_team_leader.",".$cbo_dealing_merchant.",".$txt_bhmerchant.",".$cbo_factory_merchant.",".$cbo_packing.",".$txt_remarks.",".$cbo_ship_mode.",".$cbo_order_uom.",".$item_id.",".$set_breck_down.",".$tot_set_qnty.",".$tot_smv_qnty.",".$cbo_season_name.",0,1,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		$field_array1="id, job_no, job_id, gmts_item_id, set_item_ratio, smv_pcs, smv_set, smv_pcs_precost, smv_set_precost, complexity, embelishment, cutsmv_pcs, cutsmv_set, finsmv_pcs, finsmv_set, printseq, embro, embroseq, wash, washseq, spworks, spworksseq, gmtsdying, gmtsdyingseq, quot_id, aop, aopseq";
		$add_comma=0;
		$total_smv_set=0;
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
			$data_array1 .="(".$id1.",'".$new_job_no[0]."',".$id.",'".$set_breck_down_arr[0]."','".$set_breck_down_arr[1]."','".$set_breck_down_arr[2]."','".$set_breck_down_arr[3]."','".$set_breck_down_arr[2]."','".$set_breck_down_arr[3]."','".$set_breck_down_arr[4]."','".$set_breck_down_arr[5]."','".$set_breck_down_arr[6]."','".$set_breck_down_arr[7]."','".$set_breck_down_arr[8]."','".$set_breck_down_arr[9]."','".$set_breck_down_arr[10]."','".$set_breck_down_arr[11]."','".$set_breck_down_arr[12]."','".$set_breck_down_arr[13]."','".$set_breck_down_arr[14]."','".$set_breck_down_arr[15]."','".$set_breck_down_arr[16]."','".$set_breck_down_arr[17]."','".$set_breck_down_arr[18]."','".$set_breck_down_arr[19]."','".$set_breck_down_arr[20]."','".$set_breck_down_arr[21]."')";
			$total_smv_set+=$set_breck_down_arr[3];
			$add_comma++;
			$id1=$id1+1;
		}
		$tot_smv_qnty=str_replace("'",'',$tot_smv_qnty);
		if($tot_smv_qnty != number_format($total_smv_set,2,'.',''))
		{
			echo "SMV**";
			 disconnect($con);die;
		}
		$rID=sql_insert("wo_po_details_master",$field_array,$data_array,0);
		$rID1=sql_insert("wo_po_details_mas_set_details",$field_array1,$data_array1,1);
		if($db_type==0)
		{
			if($rID && $rID1)
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
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID1)
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
// Insert Here End------------------------------------------------------
// Update Here----------------------------------------------------------
	else if ($operation==1) 
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$PrevData=sql_select("select style_ref_no,gmts_item_id from wo_po_details_master where job_no=$txt_job_no");
		$PrevStyleRefNo=$PrevData[0][csf('style_ref_no')];
		$PrevGmtsItemId=$PrevData[0][csf('gmts_item_id')];
		$field_array="quotation_id*buyer_name*location_name*style_ref_no*style_description*product_dept*product_code*pro_sub_dep*currency_id*agent_name*client_id*order_repeat_no*region*product_category*team_leader*dealing_marchant*bh_merchant*factory_marchant*packing*remarks*ship_mode*order_uom*gmts_item_id*set_break_down*total_set_qnty*set_smv*season_buyer_wise*style_ref_no_prev*gmts_item_id_prev*is_deleted*status_active*updated_by*update_date";
		$data_array="".$txt_quotation_id."*".$cbo_buyer_name."*".$cbo_location_name."*".$txt_style_ref."*".$txt_style_description."*".$cbo_product_department."*".$txt_product_code."*".$cbo_sub_dept."*".$cbo_currercy."*".$cbo_agent."*".$cbo_client."*".$txt_repeat_no."*".$cbo_region."*".$txt_item_catgory."*".$cbo_team_leader."*".$cbo_dealing_merchant."*".$txt_bhmerchant."*".$cbo_factory_merchant."*".$cbo_packing."*".$txt_remarks."*".$cbo_ship_mode."*".$cbo_order_uom."*".$item_id."*".$set_breck_down."*".$tot_set_qnty."*".$tot_smv_qnty."*".$cbo_season_name."*'".$PrevStyleRefNo."'*'".$PrevGmtsItemId."'*0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		$field_array1="id, job_no, job_id, gmts_item_id, set_item_ratio, smv_pcs, smv_set, smv_pcs_precost, smv_set_precost, complexity, embelishment, cutsmv_pcs, cutsmv_set, finsmv_pcs, finsmv_set, printseq, embro, embroseq, wash, washseq, spworks, spworksseq, gmtsdying, gmtsdyingseq, quot_id, aop, aopseq";
		$add_comma=0;
		$total_smv_set=0;
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
			$total_smv_set+=$set_breck_down_arr[3];
			$add_comma++;
			$id1=$id1+1;
			$sewSmv+=$set_breck_down_arr[3];
			$cutSmv+=$set_breck_down_arr[7];
		}
		$tot_smv_qnty=str_replace("'",'',$tot_smv_qnty);
		if($tot_smv_qnty != number_format($total_smv_set,2,'.',''))
		{
			echo "SMV**";
			 disconnect($con);die;
		}
		//print_r($data_array);
		$rID=sql_update("wo_po_details_master",$field_array,$data_array,"job_no","".$txt_job_no."",0);
		//echo "10**".$rID; die;
		$rID1=execute_query("delete from wo_po_details_mas_set_details where  job_no =".$txt_job_no."",0);
		$rID2=sql_insert("wo_po_details_mas_set_details",$field_array1,$data_array1,0);
		//$rID3=execute_query( "update  wo_booking_mst set is_apply_last_update=2  where  job_no =".$txt_job_no." and booking_type=1 and is_short=2 ",1);
		
		$txt_job_no=str_replace("'","",$txt_job_no);
		$data_int=$cbo_currercy.'****'.$set_breck_down;
		$set_smv_id=str_replace("'","",$set_smv_id);
		if($set_smv_id==1 || $set_smv_id==7) fnc_smv_style_integration($db_type,$cbo_company_name,$txt_job_no,$cbo_currercy,$sewSmv,$cutSmv,1);
		
		$txt_job_no=str_replace("'","",$txt_job_no);
		if($db_type==0)
		{
			if($rID && $rID1 && $rID2 )
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
		if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID1 && $rID2  )
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
// Update Here End ----------------------------------------------------------
// Delete Here----------------------------------------------------------
	else if ($operation==2)   
	{
		$con = connect();
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
		if($db_type==2 || $db_type==1 )
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
// Delete Here End ----------------------------------------------------------
}

if ($action=="save_update_delete_dtls")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if(trim(str_replace("'","",$update_id))!="")
	{
		$sql_data=sql_select("select approved from wo_pre_cost_mst where job_no=$update_id and is_deleted=0 and status_active=1");
		$isapproved=$sql_data[0][csf("approved")];
		
		if($isapproved==1 || $isapproved==3)
		{
			echo "50**";
			disconnect($con);
			die;
		}
	}
	
	$packing ="";
	if(str_replace("'","",$cbo_packing_po_level)==0)
	{
		$packing = $cbo_packing;
	}
	else
	{
		$packing = $cbo_packing_po_level;
	}
	if (file_exists('dateretriction.php'))
	{
		require('dateretriction.php');
	}
	if ($operation==0)  // Insert Here
	{	
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		/*if (is_duplicate_field( "po_number", "wo_po_break_down", "po_number=$txt_po_no and job_no_mst=$update_id and pub_shipment_date=$txt_pub_shipment_date and po_quantity= $txt_po_quantity   and is_deleted=0" ) == 1)
		{
			echo "11**0"; 
			die;
		}*/
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
		if (is_duplicate_field( "po_number", "wo_po_break_down", "po_number=$txt_po_no and job_no_mst=$update_id  $txt_pub_shipment_date_cond and is_deleted=0" ) == 1)
		{
			echo "11**0"; 
			 disconnect($con);die;
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
		$year=date("Y",strtotime(str_replace("'","",$txt_org_shipment_date)));
	    $month= (int) date("m",strtotime(str_replace("'","",$txt_org_shipment_date)));
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
		
		$id=return_next_id( "id", "wo_po_break_down", 1 ) ;
		$field_array="id,job_no_mst,job_id,is_confirmed,po_number,po_received_date,pub_shipment_date,shipment_date,factory_received_date,po_quantity,unit_price,original_avg_price,po_total_price,excess_cut,plan_cut,details_remarks,delay_for,packing,grouping,projected_po_id,tna_task_from_upto,t_year,t_month,original_po_qty,file_no,sc_lc,is_deleted,status_active,inserted_by,insert_date";
		$data_array="(".$id.",".$update_id.",".$hidd_job_id.",".$cbo_order_status.",".$txt_po_no.",".$txt_po_received_date.",".$txt_pub_shipment_date.",".$org_shipment_date.",".$txt_factory_rec_date.",".$txt_po_quantity.",".$txt_avg_price.",".$txt_avg_price.",".$txt_amount.",".$txt_excess_cut.",".$txt_plan_cut.",".$txt_details_remark.",".$cbo_delay_for.",".$packing.",".$txt_grouping.",".$cbo_projected_po.",".$cbo_tna_task.",'".date("Y",strtotime(str_replace("'","",$txt_org_shipment_date)))."','".date("m",strtotime(str_replace("'","",$txt_org_shipment_date)))."',".$txt_po_quantity.",".$txt_file_no.",".$txt_sc_lc.",0,".$cbo_status.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		$rID=sql_insert("wo_po_break_down",$field_array,$data_array,0);		
//====================================================================================
		
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
		
		if($db_type==2 || $db_type==1 )
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
		/*if (is_duplicate_field( "po_number", "wo_po_break_down", "po_number=$txt_po_no and job_no_mst=$update_id and pub_shipment_date=$txt_pub_shipment_date and po_quantity= $txt_po_quantity   and id!=$update_id_details and is_deleted=0" ) == 1)
		{
			echo "11**0"; 
			die;
		}*/

		//12133 start 
		$sql_attach="select sum(attached_value) as order_attach_value from com_sales_contract_order_info where status_active=1 and is_deleted=0 and wo_po_break_down_id=$update_id_details
		union all 
		select sum(attached_value) as order_attach_value from com_export_lc_order_info where status_active=1 and is_deleted=0 and wo_po_break_down_id=$update_id_details";
		$sql_attach_result=sql_select($sql_attach);
		$tot_attach_value=0;
		foreach($sql_attach_result as $row)
		{
			if($row[csf("order_attach_value")]>0)
			{
				$tot_attach_value+=$row[csf("order_attach_value")];
			}
		}
		$cu_order_val=str_replace("'","",$txt_amount);
		if($cu_order_val<$tot_attach_value)
		{
			echo "16**Order Value Not Allowed Less Then Attach Value.";disconnect($con);die;
		}
		//12133 end 

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
		//echo "select duplicate_ship_date from variable_order_tracking where company_name=$cbo_company_name and variable_list=29";
		if (is_duplicate_field( "po_number", "wo_po_break_down", "po_number=$txt_po_no and job_no_mst=$update_id $txt_pub_shipment_date_cond and id!=$update_id_details and is_deleted=0" ) == 1)
		{
			echo "11**0"; 
			 disconnect($con);die;
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
		$year=date("Y",strtotime(str_replace("'","",$txt_org_shipment_date)));
	    $month= (int) date("m",strtotime(str_replace("'","",$txt_org_shipment_date)));
		
		$min_leadtime_allocation=0;
		$sql_leadtime_vari=sql_select("select min_allocation from lib_min_lead_time_mst a, lib_min_lead_time_dtls b where a.id=b.mst_id and a.company_id=$cbo_company_name and year_id='$year' and a.month_id='$month'  and b.buyer_id=$cbo_buyer_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id");//and a.location_id=$cbo_location_name
	//	echo "select min_allocation from lib_min_lead_time_mst a, lib_min_lead_time_dtls b where a.id=b.mst_id and a.company_id=$cbo_company_name and year_id='$year' and a.month_id='$month'  and b.buyer_id=$cbo_buyer_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id";
		
		foreach($sql_leadtime_vari as $row_leadtime_vari){
			$min_leadtime_allocation=$row_leadtime_vari[csf('min_allocation')];	
		}
		if($dDiff < $min_leadtime_allocation && $min_lead_time_control==1){
			echo "LeadTime**0**".$min_leadtime_allocation;
			 disconnect($con);die;
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
		$prev_data=sql_select("SELECT is_confirmed,po_number,po_received_date,pub_shipment_date,shipment_date,factory_received_date,po_quantity,unit_price,po_total_price,excess_cut,plan_cut,details_remarks,delay_for,packing,grouping,projected_po_id,tna_task_from_upto,t_year,t_month,file_no,is_deleted,status_active,updated_by,update_date FROM wo_po_break_down WHERE id=$update_id_details");
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
		}
                $field_array="is_confirmed*po_number*po_received_date*pub_shipment_date*shipment_date*factory_received_date*po_quantity*unit_price*po_total_price*excess_cut*plan_cut*details_remarks*delay_for*packing*grouping*projected_po_id*tna_task_from_upto*t_year*t_month*file_no*sc_lc*po_number_prev*pub_shipment_date_prev*is_deleted*status_active*updated_by*update_date";
				
		$data_array ="".$cbo_order_status."*".$txt_po_no."*".$txt_po_received_date."*".$txt_pub_shipment_date."*".$org_shipment_date."*".$txt_factory_rec_date."*".$txt_po_quantity."*".$txt_avg_price."*".$txt_amount."*".$txt_excess_cut."*".$txt_plan_cut."*".$txt_details_remark."*".$cbo_delay_for."*".$packing."*".$txt_grouping."*".$cbo_projected_po."*".$cbo_tna_task."*".date("Y",strtotime(str_replace("'","",$txt_org_shipment_date)))."*".date("m",strtotime(str_replace("'","",$txt_org_shipment_date)))."*".$txt_file_no."*".$txt_sc_lc."*'".$prev_po_no."'*'".$prev_pub_shipment_date."'*0*".$cbo_status."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		
		//History Code....shajjad
		
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
		
		if($log_update=="" || $log_update!=$curr_date)
		{
			$field_array_history="id,entry_form,job_no,po_no,po_id,order_status,po_received_date,previous_po_qty,shipment_date,org_ship_date,po_status,file_no,t_year,t_month,update_date,update_by";
			
			$data_array_history="(".$log_id_mst.",2,".$update_id.",".$txt_po_no.",".$update_id_details.",".$cbo_order_status.",".$txt_po_received_date.",".$previous_po_qty.",".$txt_pub_shipment_date.",".$txt_org_shipment_date.",".$cbo_status.",".$txt_file_no.",'".date("Y",strtotime(str_replace("'","",$txt_org_shipment_date)))."','".date("m",strtotime(str_replace("'","",$txt_org_shipment_date)))."','".$current_date."',".$_SESSION['logic_erp']['user_id'].")";
			
			$rID3=sql_insert("wo_po_update_log",$field_array_history,$data_array_history,1);	
			
		}
		else if( $log_update==$curr_date)
		{
			
			$field_array_history="job_no*po_no*po_id*order_status*po_received_date*previous_po_qty*shipment_date*org_ship_date*po_status*file_no*update_date*update_by";
			
			$data_array_history="".$update_id."*".$txt_po_no."*".$update_id_details."*".$cbo_order_status."*".$txt_po_received_date."*".$txt_po_quantity."*".$txt_pub_shipment_date."*".$txt_org_shipment_date."*".$cbo_status."*".$txt_file_no."*'".$current_date."'*".$_SESSION['logic_erp']['user_id']."";
			
			$rID3=sql_update("wo_po_update_log",$field_array_history,$data_array_history,"po_id*update_date","".$update_id_details."*'".$log_update_date."'",1);
		}
		
		//History Code....shajjad
		
		
		$rID=sql_update("wo_po_break_down",$field_array,$data_array,"id","".$update_id_details."",1);
		if($rID) 
		{
			$rID4=execute_query( "update  com_export_lc_order_info set attached_rate=".$txt_avg_price.",attached_value=".$txt_amount.",updated_by=".$_SESSION['logic_erp']['user_id'].",update_date='".$pc_date_time."'  where  wo_po_break_down_id =$update_id_details",1);
			$rID5=execute_query( "update  com_sales_contract_order_info set attached_rate=".$txt_avg_price.",attached_value=".$txt_amount.",updated_by=".$_SESSION['logic_erp']['user_id'].",update_date='".$pc_date_time."'  where  wo_po_break_down_id =$update_id_details",1);
		}
		
		//$rID2=execute_query( "update  wo_booking_mst set is_apply_last_update=2  where  job_no =".$update_id." and booking_type=1 and is_short=2 ",1);
		
		
		
		
//======================================================
		
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
				echo "0**".$rID."**".$return_data[1]."**".$return_data[2]."**".$return_data[3]."**".$return_data[4]."**".$return_data[5];
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$rID."**".$return_data[1]."**".$return_data[2]."**".$return_data[3]."**".$return_data[4]."**".$return_data[5];
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con); 
				echo "0**".$rID."**".$return_data[1]."**".$return_data[2]."**".$return_data[3]."**".$return_data[4]."**".$return_data[5];
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
		
		$field_array="status_active*is_deleted*updated_by*update_date";
		$data_array="'0'*'1'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		$rID=sql_delete("wo_po_break_down",$field_array,$data_array,"id","".$update_id_details."",1);
		$return_data=update_job_mast($update_id);//define in common_functions.php
		update_cost_sheet($update_id);
		$txt_job_no=str_replace("'","",$txt_job_no);
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "0**".$rID."**".$return_data[1]."**".$return_data[2]."**".$return_data[3]."**".$return_data[4]."**".$return_data[5];
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$rID."**".$return_data[1]."**".$return_data[2]."**".$return_data[3]."**".$return_data[4]."**".$return_data[5];
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con); 
				echo "0**".$rID."**".$return_data[1]."**".$return_data[2]."**".$return_data[3]."**".$return_data[4]."**".$return_data[5];
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
function sql_update2($strTable,$arrUpdateFields,$arrUpdateValues,$arrRefFields,$arrRefValues,$commit,$return_query='')
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
	echo $strQuery;die;
	if($return_query==1){return $strQuery ;}
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

// function================

function get_tna_template( $remain_days, $tna_template, $buyer )
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

if ($action=="actual_po_info_popup")
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
				var booking=return_global_ajax_value(rowid, 'delete_row', '', 'woven_order_entry_controller_update');
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
		http.open("POST","woven_order_entry_controller_update.php",true);
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
                    <input type="text" id="poNo_<? echo $i;?>" name="poNo_<? echo $i;?>" class="text_boxes" style="width:130px" value="<? echo $row[csf('acc_po_no')] ; ?>" readonly />
                    </td>
                    <td align="center"><input type="text" id="poQnty_<? echo $i;?>" name="poQnty_<? echo $i;?>" class="text_boxes_numeric" style="width:120px" value="<? echo $row[csf('acc_po_qty')] ; ?>" readonly/></td>
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
                    <input type="text" id="poNo_1" name="poNo_1" class="text_boxes" style="width:130px" value="" readonly />
                    </td>
                    <td align="center"><input type="text" id="poQnty_1" name="poQnty_1" class="text_boxes_numeric" style="width:120px" value="" readonly/></td>
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

// --------------------------------------------pp meeting popup-------------------------------------------------------------------------------

if ($action=="all_po_ppMeeting")
{
	echo load_html_head_contents("Actual PO Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?> 
	<script>
		var permission='<? echo $permission; ?>';
		var type='<? echo $type; ?>';
		function cope_pp_date(id)
		{
			if (document.getElementById('cbx_cope_date').checked==true)
			{	
				var row_num = $('#tbl_list_pp_date tbody tr').length; 
				var initial=id+1;
			
				for( j=initial; j<=row_num; j++)
					$("#pp_meeting_date_"+j).val($("#pp_meeting_date_"+id).val())
			}
		}
		
		function fnc_pp_metting( operation )
		{
			var row_num = $('#tbl_list_pp_date tbody tr').length;  
			
			if(type==1)
			{
				var data1='';
				for( var i=1; i<=row_num; i++)
				{
					data1+=get_submitted_data_string('pp_meeting_date_'+i+'*pp_order_id_'+i, "../../../",i);
				}
			}
			else if(type==2)
			{
				var data1='';
				for( var i=1; i<=row_num; i++)
				{
					if($("#pp_meeting_date_"+i).val()!="")
					{
						if (form_validation('cbo_exship_mode_'+i,'Extended Ship Mode')==false)
						{
							return;   
						}
						
						if($("#cbo_exship_mode_"+i).val()==2)
						{
							if (form_validation('txt_sea_discount_'+i,'Sea Discount %')==false)
							{
								return;   
							}
						}
						else if($("#cbo_exship_mode_"+i).val()==4)
						{
							if (form_validation('txt_air_discount_'+i,'Air Discount %')==false)
							{
								return;   
							}
						}
						else if($("#cbo_exship_mode_"+i).val()==5)
						{
							if( ($("#txt_sea_discount_"+i).val()*1)==0 && ($("#txt_air_discount_"+i).val()*1)==0 )
							{
								alert("PLease Input Sea Discount % or Air Discount %");
								$('#txt_sea_discount_'+i).focus();
								$('#txt_air_discount_'+i).focus();
								return;   
							}
						}
					}
					data1+=get_submitted_data_string('pp_meeting_date_'+i+'*pp_order_id_'+i+'*cbo_exship_mode_'+i+'*txt_sea_discount_'+i+'*txt_air_discount_'+i, "../../../",i);
				}
			}
			
			var data="action=save_update_delete_pp_meeting&operation="+operation+'&total_row='+row_num+'&type='+type+data1;
			
			freeze_window(operation);
			http.open("POST","woven_order_entry_controller_update.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_pp_metting_reponse;
		}
		
		function fnc_pp_metting_reponse()
		{
			if(http.readyState == 4) 
			{
				//alert(http.responseText)
				var reponse=trim(http.responseText).split('**');
				
				if (reponse[0].length>2) reponse[0]=10;
				release_freezing();
				if(reponse[0]==0 || reponse[0]==1 || reponse[0]==2)
				{
					parent.emailwindow.hide();
				}
			}
		}
		
		function reset_pp_metting()
		{
			var row_num = $('#tbl_list_pp_date tbody tr').length;  
			
			for( j=1; j<=row_num; j++)
				$("#pp_meeting_date_"+j).val('');
		}
		
		function fnc_discount(val,inc)
		{
			$('#txt_sea_discount_'+inc).val(0);
			$('#txt_air_discount_'+inc).val(0);
			if(val==2)
			{
				$('#txt_sea_discount_'+inc).removeAttr('disabled','');
				$('#txt_air_discount_'+inc).attr('disabled','disabled');
			}
			else if(val==4)
			{
				$('#txt_sea_discount_'+inc).attr('disabled','disabled');
				$('#txt_air_discount_'+inc).removeAttr('disabled','');
			}
			else if(val==5)
			{
				$('#txt_sea_discount_'+inc).removeAttr('disabled','disabled');
				$('#txt_air_discount_'+inc).removeAttr('disabled','disabled');
			}
			else 
			{
				$('#txt_sea_discount_'+inc).attr('disabled','disabled');
				$('#txt_air_discount_'+inc).attr('disabled','disabled');
			}
		}
	</script>
	</head>
    <body>
        <div align="center">
        <? echo load_freeze_divs ("../../../",$permission);  
        
		if($type==1)
		{
			?>
			<fieldset style="width:820px">
				<form id="accpoinfo_1" autocomplete="off">
				<table width="800" cellspacing="0" cellpadding="0" border="0" rules="all" class="rpt_table" id="">
					<tr>
						<td width="150" align="right" colspan="8" style=" padding-right:20px;">Copy <input type="checkbox" id="cbx_cope_date" name="cbx_cope_date" checked/></td>
					</tr>
				</table>
				<table width="800" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list_pp_date">
					<thead>
						<th>SL No</th>
						<th>Order Status</th>
						<th>PO Number</th>
						<th>PO Recv. Date</th>
						<th>Ship Date</th>
						<th>Orgin. Ship Date</th>
						<th>PO Qty</th>
						<th>PP Meeting Date</th>
					</thead>
					<tbody>
					<?
						$sql= "select is_confirmed, po_number, po_received_date, pub_shipment_date, shipment_date, po_quantity, id, pp_meeting_date as pp_ship_date from  wo_po_break_down where status_active=1 and is_deleted=0 and job_no_mst='$update_id' and is_confirmed=1"; //and is_confirmed=1
						$pp_metting_data=sql_select($sql);
						$pp_meeting_date="";
						
						$i=1;
						foreach( $pp_metting_data as $row)
						{
							if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$pp_meeting_date.=$row[csf('pp_ship_date')];	
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" id="tr_1">
								<td width="40" align="center"><? echo $i; ?>
									<input type="hidden" id="pp_order_id_<? echo $i;?>" name="pp_order_id_<? echo $i;?>"  value="<? echo $row[csf('id')]; ?>" />
								</td>
								<td width="100" align="center"><? echo $order_status[$row[csf('is_confirmed')]]; ?></td>
								<td width="150" align="center"><? echo $row[csf('po_number')]; ?></td>
								<td width="100" align="center"><? echo change_date_format($row[csf('po_received_date')]); ?></td>
								<td width="100" align="center"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
								<td width="100" align="center"><? echo change_date_format($row[csf('shipment_date')]); ?></td>
								<td width="100" align="right"><? echo $row[csf('po_quantity')]; ?></td>
								<td align="center" >
									<input type="text" id="pp_meeting_date_<? echo $i;?>" name="pp_meeting_date_<? echo $i;?>" style="width:80px"     onChange="cope_pp_date(<? echo $i; ?>)"class="datepicker" value="<? echo change_date_format($row[csf('pp_ship_date')]); ?>" /></td>
							</tr>
							<?
							$i++;
						}
						?>
					</tbody>
				</table>
				<div align="center" style="margin-top:10px">
					<?
					if($pp_meeting_date!="")
					{
						echo load_submit_buttons( $permission, "fnc_pp_metting", 1,0 ,"reset_pp_metting()",1) ; 
					}
					else
					{
						echo load_submit_buttons( $permission, "fnc_pp_metting", 0,0 ,"reset_pp_metting()",1) ; 
					}
					?>
					<input type="hidden" id="hid_po_id" value="<? echo $po_id; ?>" />
					<input type="hidden" id="txt_job_no" value="<? echo $txt_job_no; ?>" />
				</div>
				</form>
			</fieldset>
        <? } else if($type==2) { ?>
            <fieldset style="width:820px">
                <form id="accpoinfo_1" autocomplete="off">
                <table width="800" cellspacing="0" cellpadding="0" border="0" rules="all" class="rpt_table" id="">
                    <tr>
                        <td width="150" align="right" colspan="11" style=" padding-right:20px;">Copy <input type="checkbox" id="cbx_cope_date" name="cbx_cope_date" /></td>
                    </tr>
                </table>
                <table width="800" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list_pp_date">
                    <thead>
                        <th width="20">SL No</th>
                        <th width="80">Order Status</th>
                        <th width="90">PO Number</th>
                        <th width="70">PO Recv. Date</th>
                        <th width="70">Ship Date</th>
                        <th width="70">Orgin. Ship Date</th>
                        <th width="80">PO Qty</th>
                        <th width="70">Extended Ship Date</th>
                        
                        <th width="70">Extended Ship Mode</th>
                        <th width="70">Sea Discount %</th>
                        <th>Air Discount %</th>
                    </thead>
                    <tbody>
                    <?
                        $sql= "select is_confirmed, po_number, po_received_date, pub_shipment_date, shipment_date, po_quantity, id, extended_ship_date as pp_ship_date, extend_ship_mode, sea_discount, air_discount from  wo_po_break_down where status_active=1 and is_deleted=0 and job_no_mst='$update_id' and is_confirmed=1"; //and is_confirmed=1
                        $pp_metting_data=sql_select($sql);
                        $pp_meeting_date="";
                        
                        $i=1;
                        foreach( $pp_metting_data as $row)
                        {
                            if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                            $pp_meeting_date.=$row[csf('pp_ship_date')];
							
							$sea_disable=""; $air_disable="";
							if($row[csf('extend_ship_mode')]==2)
							{
								$sea_disable=""; $air_disable="disabled";
							}
							else if($row[csf('extend_ship_mode')]==4)
							{
								$sea_disable="disabled"; $air_disable="";
							}
							else if($row[csf('extend_ship_mode')]==5)
							{
								$sea_disable=""; $air_disable="";
							}
							else
							{
								$sea_disable="disabled"; $air_disable="disabled";
							}
								
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
                                <td align="center"><? echo $i; ?>
                                    <input type="hidden" id="pp_order_id_<? echo $i;?>" name="pp_order_id_<? echo $i;?>"  value="<? echo $row[csf('id')]; ?>" />
                                </td>
                                <td><? echo $order_status[$row[csf('is_confirmed')]]; ?></td>
                                <td style="word-break:break-all"><? echo $row[csf('po_number')]; ?></td>
                                <td><? echo change_date_format($row[csf('po_received_date')]); ?></td>
                                <td><? echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
                                <td><? echo change_date_format($row[csf('shipment_date')]); ?></td>
                                <td align="right"><? echo $row[csf('po_quantity')]; ?></td>
                                <td align="center"><input type="text" id="pp_meeting_date_<? echo $i;?>" name="pp_meeting_date_<? echo $i;?>" style="width:60px" onChange="cope_pp_date(<? echo $i; ?>)"class="datepicker" value="<? echo change_date_format($row[csf('pp_ship_date')]); ?>" /></td>
                                
                                <td align="center"><? echo create_drop_down( "cbo_exship_mode_$i", 70, $extend_shipment_mode,"", 1, "-Select-", $row[csf('extend_ship_mode')], "fnc_discount( this.value, $i );" ); ?></td>
                                <td align="center"><input type="text" id="txt_sea_discount_<? echo $i;?>" name="txt_sea_discount_<? echo $i;?>" style="width:60px" class="text_boxes_numeric" value="<? echo $row[csf('sea_discount')]; ?>" <? echo $sea_disable; ?> /></td>
                                <td align="center"><input type="text" id="txt_air_discount_<? echo $i;?>" name="txt_air_discount_<? echo $i;?>" style="width:60px" class="text_boxes_numeric" value="<? echo $row[csf('air_discount')]; ?>" <? echo $air_disable; ?> /></td>
                            </tr>
                            <?
                            $i++;
                        }
                        ?>
                    </tbody>
                </table>
                <div align="center" style="margin-top:10px">
                    <?
                    if($pp_meeting_date!="")
                    {
                        echo load_submit_buttons( $permission, "fnc_pp_metting", 1,0 ,"reset_pp_metting()",1) ; 
                    }
                    else
                    {
                        echo load_submit_buttons( $permission, "fnc_pp_metting", 0,0 ,"reset_pp_metting()",1) ; 
                    }
                    ?>
                    <input type="hidden" id="hid_po_id" value="<? echo $po_id; ?>" />
                    <input type="hidden" id="txt_job_no" value="<? echo $txt_job_no; ?>" />
                </div>
                </form>
            </fieldset>
        <? } ?>
    </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}


// --------------------------------------------File Handover popup-------------------------------------------------------------------------------

if ($action=="all_po_file_handover")
{
	echo load_html_head_contents("Actual PO Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?> 
	<script>
		var permission='<? echo $permission; ?>';
		var type='<? echo $type; ?>';
		function cope_pp_date(id)
		{
			if (document.getElementById('cbx_cope_date').checked==true)
			{	
				var row_num = $('#tbl_list_pp_date tbody tr').length; 
				var initial=id+1;
			
				for( j=initial; j<=row_num; j++)
					$("#file_handover_date_"+j).val($("#file_handover_date_"+id).val())
			}
		}
		
		function fnc_file_handover( operation )
		{
			var row_num = $('#tbl_list_pp_date tbody tr').length;  
			
		 
				var data1='';
				for( var i=1; i<=row_num; i++)
				{
					data1+=get_submitted_data_string('file_handover_date_'+i+'*pp_order_id_'+i, "../../../",i);
				}
			
			 
			var data="action=save_update_delete_file_handover&operation="+operation+'&total_row='+row_num+'&type='+type+data1;
			
			freeze_window(operation);
			http.open("POST","woven_order_entry_controller_update.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_file_handover_reponse;
		}
		
		function fnc_file_handover_reponse()
		{
			if(http.readyState == 4) 
			{
				//alert(http.responseText)
				var reponse=trim(http.responseText).split('**');
				
				if (reponse[0].length>2) reponse[0]=10;
				release_freezing();
				if(reponse[0]==0 || reponse[0]==1 || reponse[0]==2)
				{
					parent.emailwindow.hide();
				}
			}
		}
		
		function reset_file_handover()
		{
			var row_num = $('#tbl_list_pp_date tbody tr').length;  
			
			for( j=1; j<=row_num; j++)
				$("#file_handover_date_"+j).val('');
		}
		
		function fnc_discount(val,inc)
		{
			$('#txt_sea_discount_'+inc).val(0);
			$('#txt_air_discount_'+inc).val(0);
			if(val==2)
			{
				$('#txt_sea_discount_'+inc).removeAttr('disabled','');
				$('#txt_air_discount_'+inc).attr('disabled','disabled');
			}
			else if(val==4)
			{
				$('#txt_sea_discount_'+inc).attr('disabled','disabled');
				$('#txt_air_discount_'+inc).removeAttr('disabled','');
			}
			else if(val==5)
			{
				$('#txt_sea_discount_'+inc).removeAttr('disabled','disabled');
				$('#txt_air_discount_'+inc).removeAttr('disabled','disabled');
			}
			else 
			{
				$('#txt_sea_discount_'+inc).attr('disabled','disabled');
				$('#txt_air_discount_'+inc).attr('disabled','disabled');
			}
		}
	</script>
	</head>
    <body>
        <div align="center">
        <? echo load_freeze_divs ("../../../",$permission);  
        
	
			?>
			<fieldset style="width:820px">
				<form id="accpoinfo_1" autocomplete="off">
				<table width="800" cellspacing="0" cellpadding="0" border="0" rules="all" class="rpt_table" id="">
					<tr>
						<td width="150" align="right" colspan="8" style=" padding-right:20px;">Copy <input type="checkbox" id="cbx_cope_date" name="cbx_cope_date" checked/></td>
					</tr>
				</table>
				<table width="800" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list_pp_date">
					<thead>
						<th>SL No</th>
						<th>Order Status</th>
						<th>PO Number</th>
						<th>PO Recv. Date</th>
						<th>Ship Date</th>
						<th>Orgin. Ship Date</th>
						<th>PO Qty</th>
						<th>File Handover Date</th>
					</thead>
					<tbody>
					<?
						$sql= "select is_confirmed, po_number, po_received_date, pub_shipment_date, shipment_date, po_quantity, id, file_handover_date  from  wo_po_break_down where status_active=1 and is_deleted=0 and job_no_mst='$update_id' and is_confirmed=1"; //and is_confirmed=1
						$file_handover_data=sql_select($sql);
						$file_handover_date="";
						
						$i=1;
						foreach( $file_handover_data as $row)
						{
							if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$file_handover_date.=$row[csf('file_handover_date')];	
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" id="tr_1">
								<td width="40" align="center"><? echo $i; ?>
									<input type="hidden" id="pp_order_id_<? echo $i;?>" name="pp_order_id_<? echo $i;?>"  value="<? echo $row[csf('id')]; ?>" />
								</td>
								<td width="100" align="center"><? echo $order_status[$row[csf('is_confirmed')]]; ?></td>
								<td width="150" align="center"><? echo $row[csf('po_number')]; ?></td>
								<td width="100" align="center"><? echo change_date_format($row[csf('po_received_date')]); ?></td>
								<td width="100" align="center"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
								<td width="100" align="center"><? echo change_date_format($row[csf('shipment_date')]); ?></td>
								<td width="100" align="right"><? echo $row[csf('po_quantity')]; ?></td>
								<td align="center" >
									<input type="text" id="file_handover_date_<? echo $i;?>" name="file_handover_date_<? echo $i;?>" style="width:80px"     onChange="cope_pp_date(<? echo $i; ?>)"class="datepicker" value="<? echo change_date_format($row[csf('file_handover_date')]); ?>" /></td>
							</tr>
							<?
							$i++;
						}
						?>
					</tbody>
				</table>
				<div align="center" style="margin-top:10px">
					<?
					if($file_handover_date!="")
					{
						echo load_submit_buttons( $permission, "fnc_file_handover", 1,0 ,"reset_file_handover()",1) ; 
					}
					else
					{
						echo load_submit_buttons( $permission, "fnc_file_handover", 0,0 ,"reset_file_handover()",1) ; 
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

if ($action=="bookingMeetingDate")
{
	echo load_html_head_contents("Actual PO Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');

?> 
	<script>
	var permission='<? echo $permission; ?>';
			
	function fnc_booking_metting( operation )
	{
		var data="action=save_update_delete_booking_meeting&operation="+operation+get_submitted_data_string('booking_meeting_date*txt_job_no',"../../../");
		freeze_window(operation);
		http.open("POST","woven_order_entry_controller_update.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_booking_metting_reponse;
	}

	function fnc_booking_metting_reponse()
	{
		if(http.readyState == 4) 
		{
			//alert(http.responseText)
			var reponse=trim(http.responseText).split('**');
			
			if (reponse[0].length>2) reponse[0]=10;
			release_freezing();
			if(reponse[0]==0 || reponse[0]==1 || reponse[0]==2)
			{
				parent.emailwindow.hide();
			}
		}
	}
  
	function reset_pp_metting()
	{
	   $("#booking_meeting_date").val('');
	}
    </script>
</head>

<body>
<div align="center">
 <? echo load_freeze_divs ("../../../",$permission);  ?>

	<fieldset style="width:820px">
    <form id="accpoinfo_1" autocomplete="off">
        
        <table width="800" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list_pp_date">
            <thead>
                <th>SL No</th>
                <th>Company Name</th>
                <th>Job No</th>
                <th>Buyer Name</th>
                <th>Style Ref.</th>
                <th>Order Uom</th>
                <th>Job Qty</th>
                <th>Booking Meeting Date</th>
            </thead>
            <tbody>
            <?
			 //ALTER TABLE `wo_po_details_master`  ADD `booking_meeting_date` DATE NOT NULL AFTER `style_owner`
			 //ALTER TABLE LOGIC3RDVERSION.WO_PO_DETAILS_MASTER ADD (booking_meeting_date  DATE);
			 $sql= "select job_no,company_name,buyer_name,style_ref_no,job_quantity,order_uom,booking_meeting_date from  wo_po_details_master  where status_active=1 and is_deleted=0 and job_no='$update_id'"; //and is_confirmed=1
			$booking_metting_data=sql_select($sql);
			$pp_meeting_date="";
			  
				$i=1;
				foreach($booking_metting_data as $row)
				{
				  $booking_meeting_date=$row[csf('booking_meeting_date')];	
			?>
            <tr class="" id="tr_1">
              <td width="40" align="center"><? echo $i; ?></td>
              <td width="100" align="center"><? echo $comp[$row[csf('company_name')]]; ?></td>
              <td width="150" align="center"><? echo $row[csf('job_no')]; ?></td>
              <td width="100" align="center"><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></td>
              <td width="100" align="center"><? echo $row[csf('style_ref_no')]; ?></td>
              <td width="100" align="center"><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?></td>
              <td width="100" align="right"><? echo $row[csf('job_quantity')]; ?></td>
              <td align="center" >
              <input type="text" id="booking_meeting_date" name="booking_meeting_date" style="width:80px" class="datepicker" value="<? echo change_date_format($row[csf('booking_meeting_date')]); ?>" /></td>
            </tr>
                <?
				$i++;
				}
			
				?>
            </tbody>
        </table>
        <div align="center" style="margin-top:10px">
           <?
		   if($booking_meeting_date!="")
			   {
				echo load_submit_buttons( $permission, "fnc_booking_metting", 1,0 ,"reset_pp_metting()",1) ; 
			   }
			 else
			    {
				echo load_submit_buttons( $permission, "fnc_booking_metting", 0,0 ,"reset_pp_metting()",1) ; 
			   }
		?>
            <input type="hidden" id="txt_job_no" value="<? echo $update_id; ?>" />
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

if($action=="save_update_delete_pp_meeting")
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
		
		$add_comma=0;
		if($type==1)
		{
			$field_array="pp_meeting_date*updated_by*update_date";
			for ($i=1;$i<=$total_row;$i++)
			{
				$metting_date="pp_meeting_date_".$i;
				$order_id="pp_order_id_".$i;
				if(str_replace("'",'',$$metting_date)!="")
				{
					$id_arr[]=str_replace("'",'',$$order_id);
					$data_array_up[str_replace("'",'',$$order_id)] =explode("*",("".$$metting_date."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				}
			}
		}
		else if($type==2)
		{
			$field_array="extended_ship_date*extend_ship_mode*sea_discount*air_discount*updated_by*update_date";
			for ($i=1;$i<=$total_row;$i++)
			{
				$metting_date="pp_meeting_date_".$i;
				$order_id="pp_order_id_".$i;
				$cbo_exship_mode="cbo_exship_mode_".$i;
				$txt_sea_discount="txt_sea_discount_".$i;
				$txt_air_discount="txt_air_discount_".$i;
				if(str_replace("'",'',$$metting_date)!="")
				{
					$id_arr[]=str_replace("'",'',$$order_id);
					$data_array_up[str_replace("'",'',$$order_id)] =explode("*",("".$$metting_date."*".$$cbo_exship_mode."*".$$txt_sea_discount."*".$$txt_air_discount."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				}
			}
		}
		//echo "10**".bulk_update_sql_statement( "wo_po_break_down", "id", $field_array, $data_array_up, $id_arr );die;
		 $rID=execute_query(bulk_update_sql_statement( "wo_po_break_down", "id", $field_array, $data_array_up, $id_arr ));
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
	else if ($operation==1)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$add_comma=0;
		if($type==1)
		{
			$field_array="pp_meeting_date*updated_by*update_date";
			for ($i=1;$i<=$total_row;$i++)
			{
				$metting_date="pp_meeting_date_".$i;
				$order_id="pp_order_id_".$i;
				// if(str_replace("'",'',$$metting_date)!="")
				//{
				$id_arr[]=str_replace("'",'',$$order_id);
				$data_array_up[str_replace("'",'',$$order_id)] =explode("*",("".$$metting_date."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				// }
			}
		}
		else if($type==2)
		{
			$field_array="extended_ship_date*extend_ship_mode*sea_discount*air_discount*updated_by*update_date";
			for ($i=1;$i<=$total_row;$i++)
			{
				$metting_date="pp_meeting_date_".$i;
				$order_id="pp_order_id_".$i;
				$cbo_exship_mode="cbo_exship_mode_".$i;
				$txt_sea_discount="txt_sea_discount_".$i;
				$txt_air_discount="txt_air_discount_".$i;
				if(str_replace("'",'',$$metting_date)!="")
				{
					$id_arr[]=str_replace("'",'',$$order_id);
					$data_array_up[str_replace("'",'',$$order_id)] =explode("*",("".$$metting_date."*".$$cbo_exship_mode."*".$$txt_sea_discount."*".$$txt_air_discount."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				}
			}
		}
		//echo "10**".bulk_update_sql_statement( "wo_po_break_down", "id", $field_array, $data_array_up, $id_arr );die;
		$rID=execute_query(bulk_update_sql_statement( "wo_po_break_down", "id", $field_array, $data_array_up, $id_arr ));
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
		else if($db_type==2 || $db_type==1 )
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
}
if($action=="save_update_delete_file_handover")
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
		
		$add_comma=0;
	 
			$field_array="file_handover_date*updated_by*update_date";
			for ($i=1;$i<=$total_row;$i++)
			{
				$metting_date="file_handover_date_".$i;
				$order_id="pp_order_id_".$i;
				if(str_replace("'",'',$$metting_date)!="")
				{
					$id_arr[]=str_replace("'",'',$$order_id);
					$data_array_up[str_replace("'",'',$$order_id)] =explode("*",("".$$metting_date."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				}
			}
		
	 
		//echo "10**".bulk_update_sql_statement( "wo_po_break_down", "id", $field_array, $data_array_up, $id_arr );die;
		 $rID=execute_query(bulk_update_sql_statement( "wo_po_break_down", "id", $field_array, $data_array_up, $id_arr ));
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
	else if ($operation==1)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$add_comma=0;
	 
			$field_array="file_handover_date*updated_by*update_date";
			for ($i=1;$i<=$total_row;$i++)
			{
				$metting_date="file_handover_date_".$i;
				$order_id="pp_order_id_".$i;
				// if(str_replace("'",'',$$metting_date)!="")
				//{
				$id_arr[]=str_replace("'",'',$$order_id);
				$data_array_up[str_replace("'",'',$$order_id)] =explode("*",("".$$metting_date."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				// }
			}
		 
		//echo "10**".bulk_update_sql_statement( "wo_po_break_down", "id", $field_array, $data_array_up, $id_arr );die;
		$rID=execute_query(bulk_update_sql_statement( "wo_po_break_down", "id", $field_array, $data_array_up, $id_arr ));
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
		else if($db_type==2 || $db_type==1 )
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
}

if($action=="save_update_delete_booking_meeting")
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
		$rID=execute_query("update wo_po_details_master set booking_meeting_date=$booking_meeting_date, updated_by=".$_SESSION['logic_erp']['user_id'].",update_date='".$pc_date_time."' where job_no=$txt_job_no");

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
	else if ($operation==1)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$rID=execute_query("update wo_po_details_master set booking_meeting_date=$booking_meeting_date, updated_by=".$_SESSION['logic_erp']['user_id'].",update_date='".$pc_date_time."' where job_no=$txt_job_no");

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
	
}




/*
if($action=="save_update_delete_pp_meeting")
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
		echo "10**100";
		 $add_comma=0;
		 $field_array="pp_meeting_date,updated_by,update_date";
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $metting_date="pp_meeting_date_".$i;
			 $order_id="pp_order_id_".$i;
			
            if(str_replace("'",'',$$metting_date)!="")
			 {
				 $id_arr[]=str_replace("'",'',$$order_id);
				 $data_array_up[str_replace("'",'',$$order_id)] =explode("*",("".$$metting_date."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			 }
		 }
		echo "10**".bulk_update_sql_statement( "wo_po_break_down", "id", $field_array, $data_array_up, $id_arr );die;
		 $rID=execute_query(bulk_update_sql_statement( "wo_po_break_down", "id", $field_array, $data_array_up, $id_arr ));
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
			if($rID){
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
		
		if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}

		 $add_comma=0;
		 $field_array="pp_meeting_date,updated_by,update_date";
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $poNo="poNo_".$i;
			 $poQnty="poQnty_".$i;
			 $rowid="rowid_".$i;
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
		
		if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}

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
*/





if($action=="save_update_delete_accpoinfo")
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
		
		if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}		
		 $id=return_next_id( "id", "wo_po_acc_po_info", 1 ) ;
		 $field_array="id,job_no,po_break_down_id,acc_po_no,acc_po_qty,inserted_by,insert_date";
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $poNo="poNo_".$i;
			 $poQnty="poQnty_".$i;
			 $rowid="rowid_".$i;

		    $id_arr[]=str_replace("'",'',$$rowid);
			$data_array_up[str_replace("'",'',$$rowid)] =explode("*",("".$$poNo."*".$$poQnty."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
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
		
		if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0";  disconnect($con);die;}

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
		
		if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; disconnect($con); die;}

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

function fnc_smv_style_integration($db_type,$cbo_company_name,$txt_job_no,$currercy,$sewSmv,$cutSmv,$page)
{
	if($page==1)
	{
		$is_pre_cost="";

		$pre_cost_data=sql_select("select job_no, cm_cost_predefined_method_id, sew_smv, cut_smv, sew_effi_percent, cut_effi_percent, exchange_rate, machine_line, prod_line_hr, costing_per, costing_date from wo_pre_cost_mst where job_no='$txt_job_no' and is_deleted=0 and status_active=1");
		$cm_cost=0;

		$cm_cost_predefined_method_id=$pre_cost_data[0][csf("cm_cost_predefined_method_id")]*1;
		$txt_sew_smv=$sewSmv*1;//$pre_cost_data[0][csf("sew_smv")];
		$txt_cut_smv=$cutSmv*1;//$pre_cost_data[0][csf("cut_smv")];
		$txt_sew_efficiency_per=$pre_cost_data[0][csf("sew_effi_percent")]*1;
		$txt_cut_efficiency_per=$pre_cost_data[0][csf("cut_effi_percent")]*1;
		//var txt_efficiency_wastage= parseFloat(document.getElementById('txt_efficiency_wastage').value);

		$cbo_currercy=str_replace("'","",$currercy);
		$txt_exchange_rate= $pre_cost_data[0][csf("exchange_rate")]*1;
		$txt_machine_line= $pre_cost_data[0][csf("machine_line")];
		$txt_prod_line_hr= $pre_cost_data[0][csf("prod_line_hr")];
		$cbo_costing_per= $pre_cost_data[0][csf("costing_per")];
		$costing_date= $pre_cost_data[0][csf("costing_date")];
		//var txt_job_no= document.getElementById('txt_job_no').value;

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

		$sql_pre_cost_dtls="select max(cm_cost) as cm_cost, sum(price_dzn) as price_dzn, sum(price_pcs_or_set) as price_pcs_set, sum(total_cost-cm_cost) as prev_tot_cost from wo_pre_cost_dtls where job_no='$txt_job_no' and is_deleted=0 and status_active=1 group by job_no";
		$sql_pre_cost_dtls_arr=sql_select($sql_pre_cost_dtls);
		$price_dzn=0; $cost_pcs_set=0; $prev_tot_cost=0; $prev_cm_cost=0;

		$price_dzn=$sql_pre_cost_dtls_arr[0][csf("price_dzn")]*1;
		$price_pcs_set=$sql_pre_cost_dtls_arr[0][csf("price_pcs_set")]*1;
		$prev_tot_cost=$sql_pre_cost_dtls_arr[0][csf("prev_tot_cost")]*1;
		$prev_cm_cost=$sql_pre_cost_dtls_arr[0][csf("cm_cost")]*1;

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
			else
			{
				$cm_cost=$prev_cm_cost;
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
