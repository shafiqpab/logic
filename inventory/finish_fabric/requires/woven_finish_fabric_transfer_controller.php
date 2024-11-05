<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');
include('../../../includes/class4/class.conditions.php');
include('../../../includes/class4/class.reports.php');
include('../../../includes/class4/class.fabrics.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
$store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');
$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");


if($action=="company_wise_report_button_setting"){
	
	extract($_REQUEST);

	$print_report_format=return_field_value("format_id"," lib_report_template","template_name ='".$data."'  and module_id=6 and report_id=130 and is_deleted=0 and status_active=1");

	$print_report_format_arr=explode(",",$print_report_format);

	echo "$('#print').hide();\n";
		
	if($print_report_format != "")
	{
		foreach($print_report_format_arr as $id)
		{
			if($id==78){echo "$('#print').show();\n";}					
		}
	}
	else
	{		
		echo "$('#print').hide();\n";		
	}

	exit();
}

if ($action=="load_room_rack_self_bin")
{
	load_room_rack_self_bin("requires/woven_finish_fabric_transfer_controller",$data);
}

if ($action=="load_drop_down_location")
{
	$data=explode("_",$data);
	echo create_drop_down( "cbo_location", 160, "select id,location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", 0, "load_room_rack_self_bin('requires/woven_finish_fabric_transfer_controller*3', 'store','from_store_td', $('#cbo_company_id').val(),this.value); " );

	//if( $('#cbo_transfer_criteria').val()*1==2 || $('#cbo_transfer_criteria').val()*1==4)  load_room_rack_self_bin('requires/woven_finish_fabric_transfer_controller*3*cbo_store_name_to', 'store','to_store_td', $('#cbo_company_id').val(),this.value);

	exit();
}

if ($action=="load_drop_down_location_to")
{
	$data=explode("_",$data);
	echo create_drop_down( "cbo_location_to", 160, "select id,location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", 0, "load_room_rack_self_bin('requires/woven_finish_fabric_transfer_controller*3*cbo_store_name_to', 'store','to_store_td', $('#cbo_company_id_to').val(),this.value);" );
	exit();
}

if($action=="varible_inventory_upto_rack_from")
{
	$sql_variable_inv_rack_wise=sql_select("select id, store_method  from variable_settings_inventory where company_name=$data and variable_list=21 and status_active=1 and item_category_id = 3");
	if(count($sql_variable_inv_rack_wise)>0)
	{
		echo "1**".$sql_variable_inv_rack_wise[0][csf("store_method")];
	}
	else
	{
		echo "0**".$sql_variable_inv_rack_wise[0][csf("store_method")];
	}
	die;
}
if($action=="varible_inventory_upto_rack_to")
{
	$sql_variable_inv_rack_wise=sql_select("select id, store_method  from variable_settings_inventory where company_name=$data and variable_list=21 and status_active=1 and item_category_id = 3");
	if(count($sql_variable_inv_rack_wise)>0)
	{
		echo "1**".$sql_variable_inv_rack_wise[0][csf("store_method")];
	}
	else
	{
		echo "0**".$sql_variable_inv_rack_wise[0][csf("store_method")];
	}
	die;
}

if($action=="company_wise_load")
{
	$company_id = $data; 
	$sql_variable_inv_auto_batch=sql_select("select id, user_given_code_status  from variable_settings_inventory where company_name=$data and variable_list=34 and status_active=1 and is_deleted=0 and item_category_id = 3");
 	echo "document.getElementById('style_and_po_wise_variable').value 	= '".$sql_variable_inv_auto_batch[0][csf("user_given_code_status")]."';\n";
}

if ($action=="itemTransfer_popup")
{
	echo load_html_head_contents("Item Transfer Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?> 

	<script>
		
		function js_set_value(data,transfer_criteria)
		{
			$('#transfer_id').val(data);
			$('#transfer_criteria').val(transfer_criteria);
			parent.emailwindow.hide();
		}
	
    </script>

</head>

<body>
<div align="center" style="width:780px;">
	<form name="searchdescfrm"  id="searchdescfrm">
		<fieldset style="width:760px;margin-left:10px">
        <legend>Enter search words</legend>
            <table cellpadding="0" cellspacing="0" width="750" class="rpt_table">
                <thead>
                    <th width="200">Search By</th>
                    <th width="200" id="search_by_td_up">Please Enter Transfer ID</th>
                    <th width="220">Date Range</th>
                    <th>
                        <input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
                        <input type="hidden" name="transfer_id" id="transfer_id" class="text_boxes" value="">
                        <input type="hidden" name="transfer_criteria" id="transfer_criteria" class="text_boxes" value="">
                    </th>
                </thead>
                <tr class="general">
                    <td>
						<?
							$search_by_arr=array(1=>"Transfer ID",2=>"Challan No.");
							$dd="change_search_event(this.value, '0*0', '0*0', '../../../') ";
							echo create_drop_down( "cbo_search_by", 150, $search_by_arr,"",0, "--Select--", "",$dd,0 );
                        ?>
                    </td>
                    <td id="search_by_td">
                        <input type="text" style="width:130px;" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
                    </td>
                    <td>
                    <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" />&nbsp;To&nbsp;
                    <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date" />
                    </td>
                    <td>
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $cbo_company_id; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'create_transfer_search_list_view', 'search_div', 'woven_finish_fabric_transfer_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
                    </td>
                </tr>
                <tr>                  
                    <td align="center" height="40" valign="middle" colspan="4"><? echo load_month_buttons(1);  ?></td>
                </tr> 
            </table>
        	<div style="margin-top:10px" id="search_div"></div> 
		</fieldset>
	</form>
</div>    
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=='create_transfer_search_list_view')
{
	$data = explode("_",$data);
	$search_string="%".trim($data[0])."%";
	$search_by=$data[1];
	$company_id =$data[2];
	$date_form=trim($data[3]);
	$date_to =trim($data[4]);
	
	if($date_form !="" && $date_to !="")
	{
		if($db_type==0) $date_cond=" and transfer_date between '".change_date_format($date_form,"YYYY-MM-DD")."' and '".change_date_format($date_to,"YYYY-MM-DD")."'"; 
		else $date_cond=" and transfer_date between '".change_date_format($date_form,"","",-1)."' and '".change_date_format($date_to,"","",-1)."'";
	}
	
	if($search_by==1)
		$search_field="transfer_system_id";	
	else
		$search_field="challan_no";
	
	if($db_type==0) $year_field="YEAR(insert_date)"; 
	else if($db_type==2) $year_field="to_char(insert_date,'YYYY')";
	else $year_field="";//defined Later
	
 	$sql="select id, $year_field as year, transfer_prefix_number, transfer_system_id, challan_no, company_id, transfer_date, transfer_criteria, item_category,location_id,to_location_id from inv_item_transfer_mst where item_category=3 and company_id=$company_id and $search_field like '$search_string' and transfer_criteria in(1,2,3,4,6) and status_active=1 and is_deleted=0 $date_cond order by id desc";
	//echo $sql;die;
	
	$arr=array(3=>$company_arr,5=>$item_transfer_criteria,6=>$item_category);

	echo  create_list_view("tbl_list_search", "Transfer ID,Year,Challan No,Company,Transfer Date,Transfer Criteria,Item Category", "70,60,100,120,90,140","760","250",0, $sql, "js_set_value", "id,transfer_criteria", "", 1, "0,0,0,company_id,0,transfer_criteria,item_category", $arr, "transfer_prefix_number,year,challan_no,company_id,transfer_date,transfer_criteria,item_category", '','','0,0,0,0,3,0,0');
	
	exit();
}

if($action=='populate_data_from_transfer_master')
{
	$data=explode("_", $data);
	$sys_id=$data[0];
	$transfer_criteria=$data[1];
	$data_array=sql_select("select transfer_system_id, challan_no, company_id, location_id, transfer_date, transfer_criteria, item_category, to_company, to_location_id from inv_item_transfer_mst where id='$sys_id'");
	foreach ($data_array as $row)
	{ 
		echo "active_inactive(".$row[csf("transfer_criteria")].");\n";
		
		echo "load_drop_down('requires/woven_finish_fabric_transfer_controller','".$row[csf("company_id")]."', 'load_drop_down_location', 'from_location_td' );\n";
		echo "load_drop_down('requires/woven_finish_fabric_transfer_controller','".$row[csf("to_company")]."', 'load_drop_down_location_to', 'to_location_td' );\n";
		
		echo "document.getElementById('update_id').value 					= '".$sys_id."';\n";
		echo "document.getElementById('txt_system_id').value 				= '".$row[csf("transfer_system_id")]."';\n";
		echo "document.getElementById('cbo_transfer_criteria').value 		= '".$row[csf("transfer_criteria")]."';\n";
		
		echo "document.getElementById('cbo_company_id').value 				= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_location').value 				= '".$row[csf("location_id")]."';\n";

		echo "load_room_rack_self_bin('requires/woven_finish_fabric_transfer_controller*3', 'store','from_store_td', $('#cbo_company_id').val(),'"."',$('#cbo_location').val());\n";

		echo "document.getElementById('txt_challan_no').value 				= '".$row[csf("challan_no")]."';\n";
		echo "document.getElementById('txt_transfer_date').value 			= '".change_date_format($row[csf("transfer_date")])."';\n";
		echo "document.getElementById('cbo_company_id_to').value 			= '".$row[csf("to_company")]."';\n";
		echo "document.getElementById('previous_to_company_id').value 		= '".$row[csf("to_company")]."';\n";
		echo "document.getElementById('cbo_location_to').value 				= '".$row[csf("to_location_id")]."';\n";

		echo "load_room_rack_self_bin('requires/woven_finish_fabric_transfer_controller*3*cbo_store_name_to', 'store','to_store_td', $('#cbo_company_id_to').val(),'"."',$('#cbo_location_to').val());\n";

		echo "document.getElementById('cbo_item_category').value 			= '".$row[csf("item_category")]."';\n";
		
		echo "$('#cbo_company_id').attr('disabled','disabled');\n";
		echo "$('#cbo_location').attr('disabled','disabled');\n";
		
		echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_yarn_transfer_entry',1,1);\n"; 
		exit();
		
	}
}



if ($action=="itemDescription_popup")
{
	echo load_html_head_contents("Item Description Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?> 
	<script>
		function js_set_value(data)
		{
			$('#product_id').val(data);
			parent.emailwindow.hide();
		}
    </script>

</head>

<body>
<div align="center">
	<form name="searchdescfrm"  id="searchdescfrm">
		<fieldset style="width:1210px;margin-left:10px">
        <legend>Enter search words</legend>
            <table cellpadding="0" cellspacing="0" width="1200" class="rpt_table" border="1" rules="all" align="center">
                <thead>
                		<? 
	                		if($transfer_criteria==3 || ($transfer_criteria==2 && $style_and_po_wise_variable==1)){$colSpan=7;}else{$colSpan=7;}
	                	?>
                	<tr>
             		   <th colspan="<? echo $colSpan; ?>" align="center"><? echo create_drop_down( "cbo_search_category", 150, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
          			</tr>
          			<tr>
	                	<? 
	                	if($transfer_criteria==3 || ($transfer_criteria==2 && $style_and_po_wise_variable==1))
	                	{ 
	                	}
	                	else
	                	{
	                		?>
								<th width="150">Order</th>
	                		<?	
	                	}
	                	?>
	                	<th width="150">Style</th>
	                	<th width="150">Job No</th>
	                    <th width="150">Booking</th>
	                    <th width="150">Batch No.</th>
	                    <th width="150">Buyer</th>
	                    <th width="180" id="search_by_td_up">Item Details</th>
	                    <th>
	                        <input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
	                        <input type="hidden" name="product_id" id="product_id" class="text_boxes" value="">
	                    </th>
	                </tr>
                </thead>
                <tr class="general">
                	<?
                	if($transfer_criteria==3 || ($transfer_criteria==2 && $style_and_po_wise_variable==1))
                	{ 
                		?>
                			<input type="hidden" name="txt_order_no" id="txt_order_no">
                		<?
                	}
                	else
                	{
                		?>
							<td>
		                        <input type="text" style="width:120px;" class="text_boxes"  name="txt_order_no" id="txt_order_no" value="<? if($transfer_criteria==4) echo $txt_from_order_no; ?>" <? if($transfer_criteria==4) echo "disabled"; else echo ""; ?> />
		                    </td>
                		<?
                	}
                	?>
                	
                    <td>
                        <input type="text" style="width:120px;" class="text_boxes"  name="txt_style_no" id="txt_style_no" value="<? if($transfer_criteria==3 || ($transfer_criteria==2 && $style_and_po_wise_variable==1)) echo $txt_from_style_no; ?>" <? if($transfer_criteria==3 || ($transfer_criteria==2 && $style_and_po_wise_variable==1)) echo "disabled"; else echo ""; ?> />
                    </td>
                    <td>
                        <input type="text" style="width:120px;" class="text_boxes"  name="txt_job_no" id="txt_job_no" />
                    </td>
                    <td>
                        <input type="text" style="width:120px;" class="text_boxes"  name="txt_batch_no" id="txt_batch_no" />
                        <input type="hidden"  name="txt_order_id" id="txt_order_id" value="<? if($transfer_criteria==4 || $transfer_criteria==3) echo $txt_from_order_id; ?>" />
                    </td>
                    <td>
                        <input type="text" style="width:120px;" class="text_boxes"  name="txt_booking_no" id="txt_booking_no" />
                    </td>
                   
                    <td>
                        <?
                        echo create_drop_down( "cbo_buyer_name", 140, "select a.id, a.buyer_name from lib_buyer a, lib_buyer_party_type b, lib_buyer_tag_company c where a.id=b.buyer_id and a.id=c.buyer_id and b.party_type=1 and c.tag_company='$cbo_company_id' and a.status_active=1 and a.is_deleted =0 order by a.buyer_name","id,buyer_name", 1, "-- Select --", $selected, "" ); 
						?>
                    </td>
                    
                    <td>
                        <input type="text" style="width:150px;" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
                    </td>
                    <td>
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_order_no').value+'_'+document.getElementById('txt_booking_no').value+'_'+document.getElementById('txt_batch_no').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_search_common').value+'_'+<? echo $cbo_company_id; ?>+'_'+<? echo $cbo_store_name; ?>+'_'+<? echo $cbo_floor; ?>+'_'+<? echo $cbo_room; ?>+'_'+<? echo $txt_rack; ?>+'_'+<? echo $txt_shelf; ?>+'_'+<? echo $cbo_bin; ?>+'_'+document.getElementById('txt_order_id').value+'_'+<? echo $transfer_criteria; ?>+'_'+document.getElementById('txt_style_no').value+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('cbo_search_category').value+'_'+<? echo $style_and_po_wise_variable; ?>, 'create_product_search_list_view', 'search_div', 'woven_finish_fabric_transfer_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
                    </td>
                </tr>
            </table>
            <div style="margin-top:10px" id="search_div"></div> 
		</fieldset>
	</form>    
</div> 
   
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=='create_product_search_list_view')
{
	$data = explode("_",$data);
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	
	$txt_order_no 	= str_replace("'","",$data[0]);
	$txt_batch_no 	= str_replace("'","",$data[1]);
	$txt_booking_no = str_replace("'","",$data[2]);
	$cbo_buyer_name = str_replace("'","",$data[3]);
	$search_string	= trim(str_replace("'","",$data[4]));
	$company_id 	= str_replace("'","",$data[5]);
	$cbo_store_name	= str_replace("'","",$data[6]);
	$cbo_floor		= str_replace("'","",$data[7]);
	$cbo_room		= str_replace("'","",$data[8]);
	$cbo_rack		= str_replace("'","",$data[9]);
	$txt_shelf		= str_replace("'","",$data[10]);
	$cbo_bin		= str_replace("'","",$data[11]);
	$order_id		= trim(str_replace("'","",$data[12]));
	$transfer_criteria	= trim(str_replace("'","",$data[13]));
	$txt_from_style_no	= trim(str_replace("'","",$data[14]));
	$txt_job_no			= trim(str_replace("'","",$data[15]));
	$cbo_search_category= trim(str_replace("'","",$data[16]));
	$style_and_po_wise_variable	= trim(str_replace("'","",$data[17]));
	
	//$ord_cond="";
	//if($order_id!="") $ord_cond=" and id=$order_id";
	//$orderNumber=return_library_array( "select id from wo_po_break_down where po_number='$txt_order_no' and status_active=1 and is_deleted = 0 $ord_cond", "id", "id"  );
	$product_ids="";;
	if($db_type==0) $select_prod_field=" group_concat(id) as id"; else $select_prod_field="listagg(cast(id as varchar(4000)),',') within group(order by id) as id";
	if($search_string!="") $product_ids=return_field_value("$select_prod_field","product_details_master","product_name_details ='$search_string'","id");
	
	$sql_cond="";
	if($order_id!="") 			$sql_cond =" and c.po_breakdown_id in($order_id)";
	if($product_ids!="") 		$sql_cond .=" and b.prod_id in($product_ids)";
	
	if($txt_booking_no != "") 	$sql_cond .=" and a.booking_no = '$txt_booking_no'";
	if($txt_batch_no != "") 	$sql_cond .=" and a.batch_no = '$txt_batch_no'";
	
	//if($txt_from_style_no != "") $sql_cond .=" and e.style_ref_no = '$txt_from_style_no'";
	//if($txt_job_no != "") 		$sql_cond .=" and e.job_no like '%$txt_job_no%'";
	/*if($transfer_criteria!=3 && $transfer_criteria!=6)
	{
		if($txt_order_no != "") $sql_cond .=" and d.po_number = '$txt_order_no'";
	}*/

	if($cbo_buyer_name > 0) 	$sql_cond .=" and e.buyer_name ='$cbo_buyer_name'";	
	if($cbo_floor  > 0) 		$sql_cond .=" and b.floor_id ='$cbo_floor'";
	if($cbo_room  > 0) 			$sql_cond .=" and b.room ='$cbo_room'";
	if($cbo_rack  > 0) 			$sql_cond .=" and b.rack ='$cbo_rack'";
	if($txt_shelf  > 0) 		$sql_cond .=" and b.self ='$txt_shelf'";

	if($db_type == 0){
		$select_batch = " b.pi_wo_batch_no";
	}else{
		$select_batch = " cast(b.pi_wo_batch_no as varchar(4000))";
	}


	if($data[16]==1){
		if ($txt_job_no!="") $sql_cond=" and e.job_no_prefix_num='$txt_job_no'"; 
		if ($txt_from_style_no!="") $sql_cond=" and e.style_ref_no ='$txt_from_style_no'";
		if($transfer_criteria!=3 && $transfer_criteria!=6)
		{
			if($txt_order_no != "") $sql_cond .=" and d.po_number = '$txt_order_no'";
		}
	}
	else if($data[16]==2){
		if ($txt_job_no!="") $sql_cond.=" and e.job_no_prefix_num like '$txt_job_no%'  "; 
		if ($txt_from_style_no!="") $sql_cond.=" and e.style_ref_no like '$txt_from_style_no%'";
		if($transfer_criteria!=3 && $transfer_criteria!=6)
		{
			if($txt_order_no != "") $sql_cond .=" and d.po_number like '$txt_order_no%'";
		}
	}
	else if($data[16]==3){
		if ($txt_job_no!="") $sql_cond.=" and e.job_no_prefix_num like '%$txt_job_no'  "; 
		if ($txt_from_style_no!="") $sql_cond.=" and e.style_ref_no like '%$txt_from_style_no'";
		if($transfer_criteria!=3 && $transfer_criteria!=6)
		{
			if($txt_order_no != "") $sql_cond .=" and d.po_number like '%$txt_order_no'";
		}
	}
	else if($data[16]==4 || $data[16]==0){
		if ($txt_job_no!="") $sql_cond.=" and e.job_no_prefix_num like '%$txt_job_no%'  "; 
		if ($txt_from_style_no!="") $sql_cond.=" and e.style_ref_no like '%$txt_from_style_no%'";
		if($transfer_criteria!=3 && $transfer_criteria!=6)
		{
			if($txt_order_no != "") $sql_cond .=" and d.po_number like '%$txt_order_no%'";
		}
	}



	/*$sql = "SELECT b.company_id, b.batch_id as batch_id, e.buyer_name as buyer_id, c.quantity, c.po_breakdown_id as order_id, c.prod_id, b.store_id, b.floor_id, b.room, b.rack, b.self, a.batch_no, a.booking_no_id as booking_id, a.booking_no, d.po_number FROM pro_batch_create_mst a, inv_transaction b, order_wise_pro_details c, wo_po_break_down d, wo_po_details_master e WHERE a.id=b.batch_id and b.id = c.trans_id and c.po_breakdown_id=d.id and d.job_no_mst=e.job_no and b.item_category=3 and b.transaction_type = 1 and c.entry_form in(17) and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.batch_id>0 and b.company_id=$company_id and b.store_id = '$cbo_store_name' $sql_cond
		union all 
		SELECT b.company_id, $select_batch as batch_id, e.buyer_name as buyer_id, c.quantity, c.po_breakdown_id as order_id, c.prod_id, b.store_id, b.floor_id, b.room, b.rack, b.self, a.batch_no, a.booking_no_id as booking_id, a.booking_no, d.po_number 
		from pro_batch_create_mst a, inv_transaction b, order_wise_pro_details c, wo_po_break_down d, wo_po_details_master e 
		where a.id=b.pi_wo_batch_no and b.id = c.trans_id and c.po_breakdown_id=d.id and d.job_no_mst=e.job_no and b.item_category=3 and b.transaction_type = 5 and c.entry_form in(258) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.pi_wo_batch_no>0 and b.company_id=$company_id and b.store_id = '$cbo_store_name' $sql_cond ";*/
		if($transfer_criteria==3 || $transfer_criteria==6)
		{
			$sql = "select x.company_id, x.batch_id,x.fabric_ref, x.rd_no,x.weight_type,x.cutable_width,x.width_editable,x.weight_editable, x.buyer_id, sum(x.quantity) as quantity, x.prod_id, x.body_part_id, x.store_id, x.floor_id, x.room, x.rack, x.self, x.bin_box, x.batch_no,x.job_no, x.booking_id, x.booking_no,x.style_ref_no,sum(x.cons_amount) as cons_amount 
			 from (
			 select b.company_id, b.batch_id as batch_id,b.fabric_ref, b.rd_no,b.weight_type,b.cutable_width,b.width_editable,b.weight_editable, e.buyer_name as buyer_id,e.job_no, sum(c.quantity) as quantity, c.prod_id, b.body_part_id, b.store_id, b.floor_id, b.room, b.rack, b.self, b.bin_box, a.batch_no, a.booking_no_id as booking_id, a.booking_no,e.style_ref_no,sum(b.cons_rate*c.quantity) as cons_amount 
			 from pro_batch_create_mst a, inv_transaction b, order_wise_pro_details c, wo_po_break_down d, wo_po_details_master e 
			 where a.id=b.batch_id and b.id = c.trans_id and c.po_breakdown_id=d.id and d.job_no_mst=e.job_no and b.item_category=3 and b.transaction_type=1 and c.entry_form in(17) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
			 and c.status_active=1 and c.is_deleted=0 and b.batch_id>0 and b.company_id=$company_id and b.store_id='$cbo_store_name' $sql_cond 
			 group by b.company_id, b.batch_id,b.fabric_ref, b.rd_no,b.weight_type,b.cutable_width,b.width_editable,b.weight_editable, e.buyer_name,e.job_no, c.prod_id, b.body_part_id, b.store_id, b.floor_id, b.room, b.rack, b.self, b.bin_box, a.batch_no, a.booking_no_id, a.booking_no,e.style_ref_no 
			 union all 
			 select b.company_id, $select_batch as batch_id,b.fabric_ref, b.rd_no,b.weight_type,b.cutable_width,b.width_editable,b.weight_editable, e.buyer_name as buyer_id,e.job_no, sum(c.quantity) as quantity, c.prod_id, b.body_part_id, b.store_id, b.floor_id, b.room, b.rack, b.self, b.bin_box, a.batch_no, a.booking_no_id as booking_id, a.booking_no,e.style_ref_no,sum(b.cons_rate*c.quantity) as cons_amount 
			 from pro_batch_create_mst a, inv_transaction b, order_wise_pro_details c, wo_po_break_down d, wo_po_details_master e 
			 where a.id=b.pi_wo_batch_no and b.id = c.trans_id and c.po_breakdown_id=d.id and d.job_no_mst=e.job_no and b.item_category=3 and b.transaction_type=5 and c.entry_form in(258) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
			 and c.status_active=1 and c.is_deleted=0 and b.pi_wo_batch_no>0 and b.company_id=$company_id and b.store_id='$cbo_store_name' $sql_cond
			 group by  b.company_id,b.pi_wo_batch_no,b.fabric_ref, b.rd_no,b.weight_type,b.cutable_width,b.width_editable,b.weight_editable, e.buyer_name,e.job_no, c.prod_id, b.body_part_id, b.store_id, b.floor_id, b.room, b.rack, b.self, b.bin_box, a.batch_no, a.booking_no_id, a.booking_no,e.style_ref_no  
			 ) x
			 group by x.company_id, x.batch_id,x.fabric_ref, x.rd_no,x.weight_type,x.cutable_width,x.width_editable,x.weight_editable, x.buyer_id, x.prod_id, x.body_part_id, x.store_id, x.floor_id, x.room, x.rack, x.self, x.bin_box, x.batch_no, x.booking_id, x.booking_no,x.job_no,x.style_ref_no";

			 $sql_po_ids = "select x.company_id, x.batch_id,x.fabric_ref, x.rd_no,x.weight_type,x.cutable_width,x.width_editable,x.weight_editable, x.buyer_id, sum(x.quantity) as quantity, x.order_id, x.prod_id, x.body_part_id, x.store_id, x.floor_id, x.room, x.rack, x.self, x.bin_box, x.batch_no,x.job_no, x.booking_id, x.booking_no,x.style_ref_no,sum(x.cons_amount) as cons_amount 
			 from (
			 select b.company_id, b.batch_id as batch_id,b.fabric_ref, b.rd_no,b.weight_type,b.cutable_width,b.width_editable,b.weight_editable, e.buyer_name as buyer_id,e.job_no, sum(c.quantity) as quantity, LISTAGG(c.po_breakdown_id, ',') WITHIN GROUP (ORDER BY c.po_breakdown_id DESC) order_id, c.prod_id, b.body_part_id, b.store_id, b.floor_id, b.room, b.rack, b.self, b.bin_box, a.batch_no, a.booking_no_id as booking_id, a.booking_no,e.style_ref_no,sum(b.cons_rate*c.quantity) as cons_amount 
			 from pro_batch_create_mst a, inv_transaction b, order_wise_pro_details c, wo_po_break_down d, wo_po_details_master e 
			 where a.id=b.batch_id and b.id = c.trans_id and c.po_breakdown_id=d.id and d.job_no_mst=e.job_no and b.item_category=3 and b.transaction_type=1 and c.entry_form in(17) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
			 and c.status_active=1 and c.is_deleted=0 and b.batch_id>0 and b.company_id=$company_id and b.store_id='$cbo_store_name' $sql_cond 
			 group by b.company_id, b.batch_id,b.fabric_ref, b.rd_no,b.weight_type,b.cutable_width,b.width_editable,b.weight_editable, e.buyer_name,e.job_no, c.prod_id, b.body_part_id, b.store_id, b.floor_id, b.room, b.rack, b.self, b.bin_box, a.batch_no, a.booking_no_id, a.booking_no,e.style_ref_no 
			 union all 
			 select b.company_id, $select_batch as batch_id,b.fabric_ref, b.rd_no,b.weight_type,b.cutable_width,b.width_editable,b.weight_editable, e.buyer_name as buyer_id,e.job_no, sum(c.quantity) as quantity, LISTAGG(c.po_breakdown_id, ',') WITHIN GROUP (ORDER BY c.po_breakdown_id DESC) order_id, c.prod_id, b.body_part_id, b.store_id, b.floor_id, b.room, b.rack, b.self, b.bin_box, a.batch_no, a.booking_no_id as booking_id, a.booking_no,e.style_ref_no,sum(b.cons_rate*c.quantity) as cons_amount 
			 from pro_batch_create_mst a, inv_transaction b, order_wise_pro_details c, wo_po_break_down d, wo_po_details_master e 
			 where a.id=b.pi_wo_batch_no and b.id = c.trans_id and c.po_breakdown_id=d.id and d.job_no_mst=e.job_no and b.item_category=3 and b.transaction_type=5 and c.entry_form in(258) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
			 and c.status_active=1 and c.is_deleted=0 and b.pi_wo_batch_no>0 and b.company_id=$company_id and b.store_id='$cbo_store_name' $sql_cond
			 group by  b.company_id,b.pi_wo_batch_no,b.fabric_ref, b.rd_no,b.weight_type,b.cutable_width,b.width_editable,b.weight_editable, e.buyer_name,e.job_no, c.prod_id, b.body_part_id, b.store_id, b.floor_id, b.room, b.rack, b.self, b.bin_box, a.batch_no, a.booking_no_id, a.booking_no,e.style_ref_no  
			 ) x
			 group by x.company_id, x.batch_id,x.fabric_ref, x.rd_no,x.weight_type,x.cutable_width,x.width_editable,x.weight_editable, x.buyer_id,  x.order_id, x.prod_id, x.body_part_id, x.store_id, x.floor_id, x.room, x.rack, x.self, x.bin_box, x.batch_no, x.booking_id, x.booking_no,x.job_no,x.style_ref_no";

				
		}
		else
		{
			if($transfer_criteria==2 && $style_and_po_wise_variable==1) // store to store and style wise variable YES
			{
				$sql = "select x.company_id, x.batch_id,x.fabric_ref, x.rd_no,x.weight_type,x.cutable_width,x.width_editable,x.weight_editable, x.buyer_id, sum(x.quantity) as quantity, x.order_id, x.prod_id, x.body_part_id, x.store_id, x.floor_id, x.room, x.rack, x.self, x.bin_box, x.batch_no, x.booking_id, x.booking_no, x.po_number,x.job_no,x.style_ref_no ,sum(x.cons_amount) as cons_amount
				 from (
				 select b.company_id, b.batch_id as batch_id,b.fabric_ref, b.rd_no,b.weight_type,b.cutable_width,b.width_editable,b.weight_editable, e.buyer_name as buyer_id,e.job_no, sum(c.quantity) as quantity, LISTAGG(c.po_breakdown_id, ',') WITHIN GROUP (ORDER BY c.po_breakdown_id DESC) order_id, c.prod_id, b.body_part_id, b.store_id, b.floor_id, b.room, b.rack, b.self, b.bin_box, a.batch_no, a.booking_no_id as booking_id, a.booking_no,LISTAGG(d.po_number, ',') WITHIN GROUP (ORDER BY d.po_number DESC) as po_number,e.style_ref_no,sum(b.cons_rate*c.quantity) as cons_amount  
				 from pro_batch_create_mst a, inv_transaction b, order_wise_pro_details c, wo_po_break_down d, wo_po_details_master e 
				 where a.id=b.batch_id and b.id = c.trans_id and c.po_breakdown_id=d.id and d.job_no_mst=e.job_no and b.item_category=3 and b.transaction_type=1 and c.entry_form in(17) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
				 and c.status_active=1 and c.is_deleted=0 and b.batch_id>0 and b.company_id=$company_id and b.store_id='$cbo_store_name' $sql_cond 
				 group by b.company_id, b.batch_id,b.fabric_ref, b.rd_no,b.weight_type,b.cutable_width,b.width_editable,b.weight_editable, e.buyer_name,e.job_no, c.prod_id, b.body_part_id, b.store_id, b.floor_id, b.room, b.rack, b.self, b.bin_box, a.batch_no, a.booking_no_id, a.booking_no,e.style_ref_no  
				 union all 
				 select b.company_id, $select_batch as batch_id,b.fabric_ref, b.rd_no,b.weight_type,b.cutable_width,b.width_editable,b.weight_editable, e.buyer_name as buyer_id,e.job_no, sum(c.quantity) as quantity, LISTAGG(c.po_breakdown_id, ',') WITHIN GROUP (ORDER BY c.po_breakdown_id DESC) order_id, c.prod_id, b.body_part_id, b.store_id, b.floor_id, b.room, b.rack, b.self, b.bin_box, a.batch_no, a.booking_no_id as booking_id, a.booking_no, LISTAGG(d.po_number, ',') WITHIN GROUP (ORDER BY d.po_number DESC) as po_number,e.style_ref_no,sum(b.cons_rate*c.quantity) as cons_amount  
				 from pro_batch_create_mst a, inv_transaction b, order_wise_pro_details c, wo_po_break_down d, wo_po_details_master e 
				 where a.id=b.pi_wo_batch_no and b.id = c.trans_id and c.po_breakdown_id=d.id and d.job_no_mst=e.job_no and b.item_category=3 and b.transaction_type=5 and c.entry_form in(258) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
				 and c.status_active=1 and c.is_deleted=0 and b.pi_wo_batch_no>0 and b.company_id=$company_id and b.store_id='$cbo_store_name' $sql_cond
				 group by  b.company_id,b.pi_wo_batch_no,b.fabric_ref, b.rd_no,b.weight_type,b.cutable_width,b.width_editable,b.weight_editable, e.buyer_name,e.job_no, c.prod_id, b.body_part_id, b.store_id, b.floor_id, b.room, b.rack, b.self, b.bin_box, a.batch_no, a.booking_no_id, a.booking_no,e.style_ref_no 
				 ) x
				 group by x.company_id, x.batch_id,x.fabric_ref, x.rd_no,x.weight_type,x.cutable_width,x.width_editable,x.weight_editable, x.buyer_id,  x.order_id, x.prod_id, x.body_part_id, x.store_id, x.floor_id, x.room, x.rack, x.self, x.bin_box, x.batch_no, x.booking_id, x.booking_no, x.po_number,x.job_no,x.style_ref_no ";

			}
			else
			{
				$sql = "select x.company_id, x.batch_id,x.fabric_ref, x.rd_no,x.weight_type,x.cutable_width,x.width_editable,x.weight_editable, x.buyer_id, sum(x.quantity) as quantity, x.order_id, x.prod_id, x.body_part_id, x.store_id, x.floor_id, x.room, x.rack, x.self, x.bin_box, x.batch_no, x.booking_id, x.booking_no, x.po_number,x.job_no,x.style_ref_no 
				 from (
				 select b.company_id, b.batch_id as batch_id,b.fabric_ref, b.rd_no,b.weight_type,b.cutable_width,b.width_editable,b.weight_editable, e.buyer_name as buyer_id,e.job_no, sum(c.quantity) as quantity, c.po_breakdown_id as order_id, c.prod_id, b.body_part_id, b.store_id, b.floor_id, b.room, b.rack, b.self, b.bin_box, a.batch_no, a.booking_no_id as booking_id, a.booking_no, d.po_number,e.style_ref_no  
				 from pro_batch_create_mst a, inv_transaction b, order_wise_pro_details c, wo_po_break_down d, wo_po_details_master e 
				 where a.id=b.batch_id and b.id = c.trans_id and c.po_breakdown_id=d.id and d.job_no_mst=e.job_no and b.item_category=3 and b.transaction_type=1 and c.entry_form in(17) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
				 and c.status_active=1 and c.is_deleted=0 and b.batch_id>0 and b.company_id=$company_id and b.store_id='$cbo_store_name' $sql_cond 
				 group by b.company_id, b.batch_id,b.fabric_ref, b.rd_no,b.weight_type,b.cutable_width,b.width_editable,b.weight_editable, e.buyer_name,e.job_no, c.po_breakdown_id, c.prod_id, b.body_part_id, b.store_id, b.floor_id, b.room, b.rack, b.self, b.bin_box, a.batch_no, a.booking_no_id, a.booking_no, d.po_number,e.style_ref_no  
				 union all 
				 select b.company_id, $select_batch as batch_id,b.fabric_ref, b.rd_no,b.weight_type,b.cutable_width,b.width_editable,b.weight_editable, e.buyer_name as buyer_id,e.job_no, sum(c.quantity) as quantity, c.po_breakdown_id as order_id, c.prod_id, b.body_part_id, b.store_id, b.floor_id, b.room, b.rack, b.self, b.bin_box, a.batch_no, a.booking_no_id as booking_id, a.booking_no, d.po_number,e.style_ref_no  
				 from pro_batch_create_mst a, inv_transaction b, order_wise_pro_details c, wo_po_break_down d, wo_po_details_master e 
				 where a.id=b.pi_wo_batch_no and b.id = c.trans_id and c.po_breakdown_id=d.id and d.job_no_mst=e.job_no and b.item_category=3 and b.transaction_type=5 and c.entry_form in(258) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
				 and c.status_active=1 and c.is_deleted=0 and b.pi_wo_batch_no>0 and b.company_id=$company_id and b.store_id='$cbo_store_name' $sql_cond
				 group by  b.company_id,b.pi_wo_batch_no,b.fabric_ref, b.rd_no,b.weight_type,b.cutable_width,b.width_editable,b.weight_editable, e.buyer_name,e.job_no, c.po_breakdown_id, c.prod_id, b.body_part_id, b.store_id, b.floor_id, b.room, b.rack, b.self, b.bin_box, a.batch_no, a.booking_no_id, a.booking_no, d.po_number,e.style_ref_no 
				 ) x
				 group by x.company_id, x.batch_id,x.fabric_ref, x.rd_no,x.weight_type,x.cutable_width,x.width_editable,x.weight_editable, x.buyer_id,  x.order_id, x.prod_id, x.body_part_id, x.store_id, x.floor_id, x.room, x.rack, x.self, x.bin_box, x.batch_no, x.booking_id, x.booking_no, x.po_number,x.job_no,x.style_ref_no ";
			}
		}

	//echo $sql;die;

	$result = sql_select($sql);
	$sql_poids_result = sql_select($sql_po_ids);

	if(empty($result))
	{
		echo "Data Not Found";
		die;
	}

	$order_data_array=array();
	$bookingNos="";
	foreach($result as $row )
	{
		/*$order_data_array[$row[csf('order_id')]][$row[csf('prod_id')]][$row[csf('batch_id')]]['item_details']=$row[csf('product_name_details')];
		$order_data_array[$row[csf('order_id')]][$row[csf('prod_id')]][$row[csf('batch_id')]]['color']=$row[csf('color')];
		
		$order_data_array[$row[csf('order_id')]][$row[csf('prod_id')]][$row[csf('batch_id')]]['company_id']=$row[csf('company_id')];
		$order_data_array[$row[csf('order_id')]][$row[csf('prod_id')]][$row[csf('batch_id')]]['buyer_id']=$row[csf('buyer_id')];
		$order_data_array[$row[csf('order_id')]][$row[csf('prod_id')]][$row[csf('batch_id')]]['booking_id']=$row[csf('booking_id')];
		$order_data_array[$row[csf('order_id')]][$row[csf('prod_id')]][$row[csf('batch_id')]]['booking_no']=$row[csf('booking_no')];
		$order_data_array[$row[csf('order_id')]][$row[csf('prod_id')]][$row[csf('batch_id')]]['booking_no_batch']=$row[csf('booking_no')];
		$order_data_array[$row[csf('order_id')]][$row[csf('prod_id')]][$row[csf('batch_id')]]['batch_id']=$row[csf('batch_id')];
		$order_data_array[$row[csf('order_id')]][$row[csf('prod_id')]][$row[csf('batch_id')]]['batch_no']=$row[csf('batch_no')];
		$order_data_array[$row[csf('order_id')]][$row[csf('prod_id')]][$row[csf('batch_id')]]['store_id']=$row[csf('store_id')];
		$order_data_array[$row[csf('order_id')]][$row[csf('prod_id')]][$row[csf('batch_id')]]['floor_id']=$row[csf('floor_id')];
		$order_data_array[$row[csf('order_id')]][$row[csf('prod_id')]][$row[csf('batch_id')]]['room']=$row[csf('room')];
		$order_data_array[$row[csf('order_id')]][$row[csf('prod_id')]][$row[csf('batch_id')]]['rack']=$row[csf('rack')];
		$order_data_array[$row[csf('order_id')]][$row[csf('prod_id')]][$row[csf('batch_id')]]['self']=$row[csf('self')];
		$order_data_array[$row[csf('order_id')]][$row[csf('prod_id')]][$row[csf('batch_id')]]['po_number']=$row[csf('po_number')];*/

		
		$orderidArr[$row[csf('order_id')]]=$row[csf('order_id')];
		$prodidArr[$row[csf('prod_id')]]=$row[csf('prod_id')];
		$batchidArr[$row[csf('batch_id')]]=$row[csf('batch_id')];
		$bookingNos.="'".$row[csf('booking_no')]."',";
	}
	$bookingNos=chop($bookingNos,",");
	$style_wise_po_ids=array();
	foreach($sql_poids_result as $row )
	{
		$style_wise_po_ids[$row[csf('company_id')]][$row[csf('batch_id')]][$row[csf('fabric_ref')]][$row[csf('rd_no')]][$row[csf('weight_type')]][$row[csf('cutable_width')]][$row[csf('width_editable')]][$row[csf('weight_editable')]][$row[csf('buyer_id')]][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('store_id')]][$row[csf('floor_id')]][$row[csf('room')]][$row[csf('rack')]][$row[csf('self')]][$row[csf('bin_box')]][$row[csf('rack')]][$row[csf('job_no')]][$row[csf('booking_id')]]['po_ids'].=$row[csf('order_id')].",";  
	}
	

	/*echo "<pre>";
	print_r($order_data_array);
	die;*/

	//$all_scanned_barcode_no = chop($new_barcode_nos,",");

	/*$batchidArr = array_filter($batchidArr);
	$all_batch_nos = "'".implode("','", $batchidArr)."'";
	$all_batch_id_cond=""; $batchCond=""; 
	$all_batch_id_cond2=""; $batchCond2=""; 
	if($db_type==2 && count($batchidArr)>999)
	{
		$all_batchidArr_chunk=array_chunk($batchidArr,999) ;
		foreach($all_batchidArr_chunk as $chunk_arr)
		{
			$chunk_arr_value=implode(",",$chunk_arr);	
			$batchCond.="  a.batch_id in($chunk_arr_value) or ";
			$batchCond2.="  a.pi_wo_batch_no in($chunk_arr_value) or ";
		}
		$all_batch_id_cond.=" and (".chop($batchCond,'or ').")";
		$all_batch_id_cond2.=" and (".chop($batchCond2,'or ').")";
	}
	else
	{
		$all_batch_id_cond=" and a.batch_id in($all_batch_nos)";	 
		$all_batch_id_cond2=" and a.pi_wo_batch_no in($all_batch_nos)";	 
	}*/


	$prodidArr = array_filter($prodidArr);
	$all_prod_ids = implode(",", $prodidArr);
	$all_prod_id_cond=""; $prodCond=""; 
	$all_prod_id_cond_1=""; $prodCond_1="";$prodCond_2=""; 
	if($db_type==2 && count($batchidArr)>999)
	{
		$all_batchidArr_chunk=array_chunk($batchidArr,999) ;
		foreach($all_batchidArr_chunk as $chunk_arr)
		{
			$chunk_arr_value=implode(",",$chunk_arr);	
			$prodCond.="  a.prod_id in($chunk_arr_value) or ";
			$prodCond_1.="  id in($chunk_arr_value) or ";
			$prodCond_2.="  c.id in($chunk_arr_value) or ";
		}
		$all_prod_id_cond.=" and (".chop($prodCond,'or ').")";
		$all_prod_id_cond_1.=" and (".chop($prodCond_1,'or ').")";
		$all_prod_id_cond_2.=" and (".chop($prodCond_2,'or ').")";
	}
	else
	{
		$all_prod_id_cond=" and a.prod_id in($all_prod_ids)";
		$all_prod_id_cond_1=" and id in($all_prod_ids)";
		$all_prod_id_cond_2=" and c.id in($all_prod_ids)";
	}
	if($transfer_criteria==3 || $transfer_criteria==6)
	{

		$sql_stock1=sql_select("select LISTAGG(b.po_breakdown_id, ',') WITHIN GROUP (ORDER BY b.po_breakdown_id DESC) order_id, a.prod_id, a.batch_id, a.pi_wo_batch_no, a.body_part_id, a.transaction_type, a.room, a.rack, a.self, a.floor_id, a.bin_box,a.cons_rate,sum(b.quantity) as quantity ,sum(b.quantity*a.cons_rate) as cons_amount,d.style_ref_no   from inv_transaction a, order_wise_pro_details b,wo_po_break_down c  ,wo_po_details_master  d where a.id=b.trans_id  and b.po_breakdown_id=c.id and c.job_id=d.id and b.entry_form in (19,202,209,258) and a.transaction_type in (2,3,4,6) and a.store_id = '$cbo_store_name' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category=3 and a.company_id=$company_id $all_prod_id_cond group by  a.prod_id, a.batch_id, a.pi_wo_batch_no, a.body_part_id, a.transaction_type,a.room,a.rack,a.self,a.floor_id,a.bin_box,a.cons_rate,d.style_ref_no ");
	}
	else
	{
		if($transfer_criteria==2 && $style_and_po_wise_variable==1) // store to store and style wise variable YES
		{
			$sql_stock1=sql_select("select LISTAGG(b.po_breakdown_id, ',') WITHIN GROUP (ORDER BY b.po_breakdown_id DESC) order_id, a.prod_id, a.batch_id, a.pi_wo_batch_no, a.body_part_id, a.transaction_type, a.room, a.rack, a.self, a.floor_id, a.bin_box, sum(b.quantity) as quantity,sum(b.quantity*a.cons_rate) as cons_amount,d.style_ref_no,a.fabric_ref, a.rd_no,a.weight_type,a.cutable_width from inv_transaction a, order_wise_pro_details b,wo_po_break_down c  ,wo_po_details_master  d where a.id=b.trans_id and b.po_breakdown_id=c.id and c.job_id=d.id and b.entry_form in (19,202,209,258) and a.transaction_type in (2,3,4,6) and a.store_id = '$cbo_store_name' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category=3 and a.company_id=$company_id $all_prod_id_cond group by a.prod_id, a.batch_id, a.pi_wo_batch_no, a.body_part_id, a.transaction_type,a.room,a.rack,a.self,a.floor_id,a.bin_box,a.cons_rate,d.style_ref_no,a.fabric_ref, a.rd_no,a.weight_type,a.cutable_width");
		}
		else
		{
			$sql_stock1=sql_select("select b.po_breakdown_id as order_id, a.prod_id, a.batch_id, a.pi_wo_batch_no, a.body_part_id, a.transaction_type, a.room, a.rack, a.self, a.floor_id, a.bin_box, sum(b.quantity) as quantity from inv_transaction a, order_wise_pro_details b where a.id=b.trans_id and b.entry_form in (19,202,209,258) and a.transaction_type in (2,3,4,6) and a.store_id = '$cbo_store_name' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category=3 and a.company_id=$company_id $all_prod_id_cond group by b.po_breakdown_id, a.prod_id, a.batch_id, a.pi_wo_batch_no, a.body_part_id, a.transaction_type,a.room,a.rack,a.self,a.floor_id,a.bin_box");
		}
	}
	


	foreach($sql_stock1 as $value)
	{
		$floor_id = ($value[csf('floor_id')]=="")?0:$value[csf('floor_id')];
		$room = ($value[csf('room')]=="")?0:$value[csf('room')];
		$rack = ($value[csf('rack')]=="")?0:$value[csf('rack')];
		$self = ($value[csf('self')]=="")?0:$value[csf('self')];
		$bin_box = ($value[csf('bin_box')]=="")?0:$value[csf('bin_box')];

		if($transfer_criteria==3 || $transfer_criteria==6)
		{

			if($value[csf('transaction_type')] ==2)
			{
				$stock_data_arrray[$value[csf('style_ref_no')]][$value[csf('prod_id')]][$value[csf('batch_id')]][$value[csf('body_part_id')]][$floor_id][$room][$rack][$self][$bin_box][$value[csf('fabric_ref')]][$value[csf('rd_no')]][$value[csf('weight_type')]][$value[csf('cutable_width')]]['issue_qty'] +=$value[csf('quantity')];
				$stock_data_arrray[$value[csf('style_ref_no')]][$value[csf('prod_id')]][$value[csf('batch_id')]][$value[csf('body_part_id')]][$floor_id][$room][$rack][$self][$bin_box][$value[csf('fabric_ref')]][$value[csf('rd_no')]][$value[csf('weight_type')]][$value[csf('cutable_width')]]['issue_amount'] +=$value[csf('cons_amount')];
			}
			else if($value[csf('transaction_type')] ==3)
			{
				$stock_data_arrray[$value[csf('style_ref_no')]][$value[csf('prod_id')]][$value[csf('pi_wo_batch_no')]][$value[csf('body_part_id')]][$floor_id][$room][$rack][$self][$bin_box][$value[csf('fabric_ref')]][$value[csf('rd_no')]][$value[csf('weight_type')]][$value[csf('cutable_width')]]['rcv_ret_qty'] +=$value[csf('quantity')];
				$stock_data_arrray[$value[csf('style_ref_no')]][$value[csf('prod_id')]][$value[csf('pi_wo_batch_no')]][$value[csf('body_part_id')]][$floor_id][$room][$rack][$self][$bin_box][$value[csf('fabric_ref')]][$value[csf('rd_no')]][$value[csf('weight_type')]][$value[csf('cutable_width')]]['rcv_ret_amount'] +=$value[csf('cons_amount')];
			}
			else if($value[csf('transaction_type')] ==4)
			{
				$stock_data_arrray[$value[csf('style_ref_no')]][$value[csf('prod_id')]][$value[csf('pi_wo_batch_no')]][$value[csf('body_part_id')]][$floor_id][$room][$rack][$self][$bin_box][$value[csf('fabric_ref')]][$value[csf('rd_no')]][$value[csf('weight_type')]][$value[csf('cutable_width')]]['issue_ret'] +=$value[csf('quantity')];
				$stock_data_arrray[$value[csf('style_ref_no')]][$value[csf('prod_id')]][$value[csf('pi_wo_batch_no')]][$value[csf('body_part_id')]][$floor_id][$room][$rack][$self][$bin_box][$value[csf('fabric_ref')]][$value[csf('rd_no')]][$value[csf('weight_type')]][$value[csf('cutable_width')]]['issue_ret_amount'] +=$value[csf('cons_amount')];
			}
			else if($value[csf('transaction_type')] ==6)
			{
				$stock_data_arrray[$value[csf('style_ref_no')]][$value[csf('prod_id')]][$value[csf('pi_wo_batch_no')]][$value[csf('body_part_id')]][$floor_id][$room][$rack][$self][$bin_box][$value[csf('fabric_ref')]][$value[csf('rd_no')]][$value[csf('weight_type')]][$value[csf('cutable_width')]]['trans_out'] +=$value[csf('quantity')];
				$stock_data_arrray[$value[csf('style_ref_no')]][$value[csf('prod_id')]][$value[csf('pi_wo_batch_no')]][$value[csf('body_part_id')]][$floor_id][$room][$rack][$self][$bin_box][$value[csf('fabric_ref')]][$value[csf('rd_no')]][$value[csf('weight_type')]][$value[csf('cutable_width')]]['trans_out_amount'] +=$value[csf('cons_amount')];
			}
		}
		else
		{

			if($transfer_criteria==2 && $style_and_po_wise_variable==1) // store to store and style wise variable YES
			{
				$po_or_style=$value[csf('style_ref_no')];
			}
			else
			{
				$po_or_style=$value[csf('order_id')];
			}	
			
			if($value[csf('transaction_type')] ==2)
			{
				$stock_data_arrray[$po_or_style][$value[csf('prod_id')]][$value[csf('batch_id')]][$value[csf('body_part_id')]][$floor_id][$room][$rack][$self][$bin_box ]['issue_qty'] +=$value[csf('quantity')];
				$stock_data_arrray[$po_or_style][$value[csf('prod_id')]][$value[csf('batch_id')]][$value[csf('body_part_id')]][$floor_id][$room][$rack][$self][$bin_box ]['issue_amount'] +=$value[csf('cons_amount')];
			}
			else if($value[csf('transaction_type')] ==3)
			{
				$stock_data_arrray[$po_or_style][$value[csf('prod_id')]][$value[csf('pi_wo_batch_no')]][$value[csf('body_part_id')]][$floor_id][$room][$rack][$self][$bin_box ]['rcv_ret_qty'] +=$value[csf('quantity')];
				$stock_data_arrray[$po_or_style][$value[csf('prod_id')]][$value[csf('pi_wo_batch_no')]][$value[csf('body_part_id')]][$floor_id][$room][$rack][$self][$bin_box ]['rcv_ret_amount'] +=$value[csf('cons_amount')];
			}
			else if($value[csf('transaction_type')] ==4)
			{
				$stock_data_arrray[$po_or_style][$value[csf('prod_id')]][$value[csf('pi_wo_batch_no')]][$value[csf('body_part_id')]][$floor_id][$room][$rack][$self][$bin_box ]['issue_ret'] +=$value[csf('quantity')];
				$stock_data_arrray[$po_or_style][$value[csf('prod_id')]][$value[csf('pi_wo_batch_no')]][$value[csf('body_part_id')]][$floor_id][$room][$rack][$self][$bin_box ]['issue_ret_amount'] +=$value[csf('cons_amount')];
			}
			else if($value[csf('transaction_type')] ==6)
			{
				$stock_data_arrray[$po_or_style][$value[csf('prod_id')]][$value[csf('pi_wo_batch_no')]][$value[csf('body_part_id')]][$floor_id][$room][$rack][$self][$bin_box ]['trans_out'] +=$value[csf('quantity')];
				$stock_data_arrray[$po_or_style][$value[csf('prod_id')]][$value[csf('pi_wo_batch_no')]][$value[csf('body_part_id')]][$floor_id][$room][$rack][$self][$bin_box ]['trans_out_amount'] +=$value[csf('cons_amount')];
			}
			
		}
	}

	/*echo "<pre>";
	print_r($stock_data_arrray);
	echo "</pre>";*/
	/*	$sql_stock2=sql_select("select b.po_breakdown_id as order_id, a.prod_id, a.pi_wo_batch_no,
	sum(case when a.transaction_type in(4,5) and b.trans_type in(4,5) then b.quantity end) as rcv_qty,
	sum(case when a.transaction_type in(3,6) and b.trans_type in(3,6) then b.quantity end) as issue_qty  
	from inv_transaction a, order_wise_pro_details b
	where a.id=b.trans_id and b.entry_form in(202,209,258) and a.store_id = '$cbo_store_name' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category=3  and a.company_id=$company_id and b.po_breakdown_id in(".implode(",",$orderidArr).") and a.prod_id in(".implode(",",$prodidArr).") $all_batch_id_cond2
	group by b.po_breakdown_id, a.prod_id, a.pi_wo_batch_no");

	foreach($sql_stock2 as $value)
	{
		$stock_data_arrray[$value[csf('order_id')]][$value[csf('prod_id')]][$value[csf('pi_wo_batch_no')]]['rcv_qty'] +=$value[csf('rcv_qty')];
		$stock_data_arrray[$value[csf('order_id')]][$value[csf('prod_id')]][$value[csf('pi_wo_batch_no')]]['issue_qty'] +=$value[csf('issue_qty')];
	}*/

	$prod_sql=sql_select("select id, product_name_details, color, avg_rate_per_unit, unit_of_measure, detarmination_id from product_details_master where status_active=1 $all_prod_id_cond_1");
	foreach($prod_sql as $row)
	{
		$prod_data[$row[csf("id")]]["product_name_details"]=$row[csf("product_name_details")];
		$prod_data[$row[csf("id")]]["color"]=$row[csf("color")];
		$prod_data[$row[csf("id")]]["avg_rate_per_unit"]=$row[csf("avg_rate_per_unit")];
		$prod_data[$row[csf("id")]]["unit_of_measure"]=$row[csf("unit_of_measure")];
		$prod_data[$row[csf("id")]]["detarmination_id"]=$row[csf("detarmination_id")];
	}
	unset($prod_sql);


	$lib_room_rack_shelf_sql = "select b.company_id,b.location_id,b.store_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,a.floor_room_rack_name floor_name,c.floor_room_rack_name room_name,d.floor_room_rack_name rack_name,e.floor_room_rack_name shelf_name from lib_floor_room_rack_dtls b
	left join lib_floor_room_rack_mst a on b.floor_id=a.floor_room_rack_id and a.status_active=1 and a.is_deleted=0
	left join lib_floor_room_rack_mst c on b.room_id=c.floor_room_rack_id and c.status_active=1 and c.is_deleted=0
	left join lib_floor_room_rack_mst d on b.rack_id=d.floor_room_rack_id and d.status_active=1 and d.is_deleted=0
	left join lib_floor_room_rack_mst e on b.shelf_id=e.floor_room_rack_id and e.status_active=1 and e.is_deleted=0
	where b.status_active=1 and b.is_deleted=0 and b.company_id=$company_id and b.store_id=$cbo_store_name";
	$lib_room_rack_shelf_arr=sql_select($lib_room_rack_shelf_sql);
	foreach ($lib_room_rack_shelf_arr as $room_rack_shelf_row)
	{
		$floor_id = $room_rack_shelf_row[csf("floor_id")];
		$room_id  = $room_rack_shelf_row[csf("room_id")];
		$rack_id  = $room_rack_shelf_row[csf("rack_id")];
		$shelf_id = $room_rack_shelf_row[csf("shelf_id")];
		$bin_id   = $room_rack_shelf_row[csf("bin_id")];

		if($floor_id!="" && $room_id=="" && $rack_id=="" && $shelf_id=="" && $bin_id==""){
			$lib_floor_arr[$floor_id] = $room_rack_shelf_row[csf("floor_name")];
		}

		if($floor_id!="" && $room_id!="" && $rack_id=="" && $shelf_id=="" && $bin_id==""){
			$lib_room_arr[$floor_id][$room_id] = $room_rack_shelf_row[csf("room_name")];
		}

		if($floor_id!="" && $room_id!="" && $rack_id!="" && $shelf_id=="" && $bin_id==""){
			$lib_rack_arr[$floor_id][$room_id][$rack_id] = $room_rack_shelf_row[csf("rack_name")];
		}

		if($floor_id!="" && $room_id!="" && $rack_id!="" && $shelf_id!="" && $bin_id==""){
			$lib_shelf_arr[$floor_id][$room_id][$rack_id][$shelf_id] = $room_rack_shelf_row[csf("shelf_name")];
		}

		if($floor_id!="" && $room_id!="" && $rack_id!="" && $shelf_id!="" && $bin_id!=""){
			$lib_bin_arr[$floor_id][$room_id][$rack_id][$shelf_id][$bin_id] = $room_rack_shelf_row[csf("shelf_name")];
		}
	}
	unset($lib_room_rack_shelf_arr);

	$from_transfer_wo_rate_sql=sql_select("select a.fabric_color_id,a.rate,b.lib_yarn_count_deter_id,c.id,a.job_no,b.body_part_id,a.po_break_down_id from wo_booking_dtls a,wo_pre_cost_fabric_cost_dtls b,product_details_master c 
	where a.pre_cost_fabric_cost_dtls_id=b.id and c.detarmination_id=b.lib_yarn_count_deter_id and a.fabric_color_id=c.color and c.item_category_id=3 and c.company_id=1 $all_prod_id_cond_2  and a.booking_no in($bookingNos) 
	group by  a.fabric_color_id,a.rate,b.lib_yarn_count_deter_id,c.id,a.job_no,b.body_part_id,a.po_break_down_id");
	foreach ($from_transfer_wo_rate_sql as $row)
	{
		$transfer_in_rate_arr[$row[csf("job_no")]][$row[csf("po_break_down_id")]][$row[csf("body_part_id")]][$row[csf("lib_yarn_count_deter_id")]][$row[csf("fabric_color_id")]][$row[csf("id")]]["rate"]=$row[csf("rate")];
	}


	/*echo "<pre>";
	print_r($transfer_in_rate_arr);
	echo "</pre>";*/

	
	?>
    <div style="width:2400px; float: left;" align="left">  
    <table cellspacing="0" width="100%" class="rpt_table" id="" rules="all" align="left">
        <thead>
            <tr>
                <th width="35">SL</th>
                <? 

                if(($transfer_criteria==3 || $transfer_criteria==6) || ($transfer_criteria==2 && $style_and_po_wise_variable==1))
                	{	
                	} 
                	else
                	{
                		?> 
                		<th width="80">Order</th>
                		<?
                	}

                	?>
               
                <th width="80">Item ID</th>
                <th width="150">Company</th>
                <th width="150">Job No</th>
                <th width="150">Style</th>
                <th width="150">Buyer</th>
                <th width="300">Item Details</th>
                <th width="90">Color</th>
                <th width="120">Booking</th>
                <th width="90">Batch</th>               
                <th width="120">Body Part</th>

                <th width="50">RD No</th>
                <th width="50">Fabric Ref.</th>
                <th width="50">Full Width</th>
                <th width="50">Cutable Width</th>
                <th width="50">Weight</th>
                <th width="50">Weight Type</th>

                <th width="90">Floor</th>
                <th width="90">Room</th>
                <th width="90">Rack</th>
                <th width="90">Shelf</th>
                <th width="90">Bin/Box</th>
                <th>Stock</th>
            </tr>
        </thead>
    </table>
    </div>
    <div style="width:2420px; overflow-y:scroll; max-height:250px;" align="left">  
    <table cellspacing="0" width="2400" class="rpt_table" id="tbl_list_search" rules="all" align="left"  style="margin-bottom: 5px; word-break:break-all">
        <tbody>
            <? 
            $i=1;

			foreach($result as $row )
			{
				if($row[csf('floor_id')] =="") $floor_id = "0"; else $floor_id = $row[csf('floor_id')];
				if($row[csf('room')] =="") $room_id = "0"; else $room_id = $row[csf('room')];
				if($row[csf('rack')] =="") $rack_id = "0"; else $rack_id = $row[csf('rack')];
				if($row[csf('self')] =="") $self_id = "0"; else $self_id = $row[csf('self')];
				if($row[csf('bin_box')] =="") $bin_box_id = "0"; else $bin_box_id = $row[csf('bin_box')];

				$floor_name 		= $lib_floor_arr[$floor_id];
				$room_name 			= $lib_room_arr[$floor_id][$room_id];
				$rack_name			= $lib_rack_arr[$floor_id][$room_id][$rack_id];
				$shelf_name 		= $lib_shelf_arr[$floor_id][$room_id][$rack_id][$self_id];
				$bin_box_name 		= $lib_bin_arr[$floor_id][$room_id][$rack_id][$self_id][$bin_box_id];

				$order_ids= implode(',', array_unique(explode(',', $row[csf('order_id')])));

				if($transfer_criteria==3 || $transfer_criteria==6)
				{

					$issue_qty 		=$stock_data_arrray[$row[csf('style_ref_no')]][$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('body_part_id')]][$floor_id][$room_id][$rack_id][$self_id][$bin_box_id][$row[csf('fabric_ref')]][$row[csf('rd_no')]][$row[csf('weight_type')]][$row[csf('cutable_width')]]['issue_qty'];
					$rcv_ret_qty 	= $stock_data_arrray[$row[csf('style_ref_no')]][$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('body_part_id')]][$floor_id][$room_id][$rack_id][$self_id][$bin_box_id][$row[csf('fabric_ref')]][$row[csf('rd_no')]][$row[csf('weight_type')]][$row[csf('cutable_width')]]['rcv_ret_qty'];
					$issue_ret_qty 	= $stock_data_arrray[$row[csf('style_ref_no')]][$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('body_part_id')]][$floor_id][$room_id][$rack_id][$self_id][$bin_box_id][$row[csf('fabric_ref')]][$row[csf('rd_no')]][$row[csf('weight_type')]][$row[csf('cutable_width')]]['issue_ret'];
					$trans_out_qty 	= $stock_data_arrray[$row[csf('style_ref_no')]][$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('body_part_id')]][$floor_id][$room_id][$rack_id][$self_id][$bin_box_id][$row[csf('fabric_ref')]][$row[csf('rd_no')]][$row[csf('weight_type')]][$row[csf('cutable_width')]]['trans_out'];


					$issue_qty_amount 		=$stock_data_arrray[$row[csf('style_ref_no')]][$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('body_part_id')]][$floor_id][$room_id][$rack_id][$self_id][$bin_box_id][$row[csf('fabric_ref')]][$row[csf('rd_no')]][$row[csf('weight_type')]][$row[csf('cutable_width')]]['issue_amount'];
					$rcv_ret_qty_amount 	= $stock_data_arrray[$row[csf('style_ref_no')]][$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('body_part_id')]][$floor_id][$room_id][$rack_id][$self_id][$bin_box_id][$row[csf('fabric_ref')]][$row[csf('rd_no')]][$row[csf('weight_type')]][$row[csf('cutable_width')]]['rcv_ret_amount'];
					$issue_ret_qty_amount 	= $stock_data_arrray[$row[csf('style_ref_no')]][$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('body_part_id')]][$floor_id][$room_id][$rack_id][$self_id][$bin_box_id][$row[csf('fabric_ref')]][$row[csf('rd_no')]][$row[csf('weight_type')]][$row[csf('cutable_width')]]['issue_ret_amount'];
					$trans_out_qty_amount 	= $stock_data_arrray[$row[csf('style_ref_no')]][$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('body_part_id')]][$floor_id][$room_id][$rack_id][$self_id][$bin_box_id][$row[csf('fabric_ref')]][$row[csf('rd_no')]][$row[csf('weight_type')]][$row[csf('cutable_width')]]['trans_out_amount'];


					$order_ids=$style_wise_po_ids[$row[csf('company_id')]][$row[csf('batch_id')]][$row[csf('fabric_ref')]][$row[csf('rd_no')]][$row[csf('weight_type')]][$row[csf('cutable_width')]][$row[csf('width_editable')]][$row[csf('weight_editable')]][$row[csf('buyer_id')]][$row[csf('prod_id')]][$row[csf('body_part_id')]][$row[csf('store_id')]][$row[csf('floor_id')]][$row[csf('room')]][$row[csf('rack')]][$row[csf('self')]][$row[csf('bin_box')]][$row[csf('rack')]][$row[csf('job_no')]][$row[csf('booking_id')]]['po_ids'];

					$order_ids= implode(',', array_unique(explode(',', chop($order_ids,","))));
					//echo $order_ids."<br/>";


				}
				else
				{
					if($transfer_criteria==2 && $style_and_po_wise_variable==1) // store to store and style wise variable YES
					{
						$poOrStyle=$row[csf('style_ref_no')];
					}
					else
					{
						$poOrStyle=$order_ids;
					}	


					$issue_qty 		=$stock_data_arrray[$poOrStyle][$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('body_part_id')]][$floor_id][$room_id][$rack_id][$self_id][$bin_box_id ]['issue_qty'];
					$rcv_ret_qty 	= $stock_data_arrray[$poOrStyle][$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('body_part_id')]][$floor_id][$room_id][$rack_id][$self_id][$bin_box_id ]['rcv_ret_qty'];
					$issue_ret_qty 	= $stock_data_arrray[$poOrStyle][$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('body_part_id')]][$floor_id][$room_id][$rack_id][$self_id][$bin_box_id ]['issue_ret'];
					$trans_out_qty 	= $stock_data_arrray[$poOrStyle][$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('body_part_id')]][$floor_id][$room_id][$rack_id][$self_id][$bin_box_id ]['trans_out'];

					//echo $poOrStyle."=".$row[csf('prod_id')]."=".$row[csf('batch_id')]."=".$row[csf('body_part_id')]."<br/>";


					$issue_qty_amount 		=$stock_data_arrray[$poOrStyle][$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('body_part_id')]][$floor_id][$room_id][$rack_id][$self_id][$bin_box_id ]['issue_amount'];
					$rcv_ret_qty_amount 	= $stock_data_arrray[$poOrStyle][$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('body_part_id')]][$floor_id][$room_id][$rack_id][$self_id][$bin_box_id ]['rcv_ret_amount'];
					$issue_ret_qty_amount 	= $stock_data_arrray[$poOrStyle][$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('body_part_id')]][$floor_id][$room_id][$rack_id][$self_id][$bin_box_id ]['issue_ret_amount'];
					$trans_out_qty_amount 	= $stock_data_arrray[$poOrStyle][$row[csf('prod_id')]][$row[csf('batch_id')]][$row[csf('body_part_id')]][$floor_id][$room_id][$rack_id][$self_id][$bin_box_id ]['trans_out_amount'];
				}


				$stock_qnty =  ($row[csf('quantity')] + $issue_ret_qty ) - ($issue_qty + $rcv_ret_qty + $trans_out_qty);
				//echo $row[csf('quantity')] ."+". $issue_ret_qty ."-". $issue_qty ."+". $rcv_ret_qty ."+".$trans_out_qty."<br>";

				$title = "receive+trans in= ".$row[csf('quantity')].", issue ret=".$issue_ret_qty.", issue=".$issue_qty.", receive ret=".$rcv_ret_qty.", trans out=".$trans_out_qty;


				



				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if(($transfer_criteria==3 || $transfer_criteria==6 ) || ($transfer_criteria==2 && $style_and_po_wise_variable==1))
				{

					//echo $row[csf('cons_amount')] ."+". $issue_ret_qty_amount ."-(". $issue_qty_amount ."+". $rcv_ret_qty_amount ."+". $trans_out_qty_amount."===<br>";
					$stock_amount =  ($row[csf('cons_amount')] + $issue_ret_qty_amount ) - ($issue_qty_amount + $rcv_ret_qty_amount + $trans_out_qty_amount);

					$avg_rate= $stock_amount/$stock_qnty;
					//$amount= $row[csf('amount')]/$stock_qnty;
				}
				else
				{
					$avg_rate=$prod_data[$row[csf('prod_id')]]['avg_rate_per_unit'];
				}
				//If different fabrication and different color transfer in.
				$trans_in_wo_rate=$transfer_in_rate_arr[$row[csf("job_no")]][$row[csf("order_id")]][$row[csf("body_part_id")]][$prod_data[$row[csf('prod_id')]]['detarmination_id']][$prod_data[$row[csf('prod_id')]]['color']][$row[csf("prod_id")]]["rate"];
				//echo $trans_in_wo_rate."<br/>";

				$data_ref = $order_ids."_".$row[csf('prod_id')]."_".$row[csf('company_id')]."_".$row[csf('store_id')]."_".$floor_id."_".$room_id."_".$rack_id."_".$self_id."_".$row[csf('batch_id')]."_".$row[csf('batch_no')]."_".$avg_rate."_".$prod_data[$row[csf('prod_id')]]['product_name_details']."_".$prod_data[$row[csf('prod_id')]]['color']."_".$row[csf('po_number')]."_".$bin_box_id."_".$prod_data[$row[csf('prod_id')]]['unit_of_measure']."_".$row[csf('body_part_id')]."_".$prod_data[$row[csf('prod_id')]]['detarmination_id']."_".$stock_qnty."_".$row[csf('rd_no')]."_".$row[csf('fabric_ref')]."_".$row[csf('width_editable')]."_".$row[csf('cutable_width')]."_".$row[csf('weight_editable')]."_".$row[csf('weight_type')]."_".$row[csf('booking_id')]."_".$row[csf('booking_no')]."_".$stock_amount."_".$trans_in_wo_rate;

				?>
				<tr  bgcolor="<? echo $bgcolor; ?>" valign="middle"  style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $data_ref;//$row[csf('order_id')]."_".$row[csf('prod_id')]."_".$row[csf('company_id')]."_".$row[csf('store_id')]."_".$floor_id."_".$room_id."_".$rack_id."_".$self_id."_".$row[csf('batch_id')]; ?>')" >
					<td width="35" align="center"><? echo $i; ?></td>
					<?
						if(($transfer_criteria==3 || $transfer_criteria==6) || ($transfer_criteria==2 && $style_and_po_wise_variable==1))
						{
							
						}
						else
						{
							?>
							<td width="80" ><? echo $row[csf('po_number')]; ?></td> 
							<?
						}
					?>
					
					<td width="80" title="<? echo "PO ID- ". $order_ids; ?>"><? echo $row[csf('prod_id')]; ?></td>
					<td width="150"><p><? echo $company_arr[$row[csf('company_id')]]; ?></p></td>
					<td width="150"><p><? echo $row[csf('job_no')]; ?></p></td>
					<td width="150"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
					<td width="150"><p><? echo $buyer_library[$row[csf('buyer_id')]]; ?></p></td>
					<td width="300"><p><? echo $prod_data[$row[csf('prod_id')]]["product_name_details"];?></p></td>
					<td width="90"><p><? echo $color_arr[$prod_data[$row[csf('prod_id')]]["color"]]; ?></p></td>
					<td width="120"><p><? echo $row[csf('booking_no')]; ?></p></td>
					<td width="90"><p><? echo $row[csf('batch_no')]; ?></p></td>

					<td width="120"><p><? echo $body_part[$row[csf('body_part_id')]]; ?></p></td>

					<td width="50"><? echo $row[csf('rd_no')]; ?></td>
					<td width="50"><? echo $row[csf('fabric_ref')]; ?></td>
					<td width="50"><? echo $row[csf('width_editable')]; ?></td>
					<td width="50"><? echo $row[csf('cutable_width')]; ?></td>
					<td width="50"><? echo $row[csf('weight_editable')]; ?></td>
					<td width="50"><? echo $fabric_weight_type[$row[csf('weight_type')]]; ?></td>

					<td width="90"><? echo $floor_name; ?></td>
					<td width="90"><? echo $room_name; ?></td>
					<td width="90"><? echo $rack_name; ?></td>
					<td width="90"><? echo $shelf_name; ?></td>
					<td width="90"><? echo $bin_box_name; ?></td>
					<td  title="<? echo $title;?>"><? echo number_format($stock_qnty,2); ?></td>
				</tr>
				<? 
				$i++;
			}




            /*foreach($order_data_array as $order_id => $order_data_array)
            {
                foreach($order_data_array as $item_ids => $item_val)
                {
					foreach($item_val as $batch_ids => $row)
					{
						$stock = $stock_data_arrray[$order_id][$item_ids][$batch_ids]['rcv_qty'] - $stock_data_arrray[$order_id][$item_ids][$batch_ids]['issue_qty'];
						if($stock>0)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";*/		
							?>
							<!-- <tr  bgcolor="<? echo $bgcolor; ?>" valign="middle"  style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? //echo $order_id."_".$item_ids."_".$row['company_id']."_".$row['store_id']."_".$row['floor_id']."_".$row['room']."_".$row['rack']."_".$row['self']."_".$batch_ids; ?>')" >
								<td width="35" align="center"><? echo $i; ?></td>
								<td width="80" title="<? echo $order_id; ?>"><? echo $row['po_number']; ?></td>
								<td width="80"><? //echo $item_ids; ?></td>
								<td width="120"><? //echo $company_arr[$row['company_id']]; ?></td>
								<td width="120"><? //echo $buyer_library[$row['buyer_id']]; ?></td>
								<td width="200"><? //echo $prod_data[$item_ids]["product_name_details"];?></td>
								<td width="90"><? //echo $color_arr[$prod_data[$item_ids]["color"]]; ?></td>
								<td width="90"><? //echo $row['booking_no_batch']; ?></td>
								<td width="90"><? //echo $row['batch_no']; ?></td>
								<td><? //echo $stock; ?></td>
							</tr> -->
							<? 
							/*
							$i++;
						}
					}
                }
            }*/
            ?>
        </tbody>
    </table>
    </div>
	<?
	exit();
}


if($action=='populate_data_from_product_master')
{
	$data = explode("_",$data);
	// echo "<pre>"; print_r($data);
	$transfer_criteria = $data[29];
	$style_and_po_wise_variable = $data[30];
	// echo $data[29].'==';die;
	$floor_id = $data[4];
	$room = $data[5];
	$rack = $data[6];
	$self = $data[7];
	$bin_box = $data[14];

	/*$data_ref = $order_ids."_".$row[csf('prod_id')]."_".$row[csf('company_id')]."_".$row[csf('store_id')]."_".$floor_id."_".$room_id."_".$rack_id."_".$self_id."_".$row[csf('batch_id')]."_".$row[csf('batch_no')]."_".$avg_rate
	."_".$prod_data[$row[csf('prod_id')]]['product_name_details']."_".$prod_data[$row[csf('prod_id')]]['color']."_".$row[csf('po_number')]."_".$bin_box_id."_".$prod_data[$row[csf('prod_id')]]['unit_of_measure']."_".$row[csf('body_part_id')]."_".$prod_data[$row[csf('prod_id')]]['detarmination_id']."_".$stock_qnty."_".$row[csf('rd_no')]."_".$row[csf('fabric_ref')]."_".$row[csf('width_editable')]."_".$row[csf('cutable_width')]."_".$row[csf('weight_editable')]."_".$row[csf('weight_type')]."_".$row[csf('booking_id')]."_".$row[csf('booking_no')];*/


	echo "document.getElementById('txt_from_order_id').value 			= '".$data[0]."';\n";
	echo "document.getElementById('txt_from_order_no').value 			= '".$data[13]."';\n";
	if($transfer_criteria==2)
	{
		echo "document.getElementById('txt_to_order_id').value 			= '".$data[0]."';\n";
		echo "document.getElementById('txt_to_order_no').value 			= '".$data[13]."';\n";
		
		//echo "load_drop_down('requires/woven_finish_fabric_transfer_controller',".$data[16]."+'_'+".$data[0]."+'_'+".$data[1]."+'_'+".$transfer_criteria."+'_'+2+'_'+".$transfer_criteria.", 'load_body_part', 'to_body_part' );\n";

		echo "load_drop_down('requires/woven_finish_fabric_transfer_controller',".$data[16]."+'_'+'".$data[0]."'+'_'+".$data[1]."+'_'+".$transfer_criteria."+'_'+2, 'load_body_part', 'to_body_part' );\n";



	}
	echo "document.getElementById('from_product_id').value 				= '".$data[1]."';\n";
	echo "document.getElementById('txt_item_desc').value 				= '".$data[11]."';\n";
	echo "document.getElementById('batch_id').value 					= '".$data[8]."';\n";
	echo "document.getElementById('txt_batch_no').value 				= '".$data[9]."';\n";

	echo "document.getElementById('txt_from_fabric_id').value 			= '".$data[17]."';\n";
	echo "document.getElementById('txt_current_stock').value 			= '".$data[18]."';\n";
	echo "document.getElementById('hidden_current_stock').value 		= '".$data[18]."';\n";


	echo "document.getElementById('txt_rate').value 					= '".$data[10]."';\n";
	echo "document.getElementById('hide_color_id').value 				= '".$data[12]."';\n";
	echo "document.getElementById('txt_color').value 					= '".$color_arr[$data[12]]."';\n";
	echo "document.getElementById('cbo_uom').value 						= '".$data[15]."';\n";

	echo "document.getElementById('hidden_rd_no').value 				= '".$data[19]."';\n";
	echo "document.getElementById('hidden_fabric_ref').value 			= '".$data[20]."';\n";
	echo "document.getElementById('hidden_width').value 				= '".$data[21]."';\n";
	echo "document.getElementById('hidden_cutable_width').value 		= '".$data[22]."';\n";
	echo "document.getElementById('hidden_weight').value 				= '".$data[23]."';\n";
	echo "document.getElementById('hidden_weight_type').value 			= '".$data[24]."';\n";
	echo "document.getElementById('hdn_from_booking_id').value 			= '".$data[25]."';\n";
	echo "document.getElementById('hdn_from_booking_no').value 			= '".$data[26]."';\n";
	echo "document.getElementById('hdn_from_wo_rate').value 			= '".$data[28]."';\n";

	if($transfer_criteria ==2 && $style_and_po_wise_variable==1)
	{
		echo "document.getElementById('hdn_to_booking_id').value 			= '".$data[25]."';\n";
		echo "document.getElementById('hdn_to_booking_no').value 			= '".$data[26]."';\n";
	}

	//echo "load_drop_down('requires/woven_finish_fabric_transfer_controller',".$data[16]."+'_'+".$data[0]."+'_'+".$data[1]."+'_'+".$transfer_criteria."+'_'+1, 'load_body_part', 'from_body_td' );\n";



	echo "load_drop_down('requires/woven_finish_fabric_transfer_controller',".$data[16]."+'_'+'".$data[0]."'+'_'+".$data[1]."+'_'+".$transfer_criteria."+'_'+1, 'load_body_part', 'from_body_td' );\n";


	if($floor_id !=0)
	{
		echo "load_room_rack_self_bin('requires/woven_finish_fabric_transfer_controller*3', 'floor','floor_td', '".$data[2]."','"."','".$data[3]."',this.value);\n";
		echo "document.getElementById('cbo_floor').value 				= '".$floor_id."';\n";
	}
	if($room !=0)
	{
		echo "load_room_rack_self_bin('requires/woven_finish_fabric_transfer_controller*3', 'room','room_td', '".$data[2]."','"."','".$data[3]."','".$floor_id."',this.value);\n";
		echo "document.getElementById('cbo_room').value 				= '".$room."';\n";
	}
	if($rack !=0)
	{
		echo "load_room_rack_self_bin('requires/woven_finish_fabric_transfer_controller*3', 'rack','rack_td', '".$data[2]."','"."','".$data[3]."','".$floor_id."','".$room."',this.value);\n";
		echo "document.getElementById('txt_rack').value 				= '".$rack."';\n";
	}
	if($self !=0)
	{
		echo "load_room_rack_self_bin('requires/woven_finish_fabric_transfer_controller*3', 'shelf','shelf_td', '".$data[2]."','"."','".$data[3]."','".$floor_id."','".$room."','".$rack."',this.value);\n";
		echo "document.getElementById('txt_shelf').value 				= '".$self."';\n";
	}
	if($bin_box !=0)
	{
		echo "load_room_rack_self_bin('requires/woven_finish_fabric_transfer_controller*3', 'bin','bin_td', '".$data[2]."','"."','".$data[3]."','".$floor_id."','".$room."','".$rack."','".$self."',this.value);\n";
		echo "document.getElementById('cbo_bin').value 				= '".$bin_box."';\n";
	}

	/*$transfer_criteria=$data[9];
	$order_name_arr=return_library_array( "select id ,po_number from wo_po_break_down where status_active=1 and is_deleted =0 and id in($data[0])",'id','po_number');

	$sql_stock=sql_select("select b.po_breakdown_id as order_id, a.prod_id, a.batch_id,
	sum(case when a.transaction_type in(1) and b.trans_type in(1) then b.quantity end) as rcv_qty,
	sum(case when a.transaction_type in(2) and b.trans_type in(2) then b.quantity end) as issue_qty  
	from inv_transaction a, order_wise_pro_details b
	where a.id=b.trans_id and b.entry_form in (17,19) and a.store_id = $data[3] and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category=3  and a.company_id=$data[2] and b.po_breakdown_id=$data[0] and a.prod_id=$data[1] and a.batch_id=$data[8]
	group by b.po_breakdown_id, a.prod_id, a.batch_id");

	foreach($sql_stock as $value)
	{
		$stock_data_arrray[$value[csf('order_id')]][$value[csf('prod_id')]][$value[csf('batch_id')]]['rcv_qty'] +=$value[csf('rcv_qty')];
		$stock_data_arrray[$value[csf('order_id')]][$value[csf('prod_id')]][$value[csf('batch_id')]]['issue_qty'] +=$value[csf('issue_qty')];
	}

	$sql_stock2=sql_select("select b.po_breakdown_id as order_id, a.prod_id, a.pi_wo_batch_no,
	sum(case when a.transaction_type in(4,5) and b.trans_type in(4,5) then b.quantity end) as rcv_qty,
	sum(case when a.transaction_type in(3,6) and b.trans_type in(3,6) then b.quantity end) as issue_qty  
	from inv_transaction a, order_wise_pro_details b
	where a.id=b.trans_id and b.entry_form in (202,209,258) and a.store_id = $data[3] and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category=3 and a.company_id=$data[2] and b.po_breakdown_id=$data[0] and a.prod_id=$data[1] and a.pi_wo_batch_no=$data[8]
	group by b.po_breakdown_id, a.prod_id, a.pi_wo_batch_no");

	foreach($sql_stock2 as $value)
	{
		$stock_data_arrray[$value[csf('order_id')]][$value[csf('prod_id')]][$value[csf('pi_wo_batch_no')]]['rcv_qty'] +=$value[csf('rcv_qty')];
		$stock_data_arrray[$value[csf('order_id')]][$value[csf('prod_id')]][$value[csf('pi_wo_batch_no')]]['issue_qty'] +=$value[csf('issue_qty')];
	}

	if($db_type == 0){
		$select_batch = " b.pi_wo_batch_no ";
	}else{
		$select_batch = " cast(b.pi_wo_batch_no as varchar(4000)) ";
	}

	$sql ="select  a.company_id, b.batch_id, c.quantity, c.po_breakdown_id as order_id, a.id as prod_id, a.product_name_details, a.unit_of_measure, a.color, a.current_stock , a.avg_rate_per_unit, b.store_id, b.floor_id, b.room, b.rack, b.self
	from product_details_master a, inv_transaction b, order_wise_pro_details c
	where a.id=b.prod_id and b.id = c.trans_id and b.item_category=3 and c.entry_form in(17) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and b.batch_id>0 and b.company_id=$data[2] and b.store_id = $data[3] and c.po_breakdown_id=$data[0] and b.prod_id=$data[1] and b.batch_id=$data[8]
	union all
	select  a.company_id, $select_batch as batch_id, c.quantity, c.po_breakdown_id as order_id, a.id as prod_id, a.product_name_details, a.unit_of_measure, a.color, a.current_stock , a.avg_rate_per_unit, b.store_id, b.floor_id, b.room, b.rack, b.self
	from product_details_master a, inv_transaction b, order_wise_pro_details c
	where a.id=b.prod_id and b.id = c.trans_id and b.item_category=3 and c.entry_form in(258) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and b.pi_wo_batch_no>0 and b.company_id=$data[2] and b.store_id = $data[3] and c.po_breakdown_id=$data[0] and b.prod_id=$data[1] and b.pi_wo_batch_no=$data[8]";

	$data_array=sql_select($sql);
	foreach ($data_array as $row)
	{ 
		if($row[csf("floor_id")]) $floor_id=$row[csf("floor_id")]; else $floor_id=0;
		if($row[csf("room")]) $room=$row[csf("room")]; else  $room=0;
		if($row[csf("rack")]) $rack=$row[csf("rack")]; else  $rack=0;
		if($row[csf("self")]) $self=$row[csf("self")]; else  $self=0;
		 
		$stockQty = $stock_data_arrray[$row[csf("order_id")]][$row[csf("prod_id")]][$row[csf("batch_id")]]['rcv_qty'] - $stock_data_arrray[$row[csf("order_id")]][$row[csf("prod_id")]][$row[csf("batch_id")]]['issue_qty'];
		echo "document.getElementById('txt_from_order_id').value 			= '".$row[csf("order_id")]."';\n";
		echo "document.getElementById('txt_from_order_no').value 			= '".$order_name_arr[$row[csf("order_id")]]."';\n";
		if($transfer_criteria==2)
		{
			echo "document.getElementById('txt_to_order_id').value 			= '".$row[csf("order_id")]."';\n";
			echo "document.getElementById('txt_to_order_no').value 			= '".$order_name_arr[$row[csf("order_id")]]."';\n";
		}
		echo "document.getElementById('from_product_id').value 				= '".$row[csf("prod_id")]."';\n";
		echo "document.getElementById('txt_item_desc').value 				= '".$row[csf("product_name_details")]."';\n";
		echo "document.getElementById('txt_current_stock').value 			= '".$stockQty."';\n";
		echo "document.getElementById('hidden_current_stock').value 		= '".$stockQty."';\n";
		echo "document.getElementById('batch_id').value 					= '".$row[csf("batch_id")]."';\n";
		//echo "document.getElementById('txt_current_stock').value 			= '".$row[csf("current_stock")]."';\n";
		//echo "document.getElementById('hidden_current_stock').value 		= '".$row[csf("current_stock")]."';\n";
		//echo "document.getElementById('txt_rate').value 					= '".$row[csf("avg_rate_per_unit")]."';\n";
		
		echo "document.getElementById('txt_rate').value 					= '".$row[csf("avg_rate_per_unit")]."';\n";
		echo "document.getElementById('hide_color_id').value 				= '".$row[csf("color")]."';\n";
		echo "document.getElementById('txt_color').value 					= '".$color_arr[$row[csf("color")]]."';\n";
		echo "document.getElementById('cbo_uom').value 						= '".$row[csf("unit_of_measure")]."';\n";
		
		if($floor_id !=0)
		{
			echo "load_room_rack_self_bin('requires/woven_finish_fabric_transfer_controller*3', 'floor','floor_td', '".$data[2]."','"."','".$data[3]."',this.value);\n";
			echo "document.getElementById('cbo_floor').value 				= '".$floor_id."';\n";
		}
		if($room !=0)
		{
			echo "load_room_rack_self_bin('requires/woven_finish_fabric_transfer_controller*3', 'room','room_td', '".$data[2]."','"."','".$data[3]."','".$floor_id."',this.value);\n";
			echo "document.getElementById('cbo_room').value 				= '".$room."';\n";
		}
		if($rack !=0)
		{
			echo "load_room_rack_self_bin('requires/woven_finish_fabric_transfer_controller*3', 'rack','rack_td', '".$data[2]."','"."','".$data[3]."','".$floor_id."','".$room."',this.value);\n";
			echo "document.getElementById('txt_rack').value 				= '".$rack."';\n";
		}
		if($self !=0)
		{
			echo "load_room_rack_self_bin('requires/woven_finish_fabric_transfer_controller*3', 'shelf','shelf_td', '".$data[2]."','"."','".$data[3]."','".$floor_id."','".$room."','".$rack."',this.value);\n";
			echo "document.getElementById('txt_shelf').value 				= '".$self."';\n";
		}
	}*/
	exit();
}


if ($action=="po_search_popup")
{
	echo load_html_head_contents("PO Info", "../../../", 1, 1,'','','');
	extract($_REQUEST); 
	?> 
	<script> 
		function fn_show_check()
		{
			if( $('#txt_po_no').val()=='' && $('#txt_job_no').val()=='' && $('#txt_style_no').val()=='' && $('#txt_booking_no').val()=='' && $('#cbo_buyer_name').val()*1==0 && $('#txt_internal_ref').val()==''){
				alert("Please Enter at Least One Search");return; 
			}
			show_list_view ( encodeURIComponent($('#txt_po_no').val())+'_'+$('#txt_job_no').val()+'_'+<? echo $cbo_company_id_to; ?>+'_'+$('#cbo_buyer_name').val()+'_'+$('#txt_internal_ref').val()+ '_' + $('#txt_booking_no').val()+ '_' +<? echo $transfer_criteria; ?>+ '_' + $('#txt_style_no').val()+'_'+<? echo $txt_from_fabric_id; ?>+'_'+<? echo $hide_color_id; ?>+'_'+<? echo $str; ?>+'_'+<? echo $cbo_company_id_from; ?>+'_'+document.getElementById('cbo_search_category').value+'_'+<? echo $style_and_po_wise_variable; ?>, 'create_po_search_list_view', 'search_div', 'woven_finish_fabric_transfer_controller', 'setFilterGrid(\'tbl_list_search\',-1);hidden_field_reset();');
		}

		function js_set_value( id,name,booking_no,booking_id,style_ref_no,jobNo,rdNo,fabricRef,weightType,cutableWidth,booking_type) 
		{
			$('#hidden_style_no').val(style_ref_no);
			$('#hidden_order_id').val(id);
			$('#hidden_order_no').val(name);
			$('#hidden_booking_no').val(booking_no);
			$('#hidden_booking_id').val(booking_id);
			$('#hidden_job_no').val(jobNo);
			$('#hidden_rdNo').val(rdNo);
			$('#hidden_fabricRef').val(fabricRef);
			$('#hidden_cutableWidth').val(weightType);
			$('#hidden_weightType').val(cutableWidth);
			$('#hidden_booking_type').val(booking_type);
			parent.emailwindow.hide();
		}
		
		function hidden_field_reset()
		{
			$('#hidden_style_no').val('');
			$('#hidden_order_id').val('');
			$('#hidden_order_no').val( '' );
			$('#hidden_booking_no').val( '' );
			$('#hidden_booking_id').val( '' );
			$('#hidden_job_no').val('');
			$('#hidden_rdNo').val('');
			$('#hidden_fabricRef').val('');
			$('#hidden_cutableWidth').val('');
			$('#hidden_weightType').val('');
			$('#hidden_booking_type').val('');
		}
	</script>
	</head>
	<body>
		<div align="center">
		<form name="searchdescfrm"  id="searchdescfrm">
			<fieldset style="width:620px;margin-left:5px">
				<input type="hidden" name="hidden_style_no" id="hidden_style_no" class="text_boxes" value="">
				<input type="hidden" name="hidden_order_id" id="hidden_order_id" class="text_boxes" value="">
				<input type="hidden" name="hidden_order_no" id="hidden_order_no" class="text_boxes" value="">
				<input type="hidden" name="hidden_booking_no" id="hidden_booking_no" class="text_boxes" value="">
				<input type="hidden" name="hidden_booking_id" id="hidden_booking_id" class="text_boxes" value="">
				<input type="hidden" name="hidden_job_no" id="hidden_job_no" class="text_boxes" value="">
				
				<input type="hidden" name="hidden_rdNo" id="hidden_rdNo" class="text_boxes" value="">
				<input type="hidden" name="hidden_fabricRef" id="hidden_fabricRef" class="text_boxes" value="">
				<input type="hidden" name="hidden_cutableWidth" id="hidden_cutableWidth" class="text_boxes" value="">
				<input type="hidden" name="hidden_weightType" id="hidden_weightType" class="text_boxes" value="">
				<input type="hidden" name="hidden_booking_type" id="hidden_booking_type" class="text_boxes" value="">
				
				<table cellpadding="0" cellspacing="0" width="620" class="rpt_table" border="1" rules="all">
					<thead>
						<? if(($transfer_criteria==3) || ($transfer_criteria==2 && $style_and_po_wise_variable==1)){$colSpan=6;}else{$colSpan=7;} ?>
						<tr>
							 <th colspan="<? echo $colSpan; ?>" align="center"><? echo create_drop_down( "cbo_search_category", 150, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
						</tr>
						<tr>
							<th>Buyer</th>
							<? if(($transfer_criteria==3) || ($transfer_criteria==2 && $style_and_po_wise_variable==1)){}else{?> <th>PO No</th> <? } ?>
							<th>Job No</th>
							<th>Style No</th>
	                        <th>Internal Ref. No</th>
	                        <th>Booking No</th>
							<th>
								<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
								<input type="hidden" name="po_id" id="po_id" value="">
							</th> 
						</tr>
					</thead>
					<tr class="general">
						<td align="center">
							<?
							echo create_drop_down( "cbo_buyer_name", 170, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id_to' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $buyer_name, "","" ); 
							?>       
						</td>
						<? if(($transfer_criteria==3) || ($transfer_criteria==2 && $style_and_po_wise_variable==1))
						{
						}
						else
						{
							?>
							 <td align="center">				
								<input type="text" style="width:130px" class="text_boxes"  name="txt_po_no" id="txt_po_no" placeholder="Write" />	
							</td>
							<?
						}
						?>
						<td align="center">				
							<input type="text" style="width:130px" class="text_boxes"  name="txt_job_no" id="txt_job_no"  placeholder="Write"/>	
						</td>
                        <td align="center">				
							<input type="text" style="width:130px" class="text_boxes"  name="txt_style_no" id="txt_style_no"  placeholder="Write"/>	
						</td>
                        <td align="center">				
							<input type="text" style="width:130px" class="text_boxes"  name="txt_internal_ref" id="txt_internal_ref"  placeholder="Write"/>	
						</td>
						<td align="center">
							<input type="text" style="width:100px" class="text_boxes"  name="txt_booking_no" id="txt_booking_no"  placeholder="Write"/>
						</td>
						<td align="center">
							<input type="button" name="button2" class="formbutton" value="Show" onClick="fn_show_check()" style="width:100px;" />
						</td>
					</tr>
				</table>
				<div id="search_div" style="margin-top:10px"  align="center"></div>	
			</fieldset>
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
	$data = explode("_",$data);
	$txt_po_no = trim($data[0]);
	$txt_job_no = trim($data[1]);
	$company_id =$data[2];
	$buyer_id =$data[3];
	$txt_internal_ref =trim($data[4]);
	$txt_booking_no =trim($data[5]);
	$transfer_criteria =trim($data[6]);
	$style_no =trim($data[7]);
	$from_fabric_id =trim($data[8]);
	$color_id =trim($data[9]);
	$popup_type =trim($data[10]);
	$company_id_from =trim($data[11]);
	$cbo_search_category =trim($data[12]);
	$style_and_po_wise_variable =trim($data[13]);
	
	$search_con="";$search_con2="";
	if($buyer_id!=0){
		$search_con = " and a.buyer_name=$buyer_id";
		$search_con2 = " and a.buyer_id=$buyer_id";
	}
	/*if($transfer_criteria!=3 && $transfer_criteria!=6)
	{
		if($txt_po_no!="")
		$search_con .= " and b.po_number like '%$txt_po_no%'";	
	}
	if($txt_job_no!="")
		$search_con .=" and a.job_no like '%$txt_job_no%'";
	if($style_no!="")
		$search_con .=" and a.style_ref_no like '%$style_no%'";*/
	if($txt_internal_ref!="")
		$search_con .=" and b.grouping like '%$txt_internal_ref%'";
	if($txt_booking_no!="")
		$search_con .=" and c.booking_no like '%$txt_booking_no%'";
		$search_con2 .=" and b.booking_no like '%$txt_booking_no%'";


	if($data[12]==1){
		if ($txt_job_no!="") $search_con=" and a.job_no_prefix_num='$txt_job_no'"; 
		if ($style_no!="") $search_con=" and a.style_ref_no ='$style_no'";
		if(($transfer_criteria==3 || $transfer_criteria==6) || ($transfer_criteria==2 && $style_and_po_wise_variable==1))
		{
		}
		else
		{
			if($txt_po_no!=""){
				if($txt_po_no != "") $search_con .=" and b.po_number = '$txt_po_no'";
			}
		}
	}
	else if($data[12]==2){
		if ($txt_job_no!="") $search_con.=" and a.job_no_prefix_num like '$txt_job_no%'  "; 
		if ($style_no!="") $search_con.=" and a.style_ref_no like '$style_no%'";
		if(($transfer_criteria==3 || $transfer_criteria==6) || ($transfer_criteria==2 && $style_and_po_wise_variable==1))
		{
		}
		else
		{

			if($txt_po_no!=""){
				if($txt_po_no != "") $search_con .=" and b.po_number like '$txt_po_no%'";
			}
		}
	}
	else if($data[12]==3) {
		if ($txt_job_no!="") $search_con.=" and a.job_no_prefix_num like '%$txt_job_no'  "; 
		if ($style_no!="") $search_con.=" and a.style_ref_no like '%$style_no'";
		if(($transfer_criteria==3 || $transfer_criteria==6) || ($transfer_criteria==2 && $style_and_po_wise_variable==1))
		{
		}
		else
		{
			if($txt_po_no!=""){
				if($txt_po_no != "") $search_con .=" and b.po_number like '%$txt_po_no'";
			}
		}
	}
	else if($data[12]==4 || $data[12]==0){
		if ($txt_job_no!="") $search_con.=" and a.job_no_prefix_num like '%$txt_job_no%'  "; 
		if ($style_no!="") $search_con.=" and a.style_ref_no like '%$style_no%'";
		if(($transfer_criteria==3 || $transfer_criteria==6) || ($transfer_criteria==2 && $style_and_po_wise_variable==1))
		{
		}
		else
		{
			if($txt_po_no!=""){
				if($txt_po_no != "") $search_con .=" and b.po_number like '%$txt_po_no%'";
			}
		}
	}

	/*$sql = "select a.job_no, a.style_ref_no, a.buyer_name, a.order_uom, b.id,b.grouping as ref_no, b.po_number, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs, b.pub_shipment_date 
	from wo_po_details_master a, wo_po_break_down b 
	where a.job_no=b.job_no_mst and a.company_name=$company_id $search_con and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id desc";*/

	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
	$brand_library=return_library_array( "select id, brand_name from lib_buyer_brand", "id", "brand_name");
	$season_library=return_library_array( "select id, season_name from lib_buyer_season", "id", "season_name");

	if(($transfer_criteria==3 || $transfer_criteria==6) || ($transfer_criteria==2 && $style_and_po_wise_variable==1))
	{
		if($popup_type==2)
		{
			if($transfer_criteria==3)
			{
				$sql = "SELECT a.job_no, a.style_ref_no,a.season_buyer_wise,a.season_year,a.brand_id, a.buyer_name, a.order_uom, LISTAGG(b.id, ',') WITHIN GROUP (ORDER BY b.id DESC) id,b.grouping as ref_no, sum(a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs, e.lib_yarn_count_deter_id,c.booking_type 
				from wo_po_details_master a, wo_po_break_down b, wo_booking_dtls c, wo_booking_mst d ,wo_pre_cost_fabric_cost_dtls e
				where a.job_no=b.job_no_mst and a.company_name=$company_id $search_con and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.id = c.po_break_down_id and d.booking_no = c.booking_no  and c.pre_cost_fabric_cost_dtls_id=e.id and c.job_no=e.job_no  and c.fabric_color_id=$color_id  and e.lib_yarn_count_deter_id= $from_fabric_id  and d.booking_type in (1,4) and c.status_active =1 and c.is_deleted=0 and a.garments_nature in(2,3) 
				group by a.job_no, a.style_ref_no,a.season_buyer_wise,a.season_year,a.brand_id, a.buyer_name, a.order_uom,b.grouping,e.lib_yarn_count_deter_id,c.booking_type";
				
				$nameArr=sql_select( $sql );
				$booking_nos="";
				foreach ($nameArr as $selectRow)
				{
					$booking_nos.="'".$selectRow[csf('booking_no')]."',";
				}
				$booking_nos=chop($booking_nos,",");

				$sql_qry = "SELECT a.job_no, a.style_ref_no,a.season_buyer_wise,a.season_year,a.brand_id, a.buyer_name, a.order_uom,b.grouping as ref_no, c.booking_no , d.id as booking_id,c.gsm_weight,c.dia_width
				from wo_po_details_master a, wo_po_break_down b, wo_booking_dtls c, wo_booking_mst d ,wo_pre_cost_fabric_cost_dtls e
				where a.job_no=b.job_no_mst and a.company_name=$company_id $search_con and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.id = c.po_break_down_id and d.booking_no = c.booking_no  and c.pre_cost_fabric_cost_dtls_id=e.id and c.job_no=e.job_no and c.fabric_color_id=$color_id  and e.lib_yarn_count_deter_id= $from_fabric_id   and d.booking_type in (1,4) and c.status_active =1 and c.is_deleted=0 and a.garments_nature in(2,3) 
				group by a.id, a.job_no, a.style_ref_no,a.season_buyer_wise,a.season_year,a.brand_id, a.buyer_name, a.order_uom,b.grouping, a.total_set_qnty, b.po_quantity, b.pub_shipment_date,c.booking_no ,d.id,c.gsm_weight,c.dia_width 
				order by a.id desc";
				

				$nameArr=sql_select( $sql_qry );
				$bookingNos="";
				foreach ($nameArr as $selectRow)
				{
					$bookingNos.="'".$selectRow[csf('booking_no')]."',";
				
					$bookingData[$selectRow[csf('job_no')]][$selectRow[csf('style_ref_no')]][$selectRow[csf('buyer_name')]][$selectRow[csf('order_uom')]][$selectRow[csf('ref_no')]][$selectRow[csf('season_buyer_wise')]][$selectRow[csf('season_year')]][$selectRow[csf('brand_id')]]['booking_id']=$selectRow[csf('booking_id')];

					$bookingData[$selectRow[csf('job_no')]][$selectRow[csf('style_ref_no')]][$selectRow[csf('buyer_name')]][$selectRow[csf('order_uom')]][$selectRow[csf('ref_no')]][$selectRow[csf('season_buyer_wise')]][$selectRow[csf('season_year')]][$selectRow[csf('brand_id')]]['booking_no']=$selectRow[csf('booking_no')];
					$bookingData[$selectRow[csf('job_no')]][$selectRow[csf('style_ref_no')]][$selectRow[csf('buyer_name')]][$selectRow[csf('order_uom')]][$selectRow[csf('ref_no')]][$selectRow[csf('season_buyer_wise')]][$selectRow[csf('season_year')]][$selectRow[csf('brand_id')]]['gsm_weight']=$selectRow[csf('gsm_weight')];
					$bookingData[$selectRow[csf('job_no')]][$selectRow[csf('style_ref_no')]][$selectRow[csf('buyer_name')]][$selectRow[csf('order_uom')]][$selectRow[csf('ref_no')]][$selectRow[csf('season_buyer_wise')]][$selectRow[csf('season_year')]][$selectRow[csf('brand_id')]]['dia_width']=$selectRow[csf('dia_width')];
				}
				$bookingNos=chop($bookingNos,",");

				$determination_info_sql =sql_select("SELECT a.booking_no,a.job_no,b.body_part_id,b.lib_yarn_count_deter_id, b.gsm_weight,b.uom, a.dia_width, a.fabric_color_id,c.fabric_ref,c.rd_no,b.gsm_weight_type as weight_type,g.item_size as cutable_width from wo_booking_dtls a, wo_pre_cost_fabric_cost_dtls b join  wo_pre_cos_fab_co_avg_con_dtls g on b.id=g.pre_cost_fabric_cost_dtls_id ,lib_yarn_count_determina_mst c  where a.pre_cost_fabric_cost_dtls_id=b.id and b.lib_yarn_count_deter_id=c.id and a.booking_no in($bookingNos) and b.lib_yarn_count_deter_id='$from_fabric_id' and a.fabric_color_id=$color_id and a.booking_type in (1,4) and a.status_active=1 and a.is_deleted=0 group by a.booking_no,a.job_no,b.body_part_id,b.lib_yarn_count_deter_id, b.gsm_weight,b.uom,a.dia_width, a.fabric_color_id,c.fabric_ref,c.rd_no,b.gsm_weight_type,g.item_size");
				
				foreach($determination_info_sql as $row)
				{
					$determination_info_arr[$row[csf('job_no')]][$row[csf('booking_no')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['rd_no']=$row[csf('rd_no')];
					$determination_info_arr[$row[csf('job_no')]][$row[csf('booking_no')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['fabric_ref']=$row[csf('fabric_ref')];
					$determination_info_arr[$row[csf('job_no')]][$row[csf('booking_no')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['weight_type']=$row[csf('weight_type')];
					$determination_info_arr[$row[csf('job_no')]][$row[csf('booking_no')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['cutable_width']=$row[csf('cutable_width')];
				}


			}
			else
			{
				$sql = "SELECT null as job_no, null as style_ref_no,null as season_buyer_wise,null as season_year,null as brand_id, a.buyer_id, null as order_uom, null as  id,null as ref_no, null as po_qnty_in_pcs, b.lib_yarn_count_deter_id,a.booking_no,a.id as booking_id
				 from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.company_id=$company_id $search_con2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_type in (4) and b.fabric_color=$color_id  and b.lib_yarn_count_deter_id= $from_fabric_id 
				group by a.buyer_id, b.lib_yarn_count_deter_id,a.booking_no,a.id";
				$nameArr=sql_select( $sql );
				$booking_nos="";
				foreach ($nameArr as $selectRow)
				{
					$booking_nos.="'".$selectRow[csf('booking_no')]."',";
				}
				$booking_nos=chop($booking_nos,",");

					/*$sql_qry = "SELECT a.job_no, a.style_ref_no,a.season_buyer_wise,a.season_year,a.brand_id, a.buyer_name, a.order_uom,b.grouping as ref_no, c.booking_no , d.id as booking_id
					from wo_po_details_master a, wo_po_break_down b, wo_booking_dtls c, wo_booking_mst d ,wo_pre_cost_fabric_cost_dtls e
					where a.job_no=b.job_no_mst and a.company_name=$company_id $search_con and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.id = c.po_break_down_id and d.booking_no = c.booking_no  and c.pre_cost_fabric_cost_dtls_id=e.id and c.job_no=e.job_no   and d.booking_type in (1,4) and c.status_active =1 and c.is_deleted=0 and a.garments_nature =3 and c.fabric_color_id=$color_id  and e.lib_yarn_count_deter_id= $from_fabric_id 
					group by a.id, a.job_no, a.style_ref_no,a.season_buyer_wise,a.season_year,a.brand_id, a.buyer_name, a.order_uom,b.grouping, a.total_set_qnty, b.po_quantity, b.pub_shipment_date,c.booking_no ,d.id 
					order by a.id desc";

					$nameArr=sql_select( $sql_qry );
					
					foreach ($nameArr as $selectRow)
					{
						$bookingData[$selectRow[csf('job_no')]][$selectRow[csf('style_ref_no')]][$selectRow[csf('buyer_name')]][$selectRow[csf('order_uom')]][$selectRow[csf('ref_no')]][$selectRow[csf('season_buyer_wise')]][$selectRow[csf('season_year')]][$selectRow[csf('brand_id')]]['booking_id']=$selectRow[csf('booking_id')];

						$bookingData[$selectRow[csf('job_no')]][$selectRow[csf('style_ref_no')]][$selectRow[csf('buyer_name')]][$selectRow[csf('order_uom')]][$selectRow[csf('ref_no')]][$selectRow[csf('season_buyer_wise')]][$selectRow[csf('season_year')]][$selectRow[csf('brand_id')]]['booking_no']=$selectRow[csf('booking_no')];
					}*/
			}



				/*$determination_info_sql = sql_select("SELECT b.body_part_id,b.lib_yarn_count_deter_id,b.uom, b.gsm_weight, a.booking_no as wopi_number, a.dia_width, a.fabric_color_id as color_id,a.rate,c.rd_no,c.fabric_ref from wo_booking_dtls a, wo_pre_cost_fabric_cost_dtls b ,lib_yarn_count_determina_mst c where a.pre_cost_fabric_cost_dtls_id=b.id and b.lib_yarn_count_deter_id=c.id and a.booking_no='$wopiNumber' and a.job_no=b.job_no and a.status_active=1 and a.is_deleted=0 and a.fabric_color_id=$color_id and b.lib_yarn_count_deter_id= $from_fabric_id and a.booking_no in($booking_nos)  group by b.body_part_id,a.booking_no, b.lib_yarn_count_deter_id,b.gsm_weight, a.dia_width, a.fabric_color_id,b.uom,a.rate,c.rd_no,c.fabric_ref");

				foreach($determination_info_sql as $row)
				{
					$determination_info_arr[$row[csf('lib_yarn_count_deter_id')]][$row[csf('uom')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('color_id')]][$row[csf('rate')]][$row[csf('wopi_number')]][$row[csf('body_part_id')]]['rd_no']=$row[csf('rd_no')];
					$determination_info_arr[$row[csf('lib_yarn_count_deter_id')]][$row[csf('uom')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('color_id')]][$row[csf('rate')]][$row[csf('wopi_number')]][$row[csf('body_part_id')]]['fabric_ref']=$row[csf('fabric_ref')];
				}*/




				/*$finish_to_req_qnty_sql=sql_select("SELECT d.id as po_id,d.job_no_mst, d.po_number, sum(b.fin_fab_qnty) as  fin_fab_qnty,b.booking_no,e.style_ref_no from wo_booking_dtls b, wo_po_break_down d ,wo_pre_cost_fabric_cost_dtls c, wo_po_details_master e
					where b.po_break_down_id=d.id and b.pre_cost_fabric_cost_dtls_id=c.id  and c.job_no=e.job_no and d.job_no_mst=e.job_no 
					and d.id in($txt_to_order_id) and b.fabric_color_id in($hide_color_id) and b.booking_no=$hdn_to_booking_no 
					and c.body_part_id=$cbo_to_body_part and  c.lib_yarn_count_deter_id=$txt_from_fabric_id
					and c.uom=$cbo_uom and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by d.id,d.job_no_mst, d.po_number ,b.booking_no,e.style_ref_no"); */


		}
		else
		{
			$sql = "SELECT a.job_no, a.style_ref_no,a.season_buyer_wise,a.season_year,a.brand_id, a.buyer_name, a.order_uom, LISTAGG(b.id, ',') WITHIN GROUP (ORDER BY b.id DESC) id,b.grouping as ref_no, sum(a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs 
			from wo_po_details_master a, wo_po_break_down b, wo_booking_dtls c, wo_booking_mst d 
			where a.id=b.job_id and b.id = c.po_break_down_id and d.booking_no = c.booking_no and d.booking_type in (1,4)  and  a.company_name=$company_id_from $search_con and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.garments_nature in(2,3) and c.status_active =1 and c.is_deleted=0  group by a.id, a.job_no, a.style_ref_no,a.season_buyer_wise,a.season_year,a.brand_id, a.buyer_name, a.order_uom,b.grouping order by a.id desc ";
			
			$sql_qry = "SELECT a.job_no, a.style_ref_no,a.season_buyer_wise,a.season_year,a.brand_id, a.buyer_name, a.order_uom,b.grouping as ref_no, c.booking_no , d.id as booking_id
			from wo_po_details_master a, wo_po_break_down b, wo_booking_dtls c, wo_booking_mst d 
			where a.id=b.job_id and a.company_name=$company_id_from $search_con and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.id = c.po_break_down_id and d.booking_no = c.booking_no and d.booking_type in (1,4) 
			and c.status_active =1 and c.is_deleted=0 and a.garments_nature in(2,3)
			group by a.job_no, a.style_ref_no,a.season_buyer_wise,a.season_year,a.brand_id, a.buyer_name, a.order_uom,b.grouping,c.booking_no ,d.id ";

			$nameArr=sql_select( $sql_qry );
			
			foreach ($nameArr as $selectRow)
			{
				$bookingData[$selectRow[csf('job_no')]][$selectRow[csf('style_ref_no')]][$selectRow[csf('buyer_name')]][$selectRow[csf('order_uom')]][$selectRow[csf('ref_no')]][$selectRow[csf('season_buyer_wise')]][$selectRow[csf('season_year')]][$selectRow[csf('brand_id')]]['booking_id']=$selectRow[csf('booking_id')];

				$bookingData[$selectRow[csf('job_no')]][$selectRow[csf('style_ref_no')]][$selectRow[csf('buyer_name')]][$selectRow[csf('order_uom')]][$selectRow[csf('ref_no')]][$selectRow[csf('season_buyer_wise')]][$selectRow[csf('season_year')]][$selectRow[csf('brand_id')]]['booking_no']=$selectRow[csf('booking_no')];
			}

			/*$sql = "SELECT a.job_no, a.style_ref_no, a.buyer_name, a.order_uom, LISTAGG(b.id, ',') WITHIN GROUP (ORDER BY b.id DESC) id,b.grouping as ref_no, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs, b.pub_shipment_date, c.booking_no , d.id as booking_id
			from wo_po_details_master a, wo_po_break_down b, wo_booking_dtls c, wo_booking_mst d
			where a.job_no=b.job_no_mst and a.company_name=$company_id $search_con and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.id = c.po_break_down_id and d.booking_no = c.booking_no and d.booking_type in (1,4) and c.status_active =1 and c.is_deleted=0 and a.garments_nature =3
			group by a.id, a.job_no, a.style_ref_no, a.buyer_name, a.order_uom,b.grouping, a.total_set_qnty, b.po_quantity, b.pub_shipment_date,c.booking_no ,d.id
			order by a.id desc";*/
		}
	}
	elseif ($transfer_criteria==1 && $popup_type==2) // company to company and to order popup
	{
		$sql = "SELECT a.job_no, a.style_ref_no,a.season_buyer_wise,a.season_year,a.brand_id, a.buyer_name, a.order_uom, b.id,b.grouping as ref_no, b.po_number, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs, b.pub_shipment_date, c.booking_no , d.id as booking_id
		from wo_po_details_master a, wo_po_break_down b, wo_booking_dtls c, wo_booking_mst d
		where a.job_no=b.job_no_mst and a.company_name=$company_id $search_con and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.id = c.po_break_down_id and d.booking_no = c.booking_no and d.booking_type in (1,4) and c.status_active =1 and c.is_deleted=0 and a.garments_nature in(2,3)
		group by a.id, a.job_no, a.style_ref_no,a.season_buyer_wise,a.season_year,a.brand_id, a.buyer_name, a.order_uom, b.id,b.grouping, b.po_number, a.total_set_qnty, b.po_quantity, b.pub_shipment_date,c.booking_no ,d.id
		order by a.id desc";


		
	}
	else
	{
		if($transfer_criteria==4 && $popup_type==2){ $from_fabrAndColor="and c.fabric_color_id=$color_id  and e.lib_yarn_count_deter_id= $from_fabric_id"; }
		$sql = "SELECT a.job_no, a.style_ref_no,a.season_buyer_wise,a.season_year,a.brand_id, a.buyer_name, a.order_uom, b.id,b.grouping as ref_no, b.po_number, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs, b.pub_shipment_date, c.booking_no , d.id as booking_id
		from wo_po_details_master a, wo_po_break_down b, wo_booking_dtls c, wo_booking_mst d,wo_pre_cost_fabric_cost_dtls e
		where a.job_no=b.job_no_mst and a.company_name=$company_id_from $search_con and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.id = c.po_break_down_id and d.booking_no = c.booking_no and c.pre_cost_fabric_cost_dtls_id=e.id and c.job_no=e.job_no $from_fabrAndColor and d.booking_type in (1,4) and c.status_active =1 and c.is_deleted=0 and e.status_active =1 and e.is_deleted=0 and a.garments_nature in(2,3)
		group by a.id, a.job_no, a.style_ref_no,a.season_buyer_wise,a.season_year,a.brand_id, a.buyer_name, a.order_uom, b.id,b.grouping, b.po_number, a.total_set_qnty, b.po_quantity, b.pub_shipment_date,c.booking_no ,d.id
		order by a.id desc";
	}

	
	if(($transfer_criteria==3 || $transfer_criteria==6)|| ($transfer_criteria==2 && $style_and_po_wise_variable==1))
	{
		$width=870;
	}
	else
	{
		$width=1030;
		$po_th='<th width="160">PO No</th>';
	}
	?>
	
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width; ?>" class="rpt_table" align="left">
			<thead>
				<th width="40">SL</th>
				<th width="100">Job No</th>
				<th width="160">Style No</th>
				<? echo $po_th;?>
				<th width="100">Ref. No</th>

				<th width="100">Buyer</th>
				<th width="100">Season</th>
				<th width="100">Season Year</th>
				<th width="100">Brand</th>

				<th width="100">Booking No</th>
				<th width="60">UOM</th>
			</thead>
		</table>
		<div style="width:<? echo $width+20; ?>px; overflow-y:scroll; max-height:220px; float: left;" id="buyer_list_view" align="left">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width; ?>" class="rpt_table" id="tbl_list_search" >
				<?
				$i=1; $po_row_id='';
				$nameArray=sql_select( $sql );
				foreach ($nameArray as $selectResult)
				{

					if(($transfer_criteria==3) || ($transfer_criteria==2 && $style_and_po_wise_variable==1))
					{
						$bookingID=$bookingData[$selectResult[csf('job_no')]][$selectResult[csf('style_ref_no')]][$selectResult[csf('buyer_name')]][$selectResult[csf('order_uom')]][$selectResult[csf('ref_no')]][$selectResult[csf('season_buyer_wise')]][$selectResult[csf('season_year')]][$selectResult[csf('brand_id')]]['booking_id'];
						$bookingNO=$bookingData[$selectResult[csf('job_no')]][$selectResult[csf('style_ref_no')]][$selectResult[csf('buyer_name')]][$selectResult[csf('order_uom')]][$selectResult[csf('ref_no')]][$selectResult[csf('season_buyer_wise')]][$selectResult[csf('season_year')]][$selectResult[csf('brand_id')]]['booking_no'];
						
						$gsm_weight=$bookingData[$selectResult[csf('job_no')]][$selectResult[csf('style_ref_no')]][$selectResult[csf('buyer_name')]][$selectResult[csf('order_uom')]][$selectResult[csf('ref_no')]][$selectResult[csf('season_buyer_wise')]][$selectResult[csf('season_year')]][$selectResult[csf('brand_id')]]['gsm_weight'];
						$dia_width=$bookingData[$selectResult[csf('job_no')]][$selectResult[csf('style_ref_no')]][$selectResult[csf('buyer_name')]][$selectResult[csf('order_uom')]][$selectResult[csf('ref_no')]][$selectResult[csf('season_buyer_wise')]][$selectResult[csf('season_year')]][$selectResult[csf('brand_id')]]['dia_width'];

						//echo $selectResult[csf('job_no')].'='.$bookingNO.'='.$selectResult[csf('lib_yarn_count_deter_id')].'='.$gsm_weight.'='.$dia_width.'<br/>';

						$rd_no=$determination_info_arr[$selectResult[csf('job_no')]][$bookingNO][$selectResult[csf('lib_yarn_count_deter_id')]][$gsm_weight][$dia_width]['rd_no'];
						$fabric_ref=$determination_info_arr[$selectResult[csf('job_no')]][$bookingNO][$selectResult[csf('lib_yarn_count_deter_id')]][$gsm_weight][$dia_width]['fabric_ref'];
						$weight_type=$determination_info_arr[$selectResult[csf('job_no')]][$bookingNO][$selectResult[csf('lib_yarn_count_deter_id')]][$gsm_weight][$dia_width]['weight_type'];
						$cutable_width=$determination_info_arr[$selectResult[csf('job_no')]][$bookingNO][$selectResult[csf('lib_yarn_count_deter_id')]][$gsm_weight][$dia_width]['cutable_width'];
					}
					else
					{
						$bookingID=$selectResult[csf('booking_id')];
						$bookingNO=$selectResult[csf('booking_no')];
					}

					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";

					if(in_array($selectResult[csf('id')],$hidden_po_id)) 
					{ 
						if($po_row_id=="") $po_row_id=$i; else $po_row_id.=",".$i;
					}
					$poIds=implode(',',array_unique(explode(',', $selectResult[csf('id')])));

					//echo $rd_no.'='. $fabric_ref.'='. $weight_type .'='.$cutable_width;

					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value('<? echo $poIds;?>','<? echo $selectResult[csf('po_number')];?>','<? echo $bookingNO; ?>','<? echo $bookingID;?>','<? echo $selectResult[csf('style_ref_no')];?>','<? echo $selectResult[csf('job_no')];?>','<? echo $rd_no;?>','<? echo $fabric_ref;?>','<? echo $weight_type;?>','<? echo $cutable_width;?>','<? echo $selectResult[csf('booking_type')];?>')"> 
						<td width="40" align="center"><?php echo "$i";  ?>
							<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $poIds; ?>"/>	
							<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<? echo $selectResult[csf('po_number')]; ?>"/>
							<input type="hidden" name="txt_buyer" id="txt_buyer<?php echo $i ?>" value="<? echo $selectResult[csf('buyer_name')]; ?>"/>
							<input type="hidden" name="txt_styleRef" id="txt_styleRef<?php echo $i ?>" value="<? echo $selectResult[csf('style_ref_no')]; ?>"/>	
						</td>	
						<td width="100" title="<? echo $poIds; ?>"><p><? echo $selectResult[csf('job_no')]; ?></p></td>
						<td width="160"><p><? echo $selectResult[csf('style_ref_no')]; ?></p></td>
						<? 
					
						if(($transfer_criteria==1 || $transfer_criteria==4) || ($transfer_criteria==2 && $style_and_po_wise_variable==2))
						{ 
							?>
							<td width="160"><p><? echo $selectResult[csf('po_number')]; ?></p></td>
							<?
						}
						
						?>
						<td width="100"><p><? echo $selectResult[csf('ref_no')]; ?></p></td>

						<td width="100"><p><? echo $buyer_library[$selectResult[csf('buyer_name')]]; ?></p></td>
						<td width="100"><p><? echo $season_library[$selectResult[csf('season_buyer_wise')]]; ?></p></td>
						<td width="100"><p><? echo $selectResult[csf('season_year')]; ?></p></td>
						<td width="100"><p><? echo $brand_library[$selectResult[csf('brand_id')]]; ?></p></td>

						<td width="100"><p><? echo $bookingNO; ?></p></td>
						<td width="60" align="center"><p><? echo $unit_of_measurement[$selectResult[csf('order_uom')]]; ?></p></td>
					</tr>
				<?
				$i++;
			}
			?>
			<input type="hidden" name="txt_po_row_id" id="txt_po_row_id" value="<?php echo $po_row_id; ?>"/>
		</table>
	</div>
	<?
	exit();
}

if($action=="show_transfer_listview")
{
	$data=explode("_", $data);

	$product_name_arr = return_library_array("select id, product_name_details from product_details_master where item_category_id=3","id","product_name_details");
	//$arr=array(0=>$store_arr,1=>$store_arr,2=>$product_name_arr,4=>$color_arr);
	if(($data[1]==3 || $data[1]==6) || ($data[1]==2 && $data[2]==1))
	{
		
		$result=sql_select("select a.id, b.from_store, b.to_store, b.from_prod_id,sum( b.transfer_qnty) as transfer_qnty, b.color_id from inv_item_transfer_style_wise a, inv_item_transfer_dtls b where a.id=b.mst_id_style_wise and b.mst_id='$data[0]' and b.status_active = '1' and b.is_deleted = '0'  and a.status_active = '1' and a.is_deleted = '0' group by a.id, b.from_store, b.to_store, b.from_prod_id, b.color_id");
		$style_wise=1;

	 	//echo  create_list_view("list_view", "From Store,To Store,Item Description,Transfered Qnty,Color", "130,130,280,130,110","880","200",0, $sql, "get_php_form_data", "id", "'populate_transfer_details_form_data'", 0, "from_store,to_store,from_prod_id,0,color_id", $arr, "from_store,to_store,from_prod_id,transfer_qnty,color_id", "requires/woven_finish_fabric_transfer_controller",'','0,0,0,2,0');
	}
	else
	{
		$result=sql_select("select id, from_store, to_store, from_prod_id, transfer_qnty, color_id from inv_item_transfer_dtls where mst_id='$data[0]' and status_active = '1' and is_deleted = '0'");
		$style_wise=0;
		//echo  create_list_view("list_view", "From Store,To Store,Item Description,Transfered Qnty,Color", "130,130,280,130,110","880","200",0, $sql, "get_php_form_data", "id", "'populate_transfer_details_form_data'", 0, "from_store,to_store,from_prod_id,0,color_id", $arr, "from_store,to_store,from_prod_id,transfer_qnty,color_id", "requires/woven_finish_fabric_transfer_controller",'','0,0,0,2,0');
	}
	?>
		<table class="rpt_table" border="1" cellpadding="2" cellspacing="0" style="width:880px" rules="all">
        	<thead>
            	<tr>
                	<th>From Store</th>
                    <th>To Store</th>
                    <th>Item Description</th>
                    <th>Transfered Qnty</th>
                    <th>Color</th>
                </tr>
            </thead>
            <tbody>
            	<? 
				foreach($result as $row){					
					if($i%2==0)
						$bgcolor="#E9F3FF";
					else 
						$bgcolor="#FFFFFF";
 					
 				?>
                	<tr bgcolor="<? echo $bgcolor; ?>" onClick='get_php_form_data("<? echo $row[csf("id")]."**".$style_wise;?>","populate_transfer_details_form_data","requires/woven_finish_fabric_transfer_controller")' style="cursor:pointer" >
                        <td width="130"><? echo $store_arr[$row[csf("from_store")]]; ?></td>
                        <td width="130"><p><? echo $store_arr[$row[csf("to_store")]]; ?></p></td>
                        <td width="280" align="center"><p><? echo $product_name_arr[$row[csf("from_prod_id")]]; ?></p></td>
                        <td width="130" align="right"><p><? echo number_format($row[csf("transfer_qnty")],2) ; ?></p></td>
                        <td align="center"><p><? echo $color_arr[$row[csf("color_id")]]; ?></p></td>
                   </tr>
                <? $i++; } ?>
                	
            </tbody>
        </table>
        <?
	exit();
}

if($action=='populate_transfer_details_form_data')
{
	$data=explode("**", $data);
	$mst_id=$data[0];
	$style_wise=$data[1];

	if($style_wise==1)
	{
		//$data_array=sql_select("SELECT a.transfer_criteria, a.company_id, a.to_company, a.location_id, a.to_location_id, b.from_order_id, b.to_order_id, b.id, b.mst_id, b.from_store, b.to_store, b.floor_id, b.room, b.rack, b.shelf, b.bin_box, b.to_floor_id, b.to_room, b.to_rack, b.to_shelf, b.to_bin_box, b.from_prod_id, b.to_prod_id, b.transfer_qnty, b.rate, b.transfer_value, b.color_id, b.batch_id, b.to_batch_id, b.body_part_id, b.to_body_part, b.to_ord_book_id, b.to_ord_book_no, b.remarks, b.to_trans_id, b.trans_id,b.uom from inv_item_transfer_mst a, inv_item_transfer_dtls b where b.id='$mst_id' and a.id=b.mst_id");

		$data_array=sql_select("SELECT a.transfer_criteria, a.company_id, a.to_company,listagg(cast(b.from_order_id as varchar(4000)),',') within group(order by b.from_order_id) as from_order_id,listagg(cast(b.to_order_id as varchar(4000)),',') within group(order by b.to_order_id) as to_order_id,listagg(cast(b.id as varchar(4000)),',') within group(order by b.id) as id,b.mst_id,c.id as style_wise_mst_id, c.from_store, c.to_store, c.floor_id, c.room, c.rack, c.shelf, c.bin_box, c.to_floor_id, c.to_room, c.to_rack, c.to_shelf, c.to_bin_box, c.from_prod_id,c.to_prod_id, c.transfer_qnty, d.cons_rate as rate, c.transfer_value, c.color_id, c.batch_id, c.to_batch_id, c.body_part_id,c.to_body_part, c.from_ord_book_id, c.from_ord_book_no, c.to_ord_book_id, c.to_ord_book_no, b.remarks ,listagg(cast(b.to_trans_id as varchar(4000)),',') within group(order by b.to_trans_id) as to_trans_id, listagg(cast(b.trans_id as varchar(4000)),',') within group(order by b.trans_id) as trans_id,b.uom,c.from_style,c.to_style,c.from_fabric_id ,d.fabric_ref,d.rd_no,d.weight_type,d.cutable_width,d.width_editable,d.weight_editable from inv_item_transfer_mst a, inv_item_transfer_dtls b ,inv_item_transfer_style_wise c,inv_transaction d where c.id='$mst_id' and a.id=b.mst_id and b.mst_id_style_wise=c.id and b.trans_id=d.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by  a.transfer_criteria, a.company_id, a.to_company,b.mst_id,c.id, c.from_store, c.to_store, c.floor_id, c.room, c.rack, c.shelf, c.bin_box, c.to_floor_id, c.to_room, c.to_rack, c.to_shelf, c.to_bin_box, c.from_prod_id,c.to_prod_id, c.transfer_qnty, d.cons_rate, c.transfer_value, c.color_id, c.batch_id, c.to_batch_id, c.body_part_id,c.to_body_part, c.from_ord_book_id, c.from_ord_book_no, c.to_ord_book_id, c.to_ord_book_no, b.remarks,b.uom,c.from_style,c.to_style,c.from_fabric_id,d.fabric_ref,d.rd_no,d.weight_type,d.cutable_width,d.width_editable,d.weight_editable");
	}
	else
	{
		$data_array=sql_select("SELECT a.transfer_criteria, a.company_id, a.to_company, a.location_id, a.to_location_id, b.from_order_id, b.to_order_id, b.id, b.mst_id, b.from_store, b.to_store, b.floor_id, b.room, b.rack, b.shelf, b.bin_box, b.to_floor_id, b.to_room, b.to_rack, b.to_shelf, b.to_bin_box, b.from_prod_id, b.to_prod_id, b.transfer_qnty, b.rate, b.transfer_value, b.color_id, b.batch_id, b.to_batch_id, b.body_part_id, b.to_body_part, b.to_ord_book_id, b.to_ord_book_no, b.remarks, b.to_trans_id, b.trans_id,b.uom from inv_item_transfer_mst a, inv_item_transfer_dtls b where b.id='$mst_id' and a.id=b.mst_id");
	}
	
	$order_no=explode(",",trim($data_array[0][csf('from_order_id')].",".$data_array[0][csf('to_order_id')],"  , "));
	$order_name_arr=return_library_array( "select id ,po_number from wo_po_break_down where status_active=1 and is_deleted =0 and id in(".implode(",",$order_no).")",'id','po_number');
	

	//==============================================

	$sql_stock1=sql_select("select b.po_breakdown_id as order_id, a.prod_id, a.batch_id,
	sum(case when a.transaction_type in(1) and b.trans_type in(1) then b.quantity end) as rcv_qty,
	sum(case when a.transaction_type in(2) and b.trans_type in(2) then b.quantity end) as issue_qty  
	from inv_transaction a, order_wise_pro_details b
	where a.id=b.trans_id and b.entry_form in (17,19) and a.store_id = ".$data_array[0][csf('from_store')]." and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category=3  and a.company_id=".$data_array[0][csf('company_id')]." and a.body_part_id='".$data_array[0][csf('body_part_id')]."' and a.floor_id='".$data_array[0][csf('floor_id')]."' and a.room ='".$data_array[0][csf('room')]."' and a.rack ='".$data_array[0][csf('rack')]."' and a.self ='".$data_array[0][csf('shelf')]."' and a.bin_box ='".$data_array[0][csf('bin_box')]."'
	group by b.po_breakdown_id, a.prod_id, a.batch_id");


	foreach($sql_stock1 as $value)
	{
		$stock_data_arrray[$value[csf('order_id')]][$value[csf('prod_id')]][$value[csf('batch_id')]]['rcv_qty'] +=$value[csf('rcv_qty')];
		$stock_data_arrray[$value[csf('order_id')]][$value[csf('prod_id')]][$value[csf('batch_id')]]['issue_qty'] +=$value[csf('issue_qty')];
	}

	$sql_stock2=sql_select("select b.po_breakdown_id as order_id, a.prod_id, a.pi_wo_batch_no,
	sum(case when a.transaction_type in(4,5) and b.trans_type in(4,5) then b.quantity end) as rcv_qty,
	sum(case when a.transaction_type in(3,6) and b.trans_type in(3,6) then b.quantity end) as issue_qty  
	from inv_transaction a, order_wise_pro_details b
	where a.id=b.trans_id and b.entry_form in (202,209,258) and a.store_id = ".$data_array[0][csf('from_store')]." and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category=3 and a.company_id=".$data_array[0][csf('company_id')]."  and a.body_part_id='".$data_array[0][csf('body_part_id')]."' and a.floor_id='".$data_array[0][csf('floor_id')]."' and a.room ='".$data_array[0][csf('room')]."' and a.rack ='".$data_array[0][csf('rack')]."' and a.self ='".$data_array[0][csf('shelf')]."' and a.bin_box ='".$data_array[0][csf('bin_box')]."'
	group by b.po_breakdown_id, a.prod_id, a.pi_wo_batch_no");


	foreach($sql_stock2 as $value)
	{
		$stock_data_arrray[$value[csf('order_id')]][$value[csf('prod_id')]][$value[csf('pi_wo_batch_no')]]['rcv_qty'] +=$value[csf('rcv_qty')];
		$stock_data_arrray[$value[csf('order_id')]][$value[csf('prod_id')]][$value[csf('pi_wo_batch_no')]]['issue_qty'] +=$value[csf('issue_qty')];
	}

	//==============================================
	/*echo "<pre>";
	print_r($stock_data_arrray); die;*/
	foreach ($data_array as $row)
	{ 
		if ($row[csf("transfer_criteria")]==1 || $row[csf("transfer_criteria")]==6) {
			$company_id=$row[csf("to_company")];
		}
		else
		{
			$company_id=$row[csf("company_id")];
		}

		if($style_wise==1 && $row[csf("transfer_criteria")]==2)
		{
			foreach ($order_name_arr as $poNos) {
				$poNOs.=$poNos.",";
			}
			$poNOs=chop($poNOs,",");
		}


		echo "document.getElementById('update_dtls_id').value 				= '".$row[csf("id")]."';\n";
		

		echo "load_room_rack_self_bin('requires/woven_finish_fabric_transfer_controller*3', 'store','from_store_td', $('#cbo_company_id').val(),'"."',this.value);\n";
		echo "document.getElementById('cbo_store_name').value 				= '".$row[csf("from_store")]."';\n";

		echo "load_room_rack_self_bin('requires/woven_finish_fabric_transfer_controller*3', 'floor','floor_td', $('#cbo_company_id').val(),'"."','".$row[csf('from_store')]."',this.value);\n";
		echo "document.getElementById('cbo_floor').value 				= '".$row[csf("floor_id")]."';\n";

		if($row[csf("floor_id")]>0)
		{
			echo "load_room_rack_self_bin('requires/woven_finish_fabric_transfer_controller*3', 'room','room_td', $('#cbo_company_id').val(),'"."','".$row[csf('from_store')]."','".$row[csf('floor_id')]."',this.value);\n";
			echo "document.getElementById('cbo_room').value 				= '".$row[csf("room")]."';\n";
		}
		if($row[csf("room")]>0)
		{
			echo "load_room_rack_self_bin('requires/woven_finish_fabric_transfer_controller*3', 'rack','rack_td', $('#cbo_company_id').val(),'"."','".$row[csf('from_store')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."',this.value);\n";
			echo "document.getElementById('txt_rack').value 				= '".$row[csf("rack")]."';\n";
		}

		if($row[csf("rack")]>0)
		{
			echo "load_room_rack_self_bin('requires/woven_finish_fabric_transfer_controller*3', 'shelf','shelf_td', $('#cbo_company_id').val(),'"."','".$row[csf('from_store')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."','".$row[csf('rack')]."',this.value);\n";
			echo "document.getElementById('txt_shelf').value 				= '".$row[csf("shelf")]."';\n";
		}

		if($row[csf("shelf")]>0)
		{
			echo "load_room_rack_self_bin('requires/woven_finish_fabric_transfer_controller*3', 'bin','bin_td', $('#cbo_company_id').val(),'"."','".$row[csf('from_store')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."','".$row[csf('rack')]."','".$row[csf('shelf')]."',this.value);\n";
			echo "document.getElementById('cbo_bin').value 				= '".$row[csf("bin_box")]."';\n";
		}

		echo "reset_room_rack_self_bin('store*to');\n";

		echo "load_room_rack_self_bin('requires/woven_finish_fabric_transfer_controller*3*cbo_store_name_to', 'store','to_store_td', '".$company_id."','"."',this.value);\n";
		echo "document.getElementById('cbo_store_name_to').value 				= '".$row[csf("to_store")]."';\n";
		echo "document.getElementById('previous_to_store').value 				= '".$row[csf("to_store")]."';\n";

		echo "load_room_rack_self_bin('requires/woven_finish_fabric_transfer_controller*3*cbo_floor_to', 'floor','floor_td_to', '".$company_id."','"."','".$row[csf('to_store')]."',this.value);\n";
		echo "document.getElementById('cbo_floor_to').value 			= '".$row[csf("to_floor_id")]."';\n";

		
		if($row[csf("to_floor_id")]>0){
			echo "load_room_rack_self_bin('requires/woven_finish_fabric_transfer_controller*3*cbo_room_to', 'room','room_td_to', '".$company_id."','"."','".$row[csf('to_store')]."','".$row[csf('to_floor_id')]."',this.value);\n";
			echo "document.getElementById('cbo_room_to').value 				= '".$row[csf("to_room")]."';\n";
		}
		if($row[csf("to_room")]>0){
			echo "load_room_rack_self_bin('requires/woven_finish_fabric_transfer_controller*3*txt_rack_to', 'rack','rack_td_to','".$company_id."','"."','".$row[csf('to_store')]."','".$row[csf('to_floor_id')]."','".$row[csf('to_room')]."',this.value);\n";
			echo "document.getElementById('txt_rack_to').value 				= '".$row[csf("to_rack")]."';\n";
		}
		if($row[csf("to_rack")]>0){
			echo "load_room_rack_self_bin('requires/woven_finish_fabric_transfer_controller*3*txt_shelf_to', 'shelf','shelf_td_to','".$company_id."','"."','".$row[csf('to_store')]."','".$row[csf('to_floor_id')]."','".$row[csf('to_room')]."','".$row[csf('to_rack')]."',this.value);\n";
			echo "document.getElementById('txt_shelf_to').value 			= '".$row[csf("to_shelf")]."';\n";
		}

		if($row[csf("to_shelf")]>0)
		{
			echo "load_room_rack_self_bin('requires/woven_finish_fabric_transfer_controller*3*cbo_bin_to', 'bin','bin_td_to', $('#cbo_company_id').val(),'"."','".$row[csf('to_store')]."','".$row[csf('to_floor_id')]."','".$row[csf('to_room')]."','".$row[csf('to_rack')]."','".$row[csf('to_shelf')]."',this.value);\n";
			echo "document.getElementById('cbo_bin_to').value 				= '".$row[csf("to_bin_box")]."';\n";
		}

		echo "document.getElementById('txt_transfer_qnty').value 			= '".$row[csf("transfer_qnty")]."';\n";
		echo "document.getElementById('hidden_transfer_qnty').value 		= '".$row[csf("transfer_qnty")]."';\n";
		echo "document.getElementById('hide_color_id').value 				= '".$row[csf("color_id")]."';\n";
		echo "document.getElementById('txt_color').value 					= '".$color_arr[$row[csf("color_id")]]."';\n";
		
		echo "document.getElementById('batch_id').value 					= '".$row[csf("batch_id")]."';\n";
		echo "document.getElementById('previous_from_batch_id').value 		= '".$row[csf("batch_id")]."';\n";
		echo "document.getElementById('previous_to_batch_id').value 		= '".$row[csf("to_batch_id")]."';\n";
		
		$batch_no = return_field_value("batch_no", "pro_batch_create_mst", "id=".$row[csf("batch_id")]);
		echo "document.getElementById('txt_batch_no').value 				= '".$batch_no."';\n";
		if($style_wise==1 && $row[csf("transfer_criteria")]==2)
		{
			echo "document.getElementById('txt_from_order_no').value 			= '".$poNOs."';\n";
		}
		else
		{
			echo "document.getElementById('txt_from_order_no').value 			= '".$order_name_arr[$row[csf("from_order_id")]]."';\n";
		}

		echo "document.getElementById('txt_from_order_id').value 			= '".$row[csf("from_order_id")]."';\n";
		echo "document.getElementById('from_product_id').value 				= '".$row[csf("from_prod_id")]."';\n";
		echo "document.getElementById('previous_from_prod_id').value 		= '".$row[csf("from_prod_id")]."';\n";

		echo "document.getElementById('hidden_fabric_ref').value 			= '".$row[csf("fabric_ref")]."';\n";
		echo "document.getElementById('hidden_rd_no').value 				= '".$row[csf("rd_no")]."';\n";
		echo "document.getElementById('hidden_weight_type').value 			= '".$row[csf("weight_type")]."';\n";
		echo "document.getElementById('hidden_cutable_width').value 		= '".$row[csf("cutable_width")]."';\n";
		echo "document.getElementById('hidden_width').value 				= '".$row[csf("width_editable")]."';\n";
		echo "document.getElementById('hidden_weight').value 				= '".$row[csf("weight_editable")]."';\n";
		if($style_wise==1 && $row[csf("transfer_criteria")]==2)
		{
			echo "document.getElementById('txt_to_order_no').value 				= '".$poNOs."';\n";
		}
		else
		{
			echo "document.getElementById('txt_to_order_no').value 				= '".$order_name_arr[$row[csf("to_order_id")]]."';\n";
		}
		echo "document.getElementById('txt_to_order_id').value 				= '".$row[csf("to_order_id")]."';\n";
		echo "document.getElementById('previous_to_order_id').value 		= '".$row[csf("to_order_id")]."';\n";
		echo "document.getElementById('previous_to_prod_id').value 			= '".$row[csf("to_prod_id")]."';\n";
		echo "document.getElementById('hdn_from_booking_id').value 			= '".$row[csf("from_ord_book_id")]."';\n";
		echo "document.getElementById('hdn_from_booking_no').value 			= '".$row[csf("from_ord_book_no")]."';\n";
		echo "document.getElementById('hdn_to_booking_id').value 			= '".$row[csf("to_ord_book_id")]."';\n";
		echo "document.getElementById('hdn_to_booking_no').value 			= '".$row[csf("to_ord_book_no")]."';\n";
		echo "document.getElementById('txt_remarks').value 					= '".$row[csf("remarks")]."';\n";
		
		echo "document.getElementById('txt_rate').value 					= '".$row[csf("rate")]."';\n";
		echo "document.getElementById('txt_transfer_value').value 			= '".$row[csf("transfer_value")]."';\n";
		echo "document.getElementById('cbo_uom').value 						= '".$row[csf("uom")]."';\n";
		

		if($row[csf("transfer_criteria")]==2 || $row[csf("transfer_criteria")]==3 || $row[csf("transfer_criteria")]==6)
		{
			$to_body_part = $row[csf('to_body_part')];
		}else{
			$to_body_part = 0;
		}

		echo "load_drop_down('requires/woven_finish_fabric_transfer_controller', ".$to_body_part."+'_'+'".$row[csf('to_order_id')]."'+'_'+".$row[csf("from_prod_id")]."+'_' +".$row[csf("transfer_criteria")]." +'_'+2, 'load_body_part', 'to_body_part' );";
		echo "document.getElementById('cbo_to_body_part').value 			= '".$row[csf("to_body_part")]."';\n";

		echo "load_drop_down('requires/woven_finish_fabric_transfer_controller',".$row[csf('body_part_id')]."+'_'+'".$row[csf("from_order_id")]."'+'_'+".$row[csf("from_prod_id")]."+'_'+".$row[csf("transfer_criteria")]."+'_'+1, 'load_body_part', 'from_body_td' );\n";


		$sql=sql_select("select product_name_details,current_stock,avg_rate_per_unit from product_details_master where id=".$row[csf('from_prod_id')]);

		//echo "===".$stock_data_arrray[$row[csf("from_order_id")]][$row[csf("from_prod_id")]][$row[csf("batch_id")]]['rcv_qty'] ."-", $stock_data_arrray[$row[csf("from_order_id")]][$row[csf("from_prod_id")]][$row[csf("batch_id")]]['issue_qty']."+".$row[csf("transfer_qnty")]."===";


		$stockQty = ($stock_data_arrray[$row[csf("from_order_id")]][$row[csf("from_prod_id")]][$row[csf("batch_id")]]['rcv_qty'] - $stock_data_arrray[$row[csf("from_order_id")]][$row[csf("from_prod_id")]][$row[csf("batch_id")]]['issue_qty'])+$row[csf("transfer_qnty")];
		
		
		echo "document.getElementById('txt_item_desc').value 				= '".$sql[0][csf("product_name_details")]."';\n";
		echo "document.getElementById('txt_current_stock').value 			= '".$stockQty."';\n";
		echo "document.getElementById('hidden_current_stock').value 		= '".$stockQty."';\n";

		echo "document.getElementById('update_trans_issue_id').value 		= '".$row[csf("trans_id")]."';\n"; 
		echo "document.getElementById('update_trans_recv_id').value 		= '".$row[csf("to_trans_id")]."';\n";
		//echo "$('#cbo_store_name').attr('disabled','disabled');\n";
		echo "disable_enable_fields('cbo_store_name*cbo_floor*cbo_room*txt_rack*txt_shelf*cbo_bin*cbo_store_name_to',1);\n";
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_yarn_transfer_entry',1,1);\n";


		if(($row[csf("transfer_criteria")]==3) || ($row[csf("transfer_criteria")]==2 && $style_wise==1)) 
		{
			echo "document.getElementById('txt_from_style_no').value 	= '".$row[csf("from_style")]."';\n";
			echo "document.getElementById('txt_to_style_no').value 		= '".$row[csf("to_style")]."';\n";
			echo "document.getElementById('txt_from_fabric_id').value 	= '".$row[csf("from_fabric_id")]."';\n";
			echo "document.getElementById('update_style_wise_mst_id').value = '".$row[csf("style_wise_mst_id")]."';\n";


			echo "$('#txt_from_style_no').attr('disabled','disabled');\n";
			echo "$('#txt_to_style_no').attr('disabled','disabled');\n";
			echo "$('#txt_item_desc').attr('disabled','disabled');\n";
			echo "$('#cbo_from_body_part').attr('disabled','disabled');\n";
			echo "$('#cbo_to_body_part').attr('disabled','disabled');\n";
		}
		else if($row[csf("transfer_criteria")]==6)
		{
			echo "document.getElementById('txt_from_style_no').value 	= '".$row[csf("from_style")]."';\n";
			echo "document.getElementById('txt_to_style_no').value 		= '".$row[csf("to_ord_book_no")]."';\n";
			echo "document.getElementById('txt_from_fabric_id').value 	= '".$row[csf("from_fabric_id")]."';\n";
			echo "document.getElementById('update_style_wise_mst_id').value = '".$row[csf("style_wise_mst_id")]."';\n";


			echo "$('#txt_from_style_no').attr('disabled','disabled');\n";
			echo "$('#txt_to_style_no').attr('disabled','disabled');\n";
			echo "$('#txt_item_desc').attr('disabled','disabled');\n";
			echo "$('#cbo_from_body_part').attr('disabled','disabled');\n";
			echo "$('#cbo_to_body_part').attr('disabled','disabled');\n";
		} 
		
		exit();
	}
}

//data save update delete here------------------------------//
if($action=="save_update_delete")
{	 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	if($db_type==0)	{ mysql_query("BEGIN"); }
	if(str_replace("'","",$cbo_transfer_criteria)==2 || str_replace("'","",$cbo_transfer_criteria)==4)
	{
		$company_id_to=str_replace("'","",$cbo_company_id);
		$cbo_location_to=str_replace("'","",$cbo_location);
	}
	else
	{
		$company_id_to=str_replace("'","",$cbo_company_id_to);
		$cbo_location_to=str_replace("'","",$cbo_location_to);
	}
	
	if(str_replace("'","",$update_id)!="")
	{
		$is_acknowledge = return_field_value("b.id id", "inv_item_transfer_mst a,inv_item_trans_acknowledgement b", "a.id=b.challan_id and  a.id=$update_id and a.status_active=1", "id");
		if($is_acknowledge != "" )
		{
			echo "20**Update not allowed. This Transfer Challan is already Acknowledged.\nAcknowledge System ID = $is_acknowledge";
			disconnect($con);die;
		}

		$up_cond="";
		if(str_replace("'","",$update_trans_issue_id)!="")
		{
			$all_trans_id=str_replace("'","",$update_trans_issue_id);
			$up_cond=" and id not in($all_trans_id)";
		}
		
		$duplicate_product_check=return_field_value("id", "inv_transaction", " status_active=1 and transaction_type in(6) and prod_id=$from_product_id and mst_id=$update_id $up_cond", "id");
		if($duplicate_product_check)
		{
			echo "20**Duplicate Item Not Allow Within Same MRR and prod_id=$from_product_id and mst_id=$update_id $up_cond";disconnect($con);die;
		}
	}
	
	$up_trans_id=str_replace("'","",$update_trans_issue_id);
	if(str_replace("'","",$update_trans_recv_id)!="") $up_trans_id.=",".str_replace("'","",$update_trans_recv_id);
	$up_cond="";
	if(str_replace("'","",$update_trans_issue_id)!="") $up_cond=" and a.id not in($up_trans_id)";

	$cbo_floor = (str_replace("'", "", $cbo_floor) =="")? 0 :str_replace("'", "", $cbo_floor);
	$cbo_room = (str_replace("'", "", $cbo_room)=="")? 0 :str_replace("'", "", $cbo_room);
	$txt_rack = (str_replace("'", "", $txt_rack)=="")? 0 :str_replace("'", "", $txt_rack);
	$txt_shelf = (str_replace("'", "", $txt_shelf)=="")? 0 :str_replace("'", "", $txt_shelf);
	$cbo_bin = (str_replace("'", "", $cbo_bin)=="")? 0 :str_replace("'", "", $cbo_bin);

	$cbo_floor_to = (str_replace("'", "", $cbo_floor_to) =="")? 0 :str_replace("'", "", $cbo_floor_to);
	$cbo_room_to = (str_replace("'", "", $cbo_room_to)=="")? 0 :str_replace("'", "", $cbo_room_to);
	$txt_rack_to = (str_replace("'", "", $txt_rack_to)=="")? 0 :str_replace("'", "", $txt_rack_to);
	$txt_shelf_to = (str_replace("'", "", $txt_shelf_to)=="")? 0 :str_replace("'", "", $txt_shelf_to);
	$cbo_bin_to = (str_replace("'", "", $cbo_bin_to)=="")? 0 :str_replace("'", "", $cbo_bin_to);

	$txt_from_order_id=str_replace("'","",$txt_from_order_id);
	$txt_to_order_id=str_replace("'","",$txt_to_order_id);
	$hidden_weight_type=str_replace("'","",$hidden_weight_type);
	$hidden_weight=str_replace("'","",$hidden_weight);



	$sql_stock1=sql_select("select sum(case when a.transaction_type in(1) and b.trans_type in(1) then b.quantity end) as rcv_qty,
	sum(case when a.transaction_type in(2) and b.trans_type in(2) then b.quantity end) as issue_qty  
	from inv_transaction a, order_wise_pro_details b
	where a.id=b.trans_id and b.entry_form in (17,19) and a.store_id = $cbo_store_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category=3  and a.company_id=$cbo_company_id and b.po_breakdown_id in($txt_from_order_id) and a.prod_id=$from_product_id and a.batch_id=$batch_id and a.body_part_id=$cbo_from_body_part and a.floor_id=$cbo_floor and a.room =$cbo_room and a.rack =$txt_rack and a.self =$txt_shelf and a.bin_box =$cbo_bin $up_cond");

	$rcv_qty=$issue_qty = 0;
	foreach($sql_stock1 as $value)
	{
		$rcv_qty +=$value[csf('rcv_qty')];
		$issue_qty +=$value[csf('issue_qty')];
	}

	$sql_stock2=sql_select("select sum(case when a.transaction_type in(4,5) and b.trans_type in(4,5) then b.quantity end) as rcv_qty,
	sum(case when a.transaction_type in(3,6) and b.trans_type in(3,6) then b.quantity end) as issue_qty  
	from inv_transaction a, order_wise_pro_details b
	where a.id=b.trans_id and b.entry_form in (202,209,258) and a.store_id=$cbo_store_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category=3 and a.company_id=$cbo_company_id and b.po_breakdown_id in($txt_from_order_id) and a.prod_id=$from_product_id and a.pi_wo_batch_no=$batch_id and a.body_part_id=$cbo_from_body_part and a.floor_id=$cbo_floor and a.room =$cbo_room and a.rack =$txt_rack and a.self =$txt_shelf and a.bin_box =$cbo_bin $up_cond");

	foreach($sql_stock2 as $value)
	{
		$rcv_qty +=$value[csf('rcv_qty')];
		$issue_qty +=$value[csf('issue_qty')];
	}
	$trans_qnty=str_replace("'","",$txt_transfer_qnty)*1;
	$stock_qnty=$rcv_qty - $issue_qty;

	if($trans_qnty>$stock_qnty)
	{
		echo "20**Transfer Quantity Not Allow More Than Balance Quantity.\nBalance Quantity : $stock_qnty";disconnect($con);die;
	}

	//echo "10**fail=$stock_qnty";die;

	
	//echo "10**".str_replace("'","",$item_desc_w_space)."|==";die;
	if( $operation==0 ) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		$transfer_recv_num=''; $transfer_update_id='';
		$variable_auto_rcv = return_field_value("auto_transfer_rcv", "variable_settings_inventory", " company_name=$cbo_company_id and item_category_id=3 and status_active=1 and variable_list= 27", "auto_transfer_rcv");

		if($variable_auto_rcv == "")
		{
			$variable_auto_rcv = 1;
		}
		//echo "10**".$variable_auto_rcv; die;

		if(str_replace("'","",$update_id)=="")
		{
			if($db_type==0) $year_cond="YEAR(insert_date)"; 
			else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
			else $year_cond="";//defined Later
			
			$id = return_next_id_by_sequence("INV_ITEM_TRANSFER_MST_PK_SEQ", "inv_item_transfer_mst", $con);
			$new_transfer_system_id = explode("*", return_next_id_by_sequence("INV_ITEM_TRANSFER_MST_PK_SEQ", "inv_item_transfer_mst",$con,1,$cbo_company_id,"WFTE",258,date("Y",time()),$cbo_item_category ));

			if((str_replace("'", "", $cbo_transfer_criteria) == 3 || str_replace("'", "", $cbo_transfer_criteria) == 6) || (str_replace("'", "", $cbo_transfer_criteria) == 2 && str_replace("'", "", $style_and_po_wise_variable) ==1))
			{
				$field_array="id, transfer_prefix, transfer_prefix_number, transfer_system_id, company_id, location_id, challan_no, transfer_date, entry_form, transfer_criteria, to_company,to_location_id, item_category, inserted_by, insert_date";
			
				$data_array="(".$id.",'".$new_transfer_system_id[1]."',".$new_transfer_system_id[2].",'".$new_transfer_system_id[0]."',".$cbo_company_id.",".$cbo_location.",".$txt_challan_no.",".$txt_transfer_date.",258,".$cbo_transfer_criteria.",".$company_id_to.",".$cbo_location_to.",".$cbo_item_category.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

			}
			else
			{
				$field_array="id, transfer_prefix, transfer_prefix_number, transfer_system_id, company_id, location_id, challan_no, transfer_date, entry_form, transfer_criteria, to_company,to_location_id, from_order_id, to_order_id, item_category, inserted_by, insert_date";
			
				$data_array="(".$id.",'".$new_transfer_system_id[1]."',".$new_transfer_system_id[2].",'".$new_transfer_system_id[0]."',".$cbo_company_id.",".$cbo_location.",".$txt_challan_no.",".$txt_transfer_date.",258,".$cbo_transfer_criteria.",".$company_id_to.",".$cbo_location_to.",".$txt_from_order_id.",".$txt_to_order_id.",".$cbo_item_category.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			}		 
			
			
			$transfer_recv_num=$new_transfer_system_id[0];
			$transfer_update_id=$id;
		}
		else
		{
			//echo "10**fail=1";die;
			$field_array_update="challan_no*transfer_date*to_company*to_location_id*updated_by*update_date";
			$data_array_update=$txt_challan_no."*".$txt_transfer_date."*".$company_id_to."*".$cbo_location_to."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			
			$transfer_recv_num=str_replace("'","",$txt_system_id);
			$transfer_update_id=str_replace("'","",$update_id);

			if($variable_auto_rcv != 1)
			{
				//echo "10**fail=2";die;
				$pre_saved_store=sql_select("select a.id, b.from_store, b.to_store from inv_item_transfer_mst a, inv_item_transfer_dtls b where a.id = b.mst_id and a.entry_form = 258 and a.id = $transfer_update_id and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0 group by a.id, b.from_store, b.to_store");

				if( ($pre_saved_store[0][csf("from_store")]  !=str_replace("'", "", $cbo_store_name)) || ($pre_saved_store[0][csf("to_store")]  !=str_replace("'", "", $cbo_store_name_to)) )
				{
					echo "20**Duplicate From Store and To Store is not allowed in same MRR";
					disconnect($con);
					die;
				}
			}

		}
		if((str_replace("'", "", $cbo_transfer_criteria) == 3 || str_replace("'", "", $cbo_transfer_criteria) == 6) || (str_replace("'", "", $cbo_transfer_criteria) == 2 && str_replace("'", "", $style_and_po_wise_variable) == 1))
		{
			$field_array_trans="id, mst_id, company_id, prod_id, pi_wo_batch_no, item_category, transaction_type, transaction_date, store_id, floor_id, room, rack, self, bin_box, order_id, brand_id, body_part_id, cons_uom, cons_quantity, cons_rate, cons_amount, balance_qnty, balance_amount, inserted_by, insert_date,fabric_ref,rd_no,weight_type,cutable_width,width_editable,weight_editable,transfer_wo_rate";
		}
		else
		{
			//$id_trans=return_next_id( "id", "inv_transaction", 1 ) ;
			$field_array_trans="id, mst_id, company_id, prod_id, pi_wo_batch_no, item_category, transaction_type, transaction_date, store_id, floor_id, room, rack, self, bin_box, order_id, brand_id, body_part_id, cons_uom, cons_quantity, cons_rate, cons_amount, balance_qnty, balance_amount,transfer_wo_rate,weight_type,cutable_width,fabric_ref,rd_no,width_editable,weight_editable,inserted_by,insert_date";

			
		}
		//$id_dtls=return_next_id( "id", "inv_item_transfer_dtls", 1 ) ;
		$field_array_dtls="id, mst_id, from_prod_id, to_prod_id, batch_id, yarn_lot, brand_id, color_id, item_group, from_store, floor_id, room, rack, shelf, bin_box, to_store, to_floor_id, to_room, to_rack, to_shelf, to_bin_box, item_category, transfer_qnty, rate, transfer_value, uom, inserted_by, insert_date, from_order_id, to_order_id, trans_id, to_trans_id,remarks, to_batch_id, body_part_id, to_body_part, to_ord_book_id, to_ord_book_no";

		$field_array_dtls_ac="id, mst_id, dtls_id, is_acknowledge, from_prod_id, to_prod_id, batch_id, yarn_lot, brand_id, color_id, item_group, from_store, floor_id, room, rack, shelf, bin_box, to_store, to_floor_id, to_room, to_rack, to_shelf, to_bin_box, item_category, transfer_qnty, rate, transfer_value, uom, inserted_by, insert_date, from_order_id, to_order_id, to_batch_id, body_part_id, to_body_part,fabric_ref,rd_no,weight_type,cutable_width,width_editable,weight_editable";
		
		$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, color_id, quantity, inserted_by, insert_date";
		$field_array_batch_dtls="id, mst_id, po_id, prod_id, item_description, roll_no, roll_id, barcode_no, batch_qnty, dtls_id,width_dia_type, inserted_by, insert_date";
		
		if(str_replace("'","",$cbo_transfer_criteria)==1) //Company to Company Transfer
		{
			$data_prod=sql_select("select supplier_id, current_stock, avg_rate_per_unit, stock_value, detarmination_id, weight, dia_width, is_buyer_supplied from product_details_master where id=$from_product_id");
			
			$presentStock=$data_prod[0][csf('current_stock')]-str_replace("'","",$txt_transfer_qnty);
			$presentAvgRate=$data_prod[0][csf('avg_rate_per_unit')];
			$presentStockValue=$presentStock*$presentAvgRate;
			if($presentStock<=0)
			{
				$presentAvgRate=0;	
				$presentStockValue=0;	
			}
			$field_array_prodUpdate="avg_rate_per_unit*last_issued_qnty*current_stock*stock_value*updated_by*update_date";
			$data_array_prodUpdate="'".$presentAvgRate."'*".$txt_transfer_qnty."*".$presentStock."*".$presentStockValue."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			
			$supplier_id=$data_prod[0][csf('supplier_id')];// and supplier_id='$supplier_id'
			$product_id=0;	
			
			if ($data_prod[0][csf('dia_width')]=="") 
			{
				if($db_type == 0){
					$dia_cond = " and dia_width = '' ";
				}else{
					$dia_cond = " and dia_width is null ";
				}
			}
			else
			{
				$dia_cond = " and dia_width = '".$data_prod[0][csf('dia_width')]."'";
			}

			$row_prod=sql_select("select id, current_stock, avg_rate_per_unit from product_details_master where company_id=$company_id_to and supplier_id=".$data_prod[0][csf('supplier_id')] ." and item_category_id=$cbo_item_category and detarmination_id=".$data_prod[0][csf('detarmination_id')]." $dia_cond and weight = ".$data_prod[0][csf('weight')]." and color=$hide_color_id and unit_of_measure = ".$cbo_uom." and is_buyer_supplied=".$data_prod[0][csf('is_buyer_supplied')]." and status_active=1 and is_deleted=0");

			/*echo "10**select id, current_stock, avg_rate_per_unit from product_details_master where company_id=$company_id_to and supplier_id=".$data_prod[0][csf('supplier_id')]." and item_category_id=$cbo_item_category and detarmination_id=".$data_prod[0][csf('detarmination_id')]." $dia_cond and weight = ".$data_prod[0][csf('weight')]." and color=$hide_color_id and unit_of_measure = ".$cbo_uom." and is_buyer_supplied=".$data_prod[0][csf('is_buyer_supplied')]." and status_active=1 and is_deleted=0";
			die;*/

			//echo "10**".count($row_prod)."nnn";//$item_desc_w_space
			if(count($row_prod)>0)
			{
				$product_id=$row_prod[0][csf('id')];
				$stock_qnty=$row_prod[0][csf('current_stock')];
				$avg_rate_per_unit=$row_prod[0][csf('avg_rate_per_unit')];
	
				$curr_stock_qnty=$stock_qnty+str_replace("'", '',$txt_transfer_qnty);
				$stock_value=$curr_stock_qnty*$avg_rate_per_unit;
				if($curr_stock_qnty<=0)
				{
					$avg_rate_per_unit=0;
					$stock_value=0;
				}
				if($variable_auto_rcv==1)
				{
					$field_array_prod_update="avg_rate_per_unit*last_purchased_qnty*current_stock*stock_value*updated_by*update_date";
					$data_array_prod_update=$avg_rate_per_unit."*".$txt_transfer_qnty."*".$curr_stock_qnty."*".$stock_value."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
				}
			}
			else
			{
				//$product_id=return_next_id( "id", "product_details_master", 1 ) ;
				$product_id = return_next_id_by_sequence("PRODUCT_DETAILS_MASTER_PK_SEQ", "product_details_master", $con);
				$curr_stock_qnty=str_replace("'","",$txt_transfer_qnty);

				$avg_rate_per_unit=$data_prod[0][csf('avg_rate_per_unit')];
				$stock_value=$curr_stock_qnty*$avg_rate_per_unit;
				if($variable_auto_rcv==1)
				{
					$sql_prod_insert="insert into product_details_master(id, company_id, supplier_id, item_category_id, detarmination_id, product_name_details, lot, item_code, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, weight, is_buyer_supplied, inserted_by, insert_date) 
					select	
					'$product_id', $cbo_company_id_to, supplier_id, item_category_id, detarmination_id, product_name_details, lot, item_code, unit_of_measure, avg_rate_per_unit, $txt_transfer_qnty, $curr_stock_qnty, $stock_value, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, weight, is_buyer_supplied, inserted_by, insert_date from product_details_master where id=$from_product_id";
				}
				else
				{
					$sql_prod_insert="insert into product_details_master(id, company_id, supplier_id, item_category_id, detarmination_id, item_description, product_name_details, lot, item_code, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, weight, is_buyer_supplied, inserted_by, insert_date) 
					select	
					'$product_id', $cbo_company_id_to, supplier_id, item_category_id, detarmination_id, item_description, product_name_details, lot, item_code, unit_of_measure, avg_rate_per_unit, 0, 0, 0, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, weight, is_buyer_supplied, inserted_by, insert_date from product_details_master where id=$from_product_id";
				}
			}
			
			//----------------Check Last Receive Date for Transfer Out----------------
			$max_recv_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$from_product_id and transaction_type in (1,4,5) and store_id = $cbo_store_name  and status_active = 1", "max_date");      
			if($max_recv_date !="")
			{
				$max_recv_date = date("Y-m-d", strtotime($max_recv_date));
				$transfer_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_transfer_date)));

				if ($transfer_date < $max_recv_date) 
				{
					echo "20**Transfer Out Date Can not Be Less Than Last Receive Date Of This Item";
					disconnect($con);
					die;
				}
			}

			//----------------Check Last Issue Date for Transfer In----------------
			$max_issue_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$product_id and store_id = $cbo_store_name_to  and status_active = 1", "max_date");      
			if($max_issue_date !="")
			{
				$max_issue_date = date("Y-m-d", strtotime($max_issue_date));
				$transfer_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_transfer_date)));

				if ($transfer_date < $max_issue_date) 
				{
					echo "20**Transfer In Date Can not Be Less Than Last Transaction Date Of This Lot";
					disconnect($con);
					die;
				}
			}

			$batchData=sql_select("select a.id, a.batch_weight from pro_batch_create_mst a where a.batch_no=$txt_batch_no and a.color_id=$hide_color_id and a.booking_no= $hdn_to_booking_no and a.status_active=1 and a.is_deleted=0 and a.entry_form in (17, 258) and a.company_id=$company_id_to group by a.id, a.batch_weight");
			if(count($batchData)>0)
			{
				$batch_id_to=$batchData[0][csf('id')];
				$curr_batch_weight=$batchData[0][csf('batch_weight')]+str_replace("'", '',$txt_transfer_qnty);
				$field_array_batch_update="batch_weight*updated_by*update_date";
				$data_array_batch_update=$curr_batch_weight."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			}
			else
			{
				$booking_id = str_replace("'", "", $hdn_to_booking_id);
				$booking_no = str_replace("'", "", $hdn_to_booking_no);

				$batch_id_to = return_next_id_by_sequence("PRO_BATCH_CREATE_MST_PK_SEQ", "pro_batch_create_mst", $con);
				$field_array_batch="id,batch_no,entry_form,batch_date,company_id,booking_no_id,booking_no,booking_without_order,color_id,batch_weight,inserted_by,insert_date";

				$data_array_batch="(".$batch_id_to.",".$txt_batch_no.",258,".$txt_transfer_date.",".$company_id_to.",".$booking_id.",'".$booking_no."',0,".$hide_color_id.",".$txt_transfer_qnty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			}



			$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
			$data_array_trans="(".$id_trans.",".$transfer_update_id.",".$cbo_company_id.",".$from_product_id.",".$batch_id.",".$cbo_item_category.",6,".$txt_transfer_date.",".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_bin.",".$txt_from_order_id.",0,".$cbo_from_body_part.",".$cbo_uom.",".$txt_transfer_qnty.",".$txt_rate.",".$txt_transfer_value.",0,0,".$hdn_from_wo_rate.",'".$hidden_weight_type."',".$hidden_cutable_width.",".$hidden_fabric_ref.",".$hidden_rd_no.",".$hidden_width.",".$hidden_weight.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			//$recv_trans_id=$id_trans+1;
			$recv_trans_id=0;
			if($variable_auto_rcv==1)
			{
				$recv_trans_id = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
				$data_array_trans.=",(".$recv_trans_id.",".$transfer_update_id.",".$company_id_to.",".$product_id.",".$batch_id_to.",".$cbo_item_category.",5,".$txt_transfer_date.",".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$cbo_bin_to.",".$txt_to_order_id.",0,".$cbo_to_body_part.",".$cbo_uom.",".$txt_transfer_qnty.",".$txt_rate.",".$txt_transfer_value.",0,0,".$hdn_from_wo_rate.",'".$hidden_weight_type."',".$hidden_cutable_width.",".$hidden_fabric_ref.",".$hidden_rd_no.",".$hidden_width.",".$hidden_weight.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			}
			
			//.",".$txt_from_order_id.",".$txt_to_order_id
			$id_dtls = return_next_id_by_sequence("INV_ITEM_TRANSFER_DTLS_PK_SEQ", "inv_item_transfer_dtls", $con);
			$data_array_dtls="(".$id_dtls.",".$transfer_update_id.",".$from_product_id.",".$product_id.",".$batch_id.",0,0,".$hide_color_id.",0,".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_bin.",".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$cbo_bin_to.",".$cbo_item_category.",".$txt_transfer_qnty.",".$txt_rate.",".$txt_transfer_value.",".$cbo_uom.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$txt_from_order_id.",".$txt_to_order_id.",".$id_trans.",".$recv_trans_id.",".$txt_remarks.",".$batch_id_to.",".$cbo_from_body_part.",".$cbo_to_body_part.",".$hdn_to_booking_id.",".$hdn_to_booking_no.")";
			
			if($variable_auto_rcv==2)
			{
				$id_dtls_ac = return_next_id_by_sequence("INV_ITEM_TRANS_DTLS_AC_PK_SEQ", "inv_item_transfer_dtls_ac", $con);
				$data_array_dtls_ac="(".$id_dtls_ac.",".$transfer_update_id.",".$id_dtls.",0,".$from_product_id.",".$product_id.",".$batch_id.",0,0,".$hide_color_id.",0,".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_bin.",".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$cbo_bin_to.",".$cbo_item_category.",".$txt_transfer_qnty.",".$txt_rate.",".$txt_transfer_value.",".$cbo_uom.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$txt_from_order_id.",".$txt_to_order_id.",".$batch_id_to.",".$cbo_from_body_part.",".$cbo_to_body_part.",".$hidden_fabric_ref.",".$hidden_rd_no.",'".$hidden_weight_type."',".$hidden_cutable_width.",".$hidden_width.",".$hidden_weight.")";
			}
			$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
			$data_array_prop="(".$id_prop.",".$id_trans.",6,258,".$id_dtls.",".$txt_from_order_id.",".$from_product_id.",".$hide_color_id.",".$txt_transfer_qnty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			if($variable_auto_rcv==1)
			{
				$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
				$data_array_prop.=",(".$id_prop.",".$recv_trans_id.",5,258,".$id_dtls.",".$txt_to_order_id.",".$product_id.",".$hide_color_id.",".$txt_transfer_qnty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			}

			$id_dtls_batch = return_next_id_by_sequence("PRO_BATCH_CREATE_DTLS_PK_SEQ", "pro_batch_create_dtls", $con);
			if($data_array_batch_dtls!="") $data_array_batch_dtls.=",";
			$data_array_batch_dtls.="(".$id_dtls_batch.",".$batch_id_to.",".$txt_to_order_id.",".$product_id.",".$txt_item_desc.",0,0,0,".$txt_transfer_qnty.",".$id_dtls.",0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
		}
		else //Store To Store and order to order Transfer
		{
			//if(str_replace("'","",$cbo_transfer_criteria)==2) $txt_to_order_id = $txt_from_order_id;
			//----------------Check Last Receive Date for Transfer Out----------------
			$max_recv_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$from_product_id and transaction_type in (1,4,5) and store_id = $cbo_store_name  and status_active = 1", "max_date");      
			if($max_recv_date !="")
			{
				$max_recv_date = date("Y-m-d", strtotime($max_recv_date));
				$transfer_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_transfer_date)));

				if ($transfer_date < $max_recv_date) 
				{
					echo "20**Transfer Out Date Can not Be Less Than Last Receive Date Of This Item";
					disconnect($con);
					die;
				}
			}
			
			 //----------------Check Last Issue Date for Transfer In----------------
			$max_issue_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$from_product_id and store_id = $cbo_store_name_to  and status_active = 1", "max_date");      
			if($max_issue_date !="")
			{
				$max_issue_date = date("Y-m-d", strtotime($max_issue_date));
				$transfer_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_transfer_date)));

				if ($transfer_date < $max_issue_date) 
				{
					echo "20**Transfer In Date Can not Be Less Than Last Transaction Date Of This Item";
					disconnect($con);
					die;
				}
			}

			
			if(str_replace("'", "", $cbo_transfer_criteria) == 4 || str_replace("'", "", $cbo_transfer_criteria) == 3 || str_replace("'", "", $cbo_transfer_criteria) == 6 )
			{
				$batchData=sql_select("select a.id, a.batch_weight, a.booking_no from pro_batch_create_mst a where a.batch_no=$txt_batch_no and a.color_id=$hide_color_id and a.status_active=1 and a.is_deleted=0 and a.entry_form in (17,258) and a.company_id=$company_id_to and a.booking_no= $hdn_to_booking_no group by a.id, a.batch_weight, a.booking_no");


				if(count($batchData)>0)
				{
					$batch_id_to=$batchData[0][csf('id')];
					$curr_batch_weight=$batchData[0][csf('batch_weight')]+str_replace("'", '',$txt_transfer_qnty);
					$field_array_batch_update="batch_weight*updated_by*update_date";
					$data_array_batch_update=$curr_batch_weight."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
				}
				else
				{
					$booking_id = str_replace("'", "", $hdn_to_booking_id);
					$booking_no = str_replace("'", "", $hdn_to_booking_no);

					$batch_id_to = return_next_id_by_sequence("PRO_BATCH_CREATE_MST_PK_SEQ", "pro_batch_create_mst", $con);
					$field_array_batch="id,batch_no,entry_form,batch_date,company_id,booking_no_id,booking_no,booking_without_order,color_id,batch_weight,inserted_by,insert_date";

					if(str_replace("'", "", $cbo_transfer_criteria) == 6){$bookingWithOut_order=1;}else{$bookingWithOut_order=0;}

					$data_array_batch="(".$batch_id_to.",".$txt_batch_no.",258,".$txt_transfer_date.",".$company_id_to.",".$booking_id.",'".$booking_no."',".$bookingWithOut_order.",".$hide_color_id.",".$txt_transfer_qnty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				}
			}
			else
			{
				$batch_id_to = $batch_id; // for store to store transfer
			}

			if((str_replace("'", "", $cbo_transfer_criteria) == 3 || str_replace("'", "", $cbo_transfer_criteria) == 6) || (str_replace("'", "", $cbo_transfer_criteria) == 2 && str_replace("'", "", $style_and_po_wise_variable) == 1)) // Store to Store Style wise AND Style to Style 
			{
				$field_array_dtls="id, mst_id, from_prod_id, to_prod_id, batch_id, yarn_lot, brand_id, color_id, item_group, from_store, floor_id, room, rack, shelf, bin_box, to_store, to_floor_id, to_room, to_rack, to_shelf, to_bin_box, item_category, transfer_qnty, rate, transfer_value, uom, inserted_by, insert_date, from_order_id, to_order_id, trans_id, to_trans_id,remarks, to_batch_id, body_part_id, to_body_part, to_ord_book_id, to_ord_book_no,mst_id_style_wise";


				/*echo "10**SELECT d.id as po_id,d.job_no_mst, d.po_number, sum(b.fin_fab_qnty) as  fin_fab_qnty,b.booking_no,e.style_ref_no from wo_booking_dtls b, wo_po_break_down d ,wo_pre_cost_fabric_cost_dtls c, wo_po_details_master e
				where b.po_break_down_id=d.id and b.pre_cost_fabric_cost_dtls_id=c.id  and c.job_no=e.job_no and d.job_no_mst=e.job_no 
				and d.id in($txt_from_order_id) and b.fabric_color_id in($hide_color_id) and b.booking_no=$hdn_from_booking_no 
				and c.body_part_id=$cbo_from_body_part and  c.lib_yarn_count_deter_id=$txt_from_fabric_id
				and c.uom=$cbo_uom and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.status_active=1 and c.is_deleted=0
				group by d.id,d.job_no_mst, d.po_number ,b.booking_no,e.style_ref_no";die;*/

				if(str_replace("'", "", $cbo_transfer_criteria) == 3 && str_replace("'", "", $hdn_from_booking_id)==0) //STYLE TO STYLE FOR OPENING BALANCE WITHOUT FROM BOOKING
				{
					$jobNos="";
					$finish_from_req_qnty_sql=sql_select("SELECT a.id as po_id,a.job_no_mst, a.po_number ,b.style_ref_no,b.job_no_prefix_num 
					from  wo_po_break_down a , wo_po_details_master b,order_wise_pro_details c,inv_transaction d , product_details_master e 
					where a.job_id=b.id   
					and a.id in($txt_from_order_id)  and a.id=c.po_breakdown_id and c.trans_id=d.id and d.prod_id=e.id 
					and c.color_id in($hide_color_id)  and d.body_part_id=$cbo_from_body_part and d.cons_uom=$cbo_uom and e.detarmination_id=$txt_from_fabric_id 
					and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and c.entry_form=17 group by a.id,a.job_no_mst, a.po_number ,b.style_ref_no,b.job_no_prefix_num ");
					foreach ($finish_from_req_qnty_sql as $row) {
						$jobNos.="'".$row[csf('job_no_prefix_num')]."',";		
					}
					$jobNos=implode(",",array_unique(explode(",", $jobNos))); 
					$jobNos=chop($jobNos,",");

					$condition= new condition();     
					$condition->company_name("=$cbo_company_id");
					if($jobNos !=''){
						  $condition->job_no_prefix_num("in($jobNos)");
					}
					$condition->init();
				    //$costPerArr=$condition->getCostingPerArr();
				    $fabric= new fabric($condition);
				    $fabric_costing_job_arr=$fabric->getQtyArray_by_job_knitAndwoven_greyAndfinish();
				    //echo $fabric->getQuery();die;
				    //echo "<pre>";print_r($fabric_costing_job_arr);echo "</pre>";

				    $finish_from_req_qnty_total=0;
					foreach ($finish_from_req_qnty_sql as  $poidx => $row) {
						$requiredQnty_finish2=array_sum($fabric_costing_job_arr['woven']['finish'][$row[csf('job_no_mst')]]);
						$finish_from_req_qnty_arr[$row[csf('po_id')]]["fin_fab_qnty"]=$requiredQnty_finish2;
						$finish_from_req_qnty_total+=$requiredQnty_finish2;
					}
					$transfer_qnty=str_replace("'", "", $txt_transfer_qnty);
					foreach ($finish_from_req_qnty_arr as  $poid => $val ) {
						$perc=($val['fin_fab_qnty']/$finish_from_req_qnty_total)*100;
						$from_finish_qnty_perc[$poid]=(($perc*$transfer_qnty)/100);
					}
					//echo "10**".$requiredQnty_finish2;

					$finish_to_req_qnty_sql=sql_select("SELECT d.id as po_id,d.job_no_mst, d.po_number, sum(b.fin_fab_qnty) as  fin_fab_qnty,b.booking_no,e.style_ref_no from wo_booking_dtls b, wo_po_break_down d ,wo_pre_cost_fabric_cost_dtls c, wo_po_details_master e
					where b.po_break_down_id=d.id and b.pre_cost_fabric_cost_dtls_id=c.id  and c.job_no=e.job_no and d.job_no_mst=e.job_no 
					and d.id in($txt_to_order_id) and b.fabric_color_id in($hide_color_id) and b.booking_no=$hdn_to_booking_no 
					 and  c.lib_yarn_count_deter_id=$txt_from_fabric_id
					and c.uom=$cbo_uom and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by d.id,d.job_no_mst, d.po_number ,b.booking_no,e.style_ref_no"); 

					$finish_to_req_qnty_total=0;
					foreach ($finish_to_req_qnty_sql as  $poidxx => $row) {
						$finish_to_req_qnty_arr[$row[csf('po_id')]]["fin_fab_qnty"]=$row[csf('fin_fab_qnty')];
						$finish_to_req_qnty_total+=$row[csf('fin_fab_qnty')];
					}
					foreach ($finish_to_req_qnty_arr as  $to_poid => $to_val ) {
						$to_perc=($to_val['fin_fab_qnty']/$finish_to_req_qnty_total)*100;
						$to_finish_qnty_perc[$to_poid]=(($to_perc*$transfer_qnty)/100);
					}
				}
				else
				{
					$finish_from_req_qnty_sql=sql_select("SELECT d.id as po_id,d.job_no_mst, d.po_number, sum(b.fin_fab_qnty) as  fin_fab_qnty,b.booking_no,e.style_ref_no from wo_booking_dtls b, wo_po_break_down d ,wo_pre_cost_fabric_cost_dtls c, wo_po_details_master e
					where b.po_break_down_id=d.id and b.pre_cost_fabric_cost_dtls_id=c.id  and c.job_no=e.job_no and d.job_no_mst=e.job_no 
					and d.id in($txt_from_order_id) and b.fabric_color_id in($hide_color_id) and b.booking_no=$hdn_from_booking_no 
					and c.body_part_id=$cbo_from_body_part and  c.lib_yarn_count_deter_id=$txt_from_fabric_id
					and c.uom=$cbo_uom and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.status_active=1 and c.is_deleted=0
					group by d.id,d.job_no_mst, d.po_number ,b.booking_no,e.style_ref_no"); 
				
					//and b.rate='$txt_rate'  and b.dia_width='1.0169491525423728'  and b.gsm_weight='180' 
					//$finish_from_req_qnty_sql[1001]=100;
					//$finish_from_req_qnty_sql[1002]=400;
					$finish_from_req_qnty_total=0;
					foreach ($finish_from_req_qnty_sql as  $poidx => $row) {
						$finish_from_req_qnty_arr[$row[csf('po_id')]]["fin_fab_qnty"]=$row[csf('fin_fab_qnty')];
						$finish_from_req_qnty_total+=$row[csf('fin_fab_qnty')];

						//$finish_from_req_qnty_arr[$poidx]=$row;
						//$finish_from_req_qnty_total+=$row;
					}
					$transfer_qnty=str_replace("'", "", $txt_transfer_qnty);
					//$transfer_qnty=500;
					foreach ($finish_from_req_qnty_arr as  $poid => $val ) {
						$perc=($val['fin_fab_qnty']/$finish_from_req_qnty_total)*100;
						$from_finish_qnty_perc[$poid]=(($perc*$transfer_qnty)/100);

						//$perc=($val/$finish_from_req_qnty_total)*100;
						//$from_finish_qnty_perc[$poid]=(($perc*$transfer_qnty)/100);
					}

					$finish_to_req_qnty_sql=sql_select("SELECT d.id as po_id,d.job_no_mst, d.po_number, sum(b.fin_fab_qnty) as  fin_fab_qnty,b.booking_no,e.style_ref_no from wo_booking_dtls b, wo_po_break_down d ,wo_pre_cost_fabric_cost_dtls c, wo_po_details_master e
					where b.po_break_down_id=d.id and b.pre_cost_fabric_cost_dtls_id=c.id  and c.job_no=e.job_no and d.job_no_mst=e.job_no 
					and d.id in($txt_to_order_id) and b.fabric_color_id in($hide_color_id) and b.booking_no=$hdn_to_booking_no 
					 and  c.lib_yarn_count_deter_id=$txt_from_fabric_id
					and c.uom=$cbo_uom and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by d.id,d.job_no_mst, d.po_number ,b.booking_no,e.style_ref_no"); 
					/*echo "10**SELECT d.id as po_id,d.job_no_mst, d.po_number, sum(b.fin_fab_qnty) as  fin_fab_qnty,b.booking_no,e.style_ref_no from wo_booking_dtls b, wo_po_break_down d ,wo_pre_cost_fabric_cost_dtls c, wo_po_details_master e
					where b.po_break_down_id=d.id and b.pre_cost_fabric_cost_dtls_id=c.id  and c.job_no=e.job_no and d.job_no_mst=e.job_no 
					and d.id in($txt_to_order_id) and b.fabric_color_id in($hide_color_id) and b.booking_no=$hdn_to_booking_no 
					 and  c.lib_yarn_count_deter_id=$txt_from_fabric_id
					and c.uom=$cbo_uom and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by d.id,d.job_no_mst, d.po_number ,b.booking_no,e.style_ref_no";*/
					//and c.body_part_id=$cbo_to_body_part
					//and b.rate='$txt_rate'  and b.dia_width='1.0169491525423728'  and b.gsm_weight='180' 
					//$finish_to_req_qnty_sql[1003]=200;
					//$finish_to_req_qnty_sql[1004]=200;
					//$finish_to_req_qnty_sql[1005]=100;
					$finish_to_req_qnty_total=0;
					foreach ($finish_to_req_qnty_sql as  $poidxx => $row) {
						$finish_to_req_qnty_arr[$row[csf('po_id')]]["fin_fab_qnty"]=$row[csf('fin_fab_qnty')];
						$finish_to_req_qnty_total+=$row[csf('fin_fab_qnty')];

						//$finish_to_req_qnty_arr[$poidxx]=$row;
						//$finish_to_req_qnty_total+=$row;
					}
					foreach ($finish_to_req_qnty_arr as  $to_poid => $to_val ) {
						$to_perc=($to_val['fin_fab_qnty']/$finish_to_req_qnty_total)*100;
						$to_finish_qnty_perc[$to_poid]=(($to_perc*$transfer_qnty)/100);

						//$to_perc=($to_val/$finish_to_req_qnty_total)*100;
						//$to_finish_qnty_perc[$to_poid]=(($to_perc*$transfer_qnty)/100);
					}
				}
				

				$field_array_style_wise="id, mst_id, from_prod_id, to_prod_id, batch_id, yarn_lot, brand_id, color_id, item_group, from_store, floor_id, room, rack, shelf, bin_box, to_store, to_floor_id, to_room, to_rack, to_shelf, to_bin_box, item_category, transfer_qnty, rate, transfer_value, uom, inserted_by, insert_date,remarks, to_batch_id, body_part_id, to_body_part,from_style,to_style,from_ord_book_id,from_ord_book_no,to_ord_book_id,to_ord_book_no,from_fabric_id,booking_without_order";

				if(str_replace("'", "", $cbo_transfer_criteria) == 6){$bookingWithOurtOrd=1;}else{$bookingWithOurtOrd=0;}
				if(str_replace("'","",$cbo_company_id)==str_replace("'","",$cbo_company_id_to)) //if company different
				{
					$id_styleWise_dtls = return_next_id_by_sequence("INV_ITEM_TRANSFER_STYLE_WISE_PK_SEQ", "inv_item_transfer_style_wise", $con);
					$data_array_style_wise="(".$id_styleWise_dtls.",".$transfer_update_id.",".$from_product_id.",".$from_product_id.",".$batch_id.",0,0,".$hide_color_id.",0,".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_bin.",".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$cbo_bin_to.",".$cbo_item_category.",".$txt_transfer_qnty.",".$txt_rate.",".$txt_transfer_value.",".$cbo_uom.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$txt_remarks.",".$batch_id_to.",".$cbo_from_body_part.",".$cbo_to_body_part.",".$txt_from_style_no.",".$txt_to_style_no.",".$hdn_from_booking_id.",".$hdn_from_booking_no.",".$hdn_to_booking_id.",".$hdn_to_booking_no.",".$txt_from_fabric_id.",".$bookingWithOurtOrd.")";
				}
				
				if((str_replace("'", "", $cbo_transfer_criteria) == 3) || (str_replace("'", "", $cbo_transfer_criteria) == 2 && str_replace("'", "", $style_and_po_wise_variable) == 1))
				{
					$toPoArr=array();
					foreach ($from_finish_qnty_perc as $fromPo_key => $value) {
						foreach ($to_finish_qnty_perc as $toPo_key => $toValue) {
							if($from_finish_qnty_perc[$fromPo_key]>$to_finish_qnty_perc[$toPo_key] && $from_finish_qnty_perc[$fromPo_key]>0 && $to_finish_qnty_perc[$toPo_key]>0)
							{

								$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
								$data_array_trans.=",(".$id_trans.",".$transfer_update_id.",".$cbo_company_id.",".$from_product_id.",".$batch_id.",".$cbo_item_category.",6,".$txt_transfer_date.",".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_bin.",".$fromPo_key.",0,".$cbo_from_body_part.",".$cbo_uom.",".$to_finish_qnty_perc[$toPo_key].",".$txt_rate.",".$txt_transfer_value.",0,0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$hidden_fabric_ref.",".$hidden_rd_no.",'".$hidden_weight_type."',".$hidden_cutable_width.",".$hidden_width.",".$hidden_weight.",".$hdn_from_wo_rate.")";	

								$recv_trans_id=0;
								if($variable_auto_rcv==1)
								{
									$recv_trans_id = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
									$data_array_trans.=",(".$recv_trans_id.",".$transfer_update_id.",".$cbo_company_id.",".$from_product_id.",".$batch_id_to.",".$cbo_item_category.",5,".$txt_transfer_date.",".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$cbo_bin_to.",".$toPo_key.",0,".$cbo_to_body_part.",".$cbo_uom.",".$to_finish_qnty_perc[$toPo_key].",".$txt_rate.",".$txt_transfer_value.",0,0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$hidden_fabric_ref.",".$hidden_rd_no.",'".$hidden_weight_type."',".$hidden_cutable_width.",".$hidden_width.",".$hidden_weight.",".$hdn_from_wo_rate.")";
								}


								$id_dtls = return_next_id_by_sequence("INV_ITEM_TRANSFER_DTLS_PK_SEQ", "inv_item_transfer_dtls", $con);
								$data_array_dtls.=",(".$id_dtls.",".$transfer_update_id.",".$from_product_id.",".$from_product_id.",".$batch_id.",0,0,".$hide_color_id.",0,".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_bin.",".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$cbo_bin_to.",".$cbo_item_category.",".$to_finish_qnty_perc[$toPo_key].",".$txt_rate.",".$txt_transfer_value.",".$cbo_uom.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$fromPo_key.",".$toPo_key.",".$id_trans.",".$recv_trans_id.",".$txt_remarks.",".$batch_id_to.",".$cbo_from_body_part.",".$cbo_to_body_part.",".$hdn_to_booking_id.",".$hdn_to_booking_no.",".$id_styleWise_dtls.")";


								if($variable_auto_rcv==2)
								{
									$id_dtls_ac = return_next_id_by_sequence("INV_ITEM_TRANS_DTLS_AC_PK_SEQ", "inv_item_transfer_dtls_ac", $con);
									$data_array_dtls_ac.=",(".$id_dtls_ac.",".$transfer_update_id.",".$id_dtls.",0,".$from_product_id.",".$from_product_id.",".$batch_id.",0,0,".$hide_color_id.",0,".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_bin.",".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$cbo_bin_to.",".$cbo_item_category.",".$to_finish_qnty_perc[$toPo_key].",".$txt_rate.",".$txt_transfer_value.",".$cbo_uom.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$fromPo_key.",".$toPo_key.",".$batch_id_to.",".$cbo_from_body_part.",".$cbo_to_body_part.",".$hidden_fabric_ref.",".$hidden_rd_no.",'".$hidden_weight_type."',".$hidden_cutable_width.",".$hidden_width.",".$hidden_weight.")";
								}
								
								$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
								$data_array_prop.=",(".$id_prop.",".$id_trans.",6,258,".$id_dtls.",".$fromPo_key.",".$from_product_id.",".$hide_color_id.",".$to_finish_qnty_perc[$toPo_key].",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
								
								if($variable_auto_rcv==1)
								{
									$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
									$data_array_prop.=",(".$id_prop.",".$recv_trans_id.",5,258,".$id_dtls.",".$toPo_key.",".$from_product_id.",".$hide_color_id.",".$to_finish_qnty_perc[$toPo_key].",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
								}

								if(str_replace("'", "", $cbo_transfer_criteria) == 3)
								{
									$id_dtls_batch = return_next_id_by_sequence("PRO_BATCH_CREATE_DTLS_PK_SEQ", "pro_batch_create_dtls", $con);
									if($data_array_batch_dtls!="") $data_array_batch_dtls.=",";
									$data_array_batch_dtls.="(".$id_dtls_batch.",".$batch_id_to.",".$toPo_key.",".$from_product_id.",".$txt_item_desc.",0,0,0,".$to_finish_qnty_perc[$toPo_key].",".$id_dtls.",0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
								}
								//echo "from".$fromPo_key."qnt=".$to_finish_qnty_perc[$toPo_key]." to po=".$toPo_key ."qnt=".$to_finish_qnty_perc[$toPo_key]." =<br/>";
								$from_finish_qnty_perc[$fromPo_key]=$from_finish_qnty_perc[$fromPo_key]-$to_finish_qnty_perc[$toPo_key];
								$to_finish_qnty_perc[$toPo_key]=$to_finish_qnty_perc[$toPo_key]-$to_finish_qnty_perc[$toPo_key];
							}
							else if($from_finish_qnty_perc[$fromPo_key]==$to_finish_qnty_perc[$toPo_key] && $from_finish_qnty_perc[$fromPo_key]>0 )
							{
								$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
								$data_array_trans.=",(".$id_trans.",".$transfer_update_id.",".$cbo_company_id.",".$from_product_id.",".$batch_id.",".$cbo_item_category.",6,".$txt_transfer_date.",".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_bin.",".$fromPo_key.",0,".$cbo_from_body_part.",".$cbo_uom.",".$from_finish_qnty_perc[$fromPo_key].",".$txt_rate.",".$txt_transfer_value.",0,0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$hidden_fabric_ref.",".$hidden_rd_no.",'".$hidden_weight_type."',".$hidden_cutable_width.",".$hidden_width.",".$hidden_weight.",".$hdn_from_wo_rate.")";	

								$recv_trans_id=0;
								if($variable_auto_rcv==1)
								{
									$recv_trans_id = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
									$data_array_trans.=",(".$recv_trans_id.",".$transfer_update_id.",".$cbo_company_id.",".$from_product_id.",".$batch_id_to.",".$cbo_item_category.",5,".$txt_transfer_date.",".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$cbo_bin_to.",".$toPo_key.",0,".$cbo_to_body_part.",".$cbo_uom.",".$from_finish_qnty_perc[$fromPo_key].",".$txt_rate.",".$txt_transfer_value.",0,0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$hidden_fabric_ref.",".$hidden_rd_no.",'".$hidden_weight_type."',".$hidden_cutable_width.",".$hidden_width.",".$hidden_weight.",".$hdn_from_wo_rate.")";
								}


								$id_dtls = return_next_id_by_sequence("INV_ITEM_TRANSFER_DTLS_PK_SEQ", "inv_item_transfer_dtls", $con);
								$data_array_dtls.=",(".$id_dtls.",".$transfer_update_id.",".$from_product_id.",".$from_product_id.",".$batch_id.",0,0,".$hide_color_id.",0,".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_bin.",".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$cbo_bin_to.",".$cbo_item_category.",".$from_finish_qnty_perc[$fromPo_key].",".$txt_rate.",".$txt_transfer_value.",".$cbo_uom.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$fromPo_key.",".$toPo_key.",".$id_trans.",".$recv_trans_id.",".$txt_remarks.",".$batch_id_to.",".$cbo_from_body_part.",".$cbo_to_body_part.",".$hdn_to_booking_id.",".$hdn_to_booking_no.",".$id_styleWise_dtls.")";

								if($variable_auto_rcv==2)
								{
									$id_dtls_ac = return_next_id_by_sequence("INV_ITEM_TRANS_DTLS_AC_PK_SEQ", "inv_item_transfer_dtls_ac", $con);
									$data_array_dtls_ac.=",(".$id_dtls_ac.",".$transfer_update_id.",".$id_dtls.",0,".$from_product_id.",".$from_product_id.",".$batch_id.",0,0,".$hide_color_id.",0,".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_bin.",".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$cbo_bin_to.",".$cbo_item_category.",".$from_finish_qnty_perc[$fromPo_key].",".$txt_rate.",".$txt_transfer_value.",".$cbo_uom.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$fromPo_key.",".$toPo_key.",".$batch_id_to.",".$cbo_from_body_part.",".$cbo_to_body_part.",".$hidden_fabric_ref.",".$hidden_rd_no.",'".$hidden_weight_type."',".$hidden_cutable_width.",".$hidden_width.",".$hidden_weight.")";
								}
								
								$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
								$data_array_prop.=",(".$id_prop.",".$id_trans.",6,258,".$id_dtls.",".$fromPo_key.",".$from_product_id.",".$hide_color_id.",".$from_finish_qnty_perc[$fromPo_key].",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
								
								if($variable_auto_rcv==1)
								{
									$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
									$data_array_prop.=",(".$id_prop.",".$recv_trans_id.",5,258,".$id_dtls.",".$toPo_key.",".$from_product_id.",".$hide_color_id.",".$from_finish_qnty_perc[$fromPo_key].",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
								}
								if(str_replace("'", "", $cbo_transfer_criteria) == 3)
								{
									$id_dtls_batch = return_next_id_by_sequence("PRO_BATCH_CREATE_DTLS_PK_SEQ", "pro_batch_create_dtls", $con);
									if($data_array_batch_dtls!="") $data_array_batch_dtls.=",";
									$data_array_batch_dtls.="(".$id_dtls_batch.",".$batch_id_to.",".$toPo_key.",".$from_product_id.",".$txt_item_desc.",0,0,0,".$to_finish_qnty_perc[$toPo_key].",".$id_dtls.",0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
								}

								//echo "from".$fromPo_key."qnt=".$from_finish_qnty_perc[$fromPo_key]." to po=".$toPo_key ."qnt=".$from_finish_qnty_perc[$fromPo_key]."== <br/>";
								$from_finish_qnty_perc[$fromPo_key]=0;
								$to_finish_qnty_perc[$toPo_key]=0;
							}
							else if($from_finish_qnty_perc[$fromPo_key]<$to_finish_qnty_perc[$toPo_key] && $from_finish_qnty_perc[$fromPo_key]>0  && $to_finish_qnty_perc[$toPo_key]>0)
							{
								$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
								$data_array_trans.=",(".$id_trans.",".$transfer_update_id.",".$cbo_company_id.",".$from_product_id.",".$batch_id.",".$cbo_item_category.",6,".$txt_transfer_date.",".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_bin.",".$fromPo_key.",0,".$cbo_from_body_part.",".$cbo_uom.",".$from_finish_qnty_perc[$fromPo_key].",".$txt_rate.",".$txt_transfer_value.",0,0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$hidden_fabric_ref.",".$hidden_rd_no.",'".$hidden_weight_type."',".$hidden_cutable_width.",".$hidden_width.",".$hidden_weight.",".$hdn_from_wo_rate.")";

								$recv_trans_id=0;
								if($variable_auto_rcv==1)
								{
									$recv_trans_id = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
									$data_array_trans.=",(".$recv_trans_id.",".$transfer_update_id.",".$cbo_company_id.",".$from_product_id.",".$batch_id_to.",".$cbo_item_category.",5,".$txt_transfer_date.",".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$cbo_bin_to.",".$toPo_key.",0,".$cbo_to_body_part.",".$cbo_uom.",".$from_finish_qnty_perc[$fromPo_key].",".$txt_rate.",".$txt_transfer_value.",0,0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$hidden_fabric_ref.",".$hidden_rd_no.",'".$hidden_weight_type."',".$hidden_cutable_width.",".$hidden_width.",".$hidden_weight.",".$hdn_from_wo_rate.")";
								}

								$id_dtls = return_next_id_by_sequence("INV_ITEM_TRANSFER_DTLS_PK_SEQ", "inv_item_transfer_dtls", $con);
								$data_array_dtls.=",(".$id_dtls.",".$transfer_update_id.",".$from_product_id.",".$from_product_id.",".$batch_id.",0,0,".$hide_color_id.",0,".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_bin.",".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$cbo_bin_to.",".$cbo_item_category.",".$from_finish_qnty_perc[$fromPo_key].",".$txt_rate.",".$txt_transfer_value.",".$cbo_uom.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$fromPo_key.",".$toPo_key.",".$id_trans.",".$recv_trans_id.",".$txt_remarks.",".$batch_id_to.",".$cbo_from_body_part.",".$cbo_to_body_part.",".$hdn_to_booking_id.",".$hdn_to_booking_no.",".$id_styleWise_dtls.")";

								if($variable_auto_rcv==2)
								{
									$id_dtls_ac = return_next_id_by_sequence("INV_ITEM_TRANS_DTLS_AC_PK_SEQ", "inv_item_transfer_dtls_ac", $con);
									$data_array_dtls_ac.=",(".$id_dtls_ac.",".$transfer_update_id.",".$id_dtls.",0,".$from_product_id.",".$from_product_id.",".$batch_id.",0,0,".$hide_color_id.",0,".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_bin.",".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$cbo_bin_to.",".$cbo_item_category.",".$from_finish_qnty_perc[$fromPo_key].",".$txt_rate.",".$txt_transfer_value.",".$cbo_uom.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$fromPo_key.",".$toPo_key.",".$batch_id_to.",".$cbo_from_body_part.",".$cbo_to_body_part.",".$hidden_fabric_ref.",".$hidden_rd_no.",'".$hidden_weight_type."',".$hidden_cutable_width.",".$hidden_width.",".$hidden_weight.")";
								}
								
								$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
								$data_array_prop.=",(".$id_prop.",".$id_trans.",6,258,".$id_dtls.",".$fromPo_key.",".$from_product_id.",".$hide_color_id.",".$from_finish_qnty_perc[$fromPo_key].",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
								
								if($variable_auto_rcv==1)
								{
									$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
									$data_array_prop.=",(".$id_prop.",".$recv_trans_id.",5,258,".$id_dtls.",".$toPo_key.",".$from_product_id.",".$hide_color_id.",".$from_finish_qnty_perc[$fromPo_key].",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
								}

								if(str_replace("'", "", $cbo_transfer_criteria) == 3)
								{
									$id_dtls_batch = return_next_id_by_sequence("PRO_BATCH_CREATE_DTLS_PK_SEQ", "pro_batch_create_dtls", $con);
									if($data_array_batch_dtls!="") $data_array_batch_dtls.=",";
									$data_array_batch_dtls.="(".$id_dtls_batch.",".$batch_id_to.",".$toPo_key.",".$from_product_id.",".$txt_item_desc.",0,0,0,".$to_finish_qnty_perc[$toPo_key].",".$id_dtls.",0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
								}

								//echo "from".$fromPo_key."qnt=".$from_finish_qnty_perc[$fromPo_key]." to po=".$toPo_key ."qnt=".$from_finish_qnty_perc[$fromPo_key]."=== <br/>";

								$to_finish_qnty_perc[$toPo_key]=$to_finish_qnty_perc[$toPo_key]-$from_finish_qnty_perc[$fromPo_key];
								$from_finish_qnty_perc[$fromPo_key]=$from_finish_qnty_perc[$fromPo_key]-$from_finish_qnty_perc[$fromPo_key];
							}
							//print_r($toPoArr);	
						}
					}
				}
				else
				{
					if(str_replace("'","",$cbo_company_id)!=str_replace("'","",$cbo_company_id_to)) // if company different and order to sample
					{

						$data_prod=sql_select("select supplier_id, current_stock, avg_rate_per_unit, stock_value, detarmination_id, weight, dia_width, is_buyer_supplied from product_details_master where id=$from_product_id");
						
						$presentStock=$data_prod[0][csf('current_stock')]-str_replace("'","",$txt_transfer_qnty);
						$presentAvgRate=$data_prod[0][csf('avg_rate_per_unit')];
						$presentStockValue=$presentStock*$presentAvgRate;
						if($presentStock<=0)
						{
							$presentAvgRate=0;	
							$presentStockValue=0;	
						}
						$field_array_prodUpdate="avg_rate_per_unit*last_issued_qnty*current_stock*stock_value*updated_by*update_date";
						$data_array_prodUpdate="'".$presentAvgRate."'*".$txt_transfer_qnty."*".$presentStock."*".$presentStockValue."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
						
						$supplier_id=$data_prod[0][csf('supplier_id')];// and supplier_id='$supplier_id'
						$product_id=0;	
						
						if ($data_prod[0][csf('dia_width')]=="") 
						{
							if($db_type == 0){
								$dia_cond = " and dia_width = '' ";
							}else{
								$dia_cond = " and dia_width is null ";
							}
						}
						else
						{
							$dia_cond = " and dia_width = '".$data_prod[0][csf('dia_width')]."'";
						}

						$row_prod=sql_select("select id, current_stock, avg_rate_per_unit from product_details_master where company_id=$company_id_to and supplier_id=".$data_prod[0][csf('supplier_id')] ." and item_category_id=$cbo_item_category and detarmination_id=".$data_prod[0][csf('detarmination_id')]." $dia_cond and weight = ".$data_prod[0][csf('weight')]." and color=$hide_color_id and unit_of_measure = ".$cbo_uom." and is_buyer_supplied=".$data_prod[0][csf('is_buyer_supplied')]." and status_active=1 and is_deleted=0");

						/*echo "10**select id, current_stock, avg_rate_per_unit from product_details_master where company_id=$company_id_to and supplier_id=".$data_prod[0][csf('supplier_id')]." and item_category_id=$cbo_item_category and detarmination_id=".$data_prod[0][csf('detarmination_id')]." $dia_cond and weight = ".$data_prod[0][csf('weight')]." and color=$hide_color_id and unit_of_measure = ".$cbo_uom." and is_buyer_supplied=".$data_prod[0][csf('is_buyer_supplied')]." and status_active=1 and is_deleted=0";
						die;*/

						//echo "10**".count($row_prod)."nnn";//$item_desc_w_space
						if(count($row_prod)>0)
						{
							$product_id=$row_prod[0][csf('id')];
							$stock_qnty=$row_prod[0][csf('current_stock')];
							$avg_rate_per_unit=$row_prod[0][csf('avg_rate_per_unit')];
				
							$curr_stock_qnty=$stock_qnty+str_replace("'", '',$txt_transfer_qnty);
							$stock_value=$curr_stock_qnty*$avg_rate_per_unit;
							if($curr_stock_qnty<=0)
							{
								$avg_rate_per_unit=0;
								$stock_value=0;
							}
							if($variable_auto_rcv==1)
							{
								$field_array_prod_update="avg_rate_per_unit*last_purchased_qnty*current_stock*stock_value*updated_by*update_date";
								$data_array_prod_update=$avg_rate_per_unit."*".$txt_transfer_qnty."*".$curr_stock_qnty."*".$stock_value."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
							}
						}
						else
						{
							//$product_id=return_next_id( "id", "product_details_master", 1 ) ;
							$product_id = return_next_id_by_sequence("PRODUCT_DETAILS_MASTER_PK_SEQ", "product_details_master", $con);
							$curr_stock_qnty=str_replace("'","",$txt_transfer_qnty);

							$avg_rate_per_unit=$data_prod[0][csf('avg_rate_per_unit')];
							$stock_value=$curr_stock_qnty*$avg_rate_per_unit;
							if($variable_auto_rcv==1)
							{
								$sql_prod_insert="insert into product_details_master(id, company_id, supplier_id, item_category_id, detarmination_id, product_name_details, lot, item_code, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, weight, is_buyer_supplied, inserted_by, insert_date) 
								select	
								'$product_id', $cbo_company_id_to, supplier_id, item_category_id, detarmination_id, product_name_details, lot, item_code, unit_of_measure, avg_rate_per_unit, $txt_transfer_qnty, $curr_stock_qnty, $stock_value, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, weight, is_buyer_supplied, inserted_by, insert_date from product_details_master where id=$from_product_id";
							}
							else
							{
								$sql_prod_insert="insert into product_details_master(id, company_id, supplier_id, item_category_id, detarmination_id, item_description, product_name_details, lot, item_code, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, weight, is_buyer_supplied, inserted_by, insert_date) 
								select	
								'$product_id', $cbo_company_id_to, supplier_id, item_category_id, detarmination_id, item_description, product_name_details, lot, item_code, unit_of_measure, avg_rate_per_unit, 0, 0, 0, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, weight, is_buyer_supplied, inserted_by, insert_date from product_details_master where id=$from_product_id";
							}
						}
						
									$id_styleWise_dtls = return_next_id_by_sequence("INV_ITEM_TRANSFER_STYLE_WISE_PK_SEQ", "inv_item_transfer_style_wise", $con);
									$data_array_style_wise="(".$id_styleWise_dtls.",".$transfer_update_id.",".$from_product_id.",".$product_id.",".$batch_id.",0,0,".$hide_color_id.",0,".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_bin.",".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$cbo_bin_to.",".$cbo_item_category.",".$txt_transfer_qnty.",".$txt_rate.",".$txt_transfer_value.",".$cbo_uom.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$txt_remarks.",".$batch_id_to.",".$cbo_from_body_part.",".$cbo_to_body_part.",".$txt_from_style_no.",'',".$hdn_from_booking_id.",".$hdn_from_booking_no.",".$hdn_to_booking_id.",".$hdn_to_booking_no.",".$txt_from_fabric_id.",".$bookingWithOurtOrd.")";


								
						//$toPoArr=array();
						foreach ($from_finish_qnty_perc as $fromPo_key => $value) {
							//foreach ($to_finish_qnty_perc as $toPo_key => $toValue) {
							$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
							$data_array_trans.=",(".$id_trans.",".$transfer_update_id.",".$cbo_company_id.",".$from_product_id.",".$batch_id.",".$cbo_item_category.",6,".$txt_transfer_date.",".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_bin.",".$fromPo_key.",0,".$cbo_from_body_part.",".$cbo_uom.",".$from_finish_qnty_perc[$fromPo_key].",".$txt_rate.",".$txt_transfer_value.",0,0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$hidden_fabric_ref.",".$hidden_rd_no.",'".$hidden_weight_type."',".$hidden_cutable_width.",".$hidden_width.",".$hidden_weight.",".$hdn_from_wo_rate.")";	



							$recv_trans_id=0;
							if($variable_auto_rcv==1)
							{
								$recv_trans_id = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
								$data_array_trans.=",(".$recv_trans_id.",".$transfer_update_id.",".$cbo_company_id_to.",".$product_id.",".$batch_id_to.",".$cbo_item_category.",5,".$txt_transfer_date.",".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$cbo_bin_to.",0,0,".$cbo_to_body_part.",".$cbo_uom.",".$from_finish_qnty_perc[$fromPo_key].",".$txt_rate.",".$txt_transfer_value.",0,0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$hidden_fabric_ref.",".$hidden_rd_no.",'".$hidden_weight_type."',".$hidden_cutable_width.",".$hidden_width.",".$hidden_weight.",".$hdn_from_wo_rate.")";
							}


							$id_dtls = return_next_id_by_sequence("INV_ITEM_TRANSFER_DTLS_PK_SEQ", "inv_item_transfer_dtls", $con);
							$data_array_dtls.=",(".$id_dtls.",".$transfer_update_id.",".$from_product_id.",".$product_id.",".$batch_id.",0,0,".$hide_color_id.",0,".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_bin.",".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$cbo_bin_to.",".$cbo_item_category.",".$from_finish_qnty_perc[$fromPo_key].",".$txt_rate.",".$txt_transfer_value.",".$cbo_uom.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$fromPo_key.",0,".$id_trans.",".$recv_trans_id.",".$txt_remarks.",".$batch_id_to.",".$cbo_from_body_part.",".$cbo_to_body_part.",".$hdn_to_booking_id.",".$hdn_to_booking_no.",".$id_styleWise_dtls.")";

							if($variable_auto_rcv==2)
							{
								$id_dtls_ac = return_next_id_by_sequence("INV_ITEM_TRANS_DTLS_AC_PK_SEQ", "inv_item_transfer_dtls_ac", $con);
								$data_array_dtls_ac.=",(".$id_dtls_ac.",".$transfer_update_id.",".$id_dtls.",0,".$from_product_id.",".$product_id.",".$batch_id.",0,0,".$hide_color_id.",0,".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_bin.",".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$cbo_bin_to.",".$cbo_item_category.",".$from_finish_qnty_perc[$fromPo_key].",".$txt_rate.",".$txt_transfer_value.",".$cbo_uom.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$fromPo_key.",0,".$batch_id_to.",".$cbo_from_body_part.",".$cbo_to_body_part.",".$hidden_fabric_ref.",".$hidden_rd_no.",'".$hidden_weight_type."',".$hidden_cutable_width.",".$hidden_width.",".$hidden_weight.")";
							}
							
							$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
							$data_array_prop.=",(".$id_prop.",".$id_trans.",6,258,".$id_dtls.",".$fromPo_key.",".$from_product_id.",".$hide_color_id.",".$from_finish_qnty_perc[$fromPo_key].",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
							
							if($variable_auto_rcv==1)
							{
								//$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
								//$data_array_prop.=",(".$id_prop.",".$recv_trans_id.",5,258,".$id_dtls.",0,".$from_product_id.",".$hide_color_id.",".$from_finish_qnty_perc[$fromPo_key].",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
							}

							/*$id_dtls_batch = return_next_id_by_sequence("PRO_BATCH_CREATE_DTLS_PK_SEQ", "pro_batch_create_dtls", $con);
							if($data_array_batch_dtls!="") $data_array_batch_dtls.=",";
							$data_array_batch_dtls.="(".$id_dtls_batch.",".$batch_id_to.",0,".$from_product_id.",".$txt_item_desc.",0,0,0,".$txt_transfer_qnty.",".$id_dtls.",0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";*/

							//echo "from".$fromPo_key."qnt=".$from_finish_qnty_perc[$fromPo_key]." to po=".$toPo_key ."qnt=".$from_finish_qnty_perc[$fromPo_key]."== <br/>";
							
							//$from_finish_qnty_perc[$fromPo_key]=0;
							//$to_finish_qnty_perc[$toPo_key]=0;
								
								
							//}
						}

						$id_dtls_batch = return_next_id_by_sequence("PRO_BATCH_CREATE_DTLS_PK_SEQ", "pro_batch_create_dtls", $con);
						$data_array_batch_dtls="(".$id_dtls_batch.",".$batch_id_to.",0,".$product_id.",".$txt_item_desc.",0,0,0,".$txt_transfer_qnty.",".$id_dtls.",0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";


					}
					else
					{ 			//if from company and to company SAME and order to sample

						//$toPoArr=array();
						foreach ($from_finish_qnty_perc as $fromPo_key => $value) {
							//foreach ($to_finish_qnty_perc as $toPo_key => $toValue) {
							$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
							$data_array_trans.=",(".$id_trans.",".$transfer_update_id.",".$cbo_company_id.",".$from_product_id.",".$batch_id.",".$cbo_item_category.",6,".$txt_transfer_date.",".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_bin.",".$fromPo_key.",0,".$cbo_from_body_part.",".$cbo_uom.",".$from_finish_qnty_perc[$fromPo_key].",".$txt_rate.",".$txt_transfer_value.",0,0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$hidden_fabric_ref.",".$hidden_rd_no.",'".$hidden_weight_type."',".$hidden_cutable_width.",".$hidden_width.",".$hidden_weight.",".$hdn_from_wo_rate.")";	

							$recv_trans_id=0;
							if($variable_auto_rcv==1)
							{
								$recv_trans_id = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
								$data_array_trans.=",(".$recv_trans_id.",".$transfer_update_id.",".$cbo_company_id.",".$from_product_id.",".$batch_id_to.",".$cbo_item_category.",5,".$txt_transfer_date.",".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$cbo_bin_to.",0,0,".$cbo_to_body_part.",".$cbo_uom.",".$from_finish_qnty_perc[$fromPo_key].",".$txt_rate.",".$txt_transfer_value.",0,0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$hidden_fabric_ref.",".$hidden_rd_no.",'".$hidden_weight_type."',".$hidden_cutable_width.",".$hidden_width.",".$hidden_weight.",".$hdn_from_wo_rate.")";
							}


							$id_dtls = return_next_id_by_sequence("INV_ITEM_TRANSFER_DTLS_PK_SEQ", "inv_item_transfer_dtls", $con);
							$data_array_dtls.=",(".$id_dtls.",".$transfer_update_id.",".$from_product_id.",".$from_product_id.",".$batch_id.",0,0,".$hide_color_id.",0,".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_bin.",".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$cbo_bin_to.",".$cbo_item_category.",".$from_finish_qnty_perc[$fromPo_key].",".$txt_rate.",".$txt_transfer_value.",".$cbo_uom.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$fromPo_key.",0,".$id_trans.",".$recv_trans_id.",".$txt_remarks.",".$batch_id_to.",".$cbo_from_body_part.",".$cbo_to_body_part.",".$hdn_to_booking_id.",".$hdn_to_booking_no.",".$id_styleWise_dtls.")";

							if($variable_auto_rcv==2)
							{
								$id_dtls_ac = return_next_id_by_sequence("INV_ITEM_TRANS_DTLS_AC_PK_SEQ", "inv_item_transfer_dtls_ac", $con);
								$data_array_dtls_ac.=",(".$id_dtls_ac.",".$transfer_update_id.",".$id_dtls.",0,".$from_product_id.",".$from_product_id.",".$batch_id.",0,0,".$hide_color_id.",0,".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_bin.",".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$cbo_bin_to.",".$cbo_item_category.",".$from_finish_qnty_perc[$fromPo_key].",".$txt_rate.",".$txt_transfer_value.",".$cbo_uom.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$fromPo_key.",0,".$batch_id_to.",".$cbo_from_body_part.",".$cbo_to_body_part.",".$hidden_fabric_ref.",".$hidden_rd_no.",'".$hidden_weight_type."',".$hidden_cutable_width.",".$hidden_width.",".$hidden_weight.")";
							}
							
							$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
							$data_array_prop.=",(".$id_prop.",".$id_trans.",6,258,".$id_dtls.",".$fromPo_key.",".$from_product_id.",".$hide_color_id.",".$from_finish_qnty_perc[$fromPo_key].",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
							
							if($variable_auto_rcv==1)
							{
								//$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
								//$data_array_prop.=",(".$id_prop.",".$recv_trans_id.",5,258,".$id_dtls.",0,".$from_product_id.",".$hide_color_id.",".$from_finish_qnty_perc[$fromPo_key].",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
							}

							/*$id_dtls_batch = return_next_id_by_sequence("PRO_BATCH_CREATE_DTLS_PK_SEQ", "pro_batch_create_dtls", $con);
							if($data_array_batch_dtls!="") $data_array_batch_dtls.=",";
							$data_array_batch_dtls.="(".$id_dtls_batch.",".$batch_id_to.",0,".$from_product_id.",".$txt_item_desc.",0,0,0,".$txt_transfer_qnty.",".$id_dtls.",0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";*/

							//echo "from".$fromPo_key."qnt=".$from_finish_qnty_perc[$fromPo_key]." to po=".$toPo_key ."qnt=".$from_finish_qnty_perc[$fromPo_key]."== <br/>";
							
							//$from_finish_qnty_perc[$fromPo_key]=0;
							//$to_finish_qnty_perc[$toPo_key]=0;
								
								
							//}
						}

						$id_dtls_batch = return_next_id_by_sequence("PRO_BATCH_CREATE_DTLS_PK_SEQ", "pro_batch_create_dtls", $con);
						$data_array_batch_dtls="(".$id_dtls_batch.",".$batch_id_to.",0,".$from_product_id.",".$txt_item_desc.",0,0,0,".$txt_transfer_qnty.",".$id_dtls.",0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					}

				}
				$data_array_trans=ltrim($data_array_trans,",");
				$data_array_dtls=ltrim($data_array_dtls,",");
				$data_array_prop=ltrim($data_array_prop,",");
				$data_array_dtls_ac=ltrim($data_array_dtls_ac,",");
			}
			else  // store to store PO wise
			{

				$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
				$data_array_trans="(".$id_trans.",".$transfer_update_id.",".$cbo_company_id.",".$from_product_id.",".$batch_id.",".$cbo_item_category.",6,".$txt_transfer_date.",".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_bin.",".$txt_from_order_id.",0,".$cbo_from_body_part.",".$cbo_uom.",".$txt_transfer_qnty.",".$txt_rate.",".$txt_transfer_value.",0,0,".$hdn_from_wo_rate.",'".$hidden_weight_type."',".$hidden_cutable_width.",".$hidden_fabric_ref.",".$hidden_rd_no.",".$hidden_width.",".$hidden_weight.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";			
				
				$recv_trans_id=0;
				if($variable_auto_rcv==1)
				{
					$recv_trans_id = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
					$data_array_trans.=",(".$recv_trans_id.",".$transfer_update_id.",".$cbo_company_id.",".$from_product_id.",".$batch_id_to.",".$cbo_item_category.",5,".$txt_transfer_date.",".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$cbo_bin_to.",".$txt_from_order_id.",0,".$cbo_to_body_part.",".$cbo_uom.",".$txt_transfer_qnty.",".$txt_rate.",".$txt_transfer_value.",0,0,".$hdn_from_wo_rate.",'".$hidden_weight_type."',".$hidden_cutable_width.",".$hidden_fabric_ref.",".$hidden_rd_no.",".$hidden_width.",".$hidden_weight.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				}
				
				$id_dtls = return_next_id_by_sequence("INV_ITEM_TRANSFER_DTLS_PK_SEQ", "inv_item_transfer_dtls", $con);
				$data_array_dtls="(".$id_dtls.",".$transfer_update_id.",".$from_product_id.",".$from_product_id.",".$batch_id.",0,0,".$hide_color_id.",0,".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_bin.",".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$cbo_bin_to.",".$cbo_item_category.",".$txt_transfer_qnty.",".$txt_rate.",".$txt_transfer_value.",".$cbo_uom.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$txt_from_order_id.",".$txt_to_order_id.",".$id_trans.",".$recv_trans_id.",".$txt_remarks.",".$batch_id_to.",".$cbo_from_body_part.",".$cbo_to_body_part.",".$hdn_to_booking_id.",".$hdn_to_booking_no.")";
				
				if($variable_auto_rcv==2)
				{
					$id_dtls_ac = return_next_id_by_sequence("INV_ITEM_TRANS_DTLS_AC_PK_SEQ", "inv_item_transfer_dtls_ac", $con);
					$data_array_dtls_ac="(".$id_dtls_ac.",".$transfer_update_id.",".$id_dtls.",0,".$from_product_id.",".$from_product_id.",".$batch_id.",0,0,".$hide_color_id.",0,".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_bin.",".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$cbo_bin_to.",".$cbo_item_category.",".$txt_transfer_qnty.",".$txt_rate.",".$txt_transfer_value.",".$cbo_uom.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$txt_from_order_id.",".$txt_to_order_id.",".$batch_id_to.",".$cbo_from_body_part.",".$cbo_to_body_part.",".$hidden_fabric_ref.",".$hidden_rd_no.",'".$hidden_weight_type."',".$hidden_cutable_width.",".$hidden_width.",".$hidden_weight.")";
				}
				
				$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
				$data_array_prop="(".$id_prop.",".$id_trans.",6,258,".$id_dtls.",".$txt_from_order_id.",".$from_product_id.",".$hide_color_id.",".$txt_transfer_qnty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				
				if($variable_auto_rcv==1)
				{
					$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
					$data_array_prop.=",(".$id_prop.",".$recv_trans_id.",5,258,".$id_dtls.",".$txt_to_order_id.",".$from_product_id.",".$hide_color_id.",".$txt_transfer_qnty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				}

			}

			if(str_replace("'", "", $cbo_transfer_criteria) == 4 )
			{
				$id_dtls_batch = return_next_id_by_sequence("PRO_BATCH_CREATE_DTLS_PK_SEQ", "pro_batch_create_dtls", $con);
				if($data_array_batch_dtls!="") $data_array_batch_dtls.=",";
				$data_array_batch_dtls.="(".$id_dtls_batch.",".$batch_id_to.",".$txt_to_order_id.",".$from_product_id.",".$txt_item_desc.",0,0,0,".$txt_transfer_qnty.",".$id_dtls.",0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			}
		}
		
		if(str_replace("'","",$update_id)=="")
		{
			//echo "10**insert into inv_item_transfer_mst (".$field_array.") values ".$data_array;die;
			$rID=sql_insert("inv_item_transfer_mst",$field_array,$data_array,0);
			if($rID) $flag=1; else $flag=0;
		}
		else
		{
			$rID=sql_update("inv_item_transfer_mst",$field_array_update,$data_array_update,"id",$update_id,1);
			if($rID) $flag=1; else $flag=0; 
		}
		
		//echo "10**$flag";die;
		$rID=$prod=$rID2=$rID3=$rID4=$rID5=true;
		if(str_replace("'","",$cbo_transfer_criteria)==1)
		{
			//echo "10**".$data_array_prodUpdate;die;
			$prodUpdate=sql_update("product_details_master",$field_array_prodUpdate,$data_array_prodUpdate,"id",$from_product_id,1);
			if($flag==1) 
			{
				if($prodUpdate) $flag=1; else $flag=0; 
			} 
			
			if(count($row_prod)>0)
			{
				if($variable_auto_rcv==1)
				{
					$prod=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$product_id,0);
					if($flag==1) 
					{
						if($prod) $flag=1; else $flag=0; 
					} 
				}
			}
			else
			{
				//echo "10**".$sql_prod_insert;die;
				$prod=execute_query($sql_prod_insert,0);
				if($flag==1) 
				{
					if($prod) $flag=1; else $flag=0; 
				} 
			}
		}
		else if(str_replace("'","",$cbo_transfer_criteria)==6 && (str_replace("'","",$cbo_company_id)!=str_replace("'","",$cbo_company_id_to)) )
		{
			//echo "10**".$data_array_prodUpdate;die;
			$prodUpdate=sql_update("product_details_master",$field_array_prodUpdate,$data_array_prodUpdate,"id",$from_product_id,1);
			if($flag==1) 
			{
				if($prodUpdate) $flag=1; else $flag=0; 
			} 
			
			if(count($row_prod)>0)
			{
				if($variable_auto_rcv==1)
				{
					$prod=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$product_id,0);
					if($flag==1) 
					{
						if($prod) $flag=1; else $flag=0; 
					} 
				}
			}
			else
			{
				//echo "10**".$sql_prod_insert;die;
				$prod=execute_query($sql_prod_insert,0);
				if($flag==1) 
				{
					if($prod) $flag=1; else $flag=0; 
				} 
			}

		}
		
		//echo "10**insert into inv_transaction (".$field_array_trans.") values ".$data_array_trans;die;
		$rID2=sql_insert("inv_transaction",$field_array_trans,$data_array_trans,0);
		if($flag==1) 
		{
			if($rID2) $flag=1; else $flag=0; 
		} 
		
		//echo "10**insert into inv_item_transfer_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
		$rID3=sql_insert("inv_item_transfer_dtls",$field_array_dtls,$data_array_dtls,0);
		if($flag==1) 
		{
			if($rID3) $flag=1; else $flag=0; 
		}
		
		//echo "10**insert into order_wise_pro_details (".$field_array_proportionate.") values ".$data_array_prop;die;
		$rID4=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,1); 
		if($flag==1) 
		{
			if($rID4) $flag=1; else $flag=0; 
		}
		//echo "10**".$variable_auto_rcv; die;
		if($variable_auto_rcv==2)
		{
			//echo "10**insert into inv_item_transfer_dtls_ac (".$field_array_dtls_ac.") values ".$data_array_dtls_ac;die;
			$rID5=sql_insert("inv_item_transfer_dtls_ac",$field_array_dtls_ac,$data_array_dtls_ac,0);
			if($flag==1) 
			{
				if($rID5) $flag=1; else $flag=0; 
			}
		}

		$rID6=$rID7=true;
		if(str_replace("'","",$cbo_transfer_criteria)==1 || str_replace("'","",$cbo_transfer_criteria)==4 || str_replace("'","",$cbo_transfer_criteria)==3 || str_replace("'","",$cbo_transfer_criteria)==6)
		{
			//if($data_array_batch_dtls!="")
			//{
				//echo "10**insert into pro_batch_create_dtls (".$field_array_batch_dtls.") values ".$data_array_batch_dtls;oci_rollback($con);die;
				$rID6=sql_insert("pro_batch_create_dtls",$field_array_batch_dtls,$data_array_batch_dtls,0);
				if($flag==1)
				{
					if($rID6) $flag=1; else $flag=0;
				}
			//}

			if(count($batchData)>0)
			{
				//echo "10**";echo $data_array_batch_update."==".$batch_id_to;die;
				$rID7=sql_update("pro_batch_create_mst",$field_array_batch_update,$data_array_batch_update,"id",$batch_id_to,0);
			}
			else
			{
				//echo "10**insert into pro_batch_create_mst (".$field_array_batch.") values ".$data_array_batch;oci_rollback($con);die;
				$rID7=sql_insert("pro_batch_create_mst",$field_array_batch,$data_array_batch,0);
			}

			if($flag==1)
			{
				if($rID7) $flag=1; else $flag=0;
			}
			
			


		}

		if((str_replace("'","",$cbo_transfer_criteria)==3 || str_replace("'","",$cbo_transfer_criteria)==6) || (str_replace("'", "", $cbo_transfer_criteria) == 2 && str_replace("'", "", $style_and_po_wise_variable) == 1))
		{
			//echo "10**insert into inv_item_transfer_style_wise (".$field_array_style_wise.") values ".$data_array_style_wise;oci_rollback($con);die;
			$rID8=sql_insert("inv_item_transfer_style_wise",$field_array_style_wise,$data_array_style_wise,0);
			if($flag==1)
			{
				if($rID8) $flag=1; else $flag=0;
			}
		}
		//echo "10**$flag";die;
		
		
		//echo "10**".$flag."**".$variable_auto_rcv."**".$rID."**".$prod."**".$rID2."**".$rID3."**".$rID4."**".$rID5."**".$rID6."**".$rID7."**".$rID8; oci_rollback($con); die;

		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "0**".$transfer_update_id."**".$transfer_recv_num."**0";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "5**0**"."&nbsp;"."**0";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);  
				echo "0**".$transfer_update_id."**".$transfer_recv_num."**0";
			}
			else
			{
				oci_rollback($con);
				echo "5**0**"."&nbsp;"."**0";
			}
		}
		
		disconnect($con);
		die;
				
	}	
	else if ($operation==1) // Update Here----------------------------------------------------------
	{
		$con = connect();		
		if($db_type==0)	{ mysql_query("BEGIN"); }
		$variable_auto_rcv = return_field_value("auto_transfer_rcv", "variable_settings_inventory", " company_name=$cbo_company_id and item_category_id=3 and status_active=1 and variable_list= 27", "auto_transfer_rcv");

		if($variable_auto_rcv == "")
		{
			$variable_auto_rcv = 1;
		}
		if((str_replace("'", "", $cbo_transfer_criteria) == 3 || str_replace("'", "", $cbo_transfer_criteria) == 6) || (str_replace("'", "", $cbo_transfer_criteria) == 2 && str_replace("'", "", $style_and_po_wise_variable) == 1))
		{
			$field_array_update="challan_no*transfer_date*to_company*to_location_id*updated_by*update_date";
			$data_array_update=$txt_challan_no."*".$txt_transfer_date."*".$company_id_to."*".$cbo_location_to."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		}
		else
		{
			$field_array_update="challan_no*transfer_date*to_company*to_location_id*to_order_id*updated_by*update_date";
			$data_array_update=$txt_challan_no."*".$txt_transfer_date."*".$company_id_to."*".$cbo_location_to."*".$txt_to_order_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";	
		}
		
		$field_array_trans="prod_id*pi_wo_batch_no*transaction_date*store_id*floor_id*room*rack*self*bin_box*order_id*brand_id*body_part_id*cons_uom*cons_quantity*cons_rate*cons_amount*balance_qnty*balance_amount*transfer_wo_rate*updated_by*update_date";
		$updateTransID_array=array();
		$update_trans_issue_id=str_replace("'","",$update_trans_issue_id); 
		$update_trans_recv_id=str_replace("'","",$update_trans_recv_id); 
		$hdn_from_prod_id=str_replace("'","",$previous_from_prod_id); 
		$hdn_to_prod_id=str_replace("'","",$previous_to_prod_id); 
		
		$field_array_dtls="from_prod_id*to_prod_id*batch_id*color_id*from_store*floor_id*room*rack*shelf*bin_box*to_store*to_floor_id*to_room*to_rack*to_shelf*to_bin_box*transfer_qnty*rate*transfer_value*uom*updated_by*update_date*from_order_id*to_order_id*remarks*to_batch_id*body_part_id*to_body_part*to_ord_book_id*to_ord_book_no";

		$field_array_batch_dtls="id, mst_id, po_id, prod_id, item_description, roll_no, roll_id, barcode_no, batch_qnty, dtls_id,width_dia_type, inserted_by, insert_date";
		
		$field_array_proportionate_update="po_breakdown_id*prod_id*color_id*quantity*updated_by*update_date";
		$prod=true;

		//max transaction id VALIDATION its VERY IMPORTANT for Rate, amount calculation// issue id:3510
		$sql_max_trans_id_in = sql_select("select max(a.id) as max_trans_id from inv_transaction a, product_details_master b where a.prod_id=$hdn_from_prod_id and a.id <> $update_trans_recv_id and a.prod_id=b.id and b.item_category_id =3 and a.item_category=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id asc");

		
		$max_trans_id_in=$sql_max_trans_id_in[0]['MAX_TRANS_ID'];
		if (str_replace("'", "", trim($update_trans_issue_id))<$max_trans_id_in) {
			echo "20**Found next transaction against this product ID";
			disconnect($con);
			die;
		}

		/*$sql_max_trans_id_to = sql_select("select max(a.id) as max_trans_id from inv_transaction a, product_details_master b where a.prod_id=$hdn_to_prod_id and a.prod_id=b.id and b.item_category_id =3 and a.item_category=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id asc");
		$max_trans_id_to=$sql_max_trans_id_to[0]['MAX_TRANS_ID'];
		if (str_replace("'", "", trim($update_trans_recv_id))!=$max_trans_id_to) {
			echo "20**Found next transaction against this product ID";
			disconnect($con);
			die;
		}*/
		
		if($variable_auto_rcv != 1)
		{
			$pre_saved_store=sql_select("select a.id, b.from_store, b.to_store from inv_item_transfer_mst a, inv_item_transfer_dtls b where a.id = b.mst_id and a.entry_form = 258 and a.id = $update_id and b.id != $update_dtls_id and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0 group by a.id, b.from_store, b.to_store");
			if(!empty($pre_saved_store))
			{
				if( ($pre_saved_store[0][csf("from_store")]  !=str_replace("'", "", $cbo_store_name)) || ($pre_saved_store[0][csf("to_store")]  !=str_replace("'", "", $cbo_store_name_to)) )
				{
					echo "20**Duplicate From Store and To Store is not allowed in same MRR";
					//"select a.id, b.from_store, b.to_store from inv_item_transfer_mst a, inv_item_transfer_dtls b where a.id = b.mst_id and a.entry_form = 258 and a.id = $update_id and b.id != $update_dtls_id and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0 group by a.id, b.from_store, b.to_store"
					disconnect($con);
					die;
				}
			}
		}

		//echo "10**fail";die;

		if(str_replace("'","",$cbo_transfer_criteria)==1)
		{
			$updateProdID_array=array();
			$field_array_adjust="current_stock*avg_rate_per_unit*stock_value*updated_by*update_date";
			
			$stock_from=sql_select("select current_stock, avg_rate_per_unit from product_details_master where id=$previous_from_prod_id");
			$stock_to=sql_select("select current_stock, avg_rate_per_unit from product_details_master where id=$previous_to_prod_id");
			$data_prod=sql_select("select supplier_id, current_stock, avg_rate_per_unit, stock_value, detarmination_id, dia_width, weight, is_buyer_supplied from product_details_master where id=$from_product_id");

			if(str_replace("'","",$previous_from_prod_id) != str_replace("'","",$from_product_id))
			{
				$adjust_curr_stock_from=$stock_from[0][csf('current_stock')]+str_replace("'", '',$hidden_transfer_qnty);
				$cur_st_rate_from=$stock_from[0][csf('avg_rate_per_unit')];
				$cur_st_value_from=$adjust_curr_stock_from*$cur_st_rate_from;
				$previous_from_prod_id=str_replace("'","",$previous_from_prod_id);
				$updateProdID_array[]=$previous_from_prod_id;
				if($adjust_curr_stock_from<=0)
				{
					$cur_st_rate_from=0;
					$cur_st_value_from=0;
				}
				$data_array_adjust[$previous_from_prod_id]=explode("*",("".$adjust_curr_stock_from."*".$cur_st_rate_from."*".$cur_st_value_from."*".$user_id."*'".$pc_date_time."'"));

				$presentStock=$data_prod[0][csf('current_stock')]-str_replace("'","",$txt_transfer_qnty);
				$adjust_curr_stock_to=$stock_to[0][csf('current_stock')]-str_replace("'", '',$hidden_transfer_qnty);
				
				$supplier_id=$data_prod[0][csf('supplier_id')];
				if ($data_prod[0][csf('dia_width')]=="") 
				{
					if($db_type == 0){
						$dia_cond = " and dia_width = '' ";
					}else{
						$dia_cond = " and dia_width is null ";
					}
				}
				else
				{
					$dia_cond = " and dia_width = '".$data_prod[0][csf('dia_width')]."'";
				}

				$row_prod=sql_select("select id, current_stock, avg_rate_per_unit from product_details_master where company_id=$company_id_to and supplier_id='$supplier_id' and item_category_id=$cbo_item_category and detarmination_id='".$data_prod[0][csf('detarmination_id')]."' $dia_cond and weight = '".$data_prod[0][csf('weight')]."' and unit_of_measure = ".$cbo_uom." and color=$hide_color_id and is_buyer_supplied='".$data_prod[0][csf('is_buyer_supplied')]."' and status_active=1 and is_deleted=0");			

				if(count($row_prod)>0)
				{
					$product_id=$row_prod[0][csf('id')];
					$stock_qnty=$row_prod[0][csf('current_stock')];
					$avg_rate_per_unit=$row_prod[0][csf('avg_rate_per_unit')];
		
					$curr_stock_qnty=$stock_qnty+str_replace("'", '',$txt_transfer_qnty);
					$stock_value=$curr_stock_qnty*$avg_rate_per_unit;
					if($curr_stock_qnty<=0)
					{
						$stock_value=0;
						$avg_rate_per_unit=0;
					}
					if($variable_auto_rcv == 1 )
					{
						$field_array_prod_update="avg_rate_per_unit*last_purchased_qnty*current_stock*stock_value*updated_by*update_date";
						$data_array_prod_update="'".$avg_rate_per_unit."'*".$txt_transfer_qnty."*".$curr_stock_qnty."*".$stock_value."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
					}		
				}
				else
				{
					$product_id = return_next_id_by_sequence("PRODUCT_DETAILS_MASTER_PK_SEQ", "product_details_master", $con);
					$curr_stock_qnty=str_replace("'","",$txt_transfer_qnty);
					$avg_rate_per_unit=$data_prod[0][csf('avg_rate_per_unit')];
					$stock_value=$curr_stock_qnty*$avg_rate_per_unit;
					
					if($variable_auto_rcv == 1 )
					{
						$sql_prod_insert="insert into product_details_master(id, company_id, supplier_id, item_category_id, detarmination_id, product_name_details, lot, item_code, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, weight, inserted_by, insert_date) 
					select	
					'$product_id', $cbo_company_id_to, supplier_id, item_category_id, detarmination_id, product_name_details, lot, item_code, unit_of_measure, avg_rate_per_unit, $txt_transfer_qnty, $curr_stock_qnty, $stock_value, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, weight, inserted_by, insert_date from product_details_master where id=$from_product_id";
					}
					else
					{
						$sql_prod_insert="insert into product_details_master(id, company_id, supplier_id, item_category_id, detarmination_id, product_name_details, lot, item_code, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, weight, inserted_by, insert_date) 
					select	
					'$product_id', $cbo_company_id_to, supplier_id, item_category_id, detarmination_id, product_name_details, lot, item_code, unit_of_measure, avg_rate_per_unit, 0, 0, 0, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, weight, inserted_by, insert_date from product_details_master where id=$from_product_id";
					}
				}
			}
			else
			{
				$presentStock=$data_prod[0][csf('current_stock')]+str_replace("'", '',$hidden_transfer_qnty)-str_replace("'","",$txt_transfer_qnty);
				$adjust_curr_stock_to=$stock_to[0][csf('current_stock')]-str_replace("'", '',$hidden_transfer_qnty)+str_replace("'","",$txt_transfer_qnty);
				$product_id=str_replace("'","",$previous_to_prod_id);

			}

			$presentAvgRate=$data_prod[0][csf('avg_rate_per_unit')];
			$presentStockValue=$presentStock*$presentAvgRate;
			if($presentStock<=0)
			{
				$presentAvgRate=0;
				$presentStockValue=0;
			}
			$field_array_prodUpdate="avg_rate_per_unit*last_issued_qnty*current_stock*stock_value*updated_by*update_date";
			$data_array_prodUpdate="'".$presentAvgRate."'*".$txt_transfer_qnty."*".$presentStock."*".$presentStockValue."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			
			
			if($variable_auto_rcv == 1 )
			{
				$cur_st_rate_to=$stock_to[0][csf('avg_rate_per_unit')];
				$cur_st_value_to=$adjust_curr_stock_to*$cur_st_rate_to;
			
				$previous_to_prod_id=str_replace("'","",$previous_to_prod_id);
				$updateProdID_array[]=$previous_to_prod_id; 
				if($adjust_curr_stock_to<=0)
				{
					$cur_st_rate_to=0;
					$cur_st_value_to=0;
				}
				$data_array_adjust[$previous_to_prod_id]=explode("*",("".$adjust_curr_stock_to."*".$cur_st_rate_to."*".$cur_st_value_to."*".$user_id."*'".$pc_date_time."'"));
			}

			//echo "10**".bulk_update_sql_statement("product_details_master","id",$field_array_adjust,$data_array_adjust,$updateProdID_array);die;
			
			
			//echo "10**".$product_id;die;
			
			//----------------Check Last Receive Date for Transfer Out----------------
			$max_recv_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$from_product_id and store_id = $cbo_store_name and id <> $update_trans_recv_id  and status_active = 1", "max_date");      
			if($max_recv_date !="")
			{
				$max_recv_date = date("Y-m-d", strtotime($max_recv_date));
				$transfer_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_transfer_date)));
				if ($transfer_date < $max_recv_date) 
				{
					echo "20**Transfer Out Date Can not Be Less Than Last Receive Date Of This Lot";
					disconnect($con);
					die;
				}
			}
			
			//----------------Check Last Issue Date for Transfer In----------------
			$max_issue_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$product_id and store_id = $cbo_store_name_to and id not in ($update_trans_recv_id , $update_trans_issue_id )  and status_active = 1", "max_date");      
			if($max_issue_date !="")
			{
				$max_issue_date = date("Y-m-d", strtotime($max_issue_date));
				$transfer_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_transfer_date)));

				if ($transfer_date < $max_issue_date) 
				{
					echo "20**Transfer In Date Can not Be Less Than Last Transaction Date Of This Lot";
					disconnect($con);
					die;
				}
			}

			//=============================       Batch Data Create      ====================================
			$batchData=sql_select("select a.id, a.booking_no, a.batch_weight from pro_batch_create_mst a where a.batch_no=$txt_batch_no and a.color_id=$hide_color_id and a.booking_no= $hdn_to_booking_no and a.status_active=1 and a.is_deleted=0 and a.entry_form=258 and a.company_id=$company_id_to group by a.id, a.batch_weight, a.booking_no");

			$field_array_batch_update="batch_weight*updated_by*update_date";
			if(count($batchData)>0)
			{
				$batch_id_to=$batchData[0][csf('id')];
				if($batch_id_to==str_replace("'","",$previous_to_batch_id))
				{
					$curr_batch_weight=$batchData[0][csf('batch_weight')]+str_replace("'", '',$txt_transfer_qnty)-str_replace("'", '',$hidden_transfer_qnty);


					$update_batch_id[]=str_replace("'","",$previous_to_batch_id);
					$data_array_batch_update[str_replace("'","",$previous_to_batch_id)]=explode("*",("'".$curr_batch_weight."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				}
				else
				{
					//previous batch adjusted
					$batch_weight= return_field_value("batch_weight","pro_batch_create_mst","id=$previous_to_batch_id");
					$adjust_batch_weight=$batch_weight-str_replace("'", '',$hidden_transfer_qnty);
					$data_array_batch_update[str_replace("'","",$previous_to_batch_id)]=explode("*",("'".$adjust_batch_weight."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));

					//new batch adjusted
					$curr_batch_weight=$batchData[0][csf('batch_weight')]+str_replace("'", '',$txt_transfer_qnty);
					$update_batch_id[]=$batchData[0][csf('id')];
					$data_array_batch_update[$batchData[0][csf('id')]]=explode("*",("'".$curr_batch_weight."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				}
			}
			else
			{

				$booking_id = str_replace("'", "", $hdn_to_booking_id);
				$booking_no = str_replace("'", "", $hdn_to_booking_no);

				$batch_id_to = return_next_id_by_sequence("PRO_BATCH_CREATE_MST_PK_SEQ", "pro_batch_create_mst", $con);
				$field_array_batch="id,batch_no,entry_form,batch_date,company_id,booking_no_id,booking_no,booking_without_order,color_id,batch_weight,inserted_by,insert_date";

				$data_array_batch="(".$batch_id_to.",".$txt_batch_no.",258,".$txt_transfer_date.",".$company_id_to.",".$booking_id.",'".$booking_no."',0,".$hide_color_id.",".$txt_transfer_qnty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

				//previous batch adjusted
				$batch_weight= return_field_value("batch_weight","pro_batch_create_mst","id=$previous_to_batch_id");
				$adjust_batch_weight=$batch_weight-str_replace("'", '',$hidden_transfer_qnty);
				$update_batch_id[]=str_replace("'","",$previous_to_batch_id);
				$data_array_batch_update[str_replace("'","",$previous_to_batch_id)]=explode("*",("'".$adjust_batch_weight."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));

			}
			$batch_id = str_replace("'", "", $batch_id);
			$batch_id_to = str_replace("'", "", $batch_id_to);
			//=============================       Batch Data End      ====================================

			$updateTransID_array[]=$update_trans_issue_id; 
			$updateTransID_data[$update_trans_issue_id]=explode("*",("".$from_product_id."*'".$batch_id."'*".$txt_transfer_date."*".$cbo_store_name."*".$cbo_floor."*".$cbo_room."*".$txt_rack."*".$txt_shelf."*".$cbo_bin."*".$txt_from_order_id."*0*".$cbo_from_body_part."*".$cbo_uom."*".$txt_transfer_qnty."*".$txt_rate."*".$txt_transfer_value."*'0'*'0'*".$hdn_from_wo_rate."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			if($variable_auto_rcv == 1 )
			{
				$updateTransID_array[]=$update_trans_recv_id; 				
				$updateTransID_data[$update_trans_recv_id]=explode("*",("'".$product_id."'*'".$batch_id_to."'*".$txt_transfer_date."*".$cbo_store_name_to."*".$cbo_floor_to."*".$cbo_room_to."*".$txt_rack_to."*".$txt_shelf_to."*".$cbo_bin_to."*".$txt_to_order_id."*0*".$cbo_to_body_part."*".$cbo_uom."*".$txt_transfer_qnty."*".$txt_rate."*".$txt_transfer_value."*".$txt_transfer_qnty."*".$txt_transfer_value."*".$hdn_from_wo_rate."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			}
			
			$data_array_dtls=$from_product_id."*".$product_id."*'".$batch_id."'*".$hide_color_id."*".$cbo_store_name."*".$cbo_floor."*".$cbo_room."*".$txt_rack."*".$txt_shelf."*".$cbo_bin."*".$cbo_store_name_to."*".$cbo_floor_to."*".$cbo_room_to."*".$txt_rack_to."*".$txt_shelf_to."*".$cbo_bin_to."*".$txt_transfer_qnty."*".$txt_rate."*".$txt_transfer_value."*".$cbo_uom."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$txt_from_order_id."*".$txt_to_order_id."*".$txt_remarks."*'".$batch_id_to."'*".$cbo_from_body_part."*".$cbo_to_body_part."*".$hdn_to_booking_id."*".$hdn_to_booking_no;
			//echo "10**string";
			
			
			$data_array_prop_update[$update_trans_issue_id]=explode("*",("".$txt_from_order_id."*".$from_product_id."*".$hide_color_id."*".$txt_transfer_qnty."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			if($variable_auto_rcv == 1 )
			{
				$data_array_prop_update[$update_trans_recv_id]=explode("*",("".$txt_to_order_id."*'".$product_id."'*".$hide_color_id."*".$txt_transfer_qnty."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			}

			$id_dtls_batch = return_next_id_by_sequence("PRO_BATCH_CREATE_DTLS_PK_SEQ", "pro_batch_create_dtls", $con);
			if($data_array_batch_dtls!="") $data_array_batch_dtls.=",";
			$data_array_batch_dtls.="(".$id_dtls_batch.",'".$batch_id_to."',".$txt_to_order_id.",".$product_id.",".$txt_item_desc.",0,0,0,".$txt_transfer_qnty.",".$update_dtls_id.",0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		}
		else
		{
			//$txt_to_order_id=$txt_from_order_id;

			if(str_replace("'","",$cbo_transfer_criteria)==4 || str_replace("'", "", $cbo_transfer_criteria) == 3 || str_replace("'", "", $cbo_transfer_criteria) == 6)
			{
				 
				/*$batchData=sql_select("select a.id, a.booking_no, a.batch_weight from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.batch_no=$txt_batch_no and a.color_id=$hide_color_id and a.status_active=1 and a.is_deleted=0 and a.entry_form=258 and a.company_id=$company_id_to and a.booking_no = $hdn_to_booking_no group by a.id, a.batch_weight, a.booking_no");*/

				$batchData=sql_select("select a.id, a.booking_no, a.batch_weight from pro_batch_create_mst a where a.batch_no=$txt_batch_no and a.color_id=$hide_color_id and a.status_active=1 and a.is_deleted=0 and a.entry_form in (17,258) and a.company_id=$company_id_to and a.booking_no = $hdn_to_booking_no group by a.id, a.batch_weight, a.booking_no");

				$field_array_batch_update="batch_weight*updated_by*update_date";
				if(count($batchData)>0)
				{
					$batch_id_to=$batchData[0][csf('id')];
					if($batch_id_to==str_replace("'","",$previous_to_batch_id))
					{
						$curr_batch_weight=$batchData[0][csf('batch_weight')]+str_replace("'", '',$txt_transfer_qnty)-str_replace("'", '',$hidden_transfer_qnty);

						$update_batch_id[]=str_replace("'","",$previous_to_batch_id);
						$data_array_batch_update[str_replace("'","",$previous_to_batch_id)]=explode("*",("'".$curr_batch_weight."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
					}
					else
					{
						//previous batch adjusted
						$batch_weight= return_field_value("batch_weight","pro_batch_create_mst","id=$previous_to_batch_id");
						$adjust_batch_weight=$batch_weight-str_replace("'", '',$hidden_transfer_qnty);
						$data_array_batch_update[str_replace("'","",$previous_to_batch_id)]=explode("*",("'".$adjust_batch_weight."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));

						//new batch adjusted
						$curr_batch_weight=$batchData[0][csf('batch_weight')]+str_replace("'", '',$txt_transfer_qnty);
						$update_batch_id[]=$batchData[0][csf('id')];
						$data_array_batch_update[$batchData[0][csf('id')]]=explode("*",("'".$curr_batch_weight."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
					}
				}
				else
				{
					$booking_id = str_replace("'", "", $hdn_to_booking_id);
					$booking_no = str_replace("'", "", $hdn_to_booking_no);

					$batch_id_to = return_next_id_by_sequence("PRO_BATCH_CREATE_MST_PK_SEQ", "pro_batch_create_mst", $con);
					$field_array_batch="id,batch_no,entry_form,batch_date,company_id,booking_no_id,booking_no,booking_without_order,color_id,batch_weight,inserted_by,insert_date";

					$data_array_batch="(".$batch_id_to.",".$txt_batch_no.",258,".$txt_transfer_date.",".$company_id_to.",".$booking_id.",'".$booking_no."',0,".$hide_color_id.",".$txt_transfer_qnty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					//previous batch adjusted
					$batch_weight= return_field_value("batch_weight","pro_batch_create_mst","id=$previous_to_batch_id");
					$adjust_batch_weight=$batch_weight-str_replace("'", '',$hidden_transfer_qnty);
					$update_batch_id[]=str_replace("'","",$previous_to_batch_id);
					$data_array_batch_update[str_replace("'","",$previous_to_batch_id)]=explode("*",("'".$adjust_batch_weight."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				}
			}
			else
			{
				$batch_id_to = $batch_id; // for store to store transfer
			}
			if((str_replace("'", "", $cbo_transfer_criteria) == 3 || str_replace("'", "", $cbo_transfer_criteria) == 6) || (str_replace("'", "", $cbo_transfer_criteria) == 2 && str_replace("'", "", $style_and_po_wise_variable) == 1))
			{
				$field_array_dtls="id, mst_id, from_prod_id, to_prod_id, batch_id, yarn_lot, brand_id, color_id, item_group, from_store, floor_id, room, rack, shelf, bin_box, to_store, to_floor_id, to_room, to_rack, to_shelf, to_bin_box, item_category, transfer_qnty, rate, transfer_value, uom, inserted_by, insert_date, from_order_id, to_order_id, trans_id, to_trans_id,remarks, to_batch_id, body_part_id, to_body_part, to_ord_book_id, to_ord_book_no,mst_id_style_wise";

				if(str_replace("'", "", $cbo_transfer_criteria) == 3 && $hdn_from_booking_id==0) //STYLE TO STYLE FOR OPENING BALANCE WITHOUT FROM BOOKING
				{
					$jobNos="";
					$finish_from_req_qnty_sql=sql_select("SELECT a.id as po_id,a.job_no_mst, a.po_number ,b.style_ref_no,b.job_no_prefix_num 
					from  wo_po_break_down a , wo_po_details_master b,order_wise_pro_details c,inv_transaction d , product_details_master e 
					where a.job_id=b.id   
					and a.id in($txt_from_order_id)  and a.id=c.po_breakdown_id and c.trans_id=d.id and d.prod_id=e.id 
					and c.color_id in($hide_color_id)  and d.body_part_id=$cbo_from_body_part and d.cons_uom=$cbo_uom and e.detarmination_id=$txt_from_fabric_id 
					and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and c.entry_form=17 group by a.id,a.job_no_mst, a.po_number ,b.style_ref_no,b.job_no_prefix_num ");
					foreach ($finish_from_req_qnty_sql as $row) {
						$jobNos.="'".$row[csf('job_no_prefix_num')]."',";		
					}
					$jobNos=implode(",",array_unique(explode(",", $jobNos))); 
					$jobNos=chop($jobNos,",");

					$condition= new condition();     
					$condition->company_name("=$cbo_company_id");
					if($jobNos !=''){
						  $condition->job_no_prefix_num("in($jobNos)");
					}
					$condition->init();
				    //$costPerArr=$condition->getCostingPerArr();
				    $fabric= new fabric($condition);
				    $fabric_costing_job_arr=$fabric->getQtyArray_by_job_knitAndwoven_greyAndfinish();
				    //echo $fabric->getQuery();die;
				    //echo "<pre>";print_r($fabric_costing_job_arr);echo "</pre>";

				    $finish_from_req_qnty_total=0;
					foreach ($finish_from_req_qnty_sql as  $poidx => $row) {
						$requiredQnty_finish2=array_sum($fabric_costing_job_arr['woven']['finish'][$row[csf('job_no_mst')]]);
						$finish_from_req_qnty_arr[$row[csf('po_id')]]["fin_fab_qnty"]=$requiredQnty_finish2;
						$finish_from_req_qnty_total+=$requiredQnty_finish2;
					}
					$transfer_qnty=str_replace("'", "", $txt_transfer_qnty);
					foreach ($finish_from_req_qnty_arr as  $poid => $val ) {
						$perc=($val['fin_fab_qnty']/$finish_from_req_qnty_total)*100;
						$from_finish_qnty_perc[$poid]=(($perc*$transfer_qnty)/100);
					}
					//echo "10**".$requiredQnty_finish2;

					$finish_to_req_qnty_sql=sql_select("SELECT d.id as po_id,d.job_no_mst, d.po_number, sum(b.fin_fab_qnty) as  fin_fab_qnty,b.booking_no,e.style_ref_no from wo_booking_dtls b, wo_po_break_down d ,wo_pre_cost_fabric_cost_dtls c, wo_po_details_master e
					where b.po_break_down_id=d.id and b.pre_cost_fabric_cost_dtls_id=c.id  and c.job_no=e.job_no and d.job_no_mst=e.job_no 
					and d.id in($txt_to_order_id) and b.fabric_color_id in($hide_color_id) and b.booking_no=$hdn_to_booking_no 
					and c.body_part_id=$cbo_to_body_part and  c.lib_yarn_count_deter_id=$txt_from_fabric_id
					and c.uom=$cbo_uom and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.status_active=1 and c.is_deleted=0
					group by d.id,d.job_no_mst, d.po_number ,b.booking_no,e.style_ref_no"); 
					$finish_to_req_qnty_total=0;
					foreach ($finish_to_req_qnty_sql as  $poidxx => $row) {
						$finish_to_req_qnty_arr[$row[csf('po_id')]]["fin_fab_qnty"]=$row[csf('fin_fab_qnty')];
						$finish_to_req_qnty_total+=$row[csf('fin_fab_qnty')];
					}
					foreach ($finish_to_req_qnty_arr as  $to_poid => $to_val ) {
						$to_perc=($to_val['fin_fab_qnty']/$finish_to_req_qnty_total)*100;
						$to_finish_qnty_perc[$to_poid]=(($to_perc*$transfer_qnty)/100);
					}
				}
				else
				{

					$finish_from_req_qnty_sql=sql_select("SELECT d.id as po_id,d.job_no_mst, d.po_number, sum(b.fin_fab_qnty) as  fin_fab_qnty,b.booking_no,e.style_ref_no from wo_booking_dtls b, wo_po_break_down d ,wo_pre_cost_fabric_cost_dtls c, wo_po_details_master e
					where b.po_break_down_id=d.id and b.pre_cost_fabric_cost_dtls_id=c.id  and c.job_no=e.job_no and d.job_no_mst=e.job_no 
					and d.id in($txt_from_order_id) and b.fabric_color_id in($hide_color_id) and b.booking_no=$hdn_from_booking_no 
					and c.body_part_id=$cbo_from_body_part and  c.lib_yarn_count_deter_id=$txt_from_fabric_id
					and c.uom=$cbo_uom and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.status_active=1 and c.is_deleted=0
					group by d.id,d.job_no_mst, d.po_number ,b.booking_no,e.style_ref_no"); 
					//and b.rate='$txt_rate'  and b.dia_width='1.0169491525423728'  and b.gsm_weight='180' 
					//$finish_from_req_qnty_sql[1001]=100;
					//$finish_from_req_qnty_sql[1002]=400;
					$finish_from_req_qnty_total=0;
					foreach ($finish_from_req_qnty_sql as  $poidx => $row) {
						$finish_from_req_qnty_arr[$row[csf('po_id')]]["fin_fab_qnty"]=$row[csf('fin_fab_qnty')];
						$finish_from_req_qnty_total+=$row[csf('fin_fab_qnty')];

						//$finish_from_req_qnty_arr[$poidx]=$row;
						//$finish_from_req_qnty_total+=$row;
					}
					$transfer_qnty=str_replace("'", "", $txt_transfer_qnty);
					//$transfer_qnty=500;
					foreach ($finish_from_req_qnty_arr as  $poid => $val ) {
						$perc=($val['fin_fab_qnty']/$finish_from_req_qnty_total)*100;
						$from_finish_qnty_perc[$poid]=(($perc*$transfer_qnty)/100);

						//$perc=($val/$finish_from_req_qnty_total)*100;
						//$from_finish_qnty_perc[$poid]=(($perc*$transfer_qnty)/100);
					}
					
					$finish_to_req_qnty_sql=sql_select("SELECT d.id as po_id,d.job_no_mst, d.po_number, sum(b.fin_fab_qnty) as  fin_fab_qnty,b.booking_no,e.style_ref_no from wo_booking_dtls b, wo_po_break_down d ,wo_pre_cost_fabric_cost_dtls c, wo_po_details_master e
					where b.po_break_down_id=d.id and b.pre_cost_fabric_cost_dtls_id=c.id  and c.job_no=e.job_no and d.job_no_mst=e.job_no 
					and d.id in($txt_to_order_id) and b.fabric_color_id in($hide_color_id) and b.booking_no=$hdn_to_booking_no 
					and c.body_part_id=$cbo_to_body_part and  c.lib_yarn_count_deter_id=$txt_from_fabric_id
					and c.uom=$cbo_uom and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.status_active=1 and c.is_deleted=0
					group by d.id,d.job_no_mst, d.po_number ,b.booking_no,e.style_ref_no"); 
					//and b.rate='$txt_rate'  and b.dia_width='1.0169491525423728'  and b.gsm_weight='180' 
					//$finish_to_req_qnty_sql[1003]=200;
					//$finish_to_req_qnty_sql[1004]=200;
					//$finish_to_req_qnty_sql[1005]=100;
					$finish_to_req_qnty_total=0;
					foreach ($finish_to_req_qnty_sql as  $poidxx => $row) {
						$finish_to_req_qnty_arr[$row[csf('po_id')]]["fin_fab_qnty"]=$row[csf('fin_fab_qnty')];
						$finish_to_req_qnty_total+=$row[csf('fin_fab_qnty')];

						//$finish_to_req_qnty_arr[$poidxx]=$row;
						//$finish_to_req_qnty_total+=$row;
					}
					foreach ($finish_to_req_qnty_arr as  $to_poid => $to_val ) {
						$to_perc=($to_val['fin_fab_qnty']/$finish_to_req_qnty_total)*100;
						$to_finish_qnty_perc[$to_poid]=(($to_perc*$transfer_qnty)/100);

						//$to_perc=($to_val/$finish_to_req_qnty_total)*100;
						//$to_finish_qnty_perc[$to_poid]=(($to_perc*$transfer_qnty)/100);
					}
				}

				$field_array_trans_insert="id, mst_id, company_id, prod_id, pi_wo_batch_no, item_category, transaction_type, transaction_date, store_id, floor_id, room, rack, self, bin_box, order_id, brand_id, body_part_id, cons_uom, cons_quantity, cons_rate, cons_amount, balance_qnty, balance_amount, inserted_by, insert_date,fabric_ref,rd_no,weight_type,cutable_width,width_editable,weight_editable,transfer_wo_rate";
		
				$field_array_dtls_insert="id, mst_id, from_prod_id, to_prod_id, batch_id, yarn_lot, brand_id, color_id, item_group, from_store, floor_id, room, rack, shelf, bin_box, to_store, to_floor_id, to_room, to_rack, to_shelf, to_bin_box, item_category, transfer_qnty, rate, transfer_value, uom, inserted_by, insert_date, from_order_id, to_order_id, trans_id, to_trans_id,remarks, to_batch_id, body_part_id, to_body_part, to_ord_book_id, to_ord_book_no,mst_id_style_wise";

				$field_array_dtls_ac_insert="id, mst_id, dtls_id, is_acknowledge, from_prod_id, to_prod_id, batch_id, yarn_lot, brand_id, color_id, item_group, from_store, floor_id, room, rack, shelf, bin_box, to_store, to_floor_id, to_room, to_rack, to_shelf, to_bin_box, item_category, transfer_qnty, rate, transfer_value, uom, inserted_by, insert_date, from_order_id, to_order_id, to_batch_id, body_part_id, to_body_part,fabric_ref,rd_no,weight_type,cutable_width,width_editable,weight_editable";
				
				$field_array_proportionate_insert="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, color_id, quantity, inserted_by, insert_date";

				$field_array_batch_dtls_insert="id, mst_id, po_id, prod_id, item_description, roll_no, roll_id, barcode_no, batch_qnty, dtls_id,width_dia_type, inserted_by, insert_date";

				if((str_replace("'", "", $cbo_transfer_criteria) == 3) || (str_replace("'", "", $cbo_transfer_criteria) == 2 && str_replace("'", "", $style_and_po_wise_variable) == 1))
				{
					$toPoArr=array();
					foreach ($from_finish_qnty_perc as $fromPo_key => $value) {
						foreach ($to_finish_qnty_perc as $toPo_key => $toValue) {
							if($from_finish_qnty_perc[$fromPo_key]>$to_finish_qnty_perc[$toPo_key] && $from_finish_qnty_perc[$fromPo_key]>0 && $to_finish_qnty_perc[$toPo_key]>0)
							{

								$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
								$data_array_trans.=",(".$id_trans.",".$update_id.",".$cbo_company_id.",".$from_product_id.",".$batch_id.",".$cbo_item_category.",6,".$txt_transfer_date.",".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_bin.",".$fromPo_key.",0,".$cbo_from_body_part.",".$cbo_uom.",".$to_finish_qnty_perc[$toPo_key].",".$txt_rate.",".$txt_transfer_value.",0,0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$hidden_fabric_ref.",".$hidden_rd_no.",'".$hidden_weight_type."',".$hidden_cutable_width.",".$hidden_width.",".$hidden_weight.",".$hdn_from_wo_rate.")";	

								$recv_trans_id=0;
								if($variable_auto_rcv==1)
								{
									$recv_trans_id = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
									$data_array_trans.=",(".$recv_trans_id.",".$update_id.",".$cbo_company_id.",".$from_product_id.",".$batch_id_to.",".$cbo_item_category.",5,".$txt_transfer_date.",".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$cbo_bin_to.",".$toPo_key.",0,".$cbo_to_body_part.",".$cbo_uom.",".$to_finish_qnty_perc[$toPo_key].",".$txt_rate.",".$txt_transfer_value.",0,0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$hidden_fabric_ref.",".$hidden_rd_no.",'".$hidden_weight_type."',".$hidden_cutable_width.",".$hidden_width.",".$hidden_weight.",".$hdn_from_wo_rate.")";
								}


								$id_dtls = return_next_id_by_sequence("INV_ITEM_TRANSFER_DTLS_PK_SEQ", "inv_item_transfer_dtls", $con);
								$data_array_dtls.=",(".$id_dtls.",".$update_id.",".$from_product_id.",".$from_product_id.",".$batch_id.",0,0,".$hide_color_id.",0,".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_bin.",".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$cbo_bin_to.",".$cbo_item_category.",".$to_finish_qnty_perc[$toPo_key].",".$txt_rate.",".$txt_transfer_value.",".$cbo_uom.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$fromPo_key.",".$toPo_key.",".$id_trans.",".$recv_trans_id.",".$txt_remarks.",".$batch_id_to.",".$cbo_from_body_part.",".$cbo_to_body_part.",".$hdn_to_booking_id.",".$hdn_to_booking_no.",".$update_style_wise_mst_id.")";


								if($variable_auto_rcv==2)
								{
									$id_dtls_ac = return_next_id_by_sequence("INV_ITEM_TRANS_DTLS_AC_PK_SEQ", "inv_item_transfer_dtls_ac", $con);
									$data_array_dtls_ac.=",(".$id_dtls_ac.",".$update_id.",".$id_dtls.",0,".$from_product_id.",".$from_product_id.",".$batch_id.",0,0,".$hide_color_id.",0,".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_bin.",".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$cbo_bin_to.",".$cbo_item_category.",".$to_finish_qnty_perc[$toPo_key].",".$txt_rate.",".$txt_transfer_value.",".$cbo_uom.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$fromPo_key.",".$toPo_key.",".$batch_id_to.",".$cbo_from_body_part.",".$cbo_to_body_part.",".$hidden_fabric_ref.",".$hidden_rd_no.",'".$hidden_weight_type."',".$hidden_cutable_width.",".$hidden_width.",".$hidden_weight.")";
								}
								
								$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
								$data_array_prop.=",(".$id_prop.",".$id_trans.",6,258,".$id_dtls.",".$fromPo_key.",".$from_product_id.",".$hide_color_id.",".$to_finish_qnty_perc[$toPo_key].",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
								
								if($variable_auto_rcv==1)
								{
									$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
									$data_array_prop.=",(".$id_prop.",".$recv_trans_id.",5,258,".$id_dtls.",".$toPo_key.",".$from_product_id.",".$hide_color_id.",".$to_finish_qnty_perc[$toPo_key].",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
								}

								if(str_replace("'", "", $cbo_transfer_criteria) == 3)
								{
									$id_dtls_batch = return_next_id_by_sequence("PRO_BATCH_CREATE_DTLS_PK_SEQ", "pro_batch_create_dtls", $con);
									if($data_array_batch_dtls!="") $data_array_batch_dtls.=",";
									$data_array_batch_dtls.="(".$id_dtls_batch.",".$batch_id_to.",".$toPo_key.",".$from_product_id.",".$txt_item_desc.",0,0,0,".$to_finish_qnty_perc[$toPo_key].",".$id_dtls.",0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
								}


								//echo "from".$fromPo_key."qnt=".$to_finish_qnty_perc[$toPo_key]." to po=".$toPo_key ."qnt=".$to_finish_qnty_perc[$toPo_key]." =<br/>";
								$from_finish_qnty_perc[$fromPo_key]=$from_finish_qnty_perc[$fromPo_key]-$to_finish_qnty_perc[$toPo_key];
								$to_finish_qnty_perc[$toPo_key]=$to_finish_qnty_perc[$toPo_key]-$to_finish_qnty_perc[$toPo_key];

							}
							else if($from_finish_qnty_perc[$fromPo_key]==$to_finish_qnty_perc[$toPo_key] && $from_finish_qnty_perc[$fromPo_key]>0 )
							{
								$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
								$data_array_trans.=",(".$id_trans.",".$update_id.",".$cbo_company_id.",".$from_product_id.",".$batch_id.",".$cbo_item_category.",6,".$txt_transfer_date.",".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_bin.",".$fromPo_key.",0,".$cbo_from_body_part.",".$cbo_uom.",".$from_finish_qnty_perc[$fromPo_key].",".$txt_rate.",".$txt_transfer_value.",0,0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$hidden_fabric_ref.",".$hidden_rd_no.",'".$hidden_weight_type."',".$hidden_cutable_width.",".$hidden_width.",".$hidden_weight.",".$hdn_from_wo_rate.")";	

								$recv_trans_id=0;
								if($variable_auto_rcv==1)
								{
									$recv_trans_id = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
									$data_array_trans.=",(".$recv_trans_id.",".$update_id.",".$cbo_company_id.",".$from_product_id.",".$batch_id_to.",".$cbo_item_category.",5,".$txt_transfer_date.",".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$cbo_bin_to.",".$toPo_key.",0,".$cbo_to_body_part.",".$cbo_uom.",".$from_finish_qnty_perc[$fromPo_key].",".$txt_rate.",".$txt_transfer_value.",0,0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$hidden_fabric_ref.",".$hidden_rd_no.",'".$hidden_weight_type."',".$hidden_cutable_width.",".$hidden_width.",".$hidden_weight.",".$hdn_from_wo_rate.")";
								}


								$id_dtls = return_next_id_by_sequence("INV_ITEM_TRANSFER_DTLS_PK_SEQ", "inv_item_transfer_dtls", $con);
								$data_array_dtls.=",(".$id_dtls.",".$update_id.",".$from_product_id.",".$from_product_id.",".$batch_id.",0,0,".$hide_color_id.",0,".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_bin.",".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$cbo_bin_to.",".$cbo_item_category.",".$from_finish_qnty_perc[$fromPo_key].",".$txt_rate.",".$txt_transfer_value.",".$cbo_uom.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$fromPo_key.",".$toPo_key.",".$id_trans.",".$recv_trans_id.",".$txt_remarks.",".$batch_id_to.",".$cbo_from_body_part.",".$cbo_to_body_part.",".$hdn_to_booking_id.",".$hdn_to_booking_no.",".$update_style_wise_mst_id.")";

								if($variable_auto_rcv==2)
								{
									$id_dtls_ac = return_next_id_by_sequence("INV_ITEM_TRANS_DTLS_AC_PK_SEQ", "inv_item_transfer_dtls_ac", $con);
									$data_array_dtls_ac.=",(".$id_dtls_ac.",".$update_id.",".$id_dtls.",0,".$from_product_id.",".$from_product_id.",".$batch_id.",0,0,".$hide_color_id.",0,".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_bin.",".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$cbo_bin_to.",".$cbo_item_category.",".$from_finish_qnty_perc[$fromPo_key].",".$txt_rate.",".$txt_transfer_value.",".$cbo_uom.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$fromPo_key.",".$toPo_key.",".$batch_id_to.",".$cbo_from_body_part.",".$cbo_to_body_part.",".$hidden_fabric_ref.",".$hidden_rd_no.",'".$hidden_weight_type."',".$hidden_cutable_width.",".$hidden_width.",".$hidden_weight.")";
								}
								
								$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
								$data_array_prop.=",(".$id_prop.",".$id_trans.",6,258,".$id_dtls.",".$fromPo_key.",".$from_product_id.",".$hide_color_id.",".$from_finish_qnty_perc[$fromPo_key].",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
								
								if($variable_auto_rcv==1)
								{
									$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
									$data_array_prop.=",(".$id_prop.",".$recv_trans_id.",5,258,".$id_dtls.",".$toPo_key.",".$from_product_id.",".$hide_color_id.",".$from_finish_qnty_perc[$fromPo_key].",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
								}
								if(str_replace("'", "", $cbo_transfer_criteria) == 3)
								{
									$id_dtls_batch = return_next_id_by_sequence("PRO_BATCH_CREATE_DTLS_PK_SEQ", "pro_batch_create_dtls", $con);
									if($data_array_batch_dtls!="") $data_array_batch_dtls.=",";
									$data_array_batch_dtls.="(".$id_dtls_batch.",".$batch_id_to.",".$toPo_key.",".$from_product_id.",".$txt_item_desc.",0,0,0,".$to_finish_qnty_perc[$toPo_key].",".$id_dtls.",0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
								}

								//echo "from".$fromPo_key."qnt=".$from_finish_qnty_perc[$fromPo_key]." to po=".$toPo_key ."qnt=".$from_finish_qnty_perc[$fromPo_key]."== <br/>";
								$from_finish_qnty_perc[$fromPo_key]=0;
								$to_finish_qnty_perc[$toPo_key]=0;
							}
							else if($from_finish_qnty_perc[$fromPo_key]<$to_finish_qnty_perc[$toPo_key] && $from_finish_qnty_perc[$fromPo_key]>0  && $to_finish_qnty_perc[$toPo_key]>0)
							{
								$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
								$data_array_trans.=",(".$id_trans.",".$update_id.",".$cbo_company_id.",".$from_product_id.",".$batch_id.",".$cbo_item_category.",6,".$txt_transfer_date.",".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_bin.",".$fromPo_key.",0,".$cbo_from_body_part.",".$cbo_uom.",".$from_finish_qnty_perc[$fromPo_key].",".$txt_rate.",".$txt_transfer_value.",0,0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$hidden_fabric_ref.",".$hidden_rd_no.",'".$hidden_weight_type."',".$hidden_cutable_width.",".$hidden_width.",".$hidden_weight.",".$hdn_from_wo_rate.")";

								$recv_trans_id=0;
								if($variable_auto_rcv==1)
								{
									$recv_trans_id = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
									$data_array_trans.=",(".$recv_trans_id.",".$update_id.",".$cbo_company_id.",".$from_product_id.",".$batch_id_to.",".$cbo_item_category.",5,".$txt_transfer_date.",".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$cbo_bin_to.",".$toPo_key.",0,".$cbo_to_body_part.",".$cbo_uom.",".$from_finish_qnty_perc[$fromPo_key].",".$txt_rate.",".$txt_transfer_value.",0,0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$hidden_fabric_ref.",".$hidden_rd_no.",'".$hidden_weight_type."',".$hidden_cutable_width.",".$hidden_width.",".$hidden_weight.",".$hdn_from_wo_rate.")";
								}

								$id_dtls = return_next_id_by_sequence("INV_ITEM_TRANSFER_DTLS_PK_SEQ", "inv_item_transfer_dtls", $con);
								$data_array_dtls.=",(".$id_dtls.",".$update_id.",".$from_product_id.",".$from_product_id.",".$batch_id.",0,0,".$hide_color_id.",0,".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_bin.",".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$cbo_bin_to.",".$cbo_item_category.",".$from_finish_qnty_perc[$fromPo_key].",".$txt_rate.",".$txt_transfer_value.",".$cbo_uom.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$fromPo_key.",".$toPo_key.",".$id_trans.",".$recv_trans_id.",".$txt_remarks.",".$batch_id_to.",".$cbo_from_body_part.",".$cbo_to_body_part.",".$hdn_to_booking_id.",".$hdn_to_booking_no.",".$update_style_wise_mst_id.")";

								if($variable_auto_rcv==2)
								{
									$id_dtls_ac = return_next_id_by_sequence("INV_ITEM_TRANS_DTLS_AC_PK_SEQ", "inv_item_transfer_dtls_ac", $con);
									$data_array_dtls_ac.=",(".$id_dtls_ac.",".$update_id.",".$id_dtls.",0,".$from_product_id.",".$from_product_id.",".$batch_id.",0,0,".$hide_color_id.",0,".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_bin.",".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$cbo_bin_to.",".$cbo_item_category.",".$from_finish_qnty_perc[$fromPo_key].",".$txt_rate.",".$txt_transfer_value.",".$cbo_uom.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$fromPo_key.",".$toPo_key.",".$batch_id_to.",".$cbo_from_body_part.",".$cbo_to_body_part.",".$hidden_fabric_ref.",".$hidden_rd_no.",'".$hidden_weight_type."',".$hidden_cutable_width.",".$hidden_width.",".$hidden_weight.")";
								}
								
								$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
								$data_array_prop.=",(".$id_prop.",".$id_trans.",6,258,".$id_dtls.",".$fromPo_key.",".$from_product_id.",".$hide_color_id.",".$from_finish_qnty_perc[$fromPo_key].",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
								
								if($variable_auto_rcv==1)
								{
									$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
									$data_array_prop.=",(".$id_prop.",".$recv_trans_id.",5,258,".$id_dtls.",".$toPo_key.",".$from_product_id.",".$hide_color_id.",".$from_finish_qnty_perc[$fromPo_key].",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
								}

								if(str_replace("'", "", $cbo_transfer_criteria) == 3)
								{
									$id_dtls_batch = return_next_id_by_sequence("PRO_BATCH_CREATE_DTLS_PK_SEQ", "pro_batch_create_dtls", $con);
									if($data_array_batch_dtls!="") $data_array_batch_dtls.=",";
									$data_array_batch_dtls.="(".$id_dtls_batch.",".$batch_id_to.",".$toPo_key.",".$from_product_id.",".$txt_item_desc.",0,0,0,".$to_finish_qnty_perc[$toPo_key].",".$id_dtls.",0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
								}

								//echo "from".$fromPo_key."qnt=".$from_finish_qnty_perc[$fromPo_key]." to po=".$toPo_key ."qnt=".$from_finish_qnty_perc[$fromPo_key]."=== <br/>";

								$to_finish_qnty_perc[$toPo_key]=$to_finish_qnty_perc[$toPo_key]-$from_finish_qnty_perc[$fromPo_key];
								$from_finish_qnty_perc[$fromPo_key]=$from_finish_qnty_perc[$fromPo_key]-$from_finish_qnty_perc[$fromPo_key];


							}

							
							//print_r($toPoArr);
							
						}
					}
				}
				else
				{
					if(str_replace("'","",$cbo_company_id)!=str_replace("'","",$cbo_company_id_to)) // if company different and order to sample
					{

						$updateProdID_array=array();
						$field_array_adjust="current_stock*avg_rate_per_unit*stock_value*updated_by*update_date";
						
						$stock_from=sql_select("select current_stock, avg_rate_per_unit from product_details_master where id=$previous_from_prod_id");
						$stock_to=sql_select("select current_stock, avg_rate_per_unit from product_details_master where id=$previous_to_prod_id");
						$data_prod=sql_select("select supplier_id, current_stock, avg_rate_per_unit, stock_value, detarmination_id, dia_width, weight, is_buyer_supplied from product_details_master where id=$from_product_id");

						if(str_replace("'","",$previous_from_prod_id) != str_replace("'","",$from_product_id)) // if company different and order to sample
						{
							$adjust_curr_stock_from=$stock_from[0][csf('current_stock')]+str_replace("'", '',$hidden_transfer_qnty);
							$cur_st_rate_from=$stock_from[0][csf('avg_rate_per_unit')];
							$cur_st_value_from=$adjust_curr_stock_from*$cur_st_rate_from;
							$previous_from_prod_id=str_replace("'","",$previous_from_prod_id);
							$updateProdID_array[]=$previous_from_prod_id;
							if($adjust_curr_stock_from<=0)
							{
								$cur_st_rate_from=0;
								$cur_st_value_from=0;
							}
							$data_array_adjust[$previous_from_prod_id]=explode("*",("".$adjust_curr_stock_from."*".$cur_st_rate_from."*".$cur_st_value_from."*".$user_id."*'".$pc_date_time."'"));

							$presentStock=$data_prod[0][csf('current_stock')]-str_replace("'","",$txt_transfer_qnty);
							$adjust_curr_stock_to=$stock_to[0][csf('current_stock')]-str_replace("'", '',$hidden_transfer_qnty);
							
							$supplier_id=$data_prod[0][csf('supplier_id')];
							if ($data_prod[0][csf('dia_width')]=="") 
							{
								if($db_type == 0){
									$dia_cond = " and dia_width = '' ";
								}else{
									$dia_cond = " and dia_width is null ";
								}
							}
							else
							{
								$dia_cond = " and dia_width = '".$data_prod[0][csf('dia_width')]."'";
							}

							$row_prod=sql_select("select id, current_stock, avg_rate_per_unit from product_details_master where company_id=$company_id_to and supplier_id='$supplier_id' and item_category_id=$cbo_item_category and detarmination_id='".$data_prod[0][csf('detarmination_id')]."' $dia_cond and weight = '".$data_prod[0][csf('weight')]."' and unit_of_measure = ".$cbo_uom." and color=$hide_color_id and is_buyer_supplied='".$data_prod[0][csf('is_buyer_supplied')]."' and status_active=1 and is_deleted=0");			

							if(count($row_prod)>0)
							{
								$product_id=$row_prod[0][csf('id')];
								$stock_qnty=$row_prod[0][csf('current_stock')];
								$avg_rate_per_unit=$row_prod[0][csf('avg_rate_per_unit')];
					
								$curr_stock_qnty=$stock_qnty+str_replace("'", '',$txt_transfer_qnty);
								$stock_value=$curr_stock_qnty*$avg_rate_per_unit;
								if($curr_stock_qnty<=0)
								{
									$stock_value=0;
									$avg_rate_per_unit=0;
								}
								if($variable_auto_rcv == 1 )
								{
									$field_array_prod_update="avg_rate_per_unit*last_purchased_qnty*current_stock*stock_value*updated_by*update_date";
									$data_array_prod_update="'".$avg_rate_per_unit."'*".$txt_transfer_qnty."*".$curr_stock_qnty."*".$stock_value."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
								}		
							}
							else
							{
								$product_id = return_next_id_by_sequence("PRODUCT_DETAILS_MASTER_PK_SEQ", "product_details_master", $con);
								$curr_stock_qnty=str_replace("'","",$txt_transfer_qnty);
								$avg_rate_per_unit=$data_prod[0][csf('avg_rate_per_unit')];
								$stock_value=$curr_stock_qnty*$avg_rate_per_unit;
								
								if($variable_auto_rcv == 1 )
								{
									$sql_prod_insert="insert into product_details_master(id, company_id, supplier_id, item_category_id, detarmination_id, product_name_details, lot, item_code, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, weight, inserted_by, insert_date) 
								select	
								'$product_id', $cbo_company_id_to, supplier_id, item_category_id, detarmination_id, product_name_details, lot, item_code, unit_of_measure, avg_rate_per_unit, $txt_transfer_qnty, $curr_stock_qnty, $stock_value, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, weight, inserted_by, insert_date from product_details_master where id=$from_product_id";
								}
								else
								{
									$sql_prod_insert="insert into product_details_master(id, company_id, supplier_id, item_category_id, detarmination_id, product_name_details, lot, item_code, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, weight, inserted_by, insert_date) 
								select	
								'$product_id', $cbo_company_id_to, supplier_id, item_category_id, detarmination_id, product_name_details, lot, item_code, unit_of_measure, avg_rate_per_unit, 0, 0, 0, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, weight, inserted_by, insert_date from product_details_master where id=$from_product_id";
								}
							}
						}
						else
						{
							$presentStock=$data_prod[0][csf('current_stock')]+str_replace("'", '',$hidden_transfer_qnty)-str_replace("'","",$txt_transfer_qnty);
							$adjust_curr_stock_to=$stock_to[0][csf('current_stock')]-str_replace("'", '',$hidden_transfer_qnty)+str_replace("'","",$txt_transfer_qnty);
							$product_id=str_replace("'","",$previous_to_prod_id);

						}

						$presentAvgRate=$data_prod[0][csf('avg_rate_per_unit')];
						$presentStockValue=$presentStock*$presentAvgRate;
						if($presentStock<=0)
						{
							$presentAvgRate=0;
							$presentStockValue=0;
						}
						$field_array_prodUpdate="avg_rate_per_unit*last_issued_qnty*current_stock*stock_value*updated_by*update_date";
						$data_array_prodUpdate="'".$presentAvgRate."'*".$txt_transfer_qnty."*".$presentStock."*".$presentStockValue."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
						
						
						if($variable_auto_rcv == 1 )
						{
							$cur_st_rate_to=$stock_to[0][csf('avg_rate_per_unit')];
							$cur_st_value_to=$adjust_curr_stock_to*$cur_st_rate_to;
						
							$previous_to_prod_id=str_replace("'","",$previous_to_prod_id);
							$updateProdID_array[]=$previous_to_prod_id; 
							if($adjust_curr_stock_to<=0)
							{
								$cur_st_rate_to=0;
								$cur_st_value_to=0;
							}
							$data_array_adjust[$previous_to_prod_id]=explode("*",("".$adjust_curr_stock_to."*".$cur_st_rate_to."*".$cur_st_value_to."*".$user_id."*'".$pc_date_time."'"));
						}

						//echo "10**".bulk_update_sql_statement("product_details_master","id",$field_array_adjust,$data_array_adjust,$updateProdID_array);die;
						//echo "10**".$product_id;die;


						foreach ($from_finish_qnty_perc as $fromPo_key => $value) {
							$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
							$data_array_trans.=",(".$id_trans.",".$update_id.",".$cbo_company_id.",".$from_product_id.",".$batch_id.",".$cbo_item_category.",6,".$txt_transfer_date.",".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_bin.",".$fromPo_key.",0,".$cbo_from_body_part.",".$cbo_uom.",".$from_finish_qnty_perc[$fromPo_key].",".$txt_rate.",".$txt_transfer_value.",0,0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$hidden_fabric_ref.",".$hidden_rd_no.",'".$hidden_weight_type."',".$hidden_cutable_width.",".$hidden_width.",".$hidden_weight.",".$hdn_from_wo_rate.")";	

							$recv_trans_id=0;
							if($variable_auto_rcv==1)
							{
								$recv_trans_id = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
								$data_array_trans.=",(".$recv_trans_id.",".$update_id.",".$cbo_company_id_to.",".$product_id.",".$batch_id_to.",".$cbo_item_category.",5,".$txt_transfer_date.",".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$cbo_bin_to.",0,0,".$cbo_to_body_part.",".$cbo_uom.",".$from_finish_qnty_perc[$fromPo_key].",".$txt_rate.",".$txt_transfer_value.",0,0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$hidden_fabric_ref.",".$hidden_rd_no.",'".$hidden_weight_type."',".$hidden_cutable_width.",".$hidden_width.",".$hidden_weight.",".$hdn_from_wo_rate.")";
							}


							$id_dtls = return_next_id_by_sequence("INV_ITEM_TRANSFER_DTLS_PK_SEQ", "inv_item_transfer_dtls", $con);
							$data_array_dtls.=",(".$id_dtls.",".$update_id.",".$from_product_id.",".$product_id.",".$batch_id.",0,0,".$hide_color_id.",0,".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_bin.",".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$cbo_bin_to.",".$cbo_item_category.",".$from_finish_qnty_perc[$fromPo_key].",".$txt_rate.",".$txt_transfer_value.",".$cbo_uom.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$fromPo_key.",0,".$id_trans.",".$recv_trans_id.",".$txt_remarks.",".$batch_id_to.",".$cbo_from_body_part.",".$cbo_to_body_part.",".$hdn_to_booking_id.",".$hdn_to_booking_no.",".$update_style_wise_mst_id.")";

							if($variable_auto_rcv==2)
							{
								$id_dtls_ac = return_next_id_by_sequence("INV_ITEM_TRANS_DTLS_AC_PK_SEQ", "inv_item_transfer_dtls_ac", $con);
								$data_array_dtls_ac.=",(".$id_dtls_ac.",".$update_id.",".$id_dtls.",0,".$from_product_id.",".$product_id.",".$batch_id.",0,0,".$hide_color_id.",0,".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_bin.",".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$cbo_bin_to.",".$cbo_item_category.",".$from_finish_qnty_perc[$fromPo_key].",".$txt_rate.",".$txt_transfer_value.",".$cbo_uom.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$fromPo_key.",0,".$batch_id_to.",".$cbo_from_body_part.",".$cbo_to_body_part.",".$hidden_fabric_ref.",".$hidden_rd_no.",'".$hidden_weight_type."',".$hidden_cutable_width.",".$hidden_width.",".$hidden_weight.")"; 
							}
							
							$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
							$data_array_prop.=",(".$id_prop.",".$id_trans.",6,258,".$id_dtls.",".$fromPo_key.",".$from_product_id.",".$hide_color_id.",".$from_finish_qnty_perc[$fromPo_key].",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
							
							if($variable_auto_rcv==1)
							{
								//$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
								//$data_array_prop.=",(".$id_prop.",".$recv_trans_id.",5,258,".$id_dtls.",".$toPo_key.",".$from_product_id.",".$hide_color_id.",".$from_finish_qnty_perc[$fromPo_key].",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
							}		

						}
						$id_dtls_batch = return_next_id_by_sequence("PRO_BATCH_CREATE_DTLS_PK_SEQ", "pro_batch_create_dtls", $con);
						$data_array_batch_dtls="(".$id_dtls_batch.",".$batch_id_to.",0,".$product_id.",".$txt_item_desc.",0,0,0,".$txt_transfer_qnty.",".$id_dtls.",0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";	


					}
					else     //if from company and to company SAME and order to sample
					{
						foreach ($from_finish_qnty_perc as $fromPo_key => $value) {
							$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
							$data_array_trans.=",(".$id_trans.",".$update_id.",".$cbo_company_id.",".$from_product_id.",".$batch_id.",".$cbo_item_category.",6,".$txt_transfer_date.",".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_bin.",".$fromPo_key.",0,".$cbo_from_body_part.",".$cbo_uom.",".$from_finish_qnty_perc[$fromPo_key].",".$txt_rate.",".$txt_transfer_value.",0,0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$hidden_fabric_ref.",".$hidden_rd_no.",'".$hidden_weight_type."',".$hidden_cutable_width.",".$hidden_width.",".$hidden_weight.",".$hdn_from_wo_rate.")";	

							$recv_trans_id=0;
							if($variable_auto_rcv==1)
							{
								$recv_trans_id = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
								$data_array_trans.=",(".$recv_trans_id.",".$update_id.",".$cbo_company_id.",".$from_product_id.",".$batch_id_to.",".$cbo_item_category.",5,".$txt_transfer_date.",".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$cbo_bin_to.",0,0,".$cbo_to_body_part.",".$cbo_uom.",".$from_finish_qnty_perc[$fromPo_key].",".$txt_rate.",".$txt_transfer_value.",0,0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$hidden_fabric_ref.",".$hidden_rd_no.",'".$hidden_weight_type."',".$hidden_cutable_width.",".$hidden_width.",".$hidden_weight.",".$hdn_from_wo_rate.")";
							}


							$id_dtls = return_next_id_by_sequence("INV_ITEM_TRANSFER_DTLS_PK_SEQ", "inv_item_transfer_dtls", $con);
							$data_array_dtls.=",(".$id_dtls.",".$update_id.",".$from_product_id.",".$from_product_id.",".$batch_id.",0,0,".$hide_color_id.",0,".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_bin.",".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$cbo_bin_to.",".$cbo_item_category.",".$from_finish_qnty_perc[$fromPo_key].",".$txt_rate.",".$txt_transfer_value.",".$cbo_uom.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$fromPo_key.",0,".$id_trans.",".$recv_trans_id.",".$txt_remarks.",".$batch_id_to.",".$cbo_from_body_part.",".$cbo_to_body_part.",".$hdn_to_booking_id.",".$hdn_to_booking_no.",".$update_style_wise_mst_id.")";

							if($variable_auto_rcv==2)
							{
								$id_dtls_ac = return_next_id_by_sequence("INV_ITEM_TRANS_DTLS_AC_PK_SEQ", "inv_item_transfer_dtls_ac", $con);
								$data_array_dtls_ac.=",(".$id_dtls_ac.",".$update_id.",".$id_dtls.",0,".$from_product_id.",".$from_product_id.",".$batch_id.",0,0,".$hide_color_id.",0,".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_bin.",".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$cbo_bin_to.",".$cbo_item_category.",".$from_finish_qnty_perc[$fromPo_key].",".$txt_rate.",".$txt_transfer_value.",".$cbo_uom.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$fromPo_key.",0,".$batch_id_to.",".$cbo_from_body_part.",".$cbo_to_body_part.",".$hidden_fabric_ref.",".$hidden_rd_no.",'".$hidden_weight_type."',".$hidden_cutable_width.",".$hidden_width.",".$hidden_weight.")"; 
							}
							
							$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
							$data_array_prop.=",(".$id_prop.",".$id_trans.",6,258,".$id_dtls.",".$fromPo_key.",".$from_product_id.",".$hide_color_id.",".$from_finish_qnty_perc[$fromPo_key].",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
							
							if($variable_auto_rcv==1)
							{
								//$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
								//$data_array_prop.=",(".$id_prop.",".$recv_trans_id.",5,258,".$id_dtls.",".$toPo_key.",".$from_product_id.",".$hide_color_id.",".$from_finish_qnty_perc[$fromPo_key].",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
							}		

						}
						$id_dtls_batch = return_next_id_by_sequence("PRO_BATCH_CREATE_DTLS_PK_SEQ", "pro_batch_create_dtls", $con);
						$data_array_batch_dtls="(".$id_dtls_batch.",".$batch_id_to.",0,".$from_product_id.",".$txt_item_desc.",0,0,0,".$txt_transfer_qnty.",".$id_dtls.",0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";		
					}
				}
				

				$data_array_trans=ltrim($data_array_trans,",");
				$data_array_dtls=ltrim($data_array_dtls,",");
				$data_array_prop=ltrim($data_array_prop,",");
				$data_array_dtls_ac=ltrim($data_array_dtls_ac,",");

				$field_array_trans_update="status_active*is_deleted*updated_by*update_date";
				$field_array_dtls_update="status_active*is_deleted*updated_by*update_date";
				$field_array_proportionate_update="status_active*is_deleted*updated_by*update_date";
				$field_array_batch_dtls_update="status_active*is_deleted*updated_by*update_date";


				$field_array_style_wise_update="from_prod_id*to_prod_id*batch_id*yarn_lot*brand_id*color_id*item_group*from_store*floor_id*room*rack*shelf*bin_box*to_store*to_floor_id*to_room*to_rack*to_shelf*to_bin_box*item_category*transfer_qnty*rate*transfer_value*uom*updated_by*update_date*remarks*to_batch_id*body_part_id*to_body_part*from_style*to_style*from_ord_book_id*from_ord_book_no*to_ord_book_id*to_ord_book_no*from_fabric_id";
				//$field_array_batch_dtls_update="status_active*is_deleted*updated_by*update_date";

				$status_active=0;
				$is_deleted=1;

				$updateTransID_array[]=$update_trans_issue_id; 
				
				$updateTransID_data[$update_trans_issue_id]=explode("*",("".$status_active."*".$is_deleted."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				if($variable_auto_rcv == 1 )
				{
					$updateTransID_array[]=$update_trans_recv_id; 
					$updateTransID_data[$update_trans_recv_id]=explode("*",("".$status_active."*".$is_deleted."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				}
				$update_dtls_ids=str_replace("'", "", $update_dtls_id);
				$update_dtls_id_array[]=$update_dtls_ids; 
				$data_array_dtls_update[$update_dtls_ids]=explode("*",("".$status_active."*".$is_deleted."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));

				//$data_array_style_wise_update=$status_active."*".$is_deleted."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
				if(str_replace("'","",$cbo_company_id)==str_replace("'","",$cbo_company_id_to)) //if company different
				{
					$data_array_style_wise_update=$from_product_id."*".$from_product_id."*".$batch_id."*0*0*".$hide_color_id."*0*".$cbo_store_name."*".$cbo_floor."*".$cbo_room."*".$txt_rack."*".$txt_shelf."*".$cbo_bin."*".$cbo_store_name_to."*".$cbo_floor_to."*".$cbo_room_to."*".$txt_rack_to."*".$txt_shelf_to."*".$cbo_bin_to."*".$cbo_item_category."*".$txt_transfer_qnty."*".$txt_rate."*".$txt_transfer_value."*".$cbo_uom."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$txt_remarks."*".$batch_id_to."*".$cbo_from_body_part."*".$cbo_to_body_part."*".$txt_from_style_no."*".$txt_to_style_no."*".$hdn_from_booking_id."*".$hdn_from_booking_no."*".$hdn_to_booking_id."*".$hdn_to_booking_no."*".$txt_from_fabric_id;
				}
				else
				{
					$data_array_style_wise_update=$from_product_id."*".$product_id."*".$batch_id."*0*0*".$hide_color_id."*0*".$cbo_store_name."*".$cbo_floor."*".$cbo_room."*".$txt_rack."*".$txt_shelf."*".$cbo_bin."*".$cbo_store_name_to."*".$cbo_floor_to."*".$cbo_room_to."*".$txt_rack_to."*".$txt_shelf_to."*".$cbo_bin_to."*".$cbo_item_category."*".$txt_transfer_qnty."*".$txt_rate."*".$txt_transfer_value."*".$cbo_uom."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$txt_remarks."*".$batch_id_to."*".$cbo_from_body_part."*".$cbo_to_body_part."*".$txt_from_style_no."*".$txt_to_style_no."*".$hdn_from_booking_id."*".$hdn_from_booking_no."*".$hdn_to_booking_id."*".$hdn_to_booking_no."*".$txt_from_fabric_id;	

				}



				$updateTransID_pro_array[]=$update_trans_issue_id;
				$data_array_prop_update[$update_trans_issue_id]=explode("*",("".$status_active."*".$is_deleted."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				
				if($variable_auto_rcv == 1 )
				{
					$updateTransID_pro_array[]=$update_trans_recv_id; 
					$data_array_prop_update[$update_trans_recv_id]=explode("*",("".$status_active."*".$is_deleted."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				}
				

			}
			else // store to store PO wise
			{

				$batch_id = str_replace("'", "", $batch_id);
				$batch_id_to = str_replace("'", "", $batch_id_to);


				$updateTransID_array[]=$update_trans_issue_id; 
				
				$updateTransID_data[$update_trans_issue_id]=explode("*",("".$from_product_id."*'".$batch_id."'*".$txt_transfer_date."*".$cbo_store_name."*".$cbo_floor."*".$cbo_room."*".$txt_rack."*".$txt_shelf."*".$cbo_bin."*".$txt_from_order_id."*0*".$cbo_from_body_part."*".$cbo_uom."*".$txt_transfer_qnty."*".$txt_rate."*".$txt_transfer_value."*0*0*".$hdn_from_wo_rate."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				if($variable_auto_rcv == 1 )
				{
					$updateTransID_array[]=$update_trans_recv_id; 
					
					$updateTransID_data[$update_trans_recv_id]=explode("*",("".$from_product_id."*'".$batch_id_to."'*".$txt_transfer_date."*".$cbo_store_name_to."*".$cbo_floor_to."*".$cbo_room_to."*".$txt_rack_to."*".$txt_shelf_to."*".$cbo_bin_to."*".$txt_from_order_id."*0*".$cbo_to_body_part."*".$cbo_uom."*".$txt_transfer_qnty."*".$txt_rate."*".$txt_transfer_value."*0*0*".$hdn_from_wo_rate."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				}
				
				$data_array_dtls=$from_product_id."*".$from_product_id."*'".$batch_id."'*".$hide_color_id."*".$cbo_store_name."*".$cbo_floor."*".$cbo_room."*".$txt_rack."*".$txt_shelf."*".$cbo_bin."*".$cbo_store_name_to."*".$cbo_floor_to."*".$cbo_room_to."*".$txt_rack_to."*".$txt_shelf_to."*".$cbo_bin_to."*".$txt_transfer_qnty."*".$txt_rate."*".$txt_transfer_value."*".$cbo_uom."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$txt_from_order_id."*".$txt_to_order_id."*".$txt_remarks."*'".$batch_id_to."'*".$cbo_from_body_part."*".$cbo_to_body_part."*".$hdn_to_booking_id."*".$hdn_to_booking_no;
				
				if(str_replace("'", "", $update_trans_issue_id))
				{
					$data_array_prop_update[$update_trans_issue_id]=explode("*",("".$txt_from_order_id."*".$from_product_id."*".$hide_color_id."*".$txt_transfer_qnty."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				}

				if($variable_auto_rcv == 1 )
				{
					if(str_replace("'", "", $update_trans_recv_id))
					{
						$data_array_prop_update[$update_trans_recv_id]=explode("*",("".$txt_to_order_id."*".$from_product_id."*".$hide_color_id."*".$txt_transfer_qnty."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
					}
				}
			}

			if(str_replace("'", "", $cbo_transfer_criteria) == 4 )
			{
				$id_dtls_batch = return_next_id_by_sequence("PRO_BATCH_CREATE_DTLS_PK_SEQ", "pro_batch_create_dtls", $con);
				if($data_array_batch_dtls!="") $data_array_batch_dtls.=",";
				$data_array_batch_dtls.="(".$id_dtls_batch.",'".$batch_id_to."',".$txt_to_order_id.",".$from_product_id.",".$txt_item_desc.",0,0,0,".$txt_transfer_qnty.",".$update_dtls_id.",0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			}
			
		}

		$prod=$prodUpdate_adjust=$prodUpdate=$rID=$rID2=$rID3=$rID4=$rID5=$rID6=true;
		if(str_replace("'","",$cbo_transfer_criteria)==1)
		{
			if(count($row_prod)>0)
			{
				if($data_array_prod_update!="")
				{
					$prod=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$product_id,0);
					if($flag==1) 
					{
						if($prod) $flag=1; else $flag=0; 
					}
				}
			}
			else
			{
				$prod=execute_query($sql_prod_insert,0);
				if($flag==1) 
				{
					if($prod) $flag=1; else $flag=0; 
				} 
			}	
			
			
			$prodUpdate_adjust=execute_query(bulk_update_sql_statement("product_details_master","id",$field_array_adjust,$data_array_adjust,$updateProdID_array));
			if($flag==1) 
			{
				if($prodUpdate_adjust) $flag=1; else $flag=0; 
			}
			$prodUpdate=sql_update("product_details_master",$field_array_prodUpdate,$data_array_prodUpdate,"id",$from_product_id,1);
			if($flag==1) 
			{
				if($prodUpdate) $flag=1; else $flag=0; 
			}
		}
		else if(str_replace("'","",$cbo_transfer_criteria)==6 && (str_replace("'","",$cbo_company_id)!=str_replace("'","",$cbo_company_id_to)) )
		{
			if(count($row_prod)>0)
			{
				if($data_array_prod_update!="")
				{
					$prod=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$product_id,0);
					if($flag==1) 
					{
						if($prod) $flag=1; else $flag=0; 
					}
				}
			}
			else
			{
				$prod=execute_query($sql_prod_insert,0);
				if($flag==1) 
				{
					if($prod) $flag=1; else $flag=0; 
				} 
			}	
			
			
			$prodUpdate_adjust=execute_query(bulk_update_sql_statement("product_details_master","id",$field_array_adjust,$data_array_adjust,$updateProdID_array));
			if($flag==1) 
			{
				if($prodUpdate_adjust) $flag=1; else $flag=0; 
			}
			$prodUpdate=sql_update("product_details_master",$field_array_prodUpdate,$data_array_prodUpdate,"id",$from_product_id,1);
			if($flag==1) 
			{
				if($prodUpdate) $flag=1; else $flag=0; 
			}
		}


		if(str_replace("'","",$cbo_transfer_criteria)==1 || str_replace("'","",$cbo_transfer_criteria)==4)
		{
			if(count($data_array_batch_update)>0)
			{
				//echo "10**"; echo bulk_update_sql_statement("pro_batch_create_mst","id",$field_array_batch_update,$data_array_batch_update,$update_batch_id);oci_rollback($con);die;
				$batchMstUpdate=execute_query(bulk_update_sql_statement("pro_batch_create_mst","id",$field_array_batch_update,$data_array_batch_update,$update_batch_id));
				if($flag==1)
				{
					if($batchMstUpdate) $flag=1; else $flag=0;
				}
			}

			//echo "10**insert into pro_batch_create_mst (".$field_array_batch.") values ".$data_array_batch;oci_rollback($con);die;

			if(count($data_array_batch)>0)
			{
				$rID6=sql_insert("pro_batch_create_mst",$field_array_batch,$data_array_batch,0);
				if($flag==1)
				{
					if($rID6) $flag=1; else $flag=0;
				}
			}


			$delete_batch_dtls=execute_query( "delete from pro_batch_create_dtls where mst_id=$previous_to_batch_id and dtls_id=$update_dtls_id",0);
			if($flag==1)
			{
				if($delete_batch_dtls) $flag=1; else $flag=0;
			}


			//if($data_array_batch_dtls!="")
			//{
				//echo "6**insert into pro_batch_create_dtls (".$field_array_batch_dtls.") values ".$data_array_batch_dtls;die;
				$batchDtls=sql_insert("pro_batch_create_dtls",$field_array_batch_dtls,$data_array_batch_dtls,1);
				if($flag==1)
				{
					if($batchDtls) $flag=1; else $flag=0;
				}
			//}

		}

		//echo "10**Failed";oci_rollback($con);die;
		

		//echo "10**".sql_update("inv_item_transfer_style_wise",$field_array_style_wise_update,$data_array_style_wise_update,"id",$update_style_wise_mst_id,1); die;
		//echo "10**".$field_array_update."=".$data_array_update."=".$update_id; die;
		//echo "10**".sql_update("inv_item_transfer_mst",$field_array_update,$data_array_update,"id",$update_id,1); die;

		$rID=sql_update("inv_item_transfer_mst",$field_array_update,$data_array_update,"id",$update_id,1);
		if($rID) $flag=1; else $flag=0; 
		
		if((str_replace("'","",$cbo_transfer_criteria)==3 || str_replace("'","",$cbo_transfer_criteria)==6) || (str_replace("'", "", $cbo_transfer_criteria) == 2 && str_replace("'", "", $style_and_po_wise_variable) == 1)) 
		{

			//-----------update just status in-active-------
			
			$rID2=execute_query("update inv_transaction set status_active=0, is_deleted=1 where id in($update_trans_issue_id)");
			if($variable_auto_rcv == 1 )
			{
				$rID2=execute_query("update inv_transaction set status_active=0, is_deleted=1 where id in($update_trans_recv_id)");
			}
			if($flag==1) 
			{
				if($rID2) $flag=1; else $flag=0; 
			}
			
			//$rID3=sql_update("inv_item_transfer_dtls",$field_array_dtls_update,$data_array_dtls_update,"id",$update_dtls_id,1);
			$rID3=execute_query("update inv_item_transfer_dtls set status_active=0, is_deleted=1 where id in($update_dtls_ids)");
			if($flag==1) 
			{
				if($rID3) $flag=1; else $flag=0; 
			}

			$rID4=execute_query("update order_wise_pro_details set status_active=0, is_deleted=1 where trans_id in($update_trans_issue_id) and entry_form=258 and dtls_id in($update_dtls_ids)");
			if($variable_auto_rcv == 1 )
			{
				$rID4=execute_query("update order_wise_pro_details set status_active=0, is_deleted=1 where trans_id in($update_trans_recv_id) and entry_form=258 and dtls_id in($update_dtls_ids)");
			}
			if($flag==1) 
			{
				if($rID4) $flag=1; else $flag=0; 
			}

			if($variable_auto_rcv==2)
			{
				//$rID5=sql_update("inv_item_transfer_dtls_ac",$field_array_dtls_update,$data_array_dtls_update,"dtls_id",$update_dtls_id,1);
				$rID5=execute_query("update inv_item_transfer_dtls_ac set status_active=0, is_deleted=1 where dtls_id in($update_dtls_ids)");
				if($flag==1) 
				{
					if($rID5) $flag=1; else $flag=0; 
				}
			}
			$update_style_wise_mst_id=str_replace("'","",$update_style_wise_mst_id);
			//echo "10**update inv_item_transfer_style_wise set" ."=".$field_array_style_wise_update ."=".$data_array_style_wise_update."=". $update_style_wise_mst_id;
			 
			$rID6=sql_update("inv_item_transfer_style_wise",$field_array_style_wise_update,$data_array_style_wise_update,"id",$update_style_wise_mst_id,1);
			if($flag==1) 
			{
				if($rID6) $flag=1; else $flag=0; 
			}

			if(str_replace("'","",$cbo_transfer_criteria)==3 || str_replace("'","",$cbo_transfer_criteria)==6) 
			{

				$rID7=execute_query("update pro_batch_create_dtls set status_active=0, is_deleted=1 where mst_id in($previous_to_batch_id)");
				if($flag==1) 
				{
					if($rID7) $flag=1; else $flag=0; 
				}
			}
			//-----------END update just status in-active-------

			//----------START New Insert-----------------------
			//echo "10**insert into inv_transaction (".$field_array_trans_insert.") values ".$data_array_trans;die;
			$rID8=sql_insert("inv_transaction",$field_array_trans_insert,$data_array_trans,0);
			if($flag==1) 
			{
				if($rID8) $flag=1; else $flag=0; 
			} 
			
			//echo "10**insert into inv_item_transfer_dtls (".$field_array_dtls_insert.") values ".$data_array_dtls;die;
			$rID9=sql_insert("inv_item_transfer_dtls",$field_array_dtls_insert,$data_array_dtls,0);
			if($flag==1) 
			{
				if($rID9) $flag=1; else $flag=0; 
			}
			
			//echo "10**insert into order_wise_pro_details (".$field_array_proportionate.") values ".$data_array_prop;die;
			$rID10=sql_insert("order_wise_pro_details",$field_array_proportionate_insert,$data_array_prop,1); 
			if($flag==1) 
			{
				if($rID10) $flag=1; else $flag=0; 
			}
			//echo "10**insert into pro_batch_create_dtls (".$field_array_batch_dtls_insert.") values ".$data_array_batch_dtls;die;
			if(str_replace("'","",$cbo_transfer_criteria)==3 || str_replace("'","",$cbo_transfer_criteria)==6)
			{
				$rID11=sql_insert("pro_batch_create_dtls",$field_array_batch_dtls_insert,$data_array_batch_dtls,1); 
				if($flag==1) 
				{
					if($rID11) $flag=1; else $flag=0; 
				}
			}
			//echo "10**".$variable_auto_rcv; die;
			if($variable_auto_rcv==2)
			{
				//echo "10**insert into inv_item_transfer_dtls_ac (".$field_array_dtls_ac_insert.") values ".$data_array_dtls_ac;die;
				$rID12=sql_insert("inv_item_transfer_dtls_ac",$field_array_dtls_ac_insert,$data_array_dtls_ac,0);
				if($flag==1) 
				{
					if($rID12) $flag=1; else $flag=0; 
				}
			}
			
			//----------END New Insert-----------------------
		}
		else
		{
			$rID2=execute_query(bulk_update_sql_statement("inv_transaction","id",$field_array_trans,$updateTransID_data,$updateTransID_array));
			if($flag==1) 
			{
				if($rID2) $flag=1; else $flag=0; 
			}
			
			$rID3=sql_update("inv_item_transfer_dtls",$field_array_dtls,$data_array_dtls,"id",$update_dtls_id,1);
			if($flag==1) 
			{
				if($rID3) $flag=1; else $flag=0; 
			}
			//echo "10**".bulk_update_sql_statement("inv_transaction","id",$field_array_trans,$updateTransID_data,$updateTransID_array);die;
			$rID4=execute_query(bulk_update_sql_statement("order_wise_pro_details","trans_id",$field_array_proportionate_update,$data_array_prop_update,$updateTransID_array));
			if($flag==1) 
			{
				if($rID4) $flag=1; else $flag=0; 
			}

			if($variable_auto_rcv==2)
			{
				$rID5=sql_update("inv_item_transfer_dtls_ac",$field_array_dtls,$data_array_dtls,"dtls_id",$update_dtls_id,1);
				if($flag==1) 
				{
					if($rID5) $flag=1; else $flag=0; 
				}
			}
		}
		
		//echo "10**".$flag."**".$rID."**".$prodUpdate_adjust."**".$prodUpdate."**".$prod."**".$rID2."**".$rID3."**".$rID4."**".$rID5."**".$rID6;
		//oci_rollback($con);
		//die;
		//echo "10**".$flag."**".$rID."**".$rID2."**".$rID3."**".$rID4."**".$rID5."**".$rID6."**".$rID7."**".$rID8."**".$rID9."**".$rID10."**".$rID11."**".$rID12;
		//oci_rollback($con);
		//die;
		
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_system_id)."**0";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "6**0**"."&nbsp;"."**1";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);   
				echo "1**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_system_id)."**0";
			}
			else
			{
				oci_rollback($con);
				echo "6**0**"."&nbsp;"."**1";
			}
		}	
		disconnect($con);
		die;
 	}
}


if($action=="finish_fabric_transfer_print")
{
    extract($_REQUEST);
	$data=explode('*',$data);

	$transfer_criteria=$data[3];
	$style_and_po_wise_variable=$data[4];
	// print_r ($data);die;
	
	$sql="SELECT id, transfer_system_id, challan_no, transfer_date, transfer_criteria, item_category, to_company, from_order_id, to_order_id, item_category,location_id,to_location_id from inv_item_transfer_mst where id='$data[1]' and company_id='$data[0]'";
	//echo $sql;die;
	$dataArray=sql_select($sql);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name");
	$country_arr=return_library_array( "select id, country_name from  lib_country", "id", "country_name");	
	$prod_sql=sql_select("select id, product_name_details, unit_of_measure from product_details_master where item_category_id=3");
	foreach($prod_sql as $row)
	{
		$product_arr[$row[csf("id")]]=$row[csf("product_name_details")];
		$uom_arr[$row[csf("id")]]=$row[csf("unit_of_measure")];
	}	
?>
<div style="width:930px;">
    <table width="900" cellspacing="0" align="right">
        <tr>
            <td colspan="6" align="center" style="font-size:xx-large"><strong><? echo $company_library[$data[0]]; ?></strong></td>
        </tr>
        <tr class="form_caption">
        	<td colspan="6" align="center" style="font-size:14px">  
				<?
					$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
					foreach ($nameArray as $result)
					{
					?>
						 <? echo $result[csf('plot_no')]; ?>
						 <? if($result[csf('level_no')]!="") echo ",".$result[csf('level_no')]?>
						 <? if($result[csf('road_no')]!="") echo ",".$result[csf('road_no')]; ?>
						 <? if($result[csf('block_no')]!="") echo ",".$result[csf('block_no')];?>
						 <? if($result[csf('city')]!="") echo ",".$result[csf('city')];?>
						 <? if($result[csf('zip_code')]!="") echo ",".$result[csf('zip_code')]; ?>
						 <? if($result[csf('province')]!="") echo ",".$result[csf('province')];?>
						 <? if($result[csf('country_id')]!="") echo ",".$country_arr[$result[csf('country_id')]]; ?><br>
						 Email:<? if($result[csf('email')]!="") echo $result[csf('email')].",";?>
						 Website:<? if($result[csf('website')]!="") echo $result[csf('website')];


					}
                ?> 
            </td>  
        </tr>
        <tr>
            <td colspan="6" align="center" style="font-size:x-large"><strong><u><? echo $data[2]; ?> Report</u></strong></td>
        </tr>
    	</table>
    	<br>
        <table cellspacing="0" width="800" align="center" border="1" rules="all" class="">
        <tr>
        	<td width="120"><strong>Transfer ID :</strong></td><td width="175px"><? echo $dataArray[0][csf('transfer_system_id')]; ?></td>
            <td width="130"><strong>Transfer Criteria:</strong></td> <td width="175px"><? echo $item_transfer_criteria[$dataArray[0][csf('transfer_criteria')]]; ?></td>
            <td width="125"><strong>To Company</strong></td><td width="175px"><? echo $company_library[$dataArray[0][csf('to_company')]]; ?></td>
        </tr>
        <tr>
            <td><strong>Transfer Date:</strong></td><td width="175px"><? echo change_date_format($dataArray[0][csf('transfer_date')]); ?></td>
            <td><strong>Challan No.:</strong></td><td width="175px"><? echo $dataArray[0][csf('challan_no')]; ?></td>
            <td><strong>Item Category:</strong></td> <td width="175px"><? echo $item_category[$dataArray[0][csf('item_category')]]; ?></td>
        </tr>
    </table>
        <br>
    <div style="width:100%;">
    <table cellspacing="0" width="900"  border="1" rules="all" class="rpt_table" >
        <thead bgcolor="#dddddd" align="center">
            <th width="40">SL</th>
            <th width="250">From Referance</th>
            <th width="250">To Referance</th>
            <th width="250">Item Description</th>
            <th width="50">UOM</th>
            <th width="100">Transfered Qnty</th>
            <th width="110">Color</th> 
        </thead>
        <tbody> 
		<?
        //$sql_dtls="SELECT id, from_store, to_store, from_prod_id, transfer_qnty, color_id from inv_item_transfer_dtls where mst_id='$data[1]' and status_active=1 and is_deleted=0";
		if($transfer_criteria==3 || ($transfer_criteria==2 && $style_and_po_wise_variable==1))
		{

			$sql_dtls="SELECT b.id, b.from_store, b.to_store, b.from_prod_id, b.transfer_qnty, b.color_id,a.from_order_id,a.to_order_id,c.from_style,c.to_style
			from inv_item_transfer_mst a ,inv_item_transfer_style_wise c, inv_item_transfer_dtls b
			where a.id=c.mst_id and c.mst_id=b.mst_id and a.id=b.mst_id and a.id='$data[1]' and b.status_active=1 and a.is_deleted=0 ";



			$sql_result= sql_select($sql_dtls);
	        $style_array=array();
	        foreach ($sql_result as $row) 
	        {        
	        	$style_array[]="'".$row[csf('from_style')]."','".$row[csf('to_style')]."'";
	        }
	        $style_array = implode(",",array_unique($style_array));

	        $buyer_arr=return_library_array( "SELECT b.style_ref_no ,b.buyer_name from wo_po_break_down a,wo_po_details_master b  where a.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted =0 and b.style_ref_no in($style_array)", "style_ref_no", "buyer_name" );
	        $style_arr=return_library_array( "SELECT b.style_ref_no ,b.style_ref_no from wo_po_break_down a,wo_po_details_master b  where a.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted =0 and b.style_ref_no in($style_array)", "style_ref_no", "style_ref_no" );

	        $sql_po_no="SELECT b.id, b.from_store, b.to_store, b.from_prod_id, b.color_id,a.from_order_id,a.to_order_id,c.from_style,c.to_style,c.po_breakdown_id
            from inv_item_transfer_mst a ,inv_item_transfer_style_wise c, inv_item_transfer_dtls b,order_wise_pro_details c
            where a.id=c.mst_id and c.mst_id=b.mst_id and  a.id=b.mst_id and b.id=c.dtls_id and a.id='$data[1]' and b.status_active=1 and a.is_deleted=0 and c.entry_form=258 group by 
            b.id, b.from_store, b.to_store, b.from_prod_id, b.color_id,a.from_order_id,a.to_order_id,c.from_style,c.to_style,c.po_breakdown_id";
            $sql_po_no_result= sql_select($sql_po_no);
            $po_id_array=array();
	        foreach ($sql_po_no_result as $row) 
	        {        
	        	$po_id_array[]=$row[csf('po_breakdown_id')];
	        	$po_idArr[$row[csf('id')]]=$row[csf('po_breakdown_id')];
	        }
	        $poIds = implode(",",array_unique($po_id_array));
	        $po_no_arr=return_library_array( "SELECT id, po_number from  wo_po_break_down where id in($poIds)", "id", "po_number");
		}
		else
		{
			$sql_dtls="SELECT b.id, b.from_store, b.to_store, b.from_prod_id, b.transfer_qnty, b.color_id,a.from_order_id,a.to_order_id from inv_item_transfer_mst a , inv_item_transfer_dtls b where a.id=b.mst_id and a.id='$data[1]' and b.status_active=1 and a.is_deleted=0";

			$sql_result= sql_select($sql_dtls);
	        $po_id_array=array();
	        foreach ($sql_result as $row) 
	        {        
	        	$po_id_array[]=$row[csf('from_order_id')].",".$row[csf('to_order_id')];
	        }
	        $poIds = implode(",",array_unique($po_id_array));
	        $buyer_arr=return_library_array( "SELECT a.id ,b.buyer_name from wo_po_break_down a,wo_po_details_master b  where a.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted =0 and a.id in($poIds)", "id", "buyer_name" );
	        $style_arr=return_library_array( "SELECT a.id ,b.style_ref_no from wo_po_break_down a,wo_po_details_master b  where a.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted =0 and a.id in($poIds)", "id", "style_ref_no" );
	        $po_no_arr=return_library_array( "SELECT id, po_number from  wo_po_break_down where id in($poIds)", "id", "po_number");


		}
        // echo $sql_dtls;die();
        
        

         //print_r($style_arr);die();
        // echo $poIds;die();

        $i=1;
        foreach($sql_result as $row)
        {

        	if($transfer_criteria==3 || ($transfer_criteria==2 && $style_and_po_wise_variable==1))
			{
				
				$from_buyer=$buyer_library[$buyer_arr[$row[csf("from_style")]]]; 
				$to_buyer=$buyer_library[$buyer_arr[$row[csf("to_style")]]];
				$from_order=$po_no_arr[$po_idArr[$row[csf('id')]]];
				//$to_order=$po_no_arr[$row[csf("to_style")]];
				$fromStyle=$style_arr[$row[csf("from_style")]];
				$toStyle=$style_arr[$row[csf("to_style")]];

				
			}
			else
			{
				$to_buyer=$buyer_library[$buyer_arr[$row[csf("to_order_id")]]];
				$from_buyer=$buyer_library[$buyer_arr[$row[csf("from_order_id")]]];
				$from_order=$po_no_arr[$row[csf("from_order_id")]];
				$to_order=$po_no_arr[$row[csf("to_order_id")]];
				$fromStyle=$style_arr[$row[csf("from_order_id")]];
				$toStyle=$style_arr[$row[csf("to_order_id")]];
			}
            if ($i%2==0)  
                $bgcolor="#E9F3FF";
            else
                $bgcolor="#FFFFFF";
            $transfer_qnty=$row[csf('transfer_qnty')];
            $transfer_qnty_sum += $transfer_qnty;
            ?>
            <tr bgcolor="<? echo $bgcolor; ?>">
                <td align="center"><? echo $i; ?></td>
                <td style="word-break: break-all;"><p>Store:<? echo $store_library[$row[csf("from_store")]]; ?><br>Style:<? echo $fromStyle; ?><br>Buyer:<? echo $from_buyer; ?><br>

                	<? if($transfer_criteria==2){?> Order No:<? echo $from_order; ?><? } ?>

                </p></td>

                <td style="word-break: break-all;"><p>Store:<? echo $store_library[$row[csf("to_store")]]; ?><br>Style:<? echo $toStyle; ?><br>Buyer:<? echo $to_buyer; ?><br>

                	<? if($transfer_criteria==2){?> Order No:<? echo $from_order; ?> <? } ?>

                </p></td>  


                <td style="word-break: break-all;"><p><? echo $product_arr[$row[csf("from_prod_id")]]; ?></p></td>
                <td align="center"><p><? echo $unit_of_measurement[$uom_arr[$row[csf("from_prod_id")]]]; ?></p></td>
                <td align="right"><p><? echo $row[csf("transfer_qnty")]; ?></p></td>
                <td style="word-break: break-all;"><p><? echo $color_arr[$row[csf("color_id")]]; ?></p></td>
            </tr>
            <? 
            $i++; 
        } 
        ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" align="right"><strong>Total :</strong></td>
                <td align="right"><?php echo $transfer_qnty_sum; ?></td>
                <td align="right"><?php //echo $req_qny_edit_sum; ?></td>
            </tr>                           
        </tfoot>
        </table>
        <br>
         <?
            echo signature_table(23, $data[0], "900px");
         ?>
        </div>
    </div>   
	<?	
    exit();
}

if($action=="finish_fabric_transfer_print_2")
{
    extract($_REQUEST);
	$data=explode('*',$data);
	//print_r ($data);
	$batch_arr = return_library_array("select id, batch_no from pro_batch_create_mst where is_deleted=0 and status_active=1","id","batch_no");
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$country_arr=return_library_array( "select id, country_name from  lib_country", "id", "country_name"  );
	$location_library=return_library_array( "select id, location_name from lib_location", "id", "location_name");
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
	
	$prod_sql=sql_select("select id, product_name_details, unit_of_measure from product_details_master where item_category_id=3");
	foreach($prod_sql as $row)
	{
		$product_arr[$row[csf("id")]]=$row[csf("product_name_details")];
		$uom_arr[$row[csf("id")]]=$row[csf("unit_of_measure")];
	}

	$sql="select a.id, a.transfer_system_id, a.challan_no, a.transfer_date, a.transfer_criteria, b.batch_id, a.item_category, a.location_id, a.to_company, a.from_store_id, a.to_store_id, b.from_order_id, b.to_order_id, a.item_category, a.location_id, b.from_store, b.to_store, b.from_prod_id, sum(b.transfer_qnty) as transfer_qnty, b.uom,b.remarks  
	from inv_item_transfer_mst a,inv_item_transfer_dtls b 
	where a.id=b.mst_id and a.id='$data[1]' and a.company_id='$data[0]' and a.status_active=1 and a.is_deleted=0 
	group by a.id, a.transfer_system_id, a.challan_no, a.transfer_date, a.transfer_criteria,b.batch_id, a.item_category, a.location_id, a.to_company, a.from_store_id, a.to_store_id, b.from_order_id, b.to_order_id, a.item_category, a.location_id, b.from_store, b.to_store, b.from_prod_id, b.uom,b.remarks  
	order by b.from_prod_id";
	//echo $sql;die;
	$dataArray=sql_select($sql);
	$orderID="";$prodID="";
	foreach ($dataArray as $row) {
		$orderidArr[$row[csf('from_order_id')]]=$row[csf('from_order_id')];
		$orderID.=$row[csf('from_order_id')].',';
  		$prodID.=$row[csf('from_prod_id')].',';
		
	}
	$orderID=chop($orderID,",");
	$prodID=chop($prodID,",");
	//echo "select a.id ,a.po_number,b.style_ref_no from wo_po_break_down a,wo_po_details_master b  where a.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted =0 and a.id in(".implode(",",$orderidArr).")";die;
	$sql_order= sql_select("select a.id ,a.po_number, b.style_ref_no from wo_po_break_down a,wo_po_details_master b  where a.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted =0 and a.id in(".implode(",",$orderidArr).")");
	foreach ($sql_order as  $row) {
		$orderArr[$row[csf('id')]]['po_number']=$row[csf('po_number')];
		$orderArr[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
	}

	$sql_dtls ="select   a.company_id, a.supplier_id,  b.buyer_id, c.po_breakdown_id as order_id, d.id as prod_id, d.product_name_details, d.color , f.booking_no as booking_no_batch
	from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c, product_details_master d , inv_transaction e, pro_batch_create_mst f
	where a.id=b.mst_id and b.id = c.dtls_id and b.prod_id=d.id  and c.trans_id=e.id and b.batch_id=f.id and a.item_category=2  and a.entry_form=37 and c.entry_form=37 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id is not null and c.po_breakdown_id in($orderID) and d.id in($prodID) group by a.company_id, a.supplier_id, b.buyer_id, c.po_breakdown_id, d.id, d.product_name_details, d.color , f.booking_no order by d.id";
	//echo $sql_dtls;
	$sql_result= sql_select($sql_dtls);

	$tarnsferArry=array();
	foreach($sql_result as $row)
	{
		$tarnsferArry[$row[csf('order_id')]][$row[csf('prod_id')]]['buyer_id']=$row[csf('buyer_id')];
		$tarnsferArry[$row[csf('order_id')]][$row[csf('prod_id')]]['booking_no_batch']=$row[csf('booking_no_batch')];
		$tarnsferArry[$row[csf('order_id')]][$row[csf('prod_id')]]['color']=$row[csf('color')];
		$tarnsferArry[$row[csf('order_id')]][$row[csf('prod_id')]]['product_name_details']=$row[csf('product_name_details')];
		$tarnsferArry[$row[csf('order_id')]][$row[csf('prod_id')]]['batch_id']=$batch_arr[$row[csf('batch_id')]];
	}
	$tableWidth = 1300;
?>
<div style="width:<? echo $tableWidth; ?>px;">
    <table width="<? echo $tableWidth; ?>" cellspacing="0" align="right">
        <tr>
        	<td align="left"><img src="../../<? echo $image_location; ?>" height="70" width="180"></td>
            <td  align="left" style="font-size:xx-large"><strong><? echo $company_library[$data[0]]; ?></strong></td>
            <td></td>
        </tr>
        <tr class="form_caption">
        	<td colspan="6" align="center" style="font-size:14px">  
				<?
					$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]"); 
					foreach ($nameArray as $result)
					{ 
					?>
						Plot No: <? echo $result[csf('plot_no')]; ?> 
						Level No: <? echo $result[csf('level_no')]?>
						Road No: <? echo $result[csf('road_no')]; ?> 
						Block No: <? echo $result[csf('block_no')];?> 
						City No: <? echo $result[csf('city')];?> <br>
						Zip Code: <? echo $result[csf('zip_code')]; ?> 
						Province No: <?php echo $result[csf('province')];?> 
						Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br> 
						Email Address: <? echo $result[csf('email')];?> 
						Website No: <? echo $result[csf('website')];
					}
                ?> 

            </td>  
        </tr>
        <tr><td></td></tr>
        <tr><td></td></tr>
        <tr><td></td></tr>
        <tr>
            <td colspan="6" align="center" style="font-size:x-large;"><strong><u><? echo $data[2]; ?> Report</u></strong></td>
        </tr>
    </table>
    <table width="<? echo $tableWidth; ?>" cellspacing="0" align="right" style="margin-top: 30px;">
        <tr>
        	<td width="130"><strong>Transfer Criteria</strong></td> <td width="175px"><strong>:<? echo $item_transfer_criteria[$dataArray[0][csf('transfer_criteria')]]; ?></strong></td>
        	<td><strong>Print Date and Time</strong></td><td width="175px"><strong>:<? echo date("d/m/Y") ." : ". date("h:i:sa"); ?></strong></td>
        </tr>
        <tr style="height: 20px;">
        </tr>

        <tr>
        	<td width="125"><strong>From</strong></td><td width="175px">: <? echo $store_library[$dataArray[0][csf('from_store')]]; ?></td>
        	<td width="125"><strong>To Company</strong></td><td width="175px">: <? echo $company_library[$dataArray[0][csf('to_company')]]; ?></td>
        </tr>
        <tr>
        	<td width="120"><strong>Transfer ID</strong></td><td width="175px"><strong>: <? echo $dataArray[0][csf('transfer_system_id')]; ?></strong></td>
        	<td width="125"><strong>To Store</strong></td><td width="175px">: <? echo $store_library[$dataArray[0][csf('to_store')]]; ?></td>
        </tr>
        <tr>
        	<td><strong>Transfer Date</strong></td><td width="175px">: <? echo change_date_format($dataArray[0][csf('transfer_date')]); ?></td>
        </tr>
        <tr>
        <td><strong>Challan No</strong></td><td width="175px">: <? echo $dataArray[0][csf('challan_no')]; ?></td>
        	</tr>	
        <tr>
			<td><strong>Item Category</strong></td> <td width="175px">: <? echo $item_category[$dataArray[0][csf('item_category')]]; ?></td>
        </tr>
    </table>
        <br>
    <div  style="width:<? echo $tableWidth; ?>px;">
    <table align="right" cellspacing="0" width="1300"  border="1" rules="all" class="rpt_table" >
        <thead bgcolor="#dddddd" align="center">
            <th width="30">SL</th>
            <th width="100">Buyer</th>
            <th width="180">Order</th>
            <th width="100">Style</th>
            <th width="160">Fab. Booking</th>
            <th width="100">Batch</th>
            <th width="100">Fab. Color</th> 
            <th width="350">Item Description</th> 
            <th width="100">Fab. WT</th> 
            <th width="50">UOM</th> 
            <th>Remarks</th> 
        </thead>
        <tbody> 
   
<?
  	
	//$sql_dtls="select id, from_store, to_store, from_prod_id, transfer_qnty, color_id from inv_item_transfer_dtls where mst_id='$data[1]' and status_active=1 and is_deleted=0";

	
	$i=1;
	foreach($dataArray as $row)
	{
		if ($i%2==0)  
			$bgcolor="#E9F3FF";
		else
			$bgcolor="#FFFFFF";
			
		?>
			<tr bgcolor="<? echo $bgcolor; ?>">
				<td width="30" align="center"><? echo $i; ?></td>
				<td align="center" width="100"><? echo $buyer_library[$tarnsferArry[$row[csf('from_order_id')]][$row[csf('from_prod_id')]]['buyer_id']]; ?></td>
				<td align="center" width="100" title="<? echo $order_id; ?>"><? echo $orderArr[$row[csf('from_order_id')]]['po_number'] ; ?></td>
				<td align="center" width="100"><? echo $orderArr[$row[csf('from_order_id')]]['style_ref_no']; ?></td>
				<td align="center" width="180"><? echo $tarnsferArry[$row[csf('from_order_id')]][$row[csf('from_prod_id')]]['booking_no_batch']; ?></td>
				<td align="center" width="100"><? echo $batch_arr[$row[csf('batch_id')]]; ?></td>
				<td align="center" width="100"><? echo $color_arr[$tarnsferArry[$row[csf('from_order_id')]][$row[csf('from_prod_id')]]['color']]; ?></td>
				<td align="center" width="250"><? echo $tarnsferArry[$row[csf('from_order_id')]][$row[csf('from_prod_id')]]['product_name_details']; ?></td>
				<td align="right" width="100"><? echo $total_trnf=$row[csf('transfer_qnty')];  ?></td>
				<td align="center" width="50"><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
				<td align="center"><? echo $row[csf('remarks')]; ?></td>
			</tr>
			<?
			$total_transfer_qty+=$total_trnf;
			 $i++; } ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="8" align="right"><strong>Total :</strong></td>
                <td align="right"><?php echo $total_transfer_qty; ?></td>
                <td align="right"><?php //echo $req_qny_edit_sum; ?></td>
                <td align="right"></td>
            </tr>                           
        </tfoot>
      </table>
        <br>
		 <?
            echo signature_table(23, $data[0], "1100px");
         ?>
      </div>
   </div>   
 <?	
 exit();
}



function sql_update_a($strTable,$arrUpdateFields,$arrUpdateValues,$arrRefFields,$arrRefValues,$commit)
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

if ($action=="load_body_part")
{
	$data=explode("_",$data);
	$order_id = $data[1];
	$product_id = $data[2];

	$transfer_criteria = $data[3];
	$bookingNo=$data[5];
	$bookingType=$data[6];


	//if from body part drop down
	if($data[4] == 1)
	{
		$id = "cbo_from_body_part";
	}
	else
	{
		$id = "cbo_to_body_part";
	}

	// if body part found
	if($data[0] )
	{ 
		echo create_drop_down( $id, 160,$body_part,"", 1, "--Select--", $data[0], "change_body_part(this.id)",0,$data[0] );
	}
	else
	{
		$fabric_cond_1=$fabric_cond_2="";
		/*if($transfer_criteria !=2 && $data[4] != 1)
		{
			//for to body part dropdown but not store to store tranfer criteria then fabrication check needed .
			$detarFromProduct =return_library_array("select id, detarmination_id from  product_details_master where id =$product_id","id","detarmination_id");
			$fabric_cond_1 = " and a.lib_yarn_count_deter_id = $detarFromProduct[$product_id] ";
			$fabric_cond_2 = " and b.lib_yarn_count_deter_id = $detarFromProduct[$product_id] ";
		}*/

		if($transfer_criteria==6 && $order_id=="")
		{ 
			$body_part_sql = sql_select("SELECT b.body_part as body_part_id from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_no='$bookingNo' and a.booking_type =4 group by  b.body_part");
		}
		else
		{

			if($bookingType==4) // sample order/ SM
			{
				$body_part_sql = sql_select("SELECT d.body_part_id from wo_booking_mst a, wo_booking_dtls b, wo_po_details_master c ,sample_development_fabric_acc d,lib_yarn_count_determina_mst h,lib_yarn_count_determina_dtls j, lib_composition_array k where b.entry_form_id=140 and b.booking_type=4 and a.booking_no=b.booking_no and b.job_no=c.job_no and b.pre_cost_fabric_cost_dtls_id=d.id and d.determination_id=h.id and h.id=j.mst_id and j.copmposition_id=k.id and b.po_break_down_id in($order_id) and a.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.grey_fab_qnty>0 group by  a.booking_no,d.body_part_id,b.po_break_down_id 
				union all   
				SELECT  d.body_part_id  from wo_booking_mst a, wo_booking_dtls b,wo_pre_cost_fabric_cost_dtls d where b.entry_form_id is null and b.booking_type=4 and a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=d.id and b.po_break_down_id in($order_id) group by d.body_part_id");
			}
			else
			{
				$body_part_sql = sql_select("SELECT a.body_part_id from wo_pre_cost_fabric_cost_dtls a,wo_booking_dtls b where a.job_no=b.job_no and a.status_active=1 and a.is_deleted=0  and b.status_active = 1 and b.is_deleted = 0 and b.pre_cost_fabric_cost_dtls_id = a.id and b.po_break_down_id in($order_id) $fabric_cond_1 and b.booking_type =1");
			}
				//$body_part_sql = sql_select("SELECT a.body_part_id from wo_pre_cost_fabric_cost_dtls a,wo_booking_dtls b where a.job_no=b.job_no and a.status_active=1 and a.is_deleted=0  and b.status_active = 1 and b.is_deleted = 0 and b.pre_cost_fabric_cost_dtls_id = a.id and b.po_break_down_id in($order_id) $fabric_cond_1 and b.booking_type =1 union all select b.body_part_id from wo_pre_cost_fab_conv_cost_dtls a, wo_pre_cost_fabric_cost_dtls b, wo_booking_dtls c where b.job_no=c.job_no and a.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.pre_cost_fabric_cost_dtls_id = a.id and c.po_break_down_id in($order_id) $fabric_cond_2 and a.fabric_description = b.id and c.booking_type = 4");
		}

		foreach ($body_part_sql as $row)
		{
			$body_part_arr[$row[csf("body_part_id")]] = $row[csf("body_part_id")];
		}
		$body_part_ids = implode(",",array_filter($body_part_arr));
		if($body_part_ids != "")
		{
			echo create_drop_down( $id, 160,$body_part,"", 1, "--Select--", 0, "change_body_part(this.id)",0,$body_part_ids );
		}else{
			echo create_drop_down( $id, 160,$blank_array,"", 1, "--Select--", 0, "",0,"" );
		}

	}

	exit();
}
?>