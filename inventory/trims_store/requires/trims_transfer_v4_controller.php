<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//========== user credential start ========
$user_id = $_SESSION['logic_erp']['user_id'];
$userCredential = sql_select("SELECT unit_id as company_id,store_location_id FROM user_passwd where id=$user_id");
$company_id = $userCredential[0][csf('company_id')];
$store_location_id = $userCredential[0][csf('store_location_id')];

if ($company_id !='') {
    $company_credential_cond = "and comp.id in($company_id)";
}
if ($store_location_id !='') {
    $store_location_credential_cond = "and a.id in($store_location_id)"; 
}

// ========== user credential end ==========

if ($action=="load_room_rack_self_bin")
{
	$explodeData = explode('*', $data);
	//$explodeData[11] = 'storeUpdateUptoDisable()';
	$data=implode('*', $explodeData);
	load_room_rack_self_bin("requires/trims_transfer_v4_controller",$data);
}

if ($action=="upto_variable_settings")
{
	extract($_REQUEST);
	echo $variable_inventory=return_field_value("store_method","variable_settings_inventory","company_name='$cbo_company_id' and item_category_id=4 and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1");
	exit();
}

if($action == "load_drop_down_to_company_not_selected"){
    $companyIdArr = explode(',',$company_id);
    $companyIdArr = array_map('trim', $companyIdArr);
    unset($companyIdArr[array_search($data, $companyIdArr)]);
    $company_credential_cond = "and comp.id in (".implode(',',$companyIdArr).")";
    //echo "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_credential_cond order by comp.company_name";
    echo create_drop_down( "cbo_company_id_to", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_credential_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "if($('#cbo_company_id').val()*1 == this.value){alert('Same Company Transfer is not allowed!!'); $('#cbo_company_id_to').val('0'); return;}; load_drop_down( 'requires/trims_transfer_v4_controller',this.value, 'load_drop_down_location_to', 'to_location_td' );to_company_on_change(this.value);",1 );
    exit();
}

if($action == "load_drop_down_to_company"){
    $companyIdArr = explode(',',$company_id);
    $companyIdArr = array_map('trim', $companyIdArr);
    $company_credential_cond = "and comp.id in (".implode(',',$companyIdArr).")";
    //echo "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_credential_cond order by comp.company_name";
    echo create_drop_down( "cbo_company_id_to", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_credential_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "if($('#cbo_company_id').val()*1 == this.value){alert('Same Company Transfer is not allowed!!'); $('#cbo_company_id_to').val('0'); return;}; load_drop_down( 'requires/trims_transfer_v4_controller',this.value, 'load_drop_down_location_to', 'to_location_td' );to_company_on_change(this.value);",1 );
    exit();
}

if ($action=="load_drop_down_location")
{
	$data=explode("_",$data);
	echo create_drop_down( "cbo_location", 160, "select id,location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", 0, "load_room_rack_self_bin('requires/trims_transfer_v4_controller*4', 'store','from_store_td', $('#cbo_company_id').val(),this.value, '', '', '','', '', '','', '152');");
	//if( $('#cbo_transfer_criteria').val()*1==2 || $('#cbo_transfer_criteria').val()*1==4)  load_room_rack_self_bin('requires/trims_transfer_v4_controller*4*cbo_store_name_to', 'store','to_store_td', $('#cbo_company_id').val(),this.value);
	exit();
}

if ($action=="load_drop_down_location_to")
{
	$data=explode("_",$data);
	if($data[1]==2){
		//echo create_drop_down( "cbo_location_to", 160, "select id,location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", 0, "load_room_rack_self_bin('requires/finish_fabric_transfer_controller*2', 'store','to_store_td', $('#cbo_company_id').val(),this.value);",1 );
	}else{
		echo create_drop_down( "cbo_location_to", 160, "select id,location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", 0, "load_room_rack_self_bin('requires/trims_transfer_v4_controller*4*cbo_store_name_to', 'store','to_store_td', $('#cbo_company_id_to').val(),this.value, '', '', '','', '', '','', '152');" );
	}
	exit();
}





if ($action=="order_popup")
{
	echo load_html_head_contents("Order Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?> 
	<script>
		/*function js_set_value(data)
		{
			$('#order_id').val(data);
			parent.emailwindow.hide();
		}*/
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		var selected_id=new Array; var selected_name=new Array; var buyer_id_arr_chk=new Array;
		function js_set_value(id)
		{
			var str=id.split("*");
			var order_id=str[0];
			var style_no=str[1];
			var buyer_id=str[2];
			if(buyer_id_arr_chk.length==0)
			{
				buyer_id_arr_chk.push( buyer_id );
			}
			else if( jQuery.inArray( buyer_id, buyer_id_arr_chk )==-1 &&  buyer_id_arr_chk.length>0)
			{
				alert("Buyer Mixed is Not Allowed");
				return;
			}
			
			toggle( document.getElementById( 'tr_' + str[3] ), '#FFFFFF' );
			//alert(buyer_id);return;
		
			if( jQuery.inArray(  order_id , selected_id ) == -1 ) {
				selected_id.push( order_id );
				selected_name.push( style_no );
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == order_id  ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i,1 );
			}
			
			var id = '';
			var ddd='';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				ddd += selected_name[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			ddd = ddd.substr( 0, ddd.length - 1 );
			$('#hdn_order_id').val( id );
			$('#hdn_style_ref_no').val( ddd );
			$('#hdn_buyer_id').val( buyer_id );
		} 
		
    </script>
</head>
<body>
<div align="center" style="width:995px;">
	<form name="searchdescfrm"  id="searchdescfrm">
		<fieldset style="width:970px;">
        <legend>Enter search words</legend>
            <table cellpadding="0" cellspacing="0" width="950" class="rpt_table" border="1" rules="all">
                <thead>
                    <th>Buyer Name</th>
                    <th>Job No</th>
                    <th>Order No</th>
                    <th>Style Ref</th>
                    <th>Internal Ref</th>
                    <th width="230">Shipment Date Range</th>
                    <th>
                        <input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
                        <input type="hidden" name="hdn_order_id" id="hdn_order_id" class="text_boxes" value="">
                        <input type="hidden" name="hdn_style_ref_no" id="hdn_style_ref_no" class="text_boxes" value="">
                        <input type="hidden" name="hdn_buyer_id" id="hdn_buyer_id" class="text_boxes" value="">
                    </th>
                </thead>
                <tr class="general">
                    <td>
						<?
							echo create_drop_down( "cbo_buyer_name", 170, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "",$data[0] );
						?>
                    </td>
                    <td>
                        <input type="text" style="width:110px;" class="text_boxes" name="txt_job_no_fil" id="txt_job_no_fil" />
                    </td>
                    <td>
                        <input type="text" style="width:110px;" class="text_boxes" name="txt_order_no" id="txt_order_no" />
                    </td>
                    <td>
                        <input type="text" style="width:110px;" class="text_boxes" name="txt_style_ref" id="txt_style_ref" />
                    </td>
                    <td>                    
                        <input type="text" style="width:110px;" class="text_boxes" name="txt_internal_ref" id="txt_internal_ref" />
                    </td>
                    <td>
                    	<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" readonly>
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date" readonly>
                    </td>
                    <td>
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_order_no').value+'_'+<? echo $cbo_company_id; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+'<? echo $type; ?>'+'_'+<? echo $cbo_company_id_to;?>+'_'+document.getElementById('txt_internal_ref').value+'_'+document.getElementById('txt_job_no_fil').value+'_'+document.getElementById('txt_style_ref').value+'_'+document.getElementById('cbo_year_selection').value+'_'+'<? echo $cbo_store_name; ?>'+'_'+'<? echo $cbo_floor; ?>'+'_'+'<? echo $cbo_room; ?>'+'_'+'<? echo $txt_rack; ?>'+'_'+'<? echo $txt_shelf; ?>'+'_'+'<? echo $cbo_bin; ?>'+'_'+'<? echo $store_update_upto; ?>', 'create_po_search_list_view', 'search_div', 'trims_transfer_v4_controller', 'setFilterGrid(\'list_view\',-1);')" style="width:100px;" />
                    </td>
                </tr>
                <tr>
                    <td colspan="7" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
                </tr>
            </table>
        	<div style="margin-top:10px" id="search_div" align="left"></div> 
		</fieldset>
	</form>
</div>    
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=='create_po_search_list_view')
{
	$data=explode('_',$data);

	if ($data[0]==0) $buyer="%%"; else $buyer=$data[0];
	$search_string="%".trim($data[1])."%";
	//$internal_ref="%".trim($data[7])."%";
	$internal_ref=trim($data[7]);
	$cbo_store_name=trim($data[11]);
	$cbo_floor=trim($data[12]);
	$cbo_room=trim($data[13]);
	$txt_rack=trim($data[14]);
	$txt_shelf=trim($data[15]);
	$cbo_bin=trim($data[16]);
	$store_update_upto=trim($data[17]);
	
	$company_id=$data[2];
	if ($data[3]!="" &&  $data[4]!="")
	{
		if($db_type==0)
		{
			$shipment_date = "and b.pub_shipment_date between '".change_date_format($data[3], "yyyy-mm-dd", "-")."' and '".change_date_format($data[4], "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$shipment_date = "and b.pub_shipment_date between '".change_date_format($data[3],'','',1)."' and '".change_date_format($data[4],'','',1)."'";
		}
        $cbo_year_selection = "";
	}
	else{
        $shipment_date ="";
        $cbo_year_selection = " and to_char(b.insert_date, 'YYYY') = '$data[10]'";
    }
	$type=$data[5]; 
	$cbo_company_id_to=$data[6];
	if($type=="from") $company_cond=" and a.company_name=$company_id "; else $company_cond=" and a.company_name=$cbo_company_id_to";

	$company_arr = return_library_array( "select id, company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name" );
    $buyer_arr = return_library_array( "select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0", "id", "buyer_name" );
    $jobNoCond = $data[8] != '' ? " and a.job_no_prefix_num = '$data[8]'" : '';
    $styleRefCond = $data[9] != '' ? " and a.STYLE_REF_NO ='$data[9]'" : '';
	//$arr=array (2=>$company_arr,3=>$buyer_arr);
	if($db_type==0) $year_field="YEAR(a.insert_date) as YEAR"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as YEAR";
	else $year_field="";//defined Later
	if(str_replace("'","",$internal_ref) !="") $internal_ref_cond=" and b.grouping like '%$internal_ref%'";
	if($type=="from")
	{
		$status_cond=" and b.status_active in(1,3)";
		$str_conds="";//c.floor_id, c.room, c.rack, c.self, c.bin_box
		if($cbo_floor && $store_update_upto >1) $str_conds.=" and d.floor_id=$cbo_floor";
		if($cbo_room && $store_update_upto >2) $str_conds.=" and d.room=$cbo_room";
		if($txt_rack && $store_update_upto >3) $str_conds.=" and d.rack=$txt_rack";
		if($txt_shelf && $store_update_upto >4) $str_conds.=" and d.self=$txt_shelf";
		if($cbo_bin && $store_update_upto >5) $str_conds.=" and d.bin_box=$cbo_bin";
		
		$sql= "SELECT a.JOB_NO_PREFIX_NUM, $year_field, a.JOB_NO, a.COMPANY_NAME, a.BUYER_NAME, a.STYLE_REF_NO, a.JOB_QUANTITY, b.ID, b.pub_shipment_date as SHIPMENT_DATE, sum((case when c.trans_type in(1,4,5) then c.quantity else 0 end)-(case when c.trans_type in(2,3,6) then c.quantity else 0 end)) as BALANCE_QNTY
		from wo_po_details_master a, wo_po_break_down b, order_wise_pro_details c, inv_transaction d 
		where a.job_no=b.job_no_mst and b.id=c.po_breakdown_id and c.trans_id=d.id and c.entry_form in(24,25,49,73,78,112) and d.item_category=4 and a.buyer_name like '$buyer' and b.po_number like '$search_string' and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and d.store_id=$cbo_store_name $cbo_year_selection $jobNoCond $styleRefCond $company_cond $status_cond $shipment_date $internal_ref_cond $str_conds
		group by  a.JOB_NO_PREFIX_NUM, a.insert_date, a.JOB_NO, a.COMPANY_NAME, a.BUYER_NAME, a.STYLE_REF_NO, a.JOB_QUANTITY, b.ID, b.pub_shipment_date
		having sum((case when c.trans_type in(1,4,5) then c.quantity else 0 end)-(case when c.trans_type in(2,3,6) then c.quantity else 0 end)) >0 
		order by b.id desc, b.pub_shipment_date desc";
	}
	//echo $sql;die;
	$sql_res=sql_select($sql);
	$job_data=array();
	foreach($sql_res as $val)
	{
		$job_data[$val["JOB_NO"]]["JOB_NO_PREFIX_NUM"]=$val["JOB_NO_PREFIX_NUM"];
		$job_data[$val["JOB_NO"]]["YEAR"]=$val["YEAR"];
		$job_data[$val["JOB_NO"]]["JOB_NO"]=$val["JOB_NO"];
		
		$job_data[$val["JOB_NO"]]["COMPANY_NAME"]=$val["COMPANY_NAME"];
		$job_data[$val["JOB_NO"]]["BUYER_NAME"]=$val["BUYER_NAME"];
		$job_data[$val["JOB_NO"]]["STYLE_REF_NO"]=$val["STYLE_REF_NO"];
		
		$job_data[$val["JOB_NO"]]["COMPANY_NAME"]=$val["COMPANY_NAME"];
		$job_data[$val["JOB_NO"]]["BUYER_NAME"]=$val["BUYER_NAME"];
		$job_data[$val["JOB_NO"]]["STYLE_REF_NO"]=$val["STYLE_REF_NO"];		
		$job_data[$val["JOB_NO"]]["JOB_QUANTITY"]=$val["JOB_QUANTITY"];
		$job_data[$val["JOB_NO"]]["BALANCE_QNTY"]+=$val["BALANCE_QNTY"];
		
		if($po_id_check[$val["ID"]]=="")
		{
			$po_id_check[$val["ID"]]=$val["ID"];
			$job_data[$val["JOB_NO"]]["PO_ID"].=$val["ID"].",";
			$job_data[$val["JOB_NO"]]["SHIPMENT_DATE"]=$val["SHIPMENT_DATE"];
		}
		
	}
	// echo $sql;die;
	//echo create_list_view("list_view", "Job No,Internal Ref,Year,Company,Buyer Name,Style Ref. No,Job Qty.,PO number,PO Quantity,Shipment Date", "70,70,60,70,80,120,90,110,90,80","920","200",0, $sql , "js_set_value", "id", "", 1, "0,0,company_name,buyer_name,0,0,0,0", $arr , "job_no_prefix_num,grouping,year,company_name,buyer_name,style_ref_no,job_quantity,po_number,po_quantity,shipment_date", "",'','0,0,0,0,0,1,0,1,3');
	?>
	<table border="1" class="rpt_table" rules="all" width="950" cellpadding="0" cellspacing="0" align="left">
		<thead>
            <tr>
                <th colspan="11"><?php echo create_drop_down("cbo_string_search_type", 130, $string_search_type, '', 1, "-- Searching Type --",4); ?></th>
            </tr>

            <tr>
                <th width="30">SL</th>
                <th width="120">Job No</th>
                <th width="60">Year</th>
                <th width="120">Company</th>
                <th width="120">Buyer Name</th>
                <th width="120">Style Ref No</th>
                <th width="100">Job Qty</th>               
                <th>Shipment Date</th>
            </tr>
		</thead>				
    </table>
    <div style="width:970px; max-height:420px; overflow-y:scroll;">
	    <table id="list_view" width="950" border="1" rules="all" class="rpt_table" align="left">
	    	<?
	    	$i=1;
	    	foreach ($job_data as $job_no=>$row) 
	    	{
	    		?>
		    	<tr id="tr_<?=$i;?>" onClick="js_set_value('<?= chop($row['PO_ID'],",")."*".$row['STYLE_REF_NO']."*".$row['BUYER_NAME']."*".$i; ?>')" style="text-decoration:none; cursor:pointer">
	                <td width="30" align="center"><?= $i; ?></td>
	                <td width="120"><p><?= $row['JOB_NO']; ?></p></td>
	                <td width="60" align="center"><p><?= $row['YEAR']; ?></p></td>
	                <td width="120"><p><?= $company_arr[$row['COMPANY_NAME']]; ?></p></td>
	                <td width="120"><p><?= $buyer_arr[$row['BUYER_NAME']]; ?></p></td>
	                <td width="120"><p><?= $row['STYLE_REF_NO']; ?></p></td>
	                <td width="100" align="right"><p><?= $row['JOB_QUANTITY']; ?></p></td>
	                <td align="center"><p><?= change_date_format($row['SHIPMENT_DATE']); ?></p></td>
				</tr>
				<?
				$i++;
			}	
			?>	
		</table>
    </div>
    <div style="width:970px; margin-top:10px;" align="center">
    <input type="button" name="close" id="close" onclick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px">
    </div>
	<?
	exit();
}


if($action=="show_ile_load_uom")
{
	$data=explode("_",$data);
	$uom=$trim_group_arr[$data[0]]['uom'];
	echo "document.getElementById('cbo_uom').value 	= '".$data[1]."';\n";
	exit();	
}

if($action=="show_dtls_list_view")
{
	$data_ref=explode("__",$data);
	//print_r($data);die;
	$order_id=$data_ref[0];
	$store_id=$data_ref[1];
	$cbo_floor=$data_ref[2];
	$cbo_room=$data_ref[3];
	$txt_rack=$data_ref[4];
	$txt_shelf=$data_ref[5];
	$cbo_bin=$data_ref[6];
	$store_update_upto=$data_ref[7];
	$color_arr=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');
	$size_arr=return_library_array( "select id,size_name from lib_size where status_active=1 and is_deleted=0",'id','size_name');
	
	$item_group_sql=sql_select("select a.ID, a.ITEM_GROUP_ID, b.ITEM_NAME, b.CONVERSION_FACTOR, b.ORDER_UOM 
	from product_details_master a, lib_item_group b where a.item_group_id=b.id and a.item_category_id=4 and a.entry_form=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	foreach($item_group_sql as $row)
	{
		$conversion_factor[$row["ID"]]=$row["CONVERSION_FACTOR"];
		$group_order_uom[$row["ID"]]=$row["ORDER_UOM"];
		$item_group_arr[$row["ITEM_GROUP_ID"]]=$row["ITEM_NAME"];
	}
	unset($item_group_sql);				
	 
	
	$str_conds="";//c.floor_id, c.room, c.rack, c.self, c.bin_box
	if($cbo_floor && $store_update_upto >1) $str_conds.=" and c.floor_id=$cbo_floor";
	if($cbo_room && $store_update_upto >2) $str_conds.=" and c.room=$cbo_room";
	if($txt_rack && $store_update_upto >3) $str_conds.=" and c.rack=$txt_rack";
	if($txt_shelf && $store_update_upto >4) $str_conds.=" and c.self=$txt_shelf";
	if($cbo_bin && $store_update_upto >5) $str_conds.=" and c.bin_box=$cbo_bin";
	
	$sql_order_rate="select c.ID AS TRANS_ID, b.PO_BREAKDOWN_ID, b.PROD_ID, c.TRANSACTION_TYPE, c.CONS_QUANTITY, c.CONS_AMOUNT, b.TRANS_TYPE, b.QUANTITY, b.ORDER_AMOUNT
	from product_details_master a, order_wise_pro_details b, inv_transaction c
	where a.id=b.prod_id and b.trans_id=c.id and a.item_category_id='4' and a.entry_form=24 and b.po_breakdown_id in($order_id) and c.store_id=$store_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.current_stock>0 $str_conds";
	//echo $sql_order_rate;die;
	$sql_order_rate_result=sql_select($sql_order_rate);
	$order_item_data=array();
	foreach($sql_order_rate_result as $row)
	{
		if($trans_id_check[$row["TRANS_ID"]][$row["PO_BREAKDOWN_ID"]]=="")
		{
			$trans_id_check[$row["TRANS_ID"]][$row["PO_BREAKDOWN_ID"]]=$row["TRANS_ID"];
			if($row["TRANSACTION_TYPE"]==1 || $row["TRANSACTION_TYPE"]==4 || $row["TRANSACTION_TYPE"]==5)
			{
				$order_item_data[$row["PROD_ID"]][$row["PO_BREAKDOWN_ID"]]["cons_quantity"]+=$row["CONS_QUANTITY"];
				$order_item_data[$row["PROD_ID"]][$row["PO_BREAKDOWN_ID"]]["cons_amount"]+=$row["CONS_AMOUNT"];
			}
			else
			{
				$order_item_data[$row["PROD_ID"]][$row["PO_BREAKDOWN_ID"]]["cons_quantity"]-=$row["CONS_QUANTITY"];
				$order_item_data[$row["PROD_ID"]][$row["PO_BREAKDOWN_ID"]]["cons_amount"]-=$row["CONS_AMOUNT"];
			}
		}
		
		if($row["TRANS_TYPE"]==1 || $row["TRANS_TYPE"]==4 || $row["TRANS_TYPE"]==5)
		{
			$order_item_data[$row["PROD_ID"]][$row["PO_BREAKDOWN_ID"]]["QUANTITY"]+=$row["QUANTITY"];
			$order_item_data[$row["PROD_ID"]][$row["PO_BREAKDOWN_ID"]]["ORDER_AMOUNT"]+=$row["ORDER_AMOUNT"];
		}
		else
		{
			$order_item_data[$row["PROD_ID"]][$row["PO_BREAKDOWN_ID"]]["QUANTITY"]-=$row["QUANTITY"];
			$order_item_data[$row["PROD_ID"]][$row["PO_BREAKDOWN_ID"]]["ORDER_AMOUNT"]-=$row["ORDER_AMOUNT"];
		}
	}
	
	$sql = "SELECT a.ID, a.ITEM_GROUP_ID, a.ITEM_DESCRIPTION, a.UNIT_OF_MEASURE, a.BRAND_SUPPLIER, a.ITEM_COLOR, a.ITEM_SIZE, a.COLOR, a.GMTS_SIZE, b.PO_BREAKDOWN_ID, d.PO_NUMBER, sum((case when b.trans_type in(1,4,5) then b.quantity else 0 end)-(case when b.trans_type in(2,3,6) then b.quantity else 0 end)) as BAL, sum((case when b.trans_type in(1,4,5) then b.ORDER_AMOUNT else 0 end)-(case when b.trans_type in(2,3,6) then b.ORDER_AMOUNT else 0 end)) as BAL_AMT 
	from product_details_master a, inv_transaction c, order_wise_pro_details b, WO_PO_BREAK_DOWN d 
	where a.id=c.prod_id and c.id=b.trans_id and b.PO_BREAKDOWN_ID=d.id and a.item_category_id=4 and c.item_category=4 and a.entry_form=24 and b.entry_form in(24,25,49,73,78,112) and b.po_breakdown_id in($order_id) and c.store_id=$store_id and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $str_conds 
	group by a.ID, a.ITEM_GROUP_ID, a.ITEM_DESCRIPTION, a.UNIT_OF_MEASURE, a.BRAND_SUPPLIER, a.ITEM_COLOR, a.ITEM_SIZE, a.COLOR, a.GMTS_SIZE, b.PO_BREAKDOWN_ID, d.PO_NUMBER
	having sum((case when b.trans_type in(1,4,5) then b.quantity else 0 end)-(case when b.trans_type in(2,3,6) then b.quantity else 0 end)) >0  
	order by a.item_group_id";	
	//echo $sql;die;
	$data_array=sql_select($sql);
	$i=1;
	foreach($data_array as $row)
	{  
		if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		$ord_rate=0;
		if($row["BAL_AMT"]!=0 && $row["BAL"]!=0) $ord_rate=$row["BAL_AMT"]/$row["BAL"];
		$current_stock_qnty=$row["BAL"]*$conversion_factor[$row["ID"]];
		$item_rate=0;
		if($order_item_data[$row["ID"]][$row["PO_BREAKDOWN_ID"]]["cons_amount"]!=0 && $order_item_data[$row["ID"]][$row["PO_BREAKDOWN_ID"]]["cons_quantity"]!=0)
		{
			$item_rate=$order_item_data[$row["ID"]][$row["PO_BREAKDOWN_ID"]]["cons_amount"]/$order_item_data[$row["ID"]][$row["PO_BREAKDOWN_ID"]]["cons_quantity"];
		}
		
		?>
		 <tr bgcolor="<? echo $bgcolor; ?>">
			<td id="tdSl_<?=$i;?>"><? echo $i;?></td>
			<td id="tdProdID_<?=$i;?>" title="<? echo $row["ID"];?>"><? echo $row["ID"];?></td>
            <td id="tdOrderID_<?=$i;?>" title="<? echo $row["PO_BREAKDOWN_ID"];?>"><? echo $row["PO_NUMBER"];?></td>
			<td id="tdItemGroup_<?=$i;?>" title="<? echo $row["ITEM_GROUP_ID"];?>"><? echo $item_group_arr[$row["ITEM_GROUP_ID"]];?></td>
			<td id="tdItemDescription_<?=$i;?>" title="<? echo $row["ITEM_DESCRIPTION"];?>"><? echo $row["ITEM_DESCRIPTION"];?></td>
			<td id="tdBrandSupp_<?=$i;?>" title="<? echo $row["BRAND_SUPPLIER"];?>"><? echo $row["BRAND_SUPPLIER"];?></td>
			<td id="tdItemColor_<?=$i;?>" title="<? echo $row["ITEM_COLOR"];?>"><? echo $color_arr[$row["ITEM_COLOR"]];?></td>
			<td id="tdItemSize_<?=$i;?>" title="<? echo $row["ITEM_SIZE"];?>"><? echo $row["ITEM_SIZE"];?></td>
			<td id="tdGmtsColor_<?=$i;?>" title="<? echo $row["COLOR"];?>"><? echo $color_arr[$row["COLOR"]];?></td>
			<td id="tdGmtsSize_<?=$i;?>" title="<? echo $row["GMTS_SIZE"];?>"><? echo $size_arr[$row["GMTS_SIZE"]];?></td>
			<td id="tdUom_<?=$i;?>" title="<? echo $row["UNIT_OF_MEASURE"];?>"><? echo $unit_of_measurement[$row["UNIT_OF_MEASURE"]];?></td>
			<td id="tdCurrentStock_<?=$i;?>" align="center"><input type="text" name="txtCurrentStock[]" id="txtCurrentStock_<?=$i;?>" class="text_boxes_numeric" style="width:80px" value="<? echo number_format($current_stock_qnty,4,'.','');?>" disabled="disabled" readonly/></td>
			<td id="tdTransferQnty_<?=$i;?>" align="center">
			<input type="text" name="txtTransferQnty[]" id="txtTransferQnty_<?=$i;?>" class="text_boxes_numeric" style="width:80px;" onBlur="fn_stock_check(<?=$i;?>)" />
			<input type="hidden" name="txtRate[]" id="txtRate_<?=$i;?>" value="<? echo $item_rate; ?>"/>
            <input type="hidden" name="txtOrdRate[]" id="txtOrdRate_<?=$i;?>" value="<? echo $ord_rate; ?>"/>
            
			</td>
			<td id="tdRemarks_<?=$i;?>" align="center">
			<input type="text" name="txtRemarks[]" id="txtRemarks_<?=$i;?>" class="text_boxes" style="width:90px;" />
			<input type="hidden" name="updateDtlsId[]" id="updateDtlsId_<?=$i;?>" readonly>
			<input type="hidden" name="updateTransIssueId[]" id="updateTransIssueId_<?=$i;?>" readonly>
			<input type="hidden" name="updateTransRecvId[]" id="updateTransRecvId_<?=$i;?>" readonly>
			</td>
		</tr>
		<? 
		$i++; 
	} 
	exit();
}

if($action=="show_dtls_list_view_update")
{
	$data_ref=explode("__",$data);
	//print_r($data);die;
	$order_id=$data_ref[0];
	$store_id=$data_ref[1];
	$cbo_floor=$data_ref[2];
	$cbo_room=$data_ref[3];
	$txt_rack=$data_ref[4];
	$txt_shelf=$data_ref[5];
	$cbo_bin=$data_ref[6];
	$store_update_upto=$data_ref[7];
	$mst_id=$data_ref[8];
	
	$color_arr=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');
	$size_arr=return_library_array( "select id,size_name from lib_size where status_active=1 and is_deleted=0",'id','size_name');
	
	$item_group_sql=sql_select("select a.ID, a.ITEM_GROUP_ID, b.ITEM_NAME, b.CONVERSION_FACTOR, b.ORDER_UOM 
	from product_details_master a, lib_item_group b where a.item_group_id=b.id and a.item_category_id=4 and a.entry_form=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	foreach($item_group_sql as $row)
	{
		$conversion_factor[$row["ID"]]=$row["CONVERSION_FACTOR"];
		$group_order_uom[$row["ID"]]=$row["ORDER_UOM"];
		$item_group_arr[$row["ITEM_GROUP_ID"]]=$row["ITEM_NAME"];
	}
	unset($item_group_sql);				
	 
	
	$str_conds="";//c.floor_id, c.room, c.rack, c.self, c.bin_box
	if($cbo_floor && $store_update_upto >1) $str_conds.=" and c.floor_id=$cbo_floor";
	if($cbo_room && $store_update_upto >2) $str_conds.=" and c.room=$cbo_room";
	if($txt_rack && $store_update_upto >3) $str_conds.=" and c.rack=$txt_rack";
	if($txt_shelf && $store_update_upto >4) $str_conds.=" and c.self=$txt_shelf";
	if($cbo_bin && $store_update_upto >5) $str_conds.=" and c.bin_box=$cbo_bin";
	
	$sql_order_rate="select c.ID AS TRANS_ID, b.PO_BREAKDOWN_ID, b.PROD_ID, c.CONS_QUANTITY, c.CONS_AMOUNT, c.MST_ID, c.TRANSACTION_TYPE, b.TRANS_TYPE, b.QUANTITY, b.ORDER_AMOUNT
	from product_details_master a, order_wise_pro_details b, inv_transaction c
	where a.id=b.prod_id and b.trans_id=c.id and a.item_category_id='4' and a.entry_form=24 and b.trans_type in(1,5) and b.entry_form in(24,78,112) and b.po_breakdown_id in ($order_id) and c.store_id=$store_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $str_conds";
	//echo $sql_order_rate;die;
	$sql_order_rate_result=sql_select($sql_order_rate);
	$order_item_data=array();
	foreach($sql_order_rate_result as $row)
	{
		if($trans_id_check[$row["TRANS_ID"]][$row["PO_BREAKDOWN_ID"]]=="")
		{
			$trans_id_check[$row["TRANS_ID"]][$row["PO_BREAKDOWN_ID"]]=$row["TRANS_ID"];
			if($row["MST_ID"]!=$mst_id && $row["TRANSACTION_TYPE"] !=5 && $row["TRANSACTION_TYPE"] !=6)
			{
				$order_item_data[$row["PROD_ID"]][$row["PO_BREAKDOWN_ID"]]["cons_quantity"]+=$row["CONS_QUANTITY"];
				$order_item_data[$row["PROD_ID"]][$row["PO_BREAKDOWN_ID"]]["cons_amount"]+=$row["CONS_AMOUNT"];
			}
		}
		
		if($row["MST_ID"]!=$mst_id && $row["TRANS_TYPE"] !=5 && $row["TRANS_TYPE"] !=6)
		{
			$order_item_data[$row["PROD_ID"]][$row["PO_BREAKDOWN_ID"]]["QUANTITY"]+=$row["QUANTITY"];
			$order_item_data[$row["PROD_ID"]][$row["PO_BREAKDOWN_ID"]]["ORDER_AMOUNT"]+=$row["ORDER_AMOUNT"];
		}
	}
	unset($sql_order_rate_result);
	
	$sql_stock = "SELECT b.PROD_ID, b.PO_BREAKDOWN_ID, c.MST_ID, c.TRANSACTION_TYPE, b.TRANS_TYPE, b.QUANTITY, b.ORDER_AMOUNT
	from order_wise_pro_details b, inv_transaction c 
	where b.trans_id=c.id and c.item_category=4 and b.entry_form in(24,25,49,73,78,112) and b.po_breakdown_id in($order_id) and c.store_id=$store_id and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $str_conds";
	$sql_stock_result=sql_select($sql_stock);
	$stock_data_arr=array();
	foreach($sql_stock_result as $row)
	{
		if($row["MST_ID"]!=$mst_id && $row["TRANS_TYPE"] !=5 && $row["TRANS_TYPE"] !=6)
		{
			if($row["TRANS_TYPE"]==1 || $row["TRANS_TYPE"]==4 || $row["TRANS_TYPE"]==5)
			{
				$stock_data_arr[$row["PROD_ID"]]["QUANTITY"]+=$row["QUANTITY"];
				$stock_data_arr[$row["PROD_ID"]]["ORDER_AMOUNT"]+=$row["ORDER_AMOUNT"];
			}
			else
			{
				$stock_data_arr[$row["PROD_ID"]]["QUANTITY"]-=$row["QUANTITY"];
				$stock_data_arr[$row["PROD_ID"]]["ORDER_AMOUNT"]-=$row["ORDER_AMOUNT"];
			}
			
		}
	}
	unset($sql_stock_result);
	
	$sql="select a.ID, a.ITEM_GROUP_ID, a.ITEM_DESCRIPTION, a.UNIT_OF_MEASURE, a.BRAND_SUPPLIER, a.ITEM_COLOR, a.ITEM_SIZE, a.COLOR, a.GMTS_SIZE, b.FROM_ORDER_ID as PO_BREAKDOWN_ID, b.ID as DTLS_ID, b.TRANSFER_QNTY, b.REMARKS, b.TRANS_ID, b.TO_TRANS_ID, c.PO_NUMBER  
	from product_details_master a, inv_item_transfer_dtls b, WO_PO_BREAK_DOWN c  
	where a.id=b.from_prod_id and b.FROM_ORDER_ID=c.id and b.mst_id='$mst_id' and b.status_active = '1' and b.is_deleted = '0'";	
	//echo $sql;
	$data_array=sql_select($sql);
	$i=1;
	foreach($data_array as $row)
	{  
		if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		$ord_rate=0;
		if($order_item_data[$row["ID"]][$row["PO_BREAKDOWN_ID"]]["ORDER_AMOUNT"]!=0 && $order_item_data[$row["ID"]][$row["PO_BREAKDOWN_ID"]]["QUANTITY"]!=0)
		{
			$ord_rate=$order_item_data[$row["ID"]][$row["PO_BREAKDOWN_ID"]]["ORDER_AMOUNT"]/$order_item_data[$row["ID"]][$row["PO_BREAKDOWN_ID"]]["QUANTITY"];
		}
		
		$current_stock_qnty=$stock_data_arr[$row["ID"]]["QUANTITY"]*$conversion_factor[$row["ID"]];
		$item_rate=$order_item_data[$row["ID"]][$row["PO_BREAKDOWN_ID"]]["cons_amount"]/$order_item_data[$row["ID"]][$row["PO_BREAKDOWN_ID"]]["cons_quantity"];
		
		?>
		 <tr bgcolor="<? echo $bgcolor; ?>">
			<td id="tdSl_<?=$i;?>"><? echo $i;?></td>
			<td id="tdProdID_<?=$i;?>" title="<? echo $row["ID"];?>"><? echo $row["ID"];?></td>
            <td id="tdOrderID_<?=$i;?>" title="<? echo $row["PO_BREAKDOWN_ID"];?>"><? echo $row["PO_NUMBER"];?></td>
			<td id="tdItemGroup_<?=$i;?>" title="<? echo $row["ITEM_GROUP_ID"];?>"><? echo $item_group_arr[$row["ITEM_GROUP_ID"]];?></td>
			<td id="tdItemDescription_<?=$i;?>" title="<? echo $row["ITEM_DESCRIPTION"];?>"><? echo $row["ITEM_DESCRIPTION"];?></td>
			<td id="tdBrandSupp_<?=$i;?>" title="<? echo $row["BRAND_SUPPLIER"];?>"><? echo $row["BRAND_SUPPLIER"];?></td>
			<td id="tdItemColor_<?=$i;?>" title="<? echo $row["ITEM_COLOR"];?>"><? echo $color_arr[$row["ITEM_COLOR"]];?></td>
			<td id="tdItemSize_<?=$i;?>" title="<? echo $row["ITEM_SIZE"];?>"><? echo $row["ITEM_SIZE"];?></td>
			<td id="tdGmtsColor_<?=$i;?>" title="<? echo $row["COLOR"];?>"><? echo $color_arr[$row["COLOR"]];?></td>
			<td id="tdGmtsSize_<?=$i;?>" title="<? echo $row["GMTS_SIZE"];?>"><? echo $size_arr[$row["GMTS_SIZE"]];?></td>
			<td id="tdUom_<?=$i;?>" title="<? echo $row["UNIT_OF_MEASURE"];?>"><? echo $unit_of_measurement[$row["UNIT_OF_MEASURE"]];?></td>
			<td id="tdCurrentStock_<?=$i;?>" align="center"><input type="text" name="txtCurrentStock[]" id="txtCurrentStock_<?=$i;?>" class="text_boxes_numeric" style="width:80px" value="<? echo number_format($current_stock_qnty,4,'.','');?>" disabled="disabled" readonly/></td>
			<td id="tdTransferQnty_<?=$i;?>" align="center">
			<input type="text" name="txtTransferQnty[]" id="txtTransferQnty_<?=$i;?>" class="text_boxes_numeric" style="width:80px;" value="<? echo $row["TRANSFER_QNTY"]; ?>" />
			<input type="hidden" name="txtRate[]" id="txtRate_<?=$i;?>" value="<? echo $item_rate; ?>"/>
            <input type="hidden" name="txtOrdRate[]" id="txtOrdRate_<?=$i;?>" value="<? echo $ord_rate; ?>"/>
            
			</td>
			<td id="tdRemarks_<?=$i;?>" align="center">
			<input type="text" name="txtRemarks[]" id="txtRemarks_<?=$i;?>" class="text_boxes" style="width:90px;" value="<? echo $row["REMARKS"]; ?>" />
			<input type="hidden" name="updateDtlsId[]" id="updateDtlsId_<?=$i;?>" value="<? echo $row["DTLS_ID"]; ?>" readonly>
			<input type="hidden" name="updateTransIssueId[]" id="updateTransIssueId_<?=$i;?>" value="<? echo $row["TRANS_ID"]; ?>" readonly>
			<input type="hidden" name="updateTransRecvId[]" id="updateTransRecvId_<?=$i;?>" value="<? echo $row["TO_TRANS_ID"]; ?>" readonly>
			</td>
		</tr>
		<? 
		$i++; 
	} 
	exit();
}


if ($action=="orderToorderTransfer_popup")
{
	echo load_html_head_contents("Order To Order Transfer Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?> 

	<script>
		
		function js_set_value(data)
		{
			var id = data.split("_");
			$('#transfer_id').val(id[0]);
			$('#hidden_posted_in_account').val(id[1]);
			parent.emailwindow.hide();
		}
        function createContentSearch(id) {
            var data = ["Exact","Starts with","Ends with","Contents"];
            var appender = '';
            appender += '<select name="cbo_string_search_type" id="cbo_string_search_type" class="combo_boxes " style="width:130px" onchange="">';
            appender += '<option data-attr="" value="0">-- Searching Type --</option>';
            $.each(data, function (index, val){
                if(index == 3){
                    appender += '<option data-attr="" value="'+(index+1)+'" selected>'+val+'</option>';
                }else{
                    appender += '<option data-attr="" value="'+(index+1)+'">'+val+'</option>';
                }
            });
            appender += '</select>';
            $('#'+id).find('thead').prepend('<tr><th colspan="8">'+appender+'</th></tr>');
        }
	
    </script>

</head>

<body>
<div align="center" style="width:800px;">
	<form name="searchdescfrm"  id="searchdescfrm">
		<fieldset style="width:800px;margin-left:10px">
        <legend>Enter search words</legend>
            <table cellpadding="0" cellspacing="0" width="800" class="rpt_table">
                <thead>
                    <th width="200">Search By</th>
                    <th width="200" id="search_by_td_up">Please Enter Transfer ID</th>
                    <th width="250">Date Range</th>
                    <th>
                        <input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
                        <input type="hidden" name="transfer_id" id="transfer_id" class="text_boxes" value="">
                        <input type="hidden" name="hidden_posted_in_account" id="hidden_posted_in_account" class="text_boxes" value="">
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
                        <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px">To
                        <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px">
                    </td>
                    <td>
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $cbo_company_id; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+<? echo $cbo_transfer_criteria; ?>, 'create_transfer_search_list_view', 'search_div', 'trims_transfer_v4_controller', 'setFilterGrid(\'tbl_list_search\',-1); createContentSearch(\'rpt_tabletbl_list_search\');')" style="width:100px;" />
                    </td>
                </tr>
                <tr>
                	<td colspan="4" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
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
	$date_form=$data[3];
	$date_to =$data[4];
	$year_id=$data[5];
    $transferCriteria = $data[6];
	$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	if($db_type==0)
	{
		$date_form=change_date_format($date_form,'yyyy-mm-dd');
		$date_to=change_date_format($date_to,'yyyy-mm-dd');
	}
	else
	{
		$date_form=change_date_format($date_form,'','',1);
		$date_to=change_date_format($date_to,'','',1);
	}
	
	if($date_form!="" && $date_to!="") $date_cond=" and transfer_date between '$date_form' and '$date_to'";
	
	//echo $date_form."=".$date_to."=".$year_id;die;
	
	
	if($search_by==1)
		$search_field="transfer_system_id";	
	else
		$search_field="challan_no";
	
	if($db_type==0) $year_field="YEAR(insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(insert_date,'YYYY') as year";
	else $year_field="";//defined Later
	
	if($year_id>0)
	{
		if($db_type==0)
		{
			$year_condition=" and YEAR(insert_date)='$year_id'";
		}
		else
		{
			$year_condition=" and to_char(insert_date,'YYYY')='$year_id'";
		}
	}
	
 	$sql="select id, transfer_prefix_number, transfer_system_id, $year_field, challan_no, company_id, transfer_date, transfer_criteria, item_category, is_posted_account from inv_item_transfer_mst where item_category=4 and company_id=$company_id and $search_field like '$search_string' and transfer_criteria in(1,2,4) and status_active=1 and PURPOSE=4 and transfer_criteria = $transferCriteria and is_deleted=0 $date_cond $year_condition order by id desc";
 	// echo $sql;
	
	$arr=array(3=>$company_arr,5=>$item_transfer_criteria,6=>$item_category);

	echo  create_list_view("tbl_list_search", "Transfer ID,Year,Challan No,Company,Transfer Date,Transfer Criteria,Item Category", "80,70,100,110,90,120","760","250",0, $sql, "js_set_value", "id,is_posted_account", "", 1, "0,0,0,company_id,0,transfer_criteria,item_category", $arr, "transfer_prefix_number,year,challan_no,company_id,transfer_date,transfer_criteria,item_category", '','','0,0,0,0,3,0,0');
	
	exit();
}

if($action=='populate_data_from_transfer_master')
{
	$data_array=sql_select("SELECT a.transfer_system_id, a.challan_no, a.company_id, a.to_company, a.transfer_criteria, a.location_id, a.to_location_id, a.transfer_date, a.item_category, a.from_order_id,a.to_order_id, a.from_store_id, a.to_store_id,b.from_store,b.to_store,b.floor_id,b.room,b.rack,b.shelf,b.bin_box,b.to_floor_id,b.to_room,b.to_rack,b.to_shelf,b.to_bin_box
	from inv_item_transfer_mst a,inv_item_transfer_dtls b 
	where a.id='$data' and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	
	$company_id=$data_array[0][csf("company_id")];
	$variable_inventory_sql=sql_select("select store_method, rack_balance from variable_settings_inventory  where company_name=$company_id and item_category_id=4 and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1");
	$store_method=$variable_inventory_sql[0][csf("store_method")];

	foreach ($data_array as $row)
	{
		$to_company = (str_replace("'", "", $row[csf("to_company")]) == 0 ) ? $to_company = $row[csf("company_id")] : $to_company = $row[csf("to_company")];
		
		echo "document.getElementById('update_id').value 					= '".$data."';\n";
		echo "document.getElementById('txt_system_id').value 				= '".$row[csf("transfer_system_id")]."';\n";
		echo "document.getElementById('cbo_company_id').value 				= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_company_id_to').value 			= '".$to_company."';\n";
		echo "document.getElementById('txt_challan_no').value 				= '".$row[csf("challan_no")]."';\n";
		echo "document.getElementById('store_update_upto').value 			= '".$store_method."';\n";

		echo "document.getElementById('cbo_transfer_criteria').value 		= '".$row[csf("transfer_criteria")]."';\n";
		echo "document.getElementById('cbo_location').value 				= '".$row[csf("location_id")]."';\n";
		

		if ( str_replace("'", "", $row[csf("transfer_criteria")]) == 1 ) 
		{
			echo "load_drop_down( 'requires/trims_transfer_v4_controller',$to_company, 'load_drop_down_location_to', 'to_location_td' );\n";
		}
		echo "document.getElementById('cbo_location_to').value 				= '".$row[csf("to_location_id")]."';\n";


		echo "load_room_rack_self_bin('requires/trims_transfer_v4_controller*4', 'store','from_store_td', '".$row[csf('company_id')]."','"."',this.value, '', '','', '', '','', '152');\n";
		echo "document.getElementById('cbo_store_name').value 				= '".$row[csf("from_store")]."';\n";
		echo "load_room_rack_self_bin('requires/trims_transfer_v4_controller*4', 'floor','floor_td', '".$row[csf('company_id')]."','"."','".$row[csf('from_store')]."',this.value);\n";
		echo "document.getElementById('cbo_floor').value 				= '".$row[csf("floor_id")]."';\n";
		
		if($row[csf("floor_id")])
		{
			echo "load_room_rack_self_bin('requires/trims_transfer_v4_controller*4', 'room','room_td', '".$row[csf('company_id')]."','"."','".$row[csf('from_store')]."','".$row[csf('floor_id')]."',this.value);\n";
		}
		echo "document.getElementById('cbo_room').value 				= '".$row[csf("room")]."';\n";
		
		if($row[csf("room")])
		{
			echo "load_room_rack_self_bin('requires/trims_transfer_v4_controller*4', 'rack','rack_td', '".$row[csf('company_id')]."','"."','".$row[csf('from_store')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."',this.value);\n";
		}
		echo "document.getElementById('txt_rack').value 				= '".$row[csf("rack")]."';\n";
		
		if($row[csf("rack")])
		{
			echo "load_room_rack_self_bin('requires/trims_transfer_v4_controller*4', 'shelf','shelf_td', '".$row[csf('company_id')]."','"."','".$row[csf('from_store')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."','".$row[csf('rack')]."',this.value);\n";
		}
		echo "document.getElementById('txt_shelf').value 				= '".$row[csf("shelf")]."';\n";
		
		if($row[csf("shelf")])
		{
			echo "load_room_rack_self_bin('requires/trims_transfer_v4_controller*4', 'bin','bin_td', '".$row[csf('company_id')]."','"."','".$row[csf('from_store')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."','".$row[csf('rack')]."','".$row[csf('shelf')]."',this.value);\n";
		}
		echo "document.getElementById('cbo_bin').value 				= '".$row[csf("bin_box")]."';\n";


		echo "load_room_rack_self_bin('requires/trims_transfer_v4_controller*4*cbo_store_name_to', 'store','to_store_td', '".$to_company."','"."',this.value, '', '','', '', '','', '152');\n";
		echo "document.getElementById('cbo_store_name_to').value 				= '".$row[csf("to_store")]."';\n";

		echo "load_room_rack_self_bin('requires/trims_transfer_v4_controller*4*cbo_floor_to', 'floor','floor_td_to', '".$to_company."','"."','".$row[csf('to_store')]."',this.value);\n";
		echo "document.getElementById('cbo_floor_to').value 			= '".$row[csf("to_floor_id")]."';\n";
		
		if($row[csf("to_floor_id")])
		{
			echo "load_room_rack_self_bin('requires/trims_transfer_v4_controller*4*cbo_room_to', 'room','room_td_to', '".$to_company."','"."','".$row[csf('to_store')]."','".$row[csf('to_floor_id')]."',this.value);\n";
		}
		echo "document.getElementById('cbo_room_to').value 				= '".$row[csf("to_room")]."';\n";
		
		if($row[csf("to_room")])
		{
			echo "load_room_rack_self_bin('requires/trims_transfer_v4_controller*4*txt_rack_to', 'rack','rack_td_to', '".$to_company."','"."','".$row[csf('to_store')]."','".$row[csf('to_floor_id')]."','".$row[csf('to_room')]."',this.value);\n";
		}
		echo "document.getElementById('txt_rack_to').value 				= '".$row[csf("to_rack")]."';\n";
		
		if($row[csf("to_rack")])
		{
			echo "load_room_rack_self_bin('requires/trims_transfer_v4_controller*4*txt_shelf_to', 'shelf','shelf_td_to', '".$to_company."','"."','".$row[csf('to_store')]."','".$row[csf('to_floor_id')]."','".$row[csf('to_room')]."','".$row[csf('to_rack')]."',this.value);\n";
		}
		echo "document.getElementById('txt_shelf_to').value 			= '".$row[csf("to_shelf")]."';\n";
		
		if($row[csf("to_shelf")])
		{
			echo "load_room_rack_self_bin('requires/trims_transfer_v4_controller*4*cbo_bin_to', 'bin','bin_td_to', '".$to_company."','"."','".$row[csf('to_store')]."','".$row[csf('to_floor_id')]."','".$row[csf('to_room')]."','".$row[csf('to_rack')]."','".$row[csf('to_shelf')]."',this.value);\n";
		}
		echo "document.getElementById('cbo_bin_to').value 			= '".$row[csf("to_bin_box")]."';\n";

		echo "$('#cbo_transfer_criteria').attr('disabled','disabled');\n";
		echo "$('#cbo_company_id_to').attr('disabled','disabled');\n";
		echo "$('#cbo_location').attr('disabled','disabled');\n";
		echo "$('#cbo_location_to').attr('disabled','disabled');\n";

		echo "$('#cbo_store_name').attr('disabled','disabled');\n";
		echo "$('#cbo_floor').attr('disabled','disabled');\n";
		echo "$('#cbo_room').attr('disabled','disabled');\n";
		echo "$('#txt_rack').attr('disabled','disabled');\n";
		echo "$('#txt_shelf').attr('disabled','disabled');\n";
		echo "$('#cbo_bin').attr('disabled','disabled');\n";
		echo "$('#cbo_store_name_to').attr('disabled','disabled');\n";

		echo "document.getElementById('txt_transfer_date').value 			= '".change_date_format($row[csf("transfer_date")])."';\n";
		//echo "get_php_form_data('".$row[csf("from_order_id")]."**from'".",'populate_data_from_order','requires/trims_transfer_v4_controller');\n";
		//echo "get_php_form_data('".$row[csf("to_order_id")]."**to'".",'populate_data_from_order','requires/trims_transfer_v4_controller');\n";
		echo "$('#cbo_company_id').attr('disabled','disabled');\n";
		echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_trims_transfer_entry',1,1);\n"; 
		exit();
	}
}

if($action=="style_order_transf_data")
{
	$sql="select a.FROM_ORDER_ID, c.BUYER_NAME, c.STYLE_REF_NO from inv_item_transfer_dtls a, WO_PO_BREAK_DOWN b, WO_PO_DETAILS_MASTER c  
	where a.FROM_ORDER_ID=b.ID and b.JOB_ID=c.ID and a.mst_id='$data' and a.status_active = '1' and a.is_deleted = '0'";
	$sql_result=sql_select($sql);
	$all_order_arr=$all_style_arr=$buyer_id_arr=array();
	foreach($sql_result as $val)
	{
		$all_order_arr[$val["FROM_ORDER_ID"]]=$val["FROM_ORDER_ID"];
		$all_style_arr[$val["STYLE_REF_NO"]]=$val["STYLE_REF_NO"];
		$buyer_id_arr[$val["BUYER_NAME"]]=$val["BUYER_NAME"];
	}
	echo implode(",",$all_order_arr)."*".implode(",",$all_style_arr)."*".implode(",",$buyer_id_arr);
	exit();
}

if($action=='populate_transfer_details_form_data')
{
	$data_array=sql_select("SELECT a.from_order_id, a.to_order_id, a.from_store_id, a.to_store_id, b.id, b.mst_id, b.item_group, b.from_prod_id, b.to_prod_id, b.transfer_qnty, b.item_category, b.uom, b.remarks from inv_item_transfer_mst a, inv_item_transfer_dtls b 
	where a.id=b.mst_id and b.id='$data'");
	foreach ($data_array as $row)
	{ 
		
		//echo "select from_order_id from inv_item_transfer_mst where id=".$row[csf('mst_id')]." and  status_active=1 and is_deleted=0 ";
		
		
		echo "document.getElementById('update_dtls_id').value 				= '".$row[csf("id")]."';\n";
		echo "document.getElementById('cbo_item_desc').value 				= '".$row[csf("from_prod_id")]."';\n";
		echo "document.getElementById('txt_transfer_qnty').value 			= '".$row[csf("transfer_qnty")]."';\n";
		echo "document.getElementById('hidden_transfer_qnty').value 		= '".$row[csf("transfer_qnty")]."';\n";
		echo "document.getElementById('cbo_item_category').value 			= '".$row[csf("item_category")]."';\n";
		echo "document.getElementById('txt_item_id').value 					= '".$row[csf("item_group")]."';\n";
		echo "document.getElementById('cbo_uom').value 						= '".$row[csf("uom")]."';\n";
		echo "document.getElementById('txt_remarks').value 					= '".$row[csf("remarks")]."';\n";
		
		
		/* $sql_sk =sql_select( "select 
		sum(case when b.entry_form in(24) then b.quantity else 0 end) as recv_qty,
		sum(case when b.entry_form in(25) then b.quantity else 0 end) as issue_qty
		from product_details_master a, order_wise_pro_details b, inv_transaction c
		where  
		a.id=b.prod_id and b.trans_id=c.id and a.item_category_id=4 and b.entry_form in(24,25) and b.po_breakdown_id=".$cond_po_id." and b.prod_id='".$row[csf("from_prod_id")]."'  and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0");	*/
				
		
		$conversion_factor=return_field_value("b.conversion_factor as conversion_factor","product_details_master a, lib_item_group b","a.item_group_id=b.id and a.item_category_id=4 and a.entry_form=24 and a.id='".$row[csf("from_prod_id")]."' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","conversion_factor");
		
		$sql_trim = sql_select("SELECT sum((case when b.entry_form in(24,73,78,112) and b.trans_type in(1,4,5) then b.quantity else 0 end)-(case when b.entry_form in(25,49,78,112) and b.trans_type in(2,3,6) then b.quantity else 0 end)) as balance_qnty, e.color_name, d.size_name 
		from product_details_master a left join order_wise_pro_details b on a.id=b.prod_id left join inv_transaction c on b.trans_id=c.id left join lib_size d on d.id = a.gmts_size left join lib_color e on e.id = a.item_color
		where a.item_category_id=4 and c.item_category=4 and b.entry_form in(24,25,78,73,49,112) and b.trans_type in(1,2,3,4,5,6) and c.transaction_type in(1,2,3,4,5,6) and c.status_active=1  and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.po_breakdown_id=".$row[csf("from_order_id")]." and b.prod_id='".$row[csf("from_prod_id")]."' and c.store_id ='".$row[csf("from_store_id")]."' group by e.color_name, d.size_name  ");

		$curr_stock=($sql_trim[0][csf('balance_qnty')]*$conversion_factor)+$row[csf("transfer_qnty")];
		echo "document.getElementById('txt_current_stock').value 			= '".$curr_stock."';\n";
        echo "document.getElementById('txt_item_color').value 			= '".$sql_trim[0][csf("color_name")]."';\n";
        echo "document.getElementById('txt_item_size').value 			= '".$sql_trim[0][csf("size_name")]."';\n";

        $sql_trans=sql_select("SELECT trans_id from order_wise_pro_details where dtls_id=".$row[csf('id')]." and entry_form in(78,112) and trans_type in(5,6) and status_active=1 and is_deleted=0 order by trans_type DESC");
		echo "document.getElementById('update_trans_issue_id').value 		= '".$sql_trans[0][csf("trans_id")]."';\n";
		echo "document.getElementById('update_trans_recv_id').value 		= '".$sql_trans[1][csf("trans_id")]."';\n";

		echo "document.getElementById('previous_from_prod_id').value 	= '".$row[csf("from_prod_id")]."';\n";
		echo "document.getElementById('previous_to_prod_id').value 		= '".$row[csf("to_prod_id")]."';\n";

		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_trims_transfer_entry',1,1);\n"; 
		
		exit();
	}
}

//data save update delete here------------------------------//
if($action=="save_update_delete")
{	 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$cbo_item_category=4;
	$update_id=str_replace("'","",$update_id); 
	
	$variable_auto_rcv = return_field_value("auto_transfer_rcv", "variable_settings_inventory", " company_name=$cbo_company_id and item_category_id=4 and status_active=1 and variable_list= 27", "auto_transfer_rcv");
	if($variable_auto_rcv == "")
	{
		$variable_auto_rcv = 1; // if auto receive yes(1), then no need to acknowledgement
	}
        
	$transfer_criteria=str_replace("'","",$cbo_transfer_criteria); 
	$updateTransIssueId_arr=$updateTransRecvId_arr=$item_group_id_arr=$pord_wise_trans_arr=$all_po_ids_arr=$pord_ord_wise_trans_arr=array();
	for($i=1;$i<=$tot_row;$i++)
	{
		$prodId="prod_id".$i;
		$poId="po_id".$i;
		$updateDtlsId="updateDtlsId".$i;
		$updateTransIssueId="updateTransIssueId".$i;
		$updateTransRecvId="updateTransRecvId".$i;
		$txtTransferQnty="txtTransferQnty".$i;
		$itemGroupId="itemGroupId".$i;
		
		
		$pord_wise_trans_arr[$$prodId]+=$$txtTransferQnty;
		$pord_ord_wise_trans_arr[$$prodId][$$poId]+=$$txtTransferQnty;
		$all_prod_ids_arr[$$prodId]=$$prodId;
		$all_po_ids_arr[$$poId]=$$poId;
		$updateTransIssueId_arr[$$updateTransIssueId]=$$updateTransIssueId;
		$updateTransRecvId_arr[$$updateTransRecvId]=$$updateTransRecvId;
		$item_group_id_arr[$$itemGroupId]+=$$itemGroupId;
	}
	
	if($update_id>0)
	{
		$prev_sql="select ID, MST_ID, FROM_PROD_ID, TO_PROD_ID, TRANS_ID, TO_TRANS_ID, TRANSFER_QNTY from INV_ITEM_TRANSFER_DTLS where status_active=1 and mst_id=$update_id";
		$prev_sql_result=sql_select($prev_sql);
		$previous_issue_prod_data=array();$previous_rcv_prod_data=array();$previous_trans_id_arr=array();$previous_dtls_id_arr=$all_dtls_id_arr=array();
		foreach($prev_sql_result as $val)
		{
			$previous_issue_prod_data[$val["FROM_PROD_ID"]]=$val["TRANSFER_QNTY"];
			$previous_rcv_prod_data[$val["TO_PROD_ID"]]=$val["TRANSFER_QNTY"];
			
			if($val["TRANS_ID"]>0) $previous_trans_id_arr[$val["TRANS_ID"]]=$val["TRANS_ID"];
			if($val["TO_TRANS_ID"]>0) $previous_trans_id_arr[$val["TO_TRANS_ID"]]=$val["TO_TRANS_ID"];
			
			$previous_dtls_id_arr[$val["ID"]]=$val["ID"];
			$all_dtls_id_arr[$val["ID"]]=$val["ID"];
		}
	}
	//echo "10**select A.ID, B.CONVERSION_FACTOR from product_details_master a, lib_item_group b where a.item_group_id=b.id and a.item_category_id=4 and a.entry_form=24 and a.id in(".implode(",",$all_prod_ids_arr).") and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";die;
	$conversion_factor_result=sql_select("select A.ID, B.CONVERSION_FACTOR from product_details_master a, lib_item_group b where a.item_group_id=b.id and a.item_category_id=4 and a.entry_form=24 and a.id in(".implode(",",$all_prod_ids_arr).") and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	$conversion_factor_arr=array();
	foreach($conversion_factor_result as $val)
	{
		$conversion_factor_arr[$val["ID"]]=$val["CONVERSION_FACTOR"];
	}
	unset($conversion_factor_result);
	
	$prod_stock_sql=sql_select("select ID, CURRENT_STOCK, AVG_RATE_PER_UNIT, STOCK_VALUE from product_details_master where id in(".implode(",",$all_prod_ids_arr).") and status_active=1 and is_deleted=0");
	$prod_stock_arr=array();
	foreach($prod_stock_sql as $val)
	{
		$prod_stock_arr[$val["ID"]]["CURRENT_STOCK"]=$val["CURRENT_STOCK"];
		$prod_stock_arr[$val["ID"]]["AVG_RATE_PER_UNIT"]=$val["AVG_RATE_PER_UNIT"];
		$prod_stock_arr[$val["ID"]]["STOCK_VALUE"]=$val["STOCK_VALUE"];
	}
	unset($prod_stock_sql);
	
	if(count($updateTransIssueId_arr) >0 || count($updateTransRecvId_arr) >0)
	{
		if($variable_auto_rcv==1) $up_tr_cond=" and id not in(".implode(",",$updateTransIssueId_arr).",".implode(",",$updateTransRecvId_arr).")";
		else $up_tr_cond=" and id not in(".implode(",",$updateTransIssueId_arr).")";
		$trans_sql=sql_select("select prod_id, sum((case when transaction_type in(1,4,5) then cons_quantity else 0 end)-(case when transaction_type in(2,3,6) then cons_quantity else 0 end)) as bal
		from inv_transaction where status_active=1 and is_deleted=0 and prod_id in(".implode(",",$all_prod_ids_arr).") and store_id=$cbo_store_name_to $up_tr_cond
		group by prod_id");
		foreach($trans_sql as $val)
		{
			$stockQnty=$val[csf("bal")]*1;
			$trnsQnty=$pord_wise_trans_arr[$val[csf("prod_id")]];
			if($stockQnty < 0)
			{
				 echo "20**Transfer Quantity Not Allow Over Stock Quantity.";disconnect($con);die;
			}
		}
	}
	$update_trans_cond="";
	if(count($updateTransIssueId_arr) >0 || count($updateTransRecvId_arr) >0)
	{
		if($variable_auto_rcv==1) $update_trans_cond=" and c.id not in(".implode(",",$updateTransIssueId_arr).",".implode(",",$updateTransRecvId_arr).")";
		else $update_trans_cond=" and c.id not in(".implode(",",$updateTransIssueId_arr).")";
	}
	$sqlCon="";	
	$store_update_upto=str_replace("'","",$store_update_upto);
	if($store_update_upto > 1)
	{
		if($store_update_upto==6)
		{
			if(str_replace("'","",$cbo_floor)!=0){$sqlCon= " and c.floor_id=$cbo_floor" ;}
			if(str_replace("'","",$cbo_room)!=0){$sqlCon.= " and c.room=$cbo_room" ;}
			if(str_replace("'","",$txt_rack)!=0){$sqlCon.= " and c.rack=$txt_rack" ;}
			if(str_replace("'","",$txt_shelf)!=0){$sqlCon.= " and c.self=$txt_shelf" ;}
			if(str_replace("'","",$cbo_bin)!=0){$sqlCon.= " and c.bin_box=$cbo_bin" ;}
		}
		else if($store_update_upto==5)
		{
			if(str_replace("'","",$cbo_floor)!=0){$sqlCon= " and c.floor_id=$cbo_floor" ;}
			if(str_replace("'","",$cbo_room)!=0){$sqlCon.= " and c.room=$cbo_room" ;}
			if(str_replace("'","",$txt_rack)!=0){$sqlCon.= " and c.rack=$txt_rack" ;}
			if(str_replace("'","",$txt_shelf)!=0){$sqlCon.= " and c.self=$txt_shelf" ;}
		}
		else if($store_update_upto==4)
		{
			if(str_replace("'","",$cbo_floor)!=0){$sqlCon= " and c.floor_id=$cbo_floor" ;}
			if(str_replace("'","",$cbo_room)!=0){$sqlCon.= " and c.room=$cbo_room" ;}
			if(str_replace("'","",$txt_rack)!=0){$sqlCon.= " and c.rack=$txt_rack" ;}
		}
		else if($store_update_upto==3)
		{
			if(str_replace("'","",$cbo_floor)!=0){$sqlCon= " and c.floor_id=$cbo_floor" ;}
			if(str_replace("'","",$cbo_room)!=0){$sqlCon.= " and c.room=$cbo_room" ;}
		}
		else if($store_update_upto==2)
		{
			if(str_replace("'","",$cbo_floor)!=0){$sqlCon= " and c.floor_id=$cbo_floor" ;}
		}
	}
	
	$sql_trim = sql_select("select b.prod_id, b.po_breakdown_id, sum((case when b.entry_form in(24,73,78,112) and b.trans_type in(1,4,5) then b.quantity else 0 end)-(case when b.entry_form in(25,49,78,112) and b.trans_type in(2,3,6) then b.quantity else 0 end)) as balance 
	from order_wise_pro_details b, inv_transaction c
	where  b.trans_id=c.id and c.item_category=4 and c.company_id=$cbo_company_id and c.store_id =$cbo_store_name $sqlCon and b.po_breakdown_id in(".implode(",",$all_po_ids_arr).") and b.prod_id in(".implode(",",$all_prod_ids_arr).") and c.status_active=1 and c.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $update_trans_cond
	group by b.prod_id, b.po_breakdown_id");
	foreach($sql_trim as $row)
	{
		$trim_stock=$row[csf("balance")]*$conversion_factor_arr[$row[csf("prod_id")]];
		$trans_qnty=$pord_ord_wise_trans_arr[$row[csf("prod_id")]][$row[csf("po_breakdown_id")]];
		if($trans_qnty>$trim_stock)
		{
			echo "11**Transfer Quantity Not Allow Over Stock.";
			disconnect($con);
			die;
		}
	}
	
	$trans_sql_from=sql_select("select prod_id, sum((case when transaction_type in(1,4,5) then cons_quantity else 0 end)-(case when transaction_type in(2,3,6) then cons_quantity else 0 end)) as bal
	from inv_transaction where status_active=1 and is_deleted=0 and prod_id in(".implode(",",$all_prod_ids_arr).") and store_id=$cbo_store_name $up_tr_cond
	group by prod_id");
	foreach($trans_sql_from as $row)
	{
		$stockQnty=$row[csf("bal")]*1;
		$trnsQnty=$pord_wise_trans_arr[$row[csf("prod_id")]];
		if($trnsQnty > $stockQnty)
		{
			 echo "20**Transfer Quantity Not Allow Over Stock Quantity.";disconnect($con);die;
		}
	}
	
	
	if ($transfer_criteria==4) 
	{
		$entry_form_no=78; // order to order
		$prefix_no="TSOTOTE";
	}
	else{
		$entry_form_no=112; // Company to Company and Store to Store
		$prefix_no="TTE";
	}
	
	if($operation!=2) 
	{
		//------------Check Transfer In Date with last Transaction Date-----------------
		$max_recv_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id in(".implode(",",$all_prod_ids_arr).") and store_id=$cbo_store_name_to $up_tr_cond and status_active = 1", "max_date");      
		if($max_recv_date != "")
		{
			$max_recv_date = date("Y-m-d", strtotime($max_recv_date));
			$transfer_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_transfer_date)));
			if ($transfer_date < $max_recv_date) 
			{
				echo "20**Transfer in Date Can not Be Less Than Last Transaction Date Of This Item";
				die;
			}
		}
		
		//------------Check Transfer Out Date with last Receive Date--------------------
		$is_update_cond_for_iss = ($operation==1)? " and id not in(".implode(",",$updateTransRecvId_arr).") ": "";
		$max_issue_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id in(".implode(",",$all_prod_ids_arr).") and store_id = $cbo_store_name and transaction_type in (1,4,5) $is_update_cond_for_iss and status_active = 1 and is_deleted=0", "max_date");      
		if($max_issue_date != "")
		{
			$max_issue_date = date("Y-m-d", strtotime($max_issue_date));
			$transfer_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_transfer_date)));
			if ($transfer_date < $max_issue_date) 
			{
			   echo "20**Transfer Out Date Can not Be Less Than Last Receive Date Of This Item";
				die;
			}
		}
	}
	
	if(str_replace("'","",$update_id)!="")
	{
		$is_acknowledge = return_field_value("b.id id", "inv_item_transfer_mst a, inv_item_trans_acknowledgement b", "a.id=b.challan_id and a.id=$update_id and a.status_active=1 and a.is_acknowledge=1", "id");
		if($is_acknowledge != "" )
		{
			echo "20**Update not allowed. This Transfer Challan is already Acknowledged.\nAcknowledge System ID = $is_acknowledge";
			disconnect($con);die;
		}
	}
    
	
	
	
	
	if( $operation==0 ) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		//if($db_type==0)	{ mysql_query("BEGIN"); }
		
		$transfer_recv_num=''; $transfer_update_id='';

		if(str_replace("'","",$update_id)=="")
		{
			if($db_type==0) $year_cond="YEAR(insert_date)"; 
			else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
			else $year_cond="";//defined Later
		
			$id = return_next_id_by_sequence("INV_ITEM_TRANSFER_MST_PK_SEQ", "inv_item_transfer_mst", $con);
			$new_transfer_system_id = explode("*", return_next_id_by_sequence("INV_ITEM_TRANSFER_MST_PK_SEQ", "inv_item_transfer_mst",$con,1,$cbo_company_id,$prefix_no,$entry_form_no,date("Y",time()) ));
			
			$field_array="id, transfer_prefix, transfer_prefix_number, transfer_system_id, company_id, challan_no, transfer_date, transfer_criteria, to_company, location_id, to_location_id,entry_form, from_order_id, to_order_id, from_store_id, to_store_id, item_category, purpose, inserted_by, insert_date";
			
			$data_array="(".$id.",'".$new_transfer_system_id[1]."',".$new_transfer_system_id[2].",'".$new_transfer_system_id[0]."',".$cbo_company_id.",".$txt_challan_no.",".$txt_transfer_date.",".$cbo_transfer_criteria.",".$cbo_company_id_to.",".$cbo_location.",".$cbo_location_to.",".$entry_form_no.",".$txt_from_order_id.",".$txt_to_order_id.",".$cbo_store_name.",".$cbo_store_name_to.",".$cbo_item_category.",4,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			// echo "10**insert into inv_item_transfer_mst (".$field_array.") values ".$data_array;die;
			$transfer_recv_num=$new_transfer_system_id[0];
			$transfer_update_id=$id;
		}
		else
		{
			$field_array_update="challan_no*transfer_date*from_order_id*to_order_id*updated_by*update_date";
			$data_array_update=$txt_challan_no."*".$txt_transfer_date."*".$txt_from_order_id."*".$txt_to_order_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			
			$transfer_recv_num=str_replace("'","",$txt_system_id);
			$transfer_update_id=str_replace("'","",$update_id);

			if($variable_auto_rcv == 2) // if auto receive yes(1), then no need to acknowledgement
			{
				//echo "10**fail=2";die;
				$pre_saved_store=sql_select("select a.id, b.from_store, b.to_store from inv_item_transfer_mst a, inv_item_transfer_dtls b where a.id = b.mst_id and a.entry_form in(78,112) and a.id = $transfer_update_id and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0 group by a.id, b.from_store, b.to_store");

				if( ($pre_saved_store[0][csf("from_store")]  !=str_replace("'", "", $cbo_store_name)) || ($pre_saved_store[0][csf("to_store")]  !=str_replace("'", "", $cbo_store_name_to)) )
				{
					echo "20**Duplicate From Store and To Store is not allowed in same MRR";
					disconnect($con);
					die;
				}
			}
		}
		
		
		
		$amount=str_replace("'","",$txt_transfer_qnty)*str_replace("'","",$txt_rate);
				
		$field_array_trans="id, mst_id, transaction_criteria, company_id, prod_id, item_category, transaction_type, transaction_date, store_id, floor_id, room, rack, self, bin_box, order_id, cons_uom, cons_quantity, cons_rate, cons_amount, inserted_by, insert_date";
		
		$field_array_dtls="id, mst_id, from_prod_id, to_prod_id, from_store, floor_id, room, rack, shelf, bin_box, to_store, to_floor_id, to_room, to_rack, to_shelf, to_bin_box, from_order_id, to_order_id, item_category, item_group, transfer_qnty, rate, transfer_value, uom, remarks, trans_id, to_trans_id, inserted_by, insert_date";

		$field_array_dtls_ac="id, mst_id, dtls_id, is_acknowledge, from_prod_id, to_prod_id, from_store, floor_id, room, rack, shelf, bin_box, to_store, to_floor_id, to_room, to_rack, to_shelf, to_bin_box, item_category, item_group, transfer_qnty, rate, transfer_value, uom, remarks, inserted_by, insert_date";
		$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, color_id, quantity, order_rate, order_amount, inserted_by, insert_date";
		$field_array_prodUpdate="last_issued_qnty*current_stock*stock_value*updated_by*update_date";
		$field_array_prod_update="avg_rate_per_unit*last_purchased_qnty*current_stock*stock_value*updated_by*update_date";
		for($i=1;$i<=$tot_row;$i++)
		{
			$prodId="prod_id".$i;
			$poId="po_id".$i;
			$updateDtlsId="updateDtlsId".$i;
			$updateTransIssueId="updateTransIssueId".$i;
			$updateTransRecvId="updateTransRecvId".$i;				
			$itemGroupId="itemGroupId".$i;
			$itemDescription="itemDescription".$i;
			$brandSupp="brandSupp".$i;
			$itemColorId="itemColorId".$i;
			$itemSize="itemSize".$i;				
			$gmtsColorId="gmtsColorId".$i;
			$gmtsSizeId="gmtsSizeId".$i;
			
			$txtTransferQnty="txtTransferQnty".$i;
			$uom="uom".$i;
			$txtRate="txtRate".$i;
			$txtOrdRate="txtOrdRate".$i;
			$txtRemarks="txtRemarks".$i;
			$updateDtlsId="updateDtlsId".$i;
			$updateTransIssueId="updateTransIssueId".$i;
			$updateTransRecvId="updateTransRecvId".$i;
			$prev_prod_id=$$prodId;
			$transfer_value=$$txtTransferQnty*$$txtRate;
			
			
			if($data_array_trans!="") $data_array_trans.=",";
			$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
			$data_array_trans.="(".$id_trans.",".$transfer_update_id.",".$cbo_transfer_criteria.",".$cbo_company_id.",'".$$prodId."',".$cbo_item_category.",6,".$txt_transfer_date.",".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_bin.",'".$$poId."','".$$uom."','".$$txtTransferQnty."','".$$txtRate."','".$transfer_value."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			//$id_trans_recv=$id_trans+1;
			$id_trans_recv=0;
			if($variable_auto_rcv==1)
			{
				$id_trans_recv=return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
				$data_array_trans.=",(".$id_trans_recv.",".$transfer_update_id.",".$cbo_transfer_criteria.",".$cbo_company_id_to.",'".$$prodId."',".$cbo_item_category.",5,".$txt_transfer_date.",".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$cbo_bin_to.",'".$$poId."','".$$uom."','".$$txtTransferQnty."','".$$txtRate."','".$transfer_value."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			}
			
			if($data_array_dtls!="") $data_array_dtls.=",";
			$id_dtls = return_next_id_by_sequence("INV_ITEM_TRANSFER_DTLS_PK_SEQ", "inv_item_transfer_dtls", $con);
			$data_array_dtls.="(".$id_dtls.",".$transfer_update_id.",'".$$prodId."','".$$prodId."',".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_bin.",".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$cbo_bin_to.",'".$$poId."','".$$poId."',".$cbo_item_category.",'".$$itemGroupId."','".$$txtTransferQnty."','".$$txtRate."','".$transfer_value."','".$$uom."','".$$txtRemarks."',".$id_trans.",".$id_trans_recv.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			if($variable_auto_rcv==2) // acknowledgement_dtls_table
			{
				$id_dtls_ac = return_next_id_by_sequence("INV_ITEM_TRANS_DTLS_AC_PK_SEQ", "inv_item_transfer_dtls_ac", $con);
				$data_array_dtls_ac.="(".$id_dtls_ac.",".$transfer_update_id.",".$id_dtls.",0,'".$$prodId."','".$$prodId."',".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_bin.",".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$cbo_bin_to.",".$cbo_item_category.",'".$$itemGroupId."','".$$txtTransferQnty."','".$$txtRate."','".$transfer_value."','".$$uom."','".$$txtRemarks."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			}
			
			
			$order_trans_qnty=$$txtTransferQnty/$conversion_factor_arr[$$prodId];
			$order_amount=$order_trans_qnty*$$txtOrdRate;
			$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
			if($data_array_prop!="") $data_array_prop.=",";
			$data_array_prop.="(".$id_prop.",".$id_trans.",6,".$entry_form_no.",".$id_dtls.",'".$$poId."','".$$prodId."','".$$gmtsColorId."',".$order_trans_qnty.",'".$$txtOrdRate."','".$order_amount."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			if($variable_auto_rcv==1)
			{

				$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
				$data_array_prop.=",(".$id_prop.",".$id_trans_recv.",5,".$entry_form_no.",".$id_dtls.",'".$$poId."','".$$prodId."','".$$gmtsColorId."',".$order_trans_qnty.",'".$$txtOrdRate."','".$order_amount."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
										
			}				
		}
		
		
		$rID=$rID2=$rID3=$rID4=$prodUpdate=$prod=$rID5=true;
		
		if(str_replace("'","",$update_id)=="")
		{
			$rID=sql_insert("inv_item_transfer_mst",$field_array,$data_array,0);
		}
		else
		{
			$rID=sql_update("inv_item_transfer_mst",$field_array_update,$data_array_update,"id",$update_id,1);
		}
		//echo "10**insert into inv_transaction (".$field_array_trans.") values ".$data_array_trans;oci_rollback($con);disconnect($con);die;
		$rID2=sql_insert("inv_transaction",$field_array_trans,$data_array_trans,0);
		
		// echo "insert into inv_item_transfer_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
		$rID3=sql_insert("inv_item_transfer_dtls",$field_array_dtls,$data_array_dtls,0);
		
		//echo "insert into order_wise_pro_details (".$field_array_proportionate.") values ".$data_array_prop;die;
		$rID4=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,1);

		if($variable_auto_rcv==2) // inv_item_transfer_dtls_ac
		{
			// echo "10**insert into inv_item_transfer_dtls_ac (".$field_array_dtls_ac.") values ".$data_array_dtls_ac;die;
			$rID5=sql_insert("inv_item_transfer_dtls_ac",$field_array_dtls_ac,$data_array_dtls_ac,0);
		}
		
		//echo "10**$rID=$rID2=$rID3=$rID4=$rID5";oci_rollback($con);disconnect($con);die;
		
		$txt_from_order_id=str_replace("'","",$txt_from_order_id);
		//echo $flag;die;
		if($db_type==0)
		{
			if($rID && $rID2 && $rID3 && $rID4 && $rID5)
			{
				mysql_query("COMMIT");  
				echo "0**".$transfer_update_id."**".$transfer_recv_num."**".implode(",",$all_po_ids_arr)."**".str_replace("'","",$cbo_store_name);
			}
			else
			{
				mysql_query("ROLLBACK"); 

				echo "5**0**"."&nbsp;"."**".$txt_from_order_id;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2 && $rID3 && $rID4 && $rID5)
			{
				oci_commit($con);   
				echo "0**".$transfer_update_id."**".$transfer_recv_num."**".implode(",",$all_po_ids_arr)."**".str_replace("'","",$cbo_store_name);
			}
			else
			{
				oci_rollback($con);
				echo "5**0**"."&nbsp;"."**".$txt_from_order_id;
			}
		}
		
		disconnect($con);
		die;
	}
	else if ($operation==1) // Update Here----------------------------------------------------------
	{
		$con = connect();		
		//if($db_type==0)	{ mysql_query("BEGIN"); }
		
		$field_array_update="challan_no*transfer_date*from_order_id*to_order_id*updated_by*update_date";
		$data_array_update=$txt_challan_no."*".$txt_transfer_date."*".$txt_from_order_id."*".$txt_to_order_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		/*$rID=sql_update("inv_item_transfer_mst",$field_array_update,$data_array_update,"id",$update_id,1);
		if($rID) $flag=1; else $flag=0;*/
		
		$field_array_trans="prod_id*transaction_date*order_id*cons_uom*cons_quantity*cons_rate*cons_amount*store_id*floor_id*room*rack*self*bin_box*updated_by*update_date";
		$field_array_dtls="from_prod_id*to_prod_id*item_group*to_store*to_floor_id*to_room*to_rack*to_shelf*to_bin_box*from_order_id*to_order_id*transfer_qnty*rate*transfer_value*uom*remarks*updated_by*update_date";
		$field_array_prodUpdate="last_issued_qnty*current_stock*stock_value*updated_by*update_date";
		$field_array_prod_update="avg_rate_per_unit*last_purchased_qnty*current_stock*stock_value*updated_by*update_date";
		
		$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, color_id, quantity, order_rate, order_amount, inserted_by, insert_date";
		
		for($i=1;$i<=$tot_row;$i++)
		{
			$prodId="prod_id".$i;
			$poId="po_id".$i;
			$updateDtlsId="updateDtlsId".$i;
			$updateTransIssueId="updateTransIssueId".$i;
			$updateTransRecvId="updateTransRecvId".$i;				
			$itemGroupId="itemGroupId".$i;
			$itemDescription="itemDescription".$i;
			$brandSupp="brandSupp".$i;
			$itemColorId="itemColorId".$i;
			$itemSize="itemSize".$i;				
			$gmtsColorId="gmtsColorId".$i;
			$gmtsSizeId="gmtsSizeId".$i;
			
			$txtTransferQnty="txtTransferQnty".$i;
			$uom="uom".$i;
			$txtRate="txtRate".$i;
			$txtOrdRate="txtOrdRate".$i;
			$txtRemarks="txtRemarks".$i;
			$updateDtlsId="updateDtlsId".$i;
			$updateTransIssueId="updateTransIssueId".$i;
			$updateTransRecvId="updateTransRecvId".$i;
			$prev_prod_id=$$prodId;
			$transfer_value=$$txtTransferQnty*$$txtRate;
			
			
			$updateTransID_array[]=$$updateTransIssueId; 
			$updateTransID_data[$$updateTransIssueId]=explode("*",("".$$prodId."*".$txt_transfer_date."*'".$$poId."'*'".$$uom."'*'".$$txtTransferQnty."'*'".$$txtRate."'*'".$transfer_value."'*".$cbo_store_name."*".$cbo_floor."*".$cbo_room."*".$txt_rack."*".$txt_shelf."*".$cbo_bin."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			unset($previous_trans_id_arr[$$updateTransIssueId]);
			
			//$id_trans_recv=$id_trans+1;
			$id_trans_recv=0;
			if($variable_auto_rcv==1)
			{
				$updateTransID_array[]=$$updateTransRecvId; 
				$updateTransID_data[$$updateTransRecvId]=explode("*",("".$$prodId."*".$txt_transfer_date."*'".$$poId."'*'".$$uom."'*'".$$txtTransferQnty."'*'".$$txtRate."'*'".$transfer_value."'*".$cbo_store_name_to."*".$cbo_floor_to."*".$cbo_room_to."*".$txt_rack_to."*".$txt_shelf_to."*".$cbo_bin_to."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				unset($previous_trans_id_arr[$$updateTransRecvId]);
			}
			
			$updateDtlsID_array[]=$$updateDtlsId;
			$data_array_dtls[$$updateDtlsId]=explode("*",("".$$prodId."*".$$prodId."*'".$$itemGroupId."'*".$cbo_store_name_to."*".$cbo_floor_to."*".$cbo_room_to."*".$txt_rack_to."*".$txt_shelf_to."*".$cbo_bin_to."*'".$$poId."'*'".$$poId."'*'".$$txtTransferQnty."'*'".$$txtRate."'*'".$transfer_value."'*'".$$uom."'*'".$$txtRemarks."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			unset($previous_dtls_id_arr[$$updateDtlsId]);
			
			
			$order_trans_qnty=$$txtTransferQnty/$conversion_factor_arr[$$prodId];
			$order_amount=$order_trans_qnty*$$txtOrdRate;
			$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
			if($data_array_prop!="") $data_array_prop.=",";
			$data_array_prop.="(".$id_prop.",".$$updateTransIssueId.",6,".$entry_form_no.",".$$updateDtlsId.",'".$$poId."','".$$prodId."','".$$gmtsColorId."',".$order_trans_qnty.",'".$$txtOrdRate."','".$order_amount."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			if($variable_auto_rcv==1)
			{
				$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
				$data_array_prop.=",(".$id_prop.",".$$updateTransRecvId.",5,".$entry_form_no.",".$$updateDtlsId.",'".$$poId."',".$$prodId.",'".$$gmtsColorId."',".$order_trans_qnty.",'".$$txtOrdRate."','".$order_amount."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
										
			}				
		}
		
		//echo "10**<pre>";print_r($previous_trans_id_arr);echo "<pre>";print_r($previous_dtls_id_arr);oci_rollback($con);disconnect($con);die;	
		
		$rID=$rID2=$rID3=$query=$rID5=$rID4=$rID6=$rID7=true;
		
		if(count($previous_trans_id_arr)>0)
		{
			$rID6=execute_query("Update inv_transaction set status_active=0, is_deleted=1, updated_by=$user_id, update_date='$pc_date_time' where id in(".implode(",",$previous_trans_id_arr).")");
			$rID7=execute_query("Update inv_item_transfer_dtls set status_active=0, is_deleted=1, updated_by=$user_id, update_date='$pc_date_time' where id in(".implode(",",$previous_dtls_id_arr).")");
		}
		
		$rID=sql_update("inv_item_transfer_mst",$field_array_update,$data_array_update,"id",$update_id,1);
		//echo "10**".bulk_update_sql_statement("inv_transaction","id",$field_array_trans,$updateTransID_data,$updateTransID_array);die;
		$rID2=execute_query(bulk_update_sql_statement("inv_transaction","id",$field_array_trans,$updateTransID_data,$updateTransID_array));
		$rID3=execute_query(bulk_update_sql_statement("inv_item_transfer_dtls","id",$field_array_dtls,$data_array_dtls,$updateDtlsID_array));
				
		if($variable_auto_rcv==2) //acknowledgement details table update, 
		{
			$rID5=execute_query(bulk_update_sql_statement("inv_item_transfer_dtls_ac","dtls_id",$field_array_dtls,$data_array_dtls,$updateDtlsID_array));
		}
		
		if($rID && $rID2 && $rID3 && $rID5 && $rID6 && $rID7)
		{
			$query = execute_query("DELETE FROM order_wise_pro_details WHERE dtls_id in(".implode(",",$all_dtls_id_arr).") and entry_form=$entry_form_no"); 
			$rID4=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,1);
		}
		
		//echo "10**$rID=$rID2=$rID3=$query=$rID5=$rID4=$rID6=$rID7";oci_rollback($con);disconnect($con);die;
		 
		$txt_from_order_id=str_replace("'","",$txt_from_order_id);
		if($db_type==0)
		{
			if($rID && $rID2 && $rID3 && $rID5 && $rID6 && $rID7 && $query && $rID4)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_system_id)."**".implode(",",$all_po_ids_arr)."**".str_replace("'","",$cbo_store_name);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "6**0**"."&nbsp;"."**1"."**".$txt_from_order_id;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2 && $rID3 && $rID5 && $rID6 && $rID7 && $query && $rID4)
			{
				oci_commit($con);   
				echo "1**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_system_id)."**".implode(",",$all_po_ids_arr)."**".str_replace("'","",$cbo_store_name);
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
	else if ($operation==2)   // Delete Here
	{ 
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		//if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		$update_id=str_replace("'","",$update_id);
		$previous_prod_id=str_replace("'","",$cbo_item_desc);
		$update_dtls_id=str_replace("'","",$update_dtls_id);
		$update_trans_issue_id=str_replace("'","",$update_trans_issue_id);
		$update_trans_recv_id=str_replace("'","",$update_trans_recv_id);
		$all_trans_id=$update_trans_issue_id.",".$update_trans_recv_id;
		$txt_to_order_id=str_replace("'","",$txt_to_order_id);
		
		//echo "10**$update_id=$previous_prod_id=$update_dtls_id=$update_trans_issue_id=$update_trans_recv_id";die;
		if($update_id>0 && $previous_prod_id>0 && $update_dtls_id>0 && $update_trans_issue_id>0 && $update_trans_recv_id>0)
		{
			
			$store_stock=sql_select("select b.prod_id, b.po_breakdown_id, sum((case when b.trans_type in(1,4,5) then b.quantity else 0 end)-(case when b.trans_type in(2,3,6) then b.quantity else 0 end)) as store_stock_qnty 
			from inv_transaction a, order_wise_pro_details b 
			where a.id=b.trans_id and a.prod_id in(".implode(",",$all_prod_ids_arr).") and a.store_id=$cbo_store_name_to and b.po_breakdown_id in(".implode(",",$all_po_ids_arr).") and b.entry_form in(24,25,49,73,78,112) and a.status_active=1 and b.status_active=1
			group by b.prod_id, b.po_breakdown_id");
			foreach($sql_trim as $row)
			{
				$store_stock_qnty=$row[csf("store_stock_qnty")];
				if($store_stock_qnty <= 0)
				{
					echo "20**Order Store Wise Stock Less Then Zero, \n Please Delete Next Issue Or Receive Return, \n More Information Please See Order Wise Trims Receive Issue And Stock.";
					disconnect($con);die;
				}
			}
			
			$row_propotionate=sql_select( "select id, po_breakdown_id, quantity, order_rate, order_amount, trans_type
			from order_wise_pro_details where trans_id in($all_trans_id) and status_active=1 and is_deleted=0" );
			$propotionate_data=array();
			foreach($row_propotionate as $row)
			{
				$all_order_id.=$row[csf("po_breakdown_id")].",";
				if($row[csf("trans_type")]==6)
				{
					$propotionate_data[$row[csf("po_breakdown_id")]]["quantity_issue"]+=$row[csf("quantity")];
					$propotionate_data[$row[csf("po_breakdown_id")]]["order_amount_issue"]+=$row[csf("order_amount")];
				}
				else
				{
					$propotionate_data[$row[csf("po_breakdown_id")]]["quantity_rcv"]+=$row[csf("quantity")];
					$propotionate_data[$row[csf("po_breakdown_id")]]["order_amount_rcv"]+=$row[csf("order_amount")];
				}
				
			}
			$all_order_id=chop($all_order_id,",");
			
			
			$field_arr="status_active*is_deleted*updated_by*update_date";
			$data_arr="0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			$rID=$rID2=$rID3=true;
			
			$rID=execute_query("update inv_transaction set status_active=0, is_deleted=1, updated_by=".$_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."' where id in($all_trans_id)");
			$rID2=sql_update("inv_item_transfer_dtls",$field_arr,$data_arr,"id",$update_dtls_id,1);
			$rID3=execute_query("update order_wise_pro_details set status_active=0, is_deleted=1, updated_by=".$_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."' where trans_id in($all_trans_id)");
			
			
			//echo "10** $rID && $rID2 && $rID3";oci_rollback($con);disconnect($con);die;
			if($db_type==0)
			{
				if($rID && $rID2 && $rID3)
				{
					mysql_query("COMMIT");  
					echo "2**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_system_id)."**0";
				}
				else
				{
					mysql_query("ROLLBACK"); 
					echo "7**0**0**1";
				}
			}
	
			if($db_type==2 || $db_type==1 )
			{
				if($rID && $rID2 && $rID3)
				{
					oci_commit($con);  
					echo "2**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_system_id)."**0";
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

if ($action=="trims_store_order_to_order_transfer_print")
{
    extract($_REQUEST);
	$data=explode('*',$data);
	$sql="select id, transfer_system_id, transfer_date, challan_no, from_order_id, to_order_id, item_category, transfer_criteria, to_company, from_store_id, to_store_id
	from inv_item_transfer_mst where id='$data[1]' and company_id='$data[0]'";
	$dataArray=sql_select($sql);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$country_arr=return_library_array( "select id, country_name from  lib_country", "id", "country_name"  );
	$store_name_arr = return_library_array("select a.id, a.store_name from lib_store_location a", 'id', 'store_name');
	
	$transfer_criteria = $dataArray[0][csf('transfer_criteria')];
	if( $transfer_criteria == 1 )
	{
		// echo $data[3];
		$to_po_array=array();
		$sql_po=sql_select("select a.job_no, a.buyer_name, a.style_ref_no, b.po_number, b.po_quantity, b.pub_shipment_date, b.id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name='$data[3]'");
		foreach($sql_po as $row_po)
		{
			$to_po_array[$row_po[csf('id')]]['no']=$row_po[csf('po_number')];
			$to_po_array[$row_po[csf('id')]]['job']=$row_po[csf('job_no')];
			$to_po_array[$row_po[csf('id')]]['buyer']=$row_po[csf('buyer_name')];
			$to_po_array[$row_po[csf('id')]]['qnty']=$row_po[csf('po_quantity')];
			$to_po_array[$row_po[csf('id')]]['date']=$row_po[csf('pub_shipment_date')];
			$to_po_array[$row_po[csf('id')]]['style']=$row_po[csf('style_ref_no')];
		}
	}
	$po_array=array();
	$sql_po=sql_select("select a.job_no, a.buyer_name, a.style_ref_no, b.po_number, b.po_quantity, b.pub_shipment_date, b.id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name='$data[0]'");
	foreach($sql_po as $row_po)
	{
		$po_array[$row_po[csf('id')]]['no']=$row_po[csf('po_number')];
		$po_array[$row_po[csf('id')]]['job']=$row_po[csf('job_no')];
		$po_array[$row_po[csf('id')]]['buyer']=$row_po[csf('buyer_name')];
		$po_array[$row_po[csf('id')]]['qnty']=$row_po[csf('po_quantity')];
		$po_array[$row_po[csf('id')]]['date']=$row_po[csf('pub_shipment_date')];
		$po_array[$row_po[csf('id')]]['style']=$row_po[csf('style_ref_no')];
	}
	
	$product_arr = return_library_array("select id, product_name_details from product_details_master where item_category_id=4 and status_active=1 and is_deleted=0","id","product_name_details");
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
	        <tr>
	        	<td width="125"><strong>Transfer ID :</strong></td><td width="200"><? echo $dataArray[0][csf('transfer_system_id')]; ?></td>
	            <td width="125"><strong>Transfer Date:</strong></td><td width="185"><? echo change_date_format($dataArray[0][csf('transfer_date')]); ?></td>
	            <td width="125"><strong>Challan No.:</strong></td><td width="140"><? echo $dataArray[0][csf('challan_no')]; ?></td>
	        </tr>
	        <tr>
	            <td><strong>From Company:</strong></td> <td width="200"><? echo $company_library[$data[0]]; ?></td>
	            <td><strong>From order No:</strong></td> <td width="185"><? echo $po_array[$dataArray[0][csf('from_order_id')]]['no']; ?></td>
	            <td><strong>From ord Qnty:</strong></td> <td width="140"><? echo $po_array[$dataArray[0][csf('from_order_id')]]['qnty']; ?></td>
	        </tr>
	        <tr>	            
	            <td><strong>From ord Buyer:</strong></td> <td width="200"><? echo $buyer_library[$po_array[$dataArray[0][csf('from_order_id')]]['buyer']]; ?></td>
	            <td><strong>From Style Ref.:</strong></td> <td width="185"><? echo $po_array[$dataArray[0][csf('from_order_id')]]['style']; ?></td>
	            <td><strong>From Job No:</strong></td> <td width="140"><? echo $po_array[$dataArray[0][csf('from_order_id')]]['job']; ?></td>

	        </tr>
	        <tr>	            
	            <td><strong>From Ship. Date:</strong></td> <td width="200"><? echo change_date_format($po_array[$dataArray[0][csf('from_order_id')]]['date']); ?></td>
	            <td><strong>From Store:</strong></td><td width="325" colspan="2"><? echo $store_name_arr[$dataArray[0][csf('from_store_id')]]; ?></td>
	        </tr>
	        <tr>
	            <td><strong>To Company:</strong></td> <td width="200"><? echo $company_library[$data[3]]; ?></td>
	            <td><strong>To order No:</strong></td> <td width="185"><? echo ($transfer_criteria == 1) ? $to_po_array[$dataArray[0][csf('to_order_id')]]['no'] : $po_array[$dataArray[0][csf('to_order_id')]]['no']; // echo $po_array[$dataArray[0][csf('to_order_id')]]['no']; ?></td>
	            <td><strong>To ord Qnty:</strong></td> <td width="140"><? echo ($transfer_criteria == 1) ? $to_po_array[$dataArray[0][csf('to_order_id')]]['qnty'] : $po_array[$dataArray[0][csf('to_order_id')]]['qnty'] ; //echo $po_array[$dataArray[0][csf('to_order_id')]]['qnty']; ?></td>
	        </tr>
	        <tr>	            
	            <td><strong>To ord Buyer:</strong></td> <td width="200"><? echo ($transfer_criteria == 1) ? $buyer_library[$to_po_array[$dataArray[0][csf('to_order_id')]]['buyer']] : $buyer_library[$po_array[$dataArray[0][csf('to_order_id')]]['buyer']] ; //echo $buyer_library[$po_array[$dataArray[0][csf('to_order_id')]]['buyer']]; ?></td>
	            <td><strong>To Style Ref.:</strong></td> <td width="185"><? echo ($transfer_criteria == 1) ? $to_po_array[$dataArray[0][csf('to_order_id')]]['style'] : $po_array[$dataArray[0][csf('to_order_id')]]['style'] ; //echo $po_array[$dataArray[0][csf('to_order_id')]]['style']; ?></td>
	            <td><strong>To Job No:</strong></td> <td width="140"><? echo ($transfer_criteria == 1) ? $to_po_array[$dataArray[0][csf('to_order_id')]]['job'] : $po_array[$dataArray[0][csf('to_order_id')]]['job'] ; //echo $po_array[$dataArray[0][csf('to_order_id')]]['job']; ?></td>
	        </tr>
	        <tr>	            
	            <td><strong>To Ship. Date:</strong></td> <td width="200"><? echo ($transfer_criteria == 1) ? change_date_format($to_po_array[$dataArray[0][csf('to_order_id')]]['date']) : change_date_format($po_array[$dataArray[0][csf('to_order_id')]]['date']) ; //echo change_date_format($po_array[$dataArray[0][csf('to_order_id')]]['date']); ?></td>
	            <td><strong>To Store:</strong></td><td width="325" colspan="2"><? echo $store_name_arr[$dataArray[0][csf('to_store_id')]]; ?>
	        </tr>
	    </table>
	    <br>
	    <div style="width:100%;">
		    <table align="right" cellspacing="0" width="900"  border="1" rules="all" class="rpt_table" style="margin-top: 20px;">
		        <thead bgcolor="#dddddd" align="center">
		            <th width="30">SL</th>
		            <th width="120" >Item Category</th>
		            <th width="200" >Item Description</th>
                    <th width="120" >Item Color</th>
                    <th width="120" >Item Size</th>
		            <th width="70" >UOM</th>
		            <th width="100" >Transfered Qnty</th>
		        </thead>
		        <tbody> 
		   
					<?
					$sql_dtls="select a.id, a.item_category, a.item_group, a.from_prod_id, b.PRODUCT_NAME_DETAILS, c.COLOR_NAME, d.SIZE_NAME,  a.transfer_qnty, a.uom from inv_item_transfer_dtls a left join product_details_master b on b.id = a.FROM_PROD_ID left join lib_color c on c.id = b.ITEM_COLOR left join lib_size d on d.id = b.GMTS_SIZE where a.mst_id='$data[1]' and a.status_active=1 and a.is_deleted=0";
//					echo $sql_dtls;
                    $sql_result= sql_select($sql_dtls);
					$i=1;
					foreach($sql_result as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						
						$transfer_qnty=$row[csf('transfer_qnty')];
						$transfer_qnty_sum += $transfer_qnty;
						
						?>
						<tr bgcolor="<? echo $bgcolor; ?>">
			                <td align="center"><? echo $i; ?></td>
			                <td><? echo $item_category[$row[csf("item_category")]]; ?></td>
			                <td><? echo $row[csf("product_name_details")]; ?></td>
                            <td><? echo $row[csf("color_name")]; ?></td>
                            <td><? echo $row[csf("size_name")]; ?></td>
			                <td align="center"><? echo $unit_of_measurement[$row[csf("uom")]]; ?></td>
			                <td align="right"><? echo number_format($row[csf("transfer_qnty")],2); ?></td>
						</tr>
						<? $i++; 
					} ?>
			    </tbody>
		        <tfoot>
		            <tr>
		                <td colspan="6" align="right"><strong>Total :</strong></td>
		                <td align="right"><?php echo number_format($transfer_qnty_sum,2); ?></td>
		            </tr>                           
		        </tfoot>
		    </table>
	        <br>
			 <?
	            echo signature_table(266, $data[0], "900px");
	         ?>
	    </div>
	</div>   
 	<?
 	exit();	
}

if ($action=="trims_store_order_to_order_transfer_print2")
{
	extract($_REQUEST);
	$data=explode('*',$data);	
	$cbo_company_id = $data[0];
	$mst_id = $data[1];
	$cbo_company_id_to = $data[3];

	$sql="select ID, TRANSFER_SYSTEM_ID, TRANSFER_DATE, CHALLAN_NO, FROM_ORDER_ID, TO_ORDER_ID, ITEM_CATEGORY, TRANSFER_CRITERIA, TO_COMPANY, FROM_STORE_ID, TO_STORE_ID
	from inv_item_transfer_mst where id='$mst_id' and company_id='$cbo_company_id'";
	$dataArray=sql_select($sql);

	$company_library=return_library_array("select id, company_name from lib_company", "id", "company_name");
	$buyer_library=return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$store_library=return_library_array("select id, store_name from  lib_store_location", "id", "store_name");
	$country_arr=return_library_array("select id, country_name from  lib_country", "id", "country_name");
	$item_group_arr = return_library_array("select id, item_name from lib_item_group", "id", "item_name");
	?>
	<style type="text/css">
		hr{margin: 0px;}
		.wrd_brk{word-break: break-all;}
	</style>
	<div style="width:930px;">
	    <table width="900" cellspacing="0" align="right">
	        <tr>
	            <td colspan="6" align="center" style="font-size:xx-large"><strong><? echo $company_library[$cbo_company_id]; ?></strong></td>
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
	        <table cellspacing="0" width="800" align="center" border="1" rules="all" class="">
	        <tr>
	        	<td width="125"><strong>Transfer ID:</strong></td><td width="175px"><? echo $dataArray[0]['TRANSFER_SYSTEM_ID']; ?></td>
	            <td width="125"><strong>Transfer Criteria:</strong></td><td width="175px"><? echo $item_transfer_criteria[$dataArray[0]['TRANSFER_CRITERIA']]; ?></td>
	            <td width="125"><strong>Item Category:</strong></td><td width="175px"><? echo $item_category[$dataArray[0]['ITEM_CATEGORY']]; ?></td>
	        </tr>
	        <tr>
	            <td><strong>Transfer Date:</strong></td><td width="175px"><? echo change_date_format($dataArray[0]['TRANSFER_DATE']); ?></td>
	            <td><strong>To Company:</strong></td><td width="175px"><? echo $company_library[$cbo_company_id_to]; ?></td>
	            <td><strong>Challan No:</strong></td><td width="175px"><? echo $dataArray[0]['CHALLAN_NO']; ?></td>
	        </tr>
	    </table>
	    <br>
	    <div style="width:100%;">
		    <table align="right" cellspacing="0" width="900"  border="1" rules="all" class="rpt_table">
		        <thead bgcolor="#dddddd" align="center">
		            <th width="50">SL</th>
		            <th width="160">From Referance</th>
		            <th width="160">To Referance</th>
		            <th width="100">Item Group</th>
		            <th width="140">Item Description</th>
		            <th width="70">UOM</th>
		            <th width="100">Transfered Qnty</th>
		            <th width="100">Remarks</th>
		        </thead>
		        <tbody>		   
					<?
					$sql_dtls="SELECT a.FROM_ORDER_ID, a.TO_ORDER_ID, b.FROM_STORE, b.TO_STORE, b.FROM_PROD_ID, b.TO_PROD_ID, sum(b.transfer_qnty) as TRANSFER_QNTY, b.UOM, b.ITEM_GROUP, b.REMARKS
					from inv_item_transfer_mst a, inv_item_transfer_dtls b 
					where a.id=b.mst_id and a.id='$mst_id' and a.company_id='$cbo_company_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
					group by a.from_order_id, a.to_order_id, b.from_store, b.to_store, b.from_prod_id, b.to_prod_id, b.uom, b.item_group, b.remarks 
					order by b.from_prod_id";			
					$sql_result= sql_select($sql_dtls);

					$po_id_array=array();
					$prod_id_array=array();
			        foreach ($sql_result as $row) 
			        {        
			        	$po_id_array[]=$row['FROM_ORDER_ID'].','.$row['TO_ORDER_ID'];
			        	$prod_id_array[]=$row['FROM_PROD_ID'].','.$row['TO_PROD_ID'];
			        }
			        $poIds = implode(",",array_unique($po_id_array));
			        $prodIds = implode(",",array_unique($prod_id_array));

			        $sql_order="SELECT a.ID, b.BUYER_NAME, b.STYLE_REF_NO, b.JOB_NO, a.PO_NUMBER from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted =0 and b.status_active=1 and b.is_deleted=0 and a.id in($poIds)";
			        $sql_order_res = sql_select($sql_order);
			        $order_array=array();
			        foreach ($sql_order_res as $row) {
			        	$order_array[$row['ID']]['BUYER_NAME']=$buyer_library[$row['BUYER_NAME']];
			        	$order_array[$row['ID']]['STYLE_REF_NO']=$row['STYLE_REF_NO'];
			        	$order_array[$row['ID']]['JOB_NO']=$row['JOB_NO'];
			        	$order_array[$row['ID']]['PO_NUMBER']=$row['PO_NUMBER'];
			        }

			        $product_arr = return_library_array("select id, product_name_details from product_details_master where id in($prodIds) and item_category_id=4 and status_active=1 and is_deleted=0","id","product_name_details");

					$i=1;
					foreach($sql_result as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
						?>
						<tr bgcolor="<? echo $bgcolor; ?>">
			                <td align="center"><?= $i; ?></td>
			                <td width="160" class="wrd_brk">Store:<? echo $store_library[$row['FROM_STORE']]; ?><hr>Job:<?= $order_array[$row['FROM_ORDER_ID']]['JOB_NO']; ?><hr>Style:<?= $order_array[$row['FROM_ORDER_ID']]['STYLE_REF_NO']; ?><hr>Buyer:<?= $order_array[$row['FROM_ORDER_ID']]['BUYER_NAME']; ?><hr>Order No:<?= $order_array[$row['FROM_ORDER_ID']]['PO_NUMBER']; ?></td>
			                <td width="160" class="wrd_brk">Store:<? echo $store_library[$row['FROM_STORE']]; ?><hr>Job:<?= $order_array[$row['TO_ORDER_ID']]['JOB_NO']; ?><hr>Style:<?= $order_array[$row['TO_ORDER_ID']]['STYLE_REF_NO']; ?><hr>Buyer:<?= $order_array[$row['TO_ORDER_ID']]['BUYER_NAME']; ?><hr>Order No:<?= $order_array[$row['TO_ORDER_ID']]['PO_NUMBER']; ?></td>
			                <td width="100" class="wrd_brk"><?= $item_group_arr[$row['ITEM_GROUP']]; ?></td>
			                <td width="140" class="wrd_brk"><?= $product_arr[$row['FROM_PROD_ID']]; ?></td>
			                <td width="70" class="wrd_brk" align="center"><?= $unit_of_measurement[$row['UOM']]; ?></td>
			                <td width="100" class="wrd_brk" align="right"><?= number_format($row['TRANSFER_QNTY'],2); ?></td>
			                <td class="wrd_brk" align="center"><?= $row['REMARKS']; ?></td>
						</tr>
						<? 
						$i++; 
						$tot_transfer_qnty+=$row['TRANSFER_QNTY'];
					} 
					?>
			    </tbody>
		        <tfoot>
		            <tr>
		                <td colspan="6" align="right"><strong>Total :</strong></td>
		                <td align="right"><?= number_format($tot_transfer_qnty,2); ?></td>
		                <td></td>
		            </tr>                           
		        </tfoot>
		    </table>
	        <br>
			 <?
	            //echo signature_table(24, $data[0], "900px");
	         ?>
	    </div>
	</div>   
 	<?
 	exit();
}
?>
