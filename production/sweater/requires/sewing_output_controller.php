<?
session_start();
include('../../../includes/common.php');

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
    $company_credential_cond = " and comp.id in($company_id)";
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
$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name"  );
$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');

if ($action=="load_variable_settings")
{
	echo "setFieldLevelAccess($data);\n";
	echo "$('#sewing_production_variable').val(0);\n";
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
	}
	$variable_is_control=return_field_value("is_control","variable_settings_production","company_name=$data and variable_list=50 and page_category_id=5","is_control");
	echo "document.getElementById('variable_is_controll').value='".$variable_is_control."';\n";

	echo "$('#hidden_variable_cntl').val('0');\n";
	echo "$('#hidden_preceding_process').val('0');\n";
    $control_and_preceding=sql_select("select is_control,preceding_page_id from variable_settings_production where status_active=1 and is_deleted=0 and variable_list=50 and page_category_id=5 and company_name='$data'");
    if(count($control_and_preceding)>0)
    {
      echo "$('#hidden_variable_cntl').val('".$control_and_preceding[0][csf("is_control")]."');\n";
	  echo "$('#hidden_preceding_process').val('".$control_and_preceding[0][csf("preceding_page_id")]."');\n";
    }

 	exit();
}

if($action=="production_process_control")
{
	echo "$('#hidden_variable_cntl').val('0');\n";
	echo "$('#hidden_preceding_process').val('0');\n";
    $control_and_preceding=sql_select("select is_control,preceding_page_id from variable_settings_production where status_active=1 and is_deleted=0 and variable_list=50 and page_category_id=5 and company_name='$data'");
    if(count($control_and_preceding)>0)
    {
      echo "$('#hidden_variable_cntl').val('".$control_and_preceding[0][csf("is_control")]."');\n";
	  echo "$('#hidden_preceding_process').val('".$control_and_preceding[0][csf("preceding_page_id")]."');\n";
    }

	exit();
}


if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location", 167, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' $location_credential_cond  order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/sewing_output_controller', this.value, 'load_drop_down_floor', 'floor_td' );load_drop_down( 'requires/sewing_output_controller', document.getElementById('cbo_location').value+'_'+document.getElementById('prod_reso_allo').value+'_'+document.getElementById('txt_sewing_date').value+'_'+document.getElementById('cbo_sewing_company').value, 'load_drop_down_sewing_output_line', 'sewing_line_td' );get_php_form_data(document.getElementById('cbo_source').value,'line_disable_enable','requires/sewing_output_controller');" );
	exit();
}

if ($action=="load_drop_down_floor")
{
	echo create_drop_down( "cbo_floor", 170, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' and production_process in (5) order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "load_drop_down( 'requires/sewing_output_controller', this.value+'_'+document.getElementById('cbo_location').value+'_'+document.getElementById('prod_reso_allo').value+'_'+document.getElementById('txt_sewing_date').value+'_'+document.getElementById('cbo_sewing_company').value, 'load_drop_down_sewing_line_floor', 'sewing_line_td' );",0 );
	exit();
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "txt_search_common", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "");     	 
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
			echo create_drop_down( "cbo_sewing_company", 170, "select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0 and find_in_set(22,party_type) order by supplier_name","id,supplier_name", 1, "--- Select ---", $selected, "get_php_form_data(this.value+'**'+$data+'**'+$('#hidden_po_break_down_id').val()+'**'+$('#cbo_item_name').val()+'**'+$('#cbo_country_name').val(),'display_bl_qnty','requires/sewing_output_controller');fnc_workorder_search(this.value);",0,0 );
		}
		else
		{
			echo create_drop_down( "cbo_sewing_company", 170, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=22 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "--- Select ---", $selected, "get_php_form_data(this.value+'**'+$data+'**'+$('#hidden_po_break_down_id').val()+'**'+$('#cbo_item_name').val()+'**'+$('#cbo_country_name').val(),'display_bl_qnty','requires/sewing_output_controller');fnc_workorder_search(this.value);",0,0 );
		}
	}
	else if($data==1)
	{
 		echo create_drop_down( "cbo_sewing_company", 170, "select id,company_name from lib_company comp where is_deleted=0 and status_active=1 $company_cond $company_credential_cond order by company_name","id,company_name", 1, "--- Select ---", "",  "get_php_form_data(this.value+'**'+$data+'**'+$('#hidden_po_break_down_id').val()+'**'+$('#cbo_item_name').val()+'**'+$('#cbo_country_name').val(), 'display_bl_qnty', 'requires/sewing_output_controller');load_drop_down( 'requires/sewing_output_controller', this.value, 'load_drop_down_location', 'location_td' );",0,0 );

	}
 	else
	{
		echo create_drop_down( "cbo_sewing_company", 170, $blank_array,"", 1, "--- Select ---", $selected, "",0,0 );
	}

	exit();
}

if ($action=="load_drop_down_workorder")
{
	$explode_data = explode("_",$data);

	$sql = "select a.id,a.sys_number from piece_rate_wo_mst a, piece_rate_wo_dtls b where a.id=b.mst_id and b.order_id=".$explode_data[2]." and a.company_id=$explode_data[0]  and a.rate_for=30 and a.service_provider_id=$explode_data[1]   and a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 group by a.id,a.sys_number order by a.id";
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
	$sql = sql_select("select a.id,a.sys_number,a.currence,a.exchange_rate,sum(b.avg_rate) as rate,b.uom from piece_rate_wo_mst a, piece_rate_wo_dtls b where a.id=".$data[3]."  and a.id=b.mst_id and b.order_id=".$po_break_down_id." and a.company_id=$company_id and a.service_provider_id=$suppplier and a.rate_for=30   and a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 group by a.id,a.sys_number,a.currence ,a.exchange_rate,b.uom order by a.id");
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

	$dataArray=sql_select("select SUM(CASE WHEN a.production_type=4 and b.production_type=4 THEN b.production_qnty END) as totalinput,SUM(CASE WHEN a.production_type=5 and b.production_type=5 THEN b.production_qnty ELSE 0 END) as totalsewing from pro_garments_production_mst a,pro_garments_production_dtls b WHERE a.id= b.mst_id and a.po_break_down_id='$po_break_down_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.production_source='$source' and a.serving_company='$sewing_company' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	foreach($dataArray as $row)
	{
		echo "$('#txt_input_quantity').val('".$row['totalinput']."');\n";
		echo "$('#txt_cumul_sewing_qty').val('".$row['totalsewing']."');\n";
		$yet_to_produced = $row['totalinput']-$row['totalsewing'];
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
		echo create_drop_down( "cbo_sewing_line", 110,$line_array_new,"", 1, "--- Select ---", $selected, "",0,0 );
	}
	else
	{
		echo create_drop_down( "cbo_sewing_line", 110, "select id,line_name from lib_sewing_line where is_deleted=0 and status_active=1 and location_name='$location' and location_name!=0 order by line_name","id,line_name", 1, "Select Line", $selected, "" );
	}
	exit();
}
if($action=="load_drop_down_sewing_line_floor")
{
	$explode_data = explode("_",$data);
	$prod_reso_allocation = $explode_data[2];
	$txt_sewing_date = $explode_data[3];
	$wo_company_id = $explode_data[4];
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
		echo create_drop_down( "cbo_sewing_line", 110,$line_array_new,"", 1, "--- Select ---", $selected, "",0,0 );
	}
	else
	{
		if( $explode_data[0]==0 && $explode_data[1]!=0 ) $cond = " and location_name= $explode_data[1]";
		if( $explode_data[0]!=0 ) $cond = " and floor_name= $explode_data[0]";

		echo create_drop_down( "cbo_sewing_line", 110, "select id,line_name from lib_sewing_line where  is_deleted=0 and status_active=1 and floor_name!=0 $cond order by id, line_name","id,line_name", 1, "--- Select ---", $selected, "",0,0 );
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
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
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
			else //if(str==2)
			{
				load_drop_down( 'sewing_output_controller',document.getElementById('company_search_by').value,'load_drop_down_buyer', 'search_by_td' );
				document.getElementById('search_by_th_up').innerHTML="Select Buyer Name";
			}

		}

	function js_set_value(id,item_id,po_qnty,plan_qnty,country_id)
	{
		$("#hidden_mst_id").val(id);
		$("#hidden_grmtItem_id").val(item_id);
		$("#hidden_po_qnty").val(po_qnty);
		$("#hidden_country_id").val(country_id);
		$("#hidden_company_id").val(document.getElementById('company_search_by').value);
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
									<th colspan="9"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,"", 1, "-- Select --", 1, "",0 ); ?></th>
							</tr>
                        	<th width="130" class="must_entry_caption">Company</th>
                        	<th width="130">Search By</th>
                        	<th width="130" align="center" id="search_by_th_up">Enter Order Number</th>
                        	<th width="200" class="must_entry_caption">Date Range</th>
                        	<th width="80"><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" /></th>
                    	</thead>
        				<tr class="general">
        					<td><? echo create_drop_down( "company_search_by", 130, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select --", $selected, "", 0 ); ?></td>
                    		<td>
								<?
									//$searchby_arr=array(0=>"Order No",1=>"Style Ref. Number",2=>"Buyer Name");
									$searchby_arr=array(0=>"Order No",1=>"Style Ref. Number",2=>"Buyer Name",3=>"Job No",4=>"Actual PO No",5=>"File No",6=>"Internal Ref");
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
          		</td>
            </tr>
    	</table>
        <div style="margin-top:10px" id="search_div"></div>
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
 	
 	$variable_sql=sql_select("SELECT sewing_production from variable_settings_production where company_name=$company and variable_list=1 and status_active=1");
	$sewing_level=$variable_sql[0][csf("sewing_production")];

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
				$sql_cond = " and a.job_no_prefix_num='$txt_search_common'";
			else if(trim($txt_search_by)==4)
				$sql_cond = " and upper(b.po_number_acc) like upper('%".trim($txt_search_common)."%')";
			else if(trim($txt_search_by)==5)
				$sql_cond = " and upper(b.file_no) like upper('%".trim($txt_search_common)."%')";
			else if(trim($txt_search_by)==6)
				$sql_cond = " and upper(b.grouping) like upper('%".trim($txt_search_common)."%')";
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
				$sql_cond = " and a.job_no_prefix_num='$txt_search_common'";
			else if(trim($txt_search_by)==4)
				$sql_cond = " and b.po_number_acc='$txt_search_common'";
			else if(trim($txt_search_by)==5)
				$sql_cond = " and b.file_no='$txt_search_common'";
			else if(trim($txt_search_by)==6)
				$sql_cond = " and b.grouping='$txt_search_common'";
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
				$sql_cond = " and a.job_no_prefix_num='$txt_search_common'";
			else if(trim($txt_search_by)==4)
				$sql_cond = " and upper(b.po_number_acc) like upper('".trim($txt_search_common)."%')";
			else if(trim($txt_search_by)==5)
				$sql_cond = " and upper(b.file_no) like upper('".trim($txt_search_common)."%')";
			else if(trim($txt_search_by)==6)
				$sql_cond = " and upper(b.grouping) like upper('".trim($txt_search_common)."%')";
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
				$sql_cond = " and a.job_no_prefix_num='$txt_search_common'";
			else if(trim($txt_search_by)==4)
				$sql_cond = " and upper(b.po_number_acc) like upper('%".trim($txt_search_common)."')";
			else if(trim($txt_search_by)==5)
				$sql_cond = " and upper(b.file_no) like upper('%".trim($txt_search_common)."')";
			else if(trim($txt_search_by)==6)
				$sql_cond = " and upper(b.grouping) like upper('%".trim($txt_search_common)."')";
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
		if($db_type==0) { $sql_shipment_year_cond=" and YEAR(a.insert_date)=$year";   }
		if($db_type==2) { $sql_shipment_year_cond=" and to_char(a.insert_date,'YYYY')=$year";}
	}

	$is_projected_po_allow=return_field_value("production_entry","variable_settings_production","variable_list=58 and company_name=$company");
    $projected_po_cond = ($is_projected_po_allow==2) ? " and b.is_confirmed=1" : "";
	
	$sql = "SELECT b.id, a.order_uom, a.buyer_name, a.company_name, a.total_set_qnty, a.set_break_down, a.job_no, a.style_ref_no, a.gmts_item_id, a.location_name, b.shipment_date, b.po_number, b.po_number_acc, b.po_quantity, b.plan_cut, b.grouping, b.file_no
			from wo_po_details_master a, wo_po_break_down_vw b where a.job_no = b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.garments_nature=$garments_nature $sql_cond $sql_shipment_year_cond $projected_po_cond order by b.shipment_date desc"; 

	$result = sql_select($sql);
	$all_job_array=array();
	$all_po_array=array();
	foreach($result as $k=>$v)
	{
		$all_job_array[trim($v[csf("job_no")])]=trim($v[csf("job_no")]);
		$all_po_array[trim($v[csf("id")])]=trim($v[csf("id")]);
	}
 	$all_job="'".implode("','", array_unique($all_job_array))."'";
    $all_po="'".implode("','", array_unique($all_po_array))."'";

    $all_po_cond = where_con_using_array($all_po_array,0,"po_break_down_id");

 	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');

	$po_country_sql=sql_select("SELECT po_break_down_id, country_id from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 $all_po_cond group by po_break_down_id,country_id");
	foreach ($po_country_sql as $key => $value)
	{
		if($po_country_arr[$value[csf("po_break_down_id")]]=="")
		{
			$po_country_arr[$value[csf("po_break_down_id")]].=$value[csf("country_id")];
		}
		else
		{
			$po_country_arr[$value[csf("po_break_down_id")]].=','.$value[csf("country_id")];
		}
	}

	$po_country_data_arr=array();
	$poCountryData=sql_select( "SELECT po_break_down_id, item_number_id, country_id, sum(order_quantity) as qnty, sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 $all_po_cond group by po_break_down_id, item_number_id, country_id");

	foreach($poCountryData as $row)
	{
		$po_country_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]['po_qnty']=$row[csf('qnty')];
		$po_country_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]['plan_cut_qnty']=$row[csf('plan_cut_qnty')];
	}

	$all_po_cond = where_con_using_array($all_po_array,0,"a.po_break_down_id");
	$total_in_qty_data_arr=array();
	if($sewing_level==1)
	{
		$total_in_qty=sql_select( "SELECT a.po_break_down_id, a.item_number_id, a.country_id, sum(a.production_quantity) as production_quantity,sum(case when a.production_type=4   then a.production_quantity else 0 end ) as production_quantity ,sum(case when a.production_type=5   then a.production_quantity else 0 end ) as production_quantity_swingout from pro_garments_production_mst a where a.status_active=1 and a.is_deleted=0 and a.production_type in(4,5)  $all_po_cond group by a.po_break_down_id, a.item_number_id, a.country_id");
	}
	else
	{
		$total_in_qty=sql_select( "SELECT a.po_break_down_id, a.item_number_id, a.country_id, sum(b.production_qnty) as production_quantity,sum(case when a.production_type=4 and b.production_type=4 then b.production_qnty else 0 end ) as production_quantity ,sum(case when a.production_type=5 and b.production_type=5 then b.production_qnty else 0 end ) as production_quantity_swingout from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and a.production_type in(4,5)  and b.status_active=1 and b.is_deleted=0 and b.production_type in(4,5) $all_po_cond group by a.po_break_down_id, a.item_number_id, a.country_id");
	}

	foreach($total_in_qty as $row)
	{
		$total_in_qty_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]["sew_in"]=$row[csf('production_quantity')];
		$total_in_qty_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]["sew_out"]=$row[csf('production_quantity_swingout')];
	}
	?>

     <div style="width:1270px;">
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="70">Shipment Date</th>
                <th width="100">Order No</th>
                <th width="100">Acc.Order No</th> 
                <th width="100">Buyer</th>
                <th width="120">Style</th>
                <th width="80">File No</th>
                <th width="80">Ref. No</th>
                <th width="140">Item</th>
                <th width="100">Country</th>
                <th width="80">Order Qty</th>
                <th width="80">Total Sewing Input Qty</th>
                <th width="80">Total Sewing Output Qty</th>
                <th>Balance</th>
            </thead>
     	</table>
     </div>
     <div style="width:1270px; max-height:240px;overflow-y:scroll;" >
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1252" class="rpt_table" id="tbl_po_list">
			<?
			$i=1;
            foreach( $result as $row )
            {
				$exp_grmts_item = explode("__",$row[csf("set_break_down")]);
				$numOfItem = count($exp_grmts_item);
				$set_qty=""; $grmts_item="";

				//$country=explode(",",$po_country_arr[$row[csf("id")]]);
				$country=array_unique(explode(",",$po_country_arr[$row[csf("id")]]));

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
						if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						//$po_qnty=$row[csf("po_quantity")]; $plan_cut_qnty=$row[csf("plan_cut")];
						$po_qnty=$po_country_data_arr[$row[csf('id')]][$grmts_item][$country_id]['po_qnty'];
						$plan_cut_qnty=$po_country_data_arr[$row[csf('id')]][$grmts_item][$country_id]['plan_cut_qnty'];

						?>
							<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value(<? echo $row[csf("id")];?>,'<? echo $grmts_item;?>','<? echo $po_qnty;?>','<? echo $plan_cut_qnty;?>','<? echo $country_id;?>');" >
								<td width="30" align="center"><?php echo $i; ?></td>
								<td width="70" style="word-break:break-all"><?php echo change_date_format($row[csf("shipment_date")]);?></td>
								<td width="100" style="word-break:break-all"><?php echo $row[csf("po_number")]; ?></td>
                                <td width="100" style="word-break:break-all"><?php echo $row[csf("po_number_acc")]; ?></td>
								<td width="100" style="word-break:break-all"><?php echo $buyer_arr[$row[csf("buyer_name")]]; ?></td>
								<td width="120" style="word-break:break-all"><?php echo $row[csf("style_ref_no")]; ?></td>
                                <td width="80" style="word-break:break-all"><?php echo $row[csf("file_no")]; ?></td>
                                <td width="80" style="word-break:break-all"><?php echo $row[csf("grouping")]; ?></td>
								<td width="140" style="word-break:break-all"><?php  echo $garments_item[$grmts_item];?></td>
								<td width="100" style="word-break:break-all"><?php echo $country_library[$country_id]; ?>&nbsp;</td>
								<td width="80" align="right"><?php echo $po_qnty; ?>&nbsp;</td>
                                <td width="80" align="right"><?php echo $total_in_qty=$total_in_qty_data_arr[$row[csf('id')]][$grmts_item][$country_id]["sew_in"]; ?> &nbsp;</td>
                                <td width="80" align="right"><?php echo $total_cut_qty=$total_in_qty_data_arr[$row[csf('id')]][$grmts_item][$country_id]["sew_out"]; ?>&nbsp;</td>
                                <td align="right"><?php $balance=$po_qnty-$total_cut_qty; echo $balance; ?>&nbsp; </td>
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
	exit();
}

if ($action=="all_system_id_popup")
{
		extract($_REQUEST);
		echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
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
	$preceding_process = $dataArr[3];

	$qty_source=0;
	if($preceding_process==11) $qty_source=68; //Attachment Complete
	else if($preceding_process==112) $qty_source=112; //Mending Complete
	else if($preceding_process==3) $qty_source=3; //Wash Complete

	$company_id_sql=sql_select("SELECT a.company_name from wo_po_details_master a,wo_po_break_down b where a.id=b.job_id and b.id=$po_id group by a.company_name");
	$company_id=$company_id_sql[0][csf("company_name")];
	$variable_sql=sql_select("SELECT sewing_production from variable_settings_production where company_name=$company_id and variable_list=1 and status_active=1");
	$sewing_level=$variable_sql[0][csf("sewing_production")];

	$res = sql_select("SELECT a.id, a.po_quantity, a.plan_cut, a.po_number, a.po_quantity, b.company_name, b.buyer_name, b.style_ref_no, b.gmts_item_id, b.order_uom, b.job_no, b.location_name
			from wo_po_break_down a, wo_po_details_master b
			where a.job_id=b.id and a.id=$po_id");

  	foreach($res as $result)
	{
		echo "$('#txt_order_no').val('".$result[csf('po_number')]."');\n";
		echo "$('#hidden_po_break_down_id').val('".$result[csf('id')]."');\n";
		echo "$('#cbo_buyer_name').val('".$result[csf('buyer_name')]."');\n";
		echo "$('#txt_job_no').val('".$result[csf('job_no')]."');\n";
		echo "$('#txt_style_no').val('".$result[csf('style_ref_no')]."');\n";
		if($sewing_level==1)
		{
			$dataArray=sql_select("SELECT SUM(CASE WHEN a.production_type=$qty_source   THEN a.production_quantity END) as totalinput,SUM(CASE WHEN a.production_type=5   THEN production_quantity ELSE 0 END) as totalsewing from pro_garments_production_mst a WHERE   a.po_break_down_id=".$result[csf('id')]."  and a.item_number_id='$item_id' and a.country_id='$country_id' and a.status_active=1 and a.is_deleted=0 and a.status_active=1 and a.is_deleted=0");

		}
		else
		{
			$dataArray=sql_select("SELECT SUM(CASE WHEN a.production_type=$qty_source and b.production_type=$qty_source THEN production_qnty END) as totalinput,SUM(CASE WHEN a.production_type=5 and b.production_type=5 THEN production_qnty ELSE 0 END) as totalsewing from pro_garments_production_mst a,pro_garments_production_dtls b WHERE a.id=b.mst_id and  a.po_break_down_id=".$result[csf('id')]."  and a.item_number_id='$item_id' and a.country_id='$country_id' and a.status_active=1 and a.is_deleted=0 and a.status_active=1 and a.is_deleted=0");
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

if($action=="wo_no_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
		function set_checkvalue()
		{
			if(document.getElementById('chk_job_wo_po').value==0)
				document.getElementById('chk_job_wo_po').value=1;
			else
				document.getElementById('chk_job_wo_po').value=0;
		}
		function js_set_value(val)
		{
			$("#hidden_sys_data").val(val);
			//$("#hidden_id").val(id);
			parent.emailwindow.hide();
		}
</script>
</head>
<body>
<div style="width:850px;" align="center" >
	<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
		<table width="850" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all">
			<thead>
				<tr>
					<th colspan="6">
						<? echo create_drop_down( "cbo_search_category", 130, $string_search_type,'', 1, "-- Search Catagory --" ); ?>
					</th>
					<th colspan="2" style="text-align:right"><input type="checkbox" value="0" onClick="set_checkvalue()" id="chk_job_wo_po">WO Without Job</th>
				</tr>
				<tr>
					<th width="120">Buyer Name</th>
					<th width="130">Supplier Name</th>
					<th width="100">WO No</th>
					<th width="100">Job No</th>
                    <th width="100">Style Ref.</th>
					<th width="130" colspan="2"> WO Date Range</th>
					<th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton" onClick="reset_form('searchorderfrm_1','search_div','','','','');"  /></th>
				</tr>
			</thead>
			<tbody>
				<tr class="general">
				<td><?=create_drop_down( "cbo_buyer_name", 120, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company_id' $buyer_cond order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", "", "",0); ?></td>
				<td><?=create_drop_down( "cbo_supplier_name", 130, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type in (2,21) and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select Supplier --", $service_company_id, "",0 ); 
				//echo "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.tag_company=$company_id and b.party_type in (2,21) and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name";
				
				?></td>
                <td><input name="txt_booking_prifix" id="txt_booking_prifix" class="text_boxes" style="width:90px"></td>
                
                <td><input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:90px"></td>
                <td><input name="txt_style_ref" id="txt_style_ref" class="text_boxes" style="width:90px"></td>
                <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date"/></td>
                <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To Date" /> </td>
                <td>
                    <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_supplier_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company_id; ?>+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('cbo_search_category').value+'_'+document.getElementById('txt_booking_prifix').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('chk_job_wo_po').value+'_'+document.getElementById('txt_style_ref').value+'_'+'<? echo $txt_job_no; ?>', 'create_wo_search_list_view', 'search_div', 'sewing_output_controller', 'setFilterGrid(\'tbl_list_search\',-1)')" style="width:100px;" />
                </td>
            </tr>
            <tr>
                <td align="center" valign="middle" colspan="8">
                    <?=load_month_buttons(1);  ?>
                    <input type="hidden" id="hidden_sys_data" value="hidden_sys_data" />
                </td>
            </tr>
        </tbody>
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

if($action=="create_wo_search_list_view")
{
	$ex_data = explode("_",$data);
	$supplier = $ex_data[0];
	$fromDate = $ex_data[1];
	$toDate = $ex_data[2];
	$company = $ex_data[3];
	$buyer_val=$ex_data[4];
	$search_category=$ex_data[5];
	$booking_prifix=$ex_data[6];
	$job_prifix=$ex_data[7];
	$year_selection=$ex_data[8];
	$chk_job_wo_po=trim($ex_data[9]);
	$style_ref=$ex_data[10];
	$jobno=$ex_data[11];
		
	if( $supplier!=0 )  $supplier="and a.supplier_id='$supplier'"; else  $supplier="";
	if( $company!=0 )  $company=" and a.company_id='$company'"; else  $company="";
	if( $buyer_val!=0 )  $buyer_cond="and d.buyer_name='$buyer_val'"; else  $buyer_cond="";
	
	$booking_year_cond=" and to_char(a.insert_date,'YYYY')=$ex_data[8]";
	$year_cond=" and to_char(d.insert_date,'YYYY')=$ex_data[8]";
	if( $fromDate!=0 && $toDate!=0 ) $sql_cond= "and a.booking_date  between '".change_date_format($fromDate,'mm-dd-yyyy','/',1)."' and '".change_date_format($toDate,'mm-dd-yyyy','/',1)."'";

	if($search_category==0 || $search_category==4)
	{
		if (str_replace("'","",$job_prifix)!="") $job_cond=" and d.job_no_prefix_num like '%$job_prifix%' $year_cond "; else  $job_cond="";
		if (str_replace("'","",$booking_prifix)!="") $booking_cond=" and a.subcon_wo_suffix_num like '%$booking_prifix%'  $booking_year_cond  "; else $booking_cond="";
	}
	else if($search_category==1)
	{
		if (str_replace("'","",$job_prifix)!="") $job_cond=" and d.job_no_prefix_num ='$job_prifix' "; else  $job_cond="";
		if (str_replace("'","",$booking_prifix)!="") $booking_cond=" and a.subcon_wo_suffix_num ='$booking_prifix'   "; else $booking_cond="";
	}
	else if($search_category==2)
	{
		if (str_replace("'","",$job_prifix)!="") $job_cond=" and d.job_no_prefix_num like '$job_prifix%'  $year_cond"; else  $job_cond="";
		if (str_replace("'","",$booking_prifix)!="") $booking_cond=" and a.subcon_wo_suffix_num like '$booking_prifix%'  $booking_year_cond  "; else $booking_cond="";
	}
	else if($search_category==3)
	{
		if (str_replace("'","",$job_prifix)!="") $job_cond=" and d.job_no_prefix_num like '%$job_prifix'  $year_cond"; else  $job_cond="";
		if (str_replace("'","",$booking_prifix)!="") $booking_cond=" and a.subcon_wo_suffix_num like '%$booking_prifix'  $booking_year_cond  "; else $booking_cond="";
	}

	if($db_type==0) $select_year="year(a.insert_date) as year"; else $select_year="to_char(a.insert_date,'YYYY') as year";
	if($chk_job_wo_po==1)
	{
		$sql = "select a.id, a.subcon_wo_suffix_num, a.SUCON_WO_NO, a.company_id, a.supplier_id, a.booking_date, a.delivery_date, a.delivery_date_end, a.dy_delivery_date_start, a.dy_delivery_date_end, a.currency, a.ecchange_rate, a.pay_mode, a.source, a.attention, $select_year, 0 as job_no_id, null as job_no, 0 as buyer_name, null as po_number
		from subcon_wo_mst a
		where a.status_active=1 and a.is_deleted=0 and a.entry_form=643 and a.id not in(select mst_id from subcon_wo_dtls where job_no_id>0 and entry_form=643 and status_active=1 and  is_deleted=0) $company $supplier  $sql_cond  $booking_cond order by a.id DESC";
	}
	else
	{
		$sql = "select a.id, a.subcon_wo_suffix_num, a.SUCON_WO_NO, a.supplier_id, a.booking_date, a.CLOSING_DATE, a.currency, a.service_sweater, TO_CHAR(a.insert_date,'YYYY') as year, d.buyer_name, LISTAGG(CAST(b.job_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.job_no) as job_no, LISTAGG(CAST(d.style_ref_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY d.style_ref_no) as style_ref_no from subcon_wo_mst a, subcon_wo_dtls b, wo_po_details_master d where a.id=b.mst_id and b.job_no = d.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and a.pay_mode in (1,2,4) and a.entry_form=643 and b.entry_form=643 and d.job_no='$jobno' $company $supplier $sql_cond $buyer_cond $job_cond $booking_cond $job_ids_cond group by a.id, a.subcon_wo_suffix_num, a.SUCON_WO_NO, a.supplier_id, a.booking_date, a.CLOSING_DATE, a.currency, a.service_sweater, a.insert_date, d.buyer_name order by a.id DESC";
	}
	//echo $sql;
	?>
	<div style="width:850px;" align="center">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="850" class="rpt_table" >
			<thead>
				<th width="30">SL</th>
				<th width="100">WO no</th>
                <th width="50">WO Year</th>
                <th width="70">WO Date</th>
                <th width="140">Service Company</th>
                <th width="140">Buyer Name</th>
				<th width="100">Job No</th>
                <th width="120">Style Ref.</th>
				<th >Closing Date</th>
			</thead>
		</table>
		<div style="width:850px; overflow-y:scroll; max-height:270px;" id="buyer_list_view">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="830" class="rpt_table" id="tbl_list_search" >
				<?
				$supplier_arr=return_library_array("select id, supplier_name from lib_supplier",'id','supplier_name');
				$buyer_arr=return_library_array("select id, buyer_name from lib_buyer",'id','buyer_name');
				$i=1;
				$nameArray=sql_select( $sql );
				$linkingWoArr=array();
				foreach($nameArray as $row)
				{
					$typeofservice=explode(",",$row[csf("service_sweater")]);
					if (in_array(8, $typeofservice)) {
						$linkingWoArr[$row[csf('id')]]=$row[csf('SUCON_WO_NO')];
					}
				}
				//var_dump($nameArray);die;
				foreach ($nameArray as $selectResult)
				{
					if($linkingWoArr[$selectResult[csf('id')]]!="")
					{
						$job_no=implode(",",array_unique(explode(",",$selectResult[csf("job_no")])));
						$style_ref_no=implode(",",array_unique(explode(",",$selectResult[csf("style_ref_no")])));
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$supplier=$supplier_arr[$selectResult[csf('supplier_id')]];
						
						$ref_no=implode(",",array_unique(explode(",",chop($po_ref_arr[$selectResult[csf("id")]],","))));
						?>
						<tr bgcolor="<?=$bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<?=$i;?>" onClick="js_set_value('<?=$selectResult[csf('id')].'_'.$selectResult[csf('SUCON_WO_NO')]; ?>'); ">
							<td width="30" align="center"><?=$i; ?></td>
							<td width="100" align="center" style="word-break:break-all"><?=$selectResult[csf('SUCON_WO_NO')]; ?></td>
							<td width="50" align="center"><?=$selectResult[csf('year')]; ?></td>
							<td width="70"><?=change_date_format($selectResult[csf('booking_date')]); ?></td>
							<td width="140" style="word-break:break-all"><?=$supplier; ?></td>
							<td width="140" style="word-break:break-all"><?=$buyer_arr[$selectResult[csf('buyer_name')]]; ?></td>
							<td width="100" style="word-break:break-all"><?=$job_no; ?></td>
							<td width="120" style="word-break:break-all"><?=$style_ref_no; ?></td>
							<td><?=change_date_format($selectResult[csf('CLOSING_DATE')]); ?></td>
						</tr>
							<?
						$i++;
					}
				}
				?>
			</table>
		</div>
	</div>
		<?
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
		$preceding_process = $dataArr[6];

		$qty_source=0;
		if($preceding_process==11) $qty_source=68; //Attachment Complete
		else if($preceding_process==112) $qty_source=112; //Mending Complete
		else if($preceding_process==3) $qty_source=3; //Wash Complete

		$color_library=return_library_array( "select id, color_name from lib_color",'id','color_name');
		$size_library=return_library_array( "select id, size_name from lib_size",'id','size_name');

		//#############################################################################################//
		// order wise - color level, color and size level


		//$variableSettings=2;
		if($qty_source!=0)
		{
			if( $variableSettings==2 ) // color level
			{
				if($db_type==0)
				{
					$sql = "SELECT id, item_number_id, size_number_id, color_number_id, order_quantity, sum(plan_cut_qnty) as plan_cut_qnty, (select sum(CASE WHEN pdtls.color_size_break_down_id=wo_po_color_size_breakdown.id then pdtls.production_qnty ELSE 0 END) from pro_garments_production_dtls pdtls where pdtls.production_type=4 and pdtls.is_deleted=0 ) as production_qnty, (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.production_qnty ELSE 0 END) from pro_garments_production_dtls cur where cur.production_type=5 and cur.is_deleted=0 ) as cur_production_qnty
					from wo_po_color_size_breakdown
					where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 group by color_number_id";
				}
				else
				{
					$sql = "SELECT a.item_number_id, a.color_number_id, sum(a.order_quantity) as order_quantity, sum(a.plan_cut_qnty) as plan_cut_qnty,
							sum(CASE WHEN c.production_type=$qty_source then b.production_qnty ELSE 0 END) as production_qnty,
							sum(CASE WHEN c.production_type=5 then b.production_qnty ELSE 0 END) as cur_production_qnty,
							sum(CASE WHEN c.production_type=5 then b.reject_qty ELSE 0 END) as reject_qty
							from wo_po_color_size_breakdown a
							left join pro_garments_production_dtls b on a.id=b.color_size_break_down_id and b.is_deleted=0 and b.status_active=1
							left join pro_garments_production_mst c on c.id=b.mst_id and c.is_deleted=0 and c.status_active=1
							where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active=1 group by a.item_number_id, a.color_number_id";

				}
			}
			else if( $variableSettings==3 ) //color and size level
			{
				/*$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty, (select sum(CASE WHEN pdtls.color_size_break_down_id=wo_po_color_size_breakdown.id then pdtls.production_qnty ELSE 0 END) from pro_garments_production_dtls pdtls where pdtls.production_type=4 and pdtls.is_deleted=0 ) as production_qnty, (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.production_qnty ELSE 0 END) from pro_garments_production_dtls cur where cur.production_type=5 and cur.is_deleted=0 ) as cur_production_qnty
					from wo_po_color_size_breakdown
					where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1";*/

				$dtlsData = sql_select("SELECT a.color_size_break_down_id,
											sum(CASE WHEN a.production_type=$qty_source then a.production_qnty ELSE 0 END) as production_qnty,
											sum(CASE WHEN a.production_type=5 then a.production_qnty ELSE 0 END) as cur_production_qnty ,
											sum(CASE WHEN a.production_type=5 then a.reject_qty ELSE 0 END) as reject_qty
											from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and b.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in($qty_source,5) group by a.color_size_break_down_id");

				foreach($dtlsData as $row)
				{
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['iss']= $row[csf('production_qnty')];
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv']= $row[csf('cur_production_qnty')];
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rej']= $row[csf('reject_qty')];
				}

				$sql = "select id,size_order, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty
					from wo_po_color_size_breakdown
					where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 order by color_number_id,size_order ";

			}
			else // by default color and size level
			{
				/*$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty, (select sum(CASE WHEN pdtls.color_size_break_down_id=wo_po_color_size_breakdown.id then pdtls.production_qnty ELSE 0 END) from pro_garments_production_dtls pdtls where pdtls.production_type=4 and pdtls.is_deleted=0 ) as production_qnty, (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.production_qnty ELSE 0 END) from pro_garments_production_dtls cur where cur.production_type=5 and cur.is_deleted=0 ) as cur_production_qnty
					from wo_po_color_size_breakdown
					where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1";*/

					$dtlsData = sql_select("SELECT a.color_size_break_down_id,
											sum(CASE WHEN a.production_type=$qty_source then a.production_qnty ELSE 0 END) as production_qnty,
											sum(CASE WHEN a.production_type=5 then a.production_qnty ELSE 0 END) as cur_production_qnty,
											sum(CASE WHEN a.production_type=5 then a.reject_qty ELSE 0 END) as reject_qty
											from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and b.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in($qty_source,5) group by a.color_size_break_down_id");

				foreach($dtlsData as $row)
				{
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['iss']= $row[csf('production_qnty')];
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv']= $row[csf('cur_production_qnty')];
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rej']= $row[csf('reject_qty')];
				}

				$sql = "select id, size_order,item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty
					from wo_po_color_size_breakdown
					where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 order by color_number_id,size_order ";
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
					$colorHTML .='<tr><td>'.$color_library[$color[csf("color_number_id")]].'</td><td><input type="text" name="txt_color" id="colSize_'.($i+1).'" style="width:60px"  class="text_boxes_numeric" placeholder="'.($color[csf("production_qnty")]-$color[csf("cur_production_qnty")]).'" onblur="fn_colorlevel_total('.($i+1).')" onkeypress="return isNumber(event)"></td><td><input type="text" name="txtColSizeRej" id="colSizeRej_'.($i+1).'" style="width:60px"  class="text_boxes_numeric" placeholder="Rej." onblur="fn_colorRej_total('.($i+1).') '.$disable.'" onkeypress="return isNumber(event)"></td></tr>';
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
					$rej_qnty=$color_size_qnty_array[$color[csf('id')]]['rej'];

	 				$colorHTML .='<tr><td>'.$size_library[$color[csf("size_number_id")]].'</td><td><input type="text" name="colorSize" id="colSize_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:80px" placeholder="'.($iss_qnty-$rcv_qnty).'" onblur="fn_total('.$color[csf("color_number_id")].','.($i+1).')" onkeypress="return isNumber(event)"><input type="text" name="colorSizeRej" id="colSizeRej_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:50px" placeholder="Rej. Qty" onblur="fn_total_rej('.$color[csf("color_number_id")].','.($i+1).')" '.$disable.' onkeypress="return isNumber(event)"><input type="text" name="colorSizePOQnty" id="colorSizePOQnty_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:70px" value="'.$color[csf("order_quantity")].'" readonly disabled></td></tr>';
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
		else
		{
			if( $variableSettings==2 ) // color level
			{
				if($db_type==0)
				{
					$sql = "SELECT id, item_number_id, size_number_id, color_number_id, order_quantity, sum(plan_cut_qnty) as plan_cut_qnty, (select sum(CASE WHEN pdtls.color_size_break_down_id=wo_po_color_size_breakdown.id then pdtls.production_qnty ELSE 0 END) from pro_garments_production_dtls pdtls where pdtls.production_type=4 and pdtls.is_deleted=0 ) as production_qnty, (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.production_qnty ELSE 0 END) from pro_garments_production_dtls cur where cur.production_type=5 and cur.is_deleted=0 ) as cur_production_qnty
					from wo_po_color_size_breakdown
					where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 group by color_number_id";
				}
				else
				{
					$sql = "SELECT a.item_number_id, a.color_number_id, sum(a.order_quantity) as order_quantity, sum(a.plan_cut_qnty) as plan_cut_qnty,
							sum(CASE WHEN c.production_type=4 then b.production_qnty ELSE 0 END) as production_qnty,
							sum(CASE WHEN c.production_type=5 then b.production_qnty ELSE 0 END) as cur_production_qnty,
							sum(CASE WHEN c.production_type=5 then b.reject_qty ELSE 0 END) as reject_qty
							from wo_po_color_size_breakdown a
							left join pro_garments_production_dtls b on a.id=b.color_size_break_down_id and b.is_deleted=0 and b.status_active=1
							left join pro_garments_production_mst c on c.id=b.mst_id and c.is_deleted=0 and c.status_active=1
							where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active=1 group by a.item_number_id, a.color_number_id";

				}
			}
			else if( $variableSettings==3 ) //color and size level
			{
				/*$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty, (select sum(CASE WHEN pdtls.color_size_break_down_id=wo_po_color_size_breakdown.id then pdtls.production_qnty ELSE 0 END) from pro_garments_production_dtls pdtls where pdtls.production_type=4 and pdtls.is_deleted=0 ) as production_qnty, (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.production_qnty ELSE 0 END) from pro_garments_production_dtls cur where cur.production_type=5 and cur.is_deleted=0 ) as cur_production_qnty
					from wo_po_color_size_breakdown
					where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1";*/

				$dtlsData = sql_select("SELECT a.color_size_break_down_id,
											sum(CASE WHEN a.production_type=4 then a.production_qnty ELSE 0 END) as production_qnty,
											sum(CASE WHEN a.production_type=5 then a.production_qnty ELSE 0 END) as cur_production_qnty ,
											sum(CASE WHEN a.production_type=5 then a.reject_qty ELSE 0 END) as reject_qty
											from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and b.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in(4,5) group by a.color_size_break_down_id");

				foreach($dtlsData as $row)
				{
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['iss']= $row[csf('production_qnty')];
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv']= $row[csf('cur_production_qnty')];
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rej']= $row[csf('reject_qty')];
				}

				$sql = "select id,size_order, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty
					from wo_po_color_size_breakdown
					where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 order by color_number_id,size_order ";

			}
			else // by default color and size level
			{
				/*$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty, (select sum(CASE WHEN pdtls.color_size_break_down_id=wo_po_color_size_breakdown.id then pdtls.production_qnty ELSE 0 END) from pro_garments_production_dtls pdtls where pdtls.production_type=4 and pdtls.is_deleted=0 ) as production_qnty, (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.production_qnty ELSE 0 END) from pro_garments_production_dtls cur where cur.production_type=5 and cur.is_deleted=0 ) as cur_production_qnty
					from wo_po_color_size_breakdown
					where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1";*/

					$dtlsData = sql_select("SELECT a.color_size_break_down_id,
											sum(CASE WHEN a.production_type=4 then a.production_qnty ELSE 0 END) as production_qnty,
											sum(CASE WHEN a.production_type=5 then a.production_qnty ELSE 0 END) as cur_production_qnty,
											sum(CASE WHEN a.production_type=5 then a.reject_qty ELSE 0 END) as reject_qty
											from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and b.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in(4,5) group by a.color_size_break_down_id");

				foreach($dtlsData as $row)
				{
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['iss']= $row[csf('production_qnty')];
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv']= $row[csf('cur_production_qnty')];
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rej']= $row[csf('reject_qty')];
				}

				$sql = "select id, size_order,item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty
					from wo_po_color_size_breakdown
					where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 order by color_number_id,size_order ";
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
					$colorHTML .='<tr><td>'.$color_library[$color[csf("color_number_id")]].'</td><td><input type="text" name="txt_color" id="colSize_'.($i+1).'" style="width:60px"  class="text_boxes_numeric" placeholder="'.($color[csf("production_qnty")]-$color[csf("cur_production_qnty")]).'" onblur="fn_colorlevel_total('.($i+1).')" onkeypress="return isNumber(event)"></td><td><input type="text" name="txtColSizeRej" id="colSizeRej_'.($i+1).'" style="width:60px"  class="text_boxes_numeric" placeholder="Rej." onblur="fn_colorRej_total('.($i+1).') '.$disable.'" onkeypress="return isNumber(event)"></td></tr>';
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
					$rej_qnty=$color_size_qnty_array[$color[csf('id')]]['rej'];

	 				$colorHTML .='<tr><td>'.$size_library[$color[csf("size_number_id")]].'</td><td><input type="text" name="colorSize" id="colSize_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:80px" placeholder="'.($iss_qnty-$rcv_qnty).'" onblur="fn_total('.$color[csf("color_number_id")].','.($i+1).')" onkeypress="return isNumber(event)"><input type="text" name="colorSizeRej" id="colSizeRej_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:50px" placeholder="Rej. Qty" onblur="fn_total_rej('.$color[csf("color_number_id")].','.($i+1).')" '.$disable.' onkeypress="return isNumber(event)"><input type="text" name="colorSizePOQnty" id="colorSizePOQnty_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:70px" value="'.$color[csf("order_quantity")].'" readonly disabled></td></tr>';
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
}


if($action=="show_dtls_listview")
{
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$sewing_line_arr=return_library_array( "select id, line_name from lib_sewing_line",'id','line_name');
	$sewing_floor_arr=return_library_array( "select id, floor_name from lib_prod_floor",'id','floor_name');
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');

	$dataArr = explode("**",$data);
	$po_id = $dataArr[0];
	$item_id = $dataArr[1];
	$country_id = $dataArr[2];
	$prod_reso_allo = $dataArr[3];
	$company_id_sql=sql_select("SELECT a.company_name from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and b.id=$po_id group by a.company_name");
	$company_id=$company_id_sql[0][csf("company_name")];
	$variable_sql=sql_select("SELECT sewing_production from variable_settings_production where company_name=$company_id and variable_list=1 and status_active=1");
	$sewing_level=$variable_sql[0][csf("sewing_production")];
	?>
     <div style="width:100%;">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table" >
            <thead>
                <th width="20"><p>SL</p></th>
                <th width="110" align="center"><p>Item Name</p></th>
                <th width="80" align="center"><p>Country</p></th>
                <th width="75" align="center"><p>Prod. Date</p></th>
                <th width="100" align="center"><p>QC Pass Qty</p></th>
                <th width="60" align="center"><p>Alter Qty</p></th>
                <th width="60" align="center"><p>Spot Qty</p></th>
                <th width="70" align="center"><p>Reject Qty</p></th>
                <th width="115" align="center"><p>Serving Company</p></th>
                <th width="80" align="center"><p>Location</p></th>
                <th width="50" align="center"><p>Floor</p></th>
                <th width="50" align="center"><p>Sewing Line</p></th>
                <th width="70" align="center"><p>Color Type</p></th>
                <th width="50" align="center"><p>Rep. Hour</p></th>
                <th width="80" align="center"><p>Supervisor</p></th>
                <th width="60" align="center"><p>Challan</p></th>
                <th width="50" align="center"><p>Sys Chh.</p></th>
				<th width="100" align="center"><p>Wo No.</p></th>
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
				$sqlResult =sql_select(" SELECT a.id,a.po_break_down_id,a.item_number_id,a.country_id, a.production_date, sum(a.production_quantity) as production_quantity, a.alter_qnty, a.spot_qnty, a.reject_qnty, a.production_source, a.serving_company, a.sewing_line, a.supervisor, a.location, a.prod_reso_allo, $date_cond,a.challan_no,a.floor_id,a.wo_order_no from pro_garments_production_mst a where   a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.production_type='5' and a.status_active=1 and a.is_deleted=0   GROUP BY a.id,a.po_break_down_id,a.item_number_id,a.country_id, a.production_date, a.alter_qnty, a.spot_qnty, a.reject_qnty, a.production_source, a.serving_company, a.sewing_line, a.supervisor, a.location, a.prod_reso_allo, a.production_hour,a.challan_no,a.floor_id ,a.wo_order_no ORDER BY a.production_hour DESC");

			}
			else
			{
				$sqlResult =sql_select(" SELECT a.id,a.po_break_down_id,a.item_number_id,a.country_id, a.production_date, sum(b.production_qnty) as production_quantity, a.alter_qnty, a.spot_qnty, a.reject_qnty, a.production_source, a.serving_company, a.sewing_line, a.supervisor, a.location, a.prod_reso_allo, $date_cond,a.challan_no,a.floor_id,b.color_type_id,a.wo_order_no from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and  a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.production_type='5' and a.status_active=1 and a.is_deleted=0 and b.production_type='5' and b.status_active=1 and b.is_deleted=0 GROUP BY a.id,a.po_break_down_id,a.item_number_id,a.country_id, a.production_date, a.alter_qnty, a.spot_qnty, a.reject_qnty, a.production_source, a.serving_company, a.sewing_line, a.supervisor, a.location, a.prod_reso_allo, a.production_hour,a.challan_no,a.floor_id,b.color_type_id ,a.wo_order_no  ORDER BY a.production_hour DESC");

			}



			foreach($sqlResult as $selectResult)
			{
				if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$total_production_qnty+=$selectResult[csf('production_quantity')];

				$sewing_line='';
				if($selectResult[csf('prod_reso_allo')]==1)
				{
					$line_number=explode(",",$prod_reso_arr[$selectResult[csf('sewing_line')]]);
					foreach($line_number as $val)
					{
						if($sewing_line=='') $sewing_line=$sewing_line_arr[$val]; else $sewing_line.=",".$sewing_line_arr[$val];
					}
				}
				else $sewing_line=$sewing_line_arr[$selectResult[csf('sewing_line')]];

  		?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="get_php_form_data(<? echo $selectResult[csf('id')]; ?>,'populate_input_form_data','requires/sewing_output_controller');" >
				<td width="20" align="center"><p><? echo $i; ?></p></td>
                <td width="110" align="center"><p><? echo $garments_item[$selectResult[csf('item_number_id')]]; ?></p></td>
                <td width="80" align="center"><p><? echo $country_library[$selectResult[csf('country_id')]]; ?></p></td>
                <td width="75" align="center"><p><?php echo change_date_format($selectResult[csf('production_date')]); ?></td>
                <td width="100" align="right"><p><?php  echo $selectResult[csf('production_quantity')]; ?></p></td>
                <td width="60" align="right"><p><?php  echo $selectResult[csf('alter_qnty')]; ?></p></td>
                <td width="60" align="right"><p><?php  echo $selectResult[csf('spot_qnty')]; ?></p></td>
                <td width="70" align="right"><p><?php  echo $selectResult[csf('reject_qnty')]; ?></p></td>
				<?php
                        $source= $selectResult[csf('production_source')];
					   	if($source==3) $serving_company= $supplier_arr[$selectResult[csf('serving_company')]];
						else $serving_company= $company_arr[$selectResult[csf('serving_company')]];
                 ?>
                <td width="115" style="padding-left:2px;"><p><?php echo $serving_company; ?></p></td>
                <td width="80" ><p><?php echo $location_arr[$selectResult[csf('location')]]; ?></p></td>
                <td width="50" align="center"><p><? echo $sewing_floor_arr[$selectResult[csf('floor_id')]]; ?></p></td>
                <td width="50" align="center"><p><? echo $sewing_line; ?></p></td>
                <td width="70" align="center"><p><? echo $color_type[$selectResult[csf('color_type_id')]]; ?></p></td>
                <td width="50" align="center"><p><? echo $selectResult[csf('prod_hour')]; ?></p></td>
                <td width="80" align="center"><p><? echo $selectResult[csf('supervisor')]; ?>&nbsp;</p></td>
                <td width="60"  align="center"><p><? echo $selectResult[csf('challan_no')]; ?></p></td>
                <td width="50"  align="center"><p><? echo $selectResult[csf('id')];?></p></td>
				<td width="100"  align="center"><p><? echo $selectResult['WO_ORDER_NO'];?></p></td>
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
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>

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
            <th width="50">Ship Date</th>
            <th width="45">Order Qty.</th>
            <th width="45">Sew.Input</th>
            <th width="55">Sew. Out</th>
        </thead>
        </table>
        <div style="width:375px;max-height:300px; overflow:y-scroll" align="left">
	        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="355" class="rpt_table" id="country_list_search">
			<?
			$issue_qnty_arr=sql_select("select a.po_break_down_id, a.item_number_id, a.country_id,
			(CASE WHEN a.production_type=5 THEN b.production_qnty ELSE 0 END) AS cutting_qnty,
			(CASE WHEN a.production_type=4 THEN b.production_qnty ELSE 0 END) AS input_qnty
			 from pro_garments_production_mst a, pro_garments_production_dtls b where a.id=b.mst_id and a.po_break_down_id='$data' and a.production_type in(4,5) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
			$issue_data_arr=array();
			foreach($issue_qnty_arr as $row)
			{
				$issue_data_arr[$row[csf("po_break_down_id")]][$row[csf("country_id")]][$row[csf("item_number_id")]]+=$row[csf("cutting_qnty")];
				$sewing_input_arr[$row[csf("po_break_down_id")]][$row[csf("country_id")]][$row[csf("item_number_id")]]+=$row[csf("input_qnty")];
			}
			$i=1;
			$sqlResult =sql_select("select po_break_down_id, item_number_id, country_id, max(country_ship_date) as country_ship_date, sum(order_quantity) as order_qnty, sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown where po_break_down_id='$data' and status_active=1 and is_deleted=0 group by po_break_down_id, item_number_id, country_id order by country_ship_date Asc");
			foreach($sqlResult as $row)
			{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$issue_qnty=$issue_data_arr[$row[csf("po_break_down_id")]][$row[csf("country_id")]][$row[csf("item_number_id")]];
				$sewing_inputqty=$sewing_input_arr[$row[csf("po_break_down_id")]][$row[csf("country_id")]][$row[csf("item_number_id")]];
			?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="put_country_data(<? echo $row[csf('po_break_down_id')].",".$row[csf('item_number_id')].",".$row[csf('country_id')].",".$row[csf('order_qnty')].",".$row[csf('plan_cut_qnty')]; ?>);">
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
	$company_id_sql=sql_select("SELECT  company_id from  pro_garments_production_mst where  id=$data and status_active=1 and is_deleted=0 ");
	$company_id=$company_id_sql[0][csf("company_id")];
	$variable_sql=sql_select("SELECT sewing_production from variable_settings_production where company_name=$company_id and variable_list=1 and status_active=1");
	$sewing_level=$variable_sql[0][csf("sewing_production")];
	if($sewing_level==1)
	{
		$sql_dtls ="SELECT a.id,a.company_id,a.entry_break_down_type, a.garments_nature, a.po_break_down_id, a.challan_no, a.item_number_id, a.country_id, a.production_source, a.produced_by,a.shift_name, a.serving_company, a.sewing_line, a.location, a.embel_name, a.embel_type, a.production_date, sum(a.production_quantity) as production_quantity, a.production_source, $date_cond,  a.supervisor, a.carton_qty, a.remarks, a.floor_id, a.reject_qnty, a.alter_qnty, a.total_produced, a.yet_to_produced, a.spot_qnty, a.wo_order_id, a.wo_order_no, a.currency_id,a.exchange_rate,a.rate from pro_garments_production_mst a  where   a.id='$data'  and a.production_type='5' and a.status_active=1 and a.is_deleted=0 group by  a.id,a.company_id, a.entry_break_down_type, a.garments_nature, a.po_break_down_id, a.challan_no, a.item_number_id, a.country_id, a.production_source, a.produced_by,a.shift_name, a.serving_company, a.sewing_line, a.location, a.embel_name, a.embel_type, a.production_date,  a.production_source, production_hour, a.sewing_line, a.supervisor, a.carton_qty, a.remarks, a.floor_id, a.reject_qnty, a.alter_qnty, a.total_produced, a.yet_to_produced, a.spot_qnty,a.wo_order_id, a.wo_order_no, a.currency_id,a.exchange_rate,a.rate  order by a.id";

	}
	else
	{
		$sql_dtls ="SELECT a.id,a.company_id,a.entry_break_down_type, a.garments_nature, a.po_break_down_id, a.challan_no, a.item_number_id, a.country_id, a.production_source, a.produced_by,a.shift_name, a.serving_company, a.sewing_line, a.location, a.embel_name, a.embel_type, a.production_date, sum(b.production_qnty) as production_quantity, a.production_source, $date_cond,  a.supervisor, a.carton_qty, a.remarks, a.floor_id, a.reject_qnty, a.alter_qnty, a.total_produced, a.yet_to_produced, a.spot_qnty, a.wo_order_id, a.wo_order_no, a.currency_id,a.exchange_rate,a.rate,b.color_type_id from pro_garments_production_mst a,pro_garments_production_dtls b where a.id=b.mst_id and  a.id='$data'  and a.production_type='5' and a.status_active=1 and a.is_deleted=0 and b.production_type='5' and b.status_active=1 and b.is_deleted=0 and (b.color_size_break_down_id!=0 or b.color_size_break_down_id is not null) group by  a.id,a.company_id, a.entry_break_down_type, a.garments_nature, a.po_break_down_id, a.challan_no, a.item_number_id, a.country_id, a.production_source, a.produced_by,a.shift_name, a.serving_company, a.sewing_line, a.location, a.embel_name, a.embel_type, a.production_date,  a.production_source, production_hour, a.sewing_line, a.supervisor, a.carton_qty, a.remarks, a.floor_id, a.reject_qnty, a.alter_qnty, a.total_produced, a.yet_to_produced, a.spot_qnty, a.wo_order_id, a.wo_order_no, a.currency_id,a.exchange_rate,a.rate,b.color_type_id  order by a.id";

	}




  	//echo $sql_dtls;
	$sqlResult =sql_select($sql_dtls);

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


		echo "load_drop_down( 'requires/sewing_output_controller', document.getElementById('cbo_floor').value+'_'+document.getElementById('cbo_location').value+'_'+document.getElementById('prod_reso_allo').value+'_'+document.getElementById('txt_sewing_date').value+'_'+document.getElementById('cbo_sewing_company').value, 'load_drop_down_sewing_line_floor', 'sewing_line_td' );\n";

		echo "$('#cbo_sewing_line').val('".$result[csf('sewing_line')]."');\n";
		echo "get_php_form_data(".$result[csf('production_source')].",'line_disable_enable','requires/sewing_output_controller');\n";

		if($result[csf('production_source')]==3)
		{
			echo "load_drop_down( 'requires/sewing_output_controller', '".$result[csf('company_id')]."_".$result[csf('serving_company')]."_".$result[csf('po_break_down_id')]."', 'load_drop_down_workorder', 'workorder_td' );\n";

			echo "$('#txt_wo_id').val('".$result[csf('wo_order_id')]."');\n";
			echo "$('#txt_wo_no').val('".$result[csf('wo_order_no')]."');\n";
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
		if($sewing_level==1)
		{
			$dataArray=sql_select("SELECT SUM(CASE WHEN a.production_type=4   THEN a.production_quantity END) as totalinput,SUM(CASE WHEN a.production_type=5  THEN a.production_quantity ELSE 0 END) as totalsewing from pro_garments_production_mst a  WHERE   a.po_break_down_id=".$result[csf('po_break_down_id')]." and a.item_number_id=".$result[csf('item_number_id')]." AND a.country_id=".$result[csf('country_id')]." and a.production_source=".$result[csf('production_source')]." and a.serving_company=".$result[csf('serving_company')]." and a.status_active=1 and a.is_deleted=0  ");

		}
		else
		{
			$dataArray=sql_select("SELECT SUM(CASE WHEN a.production_type=4 and b.production_type=4 THEN b.production_qnty END) as totalinput,SUM(CASE WHEN a.production_type=5 and b.production_type=5 THEN b.production_qnty ELSE 0 END) as totalsewing from pro_garments_production_mst a,pro_garments_production_dtls b WHERE a.id=b.mst_id and a.po_break_down_id=".$result[csf('po_break_down_id')]." and a.item_number_id=".$result[csf('item_number_id')]." AND a.country_id=".$result[csf('country_id')]." and a.production_source=".$result[csf('production_source')]." and a.serving_company=".$result[csf('serving_company')]." and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		}


 		foreach($dataArray as $row)
		{
			echo "$('#txt_input_quantity').val('".$row[csf('totalinput')]."');\n";
			echo "$('#txt_cumul_sewing_qty').val('".$row[csf('totalsewing')]."');\n";
			$yet_to_produced = $row[csf('totalinput')]-$row[csf('totalsewing')];
			echo "$('#txt_yet_to_sewing').val('".$yet_to_produced."');\n";
		}
		$dft_id=""; $alt_save_data=""; $spt_save_data=""; $altType_id=""; $sptType_id=""; $altpoint_id=""; $sptpoint_id=""; 
		$front_save_data="";$frontpoint_id="";$frontType_id=""; $bk_save_data="";$bktpoint_id="";$bktType_id=""; $wt_save_data="";$wttpoint_id="";$wttType_id="";
		 $me_save_data="";$metpoint_id="";$metType_id="";
		$defect_sql=sql_select("select id, po_break_down_id, defect_type_id, defect_point_id, defect_qty from pro_gmts_prod_dft where mst_id='".$result[csf('id')]."' and status_active=1 and is_deleted=0 and production_type='5'");
		//echo "select id, po_break_down_id, defect_type_id, defect_point_id, defect_qty from pro_gmts_prod_dft where mst_id='".$result[csf('id')]."' and status_active=1 and is_deleted=0 and production_type='5'";
		foreach($defect_sql as $dft_row)
		{
			if($dft_row[csf('defect_type_id')]==1)
			{
				if($alt_save_data=="") $front_save_data=$dft_row[csf('id')].'**'.$dft_row[csf('defect_point_id')].'**'.$dft_row[csf('defect_qty')]; else $alt_save_data.=','.$dft_row[csf('id')].'**'.$dft_row[csf('defect_point_id')].'**'.$dft_row[csf('defect_qty')];
				if($altpoint_id=="") $altpoint_id=$dft_row[csf('defect_point_id')]; else $altpoint_id.=','.$dft_row[csf('defect_point_id')];
				$altType_id=$dft_row[csf('defect_type_id')];
			}

			if($dft_row[csf('defect_type_id')]==2)
			{
				if($spt_save_data=="") $spt_save_data=$dft_row[csf('id')].'**'.$dft_row[csf('defect_point_id')].'**'.$dft_row[csf('defect_qty')]; else $spt_save_data.=','.$dft_row[csf('id')].'**'.$dft_row[csf('defect_point_id')].'**'.$dft_row[csf('defect_qty')];
				if($sptpoint_id=="") $sptpoint_id=$dft_row[csf('defect_point_id')]; else $sptpoint_id.=','.$dft_row[csf('defect_point_id')];
				$sptType_id=$dft_row[csf('defect_type_id')];
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
		}
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

		echo "$('#txt_mst_id').val('".$result[csf('id')]."');\n";
		echo "$('#txt_sys_chln').val('".$result[csf('id')]."');\n";
 		echo "set_button_status(1, permission, 'fnc_sewing_output_entry',1,1);\n";

		//break down of color and size------------------------------------------
 		//#############################################################################################//
		// order wise - color level, color and size level
		$color_library=return_library_array( "select id, color_name from lib_color",'id','color_name');
		$size_library=return_library_array( "select id, size_name from lib_size",'id','size_name');


		$control_and_preceding=sql_select("select is_control,preceding_page_id from variable_settings_production where status_active=1 and is_deleted=0 and variable_list=50 and page_category_id=5 and company_name=$company_id");
	    if(count($control_and_preceding)>0)
	    {
		  	$preceding_process = $control_and_preceding[0][csf("preceding_page_id")];
	    }

		$qty_source=0;
		if($preceding_process==11) $qty_source=68; //Attachment Complete
		else if($preceding_process==112) $qty_source=112; //Mending Complete
		else if($preceding_process==3) $qty_source=3; //Wash Complete

		$variableSettings = $result[csf('entry_break_down_type')];
		$variableSettingsRej = $result[csf('break_down_type_rej')];

		//$variableSettings=2;
		if($qty_source!=0)
		{
			if( $variableSettings!=1 ) // gross level
			{
				$po_id = $result[csf('po_break_down_id')];
				$item_id = $result[csf('item_number_id')];
				$country_id = $result[csf('country_id')];

				$sql_dtls = sql_select("SELECT color_size_break_down_id, production_qnty, reject_qty, size_number_id, color_number_id from  pro_garments_production_dtls a, wo_po_color_size_breakdown b where a.mst_id=$data and a.status_active=1 and a.color_size_break_down_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id'");
				foreach($sql_dtls as $row)
				{
					if( $variableSettings==2 ) $index = $row[csf('color_number_id')]; else $index = $row[csf('size_number_id')].$color_arr[$row[csf("color_number_id")]].$row[csf('color_number_id')];
				  	$amountArr[$index] = $row[csf('production_qnty')];
					$rejectArr[$index] = $row[csf('reject_qty')];
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
						$sql = "SELECT a.item_number_id, a.color_number_id, sum(a.order_quantity) as order_quantity, sum(a.plan_cut_qnty) as plan_cut_qnty,
								sum(CASE WHEN c.production_type=$qty_source then b.production_qnty ELSE 0 END) as production_qnty,
								sum(CASE WHEN c.production_type=5 then b.production_qnty ELSE 0 END) as cur_production_qnty,
								sum(CASE WHEN c.production_type=5 then b.reject_qty ELSE 0 END) as reject_qty
								from wo_po_color_size_breakdown a
								left join pro_garments_production_dtls b on a.id=b.color_size_break_down_id
								left join pro_garments_production_mst c on c.id=b.mst_id
								where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active=1 group by a.item_number_id, a.color_number_id";

					}
				}
				else if( $variableSettings==3 ) //color and size level
				{
					 $dtlsData = sql_select("SELECT a.color_size_break_down_id,
										sum(CASE WHEN a.production_type=$qty_source then a.production_qnty ELSE 0 END) as production_qnty,
										sum(CASE WHEN a.production_type=5 then a.production_qnty ELSE 0 END) as cur_production_qnty,
										sum(CASE WHEN a.production_type=5 then a.reject_qty ELSE 0 END) as reject_qty
										from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in($qty_source,5) group by a.color_size_break_down_id ");

					foreach($dtlsData as $row)
					{
						$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['iss']= $row[csf('production_qnty')];
						$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv']= $row[csf('cur_production_qnty')];
						$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rej']= $row[csf('reject_qty')];
					}

					$sql = "SELECT id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty
						from wo_po_color_size_breakdown
						where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 order by color_number_id, size_order";
				}
				else // by default color and size level
				{
					$dtlsData = sql_select("SELECT a.color_size_break_down_id,
										sum(CASE WHEN a.production_type=$qty_source then a.production_qnty ELSE 0 END) as production_qnty,
										sum(CASE WHEN a.production_type=5 then a.production_qnty ELSE 0 END) as cur_production_qnty,
										sum(CASE WHEN a.production_type=5 then a.reject_qty ELSE 0 END) as reject_qty
										from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in($qty_source,5) group by a.color_size_break_down_id");

					foreach($dtlsData as $row)
					{
						$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['iss']= $row[csf('production_qnty')];
						$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv']= $row[csf('cur_production_qnty')];
						$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rej']= $row[csf('reject_qty')];
					}
					$sql = "SELECT id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty
						from wo_po_color_size_breakdown
						where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 order by color_number_id,size_order";
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
	 			//print_r($sql);die;
				$colorHTML="";
				$colorID='';
				$chkColor = array();
				$i=0;$totalQnty=0;$colorWiseTotal=0;
				foreach($colorResult as $color)
				{
					if( $variableSettings==2 ) // color level
					{
						$amount = $amountArr[$color[csf("color_number_id")]];
						$rejectAmt = $rejectArr[$color[csf("color_number_id")]];
						$colorHTML .='<tr><td>'.$color_library[$color[csf("color_number_id")]].'</td><td><input type="text" name="txt_color" id="colSize_'.($i+1).'" style="width:60px"  class="text_boxes_numeric" placeholder="'.($color[csf("production_qnty")]-$color[csf("cur_production_qnty")]+$amount).'" value="'.$amount.'" onblur="fn_colorlevel_total('.($i+1).')" onkeypress="return isNumber(event)"></td><td><input type="text" name="txtColSizeRej" id="colSizeRej_'.($i+1).'" style="width:60px" class="text_boxes_numeric" placeholder="Rej." value="'.$rejectAmt.'" onblur="fn_colorRej_total('.($i+1).') '.$disable.'" onkeypress="return isNumber(event)"></td></tr>';
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

						$iss_qnty=$color_size_qnty_array[$color[csf('id')]]['iss'];
						$rcv_qnty=$color_size_qnty_array[$color[csf('id')]]['rcv'];
						$rej_qnty=$rejectArr[$index];
						//$color_size_qnty_array[$color[csf('id')]]['rej'];

						$colorHTML .='<tr><td>'.$size_library[$color[csf("size_number_id")]].'</td><td><input type="text" name="colorSize" id="colSize_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:80px" placeholder="'.($iss_qnty-$rcv_qnty+$amount).'" onblur="fn_total('.$color[csf("color_number_id")].','.($i+1).')" value="'.$amount.'" onkeypress="return isNumber(event)"><input type="text" name="colorSizeRej" id="colSizeRej_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:50px" placeholder="Rej. Qty" onblur="fn_total_rej('.$color[csf("color_number_id")].','.($i+1).')" value="'.$rej_qnty.'" '.$disable.' onkeypress="return isNumber(event)"><input type="text" name="colorSizePOQnty" id="colorSizePOQnty_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:70px" value="'.$color[csf("order_quantity")].'" readonly disabled></td></tr>';
						$colorWiseTotal += $amount;
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
		}
		else
		{
			if( $variableSettings!=1 ) // gross level
			{
				$po_id = $result[csf('po_break_down_id')];
				$item_id = $result[csf('item_number_id')];
				$country_id = $result[csf('country_id')];

				$sql_dtls = sql_select("select color_size_break_down_id, production_qnty, reject_qty, size_number_id, color_number_id from  pro_garments_production_dtls a, wo_po_color_size_breakdown b where a.mst_id=$data and a.status_active=1 and a.color_size_break_down_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id'");
				foreach($sql_dtls as $row)
				{
					if( $variableSettings==2 ) $index = $row[csf('color_number_id')]; else $index = $row[csf('size_number_id')].$color_arr[$row[csf("color_number_id")]].$row[csf('color_number_id')];
				  	$amountArr[$index] = $row[csf('production_qnty')];
					$rejectArr[$index] = $row[csf('reject_qty')];
				}

				if( $variableSettings==2 ) // color level
				{
					if($db_type==0)
					{

						$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, sum(plan_cut_qnty) as plan_cut_qnty, (select sum(CASE WHEN pdtls.color_size_break_down_id=wo_po_color_size_breakdown.id then pdtls.production_qnty ELSE 0 END) from pro_garments_production_dtls pdtls where pdtls.production_type=4 and pdtls.is_deleted=0 ) as production_qnty, (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.production_qnty ELSE 0 END) from pro_garments_production_dtls cur where cur.production_type=5 and cur.is_deleted=0 ) as cur_production_qnty, (select sum(CASE WHEN cur.color_size_break_down_id=wo_po_color_size_breakdown.id then cur.reject_qty ELSE 0 END) from pro_garments_production_dtls cur where cur.production_type=5 and cur.is_deleted=0 ) as reject_qty
						from wo_po_color_size_breakdown
						where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 group by color_number_id";
					}
					else
					{
						$sql = "select a.item_number_id, a.color_number_id, sum(a.order_quantity) as order_quantity, sum(a.plan_cut_qnty) as plan_cut_qnty,
								sum(CASE WHEN c.production_type=4 then b.production_qnty ELSE 0 END) as production_qnty,
								sum(CASE WHEN c.production_type=5 then b.production_qnty ELSE 0 END) as cur_production_qnty,
								sum(CASE WHEN c.production_type=5 then b.reject_qty ELSE 0 END) as reject_qty
								from wo_po_color_size_breakdown a
								left join pro_garments_production_dtls b on a.id=b.color_size_break_down_id
								left join pro_garments_production_mst c on c.id=b.mst_id
								where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active=1 group by a.item_number_id, a.color_number_id";

					}
				}
				else if( $variableSettings==3 ) //color and size level
				{
					 $dtlsData = sql_select("select a.color_size_break_down_id,
										sum(CASE WHEN a.production_type=4 then a.production_qnty ELSE 0 END) as production_qnty,
										sum(CASE WHEN a.production_type=5 then a.production_qnty ELSE 0 END) as cur_production_qnty,
										sum(CASE WHEN a.production_type=5 then a.reject_qty ELSE 0 END) as reject_qty
										from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in(4,5) group by a.color_size_break_down_id ");

					foreach($dtlsData as $row)
					{
						$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['iss']= $row[csf('production_qnty')];
						$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv']= $row[csf('cur_production_qnty')];
						$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rej']= $row[csf('reject_qty')];
					}

					$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty
						from wo_po_color_size_breakdown
						where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 order by color_number_id, size_order";
				}
				else // by default color and size level
				{
					$dtlsData = sql_select("select a.color_size_break_down_id,
										sum(CASE WHEN a.production_type=4 then a.production_qnty ELSE 0 END) as production_qnty,
										sum(CASE WHEN a.production_type=5 then a.production_qnty ELSE 0 END) as cur_production_qnty,
										sum(CASE WHEN a.production_type=5 then a.reject_qty ELSE 0 END) as reject_qty
										from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in(4,5) group by a.color_size_break_down_id");

					foreach($dtlsData as $row)
					{
						$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['iss']= $row[csf('production_qnty')];
						$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv']= $row[csf('cur_production_qnty')];
						$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rej']= $row[csf('reject_qty')];
					}
					$sql = "select id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty
						from wo_po_color_size_breakdown
						where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 order by color_number_id,size_order";
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
	 			//print_r($sql);die;
				$colorHTML="";
				$colorID='';
				$chkColor = array();
				$i=0;$totalQnty=0;$colorWiseTotal=0;
				foreach($colorResult as $color)
				{
					if( $variableSettings==2 ) // color level
					{
						$amount = $amountArr[$color[csf("color_number_id")]];
						$rejectAmt = $rejectArr[$color[csf("color_number_id")]];
						$colorHTML .='<tr><td>'.$color_library[$color[csf("color_number_id")]].'</td><td><input type="text" name="txt_color" id="colSize_'.($i+1).'" style="width:60px"  class="text_boxes_numeric" placeholder="'.($color[csf("production_qnty")]-$color[csf("cur_production_qnty")]+$amount).'" value="'.$amount.'" onblur="fn_colorlevel_total('.($i+1).')" onkeypress="return isNumber(event)"></td><td><input type="text" name="txtColSizeRej" id="colSizeRej_'.($i+1).'" style="width:60px" class="text_boxes_numeric" placeholder="Rej." value="'.$rejectAmt.'" onblur="fn_colorRej_total('.($i+1).') '.$disable.'" onkeypress="return isNumber(event)"></td></tr>';
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

						$iss_qnty=$color_size_qnty_array[$color[csf('id')]]['iss'];
						$rcv_qnty=$color_size_qnty_array[$color[csf('id')]]['rcv'];
						$rej_qnty=$rejectArr[$index];
						//$color_size_qnty_array[$color[csf('id')]]['rej'];

						$colorHTML .='<tr><td>'.$size_library[$color[csf("size_number_id")]].'</td><td><input type="text" name="colorSize" id="colSize_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:80px" placeholder="'.($iss_qnty-$rcv_qnty+$amount).'" onblur="fn_total('.$color[csf("color_number_id")].','.($i+1).')" value="'.$amount.'" onkeypress="return isNumber(event)"><input type="text" name="colorSizeRej" id="colSizeRej_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:50px" placeholder="Rej. Qty" onblur="fn_total_rej('.$color[csf("color_number_id")].','.($i+1).')" value="'.$rej_qnty.'" '.$disable.' onkeypress="return isNumber(event)"><input type="text" name="colorSizePOQnty" id="colorSizePOQnty_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:70px" value="'.$color[csf("order_quantity")].'" readonly disabled></td></tr>';
						$colorWiseTotal += $amount;
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
		}
	}
 	exit();
}

//pro_garments_production_mst
if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	//print_r($process);die;
	extract(check_magic_quote_gpc( $process ));
	$is_projected_po_allow=return_field_value("production_entry","variable_settings_production","variable_list=58 and company_name=$cbo_company_name");
	
	if($is_projected_po_allow ==2)
	{
		$is_projected_po=return_field_value("is_confirmed","wo_po_break_down","status_active in(1,2,3) and id=$hidden_po_break_down_id");
		if($is_projected_po==2)
		{			
			echo "786**Projected PO is not allowed to production. Please check variable settings";die();
		}
	}
	$is_control=return_field_value("is_control","variable_settings_production","company_name=$cbo_company_name and variable_list=33 and page_category_id=29");
	if(!str_replace("'","",$sewing_production_variable)) $sewing_production_variable=3;
	if ($operation==0) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }

		$id= return_next_id_by_sequence(  "pro_gar_production_mst_seq",   "pro_garments_production_mst", $con );
		$txt_challan_no=(str_replace("'", "", $txt_challan)==0)? $id : $txt_challan;
		$field_array1="id, garments_nature, company_id, challan_no, po_break_down_id, item_number_id, country_id, production_source, serving_company, location, produced_by, shift_name, production_date, production_quantity, production_type, entry_break_down_type, break_down_type_rej, sewing_line, supervisor, production_hour, remarks, floor_id, alter_qnty, reject_qnty, total_produced, yet_to_produced, prod_reso_allo, spot_qnty, wo_order_id, wo_order_no, currency_id, exchange_rate, rate, amount, inserted_by, insert_date";

		$rate=str_replace("'","",$hidden_piece_rate);
		if($rate!="") {  $amount=$rate*str_replace("'","",$txt_sewing_qty);}

		if($db_type==0)
		{
			$data_array1="(".$id.",".$garments_nature.",".$cbo_company_name.",".$txt_challan_no.",".$hidden_po_break_down_id.",".$cbo_item_name.",".$cbo_country_name.",".$cbo_source.",".$cbo_sewing_company.",".$cbo_location.",".$cbo_produced_by.",".$cbo_shift_name.",".$txt_sewing_date.",".$txt_sewing_qty.",5,".$sewing_production_variable.",".$sewing_production_variable_rej.",".$cbo_sewing_line.",".$txt_super_visor.",".$txt_reporting_hour.",".$txt_remark.",".$cbo_floor.",".$txt_alter_qnty.",".$txt_reject_qnty.",".$txt_cumul_sewing_qty.",".$txt_yet_to_sewing.",".$prod_reso_allo.",".$txt_spot_qnty.",".$txt_wo_id.",".$txt_wo_no.",".$hidden_currency_id.",".$hidden_exchange_rate.",'".$rate."','".$amount."',".$user_id.",'".$pc_date_time."')";
		}
		else
		{
			$txt_reporting_hour=str_replace("'","",$txt_sewing_date)." ".str_replace("'","",$txt_reporting_hour);
			$txt_reporting_hour="to_date('".$txt_reporting_hour."','DD MONTH YYYY HH24:MI:SS')";
			$data_array1="INSERT INTO pro_garments_production_mst (".$field_array1.") VALUES (".$id.",".$garments_nature.",".$cbo_company_name.",".$txt_challan_no.",".$hidden_po_break_down_id.",".$cbo_item_name.",".$cbo_country_name.",".$cbo_source.",".$cbo_sewing_company.",".$cbo_location.",".$cbo_produced_by.",".$cbo_shift_name.",".$txt_sewing_date.",".$txt_sewing_qty.",5,".$sewing_production_variable.",".$sewing_production_variable_rej.",".$cbo_sewing_line.",".$txt_super_visor.",".$txt_reporting_hour.",".$txt_remark.",".$cbo_floor.",".$txt_alter_qnty.",".$txt_reject_qnty.",".$txt_cumul_sewing_qty.",".$txt_yet_to_sewing.",".$prod_reso_allo.",".$txt_spot_qnty.",".$txt_wo_id.",".$txt_wo_no.",".$hidden_currency_id.",".$hidden_exchange_rate.",'".$rate."','".$amount."',".$user_id.",'".$pc_date_time."')";
		}


		$field_array="id, mst_id, production_type, color_size_break_down_id, production_qnty, reject_qty,color_type_id";


		$dtlsData = sql_select("select a.color_size_break_down_id,
										sum(CASE WHEN a.production_type=4 then a.production_qnty ELSE 0 END) as production_qnty,
										sum(CASE WHEN a.production_type=5 then a.production_qnty ELSE 0 END) as cur_production_qnty
										from pro_garments_production_dtls a,pro_garments_production_mst b
										where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id=$hidden_po_break_down_id and b.item_number_id=$cbo_item_name and b.country_id=$cbo_country_name and a.color_size_break_down_id!=0 and a.production_type in(4,5)
										group by a.color_size_break_down_id");
		$color_pord_data=array();
		foreach($dtlsData as $row)
		{
			$color_pord_data[$row[csf("color_size_break_down_id")]]=$row[csf('production_qnty')]-$row[csf("cur_production_qnty")];
		}

		if(str_replace("'","",$sewing_production_variable)==2)//color level wise
		{
			$color_sizeID_arr=sql_select( "select id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name   and status_active=1 and is_deleted=0 order by id" );
			$colSizeID_arr=array();
			foreach($color_sizeID_arr as $val){
				$index = $val[csf("color_number_id")];
				$colSizeID_arr[$index]=$val[csf("id")];
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
					if($j==0)$data_array = "(".$dtls_id.",".$id.",5,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."','".$rejQtyArr[$colorSizeNumberIDArr[0]]."',".$cbo_color_type.")";
					else $data_array .= ",(".$dtls_id.",".$id.",5,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."','".$rejQtyArr[$colorSizeNumberIDArr[0]]."',".$cbo_color_type.")";
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

			$color_sizeID_arr=sql_select( "select id,size_number_id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name and status_active=1 and is_deleted=0 order by size_number_id,color_number_id" );
			$colSizeID_arr=array();
			foreach($color_sizeID_arr as $val){
				$index = $val[csf("size_number_id")].$color_arr[$val[csf("color_number_id")]].$val[csf("color_number_id")];
				$colSizeID_arr[$index]=$val[csf("id")];
			}

			//	colorIDvalue concate as sizeID*colorID*value***sizeID*colorID*value	--------------------------//
			$rowExRej = array_filter(explode("***",$colorIDvalueRej));
			foreach($rowExRej as $rowR=>$valR)
			{
				$colorAndSizeRej_arr = explode("*",$valR);
				$sizeID = $colorAndSizeRej_arr[0];
				$colorID = $colorAndSizeRej_arr[1];
				$colorSizeRej = $colorAndSizeRej_arr[2];
				$index = $sizeID.$color_arr[$colorID].$colorID;
				$rejQtyArr[$index]=$colorSizeRej;
			}

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

				/*if($is_control==1 && $user_level!=2)
				{
					if($colorSizeValue>0)
					{
						if(($colorSizeValue*1)>($color_pord_data[$colSizeID_arr[$index]]*1))
						{
							echo "35**Production Quantity Not Over Sewing Input Qnty";
							//check_table_status( $_SESSION['menu_id'],0);
							disconnect($con);
							die;
						}
					}
				}*/

				if($colSizeID_arr[$index]!="")
				{
				//4 for Sewing Input Entry
					$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",  "pro_garments_production_dtls", $con );
					if($j==0)$data_array = "(".$dtls_id.",".$id.",5,'".$colSizeID_arr[$index]."','".$colorSizeValue."','".$rejQtyArr[$index]."',".$cbo_color_type.")";
					else $data_array .= ",(".$dtls_id.",".$id.",5,'".$colSizeID_arr[$index]."','".$colorSizeValue."','".$rejQtyArr[$index]."',".$cbo_color_type.")";
					//$dtls_id=$dtls_id+1;
	 				$j++;
	 			}
	 			else
	 			{
	 				echo "420**";die();
	 			}
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
		//echo "10**".$rID.'='.$dtlsrID.'='.$defectme.'='.$defectWt.'='.$defectBk.'='.$defectFront;die;
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

		if($db_type==2 || $db_type==1 )
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
		$colorIdArrays=explode("***", $colorIDvalue);
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
			
			if($db_type == 0){
			
				$sql = "SELECT a.production_date,a.prod_reso_allo,a.po_break_down_id,a.challan_no, a.serving_company,a.location,d.grouping ,f.style_ref_no,d.po_number,a.item_number_id,a.floor_id,b.color_type_id ,c.color_number_id,a.sewing_line,a.country_id,$date_cond,sum(b.production_qnty) as production_qnty,c.size_number_id
				from pro_garments_production_mst a, pro_garments_production_dtls b, wo_po_color_size_breakdown c, wo_po_break_down d,wo_po_details_master f 
				where a.id=b.mst_id and b.color_size_break_down_id=c.id and c.po_break_down_id=d.id and d.id=a.po_break_down_id and d.job_no_mst=f.job_no and a.production_type=5 and a.entry_break_down_type=3 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1   and a.id=$txt_mst_id  $size_number_con $color_num_con $item_con $line_con
				group by a.production_date,a.prod_reso_allo,a.po_break_down_id,a.challan_no,a.serving_company,a.location,f.style_ref_no,d.po_number,a.item_number_id,a.floor_id,b.color_type_id ,c.color_number_id,a.sewing_line,a.country_id,production_hour,c.size_number_id,d.grouping";
			}else if($db_type == 2){
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

 		$field_array1="production_source*serving_company*location*produced_by*shift_name*production_date*production_quantity*production_type*entry_break_down_type* break_down_type_rej*sewing_line*supervisor*production_hour*challan_no*remarks*floor_id*reject_qnty*alter_qnty*total_produced*yet_to_produced*prod_reso_allo*spot_qnty*wo_order_id*wo_order_no*currency_id*exchange_rate *rate*amount*updated_by*update_date";
		$rate=str_replace("'","",$hidden_piece_rate);
		if($rate!="") {  $amount=$rate*str_replace("'","",$txt_sewing_qty);}
		else {$amount="";}

		$data_array1="".$cbo_source."*".$cbo_sewing_company."*".$cbo_location."*".$cbo_produced_by."*".$cbo_shift_name."*".$txt_sewing_date."*".$txt_sewing_qty."*5*".$sewing_production_variable."*".$sewing_production_variable_rej."*".$cbo_sewing_line."*".$txt_super_visor."*".$txt_reporting_hour."*".$txt_challan."*".$txt_remark."*".$cbo_floor."*".$txt_reject_qnty."*".$txt_alter_qnty."*".$txt_cumul_sewing_qty."*".$txt_yet_to_sewing."*".$prod_reso_allo."*".$txt_spot_qnty."*".$txt_wo_id."*".$txt_wo_no."*".$hidden_currency_id."*".$hidden_exchange_rate."*'".$rate."'*'".$amount."'*".$user_id."*'".$pc_date_time."'";

		// pro_garments_production_dtls table data entry here
		if(str_replace("'","",$sewing_production_variable)!=1 && str_replace("'","",$txt_mst_id)!='' ) // check is not gross level
		{

			$dtlsData = sql_select("select a.color_size_break_down_id,
										sum(CASE WHEN a.production_type=4 then a.production_qnty ELSE 0 END) as production_qnty,
										sum(CASE WHEN a.production_type=5 then a.production_qnty ELSE 0 END) as cur_production_qnty
										from pro_garments_production_dtls a,pro_garments_production_mst b
										where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id=$hidden_po_break_down_id and b.item_number_id=$cbo_item_name and b.country_id=$cbo_country_name and a.color_size_break_down_id!=0 and a.production_type in(4,5) and b.id!=$txt_mst_id
										group by a.color_size_break_down_id");
			$color_pord_data=array();
			foreach($dtlsData as $row)
			{
				$color_pord_data[$row[csf("color_size_break_down_id")]]=$row[csf('production_qnty')]-$row[csf("cur_production_qnty")];
			}


 			$field_array="id, mst_id, production_type, color_size_break_down_id, production_qnty, reject_qty,color_type_id";

			if(str_replace("'","",$sewing_production_variable)==2)//color level wise
			{
				$color_sizeID_arr=sql_select( "select id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name   and status_active=1 and is_deleted=0 order by id" );
				$colSizeID_arr=array();
				foreach($color_sizeID_arr as $val){
					$index = $val[csf("color_number_id")];
					$colSizeID_arr[$index]=$val[csf("id")];
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
					/*if($is_control==1 && $user_level!=2)
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
					}*/
					if($colSizeID_arr[$colorSizeNumberIDArr[0]]!="")
					{
						//4 for Sewing Input Entry
						$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",  "pro_garments_production_dtls", $con );
						if($j==0)$data_array = "(".$dtls_id.",".$txt_mst_id.",5,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."','".$rejQtyArr[$colorSizeNumberIDArr[0]]."',".$cbo_color_type.")";
						else $data_array .= ",(".$dtls_id.",".$txt_mst_id.",5,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."','".$rejQtyArr[$colorSizeNumberIDArr[0]]."',".$cbo_color_type.")";
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

				$color_sizeID_arr=sql_select( "select id,size_number_id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name and status_active=1 and is_deleted=0 order by size_number_id,color_number_id" );
				$colSizeID_arr=array();
				foreach($color_sizeID_arr as $val){
					$index = $val[csf("size_number_id")].$color_arr[$val[csf("color_number_id")]].$val[csf("color_number_id")];
					$colSizeID_arr[$index]=$val[csf("id")];
				}

				//	colorIDvalue concate as sizeID*colorID*value***sizeID*colorID*value	--------------------------//
				$rowExRej = array_filter(explode("***",$colorIDvalueRej));
				foreach($rowExRej as $rowR=>$valR)
				{
					$colorAndSizeRej_arr = explode("*",$valR);
					$sizeID = $colorAndSizeRej_arr[0];
					$colorID = $colorAndSizeRej_arr[1];
					$colorSizeRej = $colorAndSizeRej_arr[2];
					$index = $sizeID.$color_arr[$colorID].$colorID;
					$rejQtyArr[$index]=$colorSizeRej;
				}

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

					/*if($is_control==1 && $user_level!=2)
					{
						if($colorSizeValue>0)
						{
							if(($colorSizeValue*1)>($color_pord_data[$colSizeID_arr[$index]]*1))
							{
								echo "35**Production Quantity Not Over Sewing Input Qnty";
							//	check_table_status( $_SESSION['menu_id'],0);
								disconnect($con);
								die;
							}
						}
					}*/

					if($colSizeID_arr[$index]!="")
					{
						//4 for Sewing Input Entry
						$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",   "pro_garments_production_dtls", $con );
						if($j==0)$data_array = "(".$dtls_id.",".$txt_mst_id.",5,'".$colSizeID_arr[$index]."','".$colorSizeValue."','".$rejQtyArr[$index]."',".$cbo_color_type.")";
						else $data_array .= ",(".$dtls_id.",".$txt_mst_id.",5,'".$colSizeID_arr[$index]."','".$colorSizeValue."','".$rejQtyArr[$index]."',".$cbo_color_type.")";
						//$dtls_id=$dtls_id+1;
						$j++;
					}
					else
					{
						echo "420**";die();
					}
				}
			}
 			//$dtlsrID=sql_insert("pro_garments_production_dtls",$field_array,$data_array,1);
		}//end cond
		//echo $data_array; die;

		$dtlsrDelete = execute_query("delete from pro_garments_production_dtls where mst_id=$txt_mst_id",0);
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
		if($db_type==2 || $db_type==1 )
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

function sql_insert2( $strTable, $arrNames, $arrValues, $commit, $contain_lob )
{
	global $con ;
	if($contain_lob=="") $contain_lob=0;
	if( $contain_lob==0)
	{
		$tmpv=explode(")",$arrValues);
		if(count($tmpv)>2)
			$strQuery= "INSERT ALL \n";
		else
			$strQuery= "INSERT  \n";

		for($i=0; $i<count($tmpv)-1; $i++)
		{
			if( strpos(trim($tmpv[$i]), ",")==0)
				$tmpv[$i]=substr_replace($tmpv[$i], " ", 0, 1);
			$strQuery .=" INTO ".$strTable." (".$arrNames.") values ".$tmpv[$i].") \n";
		}

	   if(count($tmpv)>2) $strQuery .= "SELECT * FROM dual";
	 //return $strQuery ;
	}
	else
	{
		$tmpv=explode(")",$arrValues);

		for($i=0; $i<count($tmpv)-1; $i++)
		{
			$strQuery="";
			$strQuery= "INSERT  \n";
			if( strpos(trim($tmpv[$i]), ",")==0)
				$tmpv[$i]=substr_replace($tmpv[$i], " ", 0, 1);
			$strQuery .=" INTO ".$strTable." (".$arrNames.") values ".$tmpv[$i].") \n";
			//return $strQuery ;
			$stid =  oci_parse($con, $strQuery);
			$exestd=oci_execute($stid,OCI_NO_AUTO_COMMIT);
			if (!$exestd) return "0";
		}
		return "1";

	}
  	//return  $strQuery; die;
	echo $strQuery;die;
	//$_SESSION['last_query']=$_SESSION['last_query'].";;".$strQuery;



	$stid =  oci_parse($con, $strQuery);
	$exestd=oci_execute($stid,OCI_NO_AUTO_COMMIT);


	if (!oci_error($exestd))
	{
		user_activities($exestd);
		
		/*		$pc_time= add_time(date("H:i:s",time()),360);
				$pc_date_time = date("d-M-Y h:i:s",strtotime(add_time(date("H:i:s",time()),360)));
				$pc_date = date("d-M-Y",strtotime(add_time(date("H:i:s",time()),360)));
		
				$strQuery= "INSERT INTO activities_history ( session_id,user_id,ip_address,entry_time,entry_date,module_name,form_name,query_details,query_type) VALUES ('".$_SESSION['logic_erp']["history_id"]."','".$_SESSION['logic_erp']["user_id"]."','".$_SESSION['logic_erp']["pc_local_ip"]."','".$pc_date_time."','".$pc_date."','".$_SESSION["module_id"]."','".$_SESSION['menu_id']."','".encrypt($_SESSION['last_query'])."','0')";
				$resultss=oci_parse($con, $strQuery);
				oci_execute($resultss,OCI_NO_AUTO_COMMIT);
				$_SESSION['last_query']="";
		*/	

	}
	
	//echo $strQuery;die;
	

	if ($exestd)
		return "1";
	else
		return "0";
	die;

}
if($action=="sewing_output_print")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	//print_r ($data);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name"  );
	$buyer_library=return_library_array( "select id, short_name from   lib_buyer", "id", "short_name"  );
	// $sewing_library=return_library_array( "select id, line_name from  lib_sewing_line", "id", "line_name"  );
	$country_arr=return_library_array( "select id, country_name from  lib_country", "id", "country_name"  );
	$line_data_variable=return_library_array("select id, line_number from prod_resource_mst", "id","line_number");

	$lineArr = return_library_array("select id,line_name from lib_sewing_line order by id","id","line_name"); 
	$prod_reso_line_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');

	$job_array=array();
	$job_sql="SELECT a.job_no, a.buyer_name, a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
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
		$sql="SELECT id, company_id, challan_no, sewing_line, po_break_down_id, item_number_id,entry_break_down_type,break_down_type_rej, country_id, production_source, produced_by, serving_company, location, embel_name, embel_type, production_date, TO_CHAR(production_hour,'HH24:MI') as production_hour, production_quantity, production_type, remarks, floor_id, sewing_line, alter_qnty, reject_qnty, spot_qnty,prod_reso_allo from pro_garments_production_mst where production_type=5 and id='$data[1]' and status_active=1 and is_deleted=0 ";
	}
	else
	{
		$sql="SELECT id, company_id, challan_no, sewing_line, po_break_down_id, item_number_id, country_id, entry_break_down_type,break_down_type_rej,production_source, produced_by, serving_company, location, embel_name, embel_type, production_date, TIME_FORMAT( production_hour, '%H:%i' ) as production_hour, production_quantity, production_type, remarks, floor_id, sewing_line, alter_qnty, reject_qnty, spot_qnty,prod_reso_allo from pro_garments_production_mst where production_type=5 and id='$data[1]' and status_active=1 and is_deleted=0 ";
	}
	//echo $sql;
	$sql_color_type=sql_select("SELECT color_type_id from pro_garments_production_dtls where status_active=1 and is_deleted=0 and mst_id='$data[1]' and production_type=5");
	$color_type_id=$sql_color_type[0][csf("color_type_id")];
	$dataArray=sql_select($sql);
	$break_down_type_reject=$dataArray[0][csf('break_down_type_rej')];
	$entry_break_down_type=$dataArray[0][csf('entry_break_down_type')];
	//echo $entry_break_down_type.'='.$entry_break_down_type;
?>
<div style="width:930px;">
    <table width="900" cellspacing="0" align="right">
        <tr>
            <td colspan="6" align="center" style="font-size:22px"><strong><? echo $company_library[$data[0]]; ?></strong></td>
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
            <td><strong>QC Pass Qty:</strong></td><td><? echo $dataArray[0][csf('production_quantity')]; ?></td>
        </tr>
        <tr>
            <td><strong>Source:</strong></td><td><? echo $knitting_source[$dataArray[0][csf('production_source')]]; ?></td>
            <td><strong>Input Date:</strong></td><td><? echo change_date_format($dataArray[0][csf('production_date')]); ?></td>
        </tr>
        <tr>
        	<? 
        	if($dataArray[0][csf('prod_reso_allo')]==1)
        	{

        		$line_resource_mst_arr=explode(",",$prod_reso_line_arr[$dataArray[0][csf('sewing_line')]]);
				$line_name="";
				foreach($line_resource_mst_arr as $resource_id)
				{
					$line_name.=$lineArr[$resource_id].",";
				}
        	 ?>
            <td><strong>Sewing Line: </strong></td><td><? echo chop($line_name,","); ?></td>
        	<? 
    		}
        	else
        	{ ?>
        	<td><strong>Sewing Line: </strong></td><td><? echo $lineArr[$dataArray[0][csf('sewing_line')]]; ?></td>
        	<? 
    		} 
    		?>
            <td><strong>Reporting Hour:</strong></td><td><? echo $dataArray[0][csf('production_hour')]; ?></td>
            <td><strong>Challan No:</strong></td><td><? echo $dataArray[0][csf('challan_no')]; ?></td>
        </tr>
        <tr>
            <td><strong>Alter Qty: </strong></td><td><? echo $dataArray[0][csf('alter_qnty')]; ?></td>
            <td><strong>Spot Qty:</strong></td><td><? echo $dataArray[0][csf('spot_qnty')]; ?></td>
            <td><strong>Color Type:</strong></td>
        	<td><? echo $color_type[$color_type_id]; ?></td>
        </tr>
        <tr>
        	<td><strong>System Challan: </strong></td><td><? echo $dataArray[0][csf('id')]; ?></td>
        	<td><strong>Produced By: </strong></td><td><? echo $worker_type[$dataArray[0][csf('produced_by')]]; ?></td>

        </tr>
        <tr>
            <td colspan="6"><strong><p>Remarks:  <? echo $dataArray[0][csf('remarks')]; ?></p></strong></td>
        </tr>
        <tr>
         <?
		if($entry_break_down_type==1)
		{
		?>

        	<td colspan="3" ><strong>Receive Qnty :  <? echo $dataArray[0][csf('production_quantity')]; ?></strong></td>
       <?
		}
		 if($break_down_type_reject==1)
		{
  ?>
            <td colspan="3" ><strong>Reject Qnty:  <? echo $dataArray[0][csf('reject_qnty')]; ?></strong></td>
       <? }
		   else
		 { ?>
			   <td colspan="6">&nbsp;</td>
	<?   }
	 ?>
        </tr>
    </table>
    <br>
        <?
		if($entry_break_down_type!=1)
		{
			$mst_id=$dataArray[0][csf('id')];
			$po_break_id=$dataArray[0][csf('po_break_down_id')];
			$sql="SELECT sum(a.production_qnty) as production_qnty, b.color_number_id, b.size_number_id from pro_garments_production_dtls a, wo_po_color_size_breakdown b where a.mst_id='$mst_id' and b.po_break_down_id='$po_break_id' and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by  b.size_number_id, b.color_number_id ";
			//echo $sql;
			$result=sql_select($sql);
			$size_array=array ();
			$qun_array=array ();
			foreach ( $result as $row )
			{
				$size_array[$row[csf('size_number_id')]]=$row[csf('size_number_id')];
				$qun_array[$row[csf('color_number_id')]][$row[csf('size_number_id')]]=$row[csf('production_qnty')];
			}

			$sql="SELECT sum(a.production_qnty) as production_qnty, b.color_number_id from pro_garments_production_dtls a, wo_po_color_size_breakdown b where a.mst_id='$mst_id' and b.po_break_down_id='$po_break_id' and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.color_number_id ";
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
    <table align="right" cellspacing="0" width="900"  border="1" rules="all" class="rpt_table" >
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
        $mst_id=$dataArray[0][csf('id')];
		$po_break_id=$dataArray[0][csf('po_break_down_id')];
		$sql="SELECT sum(a.production_qnty) as production_qnty,sum(reject_qty) as reject_qty ,	 b.color_number_id, b.size_number_id from pro_garments_production_dtls a, wo_po_color_size_breakdown b where a.mst_id='$mst_id' and b.po_break_down_id='$po_break_id' and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by  b.size_number_id, b.color_number_id ";
		//echo $sql;
		$result=sql_select($sql);
		$size_array=array ();
		$qun_array=array ();$reject_qun_array=array();
		foreach ( $result as $row )
		{
			$size_array[$row[csf('size_number_id')]]=$row[csf('size_number_id')];
			//$qun_array[$row[csf('color_number_id')]][$row[csf('size_number_id')]]=$row[csf('production_qnty')];
			$reject_qun_array[$row[csf('color_number_id')]][$row[csf('size_number_id')]]=$row[csf('reject_qty')];
		}

			$sql="SELECT sum(a.production_qnty) as production_qnty, b.color_number_id from pro_garments_production_dtls a, wo_po_color_size_breakdown b where a.mst_id='$mst_id' and b.po_break_down_id='$po_break_id' and a.color_size_break_down_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.color_number_id ";
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
             <div style="margin-left:30px;"><strong> Reject Qty.</strong></div>
    <table align="right" cellspacing="0" width="900"  border="1" rules="all" class="rpt_table" >
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
            echo signature_table(29, $data[0], "900px");
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
		$piece_sql="select a.sys_number, sum(b.wo_qty) as wo_qty from piece_rate_wo_mst a, piece_rate_wo_dtls b where a.id=b.mst_id and b.order_id=$ex_data[0] and b.item_id=$ex_data[1] and a.rate_for=30 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.sys_number";
	}
	else if($db_type==2)
	{
		$piece_sql="select a.sys_number, sum(b.wo_qty) as wo_qty from piece_rate_wo_mst a, piece_rate_wo_dtls b where a.id=b.mst_id and b.order_id=$ex_data[0] and b.item_id=$ex_data[1] and a.rate_for=30 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.sys_number";
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
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	$caption_name="";
	if($type==1) $caption_name="Alter Qty";
	else if($type==2) $caption_name="Spot Qty";
	else if($type==4) $caption_name="Front Check Qty";
	else if($type==5) $caption_name="Back Part Check Qty";
	else if($type==6) $caption_name="WestBand Check Qty";
	else if($type==7) $caption_name="Measurement Check Qty";
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
			<? //echo load_freeze_divs ("../../../",$permission,1); ?>
            <fieldset style="width:350px;">
                <input type="hidden" name="save_string" id="save_string" class="text_boxes" value="<? echo $save_data; ?>">
                <input type="hidden" name="tot_defectQnty" id="tot_defectQnty" class="text_boxes" value="<? echo $defect_qty; ?>">
                <input type="hidden" name="all_defect_id" id="all_defect_id" class="text_boxes" value="<? echo $all_defect_id; ?>">
                <input type="hidden" name="defect_type_id" id="defect_type_id" class="text_boxes" value="<? echo $type; ?>">
			<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="340">
            	<thead>
                	<tr><th colspan="3"><? echo $caption_name; ?></th></tr>
                	<tr><th width="40">SL</th><th width="150">Defect Name</th><th>Defect Qty</th></tr>
                </thead>
            </table>
            <div style="width:340px; max-height:300px; overflow-y:scroll" id="list_container" align="left">
                <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="320" id="tbl_list_search">
				 <tbody>
                    <?
					if($type==1)
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
					else if($type==2)
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
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
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
