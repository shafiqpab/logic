<?
session_start();
include('../../includes/common.php');
 
$user_id = $_SESSION['logic_erp']["user_id"];
$user_level = $_SESSION['logic_erp']['user_level'];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


//========== user credential start ========
$userCredential = sql_select("SELECT unit_id as company_id, store_location_id, item_cate_id, company_location_id as location_id FROM user_passwd where id=$user_id");
$company_id = $userCredential[0][csf('company_id')];
$store_location_id = $userCredential[0][csf('store_location_id')];
$item_cate_id = $userCredential[0][csf('item_cate_id')];
$location_id = $userCredential[0][csf('location_id')];

$company_credential_cond = "";

if ($company_id >0) {
    $company_credential_cond = " and id in($company_id)";
}

if (!empty($store_location_id)) {
    $store_location_credential_cond = " and a.id in($store_location_id)";
}

if ($location_id !='') {
    $location_credential_cond = " and id in($location_id)";
}

if($item_cate_id !='') {
    $item_cate_credential_cond = $item_cate_id ;
}

 $user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");


//========== user credential end ==========

//------------------------------------------------------------------------------------------------------
//$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name"  );
$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
//$country_short_name=return_library_array( "select id,short_name from lib_country", "id", "short_name"  );

$sqlCountry=sql_select("select id, country_name, short_name from lib_country");
$country_library=array(); $country_short_name=array();
foreach($sqlCountry as $crow)
{
	$country_library[$crow[csf("id")]]=$crow[csf("country_name")];
	$country_short_name[$crow[csf("id")]]=$crow[csf("short_name")];
}
unset($sqlCountry);

if($db_type==0) $select_field="group"; 
else if($db_type==2) $select_field="wm";
else $select_field="";//defined Later	

if ($action=="load_variable_settings")
{
	echo "$('#sewing_production_variable').val(0);\n";
	$sql_result = sql_select("select finishing_update,production_entry from variable_settings_production where company_name=$data and variable_list=1 and status_active=1");
 	foreach($sql_result as $result)
	{
		$finishing_update = ($result[csf("finishing_update")]==0) ? 3 : $result[csf("finishing_update")];
		echo "$('#sewing_production_variable').val(".$finishing_update.");\n";
		echo "$('#styleOrOrderWisw').val(".$result[csf("production_entry")].");\n";
	}	
	
	echo "$('#rack_wise_balance_show').val(0);\n";
	$rack_slq_res = sql_select("SELECT rack_balance from variable_settings_inventory where company_name=$data and variable_list=21 and item_category_id=17 and status_active=1 and is_deleted=0");

	$rack_wise_balance_show=$rack_slq_res[0]['RACK_BALANCE'];
	echo "$('#rack_wise_balance_show').val($rack_wise_balance_show);\n";
	exit();
}

if ($action=="load_drop_down_working_company")
{
	echo create_drop_down( "cbo_location", 167, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' $location_credential_cond order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/finish_gmts_issue_return_entry_controller', this.value, 'load_drop_down_floor', 'floor_td' );",0 );     	 
}

if ($action=="load_drop_down_location")
{    	 
	echo create_drop_down( "cbo_location", 167, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' $location_credential_cond order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/finish_gmts_issue_return_entry_controller', this.value, 'load_drop_down_store', 'store_td' );",0 );     	 
}

if ($action=="load_drop_down_store")
{
	echo create_drop_down( "cbo_store_name", 170, "SELECT a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id and b.category_type in(30) and a.status_active =1 and a.is_deleted=0 and a.location_id='$data' $store_location_credential_cond order by a.store_name","id,store_name", 1, "-- Select Floor --", $selected, "load_room_rack_self_bin('requires/finish_gmts_issue_return_entry_controller', 'floor','floor_td',document.getElementById('cbo_company_id').value,document.getElementById('cbo_location').value,this.value,this.value);",0 );     
}

if ($action=="load_room_rack_self_bin")
{
	load_room_rack_self_bin("requires/finish_gmts_issue_return_entry_controller",$data);
}

if($action=="load_drop_down_source")
{
	$explode_data = explode("**",$data);
	$data = $explode_data[0];
	$selected_company = $explode_data[1];
	
	if($data==3)
	{
		if($db_type==0)
		{
		echo create_drop_down( "cbo_finish_company", 170, "select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0 and find_in_set(22,party_type) order by supplier_name","id,supplier_name", 1, "--- Select ---", $selected, "",0,0 );
		}
		else
		{
			echo create_drop_down( "cbo_finish_company", 170, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=22 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "--Select--", $selected, "" );
		}
	}
	else if($data==1)
 		echo create_drop_down( "cbo_finish_company", 170, "select id,company_name from lib_company where is_deleted=0 and status_active=1 $company_credential_cond order by company_name","id,company_name", 1, "--- Select ---", "", "load_drop_down( 'requires/finish_gmts_issue_return_entry_controller', this.value, 'load_drop_down_location', 'location_td' );fnc_company_check(document.getElementById('cbo_source').value);load_drop_down( 'requires/finish_gmts_issue_return_entry_controller', this.value, 'load_drop_down_store', 'store_td' );",0,0 ); 
 	else
		echo create_drop_down( "cbo_finish_company", 170, $blank_array,"", 1, "--- Select ---", $selected, "",0,0 );	
			
	exit();
}

if($action=="challan_number_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
	?>
	<script>		
		function search_populate(str)
		{
			if(str==1) 
			{		
				document.getElementById('search_by_th_up').innerHTML="Order No";
				$("#txt_search_common").val(null);	 
			}
			else if(str==2) 
			{
				document.getElementById('search_by_th_up').innerHTML="Style Ref. Number";
				$("#txt_search_common").val(null);
			}
			else if(str==3)
			{
				document.getElementById('search_by_th_up').innerHTML="Job No";
				$("#txt_search_common").val(null);
			}																																
		}
		function js_set_value(str)
		{
			$("#hidden_search_data").val(str);
			parent.emailwindow.hide();
		}
    </script>
	</head>
	<body>
	<div align="center" style="width:100%;" >
	    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	        <table ellspacing="0" width="850" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
	            <thead>
	                <tr>
	                    <th width="180">Company</th>
						<th >Search By</th>
	                    <th id="search_by_th_up">Enter Order Number</th>
	                    <th width="150">Issue No</th>
						<th width="200">Date Range</th>
	                    <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:80px;" /></th>
	                </tr>
	            </thead>
	            <tbody>
	                <tr>
	                    <td align="center">
	                    	<? echo create_drop_down( "cbo_company_id", 170, "select company_name,id from lib_company comp where is_deleted=0  and status_active=1 order by company_name",'id,company_name', 1, '--- Select Company ---',$company, "load_drop_down( 'finish_gmts_issue_return_entry_controller', this.value, 'load_drop_down_location2', 'location_td' );");?>
	                    </td>
						<td >  
							<?  $searchby_arr=array(1=>"Order No",2=>"Style Ref. Number",3=>"Job No");
								echo create_drop_down( "txt_search_by", 100, $searchby_arr,"", 0, "-- Select --", $selected, "search_populate(this.value)",0 );
							?>
						</td>
						<td align="center">				
							<input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" onKeyDown="if (event.keyCode == 13) document.getElementById('btn_show').click()" />			
						</td>
	                    <td align="center">
	                    	<input type="text" style="width:100px" class="text_boxes"  name="txt_system_no" id="txt_system_no" />
	                    </td>
						<td align="center">
							<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px"> To
							<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px">
	                    </td>
	                    <td align="center">
	                    	<input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view( document.getElementById('cbo_company_id').value+'_'+document.getElementById('txt_system_no').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_search_by').value+'_'+document.getElementById('txt_search_common').value, 'create_challan_number_list_view', 'search_div', 'finish_gmts_issue_return_entry_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:80px;" />
	                    </td>
	                </tr>
	            </tbody>
				<tfoot>
	                <tr>
	                    <td align="center" height="25" valign="middle" colspan="9" style="background-image: -moz-linear-gradient(bottom, rgb(136,170,282) 7%, rgb(194,220,255) 10%, rgb(136,170,282) 96%);">
	                    <? echo load_month_buttons(1);  ?>
	                    </td>
	                </tr>
	            </tfoot>
	        </table>
	        <div style="margin-top:10px" id="search_div"></div>
			<input type="hidden" id="hidden_search_data">
	    </form>
	</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}

if($action=="create_challan_number_list_view")
{
 	list($company,$system_no,$txt_date_from,$txt_date_to,$txt_search_by,$txt_search_common) = explode("_",$data);
	
	$location_arr=return_library_array("select id,location_name from lib_location where status_active=1 and is_deleted=0", 'id', 'location_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$floor_arr=return_library_array( "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0",'id','floor_name');
	$sql_cond="";

	if($db_type==0)
	{
		if($txt_date_from!="" && $txt_date_to!="")
		{
			$sql_cond .= " and a.delivery_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
		}
		else if($txt_date_from=="" && $txt_date_to!=""){
			$sql_cond .= " and a.delivery_date <= '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
		}
		else if($txt_date_from!="" && $txt_date_to==""){
			$sql_cond .= " and a.delivery_date >= '".change_date_format($txt_date_from,'yyyy-mm-dd')."'";
		}
	}
	if($db_type==2 || $db_type==1)
	{
		if($txt_date_from!="" && $txt_date_to!="")
		{
			$sql_cond .= " and a.delivery_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'";
		}
		else if($txt_date_from=="" && $txt_date_to!=""){
			$sql_cond .= " and a.delivery_date <=  '".date("j-M-Y",strtotime($txt_date_to))."'";
		}
		else if($txt_date_from!="" && $txt_date_to==""){
			$sql_cond .= " and a.delivery_date >= '".date("j-M-Y",strtotime($txt_date_from))."'";
		}

	}

	if(trim($system_no)!="")
	{
		$sql_cond = " and a.sys_number like '%".trim($system_no)."'";
	}
	if(trim($company)!='0')
	{
		$sql_cond .= " and a.company_id='$company'";
	}
	if(trim($txt_search_common)!="")
	{
		if($txt_search_by==1) {
			$sql_cond .= " and c.po_number like '%".trim($txt_search_common)."%'";
		}
		elseif ($txt_search_by==2) {
			$sql_cond .= " and d.style_ref_no like '%".trim($txt_search_common)."%'";
		}
		elseif ($txt_search_by==3) {
			$sql_cond .= " and d.job_no like '%".trim($txt_search_common)."'";	
		}
	}

	if($db_type==0){$po_clm=", group_concat(distinct c.po_number) as po_number";}
	else{$po_clm=", listagg(c.po_number ,',') within group (order by c.id) as po_number";}

	// $sql ="SELECT a.id, a.sys_number, a.company_id, a.floor_id,a.location_id from pro_gmts_delivery_mst a where  a.status_active=1 and a.is_deleted=0 and a.production_type=82 and a.entry_form=503 $sql_cond order by a.id";

	$sql ="SELECT a.id, a.sys_number, a.company_id, a.floor_id, a.working_company_id, a.location_id $po_clm from pro_gmts_delivery_mst a, pro_garments_production_mst b, wo_po_break_down c, wo_po_details_master d where a.id=b.delivery_mst_id and b.po_break_down_id=c.id and c.job_id=d.id and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and a.production_type=82 and a.entry_form=503 $sql_cond group by a.id, a.sys_number, a.company_id, a.floor_id, a.working_company_id, a.location_id order by a.id desc";

	// echo $sql;die();

	$arr=array(1=>$company_arr,2=>$location_arr);

	echo create_list_view("list_view", "System Number,Company,Location","120,180","500","240",0, $sql , "js_set_value","id,sys_number", "",1, "0,company_id,location_id", $arr,"sys_number,company_id,location_id", "","setFilterGrid('list_view',-1)","0,0,0,0") ;

	exit();
}

if($action=="populate_challan_form_data")
{
	$sql ="SELECT a.id as ID, a.sys_number as SYS_NUMBER, a.company_id as COMPANY_ID, a.location_id as LOCATION_ID,a.store_id as STORE_ID,a.floor_id as FLOOR_ID from pro_gmts_delivery_mst a where a.id='$data' and production_type=82";

	//echo $sql.";\n";
	$result =sql_select($sql);
	
	echo "load_drop_down( 'requires/finish_gmts_issue_return_entry_controller', ".$result[0]['COMPANY_ID'].", 'load_drop_down_location', 'cbo_location' );\n";
	echo "get_php_form_data(".$result[0]['COMPANY_ID'].",'load_variable_settings','requires/finish_gmts_issue_return_entry_controller');\n";
	echo "load_drop_down( 'requires/finish_gmts_issue_return_entry_controller',  ".$result[0]['LOCATION_ID'].", 'load_drop_down_store', 'store_td' );\n";
	echo "load_room_rack_self_bin('requires/finish_gmts_issue_return_entry_controller', 'floor','floor_td', '".$result[0]['COMPANY_ID']."','"."','".$result[0]['STORE_ID']."',this.value);\n";
	echo "load_room_rack_self_bin('requires/finish_gmts_issue_return_entry_controller', 'room','room_td', '".$result[0]['COMPANY_ID']."','"."','".$result[0]['STORE_ID']."','".$result[0]['FLOOR_ID']."',this.value);\n";

	echo "$('#txt_challan_no').val('".$result[0]['SYS_NUMBER']."');\n";
	echo "$('#txt_challan_id').val('".$result[0]['ID']."');\n";
	echo "$('#cbo_company_id').val('".$result[0]['COMPANY_ID']."');\n";
	echo "$('#cbo_location').val('".$result[0]['LOCATION_ID']."');\n";
	echo "$('#cbo_store_name').val('".$result[0]['STORE_ID']."');\n";
	echo "$('#cbo_floor').val('".$result[0]['FLOOR_ID']."');\n";

 	exit();
}

if($action=="populate_challan_form_data_scan")
{
	$sql ="SELECT a.id as ID, a.sys_number as SYS_NUMBER, a.company_id as COMPANY_ID, a.location_id as LOCATION_ID,a.store_id as STORE_ID,a.floor_id as FLOOR_ID from pro_gmts_delivery_mst a where a.sys_number='$data' and production_type=82";

	//echo $sql.";\n";
	$result =sql_select($sql);
	
	echo "load_drop_down( 'requires/finish_gmts_issue_return_entry_controller', ".$result[0]['COMPANY_ID'].", 'load_drop_down_location', 'cbo_location' );\n";
	echo "get_php_form_data(".$result[0]['COMPANY_ID'].",'load_variable_settings','requires/finish_gmts_issue_return_entry_controller');\n";
	echo "load_drop_down( 'requires/finish_gmts_issue_return_entry_controller',  ".$result[0]['LOCATION_ID'].", 'load_drop_down_store', 'store_td' );\n";
	echo "load_room_rack_self_bin('requires/finish_gmts_issue_return_entry_controller', 'floor','floor_td', '".$result[0]['COMPANY_ID']."','"."','".$result[0]['STORE_ID']."',this.value);\n";
	echo "load_room_rack_self_bin('requires/finish_gmts_issue_return_entry_controller', 'room','room_td', '".$result[0]['COMPANY_ID']."','"."','".$result[0]['STORE_ID']."','".$result[0]['FLOOR_ID']."',this.value);\n";

	echo "$('#txt_challan_no').val('".$result[0]['SYS_NUMBER']."');\n";
	echo "$('#txt_challan_id').val('".$result[0]['ID']."');\n";
	echo "$('#cbo_company_id').val('".$result[0]['COMPANY_ID']."');\n";
	echo "$('#cbo_location').val('".$result[0]['LOCATION_ID']."');\n";
	echo "$('#cbo_store_name').val('".$result[0]['STORE_ID']."');\n";
	echo "$('#cbo_floor').val('".$result[0]['FLOOR_ID']."');\n";

 	exit();
}


if($action=="system_number_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
	?>
	<script>	
		function search_populate(str)
		{
			if(str==1) 
			{		
				document.getElementById('search_by_th_up').innerHTML="Order No";
				$("#txt_search_common").val(null);	 
			}
			else if(str==2) 
			{
				document.getElementById('search_by_th_up').innerHTML="Style Ref. Number";
				$("#txt_search_common").val(null);
			}
			else if(str==3)
			{
				document.getElementById('search_by_th_up').innerHTML="Job No";
				$("#txt_search_common").val(null);
			}																																
		}

		function js_set_value(str)
		{
			$("#hidden_search_data").val(str);
			parent.emailwindow.hide();
		}
    </script>
	</head>
	<body>
	<div align="center" style="width:100%;" >
	    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	        <table ellspacing="0" width="850" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
	            <thead>
	                <tr>
	                    <th>Company</th>
						<th >Search By</th>
	                    <th id="search_by_th_up">Enter Order Number</th>
	                    <th>Return No</th>
	                    <th width="200">Date Range</th>
	                    <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:80px;" /></th>
	                </tr>
	            </thead>
	            <tbody>
	                <tr>
	                    <td align="center">
							<? echo create_drop_down( "cbo_company_id", 170, "select company_name,id from lib_company comp where is_deleted=0  and status_active=1 order by company_name",'id,company_name', 1, '--- Select Company ---',$company, ""); ?>
	                    </td>
	                    <td >  
							<?  $searchby_arr=array(1=>"Order No",2=>"Style Ref. Number",3=>"Job No");
								echo create_drop_down( "txt_search_by", 100, $searchby_arr,"", 0, "-- Select --", $selected, "search_populate(this.value)",0 );
							?>
						</td>
						<td align="center">				
							<input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" onKeyDown="if (event.keyCode == 13) document.getElementById('btn_show').click()" />			
						</td>
	                    <td align="center">
	                    	<input type="text" style="width:100px" class="text_boxes"  name="txt_system_no" id="txt_system_no" />
	                    </td>
	                    <td align="center">
							<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px"> To
							<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px">
	                    </td>
	                    <td align="center">

	                    	<input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view( document.getElementById('cbo_company_id').value+'_'+document.getElementById('txt_system_no').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_search_by').value+'_'+document.getElementById('txt_search_common').value, 'create_system_number_list_view', 'search_div', 'finish_gmts_issue_return_entry_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:80px;" />
	                    </td>
	                </tr>
	            </tbody>
	            <tfoot>
	                <tr>
	                    <td align="center" height="25" valign="middle" colspan="9" style="background-image: -moz-linear-gradient(bottom, rgb(136,170,283) 7%, rgb(194,220,255) 10%, rgb(136,170,283) 96%);">
	                    <? echo load_month_buttons(1);  ?>
	                    </td>
	                </tr>
	            </tfoot>
	        </table>
			<input type="hidden" id="hidden_search_data">
	        <div style="margin-top:10px" id="search_div"></div>
	    </form>
	</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}

if($action=="create_system_number_list_view")
{
 	list($company,$system_no,$txt_date_from,$txt_date_to,$txt_search_by,$txt_search_common) = explode("_",$data);
	
	$location_arr=return_library_array("select id,location_name from lib_location where status_active=1 and is_deleted=0", 'id', 'location_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$floor_arr=return_library_array( "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0",'id','floor_name');
	$sql_cond="";

	if($db_type==0)
	{
		if($txt_date_from!="" && $txt_date_to!="")
		{
			$sql_cond .= " and a.delivery_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
		}
		else if($txt_date_from=="" && $txt_date_to!=""){
			$sql_cond .= " and a.delivery_date <= '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
		}
		else if($txt_date_from!="" && $txt_date_to==""){
			$sql_cond .= " and a.delivery_date >= '".change_date_format($txt_date_from,'yyyy-mm-dd')."'";
		}
	}
	if($db_type==2 || $db_type==1)
	{
		if($txt_date_from!="" && $txt_date_to!="")
		{
			$sql_cond .= " and a.delivery_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'";
		}
		else if($txt_date_from=="" && $txt_date_to!=""){
			$sql_cond .= " and a.delivery_date <=  '".date("j-M-Y",strtotime($txt_date_to))."'";
		}
		else if($txt_date_from!="" && $txt_date_to==""){
			$sql_cond .= " and a.delivery_date >= '".date("j-M-Y",strtotime($txt_date_from))."'";
		}

	}
	
	if(trim($system_no)!="")
	{
		$sql_cond = " and a.sys_number like '%".trim($system_no)."'";
	}
	if(trim($company)!='0')
	{
		$sql_cond .= " and a.company_id='$company'";
	}
	// if(trim($source)!='0')
	// {
	// 	$sql_cond .= " and a.production_source='$source'";
	// }
	if(trim($txt_search_common)!="")
	{
		if($txt_search_by==1) {
			$sql_cond .= " and c.po_number like '%".trim($txt_search_common)."%'";
		}
		elseif ($txt_search_by==2) {
			$sql_cond .= " and d.style_ref_no like '%".trim($txt_search_common)."%'";
		}
		elseif ($txt_search_by==3) {
			$sql_cond .= " and d.job_no like '%".trim($txt_search_common)."'";	
		}
		
	}


	// $sql ="SELECT a.id, a.sys_number, a.company_id, a.floor_id, a.working_company_id, a.location_id, a.production_source,a.challan_id from pro_gmts_delivery_mst a where  a.status_active=1 and a.is_deleted=0 and a.production_type=83 and a.entry_form=503 $sql_cond order by a.id";
	if($db_type==0)
	{
		$sql ="SELECT a.id, a.sys_number, a.company_id, a.floor_id, a.working_company_id, a.location_id, a.production_source,a.challan_id, group_concat(distinct c.po_number) as po_number from pro_gmts_delivery_mst a, pro_garments_production_mst b, wo_po_break_down c, wo_po_details_master d where a.id=b.delivery_mst_id and b.po_break_down_id=c.id and c.job_id=d.id and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and a.production_type=83 and a.entry_form=504 $sql_cond group by a.id, a.sys_number, a.company_id, a.floor_id, a.working_company_id, a.location_id, a.production_source,a.challan_id order by a.id";
	}
	else
	{
		$sql ="SELECT a.id, a.sys_number, a.company_id, a.floor_id, a.working_company_id, a.location_id, a.production_source,a.challan_id, listagg(c.po_number ,',') within group (order by c.id) as po_number from pro_gmts_delivery_mst a, pro_garments_production_mst b, wo_po_break_down c, wo_po_details_master d where a.id=b.delivery_mst_id and b.po_break_down_id=c.id and c.job_id=d.id and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and a.production_type=83 and a.entry_form=504 $sql_cond group by a.id, a.sys_number, a.company_id, a.floor_id, a.working_company_id, a.location_id, a.production_source,a.challan_id order by a.id";
	}

	// echo $sql;die();

	// $arr=array(1=>$company_arr,2=>$knitting_source,3=>$company_arr,4=>$location_arr);
	$arr=array(1=>$company_arr,2=>$location_arr);

	echo create_list_view("list_view", "System Number,Company,Location,PO No","120,180,180,150","650","240",0, $sql , "js_set_value","id,sys_number", "",1, "0,company_id,location_id,0", $arr,"sys_number,company_id,location_id,po_number", "","setFilterGrid('list_view',-1)","0,0,0") ;

	exit();
}

if($action=="populate_mst_form_data")
{
	$sql ="SELECT a.id, a.sys_number,a.challan_no,a.challan_id, a.company_id, a.location_id,a.delivery_date,a.remarks,a.purpose_id,a.store_id as STORE_ID,a.floor_id as FLOOR_ID from pro_gmts_delivery_mst a where a.id='$data' and production_type=83";

	// echo $sql.";\n";
	$result =sql_select($sql);

	echo "load_drop_down( 'requires/finish_gmts_issue_return_entry_controller', ".$result[0][csf('company_id')].", 'load_drop_down_location', 'cbo_location' );\n";
	echo "get_php_form_data(".$result[0]['COMPANY_ID'].",'load_variable_settings','requires/finish_gmts_issue_return_entry_controller');\n";
	echo "load_drop_down( 'requires/finish_gmts_issue_return_entry_controller',  ".$result[0][csf('location_id')].", 'load_drop_down_store', 'store_td' );\n";
	echo "$('#cbo_store_name').val('".$result[0]['STORE_ID']."');\n";

	if($result[0]['STORE_ID']>0)
	{
		echo "load_room_rack_self_bin('requires/finish_gmts_issue_return_entry_controller', 'floor','floor_td', '".$result[0][csf('company_id')]."','"."','".$result[0]['STORE_ID']."',this.value);\n";
	}
	echo "$('#cbo_floor').val('".$result[0]['FLOOR_ID']."');\n";
	if($result[0]['FLOOR_ID']>0)
	{
		echo "load_room_rack_self_bin('requires/finish_gmts_issue_return_entry_controller', 'room','room_td', '".$result[0][csf('company_id')]."','"."','".$result[0]['STORE_ID']."','".$result[0]['FLOOR_ID']."',this.value);\n";
	}

	echo "$('#txt_challan_no').val('".$result[0][csf('challan_no')]."');\n";
	echo "$('#txt_challan_id').val('".$result[0][csf('challan_id')]."');\n";
	echo "$('#cbo_company_id').val('".$result[0][csf('company_id')]."');\n";
	echo "$('#cbo_location').val('".$result[0][csf('location_id')]."');\n";
	echo "$('#txt_issue_rtn_date').val('".change_date_format($result[0][csf('delivery_date')])."');\n";
	echo "$('#cbo_purpose').val('".$result[0][csf('purpose_id')]."');\n";
	echo "$('#txt_remark').val('".$result[0][csf('remarks')]."');\n";
	echo "set_button_status(0, permission, 'fnc_gmt_issue_rtn_entry',1);\n";
 	exit();
}

if($action=="show_dtls_listview")
{
	$location_arr=return_library_array( "SELECT id, location_name from lib_location",'id','location_name');
	$company_arr=return_library_array( "SELECT id, company_name from lib_company",'id','company_name');
		
	?>	 
	<div style="width:100%;">
		<table cellspacing="0" width="550" cellpadding="0" border="1" rules="all" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="150" align="center">Item Name</th>
                <th width="150" align="center">Country</th>
                <th width="100" align="center">Issue Qty</th> 
                <th width="" align="center">Location</th>
            </thead>
			<tbody>
				<?php  
					$i=1;
					$total_production_qnty=0;

					$sqlResult =sql_select("SELECT a.id, a.item_number_id, a.country_id, a.production_quantity, a.location from pro_garments_production_mst a,pro_gmts_delivery_mst b where b.id=$data and b.id=a.delivery_mst_id and a.production_type='83' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id");

					foreach($sqlResult as $selectResult){
						if ($i%2==0)  $bgcolor="#E9F3FF";
						else $bgcolor="#FFFFFF";
						$total_production_qnty+=$selectResult[csf('production_quantity')];
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="get_php_form_data('<? echo $selectResult[csf('id')]; ?>','populate_input_form_data','requires/finish_gmts_issue_return_entry_controller');" > 
							<td width="30" align="center"><? echo $i; ?></td>
							<td width="150" align="center"><p><? echo $garments_item[$selectResult[csf('item_number_id')]]; ?></p></td>
							<td width="150" align="center"><p>
								<? 
									echo $country_library[$selectResult[csf('country_id')]]."</br>"; 
									echo "[".$country_short_name[$selectResult[csf('country_id')]]."]";
								?>        		
								</p>
							</td>
							<td width="100" align="center"><?php  echo $selectResult[csf('production_quantity')]; ?></td>
							<?php 
							$location_name= return_field_value("location_name","lib_location","id='".$selectResult[csf('location')]."'");
							?>
							<td width="" align="center"><? echo $location_name; ?></td>
						</tr>
						<?php
						$i++;
					}
					?>
			</tbody>
		</table>
	</div>
	<?
	exit();
}

if($action=="populate_input_form_data")
{
	$data = explode("_",$data);

	$sqlResult =sql_select("SELECT id, company_id, garments_nature, po_break_down_id, item_number_id, challan_no, country_id,  pack_type, production_source, location, production_quantity, production_source, production_type, carton_qty, remarks, floor_id, store_id, room_id, rack_id, shelf_id, total_produced, yet_to_produced, challan_id  from pro_garments_production_mst where id='$data[0]' and production_type='83' and status_active=1 and is_deleted=0 order by id");	
  		
	$dissable='';	
	$company_id=$sqlResult[0][csf('company_id')];
	$challan_id=$sqlResult[0][csf('challan_id')];
	$garments_nature=$sqlResult[0][csf('garments_nature')];
	$company=$sqlResult[0][csf('company_id')];
	/*if($sqlResult[0][csf('production_source')]==1)
	{
		$company=$sqlResult[0][csf('serving_company')];
	}
	else
	{
		$company=$sqlResult[0][csf('company_id')];
	}*/		 
	$control_and_preceding=sql_select("SELECT is_control,preceding_page_id from variable_settings_production where status_active=1 and is_deleted=0 and variable_list=33 and page_category_id=31 and company_name='$company'");  
	$preceding_process= $control_and_preceding[0][csf("preceding_page_id")];
	echo "$('#hidden_variable_cntl').val('".$control_and_preceding[0][csf('is_control')]."');\n";

	$qty_source=82;

	$po_id = $sqlResult[0][csf('po_break_down_id')];
	$item_id = $sqlResult[0][csf('item_number_id')];
	$country_id = $sqlResult[0][csf('country_id')];
	$country_qty_sql = return_library_array("SELECT po_break_down_id, sum(order_quantity) as country_quantity from wo_po_color_size_breakdown where po_break_down_id=$po_id and country_id=$country_id and item_number_id=$item_id and status_active=1 and is_deleted=0 group by po_break_down_id",'po_break_down_id','country_quantity');
	$sql = sql_select("SELECT a.buyer_name,a.style_ref_no,a.job_no,b.po_quantity,b.po_number from wo_po_details_master a,wo_po_break_down b where a.id=b.job_id and b.id=$po_id and b.status_active=1 and b.is_deleted=0");
	
	foreach($sqlResult as $result)
	{ 
		echo "$('#cbo_country_name').val('".$result[csf('country_id')]."');\n";
		echo "$('#cbo_item_name').val('".$result[csf('item_number_id')]."');\n";
		echo "$('#txt_mst_id').val('".$data[0]."');\n";

		echo "$('#txt_job_no').val('".$sql[0]['JOB_NO']."');\n";
		echo "$('#txt_style_no').val('".$sql[0]['STYLE_REF_NO']."');\n";
		echo "$('#cbo_buyer_name').val('".$sql[0]['BUYER_NAME']."');\n";
		echo "$('#txt_order_qty').val('".$sql[0]['PO_QUANTITY']."');\n";
		echo "$('#txt_order_no').val('".$sql[0]['PO_NUMBER']."');\n";
		echo "$('#txt_country_qty').val('".$country_qty_sql[$po_id]."');\n";
		echo "$('#hidden_po_break_down_id').val('$po_id');\n";
		echo "$('#garments_nature').val('$garments_nature');\n";

		if($result['FLOOR_ID']>0)
		{
			echo "load_room_rack_self_bin('requires/finish_gmts_issue_return_entry_controller', 'room','room_td', '".$company."','"."','".$result['STORE_ID']."','".$result['FLOOR_ID']."',this.value);\n";
		}
		echo "document.getElementById('cbo_room').value 					= '".$result['ROOM_ID']."';\n";
		if($result['ROOM_ID']>0)
		{
			echo "load_room_rack_self_bin('requires/finish_gmts_issue_return_entry_controller', 'rack','rack_td', '".$company."','"."','".$result['STORE_ID']."','".$result['FLOOR_ID']."','".$result['ROOM_ID']."',this.value);\n";
		}
		echo "document.getElementById('txt_rack').value 					= '".$result['RACK_ID']."';\n";
		if($result['RACK_ID']>0)
		{
			echo "load_room_rack_self_bin('requires/finish_gmts_issue_return_entry_controller', 'shelf','shelf_td', '".$company."','"."','".$result['STORE_ID']."','".$result['FLOOR_ID']."','".$result['ROOM_ID']."','".$result['RACK_ID']."',this.value);\n";
		}
		echo "document.getElementById('txt_shelf').value 					= '".$result["SHELF_ID"]."';\n";

 		echo "$('#txt_finishing_qty').val('".$result[csf('production_quantity')]."');\n";
		echo "$('#txt_carton_qty').val('".$result[csf('carton_qty')]."');\n";
  		echo "$('#txt_remark').val('".$result[csf('remarks')]."');\n";

		$dataSql="SELECT SUM(CASE WHEN production_type='$qty_source' and delivery_mst_id='$challan_id' THEN production_quantity END) as totalDelivery,SUM(CASE WHEN production_type=83 and challan_id='$challan_id' THEN production_quantity ELSE 0 END) as totalRcv, SUM(CASE WHEN production_type='$qty_source' and delivery_mst_id='$challan_id' THEN carton_qty END) as cartonDelivery,SUM(CASE WHEN production_type=83 and challan_id='$challan_id' THEN carton_qty ELSE 0 END) as cartonRcv from pro_garments_production_mst WHERE po_break_down_id=".$result[csf('po_break_down_id')]."  and item_number_id=".$result[csf('item_number_id')]." and country_id=".$result[csf('country_id')]." and status_active=1 and is_deleted=0";

		$dataArray=sql_select($dataSql);
 		foreach($dataArray as $row)
		{  
			echo "$('#txt_issue_input_qty').val('".$row[csf('totalDelivery')]."');\n";
			echo "$('#txt_cumul_issue_rtn_qty').val('".$row[csf('totalRcv')]."');\n";			
			echo "$('#txt_issue_input_carton').val('".$row[csf('cartonDelivery')]."');\n";
			echo "$('#txt_cumul_issue_rtn_carton').val('".$row[csf('cartonRcv')]."');\n";			
			$yet_to_produced = $row[csf('totalDelivery')]-$row[csf('totalRcv')];
			$can_to_produced = $row[csf('totalDelivery')]+$result[csf('production_quantity')]-$row[csf('totalRcv')];
			$yet_to_carton_produced = $row[csf('cartonDelivery')]+$result[csf('carton_qty')]-$row[csf('cartonRcv')];
			echo "$('#hdn_finishing_qty').val('".$can_to_produced."');\n";
			echo "$('#txt_yet_to_carton_issue_rtn').val('".$yet_to_carton_produced."');\n";
			echo "$('#txt_yet_to_issue_rtn').val('".$yet_to_produced."');\n";
		}		
		
 		echo "set_button_status(1, permission, 'fnc_gmt_issue_rtn_entry',1);\n";
 		echo "disable_enable_fields('cbo_room*txt_rack*txt_shelf',1);\n";
		
		 
		$color_library=return_library_array( "select id, color_name from lib_color",'id','color_name');
		$size_library=return_library_array( "select id, size_name from lib_size",'id','size_name');

		$variableSettings = $result[csf('entry_break_down_type')];
		
		if( $variableSettings!=1 ) // gross level
		{ 			
			$sql_dtls = sql_select("SELECT color_size_break_down_id, production_qnty, reject_qty, size_number_id, color_number_id from  pro_garments_production_dtls a,wo_po_color_size_breakdown b where a.mst_id=$data[0] and a.status_active=1 and a.color_size_break_down_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.status_active in(1,2,3) and b.is_deleted=0 and a.challan_id='$challan_id' and country_id='$country_id' ");	
			foreach($sql_dtls as $row)
			{				  
				if( $variableSettings==2 ) $index = $row[csf('color_number_id')]; else $index = $row[csf('size_number_id')].$color_arr[$row[csf("color_number_id")]].$row[csf('color_number_id')];
			  	$amountArr[$index] = $row[csf('production_qnty')];
			}  
			//print_r($amountArr);
			
			
			$color_size_qty_arr=array();
			$color_size_sql=sql_select ("SELECT  po_break_down_id,item_number_id,size_number_id,color_number_id,sum(plan_cut_qnty) as plan_cut_qnty  from wo_po_color_size_breakdown where status_active in(1,2,3) and  is_deleted=0    and  po_break_down_id in (".str_replace("'","",$po_id).") and item_number_id=$item_id $pack_typeCond group by po_break_down_id,item_number_id,size_number_id,color_number_id");
			foreach($color_size_sql as $s_id)
			{
				$color_size_qty_arr[$s_id[csf('color_number_id')]][$s_id[csf('size_number_id')]]+=$s_id[csf('plan_cut_qnty')]; 
			}
			
			if( $variableSettings==2 ) // color level
			{			 
			 	$sql = "SELECT a.item_number_id, a.color_number_id, sum(a.order_quantity) as order_quantity, sum(a.plan_cut_qnty) as plan_cut_qnty,sum(CASE WHEN c.production_type='$qty_source' and a.delivery_mst_id='$challan_id' then b.production_qnty ELSE 0 END) as production_qnty,sum(CASE WHEN c.production_type=83 and a.challan_id='$challan_id' then b.production_qnty ELSE 0 END) as cur_production_qnty from wo_po_color_size_breakdown a left join pro_garments_production_dtls b on a.id=b.color_size_break_down_id left join pro_garments_production_mst c on c.id=b.mst_id where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' $packTypecond and a.is_deleted=0 and a.status_active in(1,2,3)   group by a.item_number_id, a.color_number_id";	
			 	$sql_plan_cut="SELECT color_number_id, sum(plan_cut_qnty) as quantity from wo_po_color_size_breakdown where po_break_down_id='$po_id' and item_number_id='$item_id' and  country_id='$country_id' and status_active in(1,2,3) and is_deleted=0 group by color_number_id";
			 	foreach(sql_select($sql_plan_cut) as $key=>$value)
			 	{
			 		$plan_cut_arr[$value[csf("color_number_id")]] +=$value[csf("quantity")];
			 	}
			 	
			}
			else if( $variableSettings==3 ) //color and size level
			{				
				$dtlsData = "SELECT a.color_size_break_down_id,sum(CASE WHEN a.production_type='$qty_source' and a.delivery_mst_id='$challan_id' then a.production_qnty ELSE 0 END) as production_qnty,sum(CASE WHEN a.production_type=83 and a.challan_id='$challan_id' then a.production_qnty ELSE 0 END) as cur_production_qnty  from pro_garments_production_dtls a, pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in($qty_source,83) group by a.color_size_break_down_id";

				$dtlsData=sql_select($dtlsData);
									
				foreach($dtlsData as $row)
				{				  
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['iss']= $row[csf('production_qnty')];
 					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv']= $row[csf('cur_production_qnty')];
				} 
				
				$sql = "SELECT id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty from wo_po_color_size_breakdown where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' $pack_typeCond and is_deleted=0 and status_active in(1,2,3) order by color_number_id,size_order";
			}
			else // by default color and size level
			{
				$dtlsData = sql_select("SELECT a.color_size_break_down_id,sum(CASE WHEN a.production_type='$qty_source' and a.delivery_mst_id='$challan_id' then a.production_qnty ELSE 0 END) as production_qnty,sum(CASE WHEN a.production_type=83 and a.challan_id='$challan_id' then a.production_qnty ELSE 0 END) as cur_production_qnty from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in($qty_source,83) group by a.color_size_break_down_id");
									
				foreach($dtlsData as $row)
				{				  
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['iss']= $row[csf('production_qnty')];
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv']= $row[csf('cur_production_qnty')];
				} 
				
				$sql="SELECT id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty from wo_po_color_size_breakdown where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active in(1,2,3)  order by color_number_id,size_order";
			}
 			//echo $sql;die;
						
 			$colorResult = sql_select($sql);
 			//print_r($sql);die;
			$colorHTML="";
			$colorID='';
			$chkColor = array(); 
			$i=0;$totalQnty=0;$colorWiseTotal=0;
			$fabric_amount_total=0;
			foreach($colorResult as $color)
			{
				if( $variableSettings==2 ) // color level
				{  
					$production_quantity=$color[csf("production_qnty")];
					$amount = $amountArr[$color[csf("color_number_id")]];

					if($amount<1){$disable_for_posted="readonly";}else{$disable_for_posted="";}
					$colorHTML .='<tr><td>'.$color_library[$color[csf("color_number_id")]].'</td><td><input type="text" name="txt_color" id="colSize_'.($i+1).'" style="width:60px"  class="text_boxes_numeric" placeholder="'.($production_quantity-$color[csf("cur_production_qnty")]+$amount).'" value="'.$amount.'" onblur="fn_colorlevel_total('.($i+1).')" '.$disable_for_posted.'  ></td><td></td></tr>';
					$totalQnty += $amount;
					$colorID .= $color[csf("color_number_id")].",";
				}
				else //color and size level
				{
					$index = $color[csf("size_number_id")].$color_arr[$color[csf("color_number_id")]].$color[csf("color_number_id")];
					$amount = $amountArr[$index];
					if( !in_array( $color[csf("color_number_id")], $chkColor ) )
					{
						if( $i!=0 ) $colorHTML .= "</table></div>";
						$i=0;$colorWiseTotal=0;
						$colorHTML .= '<h3 align="left" id="accordion_h'.$color[csf("color_number_id")].'" style="width:300px" class="accordion_h" onClick="accordion_menu( this.id,\'content_search_panel_'.$color[csf("color_number_id")].'\', \'\',1)"> <span id="accordion_h'.$color[csf("color_number_id")].'span">+</span>'.$color_library[$color[csf("color_number_id")]].' : <span id="total_'.$color[csf("color_number_id")].'"></span> </h3>';
						$colorHTML .= '<div id="content_search_panel_'.$color[csf("color_number_id")].'" style="display:none" class="accord_close"><table id="table_'.$color[csf("color_number_id")].'">';
						$chkColor[] = $color[csf("color_number_id")];
						$totalFn .= "fn_total(".$color[csf("color_number_id")].");";
					}
 					$colorID .= $color[csf("size_number_id")]."*".$color[csf("color_number_id")].",";
					
					$iss_qnty=$color_size_qnty_array[$color[csf('id')]]['iss'];
					$rcv_qnty=$color_size_qnty_array[$color[csf('id')]]['rcv'];

					if($amount<1){$disable_for_posted="readonly";}else{$disable_for_posted="";}
					$colorHTML .='<tr><td>'.$size_library[$color[csf("size_number_id")]].'</td><td><input type="text" name="colorSize" id="colSize_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:80px" placeholder="'.($iss_qnty-$rcv_qnty+$amount).'" onblur="fn_total('.$color[csf("color_number_id")].','.($i+1).')" value="'.$amount.'" '.$disable_for_posted.' ><input type="text" name="colorSizePOQnty" id="colorSizePOQnty_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:50px" value="'.$color[csf("order_quantity")].'" readonly disabled></td></tr>';				
					$colorWiseTotal += $amount;
				}
				$i++; 
			}
			//echo $colorHTML;die; 
			if( $variableSettings==2 ){ $colorHTML = '<table id="table_color" class="rpt_table"><thead><th width="70">Color</th><th width="60">Quantity</th><th width="60">Reject</th></thead><tbody>'.$colorHTML.'<tbody><tfoot><tr><th>Total</th><th><input type="text" id="total_color" placeholder="'.$totalQnty.'" value="'.$totalQnty.'" class="text_boxes_numeric" style="width:60px"  ></th><th><input type="text" id="total_color_rej" placeholder="'.$totalRejQnty.'" value="'.$totalRejQnty.'" class="text_boxes_numeric" style="width:60px" ></th></tr></tfoot></table>'; }
			echo "$('#breakdown_td_id').html('".addslashes($colorHTML)."');\n";
			if( $variableSettings==3 )echo "$totalFn;\n";
			$colorList = substr($colorID,0,-1);
			echo "$('#hidden_colorSizeID').val('".$colorList."');\n";
		}
		else
		{
			$po_id = $result[csf('po_break_down_id')];
			$item_id = $result[csf('item_number_id')];
			$country_id = $result[csf('country_id')];

			$color_size_qty_arr=array();
			$color_size_sql=sql_select ("SELECT  po_break_down_id,item_number_id,size_number_id,color_number_id,sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown where is_deleted=0 and status_active in(1,2,3) and po_break_down_id in (".str_replace("'","",$po_id).") and item_number_id=$item_id group by po_break_down_id,item_number_id,size_number_id,color_number_id");
			foreach($color_size_sql as $s_id)
			{
				$color_size_qty_arr[$s_id[csf('color_number_id')]][$s_id[csf('size_number_id')]]+=$s_id[csf('plan_cut_qnty')]; 
			}			
						
		}
		//end if condtion
		//#############################################################################################//
	}
 	exit();		
}

if($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));  
	$is_projected_po_allow=return_field_value("production_entry","variable_settings_production","variable_list=58 and company_name=$cbo_company_id");
	if($is_projected_po_allow ==2)
	{
		$is_projected_po=return_field_value("is_confirmed","wo_po_break_down","status_active in(1,2,3) and id=$hidden_po_break_down_id");
		if($is_projected_po==2)
		{			
			echo "786**Projected PO is not allowed to production. Please check variable settings";die();
		}
	}
	
	if(!str_replace("'","",$sewing_production_variable)) $sewing_production_variable=3;
 	$control_and_preceding=sql_select("select is_control,preceding_page_id from variable_settings_production where status_active=1 and is_deleted=0 and variable_list=33 and page_category_id=31 and company_name=$cbo_company_id");  
    $is_control=$control_and_preceding[0][csf("is_control")];
    $preceding_process=$control_and_preceding[0][csf("preceding_page_id")];
	 
	$qty_source = 82;
	if ($operation==0) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		$txt_challan_id=str_replace("'","",$txt_challan_id);
		//table lock here 
		//if  ( check_table_status( 160, 1 )==0 ) { echo "15**0"; die;}
		//----------Compare by finishing qty and iron qty qty for validation----------------
		$txt_finishing_qty=str_replace("'","",$txt_finishing_qty);
		if($txt_finishing_qty=='')$txt_finishing_qty=0;
		$txt_carton_qty=str_replace("'","",$txt_carton_qty);
		if($txt_carton_qty=='')$txt_carton_qty=0;
		$is_fullshipment=return_field_value("shiping_status","wo_po_break_down","id=$hidden_po_break_down_id");
		if($is_fullshipment==3)
		{
			echo "505";disconnect($con);die;
		}
		
		if($is_control==1 && $user_level!=2)
		{
			if(str_replace("'","",$txt_pack_type)=="") $packType_cond=""; else $packType_cond=" and pack_type=$txt_pack_type";
			$country_iron_qty=return_field_value("sum(production_quantity)","pro_garments_production_mst","po_break_down_id=$hidden_po_break_down_id and production_type='$qty_source' and country_id=$cbo_country_name $packType_cond and status_active=1 and is_deleted=0");
			
			$country_finishing_qty=return_field_value("sum(production_quantity)","pro_garments_production_mst","po_break_down_id=$hidden_po_break_down_id and production_type=83 and country_id=$cbo_country_name $packType_cond and status_active=1 and is_deleted=0");
		
			if($country_iron_qty < $country_finishing_qty+$txt_finishing_qty)
			{
				echo "25**0";
				//check_table_status( 160,0);
				disconnect($con);
				die;
			}

			$country_carton_qty=return_field_value("sum(carton_qty)","pro_garments_production_mst","po_break_down_id=$hidden_po_break_down_id and production_type='$qty_source' and country_id=$cbo_country_name $packType_cond and status_active=1 and is_deleted=0");
			
			$country_carton_finishing_qty=return_field_value("sum(carton_qty)","pro_garments_production_mst","po_break_down_id=$hidden_po_break_down_id and production_type=83 and country_id=$cbo_country_name $packType_cond and status_active=1 and is_deleted=0");
		
			if($country_carton_qty < $country_carton_finishing_qty+$txt_carton_qty)
			{
				echo "25**0";
				//check_table_status( 160,0);
				disconnect($con);
				die;
			}
		}
		//--------------------------------------------------------------Compare end;

		if (str_replace("'", "", $txt_system_id) == "") 
		{
            if ($db_type == 0) $year_cond = "YEAR(insert_date)";
            else if ($db_type == 2) $year_cond="to_char(insert_date,'YYYY')";
            else $year_cond = "";//defined Later

			$new_sys_number = explode("*", return_next_id_by_sequence("", "pro_gmts_delivery_mst",$con,1,$cbo_company_id,'FGIRE',504,date("Y",time()),0,0,83,0,0 ));
			$field_array_delivery = "id, sys_number_prefix, sys_number_prefix_num, sys_number, challan_id, challan_no, company_id, production_type, entry_form,location_id,store_id,floor_id,purpose_id,delivery_date,remarks, inserted_by, insert_date";
			$mst_id = return_next_id_by_sequence(  "pro_gmts_delivery_mst_seq", "pro_gmts_delivery_mst", $con );
            $data_array_delivery = "(" . $mst_id . ",'" . $new_sys_number[1] . "','" .(int) $new_sys_number[2] . "','" . $new_sys_number[0] . "', " . $txt_challan_id ."," . $txt_challan_no ."," . $cbo_company_id . ",83,504,".$cbo_location.",".$cbo_store_name ."," . $cbo_floor ."," . $cbo_purpose ."," . $txt_issue_rtn_date  ."," . $txt_remark . "," . $user_id . ",'" . $pc_date_time . "')";
            $challan_no =(int) $new_sys_number[2];
            $txt_system_no = $new_sys_number[0];
        } 
        else 
        {
            $mst_id = str_replace("'", "", $txt_system_id);
            $txt_chal_no = explode("-", str_replace("'", "", $txt_system_no));
            $challan_no = (int)$txt_chal_no[3];

            $field_array_delivery = "purpose_id*remarks*updated_by*update_date";
            $data_array_delivery = "".$cbo_purpose."*".$txt_remark."*".$user_id."*'".$pc_date_time."'";
        }
		//$id=return_next_id("id", "pro_garments_production_mst", 1);

		$id= return_next_id_by_sequence(  "pro_gar_production_mst_seq",  "pro_garments_production_mst", $con );
		
  		$field_array1="id, garments_nature, company_id, challan_id, challan_no, po_break_down_id, item_number_id, country_id, location,production_date, production_quantity, production_type, entry_break_down_type, carton_qty, remarks, store_id, floor_id, room_id, rack_id, shelf_id, total_produced, yet_to_produced,delivery_mst_id, inserted_by, insert_date"; 
		if($db_type==0)
		{
			$data_array1="(".$id.",".$garments_nature.",".$cbo_company_id.",".$txt_challan_id.",".$challan_no.",".$hidden_po_break_down_id.",".$cbo_item_name.",".$cbo_country_name.",".$cbo_location.",".$txt_issue_rtn_date.",".$txt_finishing_qty.",83,".$sewing_production_variable.",".$txt_carton_qty.",".$txt_remark.",".$cbo_floor.",".$txt_cumul_issue_rtn_qty.",".$txt_yet_to_issue_rtn.",".$mst_id.",".$user_id.",'".$pc_date_time."')";
		}
	  	else if($db_type==2)
		{
			$data_array1="INSERT INTO pro_garments_production_mst (".$field_array1.") values(".$id.",".$garments_nature.",".$cbo_company_id.",".$txt_challan_id.",".$challan_no.",".$hidden_po_break_down_id.",".$cbo_item_name.",".$cbo_country_name.",".$cbo_location.",".$txt_issue_rtn_date.",".$txt_finishing_qty.",83,".$sewing_production_variable.",".$txt_carton_qty.",".$txt_remark.",".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$txt_cumul_issue_rtn_qty.",".$txt_yet_to_issue_rtn.",".$mst_id.",".$user_id.",'".$pc_date_time."')";
		}
 		//$rID=sql_insert("pro_garments_production_mst",$field_array1,$data_array1,1);
		//echo $data_array;die;
		
		// pro_garments_production_dtls table entry here ----------------------------------///
		$field_array="id, mst_id, production_type, color_size_break_down_id, production_qnty, delivery_mst_id, challan_id";
		if(str_replace("'","",$txt_pack_type)=="") $pack_type_cond=""; else $pack_type_cond=" and b.pack_type=$txt_pack_type";
		$dtlsData = sql_select("SELECT a.color_size_break_down_id,
			sum(CASE WHEN a.production_type='$qty_source' and a.delivery_mst_id='$txt_challan_id' then a.production_qnty ELSE 0 END) as production_qnty,
			sum(CASE WHEN a.production_type=83 and a.challan_id='$txt_challan_id' then a.production_qnty ELSE 0 END) as cur_production_qnty 
			from pro_garments_production_dtls a,pro_garments_production_mst b 
			where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id=$hidden_po_break_down_id and b.item_number_id=$cbo_item_name and b.country_id=$cbo_country_name and a.color_size_break_down_id<>0 and a.production_type in($qty_source,83) $pack_type_cond
			group by a.color_size_break_down_id");
		$color_pord_data=array();							
		foreach($dtlsData as $row)
		{				  
			$color_pord_data[$row[csf("color_size_break_down_id")]]=$row[csf('production_qnty')]-$row[csf("cur_production_qnty")];
		}
  		if(str_replace("'","",$txt_pack_type)=="") $packType_cond=""; else $packType_cond=" and pack_type=$txt_pack_type"; 
		if(str_replace("'","",$sewing_production_variable)==2)//color level wise
		{		
			$color_sizeID_arr=sql_select( "SELECT id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name  and status_active in(1,2,3) and is_deleted=0  and country_id=$cbo_country_name  $packType_cond order by id" );
			$colSizeID_arr=array(); 
			foreach($color_sizeID_arr as $val){
				$index = $val[csf("color_number_id")];
				$colSizeID_arr[$index]=$val[csf("id")];
			}	
			// $colorIDvalue concate as colorID*Value**colorID*Value -------------------------//
			
 			$rowEx = explode("**",$colorIDvalue); 
 			//$dtls_id=return_next_id("id", "pro_garments_production_dtls", 1);
			$data_array="";$j=0;
			foreach($rowEx as $rowE=>$val)
			{
				$colorSizeNumberIDArr = explode("*",$val);
				
				//8 for Garments Finishing Entry
				$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",  "pro_garments_production_dtls", $con );
				if($j==0)$data_array = "(".$dtls_id.",".$id.",83,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."','".$mst_id."',".$txt_challan_id.")";
				else $data_array .= ",(".$dtls_id.",".$id.",83,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."','".$mst_id."',".$txt_challan_id.")";
				//$dtls_id=$dtls_id+1;							
 				$j++;								
			}
 		}//color level wise
		
		if(str_replace("'","",$sewing_production_variable)==3)//color and size wise
		{		
			$color_sizeID_arr=sql_select( "SELECT id,size_number_id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name  and status_active in(1,2,3) and is_deleted=0  and country_id=$cbo_country_name $packType_cond order by size_number_id,color_number_id" );
			$colSizeID_arr=array(); 
			foreach($color_sizeID_arr as $val){
				$index = $val[csf("size_number_id")].$color_arr[$val[csf("color_number_id")]].$val[csf("color_number_id")];
				$colSizeID_arr[$index]=$val[csf("id")];
			}	
			//	colorIDvalue concate as sizeID*colorID*value***sizeID*colorID*value	--------------------------// 
				
 			$rowEx = array_filter(explode("***",$colorIDvalue)); 
			//$dtls_id=return_next_id("id", "pro_garments_production_dtls", 1);
			$data_array="";$j=0;
			foreach($rowEx as $rowE=>$valE)
			{
				$colorAndSizeAndValue_arr = explode("*",$valE);
				$sizeID = $colorAndSizeAndValue_arr[0];
				$colorID = $colorAndSizeAndValue_arr[1];				
				$colorSizeValue = $colorAndSizeAndValue_arr[2];
				$index = $sizeID.$color_arr[$colorID].$colorID;

				$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",   "pro_garments_production_dtls", $con );
				if($j==0)$data_array = "(".$dtls_id.",".$id.",83,'".$colSizeID_arr[$index]."','".$colorSizeValue."','".$mst_id."',".$txt_challan_id.")";
				else $data_array .= ",(".$dtls_id.",".$id.",83,'".$colSizeID_arr[$index]."','".$colorSizeValue."','".$mst_id."',".$txt_challan_id.")";
				//$dtls_id=$dtls_id+1;
 				$j++;
			}
		}//color and size wise
		// echo "INSERT INTO pro_gmts_delivery_mst (".$field_array_delivery.") VALUES ".$data_array_delivery; disconnect($con);die;
		if (str_replace("'", "", $txt_system_id) == "") 
		{
            $challanrID = sql_insert("pro_gmts_delivery_mst", $field_array_delivery, $data_array_delivery, 1);
        } 
        else 
        {
            $challanrID = sql_update("pro_gmts_delivery_mst", $field_array_delivery, $data_array_delivery, "id", $txt_system_id, 1);
        }
		// echo "INSERT INTO pro_garments_production_mst (".$field_array1.") VALUES ".$data_array1; disconnect($con);die;
		if($db_type==0)
		{
			$rID=sql_insert("pro_garments_production_mst",$field_array1,$data_array1,1);
		}
		else
		{
			$rID=execute_query($data_array1);	
		}
		if(str_replace("'","",$sewing_production_variable)==2 || str_replace("'","",$sewing_production_variable)==3)
		{ 
 			$dtlsrID=sql_insert("pro_garments_production_dtls",$field_array,$data_array,1);
		}
		// echo "10**$rID && $dtlsrID && $challanrID";die;
		
		//release lock table
		//check_table_status( 160,0);
		
		if($db_type==0)
		{  
			if(str_replace("'","",$sewing_production_variable)!=1)
			{
				if($rID && $dtlsrID && $challanrID)
				{
					mysql_query("COMMIT");  
					echo "0**".str_replace("'","",$hidden_po_break_down_id)."**".str_replace("'","",$txt_system_no)."**".$mst_id;
				}
				else
				{
					mysql_query("ROLLBACK"); 
						echo "10**";
				}
			}
			else
			{
				if($rID && $challanrID)
				{
					mysql_query("COMMIT");  
					echo "0**".str_replace("'","",$hidden_po_break_down_id)."**".str_replace("'","",$txt_system_no)."**".$mst_id;
				}
				else
				{
					mysql_query("ROLLBACK"); 
					echo "10**";
				}
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if(str_replace("'","",$sewing_production_variable)!=1)
			{
				if($rID && $dtlsrID && $challanrID)
				{
					oci_commit($con); 
					echo "0**".str_replace("'","",$hidden_po_break_down_id)."**".str_replace("'","",$txt_system_no)."**".$mst_id;
				}
				else
				{
					oci_rollback($con);
					echo "10**";
				}
			}
			else
			{
				if($rID && $challanrID)
				{
					oci_commit($con);  
					echo "0**".str_replace("'","",$hidden_po_break_down_id)."**".str_replace("'","",$txt_system_no)."**".$mst_id;
				}
				else
				{
					oci_rollback($con);
					echo "10**";
				}
			}
		}
		//check_table_status( 160,0);
		disconnect($con);
		die;
	}
  	else if ($operation==1) // Update Here End------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }		
		$txt_finishing_qty=str_replace("'","",$txt_finishing_qty);
		if($txt_finishing_qty=='') $txt_finishing_qty=0;
		$txt_mst_id=str_replace("'","",$txt_mst_id);
		$txt_system_id=str_replace("'","",$txt_system_id);
		$txt_challan_id=str_replace("'","",$txt_challan_id);
		$is_fullshipment=return_field_value("shiping_status","wo_po_break_down","id=$hidden_po_break_down_id");
		if($is_fullshipment==3)
		{
			echo "505";disconnect($con);die;
		}
		
		//--------------------------------------------------------------Compare end;txt_remark
		$field_array_delivery = "purpose_id*remarks*updated_by*update_date";
        $data_array_delivery = "".$cbo_purpose."*".$txt_remark."*".$user_id."*'".$pc_date_time."'";

		// pro_garments_production_mst table data entry here 
		
 		$field_array1="location*production_quantity*entry_break_down_type*carton_qty*store_id*floor_id*room_id*rack_id*shelf_id*total_produced*yet_to_produced*challan_id*updated_by*update_date";
		
		$data_array1="".$cbo_location."*".$txt_finishing_qty."*".$sewing_production_variable."*".$txt_carton_qty."*".$cbo_store_name."*".$cbo_floor."*".$cbo_room."*".$txt_rack."*".$txt_shelf."*".$txt_cumul_issue_rtn_qty."*".$txt_yet_to_issue_rtn."*".$txt_challan_id."*".$user_id."*'".$pc_date_time."'";
				
		if(str_replace("'","",$sewing_production_variable)!=1 && str_replace("'","",$txt_mst_id)!='' ) //  not gross level
		{
			
			$dtlsData = sql_select("SELECT a.color_size_break_down_id,
				sum(CASE WHEN a.production_type='$qty_source' and a.delivery_mst_id='$txt_challan_id' then a.production_qnty ELSE 0 END) as production_qnty,
				sum(CASE WHEN a.production_type=83 and a.challan_id='$txt_challan_id' then a.production_qnty ELSE 0 END) as cur_production_qnty 
				from pro_garments_production_dtls a,pro_garments_production_mst b 
				where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id=$hidden_po_break_down_id and b.item_number_id=$cbo_item_name and b.country_id=$cbo_country_name and a.color_size_break_down_id<>0 and a.production_type in($qty_source,83) and b.id !=$txt_mst_id 
				group by a.color_size_break_down_id");
			$color_pord_data=array();							
			foreach($dtlsData as $row)
			{				  
				$color_pord_data[$row[csf("color_size_break_down_id")]]=$row[csf('production_qnty')]-$row[csf("cur_production_qnty")];
			}
			
			
 			$field_array="id, mst_id, production_type, color_size_break_down_id, production_qnty, delivery_mst_id, challan_id";
			
			if(str_replace("'","",$sewing_production_variable)==2)//color level wise
			{		
				$color_sizeID_arr=sql_select( "SELECT id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name  and status_active in(1,2,3) and is_deleted=0  order by id" );
				$colSizeID_arr=array(); 
				foreach($color_sizeID_arr as $val){
					$index = $val[csf("color_number_id")];
					$colSizeID_arr[$index]=$val[csf("id")];
				}	
				
				// $colorIDvalue concate as colorID*Value**colorID*Value -------------------------//
				
				$rowEx = explode("**",$colorIDvalue); 
				//$dtls_id=return_next_id("id", "pro_garments_production_dtls", 1);
				$data_array="";$j=0;
				foreach($rowEx as $rowE=>$val)
				{
					$colorSizeNumberIDArr = explode("*",$val);
					
					$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",  "pro_garments_production_dtls", $con );
					if($j==0)$data_array = "(".$dtls_id.",".$txt_mst_id.",83,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."','".$txt_system_id."',".$txt_challan_id.")";
					else $data_array .= ",(".$dtls_id.",".$txt_mst_id.",83,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."','".$txt_system_id."',".$txt_challan_id.")";
					$j++;								
				}
			}
			
			if(str_replace("'","",$sewing_production_variable)==3)//color and size wise
			{		
				$color_sizeID_arr=sql_select( "SELECT id,size_number_id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name and status_active in(1,2,3) and is_deleted=0 order by size_number_id,color_number_id" );
				$colSizeID_arr=array(); 
				foreach($color_sizeID_arr as $val){
					$index = $val[csf("size_number_id")].$color_arr[$val[csf("color_number_id")]].$val[csf("color_number_id")];
					$colSizeID_arr[$index]=$val[csf("id")];
				}	
				
				//	colorIDvalue concate as sizeID*colorID*value***sizeID*colorID*value	--------------------------// 
				
				$rowEx = explode("***",$colorIDvalue); 
				//$dtls_id=return_next_id("id", "pro_garments_production_dtls", 1);
				$data_array="";$j=0;
				foreach($rowEx as $rowE=>$valE)
				{
					$colorAndSizeAndValue_arr = explode("*",$valE);
					$sizeID = $colorAndSizeAndValue_arr[0];
					$colorID = $colorAndSizeAndValue_arr[1];				
					$colorSizeValue = $colorAndSizeAndValue_arr[2];
					$index = $sizeID.$color_arr[$colorID].$colorID;

					$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",   "pro_garments_production_dtls", $con );
					if($j==0)$data_array = "(".$dtls_id.",".$txt_mst_id.",83,'".$colSizeID_arr[$index]."','".$colorSizeValue."','".$txt_system_id."',".$txt_challan_id.")";
					else $data_array .= ",(".$dtls_id.",".$txt_mst_id.",83,'".$colSizeID_arr[$index]."','".$colorSizeValue."','".$txt_system_id."',".$txt_challan_id.")";
					//$dtls_id=$dtls_id+1;
					$j++;
				}
			}
		}
		
		$rID = $dtlsrDelete = $dtlsrID = $challanrID=true;
		$dtlsrDelete = execute_query("delete from pro_garments_production_dtls where mst_id=$txt_mst_id",1);
		
		
		$challanrID=sql_update("pro_gmts_delivery_mst",$field_array_delivery,$data_array_delivery,"id","".$txt_system_id."",1);
		$rID=sql_update("pro_garments_production_mst",$field_array1,$data_array1,"id","".$txt_mst_id."",1);
		
		if(str_replace("'","",$sewing_production_variable)!=1 && str_replace("'","",$txt_mst_id)!='')// check is not gross level
		{
			$dtlsrID=sql_insert("pro_garments_production_dtls",$field_array,$data_array,1);
			// echo "10**insert into pro_garments_production_dtls (".$field_array.") values ".$data_array;die;
		}
		
		// echo "10**".$rID."**".$dtlsrDelete."**".$dtlsrID."**".$challanrID;die;
		
		//release lock table
		//check_table_status( 160,0);
		
		if($db_type==0)
		{
			if(str_replace("'","",$sewing_production_variable)!=1)
			{
				if($rID && $dtlsrDelete && $dtlsrID && $challanrID)
				{
					mysql_query("COMMIT");  
					echo "1**".str_replace("'","",$hidden_po_break_down_id)."**".str_replace("'","",$txt_system_no)."**".$txt_system_id;;
				}
				else
				{
					mysql_query("ROLLBACK"); 
					echo "10**".str_replace("'","",$hidden_po_break_down_id)."**".str_replace("'","",$txt_system_no)."**".$txt_system_id;
				}
			}
			else
			{
				if($rID && $challanrID)
				{
					mysql_query("COMMIT");  
					echo "1**".str_replace("'","",$hidden_po_break_down_id)."**".str_replace("'","",$txt_system_no)."**".$txt_system_id;;
				}
				else
				{
					mysql_query("ROLLBACK"); 
					echo "10**";
				}
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if(str_replace("'","",$sewing_production_variable)!=1)
			{
				if($rID && $dtlsrDelete && $dtlsrID && $challanrID)
				{
					oci_commit($con); 
					echo "1**".str_replace("'","",$hidden_po_break_down_id)."**".str_replace("'","",$txt_system_no)."**".$txt_system_id;
				}
				else
				{
					oci_rollback($con);
					echo "10**";
				}
			}
			else
			{
				if($rID && $challanrID)
				{
					oci_commit($con);  
					echo "1**".str_replace("'","",$hidden_po_break_down_id)."**".str_replace("'","",$txt_system_no)."**".$txt_system_id;
				}
				else
				{
					oci_rollback($con);
					echo "10**";
				}
			}
		}
		//check_table_status( 160,0);
		disconnect($con);
		die;
	}
	else if ($operation==2)  // Delete Here---------------------------------------------------------- 
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		$challanrData=sql_select("SELECT id from pro_garments_production_mst where delivery_mst_id=$txt_system_id and status_active=1 and is_deleted=0");
		if(count($challanrData)==1){
			$challanrID = sql_delete("pro_gmts_delivery_mst","updated_by*update_date*status_active*is_deleted","".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1",'id',$txt_system_id,1);
			$restLoad=1;
		}
		else{
			$challanrID = 1;
			$restLoad=2;
		}		 
 		$rID = sql_delete("pro_garments_production_mst","updated_by*update_date*status_active*is_deleted","".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1",'id',$txt_mst_id,1);

		$dtlsrID = sql_delete("pro_garments_production_dtls","status_active*is_deleted","0*1",'mst_id',$txt_mst_id,1);
		// echo "10**".$rID."**".$dtlsrID."**".$challanrID;die;
 		if($db_type==0)
		{
			if($rID && $dtlsrID && $challanrID)
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'","",$hidden_po_break_down_id)."**".str_replace("'","",$txt_system_no)."**".$txt_system_id."**".$restLoad; 
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**"; 
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($rID && $dtlsrID && $challanrID)
			{
				oci_commit($con);   
				echo "2**".str_replace("'","",$hidden_po_break_down_id)."**".str_replace("'","",$txt_system_no)."**".$txt_system_id."**".$restLoad;
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
}

if($action=="order_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
	?>
	<script>
		$(document).ready(function(e) {
            $("#txt_search_common").focus();
        });
		
		function search_populate(str)
		{
			//alert(str); 
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
			else if(str==3)
			{
				document.getElementById('search_by_th_up').innerHTML="Job No";
				document.getElementById('search_by_td').innerHTML='<input type="text" name="txt_search_common" style="width:230px " class="text_boxes" id="txt_search_common"	value=""  />';
			}
			else if(str==4)
			{
				document.getElementById('search_by_th_up').innerHTML="Actual PO No";
				document.getElementById('search_by_td').innerHTML='<input type="text" name="txt_search_common" style="width:230px " class="text_boxes" id="txt_search_common"	value=""  />';
			}
			else if(str==5)
			{
				document.getElementById('search_by_th_up').innerHTML="File No";
				document.getElementById('search_by_td').innerHTML='<input type="text" name="txt_search_common" style="width:230px " class="text_boxes" id="txt_search_common"	value=""  />';
			}
			else if(str==6)
			{
				document.getElementById('search_by_th_up').innerHTML="Internal Ref";
				document.getElementById('search_by_td').innerHTML='<input type="text" name="txt_search_common" style="width:230px " class="text_boxes" id="txt_search_common"	value=""  />';
			}
			else //if(str==2)
			{
				var buyer_name = '<option value="0">--- Select Buyer ---</option>';
				<?php 
				if($db_type==0)
				{
					$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer where find_in_set($company,tag_company) and status_active=1 and is_deleted=0 order by buyer_name",'id','buyer_name');
				}
				else
				{
					$buyer_arr=return_library_array( "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company' $buyer_cond order by buy.buyer_name",'id','buyer_name');
				}				
				foreach($buyer_arr as $key=>$val)
				{
					echo "buyer_name += '<option value=\"$key\">".($val)."</option>';";
				} 
				?>
				document.getElementById('search_by_th_up').innerHTML="Select Buyer Name";
				document.getElementById('search_by_td').innerHTML='<select	name="txt_search_common" style="width:230px " class="combo_boxes" id="txt_search_common">'+ buyer_name +'</select>';
			}																																													
		}

		function js_set_value(data)
		{
			$("#hidden_all_info").val(data);
			parent.emailwindow.hide();
		}
    </script>
	</head>
	<body>
	<div align="center" style="width:100%;" >
	    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	        <table width="580" cellspacing="0" cellpadding="0" class="rpt_table" align="center" border="1" rules="all">
	    		<tr>
	        		<td align="center" width="100%">
	                    <table ellspacing="0" cellpadding="0" width="750" border="1" rules="all" class="rpt_table" align="center">
	                   		 <thead>                	 
	                        	<th width="130">Search By</th>
	                        	<th  width="180" align="center" id="search_by_th_up">Enter Order Number</th>
	                        	<th width="200">Ship Date Range</th>
	                        	<th width="80"><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" /></th>
	                    	</thead>
	        				<tr>
	                    		<td width="130">  
									<? 
										//$searchby_arr=array(0=>"Order No",1=>"Style Ref. Number",2=>"Buyer Name");
										$searchby_arr=array(0=>"Order No",1=>"Style Ref. Number",2=>"Buyer Name",3=>"Job No",4=>"Actual PO No",5=>"File No",6=>"Internal Ref");
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
	                     			<input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>+'_'+<? echo $cbo_location; ?>+'_'+<? echo $cbo_store_name; ?>+'_'+<? echo $cbo_floor; ?>+'_'+<? echo $cbo_room; ?>+'_'+<? echo $txt_rack; ?>+'_'+<? echo $txt_shelf; ?>, 'create_po_search_list_view', 'search_div', 'finish_gmts_issue_return_entry_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:100px;" />
	                            </td>
	        				</tr>
							<tr>
								<td  align="center" height="40" valign="middle" colspan="4">
									<? echo load_month_buttons(1);  ?>
									<input type="hidden" id="hidden_all_info">
								</td>
							</tr>
	             		</table>
	          		</td>
	        	</tr>
	    	</table> 
			  
	        <div style="margin-top:10px" id="search_div"></div>  
	    </form>
	</div>
	</body>           
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}

if($action=="create_po_search_list_view")
{
 	$ex_data = explode("_",$data);
	$txt_search_by = $ex_data[0];
	$txt_search_common = $ex_data[1];
	$txt_date_from = $ex_data[2];
	$txt_date_to = $ex_data[3];
	$company = $ex_data[4];
	$cbo_location = $ex_data[5];
	$cbo_store_name = $ex_data[6];
	$cbo_floor = $ex_data[7];
	$cbo_room = $ex_data[8];
	$txt_rack = $ex_data[9];
	$txt_shelf = $ex_data[10];

	$qty_source=82;
	
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
			$sql_cond = " and a.job_no like '%".trim($txt_search_common)."'";	
		else if(trim($txt_search_by)==4)
			$sql_cond = " and c.acc_po_no like '%".trim($txt_search_common)."%'";
		else if(trim($txt_search_by)==5)
			$sql_cond = " and b.file_no like '%".trim($txt_search_common)."%'";
		else if(trim($txt_search_by)==6)
			$sql_cond = " and b.grouping like '%".trim($txt_search_common)."%'";		
 	}
	if($txt_date_from!="" || $txt_date_to!="") 
	{
		if($db_type==0){$sql_cond .= " and b.shipment_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";}
		if($db_type==2 || $db_type==1){ $sql_cond .= " and b.shipment_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'";}
	}
	
	if(trim($company)!="") $sql_cond .= " and a.company_name='$company'";
	$qty_source_cond="";
	if($qty_source!=0)
	{
		$qty_source_cond="and b.id in(SELECT po_break_down_id from pro_garments_production_mst where production_type='$qty_source' and location=$cbo_location and room_id=$cbo_room and store_id=$cbo_store_name and floor_id=$cbo_floor and rack_id=$txt_rack and shelf_id=$txt_shelf and status_active=1 and is_deleted=0)";
	}

	if(trim($txt_search_by)==4 && trim($txt_search_common)!="")
	{
		$sql = "SELECT b.id, a.order_uom, a.buyer_name, a.company_name, a.total_set_qnty, a.set_break_down, a.job_no, a.style_ref_no, a.gmts_item_id, a.location_name, b.shipment_date, b.po_number,b.file_no,b.grouping, b.po_quantity, b.plan_cut
			from wo_po_details_master a, wo_po_break_down b, wo_po_acc_po_info c
			where a.job_no = b.job_no_mst and b.id=c.po_break_down_id and a.status_active=1 and  a.is_deleted=0 and b.status_active in(1)  and  b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $sql_cond  $qty_source_cond  group by b.id, a.order_uom, a.buyer_name, a.company_name, a.total_set_qnty, a.set_break_down, a.job_no, a.style_ref_no, a.gmts_item_id, a.location_name, b.shipment_date, b.po_number, b.file_no, b.grouping, b.po_quantity, b.plan_cut order by b.shipment_date DESC";
	}
	else
	{
 		$sql = "SELECT b.id, a.order_uom, a.buyer_name, a.company_name, a.total_set_qnty, a.set_break_down, a.job_no, a.style_ref_no, a.gmts_item_id, a.location_name, b.shipment_date, b.po_number,b.file_no,b.grouping, b.po_quantity, b.plan_cut
		 from wo_po_details_master a, wo_po_break_down b 
		 where a.job_no = b.job_no_mst and a.status_active=1 and b.status_active in(1) and a.is_deleted=0  and  b.is_deleted=0 $sql_cond $qty_source_cond order by b.shipment_date DESC"; //and a.garments_nature=$garments_nature
	}
	
	// echo $sql;die;
	$result = sql_select($sql);
	$poIdArr = array();
	foreach ($result as $val) 
	{
		$poIdArr[$val[csf('id')]] = $val[csf('id')];
	}

	$poIds = implode(",", $poIdArr);
	if($poIds !="")
	{
		$po_cond="";
		if(count($poIdArr)>999)
		{
			$chunk_arr=array_chunk($poIdArr,999);
			foreach($chunk_arr as $val)
			{
				$ids=implode(",", $val);
				if($po_cond=="") $po_cond.=" and ( a.po_break_down_id in ($ids) ";
				else
					$po_cond.=" or a.po_break_down_id in ($ids) "; 
			}
			$po_cond.=") ";

		}
		else
		{
			$po_cond.=" and a.po_break_down_id in ($poIds) ";
		}
	}

 	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');

	//$po_country_arr=return_library_array( "select po_break_down_id, $select_field"."_concat(distinct(country_id)) as country from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 group by po_break_down_id",'po_break_down_id','country');
	
	$po_country_data_arr=array();
	$country_sql="SELECT a.po_break_down_id, a.item_number_id, a.country_id, sum(a.order_quantity) as PO_QTY, sum(a.plan_cut_qnty) as PLAN_CUT_QTY from wo_po_color_size_breakdown a, pro_garments_production_mst b where a.po_break_down_id=b.po_break_down_id and b.production_type=82 and a.country_id=b.country_id $po_cond  and b.location=$cbo_location and b.room_id=$cbo_room and b.store_id=$cbo_store_name and b.floor_id=$cbo_floor and b.rack_id=$txt_rack and b.shelf_id=$txt_shelf and a.status_active=1 and  a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.po_break_down_id, a.item_number_id, a.country_id"; 
	// echo $country_sql;die;
	$country_data=sql_select($country_sql);
	foreach($country_data as $row)
	{
		$po_country_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]['po_qty']=$row['PO_QTY'];
		$po_country_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]['plan_cut_qty']=$row['PLAN_CUT_QTY'];
	}
	unset($country_data);
		
	$total_entry_qty_data_arr=array();
	$total_entry_qty=sql_select( "SELECT a.po_break_down_id, a.item_number_id, a.country_id,sum( case when a.production_type=82 then a.production_quantity end ) as RCV_QNTY, sum( case when a.production_type=83 then a.production_quantity end ) as ISSUE_QNTY from pro_garments_production_mst a where a.status_active=1 and a.is_deleted=0 and a.production_type in ('82','83') $po_cond group by a.po_break_down_id, a.item_number_id, a.country_id");

	foreach($total_entry_qty as $row)
	{
		$total_entry_qty_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]['rcv_qnty']=$row['RCV_QNTY'];
		$total_entry_qty_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]['issue_qnty']=$row['ISSUE_QNTY'];
	}
	?>
	<div style="width:1190px;">
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="60">Shipment Date</th>
                <th width="100">Order No</th>
                <th width="100">Job</th>
                <th width="90">Buyer</th>
                <th width="100">Style</th>
                <th width="80">File no</th>
                <th width="80">Internal Ref</th>
                <th width="120">Item</th>
                <th width="100">Country</th>
                <th width="80">Order Qty</th>
                <th width="80">Receive Qty</th>
                <th width="80">Issue Qty</th>
                <th>Balance</th>
            </thead>
     	</table>
     </div>
     <div style="width:1190px; max-height:240px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1172" class="rpt_table" id="tbl_po_list">
			<?
			$i=1;
            foreach( $result as $row )
            {
				foreach($po_country_data_arr[$row[csf('id')]] as $grmts_item=>$item_data)
				{
					foreach($item_data as $country_id=>$val)
					{
						if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$po_qnty=$val['po_qty'];
						$rcv_qnty=$total_entry_qty_data_arr[$row[csf('id')]][$grmts_item][$country_id]['rcv_qnty'];
						$issue_qnty=$total_entry_qty_data_arr[$row[csf('id')]][$grmts_item][$country_id]['issue_qnty'];
						$plan_cut_qnty=$val['plan_cut_qty'];
						$balance=$rcv_qnty-$issue_qnty;
						?>
							<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value('<? echo $row[csf("id")];?>'+'_'+'<? echo $row[csf("po_number")];?>'+'_'+'<? echo $po_qnty;?>'+'_'+'<? echo $row[csf("style_ref_no")];?>'+'_'+'<? echo $row[csf("job_no")];?>'+'_'+'<? echo $row[csf("buyer_name")];?>'+'_'+'<? echo $grmts_item;?>'+'_'+'<? echo $country_id;?>');" > 
								<td width="30" align="center"><?php echo $i; ?></td>
								<td width="60"><p><?php echo change_date_format($row[csf("shipment_date")]);?></p></td>
								<td width="100"><p><?php echo $row[csf("po_number")]; ?></p></td>
								<td width="100"><p><?php echo $row[csf("job_no")]; ?></p></td>		
								<td width="90"><p><?php echo $buyer_arr[$row[csf("buyer_name")]]; ?></p></td>	
								<td width="100"><p><?php echo $row[csf("style_ref_no")]; ?></p></td>
								<td width="80"><p><?php echo $row[csf("file_no")]; ?></p></td>
								<td width="80"><p><?php echo $row[csf("grouping")]; ?></p></td>
								<td width="120"><p><?php echo $garments_item[$grmts_item];?></p></td>	
								<td width="100"><p><?php echo $country_library[$country_id]; ?>&nbsp;</p></td>
								<td width="80" align="right"><?php echo $po_qnty; //$po_qnty*$set_qty;?>&nbsp;</td>
								<td width="80" align="right"><?php  
									echo $rcv_qnty;
								?> &nbsp;</td>
								<td width="80" align="right"><?php echo $issue_qnty; ?>&nbsp;</td>
								<td><?php echo $balance;?>&nbsp;</td> 	
							</tr>
						<? 
						$i++;
						
					}
				}
            }
   		?>
        </table>
    </div>
	<?	
	unset($result);
	exit();	
}

if($action=="color_and_size_level")
{
	$dataArr = explode("**",$data);
	$po_id = $dataArr[0];
	$item_id = $dataArr[1];
	$variableSettings = $dataArr[2];
	$country_id = $dataArr[3];
	$cbo_location= $dataArr[4];
	$cbo_store_name= $dataArr[5];
	$cbo_floor= $dataArr[6];
	$cbo_room= $dataArr[7];
	$txt_rack= $dataArr[8];
	$txt_shelf= $dataArr[9];
	$challan_id= $dataArr[10];

 	$color_library=return_library_array( "SELECT id, color_name from lib_color",'id','color_name');
	$size_library=return_library_array( "SELECT id, size_name from lib_size",'id','size_name');
	$qty_source=82;
	// ==================== set mst form data ===============================

	$po_data_sql = sql_select("SELECT a.BUYER_NAME, a.JOB_NO, a.STYLE_REF_NO, b.PO_NUMBER, b.PO_QUANTITY 
	from wo_po_details_master a, wo_po_break_down b 
	where b.id=$po_id and a.job_no = b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0"); 

	$country_qty_sql = return_library_array("SELECT po_break_down_id, sum(order_quantity) as country_quantity from wo_po_color_size_breakdown where po_break_down_id=$po_id and country_id=$country_id and item_number_id=$item_id and status_active=1 and is_deleted=0 group by po_break_down_id",'po_break_down_id','country_quantity');
	
	$dataSql="SELECT COMPANY_ID,SUM(CASE WHEN production_type='$qty_source' and delivery_mst_id='$challan_id' THEN production_quantity END) as totalRcv,SUM(CASE WHEN production_type=83 and challan_id='$challan_id' THEN production_quantity ELSE 0 END) as totalIssue, SUM(CASE WHEN production_type='$qty_source' and delivery_mst_id='$challan_id' THEN carton_qty END) as cartonRcv,SUM(CASE WHEN production_type=83 and challan_id='$challan_id' THEN carton_qty ELSE 0 END) as cartonIssue from pro_garments_production_mst WHERE po_break_down_id=$po_id  and item_number_id=$item_id and country_id=$country_id and location=$cbo_location and store_id=$cbo_store_name and floor_id=$cbo_floor and room_id=$cbo_room and rack_id=$txt_rack and shelf_id=$txt_shelf and status_active=1 and is_deleted=0 group by company_id";

	// echo $dataSql;die;
	$sqlResult=sql_select($dataSql);
	$cbo_company_id=$sqlResult[0]['COMPANY_ID'];
	if($cbo_floor>0)
	{
		echo "load_room_rack_self_bin('requires/finish_gmts_issue_return_entry_controller', 'room','room_td', '".$cbo_company_id."','"."','".$cbo_store_name."','".$cbo_floor."',this.value);\n";
	}
	echo "document.getElementById('cbo_room').value 					= '".$cbo_room."';\n";
	if($cbo_room>0)
	{
		echo "load_room_rack_self_bin('requires/finish_gmts_issue_return_entry_controller', 'rack','rack_td', '".$cbo_company_id."','"."','".$cbo_store_name."','".$cbo_floor."','".$cbo_room."',this.value);\n";
	}
	echo "document.getElementById('txt_rack').value 					= '".$txt_rack."';\n";
	if($txt_rack>0)
	{
		echo "load_room_rack_self_bin('requires/finish_gmts_issue_return_entry_controller', 'shelf','shelf_td', '".$cbo_company_id."','"."','".$cbo_store_name."','".$cbo_floor."','".$cbo_room."','".$txt_rack."',this.value);\n";
	}
	echo "document.getElementById('txt_shelf').value 					= '".$txt_shelf."';\n";
	echo "$('#txt_job_no').val('".$po_data_sql[0]['JOB_NO']."');\n";
	echo "$('#txt_style_no').val('".$po_data_sql[0]['STYLE_REF_NO']."');\n";
	echo "$('#cbo_buyer_name').val('".$po_data_sql[0]['BUYER_NAME']."');\n";
	echo "$('#txt_order_no').val('".$po_data_sql[0]['PO_NUMBER']."');\n";
	echo "$('#cbo_country_name').val('".$country_id."');\n";
	echo "$('#cbo_item_name').val('".$item_id."');\n";
	echo "$('#hidden_po_break_down_id').val('".$po_id."');\n";
	echo "set_button_status(0, permission, 'fnc_gmt_issue_rtn_entry',1,0);\n";
	foreach($sqlResult as $row)
	{  
		echo "$('#txt_order_qty').val('".$po_data_sql[0]['PO_QUANTITY']."');\n";
		echo "$('#txt_country_qty').val('".$country_qty_sql[$po_id]."');\n";
		echo "$('#txt_issue_input_qty').val('".$row[csf('totalRcv')]."');\n";
		echo "$('#txt_cumul_issue_rtn_qty').val('".$row[csf('totalIssue')]."');\n";			
		echo "$('#txt_issue_input_carton').val('".$row[csf('cartonRcv')]."');\n";
		echo "$('#txt_cumul_issue_rtn_carton').val('".$row[csf('cartonIssue')]."');\n";			
		$yet_to_produced = $row[csf('totalRcv')]-$row[csf('totalIssue')];
		$yet_to_carton_produced = $row[csf('cartonRcv')]-$row[csf('cartonIssue')];
		echo "$('#txt_finishing_qty').val('".$yet_to_produced."');\n";
		echo "$('#hdn_finishing_qty').val('".$yet_to_produced."');\n";
		echo "$('#txt_carton_qty').val('".$yet_to_carton_produced."');\n";

		echo "$('#txt_yet_to_issue_rtn').val('".$yet_to_produced."');\n";
		echo "$('#txt_yet_to_carton_issue_rtn').val('".$yet_to_carton_produced."');\n";
	}
 		
	
	$cumulQty_arr=array();
	if($qty_source!=0)
   	{		
		$dataArray=sql_select("SELECT item_number_id, country_id, SUM(CASE WHEN production_type='$qty_source' and a.delivery_mst_id='$challan_id' THEN production_quantity else 0 END) as totalinput,SUM(CASE WHEN production_type=83 and a.challan_id='$challan_id' THEN production_quantity ELSE 0 END) as totalsewing from pro_garments_production_mst WHERE po_break_down_id=$po_id and item_number_id='$item_id' and country_id='$country_id' and location=$cbo_location and store_id=$cbo_store_name and floor_id=$cbo_floor and room_id=$cbo_room and rack_id=$txt_rack and shelf_id=$txt_shelf and status_active=1 and is_deleted=0 group by item_number_id, country_id");
		foreach($dataArray as $row)
		{ 
			$cumulQty_arr[$row[csf('item_number_id')]][$row[csf('country_id')]]['input']=$row[csf('totalinput')];
			$cumulQty_arr[$row[csf('item_number_id')]][$row[csf('country_id')]]['totalsewing']=$row[csf('totalsewing')];
		}
		unset($dataArray);
	}
	
	
	if( $variableSettings==2 ) // color level
	{
		$color_size_qty_arr=array();
		$color_size_sql=sql_select ("SELECT  po_break_down_id,item_number_id,size_number_id,color_number_id,sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown where status_active in(1,2,3) and   is_deleted=0   and  po_break_down_id in (".str_replace("'","",$po_id).") and item_number_id=$item_id $pack_type_cond group by po_break_down_id,item_number_id,size_number_id,color_number_id");
		foreach($color_size_sql as $s_id)
		{
			$color_size_qty_arr[$s_id[csf('color_number_id')]][$s_id[csf('size_number_id')]]+=$s_id[csf('plan_cut_qnty')]; 
		}
				
		
		if($db_type==0)
		{
			$sql = "SELECT id, item_number_id, size_number_id, color_number_id, order_quantity, sum(plan_cut_qnty) as plan_cut_qnty, (select sum(CASE WHEN pdtls.color_size_break_down_id=wo_po_color_size_breakdown.id then pdtls.production_qnty ELSE 0 END) from pro_garments_production_dtls pdtls where pdtls.production_type='$qty_source' and a.delivery_mst_id='$challan_id' and pdtls.is_deleted=0 ) as production_qnty, (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.production_qnty ELSE 0 END) from pro_garments_production_dtls cur where cur.production_type=83 and a.challan_id='$challan_id' and cur.is_deleted=0 ) as cur_production_qnty  from wo_po_color_size_breakdown where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' $pack_type_cond and status_active in(1,2,3) and is_deleted=0  group by color_number_id";
		}
		else
		{	if( $pack_type=='') $packTypeCond=''; else $packTypeCond=" and a.pack_type='$pack_type'";
			$sql = "SELECT a.item_number_id, a.color_number_id, sum(a.order_quantity) as order_quantity, sum(a.plan_cut_qnty) as plan_cut_qnty, sum(CASE WHEN c.production_type='$qty_source' and a.delivery_mst_id='$challan_id' then b.production_qnty ELSE 0 END) as production_qnty,sum(CASE WHEN c.production_type=83 and a.challan_id='$challan_id' then b.production_qnty ELSE 0 END) as cur_production_qnty from wo_po_color_size_breakdown a left join pro_garments_production_dtls b on a.id=b.color_size_break_down_id left join pro_garments_production_mst c on c.id=b.mst_id where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' $packTypeCond and a.is_deleted=0 and a.status_active in(1,2,3)  group by a.item_number_id, a.color_number_id";	
			
		}
	}
	else  // by default color and size level
	{
		$dtlsData = sql_select("SELECT a.color_size_break_down_id,sum(CASE WHEN a.production_type='$qty_source' and a.delivery_mst_id='$challan_id' then a.production_qnty ELSE 0 END) as production_qnty,sum(CASE WHEN a.production_type=83 and a.challan_id='$challan_id' then a.production_qnty ELSE 0 END) as cur_production_qnty,sum(CASE WHEN a.production_type=83 and a.challan_id='$challan_id' then a.reject_qty ELSE 0 END) as reject_qty from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and b.location=$cbo_location and b.store_id=$cbo_store_name and b.floor_id=$cbo_floor and b.room_id=$cbo_room and b.rack_id=$txt_rack and b.shelf_id=$txt_shelf and a.color_size_break_down_id!=0 and a.production_type in($qty_source,83) $pack_typeCond group by a.color_size_break_down_id");
									
		foreach($dtlsData as $row)
		{				  
			$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['iss']= $row[csf('production_qnty')];
			$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv']= $row[csf('cur_production_qnty')];
		} 
		$sql = "SELECT id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty from wo_po_color_size_breakdown
		where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active in(1,2,3) order by color_number_id, size_order";
	}
		
	$colorResult = sql_select($sql);		
	$colorHTML="";
	$colorID='';
	$chkColor = array(); 
	$i=0;$totalQnty=0;
	if($qty_source!=0)
	{
		foreach($colorResult as $color)
		{
			if( $variableSettings==2 ) // color level
			{ 
				$order_rate=$con_per_dzn[$color[csf("color_number_id")]]/$costing_per_qty;
				$colorHTML .='<tr><td>'.$color_library[$color[csf("color_number_id")]].'</td><td><input type="text" name="txt_color" id="colSize_'.($i+1).'" style="width:60px"  class="text_boxes_numeric" value="'.($color[csf("production_qnty")]-$color[csf("cur_production_qnty")]).'" onblur="fn_colorlevel_total('.($i+1).')"></td><td></td></tr>';				
				$totalQnty += $color[csf("production_qnty")]-$color[csf("cur_production_qnty")];
				$colorID .= $color[csf("color_number_id")].",";
			}
			else //color and size level
			{
				if( !in_array( $color[csf("color_number_id")], $chkColor ) )
				{
					if( $i!=0 ) $colorHTML .= "</table></div>";
					$i=0;
					$colorHTML .= '<h3 align="left" id="accordion_h'.$color[csf("color_number_id")].'" style="width:300px" class="accordion_h" onClick="accordion_menu( this.id,\'content_search_panel_'.$color[csf("color_number_id")].'\', \'\',1)"> <span id="accordion_h'.$color[csf("color_number_id")].'span">+</span>'.$color_library[$color[csf("color_number_id")]].' : <span id="total_'.$color[csf("color_number_id")].'"></span> </h3>';
					$colorHTML .= '<div id="content_search_panel_'.$color[csf("color_number_id")].'" style="display:none" class="accord_close"><table id="table_'.$color[csf("color_number_id")].'">';
					$chkColor[] = $color[csf("color_number_id")];					
				}
				//$index = $color[csf("size_number_id")].$color[csf("color_number_id")];
				$colorID .= $color[csf("size_number_id")]."*".$color[csf("color_number_id")].",";
				
				$iss_qnty=$color_size_qnty_array[$color[csf('id')]]['iss'];
				$rcv_qnty=$color_size_qnty_array[$color[csf('id')]]['rcv'];
				//echo 
	
				$order_rate="";
				if( $con_per_dzn[$color[csf("color_number_id")]][$color[csf("size_number_id")]] && $costing_per_qty)
				{
					$order_rate=$con_per_dzn[$color[csf("color_number_id")]][$color[csf("size_number_id")]]/$costing_per_qty;
				}
				$amount=$iss_qnty-$rcv_qnty;
				if($amount<1){$disable_for_posted="readonly";}else{$disable_for_posted="";}
				$colorHTML .='<tr><td>'.$size_library[$color[csf("size_number_id")]].'</td><td><input type="text" name="colorSize" id="colSize_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:80px" placeholder="'.($amount).'" value="'.($amount).'" onblur="fn_total('.$color[csf("color_number_id")].','.($i+1).')" '.$disable_for_posted.'><input type="text" name="colorSizePOQnty" id="colorSizePOQnty_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:50px" value="'.$color[csf("order_quantity")].'" readonly disabled></td></tr>';				
			}
			$i++; 
		}

	}
	
	//echo $colorHTML;die; 
	if( $variableSettings==2 ){ $colorHTML = '<table id="table_color" class="rpt_table"><thead><th width="70">Color</th><th width="60">Quantity</th><th width="60">Reject</th></thead><tbody>'.$colorHTML.'<tbody><tfoot><tr><th>Total</th><th><input type="text" id="total_color" value="'.$totalQnty.'" class="text_boxes_numeric" style="width:60px" ></th><th><input type="text" id="total_color_rej" class="text_boxes_numeric" style="width:60px" ></th></tr></tfoot></table>'; }
	echo "$('#breakdown_td_id').html('".addslashes($colorHTML)."');\n";
	$colorList = substr($colorID,0,-1);
	echo "$('#hidden_colorSizeID').val('".$colorList."');\n";
	//#############################################################################################//
	exit();
}

if($action=="show_dtls_listview_challan")
{
	$dataArr = explode("**",$data);
	$company_id= $dataArr[0];
	$challan_id= $dataArr[1];
	$variableSettings = $dataArr[2];

	// $location_arr=return_library_array( "SELECT id, location_name from lib_location",'id','location_name');
	$location_info_arr=return_library_array("select floor_room_rack_id,floor_room_rack_name from lib_floor_room_rack_mst where company_id='$company_id'",'floor_room_rack_id','floor_room_rack_name');	
	$qty_source=82;
	?>	 
	<div style="width:100%;">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="120" >Item Name</th>
                <th width="110" >Country</th>
                <th width="80" >Issue Qty</th>
                <th width="80" >Issue Return Qty</th> 
                <th width="80" >Room</th>
                <th width="80" >Rack</th>
                <th >Shelf</th>
            </thead>
			<tbody>
				<?php  
					$i=1;
					$dataSql="SELECT PO_BREAK_DOWN_ID, ITEM_NUMBER_ID, COUNTRY_ID, LOCATION,STORE_ID,FLOOR_ID,ROOM_ID,RACK_ID,SHELF_ID, SUM(CASE WHEN production_type='$qty_source' and delivery_mst_id=$challan_id THEN production_quantity END) as TOTALISSUE,SUM(CASE WHEN production_type=83 and challan_id=$challan_id THEN production_quantity ELSE 0 END) as TOTALISSUERETURN 
					from pro_garments_production_mst WHERE status_active=1 and is_deleted=0 and (delivery_mst_id=$challan_id or challan_id=$challan_id) and production_type in(82,83) group by po_break_down_id, item_number_id, country_id, location,store_id,floor_id,room_id,rack_id,shelf_id";
					// echo $dataSql;
					$sqlResult =sql_select($dataSql);

					foreach($sqlResult as $row){
						if ($i%2==0)  $bgcolor="#E9F3FF";
						else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="get_php_form_data('<? echo $row['PO_BREAK_DOWN_ID'].'**'.$row['ITEM_NUMBER_ID'].'**'.$variableSettings.'**'.$row['COUNTRY_ID'].'**'.$row['LOCATION'].'**'.$row['STORE_ID'].'**'.$row['FLOOR_ID'].'**'.$row['ROOM_ID'].'**'.$row['RACK_ID'].'**'.$row['SHELF_ID'].'**'.$challan_id;?>', 'color_and_size_level', 'requires/finish_gmts_issue_return_entry_controller'); ">
							<td width="30" align="center"><? echo $i; ?></td>
							<td width="120" align="center"><p><? echo $garments_item[$row['ITEM_NUMBER_ID']]; ?></p></td>
							<td width="110" align="center"><p>
								<? 
									echo $country_library[$row['COUNTRY_ID']]."</br>"; 
									echo "[".$country_short_name[$row['COUNTRY_ID']]."]";
								?>        		
								</p></td>
							<td width="80" align="center"><?php echo $row['TOTALISSUE']; ?></td>
							<td width="80" align="center"><?php  echo $row['TOTALISSUERETURN']; ?></td>
							<td width="80" align="center"><? echo $location_info_arr[$row['ROOM_ID']]; ?></td>
							<td width="80" align="center"><? echo $location_info_arr[$row['RACK_ID']]; ?></td>
							<td align="center"><? echo $location_info_arr[$row['SHELF_ID']]; ?></td>
						</tr>
						<?php
						$i++;
					}
					?>
			</tbody>
		</table>
	</div>
	<?
	exit();
}

if($action=="show_all_listview")
{
	$dataArr = explode("**",$data);
	$location_arr=return_library_array( "SELECT id, location_name from lib_location",'id','location_name');
	$company_arr=return_library_array( "SELECT id, company_name from lib_company",'id','company_name');
	$qty_source=82;
	?>	 
	<div style="width:100%;">
		<table cellspacing="0" width="400" cellpadding="0" border="1" rules="all" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="100" >Item Name</th>
                <th width="100" >Country</th>
                <th width="60" >Issue Qty</th>
                <th width="60" >Issue Return Qty</th> 
                <th >Location</th>
            </thead>
			<tbody>
				<?php  
					$i=1;
					$dataSql="SELECT a.PO_BREAK_DOWN_ID, a.ITEM_NUMBER_ID, a.COUNTRY_ID, a.LOCATION, a.STORE_ID,a.FLOOR_ID,a.ROOM_ID,a.RACK_ID,a.SHELF_ID, SUM(CASE WHEN a.production_type=83 and a.challan_id=$dataArr[2] THEN a.production_quantity ELSE 0 END) as TOTALISSUERETURN from pro_garments_production_mst a,pro_gmts_delivery_mst b WHERE b.id=$dataArr[0] and b.id=a.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.po_break_down_id, a.item_number_id, a.country_id, a.location, a.store_id,a.floor_id,a.room_id,a.rack_id,a.shelf_id";
					// echo $dataSql;
					$sqlResult =sql_select($dataSql);
					$po_info=$location_info=$Store_info=$floor_info=$room_info=$rack_info=$shelf_info='';
					foreach($sqlResult as $row){
						$po_info.=$row['PO_BREAK_DOWN_ID'].',';
						$country_info.=$row['COUNTRY_ID'].',';
						$item_info.=$row['ITEM_NUMBER_ID'].',';
						$location_info.=$row['LOCATION'].',';
						$Store_info.=$row['STORE_ID'].',';
						$floor_info.=$row['FLOOR_ID'].',';
						$room_info.=$row['ROOM_ID'].',';
						$rack_info.=$row['RACK_ID'].',';
						$shelf_info.=$row['SHELF_ID'].',';
					}
					$po_info=rtrim($po_info,',');
					$country_info=rtrim($country_info,',');
					$item_info=rtrim($item_info,',');
					$location_info=rtrim($location_info,',');
					$Store_info=rtrim($Store_info,',');
					$floor_info=rtrim($floor_info,',');
					$room_info=rtrim($room_info,',');
					$rack_info=rtrim($rack_info,',');
					$shelf_info=rtrim($shelf_info,',');

					$dataSqlRcv=sql_select("SELECT a.PO_BREAK_DOWN_ID, a.ITEM_NUMBER_ID, a.COUNTRY_ID, a.LOCATION, a.STORE_ID,a.FLOOR_ID,a.ROOM_ID,a.RACK_ID,a.SHELF_ID, a.PRODUCTION_QUANTITY from pro_garments_production_mst a WHERE a.production_type='$qty_source' and a.delivery_mst_id=$dataArr[2] and a.po_break_down_id in($po_info) and a.country_id in($country_info) and a.item_number_id in($item_info) and a.location in($location_info) and a.store_id in($Store_info) and a.floor_id in($floor_info) and a.room_id in($room_info) and a.rack_id in($rack_info) and a.shelf_id in($shelf_info) and a.status_active=1 and a.is_deleted=0 ");

					$totalIssue=array();
					foreach($dataSqlRcv as $row){
						$totalIssue[$row['PO_BREAK_DOWN_ID']][$row['COUNTRY_ID']][$row['ITEM_NUMBER_ID']][$row['LOCATION']][$row['STORE_ID']][$row['FLOOR_ID']][$row['ROOM_ID']][$row['RACK_ID']][$row['SHELF_ID']]+=$row['PRODUCTION_QUANTITY'];
					}
					
					foreach($sqlResult as $row){
						if ($i%2==0)  $bgcolor="#E9F3FF";
						else $bgcolor="#FFFFFF";
						$production_quantity_issue=$totalIssue[$row['PO_BREAK_DOWN_ID']][$row['COUNTRY_ID']][$row['ITEM_NUMBER_ID']][$row['LOCATION']][$row['STORE_ID']][$row['FLOOR_ID']][$row['ROOM_ID']][$row['RACK_ID']][$row['SHELF_ID']];
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="get_php_form_data('<? echo $row['PO_BREAK_DOWN_ID'].'**'.$row['ITEM_NUMBER_ID'].'**'.$dataArr[1].'**'.$row['COUNTRY_ID'].'**'.$row['LOCATION'].'**'.$row['STORE_ID'].'**'.$row['FLOOR_ID'].'**'.$row['ROOM_ID'].'**'.$row['RACK_ID'].'**'.$row['SHELF_ID'].'**'.$dataArr[2];?>', 'color_and_size_level', 'requires/finish_gmts_issue_return_entry_controller'); "> 
							<td width="30" align="center"><? echo $i; ?></td>
							<td width="100" align="center"><p><? echo $garments_item[$row['ITEM_NUMBER_ID']]; ?></p></td>
							<td width="100" align="center"><p>
								<? 
									echo $country_library[$row['COUNTRY_ID']]."</br>"; 
									echo "[".$country_short_name[$row['COUNTRY_ID']]."]";
								?>        		
								</p></td>
							<td width="60" align="right"><?php echo $production_quantity_issue; ?></td>
							<td width="60" align="right"><?php  echo $row['TOTALISSUERETURN']; ?></td>
							<?php 
								$location_name= return_field_value("location_name","lib_location","id='".$row['LOCATION']."'");
							?>
							<td ><? echo $location_name; ?></td>
						</tr>
						<?php
						$i++;
					}
					?>
			</tbody>
		</table>
	</div>
	<?
	exit();
}

?>