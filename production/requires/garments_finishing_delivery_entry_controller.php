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


//========== user credential end ==========//------------------------------------------------------------------------------------------------------
$sqlCountry=sql_select("select id, country_name, short_name from lib_country");
$country_library=array(); $country_short_name=array();
foreach($sqlCountry as $crow)
{
	$country_library[$crow[csf("id")]]=$crow[csf("country_name")];
	$country_short_name[$crow[csf("id")]]=$crow[csf("short_name")];
}
unset($sqlCountry);
//$country_library=return_library_array( "select id,country_name from lib_country", "id", "country_name"  );


$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
//$country_short_name=return_library_array( "select id,short_name from lib_country", "id", "short_name"  );
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
	
	echo "$('#finish_production_variable_rej').val(0);\n";
	$sql_result_rej = sql_select("select finishing_update from variable_settings_production where company_name=$data and variable_list=28 and status_active=1");
 	foreach($sql_result_rej as $result)
	{
		echo "$('#finish_production_variable_rej').val(".$result[csf("finishing_update")].");\n";
		
		
		if($result[csf("finishing_update")]==3) //Color and Size
		{
				echo "$('#txt_reject_qnty').attr('readonly','readonly');\n";
		}
		else
		{
			echo "$('#txt_reject_qnty').removeAttr('readonly','readonly');\n";	
		}
	}
	$variable_is_control=return_field_value("is_control","variable_settings_production","company_name=$data and variable_list=33 and page_category_id=269","is_control");
	echo "document.getElementById('variable_is_controll').value='".$variable_is_control."';\n";
	
	$variable_qty_source_packing=return_field_value("qty_source_packing","variable_settings_production","company_name=$data and variable_list=43","qty_source_packing");
	if($variable_qty_source_packing=='') $variable_qty_source_packing=1;
	
	echo "document.getElementById('txt_qty_source').value=".$variable_qty_source_packing.";\n";
	
 	exit();
}
if ($action=="load_drop_down_working_company")
{
	echo create_drop_down( "cbo_location", 167, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' $location_credential_cond order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/garments_finishing_delivery_entry_controller', this.value, 'load_drop_down_floor', 'floor_td' );",0 );     	 
}
if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location", 167, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' $location_credential_cond order by location_name","id,location_name", 1, "-- Select Location --", $selected, "load_drop_down( 'requires/garments_finishing_delivery_entry_controller', this.value, 'load_drop_down_floor', 'floor_td' );",0 );     	 
}
if ($action=="load_drop_down_floor")
{
	//echo "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' and production_process in (11) order by floor_name";die;
	echo create_drop_down( "cbo_floor", 170, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' and production_process in (11) order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "",0 );     	 
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
 		echo create_drop_down( "cbo_finish_company", 170, "select id,company_name from lib_company where is_deleted=0 and status_active=1 $company_credential_cond order by company_name","id,company_name", 1, "--- Select ---", "", "fnc_company_check(document.getElementById('cbo_source').value);load_drop_down( 'requires/garments_finishing_delivery_entry_controller', this.value, 'load_drop_down_location', 'location_td' );",0,0 ); 
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
	        <table ellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
	            <thead>
	                <tr>
	                    <th>Company</th>
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
	                    echo create_drop_down( "cbo_company_name", 170, "select company_name,id from lib_company comp where is_deleted=0  and status_active=1 order by company_name",'id,company_name', 1, '--- Select Company ---',$company, "load_drop_down( 'garments_finishing_delivery_entry_controller', this.value, 'load_drop_down_location2', 'location_td' );");?>
	                    </td>
	                    
	                    <!-- <td>
	                    <?
	                    echo create_drop_down( "cbo_buyer_name", 120, "select id,buyer_name from lib_buyer where is_deleted=0 and status_active=1 order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "","",0 );
	                    ?>
	                    </td> -->
	                    <td>
	                    <? echo create_drop_down( "cbo_source", 170, $knitting_source,"", 1, "-- Select Source --", $selected, "fnc_company_check(this.value);load_drop_down( 'requires/garments_finishing_delivery_entry_controller', this.value+'**'+$('#cbo_company_name').val(), 'load_drop_down_source', 'finishing_td' );dynamic_must_entry_caption(this.value);", 0, '1,3' ); ?>
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

	                    <input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view( document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_source').value+'_'+document.getElementById('txt_system_no').value+'_'+document.getElementById('txt_order_no').value+'_'+document.getElementById('txt_job_no').value, 'create_system_number_list_view', 'search_div', 'garments_finishing_delivery_entry_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:80px;" />
	                    </td>
	                </tr>
	            </tbody>
	            <tfoot>
	                <tr>
	                    <td align="center" valign="middle" colspan="9" style="background-image: -moz-linear-gradient(bottom, rgb(136,170,214) 7%, rgb(194,220,255) 10%, rgb(136,170,214) 96%);">
	                    <?=load_month_buttons(1);  ?>
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
	exit();
}

if($action=="create_system_number_list_view")
{
 	$ex_data = explode("_",$data);
    $company = $ex_data[0];
    $txt_date_from = $ex_data[1];
	$txt_date_to = $ex_data[2];
    $source = $ex_data[3];
    // $buyer_id = $ex_data[4];
	$system_no = $ex_data[4];
	$order_no = $ex_data[5];
	$job_no = $ex_data[6];
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
	if(trim($order_no)!="")
	{
		$sql_cond = " and b.po_number like '%".trim($order_no)."'";
	}
	if(trim($job_no)!="")
	{
		$sql_cond = " and c.job_no like '%".trim($job_no)."'";
	}
	if(trim($company)!='0')
	{
		$sql_cond .= " and a.company_id='$company'";
	}
	if(trim($source)!='0')
	{
		$sql_cond .= " and a.production_source='$source'";
	}
	$sql ="SELECT a.id, a.sys_number, a.company_id, a.floor_id, a.working_company_id, a.location_id, a.production_source,a.delivery_date,b.po_number,c.job_no from pro_gmts_delivery_mst a, wo_po_break_down b, wo_po_details_master c,pro_garments_production_mst d
	where  b.job_no_mst=C.job_no
	and b.id=d.po_break_down_id
	and a.id=d.delivery_mst_id
	and a.status_active=1 and a.is_deleted=0 
	and b.status_active=1 and b.is_deleted=0 
	and c.status_active=1 and c.is_deleted=0 
	and d.status_active=1 and d.is_deleted=0 
	and a.production_type=14 
	and a.entry_form=463 
	$sql_cond group by a.id, a.sys_number, a.company_id, a.floor_id, a.working_company_id, a.location_id, a.production_source,a.delivery_date,b.po_number,c.job_no order by a.id DESC";
	// echo $sql;//die();
	foreach($dataArray as $row)
	{
		$job_no=return_field_value("h.job_no"," wo_po_break_down f, wo_po_details_master h","f.job_no_mst=h.job_no and f.id=".$row[csf("po_break_down_id")],"job_no");
	}
	$arr=array(2=>$company_arr,3=>$knitting_source,4=>$company_arr,5=>$location_arr);
	echo create_list_view("list_view", "System Number,Delivery Date,Company,Source,Fin. Company,Location,Order No,Job No","120,80,100,100,100,80,100,100","880","340",0, $sql , "js_set_value","id,sys_number,company_id", "",1, "0,0,company_id,production_source,working_company_id,location_id", $arr,"sys_number,delivery_date,company_id,production_source,working_company_id,location_id,po_number,job_no", "","setFilterGrid('list_view',-1)","0,3,0,0,0,0") ;
	exit();
}
if($action=="populate_mst_form_data")
{
	$sql ="SELECT a.id, a.sys_number, a.company_id, a.delivery_date, a.floor_id,a.working_company_id,a.location_id,a.remarks,a.production_source from pro_gmts_delivery_mst a where a.id='$data' and production_type=14";

	//echo $sql.";\n";
	$result =sql_select($sql);
	
	echo"load_drop_down( 'requires/garments_finishing_delivery_entry_controller', ".$result[0][csf('production_source')].", 'load_drop_down_source', 'cbo_finish_company' );\n";
	echo"load_drop_down( 'requires/garments_finishing_delivery_entry_controller', ".$result[0][csf('working_company_id')].", 'load_drop_down_location', 'cbo_location' );\n";

	echo"load_drop_down( 'requires/garments_finishing_delivery_entry_controller', ".$result[0][csf('location_id')].", 'load_drop_down_floor', 'cbo_floor' );\n";
	// echo"load_drop_down( 'requires/garments_finishing_delivery_entry_controller', ".$result[0][csf('location_id')].", 'load_drop_down_working_floor', 'working_floor' );\n";

	echo "$('#txt_system_no').val('".$result[0][csf('sys_number')]."');\n";
	echo "$('#txt_system_id').val('".$result[0][csf('id')]."');\n";
	echo "$('#txt_mst_id').val('".$result[0][csf('id')]."');\n";
	echo "$('#cbo_company_name').val('".$result[0][csf('company_id')]."');\n";
	echo "$('#cbo_finish_company').val('".$result[0][csf('working_company_id')]."');\n";
	echo "$('#cbo_location').val('".$result[0][csf('location_id')]."');\n";
	echo "$('#cbo_floor').val('".$result[0][csf('floor_id')]."');\n";
	echo "$('#txt_delivery_date').val('".change_date_format($result[0][csf('delivery_date')])."');\n";
	echo "$('#cbo_source').val('".$result[0][csf('production_source')]."');\n";
	echo "$('#txt_remark').val('".$result[0][csf('remarks')]."');\n";
	echo "set_button_status(0, permission, 'fnc_finishing_entry',1,0);\n";
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
			// alert(str); 
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
	
	function js_set_value(id,po_no,po_qnty,style,job,buyer,item_id,country_id)
	{
		$("#hidden_mst_id").val(id);
		$("#hidden_grmtItem_id").val(item_id); 
		$("#hidden_po_qnty").val(po_qnty);
		$("#hidden_po_no").val(po_no);
		$("#hidden_country_id").val(country_id);
		$("#hidden_job_no").val(job);
		$("#hidden_style_no").val(style);
		$("#hidden_buyer").val(buyer);
  		parent.emailwindow.hide();
 	}
    </script>
	</head>
	<body>
	<div align="center" style="width:100%;" >
	    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
       <table width="780" cellspacing="0" cellpadding="0" class="rpt_table" align="center" border="1" rules="all">
             <thead>                	 
                <th width="130">Search By</th>
                <th  width="180" align="center" id="search_by_th_up">Enter Order Number</th>
                <th width="200">Date Range</th>
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
                    <input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>+'_'+<? echo $garments_nature; ?>+'_'+<? echo $production_company; ?>+'_'+<? echo $hidden_variable_cntl; ?>+'_'+<? echo $hidden_preceding_process; ?>, 'create_po_search_list_view', 'search_div', 'garments_finishing_delivery_entry_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:100px;" />
                </td>
            </tr>
            <tr>
                <td colspan="4"  align="center" valign="middle">
                    <? echo load_month_buttons(1);  ?>
                    <input type="hidden" id="hidden_mst_id">
                    <input type="hidden" id="hidden_grmtItem_id">
                    <input type="hidden" id="hidden_po_qnty">
                    <input type="hidden" id="hidden_po_no">
                    <input type="hidden" id="hidden_country_id">
                    <input type="hidden" id="hidden_style_no">
                    <input type="hidden" id="hidden_job_no">
                    <input type="hidden" id="hidden_buyer">
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
 	$garments_nature = $ex_data[5];
 	$production_company = $ex_data[6];
 	$variable_cntl = $ex_data[7];
	$preceding_process = $ex_data[8];
	$qty_source=0;
	if($preceding_process==28) $qty_source=4; //Sewing Input
	else if($preceding_process==29) $qty_source=5;//Sewing Output
	else if($preceding_process==30) $qty_source=7;//Iron Output
	else if($preceding_process==31) $qty_source=8;//Packing And Finishing
	else if($preceding_process==32) $qty_source=7;//Iron Output
	else if($preceding_process==91) $qty_source=7;//Iron Output
	else if($preceding_process==103) $qty_source=11;//Poly Entry
	$qty_source=8;
	
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
		$qty_source_cond="and b.id in(select po_break_down_id from pro_garments_production_mst where production_type='$qty_source' and status_active=1 and is_deleted=0)";
	}

	if(trim($txt_search_by)==4 && trim($txt_search_common)!="")
	{
		$sql = "SELECT b.id, a.order_uom, a.buyer_name, a.company_name, a.total_set_qnty, a.set_break_down, a.job_no, a.style_ref_no, a.gmts_item_id, a.location_name, b.shipment_date, b.po_number,b.file_no,b.grouping, b.po_quantity, b.plan_cut
		from wo_po_details_master a, wo_po_break_down b, wo_po_acc_po_info c
		where a.job_no = b.job_no_mst and b.id=c.po_break_down_id and a.status_active=1 and  a.is_deleted=0 and b.status_active in(1)  and  b.is_deleted=0 and  b.is_confirmed =1 and c.status_active=1 and c.is_deleted=0  $sql_cond group by b.id, a.order_uom, a.buyer_name, a.company_name, a.total_set_qnty, a.set_break_down, a.job_no, a.style_ref_no, a.gmts_item_id, a.location_name, b.shipment_date, b.po_number, b.file_no, b.grouping, b.po_quantity, b.plan_cut order by b.shipment_date DESC";
	}
	else
	{
 		$sql = "SELECT b.id, a.order_uom, a.buyer_name, a.company_name, a.total_set_qnty, a.set_break_down, a.job_no, a.style_ref_no, a.gmts_item_id, a.location_name, b.shipment_date, b.po_number,b.file_no,b.grouping, b.po_quantity, b.plan_cut
		from wo_po_details_master a, wo_po_break_down b 
		where a.job_no = b.job_no_mst and a.status_active=1 and b.status_active in(1) and a.is_deleted=0  and  b.is_deleted=0 and  b.is_confirmed =1  $sql_cond  order by b.shipment_date DESC"; //and a.garments_nature=$garments_nature
	}
	
	//echo $sql;
	//die;
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
				if($po_cond=="") $po_cond.=" and ( po_break_down_id in ($ids) ";
				else
					$po_cond.=" or   po_break_down_id in ($ids) "; 
			}
			$po_cond.=") ";

		}
		else
		{
			$po_cond.=" and po_break_down_id in ($poIds) ";
		}
	}

 	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');

	//$po_country_arr=return_library_array( "select po_break_down_id, $select_field"."_concat(distinct(country_id)) as country from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 group by po_break_down_id",'po_break_down_id','country');
	
	$po_country_data_arr=array();
	$country_sql=sql_select( "SELECT po_break_down_id, item_number_id, country_id, pack_type, sum(order_quantity) as po_qty, sum(plan_cut_qnty) as plan_cut_qty from wo_po_color_size_breakdown where status_active=1 and  is_deleted=0 $po_cond group by po_break_down_id, item_number_id, country_id, pack_type"); 
	
	foreach($country_sql as $row)
	{
		//$po_country_arr[$row[csf('po_break_down_id')]].=$row[csf('country_id')].',';
		$po_country_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]][$row[csf('pack_type')]]['po_qty']=$row[csf('po_qty')];
		$po_country_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]][$row[csf('pack_type')]]['plan_cut_qty']=$row[csf('plan_cut_qty')];
	}
	unset($country_sql);
	
	$qty_source=0;
	if($variable_qty_source_packing==1) $qty_source=7; //Iron Output
	else if($variable_qty_source_packing==2) $qty_source=11;//Poly Output
	
	$total_entry_qty_data_arr=array();
	$total_entry_qty=sql_select( "SELECT po_break_down_id, item_number_id, country_id, sum(production_quantity) as production_quantity, pack_type from pro_garments_production_mst where status_active=1 and is_deleted=0 and production_type=14 $po_cond group by po_break_down_id, item_number_id, country_id, pack_type");
	
	// die('go to hell');
	foreach($total_entry_qty as $row)
	{
		$total_entry_qty_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]][$row[csf('pack_type')]]=$row[csf('production_quantity')];
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
                <th width="80">Delivery Qty</th>
                <th width="80">Balance</th>
                <th>Pack Type</th>
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
					foreach($item_data as $country_id=>$country_data)
					{
						foreach($country_data as $pack_type=>$val)
						{
							if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							
							$po_qnty=$val['po_qty'];
							$plan_cut_qnty=$val['plan_cut_qty'];
							
							?>
								<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value(<? echo $row[csf("id")];?>,'<? echo $row[csf("po_number")];?>','<? echo $po_qnty;?>','<? echo $row[csf("style_ref_no")];?>','<? echo $row[csf("job_no")];?>','<? echo $row[csf("buyer_name")];?>','<? echo $grmts_item;?>','<? echo $country_id;?>');" > 
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
									//echo $total_in_qty=$total_in_qty_data_arr[$row[csf('id')]][$grmts_item][$country_id][$pack_type]; 
									echo $total_in_qty=$total_entry_qty_data_arr[$row[csf('id')]][$grmts_item][$country_id][$pack_type];
									?> &nbsp;</td>
								   <td width="80" align="right"><?php $balance=$po_qnty-$total_in_qty; echo $balance; ?>&nbsp;</td>
								   <td><?php echo $pack_type;?>&nbsp;</td> 	
								</tr>
							<? 
							$i++;
						}
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
if($action=="create_po_search_list_view__")
{
 	$ex_data = explode("_",$data);
	$txt_search_by = $ex_data[0];
	$txt_search_common = $ex_data[1];
	$txt_date_from = $ex_data[2];
	$txt_date_to = $ex_data[3];
	$company = $ex_data[4];
 	$garments_nature = $ex_data[5];
 	$production_company = $ex_data[6];
 	$variable_cntl = $ex_data[7];
	$preceding_process = $ex_data[8];
	$qty_source=0;
	if($preceding_process==28) $qty_source=4; //Sewing Input
	else if($preceding_process==29) $qty_source=5;//Sewing Output
	else if($preceding_process==30) $qty_source=7;//Iron Output
	else if($preceding_process==31) $qty_source=8;//Packing And Finishing
	else if($preceding_process==32) $qty_source=7;//Iron Output
	else if($preceding_process==91) $qty_source=7;//Iron Output
	else if($preceding_process==103) $qty_source=11;//Poly Entry

	
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
			$sql_cond = " and b.file_no like ".trim($txt_search_common)."";
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
		$qty_source_cond="and b.id in(select po_break_down_id from pro_garments_production_mst where production_type='$qty_source' and status_active=1 and is_deleted=0)";
	}
	if(trim($txt_search_by)==4 && trim($txt_search_common)!="")
	{
		$sql = "SELECT b.id, a.order_uom, a.buyer_name, a.company_name, a.total_set_qnty, a.set_break_down, a.job_no, a.style_ref_no, a.gmts_item_id, a.location_name, b.shipment_date, b.po_number,b.file_no,b.grouping, b.po_quantity, b.plan_cut
			from wo_po_details_master a, wo_po_break_down b, wo_po_acc_po_info c
			where a.job_no = b.job_no_mst and b.id=c.po_break_down_id and a.status_active=1 and  a.is_deleted=0 and b.status_active in(1)  and  b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.garments_nature=$garments_nature $sql_cond    group by b.id, a.order_uom, a.buyer_name, a.company_name, a.total_set_qnty, a.set_break_down, a.job_no, a.style_ref_no, a.gmts_item_id, a.location_name, b.shipment_date, b.po_number, b.file_no, b.grouping, b.po_quantity, b.plan_cut order by b.shipment_date DESC";
	}
	else
	{
 		$sql = "SELECT b.id, a.order_uom, a.buyer_name, a.company_name, a.total_set_qnty, a.set_break_down, a.job_no, a.style_ref_no, a.gmts_item_id, a.location_name, b.shipment_date, b.po_number,b.file_no,b.grouping, b.po_quantity, b.plan_cut
		 from wo_po_details_master a, wo_po_break_down b 
		 where a.job_no = b.job_no_mst and a.status_active=1 and b.status_active in(1) and a.is_deleted=0  and  b.is_deleted=0 and a.garments_nature=$garments_nature 
		 $sql_cond  order by b.shipment_date DESC"; 
	}
	
	   //echo $sql;die;
	$result = sql_select($sql);
 	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');

	//$po_country_arr=return_library_array( "select po_break_down_id, $select_field"."_concat(distinct(country_id)) as country from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 group by po_break_down_id",'po_break_down_id','country');
	
	$po_country_data_arr=array();
	$country_sql=sql_select( "select po_break_down_id, item_number_id, country_id, pack_type, sum(order_quantity) as po_qty, sum(plan_cut_qnty) as plan_cut_qty from wo_po_color_size_breakdown where status_active=1 and  is_deleted=0 group by po_break_down_id, item_number_id, country_id, pack_type"); 
	
	foreach($country_sql as $row)
	{
		//$po_country_arr[$row[csf('po_break_down_id')]].=$row[csf('country_id')].',';
		$po_country_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]][$row[csf('pack_type')]]['po_qty']=$row[csf('po_qty')];
		$po_country_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]][$row[csf('pack_type')]]['plan_cut_qty']=$row[csf('plan_cut_qty')];
	}
	unset($country_sql);
	
	$qty_source=0;
	if($variable_qty_source_packing==1) $qty_source=7; //Iron Output
	else if($variable_qty_source_packing==2) $qty_source=11;//Poly Output
	
	$total_entry_qty_data_arr=array();
	$total_entry_qty=sql_select( "select po_break_down_id, item_number_id, country_id, sum(production_quantity) as production_quantity, pack_type from pro_garments_production_mst where status_active=1 and is_deleted=0 and production_type=14 group by po_break_down_id, item_number_id, country_id, pack_type");
	
	
	foreach($total_entry_qty as $row)
	{
		$total_entry_qty_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]][$row[csf('pack_type')]]=$row[csf('production_quantity')];
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
                <th width="80">Total finish Qty</th>
                <th width="80">Balance</th>
                <th>Pack Type</th>
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
					foreach($item_data as $country_id=>$country_data)
					{
						foreach($country_data as $pack_type=>$val)
						{
							if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							
							$po_qnty=$val['po_qty'];
							$plan_cut_qnty=$val['plan_cut_qty'];
							
							?>
								<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value(<? echo $row[csf("id")];?>,'<? echo $grmts_item;?>','<? echo $po_qnty;?>','<? echo $plan_cut_qnty;?>','<? echo $country_id;?>','<? echo $pack_type;?>');" > 
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
									//echo $total_in_qty=$total_in_qty_data_arr[$row[csf('id')]][$grmts_item][$country_id][$pack_type]; 
									echo $total_in_qty=$total_entry_qty_data_arr[$row[csf('id')]][$grmts_item][$country_id][$pack_type];
									?> &nbsp;</td>
								   <td width="80" align="right"><?php $balance=$po_qnty-$total_in_qty; echo $balance; ?>&nbsp;</td>
								   <td><?php echo $pack_type;?>&nbsp;</td> 	
								</tr>
							<? 
							$i++;
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
if($action=="populate_data_from_search_popup")
{
	$dataArr = explode("**",$data);
	$po_id = $dataArr[0];
	$item_id = $dataArr[1];
	$country_id = $dataArr[2];
	$pack_type = $dataArr[4];
	//echo $dataArr[3];die;
	if( $pack_type=='') $pack_type_cond=''; else $pack_type_cond=" and pack_type='$pack_type'";
	$qty_source=0;
	if($dataArr[3]==28) $qty_source=4; //Sewing Input
	else if($dataArr[3]==29) $qty_source=5;//Sewing Output
	else if($dataArr[3]==30) $qty_source=7;//Iron Output
	else if($dataArr[3]==31) $qty_source=8;//Packing And Finishing
	else if($dataArr[3]==32) $qty_source=7;//Iron Output
	else if($dataArr[3]==91) $qty_source=7;//Iron Output
	else if($dataArr[3]==103) $qty_source=11;//Poly Entry
	$res = sql_select("SELECT a.id, a.po_quantity, a.plan_cut, a.po_number, a.po_quantity, b.company_name, b.buyer_name, b.style_ref_no,b.gmts_item_id, b.order_uom, b.job_no, b.location_name from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and a.id=$po_id"); 
 
  	foreach($res as $result)
	{
		echo "$('#txt_order_no').val('".$result[csf('po_number')]."');\n";
		echo "$('#hidden_po_break_down_id').val('".$result[csf('id')]."');\n";
		echo "$('#cbo_buyer_name').val('".$result[csf('buyer_name')]."');\n";
		echo "$('#txt_job_no').val('".$result[csf('job_no')]."');\n";
		echo "$('#txt_style_no').val('".$result[csf('style_ref_no')]."');\n";
  		if($qty_source!=0)
   		{
   			$dataArray=sql_select("SELECT SUM(CASE WHEN production_type='$qty_source' THEN production_quantity END) as totalinput,
   				SUM(CASE WHEN production_type=14 THEN production_quantity ELSE 0 END) as totalsewing,			  
				SUM(CASE WHEN production_type=10 and trans_type=5 THEN production_quantity ELSE 0 END) as trans_in_qty, 
				SUM(CASE WHEN production_type=10 and trans_type=6 THEN production_quantity ELSE 0 END) as trans_out_qty 
   				from pro_garments_production_mst WHERE po_break_down_id=".$result[csf('id')]."  and item_number_id='$item_id' and country_id='$country_id' $pack_type_cond and status_active=1 and is_deleted=0");
		
	 		foreach($dataArray as $row)
			{  
				$row[csf('totalinput')] = $row[csf('totalinput')]+($row[csf('trans_in_qty')]-$row[csf('trans_out_qty')]);
				echo "$('#txt_finish_input_qty').val('".$row[csf('totalinput')]."');\n";
				echo "$('#txt_cumul_finish_qty').val('".$row[csf('totalsewing')]."');\n";
				$yet_to_produced = $row[csf('totalinput')]-$row[csf('totalsewing')];
				echo "$('#txt_yet_to_finish').val('".$yet_to_produced."');\n";
			}
	    }

	    if($qty_source==0)
		{
			$plan_cut_qnty=return_field_value("sum(plan_cut_qnty)","wo_po_color_size_breakdown","po_break_down_id=".$result[csf('id')]." and item_number_id='$item_id' and country_id='$country_id'   and is_deleted=0");
		
			$total_produced = return_field_value("sum(production_quantity)","pro_garments_production_mst","po_break_down_id=".$result[csf('id')]." and item_number_id='$item_id' and country_id='$country_id' and production_type=14 and is_deleted=0");
			echo "$('#txt_finish_input_qty').val('".$plan_cut_qnty."');\n";		
			echo "$('#txt_cumul_finish_qty').attr('placeholder','".$total_produced."');\n";
			echo "$('#txt_cumul_finish_qty').val('".$total_produced."');\n";
			$yet_to_produced = $plan_cut_qnty - $total_produced;
			echo "$('#txt_yet_to_finish').attr('placeholder','".$yet_to_produced."');\n";
			echo "$('#txt_yet_to_finish').val('".$yet_to_produced."');\n";
		}
  	}
	
	$sql_finish=sql_select("select d.prod_id from order_wise_pro_details  d where d.po_breakdown_id=".$po_id." and d.entry_form=25");
	
	
	foreach($sql_finish as $t_value)
	{
		$trimsids[$t_value[csf('prod_id')]]=$t_value[csf('prod_id')];	
	}
	
	if( count($trimsids)<1) $trimsids[0]=0;
	
	//$sql_finish=sql_select("select b.prod_id,c.item_group_id,c.item_description, sum(b.cons_amount)/sum(b.cons_quantity) as avg_rage from inv_receive_master a,inv_transaction b,product_details_master  c where 
	//a.id=b.mst_id and b.prod_id=c.id and  a.entry_form=24 and b.transaction_type=1 and b.prod_id in (select d.prod_id from order_wise_pro_details  d where d.po_breakdown_id=".$po_id." and d.entry_form=25) group by b.prod_id,c.item_group_id,c.item_description");
	
	$sql_finish=sql_select("select b.prod_id,c.item_group_id,c.item_description, sum(b.cons_amount)/sum(b.cons_quantity) as avg_rage from inv_receive_master a,inv_transaction b,product_details_master  c where 
	a.id=b.mst_id and b.prod_id=c.id and a.entry_form=24 and b.transaction_type=1 and b.prod_id in (".implode(",",$trimsids).") group by b.prod_id,c.item_group_id,c.item_description");
	
	$trims_receive_rate_arr=array();
	foreach($sql_finish as $t_rate)
	{
		$trims_receive_rate_arr[$t_rate[csf('item_group_id')]]=$t_rate[csf('avg_rage')];	
	}
	$costing_per_sql=sql_select("select job_no,costing_per,exchange_rate from wo_pre_cost_mst where job_no='".$result[csf('job_no')]."'");
	$exchange_rate=$costing_per_sql[0][csf('exchange_rate')];
	$costing_per=$costing_per_sql[0][csf('costing_per')];
	if($costing_per==1)
	{
		$costing_per_qty=12;
	}
	else if($costing_per==2)
	{
		$costing_per_qty=1;
	}
	else if($costing_per==3)
	{
		$costing_per_qty=24;
	}
	else if($costing_per==4)
	{
		$costing_per_qty=36;
	}
	else if($costing_per==5)
	{
		$costing_per_qty=48;
	}	

	$sql_trims=sql_select("select trim_group,description,cons_dzn_gmts,rate,amount from wo_pre_cost_trim_cost_dtls  where  job_no='".$result[csf('job_no')]."' and status_active=1 and is_deleted=0");
	
	$trims_data='';
	$trim_data_arr=array();
	foreach($sql_trims as $trim_val)
	{
		$trims_item_cons=$trim_val[csf('cons_dzn_gmts')]/$costing_per_qty;
		if($trims_receive_rate_arr[$trim_val[csf('trim_group')]]!="")
		{
			$trims_item_rate=$trims_receive_rate_arr[$trim_val[csf('trim_group')]];
		}
		else
		{
			$trims_item_rate=($trim_val[csf('rate')])*$exchange_rate;
		}
		$trims_item_amount=$trims_item_rate*$trims_item_cons;
		$total_trims_amount+=$trims_item_amount;
		$trims_data.=$trims_item_cons."**".$trims_item_rate."**".$trims_item_amount."##";	
		$trim_data_arr[$trim_val[csf("trim_group")]]['concs']=$trims_item_cons;
		$trim_data_arr[$trim_val[csf("trim_group")]]['rate']=$trims_item_rate;
		$trim_data_arr[$trim_val[csf("trim_group")]]['amount']=$trims_item_amount;
	}
	//print_r($trim_data_arr);die;
	echo "$('#accessoric_data').val('".$total_trims_amount."');\n";
	
	$sql_embl=sql_select("select emb_name,emb_type,cons_dzn_gmts,rate,amount from wo_pre_cost_embe_cost_dtls  where  job_no='".$result[csf('job_no')]."' and status_active=1 and is_deleted=0");
	foreach($sql_embl as $embl_val)
	{
		$embl_item_amount=($embl_val[csf('amount')]/$costing_per_qty)*$exchange_rate;
		$embl_data.=$embl_val[csf('emb_name')]."**".$embl_val[csf('emb_type')]."**".$embl_item_amount."__";
	}
	echo "$('#emblishment_data').val('".$embl_data."');\n";

	$pre_cost_dtls=sql_select("select job_no,comm_cost,commission,lab_test,inspection,cm_cost,freight,currier_pre_cost,certificate_pre_cost 	,common_oh,depr_amor_pre_cost from wo_pre_cost_dtls where job_no='".$result[csf('job_no')]."'");
	foreach($pre_cost_dtls as $pre_val)
	{
		$commercial_cost=($pre_val[csf('comm_cost')]/$costing_per_qty)*$exchange_rate;
		$commision_cost=($pre_val[csf('commission')]/$costing_per_qty)*$exchange_rate;
		$lab_test_cost=($pre_val[csf('lab_test')]/$costing_per_qty)*$exchange_rate;
		$inspection_cost=($pre_val[csf('inspection')]/$costing_per_qty)*$exchange_rate;
		$cm_cost=($pre_val[csf('cm_cost')]/$costing_per_qty)*$exchange_rate;
		$freight_cost=($pre_val[csf('freight')]/$costing_per_qty)*$exchange_rate;
		$currier_cost=($pre_val[csf('currier_pre_cost')]/$costing_per_qty)*$exchange_rate;
		$cirtificate_cost=($pre_val[csf('certificate_pre_cost')]/$costing_per_qty)*$exchange_rate;
		$commision_cost=($pre_val[csf('commission')]/$costing_per_qty)*$exchange_rate;
		$operating_cost=($pre_val[csf('common_oh')]/$costing_per_qty)*$exchange_rate;
		$depriciation_cost=($pre_val[csf('depr_amor_pre_cost')]/$costing_per_qty)*$exchange_rate;
		
		$precost_data=$commercial_cost."**".$commision_cost."**".$lab_test_cost."**".$inspection_cost."**".$cm_cost."**".$freight_cost."**".$currier_cost."**".$cirtificate_cost."**".$operating_cost."**".$depriciation_cost;
	}
	echo "$('#precost_data').val('".$precost_data."');\n";
 	exit();	
}
if($action=="gross_level_entry")
{
	$dataArr = explode("**",$data);
	$po_id = $dataArr[0];
	$item_id = $dataArr[1];
	$job_no= $dataArr[2];
	
	//############################## Knit Finish Fabric Rate #########################################################################
	if($db_type==2)
	{
		$sql_finish_issue=sql_select("select listagg(a.body_part_id,',') within group(order by a.body_part_id) as body_part, listagg(a.prod_id,',') within group(order by a.prod_id) as product_id  from inv_finish_fabric_issue_dtls a,order_wise_pro_details b where a.trans_id=b.trans_id and b.po_breakdown_id=$po_id and a.gmt_item_id=$item_id and b.entry_form in (18,71)");
	}
	else
	{
		$sql_finish_issue=sql_select("select group_concat(a.body_part_id) as body_part, group_concat(a.prod_id) as product_id  from inv_finish_fabric_issue_dtls a,order_wise_pro_details b where a.trans_id=b.trans_id and b.po_breakdown_id=$po_id and a.gmt_item_id=$item_id and b.entry_form in (18,71)");
		
	}
	foreach($sql_finish_issue as $issue_val)
	{
		if($issue_val[csf('body_part')]=="") $issue_val[csf('body_part')]=0; else $issue_val[csf('body_part')]=$issue_val[csf('body_part')];
		if($issue_val[csf('product_id')]=="") $issue_val[csf('product_id')]=0; else $issue_val[csf('product_id')]=$issue_val[csf('product_id')];
		$body_part_id=$issue_val[csf('body_part')];
		$product_id=$issue_val[csf('product_id')];
	}
	
	$sql_finish_receive=sql_select("select a.fabric_description_id,a.body_part_id,sum(a.amount)/sum(a.receive_qnty) as ave_rate  from pro_finish_fabric_rcv_dtls a, order_wise_pro_details b where a.trans_id=b.trans_id and b.po_breakdown_id=$po_id and a.prod_id in($product_id) and a.body_part_id in($body_part_id) and b.entry_form in (37,68) group by a.fabric_description_id,a.body_part_id");
	$finish_rate=array();
	foreach($sql_finish_receive as $f_val)
	{
		$finish_rate[$f_val[csf('body_part_id')]][$f_val[csf('fabric_description_id')]]=number_format($f_val[csf('ave_rate')],4,".","");
	}
	
	//############################## Woven Finish Fabric Rate #########################################################################
	if($db_type==2)
	{
		$sql_woven_finish_issue=sql_select("select listagg(a.body_part_id,',') within group(order by a.body_part_id) as body_part, listagg(a.prod_id,',') within group(order by a.prod_id) as product_id  from inv_transaction a,order_wise_pro_details b where a.id=b.trans_id and b.po_breakdown_id=$po_id and a.gmt_item_id=$item_id and b.entry_form=19");
	}
	else
	{
		$sql_woven_finish_issue=sql_select("select group_concat(a.body_part_id) as body_part, group_concat(a.prod_id) as product_id  from inv_transaction a,order_wise_pro_details b where a.id=b.trans_id and b.po_breakdown_id=$po_id and a.gmt_item_id=$item_id and b.entry_form=19");
	}
	
	foreach($sql_woven_finish_issue as $woven_val)
	{
		if($woven_val[csf('body_part')]=="") $woven_val[csf('body_part')]=0; else $woven_val[csf('body_part')]=$woven_val[csf('body_part')];
		if($woven_val[csf('product_id')]=="") $woven_val[csf('product_id')]=0; else $woven_val[csf('product_id')]=$woven_val[csf('product_id')];
		
		$woven_body_part_id=$woven_val[csf('body_part')];
		$woven_product_id=$woven_val[csf('product_id')];
	}

	$sql_woven_finish_receive=sql_select( "select c.detarmination_id,a.body_part_id,sum(a.cons_amount)/sum(a.cons_quantity) as ave_rate  from inv_transaction a, order_wise_pro_details b,product_details_master  c where a.id=b.trans_id and b.po_breakdown_id=$po_id and a.prod_id in($woven_product_id) and a.body_part_id in($woven_body_part_id) and b.entry_form=17 and b.prod_id =c.id group by c.detarmination_id,a.body_part_id");
	$woven_finish_rate=array();
	foreach($sql_woven_finish_receive as $w_val)
	{
		$woven_finish_rate[$w_val[csf('body_part_id')]][$w_val[csf('detarmination_id')]]=number_format($w_val[csf('ave_rate')],4,".","");
	}
	
	// ################################ Other Process loss ######################################################################################
	
	$processloss_sql=sql_select("select sum(process_loss) as process_loss,mst_id from conversion_process_loss   where  process_id in(120,121,122,123,124,130,131) group by mst_id having (sum(process_loss)>0) ");
	$proceloss_arr=array();
	foreach($processloss_sql as $value)
	{
		$proceloss_arr[$value[csf('mst_id')]]=$value[csf('process_loss')];
	}
	
	//##########################################################################################################################################
	
	
	$costing_per=return_field_value("costing_per","wo_pre_cost_mst","job_no='".$job_no."'","costing_per");
	if($costing_per==1)
	{
		$costing_per_qty=12;
	}
	else if($costing_per==2)
	{
		$costing_per_qty=1;
	}
	else if($costing_per==3)
	{
		$costing_per_qty=24;
	}
	else if($costing_per==4)
	{
		$costing_per_qty=36;
	}
	else if($costing_per==5)
	{
		$costing_per_qty=48;
	}
	
	$color_size_qty_arr=array();
	$color_size_sql=sql_select ("SELECT  po_break_down_id,item_number_id,size_number_id,color_number_id,sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown where  is_deleted=0   and  po_break_down_id in (".str_replace("'","",$po_id).") and item_number_id=$item_id
	group by po_break_down_id,item_number_id,size_number_id,color_number_id");
	foreach($color_size_sql as $s_id)
	{
		$color_size_qty_arr[$s_id[csf('color_number_id')]][$s_id[csf('size_number_id')]]+=$s_id[csf('plan_cut_qnty')]; 
	}
	
	$sql_sewing=sql_select("SELECT a.fab_nature_id,a.lib_yarn_count_deter_id ,b.po_break_down_id,a.item_number_id,a.body_part_id, b.color_number_id, b.gmts_sizes,sum(b.cons) AS conjumction FROM wo_pre_cos_fab_co_avg_con_dtls b, wo_pre_cost_fabric_cost_dtls a WHERE a.id = b.pre_cost_fabric_cost_dtls_id  AND b.po_break_down_id in (".str_replace("'","",$po_id).") and a.item_number_id=$item_id
	and b.cons!=0 GROUP BY a.fab_nature_id,a.lib_yarn_count_deter_id ,b.po_break_down_id,a.item_number_id,a.body_part_id,b.color_number_id,b.gmts_sizes");
	
	$con_per_dzn=0;
	$po_item_qty_arr=array();
	$color_size_conjumtion=array();
	$fabric_nature_arr=array();
	foreach($sql_sewing as $row_sew)
	{
		$fabric_nature_arr[$row_sew[csf('color_number_id')]][$row_sew[csf('body_part_id')]][$row_sew[csf('lib_yarn_count_deter_id')]]=$row_sew[csf('fab_nature_id')];
		$color_size_conjumtion[$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]][$row_sew[csf('body_part_id')]][$row_sew[csf('lib_yarn_count_deter_id')]]['conjum']=str_replace("'","",$row_sew[csf("conjumction")]);
			
		$color_size_conjumtion[$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]][$row_sew[csf('body_part_id')]][$row_sew[csf('lib_yarn_count_deter_id')]]['plan_cut_qty']=$color_size_qty_arr[$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]]; 
		$po_item_qty_arr[$row_sew[csf('color_number_id')]][$row_sew[csf('body_part_id')]][$row_sew[csf('lib_yarn_count_deter_id')]]['plan_cut_qty']+=$color_size_qty_arr[$row_sew[csf('color_number_id')]][$row_sew[csf('gmts_sizes')]];   
	}
			
	
	  foreach($color_size_conjumtion as $c_id=>$c_value)
	  {
		 foreach($c_value as $s_id=>$s_value)
		 {
			 foreach($s_value as $b_id=>$b_value)
			 {
				foreach($b_value as $deter_id=>$deter_value)
			 	{
					 $order_color_size_qty=$deter_value['plan_cut_qty'];
					 $order_qty=$po_item_qty_arr[$c_id][$b_id][$deter_id]['plan_cut_qty'];
					 $order_color_size_qty_per= ($order_color_size_qty/$order_qty)*100;
					 $conjunction_per= ($deter_value['conjum']*$order_color_size_qty_per/100);
					 $fabric_nature=$fabric_nature_arr[$c_id][$b_id][$deter_id];
					 $process_loss=$proceloss_arr[$deter_id];
					 $fabric_used=($conjunction_per*100)/(100-$process_loss);
					 if($fabric_nature==3)
					{
						$con_per_dzn+=$fabric_used*$woven_finish_rate[$b_id][$deter_id];//*$finish_rate[$b_id]
					}
					else
					{
						$con_per_dzn+=$fabric_used*$finish_rate[$b_id][$deter_id];//*$finish_rate[$b_id]
					}
				}
			 }
		 }
	  }
	echo "$('#fabric_data').val('".($con_per_dzn/$costing_per_qty)."');\n";	
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
	$job_no= $dataArr[6];
 	$color_library=return_library_array( "SELECT id, color_name from lib_color",'id','color_name');
	$size_library=return_library_array( "SELECT id, size_name from lib_size",'id','size_name');
	$qty_source=8;
	// ==================== set mst form data ===============================
	$cumulQty_arr=array();
	if($qty_source!=0)
   	{		
		/* $dataArray=sql_select("SELECT item_number_id, country_id, 
			SUM(CASE WHEN production_type='$qty_source' THEN production_quantity else 0 END) as totalinput,
			SUM(CASE WHEN production_type=14 THEN production_quantity ELSE 0 END) as totalsewing, 
			SUM(CASE WHEN production_type=10 and trans_type=5 THEN production_quantity ELSE 0 END) as trans_in_qty, 
			SUM(CASE WHEN production_type=10 and trans_type=6 THEN production_quantity ELSE 0 END) as trans_out_qty, 
			SUM(CASE WHEN production_type=84 THEN production_quantity ELSE 0 END) as RCV_RTN
			from pro_garments_production_mst WHERE po_break_down_id=$po_id and status_active=1 and is_deleted=0 group by item_number_id, country_id"); */
		$dataArray=sql_select("SELECT item_number_id, country_id, 
			SUM(CASE WHEN production_type='$qty_source' THEN production_quantity else 0 END) as FINISHING,
			SUM(CASE WHEN production_type=14 THEN production_quantity ELSE 0 END) as DELIVERY, 
			SUM(CASE WHEN production_type=10 and trans_type=5 THEN production_quantity ELSE 0 END) as trans_in_qty, 
			SUM(CASE WHEN production_type=10 and trans_type=6 THEN production_quantity ELSE 0 END) as trans_out_qty, 
			SUM(CASE WHEN production_type=84 THEN production_quantity ELSE 0 END) as RCV_RTN
			from pro_garments_production_mst WHERE po_break_down_id=$po_id and status_active=1 and is_deleted=0 group by item_number_id, country_id");
		foreach($dataArray as $row)
		{ 
			$cumulQty_arr[$row[csf('item_number_id')]][$row[csf('country_id')]]['finishing']=$row[csf('FINISHING')];
			$cumulQty_arr[$row[csf('item_number_id')]][$row[csf('country_id')]]['delivery']=$row[csf('DELIVERY')];
			// $cumulQty_arr[$row[csf('item_number_id')]][$row[csf('country_id')]]['input']=$row[csf('totalinput')];
			// $cumulQty_arr[$row[csf('item_number_id')]][$row[csf('country_id')]]['totalsewing']=$row[csf('totalsewing')];
			$cumulQty_arr[$row[csf('item_number_id')]][$row[csf('country_id')]]['trans_in_qty']=$row[csf('trans_in_qty')];
			$cumulQty_arr[$row[csf('item_number_id')]][$row[csf('country_id')]]['trans_out_qty']=$row[csf('trans_out_qty')];
			$cumulQty_arr[$row[csf('item_number_id')]][$row[csf('country_id')]]['rcv_rtn']=$row["RCV_RTN"];
		}
		unset($dataArray);
	}
	
	$country_qnty=return_field_value("sum(order_quantity)","wo_po_color_size_breakdown","po_break_down_id=$po_id and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1");
	
	$total_produced = return_field_value("sum(production_quantity)","pro_garments_production_mst","po_break_down_id=$po_id and item_number_id='$item_id' and country_id='$country_id' and production_type=14 and is_deleted=0 and status_active=1");
	
	$res = sql_select("SELECT a.id, a.po_quantity, a.plan_cut, a.po_number, a.po_quantity, b.company_name, b.buyer_name, b.style_ref_no,b.gmts_item_id, b.order_uom, b.job_no, b.location_name from wo_po_break_down a, wo_po_details_master b where a.job_id=b.id and a.id=$po_id"); 
 
  	foreach($res as $result)
	{
		echo "$('#txt_order_no').val('".$result[csf('po_number')]."');\n";
		echo "$('#hidden_po_break_down_id').val('".$result[csf('id')]."');\n";
		echo "$('#cbo_buyer_name').val('".$result[csf('buyer_name')]."');\n";
		echo "$('#txt_job_no').val('".$result[csf('job_no')]."');\n";
		echo "$('#txt_order_qty').val('".$result[csf('po_quantity')]."');\n";
		echo "$('#txt_country_qty').val('".$country_qnty."');\n";
		echo "$('#txt_style_no').val('".$result[csf('style_ref_no')]."');\n";

		$trans_in_qty=$cumulQty_arr[$item_id][$country_id]['trans_in_qty'];
		$trans_out_qty=$cumulQty_arr[$item_id][$country_id]['trans_out_qty'];
		$rcv_rtn_qty=$cumulQty_arr[$item_id][$country_id]['rcv_rtn'];
		$totalFinsh=$cumulQty_arr[$item_id][$country_id]['finishing'];
		$totalDel=$cumulQty_arr[$item_id][$country_id]['delivery'];
		
		echo "$('#txt_finish_input_qty').val('".$totalFinsh."');\n";
		echo "$('#txt_cumul_delivery_qty').val('".$totalDel."');\n";
		$yet_to_produced = $totalFinsh-$totalDel+($trans_in_qty-$trans_out_qty+$rcv_rtn_qty);
		$yettoTitel="Total Finish Qty:".$totalFinsh."-Total Delivery Qty:".$totalDel."+( Trans In:".$trans_in_qty."- Trans Out:".$trans_out_qty."+ Rec. Return:".$rcv_rtn_qty.")";
		echo "$('#txt_yet_to_delivery').val('".$yet_to_produced."');\n";
		echo "$('#txt_yet_to_delivery').attr('title','".$yettoTitel."');\n";
  	}
		
	if( $variableSettings==2 ) // color level
	{
		$color_size_qty_arr=array();
		$color_size_sql=sql_select ("SELECT  po_break_down_id,item_number_id,size_number_id,color_number_id,sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown where status_active =1 and   is_deleted=0   and  po_break_down_id in (".str_replace("'","",$po_id).") and item_number_id=$item_id $pack_type_cond group by po_break_down_id,item_number_id,size_number_id,color_number_id");
		foreach($color_size_sql as $s_id)
		{
			$color_size_qty_arr[$s_id[csf('color_number_id')]][$s_id[csf('size_number_id')]]+=$s_id[csf('plan_cut_qnty')]; 
		}
				
		$sql = "SELECT a.item_number_id, a.color_number_id, sum(a.order_quantity) as order_quantity, 
		sum(a.plan_cut_qnty) as plan_cut_qnty, 
		sum(CASE WHEN c.production_type='$qty_source' then b.production_qnty ELSE 0 END) as production_qnty,
		sum(CASE WHEN c.production_type=14 then b.production_qnty ELSE 0 END) as cur_production_qnty,
		sum(CASE WHEN c.production_type=10 and c.trans_type=5 then b.production_qnty ELSE 0 END) as trans_in_qty, 
		sum(CASE WHEN c.production_type=10 and c.trans_type=6 then b.production_qnty ELSE 0 END) as trans_out_qty, 
		sum(CASE WHEN c.production_type=84 then b.production_qnty ELSE 0 END) as rcv_rtn_qty 
		from wo_po_color_size_breakdown a left join pro_garments_production_dtls b on a.id=b.color_size_break_down_id left join pro_garments_production_mst c on c.id=b.mst_id 
		where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active=1  group by a.item_number_id, a.color_number_id";	
			

	}
	else if( $variableSettings==3 ) //color and size level
	{
		
		$color_size_qty_arr=array();
		$color_size_sql=sql_select ("SELECT  po_break_down_id,item_number_id,size_number_id,color_number_id,sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown where status_active =1 and  is_deleted=0  and  po_break_down_id in (".str_replace("'","",$po_id).") and item_number_id=$item_id $pack_type_cond group by po_break_down_id,item_number_id,size_number_id,color_number_id");
		foreach($color_size_sql as $s_id)
		{
			$color_size_qty_arr[$s_id[csf('color_number_id')]][$s_id[csf('size_number_id')]]+=$s_id[csf('plan_cut_qnty')]; 
		}
				
			
		$dtlsData = sql_select("SELECT a.color_size_break_down_id,
			sum(CASE WHEN a.production_type='$qty_source' then a.production_qnty ELSE 0 END) as production_qnty,
			sum(CASE WHEN a.production_type=14 then a.production_qnty ELSE 0 END) as cur_production_qnty,
			sum(CASE WHEN a.production_type=10 and b.trans_type=5 then a.production_qnty ELSE 0 END) as trans_in_qty,  
			sum(CASE WHEN a.production_type=10 and b.trans_type=6 then a.production_qnty ELSE 0 END) as trans_out_qty,  
			sum(CASE WHEN a.production_type=84 then a.production_qnty ELSE 0 END) as rcv_rtn_qty  
			from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in($qty_source,14,10,84) $pack_typeCond group by a.color_size_break_down_id");
									
		foreach($dtlsData as $row)
		{				  
			$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['iss']= $row[csf('production_qnty')];
			$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv']= $row[csf('cur_production_qnty')];
			$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['in']= $row[csf('trans_in_qty')];
			$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['out']= $row[csf('trans_out_qty')];
			$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv_rtn_qty']= $row[csf('rcv_rtn_qty')];
		} 
					
		$sql = "SELECT id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty from wo_po_color_size_breakdown
		where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' $pack_type_cond and is_deleted=0 and status_active =1 order by color_number_id, size_order";
	}
	else // by default color and size level
	{
		$dtlsData = sql_select("SELECT a.color_size_break_down_id,
			sum(CASE WHEN a.production_type='$qty_source' then a.production_qnty ELSE 0 END) as production_qnty,
			sum(CASE WHEN a.production_type=14 then a.production_qnty ELSE 0 END) as cur_production_qnty, 
			sum(CASE WHEN a.production_type=10 and b.trans_type=5 then a.production_qnty ELSE 0 END) as trans_in_qty,  
			sum(CASE WHEN a.production_type=10 and b.trans_type=6 then a.production_qnty ELSE 0 END) as trans_out_qty,  	
			sum(CASE WHEN a.production_type=84 then a.production_qnty ELSE 0 END) as rcv_rtn_qty,  	
			from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in($qty_source,14,10,84)  group by a.color_size_break_down_id");
									
		foreach($dtlsData as $row)
		{				  
			$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['iss']= $row[csf('production_qnty')];
			$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv']= $row[csf('cur_production_qnty')];
			$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['in']= $row[csf('trans_in_qty')];
			$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['out']= $row[csf('trans_out_qty')];
			$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv_rtn_qty']= $row[csf('rcv_rtn_qty')];
		} 
		$sql = "SELECT id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty from wo_po_color_size_breakdown
		where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 order by color_number_id, size_order";
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
	$colorHTML="";
	$colorID='';
	$chkColor = array(); 
	$i=0;$totalQnty=0;

	foreach($colorResult as $color)
	{
		if( $variableSettings==2 ) // color level
		{ 
			$colorHTML .='<tr><td>'.$color_library[$color[csf("color_number_id")]].'</td><td><input type="text" name="txt_color" id="colSize_'.($i+1).'" style="width:60px"  class="text_boxes_numeric" placeholder="'.($color[csf("production_qnty")]+$color[csf("trans_in_qty")]-$color[csf("trans_out_qty")]-$color[csf("cur_production_qnty")]+$color[csf("rcv_rtn_qty")]).'" onblur="fn_colorlevel_total('.($i+1).')"></td><td></td></tr>';				
			$totalQnty += $color[csf("production_qnty")]+$color[csf("trans_in_qty")]-$color[csf("trans_out_qty")]-$color[csf("cur_production_qnty")]+$color[csf("rcv_rtn_qty")];
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
			$trans_in_qty=$color_size_qnty_array[$color[csf('id')]]['in'];
			$trans_out_qty=$color_size_qnty_array[$color[csf('id')]]['out'];
			$rcv_rtn_qty=$color_size_qnty_array[$color[csf('id')]]['rcv_rtn_qty'];
			//echo 

			$colorHTML .='<tr><td>'.$size_library[$color[csf("size_number_id")]].'</td><td><input type="text" name="colorSize" id="colSize_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:80px" placeholder="'.($iss_qnty+$trans_in_qty-$trans_out_qty-$rcv_qnty+$rcv_rtn_qty).'" onblur="fn_total('.$color[csf("color_number_id")].','.($i+1).')"><input type="text" name="colorSizePOQnty" id="colorSizePOQnty_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:50px" value="'.$color[csf("order_quantity")].'" readonly disabled></td></tr>';				
		}
		$i++; 
	}

	if( $variableSettings==2 ){ $colorHTML = '<table id="table_color" class="rpt_table"><thead><tr><th width="70">Color</th><th width="60">Quantity</th><th width="60">Reject</th></tr><tr> <th colspan="2"><div style="padding-left: 30px;text-align:left"><input type="checkbox" onClick="active_placeholder_qty_color(' . $color[csf("color_number_id")] . ')" id="set_all">&nbsp;<label for="set_all">Available Qty Auto Fill</label></div></th> </tr></thead><tbody>'.$colorHTML.'<tbody><tfoot><tr><th>Total</th><th><input type="text" id="total_color" placeholder="'.$totalQnty.'" class="text_boxes_numeric" style="width:60px" ></th><th><input type="text" id="total_color_rej" class="text_boxes_numeric" style="width:60px" ></th></tr></tfoot></table>'; }
	echo "$('#breakdown_td_id').html('".addslashes($colorHTML)."');\n";
	$colorList = substr($colorID,0,-1);
	echo "$('#hidden_colorSizeID').val('".$colorList."');\n";
	//#############################################################################################//
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
            <th width="45">Country Qty.</th>
            <th width="45">Fin Qty</th>
            <th>Delv. Qty.</th>                    
        </thead>
		<?
		$issue_qnty_arr=sql_select("SELECT a.production_type,a.po_break_down_id, a.item_number_id, a.country_id, b.production_qnty from pro_garments_production_mst a, pro_garments_production_dtls b where a.id=b.mst_id and a.po_break_down_id='$po_id' and a.production_type in(8,14) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		$issue_data_arr=array();
		foreach($issue_qnty_arr as $row)
		{
			$issue_data_arr[$row[csf("production_type")]][$row[csf("po_break_down_id")]][$row[csf("country_id")]][$row[csf("item_number_id")]]+=$row[csf("production_qnty")];
		}  
		$i=1;
		$sqlResult =sql_select("SELECT po_break_down_id, item_number_id, country_id, max(country_ship_date) as country_ship_date, sum(order_quantity) as country_qnty, sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown where po_break_down_id='$po_id' and status_active=1  and is_deleted=0 group by po_break_down_id, item_number_id, country_id order by country_ship_date");
		foreach($sqlResult as $row)
		{
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$out_qnty=$issue_data_arr[8][$row[csf("po_break_down_id")]][$row[csf("country_id")]][$row[csf("item_number_id")]];
			$issue_qnty=$issue_data_arr[14][$row[csf("po_break_down_id")]][$row[csf("country_id")]][$row[csf("item_number_id")]];
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="put_country_data(<? echo $row[csf('po_break_down_id')].",".$row[csf('item_number_id')].",".$row[csf('country_id')].",".$row[csf('country_qnty')].",".$row[csf('plan_cut_qnty')]; ?>);"> 
				<td width="20"><? echo $i; ?></td>
				<td width="100"><p><? echo $garments_item[$row[csf('item_number_id')]]; ?></p></td>
				<td width="80" align="center"><p>
					<? 
						echo $country_library[$row[csf('country_id')]]."</br>";
						echo "[".$country_short_name[$row[csf('country_id')]]."]"; 
					?>
				</p></td>
				<td width="55" align="center"><? if($row[csf('country_ship_date')]!="0000-00-00") echo change_date_format($row[csf('country_ship_date')]); ?>&nbsp;</td>
				<td align="right" width="65"><?  echo $row[csf('country_qnty')]; ?></td>
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
if($action=="show_dtls_listview")
{
	$location_arr=return_library_array( "SELECT id, location_name from lib_location",'id','location_name');
	$company_arr=return_library_array( "SELECT id, company_name from lib_company",'id','company_name');
	$supplier_arr=return_library_array( "SELECT id, supplier_name from  lib_supplier",'id','supplier_name');
	$sewing_line_arr=return_library_array( "SELECT id, line_name from  lib_sewing_line",'id','line_name');
		
	?>	 
	<div style="width:100%;">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="150" align="center">Item Name</th>
                <th width="110" align="center">Country</th>
                <th width="75" align="center">Delivery Date</th>
                <th width="80" align="center">Delivery Qty</th> 
                <th width="120" align="center">Serving Company</th>
                <th width="" align="center">Location</th>
            </thead>
		</table>
	</div>
	<div style="width:100%;max-height:180px; overflow:y-scroll" id="iron_list_view" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table" id="tbl_list_search">
		<?php  
			$i=1;
			$total_production_qnty=0;
			if($db_type==0)
			{
				$sqlResult =sql_select("SELECT a.id,a.po_break_down_id,a.item_number_id, a.country_id, a.production_date, a.production_quantity, a.reject_qnty, a.production_source, a.serving_company, a.location from pro_garments_production_mst a,pro_gmts_delivery_mst b where b.id=$data and b.id=a.delivery_mst_id and a.production_type='14' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.production_date");
			}
			else
			{
				$sqlResult =sql_select("SELECT a.id,a.po_break_down_id,a.item_number_id, a.country_id, a.production_date, a.production_quantity, a.reject_qnty, a.production_source, a.serving_company, a.location from pro_garments_production_mst a,pro_gmts_delivery_mst b where b.id=$data and b.id=a.delivery_mst_id and a.production_type='14' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.production_date");
			}

			foreach($sqlResult as $selectResult){
				
				if ($i%2==0)  $bgcolor="#E9F3FF";
                else $bgcolor="#FFFFFF";
				$total_production_qnty+=$selectResult[csf('production_quantity')];
 		?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="get_php_form_data('<? echo $selectResult[csf('id')]; ?>','populate_input_form_data','requires/garments_finishing_delivery_entry_controller');" > 
				<td width="30" align="center"><? echo $i; ?></td>
                <td width="150" align="center"><p><? echo $garments_item[$selectResult[csf('item_number_id')]]; ?></p></td>
                <td width="110" align="center"><p>
                	<? 
                		echo $country_library[$selectResult[csf('country_id')]]."</br>"; 
                		echo "[".$country_short_name[$selectResult[csf('country_id')]]."]";
                	?>        		
                	</p></td>
                <td width="75" align="center"><?php echo change_date_format($selectResult[csf('production_date')]); ?></td>
                <td width="80" align="center"><?php  echo $selectResult[csf('production_quantity')]; ?></td>
				<?php
                $source= $selectResult[csf('production_source')];
                if($source==3)
                $serving_company= return_field_value("supplier_name","lib_supplier","id='".$selectResult[csf('serving_company')]."'");
                else
                $serving_company= return_field_value("company_name","lib_company","id='".$selectResult[csf('serving_company')]."'");
                ?>	
                <td width="120" align="center"><p><?php echo $serving_company; ?></p></td>
                <?php 
                $location_name= return_field_value("location_name","lib_location","id='".$selectResult[csf('location')]."'");
                ?>
                <td width="" align="center"><? echo $location_name; ?></td>
			</tr>
			<?php
			$i++;
			}
			?>
             <!--<tfoot>
            	<tr>
                	<th colspan="3"></th>
                    <th><!? echo $total_production_qnty; ?></th>
                    <th colspan="3"></th>
                </tr>
            </tfoot>-->
		</table>
	</div>
	<?
	exit();
}
if($action=="show_country_listview")
{
	?>	
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="380" class="rpt_table">
        <thead>
            <th width="20">SL</th>
            <th width="90">Item Name</th>
            <th width="80">Country</th>
            <th width="55">Shipment Date</th>
            <th width="45">Country Qty.</th>
            <th width="45">Sew.Out</th>
            <th>Finishing Qty.</th>                    
        </thead>
		<?
		$issue_qnty_arr=sql_select("SELECT a.production_type,a.po_break_down_id, a.item_number_id, a.country_id, b.production_qnty from pro_garments_production_mst a, pro_garments_production_dtls b where a.id=b.mst_id and a.po_break_down_id='$data' and a.production_type in(5,8) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		$issue_data_arr=array();
		foreach($issue_qnty_arr as $row)
		{
			$issue_data_arr[$row[csf("production_type")]][$row[csf("po_break_down_id")]][$row[csf("country_id")]][$row[csf("item_number_id")]]+=$row[csf("production_qnty")];
		}  
		$i=1;
		$sqlResult =sql_select("SELECT po_break_down_id, item_number_id, country_id, max(country_ship_date) as country_ship_date, sum(order_quantity) as country_qnty, sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown where po_break_down_id='$data' and status_active=1  and is_deleted=0 group by po_break_down_id, item_number_id, country_id order by country_ship_date");
		foreach($sqlResult as $row)
		{
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$out_qnty=$issue_data_arr[5][$row[csf("po_break_down_id")]][$row[csf("country_id")]][$row[csf("item_number_id")]];
			$issue_qnty=$issue_data_arr[8][$row[csf("po_break_down_id")]][$row[csf("country_id")]][$row[csf("item_number_id")]];
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="put_country_data(<? echo $row[csf('po_break_down_id')].",".$row[csf('item_number_id')].",".$row[csf('country_id')].",".$row[csf('country_qnty')].",".$row[csf('plan_cut_qnty')]; ?>);"> 
				<td width="20"><? echo $i; ?></td>
				<td width="100"><p><? echo $garments_item[$row[csf('item_number_id')]]; ?></p></td>
				<td width="80" align="center"><p>
					<? 
						echo $country_library[$row[csf('country_id')]]."</br>";
						echo "[".$country_short_name[$row[csf('country_id')]]."]"; 
					?>
				</p></td>
				<td width="55" align="center"><? if($row[csf('country_ship_date')]!="0000-00-00") echo change_date_format($row[csf('country_ship_date')]); ?>&nbsp;</td>
				<td align="right" width="65"><?  echo $row[csf('country_qnty')]; ?></td>
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
if($action=="populate_input_form_data")
{
	$data = explode("_",$data);
	if($db_type==0)
	{
		$sqlResult =sql_select("SELECT id, company_id, garments_nature, po_break_down_id, item_number_id, challan_no, country_id,  pack_type, production_source, serving_company, sewing_line, location, produced_by, embel_name, embel_type, production_date, production_quantity, production_source, production_type, entry_break_down_type, break_down_type_rej, TIME_FORMAT( production_hour, '%H:%i' ) as production_hour, sewing_line, supervisor, carton_qty, remarks, floor_id, alter_qnty, spot_qnty, reject_qnty, total_produced, yet_to_produced  from pro_garments_production_mst where id='$data[0]' and production_type='14' and status_active=1 and is_deleted=0 order by id");
	}
	else
	{
		$sqlResult =sql_select("SELECT id, company_id, garments_nature, po_break_down_id, item_number_id, challan_no, country_id,  pack_type, production_source, serving_company, sewing_line, location, produced_by, embel_name, embel_type, production_date, production_quantity, production_source, production_type, entry_break_down_type, break_down_type_rej, TO_CHAR(production_hour,'HH24:MI') as production_hour, sewing_line, supervisor, carton_qty, remarks, floor_id, alter_qnty, spot_qnty, reject_qnty, total_produced, yet_to_produced  from pro_garments_production_mst where id='$data[0]' and production_type='14' and status_active=1 and is_deleted=0 order by id");	
	}
	$company_id=$sqlResult[0][csf('company_id')];
	$po_id=$sqlResult[0][csf('po_break_down_id')];
	$item_id=$sqlResult[0][csf('item_number_id')];
	$country_id=$sqlResult[0][csf('country_id')];
	$country_qnty=return_field_value("sum(order_quantity)","wo_po_color_size_breakdown","po_break_down_id=$po_id and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1");	
	$dissable='';	
	if($sqlResult[0][csf('production_source')]==1)
	{
		$company=$sqlResult[0][csf('serving_company')];
	}
	else
	{
		$company=$sqlResult[0][csf('company_id')];
	}		 
	$control_and_preceding=sql_select("SELECT is_control,preceding_page_id from variable_settings_production where status_active=1 and is_deleted=0 and variable_list=33 and page_category_id=269 and company_name='$company'");  
	$preceding_process= $control_and_preceding[0][csf("preceding_page_id")];
	echo "$('#hidden_variable_cntl').val('".$control_and_preceding[0][csf('is_control')]."');\n";

	$qty_source=8;

	$po_id = $sqlResult[0][csf('po_break_down_id')];
	$sql = sql_select("SELECT a.buyer_name,a.style_ref_no,a.job_no,b.po_quantity,b.po_number from wo_po_details_master a,wo_po_break_down b where a.id=b.job_id and b.id=$po_id and b.status_active=1 and b.is_deleted=0");
	
	foreach($sqlResult as $result)
	{ 
		echo "$('#txt_delivery_date').val('".change_date_format($result[csf('production_date')])."');\n";
		echo "$('#cbo_source').val('".$result[csf('production_source')]."');\n";
		echo "load_drop_down( 'requires/garments_finishing_delivery_entry_controller', ".$result[csf('production_source')].", 'load_drop_down_source', 'finishing_td' );\n";
		echo "$('#cbo_finish_company').val('".$result[csf('serving_company')]."');\n";
		echo "load_drop_down( 'requires/garments_finishing_delivery_entry_controller',".$result[csf('serving_company')].", 'load_drop_down_location', 'location_td' );";
		echo "$('#cbo_location').val('".$result[csf('location')]."');\n";
		echo "load_drop_down( 'requires/garments_finishing_delivery_entry_controller', ".$result[csf('location')].", 'load_drop_down_floor', 'floor_td' );\n";
		echo "$('#cbo_floor').val('".$result[csf('floor_id')]."');\n";
		echo "$('#cbo_country_name').val('".$result[csf('country_id')]."');\n";
		echo "$('#txt_country_qty').val('".$country_qnty."');\n";
		echo "$('#cbo_item_name').val('".$result[csf('item_number_id')]."');\n";
		echo "$('#txt_mst_id').val('".$data[0]."');\n";

		echo "$('#txt_job_no').val('".$sql[0]['JOB_NO']."');\n";
		echo "$('#txt_style_no').val('".$sql[0]['STYLE_REF_NO']."');\n";
		echo "$('#cbo_buyer_name').val('".$sql[0]['BUYER_NAME']."');\n";
		echo "$('#txt_order_qty').val('".$sql[0]['PO_QUANTITY']."');\n";
		echo "$('#txt_order_no').val('".$sql[0]['PO_NUMBER']."');\n";
		echo "$('#hidden_po_break_down_id').val('$po_id');\n";

		if($result[csf('production_source')]==3)
		{
			$company=$sqlResult[0][csf('company_id')];
			$control_and_preceding=sql_select("select is_control,preceding_page_id from variable_settings_production where status_active=1 and is_deleted=0 and variable_list=33 and page_category_id=269 and company_name='$company'");  
			$preceding_process= $control_and_preceding[0][csf("preceding_page_id")];
			echo "$('#hidden_variable_cntl').val('".$control_and_preceding[0][csf('is_control')]."');\n";
			 
		}
 		echo "$('#txt_finishing_qty').val('".$result[csf('production_quantity')]."');\n";
		echo "$('#txt_carton_qty').val('".$result[csf('carton_qty')]."');\n";
  		echo "$('#txt_remark').val('".$result[csf('remarks')]."');\n";
				
		
		$dataSql="SELECT 
		SUM(CASE WHEN production_type='$qty_source' THEN production_quantity END) as FINISHING,
		SUM(CASE WHEN production_type=14 THEN production_quantity ELSE 0 END) as DELIVERY,			 
		SUM(CASE WHEN production_type=10 and trans_type=5 THEN production_quantity ELSE 0 END) as trans_in_qty, 
		SUM(CASE WHEN production_type=10 and trans_type=6 THEN production_quantity ELSE 0 END) as trans_out_qty,  
		SUM(CASE WHEN production_type=84 THEN production_quantity ELSE 0 END) as RCV_RTN
		from pro_garments_production_mst WHERE po_break_down_id=".$result[csf('po_break_down_id')]."  and item_number_id=".$result[csf('item_number_id')]." and country_id=".$result[csf('country_id')]." $pack_typeCond and status_active=1 and is_deleted=0";
	/* 	$dataSql="SELECT 
		SUM(CASE WHEN production_type='$qty_source' THEN production_quantity END) as totalinput,
		SUM(CASE WHEN production_type=14 THEN production_quantity ELSE 0 END) as totalsewing,			 
		SUM(CASE WHEN production_type=10 and trans_type=5 THEN production_quantity ELSE 0 END) as trans_in_qty, 
		SUM(CASE WHEN production_type=10 and trans_type=6 THEN production_quantity ELSE 0 END) as trans_out_qty,  
		SUM(CASE WHEN production_type=84 THEN production_quantity ELSE 0 END) as RCV_RTN
		from pro_garments_production_mst WHERE po_break_down_id=".$result[csf('po_break_down_id')]."  and item_number_id=".$result[csf('item_number_id')]." and country_id=".$result[csf('country_id')]." $pack_typeCond and status_active=1 and is_deleted=0"; */


		$dataArray=sql_select($dataSql);
 		foreach($dataArray as $row)
		{  
			$row['FINISHING'] = $row['FINISHING'];
			echo "$('#txt_finish_input_qty').val('".$row['FINISHING']."');\n";
			echo "$('#txt_cumul_delivery_qty').val('".$row['DELIVERY']."');\n";			
			$yet_to_produced = $row['FINISHING']-$row['DELIVERY']+($row[csf('trans_in_qty')]-$row[csf('trans_out_qty')]+$row['RCV_RTN']);
			echo "$('#txt_yet_to_delivery').val('".$yet_to_produced."');\n";
			$yettoTitel="Total Finish Qty:".$row['FINISHING']."-Total Delivery Qty:".$row['DELIVERY']."+( Trans In:".$row[csf('trans_in_qty')]."- Trans Out:".$row[csf('trans_out_qty')]."+ Rec. Return:".$row['RCV_RTN'].")";
			echo "$('#txt_yet_to_delivery').attr('title','".$yettoTitel."');\n";
		}		
		
 		echo "set_button_status(1, permission, 'fnc_finishing_entry',1,1);\n";
		
		 
		$color_library=return_library_array( "select id, color_name from lib_color",'id','color_name');
		$size_library=return_library_array( "select id, size_name from lib_size",'id','size_name');

		$variableSettings = $result[csf('entry_break_down_type')];
		$variableSettingsRej = $result[csf('break_down_type_rej')];
		
		 
		if($pack_type=='') $pack_typeCond=""; else $pack_typeCond="and pack_type='$pack_type'";
		if($pack_type!='') $pack_cond="and b.pack_type='$pack_type'"; else $pack_cond="";
		if($pack_type=='') $packTypecond=""; else $packTypecond="and a.pack_type='$pack_type'";
		
		if( $variableSettings!=1 ) // gross level
		{ 
			$po_id = $result[csf('po_break_down_id')];
			$item_id = $result[csf('item_number_id')];
			$country_id = $result[csf('country_id')];
			
			$sql_dtls = sql_select("SELECT color_size_break_down_id, production_qnty, reject_qty, size_number_id, color_number_id from  pro_garments_production_dtls a,wo_po_color_size_breakdown b where a.mst_id=$data[0] and a.status_active=1 and a.color_size_break_down_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.status_active =1 and b.is_deleted=0 and country_id='$country_id' $pack_cond");	
			foreach($sql_dtls as $row)
			{				  
				if( $variableSettings==2 ) $index = $row[csf('color_number_id')]; else $index = $row[csf('size_number_id')].$color_arr[$row[csf("color_number_id")]].$row[csf('color_number_id')];
			  	$amountArr[$index] = $row[csf('production_qnty')];
				$rejectArr[$index] = $row[csf('reject_qty')];
			}  
			//print_r($amountArr);
			
			
			$color_size_qty_arr=array();
			$color_size_sql=sql_select ("SELECT  po_break_down_id,item_number_id,size_number_id,color_number_id,sum(plan_cut_qnty) as plan_cut_qnty  from wo_po_color_size_breakdown where status_active =1 and  is_deleted=0    and  po_break_down_id in (".str_replace("'","",$po_id).") and item_number_id=$item_id $pack_typeCond group by po_break_down_id,item_number_id,size_number_id,color_number_id");
			foreach($color_size_sql as $s_id)
			{
				$color_size_qty_arr[$s_id[csf('color_number_id')]][$s_id[csf('size_number_id')]]+=$s_id[csf('plan_cut_qnty')]; 
			}
			
			if( $variableSettings==2 ) // color level
			{			 
			 	$sql = "SELECT a.item_number_id, a.color_number_id, 
			 	sum(a.order_quantity) as order_quantity, 
			 	sum(a.plan_cut_qnty) as plan_cut_qnty,
			 	sum(CASE WHEN c.production_type='$qty_source' then b.production_qnty ELSE 0 END) as production_qnty,
			 	sum(CASE WHEN c.production_type=14 then b.production_qnty ELSE 0 END) as cur_production_qnty,
			 	sum(CASE WHEN c.production_type=14 then b.reject_qty ELSE 0 END) as reject_qty, 
			 	sum(CASE WHEN c.production_type=10 and c.trans_type=5 then b.reject_qty ELSE 0 END) as trans_in_qty, 
			 	sum(CASE WHEN c.production_type=10 and c.trans_type=6 then b.reject_qty ELSE 0 END) as trans_out_qty,
				SUM(CASE WHEN c.production_type=84 THEN b.production_quantity ELSE 0 END) as RCV_RTN 
			 	from wo_po_color_size_breakdown a left join pro_garments_production_dtls b on a.id=b.color_size_break_down_id left join pro_garments_production_mst c on c.id=b.mst_id where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active=1   
				group by a.item_number_id, a.color_number_id";	

			 	$sql_plan_cut="SELECT color_number_id, sum(plan_cut_qnty) as quantity from wo_po_color_size_breakdown where po_break_down_id='$po_id' and item_number_id='$item_id' and  country_id='$country_id' and status_active=1 and is_deleted=0 group by color_number_id";
			 	foreach(sql_select($sql_plan_cut) as $key=>$value)
			 	{
			 		$plan_cut_arr[$value[csf("color_number_id")]] +=$value[csf("quantity")];
			 	}
			 	
			}
			else if( $variableSettings==3 ) //color and size level
			{				

				$dtlsData = "SELECT a.color_size_break_down_id,
				sum(CASE WHEN a.production_type='$qty_source' then a.production_qnty ELSE 0 END) as production_qnty,
				sum(CASE WHEN a.production_type=14 then a.production_qnty ELSE 0 END) as cur_production_qnty,
				sum(CASE WHEN a.production_type=14 and b.id=$data[0] then a.reject_qty ELSE 0 END) as reject_qty,  
				sum(CASE WHEN a.production_type=10 and b.trans_type=5 then a.production_qnty ELSE 0 END) as trans_in_qty,  
				sum(CASE WHEN a.production_type=10 and b.trans_type=6 then a.production_qnty ELSE 0 END) as trans_out_qty,
				SUM(CASE WHEN a.production_type=84 THEN a.production_quantity ELSE 0 END) as RCV_RTN   
				from pro_garments_production_dtls a, pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' $pack_cond and a.color_size_break_down_id!=0 and a.production_type in($qty_source,14,10,84) group by a.color_size_break_down_id";

				$dtlsData=sql_select($dtlsData);
									
				foreach($dtlsData as $row)
				{				  
					if($qty_source)
					{
						$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['iss']= $row[csf('production_qnty')];
					}
					else
					{
						foreach($dtlsData_colsize as $rows)
						{
							$color_size_qnty_array[$rows[csf('color_size_break_down_id')]]['iss']= $rows[csf('production_qnty')];
						}
					}
					
 					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv']= $row[csf('cur_production_qnty')];
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rej']= $row[csf('reject_qty')];
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['in']= $row[csf('trans_in_qty')];
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['out']= $row[csf('trans_out_qty')];
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv_rtn']= $row['RCV_RTN'];
				} 
				
				$sql = "SELECT id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty from wo_po_color_size_breakdown where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' $pack_typeCond and is_deleted=0 and status_active =1 order by color_number_id,size_order";
			}
			else // by default color and size level
			{
				$dtlsData = sql_select("SELECT a.color_size_break_down_id,
					sum(CASE WHEN a.production_type='$qty_source' then a.production_qnty ELSE 0 END) as production_qnty,
					sum(CASE WHEN a.production_type=14 then a.production_qnty ELSE 0 END) as cur_production_qnty,
					sum(CASE WHEN a.production_type=14 then a.reject_qty ELSE 0 END) as reject_qty, 
					sum(CASE WHEN a.production_type=10 and b.trans_type=5 then a.production_qnty ELSE 0 END) as trans_in_qty,  
					sum(CASE WHEN a.production_type=10 and b.trans_type=6 then a.production_qnty ELSE 0 END) as trans_out_qty,
					SUM(CASE WHEN a.production_type=84 THEN a.production_quantity ELSE 0 END) as RCV_RTN
					from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in($qty_source,14,10,84) group by a.color_size_break_down_id");
									
				foreach($dtlsData as $row)
				{				  
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['iss']= $row[csf('production_qnty')];
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv']= $row[csf('cur_production_qnty')];
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rej']= $row[csf('reject_qty')];
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['in']= $row[csf('trans_in_qty')];
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['out']= $row[csf('trans_out_qty')];
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['rcv_rtn']= $row['RCV_RTN'];
				} 
				
				$sql="SELECT id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty from wo_po_color_size_breakdown where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' $pack_cond and is_deleted=0 and status_active =1  order by color_number_id,size_order";
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
					$production_quantity=$color[csf("production_qnty")];
					$production_rcv_rtn=$color["RCV_RTN"];
					$amount = $amountArr[$color[csf("color_number_id")]];
					$rejectAmt = $rejectArr[$color[csf("color_number_id")]];
					$order_rate=$con_per_dzn[$color[csf("color_number_id")]]/$costing_per_qty;
					$colorHTML .='<tr><td>'.$color_library[$color[csf("color_number_id")]].'</td><td><input type="text" name="txt_color" id="colSize_'.($i+1).'" style="width:60px"  class="text_boxes_numeric" placeholder="'.($production_quantity+$color[csf("trans_in_qty")]-$color[csf("trans_out_qty")]-$color[csf("cur_production_qnty")]+$color["RCV_RTN"]+$amount).'" value="'.$amount.'" onblur="fn_colorlevel_total('.($i+1).')" '.$disable_for_posted.'></td><td></td></tr>';					$fabric_amount_total+=$amount*$order_rate;
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
					
					$iss_qnty=$color_size_qnty_array[$color[csf('id')]]['iss'];
					$rcv_qnty=$color_size_qnty_array[$color[csf('id')]]['rcv'];
					$rej_qnty=$color_size_qnty_array[$color[csf('id')]]['rej'];
					$trans_in_qty=$color_size_qnty_array[$color[csf('id')]]['in'];
					$trans_out_qty=$color_size_qnty_array[$color[csf('id')]]['out'];
					$rcv_rtn=$color_size_qnty_array[$color[csf('id')]]['rcv_rtn'];
					$order_rate=$con_per_dzn[$color[csf("color_number_id")]][$color[csf("size_number_id")]]/$costing_per_qty;
					
					$colorHTML .='<tr><td>'.$size_library[$color[csf("size_number_id")]].'</td><td><input type="text" name="colorSize" id="colSize_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:80px" placeholder="'.($iss_qnty+$trans_in_qty-$trans_out_qty-$rcv_qnty+$rcv_rtn+$amount).'" onblur="fn_total('.$color[csf("color_number_id")].','.($i+1).')" value="'.$amount.'" '.$disable_for_posted.' ><input type="text" name="colorSizePOQnty" id="colorSizePOQnty_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:50px" value="'.$color[csf("order_quantity")].'" readonly disabled></td></tr>';				
					$colorWiseTotal += $amount;
					$fabric_amount_total+=$amount*$order_rate;
				}
				$i++; 
			}
			//echo $colorHTML;die; 
			if( $variableSettings==2 ){ $colorHTML = '<table id="table_color" class="rpt_table"><thead><tr><th width="70">Color</th><th width="60">Quantity</th><th width="60">Reject</th></tr><tr> <th colspan="2"><div style="padding-left: 30px;text-align:left"><input type="checkbox" onClick="active_placeholder_qty_color(' . $color[csf("color_number_id")] . ')" id="set_all">&nbsp;<label for="set_all">Available Qty Auto Fill</label></div></th> </tr></thead><tbody>'.$colorHTML.'<tbody><tfoot><tr><th>Total</th><th><input type="text" id="total_color" placeholder="'.$totalQnty.'" value="'.$totalQnty.'" class="text_boxes_numeric" style="width:60px" ></th><th><input type="text" id="total_color_rej" placeholder="'.$totalRejQnty.'" value="'.$totalRejQnty.'" class="text_boxes_numeric" style="width:60px" ></th></tr></tfoot></table>'; }
			echo "$('#breakdown_td_id').html('".addslashes($colorHTML)."');\n";
			if( $variableSettings==3 )echo "$totalFn;\n";
			$colorList = substr($colorID,0,-1);
			echo "$('#hidden_colorSizeID').val('".$colorList."');\n";
			echo "$('#fabric_data').val('".($fabric_amount_total)."');\n";
		}
		else
		{
			$po_id = $result[csf('po_break_down_id')];
			$item_id = $result[csf('item_number_id')];
			$country_id = $result[csf('country_id')];

			$color_size_qty_arr=array();
			$color_size_sql=sql_select ("SELECT  po_break_down_id,item_number_id,size_number_id,color_number_id,sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown where  is_deleted=0 and status_active =1    and  po_break_down_id in (".str_replace("'","",$po_id).") and item_number_id=$item_id group by po_break_down_id,item_number_id,size_number_id,color_number_id");
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
	$is_projected_po_allow=return_field_value("production_entry","variable_settings_production","variable_list=58 and company_name=$cbo_company_name");
	if($is_projected_po_allow ==2)
	{
		$is_projected_po=return_field_value("is_confirmed","wo_po_break_down","status_active =1 and id=$hidden_po_break_down_id");
		if($is_projected_po==2)
		{			
			echo "786**Projected PO is not allowed to production. Please check variable settings";die();
		}
	}
	
	if(!str_replace("'","",$sewing_production_variable)) $sewing_production_variable=3;
 	$control_and_preceding=sql_select("select is_control,preceding_page_id from variable_settings_production where status_active=1 and is_deleted=0 and variable_list=33 and page_category_id=269 and company_name=$cbo_company_name");  
    $is_control=$control_and_preceding[0][csf("is_control")];
    $preceding_process=$control_and_preceding[0][csf("preceding_page_id")];
	$qty_source=5;
	if($preceding_process==28) $qty_source=4; //Sewing Input
	else if($preceding_process==29) $qty_source=5;//Sewing Output
	else if($preceding_process==30) $qty_source=7;//Iron Output
	else if($preceding_process==31) $qty_source=8;//Packing And Finishing
	else if($preceding_process==32) $qty_source=7;//Iron Output
	else if($preceding_process==91) $qty_source=7;//Iron Output
	else if($preceding_process==103) $qty_source=11;//Poly Entry	
	 
	if($variable_qty_source_packing==1) $qty_source=7; //Iron Output
	else if($variable_qty_source_packing==2) $qty_source=11;//Poly Output
	$qty_source = 8;
	//echo "10**".$qty_source;
	if ($operation!=0)
	{
		if(str_replace("'","",$txt_pack_type)=="") $packType_cond=""; else $packType_cond=" and pack_type=$txt_pack_type";
		if(str_replace("'","",$hidden_ship_date)=="") $ship_date_cond=""; else $ship_date_cond=" and a.country_ship_date=$hidden_ship_date";
		$backValisql = "SELECT  b.color_size_break_down_id,
					sum(CASE WHEN b.production_type='14' and b.mst_id=$txt_mst_id then b.production_qnty ELSE 0 END) as findelvqty,
					sum(CASE WHEN b.production_type='81' and c.challan_id=$txt_system_id then b.production_qnty ELSE 0 END) as finrec
					from wo_po_color_size_breakdown a left join pro_garments_production_dtls b on a.id=b.color_size_break_down_id left join PRO_GMTS_DELIVERY_MST c on c.id=b.delivery_mst_id
					where a.po_break_down_id=$hidden_po_break_down_id and a.item_number_id=$cbo_item_name and a.country_id=$cbo_country_name $ship_date_cond $pack_type_cond and a.is_deleted=0 and a.status_active =1 and b.production_type in(14,81) group by b.color_size_break_down_id";
		//echo "10**".$backValisql; die;
		$backValiResult = sql_select($backValisql);
		$nxtQtyArr=array();
		foreach ($backValiResult as $row) {
			$nxtQtyArr[$row[csf("color_size_break_down_id")]]['finDelQty']+=$row[csf("findelvqty")];
			$nxtQtyArr[$row[csf("color_size_break_down_id")]]['finrec']+=$row[csf("finrec")];
		}
		unset($backValiResult);
	}
	//die;
	
	if ($operation==0) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		//table lock here 
		//if  ( check_table_status( 160, 1 )==0 ) { echo "15**0"; die;}
 		
		
		//----------Compare by finishing qty and iron qty qty for validation----------------
		$txt_finishing_qty=str_replace("'","",$txt_finishing_qty);
		if($txt_finishing_qty=='')$txt_finishing_qty=0;
		$is_fullshipment=return_field_value("shiping_status","wo_po_break_down","id=$hidden_po_break_down_id");
		if($is_fullshipment==3)
		{
			echo "505";disconnect($con);die;
		}
		
		if($is_control==1 && $user_level!=2)
		{
			if(str_replace("'","",$txt_pack_type)=="") $packType_cond=""; else $packType_cond=" and pack_type=$txt_pack_type";
			$country_iron_qty=return_field_value("sum(production_quantity)","pro_garments_production_mst","po_break_down_id=$hidden_po_break_down_id and production_type='$qty_source' and country_id=$cbo_country_name $packType_cond and status_active=1 and is_deleted=0");

			$transfer_in_qty=return_field_value("sum(production_quantity)","pro_garments_production_mst","po_break_down_id=$hidden_po_break_down_id and production_type='10' and country_id=$cbo_country_name $packType_cond AND trans_type = 5 and status_active=1 and is_deleted=0");

			$transfer_out_qty=return_field_value("sum(production_quantity)","pro_garments_production_mst","po_break_down_id=$hidden_po_break_down_id and production_type='10' and country_id=$cbo_country_name $packType_cond AND trans_type = 6 and status_active=1 and is_deleted=0");
			
			$country_finishing_qty=return_field_value("sum(production_quantity)","pro_garments_production_mst","po_break_down_id=$hidden_po_break_down_id and production_type=14 and country_id=$cbo_country_name $packType_cond and status_active=1 and is_deleted=0");
			
			$rcv_rtn_qty=return_field_value("sum(production_quantity)","pro_garments_production_mst","po_break_down_id=$hidden_po_break_down_id and production_type=84 and country_id=$cbo_country_name $packType_cond and status_active=1 and is_deleted=0");
		
			// echo "25**0".$country_iron_qty."+".$transfer_in_qty." < ".$country_finishing_qty."+".$txt_finishing_qty."+".$transfer_out_qty;die;
			if(($country_iron_qty+$transfer_in_qty+$rcv_rtn_qty) < ($country_finishing_qty+$txt_finishing_qty+$transfer_out_qty))
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

          	$new_sys_number = explode("*", return_next_id_by_sequence("", "pro_gmts_delivery_mst",$con,1,$cbo_company_name,'GFDE',463,date("Y",time()),0,0,14,0,0 ));
          	$field_array_delivery = "id, sys_number_prefix, sys_number_prefix_num, sys_number, company_id, production_type, production_source, delivery_date,entry_form,working_company_id,location_id,floor_id, inserted_by, insert_date";
            $mst_id = return_next_id_by_sequence(  "pro_gmts_delivery_mst_seq", "pro_gmts_delivery_mst", $con );
            $data_array_delivery = "(" . $mst_id . ",'" . $new_sys_number[1] . "','" .(int) $new_sys_number[2] . "','" . $new_sys_number[0] . "', " . $cbo_company_name . ",14," . $cbo_source . "," . $txt_delivery_date . ",463,".$cbo_finish_company.",".$cbo_location."," . $cbo_floor . "," . $user_id . ",'" . $pc_date_time . "')";
            $challan_no =(int) $new_sys_number[2];
            $txt_system_no = $new_sys_number[0];

        } 
        else 
        {
            $mst_id = str_replace("'", "", $txt_system_id);
            $txt_chal_no = explode("-", str_replace("'", "", $txt_system_no));
            $challan_no = (int)$txt_chal_no[3];

            $field_array_delivery = "company_id*production_source*floor_id*delivery_date*working_company_id*location_id*updated_by*update_date";
            $data_array_delivery = "" . $cbo_company_name . "*" . $cbo_source . "*" . $cbo_floor . "*" . $txt_delivery_date . "*" . $cbo_finish_company . "*" . $cbo_location . "*" . $user_id . "*'" . $pc_date_time . "'";

        }
        // echo "10**".$new_sys_number[0];
		//$id=return_next_id("id", "pro_garments_production_mst", 1);


		$id= return_next_id_by_sequence(  "pro_gar_production_mst_seq",  "pro_garments_production_mst", $con );
		
  		$field_array1="id, garments_nature, company_id, challan_no, po_break_down_id, item_number_id, country_id,  production_source, serving_company, location, production_date, production_quantity, production_type, entry_break_down_type, break_down_type_rej, carton_qty, remarks, floor_id, total_produced, yet_to_produced,delivery_mst_id, inserted_by, insert_date"; 
		if($db_type==0)
		{
			$data_array1="(".$id.",".$garments_nature.",".$cbo_company_name.",".$challan_no.",".$hidden_po_break_down_id.",".$cbo_item_name.",".$cbo_country_name.",".$cbo_source.",".$cbo_finish_company.",".$cbo_location.",".$txt_delivery_date.",".$txt_finishing_qty.",14,".$sewing_production_variable.",".$finish_production_variable_rej.",".$txt_carton_qty.",".$txt_remark.",".$cbo_floor.",".$txt_cumul_delivery_qty.",".$txt_yet_to_delivery.",".$mst_id.",".$user_id.",'".$pc_date_time."')";

		}
	  	else if($db_type==2)
		{
			$data_array1="INSERT INTO pro_garments_production_mst (".$field_array1.") values(".$id.",".$garments_nature.",".$cbo_company_name.",".$challan_no.",".$hidden_po_break_down_id.",".$cbo_item_name.",".$cbo_country_name.",".$cbo_source.",".$cbo_finish_company.",".$cbo_location.",".$txt_delivery_date.",".$txt_finishing_qty.",14,".$sewing_production_variable.",".$finish_production_variable_rej.",".$txt_carton_qty.",".$txt_remark.",".$cbo_floor.",".$txt_cumul_delivery_qty.",".$txt_yet_to_delivery.",".$mst_id.",".$user_id.",'".$pc_date_time."')";
		}
 		//$rID=sql_insert("pro_garments_production_mst",$field_array1,$data_array1,1);
		//echo $data_array;die;
		
		// pro_garments_production_dtls table entry here ----------------------------------///
		$field_array="id, mst_id, production_type, color_size_break_down_id, production_qnty, reject_qty,delivery_mst_id";
		if(str_replace("'","",$txt_pack_type)=="") $pack_type_cond=""; else $pack_type_cond=" and b.pack_type=$txt_pack_type";
		$dtlsData = sql_select("SELECT a.color_size_break_down_id,
										sum(CASE WHEN a.production_type='$qty_source' then a.production_qnty ELSE 0 END) as production_qnty,
										sum(CASE WHEN a.production_type=14 then a.production_qnty ELSE 0 END) as cur_production_qnty 
										from pro_garments_production_dtls a,pro_garments_production_mst b 
										where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id=$hidden_po_break_down_id and b.item_number_id=$cbo_item_name and b.country_id=$cbo_country_name and a.color_size_break_down_id<>0 and a.production_type in($qty_source,14) $pack_type_cond
										group by a.color_size_break_down_id");
		$color_pord_data=array();							
		foreach($dtlsData as $row)
		{				  
			$color_pord_data[$row[csf("color_size_break_down_id")]]=$row[csf('production_qnty')]-$row[csf("cur_production_qnty")];
		}
  		if(str_replace("'","",$txt_pack_type)=="") $packType_cond=""; else $packType_cond=" and pack_type=$txt_pack_type"; 
		if(str_replace("'","",$sewing_production_variable)==2)//color level wise
		{		
			$color_sizeID_arr=sql_select( "SELECT id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name  and status_active =1 and is_deleted=0  and country_id=$cbo_country_name  $packType_cond order by id" );
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
				/*if($is_control==1 && $user_level!=2)
				{
					if($colorSizeNumberIDArr[1]>0)
					{
						if(($colorSizeNumberIDArr[1]*1)>($color_pord_data[$colSizeID_arr[$colorSizeNumberIDArr[0]]]*1))
						{
							echo "35**Production Quantity Not Over Iron Qnty";
							//check_table_status( 160,0);
							disconnect($con);
							die;
						}
					}
				}*/
				
				//8 for Garments Finishing Entry
				$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",  "pro_garments_production_dtls", $con );
				if($j==0)$data_array = "(".$dtls_id.",".$id.",14,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."','".$rejQtyArr[$colorSizeNumberIDArr[0]]."','".$mst_id."')";
				else $data_array .= ",(".$dtls_id.",".$id.",14,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."','".$rejQtyArr[$colorSizeNumberIDArr[0]]."','".$mst_id."')";
				//$dtls_id=$dtls_id+1;							
 				$j++;								
			}
 		}//color level wise
		
		if(str_replace("'","",$sewing_production_variable)==3)//color and size wise
		{		
			$color_sizeID_arr=sql_select( "SELECT id,size_number_id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name  and status_active =1 and is_deleted=0  and country_id=$cbo_country_name $packType_cond order by size_number_id,color_number_id" );
			$colSizeID_arr=array(); 
			foreach($color_sizeID_arr as $val){
				$index = $val[csf("size_number_id")].$color_arr[$val[csf("color_number_id")]].$val[csf("color_number_id")];
				$colSizeID_arr[$index]=$val[csf("id")];
			}	
			//	colorIDvalue concate as sizeID*colorID*value***sizeID*colorID*value	--------------------------// 
			$rowExRej = explode("***",$colorIDvalueRej);
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
							echo "35**Production Quantity Not Over Iron Qnty";
							//check_table_status( 160,0);
							disconnect($con);
							die;
						}
					}
				}*/
				
 				
				//8 for Garments Finishing Entry
				$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",   "pro_garments_production_dtls", $con );
				if($j==0)$data_array = "(".$dtls_id.",".$id.",14,'".$colSizeID_arr[$index]."','".$colorSizeValue."','".$rejQtyArr[$index]."','".$mst_id."')";
				else $data_array .= ",(".$dtls_id.",".$id.",14,'".$colSizeID_arr[$index]."','".$colorSizeValue."','".$rejQtyArr[$index]."','".$mst_id."')";
				//$dtls_id=$dtls_id+1;
 				$j++;
			}
		}//color and size wise
		
		if (str_replace("'", "", $txt_system_id) == "") 
		{
            $challanrID = sql_insert("pro_gmts_delivery_mst", $field_array_delivery, $data_array_delivery, 1);
        } 
        else 
        {
            $challanrID = sql_update("pro_gmts_delivery_mst", $field_array_delivery, $data_array_delivery, "id", $txt_system_id, 1);
        }

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
		// echo "10**".$data_array1;die();
		// echo "10**insert into pro_garments_production_mst (".$field_array1.") values ".$data_array1;die;
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
		$is_fullshipment=return_field_value("shiping_status","wo_po_break_down","id=$hidden_po_break_down_id");
		if($is_fullshipment==3)
		{
			echo "505";disconnect($con);die;
		}
		
		//--------------------------------------------------------------Compare end;
		$field_array_delivery = "company_id*production_source*floor_id*delivery_date*working_company_id*location_id*updated_by*update_date";
        $data_array_delivery = "" . $cbo_company_name . "*" . $cbo_source . "*" . $cbo_floor . "*" . $txt_delivery_date . "*".$cbo_finish_company."*".$cbo_location."*" . $user_id . "*'" . $pc_date_time . "'";
		
		
		// pro_garments_production_mst table data entry here 
		
 		$field_array1="production_source*serving_company*location*production_date*production_quantity*entry_break_down_type*break_down_type_rej*carton_qty*floor_id*total_produced*yet_to_produced*updated_by*update_date";
		
		$data_array1="".$cbo_source."*".$cbo_finish_company."*".$cbo_location."*".$txt_delivery_date."*".$txt_finishing_qty."*".$sewing_production_variable."*".$finish_production_variable_rej."*".$txt_carton_qty."*".$cbo_floor."*".$txt_cumul_delivery_qty."*".$txt_yet_to_delivery."*".$user_id."*'".$pc_date_time."'";
				
		
		if(str_replace("'","",$sewing_production_variable)!=1 && str_replace("'","",$txt_mst_id)!='' ) //  not gross level
		{
			
			$dtlsData = sql_select("SELECT a.color_size_break_down_id,
										sum(CASE WHEN a.production_type='$qty_source' then a.production_qnty ELSE 0 END) as production_qnty,
										sum(CASE WHEN a.production_type=14 then a.production_qnty ELSE 0 END) as cur_production_qnty 
										from pro_garments_production_dtls a,pro_garments_production_mst b 
										where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id=$hidden_po_break_down_id and b.item_number_id=$cbo_item_name and b.country_id=$cbo_country_name and a.color_size_break_down_id<>0 and a.production_type in($qty_source,14) and b.id !=$txt_mst_id 
										group by a.color_size_break_down_id");
			$color_pord_data=array();							
			foreach($dtlsData as $row)
			{				  
				$color_pord_data[$row[csf("color_size_break_down_id")]]=$row[csf('production_qnty')]-$row[csf("cur_production_qnty")];
			}
			
			
 			$field_array="id, mst_id, production_type, color_size_break_down_id, production_qnty, reject_qty,delivery_mst_id";
			
			if(str_replace("'","",$sewing_production_variable)==2)//color level wise
			{		
				$color_sizeID_arr=sql_select( "SELECT id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name  and status_active =1 and is_deleted=0  order by id" );
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
					
					$nxt_process_qty=$nxtQtyArr[$colSizeID_arr[$colorSizeNumberIDArr[0]]]['finrec']*1;
					$current_priv_qty=($nxtQtyArr[$colSizeID_arr[$colorSizeNumberIDArr[0]]]['finDelQty']*1)+($colorSizeNumberIDArr[1]*1);
					
					if( $nxt_process_qty > $current_priv_qty)
					{
						echo "36**Gmts Finishing Delivery Qty is not less then Finish Gmts Receive Qty.";
						//check_table_status( 160,0);
						disconnect($con);
						die;
					}
					
					$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",  "pro_garments_production_dtls", $con );
					if($j==0)$data_array = "(".$dtls_id.",".$txt_mst_id.",14,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."','".$rejQtyArr[$colorSizeNumberIDArr[0]]."','".$txt_system_id."')";
					else $data_array .= ",(".$dtls_id.",".$txt_mst_id.",14,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."','".$rejQtyArr[$colorSizeNumberIDArr[0]]."','".$txt_system_id."')";
					$j++;								
				}
			}
			
			if(str_replace("'","",$sewing_production_variable)==3)//color and size wise
			{		
				$color_sizeID_arr=sql_select( "SELECT id,size_number_id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name and status_active =1 and is_deleted=0 order by size_number_id,color_number_id" );
				$colSizeID_arr=array(); 
				foreach($color_sizeID_arr as $val){
					$index = $val[csf("size_number_id")].$color_arr[$val[csf("color_number_id")]].$val[csf("color_number_id")];
					$colSizeID_arr[$index]=$val[csf("id")];
				}	
				
				//	colorIDvalue concate as sizeID*colorID*value***sizeID*colorID*value	--------------------------// 
				$rowExRej = explode("***",$colorIDvalueRej);
				foreach($rowExRej as $rowR=>$valR)
				{
					$colorAndSizeRej_arr = explode("*",$valR);
					$sizeID = $colorAndSizeRej_arr[0];
					$colorID = $colorAndSizeRej_arr[1];				
					$colorSizeRej = $colorAndSizeRej_arr[2];
					$index = $sizeID.$color_arr[$colorID].$colorID;
					$rejQtyArr[$index]=$colorSizeRej;
				}
				
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
					if($is_control==1 && $user_level!=2)
					{
						if($colorSizeValue>0)
						{
							if(($colorSizeValue*1)>($color_pord_data[$colSizeID_arr[$index]]*1))
							{
								echo "35**Production Quantity Not Over Iron Qnty";
								//check_table_status( 160,0);
								disconnect($con);
								die;
							}
						}
					}
					
					$nxt_process_qty=$nxtQtyArr[$colSizeID_arr[$index]]['finrec']*1;
					//$current_priv_qty=($nxtQtyArr[$colSizeID_arr[$index]]['finDelQty']*1)+($colorSizeValue*1);
					
					//echo "10**".$nxt_process_qty.'-'.$nxtQtyArr[$colSizeID_arr[$index]]['packfinqty'].'-'.$colorSizeValue; die;
					
					if( $nxt_process_qty > $colorSizeValue)
					{
						echo "36**Gmts Finishing Delivery Qty is not less then Finish Gmts Receive Qty.";
						//check_table_status( 160,0);
						disconnect($con);
						die;
					}
					
					//8 for Garments Finishing Entry
					$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",   "pro_garments_production_dtls", $con );
					if($j==0)$data_array = "(".$dtls_id.",".$txt_mst_id.",14,'".$colSizeID_arr[$index]."','".$colorSizeValue."','".$rejQtyArr[$index]."','".$txt_system_id."')";
					else $data_array .= ",(".$dtls_id.",".$txt_mst_id.",14,'".$colSizeID_arr[$index]."','".$colorSizeValue."','".$rejQtyArr[$index]."','".$txt_system_id."')";
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
				//echo "10**".str_replace("'","",$hidden_po_break_down_id); 
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
				//echo "10**".str_replace("'","",$hidden_po_break_down_id); 
			}
		}
		disconnect($con);
		die;
	}
}

if($action=="delivery_entry_print")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	//print_r ($data);
	$company_library=return_library_array("select id, company_name from lib_company", "id", "company_name");
	$country_library=return_library_array("select id, country_name from lib_country", "id", "country_name");
	$supplier_library=return_library_array("select id,supplier_name from lib_supplier", "id","supplier_name");
	$buyer_library=return_library_array("select id, short_name from lib_buyer", "id", "short_name");
	$sewing_library=return_library_array("select id, line_name from lib_sewing_line", "id", "line_name");
	$floor_library=return_library_array("select id, floor_name from lib_prod_floor", "id", "floor_name");
	
	$sql="SELECT a.id,a.inserted_by, b.company_id, b.challan_no, b.po_break_down_id, b.item_number_id, b.entry_break_down_type, b.country_id, b.production_source, b.serving_company, b.location,  b.production_date, b.production_quantity, b.carton_qty, b.production_type, b.remarks, b.floor_id from pro_gmts_delivery_mst a, pro_garments_production_mst b where a.id=b.delivery_mst_id and b.production_type=14 and a.id='$data[1]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ";
	//echo $sql;
	$dataArray = sql_select($sql);
	$break_down_type_reject=$dataArray[0][csf('break_down_type_rej')];
	$entry_break_down_type=$dataArray[0][csf('entry_break_down_type')];
	$po_id_array = array();
	foreach ($dataArray as $val) 
	{
		$po_id_array[$val['PO_BREAK_DOWN_ID']] = $val['PO_BREAK_DOWN_ID'];
	}
	$po_id_cond = where_con_using_array($po_id_array,0,"id");
	$order_library=return_library_array( "SELECT id, po_number from  wo_po_break_down where status_active=1 $po_id_cond", "id", "po_number"  );
	?>
	<div style="width:930px;">
	    <table width="900" cellspacing="0" align="right">
	        <tr>
	            <td colspan="6" align="center" style="font-size:xx-large"><strong><? echo $company_library[$data[0]]; ?></strong></td>
	        </tr>
	        <tr class="form_caption">
	        	<td colspan="6" align="center" style="font-size:14px">  
					<?
						$nameArray=sql_select( "SELECT plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]"); 
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
	            <td colspan="6" align="center" style="font-size:x-large"><strong><? echo $data[2];  ?> Challan</strong></td>
	        </tr>
	        <tr>
				<?
	                $supp_add=$dataArray[0][csf('serving_company')];
	                $nameArray=sql_select( "SELECT address_1,web_site,email,country_id from lib_supplier where id=$supp_add"); 
	                foreach ($nameArray as $result)
	                { 
	                    $address="";
	                    if($result!="") $address=$result[csf('address_1')];//.'<br>'.$country_arr[$result['country_id']].'<br>'.$result['email'].'<br>'.$result['web_site']
	                }
					//echo $address;
					foreach($dataArray as $row)
					{
						$job_no=return_field_value("h.job_no"," wo_po_break_down f, wo_po_details_master h","f.job_no_mst=h.job_no and f.id=".$row[csf("po_break_down_id")],"job_no");
						$buyer_val=return_field_value("h.buyer_name"," wo_po_break_down f, wo_po_details_master h","f.job_no_mst=h.job_no and f.id=".$row[csf("po_break_down_id")],"buyer_name");
						$style_val=return_field_value("h.style_ref_no"," wo_po_break_down f, wo_po_details_master h","f.job_no_mst=h.job_no and f.id=".$row[csf("po_break_down_id")],"style_ref_no");
						$order_qty=return_field_value("po_quantity"," wo_po_break_down","id=".$row[csf("po_break_down_id")],"po_quantity");
					}
	            ?>
	            <td><strong>Challan No:</strong></td> <td width="175px"><? echo $dataArray[0][csf('challan_no')]; ?></td> 
	        	
	            <td width="125"><strong>Order No :</strong></td><td width="175px"><? echo $order_library[$dataArray[0][csf('po_break_down_id')]]; ?></td>
	            <td width="125"><strong>Buyer:</strong></td><td width="175px"><? echo $buyer_library[$buyer_val]; ?></td>
	        </tr>
	        <tr>
	        	<td width="270" valign="top" colspan="2"><strong>Issue To : <? if($dataArray[0][csf('production_source')]==1) echo $company_library[$dataArray[0][csf('serving_company')]]; else if($dataArray[0][csf('production_source')]==3) echo $supplier_library[$dataArray[0][csf('serving_company')]].'<br>'.$address;  ?></strong></td>
	            <td><strong>Job No :</strong></td><td width="175px"><? echo $job_no; ?></td>
	            <td><strong>Style Ref.:</strong></td> <td width="175px"><? echo $style_val; ?></td>
	        </tr>
	        <tr>
	        	<td><strong>Date:</strong></td><td width="175px"><? echo change_date_format($dataArray[0][csf('production_date')]); ?></td>
	        	<td><strong>Item:</strong></td> <td width="175px"><? echo $garments_item[$dataArray[0][csf('item_number_id')]]; ?></td>
	            <td><strong>Order Qnty:</strong></td><td width="175px"><? echo $order_qty; ?></td>
	        </tr>
	        <tr>
	        	<td><strong>Floor:</strong></td>
	        	<td><? echo $floor_library[$dataArray[0][csf('floor_id')]]; ?></td>
	            <td><strong>Source:</strong></td><td width="175px"><? echo $knitting_source[$dataArray[0][csf('production_source')]]; ?></td>
	            <td><strong>Date:</strong></td><td width="175px"><? echo change_date_format($dataArray[0][csf('production_date')]); ?></td>
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
	        <?
			if($entry_break_down_type!=1)
			{
				$delv_id=$dataArray[0][csf('id')];
				$po_break_id=$dataArray[0][csf('po_break_down_id')];
				$sql="SELECT c.id,sum(a.production_qnty) as production_qnty,c.carton_qty, b.color_number_id, b.size_number_id,b.size_order,b.country_id 
				from pro_garments_production_dtls a, wo_po_color_size_breakdown b,pro_garments_production_mst c 
				where a.delivery_mst_id='$data[1]' and a.color_size_break_down_id=b.id and c.id=a.mst_id and a.production_type=14  and c.production_type=14 and a.status_active=1 and b.status_active=1 and c.status_active=1 
				group by c.id,c.carton_qty, b.color_number_id, b.size_number_id,b.size_order,b.country_id  order by b.size_order ";//and b.po_break_down_id='$po_break_id'
				//echo $sql;
				$result=sql_select($sql);
				$size_array=array ();
				$qun_array=array ();
				$id_chk_arr = array();
				foreach ( $result as $row )
				{
					$size_array[$row[csf('size_number_id')]]=$row[csf('size_number_id')];
					$qun_array[$row[csf('country_id')]][$row[csf('color_number_id')]][$row[csf('size_number_id')]]['qty']+=$row[csf('production_qnty')];
					if($id_chk_arr[$row[csf('id')]]=="")
					{
						$qun_array[$row[csf('country_id')]]['carton_qty']+=$row[csf('carton_qty')];
						$id_chk_arr[$row[csf('id')]] = $row[csf('id')];
					}
				}
				// echo "<pre>";print_r($qun_array);die();
				
				$sql="SELECT sum(a.production_qnty) as production_qnty, b.color_number_id,b.country_id from pro_garments_production_dtls a, wo_po_color_size_breakdown b where a.delivery_mst_id='$delv_id' and a.color_size_break_down_id=b.id and a.production_type=14 and a.status_active=1 and b.status_active=1 group by b.color_number_id,b.country_id ";
				//echo $sql; //and a.production_date='$production_date'
				$result=sql_select($sql);
				$color_array=array ();
				foreach ( $result as $row )
				{
					$color_array[$row[csf('country_id')]][$row[csf('color_number_id')]]=$row[csf('color_number_id')];
				}
				
				$sizearr=return_library_array("select id,size_name from lib_size ","id","size_name");
				$colorarr=return_library_array("select id,color_name from  lib_color ","id","color_name");
				// $tbl_width = 350+(count($size_array)*80);
				$tbl_width = 900;
				$size_width = round(550/count($size_array));
				?> 
	         	<div style="width:100%;">
			    <table align="left" cellspacing="0" width="<? echo $tbl_width;?>"  border="1" rules="all" class="rpt_table" >
			        <thead bgcolor="#dddddd" align="center">
			            <th width="30">SL</th>
			            <th width="80" align="center">Country</th>
			            <th width="80" align="center">Color/Size</th>
							<?
			                foreach ($size_array as $sizid)
			                {
								//$size_count=count($sizid);
			                    ?>
			                        <th width="<? echo $size_width;?>"><strong><? echo  $sizearr[$sizid];  ?></strong></th>
			                    <?
			                }
			                ?>
			            <th width="80" align="center">Total Issue Qty.</th>
			            <th width="80" align="center">Carton Qty.</th>
			        </thead>
			        <tbody>
						<?
			            //$mrr_no=$dataArray[0][csf('issue_number')];
			            $i=1;
			            $tot_qnty=array();
			            $tot_carton_qnty=0;
			                foreach($color_array as $country_id=>$country_data)
			                {
								$c=0;
								foreach($country_data as $color_id=>$cid)
			                	{
									 	
									//echo "<pre>";print_r($cid);
									
				                    if ($i%2==0)  
				                        $bgcolor="#E9F3FF";
				                    else
				                        $bgcolor="#FFFFFF";
									$color_count=count($cid);
				                    ?>
				                    <tr bgcolor="<? echo $bgcolor; ?>">
				                        <td><? echo $i;  ?></td>
				                        <td><? echo $country_library[$country_id]; ?></td>
				                        <td><? echo $colorarr[$cid]; ?></td>
				                        <?
				                        foreach ($size_array as $sizval)
				                        {
											$size_count=count($sizval);
				                            ?>
				                            <td align="right"><? echo $qun_array[$country_id][$cid][$sizval]['qty']; ?></td>
				                            <?
				                            $tot_qnty[$country_id][$cid]+=$qun_array[$country_id][$cid][$sizval]['qty'];
											$tot_qnty_size[$sizval]+=$qun_array[$country_id][$cid][$sizval]['qty'];
				                        }
				                        ?>
				                        <td align="right"><? echo $tot_qnty[$country_id][$cid]; ?></td>
				                        <? if($c==0)
				                        {
				                        	?>
				                        	<td rowspan="<?=count($country_data);?>" align="right"><? echo $qun_array[$country_id]['carton_qty']; ?></td>
					                        <?
					                        $c++;
					            			$tot_carton_qnty+=$qun_array[$country_id]['carton_qty'];
				            			} 
				            			?>
				                    </tr>
				                    <?
									$production_quantity+=$tot_qnty[$country_id][$cid];
									$i++;
								}
			                }
			            ?>
			        </tbody>
			        <tr>
			            <td colspan="3" align="right"><strong>Grand Total :</strong></td>
			            <?
							foreach ($size_array as $sizval)
							{
								?>
			                    <td align="right"><?php echo $tot_qnty_size[$sizval]; ?></td>
			                    <?
							}
						?>
			            <td align="right"><?php echo $production_quantity; ?></td>
			            <td align="right"><?php echo $tot_carton_qnty; ?></td>
			        </tr>                           
			    </table>
			 	<?	 
			   }
			    $inserted_by = $dataArray[0][csf('inserted_by')];
	            $insert_date = $dataArray[0][csf('insert_date')];

			    $sql = "SELECT a.id, a.user_full_name, a.designation, b.id as desig_id, b.custom_designation FROM user_passwd a, lib_designation b WHERE a.designation= b.id";
				$user_res = sql_select($sql);
				$user_arr = array();
				foreach($user_res as $row)
				{
					$user_arr[$row['ID']]['name'] = $row['USER_FULL_NAME']; 
					$user_arr[$row['ID']]['custom_designation'] = $row['CUSTOM_DESIGNATION']; 
				} 
				$userDtlsArr=array(); 
				$userDtlsArr[$dataArray[0]['INSERTED_BY']] = "<div><b>".$user_arr[$dataArray[0]['INSERTED_BY']]['name']."</b></div><div><b>".$user_arr[$dataArray[0]['INSERTED_BY']]['custom_designation']."</b></div><div><small>".$dataArray[0]['INSERT_DATE']."</small></div>";
				 
			    // echo get_app_signature(276, $data[0], "1000px",'', '', $inserted_by, $userDtlsArr); 
	            echo signature_table(276, $data[0], "900px", '', '', $inserted_by);
	         ?>
		</div>
		</div>
	<?
	exit();	
}

if($action=="production_process_control")
{
	echo "$('#hidden_variable_cntl').val('0');\n";
	echo "$('#hidden_preceding_process').val('0');\n";
    $control_and_preceding=sql_select("select is_control,preceding_page_id from variable_settings_production where status_active=1 and is_deleted=0 and variable_list=33 and page_category_id=269 and company_name='$data'");  
    if(count($control_and_preceding)>0)
    {
      echo "$('#hidden_variable_cntl').val('".$control_and_preceding[0][csf("is_control")]."');\n";
	  echo "$('#hidden_preceding_process').val('".$control_and_preceding[0][csf("preceding_page_id")]."');\n";
    }
	
	exit();	
}
if($action=="color_size_missing_api")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
	$sqls=sql_select("SELECT d.color_name, production_qnty,size_name  from PRO_GARMENTS_PRODUCTION_DTLS b,wo_po_color_size_breakdown a,lib_size c ,lib_color d  where b.COLOR_SIZE_BREAK_DOWN_ID=a.id and a.size_number_id=c.id  and a.color_number_id=d.id  and b.mst_id='$id'"); 
	?>
	<table width="500" border="2" cellpadding="0" cellspacing="0" class="rpt_table"  style="margin: 0px auto;margin-top: 50px;">
	<thead>
		<tr> 
			<th>Color</th>
			<th>Size</th>
			<th>Qnty.</th>
		</tr>
	</thead>
	<tbody>
	<?
	foreach($sqls as $vals)
	{
		?>
		<tr>
			<td align="center"><? echo $vals[csf("color_name")];?></td>
			<td  align="center"><? echo $vals[csf("size_name")];?></td>
			<td  align="center"><? echo $vals[csf("production_qnty")];?></td>
			
		</tr>

		<?

	}

	 ?>
		
	</tbody>
		
	</table>


	<?
  
}
?>