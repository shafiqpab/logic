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

if ($action=="load_drop_down_location")
{    	 	  	 
	echo create_drop_down( "cbo_location", 170, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' $location_credential_cond order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/finish_gmts_receive_entry_controller', $('#cbo_company_id').val()+'**'+this.value , 'load_drop_down_store', 'store_td' );" );
}

if ($action=="load_drop_down_store")
{
	$explode_data=explode('**',$data);

	$sql="SELECT a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id and b.category_type in(30) and a.status_active =1 and a.is_deleted=0 and a.company_id='$explode_data[0]' and a.location_id='$explode_data[1]' $store_location_credential_cond order by a.store_name";
	//and a.company_id='$explode_data[0]' and a.location_id='$explode_data[1]'
	//echo $sql; die;
	echo create_drop_down( "cbo_store_name", 170, $sql,"id,store_name", 1, "-- Select Store --", $selected, "load_room_rack_self_bin('requires/finish_gmts_receive_entry_controller', 'floor','floor_td',document.getElementById('cbo_company_id').value,document.getElementById('cbo_location').value,this.value,this.value);",0 );     
}

if ($action=="load_room_rack_self_bin")
{
	load_room_rack_self_bin("requires/finish_gmts_receive_entry_controller",$data);
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
 		echo create_drop_down( "cbo_finish_company", 170, "select id,company_name from lib_company where is_deleted=0 and status_active=1 $company_credential_cond order by company_name","id,company_name", 1, "--- Select ---", "", "load_drop_down( 'requires/finish_gmts_receive_entry_controller', this.value, 'load_drop_down_finish_location', 'finish_location_td' );",0,0 ); 
 	else
		echo create_drop_down( "cbo_finish_company", 170, $blank_array,"", 1, "--- Select ---", $selected, "",0,0 );	
			
	exit();
}

if ($action=="load_drop_down_finish_location")
{    	 	  	 
	echo create_drop_down( "cbo_finish_location", 170, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' $location_credential_cond order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/finish_gmts_receive_entry_controller', $data+'**'+this.value, 'load_drop_down_finish_floor', 'finish_floor_td' );" );
}

if ($action=="load_drop_down_finish_floor")
{    	 	  	 
	$data = explode("**",$data);
	// $sql="SELECT b.floor_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.floor_id and a.company_id='$data[0]' and b.location_id='$data[1]' and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0  group by b.floor_id,a.floor_room_rack_name  order by a.floor_room_rack_name";
	//echo $sql;
	//echo create_drop_down( "cbo_finish_floor", 170,  $sql, "floor_id,floor_room_rack_name", 1, "-- Select Floor --", $selected, "","" );
	echo create_drop_down( "cbo_finish_floor", 170, "SELECT id,floor_name from lib_prod_floor where company_id='$data[0]' and location_id='$data[1]'  AND production_process = 11  and status_active =1 and is_deleted=0 group by id,floor_name order by floor_name","id,floor_name", 0, "-- Select Floor --", $selected, "" );
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
	        <table ellspacing="0" cellpadding="0" width="960" border="1" rules="all" class="rpt_table" align="center">
	            <thead>
	                <tr>
	                    <th>Company</th>
	                    <!-- <th>Buyer</th> -->
	                    <th>Source</th>
	                    <th>System No</th>
						<th>Order No</th>
	                    <th>Job No</th>
	                    <th width="200">Date Range</th>
	                    <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:80px;" /></th>
	                </tr>
	            </thead>
	            <tbody>
	                <tr>
	                    <td>
	                    <?
	                    echo create_drop_down( "cbo_company_id", 170, "select company_name,id from lib_company comp where is_deleted=0  and status_active=1 order by company_name",'id,company_name', 1, '--- Select Company ---',$company, ";");?>
	                    </td>
	                    
	                    <!-- <td>
	                    <?
	                    echo create_drop_down( "cbo_buyer_name", 120, "select id,buyer_name from lib_buyer where is_deleted=0 and status_active=1 order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "","",0 );
	                    ?>
	                    </td> -->
	                    <td>
	                    <? echo create_drop_down( "cbo_source", 170, $knitting_source,"", 1, "-- Select Source --", $selected, "fnc_company_check(this.value);load_drop_down( 'requires/finish_gmts_receive_entry_controller', this.value+'**'+$('#cbo_company_id').val(), 'load_drop_down_source', 'finishing_td' );dynamic_must_entry_caption(this.value);", 0, '1,3' ); ?>
	                    </td>
	                    <td>
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

	                    <input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view( document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_source').value+'_'+document.getElementById('txt_system_no').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_order_no').value+'_'+document.getElementById('txt_job_no').value, 'create_system_number_list_view', 'search_div', 'finish_gmts_receive_entry_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:80px;" />
	                    </td>
	                </tr>
	            </tbody>
	            <tfoot>
	                <tr>
	                    <td align="center" height="25" valign="middle" colspan="9" style="background-image: -moz-linear-gradient(bottom, rgb(136,170,281) 7%, rgb(194,220,255) 10%, rgb(136,170,281) 96%);">
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
    $source = $ex_data[1];
    // $buyer_id = $ex_data[4];
	$system_no = $ex_data[2];
	$txt_date_from = $ex_data[3];
	$txt_date_to = $ex_data[4];
	$txt_order_no = $ex_data[5];
	$txt_job_no = $ex_data[6];
	//echo $txt_order_no."-".$txt_job_no;//die();

	
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
	if(trim($txt_order_no)!="")
	{
		$sql_cond = " and b.po_number like '%".trim($txt_order_no)."'";
	}
	if(trim($txt_job_no)!="")
	{
		$sql_cond = " and c.job_no like '%".trim($txt_job_no)."'";
	}


	if(trim($company)!='0')
	{
		$sql_cond .= " and a.company_id='$company'";
	}
	if(trim($source)!='0')
	{
		$sql_cond .= " and a.production_source='$source'";
	}


	$sql ="SELECT a.id, a.sys_number, a.company_id, a.floor_id, a.working_company_id, a.location_id, a.production_source,a.challan_id,b.po_number,c.job_no from pro_gmts_delivery_mst a, 
	wo_po_break_down b, wo_po_details_master c,pro_garments_production_mst d
	where 
	b.job_no_mst=C.job_no
	and b.id=d.po_break_down_id
	and a.id=d.delivery_mst_id 
	and a.status_active=1 and a.is_deleted=0 and a.production_type=81 and a.entry_form=502 $sql_cond order by a.id DESC";

	 //echo $sql;//die();

	$arr=array(1=>$company_arr,2=>$knitting_source,3=>$company_arr,4=>$location_arr);

	echo create_list_view("list_view", "System Number,Company,Source,Fin. Company,Location,Order No,Job No","150,80,100,100,100,80,80,80","790","340",0, $sql , "js_set_value","id,sys_number,challan_id", "",1, "0,company_id,production_source,working_company_id,location_id", $arr,"sys_number,company_id,production_source,working_company_id,location_id,po_number,job_no", "","setFilterGrid('list_view',-1)","0,0,0,0,0") ;

	exit();
}

if($action=="populate_mst_form_data")
{
	$sql ="SELECT a.id, a.sys_number, a.company_id, a.delivery_date ,a.working_company_id,a.working_location_id,a.location_id,a.remarks,a.production_source,a.challan_id,a.store_id as STORE_ID,a.floor_id as FLOOR_ID,a.challan_no as CHALLAN_NO, a.working_floor_id as WORKING_FLOOR_ID from pro_gmts_delivery_mst a where a.id='$data' and production_type=81";

	//echo $sql.";\n";
	$result =sql_select($sql);
	// $delivery_no=return_library_array("select id, sys_number from pro_gmts_delivery_mst where id='".$result[0][csf('challan_id')]."'","id","sys_number");
	echo "$('#cbo_company_id').val('".$result[0][csf('company_id')]."');\n";
	echo "get_php_form_data(".$result[0][csf('company_id')].",'load_variable_settings','requires/finish_gmts_receive_entry_controller');\n";
	echo "load_drop_down( 'requires/finish_gmts_receive_entry_controller', ".$result[0][csf('production_source')].", 'load_drop_down_source', 'finishing_td' );\n";
	echo "$('#cbo_finish_company').val('".$result[0][csf('working_company_id')]."');\n";
	if($result[0][csf('production_source')]==1)
	{
		echo "load_drop_down( 'requires/finish_gmts_receive_entry_controller', ".$result[0][csf('working_company_id')].", 'load_drop_down_finish_location', 'finish_location_td' );\n";
		echo "$('#cbo_finish_location').val('".$result[0][csf('working_location_id')]."');\n";
		echo "load_drop_down( 'requires/finish_gmts_receive_entry_controller', '".$result[0][csf('working_company_id')]."**".$result[0][csf('working_location_id')]."', 'load_drop_down_finish_floor', 'finish_floor_td' );\n";
		echo "$('#cbo_finish_floor').val('".$result[0]['WORKING_FLOOR_ID']."');\n";
	}
	echo "load_drop_down( 'requires/finish_gmts_receive_entry_controller', ".$result[0][csf('company_id')].", 'load_drop_down_location', 'location_td' );\n";
	echo "$('#cbo_location').val('".$result[0][csf('location_id')]."');\n";
	echo "load_drop_down( 'requires/finish_gmts_receive_entry_controller',  '".$result[0][csf('company_id')]."**".$result[0][csf('location_id')]."', 'load_drop_down_store', 'store_td' );\n";
	echo "$('#cbo_store_name').val('".$result[0]['STORE_ID']."');\n";

	if($result[0]['STORE_ID']>0)
	{
		echo "load_room_rack_self_bin('requires/finish_gmts_receive_entry_controller', 'floor','floor_td', '".$result[0][csf('company_id')]."','"."','".$result[0]['STORE_ID']."',this.value);\n";
	}
	echo "$('#cbo_floor').val('".$result[0]['FLOOR_ID']."');\n";
	if($result[0]['FLOOR_ID']>0)
	{
		echo "load_room_rack_self_bin('requires/finish_gmts_receive_entry_controller', 'room','room_td', '".$result[0][csf('company_id')]."','"."','".$result[0]['STORE_ID']."','".$result[0]['FLOOR_ID']."',this.value);\n";
	}

	echo "$('#txt_challan_no').val('".$result[0][csf('challan_no')]."');\n";
	echo "$('#txt_challan_id').val('".$result[0][csf('challan_id')]."');\n";
	echo "$('#cbo_source').val('".$result[0][csf('production_source')]."');\n";
	echo "$('#txt_remark').val('".$result[0][csf('remarks')]."');\n";
	echo "$('#txt_rcv_date').val('".change_date_format($result[0][csf('delivery_date')])."');\n";
	echo "set_button_status(0, permission, 'fnc_gmt_rcv_entry',1);\n";
 	exit();
}

if($action=="show_all_listview")
{

	$location_arr=return_library_array( "SELECT id, location_name from lib_location",'id','location_name');
	$company_arr=return_library_array( "SELECT id, company_name from lib_company",'id','company_name');
	$supplier_arr=return_library_array( "SELECT id, supplier_name from  lib_supplier",'id','supplier_name');
	$sewing_line_arr=return_library_array( "SELECT id, line_name from  lib_sewing_line",'id','line_name');
	
	$dataArr = explode("**",$data);
	$po_id = $dataArr[0];
	$item_id = $dataArr[1];
	$country_id = $dataArr[2];
	$job_no= $dataArr[3];	
	$system_id= $dataArr[4];	
	?>
	<!-- ========================== dtls list view end and country list view start ======================== -->
	<? echo "******";?>
		
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="380" class="rpt_table">
        <thead>
            <th width="20">SL</th>
            <th width="90">Item Name</th>
            <th width="80">Country</th>
            <th width="55">Shipment Date</th>
            <th width="45">Order Qty.</th>
            <th width="45">Fin Qty</th>
            <th>Delv. Qty.</th>                    
        </thead>
		<?
		$issue_qnty_arr=sql_select("SELECT a.production_type,a.po_break_down_id, a.item_number_id, a.country_id, b.production_qnty from pro_garments_production_mst a, pro_garments_production_dtls b where a.id=b.mst_id and a.delivery_mst_id='$po_id' and a.production_type in(8,81) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		$issue_data_arr=array();
		foreach($issue_qnty_arr as $row)
		{
			$issue_data_arr[$row[csf("production_type")]][$row[csf("po_break_down_id")]][$row[csf("country_id")]][$row[csf("item_number_id")]]+=$row[csf("production_qnty")];
		}  
		$i=1;
		$sqlResult =sql_select("SELECT po_break_down_id, item_number_id, country_id, max(country_ship_date) as country_ship_date, sum(order_quantity) as order_qnty, sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown where po_break_down_id='$po_id' and status_active=1  and is_deleted=0 group by po_break_down_id, item_number_id, country_id order by country_ship_date");
		foreach($sqlResult as $row)
		{
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$out_qnty=$issue_data_arr[8][$row[csf("po_break_down_id")]][$row[csf("country_id")]][$row[csf("item_number_id")]];
			$issue_qnty=$issue_data_arr[81][$row[csf("po_break_down_id")]][$row[csf("country_id")]][$row[csf("item_number_id")]];
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="put_country_data(<? echo $row[csf('po_break_down_id')].",".$row[csf('item_number_id')].",".$row[csf('country_id')].",".$row[csf('order_qnty')].",".$row[csf('plan_cut_qnty')]; ?>);"> 
				<td width="20"><? echo $i; ?></td>
				<td width="100"><p><? echo $garments_item[$row[csf('item_number_id')]]; ?></p></td>
				<td width="80" align="center"><p>
					<? 
						echo $country_library[$row[csf('country_id')]]."</br>";
						echo "[".$country_short_name[$row[csf('country_id')]]."]"; 
					?>
				</p></td>
				<td width="55" align="center"><? if($row[csf('country_ship_date')]!="0000-00-00") echo change_date_format($row[csf('country_ship_date')]); ?>&nbsp;</td>
				<td align="right" width="65"><?  echo $row[csf('order_qnty')]; ?></td>
				<td align="right" width="65"><?  echo $out_qnty; ?></td>
                <td align="right"><?  echo $issue_qnty; ?></td>
			</tr>
			<?	
			$i++;
		}
		?>
	</table>
	<?
	exit();
}

if($action=="challan_number_popup")
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
	        <table ellspacing="0" cellpadding="0" width="800" border="1" rules="all" class="rpt_table" align="center">
	            <thead>
	                <tr>
	                    <th>Company</th>
	                    <!-- <th>Buyer</th> -->
	                    <th>Source</th>
	                    <th>System No</th>
	                    <th width="200">Date Range</th>
	                    <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:80px;" /></th>
	                </tr>
	            </thead>
	            <tbody>
	                <tr>
	                    <td>
	                    <?
	                    echo create_drop_down( "cbo_company_id", 170, "select company_name,id from lib_company comp where is_deleted=0  and status_active=1 order by company_name",'id,company_name', 1, '--- Select Company ---',$company, "");?>
	                    </td>
	                    
	                    <!-- <td>
	                    <?
	                    echo create_drop_down( "cbo_buyer_name", 120, "select id,buyer_name from lib_buyer where is_deleted=0 and status_active=1 order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "","",0 );
	                    ?>
	                    </td> -->
	                    <td>
	                    <? echo create_drop_down( "cbo_source", 170, $knitting_source,"", 1, "-- Select Source --", $selected, "fnc_company_check(this.value);load_drop_down( 'requires/finish_gmts_receive_entry_controller', this.value+'**'+$('#cbo_company_id').val(), 'load_drop_down_source', 'finishing_td' );dynamic_must_entry_caption(this.value);", 0, '1,3' ); ?>
	                    </td>
	                    <td>
	                    <input type="text" style="width:100px" class="text_boxes"  name="txt_system_no" id="txt_system_no" />
	                    </td>
	                    <td align="center">
	                    <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px"> To
	                    <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px">
	                    </td>
	                    <td align="center">

	                    <input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view( document.getElementById('cbo_company_id').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_source').value+'_'+document.getElementById('txt_system_no').value, 'create_challan_number_list_view', 'search_div', 'finish_gmts_receive_entry_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:80px;" />
	                    </td>
	                </tr>
	            </tbody>
	            <tfoot>
	                <tr>
	                    <td align="center" height="25" valign="middle" colspan="9" style="background-image: -moz-linear-gradient(bottom, rgb(136,170,281) 7%, rgb(194,220,255) 10%, rgb(136,170,281) 96%);">
	                    <? echo load_month_buttons(1);  ?>
	                    <input type="hidden" id="hidden_search_data">
	                    </td>
	                </tr>
	            </tfoot>
	        </table>
	        <div style="margin-top:10px" id="search_div"></div>
	    </form>
	</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}

if($action=="create_challan_number_list_view")
{
 	$ex_data = explode("_",$data);

    $company = $ex_data[0];
    $txt_date_from = $ex_data[1];
	$txt_date_to = $ex_data[2];
    $source = $ex_data[3];
    // $buyer_id = $ex_data[4];
	$system_no = $ex_data[4];

	
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
	if(trim($source)!='0')
	{
		$sql_cond .= " and a.production_source='$source'";
	}

	$prv_rcv_data=return_library_array( "SELECT challan_id, sum(production_quantity) as production_quantity from pro_garments_production_mst where production_type='81' and status_active=1 group by challan_id",'challan_id','production_quantity');
	// $prv_rcv_rtn_data=return_library_array( "SELECT a.challan_id, sum(b.production_quantity) as production_quantity from pro_gmts_delivery_mst a,pro_garments_production_mst b where a.id=b.challan_id and a.production_type='81' and b.production_type='84' and a.status_active=1 and b.status_active=1 group by a.challan_id",'challan_id','production_quantity');

	$sql ="SELECT a.id, a.sys_number, a.company_id, a.floor_id, a.working_company_id, a.location_id, a.production_source,a.delivery_date,sum(b.production_quantity) as PRODUCTION_QUANTITY from pro_gmts_delivery_mst a, pro_garments_production_mst b where a.id=b.delivery_mst_id and a.status_active=1 and b.status_active=1 and a.production_type=14 and b.production_type=14 and a.entry_form=463 $sql_cond group by a.id, a.sys_number, a.company_id, a.floor_id, a.working_company_id, a.location_id, a.production_source,a.delivery_date order by a.id desc";

	// echo $sql;die();
	$sqlResult=sql_select($sql);

	?>	 
	<div style="width:100%;">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="700" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="120">System Number</th>
                <th width="80" align="center">Delivery Date</th>
				<th width="120">Company</th>
				<th width="80" align="center">Source</th>
                <th width="100">Fin. Company</th>
                <th >Location</th> 
            </thead>
			<tbody>
				<?php  
					$i=1;
					foreach($sqlResult as $row)
					{
						if($i%2==0){$bgcolor="#E9F3FF";}else{$bgcolor="#FFFFFF";}
						$balance=$row["PRODUCTION_QUANTITY"]-$prv_rcv_data[$row["ID"]];
						if($balance>0)
						{
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('id')].'_'.$row[csf('sys_number')].'_'.$row[csf('company_id')]; ?>');" > 
								<td ><? echo $i; ?></td>
								<td ><p><? echo $row[csf('sys_number')]; ?></p></td>
								<td ><p><?php echo change_date_format($row[csf('delivery_date')]); ?></p></td>
								<td ><p><?php echo $company_arr[$row[csf('company_id')]]; ?></p></td>
								<td ><?php echo $knitting_source[$row[csf('production_source')]] ;?></td>
								<td ><?php  echo $company_arr[$row[csf('working_company_id')]]; ?></td>
								<td ><? echo $location_arr[$row[csf('location_id')]]; ?></td>
							</tr>
							<?php
							$i++;
						}
					}
					?>
			</tbody>
		</table>
	</div>
	<?
	exit();
}

if($action=="populate_challan_form_data")
{
	$sql ="SELECT a.id, a.sys_number, a.company_id, a.delivery_date ,a.working_company_id,a.remarks,a.production_source,a.location_id,a.floor_id from pro_gmts_delivery_mst a where a.id='$data' and production_type=14";

	// echo $sql.";\n";
	$result =sql_select($sql);
	
	echo "load_drop_down( 'requires/finish_gmts_receive_entry_controller', ".$result[0][csf('production_source')].", 'load_drop_down_source', 'finishing_td' );\n";
	echo "load_drop_down( 'requires/finish_gmts_receive_entry_controller', ".$result[0][csf('company_id')].", 'load_drop_down_location', 'location_td' );\n";
	if($result[0][csf('production_source')]==1)
	{
		echo "load_drop_down( 'requires/finish_gmts_receive_entry_controller', ".$result[0][csf('working_company_id')].", 'load_drop_down_finish_location', 'finish_location_td' );\n";

		$wo_company = $result[0][csf('working_company_id')];
		$location = $result[0][csf('location_id')];
		$data_str = $wo_company."**".$location;
		echo "load_drop_down( 'requires/finish_gmts_receive_entry_controller','".$data_str."', 'load_drop_down_finish_floor', 'finish_floor_td' );\n";
	}
	echo "get_php_form_data(".$result[0][csf('company_id')].",'load_variable_settings','requires/finish_gmts_receive_entry_controller');\n";
	// echo "load_drop_down( 'requires/finish_gmts_receive_entry_controller',  ".$result[0][csf('location_id')].", 'load_drop_down_store', 'store_td' );\n";

	echo "$('#txt_challan_no').val('".$result[0][csf('sys_number')]."');\n";
	echo "$('#txt_challan_id').val('".$result[0][csf('id')]."');\n";
	echo "$('#cbo_company_id').val('".$result[0][csf('company_id')]."');\n";
	echo "$('#cbo_finish_company').val('".$result[0][csf('working_company_id')]."');\n";
	echo "$('#cbo_finish_location').val('".$result[0][csf('location_id')]."');\n";
	echo "$('#cbo_finish_floor').val('".$result[0][csf('floor_id')]."');\n";
	echo "$('#cbo_source').val('".$result[0][csf('production_source')]."');\n";
	echo "set_button_status(0, permission, 'fnc_gmt_rcv_entry',1);\n";
 	exit();
}

if($action=="show_dtls_listview_challan")
{
	$data_arr=explode("**",$data);
	$delivery_id=$data_arr[0];
	$system_id=$data_arr[1];
	$location_arr=return_library_array( "SELECT id, location_name from lib_location",'id','location_name');
	$company_arr=return_library_array( "SELECT id, company_name from lib_company",'id','company_name');
	$supplier_arr=return_library_array( "SELECT id, supplier_name from  lib_supplier",'id','supplier_name');

	$sys_rcv_info=array();
	if($system_id!="")
	{
		$sys_rcv_sql=sql_select("SELECT a.id as ID, a.po_break_down_id as PO_BREAK_DOWN_ID, a.item_number_id as ITEM_NUMBER_ID, a.country_id as COUNTRY_ID from pro_garments_production_mst a where a.delivery_mst_id=$system_id and a.challan_id=$delivery_id and a.production_type='81' and a.status_active=1 ");
		foreach($sys_rcv_sql as $row)
		{
			$sys_rcv_info[$row['PO_BREAK_DOWN_ID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']]=$row['ID'];
		}
	}
		
	?>	 
	<div style="width:100%;">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="100" align="center">Item Name</th>
                <th width="90" align="center">Country</th>
				<th width="110" align="center">Style No</th>
				<th width="100" align="center">PO No</th>
                <th width="60" align="center">Delivery Date</th>
                <th width="60" align="center">Delivery Qty</th> 
                <th align="center">Balance</th>
            </thead>
			<tbody>
				<?php  
					$i=1;

					//$sqlResult =sql_select("SELECT a.id,a.po_break_down_id,a.item_number_id, a.country_id, a.production_date, a.production_quantity, a.production_source, a.serving_company, a.location from pro_garments_production_mst a,pro_gmts_delivery_mst b where b.id=$data and b.id=a.delivery_mst_id and a.production_type='14' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.production_date");

					$sql="SELECT a.id as ID,a.po_break_down_id as PO_BREAK_DOWN_ID,a.item_number_id as ITEM_NUMBER_ID, a.country_id as COUNTRY_ID, a.production_date as PRODUCTION_DATE, a.production_quantity as PRODUCTION_QUANTITY, b.po_number as PO_NUMBER, c.style_ref_no as STYLE_REF_NO from pro_garments_production_mst a, wo_po_break_down b, wo_po_details_master c where a.delivery_mst_id=$delivery_id and a.production_type='14' and a.po_break_down_id=b.id and b.job_id=c.id and a.status_active=1 and b.status_active=1 and c.status_active=1 order by a.production_date";
					// echo $sql;
					$sqlResult=sql_select($sql);

					$prv_rcv_sql=sql_select("SELECT a.po_break_down_id as PO_BREAK_DOWN_ID, a.item_number_id as ITEM_NUMBER_ID, a.country_id as COUNTRY_ID, sum(a.production_quantity) as PRODUCTION_QUANTITY from pro_garments_production_mst a where a.challan_id=$delivery_id and a.production_type='81' and a.status_active=1 group by a.po_break_down_id, a.item_number_id, a.country_id");

					foreach($prv_rcv_sql as $row)
					{
						$pre_rcv_info[$row['PO_BREAK_DOWN_ID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']]['rcv']=$row['PRODUCTION_QUANTITY'];
						$po_id_arr[$row['PO_BREAK_DOWN_ID']]=$row['PO_BREAK_DOWN_ID'];
					}

					/* if(count($po_id_arr)>0)
					{
						$po_id_in=where_con_using_array($po_id_arr,0,'a.po_break_down_id');
						$prv_rcv_rtn_sql=sql_select("SELECT a.po_break_down_id as PO_BREAK_DOWN_ID, a.item_number_id as ITEM_NUMBER_ID, a.country_id as COUNTRY_ID, sum(CASE WHEN a.production_type=84 and b.challan_id=$delivery_id THEN a.production_quantity ELSE 0 END) as PRODUCTION_QUANTITY from pro_garments_production_mst a,pro_gmts_delivery_mst b where b.id=a.challan_id and a.production_type in (81,84) and a.status_active=1 and b.status_active=1 $po_id_in group by a.po_break_down_id, a.item_number_id, a.country_id");
					
						foreach($prv_rcv_rtn_sql as $row)
						{
							$pre_rcv_info[$row['PO_BREAK_DOWN_ID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']]['rcv_rtn']=$row['PRODUCTION_QUANTITY'];
						}
					} */

					foreach($sqlResult as $selectResult){
						if($sys_rcv_info[$selectResult['PO_BREAK_DOWN_ID']][$selectResult['ITEM_NUMBER_ID']][$selectResult['COUNTRY_ID']]!="")
						{
							$bgcolor="#008000";
						}
						else
						{
							if ($i%2==0){ $bgcolor="#E9F3FF";} else{ $bgcolor="#FFFFFF"; }
						}
						$balance=$selectResult['PRODUCTION_QUANTITY']-$pre_rcv_info[$selectResult['PO_BREAK_DOWN_ID']][$selectResult['ITEM_NUMBER_ID']][$selectResult['COUNTRY_ID']]['rcv'];
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="get_php_form_data('<? echo $selectResult['ID']; ?>','populate_input_form_data_challan','requires/finish_gmts_receive_entry_controller');" > 
							<td align="center"><? echo $i; ?></td>
							<td align="center"><p><? echo $garments_item[$selectResult['ITEM_NUMBER_ID']]; ?></p></td>
							<td align="center"><p>
								<? 
									echo $country_library[$selectResult['COUNTRY_ID']]."</br>"; 
									echo "[".$country_short_name[$selectResult['COUNTRY_ID']]."]";
								?>        		
								</p>
							</td>
							<td align="center"><p><?php echo $selectResult['STYLE_REF_NO']; ?></p></td>
							<td align="center"><p><?php echo $selectResult['PO_NUMBER']; ?></p></td>
							<td align="center"><?php echo change_date_format($selectResult['PRODUCTION_DATE']); ?></td>
							<td align="center"><?php  echo number_format($selectResult['PRODUCTION_QUANTITY'],2); ?></td>
							<td align="center"><? echo number_format($balance,2); ?></td>
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
if($action=="populate_challan_form_data_scan")
{
	$sql ="SELECT a.id, a.sys_number, a.company_id, a.delivery_date ,a.working_company_id,a.remarks,a.production_source,a.location_id,a.floor_id from pro_gmts_delivery_mst a where a.sys_number='$data' and production_type=14";

	//echo $sql.";\n";
	$result =sql_select($sql);
	
	echo "load_drop_down( 'requires/finish_gmts_receive_entry_controller', ".$result[0][csf('production_source')].", 'load_drop_down_source', 'finishing_td' );\n";
	echo "load_drop_down( 'requires/finish_gmts_receive_entry_controller', ".$result[0][csf('company_id')].", 'load_drop_down_location', 'location_td' );\n";
	if($result[0][csf('production_source')]==1)
	{
		echo "load_drop_down( 'requires/finish_gmts_receive_entry_controller', ".$result[0][csf('working_company_id')].", 'load_drop_down_finish_location', 'finish_location_td' );\n";
		$wo_company = $result[0][csf('working_company_id')];
		$location = $result[0][csf('location_id')];
		$data_str = $wo_company."**".$location;
		echo "load_drop_down( 'requires/finish_gmts_receive_entry_controller','".$data_str."', 'load_drop_down_finish_floor', 'finish_floor_td' );\n";

	}
	echo "get_php_form_data(".$result[0][csf('company_id')].",'load_variable_settings','requires/finish_gmts_receive_entry_controller');\n";
	// echo "load_drop_down( 'requires/finish_gmts_receive_entry_controller',  ".$result[0][csf('location_id')].", 'load_drop_down_store', 'store_td' );\n";

	echo "$('#txt_challan_no').val('".$result[0][csf('sys_number')]."');\n";
	echo "$('#txt_challan_id').val('".$result[0][csf('id')]."');\n";
	echo "$('#cbo_company_id').val('".$result[0][csf('company_id')]."');\n";
	echo "$('#cbo_finish_company').val('".$result[0][csf('working_company_id')]."');\n";
	echo "$('#cbo_finish_location').val('".$result[0][csf('location_id')]."');\n";
	echo "$('#cbo_finish_floor').val('".$result[0][csf('floor_id')]."');\n"; 
	echo "$('#cbo_source').val('".$result[0][csf('production_source')]."');\n";
	echo "set_button_status(0, permission, 'fnc_gmt_rcv_entry',1);\n";
 	exit();
}

if($action=="show_dtls_listview_challan_scan")
{
	$location_arr=return_library_array( "SELECT id, location_name from lib_location",'id','location_name');
	$company_arr=return_library_array( "SELECT id, company_name from lib_company",'id','company_name');
		
	?>	 
	<div style="width:100%;">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="100" align="center">Item Name</th>
                <th width="90" align="center">Country</th>
				<th width="110" align="center">Style No</th>
				<th width="100" align="center">PO No</th>
                <th width="60" align="center">Delivery Date</th>
                <th width="60" align="center">Delivery Qty</th> 
                <th align="center">Balance</th>
            </thead>
			<tbody>
				<?php  
					$i=1;
					$total_production_qnty=0;

					// $sqlResult =sql_select("SELECT a.id,a.po_break_down_id,a.item_number_id, a.country_id, a.production_date, a.production_quantity, a.production_source, a.serving_company, a.location from pro_garments_production_mst a,pro_gmts_delivery_mst b where b.sys_number='$data' and b.id=a.delivery_mst_id and a.production_type='14' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.production_date");

					$sql ="SELECT a.id,a.po_break_down_id,a.item_number_id, a.country_id, a.production_date, a.production_quantity, b.id as MST_ID, c.po_number as PO_NUMBER, d.style_ref_no as STYLE_REF_NO from pro_garments_production_mst a,pro_gmts_delivery_mst b, wo_po_break_down c, wo_po_details_master d where b.sys_number='$data' and b.id=a.delivery_mst_id and a.production_type='14' and a.po_break_down_id=c.id and c.job_id=d.id and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 order by a.production_date";
					$sqlResult =sql_select($sql);

					$mst_id=$sqlResult[0]['MST_ID'];
					$prv_rcv_sql=sql_select("SELECT a.po_break_down_id as PO_BREAK_DOWN_ID, a.item_number_id as ITEM_NUMBER_ID, a.country_id as COUNTRY_ID, sum(a.production_quantity) as PRODUCTION_QUANTITY from pro_garments_production_mst a where a.challan_id=$mst_id and a.production_type='81' and a.status_active=1 group by a.po_break_down_id, a.item_number_id, a.country_id");

					foreach($prv_rcv_sql as $row)
					{
						$pre_rcv_info[$row['PO_BREAK_DOWN_ID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']]['rcv']=$row['PRODUCTION_QUANTITY'];
						$po_id_arr[$row['PO_BREAK_DOWN_ID']]=$row['PO_BREAK_DOWN_ID'];
					}

					/* if(count($po_id_arr)>0)
					{
						$po_id_in=where_con_using_array($po_id_arr,0,'a.po_break_down_id');
						$prv_rcv_rtn_sql=sql_select("SELECT a.po_break_down_id as PO_BREAK_DOWN_ID, a.item_number_id as ITEM_NUMBER_ID, a.country_id as COUNTRY_ID, sum(CASE WHEN a.production_type=84 and b.challan_id=$mst_id THEN a.production_quantity ELSE 0 END) as PRODUCTION_QUANTITY from pro_garments_production_mst a,pro_gmts_delivery_mst b where b.id=a.challan_id and a.production_type in (81,84) and a.status_active=1 and b.status_active=1 $po_id_in group by a.po_break_down_id, a.item_number_id, a.country_id");
					
						foreach($prv_rcv_rtn_sql as $row)
						{
							$pre_rcv_info[$row['PO_BREAK_DOWN_ID']][$row['ITEM_NUMBER_ID']][$row['COUNTRY_ID']]['rcv_rtn']=$row['PRODUCTION_QUANTITY'];
						}
					} */					

					foreach($sqlResult as $selectResult){
						if ($i%2==0)  $bgcolor="#E9F3FF";
						else $bgcolor="#FFFFFF";
						$total_production_qnty+=$selectResult[csf('production_quantity')];
						$balance=$selectResult['PRODUCTION_QUANTITY']-$pre_rcv_info[$selectResult['PO_BREAK_DOWN_ID']][$selectResult['ITEM_NUMBER_ID']][$selectResult['COUNTRY_ID']]['rcv'];
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="get_php_form_data('<? echo $selectResult[csf('id')]; ?>','populate_input_form_data_challan','requires/finish_gmts_receive_entry_controller');" > 
							<td align="center"><? echo $i; ?></td>
							<td align="center"><p><? echo $garments_item[$selectResult[csf('item_number_id')]]; ?></p></td>
							<td align="center"><p>
								<? 
									echo $country_library[$selectResult[csf('country_id')]]."</br>"; 
									echo "[".$country_short_name[$selectResult[csf('country_id')]]."]";
								?>        		
								</p>
							</td>
							<td align="center"><p><?php echo $selectResult['STYLE_REF_NO']; ?></p></td>
							<td align="center"><p><?php echo $selectResult['PO_NUMBER']; ?></p></td>
							<td align="center"><?php echo change_date_format($selectResult[csf('production_date')]); ?></td>
							<td align="center"><?php  echo $selectResult[csf('production_quantity')]; ?></td>
							<td align="center"><? echo number_format($balance,2); ?></td>
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

if($action=="populate_input_form_data_challan")
{
	$data = explode("_",$data);

	$sqlResult =sql_select("SELECT id, company_id, garments_nature, po_break_down_id, item_number_id, challan_no, country_id,production_source, serving_company, location, production_quantity, production_source, production_type, entry_break_down_type, carton_qty, remarks, floor_id, total_produced, yet_to_produced, delivery_mst_id  from pro_garments_production_mst where id='$data[0]' and production_type='14' and status_active=1 and is_deleted=0 order by id");	

	$dissable='';
	$challan_id=$sqlResult[0][csf('delivery_mst_id')];	
	$company_id=$sqlResult[0][csf('company_id')];
	$garments_nature=$sqlResult[0][csf('garments_nature')];
	if($sqlResult[0][csf('production_source')]==1)
	{
		$company=$sqlResult[0][csf('serving_company')];
	}
	else
	{
		$company=$sqlResult[0][csf('company_id')];
	}		 
	$control_and_preceding=sql_select("SELECT is_control,preceding_page_id from variable_settings_production where status_active=1 and is_deleted=0 and variable_list=33 and page_category_id=268 and company_name='$company'");  
	$preceding_process= $control_and_preceding[0][csf("preceding_page_id")];
	echo "$('#hidden_variable_cntl').val('".$control_and_preceding[0][csf('is_control')]."');\n";

    $is_control=$control_and_preceding[0][csf("is_control")];
    $preceding_process=$control_and_preceding[0][csf("preceding_page_id")];
	$qty_source=14;

	$po_id = $sqlResult[0][csf('po_break_down_id')];
	$item_id = $sqlResult[0][csf('item_number_id')];
	$country_id = $sqlResult[0][csf('country_id')];
	$sql = sql_select("SELECT a.buyer_name,a.style_ref_no,a.job_no,b.po_quantity,b.po_number from wo_po_details_master a,wo_po_break_down b where a.id=b.job_id and b.id=$po_id and b.status_active=1 and b.is_deleted=0");
	$country_qty_sql = return_library_array("SELECT po_break_down_id, sum(order_quantity) as country_quantity from wo_po_color_size_breakdown where po_break_down_id=$po_id and country_id=$country_id and item_number_id=$item_id and status_active=1 and is_deleted=0 group by po_break_down_id",'po_break_down_id','country_quantity');

	foreach($sqlResult as $result)
	{ 
		echo "$('#cbo_country_name').val('".$result[csf('country_id')]."');\n";
		echo "$('#cbo_item_name').val('".$result[csf('item_number_id')]."');\n";

		echo "$('#txt_job_no').val('".$sql[0]['JOB_NO']."');\n";
		echo "$('#txt_style_no').val('".$sql[0]['STYLE_REF_NO']."');\n";
		echo "$('#cbo_buyer_name').val('".$sql[0]['BUYER_NAME']."');\n";
		echo "$('#txt_order_qty').val('".$sql[0]['PO_QUANTITY']."');\n";
		echo "$('#txt_order_no').val('".$sql[0]['PO_NUMBER']."');\n";
		echo "$('#txt_country_qty').val('".$country_qty_sql[$po_id]."');\n";
		echo "$('#hidden_po_break_down_id').val('$po_id');\n";
		echo "$('#garments_nature').val('$garments_nature');\n";

		if($result[csf('production_source')]==3)
		{
			$company=$sqlResult[0][csf('company_id')];
			$control_and_preceding=sql_select("select is_control,preceding_page_id from variable_settings_production where status_active=1 and is_deleted=0 and variable_list=33 and page_category_id=268 and company_name='$company'");  
			$preceding_process= $control_and_preceding[0][csf("preceding_page_id")];
			echo "$('#hidden_variable_cntl').val('".$control_and_preceding[0][csf('is_control')]."');\n";
			 
		}
		$rcvRtnSql="SELECT  
		SUM(CASE WHEN b.production_type=84 and a.challan_id=$challan_id THEN b.production_quantity ELSE 0 END) as TOTAL_RCV_RTN, 
		SUM(CASE WHEN b.production_type=84 and a.challan_id=$challan_id THEN b.carton_qty ELSE 0 END) as CARTON_RCV_RTN
		from pro_gmts_delivery_mst a, pro_garments_production_mst b WHERE a.id=b.challan_id and a.production_type in (81,84) and b.po_break_down_id=".$result[csf('po_break_down_id')]."  and b.item_number_id=".$result[csf('item_number_id')]." and b.country_id=".$result[csf('country_id')]." and a.status_active=1 and b.status_active=1";
		$rcvRtnArray=sql_select($rcvRtnSql);

		$dataSql="SELECT 
		SUM(CASE WHEN production_type='$qty_source' and delivery_mst_id='$challan_id' THEN production_quantity END) as totalDelivery,
		SUM(CASE WHEN production_type=81 and challan_id='$challan_id' THEN production_quantity ELSE 0 END) as totalRcv, 
		SUM(CASE WHEN production_type='$qty_source' and delivery_mst_id='$challan_id' THEN carton_qty END) as cartonDelivery, 
		SUM(CASE WHEN production_type=81 and challan_id='$challan_id' THEN carton_qty ELSE 0 END) as cartonRcv 
		from pro_garments_production_mst 
		WHERE po_break_down_id=".$result[csf('po_break_down_id')]." and item_number_id=".$result[csf('item_number_id')]." and country_id=".$result[csf('country_id')]." and status_active=1 and is_deleted=0";
		

		// echo $dataSql;
		$dataArray=sql_select($dataSql);
 		foreach($dataArray as $row)
		{  
			echo "$('#txt_delivery_input_qty').val('".$row[csf('totalDelivery')]."');\n";
			echo "$('#txt_cumul_rcv_qty').val('".$row[csf('totalRcv')]."');\n";			
			echo "$('#txt_delivery_input_carton').val('".$row[csf('cartonDelivery')]."');\n";
			echo "$('#txt_cumul_rcv_carton').val('".$row[csf('cartonRcv')]."');\n";			
			echo "$('#txt_cumul_rcv_rtn_qty').val('".$rcvRtnArray[0]["TOTAL_RCV_RTN"]."');\n";			
			$yet_to_produced = $row[csf('totalDelivery')]-$row[csf('totalRcv')];
			$yet_to_carton_produced = $row[csf('cartonDelivery')]-$row[csf('cartonRcv')];
			$actual_rcv = $row[csf('totalDelivery')]-$row[csf('totalRcv')]+$rcvRtnArray[0]["TOTAL_RCV_RTN"];
			echo "$('#txt_finishing_qty').val('".$yet_to_produced."');\n";
			echo "$('#hdn_finishing_qty').val('".$yet_to_produced."');\n";
		    echo "$('#txt_carton_qty').val('".$yet_to_carton_produced."');\n";
			echo "$('#txt_yet_to_rcv').val('".$yet_to_produced."');\n";
			echo "$('#hdn_yet_to_carton_rcv').val('".$yet_to_carton_produced."');\n";
			echo "$('#txt_actual_rcv').val('".$actual_rcv."');\n";
		}		
		
 		echo "set_button_status(0, permission, 'fnc_gmt_rcv_entry',1);\n";
		
		$color_library=return_library_array( "select id, color_name from lib_color",'id','color_name');
		$size_library=return_library_array( "select id, size_name from lib_size",'id','size_name');

		$variableSettings = $result[csf('entry_break_down_type')];
		
		if( $variableSettings!=1 ) // gross level
		{ 			
			$sql_dtls = sql_select("SELECT color_size_break_down_id, production_qnty, size_number_id, color_number_id from  pro_garments_production_dtls a,wo_po_color_size_breakdown b where a.mst_id=$data[0] and a.status_active=1 and a.color_size_break_down_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.status_active in(1,2,3) and b.is_deleted=0 and a.challan_id='$challan_id' and country_id='$country_id' ");	

			foreach($sql_dtls as $row)
			{				  
				if( $variableSettings==2 ) $index = $row[csf('color_number_id')]; else $index = $row[csf('size_number_id')].$color_arr[$row[csf("color_number_id")]].$row[csf('color_number_id')];
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
			 	$sql = "SELECT a.item_number_id, a.color_number_id, sum(a.order_quantity) as order_quantity, sum(a.plan_cut_qnty) as plan_cut_qnty,sum(CASE WHEN c.production_type='$qty_source' and a.delivery_mst_id='$challan_id' then b.production_qnty ELSE 0 END) as production_qnty,sum(CASE WHEN c.production_type=81 and a.challan_id='$challan_id' then b.production_qnty ELSE 0 END) as cur_production_qnty,sum(CASE WHEN c.production_type=81 and a.challan_id='$challan_id' then b.reject_qty ELSE 0 END) as reject_qty from wo_po_color_size_breakdown a left join pro_garments_production_dtls b on a.id=b.color_size_break_down_id left join pro_garments_production_mst c on c.id=b.mst_id where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active in(1,2,3)   group by a.item_number_id, a.color_number_id";	
			 	$sql_plan_cut="SELECT color_number_id, sum(plan_cut_qnty) as quantity from wo_po_color_size_breakdown where po_break_down_id='$po_id' and item_number_id='$item_id' and  country_id='$country_id' and status_active in(1,2,3) and is_deleted=0 group by color_number_id";
			 	foreach(sql_select($sql_plan_cut) as $key=>$value)
			 	{
			 		$plan_cut_arr[$value[csf("color_number_id")]] +=$value[csf("quantity")];
			 	}
			 	
			}
			else if( $variableSettings==3 ) //color and size level
			{				
				$dtlsData = "SELECT a.color_size_break_down_id,sum(CASE WHEN a.production_type='$qty_source' and a.delivery_mst_id='$challan_id' then a.production_qnty ELSE 0 END) as production_qnty, sum(CASE WHEN a.production_type=81 and a.challan_id='$challan_id' then a.production_qnty ELSE 0 END) as cur_production_qnty from pro_garments_production_dtls a, pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in($qty_source,81) group by a.color_size_break_down_id";

				$dtlsResult=sql_select($dtlsData);				
				foreach($dtlsResult as $row)
				{				  
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['del']= $row[csf('production_qnty')];
 					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv']= $row[csf('cur_production_qnty')];
				} 

				/* $rcvRtnDtlsData = "SELECT a.color_size_break_down_id,
				sum(CASE WHEN b.production_type=84 and c.challan_id=$challan_id then a.production_qnty ELSE 0 END) as production_qnty 
				from pro_garments_production_dtls a, pro_garments_production_mst b, pro_gmts_delivery_mst c  where a.status_active=1 and a.mst_id=b.id and c.id=b.challan_id and a.challan_id=b.challan_id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in(81,84) and b.production_type in(81,84) and c.production_type in(81,84) group by a.color_size_break_down_id";

				$rcvRtnDtlsResult=sql_select($rcvRtnDtlsData);				
				foreach($rcvRtnDtlsResult as $row)
				{				  
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv_rtn']= $row[csf('production_qnty')];
				}  */
				
				$sql = "SELECT id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty from wo_po_color_size_breakdown where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active in(1,2,3) order by color_number_id,size_order";
			}
			else // by default color and size level
			{
				$dtlsData = sql_select("SELECT a.color_size_break_down_id,sum(CASE WHEN a.production_type='$qty_source' and a.delivery_mst_id='$challan_id' then a.production_qnty ELSE 0 END) as production_qnty from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in($qty_source,81) group by a.color_size_break_down_id");
									
				foreach($dtlsData as $row)
				{				  
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['del']= $row[csf('production_qnty')];
				} 
				
				$sql="SELECT id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty from wo_po_color_size_breakdown where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active in(1,2,3)  order by color_number_id,size_order";
			}
 			//echo $sql;die;
			
			if($variableSettingsRej!=1)
			{
				$disable="";
			}
			else
			{
				$disable="disabled";
			}
			
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
					if($qty_source)
					{
						$production_quantity=$color[csf("production_qnty")];
					}
					else
					{
						$production_quantity=$plan_cut_arr[$color[csf("color_number_id")]];
					}
					$amount = $amountArr[$color[csf("color_number_id")]];
					$rejectAmt = $rejectArr[$color[csf("color_number_id")]];
					$order_rate=$con_per_dzn[$color[csf("color_number_id")]]/$costing_per_qty;
					if($amount<1){$disable_for_posted="readonly";}
					$colorHTML .='<tr><td>'.$color_library[$color[csf("color_number_id")]].'</td><td><input type="text" name="txt_color" id="colSize_'.($i+1).'" style="width:60px"  class="text_boxes_numeric" placeholder="'.($production_quantity-$color[csf("cur_production_qnty")]+$amount).'" value="'.$amount.'" onblur="fn_colorlevel_total('.($i+1).')" '.$disable_for_posted.'  ></td><td></td></tr>';					
					$fabric_amount_total+=$amount*$order_rate;
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
						$colorHTML .= '<div id="content_search_panel_'.$color[csf("color_number_id")].'" style="display:none" class="accord_close"><table id="table_'.$color[csf("color_number_id")].'">';
						$chkColor[] = $color[csf("color_number_id")];
						$totalFn .= "fn_total(".$color[csf("color_number_id")].");";
					}
 					$colorID .= $color[csf("size_number_id")]."*".$color[csf("color_number_id")].",";
					
					$del_qnty=$color_size_qnty_array[$color[csf('id')]]['del'];
					$rcv_qnty=$color_size_qnty_array[$color[csf('id')]]['rcv'];
					// $rcv_rtn_qnty=$color_size_qnty_array[$color[csf('id')]]['rcv_rtn'];
					$balance_qnty=$del_qnty-$rcv_qnty;
					$order_rate=$con_per_dzn[$color[csf("color_number_id")]][$color[csf("size_number_id")]]/$costing_per_qty;
					$disable_for_posted="";
					if($balance_qnty<1){$disable_for_posted="readonly";}
					$colorHTML .='<tr><td>'.$size_library[$color[csf("size_number_id")]].'</td><td><input type="text" name="colorSize" id="colSize_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:80px" placeholder="'.($balance_qnty).'" onblur="fn_total('.$color[csf("color_number_id")].','.($i+1).')" value="'.$balance_qnty.'" '.$disable_for_posted.' ><input type="text" name="colorSizePOQnty" id="colorSizePOQnty_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:50px" value="'.$color[csf("order_quantity")].'" readonly disabled></td></tr>';				
					$colorWiseTotal += $balance_qnty;
				}
				$i++; 
			}
			//echo $colorHTML;die; 
			if( $variableSettings==2 ){ $colorHTML = '<table id="table_color" class="rpt_table"><thead><th width="70">Color</th><th width="60">Quantity</th><th width="60">Reject</th></thead><tbody>'.$colorHTML.'<tbody><tfoot><tr><th>Total</th><th><input type="text" id="total_color" placeholder="'.$totalQnty.'" value="'.$totalQnty.'" class="text_boxes_numeric" style="width:60px" ></th><th><input type="text" id="total_color_rej" placeholder="'.$totalRejQnty.'" value="'.$totalRejQnty.'" class="text_boxes_numeric" style="width:60px" ></th></tr></tfoot></table>'; }
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

if($action=="show_dtls_listview")
{	
	$room_rack_self_arr = return_library_array("SELECT floor_room_rack_id,floor_room_rack_name from lib_floor_room_rack_mst", "floor_room_rack_id", "floor_room_rack_name");
	?>	 
	<div style="width:100%;">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="150" >Item Name</th>
                <th width="150" >Country</th>
                <th width="100" >Receive Qty</th> 
                <th width="150" >Serving Company</th>
                <th width="100" >Location</th>
                <th width="100" >Rack no</th>
                <th >Shelf no</th>
            </thead>
			<tbody>
				<?php  
					$i=1;
					$sqlResult =sql_select("SELECT a.id as ID,a.po_break_down_id as PO_BREAK_DOWN_ID,a.item_number_id as ITEM_NUMBER_ID, a.country_id as COUNTRY_ID, a.production_date as PRODUCTION_DATE, a.production_quantity as PRODUCTION_QUANTITY, a.production_source as PRODUCTION_SOURCE, a.serving_company as SERVING_COMPANY, a.location as LOCATION,a.rack_id as RACK_ID, a.shelf_id as SHELF_ID from pro_garments_production_mst a,pro_gmts_delivery_mst b where b.id=$data and b.id=a.delivery_mst_id and a.production_type='81' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id");

					foreach($sqlResult as $selectResult){
						if ($i%2==0){ $bgcolor="#E9F3FF";}else{ $bgcolor="#FFFFFF";}
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="get_php_form_data('<? echo $selectResult['ID']; ?>','populate_input_form_data','requires/finish_gmts_receive_entry_controller');" > 
							<td align="center"><? echo $i; ?></td>
							<td align="center"><p><? echo $garments_item[$selectResult['ITEM_NUMBER_ID']]; ?></p></td>
							<td align="center"><p>
								<? 
									echo $country_library[$selectResult['COUNTRY_ID']]."</br>"; 
									echo "[".$country_short_name[$selectResult['COUNTRY_ID']]."]";
								?>        		
								</p>
							</td>
							<td align="center"><?php  echo $selectResult['PRODUCTION_QUANTITY']; ?></td>
							<?php
							$source= $selectResult['PRODUCTION_SOURCE'];
							if($source==3)
							$serving_company= return_field_value("supplier_name","lib_supplier","id='".$selectResult['SERVING_COMPANY']."'");
							else
							$serving_company= return_field_value("company_name","lib_company","id='".$selectResult['SERVING_COMPANY']."'");
							?>	
							<td align="center"><p><?php echo $serving_company; ?></p></td>
							<?php $location_name= return_field_value("location_name","lib_location","id='".$selectResult['LOCATION']."'");?>
							<td align="center"><? echo $location_name; ?></td>
							<td align="center"><? echo $room_rack_self_arr[$selectResult['RACK_ID']]; ?></td>
							<td align="center"><? echo $room_rack_self_arr[$selectResult['SHELF_ID']]; ?></td>
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

	$sqlResult =sql_select("SELECT id, company_id, garments_nature, po_break_down_id, item_number_id, challan_no, country_id, production_source, serving_company,  location, produced_by, production_quantity, production_source, production_type, entry_break_down_type, carton_qty, remarks, floor_id, store_id, room_id, rack_id, shelf_id, total_produced, yet_to_produced, challan_id from pro_garments_production_mst where id='$data[0]' and production_type='81' and status_active=1 and is_deleted=0 order by id");	
  		
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
	$control_and_preceding=sql_select("SELECT is_control,preceding_page_id from variable_settings_production where status_active=1 and is_deleted=0 and variable_list=33 and page_category_id=268 and company_name='$company'");  
	$preceding_process= $control_and_preceding[0][csf("preceding_page_id")];
	echo "$('#hidden_variable_cntl').val('".$control_and_preceding[0][csf('is_control')]."');\n";

	$is_control=$control_and_preceding[0][csf("is_control")];
    $preceding_process=$control_and_preceding[0][csf("preceding_page_id")];
	$qty_source=14;

	$po_id = $sqlResult[0][csf('po_break_down_id')];
	$item_id = $sqlResult[0][csf('item_number_id')];
	$country_id = $sqlResult[0][csf('country_id')];
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
			echo "load_room_rack_self_bin('requires/finish_gmts_receive_entry_controller', 'room','room_td', '".$company."','"."','".$result['STORE_ID']."','".$result['FLOOR_ID']."',this.value);\n";
		}
		echo "document.getElementById('cbo_room').value 					= '".$result['ROOM_ID']."';\n";
		if($result['ROOM_ID']>0)
		{
			echo "load_room_rack_self_bin('requires/finish_gmts_receive_entry_controller', 'rack','rack_td', '".$company."','"."','".$result['STORE_ID']."','".$result['FLOOR_ID']."','".$result['ROOM_ID']."',this.value);\n";
		}
		echo "document.getElementById('txt_rack').value 					= '".$result['RACK_ID']."';\n";
		if($result['RACK_ID']>0)
		{
			echo "load_room_rack_self_bin('requires/finish_gmts_receive_entry_controller', 'shelf','shelf_td', '".$company."','"."','".$result['STORE_ID']."','".$result['FLOOR_ID']."','".$result['ROOM_ID']."','".$result['RACK_ID']."',this.value);\n";
		}
		echo "document.getElementById('txt_shelf').value 					= '".$result["SHELF_ID"]."';\n";


		if($result[csf('production_source')]==3)
		{
			$company=$sqlResult[0][csf('company_id')];
			$control_and_preceding=sql_select("select is_control,preceding_page_id from variable_settings_production where status_active=1 and is_deleted=0 and variable_list=33 and page_category_id=268 and company_name='$company'");  
			$preceding_process= $control_and_preceding[0][csf("preceding_page_id")];
			echo "$('#hidden_variable_cntl').val('".$control_and_preceding[0][csf('is_control')]."');\n";
			 
		}
 		echo "$('#txt_finishing_qty').val('".$result[csf('production_quantity')]."');\n";
		echo "$('#txt_carton_qty').val('".$result[csf('carton_qty')]."');\n";
  		echo "$('#txt_remark').val('".$result[csf('remarks')]."');\n";
				
		$rcvRtnSql="SELECT  
		SUM(CASE WHEN b.production_type=84 and a.challan_id=$challan_id THEN b.production_quantity ELSE 0 END) as TOTAL_RCV_RTN, 
		SUM(CASE WHEN b.production_type=84 and a.challan_id=$challan_id THEN b.production_quantity ELSE 0 END) as CARTON_RCV_RTN 
		from pro_gmts_delivery_mst a, pro_garments_production_mst b WHERE a.id=b.challan_id and a.production_type in (81,84) and b.po_break_down_id=".$result[csf('po_break_down_id')]."  and b.item_number_id=".$result[csf('item_number_id')]." and b.country_id=".$result[csf('country_id')]." and a.status_active=1 and b.status_active=1";
		$rcvRtnArray=sql_select($rcvRtnSql);

		$dataSql="SELECT 
		SUM(CASE WHEN production_type='$qty_source' and delivery_mst_id='$challan_id' THEN production_quantity END) as totalDelivery,
		SUM(CASE WHEN production_type=81 and challan_id='$challan_id' THEN production_quantity ELSE 0 END) as totalRcv,  
		SUM(CASE WHEN production_type='$qty_source' and delivery_mst_id='$challan_id' THEN carton_qty END) as cartonDelivery,
		SUM(CASE WHEN production_type=81 and challan_id='$challan_id' THEN carton_qty ELSE 0 END) as cartonRcv from pro_garments_production_mst WHERE po_break_down_id=".$result[csf('po_break_down_id')]."  and item_number_id=".$result[csf('item_number_id')]." and country_id=".$result[csf('country_id')]." and status_active=1 and is_deleted=0";
		
		//echo $dataSql;
		$dataArray=sql_select($dataSql);
 		foreach($dataArray as $row)
		{  
			echo "$('#txt_delivery_input_qty').val('".$row[csf('totalDelivery')]."');\n";
			echo "$('#txt_cumul_rcv_qty').val('".$row[csf('totalRcv')]."');\n";			
			echo "$('#txt_delivery_input_carton').val('".$row[csf('cartonDelivery')]."');\n";
			echo "$('#txt_cumul_rcv_carton').val('".$row[csf('cartonRcv')]."');\n";			
			echo "$('#txt_cumul_rcv_rtn_qty').val('".$rcvRtnArray[0]["TOTAL_RCV_RTN"]."');\n";			
			$yet_to_produced = $row[csf('totalDelivery')]-$row[csf('totalRcv')];
			$can_to_produced = $row[csf('totalDelivery')]+$result[csf('production_quantity')]-$row[csf('totalRcv')];
			$yet_to_carton_produced = $row[csf('cartonDelivery')]+$result[csf('carton_qty')]-$row[csf('cartonRcv')];
			$actual_rcv = $row[csf('totalDelivery')]-$row[csf('totalRcv')]+$rcvRtnArray[0]["TOTAL_RCV_RTN"];
			echo "$('#hdn_finishing_qty').val('".$can_to_produced."');\n";
			echo "$('#hdn_yet_to_carton_rcv').val('".$yet_to_carton_produced."');\n";
			echo "$('#txt_yet_to_rcv').val('".$yet_to_produced."');\n";
			echo "$('#txt_actual_rcv').val('".$actual_rcv."');\n";
		}		
		
 		echo "set_button_status(1, permission, 'fnc_gmt_rcv_entry',1);\n";

		$color_library=return_library_array( "select id, color_name from lib_color",'id','color_name');
		$size_library=return_library_array( "select id, size_name from lib_size",'id','size_name');

		$variableSettings = $result[csf('entry_break_down_type')];
		
		if( $variableSettings!=1 ) // gross level
		{ 			
			$sql_dtls = sql_select("SELECT color_size_break_down_id, production_qnty,size_number_id, color_number_id from  pro_garments_production_dtls a,wo_po_color_size_breakdown b where a.mst_id=$data[0] and a.status_active=1 and a.color_size_break_down_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.status_active in(1,2,3) and b.is_deleted=0 and a.challan_id='$challan_id' and country_id='$country_id' $pack_cond");	
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
			 	$sql = "SELECT a.item_number_id, a.color_number_id, sum(a.order_quantity) as order_quantity, sum(a.plan_cut_qnty) as plan_cut_qnty,sum(CASE WHEN c.production_type='$qty_source' and a.delivery_mst_id='$challan_id' then b.production_qnty ELSE 0 END) as production_qnty,sum(CASE WHEN c.production_type=81 and a.challan_id='$challan_id' then b.production_qnty ELSE 0 END) as cur_production_qnty from wo_po_color_size_breakdown a left join pro_garments_production_dtls b on a.id=b.color_size_break_down_id left join pro_garments_production_mst c on c.id=b.mst_id where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active in(1,2,3)   group by a.item_number_id, a.color_number_id";	
			 	$sql_plan_cut="SELECT color_number_id, sum(plan_cut_qnty) as quantity from wo_po_color_size_breakdown where po_break_down_id='$po_id' and item_number_id='$item_id' and  country_id='$country_id' and status_active in(1,2,3) and is_deleted=0 group by color_number_id";
			 	foreach(sql_select($sql_plan_cut) as $key=>$value)
			 	{
			 		$plan_cut_arr[$value[csf("color_number_id")]] +=$value[csf("quantity")];
			 	}
			 	
			}
			else if( $variableSettings==3 ) //color and size level
			{				
				$dtlsData = "SELECT a.color_size_break_down_id,sum(CASE WHEN a.production_type='$qty_source' and a.delivery_mst_id='$challan_id' then a.production_qnty ELSE 0 END) as production_qnty,sum(CASE WHEN a.production_type=81 and a.challan_id='$challan_id' then a.production_qnty ELSE 0 END) as cur_production_qnty from pro_garments_production_dtls a, pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in($qty_source,81) group by a.color_size_break_down_id";

				$dtlsData=sql_select($dtlsData);
									
				foreach($dtlsData as $row)
				{				  
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['del']= $row[csf('production_qnty')];
 					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv']= $row[csf('cur_production_qnty')];
				} 

				/* $rcvRtnDtlsData = "SELECT a.color_size_break_down_id,sum(CASE WHEN b.production_type=84 and c.challan_id=$challan_id then a.production_qnty ELSE 0 END) as production_qnty from pro_garments_production_dtls a, pro_garments_production_mst b, pro_gmts_delivery_mst c  where a.status_active=1 and a.mst_id=b.id and c.id=b.challan_id and a.challan_id=b.challan_id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in(81,84) and b.production_type in(81,84) and c.production_type in(81,84) group by a.color_size_break_down_id";

				$rcvRtnDtlsResult=sql_select($rcvRtnDtlsData);				
				foreach($rcvRtnDtlsResult as $row)
				{				  
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv_rtn']= $row[csf('production_qnty')];
				}  */
				
				$sql = "SELECT id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty from wo_po_color_size_breakdown where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' $pack_typeCond and is_deleted=0 and status_active in(1,2,3) order by color_number_id,size_order";
			}
			else // by default color and size level
			{
				$dtlsData = sql_select("SELECT a.color_size_break_down_id,sum(CASE WHEN a.production_type='$qty_source' and a.delivery_mst_id='$challan_id' then a.production_qnty ELSE 0 END) as production_qnty,SUM(CASE WHEN production_type=81 and a.challan_id='$challan_id' then a.production_qnty ELSE 0 END) as cur_production_qnty from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in($qty_source,81) group by a.color_size_break_down_id");
									
				foreach($dtlsData as $row)
				{				  
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['del']= $row[csf('production_qnty')];
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv']= $row[csf('cur_production_qnty')];
				} 

				/* $rcvRtnDtlsData = "SELECT a.color_size_break_down_id,sum(CASE WHEN b.production_type=84 and c.challan_id=$challan_id then a.production_qnty ELSE 0 END) as production_qnty from pro_garments_production_dtls a, pro_garments_production_mst b, pro_gmts_delivery_mst c  where a.status_active=1 and a.mst_id=b.id and c.id=b.challan_id and a.challan_id=b.challan_id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in(81,84) and b.production_type in(81,84) and c.production_type in(81,84) group by a.color_size_break_down_id";

				$rcvRtnDtlsResult=sql_select($rcvRtnDtlsData);				
				foreach($rcvRtnDtlsResult as $row)
				{				  
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv_rtn']= $row[csf('production_qnty')];
				}  */
				
				$sql="SELECT id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty from wo_po_color_size_breakdown where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' $pack_cond and is_deleted=0 and status_active in(1,2,3)  order by color_number_id,size_order";
			}
 			//echo $sql;die;
			
			if($variableSettingsRej!=1)
			{
				$disable="";
			}
			else
			{
				$disable="disabled";
			}
			
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
					if($qty_source)
					{
						$production_quantity=$color[csf("production_qnty")];
					}
					else
					{
						$production_quantity=$plan_cut_arr[$color[csf("color_number_id")]];
					}
					$amount = $amountArr[$color[csf("color_number_id")]];
					$rejectAmt = $rejectArr[$color[csf("color_number_id")]];
					$order_rate=$con_per_dzn[$color[csf("color_number_id")]]/$costing_per_qty;
					if($amount<1){$disable_for_posted="readonly";}else{$disable_for_posted="";}
					$colorHTML .='<tr><td>'.$color_library[$color[csf("color_number_id")]].'</td><td><input type="text" name="txt_color" id="colSize_'.($i+1).'" style="width:60px"  class="text_boxes_numeric" placeholder="'.($production_quantity-$color[csf("cur_production_qnty")]+$amount).'" value="'.$amount.'" onblur="fn_colorlevel_total('.($i+1).')" '.$disable_for_posted.'  ></td><td></td></tr>';					$fabric_amount_total+=$amount*$order_rate;
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
						$colorHTML .= '<div id="content_search_panel_'.$color[csf("color_number_id")].'" style="display:none" class="accord_close"><table id="table_'.$color[csf("color_number_id")].'">';
						$chkColor[] = $color[csf("color_number_id")];
						$totalFn .= "fn_total(".$color[csf("color_number_id")].");";
					}
 					$colorID .= $color[csf("size_number_id")]."*".$color[csf("color_number_id")].",";
					
					$del_qnty=$color_size_qnty_array[$color[csf('id')]]['del'];
					$rcv_qnty=$color_size_qnty_array[$color[csf('id')]]['rcv'];
					// $rcv_rtn_qnty=$color_size_qnty_array[$color[csf('id')]]['rcv_rtn'];
					$order_rate=$con_per_dzn[$color[csf("color_number_id")]][$color[csf("size_number_id")]]/$costing_per_qty;
					
					if($amount<1){$disable_for_posted="readonly";}else{$disable_for_posted="";}
					$colorHTML .='<tr><td>'.$size_library[$color[csf("size_number_id")]].'</td><td><input type="text" name="colorSize" id="colSize_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:80px" placeholder="'.($del_qnty-$rcv_qnty+$amount).'" onblur="fn_total('.$color[csf("color_number_id")].','.($i+1).')" value="'.$amount.'" '.$disable_for_posted.' ><input type="text" name="colorSizePOQnty" id="colorSizePOQnty_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:50px" value="'.$color[csf("order_quantity")].'" readonly disabled></td></tr>';				
					$colorWiseTotal += $amount;
					$fabric_amount_total+=$amount*$order_rate;
				}
				$i++; 
			}
			//echo $colorHTML;die; 
			if( $variableSettings==2 ){ $colorHTML = '<table id="table_color" class="rpt_table"><thead><th width="70">Color</th><th width="60">Quantity</th><th width="60">Reject</th></thead><tbody>'.$colorHTML.'<tbody><tfoot><tr><th>Total</th><th><input type="text" id="total_color" placeholder="'.$totalQnty.'" value="'.$totalQnty.'" class="text_boxes_numeric" style="width:60px"  ></th><th><input type="text" id="total_color_rej" placeholder="'.$totalRejQnty.'" value="'.$totalRejQnty.'" class="text_boxes_numeric" style="width:60px" ></th></tr></tfoot></table>'; }
			echo "$('#breakdown_td_id').html('".addslashes($colorHTML)."');\n";
			if( $variableSettings==3 )echo "$totalFn;\n";
			$colorList = substr($colorID,0,-1);
			echo "$('#hidden_colorSizeID').val('".$colorList."');\n";
			echo "$('#fabric_data').val('".($fabric_amount_total)."');\n";
		}
		else
		{
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

if ($action=="save_update_delete")
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
 	$control_and_preceding=sql_select("select is_control,preceding_page_id from variable_settings_production where status_active=1 and is_deleted=0 and variable_list=33 and page_category_id=268 and company_name=$cbo_company_id");  
    $is_control=$control_and_preceding[0][csf("is_control")];
    $preceding_process=$control_and_preceding[0][csf("preceding_page_id")];
	$qty_source=14;
	if($preceding_process==29) $qty_source=5;//Sewing Output
	else if($preceding_process==31) $qty_source=8;//Packing And Finishing
	else if($preceding_process==269) $qty_source=14;//finishing delivery
	//echo "10**";
	if ($operation!=0)
	{ 
		$gmts_receive_return = return_field_value("challan_id","pro_gmts_delivery_mst","challan_id=$txt_system_id and production_type=84 and status_active=1 and is_deleted=0 ");
		$gmts_finish_issue = return_field_value("id","pro_garments_production_mst","po_break_down_id=$hidden_po_break_down_id and production_type=82 and status_active=1 and is_deleted=0 ");

		if($gmts_receive_return || $gmts_finish_issue)
		{
			echo "101";disconnect($con);die;
		}
		die;
		if(str_replace("'","",$txt_pack_type)=="") $packType_cond=""; else $packType_cond=" and pack_type=$txt_pack_type";
		if(str_replace("'","",$hidden_ship_date)=="") $ship_date_cond=""; else $ship_date_cond=" and a.country_ship_date=$hidden_ship_date";
		$backValisql = "SELECT  b.color_size_break_down_id,
					(sum(CASE WHEN b.production_type='81' then b.production_qnty ELSE 0 END)-sum(CASE WHEN b.production_type='81' and b.mst_id=$txt_mst_id then b.production_qnty ELSE 0 END)) as recqty,
					(sum(CASE WHEN b.production_type='82' then b.production_qnty ELSE 0 END) + sum( CASE WHEN b.production_type=84 THEN b.production_qnty ELSE 0 END)) as iss_rec_ret_qnty
					from wo_po_color_size_breakdown a left join pro_garments_production_dtls b on a.id=b.color_size_break_down_id left join PRO_GMTS_DELIVERY_MST c on c.id=b.delivery_mst_id
					where a.po_break_down_id=$hidden_po_break_down_id and a.item_number_id=$cbo_item_name and a.country_id=$cbo_country_name $ship_date_cond $pack_type_cond and a.is_deleted=0 and a.status_active in(1,2,3) and b.production_type in(81,82,84) group by b.color_size_break_down_id";
		//echo "10**".$backValisql; die;
		$backValiResult = sql_select($backValisql);
		$nxtQtyArr=array();
		foreach ($backValiResult as $row) {
			$nxtQtyArr[$row[csf("color_size_break_down_id")]]['recQty']+=$row[csf("recqty")];
			$nxtQtyArr[$row[csf("color_size_break_down_id")]]['iss_rec_ret']+=$row[csf("iss_rec_ret_qnty")];
		}
		unset($backValiResult);
	}
	if ($operation==0) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		//table lock here 
		//if  ( check_table_status( 160, 1 )==0 ) { echo "15**0"; die;}
 		
		$txt_challan_id=str_replace("'","",$txt_challan_id);
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
			
			$country_finishing_qty=return_field_value("sum(production_quantity)","pro_garments_production_mst","po_break_down_id=$hidden_po_break_down_id and production_type=81 and country_id=$cbo_country_name $packType_cond and status_active=1 and is_deleted=0");
		
			if($country_iron_qty < $country_finishing_qty+$txt_finishing_qty)
			{
				echo "25**0";
				//check_table_status( 160,0);
				disconnect($con);
				die;
			}

			$country_carton_qty=return_field_value("sum(carton_qty)","pro_garments_production_mst","po_break_down_id=$hidden_po_break_down_id and production_type='$qty_source' and country_id=$cbo_country_name $packType_cond and status_active=1 and is_deleted=0");
			
			$country_carton_finishing_qty=return_field_value("sum(carton_qty)","pro_garments_production_mst","po_break_down_id=$hidden_po_break_down_id and production_type=81 and country_id=$cbo_country_name $packType_cond and status_active=1 and is_deleted=0");
		
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

			$new_sys_number = explode("*", return_next_id_by_sequence("", "pro_gmts_delivery_mst",$con,1,$cbo_company_id,'FGRE',502,date("Y",time()),0,0,81,0,0 ));
			$field_array_delivery = "id, sys_number_prefix, sys_number_prefix_num, sys_number, challan_id, challan_no, company_id, production_type, production_source, entry_form,working_company_id,working_location_id,working_floor_id,location_id,store_id,floor_id,delivery_date,remarks, inserted_by, insert_date";
			$mst_id = return_next_id_by_sequence(  "pro_gmts_delivery_mst_seq", "pro_gmts_delivery_mst", $con );
            $data_array_delivery = "(" . $mst_id . ",'" . $new_sys_number[1] . "','" .(int) $new_sys_number[2] . "','" . $new_sys_number[0] . "', " . $txt_challan_id . ", " . $txt_challan_no . ", " . $cbo_company_id . ",81," . $cbo_source . ",502,".$cbo_finish_company.",".$cbo_finish_location.",".$cbo_finish_floor.",".$cbo_location.",".$cbo_store_name ."," . $cbo_floor ."," . $txt_rcv_date ."," . $txt_remark . "," . $user_id . ",'" . $pc_date_time . "')";
            $challan_no =(int) $new_sys_number[2];
            $txt_system_no = $new_sys_number[0];
        } 
        else 
        {
            $mst_id = str_replace("'", "", $txt_system_id);
            $txt_chal_no = explode("-", str_replace("'", "", $txt_system_no));
            $challan_no = (int)$txt_chal_no[3];

            $field_array_delivery = "company_id*production_source*floor_id*remarks*working_company_id*working_location_id*working_floor_id*location_id*updated_by*update_date";
            $data_array_delivery = "".$cbo_company_id."*".$cbo_source."*".$cbo_floor."*".$txt_remark."*".$cbo_finish_company."*".$cbo_finish_location."*".$cbo_finish_floor."*".$cbo_location."*".$user_id."*'".$pc_date_time."'";
        }
		//$id=return_next_id("id", "pro_garments_production_mst", 1);

		$id= return_next_id_by_sequence(  "pro_gar_production_mst_seq",  "pro_garments_production_mst", $con );
		
  		$field_array1="id, garments_nature, company_id, challan_id, challan_no, po_break_down_id, item_number_id, country_id,  production_source, serving_company, location, production_date, production_quantity, production_type, entry_break_down_type, carton_qty, remarks, store_id, floor_id, room_id, rack_id, shelf_id, total_produced, yet_to_produced,delivery_mst_id, inserted_by, insert_date"; 
		if($db_type==0)
		{
			$data_array1="(".$id.",".$garments_nature.",".$cbo_company_id.",".$txt_challan_id.",".$challan_no.",".$hidden_po_break_down_id.",".$cbo_item_name.",".$cbo_country_name.",".$cbo_source.",".$cbo_finish_company.",".$cbo_location.",".$txt_rcv_date.",".$txt_finishing_qty.",81,".$sewing_production_variable.",".$txt_carton_qty.",".$txt_remark.",".$cbo_floor.",".$txt_cumul_rcv_qty.",".$txt_yet_to_rcv.",".$mst_id.",".$user_id.",'".$pc_date_time."')";
		}
	  	else if($db_type==2)
		{
			$data_array1="INSERT INTO pro_garments_production_mst (".$field_array1.") values(".$id.",".$garments_nature.",".$cbo_company_id.",".$txt_challan_id.",".$challan_no.",".$hidden_po_break_down_id.",".$cbo_item_name.",".$cbo_country_name.",".$cbo_source.",".$cbo_finish_company.",".$cbo_location.",".$txt_rcv_date.",".$txt_finishing_qty.",81,".$sewing_production_variable.",".$txt_carton_qty.",".$txt_remark.",".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$txt_cumul_rcv_qty.",".$txt_yet_to_rcv.",".$mst_id.",".$user_id.",'".$pc_date_time."')";
		}
 		//$rID=sql_insert("pro_garments_production_mst",$field_array1,$data_array1,1);
		// echo $data_array1;die;
		
		// pro_garments_production_dtls table entry here ----------------------------------///
		$field_array="id, mst_id, production_type, color_size_break_down_id, production_qnty, delivery_mst_id, challan_id";
		if(str_replace("'","",$txt_pack_type)=="") $pack_type_cond=""; else $pack_type_cond=" and b.pack_type=$txt_pack_type";
		$dtlsData = sql_select("SELECT a.color_size_break_down_id,
			sum(CASE WHEN a.production_type='$qty_source' then a.production_qnty ELSE 0 END) as production_qnty,
			sum(CASE WHEN a.production_type=81 then a.production_qnty ELSE 0 END) as cur_production_qnty 
			from pro_garments_production_dtls a,pro_garments_production_mst b 
			where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id=$hidden_po_break_down_id and b.item_number_id=$cbo_item_name and b.country_id=$cbo_country_name and a.color_size_break_down_id<>0 and a.production_type in($qty_source,81) $pack_type_cond
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
				if($j==0)$data_array = "(".$dtls_id.",".$id.",81,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."','".$mst_id."',".$txt_challan_id.")";
				else $data_array .= ",(".$dtls_id.",".$id.",81,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."','".$mst_id."',".$txt_challan_id.")";
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
				if($j==0)$data_array = "(".$dtls_id.",".$id.",81,'".$colSizeID_arr[$index]."','".$colorSizeValue."','".$mst_id."',".$txt_challan_id.")";
				else $data_array .= ",(".$dtls_id.",".$id.",81,'".$colSizeID_arr[$index]."','".$colorSizeValue."','".$mst_id."',".$txt_challan_id.")";
				//$dtls_id=$dtls_id+1;
 				$j++;
			}
		}//color and size wise
		// echo "10**INSERT INTO pro_gmts_delivery_mst (".$field_array_delivery.") VALUES ".$data_array_delivery; disconnect($con);die;
		if (str_replace("'", "", $txt_system_id) == "") 
		{
            $challanrID = sql_insert("pro_gmts_delivery_mst", $field_array_delivery, $data_array_delivery, 1);
        } 
        else 
        {
            $challanrID = sql_update("pro_gmts_delivery_mst", $field_array_delivery, $data_array_delivery, "id", $txt_system_id, 1);
        }
		// echo "10**INSERT INTO pro_garments_production_mst (".$field_array1.") VALUES ".$data_array1; disconnect($con);die;
		if($db_type==0)
		{
			$rID=sql_insert("pro_garments_production_mst",$field_array1,$data_array1,1);
		}
		else
		{
			$rID=execute_query($data_array1);	
		}
		// echo "10**INSERT INTO pro_garments_production_dtls (".$field_array.") VALUES ".$data_array; disconnect($con);
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
					//	echo "10**".str_replace("'","",$hidden_po_break_down_id);
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
					//echo "10**".str_replace("'","",$hidden_po_break_down_id);
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
				//	echo "10**".str_replace("'","",$hidden_po_break_down_id);
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
				//	echo "10**".str_replace("'","",$hidden_po_break_down_id);
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

		$prv_iss_data=sql_select( "SELECT sum(production_quantity) as PRODUCTION_QUANTITY from pro_garments_production_mst where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name and production_type=82  and status_active=1 and is_deleted=0 " );

		$prv_rcv_data=sql_select( "SELECT sum(production_quantity) as PRODUCTION_QUANTITY from pro_garments_production_mst where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name and production_type=81 and id<>$txt_mst_id  and status_active=1 and is_deleted=0 " );

		if(count($prv_iss_data)>0)
		{
			$prv_iss_qnty=$prv_iss_data[0]['PRODUCTION_QUANTITY'];
			$total_finishing_qty=str_replace("'","",$txt_finishing_qty)+$prv_rcv_data[0]['PRODUCTION_QUANTITY'];
			if($prv_iss_qnty>$total_finishing_qty)
			{
				echo "555**Receive Qnty Can't be Updated Less than Issue Qnty";disconnect($con);die;
			}
		}

		$field_array_delivery = "company_id*production_source*floor_id*remarks*working_company_id*working_location_id*working_floor_id*location_id*challan_id*challan_no*updated_by*update_date";
        $data_array_delivery = "".$cbo_company_id."*".$cbo_source."*".$cbo_floor."*".$txt_remark."*".$cbo_finish_company."*".$cbo_finish_location."*".$cbo_finish_floor."*".$cbo_location."*".$txt_challan_id."*".$txt_challan_no."*".$user_id."*'".$pc_date_time."'";
				
		// pro_garments_production_mst table data entry here 
		
 		$field_array1="challan_id*production_source*serving_company*location*production_quantity*entry_break_down_type*carton_qty*store_id*floor_id*room_id*rack_id*shelf_id*total_produced*yet_to_produced*challan_id*updated_by*update_date";
		
		$data_array1="".$txt_challan_id."*".$cbo_source."*".$cbo_finish_company."*".$cbo_location."*".$txt_finishing_qty."*".$sewing_production_variable."*".$txt_carton_qty."*".$cbo_store_name."*".$cbo_floor."*".$cbo_room."*".$txt_rack."*".$txt_shelf."*".$txt_cumul_rcv_qty."*".$txt_yet_to_rcv."*".$txt_challan_id."*".$user_id."*'".$pc_date_time."'";		
		
		if(str_replace("'","",$sewing_production_variable)!=1 && str_replace("'","",$txt_mst_id)!='' ) //  not gross level
		{
			$dtlsData = sql_select("SELECT a.color_size_break_down_id,
				sum(CASE WHEN a.production_type='$qty_source' and a.delivery_mst_id='$txt_challan_id' then a.production_qnty ELSE 0 END) as production_qnty,
				sum(CASE WHEN a.production_type=81 and a.challan_id='$txt_challan_id' then a.production_qnty ELSE 0 END) as cur_production_qnty 
				from pro_garments_production_dtls a,pro_garments_production_mst b 
				where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id=$hidden_po_break_down_id and b.item_number_id=$cbo_item_name and b.country_id=$cbo_country_name and a.color_size_break_down_id<>0 and a.production_type in($qty_source,81) and b.id !=$txt_mst_id 
				group by a.color_size_break_down_id");
			$color_pord_data=array();							
			foreach($dtlsData as $row)
			{				  
				$color_pord_data[$row[csf("color_size_break_down_id")]]=$row[csf('production_qnty')]-$row[csf("cur_production_qnty")];
			}
			
 			$field_array="id, mst_id, production_type, color_size_break_down_id, production_qnty, delivery_mst_id,challan_id";
			
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
					
					$nxt_process_qty=$nxtQtyArr[$colSizeID_arr[$colorSizeNumberIDArr[0]]]['iss_rec_ret']*1;
					$current_priv_qty=($nxtQtyArr[$colSizeID_arr[$colorSizeNumberIDArr[0]]]['recQty']*1)+($colorSizeNumberIDArr[1]*1);
					
					if( $nxt_process_qty > $current_priv_qty)
					{
						echo "36**Finish Gmts Receive Qty is not less then Finish Gmts Receive or Return.";
						//check_table_status( 160,0);
						disconnect($con);
						die;
					}
					
					$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",  "pro_garments_production_dtls", $con );
					if($j==0)$data_array = "(".$dtls_id.",".$txt_mst_id.",81,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."','".$txt_system_id."',".$txt_challan_id.")";
					else $data_array .= ",(".$dtls_id.",".$txt_mst_id.",81,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."','".$txt_system_id."',".$txt_challan_id.")";
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
					
					$nxt_process_qty=$nxtQtyArr[$colSizeID_arr[$index]]['iss_rec_ret']*1;
					$current_priv_qty=($nxtQtyArr[$colSizeID_arr[$index]]['recQty']*1)+($colorSizeValue*1);
					
					//echo "10**".$nxt_process_qty.'-'.$nxtQtyArr[$colSizeID_arr[$index]]['recQty'].'-'.$colorSizeValue; die;
					if( $nxt_process_qty > $current_priv_qty)
					{
						echo "36**Finish Gmts Receive Qty is not less then Finish Gmts Receive or Return.";
						//check_table_status( 160,0);
						disconnect($con);
						die;
					}

					$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",   "pro_garments_production_dtls", $con );
					if($j==0)$data_array = "(".$dtls_id.",".$txt_mst_id.",81,'".$colSizeID_arr[$index]."','".$colorSizeValue."','".$txt_system_id."',".$txt_challan_id.")";
					else $data_array .= ",(".$dtls_id.",".$txt_mst_id.",81,'".$colSizeID_arr[$index]."','".$colorSizeValue."','".$txt_system_id."',".$txt_challan_id.")";
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
					//echo "10**".str_replace("'","",$hidden_po_break_down_id);
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
					//echo "10**".str_replace("'","",$hidden_po_break_down_id);
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
					//echo "10**".str_replace("'","",$hidden_po_break_down_id);
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
		
		$prv_iss_data=sql_select( "SELECT sum(production_quantity) as PRODUCTION_QUANTITY from pro_garments_production_mst where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name and production_type=82  and status_active=1 and is_deleted=0 " );

		$prv_rcv_data=sql_select( "SELECT sum(production_quantity) as PRODUCTION_QUANTITY from pro_garments_production_mst where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name and production_type=81 and id<>$txt_mst_id  and status_active=1 and is_deleted=0 " );

		if(count($prv_iss_data)>0)
		{
			$prv_iss_qnty=$prv_iss_data[0]['PRODUCTION_QUANTITY'];
			$total_finishing_qty=str_replace("'","",$txt_finishing_qty)+$prv_rcv_data[0]['PRODUCTION_QUANTITY'];
			if($prv_iss_qnty>$total_finishing_qty)
			{
				echo "555**Receive Qnty Can't be Deleted Less than Issue Qnty";disconnect($con);die;
			}
		}

		$challanrData=sql_select("SELECT id from pro_garments_production_mst where delivery_mst_id=$txt_system_id and status_active=1 and is_deleted=0");
		if(count($challanrData)==1){
			$challanrID = sql_delete("pro_gmts_delivery_mst","updated_by*update_date*status_active*is_deleted","".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1",'id',$txt_system_id,1);
			$resetLoad=1;
		}
		else{
			$challanrID = 1;
			$resetLoad=2;
		}		 
 		$rID = sql_delete("pro_garments_production_mst","updated_by*update_date*status_active*is_deleted","".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1",'id',$txt_mst_id,1);

		$dtlsrID = sql_delete("pro_garments_production_dtls","status_active*is_deleted","0*1",'mst_id',$txt_mst_id,1);
		// echo "10**".$rID."**".$dtlsrID."**".$challanrID;die;
 		if($db_type==0)
		{
			if($rID && $dtlsrID && $challanrID)
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'","",$hidden_po_break_down_id)."**".str_replace("'","",$txt_system_no)."**".$txt_system_id."**".$resetLoad; 
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
				echo "2**".str_replace("'","",$hidden_po_break_down_id)."**".str_replace("'","",$txt_system_no)."**".$txt_system_id."**".$resetLoad;
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

?>