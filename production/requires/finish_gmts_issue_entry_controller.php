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
	echo create_drop_down( "cbo_location", 167, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' $location_credential_cond order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/finish_gmts_issue_entry_controller', this.value, 'load_drop_down_floor', 'floor_td' );",0 );     	 
}

if ($action=="load_drop_down_location")
{    	 
	echo create_drop_down( "cbo_location", 167, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' $location_credential_cond order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/finish_gmts_issue_entry_controller', this.value, 'load_drop_down_store', 'store_td' );",0 );     	 
}

if ($action=="load_drop_down_store")
{
	echo create_drop_down( "cbo_store_name", 170, "SELECT a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id and b.category_type in(30) and a.status_active =1 and a.is_deleted=0 and a.location_id='$data' $store_location_credential_cond order by a.store_name","id,store_name", 1, "-- Select Store --", $selected, "load_room_rack_self_bin('requires/finish_gmts_issue_entry_controller', 'floor','floor_td',document.getElementById('cbo_company_id').value,document.getElementById('cbo_location').value,this.value,this.value);",0 );     
}

if ($action=="load_room_rack_self_bin")
{
	load_room_rack_self_bin("requires/finish_gmts_issue_entry_controller",$data);
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
 		echo create_drop_down( "cbo_finish_company", 170, "select id,company_name from lib_company where is_deleted=0 and status_active=1 $company_credential_cond order by company_name","id,company_name", 1, "--- Select ---", "", "load_drop_down( 'requires/finish_gmts_issue_entry_controller', this.value, 'load_drop_down_location', 'location_td' );fnc_company_check(document.getElementById('cbo_source').value);load_drop_down( 'requires/finish_gmts_issue_entry_controller', this.value, 'load_drop_down_store', 'store_td' );",0,0 ); 
 	else
		echo create_drop_down( "cbo_finish_company", 170, $blank_array,"", 1, "--- Select ---", $selected, "",0,0 );	
			
	exit();
}

if($action=="system_number_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
	?>
	<script>		
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
	        <table ellspacing="0" cellpadding="0" width="750" border="1" rules="all" class="rpt_table" align="center">
	            <thead>
	                <tr>
	                    <th>Company</th>
	                    <th>System No</th>
						<th>Order No</th>
	                    <th>Job No</th>
	                    <th width="200">Date Range</th>
	                    <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:80px;" /></th>
	                </tr>
	            </thead>
	            <tbody>
	                <tr>
	                    <td align="center">
	                    <?
	                    echo create_drop_down( "cbo_company_id", 170, "select company_name,id from lib_company comp where is_deleted=0  and status_active=1 order by company_name",'id,company_name', 1, '--- Select Company ---',$company, "");?>
	                    </td>
	                    
	                    <!-- <td>
	                    <?
	                    echo create_drop_down( "cbo_buyer_name", 120, "select id,buyer_name from lib_buyer where is_deleted=0 and status_active=1 order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "","",0 );
	                    ?>
	                    </td>
	                    <td>
	                    <? echo create_drop_down( "cbo_source", 170, $knitting_source,"", 1, "-- Select Source --", $selected, "fnc_company_check(this.value);load_drop_down( 'requires/finish_gmts_issue_entry_controller', this.value+'**'+$('#cbo_company_id').val(), 'load_drop_down_source', 'finishing_td' );dynamic_must_entry_caption(this.value);", 0, '1,3' ); ?>
	                    </td> -->
	                    <td align="center">
	                    <input type="text" style="width:100px" class="text_boxes"  name="txt_system_no" id="txt_system_no" />
	                    </td>
						<td>
	                    <input type="text" style="width:100px" class="text_boxes"  name="txt_order_no" id="txt_order_no" />
	                    </td>
						<td>
	                    <input type="text" style="width:100px" class="text_boxes"  name="txt_job_no" id="txt_job_no" />
	                    </td>
	                    <td align="center">
	                    <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px"> To
	                    <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px">
	                    </td>
	                    <td align="center">

	                    <input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view( document.getElementById('cbo_company_id').value+'_'+document.getElementById('txt_system_no').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_order_no').value+'_'+document.getElementById('txt_job_no').value, 'create_system_number_list_view', 'search_div', 'finish_gmts_issue_entry_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:80px;" />
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
 	$ex_data = explode("_",$data);

    $company = $ex_data[0];
	$system_no = $ex_data[1];
	$txt_date_from = $ex_data[2];
	$txt_date_to = $ex_data[3];
	$txt_order_no = $ex_data[4];
	$txt_job_no = $ex_data[5];
	//echo $txt_order_no."<br>".$txt_job_no;
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
	
	if(trim($txt_order_no)!="")
	{
		$sql_cond = " and b.po_number like '%".trim($txt_order_no)."'";
	}
	if(trim($txt_job_no)!="")
	{
		$sql_cond = " and c.job_no like '%".trim($txt_job_no)."'";
	}
	// if(trim($source)!='0')
	// {
	// 	$sql_cond .= " and a.production_source='$source'";
	// }

	$sql ="SELECT a.id, a.sys_number, a.company_id, a.floor_id, a.location_id, a.production_source,b.po_number,c.job_no from pro_gmts_delivery_mst a ,wo_po_break_down b, wo_po_details_master c,pro_garments_production_mst d
	where  b.job_no_mst=C.job_no
	and b.id=d.po_break_down_id
	and a.id=d.delivery_mst_id
	and a.status_active=1 and a.is_deleted=0 
	and b.status_active=1 and b.is_deleted=0 
	and c.status_active=1 and c.is_deleted=0 
	and d.status_active=1 and d.is_deleted=0 
	and a.entry_form=503 $sql_cond order by a.id DESC";
	// echo $sql;//die();

	// $arr=array(1=>$company_arr,2=>$knitting_source,3=>$company_arr,4=>$location_arr);
	$arr=array(1=>$company_arr,2=>$location_arr);

	echo create_list_view("list_view", "System Number,Company,Location,Order No,Job No","120,120,120,80,80","620","240",0, $sql , "js_set_value","id,sys_number", "",1, "0,company_id,location_id", $arr,"sys_number,company_id,location_id,po_number,job_no", "","setFilterGrid('list_view',-1)","0,0,0") ;

	exit();
}

if($action=="populate_mst_form_data")
{
	$sql ="SELECT a.id, a.sys_number, a.company_id, a.location_id,a.delivery_date,a.remarks,a.purpose_id,a.store_id as STORE_ID,a.floor_id as FLOOR_ID from pro_gmts_delivery_mst a where a.id='$data' and production_type=82";

	// echo $sql.";\n";
	$result =sql_select($sql);

	echo "load_drop_down( 'requires/finish_gmts_issue_entry_controller', ".$result[0][csf('company_id')].", 'load_drop_down_location', 'cbo_location' );\n";
	echo "get_php_form_data(".$result[0][csf('company_id')].",'load_variable_settings','requires/finish_gmts_issue_entry_controller');\n";
	echo "load_drop_down( 'requires/finish_gmts_issue_entry_controller',  ".$result[0][csf('location_id')].", 'load_drop_down_store', 'store_td' );\n";
	echo "$('#cbo_store_name').val('".$result[0]['STORE_ID']."');\n";

	if($result[0]['STORE_ID']>0)
	{
		echo "load_room_rack_self_bin('requires/finish_gmts_issue_entry_controller', 'floor','floor_td', '".$result[0][csf('company_id')]."','"."','".$result[0]['STORE_ID']."',this.value);\n";
	}
	echo "$('#cbo_floor').val('".$result[0]['FLOOR_ID']."');\n";
	if($result[0]['FLOOR_ID']>0)
	{
		echo "load_room_rack_self_bin('requires/finish_gmts_issue_entry_controller', 'room','room_td', '".$result[0][csf('company_id')]."','"."','".$result[0]['STORE_ID']."','".$result[0]['FLOOR_ID']."',this.value);\n";
	}

	echo "$('#cbo_company_id').val('".$result[0][csf('company_id')]."');\n";
	echo "$('#cbo_location').val('".$result[0][csf('location_id')]."');\n";
	echo "$('#txt_issue_date').val('".change_date_format($result[0][csf('delivery_date')])."');\n";
	echo "$('#cbo_purpose').val('".$result[0][csf('purpose_id')]."');\n";
	echo "$('#txt_remark').val('".$result[0][csf('remarks')]."');\n";
	echo "set_button_status(0, permission, 'fnc_gmt_issue_entry',1);\n";
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

					$sqlResult =sql_select("SELECT a.id,a.item_number_id, a.country_id, a.production_quantity, a.location from pro_garments_production_mst a,pro_gmts_delivery_mst b where b.id=$data and b.id=a.delivery_mst_id and a.production_type='82' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id");

					foreach($sqlResult as $selectResult){
						if ($i%2==0)  $bgcolor="#E9F3FF";
						else $bgcolor="#FFFFFF";
						$total_production_qnty+=$selectResult[csf('production_quantity')];
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="get_php_form_data('<? echo $selectResult[csf('id')]; ?>','populate_input_form_data','requires/finish_gmts_issue_entry_controller');" > 
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

	$sqlResult =sql_select("SELECT id, company_id, garments_nature, po_break_down_id, item_number_id, challan_no, country_id, production_quantity, production_source, production_type, entry_break_down_type, carton_qty, remarks, location,floor_id, store_id, room_id, rack_id, shelf_id, total_produced, yet_to_produced from pro_garments_production_mst where id='$data[0]' and production_type='82' and status_active=1 and is_deleted=0 order by id");	
  		
	$dissable='';	
	$company_id=$sqlResult[0][csf('company_id')];
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
	$control_and_preceding=sql_select("SELECT is_control,preceding_page_id from variable_settings_production where status_active=1 and is_deleted=0 and variable_list=33 and page_category_id=260 and company_name='$company'"); 
	echo "$('#hidden_variable_cntl').val('".$control_and_preceding[0][csf('is_control')]."');\n"; 

	$preceding_process= $control_and_preceding[0][csf("preceding_page_id")];
	$is_control=$control_and_preceding[0][csf("is_control")];
	$qty_source=81;

	$po_id = $sqlResult[0][csf('po_break_down_id')];
	$item_id = $sqlResult[0][csf('item_number_id')];
	$country_id = $sqlResult[0][csf('country_id')];
	$location_id = $sqlResult[0][csf('location')];
	$store_id = $sqlResult[0][csf('store_id')];
	$floor_id = $sqlResult[0][csf('floor_id')];
	$room_id = $sqlResult[0][csf('room_id')];
	$rack_id = $sqlResult[0][csf('rack_id')];
	$shelf_id = $sqlResult[0][csf('shelf_id')];
	$sql = sql_select("SELECT a.buyer_name,a.style_ref_no,a.job_no,b.po_quantity,b.po_number from wo_po_details_master a,wo_po_break_down b where a.id=b.job_id and b.id=$po_id and b.status_active=1 and b.is_deleted=0");
	$country_qty_sql = return_library_array("SELECT po_break_down_id, sum(order_quantity) as country_quantity from wo_po_color_size_breakdown where po_break_down_id=$po_id and country_id=$country_id and item_number_id=$item_id and status_active=1 and is_deleted=0 group by po_break_down_id",'po_break_down_id','country_quantity');
	
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
			echo "load_room_rack_self_bin('requires/finish_gmts_issue_entry_controller', 'room','room_td', '".$company."','"."','".$result['STORE_ID']."','".$result['FLOOR_ID']."',this.value);\n";
		}
		echo "document.getElementById('cbo_room').value 					= '".$result['ROOM_ID']."';\n";
		if($result['ROOM_ID']>0)
		{
			echo "load_room_rack_self_bin('requires/finish_gmts_issue_entry_controller', 'rack','rack_td', '".$company."','"."','".$result['STORE_ID']."','".$result['FLOOR_ID']."','".$result['ROOM_ID']."',this.value);\n";
		}
		echo "document.getElementById('txt_rack').value 					= '".$result['RACK_ID']."';\n";
		if($result['RACK_ID']>0)
		{
			echo "load_room_rack_self_bin('requires/finish_gmts_issue_entry_controller', 'shelf','shelf_td', '".$company."','"."','".$result['STORE_ID']."','".$result['FLOOR_ID']."','".$result['ROOM_ID']."','".$result['RACK_ID']."',this.value);\n";
		}
		echo "document.getElementById('txt_shelf').value 					= '".$result["SHELF_ID"]."';\n";

 		echo "$('#txt_finishing_qty').val('".$result[csf('production_quantity')]."');\n";
		echo "$('#txt_carton_qty').val('".$result[csf('carton_qty')]."');\n";
  		echo "$('#txt_remark').val('".$result[csf('remarks')]."');\n";

		$dataSql="SELECT SUM(CASE WHEN production_type='$qty_source' THEN production_quantity ELSE 0 END) as totalRcv,
		SUM(CASE WHEN production_type=84 THEN production_quantity ELSE 0 END) as totalRcvRtn, 
		SUM(CASE WHEN production_type=82 THEN production_quantity ELSE 0 END) as totalIssue, 
		SUM(CASE WHEN production_type=83 THEN production_quantity ELSE 0 END) as totalIssueRtn, 
		SUM(CASE WHEN production_type='$qty_source' THEN carton_qty ELSE 0 END) as cartonRcv,
		SUM(CASE WHEN production_type=84 THEN carton_qty ELSE 0 END) as cartonRcvRtn, 
		SUM(CASE WHEN production_type=82 THEN carton_qty ELSE 0 END) as cartonIssue, 
		SUM(CASE WHEN production_type=83 THEN carton_qty ELSE 0 END) as cartonIssueRtn 
		from pro_garments_production_mst WHERE po_break_down_id=".$po_id." and item_number_id=".$item_id." and country_id=".$country_id ." and location=".$location_id." and store_id=".$store_id." and floor_id=".$floor_id." and room_id=".$room_id." and rack_id=".$rack_id." and shelf_id=".$shelf_id." and production_type in(81,82,83,84) and status_active=1 and is_deleted=0";

		$dataArray=sql_select($dataSql);
 		foreach($dataArray as $row)
		{  
			// $issue_qnty_balance=$row[csf('totalIssue')]-$row[csf('totalIssueRtn')];
			$issue_carton_balance=$row[csf('cartonIssue')]-$row[csf('cartonIssueRtn')];
			echo "$('#txt_rcv_input_qty').val('".$row[csf('totalRcv')]."');\n";
			echo "$('#txt_rcv_rtn_input_qty').val('".$row[csf('totalRcvRtn')]."');\n";
			echo "$('#txt_cumul_issue_qty').val('".$row[csf('totalIssue')]."');\n";			
			echo "$('#txt_rcv_input_carton').val('".$row[csf('cartonRcv')]."');\n";
			echo "$('#txt_rcv_rtn_input_carton').val('".$row[csf('cartonRcvRtn')]."');\n";
			echo "$('#txt_cumul_issue_carton').val('".$row[csf('cartonIssue')]."');\n";			
			echo "$('#txt_cumul_issue_rtn_qty').val('".$row[csf('totalIssueRtn')]."');\n";			
			$yet_to_produced = $row[csf('totalRcv')]-$row[csf('totalRcvRtn')]-$row[csf('totalIssue')]+$row[csf('totalIssueRtn')];
			$can_to_produced = $row[csf('totalRcv')]-$row[csf('totalRcvRtn')]+$result[csf('production_quantity')]-$row[csf('totalIssue')]+$row[csf('totalIssueRtn')];
			$yet_to_carton_produced = $row[csf('cartonRcv')]-$row[csf('cartonRcvRtn')]+$result[csf('carton_qty')]-$row[csf('cartonIssue')]+$row[csf('cartonIssueRtn')];
			echo "$('#hdn_finishing_qty').val('".$can_to_produced."');\n";
			echo "$('#txt_yet_to_carton_issue').val('".$yet_to_carton_produced."');\n";
			echo "$('#txt_yet_to_issue').val('".$yet_to_produced."');\n";
			$actual_issue = $row[csf('totalIssue')]-$row[csf('totalIssueRtn')];
			echo "$('#txt_actual_issue').val('".$actual_issue."');\n";
		}		
		
 		echo "set_button_status(1, permission, 'fnc_gmt_issue_entry',1);\n";
 		echo "disable_enable_fields('cbo_room*txt_rack*txt_shelf',1);\n";

		$color_library=return_library_array( "select id, color_name from lib_color",'id','color_name');
		$size_library=return_library_array( "select id, size_name from lib_size",'id','size_name');

		$variableSettings = $result[csf('entry_break_down_type')];		
		if( $variableSettings!=1 ) // gross level
		{ 				
			$sql_dtls = sql_select("SELECT a.color_size_break_down_id, a.production_qnty, b.size_number_id, b.color_number_id 
			from  pro_garments_production_dtls a,wo_po_color_size_breakdown b 
			where a.mst_id=$data[0] and a.status_active=1 and a.color_size_break_down_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.status_active=1 and a.is_deleted=0 and b.country_id='$country_id' ");
			foreach($sql_dtls as $row)
			{				  
				if( $variableSettings==2 ) $index = $row[csf('color_number_id')]; else $index = $row[csf('size_number_id')].$color_arr[$row[csf("color_number_id")]].$row[csf("color_number_id")];
			  	$amountArr[$index] = $row[csf('production_qnty')];
			}  
			//print_r($amountArr);
			
			$color_size_qty_arr=array();
			$color_size_sql=sql_select ("SELECT  po_break_down_id,item_number_id,size_number_id,color_number_id,sum(plan_cut_qnty) as plan_cut_qnty  from wo_po_color_size_breakdown where status_active in(1,2,3) and  is_deleted=0    and  po_break_down_id in (".str_replace("'","",$po_id).") and item_number_id=$item_id group by po_break_down_id,item_number_id,size_number_id,color_number_id");
			foreach($color_size_sql as $s_id)
			{
				$color_size_qty_arr[$s_id[csf('color_number_id')]][$s_id[csf('size_number_id')]]+=$s_id[csf('plan_cut_qnty')]; 
			}
			
			if( $variableSettings==2 ) // color level
			{			 
			 	$sql = "SELECT a.item_number_id, a.color_number_id, 
				sum(CASE WHEN c.production_type='$qty_source' then b.production_qnty ELSE 0 END) as rcv_qnty,
				sum(CASE WHEN c.production_type=82 then b.production_qnty ELSE 0 END) as iss_qnty, 
				sum(CASE WHEN c.production_type=83 then b.production_qnty ELSE 0 END) as iss_rtn_qnty, 
				sum(CASE WHEN c.production_type=84 then b.production_qnty ELSE 0 END) as rcv_rtn 
				from wo_po_color_size_breakdown a left join pro_garments_production_dtls b on a.id=b.color_size_break_down_id left join pro_garments_production_mst c on c.id=b.mst_id 
				where a.po_break_down_id='$po_id' and c.production_type in (81,82,83,84) and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active in(1,2,3)   group by a.item_number_id, a.color_number_id";	

			 	$sql_plan_cut="SELECT color_number_id, sum(plan_cut_qnty) as quantity from wo_po_color_size_breakdown where po_break_down_id='$po_id' and item_number_id='$item_id' and  country_id='$country_id' and status_active in(1,2,3) and is_deleted=0 group by color_number_id";
			 	foreach(sql_select($sql_plan_cut) as $key=>$value)
			 	{
			 		$plan_cut_arr[$value[csf("color_number_id")]] +=$value[csf("quantity")];
			 	}
			 	
			}
			else if( $variableSettings==3 ) //color and size level
			{				
				$dtlsData = "SELECT a.color_size_break_down_id,
				sum(CASE WHEN a.production_type='$qty_source' then a.production_qnty ELSE 0 END) as rcv_qnty,
				sum(CASE WHEN a.production_type=84 then a.production_qnty ELSE 0 END) as rcv_rtn_qnty, 
				sum(CASE WHEN a.production_type=82 then a.production_qnty ELSE 0 END) as iss_qnty, 
				sum(CASE WHEN a.production_type=83 then a.production_qnty ELSE 0 END) as iss_rtn_qnty 
				from pro_garments_production_dtls a, pro_garments_production_mst b where a.status_active=1 and b.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and b.location=".$location_id." and b.store_id=".$store_id." and b.floor_id=".$floor_id." and b.room_id=".$room_id." and b.rack_id=".$rack_id." and b.shelf_id=".$shelf_id." and b.production_type in(81,82,83,84) and a.production_type in($qty_source,82,83) group by a.color_size_break_down_id";

				$dtlsData=sql_select($dtlsData);					
				foreach($dtlsData as $row)
				{				  
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv']= $row[csf('rcv_qnty')];
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv_rtn']= $row[csf('rcv_rtn_qnty')];
 					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['iss']= $row[csf('iss_qnty')];
 					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['iss_rtn']= $row[csf('iss_rtn_qnty')];
				} 
				
				$sql = "SELECT id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty from wo_po_color_size_breakdown where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active in(1,2,3) order by color_number_id,size_order";
			}
			else // by default color and size level
			{
				$dtlsData = sql_select("SELECT a.color_size_break_down_id,
				sum(CASE WHEN a.production_type='$qty_source' then a.production_qnty ELSE 0 END) as rcv_qnty,
				sum(CASE WHEN a.production_type=84 then a.production_qnty ELSE 0 END) as rcv_rtn_qnty,
				sum(CASE WHEN a.production_type=82 then a.production_qnty ELSE 0 END) as iss_qnty, 
				sum(CASE WHEN a.production_type=83 then a.production_qnty ELSE 0 END) as iss_rtn_qnty 
				from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and b.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and b.location=".$location_id." and b.store_id=".$store_id." and b.floor_id=".$floor_id." and b.room_id=".$room_id." and b.rack_id=".$rack_id." and b.shelf_id=".$shelf_id." and b.production_type in(81,82,83,84) and a.production_type in($qty_source,82,83,84) group by a.color_size_break_down_id");
									
				foreach($dtlsData as $row)
				{				  
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv']= $row[csf('rcv_qnty')];
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv_rtn']= $row[csf('rcv_rtn_qnty')];
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['iss']= $row[csf('iss_qnty')];
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['iss_rtn']= $row[csf('iss_rtn_qnty')];
				} 
				
				$sql="SELECT id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty from wo_po_color_size_breakdown where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' $pack_cond and is_deleted=0 and status_active in(1,2,3)  order by color_number_id,size_order";
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

					$production_quantity=$color[csf("rcv_qnty")];
					$amount = $amountArr[$color[csf("color_number_id")]];

					if($amount<1){$disable_for_posted="readonly";}
					$colorHTML .='<tr><td>'.$color_library[$color[csf("color_number_id")]].'</td><td><input type="text" name="txt_color" id="colSize_'.($i+1).'" style="width:60px"  class="text_boxes_numeric" placeholder="'.($production_quantity-$color[csf("iss_qnty")]+$amount-$color[csf("rcv_rtn")]+$color[csf("iss_rtn_qnty")]).'" value="'.$amount.'" onblur="fn_colorlevel_total('.($i+1).')" '.$disable_for_posted.'  ></td><td></td></tr>';
					$totalQnty += $amount;
					$totalRejQnty += $rejectAmt;
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
						$colorHTML .= '<div id="content_search_panel_'.$color[csf("color_number_id")].'" style="display:none" class="accord_close"><div style="padding-left: 40px;text-align:center"><input type="checkbox" onClick="active_placeholder_qty(' . $color[csf("color_number_id")] . ')" id="set_all_' . $color[csf("color_number_id")] . '">&nbsp;Available Qty Auto Fill</div><table id="table_'.$color[csf("color_number_id")].'">';
						$chkColor[] = $color[csf("color_number_id")];
						$totalFn .= "fn_total(".$color[csf("color_number_id")].");";
					}
 					$colorID .= $color[csf("size_number_id")]."*".$color[csf("color_number_id")].",";
					
					$rcv_qnty=$color_size_qnty_array[$color[csf('id')]]['rcv'];
					$rcv_rtn_qnty=$color_size_qnty_array[$color[csf('id')]]['rcv_rtn'];
					$iss_qnty=$color_size_qnty_array[$color[csf('id')]]['iss'];
					$iss_rtn_qnty=$color_size_qnty_array[$color[csf('id')]]['iss_rtn'];

					if($amount<1){$disable_for_posted="readonly";}else{$disable_for_posted="";}
					$colorHTML .='<tr><td>'.$size_library[$color[csf("size_number_id")]].'</td><td><input type="text" name="colorSize" id="colSize_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:80px" placeholder="'.($rcv_qnty-$rcv_rtn_qnty-$iss_qnty+$amount+$iss_rtn_qnty).'" onblur="fn_total('.$color[csf("color_number_id")].','.($i+1).')" value="'.$amount.'" '.$disable_for_posted.' ><input type="text" name="colorSizePOQnty" id="colorSizePOQnty_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:50px" value="'.$color[csf("order_quantity")].'" readonly disabled></td></tr>';				
					$colorWiseTotal += $amount;

				}
				$i++; 
			}
			//echo $colorHTML;die; 
			if( $variableSettings==2 ){ $colorHTML = '<table id="table_color" class="rpt_table"><thead><tr><th width="70">Color</th><th width="60">Quantity</th><th width="60">Reject</th></tr><tr> <th colspan="2"><div style="padding-left: 30px;text-align:left"><input type="checkbox" onClick="active_placeholder_qty_color(' . $color[csf("color_number_id")] . ')" id="set_all">&nbsp;<label for="set_all">Available Qty Auto Fill</label></div></th> </tr></thead><tbody>'.$colorHTML.'<tbody><tfoot><tr><th>Total</th><th><input type="text" id="total_color" placeholder="'.$totalQnty.'" value="'.$totalQnty.'" class="text_boxes_numeric" style="width:60px"  ></th><th><input type="text" id="total_color_rej" placeholder="'.$totalRejQnty.'" value="'.$totalRejQnty.'" class="text_boxes_numeric" style="width:60px" ></th></tr></tfoot></table>'; }
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
			$color_size_sql=sql_select ("SELECT  po_break_down_id,item_number_id,size_number_id,color_number_id,sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown where  is_deleted=0 and status_active in(1,2,3)    and  po_break_down_id in (".str_replace("'","",$po_id).") and item_number_id=$item_id group by po_break_down_id,item_number_id,size_number_id,color_number_id");
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
 	$control_and_preceding=sql_select("select is_control,preceding_page_id from variable_settings_production where status_active=1 and is_deleted=0 and variable_list=33 and page_category_id=260 and company_name=$cbo_company_id");  
    $is_control=$control_and_preceding[0][csf("is_control")];
    $preceding_process=$control_and_preceding[0][csf("preceding_page_id")];
	$qty_source=81;
	if($preceding_process==29) $qty_source=5;//Sewing Output
	else if($preceding_process==31) $qty_source=8;//Packing And Finishing
	else if($preceding_process==269) $qty_source=14;//Finishing Delivery
	else if($preceding_process==268) $qty_source=81;//Finishing Receive
	
	if ($operation!=0)
	{
		if(str_replace("'","",$txt_pack_type)=="") $packType_cond=""; else $packType_cond=" and pack_type=$txt_pack_type";
		if(str_replace("'","",$hidden_ship_date)=="") $ship_date_cond=""; else $ship_date_cond=" and a.country_ship_date=$hidden_ship_date";
		$backValisql = "SELECT  b.color_size_break_down_id,
					(sum(CASE WHEN b.production_type='82' then b.production_qnty ELSE 0 END)-sum(CASE WHEN b.production_type='82' and b.mst_id=$txt_mst_id then b.production_qnty ELSE 0 END)) as issueqty,
					sum(CASE WHEN b.production_type='83' then b.production_qnty ELSE 0 END) as issueretqty
					from wo_po_color_size_breakdown a left join pro_garments_production_dtls b on a.id=b.color_size_break_down_id left join PRO_GMTS_DELIVERY_MST c on c.id=b.delivery_mst_id
					where a.po_break_down_id=$hidden_po_break_down_id and a.item_number_id=$cbo_item_name and a.country_id=$cbo_country_name $ship_date_cond $pack_type_cond and a.is_deleted=0 and a.status_active in(1,2,3) and b.production_type in(82,83) group by b.color_size_break_down_id";
		//echo "10**".$backValisql; die;
		$backValiResult = sql_select($backValisql);
		$nxtQtyArr=array();
		foreach ($backValiResult as $row) {
			$nxtQtyArr[$row[csf("color_size_break_down_id")]]['issueqty']+=$row[csf("issueqty")];
			$nxtQtyArr[$row[csf("color_size_break_down_id")]]['issueretqty']+=$row[csf("issueretqty")];
		}
		unset($backValiResult);
		
		$ex_fac_countrysql = "select color_size_break_down_id, sum(b.production_qnty) as production_qnty from pro_ex_factory_mst a,pro_ex_factory_dtls b where a.id=b.mst_id and a.po_break_down_id=$hidden_po_break_down_id and a.item_number_id=$cbo_item_name and country_id=$cbo_country_name and a.status_active=1 and a.entry_form<>85 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $packType_cond group by color_size_break_down_id";
		//echo "10**".$ex_fac_countrysql; die;
		$exfacres = sql_select($ex_fac_countrysql);
		foreach ($exfacres as $exrow) {
			$nxtQtyArr[$exrow[csf("color_size_break_down_id")]]['exqty']+=$exrow[csf("production_qnty")];
		}
		unset($exfacres);
	}
	
	if ($operation==0) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
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
			
			$country_finishing_qty=return_field_value("sum(production_quantity)","pro_garments_production_mst","po_break_down_id=$hidden_po_break_down_id and production_type=82 and country_id=$cbo_country_name $packType_cond and status_active=1 and is_deleted=0");
			
			$country_rtn_qty=return_field_value("sum(production_quantity)","pro_garments_production_mst","po_break_down_id=$hidden_po_break_down_id and production_type=83 and country_id=$cbo_country_name $packType_cond and status_active=1 and is_deleted=0");
		
			if($country_iron_qty+$country_rtn_qty < $country_finishing_qty+$txt_finishing_qty)
			{
				echo "25**0";
				//check_table_status( 160,0);
				disconnect($con);
				die;
			}

			$country_carton_qty=return_field_value("sum(carton_qty)","pro_garments_production_mst","po_break_down_id=$hidden_po_break_down_id and production_type='$qty_source' and country_id=$cbo_country_name $packType_cond and status_active=1 and is_deleted=0");
			
			$country_carton_finishing_qty=return_field_value("sum(carton_qty)","pro_garments_production_mst","po_break_down_id=$hidden_po_break_down_id and production_type=82 and country_id=$cbo_country_name $packType_cond and status_active=1 and is_deleted=0");

			$country_carton_rtn_qty=return_field_value("sum(carton_qty)","pro_garments_production_mst","po_break_down_id=$hidden_po_break_down_id and production_type=83 and country_id=$cbo_country_name $packType_cond and status_active=1 and is_deleted=0");
		
			if($country_carton_qty+$country_carton_rtn_qty < $country_carton_finishing_qty+$txt_carton_qty)
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

			$new_sys_number = explode("*", return_next_id_by_sequence("", "pro_gmts_delivery_mst",$con,1,$cbo_company_id,'FGIE',503,date("Y",time()),0,0,82,0,0 ));
			$field_array_delivery = "id, sys_number_prefix, sys_number_prefix_num, sys_number, company_id, production_type, entry_form,location_id,store_id,floor_id,purpose_id,delivery_date,remarks, inserted_by, insert_date";
			$mst_id = return_next_id_by_sequence(  "pro_gmts_delivery_mst_seq", "pro_gmts_delivery_mst", $con );
            $data_array_delivery = "(" . $mst_id . ",'" . $new_sys_number[1] . "','" .(int) $new_sys_number[2] . "','" . $new_sys_number[0] . "', " .  $cbo_company_id . ",82,503,".$cbo_location.",".$cbo_store_name ."," . $cbo_floor ."," . $cbo_purpose ."," . $txt_issue_date ."," . $txt_remark . "," . $user_id . ",'" . $pc_date_time . "')";
            $challan_no =(int) $new_sys_number[2];
            $txt_system_no = $new_sys_number[0];
        } 
        else 
        {
            $mst_id = str_replace("'", "", $txt_system_id);
            $txt_chal_no = explode("-", str_replace("'", "", $txt_system_no));
            $challan_no = (int)$txt_chal_no[3];

            $field_array_delivery = "remarks*updated_by*update_date";
            $data_array_delivery = "" . $txt_remark . "*" . $user_id . "*'" . $pc_date_time . "'";
        }
		//$id=return_next_id("id", "pro_garments_production_mst", 1);

		$id= return_next_id_by_sequence(  "pro_gar_production_mst_seq",  "pro_garments_production_mst", $con );
		
  		$field_array1="id, garments_nature, company_id, challan_no, po_break_down_id, item_number_id, country_id, location,production_date, production_quantity, production_type, entry_break_down_type, carton_qty, remarks, store_id, floor_id, room_id, rack_id, shelf_id, total_produced, yet_to_produced,delivery_mst_id,issue_purpose, inserted_by, insert_date"; 
		if($db_type==0)
		{
			$data_array1="(".$id.",".$garments_nature.",".$cbo_company_id.",".$challan_no.",".$hidden_po_break_down_id.",".$cbo_item_name.",".$cbo_country_name.",".$cbo_location.",".$txt_issue_date.",".$txt_finishing_qty.",82,".$sewing_production_variable.",".$txt_carton_qty.",".$txt_remark.",".$cbo_floor.",".$txt_cumul_issue_qty.",".$txt_yet_to_issue.",".$mst_id."," . $cbo_purpose .",".$user_id.",'".$pc_date_time."')";
		}
	  	else if($db_type==2)
		{
			$data_array1="INSERT INTO pro_garments_production_mst (".$field_array1.") values(".$id.",".$garments_nature.",".$cbo_company_id.",".$challan_no.",".$hidden_po_break_down_id.",".$cbo_item_name.",".$cbo_country_name.",".$cbo_location.",".$txt_issue_date.",".$txt_finishing_qty.",82,".$sewing_production_variable.",".$txt_carton_qty.",".$txt_remark.",".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$txt_cumul_issue_qty.",".$txt_yet_to_issue.",".$mst_id."," . $cbo_purpose .",".$user_id.",'".$pc_date_time."')";
		}
 		//$rID=sql_insert("pro_garments_production_mst",$field_array1,$data_array1,1);
		//echo $data_array;die;
		
		// pro_garments_production_dtls table entry here ----------------------------------///
		$field_array="id, mst_id, production_type, color_size_break_down_id, production_qnty, delivery_mst_id";
		if(str_replace("'","",$txt_pack_type)=="") $pack_type_cond=""; else $pack_type_cond=" and b.pack_type=$txt_pack_type";
		$dtlsData = sql_select("SELECT a.color_size_break_down_id,
			sum(CASE WHEN a.production_type='$qty_source' then a.production_qnty ELSE 0 END) as production_qnty,
			sum(CASE WHEN a.production_type=82 then a.production_qnty ELSE 0 END) as cur_production_qnty 
			from pro_garments_production_dtls a,pro_garments_production_mst b 
			where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id=$hidden_po_break_down_id and b.item_number_id=$cbo_item_name and b.country_id=$cbo_country_name and a.color_size_break_down_id<>0 and a.production_type in($qty_source,82) $pack_type_cond
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
				if($j==0)$data_array = "(".$dtls_id.",".$id.",82,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."','".$mst_id."')";
				else $data_array .= ",(".$dtls_id.",".$id.",82,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."','".$mst_id."')";
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
				if($j==0)$data_array = "(".$dtls_id.",".$id.",82,'".$colSizeID_arr[$index]."','".$colorSizeValue."','".$mst_id."')";
				else $data_array .= ",(".$dtls_id.",".$id.",82,'".$colSizeID_arr[$index]."','".$colorSizeValue."','".$mst_id."')";
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
		$is_fullshipment=return_field_value("shiping_status","wo_po_break_down","id=$hidden_po_break_down_id");
		if($is_fullshipment==3)
		{
			echo "505";disconnect($con);die;
		}
		
		//--------------------------------------------------------------Compare end;txt_remark
		$field_array_delivery = "company_id*purpose_id*floor_id*remarks*location_id*updated_by*update_date";
        $data_array_delivery = "" . $cbo_company_id . "*" . $cbo_purpose . "*" . $cbo_floor  . "*" . $txt_remark ."*".$cbo_location."*" . $user_id . "*'" . $pc_date_time . "'";

		// pro_garments_production_mst table data entry here 
		
 		$field_array1="location*production_quantity*entry_break_down_type*carton_qty*store_id*floor_id*room_id*rack_id*shelf_id*total_produced*yet_to_produced*issue_purpose*updated_by*update_date";
		
		$data_array1="".$cbo_location."*".$txt_finishing_qty."*".$sewing_production_variable."*".$txt_carton_qty."*".$cbo_store_name."*".$cbo_floor."*".$cbo_room."*".$txt_rack."*".$txt_shelf."*".$txt_cumul_issue_qty."*".$txt_yet_to_issue."*".$cbo_purpose."*".$user_id."*'".$pc_date_time."'";
				
		if(str_replace("'","",$sewing_production_variable)!=1 && str_replace("'","",$txt_mst_id)!='' ) //  not gross level
		{
			$dtlsData = sql_select("SELECT a.color_size_break_down_id,
				sum(CASE WHEN a.production_type='$qty_source' then a.production_qnty ELSE 0 END) as production_qnty,
				sum(CASE WHEN a.production_type=82 then a.production_qnty ELSE 0 END) as cur_production_qnty 
				from pro_garments_production_dtls a,pro_garments_production_mst b 
				where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id=$hidden_po_break_down_id and b.item_number_id=$cbo_item_name and b.country_id=$cbo_country_name and a.color_size_break_down_id<>0 and a.production_type in($qty_source,82) and b.id !=$txt_mst_id 
				group by a.color_size_break_down_id");
			$color_pord_data=array();							
			foreach($dtlsData as $row)
			{				  
				$color_pord_data[$row[csf("color_size_break_down_id")]]=$row[csf('production_qnty')]-$row[csf("cur_production_qnty")];
			}
			
			
 			$field_array="id, mst_id, production_type, color_size_break_down_id, production_qnty, delivery_mst_id";
			
			if(str_replace("'","",$sewing_production_variable)==2)//color level wise
			{		
				$color_sizeID_arr=sql_select( "SELECT id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name  and status_active in(1,2,3) and is_deleted=0  order by id" );
				$colSizeID_arr=array(); 
				foreach($color_sizeID_arr as $val){
					$index = $val[csf("color_number_id")];
					$colSizeID_arr[$index]=$val[csf("id")];
				}	
				
				// $colorIDvalue concate as colorID*Value**colorID*Value -------------------------//
				$rowExRej = explode("**",$colorIDvalueRej);
				foreach($rowExRej as $rowR=>$valR)
				{
					$colorSizeRejIDArr = explode("*",$valR);
					//echo $colorSizeRejIDArr[0]; die;
					$rejQtyArr[$colorSizeRejIDArr[0]]=$colorSizeRejIDArr[1];
				}
				
				$rowEx = explode("**",$colorIDvalue); 
				//$dtls_id=return_next_id("id", "pro_garments_production_dtls", 1);
				$data_array="";$j=0;
				foreach($rowEx as $rowE=>$val)
				{
					$colorSizeNumberIDArr = explode("*",$val);
					
					$nxt_process_qty=($nxtQtyArr[$colSizeID_arr[$colorSizeNumberIDArr[0]]]['issueretqty']*1)+$nxtQtyArr[$colSizeID_arr[$colorSizeNumberIDArr[0]]]['exqty'];
					$current_priv_qty=($nxtQtyArr[$colSizeID_arr[$colorSizeNumberIDArr[0]]]['issueqty']*1)+($colorSizeNumberIDArr[1]*1);
					
					if( $nxt_process_qty > $current_priv_qty)
					{
						echo "36**Finish Gmts Issue is not less then Gmts Delivery and Issue Return.";
						//check_table_status( 160,0);
						disconnect($con);
						die;
					}
					
					$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",  "pro_garments_production_dtls", $con );
					if($j==0)$data_array = "(".$dtls_id.",".$txt_mst_id.",82,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."','".$txt_system_id."')";
					else $data_array .= ",(".$dtls_id.",".$txt_mst_id.",82,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."','".$txt_system_id."')";
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
				//echo "10**";
				foreach($rowEx as $rowE=>$valE)
				{
					$colorAndSizeAndValue_arr = explode("*",$valE);
					$sizeID = $colorAndSizeAndValue_arr[0];
					$colorID = $colorAndSizeAndValue_arr[1];				
					$colorSizeValue = $colorAndSizeAndValue_arr[2];
					$index = $sizeID.$color_arr[$colorID].$colorID;
					
					$nxt_process_qty=($nxtQtyArr[$colSizeID_arr[$index]]['issueretqty']*1)+$nxtQtyArr[$colSizeID_arr[$index]]['exqty'];
					$current_priv_qty=($nxtQtyArr[$colSizeID_arr[$index]]['issueqty']*1)+($colorSizeValue*1);
					//if ($colSizeID_arr[$index]==624500) { echo "10**".$nxtQtyArr[$colSizeID_arr[$index]]['issueretqty'].'-'.$nxtQtyArr[$colSizeID_arr[$index]]['exqty'].'-'.$nxtQtyArr[$colSizeID_arr[$index]]['issueqty'].'-'.$colorSizeValue; die; }
					if( $nxt_process_qty > $current_priv_qty)
					{
						echo "36**Finish Gmts Issue is not less then Gmts Delivery and Issue Return.";
						//check_table_status( 160,0);
						disconnect($con);
						die;
					}

					$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",   "pro_garments_production_dtls", $con );
					if($j==0)$data_array = "(".$dtls_id.",".$txt_mst_id.",82,'".$colSizeID_arr[$index]."','".$colorSizeValue."','".$txt_system_id."')";
					else $data_array .= ",(".$dtls_id.",".$txt_mst_id.",82,'".$colSizeID_arr[$index]."','".$colorSizeValue."','".$txt_system_id."')";
					//$dtls_id=$dtls_id+1;
					$j++;
				}
				//die;
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

if($action=="print_report")
{
	extract($_REQUEST);
	$data=explode('**',$data);
	$mst_id=$data[0];
	$type=$data[1];

	// print_r ($data);
	$issue_purpose_arr=array(1=>"Delivery",2=>"Buyer Inspection",3=>"Sales");

	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$address=sql_select("select id, company_name,plot_no,level_no,road_no,block_no,city,ZIP_CODE,province,email,website from lib_company");

	foreach ($address as $row) 
	{
		$company_address[$row[csf('id')]]['plot_no']=$row[csf('plot_no')];
		$company_address[$row[csf('id')]]['level_no']=$row[csf('level_no')];
		$company_address[$row[csf('id')]]['road_no']=$row[csf('road_no')];
		$company_address[$row[csf('id')]]['block_no']=$row[csf('block_no')];
		$company_address[$row[csf('id')]]['city']=$row[csf('city')];
		$company_address[$row[csf('id')]]['zip_code']=$row[csf('zip_code')];
		$company_address[$row[csf('id')]]['province']=$row[csf('province')];
		$company_address[$row[csf('id')]]['email']=$row[csf('email')];
		$company_address[$row[csf('id')]]['website']=$row[csf('website')];
		
	}

	$floor_library = return_library_array("select b.floor_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.floor_id  and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0  group by b.floor_id,a.floor_room_rack_name  order by a.floor_room_rack_name", "floor_id", "floor_room_rack_name");

	$country_library=return_library_array( "select id, country_name from lib_country", "id", "country_name"  );
	$country_source=return_library_array( "select id, SUPPLIER_NAME from lib_supplier", "id", "SUPPLIER_NAME"  );
	$buyer_library=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$size_arr = return_library_array("select id, SIZE_NAME from lib_size", 'id', 'SIZE_NAME');
	$user_lib_name = return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");
	
	//Print 1 Button
	if($type==1)
	{
		$sql="SELECT a.SYS_NUMBER_PREFIX_NUM, a.COMPANY_ID, a.PURPOSE_ID, a.DELIVERY_DATE, b.REMARKS,a.INSERTED_BY, b.ID, b.ITEM_NUMBER_ID, b.COUNTRY_ID, b.PRODUCTION_QUANTITY, b.CARTON_QTY, c.PO_NUMBER, d.STYLE_REF_NO, d.BUYER_NAME 
		from pro_gmts_delivery_mst a,pro_garments_production_mst b, wo_po_break_down c, wo_po_details_master d where a.id=$mst_id and a.id=b.delivery_mst_id and b.production_type='82' and b.po_break_down_id=c.id and c.job_id=d.id and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 order by b.id";
		// echo $sql;
		$sqlResult =sql_select($sql);
		$inserted_by=$sqlResult[0]["INSERTED_BY"];
		$company_id=$sqlResult[0]["COMPANY_ID"];
		?>
		<style>
			.right{text-align: right;}
		</style>

		<div style="width:930px;">
			<table width="900" cellspacing="0" align="right">
				<tr>
					<td colspan="4" align="center" style="font-size:xx-large"><strong>Finish Garment Issue Challan</strong></td>
				</tr>
				<tr>
					<td colspan="4" height="30"></td>
				</tr>
				<tr>
					<td width="100"> <strong>Company: </strong> </td>
					<td width="350"><?=$company_library[$sqlResult[0]['COMPANY_ID']];?></td>
					<td width="150"> <strong>Issue Purpose: </strong> </td>
					<td><?=$issue_purpose_arr[$sqlResult[0]['PURPOSE_ID']];?></td>
				</tr>
				<tr>
					<td > <strong>Challan No: </strong> </td>
					<td ><?=$sqlResult[0]['SYS_NUMBER_PREFIX_NUM'];?></td>
					<td > <strong>Date: </strong> </td>
					<td><?=change_date_format($sqlResult[0]['DELIVERY_DATE']);?></td>
				</tr>
				<tr>
					<td colspan="4" height="30"></td>
				</tr>
			</table>
			<table cellspacing="0" width="900"  border="1" rules="all" class="rpt_table" >
			    <thead>
					<tr>
						<th width="30">SL</th>
						<th width="120">Buyer</th>
						<th width="120">Style Ref.</th>
						<th width="120">Order No</th>
						<th width="120">Country</th>
						<th width="120">Item Name</th>
						<th width="80">Delivery Qnty</th>
						<th width="80">NO Of Carton</th>
						<th>Remarks</th>
					</tr>
				</thead>
				<tbody>
					<?
					$i=1;
					foreach($sqlResult as $row)
					{
						?>
							<tr>
								<td><?=$i;?></td>
								<td><?echo $buyer_library[$row['BUYER_NAME']];?></td>
								<td><?echo $row['STYLE_REF_NO'];?></td>
								<td><?echo $row['PO_NUMBER'];?></td>
								<td><?echo $country_library[$row['COUNTRY_ID']];?></td>
								<td><?echo $garments_item[$row['ITEM_NUMBER_ID']];?></td>
								<td class="right"><?echo $row['PRODUCTION_QUANTITY'];?></td>
								<td class="right"><?echo $row['CARTON_QTY'];?></td>
								<td><?echo $row['REMARKS'];?></td>
							</tr>
						<?
						$tot_production_qnty+=$row['PRODUCTION_QUANTITY'];
						$tot_carton_qty+=$row['CARTON_QTY'];
						$i++;
					}
					?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="6" class="right"><b>Grand Total: </b></td>
						<td class="right"><?echo $tot_production_qnty;?></td>
						<td class="right"><?echo $tot_carton_qty;?></td>
						<td></td>
					</tr>
				</tfoot>
			</table><br>
			<b><? echo 'In Words: '.number_to_words($tot_production_qnty).' Pcs';?></b>
					
		</div>
		<?
		echo signature_table(260, $company_id, "1100px", "", 70, $user_lib_name[$inserted_by]);
		exit();	
	}
	// Print 2 Button
	else if($type==2)
	{
		$sql = "SELECT a.SYS_NUMBER_PREFIX_NUM,d.JOB_NO,a.COMPANY_ID, a.PURPOSE_ID, a.DELIVERY_DATE, a.FLOOR_ID, a.PRODUCTION_SOURCE ,a.REMARKS,a.INSERTED_BY, b.ID, b.ITEM_NUMBER_ID, b.COUNTRY_ID, b.PRODUCTION_QUANTITY, b.CARTON_QTY,b.SERVING_COMPANY, c.PO_NUMBER,c.PO_QUANTITY, d.STYLE_REF_NO, d.BUYER_NAME,e.COLOR_SIZE_BREAK_DOWN_ID,f.id,e.MST_ID,f.COLOR_NUMBER_ID,f.SIZE_NUMBER_ID 
		from pro_gmts_delivery_mst a, 
		pro_garments_production_mst b,
		wo_po_break_down c, 
		wo_po_details_master d, 
		pro_garments_production_dtls e,
		wo_po_color_size_breakdown f 
		where a.id=$mst_id and a.id=b.delivery_mst_id and b.production_type='82' and b.po_break_down_id=c.id and c.job_id=d.id and e.COLOR_SIZE_BREAK_DOWN_ID=f.id and e.MST_ID=b.id and f.job_id=d.id and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 order by f.size_order";
		//echo $sql;
		$sqlResult = sql_select($sql);
		foreach ($sqlResult as $value) 
		{
			$company_id=$value["COMPANY_ID"];
			$inserted_by=$value["INSERTED_BY"];
			$size_order_arr[$value["SIZE_NUMBER_ID"]]=$value["SIZE_NUMBER_ID"];
			$item_name_id.=$garments_item[$value["ITEM_NUMBER_ID"]].",";
		}
		$item_name_id=implode(", ",array_unique(explode(",",chop($item_name_id,','))));
		

		
		?>
		<style>
			.right{text-align: right;}
		</style>
		<div style="width:930px;">
			<table width="900" cellspacing="0" align="right">
				<tr>
					<td colspan="8" align="center" style="font-size:xx-large"><strong><?=$company_library[$sqlResult[0]['COMPANY_ID']];?></strong>
				</td>
				</tr>
				<tr>
					<td colspan="8" align="center"><p>
						<? echo "Polot No: ".$company_address[$sqlResult[0]['COMPANY_ID']]['plot_no'].","." Lavel NO: ".$company_address[$sqlResult[0]['COMPANY_ID']]['level_no'].","." Road No: ".$company_address[$sqlResult[0]['COMPANY_ID']]['road_no'].","." BlockNo: ".$company_address[$sqlResult[0]['COMPANY_ID']]['block_no'].","." City: ".$company_address[$sqlResult[0]['COMPANY_ID']]['city'].","." Zip Code: ".$company_address[$sqlResult[0]['COMPANY_ID']]['zip_code'].","." Province: ".$company_address[$sqlResult[0]['COMPANY_ID']]['province'];?>
				</p>
				</td>
				</tr>
					<tr>
					<td colspan="8" align="center"><p><? echo "Email: ".$company_address[$sqlResult[0]['COMPANY_ID']]['email']." Web Site: ".$company_address[$sqlResult[0]['COMPANY_ID']]['website'];?></p>
				</td>
				</tr>
				<tr>
					<td colspan="8" height="30" align="center" style="font-size:x-large"><strong>Finish Garment Issue Callan</strong></td>
				</tr>
				<tr>
					<td align="left" width="10"><strong>Challan No</strong> </td>
					<td width="5">:</td>
					<td width="70"><?=$sqlResult[0]['SYS_NUMBER_PREFIX_NUM'];?></td>

					<td align="left" width="10"><strong>Order No</strong></td>
					<td width="5">:</td>
					<td width="70"><?=$sqlResult[0]['PO_NUMBER'];?></td>

					<td align="left" width="10"><strong>Buyer</strong></td>
					<td width="5">:</td>
					<td width="70"><?=$buyer_library[$sqlResult[0]['BUYER_NAME']];?></td>
				</tr>
				<tr>
					<td align="left" width="10"><strong> Issue Purpose</strong> </td>
					<td width="5">:</td>
					<td width="70"><?=$issue_purpose_arr[$sqlResult[0]['PURPOSE_ID']];?></td>

					<td align="left" width="10"><strong>Job No</strong></td>
					<td width="5">:</td>
					<td width="70"><?=$sqlResult[0]['JOB_NO'];?></td>

					<td align="left" width="10"><strong>Style Ref</strong></td>
					<td width="5">:</td>
					<td width="70"><?=$sqlResult[0]['STYLE_REF_NO'];?></td>
				</tr>
				<tr>
					<td valign="top" align="left" width="10"><strong>Date</strong> </td>

					<td valign="top" width="5">:</td>
					<td valign="top" width="70"><?=change_date_format($sqlResult[0]['DELIVERY_DATE']);?></td>
					
					<td valign="top" align="left" width="10"><strong>Item</strong></td>
					<td valign="top" width="5">:</td>
					<td valign="top" width="70"><?=$item_name_id;?></td>
					
					<td valign="top" align="left" width="10"><strong>Order Qnty</strong></td>
					<td valign="top" width="5">:</td>
					<td valign="top" width="70"><?=$sqlResult[0]['PO_QUANTITY'];?></td>
				</tr>
					<tr>
					<td align="left" width="10"><strong>Floor</strong> </td>
					<td width="5">:</td>
					<td width="70"><?=$floor_library[$sqlResult[0]['FLOOR_ID']];?></td>

					<td align="left" width="10"><strong>Remark</strong> </td>
					<td width="5">:</td>
					<td width="70" colspan="3"><?=$sqlResult[0]['REMARKS'];?></td>

					<!-- <td align="center" width="80"><strong>Source:</strong></td>
					<td align="center" width="300"><?//=$country_source[$sqlResult[0]['PRODUCTION_SOURCE']];?></td> -->
					<!-- <td align="center" width="80"><strong>Date</strong></td>
					<td align="center" width="350"><?//=change_date_format($sqlResult[0]['DELIVERY_DATE']);?></td> -->
					</tr>
				<tr>
					<td colspan="8" height="30"></td>
				</tr>
			</table>
			<table cellspacing="0" width="900"  border="1" rules="all" class="rpt_table" >
			    <thead>
					<tr>
						<th width="30">SL</th>
						<th width="120">Country</th>
						<th width="120">Color/Size</th>
						<?php
							foreach ($size_order_arr as $value) {
						?>
							<th width="120"><?=$size_arr[$value];?></th>
						<?
						}
						?>
						<th width="80">Total Issue Qty.</th>
						<th width="80">Carton Qty.</th>
					</tr>
				</thead>
				<?
				$sql = "SELECT a.production_qnty as production_qnty,c.carton_qty, b.color_number_id, b.size_number_id,b.size_order,b.country_id,c.id 
				from pro_garments_production_dtls a, wo_po_color_size_breakdown b,pro_garments_production_mst c 
				where c.id=a.mst_id and a.delivery_mst_id=$mst_id  and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and c.status_active=1 order by b.size_order ";
				//echo $sql;
				$result = sql_select($sql);
				$carton_array=array();
				$size_array = array();
				$data_array = array();
				foreach ($result as $row) 
				{
					$size_array[$row[csf('size_number_id')]] = $row[csf('size_number_id')];
					$data_array[$row[csf('country_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['qty'] += $row[csf('production_qnty')];
					$data_array[$row[csf('country_id')]][$row[csf('color_number_id')]]['carton_qty'] = $row[csf('carton_qty')];
					$carton_array[$row[csf('country_id')]][$row[csf('id')]]=$row[csf('carton_qty')];

				}
				?>
				<tbody>
					<?
					$i=1;
					$size_total_array=array();
					foreach($data_array as $country_id => $country_data)
					{
					$z=1;
						foreach ($country_data as $color_id => $color_data) 
						{
							?>
								<tr>
									<td><?=$i;?></td>
									<td><?echo $country_library[$country_id];?></td>
									<td><?echo $color_arr[$color_id];?></td>
									<?
									$size_total=0;
										foreach ($size_order_arr as $value) 
										{
											?>
											<td><?echo $color_data[$value]['qty'];?></td>
											<?
											$size_total_array[$value]+=$color_data[$value]['qty'];
											$size_total+=$color_data[$value]['qty'];
										}
									?>
									<td class="left"><?echo 	$size_total;?></td>
									<?
									if ($z==1) {
										?>
										<td class="right" rowspan="<?=count($country_data);?>"><?echo array_sum($carton_array[$country_id]);?></td>
										<?
										$z++;
										$total_carton+=array_sum($carton_array[$country_id]);

									}
									?>
								</tr>
							<?
							$i++;
							$tot_production_qnty+= $size_total;
						}
					}
					?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="3" class="right"><b>Grand Total: </b></td>
							<?
									foreach ($size_order_arr as $value) 
									{
										?>
										<td><?echo $size_total_array[$value];?></td>
										<?
									}
								?>
						<td class="right"><?echo $tot_production_qnty;?></td>
						<td class="right"><?echo $total_carton;?></td>
					</tr>
				</tfoot>
			</table><br>
			<b><? echo 'In Words: '.number_to_words($tot_production_qnty).' Pcs';	?></b>
		</div>
		<?
		echo signature_table(260, $company_id, "1100px", "", 70, $user_lib_name[$inserted_by]);
	}
	exit();	
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
	                    <table ellspacing="0" cellpadding="0" width="740" border="1" rules="all" class="rpt_table" align="center">
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
	                     			<input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>+'_'+<? echo $cbo_location; ?>+'_'+<? echo $cbo_store_name; ?>+'_'+<? echo $cbo_floor; ?>, 'create_po_search_list_view', 'search_div', 'finish_gmts_issue_entry_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:100px;" />
	                            </td>
	        				</tr>
	             		</table>
	          		</td>
	        	</tr>
	        	<tr>
	            	<td  align="center" height="40" valign="middle">
						<? echo load_month_buttons(1);  ?>
	                    <input type="hidden" id="hidden_all_info">
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
	// $cbo_room = $ex_data[8];
	// $txt_rack = $ex_data[9];
	// $txt_shelf = $ex_data[10];

	$qty_source=81;
	
	$variable_qty_source_packing=return_field_value("qty_source_packing","variable_settings_production","company_name=$company and variable_list=43","qty_source_packing");
	
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
		$floor_cond = str_replace("'","",$cbo_floor) ? " and floor_id=$cbo_floor " : "";
		$qty_source_cond="and b.id in(SELECT po_break_down_id from pro_garments_production_mst where production_type='$qty_source' and location=$cbo_location and store_id=$cbo_store_name $floor_cond and status_active=1 and is_deleted=0)";
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
	$country_sql="SELECT a.po_break_down_id, a.item_number_id, a.country_id, sum(a.order_quantity) as PO_QTY, sum(a.plan_cut_qnty) as PLAN_CUT_QTY from wo_po_color_size_breakdown a, pro_garments_production_mst b where a.po_break_down_id=b.po_break_down_id and b.production_type=81 and a.country_id=b.country_id $po_cond and a.status_active=1 and  a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.po_break_down_id, a.item_number_id, a.country_id"; 
	// echo $country_sql;die;
	$country_data=sql_select($country_sql);
	foreach($country_data as $row)
	{
		$po_country_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]['po_qty']=$row['PO_QTY'];
		$po_country_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]['plan_cut_qty']=$row['PLAN_CUT_QTY'];
	}
	unset($country_data);
		
	$total_entry_qty_data_arr=array();
	$total_entry_qty=sql_select( "SELECT a.po_break_down_id, a.item_number_id, a.country_id,
	sum( case when a.production_type=81 then a.production_quantity ELSE 0 END ) as RCV_QNTY, 
	sum( case when a.production_type=84 then a.production_quantity ELSE 0 END ) as RCV_RTN_QNTY, 
	sum( case when a.production_type=82 then a.production_quantity ELSE 0 END ) as ISSUE_QNTY, 
	sum( case when a.production_type=83 then a.production_quantity ELSE 0 END ) as ISSUE_QNTY_RTN from pro_garments_production_mst a where a.status_active=1 and a.is_deleted=0 and a.production_type in ('81','82','83','84') $po_cond group by a.po_break_down_id, a.item_number_id, a.country_id");

	foreach($total_entry_qty as $row)
	{
		$total_entry_qty_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]['rcv_qnty']=$row['RCV_QNTY'];
		$total_entry_qty_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]['rcv_rtn_qnty']=$row['RCV_RTN_QNTY'];
		$total_entry_qty_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]['issue_qnty']=$row['ISSUE_QNTY'];
		$total_entry_qty_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]['issue_qnty_rtn']=$row['ISSUE_QNTY_RTN'];
	}
	?>
	<div style="width:1290px;">
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
                <th width="60">Order Qty</th>
                <th width="60">Receive Qty</th>
                <th width="60">Receive Return Qty</th>
                <th width="60">Issue Qty</th>
                <th width="60">Issue Return Qty</th>
                <th>Balance</th>
            </thead>
     	</table>
     </div>
     <div style="width:1290px; max-height:240px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1272" class="rpt_table" id="tbl_po_list">
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
						$rcv_rtn_qnty=$total_entry_qty_data_arr[$row[csf('id')]][$grmts_item][$country_id]['rcv_rtn_qnty'];
						$issue_qnty_rtn=$total_entry_qty_data_arr[$row[csf('id')]][$grmts_item][$country_id]['issue_qnty_rtn'];
						$issue_qnty=$total_entry_qty_data_arr[$row[csf('id')]][$grmts_item][$country_id]['issue_qnty']-$issue_qnty_rtn;
						$plan_cut_qnty=$val['plan_cut_qty'];
						$balance=$rcv_qnty-$rcv_rtn_qnty-$issue_qnty+$issue_qnty_rtn;
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
								<td width="60" align="right"><?php echo $po_qnty; //$po_qnty*$set_qty;?>&nbsp;</td>
								<td width="60" align="right"><?=$rcv_qnty;?> &nbsp;</td>
								<td width="60" align="right"><?=$rcv_rtn_qnty;?> &nbsp;</td>
								<td width="60" align="right"><?php echo $issue_qnty; ?>&nbsp;</td>
								<td width="60" align="right"><?php echo $issue_qnty_rtn; ?>&nbsp;</td>
								<td align="right"><?php echo $balance;?>&nbsp;</td> 	
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

 	$color_library=return_library_array( "SELECT id, color_name from lib_color",'id','color_name');
	$size_library=return_library_array( "SELECT id, size_name from lib_size",'id','size_name');
	$qty_source=81;
	// ==================== set mst form data ===============================
	$sql = sql_select("SELECT a.buyer_name,a.style_ref_no,a.job_no,b.po_quantity,b.po_number from wo_po_details_master a,wo_po_break_down b where a.id=b.job_id and b.id=$po_id and b.status_active=1 and b.is_deleted=0");
	$country_qty_sql = return_library_array("SELECT po_break_down_id, sum(order_quantity) as country_quantity from wo_po_color_size_breakdown where po_break_down_id=$po_id and country_id=$country_id and item_number_id=$item_id and status_active=1 and is_deleted=0 group by po_break_down_id",'po_break_down_id','country_quantity');
	$po_qty_sql = sql_select("SELECT b.po_number as PO_NUMBER, b.po_quantity as ORDER_QUANTITY from wo_po_break_down b where b.id=$po_id and b.status_active=1 and b.is_deleted=0");
	echo "$('#cbo_country_name').val('".$country_id."');\n";
	echo "$('#cbo_item_name').val('".$item_id."');\n";

	echo "$('#txt_job_no').val('".$sql[0]['JOB_NO']."');\n";
	echo "$('#txt_style_no').val('".$sql[0]['STYLE_REF_NO']."');\n";
	echo "$('#cbo_buyer_name').val('".$sql[0]['BUYER_NAME']."');\n";
	echo "$('#txt_country_qty').val('".$country_qty_sql[$po_id]."');\n";
	echo "$('#txt_order_qty').val('".$po_qty_sql[0]['ORDER_QUANTITY']."');\n";

	$dataSql="SELECT COMPANY_ID,
	SUM(CASE WHEN production_type='$qty_source' THEN production_quantity END) as totalRcv,
	SUM(CASE WHEN production_type=84 THEN production_quantity ELSE 0 END) as totalRcvRtn,
	SUM(CASE WHEN production_type=82 THEN production_quantity ELSE 0 END) as totalIssue, 
	SUM(CASE WHEN production_type=83 THEN production_quantity ELSE 0 END) as totalIssueRtn, 
	SUM(CASE WHEN production_type='$qty_source' THEN carton_qty END) as cartonRcv,
	SUM(CASE WHEN production_type=84 THEN carton_qty ELSE 0 END) as cartonRcvRtn,
	SUM(CASE WHEN production_type=82 THEN carton_qty ELSE 0 END) as cartonIssue, 
	SUM(CASE WHEN production_type=83 THEN carton_qty ELSE 0 END) as cartonIssueRtn 
	from pro_garments_production_mst WHERE po_break_down_id=$po_id  and item_number_id=$item_id and country_id=$country_id and location=$cbo_location and store_id=$cbo_store_name and floor_id=$cbo_floor and room_id=$cbo_room and rack_id=$txt_rack and shelf_id=$txt_shelf and status_active=1 and is_deleted=0 group by company_id";

	// echo $dataSql;
	$sqlResult=sql_select($dataSql);
	$cbo_company_id=$sqlResult[0]['COMPANY_ID'];
	if($cbo_floor>0)
	{
		echo "load_room_rack_self_bin('requires/finish_gmts_issue_entry_controller', 'room','room_td', '".$cbo_company_id."','"."','".$cbo_store_name."','".$cbo_floor."',this.value);\n";
	}
	echo "document.getElementById('cbo_room').value 					= '".$cbo_room."';\n";
	if($cbo_room>0)
	{
		echo "load_room_rack_self_bin('requires/finish_gmts_issue_entry_controller', 'rack','rack_td', '".$cbo_company_id."','"."','".$cbo_store_name."','".$cbo_floor."','".$cbo_room."',this.value);\n";
	}
	echo "document.getElementById('txt_rack').value 					= '".$txt_rack."';\n";
	if($txt_rack>0)
	{
		echo "load_room_rack_self_bin('requires/finish_gmts_issue_entry_controller', 'shelf','shelf_td', '".$cbo_company_id."','"."','".$cbo_store_name."','".$cbo_floor."','".$cbo_room."','".$txt_rack."',this.value);\n";
	}
	echo "document.getElementById('txt_shelf').value 					= '".$txt_shelf."';\n";
	echo "$('#txt_order_no').val('".$po_qty_sql[0]['PO_NUMBER']."');\n";
	echo "$('#hidden_po_break_down_id').val('".$po_id."');\n";
	echo "set_button_status(0, permission, 'fnc_gmt_issue_entry',1,0);\n";
	foreach($sqlResult as $row)
	{  
		// $issue_qnty_balance=$row[csf('totalIssue')]-$row[csf('totalIssueRtn')];
		$issue_carton_balance=$row[csf('cartonIssue')]-$row[csf('cartonIssueRtn')];
		echo "$('#txt_rcv_input_qty').val('".$row[csf('totalRcv')]."');\n";
		echo "$('#txt_rcv_rtn_input_qty').val('".$row[csf('totalRcvRtn')]."');\n";
		echo "$('#txt_cumul_issue_qty').val('".$row[csf('totalIssue')]."');\n";			
		echo "$('#txt_rcv_input_carton').val('".$row[csf('cartonRcv')]."');\n";
		echo "$('#txt_rcv_rtn_input_carton').val('".$row[csf('cartonRcvRtn')]."');\n";
		echo "$('#txt_cumul_issue_carton').val('".$issue_carton_balance."');\n";	
		echo "$('#txt_cumul_issue_rtn_qty').val('".$row[csf('totalIssueRtn')]."');\n";				
		$actual_issue = $row[csf('totalIssue')]+$row[csf('totalIssueRtn')];
		$yet_to_produced = $row[csf('totalRcv')]-$row[csf('totalRcvRtn')]-$row[csf('totalIssue')]+$row[csf('totalIssueRtn')];
		$yet_to_carton_produced = $row[csf('cartonRcv')]-$row[csf('cartonRcvRtn')]-$row[csf('cartonIssue')]+$row[csf('cartonIssueRtn')];
		echo "$('#txt_finishing_qty').val('".$yet_to_produced."');\n";
		echo "$('#hdn_finishing_qty').val('".$yet_to_produced."');\n";
		echo "$('#txt_carton_qty').val('".$yet_to_carton_produced."');\n";

		echo "$('#txt_yet_to_issue').val('".$yet_to_produced."');\n";
		echo "$('#txt_yet_to_carton_issue').val('".$yet_to_carton_produced."');\n";
		echo "$('#txt_actual_issue').val('".$actual_issue."');\n";
	}
 		
	$cumulQty_arr=array();
	if($qty_source!=0)
   	{		
		$dataArray=sql_select("SELECT item_number_id, country_id, SUM(CASE WHEN production_type='$qty_source' THEN production_quantity else 0 END) as totalinput,SUM(CASE WHEN production_type=82 THEN production_quantity ELSE 0 END) as totalsewing from pro_garments_production_mst WHERE po_break_down_id=$po_id and item_number_id='$item_id' and country_id='$country_id' and location=$cbo_location and store_id=$cbo_store_name and floor_id=$cbo_floor and room_id=$cbo_room and rack_id=$txt_rack and shelf_id=$txt_shelf and status_active=1 and is_deleted=0 group by item_number_id, country_id");
		foreach($dataArray as $row)
		{ 
			$cumulQty_arr[$row[csf('item_number_id')]][$row[csf('country_id')]]['input']=$row[csf('totalinput')];
			$cumulQty_arr[$row[csf('item_number_id')]][$row[csf('country_id')]]['totalsewing']=$row[csf('totalsewing')];
		}
		unset($dataArray);
	}
	
	/*$plan_cut_qnty=return_field_value("sum(plan_cut_qnty)","wo_po_color_size_breakdown","po_break_down_id=$po_id and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0");
	
	$total_produced = return_field_value("sum(production_quantity)","pro_garments_production_mst","po_break_down_id=$po_id and item_number_id='$item_id' and country_id='$country_id' and location=$cbo_location and store_id=$cbo_store_name and floor_id=$cbo_floor and room_id=$cbo_room and rack_id=$txt_rack and shelf_id=$txt_shelf and production_type=82 and is_deleted=0");
	
	$res = sql_select("SELECT a.id, a.po_quantity, a.plan_cut, a.po_number, a.po_quantity, b.company_name, b.buyer_name, b.style_ref_no,b.gmts_item_id, b.order_uom, b.job_no, b.location_name from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and a.id=$po_id"); 
 
  	foreach($res as $result)
	{
		echo "$('#cbo_buyer_name').val('".$result[csf('buyer_name')]."');\n";
		echo "$('#txt_job_no').val('".$result[csf('job_no')]."');\n";
		echo "$('#txt_style_no').val('".$result[csf('style_ref_no')]."');\n";
  		if($qty_source!=0)
   		{
			$totalinput=$cumulQty_arr[$item_id][$country_id]['input'] ;
			$totalsewing=$cumulQty_arr[$item_id][$country_id]['totalsewing'];
			
			echo "$('#txt_rcv_input_qty').val('".$totalinput."');\n";
			echo "$('#txt_cumul_issue_qty').val('".$totalsewing."');\n";
			$yet_to_produced = $totalinput-$totalsewing;
			echo "$('#txt_yet_to_issue').val('".$yet_to_produced."');\n";
	    }
  	}*/
	
	
	if( $variableSettings==2 ) // color level
	{
		$color_size_qty_arr=array();
		$color_size_sql=sql_select ("SELECT po_break_down_id,item_number_id,size_number_id,color_number_id,sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown where status_active in(1,2,3) and is_deleted=0 and po_break_down_id in (".str_replace("'","",$po_id).") and item_number_id=$item_id group by po_break_down_id,item_number_id,size_number_id,color_number_id");
		foreach($color_size_sql as $s_id)
		{
			$color_size_qty_arr[$s_id[csf('color_number_id')]][$s_id[csf('size_number_id')]]+=$s_id[csf('plan_cut_qnty')]; 
		}

		$sql = "SELECT a.item_number_id, a.color_number_id, sum(a.order_quantity) as order_quantity, 
		sum(a.plan_cut_qnty) as plan_cut_qnty, 
		sum(CASE WHEN c.production_type='$qty_source' then b.production_qnty ELSE 0 END) as production_qnty,
		sum(CASE WHEN c.production_type=82 then b.production_qnty ELSE 0 END) as cur_production_qnty,
		sum(CASE WHEN c.production_type=83 then b.production_qnty ELSE 0 END) as rtn_qnty 
		sum(CASE WHEN c.production_type=84 then b.production_qnty ELSE 0 END) as rtn_rtn_qnty 
		from wo_po_color_size_breakdown a 
		left join pro_garments_production_dtls b on a.id=b.color_size_break_down_id 
		left join pro_garments_production_mst c on c.id=b.mst_id 
		where a.po_break_down_id='$po_id' and c.production_type in (81,82,83,84) and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active in(1,2,3)  group by a.item_number_id, a.color_number_id";	

	}
	else if( $variableSettings==3 ) //color and size level
	{
		
		$color_size_qty_arr=array();
		$color_size_sql=sql_select ("SELECT  po_break_down_id,item_number_id,size_number_id,color_number_id,sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown where status_active in(1,2,3) and  is_deleted=0 and po_break_down_id in (".str_replace("'","",$po_id).") and item_number_id=$item_id group by po_break_down_id,item_number_id,size_number_id,color_number_id");
		foreach($color_size_sql as $s_id)
		{
			$color_size_qty_arr[$s_id[csf('color_number_id')]][$s_id[csf('size_number_id')]]+=$s_id[csf('plan_cut_qnty')]; 
		}
				
		$dtlsData = sql_select("SELECT a.color_size_break_down_id,
		sum(CASE WHEN a.production_type='$qty_source' then a.production_qnty ELSE 0 END) as rcv_qnty,
		sum(CASE WHEN a.production_type=84 then a.production_qnty ELSE 0 END) as rcv_rtn_qnty,
		sum(CASE WHEN a.production_type=82 then a.production_qnty ELSE 0 END) as issue_qnty, 
		sum(CASE WHEN a.production_type=83 then a.production_qnty ELSE 0 END) as issue_rtn_qnty 
		from pro_garments_production_dtls a,pro_garments_production_mst b 
		where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and b.location=$cbo_location and b.store_id=$cbo_store_name and b.floor_id=$cbo_floor and b.room_id=$cbo_room and b.rack_id=$txt_rack and b.shelf_id=$txt_shelf and a.color_size_break_down_id!=0 and a.production_type in($qty_source,82,83,84) group by a.color_size_break_down_id");
									
		foreach($dtlsData as $row)
		{				  
			$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv']= $row[csf('rcv_qnty')];
			$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['iss']= $row[csf('issue_qnty')];
			$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rtn']= $row[csf('issue_rtn_qnty')];
			$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv_rtn']= $row[csf('rcv_rtn_qnty')];
		} 
					
		$sql = "SELECT id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty from wo_po_color_size_breakdown
		where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' $pack_type_cond and is_deleted=0 and status_active in(1,2,3) order by color_number_id, size_order";
	}
	else // by default color and size level
	{
		$dtlsData = sql_select("SELECT a.color_size_break_down_id,
		sum(CASE WHEN a.production_type='$qty_source' then a.production_qnty ELSE 0 END) as rcv_qnty,
		sum(CASE WHEN a.production_type=84 then a.production_qnty ELSE 0 END) as rcv_rtn_qnty,
		sum(CASE WHEN a.production_type=82 then a.production_qnty ELSE 0 END) as issue_qnty, 
		sum(CASE WHEN a.production_type=83 then a.production_qnty ELSE 0 END) as issue_rtn_qnty 
		from pro_garments_production_dtls a,pro_garments_production_mst b 
		where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and b.location=$cbo_location and b.store_id=$cbo_store_name and b.floor_id=$cbo_floor and b.room_id=$cbo_room and b.rack_id=$txt_rack and b.shelf_id=$txt_shelf and a.color_size_break_down_id!=0 and a.production_type in($qty_source,82,83,84) $pack_typeCond group by a.color_size_break_down_id");
									
		foreach($dtlsData as $row)
		{				  
			$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv']= $row[csf('rcv_qnty')];
			$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv_rtn']= $row[csf('rcv_rtn_qnty')];
			$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['iss']= $row[csf('issue_qnty')];
			$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rtn']= $row[csf('issue_rtn_qnty')];
		} 
		$sql = "SELECT id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty from wo_po_color_size_breakdown
		where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' $pack_type_cond and is_deleted=0 and status_active in(1,2,3) order by color_number_id, size_order";
	}

		
	$colorResult = sql_select($sql);		
	$colorHTML="";
	$colorID='';
	$chkColor = array(); 
	$i=0;$totalQnty=0;

	foreach($colorResult as $color)
	{
		if( $variableSettings==2 ) // color level
		{ 
			$amount=$color[csf("production_qnty")]-$color[csf("cur_production_qnty")];//+$color[csf("rtn_qnty")];
			if($amount<1){$disable_for_posted="readonly";}else{$disable_for_posted="";}
			$colorHTML .='<tr><td>'.$color_library[$color[csf("color_number_id")]].'</td><td><input type="text" name="txt_color" id="colSize_'.($i+1).'" style="width:60px"  class="text_boxes_numeric" value="'.($amount).'" onblur="fn_colorlevel_total('.($i+1).')" '.$disable_for_posted.'></td><td></td></tr>';				
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
				$colorHTML .= '<div id="content_search_panel_'.$color[csf("color_number_id")].'" style="display:none" class="accord_close"><div style="padding-left: 40px;text-align:center"><input type="checkbox" onClick="active_placeholder_qty(' . $color[csf("color_number_id")] . ')" id="set_all_' . $color[csf("color_number_id")] . '">&nbsp;Available Qty Auto Fill</div><table id="table_'.$color[csf("color_number_id")].'">';
				$chkColor[] = $color[csf("color_number_id")];					
			}
			//$index = $color[csf("size_number_id")].$color[csf("color_number_id")];
			$colorID .= $color[csf("size_number_id")]."*".$color[csf("color_number_id")].",";
			
			$rcv_qnty=$color_size_qnty_array[$color[csf('id')]]['rcv'];
			$rcv_rtn_qnty=$color_size_qnty_array[$color[csf('id')]]['rcv_rtn'];
			$iss_qnty=$color_size_qnty_array[$color[csf('id')]]['iss'];
			$rtn_qnty=$color_size_qnty_array[$color[csf('id')]]['rtn'];

			//echo 
			$amount=$rcv_qnty-$rcv_rtn_qnty-$iss_qnty+$rtn_qnty;
			if($amount<1){$disable_for_posted="readonly";}else{$disable_for_posted="";}
			$colorHTML .='<tr><td>'.$size_library[$color[csf("size_number_id")]].'</td><td><input type="text" name="colorSize" id="colSize_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:80px" placeholder="'.$amount.'" value="" onblur="fn_total('.$color[csf("color_number_id")].','.($i+1).')" '.$disable_for_posted.'><input type="text" name="colorSizePOQnty" id="colorSizePOQnty_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:50px" value="'.$color[csf("order_quantity")].'" readonly disabled></td></tr>';				
		}
		$i++; 
	}

	
	
	//echo $colorHTML;die; 
	if( $variableSettings==2 ){ $colorHTML = '<table id="table_color" class="rpt_table"><thead><tr><th width="70">Color</th><th width="60">Quantity</th><th width="60">Reject</th></tr><tr> <th colspan="2"><div style="padding-left: 30px;text-align:left"><input type="checkbox" onClick="active_placeholder_qty_color(' . $color[csf("color_number_id")] . ')" id="set_all">&nbsp;<label for="set_all">Available Qty Auto Fill</label></div></th> </tr></thead><tbody>'.$colorHTML.'<tbody><tfoot><tr><th>Total</th><th><input type="text" id="total_color" value="'.$totalQnty.'" class="text_boxes_numeric" style="width:60px" ></th><th><input type="text" id="total_color_rej" class="text_boxes_numeric" style="width:60px" ></th></tr></tfoot></table>'; }
	echo "$('#breakdown_td_id').html('".addslashes($colorHTML)."');\n";
	$colorList = substr($colorID,0,-1);
	echo "$('#hidden_colorSizeID').val('".$colorList."');\n";
	//#############################################################################################//
	exit();
}

if($action=="show_dtls_listview_order")
{
	$dataArr = explode("**",$data);
	$po_id = $dataArr[0];
	$item_id = $dataArr[1];
	$variableSettings = $dataArr[2];
	$country_id = $dataArr[3];
	$cbo_location= $dataArr[4];
	$cbo_store_name= $dataArr[5];
	$cbo_floor= $dataArr[6];
	// $cbo_room= $dataArr[7];
	// $txt_rack= $dataArr[8];
	// $txt_shelf= $dataArr[9];

	$location_arr=return_library_array( "SELECT id, location_name from lib_location",'id','location_name');
	$company_arr=return_library_array( "SELECT id, company_name from lib_company",'id','company_name');		
	$room_rack_shelf_arr=return_library_array( "SELECT floor_room_rack_id, floor_room_rack_name from lib_floor_room_rack_mst",'floor_room_rack_id','floor_room_rack_name');		
	$qty_source=81;
	?>	 
	<div style="width:100%;">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="110" >Item Name</th>
                <th width="90" >Country</th>
                <th width="80" >Room</th>
                <th width="80" >Rack</th>
                <th width="80" >Shelf</th>
                <th width="60" >Receive Qty</th>
                <th width="60" >Receive Return Qty</th> 
                <th width="60" >Issue Qty</th> 
                <th width="60" >Issue Return Qty</th> 
                <th >Balance</th>
            </thead>
			<tbody>
				<?php  
					// and item_number_id=$item_id and country_id=$country_id
					$i=1;
					$dataSql="SELECT PO_BREAK_DOWN_ID, ITEM_NUMBER_ID, COUNTRY_ID, LOCATION,FLOOR_ID, ROOM_ID, RACK_ID, SHELF_ID, 
					SUM(CASE WHEN production_type='$qty_source' THEN production_quantity ELSE 0 END) as TOTALRCV,
					SUM(CASE WHEN production_type='84' THEN production_quantity ELSE 0 END) as TOTALRCVRTN,
					SUM(CASE WHEN production_type=82 THEN production_quantity ELSE 0 END) as TOTALISSUE, 
					SUM(CASE WHEN production_type=83 THEN production_quantity ELSE 0 END) as TOTALISSUERTN 
					from pro_garments_production_mst WHERE po_break_down_id=$po_id and production_type in ('81','82','83','84') and location=$cbo_location and store_id=$cbo_store_name and floor_id=$cbo_floor and status_active=1 and is_deleted=0 
					group by po_break_down_id, item_number_id, country_id, location, floor_id,room_id, rack_id, shelf_id ";
					// echo $dataSql;
					$sqlResult =sql_select($dataSql);
					// $sqlResult =sql_select("SELECT a.id,a.po_break_down_id,a.item_number_id, a.country_id, a.production_date, a.production_quantity, a.reject_qnty, a.production_source, a.serving_company, a.location from pro_garments_production_mst a,pro_gmts_delivery_mst b where b.id=$data and b.id=a.delivery_mst_id and a.production_type='14' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.production_date");

					foreach($sqlResult as $selectResult){
						if ($i%2==0)  $bgcolor="#E9F3FF";
						else $bgcolor="#FFFFFF";
						?>
							<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="get_php_form_data('<? echo $dataArr[0].'**'.$selectResult['ITEM_NUMBER_ID'].'**'.$dataArr[2].'**'.$selectResult['COUNTRY_ID'].'**'.$selectResult['LOCATION'].'**'.$dataArr[5].'**'.$selectResult['FLOOR_ID'].'**'.$selectResult['ROOM_ID'].'**'.$selectResult['RACK_ID'].'**'.$selectResult['SHELF_ID'];?>', 'color_and_size_level', 'requires/finish_gmts_issue_entry_controller'); ">
							<td width="30" align="center"><? echo $i; ?></td>
							<td width="110" align="center"><p><? echo $garments_item[$selectResult['ITEM_NUMBER_ID']]; ?></p></td>
							<td width="90" align="center"><p>
								<? 
									echo $country_library[$selectResult['COUNTRY_ID']]."</br>"; 
									echo "[".$country_short_name[$selectResult['COUNTRY_ID']]."]";
								?>        		
								</p></td>
							<td width="80"><?php echo $room_rack_shelf_arr[$selectResult['ROOM_ID']]; ?></td>
							<td width="80"><?php echo $room_rack_shelf_arr[$selectResult['RACK_ID']]; ?></td>
							<td width="80"><?php echo $room_rack_shelf_arr[$selectResult['SHELF_ID']]; ?></td>
							<td width="60" align="right"><?php echo $selectResult['TOTALRCV']; ?></td>
							<td width="60" align="right"><?php echo $selectResult['TOTALRCVRTN']; ?></td>
							<td width="60" align="right"><?php  echo $selectResult['TOTALISSUE']; ?></td>
							<td width="60" align="right"><?php  echo $selectResult['TOTALISSUERTN']; ?></td>
							<td align="right"><? echo $selectResult['TOTALRCV']-$selectResult['TOTALRCVRTN']-$selectResult['TOTALISSUE']+$selectResult['TOTALISSUERTN']; ?></td>
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
	$room_rack_shelf_arr=return_library_array( "SELECT floor_room_rack_id, floor_room_rack_name from lib_floor_room_rack_mst",'floor_room_rack_id','floor_room_rack_name');
	$qty_source=81;
	?>	 
	<div style="width:100%;">
		<table cellspacing="0" width="400" cellpadding="0" border="1" rules="all" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="100" >Item Name</th>
                <th width="90" >Country</th>
                <th width="80" >Room</th>
                <th width="80" >Rack</th>
                <th width="80" >Shelf</th>
                <th width="80" >Receive Qty</th>
                <th width="80" >Issue Qty</th> 
                <th width="80" >Issue Return Qty</th> 
                <th >Balance</th>
            </thead>
			<tbody>
				<?php  
					$i=1;

					$sql="SELECT a.PO_BREAK_DOWN_ID, a.ITEM_NUMBER_ID, a.COUNTRY_ID, a.LOCATION, a.STORE_ID,a.FLOOR_ID,a.ROOM_ID,a.RACK_ID,a.SHELF_ID, SUM(CASE WHEN a.production_type=82 THEN a.production_quantity ELSE 0 END) as TOTALISSUE from pro_garments_production_mst a,pro_gmts_delivery_mst b WHERE b.id=$dataArr[0] and b.id=a.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.po_break_down_id, a.item_number_id, a.country_id, a.location, a.store_id,a.floor_id,a.room_id,a.rack_id,a.shelf_id";
					// echo $dataSql;
					$sqlData =sql_select($sql);
					$po_info=$location_info=$Store_info=$floor_info=$room_info=$rack_info=$shelf_info='';
					foreach($sqlData as $row){
						$po_info.=$row['PO_BREAK_DOWN_ID'].',';
						$location_info.=$row['LOCATION'].',';
						$Store_info.=$row['STORE_ID'].',';
						$floor_info.=$row['FLOOR_ID'].',';
						// $room_info.=$row['ROOM_ID'].',';
						// $rack_info.=$row['RACK_ID'].',';
						// $shelf_info.=$row['SHELF_ID'].',';
					}
					$po_info=rtrim($po_info,',');
					$location_info=rtrim($location_info,',');
					$Store_info=rtrim($Store_info,',');
					$floor_info=rtrim($floor_info,',');
					// $room_info=rtrim($room_info,',');
					// $rack_info=rtrim($rack_info,',');
					// $shelf_info=rtrim($shelf_info,',');

					$sqlResult=sql_select("SELECT a.PO_BREAK_DOWN_ID, a.ITEM_NUMBER_ID, a.COUNTRY_ID, a.LOCATION, a.STORE_ID,a.FLOOR_ID,a.ROOM_ID,a.RACK_ID,a.SHELF_ID,
					SUM(CASE WHEN production_type='$qty_source' THEN production_quantity END) as TOTALRCV,
					SUM(CASE WHEN production_type=82 THEN production_quantity ELSE 0 END) as TOTALISSUE, 
					SUM(CASE WHEN production_type=83 THEN production_quantity ELSE 0 END) as TOTALISSUERTN 
					from pro_garments_production_mst a WHERE a.production_type in(81,82,83) and a.po_break_down_id in($po_info) and a.location in($location_info) and a.store_id in($Store_info) and a.floor_id in($floor_info) and a.status_active=1 and a.is_deleted=0  group by a.po_break_down_id, a.item_number_id, a.country_id, a.location, a.store_id,a.floor_id,a.room_id,a.rack_id,a.shelf_id");
					
					foreach($sqlResult as $row){
						if ($i%2==0)  $bgcolor="#E9F3FF";else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="get_php_form_data('<? echo $row['PO_BREAK_DOWN_ID'].'**'.$row['ITEM_NUMBER_ID'].'**'.$dataArr[1].'**'.$row['COUNTRY_ID'].'**'.$row['LOCATION'].'**'.$row['STORE_ID'].'**'.$row['FLOOR_ID'].'**'.$row['ROOM_ID'].'**'.$row['RACK_ID'].'**'.$row['SHELF_ID'];?>', 'color_and_size_level', 'requires/finish_gmts_issue_entry_controller'); "> 
							<td width="30" align="center"><? echo $i; ?></td>
							<td width="100" align="center"><p><? echo $garments_item[$row['ITEM_NUMBER_ID']]; ?></p></td>
							<td width="90" align="center"><p>
								<? 
									echo $country_library[$row['COUNTRY_ID']]."</br>"; 
									echo "[".$country_short_name[$row['COUNTRY_ID']]."]";
								?>        		
								</p>
							</td>
							<td width="80"><?php echo $room_rack_shelf_arr[$row['ROOM_ID']]; ?></td>
							<td width="80"><?php echo $room_rack_shelf_arr[$row['RACK_ID']]; ?></td>
							<td width="80"><?php echo $room_rack_shelf_arr[$row['SHELF_ID']]; ?></td>
							<td width="80" align="right"><?php echo $row['TOTALRCV']; ?></td>
							<td width="80" align="right"><?php  echo $row['TOTALISSUE']-$row['TOTALISSUERTN']; ?></td>
							<td width="80" align="right"><?php  echo $row['TOTALISSUERTN']; ?></td>
							<td align="right"><? echo $row['TOTALRCV']-$row['TOTALISSUE']+$row['TOTALISSUERTN']; ?></td>
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