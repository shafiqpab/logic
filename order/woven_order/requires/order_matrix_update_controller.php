﻿<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];

//************************************ Start*************************************************
$color_library=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name"  );
$size_library=return_library_array( "select id,size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name"  );

// Master Form*************************************Master Form*************************
if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 150, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select --", $selected, "" );
	exit();	 
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "sub_dept_load(this.value,document.getElementById('cbo_product_department').value); check_tna_templete(this.value); load_drop_down( 'requires/order_matrix_update_controller', this.value, 'load_drop_down_season', 'season_td'); " );   
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

if ($action=="cbo_factory_merchant")
{
	echo create_drop_down( "cbo_factory_merchant", 150, "select id,team_member_name from lib_mkt_team_member_info where team_id='$data' and status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-- Select Team Member --", $selected, "" );
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
	echo create_drop_down( "cbo_projected_po", 100, "select id, po_number from  wo_po_break_down where job_no_mst='$data' and is_confirmed=2 and status_active =1 and is_deleted=0 order by po_number","id,po_number", 1, "-- Select --", "", "" );
	exit();
}

if ($action=="load_drop_down_season")
{
	echo create_drop_down( "cbo_season_id", 150, "select id, season_name from lib_buyer_season where buyer_id='$data' and status_active =1 and is_deleted=0 order by season_name ASC","id,season_name", 1, "-- Select Season--", "", "" );
	exit();
}

if ($action=="load_variable_settings")
{
	$sql_result = sql_select("select variable_list, tna_integrated, copy_quotation, publish_shipment_date, po_update_period, po_current_date, season_mandatory, excut_source from variable_order_tracking where company_name=$data and variable_list in (14,20,25,32,33,44,45) and status_active=1 and is_deleted=0 order by variable_list ASC");
	$tna_integrated=0; $copy_quotation=0; $publish_shipment_date=0; $po_update_period=0; $po_current_date=0; $season_mandatory=0; $excut_source=0; 
 	foreach($sql_result as $result)
	{
		if($result[csf('variable_list')]==14) $tna_integrated=$result[csf('tna_integrated')];
		else if($result[csf('variable_list')]==20) $copy_quotation=$result[csf('copy_quotation')];
		else if($result[csf('variable_list')]==25) $publish_shipment_date=$result[csf('publish_shipment_date')];
		else if($result[csf('variable_list')]==32) $po_update_period=$result[csf('po_update_period')];
		else if($result[csf('variable_list')]==33) $po_current_date=$result[csf('po_current_date')];
		else if($result[csf('variable_list')]==44) $season_mandatory=$result[csf('season_mandatory')];
		else if($result[csf('variable_list')]==45) $excut_source=$result[csf('excut_source')];
	}
	echo $tna_integrated."_".$copy_quotation."_".$publish_shipment_date."_".$po_update_period."_".$po_current_date."_".$season_mandatory."_".$excut_source;
 	exit();
}

if($action=="check_tna_templete")
{
	$data=explode("_",$data);
	$temp=0;
	$sql_temp=sql_select("select count(for_specific) as for_specific  from tna_task_template_details where for_specific in ($data[0],0) and status_active=1 and is_deleted=0");
	foreach($sql_temp as $row_temp)
	{
		if($row_temp[csf('for_specific')]>0) $temp=1;
		else $temp=0;
	}
	//echo $temp;	
	$tna=0;
	$tna_integrated=return_field_value("tna_integrated", "variable_order_tracking", "company_name=$data[1] and variable_list=14 and status_active=1 and is_deleted=0");
    if($tna_integrated==1) $tna=1; else $tna=0;
	echo $temp."_".$tna;
	die;
}

if ($action=="load_drop_gmts_item")
{
	echo create_drop_down( "cbo_gmtsItem_id", 120, $garments_item, 0, 1, "--Select Item--", $data,"fnc_calAmountQty_ex(0,1);",'',$data);  
	exit();	 
}

if ($action=="load_dorp_down_code")
{
	echo create_drop_down( "cbo_code_id", 100,"select id, ultimate_country_code from  lib_country_loc_mapping where country_id='$data' and status_active=1 and is_deleted=0 order by ultimate_country_code", "id,ultimate_country_code", 1, "--Select Code--", "","");
	exit();	 
}

/*if ($action=="load_dorp_down_ultimate_country")
{
	echo create_drop_down( "cbo_country_id", 100,"select id, ultimate_country_code from  lib_country_loc_mapping where country_id='$data' and status_active=1 and is_deleted=0 order by ultimate_country_code", "id,ultimate_country_code", 1, "--Ultimate Country--", "","");
	exit();	 
}
*/
if ($action=="load_dorp_down_countryCode")
{
	echo create_drop_down( "cbo_countryCode_id", 100,"select id, ultimate_country_code from  lib_country_loc_mapping where country_id='$data' and status_active=1 and is_deleted=0 order by ultimate_country_code", "id,ultimate_country_code", 1, "--Country Code--", "","");
	exit();	 
}

if ($action=="load_drop_down_buyer_pop")
{
	echo create_drop_down( "cbo_buyer_id", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );   
	exit();	 
}

if ($action=="load_buyer_style_po_check")
{
	$ex_data=explode('_', $data);
	$company_id=$ex_data[0];
	$buyer_id=$ex_data[1];
	$style_ref=$ex_data[2];
	$po_no=$ex_data[3];
	$po_id=$ex_data[4];
	if($po_id=="") $po_id_cond=""; else $po_id_cond=" and b.id!='$po_id'";
	$sql_check=sql_select("Select a.job_no from  wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name='$company_id' and a.buyer_name='$buyer_id' and a.style_ref_no='$style_ref' and b.po_number='$po_no' $po_id_cond and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.job_no order by a.job_no ASC");
	$job_no_all='';
	foreach ($sql_check as $row)
	{
		if($job_no_all=='') $job_no_all=$row[csf("job_no")]; else $job_no_all.=', '.$row[csf("job_no")];
	}
	echo "50***Duplicate Buyer, Style and PO no.\n Merchandising Job: ".$job_no_all."\nPress \"OK\" Allow this PO No.\nPress \"Cancel\" Dont Allow this PO No.***".$job_no_all;
	exit();	 
}

if ($action=="quotation_id_popup")
{
  	echo load_html_head_contents("Quotation Tag popup","../../../", 1, 1, $unicode);
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
                <th width="100">Style Reff.</th>
                <th width="180">Date Range</th>
                <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th> 
            </tr>          
        </thead>
        <tr class="general">
            <td> <input type="hidden" id="selected_id">
                <? echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $cbo_company_name,"load_drop_down('order_matrix_update_controller', this.value, 'load_drop_down_buyer_pop', 'buyer_pop_td' );",'1' ); ?>
            </td>
            <td id="buyer_pop_td"><? echo create_drop_down( "cbo_buyer_id", 150, $blank_array,'', 1, "-- Select Buyer --" ); ?></td>
            <td><input type="text" style="width:70px" class="text_boxes"  name="txt_quotation_no" id="txt_quotation_no" /></td>
            <td align="center"><input type="text" style="width:100px" class="text_boxes"  name="txt_style" id="txt_style" /></td>
            <td>
                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date">To
                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To Date">
            </td> 
            <td align="center">
            	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_id').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_quotation_no').value+'_'+document.getElementById('txt_style').value, 'create_quotation_id_list_view', 'search_div', 'order_matrix_update_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
        </tr>
        <tr>
        	<td align="center" height="40" colspan="6"><? echo load_month_buttons(1); ?></td>
        </tr>
    </table>
    </form>
    	<div id="search_div"></div>
    </div>
    </body> 
    <script>
		load_drop_down('order_matrix_update_controller', <? echo  $cbo_company_name ?>, 'load_drop_down_buyer_pop', 'buyer_pop_td' )
		document.getElementById('cbo_buyer_id').value=<? echo $cbo_buyer_name; ?>
    </script>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if($action=="create_quotation_id_list_view")
{
	$data=explode('_',$data);
	//if ($data[0]!=0) $company=" and company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $buyer_cond=" and a.buyer_id='$data[1]'"; else $buyer_cond="";// else { echo "Please Select Buyer First."; die; }
	echo $data[1];
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
	//$sql= "select id, buyer_id, style_ref, style_desc, pord_dept, offer_qnty, est_ship_date from wo_price_quotation a where status_active=1 and is_deleted=0 $company $buyer $style_cond $quotation_id_cond order by id DESC";
	
	$sql= "select a.id, a.cost_sheet_no, a.buyer_id, a.style_ref, a.style_des, a.department_id, a.offer_qty, a.delivery_date, b.confirm_style from qc_mst a, qc_confirm_mst b where a.id=b.cost_sheet_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $buyer_cond $style_cond $quotation_id_cond order by a.id DESC";
	echo  create_list_view("list_view", "QC ID, Cost Sheet No, Buyer Name, Style Ref, Style Desc., Prod. Dept., Offer Qty, Delivery Date", "50,70,100,100,150,100,100","800","280",0, $sql , "js_set_value", "id", "", 1, "0,0,buyer_id,0,0,pord_dept,0,0", $arr , "id,cost_sheet_no,buyer_id,style_ref,style_des,department_id,offer_qty,delivery_date", "",'','0,0,0,0,0,0,2,3') ;
	exit();
}

if($action=="populate_data_from_search_popup_quotation")
{
	$data_array=sql_select("select a.id, a.buyer_id, a.style_ref, a.pord_dept, a.product_code, a.style_desc, a.currency, a.agent, a.region, a.season_matrix, a.order_uom, a.gmts_item_id, a.set_break_down, a.total_set_qnty, b.price_with_commn_pcs from wo_price_quotation a, wo_price_quotation_costing_mst b where a.id=b.quotation_id and a.id='$data'");
	foreach ($data_array as $row)
	{
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_id")]."';\n";  
		echo "document.getElementById('txt_style_ref').value = '".$row[csf("style_ref")]."';\n";
		echo "document.getElementById('txt_style_description').value = '".$row[csf("style_desc")]."';\n";
		echo "document.getElementById('cbo_product_department').value = '".$row[csf("pord_dept")]."';\n"; 
		echo "document.getElementById('txt_product_code').value = '".$row[csf("product_code")]."';\n";
		echo "document.getElementById('cbo_currercy').value = '".$row[csf("currency")]."';\n";  
		echo "document.getElementById('cbo_agent').value = '".$row[csf("agent")]."';\n";  
		echo "document.getElementById('cbo_region').value = '".$row[csf("region")]."';\n";  
		echo "document.getElementById('cbo_order_uom').value = '".$row[csf("order_uom")]."';\n";  
		echo "document.getElementById('item_id').value = '".$row[csf("gmts_item_id")]."';\n";  
		echo "document.getElementById('set_breck_down').value = '".$row[csf("set_break_down")]."';\n";  
		echo "document.getElementById('tot_set_qnty').value = '".$row[csf("total_set_qnty")]."';\n";  
		echo "document.getElementById('txt_quotation_id').value = '".$row[csf("id")]."';\n";
		echo "document.getElementById('txt_avg_price').value = '".$row[csf("price_with_commn_pcs")]."';\n";
		echo "document.getElementById('cbo_season_id').value = '".$row[csf("season_matrix")]."';\n";
		exit();
	}
}

if ($action=="qc_id_popup")
{
  	echo load_html_head_contents("Quick Costing Tag popup","../../../", 1, 1, $unicode);
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
            	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_buyer_id').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_quotation_no').value+'_'+document.getElementById('txt_style').value, 'create_qc_id_list_view', 'search_div', 'order_matrix_update_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
        </tr>
        <tr>
        	<td align="center" height="40" colspan="5"><? echo load_month_buttons(1); ?></td>
        </tr>
    </table>
    </form>
    	<div id="search_div"></div>
    </div>
    </body> 
    <script>
		load_drop_down('order_matrix_update_controller', <? echo  $cbo_company_name ?>, 'load_drop_down_buyer_pop', 'buyer_pop_td' )
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
	//if ($data[0]!=0) $company=" and company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[0]!=0) $buyer=" and a.buyer_id='$data[0]'"; else $buyer="";//else { echo "Please Select Buyer First."; die; }
	echo $data[0];
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
	
	$sql= "select a.id, a.cost_sheet_no, a.buyer_id, a.style_ref, a.style_des, a.department_id, a.offer_qty, a.delivery_date, b.confirm_style from qc_mst a, qc_confirm_mst b where a.id=b.cost_sheet_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $buyer_cond $style_cond $quotation_id_cond order by a.id DESC";
	
	echo  create_list_view("list_view", "QC ID, Cost Sheet No, Buyer Name, Style Ref, Style Desc., Prod. Dept., Offer Qty, Delivery Date", "50,70,100,100,150,100,100","800","280",0, $sql , "js_set_value", "id", "", 1, "0,0,buyer_id,0,0,department_id,0,0", $arr , "id,cost_sheet_no,buyer_id,confirm_style,style_des,department_id,offer_qty,delivery_date", "",'','0,0,0,0,0,0,2,3') ;
	exit();
}

if($action=="populate_data_from_search_popup_qc")
{
	$data_array=sql_select("select a.id, a.cost_sheet_no, a.buyer_id, a.season_id, a.style_ref, a.style_des, a.department_id, a.offer_qty, a.delivery_date, b.confirm_style from qc_mst a, qc_confirm_mst b where a.id=b.cost_sheet_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id='$data'");
	foreach ($data_array as $row)
	{
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_id")]."';\n";  
		echo "document.getElementById('txt_style_ref').value = '".$row[csf("confirm_style")]."';\n";
		echo "document.getElementById('txt_style_description').value = '".$row[csf("style_des")]."';\n";
		echo "document.getElementById('cbo_product_department').value = '".$row[csf("department_id")]."';\n"; 
		echo "document.getElementById('cbo_season_id').value = '".$row[csf("season_id")]."';\n";
		echo "document.getElementById('txt_quotation_id').value = '".$row[csf("id")]."';\n";
		exit();
	}
}

if($action=="check_precost")
{
	$sql_data=sql_select("select count(a.id) as id, c.order_uom from  wo_pre_cost_mst a, wo_pre_cost_fabric_cost_dtls b, wo_po_details_master c where a.job_no=b.job_no and a.job_no=c.job_no and a.job_no='$data' and a.is_deleted=0 and  a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.job_no,c.order_uom");
	$id=0;
	$order_uom=0;
	foreach($sql_data as $row)
	{
		$id=$row[csf('id')];
		$order_uom=$row[csf('order_uom')];
	}
	echo trim($id)."_".trim($order_uom);
	die;
}
if($action=="check_precost_data")
{
	$sql_data=sql_select("select job_no from wo_pre_cost_mst where job_no='$data' and is_deleted=0 and status_active=1");
	$job_no=$sql_data[0][csf("job_no")];
	echo count($sql_data);
	die;
}
if($action=="check_precost_approve")
{
	$sql_data=sql_select("select approved from wo_pre_cost_mst where job_no='$data' and is_deleted=0 and status_active=1");
	$isapproved=$sql_data[0][csf("approved")];
	echo trim($isapproved);
	die;
}

if($action=="open_set_list_view")
{
	echo load_html_head_contents("Set Entry","../../../", 1, 1, $unicode);
	extract($_REQUEST); ?>
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
			$('#increaseset_'+i).removeAttr("onClick").attr("onClick","add_break_down_set_tr("+i+")");
			$('#decreaseset_'+i).removeAttr("onClick").attr("onClick","fn_delete_down_tr("+i+",'tbl_set_details')");
			$('#cboitem_'+i).val(''); 
			set_sum_value_set( 'tot_set_qnty', 'txtsetitemratio_' );
			set_sum_value_set( 'tot_smv_qnty', 'smvset_' );
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

	function calculate_set_smv(i)
	{
		var txtsetitemratio=document.getElementById('txtsetitemratio_'+i).value;
		var smv=document.getElementById('smv_'+i).value;
		var set_smv=txtsetitemratio*smv;
		document.getElementById('smvset_'+i).value=set_smv;
		set_sum_value_set( 'tot_set_qnty', 'txtsetitemratio_' );
		set_sum_value_set( 'tot_smv_qnty', 'smvset_' );
	}

	function set_sum_value_set(des_fil_id,field_id)
	{
		var rowCount = $('#tbl_set_details tr').length-1;
		if(des_fil_id=="tot_set_qnty")
		{
			math_operation( des_fil_id, field_id, '+', rowCount );
		}
		else if(des_fil_id=="tot_smv_qnty")
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
			if(set_breck_down=="")
			{
				set_breck_down+=$('#cboitem_'+i).val()+'_'+$('#txtsetitemratio_'+i).val()+'_'+($('#smv_'+i).val()*1)+'_'+$('#smvset_'+i).val()+'_'+$('#complexity_'+i).val()+'_'+$('#emblish_'+i).val();
				item_id+=$('#cboitem_'+i).val();
			}
			else
			{
				set_breck_down+="__"+$('#cboitem_'+i).val()+'_'+$('#txtsetitemratio_'+i).val()+'_'+($('#smv_'+i).val()*1)+'_'+$('#smvset_'+i).val()+'_'+$('#complexity_'+i).val()+'_'+$('#emblish_'+i).val();
				item_id+=","+$('#cboitem_'+i).val();
			}
		}
		document.getElementById('set_breck_down').value=set_breck_down;
		document.getElementById('item_id').value=item_id;
		parent.emailwindow.hide();
	}

	function open_emblishment_pop_up(i)
	{ 
		var page_link="order_matrix_update_controller.php?action=open_emblishment_list";
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
			$disabled=0;
			if($precostfound >0 ){ 
				echo "Pre Cost Found, Any Change will be not allowed";
				$disabled=1;
			}
			else{
				$disabled=0;
			}
			$disabled=1;
        ?>
        <form id="setdetails_1" autocomplete="off">
            <input type="hidden" id="set_breck_down" />     
            <input type="hidden" id="item_id" />  
            <input type="hidden" id="unit_id" value="<? echo $unit_id;  ?>" />        	
            <table width="560" cellspacing="0" class="rpt_table" border="0" id="tbl_set_details" rules="all">
                <thead>
                    <tr>
                        <th width="230" class="must_entry_caption">Item</th>
                        <th width="40" class="must_entry_caption">Set Ratio</th>
                        <th width="40" class="must_entry_caption">SMV/ Pcs</th>
                        <th width="80">Complexity</th>
                        <th width="80">Embellishment</th>
                        <th width=""></th>
                    </tr>
                </thead>
                <tbody>
					<?
                    //echo $set_breck_down;
                    $data_array=explode("__",$set_breck_down);
                    if($data_array[0]=="")
                    {
                    	$data_array=array();
                    }
                    if(count($data_array)>0)
                    {
						$i=0;
						foreach( $data_array as $row )
						{
							$i++;
							$data=explode('_',$row);
							?>
							<tr id="settr_1" align="center">
                                <td><? echo create_drop_down( "cboitem_".$i, 230, get_garments_item_array(2), "",1," -- Select Item --", $data[0], "check_duplicate(".$i.",this.id )",$disabled,'' ); ?></td>
                                <td><input type="text" id="txtsetitemratio_<? echo $i;?>"   name="txtsetitemratio_<? echo $i;?>" style="width:30px"  class="text_boxes_numeric" onChange="calculate_set_smv(<? echo $i;?>)"  value="<? echo $data[1] ?>" <? if ($unit_id==1){echo "readonly";} else{echo "";}?> <? if ($disabled==1){echo "disabled";}else{ echo "";} ?>  /></td>
                                <td><input type="text" id="smv_<? echo $i;?>"   name="smv_<? echo $i;?>" style="width:30px"  class="text_boxes_numeric" onChange="calculate_set_smv(<? echo $i;?>)"  value="<? echo $data[2] ?>" <? if ($disabled==1){echo "disabled";}else{ echo "";} ?> /> 
                                	<input type="hidden" id="smvset_<? echo $i;?>"   name="smvset_<? echo $i;?>" style="width:30px"  class="text_boxes_numeric"  value="<? echo $data[3] ?>" readonly/> 
                                </td>
                                <td><? echo create_drop_down( "complexity_".$i, 80, $complexity_level, "",1," -- Select --", $data[4], "",$disabled,'' ); ?></td>
                                <td><? echo create_drop_down( "emblish_".$i, 80, $yes_no, "",1," -- Select--", $data[5], "",$disabled,'' ); ?></td>
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
                            <td><? echo create_drop_down( "cboitem_1", 230, get_garments_item_array(2), "",1,"--Select--", 0, "check_duplicate(1,this.id )",'','' ); ?></td>
                            <td>
                            	<input type="text" id="txtsetitemratio_1" name="txtsetitemratio_1" style="width:30px" class="text_boxes_numeric" onChange="calculate_set_smv(1)"  value="<?  if ($unit_id==1){echo "1";} else{echo "";}?>" <? if ($unit_id==1){echo "readonly";} else{echo "";}?> /> 
                            </td>
                            <td>
                                <input type="text" id="smv_1" name="smv_1" style="width:30px" class="text_boxes_numeric" onChange="calculate_set_smv(1)"  value="" /> 
                                <input type="hidden" id="smvset_1" name="smvset_1" style="width:30px" class="text_boxes_numeric" value="0"  /> 
                            </td>
                            <td><? echo create_drop_down( "complexity_1", 80, $complexity_level, "",1," -- Select --", 0, "",'','' ); ?></td>
                            <td><? echo create_drop_down( "emblish_1", 80, $yes_no, "",1," -- Select --", 0, "",'','' ); ?></td>
                            <td>
                                <input type="button" id="increaseset_1" style="width:30px" class="formbutton" value="+" onClick="add_break_down_set_tr(1)" />
                                <input type="button" id="decreaseset_1" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_delete_down_tr(1 ,'tbl_set_details');"/>
                            </td> 
						</tr>
						<? 
                    } 
                    ?>
                </tbody>
            </table>
            <table width="560" cellspacing="0" class="rpt_table" border="0" rules="all">
                <tfoot>
                    <tr>
                        <th width="230">Total</th>
                        <th width="40">
                        	<input type="text" id="tot_set_qnty" name="tot_set_qnty" class="text_boxes_numeric" style="width:30px"  value="<? if($tot_set_qnty!=''){ echo $tot_set_qnty;} else{ echo 1;} ?>" readonly />
                        </th>
                        <th  width="40">
                        	<input type="text" id="tot_smv_qnty" name="tot_smv_qnty" class="text_boxes_numeric" style="width:30px"  value="<? if($tot_smv_qnty!=''){ echo $tot_smv_qnty;} else{ echo 0;} ?>" readonly />
                        </th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                    </tr>
                </tfoot>
            </table>
            <table width="560" cellspacing="0" class="" border="0">
                <tr>
                    <td align="center" width="100%" class="button_container"><input type="button" class="formbutton" value="Close" onClick="js_set_value_set()"/></td> 
                </tr>
            </table>
            </form>
        </fieldset>
    </div>
    </body>  
    <script>$('#smv_1').focus();</script>         
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
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
			}  //alert(data_all);return;
			var data="action=save_update_delete_wo_order_entry_ref&operation="+operation+'&total_row='+row_num+'&txt_job_no='+txt_job_no+data_all;
			freeze_window(operation);
			http.open("POST","order_matrix_update_controller.php",true);
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
					parent.emailwindow.hide();
				}
				set_button_status(1, permission, 'fnc_order_entry_terms_condition',1);
			}
		}
		//Row Sequence

		function row_sequence(row_id)
		{
			var row_num=$('#tbl_termcondi_details tbody tr').length-1;
			var txt_seq=$('#termscondition_'+row_id).val();
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
    
		if($db_type==0) $insert_year_cond="  YEAR(job_insert_date)=$insert_date";
		else if($db_type==2) $insert_year_cond=" to_char(job_insert_date,'YYYY')=$insert_date";
    ?>
    <input type="text" id="txt_job_no" class="text_boxes" style="width:100px"  name="txt_job_no" value="<? echo str_replace("'","",$txt_job_no) ?>"/>
    <input type="hidden" id="job_insert_date" class="text_boxes" style="width:100px"  name="txt_job_no" value="<? echo str_replace("'","",$job_insert_date) ?>"/>
    <input type="hidden" id="insert_date" class="text_boxes" style="width:100px"  name="txt_job_no" value="<? echo str_replace("'","",$insert_date) ?>"/>
    
    <table width="350" cellspacing="0" class="rpt_table" border="0" id="tbl_termcondi_details" rules="all">
        <thead>
            <tr>
            	<th width="30">Sl</th><th width="150">Internal Ref</th><th width="80"></th>
            </tr>
        </thead>
        <tbody>
        <?
        $current_year=date("Y",time());
        $data_array=sql_select("select max(internal_ref) as internal_ref from wo_order_entry_internal_ref where  $insert_year_cond");// quotation_id='$data'
        $max_ref=$data_array[0][csf('internal_ref')]+1;
        $data_array=sql_select("select id as update_id, internal_ref from wo_order_entry_internal_ref where job_no=$txt_job_no order by id asc");// quotation_id='$data'
        
        if( count($data_array)>0)
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
                        <input type="text" id="termscondition_<? echo $i;?>"   name="termscondition_<? echo $i;?>" style="width:140px"   class="text_boxes_numeric" value="<? echo $row[csf('internal_ref')]; ?>" onBlur="row_sequence(<? echo $i; ?>); "   /> 
                        <input type="hidden" id="termsconditionID_<? echo $i;?>"  name="termsconditionID_<? echo $i;?>" style="width:50px" value="<? echo $row[csf('update_id')]?>"  />
                    </td>
                    <td> 
                    	<input type="button" id="increase_<? echo $i; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(<? echo $i; ?> )" />                    	<input type="button" id="decrease_<? echo $i; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $i; ?>);" />
                    </td>
				</tr>
				<?
			}
        }
        else
        {
			$k=1; ?>
                <tr id="settr_1" align="center">
                    <td>
                        <input type="text" id="sltd_<? echo $k;?>"   name="sltd_<? echo $k;?>" style="width:30px"   class="text_boxes_numeric" value="<? echo $k;?>"    /> 
                    </td>
                    <td>
                        <input type="text" id="termscondition_<? echo $k;?>"  onBlur="row_sequence(<? echo $k; ?>); "   name="termscondition_<? echo $k;?>" style="width:140px"   class="text_boxes_numeric" value="<? echo $max_ref;?>"    /> 
                        <input type="hidden" id="termsconditionID_<? echo $k;?>"   name="termsconditionID_<? echo $k;?>" style="width:50px" value=""  />
                    </td>
                    <td>
                        <input type="button" id="increase_<? echo $k; ?>" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr(<? echo $k; ?> )" />                	<input type="button" id="decrease_<? echo $k; ?>" style="width:30px" class="formbutton" value="-" onClick="javascript:fn_deletebreak_down_tr(<? echo $k; ?> );" /></td>
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
	exit();
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
		if($db_type==0) $insert_year_cond=" and  YEAR(job_insert_date)='$insert_date'";
		else if($db_type==2) $insert_year_cond=" and to_char(job_insert_date,'YYYY')='$insert_date'";
		$id=return_next_id( "id", "wo_order_entry_internal_ref", 1 ) ;
		$field_array="id,job_no,internal_ref,job_insert_date,insert_date";
		for ($i=1;$i<=$total_row;$i++)
		{
			$internal_ref="termscondition_".$i;
			$internal_cond="termscondition_".$i;
			if(is_duplicate_field( "internal_ref", "wo_order_entry_internal_ref", "internal_ref=".$$internal_cond."  $insert_year_cond" )==1)
			{
				echo "11**0"; 
				disconnect($con);die;			
			}
			if ($i!=1) $data_array .=",";
			$data_array .="(".$id.",".$txt_job_no.",".$$internal_ref.",".$job_insert_date.",'".$pc_date_time."')";
			$id=$id+1;
		}
		$rID=sql_insert("wo_order_entry_internal_ref",$field_array,$data_array,1);
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "0**".$job;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$job;
			}
		}
		
		else if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);
				echo "0**".$job;
			}
			else{
				oci_rollback($con);
				echo "10**".$job;
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
		if($db_type==0) $insert_year_cond=" and  YEAR(job_insert_date)='$insert_date'";
		else if($db_type==2) $insert_year_cond=" and to_char(job_insert_date,'YYYY')='$insert_date'";
		$data_array2=sql_select("select max(internal_ref) as internal_ref from   wo_order_entry_internal_ref");// quotation_id='$data'
		$max_ref=$data_array2[0][csf('internal_ref')];
		$id=return_next_id( "id", "wo_order_entry_internal_ref", 1 ) ;
		$field_array="id,job_no,internal_ref,job_insert_date,insert_date";
		$field_array_up="job_no*internal_ref*job_insert_date*insert_date";
		$add_comma=1;
		for ($i=1;$i<=$total_row;$i++)
		{
			$internal_ref="termscondition_".$i;
			$internal_cond=str_replace("'","",$$internal_ref);
			$update_id="termsconditionID_".$i;
			$mst_update_id=str_replace("'","",$$update_id);
			if($mst_update_id!="")
			{ 
				if(is_duplicate_field( "internal_ref", "wo_order_entry_internal_ref", "internal_ref=".$internal_cond." and id!=$mst_update_id $insert_year_cond " )==1)
				{
					echo "11**0"; 
					disconnect($con);die;			
				}
				$id_arr[]=str_replace("'",'',$$update_id);
				$data_array_up[str_replace("'",'',$$update_id)] =explode("*",("".$txt_job_no."*".$internal_cond."*".$job_insert_date."*'".$pc_date_time."' "));
			}
			else if($mst_update_id=="")
			{ 
				if(is_duplicate_field( "internal_ref", "wo_order_entry_internal_ref", "internal_ref=".$internal_cond."  $insert_year_cond" )==1)
				{
					echo "11**0"; 
					disconnect($con);die;			
				}
		
				if ($add_comma!=1) $data_array .=",";
				$data_array .="(".$id.",".$txt_job_no.",".$$internal_ref.",".$job_insert_date.",'".$pc_date_time."')";
				$id=$id+1;
				$add_comma++;
			}
		}
		$rID=execute_query(bulk_update_sql_statement("wo_order_entry_internal_ref", "id",$field_array_up,$data_array_up,$id_arr ));
		if($data_array!="")
		{
			$rID=sql_insert("wo_order_entry_internal_ref",$field_array,$data_array,1);
		}
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "1**".$job;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$job;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);
				echo "0**".$job;
			}
			else{
				oci_rollback($con);
				echo "10**".$job;
			}
		}
		disconnect($con);
		die;
	}  // Update End
}

if ($action=="job_popup")
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
        <table width="950" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
            <thead>
            	<tr>
                	<th colspan="10" align="center"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                </tr>
                <tr>
                    <th>Company Name</th>
                    <th>Buyer Name</th>
                    <th>Job No</th>
                    <th>Style Ref </th>
                    <th>Internal Ref</th>
                    <th>File No</th>
                    <th>Order No</th>
                    <th colspan="2">Ship Date Range</th>
                    <th><input type="checkbox" value="0" onClick="set_checkvalue()" id="chk_job_wo_po">Job Without PO</th>
                </tr>        
            </thead>
            <tr class="general">
                <td> 
                    <input type="hidden" id="selected_job">
                    <input type="hidden" id="garments_nature" value="<? echo $garments_nature; ?>">
					<? echo create_drop_down( "cbo_company_mst", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $cbo_company_name,"load_drop_down( 'order_matrix_update_controller', this.value, 'load_drop_down_buyer_pop', 'buyer_pop_td' );",'1' ); ?>
                </td>
        		<td id="buyer_pop_td"><? echo create_drop_down( "cbo_buyer_id", 150, $blank_array,'', 1, "-- Select Buyer --" ); ?></td>
                <td><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:50px"></td>
                <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:80px"></td>
                <td><input name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:70px"></td>
                <td><input name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:70px"></td>
                <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:80px"></td>
                <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date"></td>
                <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To Date"></td> 
                <td align="center">
                	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_id').value+'_'+document.getElementById('chk_job_wo_po').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('garments_nature').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('txt_internal_ref').value+'_'+document.getElementById('txt_file_no').value, 'create_po_search_list_view', 'search_div', 'order_matrix_update_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
        	</tr>
            <tr>
                <td height="40" align="center" colspan="10">
                <? 
                	//echo create_drop_down( "cbo_year_selection", 70, $year,"", 1, "-- Select --", date('Y'), "",0 );		
                	echo load_month_buttons(1);  ?>
                </td>
            </tr>
        </table>
        <div id="search_div"></div>
    </form>
    </div>
    </body>
    <script>
		load_drop_down('order_matrix_update_controller', <? echo $cbo_company_name; ?>, 'load_drop_down_buyer_pop', 'buyer_pop_td' )
		document.getElementById('cbo_buyer_id').value=<? echo $cbo_buyer_name; ?>
    </script>         
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
	
	if(str_replace("'","",$data[1])==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer="";
		}
		else
		{
			$buyer="";
		}
	}
	else
	{
		$buyer=" and a.buyer_name='$data[1]'";
	}
	
	//if ($data[1]!=0) $buyer=" and a.buyer_name='$data[1]'"; else $buyer="";//{ echo "Please Select Buyer First."; die; }
	
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
	if($data[8]==1)
	{
		if (str_replace("'","",$data[6])!="") $job_cond=" and a.job_no_prefix_num='$data[6]'  $year_cond";
		if (trim($data[9])!="") $order_cond=" and b.po_number='$data[9]'  "; //else  $order_cond=""; 
		if (trim($data[10])!="") $style_cond=" and a.style_ref_no='$data[10]'  "; //else  $style_cond=""; 
	}
	else if($data[8]==4 || $data[8]==0)
	{
		if (str_replace("'","",$data[6])!="") $job_cond=" and a.job_no_prefix_num like '%$data[6]%'  $year_cond"; //else  $job_cond=""; 
		if (trim($data[9])!="") $order_cond=" and b.po_number like '%$data[9]%'  ";
		if (trim($data[10])!="") $style_cond=" and a.style_ref_no like '%$data[10]%'  "; //else  $style_cond=""; 
	}
	else if($data[8]==2)
	{
		if (str_replace("'","",$data[6])!="") $job_cond=" and a.job_no_prefix_num like '$data[6]%'  $year_cond"; //else  $job_cond=""; 
		if (trim($data[9])!="") $order_cond=" and b.po_number like '$data[9]%'  ";
		if (trim($data[10])!="") $style_cond=" and a.style_ref_no like '$data[10]%'  "; //else  $style_cond=""; 
	}
	else if($data[8]==3)
	{
		if (str_replace("'","",$data[6])!="") $job_cond=" and a.job_no_prefix_num like '%$data[6]'  $year_cond"; //else  $job_cond=""; 
		if (trim($data[9])!="") $order_cond=" and b.po_number like '%$data[9]'  ";
		if (trim($data[10])!="") $style_cond=" and a.style_ref_no like '%$data[10]'  "; //else  $style_cond=""; 
	}
			
	$internal_ref = str_replace("'","",$data[11]);
	$file_no = str_replace("'","",$data[12]);
	if ($file_no=="") $file_no_cond=""; else $file_no_cond=" and b.file_no='".trim($file_no)."' "; 
	if ($internal_ref=="") $internal_ref_cond=""; else $internal_ref_cond=" and b.grouping='".trim($internal_ref)."' "; 
	
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	
	if ($data[2]==0)
	{
		$arr=array(2=>$buyer_arr,9=>$item_category);
		if($db_type==0)
		{
			$sql= "select a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no, a.job_quantity, a.order_repeat_no, b.po_number, b.po_quantity, b.shipment_date, a.garments_nature, b.grouping, b.file_no, DATEDIFF(pub_shipment_date,po_received_date) as date_diff, SUBSTRING_INDEX(a.`insert_date`, '-', 1) as year from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.garments_nature=$data[5] and a.status_active=1 and b.status_active=1 $shipment_date $company $buyer $job_cond $order_cond $style_cond $file_no_cond $internal_ref_cond $year_cond order by a.job_no DESC";
		}
		else if($db_type==2)
		{
			$sql= "select a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no, a.job_quantity, a.order_repeat_no, b.po_number, b.po_quantity, b.shipment_date, a.garments_nature, b.grouping, b.file_no, (pub_shipment_date - po_received_date) as  date_diff, to_char(a.insert_date,'YYYY') as year from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.garments_nature=$data[5] and a.status_active=1 and b.status_active=1 $shipment_date $company $buyer $job_cond $order_cond $style_cond $file_no_cond $internal_ref_cond $year_cond order by a.job_no DESC";
		}
		//echo $sql;
		echo  create_list_view("list_view", "Job No,Year,Buyer Name,Style Ref. No,Job Qty.,Repeat No,PO number,PO Qty.,Shipment Date,Gmts Nature,Ref no, File No,Lead time", "50,40,100,100,70,90,50,70,65,65,70,70,50","950","300",0, $sql , "js_set_value", "job_no", "", 1, "0,0,buyer_name,0,0,0,order_repeat_no,0,0,garments_nature,0,0,0", $arr , "job_no_prefix_num,year,buyer_name,style_ref_no,job_quantity,order_repeat_no,po_number,po_quantity,shipment_date,garments_nature,grouping,file_no,date_diff", "",'','0,0,0,0,1,0,0,1,3,0,0,0,0');
	}
	else
	{
		$arr=array (2=>$buyer_arr,4=>$item_category);
		if($db_type==0)
		{
			$sql= "select a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no, a.garments_nature, SUBSTRING_INDEX(a.`insert_date`, '-', 1) as year from wo_po_details_master a where a.job_no not in( select distinct job_no_mst from wo_po_break_down where status_active=1 and is_deleted=0 ) and a.garments_nature=$data[5] and a.status_active=1 and a.is_deleted=0 $company $buyer $job_cond $style_cond order by a.job_no DESC";
		}
		else if($db_type==2)
		{
			$sql= "select a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no, a.garments_nature, to_char(a.insert_date,'YYYY') as year from wo_po_details_master a where a.job_no not in( select distinct job_no_mst from wo_po_break_down where status_active=1 and is_deleted=0 ) and a.garments_nature=$data[5] and a.status_active=1 and a.is_deleted=0 $company $buyer $job_cond $style_cond order by a.job_no DESC";
		}
		echo  create_list_view("list_view", "Job No,Year,Company,Buyer Name,Style Ref. No,Gmts Nature", "90,60,120,100,100,90","950","200",0, $sql , "js_set_value", "job_no", "", 1, "0,0,company_name,buyer_name,0,garments_nature", $arr , "job_no_prefix_num,year,company_name,buyer_name,style_ref_no,garments_nature", "",'','0,0,0,0,0,0');
	}
	exit();
} 

if ($action=="populate_data_from_search_popup")
{
	$company_id=return_field_value("company_name","wo_po_details_master","job_no ='$data' and is_deleted=0 and status_active=1");
	
	$update_period_id=return_field_value("po_update_period","variable_order_tracking"," company_name ='$company_id' and variable_list=32 and is_deleted=0 and status_active=1");
	$po_current_date_data=return_field_value("po_current_date","variable_order_tracking"," company_name ='$company_id' and variable_list=33 and is_deleted=0 and status_active=1");
	
	if($update_period_id=="") $update_period_id=0; else $update_period_id=$update_period_id;
	if($po_current_date_data=="" || $po_current_date_data==2) $po_current_date_data=0; else $po_current_date_data=$po_current_date_data;
	
	/*echo "select id, garments_nature, job_no, job_no_prefix, job_no_prefix_num, company_name, buyer_name, location_name, style_ref_no, style_description, product_dept, product_code, pro_sub_dep, currency_id, agent_name, client_id, order_repeat_no, region, product_category, team_leader, dealing_marchant, packing, remarks, ship_mode, order_uom, set_break_down, gmts_item_id, total_set_qnty, set_smv, season_matrix, quotation_id, job_quantity, order_uom, avg_unit_price, currency_id, total_price, factory_marchant
 from wo_po_details_master where job_no='$data'";die;*/
	$data_array=sql_select("select id, garments_nature, job_no, job_no_prefix, job_no_prefix_num, company_name, buyer_name, location_name, style_ref_no, style_description, product_dept, product_code, pro_sub_dep, currency_id, agent_name, client_id, is_repeat, order_repeat_no, region, product_category, team_leader, dealing_marchant, packing, remarks, ship_mode, order_uom, set_break_down, gmts_item_id, total_set_qnty, set_smv, season_matrix, quotation_id, job_quantity, order_uom, avg_unit_price, currency_id, total_price, factory_marchant, qlty_label, style_owner from wo_po_details_master where job_no='$data' and is_deleted=0 and status_active=1");
	foreach ($data_array as $row)
	{
		echo "load_drop_down( 'requires/order_matrix_update_controller', '".$row[csf("team_leader")]."', 'cbo_dealing_merchant', 'div_marchant' ) ;\n";
		echo "load_drop_down( 'requires/order_matrix_update_controller', '".$row[csf("team_leader")]."', 'cbo_factory_merchant', 'div_marchant_factory' ) ;\n";
		
		//echo "document.getElementById('garments_nature').value = '".$row[csf("garments_nature")]."';\n";  
		echo "document.getElementById('txt_job_no').value = '".$row[csf("job_no")]."';\n";  
		echo "document.getElementById('cbo_location_name').value = '".$row[csf("location_name")]."';\n";  
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_name")]."';\n";  
		echo "document.getElementById('txt_style_ref').value = '".$row[csf("style_ref_no")]."';\n";  
		echo "document.getElementById('txt_style_description').value = '".$row[csf("style_description")]."';\n";  
		echo "document.getElementById('cbo_product_department').value = '".$row[csf("product_dept")]."';\n";
		echo "document.getElementById('txt_product_code').value = '".$row[csf("product_code")]."';\n";
		echo "document.getElementById('cbo_sub_dept').value = '".$row[csf("pro_sub_dep")]."';\n";
		echo "document.getElementById('cbo_currercy').value = '".$row[csf("currency_id")]."';\n";  
		echo "document.getElementById('cbo_agent').value = '".$row[csf("agent_name")]."';\n"; 
		echo "document.getElementById('cbo_client').value = '".$row[csf("client_id")]."';\n"; 
		echo "document.getElementById('po_update_period_maintain').value = '".$update_period_id."';\n"; 
		echo "document.getElementById('po_current_date_maintain').value = '".$po_current_date_data."';\n"; 
		$current_date=date('d-m-Y');
		if($po_current_date_data==1)
		{
			echo "document.getElementById('txt_po_received_date').value = '".$current_date."';\n";
			echo "$('#txt_po_received_date').attr('disabled',true);\n";   
		}
		else
		{
			echo "document.getElementById('txt_po_received_date').value = '';\n"; 
			echo "$('#txt_po_received_date').attr('disabled',false);\n";   
		}
		 
		if($row[csf("is_repeat")]==1)
		{
			echo "$('#chk_is_repeat').prop('checked', true);\n";
		}
		else
		{
			echo "$('#chk_is_repeat').prop('checked', false);\n";
		}
		
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
		echo "document.getElementById('txt_job_qty').value = '".number_format($row[csf("job_quantity")])."';\n";
		echo "document.getElementById('txt_avgUnit_price').value = '".$row[csf("avg_unit_price")]."';\n";
		echo "document.getElementById('txt_total_price').value = '".number_format($row[csf("total_price")],2)."';\n";
		echo "load_drop_down( 'requires/order_matrix_update_controller', '".$row[csf("buyer_name")]."', 'load_drop_down_season', 'season_td');\n";
		echo "document.getElementById('cbo_season_id').value = '".$row[csf("season_matrix")]."';\n";
		echo "document.getElementById('cbo_factory_merchant').value = '".$row[csf("factory_marchant")]."';\n";
		echo "document.getElementById('cbo_qltyLabel').value = '".$row[csf("qlty_label")]."';\n";
		echo "document.getElementById('cbo_style_owner').value = '".$row[csf("style_owner")]."';\n";
		echo "document.getElementById('update_id').value = '".$row[csf("job_no")]."';\n"; 
		echo "load_drop_down( 'requires/order_matrix_update_controller', '".$row[csf("gmts_item_id")]."', 'load_drop_gmts_item', 'itm_td') ;\n";
		
		echo "disable_enable_fields( 'cbo_company_name*cbo_location_name*cbo_buyer_name*cbo_product_department*cbo_sub_dept*cbo_currercy*cbo_season_id*cbo_region*txt_item_catgory*cbo_team_leader*cbo_dealing_merchant*cbo_ship_mode*cbo_order_uom*cbo_client*cbo_qltyLabel*cbo_style_owner',1);\n";
	}
	$sql_data=sql_select("select approved from wo_pre_cost_mst where job_no='$data' and is_deleted=0 and status_active=1");
	$isapproved=$sql_data[0][csf("approved")];
	if ($isapproved==1)
	{
		echo "document.getElementById('budgetApp_td').innerHTML = 'Pre Cost Approved, Any Change will be not allowed.';\n";
	}
	else
	{
		echo "document.getElementById('budgetApp_td').innerHTML = '';\n";
	}
	
	$projected_data_array=sql_select("select sum(CASE WHEN is_confirmed=2 THEN po_quantity ELSE 0 END) as job_projected_qty,
	sum(CASE WHEN is_confirmed=2 THEN (po_quantity*unit_price) ELSE 0 END) as job_projected_total,
	
	 sum(original_po_qty) as projected_qty, sum(original_po_qty*original_avg_price) as projected_amount, (sum(original_po_qty*original_avg_price)/sum(original_po_qty)) as projected_rate from wo_po_break_down where job_no_mst='$data' ");
	foreach ($projected_data_array as $row_val)
	{
		$job_projected_price=0;
		$job_projected_price=$row_val[csf("job_projected_total")]/$row_val[csf("job_projected_qty")];
	    echo "document.getElementById('txt_proj_qty').value = '".number_format($row_val[csf("job_projected_qty")])."';\n";
		echo "document.getElementById('txt_proj_avgUnit_price').value = '".number_format($job_projected_price,4)."';\n";
		echo "document.getElementById('txt_proj_total_price').value = '".number_format($row_val[csf("job_projected_total")],2)."';\n";
		echo "document.getElementById('txt_orginProj_qty').value = '".number_format($row_val[csf("projected_qty")])."';\n";
		echo "document.getElementById('txt_orginProj_total_price').value = '".number_format($row_val[csf("projected_rate")],4)."';\n";
		echo "document.getElementById('txt_orginProj_total_amt').value = '".number_format($row_val[csf("projected_amount")],2)."';\n";
	}
	exit();
	/*$internal=substr(return_library_autocomplete( "select distinct internal_ref as internal_ref  from wo_order_entry_internal_ref where job_no='$data' ", "internal_ref"  ), 0, -1);
	echo "internal( '".$internal."' ) ;\n";
	$sql_data=sql_select("select count(a.id) as id  from  wo_pre_cost_mst a, wo_pre_cost_fabric_cost_dtls b where a.job_no=b.job_no and a.job_no='$data' and a.is_deleted=0 and  a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.job_no");
	foreach($sql_data as $row)
	{
	 if($row[csf('id')]>0){
	 }
	}*/
}

if ($action=="load_drop_down_tna_task")
{
	$sql_task = "SELECT a.id, task_template_id, lead_time, material_source, total_task, tna_task_id, deadline, execution_days, notice_before, a.sequence_no, for_specific, b.task_catagory, b.task_name FROM  tna_task_template_details a, lib_tna_task b WHERE  a.is_deleted = 0 and a.status_active=1 and a.tna_task_id=b.id order by for_specific, lead_time";
	$result = sql_select( $sql_task ) ;
	$tna_template = array();
	$i=0; $k=0; $j=0;
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
	if($db_type==0) $short_name_cond="concat(a.sequence_no,'-',b.task_short_name)";
	else if($db_type==2) $short_name_cond="a.sequence_no || '-' || b.task_short_name";
	//echo "select a.id, $short_name_cond as task_short_name ,a.tna_task_id from  tna_task_template_details a,lib_tna_task b where a.tna_task_id=b.id and task_template_id='$template_id'  and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 order by a.sequence_no";
	echo create_drop_down( "cbo_tna_task", 80, "select a.id, $short_name_cond as task_short_name ,a.tna_task_id from  tna_task_template_details a,lib_tna_task b where a.tna_task_id=b.id and task_template_id='$template_id'  and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 order by a.sequence_no","id,task_short_name", 1, "-- Select --", "", "" );
	exit();
}

if ($action=="show_po_active_listview")
{
	$country_arr=return_library_array( "select id,country_name from lib_country", "id", "country_name");
	$country_code_arr=return_library_array( "select id, ultimate_country_code from lib_country_loc_mapping", "id", "ultimate_country_code");
	
	//echo $data;
	$sql= "select country_id, item_number_id, cutup, country_ship_date, code_id, ultimate_country_id, ul_country_code, pack_type, sum(order_quantity) as po_qty from wo_po_color_size_breakdown where po_break_down_id='$data' and is_deleted=0 and status_active=1 group by item_number_id, country_id, cutup, country_ship_date, code_id, ultimate_country_id, ul_country_code, pack_type order by country_ship_date";
	?>
	 <div>
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="790" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="80">Delivery Country</th>
                <th width="80">Code</th>
                <th width="80">Country</th>
                <th width="80">Country Code</th>
                <th width="120">Item</th>
                <th width="80">Cut-Off</th>
                <th width="80">Country Ship Date</th>
                <th width="40">Pack Type</th>
                <th>Country Qty</th>
            </thead>
     	</table>
     </div>
     <div style="width:790px; max-height:270px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="770" class="rpt_table" id="tbl_po_list">
			<?
			$i=1; $result = sql_select($sql);
            foreach( $result as $row )
            {
                if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="get_php_form_data('<? echo $row[csf("country_id")].'_'.$row[csf("item_number_id")].'_'.$row[csf("country_ship_date")].'_'.$data.'_'.$row[csf("code_id")].'_'.$row[csf("ultimate_country_id")].'_'.$row[csf("ul_country_code")].'_'.$row[csf("pack_type")]; ?>','populate_country_details_form_data','requires/order_matrix_update_controller');"> 
						<td width="30" align="center"><? echo $i; ?></td>
						<td width="80"><? echo $country_arr[$row[csf("country_id")]]; ?></td>
                        <td width="80"><? echo $country_code_arr[$row[csf("code_id")]]; ?></td>
                        <td width="80"><? echo $country_arr[$row[csf("ultimate_country_id")]]; ?></td>
                        <td width="80"><? echo $country_code_arr[$row[csf("ul_country_code")]]; ?></td>
                        <td width="120"><? echo $garments_item[$row[csf("item_number_id")]]; ?></td>		
						<td width="80"><? echo $cut_up_array[$row[csf("cutup")]];  ?></td>	
                        <td width="80"><? echo change_date_format($row[csf("country_ship_date")]);  ?></td>
                        <td width="40" align="center"><? echo $row[csf("pack_type")]; ?></td>
						<td align="right"><? echo  number_format($row[csf("po_qty")]); ?></td>
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

if($action=="populate_country_details_form_data")
{
	$ex_data=explode('_',$data);
	$ret_matrix_type=return_field_value("matrix_type"," wo_po_break_down","id='$ex_data[3]' and is_deleted=0 and status_active=1");
	//echo $ret_matrix_type; die;
	$prod_country_arr=array(); $prod_item_arr=array(); $prod_color_arr=array(); $prod_size_arr=array();
	$sql_check=sql_select("select a.order_id, a.country_id, b.gmt_item_id, a.color_id, a.size_id from ppl_cut_lay_size a, ppl_cut_lay_dtls b where b.id=a.dtls_id and  a.order_id='$ex_data[3]' group by a.country_id, a.order_id,b.gmt_item_id, a.color_id,a.size_id");
	//echo "select a.order_id, a.country_id, b.gmt_item_id, a.color_id, a.size_id from ppl_cut_lay_size a, ppl_cut_lay_dtls b where b.id=a.dtls_id and  a.order_id='$ex_data[3]' group by a.country_id, a.order_id,b.gmt_item_id, a.color_id,a.size_id";
	foreach($sql_check as $dts)
	{
		$prod_country_arr[$dts[csf("order_id")]][$dts[csf("country_id")]]=$dts[csf("country_id")]; 
		$prod_item_arr[$dts[csf("order_id")]][$dts[csf("country_id")]][$dts[csf("gmt_item_id")]]=$dts[csf("gmt_item_id")]; 
		$prod_color_arr[$dts[csf("order_id")]][$dts[csf("country_id")]][$dts[csf("gmt_item_id")]][$color_library[$dts[csf("color_id")]]]=$dts[csf("color_id")]; 
		$prod_size_arr[$dts[csf("order_id")]][$dts[csf("country_id")]][$dts[csf("gmt_item_id")]][$color_library[$dts[csf("color_id")]]][$size_library[$dts[csf("size_id")]]]=$dts[csf("size_id")]; 
	}
	unset($sql_check);
	
	$set_arr=array();
	$po_sql_data_arr=sql_select( "select a.order_uom, b.unit_price, a.set_break_down, a.total_set_qnty, b.id from  wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id='$ex_data[3]' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1");
	foreach($po_sql_data_arr as $sRow)
	{
		$set_arr[$sRow[csf("id")]]['uom']=$sRow[csf("order_uom")];
		$set_arr[$sRow[csf("id")]]['rate']=$sRow[csf("unit_price")];
		$set_arr[$sRow[csf("id")]]['set_break_down']=$sRow[csf("set_break_down")];
		$set_arr[$sRow[csf("id")]]['set_qty']=$sRow[csf("total_set_qnty")];
	}
	unset($po_sql_data_arr);
	if( $ex_data[0]=="" || $ex_data[0]==0) $country_id_cond=""; else  $country_id_cond="and country_id='$ex_data[0]'";
	if( $ex_data[4]!=0 || $ex_data[4]!='') $code_cond="and code_id='$ex_data[4]'"; else $code_cond="";
	if( $ex_data[5]!=0 || $ex_data[5]!='') $ultimate_country_cond="and ultimate_country_id='$ex_data[5]'"; else $ultimate_country_cond="";
	if( $ex_data[6]!=0 || $ex_data[6]!='') $country_code_cond="and ul_country_code='$ex_data[6]'"; else $country_code_cond="";
	if( $ex_data[7]!="") $pack_type_cond="and pack_type='$ex_data[7]'"; else $pack_type_cond="";
	$sql= "select id, country_id, code_id, ultimate_country_id, ul_country_code, item_number_id, cutup_date, cutup, country_ship_date, size_number_id, color_number_id, excess_cut_perc, article_number, order_rate, order_quantity, plan_cut_qnty, pack_qty, pcs_per_pack, pack_type, pcs_pack, assort_qty, solid_qty from wo_po_color_size_breakdown where po_break_down_id='$ex_data[3]' and item_number_id='$ex_data[1]' and country_ship_date='$ex_data[2]' $country_id_cond $code_cond $ultimate_country_cond $country_code_cond $pack_type_cond and is_deleted=0 and status_active=1 order by color_order, size_order";
	 
	$result = sql_select($sql); $color_array=array(); $size_array=array(); $all_data_arr=array(); $prod_check_color_arr=array(); $prod_check_size_arr=array();
	$m=1; $all_data="";
	foreach($result as $row)
	{
		if($m==1)
		{
			//echo $prod_country_arr[$ex_data[3]][$row[csf("country_id")]];
			if($row[csf("country_id")]=='') $row[csf("country_id")]=0;
			if($row[csf("ultimate_country_id")]=='') $row[csf("ultimate_country_id")]=0;
			echo "$('#cbo_gmtsItem_id').val('".$row[csf("item_number_id")]."');\n";
			
			if($prod_item_arr[$ex_data[3]][$row[csf("country_id")]][$row[csf("item_number_id")]]!="")
				echo "$('#cbo_gmtsItem_id').attr('disabled','disabled');\n";
			else 
				echo "$('#cbo_gmtsItem_id').removeAttr('disabled','disabled')".";\n";
			
			echo "$('#cbo_deliveryCountry_id').val('".$row[csf("country_id")]."');\n";
			echo "$('#hid_prev_country').val('".$row[csf("country_id")]."');\n";
			
			if($prod_country_arr[$ex_data[3]][$row[csf("country_id")]]!="")
				echo "$('#cbo_deliveryCountry_id').attr('disabled','disabled');\n";
			else 
				echo "$('#cbo_deliveryCountry_id').removeAttr('disabled','disabled')".";\n";
			
			echo "load_drop_down( 'requires/order_matrix_update_controller', ".$row[csf("country_id")].", 'load_dorp_down_code', 'code_td' );\n";
			//echo "load_drop_down( 'requires/order_matrix_update_controller', ".$row[csf("country_id")].", 'load_dorp_down_ultimate_country', 'ultimate_td' );\n";
			echo "load_drop_down( 'requires/order_matrix_update_controller', ".$row[csf("ultimate_country_id")].", 'load_dorp_down_countryCode', 'countryCode_td' );\n";
			echo "$('#cbo_code_id').val('".$row[csf("code_id")]."');\n";
			echo "$('#cbo_country_id').val('".$row[csf("ultimate_country_id")]."');\n";
			echo "$('#cbo_countryCode_id').val('".$row[csf("ul_country_code")]."');\n";
			echo "$('#txt_cutup_date').val('".change_date_format($row[csf("cutup_date")])."');\n";
			echo "$('#cbo_cutOff_id').val('".$row[csf("cutup")]."');\n";
			echo "$('#txt_countryShip_date').val('".change_date_format($row[csf("country_ship_date")])."');\n";
			echo "$('#txt_breakdownGrouping').val('".$row[csf("pack_type")]."');\n";
			echo "$('#txt_pcsQty').val('".$row[csf("pcs_pack")]."');\n";
			echo "$('#txt_is_update').val(1);\n";
			$m++;
		}
		
		$color_name=""; $size_name="";
		$color_name=$color_library[$row[csf("color_number_id")]]; 
		$size_name=$size_library[$row[csf("size_number_id")]];
		
		$prod_color=""; $prod_size=""; $prod_color_val=""; $prod_size_val=""; 
		$prod_color=$prod_color_arr[$ex_data[3]][$row[csf("country_id")]][$row[csf("item_number_id")]][$color_name];
		$prod_size=$prod_size_arr[$ex_data[3]][$row[csf("country_id")]][$row[csf("item_number_id")]][$color_name][$size_name]; 
		//echo $row[csf("item_number_id")].'='.$row[csf("country_id")];
		if($prod_color=="" || $prod_color==0) $prod_color_val=""; else  $prod_color_val=$prod_color;
		if($prod_size=="" || $prod_size==0) $prod_size_val=""; else  $prod_size_val=$prod_size;
		
		$prod_check_color_arr[$color_name]=$prod_color_val;
		$prod_check_size_arr[$size_name]=$prod_size_val;
		
		$color_array[$color_name]=$row[csf("color_number_id")];
		$size_array[$size_name]=$row[csf("size_number_id")];
		
		$all_data_arr[$color_name][$size_name]['po_qty']=$row[csf("order_quantity")];
		$all_data_arr[$color_name][$size_name]['rate']=$row[csf("order_rate")];
		$all_data_arr[$color_name][$size_name]['ex_per']=$row[csf("excess_cut_perc")];
		$all_data_arr[$color_name][$size_name]['art_no']=$row[csf("article_number")];
		$all_data_arr[$color_name][$size_name]['id']=$row[csf("id")];
		
		
		$item_id=$row[csf("item_number_id")];
		$avg_rate=$set_arr[$ex_data[3]]['rate'];
		$set_qnty=$set_arr[$ex_data[3]]['set_qty'];
		$set_breck_down=explode('__',str_replace("'","",$set_arr[$ex_data[3]]['set_break_down']));
		$item_ratio_arr=array();
		foreach($set_breck_down as $set_data)
		{
			$ex_set_data=explode('_',$set_data);
			$ex_item_id=$ex_set_data[0];
			$ex_item_ratio=$ex_set_data[1];
			$item_ratio_arr[$ex_item_id]=$ex_item_ratio;
		}
		
		$assort_data=0;
		$assort_data=$row[csf("assort_qty")].'!!'.$row[csf("solid_qty")];
		
		$order_total_amt=0; $color_size_rate=0; $color_size_poQty=0;
		$color_size_poQty=$row[csf("order_quantity")]/$item_ratio_arr[$item_id];
		$color_size_planCut=$row[csf("plan_cut_qnty")]/$item_ratio_arr[$item_id];
		$color_size_rate=number_format($row[csf("order_rate")],4)*$item_ratio_arr[$item_id];
		if($ret_matrix_type==4)
		{
			if ($all_data=="") $all_data=$row[csf("id")].'**'.$color_name.'**'.$size_name.'**'.$color_size_poQty.'**'.$color_size_rate.'**'.$row[csf("excess_cut_perc")].'**'.$row[csf("article_number")].'**'.$color_size_planCut.'**'.$row[csf("pack_qty")].'**'.$row[csf("pcs_per_pack")].'**'.$assort_data; else $all_data.='___'.$row[csf("id")].'**'.$color_name.'**'.$size_name.'**'.$color_size_poQty.'**'.$color_size_rate.'**'.$row[csf("excess_cut_perc")].'**'.$row[csf("article_number")].'**'.$color_size_planCut.'**'.$row[csf("pack_qty")].'**'.$row[csf("pcs_per_pack")].'**'.$assort_data;
		}
		else
		{
			if ($all_data=="") $all_data=$row[csf("id")].'**'.$color_name.'**'.$size_name.'**'.$color_size_poQty.'**'.$color_size_rate.'**'.$row[csf("excess_cut_perc")].'**'.$row[csf("article_number")].'**'.$color_size_planCut.'**'.$assort_data; else $all_data.='___'.$row[csf("id")].'**'.$color_name.'**'.$size_name.'**'.$color_size_poQty.'**'.$color_size_rate.'**'.$row[csf("excess_cut_perc")].'**'.$row[csf("article_number")].'**'.$color_size_planCut.'**'.$assort_data;	
		}
		//echo "append_color_size_row(1);\n";  
	}
	//print_r($prod_check_color_arr);
	echo "$('#color_size_break_down_all_data').val('".$all_data."');\n";
	
	if($ret_matrix_type==2 || $ret_matrix_type==3)
	{
		$ratio_data="";
		$sql_ratio= "select id, color_id, size_id, ratio_qty, ratio_rate from wo_po_ratio_breakdown where country_id='$ex_data[0]' and gmts_item_id='$ex_data[1]' and country_ship_date='$ex_data[2]' and po_id='$ex_data[3]' $code_cond $ultimate_country_cond $country_code_cond and is_deleted=0 and status_active=1";
		$sql_ratio_result = sql_select($sql_ratio);
		foreach($sql_ratio_result as $row)
		{
			$color_name=$color_library[$row[csf("color_id")]]; 
			$size_name=$size_library[$row[csf("size_id")]];
			if ($ratio_data=="") $ratio_data=$row[csf("id")].'**'.$color_name.'**'.$size_name.'**'.$row[csf("ratio_qty")].'**'.$row[csf("ratio_rate")];  else $ratio_data.='___'.$row[csf("id")].'**'.$color_name.'**'.$size_name.'**'.$row[csf("ratio_qty")].'**'.$row[csf("ratio_rate")];
		}
		
		echo "$('#color_size_ratio_data').val('".$ratio_data."');\n";
	}
	
	$x=1; $html="";
	foreach($size_array as $size=>$size_val)
	{
		$disable="";
		if($prod_check_size_arr[$size]!="") $disable="disabled"; else $disable="";
		$html=$html.'<tr align="center" id="trSize_'.$x.'"><td title="'.$size.'"><input type="text" name="txtSizeName[]" id="txtSizeName_'.$x.'" value="'.$size.'" class="text_boxes" style="width:80px" onKeyUp="append_color_size_row(2);" disabled /><input type="hidden" name="txtSizeId[]" id="txtSizeId_'.$x.'" value="'.$size_val.'" class="text_boxes" style="width:50px"/></td></tr>';
		$x++;
	}
	$html=$html.'<tr align="center" id="trSize_'.$x.'"><td><input type="text" name="txtSizeName[]" id="txtSizeName_'.$x.'" value="" class="text_boxes" style="width:80px"  onKeyUp="append_color_size_row(2);"/><input type="hidden" name="txtSizeId[]" id="txtSizeId_'.$x.'" value="" class="text_boxes" style="width:50px"/></td></tr>';
	echo "$('#td_size').html('".$html."')".";\n";
	
	$i=1; $table=''; 
	foreach($all_data_arr as $color=>$color_val)
	{
		$disable="";
		if($prod_check_color_arr[$color]!="") $disable="disabled"; else $disable="";
		
		$table=$table.'<tr align="center" id="trColor_'.$i.'"><td>'.$i.'</td><td><input type="text" name="txtColorName[]" id="txtColorName_'.$i.'" value="'.$color.'" class="text_boxes" style="width:80px" onKeyUp="append_color_size_row(1);" title="'.$color.'" disabled /><input type="hidden" name="txtColorId[]" id="txtColorId_'.$i.'" value="'.$color_val.'" class="text_boxes" style="width:50px"/></td></tr>';
		$i++;
	}
	$table=$table.'<tr align="center" id="trColor_'.$i.'"><td>'.$i.'</td><td><input type="text" name="txtColorName[]" id="txtColorName_'.$i.'" value="" title="" class="text_boxes" style="width:80px" onKeyUp="append_color_size_row(1);"/><input type="hidden" name="txtColorId[]" id="txtColorId_'.$i.'" value=""  class="text_boxes" style="width:50px"/></td></tr>';
	
	echo "$('#td_color').html('".$table."')".";\n";
	echo "$('#breakdown_div').html('')".";\n";
	echo "$('#breakdownratio_div').html('')".";\n";
	exit();
}

if($action=="set_ship_date")
{
	$data=explode("_",$data);
	$Date = change_date_format($data[0],"yyyy-mm-dd","-");
	if($data[1]==1) echo date('d-m-Y', strtotime($Date. ' - 1 days'));
	else if($data[1]==2) echo date('d-m-Y', strtotime($Date. ' + 1 days'));
	else if($data[1]==3) echo date('d-m-Y', strtotime($Date. ' + 3 days'));
	exit();
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

if ($action=="save_update_delete_mst")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$id=return_next_id("id", "wo_po_details_master", 1);
		//echo "select job_no_prefix,job_no_prefix_num from wo_po_details_master where company_name=$cbo_company_name and YEAR('Y',insert_date)=".date('Y',time())." order by job_no_prefix_num desc"; die; 
		if(str_replace("'","",$chk_is_repeat)==2)
		{
			if(str_replace("'","",$txt_repeat_no)=="")
			{
				if(str_replace("'","",$cbo_season_id)=="") $season_cond=""; else $season_cond="and season_matrix=$cbo_season_id";
				$sql_repeat_no=sql_select("select max(order_repeat_no) as repeat_no from wo_po_details_master where company_name=$cbo_company_name and buyer_name=$cbo_buyer_name and style_ref_no=$txt_style_ref $season_cond");
				
				if($sql_repeat_no[0][csf('repeat_no')]=="") $repeat_no=0;
				else $repeat_no=$sql_repeat_no[0][csf('repeat_no')]+1;
			}
			else
			{
				$repeat_no=str_replace("'","",$txt_repeat_no);
			}
		}
		else
		{
			$repeat_no=str_replace("'","",$txt_repeat_no);
		}
		
		if($db_type==0) $date_cond=" YEAR(insert_date)";
		else if($db_type==2) $date_cond="to_char(insert_date,'YYYY')";
			
		$new_job_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', '', date("Y",time()), 5, "select job_no_prefix,job_no_prefix_num from wo_po_details_master where company_name=$cbo_company_name and $date_cond=".date('Y',time())." order by id DESC", "job_no_prefix", "job_no_prefix_num" ));
		
		$field_array="id, garments_nature, quotation_id, job_no, job_no_prefix, job_no_prefix_num, company_name, buyer_name, location_name, style_ref_no, style_description, product_dept, product_code, pro_sub_dep, currency_id, agent_name, client_id, is_repeat, order_repeat_no, region, product_category, team_leader, dealing_marchant, packing, remarks, ship_mode, order_uom, gmts_item_id, set_break_down, total_set_qnty, set_smv, season_matrix, factory_marchant, qlty_label, style_owner, is_deleted, status_active, inserted_by, insert_date";
		
		$data_array="(".$id.",".$garments_nature.",".$txt_quotation_id.",'".$new_job_no[0]."','".$new_job_no[1]."','".$new_job_no[2]."',".$cbo_company_name.",".$cbo_buyer_name.",".$cbo_location_name.",".$txt_style_ref.",".$txt_style_description.",".$cbo_product_department.",".$txt_product_code.",".$cbo_sub_dept.",".$cbo_currercy.",".$cbo_agent.",".$cbo_client.",".$chk_is_repeat.",'".$repeat_no."',".$cbo_region.",".$txt_item_catgory.",".$cbo_team_leader.",".$cbo_dealing_merchant.",".$cbo_packing.",".$txt_remarks.",".$cbo_ship_mode.",".$cbo_order_uom.",".$item_id.",".$set_breck_down.",".$tot_set_qnty.",".$tot_smv_qnty.",".$cbo_season_id.",".$cbo_factory_merchant.",".$cbo_qltyLabel.",".$cbo_style_owner.",0,1,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		//echo "INSERT INTO wo_po_details_master (".$field_array.") VALUES ".$data_array; die;
		$field_array1="id, job_no, gmts_item_id, set_item_ratio, smv_pcs, smv_set, complexity, embelishment";
		$add_comma=0;
		$id1=return_next_id( "id", "wo_po_details_mas_set_details", 1) ;
		$set_breck_down_array=explode('__',str_replace("'",'',$set_breck_down));
		for($c=0;$c < count($set_breck_down_array);$c++)
		{
			$set_breck_down_arr=explode('_',$set_breck_down_array[$c]);
			
			if($set_breck_down_arr[2]==0 || $set_breck_down_arr[2]==''){
				echo "SMV**";
				disconnect($con);die;
			}
			
			if ($add_comma!=0) $data_array1 .=",";
			$data_array1 .="(".$id1.",'".$new_job_no[0]."','".$set_breck_down_arr[0]."','".$set_breck_down_arr[1]."','".$set_breck_down_arr[2]."','".$set_breck_down_arr[3]."','".$set_breck_down_arr[4]."','".$set_breck_down_arr[5]."')";
			$add_comma++;
			$id1=$id1+1;
		}
		$rID=sql_insert("wo_po_details_master",$field_array,$data_array,0);
		$rID1=sql_insert("wo_po_details_mas_set_details",$field_array1,$data_array1,1);
		
		if($db_type==0)
		{
			if($rID && $rID1)
			{
				mysql_query("COMMIT");  
				echo "0**".$new_job_no[0]."**".$rID."**".$repeat_no;
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
				echo "0**".$new_job_no[0]."**".$rID."**".$repeat_no;
			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}
		disconnect($con);
		die;
	}// Insert Here End------------------------------------------------------
	else if ($operation==1) // Update Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		if(str_replace("'","",$chk_is_repeat)==2)
		{
			if(str_replace("'","",$txt_repeat_no)=="")
			{
				if(str_replace("'","",$cbo_season_id)=="") $season_cond=""; else $season_cond="and season_matrix=$cbo_season_id";
				$sql_repeat_no=sql_select("select max(order_repeat_no) as repeat_no from wo_po_details_master where company_name=$cbo_company_name and buyer_name=$cbo_buyer_name and style_ref_no=$txt_style_ref $season_cond");
				if($sql_repeat_no[0][csf('repeat_no')]=="") $repeat_no=0;
				else $repeat_no=$sql_repeat_no[0][csf('repeat_no')]+1;
			}
			else
			{
				$repeat_no=str_replace("'","",$txt_repeat_no);
			}
		}
		else
		{
			$repeat_no=str_replace("'","",$txt_repeat_no);
		}
		
		$field_array="quotation_id*buyer_name*location_name*style_ref_no*style_description*product_dept*product_code*pro_sub_dep*currency_id*agent_name*client_id*is_repeat*order_repeat_no*region*product_category*team_leader*dealing_marchant*packing*remarks*ship_mode*order_uom*gmts_item_id*set_break_down*total_set_qnty*set_smv*season_matrix*factory_marchant*qlty_label*style_owner*updated_by*update_date";

		$data_array="".$txt_quotation_id."*".$cbo_buyer_name."*".$cbo_location_name."*".$txt_style_ref."*".$txt_style_description."*".$cbo_product_department."*".$txt_product_code."*".$cbo_sub_dept."*".$cbo_currercy."*".$cbo_agent."*".$cbo_client."*".$chk_is_repeat."*'".$repeat_no."'*".$cbo_region."*".$txt_item_catgory."*".$cbo_team_leader."*".$cbo_dealing_merchant."*".$cbo_packing."*".$txt_remarks."*".$cbo_ship_mode."*".$cbo_order_uom."*".$item_id."*".$set_breck_down."*".$tot_set_qnty."*".$tot_smv_qnty."*".$cbo_season_id."*".$cbo_factory_merchant."*".$cbo_qltyLabel."*".$cbo_style_owner."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		$field_array1="id, job_no, gmts_item_id, set_item_ratio, smv_pcs, smv_set, complexity, embelishment";
		$add_comma=0;
		$id1=return_next_id( "id", "wo_po_details_mas_set_details", 1 ) ;
		$set_breck_down_array=explode('__',str_replace("'",'',$set_breck_down));
		for($c=0;$c < count($set_breck_down_array);$c++)
		{
			$set_breck_down_arr=explode('_',$set_breck_down_array[$c]);
			if($set_breck_down_arr[2]==0 || $set_breck_down_arr[2]==''){
				echo "SMV**";
				disconnect($con);die;
			}
			if ($add_comma!=0) $data_array1 .=",";
			$data_array1 .="(".$id1.",".$txt_job_no.",'".$set_breck_down_arr[0]."','".$set_breck_down_arr[1]."','".$set_breck_down_arr[2]."','".$set_breck_down_arr[3]."','".$set_breck_down_arr[4]."','".$set_breck_down_arr[5]."')";
			$add_comma++;
			$id1=$id1+1;
		}
		$rID=sql_update("wo_po_details_master",$field_array,$data_array,"job_no","".$txt_job_no."",0);
		$rID1=execute_query("delete from wo_po_details_mas_set_details where job_no =".$txt_job_no."",0);
		$rID2=sql_insert("wo_po_details_mas_set_details",$field_array1,$data_array1,0);
		$rID3=execute_query( "update wo_booking_mst set is_apply_last_update=2 where job_no =".$txt_job_no." and booking_type=1 and is_short=2 ",1);
		$txt_job_no=str_replace("'","",$txt_job_no);
		if($db_type==0)
		{
			if($rID && $rID1 && $rID2 && $rID3 )
			{
				mysql_query("COMMIT");  
				echo "1**".$txt_job_no."**".$rID."**".$repeat_no;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID1 && $rID2 && $rID3 )
			{
				oci_commit($con); 
				echo "1**".$txt_job_no."**".$rID."**".$repeat_no;
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
	else if ($operation==2) // Delete Here----------------------------------------------------------  
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
	}// Delete Here End ----------------------------------------------------------
}

// Master Form End ***************************************** Master Form End******************************************
 
// Dtls Form ************************************************Dtls Form************************************************
if ($action=="order_listview")
{
	$data=explode("*",$data);
	$sql= "select id, po_number, po_received_date, pub_shipment_date, po_quantity, status_active from wo_po_break_down where is_deleted=0 and job_no_mst='$data[0]' order by id DESC"; 
	$data_array=sql_select($sql);
	?>
    <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="380">
        <thead>
            <th width="20">SL</th>
            <th width="80">Po No.</th>
            <th width="70">Po Qty</th>
            <th width="70">Ship Date</th>
            <th width="60">Lead Time</th>
            <th>Status</th>
        </thead>
     </table>
     <div style="width:400px; max-height:340px; overflow-y:scroll"> 
         <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="380" id="tbl_po_list">
            <tbody>
            <? 
            $i=1;
            foreach($data_array as $row)
            {  
               // if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($i%2==0) $bgcolor="#FFFFFF"; else $bgcolor="#E9F3FF";
                if($data[1]==$row[csf('id')]) $bgcolor="#33CC00"; else $bgcolor;
				$daysOnHand=0;
				if($db_type==2)
				{
					$today= change_date_format($row[csf('pub_shipment_date')],'','',1);	
					$daysOnHand = datediff("d",change_date_format($row[csf('po_received_date')],'','',1),$today);
				}
				else
				{
					$today= change_date_format($row[csf('pub_shipment_date')]);	
					$daysOnHand = datediff("d",change_date_format($row[csf('po_received_date')]),$today);
				}
				
                ?>
                <tr id="tr_<? echo $i; ?>" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="change_color_tr('<? echo $i; ?>','<? echo $bgcolor; ?>'); get_php_form_data('<? echo $row[csf('id')]; ?>', 'populate_order_details_form_data', 'requires/order_matrix_update_controller');"> 
                    <td width="20"><? echo $i; ?></td>
                    <td width="80"><div style="word-wrap:break-word; width:80px"><? echo $row[csf('po_number')]; ?></div></td>
                    <td width="70" align="right"><? echo number_format($row[csf('po_quantity')]); ?></td>
                    <td width="70"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
                    <td width="60" align="center"><? echo $daysOnHand; ?></td>
                    <td><? echo $row_status[$row[csf('status_active')]]; ?></td>
                </tr>
            <? 
            $i++; 
            } 
            ?>
            </tbody>
        </table>
    </div>
<?
exit();
}

if($action=="populate_order_details_form_data")
{
	$result= sql_select("select a.company_name from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and b.id='$data' and a.is_deleted=0 and a.status_active=1");
	$company_id=$result[0][csf('company_name')];
	//$chk_variable_setting=sql_select("select case variable_list when 32 THEN po_update_period when 33 then po_current_date from variable_order_tracking where company_name=1");
	$update_period_id=return_field_value("po_update_period","variable_order_tracking"," company_name ='$company_id' and variable_list=32 and is_deleted=0 and status_active=1");
	$po_current_date_data=return_field_value("po_current_date","variable_order_tracking"," company_name ='$company_id' and variable_list=33 and is_deleted=0 and status_active=1");
	if($update_period_id=="") $update_period_id=0; else $update_period_id=$update_period_id;
	if($po_current_date_data=="" || $po_current_date_data==2) $po_current_date_data=0; else $po_current_date_data=$po_current_date_data;
	//echo "select id, is_confirmed, po_number, po_received_date, pub_shipment_date, po_quantity, unit_price, po_total_price, excess_cut, plan_cut, country_name, details_remarks, delay_for, status_active, packing, grouping, projected_po_id, tna_task_from_upto, file_no, insert_date from wo_po_break_down where id='$data'"; die;
	$data_array=sql_select("select id, is_confirmed, po_number, po_received_date, pub_shipment_date, doc_sheet_qty, pack_price, no_of_carton, actual_po_no, matrix_type, round_type, unit_price, up_charge, po_total_price, excess_cut, plan_cut, country_name, details_remarks, delay_for, status_active, packing, grouping, projected_po_id, tna_task_from_upto, file_no, insert_date from wo_po_break_down where id='$data'");
	foreach ($data_array as $row)
	{ 
		$insert_date=explode(" ",$row[csf("insert_date")]);
		$current_date=date('d-m-Y h:i:s');
		$po_insert_date=change_date_format($insert_date[0],'dd-mm-yyyy','-').' '.$insert_date[1];
		$total_time=datediff(n,$po_insert_date,$current_date);
		$total_hour=floor($total_time/60);
		echo "document.getElementById('cbo_breakdown_type').value = '".$row[csf("matrix_type")]."';\n"; 
		echo "fnc_noof_carton(".$row[csf("matrix_type")].");\n"; 
		echo "document.getElementById('cbo_round_type').value = '".$row[csf("round_type")]."';\n";
		echo "document.getElementById('cbo_order_status').value = '".$row[csf("is_confirmed")]."';\n"; 
		//echo "$('#cbo_breakdown_type').attr('disabled','true')".";\n";
		echo "document.getElementById('txt_po_received_date').value = '".change_date_format($row[csf("po_received_date")], "dd-mm-yyyy", "-")."';\n";
		if($po_current_date_data==1 && $row[csf("is_confirmed")]==1) echo "$('#txt_po_received_date').attr('disabled',true);\n";   
		else echo "$('#txt_po_received_date').attr('disabled',false);\n";   
		
		//echo "$('#copy_id').removeAttr('disabled','disabled');\n";  
		
		echo "document.getElementById('txt_po_no').value = '".$row[csf("po_number")]."';\n";  
		echo "document.getElementById('txt_pub_shipment_date').value = '".change_date_format($row[csf("pub_shipment_date")], "dd-mm-yyyy", "-")."';\n";  
		 
		echo "document.getElementById('txt_po_datedif_hour').value = '".$total_hour."';\n";  
		echo "document.getElementById('txt_user_id').value = '".$user_id."';\n";  
		echo "document.getElementById('txt_po_remarks').value = '".$row[csf("details_remarks")]."';\n";
		if($row[csf("matrix_type")]==4)
		{
			if($row[csf("pack_price")]!=0)
			{
				echo "document.getElementById('txt_avg_price').value = '".number_format($row[csf("pack_price")],4,'.','')."';\n";
			}
			else
			{
				 echo "document.getElementById('txt_avg_price').value = '".number_format($row[csf("unit_price")],4,'.','')."';\n";
			}
			echo "document.getElementById('txt_docSheetQty').value = '".number_format($row[csf("unit_price")],4,'.','')."';\n";
		}
		else
		{
			echo "document.getElementById('txt_avg_price').value = '".number_format($row[csf("unit_price")],4,'.','')."';\n";
			echo "document.getElementById('txt_docSheetQty').value = '".number_format($row[csf("doc_sheet_qty")],0,'.','')."';\n";
		}
		//echo "$('#txt_avg_price').attr('disabled',true);\n";
		echo "document.getElementById('txt_upCharge').value = '".$row[csf("up_charge")]."';\n";    
		//echo "document.getElementById('txt_docSheetQty').value = '".$row[csf("doc_sheet_qty")]."';\n"; 
		echo "document.getElementById('txt_noOf_carton').value = '".$row[csf("no_of_carton")]."';\n"; 
		
		echo "document.getElementById('update_id_details').value = '".$row[csf("id")]."';\n"; 
		echo "set_multiselect('cbo_delay_for','0','1','".($row[csf("delay_for")])."','0');\n"; 
		echo "set_tna_task();\n"; 

		echo "document.getElementById('cbo_packing_po_level').value = '".$row[csf("packing")]."';\n";  
		echo "document.getElementById('txt_grouping').value = '".$row[csf("grouping")]."';\n"; 
		echo "document.getElementById('cbo_projected_po').value = '".$row[csf("projected_po_id")]."';\n";  
		echo "document.getElementById('cbo_tna_task').value = '".$row[csf("tna_task_from_upto")]."';\n"; 
		echo "document.getElementById('txt_file_no').value = '".$row[csf("file_no")]."';\n";
		echo "document.getElementById('cbo_status').value = '".$row[csf("status_active")]."';\n"; 
		
		
		echo "disable_enable_fields( 'cbo_breakdown_type*cbo_order_status*copy_id*txt_avg_price',1);\n";
		
		echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_order_entry_details',2);\n"; 
		echo "show_list_view('".$row[csf("id")]."','show_po_active_listview','country_po_list_view','requires/order_matrix_update_controller','');\n";  
	}
	exit();
}

if ($action=="save_update_delete_dtls")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$packing ="";
	if(str_replace("'","",$cbo_packing_po_level)==0) $packing = str_replace("'","",$cbo_packing);
	else $packing = str_replace("'","",$cbo_packing_po_level);
	
	/*if (file_exists('dateretriction.php'))
	{
		require('dateretriction.php');
	}*/
	
	if ($operation==0)  // Insert Here
	{	
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		if (str_replace("'","",$copy_id)==2)
		{
			$data_shipDate_vari=""; $flag=1;
			$sql_shipDate_vari=sql_select("select duplicate_ship_date from variable_order_tracking where company_name=$cbo_company_name and variable_list=29");
			if($sql_shipDate_vari[0][csf("duplicate_ship_date")]==1) $txt_pub_shipment_date_cond="and pub_shipment_date=$txt_pub_shipment_date";	
			else $txt_pub_shipment_date_cond="";
				
			$image_mdt=return_field_value("image_mandatory", "variable_order_tracking", "company_name=$cbo_company_name and variable_list=30");
			$image=return_field_value("id", "common_photo_library", "master_tble_id=$update_id and form_name='knit_order_entry' and file_type=1");
			
			if($image_mdt==1 && $image=="")
			{
				echo "24**0"; disconnect($con);die;
			}
	
			if (str_replace("'","",$update_id_details)=="")
			{
				if (is_duplicate_field( "po_number", "wo_po_break_down", "po_number=$txt_po_no and job_no_mst=$update_id $txt_pub_shipment_date_cond and is_deleted=0" ) == 1)
				{
					echo "11**0"; 
					disconnect($con);die;
				}
			}
			else
			{
				if (is_duplicate_field( "po_number", "wo_po_break_down", "po_number=$txt_po_no and job_no_mst=$update_id  $txt_pub_shipment_date_cond and id!=$update_id_details and is_deleted=0" )== 1)
				{
					echo "11**0"; 
					disconnect($con);die;
				}
			}
			
			$id=return_next_id("id", "wo_po_break_down", 1);
			$breakdown_type=str_replace("'","",$cbo_breakdown_type);
			if($breakdown_type==4)
			{
				$docSheet_col="pack_price";
				$docSheet_field=$txt_avg_price;
				
				$avg_rate_pack=$txt_docSheetQty;
				
			}
			else
			{
				$docSheet_col="doc_sheet_qty";
				$docSheet_field=$txt_docSheetQty;
				$avg_rate_pack=$txt_avg_price;
			}
			if (str_replace("'","",$update_id_details)=="")
			{
				if(str_replace("'","",$cbo_order_status)==2)
				{
					$field_array="id, job_no_mst, is_confirmed, po_number, po_received_date, pub_shipment_date, shipment_date, factory_received_date, unit_price, up_charge, original_avg_price, $docSheet_col, no_of_carton, details_remarks, delay_for, packing, grouping, projected_po_id, matrix_type, round_type, tna_task_from_upto, t_year, t_month, file_no, inserted_by, insert_date, is_deleted, status_active";
					$data_array="(".$id.",".$update_id.",".$cbo_order_status.",".$txt_po_no.",".$txt_po_received_date.",".$txt_pub_shipment_date.",".$txt_pub_shipment_date.",".$txt_po_received_date.",".$avg_rate_pack.",".$txt_upCharge.",".$txt_avg_price.",".$docSheet_field.",".$txt_noOf_carton.",".$txt_po_remarks.",".$cbo_delay_for.",'".$packing."',".$txt_grouping.",".$cbo_projected_po.",".$cbo_breakdown_type.",".$cbo_round_type.",".$cbo_tna_task.",'".date("Y",strtotime(str_replace("'","",$txt_pub_shipment_date)))."','".date("m",strtotime(str_replace("'","",$txt_pub_shipment_date)))."',".$txt_file_no.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',0,1)";
				}
				else
				{
					$field_array="id, job_no_mst, is_confirmed, po_number, po_received_date, pub_shipment_date, shipment_date, factory_received_date, unit_price, up_charge, $docSheet_col, no_of_carton, details_remarks, delay_for, packing, grouping, projected_po_id, matrix_type, round_type, tna_task_from_upto, t_year, t_month, file_no, inserted_by, insert_date, is_deleted, status_active";
					$data_array="(".$id.",".$update_id.",".$cbo_order_status.",".$txt_po_no.",".$txt_po_received_date.",".$txt_pub_shipment_date.",".$txt_pub_shipment_date.",".$txt_po_received_date.",".$avg_rate_pack.",".$txt_upCharge.",".$docSheet_field.",".$txt_noOf_carton.",".$txt_po_remarks.",".$cbo_delay_for.",'".$packing."',".$txt_grouping.",".$cbo_projected_po.",".$cbo_breakdown_type.",".$cbo_round_type.",".$cbo_tna_task.",'".date("Y",strtotime(str_replace("'","",$txt_pub_shipment_date)))."','".date("m",strtotime(str_replace("'","",$txt_pub_shipment_date)))."',".$txt_file_no.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',0,1)";

				}
				$po_id="'".$id."'";
			}
			else
			{
				$field_array="is_confirmed*po_number*po_received_date*pub_shipment_date*shipment_date*factory_received_date*unit_price*up_charge*".$docSheet_col."*no_of_carton*details_remarks*delay_for*packing*grouping*projected_po_id*matrix_type*round_type*tna_task_from_upto*t_year*t_month*file_no*updated_by*update_date";
				
				$data_array="".$cbo_order_status."*".$txt_po_no."*".$txt_po_received_date."*".$txt_pub_shipment_date."*".$txt_pub_shipment_date."*".$txt_po_received_date."*".$avg_rate_pack."*".$txt_upCharge."*".$docSheet_field."*".$txt_noOf_carton."*".$txt_po_remarks."*".$cbo_delay_for."*'".$packing."'*".$txt_grouping."*".$cbo_projected_po."*".$cbo_breakdown_type."*".$cbo_round_type."*".$cbo_tna_task."*'".date("Y",strtotime(str_replace("'","",$txt_pub_shipment_date)))."'*'".date("m",strtotime(str_replace("'","",$txt_pub_shipment_date)))."'*".$txt_file_no."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
				$po_id=$update_id_details;
			}
			//echo "INSERT INTO wo_po_break_down (".$field_array.") VALUES ".$data_array; die;
				
			//====================================================================================
			if(str_replace("'","",$txt_breakdownGrouping)!="") $pack_type_cond=" and pack_type=$txt_breakdownGrouping"; else $pack_type_cond="";
			
			if (is_duplicate_field( "country_id", "wo_po_color_size_breakdown", "country_id=$cbo_deliveryCountry_id and po_break_down_id=$po_id and item_number_id=$cbo_gmtsItem_id and is_deleted=0 $pack_type_cond" )== 1)
			{
				echo "11**0"; 
				disconnect($con);die;
			}
			$id1=return_next_id( "id", "wo_po_color_size_breakdown", 1) ;
			$breakdown_type=str_replace("'","",$cbo_breakdown_type);
			
			if($breakdown_type==4)
			{
				$field_array1="id, po_break_down_id, job_no_mst, color_mst_id, size_mst_id, item_mst_id, item_number_id, country_id, code_id, ultimate_country_id, ul_country_code, cutup_date, cutup, country_ship_date, color_number_id, size_number_id, order_quantity, order_rate, excess_cut_perc, article_number, order_total, plan_cut_qnty, pack_qty, pcs_per_pack, pack_type, pcs_pack, assort_qty, solid_qty, inserted_by, insert_date, is_deleted, status_active";
			}
			else
			{
				$field_array1="id, po_break_down_id, job_no_mst, color_mst_id, size_mst_id, item_mst_id, item_number_id, country_id, code_id, ultimate_country_id, ul_country_code, cutup_date, cutup, country_ship_date, color_number_id, size_number_id, order_quantity, order_rate, excess_cut_perc, article_number, order_total, plan_cut_qnty, assort_qty, solid_qty, inserted_by, insert_date, is_deleted, status_active";
			}
			
			//echo $cbo_gmtsItem_id.'kausar';die;$excess_cut_perc=($data[1]*$row[csf('order_quantity')]/100)+$row[csf('order_quantity')];
			$item_id=str_replace("'","",$cbo_gmtsItem_id);
			$avg_rate=str_replace("'","",$txt_avg_price);
			$set_qnty=str_replace("'","",$tot_set_qnty);
			$set_breck_down=explode('__',str_replace("'","",$set_breck_down));
			$item_ratio_arr=array();
			foreach($set_breck_down as $set_data)
			{
				$ex_set_data=explode('_',$set_data);
				$ex_item_id=$ex_set_data[0];
				$ex_item_ratio=$ex_set_data[1];
				$item_ratio_arr[$ex_item_id]=$ex_item_ratio;
			}
			$add_comma=0; $data_array1="";  $ratio_comma=0; $data_array_ratio=""; 
			$size_id_arr=array();
			for($k=1; $k<=$size_table; $k++)
			{
				$txtSizeName="txtSizeName_".$k;
				//$size_id_val = return_id_lib_common( $$txtSizeName, $size_library, "lib_size", "id,size_name");
				if(str_replace("'","",$$txtSizeName)!="")
				{
					if (!in_array(str_replace("'","",$$txtSizeName),$new_array_size))
					{
						$size_id_val = return_id_lib_common( str_replace("'","",$$txtSizeName), $size_library, "lib_size", "id,size_name","401");
						$new_array_size[$size_id_val]=str_replace("'","",$$txtSizeName);
					}
					else $size_id_val =  array_search(str_replace("'","",$$txtSizeName), $new_array_size);
				}
				else $size_id_val=0;

				$size_id_arr[$k]=$size_id_val;
			}
			
			//print_r($size_id_arr); die;
			if($breakdown_type==1)
			{
				for($i=1; $i<=$color_table; $i++)
				{
					$txtColorName="txtColorName_".$i;
					if($$txtColorName!="")
					{
						//$color_id = return_id_lib_common( str_replace("'","",$$txtColorName), $color_library, "lib_color", "id,color_name");
						if(str_replace("'","",$$txtColorName)!="")
						{
							if (!in_array(str_replace("'","",$$txtColorName),$new_array_color))
							{
								$color_id = return_id_lib_common( str_replace("'","",$$txtColorName), $color_library, "lib_color", "id,color_name","401");
								$new_array_color[$color_id]=str_replace("'","",$$txtColorName);
							}
							else $color_id =  array_search(str_replace("'","",$$txtColorName), $new_array_color);
						}
						else $color_id=0;
						 
						for($m=1; $m<=$size_table; $m++)
						{
							$txtSizeName="txtSizeName_".$m;
							if($$txtSizeName!="")
							{
								$size_id = $size_id_arr[$m];
								//$txt_colorSizeQty="";
								$txt_colorSizeQty="txt_colorSizeQty_".$i.'_'.$m;
								$txt_colorSizeRate="txt_colorSizeRate_".$i.'_'.$m;
								$txt_colorSizeExCut="txt_colorSizeExCut_".$i.'_'.$m;
								$txt_colorSizePLanCut="txt_colorSizePLanCut_".$i.'_'.$m;
								$txt_colorSizeArticleNo="txt_colorSizeArticleNo_".$i.'_'.$m;
								
								$txt_assortQty="txt_assortQty_".$i.'_'.$m;
								$ex_assort=explode('!!',str_replace("'","",$$txt_assortQty));
								$assort_qty=0; $solid_qty=0;
								$assort_qty=$ex_assort[0];
								$solid_qty=$ex_assort[1];
								
								$order_total_amt=0; $plancut_Qty=0; $color_size_poQty=0;
								$color_size_poQty=str_replace("'","",$$txt_colorSizeQty)*$item_ratio_arr[$item_id];
								$color_size_rate=str_replace("'","",$$txt_colorSizeRate)/$item_ratio_arr[$item_id];
								$order_total_amt=$color_size_poQty*$color_size_rate;//str_replace("'","",$$txt_colorSizeRate);
								$color_size_planCutQty=str_replace("'","",$$txt_colorSizePLanCut)*$item_ratio_arr[$item_id];
								//print_r($$txt_colorSizeQty).'</br>';
								if($color_size_poQty>0)
								{
									if ($add_comma!=0) $data_array1 .=",";
									$data_array1 .="(".$id1.",".$po_id.",".$update_id.",'".$color_mst_id."','".$size_mst_id."','".$item_mst_id."',".$cbo_gmtsItem_id.",".$cbo_deliveryCountry_id.",".$cbo_code_id.",".$cbo_country_id.",".$cbo_countryCode_id.",".$txt_cutup_date.",".$cbo_cutOff_id.",".$txt_countryShip_date.",".$color_id.",'".$size_id."','".$color_size_poQty."','".$color_size_rate."',".$$txt_colorSizeExCut.",".$$txt_colorSizeArticleNo.",'".$order_total_amt."','".$color_size_planCutQty."','".$assort_qty."','".$solid_qty."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',0,".$cbo_status.")";
									
									$id1=$id1+1;
									$add_comma++;
								}
							}
							//echo $data_array1;
						}
					}
				}
			}
			else if($breakdown_type==4)
			{
				for($i=1; $i<=$color_table; $i++)
				{
					$txtColorName="txtColorName_".$i;
					if($$txtColorName!="")
					{
						//$color_id = return_id_lib_common( str_replace("'","",$$txtColorName), $color_library, "lib_color", "id,color_name"); 
						if(str_replace("'","",$$txtColorName)!="")
						{
							if (!in_array(str_replace("'","",$$txtColorName),$new_array_color))
							{
								$color_id = return_id_lib_common( str_replace("'","",$$txtColorName), $color_library, "lib_color", "id,color_name","401");
								$new_array_color[$color_id]=str_replace("'","",$$txtColorName);
							}
							else $color_id =  array_search(str_replace("'","",$$txtColorName), $new_array_color);
						}
						else $color_id=0;
						 
						for($m=1; $m<=$size_table; $m++)
						{
							$txtSizeName="txtSizeName_".$m;
							if($$txtSizeName!="")
							{
								$size_id = $size_id_arr[$m];
								//$txt_colorSizeQty="";
								$txt_colorSizePackQty="txt_colorSizePackQty_".$i.'_'.$m;
								$txt_colorSizePcsQty="txt_colorSizePcsQty_".$i.'_'.$m;
								$txt_colorSizeQty="txt_colorSizeQty_".$i.'_'.$m;
								$txt_colorSizeRate="txt_colorSizeRate_".$i.'_'.$m;
								$txt_colorSizeExCut="txt_colorSizeExCut_".$i.'_'.$m;
								$txt_colorSizePLanCut="txt_colorSizePLanCut_".$i.'_'.$m;
								$txt_colorSizeArticleNo="txt_colorSizeArticleNo_".$i.'_'.$m;
								
								$txt_assortQty="txt_assortQty_".$i.'_'.$m;
								$ex_assort=explode('!!',str_replace("'","",$$txt_assortQty));
								$assort_qty=0; $solid_qty=0;
								$assort_qty=$ex_assort[0];
								$solid_qty=$ex_assort[1];
								
								$order_total_amt=0; $plancut_Qty=0; $color_size_poQty=0;
								$color_size_poQty=str_replace("'","",$$txt_colorSizeQty)*$item_ratio_arr[$item_id];
								$color_size_rate=str_replace("'","",$$txt_colorSizeRate)/$item_ratio_arr[$item_id];
								$order_total_amt=$color_size_poQty*$color_size_rate;//str_replace("'","",$$txt_colorSizeRate);
								$color_size_planCutQty=str_replace("'","",$$txt_colorSizePLanCut)*$item_ratio_arr[$item_id];
								//print_r($$txt_colorSizeQty).'</br>';
								if($color_size_poQty>0)
								{
									if ($add_comma!=0) $data_array1 .=",";
									$data_array1 .="(".$id1.",".$po_id.",".$update_id.",'".$color_mst_id."','".$size_mst_id."','".$item_mst_id."',".$cbo_gmtsItem_id.",".$cbo_deliveryCountry_id.",".$cbo_code_id.",".$cbo_country_id.",".$cbo_countryCode_id.",".$txt_cutup_date.",".$cbo_cutOff_id.",".$txt_countryShip_date.",".$color_id.",'".$size_id."','".$color_size_poQty."','".$color_size_rate."',".$$txt_colorSizeExCut.",".$$txt_colorSizeArticleNo.",'".$order_total_amt."','".$color_size_planCutQty."',".$$txt_colorSizePackQty.",".$$txt_colorSizePcsQty.",".$txt_breakdownGrouping.",".$txt_pcsQty.",'".$assort_qty."','".$solid_qty."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',0,".$cbo_status.")";
									
									$id1=$id1+1;
									$add_comma++;
								}
							}
							//echo $data_array1;
						}
					}
				}
			}
			else
			{
				$idratio=return_next_id( "id", "wo_po_ratio_breakdown", 1) ;
				$field_array_ratio="id, job_id, po_id, country_id, gmts_item_id, code_id, ultimate_country_id, ul_country_code, country_ship_date, color_id, size_id, ratio_qty, ratio_rate, inserted_by, insert_date";
				for($i=1; $i<=$color_table; $i++)
				{
					$txtColorName="txtColorName_".$i;
					if($$txtColorName!="")
					{
						//$color_id = return_id_lib_common( str_replace("'","",$$txtColorName), $color_library, "lib_color", "id,color_name");
						if(str_replace("'","",$$txtColorName)!="")
						{
							if (!in_array(str_replace("'","",$$txtColorName),$new_array_color))
							{
								$color_id = return_id_lib_common( str_replace("'","",$$txtColorName), $color_library, "lib_color", "id,color_name","401");
								$new_array_color[$color_id]=str_replace("'","",$$txtColorName);
							}
							else $color_id =  array_search(str_replace("'","",$$txtColorName), $new_array_color);
						}
						else $color_id=0;
						  
						for($m=1; $m<=$size_table; $m++)
						{
							$txtSizeName="txtSizeName_".$m;
							if($$txtSizeName!="")
							{
								$size_id = $size_id_arr[$m];
								//$txt_colorSizeQty="";
								$txt_colorSizeQty="txt_colorSizeQty_".$i.'_'.$m;
								$txt_colorSizeRate="txt_colorSizeRate_".$i.'_'.$m;
								$txt_colorSizeExCut="txt_colorSizeExCut_".$i.'_'.$m;
								$txt_colorSizePLanCut="txt_colorSizePLanCut_".$i.'_'.$m;
								$txt_colorSizeArticleNo="txt_colorSizeArticleNo_".$i.'_'.$m;
								
								$txt_assortQty="txt_assortQty_".$i.'_'.$m;
								$ex_assort=explode('!!',str_replace("'","",$$txt_assortQty));
								$assort_qty=0; $solid_qty=0;
								$assort_qty=$ex_assort[0];
								$solid_qty=$ex_assort[1];
								
								$txt_colorSizeRatioQty="txt_colorSizeRatioQty_".$i.'_'.$m;
								$txt_colorSizeRatioRate="txt_colorSizeRatioRate_".$i.'_'.$m;
								$txt_colorSizeRatioId="txt_colorSizeRatioId_".$i.'_'.$m;
								
								$order_total_amt=0; $plancut_Qty=0; $color_size_poQty=0;
								$color_size_poQty=str_replace("'","",$$txt_colorSizeQty)*$item_ratio_arr[$item_id];
								$color_size_rate=str_replace("'","",$$txt_colorSizeRate)/$item_ratio_arr[$item_id];
								$order_total_amt=$color_size_poQty*$color_size_rate;//str_replace("'","",$$txt_colorSizeQty)*str_replace("'","",$$txt_colorSizeRate);
								$color_size_planCutQty=str_replace("'","",$$txt_colorSizePLanCut)*$item_ratio_arr[$item_id];
								//print_r($$txt_colorSizeQty).'</br>';
								if($color_size_poQty>0)
								{
									if ($add_comma!=0) $data_array1 .=",";
									$data_array1 .="(".$id1.",".$po_id.",".$update_id.",'".$color_mst_id."','".$size_mst_id."','".$item_mst_id."',".$cbo_gmtsItem_id.",".$cbo_deliveryCountry_id.",".$cbo_code_id.",".$cbo_country_id.",".$cbo_countryCode_id.",".$txt_cutup_date.",".$cbo_cutOff_id.",".$txt_countryShip_date.",".$color_id.",'".$size_id."','".$color_size_poQty."','".$color_size_rate."',".$$txt_colorSizeExCut.",".$$txt_colorSizeArticleNo.",'".$order_total_amt."','".$color_size_planCutQty."','".$assort_qty."','".$solid_qty."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',0,".$cbo_status.")";
									
									$id1=$id1+1;
									$add_comma++;
									
									if ($ratio_comma!=0) $data_array_ratio .=",";
										$data_array_ratio .="(".$idratio.",".$po_id.",".$po_id.",".$cbo_deliveryCountry_id.",".$cbo_gmtsItem_id.",".$cbo_code_id.",".$cbo_country_id.",".$cbo_countryCode_id.",".$txt_countryShip_date.",'".$color_id."','".$size_id."',".$$txt_colorSizeRatioQty.",".$$txt_colorSizeRatioRate.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
									$idratio=$idratio+1;
									$ratio_comma++;
								}
							}
							//echo $data_array1;
						}
					}
				}
			}
			
			
			//echo "10**INSERT INTO wo_po_color_size_breakdown (".$field_array1.") VALUES ".$data_array1; die;
			if (str_replace("'","",$update_id_details)=="")
			{
				$rID=sql_insert("wo_po_break_down",$field_array,$data_array,0);	
				if($rID) $flag=1; else $flag=0;
			}	
			else
			{
				$rID=sql_update("wo_po_break_down",$field_array,$data_array,"id","".$update_id_details."",0);
				if($rID) $flag=1; else $flag=0;
			}
			
			$rID1=sql_insert("wo_po_color_size_breakdown",$field_array1,$data_array1,0);
			if($rID1) $flag=1; else $flag=0;
			
			if($breakdown_type==2 || $breakdown_type==3)
			{
				$rIDratio=sql_insert("wo_po_ratio_breakdown",$field_array_ratio,$data_array_ratio,0);
				if($rIDratio) $flag=1; else $flag=0;
			}
			//============================================================================================
			if($db_type==0) $sequNullCheck="IFNULL(sequ,0)";
			 else if($db_type==2) $sequNullCheck="nvl(sequ,0)";
			$sam=1;
			$id_sm=return_next_id( "id", "wo_po_sample_approval_info", 1 ) ;
			//$cbosampletype=return_field_value( 'id', 'lib_sample', 'sample_type=2 and status_active=1 and is_deleted=0' );
			$sample_tag=sql_select("select tag_sample,sequ from lib_buyer_tag_sample where $sequNullCheck!=0 and buyer_id=$cbo_buyer_name order by sequ");
			$field_array_sm="id,job_no_mst,po_break_down_id,color_number_id,sample_type_id,status_active,is_deleted"; 
			//echo "select a.id as po_id, b.color_number_id, min(b.id) as color_size_table_id from  wo_po_break_down a, wo_po_color_size_breakdown b where a.job_no_mst=b.job_no_mst and a.job_no_mst=$update_id and a.id=b.po_break_down_id and b.po_break_down_id=$po_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.id,b.color_number_id order by a.id";	 die;	
			$data_array_sample=sql_select("select a.id as po_id, b.color_number_id, min(b.id) as color_size_table_id from  wo_po_break_down a, wo_po_color_size_breakdown b where a.job_no_mst=b.job_no_mst and a.job_no_mst=$update_id and a.id=b.po_break_down_id and b.po_break_down_id=$po_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.id,b.color_number_id order by a.id");
			//print_r($data_array_sample);
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
				if($rID3) $flag=1; else $flag=0;
			}
			//============================================================================================
			$lap=1;
			$id_lap=return_next_id( "id", "wo_po_lapdip_approval_info", 1 ) ;
			// $cbosampletype=return_field_value( 'id', 'lib_sample', 'sample_type=2 and status_active=1 and is_deleted=0' );
			$field_array_lap="id,job_no_mst,po_break_down_id,color_name_id,status_active,is_deleted"; 		
			$data_array_lapdip=sql_select("select a.id as po_id, b.color_number_id, min(b.id) as color_size_table_id from  wo_po_break_down a, wo_po_color_size_breakdown b where a.job_no_mst=b.job_no_mst and a.job_no_mst=$update_id and a.id=b.po_break_down_id and  b.po_break_down_id=$po_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.id,b.color_number_id order by a.id");
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
				if($rID4) $flag=1; else $flag=0;
			}
			execute_query("update wo_booking_mst set is_apply_last_update=2 where job_no ='".$txt_job_no."' and booking_type=1 and is_short=2 ",1);
			execute_query("update wo_pre_cost_fabric_cost_dtls set is_apply_last_update=2 where job_no ='".$txt_job_no."'",1);
			execute_query("update wo_pre_cost_trim_cost_dtls set is_apply_last_update=2 where job_no ='".$txt_job_no."'",1);
			execute_query("update wo_pre_cost_embe_cost_dtls set is_apply_last_update=2 where job_no ='".$txt_job_no."'",1);
			execute_query("update wo_pre_cost_fab_conv_cost_dtls set is_apply_last_update=2 where job_no ='".$txt_job_no."'",1);
			//die;
			update_color_size_sequence($update_id,1);
			update_cost_sheet($update_id);
			$return_data=job_order_qty_update($update_id,$po_id,$set_breck_down,$breakdown_type,$cbo_order_status);
			//$return_data=update_job_mast($update_id);//define in common_functions.php
			//update_cost_sheet($update_id);
		//=============================================================================================
			if($db_type==0)
			{
				if($flag==1)
				{
					mysql_query("COMMIT");  
					echo "0**".str_replace("'",'',$po_id)."**".$return_data;
				}
				else
				{
					mysql_query("ROLLBACK"); 
					echo "10**".str_replace("'",'',$po_id)."**".$return_data;
				}
			}
			else if($db_type==2 || $db_type==1 )
			{
				if($flag==1)
				{
					oci_commit($con);  
					echo "0**".str_replace("'",'',$po_id)."**".$return_data;
				}
				else
				{
					oci_rollback($con); 
					echo "10**".str_replace("'",'',$po_id)."**".$return_data;
				}
			}
			disconnect($con);
			die;
		}
		else if (str_replace("'","",$copy_id)==1)
		{
			$po_sql=sql_select("select id, job_no_mst, is_confirmed, po_number, po_received_date, pub_shipment_date, shipment_date, factory_received_date, doc_sheet_qty, no_of_carton, po_quantity, unit_price, up_charge, original_avg_price, po_total_price, excess_cut, plan_cut, details_remarks, delay_for, packing, grouping, projected_po_id, actual_po_no, matrix_type, round_type, tna_task_from_upto, t_year, t_month, original_po_qty, file_no from wo_po_break_down where id=$update_id_details and job_no_mst=$update_id");
			
			$cbo_order_status="'".$po_sql[0][csf('is_confirmed')]."'";
			$txt_po_received_date="'".$po_sql[0][csf('po_received_date')]."'";
			$txt_pub_shipment_date="'".$po_sql[0][csf('pub_shipment_date')]."'";
			$txt_shipment_date="'".$po_sql[0][csf('shipment_date')]."'";
			$txt_fac_received_date="'".$po_sql[0][csf('factory_received_date')]."'";
			$txt_docSheetQty="'".$po_sql[0][csf('doc_sheet_qty')]."'";
			$txt_noOf_carton="'".$po_sql[0][csf('no_of_carton')]."'";
			$txt_poQty="'".$po_sql[0][csf('po_quantity')]."'";
			$txt_avg_price=(float)trim($po_sql[0][csf('unit_price')]);
			$txt_upCharge=(float)trim($po_sql[0][csf('up_charge')]);
			$txt_poAmt="'".$po_sql[0][csf('po_total_price')]."'";
			$txt_excessCut="'".$po_sql[0][csf('excess_cut')]."'";
			$txt_planCut="'".$po_sql[0][csf('plan_cut')]."'";
			$txt_po_remarks="'".$po_sql[0][csf('details_remarks')]."'";
			$cbo_delay_for="'".$po_sql[0][csf('delay_for')]."'";
			$packing="'".$po_sql[0][csf('packing')]."'";
			$txt_grouping="'".$po_sql[0][csf('grouping')]."'";
			$cbo_projected_po="'".$po_sql[0][csf('projected_po_id')]."'";
			//$txt_actual_po="'".$po_sql[0][csf('actual_po_no')]."'";
			$cbo_breakdown_type="'".$po_sql[0][csf('matrix_type')]."'";
			$cbo_round_type="'".$po_sql[0][csf('round_type')]."'";
			$cbo_tna_task="'".$po_sql[0][csf('tna_task_from_upto')]."'";
			$t_year="'".$po_sql[0][csf('t_year')]."'";
			$t_month="'".$po_sql[0][csf('t_month')]."'";
			$txt_file_no="'".$po_sql[0][csf('file_no')]."'";
			$txt_orgi_avg_price="'".$po_sql[0][csf('original_avg_price')]."'";
			$txt_orgi_po_qty="'".$po_sql[0][csf('original_po_qty')]."'";
			if (str_replace("'","",$update_id_details)!="")
			{
				$id=return_next_id("id", "wo_po_break_down", 1);
				if (str_replace("'","",$cbo_order_status)==1)
				{
					$field_array="id, job_no_mst, is_confirmed, po_number, po_received_date, pub_shipment_date, shipment_date, factory_received_date, doc_sheet_qty, no_of_carton, po_quantity, unit_price, up_charge, po_total_price, excess_cut, plan_cut, details_remarks, delay_for, packing, grouping, projected_po_id, matrix_type, round_type, tna_task_from_upto, t_year, t_month, file_no, inserted_by, insert_date, is_deleted, status_active";
					$data_array="(".$id.", ".$update_id.", ".$cbo_order_status.", ".$txt_copypo_no.", ".$txt_po_received_date.", ".$txt_pub_shipment_date.", ".$txt_shipment_date.", ".$txt_fac_received_date.",".$txt_docSheetQty.", ".$txt_noOf_carton.", ".$txt_poQty.", '".$txt_avg_price."', '".$txt_upCharge."', ".$txt_poAmt.", ".$txt_excessCut.", ".$txt_planCut.", ".$txt_po_remarks.",".$cbo_delay_for.",".$packing.",".$txt_grouping.",".$cbo_projected_po.",".$cbo_breakdown_type.",".$cbo_round_type.",".$cbo_tna_task.",".$t_year.",".$t_month.",".$txt_file_no.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',0,1)";
				}
				else
				{
					$field_array="id, job_no_mst, is_confirmed, po_number, po_received_date, pub_shipment_date, shipment_date, factory_received_date, doc_sheet_qty, no_of_carton, po_quantity, unit_price, up_charge, original_avg_price, po_total_price, excess_cut, plan_cut, details_remarks, delay_for, packing, grouping, projected_po_id, matrix_type, round_type, tna_task_from_upto, t_year, t_month, original_po_qty, file_no, inserted_by, insert_date, is_deleted, status_active";
					$data_array="(".$id.", ".$update_id.", ".$cbo_order_status.", ".$txt_copypo_no.", ".$txt_po_received_date.", ".$txt_pub_shipment_date.", ".$txt_shipment_date.", ".$txt_fac_received_date.",".$txt_docSheetQty.",".$txt_noOf_carton.", ".$txt_poQty.", ".$txt_avg_price.", ".$txt_upCharge.", ".$txt_orgi_avg_price.", ".$txt_poAmt.", ".$txt_excessCut.", ".$txt_planCut.", ".$txt_po_remarks.", ".$cbo_delay_for.",".$packing.",".$txt_grouping.",".$cbo_projected_po.",".$cbo_breakdown_type.",".$cbo_round_type.",".$cbo_tna_task.",".$t_year.",".$t_month.",".$txt_orgi_po_qty.",".$txt_file_no.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',0,1)";
				}
				$po_id=$id;
				//echo "INSERT INTO wo_po_break_down (".$field_array.") VALUES ".$data_array; die;
				//echo "select id, po_break_down_id, job_no_mst, color_mst_id, size_mst_id, item_mst_id, item_number_id, country_id, code_id, ultimate_country_id, ul_country_code, cutup, country_ship_date, color_number_id, size_number_id, order_quantity, order_rate, excess_cut_perc, article_number, order_total, plan_cut_qnty from wo_po_color_size_breakdown where po_break_down_id=$update_id_details and job_no_mst=$update_id and status_active=1 and is_deleted=0 order by id ASC";
				$color_size_breakdown_sql=sql_select("select id, po_break_down_id, job_no_mst, color_mst_id, size_mst_id, item_mst_id, item_number_id, country_id, code_id, ultimate_country_id, ul_country_code, cutup_date, cutup, country_ship_date, color_number_id, size_number_id, order_quantity, order_rate, excess_cut_perc, article_number, order_total, plan_cut_qnty, pack_qty, pcs_per_pack, pack_type, color_order, size_order, assort_qty, solid_qty from wo_po_color_size_breakdown where po_break_down_id=$update_id_details and job_no_mst=$update_id and status_active=1 and is_deleted=0 order by color_order, size_order ASC");
				
				$id1=return_next_id( "id", "wo_po_color_size_breakdown", 1) ;
				$field_array1="id, po_break_down_id, job_no_mst, color_mst_id, size_mst_id, item_mst_id, item_number_id, country_id, code_id, ultimate_country_id, ul_country_code, cutup_date, cutup, country_ship_date, color_number_id, size_number_id, order_quantity, order_rate, excess_cut_perc, article_number, order_total, plan_cut_qnty, pack_qty, pcs_per_pack, pack_type, color_order, size_order, assort_qty, solid_qty, inserted_by, insert_date, is_deleted, status_active";
				
				//echo $cbo_gmtsItem_id.'kausar';die;
				$add_comma=0; $data_array1="";
				foreach($color_size_breakdown_sql as $row)
				{
					$color_mst_id=0; $size_mst_id=0; $item_mst_id=0; $cbo_gmtsItem_id=0; $cbo_deliveryCountry_id=0; $txt_cutup_date=""; $cbo_cutOff_id=0; $txt_countryShip_date=""; $color_id=0; $size_id=0; $txt_colorSizeQty=0; $txt_colorSizeRate=0; $txt_colorSizeExCut=0; $txt_colorSizeArticleNo=0; $order_total_amt=0; $plancut_Qty=0; $assort_qty=0; $solid_qty=0;
					
					$cbo_gmtsItem_id = "'".$row[csf('item_number_id')]."'";
					$cbo_deliveryCountry_id = "'".$row[csf('country_id')]."'";
					$cbo_code_id = "'".$row[csf('code_id')]."'";
					$cbo_country_id = "'".$row[csf('ultimate_country_id')]."'";
					$cbo_countryCode_id = "'".$row[csf('ul_country_code')]."'";
					
					$txt_cutup_date = "'".$row[csf('cutup_date')]."'";
					$cbo_cutOff_id = "'".$row[csf('cutup')]."'";
					$txt_countryShip_date = "'".$row[csf('country_ship_date')]."'";
					$color_id = "'".$row[csf('color_number_id')]."'";
					$size_id = "'".$row[csf('size_number_id')]."'";
					
					$txt_colorSizePackQty="'".$row[csf('pack_qty')]."'";
					$txt_colorSizePcsQty=(float)trim($row[csf('pcs_per_pack')]);
					$txt_breakdownGrouping="'".$row[csf('pack_type')]."'";
					
					$txt_colorOrder="'".$row[csf('color_order')]."'";
					$txt_sizeOrder="'".$row[csf('size_order')]."'";
					
					$txt_colorSizeQty="'".$row[csf('order_quantity')]."'";
					$txt_colorSizeRate=(float)trim($row[csf('order_rate')]);
					$txt_colorSizeExCut="'".$row[csf('excess_cut_perc')]."'";
					$txt_colorSizeArticleNo="'".$row[csf('article_number')]."'";
						
					$order_total_amt="'".$row[csf('order_total')]."'";
					$plancut_Qty="'".$row[csf('plan_cut_qnty')]."'";
					$assort_qty="'".$row[csf('assort_qty')]."'";
					$solid_qty="'".$row[csf('solid_qty')]."'";
					//print_r($$txt_colorSizeQty).'</br>';
					if ($add_comma!=0) $data_array1 .=",";
					$data_array1 .="(".$id1.",".$po_id.",".$update_id.",'".$color_mst_id."','".$size_mst_id."','".$item_mst_id."',".$cbo_gmtsItem_id.",".$cbo_deliveryCountry_id.",".$cbo_code_id.",".$cbo_country_id.",".$cbo_countryCode_id.",".$txt_cutup_date .",".$cbo_cutOff_id.",".$txt_countryShip_date.",".$color_id.",".$size_id.",".$txt_colorSizeQty.",'".$txt_colorSizeRate."',".$txt_colorSizeExCut.",".$txt_colorSizeArticleNo.",".$order_total_amt.",".$plancut_Qty.",".$txt_colorSizePackQty.",".$txt_colorSizePcsQty.",".$txt_breakdownGrouping.",".$txt_colorOrder.",".$txt_sizeOrder.",".$assort_qty.",".$solid_qty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',0,1)";
					
					$id1=$id1+1;
					$add_comma++;
					//echo $data_array1;
				}
				//echo "INSERT INTO wo_po_color_size_breakdown (".$field_array1.") VALUES ".$data_array1; die;
				//echo $data_array1;
				if(str_replace("'","",$cbo_breakdown_type)==2 || str_replace("'","",$cbo_breakdown_type)==3)
				{
					//echo "select id, job_id, po_id, country_id, gmts_item_id, country_ship_date, color_id, size_id, ratio_qty, ratio_rate, ultimate_country_id, code_id, ul_country_code from  wo_po_ratio_breakdown where po_id=$update_id_details and status_active=1 and is_deleted=0 order by id ASC";
					$sql_ratio=sql_select("select id, job_id, po_id, country_id, gmts_item_id, country_ship_date, color_id, size_id, ratio_qty, ratio_rate, ultimate_country_id, code_id, ul_country_code from  wo_po_ratio_breakdown where po_id=$update_id_details and status_active=1 and is_deleted=0 order by id ASC");
					$idRatio=return_next_id( "id", "wo_po_ratio_breakdown", 1) ;
					$field_array_ratio="id, job_id, po_id, country_id, gmts_item_id, country_ship_date,color_id, size_id, ratio_qty, ratio_rate, ultimate_country_id, code_id, ul_country_code, inserted_by, insert_date";
				
					$add_comma=0; $data_array_ratio="";
					foreach($sql_ratio as $row)
					{
						$cbo_gmtsItem_id=0; $cbo_deliveryCountry_id=0; $cbo_code_id=0; $cbo_country_id=""; $cbo_countryCode_id=0; $txt_countryShip_date=0; $color_id=0; $size_id=0; $txt_colorSizeRatioQty=0; $txt_colorSizeRatioRate=0;
						
						$cbo_gmtsItem_id = "'".$row[csf('gmts_item_id')]."'";
						$cbo_deliveryCountry_id = "'".$row[csf('country_id')]."'";
						$cbo_code_id = "'".$row[csf('code_id')]."'";
						$cbo_country_id = "'".$row[csf('ultimate_country_id')]."'";
						$cbo_countryCode_id = "'".$row[csf('ul_country_code')]."'";
						
						$txt_countryShip_date = "'".$row[csf('country_ship_date')]."'";
						$color_id = "'".$row[csf('color_id')]."'";
						$size_id = "'".$row[csf('size_id')]."'";
						
						$txt_colorSizeRatioQty="'".$row[csf('ratio_qty')]."'";
						$txt_colorSizeRatioRate=(float)trim($row[csf('ratio_rate')]);
						//print_r($$txt_colorSizeQty).'</br>';
						if ($add_comma!=0) $data_array_ratio .=",";
						$data_array_ratio .="(".$idRatio.",".$po_id.",".$po_id.",".$cbo_deliveryCountry_id.",".$cbo_gmtsItem_id.",".$txt_countryShip_date.",".$color_id.",".$size_id.",".$txt_colorSizeRatioQty.",'".$txt_colorSizeRatioRate."',".$cbo_country_id.",".$cbo_code_id.",".$cbo_countryCode_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
						$idRatio=$idRatio+1;
						$add_comma++;
					}
					//echo "INSERT INTO wo_po_ratio_breakdown (".$field_array_ratio.") VALUES ".$data_array_ratio; die;
					$sql_destination=sql_select("select id, po_id, item_id, country_id, country_ship_date, ultimate_country_id,  color_id, destination_id, destination_qty, ul_country_code, code_id from wo_po_destination_info where po_id=$update_id_details and status_active=1 and is_deleted=0 order by id ASC");
					
					$idDest=return_next_id( "id", "wo_po_destination_info", 1) ;
					$field_array_des="id, po_id, item_id, country_id, country_ship_date, code_id, ultimate_country_id, ul_country_code, color_id, destination_id, destination_qty, inserted_by, insert_date";
				
					$add_comma=0; $data_array_des="";
					foreach($sql_destination as $row)
					{
						$cbo_gmtsItem_id=0; $cbo_deliveryCountry_id=0; $cbo_code_id=0; $cbo_country_id=""; $cbo_countryCode_id=0; $txt_countryShip_date=0; $color_id=0; $cboDestination=0; $txt_qty=0;
						
						$cbo_gmtsItem_id = "'".$row[csf('item_id')]."'";
						$cbo_deliveryCountry_id = "'".$row[csf('country_id')]."'";
						$cbo_code_id = "'".$row[csf('code_id')]."'";
						$cbo_country_id = "'".$row[csf('ultimate_country_id')]."'";
						$cbo_countryCode_id = "'".$row[csf('ul_country_code')]."'";
						
						$txt_countryShip_date = "'".$row[csf('country_ship_date')]."'";
						$color_id = "'".$row[csf('color_id')]."'";
						
						$cboDestination="'".$row[csf('destination_id')]."'";
						$txt_qty="'".$row[csf('destination_qty')]."'";
						//print_r($$txt_colorSizeQty).'</br>';
						if ($add_comma!=0) $data_array_ratio .=",";
						$data_array_des .="(".$idDest.",".$po_id.",".$cbo_gmtsItem_id.",".$cbo_deliveryCountry_id.",".$txt_countryShip_date.",".$cbo_code_id.",".$cbo_country_id.",".$cbo_countryCode_id.",".$color_id.",".$cboDestination.",".$txt_qty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
						$idDest=$idDest+1;
						$add_comma++;
					}
				}
				
				$rID=sql_insert("wo_po_break_down",$field_array,$data_array,0);	
				if($rID) $flag=1; else $flag=0;
				$rID1=sql_insert("wo_po_color_size_breakdown",$field_array1,$data_array1,0);
				if($rID1) $flag=1; else $flag=0;
				//echo $data_array_ratio.'<br>'.$data_array_des;
				//update_color_size_sequence($update_id,1);
				if(str_replace("'","",$cbo_breakdown_type)==2 || str_replace("'","",$cbo_breakdown_type)==3)
				{
					if($data_array_ratio!="" )
					{
						$rIDRatio=sql_insert("wo_po_ratio_breakdown",$field_array_ratio,$data_array_ratio,0);
						if($rIDRatio) $flag=1; else $flag=0;
					}
					
					if($data_array_des!="" )
					{
						$rIDDes=sql_insert("wo_po_destination_info",$field_array_des,$data_array_des,0);
						if($rIDDes) $flag=1; else $flag=0;
					}
				}
				
				execute_query("update wo_booking_mst set is_apply_last_update=2 where job_no ='".$txt_job_no."' and booking_type=1 and is_short=2 ",1);
				execute_query("update wo_pre_cost_fabric_cost_dtls set is_apply_last_update=2 where job_no ='".$txt_job_no."'",1);
				execute_query("update wo_pre_cost_trim_cost_dtls set is_apply_last_update=2 where job_no ='".$txt_job_no."'",1);
				execute_query("update wo_pre_cost_embe_cost_dtls set is_apply_last_update=2 where job_no ='".$txt_job_no."'",1);
				execute_query("update wo_pre_cost_fab_conv_cost_dtls set is_apply_last_update=2 where job_no ='".$txt_job_no."'",1);
				//echo $flag.'='.$rID1;
				if($db_type==0)
				{
					if($flag==1)
					{
						mysql_query("COMMIT");  
						echo "0**".str_replace("'",'',$po_id)."**".str_replace("'",'',$txt_copypo_no);
					}
					else
					{
						mysql_query("ROLLBACK"); 
						echo "10**".str_replace("'",'',$po_id)."**".str_replace("'",'',$txt_copypo_no);
					}
				}
				else if($db_type==2 || $db_type==1 )
				{
					if($flag==1)
					{
						oci_commit($con);  
						echo "0**".str_replace("'",'',$po_id)."**".str_replace("'",'',$txt_copypo_no);
					}
					else
					{
						oci_rollback($con); 
						echo "10**".str_replace("'",'',$po_id)."**".str_replace("'",'',$txt_copypo_no);
					}
				}
				disconnect($con);
				die;
			}
			else
			{
				disconnect($con);
				die;	
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
		$data_shipDate_vari="";
		$sql_shipDate_vari=sql_select("select duplicate_ship_date from variable_order_tracking where company_name=$cbo_company_name and variable_list=29");
		$data_shipDate_vari=$sql_shipDate_vari[0][csf("duplicate_ship_date")];
		
		if($data_shipDate_vari==1) $txt_pub_shipment_date_cond="and pub_shipment_date=$txt_pub_shipment_date";	
		else $txt_pub_shipment_date_cond="";	
		
		if (is_duplicate_field( "po_number", "wo_po_break_down", "po_number=$txt_po_no and job_no_mst=$update_id $txt_pub_shipment_date_cond and id!=$update_id_details and is_deleted=0" ) == 1)
		{
			echo "11**0"; 
			disconnect($con);die;
		}
		
		$sql_check=sql_select("select a.country_id, a.order_id,b.gmt_item_id, a.color_id,a.size_id,sum(a.marker_qty) as marker_qnty from ppl_cut_lay_size a, ppl_cut_lay_dtls b 
    	where b.id=a.dtls_id and  a.order_id=$update_id_details and 1=1 group by a.country_id, a.order_id,b.gmt_item_id, a.color_id,a.size_id");
		foreach($sql_check as $dts)
		{
			$prod_array[$dts[csf("order_id")]][$dts[csf("country_id")]][$dts[csf("gmt_item_id")]][$dts[csf("color_id")]][$dts[csf("size_id")]]=$dts[csf("marker_qnty")];
		}
		
		$breakdown_type=str_replace("'","",$cbo_breakdown_type);
		if($breakdown_type==4)
		{
			$docSheet_col="pack_price";
			$docSheet_field=$txt_avg_price;
			$avg_rate_pack=$txt_docSheetQty;
		}
		else
		{
			$docSheet_col="doc_sheet_qty";
			$docSheet_field=$txt_docSheetQty;
			$avg_rate_pack=$txt_avg_price;
		}
		//echo $tot_poQty.'='.$tot_poQty.'='.$tot_poAmt.'='.$txt_avg_price.'='.$set_qnty;
		$field_array="po_number*po_received_date*pub_shipment_date*shipment_date*factory_received_date*unit_price*up_charge*".$docSheet_col."*no_of_carton*details_remarks*delay_for*packing*grouping*projected_po_id*tna_task_from_upto*t_year*t_month*file_no*updated_by*update_date*status_active";
		
		$data_array="".$txt_po_no."*".$txt_po_received_date."*".$txt_pub_shipment_date."*".$txt_pub_shipment_date."*".$txt_po_received_date."*".$avg_rate_pack."*".$txt_upCharge."*".$docSheet_field."*".$txt_noOf_carton."*".$txt_po_remarks."*".$cbo_delay_for."*'".$packing."'*".$txt_grouping."*".$cbo_projected_po."*".$cbo_tna_task."*'".date("Y",strtotime(str_replace("'","",$txt_pub_shipment_date)))."'*'".date("m",strtotime(str_replace("'","",$txt_pub_shipment_date)))."'*".$txt_file_no."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_status."";
		
		//echo '10**'.$field_array.'<br>Kausar'.$data_array; die;
		
		//Check any change  to wo_po_break_down table;
		//--------------------------------
	
		$prev_data=sql_select("SELECT is_confirmed ,po_number, po_received_date, pub_shipment_date, shipment_date, factory_received_date, doc_sheet_qty, no_of_carton, po_quantity, unit_price, po_total_price, excess_cut, plan_cut, details_remarks, delay_for, packing, grouping, projected_po_id, matrix_type, round_type, tna_task_from_upto, t_year, t_month, file_no, is_deleted, status_active,updated_by,update_date FROM wo_po_break_down WHERE id=$update_id_details");
		foreach($prev_data as $rows)
		{
			$prev_po_no=$rows[csf('po_number')];
			$prev_matrix_type=$rows[csf('matrix_type')];
			$prev_round_type=$rows[csf('round_type')];
			$prev_doc_sheet_qty=$rows[csf('doc_sheet_qty')];
			$prev_no_of_carton=$rows[csf('no_of_carton')];
			
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
	//----------------------------------
		$sql_con="matrix_type=$cbo_breakdown_type and round_type=$cbo_round_type and is_confirmed=$cbo_order_status and po_number =$txt_po_no and job_no_mst=$update_id and po_received_date=$txt_po_received_date and pub_shipment_date=$txt_pub_shipment_date and shipment_date=$txt_pub_shipment_date and factory_received_date=$txt_po_received_date and doc_sheet_qty=$txt_docSheetQty and no_of_carton=$txt_noOf_carton and po_quantity=$txt_poQty and unit_price=$txt_avg_price and po_total_price=$txt_poAmt and excess_cut='1' and plan_cut='1' and details_remarks=$txt_po_remarks and delay_for=$cbo_delay_for and packing=$packing and grouping=$txt_grouping and projected_po_id=$cbo_projected_po and tna_task_from_upto=$cbo_tna_task and t_year=".date("Y",strtotime(str_replace("'","",$txt_pub_shipment_date)))." and t_month=".date("m",strtotime(str_replace("'","",$txt_pub_shipment_date)))." and file_no=$txt_file_no and id=$update_id_details and is_deleted=0";
		$sql_con=str_replace("=''"," IS NULL ",$sql_con);
		$is_duplicate=is_duplicate_field( "po_number", "wo_po_break_down", $sql_con );
			
		$log_id_mst=return_next_id( "id", "wo_po_update_log", 1);
			
		if($db_type==0) $current_date = $pc_date_time;
		else $current_date = change_date_format(date("Y-m-d H:i:s",strtotime(add_time(date("H:i:s",time()),0))),'','',1);
		
		$previous_po_qty=return_field_value("po_quantity","wo_po_break_down","job_no_mst=".$update_id." and id=".$update_id_details."");
		
		$log_update_date=return_field_value("update_date","wo_po_update_log","job_no=".$update_id." and po_id=".$update_id_details." order by id DESC");
		
		$log_update=date("Y-m-d", strtotime($log_update_date));
		$curr_date=date("Y-m-d", strtotime($current_date));
		
		if(($log_update=="" && $is_duplicate!=1) || ($log_update!=$curr_date && $is_duplicate!=1))
		{
			$field_array_history="id, entry_form, matrix_type, round_type, job_no, po_no, po_id, order_status, po_received_date, previous_po_qty, shipment_date, org_ship_date, po_status, t_year, t_month, fac_receive_date, projected_po, packing, remarks, file_no, doc_sheet_qty, avg_price, no_of_carton, excess_cut_parcent, plan_cut, status, update_date, update_by";
			
			$data_array_history="(".$log_id_mst.",1,'".$prev_matrix_type."','".$prev_round_type."',".$update_id.",'".$prev_po_no."',".$update_id_details.",'".$prev_order_status."','".$prev_po_received_date."','".$prev_po_qty."','".$prev_pub_shipment_date."','".$prev_org_shipment_date."','".$prev_status."','".date("Y",strtotime(str_replace("'","",$prev_org_shipment_date)))."','".date("m",strtotime(str_replace("'","",$prev_org_shipment_date)))."','".$prev_factory_rec_date."','".$prev_projected_po."','".$prev_packing."','".$prev_details_remark."','".$prev_file_no."','".$prev_doc_sheet_qty."','".$prev_avg_price."','".$prev_no_of_carton."','".$prev_excess_cut."','".$prev_plan_cut."','".$prev_status."','".$prev_update_date."',".$prev_updated_by.")";
			
			$rID3=sql_insert("wo_po_update_log",$field_array_history,$data_array_history,1);	
		}
		else if( $log_update==$curr_date)
		{
			$field_array_history="po_no*po_id*matrix_type*round_type*order_status*po_received_date*previous_po_qty*shipment_date*org_ship_date*po_status*fac_receive_date*projected_po*packing*remarks*file_no*avg_price*doc_sheet_qty*no_of_carton*excess_cut_parcent*plan_cut*status*update_date*update_by";
			
			$data_array_history="'".$prev_po_no."'*".$update_id_details."*'".$prev_matrix_type."'*'".$round_type."'*'".$prev_order_status."'*'".$prev_po_received_date."'*'".$prev_po_qty."'*'".$prev_pub_shipment_date."'*'".$prev_org_shipment_date."'*'".$prev_status."'*'".$prev_factory_rec_date."'*'".$prev_projected_po."'*'".$prev_packing."'*'".$prev_details_remark."'*'".$prev_file_no."'*'".$prev_avg_price."'*'".$prev_doc_sheet_qty."'*'".$prev_no_of_carton."'*'".$prev_excess_cut."'*'".$prev_plan_cut."'*'".$prev_order_status."'*'".$current_date."'*".$_SESSION['logic_erp']['user_id']."";
			$rID3=sql_update("wo_po_update_log",$field_array_history,$data_array_history,"po_id*update_date","".$update_id_details."*'".$log_update_date."'",1); 
		}
		//Log History end.-------------------------...REZA
		$breakdown_type=str_replace("'","",$cbo_breakdown_type);
		$id1=return_next_id( "id", "wo_po_color_size_breakdown", 1) ;
		
		if($breakdown_type==4)
		{
			//$field_array1="id, po_break_down_id, job_no_mst, color_mst_id, size_mst_id, item_mst_id, item_number_id, country_id, code_id, ultimate_country_id, ul_country_code, cutup_date, cutup, country_ship_date, color_number_id, size_number_id, order_quantity, order_rate, excess_cut_perc, article_number, order_total, plan_cut_qnty, pack_qty, pcs_per_pack, pack_type, pcs_pack, assort_qty, solid_qty, inserted_by, insert_date, is_deleted, status_active";
			
			$field_array_up="order_quantity*order_rate*excess_cut_perc*order_total*plan_cut_qnty*color_number_id_prev*country_ship_date_prev*pack_qty*pcs_per_pack*pack_type*pcs_pack*assort_qty*solid_qty*updated_by*update_date";
		}
		else
		{
			//$field_array1="id, po_break_down_id, job_no_mst, color_mst_id, size_mst_id, item_mst_id, item_number_id, country_id, code_id, ultimate_country_id, ul_country_code, cutup_date, cutup, country_ship_date, color_number_id, size_number_id, order_quantity, order_rate, excess_cut_perc, article_number, order_total, plan_cut_qnty, assort_qty, solid_qty, inserted_by, insert_date, is_deleted, status_active";
			
			$field_array_up="order_quantity*order_rate*excess_cut_perc*order_total*plan_cut_qnty*color_number_id_prev*country_ship_date_prev*assort_qty*solid_qty*updated_by*update_date";
		}
		
		$pre_color_date_arr=array();
		$PrevData=sql_select("select id, color_number_id, country_ship_date, order_quantity from wo_po_color_size_breakdown where po_break_down_id=".$update_id_details);
		foreach($PrevData as $row)
		{
			$pre_color_date_arr[$row[csf('id')]]['color_id']=$row[csf('color_number_id')];
			$pre_color_date_arr[$row[csf('id')]]['ship_date']=$row[csf('country_ship_date')];
			$pre_color_date_arr[$row[csf('id')]]['qty']=$row[csf('order_quantity')];
		}
		unset($PrevData);
		//echo $cbo_gmtsItem_id.'kausar';die;
		$item_id=str_replace("'","",$cbo_gmtsItem_id);
		$avg_rate=str_replace("'","",$txt_avg_price);
		$set_qnty=str_replace("'","",$tot_set_qnty);
		$set_breck_down=explode('__',str_replace("'","",$set_breck_down));
		$item_ratio_arr=array();
		foreach($set_breck_down as $set_data)
		{
			$ex_set_data=explode('_',$set_data);
			$ex_item_id=$ex_set_data[0];
			$ex_item_ratio=$ex_set_data[1];
			$item_ratio_arr[$ex_item_id]=$ex_item_ratio;
		}
		$set_breck_data="'".$item_id.'___'.$item_ratio_arr[$item_id]."'";
		$add_comma=0; $data_array1=""; $ratio_comma=0; $data_array_ratio=""; 
		$size_id_arr=array();
		for($k=1; $k<=$size_table; $k++)
		{
			$txtSizeName="txtSizeName_".$k;
			//$size_id_val = return_id_lib_common( $$txtSizeName, $size_library, "lib_size", "id,size_name");
			if(str_replace("'","",$$txtSizeName)!="")
			{
				if (!in_array(str_replace("'","",$$txtSizeName),$new_array_size))
				{
					$size_id_val = return_id_lib_common( str_replace("'","",$$txtSizeName), $size_library, "lib_size", "id,size_name","401");
					$new_array_size[$size_id_val]=str_replace("'","",$$txtSizeName);
				}
				else $size_id_val =  array_search(str_replace("'","",$$txtSizeName), $new_array_size);
			}
			else $size_id_val=0;
			
			$size_id_arr[$k]=$size_id_val;
		}
		//print_r($size_id_arr);
		$tmpIds=array();
		if($breakdown_type==1)
		{
			for($i=1; $i<=$color_table; $i++)
			{
				$txtColorName="txtColorName_".$i;
				//$color_id = return_id_lib_common( str_replace("'","",$$txtColorName), $color_library, "lib_color", "id,color_name");  
				if(str_replace("'","",$$txtColorName)!="")
				{ 
					if (!in_array(str_replace("'","",$$txtColorName),$new_array_color))
					{
						$color_id = return_id_lib_common( str_replace("'","",$$txtColorName), $color_library, "lib_color", "id,color_name","401");  
						//echo $$txtColorName.'='.$color_id.'<br>';
						$new_array_color[$color_id]=str_replace("'","",$$txtColorName);
					}
					else $color_id =  array_search(str_replace("'","",$$txtColorName), $new_array_color); 
				}
				else $color_id=0;
				
				for($m=1; $m<=$size_table; $m++)
				{
					$txtSizeName="txtSizeName_".$m;
					$size_id = $size_id_arr[$m];
					//$txt_colorSizeQty="";
					$txt_colorSizeId="txt_colorSizeId_".$i.'_'.$m;
					$txt_colorSizeQty="txt_colorSizeQty_".$i.'_'.$m;
					$txt_colorSizeRate="txt_colorSizeRate_".$i.'_'.$m;
					$txt_colorSizeExCut="txt_colorSizeExCut_".$i.'_'.$m;
					$txt_colorSizePLanCut="txt_colorSizePLanCut_".$i.'_'.$m;
					$txt_colorSizeArticleNo="txt_colorSizeArticleNo_".$i.'_'.$m;
					
					$txt_assortQty="txt_assortQty_".$i.'_'.$m;
					$ex_assort=explode('!!',str_replace("'","",$$txt_assortQty));
					$assort_qty=0; $solid_qty=0;
					$assort_qty=$ex_assort[0];
					$solid_qty=$ex_assort[1];
					
					$order_total_amt=0; $plancut_Qty=0; $color_size_poQty=0;
					$color_size_poQty=str_replace("'","",$$txt_colorSizeQty)*$item_ratio_arr[$item_id];
					$color_size_rate=str_replace("'","",$$txt_colorSizeRate)/$item_ratio_arr[$item_id];
					$order_total_amt=$color_size_poQty*$color_size_rate;//str_replace("'","",$$txt_colorSizeQty)*$color_size_rate;//str_replace("'","",$$txt_colorSizeRate);
					//$plancut_Qty=$color_size_poQty+((str_replace("'","",$$txt_colorSizeExCut)*$color_size_poQty)/100);
					$color_size_planCutQty=str_replace("'","",$$txt_colorSizePLanCut)*$item_ratio_arr[$item_id];
					//print_r($$txt_colorSizeQty).'</br>';
					if($color_size_poQty>0)
					{
						if (str_replace("'",'',$$txt_colorSizeId)!="")
						{
							if( ($prod_array[str_replace("'",'',$update_id_details)][str_replace("'",'',$cbo_deliveryCountry_id)][str_replace("'",'',$cbo_gmtsItem_id)][str_replace("'",'',$color_id)][str_replace("'",'',$size_id)]*1) <=$color_size_poQty )
							{
								$color_size_Qty=$color_size_poQty;
								$prod_id[]=str_replace("'",'',$$txt_colorSizeId);
							}
							else 
							{
								$color_size_Qty=$pre_color_date_arr[str_replace("'",'',$$txt_colorSizeId)]['qty'];
								$tmpIds[]=str_replace("'",'',$$txt_colorSizeId);
							}
							$id_arr[]=str_replace("'",'',$$txt_colorSizeId);
							
							$pre_color_id=0; $pre_country_ship_date='';
							$pre_color_id=$pre_color_date_arr[str_replace("'",'',$$txt_colorSizeId)]['color_id'];
							$pre_country_ship_date=$pre_color_date_arr[str_replace("'",'',$$txt_colorSizeId)]['ship_date'];
							
							$data_array_up[str_replace("'",'',$$txt_colorSizeId)] =explode("*",("'".$color_size_Qty."'*'".$color_size_rate."'*".$$txt_colorSizeExCut."*".$order_total_amt."*'".$color_size_planCutQty."'*'".$pre_color_id."'*'".$pre_country_ship_date."'*'".$assort_qty."'*'".$solid_qty."'*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
							
						}
					}
				}
			}//echo $data_array1;
			//print_r($data_array_up); die;
			$all_id="";
			foreach($id_arr as $val)
			{
				if($val!="")
				{
					if($all_id=="") $all_id=$val; else $all_id.=','.$val; 
				}
			}
		}
		else if($breakdown_type==4)
		{
			for($i=1; $i<=$color_table; $i++)
			{
				$txtColorName="txtColorName_".$i;
				//$color_id = return_id_lib_common( str_replace("'","",$$txtColorName), $color_library, "lib_color", "id,color_name"); 
				if(str_replace("'","",$$txtColorName)!="")
				{ 
					if (!in_array(str_replace("'","",$$txtColorName),$new_array_color))
					{
						$color_id = return_id_lib_common( str_replace("'","",$$txtColorName), $color_library, "lib_color", "id,color_name","401");  
						//echo $$txtColorName.'='.$color_id.'<br>';
						$new_array_color[$color_id]=str_replace("'","",$$txtColorName);
					}
					else $color_id =  array_search(str_replace("'","",$$txtColorName), $new_array_color); 
				}
				else $color_id=0;
				 
				for($m=1; $m<=$size_table; $m++)
				{
					$txtSizeName="txtSizeName_".$m;
					$size_id = $size_id_arr[$m];
					//$txt_colorSizeQty="";
					$txt_colorSizePackQty="txt_colorSizePackQty_".$i.'_'.$m;
					$txt_colorSizePcsQty="txt_colorSizePcsQty_".$i.'_'.$m;

					$txt_colorSizeId="txt_colorSizeId_".$i.'_'.$m;
					$txt_colorSizeQty="txt_colorSizeQty_".$i.'_'.$m;
					$txt_colorSizeRate="txt_colorSizeRate_".$i.'_'.$m;
					$txt_colorSizeExCut="txt_colorSizeExCut_".$i.'_'.$m;
					$txt_colorSizePLanCut="txt_colorSizePLanCut_".$i.'_'.$m;
					$txt_colorSizeArticleNo="txt_colorSizeArticleNo_".$i.'_'.$m;
					
					$txt_assortQty="txt_assortQty_".$i.'_'.$m;
					$ex_assort=explode('!!',str_replace("'","",$$txt_assortQty));
					$assort_qty=0; $solid_qty=0;
					$assort_qty=$ex_assort[0];
					$solid_qty=$ex_assort[1];
					
					$order_total_amt=0; $plancut_Qty=0; $color_size_poQty=0;
					$color_size_poQty=str_replace("'","",$$txt_colorSizeQty)*$item_ratio_arr[$item_id];
					$color_size_rate=str_replace("'","",$$txt_colorSizeRate)/$item_ratio_arr[$item_id];
					$order_total_amt=$color_size_poQty*$color_size_rate;//str_replace("'","",$$txt_colorSizeQty)*str_replace("'","",$$txt_colorSizeRate);
					//$plancut_Qty=$color_size_poQty+((str_replace("'","",$$txt_colorSizeExCut)*$color_size_poQty)/100);
					$color_size_planCutQty=str_replace("'","",$$txt_colorSizePLanCut)*$item_ratio_arr[$item_id];
					//print_r($$txt_colorSizeQty).'</br>';
					if($color_size_poQty>0)
					{
						if (str_replace("'",'',$$txt_colorSizeId)!="")
						{
							if( ($prod_array[str_replace("'",'',$update_id_details)][str_replace("'",'',$cbo_deliveryCountry_id)][str_replace("'",'',$cbo_gmtsItem_id)][str_replace("'",'',$color_id)][str_replace("'",'',$size_id)]*1) <1 )
							{
								$color_size_Qty=$color_size_poQty;
								$prod_id[]=str_replace("'",'',$$txt_colorSizeId);
							}
							else 
							{
								$color_size_Qty=$pre_color_date_arr[str_replace("'",'',$$txt_colorSizeId)]['qty'];
								$tmpIds[]=str_replace("'",'',$$txt_colorSizeId);
							}
							$id_arr[]=str_replace("'",'',$$txt_colorSizeId);
								
							$pre_color_id=0; $pre_country_ship_date='';
							$pre_color_id=$pre_color_date_arr[str_replace("'",'',$$txt_colorSizeId)]['color_id'];
							$pre_country_ship_date=$pre_color_date_arr[str_replace("'",'',$$txt_colorSizeId)]['ship_date'];
							
							$data_array_up[str_replace("'",'',$$txt_colorSizeId)] =explode("*",("'".$color_size_Qty."'*'".$color_size_rate."'*".$$txt_colorSizeExCut."*".$order_total_amt."*'".$color_size_planCutQty."'*'".$pre_color_id."'*'".$pre_country_ship_date."'*".$$txt_colorSizePackQty."*".$$txt_colorSizePcsQty."*".$txt_breakdownGrouping."*".$txt_pcsQty."*'".$assort_qty."'*'".$solid_qty."'*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
							
						}
					}
				}
			}//echo $data_array1;
			//print_r($data_array_up); die;
			$all_id="";
			foreach($id_arr as $val)
			{
				if($val!="")
				{
					if($all_id=="") $all_id=$val; else $all_id.=','.$val; 
				}
			}
		}
		else
		{
			$idratio=return_next_id( "id", "wo_po_ratio_breakdown", 1) ;
			$field_array_ratio="id, job_id, po_id, country_id, gmts_item_id, code_id, ultimate_country_id, ul_country_code, country_ship_date, color_id, size_id, ratio_qty, ratio_rate, inserted_by, insert_date";
			$field_array_ratioUp="country_id*gmts_item_id*code_id*ultimate_country_id*ul_country_code*country_ship_date*color_id*size_id*ratio_qty*ratio_rate*updated_by*update_date";
			for($i=1; $i<=$color_table; $i++)
			{
				$txtColorName="txtColorName_".$i;
				//$color_id = return_id_lib_common( str_replace("'","",$$txtColorName), $color_library, "lib_color", "id,color_name");  
				if(str_replace("'","",$$txtColorName)!="")
				{ 
					if (!in_array(str_replace("'","",$$txtColorName),$new_array_color))
					{
						$color_id = return_id_lib_common( str_replace("'","",$$txtColorName), $color_library, "lib_color", "id,color_name","401");  
						//echo $$txtColorName.'='.$color_id.'<br>';
						$new_array_color[$color_id]=str_replace("'","",$$txtColorName);
					}
					else $color_id = array_search(str_replace("'","",$$txtColorName), $new_array_color); 
				}
				else $color_id=0;
				
				for($m=1; $m<=$size_table; $m++)
				{
					$txtSizeName="txtSizeName_".$m;
					$size_id = $size_id_arr[$m];
					//$txt_colorSizeQty="";
					$txt_colorSizeId="txt_colorSizeId_".$i.'_'.$m;
					$txt_colorSizeQty="txt_colorSizeQty_".$i.'_'.$m;
					$txt_colorSizeRate="txt_colorSizeRate_".$i.'_'.$m;
					$txt_colorSizeExCut="txt_colorSizeExCut_".$i.'_'.$m;
					$txt_colorSizePLanCut="txt_colorSizePLanCut_".$i.'_'.$m;
					$txt_colorSizeArticleNo="txt_colorSizeArticleNo_".$i.'_'.$m;
					
					$txt_assortQty="txt_assortQty_".$i.'_'.$m;
					$ex_assort=explode('!!',str_replace("'","",$$txt_assortQty));
					$assort_qty=0; $solid_qty=0;
					$assort_qty=$ex_assort[0];
					$solid_qty=$ex_assort[1];
					
					$txt_colorSizeRatioQty="txt_colorSizeRatioQty_".$i.'_'.$m;
					$txt_colorSizeRatioRate="txt_colorSizeRatioRate_".$i.'_'.$m;
					$txt_colorSizeRatioId="txt_colorSizeRatioId_".$i.'_'.$m;
					
					$order_total_amt=0; $plancut_Qty=0; $color_size_poQty=0;
					$color_size_poQty=str_replace("'","",$$txt_colorSizeQty)*$item_ratio_arr[$item_id];
					$color_size_rate=str_replace("'","",$$txt_colorSizeRate)/$item_ratio_arr[$item_id];
					$order_total_amt=$color_size_poQty*$color_size_rate;//str_replace("'","",$$txt_colorSizeQty)*str_replace("'","",$$txt_colorSizeRate);
					//$plancut_Qty=$color_size_poQty+((str_replace("'","",$$txt_colorSizeExCut)*$color_size_poQty)/100);
					$color_size_planCutQty=str_replace("'","",$$txt_colorSizePLanCut)*$item_ratio_arr[$item_id];
					//print_r($$txt_colorSizeQty).'</br>';
					if($color_size_poQty>0)
					{
						if (str_replace("'",'',$$txt_colorSizeId)!="")
						{
							if( ($prod_array[str_replace("'",'',$update_id_details)][str_replace("'",'',$cbo_deliveryCountry_id)][str_replace("'",'',$cbo_gmtsItem_id)][str_replace("'",'',$color_id)][str_replace("'",'',$size_id)]*1) <=$color_size_poQty )
							{
								$color_size_Qty=$color_size_poQty;
								$prod_id[]=str_replace("'",'',$$txt_colorSizeId);
							}
							else 
							{
								$color_size_Qty=$pre_color_date_arr[str_replace("'",'',$$txt_colorSizeId)]['qty'];
								$tmpIds[]=str_replace("'",'',$$txt_colorSizeId);
							}
							$id_arr[]=str_replace("'",'',$$txt_colorSizeId);
							
							$pre_color_id=0; $pre_country_ship_date='';
							$pre_color_id=$pre_color_date_arr[str_replace("'",'',$$txt_colorSizeId)]['color_id'];
							$pre_country_ship_date=$pre_color_date_arr[str_replace("'",'',$$txt_colorSizeId)]['ship_date'];
							
							$data_array_up[str_replace("'",'',$$txt_colorSizeId)] =explode("*",("'".$color_size_Qty."'*'".$color_size_rate."'*".$$txt_colorSizeExCut."*".$order_total_amt."*'".$color_size_planCutQty."'*'".$pre_color_id."'*'".$pre_country_ship_date."'*'".$assort_qty."'*'".$solid_qty."'*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
						}
					
						if(str_replace("'",'',$$txt_colorSizeRatioId)=="")
						{
							if ($ratio_comma!=0) $data_array_ratio .=",";
								$data_array_ratio .="(".$idratio.",".$update_id_details.",".$update_id_details.",".$cbo_deliveryCountry_id.",".$cbo_gmtsItem_id.",".$cbo_code_id.",".$cbo_country_id.",".$cbo_countryCode_id.",".$txt_countryShip_date.",'".$color_id."','".$size_id."',".$$txt_colorSizeRatioQty.",".$$txt_colorSizeRatioRate.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
							$idratio=$idratio+1;
							$ratio_comma++;
						}
						else
						{
							$idRatio_arr[]=str_replace("'",'',$$txt_colorSizeRatioId);
							$data_array_ratio_up[str_replace("'",'',$$txt_colorSizeRatioId)] =explode("*",("".$cbo_deliveryCountry_id."*".$cbo_gmtsItem_id."*".$cbo_code_id."*".$cbo_country_id."*".$cbo_countryCode_id."*".$txt_countryShip_date."*".$color_id."*".$size_id."*".$$txt_colorSizeRatioQty."*".$$txt_colorSizeRatioRate."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
						}
					}
				}
			}//echo $data_array1;
			//print_r($data_array_up); die;
			$all_id="";
			foreach($id_arr as $val)
			{
				if($val!="")
				{
					if($all_id=="") $all_id=$val; else $all_id.=','.$val; 
				}
			}
			$all_ratio_id="";
			foreach($idRatio_arr as $val)
			{
				if($val!="")
				{
					if($all_ratio_id=="") $all_ratio_id=$val; else $all_ratio_id.=','.$val; 
				}
			}
		}
		
		//print_r($all_id);
			
		$rID=sql_update("wo_po_break_down",$field_array,$data_array,"id","".$update_id_details."",1);
		if($rID) $flag=1; else $flag=0;
		
		$nodeleted_ids=array_merge($tmpIds,$prod_id); $colSizeUpdate_id_array=array();
		if(implode(',',$nodeleted_ids)!='') $col_id_cond="and id not in (".implode(',',$nodeleted_ids).")"; else $col_id_cond="";
		
		if(str_replace("'","",$txt_breakdownGrouping)!="") $pack_type_cond=" and pack_type=$txt_breakdownGrouping"; else $pack_type_cond="";
		$colSize_sql_dtls="Select id, color_number_id, size_number_id from wo_po_color_size_breakdown where po_break_down_id=$update_id_details and country_id=$cbo_deliveryCountry_id and item_number_id=$cbo_gmtsItem_id and status_active=1 and is_deleted=0 $col_id_cond $pack_type_cond";//
		
		//echo "10**".$colSize_sql_dtls; die;
		$nameArray=sql_select( $colSize_sql_dtls ); 
		foreach($nameArray as $row)
		{
			if( ($prod_array[str_replace("'","",$update_id_details)][str_replace("'","",$cbo_deliveryCountry_id)][str_replace("'","",$cbo_gmtsItem_id)][$row[csf("color_number_id")]][$row[csf("size_number_id")]]*1)==0 )
				$colSizeUpdate_id_array[]=$row[csf('id')];
		}
		
		if($data_array1!="")
		{
			//echo "insert into wo_po_color_size_breakdown (".$field_array1.") values ".$data_array1;die;
			$rID4=sql_insert("wo_po_color_size_breakdown",$field_array1,$data_array1,0);
			if($rID4) $flag=1; else $flag=0;
		}
		
		/*foreach($id_arr as $id_up)
		{
			$rID1=sql_update("wo_po_color_size_breakdown",$field_array_up,$data_array_up,"id","".$id_up."",1);
			if($rID1) $flag=1; else $flag=0;
		}*/
		if($data_array_up!="")
		{
			$rID1=execute_query(bulk_update_sql_statement("wo_po_color_size_breakdown", "id",$field_array_up,$data_array_up,$id_arr ));
			if($rID1) $flag=1; else $flag=0;
		}
		
		
		//$distance_delete_id=array_diff($colSizeUpdate_id_array,$nodeleted_ids);
		
		$field_array_del="status_active*is_deleted*updated_by*update_date";
		$data_array_del="'0'*'1'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		//print_r ($distance_delete_id);
	
		foreach($colSizeUpdate_id_array as $id_val)
		{
			$rID5=sql_update("wo_po_color_size_breakdown",$field_array_del,$data_array_del,"id","".$id_val."",1);
			if($rID5) $flag=1; else $flag=0;
		}
		
		if($breakdown_type==2 || $breakdown_type==3)
		{
			if($data_array_ratio!="")
			{
				//echo "insert into wo_po_ratio_breakdown (".$field_array_ratio.") values ".$data_array_ratio;die;
				$rIDratio=sql_insert("wo_po_ratio_breakdown",$field_array_ratio,$data_array_ratio,0);
				if($rIDratio) $flag=1; else $flag=0;
			}
			$rIDratioUp=execute_query(bulk_update_sql_statement("wo_po_ratio_breakdown", "id",$field_array_ratioUp,$data_array_ratio_up,$idRatio_arr ));
			if($rIDratioUp) $flag=1; else $flag=0;
			
			$ratio_sql="Select id from wo_po_ratio_breakdown where po_id=$update_id_details and country_id=$cbo_deliveryCountry_id and gmts_item_id=$cbo_gmtsItem_id and status_active=1 and is_deleted=0";
			$ratio_sql_res=sql_select( $ratio_sql );
			foreach($ratio_sql_res as $row)
			{
				$ratioUpdate_id_array[]=$row[csf('id')];
			}			
			if(implode(',',$idRatio_arr)!="")
			{
				$distance_ratio_delete_id=array_diff($ratioUpdate_id_array,$idRatio_arr);
			}
			else
			{
				$distance_ratio_delete_id=$ratioUpdate_id_array;
			}
			//print_r ($distance_ratio_delete_id);
			$field_array_ratio_del="status_active*is_deleted*updated_by*update_date";
			$data_array_ratio_del="'0'*'1'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			//print_r ($distance_ratio_delete_id);
			if(implode(',',$distance_ratio_delete_id)!="")
			{
				foreach($distance_ratio_delete_id as $id_val)
				{
					$rIDdelratio=sql_update("wo_po_ratio_breakdown",$field_array_ratio_del,$data_array_ratio_del,"id","".$id_val."",1);
					if($rIDdelratio) $flag=1; else $flag=0;
				}
			}
		}
		
		execute_query("update wo_booking_mst set is_apply_last_update=2 where job_no ='".$txt_job_no."' and booking_type=1 and is_short=2 ",1);
		execute_query("update wo_pre_cost_fabric_cost_dtls set is_apply_last_update=2 where job_no ='".$txt_job_no."'",1);
		execute_query("update wo_pre_cost_trim_cost_dtls set is_apply_last_update=2 where job_no ='".$txt_job_no."'",1);
		execute_query("update wo_pre_cost_embe_cost_dtls set is_apply_last_update=2 where job_no ='".$txt_job_no."'",1);
		execute_query("update wo_pre_cost_fab_conv_cost_dtls set is_apply_last_update=2 where job_no ='".$txt_job_no."'",1);
		
		//echo $update_id; die;
		update_color_size_sequence($update_id,2);
		//job_order_qty_update($update_id,$po_id,$tot_set_qnty,$cbo_order_status);
		$return_data=job_order_qty_update($update_id,$update_id_details,$set_breck_down,$breakdown_type,'0');
		update_cost_sheet($update_id);
		//echo $return_data; die;
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'",'',$update_id_details)."**".$return_data;
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$update_id_details)."**".$return_data;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1){
				oci_commit($con); 
				echo "1**".str_replace("'",'',$update_id_details)."**".$return_data;
			}
			else{
				oci_rollback($con); 
				echo "10**".str_replace("'",'',$update_id_details)."**".$return_data;
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
		//echo "select item_number_id, country_id, cutup, country_ship_date, code_id, ultimate_country_id, ul_country_code from wo_po_color_size_breakdown where po_break_down_id=$update_id_details and is_deleted=0 and status_active=1 group by item_number_id, country_id, cutup, country_ship_date, code_id, ultimate_country_id, ul_country_code order by country_ship_date";
		
		if (is_duplicate_field( "po_break_down_id", "pro_garments_production_mst", "po_break_down_id=$update_id_details and is_deleted=0" ) == 1)
		{
			echo "14**0"; 
			disconnect($con);die;
		}
		else if (is_duplicate_field( "order_id", "ppl_cut_lay_size", "order_id=$update_id_details and is_deleted=0" ) == 1)
		{
			echo "14**0"; 
			die;
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
		
		if(str_replace("'","",$txt_breakdownGrouping)!="") $pack_type_cond=" and pack_type=$txt_breakdownGrouping"; else $pack_type_cond="";
		$sql= sql_select("select item_number_id, country_id, cutup, country_ship_date, code_id, ultimate_country_id, ul_country_code, pack_type from wo_po_color_size_breakdown where po_break_down_id=$update_id_details and is_deleted=0 and status_active=1 group by item_number_id, country_id, cutup, country_ship_date, code_id, ultimate_country_id, ul_country_code, pack_type order by country_ship_date");
		//echo count($sql); die;
		
		if(count($sql)==1)
		{
			$field_array_po="status_active*is_deleted*updated_by*update_date";
			$data_array_po="'0'*'1'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			$rID1=sql_update("wo_po_break_down",$field_array_po,$data_array_po,"id","".$update_id_details."",1);
			if($rID1) $flag=1; else $flag=0;
		}
		if(str_replace("'",'',$delete_country)==1)
		{
			for($i=1; $i<=$color_table; $i++)
			{
				for($m=1; $m<=$size_table; $m++)
				{
					$txt_colorSizeId="txt_colorSizeId_".$i.'_'.$m;
					$txt_colorSizeRatioId="txt_colorSizeRatioId_".$i.'_'.$m;
					if (str_replace("'",'',$$txt_colorSizeId)!="")
					{
						$id_arr[]=str_replace("'",'',$$txt_colorSizeId);
					}
					
					if (str_replace("'",'',$$txt_colorSizeRatioId)!="")
					{
						$id_ratio_arr[]=str_replace("'",'',$$txt_colorSizeRatioId);
					}
				}
			}
			
			$field_array="status_active*is_deleted*updated_by*update_date";
			$data_array="'0'*'1'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			foreach($id_arr as $val)
			{
				$rID=sql_update("wo_po_color_size_breakdown",$field_array,$data_array,"id","".$val."",1);
				if($rID) $flag=1; else $flag=0;
			}
			$field_array_ratio="status_active*is_deleted*updated_by*update_date";
			$data_array_ratio="'0'*'1'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			foreach($id_ratio_arr as $val)
			{
				$rIDratio=sql_update("wo_po_color_size_breakdown",$field_array_ratio,$data_array_ratio,"id","".$val."",1);
				if($rIDratio) $flag=1; else $flag=0;
			}
		}
		else
		{
			 $flag=0;
		}
		//$return_data=update_job_mast($update_id);
		$set_breck_down=explode('__',str_replace("'","",$set_breck_down));
		//echo $update_id.'=='.$update_id_details.'=='.$set_breck_down; die;
		update_color_size_sequence($update_id,2);
		//job_order_qty_update($update_id,$po_id,$tot_set_qnty,$cbo_order_status);
		$return_data=job_order_qty_update($update_id,$update_id_details,$set_breck_down,$breakdown_type,'0');
		update_cost_sheet($update_id);
		//die;
		$txt_job_no=str_replace("'","",$txt_job_no);
		if($db_type==0)
		{
			if($flag==1){
				mysql_query("COMMIT");  
				echo "2**".str_replace("'",'',$delete_po);
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1){
				oci_commit($con); 
				echo "2**".str_replace("'",'',$update_id_details);
			}
			else{
				oci_rollback($con); 
				echo "10**";
			}
		}
		//echo "2**".$rID."**".$return_data[1]."**".$return_data[2]."**".$return_data[3];
		disconnect($con);
		die;
	}
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
				var booking=return_global_ajax_value(rowid, 'delete_row', '', 'order_matrix_update_controller');
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
		http.open("POST","order_matrix_update_controller.php",true);
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
		
		if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; disconnect($con);die;}		
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
	if ($operation==1)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; disconnect($con);die;}
		
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
if($action=="reorder_size_color")
{
	echo load_html_head_contents("Color Size Pop Up","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
	var permission='<? echo $permission; ?>';
	
	function fnc_size_color_reorder(operation)
	{
		var row_num_color=$('#color_order tbody tr').length;
		var data_all_color="";
		for (var i=1; i<=row_num_color; i++)
		{
			if (form_validation('colorordering_'+i,'Color Ordering')==false)
			{
				return;
			}
			data_all_color=data_all_color+get_submitted_data_string('txt_job_no*colorid_'+i+'*colorordering_'+i,"../../../",i);
		}
	
		var row_num_size=$('#size_order tbody tr').length;
		var data_all_size="";
		for (var i=1; i<=row_num_size; i++)
		{
			if (form_validation('sizeordering_'+i,'Size Ordering')==false)
			{
				return;
			}
			data_all_size=data_all_size+get_submitted_data_string('txt_job_no*sizeid_'+i+'*sizeordering_'+i,"../../../",i);
		}
	
		var data="action=save_update_color_size_ordering&operation="+operation+'&total_row_color='+row_num_color+data_all_color+'&total_row_size='+row_num_size+data_all_size;
		//alert(data); return;
		freeze_window(operation);
		http.open("POST","size_color_breakdown_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_size_color_reorder_reponse;
	}
	
	function fnc_size_color_reorder_reponse()
	{
		if(http.readyState == 4) 
		{
			var reponse=http.responseText.split('**');
			release_freezing();
		}
	}
	</script>
	</head>
	<body onLoad="set_hotkey()">
	<div style="width:100%;" align="center">
        <input type="hidden" id="garments_nature" value="2">
        <? echo load_freeze_divs ("../../../",$permission);  ?>
        <fieldset style="width:500px;">
            <form id="colorsizeorder_1">
            <input type="hidden" class="text_boxes_numeric" id="txt_job_no" value="<? echo $txt_job_no; ?>" style="widows:60px"/>
                <table>
                    <tr>
                        <td valign="top">
                        	<table id="color_order" class="rpt_table" border="1" rules="all">
                                <thead>
                                    <tr>
                                        <th width="30">Sl</th>
                                        <th width="150">Color</th>
                                        <th width="">Color Ordering</th>
                                    </tr>
                            	</thead>
                    			<tbody>
								<? $sql_data=sql_select("select min(id) as id, color_number_id, min(color_order) as color_order from wo_po_color_size_breakdown where job_no_mst='$txt_job_no' and status_active=1 and is_deleted=0 group by color_number_id order by color_order"); 
                                $i=1;
                                foreach($sql_data as $sql_row)
                                {
									?>
									<tr>
                                        <td><? echo $i; ?></td>
                                        <td><? echo $color_library[$sql_row[csf('color_number_id')]]; ?><input type="hidden" class="text_boxes_numeric" id="colorid_<? echo $i; ?>" value="<? echo $sql_row[csf('color_number_id')]; ?>" style="widows:60px"/></td>
                                        <td><input type="text" class="text_boxes_numeric" id="colorordering_<? echo $i; ?>" style="widows:60px" value="<? echo $sql_row[csf('color_order')];  ?>"/></td>
									</tr>
									<?	
									$i++;
                                }
                                ?>
                                </tbody>
                			</table>
                		</td>
               			<td valign="top">
                			<table id="size_order" class="rpt_table" border="1" rules="all">
                				<thead>
                                    <tr>
                                        <th width="30">Sl</th>
                                        <th width="150">Size</th>
                                        <th width="">Size Ordering</th>
                                    </tr>
                                </thead>
                                <tbody>
								<? $sql_data=sql_select("select min(id) as id, size_number_id, min(size_order) as size_order from wo_po_color_size_breakdown where job_no_mst='$txt_job_no' and status_active=1 and is_deleted=0 group by size_number_id order by size_order "); 
                                $i=1;
                                foreach($sql_data as $sql_row)
                                {
									?>
									<tr>
                                        <td><? echo $i; ?></td>
                                        <td><? echo $size_library[$sql_row[csf('size_number_id')]]; ?><input type="hidden" class="text_boxes_numeric" id="sizeid_<? echo $i; ?>" value="<? echo $sql_row[csf('size_number_id')]; ?>" style="widows:60px"/></td>
                                        <td><input type="text" class="text_boxes_numeric" id="sizeordering_<? echo $i; ?>" style="widows:60px" value="<? echo $sql_row[csf('size_order')];  ?>"/></td>
									</tr>
									<?	
									$i++;
                                }
                                ?>
                                </tbody>
               				</table>
                		</td>
                	</tr>
                	<tr>
                        <td align="center" colspan="7"  class="button_container">
                        	<? echo load_submit_buttons( $permission, "fnc_size_color_reorder", 1,0 ,"",1); ?>  
                        </td> 
                	</tr>
                </table>
            </form>
        </fieldset>
    </div>
	</body>  
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="save_update_color_size_ordering")
{
    $process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	if($operation==1)
	{
	    $con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		 for ($i=1;$i<=$total_row_color;$i++)
		 {
			 $colorid="colorid_".$i;
			 $colorordering="colorordering_".$i;
			 $rID=execute_query( "update wo_po_color_size_breakdown set color_order=".$$colorordering." where  color_number_id =".$$colorid." and job_no_mst=".$txt_job_no."",0);
		 }
		 
		 for ($i=1;$i<=$total_row_size;$i++)
		 {
			 $sizeid="sizeid_".$i;
			 $sizeordering="sizeordering_".$i;
			 $rID=execute_query( "update wo_po_color_size_breakdown set  size_order=".$$sizeordering."  where  size_number_id =".$$sizeid." and job_no_mst=".$txt_job_no."",0);
		 }
		if($db_type==0)
		{
			if($rID )
			{
				mysql_query("COMMIT");  
				echo "0**".$new_job_no[0]."**".$rID;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$new_job_no[0]."**".$rID;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID )
			{
				oci_commit($con);  
				echo "0**".$new_job_no[0]."**".$rID;
			}
			else
			{
				oci_rollback($con);   
				echo "10**".$new_job_no[0]."**".$rID;
			}
		}
		disconnect($con);
		die;
	}	
}

// function update_color_size_sequence($txt_job_no,$btn_mood)
// {
// 	$colororder_by="";
// 	$sizeorder_by="";
// 	if($btn_mood==1)
// 	{
// 		$colororder_by="order by id";
// 		$sizeorder_by="order by id";
// 	}
// 	else if($btn_mood==2)
// 	{
// 		$colororder_by="order by color_order";
// 		$sizeorder_by="order by size_order";
// 	}
// 	$sql_data=sql_select("select min(id) as id, color_number_id, min(color_order) as color_order from wo_po_color_size_breakdown where job_no_mst=$txt_job_no and status_active=1 and is_deleted=0 group by color_number_id $colororder_by");
// 	$color_order=1;
// 	foreach ($sql_data as $row)
// 	{
// 		$rID=execute_query("update wo_po_color_size_breakdown set color_order=".$color_order." where color_number_id=".$row[csf('color_number_id')]." and job_no_mst=$txt_job_no",0);
// 		$color_order++;
// 	}
// 	$sql_data1=sql_select("select min(id) as id, size_number_id, min(size_order) as size_order from wo_po_color_size_breakdown where job_no_mst=$txt_job_no and status_active=1 and is_deleted=0 group by size_number_id $sizeorder_by");
// 	$size_order=1;
// 	foreach ($sql_data1 as $row1)
// 	{
// 		$rID=execute_query( "update wo_po_color_size_breakdown set size_order=".$size_order." where size_number_id=".$row1[csf('size_number_id')]." and job_no_mst=$txt_job_no",0);
// 		$size_order++;	
// 	}
// }

function job_order_qty_update($job_no,$po_id,$set_data,$breakdown_type,$order_status)
{
	$po_data_arr=array(); $job_data_arr=array(); $item_set_arr=array(); $item_ratio=0;
	//print_r($set_data);
	foreach($set_data as $exSet)
	{
		$exItemRatio=explode('_',$exSet);
		//$item_ratio_arr[$exItemRatio[0]]=$exItemRatio[1];
		$item_ratio+=$exItemRatio[1];
	}
	//echo "select po_break_down_id, item_number_id, sum(order_quantity) as po_tot, sum(order_total) as po_tot_price, sum(plan_cut_qnty) as plan_cut from wo_po_color_size_breakdown where job_no_mst=$job_no and is_deleted=0 and status_active=1 group by po_break_down_id, item_number_id";
	$data_array_se=sql_select("select po_break_down_id, sum(order_quantity) as po_tot, sum(order_total) as po_tot_price, sum(plan_cut_qnty) as plan_cut from wo_po_color_size_breakdown where job_no_mst=$job_no and is_deleted=0 and status_active=1 group by po_break_down_id");
	foreach($data_array_se as $row)
	{
		//$item_ratio=0; 
		$item_qty=0; $item_amt=0; $item_planCut=0;
		//$item_ratio=$item_ratio_arr[$row[csf('item_number_id')]];
		$item_qty=$row[csf('po_tot')]/$item_ratio;
		$item_amt=$row[csf('po_tot_price')];//*$item_ratio;
		$item_planCut=$row[csf('plan_cut')]/$item_ratio;
		$po_data_arr[$row[csf('po_break_down_id')]]['qty']+=$item_qty;
		$po_data_arr[$row[csf('po_break_down_id')]]['amt']+=$item_amt;
		$po_data_arr[$row[csf('po_break_down_id')]]['plan']+=$item_planCut;
		$job_data_arr['qty']+=$item_qty;
		$job_data_arr['amt']+=$item_amt;
	}
	//echo $item_ratio; die;
	//list($po_data)=$data_array_se;
	$set_qnty=str_replace("'","",$set_qnty);
	
	$job_qty=$job_data_arr['qty'];
	$job_amt=$job_data_arr['amt'];
	$poavgprice=number_format($job_amt/$job_qty,4);
	//echo $job_qty_set.'='.$job_amt_set.'='.$job_price; die;
	$field_array_job="job_quantity*avg_unit_price*total_price";
	$data_array_job="".$job_qty."*".$poavgprice."*".$job_amt."";
	//echo $field_array_job."****".$data_array_job;
	$po_qty=$po_data_arr[str_replace("'","",$po_id)]['qty'];
	$po_unit_price=number_format($po_data_arr[str_replace("'","",$po_id)]['amt']/$po_qty,4);
	$poavgprice_po=number_format($po_data_arr[str_replace("'","",$po_id)]['amt']/$po_qty,4);
	
	$po_ex_per=number_format((($po_data_arr[str_replace("'","",$po_id)]['plan']-$po_qty)/$po_qty)*100,2);
	if($breakdown_type==4)
	{
		if(str_replace("'","",$cbo_order_status)==2)
		{
			$field_array_po="po_quantity*unit_price*po_total_price*plan_cut*excess_cut*original_po_qty";
			$data_array_po="".$po_qty."*'".$po_unit_price."'*'".$po_data_arr[str_replace("'","",$po_id)]['amt']."'*'".$po_data_arr[str_replace("'","",$po_id)]['plan']."'*'".$po_ex_per."'*'".$po_qty."'";
		}
		else
		{
			$field_array_po="po_quantity*unit_price*po_total_price*plan_cut*excess_cut";
			$data_array_po="".$po_qty."*'".$po_unit_price."'*'".$po_data_arr[str_replace("'","",$po_id)]['amt']."'*'".$po_data_arr[str_replace("'","",$po_id)]['plan']."'*'".$po_ex_per."'";
		}
	}
	else
	{
		if(str_replace("'","",$cbo_order_status)==2)
		{
			$field_array_po="po_quantity*po_total_price*plan_cut*excess_cut*original_po_qty";
			$data_array_po="".$po_qty."*'".$po_data_arr[str_replace("'","",$po_id)]['amt']."'*'".$po_data_arr[str_replace("'","",$po_id)]['plan']."'*'".$po_ex_per."'*'".$po_qty."'";
		}
		else
		{
			$field_array_po="po_quantity*po_total_price*plan_cut*excess_cut";
			$data_array_po="".$po_qty."*'".$po_data_arr[str_replace("'","",$po_id)]['amt']."'*'".$po_data_arr[str_replace("'","",$po_id)]['plan']."'*'".$po_ex_per."'";
		}

	}
	//echo $data_array_job."*".$data_array_po;
	$rID2=sql_update("wo_po_details_master",$field_array_job,$data_array_job,"job_no","".$job_no."",1);
	$rID3=sql_update("wo_po_break_down",$field_array_po,$data_array_po,"id","".$po_id."",1);
	
	$projected_data_array=sql_select("select sum(CASE WHEN is_confirmed=2 THEN po_quantity ELSE 0 END) as job_projected_qty,
	sum(CASE WHEN is_confirmed=2 THEN (po_quantity*unit_price) ELSE 0 END) as job_projected_total,
	sum(original_po_qty) as projected_qty, sum(original_po_qty*original_avg_price) as projected_amount, (sum(original_po_qty*original_avg_price)/sum(original_po_qty)) as projected_rate from wo_po_break_down where job_no_mst='$job_no' ");
	
	$jobQtyProjected=0; $jobPriceProjected=0; $jobAmtProjected=0; $jobQtyOriginal=0; $jobPriceOriginal=0; $jobAmtOriginal=0;
	$job_projected_price=0;
	$job_projected_price=$projected_data_array[0][csf('job_projected_total')]/$projected_data_array[0][csf('job_projected_qty')];
	
	$jobQtyProjected= number_format($projected_data_array[0][csf('job_projected_qty')]);
	$jobPriceProjected= number_format($job_projected_price,4);
	$jobAmtProjected= number_format($projected_data_array[0][csf('job_projected_total')],2);
	
	$jobQtyOriginal= number_format($projected_data_array[0][csf('projected_qty')]);
	$jobPriceOriginal= number_format($projected_data_array[0][csf('projected_rate')],4);
	$jobAmtOriginal= number_format($projected_data_array[0][csf('projected_amount')],2);
	
	$value= $job_qty."**".$poavgprice."**".$job_amt."**".$jobQtyProjected."**".$jobPriceProjected."**".$jobAmtProjected."**".$jobQtyOriginal."**".$jobPriceOriginal."**".$jobAmtOriginal."**".$po_unit_price;
	//array(0=>$rID,1=>$po_data[csf('po_tot')],2=>$poavgprice,3=>$po_data[csf('po_tot_price')]);
	return $value;
	//exit();
}

if($action=="ultimate_dtls_popup")
{
	echo load_html_head_contents("Ultimate Country Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	
	$ex_data=explode('_',$data);
	$po_no=$ex_data[0];
	$po_id=$ex_data[1];
	$qty=$ex_data[2];
	$color=$ex_data[3];
	$item_id=$ex_data[5];
	$country_id=$ex_data[6];
	$code_id=$ex_data[7];
	$ultimate_country_id=$ex_data[8];
	$countryCode_id=$ex_data[9];
	$countryShip_date=$ex_data[10];
	//echo $data; die;
	
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
			var colorQty=$('#txt_colorQty').val()*1;
			var inQty=0;
			for( var k = 1; k <= row_num; k++)
			{
				inQty=inQty+($('#txtQty_'+k).val()*1);
			}
			var desQty=0;
			desQty=colorQty-inQty;
			
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
			$('#cboDestination_'+i).val("");
			$('#txtQty_'+i).attr('placeholder',desQty);
			//$('#txt_qty_'+i).removeAttr("placeholder").attr("placeholder","Type an ID");
			$('#txtQty_'+i).val("");
			//$('#txt_qty_'+i).removeAttr('placeholder').placeholder();
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
				var booking=return_global_ajax_value(rowid, 'delete_row', '', 'order_matrix_update_controller');
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
	
	function fnc_destination_info( operation )
	{
		var row_num = $('table#tbl_list_search tbody tr').length; 
		var data_all="";
		var colorQty=$('#txt_colorQty').val()*1;
		var inQty=0;
		for (var i=1; i<=row_num; i++)
		{
			if(form_validation('cboDestination_'+i+'*txtQty_'+i,'Destination*Qty')==false)
			{
				return;   
			}
			inQty=inQty+($('#txtQty_'+i).val()*1);
			data_all+=get_submitted_data_string('cboDestination_'+i+'*txtQty_'+i,"../../../",i);
		}
		var desQty=0;
		desQty=colorQty-inQty;
		if (colorQty<inQty)
		{
			alert("Qty Excceded From Color Qty.");
			return;
		}
		var data_main="action=save_update_delete_destination_info&operation="+operation+"&total_row="+row_num+get_submitted_data_string('txt_poId*cbo_country*cbo_code_id*cbo_ultimate*cbo_countrycode_id*txt_country_date*txt_color*cbo_item',"../../../");
		//alert(data_main);
		var data=data_main+data_all;
		//alert (data); return;
		freeze_window(operation);
		http.open("POST","order_matrix_update_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_destination_info_reponse;
	}
	
	function fnc_destination_info_reponse()
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
        <fieldset style="width:720px">
        <form id="ultimateinfo_1" autocomplete="off">
            <table width="720" cellspacing="2" cellpadding="2">
                <tr>
                	<td width="100"><strong>Po No</strong></td>
                    <td width="100"><input type="text" id="txt_poNo" name="txt_poNo" class="text_boxes" style="width:90px" value="<? echo $po_no; ?>" disabled /><input type="hidden" id="txt_poId" name="txt_poId" class="text_boxes" style="width:70px" value="<? echo $po_id; ?>" disabled /></td>
                    <td width="100"><strong>Delivery Country</strong></td>
                    <td width="100"><? echo create_drop_down( "cbo_country", 100,"select id, country_name from lib_country where status_active=1 and is_deleted=0 order by country_name", "id,country_name", 1, "--Select Country--", $country_id,"",1 ); ?></td>
                    <td width="100"><strong>Code</strong></td>
                    <td width="100"><? echo create_drop_down( "cbo_code_id", 100,"select id, ultimate_country_code from lib_country_loc_mapping where status_active=1 and is_deleted=0 order by ultimate_country_code", "id,ultimate_country_code", 1, "--Select--", $code_id,"",1 ); ?></td>
                </tr>
                <tr>
                	<td width="100"><strong>Country</strong></td>
                    <td width="100"><? echo create_drop_down( "cbo_ultimate", 100,"select id, country_name from lib_country where status_active=1 and is_deleted=0 order by country_name", "id,country_name", 1, "--Select--", $ultimate_country_id,"",1 ); ?></td>
                	<td width="100"><strong>Country Code</strong></td>
                    <td width="100"><? echo create_drop_down( "cbo_countrycode_id", 100,"select id, ultimate_country_code from lib_country_loc_mapping where status_active=1 and is_deleted=0 order by ultimate_country_code", "id,ultimate_country_code", 1, "--Select--", $countryCode_id,"",1 ); ?></td>
                	<td width="100"><strong>Country Ship date</strong></td>
                    <td width="100"><input type="text" id="txt_country_date" name="txt_country_date" class="datepicker" style="width:90px" value="<? echo $countryShip_date; ?>" disabled /></td>
                </tr>
                <tr>
                	<td width="100"><strong>Gmts Item</strong></td>
                    <td width="100"><? echo create_drop_down( "cbo_item", 100,$garments_item, "", 1, "--Select Item--", $item_id,"",1 ); ?></td>
                	<td width="100"><strong>Color</strong></td>
                    <td width="100"><input type="text" id="txt_color" name="txt_color" class="text_boxes" style="width:90px" value="<? echo $color; ?>" disabled /></td>
                	<td width="100"><strong>Color Qty</strong></td>
                    <td width="100"><input type="text" id="txt_colorQty" name="txt_colorQty" class="text_boxes_numeric" style="width:90px" value="<? echo $qty; ?>" disabled /></td>
                </tr>
            </table>
            <table width="400" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list_search">
                <thead>
                    <th>Destination</th>
                    <th>Qty.</th>
                    <th>&nbsp;</th>
                </thead>
                <tbody>
					<?
					$color_arr=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
					//$color_id = return_id( str_replace("'","",$color), $color_arr, "lib_color", "id,color_name");
					if(str_replace("'","",$color)!="")
					{ 
						if (!in_array(str_replace("'","",$color),$new_array_color))
						{
							$color_id = return_id_lib_common( str_replace("'","",$color), $color_arr, "lib_color", "id,color_name","401");
							$new_array_color[$color_id]=str_replace("'","",$color);
						}
						else $color_id = array_search(str_replace("'","",$color), $new_array_color); 
					}
					else
					{
						$color_id=0;
					}
					
                    $data_array=sql_select("select id, destination_id, destination_qty from wo_po_destination_info where po_id =".$po_id." and item_id=".$item_id." and country_id=".$country_id." and code_id=".$code_id." and ultimate_country_id=".$ultimate_country_id." and ul_country_code=".$countryCode_id." and color_id='".$color_id."'");
					
					//echo "select id, destination_id, destination_qty from wo_po_destination_info where po_id =".$po_id." and item_id=".$item_id." and country_id=".$country_id." and code_id=".$code_id." and ultimate_country_id=".$ultimate_country_id." and ul_country_code=".$countryCode_id." and color_id='".$color_id."'";
                    if(count($data_array)>0)
                    {
						$i=1;
						foreach($data_array as $row)
						{
							?>
							<tr class="general" id="tr_<? echo $i;?>">
                                <td><? echo create_drop_down( "cboDestination_$i", 200,"select id, depo_code from lib_country_depo_mapping where country_id='$country_id' and status_active=1 and is_deleted=0 order by depo_code", "id,depo_code", 1, "--Select--", $row[csf("destination_id")],"",'' ); ?></td>
                                <td><input type="text" id="txtQty_<? echo $i;?>" name="txtQty_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" value="<? echo $row[csf("destination_qty")]; ?>" /></td>
                                <td width="100">
                                    <input type="button" id="increase_<? echo $i;?>" name="increase_<? echo $i;?>" style="width:40px" class="formbutton" value="+" onClick="add_break_down_tr(<? echo $i;?>)" />
                                    <input type="button" id="decrease_<? echo $i;?>" name="decrease_<? echo $i;?>" style="width:40px" class="formbutton" value="-" onClick="fn_deletebreak_down_tr(<? echo $i;?>);" />
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
                            <td><? echo create_drop_down( "cboDestination_1", 200,"select id, depo_code from lib_country_depo_mapping where country_id='$country_id' and  status_active=1 and is_deleted=0 order by depo_code", "id,depo_code", 1, "--Select--", '',"",'' ); ?></td>
                            <td><input type="text" id="txtQty_1" name="txtQty_1" class="text_boxes_numeric" style="width:80px" placeholder="<? echo $qty; ?>" /></td>
                            <td width="100">
                                <input type="button" id="increase_1" name="increase_1" style="width:40px" class="formbutton" value="+" onClick="add_break_down_tr(1)" />
                                <input type="button" id="decrease_1" name="decrease_1" style="width:40px" class="formbutton" value="-" onClick="fn_deletebreak_down_tr(1);" />
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
            	echo load_submit_buttons( $permission, "fnc_destination_info", 1,0 ,"reset_form('ultimateinfo_1','','','','')",1) ; 
            }
            else
            {
            	echo load_submit_buttons( $permission, "fnc_destination_info", 0,0 ,"reset_form('ultimateinfo_1','','','','')",1) ; 
            }
            ?>
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

if($action=="save_update_delete_destination_info")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$color_arr=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		//if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}		
		$id=return_next_id( "id", "wo_po_destination_info", 1) ;
		$field_array="id, po_id, item_id, country_id, country_ship_date, code_id, ultimate_country_id, ul_country_code, color_id, destination_id, destination_qty, inserted_by, insert_date";
		//$color_id = return_id_lib_common( str_replace("'","",$txt_color), $color_arr, "lib_color", "id,color_name"); 
		if(str_replace("'","",$txt_color)!="")
		{
			if (!in_array(str_replace("'","",$txt_color),$new_array_color))
			{
				$color_id = return_id_lib_common( str_replace("'","",$txt_color), $color_arr, "lib_color", "id,lib_color","401");
				$new_array_color[$color_id]=str_replace("'","",$txt_color);
			}
			else $color_id =  array_search(str_replace("'","",$txt_color), $new_array_color);
		}
		else $color_id=0;
		 
		for ($i=1;$i<=$total_row;$i++)
		{
			$cboDestination="cboDestination_".$i;
			$txt_qty="txtQty_".$i;
			
			if ($i!=1) $data_array .=",";
			$data_array .="(".$id.",".$txt_poId.",".$cbo_item.",".$cbo_country.",".$txt_country_date.",".$cbo_code_id.",".$cbo_ultimate.",".$cbo_countrycode_id.",'".$color_id."',".$$cboDestination.",".$$txt_qty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			$id=$id+1;
		}
		$rID=sql_insert("wo_po_destination_info",$field_array,$data_array,1);
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
	else if ($operation==1)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; disconnect($con);die;}
		//$color_id = return_id_lib_common( str_replace("'","",$txt_color), $color_arr, "lib_color", "id,color_name");  
		if(str_replace("'","",$txt_color)!="")
		{
			if (!in_array(str_replace("'","",$txt_color),$new_array_color))
			{
				$color_id = return_id_lib_common( str_replace("'","",$txt_color), $color_arr, "lib_color", "id,lib_color","401");
				$new_array_color[$color_id]=str_replace("'","",$txt_color);
			}
			else $color_id =  array_search(str_replace("'","",$txt_color), $new_array_color);
		}
		else $color_id=0;
		
		$add_comma=0;
		execute_query("delete from wo_po_destination_info where po_id =".$txt_poId." and item_id=".$cbo_item." and country_id=".$cbo_country." and code_id=".$cbo_code_id." and ultimate_country_id=".$cbo_ultimate." and ul_country_code=".$cbo_countrycode_id." and color_id='".$color_id."'",0);
		$id=return_next_id( "id", "wo_po_destination_info", 1) ;
		$field_array="id, po_id, item_id, country_id, country_ship_date, code_id, ultimate_country_id, ul_country_code, color_id, destination_id, destination_qty, inserted_by, insert_date";
		
		for ($i=1;$i<=$total_row;$i++)
		{
			$cboDestination="cboDestination_".$i;
			$txt_qty="txtQty_".$i;
			
			if ($i!=1) $data_array .=",";
			$data_array .="(".$id.",".$txt_poId.",".$cbo_item.",".$cbo_country.",".$txt_country_date.",".$cbo_code_id.",".$cbo_ultimate.",".$cbo_countrycode_id.",'".$color_id."',".$$cboDestination.",".$$txt_qty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			$id=$id+1;
		}
		
		$rID=sql_insert("wo_po_destination_info",$field_array,$data_array,1);
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
	else if ($operation==2)  // Insert Here
	{
		/*$con = connect();
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
		die;*/
	}
}

if($action=='full_qty_check_for_validation')
{
	$ex_data=explode('***',$data);
	if($ex_data[3]==0)
	{
		$sql_result = sql_select("select sum(order_quantity) as po_qty from  wo_po_color_size_breakdown where po_break_down_id='$ex_data[0]' and country_id!='$ex_data[1]' and item_number_id='$ex_data[2]' and status_active=1 and is_deleted=0");
	}
	else if($ex_data[3]==1)
	{
		$sql_result = sql_select("select sum(order_quantity) as po_qty from  wo_po_color_size_breakdown where po_break_down_id='$ex_data[0]' and country_id!='$ex_data[4]' and item_number_id='$ex_data[2]' and status_active=1 and is_deleted=0");
	}
	//echo "select sum(order_quantity) as po_qty from  wo_po_color_size_breakdown where po_break_down_id='$ex_data[0]' and country_id!='$ex_data[1]' and item_number_id='$ex_data[2]' and status_active=1 and is_deleted=0";
	
	echo $sql_result[0][csf('po_qty')];
 	exit();
}

if($action=="set_ship_date")
{
	$data=explode("_",$data);
	$Date = change_date_format($data[0],"yyyy-mm-dd","-");
	if($data[1]==1)
	{
		echo date('d-m-Y', strtotime($Date. ' - 1 days'));
	}
	else if($data[1]==2)
	{
		echo date('d-m-Y', strtotime($Date. ' + 1 days'));
	}
	else if($data[1]==3)
	{
		echo date('d-m-Y', strtotime($Date. ' + 3 days'));
	}
	exit();
}

if($action=="load_cutOff_id_from_lib")
{
	$sql_country=sql_select("select cut_off from lib_country where id='$data'");
	echo "document.getElementById('cbo_cutOff_id').value = '".$sql_country[0][csf('cut_off')]."';\n";
 	exit();
}

if($action=="assortment_pop_up")
{
	echo load_html_head_contents("Assortment Info","../../../", 1, 1, '','1','');
	extract($_REQUEST);
	$ex_data=explode("_",$data);
	$ex_ass_data=explode('!!',$ex_data[3]);
	?>
    <script>
		function fnc_total_qty_check(index_i)
		{
			var assort_qty=$('#txt_assort').val()*1;
			var solid_qty=$('#txt_solid').val()*1;
			var tot_qty=$('#txt_tot_asst').val()*1;
			var tot_ast_sold=assort_qty+solid_qty;
			if(assort_qty>tot_qty)
			{
				alert('Assort Qty is Over.')
				$('#txt_assort').val('');
				return;
			}
			else if(solid_qty>tot_qty)
			{
				alert('Solid Qty is Over.')
				$('#txt_solid').val('');
				return;
			}
			var balance_qty=0;
			if(index_i==1)
			{
				balance_qty=tot_qty-assort_qty;
				$('#txt_solid').val( balance_qty );
			}
			else if(index_i==2)
			{
				balance_qty=tot_qty-solid_qty;
				$('#txt_assort').val( balance_qty );
			}
		}
		
		function js_set_value()
		{
			document.getElementById('txt_assort').value;
			document.getElementById('txt_solid').value;
			parent.emailwindow.hide();
		}
		
	</script>
	</head>
	<body>
    <div id="rate_details"  align="center">            
        <form name="rateDetails_1" id="rateDetails_1" autocomplete="off">
            <table width="360" cellspacing="0" border="1" class="rpt_table" id="tbl_rateDetails" rules="all">
            	<thead>
                	<th width="80">Color Name</th>
                    <th width="60">Size</th>
                	<th width="70">Assort</th>
                    <th width="70">Solid</th>
                    <th>Total</th>
                </thead>
                <tr>
                    <td bgcolor="#CCFF66" align="center"><? echo $ex_data[0]; ?></td>
                    <td bgcolor="#FFFFCC" align="center"><? echo $ex_data[1]; ?></td>
                    <td><input style="width:55px;" type="text" class="text_boxes_numeric" name="txt_assort" id="txt_assort" value="<? echo $ex_ass_data[0]; ?>" onBlur="fnc_total_qty_check(1);" /></td>
                    <td><input style="width:55px;" type="text" class="text_boxes_numeric" name="txt_solid" id="txt_solid" value="<? echo $ex_ass_data[1]; ?>" onBlur="fnc_total_qty_check(2);" /></td>
                    <td><input style="width:60px;" type="text" class="text_boxes_numeric" name="txt_tot_asst" id="txt_tot_asst" value="<? echo $ex_data[2]; ?>" disabled /></td>
                </tr>
            </table>
            <table width="360" cellspacing="0" class="" border="0">
                <tr>
                    <td align="center" width="100%" class="button_container"><input type="button" class="formbutton" value="Close" onClick="js_set_value()" /></td> 
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

if($action=="sc_lc_status")
{
	$data=explode("_",$data);
	/*if($data[1]=="")
	{
		$sql="select booking_no, is_approved from wo_booking_mst where job_no='$data[0]' and booking_type=1 and is_short=2 and is_deleted=0 and status_active=1";
	}
	else
	{
		$sql="select a.booking_no,a.is_approved from wo_booking_mst a, wo_booking_dtls b where a.job_no=b.job_no and a.booking_no=b.booking_no and  a.job_no='$data[0]' and a.booking_type=1 and a.is_short=2 and b.po_break_down_id=$data[1] and a.is_deleted=0 and a.status_active=1 group by a.booking_no,is_approved";
	}*/
	
	$sql_sc="select a.contact_system_id, a.contract_no from com_sales_contract a, com_sales_contract_order_info b where a.id=b.com_sales_contract_id and b.wo_po_break_down_id='$data[1]' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.contact_system_id, a.contract_no";
	
	$sql_lc="select a.export_lc_system_id, a.export_lc_no from com_export_lc a, com_export_lc_order_info b where a.id=b.com_export_lc_id and b.wo_po_break_down_id='$data[1]' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.export_lc_system_id, a.export_lc_no";
	$sql_sc_res=sql_select($sql_sc);
	$sql_lc_res=sql_select($sql_lc);
	$sc_data=""; $lc_data="";
	
	foreach($sql_sc_res as $rowsc)
	{
		if($rowsc[csf('contact_system_id')]=="")
		{
			$sc_data='';
		}
		else
		{
			if($sc_data=='') $sc_data=$rowsc[csf('contact_system_id')]." : ".$rowsc[csf('contract_no')]; else $sc_data.=', '.$rowsc[csf('contact_system_id')]." : ".$rowsc[csf('contract_no')];	
		}
	}
	
	foreach($sql_lc_res as $rowlc)
	{
		if($rowlc[csf('export_lc_system_id')]=="")
		{
			$lc_data='';
		}
		else
		{
			if($lc_data=='') $lc_data=$rowlc[csf('export_lc_system_id')]." : ".$rowlc[csf('export_lc_no')]; else $lc_data.=', '.$rowlc[csf('export_lc_system_id')]." : ".$rowlc[csf('export_lc_no')];	
		}
	}
	unset($sql_sc_res); unset($sql_lc_res);
	/*if($sc_data=='') $sc_data=0; 
	if($lc_data=='') $lc_data=0; */
	echo rtrim($sc_data)."_".rtrim($lc_data);
	exit();
}

if($action=="get_cutting_qty_country")
{
	$data=explode("_",$data);
	$production_quantity=0;
	$sql_data=sql_select( "select count(id) as id from  pro_garments_production_mst where po_break_down_id='$data[0]' and country_id='$data[1]' and  status_active=1 and is_deleted=0");
	foreach($sql_data as $row_data)
	{
		if($row_data[csf('id')]>0)
		{
			$production_quantity=$row_data[csf('id')];
		}
	}
	echo trim($production_quantity);
	exit();
}
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
			var data="action=save_update_delete_pp_meeting&operation="+operation+'&total_row='+row_num+'&type='+type+data1;
			
			freeze_window(operation);
			http.open("POST","order_matrix_update_controller.php",true);
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
        <? } ?>
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

?>