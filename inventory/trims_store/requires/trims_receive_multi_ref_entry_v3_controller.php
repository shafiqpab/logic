<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
$permission=$_SESSION['page_permission'];
include('../../../includes/common.php');
$payment_yes_no=array(0=>"yes", 1=>"No");

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

$userCredential = sql_select("SELECT store_location_id  FROM user_passwd where id=$user_id");
$storeCredentialId = $userCredential[0][csf('store_location_id')];
if ($storeCredentialId !='') {
    $store_location_credential_cond = " and a.id in($storeCredentialId)"; 
}


if ($action == "varible_inventory") {
	$sql_variable_inventory = sql_select("select id, independent_controll, rate_optional, is_editable, rate_edit  from variable_settings_inventory where company_name=$data and variable_list=20 and status_active=1 and menu_page_id=631");
	if (count($sql_variable_inventory) > 0) 
	{
		echo "1**" . $sql_variable_inventory[0][csf("is_editable")];
	} else {
		echo "0**" . $sql_variable_inventory[0][csf("is_editable")];
	}
	$variable_inventory=return_field_value("store_method","variable_settings_inventory","company_name=$data and item_category_id=4 and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1");
	echo "**" . $variable_inventory;
	die;
}

//if ($action=="upto_variable_settings")
//{
//    extract($_REQUEST);
//    echo $variable_inventory=return_field_value("store_method","variable_settings_inventory","company_name='$cbo_company_id' and item_category_id=4 and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1");
//    exit();
//}


if($action=="load_drop_down_supplier")
{
	$data_ref=explode("_",$data);
	if($data_ref[1]==3 || $data_ref[1]==5)
	{
		echo create_drop_down( "cbo_supplier_name", 122,"select id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name",'id,company_name', 1, '-- Select Supplier --',0,0,1);
	}
	else
	{
		echo create_drop_down( "cbo_supplier_name", 122,"select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company=$data_ref[0] and b.party_type in(4,5) and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name",'id,supplier_name', 1, '-- Select Supplier --',0,0,1);
	}
	exit();
}



if($action=="print_button_variable_setting")
{
    $print_report_format=0;
    $print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=6 and report_id=230 and is_deleted=0 and status_active=1");
    $printButton=explode(',',$print_report_format);

    foreach($printButton as $id)
    {
        if($id==78)$buttonHtml.='<input type="button" id="btn_print_1" name="btn_print_1" value="Print" class="formbutton" style="width:80px;" onClick="fnc_trims_receive(4)" />';
        if($id==84)$buttonHtml.='<input type="button" id="btn_print_2" name="btn_print_2" value="Print 2" class="formbutton" style="width:80px;" onClick="fnc_trims_receive(5)" />';
		if($id==85)$buttonHtml.='<input type="button" id="btn_print_3" name="btn_print_3" value="Print 3" class="formbutton" style="width:80px;" onClick="fnc_trims_receive(6)" />';
    }

    echo "document.getElementById('button_data_panel').innerHTML = '".$buttonHtml."';\n";
    exit();
}

if($action=="load_drop_down_supplier_popup")
{
	$data_ref=explode("_",$data);
	if($data_ref[1]==3 || $data_ref[1]==5)
	{
		echo create_drop_down( "cbo_supplier", 122,"select id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name",'id,company_name', 1, '-- Select Supplier --',0,0,0);
	}
	else
	{
		echo create_drop_down( "cbo_supplier", 122,"select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company=$data_ref[0] and b.party_type in(4,5) and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name",'id,supplier_name', 1, '-- Select Supplier --',0,0,0);
	}
	exit();
}

if($action=="load_drop_down_pay_mode")
{
	$data_ref=explode("_",$data);
	if($data_ref[0]==1)
	{
		echo create_drop_down( "cbo_pay_mode", 120, $pay_mode,"", 1, "-- Select Pay Mode --", 2, "load_drop_down( 'trims_receive_multi_ref_entry_v3_controller', $cbo_company_id+'_'+this.value, 'load_drop_down_supplier_popup', 'supplier_td_id' )",1 );
		
	}
	else
	{
		echo create_drop_down( "cbo_pay_mode", 120, $pay_mode,"", 1, "-- Select Pay Mode --", 0, "load_drop_down( 'trims_receive_multi_ref_entry_v3_controller', $data_ref[1]+'_'+this.value, 'load_drop_down_supplier_popup', 'supplier_td_id' )",0,'1,3,4,5' );
		
	}
	exit();
}

//if ($action=="load_room_rack_self_bin")
//{
//	load_room_rack_self_bin("requires/trims_receive_multi_ref_entry_v3_controller",$data);
//}

// if ($action=="load_drop_down_store")
// {
// 	echo create_drop_down( "cbo_store_name", 142, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id='$data' and b.category_type=4 and a.status_active=1 and a.is_deleted=0 group by a.id, a.store_name order by a.store_name","id,store_name", 1, "--Select store--", 0, "" );
// 	exit();
// }

if ($action == "load_drop_down_store") 
{
	$data = explode("_", $data);
	$category_id = 4;
	$sql_store = "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and
	a.company_id='$data[0]' and b.category_type=$category_id and a.status_active=1 and a.is_deleted=0 $store_location_credential_cond group by a.id, a.store_name order by a.store_name";
	// echo $sql_store;die;
	/*$store_id = sql_select($sql_store);
	$selected_store = "";
	if (count($store_id) == 1) $selected_store = $store_id[0][csf('id')];*/
	echo create_drop_down("cbo_store_name", 142, $sql_store, "id,store_name", 1, "--Select store--", 0, "fn_load_floor(this.value);reset_room_rack_shelf('','cbo_store_name');");
	exit();
}


if($action=="floor_list")
{
	$data_ref=explode("__",$data);
	$floor_arr=array();
	$location_cond = "";
	if($data_ref[2]) $location_cond = "and b.location_id='$data_ref[2]'";
	$floor_data=sql_select("select b.floor_id, a.floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.floor_id and b.store_id='$data_ref[1]' and a.company_id='$data_ref[0]' $location_cond and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0
	group by b.floor_id,a.floor_room_rack_name  order by a.floor_room_rack_name asc");
	foreach($floor_data as $row)
	{
		$floor_arr[$row[csf('floor_id')]]=$row[csf('floor_room_rack_name')];
	}
	$jsFloor_arr= json_encode($floor_arr);
	echo $jsFloor_arr;
	die();
}

if($action=="room_list")
{
	$data_ref=explode("__",$data); // com_id + "__" + location_id + "__" + store_id + "__" + floor_id;
	$room_arr=array();
	$location_cond = "";
	if($data_ref[1]) $location_cond = "and b.location_id='$data_ref[1]'";
	$room_data=sql_select("select b.room_id, a.floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.room_id and b.store_id='$data_ref[2]' and a.company_id='$data_ref[0]' and b.floor_id='$data_ref[3]' $location_cond and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0
	group by b.room_id,a.floor_room_rack_name  order by a.floor_room_rack_name asc");
	foreach($room_data as $row)
	{
		$room_arr[$row[csf('room_id')]]=$row[csf('floor_room_rack_name')];
	}
	$jsRoom_arr= json_encode($room_arr);
	echo $jsRoom_arr;
	die();
}

if($action=="rack_list")
{
	$data_ref=explode("__",$data); // com_id + "__" + location_id + "__" + store_id + "__" + floor_id;
	$rack_arr=array();
	$location_cond = "";
	if($data_ref[1]) $location_cond = "and b.location_id='$data_ref[1]'";
	$rack_data=sql_select("select b.rack_id, a.floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.rack_id and b.store_id='$data_ref[2]' and a.company_id='$data_ref[0]' and b.room_id='$data_ref[3]' $location_cond and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0
	group by b.rack_id,a.floor_room_rack_name  order by a.floor_room_rack_name asc");
	foreach($rack_data as $row)
	{
		$rack_arr[$row[csf('rack_id')]]=$row[csf('floor_room_rack_name')];
	}
	$jsRack_arr= json_encode($rack_arr);
	echo $jsRack_arr;
	die();
}

if($action=="shelf_list")
{
	$data_ref=explode("__",$data); // com_id + "__" + location_id + "__" + store_id + "__" + floor_id;
	$shelf_arr=array();
	$location_cond = "";
	if($data_ref[1]) $location_cond = "and b.location_id='$data_ref[1]'";
	$shelf_data=sql_select("select b.shelf_id, a.floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.shelf_id and b.store_id='$data_ref[2]' and a.company_id='$data_ref[0]' and b.rack_id='$data_ref[3]' $location_cond and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0
	group by b.shelf_id,a.floor_room_rack_name  order by a.floor_room_rack_name asc");
	foreach($shelf_data as $row)
	{
		$shelf_arr[$row[csf('shelf_id')]]=$row[csf('floor_room_rack_name')];
	}
	$jsShelf_arr= json_encode($shelf_arr);
	echo $jsShelf_arr;
	die();
}

if($action=="bin_list")
{
	$data_ref=explode("__",$data); // com_id + "__" + location_id + "__" + store_id + "__" + floor_id;
	$bin_arr=array();
	$location_cond = "";
	if($data_ref[1]) $location_cond = "and b.location_id='$data_ref[1]'";
	$bin_data=sql_select("select b.bin_id, a.floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.bin_id and b.store_id='$data_ref[2]' and a.company_id='$data_ref[0]' and b.shelf_id='$data_ref[3]' $location_cond and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0
	group by b.bin_id,a.floor_room_rack_name  order by a.floor_room_rack_name asc");
	foreach($bin_data as $row)
	{
		$bin_arr[$row[csf('bin_id')]]=$row[csf('floor_room_rack_name')];
	}
	$jsBin_arr= json_encode($bin_arr);
	echo $jsBin_arr;
	die();
}

if($action=="get_library_exchange_rate")
{
	$exchange_rate=sql_select("select conversion_rate from currency_conversion_rate where currency=$data and status_active=1 and is_deleted=0 order by id desc");
	if($data==1)
	{
		echo "document.getElementById('txt_exchange_rate').value 	= '1';\n";
	}
	else
	{
		echo "document.getElementById('txt_exchange_rate').value 	= '".$exchange_rate[0][csf("conversion_rate")]."';\n";
	}
	exit();
}

 //-------------------START ----------------------------------------

$color_arr = return_library_array("select id, color_name from lib_color","id","color_name");
$size_arr = return_library_array("select id, size_name from lib_size","id","size_name");

$trim_group_arr =array(); 
$data_array=sql_select("select id, item_name, order_uom from lib_item_group");
foreach($data_array as $row)
{
	$trim_group_arr[$row[csf('id')]]['name']=$row[csf('item_name')];
	$trim_group_arr[$row[csf('id')]]['uom']=$row[csf('order_uom')];
}



if ($action=="wo_pi_popup")
{
	echo load_html_head_contents("WO/PI Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?> 

	<script>
		var update_id='<? echo $update_id; ?>';
		
		function js_set_value(id,no,type,data,receive_basis,booking_type)
		{
			if(update_id!="")
			{
				var response = trim(return_global_ajax_value(update_id, 'duplication_check', '', 'trims_receive_multi_ref_entry_v3_controller'));
				if(response!="")
				{
					var curr_data=data.split("**");
					var curr_supplier_id=curr_data[0];
					var curr_currency_id=curr_data[1];
					var curr_source=curr_data[2];
					var curr_lc_id=curr_data[4];
					
					var prev_data=response.split("**");
					var prev_supplier_id=prev_data[0];
					var prev_currency_id=prev_data[1];
					var prev_source=prev_data[2];
					var prev_lc_id=prev_data[3];
					
					if(!(curr_supplier_id==prev_supplier_id && curr_currency_id==prev_currency_id && curr_source==prev_source))
					{
						alert("Supplier, Currency and Source Mix not allow in Same Received ID \n");
						//alert("Supplier, Currency and Source Mix not allow in Same Received ID \n"+curr_supplier_id+"=="+prev_supplier_id+"=="+curr_currency_id+"=="+prev_currency_id+"=="+curr_source+"=="+prev_source);
						return;
					}
				}
			}
			//alert("Fuad");return;
			$('#hidden_wo_pi_id').val(id);
			$('#hidden_wo_pi_no').val(no);
			$('#booking_without_order').val(type);
			$('#hidden_data').val(data);
			$('#receive_basis').val(receive_basis);
			$('#hid_booking_type').val(booking_type);
			//alert(receive_basis);
			parent.emailwindow.hide();
		}
	
    </script>

	</head>

	<body onLoad="set_hotkey()">
	<div align="center" style="width:1190px;">
	<form name="searchwofrm"  id="searchwofrm">
		<fieldset style="width:990px; margin-left:5px">
		<legend>Enter search words</legend>           
            <table cellpadding="0" cellspacing="0" border="1" rules="all" width="990" class="rpt_table">
                <thead>
                    <th width="140">Receive Basis</th>
                    <th width="120">Pay Mode</th>
                    <th width="140">Supplier Name</th>
                    <th width="140">Buyer Name</th>
                    <th width="110">WO No</th>
                    <th width="110">PI No</th>
                    <th width="180">WO/PI Date</th>
                    <th>
                    	<input type="reset" name="reset" id="reset" value="Reset" style="width:60px" class="formbutton" />
                        <input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes" value="<? echo $cbo_company_id; ?>">
                    	<input type="hidden" name="hidden_wo_pi_id" id="hidden_wo_pi_id" class="text_boxes" value="">  
                        <input type="hidden" name="hidden_wo_pi_no" id="hidden_wo_pi_no" class="text_boxes" value=""> 
                        <input type="hidden" name="booking_without_order" id="booking_without_order" class="text_boxes" value="">
                        <input type="hidden" name="hidden_data" id="hidden_data" class="text_boxes" value="">
                        <input type="hidden" name="receive_basis" id="receive_basis" class="text_boxes" value=""> 
                        <input type="hidden" name="hid_booking_type" id="hid_booking_type" class="text_boxes" value=""> 
                    </th> 
                </thead>
                <tr class="general">
                    <td align="center">	
                    	<? echo create_drop_down("cbo_receive_basis",140,$receive_basis_arr,"",1,"-- Select --",$recieve_basis,"load_drop_down( 'trims_receive_multi_ref_entry_v3_controller',this.value+'_'+$cbo_company_id, 'load_drop_down_pay_mode', 'pay_mode_td' );","0","1,2"); ?>
                    </td>
                    <td id="pay_mode_td">
					<?
					if($recieve_basis==1)
					{
						echo create_drop_down( "cbo_pay_mode", 120, $pay_mode,"", 1, "-- Select Pay Mode --", 2, "load_drop_down( 'trims_receive_multi_ref_entry_v3_controller', $cbo_company_id+'_'+this.value, 'load_drop_down_supplier_popup', 'supplier_td_id' )",1 );
					}
					else if($recieve_basis==2)
					{
						echo create_drop_down( "cbo_pay_mode", 120, $pay_mode,"", 1, "-- Select Pay Mode --", 0, "load_drop_down( 'trims_receive_multi_ref_entry_v3_controller', $cbo_company_id+'_'+this.value, 'load_drop_down_supplier_popup', 'supplier_td_id' )",0,'1,3,4,5' );
					}
					else
					{
						echo create_drop_down( "cbo_pay_mode", 120, $pay_mode,"", 1, "-- Select Pay Mode --", 0, "load_drop_down( 'trims_receive_multi_ref_entry_v3_controller', $cbo_company_id+'_'+this.value, 'load_drop_down_supplier_popup', 'supplier_td_id' )",0 );
					}
					 
					?>
                    </td>
                    <td align="center" id="supplier_td_id">	
                    	<?
						$sup_cond="";
						if(str_replace("'","",$cbo_supplier_name)>0) $sup_cond=" and a.id=$cbo_supplier_name"; 
						echo create_drop_down( "cbo_supplier", 140,"select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company=$cbo_company_id $sup_cond and b.party_type in(4,5) and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name",'id,supplier_name', 1, '-- Select Supplier --',0,0);
						?>
                    </td>
                    <td align="center">	
                    	<?
						$is_disable=0;
						if($recieve_basis==1) $is_disable=1;
						echo create_drop_down( "cbo_buyer", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$cbo_company_id and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",$is_disable ); 
						?>
                    </td>  
                    <td align="center">				
                        <input type="text" style="width:100px" class="text_boxes"  name="txt_wo_num" id="txt_wo_num" />	
                    </td>
                    <td align="center">				
                        <input type="text" style="width:100px" class="text_boxes"  name="txt_pi_no" id="txt_pi_no" />	
                    </td> 
                    <td align="center">
                    	<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px">To
					  	<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px">
					</td>						
            		<td align="center">
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_wo_num').value+'_'+document.getElementById('cbo_receive_basis').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $cbo_currency_id ?>+'_'+<? echo $cbo_source ?>+'_'+document.getElementById('cbo_supplier').value+'_'+document.getElementById('cbo_buyer').value+'_'+document.getElementById('txt_pi_no').value+'_'+document.getElementById('cbo_pay_mode').value+'_'+document.getElementById('cbo_year_selection').value, 'create_wo_pi_search_list_view', 'search_div', 'trims_receive_multi_ref_entry_v3_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:60px;" />
                     </td>
                </tr>
                <tr>
                	<td colspan="8" align="center" height="30" valign="middle"><? echo load_month_buttons(1); ?></td>
                </tr>
           </table>
            <div style="margin-top:10px;" id="search_div" align="left"></div> 
		</fieldset>
	</form>
	</div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}

if($action=="create_wo_pi_search_list_view")
{
	$data = explode("_",$data);
	//echo $data[1]."jahid";die;
	$recieve_basis=$data[1];
	$company_id =$data[2];
	$date_form=$data[3];
	$date_to =$data[4];
	
	$cbo_currency_id =$data[5];
	$cbo_source =$data[6];
	$cbo_supplier_name =$data[7];
	$cbo_buyer =$data[8];
	$pi_num =$data[9];
	$wo_num =$data[0];
	$pay_mode =$data[10];
	$cbo_year =$data[11];
	//echo $cbo_year.jahid;die;
	
	if($recieve_basis<1){ echo "Please Select Receive Basis.";die;}
	if($recieve_basis==1)
	{
		if($wo_num=="" && $pi_num=="" && $date_form=="" && $date_to=="" && $cbo_supplier_name==0){ echo "Please select date range.";die;}
	}
	else
	{
		if($wo_num=="" && $date_form=="" && $date_to=="" && $cbo_supplier_name==0 && $cbo_buyer==0){ echo "Please select date range.";die;}
	}
	
	
	if($date_form!="" && $date_to!="")
	{
		if($db_type==0)
		{
			$date_form=change_date_format($date_form,'yyyy-mm-dd', "-");
			$date_to=change_date_format($date_to,'yyyy-mm-dd', "-");
		}
		else
		{
			$date_form=change_date_format($date_form,'','',1);
			$date_to=change_date_format($date_to,'','',1);
		}
	}
	else
	{
		if($db_type==0){ $year_cond=" and year(a.insert_date)=$cbo_year ";}
		else if($db_type==2){ $year_cond=" and to_char(a.insert_date,'YYYY')='$cbo_year' ";}
	}
	
	
	
	//echo $date_form."==".$date_to;die;
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$user_name_arr = return_library_array("select id, user_full_name from user_passwd","id","user_full_name");
	
	$variable_set_invent = sql_select("select category,over_rcv_status,over_rcv_percent,over_rcv_payment from variable_inv_ile_standard where company_name=$company_id and variable_list=23 and category =4 order by id");
	$over_receive_limit = !empty($variable_set_invent) ? $variable_set_invent[0][csf('over_rcv_percent')] : 0;
	
	$previous_rcv_sql=" SELECT a.PI_WO_BATCH_NO, sum(a.ORDER_QNTY) as ORDER_QNTY from inv_transaction a where a.status_active=1 and a.is_deleted=0 and a.transaction_type=1 and a.item_category=4 and a.PI_WO_BATCH_NO>0 and a.company_id=$company_id and a.receive_basis=$recieve_basis group by a.PI_WO_BATCH_NO";
	//echo $previous_rcv_sql;die;
	$previous_rcv_sql_result=sql_select($previous_rcv_sql);
	$pi_receive_data=array();
	foreach($previous_rcv_sql_result as $val)
	{
		$pi_receive_data[$val["PI_WO_BATCH_NO"]]=$val["ORDER_QNTY"];
	}

	$trims_rec_return="SELECT a.BOOKING_NO, sum(a.issue_qnty) as RETURN_QTY FROM inv_trims_issue_dtls a, inv_transaction b where a.trans_id=b.id and b.TRANSACTION_TYPE=3 and b.company_id=$company_id and a.status_active=1 and b.status_active=1 group by a.booking_no";
	$rcv_return_result=sql_select($trims_rec_return);
    $receive_return_arr=array();
	foreach($rcv_return_result as $val)
	{
		$receive_return_arr[$val["BOOKING_NO"]]=$val["RETURN_QTY"];
	}
	
	if($recieve_basis==1)
	{
		$search_field_cond="";
		if(trim($wo_num)!="")
		{
			$search_field_cond.=" and b.work_order_no like '%$wo_num'";
		}
		
		if(trim($pi_num)!="")
		{
			$search_field_cond.=" and a.pi_number like '$pi_num'";
		}
		
		if($date_form!="" && $date_to!="")
		{
			$search_field_cond.=" and a.pi_date between '$date_form' and '$date_to'";
		}
		
		if($cbo_currency_id>0) $search_field_cond.=" and a.currency_id=$cbo_currency_id";
		if($cbo_source>0) $search_field_cond.=" and a.source=$cbo_source";
		if($cbo_supplier_name>0) $search_field_cond.=" and a.supplier_id=$cbo_supplier_name";
		
		$btbLcArr=array();
		$lc_data=sql_select("select a.pi_id, b.id, b.lc_number from com_btb_lc_pi a, com_btb_lc_master_details b where a.status_active=1 and a.is_deleted=0 and a.com_btb_lc_master_details_id=b.id");
		foreach($lc_data as $row)
		{
			$btbLcArr[$row[csf('pi_id')]]=$row[csf('id')]."**".$row[csf('lc_number')];
		}
		
		$approval_status_cond="";
		if($db_type==0)
		{ 
			$approval_status="select approval_need, allow_partial from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$company_id' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format(date('d-m-Y'),'yyyy-mm-dd')."' and company_id='$company_id')) and page_id=18 and status_active=1 and is_deleted=0";
		}
		else
		{
			$approval_status="select approval_need, allow_partial from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$company_id' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format(date('d-m-Y'), "", "",1)."' and company_id='$company_id')) and page_id=18 and status_active=1 and is_deleted=0";
		}
		$approval_status=sql_select($approval_status);
		if($approval_status[0][csf('approval_need')]==1)
		{
			if($approval_status[0][csf('allow_partial')]==1)
			{
				$approval_status_cond= " and a.approved <> 0";
			}
			else
			{
				$approval_status_cond= " and a.approved = 1";
			}
		}
		
		$sql = "SELECT a.id, a.pi_number, a.supplier_id, a.pi_date, a.last_shipment_date, a.pi_basis_id, a.internal_file_no, a.currency_id, a.source, a.inserted_by, sum(b.quantity) as quantity 
		from com_pi_master_details a, com_pi_item_details b  
		where a.id=b.pi_id and a.item_category_id=4 and a.status_active=1 and a.is_deleted=0 and a.importer_id=$company_id and a.goods_rcv_status<>1 and b.work_order_dtls_id>0 $search_field_cond $approval_status_cond $year_cond
		group by a.id, a.pi_number, a.supplier_id, a.pi_date, a.last_shipment_date, a.pi_basis_id, a.internal_file_no, a.currency_id, a.source,a.inserted_by "; 
		//echo $sql;
		$result = sql_select($sql);
		?>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1090" class="rpt_table">
			<thead>
				<tr>
					<th colspan="10"><?php echo create_drop_down("cbo_string_search_type", 130, $string_search_type, '', 1, "-- Searching Type --",4); ?></th>
				</tr>
				<tr>
					<th width="50">SL</th>
					<th width="135">PI No</th>
					<th width="80">PI Date</th>
					<th width="110">PI Basis</th>               
					<th width="200">Supplier</th>
					<th width="100">Last Shipment Date</th>
					<th width="100">Internal File No</th>
					<th width="80">Currency</th>
					<th width="60">Source</th>
					<th>Insert User</th>
				</tr>
			</thead>
		</table>
		<div style="width:1110px; max-height:280px; overflow-y:scroll" id="list_container_batch" align="left">	 
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1090" class="rpt_table" id="tbl_list_search">  
			<?
				$i=1;
				foreach ($result as $row)
				{  
					$lc_data=explode("**",$btbLcArr[$row[csf('id')]]);
					$lc_id=$lc_data[0];
					$lc_no=$lc_data[1];
					$pi_wo_qnty=$row[csf('quantity')];
					$over_receive_limit_qnty = ($over_receive_limit>0)?($over_receive_limit / 100) * $pi_wo_qnty:0;
					$allow_total_qnty = $pi_wo_qnty + $over_receive_limit_qnty;
					$prev_rcv_qnty=$pi_receive_data[$row[csf('id')]];
					$allowed_remain_qnty=$allow_total_qnty-$prev_rcv_qnty;
					
					$data=$row[csf('supplier_id')]."**".$row[csf('currency_id')]."**".$row[csf('source')]."**".$lc_no."**".$lc_id."**2"; 
					if($allowed_remain_qnty>0)
					{
						if ($i%2==0)  
						$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $row[csf('id')]; ?>,'<? echo $row[csf('pi_number')]; ?>','0','<? echo $data; ?>','<? echo $recieve_basis; ?>','');"> 
							<td width="50" align="center"><? echo $i; ?></td>
							<td width="135"><p><? echo $row[csf('pi_number')]; ?></p></td>
							<td width="80" align="center"><? echo change_date_format($row[csf('pi_date')]); ?></td>  
							<td width="110"><? echo $pi_basis[$row[csf('pi_basis_id')]]; ?></td>             
							<td width="200"><p><? echo $supplier_arr[$row[csf('supplier_id')]]; ?>&nbsp;</p></td>
							<td width="100" align="center"><? echo change_date_format($row[csf('last_shipment_date')]); ?>&nbsp;</td>
							<td width="100"><p><? echo $row[csf('internal_file_no')]; ?></p></td>
							<td width="80"><p><? echo $currency[$row[csf('currency_id')]]; ?></p></td>
							<td width="60"><p><? echo $source[$row[csf('source')]]; ?></p></td>
							<td ><p><? echo $user_name_arr[$row[csf('inserted_by')]]; ?></p></td>
						</tr>
						<?
						$i++;
					}
				}
				?>
			</table>
		</div>
		<?	
	}
	else if($recieve_basis==2)
	{
		//$search_string="%".trim($wo_num);
		
		if($db_type==0)
		{ 
			$approval_status="select page_id, approval_need, allow_partial from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$company_id' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format(date('d-m-Y'),'yyyy-mm-dd')."' and company_id='$company_id')) and page_id in(9,10,50) and status_active=1 and is_deleted=0";
		}
		else
		{
			$approval_status="select page_id, approval_need, allow_partial from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$company_id' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format(date('d-m-Y'), "", "",1)."' and company_id='$company_id')) and page_id in(9,10,50) and status_active=1 and is_deleted=0";
		}
		//echo $approval_status;die;
		$approval_status=sql_select($approval_status);
		$approval_status_cond_main=$approval_status_cond_short="";
		//echo $approval_status_cond_main.test;
		foreach($approval_status as $row)
		{
			if( $row[csf("page_id")]==9 && $row[csf("approval_need")]==1)
			{
				if($row[csf("allow_partial")]==1) $approval_status_cond_main=" and a.is_approved in(1,3)";
				else $approval_status_cond_main=" and a.is_approved=1";
			}
			else if($row[csf("page_id")]==10 && $row[csf("approval_need")]==1)
			{
				if($row[csf("allow_partial")]==1) $approval_status_cond_short=" and a.is_approved in(1,3)";
				else $approval_status_cond_short=" and a.is_approved=1";
				
			}
			else if( $row[csf("page_id")]==50 && $row[csf("approval_need")]==1)
			{
				if($row[csf("allow_partial")]==1) $approval_status_cond_main=" and a.is_approved in(1,3)";
				else $approval_status_cond_edtion=" and a.is_approved=1";
			}
		}
		//echo $approval_status_cond_main.test2;die;
		$po_arr=array();
		$po_data=sql_select("select b.id, b.po_number, b.pub_shipment_date, b.po_quantity, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs from wo_po_details_master a, wo_po_break_down b where a.id=b.job_id");	
		foreach($po_data as $row)
		{
			$po_arr[$row[csf('id')]]=$row[csf('po_number')]."**".$row[csf('pub_shipment_date')]."**".$row[csf('po_quantity')]."**".$row[csf('po_qnty_in_pcs')];
		}
		$search_field_cond="";
		$search_field_cond_sample="";
		if(trim($wo_num)!="")
		{
			$search_field_cond="and a.booking_no like '%$wo_num'";
			$search_field_cond_sample="and s.booking_no like '%$wo_num'";
		}
		
		if($date_form!="" && $date_to!="")
		{
			$search_field_cond.=" and a.booking_date between '$date_form' and '$date_to'";
			$search_field_cond_sample.=" and s.booking_date between '$date_form' and '$date_to'";
		}
		
		if($cbo_currency_id>0) $search_field_cond.=" and a.currency_id=$cbo_currency_id";
		if($cbo_source>0) $search_field_cond.=" and a.source=$cbo_source";
		if($cbo_supplier_name>0) $search_field_cond.=" and a.supplier_id=$cbo_supplier_name";
		if($cbo_buyer>0) $search_field_cond.=" and a.buyer_id=$cbo_buyer";
		if($pay_mode>0) $search_field_cond.=" and a.pay_mode=$pay_mode";
		
		if($db_type==0)
		{
			$sql = "SELECT a.id, a.buyer_id, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.delivery_date, a.currency_id, a.source, a.supplier_id, a.booking_type, a.is_short, a.pay_mode, a.fabric_source, group_concat(distinct(b.po_break_down_id)) as po_id, group_concat(distinct(b.job_no)) as job_no, YEAR(a.insert_date) as year, 0 as type ,a.inserted_by 
			from wo_booking_mst a, wo_booking_dtls b 
			where a.booking_no=b.booking_no and a.pay_mode<>2 and a.company_id=$company_id and a.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $search_field_cond $year_cond $approval_status_cond_main
			group by a.id, a.buyer_id, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.delivery_date, a.currency_id, a.source, a.supplier_id, a.booking_type, a.is_short, a.pay_mode, a.fabric_source,a.inserted_by 
			order by a.booking_date desc";
		}
		else
		{
			$po_cond="rtrim(xmlagg(xmlelement(e,b.po_break_down_id,',').extract('//text()') order by b.id).GetClobVal(),',') as po_id";
			$job_cond="rtrim(xmlagg(xmlelement(e,b.job_no,',').extract('//text()') order by b.id).GetClobVal(),',') as job_no";

			/*$sql = "select a.id, a.buyer_id, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.delivery_date, a.currency_id, a.source, a.supplier_id, a.booking_type, a.is_short, a.pay_mode, a.fabric_source, LISTAGG(cast(b.po_break_down_id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY b.po_break_down_id) as po_id, LISTAGG(cast(b.job_no as varchar2(4000)), ',') WITHIN GROUP (ORDER BY b.job_no) as job_no, to_char(a.insert_date,'YYYY') as year, 0 as type 
			from wo_booking_mst a, wo_booking_dtls b 
			where a.booking_no=b.booking_no and a.pay_mode<>2 and a.company_id=$company_id and a.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $search_field_cond 
			group by a.id, a.buyer_id, a.booking_no, a.booking_date, a.delivery_date, a.currency_id, a.source, a.supplier_id, a.booking_no_prefix_num, a.insert_date, a.booking_type, a.is_short, a.pay_mode, a.fabric_source";*/


			$sql = "SELECT a.id, a.buyer_id, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.delivery_date, a.currency_id, a.source, a.supplier_id, a.booking_type, a.is_short, a.pay_mode, a.fabric_source, $po_cond ,$job_cond ,to_char(a.insert_date,'YYYY') as year, 0 as type, a.inserted_by, sum(b.wo_qnty) as quantity 
			from wo_booking_mst a, wo_booking_dtls b 
			where a.booking_no=b.booking_no and a.pay_mode<>2 and a.company_id=$company_id and a.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_type in (2,5) and a.is_short=2 $search_field_cond $year_cond $approval_status_cond_main
			group by a.id, a.buyer_id, a.booking_no, a.booking_date, a.delivery_date, a.currency_id, a.source, a.supplier_id, a.booking_no_prefix_num, a.insert_date, a.booking_type, a.is_short, a.pay_mode, a.fabric_source,a.inserted_by
			union all
			SELECT a.id, a.buyer_id, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.delivery_date, a.currency_id, a.source, a.supplier_id, a.booking_type, a.is_short, a.pay_mode, a.fabric_source, $po_cond ,$job_cond ,to_char(a.insert_date,'YYYY') as year, 0 as type, a.inserted_by, sum(b.wo_qnty) as quantity 
			from wo_booking_mst a, wo_booking_dtls b 
			where a.booking_no=b.booking_no and a.pay_mode<>2 and a.company_id=$company_id and a.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_type in (2,5) and a.is_short=1 $search_field_cond $year_cond $approval_status_cond_short
			group by a.id, a.buyer_id, a.booking_no, a.booking_date, a.delivery_date, a.currency_id, a.source, a.supplier_id, a.booking_no_prefix_num, a.insert_date, a.booking_type, a.is_short, a.pay_mode, a.fabric_source,a.inserted_by
			union all
			SELECT a.id, a.buyer_id, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.delivery_date, a.currency_id, a.source, a.supplier_id, a.booking_type, a.is_short, a.pay_mode, a.fabric_source, $po_cond ,$job_cond ,to_char(a.insert_date,'YYYY') as year, 0 as type, a.inserted_by, sum(b.wo_qnty) as quantity 
			from wo_booking_mst a, wo_booking_dtls b 
			where a.booking_no=b.booking_no and a.pay_mode<>2 and a.company_id=$company_id and a.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_type in (8) $search_field_cond $year_cond $approval_status_cond_edtion
			group by a.id, a.buyer_id, a.booking_no, a.booking_date, a.delivery_date, a.currency_id, a.source, a.supplier_id, a.booking_no_prefix_num, a.insert_date, a.booking_type, a.is_short, a.pay_mode, a.fabric_source,a.inserted_by 
			order by booking_date desc";
			//echo $sql;
		}
		//echo $sql;//die;

		$buyer_arr = return_library_array("SELECT buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buy.buyer_name",'id','buyer_name');

		$result = sql_select($sql);
		$mst_id_arr=array();
		foreach ($result as $row) {
			$mst_id_arr[$row[csf('id')]] = $row[csf('id')];
		}
		$mst_id_arr = array_unique($mst_id_arr);
		$mst_id_con=where_con_using_array($mst_id_arr,0,"d.id");

		$sql_style ="select d.id, a.buyer_name, a.style_ref_no, b.po_number, b.id as po_id, b.grouping as internal_ref_no from wo_po_details_master a, wo_po_break_down b, wo_booking_dtls c, wo_booking_mst d where a.id=b.job_id and b.id=c.po_break_down_id and c.booking_no=d.booking_no and d.company_id=$company_id $mst_id_con and d.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 ";
	 		//echo $sql_style;
 		$result_dtls = sql_select($sql_style); 
 		$booking_dtls_dataArr=array();
 		foreach($result_dtls as $val)
		{
			if ($val[csf('internal_ref_no')]!=""){
				$booking_dtls_dataArr[$val[csf('id')]]['internal_ref_no'].=$val[csf('internal_ref_no')].",";
			}			
			$booking_dtls_dataArr[$val['ID']]['style_ref_no'].=$val['STYLE_REF_NO'].",";
		}

		?>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1280" class="rpt_table">
			<thead>
				<tr>
					<th colspan="17"><?php echo create_drop_down("cbo_string_search_type", 130, $string_search_type, '', 1, "-- Searching Type --",4); ?></th>
				</tr>
				<tr>
					<th width="30">SL</th>
					<th width="60">Booking No</th>
					<th width="100">Buyer</th>
					<th width="40">Year</th>
					<th width="70">Type</th>
					<th width="75">Booking Date</th>               
					<th width="100">Supplier</th>
					<th width="75">Delivary date</th>
					<th width="45">Source</th>
					<th width="45">Currency</th>
					<th width="90">Job No</th>
					<th width="100">Style Ref.</th>
					<th width="100">Internal Ref.</th>
					<th width="60">Order Qnty</th>
					<th width="75">Shipment Date</th>
					<th width="100">Order No</th>
					<th>Insert User</th>
				</tr>
			</thead>
		</table>
		<div style="width:1298px; max-height:280px; overflow-y:scroll" id="list_container_batch" align="left">	 
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1280" class="rpt_table" id="tbl_list_search">  
			<?
				$i=1;
				foreach ($result as $row)
				{  
					$pi_wo_qnty=$row[csf('quantity')];
					$over_receive_limit_qnty = ($over_receive_limit>0)?($over_receive_limit / 100) * $pi_wo_qnty:0;
					$allow_total_qnty = $pi_wo_qnty + $over_receive_limit_qnty;
					$prev_rcv_qnty=$pi_receive_data[$row[csf('id')]];
				    $rec_return_qty= $receive_return_arr[$row["BOOKING_NO"]];
					$allowed_remain_qnty=$allow_total_qnty-$prev_rcv_qnty+$rec_return_qty;
					if($allowed_remain_qnty>0)
					{
						if ($i%2==0)  
						$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
						
						$booking_type='';	
						if($row[csf('booking_type')]==0) 
						{
							$booking_type='Sample Without Order';
						}
						else if($row[csf('booking_type')]==5) 
						{
							$booking_type='Sample';
						}
						else
						{
							if($row[csf('is_short')]==1) $booking_type='Short'; else $booking_type='Main';
						}
						
						$po_qnty_in_pcs=''; $po_no=''; $min_shipment_date='';
						if($db_type==2) $po_ids = $row[csf('po_id')]->load();
						if($db_type==2) $job_nos = $row[csf('job_no')]->load();
	
						if($po_ids!="" && $row[csf('type')]==0)
						{
							$po_id=array_unique(explode(",",$po_ids));
							foreach ($po_id as $id)
							{
								$po_data=explode("**",$po_arr[$id]);
								$po_number=$po_data[0];
								$pub_shipment_date=$po_data[1];
								$po_qnty=$po_data[2];
								$poQntyPcs=$po_data[3];
								
								if($po_no=="") $po_no=$po_number; else $po_no.=",".$po_number;
								
								if($min_shipment_date=='')
								{
									$min_shipment_date=$pub_shipment_date;
								}
								else
								{
									if($pub_shipment_date<$min_shipment_date) $min_shipment_date=$pub_shipment_date; else $min_shipment_date=$min_shipment_date;
								}
								
								$po_qnty_in_pcs+=$poQntyPcs;
							}
							$min_shipment_date=change_date_format($min_shipment_date);
						}
						else
						{
							$po_qnty_in_pcs='&nbsp;'; $po_no='&nbsp;'; $min_shipment_date='&nbsp;';

						}
						
						$data=$row[csf('supplier_id')]."**".$row[csf('currency_id')]."**".$row[csf('source')]."******".$row[csf('pay_mode')]."**".$row[csf('fabric_source')];
						if($row[csf('pay_mode')]==3 || $row[csf('pay_mode')]==5) $sup_name=$company_library[$row[csf('supplier_id')]]; else $sup_name=$supplier_arr[$row[csf('supplier_id')]];
	
						$style_ref=implode(", ",array_unique(explode(",",chop($booking_dtls_dataArr[$row[csf('id')]]['style_ref_no'],","))));
						$internal_ref=implode(",",array_unique(explode(",",chop($booking_dtls_dataArr[$row[csf('id')]]['internal_ref_no'],","))));
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $row[csf('id')]; ?>,'<? echo $row[csf('booking_no')]; ?>','<? echo $row[csf('type')]; ?>','<? echo $data; ?>','<? echo $recieve_basis; ?>','<? echo $row[csf('booking_type')]; ?>');">
							<td width="30" align="center"><? echo $i; ?></td>
							<td width="60" align="center"><p><? echo $row[csf('booking_no_prefix_num')]; ?></p></td>
							<td width="100" align="center"> <p> <? echo $buyer_arr[$row[csf('buyer_id')]]; ?> </p> </td>
							<td width="40" align="center"><? echo $row[csf('year')]; ?></td>
							<td width="70" align="center"><p><? echo $booking_type; ?></p></td>
							<td width="75" align="center"><? echo change_date_format($row[csf('booking_date')]); ?>&nbsp;</td>               
							<td width="100"><p><? echo $sup_name; ?>&nbsp;</p></td>
							<td width="75" align="center"><? echo change_date_format($row[csf('delivery_date')]); ?>&nbsp;</td>
							<td width="45"><p><? echo $source[$row[csf('source')]]; ?></p></td>
							<td width="45" align="center"><p><? echo $currency[$row[csf('currency_id')]]; ?></p></td>
							<td width="90"><p><? echo implode(", ",array_unique(explode(",",$job_nos))); ?>&nbsp;</p></td>
							<td width="100"><p><? echo $style_ref; ?>&nbsp;</p></td>
							<td width="100"><p><? echo $internal_ref; ?>&nbsp;</p></td>
							<td width="60" align="right"><? echo $po_qnty_in_pcs; ?></td>
							<td width="75" align="center"><? echo $min_shipment_date; ?></td>
							<td width="100"><p><? echo $po_no; ?></p></td>
							<td ><p><? echo $user_name_arr[$row[csf('inserted_by')]]; ?></p></td>
						</tr>
						<?
						$i++;
					}
				}
				?>
			</table>
		</div>
		<?	
	}
	exit();
}


if( $action == 'mrr_details' ) 
{
	//echo $data;die;
	
	?>
    <tbody>
        <tr id="row_1" align="center">
            <td width="100" id="po_no_1"></td>
            <td width="70" id="ship_date_1"></td>
            <td width="50" id="job_no_1"></td>
            <td width="70" id="ref_no_1"></td>
            <td width="70" id="article_no_1"></td>
            <td width="100" id="style_no_1"></td>
            <td width="80" id="item_group_1"></td>
            <td width="120" id="item_descrip_1"></td>
            <td width="70" id="brand_supp_1"></td>
            <td width="70" id="tdgmtscolorid_1"><input type="hidden" name="gmtscolorid[]" id="gmtscolorid_1" value="" readonly></td>
            <td width="70" id="gmt_size_1"></td>
            <td width="70" id="item_color_1"></td>
            <td width="60" id="item_size_1"></td>
            <td width="50" id="uom_1"></td>
            <td width="50" align="center" id="floor_td_to" class="floor_td_to"><p>
            <? 
            $i=1;
            $argument = "'".$i.'_0'."'";
            echo create_drop_down( "cbo_floor_to_$i", 50,$blank_array,"", 1, "--Select--", 0, "fn_load_room(this.value, $i); copy_all($argument); reset_room_rack_shelf($i,'cbo_floor_to');",0,"","","","","","","cbo_floor_to[]" ,"onchange_void"); ?>
            </p></td>
            <td width="50" align="center" id="room_td_to"><p>
            <? $argument = "'".$i.'_1'."'";
            echo create_drop_down( "cbo_room_to_$i", 50,$blank_array,"", 1, "--Select--", 0, "fn_load_rack(this.value, $i); copy_all($argument); reset_room_rack_shelf($i,'cbo_room_to');",0,"","","","","","","cbo_room_to[]","onchange_void" ); ?>
            </p>
            </td>
            <td width="50" align="center" id="rack_td_to"><p>
            <? $argument = "'".$i.'_2'."'";
            echo create_drop_down( "txt_rack_to_$i", 50,$blank_array,"", 1, "--Select--", 0, "fn_load_shelf(this.value, $i); copy_all($argument); reset_room_rack_shelf($i,'txt_rack_to');",0,"","","","","","","txt_rack_to[]","onchange_void" ); ?>
            </p></td>
            <td width="50" align="center" id="shelf_td_to"><p>
            <? $argument = "'".$i.'_3'."'";
            echo create_drop_down( "txt_shelf_to_$i", 50,$blank_array,"", 1, "--Select--", 0, "fn_load_bin(this.value, $i); copy_all($argument); reset_room_rack_shelf($i,'txt_shelf_to');",0,"","","","","","","txt_shelf_to[]","onchange_void" ); ?>
            </p></td>
            <td width="50" align="center" id="bin_td_to"><p>
            <? $argument = "'".$i.'_4'."'"; 
            echo create_drop_down( "txt_bin_to_$i", 50,$blank_array,"", 1, "--Select--", 0, "copy_all($argument);",0,"","","","","","","txt_bin_to[]","onchange_void" ); ?>
            </p></td>
            <td width="60" id="WOPIQnty_1"></td>
            <td width="70" id="tdreceiveqnty_1">
                <input type="text" name="receiveqnty[]" id="receiveqnty_1" class="text_boxes_numeric" style="width:50px;" value="" onBlur="calculate(1);"/>
            </td>
            <td width="70" id="tdrate_1" class="rate_td"></td>
            <td width="70" id="tdile_1"></td>
            <td width="70" id="tdamount_1" class="amount_td"></td>
            <td width="70" id="tdrejectrecvqnty_1"></td>
            <td width="70" id="tdbookcurrency_1"></td>
            <td width="70" id="PrevRcvQnty_1"></td>
            <td width="70" id="tdblqty_1"></td>
            <td id="tdcbopaymentoverrecv_1">
            <? 
            echo create_drop_down( "cbopaymentoverrecv_1",50, $payment_yes_no,'', 0, '',1,"",0,"","","","","","","cbopaymentoverrecv[]","common_color_1"); 
            ?>
            <input type="hidden" name="updatedtlsid[]" id="updatedtlsid_1" value="" readonly>
            <input type="hidden" name="updatetransid[]" id="updatetransid_1" value="" readonly>
            <input type="hidden" name="previousprodid[]" id="previousprodid_1" value="" readonly>
            </td>
        </tr>
    </tbody>
    <?
	exit();
}

if($action=="show_ile_load_uom")

{
	$data=explode("_",$data);
	
	$uom=$trim_group_arr[$data[0]]['uom'];
	$company = $data[1];
	$source = $data[2];
	$rate = $data[3];
	
	$ile=return_field_value("standard","variable_inv_ile_standard","source='$source' and company_name='$company' and category=4 and status_active=1 and is_deleted=0");
	echo "document.getElementById('cbo_uom').value 	= '".$uom."';\n";
	// NOTE :- ILE=standard, ILE% = standard/100*rate
	
	if($ile<0 || $ile=='')
	{
		$ile_percentage=0; $ile=0;
	}
	else
	{
		$ile_percentage = number_format(($ile/100)*$rate,$dec_place[3],".","");
	}
	echo "document.getElementById('ile_td').innerHTML 	= 'ILE% ".$ile."';\n";
	echo "document.getElementById('txt_ile').value 	= '".$ile_percentage."';\n";
	
	exit();	
}

if( $action == 'show_fabric_desc_listview' ) 
{
	$data=explode("**",$data);
	//print_r($data);
	$bookingNo_piId=$data[0];
	$receive_basis=$data[1];
	$booking_without_order=$data[2];
	$company=$data[3];
	$source=$data[4];
	$exchange_rate=$data[5];
	$booking_type=$data[6];
	$wo_pi_id=$data[7];
	
	$sql_ile=sql_select("select item_group, standard from variable_inv_ile_standard where status_active=1 and is_deleted=0 and company_name=$company and source='$source' and category=4");
	$ile_data=$sql_ile[0][csf("standard")];
	//$ile_data=array();
//	foreach($sql_ile as $row)
//	{
//		$ile_data[$row[csf("item_group")]]=$row[csf("standard")];
//	}
	
	$rcvRtn_qty_sql = "SELECT b.PROD_ID, b.PO_BREAKDOWN_ID, a.ITEM_GROUP_ID, a.ITEM_DESCRIPTION, a.COLOR, a.ITEM_COLOR, a.GMTS_SIZE, a.ITEM_SIZE, a.BRAND_SUPPLIER, b.QUANTITY 
	from product_details_master a, inv_transaction t, order_wise_pro_details b
	where a.id=b.prod_id and a.id=t.prod_id and t.id=b.trans_id and a.item_category_id=4 and b.entry_form in(49) and t.transaction_type=3 and t.pi_wo_batch_no=$wo_pi_id and t.receive_basis=$receive_basis and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and t.status_active=1 and t.is_deleted=0";
	$rcvRtn_qty_sql_result=sql_select($rcvRtn_qty_sql);
	$prev_return_data=array();
	foreach($rcvRtn_qty_sql_result as $row)
	{
		$prev_return_data[$row["PO_BREAKDOWN_ID"]][$row["ITEM_GROUP_ID"]][$row["ITEM_DESCRIPTION"]][$row["BRAND_SUPPLIER"]][$row["COLOR"]][$row["ITEM_COLOR"]][$row["GMTS_SIZE"]][$row["ITEM_SIZE"]]+=$row["QUANTITY"];
	}
	//echo "<pre>";print_r($prev_return_data);die;
	
	$item_name_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
	if($receive_basis==1)
	{
		$prev_entry="select c.prod_id, c.po_breakdown_id, sum(c.quantity) as quantity, c.order_rate, b.item_group_id, b.item_description, b.gmts_color_id as grms_color, b.item_color, b.gmts_size_id as gmts_size, b.item_size, b.item_description, b.brand_supplier   
		from inv_receive_master a, inv_trims_entry_dtls b, order_wise_pro_details c
		where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=24 and c.entry_form=24 and c.trans_type=1 and a.company_id=$company and a.receive_basis=$receive_basis and b.booking_id='$bookingNo_piId' and a.status_active=1 and b.status_active=1 and c.status_active=1 
		group by c.prod_id, c.po_breakdown_id, c.order_rate, b.item_group_id, b.item_description, b.gmts_color_id, b.item_color, b.gmts_size_id, b.item_size, b.item_description, b.brand_supplier";
		
		$prev_entry_result=sql_select($prev_entry);
		$prev_data=array();
		foreach($prev_entry_result as $row)
		{
			$prev_data[$row[csf("po_breakdown_id")]][$row[csf("item_group_id")]][$row[csf("item_description")]][$row[csf("brand_supplier")]][$row[csf("grms_color")]][$row[csf("item_color")]][$row[csf("gmts_size")]][$row[csf("item_size")]][$row[csf("order_rate")]]=$row[csf("quantity")];
		}
		
		
		
		$sql="select b.po_break_down_id, b.sensitivity, b.uom, a.item_group as trim_group, a.item_description as description, a.color_id, a.item_color, a.size_id, a.item_size, a.brand_supplier, a.booking_article_no as item_ref, a.rate as rate, sum(a.quantity) as book_qnty, sum(a.amount) as book_amount
		from com_pi_item_details a, wo_booking_dtls b
		where a.work_order_dtls_id=b.id and a.pi_id='$bookingNo_piId' and a.work_order_dtls_id>0 and a.status_active=1 and a.is_deleted=0
		group by b.po_break_down_id, b.sensitivity, b.uom, a.item_group, a.item_description, a.color_id, a.item_color, a.size_id, a.item_size, a.brand_supplier, a.rate, a.booking_article_no
		order by a.item_group, a.item_description, a.color_id, a.item_color, a.size_id, a.item_size, a.brand_supplier";// group by item_group, item_description, brand_supplier, color_id, item_color, size_id, item_size
		
		
	}
	else if($receive_basis==2)
	{
		if($booking_without_order==1)
		{
			//$table_width=480; $po_column=''; $sensitivity_column=''; $size_width="60"; $brandSup_width="60"; $rate_width="50";
			$sql = "select 0 as po_break_down_id,0 as sensitivity, trim_group, uom, fabric_description as description, gmts_color as color_id, fabric_color as item_color, gmts_size as size_id, item_size, barnd_sup_ref as brand_supplier, rate, trim_qty as book_qnty, sum(amount) as book_amount 
			from wo_non_ord_samp_booking_dtls where booking_no='$bookingNo_piId' and status_active=1 and is_deleted=0
			order by trim_group, uom, fabric_description, gmts_color, fabric_color, gmts_size, item_size, barnd_sup_ref";
		}
		else
		{
			
			$prev_entry="select c.prod_id, c.po_breakdown_id, sum(c.quantity) as quantity, c.order_rate, b.item_group_id, b.item_description, b.gmts_color_id as grms_color, b.item_color, b.gmts_size_id as gmts_size, b.item_size, b.item_description, b.brand_supplier   
			from inv_receive_master a, inv_trims_entry_dtls b, order_wise_pro_details c
			where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=24 and c.entry_form=24 and c.trans_type=1 and a.company_id=$company and a.receive_basis=$receive_basis and b.booking_no='$bookingNo_piId' and a.status_active=1 and b.status_active=1 and c.status_active=1 
			group by c.prod_id, c.po_breakdown_id, c.order_rate, b.item_group_id, b.item_description, b.gmts_color_id, b.item_color, b.gmts_size_id, b.item_size, b.item_description, b.brand_supplier";
			
			//echo $prev_entry;die;
			
			$prev_entry_result=sql_select($prev_entry);
			$prev_data=array();
			foreach($prev_entry_result as $row)
			{
				$prev_data[$row[csf("po_breakdown_id")]][$row[csf("item_group_id")]][str_replace(", [BS]","",$row[csf("item_description")])][$row[csf("brand_supplier")]][$row[csf("grms_color")]][$row[csf("item_color")]][$row[csf("gmts_size")]][$row[csf("item_size")]][$row[csf("order_rate")]]=$row[csf("quantity")];
			}
			
			
			
			
			//$table_width=550; $size_width="40"; $brandSup_width="50"; $rate_width="45";
			//$po_column="<th width='50'>PO No.</th><th width='50'>Style Ref.</th>"; $sensitivity_column="<th width='55'>Sensitivity</th>";
			$sql = "select b.po_break_down_id, b.sensitivity, b.trim_group, b.uom, c.description, c.color_number_id as color_id, c.item_color, c.gmts_sizes as size_id, c.item_size, c.brand_supplier, c.rate as rate, sum(c.cons) as book_qnty, sum(c.amount) as book_amount, c.item_ref 
			from wo_booking_dtls b, wo_trim_book_con_dtls c where b.id=c.wo_trim_booking_dtls_id and b.booking_no='$bookingNo_piId' and c.booking_no='$bookingNo_piId' and b.booking_type=$booking_type and c.cons>0 and b.status_active=1 and b.is_deleted=0
			group by b.po_break_down_id, b.sensitivity, b.trim_group, b.uom, c.description, c.color_number_id, c.item_color, c.gmts_sizes, c.item_size, c.brand_supplier, c.item_ref, c.rate
			order by b.trim_group, b.uom, c.description, c.color_number_id, c.item_color, c.gmts_sizes, c.item_size, c.brand_supplier";
		}
	}
	//echo $sql;
	$data_array=sql_select($sql);
    $po_id_arr = array();
    foreach ($data_array as $po_id){
        $po_id_arr[$po_id[csf("po_break_down_id")]] = $po_id[csf("po_break_down_id")];
    }

    $po_id_arr = array_chunk($po_id_arr, 900);
    $po_id_cond = "";
    foreach ($po_id_arr as $key => $val){
        if($key == 0){
            $po_id_cond .= " b.id in (".implode(',', $val).") ";
        }else{
            $po_id_cond .= " or b.id in (".implode(',', $val).") ";
        }
    }

	$po_arr=array();

	$poDataArr = sql_select("select A.STYLE_REF_NO, B.ID, B.PO_NUMBER, B.PUB_SHIPMENT_DATE, B.GROUPING, C.ARTICLE_NUMBER, C.SIZE_NUMBER_ID, C.COLOR_NUMBER_ID, a.JOB_NO_PREFIX_NUM, a.JOB_NO 
	from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and b.id = c.po_break_down_id and a.company_name=$company and ($po_id_cond)");

//    $poDataArr = sql_select("select A.STYLE_REF_NO, B.ID, B.PO_NUMBER, B.PUB_SHIPMENT_DATE, B.GROUPING from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$company");

    foreach($poDataArr as $key => $rowP)
	{
		$po_arr[$rowP["ID"]][1]=$rowP["STYLE_REF_NO"];
		$po_arr[$rowP["ID"]][2]=$rowP["PO_NUMBER"];
		$po_arr[$rowP["ID"]][3]=$rowP["PUB_SHIPMENT_DATE"];
		$po_arr[$rowP["ID"]][4]=$rowP["GROUPING"];
		$po_arr[$rowP["ID"]][5][$rowP["COLOR_NUMBER_ID"]."*".$rowP["SIZE_NUMBER_ID"]]=$rowP["ARTICLE_NUMBER"];
		$po_arr[$rowP["ID"]][6]=$rowP["JOB_NO_PREFIX_NUM"];
		$po_arr[$rowP["ID"]][7]=$rowP["JOB_NO"];
	}
	unset($poDataArr);
	
	$lib_item_group_sql = sql_select("select id, trim_uom,conversion_factor from lib_item_group");
	$lib_item_group_arrr=array();
	foreach($lib_item_group_sql as $row)
	{
		$lib_item_group_arrr[$row[csf("id")]]["trim_uom"]=$row[csf("trim_uom")];
		$lib_item_group_arrr[$row[csf("id")]]["conversion_factor"]=$row[csf("conversion_factor")];
	}
	//echo "<pre>"; print_r($po_arr);//die;
	
	$i=1;
	foreach($data_array as $row)
	{
		if ($i%2==0)$bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		//$description=str_replace("+","",$row[csf('description')]);
		$prev_rcv_qnty=$prev_data[$row[csf("po_break_down_id")]][$row[csf("trim_group")]][$row[csf('description')]][$row[csf('brand_supplier')]][$row[csf("color_id")]][$row[csf("item_color")]][$row[csf("size_id")]][$row[csf("item_size")]][$row[csf("rate")]];
		$prev_rcv_rtn_qnty=$prev_return_data[$row[csf("po_break_down_id")]][$row[csf("trim_group")]][$row[csf('description')]][$row[csf('brand_supplier')]][$row[csf("color_id")]][$row[csf("item_color")]][$row[csf("size_id")]][$row[csf("item_size")]];
		$qnty=(($row[csf('book_qnty')]+$prev_rcv_rtn_qnty)-$prev_rcv_qnty);
		//$amount=$qnty*$row[csf('rate')];
		//$amount=$row[csf('book_amount')];
		//$book_avg_rate=$row[csf('book_amount')]/$row[csf('book_qnty')];
		//$amount=$qnty*$book_avg_rate;
		$book_avg_rate=$row[csf('rate')];
		$amount=$qnty*$row[csf('rate')];
		$ile_amt=(($amount/100)*$ile_data);
		$item_key=$row[csf('trim_group')].$row[csf('description')].$row[csf('brand_supplier')].$row[csf("item_color")].$row[csf('color_id')].$row[csf("item_size")].$row[csf("size_id")].$row[csf("rate")];
		if(number_format($qnty,4,'.','')>0)
		{
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $i; ?>" align="center">
				<td width="100" style="word-break:break-all" id="po_no_<? echo $i; ?>" title="<? echo $row[csf("po_break_down_id")];?>"><? echo $po_arr[$row[csf("po_break_down_id")]][2]; ?></td>
				<td width="70" id="ship_date_<? echo $i; ?>" title="<? echo $row[csf("po_break_down_id")];?>"><? echo change_date_format($po_arr[$row[csf("po_break_down_id")]][3]); ?></td>
                <td width="50" id="job_no_<? echo $i; ?>" title="<? echo $po_arr[$row[csf("po_break_down_id")]][7];?>"><? echo $po_arr[$row[csf("po_break_down_id")]][6]; ?></td>
				<td width="70" id="ref_no_<? echo $i; ?>" title="<? echo $row[csf("po_break_down_id")];?>"><? echo $po_arr[$row[csf("po_break_down_id")]][4]; ?></td>
				<td width="70" id="article_no_<? echo $i; ?>" title="<? echo $row[csf("po_break_down_id")];?>">
                    <?
                    	echo $row[csf("item_ref")];
                       //echo $po_arr[$row[csf("po_break_down_id")]][5][$row[csf('color_id')]."*".$row[csf('size_id')]];
                    ?>
                </td>
				<td width="100" id="style_no_<? echo $i; ?>"><? echo $po_arr[$row[csf("po_break_down_id")]][1]; ?></td>
				<td width="80" id="item_group_<? echo $i; ?>"  title="<? echo $row[csf('trim_group')];?>"><? echo $item_name_arr[$row[csf("trim_group")]]; ?></td>
				<td width="120" id="item_descrip_<? echo $i; ?>" style="word-break:break-all;"><? echo $row[csf('description')]; ?></td>
				<td width="70" id="brand_supp_<? echo $i; ?>"><? echo $row[csf('brand_supplier')]; ?></td>
                <td width="70" id="tdgmtscolorid_<? echo $i; ?>"><? echo $color_arr[$row[csf('color_id')]]; ?>
                <input type="hidden" name="gmtscolorid[]" id="gmtscolorid_<? echo $i; ?>" value="<? echo $row[csf('color_id')]; ?>" readonly>
                </td>
				<td width="70" id="gmt_size_<? echo $i; ?>" title="<? echo $row[csf("size_id")];?>"><? echo $size_arr[$row[csf('size_id')]]; ?></td>
				<td width="70" id="item_color_<? echo $i; ?>"  title="<? echo $row[csf("item_color")];?>"><? echo $color_arr[$row[csf('item_color')]]; ?></td>
				<td width="60" id="item_size_<? echo $i; ?>" title="<? echo $row[csf("item_size")];?>"><? echo $row[csf('item_size')]; ?></td>
				<td width="50" id="uom_<? echo $i; ?>" title="<? echo $row[csf("uom")];?>"><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
                <td width="50" align="center" id="floor_td_to" class="floor_td_to"><p>
				<? 
                $argument = "'".$i.'_0'."'";
                echo create_drop_down( "cbo_floor_to_$i", 50,$blank_array,"", 1, "--Select--", 0, "fn_load_room(this.value, $i); copy_all($argument); reset_room_rack_shelf($i,'cbo_floor_to');",0,"","","","","","","cbo_floor_to[]" ,"onchange_void"); ?>
                </p></td>
                <td width="50" align="center" id="room_td_to"><p>
                <? $argument = "'".$i.'_1'."'";
                echo create_drop_down( "cbo_room_to_$i", 50,$blank_array,"", 1, "--Select--", 0, "fn_load_rack(this.value, $i); copy_all($argument); reset_room_rack_shelf($i,'cbo_room_to');",0,"","","","","","","cbo_room_to[]","onchange_void" ); ?>
                </p>
                </td>
                <td width="50" align="center" id="rack_td_to"><p>
                <? $argument = "'".$i.'_2'."'";
                echo create_drop_down( "txt_rack_to_$i", 50,$blank_array,"", 1, "--Select--", 0, "fn_load_shelf(this.value, $i); copy_all($argument); reset_room_rack_shelf($i,'txt_rack_to');",0,"","","","","","","txt_rack_to[]","onchange_void" ); ?>
                </p></td>
                <td width="50" align="center" id="shelf_td_to"><p>
                <? $argument = "'".$i.'_3'."'";
                echo create_drop_down( "txt_shelf_to_$i", 50,$blank_array,"", 1, "--Select--", 0, "fn_load_bin(this.value, $i); copy_all($argument); reset_room_rack_shelf($i,'txt_shelf_to');",0,"","","","","","","txt_shelf_to[]","onchange_void" ); ?>
                </p></td>
                <td width="50" align="center" id="txt_bin_to"><p>
                <? $argument = "'".$i.'_4'."'"; 
                echo create_drop_down( "txt_bin_to_$i", 50,$blank_array,"", 1, "--Select--", 0, "copy_all($argument);",0,"","","","","","","txt_bin_to[]","onchange_void" ); ?>
                </p></td>
				<td width="60" id="WOPIQnty_<? echo $i; ?>" title="<? echo $row[csf('book_qnty')]."=".$prev_rcv_rtn_qnty."**".$row[csf("po_break_down_id")]."=".$row[csf("trim_group")]."=".$row[csf('description')]."=".$row[csf('brand_supplier')]."=".$row[csf("color_id")]."=".$row[csf("item_color")]."=".$row[csf("size_id")]."=".$row[csf("item_size")];?>" align="right"><? echo number_format($row[csf('book_qnty')],4,'.',''); ?></td>
				<td width="70" id="tdreceiveqnty_<? echo $i; ?>">
					<input type="text" name="receiveqnty[]" id="receiveqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:50px;" value="<? echo number_format($qnty,4,'.',''); ?>" placeholder="<? echo number_format($qnty,4,'.',''); ?>" onBlur="calculate(<? echo $i; ?>);"/>
				</td>
				<td width="70" align="right" class="rate_td" id="tdrate_<? echo $i; ?>" title="<?= $book_avg_rate;?>"><? echo number_format($book_avg_rate,6,'.',''); ?></td>
				<td width="70" align="right" id="tdile_<? echo $i; ?>" title="<?= $ile_amt;?>"><? echo $ile_amt; ?></td>
				<td width="70" align="right" class="amount_td" id="tdamount_<? echo $i; ?>" title="<?= $amount;?>"><? echo number_format($amount,4,'.','') ; ?></td>
				<td width="70" align="right" id="tdrejectrecvqnty_<? echo $i; ?>"></td>
				<td width="70" align="right" id="tdbookcurrency_<? echo $i; ?>" title="<?= ($amount*$exchange_rate);?>"><? echo number_format(($amount*$exchange_rate),2,'.',''); ?></td>
				<td width="70" align="right" id="PrevRcvQnty_<? echo $i; ?>" title="<? echo $prev_rcv_qnty;?>"><? echo number_format($prev_rcv_qnty,4,'.',''); ?></td>
				<td width="70" align="right" id="tdblqty_<? echo $i; ?>" title="<? echo $qnty;?>" blamt="<? echo number_format($amount,4,'.','');?>"><? echo number_format($qnty,4,'.',''); ?></td>
				<td id="tdcbopaymentoverrecv_<? echo $i; ?>">
				<? 
				echo create_drop_down( "cbopaymentoverrecv_".$i,50, $payment_yes_no,'', 0, '',1,"fn_pament_over('$item_key',this.value,$i)",0,"","","","","","","cbopaymentoverrecv[]","common_color_".$item_key); 
				?>
				<input type="hidden" name="updatedtlsid[]" id="updatedtlsid_<? echo $i; ?>" value="" readonly>
				<input type="hidden" name="updatetransid[]" id="updatetransid_<? echo $i; ?>" value="" readonly>
				<input type="hidden" name="previousprodid[]" id="previousprodid_<? echo $i; ?>" value="" readonly>
				</td>
			</tr>
			<?
			$i++;
		}
    }
	exit();
}

if( $action == 'show_fabric_desc_listview_update' ) 
{
	$data=explode("**",$data);
	//print_r($data);
	$bookingNo_piId=$data[0];
	$receive_basis=$data[1];
	$booking_without_order=$data[2];
	$company=$data[3];
	$source=$data[4];
	$exchange_rate=$data[5];
	$mst_id=$data[6];
	$book_id=$data[7];
	$store_id=$data[8];
	$store_cond="";
	if($store_id) $store_cond=" and b.store_id=$store_id";
	
	$lib_room_rack_shelf_sql = "select b.company_id,b.location_id,b.store_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,
	a.floor_room_rack_name floor_name, c.floor_room_rack_name room_name, d.floor_room_rack_name rack_name,
	e.floor_room_rack_name shelf_name, f.floor_room_rack_name bin_name
	from lib_floor_room_rack_dtls b 
	left join lib_floor_room_rack_mst a on b.floor_id=a.floor_room_rack_id and a.status_active=1 and a.is_deleted=0
	left join lib_floor_room_rack_mst c on b.room_id=c.floor_room_rack_id and c.status_active=1 and c.is_deleted=0 
	left join lib_floor_room_rack_mst d on b.rack_id=d.floor_room_rack_id and d.status_active=1 and d.is_deleted=0 
	left join lib_floor_room_rack_mst e on b.shelf_id=e.floor_room_rack_id and e.status_active=1 and e.is_deleted=0 
	left join lib_floor_room_rack_mst f on b.bin_id=f.floor_room_rack_id and f.status_active=1 and f.is_deleted=0 
	where b.status_active=1 and b.is_deleted=0 and b.company_id=$company $store_cond
	order by a.floor_room_rack_name , c.floor_room_rack_name , d.floor_room_rack_name , e.floor_room_rack_name , f.floor_room_rack_name";
	// echo $lib_room_rack_shelf_sql;die;
	$lib_rrsb_arr=sql_select($lib_room_rack_shelf_sql);
	if(!empty($lib_rrsb_arr))
	{
		foreach ($lib_rrsb_arr as $room_rack_shelf_row) {
			$company  = $room_rack_shelf_row[csf("company_id")];
			$floor_id = $room_rack_shelf_row[csf("floor_id")];
			$room_id  = $room_rack_shelf_row[csf("room_id")];
			$rack_id  = $room_rack_shelf_row[csf("rack_id")];
			$shelf_id = $room_rack_shelf_row[csf("shelf_id")];
			$bin_id   = $room_rack_shelf_row[csf("bin_id")];

			if($floor_id!=""){
				$lib_floor_arr[$floor_id] = $room_rack_shelf_row[csf("floor_name")];
			}

			if($floor_id!="" && $room_id!=""){
				$lib_room_arr[$room_id] = $room_rack_shelf_row[csf("room_name")];
			}

			if($floor_id!="" && $room_id!="" && $rack_id!=""){
				$lib_rack_arr[$rack_id] = $room_rack_shelf_row[csf("rack_name")];
			}

			if($floor_id!="" && $room_id!="" && $rack_id!="" && $shelf_id!=""){
				$lib_shelf_arr[$shelf_id] = $room_rack_shelf_row[csf("shelf_name")];
			}
			if($floor_id!="" && $room_id!="" && $rack_id!="" && $shelf_id!="" && $bin_id!=""){
				$lib_bin_arr[$bin_id] = $room_rack_shelf_row[csf("bin_name")];
			}
		}
	}
	else
	{
		$lib_floor_arr[0]="";
		$lib_room_arr[0]="";
		$lib_rack_arr[0]="";
		$lib_shelf_arr[0]="";
		$lib_bin_arr[0]="";
	}
	
	$sql_ile=sql_select("select item_group, standard from variable_inv_ile_standard where status_active=1 and is_deleted=0 and company_name=$company and source='$source' and category=4");
	$ile_data=$sql_ile[0][csf("standard")];
	//$ile_data=array();
//	foreach($sql_ile as $row)
//	{
//		$ile_data[$row[csf("item_group")]]=$row[csf("standard")];
//	}
	$item_name_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
	$all_po_id="";
	$po_id_arr = array();
	if($receive_basis==1)
	{
		$prev_entry="select c.prod_id, c.po_breakdown_id, sum(c.quantity) as quantity   
		from inv_receive_master a, inv_trims_entry_dtls b, order_wise_pro_details c
		where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=24 and c.entry_form=24 and c.trans_type=1 and a.company_id=$company and a.receive_basis=$receive_basis and b.booking_id='$bookingNo_piId' and a.id<>$mst_id and a.status_active=1 and b.status_active=1 and c.status_active=1
		group by c.prod_id, c.po_breakdown_id";
		
		$prev_entry_result=sql_select($prev_entry);
		$prev_data=array();
		foreach($prev_entry_result as $row)
		{
			$prev_data[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]]=$row[csf("quantity")];
		}
		
		
		
		$sql="select b.po_break_down_id, b.sensitivity, b.uom, a.item_group as trim_group, a.item_description as description, a.color_id, a.item_color, a.size_id, a.item_size, a.brand_supplier, a.rate, a.quantity as book_qnty, a.booking_article_no
		from com_pi_item_details a, wo_booking_dtls b 
		where a.work_order_dtls_id=b.id and a.pi_id='$bookingNo_piId' and a.work_order_dtls_id>0 and a.status_active=1 and a.is_deleted=0
		order by a.item_group, a.item_description, a.color_id, a.item_color, a.size_id, a.item_size, a.brand_supplier";
		
		$sql_result=sql_select($sql);
		$booking_pi_data=array();
		foreach($sql_result as $row)
		{
			if($row[csf("po_break_down_id")]>0){
				if($row[csf("po_break_down_id")]>0) $all_po_id.=$row[csf("po_break_down_id")].",";
				$po_id_arr[$row[csf("po_break_down_id")]] = $row[csf("po_break_down_id")];
			}
			
			$trim_key=$row[csf("po_break_down_id")]."__".$row[csf("trim_group")]."__".$row[csf("description")]."__".$row[csf("color_id")]."__".$row[csf("item_color")]."__".$row[csf("size_id")]."__".$row[csf("item_size")]."__".$row[csf("brand_supplier")]."__".$row[csf("rate")];
			$booking_pi_data[$trim_key]["book_qnty"]+=$row[csf("book_qnty")];
			$booking_pi_data[$trim_key]["book_amount"]+=$row[csf("book_qnty")]*$row[csf("rate")];
			$booking_pi_data[$trim_key]["item_ref"]=$row[csf("booking_article_no")];
		}
		// group by item_group, item_description, brand_supplier, color_id, item_color, size_id, item_size
	}
	else if($receive_basis==2)
	{
		if($booking_without_order==1)
		{
			//$table_width=480; $po_column=''; $sensitivity_column=''; $size_width="60"; $brandSup_width="60"; $rate_width="50";
			$sql = "select 0 as po_break_down_id,0 as sensitivity, trim_group, uom, fabric_description as description, gmts_color as color_id, fabric_color as item_color, gmts_size as size_id, item_size, barnd_sup_ref as brand_supplier, rate, trim_qty as book_qnty 
			from wo_non_ord_samp_booking_dtls where booking_no='$bookingNo_piId' and status_active=1 and is_deleted=0
			order by trim_group, uom, fabric_description, gmts_color, fabric_color, gmts_size, item_size, barnd_sup_ref, rate, trim_qty";
		}
		else
		{
			$prev_entry="select c.prod_id, c.po_breakdown_id, sum(c.quantity) as quantity   
			from inv_receive_master a, inv_trims_entry_dtls b, order_wise_pro_details c

			where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=24 and c.entry_form=24 and c.trans_type=1 and a.company_id=$company and a.receive_basis=$receive_basis and b.booking_no='$bookingNo_piId' and a.id<>$mst_id and a.status_active=1 and b.status_active=1 and c.status_active=1
			group by c.prod_id, c.po_breakdown_id";
			
			//echo $prev_entry;die;
			
			$prev_entry_result=sql_select($prev_entry);
			$prev_data=array();
			foreach($prev_entry_result as $row)
			{
				$prev_data[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]]=$row[csf("quantity")];
			}
			
			
			//$table_width=550; $size_width="40"; $brandSup_width="50"; $rate_width="45";
			//$po_column="<th width='50'>PO No.</th><th width='50'>Style Ref.</th>"; $sensitivity_column="<th width='55'>Sensitivity</th>";
			$sql = "select b.po_break_down_id, b.sensitivity, b.trim_group, b.uom, c.description, c.color_number_id as color_id, c.item_color, c.gmts_sizes as size_id, c.item_size, c.brand_supplier, c.rate, c.cons as book_qnty, c.item_ref 
			from wo_booking_dtls b, wo_trim_book_con_dtls c where b.id=c.wo_trim_booking_dtls_id and b.booking_no='$bookingNo_piId' and c.cons>0 and b.status_active=1 and b.is_deleted=0
			order by b.trim_group, b.uom, c.description, c.color_number_id, c.item_color, c.gmts_sizes, c.item_size, c.brand_supplier, c.rate, c.cons"; 
			//echo $sql;die;
			$sql_result=sql_select($sql);
			$booking_pi_data=array();            
			foreach($sql_result as $row)
			{
				if($row[csf("po_break_down_id")]>0){
                    $all_po_id.=$row[csf("po_break_down_id")].",";
                    $po_id_arr[$row[csf("po_break_down_id")]] = $row[csf("po_break_down_id")];
                }
				$trim_key=$row[csf("po_break_down_id")]."__".$row[csf("trim_group")]."__".$row[csf("description")]."__".$row[csf("color_id")]."__".$row[csf("item_color")]."__".$row[csf("size_id")]."__".$row[csf("item_size")]."__".$row[csf("brand_supplier")]."__".$row[csf("rate")];
				$booking_pi_data[$trim_key]["book_qnty"]+=$row[csf("book_qnty")];
				$booking_pi_data[$trim_key]["book_amount"]+=$row[csf("book_qnty")]*$row[csf("rate")];
				$booking_pi_data[$trim_key]["item_ref"]=$row[csf("item_ref")];
			}
		}
	}

	$po_id_cond = '';
	if (!empty($po_id_arr))
    {       
        if($db_type==2 && count($po_id_arr)>999)
        {
            $poIds = array_keys($po_id_arr);
            $po_id_cond = ' and (';

            $poIdArr = array_chunk($poIds,999);
            foreach($poIdArr as $ids)
            {
                $ids = implode(',',$ids);
                $po_id_cond .= " b.id in($ids) or ";
            }
            
            $po_id_cond = rtrim($po_id_cond,'or ');
            $po_id_cond .= ')';
        }
        else
        {
            $poIds = implode(',',array_keys($po_id_arr));
            $po_id_cond = " and b.id in ($poIds) ";
        }
    }

    $po_arr=array();
    $poDataArr = sql_select("select A.STYLE_REF_NO, B.ID, B.PO_NUMBER, B.PUB_SHIPMENT_DATE, B.GROUPING, C.ARTICLE_NUMBER, C.SIZE_NUMBER_ID, C.COLOR_NUMBER_ID, a.JOB_NO_PREFIX_NUM, a.JOB_NO 
	from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and b.id = c.po_break_down_id and a.company_name=$company $po_id_cond");
    $all_po_id=chop($all_po_id,",");
	//	$poDataArr = sql_select("select A.STYLE_REF_NO, B.ID, B.PO_NUMBER, B.PUB_SHIPMENT_DATE, B.GROUPING from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$company");
	foreach($poDataArr as $rowP)
	{
		$po_arr[$rowP["ID"]][1]=$rowP["STYLE_REF_NO"];
		$po_arr[$rowP["ID"]][2]=$rowP["PO_NUMBER"];
		$po_arr[$rowP["ID"]][3]=$rowP["PUB_SHIPMENT_DATE"];
		$po_arr[$rowP["ID"]][4]=$rowP["GROUPING"];
        $po_arr[$rowP["ID"]][5][$rowP["SIZE_NUMBER_ID"]."*".$rowP["COLOR_NUMBER_ID"]]=$rowP["ARTICLE_NUMBER"];
		$po_arr[$rowP["ID"]][6]=$rowP["JOB_NO_PREFIX_NUM"];
		$po_arr[$rowP["ID"]][7]=$rowP["JOB_NO"];
    }
	unset($poDataArr);
	//	echo "<pre>";print_r($po_arr);//die;
	
	$lib_item_group_sql = sql_select("select id, trim_uom,conversion_factor from lib_item_group");
	$lib_item_group_arrr=array();
	foreach($lib_item_group_sql as $row)
	{
		$lib_item_group_arrr[$row[csf("id")]]["trim_uom"]=$row[csf("trim_uom")];
		$lib_item_group_arrr[$row[csf("id")]]["conversion_factor"]=$row[csf("conversion_factor")];
	}
	
	$sql_receive="select b.id as dtls_id, b.trans_id, b.booking_id, b.booking_no, b.booking_without_order, b.item_group_id as trim_group, b.order_uom as uom, b.item_description as description, b.brand_supplier, b.gmts_color_id as color_id, b.item_color, b.gmts_size_id as size_id, b.item_size, b.rate, c.order_rate, c.payment_over_recv, b.material_source, c.prod_id, c.po_breakdown_id, c.quantity as quantity, c.reject_qty, b.floor, b.room_no, b.rack_no, b.self_no, b.box_bin_no, b.payment_over_recv 
	from inv_trims_entry_dtls b, order_wise_pro_details c
	where b.id=c.dtls_id and b.trans_id=c.trans_id and b.prod_id=c.prod_id and c.entry_form=24 and c.trans_type=1 and b.mst_id=$mst_id and b.booking_id=$book_id and b.status_active=1 and c.status_active=1
	order by b.id";//
	//echo $sql_receive;//die;
	$data_array=sql_select($sql_receive);
	$update_details_data=array();$prev_ord_rate=0;
	foreach($data_array as $row)
	{
		if($row[csf("payment_over_recv")]==1) $row[csf("order_rate")]=$prev_ord_rate;
		if($dtls_check[$row[csf("dtls_id")]]=="")
		{
			$update_details_data[$row[csf("prod_id")]][$row[csf("po_breakdown_id")]][$row[csf("order_rate")]]["dtls_id"].=$row[csf("dtls_id")].",";
			$update_details_data[$row[csf("prod_id")]][$row[csf("po_breakdown_id")]][$row[csf("order_rate")]]["trans_id"].=$row[csf("trans_id")].",";
		}
		$update_details_data[$row[csf("prod_id")]][$row[csf("po_breakdown_id")]][$row[csf("order_rate")]]["prod_id"]=$row[csf("prod_id")];
		$update_details_data[$row[csf("prod_id")]][$row[csf("po_breakdown_id")]][$row[csf("order_rate")]]["po_breakdown_id"]=$row[csf("po_breakdown_id")];
		$update_details_data[$row[csf("prod_id")]][$row[csf("po_breakdown_id")]][$row[csf("order_rate")]]["booking_id"]=$row[csf("booking_id")];
		$update_details_data[$row[csf("prod_id")]][$row[csf("po_breakdown_id")]][$row[csf("order_rate")]]["booking_no"]=$row[csf("booking_no")];
		$update_details_data[$row[csf("prod_id")]][$row[csf("po_breakdown_id")]][$row[csf("order_rate")]]["booking_without_order"]=$row[csf("booking_without_order")];
		$update_details_data[$row[csf("prod_id")]][$row[csf("po_breakdown_id")]][$row[csf("order_rate")]]["trim_group"]=$row[csf("trim_group")];
		$update_details_data[$row[csf("prod_id")]][$row[csf("po_breakdown_id")]][$row[csf("order_rate")]]["uom"]=$row[csf("uom")];
		$update_details_data[$row[csf("prod_id")]][$row[csf("po_breakdown_id")]][$row[csf("order_rate")]]["description"]=$row[csf("description")];
		$update_details_data[$row[csf("prod_id")]][$row[csf("po_breakdown_id")]][$row[csf("order_rate")]]["brand_supplier"]=$row[csf("brand_supplier")];
		$update_details_data[$row[csf("prod_id")]][$row[csf("po_breakdown_id")]][$row[csf("order_rate")]]["color_id"]=$row[csf("color_id")];
		$update_details_data[$row[csf("prod_id")]][$row[csf("po_breakdown_id")]][$row[csf("order_rate")]]["item_color"]=$row[csf("item_color")];
		$update_details_data[$row[csf("prod_id")]][$row[csf("po_breakdown_id")]][$row[csf("order_rate")]]["size_id"]=$row[csf("size_id")];
		$update_details_data[$row[csf("prod_id")]][$row[csf("po_breakdown_id")]][$row[csf("order_rate")]]["item_size"]=$row[csf("item_size")];
		$update_details_data[$row[csf("prod_id")]][$row[csf("po_breakdown_id")]][$row[csf("order_rate")]]["payment_over_recv"]=$row[csf("payment_over_recv")];
		$update_details_data[$row[csf("prod_id")]][$row[csf("po_breakdown_id")]][$row[csf("order_rate")]]["material_source"]=$row[csf("material_source")];
		
		$update_details_data[$row[csf("prod_id")]][$row[csf("po_breakdown_id")]][$row[csf("order_rate")]]["floor"]=$row[csf("floor")];
		$update_details_data[$row[csf("prod_id")]][$row[csf("po_breakdown_id")]][$row[csf("order_rate")]]["room_no"]=$row[csf("room_no")];
		$update_details_data[$row[csf("prod_id")]][$row[csf("po_breakdown_id")]][$row[csf("order_rate")]]["rack_no"]=$row[csf("rack_no")];
		$update_details_data[$row[csf("prod_id")]][$row[csf("po_breakdown_id")]][$row[csf("order_rate")]]["self_no"]=$row[csf("self_no")];
		$update_details_data[$row[csf("prod_id")]][$row[csf("po_breakdown_id")]][$row[csf("order_rate")]]["box_bin_no"]=$row[csf("box_bin_no")];
		
		if($prod_po_check[$row[csf("prod_id")]][$row[csf("po_breakdown_id")]][$row[csf("order_rate")]]=="")
		{
			$prod_po_check[$row[csf("prod_id")]][$row[csf("po_breakdown_id")]][$row[csf("order_rate")]]=$row[csf("po_breakdown_id")];
			if($row[csf("order_rate")]>0)
			{
				$update_details_data[$row[csf("prod_id")]][$row[csf("po_breakdown_id")]][$row[csf("order_rate")]]["rate"]=$row[csf("order_rate")];
			}
			else
			{
				$update_details_data[$row[csf("prod_id")]][$row[csf("po_breakdown_id")]][$row[csf("order_rate")]]["rate"]=$row[csf("rate")];
			}
		}
		
		$update_details_data[$row[csf("prod_id")]][$row[csf("po_breakdown_id")]][$row[csf("order_rate")]]["quantity"]+=$row[csf("quantity")];
		$update_details_data[$row[csf("prod_id")]][$row[csf("po_breakdown_id")]][$row[csf("order_rate")]]["reject_qty"]+=$row[csf("reject_qty")];
		if($row[csf("payment_over_recv")]==0) $prev_ord_rate=$row[csf("order_rate")];
		
	}
	$i=1;
	
	//echo "<pre>";print_r($update_details_data);die;
	//echo "<pre>";print_r($booking_pi_data); echo "<pre>";print_r($prev_data);die;
	foreach($update_details_data as $prod_id=>$prod_data)
	{
		foreach($prod_data as $po_id=>$po_data)
		{
			foreach($po_data as $po_rate=>$row)
			{
				if ($i%2==0)$bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$qnty=$row[('quantity')];
				$amount=$qnty*$row[('rate')];
				$ile_amt=(($amount/100)*$ile_data);
				$prev_rcv_qnty=$prev_data[$row[("po_breakdown_id")]][$row[("prod_id")]];
				$descrip_arr=explode(",",$row[("description")]);
				$last_index=end(array_values($descrip_arr));
				$last_index=str_replace("[","",$last_index);
				$last_index=str_replace("]","",$last_index);
				if(trim($last_index)=="BS") $des_dtls=chop($row[("description")],', [BS]'); else $des_dtls=$row[("description")];
				$trim_key=$row[("po_breakdown_id")]."__".$row[("trim_group")]."__".$des_dtls."__".$row[("color_id")]."__".$row[("item_color")]."__".$row[("size_id")]."__".$row[("item_size")]."__".$row[("brand_supplier")]."__".$row[('rate')];
				$bl_qnty=($booking_pi_data[$trim_key]["book_qnty"]-$prev_data[$row[("po_breakdown_id")]][$row[("prod_id")]]);
				$bl_amount=$bl_qnty*$row[('rate')];
				
				
				$item_key=$row[('trim_group')].$des_dtls.$row[('brand_supplier')].$row[("item_color")].$row[('color_id')].$row[("item_size")].$row[("size_id")];
				
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $i; ?>" align="center">
					<td width="100" style="word-break:break-all" id="po_no_<? echo $i; ?>" title="<? echo $row[("po_breakdown_id")];?>"><? echo $po_arr[$row[("po_breakdown_id")]][2]; ?></td>
					<td width="70" id="ship_date_<? echo $i; ?>" title="<? echo $row[("po_breakdown_id")];?>"><? echo change_date_format($po_arr[$row[("po_breakdown_id")]][3]); ?></td>
                    <td width="50" id="job_no_<? echo $i; ?>" title="<? echo $po_arr[$row[("po_breakdown_id")]][7];?>"><? echo $po_arr[$row[("po_breakdown_id")]][6]; ?></td>
					<td width="70" id="ref_no_<? echo $i; ?>" title="<? echo $row[("po_breakdown_id")];?>"><? echo $po_arr[$row[("po_breakdown_id")]][4]; ?></td>
					<td width="70" id="article_no_<? echo $i; ?>" title="<? echo $row[("po_breakdown_id")];?>">
						<?
							echo $booking_pi_data[$trim_key]["item_ref"];
							//echo $row[csf("item_ref")];
						   // echo $po_arr[$row[("po_breakdown_id")]][5][$row[('size_id')]."*".$row[('color_id')]];
						?>
					</td>
					<td width="100" id="style_no_<? echo $i; ?>"  title="<? echo $trim_key;?>"><? echo $po_arr[$row[("po_breakdown_id")]][1]; ?></td>
					<td width="80" id="item_group_<? echo $i; ?>"  title="<? echo $row[('trim_group')];?>"><? echo $item_name_arr[$row[("trim_group")]]; ?></td>
					<td width="120" id="item_descrip_<? echo $i; ?>" style="word-break:break-all;"><? echo $row[('description')]; ?></td>
					<td width="70" id="brand_supp_<? echo $i; ?>"><? echo $row[('brand_supplier')]; ?></td>
					<td width="70" id="tdgmtscolorid_<? echo $i; ?>"><? echo $color_arr[$row[('color_id')]]; ?>
					<input type="hidden" name="gmtscolorid[]" id="gmtscolorid_<? echo $i; ?>" value="<? echo $row[('color_id')]; ?>" readonly>
					</td>
					<td width="70" id="gmt_size_<? echo $i; ?>" title="<? echo $row[("size_id")];?>"><? echo $size_arr[$row[('size_id')]]; ?></td>
					<td width="70" id="item_color_<? echo $i; ?>"  title="<? echo $row[("item_color")];?>"><? echo $color_arr[$row[('item_color')]]; ?></td>
					<td width="60" id="item_size_<? echo $i; ?>" title="<? echo $row[("item_size")];?>"><? echo $row[('item_size')]; ?></td>
					<td width="50" id="uom_<? echo $i; ?>" title="<? echo $row[("uom")];?>"><? echo $unit_of_measurement[$row[('uom')]]; ?></td>
					<td width="50" align="center" id="floor_td_to" class="floor_td_to"><p>
					<? 
					$argument = "'".$i.'_0'."'";
					echo create_drop_down( "cbo_floor_to_$i", 50,$lib_floor_arr,"", 1, "--Select--", $row[("floor")], "fn_load_room(this.value, $i); copy_all($argument); reset_room_rack_shelf($i,'cbo_floor_to');",0,"","","","","","","cbo_floor_to[]" ,"onchange_void"); ?>
					</p></td>
					<td width="50" align="center" id="room_td_to"><p>
					<? $argument = "'".$i.'_1'."'";
					echo create_drop_down( "cbo_room_to_$i", 50,$lib_room_arr,"", 1, "--Select--", $row[("room_no")], "fn_load_rack(this.value, $i); copy_all($argument); reset_room_rack_shelf($i,'cbo_room_to');",1,"","","","","","","cbo_room_to[]","onchange_void" ); ?>
					</p>
					</td>
					<td width="50" align="center" id="rack_td_to"><p>
					<? $argument = "'".$i.'_2'."'";
					echo create_drop_down( "txt_rack_to_$i", 50,$lib_rack_arr,"", 1, "--Select--", $row[("rack_no")], "fn_load_shelf(this.value, $i); copy_all($argument); reset_room_rack_shelf($i,'txt_rack_to');",1,"","","","","","","txt_rack_to[]","onchange_void" ); ?>
					</p></td>
					<td width="50" align="center" id="shelf_td_to"><p>
					<? $argument = "'".$i.'_3'."'";
					echo create_drop_down( "txt_shelf_to_$i", 50,$lib_shelf_arr,"", 1, "--Select--", $row[("self_no")], "fn_load_bin(this.value, $i); copy_all($argument); reset_room_rack_shelf($i,'txt_shelf_to');",1,"","","","","","","txt_shelf_to[]","onchange_void" ); ?>
					</p></td>
					<td width="50" align="center" id="bin_td_to"><p>
					<? $argument = "'".$i.'_4'."'"; 
					echo create_drop_down( "txt_bin_to_$i", 50,$lib_bin_arr,"", 1, "--Select--", $row[("box_bin_no")], "copy_all($argument);",1,"","","","","","","txt_bin_to[]","onchange_void" ); 
					?>
					</p></td>
					<td width="60" id="WOPIQnty_<? echo $i; ?>" title="<? echo $booking_pi_data[$trim_key]["book_qnty"];?>" align="right"><? echo number_format($booking_pi_data[$trim_key]["book_qnty"],4,'.',''); ?></td>
					<td width="70" id="tdreceiveqnty_<? echo $i; ?>">
						<input type="text" name="receiveqnty[]" id="receiveqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:50px;" value="<? echo number_format($qnty,4,'.',''); ?>" placeholder="<? echo number_format($qnty,4,'.',''); ?>" onBlur="calculate(<? echo $i; ?>);" />
					</td>
					<td width="70" align="right" class="rate_td" id="tdrate_<? echo $i; ?>" title="<?= $row[('rate')];?>"><? echo number_format($row[('rate')],6,'.',''); ?></td>
					<td width="70" align="right" id="tdile_<? echo $i; ?>" title="<?= $ile_amt;?>"><? echo $ile_amt; ?></td>
					<td width="70" align="right" class="amount_td" id="tdamount_<? echo $i; ?>" title="<?= $amount;?>"><? echo number_format($amount,4,'.','') ; ?></td>
					<td width="70" align="right" id="tdrejectrecvqnty_<? echo $i; ?>" title="<?= $row[('reject_qty')];?>"><? echo $row[('reject_qty')]; ?></td>
					<td width="70" align="right" id="tdbookcurrency_<? echo $i; ?>" title="<?= ($amount*$exchange_rate);?>"><? echo number_format(($amount*$exchange_rate),2,'.',''); ?></td>
					<td width="70" align="right" id="PrevRcvQnty_<? echo $i; ?>" title="<? echo $prev_rcv_qnty;?>"><? echo number_format($prev_rcv_qnty,4,'.',''); ?></td>
					<td width="70" id="tdblqty_<? echo $i; ?>" title="<? echo $bl_qnty;?>" blamt="<? echo number_format($bl_amount,4,'.',''); ?>"><?  echo number_format($bl_qnty,4,'.',''); ?></td>
					<td id="tdcbopaymentoverrecv_<? echo $i; ?>">
					<? 
					echo create_drop_down( "cbopaymentoverrecv_".$i,50, $payment_yes_no,'', 0, '',$row[("payment_over_recv")],"fn_pament_over('$item_key',this.value,$i)",1,"","","","","","","cbopaymentoverrecv[]","common_color_".$item_key); 
					?>
					<input type="hidden" name="updatedtlsid[]" id="updatedtlsid_<? echo $i; ?>" value="<? echo chop($row[("dtls_id")],",");?>" readonly>
					<input type="hidden" name="updatetransid[]" id="updatetransid_<? echo $i; ?>" value="<? echo chop($row[("trans_id")],",");?>" readonly>
					  <input type="hidden" name="previousprodid[]" id="previousprodid_<? echo $i; ?>" value="<? echo $row[("prod_id")];?>" readonly>
					</td>
				</tr> 
				<?
				$i++;
			}
		}
		
    }
	exit();
}



if($action=="duplication_check")
{
	$data=explode("**",$data);
	$update_id=$data[0];
	$dtls_id=$data[1];
	
	if($dtls_id=="") $dtls_id_cond=""; else $dtls_id_cond=" and b.id!=$dtls_id";
	
	//$sql="select a.supplier_id, a.currency_id, a.source, a.lc_no from inv_receive_master a, inv_trims_entry_dtls b where a.id=b.mst_id and a.id=$update_id and a.item_category=4 and a.entry_form=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $dtls_id_cond";
	
	$sql="select a.supplier_id, a.currency_id, a.source, a.lc_no from inv_receive_master a where a.id=$update_id and a.item_category=4 and a.entry_form=24 and a.status_active=1 and a.is_deleted=0";
	$dataArray=sql_select($sql);
	$data=$dataArray[0][csf('supplier_id')]."**".$dataArray[0][csf('currency_id')]."**".$dataArray[0][csf('source')]."**".$dataArray[0][csf('lc_no')];
	echo $data;
	exit();
}	 

if ($action=="save_update_delete")
{
	//$process = array( &$_POST );
	$process = $_POST;
	extract(check_magic_quote_gpc( $process )); 
	
	$sql_ile=sql_select("select item_group, standard from variable_inv_ile_standard where status_active=1 and is_deleted=0 and company_name=$cbo_company_id and source=$cbo_source and category=4");
	$ile_data=$sql_ile[0][csf("standard")];
	//$ile_data=array();
//	foreach($sql_ile as $row)
//	{
//		$ile_data[$row[csf("item_group")]]=$row[csf("standard")];
//	}
	//echo "10**$meterial_source";die;
	$meterial_source = str_replace("'","",$meterial_source);
	$is_buyer_supplied = ($meterial_source==3)?1 : 0;
	$buyer_supplied = ($meterial_source==3)?", [BS]" : "";
	
	$variable_set_invent = sql_select("select category,over_rcv_status,over_rcv_percent,over_rcv_payment from variable_inv_ile_standard where company_name=$cbo_company_id and variable_list=23 and category =4 order by id");
	$over_receive_limit = !empty($variable_set_invent) ? $variable_set_invent[0][csf('over_rcv_percent')] : 0;

	// check MRR Auditing Report is Audited or Not
	if (str_replace("'",'',$update_id) != '')
	{
		$is_audited=return_field_value("is_audited","inv_receive_master","id=".str_replace("'",'',$update_id)." and status_active=1 and is_deleted=0","is_audited");
		//echo "10**$is_audited".'rakib';die;
		if($is_audited==1) {
			echo "50**This MRR is Audited. Save, Update and Delete Not Allowed..";
			die;
		}
	}
	
	if ($operation==0)  // Insert Here
	{ 
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
	//	if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		
		$trims_recv_num=''; $trims_update_id='';
		if(str_replace("'","",$update_id)=="")
		{
			if($db_type==0) $year_cond="YEAR(insert_date)"; 
			else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
			else $year_cond="";//defined Later
			
			$id = return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master", $con);
			$new_trims_recv_system_id = explode("*", return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master",$con,1,$cbo_company_id,'TRE',24,date("Y",time()) ));
			$field_array="id, recv_number_prefix, recv_number_prefix_num, recv_number, entry_form, item_category, receive_basis, company_id, receive_date, challan_no, challan_date, booking_id, booking_no, booking_without_order, store_id, lc_no, source, pay_mode, supplier_id, currency_id, exchange_rate, is_multi, gate_entry_no, gate_entry_date, inserted_by, insert_date, remarks";
			
			$data_array="(".$id.",'".$new_trims_recv_system_id[1]."',".$new_trims_recv_system_id[2].",'".$new_trims_recv_system_id[0]."',24,4,".$cbo_receive_basis.",".$cbo_company_id.",".$txt_receive_date.",".$txt_receive_chal_no.",".$txt_challan_date.",".$txt_booking_pi_id.",".$txt_booking_pi_no.",".$booking_without_order.",".$cbo_store_name.",".$lc_id.",".$cbo_source.",".$cbo_pay_mode.",".$cbo_supplier_name.",".$cbo_currency_id.",".$txt_exchange_rate.",3,".$txt_gate_entry.",".$txt_gate_entry_date.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$txt_remarks.")";
			
			$trims_recv_num=$new_trims_recv_system_id[0];
			$trims_update_id=$id;
		}
		else
		{
			$original_receive_basis=sql_select("select receive_basis, supplier_id, source from inv_receive_master where id=$update_id");
			if(str_replace("'","",$cbo_receive_basis)!=$original_receive_basis[0][csf('receive_basis')])
			{
				echo "40**Multiple Receive Basis Not Allow In Same Received ID";
				disconnect($con);die;
			}
			if(str_replace("'","",$cbo_source)!=$original_receive_basis[0][csf('source')])
			{
				echo "40**Multiple Source Not Allow In Same Received ID";
				disconnect($con);die;
			}

			if(str_replace("'","",$cbo_supplier_name)!=$original_receive_basis[0][csf('supplier_id')])
			{
				echo "40**Multiple Supplier Not Allow In Same Received ID";
				disconnect($con);die;
			}
			 
			$field_array_update="receive_basis*receive_date*challan_no*challan_date*booking_id*booking_no*booking_without_order*store_id*lc_no*source*pay_mode*supplier_id*currency_id*exchange_rate*gate_entry_no*gate_entry_date*updated_by*update_date*remarks";
			
			$data_array_update=$cbo_receive_basis."*".$txt_receive_date."*".$txt_receive_chal_no."*".$txt_challan_date."*".$txt_booking_pi_id."*".$txt_booking_pi_no."*".$booking_without_order."*".$cbo_store_name."*".$lc_id."*".$cbo_source."*".$cbo_pay_mode."*".$cbo_supplier_name."*".$cbo_currency_id."*".$txt_exchange_rate."*".$txt_gate_entry."*".$txt_gate_entry_date."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$txt_remarks;
			
			$trims_recv_num=str_replace("'","",$txt_recieved_id);
			$trims_update_id=str_replace("'","",$update_id);
		}

		$item_wise_data=array();$item_order_qnty=array();
		//echo "10**$key=$meterial_source=$buyer_supplied";die;
		for($i=1;$i<=$tot_row; $i++)
		{
			$po_id="po_id".$i;
			$ref_no="ref_no".$i;
			$styleref="styleref".$i;
			$cboitemgroup="cboitemgroup".$i;
			$itemdescription="itemdescription".$i;
			$brandSupref="brandSupref".$i;
			$gmtssizeId="gmtssizeId".$i;
			$itemcolorid="itemcolorid".$i;
			$gmtscolorid="gmtscolorid".$i;
			$itemsizeid="itemsizeid".$i;
			$cbouom="cbouom".$i;
			
			$cbo_floor_to="cbo_floor_to".$i;
			$cbo_room_to="cbo_room_to".$i;
			$txt_rack_to="txt_rack_to".$i;
			$txt_shelf_to="txt_shelf_to".$i;
			$txt_bin_to="txt_bin_to".$i;
			
			$receiveqnty="receiveqnty".$i;
			$rate="rate".$i;
			$ile="ile".$i;
			$amount="amount".$i;
			$rejectrecvqnty="rejectrecvqnty".$i;
			$bookcurrency="bookcurrency".$i;
			$blqty="blqty".$i;
			$blamt="blamt".$i;
			$cbopaymentoverrecv="cbopaymentoverrecv".$i;
			$updatedtlsid="updatedtlsid".$i;
			$updatetransid="updatetransid".$i;
			$previousprodid="previousprodid".$i;
			$wopiqnty="wopiqnty".$i;
			
			/*$item_wise_data[$$cboitemgroup][$$itemdescription][$$brandSupref][$$itemcolorid][$$gmtscolorid][$$gmtssizeId][$$itemsizeid]['quantity']+=$$receiveqnty;
			$item_wise_data[$$cboitemgroup][$$itemdescription][$$brandSupref][$$itemcolorid][$$gmtscolorid][$$gmtssizeId][$$itemsizeid]['amount']+=$$amount;*/
			
			
			
			
			$all_item_group_id.=$$cboitemgroup.",";
			//$item_descriptions=$$itemdescription.$buyer_supplied;
			$item_descriptions=$$itemdescription.$buyer_supplied;
			$key=$$cboitemgroup."__".$item_descriptions."__".$$brandSupref."__".$$itemcolorid."__".$$gmtscolorid."__".$$gmtssizeId."__".$$itemsizeid."__".$$cbo_floor_to."__".$$cbo_room_to."__".$$txt_rack_to."__".$$txt_shelf_to."__".$$txt_bin_to."__".$$rate;
			//$buyer_supplied = ($meterial_source==3)?", [BS]" : "";
			//echo "10**$key=$meterial_source=$buyer_supplied";die;
			//$key=$$cboitemgroup."__".$item_descriptions."__".$$brandSupref."__".$$itemcolorid."__".$$gmtscolorid."__".$$gmtssizeId."__".$$itemsizeid;
			
			
			$item_wise_data[$key]['quantity']+=$$receiveqnty;
			$item_wise_data[$key]['amount']+=$$receiveqnty*$$rate;
			$item_wise_data[$key]['blqty']+=$$blqty;
			$item_wise_data[$key]['blamt']+=$$blqty*$$rate;
			$item_wise_data[$key]['wopiqnty']+=$$wopiqnty;
			$item_wise_data[$key]['rejectrecvqnty']+=$$rejectrecvqnty;
			$item_wise_data[$key]['ile']+=$$ile;
			
			$item_wise_data[$key]['cbouom']=$$cbouom;
			$item_wise_data[$key]['cbopaymentoverrecv']=$$cbopaymentoverrecv;
			
			
			if($po_check[$key][$$po_id]=="")
			{
				$po_check[$key][$$po_id]=$$po_id;
				$item_wise_data[$key]['po_id'].=$$po_id.",";
			}
			
			if($$cbopaymentoverrecv==1)
			{
				/*$over_qnty=$$receiveqnty-$$blqty;
				$item_order_qnty[$key][$$po_id]+=$$blqty;
				$item_order_over_qnty[$key][$$po_id]+=$over_qnty;
				
				$item_order_rejectQnty[$key][$$po_id]+=$$rejectrecvqnty;
				$item_order_over_rejectQnty[$key][$$po_id] =0;*/
				
				$over_qnty=$$receiveqnty-$$blqty;
				if(($$receiveqnty > $$blqty) && $$blqty>0)
				{
					//$check_data.="a,=".$$receiveqnty."=".$$blqty;
					$item_order_qnty[$key][$$po_id]+=$$blqty;
					$item_order_over_qnty[$key][$$po_id]+=$over_qnty;
					
					$item_order_rejectQnty[$key][$$po_id]+=$$rejectrecvqnty;
					$item_order_over_rejectQnty[$key][$$po_id] =0;
				}
				else if(($$receiveqnty<=$$blqty) && $$blqty>0)
				{
					//$check_data.="b,";
					$item_order_qnty[$key][$$po_id]+=$$receiveqnty;
					$item_order_rejectQnty[$key][$$po_id]+=$$rejectrecvqnty;
					$item_order_over_rejectQnty[$key][$$po_id] =0;
				}
				else
				{
					//$check_data.="c,$over_qnty";
					$item_order_over_qnty[$key][$$po_id]+=$over_qnty;
					$item_order_rejectQnty[$key][$$po_id]+=$$rejectrecvqnty;
					$item_order_over_rejectQnty[$key][$$po_id] =0;
				}
			}
			else
			{
				$item_order_qnty[$key][$$po_id]+=$$receiveqnty;
				$item_order_rejectQnty[$key][$$po_id]+=$$rejectrecvqnty;
			}
			$item_order_qnty_rate[$key][$$po_id]=$$rate;
			$item_order_paymentoverrecv[$key][$$po_id]=$$cbopaymentoverrecv;
		}

		
		$all_item_group_id=implode(",",array_unique(explode(",",chop($all_item_group_id,","))));
		if($all_item_group_id!="")
		{
			$lib_item_group_sql = sql_select("select id, trim_uom,conversion_factor from lib_item_group where id in($all_item_group_id)");
			$lib_item_group_arrr=array();
			foreach($lib_item_group_sql as $row)
			{
				$lib_item_group_arrr[$row[csf("id")]]["trim_uom"]=$row[csf("trim_uom")];
				$lib_item_group_arrr[$row[csf("id")]]["conversion_factor"]=$row[csf("conversion_factor")];
			}
			
			$prod_sql=sql_select("select id, item_group_id, item_description, brand_supplier, color as grms_color,  item_color, gmts_size, item_size, current_stock, avg_rate_per_unit, stock_value 
			from product_details_master 
			where company_id=$cbo_company_id and item_category_id=4 and entry_form=24 and item_group_id in($all_item_group_id) and status_active=1 and is_deleted=0");
			$prod_data=array();
			foreach($prod_sql as $row)
			{
				$prod_key=$row[csf("item_group_id")]."__".$row[csf("item_description")]."__".$row[csf("brand_supplier")]."__".$row[csf("item_color")]."__".$row[csf("grms_color")]."__".$row[csf("gmts_size")]."__".$row[csf("item_size")];
				$prod_data[$prod_key]["id"]=$row[csf("id")];
				$prod_data[$prod_key]["current_stock"]=$row[csf("current_stock")];
				$prod_data[$prod_key]["avg_rate_per_unit"]=$row[csf("avg_rate_per_unit")];
				$prod_data[$prod_key]["stock_value"]=$row[csf("stock_value")];
			}
		}
		
		
		$field_array_prod="id, company_id, store_id, item_category_id, entry_form, item_group_id, item_description, product_name_details, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, color, item_color, gmts_size, item_size, brand_supplier, is_buyer_supplied, inserted_by, insert_date";
		$field_array_trans="id, mst_id, receive_basis, pi_wo_batch_no, booking_no, booking_without_order, company_id, supplier_id, prod_id, item_category, transaction_type, transaction_date, store_id, order_uom, order_qnty, order_rate, order_amount, order_ile, order_ile_cost, cons_uom, cons_quantity, cons_rate, cons_amount, cons_ile, cons_ile_cost, balance_qnty, balance_amount, payment_over_recv, inserted_by, insert_date, floor_id, room, rack, self, bin_box";
		$field_array_dtls="id, mst_id, trans_id, booking_id, booking_no, booking_without_order, prod_id, item_group_id, item_description, brand_supplier, order_uom, order_id, receive_qnty, reject_receive_qnty, rate, amount, ile, ile_cost, gmts_color_id, item_color, gmts_size_id, item_size, save_string, item_description_color_size, cons_uom, cons_qnty, cons_rate, cons_ile, cons_ile_cost, book_keeping_curr, sensitivity, payment_over_recv, material_source, inserted_by, insert_date, floor, room_no, rack_no, self_no, box_bin_no";
		
		$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, quantity, reject_qty, order_rate, order_amount, inserted_by, insert_date,payment_over_recv";
		//$field_array_ord_prod="id, company_id, category_id, prod_id, po_breakdown_id, stock_quantity, last_rcv_qnty, avg_rate, stock_amount, inserted_by, insert_date";
		//$field_array_ord_prod_update="avg_rate*last_rcv_qnty*stock_quantity*stock_amount*updated_by*update_date";
		
		
		$data_array_prod_update=array();
		$data_array_prod="";$all_prod_id="";$data_array_trans="";$data_array_dtls="";$data_array_prop="";$data_array_ord_prod="";
		//echo "<pre>";print_r($item_wise_data);die;
		/*$id_trans=return_next_id( "id", "inv_transaction", 1 ) ;
		$id_dtls=return_next_id( "id", "inv_trims_entry_dtls", 1 ) ;
		$id_prop = return_next_id( "id", "order_wise_pro_details", 1 );
		$product_id=return_next_id( "id", "product_details_master", 1 ) ;*/
		//echo "10*<pre>";print_r($item_wise_data);die;
		$exchange_rate = str_replace("'","",$txt_exchange_rate);
		$product_unique_data_arr=array();
		foreach($item_wise_data as $item_key=>$item_val)
		{
			$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
			$id_dtls = return_next_id_by_sequence("INV_TRIMS_ENTRY_DTLS_PK_SEQ", "inv_trims_entry_dtls", $con);
			
			
			$item_key_data=explode("__",$item_key);
			$item_group_id=$item_key_data[0];
			$item_description=$item_key_data[1];
			$brand_supp=$item_key_data[2];
			$item_color_id=$item_key_data[3];
			$garments_color_id=$item_key_data[4];
			$garments_size_id=$item_key_data[5];
			$item_size_id=$item_key_data[6];
			
			$cbo_floor_to=$item_key_data[7];
			$cbo_room_to=$item_key_data[8];
			$txt_rack_to=$item_key_data[9];
			$txt_shelf_to=$item_key_data[10];
			$txt_bin_to=$item_key_data[11];
			
			$item_des_color_size=$item_description;
			if($color_arr[$item_color_id]!="") $item_des_color_size.=", ".$color_arr[$item_color_id];
			$prod_keys=$item_group_id."__".$item_description."__".$brand_supp."__".$item_color_id."__".$garments_color_id."__".$garments_size_id."__".$item_size_id;
			
			
			//if($size_arr[$item_size_id]!="") $item_des_color_size.=", ".$size_arr[$item_size_id];
			
			if($item_size_id!="") $item_des_color_size.=", ".$item_size_id;
			
			$ile_percent=$ile_data;
			$order_qnty=$item_val['quantity'];
			$order_amount=$item_val['amount'];
			$order_blqty=$item_val['blqty'];
			$order_blamt=$item_val['blamt'];
			$wopiqnty=$item_val['wopiqnty'];
			$order_rate=0;
			//if($order_amount!=0 && $order_qnty!=0) $order_rate=number_format(($order_amount/$order_qnty),8,'.','');
			$order_rate=$item_key_data[12];
			$cons_uom = $lib_item_group_arrr[$item_group_id]["trim_uom"];
			$conversion_factor = $lib_item_group_arrr[$item_group_id]["conversion_factor"];
			
			//echo "10**$conversion_factor=$item_group_id";die;
			
			
			
			$order_ile=$item_val['ile'];
			$cons_ile=($conversion_factor*$exchange_rate*$item_val['ile']);
			$payment_con_qnty = ($conversion_factor*$item_val['quantity']);
			$payment_con_amount=0;
			$payment_ord_amount=0;
			
			
			
			$txt_receive_qty = str_replace("'", "", $order_qnty);
			$over_receive_limit_qnty = ($over_receive_limit>0)?($over_receive_limit / 100) * $wopiqnty:0;			
			$allow_total_val = $order_blqty + $over_receive_limit_qnty;
			$overRecvLimitMsg="Over Receive limit = $over_receive_limit% ($over_receive_limit_qnty)";
			//echo "10**".$allow_total_val."=".$txt_receive_qty."=".$order_blqty."=".$over_receive_limit_qnty."=".$wopiqnty."=".$over_receive_limit;die;
			if(number_format($allow_total_val,4,".","")<number_format($txt_receive_qty,4,".","")) 
			{
				$over_msg = ($over_receive_limit>0)?"\nAllowed Quantity = $allow_total_val current input = $txt_receive_qty = $prod_keys":"";
				echo "40**Recv. quantity can not be greater than WO/Booking quantity.\n\nWO/Booking quantity = $wopiqnty \n$overRecvLimitMsg $over_msg \nAllowed Quantity = $allow_total_val";
				disconnect($con);die;
			}
			
			if($item_val['cbopaymentoverrecv']==1)
			{
				//echo "10**".$item_val['amount']."=".$order_blamt."=".$exchange_rate;die; 
				if($item_val['amount']>=$order_blamt && $order_blamt>0)
				{
					$payment_con_amount=($exchange_rate*$order_blamt);
					$payment_ord_amount=$order_blamt;
				}
				else if($item_val['amount'] < $order_blamt && $order_blamt>0)
				{
					$payment_con_amount=($exchange_rate*$item_val['amount']);
					$payment_ord_amount=$item_val['amount'];
				}
				else
				{
					$payment_con_amount=0;
					$payment_ord_amount=0;
				}
			}
			else
			{
				$payment_con_amount=($exchange_rate*$item_val['amount']);
				$payment_ord_amount=$item_val['amount'];
			}
			
			if($payment_ord_amount <=0)
			{
				echo "20**Invalid Operation";oci_rollback($con);disconnect($con);die;
			}
			
			$payment_cons_rate=0;
			if($payment_con_amount!=0 && $payment_con_qnty !=0) $payment_cons_rate = number_format(($payment_con_amount/$payment_con_qnty),8,'.','');
			$item_name =$trim_group_arr[$item_group_id]['name'];
			$prod_name_dtls=$item_name.", ".trim($item_description);
			
			if($item_unique_check[$prod_keys]=="")
			{
				$item_unique_check[$prod_keys]=$prod_keys;
				$runtime_tot_rcv=0;
				$runtime_tot_amt=0;
			}
			
			$runtime_tot_rcv+=$payment_con_qnty;
			$runtime_tot_amt+=$payment_con_amount;
			
			//print_r($item_wise_data);
			//echo "10** $payment_cons_rate == $payment_con_qnty == $payment_con_amount";check_table_status( $_SESSION['menu_id'],0); disconnect($con);die;
			
			if($prod_data[$prod_keys]["id"]!="")
			{
				$prod_id=$prod_data[$prod_keys]["id"];
				$max_transaction_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$prod_id and store_id=$cbo_store_name  and status_active = 1", "max_date");      
				if($max_transaction_date != "")
				{
					$max_transaction_date = date("Y-m-d", strtotime($max_transaction_date));
					$receive_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_receive_date)));
					if ($receive_date < $max_transaction_date) 
					{
						echo "20**Receive Date Can not Be Less Than Last Transaction Date Of This Item";
						//check_table_status($_SESSION['menu_id'], 0);
						disconnect($con); die;
					}
				}
				
				$product_unique_data_arr[$prod_keys]["id"]=$prod_data[$prod_keys]["id"];
				$product_unique_data_arr[$prod_keys]["rcv_qnty"]=$runtime_tot_rcv;
				$product_unique_data_arr[$prod_keys]["rcv_amt"]=$runtime_tot_amt;			
				
			}
			else
			{
				$product_id = return_next_id_by_sequence("PRODUCT_DETAILS_MASTER_PK_SEQ", "product_details_master", $con);
				$prod_id=$product_id;
				if($data_array_prod!="") $data_array_prod.=", ";
				$data_array_prod.="(".$prod_id.",".$cbo_company_id.",".$cbo_store_name.",4,24,'".$item_group_id."','".$item_description."','".$prod_name_dtls."','".$cons_uom."',0,0,0,0,'".$garments_color_id."','".$item_color_id."','".$garments_size_id."','".$item_size_id."','".$brand_supp."','".$is_buyer_supplied."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				
				$all_prod_id.=$prod_id.",";
				$runtime_current_stock=$payment_con_amount;
				$prod_data[$prod_keys]["id"]=$prod_id;
				
				$product_unique_data_arr[$prod_keys]["id"]=$prod_id;
				$product_unique_data_arr[$prod_keys]["rcv_qnty"]=$runtime_tot_rcv;
				$product_unique_data_arr[$prod_keys]["rcv_amt"]=$runtime_tot_amt;
			}
			
			
			if($item_val['cbopaymentoverrecv']==1)
			{
				
				if($order_qnty > $order_blqty)
				{
					//$che_data.="C".$order_blqty."=".$withRate_trans_id.",";
					if($order_blqty > 0)
					{
						$over_ord_qnty=($order_qnty-$order_blqty);
						$over_cons_qnty=($over_ord_qnty*$conversion_factor);
						$payment_con_qnty = ($conversion_factor*$order_blqty);
						$payment_cons_rate=0;
						if($payment_con_amount!=0 && $payment_con_qnty!=0) $payment_cons_rate = number_format(($payment_con_amount/$payment_con_qnty),8,'.','');
						//echo "10**".$payment_con_qnty."=".$payment_con_amount."=".$conversion_factor."=".$order_blqty;die;
						if($data_array_trans!="") $data_array_trans.=",";
						$data_array_trans.="(".$id_trans.",".$trims_update_id.",".$cbo_receive_basis.",".$txt_booking_pi_id.",".$txt_booking_pi_no.",".$booking_without_order.",".$cbo_company_id.",".$cbo_supplier_name.",".$prod_id.",4,1,".$txt_receive_date.",".$cbo_store_name.",'".$item_val['cbouom']."','".$order_blqty."','".$order_rate."','".$payment_ord_amount."','".$ile_percent."','".$order_ile."','".$cons_uom."','".$payment_con_qnty."','".$payment_cons_rate."','".$payment_con_amount."','".$ile_percent."','".$cons_ile."','".$payment_con_qnty."','".$payment_con_amount."','0',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$cbo_floor_to."','".$cbo_room_to."','".$txt_rack_to."','".$txt_shelf_to."','".$txt_bin_to."')";
						//echo "10**".$data_array_trans;die;
						//'po_id' rejectrecvqnty
						if($data_array_dtls!="") $data_array_dtls.=",";
						$data_array_dtls.="(".$id_dtls.",".$trims_update_id.",".$id_trans.",".$txt_booking_pi_id.",".$txt_booking_pi_no.",".$booking_without_order.",".$prod_id.",'".$item_group_id."','".$item_description."','".$brand_supp."','".$item_val['cbouom']."','".chop($item_val['po_id'],",")."','".$order_blqty."','".$item_val['rejectrecvqnty']."','".$order_rate."','".$payment_ord_amount."','".$ile_percent."','".$order_ile."','".$garments_color_id."','".$item_color_id."','".$garments_size_id."','".$item_size_id."','','".$item_des_color_size."','".$cons_uom."','".$payment_con_qnty."','".$payment_cons_rate."','".$ile_percent."','".$cons_ile."','".$payment_con_amount."','','0','".$meterial_source."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$cbo_floor_to."','".$cbo_room_to."','".$txt_rack_to."','".$txt_shelf_to."','".$txt_bin_to."')";
						
						$all_order_id_arr=explode(",",chop($item_val['po_id'],","));
						foreach($all_order_id_arr as $order_id)
						{
							if($item_order_qnty[$item_key][$order_id])
							{
								$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
								$order_amount=$item_order_qnty[$item_key][$order_id]*$item_order_qnty_rate[$item_key][$order_id];
								if($data_array_prop!="") $data_array_prop.=",";
								$data_array_prop.="(".$id_prop.",'".$id_trans."',1,24,'".$id_dtls."','".$order_id."','".$prod_id."','".$item_order_qnty[$item_key][$order_id]."','".$item_order_rejectQnty[$item_key][$order_id]."','".$item_order_qnty_rate[$item_key][$order_id]."','".$order_amount."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$item_order_paymentoverrecv[$item_key][$order_id]."')";
								//$id_prop = $id_prop+1;
								
							}
						}
					}
					
					$without_rate_ord_qnty=$order_qnty-$order_blqty;
					$without_rate_con_qnty=($conversion_factor*$without_rate_ord_qnty );
					$without_payment_con_amount= 0;
					$without_payment_cons_rate = 0;
					
					//$id_trans=$id_trans+1;
					//$id_dtls=$id_dtls+1;
					$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
					$id_dtls = return_next_id_by_sequence("INV_TRIMS_ENTRY_DTLS_PK_SEQ", "inv_trims_entry_dtls", $con);
					if($data_array_trans != "") $data_array_trans.=",";
					$data_array_trans.="(".$id_trans.",".$trims_update_id.",".$cbo_receive_basis.",".$txt_booking_pi_id.",".$txt_booking_pi_no.",".$booking_without_order.",".$cbo_company_id.",".$cbo_supplier_name.",".$prod_id.",4,1,".$txt_receive_date.",".$cbo_store_name.",'".$item_val['cbouom']."','".$without_rate_ord_qnty."','0','0','".$ile_percent."','".$order_ile."','".$cons_uom."','".$without_rate_con_qnty."','0','0','".$ile_percent."','".$cons_ile."','".$without_rate_con_qnty."','0','".$item_val['cbopaymentoverrecv']."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$cbo_floor_to."','".$cbo_room_to."','".$txt_rack_to."','".$txt_shelf_to."','".$txt_bin_to."')";
					
					//echo "10**".$data_array_trans;die;
					
					if($data_array_dtls != "") $data_array_dtls.=",";
					$data_array_dtls.="(".$id_dtls.",".$trims_update_id.",".$id_trans.",".$txt_booking_pi_id.",".$txt_booking_pi_no.",".$booking_without_order.",".$prod_id.",'".$item_group_id."','".$item_description."','".$brand_supp."','".$item_val['cbouom']."','".chop($item_val['po_id'],",")."','".$without_rate_ord_qnty."','0','0','0','".$ile_percent."','".$order_ile."','".$garments_color_id."','".$item_color_id."','".$garments_size_id."','".$item_size_id."','','".$item_des_color_size."','".$cons_uom."','".$without_rate_con_qnty."','0','".$ile_percent."','".$cons_ile."','0','','".$item_val['cbopaymentoverrecv']."','".$meterial_source."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$cbo_floor_to."','".$cbo_room_to."','".$txt_rack_to."','".$txt_shelf_to."','".$txt_bin_to."')";
					$all_order_id_arr=explode(",",chop($item_val['po_id'],","));
					foreach($all_order_id_arr as $order_id)
					{
						$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
						if($data_array_prop!="") $data_array_prop.=",";
						$data_array_prop.="(".$id_prop.",'".$id_trans."',1,24,'".$id_dtls."','".$order_id."','".$prod_id."','".$item_order_over_qnty[$item_key][$order_id]."','".$item_order_over_rejectQnty[$item_key][$order_id]."','0','0',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$item_order_paymentoverrecv[$item_key][$order_id]."')";
						
					}
				}
				else
				{
					//$che_data.="D".$order_blqty."=".$withRate_trans_id.",";
					//$over_ord_qnty=number_format(($order_qnty-$order_blqty),5,".","");
					//$over_cons_qnty=number_format(($over_ord_qnty*$conversion_factor),5,".","");
					$pay_ord_rate=$order_rate;
					$payment_con_qnty = ($conversion_factor*$order_qnty);
					$payment_cons_rate = ($payment_con_amount/$payment_con_qnty);
					//echo "10**".$order_qnty."**".$order_blqty;die;
					if($data_array_trans!="") $data_array_trans.=",";
					$data_array_trans.="(".$id_trans.",".$trims_update_id.",".$cbo_receive_basis.",".$txt_booking_pi_id.",".$txt_booking_pi_no.",".$booking_without_order.",".$cbo_company_id.",".$cbo_supplier_name.",".$prod_id.",4,1,".$txt_receive_date.",".$cbo_store_name.",'".$item_val['cbouom']."','".$order_qnty."','".$pay_ord_rate."','".$payment_ord_amount."','".$ile_percent."','".$order_ile."','".$cons_uom."','".$payment_con_qnty."','".$payment_cons_rate."','".$payment_con_amount."','".$ile_percent."','".$cons_ile."','".$payment_con_qnty."','".$payment_con_amount."','0',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$cbo_floor_to."','".$cbo_room_to."','".$txt_rack_to."','".$txt_shelf_to."','".$txt_bin_to."')";
					//'po_id' rejectrecvqnty
					if($data_array_dtls!="") $data_array_dtls.=",";
					$data_array_dtls.="(".$id_dtls.",".$trims_update_id.",".$id_trans.",".$txt_booking_pi_id.",".$txt_booking_pi_no.",".$booking_without_order.",".$prod_id.",'".$item_group_id."','".$item_description."','".$brand_supp."','".$item_val['cbouom']."','".chop($item_val['po_id'],",")."','".$order_qnty."','".$item_val['rejectrecvqnty']."','".$pay_ord_rate."','".$payment_ord_amount."','".$ile_percent."','".$order_ile."','".$garments_color_id."','".$item_color_id."','".$garments_size_id."','".$item_size_id."','','".$item_des_color_size."','".$cons_uom."','".$payment_con_qnty."','".$payment_cons_rate."','".$ile_percent."','".$cons_ile."','".$payment_con_amount."','','0','".$meterial_source."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$cbo_floor_to."','".$cbo_room_to."','".$txt_rack_to."','".$txt_shelf_to."','".$txt_bin_to."')";
					
					$all_order_id_arr=explode(",",chop($item_val['po_id'],","));
					foreach($all_order_id_arr as $order_id)
					{
						$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
						$order_amount=$item_order_qnty[$item_key][$order_id]*$item_order_qnty_rate[$item_key][$order_id];
						if($data_array_prop!="") $data_array_prop.=",";
						$data_array_prop.="(".$id_prop.",'".$id_trans."',1,24,'".$id_dtls."','".$order_id."','".$prod_id."','".$item_order_qnty[$item_key][$order_id]."','".$item_order_rejectQnty[$item_key][$order_id]."','".$item_order_qnty_rate[$item_key][$order_id]."','".$order_amount."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$item_order_paymentoverrecv[$item_key][$order_id]."')";
						//$id_prop = $id_prop+1;
					}
				}
			}
			else
			{
				if($data_array_trans!="") $data_array_trans.=",";
				$data_array_trans.="(".$id_trans.",".$trims_update_id.",".$cbo_receive_basis.",".$txt_booking_pi_id.",".$txt_booking_pi_no.",".$booking_without_order.",".$cbo_company_id.",".$cbo_supplier_name.",".$prod_id.",4,1,".$txt_receive_date.",".$cbo_store_name.",'".$item_val['cbouom']."','".$order_qnty."','".$order_rate."','".$order_amount."','".$ile_percent."','".$order_ile."','".$cons_uom."','".$payment_con_qnty."','".$payment_cons_rate."','".$payment_con_amount."','".$ile_percent."','".$cons_ile."','".$payment_con_qnty."','".$payment_con_amount."','".$item_val['cbopaymentoverrecv']."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$cbo_floor_to."','".$cbo_room_to."','".$txt_rack_to."','".$txt_shelf_to."','".$txt_bin_to."')";
				
				if($data_array_dtls!="") $data_array_dtls.=",";
				
				$data_array_dtls.="(".$id_dtls.",".$trims_update_id.",".$id_trans.",".$txt_booking_pi_id.",".$txt_booking_pi_no.",".$booking_without_order.",".$prod_id.",'".$item_group_id."','".$item_description."','".$brand_supp."','".$item_val['cbouom']."','".chop($item_val['po_id'],",")."','".$order_qnty."','".$item_val['rejectrecvqnty']."','".$order_rate."','".$order_amount."','".$ile_percent."','".$order_ile."','".$garments_color_id."','".$item_color_id."','".$garments_size_id."','".$item_size_id."','','".$item_des_color_size."','".$cons_uom."','".$payment_con_qnty."','".$payment_cons_rate."','".$ile_percent."','".$cons_ile."','".$payment_con_amount."','','".$item_val['cbopaymentoverrecv']."','".$meterial_source."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$cbo_floor_to."','".$cbo_room_to."','".$txt_rack_to."','".$txt_shelf_to."','".$txt_bin_to."')";
				
				$all_order_id_arr=explode(",",chop($item_val['po_id'],","));
				foreach($all_order_id_arr as $order_id)
				{
					$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
					$order_amount=$item_order_qnty[$item_key][$order_id]*$item_order_qnty_rate[$item_key][$order_id];
					if($data_array_prop!="") $data_array_prop.=",";
					$data_array_prop.="(".$id_prop.",'".$id_trans."',1,24,'".$id_dtls."','".$order_id."','".$prod_id."','".$item_order_qnty[$item_key][$order_id]."','".$item_order_over_rejectQnty[$item_key][$order_id]."','".$item_order_qnty_rate[$item_key][$order_id]."','".$order_amount."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$item_order_paymentoverrecv[$item_key][$order_id]."')";
				}
			}
		}
		//echo "10**".$data_array_prop;die;
		$all_prod_id=chop($all_prod_id,",");
		if($all_prod_id=="") $all_prod_id=0;
		$max_issue_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id in($all_prod_id) and transaction_type in (2,3,6)", "max_date");      
		if($max_issue_date !="")
		{
			$max_issue_date = date("Y-m-d", strtotime($max_issue_date));
			$receive_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_receive_date)));

			if ($receive_date < $max_issue_date) 
			{
				echo "40**Receive Date Can not Be Less Than Last Issue Date Of This Item";
				//check_table_status( $_SESSION['menu_id'],0);
				disconnect($con);die;
			}
		}
		
		//echo "10** insert into inv_receive_master ($field_array) values $data_array";die;
		//echo "10**insert into inv_transaction (".$field_array_trans.") values ".$data_array_trans;oci_rollback($con);die;
		$rID=$rID2=$rID3=$rID4=$prodUpdate=$prodInsert=$ordProdUpdate=$ordProdInsert=true;
		if(str_replace("'","",$update_id)=="")
		{
			$rID=sql_insert("inv_receive_master",$field_array,$data_array,0);
		}
		else
		{
			$rID=sql_update("inv_receive_master",$field_array_update,$data_array_update,"id",$update_id,1);
		}
		
		//echo "10**insert into inv_transaction (".$field_array_trans.") values ".$data_array_trans;oci_rollback($con);disconnect($con);die;
		if($data_array_trans!="")
		{
			$rID2=sql_insert("inv_transaction",$field_array_trans,$data_array_trans,0);
		}
		
		//check_table_status( $_SESSION['menu_id'],0);disconnect($con);die;
		if($data_array_dtls!="")
		{
			$rID3=sql_insert("inv_trims_entry_dtls",$field_array_dtls,$data_array_dtls,0);
		}
		//echo "10**insert into inv_trims_entry_dtls (".$field_array_dtls.") values ".$data_array_dtls;oci_rollback($con);disconnect($con);die;
		if($data_array_prop!="" && str_replace("'","",$booking_without_order)!=1)
		{
			$rID4=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,0);
		}
		
		if($rID && $rID2 && $rID3 && $rID4 && $data_array_prod!="")
		{
			$prodInsert=sql_insert("product_details_master",$field_array_prod,$data_array_prod,0);
			if($prodInsert) oci_commit($con); 
			else {
				oci_rollback($con);
				echo "5**0**"."&nbsp;"."**0";
				disconnect($con);die;
			} 
		}
		
		//echo "10**<pre>";print_r($prod_data);print_r($product_unique_data_arr);//die;
		
		$field_array_prod_update="avg_rate_per_unit*last_purchased_qnty*current_stock*stock_value*updated_by*update_date";
		foreach($product_unique_data_arr as $prod_key=>$prod_val)
		{
			$current_stock=$current_stock_value=$avg_rate_per_unit=0;
			if($prod_data[$prod_key]["id"]!="")
			{
				$current_stock=$prod_data[$prod_key]["current_stock"]+$prod_val["rcv_qnty"];
				$current_stock_value=$prod_data[$prod_key]["stock_value"]+$prod_val["rcv_amt"];
				if($current_stock!=0 && $current_stock_value!=0) $avg_rate_per_unit=$current_stock_value/$current_stock;
			}
			else
			{
				$current_stock=$prod_val["rcv_qnty"];
				$current_stock_value=$prod_val["rcv_amt"];
				if($current_stock!=0 && $current_stock_value!=0) $avg_rate_per_unit=$current_stock_value/$current_stock;
			}
			$updateProdID_array[]=$prod_val["id"];
			$data_array_prod_update[$prod_val["id"]]=explode("*",("".$avg_rate_per_unit."*".$prod_val["rcv_qnty"]."*".$current_stock."*".$current_stock_value."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
		}
		
		
		if(count($data_array_prod_update)>0)
		{
			//echo "10**".bulk_update_sql_statement("product_details_master","id",$field_array_prod_update,$data_array_prod_update,$updateProdID_array);oci_rollback($con);die;
			$prodUpdate=execute_query(bulk_update_sql_statement("product_details_master","id",$field_array_prod_update,$data_array_prod_update,$updateProdID_array));
		}				
		
		//echo "10** $rID && $rID2 && $rID3 && $rID4 && $prodUpdate && $prodInsert";oci_rollback($con);disconnect($con);die;

		if($db_type==0)
		{
			if($rID && $rID2 && $rID3 && $rID4 && $prodUpdate && $prodInsert)
			{
				mysql_query("COMMIT");  
				echo "0**".$trims_update_id."**".$trims_recv_num."**0";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "5**0**"."&nbsp;"."**0";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2 && $rID3 && $rID4 && $prodUpdate && $prodInsert)
			{
				oci_commit($con);  
				echo "0**".$trims_update_id."**".$trims_recv_num."**0";
			}
			else
			{
				oci_rollback($con);
				echo "5**0**"."&nbsp;"."**0";
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
		
		$trims_recv_num=str_replace("'","",$txt_recieved_id);
		$trims_update_id=str_replace("'","",$update_id);
		
		if($trims_update_id<1)
		{
			echo "40**Update Not Allow";disconnect($con);die;
		}
		
		$original_receive_basis=sql_select("select receive_basis, supplier_id, source from inv_receive_master where id=$update_id");
		if(str_replace("'","",$cbo_receive_basis)!=$original_receive_basis[0][csf('receive_basis')])
		{
			echo "40**Multiple Receive Basis Not Allow In Same Received ID";disconnect($con);
			die;
		}

		if(str_replace("'","",$cbo_source)!=$original_receive_basis[0][csf('source')])
		{
			echo "40**Multiple Source Not Allow In Same Received ID";
			//check_table_status( $_SESSION['menu_id'],0);
			disconnect($con);die;
		}
		if(str_replace("'","",$cbo_supplier_name)!=$original_receive_basis[0][csf('supplier_id')])
		{
			echo "40**Multiple Supplier Not Allow In Same Received ID";
			//check_table_status( $_SESSION['menu_id'],0);
			disconnect($con);die;
		}
		
		$up_details_data=sql_select("select a.id as dtls_id, a.trans_id, a.rate, a.prod_id, a.cons_qnty, a.book_keeping_curr, b.transaction_date 
		from inv_trims_entry_dtls a, inv_transaction b 
		where a.trans_id=b.id and a.mst_id=$update_id and a.booking_id=$txt_booking_pi_id and a.status_active=1 and a.is_deleted=0
		order by a.trans_id desc");
		$dtls_data_arr=array();$prev_prod_data=array();
		foreach($up_details_data as $row)
		{
			$dtls_data_arr[$row[csf("dtls_id")]]["rate"]=$row[csf("rate")];
			$dtls_data_arr[$row[csf("dtls_id")]]["trans_id"]=$row[csf("trans_id")];
			$prev_dtls_id[$row[csf("dtls_id")]]=$row[csf("dtls_id")];
			$all_prev_prod_id[$row[csf("prod_id")]]=$row[csf("prod_id")];
			$prev_prod_data[$row[csf("prod_id")]]["cons_qnty"]+=$row[csf("cons_qnty")];
			$prev_prod_data[$row[csf("prod_id")]]["cons_amt"]+=$row[csf("book_keeping_curr")];
			$prev_transaction_date=$row[csf("transaction_date")];
		}
		
		//echo "10**".strtotime("25-11-2022");die;
		if(strtotime($prev_transaction_date)<strtotime("25-11-2022"))
		{
			echo "40**Next Transaction Found, So Update Not Allow";disconnect($con);die;
		}
		
		$field_array_update="receive_basis*receive_date*challan_no*challan_date*booking_id*booking_no*booking_without_order*store_id*lc_no*source*pay_mode*supplier_id*currency_id*exchange_rate*gate_entry_no*gate_entry_date*updated_by*update_date*remarks";
			
		$data_array_update=$cbo_receive_basis."*".$txt_receive_date."*".$txt_receive_chal_no."*".$txt_challan_date."*".$txt_booking_pi_id."*".$txt_booking_pi_no."*".$booking_without_order."*".$cbo_store_name."*".$lc_id."*".$cbo_source."*".$cbo_pay_mode."*".$cbo_supplier_name."*".$cbo_currency_id."*".$txt_exchange_rate."*".$txt_gate_entry."*".$txt_gate_entry_date."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$txt_remarks;
		
		for($i=1;$i<=$tot_row; $i++)
		{
			$po_id="po_id".$i;
			$previousprodid="previousprodid".$i;
			$all_prod_ids[str_replace("'","",$$previousprodid)]=str_replace("'","",$$previousprodid);
			$all_po_ids[str_replace("'","",$$po_id)]=str_replace("'","",$$po_id);
		}
		
		$sql_next_trans="select PROD_ID, PO_BREAKDOWN_ID,  max(TRANS_ID) as TRANS_ID from ORDER_WISE_PRO_DETAILS where status_active=1 and is_deleted=0 and trans_type in(2,3,6) and PROD_ID in (".implode(",",$all_prod_ids).") and PO_BREAKDOWN_ID in (".implode(",",$all_po_ids).") group by PROD_ID, PO_BREAKDOWN_ID";
		$sql_next_trans_result=sql_select($sql_next_trans);
		$next_trans_data=array();
		foreach($sql_next_trans_result as $val)
		{
			$next_trans_data[$val["PROD_ID"]][$val["PO_BREAKDOWN_ID"]]=$val["TRANS_ID"];
		}
		
		//echo "10**<pre>";print_r($next_trans_data);die;
		
		$item_wise_data=array();$item_order_qnty=array();$all_dtls_id="";
		for($i=1;$i<=$tot_row; $i++)
		{
			$po_id="po_id".$i;
			$ref_no="ref_no".$i;
			$styleref="styleref".$i;
			$cboitemgroup="cboitemgroup".$i;
			$itemdescription="itemdescription".$i;
			$brandSupref="brandSupref".$i;
			$gmtssizeId="gmtssizeId".$i;
			$itemcolorid="itemcolorid".$i;
			$gmtscolorid="gmtscolorid".$i;
			$itemsizeid="itemsizeid".$i;
			$cbouom="cbouom".$i;
			
			$cbo_floor_to="cbo_floor_to".$i;
			$cbo_room_to="cbo_room_to".$i;
			$txt_rack_to="txt_rack_to".$i;
			$txt_shelf_to="txt_shelf_to".$i;
			$txt_bin_to="txt_bin_to".$i;
			
			$receiveqnty="receiveqnty".$i;
			$rate="rate".$i;
			$ile="ile".$i;
			$amount="amount".$i;
			$rejectrecvqnty="rejectrecvqnty".$i;
			$bookcurrency="bookcurrency".$i;
			$blqty="blqty".$i;
			$blamt="blamt".$i;
			$cbopaymentoverrecv="cbopaymentoverrecv".$i;
			$updatedtlsid="updatedtlsid".$i;
			$updatetransid="updatetransid".$i;
			$previousprodid="previousprodid".$i;
			$wopiqnty="wopiqnty".$i;
			
			$next_trans_id=$next_trans_data[str_replace("'","",$$previousprodid)][str_replace("'","",$$po_id)];
			$trans_id_arrs=explode(",",chop(str_replace("'","",$$updatetransid),","));
			$current_trans_id=$trans_id_arrs[0];
			//echo "10**".$next_trans_id."=".$current_trans_id;die;
			if($current_trans_id<$next_trans_id && $next_trans_id!="")
			{
				echo "40**Next Transaction Found, So Update Not Allow";disconnect($con);die;
			}
			/*$item_wise_data[$$cboitemgroup][$$itemdescription][$$brandSupref][$$itemcolorid][$$gmtscolorid][$$gmtssizeId][$$itemsizeid]['quantity']+=$$receiveqnty;
			$item_wise_data[$$cboitemgroup][$$itemdescription][$$brandSupref][$$itemcolorid][$$gmtscolorid][$$gmtssizeId][$$itemsizeid]['amount']+=$$amount;*/
			
			$all_item_group_id.=$$cboitemgroup.",";
				
			if(strpos($$itemdescription, ", [BS]") == false){
				$item_descriptions=$$itemdescription.$buyer_supplied;
			} else{
				$item_descriptions=$$itemdescription;
			}	
			
			$key=$$cboitemgroup."__".$item_descriptions."__".$$brandSupref."__".$$itemcolorid."__".$$gmtscolorid."__".$$gmtssizeId."__".$$itemsizeid."__".$$cbo_floor_to."__".$$cbo_room_to."__".$$txt_rack_to."__".$$txt_shelf_to."__".$$txt_bin_to."__".$$rate;
			
			//$key=$$cboitemgroup."__".$item_descriptions."__".$$brandSupref."__".$$itemcolorid."__".$$gmtscolorid."__".$$gmtssizeId."__".$$itemsizeid;
			//echo "10**".$key;oci_rollback($con);disconnect($con);die;
			
			$item_wise_data[$key]['previousprodid']=str_replace("'","",$$previousprodid);
			$item_wise_data[$key]['quantity']+=$$receiveqnty;
			$item_wise_data[$key]['amount']+=$$receiveqnty*$$rate;
			$item_wise_data[$key]['blqty']+=$$blqty;
			$item_wise_data[$key]['blamt']+=$$blqty*$$rate;
			$item_wise_data[$key]['wopiqnty']+=$$wopiqnty;
			$item_wise_data[$key]['rejectrecvqnty']+=$$rejectrecvqnty;
			$item_wise_data[$key]['ile']+=$$ile;
			
			
			
			$item_wise_data[$key]['cbouom']=$$cbouom;
			$item_wise_data[$key]['cbopaymentoverrecv']=$$cbopaymentoverrecv;
			
			if($po_check[$key][$$po_id]=="")
			{
				$po_check[$key][$$po_id]=$$po_id;
				$item_wise_data[$key]['po_id'].=$$po_id.",";
			}
			
			if($$cbopaymentoverrecv==1)
			{
				$over_qnty=$$receiveqnty-$$blqty;
				
				if(($$receiveqnty>=$$blqty) && $$blqty>0)
				{
					$item_order_qnty[$key][$$po_id]+=$$blqty;
					$item_order_over_qnty[$key][$$po_id]+=$over_qnty;
					
					$item_order_rejectQnty[$key][$$po_id]+=$$rejectrecvqnty;
					$item_order_over_rejectQnty[$key][$$po_id] =0;
					$all_dtls_id.=$$updatedtlsid.",";
				}
				else if(($$receiveqnty<$$blqty) && $$blqty>0)
				{
					$item_order_qnty[$key][$$po_id]+=$$receiveqnty;
					$item_order_rejectQnty[$key][$$po_id]+=$$rejectrecvqnty;
					$item_order_over_rejectQnty[$key][$$po_id] =0;
					$all_dtls_id.=$$updatedtlsid.",";
				}
				else
				{
					
					$item_order_over_qnty[$key][$$po_id]+=$over_qnty;
					$item_order_rejectQnty[$key][$$po_id]+=$$rejectrecvqnty;
					$item_order_over_rejectQnty[$key][$$po_id] =0;
					$all_dtls_id.=$$updatedtlsid.",";
				}
				
				//echo "10**t3=".$$receiveqnty."=".$$blqty ."=";print_r($item_order_over_qnty);die;
				
			}
			else
			{
				$all_dtls_id.=$$updatedtlsid.",";
				$item_order_qnty[$key][$$po_id]+=$$receiveqnty;
				$item_order_rejectQnty[$key][$$po_id]+=$$rejectrecvqnty;
			}
			
			
			if($dtls_id_check[$key][$$updatedtlsid]=="")
			{
				$dtls_id_check[$key][$$updatedtlsid]=$$updatedtlsid;
				$item_wise_data[$key]['updatedtlsid']=$$updatedtlsid;
				$item_wise_data[$key]['updatetransid']=$$updatetransid;
			}
			
			/*else
			{
				$item_wise_data[$key]['updatedtlsid'].=$$updatedtlsid.",";
				$item_wise_data[$key]['updatetransid'].=$$updatetransid.",";
			}*/
			$up_dtls_ids_arr=explode(",",chop($$updatedtlsid,","));
			foreach($up_dtls_ids_arr as $dtls_ids)
			{
				$current_dtls_id[$dtls_ids]=$dtls_ids;
			}
			
			$item_order_qnty_rate[$key][$$po_id]=$$rate;
			$item_order_paymentoverrecv[$key][$$po_id]=$$cbopaymentoverrecv;
			
		}
		
		
		
		//echo "10**".$sql_next_trans;die;
		//echo "10**<pre>";print_r($item_order_qnty);die;
		
		$all_item_group_id=implode(",",array_unique(explode(",",chop($all_item_group_id,","))));
		if($all_item_group_id!="")
		{
			$lib_item_group_sql = sql_select("select id, trim_uom,conversion_factor from lib_item_group where id in($all_item_group_id)");
			$lib_item_group_arrr=array();
			foreach($lib_item_group_sql as $row)
			{
				$lib_item_group_arrr[$row[csf("id")]]["trim_uom"]=$row[csf("trim_uom")];
				$lib_item_group_arrr[$row[csf("id")]]["conversion_factor"]=$row[csf("conversion_factor")];
			}
			$prod_sql=sql_select("select id, item_group_id, item_description, brand_supplier, color as grms_color,  item_color, gmts_size, item_size, current_stock, avg_rate_per_unit, stock_value 
			from product_details_master 
			where company_id=$cbo_company_id and item_category_id=4 and entry_form=24 and item_group_id in($all_item_group_id) and status_active=1 and is_deleted=0");
			$prod_data=array();
			foreach($prod_sql as $row)
			{
				$prod_key=$row[csf("item_group_id")]."__".$row[csf("item_description")]."__".$row[csf("brand_supplier")]."__".$row[csf("item_color")]."__".$row[csf("grms_color")]."__".$row[csf("gmts_size")]."__".$row[csf("item_size")];
				$prod_data[$prod_key]["id"]=$row[csf("id")];
				$prod_data[$prod_key]["current_stock"]=$row[csf("current_stock")];
				$prod_data[$prod_key]["avg_rate_per_unit"]=$row[csf("avg_rate_per_unit")];
				$prod_data[$prod_key]["stock_value"]=$row[csf("stock_value")];
				
				$exist_prod_data[$row[csf("id")]]["id"]=$row[csf("id")];
				$exist_prod_data[$row[csf("id")]]["current_stock"]=$row[csf("current_stock")];
				$exist_prod_data[$row[csf("id")]]["avg_rate_per_unit"]=$row[csf("avg_rate_per_unit")];
				$exist_prod_data[$row[csf("id")]]["stock_value"]=$row[csf("stock_value")];
				
			}
			
		}
		
		//echo "10**<pre>";print_r($prod_data);oci_rollback($con);disconnect($con);die;
		
		
		$field_array_prod="id, company_id, store_id, item_category_id, entry_form, item_group_id, item_description, product_name_details, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, color, item_color, gmts_size, item_size, brand_supplier, is_buyer_supplied, inserted_by, insert_date";
		$field_array_trans="id, mst_id, receive_basis, pi_wo_batch_no, booking_no, booking_without_order, company_id, supplier_id, prod_id, item_category, transaction_type, transaction_date, store_id, order_uom, order_qnty, order_rate, order_amount, order_ile, order_ile_cost, cons_uom, cons_quantity, cons_rate, cons_amount, cons_ile, cons_ile_cost, balance_qnty, balance_amount, payment_over_recv, inserted_by, insert_date, floor_id, room, rack, self, bin_box";
		$field_array_trans_update="transaction_date*store_id*order_uom*order_qnty*order_rate*order_amount*order_ile* order_ile_cost*cons_uom*cons_quantity*cons_rate*cons_amount*cons_ile*cons_ile_cost*balance_qnty*balance_amount* payment_over_recv*updated_by*update_date*floor_id*room*rack*self*bin_box";
		$field_array_dtls="id, mst_id, trans_id, booking_id, booking_no, booking_without_order, prod_id, item_group_id, item_description, brand_supplier, order_uom, order_id, receive_qnty, reject_receive_qnty, rate, amount, ile, ile_cost, gmts_color_id, item_color, gmts_size_id, item_size, save_string, item_description_color_size, cons_uom, cons_qnty, cons_rate, cons_ile, cons_ile_cost, book_keeping_curr, sensitivity, payment_over_recv, material_source, inserted_by, insert_date, floor, room_no, rack_no, self_no, box_bin_no";
		$field_array_dtls_update="item_group_id*item_description*brand_supplier*order_uom*order_id*receive_qnty*reject_receive_qnty*rate*amount*ile*ile_cost* gmts_color_id*item_color*gmts_size_id*item_size*save_string*item_description_color_size*cons_uom*cons_qnty*cons_rate* cons_ile*cons_ile_cost*book_keeping_curr*sensitivity*payment_over_recv*updated_by*update_date*floor*room_no*rack_no*self_no*box_bin_no";
		
		$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, quantity, reject_qty, order_rate, order_amount, inserted_by, insert_date,payment_over_recv";
		
		
		$data_array_prod_update=array();
		$data_array_prod="";$all_prod_id="";$data_array_trans="";$data_array_dtls="";$data_array_prop=""; $data_array_ord_prod="";
		
		/*$product_id=return_next_id( "id", "product_details_master", 1 ) ;
		$id_trans=return_next_id( "id", "inv_transaction", 1 ) ;
		$id_dtls=return_next_id( "id", "inv_trims_entry_dtls", 1 ) ;
		$id_prop = return_next_id( "id", "order_wise_pro_details", 1 );*/
		$exchange_rate = str_replace("'","",$txt_exchange_rate);
		$all_up_dtls_id="";$Inactive_dtls_id=$Inactive_trans_id="";
		$Inactive_trans_id=$Inactive_dtls_id="";$runtime_current_stock=0;
		foreach($item_wise_data as $item_key=>$item_val)
		{
			$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
			$id_dtls = return_next_id_by_sequence("INV_TRIMS_ENTRY_DTLS_PK_SEQ", "inv_trims_entry_dtls", $con);
			$previousprodid=$item_val['previousprodid'];
			if($previousprodid>0)
			{
				$product_id =$previousprodid;
			}
			else
			{
				$product_id = return_next_id_by_sequence("PRODUCT_DETAILS_MASTER_PK_SEQ", "product_details_master", $con);
			}
		
			$item_key_data=explode("__",$item_key);
			$item_group_id=$item_key_data[0];
			$item_description=$item_key_data[1];

			$brand_supp=$item_key_data[2];
			$item_color_id=$item_key_data[3];
			$garments_color_id=$item_key_data[4];
			$garments_size_id=$item_key_data[5];
			$item_size_id=$item_key_data[6];
			
			$cbo_floor_to=$item_key_data[7];
			$cbo_room_to=$item_key_data[8];
			$txt_rack_to=$item_key_data[9];
			$txt_shelf_to=$item_key_data[10];
			$txt_bin_to=$item_key_data[11];
			
			$prod_item_key=$item_key_data[0]."__".$item_key_data[1]."__".$item_key_data[2]."__".$item_key_data[3]."__".$item_key_data[4]."__".$item_key_data[5]."__".$item_key_data[6];
			$item_des_color_size=$item_description;
			if($color_arr[$item_color_id]!="") $item_des_color_size.=", ".$color_arr[$item_color_id];
			//if($size_arr[$item_size_id]!="") $item_des_color_size.=", ".$size_arr[$item_size_id];
			if($item_size_id!="") $item_des_color_size.=", ".$item_size_id;
			
			$ile_percent=$ile_data;
			$order_qnty=$item_val['quantity'];
			$order_amount=$item_val['amount'];
			$order_blqty=$item_val['blqty'];
			$order_blamt=$item_val['blamt'];
			$wopiqnty=$item_val['wopiqnty'];
			$order_rate=0;
			//if($order_amount!=0 && $order_qnty !=0) $order_rate=number_format(($order_amount/$order_qnty),8,'.','');
			$order_rate=$item_key_data[12];
			$cons_uom = $lib_item_group_arrr[$item_group_id]["trim_uom"];
			$conversion_factor = $lib_item_group_arrr[$item_group_id]["conversion_factor"];
			
			$order_ile=$item_val['ile'];
			$cons_ile=$conversion_factor*$exchange_rate*$item_val['ile'];
			$payment_con_qnty = ($conversion_factor*$item_val['quantity']);
			$payment_con_amount=0;
			$payment_ord_amount=0;
			if($item_val['cbopaymentoverrecv']==1)
			{
				
				if($item_val['amount']>=$order_blamt && $order_blamt>0)
				{
					$payment_con_amount=($exchange_rate*$order_blamt);
					$payment_ord_amount=$order_blamt;
				}
				else if($item_val['amount'] < $order_blamt && $order_blamt>0)
				{
					$payment_con_amount=($exchange_rate*$item_val['amount']);
					$payment_ord_amount=$item_val['amount'];
				}
				else
				{
					$payment_con_amount=0;
					$payment_ord_amount=0;
				}
			}
			else
			{
				$payment_con_amount=($exchange_rate*$item_val['amount']);
				$payment_ord_amount=$item_val['amount'];
			}
			
			$payment_cons_rate =0;
			if($payment_con_amount!=0 && $payment_con_qnty!=0) $payment_cons_rate = number_format(($payment_con_amount/$payment_con_qnty),8,'.','');
			$item_name =$trim_group_arr[$item_group_id]['name'];
			$prod_name_dtls=$item_name.", ".trim($item_description);
			
			
			$txt_receive_qty = str_replace("'", "", $order_qnty);
			$over_receive_limit_qnty = ($over_receive_limit>0)?($over_receive_limit / 100) * $wopiqnty:0;			
			$allow_total_val = $order_blqty + $over_receive_limit_qnty;
			$overRecvLimitMsg="Over Receive limit = $over_receive_limit% ($over_receive_limit_qnty)";
			if(number_format($allow_total_val,4,".","")<number_format($txt_receive_qty,4,".","")) 
			{
				$over_msg = ($over_receive_limit>0)?"\nAllowed Quantity = $allow_total_val":"";
				echo "40**Recv. quantity can not be greater than WO/Booking quantity.\n\nWO/Booking quantity = $wopiqnty \n$overRecvLimitMsg $over_msg";
				disconnect($con);die;
			}
			if($payment_ord_amount <=0)
			{
				echo "20**Invalid Operation";oci_rollback($con);disconnect($con);die;

			}
			
			if($item_unique_check[$prod_item_key]=="")
			{
				$item_unique_check[$prod_item_key]=$prod_item_key;
				$runtime_tot_rcv=0;
				$runtime_tot_amt=0;
			}
			
			$runtime_tot_rcv+=$payment_con_qnty;
			$runtime_tot_amt+=$payment_con_amount;
			
			$test_data.=$prod_item_key."\n";
			//echo "10**<pre>".$prod_data[$prod_item_key]["id"]."=".$prod_item_key;oci_rollback($con);disconnect($con);die;
			if($previousprodid>0)
			{
				$prod_id=$previousprodid;
				//$prod_id=$prod_data[$prod_item_key]["id"];
				if(chop($item_val['updatedtlsid'],",")!="")
				{
					$max_transaction_date = return_field_value("max(a.transaction_date) as max_date", "inv_transaction a, inv_trims_entry_dtls b", " a.id=b.trans_id and a.item_category=4 and a.transaction_type=1 and a.prod_id=$prod_id and a.store_id= $cbo_store_name and b.id not in(".chop($item_val['updatedtlsid'],",").")  and a.status_active = 1 and b.status_active = 1", "max_date");      
					if($max_transaction_date != "")
					{
						$max_transaction_date = date("Y-m-d", strtotime($max_transaction_date));
						$receive_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_receive_date)));
						if ($receive_date < $max_transaction_date && $_SESSION['logic_erp']['user_id']!=1) 
						{
							echo "20**Receive Date Can not Be Less Than Last Transaction Date Of This Item";
							//check_table_status($_SESSION['menu_id'], 0);
							disconnect($con);
							die;
						}
					}
				}
				 
				$current_prod_id[$prod_id]=$prod_id;
				
				$prev_prod_qnty=$prev_prod_data[$prod_id]["cons_qnty"];
				$prev_prod_amount=$prev_prod_qnty*$exist_prod_data[$prod_id]["avg_rate_per_unit"];
				
				$product_unique_data_arr[$prod_item_key]["id"]=$prod_data[$prod_item_key]["id"];
				$product_unique_data_arr[$prod_item_key]["rcv_qnty"]=$runtime_tot_rcv;
				$product_unique_data_arr[$prod_item_key]["rcv_amt"]=$runtime_tot_amt;
				$prod_data[$prod_item_key]["id"]=$prod_id;
				
			}
			else
			{
				$runtime_current_stock=0;
				$prod_id=$product_id;
				if($data_array_prod!="") $data_array_prod.=", ";
				$data_array_prod.="(".$prod_id.",".$cbo_company_id.",".$cbo_store_name.",4,24,'".$item_group_id."','".$item_description."','".$prod_name_dtls."','".$cons_uom."',0,0,0,0,'".$garments_color_id."','".$item_color_id."','".$garments_size_id."','".$item_size_id."','".$brand_supp."','".$is_buyer_supplied."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				
				$all_prod_id.=$prod_id.",";
				$prod_data[$prod_item_key]["id"]=$prod_id;
				
				$product_unique_data_arr[$prod_item_key]["id"]=$prod_data[$prod_item_key]["id"];
				$product_unique_data_arr[$prod_item_key]["rcv_qnty"]=$runtime_tot_rcv;
				$product_unique_data_arr[$prod_item_key]["rcv_amt"]=$runtime_tot_amt;
			}
			
			
			if($item_val['cbopaymentoverrecv']==1)
			{
				$update_dtls_id_arr=array();$update_trans_id_arr=array();
				$without_rate_qnty=$order_blqty-$order_qnty;
				//echo "10**$without_rate_qnty = $order_blqty = $order_qnty";die;
				if(chop($item_val['updatedtlsid'],",")!="")
				{
					$update_trans_id_arr=array();$update_dtls_id_arr=array();
					//echo "10**test2=".$without_rate_qnty;die;
					if($without_rate_qnty>=0)
					{
						//echo "10**".$item_val['updatetransid'];die;
						$payment_con_qnty = ($conversion_factor*$order_qnty );
						$payment_con_amount=($exchange_rate*$order_amount);
						$payment_cons_rate = ($payment_con_amount/$payment_con_qnty);
						$without_payment_con_qnty = ($conversion_factor*$without_payment_order_qnty);
						$without_payment_con_amount= 0;
						$without_payment_cons_rate = 0;
						
						$update_trans_id_arr=explode(",",$item_val['updatetransid']);
						$update_dtls_id_arr=explode(",",$item_val['updatedtlsid']);
					
						$updateTransId_array[]=$update_trans_id_arr[0];
						$data_array_trans_update[$update_trans_id_arr[0]]=explode("*",("".$txt_receive_date."*".$cbo_store_name."*'".$item_val['cbouom']."'*'".$order_qnty."'*'".$order_rate."'*'".$order_amount."'*'".$ile_percent."'*'".$order_ile."'*'".$cons_uom."'*'".$payment_con_qnty."'*'".$payment_cons_rate."'*'".$payment_con_amount."'*'".$ile_percent."'*'".$cons_ile."'*'".$payment_con_qnty."'*'".$payment_con_amount."'*'0'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'".$cbo_floor_to."'*'".$cbo_room_to."'*'".$txt_rack_to."'*'".$txt_shelf_to."'*'".$txt_bin_to."'"));
						
						$updateDtlsId_array[]=$update_dtls_id_arr[0];
						$data_array_dtls_update[$update_dtls_id_arr[0]]=explode("*",("'".$item_group_id."'*'".$item_description."'*'".$brand_supp."'*'".$item_val['cbouom']."'*'".chop($item_val['po_id'],",")."'*'".$order_qnty."'*'".$item_val['rejectrecvqnty']."'*'".$order_rate."'*'".$order_amount."'*'".$ile_percent."'*'".$order_ile."'*'".$garments_color_id."'*'".$item_color_id."'*'".$garments_size_id."'*'".$item_size_id."'*''*'".$item_des_color_size."'*'".$cons_uom."'*'".$payment_con_qnty."'*'".$payment_cons_rate."'*'".$ile_percent."'*'".$cons_ile."'*'".$payment_con_amount."'*''*'0'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'".$cbo_floor_to."'*'".$cbo_room_to."'*'".$txt_rack_to."'*'".$txt_shelf_to."'*'".$txt_bin_to."'"));
						
						$all_up_dtls_id.=$withRate_dtls_id.",";
						$all_order_id_arr=explode(",",chop($item_val['po_id'],","));
						//echo "10**";print_r($all_order_id_arr);die;
						//echo "10**";print_r($item_order_qnty);die;
						foreach($all_order_id_arr as $order_id)
						{
							$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
							$order_amount=$item_order_qnty[$item_key][$order_id]*$item_order_qnty_rate[$item_key][$order_id];
							if($data_array_prop!="") $data_array_prop.=",";
							$data_array_prop.="(".$id_prop.",'".$update_trans_id_arr[0]."',1,24,'".$update_dtls_id_arr[0]."','".$order_id."','".$prod_id."','".$item_order_qnty[$item_key][$order_id]."','".$item_order_rejectQnty[$item_key][$order_id]."','".$item_order_qnty_rate[$item_key][$order_id]."','".$order_amount."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$item_order_paymentoverrecv[$item_key][$order_id]."')";
							
						}
						
						if(count($update_trans_id_arr)>1)
						{
							$Inactive_trans_id.=$update_trans_id_arr[1];
							$Inactive_dtls_id.=$update_dtls_id_arr[1];
						}
						else
						{
							
						
							$all_order_id_arr=explode(",",chop($item_val['po_id'],","));
							foreach($all_order_id_arr as $order_id)
							{
								//echo "10** test3 = $item_key $order_id = ".$item_order_over_qnty[$item_key][$order_id]; print_r($item_order_over_qnty);die;
								if($item_order_over_qnty[$item_key][$order_id]>0)
								{
									
									$order_amount=$item_order_over_qnty[$item_key][$order_id]*$item_order_qnty_rate[$item_key][$order_id];
									$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
									if($data_array_prop!="") $data_array_prop.=",";
									$data_array_prop.="(".$id_prop.",'".$update_trans_id_arr[0]."',1,24,'".$update_dtls_id_arr[0]."','".$order_id."','".$prod_id."','".$item_order_over_qnty[$item_key][$order_id]."','".$item_order_over_rejectQnty[$item_key][$order_id]."','".$item_order_qnty_rate[$item_key][$order_id]."','".$order_amount."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$item_order_paymentoverrecv[$item_key][$order_id]."')";
									
								}
							}
						}

					}
					else
					{
						//echo "10**test";die;
						$up_trans_ids_arr=explode(",",$item_val['updatetransid']);
						$up_dtls_ids_arr=explode(",",$item_val['updatedtlsid']);
						//echo "10**".$order_blqty."=";oci_rollback($con);disconnect($con);die;
						if($order_blqty>0)
						{
							
							$payment_con_qnty = ($conversion_factor*$order_blqty );
							$payment_con_amount=($exchange_rate*$order_blamt);
							$payment_cons_rate=0;
							if($payment_con_amount!=0 && $payment_con_qnty!=0 ) $payment_cons_rate = number_format(($payment_con_amount/$payment_con_qnty),8,'.','');
							$without_payment_con_qnty = ($conversion_factor*$without_payment_order_qnty);
							
							//echo "10**".$up_trans_ids_arr[0]."=";oci_rollback($con);disconnect($con);die;
							
							$updateTransId_array[]=$up_trans_ids_arr[0];
							$data_array_trans_update[$up_trans_ids_arr[0]]=explode("*",("".$txt_receive_date."*".$cbo_store_name."*'".$item_val['cbouom']."'*'".$order_blqty."'*'".$order_rate."'*'".$order_blamt."'*'".$ile_percent."'*'".$order_ile."'*'".$cons_uom."'*'".$payment_con_qnty."'*'".$payment_cons_rate."'*'".$payment_con_amount."'*'".$ile_percent."'*'".$cons_ile."'*'".$payment_con_qnty."'*'".$payment_con_amount."'*'0'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'".$cbo_floor_to."'*'".$cbo_room_to."'*'".$txt_rack_to."'*'".$txt_shelf_to."'*'".$txt_bin_to."'"));
							
							$updateDtlsId_array[]=$up_dtls_ids_arr[0];
							$data_array_dtls_update[$up_dtls_ids_arr[0]]=explode("*",("'".$item_group_id."'*'".$item_description."'*'".$brand_supp."'*'".$item_val['cbouom']."'*'".chop($item_val['po_id'],",")."'*'".$order_blqty."'*'".$item_val['rejectrecvqnty']."'*'".$order_rate."'*'".$order_blamt."'*'".$ile_percent."'*'".$order_ile."'*'".$garments_color_id."'*'".$item_color_id."'*'".$garments_size_id."'*'".$item_size_id."'*''*'".$item_des_color_size."'*'".$cons_uom."'*'".$payment_con_qnty."'*'".$payment_cons_rate."'*'".$ile_percent."'*'".$cons_ile."'*'".$payment_con_amount."'*''*'0'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'".$cbo_floor_to."'*'".$cbo_room_to."'*'".$txt_rack_to."'*'".$txt_shelf_to."'*'".$txt_bin_to."'"));
							
							$all_up_dtls_id.=$withRate_dtls_id.",";
							$all_order_id_arr=explode(",",chop($item_val['po_id'],","));
							//echo "10**".$up_trans_ids_arr[0]."=";print_r($all_order_id_arr);oci_rollback($con);disconnect($con);die;							
							foreach($all_order_id_arr as $order_id)
							{
								$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
								$order_amount=$item_order_qnty[$item_key][$order_id]*$item_order_qnty_rate[$item_key][$order_id];
								
								if($data_array_prop!="") $data_array_prop.=",";
								$data_array_prop.="(".$id_prop.",'".$up_trans_ids_arr[0]."',1,24,'".$up_dtls_ids_arr[0]."','".$order_id."','".$prod_id."','".$item_order_qnty[$item_key][$order_id]."','".$item_order_rejectQnty[$item_key][$order_id]."','".$item_order_qnty_rate[$item_key][$order_id]."','".$order_amount."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$item_order_paymentoverrecv[$item_key][$order_id]."')";
								
							}
						}
						
						$without_rate_ord_qnty=$order_qnty-$order_blqty;
						$without_rate_con_qnty=($conversion_factor*$without_rate_ord_qnty );
						$without_payment_con_amount= 0;
						$without_payment_cons_rate = 0;
						
						if($up_trans_ids_arr[1])
						{
							$updateTransId_array[]=$up_trans_ids_arr[1];
							$data_array_trans_update[$up_trans_ids_arr[1]]=explode("*",("".$txt_receive_date."*".$cbo_store_name."*'".$item_val['cbouom']."'*'".$without_rate_ord_qnty."'*'0'*'0'*'".$ile_percent."'*'".$order_ile."'*'".$cons_uom."'*'".$without_rate_con_qnty."'*'0'*'0'*'".$ile_percent."'*'".$cons_ile."'*'".$without_rate_con_qnty."'*'0'*'".$item_val['cbopaymentoverrecv']."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'".$cbo_floor_to."'*'".$cbo_room_to."'*'".$txt_rack_to."'*'".$txt_shelf_to."'*'".$txt_bin_to."'"));
						
							$updateDtlsId_array[]=$up_dtls_ids_arr[1];
							$data_array_dtls_update[$up_dtls_ids_arr[1]]=explode("*",("'".$item_group_id."'*'".$item_description."'*'".$brand_supp."'*'".$item_val['cbouom']."'*'".chop($item_val['po_id'],",")."'*'".$without_rate_ord_qnty."'*'".$item_val['rejectrecvqnty']."'*'0'*'0'*'".$ile_percent."'*'".$order_ile."'*'".$garments_color_id."'*'".$item_color_id."'*'".$garments_size_id."'*'".$item_size_id."'*''*'".$item_des_color_size."'*'".$cons_uom."'*'".$without_rate_con_qnty."'*'0'*'".$ile_percent."'*'".$cons_ile."'*'0'*''*'".$item_val['cbopaymentoverrecv']."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'".$cbo_floor_to."'*'".$cbo_room_to."'*'".$txt_rack_to."'*'".$txt_shelf_to."'*'".$txt_bin_to."'"));
							
							$all_up_dtls_id.=$withoutRate_dtls_id.",";
							$all_order_id_arr=explode(",",chop($item_val['po_id'],","));
							foreach($all_order_id_arr as $order_id)
							{
								$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
								
								
								if($data_array_prop!="") $data_array_prop.=",";
								
								$data_array_prop.="(".$id_prop.",'".$up_trans_ids_arr[1]."',1,24,'".$up_dtls_ids_arr[1]."','".$order_id."','".$prod_id."','".$item_order_over_qnty[$item_key][$order_id]."','".$item_order_rejectQnty[$item_key][$order_id]."','0','0',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$item_order_paymentoverrecv[$item_key][$order_id]."')";
								
									
							}
						}
						else
						{
							$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
							$id_dtls = return_next_id_by_sequence("INV_TRIMS_ENTRY_DTLS_PK_SEQ", "inv_trims_entry_dtls", $con);
							
							if($data_array_trans != "") $data_array_trans.=",";
							$data_array_trans.="(".$id_trans.",".$trims_update_id.",".$cbo_receive_basis.",".$txt_booking_pi_id.",".$txt_booking_pi_no.",".$booking_without_order.",".$cbo_company_id.",".$cbo_supplier_name.",".$prod_id.",4,1,".$txt_receive_date.",".$cbo_store_name.",'".$item_val['cbouom']."','".$without_rate_ord_qnty."','0','0','".$ile_percent."','".$order_ile."','".$cons_uom."','".$without_rate_con_qnty."','0','0','".$ile_percent."','".$cons_ile."','".$without_rate_con_qnty."','0','".$item_val['cbopaymentoverrecv']."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$cbo_floor_to."','".$cbo_room_to."','".$txt_rack_to."','".$txt_shelf_to."','".$txt_bin_to."')";
							
							if($data_array_dtls != "") $data_array_dtls.=",";
							$data_array_dtls.="(".$id_dtls.",".$trims_update_id.",".$id_trans.",".$txt_booking_pi_id.",".$txt_booking_pi_no.",".$booking_without_order.",".$prod_id.",'".$item_group_id."','".$item_description."','".$brand_supp."','".$item_val['cbouom']."','".chop($item_val['po_id'],",")."','".$without_rate_ord_qnty."','0','0','0','".$ile_percent."','".$order_ile."','".$garments_color_id."','".$item_color_id."','".$garments_size_id."','".$item_size_id."','','".$item_des_color_size."','".$cons_uom."','".$without_rate_con_qnty."','0','".$ile_percent."','".$cons_ile."','0','','".$item_val['cbopaymentoverrecv']."','".$meterial_source."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$cbo_floor_to."','".$cbo_room_to."','".$txt_rack_to."','".$txt_shelf_to."','".$txt_bin_to."')";
							
							$all_order_id_arr=explode(",",chop($item_val['po_id'],","));
							foreach($all_order_id_arr as $order_id)
							{
								$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
								if($data_array_prop!="") $data_array_prop.=",";
								$data_array_prop.="(".$id_prop.",'".$id_trans."',1,24,'".$id_dtls."','".$order_id."','".$prod_id."','".$item_order_over_qnty[$item_key][$order_id]."','".$item_order_over_rejectQnty[$item_key][$order_id]."','0','0',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$item_order_paymentoverrecv[$item_key][$order_id]."')";
								
							}
						}
						

					}
				}
				else
				{
					if($order_qnty > $order_blqty)
					{
						//$che_data.="C".$order_blqty."=".$withRate_trans_id.",";
						if($order_blqty > 0)
						{
							$over_ord_qnty=($order_qnty-$order_blqty);
							$over_cons_qnty=($over_ord_qnty*$conversion_factor);
							$payment_con_qnty = ($conversion_factor*$order_blqty);
							$payment_cons_rate =0;
							if($payment_con_amount !=0 && $payment_con_qnty !=0) $payment_cons_rate = number_format(($payment_con_amount/$payment_con_qnty),8,'.','');
							
							if($data_array_trans!="") $data_array_trans.=",";
							$data_array_trans.="(".$id_trans.",".$trims_update_id.",".$cbo_receive_basis.",".$txt_booking_pi_id.",".$txt_booking_pi_no.",".$booking_without_order.",".$cbo_company_id.",".$cbo_supplier_name.",".$prod_id.",4,1,".$txt_receive_date.",".$cbo_store_name.",'".$item_val['cbouom']."','".$order_blqty."','".$order_rate."','".$payment_ord_amount."','".$ile_percent."','".$order_ile."','".$cons_uom."','".$payment_con_qnty."','".$payment_cons_rate."','".$payment_con_amount."','".$ile_percent."','".$cons_ile."','".$payment_con_qnty."','".$payment_con_amount."','0',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$cbo_floor_to."','".$cbo_room_to."','".$txt_rack_to."','".$txt_shelf_to."','".$txt_bin_to."')";
							//'po_id' rejectrecvqnty
							if($data_array_dtls!="") $data_array_dtls.=",";
							$data_array_dtls.="(".$id_dtls.",".$trims_update_id.",".$id_trans.",".$txt_booking_pi_id.",".$txt_booking_pi_no.",".$booking_without_order.",".$prod_id.",'".$item_group_id."','".$item_description."','".$brand_supp."','".$item_val['cbouom']."','".chop($item_val['po_id'],",")."','".$order_blqty."','".$item_val['rejectrecvqnty']."','".$order_rate."','".$payment_ord_amount."','".$ile_percent."','".$order_ile."','".$garments_color_id."','".$item_color_id."','".$garments_size_id."','".$item_size_id."','','".$item_des_color_size."','".$cons_uom."','".$payment_con_qnty."','".$payment_cons_rate."','".$ile_percent."','".$cons_ile."','".$payment_con_amount."','','0','".$meterial_source."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$cbo_floor_to."','".$cbo_room_to."','".$txt_rack_to."','".$txt_shelf_to."','".$txt_bin_to."')";
							
							$all_order_id_arr=explode(",",chop($item_val['po_id'],","));
							foreach($all_order_id_arr as $order_id)
							{
								$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
								$order_amount=$item_order_qnty[$item_key][$order_id]*$item_order_qnty_rate[$item_key][$order_id];
								if($data_array_prop!="") $data_array_prop.=",";
								$data_array_prop.="(".$id_prop.",'".$id_trans."',1,24,'".$id_dtls."','".$order_id."','".$prod_id."','".$item_order_qnty[$item_key][$order_id]."','".$item_order_rejectQnty[$item_key][$order_id]."','".$item_order_qnty_rate[$item_key][$order_id]."','".$order_amount."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$item_order_paymentoverrecv[$item_key][$order_id]."')";
								//$id_prop = $id_prop+1;
								
							}
						}
						
						$without_rate_ord_qnty=$order_qnty-$order_blqty;
						$without_rate_con_qnty=($conversion_factor*$without_rate_ord_qnty );
						$without_payment_con_amount= 0;
						$without_payment_cons_rate = 0;
						
						//$id_trans=$id_trans+1;
						//$id_dtls=$id_dtls+1;
						$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
						$id_dtls = return_next_id_by_sequence("INV_TRIMS_ENTRY_DTLS_PK_SEQ", "inv_trims_entry_dtls", $con);
						
						if($data_array_trans != "") $data_array_trans.=",";
						$data_array_trans.="(".$id_trans.",".$trims_update_id.",".$cbo_receive_basis.",".$txt_booking_pi_id.",".$txt_booking_pi_no.",".$booking_without_order.",".$cbo_company_id.",".$cbo_supplier_name.",".$prod_id.",4,1,".$txt_receive_date.",".$cbo_store_name.",'".$item_val['cbouom']."','".$without_rate_ord_qnty."','0','0','".$ile_percent."','".$order_ile."','".$cons_uom."','".$without_rate_con_qnty."','0','0','".$ile_percent."','".$cons_ile."','".$without_rate_con_qnty."','0','".$item_val['cbopaymentoverrecv']."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$cbo_floor_to."','".$cbo_room_to."','".$txt_rack_to."','".$txt_shelf_to."','".$txt_bin_to."')";
						
						if($data_array_dtls != "") $data_array_dtls.=",";
						$data_array_dtls.="(".$id_dtls.",".$trims_update_id.",".$id_trans.",".$txt_booking_pi_id.",".$txt_booking_pi_no.",".$booking_without_order.",".$prod_id.",'".$item_group_id."','".$item_description."','".$brand_supp."','".$item_val['cbouom']."','".chop($item_val['po_id'],",")."','".$without_rate_ord_qnty."','0','0','0','".$ile_percent."','".$order_ile."','".$garments_color_id."','".$item_color_id."','".$garments_size_id."','".$item_size_id."','','".$item_des_color_size."','".$cons_uom."','".$without_rate_con_qnty."','0','".$ile_percent."','".$cons_ile."','0','','".$item_val['cbopaymentoverrecv']."','".$meterial_source."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$cbo_floor_to."','".$cbo_room_to."','".$txt_rack_to."','".$txt_shelf_to."','".$txt_bin_to."')";
						
						$all_order_id_arr=explode(",",chop($item_val['po_id'],","));
						foreach($all_order_id_arr as $order_id)
						{
							$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
							if($data_array_prop!="") $data_array_prop.=",";
							$data_array_prop.="(".$id_prop.",'".$id_trans."',1,24,'".$id_dtls."','".$order_id."','".$prod_id."','".$item_order_over_qnty[$item_key][$order_id]."','".$item_order_over_rejectQnty[$item_key][$order_id]."','0','0',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$item_order_paymentoverrecv[$item_key][$order_id]."')";
							
						}
					}
					else
					{
						//$che_data.="D".$order_blqty."=".$withRate_trans_id.",";
						//$over_ord_qnty=number_format(($order_qnty-$order_blqty),5,".","");
						//$over_cons_qnty=number_format(($over_ord_qnty*$conversion_factor),5,".","");
						$pay_ord_rate=($payment_ord_amount/$order_qnty);
						$payment_con_qnty = ($conversion_factor*$order_qnty);
						$payment_cons_rate = 0;
						if($payment_con_amount !=0 && $payment_con_qnty !=0) $payment_cons_rate = number_format(($payment_con_amount/$payment_con_qnty),8,'.','');
						
						if($data_array_trans!="") $data_array_trans.=",";
						$data_array_trans.="(".$id_trans.",".$trims_update_id.",".$cbo_receive_basis.",".$txt_booking_pi_id.",".$txt_booking_pi_no.",".$booking_without_order.",".$cbo_company_id.",".$cbo_supplier_name.",".$prod_id.",4,1,".$txt_receive_date.",".$cbo_store_name.",'".$item_val['cbouom']."','".$order_qnty."','".$pay_ord_rate."','".$payment_ord_amount."','".$ile_percent."','".$order_ile."','".$cons_uom."','".$payment_con_qnty."','".$payment_cons_rate."','".$payment_con_amount."','".$ile_percent."','".$cons_ile."','".$payment_con_qnty."','".$payment_con_amount."','0',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$cbo_floor_to."','".$cbo_room_to."','".$txt_rack_to."','".$txt_shelf_to."','".$txt_bin_to."')";
						//'po_id' rejectrecvqnty
						if($data_array_dtls!="") $data_array_dtls.=",";
						$data_array_dtls.="(".$id_dtls.",".$trims_update_id.",".$id_trans.",".$txt_booking_pi_id.",".$txt_booking_pi_no.",".$booking_without_order.",".$prod_id.",'".$item_group_id."','".$item_description."','".$brand_supp."','".$item_val['cbouom']."','".chop($item_val['po_id'],",")."','".$order_qnty."','".$item_val['rejectrecvqnty']."','".$pay_ord_rate."','".$payment_ord_amount."','".$ile_percent."','".$order_ile."','".$garments_color_id."','".$item_color_id."','".$garments_size_id."','".$item_size_id."','','".$item_des_color_size."','".$cons_uom."','".$payment_con_qnty."','".$payment_cons_rate."','".$ile_percent."','".$cons_ile."','".$payment_con_amount."','','0','".$meterial_source."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$cbo_floor_to."','".$cbo_room_to."','".$txt_rack_to."','".$txt_shelf_to."','".$txt_bin_to."')";
						
						$all_order_id_arr=explode(",",chop($item_val['po_id'],","));
						foreach($all_order_id_arr as $order_id)
						{
							$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
							$order_amount=$item_order_qnty[$item_key][$order_id]*$item_order_qnty_rate[$item_key][$order_id];
							if($data_array_prop!="") $data_array_prop.=",";
							$data_array_prop.="(".$id_prop.",'".$id_trans."',1,24,'".$id_dtls."','".$order_id."','".$prod_id."','".$item_order_qnty[$item_key][$order_id]."','".$item_order_rejectQnty[$item_key][$order_id]."','".$item_order_qnty_rate[$item_key][$order_id]."','".$order_amount."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$item_order_paymentoverrecv[$item_key][$order_id]."')";
							//$id_prop = $id_prop+1;
							
						}
					}
				}
			}
			else
			{
				//echo "";check_table_status( $_SESSION['menu_id'],0); disconnect($con);die;
				$all_pay_rcv.=$item_val['cbopaymentoverrecv'].",";
				if(chop($item_val['updatedtlsid'],",")!="")
				{
					//$che_data.="E".$order_blqty."=".$withRate_trans_id.",";
					$all_update_dtlsID=chop($item_val['updatedtlsid'],",");
					$active_dtls_id=chop($item_val['updatedtlsid'],","); 
					$active_trans_id=$dtls_data_arr[chop($item_val['updatedtlsid'],",")]["trans_id"];

					$updateTransId_array[]=$active_trans_id;
					$data_array_trans_update[$active_trans_id]=explode("*",("".$txt_receive_date."*".$cbo_store_name."*'".$item_val['cbouom']."'*'".$order_qnty."'*'".$order_rate."'*'".$order_amount."'*'".$ile_percent."'*'".$order_ile."'*'".$cons_uom."'*'".$payment_con_qnty."'*'".$payment_cons_rate."'*'".$payment_con_amount."'*'".$ile_percent."'*'".$cons_ile."'*'".$payment_con_qnty."'*'".$payment_con_amount."'*'".$item_val['cbopaymentoverrecv']."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'".$cbo_floor_to."'*'".$cbo_room_to."'*'".$txt_rack_to."'*'".$txt_shelf_to."'*'".$txt_bin_to."'"));
					
					$updateDtlsId_array[]=$active_dtls_id;
					$data_array_dtls_update[$active_dtls_id]=explode("*",("'".$item_group_id."'*'".$item_description."'*'".$brand_supp."'*'".$item_val['cbouom']."'*'".chop($item_val['po_id'],",")."'*'".$order_qnty."'*'".$item_val['rejectrecvqnty']."'*'".$order_rate."'*'".$order_amount."'*'".$ile_percent."'*'".$order_ile."'*'".$garments_color_id."'*'".$item_color_id."'*'".$garments_size_id."'*'".$item_size_id."'*''*'".$item_des_color_size."'*'".$cons_uom."'*'".$payment_con_qnty."'*'".$payment_cons_rate."'*'".$ile_percent."'*'".$cons_ile."'*'".$payment_con_amount."'*''*'".$item_val['cbopaymentoverrecv']."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'".$cbo_floor_to."'*'".$cbo_room_to."'*'".$txt_rack_to."'*'".$txt_shelf_to."'*'".$txt_bin_to."'"));
					
					//$all_up_dtls_id.=$active_dtls_id.",";
					
					$all_order_id_arr=explode(",",chop($item_val['po_id'],","));
					foreach($all_order_id_arr as $order_id)
					{
						$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
						$order_amount=$item_order_qnty[$item_key][$order_id]*$item_order_qnty_rate[$item_key][$order_id];
						if($data_array_prop!="") $data_array_prop.=",";
						$data_array_prop.="(".$id_prop.",'".$active_trans_id."',1,24,'".$active_dtls_id."','".$order_id."','".$prod_id."','".$item_order_qnty[$item_key][$order_id]."','".$item_order_rejectQnty[$item_key][$order_id]."','".$item_order_qnty_rate[$item_key][$order_id]."','".$order_amount."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$item_order_paymentoverrecv[$item_key][$order_id]."')";
						
						//$id_prop = $id_prop+1;
						
					}
				}
				else
				{
					//$che_data.="F".$order_blqty."=".$withRate_trans_id.",";
					if($data_array_trans!="") $data_array_trans.=",";
					$data_array_trans.="(".$id_trans.",".$trims_update_id.",".$cbo_receive_basis.",".$txt_booking_pi_id.",".$txt_booking_pi_no.",".$booking_without_order.",".$cbo_company_id.",".$cbo_supplier_name.",".$prod_id.",4,1,".$txt_receive_date.",".$cbo_store_name.",'".$item_val['cbouom']."','".$order_qnty."','".$order_rate."','".$order_amount."','".$ile_percent."','".$order_ile."','".$cons_uom."','".$payment_con_qnty."','".$payment_cons_rate."','".$payment_con_amount."','".$ile_percent."','".$cons_ile."','".$payment_con_qnty."','".$payment_con_amount."','".$item_val['cbopaymentoverrecv']."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$cbo_floor_to."','".$cbo_room_to."','".$txt_rack_to."','".$txt_shelf_to."','".$txt_bin_to."')";
					
					if($data_array_dtls!="") $data_array_dtls.=",";
					
					$data_array_dtls.="(".$id_dtls.",".$trims_update_id.",".$id_trans.",".$txt_booking_pi_id.",".$txt_booking_pi_no.",".$booking_without_order.",".$prod_id.",'".$item_group_id."','".$item_description."','".$brand_supp."','".$item_val['cbouom']."','".chop($item_val['po_id'],",")."','".$order_qnty."','".$item_val['rejectrecvqnty']."','".$order_rate."','".$order_amount."','".$ile_percent."','".$order_ile."','".$garments_color_id."','".$item_color_id."','".$garments_size_id."','".$item_size_id."','','".$item_des_color_size."','".$cons_uom."','".$payment_con_qnty."','".$payment_cons_rate."','".$ile_percent."','".$cons_ile."','".$payment_con_amount."','','".$item_val['cbopaymentoverrecv']."','".$meterial_source."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$cbo_floor_to."','".$cbo_room_to."','".$txt_rack_to."','".$txt_shelf_to."','".$txt_bin_to."')";
					
					$all_order_id_arr=explode(",",chop($item_val['po_id'],","));
					foreach($all_order_id_arr as $order_id)
					{
						$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
						$order_amount=$item_order_qnty[$item_key][$order_id]*$item_order_qnty_rate[$item_key][$order_id];
						if($data_array_prop!="") $data_array_prop.=",";
						$data_array_prop.="(".$id_prop.",'".$id_trans."',1,24,'".$id_dtls."','".$order_id."','".$prod_id."','".$item_order_qnty[$item_key][$order_id]."','".$item_order_over_rejectQnty[$item_key][$order_id]."','".$item_order_qnty_rate[$item_key][$order_id]."','".$order_amount."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$item_order_paymentoverrecv[$item_key][$order_id]."')";
					}
				}
			}
		}
		
		
		//echo "10**<pre>".$test_data;print_r($prod_data);die;
		//echo "10**<pre>"; print_r($data_array_trans_update);die;
		
		
		$inactive_prod_id_arr=array_diff($all_prev_prod_id,$current_prod_id);
		
		if(count($inactive_prod_id_arr)>0)
		{
			$prod_sql=sql_select("select id, current_stock, avg_rate_per_unit, stock_value from product_details_master where id in(".implode(",",$inactive_prod_id_arr).")");
			foreach($prod_sql as $row)
			{
				$prev_prod_qnty=$prev_prod_data[$row[csf("id")]]["cons_qnty"];
				$prev_prod_amount=$prev_prod_qnty*$row[csf("avg_rate_per_unit")];
				
				$curr_stock_qnty=($row[csf("current_stock")]-$prev_prod_qnty);
				if ($curr_stock_qnty != 0){
					$curr_stock_value=($row[csf("current_stock")]-$prev_prod_amount);
					$avg_rate_per_unit=0;
					if($curr_stock_value != 0 && $curr_stock_qnty  != 0) $avg_rate_per_unit=number_format(($curr_stock_value/$curr_stock_qnty),8,'.','');
					else $avg_rate_per_unit=0;
				} else {
					$curr_stock_value=0;
					$avg_rate_per_unit=0;
				}
				
				$updateProdID_array[]=$row[csf("id")];
				$data_array_prod_update[$row[csf("id")]]=explode("*",("".$avg_rate_per_unit."*0*".$curr_stock_qnty."*".$curr_stock_value."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				$all_prod_id.=$row[csf("id")].",";
			}
		}
		
		//echo "10**";print_r($prev_dtls_id);echo "tt";print_r($current_dtls_id);die;
		
		$inactive_dtls_id_arr=array_diff($prev_dtls_id,$current_dtls_id);
		foreach($inactive_dtls_id_arr as $dtls_id)
		{
			if($Inactive_dtls_id!="") $Inactive_dtls_id.=",";
			$Inactive_dtls_id.=$dtls_id;
			if($Inactive_trans_id!="") $Inactive_trans_id.=",";
			$Inactive_trans_id.=$dtls_data_arr[$dtls_id]["trans_id"];
			$all_up_dtls_id.=$dtls_id.",";
		}
		$Inactive_dtls_id=chop($Inactive_dtls_id,",");
		$Inactive_trans_id=chop($Inactive_trans_id,",");
		
		//echo "10** $Inactive_dtls_id = $Inactive_trans_id";print_r($prev_dtls_id);print_r($current_dtls_id);print_r($inactive_dtls_id_arr);oci_rollback($con);disconnect($con);die;

		//echo "10**$data_array_prop";check_table_status( $_SESSION['menu_id'],0); disconnect($con);die;
		//echo "10**";print_r($inactive_dtls_id_arr);check_table_status( $_SESSION['menu_id'],0); disconnect($con);die;
		
		$rID=$rID2=$rID3=$rID4=$transUpdate=$dtlsUpdate=$delete_prop=$delete_dtls=$delete_trans=$prodInsert=$prodUpdate=true;
		
		$rID=sql_update("inv_receive_master",$field_array_update,$data_array_update,"id",$update_id,1);
		if($data_array_trans!="")
		{
			$rID2=sql_insert("inv_transaction",$field_array_trans,$data_array_trans,0);
		}
		//echo "10**insert into product_details_master (".$field_array_prod.") values ".$data_array_prod;die;
		//check_table_status( $_SESSION['menu_id'],0);disconnect($con);die;
		if($data_array_dtls!="")

		{
			$rID3=sql_insert("inv_trims_entry_dtls",$field_array_dtls,$data_array_dtls,0);
		}
		//echo "insert into inv_trims_entry_dtls (".$field_array_dtls.") values ".$data_array_dtls;die; execute_query  
		if(count($data_array_trans_update)>0)
		{
			//echo "10**$che_data ===".bulk_update_sql_statement("inv_transaction","id",$field_array_trans_update,$data_array_trans_update,$updateTransId_array);oci_rollback($con);die;
			$transUpdate=execute_query(bulk_update_sql_statement("inv_transaction","id",$field_array_trans_update,$data_array_trans_update,$updateTransId_array));
		}
		//echo "10**".$transUpdate;oci_rollback($con);check_table_status( $_SESSION['menu_id'],0);disconnect($con);die;
		
		if(count($data_array_dtls_update)>0)
		{
			//echo "10**".bulk_update_sql_statement("inv_trims_entry_dtls","id",$field_array_dtls_update,$data_array_dtls_update,$updateDtlsId_array);die;
			$dtlsUpdate=execute_query(bulk_update_sql_statement("inv_trims_entry_dtls","id",$field_array_dtls_update,$data_array_dtls_update,$updateDtlsId_array));
		}
		//echo "10**".$dtlsUpdate;oci_rollback($con);check_table_status( $_SESSION['menu_id'],0);disconnect($con);die;
		
		if($data_array_prop!="" && str_replace("'","",$booking_without_order)!=1)
		{
			if(count($prev_dtls_id)>0)
			{
				$delete_prop=execute_query( "delete from order_wise_pro_details where dtls_id in(".implode(",",$prev_dtls_id).") and entry_form=24 and trans_type=1",0);
			}
			//echo "10**insert into order_wise_pro_details (".$field_array_proportionate.") values ".$data_array_prop;oci_rollback($con);disconnect($con);die;
			$rID4=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,0);
		}
		
		if($Inactive_dtls_id!="")
		{
			$delete_dtls=execute_query("update inv_trims_entry_dtls set status_active=0, is_deleted=1, updated_by=".$_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."' where id in($Inactive_dtls_id)");
		}
		if($Inactive_trans_id!="")
		{
			$delete_trans=execute_query("update inv_transaction set status_active=0, is_deleted=1, updated_by=".$_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."' where id in($Inactive_trans_id)");
		}
		
		if($rID && $rID2 && $rID3 && $rID4 && $transUpdate && $dtlsUpdate && $delete_prop && $delete_dtls && $delete_trans && $data_array_prod!="")
		{
			$prodInsert=sql_insert("product_details_master",$field_array_prod,$data_array_prod,0);
			if($prodInsert)
			{
				oci_commit($con);
			}
			else
			{
				oci_rollback($con);
				echo "6**0**0**1";disconnect($con);die;
			}
		}
		
		
		
		$field_array_prod_update="avg_rate_per_unit*last_purchased_qnty*current_stock*stock_value*updated_by*update_date";
		foreach($product_unique_data_arr as $prod_key=>$prod_val)
		{
			
			$current_stock=$current_stock_value=$avg_rate_per_unit=0;
			if($prod_data[$prod_key]["id"]!="")
			{
				$current_stock=($prod_data[$prod_key]["current_stock"]-$prev_prod_data[$prod_val["id"]]["cons_qnty"])+$prod_val["rcv_qnty"];
				$current_stock_value=($prod_data[$prod_key]["stock_value"]-$prev_prod_data[$prod_val["id"]]["cons_amt"])+$prod_val["rcv_amt"];
				if($current_stock!=0 && $current_stock_value!=0) $avg_rate_per_unit=$current_stock_value/$current_stock;
			}
			else
			{
				$current_stock=$prod_val["rcv_qnty"];
				$current_stock_value=$prod_val["rcv_amt"];
				if($current_stock!=0 && $current_stock_value!=0) $avg_rate_per_unit=$current_stock_value/$current_stock;
			}
			$updateProdID_array[]=$prod_val["id"];
			$data_array_prod_update[$prod_val["id"]]=explode("*",("".$avg_rate_per_unit."*".$prod_val["rcv_qnty"]."*".$current_stock."*".$current_stock_value."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
		}
		
		if(count($data_array_prod_update)>0)
		{
			$prodUpdate=execute_query(bulk_update_sql_statement("product_details_master","id",$field_array_prod_update,$data_array_prod_update,$updateProdID_array),1);
		}
		
		
		//echo "10**$rID=$prodUpdate=$prodInsert=$rID2=$rID3=$rID4=$transUpdate=$dtlsUpdate=$delete_prop=$delete_dtls=$delete_trans=$ordProdUpdate=$ordProdInsert";oci_rollback($con);disconnect($con);die;
		//oci_rollback($con);check_table_status( $_SESSION['menu_id'],0);disconnect($con);die;
		
		
		
		if($db_type==0)
		{ 
			if($rID && $rID2 && $rID3 && $rID4 && $transUpdate && $dtlsUpdate && $delete_prop && $prodUpdate && $prodInsert )
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_recieved_id)."**0";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "6**0**0**1";
			}
		}

		if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2 && $rID3 && $rID4 && $transUpdate && $dtlsUpdate && $delete_prop && $prodUpdate && $prodInsert )
			{
				oci_commit($con);  
				echo "1**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_recieved_id)."**0";
			}
			else
			{
				oci_rollback($con);
				echo "6**0**0**1";
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
		
		//if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}		
		
		$update_id=str_replace("'","",$update_id);
		
		if($update_id>0)
		{
			if($db_type==0) $trns_id_select=", group_concat(id) as all_rcv_id"; else $trns_id_select=", LISTAGG(cast(id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY id) as all_rcv_id";
			$rcv_sql=sql_select("select max(id) as rcv_id, prod_id, sum(cons_quantity) as rcv_qnty, sum(cons_amount) as rcv_amt  $trns_id_select  from inv_transaction where transaction_type=1 and mst_id=$update_id and status_active=1 group by prod_id order by prod_id");
			$receive_data=array();
			$all_rcv_trans_id="";$all_product_id="";$prod_wise_data=array();
			foreach($rcv_sql as $row)
			{
				$receive_data[$row[csf("prod_id")]]=$row[csf("rcv_id")];
				$all_rcv_trans_id.=$row[csf("rcv_id")].",";
				$all_rcv_id.=$row[csf("all_rcv_id")].",";
				$all_product_id.=$row[csf("prod_id")].",";
				$prod_wise_data[$row[csf("prod_id")]]["rcv_qnty"]=$row[csf("rcv_qnty")];
				$prod_wise_data[$row[csf("prod_id")]]["rcv_amt"]=$row[csf("rcv_amt")];
				
			}
			
			$all_rcv_id=chop($all_rcv_id,",");
			$all_rcv_trans_id=chop($all_rcv_trans_id,",");
			$all_product_id=chop($all_product_id,",");
			
			
			$issue_sql=sql_select("select min(a.id) as issue_id, min(b.issue_number) as issue_number, a.prod_id  
			from inv_transaction a, inv_issue_master b 
			where a.mst_id=b.id and a.transaction_type in(2,3) and a.transaction_date >='".str_replace("'","",$txt_receive_date)."' and a.status_active=1 and a.prod_id in($all_product_id)
			group by prod_id
			union all 
			select min(a.id) as issue_id, min(b.transfer_system_id) as issue_number, a.prod_id  
			from inv_transaction a, inv_item_transfer_mst b 
			where a.mst_id=b.id and a.transaction_type in(2,3) and a.transaction_date >='".str_replace("'","",$txt_receive_date)."' and a.status_active=1 and a.prod_id in($all_product_id)
			group by prod_id");
			$issue_data=array();
			foreach($issue_sql as $row)
			{
				$issue_data[$row[csf("prod_id")]]["issue_id"]=$row[csf("issue_id")];
				$issue_data[$row[csf("prod_id")]]["issue_number"]=$row[csf("issue_number")];
			}
			
			foreach($receive_data as $prod_id=>$rcv_val)
			{
				if($issue_data[$prod_id]["issue_id"]>0)
				{
					if($issue_data[$prod_id]["issue_id"]>$rcv_val)
					{
						$issue_num=$issue_data[$prod_id]["issue_number"];
						echo "20**Issue Number $issue_num Found, Product Id $prod_id , Delete Not Allow.";die;
					}
				}
			}
			
			
			$field_array_prod_update="avg_rate_per_unit*current_stock*stock_value*updated_by*update_date";
			$row_prod=sql_select( "select id, current_stock, avg_rate_per_unit, stock_value 
			from product_details_master where id in($all_product_id) and status_active=1 and is_deleted=0");
			foreach($row_prod as $row)
			{
				$prev_prod_qnty=$prod_wise_data[$row[csf("id")]]["rcv_qnty"];
				$prev_prod_amount=$prod_wise_data[$row[csf("id")]]["rcv_amt"];
				
				$curr_stock_qnty=($row[csf("current_stock")]-$prev_prod_qnty);
				if ($curr_stock_qnty != 0){
					$curr_stock_value=($row[csf("current_stock")]-$prev_prod_amount);
					$avg_rate_per_unit=0;
					if ($curr_stock_value != 0 && $curr_stock_qnty != 0) $avg_rate_per_unit=number_format(($curr_stock_value/$curr_stock_qnty),8,'.','');
					else $avg_rate_per_unit=0;
				} else {
					$curr_stock_value=0;
					$avg_rate_per_unit=0;
				}				
			
				$updateProdID_array[]=$row[csf("id")];
				$data_array_prod_update[$row[csf("id")]]=explode("*",("".$avg_rate_per_unit."*".$curr_stock_qnty."*".$curr_stock_value."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			}
			//echo "10**";print_r($updateProdID_array);print_r($data_array_prod_update);die;
			
			$order_wise_sql=sql_select("select prod_id, po_breakdown_id, quantity, order_amount from order_wise_pro_details where status_active=1 and is_deleted=0 and entry_form=24 and trans_id in($all_rcv_id)");
			$prod_order_data=array();
			foreach($order_wise_sql as $row)
			{
				$all_prod_ids[$row[csf("prod_id")]]=$row[csf("prod_id")];
				$all_order_ids[$row[csf("po_breakdown_id")]]=$row[csf("po_breakdown_id")];
				$prod_order_data[$row[csf("prod_id")]][$row[csf("po_breakdown_id")]]["quantity"]+=$row[csf("quantity")];
				$prod_order_data[$row[csf("prod_id")]][$row[csf("po_breakdown_id")]]["order_amount"]+=$row[csf("order_amount")];
			}
			$field_array_prod_order_update="avg_rate*stock_quantity*stock_amount*updated_by*update_date";
			$prod_order_sql=sql_select("select id, prod_id, po_breakdown_id, stock_quantity, stock_amount from order_wise_stock where status_active=1 and is_deleted=0 and prod_id in(".implode(",",$all_prod_ids).") and po_breakdown_id in(".implode(",",$all_order_ids).")");
			$avg_rate_per_unit=$curr_stock_qnty=$curr_stock_value=0;
			foreach($prod_order_sql as $row)
			{
				$prev_prod_ord_qnty=$prod_order_data[$row[csf("prod_id")]][$row[csf("po_breakdown_id")]]["quantity"];
				$prev_prod_ord_amount=$prod_order_data[$row[csf("prod_id")]][$row[csf("po_breakdown_id")]]["order_amount"];
				
				$curr_stock_qnty=($row[csf("stock_quantity")]-$prev_prod_ord_qnty);
				$curr_stock_value=($row[csf("stock_amount")]-$prev_prod_ord_amount);
				if($curr_stock_qnty > 0 && $curr_stock_value > 0)
				{
					$avg_rate_per_unit=0;
					if($curr_stock_value !=0 && $curr_stock_qnty !=0) $avg_rate_per_unit=number_format(($curr_stock_value/$curr_stock_qnty),8,'.','');
				}
				else
				{
					$avg_rate_per_unit=0;
				}
				
				$updateProdOrderID_array[]=$row[csf("id")];
				$data_array_prod_order_update[$row[csf("id")]]=explode("*",("".$avg_rate_per_unit."*".$curr_stock_qnty."*".$curr_stock_value."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			}
			
			$rID=$rID2=$rID3=$rID4=$rID5=$rID6=true;
			if(count($data_array_prod_update)>0)
			{
				$rID=execute_query(bulk_update_sql_statement("product_details_master","id",$field_array_prod_update,$data_array_prod_update,$updateProdID_array),1);
			}
			if(count($data_array_prod_order_update)>0)
			{
				$rID5=execute_query(bulk_update_sql_statement("order_wise_stock","id",$field_array_prod_order_update,$data_array_prod_order_update,$updateProdOrderID_array),1);
			}
			$rID6=execute_query("update inv_receive_master set status_active=0, is_deleted=1, updated_by='".$_SESSION['logic_erp']['user_id']."', update_date='".$pc_date_time."' where id=$update_id");
			$rID2=execute_query("update inv_transaction set status_active=0, is_deleted=1, updated_by='".$_SESSION['logic_erp']['user_id']."', update_date='".$pc_date_time."' where transaction_type=1 and mst_id=$update_id");
			$rID3=execute_query("update inv_trims_entry_dtls set status_active=0, is_deleted=1, updated_by='".$_SESSION['logic_erp']['user_id']."', update_date='".$pc_date_time."' where mst_id=$update_id");
			$rID4=execute_query("update order_wise_pro_details set status_active=0, is_deleted=1, updated_by='".$_SESSION['logic_erp']['user_id']."', update_date='".$pc_date_time."' where trans_id in($all_rcv_id) and trans_type=1 and entry_form=24");
			
			//echo "10**$rID && $rID2 && $rID3 && $rID4 && $rID5 && $rID6";die;
			
			if($db_type==0)
			{
				if($rID && $rID2 && $rID3 && $rID4 && $rID5 && $rID6)
				{
					mysql_query("COMMIT");  
					echo "2**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_recieved_id)."**0";
				}
				else
				{
					mysql_query("ROLLBACK"); 
					echo "7**0**0**1";
				}
			}
			else if($db_type==2 || $db_type==1 )
			{
				if($rID && $rID2 && $rID3 && $rID4 && $rID5 && $rID6)
				{
					oci_commit($con);  
					echo "2**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_recieved_id)."**0";
				}
				else
				{
					oci_rollback($con);
					echo "7**0**0**1";
				}
			}
			//check_table_status( $_SESSION['menu_id'],0);
			disconnect($con);
			die;
		}
	}
}



if ($action=="trims_receive_popup_search")
{
	echo load_html_head_contents("Trims Receive Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?> 

	<script>
	
		function js_set_value(id)
		{
			var ids= id.split("_");
			$('#hidden_recv_id').val(ids[0]);
			$('#hidden_posted_in_account').val(ids[1]);
			parent.emailwindow.hide();
		}
	
    </script>

	</head>

	<body>
	<div align="center" style="width:885px;">
		<form name="searchwofrm"  id="searchwofrm">
			<fieldset style="width:883px; margin-left:3px">
			<legend>Enter search words</legend>           
				<table cellpadding="0" cellspacing="0" width="820" class="rpt_table" border="1" rules="all">
					<thead>
						<th>Supplier</th>
						<th>Received Date Range</th>
						<th>Search By</th>
						<th id="search_by_td_up" width="200">Enter Received ID No</th>
						<th>
							<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
							<input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes" value="<? echo $cbo_company_id; ?>">
							<input type="hidden" name="hidden_recv_id" id="hidden_recv_id" class="text_boxes" value=""> 
							<input type="hidden" name="hidden_posted_in_account" id="hidden_posted_in_account" class="text_boxes" value=""> 
						</th> 
					</thead>
					<tr class="general">
						<td align="center">
							<?
								echo create_drop_down( "cbo_supplier_name", 150,"select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$cbo_company_id' and b.party_type in(4,5) and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name",'id,supplier_name', 1, '-- ALL Supplier --',0);
							?>       
						</td>
						<td align="center">
							<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">To
							<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
						</td>
						<td align="center">	
							<?
								$search_by_arr=array(1=>"Received ID",2=>"WO/PI",3=>"Challan No");
								$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../../') ";							
								echo create_drop_down( "cbo_search_by", 150, $search_by_arr,"",0, "--Select--", "",$dd,0 );
							?>
						</td>     
						<td align="center" id="search_by_td">				
							<input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
						</td> 						
						<td align="center">
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('cbo_supplier_name').value+'_'+document.getElementById('cbo_year_selection').value, 'create_trims_recv_search_list_view', 'search_div', 'trims_receive_multi_ref_entry_v3_controller', 'setFilterGrid(\'list_view\',-1);')" style="width:100px;" />
						</td>
					</tr>
					<tr>
						<td colspan="5" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
					</tr>
			</table>
			<div style="width:100%; margin-top:5px; margin-left:2px" id="search_div" align="left"></div>
			</fieldset>
		</form>
	</div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}

if($action=="create_trims_recv_search_list_view")
{
	$data = explode("_",$data);
	
	$search_string="%".trim($data[0])."%";
	$search_by=$data[1];
	$start_date =$data[2];
	$end_date =$data[3];
	$company_id =$data[4];
	$supplier_id =$data[5];
	$cbo_year =$data[6];
	
	if($supplier_id==0) $supplier_name="%%"; else $supplier_name=$supplier_id;
	$com_arr = return_library_array("select id, company_name from lib_company","id","company_name");
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");
	$store_arr = return_library_array("select id, store_name from lib_store_location","id","store_name");
	$user_name_arr = return_library_array("select id, user_name from user_passwd","id","user_name");
	
	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and a.receive_date between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$date_cond="and a.receive_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";	
		}
	}
	else
	{
		if($db_type==0){ $year_cond=" and year(a.insert_date)=$cbo_year";}
		else if($db_type==2){ $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";}
	}
	

	if(trim($data[0])!="")
	{
		if($search_by==1)	
			$search_field_cond="and a.recv_number like '$search_string'";
		else if($search_by==2)
			$search_field_cond="and a.booking_no like '$search_string'";
		else	
			$search_field_cond="and a.challan_no like '$search_string'";
	}
	else
	{
		$search_field_cond="";
	}
	
	if($db_type==0){ $year_field="YEAR(a.insert_date) as year"; }
	else if($db_type==2){ $year_field="to_char(a.insert_date,'YYYY') as year"; }
	else{ $year_field="";}//defined Later
	
	$sql = "SELECT a.id, a.recv_number_prefix_num, $year_field, a.recv_number, a.receive_basis, a.supplier_id, a.store_id, a.source, a.currency_id, a.booking_no, a.receive_date, a.challan_no, a.challan_date, a.pay_mode, a.is_posted_account, a.inserted_by, sum(b.order_qnty) as order_qnty from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.entry_form=24 and a.is_multi=3 and a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id and a.supplier_id like '$supplier_name' $search_field_cond $date_cond $year_cond and b.transaction_type=1 and b.item_category=4 and b.status_active=1 and b.is_deleted=0 group by a.id, a.recv_number_prefix_num, a.insert_date, a.recv_number, a.receive_basis, a.supplier_id, a.store_id, a.source, a.currency_id, a.booking_no, a.receive_date, a.challan_no, a.challan_date, a.pay_mode, a.is_posted_account, a.inserted_by order by a.id desc"; 
	//echo $sql;
	
	//$arr=array(2=>$receive_basis_arr,3=>$supplier_arr,4=>$store_arr,8=>$currency,9=>$source);
	//echo create_list_view("list_view", "Received No,Year,Receive Basis,Supplier,Store,Receive date,Challan No,Challan Date,Currency,Source", "75,50,105,130,80,75,75,80,60","870","240",0, $sql, "js_set_value", "id", "", 1, "0,0,receive_basis,supplier_id,store_id,0,0,0,currency_id,source", $arr, "recv_number_prefix_num,year,receive_basis,supplier_id,store_id,receive_date,challan_no,challan_date,currency_id,source", "",'','0,0,0,0,0,3,0,3,0,0');
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1060" class="rpt_table">
        <thead>
			<tr>
				<th colspan="14"><?php echo create_drop_down("cbo_string_search_type", 130, $string_search_type, '', 1, "-- Searching Type --",4); ?></th>
			</tr>
			<tr>
				<th width="30">Sl</th>	
				<th width="75">Received No</th>
				<th width="40">Year</th>
				<th width="105">Receive Basis</th>
				<th width="100">WO/PI</th>
				<th width="120">Supplier</th>
				<th width="80">Store</th>
				<th width="75">Receive date</th>
				<th width="75">Challan No</th>
				<th width="75">Challan Date</th>
				<th width="70">MRR Qnty</th>
				<th width="55">Currency</th>
				<th width="60">Source</th>
				<th>Insert User</th>
			</tr>
        </thead>
	</table>
    <div style="width:1080px; max-height:240px; overflow-y:scroll" id="search_div" align="left">	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1060" class="rpt_table" id="list_view" align="left"> 
        	<tbody>
            </tbody> 
        	<?
            $i=1;
			$result=sql_select($sql);
            foreach($result as $row)
            {  
                if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
        		?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('id')]."_".$row[csf('is_posted_account')]; ?>');"> 
                    <td width="30" align="center"><p><? echo $i; ?></p></td>	
                    <td width="75"><p><? echo $row[csf('recv_number_prefix_num')]; ?>&nbsp;</p></td>
                    <td width="40" align="center"><p><? echo $row[csf('year')]; ?>&nbsp;</p></td>
                    <td width="105"><p><? echo $receive_basis_arr[$row[csf('receive_basis')]]; ?>&nbsp;</p></td>
					<td width="100"><p><? echo $row[csf('booking_no')]; ?>&nbsp;</p></td>
                    <td width="120" title="<? echo $row[csf('pay_mode')]."==".$row[csf('supplier_id')]; ?>"><p><? if($row[csf('pay_mode')] == 3 || $row[csf('pay_mode')] == 5) echo $com_arr[$row[csf('supplier_id')]]; else echo $supplier_arr[$row[csf('supplier_id')]]; ?>&nbsp;</p></td>
                    <td width="80"><p><? echo $store_arr[$row[csf('store_id')]]; ?>&nbsp;</p></td>
                    <td width="75" align="center"><p><? if($row[csf('receive_date')]!="" && $row[csf('receive_date')]!="0000-00-00") echo change_date_format($row[csf('receive_date')]); ?>&nbsp;</p></td>
                    <td width="75"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
                    <td width="75" align="center"><p><? if($row[csf('challan_date')]!="" && $row[csf('challan_date')]!="0000-00-00") echo change_date_format($row[csf('challan_date')]); ?>&nbsp;</p></td>
					<td width="70" align="right"><p><? echo number_format($row[csf('order_qnty')],4); ?>&nbsp;</p></td>
                    <td width="55" align="center"><p><? echo $currency[$row[csf('currency_id')]]; ?>&nbsp;</p></td>
                    <td width="60"><p><? echo $source[$row[csf('source')]]; ?>&nbsp;</p></td>
                    <td><p><? echo $user_name_arr[$row[csf('inserted_by')]]; ?>&nbsp;</p></td>
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

if($action=='populate_data_from_trims_recv')
{
	$data_array=sql_select("select id, recv_number, company_id, receive_basis, pay_mode, supplier_id, store_id, source, currency_id, challan_no, receive_date, challan_date, lc_no, exchange_rate, booking_id, booking_no, booking_without_order, gate_entry_no, gate_entry_date, remarks from inv_receive_master where id='$data'");//, booking_id, booking_no, booking_without_order
    $company_id=$data_array[0][csf("company_id")];
    $variable_inventory_sql=sql_select("select store_method, rack_balance from variable_settings_inventory  where company_name=$company_id and item_category_id=4 and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1");
    $store_method=$variable_inventory_sql[0][csf("store_method")];
    echo "document.getElementById('store_update_upto').value 			= '" . $store_method . "';\n";
    foreach ($data_array as $row)
	{ 
		echo "document.getElementById('txt_recieved_id').value 				= '".$row[csf("recv_number")]."';\n";
		echo "document.getElementById('txt_remarks').value 				    = '".$row[csf("remarks")]."';\n";
		echo "document.getElementById('cbo_company_id').value 				= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_receive_basis').value 			= '".$row[csf("receive_basis")]."';\n";
		echo "$('#cbo_company_id').attr('disabled','true')".";\n";
		echo "set_receive_basis(0);\n";
		echo "document.getElementById('txt_receive_date').value 			= '".change_date_format($row[csf("receive_date")])."';\n";
		
		$lc_no=return_field_value("lc_number","com_btb_lc_master_details","id='".$row[csf("lc_no")]."'");
		
		echo "document.getElementById('txt_receive_chal_no').value 			= '".$row[csf("challan_no")]."';\n";
		echo "document.getElementById('txt_challan_date').value 			= '".change_date_format($row[csf("challan_date")])."';\n";
		//echo "load_room_rack_self_bin('requires/trims_receive_multi_ref_entry_v3_controller*4', 'store','store_td', '".$row[csf('company_id')]."','"."',this.value);\n";
		echo "load_drop_down( 'requires/trims_receive_multi_ref_entry_v3_controller', '".$row[csf('company_id')]."', 'load_drop_down_store', 'store_td');\n";
		echo "document.getElementById('txt_lc_no').value 					= '".$lc_no."';\n";
		echo "document.getElementById('lc_id').value 						= '".$row[csf("lc_no")]."';\n";
		echo "document.getElementById('cbo_source').value 					= '".$row[csf("source")]."';\n";
		echo "document.getElementById('cbo_store_name').value 				= '".$row[csf("store_id")]."';\n";
		echo "document.getElementById('txt_booking_pi_id').value 				= '".$row[csf("booking_id")]."';\n";
		echo "document.getElementById('txt_booking_pi_no').value 				= '".$row[csf("booking_no")]."';\n";
		echo "document.getElementById('booking_without_order').value 			= '".$row[csf("booking_without_order")]."';\n";
		$material_source="";
		if($row[csf("receive_basis")]==2)
		{
			$material_source=return_field_value("fabric_source","wo_booking_mst","id='".$row[csf("booking_id")]."'","fabric_source");
		}
		echo "document.getElementById('meterial_source').value 			= '".$material_source."';\n";
		echo "$('#cbo_store_name').attr('disabled','true')".";\n";
		echo "document.getElementById('cbo_pay_mode').value 			= '".$row[csf("pay_mode")]."';\n";
		if($row[csf('pay_mode')] == 3 || $row[csf('pay_mode')] == 5)
		{
			echo "load_drop_down( 'requires/trims_receive_multi_ref_entry_v3_controller', '".$row[csf("company_id")]."'+'_'+'".$row[csf("pay_mode")]."', 'load_drop_down_supplier', 'supplier_td_id' );";
		}
		echo "document.getElementById('cbo_supplier_name').value 			= '".$row[csf("supplier_id")]."';\n";
		
		echo "document.getElementById('cbo_currency_id').value 				= '".$row[csf("currency_id")]."';\n";
		echo "document.getElementById('txt_exchange_rate').value 			= '".$row[csf("exchange_rate")]."';\n";
		echo "document.getElementById('txt_gate_entry').value 			= '".$row[csf("gate_entry_no")]."';\n";
		echo "document.getElementById('txt_gate_entry_date').value 			= '".change_date_format($row[csf("gate_entry_date")])."';\n";
		echo "document.getElementById('update_id').value 					= '".$row[csf("id")]."';\n";
		
		echo "set_button_status(1, '".$_SESSION['page_permission']."', '',1,0);\n";
		exit();
	}
}

if($action=="show_trims_listview")
{
	$item_arr = return_library_array("select id, item_name from lib_item_group","id","item_name");

	$sql="select mst_id, booking_id, booking_no, booking_without_order, material_source, sum(receive_qnty) as receive_qnty, sum(amount) as amount, sum(reject_receive_qnty) as reject_receive_qnty
	from inv_trims_entry_dtls 
	where mst_id='$data' and status_active = '1' and is_deleted = '0'
	group by mst_id, booking_id, booking_no, booking_without_order, material_source";
	$result=sql_select($sql);
?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="520" class="rpt_table">
        <thead>
        	<th width="50">Sl</th>	
            <th width="150">Booking No</th>
            <th width="100">Receive Quantity</th>
            <th width="100">Receive Amount</th>
            <th>Reject Quantity</th>
        </thead>
	</table>
	<div style="width:520px; max-height:200px; overflow-y:scroll" id="list_container_batch" align="left">	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="500" class="rpt_table" id="list_view">  
        <?
            $i=1;
            foreach($result as $row)
            {  
                if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
        	?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="load_details_data('<? echo $row[csf('booking_id')]; ?>','<? echo $row[csf('booking_no')]; ?>','<? echo $row[csf('booking_without_order')]; ?>','<? echo $row[csf('mst_id')]; ?>','<? echo $row[csf('material_source')]; ?>');"> 
                    <td width="50" align="center"><p><? echo $i; ?></p></td>
                    <td width="150"><p><? echo $row[csf('booking_no')]; ?></p></td>
                    <td width="100" align="right"><? echo number_format($row[csf('receive_qnty')],2); ?></td>
                    <td width="100" align="right"><? echo number_format($row[csf('amount')],2); ?></td>
                    <td align="right"><? echo number_format($row[csf('reject_receive_qnty')],2); ?>&nbsp;</p></td>
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

function return_domestic_rate($rate,$ile_cost,$exchange_rate,$conversion_factor)
{
	$rate_ile=$rate+$ile_cost;
	$rate_ile_exchange=$rate_ile*$exchange_rate;
	$doemstic_rate=$rate_ile_exchange/$conversion_factor;
	return $doemstic_rate;	
}

if ($action=="trims_receive_entry_print")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	 //print_r ($data);
	
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$supplier_library=return_library_array( "select id,supplier_name from lib_supplier", "id","supplier_name"  );
	$item_library=return_library_array( "select id, item_name from lib_item_group", "id", "item_name"  );
	$color_library=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	
	$sql="select id, recv_number, receive_basis, receive_date, booking_id, booking_no, challan_no, challan_date, lc_no, source, store_id, supplier_id, currency_id, exchange_rate, booking_without_order, gate_entry_no, gate_entry_date, remarks, pay_mode, inserted_by from inv_receive_master where id='$data[1]' and status_active=1 and is_deleted=0 and entry_form=24 ";
	//echo $sql;
	$dataArray=sql_select($sql);
	$booking_id=$dataArray[0][csf('booking_id')];

   ?>
  <div style="width:1300px; margin-left:5px;">
    <table width="1300" cellspacing="0" align="right" border="0" >
        <tr>
            <td colspan="7" align="center" style="font-size:xx-large"><strong><? echo $company_library[$data[0]]; ?></strong>
                <br><b style="font-size:13px">
                <?
                $nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]"); 
                foreach ($nameArray as $result)
                { 
                    echo $result[csf('plot_no')].', ';  
                    echo $result[csf('level_no')].', ';
                    echo $result[csf('road_no')].', ';  
                    echo $result[csf('block_no')].', '; 
                    echo $result[csf('city')].', '; 
                    echo $result[csf('zip_code')].', ';  
                    echo $result[csf('province')].', '; 
                    echo $country_arr[$result[csf('country_id')]]; 
                    
                }
                ?>
                </b>
            </td>
        </tr>
		       
        <tr>
            <td colspan="7" align="center" style="font-size:x-large"><strong><u> Material Received Report</u></strong></center></td>
        </tr>
        <tr> 
            <td width="160"><strong>System ID:</strong></td> <td width="175px"><? echo $dataArray[0][csf('recv_number')]; ?></td>
            <td width="120"><strong> Receive Basis :</strong></td><td width="175px" ><? echo $receive_basis_arr[$dataArray[0][csf('receive_basis')]]; ?></td>
            <td width="125"><strong> Received Date:</strong></td><td width="175px"><? echo  change_date_format($dataArray[0][csf('receive_date')]); ?></td>
        </tr>
        <tr>
            <!--<td><strong>WO/PI:</strong></td> <td width="175px"><?echo $dataArray[0][csf('booking_no')]; ?></td>-->
            <td><strong>Challan No :</strong></td><td width="175px" ><? echo $dataArray[0][csf('challan_no')]; ?></td>
            <td><strong> Challan Date:</strong></td><td width="175px"><? echo  change_date_format($dataArray[0][csf('challan_date')]); ?></td>
            <td><strong> Source:</strong></td><td width="175px"><? echo $source[$dataArray[0][csf('source')]]; ?></td>
        </tr>
        <tr>
            <td><strong>Supplier:</strong></td><td width="175px"><? if($dataArray[0][csf('pay_mode')]==3 || $dataArray[0][csf('pay_mode')]==5) echo $company_library[$dataArray[0][csf('supplier_id')]]; else echo $supplier_library[$dataArray[0][csf('supplier_id')]]; ?></td>
            <td><strong> Currency:</strong></td><td width="175px"><? echo $currency[$dataArray[0][csf('currency_id')]]; ?></td>
            <td><strong> Gate In No.:</strong></td><td width="175px"><? echo  $dataArray[0][csf('gate_entry_no')]; ?></td>
        </tr>
        <tr>
            <td><strong>Remarks:</strong></td><td colspan="3"><?  echo $dataArray[0][csf('remarks')];; ?></td>
			<td><strong> Gate Entry Date:</strong></td><td width="175px"><? echo change_date_format($dataArray[0][csf('gate_entry_date')]); ?></td>
        </tr>
    </table>
    <br>
	<div style="width:100%;">
        <table align="right" cellspacing="0" width="1300"  border="1" rules="all" class="rpt_table" >
            <thead bgcolor="#dddddd" align="center">
                <th width="30">SL</th>
                <th width="80" align="center">WO/PI No.</th>
                <th width="80" align="center">Item Group</th>
                <th width="80" align="center">Item Des.</th>
                <th width="70" align="center">Gmts Color</th>
                <th width="70" align="center">Item Color</th>
                <th width="70" align="center">Item Size</th>
                <th width="70" align="center">Buyer name</th>
                <th width="100" align="center">Buyer Order</th>
                 <th width="70" align="center">Internal Ref. No</th>
                <th width="40" align="center">UOM</th>
                <th width="70" align="center">WO. Qty </th>
                <th width="70" align="center">Curr. Rec. Qty </th>
                <th width="60" align="center">Rate</th>
                <th width="70" align="center">Amount</th>
                <th width="70" align="center">Total Recv. Qty.</th>
                <th width="70" align="center">Balance Qty.</th>
                <th width="50" align="center">Reject Qty</th>
                <!--<th width="60" align="center">Rack</th>
                <th width="60" align="center">Shelf</th>
                <th width="60" align="center">Box</th>-->
            </thead>
    <?
		$mst_id=$dataArray[0][csf('id')];
		$booking_nos=''; $booking_sam_nos=''; $pi_ids=''; $orderIds='';
		//echo "select booking_no, booking_id, booking_without_order, order_id from inv_trims_entry_dtls where mst_id='$mst_id' and status_active='1' and is_deleted='0'";
		$dtls_data=sql_select("select booking_no, booking_id, booking_without_order, order_id from inv_trims_entry_dtls where mst_id='$mst_id' and status_active='1' and is_deleted='0'");
		foreach($dtls_data as $row)
		{
			$orderIds.=$row[csf('order_id')].",";
			
			if($dataArray[0][csf('receive_basis')]==1)
			{
				$pi_ids.=$row[csf('booking_id')].",";
			}
			else if($dataArray[0][csf('receive_basis')]==12)
			{
				$booking_nos.="'".$row[csf('booking_no')]."',";
			}
			else if($dataArray[0][csf('receive_basis')]==2)
			{
				if($row[csf('booking_without_order')]==1)
				{
					$booking_sam_nos.="'".$row[csf('booking_no')]."',";
				}
				else
				{
					$booking_nos.="'".$row[csf('booking_no')]."',";
				}
			}
		}
		
		$orderIds=chop($orderIds,','); 
		$piArray=array();
		//echo $orderIds.test;
		if($orderIds!="")
		{
			$orderIds=implode(",",array_unique(explode(",",$orderIds)));
			
			$piArray=array();
			$sql="select a.id, a.po_number, a.grouping as internal_ref from wo_po_break_down a, wo_po_details_master b where a.job_id=b.id and a.id in($orderIds)";
			//echo $sql;
			$po_data=sql_select($sql);
			foreach($po_data as $row)
			{				
				$piArray[$row[csf('id')]]['po_number']=$row[csf('po_number')];
				$piArray[$row[csf('id')]]['grouping']=$row[csf('internal_ref')];
				$piArray[$row[csf('id')]]['buyer_id']=$buyer_library[$row[csf('buyer_id')]];
			}
			
			//$piArray=return_library_array( "select id, po_number from wo_po_break_down where id in ($orderIds)", "id", "po_number"  );
		}
		//echo "<pre>";print_r($piArray);die;
		//echo $dataArray[0][csf('receive_basis')];die;
		if($dataArray[0][csf('receive_basis')]==2)
		{
			$recv_wo_data_arr=array();$recv_wo_data_arr_amt=array();
			$sql_recv = "select a.booking_no, b.order_id as po_id, b.item_group_id as item_group, b.item_description, b.gmts_color_id, b.item_color, b.item_size, a.recv_number, sum(c.quantity) as receive_qnty 
			from inv_receive_master a, inv_trims_entry_dtls b, order_wise_pro_details c
			where a.id=b.mst_id  and a.booking_no=b.booking_no and b.id=c.dtls_id and b.trans_id=c.trans_id and c.entry_form=24 and a.entry_form=24 and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.trans_type=1 and c.po_breakdown_id in($orderIds) and c.po_breakdown_id in(556,555) and a.booking_no='SCL1-TB-22-00321'
			group by a.recv_number, a.booking_no, b.item_group_id, b.item_description, b.gmts_color_id, b.item_color, b.item_size, b.order_id";
			//echo $sql_recv;//die;
			$recv_data=sql_select($sql_recv);
			foreach($recv_data as $row)
			{ //pre_cost_fabric_cost_dtls_id
				$po_id_arr=array_unique(explode(",",$row[csf('po_id')]));
				foreach($po_id_arr as $po)
				{
					$recv_wo_data_arr[$row[csf('booking_no')]][$po][$row[csf('item_group')]][$row[csf('item_description')]]['recv_no'].=$row[csf('recv_number')].',';
					$recv_wo_data_arr_amt[$row[csf('recv_number')]][$row[csf('booking_no')]][$po][$row[csf('item_group')]][$row[csf('item_description')]][$row[csf('gmts_color_id')]][$row[csf('item_color')]][$row[csf('item_size')]]['recv_qty']=$row[csf('receive_qnty')];		
				}
			}
			//echo "<pre>";print_r($recv_wo_data_arr_amt);die;
			
			
			$booking_nos=chop($booking_nos,','); $booking_sam_nos=chop($booking_sam_nos,',');
			//echo $booking_nos.kok;
			if($booking_nos!="")
			{
				$booking_sam_nos=implode(",",array_unique(explode(",",$booking_sam_nos)));
				//,b.po_break_down_id
				$sql_bookingqty = sql_select("select b.booking_no, b.trim_group as item_group, c.po_break_down_id, c.color_number_id, c.item_color, c.description, c.gmts_sizes, c.item_size, sum(c.cons) as wo_qnty
				from wo_booking_dtls b,wo_trim_book_con_dtls c 
				where b.id=c.wo_trim_booking_dtls_id and b.booking_no=c.booking_no and c.cons>0 and c.status_active=1 and c.is_deleted=0 and b.booking_no in($booking_nos) 
				group by b.booking_no, b.trim_group, c.po_break_down_id, c.color_number_id, c.item_color, c.description, c.gmts_sizes, c.item_size");
			}
			
			foreach($sql_bookingqty as $b_qty)
			{
				if($b_qty[csf('color_number_id')]=="") $b_qty[csf('color_number_id')]=0;
				if($b_qty[csf('item_color')]=="") $b_qty[csf('item_color')]=0;
				$booking_qty_arr[$b_qty[csf('booking_no')]][$b_qty[csf('po_break_down_id')]][$b_qty[csf('item_group')]][$b_qty[csf('color_number_id')]][$b_qty[csf('item_color')]][$b_qty[csf('description')]][$b_qty[csf('gmts_sizes')]][$b_qty[csf('item_size')]]+=$b_qty[csf('wo_qnty')];
				
			}
			
			if($booking_sam_nos!="")
			{
				$booking_sam_nos=implode(",",array_unique(explode(",",$booking_sam_nos)));
				$sql_bookingqtysam = sql_select("select a.booking_no, 0 as po_break_down_id, sum(a.trim_qty) as wo_qnty,a.trim_group as item_group,a.fabric_color as item_color,a.gmts_color as color_number_id,a.fabric_description as description, a.item_size, a.gmts_size 
				from wo_non_ord_samp_booking_dtls a 
				where a.booking_no in($booking_sam_nos) and a.status_active=1 and a.is_deleted=0 
				group by a.booking_no,b.po_break_down_id, a.trim_group, a.fabric_color, a.gmts_color, a.fabric_description, a.item_size, a.gmts_size ");	
			}
			foreach($sql_bookingqtysam as $b_qty)
			{
				if($b_qty[csf('color_number_id')]=="") $b_qty[csf('color_number_id')]=0;
				if($b_qty[csf('item_color')]=="") $b_qty[csf('item_color')]=0;
				$booking_qty_arr[$b_qty[csf('booking_no')]][$b_qty[csf('item_group')]][$b_qty[csf('color_number_id')]][$b_qty[csf('item_color')]][$b_qty[csf('description')]][$b_qty[csf('gmts_size')]][$b_qty[csf('item_size')]]+=$b_qty[csf('wo_qnty')];
			}
		}
		else if($dataArray[0][csf('receive_basis')]==1)
		{
			$pi_ids=chop($pi_ids,',');
			$sql_bookingqty = sql_select("select a.id, b.item_group, b.item_color, b.color_id as color_number_id, b.item_description as description, c.po_break_down_id, sum(b.quantity) as wo_qnty 
			from com_pi_master_details a, com_pi_item_details b, wo_booking_dtls c 
			where a.id=b.pi_id and b.work_order_dtls_id=c.id and a.id in($pi_ids) and b.status_active=1 and b.is_deleted=0 
			group by a.id, b.item_group, b.item_color, b.color_id, b.item_description, c.po_break_down_id");	
			foreach($sql_bookingqty as $b_qty)
			{
				if($b_qty[csf('color_number_id')]=="") $b_qty[csf('color_number_id')]=0;
				if($b_qty[csf('item_color')]=="") $b_qty[csf('item_color')]=0;
				$booking_qty_arr[$b_qty[csf('id')]][$b_qty[csf('item_group')]][$b_qty[csf('color_number_id')]][$b_qty[csf('item_color')]][$b_qty[csf('description')]]+=$b_qty[csf('wo_qnty')];
			}
		}
		else if($dataArray[0][csf('receive_basis')]==12)
		{ 
			$booking_nos=chop($booking_nos,',');	
			$sql_bookingqty = sql_select("select b.po_break_down_id,c.trim_group as item_group,b.booking_no, sum(b.requirment) as wo_qnty,b.item_color,b.gmts_sizes ,b.description from 
			wo_booking_mst a,wo_trim_book_con_dtls b,wo_booking_dtls c
			 where a.booking_no=b.booking_no and a.supplier_id=147 and a.item_category=4 and c.po_break_down_id=b.po_break_down_id and c.job_no=b.job_no and c.booking_no=b.booking_no and c.booking_type=2  and b.booking_no in($booking_nos) group by b.booking_no,b.po_break_down_id, c.trim_group, b.item_color, b.gmts_sizes, b.description");
			 	
			foreach($sql_bookingqty as $b_qty)
			{
				if($b_qty[csf('gmts_sizes')]=="") $b_qty[csf('gmts_sizes')]=0;
				if($b_qty[csf('item_color')]=="") $b_qty[csf('item_color')]=0;
				$booking_qty_arr[$b_qty[csf('booking_no')]][$b_qty[csf('po_break_down_id')]][$b_qty[csf('item_group')]][$b_qty[csf('gmts_sizes')]][$b_qty[csf('item_color')]][$b_qty[csf('description')]]=$b_qty[csf('wo_qnty')];
			}
			
			
		}

        $i=1;$total_rec_qty=0; $total_rec_balance_qty=0;
		$sql_dtls="SELECT b.booking_no, b.booking_id, b.booking_without_order, b.item_group_id, b.item_description, b.order_id, b.gmts_color_id, b.item_color, b.item_size, sum(b.cons_qnty) as cons_qnty, b.order_uom, b.cons_uom, sum(b.receive_qnty) as receive_qnty, max(b.rate) as rate, sum(b.amount) as amount, sum(b.reject_receive_qnty) as reject_receive_qnty, sum(c.quantity) as quantity, b.gmts_size_id
		from inv_trims_entry_dtls b, order_wise_pro_details c
	    where b.id=c.dtls_id and b.trans_id=c.trans_id and b.prod_id=c.prod_id and c.entry_form=24 and c.trans_type=1 and b.mst_id='$mst_id' AND b.booking_id = $booking_id and b.status_active='1' and b.is_deleted='0'
		group by b.booking_no, b.booking_id, b.booking_without_order, b.item_group_id, b.item_description, b.order_id, b.gmts_color_id, b.item_color, b.item_size, b.order_uom, b.cons_uom, b.gmts_size_id";
		
        // echo $sql_dtls;
        $sql_result=sql_select($sql_dtls);
        foreach($sql_result as $row)
        {
			//print_r($booking_qty_arr);
            if ($i%2==0)  
                $bgcolor="#E9F3FF";
            else
                $bgcolor="#FFFFFF";
                
                $order_id_arr=explode(",",$row[csf('order_id')]);
				
				$order_number=$buyer_name='';$recv_no_arr='';$grouping_number='';$grouping_number_arr=array();
				//echo "<pre>";print_r($piArray);
				$prev_recv_qty=0;
				foreach($order_id_arr as $po_id)
				{
					
					//echo $po_id."=".$piArray[$po_id]['po_number'];die;
					$order_number.=$piArray[$po_id]['po_number'].',';
					$buyer_name.=$piArray[$po_id]['buyer_id'].',';
					$grouping_number_arr[$piArray[$po_id]['grouping']]=$piArray[$po_id]['grouping'];
					$recv_no_arr=implode(",",array_unique(explode(",",$recv_wo_data_arr[$row[csf('booking_no')]][$po_id][$row[csf('item_group_id')]][$row[csf('item_description')]]['recv_no'])));
					
					$recv_id_arr=explode(",",$recv_no_arr);
					foreach($recv_id_arr as $recv_id)
					{
						if($recv_id!=$dataArray[0][csf('recv_number')])
						{
							$prev_recv_qty+=$recv_wo_data_arr_amt[$recv_id][$row[csf('booking_no')]][$po_id][$row[csf('item_group_id')]][$row[csf('item_description')]][$row[csf('gmts_color_id')]][$row[csf('item_color')]][$row[csf('item_size')]]['recv_qty'];
						}
					}
				}

				//echo $prev_recv_qty."-";
				$order_number=chop($order_number,',');
				//$grouping_number=chop($grouping_number,',');
				$grouping_number=implode(',',$grouping_number_arr);
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>">
                    <td><? echo $i; ?></td>
                    <td width="80" title="<?= $row[csf('order_id')]; ?>" style="word-break:break-all;"><p><? echo $row[csf('booking_no')]; ?></p></td>
                    <td width="80" style="word-break:break-all;"><p><? echo $item_library[$row[csf('item_group_id')]]; ?></p></td>
                    <td width="80" style="word-break:break-all;"><p><? echo $row[csf('item_description')]; ?></p></td>
                    <td width="70" style="word-break:break-all;"><p><? echo $color_library[$row[csf('gmts_color_id')]]; ?></p></td>
                    <td width="70" style="word-break:break-all;"><p><? echo $color_library[$row[csf('item_color')]]; ?></p></td>
                    <td width="70" style="word-break:break-all;"><p><? echo $row[csf('item_size')]; ?></p></td>
                    <td width="70" style="word-break:break-all;"><? echo rtrim($buyer_name,','); ?></td>
                    <td width="100" style="word-break:break-all;"><? echo $order_number; ?></td>
                    <td width="70" style="word-break:break-all;"><? echo $grouping_number; ?></td>
                    <td align="center"><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?></td>
                    <td align="right" title="<? echo $row[csf('booking_no')]."=".$row[csf('item_group_id')]."=".$row[csf('gmts_color_id')]."=".$row[csf('item_color')]."=".$des_dtls."=".$row[csf('gmts_size_id')]."=".$row[csf('item_size')];?>">
					<?
                        if($row[csf('gmts_size_id')]=="") $row[csf('gmts_size_id')]=0;
                        if($row[csf('gmts_color_id')]=="") $row[csf('gmts_color_id')]=0;
                        if($row[csf('item_color')]=="") $row[csf('item_color')]=0;							
                        $woorder_qty='';$woorder_qty=0;
                        $descrip_arr=explode(",",$row[csf('item_description')]);
                        $last_index=end(array_values($descrip_arr));
                        $last_index=str_replace("[","",$last_index);
                        $last_index=str_replace("]","",$last_index);
                        if(trim($last_index)=="BS") $des_dtls=chop($row[csf('item_description')],', [BS]'); else $des_dtls=$row[csf('item_description')];
                        if($dataArray[0][csf('receive_basis')]==1)
                        {
							$woorder_qty = $booking_qty_arr[$row[csf('booking_id')]][$row[csf('item_group_id')]][$row[csf('gmts_color_id')]][$row[csf('item_color')]][$des_dtls];
                        }
                        else if($dataArray[0][csf('receive_basis')]==12)
                        {
                            $woorder_qty=$booking_qty_arr[$row[csf('booking_no')]][$row[csf('order_id')]][$row[csf('item_group_id')]][$row[csf('gmts_size_id')]][$row[csf('item_color')]][$row[csf('item_description')]];
                        }
                        else
                        {
							foreach($order_id_arr as $po_id)
							{
								$woorder_qty += $booking_qty_arr[$row[csf('booking_no')]][$po_id][$row[csf('item_group_id')]][$row[csf('gmts_color_id')]][$row[csf('item_color')]][$des_dtls][$row[csf('gmts_size_id')]][$row[csf('item_size')]];
							}
							
                            
                        }
                        $total_woorder_qty+=$woorder_qty;
                        echo number_format($woorder_qty,2,".",""); 
                        $tot_recv_qty=$row[csf('quantity')]+$prev_recv_qty;
                        $tot_recv_balance=$woorder_qty-$tot_recv_qty;//$row[csf('receive_qnty')]+$prev_recv_qty;
                    ?>
                    </td>
                    <td align="right" title="<? echo $des_dtls; ?>"><? echo number_format($row[csf('quantity')],2,".",""); ?></td>
                    <td align="right"><? echo number_format($row[csf('rate')],6,'.',''); ?></td>
                    <td align="right"><? echo number_format($row[csf('quantity')]*$row[csf('rate')],2,'.',''); ?></td>
                    <td align="right" title="<? echo $row[csf('receive_qnty')]."=".$prev_recv_qty; ?>"><? echo number_format($tot_recv_qty,2,'.',''); ?></td>
                    <td align="right"><? echo number_format($tot_recv_balance,2,'.',''); ?></td>
                    <td align="right"><? echo number_format($row[csf('reject_receive_qnty')],2,'.',''); ?></td>
                </tr>
            <?
			$i++;
			$tot_rec_qty+=$row[csf('quantity')];
			$tot_amount+=$row[csf('quantity')]*$row[csf('rate')];
			$tot_reject_qty+=$row[csf('reject_receive_qnty')];
			$total_rec_qty+=$tot_recv_qty;
			$total_rec_balance_qty+=$tot_recv_balance;
        }
       ?>
            <tr bgcolor="#dddddd">
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td colspan="2" align="right"><b>Total :</b></td>
                <td align="right"><? echo number_format($total_woorder_qty,2,'.',''); ?></td>
                <td align="right"><? echo number_format($tot_rec_qty,2,'.',''); ?></td>
                <td>&nbsp;</td>
                <td align="right"><? echo number_format($tot_amount,2,'.',''); ?></td>
                <td align="right"><? echo number_format($total_rec_qty,2,'.',''); ?></td>
                <td align="right"><? echo number_format($total_rec_balance_qty,2,'.',''); ?></td>
                <td align="right"><? echo number_format($tot_reject_qty,2,'.',''); ?></td>

               <!-- <td align="center">&nbsp;</td>
                <td align="center">&nbsp;</td>
                <td align="center">&nbsp;</td>-->
            </tr>
       </table>
       <br>
       <?
		  echo signature_table(35, $data[0], "980px",'','',$dataArray[0][csf('inserted_by')]);
	   ?>
	</div>
  </div>
   <?
  exit();
}

if ($action=="trims_receive_entry_print2")
{ 
	extract($_REQUEST);
	$data=explode('*',$data);
	 //print_r ($data);
	
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$user_arr=return_library_array( "select id,user_full_name from user_passwd", "id","user_full_name"  );
	$supplier_library=return_library_array( "select id,supplier_name from lib_supplier", "id","supplier_name"  );
	$buyer_nameArr=return_library_array( "select id,buyer_name from lib_buyer", "id","buyer_name"  );
	$item_library=return_library_array( "select id, item_name from lib_item_group", "id", "item_name"  );
	$color_library=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );
	$store_name=return_library_array( "select id, store_name from lib_store_location", "id", "store_name"  );
	$floor_room_rack_name=return_library_array( "select floor_room_rack_id, floor_room_rack_name from lib_floor_room_rack_mst", "floor_room_rack_id", "floor_room_rack_name"  );
	
	$sql="select id, recv_number, receive_basis, receive_date, booking_id, booking_no, gate_entry_no, challan_no, challan_date, lc_no, source, store_id, supplier_id, currency_id, remarks, exchange_rate, booking_without_order,gate_entry_date, pay_mode from inv_receive_master where id='$data[1]' and status_active=1 and is_deleted=0 and entry_form=24 ";
	//echo $sql;
	$dataArray=sql_select($sql);
	$booking_no=$dataArray[0][csf('booking_no')];
	$exchange_rate=$dataArray[0][csf('exchange_rate')];
	$booking_without_order=$dataArray[0][csf('booking_without_order')];
	$receive_basis_id=$dataArray[0][csf('receive_basis')];
	$booking_id=$dataArray[0][csf('booking_id')];
	if($receive_basis_id==2)
	{
		if($booking_without_order==0)
		{
			$sql_bookingqty = sql_select("select a.buyer_id,a.inserted_by,a.booking_no from wo_booking_mst a  where  a.item_category=4 and a.booking_type=2  and a.booking_no in('$booking_no') and a.status_active=1");
			$buyer_name=$buyer_nameArr[$sql_bookingqty[0][csf('buyer_id')]];
			$inserted_by=$sql_bookingqty[0][csf('inserted_by')];
		}
		else
		{
			$sql_bookingqty = sql_select("select a.buyer_id,a.inserted_by,a.booking_no from wo_non_ord_samp_booking_mst a  where  a.item_category=4 and a.booking_type=5  and a.booking_no in('$booking_no') and a.status_active=1");
			$buyer_name=$buyer_nameArr[$sql_bookingqty[0][csf('buyer_id')]];
			$inserted_by=$sql_bookingqty[0][csf('inserted_by')];
		}
	}
	else
	{
			$sql_bookingqty =sql_select("select a.id,a.inserted_by, c.booking_no,d.buyer_id
			from com_pi_master_details a, com_pi_item_details b, wo_booking_dtls c,wo_booking_mst d 
			where a.id=b.pi_id and b.work_order_dtls_id=c.id and d.id=b.work_order_id and a.id in($booking_id) and b.status_active=1 and b.is_deleted=0 
			group by a.id,a.inserted_by, c.booking_no,d.buyer_id");	
			 
			foreach($sql_bookingqty as $row)
			{				 
				$buyer_name.=$buyer_nameArr[$row[csf('buyer_id')]].',';
				$inserted_by=$row[csf('inserted_by')];
			}
	}
	$buyer_name=rtrim($buyer_name,',');
	$buyer_name=implode(",",array_unique(explode(",",$buyer_name)));
			 
	
   ?>
  <div style="width:1550px;margin-left:2px;">
    <table width="1550" cellspacing="0" align="right" border="0" >
        <tr>
            <td colspan="7" align="center" style="font-size:xx-large"><strong><? echo $company_library[$data[0]]; ?></strong>
                <br><b style="font-size:13px">
                <?
                $nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]"); 
                foreach ($nameArray as $result)
                { 
                    echo $result[csf('plot_no')].', ';  
                    echo $result[csf('level_no')].', ';
                    echo $result[csf('road_no')].', ';  
                    echo $result[csf('block_no')].', '; 
                    echo $result[csf('city')].', '; 
                    echo $result[csf('zip_code')].', ';  
                    echo $result[csf('province')].', '; 
                    echo $country_arr[$result[csf('country_id')]]; 
                    
                }
                ?>
                </b>
            </td>
        </tr>
        
        <tr>
            <td colspan="7" align="center" style="font-size:x-large"><strong><u>Material Receiving Report (Trims)</u></strong></center></td>
        </tr>
        <tr>
            <td width="160"><strong>System ID:</strong></td> <td width="175px"><? echo $dataArray[0][csf('recv_number')]; ?></td>
            <td width="120"><strong> Receive Basis :</strong></td><td width="175px" ><? echo $receive_basis_arr[$dataArray[0][csf('receive_basis')]]; ?></td>
            <td width="125"><strong>Received Date:</strong></td><td width="175px"><? echo  change_date_format($dataArray[0][csf('receive_date')]); ?></td>
        </tr>
        <tr>
            <!--<td><strong>WO/PI:</strong></td> <td width="175px"><?echo $dataArray[0][csf('booking_no')]; ?></td>-->
            <td><strong>Challan No :</strong></td><td width="175px" ><? echo $dataArray[0][csf('challan_no')]; ?></td>
            <td><strong>Challan Date:</strong></td><td width="175px"><? echo  change_date_format($dataArray[0][csf('challan_date')]); ?></td>
            <td><strong>Source:</strong></td><td width="175px"><? echo $source[$dataArray[0][csf('source')]]; ?></td>
        </tr>
        <tr>
            <td><strong>Supplier:</strong></td><td width="175px"><? if($dataArray[0][csf('pay_mode')]==3 || $dataArray[0][csf('pay_mode')]==5) echo $company_library[$dataArray[0][csf('supplier_id')]]; else echo $supplier_library[$dataArray[0][csf('supplier_id')]]; ?></td>
            <td><strong> Currency:</strong></td><td width="175px"><? echo $currency[$dataArray[0][csf('currency_id')]]; ?></td>
			<td><strong> Store Name:</strong></td><td width="175px"><? echo $store_name[$dataArray[0][csf('store_id')]]; ?></td>
        </tr>
         <tr>
            <td><strong>WO/PI No:</strong></td><td width="175px"><?  echo $dataArray[0][csf('booking_no')]; ?></td>
            <td><strong> PO Create By:</strong></td><td width="175px"><? echo $user_arr[$inserted_by]; ?></td>
			<td><strong> Exchange Rate:</strong></td><td width="175px"><? echo $exchange_rate; ?></td>
        </tr>
         <tr>
            <td><strong>Buyer:</strong></td><td width="175px"><?  echo $buyer_name; ?></td>
            <td><strong> Gate In No.:</strong></td><td width="175px"><? echo  $dataArray[0][csf('gate_entry_no')]; ?></td>
			<td><strong> Gate Entry Date:</strong></td><td width="175px"><? echo  $dataArray[0][csf('gate_entry_date')]; ?></td>
         </tr>
        <tr>
            <td><strong>Remarks:</strong></td><td colspan="5"><?  echo $dataArray[0][csf('remarks')]; ?></td>
        </tr>
    </table>
    <br>
	<div style="width:100%; ">
        <table align="right" cellspacing="0" width="1620"  border="1" rules="all" class="rpt_table" >
            <thead bgcolor="#dddddd" align="center">
                <th width="30">SL</th>
                <th width="80" align="center">WO/PI No.</th>
                <th width="70" align="center">Item Group</th>
                <th width="80" align="center">Item Des.</th>
                <th width="70" align="center">Gmts Color</th>
                <th width="70" align="center">Item Color</th>
                <th width="60" align="center">Item Size</th>
                <th width="80" align="center">Style &nbsp;</th>
                <th width="100" align="center">Buyer Order</th>
                 <th width="60" align="center">Internal Ref. No</th>
                <th width="50" align="center">Floor</th>
                <th width="50" align="center">Room</th>
                <th width="50" align="center">Rack</th>
                <th width="50" align="center">Shelf</th>
                <th width="80" align="center">Bin/Box</th>
                <th width="40" align="center">UOM</th>
                <th width="70" align="center">WO. Qty </th>
                <th width="70" align="center">MRR Qty </th>
                <th width="70" align="center">Curr. Rec. Qty </th>
                <th width="60" align="center">Rate</th>
                <th width="70" align="center">Amount</th>
                <th width="70" align="center">Book Currency</th>
                <th width="70" align="center">Total Recv. Qty.</th>
                <th width="70" align="center">Balance Qty.</th>
                <th width="50" align="center">Reject Qty</th>
                <!--<th width="60" align="center">Rack</th>
                <th width="60" align="center">Shelf</th>
                <th width="60" align="center">Box</th>-->
            </thead>
    <? //book_keeping_curr
		$mst_id=$dataArray[0][csf('id')];
		$booking_nos=''; $booking_sam_nos=''; $pi_ids=''; $orderIds='';
		//echo "select booking_no, booking_id, booking_without_order, order_id from inv_trims_entry_dtls where mst_id='$mst_id' and status_active='1' and is_deleted='0'";
		$dtls_data=sql_select("select booking_no, booking_id, booking_without_order, order_id from inv_trims_entry_dtls where mst_id='$mst_id' and status_active='1' and is_deleted='0'");
		foreach($dtls_data as $row)
		{
			$orderIds.=$row[csf('order_id')].",";
			
			if($dataArray[0][csf('receive_basis')]==1)
			{
				$pi_ids.=$row[csf('booking_id')].",";
			}
			else if($dataArray[0][csf('receive_basis')]==12)
			{
				$booking_nos.="'".$row[csf('booking_no')]."',";
			}
			else if($dataArray[0][csf('receive_basis')]==2)
			{
				if($row[csf('booking_without_order')]==1)
				{
					$booking_sam_nos.="'".$row[csf('booking_no')]."',";
				}
				else
				{
					$booking_nos.="'".$row[csf('booking_no')]."',";
				}
			}
		}
		
		$orderIds=chop($orderIds,','); 
		$piArray=array();
		//echo $orderIds.test;
		if($orderIds!="")
		{
			$orderIds=implode(",",array_unique(explode(",",$orderIds)));
			
			$piArray=array();
			$sql="select a.id, a.po_number, a.grouping as internal_ref,b.style_ref_no from wo_po_break_down a,wo_po_details_master b where b.id=a.job_id and a.id in($orderIds)";
			// echo $sql;
			$po_data=sql_select($sql);
			foreach($po_data as $row)
			{
				
				$piArray[$row[csf('id')]]['po_number']=$row[csf('po_number')];
				$piArray[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
				$piArray[$row[csf('id')]]['grouping']=$row[csf('internal_ref')];
			}
			
			//$piArray=return_library_array( "select id, po_number from wo_po_break_down where id in ($orderIds)", "id", "po_number"  );
		}
		//echo "<pre>";print_r($piArray);die;
		//echo $dataArray[0][csf('receive_basis')];die;
		if($dataArray[0][csf('receive_basis')]==2)
		{
			
			$recv_wo_data_arr=array();$recv_wo_data_arr_amt=array();
			$sql_recv = "select a.booking_no, b.order_id as po_id, b.item_group_id as item_group, b.item_description, b.gmts_color_id, b.item_color, b.item_size, a.recv_number, sum(c.quantity) as receive_qnty 
			from inv_receive_master a, inv_trims_entry_dtls b, order_wise_pro_details c
			where a.id=b.mst_id  and a.booking_no=b.booking_no and b.id=c.dtls_id and b.trans_id=c.trans_id and c.entry_form=24 and a.entry_form=24 and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.trans_type=1 and c.po_breakdown_id in($orderIds) 
			group by a.recv_number, a.booking_no, b.item_group_id, b.item_description, b.gmts_color_id, b.item_color, b.item_size, b.order_id";
			//echo $sql_recv;//die;
			$recv_data=sql_select($sql_recv);
			foreach($recv_data as $row)
			{ //pre_cost_fabric_cost_dtls_id
				$po_id_arr=array_unique(explode(",",$row[csf('po_id')]));
				foreach($po_id_arr as $po)
				{
					$recv_wo_data_arr[$row[csf('booking_no')]][$po][$row[csf('item_group')]][$row[csf('item_description')]]['recv_no'].=$row[csf('recv_number')].',';
					$recv_wo_data_arr_amt[$row[csf('recv_number')]][$row[csf('booking_no')]][$po][$row[csf('item_group')]][$row[csf('item_description')]][$row[csf('gmts_color_id')]][$row[csf('item_color')]][$row[csf('item_size')]]['recv_qty']=$row[csf('receive_qnty')];		
				}
			}
			//echo "<pre>";
			//print_r($recv_wo_data_arr_amt);
			
			
			$booking_nos=implode(",",array_unique(explode(",",chop($booking_nos,',')))); 
			$booking_sam_nos=chop($booking_sam_nos,',');
			//echo $booking_nos.kok;
			if($booking_nos!="")
			{
				$booking_sam_nos=implode(",",array_unique(explode(",",$booking_sam_nos)));
				$sql_bookingqty = sql_select("select b.booking_no, b.po_break_down_id, sum(c.cons) as wo_qnty, b.trim_group as item_group, c.color_number_id, c.item_color, c.description, c.gmts_sizes, c.item_size 
				from wo_booking_dtls b,wo_trim_book_con_dtls c 
				where b.id=c.wo_trim_booking_dtls_id and b.status_active=1 and c.status_active=1 and c.cons>0 and b.booking_no in($booking_nos) 
				group by b.booking_no,b.po_break_down_id, b.trim_group, c.color_number_id, c.item_color, c.description, c.gmts_sizes, c.item_size");
			}
			
			foreach($sql_bookingqty as $b_qty)
			{
				if($b_qty[csf('color_number_id')]=="") $b_qty[csf('color_number_id')]=0;
				if($b_qty[csf('item_color')]=="") $b_qty[csf('item_color')]=0;
				if($b_qty[csf('gmts_sizes')]=="") $b_qty[csf('gmts_sizes')]=0;
				$booking_qty_arr[$b_qty[csf('booking_no')]][$b_qty[csf('po_break_down_id')]][$b_qty[csf('item_group')]][$b_qty[csf('color_number_id')]][$b_qty[csf('item_color')]][$b_qty[csf('description')]][$b_qty[csf('gmts_sizes')]][$b_qty[csf('item_size')]]=$b_qty[csf('wo_qnty')];
			}
			
			if($booking_sam_nos!="")
			{
				$booking_sam_nos=implode(",",array_unique(explode(",",$booking_sam_nos)));
				$sql_bookingqtysam = sql_select("select a.booking_no, 0 as po_break_down_id, sum(a.trim_qty) as wo_qnty,a.trim_group as item_group,a.fabric_color as item_color,a.gmts_color as color_number_id,a.fabric_description as description, a.item_size, a.gmts_size 
				from wo_non_ord_samp_booking_dtls a 
				where a.booking_no in($booking_sam_nos) and a.status_active=1 and a.is_deleted=0 
				group by a.booking_no,b.po_break_down_id, a.trim_group, a.fabric_color, a.gmts_color, a.fabric_description, a.item_size, a.gmts_size ");	
			}
			foreach($sql_bookingqtysam as $b_qty)
			{
				if($b_qty[csf('color_number_id')]=="") $b_qty[csf('color_number_id')]=0;
				if($b_qty[csf('item_color')]=="") $b_qty[csf('item_color')]=0;
				if($b_qty[csf('gmts_size')]=="") $b_qty[csf('gmts_size')]=0;
				$booking_qty_arr[$b_qty[csf('booking_no')]][$b_qty[csf('po_break_down_id')]][$b_qty[csf('item_group')]][$b_qty[csf('color_number_id')]][$b_qty[csf('item_color')]][$b_qty[csf('description')]][$b_qty[csf('gmts_size')]][$b_qty[csf('item_size')]]=$b_qty[csf('wo_qnty')];
			}
		}
		else if($dataArray[0][csf('receive_basis')]==1)
		{
			$pi_ids=chop($pi_ids,',');
			$sql_bookingqty = sql_select("select a.id, b.item_group, b.item_color, b.color_id as color_number_id, b.item_description as description, c.po_break_down_id, sum(b.quantity) as wo_qnty 
			from com_pi_master_details a, com_pi_item_details b, wo_booking_dtls c 
			where a.id=b.pi_id and b.work_order_dtls_id=c.id and a.id in($pi_ids) and b.status_active=1 and b.is_deleted=0 
			group by a.id, b.item_group, b.item_color, b.color_id, b.item_description, c.po_break_down_id");	
			foreach($sql_bookingqty as $b_qty)
			{
				if($b_qty[csf('color_number_id')]=="") $b_qty[csf('color_number_id')]=0;
				if($b_qty[csf('item_color')]=="") $b_qty[csf('item_color')]=0;
				$booking_qty_arr[$b_qty[csf('id')]][$b_qty[csf('po_break_down_id')]][$b_qty[csf('item_group')]][$b_qty[csf('color_number_id')]][$b_qty[csf('item_color')]][$b_qty[csf('description')]]=$b_qty[csf('wo_qnty')];
			}
		}
		else if($dataArray[0][csf('receive_basis')]==12)
		{ 
			$booking_nos=chop($booking_nos,',');	
			$sql_bookingqty = sql_select("select b.po_break_down_id,c.trim_group as item_group,b.booking_no, sum(b.requirment) as wo_qnty,b.item_color,b.gmts_sizes ,b.description from 
			wo_booking_mst a,wo_trim_book_con_dtls b,wo_booking_dtls c
			 where a.booking_no=b.booking_no and a.supplier_id=147 and a.item_category=4 and c.po_break_down_id=b.po_break_down_id and c.job_no=b.job_no and c.booking_no=b.booking_no and c.booking_type=2  and b.booking_no in($booking_nos) group by b.booking_no,b.po_break_down_id, c.trim_group, b.item_color, b.gmts_sizes, b.description");
			 	
			foreach($sql_bookingqty as $b_qty)
			{
				if($b_qty[csf('gmts_sizes')]=="") $b_qty[csf('gmts_sizes')]=0;
				if($b_qty[csf('item_color')]=="") $b_qty[csf('item_color')]=0;
				if($b_qty[csf('gmts_sizes')]=="") $b_qty[csf('gmts_sizes')]=0;
				$booking_qty_arr[$b_qty[csf('booking_no')]][$b_qty[csf('po_break_down_id')]][$b_qty[csf('item_group')]][$b_qty[csf('gmts_sizes')]][$b_qty[csf('item_color')]][$b_qty[csf('description')]]=$b_qty[csf('wo_qnty')];
			}
			
			
		}


        $i=1;$total_rec_qty=0; $total_rec_balance_qty=0;
		 $sql_dtls="select b.booking_no, b.booking_id, b.booking_without_order, b.item_group_id, b.item_description, b.order_id, b.gmts_color_id, b.item_color, b.item_size, sum(b.cons_qnty) as cons_qnty, b.cons_uom, sum(case when b.PAYMENT_OVER_RECV=0 then b.receive_qnty else 0 end) as receive_qnty_payment, sum(b.receive_qnty) as receive_qnty, sum(b.book_keeping_curr) as book_keeping_curr, max(b.rate) as rate, sum(b.amount) as amount, sum(b.reject_receive_qnty) as reject_receive_qnty, b.gmts_size_id, b.floor, b.room_no, b.rack_no, b.self_no, b.box_bin_no 
		from inv_trims_entry_dtls b 
		where b.mst_id='$mst_id' and b.status_active='1' and b.is_deleted='0'
		group by b.booking_no, b.booking_id, b.booking_without_order, b.item_group_id, b.item_description, b.order_id, b.gmts_color_id, b.item_color, b.item_size, b.cons_uom, b.gmts_size_id, b.floor, b.room_no, b.rack_no, b.self_no, b.box_bin_no";
		
        //echo $sql_dtls;
        $sql_result=sql_select($sql_dtls);
        foreach($sql_result as $row)
        {
			//print_r($booking_qty_arr);
            if ($i%2==0)  
                $bgcolor="#E9F3FF";
            else
                $bgcolor="#FFFFFF";
                
                $order_id_arr=explode(",",$row[csf('order_id')]);
				
				$order_number='';	$style_ref_no='';$recv_no_arr='';$grouping_number='';$grouping_number_arr=array();
				//echo "<pre>";print_r($piArray);
				foreach($order_id_arr as $po_id)
				{
					$prev_recv_qty=0;
					//echo $po_id."=".$piArray[$po_id]['po_number'];die;
					$order_number.=$piArray[$po_id]['po_number'].',';
					$style_ref_no.=$piArray[$po_id]['style_ref_no'].',';
					$grouping_number_arr[$piArray[$po_id]['grouping']]=$piArray[$po_id]['grouping'];
					$recv_no_arr=implode(",",array_unique(explode(",",$recv_wo_data_arr[$row[csf('booking_no')]][$po_id][$row[csf('item_group_id')]][$row[csf('item_description')]]['recv_no'])));
					
					$recv_id_arr=explode(",",$recv_no_arr);
					foreach($recv_id_arr as $recv_id)
					{
						if($recv_id!=$dataArray[0][csf('recv_number')])
						{
							$prev_recv_qty+=$recv_wo_data_arr_amt[$recv_id][$row[csf('booking_no')]][$po_id][$row[csf('item_group_id')]][$row[csf('item_description')]][$row[csf('gmts_color_id')]][$row[csf('item_color')]][$row[csf('item_size')]]['recv_qty'];
						}
					}
				}
				//echo $prev_recv_qty;
				$order_number=chop($order_number,',');
				$order_number=implode(",",array_unique(explode(",",$order_number)));
				//$grouping_number=chop($grouping_number,',');
				$grouping_number=implode(',',$grouping_number_arr);
				
				$style_ref_no=chop($style_ref_no,',');
				$style_ref_no=implode(",",array_unique(explode(",",$style_ref_no)));
				//$grouping_number=chop($grouping_number,',');
				//$style_ref_no=implode(',',$style_ref_no);
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>">
                    <td><? echo $i; ?></td>
                    <td width="80" style="word-break:break-all;" title="<?= $row[csf('order_id')]; ?>"><p><? echo $row[csf('booking_no')]; ?></p></td>
                    <td width="70" style="word-break:break-all;"><p><? echo $item_library[$row[csf('item_group_id')]]; ?></p></td>
                    <td width="80" style="word-break:break-all;"><p><? echo $row[csf('item_description')]; ?></p></td>
                    <td width="70" style="word-break:break-all;"><p><? echo $color_library[$row[csf('gmts_color_id')]]; ?></p></td>
                    <td width="70" style="word-break:break-all;"><p><? echo $color_library[$row[csf('item_color')]]; ?></p></td>
                    <td width="60" style="word-break:break-all;"><p><? echo $row[csf('item_size')]; ?></p></td>
                    <td width="80" style="word-break:break-all;"><? echo $style_ref_no; ?></td>
                    <td width="100" style="word-break:break-all;"><? echo $order_number; ?></td>
                    <td width="60" style="word-break:break-all;"><? echo $grouping_number; ?></td>
                    <td align="center"><? echo $floor_room_rack_name[$row[csf('floor')]]; ?></td>
                    <td align="center"><? echo $floor_room_rack_name[$row[csf('room_no')]]; ?></td>
                    <td align="center"><? echo $floor_room_rack_name[$row[csf('rack_no')]]; ?></td>
                    <td align="center"><? echo $floor_room_rack_name[$row[csf('self_no')]]; ?></td>
                    <td align="center"><? echo $floor_room_rack_name[$row[csf('box_bin_no')]]; ?></td>
                    <td align="center"><? echo $unit_of_measurement[$row[csf('cons_uom')]]; ?></td>
                    <td align="right">
					<?
                        if($row[csf('gmts_size_id')]=="") $row[csf('gmts_size_id')]=0;
                        if($row[csf('gmts_color_id')]=="") $row[csf('gmts_color_id')]=0;
                        if($row[csf('item_color')]=="") $row[csf('item_color')]=0;							
                        $woorder_qty='';
                        $descrip_arr=explode(",",$row[csf('item_description')]);
                        $last_index=end(array_values($descrip_arr));
                        $last_index=str_replace("[","",$last_index);
                        $last_index=str_replace("]","",$last_index);
                        if(trim($last_index)=="BS") $des_dtls=chop($row[csf('item_description')],', [BS]'); else $des_dtls=$row[csf('item_description')];
                        if($dataArray[0][csf('receive_basis')]==1)
                        {
							$order_ids=explode(",",$row[csf('order_id')]);
							foreach($order_ids as $val)
                            {
								$woorder_qty+=$booking_qty_arr[$row[csf('booking_id')]][$val][$row[csf('item_group_id')]][$row[csf('gmts_color_id')]][$row[csf('item_color')]][$des_dtls];
							}
                        }
                        else if($dataArray[0][csf('receive_basis')]==12)
                        {
                            $woorder_qty=$booking_qty_arr[$row[csf('booking_no')]][$row[csf('order_id')]][$row[csf('item_group_id')]][$row[csf('gmts_size_id')]][$row[csf('item_color')]][$row[csf('item_description')]];
                        }
                        else
                        {
                            $order_ids=explode(",",$row[csf('order_id')]);
                            foreach($order_ids as $val)
                            {
                                $woorder_qty+=$booking_qty_arr[$row[csf('booking_no')]][$val][$row[csf('item_group_id')]][$row[csf('gmts_color_id')]][$row[csf('item_color')]][$des_dtls][$row[csf('gmts_size_id')]][$row[csf('item_size')]];
                            }
                            
                        }
                        $total_woorder_qty+=$woorder_qty;
                        echo number_format($woorder_qty,2,".",""); 
                        $tot_recv_qty=$row[csf('receive_qnty')]+$prev_recv_qty;
                        $tot_recv_balance=$woorder_qty-$tot_recv_qty;//$row[csf('receive_qnty')]+$prev_recv_qty;
                    ?>
                    </td>
                    <td align="right" title="<? echo $des_dtls; ?>"><? echo number_format($row[csf('receive_qnty_payment')],2,".",""); ?></td>
                    <td align="right" title="<? echo $des_dtls; ?>"><? echo number_format($row[csf('receive_qnty')],2,".",""); ?></td>
                    <td align="right"><? echo number_format($row[csf('amount')]/$row[csf('receive_qnty_payment')],4,'.',''); ?></td>
                    <td align="right"><? echo number_format($row[csf('amount')],2,'.',''); ?></td>
                     <td align="right"><? echo number_format($row[csf('book_keeping_curr')],2,'.',''); ?></td>
                    <td align="right"><? echo number_format($tot_recv_qty,2,'.',''); ?></td>
                    <td align="right"><? echo number_format($tot_recv_balance,2,'.',''); ?></td>
                    <td align="right"><? echo number_format($row[csf('reject_receive_qnty')],2,'.',''); ?></td>
                   <!-- <td align="center"><p><? //echo $row[csf('rack_no')]; ?></p></td>
                    <td align="center"><p><? //echo $row[csf('self_no')]; ?></p></td>
                    <td align="center"><p><? //echo $row[csf('box_bin_no')]; ?></p></td>-->
                </tr>
            <?
			$i++;
			$tot_rec_qty+=$row[csf('receive_qnty')];
			$tot_amount+=$row[csf('amount')];
			$tot_reject_qty+=$row[csf('reject_receive_qnty')];
			$tot_book_keeping_curr+=$row[csf('book_keeping_curr')];
			$total_rec_qty+=$tot_recv_qty;
			$total_rec_balance_qty+=$tot_recv_balance;
        }
       ?>
            <tr bgcolor="#dddddd">
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
                <td colspan="2" align="right"><b>Total :</b></td>
                <td align="right"><? echo number_format($total_woorder_qty,2,'.',''); ?></td>
                <td>&nbsp;</td>
                <td align="right"><? echo number_format($tot_rec_qty,2,'.',''); ?></td>
                <td>&nbsp;</td>
                <td align="right"><? echo number_format($tot_amount,2,'.',''); ?></td>
                 <td align="right"><? echo number_format($tot_book_keeping_curr,2,'.',''); ?></td>
                <td align="right"><? echo number_format($total_rec_qty,2,'.',''); ?></td>
                <td align="right"><? echo number_format($total_rec_balance_qty,2,'.',''); ?></td>
                <td align="right"><? echo number_format($tot_reject_qty,2,'.',''); ?></td>
               <!-- <td align="center">&nbsp;</td>
                <td align="center">&nbsp;</td>
                <td align="center">&nbsp;</td>-->
            </tr>
       </table>
       <br>
       <?
		  echo signature_table(35, $data[0], "980px");
	   ?>
	</div>
  </div>
   <?
  exit();
}
if ($action=="trims_receive_entry_print3")
{ 
	extract($_REQUEST);
	$data=explode('*',$data);
	 //print_r ($data);
	$user_library=return_library_array( "select id, user_full_name from user_passwd", "id", "user_full_name"  );
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$supplier_library=return_library_array( "select id,supplier_name from lib_supplier where status_active=1", "id","supplier_name"  );
	$buyer_nameArr=return_library_array( "select id,buyer_name from lib_buyer where status_active=1", "id","buyer_name"  );
	$item_library=return_library_array( "select id, item_name from lib_item_group where status_active=1 and item_category=4", "id", "item_name"  );
	$color_library=return_library_array( "select id, color_name from lib_color where status_active=1", "id", "color_name"  );
	$size_library=return_library_array( "select id, size_name from lib_size where status_active=1", "id", "size_name"  );
	$store_name=return_library_array( "select id, store_name from lib_store_location", "id", "store_name"  );
	$floor_room_rack_name=return_library_array( "select floor_room_rack_id, floor_room_rack_name from lib_floor_room_rack_mst", "floor_room_rack_id", "floor_room_rack_name"  );
	
	$sql="select id, recv_number, receive_basis, receive_date, booking_id, booking_no, gate_entry_no, challan_no, challan_date, lc_no, source, store_id, supplier_id, currency_id, remarks, exchange_rate, booking_without_order,gate_entry_date, pay_mode,inserted_by,insert_date from inv_receive_master where id='$data[1]' and status_active=1 and is_deleted=0 and entry_form=24 ";
	//echo $sql;
	$dataArray=sql_select($sql);
	$booking_no=$dataArray[0][csf('booking_no')];
	$inserted_by_user=$user_library[$dataArray[0][csf('inserted_by')]];
	$exchange_rate=$dataArray[0][csf('exchange_rate')];
	$booking_without_order=$dataArray[0][csf('booking_without_order')];
	$receive_basis_id=$dataArray[0][csf('receive_basis')];
	$booking_id=$dataArray[0][csf('booking_id')];
	if($receive_basis_id==2)
	{
		if($booking_without_order==0)
		{
			$sql_bookingqty = sql_select("select a.buyer_id,a.inserted_by,a.booking_no from wo_booking_mst a  where  a.item_category=4 and a.booking_type=2  and a.booking_no in('$booking_no') and a.status_active=1");
			$buyer_name=$buyer_nameArr[$sql_bookingqty[0][csf('buyer_id')]];
			$inserted_by=$sql_bookingqty[0][csf('inserted_by')];
		}
		else
		{
			$sql_bookingqty = sql_select("select a.buyer_id,a.inserted_by,a.booking_no from wo_non_ord_samp_booking_mst a  where  a.item_category=4 and a.booking_type=5  and a.booking_no in('$booking_no') and a.status_active=1");
			$buyer_name=$buyer_nameArr[$sql_bookingqty[0][csf('buyer_id')]];
			$inserted_by=$sql_bookingqty[0][csf('inserted_by')];
		}
	}
	else
	{
			$sql_bookingqty =sql_select("select a.id,a.inserted_by, c.booking_no,d.buyer_id
			from com_pi_master_details a, com_pi_item_details b, wo_booking_dtls c,wo_booking_mst d 
			where a.id=b.pi_id and b.work_order_dtls_id=c.id and d.id=b.work_order_id and a.id in($booking_id) and b.status_active=1 and b.is_deleted=0 
			group by a.id,a.inserted_by, c.booking_no,d.buyer_id");	
			 
			foreach($sql_bookingqty as $row)
			{				 
				$buyer_name.=$buyer_nameArr[$row[csf('buyer_id')]].',';
				$inserted_by=$row[csf('inserted_by')];
			}
	}
	$buyer_name=rtrim($buyer_name,',');
	$buyer_name=implode(",",array_unique(explode(",",$buyer_name)));
			 
	
   ?>
  <div style="width:1610px;margin-left:2px;">
    <table width="1610" cellspacing="0" align="right" border="0" >
        <tr>
            <td colspan="7" align="center" style="font-size:xx-large"><strong><? echo $company_library[$data[0]]; ?></strong>
                <br><b style="font-size:13px">
                <?
                $nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]"); 
                foreach ($nameArray as $result)
                { 
                    echo $result[csf('plot_no')].', ';  
                    echo $result[csf('level_no')].', ';
                    echo $result[csf('road_no')].', ';  
                    echo $result[csf('block_no')].', '; 
                    echo $result[csf('city')].', '; 
                    echo $result[csf('zip_code')].', ';  
                    echo $result[csf('province')].', '; 
                    echo $country_arr[$result[csf('country_id')]]; 
                    
                }
                ?>
                </b>
            </td>
        </tr>
        
        <tr>
            <td colspan="7" align="center" style="font-size:x-large"><strong><u>Trims Receive Challan</u></strong></center></td>
        </tr>
        <tr>
            <td width="160"><strong>System ID:</strong></td> <td width="175px"><? echo $dataArray[0][csf('recv_number')]; ?></td>
            <td width="120"><strong> Receive Basis :</strong></td><td width="175px" ><? echo $receive_basis_arr[$dataArray[0][csf('receive_basis')]]; ?></td>
            <td width="125"><strong>Received Date:</strong></td><td width="175px"><? echo  change_date_format($dataArray[0][csf('receive_date')]); ?></td>
        </tr>
        <tr>
            <!--<td><strong>WO/PI:</strong></td> <td width="175px"><?echo $dataArray[0][csf('booking_no')]; ?></td>-->
            <td><strong>Challan No :</strong></td><td width="175px" ><? echo $dataArray[0][csf('challan_no')]; ?></td>
            <td><strong>Challan Date:</strong></td><td width="175px"><? echo  change_date_format($dataArray[0][csf('challan_date')]); ?></td>
            <td><strong>Source:</strong></td><td width="175px"><? echo $source[$dataArray[0][csf('source')]]; ?></td>
        </tr>
        <tr>
            <td><strong>Supplier:</strong></td><td width="175px"><? if($dataArray[0][csf('pay_mode')]==3 || $dataArray[0][csf('pay_mode')]==5) echo $company_library[$dataArray[0][csf('supplier_id')]]; else echo $supplier_library[$dataArray[0][csf('supplier_id')]]; ?></td>
            <td><strong> Currency:</strong></td><td width="175px"><? echo $currency[$dataArray[0][csf('currency_id')]]; ?></td>
			<td><strong> Store Name:</strong></td><td width="175px"><? echo $store_name[$dataArray[0][csf('store_id')]]; ?></td>
        </tr>
         <tr>
            <td><strong>WO/PI No:</strong></td><td width="175px"><?  echo $dataArray[0][csf('booking_no')]; ?></td>
            <td><strong> PO Create By:</strong></td><td width="175px"><? echo $user_library[$inserted_by]; ?></td>
			<td><strong> Exchange Rate:</strong></td><td width="175px"><? echo $exchange_rate; ?></td>
        </tr>
         <tr>
            <td><strong>Buyer:</strong></td><td width="175px"><?  echo $buyer_name; ?></td>
            <td><strong> Gate In No.:</strong></td><td width="175px"><? echo  $dataArray[0][csf('gate_entry_no')]; ?></td>
			<td><strong> Gate Entry Date:</strong></td><td width="175px"><? echo  $dataArray[0][csf('gate_entry_date')]; ?></td>
         </tr>
        <tr>
            <td><strong>Remarks:</strong></td><td colspan="5"><?  echo $dataArray[0][csf('remarks')]; ?></td>
        </tr>
    </table>
    <br>
	<div style="width:100%; ">
        <table align="right" cellspacing="0" width="1610"  border="1" rules="all" class="rpt_table" >
            <thead bgcolor="#dddddd" align="center">
                <th width="30">SL</th>
                <th width="80" align="center">WO/PI No.</th>
                <th width="70" align="center">Item Group</th>
                <th width="80" align="center">Item Des.</th>
                <th width="70" align="center">Gmts Color</th>
                <th width="70" align="center">Item Color</th>
                <th width="60" align="center">Gmts Size</th>
                <th width="60" align="center">Item Size</th>
                <th width="80" align="center">Style &nbsp;</th>
                <th width="100" align="center">Buyer Order</th>
				<th width="70" align="center">Brand/Sup Ref</th>
                <th width="50" align="center">Floor</th>
                <th width="50" align="center">Room</th>
                <th width="50" align="center">Rack</th>
                <th width="50" align="center">Shelf</th>
                <th width="80" align="center">Bin/Box</th>
                <th width="40" align="center">UOM</th>
                <th width="70" align="center">WO. Qty </th>
                <th width="70" align="center">Curr. Rec. Qty </th>
                <th width="60" align="center">Rate</th>
                <th width="70" align="center">Amount</th>
                <th width="70" align="center">Book Currency</th>
                <th width="70" align="center">Total Recv. Qty.</th>
                <th width="70" align="center">Balance Qty.</th>
                <th width="50" align="center">Reject Qty</th>
            </thead>
    <? //book_keeping_curr
		$mst_id=$dataArray[0][csf('id')];
		$booking_nos=''; $booking_sam_nos=''; $pi_ids=''; $orderIds='';
		//echo "select booking_no, booking_id, booking_without_order, order_id from inv_trims_entry_dtls where mst_id='$mst_id' and status_active='1' and is_deleted='0'";
		$dtls_data=sql_select("select booking_no, booking_id, booking_without_order, order_id from inv_trims_entry_dtls where mst_id='$mst_id' and status_active='1' and is_deleted='0'");
		foreach($dtls_data as $row)
		{
			$orderIds.=$row[csf('order_id')].",";
			
			if($dataArray[0][csf('receive_basis')]==1)
			{
				$pi_ids.=$row[csf('booking_id')].",";
			}
			else if($dataArray[0][csf('receive_basis')]==12)
			{
				$booking_nos.="'".$row[csf('booking_no')]."',";
			}
			else if($dataArray[0][csf('receive_basis')]==2)
			{
				if($row[csf('booking_without_order')]==1)
				{
					$booking_sam_nos.="'".$row[csf('booking_no')]."',";
				}
				else
				{
					$booking_nos.="'".$row[csf('booking_no')]."',";
				}
			}
		}
		
		$orderIds=chop($orderIds,','); 
		$piArray=array();
		//echo $orderIds.test;
		if($orderIds!="")
		{
			$orderIds=implode(",",array_unique(explode(",",$orderIds)));
			
			$piArray=array();
			$sql="select a.id, a.po_number, a.grouping as internal_ref,b.style_ref_no from wo_po_break_down a,wo_po_details_master b where b.id=a.job_id and a.id in($orderIds)";
			// echo $sql;
			$po_data=sql_select($sql);
			foreach($po_data as $row)
			{
				
				$piArray[$row[csf('id')]]['po_number']=$row[csf('po_number')];
				$piArray[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
				$piArray[$row[csf('id')]]['grouping']=$row[csf('internal_ref')];
			}
			
			//$piArray=return_library_array( "select id, po_number from wo_po_break_down where id in ($orderIds)", "id", "po_number"  );
		}
		//echo "<pre>";print_r($piArray);die;
		//echo $dataArray[0][csf('receive_basis')];die;
		if($dataArray[0][csf('receive_basis')]==2)
		{
			
			$recv_wo_data_arr=array();$recv_wo_data_arr_amt=array();
			$sql_recv = "select a.id as mst_id, a.booking_no, c.po_breakdown_id as po_id, b.item_group_id as item_group, b.item_description, b.gmts_color_id, b.item_color, b.gmts_size_id, b.item_size, a.recv_number, c.quantity as receive_qnty 
			from inv_receive_master a, inv_trims_entry_dtls b, order_wise_pro_details c
			where a.id=b.mst_id  and a.booking_no=b.booking_no and b.id=c.dtls_id and b.trans_id=c.trans_id and c.entry_form=24 and a.entry_form=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.trans_type=1 and c.po_breakdown_id in($orderIds) and a.id <> $mst_id";
			//echo $sql_recv;//die;
			$recv_data=sql_select($sql_recv);
			foreach($recv_data as $row)
			{ //pre_cost_fabric_cost_dtls_id
				$recv_wo_data_arr_amt[$row[csf('booking_no')]][$row[csf('po_id')]][$row[csf('item_group')]][$row[csf('item_description')]][$row[csf('gmts_color_id')]][$row[csf('item_color')]][$row[csf('gmts_size_id')]][$row[csf('item_size')]]['recv_qty']+=$row[csf('receive_qnty')];
			}
			
			//echo "<pre>";print_r($recv_wo_data_arr_amt);
			
			
			$booking_nos=chop($booking_nos,','); $booking_sam_nos=chop($booking_sam_nos,',');
			//echo $booking_nos.kok;
			if($booking_nos!="")
			{
				$booking_sam_nos=implode(",",array_unique(explode(",",$booking_sam_nos)));
				
				$sql_bookingqty = sql_select("select b.booking_no, b.po_break_down_id, c.cons as wo_qnty, b.trim_group as item_group, c.color_number_id, c.item_color, c.description, c.gmts_sizes, c.item_size 
				from wo_booking_dtls b,wo_trim_book_con_dtls c 
				where b.id=c.wo_trim_booking_dtls_id and b.status_active=1 and c.status_active=1 and b.booking_no in($booking_nos)");

			}
			$booking_qty_arr=array(); //FROM THIS WE REMOVE description
			foreach($sql_bookingqty as $b_qty)
			{
				if($b_qty[csf('color_number_id')]=="") $b_qty[csf('color_number_id')]=0;
				if($b_qty[csf('item_color')]=="") $b_qty[csf('item_color')]=0;
				if($b_qty[csf('gmts_sizes')]=="") $b_qty[csf('gmts_sizes')]=0;
				$booking_qty_arr[$b_qty[csf('booking_no')]][$b_qty[csf('po_break_down_id')]][$b_qty[csf('item_group')]][$b_qty[csf('color_number_id')]][$b_qty[csf('item_color')]][$b_qty[csf('gmts_sizes')]][$b_qty[csf('item_size')]][$b_qty[csf('description')]]+=$b_qty[csf('wo_qnty')];
			}
			
			if($booking_sam_nos!="")
			{
				$booking_sam_nos=implode(",",array_unique(explode(",",$booking_sam_nos)));
				$sql_bookingqtysam = sql_select("select a.booking_no, 0 as po_break_down_id, a.trim_qty as wo_qnty,a.trim_group as item_group,a.fabric_color as item_color,a.gmts_color as color_number_id,a.fabric_description as description, a.item_size, a.gmts_size 
				from wo_non_ord_samp_booking_dtls a 
				where a.booking_no in($booking_sam_nos) and a.status_active=1 and a.is_deleted=0");	
			}
			foreach($sql_bookingqtysam as $b_qty)
			{
				if($b_qty[csf('color_number_id')]=="") $b_qty[csf('color_number_id')]=0;
				if($b_qty[csf('item_color')]=="") $b_qty[csf('item_color')]=0;
				if($b_qty[csf('gmts_size')]=="") $b_qty[csf('gmts_size')]=0;
				$booking_qty_arr[$b_qty[csf('booking_no')]][$b_qty[csf('po_break_down_id')]][$b_qty[csf('item_group')]][$b_qty[csf('color_number_id')]][$b_qty[csf('item_color')]][$b_qty[csf('gmts_size')]][$b_qty[csf('item_size')]][$b_qty[csf('description')]]=$b_qty[csf('wo_qnty')];
			}
		}
		else if($dataArray[0][csf('receive_basis')]==1)
		{
			$pi_ids=chop($pi_ids,',');
			$sql_bookingqty = sql_select("select a.id, b.item_group, b.item_color, b.color_id as color_number_id, b.item_description as description, c.po_break_down_id, b.quantity as wo_qnty 
			from com_pi_master_details a, com_pi_item_details b, wo_booking_dtls c 
			where a.id=b.pi_id and b.work_order_dtls_id=c.id and a.id in($pi_ids) and b.status_active=1 and b.is_deleted=0");	
			foreach($sql_bookingqty as $b_qty)
			{
				if($b_qty[csf('color_number_id')]=="") $b_qty[csf('color_number_id')]=0;
				if($b_qty[csf('item_color')]=="") $b_qty[csf('item_color')]=0;
				$booking_qty_arr[$b_qty[csf('id')]][$b_qty[csf('po_break_down_id')]][$b_qty[csf('item_group')]][$b_qty[csf('color_number_id')]][$b_qty[csf('item_color')]][$b_qty[csf('description')]]=$b_qty[csf('wo_qnty')];
			}
		}
		else if($dataArray[0][csf('receive_basis')]==12)
		{ 
			$booking_nos=chop($booking_nos,',');	
			$sql_bookingqty = sql_select("select b.po_break_down_id,c.trim_group as item_group,b.booking_no, sum(b.requirment) as wo_qnty,b.item_color,b.gmts_sizes ,b.description from 
			wo_booking_mst a,wo_trim_book_con_dtls b,wo_booking_dtls c
			 where a.booking_no=b.booking_no and a.supplier_id=147 and a.item_category=4 and c.po_break_down_id=b.po_break_down_id and c.job_no=b.job_no and c.booking_no=b.booking_no and c.booking_type=2  and b.booking_no in($booking_nos) group by b.booking_no,b.po_break_down_id, c.trim_group, b.item_color, b.gmts_sizes, b.description");
			 	
			foreach($sql_bookingqty as $b_qty)
			{
				if($b_qty[csf('gmts_sizes')]=="") $b_qty[csf('gmts_sizes')]=0;
				if($b_qty[csf('item_color')]=="") $b_qty[csf('item_color')]=0;
				if($b_qty[csf('gmts_sizes')]=="") $b_qty[csf('gmts_sizes')]=0;
				$booking_qty_arr[$b_qty[csf('booking_no')]][$b_qty[csf('po_break_down_id')]][$b_qty[csf('item_group')]][$b_qty[csf('gmts_sizes')]][$b_qty[csf('item_color')]][$b_qty[csf('description')]]=$b_qty[csf('wo_qnty')];
			}
			
			
		}
        $i=1;$total_rec_qty=0; $total_rec_balance_qty=0;

		$sql_dtls="SELECT b.id as dtls_id, b.trans_id, b.booking_id, b.booking_no, b.booking_without_order, b.item_group_id, b.order_uom as uom, b.item_description, b.brand_supplier, b.gmts_color_id, b.item_color, b.gmts_size_id as size_id, b.item_size, b.rate, c.order_rate, c.payment_over_recv, b.material_source, c.prod_id, c.po_breakdown_id, c.quantity as quantity, c.reject_qty, b.floor, b.room_no, b.rack_no, b.self_no, b.box_bin_no, b.payment_over_recv,b.receive_qnty, c.po_breakdown_id as order_id, b.cons_qnty, b.cons_uom, b.book_keeping_curr, b.amount, b.reject_receive_qnty
		from inv_trims_entry_dtls b, order_wise_pro_details c
		where b.id=c.dtls_id and b.trans_id=c.trans_id and b.prod_id=c.prod_id and c.entry_form=24 and c.trans_type=1 and b.mst_id=$mst_id  and b.status_active=1 and c.status_active=1 and c.quantity>0
		order by b.item_description";
		
        // echo $sql_dtls;
        $sql_result=sql_select($sql_dtls);
		$runtime_rcv_qnty=array();
        foreach($sql_result as $row)
        {
			//print_r($booking_qty_arr);
            if ($i%2==0)  
                $bgcolor="#E9F3FF";
            else
                $bgcolor="#FFFFFF";
                
                $order_id_arr=explode(",",$row[csf('order_id')]);
				
				$order_number='';	$style_ref_no='';$recv_no_arr='';$grouping_number='';$grouping_number_arr=array();
				$prev_recv_qty=0;
				//echo $po_id."=".$piArray[$po_id]['po_number'];die;
				$order_number=$piArray[$row[csf('order_id')]]['po_number'];
				$style_ref_no=$piArray[$row[csf('order_id')]]['style_ref_no'];
				$grouping_number_arr[$piArray[$row[csf('order_id')]]['grouping']]=$piArray[$row[csf('order_id')]]['grouping'];
				$prev_recv_qty+=$recv_wo_data_arr_amt[$row[csf('booking_no')]][$row[csf('order_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]][$row[csf('gmts_color_id')]][$row[csf('item_color')]][$row[csf('size_id')]][$row[csf('item_size')]]['recv_qty'];
				//echo $prev_recv_qty;
				$grouping_number=implode(',',$grouping_number_arr);
				
				if($i>1 && !in_array(trim($row[csf('item_description')]), $descrip_check))
				{
					?>
                    <tr bgcolor="#FFFFCC">
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td colspan="2" align="right"><b>Sub-Total :</b></td>
                        <td align="right"><? echo number_format($sub_woorder_qty,2,'.',''); ?></td>
                        <td align="right"><? echo number_format($sub_rec_qty,2,'.',''); ?></td>
                        <td>&nbsp;</td>
                        <td align="right"><? echo number_format($sub_amount,2,'.',''); ?></td>
                         <td align="right"><? echo number_format($sub_book_keeping_curr,2,'.',''); ?></td>
                        <td align="right"><? echo number_format($sub_total_rec_qty,2,'.',''); ?></td>
                        <td align="right"><? echo number_format($sub_rec_balance_qty,2,'.',''); ?></td>
                        <td align="right"><? echo number_format($sub_reject_qty,2,'.',''); ?></td>
                    </tr>
                    <?
					$sub_woorder_qty=$sub_rec_qty=$sub_amount=$sub_book_keeping_curr=$sub_total_rec_qty=$sub_rec_balance_qty=$sub_reject_qty=0;
				}
				
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>">
                    <td><? echo $i; ?></td>
                    <td width="80" style="word-break:break-all;" title="<?= $row[csf('order_id')]; ?>"><p><? echo $row[csf('booking_no')]; ?></p></td>
                    <td width="70" style="word-break:break-all;"><p><? echo $item_library[$row[csf('item_group_id')]]; ?></p></td>
                    <td width="80" style="word-break:break-all;"><p><? echo $row[csf('item_description')]; ?></p></td>
                    <td width="70" style="word-break:break-all;" title="<?= $row[csf('gmts_color_id')];?>"><p><? echo $color_library[$row[csf('gmts_color_id')]]; ?></p></td>
                    <td width="70" style="word-break:break-all;" title="<?= $row[csf('item_color')];?>"><p><? echo $color_library[$row[csf('item_color')]]; ?></p></td>
                    <td width="60" style="word-break:break-all;" title="<?= $row[csf('size_id')];?>"><p><? echo $size_library[$row[csf('size_id')]]; ?></p></td>
                    <td width="60" style="word-break:break-all;"><p><? echo $row[csf('item_size')]; ?></p></td>
                    <td width="80" style="word-break:break-all;"><? echo $style_ref_no; ?></td>
                    <td width="100" style="word-break:break-all;"><? echo $order_number; ?></td>

					<td width="50" style="word-break:break-all;text-align:center;"><? echo $row[csf('brand_supplier')]; ?></td> 

                    <td align="center"><? echo $floor_room_rack_name[$row[csf('floor')]]; ?></td>
                    <td align="center"><? echo $floor_room_rack_name[$row[csf('room_no')]]; ?></td>
                    <td align="center"><? echo $floor_room_rack_name[$row[csf('rack_no')]]; ?></td>
                    <td align="center"><? echo $floor_room_rack_name[$row[csf('self_no')]]; ?></td>
                    <td align="center"><? echo $floor_room_rack_name[$row[csf('box_bin_no')]]; ?></td>
                    <td align="center"><? echo $unit_of_measurement[$row[csf('cons_uom')]]; ?></td>
                    <td align="right">
					<?
                        if($row[csf('gmts_size_id')]=="") $row[csf('gmts_size_id')]=0;
                        if($row[csf('gmts_color_id')]=="") $row[csf('gmts_color_id')]=0;
                        if($row[csf('item_color')]=="") $row[csf('item_color')]=0;							
                        $woorder_qty='';
                        $descrip_arr=explode(",",$row[csf('item_description')]);
                        $last_index=end(array_values($descrip_arr));
                        $last_index=str_replace("[","",$last_index);
                        $last_index=str_replace("]","",$last_index);
                        if(trim($last_index)=="BS") $des_dtls=chop($row[csf('item_description')],', [BS]'); else $des_dtls=$row[csf('item_description')];
						//[$row[csf('item_description')]] 
                        if($dataArray[0][csf('receive_basis')]==1)
                        {							
							$woorder_qty+=$booking_qty_arr[$row[csf('booking_id')]][$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][$row[csf('size_id')]][$row[csf('item_color')]][$row[csf('item_description')]];
							
                        }
                        else if($dataArray[0][csf('receive_basis')]==12)
                        {
                            $woorder_qty=$booking_qty_arr[$row[csf('booking_no')]][$row[csf('order_id')]][$row[csf('item_group_id')]][$row[csf('gmts_size_id')]][$row[csf('item_color')]][$row[csf('item_description')]];
                        }
                        else
                        {
							$woorder_qty+=$booking_qty_arr[$row[csf('booking_no')]][$row[csf('po_breakdown_id')]][$row[csf('item_group_id')]][$row[csf('gmts_color_id')]][$row[csf('item_color')]][$row[csf('size_id')]][$row[csf('item_size')]][$row[csf('item_description')]];
                            
                        }
                        
                        // $total_woorder_qty+=$row[csf('quantity')];
                         echo number_format($woorder_qty,2,".",""); 
						// echo number_format($row[csf('quantity')],2,".","");

						// echo $tot_recv_qty."__".$row[csf('receive_qnty')]."__".$prev_recv_qty;

                        $tot_recv_qty=$row[csf('receive_qnty')]+$prev_recv_qty;
						// echo $woorder_qty."__".$tot_recv_qty."________-";
                        //$tot_recv_balance=$woorder_qty-$row[csf('quantity')];//$row[csf('receive_qnty')]+$prev_recv_qty;
						$tot_recv_balance=$woorder_qty-($row[csf('quantity')]+$prev_recv_qty+$runtime_rcv_qnty[$row[csf('booking_no')]][$row[csf('order_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]][$row[csf('gmts_color_id')]][$row[csf('item_color')]][$row[csf('size_id')]][$row[csf('item_size')]]);
                    ?>
                    </td>
                    <td align="right"><? echo number_format($row[csf('quantity')],2,".",""); ?></td>
                    <td align="right"><? echo number_format($row[csf('rate')],4,'.',''); ?></td>
                    <td align="right"><? echo number_format($row[csf('rate')]*$row[csf('quantity')],2,'.',''); ?></td>
                     <td align="right"><? echo number_format(($row[csf('rate')]*$row[csf('quantity')])*$exchange_rate,2,'.',''); ?></td>
                    <td align="right"><? echo number_format($row[csf('quantity')]+$prev_recv_qty,2,'.',''); ?></td>
                    <td align="right" title="<?= "wo_qnty=$woorder_qty , current_qnty=".$row[csf('quantity')].", prev_receive=".$prev_recv_qty.", runtime_receive=".$runtime_rcv_qnty[$row[csf('booking_no')]][$row[csf('order_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]][$row[csf('gmts_color_id')]][$row[csf('item_color')]][$row[csf('size_id')]][$row[csf('item_size')]];?>"><? echo number_format($tot_recv_balance,2,'.',''); ?></td>
                    <td align="right"><? echo number_format($row[csf('reject_receive_qnty')],2,'.',''); ?></td>
                </tr>
            <?
			$descrip_check[trim($row[csf('item_description')])]=trim($row[csf('item_description')]);
			$runtime_rcv_qnty[$row[csf('booking_no')]][$row[csf('order_id')]][$row[csf('item_group_id')]][$row[csf('item_description')]][$row[csf('gmts_color_id')]][$row[csf('item_color')]][$row[csf('size_id')]][$row[csf('item_size')]]+=$row[csf('quantity')];
			$i++;
			$sub_woorder_qty+=$woorder_qty;
			$sub_rec_qty+=$row[csf('quantity')];
			$sub_amount+=$row[csf('rate')]*$row[csf('quantity')];
			$sub_book_keeping_curr+=$row[csf('rate')]*$row[csf('quantity')]*$exchange_rate;
			$sub_total_rec_qty+=$row[csf('quantity')]+$prev_recv_qty;
			$sub_rec_balance_qty+=$tot_recv_balance;
			$sub_reject_qty+=$row[csf('reject_receive_qnty')];
			
			$total_woorder_qty+=$woorder_qty;
			$tot_rec_qty+=$row[csf('quantity')];
			$tot_amount+=$row[csf('rate')]*$row[csf('quantity')];
			$tot_reject_qty+=$row[csf('reject_receive_qnty')];
			$tot_book_keeping_curr+=$row[csf('rate')]*$row[csf('quantity')]*$exchange_rate;
			$total_rec_qty+=$row[csf('quantity')]+$prev_recv_qty;
			// $total_rec_qty+=$tot_recv_qty;
			$total_rec_balance_qty+=$tot_recv_balance;
        }
       ?>
            <tr bgcolor="#FFFFCC">
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td colspan="2" align="right"><b>Sub-Total :</b></td>
                <td align="right"><? echo number_format($sub_woorder_qty,2,'.',''); ?></td>
                <td align="right"><? echo number_format($sub_rec_qty,2,'.',''); ?></td>
                <td>&nbsp;</td>
                <td align="right"><? echo number_format($sub_amount,2,'.',''); ?></td>
                 <td align="right"><? echo number_format($sub_book_keeping_curr,2,'.',''); ?></td>
                <td align="right"><? echo number_format($sub_total_rec_qty,2,'.',''); ?></td>
                <td align="right"><? echo number_format($sub_rec_balance_qty,2,'.',''); ?></td>
                <td align="right"><? echo number_format($sub_reject_qty,2,'.',''); ?></td>
            </tr>
            <tr bgcolor="#CCCCCC">
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
				<td>&nbsp;</td>
                <td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
                <td colspan="2" align="right"><b>Total :</b></td>
                <td align="right"><? echo number_format($total_woorder_qty,2,'.',''); ?></td>
                <td align="right"><? echo number_format($tot_rec_qty,2,'.',''); ?></td>
                <td>&nbsp;</td>
                <td align="right"><? echo number_format($tot_amount,2,'.',''); ?></td>
                 <td align="right"><? echo number_format($tot_book_keeping_curr,2,'.',''); ?></td>
                <td align="right"><? echo number_format($total_rec_qty,2,'.',''); ?></td>
                <td align="right"><? echo number_format($total_rec_balance_qty,2,'.',''); ?></td>
                <td align="right"><? echo number_format($tot_reject_qty,2,'.',''); ?></td>
            </tr>
       </table>
       <br>
       <?
		  echo signature_table(304, $data[0], "980px","","",$inserted_by_user."<br>".$dataArray[0][csf('insert_date')]);
	   ?>
	</div>
  </div>
   <?
  exit();
}
?>