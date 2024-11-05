<?
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

if ($action=="load_variable_settings")
{
	$sql_result = sql_select("select variable_list, tna_integrated, copy_quotation, publish_shipment_date, po_update_period, po_current_date, season_mandatory, excut_source, cost_control_source, color_from_library from variable_order_tracking where company_name=$data and variable_list in (14,20,23,25,32,33,44,45,47,53) and status_active=1 and is_deleted=0 order by variable_list ASC");
	$tna_integrated=0; $copy_quotation=0; $set_smv_id=0; $publish_shipment_date=0; $po_update_period=0; $po_current_date=0; $season_mandatory=0; $excut_source=0; $cost_control_source=0; $color_from_lib=0;
 	foreach($sql_result as $result)
	{
		if($result[csf('variable_list')]==14) $tna_integrated=$result[csf('tna_integrated')];
		else if($result[csf('variable_list')]==20) $copy_quotation=$result[csf('copy_quotation')];
		else if($result[csf('variable_list')]==23) $color_from_lib=$result[csf('color_from_library')];
		else if($result[csf('variable_list')]==25) $publish_shipment_date=$result[csf('publish_shipment_date')];
		else if($result[csf('variable_list')]==32) $po_update_period=$result[csf('po_update_period')];
		else if($result[csf('variable_list')]==33) $po_current_date=$result[csf('po_current_date')];
		else if($result[csf('variable_list')]==44) $season_mandatory=$result[csf('season_mandatory')];
		else if($result[csf('variable_list')]==45) $excut_source=$result[csf('excut_source')];
		else if($result[csf('variable_list')]==47) $set_smv_id=$result[csf('publish_shipment_date')];
		else if($result[csf('variable_list')]==53) $cost_control_source=$result[csf('cost_control_source')];
	}
	echo $tna_integrated."_".$copy_quotation."_".$publish_shipment_date."_".$po_update_period."_".$po_current_date."_".$season_mandatory."_".$excut_source."_".$cost_control_source."_".$set_smv_id."_".$color_from_lib;
 	exit();
}

/*if ($action=="load_variable_settings")
{
	$sql_result = sql_select("select variable_list, tna_integrated, copy_quotation, publish_shipment_date, po_update_period, po_current_date, season_mandatory, excut_source, cost_control_source, color_from_library from variable_order_tracking where company_name=$data and variable_list in (14,20,23,25,32,33,44,45,47,53) and status_active=1 and is_deleted=0 order by variable_list ASC");
	$tna_integrated=0; $copy_quotation=0; $set_smv_id=0; $publish_shipment_date=0; $po_update_period=0; $po_current_date=0; $season_mandatory=0; $excut_source=0; $cost_control_source=0; $color_from_lib=0;
 	foreach($sql_result as $result)
	{
		if($result[csf('variable_list')]==14) $tna_integrated=$result[csf('tna_integrated')];
		else if($result[csf('variable_list')]==20) $copy_quotation=$result[csf('copy_quotation')];
		else if($result[csf('variable_list')]==23) $color_from_lib=$result[csf('color_from_library')];
		else if($result[csf('variable_list')]==25) $publish_shipment_date=$result[csf('publish_shipment_date')];
		else if($result[csf('variable_list')]==32) $po_update_period=$result[csf('po_update_period')];
		else if($result[csf('variable_list')]==33) $po_current_date=$result[csf('po_current_date')];
		else if($result[csf('variable_list')]==44) $season_mandatory=$result[csf('season_mandatory')];
		else if($result[csf('variable_list')]==45) $excut_source=$result[csf('excut_source')];
		else if($result[csf('variable_list')]==47) $set_smv_id=$result[csf('publish_shipment_date')];
		else if($result[csf('variable_list')]==53) $cost_control_source=$result[csf('cost_control_source')];
	}
	echo $tna_integrated."_".$copy_quotation."_".$publish_shipment_date."_".$po_update_period."_".$po_current_date."_".$season_mandatory."_".$excut_source."_".$cost_control_source."_".$set_smv_id."_".$color_from_lib;
 	exit();
}*/

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 150, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select --", $selected, "" );
	exit();	 
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "sub_dept_load(this.value,document.getElementById('cbo_product_department').value);load_drop_down( 'requires/time_weight_record_controller', this.value, 'load_drop_down_brand', 'brand_td'); load_drop_down( 'requires/time_weight_record_controller', this.value, 'load_drop_down_season', 'season_td'); " ); 
	exit();	 
}
if ($action=="load_drop_down_brand")
{
	$data_arr = explode("*", $data);
	if($data_arr[1] == 1) $width=90; else $width=150;
	echo create_drop_down( "cbo_brand_id", $width, "select id, brand_name from lib_buyer_brand brand where buyer_id='$data_arr[0]' and status_active =1 and is_deleted=0 $brand_cond order by brand_name ASC","id,brand_name", 1, "--Brand--", $selected, "" );
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

if ($action=="load_drop_down_season")
{
	$data_arr = explode("*", $data);
	if($data_arr[1] == 1) $width=90; else $width=150;
	echo create_drop_down( "cbo_season_id", $width, "select id, season_name from lib_buyer_season where buyer_id='$data_arr[0]' and status_active =1 and is_deleted=0 order by season_name ASC","id,season_name", 1, "-- Select Season--", "", "" );
	exit();
}

if ($action=="load_drop_down_buyer_pop")
{
	echo create_drop_down( "cbo_buyer_id", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "load_drop_down( 'time_weight_record_controller', this.value+'*1', 'load_drop_down_brand', 'brand_td');load_drop_down( 'time_weight_record_controller', this.value+'*1', 'load_drop_down_season', 'season_td');" );   
	exit();	 
}

if ($action=="style_popup")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
		function set_checkvalue()
		{
			if(document.getElementById('chk_style_dtls').value==0) document.getElementById('chk_style_dtls').value=1;
			else document.getElementById('chk_style_dtls').value=0;
		}
	
		function js_set_value( id )
		{
			document.getElementById('selected_id').value=id;
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
                	<th colspan="10" align="center"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --",1 ); ?></th>
                </tr>
                <tr>
                    <th>Company Name</th>
                    <th>Buyer Name</th>
                    <th>Brand</th>
                    <th>Style ID</th>
                    <th>Season Year</th>
                    <th>Season</th>
                    <th>Style Ref </th>
                    <th colspan="2">Est. Ship Date</th>
                    <th><input type="checkbox" value="0" onClick="set_checkvalue()" id="chk_style_dtls">Style Without Details</th>
                </tr>        
            </thead>
            <tr class="general">
                <td> 
                    <input type="hidden" id="selected_id">
					<? echo create_drop_down( "cbo_company_mst", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $cbo_company_name,"load_drop_down( 'order_entry_controller', this.value, 'load_drop_down_buyer_pop', 'buyer_pop_td' );",'1' ); ?>
                </td>
        		<td id="buyer_pop_td"><? echo create_drop_down( "cbo_buyer_id", 150, $blank_array,'', 1, "-- Select Buyer --" ); ?></td>
        		<td id="brand_td"><? echo create_drop_down( "cbo_brand_id", 90, $blank_array,'', 1, "Brand",$selected, "" ); ?>
                <td><input name="txt_style_prifix" id="txt_style_prifix" class="text_boxes" style="width:80px"></td>                
                <td><? echo create_drop_down( "cbo_season_year", 90, create_year_array(),"", 1,"-Year-", 1, "",0,"" ); ?></td>
                <td id="season_td"><? echo create_drop_down( "cbo_season_id", 90, $blank_array,'', 1, "Season",$selected, "" ); ?></td>
                <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:80px"></td>
                <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date"></td>
                <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To Date"></td> 
                <td align="center">
                	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_id').value+'_'+document.getElementById('chk_style_dtls').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_style_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('cbo_brand_id').value+'_'+document.getElementById('cbo_season_year').value+'_'+document.getElementById('cbo_season_id').value, 'create_style_search_list_view', 'search_div', 'time_weight_record_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
        	</tr>
            <tr>
                <td align="center" colspan="10"><? echo load_month_buttons(1); ?></td>
            </tr>
        </table>
        <div id="search_div"></div>
    </form>
    </div>
    </body>
    <script>
		load_drop_down('time_weight_record_controller', <? echo $cbo_company_name; ?>, 'load_drop_down_buyer_pop', 'buyer_pop_td' );
		<? if($cbo_buyer_name !=0)
		{ ?>
			load_drop_down('time_weight_record_controller', <? echo $cbo_buyer_name; ?>+'*1', 'load_drop_down_brand', 'brand_td' );
			load_drop_down('time_weight_record_controller', <? echo $cbo_buyer_name; ?>+'*1', 'load_drop_down_season', 'season_td' );
			document.getElementById('cbo_buyer_id').value=<? echo $cbo_buyer_name; ?>
		<? } ?>
		
    </script>         
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if($action=="create_style_search_list_view")
{
	//echo $data;die;
	$data=explode('_',$data);
	if ($data[0]!=0) $company=" and company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	
	if(str_replace("'","",$data[1])==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer=" and buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer="";
		}
		else $buyer="";
	}
	else $buyer=" and buyer_name='$data[1]'";
	
	//if ($data[1]!=0) $buyer=" and a.buyer_name='$data[1]'"; else $buyer="";//{ echo "Please Select Buyer First."; die; }
	
	if($db_type==0)
	{
		$year_cond=" and SUBSTRING_INDEX(insert_date, '-', 1)=$data[6]";
		$insert_year_cond="SUBSTRING_INDEX(insert_date, '-', 1)";
		if ($data[3]!="" &&  $data[4]!="") $shipment_date = "and estimated_shipdate between '".change_date_format($data[3], "yyyy-mm-dd", "-")."' and '".change_date_format($data[4], "yyyy-mm-dd", "-")."'"; else $shipment_date ="";
	}
	else if($db_type==2)
	{
		$year_cond=" and to_char(insert_date,'YYYY')=$data[6]";
		$insert_year_cond="to_char(insert_date,'YYYY')";
		if ($data[3]!="" &&  $data[4]!="") $shipment_date = "and estimated_shipdate between '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."'"; else $shipment_date ="";
	}
	
	$style_id_cond=""; $style_cond="";
	if($data[7]==1)
	{
		if (str_replace("'","",$data[5])!="") $style_id_cond=" and requisition_number_prefix_num='$data[5]'  $year_cond";
		if (trim($data[8])!="") $style_cond=" and style_ref_no='$data[8]'  "; //else  $style_cond=""; 
	}
	else if($data[7]==4 || $data[7]==0)
	{
		if (str_replace("'","",$data[5])!="") $style_id_cond=" and requisition_number_prefix_num like '%$data[5]%'  $year_cond"; //else  $job_cond=""; 
		if (trim($data[8])!="") $style_cond=" and style_ref_no like '%$data[8]%'  "; //else  $style_cond=""; 
	}
	else if($data[7]==2)
	{
		if (str_replace("'","",$data[5])!="") $style_id_cond=" and requisition_number_prefix_num like '$data[5]%'  $year_cond"; //else  $job_cond=""; 
		if (trim($data[8])!="") $style_cond=" and style_ref_no like '$data[8]%'  "; //else  $style_cond=""; 
	}
	else if($data[7]==3)
	{
		if (str_replace("'","",$data[5])!="") $style_id_cond=" and requisition_number_prefix_num like '%$data[5]'  $year_cond"; //else  $job_cond=""; 
		if (trim($data[8])!="") $style_cond=" and style_ref_no like '%$data[8]'  "; //else  $style_cond=""; 
	}
	if ($data[9]!=0) $brand_cond=" and brand_id='$data[9]'"; else $brand_cond = "";
	if ($data[10]!=0) $season_year_cond=" and season_year='$data[10]'"; else $season_year_cond = "";
	if ($data[11]!=0) $season_cond=" and season_buyer_wise='$data[11]'" ;else $season_cond = "";
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$brand_arr=return_library_array( "select id, brand_name from lib_buyer_brand",'id','brand_name');
	$season_arr=return_library_array( "select id, season_name from lib_buyer_season",'id','season_name');
	
	$arr=array(2=>$buyer_arr,3=>$brand_arr,5=>$season_arr);
	$sql= "select id, requisition_number_prefix_num, requisition_number, buyer_name, style_ref_no, estimated_shipdate, requisition_date, gauge_no_ends, efficiency, $insert_year_cond as year,brand_id,season_year,season_buyer_wise from sample_development_mst where status_active=1 and is_deleted=0 and entry_form_id=245 $shipment_date $company $buyer $style_id_cond $style_cond $year_cond $brand_cond $season_year_cond $season_cond order by id DESC";
	
	//echo $sql;
	echo  create_list_view("list_view", "Style ID,Year,Buyer Name,Brand, Season Year, Season, Style Ref. No,Est. Ship. Date,Sample Date,Gauge & No. Ends,Efficiency %", "60,50,120,90,90,90,120,70,70,110","1150","300",0, $sql , "js_set_value", "id", "", 1, "0,0,buyer_name,brand_id,0,season_buyer_wise,0,0,0,0,0", $arr , "requisition_number_prefix_num,year,buyer_name,brand_id,season_year,season_buyer_wise,style_ref_no,estimated_shipdate,requisition_date,gauge_no_ends,efficiency", "",'','0,0,0,0,0,0,0,3,3,0,0,0');
	
	exit();
} 
if ($action=="style_ref_popup")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
		/*function set_checkvalue()
		{
			if(document.getElementById('chk_style_dtls').value==0) document.getElementById('chk_style_dtls').value=1;
			else document.getElementById('chk_style_dtls').value=0;
		}*/
	
		function js_set_value( id )
		{
			document.getElementById('selected_id').value=id;
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
                	<th colspan="10" align="center"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --",1 ); ?></th>
                </tr>
                <tr>
                    
                    <th>Buyer Name</th>
                    <th>Style Ref </th>
                    <th>Job No.</th>
                    <th colspan="2">Date Range</th>
                    <th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:80px" class="formbutton"  />
                     <input type="hidden" id="selected_id">
                    </th>
                </tr>        
            </thead>
            <tr class="general">
                
        		<td id="buyer_pop_td">
				
				<? echo create_drop_down( "cbo_buyer_id", 150, $blank_array,'', 1, "-- Select Buyer --" ); ?></td>
        		 <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:80px"></td>
                <td><input name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:80px"></td>                
               
               
                <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date"></td>
                <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To Date"></td> 
                <td align="center">
                	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( <? echo $cbo_company_name; ?>+'_'+document.getElementById('cbo_buyer_id').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_string_search_type').value, 'create_style_ref_search_list_view', 'search_div', 'time_weight_record_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" /></td>
        	</tr>
            <tr>
                <td align="center" colspan="10"><? echo load_month_buttons(1); ?></td>
            </tr>
        </table>
        <div id="search_div"></div>
    </form>
    </div>
    </body>
    <script>
		load_drop_down('time_weight_record_controller', <? echo $cbo_company_name; ?>, 'load_drop_down_buyer_pop', 'buyer_pop_td' );
		<? if($cbo_buyer_name !=0)
		{ ?>
			 
			
			document.getElementById('cbo_buyer_id').value=<? echo $cbo_buyer_name; ?>
		<? } ?>
		
    </script>         
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if($action=="create_style_ref_search_list_view")
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
		else $buyer="";
	}
	else $buyer=" and a.buyer_name='$data[1]'";
	
	//if ($data[1]!=0) $buyer=" and a.buyer_name='$data[1]'"; else $buyer="";//{ echo "Please Select Buyer First."; die; }
	
	if($db_type==0)
	{
		$year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$data[6]";
		$insert_year_cond="SUBSTRING_INDEX(a.insert_date, '-', 1)";
		if ($data[4]!="" &&  $data[5]!="") $shipment_date = "and b.shipment_date between '".change_date_format($data[4], "yyyy-mm-dd", "-")."' and '".change_date_format($data[5], "yyyy-mm-dd", "-")."'"; else $shipment_date ="";
	}
	else if($db_type==2)
	{
		$year_cond=" and to_char(a.insert_date,'YYYY')=$data[6]";
		$insert_year_cond="to_char(a.insert_date,'YYYY')";
		if ($data[4]!="" &&  $data[5]!="") $shipment_date = "and b.shipment_date between '".change_date_format($data[4], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[5], "yyyy-mm-dd", "-",1)."'"; else $shipment_date ="";
	}
	
	$style_id_cond=""; $style_cond="";
	if($data[7]==1)
	{
		if (str_replace("'","",$data[3])!="") $style_id_cond=" and a.job_no_prefix_num='$data[3]'  $year_cond";
		if (trim($data[2])!="") $style_cond=" and a.style_ref_no='$data[2]'  "; //else  $style_cond=""; 
	}
	else if($data[7]==4 || $data[7]==0)
	{
		if (str_replace("'","",$data[3])!="") $style_id_cond=" and a.job_no_prefix_num like '%$data[3]%'  $year_cond"; //else  $job_cond=""; 
		if (trim($data[2])!="") $style_cond=" and a.style_ref_no like '%$data[2]%'  "; //else  $style_cond=""; 
	}
	else if($data[7]==2)
	{
		if (str_replace("'","",$data[3])!="") $style_id_cond=" and a.job_no_prefix_num like '$data[3]%'  $year_cond"; //else  $job_cond=""; 
		if (trim($data[2])!="") $style_cond=" and a.style_ref_no like '$data[2]%'  "; //else  $style_cond=""; 
	}
	else if($data[7]==3)
	{
		if (str_replace("'","",$data[3])!="") $style_id_cond=" and a.job_no_prefix_num like '%$data[3]'  $year_cond"; //else  $job_cond=""; 
		if (trim($data[2])!="") $style_cond=" and a.style_ref_no like '%$data[2]'  "; //else  $style_cond=""; 
	}
	$buyer_Array=return_library_array( "select id, buyer_name from lib_buyer","id","buyer_name");
	$arr=array(0=>$buyer_Array);
	$sql= "select a.id, a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no, $insert_year_cond as year from wo_po_details_master a,wo_po_break_down b where a.id=b.job_id and a.status_active=1 and a.is_deleted=0  $shipment_date $company $buyer $style_id_cond $style_cond $year_cond group by a.id, a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no,a.insert_date order by a.id DESC";
	
	// echo $sql;
	echo  create_list_view("list_view", "Buyer,Style Ref,Job No", "130,130,130","420","300",0, $sql , "js_set_value", "id", "", 1, "buyer_name,0,0", $arr , "buyer_name,style_ref_no,job_no", "",'','0,0,0');
	
	exit();
} 
if ($action=="populate_data_from_order_popup")
{
$data_array=sql_select("select id, garments_nature, buyer_name, style_ref_no,product_dept, product_category,product_code, gmts_item_id, team_leader, dealing_marchant,season_buyer_wise,  season_year, brand_id from wo_po_details_master where id='$data' and is_deleted=0 and status_active=1");
//echo "select id, garments_nature, buyer_name, style_ref_no,product_dept, product_code, gmts_item_id, team_leader, dealing_marchant,  season_year, brand_id from wo_po_details_master where id='$data' and is_deleted=0 and status_active=1";

	foreach ($data_array as $row)
	{
		echo "document.getElementById('cbo_team_leader').value = '".$row[csf("team_leader")]."';\n";  //cbo_dealing_merchant
		echo "document.getElementById('txt_style_ref').value = '".$row[csf("style_ref_no")]."';\n";  
		echo "load_drop_down( 'requires/time_weight_record_controller', '".$row[csf("team_leader")]."', 'cbo_dealing_merchant', 'div_marchant' ) ;\n";
		
		
		echo "document.getElementById('cbo_dealing_merchant').value = '".$row[csf("dealing_marchant")]."';\n";  
		echo "document.getElementById('txt_product_code').value = '".$row[csf("product_code")]."';\n";
		echo "document.getElementById('cbo_product_department').value = '".$row[csf("product_dept")]."';\n";
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_name")]."';\n";  
		echo "load_drop_down( 'requires/time_weight_record_controller', '".$row[csf("buyer_name")]."', 'load_drop_down_season', 'season_td');\n";
		echo "document.getElementById('cbo_brand_id').value = '".$row[csf("brand_id")]."';\n";
		echo "document.getElementById('cbo_season_year').value = '".$row[csf("season_year")]."';\n";
		echo "document.getElementById('cbo_season_id').value = '".$row[csf("season_buyer_wise")]."';\n";
		echo "document.getElementById('cbo_gmts_item').value = '".$row[csf("gmts_item_id")]."';\n"; 
		echo "document.getElementById('cbo_item_catgory').value = '".$row[csf("product_category")]."';\n"; 
		
		
	}
exit();	
}
if ($action=="populate_data_from_search_popup")
{
	/*echo "select id, garments_nature, job_no, job_no_prefix, job_no_prefix_num, company_name, buyer_name, location_name, style_ref_no, style_description, product_dept, product_code, pro_sub_dep, currency_id, agent_name, client_id, order_repeat_no, region, product_category, team_leader, dealing_marchant, packing, remarks, ship_mode, order_uom, set_break_down, gmts_item_id, total_set_qnty, set_smv, season_buyer_wise, quotation_id, job_quantity, order_uom, avg_unit_price, currency_id, total_price, factory_marchant
 from wo_po_details_master where job_no='$data'";die;*/
	$data_array=sql_select("select id, garments_nature, entry_form_id, requisition_number, requisition_number_prefix, requisition_number_prefix_num, company_id, location_id, buyer_name, style_ref_no,mers_style, product_dept, product_code, item_category, article_no, item_name, region, agent_name, season_buyer_wise, team_leader, dealing_marchant, bh_merchant, remarks, req_ready_to_approved, estimated_shipdate, requisition_date, gauge_no_ends, efficiency, season_year, brand_id, uom from sample_development_mst where id='$data' and is_deleted=0 and status_active=1");
	foreach ($data_array as $row)
	{
		echo "load_drop_down( 'requires/time_weight_record_controller', '".$row[csf("team_leader")]."', 'cbo_dealing_merchant', 'div_marchant' ) ;\n";
		//echo "load_drop_down( 'requires/time_weight_record_controller', '".$row[csf("team_leader")]."', 'cbo_factory_merchant', 'div_marchant_factory' ) ;\n";
		
		echo "load_drop_down( 'requires/time_weight_record_controller', '".$row[csf("buyer_name")]."', 'load_drop_down_season', 'season_td');\n";
		echo "load_drop_down( 'requires/time_weight_record_controller', '".$row[csf("buyer_name")]."', 'load_drop_down_brand', 'brand_td');\n";
		//echo "document.getElementById('garments_nature').value = '".$row[csf("garments_nature")]."';\n";  
		echo "document.getElementById('txt_style_no').value = '".$row[csf("requisition_number")]."';\n";  
		echo "document.getElementById('txt_mers_style').value = '".$row[csf("mers_style")]."';\n";  
		echo "document.getElementById('cbo_location_name').value = '".$row[csf("location_id")]."';\n";  
		echo "document.getElementById('cbo_buyer_name').value = '".$row[csf("buyer_name")]."';\n";  
		echo "document.getElementById('txt_style_ref').value = '".$row[csf("style_ref_no")]."';\n";  
		echo "document.getElementById('cbo_product_department').value = '".$row[csf("product_dept")]."';\n";
		echo "document.getElementById('txt_product_code').value = '".$row[csf("product_code")]."';\n";
		echo "document.getElementById('txt_artical_no').value = '".$row[csf("article_no")]."';\n";  
		echo "document.getElementById('cbo_gmts_item').value = '".$row[csf("item_name")]."';\n"; 
		echo "document.getElementById('cbo_item_catgory').value = '".$row[csf("item_category")]."';\n"; 
		echo "document.getElementById('cbo_kniting_uom').value = '".$row[csf("uom")]."';\n"; 
		echo "fnc_uomchnage('".$row[csf("uom")]."');\n";
		echo "$('#cbo_kniting_uom').attr('disabled',true);\n";
		echo "$('#txt_style_ref').attr('disabled',true);\n";
		echo "document.getElementById('cbo_region').value = '".$row[csf("region")]."';\n";  
		echo "document.getElementById('cbo_agent').value = '".$row[csf("agent_name")]."';\n";  
		echo "document.getElementById('cbo_season_id').value = '".$row[csf("season_buyer_wise")]."';\n";  
		echo "document.getElementById('cbo_team_leader').value = '".$row[csf("team_leader")]."';\n";  
		echo "document.getElementById('cbo_dealing_merchant').value = '".$row[csf("dealing_marchant")]."';\n";  
		echo "document.getElementById('txt_bh_merchant').value = '".$row[csf("bh_merchant")]."';\n";  
		echo "document.getElementById('txt_est_ship_date').value = '".change_date_format($row[csf("estimated_shipdate")])."';\n";  
		echo "document.getElementById('txt_gause').value = '".$row[csf("gauge_no_ends")]."';\n";
		echo "document.getElementById('txt_sample_date').value = '".change_date_format($row[csf("requisition_date")])."';\n";
		echo "document.getElementById('txt_efficiency').value = '".$row[csf("efficiency")]."';\n";
		echo "document.getElementById('cbo_brand_id').value = '".$row[csf("brand_id")]."';\n";
		echo "document.getElementById('cbo_season_year').value = '".$row[csf("season_year")]."';\n";
		echo "document.getElementById('txt_remarks').value = '".$row[csf("remarks")]."';\n";
		echo "document.getElementById('cbo_ready_to_approved').value = '".$row[csf("req_ready_to_approved")]."';\n";
		
		echo "document.getElementById('update_id').value = '".$row[csf("id")]."';\n"; 
	}
	
	$dtls_arr=sql_select("select id, sample_name, acc_status_id, color_id, size_id, designer, tech_manager, programmer, yarn_quality, count_ply, minute, second, movingsec, knitinggm, critical_point, knitingweight,knitting_system,machine_brand_name from sample_development_dtls where sample_mst_id='$data' and is_deleted=0 and status_active=1");
	foreach ($dtls_arr as $row)
	{
		echo "document.getElementById('cbo_sample_type').value = '".$row[csf("sample_name")]."';\n";  
		echo "document.getElementById('cbo_dev_no').value = '".$row[csf("acc_status_id")]."';\n";  
		echo "document.getElementById('txt_sample_color').value = '".$color_library[$row[csf("color_id")]]."';\n";  
		echo "document.getElementById('txt_sample_size').value = '".$size_library[$row[csf("size_id")]]."';\n";  
		echo "document.getElementById('txt_designer').value = '".$row[csf("designer")]."';\n";  
		echo "document.getElementById('txt_asst_tech_manager').value = '".$row[csf("tech_manager")]."';\n";  
		echo "document.getElementById('txt_programmer').value = '".$row[csf("programmer")]."';\n";  
		echo "document.getElementById('txt_yarn_quality').value = '".$row[csf("yarn_quality")]."';\n";
		echo "document.getElementById('txt_count_ply').value = '".$row[csf("count_ply")]."';\n";
		echo "document.getElementById('txtminute_tot').value = '".$row[csf("minute")]."';\n";
		echo "document.getElementById('txtsecond_tot').value = '".$row[csf("second")]."';\n";
		
		echo "document.getElementById('txtmovingsec_tot').value = '".$row[csf("movingsec")]."';\n";
		echo "document.getElementById('txtknitinggm_tot').value = '".$row[csf("knitinggm")]."';\n";
		echo "document.getElementById('txtcritical_point').value = '".$row[csf("critical_point")]."';\n";
		echo "document.getElementById('txtknitingweight_dzn').value = '".$row[csf("knitingweight")]."';\n";
		echo "document.getElementById('txt_knitting_system').value = '".$row[csf("knitting_system")]."';\n";
		echo "document.getElementById('txt_machine_brand_name').value = '".$row[csf("machine_brand_name")]."';\n";
		
		echo "document.getElementById('updatedtls_id').value = '".$row[csf("id")]."';\n"; 
	}
	
	$dtls_rarr=sql_select("select id, body_part_id, minute, second, movingsec, knitinggm from sample_development_fabric_acc where sample_mst_id='$data' and is_deleted=0 and status_active=1 order by id asc");
	$i=1;
	foreach ($dtls_rarr as $row)
	{
		//echo "document.getElementById('txtpanelupid_'".$i.").value = '".$row[csf("id")]."';\n";  
		//echo "document.getElementById('txtpanelid_'".$i.").value = '".$row[csf("body_part_id")]."';\n";  
		echo "document.getElementById('txtminute_".$i."').value = '".$row[csf("minute")]."';\n";  
		echo "document.getElementById('txtsecond_".$i."').value = '".$row[csf("second")]."';\n";  
		echo "document.getElementById('txtmovingsec_".$i."').value = '".$row[csf("movingsec")]."';\n";  
		echo "document.getElementById('txtknitinggm_".$i."').value = '".$row[csf("knitinggm")]."';\n";  
		
		$i++; 
	}
	exit();
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
		if (is_duplicate_field( "a.requisition_number", "sample_development_mst a, sample_development_dtls b", " a.id=b.sample_mst_id and a.buyer_name=$cbo_buyer_name and a.style_ref_no=$txt_style_ref and b.sample_name=$cbo_sample_type and b.acc_status_id=$cbo_dev_no and a.status_active=1 and a.is_deleted=0 and a.status_active=1 and a.is_deleted=0" ) == 1)
		{
			echo "11**0"; 
			die;
		}
		//if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		$id=return_next_id("id", "sample_development_mst", 1);
		//echo "10**";
		if($db_type==0) $date_cond=" YEAR(insert_date)"; else if($db_type==2) $date_cond="to_char(insert_date,'YYYY')";
			
		$new_sample_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'SMP', date("Y",time()), 5, "select requisition_number_prefix, requisition_number_prefix_num from sample_development_mst where company_id=$cbo_company_name and entry_form_id=245 and $date_cond=".date('Y',time())." order by id DESC", "requisition_number_prefix", "requisition_number_prefix_num"));
		
		$field_array_mst="id, garments_nature, entry_form_id, requisition_number, requisition_number_prefix, requisition_number_prefix_num, company_id, location_id, buyer_name, style_ref_no,mers_style, product_dept, product_code, item_category, article_no, item_name, region, agent_name, season_buyer_wise, team_leader, dealing_marchant, bh_merchant, remarks, req_ready_to_approved, estimated_shipdate, requisition_date, gauge_no_ends, efficiency, brand_id, season_year, uom, inserted_by, insert_date, status_active, is_deleted";
		$txt_mers_style=str_replace("'", "", $txt_mers_style);
		$data_array_mst="(".$id.",".$garments_nature.",'245','".$new_sample_no[0]."','".$new_sample_no[1]."','".$new_sample_no[2]."',".$cbo_company_name.",".$cbo_location_name.",".$cbo_buyer_name.",".$txt_style_ref.",'".$txt_mers_style."',".$cbo_product_department.",".$txt_product_code.",".$cbo_item_catgory.",".$txt_artical_no.",".$cbo_gmts_item.",".$cbo_region.",".$cbo_agent.",".$cbo_season_id.",".$cbo_team_leader.",".$cbo_dealing_merchant.",".$txt_bh_merchant.",".$txt_remarks.",".$cbo_ready_to_approved.",".$txt_est_ship_date.",".$txt_sample_date.",".$txt_gause.",".$txt_efficiency.",".$cbo_brand_id.",".$cbo_season_year.",".$cbo_kniting_uom.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
		
		$id_dtls=return_next_id("id", "sample_development_dtls", 1);
		
		if(str_replace("'","",$txt_sample_color)!="")
		{ 
			if (!in_array(str_replace("'","",$txt_sample_color),$new_array_color))
			{
				$color_id = return_id( str_replace("'","",$txt_sample_color), $color_library, "lib_color", "id,color_name","245");  
				$new_array_color[$color_id]=str_replace("'","",$txt_sample_color);
			}
			else $color_id =  array_search(str_replace("'","",$txt_sample_color), $new_array_color); 
		}
		else $color_id=0;
		
		if(str_replace("'","",$txt_sample_size)!="")
		{ 
			if (!in_array(str_replace("'","",$txt_sample_size),$new_array_size))
			{
				$size_id = return_id( str_replace("'","",$txt_sample_size), $size_library, "lib_size", "id,size_name","245");  
				$new_array_size[$size_id]=str_replace("'","",$txt_sample_size);
			}
			else $size_id =  array_search(str_replace("'","",$txt_sample_size), $new_array_size); 
		}
		else $size_id=0;
		
		$field_array_dtls="id, sample_mst_id, sample_name, acc_status_id, entry_form_id, color_id, size_id, designer, tech_manager, programmer, yarn_quality, count_ply, minute, second, movingsec, knitinggm, critical_point, knitingweight, knitting_system, machine_brand_name, inserted_by, insert_date, is_deleted, status_active";
		$data_array_dtls="(".$id_dtls.",".$id.",".$cbo_sample_type.",".$cbo_dev_no.",'245','".$color_id."','".$size_id."',".$txt_designer.",".$txt_asst_tech_manager.",".$txt_programmer.",".$txt_yarn_quality.",".$txt_count_ply.",".$txtminute_tot.",".$txtsecond_tot.",".$txtmovingsec_tot.",".$txtknitinggm_tot.",".$txtcritical_point.",".$txtknitingweight_dzn.",".$txt_knitting_system.",".$txt_machine_brand_name.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',0,1)";
		//echo "10**INSERT INTO sample_development_dtls (".$field_array.") VALUES ".$data_array; die;
		$field_array1="id, sample_mst_id, sample_dtls_id, body_part_id, minute, second, movingsec, knitinggm, inserted_by, insert_date, is_deleted, status_active";
		
		$id1=return_next_id("id", "sample_development_fabric_acc", 1);
		$add_comma=0; $data_array1='';
		for($m=1; $m<=$panel_tr; $m++)
		{
			$txtpanelupid="txtpanelupid_".$m;
			$txtpanelid="txtpanelid_".$m;
			$txtminute="txtminute_".$m;
			$txtsecond="txtsecond_".$m;
			$txtmovingsec="txtmovingsec_".$m;
			$txtknitinggm="txtknitinggm_".$m;
			
			if ($add_comma!=0) $data_array1 .=",";
			$data_array1 .="(".$id1.",".$id.",".$id_dtls.",'".$$txtpanelid."','".$$txtminute."','".$$txtsecond."','".$$txtmovingsec."','".$$txtknitinggm."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',0,1)";
			
			$id1=$id1+1;
			$add_comma++;
		}
		$flag=1;
		//echo "INSERT INTO sample_development_mst (".$field_array.") VALUES ".$data_array; die;
		
		$rID=sql_insert("sample_development_mst",$field_array_mst,$data_array_mst,0);
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		
		//echo "10**INSERT INTO wo_po_color_size_breakdown (".$field_array1.") VALUES ".$data_array1; die;
		$rID1=sql_insert("sample_development_dtls",$field_array_dtls,$data_array_dtls,0);	
		if($rID1==1 && $flag==1) $flag=1; else $flag=0;
		
		$rID2=sql_insert("sample_development_fabric_acc",$field_array1,$data_array1,0);
		if($rID2==1 && $flag==1) $flag=1; else $flag=0;
		//echo "10**".$flag.'='.$rID.'='.$rID1.'='.$rID2; die;
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "0**".$new_sample_no[0]."**".$id."**".$id_dtls;
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
				echo "0**".$new_sample_no[0]."**".$id."**".$id_dtls;
			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}
		//check_table_status( $_SESSION['menu_id'],0);
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
		
		$used_sample=0;
		$sqlre=sql_select("select job_no from wo_pre_cost_fabric_cost_dtls where sample_id=$update_id and status_active=1 and is_deleted=0 group by job_no");
		foreach($sqlre as $rows){
			$used_sample=$rows[csf('job_no')];
		}
		if($used_sample){
			echo "budget**".str_replace("'","",$txt_style_no)."**".$used_sample;
			die;
		}
		
		//if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**1"; die;}
		if (is_duplicate_field( "a.requisition_number", "sample_development_mst a, sample_development_dtls b", " a.id=b.sample_mst_id and a.buyer_name=$cbo_buyer_name and a.style_ref_no=$txt_style_ref and b.sample_name=$cbo_sample_type and b.acc_status_id=$cbo_dev_no and a.id!=$update_id and a.status_active=1 and a.is_deleted=0 and a.status_active=1 and a.is_deleted=0" ) == 1)
		{
			echo "11**0"; 
			die;
		}	
		$field_array_mst="location_id*buyer_name*style_ref_no*mers_style*product_dept*product_code*item_category*article_no*item_name*region*agent_name*season_buyer_wise*team_leader*dealing_marchant*bh_merchant*remarks*req_ready_to_approved*estimated_shipdate*requisition_date*gauge_no_ends*efficiency*brand_id*season_year*uom*updated_by*update_date";

		$data_array_mst="".$cbo_location_name."*".$cbo_buyer_name."*".$txt_style_ref."*".$txt_mers_style."*".$cbo_product_department."*".$txt_product_code."*".$cbo_item_catgory."*".$txt_artical_no."*".$cbo_gmts_item."*".$cbo_region."*".$cbo_agent."*".$cbo_season_id."*".$cbo_team_leader."*".$cbo_dealing_merchant."*".$txt_bh_merchant."*".$txt_remarks."*".$cbo_ready_to_approved."*".$txt_est_ship_date."*".$txt_sample_date."*".$txt_gause."*".$txt_efficiency."*".$cbo_brand_id."*".$cbo_season_year."*".$cbo_kniting_uom."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		if(str_replace("'","",$txt_sample_color)!="")
		{ 
			if (!in_array(str_replace("'","",$txt_sample_color),$new_array_color))
			{
				$color_id = return_id( str_replace("'","",$txt_sample_color), $color_library, "lib_color", "id,color_name","245");  
				$new_array_color[$color_id]=str_replace("'","",$txt_sample_color);
			}
			else $color_id =  array_search(str_replace("'","",$txt_sample_color), $new_array_color); 
		}
		else $color_id=0;
		
		if(str_replace("'","",$txt_sample_size)!="")
		{ 
			if (!in_array(str_replace("'","",$txt_sample_size),$new_array_size))
			{
				$size_id = return_id( str_replace("'","",$txt_sample_size), $size_library, "lib_size", "id,size_name","245");  
				$new_array_size[$size_id]=str_replace("'","",$txt_sample_size);
			}
			else $size_id =  array_search(str_replace("'","",$txt_sample_size), $new_array_size); 
		}
		else $size_id=0;
		
		$field_array_dtls="sample_name*acc_status_id*color_id*size_id*designer*tech_manager*programmer*yarn_quality*count_ply*minute*second*movingsec*knitinggm*critical_point*knitingweight*knitting_system*machine_brand_name*updated_by*update_date";
		
		$data_array_dtls="".$cbo_sample_type."*".$cbo_dev_no."*'".$color_id."'*'".$size_id."'*".$txt_designer."*".$txt_asst_tech_manager."*".$txt_programmer."*".$txt_yarn_quality."*".$txt_count_ply."*".$txtminute_tot."*".$txtsecond_tot."*".$txtmovingsec_tot."*".$txtknitinggm_tot."*".$txtcritical_point."*".$txtknitingweight_dzn."*".$txt_knitting_system."*".$txt_machine_brand_name."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		$field_array1="id, sample_mst_id, sample_dtls_id, body_part_id, minute, second, movingsec, knitinggm, inserted_by, insert_date, is_deleted, status_active";
		
		$id1=return_next_id("id", "sample_development_fabric_acc", 1);
		$add_comma=0; $data_array1='';
		for($m=1; $m<=$panel_tr; $m++)
		{
			$txtpanelupid="txtpanelupid_".$m;
			$txtpanelid="txtpanelid_".$m;
			$txtminute="txtminute_".$m;
			$txtsecond="txtsecond_".$m;
			$txtmovingsec="txtmovingsec_".$m;
			$txtknitinggm="txtknitinggm_".$m;
			
			if ($add_comma!=0) $data_array1 .=",";
			$data_array1 .="(".$id1.",".$update_id.",".$updatedtls_id.",'".$$txtpanelid."','".$$txtminute."','".$$txtsecond."','".$$txtmovingsec."','".$$txtknitinggm."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',0,1)";
			
			$id1=$id1+1;
			$add_comma++;
		}
		
		$flag=1;
		
		$rID=sql_update("sample_development_mst",$field_array_mst,$data_array_mst,"id","".$update_id."",0);
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		
		$rID1=sql_update("sample_development_dtls",$field_array_dtls,$data_array_dtls,"id","".$updatedtls_id."",1);
		if($rID1==1 && $flag==1) $flag=1; else $flag=0;
		
		$rID2=execute_query("delete from sample_development_fabric_acc where sample_dtls_id =".$updatedtls_id."",0);
		if($rID2==1 && $flag==1) $flag=1; else $flag=0;
		//echo "10**INSERT INTO sample_development_fabric_acc (".$field_array1.") VALUES ".$data_array1; die;
		$rID3=sql_insert("sample_development_fabric_acc",$field_array1,$data_array1,0);
		if($rID3==1 && $flag==1) $flag=1; else $flag=0;
		
		//echo "10**".$flag.'='.$rID.'='.$rID1.'='.$rID2.'='.$rID3; die;
		//echo "10**";
		
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'",'',$txt_style_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$updatedtls_id);
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
				echo "1**".str_replace("'",'',$txt_style_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$updatedtls_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}
		//check_table_status( $_SESSION['menu_id'],0);
		disconnect($con);
		die;
	}
	else if ($operation==2) // Delete Here----------------------------------------------------------  
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$used_sample=0;
		$sqlre=sql_select("select job_no from wo_pre_cost_fabric_cost_dtls where sample_id=$update_id and status_active=1 and is_deleted=0 group by job_no");
		foreach($sqlre as $rows){
			$used_sample=$rows[csf('job_no')];
		}
		if($used_sample){
			echo "budget**".str_replace("'","",$txt_style_no)."**".$used_sample;
			die;
		}
		$flag=1;
		$field_array="status_active*is_deleted*updated_by*update_date";
		$data_array="'0'*'1'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		$rID=sql_delete("sample_development_mst",$field_array,$data_array,"id","".$update_id."",0);
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		
		$rID1=sql_delete("sample_development_dtls",$field_array,$data_array,"sample_mst_id","".$update_id."",0);
		if($rID1==1 && $flag==1) $flag=1; else $flag=0;
		
		$rID2=sql_delete("sample_development_fabric_acc",$field_array,$data_array,"sample_mst_id","".$update_id."",0);
		if($rID2==1 && $flag==1) $flag=1; else $flag=0;
		
		$rID3=sql_delete("sample_development_rf_color",$field_array,$data_array,"mst_id","".$update_id."",0);
		if($rID3==1 && $flag==1) $flag=1; else $flag=0;
		
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'",'',$txt_style_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$updatedtls_id);
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
				echo "2**".str_replace("'",'',$txt_style_no)."**".str_replace("'",'',$update_id)."**".str_replace("'",'',$updatedtls_id);
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

if($action=="special_comments")
{
	echo load_html_head_contents("Critical Points / Special Comments","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$ex_data=explode("___",$data);
	?>
		<script>
            function js_set_value()
            {
               $('#txtcritical_pointknitting').val();
			   $('#txtcritical_pointlinking').val();
			   $('#txtcritical_pointwashing').val();
			   $('#txtcritical_pointaddons').val();
			   $('#txtcritical_pointfinishing').val();
               parent.emailwindow.hide();
            }
        </script>
    </head>
    <body>
    <div align="center">
        <fieldset style="width:400px;margin-left:4px;">
            <form name="remarksfrm_1"  id="remarksfrm_1" autocomplete="off">
                <table cellpadding="2" cellspacing="0" width="370" >
                    <tr>
                    	<td width="70">Knitting</td>
                        <td align="center">
                          <textarea id="txtcritical_pointknitting" name="txtcritical_pointknitting" class="text_area" title="Maximum 1000 Character" maxlength="1000" style="width:300px; height:50px" placeholder="Knitting Critical Points / Special Comments Here. Maximum 1000 Character." ><? echo $ex_data[0]; ?></textarea>
                        </td>
                    </tr>
                    <tr>
                    	<td>Linking</td>
                        <td align="center">
                          <textarea id="txtcritical_pointlinking" name="txtcritical_pointlinking" class="text_area" title="Maximum 1000 Character" maxlength="1000" style="width:300px; height:50px" placeholder="Linking Critical Points / Special Comments Here. Maximum 1000 Character." ><? echo $ex_data[1]; ?></textarea>
                        </td>
                    </tr>
                    <tr>
                    	<td>Washing</td>
                        <td align="center">
                          <textarea id="txtcritical_pointwashing" name="txtcritical_pointwashing" class="text_area" title="Maximum 1000 Character" maxlength="1000" style="width:300px; height:50px" placeholder="Washing Critical Points / Special Comments Here. Maximum 1000 Character." ><? echo $ex_data[2]; ?></textarea>
                        </td>
                    </tr>
                    <tr>
                    	<td>Add Ons</td>
                        <td align="center">
                          <textarea id="txtcritical_pointaddons" name="txtcritical_pointaddons" class="text_area" title="Maximum 1000 Character" maxlength="1000" style="width:300px; height:50px" placeholder="Add Ons Critical Points / Special Comments Here. Maximum 1000 Character." ><? echo $ex_data[3]; ?></textarea>
                        </td>
                    </tr>
                    <tr>
                    	<td>Finishing</td>
                        <td align="center">
                          <textarea id="txtcritical_pointfinishing" name="txtcritical_pointfinishing" class="text_area" title="Maximum 1000 Character" maxlength="1000" style="width:300px; height:50px" placeholder="Finishing Critical Points / Special Comments Here. Maximum 1000 Character." ><? echo $ex_data[4]; ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" colspan="2">
                     <input type="button" id="formbuttonplasminus" align="middle" class="formbutton" style="width:100px" value="Close" onClick="js_set_value();" />
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

// Master Form End ***************************************** Master Form End******************************************

if($action=="color_popup")
{
	echo load_html_head_contents("Consumption Entry","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script> 
		function js_set_value(data)
		{
			document.getElementById('color_name').value=data;
			parent.emailwindow.hide();
		}
	</script> 
	</head>
	<body>
        <div align="center">
        <form>
            <input type="hidden" id="color_name" name="color_name" />
            <?
            if($buyer_name=="" || $buyer_name==0)
            {
            	$sql="select id, color_name FROM lib_color  WHERE status_active=1 and is_deleted=0"; 
            }
            else
            {
            	$sql="select a.id, a.color_name FROM lib_color a, lib_color_tag_buyer b WHERE a.id=b.color_id and b.buyer_id=$buyer_name and status_active=1 and is_deleted=0"; 
            }
            echo  create_list_view("list_view", "Color Name", "160","210","420",0, $sql , "js_set_value", "color_name", "", 1, "0", $arr , "color_name", "",'setFilterGrid("list_view",-1);','0') ;
            ?>
        </form>
        </div>
	</body>
	</html>
	<?
	exit();
}

if($action=="color_body_part_dtls_list")
{
	$ex_data=explode("_",$data);
	$mst_id=$ex_data[0];
	$dtls_id=$ex_data[1];
	
	$colordtls_data=sql_select("select id, color_id, body_part_id, bodycolor from sample_development_rf_color where mst_id='$mst_id' and dtls_id='$dtls_id' and is_deleted=0 and status_active=1 order by id asc");
	//echo "select id, color_id, body_part_id, bodycolor from sample_development_rf_color where mst_id='$mst_id' and dtls_id='$dtls_id' and is_deleted=0 and status_active=1 order by id asc";
	$color_data_arr=array(); $bodycolor_arr=array(); $x=0; $save_update=0;
	foreach($colordtls_data as $row)
	{
		$save_update=1;
		$color_data_arr[$row[csf("color_id")]][$row[csf("body_part_id")]]['cons']=$row[csf("bodycolor")];
		if(!in_array($row[csf("color_id")],$temp_arr_color))
		{
			if($row[csf("color_id")]!=0)
			{
				$x++;
				$bodycolor_arr[$x]=$row[csf("color_id")];
			}
			$temp_arr_color[]=$row[csf("color_id")];
		}
	}
	//print_r($bodycolor_arr);
	
	$body_data_arr=array();
	$dtls_rarr=sql_select("select id, body_part_id from sample_development_fabric_acc where sample_dtls_id='$dtls_id' and knitinggm is not null and is_deleted=0 and status_active=1 order by id asc");
	//echo "select id, body_part_id from sample_development_fabric_acc where sample_dtls_id='$dtls_id' and knitinggm is not null and is_deleted=0 and status_active=1 order by id asc";
	$i=0;
	foreach($dtls_rarr as $row)
	{
		$i++;
		$body_data_arr[$i]=$row[csf("body_part_id")];
	}
	unset($dtls_rarr);
	
	$tbl_width=80*$i;
	?>
    <h3 style="width:<? echo $tbl_width+270; ?>px;" align="left" class="accordion_h">+Sample Color & Body Part Details Entry</h3>
       <div id="color_body_part">
    	<fieldset style="width:<? echo $tbl_width+270; ?>px;">
        	<form id="timeweightentry_3" autocomplete="off">
            <table width="<? echo $tbl_width+270; ?>" cellspacing="0" class="rpt_table" border="0" id="tbl_color_body_part" rules="all">
                <thead>
                    <tr>
                        <th width="30">SL<input type="hidden" id="txtbodyc" name="txtbodyc" class="text_boxes" style="width:20px" value="<? echo $i; ?>" /></th>
                        <th width="80">Color</th>
                        <?
						foreach ($body_data_arr as $id=>$bid )
						{
							?>
                            <th width="80"><? echo $time_weight_panel[$bid]; ?><input type="hidden" id="bodypartid_<? echo $id; ?>" value="<? echo $bid; ?>"></th>
                            <?
						}
						?>
                        
                        <th width="80">Total</th>
                        <th>Color %</th>
                    </tr>
                </thead>
                <tbody>
                <?
				$j=14;
				for($m=1; $m<=$j; $m++)
				{
					$color_id='';
					$color_id=$bodycolor_arr[$m];
					?>
                    <tr id="colortr_<? echo $m; ?>" align="center">
                        <td><? echo $m; ?></td>
                        <td><input type="text" id="txtcolor_<? echo $m; ?>" name="txtcolor_<? echo $m; ?>" class="text_boxes" style="width:70px" value="<? echo $color_library[$color_id]; ?>" /></td>
                        <? $k=1;
						foreach ($body_data_arr as $id=>$bid )
						{
							$cons=0;
							$cons=$color_data_arr[$color_id][$bid]['cons'];
							?>
                        	<td><input type="text" id="txtbodycolor_<? echo $m.'_'.$k; ?>" name="txtbodycolor_<? echo $m.'_'.$k; ?>" class="text_boxes_numeric" style="width:68px" value="<? echo $cons; ?>" onChange="fnc_tot_per_cal();" /></td>
                        	<?
							$k++;
						}
						?>
                        <td><input type="text" id="txtbodycolorval_<? echo $m; ?>" name="txtbodycolorval_<? echo $m; ?>" class="text_boxes_numeric" style="width:68px" value="" disabled /></td>
                        <td><input type="text" id="txtbodycolorper_<? echo $m; ?>" name="txtbodycolorper_<? echo $m; ?>" class="text_boxes_numeric" style="width:68px" value="" disabled /></td>
                    </tr>
				<?
                }
            	?>
        	</tbody>
            <tfoot>
            	<tr align="center" class="general">
                    <td>&nbsp;</td>
                    <td>Total:</td>
                    <?
                    for($k=1; $k<=$i; $k++)
                    {
                        ?>
                        <td><input type="text" id="txtbodytot_<? echo $k; ?>" name="txtbodytot_<? echo $k; ?>" class="text_boxes_numeric" style="width:68px" value="<? echo $row[csf("gsm_weight_yarn")]; ?>" total_value="" readonly /></td>
                        <?
                    }
                    ?>
                    <td><input type="text" id="txtcolortot" name="txtcolortot" class="text_boxes_numeric" style="width:68px" value="" readonly /></td>
                    <td><input type="text" id="txttotper" name="txttotper" class="text_boxes_numeric" style="width:68px" value="" readonly /></td>
                </tr>
            </tfoot>
        </table>
        <br/>
        <table width="<? echo $tbl_width+270; ?>" cellspacing="0" class="" border="0">
            <tr>
                <td align="center" class="button_container">
                	<? echo load_submit_buttons( $permission, "fnc_color_dtls", $save_update,0,"reset_form('timeweightentry_3','','',0)",3); ?>
                    <input type="button" id="report_btn" class="formbutton" value="Record Sheet" onClick="generate_report('timeWeightRecordSheet')" style="width:80px" />
                </td>
            </tr>
        </table>
    </form>
    </fieldset>
    </div>
    <?
	exit();
}

if ($action=="save_update_delete_colordtls")
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
		//if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		
		$id=return_next_id("id", "sample_development_rf_color", 1);
		
		$j=14; $tot_body=str_replace("'","",$txtbodyc);
		
		$field_arr="id, mst_id, dtls_id, color_id, body_part_id, bodycolor, inserted_by, insert_date, status_active, is_deleted";

		$add_comma=0; $data_array='';
		for($k=1; $k<=$j; $k++)
		{
			$txtcolor="txtcolor_".$k;
			$txtbodycolorval="txtbodycolorval_".$k;
			$txtbodycolorper="txtbodycolorper_".$k;
			
			if(str_replace("'","",$$txtcolor)!="")
			{ 
				if (!in_array(str_replace("'","",$$txtcolor),$new_color_array))
				{
					$color_id = return_id( str_replace("'","",$$txtcolor), $color_library, "lib_color", "id,color_name","245");  
					$new_color_array[$color_id]=str_replace("'","",$$txtcolor);
				}
				else $color_id =  array_search(str_replace("'","",$$txtcolor), $new_color_array); 
			}
			else
			{
				$color_id=0;
			}
			
			for($m=1; $m<=$tot_body; $m++)
			{
				$txtbodycolor="txtbodycolor_".$k.'_'.$m;
				$bodypartid="bodypartid_".$m;
				
				if ($add_comma!=0) $data_array .=",";
				$data_array .="(".$id.",".$update_id.",".$updatedtls_id.",'".$color_id."',".$$bodypartid.",".$$txtbodycolor.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				
				$id=$id+1;
				$add_comma++;
			}
		}
		$flag=1;
		//echo "10**INSERT INTO sample_development_rf_color (".$field_arr.") VALUES ".$data_array; die;
		$rID=sql_insert("sample_development_rf_color",$field_arr,$data_array,0);	
		if($rID) $flag=1; else $flag=0;
		//echo "10**".$flag.'='.$rID.'='.$rID1; die;

		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "0**".str_replace("'",'',$update_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$update_id);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);  
				echo "0**".str_replace("'",'',$update_id);
			}
			else
			{
				oci_rollback($con); 
				echo "10**".str_replace("'",'',$update_id);
			}
		}
		//check_table_status( $_SESSION['menu_id'],0);
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
		
		$used_sample=0;
		$sqlre=sql_select("select job_no from wo_pre_cost_fabric_cost_dtls where sample_id=$update_id and status_active=1 and is_deleted=0 group by job_no");
		foreach($sqlre as $rows){
			$used_sample=$rows[csf('job_no')];
		}
		if($used_sample){
			echo "budget**".str_replace("'","",$txt_style_no)."**".$used_sample;
			die;
		}
		
		//if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		
		$id=return_next_id("id", "sample_development_rf_color", 1);
		
		$j=14; $tot_body=str_replace("'","",$txtbodyc);
		
		$field_arr="id, mst_id, dtls_id, color_id, body_part_id, bodycolor, inserted_by, insert_date, status_active, is_deleted";

		$add_comma=0; $data_array='';
		for($k=1; $k<=$j; $k++)
		{
			$txtcolor="txtcolor_".$k;
			$txtbodycolorval="txtbodycolorval_".$k;
			$txtbodycolorper="txtbodycolorper_".$k;
			
			if(str_replace("'","",$$txtcolor)!="")
			{ 
				if (!in_array(str_replace("'","",$$txtcolor),$new_color_array))
				{
					$color_id = return_id( str_replace("'","",$$txtcolor), $color_library, "lib_color", "id,color_name","245");  
					$new_color_array[$color_id]=str_replace("'","",$$txtcolor);
				}
				else $color_id =  array_search(str_replace("'","",$$txtcolor), $new_color_array); 
			}
			else
			{
				$color_id=0;
			}
			
			for($m=1; $m<=$tot_body; $m++)
			{
				$txtbodycolor="txtbodycolor_".$k.'_'.$m;
				$bodypartid="bodypartid_".$m;
				
				if ($add_comma!=0) $data_array .=",";
				$data_array.="(".$id.",".$update_id.",".$updatedtls_id.",'".$color_id."',".$$bodypartid.",".$$txtbodycolor.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				
				$id=$id+1;
				$add_comma++;
			}
		}
		$flag=1;
		//echo "10**INSERT INTO sample_development_rf_color (".$field_arr.") VALUES ".$data_array; die;
		
		$rID=execute_query("delete from sample_development_rf_color where dtls_id =".$updatedtls_id."",0);
		if($rID) $flag=1; else $flag=0; 
		
		$rID1=sql_insert("sample_development_rf_color",$field_arr,$data_array,0);	
		if($rID1  && $flag==1) $flag=1; else $flag=0;
		
		//echo "10**".$flag.'='.$rID.'='.$rID1.'='.$rID2; die;
			
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'",'',$updatedtls_id);
			}
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$updatedtls_id);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1){
				oci_commit($con); 
				echo "1**".str_replace("'",'',$updatedtls_id);
			}
			else{
				oci_rollback($con); 
				echo "10**".str_replace("'",'',$updatedtls_id);
			}
		}
		//check_table_status( $_SESSION['menu_id'],0);
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
		
		$used_sample=0;
		$sqlre=sql_select("select job_no from wo_pre_cost_fabric_cost_dtls where sample_id=$update_id and status_active=1 and is_deleted=0 group by job_no");
		foreach($sqlre as $rows){
			$used_sample=$rows[csf('job_no')];
		}
		if($used_sample){
			echo "budget**".str_replace("'","",$txt_style_no)."**".$used_sample;
			die;
		}
		
		$flag=1;
		
		$field_array_po="status_active*is_deleted*updated_by*update_date";
		$data_array_po="'0'*'1'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		$rID=sql_delete("sample_development_rf_color",$field_array,$data_array,"mst_id","".$update_id."",0);
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		
		if($db_type==0)
		{
			if($flag==1){
				mysql_query("COMMIT");  
				echo "2**".str_replace("'",'',$updatedtls_id);
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
				echo "2**".str_replace("'",'',$updatedtls_id);
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

if($action=="timeWeightRecordSheet") 
{
    extract($_REQUEST);
	$data=explode('*',$data);
	//$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$party_library=return_library_array( "select id,buyer_name from lib_buyer", "id","buyer_name");
	$brand_arr=return_library_array( "select id, brand_name from lib_buyer_brand",'id','brand_name');
	//$yarn_desc_arr=return_library_array( "select id,yarn_description from lib_subcon_charge",'id','yarn_description');
	//$color_arr=return_library_array( "select id,color_name from lib_color",'id','color_name');
	//$yearn_count_arr=return_library_array( "select id,yarn_count from lib_yarn_count",'id','yarn_count');
	$imge_arr=array(); $img_caption_arr=array();
	$imge_sql=sql_select( "select id, master_tble_id, form_name, image_location from common_photo_library where form_name in('front_time_weight','back_time_weight') and master_tble_id='$data[1]' and file_type=1");
	foreach($imge_sql as $row)
	{
		$imge_arr[$row[csf('id')]]=$row[csf('image_location')];
		$img_caption_arr[$row[csf('id')]]=$row[csf('form_name')];
	}
	
	//$sql_mst="Select id, bill_no, bill_date, party_id, party_source, location_id,party_location_id, attention, bill_for from subcon_inbound_bill_mst where company_id=$data[0] and id='$data[1]' and status_active=1 and is_deleted=0";
	$sql_mst="SELECT a.id, a.company_id, a.buyer_name, a.style_ref_no, a.product_dept, a.item_name, a.item_category, a.region, a.agent_name, a.team_leader, a.dealing_marchant, a.estimated_shipdate, a.remarks, a.product_code, a.bh_merchant, a.season_buyer_wise, a.requisition_number, a.location_id, a.requisition_date, a.garments_nature, a.gauge_no_ends, a.efficiency, 
	b.id, b.sample_mst_id, b.sample_name, b.acc_status_id, b.entry_form_id, b.color_id, b.size_id, b.designer, b.tech_manager, b.programmer, b.yarn_quality, b.count_ply, b.minute, b.second, b.movingsec, b.knitinggm, b.critical_point, b.knitingweight, 
	c.body_part_id, c.minute, c.second, c.movingsec, c.knitinggm,
	d.color_id as colorid, d.body_part_id as bodypartid, d.bodycolor, a.brand_id, a.season_year, b.knitting_system, b.machine_brand_name
	from sample_development_mst a, sample_development_dtls b, sample_development_fabric_acc c, sample_development_rf_color d 
	where a.id=b.sample_mst_id and b.sample_mst_id=c.sample_mst_id and c.sample_mst_id=d.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and d.status_active=1 and d.is_deleted=0 and a.entry_form_id=245 and b.entry_form_id=245 
	and a.company_id=$data[0] and a.id='$data[1]' order by c.body_part_id,d.body_part_id";
	//echo $sql_mst;
	$dataArray=sql_select($sql_mst);
	foreach($dataArray as $row){
		if(($row[csf('minute')]*1)>0 || ($row[csf('second')]*1)>0 || ($row[csf('movingsec')]*1)>0 || ($row[csf('knitinggm')]*1)>0)
		{
			$panel_dtls_data[$row[csf('body_part_id')]]['minute']=$row[csf('minute')];
			$panel_dtls_data[$row[csf('body_part_id')]]['second']=$row[csf('second')];
			$panel_dtls_data[$row[csf('body_part_id')]]['movingsec']=$row[csf('movingsec')];
			$panel_dtls_data[$row[csf('body_part_id')]]['knitinggm']=$row[csf('knitinggm')];
		}
		
		
		$body_data_arr[$row[csf('bodypartid')]]=$row[csf('bodypartid')];
		$color_data_arr[$row[csf('colorid')]][$row[csf('bodypartid')]]['cons']=$row[csf('bodycolor')];
	}
	//echo "<pre>";
	//print_r($imge_arr);
	
	?>
    	<div style="width:900px;">
            <table align="left"  cellspacing="0" width="1000"  border="0" rules="" class="rpt_table" >
                <tr>
                    <td align="center"><strong>TIME AND WEIGHT RECORD SHEET</strong></td> 
                </tr>
            </table>
        </div>
        <div style="width:100%;">
        	<div style="width:70%; float:left;">
                <table align="left"  cellspacing="0" width="1000"  border="1" rules="all" class="rpt_table">
                    <tr>
                        <td width="150" valign="top"><strong>Inquiry No. : </strong></td> 
                        <td width="150"><? echo $dataArray[0][csf('requisition_number')]; ?></td>
                        <td width="150" valign="top"><strong>Yarn Count & Ply : </strong></td> 
                        <td width="150"><? echo $dataArray[0][csf('count_ply')]; ?></td>
                        <td width="150" valign="top"><strong>Brand : </strong></td> 
                        <td width="150"><? echo $brand_arr[$dataArray[0][csf('brand_id')]]; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Buyer : </strong></td>
                        <td style="word-break:break-all;"> <? echo $party_library[$dataArray[0][csf('buyer_name')]]; ?></td>
                        <td><strong>Season  : </strong></td>
                        <td style="word-break:break-all;"><?
                        $season_name=return_field_value("season_name","lib_buyer_season"," buyer_id=".$dataArray[0][csf('buyer_name')]." and id=".$dataArray[0][csf('season_buyer_wise')]." and status_active =1 and is_deleted=0"); 
                        echo $season_name; //$dataArray[0][csf('season_buyer_wise')]; 
                        ?></td>
                        <td><strong>Season Year : </strong></td>
                        <td style="word-break:break-all;"><?=$dataArray[0][csf('season_year')]; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Style Name : </strong></td> 
                        <td style="word-break:break-all;"><? $style_ref_no=$dataArray[0][csf('style_ref_no')];echo $style_ref_no; ?></td>
                        <td><strong>Sample Name  : </strong></td>
                        <td style="word-break:break-all;"><? 
                        $sample_name=return_field_value("sample_name","lib_sample","is_deleted=0 and status_active=1 and id=".$dataArray[0][csf('sample_name')].""); 
                        echo $sample_name; 
                        ?></td>
                        <td><strong>Knitting System : </strong></td>
                        <td style="word-break:break-all;"><?=$knitting_system_arr[$dataArray[0][csf('knitting_system')]]; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Gauge & No. Ends : </strong></td> 
                        <td style="word-break:break-all;"><?=$dataArray[0][csf('gauge_no_ends')]; ?></td>
                        <td><strong>Development No.  : </strong></td>
                        <td style="word-break:break-all;"><?=$development_no[$dataArray[0][csf('acc_status_id')]]; ?></td>
                        <td><strong>Machine Brand:</strong></td>
                        <td style="word-break:break-all;"><?=$dataArray[0][csf('machine_brand_name')]; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Size & Color : </strong></td> 
                        <td style="word-break:break-all;"><?=$size_library[$dataArray[0][csf("size_id")]].", ".$color_library[$dataArray[0][csf("color_id")]]; ?></td>
                        <td><strong>Sample Date : </strong></td>
                        <td style="word-break:break-all;"><?=change_date_format($dataArray[0][csf('requisition_date')]); ?></td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td><strong>Sample Yarn Quality: </strong></td> 
                        <td style="word-break:break-all;"><? echo $dataArray[0][csf('yarn_quality')]; ?></td>
                        <td><strong>Production Yarn Quality:</strong></td> 
                        <td style="word-break:break-all;"><?
                        $remarks=return_field_value("yarn_quality","wo_po_details_master"," style_ref_no='".$style_ref_no."' and status_active =1 and is_deleted=0","yarn_quality"); 
						echo $remarks;
						?></td>
						<td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                </table>
            </div>
            <div style="width:2%; float:left;">&nbsp;</div>
            <div style="width:28%; float:left; text-align:center;">
	            <table align="left" cellspacing="0" width="100%" border="1" rules="all" class="rpt_table" style="float:left;">
	                <tr>
						<?
	                    foreach($imge_arr as $key=> $row ){
	                        if($row=="") $row="&nbsp;";
	                        echo "<td><img src='../../../".$row."' height='115px' width='100%' /></td>";
	                    }
	                    ?>
	                </tr>
	                <tr>
						<?
	                    foreach($img_caption_arr as $key=> $row ){
	                        if($row=="front_time_weight") $caption="Front View"; else $caption="Back View";
	                        echo "<td align='center'>".$caption."&nbsp;</td>";
	                    }
	                    ?>
	                </tr>
	            </table>
            </div>
        </div>
        <div style="width:1150px; clear:left;">&nbsp;</div>
        <div style="width:1150px;">
        	<div style="width:65%; float:left;">
                <table align="left" cellspacing="0" width="100%"  border="1" rules="all" class="rpt_table" style="float:left;">
                    <thead>
                        <tr>
                            <th rowspan="2" width="180">Panel Description</th>
                            <th colspan="3">Knitting Time</th>
                            <th rowspan="2" width="95">M/C Speed M/Sec</th>
                            <th rowspan="2" width="95">Knitting Weight (Gm)</th>
                        </tr>
                        <tr>
                            <th width="95">Minute</th>
                            <th width="95">Second</th>
                            <th width="95">Total Minute</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?
                        $grand_total_minutes=0; $grand_total_movingsec=0; $grand_total_knitinggm=0;
                        $i=1; $j=0;
                        foreach($panel_dtls_data as $body_part_id=>$val)
                        {
                            if($i%2==0) $bgcolor="#FFFFFF"; else $bgcolor="#E9F3FF";
                            
                            $total_minutes = $val['minute']+($val['second']/60);
                            $grand_total_minutes	+= $total_minutes;
                            $grand_total_movingsec	+= $val['movingsec'];
                            $grand_total_knitinggm	+= $val['knitinggm'];
                            if($val['movingsec']){
                                $j+=1;
                            }
                            
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>">
                                <td><? echo $time_weight_panel[$body_part_id]; ?></td>
                                <td align="center"><? echo $val['minute'];?></td>
                                <td align="center"><? echo $val['second'];?></td>
                                <td align="center"><?
									if($total_minutes>0){
                                    echo number_format( $total_minutes, 2);
									}
                                ?></td>
                                <td align="center"><? echo $val['movingsec'];?></td>
                                <td align="center"><? echo $val['knitinggm'];?></td>
                            </tr>
                            <?
                            $i++;
                        }
                        ?>
                        <tr>
                            <td align="center" colspan="3"><strong>Total</strong></td>
                            <td align="center"><strong><? echo number_format( $grand_total_minutes, 2)." Minutes"; ?></strong></td>
                            <td align="center"><strong><? echo number_format( $grand_total_movingsec/$j, 2)." M/Sec"; ?></strong></td>
                            <td align="center"><strong><? echo number_format( $grand_total_knitinggm, 2)." Gm"; ?></strong></td>
                        </tr>
                        <tr>
                            <td>Prod. Pcs/Shift</td>
                            <td><? echo number_format((((60*12)/$grand_total_minutes)*$dataArray[0][csf('efficiency')])/100) . " Pcs/Shift"; ?></td>
                            <td>Prod. Pcs/Day</td>
                            <td><? echo number_format((((60*24)/$grand_total_minutes)*$dataArray[0][csf('efficiency')])/100) . " Pcs/Day"; ?></td>
                            <td>Knit. Weight/Dzn </td>
                            <td><? echo number_format(((($grand_total_knitinggm/1000)* 2.2046 )*12),2) . " Lbs/Dzn"; ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div style="width:2%; float:left;">&nbsp;</div>
            <div style="width:33%; float:left;">
            <?
				foreach($color_data_arr as $colorID => $colordtls_data)
				{
					foreach($colordtls_data as $bodyPartID => $row)
					{
						if(!in_array($colorID,$temp_arr_color))
						{
							if($colorID!=0)
							{
								$x++;
								$bodycolor_arr2[$colorID]=$colorID;
							}
							$temp_arr_color[]=$colorID;
						}
					}
				}
			?>
            <div style="width:<? echo $tbl_width;?>px">
                <table align="left"  cellspacing="0" width="100%"  border="1" rules="all" class="rpt_table">
                    <thead>
                        <tr>
                            <th width="40">Color</th>
                            <?
                            foreach ($body_data_arr as $id=>$bid )
                            {
                                ?>
                                <th width="80"><? echo $time_weight_panel[$bid]; ?></th>
                                <?
                            }
                            ?>
                            <th width="80">Total</th>
                            <th width="80">Color %</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?
                    $k=14;
                    $grand_total_cons=0;
					foreach ($bodycolor_arr2 as $color_id=>$color_id_data )
                    {
                        foreach ($body_data_arr as $id=>$bid )
                        {
                            $grand_total_cons += $color_data_arr[$color_id][$bid]['cons'];
                        }
                    }
                    
                    $total_color_data_arr=array();
                    $grand_total_color=0;
                    $j=14;
					$m=1;
					asort($bodycolor_arr2);
					foreach($bodycolor_arr2 as $color_id=> $color_id_data)
                    {
                        if($m%2==0) $bgcolor="#FFFFFF"; else $bgcolor="#E9F3FF";
                        ?>
                        <tr align="center" bgcolor="<? echo $bgcolor; ?>">
                            <td width="80"><? echo $color_library[$color_id]; ?></td>
                            <? $k=1;
                            $total_cons=0;
                            foreach ($body_data_arr as $id=>$bid )
                            {
                                
                                $total_color_data_arr[$id]['cons']+=$color_data_arr[$color_id][$bid]['cons'];
                                $cons=$color_data_arr[$color_id][$bid]['cons'];
                                $total_cons+=$cons;
                                ?>
                                <td width="80"><? echo $cons; ?></td>
                                <?
                                $k++;
                            }
                            ?>
                            <td width="80"><? echo number_format($total_cons,2); ?></td>
                            <td width="80"><? 
                            echo number_format((($total_cons*100)/$grand_total_cons),2); 
                            $grand_total_color+=(($total_cons*100)/$grand_total_cons);
                            ?></td>
                        </tr>
                    <?
					$m++;
                    }
                    ?>
                        <tr align="center">
                            <td align="center"><b>Total</b></td>
                            <? 
                            foreach ($body_data_arr as $id=>$bid  )
                            {
                                ?>
                                <td align="center"><b><? echo number_format($total_color_data_arr[$id]['cons'],2) ; ?></b></td>
                                <?
                            }
                            ?>
                            <td align="center"><b><? echo number_format($grand_total_cons,2); ?></b></td>
                            <td align="center"><b><? echo number_format($grand_total_color,2); ?></b></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div style="width:100%;">&nbsp;</div>
            <div style="width:100%;">
                <table align="left"  cellspacing="0" width="100%"  border="1" rules="all" class="rpt_table">
                    <tr>
                        <td width="165">Designer</td><td><? echo $dataArray[0][csf('designer')];?></td>
                    </tr>
                    <tr>
                        <td>Asst. Technical Manager</td><td><? echo $dataArray[0][csf('tech_manager')];?></td>
                    </tr>
                    <tr>
                        <td>Programmer</td><td><? echo $dataArray[0][csf('programmer')];?></td>
                    </tr>
                </table>
            </div>
        </div>
        </div>
        
        <div style="width:1150px; clear:left;">&nbsp;</div>
        
        <div style="width:1150px;">
            <table align="left"  cellspacing="0" width="1150"  border="1" rules="all" class="rpt_table">
					<?
                    $critical_points_arr = explode("___",$dataArray[0][csf('critical_point')]);
                    ?>
                 	<tr>
                        <td colspan="2"><strong>Critical Points / Special Comments:</strong> </td>
                    </tr>
                    <tr>
                        <td width="180"><strong># Knitting</strong></td><td><? echo $critical_points_arr[0]; ?></td>
                    </tr>
                    <tr>
                        <td width=""><strong># Linking</strong></td><td><? echo $critical_points_arr[1]; ?></td>
                    </tr>
                    <tr>
                        <td width=""><strong># Washing</strong></td><td><? echo $critical_points_arr[2]; ?></td>
                    </tr>
                    <tr>
                        <td width=""><strong># Add Ons / Attachments</strong></td><td><? echo $critical_points_arr[3]; ?></td>
                    </tr>
                	<tr>
                        <td width=""><strong># Finishing</strong></td><td><? echo $critical_points_arr[4]; ?></td>
                    </tr>
            </table>
        </div>
        <br>
        <div style="width:100%;" > <? echo signature_table(73, $data[0], "1150px"); ?> </div>
        
	<?
    exit();
}

if($action=="copy_weightrecord")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	if ($operation==5)
	{
		$con = connect();
		if (is_duplicate_field( "a.requisition_number", "sample_development_mst a, sample_development_dtls b", " a.id=b.sample_mst_id and a.buyer_name=$cbo_buyer_name and a.style_ref_no=$txt_style_ref and b.sample_name=$cbo_sample_type and b.acc_status_id=$cbo_dev_no and a.status_active=1 and a.is_deleted=0 and a.status_active=1 and a.is_deleted=0" ) == 1)
		{
			echo "11**0"; 
			die;
		}
		
		if($db_type==0) $date_cond=" YEAR(insert_date)"; else if($db_type==2) $date_cond="to_char(insert_date,'YYYY')";
			
		$new_sample_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'SMP', date("Y",time()), 5, "select requisition_number_prefix, requisition_number_prefix_num from sample_development_mst where company_id=$cbo_company_name and entry_form_id=245 and $date_cond=".date('Y',time())." order by id DESC", "requisition_number_prefix", "requisition_number_prefix_num"));
		
		$id=return_next_id("id", "sample_development_mst", 1);
		$id_dtls=return_next_id("id", "sample_development_dtls", 1);
		$id1=return_next_id("id", "sample_development_fabric_acc", 1);
		$idrf=return_next_id("id", "sample_development_rf_color", 1);
		
		$sqlBodypanel="select id, sample_mst_id, sample_dtls_id, body_part_id, minute, second, movingsec, knitinggm from sample_development_fabric_acc where sample_mst_id=$update_id and is_deleted=0 and status_active=1";
		$sqlBodypanelRes=sql_select($sqlBodypanel);
		
		$sqlRfcolor="select id, mst_id, dtls_id, color_id, body_part_id, bodycolor from sample_development_rf_color where mst_id=$update_id and is_deleted=0 and status_active=1 ";
		$sqlRfcolorRes=sql_select($sqlRfcolor);
		
		$sql_mstInst="insert into sample_development_mst( id, garments_nature, entry_form_id, requisition_number, requisition_number_prefix, requisition_number_prefix_num, company_id, location_id, buyer_name, style_ref_no, mers_style, product_dept, product_code, item_category, article_no, item_name, region, agent_name, season_buyer_wise, team_leader, dealing_marchant, bh_merchant, remarks, req_ready_to_approved, estimated_shipdate, requisition_date, gauge_no_ends, efficiency, brand_id, season_year, uom, inserted_by, insert_date, status_active, is_deleted, is_copy, copy_from)
	select
	$id, garments_nature, 245, '".$new_sample_no[0]."', '".$new_sample_no[1]."', '".$new_sample_no[2]."', company_id, location_id, buyer_name, style_ref_no, mers_style, product_dept, product_code, item_category, article_no, item_name, region, agent_name, season_buyer_wise, team_leader, dealing_marchant, bh_merchant, remarks, req_ready_to_approved, estimated_shipdate, requisition_date, gauge_no_ends, efficiency, brand_id, season_year, uom, ".$_SESSION['logic_erp']['user_id'].", '".$pc_date_time."', 1, 0, 1, $update_id from sample_development_mst where id=$update_id";
	
		$sql_dtlsInst="insert into sample_development_dtls( id, sample_mst_id, sample_name, acc_status_id, entry_form_id, color_id, size_id, designer, tech_manager, programmer, yarn_quality, count_ply, minute, second, movingsec, knitinggm, critical_point, knitingweight, knitting_system, machine_brand_name, inserted_by, insert_date, is_deleted, status_active)
		select
		$id_dtls, $id, sample_name, acc_status_id, 245, color_id, size_id, designer, tech_manager, programmer, yarn_quality, count_ply, minute, second, movingsec, knitinggm, critical_point, knitingweight, knitting_system, machine_brand_name, ".$_SESSION['logic_erp']['user_id'].", '".$pc_date_time."', 0,1 from sample_development_dtls where sample_mst_id=$update_id and status_active=1 and is_deleted=0";
		
		$field_array1="id, sample_mst_id, sample_dtls_id, body_part_id, minute, second, movingsec, knitinggm, inserted_by, insert_date, is_deleted, status_active";
		
		$data_array1='';
		foreach($sqlBodypanelRes as $row)
		{
			$data_array1 .="INTO sample_development_fabric_acc ( ".$field_array1." ) VALUES( ".$id1.",".$id.",".$id_dtls.",'".$row[csf('body_part_id')]."','".$row[csf('minute')]."','".$row[csf('second')]."','".$row[csf('movingsec')]."','".$row[csf('knitinggm')]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',0,1)";
			
			$id1=$id1+1;
		}
		unset($sqlBodypanelRes);
		
		$field_arrRf="id, mst_id, dtls_id, color_id, body_part_id, bodycolor, inserted_by, insert_date, status_active, is_deleted";
		
		$data_arrayRf='';
		foreach($sqlRfcolorRes as $rfrow)
		{
			$data_arrayRf .="INTO sample_development_rf_color ( ".$field_arrRf." ) VALUES( ".$idrf.",".$id.",".$id_dtls.",'".$rfrow[csf('color_id')]."','".$rfrow[csf('body_part_id')]."','".$rfrow[csf('bodycolor')]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			
			$idrf=$idrf+1;
		}
		unset($sqlRfcolorRes);
		
		$flag=1;
		
		$rID=execute_query($sql_mstInst,0);
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		
		$rID1=execute_query($sql_dtlsInst,0);
		if($rID1==1 && $flag==1) $flag=1; else $flag=0;
		
		$queryPanel="INSERT ALL ".$data_array1." SELECT * FROM dual";
		$rID2=execute_query($queryPanel);
		if($rID2==1 && $flag==1) $flag=1; else $flag=0;
		
		$queryRf="INSERT ALL ".$data_arrayRf." SELECT * FROM dual";
		//echo "10**".$queryRf; die;
		$rID3=execute_query($queryRf);
		if($rID3==1 && $flag==1) $flag=1; else $flag=0;
		
		//echo "10**".$rID."--".$rID1."--".$rID2."--".$rID3."--".$flag; disconnect($con); die;
		
		if($db_type==2)
		{
			if($flag==1)
			{
				oci_commit($con);  
				echo "36**".$new_sample_no[0]."**".$id."**".$id_dtls;
			}
			else
			{
				oci_rollback($con); 
				echo "10**".$new_sample_no[0]."**".$id."**".$id_dtls;
			}
		}
		//check_table_status( $_SESSION['menu_id'],0);
		disconnect($con);
		die;
	}
}

?>