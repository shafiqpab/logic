<?
session_start();
include('../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
$user_level = $_SESSION['logic_erp']['user_level'];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//========== user credential start ========
$userCredential = sql_select("SELECT WORKING_UNIT_ID,unit_id as company_id, store_location_id, item_cate_id, company_location_id as location_id FROM user_passwd where id=$user_id");
$company_id = $userCredential[0][csf('company_id')];
$working_unit_id = $userCredential[0][csf('WORKING_UNIT_ID')];
$store_location_id = $userCredential[0][csf('store_location_id')];
$item_cate_id = $userCredential[0][csf('item_cate_id')];
$location_id = $userCredential[0][csf('location_id')];

$company_credential_cond = "";

if ($company_id >0) {
    $company_credential_cond = " and comp.id in($company_id)";
}
$working_credential_cond = "";

if ($working_unit_id >0) {
    $working_credential_cond = " and comp.id in($working_unit_id)";
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

// $user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");


//========== user credential end ==========

//------------------------------------------------------------------------------------------------------
// $country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name"  );
$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
// $prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');



if ($action=="load_variable_settings")
{
	echo "$('#sewing_production_variable').val(0);\n";
	echo "$('#sewing_production_variable_rej').val(0);\n";

	$sql = "SELECT sewing_production,production_entry,auto_update,is_control,variable_list from variable_settings_production where company_name=$data and variable_list in(1,23,28) and status_active=1";
	$res = sql_select($sql);
	foreach ($res as $v)
	{
		if($v['VARIABLE_LIST']==1)
		{
			echo "$('#sewing_production_variable').val(".$v[csf("sewing_production")].");\n";
			echo "$('#styleOrOrderWisw').val(".$v[csf("production_entry")].");\n";
		}
		else if($v['VARIABLE_LIST']==23)
		{
			if($v['AUTO_UPDATE']!=1) $v['AUTO_UPDATE']=0;
			echo "document.getElementById('prod_reso_allo').value=".$v['AUTO_UPDATE'].";\n";
		}
		else if($v['VARIABLE_LIST']==28)
		{
			echo "$('#sewing_production_variable_rej').val(".$v[csf("sewing_production")].");\n";
			echo "$('#txt_reject_qnty').removeAttr('readonly','readonly');\n";
			if($v[csf("sewing_production")]==3) //Color and Size
			{
					echo "$('#txt_reject_qnty').attr('readonly','readonly');\n";
			}
			else
			{
				echo "$('#txt_reject_qnty').removeAttr('readonly','readonly');\n";
			}
		}
	}


	/* echo "$('#sewing_production_variable').val(0);\n";
	$sql_result = sql_select("select sewing_production,production_entry from variable_settings_production where company_name=$data and variable_list=1 and status_active=1");
 	foreach($sql_result as $result)
	{
		echo "$('#sewing_production_variable').val(".$result[csf("sewing_production")].");\n";
		echo "$('#styleOrOrderWisw').val(".$result[csf("production_entry")].");\n";
	}

	$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name =$data and variable_list=23 and is_deleted=0 and status_active=1");
	if($prod_reso_allo!=1) $prod_reso_allo=0;
	echo "document.getElementById('prod_reso_allo').value=".$prod_reso_allo.";\n";

	echo "$('#sewing_production_variable_rej').val(0);\n";
	$sql_result_rej = sql_select("select sewing_production from variable_settings_production where company_name=$data and variable_list=28 and status_active=1");
 	foreach($sql_result_rej as $result)
	{
		echo "$('#sewing_production_variable_rej').val(".$result[csf("sewing_production")].");\n";
		echo "$('#txt_reject_qnty').removeAttr('readonly','readonly');\n";
		if($result[csf("sewing_production")]==3) //Color and Size
		{
				echo "$('#txt_reject_qnty').attr('readonly','readonly');\n";
		}
		else
		{
			echo "$('#txt_reject_qnty').removeAttr('readonly','readonly');\n";
		}
	} */
	$variable_is_control=return_field_value("is_control","variable_settings_production","company_name=$data and variable_list=33 and page_category_id=29","is_control");
	echo "document.getElementById('variable_is_controll').value='".$variable_is_control."';\n";

 	exit();
}

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location", 167, "SELECT id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' $location_credential_cond  order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/sewing_output_controller', this.value, 'load_drop_down_floor', 'floor_td' );load_drop_down( 'requires/sewing_output_controller', document.getElementById('cbo_location').value+'_'+document.getElementById('prod_reso_allo').value+'_'+document.getElementById('txt_sewing_date').value+'_'+document.getElementById('cbo_sewing_company').value, 'load_drop_down_sewing_output_line', 'sewing_line_td' ); fnc_line_disable_enable(this.value);" );
	exit();
}

if ($action=="load_drop_down_floor")
{
	echo create_drop_down( "cbo_floor", 170, "SELECT id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' and production_process in (5) order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "load_drop_down( 'requires/sewing_output_controller', this.value+'_'+document.getElementById('cbo_location').value+'_'+document.getElementById('prod_reso_allo').value+'_'+document.getElementById('txt_sewing_date').value+'_'+document.getElementById('cbo_sewing_company').value, 'load_drop_down_sewing_line_floor', 'sewing_line_td' );",0 );
	exit();
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "txt_search_common", 140, "SELECT buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "");
	exit();
}

if($action=="line_disable_enable")
{
	if($data==1) echo "disable_enable_fields('cbo_sewing_line',0,'','');\n";
	else
	{
		echo "$('#cbo_sewing_line').val(0);\n";
		echo "disable_enable_fields('cbo_sewing_line',1,'','');\n";

	}
	exit();
}

if($action=="load_drop_down_sewing_output")
{
	$explode_data = explode("**",$data);
	$data = $explode_data[0];
	$selected_company = $explode_data[1];

	if($data==3)
	{
		if($db_type==0)
		{
			echo create_drop_down( "cbo_sewing_company", 170, "SELECT id,supplier_name from lib_supplier where status_active=1 and is_deleted=0 and find_in_set(22,party_type) order by supplier_name","id,supplier_name", 1, "--- Select ---", $selected, "get_php_form_data(this.value+'**'+$data+'**'+$('#hidden_po_break_down_id').val()+'**'+$('#cbo_item_name').val()+'**'+$('#cbo_country_name').val(),'display_bl_qnty','requires/sewing_output_controller');fnc_workorder_search(this.value);",0,0 );
		}
		else
		{
			echo create_drop_down( "cbo_sewing_company", 170, "SELECT a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=22 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "--- Select ---", $selected, "get_php_form_data(this.value+'**'+$data+'**'+$('#hidden_po_break_down_id').val()+'**'+$('#cbo_item_name').val()+'**'+$('#cbo_country_name').val(),'display_bl_qnty','requires/sewing_output_controller');fnc_workorder_search(this.value);",0,0 );
		}
	}
	else if($data==1)
	{
 		echo create_drop_down( "cbo_sewing_company", 170, "SELECT id,company_name from lib_company comp where is_deleted=0 and status_active=1 $working_credential_cond order by company_name","id,company_name", 1, "--- Select ---", "",  "get_php_form_data(this.value+'**'+$data+'**'+$('#hidden_po_break_down_id').val()+'**'+$('#cbo_item_name').val()+'**'+$('#cbo_country_name').val(), 'display_bl_qnty', 'requires/sewing_output_controller');load_drop_down( 'requires/sewing_output_controller', this.value, 'load_drop_down_location', 'location_td' );",0,0 );

	}
 	else
	{
		echo create_drop_down( "cbo_sewing_company", 170, $blank_array,"", 1, "--- Select ---", $selected, "",0,0 );
	}
	//echo "select id,company_name from lib_company comp where is_deleted=0 and status_active=1 $working_credential_cond order by company_name";

	exit();
}

if ($action=="load_drop_down_workorder")
{
	$explode_data = explode("_",$data);

	$sql = "SELECT a.id,a.sys_number from piece_rate_wo_mst a, piece_rate_wo_dtls b where a.id=b.mst_id and b.order_id=".$explode_data[2]." and a.company_id=$explode_data[0]  and a.rate_for=30 and a.service_provider_id=$explode_data[1]   and a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 group by a.id,a.sys_number order by a.id";
	//echo $sql;
	echo create_drop_down( "cbo_work_order", 170, $sql,"id,sys_number", 1, "-- Select Work Order --", $selected, "fnc_workorder_rate('$data',this.value)",0 );
	exit();
}

if($action=="populate_workorder_rate")
{
	$data=explode("_",$data);
	$po_break_down_id=$data[2];
	$company_id=$data[0];
	$suppplier=$data[1];
	$sql = sql_select("SELECT a.id,a.sys_number,a.currence,a.exchange_rate,sum(b.avg_rate) as rate,b.uom from piece_rate_wo_mst a, piece_rate_wo_dtls b where a.id=".$data[3]."  and a.id=b.mst_id and b.order_id=".$po_break_down_id." and a.company_id=$company_id and a.service_provider_id=$suppplier and a.rate_for=30   and a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 group by a.id,a.sys_number,a.currence ,a.exchange_rate,b.uom order by a.id");
	//echo $sql;
	if($sql[0][csf('uom')]==2)
	{
		$rate=$sql[0][csf('rate')]/12;
	}
	else
	{
		$rate=$sql[0][csf('rate')];
	}
	echo "$('#hidden_currency_id').val('".$sql[0][csf('currence')]."');\n";
	echo "$('#hidden_exchange_rate').val('".$sql[0][csf('exchange_rate')]."');\n";
	echo "$('#hidden_piece_rate').val('".$rate."');\n";
	echo "$('#workorder_rate_id').text('');\n";
	$rate_string='';
	$rate_string=$rate." ".$currency[$sql[0][csf('currence')]];
	if(trim($rate_string)!="")
	{
		$rate_string="Work Order Rate ".$rate_string." /Pcs";
		echo "$('#workorder_rate_id').text('".$rate_string."');\n";
	}
	exit();
}

if($action=="display_bl_qnty")
{
	$explode_data = explode("**",$data);
	$sewing_company=$explode_data[0];
	$source=$explode_data[1];
	$po_break_down_id=$explode_data[2];
	$item_id=$explode_data[3];
	$country_id=$explode_data[4];

	$dataArray=sql_select("SELECT SUM(CASE WHEN a.production_type=4 and b.production_type=4 THEN b.production_qnty END) as totalinput,SUM(CASE WHEN a.production_type=5 and b.production_type=5 THEN b.production_qnty ELSE 0 END) as totalsewing from pro_garments_production_mst a,pro_garments_production_dtls b WHERE a.id= b.mst_id and a.po_break_down_id='$po_break_down_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.production_source='$source' and a.serving_company='$sewing_company' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	foreach($dataArray as $row)
	{
		echo "$('#txt_input_quantity').val('".$row['TOTALINPUT']."');\n";
		echo "$('#txt_cumul_sewing_qty').val('".$row['TOTALSEWING']."');\n";
		$yet_to_produced = $row['TOTALINPUT']-$row['TOTALSEWING'];
		echo "$('#txt_yet_to_sewing').val('".$yet_to_produced."');\n";
	}

	exit();
}

if($action=="load_drop_down_sewing_output_line")
{
	$explode_data = explode("_",$data);
	$location = $explode_data[0];
	$prod_reso_allocation = $explode_data[1];
	$txt_sewing_date = $explode_data[2];
	if($location)
		$loc_cond=" and a.location_id='$location'";

	$wo_company_id = $explode_data[3];
	$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name =$wo_company_id and variable_list=23 and is_deleted=0 and status_active=1");
	if($prod_reso_allo==1)
	{
		$line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"  );
		$line_array=array();

		if($txt_sewing_date=="")
		{
			$line_data=sql_select("select id, line_number from prod_resource_mst where is_deleted=0 and location_id='$location'");
		}
		else
		{
			if($db_type==0)
			{
				$line_data=sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and b.pr_date='".change_date_format($txt_sewing_date,'yyyy-mm-dd')."' and a.location_id='$location' and a.is_deleted=0 and b.is_deleted=0 group by a.id");
			}
			if($db_type==2 || $db_type==1)
			{
				$line_data=sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and b.pr_date='".date("j-M-Y",strtotime($txt_sewing_date))."' and a.is_deleted=0 and b.is_deleted=0  $loc_cond  group by a.id, a.line_number");

			}

		}
		$line_merge=9999;
		foreach($line_data as $row)
		{
			$line='';
			$line_number=explode(",",$row[csf('line_number')]);
			foreach($line_number as $val)
			{
				if(count($line_number)>1)
				{
					$line_merge++;
					$new_arr[$line_merge]=$row[csf('id')];
				}
				else
					$new_arr[$val]=$row[csf('id')];

				if($line=='') $line=$line_library[$val]; else $line.=",".$line_library[$val];
			}
			$line_array[$row[csf('id')]]=$line;
		}
		ksort($new_arr);
		foreach($new_arr as $key=>$v)
		{
			$line_array_new[$v]=$line_array[$v];
		}
		echo create_drop_down( "cbo_sewing_line", 110,$line_array_new,"", 1, "--- Select ---", $selected, "fn_generate_color_size_break_down(this.value)",0,0 );
	}
	else
	{
		echo create_drop_down( "cbo_sewing_line", 110, "select id,line_name from lib_sewing_line where is_deleted=0 and status_active=1 and location_name='$location' and location_name!=0 order by line_name","id,line_name", 1, "Select Line", $selected, "fn_generate_color_size_break_down(this.value)" );
	}
	exit();
}
if($action=="load_drop_down_sewing_line_floor")
{
	$explode_data = explode("_",$data);
	$prod_reso_allocation = $explode_data[2];
	$txt_sewing_date = $explode_data[3];
	$wo_company_id = $explode_data[4];
	$is_update_mood = $explode_data[5];

	$cond="";
	$prod_reso_allo=return_field_value("auto_update","variable_settings_production","company_name =$wo_company_id and variable_list=23 and is_deleted=0 and status_active=1");
	if($prod_reso_allo==1)
	{
		$line_library=return_library_array( "select id,line_name from lib_sewing_line", "id", "line_name"  );
		$line_array=array();
		if($txt_sewing_date=="")
		{
			if( $explode_data[1] ) $cond.= " and location_id= $explode_data[1]";
			if( $explode_data[0] ) $cond.= " and floor_id= $explode_data[0]";

			$line_data=sql_select("select id, line_number from prod_resource_mst where is_deleted=0 $cond");
		}
		else
		{
			if( $explode_data[1]) $cond.= " and a.location_id= $explode_data[1]";
			if( $explode_data[0]) $cond.= " and a.floor_id= $explode_data[0]";

			if($db_type==0)
			{
				$line_data=sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and b.pr_date='".change_date_format($txt_sewing_date,'yyyy-mm-dd')."' and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id  order by a.id, a.prod_resource_num");
			}
			else if($db_type==2 || $db_type==1)
			{
				$line_data=sql_select("select a.id, a.line_number,a.prod_resource_num from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and b.pr_date='".date("j-M-Y",strtotime($txt_sewing_date))."' and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id,a.prod_resource_num, a.line_number  order by a.id, a.prod_resource_num");
			}
		}
		$line_merge=9999;
		foreach($line_data as $row)
		{
			$line='';
			$line_number=explode(",",$row[csf('line_number')]);
			foreach($line_number as $val)
			{
				if(count($line_number)>1)
				{
					$line_merge++;
					$new_arr[$line_merge]=$row[csf('id')];
				}
				else
					$new_arr[$val]=$row[csf('id')];
				if($line=='') $line=$line_library[$val]; else $line.=",".$line_library[$val];
			}
			$line_array[$row[csf('id')]]=$line;
		}

		ksort($new_arr);
		foreach($new_arr as $key=>$v)
		{
			$line_array_new[$v]=$line_array[$v];
		}
		//echo create_drop_down( "cbo_sewing_line", 110,$line_array_new,"", 1, "--- Select ---", $selected, "",0,0 );
		echo create_drop_down( "cbo_sewing_line", 110,$line_array_new,"", 1, "--- Select ---", $selected, "fn_generate_color_size_break_down(this.value)",0,0 );
	}
	else
	{
		if( $explode_data[0]==0 && $explode_data[1]!=0 ) $cond = " and location_name= $explode_data[1]";
		if( $explode_data[0]!=0 ) $cond = " and floor_name= $explode_data[0]";

		echo create_drop_down( "cbo_sewing_line", 110, "select id,line_name from lib_sewing_line where  is_deleted=0 and status_active=1 and floor_name!=0 $cond order by id, line_name","id,line_name", 1, "--- Select ---", $selected, "fn_generate_color_size_break_down(this.value)",0,0 );
	}
	exit();
}
if($action=="load_drop_down_sewing_line_floor_oldd")
{
	$explode_data = explode("_",$data);
	$prod_reso_allocation = $explode_data[2];
	$txt_sewing_date = $explode_data[3];
	$cond="";

	if($prod_reso_allocation==1)
	{
		$line_library=return_library_array( "select id,line_name from lib_sewing_line order by sewing_line_serial", "id", "line_name"  );
		$line_array=array();
		if($txt_sewing_date=="")
		{
			if( $explode_data[0]==0 && $explode_data[1]!=0 ) $cond = " and location_id= $explode_data[1]";
			if( $explode_data[0]!=0 ) $cond = " and floor_id= $explode_data[0]";

			$line_data=sql_select("select id, line_number from prod_resource_mst where is_deleted=0 $cond");
		}
		else
		{
			if( $explode_data[0]==0 && $explode_data[1]!=0 ) $cond = " and a.location_id= $explode_data[1]";
			if( $explode_data[0]!=0 ) $cond = " and a.floor_id= $explode_data[0]";

			if($db_type==0)
			{
				$line_data=sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and b.pr_date='".change_date_format($txt_sewing_date,'yyyy-mm-dd')."' and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id");
			}
			if($db_type==2 || $db_type==1)
			{
				$line_data=sql_select("select a.id, a.line_number from prod_resource_mst a, prod_resource_dtls b where a.id=b.mst_id and b.pr_date='".date("j-M-Y",strtotime($txt_sewing_date))."' and a.is_deleted=0 and b.is_deleted=0 $cond group by a.id, a.line_number, a.prod_resource_num  order by a.prod_resource_num");

			}
		}
		$line_merge=9999;
		foreach($line_data as $row)
		{
			$line='';
			$line_number=explode(",",$row[csf('line_number')]);
			foreach($line_number as $val)
			{
				if(count($line_number)>1)
				{
					$line_merge++;
					$new_arr[$line_merge]=$row[csf('id')];
				}
				else
					$new_arr[$val]=$row[csf('id')];

				if($line=='') $line=$line_library[$val]; else $line.=",".$line_library[$val];
			}

			$line_array[$row[csf('id')]]=$line;
		}
		ksort($new_arr);
		foreach($new_arr as $key=>$v)
		{
			$line_array_new[$v]=$line_array[$v];
		}
		echo create_drop_down( "cbo_sewing_line", 110,$line_array_new,"", 1, "--- Select ---", $selected, "",0,0 );
	}
	else
	{
		if( $explode_data[0]==0 && $explode_data[1]!=0 ) $cond = " and location_name= $explode_data[1]";
		if( $explode_data[0]!=0 ) $cond = " and floor_name= $explode_data[0]";

		echo create_drop_down( "cbo_sewing_line", 110, "select id,line_name from lib_sewing_line where  is_deleted=0 and status_active=1 and floor_name!=0 $cond order by line_name","id,line_name", 1, "--- Select ---", $selected, "",0,0 );
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
            	$("#company_search_by").val(<?php echo $_REQUEST['company'] ?>);
        });

		function search_populate(str)
		{
			//alert(str);
			if(str==0)
			{
				document.getElementById('search_by_th_up').innerHTML="Order No";
				document.getElementById('search_by_td').innerHTML='<input	type="text" onkeydown="getActionOnEnter(event)"	name="txt_search_common" style="width:130px " class="text_boxes" id="txt_search_common"	value=""  />';
			}
			else if(str==1)
			{
				document.getElementById('search_by_th_up').innerHTML="Style Ref. Number";
				document.getElementById('search_by_td').innerHTML='<input onkeydown="getActionOnEnter(event)"	type="text"	name="txt_search_common" style="width:130px " class="text_boxes" id="txt_search_common"	value=""  />';
			}
			else if(str==3)
			{
				document.getElementById('search_by_th_up').innerHTML="Job No";
				document.getElementById('search_by_td').innerHTML='<input type="text" name="txt_search_common" style="width:130px " class="text_boxes" id="txt_search_common"	value=""  />';
			}
			else if(str==4)
			{
				document.getElementById('search_by_th_up').innerHTML="Actual PO No";
				document.getElementById('search_by_td').innerHTML='<input type="text" name="txt_search_common" style="width:130px " class="text_boxes" id="txt_search_common"	value=""  />';
			}
			else if(str==5)
			{
				document.getElementById('search_by_th_up').innerHTML="File No";
				document.getElementById('search_by_td').innerHTML='<input type="text" name="txt_search_common" style="width:130px " class="text_boxes" id="txt_search_common"	value=""  />';
			}
			else if(str==6)
			{
				document.getElementById('search_by_th_up').innerHTML="Internal Ref.";
				document.getElementById('search_by_td').innerHTML='<input type="text" name="txt_search_common" style="width:130px " class="text_boxes" id="txt_search_common"	value=""  />';
			}
			else if(str==7)
			{
				document.getElementById('search_by_th_up').innerHTML="Booking No";
				document.getElementById('search_by_td').innerHTML='<input type="text" name="txt_search_common" style="width:130px " class="text_boxes" id="txt_search_common"	value=""  />';
			}
			else //if(str==2)
			{
				load_drop_down( 'sewing_output_controller',document.getElementById('company_search_by').value,'load_drop_down_buyer', 'search_by_td' );
				document.getElementById('search_by_th_up').innerHTML="Select Buyer Name";
			}

		}

	function js_set_value(id,item_id,po_qnty,plan_qnty,country_id,ship_date, txt_sewing_date_disabled)
	{
		//alert("Alert...");
		$("#hidden_mst_id").val(id);
		$("#hidden_grmtItem_id").val(item_id);
		$("#hidden_po_qnty").val(po_qnty);
		$("#hidden_country_id").val(country_id);
		$("#hidden_company_id").val(document.getElementById('company_search_by').value);
		$("#hid_country_ship_date").val(ship_date);
		$("#txt_sewing_date_disabled").val(txt_sewing_date_disabled);
  		parent.emailwindow.hide();
 	}

    </script>
    </head>
    <body>
    <div align="center" style="width:100%;" >
    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="780" cellspacing="0" cellpadding="0" class="rpt_table" align="center" border="1" rules="all">
    		<tr>
        		<td align="center" width="100%">
                    <table ellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                   		 <thead>
							<tr>
									<th colspan="9"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,"", 1, "-- Select --", 4, "",0 ); ?></th>
							</tr>
                        	<th width="130" class="must_entry_caption">Company</th>
                        	<th width="130">Search By</th>
                        	<th width="130" align="center" id="search_by_th_up">Enter Order Number</th>
                        	<th width="200" class="must_entry_caption">Date Range</th>
                        	<th width="80"><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" /></th>
                    	</thead>
        				<tr class="general">
        					<td><? echo create_drop_down( "company_search_by", 130, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name", 1, "-- Select --", $selected, "", 0 ); ?></td>
                    		<td>
								<?
									//$searchby_arr=array(0=>"Order No",1=>"Style Ref. Number",2=>"Buyer Name");
									$searchby_arr=array(0=>"Order No",1=>"Style Ref. Number",2=>"Buyer Name",3=>"Job No",4=>"Actual PO No",5=>"File No",6=>"Internal Ref",7=>"Booking No");
									echo create_drop_down( "txt_search_by", 130, $searchby_arr,"", 1, "-- Select --", $selected, "search_populate(this.value)",0 );
                                ?>
                    		</td>
                   			<td id="search_by_td"><input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" onKeyDown="if (event.keyCode == 13) document.getElementById('btn_show').click()" /></td>
                    		<td>
                            	<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px"> To
					  			<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
					 		</td>
            		 		<td><input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('company_search_by').value+'_'+<? echo $garments_nature; ?>+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_string_search_type').value, 'create_po_search_list_view', 'search_div', 'sewing_output_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:100px;" /></td>
        				</tr>
             		</table>
          		</td>
        	</tr>
        	<tr>
            	<td  align="center" valign="middle">
				<? echo create_drop_down( "cbo_year_selection", 70, $year,"", 1, "-- Select --", date('Y'), "",0 ); echo load_month_buttons();  ?>
                    <input type="hidden" id="hidden_mst_id">
                    <input type="hidden" id="hidden_grmtItem_id">
                    <input type="hidden" id="hidden_po_qnty">
                    <input type="hidden" id="hidden_country_id">
                     <input type="hidden" id="hidden_company_id">
                     <input type="hidden" id="txt_sewing_date_disabled">
					 <input type="hidden" id="hid_country_ship_date">
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
	exit();
}

if($action=="create_po_search_list_view")
{
 	$ex_data = explode("_",$data);
	$txt_search_by = $ex_data[0];
	$txt_search_common = $ex_data[1];
	$txt_date_from = $ex_data[2];
	$txt_date_to = $ex_data[3];
	$company = $ex_data[4];
	$garments_nature = $ex_data[5];
 	$year = $ex_data[6];
	$search_type =$ex_data[7];
	if($company == 0)
	{
		//print_r ($data);die;
		echo "Please Select Company First."; die;
	}

	if($txt_search_common =="" && $txt_date_from =="" && $txt_date_to =="")
	{
		echo "Please Select Search By OR Date Range Field."; die;
	}
	if($txt_date_from !="" && $txt_date_to !="")
	{
		$tot_days = datediff('d',$txt_date_from,$txt_date_to);
		if($tot_days>93)// max 3 month
		{
			echo "<div style='color:red;font-size:16px;font-weight:bold;text-align:center;'>Invalid Date Range.</div>"; die;
		}
	}

 	$variable_sql=sql_select("SELECT sewing_production from variable_settings_production where company_name='$company' and variable_list=1 and status_active=1");
	$sewing_level=$variable_sql[0][csf("sewing_production")];
	$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name"  );

	$sql_cond="";
	if ($search_type==4){
		if(trim($txt_search_common)!="")
		{
			if(trim($txt_search_by)==0)
				$sql_cond = " and upper(b.po_number) like upper('%".trim($txt_search_common)."%')";
			else if(trim($txt_search_by)==1)
				$sql_cond = " and upper(a.style_ref_no) like upper('%".trim($txt_search_common)."%')";
			else if(trim($txt_search_by)==2)
				$sql_cond = " and a.buyer_name='$txt_search_common'";
			else if(trim($txt_search_by)==3)
				$sql_cond = " and upper(a.job_no) like upper('%".trim($txt_search_common)."')";
			else if(trim($txt_search_by)==4)
				$sql_cond = " and upper(b.po_number_acc) like upper('%".trim($txt_search_common)."%')";
			else if(trim($txt_search_by)==5)
				$sql_cond = " and upper(b.file_no) like upper('%".trim($txt_search_common)."%')";
			else if(trim($txt_search_by)==6)
				$sql_cond = " and upper(b.grouping) like upper('%".trim($txt_search_common)."%')";
			else if(trim($txt_search_by)==7)
			{
				$order_id_arr=return_library_array( "SELECT po_break_down_id,po_break_down_id from WO_BOOKING_DTLS where upper(booking_no) like upper('%".trim($txt_search_common)."%')", "po_break_down_id", "po_break_down_id");
				$sql_cond = where_con_using_array($order_id_arr,0,"b.id");
			}
		}
	}
	else if ($search_type==1){
		if(trim($txt_search_common)!="")
		{
			if(trim($txt_search_by)==0)
				$sql_cond = " and b.po_number ='$txt_search_common'";
			else if(trim($txt_search_by)==1)
				$sql_cond = " and a.style_ref_no ='$txt_search_common'";
			else if(trim($txt_search_by)==2)
				$sql_cond = " and a.buyer_name='$txt_search_common'";
			else if(trim($txt_search_by)==3)
				$sql_cond = " and a.job_no='$txt_search_common'";
			else if(trim($txt_search_by)==4)
				$sql_cond = " and b.po_number_acc='$txt_search_common'";
			else if(trim($txt_search_by)==5)
				$sql_cond = " and b.file_no='$txt_search_common'";
			else if(trim($txt_search_by)==6)
				$sql_cond = " and b.grouping='$txt_search_common'";
			else if(trim($txt_search_by)==7)
			{
				$order_id_arr=return_library_array( "SELECT po_break_down_id,po_break_down_id from WO_BOOKING_DTLS where booking_no='$txt_search_common'", "po_break_down_id", "po_break_down_id");
				$sql_cond = where_con_using_array($order_id_arr,0,"b.id");

			}
		}
	}
	else if ($search_type==2){
		if(trim($txt_search_common)!="")
		{
			if(trim($txt_search_by)==0)
				$sql_cond = " and upper(b.po_number) like upper('".trim($txt_search_common)."%')";
			else if(trim($txt_search_by)==1)
				$sql_cond = " and upper(a.style_ref_no) like upper('".trim($txt_search_common)."%')";
			else if(trim($txt_search_by)==2)
				$sql_cond = " and a.buyer_name='$txt_search_common'";
			else if(trim($txt_search_by)==3)
				$sql_cond = " and upper(a.job_no) like upper('".trim($txt_search_common)."%')";
			else if(trim($txt_search_by)==4)
				$sql_cond = " and upper(b.po_number_acc) like upper('".trim($txt_search_common)."%')";
			else if(trim($txt_search_by)==5)
				$sql_cond = " and upper(b.file_no) like upper('".trim($txt_search_common)."%')";
			else if(trim($txt_search_by)==6)
				$sql_cond = " and upper(b.grouping) like upper('".trim($txt_search_common)."%')";
			else if(trim($txt_search_by)==7)
			{
				$order_id_arr=return_library_array( "SELECT po_break_down_id,po_break_down_id from WO_BOOKING_DTLS where upper(booking_no) like upper('".trim($txt_search_common)."%')", "po_break_down_id", "po_break_down_id");
				$sql_cond = where_con_using_array($order_id_arr,0,"b.id");

			}
		}
	}
	else if ($search_type==3){
		if(trim($txt_search_common)!="")
		{
			if(trim($txt_search_by)==0)
				$sql_cond = " and upper(b.po_number) like upper('%".trim($txt_search_common)."')";
			else if(trim($txt_search_by)==1)
				$sql_cond = " and upper(a.style_ref_no) like upper('%".trim($txt_search_common)."')";
			else if(trim($txt_search_by)==2)
				$sql_cond = " and a.buyer_name='$txt_search_common'";
			else if(trim($txt_search_by)==3)
				$sql_cond = " and upper(a.job_no) like upper('%".trim($txt_search_common)."')";
			else if(trim($txt_search_by)==4)
				$sql_cond = " and upper(b.po_number_acc) like upper('%".trim($txt_search_common)."')";
			else if(trim($txt_search_by)==5)
				$sql_cond = " and upper(b.file_no) like upper('%".trim($txt_search_common)."')";
			else if(trim($txt_search_by)==6)
				$sql_cond = " and upper(b.grouping) like upper('%".trim($txt_search_common)."')";
			else if(trim($txt_search_by)==7)
			{
				$order_id_arr=return_library_array( "SELECT po_break_down_id,po_break_down_id from WO_BOOKING_DTLS where upper(booking_no) like upper('%".trim($txt_search_common)."')", "po_break_down_id", "po_break_down_id");
				$sql_cond = where_con_using_array($order_id_arr,0,"b.id");

			}
		}
	}

	if($txt_date_from!="" || $txt_date_to!="")
	{
		if($db_type==0){$sql_cond .= " and b.shipment_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";}
		if($db_type==2 || $db_type==1){ $sql_cond .= " and b.shipment_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'";}
	}

	if(trim($company)!="") $sql_cond .= " and a.company_name='$company'";

	if($year !=0)
	{
		if($db_type==0) { $sql_shipment_year_cond=" and YEAR(b.shipment_date)=$year";   }
		if($db_type==2) {$sql_shipment_year_cond=" and to_char(b.shipment_date,'YYYY')=$year";}
	}

	$is_projected_po_allow=return_field_value("production_entry","variable_settings_production","variable_list=58 and company_name=$company");
    $projected_po_cond = ($is_projected_po_allow==2) ? " and b.is_confirmed=1" : "";

	$sql = "SELECT b.id, a.order_uom, a.buyer_name, a.company_name, a.total_set_qnty, a.set_break_down, a.job_no, a.style_ref_no, a.gmts_item_id, a.location_name, b.shipment_date, b.po_number, b.po_number_acc, b.po_quantity, b.plan_cut, b.grouping, b.file_no, a.job_no
			from wo_po_details_master a, wo_po_break_down_vw b where a.job_no = b.job_no_mst and b.shiping_status!=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.garments_nature=$garments_nature $sql_cond  $projected_po_cond order by b.shipment_date desc"; //$sql_shipment_year_cond
	// echo $sql;die;
	$result = sql_select($sql);
	$all_job_array=array();
	$all_po_array=array();
	foreach($result as $k=>$v)
	{
		$all_job_array[trim($v[csf("job_no")])]=trim($v[csf("job_no")]);
		$all_po_array[trim($v[csf("id")])]=trim($v[csf("id")]);
	}
	// print_r($all_po_array);
	// ============================= store data in gbl table ==============================
	$con = connect();
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = $user_id and ref_from =1 and ENTRY_FORM=65");
	oci_commit($con);

	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 65, 1, $all_po_array, $empty_arr);//Po ID
	disconnect($con);

 	$all_job="'".implode("','", array_unique($all_job_array))."'";
    $all_po="'".implode("','", array_unique($all_po_array))."'";

    $all_po_cond = where_con_using_array($all_po_array,0,"po_break_down_id");

 	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$po_country_data_arr=array();
	$po_country_sql=sql_select("SELECT a.po_break_down_id,a.item_number_id ,a.country_id,a.color_number_id,a.order_quantity as qnty,a.plan_cut_qnty,a.country_ship_date,a.pack_type from wo_po_color_size_breakdown a,GBL_TEMP_ENGINE tmp where a.po_break_down_id=tmp.ref_val and tmp.entry_form=65  and tmp.user_id=$user_id and tmp.ref_from=1 and a.status_active=1 and a.is_deleted=0");
	$po_country_data_arr=array();
	foreach ($po_country_sql as $key => $value)
	{

		$po_country_data_arr[$value[csf('po_break_down_id')]][$value[csf('item_number_id')]][$value[csf('country_id')]][$value[csf('country_ship_date')]][$value[csf('pack_type')]]['po_qnty'] +=$value[csf('qnty')];
		$po_country_data_arr[$value[csf('po_break_down_id')]][$value[csf('item_number_id')]][$value[csf('country_id')]][$value[csf('country_ship_date')]][$value[csf('pack_type')]]['plan_cut_qnty']+=$value[csf('plan_cut_qnty')];

		if($po_country_arr[$value[csf("po_break_down_id")]]=="")
		{
			$po_country_arr[$value[csf("po_break_down_id")]].=$value[csf("country_id")];
			$po_color_arr[$value[csf("po_break_down_id")]].=$value[csf("color_number_id")];
		}
		else
		{
			$po_country_arr[$value[csf("po_break_down_id")]].=','.$value[csf("country_id")];
			$po_color_arr[$value[csf("po_break_down_id")]].=','.$value[csf("color_number_id")];
		}
		// $po_country_data_arr[$value[csf('po_break_down_id')]][$value[csf('item_number_id')]][$value[csf('country_id')]]['po_qnty']+=$value[csf('qnty')];
		// $po_country_data_arr[$value[csf('po_break_down_id')]][$value[csf('item_number_id')]][$value[csf('country_id')]]['plan_cut_qnty']+=$value[csf('plan_cut_qnty')];
	}
	// echo "<pre>";
	// print_r($po_country_data_arr);die;

	/* $po_country_data_arr=array();
	$poCountryData=sql_select( "SELECT po_break_down_id, item_number_id, country_id, sum(order_quantity) as qnty, sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 $all_po_cond group by po_break_down_id, item_number_id, country_id");

	foreach($poCountryData as $row)
	{
		$po_country_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]['po_qnty']=$row[csf('qnty')];
		$po_country_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]['plan_cut_qnty']=$row[csf('plan_cut_qnty')];
	} */

	// echo $sewing_level;die;
	$total_in_qty_data_arr=array();
	if($sewing_level==1)
	{
		$total_in_qty=sql_select( "SELECT a.country_ship_date,a.po_break_down_id, a.item_number_id, a.country_id, sum(a.production_quantity) as production_quantity,(case when a.production_type=4   then a.production_quantity else 0 end ) as production_quantity ,(case when a.production_type=5   then a.production_quantity else 0 end ) as production_quantity_swingout from pro_garments_production_mst a ,GBL_TEMP_ENGINE tmp where a.po_break_down_id=tmp.ref_val and tmp.entry_form=65  and tmp.user_id=$user_id and tmp.ref_from=1 and a.status_active=1 and a.is_deleted=0 and a.production_type in(4,5)");
	}
	else
	{
		$total_in_qty=sql_select( "SELECT a.country_ship_date,a.po_break_down_id, a.item_number_id, a.country_id, (case when b.production_type=4 then b.production_qnty else 0 end ) as production_quantity ,(case when b.production_type=5 then b.production_qnty else 0 end ) as production_quantity_swingout from wo_po_color_size_breakdown a,pro_garments_production_dtls b,GBL_TEMP_ENGINE tmp where a.id=b.color_size_break_down_id and a.po_break_down_id=tmp.ref_val and tmp.entry_form=65  and tmp.user_id=$user_id and tmp.ref_from=1 and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and b.production_type in(4,5)");
	}

	
	// echo "<pre>"; print_r($attach_style_arr);die;

	$con = connect();
	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = $user_id and ref_from =1 and ENTRY_FORM=65");
	oci_commit($con);
	disconnect($con);

	foreach($total_in_qty as $row)
	{
		$total_in_qty_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]][$row[csf('country_ship_date')]]["sew_in"]+=$row[csf('production_quantity')];
		$total_in_qty_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]][$row[csf('country_ship_date')]]["sew_out"]+=$row[csf('production_quantity_swingout')];
	}

	$is_disabled = return_field_value("is_disable","field_level_access","company_id=$company and user_id=$user_id and page_id=500 and field_id=1 and status_active=1 and is_deleted=0");

	?>
     <div style="width:1270px;">
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1270" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="65">Shipment Date</th>
                <th width="100">Order No</th>
                <th width="90">Acc.Order No</th>
                <th width="100">Buyer</th>
                <th width="120">Style</th>
                <th width="80">Job No</th>
                <th width="80">File No</th>
                <th width="80">Ref. No</th>
                <th width="120">Item</th>
                <th width="90">Country</th>
                <th width="80">Order Qty</th>
                <th width="60">Total Sewing Input Qty</th>
                <th width="60">Total Sewing Output Qty</th>
                <th>Balance</th>
            </thead>
     	</table>
     </div>
     <div style="width:1270px; max-height:240px;overflow-y:scroll;" >
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1250" class="rpt_table" id="tbl_po_list">
			<?
			$i=1;
            foreach( $result as $row )
            {
				$exp_grmts_item = explode("__",$row[csf("set_break_down")]);
				$numOfItem = count($exp_grmts_item);
				$set_qty=""; $grmts_item="";

				//$country=explode(",",$po_country_arr[$row[csf("id")]]);
				$country=array_unique(explode(",",$po_country_arr[$row[csf("id")]]));
				$color=array_unique(explode(",",$po_color_arr[$row[csf("id")]]));
				// print_r($color_arr);
				$color_name = '';
				foreach ($color as $key => $value)
				{
					if ($color_name !='')
					{
						$color_name .=','.$color_arr[$value];
					}
					else
					{
					 	$color_name = $color_arr[$value];
					}
				}
				$numOfCountry = count($country);

				for($k=0;$k<$numOfItem;$k++)
				{
					if($row["total_set_qnty"]>1)
					{
						$grmts_item_qty = explode("_",$exp_grmts_item[$k]);
						$grmts_item = $grmts_item_qty[0];
						$set_qty = $grmts_item_qty[1];
					}else
					{
						$grmts_item_qty = explode("_",$exp_grmts_item[$k]);
						$grmts_item = $grmts_item_qty[0];
						$set_qty = $grmts_item_qty[1];
					}

					foreach($country as $country_id)
					{
						foreach ($po_country_data_arr[$row[csf('id')]][$grmts_item][$country_id] as $coun_ship_date=>$coun_ship_date_data) 
						{ 
							//$country_ship_date = $coun_ship_date_data; 
							foreach ($coun_ship_date_data as $pack_type=>$pack_data) 
							{
								$country_ship_date = $coun_ship_date;
								if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								//$po_qnty=$row[csf("po_quantity")]; $plan_cut_qnty=$row[csf("plan_cut")];
								$po_qnty=$po_country_data_arr[$row[csf('id')]][$grmts_item][$country_id][$coun_ship_date][$pack_type]['po_qnty'];
								$plan_cut_qnty=$po_country_data_arr[$row[csf('id')]][$grmts_item][$country_id][$coun_ship_date][$pack_type]['plan_cut_qnty'];
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value(<? echo $row[csf("id")];?>,'<? echo $grmts_item;?>','<? echo $po_qnty;?>','<? echo $plan_cut_qnty;?>','<? echo $country_id;?>','<?  echo $country_ship_date  ?>','<?  echo $is_disabled  ?>');" >
									<td width="30" align="center"><?php echo $i; ?></td>
									<td width="65" style="word-break:break-all"><?php echo change_date_format($coun_ship_date);?></td>
									<td title="<?php echo $color_name?$color_name:'' ?>" width="100" style="word-break:break-all"><?php echo $row[csf("po_number")]; ?></td>
									<td width="90" style="word-break:break-all"><?php echo $row[csf("po_number_acc")]; ?></td>
									<td width="100" style="word-break:break-all"><?php echo $buyer_arr[$row[csf("buyer_name")]]; ?></td>
									<td width="120" style="word-break:break-all"><?php echo $row[csf("style_ref_no")]; ?></td>
									<td width="80" style="word-break:break-all"><?php echo $row[csf("job_no")]; ?></td>
									<td width="80" style="word-break:break-all"><?php echo $row[csf("file_no")]; ?></td>
									<td width="80" style="word-break:break-all"><?php echo $row[csf("grouping")]; ?></td>
									<td width="120" style="word-break:break-all"><?php echo $garments_item[$grmts_item];?></td>
									<td width="90" style="word-break:break-all"><?php echo $country_library[$country_id]; ?>&nbsp;</td>
									<td width="80" align="right"><?php echo $po_qnty; ?>&nbsp;</td>
									<td width="60" align="right"><?php echo $total_in_qty=$total_in_qty_data_arr[$row[csf('id')]][$grmts_item][$country_id][$coun_ship_date]["sew_in"]; ?> &nbsp;</td>
									<td width="60" align="right"><?php echo $total_cut_qty=$total_in_qty_data_arr[$row[csf('id')]][$grmts_item][$country_id][$coun_ship_date]["sew_out"]; ?>&nbsp;</td>
									<td align="right"><?php $balance=$po_qnty-$total_cut_qty; echo $balance; ?>&nbsp; </td>
								</tr>
								<?
								$i++;
								
							}
						}
					}
				}
      		}
   		?>
        </table>
    </div>
	<?
	exit();
}

if ($action=="all_system_id_popup")
{
		extract($_REQUEST);
		echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
		$sqls="SELECT  id FROM pro_garments_production_mst Where po_break_down_id = '$po_id' AND production_type = '5' AND status_active = 1 ORDER BY ID asc ";
		$k=1;
		?>
		<table width="310" style="margin: 0px auto;font-weight: bold;" cellspacing="0" cellpadding="0" class="rpt_table" align="left" border="1" rules="all">
			<thead>
				<tr>
					<th width="80">SL</th>
					<th width="230">Sys.Challan No</th>
				</tr>
			</thead>
		</table>
		<div style="width:330px; max-height:200px;overflow-y:scroll;" >
		<table cellspacing="0" cellpadding="0" border="0" rules="all" width="310" class="rpt_table" id="tbl_search_list2">
			<tbody>


			<?
			foreach(sql_select($sqls) as $v)
			{
				?>
				<tr>
					<td width="80" align="center"><? echo $k++;?></td>
					<td width="230" align="center"><? echo $v[csf("id")];?></td>
				</tr>

				<?

			}
			?>
			</tbody>
		</table>
		</div>
		<script type="text/javascript">

		setFilterGrid("tbl_search_list2",-1);
		</script>

		<?
		exit();
}



if($action=="populate_data_from_search_popup")
{

	$dataArr = explode("**",$data);
	$po_id = $dataArr[0];
	$item_id = $dataArr[1];
	$country_id = $dataArr[2];
	$country_ship_date = $dataArr[3];

	$res = sql_select("SELECT a.id, a.po_quantity, a.plan_cut, a.po_number, a.po_quantity, b.company_name, b.buyer_name, b.style_ref_no, b.gmts_item_id, b.order_uom, b.job_no, b.location_name
			from wo_po_break_down a, wo_po_details_master b
			where a.job_id=b.id and a.id=$po_id");

	foreach($res as $v)
	{
		$company_id=$v[0][csf("company_name")];
	}

	$variable_sql=sql_select("SELECT sewing_production from variable_settings_production where company_name='$company_id' and variable_list=1 and status_active=1");
	$sewing_level=$variable_sql[0][csf("sewing_production")];
	if( $country_ship_date=='') $country_ship_date_cond=''; else $country_ship_date_cond=" and country_ship_date='$country_ship_date'";

	// echo $sewing_level; die;
  	foreach($res as $result)
	{
		echo "$('#txt_order_no').val('".$result[csf('po_number')]."');\n";
		echo "$('#hidden_po_break_down_id').val('".$result[csf('id')]."');\n";
		echo "$('#cbo_buyer_name').val('".$result[csf('buyer_name')]."');\n";
		echo "$('#txt_job_no').val('".$result[csf('job_no')]."');\n";
		echo "$('#txt_style_no').val('".$result[csf('style_ref_no')]."');\n";
		if($sewing_level==1)
		{
			$dataArray=sql_select("SELECT SUM(CASE WHEN a.production_type=4   THEN a.production_quantity END) as totalinput,SUM(CASE WHEN a.production_type=5   THEN production_quantity ELSE 0 END) as totalsewing from pro_garments_production_mst a WHERE   a.po_break_down_id=".$result[csf('id')]."  and a.item_number_id='$item_id' and a.country_id='$country_id'  $country_ship_date_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");

		}
		else
		{
			$dataArray=sql_select("SELECT SUM(CASE WHEN b.production_type=4 THEN production_qnty END) as totalinput,SUM(CASE WHEN b.production_type=5 THEN production_qnty ELSE 0 END) as totalsewing from wo_po_color_size_breakdown a,pro_garments_production_dtls b WHERE a.id=b.color_size_break_down_id and  a.po_break_down_id=".$result[csf('id')]."  and a.item_number_id='$item_id' and a.country_id='$country_id'  $country_ship_date_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");

		}


 		foreach($dataArray as $row)
		{
			echo "$('#txt_input_quantity').val('".$row[csf('totalinput')]."');\n";
			echo "$('#txt_cumul_sewing_qty').val('".$row[csf('totalsewing')]."');\n";
			$yet_to_produced = $row[csf('totalinput')]-$row[csf('totalsewing')];
			echo "$('#txt_yet_to_sewing').val('".$yet_to_produced."');\n";
		}

  	}
 	exit();
}

if($action=="color_and_size_level")
{
		$dataArr = explode("**",$data);
		$po_id = $dataArr[0];
		$item_id = $dataArr[1];
		$variableSettings = $dataArr[2];
		$styleOrOrderWisw = $dataArr[3];
		$country_id = $dataArr[4];
		$variableSettingsRej = $dataArr[5];
		$sewing_line = $dataArr[6];
		$country_ship_date=$dataArr[7];
		
	 
		$sewing_line_cond = "";
		if($sewing_line)
		{
			$sewing_line_cond = " and c.sewing_line=$sewing_line";
			$sewing_line_cond2 = " and b.sewing_line=$sewing_line";
		}
		if( $country_ship_date=='') $country_ship_date_cond=''; else $country_ship_date_cond=" and c.country_ship_date='$country_ship_date'";
		if( $country_ship_date=='') $country_ship_date_cond2=''; else $country_ship_date_cond2=" and a.country_ship_date='$country_ship_date'";
		if( $country_ship_date=='') $country_ship_date_cond3=''; else $country_ship_date_cond3=" and b.country_ship_date='$country_ship_date'";
         
		$color_library=return_library_array( "select id, color_name from lib_color",'id','color_name');
		$size_library=return_library_array( "select id, size_name from lib_size",'id','size_name');

		//#############################################################################################//
		// order wise - color level, color and size level


		//$variableSettings=2;

		if( $variableSettings==2 ) // color level
		{
			if($db_type==0)
			{
				$sql = "SELECT id, item_number_id, size_number_id, color_number_id, order_quantity, sum(plan_cut_qnty) as plan_cut_qnty, (select sum(CASE WHEN pdtls.color_size_break_down_id=wo_po_color_size_breakdown.id then pdtls.production_qnty ELSE 0 END) from pro_garments_production_dtls pdtls where pdtls.production_type=4 and pdtls.is_deleted=0 ) as production_qnty, (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.production_qnty ELSE 0 END) from pro_garments_production_dtls cur where cur.production_type=5 and cur.is_deleted=0 ) as cur_production_qnty
				from wo_po_color_size_breakdown
				where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' $country_ship_date_cond2 and is_deleted=0 and status_active=1 group by color_number_id";
			}
			else
			{
				$sql = "SELECT a.item_number_id, a.color_number_id, sum(a.order_quantity) as order_quantity, sum(a.plan_cut_qnty) as plan_cut_qnty,
						sum(CASE WHEN c.production_type=4 $sewing_line_cond then b.production_qnty ELSE 0 END) as production_qnty,
						sum(CASE WHEN c.production_type=5 $sewing_line_cond then b.production_qnty ELSE 0 END) as cur_production_qnty,
						sum(CASE WHEN c.production_type=5 $sewing_line_cond then b.reject_qty ELSE 0 END) as reject_qty
						from wo_po_color_size_breakdown a
						left join pro_garments_production_dtls b on a.id=b.color_size_break_down_id and b.is_deleted=0 and b.status_active=1
						left join pro_garments_production_mst c on c.id=b.mst_id and c.is_deleted=0 and c.status_active=1
						where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' $country_ship_date_cond2 and a.is_deleted=0 and a.status_active=1 group by a.item_number_id, a.color_number_id";

			}
		}
		else if( $variableSettings==3 ) //color and size level
		{
			/*$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty, (select sum(CASE WHEN pdtls.color_size_break_down_id=wo_po_color_size_breakdown.id then pdtls.production_qnty ELSE 0 END) from pro_garments_production_dtls pdtls where pdtls.production_type=4 and pdtls.is_deleted=0 ) as production_qnty, (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.production_qnty ELSE 0 END) from pro_garments_production_dtls cur where cur.production_type=5 and cur.is_deleted=0 ) as cur_production_qnty
				from wo_po_color_size_breakdown
				where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1";*/

			$dtlsData = sql_select("SELECT a.color_size_break_down_id,
										sum(CASE WHEN a.production_type=4 $sewing_line_cond2 then a.production_qnty ELSE 0 END) as production_qnty,
										sum(CASE WHEN a.production_type=5 $sewing_line_cond2 then a.production_qnty ELSE 0 END) as cur_production_qnty ,
										sum(CASE WHEN a.production_type=5 $sewing_line_cond2 then a.reject_qty ELSE 0 END) as reject_qty
										from pro_garments_production_dtls a,pro_garments_production_mst b,wo_po_color_size_breakdown c where  a.mst_id=b.id and a.color_size_break_down_id=c.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' $country_ship_date_cond and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.color_size_break_down_id!=0 and a.production_type in(4,5) group by a.color_size_break_down_id");
										
										

			foreach($dtlsData as $row)
			{
				$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['iss']= $row[csf('production_qnty')];
				$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv']= $row[csf('cur_production_qnty')];
				$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rej']= $row[csf('reject_qty')];
			}

			$sql = "SELECT a.color_order,a.id,a.size_order, a.item_number_id, a.size_number_id, a.color_number_id, a.order_quantity, a.plan_cut_qnty
				from wo_po_color_size_breakdown a
				where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' $country_ship_date_cond2 and a.is_deleted=0 and a.status_active=1 order by a.color_order,a.size_order ";
				//echo $sql  ;

		}
		else // by default color and size level
		{
			/*$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty, (select sum(CASE WHEN pdtls.color_size_break_down_id=wo_po_color_size_breakdown.id then pdtls.production_qnty ELSE 0 END) from pro_garments_production_dtls pdtls where pdtls.production_type=4 and pdtls.is_deleted=0 ) as production_qnty, (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.production_qnty ELSE 0 END) from pro_garments_production_dtls cur where cur.production_type=5 and cur.is_deleted=0 ) as cur_production_qnty
				from wo_po_color_size_breakdown
				where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1";*/

				$dtlsData = sql_select("SELECT a.color_size_break_down_id,
				sum(CASE WHEN a.production_type=4 $sewing_line_cond2 then a.production_qnty ELSE 0 END) as production_qnty,
				sum(CASE WHEN a.production_type=5 $sewing_line_cond2 then a.production_qnty ELSE 0 END) as cur_production_qnty ,
				sum(CASE WHEN a.production_type=5 $sewing_line_cond2 then a.reject_qty ELSE 0 END) as reject_qty
				from pro_garments_production_dtls a,pro_garments_production_mst b,wo_po_color_size_breakdown c where  a.mst_id=b.id and a.color_size_break_down_id=c.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' $country_ship_date_cond and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.color_size_break_down_id!=0 and a.production_type in(4,5) group by a.color_size_break_down_id");

			foreach($dtlsData as $row)
			{
				$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['iss']= $row[csf('production_qnty')];
				$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv']= $row[csf('cur_production_qnty')];
				$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rej']= $row[csf('reject_qty')];
			}

			$sql = "SELECT  a.color_order,a.id, a.size_order,a.item_number_id, a.size_number_id, a.color_number_id, a.order_quantity, a.plan_cut_qnty
				from wo_po_color_size_breakdown a
				where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' $country_ship_date_cond2  and a.country_id='$country_id' and a.is_deleted=0 and a.status_active=1 order by a.color_order,a.size_order ";
				
		}

		if($variableSettingsRej!=1)
		{
			$disable="";
		}
		else
		{
			$disable="disabled";
		}
         
		$colorResult = sql_select($sql);
 		//print_r($sql);
  		$colorHTML="";
		$colorID='';
		$chkColor = array();
		$i=0;$totalQnty=0;
 		foreach($colorResult as $color)
		{

			if( $variableSettings==2 ) // color level
			{
				$colorHTML .='<tr><td>'.$color_library[$color[csf("color_number_id")]].'</td><td><input type="text" name="txt_color" id="colSize_'.($i+1).'" style="width:60px"  class="text_boxes_numeric" placeholder="'.($color[csf("production_qnty")]-$color[csf("cur_production_qnty")]).'" onblur="fn_colorlevel_total('.($i+1).')" onkeypress="return isNumber(event)"></td><td><input type="text" name="txtColSizeRej" id="colSizeRej_'.($i+1).'" style="width:60px"  class="text_boxes_numeric" placeholder="Rej." onblur="fn_colorRej_total('.($i+1).') '.$disable.'" onkeypress="return isNumber(event)"></td><td><input type="text" name="colorSizeBundleQnty" id="colorSizeBundleQnty_'.$color[csf("color_number_id")].($i+1).'" placeholder="Bundle Qty"  class="text_boxes_numeric" style="width:80px"></td></tr>';
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

				$iss_qnty=$color_size_qnty_array[$color[csf('id')]]['iss'];
				$rcv_qnty=$color_size_qnty_array[$color[csf('id')]]['rcv'];
				$rej_qnty=$color_size_qnty_array[$color[csf('id')]]['rej'];
				$color_size_breakdown_id = $color["ID"];

 				$colorHTML .='<tr><td>'.$size_library[$color[csf("size_number_id")]].'</td><td><input type="text" name="colorSize" data-colorSizeBreakdown="'.$color_size_breakdown_id.'"  id="colSize_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:80px" placeholder="'.($iss_qnty-$rcv_qnty-$rej_qnty).'" onblur="fn_total('.$color[csf("color_number_id")].','.($i+1).')" onkeypress="return isNumber(event)" onfocus="check_line(this.id)"><input type="text" name="colorSizeRej" data-colorSizeBreakdown="'.$color_size_breakdown_id.'"  id="colSizeRej_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:50px" placeholder="Rej. Qty" onblur="fn_total_rej('.$color[csf("color_number_id")].','.($i+1).')" '.$disable.' onkeypress="return isNumber(event)"><input type="text" name="colorSizePOQnty" id="colorSizePOQnty_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:70px" value="'.$color[csf("order_quantity")].'" readonly disabled><input type="text" data-colorSizeBreakdown="'.$color_size_breakdown_id.'"  name="colorSizeBundleQnty" id="colorSizeBundleQnty_'.$color[csf("color_number_id")].($i+1).'" placeholder="Bundle Qty"  class="text_boxes_numeric" style="width:80px"></td></tr>';
			}

			$i++;
		}
		//echo $colorHTML;die;
		if( $variableSettings==2 ){ $colorHTML = '<table id="table_color" class="rpt_table"><thead><th width="70">Color</th><th width="60">Quantity</th><th width="60">Reject</th></thead><tbody>'.$colorHTML.'<tbody><tfoot><tr><th>Total</th><th><input type="text" id="total_color" placeholder="'.$totalQnty.'" class="text_boxes_numeric" style="width:60px" ></th><th><input type="text" id="total_color_rej" class="text_boxes_numeric" style="width:60px" ></th></tr></tfoot></table>'; }
		echo "$('#breakdown_td_id').html('".addslashes($colorHTML)."');\n";
		$colorList = substr($colorID,0,-1);
		echo "$('#hidden_colorSizeID').val('".$colorList."');\n";
		//#############################################################################################//
		exit();
}

if ($action=="service_booking_popup")
{
	echo load_html_head_contents("Booking Search","../../", 1, 1, $unicode);
	extract($_REQUEST);
	$preBookingNos = 0;
	?>

	<script>

		function js_set_value(booking_no)
		{
			// alert(booking_no);
			document.getElementById('selected_booking').value=booking_no; //return;
	 	 	parent.emailwindow.hide();
		}

	</script>

	</head>
	<body>
	<div align="center" style="width:100%;" >
		<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
			<table width="1300" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
				<tr>
					<td align="center" width="100%">
						<table  cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
                        	 <input type="hidden" id="selected_batchDtls" class="text_boxes" style="width:70px" value="<? echo $txt_batch_dtls;?>">
                              <input type="hidden" id="booking_no" class="text_boxes" style="width:70px" value="">
                              <input type="hidden" id="booking_id" class="text_boxes" style="width:70px">


							<thead>
								<th  colspan="11">
									<?
									echo create_drop_down( "cbo_search_category", 140, $string_search_type,'', 1, "-- Search Catagory --",1 );
									?>
								</th>
							</thead>
							<thead>
								<th width="150">Company Name</th>
								<th width="150">Supplier Name</th>
								<th width="150">Buyer  Name</th>
								<th width="100">Job  No</th>
								<th width="100">Order No</th>
								<th width="100">Internal Ref.</th>
								<th width="100">File No</th>
								<th width="100">Style No.</th>
								<th width="100">WO No</th>
								<th width="200">Date Range</th>
								<th></th>
							</thead>
							<tr>
								<td> <input type="hidden" id="selected_booking">
									<?
									echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 $company_cond order by company_name","id,company_name",1, "-- Select Company --", "".$company_id."", "load_drop_down( 'fabric_issue_to_finishing_process_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );",1);
									?>
								</td>
								<td>
									<?php
									if($cbo_service_source==3)
									{
										echo create_drop_down( "cbo_supplier_name", 152, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and a.status_active=1 and b.party_type in (21,24,25) group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select --", "".$supplier_id."", "",1 );
									}
									else
									{
										echo create_drop_down( "cbo_supplier_name", 152, "select id,company_name from lib_company comp where status_active=1 $company_cond order by company_name","id,company_name", 1, "-- Select --", "".$supplier_id."", "",1 );
									}
									?>
								</td>
								<td id="buyer_td">
									<?
									echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company_id' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
									?>
								</td>
								<td>
									<input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:70px">
								</td>


								<td>
									<input name="txt_order_number" id="txt_order_number" class="text_boxes" style="width:70px" readonly value="<?php echo $po_order_no;?>">
								</td>
								<td>
									<input name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:70px">
								</td>
								<td>
									<input name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:70px">
								</td>



								<td>
									<input name="txt_style" id="txt_style" class="text_boxes" style="width:70px">
								</td>
								<td>
									<input name="txt_booking_prifix" id="txt_booking_prifix" class="text_boxes" style="width:70px">
								</td>
								<td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px">
									<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px">
								</td>
								<td align="center">
									<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('txt_booking_prifix').value+'_'+document.getElementById('cbo_search_category').value+'_'+document.getElementById('cbo_supplier_name').value+'_'+document.getElementById('txt_style').value+'_'+<? echo $preBookingNos;?>+'_'+document.getElementById('txt_order_number').value+'_'+document.getElementById('txt_internal_ref').value+'_'+document.getElementById('txt_file_no').value+'_'+<? echo $cbo_service_source;?>+'_'+<?php echo $po_order_id;?>, 'create_booking_search_list_view', 'search_div', 'sewing_output_controller','setFilterGrid(\'tbl_list_search\',-1)')" style="width:100px;" /></td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td  align="center" height="40" valign="middle"><? echo load_month_buttons(1);  ?>
						</td>
					</tr>

   </table>
   <div style="width:100%; margin-top:5px" id="search_div" align="left"></div>

	</form>
	</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}

if ($action=="create_booking_search_list_view")
{

	$data=explode('_',$data);
	 //echo "<pre>";print_r($data);
	if ($data[0]!=0) $company=" and c.company_name='$data[0]'"; else { echo "Please Select Company First."; die; }
    if ($data[1]!=0) $buyer=" and c.buyer_name='$data[1]'"; else $buyer="";

	if($data[2] !="" && $data[3] !="")
	{
		$tot_days = datediff('d',$data[2],$data[3]);
		if($tot_days>93)// max 3 month
		{
			echo "<div style='color:red;font-size:16px;font-weight:bold;text-align:center;'>Invalid Date Range.</div>"; die;
		}
	}

    if($db_type==0)
    {
    	if ($data[2]!="" &&  $data[3]!="") $wo_date  = "and a.wo_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $wo_date ="";
    }

    if($db_type==2)
    {
    	if ($data[2]!="" &&  $data[3]!="") $wo_date  = "and a.wo_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $wo_date ="";
    }
    //echo $data[8];
    if($data[6]==1)
    {
    	if (str_replace("'","",$data[5])!="") $wo_cond=" and a.sys_number_prefix_num='$data[5]'    "; else  $wo_cond="";
    	if (str_replace("'","",$data[4])!="") $job_cond=" and c.job_no_prefix_num='$data[4]'  "; else  $job_cond="";
    	if (str_replace("'","",$data[8])!="") $style_cond=" and c.style_ref_no='$data[8]'  "; else  $style_cond="";
    }
    if($data[6]==4 || $data[6]==0)
    {
    	if (str_replace("'","",$data[5])!="") $wo_cond=" and a.sys_number_prefix_num like '%$data[5]%'  $booking_year_cond  "; else  $wo_cond="";
    	if (str_replace("'","",$data[4])!="") $job_cond=" and c.job_no_prefix_num like '%$data[4]%'  $year_cond  "; else  $job_cond="";
    	if (str_replace("'","",$data[8])!="") $style_cond=" and c.style_ref_no like '%$data[8]%'  "; else  $style_cond="";
    }

    if($data[6]==2)
    {
    	if (str_replace("'","",$data[5])!="") $wo_cond=" and a.sys_number_prefix_num like '$data[5]%'  $booking_year_cond  "; else  $wo_cond="";
    	if (str_replace("'","",$data[4])!="") $job_cond=" and c.job_no_prefix_num like '$data[4]%'  $year_cond  "; else  $job_cond="";
    	if (str_replace("'","",$data[8])!="") $style_cond=" and c.style_ref_no like '$data[8]%'  "; else  $style_cond="";
    }

    if($data[6]==3)
    {
    	if (str_replace("'","",$data[5])!="") $wo_cond=" and a.sys_number_prefix_num like '%$data[5]'  $booking_year_cond  "; else  $wo_cond="";
    	if (str_replace("'","",$data[4])!="") $job_cond=" and c.job_no_prefix_num like '%$data[4]'  $year_cond  "; else  $job_cond="";
    	if (str_replace("'","",$data[8])!="") $style_cond=" and c.style_ref_no like '%$data[8]'  "; else  $style_cond="";
    }

    if ($data[9]!="")
    {
    	foreach(explode(",", $data[9]) as $bok){
    		$bookingnos .= "'".$bok."',";
    	}
    	$bookingnos = chop($bookingnos,",");
		if( $service_source!=1)
		{
    	$preBookingNos_1 = " and a.booking_no not in (".$bookingnos.")";
    	$preBookingNos_2 = " and a.wo_no not in (".$bookingnos.")";
		}
    }
    if ($data[10]!="")
    {
    	$po_number_cond = " and d.po_number = '$data[10]'";
    }
    if ($data[11]!="")
    {
    	$internal_ref_cond = " and d.grouping = '$data[11]'";
    }
    if ($data[12]!="")
    {
    	$file_cond = " and d.file_no = '$data[12]'";
    }
    if ($data[14]!="")
    {
    	$po_id_cond = " and d.id = '$data[14]'";
    }


    $buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
    $comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
    // $suplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
    // $po_no=return_library_array( "select id, po_number from wo_po_break_down",'id','po_number');

    // $arr=array (2=>$comp,3=>$conversion_cost_head_array,4=>$buyer_arr,7=>$po_no,8=>$item_category,9=>$fabric_source,10=>$suplier);

	$sql= "SELECT a.id, a.sys_number_prefix_num, a.sys_number, a.wo_date, c.company_name, c.buyer_name, c.job_no_prefix_num,c.style_ref_no, c.job_no, b.po_id, d.file_no,d.po_number,d.grouping
	from garments_service_wo_mst a, garments_service_wo_dtls b, wo_po_details_master c ,wo_po_break_down d
	where a.id = b.mst_id  and b.po_id=d.id and c.id=d.job_id $company $buyer $wo_date $wo_cond $style_cond $file_cond $po_number_cond $internal_ref_cond $po_id_cond and a.status_active=1 and a.is_deleted=0 and b.rate_for=30 $job_cond
	group by a.id, a.sys_number_prefix_num, a.sys_number, a.wo_date, c.company_name, c.buyer_name, c.job_no_prefix_num,c.style_ref_no, c.job_no, b.po_id, d.file_no,d.po_number,d.grouping";
   	 //echo $sql;
    ?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="770" class="rpt_table">
    	<thead>
    		<tr>
    			<th width="20">SL No.</th>
    			<th width="120">WO No</th>
    			<th width="60">WO Date</th>
    			<th width="80">Company</th>
    			<th width="100">Buyer</th>
    			<th width="50">Job No</th>

    			<th width="70">Internal Ref.</th>
    			<th width="70">File No</th>


    			<th width="100">Style No.</th>
    			<th width="100">PO number</th>
    		</tr>
    	</thead>
    </table>
    <div style="width:1288px; max-height:400px; overflow-y:scroll;" >
    	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="770" class="rpt_table" id="tbl_list_search" >
    		<tbody>
    			<?
    			$result = sql_select($sql);
	    		$i=1;
	            foreach($result as $row)
	            {
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
                     <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('id')]."_".$row[csf('sys_number')]; ?>');">

						<td width="20"><? echo $i; ?></td>
						<td width="120"><p><? echo $row[csf('sys_number')]; ?></p></td>
						<td width="60"><p><? echo change_date_format($row[csf('wo_date')]); ?></p></td>
						<td width="80"><p><? echo $comp[$row[csf('company_name')]]; ?></p></td>

						<td width="100"><p><? echo $buyer_arr[$row[csf('buyer_name')]]; ?></p></td>
						<td width="50"><p><? echo $row[csf('job_no_prefix_num')]; ?></p></td>


						<td width="70"><p><? echo $row[csf('grouping')]; ?></p></td>
						<td width="70"><p><? echo $row[csf('file_no')]; ?></p></td>

						<td width="100"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
						<td width="100"><p><? echo $row[csf('po_number')]; ?></p></td>

					</tr>
					<?
					$i++;
    			}
    			?>
    		</tbody>
    	</table>
    </div>
    <script type="text/javascript">
    	setFilterGrid("tbl_list_search",-1);
    </script>
    <?

    exit();
}


if($action=="show_dtls_listview")
{
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$sewing_line_arr=return_library_array( "select id, line_name from lib_sewing_line",'id','line_name');
	$sewing_floor_arr=return_library_array( "select id, floor_name from lib_prod_floor",'id','floor_name');
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name"  );
    $color_name=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
	$dataArr = explode("**",$data);
	$po_id = $dataArr[0];
	$item_id = $dataArr[1];
	$country_id = $dataArr[2];
	$prod_reso_allo = $dataArr[3];
	$company_id_sql=sql_select("SELECT a.company_name from wo_po_details_master a,wo_po_break_down b where a.id=b.job_id and b.id=$po_id");
	$company_id=$company_id_sql[0][csf("company_name")];
	$variable_sql=sql_select("SELECT sewing_production from variable_settings_production where company_name='$company_id' and variable_list=1 and status_active=1");
	$sewing_level=$variable_sql[0][csf("sewing_production")];
	?>
     <div style="width:100%;">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table" >
            <thead>
                <th width="20"><p>SL</p></th>
                <th width="100" align="center"><p>Item Name</p></th>
                <th width="80" align="center"><p>Country</p></th>
                <th width="60" align="center"><p>Prod. Date</p></th>
                <th width="60" align="center"><p>QC Pass Qty</p></th>
                <th width="45" align="center"><p>Alter Qty</p></th>
                <th width="45" align="center"><p>Spot Qty</p></th>
                <th width="45" align="center"><p>Reject Qty</p></th>
                <th width="110" align="center"><p>Serving Company</p></th>
                <th width="80" align="center"><p>Location</p></th>
                <th width="50" align="center"><p>Floor</p></th>
                <th width="50" align="center"><p>Sewing Line</p></th>
				<th width="100" align="center"><p>Color</p></th>
                <th width="70" align="center"><p>Color Type</p></th>
                <th width="40" align="center"><p>Rep. Hour</p></th>
                <th width="80" align="center"><p>Supervisor</p></th>
                <th width="50" align="center"><p>Challan</p></th>
                <th width="50" align="center"><p>Sys Chh.</p></th>
            </thead>
		</table>
	</div>
	<div style="width:100%;max-height:180px; overflow:y-scroll" id="tbl_list_search_" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table" id="tbl_list_search">
		<?php
			$i=1;//,TO_CHAR(production_hour,'DD-MON-YYYY HH24:MI')//
			$total_production_qnty=0;
			$date_cond=($db_type==2)? " TO_CHAR(a.production_hour,'HH24:MI') as prod_hour " : " TIME_FORMAT( production_hour, '%H:%i' ) as prod_hour ";
			if($sewing_level==1)
			{
				$sqlResult =sql_select(" SELECT a.id, a.po_break_down_id, a.item_number_id, a.country_id, a.production_date, sum(a.production_quantity) as production_quantity, a.alter_qnty, a.spot_qnty, a.reject_qnty, a.production_source, a.serving_company, a.sewing_line, a.supervisor, a.location, a.prod_reso_allo, $date_cond, a.challan_no, a.floor_id from pro_garments_production_mst a where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.production_type='5' and a.status_active=1 and a.is_deleted=0 GROUP BY a.id, a.po_break_down_id, a.item_number_id, a.country_id, a.production_date, a.alter_qnty, a.spot_qnty, a.reject_qnty, a.production_source, a.serving_company, a.sewing_line, a.supervisor, a.location, a.prod_reso_allo, a.production_hour, a.challan_no, a.floor_id ORDER BY a.production_hour DESC");
			}
			else
			{
				$sqlResult =sql_select("SELECT a.id, a.po_break_down_id, a.item_number_id, a.country_id, a.production_date, sum(b.production_qnty) as production_quantity, a.alter_qnty, a.spot_qnty, a.reject_qnty, a.production_source, a.serving_company, a.sewing_line, a.supervisor, a.location, a.prod_reso_allo, $date_cond, a.challan_no, a.floor_id, b.color_type_id from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and  a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.production_type='5' and a.status_active=1 and a.is_deleted=0 and b.production_type='5' and b.status_active=1 and b.is_deleted=0 GROUP BY a.id, a.po_break_down_id, a.item_number_id, a.country_id, a.production_date, a.alter_qnty, a.spot_qnty, a.reject_qnty, a.production_source, a.serving_company, a.sewing_line, a.supervisor, a.location, a.prod_reso_allo, a.production_hour, a.challan_no, a.floor_id, b.color_type_id ORDER BY a.production_hour DESC");
			}
			$order_cond = array();
            foreach($sqlResult as $result)
			{
				 
				 $order_cond[$result['PO_BREAK_DOWN_ID']]  = $result['PO_BREAK_DOWN_ID'];
			}
			$order_cond_id = implode(",",$order_cond);
		    $newSql = "SELECT b.PRODUCTION_QNTY, b.mst_id, a.PO_BREAK_DOWN_ID , a.ITEM_NUMBER_ID , a.COUNTRY_ID , a.COLOR_NUMBER_ID FROM WO_PO_COLOR_SIZE_BREAKDOWN a,pro_garments_production_dtls b WHERE a.PO_BREAK_DOWN_ID in ($order_cond_id) and b.COLOR_SIZE_BREAK_DOWN_ID = a.id AND b.status_active=1 AND b.is_deleted=0";
			$mysqli_ex_query = sql_select($newSql);

			$Color_Item_arr = array();
			foreach($mysqli_ex_query as $data)
			{ 
				if($data['PRODUCTION_QNTY']>0) 
				{
					$Color_Item_arr[$data['MST_ID']][$data['COLOR_NUMBER_ID']] = $color_name[$data['COLOR_NUMBER_ID']]; 
				}
			   
			}
			
			foreach($sqlResult as $result)
			{
				if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$total_production_qnty+=$result[csf('production_quantity')];

				$sewing_line='';
				if($result[csf('prod_reso_allo')]==1)
				{
					$line_number=explode(",",$prod_reso_arr[$result[csf('sewing_line')]]);
					foreach($line_number as $val)
					{
						if($sewing_line=='') $sewing_line=$sewing_line_arr[$val]; else $sewing_line.=",".$sewing_line_arr[$val];
					}
				}
				else $sewing_line=$sewing_line_arr[$result[csf('sewing_line')]];
  			?>
			<tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none; cursor:pointer" >
				<td width="20">
                    <input type="checkbox" id="tbl_<?=$i; ?>" onClick="fnc_checkbox_check(<?=$i; ?>);"/>
                    <input type="hidden" id="mstidall_<?=$i; ?>" value="<?=$result[csf('id')]; ?>" style="width:10px"/>
                    <input type="hidden" id="productiondate_<?=$i; ?>" value="<?=$result[csf('production_date')]; ?>" />
                    <input type="hidden" id="productionsource_<?=$i; ?>" value="<?=$result[csf('production_source')]; ?>" />
                    <input type="hidden" id="servingcompany_<?=$i; ?>" value="<?=$result[csf('serving_company')]; ?>" />
                &nbsp;</td>
                <td width="100" style="word-break:break-all" onClick="fnc_load_from_dtls('<?=$result[csf('id')]; ?>');"><?=$garments_item[$result[csf('item_number_id')]]; ?></td>
                <td width="80" style="word-break:break-all" onClick="fnc_load_from_dtls('<?=$result[csf('id')]; ?>');"><?=$country_library[$result[csf('country_id')]]; ?></td>
                <td width="60" style="word-break:break-all" onClick="fnc_load_from_dtls('<?=$result[csf('id')]; ?>');"><?=change_date_format($result[csf('production_date')]); ?></td>
                <td width="60" align="right" onClick="fnc_load_from_dtls('<?=$result[csf('id')]; ?>');"><?=$result[csf('production_quantity')]; ?></td>
                <td width="45" align="right" onClick="fnc_load_from_dtls('<?=$result[csf('id')]; ?>');"><?=$result[csf('alter_qnty')]; ?></td>
                <td width="45" align="right" onClick="fnc_load_from_dtls('<?=$result[csf('id')]; ?>');"><?=$result[csf('spot_qnty')]; ?></td>
                <td width="45" align="right" onClick="fnc_load_from_dtls('<?=$result[csf('id')]; ?>');"><?=$result[csf('reject_qnty')]; ?></td>
				<?php
                        $source= $result[csf('production_source')];
					   	if($source==3) $serving_company= $supplier_arr[$result[csf('serving_company')]];
						else $serving_company= $company_arr[$result[csf('serving_company')]];
                 ?>
                <td width="110" style="word-break:break-all" onClick="fnc_load_from_dtls('<?=$result[csf('id')]; ?>');"><?=$serving_company; ?></td>
                <td width="80" style="word-break:break-all" onClick="fnc_load_from_dtls('<?=$result[csf('id')]; ?>');"><?=$location_arr[$result[csf('location')]]; ?></td>
                <td width="50" style="word-break:break-all" onClick="fnc_load_from_dtls('<?=$result[csf('id')]; ?>');"><?=$sewing_floor_arr[$result[csf('floor_id')]]; ?></td>
                <td width="50" style="word-break:break-all" onClick="fnc_load_from_dtls('<?=$result[csf('id')]; ?>');"><?=$sewing_line; ?></td>

				<td width="100" style="word-break:break-all" onClick="fnc_load_from_dtls('<?=$result[csf('id')]; ?>');"><? echo implode(',',$Color_Item_arr[$result['ID']]); ?></td>

                <td width="70" style="word-break:break-all" onClick="fnc_load_from_dtls('<?=$result[csf('id')]; ?>');"><?=$color_type[$result[csf('color_type_id')]]; ?></td>
                <td width="40" style="word-break:break-all" align="center" onClick="fnc_load_from_dtls('<?=$result[csf('id')]; ?>');"><?=$result[csf('prod_hour')]; ?></td>
                <td width="80" style="word-break:break-all" onClick="fnc_load_from_dtls('<?=$result[csf('id')]; ?>');"><?=$result[csf('supervisor')]; ?>&nbsp;</td>
                <td width="50" style="word-break:break-all" align="center" onClick="fnc_load_from_dtls('<?=$result[csf('id')]; ?>');"><?=$result[csf('challan_no')]; ?></td>
                <td width="50" style="word-break:break-all" align="center" onClick="fnc_load_from_dtls('<?=$result[csf('id')]; ?>');"><?=$result[csf('id')];?></td>
			</tr>
			<?php
			$i++;
			}
			?>
            <!--<tfoot>
            	<tr>
                	<th colspan="3"></th>
                    <th><!? echo $total_production_qnty; ?></th>
                    <th colspan="4"></th>
                </tr>
            </tfoot>-->
		</table>
    </div>
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	<?
	exit();
}

if($action=="show_country_listview")
{
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="355" class="rpt_table">
        <thead>
            <th width="15">SL</th>
            <th width="85">Item Name</th>
            <th width="60">Country</th>
            <th width="50">Country Ship Date</th>
            <th width="45">Order Qty.</th>
            <th width="45">Sew.Input</th>
            <th width="55">Sew. Out</th>
        </thead>
        </table>
        <div style="width:375px;max-height:300px; overflow:y-scroll" align="left">
	        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="355" class="rpt_table" id="country_list_search">
			<?
			$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name"  );
			$issue_qnty_arr=sql_select("SELECT a.po_break_down_id, a.item_number_id, a.country_id,a.country_ship_date,
			(CASE WHEN b.production_type=5 THEN b.production_qnty ELSE 0 END) AS cutting_qnty,
			(CASE WHEN b.production_type=4 THEN b.production_qnty ELSE 0 END) AS input_qnty
			 from wo_po_color_size_breakdown a, pro_garments_production_dtls b where a.id=b.color_size_break_down_id and a.po_break_down_id='$data' and b.production_type in(4,5) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
			$issue_data_arr=array();
			foreach($issue_qnty_arr as $row)
			{
				$issue_data_arr[$row[csf("po_break_down_id")]][$row[csf("country_id")]][$row[csf("item_number_id")]][$row[csf("country_ship_date")]]+=$row[csf("cutting_qnty")];
				$sewing_input_arr[$row[csf("po_break_down_id")]][$row[csf("country_id")]][$row[csf("item_number_id")]][$row[csf("country_ship_date")]]+=$row[csf("input_qnty")];
			}
			$i=1;
			// $sqlResult =sql_select("SELECT po_break_down_id, item_number_id, country_id, max(country_ship_date) as country_ship_date, sum(order_quantity) as order_qnty, sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown where po_break_down_id='$data' and status_active=1 and is_deleted=0 group by po_break_down_id, item_number_id, country_id order by country_ship_date Asc");
			$sqlResult = sql_select("SELECT po_break_down_id, item_number_id, country_id, country_ship_date,pack_type, sum(order_quantity) as order_qnty, sum(plan_cut_qnty) as plan_cut_qnty, max(cutup) as cutup from wo_po_color_size_breakdown where po_break_down_id='$data' and status_active in(1,2,3) and is_deleted=0 group by po_break_down_id, item_number_id, country_id,country_ship_date,pack_type order by country_ship_date");
			foreach($sqlResult as $row)
			{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$issue_qnty=$issue_data_arr[$row[csf("po_break_down_id")]][$row[csf("country_id")]][$row[csf("item_number_id")]][$row[csf("country_ship_date")]];
				$sewing_inputqty=$sewing_input_arr[$row[csf("po_break_down_id")]][$row[csf("country_id")]][$row[csf("item_number_id")]][$row[csf("country_ship_date")]];
			?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="put_country_data(<? echo $row[csf('po_break_down_id')].",".$row[csf('item_number_id')].",".$row[csf('country_id')].",".$row[csf('order_qnty')].",".$row[csf('plan_cut_qnty')]; ?> ,'<?=$row[csf('country_ship_date')]; ?>'  );">
					<td width="15"><? echo $i; ?></td>
					<td width="85"><p><? echo $garments_item[$row[csf('item_number_id')]]; ?></p></td>
					<td width="60"><p><? echo $country_library[$row[csf('country_id')]]; ?>&nbsp;</p></td>
					<td width="50" align="center"><? if($row[csf('country_ship_date')]!="0000-00-00") echo change_date_format($row[csf('country_ship_date')]); ?>&nbsp;</td>
					<td align="right" width="45"><?php  echo $row[csf('order_qnty')]; ?></td>
	                <td align="right" width="45"><?php  echo $sewing_inputqty; ?></td>
	                <td align="right" width="55"><?php  echo $issue_qnty; ?></td>
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

if($action=="populate_input_form_data")
{
	//production type=5 come from array
	$date_cond=($db_type==2)? " TO_CHAR(production_hour,'HH24:MI') as production_hour " : " TIME_FORMAT( production_hour, '%H:%i' ) as production_hour ";
	$company_id_sql=sql_select("SELECT  company_id,sewing_line,po_break_down_id,item_number_id,country_id from  pro_garments_production_mst where  id='$data' and status_active=1 and is_deleted=0 ");
	$company_id=$company_id_sql[0][csf("company_id")];
	$sewing_line=$company_id_sql[0][csf("sewing_line")];
	$sewing_line_cond = "";
	if($sewing_line!="")
	{
		$sewing_line_cond = " and c.sewing_line=$sewing_line";
		$sewing_line_cond2 = " and b.sewing_line=$sewing_line";
	}
	$po_break_down_id = $company_id_sql[0][csf('po_break_down_id')];
	$item_number_id = $company_id_sql[0][csf('item_number_id')];
	$country_id = $company_id_sql[0][csf('country_id')];

	// ======================= get country ship date and update prod mst table ====================
	$con = connect();
	$sql_colsize ="SELECT a.mst_id,b.pack_type,b.country_ship_date from  pro_garments_production_dtls a,wo_po_color_size_breakdown b where a.color_size_break_down_id=b.id and a.mst_id=$data and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and  b.po_break_down_id='$po_break_down_id' and b.item_number_id='$item_number_id' and b.country_id='$country_id'";
	// echo $sql_colsize;die;
	$colsize_res = sql_select($sql_colsize);
	
	$country_ship_date = $colsize_res[0][csf('country_ship_date')];
	$pack_type = $colsize_res[0][csf('pack_type')];
	$update_shidate = execute_query("UPDATE pro_garments_production_mst set country_ship_date='$country_ship_date' WHERE id=$data");
	// echo $update_shidate;die;
	if($update_shidate)
	{		
		oci_commit($con); 
	}
	else
	{
		oci_rollback($con);
	}
	disconnect($con);
	/* 
	@end
	*/

	$variable_sql=sql_select("SELECT sewing_production from variable_settings_production where company_name='$company_id' and variable_list=1 and status_active=1");
	$sewing_level=$variable_sql[0][csf("sewing_production")];
	if($sewing_level==1)
	{
		$sql_dtls ="SELECT  a.country_ship_date, a.id,a.company_id,a.entry_break_down_type, a.garments_nature, a.po_break_down_id, a.challan_no, a.item_number_id, a.country_id, a.production_source, a.produced_by,a.shift_name, a.serving_company, a.sewing_line, a.location, a.embel_name, a.embel_type, a.production_date, sum(a.production_quantity) as production_quantity, a.production_source, $date_cond,  a.supervisor, a.carton_qty, a.remarks, a.floor_id, a.reject_qnty, a.alter_qnty, a.total_produced, a.yet_to_produced, a.spot_qnty,a.wo_order_id,a.currency_id,a.exchange_rate,a.rate,a.is_days_count from pro_garments_production_mst a  where   a.id='$data'  and a.production_type='5' and a.status_active=1 and a.is_deleted=0 group by  a.country_ship_date, a.id,a.company_id, a.entry_break_down_type, a.garments_nature, a.po_break_down_id, a.challan_no, a.item_number_id, a.country_id, a.production_source, a.produced_by,a.shift_name, a.serving_company, a.sewing_line, a.location, a.embel_name, a.embel_type, a.production_date,  a.production_source, production_hour, a.sewing_line, a.supervisor, a.carton_qty, a.remarks, a.floor_id, a.reject_qnty, a.alter_qnty, a.total_produced, a.yet_to_produced, a.spot_qnty,a.wo_order_id,a.currency_id,a.exchange_rate,a.rate,a.is_days_count  order by a.id";

	}
	else
	{
		$sql_dtls ="SELECT  a.country_ship_date, a.id,a.company_id,a.entry_break_down_type, a.garments_nature, a.po_break_down_id, a.challan_no, a.item_number_id, a.country_id, a.production_source, a.produced_by,a.shift_name, a.serving_company, a.sewing_line, a.location, a.embel_name, a.embel_type, a.production_date, sum(b.production_qnty) as production_quantity, a.production_source, $date_cond,  a.supervisor, a.carton_qty, a.remarks, a.floor_id, a.reject_qnty, a.alter_qnty, a.total_produced, a.yet_to_produced, a.spot_qnty,a.wo_order_id,a.currency_id,a.exchange_rate,a.rate,b.color_type_id,a.is_days_count from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and  a.id='$data'  and a.production_type='5' and a.status_active=1 and a.is_deleted=0 and b.production_type='5' and b.status_active=1 and b.is_deleted=0 and (b.color_size_break_down_id!=0 or b.color_size_break_down_id is not null) group by  a.country_ship_date, a.id,a.company_id, a.entry_break_down_type, a.garments_nature, a.po_break_down_id, a.challan_no, a.item_number_id, a.country_id, a.production_source, a.produced_by,a.shift_name, a.serving_company, a.sewing_line, a.location, a.embel_name, a.embel_type, a.production_date,  a.production_source, production_hour, a.sewing_line, a.supervisor, a.carton_qty, a.remarks, a.floor_id, a.reject_qnty, a.alter_qnty, a.total_produced, a.yet_to_produced, a.spot_qnty,a.wo_order_id,a.currency_id,a.exchange_rate,a.rate,b.color_type_id,a.is_days_count  order by a.id";

	}
  	// echo $sql_dtls;	  die;
	 
	$sqlResult =sql_select($sql_dtls);
	$country_ship_date = $sqlResult[0][csf('country_ship_date')];
	if($country_ship_date=='') $country_ship_date_cond=""; else $country_ship_date_cond="and a.country_ship_date='$country_ship_date'";
	if($country_ship_date=='') $country_ship_date_cond2=""; else $country_ship_date_cond2="and b.country_ship_date='$country_ship_date'";

	foreach($sqlResult as $result)
	{
		echo "$('#txt_sewing_date').val('".change_date_format($result[csf('production_date')])."');\n";
		echo "$('#cbo_source').val('".$result[csf('production_source')]."');\n";
		echo "load_drop_down( 'requires/sewing_output_controller', ".$result[csf('production_source')].", 'load_drop_down_sewing_output', 'sew_company_td' );\n";
		echo "$('#cbo_sewing_company').val('".$result[csf('serving_company')]."');\n";
		echo "load_drop_down( 'requires/sewing_output_controller',".$result[csf('serving_company')].", 'load_drop_down_location', 'location_td' );";
		echo "$('#cbo_location').val('".$result[csf('location')]."');\n";
		echo "load_drop_down( 'requires/sewing_output_controller', ".$result[csf('location')].", 'load_drop_down_floor', 'floor_td' );\n";
		echo "$('#cbo_floor').val('".$result[csf('floor_id')]."');\n";

		echo "load_drop_down( 'requires/sewing_output_controller', ".$result[csf('po_break_down_id')].", 'load_drop_down_color_type', 'color_type_td' );\n";
		echo "$('#cbo_color_type').val('".$result[csf('color_type_id')]."');\n";
		echo "$('#is_update_mood').val('1');\n";


		echo "load_drop_down( 'requires/sewing_output_controller', document.getElementById('cbo_floor').value+'_'+document.getElementById('cbo_location').value+'_'+document.getElementById('prod_reso_allo').value+'_'+document.getElementById('txt_sewing_date').value+'_'+document.getElementById('cbo_sewing_company').value+'_0', 'load_drop_down_sewing_line_floor', 'sewing_line_td' );\n";

		echo "$('#cbo_sewing_line').val('".$result[csf('sewing_line')]."');\n";
		//echo "get_php_form_data(".$result[csf('production_source')].",'line_disable_enable','requires/sewing_output_controller');\n";
		echo "fnc_line_disable_enable(".$result[csf('production_source')].");\n";

		if($result[csf('production_source')]==3)
		{
			echo "load_drop_down( 'requires/sewing_output_controller', '".$result[csf('company_id')]."_".$result[csf('serving_company')]."_".$result[csf('po_break_down_id')]."', 'load_drop_down_workorder', 'workorder_td' );\n";

			echo "$('#cbo_work_order').val('".$result[csf('wo_order_id')]."');\n";
			echo "$('#hidden_currency_id').val('".$result[csf('currency_id')]."');\n";
			echo "$('#hidden_exchange_rate').val('".$result[csf('exchange_rate')]."');\n";
			echo "$('#hidden_piece_rate').val('".$result[csf('rate')]."');\n";
			$rate_string=$result[csf('rate')]." ".$currency[$result[csf('currency_id')]];
			if(trim($rate_string)!="")
			{
				$rate_string="Work Order Rate ".$rate_string." /Pcs";
				echo "$('#workorder_rate_id').text('".$rate_string."');\n";
			}
			else
			{
				echo "$('#workorder_rate_id').text('');\n";
			}
		}
		// echo $result[csf('is_days_count')];die;
		if($result[csf('is_days_count')]==1)
		{
			echo "$('#is_size_set').prop('checked', true);\n";
			echo "$('#is_size_set').attr('disabled',true);\n";
		}
		/* else
		{
			echo "$('#is_size_set').prop('checked', false);\n";
			echo "$('#is_size_set').attr('disabled',false);\n";
		} */

		echo "$('#cbo_produced_by').val('".$result[csf('produced_by')]."');\n";
		echo "$('#cbo_shift_name').val('".$result[csf('shift_name')]."');\n";
		echo "$('#txt_reporting_hour').val('".$result[csf('production_hour')]."');\n";
		echo "$('#txt_super_visor').val('".$result[csf('supervisor')]."');\n";
		echo "$('#txt_sewing_qty').val('');\n";
		echo "$('#txt_sewing_qty').val('".$result[csf('production_quantity')]."');\n";
		echo "$('#txt_reject_qnty').val('".$result[csf('reject_qnty')]."');\n";
		echo "$('#txt_alter_qnty').val('".$result[csf('alter_qnty')]."');\n";
		echo "$('#txt_spot_qnty').val('".$result[csf('spot_qnty')]."');\n";
		echo "$('#txt_challan').val('".$result[csf('challan_no')]."');\n";
		echo "$('#txt_remark').val('".$result[csf('remarks')]."');\n";
		echo "$('#country_ship_date').val('".$result[csf('country_ship_date')]."');\n";
		//echo "$('#is_size_set').val('".$result[csf('is_days_count')]."');\n ";
		//echo "$('#is_size_set').attr('disabled');\n ";
		if($sewing_level==1)
		{
			$dataArray=sql_select("SELECT SUM(CASE WHEN a.production_type=4   THEN a.production_quantity END) as totalinput,SUM(CASE WHEN a.production_type=5  THEN a.production_quantity ELSE 0 END) as totalsewing from pro_garments_production_mst a  WHERE   a.po_break_down_id=".$result[csf('po_break_down_id')]." and a.item_number_id=".$result[csf('item_number_id')]." AND a.country_id=".$result[csf('country_id')]." and a.production_source=".$result[csf('production_source')]." and a.serving_company=".$result[csf('serving_company')]." $country_ship_date_cond and a.status_active=1 and a.is_deleted=0  ");
		}
		else
		{
			$dataArray=sql_select("SELECT SUM(CASE WHEN b.production_type=4 THEN b.production_qnty END) as totalinput,SUM(CASE WHEN b.production_type=5 THEN b.production_qnty ELSE 0 END) as totalsewing from WO_PO_COLOR_SIZE_BREAKDOWN a,pro_garments_production_dtls b,pro_garments_production_mst c WHERE a.id=b.color_size_break_down_id and b.mst_id=c.id and a.po_break_down_id=".$result[csf('po_break_down_id')]." and a.item_number_id=".$result[csf('item_number_id')]." AND a.country_id=".$result[csf('country_id')]." and c.production_source=".$result[csf('production_source')]." and c.serving_company=".$result[csf('serving_company')]." $country_ship_date_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		}

 		foreach($dataArray as $row)
		{
			echo "$('#txt_input_quantity').val('".$row[csf('totalinput')]."');\n";
			echo "$('#txt_cumul_sewing_qty').val('".$row[csf('totalsewing')]."');\n";
			$yet_to_produced = $row[csf('totalinput')]-$row[csf('totalsewing')];
			echo "$('#txt_yet_to_sewing').val('".$yet_to_produced."');\n";
		}
		$dft_id=""; $alt_save_data=""; $spt_save_data=""; $altType_id=""; $sptType_id=""; $altpoint_id=""; $sptpoint_id="";
		$Reject_save_data="";$Rejectpoint_id="";$RejectType_id="";
		$front_save_data="";$frontpoint_id="";$frontType_id=""; $bk_save_data="";$bktpoint_id="";$bktType_id=""; $wt_save_data="";$wttpoint_id="";$wttType_id="";
		 $me_save_data="";$metpoint_id="";$metType_id="";
		 $inside_save_data="";$insidepoint_id="";$insideType_id="";
		 $topside_save_data="";$topsidepoint_id="";$topsideType_id="";
		 $collar_save_data="";$collarpoint_id="";$collarType_id="";
		 $armhole_save_data="";$armholepoint_id="";$armholeType_id="";
		 $make_save_data="";$makepoint_id="";$makeType_id="";
		$defect_sql=sql_select("SELECT id, po_break_down_id, defect_type_id, defect_point_id, defect_qty from pro_gmts_prod_dft where mst_id='".$result[csf('id')]."' and status_active=1 and is_deleted=0 and production_type='5'");
		//echo "select id, po_break_down_id, defect_type_id, defect_point_id, defect_qty from pro_gmts_prod_dft where mst_id='".$result[csf('id')]."' and status_active=1 and is_deleted=0 and production_type='5'";
		foreach($defect_sql as $dft_row)
		{
			if($dft_row[csf('defect_type_id')]==1)
			{
				if($alt_save_data=="") $alt_save_data=$dft_row[csf('id')].'**'.$dft_row[csf('defect_point_id')].'**'.$dft_row[csf('defect_qty')]; else $alt_save_data.=','.$dft_row[csf('id')].'**'.$dft_row[csf('defect_point_id')].'**'.$dft_row[csf('defect_qty')];
				if($altpoint_id=="") $altpoint_id=$dft_row[csf('defect_point_id')]; else $altpoint_id.=','.$dft_row[csf('defect_point_id')];
				$altType_id=$dft_row[csf('defect_type_id')];
			}

			if($dft_row[csf('defect_type_id')]==2)
			{
				if($spt_save_data=="") $spt_save_data=$dft_row[csf('id')].'**'.$dft_row[csf('defect_point_id')].'**'.$dft_row[csf('defect_qty')]; else $spt_save_data.=','.$dft_row[csf('id')].'**'.$dft_row[csf('defect_point_id')].'**'.$dft_row[csf('defect_qty')];
				if($sptpoint_id=="") $sptpoint_id=$dft_row[csf('defect_point_id')]; else $sptpoint_id.=','.$dft_row[csf('defect_point_id')];
				$sptType_id=$dft_row[csf('defect_type_id')];
			}
			if($dft_row[csf('defect_type_id')]==3) //Reject Part
			{
				if($Reject_save_data=="") $Reject_save_data=$dft_row[csf('id')].'**'.$dft_row[csf('defect_point_id')].'**'.$dft_row[csf('defect_qty')]; else $Reject_save_data.=','.$dft_row[csf('id')].'**'.$dft_row[csf('defect_point_id')].'**'.$dft_row[csf('defect_qty')];
				if($Rejectpoint_id=="") $Rejectpoint_id=$dft_row[csf('defect_point_id')]; else $Rejectpoint_id.=','.$dft_row[csf('defect_point_id')];
				$RejectType_id=$dft_row[csf('defect_type_id')];
			}
			if($dft_row[csf('defect_type_id')]==4) //Front Part
			{
				if($front_save_data=="") $front_save_data=$dft_row[csf('id')].'**'.$dft_row[csf('defect_point_id')].'**'.$dft_row[csf('defect_qty')]; else $front_save_data.=','.$dft_row[csf('id')].'**'.$dft_row[csf('defect_point_id')].'**'.$dft_row[csf('defect_qty')];
				if($frontpoint_id=="") $frontpoint_id=$dft_row[csf('defect_point_id')]; else $frontpoint_id.=','.$dft_row[csf('defect_point_id')];
				$frontType_id=$dft_row[csf('defect_type_id')];
			}

			if($dft_row[csf('defect_type_id')]==5)//Back
			{
				if($bk_save_data=="") $bk_save_data=$dft_row[csf('id')].'**'.$dft_row[csf('defect_point_id')].'**'.$dft_row[csf('defect_qty')]; else $bk_save_data.=','.$dft_row[csf('id')].'**'.$dft_row[csf('defect_point_id')].'**'.$dft_row[csf('defect_qty')];
				if($bktpoint_id=="") $bktpoint_id=$dft_row[csf('defect_point_id')]; else $bktpoint_id.=','.$dft_row[csf('defect_point_id')];
				$bktType_id=$dft_row[csf('defect_type_id')];
			}
			if($dft_row[csf('defect_type_id')]==6) //West Band
			{
				if($wt_save_data=="") $wt_save_data=$dft_row[csf('id')].'**'.$dft_row[csf('defect_point_id')].'**'.$dft_row[csf('defect_qty')]; else $wt_save_data.=','.$dft_row[csf('id')].'**'.$dft_row[csf('defect_point_id')].'**'.$dft_row[csf('defect_qty')];
				if($wttpoint_id=="") $wttpoint_id=$dft_row[csf('defect_point_id')]; else $wttpoint_id.=','.$dft_row[csf('defect_point_id')];
				$wttType_id=$dft_row[csf('defect_type_id')];
			}
			if($dft_row[csf('defect_type_id')]==7)//Mesure
			{
				if($me_save_data=="") $me_save_data=$dft_row[csf('id')].'**'.$dft_row[csf('defect_point_id')].'**'.$dft_row[csf('defect_qty')]; else $me_save_data.=','.$dft_row[csf('id')].'**'.$dft_row[csf('defect_point_id')].'**'.$dft_row[csf('defect_qty')];
				if($metpoint_id=="") $metpoint_id=$dft_row[csf('defect_point_id')]; else $metpoint_id.=','.$dft_row[csf('defect_point_id')];
				$metType_id=$dft_row[csf('defect_type_id')];
			}
			if($dft_row[csf('defect_type_id')]==8) //Inside Part
			{
				if($inside_save_data=="") $inside_save_data=$dft_row[csf('id')].'**'.$dft_row[csf('defect_point_id')].'**'.$dft_row[csf('defect_qty')]; else $inside_save_data.=','.$dft_row[csf('id')].'**'.$dft_row[csf('defect_point_id')].'**'.$dft_row[csf('defect_qty')];
				if($insidepoint_id=="") $insidepoint_id=$dft_row[csf('defect_point_id')]; else $insidepoint_id.=','.$dft_row[csf('defect_point_id')];
				$insideType_id=$dft_row[csf('defect_type_id')];
			}
			if($dft_row[csf('defect_type_id')]==9) //Topside Part
			{
				if($topside_save_data=="") $topside_save_data=$dft_row[csf('id')].'**'.$dft_row[csf('defect_point_id')].'**'.$dft_row[csf('defect_qty')]; else $topside_save_data.=','.$dft_row[csf('id')].'**'.$dft_row[csf('defect_point_id')].'**'.$dft_row[csf('defect_qty')];
				if($topsidepoint_id=="") $topsidepoint_id=$dft_row[csf('defect_point_id')]; else $topsidepoint_id.=','.$dft_row[csf('defect_point_id')];
				$topsideType_id=$dft_row[csf('defect_type_id')];
			}
			if($dft_row[csf('defect_type_id')]==10) //Collar Side Part
			{
				if($collar_save_data=="") $collar_save_data=$dft_row[csf('id')].'**'.$dft_row[csf('defect_point_id')].'**'.$dft_row[csf('defect_qty')]; else $collar_save_data.=','.$dft_row[csf('id')].'**'.$dft_row[csf('defect_point_id')].'**'.$dft_row[csf('defect_qty')];
				if($collarpoint_id=="") $collarpoint_id=$dft_row[csf('defect_point_id')]; else $collarpoint_id.=','.$dft_row[csf('defect_point_id')];
				$collarType_id=$dft_row[csf('defect_type_id')];
			}
			if($dft_row[csf('defect_type_id')]==11) //Armhole Part
			{
				if($armhole_save_data=="") $armhole_save_data=$dft_row[csf('id')].'**'.$dft_row[csf('defect_point_id')].'**'.$dft_row[csf('defect_qty')]; else $armhole_save_data.=','.$dft_row[csf('id')].'**'.$dft_row[csf('defect_point_id')].'**'.$dft_row[csf('defect_qty')];
				if($armholepoint_id=="") $armholepoint_id=$dft_row[csf('defect_point_id')]; else $armholepoint_id.=','.$dft_row[csf('defect_point_id')];
				$armholeType_id=$dft_row[csf('defect_type_id')];
			}
			if($dft_row[csf('defect_type_id')]==12) //Makecheck Part
			{
				if($make_save_data=="") $make_save_data=$dft_row[csf('id')].'**'.$dft_row[csf('defect_point_id')].'**'.$dft_row[csf('defect_qty')]; else $make_save_data.=','.$dft_row[csf('id')].'**'.$dft_row[csf('defect_point_id')].'**'.$dft_row[csf('defect_qty')];
				if($makepoint_id=="") $makepoint_id=$dft_row[csf('defect_point_id')]; else $makepoint_id.=','.$dft_row[csf('defect_point_id')];
				$makeType_id=$dft_row[csf('defect_type_id')];
			}
		}

		echo "$('#save_dataReject').val('".$Reject_save_data."');\n";
		echo "$('#allReject_defect_id').val('".$Rejectpoint_id."');\n";
		echo "$('#defectReject_type_id').val('".$RejectType_id."');\n";

		echo "$('#save_dataFront').val('".$front_save_data."');\n";
		echo "$('#allFront_defect_id').val('".$frontpoint_id."');\n";
		echo "$('#defectFront_type_id').val('".$frontType_id."');\n";

		echo "$('#save_dataBack').val('".$bk_save_data."');\n";
		echo "$('#allBack_defect_id').val('".$sptpoint_id."');\n";
		echo "$('#defectBack_type_id').val('".$bktType_id."');\n";

		echo "$('#save_dataWest').val('".$wt_save_data."');\n";
		echo "$('#allWest_defect_id').val('".$wttpoint_id."');\n";
		echo "$('#defectWest_type_id').val('".$wttType_id."');\n";

		echo "$('#save_dataMeasure').val('".$me_save_data."');\n";
		echo "$('#allMeasure_defect_id').val('".$metpoint_id."');\n";
		echo "$('#defectMeasure_type_id').val('".$metType_id."');\n";

		echo "$('#save_data').val('".$alt_save_data."');\n";
		echo "$('#all_defect_id').val('".$altpoint_id."');\n";
		echo "$('#defect_type_id').val('".$altType_id."');\n";

		echo "$('#save_dataSpot').val('".$spt_save_data."');\n";
		echo "$('#allSpot_defect_id').val('".$sptpoint_id."');\n";
		echo "$('#defectSpot_type_id').val('".$sptType_id."');\n";

		echo "$('#save_dataInside').val('".$inside_save_data."');\n";
		echo "$('#allInside_defect_id').val('".$insidepoint_id."');\n";
		echo "$('#defectInside_type_id').val('".$insideType_id."');\n";

		echo "$('#save_dataTopside').val('".$topside_save_data."');\n";
		echo "$('#allTopside_defect_id').val('".$topsidepoint_id."');\n";
		echo "$('#defectTopside_type_id').val('".$topsideType_id."');\n";

		echo "$('#save_dataCollar').val('".$collar_save_data."');\n";
		echo "$('#allCollar_defect_id').val('".$collarpoint_id."');\n";
		echo "$('#defectCollar_type_id').val('".$collarType_id."');\n";

		echo "$('#save_dataArmhole').val('".$armhole_save_data."');\n";
		echo "$('#allArmhole_defect_id').val('".$armholepoint_id."');\n";
		echo "$('#defectArmhole_type_id').val('".$armholeType_id."');\n";

		echo "$('#save_dataMake').val('".$make_save_data."');\n";
		echo "$('#allMake_defect_id').val('".$makepoint_id."');\n";
		echo "$('#defectMake_type_id').val('".$makeType_id."');\n";

		echo "$('#txt_mst_id').val('".$result[csf('id')]."');\n";
		echo "$('#txt_sys_chln').val('".$result[csf('id')]."');\n";
		echo "$('#txt_sewing_date').removeAttr('onChange');\n";

 		echo "set_button_status(1, permission, 'fnc_sewing_output_entry',1,1);\n";

		//break down of color and size------------------------------------------
 		//#############################################################################################//
		// order wise - color level, color and size level
		$color_library=return_library_array( "select id, color_name from lib_color",'id','color_name');
		$size_library=return_library_array( "select id, size_name from lib_size",'id','size_name');

		$variableSettings = $result[csf('entry_break_down_type')];
		$variableSettingsRej = $result[csf('break_down_type_rej')];

		//$variableSettings=2;

		if( $variableSettings!=1 ) // gross level
		{
			$po_id = $result[csf('po_break_down_id')];
			$item_id = $result[csf('item_number_id')];
			$country_id = $result[csf('country_id')];

			$sql_dtls = sql_select("SELECT color_size_break_down_id, production_qnty, reject_qty, size_number_id, color_number_id, bundle_qty from  pro_garments_production_dtls a, wo_po_color_size_breakdown b where a.mst_id=$data and a.status_active=1 and a.color_size_break_down_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' $country_ship_date_cond2 ");
			foreach($sql_dtls as $row)
			{
				if( $variableSettings==2 ) $index = $row[csf('color_number_id')]; else $index = $row[csf('size_number_id')].$color_arr[$row[csf("color_number_id")]].$row[csf('color_number_id')];
			  	$amountArr[$index] = $row[csf('production_qnty')];
				$rejectArr[$index] = $row[csf('reject_qty')];
				$bundleArr[$index] = $row[csf('bundle_qty')];

			  	$amountColorSizeArr[$row['COLOR_SIZE_BREAK_DOWN_ID']] = $row[csf('production_qnty')];
				$rejectColorSizeArr[$row['COLOR_SIZE_BREAK_DOWN_ID']] = $row[csf('reject_qty')];
				$bundleColorSizeArr[$row['COLOR_SIZE_BREAK_DOWN_ID']] = $row[csf('bundle_qty')];
			}

			if( $variableSettings==2 ) // color level
			{
				if($db_type==0)
				{

					$sql = "SELECT id, item_number_id, size_number_id, color_number_id, order_quantity, sum(plan_cut_qnty) as plan_cut_qnty, (select sum(CASE WHEN pdtls.color_size_break_down_id=wo_po_color_size_breakdown.id then pdtls.production_qnty ELSE 0 END) from pro_garments_production_dtls pdtls where pdtls.production_type=4 and pdtls.is_deleted=0 ) as production_qnty, (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.production_qnty ELSE 0 END) from pro_garments_production_dtls cur where cur.production_type=5 and cur.is_deleted=0 ) as cur_production_qnty, (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.reject_qty ELSE 0 END) from pro_garments_production_dtls cur where cur.production_type=5 and cur.is_deleted=0 ) as reject_qty
					from wo_po_color_size_breakdown
					where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 group by color_number_id";
				}
				else
				{
					$sql = "SELECT a.color_order, a.item_number_id, a.color_number_id, sum(a.order_quantity) as order_quantity, sum(a.plan_cut_qnty) as plan_cut_qnty,
							sum(CASE WHEN c.production_type=4 $sewing_line_cond then b.production_qnty ELSE 0 END) as production_qnty,
							sum(CASE WHEN c.production_type=5 $sewing_line_cond then b.production_qnty ELSE 0 END) as cur_production_qnty,
							sum(CASE WHEN c.production_type=5 $sewing_line_cond then b.reject_qty ELSE 0 END) as reject_qty
							from wo_po_color_size_breakdown a
							left join pro_garments_production_dtls b on a.id=b.color_size_break_down_id
							left join pro_garments_production_mst c on c.id=b.mst_id
							where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' $country_ship_date_cond and a.is_deleted=0 and a.status_active=1 group by a.color_order, a.color_number_id";

				}
			}
			else if( $variableSettings==3 ) //color and size level
			{
				 $country_ship_date_cond3 = str_replace("b.","c.",$country_ship_date_cond2);
				 $dtlsData = sql_select("SELECT a.color_size_break_down_id,
									sum(CASE WHEN a.production_type=4 $sewing_line_cond2 then a.production_qnty ELSE 0 END) as production_qnty,
									sum(CASE WHEN a.production_type=5 $sewing_line_cond2 then a.production_qnty ELSE 0 END) as cur_production_qnty,
									sum(CASE WHEN a.production_type=5 $sewing_line_cond2 then a.reject_qty ELSE 0 END) as reject_qty
									from pro_garments_production_dtls a,pro_garments_production_mst b,wo_po_color_size_breakdown c where a.mst_id=b.id and c.id=a.color_size_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' $country_ship_date_cond3 and a.production_type in(4,5) group by a.color_size_break_down_id ");

				foreach($dtlsData as $row)
				{
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['iss']= $row[csf('production_qnty')];
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv']= $row[csf('cur_production_qnty')];
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rej']= $row[csf('reject_qty')];
				}

				$sql = "SELECT a.color_order,a.id, a.item_number_id, a.size_number_id, a.color_number_id, a.order_quantity, a.plan_cut_qnty
					from wo_po_color_size_breakdown a
					where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active=1 $country_ship_date_cond order by a.color_order, a.size_order";
			}
			else // by default color and size level
			{
				$country_ship_date_cond3 = str_replace("b.","c.",$country_ship_date_cond2);
				$dtlsData = sql_select("SELECT a.color_size_break_down_id,
									sum(CASE WHEN a.production_type=4 $sewing_line_cond2 then a.production_qnty ELSE 0 END) as production_qnty,
									sum(CASE WHEN a.production_type=5 $sewing_line_cond2 then a.production_qnty ELSE 0 END) as cur_production_qnty,
									sum(CASE WHEN a.production_type=5 $sewing_line_cond2 then a.reject_qty ELSE 0 END) as reject_qty
									from pro_garments_production_dtls a,pro_garments_production_mst b,wo_po_color_size_breakdown c where a.mst_id=b.id and c.id=a.color_size_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' $country_ship_date_cond3 and a.production_type in(4,5) group by a.color_size_break_down_id ");

				foreach($dtlsData as $row)
				{
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['iss']= $row[csf('production_qnty')];
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv']= $row[csf('cur_production_qnty')];
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rej']= $row[csf('reject_qty')];
				}
				$sql = "SELECT a.color_order,a.id, a.item_number_id, a.size_number_id, a.color_number_id, a.order_quantity, a.plan_cut_qnty
					from wo_po_color_size_breakdown a
					where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active=1 $country_ship_date_cond order by a.color_order,a.size_order";
			}

			if($variableSettingsRej!=1)
			{
				$disable="";
			}
			else
			{
				$disable="disabled";
			}
			// echo $sql;die;
 			$colorResult = sql_select($sql);
 			//print_r($sql);die;
			$colorHTML="";
			$colorID='';
			$chkColor = array();
			$i=0;$totalQnty=0;$colorWiseTotal=0;
			$tot_order_qty = 0;
			foreach($colorResult as $color)
			{
				if( $variableSettings==2 ) // color level
				{
					$amount = $amountArr[$color[csf("color_number_id")]];
					$rejectAmt = $rejectArr[$color[csf("color_number_id")]];
					$bundle_qnty = $bundleArr[$color[csf("color_number_id")]];
					$colorHTML .='<tr><td>'.$color_library[$color[csf("color_number_id")]].'</td><td><input type="text" name="txt_color" id="colSize_'.($i+1).'" style="width:60px"  class="text_boxes_numeric" placeholder="'.($color[csf("production_qnty")]-$color[csf("cur_production_qnty")]+$amount).'" value="'.$amount.'" onblur="fn_colorlevel_total('.($i+1).')" onkeypress="return isNumber(event)"></td><td><input type="text" name="txtColSizeRej" id="colSizeRej_'.($i+1).'" style="width:60px" class="text_boxes_numeric" placeholder="Rej." value="'.$rejectAmt.'" onblur="fn_colorRej_total('.($i+1).') '.$disable.'" onkeypress="return isNumber(event)"><input type="text" name="colorSizeBundleQnty" id="colorSizeBundleQnty_'.$color[csf("color_number_id")].($i+1).'" placeholder="Bundle Qty" value="'.$bundle_qnty.'"  class="text_boxes_numeric" style="width:80px"></td></tr>';
					$totalQnty += $amount;
					$totalRejQnty += $rejectAmt;
					$colorID .= $color[csf("color_number_id")].",";
				}
				else //color and size level
				{
					$index = $color[csf("size_number_id")].$color_arr[$color[csf("color_number_id")]].$color[csf("color_number_id")];
					
					if( !in_array( $color[csf("color_number_id")], $chkColor ) )
					{
						if( $i!=0 ) $colorHTML .= "</table></div>";
						$i=0;$colorWiseTotal=0;
						$colorHTML .= '<h3 align="left" id="accordion_h'.$color[csf("color_number_id")].'" style="width:300px" class="accordion_h" onClick="accordion_menu( this.id,\'content_search_panel_'.$color[csf("color_number_id")].'\', \'\',1)"> <span id="accordion_h'.$color[csf("color_number_id")].'span">+</span>'.$color_library[$color[csf("color_number_id")]].' : <span id="total_'.$color[csf("color_number_id")].'"></span> </h3>';
						$colorHTML .= '<div id="content_search_panel_'.$color[csf("color_number_id")].'" style="display:none" class="accord_close"> <div style="padding-left: 40px;text-align:center"><input type="checkbox" onClick="active_placeholder_qty(' . $color[csf("color_number_id")] . ')" id="set_all_' . $color[csf("color_number_id")] . '">&nbsp;Available Qty Auto Fill</div> <table id="table_'.$color[csf("color_number_id")].'">';
						$chkColor[] = $color[csf("color_number_id")];
						$totalFn .= "fn_total(".$color[csf("color_number_id")].");";
					}
 					$colorID .= $color[csf("size_number_id")]."*".$color[csf("color_number_id")].",";

					$iss_qnty=$color_size_qnty_array[$color[csf('id')]]['iss'];
					$rcv_qnty=$color_size_qnty_array[$color[csf('id')]]['rcv'];
					$amount = $amountArr[$index];
					$bundle_qnty = $bundleArr[$index]; 
					$rej_qnty=$rejectArr[$index];
					//$color_size_qnty_array[$color[csf('id')]]['rej'];
					$color_size_breakdown_id = $color[csf('id')];
					$amount 		= $amountColorSizeArr[$color_size_breakdown_id];
					$rej_qnty 		= $rejectColorSizeArr[$color_size_breakdown_id];
					$bundle_qnty	= $bundleColorSizeArr[$color_size_breakdown_id];

					$colorHTML .='<tr><td>'.$size_library[$color[csf("size_number_id")]].'</td><td><input type="text" name="colorSize" data-colorSizeBreakdown="'.$color_size_breakdown_id.'" id="colSize_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:80px" placeholder="'.($iss_qnty-$rcv_qnty+$amount).'" onblur="fn_total('.$color[csf("color_number_id")].','.($i+1).')" value="'.$amount.'" onkeypress="return isNumber(event)"><input type="text" name="colorSizeRej" data-colorSizeBreakdown="'.$color_size_breakdown_id.'" id="colSizeRej_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:50px" placeholder="Rej. Qty" onblur="fn_total_rej('.$color[csf("color_number_id")].','.($i+1).')" value="'.$rej_qnty.'" '.$disable.' onkeypress="return isNumber(event)"><input type="text" name="colorSizePOQnty" id="colorSizePOQnty_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:70px" value="'.$color[csf("order_quantity")].'" readonly disabled> <input type="text" name="colorSizeBundleQnty" data-colorSizeBreakdown="'.$color_size_breakdown_id.'" id="colorSizeBundleQnty_'.$color[csf("color_number_id")].($i+1).'" placeholder="Bundle Qty" value="'.$bundle_qnty.'"  class="text_boxes_numeric" style="width:80px"></td></tr>';
					$colorWiseTotal += $amount;
				}
				$i++;
				$tot_order_qty += $color['ORDER_QUANTITY'];
			}
			//echo $colorHTML;die;
			if( $variableSettings==2 ){ $colorHTML = '<table id="table_color" class="rpt_table"><thead><th width="70">Color</th><th width="60">Quantity</th><th width="60">Reject</th></thead><tbody>'.$colorHTML.'<tbody><tfoot><tr><th>Total</th><th><input type="text" id="total_color" placeholder="'.$totalQnty.'" value="'.$totalQnty.'" class="text_boxes_numeric" style="width:60px" ></th><th><input type="text" id="total_color_rej" placeholder="'.$totalRejQnty.'" value="'.$totalRejQnty.'" class="text_boxes_numeric" style="width:60px" ></th></tr></tfoot></table>'; }
			echo "$('#breakdown_td_id').html('".addslashes($colorHTML)."');\n";
			if( $variableSettings==3 )echo "$totalFn;\n";
			$colorList = substr($colorID,0,-1);
			echo "$('#hidden_colorSizeID').val('".$colorList."');\n";
			echo "$('#txt_order_qty').val('".$tot_order_qty."');\n";
		}//end if condtion
		//#############################################################################################//
	}
 	exit();
}

//pro_garments_production_mst
if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	// echo"<pre>";print_r($process);die;
	extract(check_magic_quote_gpc( $process ));

	if(str_replace("'","",$cbo_source)==1)
	{
		$is_style_attach_in_reso_allo=return_field_value("is_locked","variable_settings_production","variable_list=60 and company_name=$cbo_company_name");
		$attach_style_arr = array();
		if($is_style_attach_in_reso_allo==1)
		{
			$sql = "SELECT b.id from PROD_RESOURCE_COLOR_SIZE b,PROD_RESOURCE_MST c, PROD_RESOURCE_DTLS_MAST d where b.mst_id=c.id and c.id=d.mst_id and b.dtls_id=d.id and b.po_id=$hidden_po_break_down_id and c.id=$cbo_sewing_line AND d.from_date >=$txt_sewing_date AND d.to_date <= $txt_sewing_date and  c.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
			$res = sql_select($sql);
			if(count($res)==0)
			{
				echo "785**This style is not assigned for the selected line in the actual resource entry.";die();
			}
		}
	}
	// echo "10**".$sql;die;
	$is_projected_po_allow=return_field_value("production_entry","variable_settings_production","variable_list=58 and company_name=$cbo_company_name");

	if($is_projected_po_allow ==2)
	{
		$is_projected_po=return_field_value("is_confirmed","wo_po_break_down","status_active in(1,2,3) and id=$hidden_po_break_down_id");
		if($is_projected_po==2)
		{
			echo "786**Projected PO is not allowed to production. Please check variable settings";die();
		}
	}

	
	$sew_prod_hour_validatin=return_field_value("distribute_qnty","variable_settings_production","variable_list=86 and company_name=$cbo_company_name");

	if($sew_prod_hour_validatin ==1)
	{
		$reportingHourArr = explode(":", str_replace("'","",$txt_reporting_hour));
		$curHour = date('H',time());
		
		if($reportingHourArr[0] > $curHour)
		{
			echo "786**Next hour entry not allow. Please check variable settings";die();
		}
	}

	$is_control=return_field_value("is_control","variable_settings_production","company_name=$cbo_company_name and variable_list=33 and page_category_id=29");
	if(!str_replace("'","",$sewing_production_variable)) $sewing_production_variable=3;
	$sewing_line = str_replace("'","",$cbo_sewing_line);

	if(str_replace("'","",$country_ship_date)=="") $country_ship_date_cond=""; else $country_ship_date_cond=" and country_ship_date=$country_ship_date";
	if(str_replace("'","",$country_ship_date)=="") $country_ship_date_cond2=""; else $country_ship_date_cond2=" and c.country_ship_date=$country_ship_date";

	if ($operation==0) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }

		$id= return_next_id_by_sequence(  "pro_gar_production_mst_seq",   "pro_garments_production_mst", $con );
		$txt_challan_no=(str_replace("'", "", $txt_challan)==0)? $id : $txt_challan;
		$field_array1="id, garments_nature, company_id, challan_no, po_break_down_id, item_number_id, country_id, production_source, serving_company, location, produced_by, shift_name, production_date, production_quantity, production_type, entry_break_down_type, break_down_type_rej, sewing_line, supervisor, production_hour, remarks, floor_id, alter_qnty, reject_qnty, total_produced, yet_to_produced, prod_reso_allo, spot_qnty,wo_order_id,currency_id, exchange_rate, rate, amount,is_days_count, inserted_by, insert_date,country_ship_date";

		$rate=str_replace("'","",$hidden_piece_rate);
		if($rate!="") {  $amount=$rate*str_replace("'","",$txt_sewing_qty);}

		
		if($is_size_set=='true')
		{
			$is_days_count = 1;
			
		}
		// echo "10**".$is_size_set."=".$is_days_count;die;
		// echo 10 ."**". $is_days_count ;die;

		if($db_type==0)
		{
			$data_array1="(".$id.",".$garments_nature.",".$cbo_company_name.",".$txt_challan_no.",".$hidden_po_break_down_id.",".$cbo_item_name.",".$cbo_country_name.",".$cbo_source.",".$cbo_sewing_company.",".$cbo_location.",".$cbo_produced_by.",".$cbo_shift_name.",".$txt_sewing_date.",".$txt_sewing_qty.",5,".$sewing_production_variable.",".$sewing_production_variable_rej.",".$cbo_sewing_line.",".$txt_super_visor.",".$txt_reporting_hour.",".$txt_remark.",".$cbo_floor.",".$txt_alter_qnty.",".$txt_reject_qnty.",".$txt_cumul_sewing_qty.",".$txt_yet_to_sewing.",".$prod_reso_allo.",".$txt_spot_qnty.",".$cbo_work_order.",".$hidden_currency_id.",".$hidden_exchange_rate.",'".$rate."','".$amount."',".$user_id.",'".$pc_date_time."','".$country_ship_date."')";
		}
		else
		{
			$txt_reporting_hour=str_replace("'","",$txt_sewing_date)." ".str_replace("'","",$txt_reporting_hour);
			$txt_reporting_hour="to_date('".$txt_reporting_hour."','DD MONTH YYYY HH24:MI:SS')";
			$data_array1="INSERT INTO pro_garments_production_mst (".$field_array1.") VALUES (".$id.",".$garments_nature.",".$cbo_company_name.",".$txt_challan_no.",".$hidden_po_break_down_id.",".$cbo_item_name.",".$cbo_country_name.",".$cbo_source.",".$cbo_sewing_company.",".$cbo_location.",".$cbo_produced_by.",".$cbo_shift_name.",".$txt_sewing_date.",".$txt_sewing_qty.",5,".$sewing_production_variable.",".$sewing_production_variable_rej.",".$cbo_sewing_line.",".$txt_super_visor.",".$txt_reporting_hour.",".$txt_remark.",".$cbo_floor.",".$txt_alter_qnty.",".$txt_reject_qnty.",".$txt_cumul_sewing_qty.",".$txt_yet_to_sewing.",".$prod_reso_allo.",".$txt_spot_qnty.",".$cbo_work_order.",".$hidden_currency_id.",".$hidden_exchange_rate.",'".$rate."','".$amount."','".$is_days_count."',".$user_id.",'".$pc_date_time."',".$country_ship_date.")";
		}


		$field_array="id, mst_id, production_type, color_size_break_down_id, production_qnty, reject_qty,bundle_qty,color_type_id";


		$dtlsData = sql_select("SELECT a.color_size_break_down_id,
										sum(CASE WHEN a.production_type=4 and b.sewing_line=$sewing_line then a.production_qnty ELSE 0 END) as production_qnty,
										sum(CASE WHEN a.production_type=5 and b.sewing_line=$sewing_line then a.production_qnty ELSE 0 END) as cur_production_qnty
										from wo_po_color_size_breakdown c,pro_garments_production_dtls a,pro_garments_production_mst b
										where c.id=a.color_size_break_down_id and a.mst_id=b.id and b.po_break_down_id=$hidden_po_break_down_id and b.item_number_id=$cbo_item_name and b.country_id=$cbo_country_name $country_ship_date_cond2 and a.color_size_break_down_id!=0 and a.production_type in(4,5) and a.status_active=1 and b.status_active=1  
										group by a.color_size_break_down_id");
		$color_pord_data=array();
		foreach($dtlsData as $row)
		{
			$color_pord_data[$row[csf("color_size_break_down_id")]]=$row[csf('production_qnty')]-$row[csf("cur_production_qnty")];
		}

		if(str_replace("'","",$sewing_production_variable)==2)//color level wise
		{
			$color_sizeID_arr=sql_select( "select id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name $country_ship_date_cond  and status_active=1 and is_deleted=0 order by id" );
			$colSizeID_arr=array();
			foreach($color_sizeID_arr as $val){
				$index = $val[csf("color_number_id")];
				$colSizeID_arr[$index]=$val[csf("id")];
			}

			$rowExBundle = array_filter(explode("**",$colorBundleVal));
			foreach($rowExBundle as $rowR=>$valR)
			{
				$colorSizeBunIDArr = explode("*",$valR);
				//echo $colorSizeBunIDArr[0]; die;
				$BunQtyArr[$colorSizeBunIDArr[0]]=$colorSizeBunIDArr[1];
			}

			// $colorIDvalue concate as colorID*Value**colorID*Value -------------------------//
			$rowExRej = array_filter(explode("**",$colorIDvalueRej));
			foreach($rowExRej as $rowR=>$valR)
			{
				$colorSizeRejIDArr = explode("*",$valR);
				//echo $colorSizeRejIDArr[0]; die;
				$rejQtyArr[$colorSizeRejIDArr[0]]=$colorSizeRejIDArr[1];
			}

 			$rowEx = array_filter(explode("**",$colorIDvalue));
 			//$dtls_id=return_next_id("id", "pro_garments_production_dtls", 1);
			$data_array="";$j=0;
			foreach($rowEx as $rowE=>$val)
			{
				$colorSizeNumberIDArr = explode("*",$val);

				if($colSizeID_arr[$colorSizeNumberIDArr[0]]!="")
				{
					//4 for Sewing Input Entry
					$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",   "pro_garments_production_dtls", $con );
					if($j==0)$data_array = "(".$dtls_id.",".$id.",5,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."','".$rejQtyArr[$colorSizeNumberIDArr[0]]."','".$BunQtyArr[$colorSizeNumberIDArr[0]]."',".$cbo_color_type.")";
					else $data_array .= ",(".$dtls_id.",".$id.",5,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."','".$rejQtyArr[$colorSizeNumberIDArr[0]]."','".$BunQtyArr[$colorSizeNumberIDArr[0]]."',".$cbo_color_type.")";
					//$dtls_id=$dtls_id+1;
	 				$j++;
	 			}
	 			else
	 			{
	 				echo "420**";die();
	 			}
			}
 		}//color level wise

		if(str_replace("'","",$sewing_production_variable)==3)//color and size wise
		{

			/* $color_sizeID_arr=sql_select( "select id,size_number_id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name $country_ship_date_cond and status_active=1 and is_deleted=0 order by size_number_id,color_number_id" );
			$colSizeID_arr=array();
			foreach($color_sizeID_arr as $val){
				$index = $val[csf("size_number_id")].$color_arr[$val[csf("color_number_id")]].$val[csf("color_number_id")];
				$colSizeID_arr[$index]=$val[csf("id")];
			} */

			$rowExBundle = array_filter(explode("***",$colorBundleVal));
			foreach($rowExBundle as $rowR=>$valR)
			{
				$colorAndSizeBun_arr = explode("*",$valR);
				/* $sizeID = $colorAndSizeBun_arr[0];
				$colorID = $colorAndSizeBun_arr[1];
				$colorSizeBun = $colorAndSizeBun_arr[2];
				$index = $sizeID.$color_arr[$colorID].$colorID;
				$BunQtyArr[$index]=$colorSizeBun; */
				$color_size_id = $colorAndSizeBun_arr[0];
				$colorSizeBun = $colorAndSizeBun_arr[1]; 
				$BunQtyArr[$color_size_id]=$colorSizeBun;

			}

			//	colorIDvalue concate as sizeID*colorID*value***sizeID*colorID*value	--------------------------//
			$rowExRej = array_filter(explode("***",$colorIDvalueRej));
			foreach($rowExRej as $rowR=>$valR)
			{
				$colorAndSizeRej_arr = explode("*",$valR);
				/* $sizeID = $colorAndSizeRej_arr[0];
				$colorID = $colorAndSizeRej_arr[1];
				$colorSizeRej = $colorAndSizeRej_arr[2];
				$index = $sizeID.$color_arr[$colorID].$colorID;
				$rejQtyArr[$index]=$colorSizeRej; */
				$color_size_id = $colorAndSizeRej_arr[0];
				$colorSizeRej = $colorAndSizeRej_arr[1]; 
				$rejQtyArr[$color_size_id]=$colorSizeRej;
			}

 			$rowEx = array_filter(explode("***",$colorIDvalue));
			//$dtls_id=return_next_id("id", "pro_garments_production_dtls", 1);
			$data_array="";$j=0;
			foreach($rowEx as $rowE=>$valE)
			{
				$colorAndSizeAndValue_arr = explode("*",$valE);
				/* $sizeID = $colorAndSizeAndValue_arr[0];
				$colorID = $colorAndSizeAndValue_arr[1];
				$colorSizeValue = $colorAndSizeAndValue_arr[2];
				$index = $sizeID.$color_arr[$colorID].$colorID; */

				$color_size_id = $colorAndSizeAndValue_arr[0];
				$colorSizeValue = $colorAndSizeAndValue_arr[1];  

				if($is_control==1 && $user_level!=2)// dont hide ISD-23-00517
				{
					if($colorSizeValue>0)
					{
						if(($colorSizeValue*1)>($color_pord_data[$color_size_id]*1))
						{
							echo "35**Production Quantity Not Over Sewing Input Qnty";
							//check_table_status( $_SESSION['menu_id'],0);
							disconnect($con);
							die;
						}
					}
				}

				/* if($colSizeID_arr[$index]!="")
				{ */
				//4 for Sewing Input Entry
					$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",  "pro_garments_production_dtls", $con );
					if($j==0)$data_array = "(".$dtls_id.",".$id.",5,'".$color_size_id."','".$colorSizeValue."','".$rejQtyArr[$color_size_id]."','".$BunQtyArr[$color_size_id]."',".$cbo_color_type.")";
					else $data_array .= ",(".$dtls_id.",".$id.",5,'".$color_size_id."','".$colorSizeValue."','".$rejQtyArr[$color_size_id]."','".$BunQtyArr[$color_size_id]."',".$cbo_color_type.")";
					//$dtls_id=$dtls_id+1;
	 				$j++;
	 			/* }
	 			else
	 			{
	 				echo "420**";die();
	 			} */
			}
		}//color and size wise

		$defectQ=true;
		$data_array_defect="";
		$save_string=explode(",",str_replace("'","",$save_data));

		if(count($save_string)>0 && str_replace("'","",$save_data)!="")
		{
 			//order_wise_pro_details table data insert START-----//
			$field_array_defect="id, mst_id, production_type, po_break_down_id, defect_type_id, defect_point_id, defect_qty, inserted_by, insert_date";
 			$defect_array=array();
			for($i=0;$i<count($save_string);$i++)
			{
				$order_dtls=explode("**",$save_string[$i]);
				$defect_update_id=$order_dtls[0];
				$defect_point_id=$order_dtls[1];
				$defect_qnty=$order_dtls[2];

				if( array_key_exists($defect_point_id,$defect_array) )
				{
					$defect_array[$defect_point_id]=$defect_qnty;
				}
				else
				{
					$defect_array[$defect_point_id]=$defect_qnty;
				}
			}
			$i=0;
			foreach($defect_array as $key=>$val)
			{
				if( $i>0 ) $data_array_defect.=",";
 				if( $dft_id=="" )
				{
					//$dft_id=return_next_id("id", "pro_gmts_prod_dft", 1);
					$dft_id = return_next_id_by_sequence(  "pro_gmts_prod_dft_seq", "pro_gmts_prod_dft", $con );


				}
				else
				{
					$dft_id = return_next_id_by_sequence(  "pro_gmts_prod_dft_seq",  "pro_gmts_prod_dft", $con );

				}

				$defectPointId=$key;
				$defect_qty=$val;
				$data_array_defect.="(".$dft_id.",".$id.",5,".$hidden_po_break_down_id.",".$defect_type_id.",".$defectPointId.",'".$defect_qty."',".$user_id.",'".$pc_date_time."')";
				$i++;
			}
		}
		if($data_array_defect!="")
		{
			//echo "10**INSERT INTO pro_gmts_prod_dft (".$field_array_defect.") VALUES ".$data_array_defect."";// die;
			$defectQ=sql_insert("pro_gmts_prod_dft",$field_array_defect,$data_array_defect,1);
		}
		$defectSpot=true;
		$data_array_defectsp="";
		$save_dataSpot=explode(",",str_replace("'","",$save_dataSpot));
		if(count($save_dataSpot)>0 && str_replace("'","",$save_dataSpot)!="")
		{
 			//order_wise_pro_details table data insert START-----//
			$field_array_defectsp="id, mst_id, production_type, po_break_down_id, defect_type_id, defect_point_id, defect_qty, inserted_by, insert_date";
 			$defectSpt_array=array();
			for($i=0;$i<count($save_dataSpot);$i++)
			{
				$order_dtls=explode("**",$save_dataSpot[$i]);
				$defect_update_id=$order_dtls[0];
				$defectsp_point_id=$order_dtls[1];
				$defect_qnty=$order_dtls[2];

				if( array_key_exists($defectsp_point_id,$defectSpt_array) )
				{
					$defectSpt_array[$defectsp_point_id]=$defect_qnty;
				}
				else
				{
					$defectSpt_array[$defectsp_point_id]=$defect_qnty;
				}
			}
			$i=0;
			foreach($defectSpt_array as $keysp=>$valsp)
			{
				if( $i>0 ) $data_array_defectsp.=",";

				if( $dftSp_id=="" )
				{
					//$dft_id=return_next_id("id", "pro_gmts_prod_dft", 1);
					$dftSp_id = return_next_id_by_sequence(  "pro_gmts_prod_dft_seq",  "pro_gmts_prod_dft", $con );
				}
				else
				{
					$dftSp_id = return_next_id_by_sequence(  "pro_gmts_prod_dft_seq",  "pro_gmts_prod_dft", $con );
				}

				$defectspPointId=$keysp;
				$defectsp_qty=$valsp;
				$data_array_defectsp.="(".$dftSp_id.",".$id.",5,".$hidden_po_break_down_id.",".$defectSpot_type_id.",".$defectspPointId.",'".$defectsp_qty."',".$user_id.",'".$pc_date_time."')";
				$i++;
			}
		}

		if($data_array_defectsp!="")
		{
			//echo "10**INSERT INTO pro_gmts_prod_dft (".$field_array_defectsp.") VALUES ".$data_array_defectsp.""; die;
			$defectSpot=sql_insert("pro_gmts_prod_dft",$field_array_defectsp,$data_array_defectsp,1);
		}
        //Front Part
		$defectFront=true;
		$data_array_defectFr="";
		$save_stringFront=explode(",",str_replace("'","",$save_dataFront));
		//$dft_front_idF=="";

		if(count($save_stringFront)>0 && str_replace("'","",$save_dataFront)!="")
		{
 			//order_wise_pro_details table data insert START-----//
			$dft_id=return_next_id("id", "pro_gmts_prod_dft", 1);
			$field_array_defectFr="id, mst_id, production_type, po_break_down_id, defect_type_id, defect_point_id, defect_qty, inserted_by, insert_date";
 			$defect_arrayFr=array();
			for($i=0;$i<count($save_stringFront);$i++)
			{
				$order_dtls=explode("**",$save_stringFront[$i]);
				$defect_update_id=$order_dtls[0];
				$defect_point_id=$order_dtls[1];
				$defect_qnty=$order_dtls[2];

				if( array_key_exists($defect_point_id,$defect_arrayFr) )
				{
					$defect_arrayFr[$defect_point_id]=$defect_qnty;
				}
				else
				{
					$defect_arrayFr[$defect_point_id]=$defect_qnty;
				}
				//$dd_arr[$i]=$i;
			}
			//echo "10**";
			//print_r($defect_array);die;
			$i=0;
			foreach($defect_arrayFr as $key=>$val)
			{
				if( $i>0 ) $data_array_defectFr.=",";
				if( $dft_front_idF=="" )
				{
					//$dft_id=return_next_id("id", "pro_gmts_prod_dft", 1);
					$dft_front_idF = return_next_id_by_sequence(  "pro_gmts_prod_dft_seq",  "pro_gmts_prod_dft", $con );
				}
				else
				{
					$dft_front_idF = return_next_id_by_sequence(  "pro_gmts_prod_dft_seq",  "pro_gmts_prod_dft", $con );
				}
			//	print_r($defect_array);die;
				$defectPointId=$key;
				$defect_qty=$val;
				//$dft_id_arr[$dft_id]=$dft_id;
				$data_array_defectFr.="(".$dft_front_idF.",".$id.",5,".$hidden_po_break_down_id.",".$defectFront_type_id.",".$defectPointId.",'".$defect_qty."',".$user_id.",'".$pc_date_time."')";				//$dft_id = $dft_id + 1;
				$i++;
			}
		}
		//echo "10**DDD";
		//print_r($defect_array);die;
		if($data_array_defectFr!="")
		{
			//echo "10**INSERT INTO pro_gmts_prod_dft (".$field_array_defectFr.") VALUES ".$data_array_defectFr.""; die;
			$defectFront=sql_insert("pro_gmts_prod_dft",$field_array_defectFr,$data_array_defectFr,1);
		}
		//echo "10**X=INSERT INTO pro_gmts_prod_dft (".$field_array_defectFr.") VALUES ".$data_array_defectFr.""; die;
		//Front part End
		// Inside Part Start
		$defectInside=true;
		$data_array_defectIn="";
		$save_stringInside=explode(",",str_replace("'","",$save_dataInside));
		//$dft_front_idF=="";

		if(count($save_stringInside)>0 && str_replace("'","",$save_dataInside)!="")
		{
 			//order_wise_pro_details table data insert START-----//
			$dft_id=return_next_id("id", "pro_gmts_prod_dft", 1);
			$field_array_defectIn="id, mst_id, production_type, po_break_down_id, defect_type_id, defect_point_id, defect_qty, inserted_by, insert_date";
 			$defect_arrayIn=array();
			for($i=0;$i<count($save_stringInside);$i++)
			{
				$order_dtls=explode("**",$save_stringInside[$i]);
				$defect_update_id=$order_dtls[0];
				$defect_point_id=$order_dtls[1];
				$defect_qnty=$order_dtls[2];

				if( array_key_exists($defect_point_id,$defect_arrayIn) )
				{
					$defect_arrayIn[$defect_point_id]=$defect_qnty;
				}
				else
				{
					$defect_arrayIn[$defect_point_id]=$defect_qnty;
				}
				//$dd_arr[$i]=$i;
			}
			//echo "10**";
			//print_r($defect_array);die;
			$i=0;
			foreach($defect_arrayIn as $key=>$val)
			{
				if( $i>0 ) $data_array_defectIn.=",";
				if( $dft_front_idF=="" )
				{
					//$dft_id=return_next_id("id", "pro_gmts_prod_dft", 1);
					$dft_front_idF = return_next_id_by_sequence(  "pro_gmts_prod_dft_seq",  "pro_gmts_prod_dft", $con );
				}
				else
				{
					$dft_front_idF = return_next_id_by_sequence(  "pro_gmts_prod_dft_seq",  "pro_gmts_prod_dft", $con );
				}
			//	print_r($defect_array);die;
				$defectPointId=$key;
				$defect_qty=$val;
				//$dft_id_arr[$dft_id]=$dft_id;
				$data_array_defectIn.="(".$dft_front_idF.",".$id.",5,".$hidden_po_break_down_id.",".$defectInside_type_id.",".$defectPointId.",'".$defect_qty."',".$user_id.",'".$pc_date_time."')";				//$dft_id = $dft_id + 1;
				$i++;
			}
		}
		//echo "10**DDD";
		//print_r($defect_array);die;
		if($data_array_defectIn!="")
		{
			//echo "10**INSERT INTO pro_gmts_prod_dft (".$field_array_defectFr.") VALUES ".$data_array_defectFr.""; die;
			$defectInside=sql_insert("pro_gmts_prod_dft",$field_array_defectIn,$data_array_defectIn,1);
		}
		// Inside Part End
		// TopSide Part Start
		$defectTopside=true;
		$data_array_defectTo="";
		$save_stringTopside=explode(",",str_replace("'","",$save_dataTopside));
		//$dft_front_idF=="";

		if(count($save_stringTopside)>0 && str_replace("'","",$save_dataTopside)!="")
		{
 			//order_wise_pro_details table data insert START-----//
			$dft_id=return_next_id("id", "pro_gmts_prod_dft", 1);
			$field_array_defectTo="id, mst_id, production_type, po_break_down_id, defect_type_id, defect_point_id, defect_qty, inserted_by, insert_date";
 			$defect_arrayTo=array();
			for($i=0;$i<count($save_stringTopside);$i++)
			{
				$order_dtls=explode("**",$save_stringTopside[$i]);
				$defect_update_id=$order_dtls[0];
				$defect_point_id=$order_dtls[1];
				$defect_qnty=$order_dtls[2];

				if( array_key_exists($defect_point_id,$defect_arrayTo) )
				{
					$defect_arrayTo[$defect_point_id]=$defect_qnty;
				}
				else
				{
					$defect_arrayTo[$defect_point_id]=$defect_qnty;
				}
				//$dd_arr[$i]=$i;
			}
			//echo "10**";
			//print_r($defect_array);die;
			$i=0;
			foreach($defect_arrayTo as $key=>$val)
			{
				if( $i>0 ) $data_array_defectTo.=",";
				if( $dft_front_idF=="" )
				{
					//$dft_id=return_next_id("id", "pro_gmts_prod_dft", 1);
					$dft_front_idF = return_next_id_by_sequence(  "pro_gmts_prod_dft_seq",  "pro_gmts_prod_dft", $con );
				}
				else
				{
					$dft_front_idF = return_next_id_by_sequence(  "pro_gmts_prod_dft_seq",  "pro_gmts_prod_dft", $con );
				}
			//	print_r($defect_array);die;
				$defectPointId=$key;
				$defect_qty=$val;
				//$dft_id_arr[$dft_id]=$dft_id;
				$data_array_defectTo.="(".$dft_front_idF.",".$id.",5,".$hidden_po_break_down_id.",".$defectTopside_type_id.",".$defectPointId.",'".$defect_qty."',".$user_id.",'".$pc_date_time."')";				//$dft_id = $dft_id + 1;
				$i++;
			}
		}
		//echo "10**DDD";
		//print_r($defect_array);die;
		if($data_array_defectTo!="")
		{
			//echo "10**INSERT INTO pro_gmts_prod_dft (".$field_array_defectFr.") VALUES ".$data_array_defectFr.""; die;
			$defectTopside=sql_insert("pro_gmts_prod_dft",$field_array_defectTo,$data_array_defectTo,1);
		}
		//echo "10**X=INSERT INTO pro_gmts_prod_dft (".$field_array_defectFr.") VALUES ".$data_array_defectFr.""; die;
		//Topside part End
		// Collar Part Start
		$defectCollar=true;
		$data_array_defectCo="";
		$save_stringCollar=explode(",",str_replace("'","",$save_dataCollar));
		//$dft_front_idF=="";

		if(count($save_stringCollar)>0 && str_replace("'","",$save_dataCollar)!="")
		{
 			//order_wise_pro_details table data insert START-----//
			$dft_id=return_next_id("id", "pro_gmts_prod_dft", 1);
			$field_array_defectCo="id, mst_id, production_type, po_break_down_id, defect_type_id, defect_point_id, defect_qty, inserted_by, insert_date";
 			$defect_arrayCo=array();
			for($i=0;$i<count($save_stringCollar);$i++)
			{
				$order_dtls=explode("**",$save_stringCollar[$i]);
				$defect_update_id=$order_dtls[0];
				$defect_point_id=$order_dtls[1];
				$defect_qnty=$order_dtls[2];

				if( array_key_exists($defect_point_id,$defect_arrayCo) )
				{
					$defect_arrayCo[$defect_point_id]=$defect_qnty;
				}
				else
				{
					$defect_arrayCo[$defect_point_id]=$defect_qnty;
				}
				//$dd_arr[$i]=$i;
			}
			//echo "10**";
			//print_r($defect_array);die;
			$i=0;
			foreach($defect_arrayCo as $key=>$val)
			{
				if( $i>0 ) $data_array_defectCo.=",";
				if( $dft_front_idF=="" )
				{
					//$dft_id=return_next_id("id", "pro_gmts_prod_dft", 1);
					$dft_front_idF = return_next_id_by_sequence(  "pro_gmts_prod_dft_seq",  "pro_gmts_prod_dft", $con );
				}
				else
				{
					$dft_front_idF = return_next_id_by_sequence(  "pro_gmts_prod_dft_seq",  "pro_gmts_prod_dft", $con );
				}
			//	print_r($defect_array);die;
				$defectPointId=$key;
				$defect_qty=$val;
				//$dft_id_arr[$dft_id]=$dft_id;
				$data_array_defectCo.="(".$dft_front_idF.",".$id.",5,".$hidden_po_break_down_id.",".$defectCollar_type_id.",".$defectPointId.",'".$defect_qty."',".$user_id.",'".$pc_date_time."')";				//$dft_id = $dft_id + 1;
				$i++;
			}
		}
		//echo "10**DDD";
		//print_r($defect_array);die;
		if($data_array_defectCo!="")
		{
			//echo "10**INSERT INTO pro_gmts_prod_dft (".$field_array_defectFr.") VALUES ".$data_array_defectFr.""; die;
			$defectCollar=sql_insert("pro_gmts_prod_dft",$field_array_defectCo,$data_array_defectCo,1);
		}
		// Collar Part End
		// Armhole Part Start
		$defectArmhole=true;
		$data_array_defectAr="";
		$save_stringArmhole=explode(",",str_replace("'","",$save_dataArmhole));
		//$dft_front_idF=="";

		if(count($save_stringArmhole)>0 && str_replace("'","",$save_dataArmhole)!="")
		{
 			//order_wise_pro_details table data insert START-----//
			$dft_id=return_next_id("id", "pro_gmts_prod_dft", 1);
			$field_array_defectAr="id, mst_id, production_type, po_break_down_id, defect_type_id, defect_point_id, defect_qty, inserted_by, insert_date";
 			$defect_arrayAr=array();
			for($i=0;$i<count($save_stringArmhole);$i++)
			{
				$order_dtls=explode("**",$save_stringArmhole[$i]);
				$defect_update_id=$order_dtls[0];
				$defect_point_id=$order_dtls[1];
				$defect_qnty=$order_dtls[2];

				if( array_key_exists($defect_point_id,$defect_arrayAr) )
				{
					$defect_arrayAr[$defect_point_id]=$defect_qnty;
				}
				else
				{
					$defect_arrayAr[$defect_point_id]=$defect_qnty;
				}
				//$dd_arr[$i]=$i;
			}
			//echo "10**";
			//print_r($defect_array);die;
			$i=0;
			foreach($defect_arrayAr as $key=>$val)
			{
				if( $i>0 ) $data_array_defectAr.=",";
				if( $dft_front_idF=="" )
				{
					//$dft_id=return_next_id("id", "pro_gmts_prod_dft", 1);
					$dft_front_idF = return_next_id_by_sequence(  "pro_gmts_prod_dft_seq",  "pro_gmts_prod_dft", $con );
				}
				else
				{
					$dft_front_idF = return_next_id_by_sequence(  "pro_gmts_prod_dft_seq",  "pro_gmts_prod_dft", $con );
				}
			//	print_r($defect_array);die;
				$defectPointId=$key;
				$defect_qty=$val;
				//$dft_id_arr[$dft_id]=$dft_id;
				$data_array_defectAr.="(".$dft_front_idF.",".$id.",5,".$hidden_po_break_down_id.",".$defectArmhole_type_id.",".$defectPointId.",'".$defect_qty."',".$user_id.",'".$pc_date_time."')";				//$dft_id = $dft_id + 1;
				$i++;
			}
		}
		//echo "10**DDD";
		//print_r($defect_array);die;
		if($data_array_defectAr!="")
		{
			//echo "10**INSERT INTO pro_gmts_prod_dft (".$field_array_defectFr.") VALUES ".$data_array_defectFr.""; die;
			$defectArmhole=sql_insert("pro_gmts_prod_dft",$field_array_defectAr,$data_array_defectAr,1);
		}
		// Armhole Part End
		// MakeCheck Part Start
        $defectMake=true;
		$data_array_defectMak="";
		$save_stringMake=explode(",",str_replace("'","",$save_dataMake));
		//$dft_front_idF=="";

		if(count($save_stringMake)>0 && str_replace("'","",$save_dataMake)!="")
		{
 			//order_wise_pro_details table data insert START-----//
			$dft_id=return_next_id("id", "pro_gmts_prod_dft", 1);
			$field_array_defectMak="id, mst_id, production_type, po_break_down_id, defect_type_id, defect_point_id, defect_qty, inserted_by, insert_date";
 			$defect_arrayMak=array();
			for($i=0;$i<count($save_stringMake);$i++)
			{
				$order_dtls=explode("**",$save_stringMake[$i]);
				$defect_update_id=$order_dtls[0];
				$defect_point_id=$order_dtls[1];
				$defect_qnty=$order_dtls[2];

				if( array_key_exists($defect_point_id,$defect_arrayMak) )
				{
					$defect_arrayMak[$defect_point_id]=$defect_qnty;
				}
				else
				{
					$defect_arrayMak[$defect_point_id]=$defect_qnty;
				}
				//$dd_arr[$i]=$i;
			}
			//echo "10**";
			//print_r($defect_array);die;
			$i=0;
			foreach($defect_arrayMak as $key=>$val)
			{
				if( $i>0 ) $data_array_defectMak.=",";
				if( $dft_front_idF=="" )
				{
					//$dft_id=return_next_id("id", "pro_gmts_prod_dft", 1);
					$dft_front_idF = return_next_id_by_sequence(  "pro_gmts_prod_dft_seq",  "pro_gmts_prod_dft", $con );
				}
				else
				{
					$dft_front_idF = return_next_id_by_sequence(  "pro_gmts_prod_dft_seq",  "pro_gmts_prod_dft", $con );
				}
			//	print_r($defect_array);die;
				$defectPointId=$key;
				$defect_qty=$val;
				//$dft_id_arr[$dft_id]=$dft_id;
				$data_array_defectMak.="(".$dft_front_idF.",".$id.",5,".$hidden_po_break_down_id.",".$defectMake_type_id.",".$defectPointId.",'".$defect_qty."',".$user_id.",'".$pc_date_time."')";				//$dft_id = $dft_id + 1;
				$i++;
			}
		}
			//echo "10**DDD";
			//print_r($defect_array);die;
			if($data_array_defectMak!="")
			{
				//echo "10**INSERT INTO pro_gmts_prod_dft (".$field_array_defectFr.") VALUES ".$data_array_defectFr.""; die;
				$defectMake=sql_insert("pro_gmts_prod_dft",$field_array_defectMak,$data_array_defectMak,1);
			}
		// MakeCheck Part End
		// Back Part Start
		$defectBack=true;
		$data_array_defectbk="";
		$save_dataStringBack=explode(",",str_replace("'","",$save_dataBack));
		$dftbk_id=="";

		if(count($save_dataStringBack)>0 && str_replace("'","",$save_dataBack)!="")
		{
 			//order_wise_pro_details table data insert START-----//
			//$dft_bk_id=return_next_id("id", "pro_gmts_prod_dft", 1);
			$field_array_defectbk="id, mst_id, production_type, po_break_down_id, defect_type_id, defect_point_id, defect_qty, inserted_by, insert_date";
 			$defectBack_array=array();
			for($i=0;$i<count($save_dataStringBack);$i++)
			{
				$order_dtls=explode("**",$save_dataStringBack[$i]);
				$defect_update_id=$order_dtls[0];
				$defectbk_point_id=$order_dtls[1];
				$defect_qnty=$order_dtls[2];

				if( array_key_exists($defectbk_point_id,$defectBack_array) )
				{
					$defectBack_array[$defectbk_point_id]=$defect_qnty;
				}
				else
				{
					$defectBack_array[$defectbk_point_id]=$defect_qnty;
				}
			}
			$i=0;
			foreach($defectBack_array as $keysp=>$valsp)
			{
				if( $i>0 ) $data_array_defectbk.=",";
				if( $dft_bk_id=="" )
				{
					//$dft_id=return_next_id("id", "pro_gmts_prod_dft", 1);
					$dft_bk_id = return_next_id_by_sequence(  "pro_gmts_prod_dft_seq",  "pro_gmts_prod_dft", $con );
				}
				else
				{
					$dft_bk_id = return_next_id_by_sequence(  "pro_gmts_prod_dft_seq",  "pro_gmts_prod_dft", $con );
				}

				$defectbkPointId=$keysp;
				$defectbk_qty=$valsp;
				$data_array_defectbk.="(".$dft_bk_id.",".$id.",5,".$hidden_po_break_down_id.",".$defectBack_type_id.",".$defectbkPointId.",'".$defectbk_qty."',".$user_id.",'".$pc_date_time."')";
				//$dft_bk_id = $dft_bk_id + 1;
				$i++;
			}
		}

		if($data_array_defectbk!="")
		{
			//echo "10**INSERT INTO pro_gmts_prod_dft_gross (".$field_array_defectbk.") VALUES ".$data_array_defectbk.""; die;
			$defectBk=sql_insert("pro_gmts_prod_dft",$field_array_defectbk,$data_array_defectbk,1);
			//echo "10**=".$defectBk.'ASA';die;
		}
		//Back part End
		// Reject Part
		$defectReject=true;
		$data_array_defect_reject="";
		$save_dataReject=explode(",",str_replace("'","",$save_dataReject));
		if(count($save_dataReject)>0 && str_replace("'","",$save_dataReject)!="")
		{
 			//order_wise_pro_details table data insert START-----//
			$field_array_defectsp="id, mst_id, production_type, po_break_down_id, defect_type_id, defect_point_id, defect_qty, inserted_by, insert_date";
 			$defectReject_array=array();
			for($i=0;$i<count($save_dataReject);$i++)
			{
				$order_dtls=explode("**",$save_dataReject[$i]);
				$defect_update_id=$order_dtls[0];
				$defectsp_point_id=$order_dtls[1];
				$defect_qnty=$order_dtls[2];

				if( array_key_exists($defectsp_point_id,$defectReject_array) )
				{
					$defectReject_array[$defectsp_point_id]=$defect_qnty;
				}
				else
				{
					$defectReject_array[$defectsp_point_id]=$defect_qnty;
				}
			}
			$i=0;
			foreach($defectReject_array as $keysp=>$valsp)
			{
				if( $i>0 ) $data_array_defect_reject.=",";
				//if( $dftSp_id=="" ) $dftSp_id=return_next_id("id", "pro_gmts_prod_dft", 1); else $dftSp_id = $dftSp_id+1;
				$dftSp_id=return_next_id_by_sequence("PRO_GMTS_PROD_DFT_SEQ", "pro_gmts_prod_dft", $con);
				$defectspPointId=$keysp;
				$defectsp_qty=$valsp;
				$data_array_defect_reject.="(".$dftSp_id.",".$id.",5,".$hidden_po_break_down_id.",".$defectReject_type_id.",".$defectspPointId.",'".$defectsp_qty."',".$user_id.",'".$pc_date_time."')";
				$i++;
			}

			if($data_array_defect_reject!="")
			{
				// echo "10**INSERT INTO pro_gmts_prod_dft (".$field_array_defectsp.") VALUES ".$data_array_defect_reject.""; die;
				//echo "DELETE FROM pro_gmts_prod_dft WHERE mst_id='$txt_mst_id' and defect_type_id=2";die;
				$query4=execute_query("DELETE FROM pro_gmts_prod_dft WHERE mst_id=$id and defect_type_id=3 and production_type=5",1);
				$defectReject=sql_insert("pro_gmts_prod_dft",$field_array_defectsp,$data_array_defect_reject,1);
			}
		}

		// ---West Part
		$defectWest=true;
		$data_array_defectWt="";
		$save_dataStringWest=explode(",",str_replace("'","",$save_dataWest));
		$dftwt_id=="";

		if(count($save_dataStringWest)>0 && str_replace("'","",$save_dataWest)!="")
		{
 			//order_wise_pro_details table data insert START-----//
			//$dft_wt_id=return_next_id("id", "pro_gmts_prod_dft_gross", 1);
			$field_array_defectwt="id, mst_id, production_type, po_break_down_id, defect_type_id, defect_point_id, defect_qty, inserted_by, insert_date";
 			$defectWest_array=array();
			for($i=0;$i<count($save_dataStringWest);$i++)
			{
				$order_dtls=explode("**",$save_dataStringWest[$i]);
				$defect_update_id=$order_dtls[0];
				$defectbk_point_id=$order_dtls[1];
				$defect_qnty=$order_dtls[2];

				if( array_key_exists($defectbk_point_id,$defectWest_array) )
				{
					$defectWest_array[$defectbk_point_id]=$defect_qnty;
				}
				else
				{
					$defectWest_array[$defectbk_point_id]=$defect_qnty;
				}
			}
			$i=0;
			foreach($defectWest_array as $keysp=>$valsp)
			{
				if( $i>0 ) $data_array_defectwt.=",";
				if( $dft_wt_id=="" )
				{
					//$dft_id=return_next_id("id", "pro_gmts_prod_dft", 1);
					$dft_wt_id = return_next_id_by_sequence(  "pro_gmts_prod_dft_seq",  "pro_gmts_prod_dft", $con );
				}
				else
				{
					$dft_wt_id = return_next_id_by_sequence(  "pro_gmts_prod_dft_seq",  "pro_gmts_prod_dft", $con );
				}

				$defectbkPointId=$keysp;
				$defectbk_qty=$valsp;
				$data_array_defectwt.="(".$dft_wt_id.",".$id.",5,".$hidden_po_break_down_id.",".$defectWest_type_id.",".$defectbkPointId.",'".$defectbk_qty."',".$user_id.",'".$pc_date_time."')";
				//$dft_wt_id = $dft_wt_id + 1;
				$i++;
			}
		}

		if($data_array_defectwt!="")
		{
			//echo "10**INSERT INTO pro_gmts_prod_dft (".$field_array_defectwt.") VALUES ".$data_array_defectwt.""; die;
			$defectWt=sql_insert("pro_gmts_prod_dft",$field_array_defectwt,$data_array_defectwt,1);
		}
		//West Band End

		$defectMeasure=true;
		$data_array_defectme="";
		$save_dataStringMeasure=explode(",",str_replace("'","",$save_dataMeasure));
		if(count($save_dataStringMeasure)>0 && str_replace("'","",$save_dataMeasure)!="")
		{
 			//order_wise_pro_details table data insert START-----//
			$field_array_defectMe="id, mst_id, production_type, po_break_down_id, defect_type_id, defect_point_id, defect_qty, inserted_by, insert_date";
 			$defectMeasure_array=array();
			for($i=0;$i<count($save_dataStringMeasure);$i++)
			{
				$order_dtls=explode("**",$save_dataStringMeasure[$i]);
				$defect_update_id=$order_dtls[0];
				$defectbk_point_id=$order_dtls[1];
				$defect_qnty=$order_dtls[2];

				if( array_key_exists($defectbk_point_id,$defectMeasure_array) )
				{
					$defectMeasure_array[$defectbk_point_id]=$defect_qnty;
				}
				else
				{
					$defectMeasure_array[$defectbk_point_id]=$defect_qnty;
				}
			}
			$i=0;
			foreach($defectMeasure_array as $keysp=>$valsp)
			{
				if( $i>0 ) $data_array_defectme.=",";
				if( $dft_wt_id=="" )
				{
					//$dft_id=return_next_id("id", "pro_gmts_prod_dft", 1);
					$dft_me_id = return_next_id_by_sequence(  "pro_gmts_prod_dft_seq",  "pro_gmts_prod_dft", $con );
				}
				else
				{
					$dft_me_id = return_next_id_by_sequence(  "pro_gmts_prod_dft_seq",  "pro_gmts_prod_dft", $con );
				}

				$defectbkPointId=$keysp;
				$defectbk_qty=$valsp;
				$data_array_defectme.="(".$dft_me_id.",".$id.",5,".$hidden_po_break_down_id.",".$defectMeasure_type_id.",".$defectbkPointId.",'".$defectbk_qty."',".$user_id.",'".$pc_date_time."')";
				//$dft_me_id = $dft_me_id + 1;
				$i++;
			}
		}

		if($data_array_defectme!="")
		{
			//echo "10**=INSERT INTO pro_gmts_prod_dft (".$field_array_defectMe.") VALUES ".$data_array_defectme.""; die;
			$defectme=sql_insert("pro_gmts_prod_dft",$field_array_defectMe,$data_array_defectme,0);
		}


		if($db_type==2)
		{
			$rID=execute_query($data_array1);
		}
		else
		{
			$rID=sql_insert("pro_garments_production_mst",$field_array1,$data_array1,1);
		}

		if(str_replace("'","",$sewing_production_variable)==2 || str_replace("'","",$sewing_production_variable)==3)
		{
 			$dtlsrID=sql_insert("pro_garments_production_dtls",$field_array,$data_array,1);
		}
		// echo "10**".$rID.'='.$dtlsrID.'='.$defectme.'='.$defectWt.'='.$defectBk.'='.$defectFront.'='.$defectReject;die;
		//release lock table
		//check_table_status( $_SESSION['menu_id'],0);

		if($db_type==0)
		{
			if(str_replace("'","",$sewing_production_variable)!=1)
			{
				if($rID && $dtlsrID)
				{
					mysql_query("COMMIT");
					echo "0**".str_replace("'","",$hidden_po_break_down_id);
				}
				else
				{
					mysql_query("ROLLBACK");
					echo "10**".str_replace("'","",$hidden_po_break_down_id);
				}
			}
			else
			{
				if($rID)
				{
					mysql_query("COMMIT");
					echo "0**".str_replace("'","",$hidden_po_break_down_id);
				}
				else
				{
					mysql_query("ROLLBACK");
					echo "10**".str_replace("'","",$hidden_po_break_down_id);
				}
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if(str_replace("'","",$sewing_production_variable)!=1)
			{
				if($rID && $dtlsrID )
				{
					oci_commit($con);
					echo "0**".str_replace("'","",$hidden_po_break_down_id);
				}
				else
				{
					oci_rollback($con);
					echo "10**".str_replace("'","",$hidden_po_break_down_id);
				}
			}
			else
			{
				if($rID)
				{
					oci_commit($con);
					echo "0**".str_replace("'","",$hidden_po_break_down_id);
				}
				else
				{
					oci_rollback($con);
					echo "10**".str_replace("'","",$hidden_po_break_down_id);
				}
			}
		}
		unset($_POST);
		disconnect($con);
		die;
	}
  	else if ($operation==1) // Update Here End------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		$ok=true;
		$date_cond=($db_type==2)? " TO_CHAR(a.production_hour,'HH24:MI') as production_hour " : " TIME_FORMAT( production_hour, '%H:%i' ) as production_hour ";
		$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
		$colorIdArrays=explode("***", $colorIDvalue);
		$fin_sql="SELECT po_break_down_id,color_id,size_id,SUM(fin_receive_qnty) AS qnty FROM gmt_finishing_receive_dtls WHERE status_active=1 and is_deleted=0 and po_break_down_id=$hidden_po_break_down_id and item_id=$cbo_item_name and country_id=$cbo_country_name Group by po_break_down_id,color_id,size_id,country_id";
		$fin_res=sql_select($fin_sql);
		$fin_data=array();
		foreach ($fin_res as $row)
		{
			$fin_data[$row[csf('po_break_down_id')]][$row[csf('color_id')]][$row[csf('size_id')]]+=$row[csf('qnty')];
		}
		$country_ship_date_cond3 = str_replace("country_ship_date","a.country_ship_date",$country_ship_date_cond);
		if($db_type == 0)
		{
			$sql_swo = "SELECT a.po_break_down_id,c.color_number_id,c.size_number_id,sum(b.production_qnty) as production_qnty
			from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c, wo_po_break_down d,wo_po_details_master f
			where a.id=b.mst_id and b.color_size_break_down_id=c.id and c.po_break_down_id=d.id and d.id=a.po_break_down_id and d.job_no_mst=f.job_no and a.production_type=5 and a.entry_break_down_type=3 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and d.id=$hidden_po_break_down_id and a.item_number_id=$cbo_item_name and a.country_id=$cbo_country_name and a.id!=$txt_mst_id
			group by a.po_break_down_id,c.color_number_id,c.size_number_id";
		}
		else if($db_type == 2)
		{
			$sql_swo = "SELECT a.po_break_down_id,c.color_number_id,c.size_number_id,sum(b.production_qnty) as production_qnty
			from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c, wo_po_break_down d,wo_po_details_master f
			where a.id=b.mst_id and b.color_size_break_down_id=c.id and c.po_break_down_id=d.id and d.id=a.po_break_down_id and d.job_no_mst=f.job_no and a.production_type=5 and a.entry_break_down_type=3 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and d.id=$hidden_po_break_down_id and a.item_number_id=$cbo_item_name and a.country_id=$cbo_country_name $country_ship_date_cond2 and a.id!=$txt_mst_id
			group by a.po_break_down_id,c.color_number_id,c.size_number_id";
		}
		$result_swo=sql_select($sql_swo);
		$swing_output_data=array();

		foreach ($result_swo as $row)
		{
			$swing_output_data[$row[csf('po_break_down_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]+=$row[csf('production_qnty')];
		}

		for($i=0;$i<count($colorIdArrays);$i++)
		{
			$item_con='';
			$colorIdArray=explode('*',$colorIdArrays[$i]);
			if($cbo_item_name!=''){
				$item_con=" and a.item_number_id=$cbo_item_name ";
			}
			$line_con='';
			if($cbo_sewing_line!=''){
				$line_con=" and a.sewing_line=$cbo_sewing_line ";
			}
			$color_num_con='';
			if($colorIdArray[1]!=''){
				$color_num_con=" and c.color_number_id=$colorIdArray[1] ";
			}
			$size_number_con='';
			if($colorIdArray[0]!=''){
				$size_number_con=" and c.size_number_id=$colorIdArray[0] ";
			}
			$fin_color_id=str_replace("'", "", $colorIdArray[1]);
			$fin_size_id=str_replace("'", "", $colorIdArray[0]);
			$fin_po_id=str_replace("'","",$hidden_po_break_down_id);
			$fin_qnty=str_replace("'","",$colorIdArray[2])*1;

			if($fin_data[$fin_po_id][$fin_color_id][$fin_size_id]>($fin_qnty+$swing_output_data[$fin_po_id][$fin_color_id][$fin_size_id]))
			{
				if($db_type==0)
				{
					mysql_query("ROLLBACK");
					echo "gmt_finishing_receive**".$fin_data[$fin_po_id][$fin_color_id][$fin_size_id]."**".($fin_qnty+$swing_output_data[$fin_po_id][$fin_color_id][$fin_size_id]);
				}
				if($db_type==2 || $db_type==1 )
				{
					oci_rollback($con);
					echo "gmt_finishing_receive**".$fin_data[$fin_po_id][$fin_color_id][$fin_size_id]."**".($fin_qnty+$swing_output_data[$fin_po_id][$fin_color_id][$fin_size_id]);
				}
				disconnect($con);
				die;
			}

			if($db_type == 0)
			{

				$sql = "SELECT a.production_date,a.prod_reso_allo,a.po_break_down_id,a.challan_no, a.serving_company,a.location,d.grouping ,f.style_ref_no,d.po_number,a.item_number_id,a.floor_id,b.color_type_id ,c.color_number_id,a.sewing_line,a.country_id,$date_cond,sum(b.production_qnty) as production_qnty,c.size_number_id
				from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c, wo_po_break_down d,wo_po_details_master f
				where a.id=b.mst_id and b.color_size_break_down_id=c.id and c.po_break_down_id=d.id and d.id=a.po_break_down_id and d.job_no_mst=f.job_no and a.production_type=5 and a.entry_break_down_type=3 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1   and a.id=$txt_mst_id  $size_number_con $color_num_con $item_con $line_con
				group by a.production_date,a.prod_reso_allo,a.po_break_down_id,a.challan_no,a.serving_company,a.location,f.style_ref_no,d.po_number,a.item_number_id,a.floor_id,b.color_type_id ,c.color_number_id,a.sewing_line,a.country_id,production_hour,c.size_number_id,d.grouping";
			}
			else if($db_type == 2)
			{
				$sql = "SELECT a.production_date,a.prod_reso_allo,a.po_break_down_id,a.challan_no, a.serving_company,a.location,d.grouping ,f.style_ref_no,d.po_number,a.item_number_id,a.floor_id,b.color_type_id ,c.color_number_id,a.sewing_line,a.country_id,$date_cond,sum(b.production_qnty) as production_qnty,c.size_number_id
				from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c, wo_po_break_down d,wo_po_details_master f
				where a.id=b.mst_id and b.color_size_break_down_id=c.id and c.po_break_down_id=d.id and d.id=a.po_break_down_id and d.job_no_mst=f.job_no and a.production_type=5 and a.entry_break_down_type=3 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.id=$txt_mst_id   $size_number_con $color_num_con $item_con $line_con
				group by a.production_date,a.prod_reso_allo,a.po_break_down_id,a.challan_no, a.serving_company,a.location,f.style_ref_no,d.po_number,a.item_number_id,a.floor_id,b.color_type_id ,c.color_number_id,a.sewing_line,a.country_id,production_hour,c.size_number_id,d.grouping";
			}
			$result=sql_select($sql);
			//echo $sql;die;
			unset($sql);
			foreach ($result as $row) {
				//echo $row[csf('production_qnty')]."**".$colorIdArray[2]."**";
				if($row[csf('production_qnty')]!=$colorIdArray[2])
				{
					$po_break_down_id=$row[csf('po_break_down_id')];
					$challan_no=$row[csf('challan_no')];
					$country_id=$row[csf('country_id')];
					$company_id=$row[csf('serving_company')];
					$location_id=$row[csf('location')];
					$floor_id=$row[csf('floor_id')];
					$size_id=$row[csf('size_number_id')];
					$color_type_id=$row[csf('color_type_id')];
					$color_id= $row[csf('color_number_id')];
					$item_id=$row[csf('item_number_id')];
					$sewing_line='';
					$line_id='';
					if($row[csf('prod_reso_allo')]==1)
					{
						$line_number=explode(",",$prod_reso_arr[$row[csf('sewing_line')]]);
						foreach($line_number as $val)
						{
							if($line_id==''){
								$line_id=$val;
							}
							else {
								$line_id.=",".$val;
							}
						}
					}
					else{
						$line_id=$row[csf('sewing_line')];
					}

					$production_date=$row[csf('production_date')];
					$production_hour=$row[csf('production_hour')];
					$s="select * from finish_barcode  where po_break_down_id=$po_break_down_id and color_id=$color_id and country_id=$country_id and size_id=$size_id and challan_no=$challan_no and company_id=$company_id and color_type_id=$color_type_id and floor_id=$floor_id and item_id=$item_id and line_id=$line_id and production_hour='$production_hour' and production_date='$production_date' and status_active=1 and is_deleted=0";
			      //  echo "<pre>".$s."</pre>";
			        $res=sql_select($s);
			        unset($s);
				    $count=count($res);
				    if(count($res)){
				    	$ok=false;
						if($db_type==0)
						{
							//mysql_query("ROLLBACK");
							echo "111**".str_replace("'","",$hidden_po_break_down_id);
						}
						if($db_type==2 || $db_type==1 )
						{
							//oci_rollback($con);
							echo "111**".str_replace("'","",$hidden_po_break_down_id);
						}
						disconnect($con);
						die;
					}
					unset($res);
				}

			}
			unset($colorIdArray);
			unset($result);

		}
		//echo "Test";die;

		if($db_type==2)
		{
			$txt_reporting_hour=str_replace("'","",$txt_sewing_date)." ".str_replace("'","",$txt_reporting_hour);
			$txt_reporting_hour="to_date('".$txt_reporting_hour."','DD MONTH YYYY HH24:MI:SS')";
		}

 		$field_array1="production_source*serving_company*location*produced_by*shift_name*production_date*production_quantity*production_type*entry_break_down_type* break_down_type_rej*sewing_line*supervisor*production_hour*challan_no*remarks*floor_id*reject_qnty*alter_qnty*total_produced*yet_to_produced*prod_reso_allo*spot_qnty*wo_order_id*currency_id *exchange_rate *rate*amount*is_days_count*updated_by*update_date";
		$rate=str_replace("'","",$hidden_piece_rate);
		if($rate!="") {  $amount=$rate*str_replace("'","",$txt_sewing_qty);}
		else {$amount="";}
		
		if($is_size_set=='true')
		{
			$is_days_count = 1;
			
		}
		// echo "10**".$is_size_set."=".$is_days_count;die;

		$data_array1="".$cbo_source."*".$cbo_sewing_company."*".$cbo_location."*".$cbo_produced_by."*".$cbo_shift_name."*".$txt_sewing_date."*".$txt_sewing_qty."*5*".$sewing_production_variable."*".$sewing_production_variable_rej."*".$cbo_sewing_line."*".$txt_super_visor."*".$txt_reporting_hour."*".$txt_challan."*".$txt_remark."*".$cbo_floor."*".$txt_reject_qnty."*".$txt_alter_qnty."*".$txt_cumul_sewing_qty."*".$txt_yet_to_sewing."*".$prod_reso_allo."*".$txt_spot_qnty."*".$cbo_work_order."*".$hidden_currency_id."*".$hidden_exchange_rate."*'".$rate."'*'".$amount."'*'".$is_days_count."'*".$user_id."*'".$pc_date_time."'";

		// pro_garments_production_dtls table data entry here
		if(str_replace("'","",$sewing_production_variable)!=1 && str_replace("'","",$txt_mst_id)!='' ) // check is not gross level
		{
			$dtlsData = sql_select("SELECT a.color_size_break_down_id,
			sum(CASE WHEN a.production_type=4 and b.sewing_line=$cbo_sewing_line then a.production_qnty ELSE 0 END) as production_qnty,
			sum(CASE WHEN a.production_type=5 and b.sewing_line=$cbo_sewing_line then a.production_qnty ELSE 0 END) as cur_production_qnty
			from wo_po_color_size_breakdown c, pro_garments_production_dtls a,pro_garments_production_mst b
			where c.id=a.color_size_break_down_id and a.mst_id=b.id and b.po_break_down_id=$hidden_po_break_down_id and a.status_active=1 and  b.item_number_id=$cbo_item_name and b.country_id=$cbo_country_name $country_ship_date_cond2 and a.color_size_break_down_id!=0 and a.production_type in(4,5) and b.id!=$txt_mst_id
			group by a.color_size_break_down_id");
			$color_pord_data=array();
			foreach($dtlsData as $row)
			{
				$color_pord_data[$row[csf("color_size_break_down_id")]]=$row[csf('production_qnty')]-$row[csf("cur_production_qnty")];
			}

 			$field_array="id, mst_id, production_type, color_size_break_down_id, production_qnty, reject_qty, bundle_qty,color_type_id";

			if(str_replace("'","",$sewing_production_variable)==2)//color level wise
			{
				$color_sizeID_arr=sql_select( "select id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name $country_ship_date_cond  and status_active=1 and is_deleted=0 order by id" );
				$colSizeID_arr=array();
				foreach($color_sizeID_arr as $val){
					$index = $val[csf("color_number_id")];
					$colSizeID_arr[$index]=$val[csf("id")];
				}

				$rowExBundle = array_filter(explode("**",$colorBundleVal));
				foreach($rowExBundle as $rowR=>$valR)
				{
					$colorSizeBunIDArr = explode("*",$valR);
					//echo $colorSizeBunIDArr[0]; die;
					$BunQtyArr[$colorSizeBunIDArr[0]]=$colorSizeBunIDArr[1];
				}

				// $colorIDvalue concate as colorID*Value**colorID*Value -------------------------//
				$rowExRej = array_filter(explode("**",$colorIDvalueRej));
				foreach($rowExRej as $rowR=>$valR)
				{
					$colorSizeRejIDArr = explode("*",$valR);

					//echo $colorSizeRejIDArr[0]; die;
					$rejQtyArr[$colorSizeRejIDArr[0]]=$colorSizeRejIDArr[1];
				}

				$rowEx = array_filter(explode("**",$colorIDvalue));
				//$dtls_id=return_next_id("id", "pro_garments_production_dtls", 1);
				$data_array="";$j=0;
				foreach($rowEx as $rowE=>$val)
				{
					$colorSizeNumberIDArr = explode("*",$val);
					if($is_control==1 && $user_level!=2)// Dont Hide ISD-23-00517
					{
						if($colorSizeNumberIDArr[1]>0)
						{
							if(($colorSizeNumberIDArr[1]*1)>($color_pord_data[$colSizeID_arr[$colorSizeNumberIDArr[0]]]*1))
							{
								echo "35**Production Quantity Not Over Sewing Input Qnty";
								//check_table_status( $_SESSION['menu_id'],0);
								disconnect($con);
								die;
							}
						}
					}
					if($colSizeID_arr[$colorSizeNumberIDArr[0]]!="")
					{
						//4 for Sewing Input Entry
						$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",  "pro_garments_production_dtls", $con );
						if($j==0)$data_array = "(".$dtls_id.",".$txt_mst_id.",5,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."','".$rejQtyArr[$colorSizeNumberIDArr[0]]."','".$BunQtyArr[$colorSizeNumberIDArr[0]]."',".$cbo_color_type.")";
						else $data_array .= ",(".$dtls_id.",".$txt_mst_id.",5,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."','".$rejQtyArr[$colorSizeNumberIDArr[0]]."','".$BunQtyArr[$colorSizeNumberIDArr[0]]."',".$cbo_color_type.")";
						//$dtls_id=$dtls_id+1;
						$j++;
					}
					else
					{
						echo "420**";die();
					}
				}
			}

			if(str_replace("'","",$sewing_production_variable)==3)//color and size wise
			{
				/* $color_sizeID_arr=sql_select( "select id,size_number_id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name $country_ship_date_cond and status_active=1 and is_deleted=0 order by size_number_id,color_number_id" );
				$colSizeID_arr=array();
				foreach($color_sizeID_arr as $val){
					$index = $val[csf("size_number_id")].$color_arr[$val[csf("color_number_id")]].$val[csf("color_number_id")];
					$colSizeID_arr[$index]=$val[csf("id")];
				} */

				$rowExBundle = array_filter(explode("***",$colorBundleVal));
				foreach($rowExBundle as $rowR=>$valR)
				{
					$colorAndSizeBun_arr = explode("*",$valR);
					/* $sizeID = $colorAndSizeBun_arr[0];
					$colorID = $colorAndSizeBun_arr[1];
					$colorSizeBun = $colorAndSizeBun_arr[2];
					$index = $sizeID.$color_arr[$colorID].$colorID;
					$BunQtyArr[$index]=$colorSizeBun; */

					$color_size_id = $colorAndSizeBun_arr[0];
					$colorSizeBun = $colorAndSizeBun_arr[1]; 
					$BunQtyArr[$color_size_id]=$colorSizeBun;
				}

				//	colorIDvalue concate as sizeID*colorID*value***sizeID*colorID*value	--------------------------//
				$rowExRej = array_filter(explode("***",$colorIDvalueRej));
				foreach($rowExRej as $rowR=>$valR)
				{
					$colorAndSizeRej_arr = explode("*",$valR);
					/* $sizeID = $colorAndSizeRej_arr[0];
					$colorID = $colorAndSizeRej_arr[1];
					$colorSizeRej = $colorAndSizeRej_arr[2];
					$index = $sizeID.$color_arr[$colorID].$colorID;
					$rejQtyArr[$index]=$colorSizeRej; */

					$color_size_id = $colorAndSizeRej_arr[0];
					$colorSizeRej = $colorAndSizeRej_arr[1]; 
					$rejQtyArr[$color_size_id]=$colorSizeRej;
				}

				$rowEx = array_filter(explode("***",$colorIDvalue));
				//$dtls_id=return_next_id("id", "pro_garments_production_dtls", 1);
				$data_array="";$j=0;
				foreach($rowEx as $rowE=>$valE)
				{
					$colorAndSizeAndValue_arr = explode("*",$valE);
					/* $sizeID = $colorAndSizeAndValue_arr[0];
					$colorID = $colorAndSizeAndValue_arr[1];
					$colorSizeValue = $colorAndSizeAndValue_arr[2];
					$index = $sizeID.$color_arr[$colorID].$colorID; */

					$color_size_break_down_id = $colorAndSizeAndValue_arr[0];
					$colorSizeValue  = $colorAndSizeAndValue_arr[1];  
					

					if($is_control==1 && $user_level!=2) // Dont Hide ISD-23-00517
					{
						if($colorSizeValue>0)
						{
							if(($colorSizeValue*1)>($color_pord_data[$color_size_break_down_id]*1))
							{
								echo "35**Production Quantity Not Over Sewing Input Qnty";
								//	check_table_status( $_SESSION['menu_id'],0);
								disconnect($con);
								die;
							}
						}
					}

					/* if($colSizeID_arr[$index]!="")
					{ */
						//4 for Sewing Input Entry
						$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",   "pro_garments_production_dtls", $con );
						if($j==0)$data_array = "(".$dtls_id.",".$txt_mst_id.",5,'".$color_size_break_down_id."','".$colorSizeValue."','".$rejQtyArr[$color_size_break_down_id]."','".$BunQtyArr[$color_size_break_down_id]."',".$cbo_color_type.")";
						else $data_array .= ",(".$dtls_id.",".$txt_mst_id.",5,'".$color_size_break_down_id."','".$colorSizeValue."','".$rejQtyArr[$color_size_break_down_id]."','".$BunQtyArr[$color_size_break_down_id]."',".$cbo_color_type.")";
						//$dtls_id=$dtls_id+1;
						$j++;
					/* }
					else
					{
						echo "420**";die();
					} */
				}
			}
 			//$dtlsrID=sql_insert("pro_garments_production_dtls",$field_array,$data_array,1);
		}//end cond
		//echo $data_array; die;

		$dtlsrDelete = execute_query("UPDATE pro_garments_production_dtls SET status_active=0,is_deleted=1 where mst_id=$txt_mst_id",0);
		$defectQ=true;
		$data_array_defect="";
		$save_string=explode(",",str_replace("'","",$save_data));

		// if(count($save_string)>0 && str_replace("'","",$save_data)!="")
		if(count($save_string)>0)
		{
 			//order_wise_pro_details table data insert START-----//
			$field_array_defect="id, mst_id, production_type, po_break_down_id, defect_type_id, defect_point_id, defect_qty, inserted_by, insert_date";
 			$defect_array=array();
			for($i=0;$i<count($save_string);$i++)
			{
				$order_dtls=explode("**",$save_string[$i]);
				$defect_update_id=$order_dtls[0];
				$defect_point_id=$order_dtls[1];
				$defect_qnty=$order_dtls[2];

				if( array_key_exists($defect_point_id,$defect_array) )
				{
					$defect_array[$defect_point_id]=$defect_qnty;
				}
				else
				{
					$defect_array[$defect_point_id]=$defect_qnty;
				}
			}
			$i=0;
			foreach($defect_array as $key=>$val)
			{
				if( $i>0 ) $data_array_defect.=",";

				if( $dft_id=="" )
				{
					//$dft_id=return_next_id("id", "pro_gmts_prod_dft", 1);
					$dft_id = return_next_id_by_sequence(  "pro_gmts_prod_dft_seq","pro_gmts_prod_dft", $con );
				}
				else
				{
					$dft_id = return_next_id_by_sequence(  "pro_gmts_prod_dft_seq", "pro_gmts_prod_dft", $con );
				}
				$defectPointId=$key;
				$defect_qty=$val;
				$data_array_defect.="(".$dft_id.",".$txt_mst_id.",5,".$hidden_po_break_down_id.",".$defect_type_id.",".$defectPointId.",'".$defect_qty."',".$user_id.",'".$pc_date_time."')";
				$i++;
			}
		}
		if($data_array_defect!="")
		{
			//echo "10**INSERT INTO pro_gmts_prod_dft (".$field_array_defect.") VALUES ".$data_array_defect."";// die;
			//echo "5**DELETE FROM pro_gmts_prod_dft WHERE mst_id='$txt_mst_id' and defect_type_id=1";die;
			$query3=execute_query("DELETE FROM pro_gmts_prod_dft WHERE mst_id=$txt_mst_id and defect_type_id=1 and production_type=5");
			$defectQ=sql_insert("pro_gmts_prod_dft",$field_array_defect,$data_array_defect,1);
		}
		$defectSpot=true;
		$data_array_defectsp="";
		$save_dataSpot=explode(",",str_replace("'","",$save_dataSpot));
		// if(count($save_dataSpot)>0 && str_replace("'","",$save_dataSpot)!="")
		if(count($save_dataSpot)>0)
		{
 			//order_wise_pro_details table data insert START-----//
			$field_array_defectsp="id, mst_id, production_type, po_break_down_id, defect_type_id, defect_point_id, defect_qty, inserted_by, insert_date";
 			$defectSpt_array=array();
			for($i=0;$i<count($save_dataSpot);$i++)
			{
				$order_dtls=explode("**",$save_dataSpot[$i]);
				$defect_update_id=$order_dtls[0];
				$defectsp_point_id=$order_dtls[1];
				$defect_qnty=$order_dtls[2];

				if( array_key_exists($defectsp_point_id,$defectSpt_array) )
				{
					$defectSpt_array[$defectsp_point_id]=$defect_qnty;
				}
				else
				{
					$defectSpt_array[$defectsp_point_id]=$defect_qnty;
				}
			}
			$i=0;
			foreach($defectSpt_array as $keysp=>$valsp)
			{
				if( $i>0 ) $data_array_defectsp.=",";

				if( $dftSp_id=="" )
				{
					//$dft_id=return_next_id("id", "pro_gmts_prod_dft", 1);
					$dftSp_id = return_next_id_by_sequence(  "pro_gmts_prod_dft_seq", "pro_gmts_prod_dft", $con );
				}
				else
				{
					$dftSp_id = return_next_id_by_sequence(  "pro_gmts_prod_dft_seq",  "pro_gmts_prod_dft", $con );
				}

				$defectspPointId=$keysp;
				$defectsp_qty=$valsp;
				$data_array_defectsp.="(".$dftSp_id.",".$txt_mst_id.",5,".$hidden_po_break_down_id.",".$defectSpot_type_id.",".$defectspPointId.",'".$defectsp_qty."',".$user_id.",'".$pc_date_time."')";
				$i++;
			}
		}

		if($data_array_defectsp!="")
		{
			//echo "10**INSERT INTO pro_gmts_prod_dft (".$field_array_defectsp.") VALUES ".$data_array_defectsp.""; die;
			//echo "DELETE FROM pro_gmts_prod_dft WHERE mst_id='$txt_mst_id' and defect_type_id=2";die;
			$query4=execute_query("DELETE FROM pro_gmts_prod_dft WHERE mst_id=$txt_mst_id and defect_type_id=2 and production_type=5");
			$defectSpot=sql_insert("pro_gmts_prod_dft",$field_array_defectsp,$data_array_defectsp,1);
		}
		// Front Part Start
		$defectFront=true;
		$data_array_defectFr="";
		$save_stringFront=explode(",",str_replace("'","",$save_dataFront));
		//$dft_front_idF=="";

		// if(count($save_stringFront)>0 && str_replace("'","",$save_dataFront)!="")
		if(count($save_stringFront)>0)
		{
 			//order_wise_pro_details table data insert START-----//
			$dft_id=return_next_id("id", "pro_gmts_prod_dft", 1);
			$field_array_defectFr="id, mst_id, production_type, po_break_down_id, defect_type_id, defect_point_id, defect_qty, inserted_by, insert_date";
 			$defect_arrayFr=array();
			for($i=0;$i<count($save_stringFront);$i++)
			{
				$order_dtls=explode("**",$save_stringFront[$i]);
				$defect_update_id=$order_dtls[0];
				$defect_point_id=$order_dtls[1];
				$defect_qnty=$order_dtls[2];

				if( array_key_exists($defect_point_id,$defect_arrayFr) )
				{
					$defect_arrayFr[$defect_point_id]=$defect_qnty;
				}
				else
				{
					$defect_arrayFr[$defect_point_id]=$defect_qnty;
				}
				//$dd_arr[$i]=$i;
			}
			//echo "10**";
			//print_r($defect_array);die;
			$i=0;
			foreach($defect_arrayFr as $key=>$val)
			{
				if( $i>0 ) $data_array_defectFr.=",";
				if( $dft_front_idF=="" )
				{
					//$dft_id=return_next_id("id", "pro_gmts_prod_dft", 1);
					$dft_front_idF = return_next_id_by_sequence(  "pro_gmts_prod_dft_seq",  "pro_gmts_prod_dft", $con );
				}
				else
				{
					$dft_front_idF = return_next_id_by_sequence(  "pro_gmts_prod_dft_seq",  "pro_gmts_prod_dft", $con );
				}
			//	print_r($defect_array);die;
				$defectPointId=$key;
				$defect_qty=$val;
				//$dft_id_arr[$dft_id]=$dft_id;
				$data_array_defectFr.="(".$dft_front_idF.",".$txt_mst_id.",5,".$hidden_po_break_down_id.",".$defectFront_type_id.",".$defectPointId.",'".$defect_qty."',".$user_id.",'".$pc_date_time."')";				//$dft_id = $dft_id + 1;
				$i++;
			}
		}
		//echo "10**DDD";
		//print_r($defect_array);die;
		if($data_array_defectFr!="")
		{
			//echo "10**INSERT INTO pro_gmts_prod_dft (".$field_array_defectFr.") VALUES ".$data_array_defectFr.""; die;
			$query5=execute_query("DELETE FROM pro_gmts_prod_dft WHERE mst_id=$txt_mst_id and defect_type_id=4 and production_type=5");
			$defectFront=sql_insert("pro_gmts_prod_dft",$field_array_defectFr,$data_array_defectFr,1);
		}
		//echo "10**X=INSERT INTO pro_gmts_prod_dft (".$field_array_defectFr.") VALUES ".$data_array_defectFr.""; die;
		//Front part End
		// Inside Part Start
		$defectInside=true;
		$data_array_defectIn="";
		$save_stringInside=explode(",",str_replace("'","",$save_dataInside));
		//$dft_front_idF=="";

		// if(count($save_stringFront)>0 && str_replace("'","",$save_dataFront)!="")
		if(count($save_stringInside)>0)
		{
 			//order_wise_pro_details table data insert START-----//
			$dft_id=return_next_id("id", "pro_gmts_prod_dft", 1);
			$field_array_defectIn="id, mst_id, production_type, po_break_down_id, defect_type_id, defect_point_id, defect_qty, inserted_by, insert_date";
 			$defect_arrayIn=array();
			for($i=0;$i<count($save_stringInside);$i++)
			{
				$order_dtls=explode("**",$save_stringInside[$i]);
				$defect_update_id=$order_dtls[0];
				$defect_point_id=$order_dtls[1];
				$defect_qnty=$order_dtls[2];

				if( array_key_exists($defect_point_id,$defect_arrayIn) )
				{
					$defect_arrayIn[$defect_point_id]=$defect_qnty;
				}
				else
				{
					$defect_arrayIn[$defect_point_id]=$defect_qnty;
				}
				//$dd_arr[$i]=$i;
			}
			//echo "10**";
			//print_r($defect_array);die;
			$i=0;
			foreach($defect_arrayIn as $key=>$val)
			{
				if( $i>0 ) $data_array_defectIn.=",";
				if( $dft_front_idF=="" )
				{
					//$dft_id=return_next_id("id", "pro_gmts_prod_dft", 1);
					$dft_front_idF = return_next_id_by_sequence(  "pro_gmts_prod_dft_seq",  "pro_gmts_prod_dft", $con );
				}
				else
				{
					$dft_front_idF = return_next_id_by_sequence(  "pro_gmts_prod_dft_seq",  "pro_gmts_prod_dft", $con );
				}
			//	print_r($defect_array);die;
				$defectPointId=$key;
				$defect_qty=$val;
				//$dft_id_arr[$dft_id]=$dft_id;
				$data_array_defectIn.="(".$dft_front_idF.",".$txt_mst_id.",5,".$hidden_po_break_down_id.",".$defectInside_type_id.",".$defectPointId.",'".$defect_qty."',".$user_id.",'".$pc_date_time."')";				//$dft_id = $dft_id + 1;
				$i++;
			}
		}
		//echo "10**DDD";
		//print_r($defect_array);die;
		if($data_array_defectIn!="")
		{
			//echo "10**INSERT INTO pro_gmts_prod_dft (".$field_array_defectFr.") VALUES ".$data_array_defectFr.""; die;
			$query5=execute_query("DELETE FROM pro_gmts_prod_dft WHERE mst_id=$txt_mst_id and defect_type_id=4 and production_type=5");
			$defectInside=sql_insert("pro_gmts_prod_dft",$field_array_defectIn,$data_array_defectIn,1);
		}
		//echo "10**X=INSERT INTO pro_gmts_prod_dft (".$field_array_defectFr.") VALUES ".$data_array_defectFr.""; die;
		//Inside part End
		// Topside Part Start
        $defectTopside=true;
		$data_array_defectTo="";
		$save_stringTopside=explode(",",str_replace("'","",$save_dataTopside));
		//$dft_front_idF=="";

		// if(count($save_stringFront)>0 && str_replace("'","",$save_dataFront)!="")
		if(count($save_stringTopside)>0)
		{
 			//order_wise_pro_details table data insert START-----//
			$dft_id=return_next_id("id", "pro_gmts_prod_dft", 1);
			$field_array_defectTo="id, mst_id, production_type, po_break_down_id, defect_type_id, defect_point_id, defect_qty, inserted_by, insert_date";
 			$defect_arrayTo=array();
			for($i=0;$i<count($save_stringTopside);$i++)
			{
				$order_dtls=explode("**",$save_stringTopside[$i]);
				$defect_update_id=$order_dtls[0];
				$defect_point_id=$order_dtls[1];
				$defect_qnty=$order_dtls[2];

				if( array_key_exists($defect_point_id,$defect_arrayTo) )
				{
					$defect_arrayTo[$defect_point_id]=$defect_qnty;
				}
				else
				{
					$defect_arrayTo[$defect_point_id]=$defect_qnty;
				}
				//$dd_arr[$i]=$i;
			}
			//echo "10**";
			//print_r($defect_array);die;
			$i=0;
			foreach($defect_arrayTo as $key=>$val)
			{
				if( $i>0 ) $data_array_defectIn.=",";
				if( $dft_front_idF=="" )
				{
					//$dft_id=return_next_id("id", "pro_gmts_prod_dft", 1);
					$dft_front_idF = return_next_id_by_sequence(  "pro_gmts_prod_dft_seq",  "pro_gmts_prod_dft", $con );
				}
				else
				{
					$dft_front_idF = return_next_id_by_sequence(  "pro_gmts_prod_dft_seq",  "pro_gmts_prod_dft", $con );
				}
			//	print_r($defect_array);die;
				$defectPointId=$key;
				$defect_qty=$val;
				//$dft_id_arr[$dft_id]=$dft_id;
				$data_array_defectTo.="(".$dft_front_idF.",".$txt_mst_id.",5,".$hidden_po_break_down_id.",".$defectTopside_type_id.",".$defectPointId.",'".$defect_qty."',".$user_id.",'".$pc_date_time."')";				//$dft_id = $dft_id + 1;
				$i++;
			}
		}
		//echo "10**DDD";
		//print_r($defect_array);die;
		if($data_array_defectTo!="")
		{
			//echo "10**INSERT INTO pro_gmts_prod_dft (".$field_array_defectFr.") VALUES ".$data_array_defectFr.""; die;
			$query5=execute_query("DELETE FROM pro_gmts_prod_dft WHERE mst_id=$txt_mst_id and defect_type_id=4 and production_type=5");
			$defectTopside=sql_insert("pro_gmts_prod_dft",$field_array_defectTo,$data_array_defectTo,1);
		}
		//echo "10**X=INSERT INTO pro_gmts_prod_dft (".$field_array_defectFr.") VALUES ".$data_array_defectFr.""; die;
		//Topside part End
		// Collar Part Start
		$defectCollar=true;
		$data_array_defectCo="";
		$save_stringCollar=explode(",",str_replace("'","",$save_dataCollar));
		//$dft_front_idF=="";

		// if(count($save_stringFront)>0 && str_replace("'","",$save_dataFront)!="")
		if(count($save_stringCollar)>0)
		{
 			//order_wise_pro_details table data insert START-----//
			$dft_id=return_next_id("id", "pro_gmts_prod_dft", 1);
			$field_array_defectCo="id, mst_id, production_type, po_break_down_id, defect_type_id, defect_point_id, defect_qty, inserted_by, insert_date";
 			$defect_arrayCo=array();
			for($i=0;$i<count($save_stringCollar);$i++)
			{
				$order_dtls=explode("**",$save_stringCollar[$i]);
				$defect_update_id=$order_dtls[0];
				$defect_point_id=$order_dtls[1];
				$defect_qnty=$order_dtls[2];

				if( array_key_exists($defect_point_id,$defect_arrayCo) )
				{
					$defect_arrayCo[$defect_point_id]=$defect_qnty;
				}
				else
				{
					$defect_arrayCo[$defect_point_id]=$defect_qnty;
				}
				//$dd_arr[$i]=$i;
			}
			//echo "10**";
			//print_r($defect_array);die;
			$i=0;
			foreach($defect_arrayCo as $key=>$val)
			{
				if( $i>0 ) $data_array_defectCo.=",";
				if( $dft_front_idF=="" )
				{
					//$dft_id=return_next_id("id", "pro_gmts_prod_dft", 1);
					$dft_front_idF = return_next_id_by_sequence(  "pro_gmts_prod_dft_seq",  "pro_gmts_prod_dft", $con );
				}
				else
				{
					$dft_front_idF = return_next_id_by_sequence(  "pro_gmts_prod_dft_seq",  "pro_gmts_prod_dft", $con );
				}
			//	print_r($defect_array);die;
				$defectPointId=$key;
				$defect_qty=$val;
				//$dft_id_arr[$dft_id]=$dft_id;
				$data_array_defectCo.="(".$dft_front_idF.",".$txt_mst_id.",5,".$hidden_po_break_down_id.",".$defectCollar_type_id.",".$defectPointId.",'".$defect_qty."',".$user_id.",'".$pc_date_time."')";				//$dft_id = $dft_id + 1;
				$i++;
			}
		}
		//echo "10**DDD";
		//print_r($defect_array);die;
		if($data_array_defectCo!="")
		{
			//echo "10**INSERT INTO pro_gmts_prod_dft (".$field_array_defectFr.") VALUES ".$data_array_defectFr.""; die;
			$query5=execute_query("DELETE FROM pro_gmts_prod_dft WHERE mst_id=$txt_mst_id and defect_type_id=4 and production_type=5");
			$defectCollar=sql_insert("pro_gmts_prod_dft",$field_array_defectCo,$data_array_defectCo,1);
		}
		//echo "10**X=INSERT INTO pro_gmts_prod_dft (".$field_array_defectFr.") VALUES ".$data_array_defectFr.""; die;
		//Collar part End
        // Armhole Part Start
		$defectArmhole=true;
		$data_array_defectAr="";
		$save_stringArmhole=explode(",",str_replace("'","",$save_dataArmhole));
		//$dft_front_idF=="";

		// if(count($save_stringFront)>0 && str_replace("'","",$save_dataFront)!="")
		if(count($save_stringArmhole)>0)
		{
 			//order_wise_pro_details table data insert START-----//
			$dft_id=return_next_id("id", "pro_gmts_prod_dft", 1);
			$field_array_defectAr="id, mst_id, production_type, po_break_down_id, defect_type_id, defect_point_id, defect_qty, inserted_by, insert_date";
 			$defect_arrayAr=array();
			for($i=0;$i<count($save_stringArmhole);$i++)
			{
				$order_dtls=explode("**",$save_stringArmhole[$i]);
				$defect_update_id=$order_dtls[0];
				$defect_point_id=$order_dtls[1];
				$defect_qnty=$order_dtls[2];

				if( array_key_exists($defect_point_id,$defect_arrayAr) )
				{
					$defect_arrayAr[$defect_point_id]=$defect_qnty;
				}
				else
				{
					$defect_arrayAr[$defect_point_id]=$defect_qnty;
				}
				//$dd_arr[$i]=$i;
			}
			//echo "10**";
			//print_r($defect_array);die;
			$i=0;
			foreach($defect_arrayAr as $key=>$val)
			{
				if( $i>0 ) $data_array_defectAr.=",";
				if( $dft_front_idF=="" )
				{
					//$dft_id=return_next_id("id", "pro_gmts_prod_dft", 1);
					$dft_front_idF = return_next_id_by_sequence(  "pro_gmts_prod_dft_seq",  "pro_gmts_prod_dft", $con );
				}
				else
				{
					$dft_front_idF = return_next_id_by_sequence(  "pro_gmts_prod_dft_seq",  "pro_gmts_prod_dft", $con );
				}
			//	print_r($defect_array);die;
				$defectPointId=$key;
				$defect_qty=$val;
				//$dft_id_arr[$dft_id]=$dft_id;
				$data_array_defectAr.="(".$dft_front_idF.",".$txt_mst_id.",5,".$hidden_po_break_down_id.",".$defectArmhole_type_id.",".$defectPointId.",'".$defect_qty."',".$user_id.",'".$pc_date_time."')";				//$dft_id = $dft_id + 1;
				$i++;
			}
		}
		//echo "10**DDD";
		//print_r($defect_array);die;
		if($data_array_defectAr!="")
		{
			//echo "10**INSERT INTO pro_gmts_prod_dft (".$field_array_defectFr.") VALUES ".$data_array_defectFr.""; die;
			$query5=execute_query("DELETE FROM pro_gmts_prod_dft WHERE mst_id=$txt_mst_id and defect_type_id=4 and production_type=5");
			$defectArmhole=sql_insert("pro_gmts_prod_dft",$field_array_defectAr,$data_array_defectAr,1);
		}
		//echo "10**X=INSERT INTO pro_gmts_prod_dft (".$field_array_defectFr.") VALUES ".$data_array_defectFr.""; die;
		// Armhole Part End
		// MakeCheck Part Start
        $defectMake=true;
		$data_array_defectMak="";
		$save_stringMake=explode(",",str_replace("'","",$save_dataMake));
		//$dft_front_idF=="";

		// if(count($save_stringFront)>0 && str_replace("'","",$save_dataFront)!="")
		if(count($save_stringMake)>0)
		{
 			//order_wise_pro_details table data insert START-----//
			$dft_id=return_next_id("id", "pro_gmts_prod_dft", 1);
			$field_array_defectMak="id, mst_id, production_type, po_break_down_id, defect_type_id, defect_point_id, defect_qty, inserted_by, insert_date";
 			$defect_arrayMak=array();
			for($i=0;$i<count($save_stringMake);$i++)
			{
				$order_dtls=explode("**",$save_stringMake[$i]);
				$defect_update_id=$order_dtls[0];
				$defect_point_id=$order_dtls[1];
				$defect_qnty=$order_dtls[2];

				if( array_key_exists($defect_point_id,$defect_arrayMak) )
				{
					$defect_arrayMak[$defect_point_id]=$defect_qnty;
				}
				else
				{
					$defect_arrayMak[$defect_point_id]=$defect_qnty;
				}
				//$dd_arr[$i]=$i;
			}
			//echo "10**";
			//print_r($defect_array);die;
			$i=0;
			foreach($defect_arrayMak as $key=>$val)
			{
				if( $i>0 ) $data_array_defectMak.=",";
				if( $dft_front_idF=="" )
				{
					//$dft_id=return_next_id("id", "pro_gmts_prod_dft", 1);
					$dft_front_idF = return_next_id_by_sequence(  "pro_gmts_prod_dft_seq",  "pro_gmts_prod_dft", $con );
				}
				else
				{
					$dft_front_idF = return_next_id_by_sequence(  "pro_gmts_prod_dft_seq",  "pro_gmts_prod_dft", $con );
				}
			//	print_r($defect_array);die;
				$defectPointId=$key;
				$defect_qty=$val;
				//$dft_id_arr[$dft_id]=$dft_id;
				$data_array_defectMak.="(".$dft_front_idF.",".$txt_mst_id.",5,".$hidden_po_break_down_id.",".$defectMake_type_id.",".$defectPointId.",'".$defect_qty."',".$user_id.",'".$pc_date_time."')";				//$dft_id = $dft_id + 1;
				$i++;
			}
		}
		//echo "10**DDD";
		//print_r($defect_array);die;
		if($data_array_defectMak!="")
		{
			//echo "10**INSERT INTO pro_gmts_prod_dft (".$field_array_defectFr.") VALUES ".$data_array_defectFr.""; die;
			$query5=execute_query("DELETE FROM pro_gmts_prod_dft WHERE mst_id=$txt_mst_id and defect_type_id=4 and production_type=5");
			$defectMake=sql_insert("pro_gmts_prod_dft",$field_array_defectMak,$data_array_defectMak,1);
		}
		// MakeCheck Part End
		// Back Part Start
		$defectBack=true;
		$data_array_defectbk="";
		$save_dataStringBack=explode(",",str_replace("'","",$save_dataBack));
		$dftbk_id=="";

		// if(count($save_dataStringBack)>0 && str_replace("'","",$save_dataBack)!="")
		if(count($save_dataStringBack)>0)
		{
 			//order_wise_pro_details table data insert START-----//
			//$dft_bk_id=return_next_id("id", "pro_gmts_prod_dft", 1);
			$field_array_defectbk="id, mst_id, production_type, po_break_down_id, defect_type_id, defect_point_id, defect_qty, inserted_by, insert_date";
 			$defectBack_array=array();
			for($i=0;$i<count($save_dataStringBack);$i++)
			{
				$order_dtls=explode("**",$save_dataStringBack[$i]);
				$defect_update_id=$order_dtls[0];
				$defectbk_point_id=$order_dtls[1];
				$defect_qnty=$order_dtls[2];

				if( array_key_exists($defectbk_point_id,$defectBack_array) )
				{
					$defectBack_array[$defectbk_point_id]=$defect_qnty;
				}
				else
				{
					$defectBack_array[$defectbk_point_id]=$defect_qnty;
				}
			}
			$i=0;
			foreach($defectBack_array as $keysp=>$valsp)
			{
				if( $i>0 ) $data_array_defectbk.=",";
				if( $dft_bk_id=="" )
				{
					//$dft_id=return_next_id("id", "pro_gmts_prod_dft", 1);
					$dft_bk_id = return_next_id_by_sequence(  "pro_gmts_prod_dft_seq",  "pro_gmts_prod_dft", $con );
				}
				else
				{
					$dft_bk_id = return_next_id_by_sequence(  "pro_gmts_prod_dft_seq",  "pro_gmts_prod_dft", $con );
				}

				$defectbkPointId=$keysp;
				$defectbk_qty=$valsp;
				$data_array_defectbk.="(".$dft_bk_id.",".$txt_mst_id.",5,".$hidden_po_break_down_id.",".$defectBack_type_id.",".$defectbkPointId.",'".$defectbk_qty."',".$user_id.",'".$pc_date_time."')";
				//$dft_bk_id = $dft_bk_id + 1;
				$i++;
			}
		}

		if($data_array_defectbk!="")
		{
			//echo "10**INSERT INTO pro_gmts_prod_dft_gross (".$field_array_defectbk.") VALUES ".$data_array_defectbk.""; die;
			$query6=execute_query("DELETE FROM pro_gmts_prod_dft WHERE mst_id=$txt_mst_id and defect_type_id=5 and production_type=5");
			$defectBk=sql_insert("pro_gmts_prod_dft",$field_array_defectbk,$data_array_defectbk,1);
			//echo "10**=".$defectBk.'ASA';die;
		}
		//Back part End
		$defectWest=true;
		$data_array_defectWt="";
		$save_dataStringWest=explode(",",str_replace("'","",$save_dataWest));
		$dftwt_id=="";

		// if(count($save_dataStringWest)>0 && str_replace("'","",$save_dataWest)!="")
		if(count($save_dataStringWest)>0)
		{
 			//order_wise_pro_details table data insert START-----//
			//$dft_wt_id=return_next_id("id", "pro_gmts_prod_dft_gross", 1);
			$field_array_defectwt="id, mst_id, production_type, po_break_down_id, defect_type_id, defect_point_id, defect_qty, inserted_by, insert_date";
 			$defectWest_array=array();
			for($i=0;$i<count($save_dataStringWest);$i++)
			{
				$order_dtls=explode("**",$save_dataStringWest[$i]);
				$defect_update_id=$order_dtls[0];
				$defectbk_point_id=$order_dtls[1];
				$defect_qnty=$order_dtls[2];

				if( array_key_exists($defectbk_point_id,$defectWest_array) )
				{
					$defectWest_array[$defectbk_point_id]=$defect_qnty;
				}
				else
				{
					$defectWest_array[$defectbk_point_id]=$defect_qnty;
				}
			}
			$i=0;
			foreach($defectWest_array as $keysp=>$valsp)
			{
				if( $i>0 ) $data_array_defectwt.=",";
				if( $dft_wt_id=="" )
				{
					//$dft_id=return_next_id("id", "pro_gmts_prod_dft", 1);
					$dft_wt_id = return_next_id_by_sequence(  "pro_gmts_prod_dft_seq",  "pro_gmts_prod_dft", $con );
				}
				else
				{
					$dft_wt_id = return_next_id_by_sequence(  "pro_gmts_prod_dft_seq",  "pro_gmts_prod_dft", $con );
				}


				$defectbkPointId=$keysp;
				$defectbk_qty=$valsp;
				$data_array_defectwt.="(".$dft_wt_id.",".$txt_mst_id.",5,".$hidden_po_break_down_id.",".$defectWest_type_id.",".$defectbkPointId.",'".$defectbk_qty."',".$user_id.",'".$pc_date_time."')";
				//$dft_wt_id = $dft_wt_id + 1;
				$i++;
			}
		}

		if($data_array_defectwt!="")
		{
			//echo "10**INSERT INTO pro_gmts_prod_dft (".$field_array_defectwt.") VALUES ".$data_array_defectwt.""; die;
			$query7=execute_query("DELETE FROM pro_gmts_prod_dft WHERE mst_id=$txt_mst_id and defect_type_id=6 and production_type=5");
			$defectWt=sql_insert("pro_gmts_prod_dft",$field_array_defectwt,$data_array_defectwt,1);
		}
		//West Band End
		$defectMeasure=true;
		$data_array_defectme="";
		$save_dataStringMeasure=explode(",",str_replace("'","",$save_dataMeasure));
		// if(count($save_dataStringMeasure)>0 && str_replace("'","",$save_dataMeasure)!="")
		if(count($save_dataStringMeasure)>0)
		{
 			//order_wise_pro_details table data insert START-----//
			$field_array_defectMe="id, mst_id, production_type, po_break_down_id, defect_type_id, defect_point_id, defect_qty, inserted_by, insert_date";
 			$defectMeasure_array=array();
			for($i=0;$i<count($save_dataStringMeasure);$i++)
			{
				$order_dtls=explode("**",$save_dataStringMeasure[$i]);
				$defect_update_id=$order_dtls[0];
				$defectbk_point_id=$order_dtls[1];
				$defect_qnty=$order_dtls[2];

				if( array_key_exists($defectbk_point_id,$defectMeasure_array) )
				{
					$defectMeasure_array[$defectbk_point_id]=$defect_qnty;
				}
				else
				{
					$defectMeasure_array[$defectbk_point_id]=$defect_qnty;
				}
			}
			$i=0;
			foreach($defectMeasure_array as $keysp=>$valsp)
			{
				if( $i>0 ) $data_array_defectme.=",";
				if( $dft_wt_id=="" )
				{
					//$dft_id=return_next_id("id", "pro_gmts_prod_dft", 1);
					$dft_me_id = return_next_id_by_sequence(  "pro_gmts_prod_dft_seq",  "pro_gmts_prod_dft", $con );
				}
				else
				{
					$dft_me_id = return_next_id_by_sequence(  "pro_gmts_prod_dft_seq",  "pro_gmts_prod_dft", $con );
				}

				$defectbkPointId=$keysp;
				$defectbk_qty=$valsp;
				$data_array_defectme.="(".$dft_me_id.",".$txt_mst_id.",5,".$hidden_po_break_down_id.",".$defectMeasure_type_id.",".$defectbkPointId.",'".$defectbk_qty."',".$user_id.",'".$pc_date_time."')";
				//$dft_me_id = $dft_me_id + 1;
				$i++;
			}
		}

		if($data_array_defectme!="")
		{
			//echo "10**=INSERT INTO pro_gmts_prod_dft (".$field_array_defectMe.") VALUES ".$data_array_defectme.""; die;
			$query8=execute_query("DELETE FROM pro_gmts_prod_dft WHERE mst_id=$txt_mst_id and defect_type_id=7 and production_type=5");
			$defectme=sql_insert("pro_gmts_prod_dft",$field_array_defectMe,$data_array_defectme,0);
		}


		// Reject Part
		$defectReject=true;
		$data_array_defect_reject="";
		$save_dataReject=explode(",",str_replace("'","",$save_dataReject));
		if(count($save_dataReject)>0 && str_replace("'","",$save_dataReject)!="")
		{
 			//order_wise_pro_details table data insert START-----//
			$field_array_defectsp="id, mst_id, production_type, po_break_down_id, defect_type_id, defect_point_id, defect_qty, inserted_by, insert_date";
 			$defectReject_array=array();
			for($i=0;$i<count($save_dataReject);$i++)
			{
				$order_dtls=explode("**",$save_dataReject[$i]);
				$defect_update_id=$order_dtls[0];
				$defectsp_point_id=$order_dtls[1];
				$defect_qnty=$order_dtls[2];

				if( array_key_exists($defectsp_point_id,$defectReject_array) )
				{
					$defectReject_array[$defectsp_point_id]=$defect_qnty;
				}
				else
				{
					$defectReject_array[$defectsp_point_id]=$defect_qnty;
				}
			}
			$i=0;
			foreach($defectReject_array as $keysp=>$valsp)
			{
				if( $i>0 ) $data_array_defect_reject.=",";
				//if( $dftSp_id=="" ) $dftSp_id=return_next_id("id", "pro_gmts_prod_dft", 1); else $dftSp_id = $dftSp_id+1;
				$dftSp_id=return_next_id_by_sequence("PRO_GMTS_PROD_DFT_SEQ", "pro_gmts_prod_dft", $con);
				$defectspPointId=$keysp;
				$defectsp_qty=$valsp;
				$data_array_defect_reject.="(".$dftSp_id.",".$txt_mst_id.",5,".$hidden_po_break_down_id.",".$defectReject_type_id.",".$defectspPointId.",'".$defectsp_qty."',".$user_id.",'".$pc_date_time."')";
				$i++;
			}

			if($data_array_defect_reject!="")
			{
				// echo "10**INSERT INTO pro_gmts_prod_dft (".$field_array_defectsp.") VALUES ".$data_array_defect_reject.""; die;
				//echo "DELETE FROM pro_gmts_prod_dft WHERE mst_id='$txt_mst_id' and defect_type_id=2";die;
				$query4=execute_query("DELETE FROM pro_gmts_prod_dft WHERE mst_id=$txt_mst_id and defect_type_id=3 and production_type=5",1);
				$defectReject=sql_insert("pro_gmts_prod_dft",$field_array_defectsp,$data_array_defect_reject,1);
			}
		}

		// echo "10**=INSERT INTO pro_garments_production_mst (".$field_array1.") VALUES ".$data_array1.""; die;

		$rID=sql_update("pro_garments_production_mst",$field_array1,$data_array1,"id","".$txt_mst_id."",1);

		if(str_replace("'","",$sewing_production_variable)!=1 && str_replace("'","",$txt_mst_id)!='')// check is not gross level
		{
			$dtlsrID=sql_insert("pro_garments_production_dtls",$field_array,$data_array,1);
		}

		//release lock table
		//check_table_status( $_SESSION['menu_id'],0);
		// echo "10**$rID ** $dtlsrDelete ** $dtlsrID";die();

		if($db_type==0)
		{
			if(str_replace("'","",$sewing_production_variable)!=1)
			{
				if($rID && $dtlsrDelete && $dtlsrID)
				{
					mysql_query("COMMIT");
					echo "1**".str_replace("'","",$hidden_po_break_down_id);
				}
				else
				{
					mysql_query("ROLLBACK");
					echo "10**".str_replace("'","",$hidden_po_break_down_id);
				}
			}
			else
			{
				if($rID)
				{
					mysql_query("COMMIT");
					echo "1**".str_replace("'","",$hidden_po_break_down_id);
				}
				else
				{
					mysql_query("ROLLBACK");
					echo "10**".str_replace("'","",$hidden_po_break_down_id);
				}
			}

		}
		else if($db_type==2 || $db_type==1 )
		{
			if(str_replace("'","",$sewing_production_variable)!=1)
			{
				if($rID && $dtlsrID )
				{
					oci_commit($con);
					echo "1**".str_replace("'","",$hidden_po_break_down_id);
				}
				else
				{
					oci_rollback($con);
					echo "10**".str_replace("'","",$hidden_po_break_down_id);
				}
			}
			else
			{
				if($rID)
				{
					oci_commit($con);
					echo "1**".str_replace("'","",$hidden_po_break_down_id);
				}
				else
				{
					oci_rollback($con);
					echo "10**".str_replace("'","",$hidden_po_break_down_id);
				}
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)  // Delete Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
		$fin_sql="SELECT po_break_down_id,color_id,size_id FROM gmt_finishing_receive_dtls  WHERE status_active=1 and is_deleted=0 and po_break_down_id=$hidden_po_break_down_id and item_id=$cbo_item_name and country_id=$cbo_country_name";
		$fin_res=sql_select($fin_sql);
		if(count($fin_res)>0)
		{
			if($db_type==0)
			{
				mysql_query("ROLLBACK");
				echo "gmt_finishing_receive_delete**".$hidden_po_break_down_id;
			}
			if($db_type==2 || $db_type==1 )
			{
				oci_rollback($con);
				echo "gmt_finishing_receive_delete**".$hidden_po_break_down_id;
			}
			disconnect($con);
			die;
		}

		$ok=true;
		$date_cond=($db_type==2)? " TO_CHAR(a.production_hour,'HH24:MI') as production_hour " : " TIME_FORMAT( production_hour, '%H:%i' ) as production_hour ";
		if($db_type == 0){

			$sql = "select a.production_date,a.prod_reso_allo,a.po_break_down_id,a.challan_no, a.serving_company,a.location,d.grouping ,f.style_ref_no,d.po_number,a.item_number_id,a.floor_id,b.color_type_id ,c.color_number_id,a.sewing_line,a.country_id,$date_cond,sum(b.production_qnty) as production_qnty,c.size_number_id
			from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c, wo_po_break_down d,wo_po_details_master f
			where a.id=b.mst_id and b.color_size_break_down_id=c.id and c.po_break_down_id=d.id and d.id=a.po_break_down_id and d.job_no_mst=f.job_no and a.production_type=5 and a.entry_break_down_type=3 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and c.size_number_id is not null and length(c.color_number_id)>0 and a.id=$txt_mst_id
			group by a.production_date,a.prod_reso_allo,a.po_break_down_id,a.challan_no, a.serving_company,a.location,f.style_ref_no,d.po_number,a.item_number_id,a.floor_id,b.color_type_id ,c.color_number_id,a.sewing_line,a.country_id,production_hour,c.size_number_id,d.grouping";
		}else if($db_type == 2){
			$sql = "select a.production_date,a.prod_reso_allo,a.po_break_down_id,a.challan_no, a.serving_company,a.location,d.grouping ,f.style_ref_no,d.po_number,a.item_number_id,a.floor_id,b.color_type_id ,c.color_number_id,a.sewing_line,a.country_id,$date_cond,sum(b.production_qnty) as production_qnty,c.size_number_id
			from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c, wo_po_break_down d,wo_po_details_master f
			where a.id=b.mst_id and b.color_size_break_down_id=c.id and c.po_break_down_id=d.id and d.id=a.po_break_down_id and d.job_no_mst=f.job_no and a.production_type=5 and a.entry_break_down_type=3 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and c.size_number_id is not null and c.color_number_id is not null  and a.id=$txt_mst_id
			group by a.production_date,a.prod_reso_allo,a.po_break_down_id,a.challan_no, a.serving_company,a.location,f.style_ref_no,d.po_number,a.item_number_id,a.floor_id,b.color_type_id ,c.color_number_id,a.sewing_line,a.country_id,production_hour,c.size_number_id,d.grouping";
		}
		$result=sql_select($sql);
		//print_r($sql);
		unset($sql);
		foreach ($result as $row) {
			$po_break_down_id=$row[csf('po_break_down_id')];
			$challan_no=$row[csf('challan_no')];
			$country_id=$row[csf('country_id')];
			$company_id=$row[csf('serving_company')];
			$location_id=$row[csf('location')];
			$floor_id=$row[csf('floor_id')];
			$size_id=$row[csf('size_number_id')];
			$color_type_id=$row[csf('color_type_id')];
			$color_id= $row[csf('color_number_id')];
			$item_id=$row[csf('item_number_id')];
			$sewing_line='';
			$line_id='';
			if($row[csf('prod_reso_allo')]==1)
			{
				$line_number=explode(",",$prod_reso_arr[$row[csf('sewing_line')]]);
				foreach($line_number as $val)
				{
					if($line_id==''){
						$line_id=$val;
					}
					else {
						$line_id.=",".$val;
					}

				}
			}
			else{
				$line_id=$row[csf('sewing_line')];
			}

			$production_date=$row[csf('production_date')];
			$production_hour=$row[csf('production_hour')];
			$s="select * from finish_barcode  where po_break_down_id=$po_break_down_id and color_id=$color_id and country_id=$country_id and size_id=$size_id and challan_no=$challan_no and company_id=$company_id and color_type_id=$color_type_id and floor_id=$floor_id and item_id=$item_id and line_id=$line_id and production_hour='$production_hour' and production_date='$production_date' and status_active=1 and is_deleted=0";
	      //  echo "<pre>".$s."</pre>";
	        $res=sql_select($s);
	        unset($s);
		    $count=count($res);
		    if(count($res)){
		    	$ok=false;
				if($db_type==0)
				{
					mysql_query("ROLLBACK");
					echo "112**".str_replace("'","",$hidden_po_break_down_id);
				}
				if($db_type==2 || $db_type==1 )
				{
					oci_rollback($con);
					echo "112**".str_replace("'","",$hidden_po_break_down_id);
				}
				disconnect($con);
				die;
			}
			unset($res);

		}

 		$rID = sql_delete("pro_garments_production_mst","updated_by*update_date*status_active*is_deleted","".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1",'id ',$txt_mst_id,1);
		$dtlsrID = sql_delete("pro_garments_production_dtls","status_active*is_deleted","0*1",'mst_id',$txt_mst_id,1);

 		if($db_type==0)
		{
			if($rID && $dtlsrID)
			{
				mysql_query("COMMIT");
				echo "2**".str_replace("'","",$hidden_po_break_down_id);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$hidden_po_break_down_id);
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($rID)
			{
				oci_commit($con);
				echo "2**".str_replace("'","",$hidden_po_break_down_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$hidden_po_break_down_id);
			}
		}
		disconnect($con);
		die;
	}
}

if($action=="sewing_output_print")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	$mst_id=implode(',',explode("_",$data[1]));
	//print_r ($mst_id);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name"  );
	$buyer_library=return_library_array( "select id, short_name from   lib_buyer", "id", "short_name"  );
	// $sewing_library=return_library_array( "select id, line_name from  lib_sewing_line", "id", "line_name"  );
	$country_arr=return_library_array( "select id, country_name from  lib_country", "id", "country_name"  );
	$line_data_variable=return_library_array("select id, line_number from prod_resource_mst", "id","line_number");

	$lineArr = return_library_array("select id,line_name from lib_sewing_line order by id","id","line_name");
	$prod_reso_line_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');

	$name_iso_Array=sql_select("select iso_no from lib_iso where company_id=$data[0] and status_active=1 and module_id=7 and menu_id=207");

	$job_array=array();
	$job_sql="SELECT a.job_no, a.buyer_name, a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b,pro_garments_production_mst c where a.id=b.job_id and b.id=c.po_break_down_id and c.id in($mst_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.is_deleted=0";
	$job_sql_result=sql_select($job_sql);
	foreach($job_sql_result as $row)
	{
		$job_array[$row[csf('id')]]['job_no']=$row[csf('job_no')];
		$job_array[$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
		$job_array[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
		$job_array[$row[csf('id')]]['po_number']=$row[csf('po_number')];
	}

	if($db_type==2)
	{
		$sql="SELECT id, company_id, challan_no, sewing_line, po_break_down_id, item_number_id,entry_break_down_type,break_down_type_rej, country_id, production_source, produced_by, serving_company, location, embel_name, embel_type, production_date, TO_CHAR(production_hour,'HH24:MI') as production_hour, production_quantity, production_type, remarks, floor_id, sewing_line, alter_qnty, reject_qnty, spot_qnty,prod_reso_allo from pro_garments_production_mst where production_type=5 and id in($mst_id) and status_active=1 and is_deleted=0 ";
	}
	else
	{
		$sql="SELECT id, company_id, challan_no, sewing_line, po_break_down_id, item_number_id, country_id, entry_break_down_type,break_down_type_rej,production_source, produced_by, serving_company, location, embel_name, embel_type, production_date, TIME_FORMAT( production_hour, '%H:%i' ) as production_hour, production_quantity, production_type, remarks, floor_id, sewing_line, alter_qnty, reject_qnty, spot_qnty,prod_reso_allo from pro_garments_production_mst where production_type=5 and id in($mst_id) and status_active=1 and is_deleted=0 ";
	}
	//echo $sql;
	$sql_color_type=sql_select("SELECT color_type_id from pro_garments_production_dtls where status_active=1 and is_deleted=0 and mst_id in($mst_id) and production_type=5");
	$color_type_id=$sql_color_type[0][csf("color_type_id")];
	$dataArray=sql_select($sql);
	$break_down_type_reject=$dataArray[0][csf('break_down_type_rej')];
	$entry_break_down_type=$dataArray[0][csf('entry_break_down_type')];
	//echo $entry_break_down_type.'='.$entry_break_down_type;

	$challanNo=""; $line_name=""; $production_hour=""; $qcQty=$alterQty=$spotQty=$rejectQty=$recQty=0;

	foreach($dataArray as $row)
	{
		if($challanNo=="") $challanNo=$row[csf('challan_no')]; else $challanNo.=','.$row[csf('challan_no')];
		if($production_hour=="") $production_hour=$row[csf('production_hour')]; else $production_hour.=','.$row[csf('production_hour')];

		if($row[csf('prod_reso_allo')]==1)
		{
			$line_resource_mst_arr=explode(",",$prod_reso_line_arr[$row[csf('sewing_line')]]);

			foreach($line_resource_mst_arr as $resource_id)
			{
				$line_name.=$lineArr[$resource_id].",";
			}
		}
		else
		{
			$line_name.=$lineArr[$row[csf('sewing_line')]].",";
		}
		$qcQty+=$row[csf('production_quantity')];
		$alterQty+=$row[csf('alter_qnty')];
		$spotQty+=$row[csf('spot_qnty')];
		if($row[csf('entry_break_down_type')]==1) $recQty+=$row[csf('production_quantity')];
		if($row[csf('break_down_type_rej')]==1) $rejectQty+=$row[csf('reject_qnty')];
	}
?>
<div style="width:930px;">
    <table width="900" cellspacing="0" align="left">
        <tr>
            <td colspan="6" align="center" style="font-size:22px"><strong><? echo $company_library[$data[0]]; ?></strong></td>
			<td width='200'><b><?="ISO Number  :".$name_iso_Array[0]["ISO_NO"]?></b> </td>
        </tr>
        <tr class="form_caption">
        	<td colspan="6" align="center" style="font-size:12px">
				<?
					$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
					foreach ($nameArray as $result)
					{
						?>
						Plot No: <? echo $result[csf('plot_no')]; ?>
						Level No: <? echo $result[csf('level_no')]?>
						Road No: <? echo $result[csf('road_no')]; ?>
						Block No: <? echo $result[csf('block_no')];?>
						City No: <? echo $result[csf('city')];?>
						Zip Code: <? echo $result[csf('zip_code')]; ?>
						Province No: <?php echo $result[csf('province')];?>
						Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br>
						Email Address: <? echo $result[csf('email')];?>
						Website No: <? echo $result[csf('website')];
					}
                ?>
            </td>
        </tr>
        <tr>
            <td colspan="6" align="center" style="font-size:16px"><strong><? echo $data[2]; ?> Challan</strong></td>
        </tr>
        <tr>
			<?
                $supp_add=$dataArray[0][csf('serving_company')];
                $nameArray=sql_select( "select address_1,web_site,email,country_id from lib_supplier where id=$supp_add");
                foreach ($nameArray as $result)
                {
                    $address="";
                    if($result!="") $address=$result[csf('address_1')];//.'<br>'.$country_arr[$result['country_id']].'<br>'.$result['email'].'<br>'.$result['web_site']
                }
            ?>
        	<td width="270" rowspan="4" valign="top" colspan="2"><strong>Issue To : <? if($dataArray[0][csf('production_source')]==1) echo $company_library[$dataArray[0][csf('serving_company')]]; else if($dataArray[0][csf('production_source')]==3) echo $supplier_library[$dataArray[0][csf('serving_company')]].'<br>'.$address;  ?></strong></td>
            <td width="125"><strong>Order No :</strong></td><td width="175px"><? echo $job_array[$dataArray[0][csf('po_break_down_id')]]['po_number']; ?></td>
            <td width="125"><strong>Buyer:</strong></td><td width="175px"><? echo $buyer_library[$job_array[$dataArray[0][csf('po_break_down_id')]]['buyer_name']]; ?></td>
        </tr>
        <tr>
            <td><strong>Job No :</strong></td><td><? echo $job_array[$dataArray[0][csf('po_break_down_id')]]['job_no']; ?></td>
            <td><strong>Style Ref.:</strong></td><td><? echo $job_array[$dataArray[0][csf('po_break_down_id')]]['style_ref_no']; ?></td>
        </tr>
        <tr>
        	<td><strong>Item:</strong></td> <td><? echo $garments_item[$dataArray[0][csf('item_number_id')]]; ?></td>
            <td><strong>QC Pass Qty:</strong></td><td><? echo $qcQty; ?></td>
        </tr>
        <tr>
            <td><strong>Source:</strong></td><td><? echo $knitting_source[$dataArray[0][csf('production_source')]]; ?></td>
            <td><strong>Input Date:</strong></td><td><? echo change_date_format($dataArray[0][csf('production_date')]); ?></td>
        </tr>
        <tr>
            <td><strong>Sewing Line: </strong></td><td style="word-break:break-all"><? echo chop($line_name,","); ?></td>
            <td><strong>Reporting Hour:</strong></td><td style="word-break:break-all"><? echo $production_hour; ?></td>
            <td><strong>Challan No:</strong></td><td style="word-break:break-all"><? echo implode(",",array_filter(array_unique(explode(",",$challanNo)))); ?></td>
        </tr>
        <tr>
            <td><strong>Alter Qty: </strong></td><td><? echo $alterQty; ?></td>
            <td><strong>Spot Qty:</strong></td><td><? echo $spotQty; ?></td>
            <td><strong>Color Type:</strong></td>
        	<td><? echo $color_type[$color_type_id]; ?></td>
        </tr>
        <tr>
        	<td><strong>System Challan: </strong></td><td style="word-break:break-all"><?=$mst_id; ?></td>
        	<td><strong>Produced By: </strong></td><td><? echo $worker_type[$dataArray[0][csf('produced_by')]]; ?></td>
        </tr>
        <tr>
            <td colspan="6"><strong>Remarks:</strong> <? echo $dataArray[0][csf('remarks')]; ?></td>
        </tr>
        <tr>
         <?
		if($entry_break_down_type==1)
		{
		?>

        	<td colspan="3" ><strong>Receive Qnty :  <? echo $recQty; ?></strong></td>
       <?
		}
		 if($break_down_type_reject==1)
		{
  ?>
            <td colspan="3" ><strong>Reject Qnty:  <? echo $rejectQty; ?></strong></td>
       <? }
	 ?>
        </tr>
    </table>
    <br>
        <?
		if($entry_break_down_type!=1)
		{
			$po_break_id=$dataArray[0][csf('po_break_down_id')];
			$sql="SELECT sum(a.production_qnty) as production_qnty, b.color_number_id, b.size_number_id from pro_garments_production_dtls a, wo_po_color_size_breakdown b where a.mst_id in($mst_id) and b.po_break_down_id='$po_break_id' and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by  b.size_number_id, b.color_number_id ";
			//echo $sql;
			$result=sql_select($sql);
			$size_array=array ();
			$qun_array=array ();
			foreach ( $result as $row )
			{
				$size_array[$row[csf('size_number_id')]]=$row[csf('size_number_id')];
				$qun_array[$row[csf('color_number_id')]][$row[csf('size_number_id')]]=$row[csf('production_qnty')];
			}

			$sql="SELECT sum(a.production_qnty) as production_qnty, b.color_number_id from pro_garments_production_dtls a, wo_po_color_size_breakdown b where a.mst_id in($mst_id) and b.po_break_down_id='$po_break_id' and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.color_number_id ";
			//echo $sql; and a.production_date='$production_date'
			$result=sql_select($sql);
			$color_array=array ();
			foreach ( $result as $row )
			{
				$color_array[$row[csf('color_number_id')]]=$row[csf('color_number_id')];
			}

			$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
			$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
		?>
         	<div style="width:100%;">
            <div style="margin-left:30px;"><strong> Goods Qty.</strong></div>
    <table align="left" cellspacing="0" width="900"  border="1" rules="all" class="rpt_table" >
        <thead bgcolor="#dddddd" align="center">
            <th width="30">SL</th>
            <th width="80" align="center">Color/Size</th>
				<?
                foreach ($size_array as $sizid)
                {
					//$size_count=count($sizid);
                    ?>
                        <th width="150"><strong><? echo  $sizearr[$sizid];  ?></strong></th>
                    <?
                }
                ?>
            <th width="80" align="center">Total Issue Qnty.</th>
        </thead>
        <tbody>
			<?
            //$mrr_no=$dataArray[0][csf('issue_number')];
            $i=1;
            $tot_qnty=array();
                foreach($color_array as $cid)
                {
                    if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$color_count=count($cid);
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td><? echo $i;  ?></td>
                        <td><? echo $colorarr[$cid]; ?></td>
                        <?
                        foreach ($size_array as $sizval)
                        {
							$size_count=count($sizval);
                            ?>
                            <td align="right"><? echo $qun_array[$cid][$sizval]; ?></td>
                            <?
                            $tot_qnty[$cid]+=$qun_array[$cid][$sizval];
							$tot_qnty_size[$sizval]+=$qun_array[$cid][$sizval];
                        }
                        ?>
                        <td align="right"><? echo $tot_qnty[$cid]; ?></td>
                    </tr>
                    <?
					$production_quantity+=$tot_qnty[$cid];
					$i++;
                }
            ?>
        </tbody>
        <tr>
            <td colspan="2" align="right"><strong>Grand Total :</strong></td>
            <?
				foreach ($size_array as $sizval)
				{
					?>
                    <td align="right"><?php echo $tot_qnty_size[$sizval]; ?></td>
                    <?
				}
			?>
            <td align="right"><?php echo $production_quantity; ?></td>
        </tr>
    </table>
        <br>
		 <?
		}
		if($break_down_type_reject!=1)
		{
		$po_break_id=$dataArray[0][csf('po_break_down_id')];
		$sql="SELECT sum(a.production_qnty) as production_qnty,sum(reject_qty) as reject_qty ,	 b.color_number_id, b.size_number_id from pro_garments_production_dtls a, wo_po_color_size_breakdown b where a.mst_id in($mst_id) and b.po_break_down_id='$po_break_id' and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by  b.size_number_id, b.color_number_id ";
		//echo $sql;
		$result=sql_select($sql);
		$size_array=array ();
		$qun_array=array ();$reject_qun_array=array();
		$color_array=array ();
		foreach ( $result as $row )
		{
			$size_array[$row[csf('size_number_id')]]=$row[csf('size_number_id')];
			//$qun_array[$row[csf('color_number_id')]][$row[csf('size_number_id')]]=$row[csf('production_qnty')];
			$reject_qun_array[$row[csf('color_number_id')]][$row[csf('size_number_id')]]=$row[csf('reject_qty')];
			$color_array[$row[csf('color_number_id')]]=$row[csf('color_number_id')];
		}

		/* $sql="SELECT sum(a.production_qnty) as production_qnty, b.color_number_id from pro_garments_production_dtls a, wo_po_color_size_breakdown b where a.mst_id in($mst_id) and b.po_break_down_id='$po_break_id' and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.color_number_id ";
		//echo $sql; and a.production_date='$production_date'
		$result=sql_select($sql);
		$color_array=array ();
		foreach ( $result as $row )
		{
			$color_array[$row[csf('color_number_id')]]=$row[csf('color_number_id')];
		} */

			$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
			$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
		?>
         	<div style="width:100%;">
             <div style="margin-left:30px;"><strong> Reject Qty.</strong></div>
    <table align="left" cellspacing="0" width="900"  border="1" rules="all" class="rpt_table" >
        <thead bgcolor="#dddddd" align="center">
            <th width="30">SL</th>
            <th width="80" align="center">Color/Size</th>
				<?
                foreach ($size_array as $sizid)
                {
					//$size_count=count($sizid);
                    ?>
                        <th width="150"><strong><? echo  $sizearr[$sizid];  ?></strong></th>
                    <?
                }
                ?>
            <th width="80" align="center">Total Issue Qnty.</th>
        </thead>
        <tbody>
			<?
            //$mrr_no=$dataArray[0][csf('issue_number')];
            $i=1;
            $tot_qnty=array();
                foreach($color_array as $cid)
                {
                    if ($i%2==0)
                        $bgcolor="#E9F3FF";
                    else
                        $bgcolor="#FFFFFF";
					$color_count=count($cid);
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td><? echo $i;  ?></td>
                        <td><? echo $colorarr[$cid]; ?></td>
                        <?
                        foreach ($size_array as $sizval)
                        {
							$size_count=count($sizval);
                            ?>
                            <td align="right"><? echo $reject_qun_array[$cid][$sizval]; ?></td>
                            <?
                            $reject_tot_qnty[$cid]+=$reject_qun_array[$cid][$sizval];
							$reject_tot_qnty_size[$sizval]+=$reject_qun_array[$cid][$sizval];
                        }
                        ?>
                        <td align="right"><? echo $reject_tot_qnty[$cid]; ?></td>
                    </tr>
                    <?
					$reject_production_quantity+=$reject_tot_qnty[$cid];
					$i++;
                }
            ?>
        </tbody>
        <tr>
            <td colspan="2" align="right"><strong>Grand Total :</strong></td>
            <?
				foreach ($size_array as $sizval)
				{
					?>
                    <td align="right"><?php echo $reject_tot_qnty_size[$sizval]; ?></td>
                    <?
				}
			?>
            <td align="right"><?php echo $reject_production_quantity; ?></td>
        </tr>
    </table>
       <?
		}
            echo signature_table(29, $data[0], "900px",'',0);
         ?>
	</div>
	</div>
	<?
    exit();
}

if ($action=="piece_rate_order_cheack")
{
	$ex_data=explode('**',$data);
	if($db_type==0)
	{
		$piece_sql="SELECT a.sys_number, sum(b.wo_qty) as wo_qty from piece_rate_wo_mst a, piece_rate_wo_dtls b where a.id=b.mst_id and b.order_id=$ex_data[0] and b.item_id=$ex_data[1] and a.rate_for=30 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.sys_number";
	}
	else if($db_type==2)
	{
		$piece_sql="SELECT a.sys_number, sum(b.wo_qty) as wo_qty from piece_rate_wo_mst a, piece_rate_wo_dtls b where a.id=b.mst_id and b.order_id=$ex_data[0] and b.item_id=$ex_data[1] and a.rate_for=30 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.sys_number";
	}
	//echo $piece_sql;
	$data_array=sql_select($piece_sql,0);
	if(count($data_array)>0)
	{
		$sys_number=""; $wo_qty=0;
		foreach($data_array as $row)
		{
			if ($sys_number=="") $sys_number=$row[csf('sys_number')]; else $sys_number.=','.$row[csf('sys_number')];
			$wo_qty+=$row[csf('wo_qty')];
		}
		echo "1"."_".$sys_number."_".$wo_qty;
	}
	else
	{
		echo "0_";
	}
	exit();
}

if($action=="defect_data")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
	$caption_name="";

	if($type==1) $caption_name="Alter Qty";
	else if($type==2) $caption_name="Spot Qty";
	else if($type==3) $caption_name="Reject Qty";
	else if($type==4) $caption_name="Front Check Qty";
	else if($type==5) $caption_name="Back Part Check Qty";
	else if($type==6) $caption_name="WestBand Check Qty";
	else if($type==7) $caption_name="Measurement Check Qty";
	else if($type==8) $caption_name="Inside Check Qty";
	else if($type==9) $caption_name="Topside Check Qty";
	else if($type==10) $caption_name="Collar Qty";
	else if($type==11) $caption_name="Armhole Qty";
	else if($type==12) $caption_name="Makecheck Qty";




	?>
    <script>
		function fnc_close()
		{
			var save_string='';	var tot_defect_qnty='';
			var defect_id_array = new Array();
			$("#tbl_list_search").find('tbody tr').each(function()
			{
				var txtDefectId=$(this).find('input[name="txtDefectId[]"]').val();
				var txtDefectQnty=$(this).find('input[name="txtDefectQnty[]"]').val();
				var txtDefectUpdateId=$(this).find('input[name="txtDefectUpdateId[]"]').val();
				tot_defect_qnty=tot_defect_qnty*1+txtDefectQnty*1;
				//
				if(txtDefectQnty*1>0)
				{
					if(save_string=="")
					{
						save_string=txtDefectUpdateId+"**"+txtDefectId+"**"+txtDefectQnty;
					}
					else
					{
						save_string+=","+txtDefectUpdateId+"**"+txtDefectId+"**"+txtDefectQnty;
					}

					if( jQuery.inArray( txtDefectId, defect_id_array) == -1 )
					{
						defect_id_array.push(txtDefectId);
					}
				}
			});
			//alert (save_string);
			//var defect_type_id=
			$('#defect_type_id').val();
			$('#save_string').val( save_string );
			$('#tot_defectQnty').val( tot_defect_qnty );
			$('#all_defect_id').val( defect_id_array );
			parent.emailwindow.hide();
		}
		function calculate_reject()
		{
			var reject_qty=0;
			$("#tbl_list_search").find('tbody tr').each(function()
				{
					// alert(4);
				reject_qty+=$(this).find('input[name="txtDefectQnty[]"]').val()*1;
				// alert(reject_qty);
				});
			$("#reject_qty_td").text(reject_qty);
		}
		function calculate_rejecttype1()
		{
			var reject_qty=0;
			$("#tbl_list_search").find('tbody tr').each(function()
				{
				reject_qty+=$(this).find('input[name="txtDefectQnty[]"]').val()*1;
				// alert(reject_qty);
				});
			$("#reject_qty_td").text(reject_qty);
		}
		function calculate_rejecttype2()
		{
			var reject_qty=0;
			$("#tbl_list_search").find('tbody tr').each(function()
				{
				reject_qty+=$(this).find('input[name="txtDefectQnty[]"]').val()*1;
				// alert(reject_qty);
				});
			$("#reject_qty_td").text(reject_qty);
		}
		function calculate_rejecttype3()
		{
			var reject_qty=0;
			$("#tbl_list_search").find('tbody tr').each(function()
				{
				reject_qty+=$(this).find('input[name="txtDefectQnty[]"]').val()*1;
				// alert(reject_qty);
				});
			$("#reject_qty_td").text(reject_qty);
		}
		function calculate_rejecttype5()
		{
			var reject_qty=0;
			$("#tbl_list_search").find('tbody tr').each(function()
				{
				reject_qty+=$(this).find('input[name="txtDefectQnty[]"]').val()*1;
				// alert(reject_qty);
				});
			$("#reject_qty_td").text(reject_qty);
		}
		function calculate_rejecttype6()
		{
			var reject_qty=0;
			$("#tbl_list_search").find('tbody tr').each(function()
				{
				reject_qty+=$(this).find('input[name="txtDefectQnty[]"]').val()*1;
				// alert(reject_qty);
				});
			$("#reject_qty_td").text(reject_qty);
		}
		function calculate_rejecttype7()
		{
			var reject_qty=0;
			$("#tbl_list_search").find('tbody tr').each(function()
				{
				reject_qty+=$(this).find('input[name="txtDefectQnty[]"]').val()*1;
				// alert(reject_qty);
				});
			$("#reject_qty_td").text(reject_qty);
		}
	</script>
    </head>
    <body>
        <div align="center" style="width:100%;" >
        <form name="defect_1"  id="defect_1" autocomplete="off">
			<? //echo load_freeze_divs ("../../",$permission,1); ?>
            <fieldset style="width:350px;">
                <input type="hidden" name="save_string" id="save_string" class="text_boxes" value="<? echo $save_data; ?>">
                <input type="hidden" name="tot_defectQnty" id="tot_defectQnty" class="text_boxes" value="<? echo $defect_qty; ?>">
                <input type="hidden" name="all_defect_id" id="all_defect_id" class="text_boxes" value="<? echo $all_defect_id; ?>">
                <input type="hidden" name="defect_type_id" id="defect_type_id" class="text_boxes" value="<? echo $type; ?>">
             <?
				if($type==3){
               ?>
                   <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="340">
            	<thead>
                	<tr><th colspan="3"><? echo $caption_name; ?></th></tr>
                	<tr><th width="40">SL</th><th width="150">Reject Name</th><th>Reject Qty</th></tr>
                </thead>
            </table>
             <?
				}
				else{

					?>
			<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="340">
			<thead>
				<tr><th colspan="3"><? echo $caption_name; ?></th></tr>
				<tr><th width="40">SL</th><th width="150">Defect Name</th><th>Defect Qty</th></tr>
			</thead>
		</table>
        <?
				}
             ?>


            <div style="width:340px; max-height:300px; overflow-y:scroll" id="list_container" align="left">
                <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="320" id="tbl_list_search">
				 <tbody>
                    <?
					if($type==1) // Alter
					{
						if($save_string=="") //$save_data=$prevQnty;
						$explSaveData = explode(",",$save_data);

						$defect_dataArray=array();
						foreach($explSaveData as $val)
						{
							$difectVal = explode("**",$val);
							$defect_dataArray[$difectVal[1]]['up_id']=$difectVal[0];
							$defect_dataArray[$difectVal[1]]['defectid']=$difectVal[1];
							$defect_dataArray[$difectVal[1]]['defectQnty']=$difectVal[2];
						}
                        $i=1;
						$total_reject=0;
                        foreach($sew_fin_alter_defect_type as $id=>$val)
                        {
                            if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$total_reject+=$defect_dataArray[$id]['defectQnty'];
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
                                <td width="40"><? echo $i; ?></td>
                                <td width="150"><? echo $val; ?></td>
                                <td align="center">
                                    <input type="text" name="txtDefectQnty[]" id="txtDefectQnty_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" value="<? echo $defect_dataArray[$id]['defectQnty']; ?>" onKeyUp="calculate_rejecttype1()">
                                    <input type="hidden" name="txtDefectId[]" id="txtDefectId_<? echo $i; ?>" style="width:40px" value="<? echo $id; ?>">
                                    <input type="hidden" name="txtDefectUpdateId[]" id="txtDefectUpdateId_<? echo $i; ?>" style="width:40px" value="<? echo $defect_dataArray[$id]['up_id']; ?>">
                                </td>
                            </tr>
                            <?
                            $i++;
                        }
					}
					else if($type==2) // Spot
					{
						if($save_string=="") //$save_data=$prevQnty;
						$explSaveData = explode(",",$save_data);

						$defect_dataArray=array();
						foreach($explSaveData as $val)
						{
							$difectVal = explode("**",$val);
							$defect_dataArray[$difectVal[1]]['up_id']=$difectVal[0];
							$defect_dataArray[$difectVal[1]]['defectid']=$difectVal[1];
							$defect_dataArray[$difectVal[1]]['defectQnty']=$difectVal[2];
						}
                        $i=1;
						$total_reject=0;
                        foreach($sew_fin_spot_defect_type as $id=>$val)
                        {
                            if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$total_reject+=$defect_dataArray[$id]['defectQnty'];
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
                                <td width="40"><? echo $i; ?></td>
                                <td width="150"><? echo $val; ?></td>
                                <td align="center">
                                    <input type="text" name="txtDefectQnty[]" id="txtDefectQnty_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" value="<? echo $defect_dataArray[$id]['defectQnty']; ?>" onKeyUp="calculate_rejecttype2()">
                                    <input type="hidden" name="txtDefectId[]" id="txtDefectId_<? echo $i; ?>" style="width:40px" value="<? echo $id; ?>">
                                    <input type="hidden" name="txtDefectUpdateId[]" id="txtDefectUpdateId_<? echo $i; ?>" style="width:40px" value="<? echo $defect_dataArray[$id]['up_id']; ?>">
                                </td>
                            </tr>
                            <?
                            $i++;
                        }
					}
					else if($type==3) // Reject
					{

						if($save_string=="") //$save_data=$prevQnty;
						$explSaveData = explode(",",$save_data);

						$defect_dataArray=array();
						foreach($explSaveData as $val)
						{
							$difectVal = explode("**",$val);
							$defect_dataArray[$difectVal[1]]['up_id']=$difectVal[0];
							$defect_dataArray[$difectVal[1]]['defectid']=$difectVal[1];
							$defect_dataArray[$difectVal[1]]['defectQnty']=$difectVal[2];
						}
                        $i=1;
						$total_reject=0;
                        foreach($sew_fin_reject_type_for_arr as $id=>$val)
                        {
                            if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$total_reject+=$defect_dataArray[$id]['defectQnty'];
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
                                <td width="40"><? echo $i; ?></td>
                                <td width="150"><? echo $val; ?></td>
                                <td align="center">
                                    <input type="text" name="txtDefectQnty[]" id="txtDefectQnty_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" value="<? echo $defect_dataArray[$id]['defectQnty']; ?>" onKeyUp="calculate_rejecttype3()">
                                    <input type="hidden" name="txtDefectId[]" id="txtDefectId_<? echo $i; ?>" style="width:40px" value="<? echo $id; ?>">
                                    <input type="hidden" name="txtDefectUpdateId[]" id="txtDefectUpdateId_<? echo $i; ?>" style="width:40px" value="<? echo $defect_dataArray[$id]['up_id']; ?>">
                                </td>
                            </tr>
                            <?
                            $i++;
                        }

					}
					else if($type==4)//Front
					{
						if($save_string=="") //$save_data=$prevQnty;
						$explSaveData = explode(",",$save_data);

						$defect_dataArray=array();
						foreach($explSaveData as $val)
						{
							$difectVal = explode("**",$val);
							$defect_dataArray[$difectVal[1]]['up_id']=$difectVal[0];
							$defect_dataArray[$difectVal[1]]['defectid']=$difectVal[1];
							$defect_dataArray[$difectVal[1]]['defectQnty']=$difectVal[2];
						}
                        $i=1;
						$total_reject=0;
                        foreach($sew_fin_woven_defect_array as $id=>$val)
                        {
                            if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$total_reject+=$defect_dataArray[$id]['defectQnty'];
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
                                <td width="40"><? echo $i; ?></td>
                                <td width="150"><? echo $val; ?></td>
                                <td align="center">
                                    <input type="text" name="txtDefectQnty[]" id="txtDefectQnty_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" value="<? echo $defect_dataArray[$id]['defectQnty']; ?>" onKeyUp="calculate_reject()">
                                    <input type="hidden" name="txtDefectId[]" id="txtDefectId_<? echo $i; ?>" style="width:40px" value="<? echo $id; ?>">
                                    <input type="hidden" name="txtDefectUpdateId[]" id="txtDefectUpdateId_<? echo $i; ?>" style="width:40px" value="<? echo $defect_dataArray[$id]['up_id']; ?>">
                                </td>
                            </tr>
                            <?
                            $i++;
                        }
					}
					else if($type==8)//Inside Part
					{
						if($save_string=="") //$save_data=$prevQnty;
						$explSaveData = explode(",",$save_data);

						$defect_dataArray=array();
						foreach($explSaveData as $val)
						{
							$difectVal = explode("**",$val);
							$defect_dataArray[$difectVal[1]]['up_id']=$difectVal[0];
							$defect_dataArray[$difectVal[1]]['defectid']=$difectVal[1];
							$defect_dataArray[$difectVal[1]]['defectQnty']=$difectVal[2];
						}
                        $i=1;
						$total_reject=0;
                        foreach($sew_fin_woven_defect_array as $id=>$val)
                        {
                            if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$total_reject+=$defect_dataArray[$id]['defectQnty'];
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
                                <td width="40"><? echo $i; ?></td>
                                <td width="150"><? echo $val; ?></td>
                                <td align="center">
                                    <input type="text" name="txtDefectQnty[]" id="txtDefectQnty_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" value="<? echo $defect_dataArray[$id]['defectQnty']; ?>" onKeyUp="calculate_reject()">
                                    <input type="hidden" name="txtDefectId[]" id="txtDefectId_<? echo $i; ?>" style="width:40px" value="<? echo $id; ?>">
                                    <input type="hidden" name="txtDefectUpdateId[]" id="txtDefectUpdateId_<? echo $i; ?>" style="width:40px" value="<? echo $defect_dataArray[$id]['up_id']; ?>">
                                </td>
                            </tr>
                            <?
                            $i++;
                        }
					}
					else if($type==9)//Topside Part
					{
						if($save_string=="") //$save_data=$prevQnty;
						$explSaveData = explode(",",$save_data);

						$defect_dataArray=array();
						foreach($explSaveData as $val)
						{
							$difectVal = explode("**",$val);
							$defect_dataArray[$difectVal[1]]['up_id']=$difectVal[0];
							$defect_dataArray[$difectVal[1]]['defectid']=$difectVal[1];
							$defect_dataArray[$difectVal[1]]['defectQnty']=$difectVal[2];
						}
                        $i=1;
						$total_reject=0;
                        foreach($sew_fin_woven_defect_array as $id=>$val)
                        {
                            if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$total_reject+=$defect_dataArray[$id]['defectQnty'];
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
                                <td width="40"><? echo $i; ?></td>
                                <td width="150"><? echo $val; ?></td>
                                <td align="center">
                                    <input type="text" name="txtDefectQnty[]" id="txtDefectQnty_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" value="<? echo $defect_dataArray[$id]['defectQnty']; ?>" onKeyUp="calculate_reject()">
                                    <input type="hidden" name="txtDefectId[]" id="txtDefectId_<? echo $i; ?>" style="width:40px" value="<? echo $id; ?>">
                                    <input type="hidden" name="txtDefectUpdateId[]" id="txtDefectUpdateId_<? echo $i; ?>" style="width:40px" value="<? echo $defect_dataArray[$id]['up_id']; ?>">
                                </td>
                            </tr>
                            <?
                            $i++;
                        }
					}
					else if($type==10)//Collar Part
					{
						if($save_string=="") //$save_data=$prevQnty;
						$explSaveData = explode(",",$save_data);

						$defect_dataArray=array();
						foreach($explSaveData as $val)
						{
							$difectVal = explode("**",$val);
							$defect_dataArray[$difectVal[1]]['up_id']=$difectVal[0];
							$defect_dataArray[$difectVal[1]]['defectid']=$difectVal[1];
							$defect_dataArray[$difectVal[1]]['defectQnty']=$difectVal[2];
						}
                        $i=1;
						$total_reject=0;
                        foreach($sew_fin_woven_defect_array as $id=>$val)
                        {
                            if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$total_reject+=$defect_dataArray[$id]['defectQnty'];
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
                                <td width="40"><? echo $i; ?></td>
                                <td width="150"><? echo $val; ?></td>
                                <td align="center">
                                    <input type="text" name="txtDefectQnty[]" id="txtDefectQnty_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" value="<? echo $defect_dataArray[$id]['defectQnty']; ?>" onKeyUp="calculate_reject()">
                                    <input type="hidden" name="txtDefectId[]" id="txtDefectId_<? echo $i; ?>" style="width:40px" value="<? echo $id; ?>">
                                    <input type="hidden" name="txtDefectUpdateId[]" id="txtDefectUpdateId_<? echo $i; ?>" style="width:40px" value="<? echo $defect_dataArray[$id]['up_id']; ?>">
                                </td>
                            </tr>
                            <?
                            $i++;
                        }
					}
					else if($type==11)//Armhole Part
					{
						if($save_string=="") //$save_data=$prevQnty;
						$explSaveData = explode(",",$save_data);

						$defect_dataArray=array();
						foreach($explSaveData as $val)
						{
							$difectVal = explode("**",$val);
							$defect_dataArray[$difectVal[1]]['up_id']=$difectVal[0];
							$defect_dataArray[$difectVal[1]]['defectid']=$difectVal[1];
							$defect_dataArray[$difectVal[1]]['defectQnty']=$difectVal[2];
						}
                        $i=1;
						$total_reject=0;
                        foreach($sew_fin_woven_defect_array as $id=>$val)
                        {
                            if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$total_reject+=$defect_dataArray[$id]['defectQnty'];
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
                                <td width="40"><? echo $i; ?></td>
                                <td width="150"><? echo $val; ?></td>
                                <td align="center">
                                    <input type="text" name="txtDefectQnty[]" id="txtDefectQnty_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" value="<? echo $defect_dataArray[$id]['defectQnty']; ?>" onKeyUp="calculate_reject()">
                                    <input type="hidden" name="txtDefectId[]" id="txtDefectId_<? echo $i; ?>" style="width:40px" value="<? echo $id; ?>">
                                    <input type="hidden" name="txtDefectUpdateId[]" id="txtDefectUpdateId_<? echo $i; ?>" style="width:40px" value="<? echo $defect_dataArray[$id]['up_id']; ?>">
                                </td>
                            </tr>
                            <?
                            $i++;
                        }
					}
					else if($type==12)//Makecheck Part
					{
						if($save_string=="") //$save_data=$prevQnty;
						$explSaveData = explode(",",$save_data);

						$defect_dataArray=array();
						foreach($explSaveData as $val)
						{
							$difectVal = explode("**",$val);
							$defect_dataArray[$difectVal[1]]['up_id']=$difectVal[0];
							$defect_dataArray[$difectVal[1]]['defectid']=$difectVal[1];
							$defect_dataArray[$difectVal[1]]['defectQnty']=$difectVal[2];
						}
                        $i=1;
						$total_reject=0;
                        foreach($sew_fin_woven_defect_array as $id=>$val)
                        {
                            if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$total_reject+=$defect_dataArray[$id]['defectQnty'];
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
                                <td width="40"><? echo $i; ?></td>
                                <td width="150"><? echo $val; ?></td>
                                <td align="center">
                                    <input type="text" name="txtDefectQnty[]" id="txtDefectQnty_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" value="<? echo $defect_dataArray[$id]['defectQnty']; ?>" onKeyUp="calculate_reject()">
                                    <input type="hidden" name="txtDefectId[]" id="txtDefectId_<? echo $i; ?>" style="width:40px" value="<? echo $id; ?>">
                                    <input type="hidden" name="txtDefectUpdateId[]" id="txtDefectUpdateId_<? echo $i; ?>" style="width:40px" value="<? echo $defect_dataArray[$id]['up_id']; ?>">
                                </td>
                            </tr>
                            <?
                            $i++;
                        }
					}
					else if($type==5)//Back part
					{
						if($save_string=="") //$save_data=$prevQnty;
						$explSaveData = explode(",",$save_data);

						$defect_dataArray=array();
						foreach($explSaveData as $val)
						{
							$difectVal = explode("**",$val);
							$defect_dataArray[$difectVal[1]]['up_id']=$difectVal[0];
							$defect_dataArray[$difectVal[1]]['defectid']=$difectVal[1];
							$defect_dataArray[$difectVal[1]]['defectQnty']=$difectVal[2];
						}
                        $i=1;
						$total_reject=0;
                        foreach($sew_fin_woven_defect_array as $id=>$val)
                        {
                            if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$total_reject+=$defect_dataArray[$id]['defectQnty'];
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
                                <td width="40"><? echo $i; ?></td>
                                <td width="150"><? echo $val; ?></td>
                                <td align="center">
                                    <input type="text" name="txtDefectQnty[]" id="txtDefectQnty_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" value="<? echo $defect_dataArray[$id]['defectQnty']; ?>" onKeyUp="calculate_rejecttype5()">
                                    <input type="hidden" name="txtDefectId[]" id="txtDefectId_<? echo $i; ?>" style="width:40px" value="<? echo $id; ?>">
                                    <input type="hidden" name="txtDefectUpdateId[]" id="txtDefectUpdateId_<? echo $i; ?>" style="width:40px" value="<? echo $defect_dataArray[$id]['up_id']; ?>">
                                </td>
                            </tr>
                            <?
                            $i++;
                        }
					}
					else if($type==6) //West part
					{
						if($save_string=="") //$save_data=$prevQnty;
						$explSaveData = explode(",",$save_data);

						$defect_dataArray=array();
						foreach($explSaveData as $val)
						{
							$difectVal = explode("**",$val);
							$defect_dataArray[$difectVal[1]]['up_id']=$difectVal[0];
							$defect_dataArray[$difectVal[1]]['defectid']=$difectVal[1];
							$defect_dataArray[$difectVal[1]]['defectQnty']=$difectVal[2];
						}
                        $i=1;
						$total_reject=0;
                        foreach($sew_fin_woven_defect_array as $id=>$val)
                        {
                            if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$total_reject+=$defect_dataArray[$id]['defectQnty'];
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
                                <td width="40"><? echo $i; ?></td>
                                <td width="150"><? echo $val; ?></td>
                                <td align="center">
                                    <input type="text" name="txtDefectQnty[]" id="txtDefectQnty_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" value="<? echo $defect_dataArray[$id]['defectQnty']; ?>" onKeyUp="calculate_rejecttype6()">
                                    <input type="hidden" name="txtDefectId[]" id="txtDefectId_<? echo $i; ?>" style="width:40px" value="<? echo $id; ?>">
                                    <input type="hidden" name="txtDefectUpdateId[]" id="txtDefectUpdateId_<? echo $i; ?>" style="width:40px" value="<? echo $defect_dataArray[$id]['up_id']; ?>">
                                </td>
                            </tr>
                            <?
                            $i++;
                        }
					}
					else if($type==7) //Measure
					{
						if($save_string=="") //$save_data=$prevQnty;
						$explSaveData = explode(",",$save_data);

						$defect_dataArray=array();
						foreach($explSaveData as $val)
						{
							$difectVal = explode("**",$val);
							$defect_dataArray[$difectVal[1]]['up_id']=$difectVal[0];
							$defect_dataArray[$difectVal[1]]['defectid']=$difectVal[1];
							$defect_dataArray[$difectVal[1]]['defectQnty']=$difectVal[2];
						}
                        $i=1;
						$total_reject=0;
                        foreach($sew_fin_measurment_check_array as $id=>$val)
                        {
                            if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$total_reject+=$defect_dataArray[$id]['defectQnty'];
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
                                <td width="40"><? echo $i; ?></td>
                                <td width="150"><? echo $val; ?></td>
                                <td align="center">
                                    <input type="text" name="txtDefectQnty[]" id="txtDefectQnty_<? echo $i;?>" class="text_boxes_numeric" style="width:80px" value="<? echo $defect_dataArray[$id]['defectQnty']; ?>" onKeyUp="calculate_rejecttype7()">
                                    <input type="hidden" name="txtDefectId[]" id="txtDefectId_<? echo $i; ?>" style="width:40px" value="<? echo $id; ?>">
                                    <input type="hidden" name="txtDefectUpdateId[]" id="txtDefectUpdateId_<? echo $i; ?>" style="width:40px" value="<? echo $defect_dataArray[$id]['up_id']; ?>">
                                </td>
                            </tr>
				 </tbody>
                            <?
                            $i++;
                        }
					}
                    ?>
				 <tfoot>
                        <tr class="tbl_bottom">
                            <td align="right" colspan="2">Total</td>

                            <td align="right"  id="reject_qty_td" style="padding-right:20px"> <? echo $total_reject; ?></td>
                        </tr>
                 </tfoot>
                </table>
            </div>
			<table width="320" id="table_id">
				 <tr>
					<td align="center" colspan="3">
						<input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
					</td>
				</tr>
			</table>
            </fieldset>
        </form>
        </div>
    </body>
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
  <?
  exit();
}


if ($action=="load_drop_down_color_type")
{

	$color_type_arr=array();
	$sql="SELECT b.color_type_id from  wo_po_break_down a,wo_pre_cost_fabric_cost_dtls b,wo_pre_cos_fab_co_avg_con_dtls c where   a.job_no_mst=b.job_no  and b.id=c.pre_cost_fabric_cost_dtls_id   and   a.status_active=1 and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and a.id='$data' and c.cons>0  group by b.color_type_id";

	foreach(sql_select($sql) as $key=>$vals)
	{
		$color_type_arr[$vals[csf("color_type_id")]]=$color_type[$vals[csf("color_type_id")]];
	}

	if(count(sql_select($sql))>1)
	{
		echo create_drop_down( "cbo_color_type", 110, $color_type_arr,"", 0, "Select Type", $selected,"");
	}
	else
	{
		echo create_drop_down( "cbo_color_type", 110, $color_type_arr,"", 0, "Select Type", $selected,"");
	}

	exit();
}



?>

<script type="text/javascript">
	function getActionOnEnter(event){
			if (event.keyCode == 13){
				document.getElementById('btn_show').click();
			}

	}
</script>
