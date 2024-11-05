<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
$permission=$_SESSION['page_permission'];
include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$user_id = $_SESSION['logic_erp']['user_id'];
$userCredential = sql_select("SELECT unit_id as company_id, store_location_id, item_cate_id, supplier_id FROM user_passwd where id=$user_id");
$company_id = $userCredential[0][csf('company_id')];
$supplier_id = $userCredential[0][csf('supplier_id')];
$store_location_id = $userCredential[0][csf('store_location_id')];
$item_cate_id = $userCredential[0][csf('item_cate_id')];

if ($company_id !='') {
    $company_credential_cond = "and comp.id in($company_id)";
}
if ($store_location_id !='') {
    $store_location_credential_cond = "and a.id in($store_location_id)"; 
}
if ($supplier_id !='') {
    $supplier_credential_cond = "and c.id in($supplier_id)";
}

if ($action=="upto_variable_settings")
{
	extract($_REQUEST);
	/*echo "select store_method from variable_settings_inventory where company_name='$cbo_company_id' and item_category_id=13 and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1";die;*/
	echo $variable_inventory=return_field_value("store_method","variable_settings_inventory","company_name='$cbo_company_id' and item_category_id=4 and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1");
	exit();
}

if($action=="varible_inventory")
{
	$sql_variable_inventory=sql_select("select id, independent_controll, rate_optional, is_editable, rate_edit  from variable_settings_inventory where company_name=$data and variable_list=20 and status_active=1 and menu_page_id=24");
	if(count($sql_variable_inventory)>0)
	{
		echo "1**".$sql_variable_inventory[0][csf("independent_controll")]."**".$sql_variable_inventory[0][csf("rate_optional")]."**".$sql_variable_inventory[0][csf("is_editable")]."**".$sql_variable_inventory[0][csf("rate_edit")];
	}
	else
	{
		echo "0**".$sql_variable_inventory[0][csf("independent_controll")]."**".$sql_variable_inventory[0][csf("rate_optional")]."**".$sql_variable_inventory[0][csf("is_editable")]."**".$sql_variable_inventory[0][csf("rate_edit")];
	}
	/*$variable_inventory=return_field_value("store_method","variable_settings_inventory","company_name=$data and item_category_id=8 and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1");
	echo "**".$variable_inventory;
	$variable_lot=return_field_value("auto_transfer_rcv","variable_settings_inventory","company_name=$data and variable_list=32 and status_active=1 and is_deleted=0");
	echo "**".$variable_lot;*/
	die;
}

if($action=="load_drop_down_supplier")
{
	$data_ref=explode("**",$data);
	//echo "<pre>";
	//print_r($data_ref);
	
	$company_id=$data_ref[0];	
	$sup_type=$data_ref[1];
	$supplier_id=$data_ref[2];
	if($sup_type==3 || $sup_type==5)
	{
		//echo create_drop_down( "cbo_supplier_name", 172, "select id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name", "id,company_name",1, "-- Select Company --", "", "get_php_form_data( this.value, 'load_drop_down_attention', 'requires/trims_booking_multi_job_controllerurmi');",0,"" );
		echo create_drop_down( "cbo_supplier_name", 142,"select id,company_name from lib_company where status_active=1 and is_deleted=0 and id=$supplier_id order by company_name",'id,company_name', 1, '-- Select Supplier --',0,0,1);
	}
	else
	{
		echo create_drop_down( "cbo_supplier_name", 142,"select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company=$company_id and b.party_type in(4,5) and c.status_active=1 and c.is_deleted=0 $supplier_credential_cond group by c.id, c.supplier_name order by supplier_name",'id,supplier_name', 1, '-- Select Supplier --',0,0,1);
	}
	
	exit();
}
if($action=="load_drop_down_supplier_new")
{  
	extract($data);
	$exdata = explode("*",$data);
	
	$comId = $exdata[0];
	$suppId = $exdata[1];
	$user_supplier_cond = $suppId ? "and c.id in ($suppId)" :"";
	
	
  
	echo create_drop_down("cbo_supplier_name", 142, "SELECT DISTINCT c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$comId' and b.party_type in(4,5) and c.status_active IN(1) and c.is_deleted=0 group by c.id, c.supplier_name   UNION ALL  SELECT DISTINCT c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c, inv_receive_master d where c.id=b.supplier_id and a.supplier_id = b.supplier_id and c.id=d.supplier_id and a.tag_company='$comId' $user_supplier_cond and b.party_type  in(4,5) and c.status_active IN(1,3) and c.is_deleted=0 group by c.id, c.supplier_name    order by supplier_name    ", "id,supplier_name", 1, "-- Select --", 0, "", 0);
	exit();
}

if($action=="load_drop_down_buyer")
{
	$data=explode("_",$data);
	//if($data[1]==1) $party="1,3,21,90"; else 
	$party="1";
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in ($party)) order by buy.buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, "","" );
	exit();
}

/*if ($action=="load_drop_down_store")
{
	echo create_drop_down( "cbo_store_name", 122, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id='$data' and b.category_type=4 and a.status_active=1 and a.is_deleted=0 $store_location_credential_cond group by a.id, a.store_name order by a.store_name","id,store_name", 1, "--Select store--", 0, "" );
	exit();
}*/

if ($action=="load_room_rack_self_bin")
{
	load_room_rack_self_bin("requires/trims_receive_entry_controller",$data);
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

//$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0","id","color_name");
//$size_arr = return_library_array("select id, size_name from lib_size","id","size_name");



if ($action=="wo_pi_popup")
{
	echo load_html_head_contents("WO/PI Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $recieve_basis."__".$cbo_company_id;
    ?> 
	<script>
	 //var recieve_basis='<?echo $recieve_basis ?>';
		
		function js_set_value(id,no,type,data)
		{
			$('#hidden_wo_pi_id').val(id);
			$('#hidden_wo_pi_no').val(no);
			$('#booking_without_order').val(type);
			$('#hidden_data').val(data);
			parent.emailwindow.hide();
		}

		function load_buyer()
		{
			var cbo_company_id='<? echo $cbo_company_id; ?>';
			load_drop_down('trims_receive_entry_controller',cbo_company_id, 'load_drop_down_buyer', 'buyer_td' );
		}
	
    </script>

</head>

<body onLoad="set_hotkey();load_buyer();">
<div align="center" style="width:1290px;">
	<form name="searchwofrm"  id="searchwofrm">
		<fieldset style="width:1288px; margin-left:2px">
		<legend>Enter search words</legend>           
            <table cellpadding="0" cellspacing="0" border="1" rules="all" width="1100" class="rpt_table" align="center">
                <thead>
					<tr>
					<tr>
                            <th colspan="11" align="center"><?=create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --",1 ); ?></th>
                        </tr>
					</tr>
					<tr>
						<th width="150">Search By</th>
						<th width="140">Enter WO/PI No</th>
						<th width="150">Buyer</th>
						<th width="100">Job No</th>
                        <th width="100">Style Ref </th>
						<th width="100">PO Number</th>
						<th width="100">AC PO NO</th>
						<th width="160">Date Range</th>
						<th>
							<input type="reset" name="reset" id="reset" value="Reset" style="width:70px" class="formbutton" />
						</th> 
					</tr>
                </thead>
                <tbody>
                	<tr class="general">
	                    <td align="center">	
	                    	<?
								echo create_drop_down("cbo_receive_basis",142,$receive_basis_arr,"",1,"-- Select --",$recieve_basis,"","1","1,2,4,6");
							?>
	                    </td>                 
	                    <td align="center" id="search_by_td">				
	                        <input type="text" style="width:120px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
	                    </td> 
	                    <td id="buyer_td">
						<? 
                        /*echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active=1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$company and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --","", "","0" );*/ 
                        echo create_drop_down( "cbo_buyer_name", 150, $blank_array,"", 1, "-- Select Buyer --", 0, "" ); 
                        ?>
	                    </td>
                        <td><input name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:80px"></td>  
	                    <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:80px"></td>  
	                    <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:80px"></td>
                         <td><input name="txt_ac_po" id="txt_ac_po" class="text_boxes" style="width:80px"></td>
	                    <td align="center">
	                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px" placeholder="From Date" />
	                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px" placeholder="To Date" />
	                     </td>						
	            		<td align="center">
	                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_receive_basis').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_ac_po').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_job_no').value, 'create_wo_pi_search_list_view', 'search_div', 'trims_receive_entry_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:70px;" />
	                     </td>
                	</tr>
                	<tr style="background-color:#CCC;">                  
                    <td align="center" height="25" valign="middle" colspan="9">
                        <? echo load_month_buttons(1);  ?>
                        <!-- Hidden field here -->
                        <input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes" value="<? echo $cbo_company_id; ?>">
                    	<input type="hidden" name="hidden_wo_pi_id" id="hidden_wo_pi_id" class="text_boxes" value="">  
                        <input type="hidden" name="hidden_wo_pi_no" id="hidden_wo_pi_no" class="text_boxes" value=""> 
                        <input type="hidden" name="booking_without_order" id="booking_without_order" class="text_boxes" value="">
                        <input type="hidden" name="hidden_data" id="hidden_data" class="text_boxes" value=""> 
                        <!-- <input type="hidden" id="hidden_tbl_id" value="" />
                        <input type="hidden" id="hidden_wopi_number" value="" />
                        <input type="hidden" id="hidden_is_non_ord_sample" value="" />
                        <input type="hidden" id="hidden_fabric_source" value="" />
                        <input type="hidden" id="hidden_basis" value="" /> -->
                        <!-- END -->
                    </td>
                </tr>
                </tbody>
              
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
	//echo $data; die;//11_1_1_0_a_b_01-09-2019_30-09-2019
	$data = explode("_",$data);
	//print_r($data);die;
	$txt_search_common = trim($data[0]);
	$recieve_basis=$data[1];
	
	
	$company_id =$data[2];
	$buyer = $data[3];
	$style =trim( $data[4]);
	$order = trim($data[5]);
	$txt_date_from = trim($data[6]);
	$txt_date_to = trim($data[7]);
	$txt_ac_po = trim($data[8]);	
	$cbo_year = trim($data[9]);	
	$search_string = $data[10];	
	$job_no = $data[11];
	//echo $search_string;die;
	//echo $txt_date_to."//**".$txt_date_from."**".$order; die;
	//echo $txt_search_common.sdddd.$data[0];die;
	$booking_cond='';
 	$buyer_arr=return_library_array( "select id,buyer_name from lib_buyer where is_deleted=0  and status_active=1 order by buyer_name",'id','buyer_name');
 	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	if($txt_search_common=='' && $buyer==0 && $job_no=='' && $style=='' && $order=='' && $txt_ac_po=='' && $txt_date_from=='' && $txt_date_to=='')
	{
		echo "Please select date range.";die;
	}
	//echo $booking_cond; die;
	$sql_cond="";

 	//echo $buyer."**".$style."**".$order; die;
	if($recieve_basis==1) //Pi Basis
	{
		// $sql_cond .= " and a.pi_number LIKE '%$txt_search_common%'";	
		if ($txt_search_common != '')
		{
			if($search_string==1){ $sql_cond .=" and a.pi_number = '".$txt_search_common."'"; }
			else if ($search_string==2){ $sql_cond .=" and a.pi_number like '".$txt_search_common."%'"; }
			else if ($search_string==3){ $sql_cond .=" and a.pi_number like '%".$txt_search_common."'"; }
			else if ($search_string==4 || $search_string==0){ $sql_cond .=" and a.pi_number like '%".$txt_search_common."%'"; }
		}
		if( $txt_date_from!="" || $txt_date_to!="" ) 
		{
			if($db_type==0)
			{
				$sql_cond .= " and a.pi_date  between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
			}
			if($db_type==2 || $db_type==1)
			{ 
				$sql_cond .= " and a.pi_date  between '".change_date_format($txt_date_from,'','',1)."' and '".change_date_format($txt_date_to,'','',1)."'";
			}
		}
		if(trim($company)!="") $sql_cond .= " and a.importer_id='$company'";
		
		$approval_status_cond="";
		if($db_type==0)
		{ 
			$approval_status="select approval_need, allow_partial from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$company_id' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format(date('d-m-Y'),'yyyy-mm-dd')."' and company_id='$company_id')) and page_id=18 and status_active=1 and is_deleted=0";
		}
		else
		{
			$approval_status="select approval_need, allow_partial from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$company_id' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format(date('d-m-Y'), "", "",1)."' and company_id='$company_id')) and page_id=18 and status_active=1 and is_deleted=0";
		}
		//echo $approval_status;die;
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
		
		//echo $buyer."==".$style."==".$order; die;
		$year_cond = "";
		if($db_type==2)	$year_cond=" and extract(year from a.insert_date)=$cbo_year";
		else $year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$cbo_year";
		
		
	   	$btbLcArr=array();
		$lc_data=sql_select("select a.pi_id, b.id, b.lc_number from com_btb_lc_pi a, com_btb_lc_master_details b where a.status_active=1 and a.is_deleted=0 and a.com_btb_lc_master_details_id=b.id and b.importer_id=$company_id");
		foreach($lc_data as $row)
		{
			$btbLcArr[$row[csf('pi_id')]]=$row[csf('id')]."**".$row[csf('lc_number')];
		}
		$ac_po_id_arr=array();
		if($txt_ac_po !="")
		{
			if($search_string==1){ $search_sql=" and acc_po_no = '".$txt_ac_po."'"; }
			else if ($search_string==2){ $search_sql=" and acc_po_no like '".$txt_ac_po."%'"; }
			else if ($search_string==3){ $search_sql=" and acc_po_no like '%".$txt_ac_po."'"; }
			else if ($search_string==4 || $search_string==0){ $search_sql=" and acc_po_no like '%".$txt_ac_po."%'"; }

			$sql_ac_po = "select po_break_down_id from wo_po_acc_po_info where status_active=1 and is_deleted=0 $search_sql";
			$sql_ac_po_result=sql_select($sql_ac_po);
			foreach($sql_ac_po_result as $val)
			{
				$ac_po_id_arr[$val[csf("po_break_down_id")]]=$val[csf("po_break_down_id")];
			}
		}
		
		if($buyer!=0)
		{
			$sql_condition .= " and e.buyer_name=$buyer";
		}
		if($style!='')
		{
			// $sql_condition .= " and e.style_ref_no LIKE '%$style%'";
			if($search_string==1){ $sql_condition .=" and e.style_ref_no = '".$style."'"; }
			else if ($search_string==2){ $sql_condition .=" and e.style_ref_no like '".$style."%'"; }
			else if ($search_string==3){ $sql_condition .=" and e.style_ref_no like '%".$style."'"; }
			else if ($search_string==4 || $search_string==0){ $sql_condition .=" and e.style_ref_no like '%".$style."%'"; }
		}
		if($order!= '')
		{
			// $sql_condition .= " and d.po_number LIKE '%$order%'";
			if($search_string==1){ $sql_condition .=" and d.po_number = '".$order."'"; }
			else if ($search_string==2){ $sql_condition .=" and d.po_number like '".$order."%'"; }
			else if ($search_string==3){ $sql_condition .=" and d.po_number like '%".$order."'"; }
			else if ($search_string==4 || $search_string==0){ $sql_condition .=" and d.po_number like '%".$order."%'"; }
		}
		if(count($ac_po_id_arr)>0)
		{
			$sql_condition .= " and d.id in(".implode(",",$ac_po_id_arr).")";
		}
		
		
		if($sql_condition!=''){
			$sql_pi = "select A.ID, A.PI_NUMBER, A.SUPPLIER_ID, A.PI_DATE, A.LAST_SHIPMENT_DATE, A.PI_BASIS_ID, A.INTERNAL_FILE_NO, A.CURRENCY_ID, A.SOURCE, E.BUYER_NAME AS BUYER_NAME, E.STYLE_REF_NO AS STYLE_REF_NO, D.ID AS PO_ID, D.PO_NUMBER AS PO_NUMBER 
		from com_pi_master_details a, com_pi_item_details b, wo_booking_dtls c, wo_po_break_down d, wo_po_details_master e
		where a.id=b.pi_id and b.work_order_dtls_id=c.id and c.po_break_down_id=d.id and d.job_no_mst=e.job_no and a.item_category_id=4 and a.status_active=1 and a.is_deleted=0 and a.importer_id=$company_id and a.goods_rcv_status<>1 and a.pi_basis_id=1 $sql_cond $approval_status_cond $year_cond $sql_condition";
		}
		else
		{
			 $sql_pi = "select A.ID, A.PI_NUMBER, A.SUPPLIER_ID, A.PI_DATE, A.LAST_SHIPMENT_DATE, A.PI_BASIS_ID, A.INTERNAL_FILE_NO, A.CURRENCY_ID, A.SOURCE, E.BUYER_NAME AS BUYER_NAME, E.STYLE_REF_NO AS STYLE_REF_NO, D.ID AS PO_ID, D.PO_NUMBER AS PO_NUMBER 
		from com_pi_master_details a, com_pi_item_details b, wo_booking_dtls c, wo_po_break_down d, wo_po_details_master e
		where a.id=b.pi_id and b.work_order_dtls_id=c.id and c.po_break_down_id=d.id and d.job_no_mst=e.job_no and a.item_category_id=4 and a.status_active=1 and a.is_deleted=0 and a.importer_id=$company_id and a.goods_rcv_status<>1 and a.pi_basis_id=1 $sql_cond $approval_status_cond $year_cond $sql_condition
		union all
		select A.ID, A.PI_NUMBER, A.SUPPLIER_ID, A.PI_DATE, A.LAST_SHIPMENT_DATE, A.PI_BASIS_ID, A.INTERNAL_FILE_NO, A.CURRENCY_ID, A.SOURCE, NULL AS BUYER_NAME, NULL AS STYLE_REF_NO, 0 AS PO_ID, NULL AS PO_NUMBER 
		from com_pi_master_details a, com_pi_item_details b 
		where a.id=b.pi_id and a.item_category_id=4 and a.status_active=1 and a.is_deleted=0 and a.importer_id=$company_id and a.goods_rcv_status<>1 and a.pi_basis_id<>1 $sql_cond $approval_status_cond $year_cond";
		}
		
		
		//echo $sql_pi;die;
		$result = sql_select($sql_pi);
		$pi_data=array();
		foreach($result as $val)
		{
			$pi_data[$val["ID"]]["ID"]=$val["ID"];
			$pi_data[$val["ID"]]["PI_NUMBER"]=$val["PI_NUMBER"];
			$pi_data[$val["ID"]]["SUPPLIER_ID"]=$val["SUPPLIER_ID"];
			$pi_data[$val["ID"]]["PI_DATE"]=$val["PI_DATE"];
			$pi_data[$val["ID"]]["LAST_SHIPMENT_DATE"]=$val["LAST_SHIPMENT_DATE"];
			$pi_data[$val["ID"]]["PI_BASIS_ID"]=$val["PI_BASIS_ID"];			
			$pi_data[$val["ID"]]["INTERNAL_FILE_NO"]=$val["INTERNAL_FILE_NO"];
			$pi_data[$val["ID"]]["CURRENCY_ID"]=$val["CURRENCY_ID"];
			$pi_data[$val["ID"]]["SOURCE"]=$val["SOURCE"];
			if($pi_po_data_check[$val["ID"]][$val["PO_ID"]]=="")
			{
				$pi_po_data_check[$val["ID"]][$val["PO_ID"]]=$val["PO_ID"];
				$pi_data[$val["ID"]]["BUYER_NAME"].=$buyer_arr[$val["BUYER_NAME"]].",";
				$pi_data[$val["ID"]]["STYLE_REF_NO"].=$val["STYLE_REF_NO"].",";
				$pi_data[$val["ID"]]["PO_ID"].=$val["PO_ID"].",";
				$pi_data[$val["ID"]]["PO_NUMBER"].=$val["PO_NUMBER"].",";
			}
		}
		

		//for Huge Data
		/*$sql_pi_dtls = "select a.id, b.work_order_id 
		from com_pi_master_details a, com_pi_item_details b 
		where a.id=b.pi_id and b.work_order_id is not NULL and a.item_category_id='4' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.importer_id=$company_id and goods_rcv_status<>1 $pi_cond $sql_cond $approval_status_cond  group by a.id, b.work_order_id, a.pi_number, a.supplier_id, a.pi_date, a.last_shipment_date, a.pi_basis_id, a.internal_file_no, a.currency_id, a.source";
		$result_pi_dtls=sql_select($sql_pi_dtls); $work_order_id_arr=array();
		foreach($result_pi_dtls as $val)
		{
			//$booking_ids.=$val[csf("id")].",";
			$work_order_id_arr[$val[csf('id')]]['work_order_id'].=$val[csf('work_order_id')].",";
		}*/
		//echo "<pre>";
		//print_r($work_order_id_arr);
		
		?>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1272" class="rpt_table">
			<thead>
				<th width="30">SL</th>
				<th width="100">PI No</th>
				<th width="60">PI Date</th>
                <th width="90">PI Basis</th>               
				<th width="150">Supplier</th>
				<th width="60">Last Shipment Date</th>
				<th width="60">Internal File No</th>
				<th width="60">Currency</th>
				<th width="80">Buyer</th>
				<th width="150">Style Ref</th>
				<th width="300">PO Number</th>
				<th>Source</th>
			</thead> 	 	
		</table>
		<div style="width:1290px; max-height:270px; overflow-y:scroll" id="list_container_batch" align="left">	 
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1272" class="rpt_table" id="tbl_list_search">  
			<?
				$i=1;
				foreach ($pi_data as $row)
				{  
					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
					
					$lc_data=explode("**",$btbLcArr[$row["ID"]]);
					$lc_id=$lc_data[0];
					$lc_no=$lc_data[1];
					
						
					$data=$row[csf('supplier_id')]."**".$row[csf('currency_id')]."**".$row[csf('source')]."**".$lc_no."**".$lc_id."**".$row[csf('pi_basis_id')]; 
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $row[csf('id')]; ?>,'<? echo $row[csf('pi_number')]; ?>','0','<? echo $data; ?>');"> 
						<td width="30" align="center"><? echo $i; ?></td>
						<td width="100" align="center"><p><? echo $row["PI_NUMBER"]; ?></p></td>
						<td width="60" align="center"><? echo change_date_format($row["PI_DATE"]); ?></td>  
                        <td width="90"><? echo $pi_basis[$row[csf('pi_basis_id')]]; ?></td>             
						<td width="150"><p><? echo $supplier_arr[$row[csf('supplier_id')]]; ?>&nbsp;</p></td>
						<td width="60" align="center"><? echo change_date_format($row["LAST_SHIPMENT_DATE"]); ?>&nbsp;</td>
						<td width="60"><p><? echo $row["INTERNAL_FILE_NO"]; ?></p></td>
						<td width="60"><p><? echo $currency[$row["CURRENCY_ID"]]; ?></p></td>
						<td width="80"><?
							$buyer_name=implode(",",array_unique(explode(",",chop($row["BUYER_NAME"],","))));
							$style_ref_no=implode(",",array_unique(explode(",",chop($row["STYLE_REF_NO"],","))));
							$po_number=implode(",",array_unique(explode(",",chop($row["PO_NUMBER"],","))));
                		 	echo $buyer_name; 
                		?></td>
						<td width="150"><p><? echo $style_ref_no; ?></p></td>
						<td width="300"><p><? echo $po_number; ?></p></td>
						<td><p><? echo $source[$row["SOURCE"]]; ?></p></td>
					</tr>
				<?
				$i++;
				}
				?>
			</table>
		</div>
		<?	
	}
	else if($recieve_basis==2)
	{
		
		// $sql_cond .= " and  a.booking_no LIKE '%$txt_search_common%'";
		// $sql_condWo .= " and  d.booking_no LIKE '%$txt_search_common%'";	
		if($txt_search_common != '')
		{
			if($search_string==1)
			{ 
				$sql_cond .=" and a.booking_no like '%".$txt_search_common."%'"; 
				$sql_condWo .=" and d.booking_no like '%".$txt_search_common."%'"; 
				$search_field_cond =" and a.booking_no like '%".$txt_search_common."%'"; 
				$search_field_cond_sample =" and s.booking_no like '%".$txt_search_common."%'"; 
				$search_field_cond_2 =" and c.booking_no like '%".$txt_search_common."%'"; 
			}
			else if ($search_string==2)
			{ 
				$sql_cond .=" and a.booking_no like '".$txt_search_common."%'"; 
				$sql_condWo .=" and d.booking_no like '".$txt_search_common."%'"; 
				$search_field_cond =" and a.booking_no like '".$txt_search_common."%'"; 
				$search_field_cond_sample =" and s.booking_no like '".$txt_search_common."%'"; 
				$search_field_cond_2 =" and c.booking_no like '".$txt_search_common."%'"; 
			}
			else if ($search_string==3)
			{ 
				$sql_cond .=" and a.booking_no like '%".$txt_search_common."'"; 
				$sql_condWo .=" and d.booking_no like '%".$txt_search_common."'"; 
				$search_field_cond =" and a.booking_no like '%".$txt_search_common."'"; 
				$search_field_cond_sample =" and s.booking_no like '%".$txt_search_common."'"; 
				$search_field_cond_2 =" and c.booking_no like '%".$txt_search_common."'"; 
			}
			else if ($search_string==4 || $search_string==0)
			{ 
				$sql_cond .=" and a.booking_no like '%".$txt_search_common."%'"; 
				$sql_condWo .=" and d.booking_no like '%".$txt_search_common."%'"; 
				$search_field_cond =" and a.booking_no like '%".$txt_search_common."%'"; 
				$search_field_cond_sample =" and s.booking_no like '%".$txt_search_common."%'"; 
				$search_field_cond_2 =" and c.booking_no like '%".$txt_search_common."%'"; 
			}
		}	
		else
		{
			$search_field_cond = $search_field_cond_sample = $search_field_cond_2 = "";
		}
		if( $txt_date_from!="" || $txt_date_to!="" ) 
		{
			if($db_type==0)
			{
				$sql_cond .= " and a.booking_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
				//$sql_condWo .= " and d.booking_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
				$booking_condA .= " and a.booking_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
				$bookingcondS .= " and  s.booking_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
			}
			
			if($db_type==2 || $db_type==1)
			{ 
				$sql_cond .= " and a.booking_date between '".change_date_format($txt_date_from,'','',1)."' and '".change_date_format($txt_date_to,'','',1)."'";
				//$sql_condWo .= " and d.booking_date between '".change_date_format($txt_date_from,'','',1)."' and '".change_date_format($txt_date_to,'','',1)."'";
				$booking_condA .= " and a.booking_date between '".change_date_format($txt_date_from,'','',1)."' and '".change_date_format($txt_date_to,'','',1)."'";
				$bookingcondS .= " and s.booking_date between '".change_date_format($txt_date_from,'','',1)."' and '".change_date_format($txt_date_to,'','',1)."'";
			}
		}
		if(trim($company)!="") $sql_cond .= " and  company_id='$company'";
		
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
		foreach($approval_status as $row)
		{
			if($row[csf("page_id")]==9 && $row[csf("approval_need")]==1)
			{
				if($row[csf("allow_partial")]==1)
				{
					$approval_status_cond_main=" and a.is_approved in(1,3)";
				}
				else
				{
					$approval_status_cond_main=" and a.is_approved = 1";
				}
				
			}
			if($row[csf("page_id")]==10 && $row[csf("approval_need")]==1)
			{
				if($row[csf("allow_partial")]==1)
				{
					$approval_status_cond_short=" and a.is_approved in(1,3)";
				}
				else
				{
					$approval_status_cond_short=" and a.is_approved = 1";
				}
				
			}

			if($row[csf("page_id")]==50 && $row[csf("approval_need")]==1)
			{
				if($row[csf("allow_partial")]==1)
				{
					$approval_status_cond_short=" and a.is_approved in(1,3)";
				}
				else
				{
					$approval_status_cond_short=" and a.is_approved = 1";
				}
				
			}
		}
		//echo $approval_status_cond_short;die;
		
		if($buyer!=0)
 		{
 			$sql_condition .= " and d.buyer_name=$buyer";
 			$sql_condition_non .= " and s.buyer_id=$buyer";
 		}
		
		if($job_no!='')
 		{
			 if($search_string==1)
			 { 
				 $sql_condition .=" and d.job_no = '".$job_no."'"; 
			 }
			 else if ($search_string==2)
			 { 
				 $sql_condition .=" and d.job_no like '".$job_no."%'"; 
			 }
			 else if ($search_string==3)
			 { 
				 $sql_condition .=" and d.job_no like '%".$job_no."'"; 
			 }
			 else if ($search_string==4 || $search_string==0)
			 { 
				 $sql_condition .=" and d.job_no like '%".$job_no."%'"; 
			 }
 		}
		
 		if($style!='')
 		{
			 if($search_string==1)
			 { 
				 $sql_condition .=" and d.style_ref_no = '".$style."'"; 
				 $sql_conditionition_non .=" and t.style_des = '".$style."'"; 
			 }
			 else if ($search_string==2)
			 { 
				 $sql_condition .=" and d.style_ref_no like '".$style."%'"; 
				 $sql_conditionition_non .=" and t.style_des like '".$style."%'"; 
			 }
			 else if ($search_string==3)
			 { 
				 $sql_condition .=" and d.style_ref_no like '%".$style."'"; 
				 $sql_conditionition_non .=" and t.style_des like '%".$style."'"; 
			 }
			 else if ($search_string==4 || $search_string==0)
			 { 
				 $sql_condition .=" and d.style_ref_no like '%".$style."%'"; 
				 $sql_condition_non .=" and t.style_des like '%".$style."%'"; 
			 }
 		}
		
 		if($order!= '')
 		{
 			// $sql_condition .= " and c.po_number LIKE '%$order%'";
			 if($search_string==1){ $sql_condition .=" and c.po_number = '".$order."'"; }
			 else if ($search_string==2){ $sql_condition .=" and c.po_number like '".$order."%'"; }
			 else if ($search_string==3){ $sql_condition .=" and c.po_number like '%".$order."'"; }
			 else if ($search_string==4 || $search_string==0){ $sql_condition .=" and c.po_number like '%".$order."%'"; }
 		}
		
		$ac_po_id_arr=array();
		if($txt_ac_po !="")
		{
			if($search_string==1){ $search_sql=" and acc_po_no = '".$txt_ac_po."'"; }
			else if ($search_string==2){ $search_sql=" and acc_po_no like '".$txt_ac_po."%'"; }
			else if ($search_string==3){ $search_sql=" and acc_po_no like '%".$txt_ac_po."'"; }
			else if ($search_string==4 || $search_string==0){ $search_sql=" and acc_po_no like '%".$txt_ac_po."%'"; }

			$sql_ac_po = "select po_break_down_id from wo_po_acc_po_info where status_active=1 and is_deleted=0 $search_sql";
			$sql_ac_po_result=sql_select($sql_ac_po);
			foreach($sql_ac_po_result as $val)
			{
				$ac_po_id_arr[$val[csf("po_break_down_id")]]=$val[csf("po_break_down_id")];
			}
		}
		
		if(count($ac_po_id_arr)>0)
		{
			$sql_condition .= " and c.id in(".implode(",",$ac_po_id_arr).")";
		}

		if($db_type==2) { 
			$year_cond=" and extract(year from a.insert_date)=$cbo_year"; $year=" extract(year from a.insert_date)";
			$year_cond2=" and extract(year from s.insert_date)=$cbo_year"; $year=" extract(year from s.insert_date)";
			$year_cond3=" and extract(year from d.insert_date)=$cbo_year";
		}
    	if($db_type==0) {
    		$year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$cbo_year"; $year=" SUBSTRING_INDEX(a.insert_date, '-', 1) ";
    		$year_cond2=" and SUBSTRING_INDEX(s.insert_date, '-', 1)=$cbo_year"; $year=" SUBSTRING_INDEX(s.insert_date, '-', 1) ";
    		$year_cond3=" and SUBSTRING_INDEX(d.insert_date, '-', 1)=$cbo_year";
    	}
		
		
		
		if($db_type==0)
		{
			//group_concat(distinct(b.po_break_down_id)) as po_id
			$sql = "select a.id, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.delivery_date, a.currency_id, a.source, a.supplier_id, a.booking_type, a.is_short,YEAR(a.insert_date) as year, a.pay_mode, a.fabric_source, group_concat(d.job_no) as job_no, group_concat(c.id) as po_id, group_concat(c.po_number) as po_number, group_concat(c.pub_shipment_date) as pub_shipment_date, sum(c.po_quantity) as po_quantity, sum(c.po_quantity*d.total_set_qnty) as po_quantity_pcs, group_concat(d.buyer_name) as buyer_name, group_concat(d.style_ref_no) as style_ref_no, 0 as type 
			from wo_booking_mst a, wo_booking_dtls b, wo_po_break_down c, wo_po_details_master d  
			where a.booking_no=b.booking_no and b.po_break_down_id=c.id and c.job_no_mst=d.job_no and a.company_id=$company_id and a.item_category=4 and a.booking_type in (2,5,8) and a.is_short=2 and a.pay_mode<>2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $search_field_cond $booking_cond $approval_status_cond_main  $booking_condA $year_cond
			group by a.id
			union all
			select a.id, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.delivery_date, a.currency_id, a.source, a.supplier_id, a.booking_type, a.is_short,YEAR(a.insert_date) as year, a.pay_mode, a.fabric_source, group_concat(d.job_no) as job_no, group_concat(c.id) as po_id, group_concat(c.po_number) as po_number, group_concat(c.pub_shipment_date) as pub_shipment_date, sum(c.po_quantity) as po_quantity, sum(c.po_quantity*d.total_set_qnty) as po_quantity_pcs, group_concat(d.buyer_name) as buyer_name, group_concat(d.style_ref_no) as style_ref_no, 0 as type 
			from wo_booking_mst a, wo_booking_dtls b, wo_po_break_down c, wo_po_details_master d  
			where a.booking_no=b.booking_no and b.po_break_down_id=c.id and c.job_no_mst=d.job_no and a.company_id=$company_id and a.item_category=4 and a.booking_type in (2,5,8) and a.is_short=1 and a.pay_mode<>2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $search_field_cond $booking_cond $approval_status_cond_short $booking_condA $year_cond group by a.id
			union all
			select s.id, s.booking_no_prefix_num, s.booking_no, s.booking_date, s.delivery_date, s.currency_id, s.source, s.supplier_id, 0 as booking_type,0 as is_short,  YEAR(s.insert_date) as year,s.pay_mode, 0 as fabric_source, '' as job_no, 0 as po_id, '' as po_number, '' as pub_shipment_date, 0 as po_quantity, 0 as po_quantity_pcs, '' as buyer_name, '' as style_ref_no, 1 as type 
			FROM wo_non_ord_samp_booking_mst s, wo_non_ord_samp_booking_dtls t 
			WHERE s.booking_no=t.booking_no and s.company_id=$company_id and s.pay_mode<>2 and s.status_active =1 and s.is_deleted=0 and t.status_active =1 and t.is_deleted=0 and s.item_category=4 $search_field_cond_sample $sql_condition_non $approval_status_cond_sample $bookingcondS $year_cond2
			group by s.id"; //, group_concat(distinct(b.job_no)) as job_no, null as job_no
		}
		else
		{
			$sql = "SELECT a.id, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.delivery_date, a.currency_id, a.source, a.supplier_id, a.booking_type, a.is_short, to_char(a.insert_date,'YYYY') as year, a.pay_mode, a.fabric_source,  MIN(c.pub_shipment_date) as pub_shipment_date, sum(c.po_quantity) as po_quantity, sum(c.po_quantity*d.total_set_qnty) as po_quantity_pcs, rtrim(xmlagg(xmlelement(e,d.buyer_name,', ').extract('//text()') order by d.buyer_name).getclobval(),', ') as buyer_name, rtrim(xmlagg(xmlelement(e,d.job_no,', ').extract('//text()') order by d.job_no).getclobval(),', ') as job_no, rtrim(xmlagg(xmlelement(e,c.id,', ').extract('//text()') order by c.id).getclobval(),', ') as po_id, rtrim(xmlagg(xmlelement(e,c.po_number,', ').extract('//text()') order by c.po_number).getclobval(),', ') as po_number, rtrim(xmlagg(xmlelement(e,d.style_ref_no,', ').extract('//text()') order by d.style_ref_no).getclobval(),', ') as style_ref_no, 0 as type 
			from wo_booking_mst a, wo_booking_dtls b, wo_po_break_down c, wo_po_details_master d 
			where a.booking_no=b.booking_no and b.po_break_down_id=c.id and c.job_no_mst=d.job_no and a.company_id=$company_id  $booking_cond $year_cond and a.item_category=4 and a.booking_type in (2,5) and a.is_short in (1,2) and a.pay_mode<>2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $search_field_cond $approval_status_cond_main $booking_condA $sql_condition $year_cond			
			group by a.id, a.booking_no, a.booking_date, a.delivery_date, a.currency_id, a.source, a.supplier_id, a.booking_no_prefix_num, a.insert_date, a.booking_type, a.is_short, a.pay_mode, a.fabric_source
			union all
			select a.id, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.delivery_date, a.currency_id, a.source, a.supplier_id, a.booking_type, a.is_short, to_char(a.insert_date,'YYYY') as year, a.pay_mode, a.fabric_source, MIN(c.pub_shipment_date) as pub_shipment_date, sum(c.po_quantity) as po_quantity, sum(c.po_quantity*d.total_set_qnty) as po_quantity_pcs, rtrim(xmlagg(xmlelement(e,d.buyer_name,', ').extract('//text()') order by d.buyer_name).getclobval(),', ') as buyer_name, rtrim(xmlagg(xmlelement(e,d.job_no,', ').extract('//text()') order by d.job_no).getclobval(),', ') as job_no, rtrim(xmlagg(xmlelement(e,c.id,', ').extract('//text()') order by c.id).getclobval(),', ') as po_id, rtrim(xmlagg(xmlelement(e,c.po_number,', ').extract('//text()') order by c.po_number).getclobval(),', ') as po_number, rtrim(xmlagg(xmlelement(e,d.style_ref_no,', ').extract('//text()') order by d.style_ref_no).getclobval(),', ') as style_ref_no, 0 as type 
			from wo_booking_mst a, wo_booking_dtls b, wo_po_break_down c, wo_po_details_master d 
			where a.booking_no=b.booking_no and b.po_break_down_id=c.id and c.job_no_mst=d.job_no and a.company_id=$company_id  $booking_cond  and a.item_category=4 and a.booking_type in (8)  and a.pay_mode<>2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $search_field_cond $approval_status_cond_short $booking_condA $sql_condition $year_cond
			group by a.id, a.booking_no, a.booking_date, a.delivery_date, a.currency_id, a.source, a.supplier_id, a.booking_no_prefix_num, a.insert_date, a.booking_type, a.is_short, a.pay_mode, a.fabric_source
			union all
			select s.id, s.booking_no_prefix_num, s.booking_no, s.booking_date, s.delivery_date, s.currency_id, s.source, s.supplier_id, 0 as booking_type, 0 as is_short, to_char(s.insert_date,'YYYY') as year, s.pay_mode, 0 as fabric_source, null as pub_shipment_date, 0 as po_quantity, 0 as po_quantity_pcs,  null as buyer_name, null as job_no, null as po_id, null as po_number, null as style_ref_no, 1 as type 
			FROM wo_non_ord_samp_booking_mst s, wo_non_ord_samp_booking_dtls t 
			WHERE s.booking_no=t.booking_no and s.company_id=$company_id and s.pay_mode<>2 and s.status_active =1 and s.is_deleted=0 and t.status_active =1 and t.is_deleted=0 and s.item_category=4 $search_field_cond_sample $sql_condition_non $approval_status_cond_sample $bookingcondS $year_cond2
			group by s.id, s.booking_no_prefix_num, s.booking_no, s.booking_date, s.delivery_date, s.currency_id, s.source, s.supplier_id, s.insert_date, s.pay_mode 
			order by type asc, booking_no desc";
		}
		
		//echo $sql;//die;
		$result = sql_select($sql);
		
		?>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1272" class="rpt_table" align="center">
			<thead>
				<th width="30">SL</th>
				<th width="60">Booking No</th>
                <th width="45">Year</th>
                <th width="70">Type</th>
				<th width="75">Booking Date</th>               
				<th width="100">Supplier</th>
				<th width="75">Delivary date</th>
                <th width="65">Source</th>
                <th width="65">Currency</th>
				<th width="90">Job No</th>
				<th width="80">Order Qnty</th>
				<th width="75">Shipment Date</th>
				<th width="80">Buyer</th>
                <th width="160">Style Ref</th>
				<th>Order No</th>
			</thead>
		</table>
		<div style="width:1290px; max-height:270px; overflow-y:scroll" id="list_container_batch" align="center">	 
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1272" class="rpt_table" id="tbl_list_search" align="left">
			<?
				$i=1;
				foreach ($result as $row)
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
					
					if($row[csf('pay_mode')]==3 || $row[csf('pay_mode')]==5)
					{
						$supplier_names=$company_library[$row[csf('supplier_id')]];
					}
					else
					{
						$supplier_names=$supplier_arr[$row[csf('supplier_id')]];
					}
										
					$po_qnty_in_pcs=''; $po_no=''; $min_shipment_date=''; $job_no='';
					
					
					if($row[csf('pub_shipment_date')]!="")
					{
						$min_shipment_date=change_date_format($row[csf('pub_shipment_date')]);
						$po_qnty_in_pcs=$row[csf('po_quantity_pcs')];
					}
					else
					{
						$po_qnty_in_pcs='&nbsp;'; $po_no='&nbsp;'; $min_shipment_date='&nbsp;';
					}
					
					
					$data=$row[csf('supplier_id')]."**".$row[csf('currency_id')]."**".$row[csf('source')]."********".$row[csf('pay_mode')]."**".$row[csf('fabric_source')]; 
					
					$job_nos=$po_numbers=$style_ref_no=$buyer_name='';
					if ($row[csf('type')]==0)
					{
						$job_nos=implode(",",array_unique(explode(",",$row[csf('job_no')]->load())));
						$all_buyer_arr=array_unique(explode(",",$row[csf('buyer_name')]->load()));
						foreach($all_buyer_arr as $val)
						{
							if($buyer_name=="") $buyer_name=$buyer_arr[$val]; else $buyer_name.=','.$buyer_arr[$val];
						}
						$style_ref_no=implode(",",array_unique(explode(",",$row[csf('style_ref_no')]->load())));
						$po_numbers=implode(",",array_unique(explode(",",$row[csf('po_number')]->load())));
					}					
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $row[csf('id')]; ?>,'<? echo $row[csf('booking_no')]; ?>','<? echo $row[csf('type')]; ?>','<? echo $data; ?>');"> 
						<td width="30" align="center"><? echo $i; ?></td>
						<td width="60" align="center"><p>&nbsp;&nbsp;<? echo $row[csf('booking_no_prefix_num')]; ?></p></td>
                        <td width="45" align="center"><? echo $row[csf('year')]; ?></td>
                        <td width="70" align="center"><p><? echo $booking_type; ?></p></td>
						<td width="75" align="center"><? echo change_date_format($row[csf('booking_date')]); ?>&nbsp;</td>               
						<td width="100"><p><? echo $supplier_names; ?>&nbsp;</p></td>
						<td width="75" align="center"><? echo change_date_format($row[csf('delivery_date')]); ?>&nbsp;</td>
                        <td width="65"><p><? echo $source[$row[csf('source')]]; ?></p></td>
                        <td width="65"><p><? echo $currency[$row[csf('currency_id')]]; ?></p></td>
						<td width="90"><p><? echo $job_nos; ?>&nbsp;</p></td>
						<td width="80" align="right"><? echo $po_qnty_in_pcs; ?></td>
						<td width="75" align="center"><? echo $min_shipment_date; ?></td>
						<td width="80"><? echo rtrim($buyer_name,','); ?></td>
						<td width="160"><p><? echo $style_ref_no; ?></p></td>
						<td><p><? echo $po_numbers; ?></p></td>
					</tr>
					<?
                    $i++;
					
				}
				
				?>
			</table>
		</div>
		<?
		
	}
	exit();
}

if($action=='show_fabric_desc_listview')
{
	$data=explode("__",$data);
	$bookingNo_piId=$data[0];
	$receive_basis=$data[1];
	$booking_without_order=$data[2];
	$variable_string_inventory=$data[3];
	$variable_string_inventory_ref=explode("**",$variable_string_inventory);
	$rate_hide_inventory=$variable_string_inventory_ref[2];
	$book_pi_id=$data[4];


	if($receive_basis==1)
	{
		$table_width=780;  $sensitivity_column=''; $qty_column=''; $size_width="40"; $brandSup_width="60"; $item_width="40"; 
		$po_column="<th width='60'>PO No.</th><th width='40'>Job No.</th><th width='60'>I.R.</th><th width='60'>Style Ref.</th>";
		$qty_column="<th width='50'>Qty.</th>";
		$sql="select item_group as trim_group, work_order_dtls_id as po_break_down_id, item_description as description, brand_supplier, color_id, item_color, size_id, item_size, '' as sensitivity, quantity as qty, rate from com_pi_item_details where pi_id='$bookingNo_piId' and status_active=1 and is_deleted=0 order by item_group";
		
		//echo $sql;die;
		
		// group by item_group, item_description, brand_supplier, color_id, item_color, size_id, item_size
		 
		//$po_arr = return_library_array("select id, po_number from wo_po_break_down","id","po_number");
		
		//echo "select c.id as po_break_down_id,a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b,wo_booking_dtls c,wo_booking_mst d,com_pi_item_details e where a.job_no=b.job_no_mst and c.po_break_down_id=b.id and c.booking_no=d.booking_no and e.pi_id='$bookingNo_piId' and e.work_order_dtls_id=c.id and e.work_order_no=c.booking_no and d.booking_type=2 and d.item_category=4 ";
		
		$po_arr=array(); $ir_arr=array();  $style_arr=array(); $booking_po_id=array();
		
		 $poDataArr = sql_select("select c.id as po_break_down_id, a.job_no_prefix_num as job_no, a.style_ref_no, b.id, b.po_number, b.grouping as int_ref_no
		 from wo_po_details_master a, wo_po_break_down b, wo_booking_dtls c, com_pi_item_details e 
         where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.id=e.work_order_dtls_id and e.work_order_no=c.booking_no and c.booking_type in (2,5) and e.pi_id='$bookingNo_piId' ");
		foreach($poDataArr as $wRow)
		{
			$po_arr[$wRow[csf('po_break_down_id')]]=$wRow[csf('po_number')];
			$ir_arr[$wRow[csf('po_break_down_id')]]=$wRow[csf('int_ref_no')];
			$job_arr[$wRow[csf('po_break_down_id')]]=$wRow[csf('job_no')];
			$booking_po_id[$wRow[csf('po_break_down_id')]]=$wRow[csf('id')];
			$style_arr[$wRow[csf('po_break_down_id')]]=$wRow[csf('style_ref_no')];
		}
		
		$prev_rcv_sql="select b.po_breakdown_id, a.item_group_id, a.item_description, a.brand_supplier, a.gmts_color_id, a.item_color, a.gmts_size_id, a.item_size, b.quantity as qnty ,b.ORDER_RATE as rate
		from inv_receive_master m, inv_trims_entry_dtls a, order_wise_pro_details b 
		where m.id=a.mst_id and a.id=b.dtls_id and m.receive_basis=1 and b.entry_form=24 and b.trans_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_id=$bookingNo_piId";
		
	}
	else if($receive_basis==2)
	{
		if($booking_without_order==1)
		{
			$table_width=630; $po_column=''; $sensitivity_column=''; $qty_column="<th width='50'>Qty.</th>"; $size_width="40"; $brandSup_width="60"; $item_width="40";
			 $sql = "select 0 as sensitivity, trim_group, fabric_description as description, gmts_color as color_id, fabric_color as item_color, gmts_size as size_id, item_size, barnd_sup_ref as brand_supplier, trim_qty as qty, rate from wo_non_ord_samp_booking_dtls where booking_no='$bookingNo_piId' and status_active=1 and is_deleted=0 order by  trim_group";
			 $prev_rcv_sql="select 0 as po_breakdown_id, a.item_group_id, a.item_description, a.brand_supplier, a.gmts_color_id, a.item_color, a.gmts_size_id, a.item_size, a.receive_qnty as qnty, a.rate as rate
			from inv_receive_master m, inv_trims_entry_dtls a
			where m.id=a.mst_id and m.receive_basis=2 and m.entry_form=24 and a.booking_without_order=1 and a.status_active=1 and a.is_deleted=0 and m.status_active=1 and a.booking_no='$bookingNo_piId'";
		}
		else
		{
			$po_arr=array(); $style_arr=array(); 
			//$po_arr = return_library_array("select id, po_number from wo_po_break_down","id","po_number");
			$poDataArr = sql_select("select a.style_ref_no, a.job_no_prefix_num as job_no, b.id, b.po_number, b.grouping as int_ref_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst");
			foreach($poDataArr as $wRow)
			{
				$po_arr[$wRow[csf('id')]]=$wRow[csf('po_number')];
				$job_arr[$wRow[csf('id')]]=$wRow[csf('job_no')];
				$style_arr[$wRow[csf('id')]]=$wRow[csf('style_ref_no')];
				$ir_arr[$wRow[csf('id')]]=$wRow[csf('int_ref_no')];
			}
			
			$table_width=840; $size_width="40"; $brandSup_width="50"; $item_width="40"; 
			$po_column="<th width='60'>PO No.</th><th width='40'>Job No.</th><th width='60'>I.R.</th><th width='60'>Style Ref.</th>"; $sensitivity_column="<th width='60'>Sensitivity</th>"; $qty_column="<th width='50'>Qty.</th>";
			/*$sql = "SELECT b.po_break_down_id, b.sensitivity, b.trim_group, c.description, c.color_number_id as color_id, c.item_color, c.gmts_sizes as size_id, c.item_size, c.brand_supplier, c.cons as qty, c.rate from wo_booking_dtls b, wo_trim_book_con_dtls c where b.id=c.wo_trim_booking_dtls_id and b.booking_no='$bookingNo_piId'  and b.booking_no=c.booking_no  and c.cons>0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by  b.trim_group";*/
			$sql = "SELECT b.po_break_down_id, b.sensitivity, b.trim_group, c.description, c.color_number_id as color_id, c.item_color as item_color, c.gmts_sizes as size_id, c.item_size, c.brand_supplier, sum(c.cons) as qty, c.rate from wo_booking_dtls b, wo_trim_book_con_dtls c where b.id=c.wo_trim_booking_dtls_id and b.booking_no='$bookingNo_piId'  and b.booking_no=c.booking_no  and c.cons>0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
			group by b.po_break_down_id, b.sensitivity, b.trim_group, c.description, c.color_number_id, c.item_color, c.gmts_sizes, c.item_size, c.brand_supplier, c.rate
			order by  b.trim_group";
			//ROUND(a.rate,6)
			$prev_rcv_sql="select b.po_breakdown_id, a.item_group_id, a.item_description, a.brand_supplier, a.gmts_color_id, a.item_color, a.gmts_size_id, a.item_size, b.quantity as qnty, a.rate as rate
			from inv_receive_master m, inv_trims_entry_dtls a, order_wise_pro_details b 
			where m.id=a.mst_id and a.id=b.dtls_id and m.receive_basis=2 and b.entry_form=24 and b.trans_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and m.status_active=1 and a.booking_no='$bookingNo_piId'";
		}

	}
	//echo $prev_rcv_sql."<br>";
	$prev_rcv_sql_result=sql_select($prev_rcv_sql);
	$prev_rcv_data=array();
	foreach($prev_rcv_sql_result as $row)
	{
		$prev_rcv_data[$row[csf("po_breakdown_id")]][$row[csf("item_group_id")]][strtoupper(trim($row[csf("item_description")]))][trim($row[csf("brand_supplier")])][$row[csf("gmts_color_id")]][$row[csf("item_color")]][$row[csf("gmts_size_id")]][trim($row[csf("item_size")])][number_format($row[csf("rate")],6,".","")]["qnty"]+=$row[csf("qnty")];
	}
	//echo "<pre>";print_r($prev_rcv_data);
	//echo $sql; die;
	
	if($receive_basis==1 || $receive_basis==2)
	{ 
		$rcvRtn_qty_sql = "SELECT b.po_breakdown_id, a.item_group_id, a.item_description, a.brand_supplier, a.color as gmts_color_id, a.gmts_size as gmts_size_id, a.item_color, a.item_size, b.quantity as recv_return_qty from product_details_master a, inv_transaction t, order_wise_pro_details b, wo_po_break_down c, wo_po_details_master d
		where a.id=b.prod_id and a.id=t.prod_id and t.id=b.trans_id and b.po_breakdown_id=c.id and c.job_no_mst=d.job_no and a.id=b.prod_id and a.item_category_id=4 and b.entry_form in(49) and t.transaction_type=3 and t.pi_wo_batch_no in($book_pi_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and t.status_active=1 and t.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
	}
	//echo $rcvRtn_qty_sql;
	$totalRcvRtnQty_arr=array();
	$rcvRtn_qtyArray=sql_select($rcvRtn_qty_sql); 
	foreach($rcvRtn_qtyArray as $row)
	{ 
		$totalRcvRtnQty_arr[$row[csf("po_breakdown_id")]][$row[csf("item_group_id")]][strtoupper(trim($row[csf("item_description")],', [BS]'))][trim($row[csf("brand_supplier")])][$row[csf("gmts_color_id")]][$row[csf("item_color")]][$row[csf("gmts_size_id")]][trim($row[csf("item_size")])]+=$row[csf('recv_return_qty')];
	}

	$trim_group_arr =array(); 
	$data_array=sql_select("select id, item_name, order_uom from lib_item_group");
	foreach($data_array as $row)
	{
		$trim_group_arr[$row[csf('id')]]['name']=$row[csf('item_name')];
		$trim_group_arr[$row[csf('id')]]['uom']=$row[csf('order_uom')];
	}

	$data_array=sql_select($sql);
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0","id","color_name");
	$size_arr = return_library_array("select id, size_name from lib_size","id","size_name");
	?>
    <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="<? echo $table_width; ?>">
        <thead>
            <th width="25">SL</th>
            <? echo $po_column; ?>
            <th width="60">Item Group</th>
            <th>Item Description</th>
            <th width="<? echo $brandSup_width; ?>">Brand/ Sup Ref</th>
            <th width="60">Gmts Color</th>
            <th width="<? echo $size_width; ?>">Gmts Size</th>
            <th width="60">Item Color</th>
            <th width="<? echo $item_width; ?>">Item Size</th>
            <? echo $qty_column; ?>
            <th width="40">Balance</th>
            <?
			if($rate_hide_inventory!=1)
			{
				?>
                <th width="40">rate</th>
                <?
			}
			?>
            
            <? echo $sensitivity_column; ?>
            
        </thead>
	</table>
    <div style="width:<? echo $table_width+20; ?>px; overflow-y: scroll; max-height:400px;" id="scroll_body">
    	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="<? echo $table_width; ?>" id="tbl_list_search">
            <? 
            $i=1;
            foreach($data_array as $row)
            {  
                if ($i%2==0)  
                    $bgcolor="#E9F3FF";
                else
                    $bgcolor="#FFFFFF";
				
				if( $receive_basis==1 && $booking_without_order!=1)
				{
					$order_id=$booking_po_id[$row[csf('po_break_down_id')]];
				}
				else if( $receive_basis==2 && $booking_without_order!=1)
				{
					$order_id=$row[csf('po_break_down_id')];
				}
				else
				{
					$order_id=0;
				}
				
				$data=$row[csf('trim_group')]."**".trim($row[csf('description')])."**".$row[csf('brand_supplier')]."**".$row[csf('sensitivity')]."**".$row[csf('color_id')]."**".$color_arr[$row[csf('color_id')]]."**".$row[csf('size_id')]."**".$size_arr[$row[csf('size_id')]]."**".$row[csf('item_color')]."**".$color_arr[$row[csf('item_color')]]."**".$row[csf('item_size')]."**".$order_id."**".$row[csf('qty')]."**".$row[csf('rate')]."**".$job_arr[$row[csf('po_break_down_id')]]."**".$ir_arr[$row[csf('po_break_down_id')]];
				
				$desc=$row[csf('description')];
				$rate=number_format($row[csf('rate')],6,".","");
				//$rate=$row[csf('rate')];
				//$rate=ltrim($rate, '0');
				//echo $order_id."**".$row[csf('trim_group')]."**".strtoupper($row[csf('description')])."**".$row[csf('brand_supplier')]."**".$row[csf('color_id')]."**".$row[csf('item_color')]."**".$row[csf('size_id')]."**".$row[csf('item_size')]."**".$rate."#";
				if($row[csf('color_id')]=="") $row[csf('color_id')]=0;
				if($row[csf('item_color')]=="") $row[csf('item_color')]=0;
				if($row[csf('size_id')]=="") $row[csf('size_id')]=0;
				$prev_rec_qnty=$prev_rcv_data[$order_id][$row[csf('trim_group')]][strtoupper(trim($row[csf('description')]))][trim($row[csf('brand_supplier')])][$row[csf('color_id')]][$row[csf('item_color')]][$row[csf('size_id')]][trim($row[csf('item_size')])][$rate]["qnty"];
				$prev_rtn_qnty=$totalRcvRtnQty_arr[$order_id][$row[csf('trim_group')]][strtoupper(trim($row[csf('description')]))][trim($row[csf('brand_supplier')])][$row[csf('color_id')]][$row[csf('item_color')]][$row[csf('size_id')]][trim($row[csf('item_size')])];
				$balance_qnty=(($row[csf('qty')]+$prev_rtn_qnty)-$prev_rec_qnty);
				
				//if($row[csf('item_color')]!="" && $row[csf('item_color')]!=0) {$desc.=" ".$color_arr[$row[csf('item_color')]];}
				//if($row[csf('item_size')]!="") $desc.=", ".$row[csf('item_size')];
             	?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick='set_form_data("<? echo $data; ?>");change_color("tr_<? echo $i; ?>","<? echo $bgcolor;?>");' id="tr_<? echo $i; ?>" style="cursor:pointer" >
                    <td width="25" title="<?=$row[csf('qty')].'==>'.$prev_rtn_qnty.'==>'.$prev_rec_qnty;?>"><? echo $i; ?></td>
                    <?
					if(($receive_basis==2 || $receive_basis==1) && $booking_without_order!=1)
					{
						?>
                    	<td width="60" style="word-break:break-all"><p><? echo $po_arr[$row[csf('po_break_down_id')]]; ?></p></td>
                        <td width="40" style="word-break:break-all"><p><? echo $job_arr[$row[csf('po_break_down_id')]]; ?></p></td>
                        <td width="60" style="word-break:break-all"><p><? echo $ir_arr[$row[csf('po_break_down_id')]]; ?></p></td>
                        <td width="60" style="word-break:break-all"><p><? echo $style_arr[$row[csf('po_break_down_id')]]; ?></p></td> 
                    	<?
					}
					?>
                    <td width="60" style="word-break:break-all"><p><? echo $trim_group_arr[$row[csf('trim_group')]]['name']; ?></p></td>
                    <td style="word-break:break-all"><p><? echo $desc; ?></p></td>
                    <td width="<? echo $brandSup_width; ?>" style="word-break:break-all"><p><? echo $row[csf('brand_supplier')]; ?></p></td>
                    <td width="60" style="word-break:break-all"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
                    <td width="<? echo $size_width; ?>" style="word-break:break-all"><p><? echo $size_arr[$row[csf('size_id')]]; ?></p></td>
                    <td width="60" style="word-break:break-all"><p><? echo $color_arr[$row[csf('item_color')]]; ?></p></td>
                    <td width="<? echo $item_width; ?>" style="word-break:break-all"><p><? echo $row[csf('item_size')]; ?></p></td>
                    <td width="50" align="right"><? echo number_format($row[csf('qty')],2); ?></td>
                    <td width="40" align="right"><? echo number_format($balance_qnty,2); ?></td>
                    <?
					if($rate_hide_inventory!=1)
					{
						?>
		                <td width="40" align="right"><? echo number_format($row[csf('rate')],4); ?></td>
		                <?
					}
					?>
                    <? if($receive_basis==2 && $booking_without_order!=1) echo "<td width='60' style='word-break:break-all'><p>".$size_color_sensitive[$row[csf('sensitivity')]]."</p></td>";  ?>
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

if ($action=="po_popup")
{
	echo load_html_head_contents("PO Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);

	$po_id=$data[0]; //$type=$receive_basis;
	$booking_no=trim($booking_no);
	$cbo_company_id=str_replace("'","",$cbo_company_id);
	$booking_no=str_replace("'","",$booking_no);
	$booking_pi_id=str_replace("'","",$booking_pi_id);
	$wo_pi_basis_id=str_replace("'","",$wo_pi_basis_id);
	$item_group=str_replace("'","",$item_group);
	$item_description=str_replace("'","",$item_description);
	$brand_supref=str_replace("'","",$brand_supref);
	$order_uom=str_replace("'","",$order_uom);
	$sensitivity=str_replace("'","",$sensitivity);
	$gmts_color_id=str_replace("'","",$gmts_color_id);
	$gmts_size_id=str_replace("'","",$gmts_size_id);
	$item_color_id=str_replace("'","",$item_color_id);
	$item_size=str_replace("'","",$item_size);
	$all_po_id=str_replace("'","",$all_po_id);
	$save_data=str_replace("'","",$save_data);
	$txt_receive_qnty=str_replace("'","",$txt_receive_qnty);
	$receive_basis=str_replace("'","",$receive_basis);
	$hid_job_no=str_replace("'","",$hid_job_no);
	$hid_ir_no=str_replace("'","",$hid_ir_no);
	$update_id=str_replace("'","",$update_id);
	//echo $update_id.stes;die;
	//$txt_rate=str_replace("'","",$txt_rate);
	$txt_rate=str_replace("'","",trim($txt_rate));
	if($txt_rate!="") {
		$txt_rate="'".number_format($txt_rate,6,'.','')."'";
	}

	//echo $booking_no."=";
	//echo $type.'kaiyum';
	$data=explode("**",$data);
	$po_id=$data[0];
	$type=$data[1];
	if($type==1) 
	{
		$item_group=$data[2]; 
		$item_description=$data[3]; 
		$brand_supref=$data[4]; 
		$order_uom=$data[5]; 
		$receive_basis=$data[6]; 
		$save_data=$data[7];
		$gmts_color_id=$data[8]; 
		$gmts_size_id=$data[9]; 
		$item_color_id=$data[10]; 
		$item_size=$data[11];
		$booking_pi_id=$data[12];
	}
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0","id","color_name");
	$size_arr = return_library_array("select id, size_name from lib_size","id","size_name");
	$variable_set_invent = sql_select("SELECT category, over_rcv_status, over_rcv_percent, over_rcv_payment from variable_inv_ile_standard where company_name=$cbo_company_id and variable_list=23 and category =4 order by id");
	$over_receive_limit = !empty($variable_set_invent) ? $variable_set_invent[0][csf('over_rcv_percent')] : 0;
	?> 

	<script>
		var receive_basis=<? echo $receive_basis; ?>;
		
		function fn_show_check()
		{
			if(form_validation('cbo_buyer_name','Buyer Name')==false )
			{
				return;
			}	
					
			show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $cbo_company_id; ?>+'_'+document.getElementById('cbo_buyer_name').value+'_'+'<? echo $all_po_id; ?>', 'create_po_search_list_view', 'search_div', 'trims_receive_entry_controller', 'setFilterGrid(\'tbl_list_search\',-1);hidden_field_reset();');
			set_all();
		}
		
		function distribute_qnty()
		{ 
			var tot_wo_qty=$('#value_tot_wo_qty').text();
			var tot_rcv_balance=$('#value_tot_bal_qty').text().replace(/,/g, "")*1;
			var tot_wo_qty=tot_wo_qty.replace(/,/g, "");
			//alert(tot_wo_qty+"="+tot_rcv_balance);return;
			
			var txt_prop_trims_qty = $('#txt_prop_trims_qty').val()*1;
			var tblRow = $("#tbl_list_search tbody tr").length-1;
			var tot_visiable_row = $("#tbl_list_search tbody tr:visible").length-1;
			var balance = txt_prop_trims_qty;
			var len=totalTrims=0;totalTrimsTest=0;
			
			if(txt_prop_trims_qty>0)
			{
				$('#txt_prop_ship_trims_qty').attr('disabled',true);
			}
			else
			{
				$('#txt_prop_ship_trims_qty').attr('disabled',false);
			}
			for(var i=1;i<=tblRow;i++)
			{
				if($("#tr_"+i).is(':visible')==true)
				{
					len=len+1;
					var wo_qnty=$('#woQty_'+i).text()*1;
					var RcvBalance=$('#txtRcvBalance_'+i).val()*1;
					var perc=(RcvBalance/tot_rcv_balance)*100;
					if(perc>100) perc=100;
					//alert(perc+"="+RcvBalance+"="+tot_rcv_balance);
					var trims_qnty=(perc*txt_prop_trims_qty)/100;
					totalTrims = totalTrims*1+trims_qnty*1;
					totalTrims = totalTrims.toFixed(6);
											
					if(tot_visiable_row==len)
					{
						var balance = txt_prop_trims_qty-totalTrims;
						if(balance>0) trims_qnty=trims_qnty+(balance);
					}					
					//alert(perc+"="+txt_prop_trims_qty+"="+i+"="+trims_qnty+"="+totalTrims);
					$('#txtRecvQnty_'+i).val(trims_qnty.toFixed(6));
				}
				else{
					$('#txtRecvQnty_'+i).val(0);
				}//end if;
			} 
			
			calculate_total(0);
		}
		
		function distribute_ship_qnty()
		{ 
			var tot_wo_qty=$('#value_tot_wo_qty').text();
			var tot_rcv_balance=$('#value_tot_rcv_balance').text().replace(/,/g, "")*1;
			var tot_wo_qty=tot_wo_qty.replace(/,/g, "");
			
			var txt_prop_ship_trims_qty=$('#txt_prop_ship_trims_qty').val()*1;
			var tblRow = $("#tbl_list_search tbody tr").length-1;
			var balance =txt_prop_ship_trims_qty;
			var len=totalTrims=0;
			
			if(txt_prop_ship_trims_qty>0)
			{
				$('#txt_prop_trims_qty').attr('disabled',true);
			}
			else
			{
				$('#txt_prop_trims_qty').attr('disabled',false);
			}
			
			$("#tbl_list_search").find('tbody tr').each(function()
				{
					$(this).find('input[name="txtRecvQnty[]"]').val("");
				});
			
			for(var i=1;i<=tblRow;i++)
			{
				if($("#tr_"+i).is(':visible')==true)
				{
				
					len=len+1;
					var RcvBalance=$('#txtRcvBalance_'+i).val()*1;
					var trims_qnty=RcvBalance;
					totalTrims = totalTrims*1+trims_qnty*1;
					totalTrims = totalTrims.toFixed(6);
					if(RcvBalance>0 && txt_prop_ship_trims_qty>0)
					{
						if(balance<trims_qnty)
						{
							$('#txtRecvQnty_'+i).val(balance.toFixed(6));
							break;
						}
						else
						{
							$('#txtRecvQnty_'+i).val(trims_qnty.toFixed(6));
							balance=(balance*1)-(trims_qnty*1).toFixed(6);
						}
					}
				}
				else{
					//$('#txtRecvQnty_'+i).val(0);
				}//end if;
			} 
			calculate_total(3);
		}
		
		function calculate_total(src_type)
		{
			var tblRow = $("#tbl_list_search tbody tr").length-1;
			var total_receive=0;
			for(var i=1;i<=tblRow;i++)
			{
				//if($("#tr_"+i).is(':visible')==true){
				if($('#txtRecvQnty_'+i).val()*1>0)
				{
					var wo_qnty=$('#woQty_'+i).text()*1;
					var over_receive_limit="<?= $over_receive_limit;?>";
					var cbo_payment_over_recv="<?= $cbo_payment_over_recv;?>";
					
					if(over_receive_limit>0)
					{ 
						var over_receive_limit_qnty=((over_receive_limit/100)* wo_qnty);
					}
					else var over_receive_limit_qnty=0;
					//alert(wo_qnty+"="+over_receive_limit+"="+over_receive_limit_qnty+"="+src_type);return;
					//var recv_bal=(($('#tdRcvQnty_'+i).attr("title")*1+$('#txtTotRecvQnty_'+i).val()*1)+over_receive_limit_qnty);
					var recv_bal=((wo_qnty+over_receive_limit_qnty)-$('#txtTotRecvQnty_'+i).val()*1+$('#tdRcvQnty_'+i).attr("title")*1);
					var recv_qnty=$('#txtRecvQnty_'+i).val()*1;
					//alert(recv_qnty);return;
					if(recv_qnty>recv_bal && src_type!=1 && cbo_payment_over_recv==0)
					{
						
						alert("Receive Quantity Not Allow Over Balance");
						$('#txtRecvQnty_'+i).val("");
					}
					else
					{
						total_receive=total_receive*1+recv_qnty;
					}
					
				}
				//}
			}
			if(src_type==2) var tot_input_qnty=$('#txt_prop_ship_trims_qty').val()*1;
			else var tot_input_qnty=$('#txt_prop_trims_qty').val()*1;

			if(tot_input_qnty>0){
			if(total_receive!=tot_input_qnty) total_receive=tot_input_qnty;
			}
			
			$('#total_recieve').html(total_receive.toFixed(6));
		}
		
		var selected_id = new Array();
		
		function check_all_data() 
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length; 

			tbl_row_count = tbl_row_count-1;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value( i );
			}
		}
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function set_all()
		{
			var old=document.getElementById('txt_po_row_id').value;
			if(old!="")
			{   
				old=old.split(",");
				for(var i=0; i<old.length; i++)
				{  
					js_set_value( old[i],0 ) 
				}
			}
		}
		
		function js_set_value( str) 
		{
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
			
			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual_id' + str).val() );
				
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
			}
			var id = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			
			$('#po_id').val( id );
		}
		
		function show_trims_recv() 
		{ 
			var po_id=$('#po_id').val();
			show_list_view ( po_id+'**'+'1'+'**'+'<? echo $item_group; ?>'+'**'+'<? echo $item_description; ?>'+'**'+'<? echo $brand_supref; ?>'+'**'+'<? echo $order_uom; ?>'+'**'+'<? echo $receive_basis; ?>'+'**'+'<? echo $save_data; ?>'+'**'+'<? echo $gmts_color_id; ?>'+'**'+'<? echo $gmts_size_id; ?>'+'**'+'<? echo $item_color_id; ?>'+'**'+'<? echo $item_size; ?>'+'**'+'<? echo $booking_pi_id; ?>', 'po_popup', 'search_div', 'trims_receive_entry_controller', 'setFilterGrid(\'tbl_list_search\',-1);');
		}
		
		function hidden_field_reset()
		{
			$('#po_id').val('');
			$('#save_string').val( '' );
			$('#tot_trims_qnty').val( '' );
			selected_id = new Array();
		}
		
		function fnc_close()
		{
			var save_string='';	 var tot_trims_qnty=''; var po_id_array = new Array(); var po_no='';
			
			$("#tbl_list_search").find('tbody tr').not(':first').each(function()
			{
				var txtPoId=$(this).find('input[name="txtPoId[]"]').val();
				var txtPoName=$(this).find('input[name="txtPoName[]"]').val();
				var txtRecvQnty=$(this).find('input[name="txtRecvQnty[]"]').val();
				var txtTotRecvQnty=$(this).find('input[name="txtTotRecvQnty[]"]').val();
				
				tot_trims_qnty=tot_trims_qnty*1+txtRecvQnty*1;
				
				if(txtRecvQnty*1>0)
				{
					if(save_string=="")
					{
						save_string=txtPoId+"_"+txtRecvQnty;
					}
					else
					{
						save_string+=","+txtPoId+"_"+txtRecvQnty;
					}
					
					if(jQuery.inArray( txtPoId, po_id_array) == -1 ) 
					{
						po_id_array.push(txtPoId);
						if(po_no=="") po_no=txtPoName; else po_no+=","+txtPoName;
					}
				}
			});
			
			if(save_string!="")
			{
				$('#save_string').val( save_string );
				$('#tot_trims_qnty').val( tot_trims_qnty.toFixed(4));
				$('#all_po_id').val( po_id_array );
				$('#all_po_no').val( po_no );
			}
			parent.emailwindow.hide();
		}
    </script>
    </head>
    <body>
    
	<? 
	if($type!=1)
	{
		$type=0;
		?>
		<form name="searchdescfrm"  id="searchdescfrm">
		<fieldset style="width:640px;margin-left:10px">
        	<input type="hidden" name="save_string" id="save_string" class="text_boxes" value="">
            <input type="hidden" name="tot_trims_qnty" id="tot_trims_qnty" class="text_boxes" value="">
            <input type="hidden" name="all_po_id" id="all_po_id" class="text_boxes" value="">
            <input type="hidden" name="all_po_no" id="all_po_no" class="text_boxes" value="">
		<?
	}
	
	
	if((($receive_basis==1 && $wo_pi_basis_id==2) || $receive_basis==4 || $receive_basis==6 ) && $type!=1)
	{
		?>
		<table cellpadding="0" cellspacing="0" width="640" class="rpt_table" border="1" rules="all">
			<thead>
				<th>Buyer</th>
				<th>Search By</th>
				<th>Search</th>
				<th>
					<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
					<input type="hidden" name="po_id" id="po_id" value="">
				</th> 
			</thead>
			<tr class="general">
				<td align="center">
					<?
						echo create_drop_down( "cbo_buyer_name", 170, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",$data[0] ); 
					?>       
				</td>
				<td align="center">	
					<?
						$search_by_arr=array(1=>"PO No",2=>"Job No",3=>"Style Ref." );
						echo create_drop_down( "cbo_search_by", 170, $search_by_arr,"",0, "--Select--", "",$dd,0 );
					?>
				</td>                 
				<td align="center">				
					<input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
				</td> 						
				<td align="center">
					<input type="button" name="button2" class="formbutton" value="Show" onClick="fn_show_check();" style="width:100px;" />
				</td>
			</tr>
		</table>
		<div id="search_div" style="margin-top:10px">
       		<?
			if($save_data!="")
			{
				?>
				<div style="margin-left:10px; margin-top:10px">
					<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="670">
						<thead>
							<th width="80">PO No</th>
                            <th width="70">Ship Date</th>
                            <th width="60">Style</th>
                            <th width="80">Garments Qty.</th>
                            <th width="110">Total Receive Qty.</th>
                            <th width="60">UOM</th>
                            <th width="115">Receive Qty.</th>
						</thead>
					</table>
					<div style="width:690px; max-height:280px; overflow-y:scroll" id="list_container" align="left"> 
						<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="670" id="tbl_list_search">  
							<tbody>
							<?
							
							$i=1; $tot_trims_receive_qnty=$total_recv_qnty=0;

							$explSaveData = explode(",",$save_data); 	
							for($z=0;$z<count($explSaveData);$z++)
							{
								if ($i%2==0)  
									$bgcolor="#E9F3FF";
								else
									$bgcolor="#FFFFFF";
									
								$po_wise_data = explode("_",$explSaveData[$z]);
								$order_id=$po_wise_data[0];
								$recv_qnty=$po_wise_data[1];
								
								if($item_size!="" || $db_type==0)
								{
									$item_size_cond="trim(a.item_size)='".trim($item_size)."'";
								}
								else $item_size_cond="a.item_size is null";
								
								if($brand_supref!="" || $db_type==0)
								{
									$brand_supref_cond="trim(a.brand_supplier)='".trim($brand_supref)."'";
								}
								else $brand_supref_cond="a.brand_supplier is null";
								
								if($db_type==2 && $gmts_size_id=="")
								{
									$gmts_size_field="nvl(a.gmts_size_id,0)";
									$gmts_size_id=0;
								}
								else
								{
									$gmts_size_field="a.gmts_size_id";
								}
								
								if($db_type==2 && $gmts_color_id=="")
								{
									$gmts_color_field="nvl(a.gmts_color_id,0)";
									$gmts_color_id=0;
								}
								else
								{
									$gmts_color_field="a.gmts_color_id";
								}
								
								if($db_type==2 && $item_color_id=="")
								{
									$item_color_field="nvl(a.item_color,0)";
									$item_color_id=0;
								}
								else
								{
									$item_color_field="a.item_color";
								}
								
								if($receive_basis==1)
								{
									$tot_recv_qnty=return_field_value("sum(b.quantity) as qnty","inv_receive_master m, inv_trims_entry_dtls a, order_wise_pro_details b","m.id=a.mst_id and m.receive_basis=1 and m.booking_id='$booking_pi_id' and a.id=b.dtls_id and a.item_group_id='$item_group' and a.item_description='$item_description' and $brand_supref_cond and $gmts_color_field='$gmts_color_id' and $item_color_field='$item_color_id' and $gmts_size_field='$gmts_size_id' and $item_size_cond and b.entry_form=24 and b.trans_type=1 and b.po_breakdown_id='$order_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","qnty");
								}
								else
								{
									$tot_recv_qnty=return_field_value("sum(b.quantity) as qnty","inv_receive_master m, inv_trims_entry_dtls a, order_wise_pro_details b","m.id=a.mst_id and m.receive_basis=$receive_basis and m.entry_form=24 and a.id=b.dtls_id and a.item_group_id='$item_group' and a.item_description='$item_description' and $brand_supref_cond and $gmts_color_field='$gmts_color_id' and $item_color_field='$item_color_id' and $gmts_size_field='$gmts_size_id' and $item_size_cond and b.entry_form=24 and b.trans_type=1 and b.po_breakdown_id='$order_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","qnty");
								}

								$po_data=sql_select("select a.style_ref_no,b.id, b.po_number, b.po_quantity,b.pub_shipment_date from wo_po_break_down b,wo_po_details_master a where b.job_no_mst=a.job_no and b.id=$order_id order by b.pub_shipment_date");
								
								$tot_trims_receive_qnty+=$recv_qnty;
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
									<td width="80">
                                        <p><? echo $po_data[0][csf('po_number')]; ?></p>
                                        <input type="hidden" name="txtPoId[]" id="txtPoId_<? echo $i; ?>" value="<? echo $po_data[0][csf('id')]; ?>">
                                        <input type="hidden" name="txtPoName[]" id="txtPoName_<? echo $i; ?>" value="<? echo $po_data[0][csf('po_number')]; ?>">
                                    </td>
                                     <td width="70"><p><? echo change_date_format($po_data[0][csf('pub_shipment_date')]); ?></p></td>
                                     <td width="60"><p><? echo $po_data[0][csf('style_ref_no')]; ?></p></td>
                                    <td align="right" width="80"><? echo $po_data[0][csf('po_quantity')]; ?></td>
                                    <td width="110" align="right">
                                        <? 
										$total_recv_qnty+=$tot_recv_qnty;
										echo number_format($tot_recv_qnty); ?>
                                        <input type="hidden" name="txtTotRecvQnty[]" id="txtTotRecvQnty_<? echo $i; ?>" value="<? echo $tot_recv_qnty; ?>">
                                    </td>
                                    <td width="60" align="center"><? echo $unit_of_measurement[$order_uom]; ?></td>
                                    <td align="right" width="115">
                                        <input type="text" name="txtRecvQnty[]" id="txtRecvQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:100px" value="<? echo $recv_qnty; ?>" onKeyUp="calculate_total(<? echo $type;?>);">
                                    </td>
								</tr>
							<? 
							$i++;
							}
							?>
                            </tbody>
                            <tfoot class="tbl_bottom">
                                <td colspan="4">Total</td>
                                <td id=""><? echo $total_recv_qnty; ?></td>
                                <td id=""><? //echo $tot_trims_receive_qnty; ?></td>
                                <td id="total_recieve"><? echo $tot_trims_receive_qnty; ?></td>
                            </tfoot>
						</table>
					</div>
					<table width="620">
						 <tr>
							<td align="center" >
								<input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
							</td>
						</tr>
					</table>
				</div>
				<?
			}
			?>
        </div>
		<?
	}
	else //|| $wo_pi_basis_id==2
	{

		if($receive_basis==2 || $wo_pi_basis_id==1) 
		{
			$style='';
			$disabled="";
		}
		else 
		{
			$style='style="display:none"';
			$disabled="disabled";
		}
		?>
		<div style="margin-left:10px; margin-top:5px">
            <table cellpadding="0" cellspacing="0" rules="all" width="790" align="center" id="tbl_prop" <? echo $style; ?> >
            	<tbody>
	                <tr>
	                	<td>&nbsp;&nbsp;&nbsp;</td>
	                    <td><b>Proportionately</b></td>
	                    <td><b>Ship Date Wise</b></td>
	                </tr>
	                <tr>
	                    <td width="250" align="right"><b>Total Receive Qty : &nbsp;&nbsp;</b></td>
	                    <td><input type="text" name="txt_prop_trims_qty" id="txt_prop_trims_qty" class="text_boxes_numeric" style="width:100px" onBlur="distribute_qnty()" <? echo $disabled; ?> /></td>
	                    <td><input type="text" name="txt_prop_ship_trims_qty" id="txt_prop_ship_trims_qty" class="text_boxes_numeric" style="width:100px" onBlur="distribute_ship_qnty()" <? echo $disabled; ?> /></td>
	                </tr>
                </tbody>
                <tfoot class="tbl_bottom">
						<!-- Job No.:<span id="job_no_span"></span>; -->
                	<td colspan="3">IR :<? echo $hid_ir_no; ?> ; Gmts. Color:<? echo $color_arr[$gmts_color_id]; ?> ; Item Color:<? echo $color_arr[$item_color_id]; ?> , Gmts. Size:<? echo $size_arr[$gmts_size_id]; ?> ; Item Size:<? echo $item_size; ?></td>
                </tfoot>
            </table>
			<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="790">
				<thead>
					<th width="80">PO No</th>
					<th width="60">Job No</th>
                    <? 
					if($receive_basis==2 || $wo_pi_basis_id==1) 
					{
						echo "<th width='65'>I.R.</th>";
						echo "<th width='65'>Ship Date</th>";
						echo "<th width='60'>Style</th>";
						echo "<th width='80'>Garments Qty.</th>";
						echo "<th width='80'>WO Qty.</th>";
						echo "<th width='80'>Total Receive Qty.</th>";
						echo "<th width='80'>Balance </th>";
						echo "<th width='50'>UOM</th>";
						echo "<th>Receive Qty.</th>";
					}
					else
					{
						echo "<th width='65'>I.R.</th>";
						echo "<th width='65'>Ship Date</th>";
						echo "<th width='60'>Style</th>";
						echo "<th width='100'>Garments Qty.</th>";
						echo "<th width='80'>Total Receive Qty.</th>";
						echo "<th width='80'>Balance </th>";
						echo "<th width='50'>UOM</th>";
						echo "<th width='80'>Receive Qty.</th>";
					}
					?>
				</thead>
			</table>
			<div style="width:800px; max-height:280px; overflow-y:scroll" id="list_container" align="left"> 
				<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="790" id="tbl_list_search">  
                	<tbody>
					<? 
					$i=1; $tot_trims_receive_qnty=0; $po_array=array();
					
					if($item_size!="" || $db_type==0)
					{
						$item_size_cond="trim(a.item_size)='".trim(str_replace("'","",$item_size))."'";
						$item_size_cond2="trim(c.item_size)='".trim(str_replace("'","",$item_size))."'";
						$item_size_cond4="trim(b.item_size)='".trim(str_replace("'","",$item_size))."'";
					}
					else 
					{
						$item_size_cond="a.item_size is null";
						$item_size_cond2="c.item_size is null";
						$item_size_cond4="b.item_size is null";
					}
					
					if(trim(str_replace("'","",$brand_supref))!="" || $db_type==0)
					{
						$brand_supref_cond="trim(a.brand_supplier)='".trim(str_replace("'","",$brand_supref))."'";
						$brand_supref_cond2="trim(c.brand_supplier)='".trim(str_replace("'","",$brand_supref))."'";
						$brand_supref_cond4="trim(b.brand_supplier)='".trim(str_replace("'","",$brand_supref))."'";
					}
					else 
					{
						$brand_supref_cond="a.brand_supplier is null";
						$brand_supref_cond2="c.brand_supplier is null";
						$brand_supref_cond4="b.brand_supplier is null";
					}
					
					if($db_type==2 && ($gmts_size_id=="" || $gmts_size_id==0))
					{
						$gmts_size_field="nvl(a.gmts_size_id,0)";
						$gmts_size_field2="nvl(c.gmts_sizes,0)";
						$gmts_size_field3="nvl(a.gmts_size,0)";
						$gmts_size_id=0;
						$gmts_size_field4="nvl(b.size_id,0)";
					}
					else
					{
						$gmts_size_field="a.gmts_size_id";
						$gmts_size_field2="c.gmts_sizes";
						$gmts_size_field3="a.gmts_size";
						$gmts_size_field4="b.size_id";
					}
					
					if($db_type==2 && ($gmts_color_id=="" || $gmts_color_id==0))
					{
						$gmts_color_field="nvl(a.gmts_color_id,0)";
						$gmts_color_field2="nvl(c.color_number_id,0)";
						$gmts_color_id=0;
						$gmts_color_field4="nvl(b.color_id,0)";
					}
					else
					{
						$gmts_color_field="a.gmts_color_id";
						$gmts_color_field2="c.color_number_id";
						$gmts_color_field4="b.color_id";
					}
					
					if($db_type==2 && ($item_color_id==""|| $item_color_id==0))
					{
						$item_color_field="nvl(a.item_color,0)";
						$item_color_field2="nvl(c.item_color,0)";
						$item_color_id=0;
						$item_color_field4="nvl(b.item_color,0)";
					}
					else
					{
						$item_color_field="a.item_color";
						$item_color_field2="c.item_color";
						$item_color_field4="b.item_color";
					}
					$total_recv_qnty=0;$total_balance_qnty=0;
					if($save_data!="" &&(($receive_basis==1 && $wo_pi_basis_id==2)||$receive_basis==4 || $receive_basis==6))
					{
						$explSaveData = explode(",",$save_data); $po_array=array();	
						for($z=0;$z<count($explSaveData);$z++)
						{
							$po_wise_data = explode("_",$explSaveData[$z]);
							$order_id=$po_wise_data[0];
							$recv_qnty=$po_wise_data[1];
							$po_array[$order_id]=$recv_qnty;
						}
					
						$data_array=sql_select("select a.style_ref_no as style_ref, b.id, b.po_number, b.po_quantity,b.pub_shipment_date, b.grouping as int_ref_no,a.job_no_prefix_num from wo_po_details_master a, wo_po_break_down b where  a.job_no=b.job_no_mst and b.id in ($po_id) order by b.pub_shipment_date");
						foreach($data_array as $row)
						{
							if ($i%2==0)  
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";
							
							$recv_qnty=$po_array[$row[csf('id')]];
							$tot_trims_receive_qnty+=$recv_qnty;
							// and $item_color_field='$item_color_id' and $gmts_color_field='$gmts_color_id' and ROUND(c.rate,6)=ROUND($booking_rate,6)
							
							if($receive_basis==1)
							{
								$tot_recv_qnty=return_field_value("sum(b.quantity) as qnty","inv_receive_master m, inv_trims_entry_dtls a, order_wise_pro_details b","m.id=a.mst_id and m.receive_basis=1 and m.booking_id='$booking_pi_id' and a.id=b.dtls_id and a.item_group_id='$item_group' and trim(REGEXP_REPLACE((case when a.item_description like '%, [BS]' then SUBSTR(a.item_description, 0, LENGTH(a.item_description) - 6) else a.item_description end), '\s{2,}', ' '))=trim(REGEXP_REPLACE('$item_description', '\s{2,}', ' ')) and $brand_supref_cond and $gmts_color_field='$gmts_color_id' and $item_color_field='$item_color_id' and $gmts_size_field='$gmts_size_id' and $item_size_cond and b.entry_form=24 and b.trans_type=1 and b.po_breakdown_id='".$row[csf('id')]."' and ROUND(a.rate,6)=ROUND($txt_rate,6) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","qnty");
							}
							else
							{
								$tot_recv_qnty=return_field_value("sum(b.quantity) as qnty","inv_receive_master m, inv_trims_entry_dtls a, order_wise_pro_details b","m.id=a.mst_id and m.receive_basis=$receive_basis and m.entry_form=24 and a.id=b.dtls_id and a.item_group_id='$item_group' and trim(REGEXP_REPLACE((case when a.item_description like '%, [BS]' then SUBSTR(a.item_description, 0, LENGTH(a.item_description) - 6) else a.item_description end), '\s{2,}', ' '))=trim(REGEXP_REPLACE('$item_description', '\s{2,}', ' ')) and $brand_supref_cond and $gmts_color_field='$gmts_color_id' and $gmts_size_field='$gmts_size_id' and $item_size_cond and b.entry_form=24 and b.trans_type=1 and b.po_breakdown_id='".$row[csf('id')]."' and ROUND(a.rate,6)=ROUND($txt_rate,6) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","qnty");
							}
							$rcv_balance=$recv_qnty-$total_recv_qnty;
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" style="">
								<td width="80">
									<p><? echo $row[csf('po_number')]; ?></p>
									<input type="hidden" name="txtPoId[]" id="txtPoId_<? echo $i; ?>" value="<? echo $row[csf('id')]; ?>">
									<input type="hidden" name="txtPoName[]" id="txtPoName_<? echo $i; ?>" value="<? echo $row[csf('po_number')]; ?>">
								</td>
                                <td width="60"><p><? echo $row[csf('job_no_prefix_num')]; ?></p></td>
                                <td width="65"><p><? echo $row[csf('int_ref_no')]; ?></p></td>
                                <td width="70"><p><? echo change_date_format($row[csf('pub_shipment_date')]); ?></p></td>
                                <td width="60"><p><? echo $row[csf('style_ref')]; ?></p></td>
                                <td width="100" align="right"><? echo $row[csf('po_quantity')]; ?></td>
								<td width="110" align="right">
									<?
									$total_recv_qnty+=$tot_recv_qnty;//$tot_trims_receive_qnty+=$recv_qnty;
									 echo number_format($tot_recv_qnty,4); ?>
									<input type="hidden" name="txtTotRecvQnty[]" id="txtTotRecvQnty_<? echo $i; ?>" value="<? echo $tot_recv_qnty; ?>">
								</td>
								<td width="50" align="center"><? echo $unit_of_measurement[$order_uom]; ?></td>
								<td align="right" width="115" title="<?= $recv_qnty ?>" id="tdRcvQnty_<? echo $i; ?>">
									<input type="text" name="txtRecvQnty[]" id="txtRecvQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:100px" value="<? echo $recv_qnty; ?>" onKeyUp="calculate_total(<? echo $type;?>);">
								</td>
							</tr>
							<? 
							$i++;
						}
					}
					else if($save_data!="" && ($receive_basis==2 || $wo_pi_basis_id==1))
					{
						$explSaveData = explode(",",$save_data); $order_data_array=array();$po_asc_data=array();
							
						for($z=0;$z<count($explSaveData);$z++)
						{
							$po_wise_data = explode("_",$explSaveData[$z]);
							$order_data_array[$po_wise_data[0]]=$po_wise_data[1];
							//$po_asc_data[$po_wise_data[0]].=$po_wise_data[1]."__";
						}
						
						if($receive_basis==1 && $wo_pi_basis_id==1)
						{
							$sql_data="select b.work_order_dtls_id from com_pi_master_details a,com_pi_item_details b where a.id=b.pi_id and a.id=$booking_pi_id and a.item_category_id in(4) and b.status_active=1 and b.is_deleted=0";
							$booking_no='';
							$sql_result=sql_select($sql_data);
							foreach($sql_result as $row)
							{
								if($booking_no=='') $booking_no=$row[csf('work_order_dtls_id')];else $booking_no.=",".$row[csf('work_order_dtls_id')];
							}
							$booking_no=rtrim($booking_no,",");
							$book_id_cond="";
							if($booking_no!="") 
							{
								if($db_type==0) $book_id_cond="and b.id in(".$booking_no.")";
								else
								{
									$b_ids=explode(",",$booking_no);
									if(count($b_ids)>990)
									{
										$book_id_cond="and (";
										$b_ids=array_chunk($b_ids,990);
										$z=0;
										foreach($b_ids as $id)
										{
											$id=implode(",",$id);
											if($z==0) $book_id_cond.=" b.id in(".$id.")";
											else $book_id_cond.=" or b.id in(".$id.")";
											$z++;
										}
										$book_id_cond.=")";
									}
									else $book_id_cond="and b.id in(".$booking_no.")";
								}
							}
						}
						
						if($sensitivity=='') $sensitivity=0; else $sensitivity=$sensitivity;
						$descrip_cond="";
						//if($item_description!="") $descrip_cond=" and trim(c.description)='".trim($item_description)."'";
						if($item_description!="") $descrip_cond=" and trim(REGEXP_REPLACE(c.description, '\s{2,}', ' '))=REGEXP_REPLACE('".trim($item_description)."', '\s{2,}', ' ')";
						if($receive_basis==1 && $wo_pi_basis_id==1)
						{
							$item_color_cond_pi = "";
							if($item_color_id)
							{
								$item_color_cond_pi = "and $item_color_field2='$item_color_id'";
							}
							
						    $po_sql="select a.id, a.pub_shipment_date,a.po_number, d.job_no_prefix_num,d.style_ref_no as style_ref, a.grouping as int_ref_no, sum(distinct a.po_quantity) as po_quantity, sum(c.cons) as qty 
							from wo_po_break_down a, wo_booking_dtls b, wo_trim_book_con_dtls c, wo_po_details_master d 
							where a.id=b.po_break_down_id and b.id=c.wo_trim_booking_dtls_id and d.job_no=a.job_no_mst and b.job_no=a.job_no_mst  and b.trim_group='$item_group' $descrip_cond and $brand_supref_cond2 and $gmts_color_field2='$gmts_color_id' and $gmts_size_field2='$gmts_size_id' $item_color_cond_pi and $item_size_cond2 and c.cons>0 and b.status_active=1 and b.is_deleted=0 $book_id_cond and ROUND(c.rate,6)=ROUND($txt_rate,6)
							group by a.id, a.pub_shipment_date, a.po_number, d.job_no_prefix_num, a.grouping, d.style_ref_no
							order by a.pub_shipment_date, a.id";
						}
						else if($receive_basis==2)
						{
							 $po_sql="select a.id, a.pub_shipment_date,a.po_number, d.job_no_prefix_num,d.style_ref_no as style_ref, a.grouping as int_ref_no, sum(distinct a.po_quantity) as po_quantity, sum(c.cons) as qty  
							 from wo_po_break_down a, wo_booking_dtls b, wo_trim_book_con_dtls c, wo_po_details_master d 
							 where a.id=b.po_break_down_id and b.id=c.wo_trim_booking_dtls_id and d.job_no=a.job_no_mst and b.job_no=a.job_no_mst and b.booking_no='$booking_no' and b.sensitivity='$sensitivity' and b.trim_group='$item_group' $descrip_cond and $brand_supref_cond2 and $gmts_color_field2='$gmts_color_id'  and $gmts_size_field2='$gmts_size_id' and $item_color_field2='$item_color_id' and $item_size_cond2 and c.cons>0 and b.status_active=1 and b.is_deleted=0 and ROUND(c.rate,6)=ROUND($txt_rate,6)
							 group by a.id, a.pub_shipment_date,a.po_number, d.job_no_prefix_num,d.style_ref_no, a.grouping  
							 order by a.pub_shipment_date, a.id";
						}
						//echo $po_sql; and trim(REGEXP_REPLACE((case when a.item_description like '%, [BS]' then SUBSTR(a.item_description, 4, LENGTH(a.item_description) - 8) else a.item_description end), '\s{2,}', ' '))=trim('$item_description')
						if($update_id>0) $up_rcv_cond=" and m.id<>$update_id";
						$qty_sql="select b.po_breakdown_id,sum(b.quantity) as qnty 
						from inv_receive_master m, inv_trims_entry_dtls a, order_wise_pro_details b
						where m.id=a.mst_id and m.receive_basis=$receive_basis and m.entry_form=24 and m.booking_id='$booking_pi_id' and a.id=b.dtls_id and a.item_group_id='$item_group' and trim(REGEXP_REPLACE((case when a.item_description like '%, [BS]' then SUBSTR(a.item_description, 0, LENGTH(a.item_description) - 6) else a.item_description end), '\s{2,}', ' '))=trim(REGEXP_REPLACE('$item_description', '\s{2,}', ' ')) and $brand_supref_cond and $gmts_color_field='$gmts_color_id' and $item_color_field='$item_color_id' and $gmts_size_field='$gmts_size_id' and $item_size_cond and ROUND(a.rate,6)=ROUND($txt_rate,6) and b.entry_form=24 and b.trans_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $up_rcv_cond
						group by b.po_breakdown_id ";
						$totalRcvQty_arr=array();
						$qtyArray=sql_select($qty_sql);
						foreach($qtyArray as $row)
						{ 
							$totalRcvQty_arr[$row[csf('po_breakdown_id')]]=$row[csf('qnty')];
							$po_breakdown_ids .=$row[csf('po_breakdown_id')].',';
							//$prod_ids .=$row[csf('prod_id')].',';
						}
						$po_breakdown_ids=implode(",",array_unique(explode(",",chop($po_breakdown_ids,','))));
						//and $gmts_color_field='$gmts_color_id' 

						$rcvRtn_qty_sql = "SELECT b.po_breakdown_id ,sum( b.quantity ) as recv_return_qty from product_details_master a, inv_transaction t, order_wise_pro_details b, wo_po_break_down c, wo_po_details_master d
						where a.id=b.prod_id and a.id=t.prod_id and t.id=b.trans_id and b.po_breakdown_id=c.id and c.job_no_mst=d.job_no and a.id=b.prod_id and  b.po_breakdown_id in ($po_breakdown_ids) and a.item_category_id=4 and b.entry_form in(49) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_group_id='$item_group' and trim(REGEXP_REPLACE((case when a.item_description like '%, [BS]' then SUBSTR(a.item_description, 0, LENGTH(a.item_description) - 6) else a.item_description end), '\s{2,}', ' '))=trim(REGEXP_REPLACE('$item_description', '\s{2,}', ' ')) and $brand_supref_cond and $item_color_field='$item_color_id' and $gmts_size_field3='$gmts_size_id' and $item_size_cond 
						group by b.po_breakdown_id";

						$totalRcvRtnQty_arr=array();
						$rcvRtn_qtyArray=sql_select($rcvRtn_qty_sql); 
						foreach($rcvRtn_qtyArray as $row)
						{ 
							//[$row[csf('prod_id')]]
							$totalRcvRtnQty_arr[$row[csf('po_breakdown_id')]]=$row[csf('recv_return_qty')];
						}
						//echo $qty_sql; die;
						
						//echo $po_sql;
						$nameArray=sql_select($po_sql);
						foreach($nameArray as $row)
						{  
							if ($i%2==0)  
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";
							$tot_recv_qnty=$totalRcvQty_arr[$row[csf('id')]];
							$tot_recv_rtn_qnty=$totalRcvRtnQty_arr[$row[csf('id')]];
							$tot_recv_qnty=$tot_recv_qnty-$tot_recv_rtn_qnty;

							$recv_qnty=$order_data_array[$row[csf('id')]];
							$tot_trims_receive_qnty+=$recv_qnty;
							$tot_wo_qty+=$row[csf('qty')];
							$all_job .=$row[csf('job_no_prefix_num')].',';
							
						 	?>
							<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" style="">
								<td width="80">
									<p><? echo $row[csf('po_number')]; ?></p>
									<input type="hidden" name="txtPoId[]" id="txtPoId_<? echo $i; ?>" value="<? echo $row[csf('id')]; ?>">
									<input type="hidden" name="txtPoName[]" id="txtPoName_<? echo $i; ?>" value="<? echo $row[csf('po_number')]; ?>">
								</td>
                                <td width="60"><p><? echo $row[csf('job_no_prefix_num')]; ?></p></td>
                                <td width="65"><p><? echo $row[csf('int_ref_no')]; ?></p></td>
                                <td width='65'><p><? echo change_date_format($row[csf('pub_shipment_date')]); ?></p></td> 
                                <td width='60'><p><? echo $row[csf('style_ref')]; ?></p></td>
                                 <td width='80' align='right'><? echo $row[csf('po_quantity')]; ?></td>
								<td width='80' align='right' id="woQty_<? echo $i; ?>"><? echo number_format($row[csf('qty')],4,'.',''); ?></td>
								<td width="80" align="right">
									<? 
									$total_recv_qnty+=$tot_recv_qnty;
									echo number_format($tot_recv_qnty,4); ?>
									<input type="hidden" name="txtTotRecvQnty[]" id="txtTotRecvQnty_<? echo $i; ?>" value="<? echo number_format($tot_recv_qnty,4,'.','') ?>">
								</td>
                                <td width="80" align="right">
                               
									<?php 
									
									$rcv_balance=$row[csf('qty')]-$tot_recv_qnty; 
									$total_balance_qnty+=$rcv_balance;
									if(number_format($rcv_balance,4,'.','')>0) echo number_format($rcv_balance,4,'.',''); else echo "0.00"; ?>
									<input type="hidden" name="txtRcvBalance[]" id="txtRcvBalance_<? echo $i; ?>" value="<? echo $rcv_balance; ?>">
								</td>
								<td width="50" align="center"><? echo $unit_of_measurement[$order_uom]; ?></td>
								<td align="right" width="80" title="<?= $recv_qnty ?>" id="tdRcvQnty_<? echo $i; ?>">
									<input type="text" name="txtRecvQnty[]" id="txtRecvQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:70px" value="<? echo $recv_qnty; ?>" onKeyUp="calculate_total(<? echo $type;?>);">
								</td>
							</tr>
							<? 
                            $i++; 
						} 
						$all_jobs=implode(",",array_unique(explode(",",chop($all_job,","))));
					}
					else
					{ 
						if($type==1)
						{
							if($po_id!="")
							{
								$po_sql="select b.id, b.po_number, a.job_no_prefix_num,a.style_ref_no as style_ref, b.po_quantity,b.pub_shipment_date from wo_po_break_down b,wo_po_details_master a where a.job_no=b.job_no_mst and b.id in ($po_id) order by b.pub_shipment_date";
							}
						}
						else
						{
							if($receive_basis==1 && $wo_pi_basis_id==1)
							{
								$sql_data="select b.work_order_dtls_id from com_pi_master_details a,com_pi_item_details b where a.id=b.pi_id and a.id=$booking_pi_id and a.item_category_id in(4) and b.status_active=1 and b.is_deleted=0 and b.item_group='$item_group' and trim(REGEXP_REPLACE(b.item_description, '\s{2,}', ' '))='".trim(str_replace("'","",$item_description))."' and $brand_supref_cond4  and $gmts_size_field4='$gmts_size_id' and $gmts_color_field4='$gmts_color_id' and $item_color_field4='$item_color_id' and $item_size_cond4";
								$booking_no='';
								$sql_result=sql_select($sql_data);
								foreach($sql_result as $row)
								{
									if($temp_wo_id_check[$row[csf('work_order_dtls_id')]]=="" && $row[csf('work_order_dtls_id')])
									{
										$temp_wo_id_check[$row[csf('work_order_dtls_id')]]=$row[csf('work_order_dtls_id')];
										if($booking_no=='') $booking_no=$row[csf('work_order_dtls_id')];else $booking_no.=",".$row[csf('work_order_dtls_id')];
									}
								}
								
								$booking_no=rtrim($booking_no,",");
								$book_id_cond="";
								if($booking_no!="") 
								{
									if($db_type==0) $book_id_cond="and b.id in(".$booking_no.")";
									else
									{
										$b_ids=explode(",",$booking_no);
										if(count($b_ids)>990)
										{
											$book_id_cond="and (";
											$b_ids=array_chunk($b_ids,990);
											$z=0;
											foreach($b_ids as $id)
											{
												$id=implode(",",$id);
												if($z==0) $book_id_cond.=" b.id in(".$id.")";
												else $book_id_cond.=" or b.id in(".$id.")";
												$z++;
											}
											$book_id_cond.=")";
										}
										else $book_id_cond="and b.id in(".$booking_no.")";
									}
								}
							}
							
							if($sensitivity=='') $sensitivity=0; else $sensitivity=$sensitivity;
							$descrip_cond="";
							//if($item_description!="") $descrip_cond=" and trim(c.description)='".trim($item_description)."'";   
							if($item_description!="") $descrip_cond=" and trim(REGEXP_REPLACE(c.description, '\s{2,}', ' '))=REGEXP_REPLACE('".trim($item_description)."', '\s{2,}', ' ')";
							if($receive_basis==1 && $wo_pi_basis_id==1)
							{
								$item_color_cond_pi = "";
								if($item_color_id)
								{
									$item_color_cond_pi = "and $item_color_field2='$item_color_id'";
								}
								$po_sql="select a.id, a.po_number, d.job_no_prefix_num, d.style_ref_no as style_ref,a.po_quantity,a.pub_shipment_date, a.grouping as int_ref_no, sum(c.cons) as qty 
								from wo_po_break_down a, wo_booking_dtls b, wo_trim_book_con_dtls c, wo_po_details_master d 
								where a.id=b.po_break_down_id and b.id=c.wo_trim_booking_dtls_id  and d.job_no=a.job_no_mst and b.job_no=a.job_no_mst  and b.trim_group='$item_group'  $descrip_cond and $brand_supref_cond2 and $gmts_color_field2='$gmts_color_id' and $gmts_size_field2='$gmts_size_id' $item_color_cond_pi and $item_size_cond2 and c.cons>0 and b.status_active=1 and b.is_deleted=0 $book_id_cond and ROUND(c.rate,6)=ROUND($txt_rate,6)
								group by a.id, a.po_number, d.job_no_prefix_num, d.style_ref_no,a.po_quantity,a.pub_shipment_date, a.grouping 
								order by a.pub_shipment_date, a.id";
							}
							else if($receive_basis==2)
							{
								$po_sql="select a.id, a.po_number, d.job_no_prefix_num, d.style_ref_no as style_ref, a.pub_shipment_date, a.po_quantity, a.grouping as int_ref_no, sum(c.cons) as qty 
								from wo_po_break_down a, wo_booking_dtls b, wo_trim_book_con_dtls c,wo_po_details_master d 
								where a.id=b.po_break_down_id and b.id=c.wo_trim_booking_dtls_id and d.job_no=a.job_no_mst and b.job_no=a.job_no_mst and b.booking_no='$booking_no' and b.sensitivity='$sensitivity' and b.trim_group='$item_group' $descrip_cond and $brand_supref_cond2 and $gmts_color_field2='$gmts_color_id' and $gmts_size_field2='$gmts_size_id' and $item_color_field2='$item_color_id' and $item_size_cond2 and c.cons>0 and b.status_active=1 and b.is_deleted=0 and ROUND(c.rate,6)=ROUND($txt_rate,6)
								group by a.id, a.po_number, d.job_no_prefix_num, d.style_ref_no, a.pub_shipment_date, a.po_quantity, a.grouping
								order by a.pub_shipment_date, a.id";
							}
						}
						//echo $po_sql;
						if(trim(str_replace("'","",$item_description)) !="" || $db_type==0)
						{
							$description_cond_rcv=" and trim(REGEXP_REPLACE((case when a.item_description like '%, [BS]' then SUBSTR(a.item_description, 0, LENGTH(a.item_description) - 6) else a.item_description end), '\s{2,}', ' '))=REGEXP_REPLACE('".trim(str_replace(", [BS]","",str_replace("'","",$item_description)))."', '\s{2,}', ' ')";
						}
						else
						{
							$description_cond_rcv=" and trim(a.item_description) is null";
						}
						if($receive_basis==1 || $receive_basis==2)
						{ 
							$qty_sql="select b.po_breakdown_id,sum(b.quantity) as qnty 
							from inv_receive_master m, inv_trims_entry_dtls a, order_wise_pro_details b
							where m.id=a.mst_id and m.receive_basis=$receive_basis and m.entry_form=24 and a.booking_id='$booking_pi_id' and a.id=b.dtls_id and a.item_group_id='$item_group' $description_cond_rcv and $brand_supref_cond and $gmts_color_field='$gmts_color_id' and $item_color_field='$item_color_id' and $gmts_size_field='$gmts_size_id' and $item_size_cond and ROUND(a.rate,6)=ROUND($txt_rate,6) and b.entry_form=24 and b.trans_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and m.status_active=1 group by b.po_breakdown_id ";
						}
						else
						{
							$qty_sql="select b.po_breakdown_id,sum(b.quantity) as qnty 
							from inv_receive_master m, inv_trims_entry_dtls a, order_wise_pro_details b
							where m.id=a.mst_id and m.receive_basis=$receive_basis and m.entry_form=24 and a.booking_id='$booking_pi_id' and a.id=b.dtls_id and a.item_group_id='$item_group' $description_cond_rcv and $brand_supref_cond and $gmts_color_field='$gmts_color_id' and $item_color_field='$item_color_id' and $gmts_size_field='$gmts_size_id' and $item_size_cond and ROUND(a.rate,6)=ROUND($txt_rate,6) and b.entry_form=24 and b.trans_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_breakdown_id ";

						}

						//echo $qty_sql;// die;
						$totalRcvQty_arr=array();
						$qtyArray=sql_select($qty_sql);
						//print_r($qtyArray); 
						foreach($qtyArray as $row)
						{ 
							$totalRcvQty_arr[$row[csf('po_breakdown_id')]]=$row[csf('qnty')];
							$po_breakdown_ids .=$row[csf('po_breakdown_id')].',';
							//$prod_ids .=$row[csf('prod_id')].',';
						}
						//print_r($totalRcvQty_arr);
						$po_breakdown_ids=implode(",",array_unique(explode(",",chop($po_breakdown_ids,','))));
						//$prod_ids=implode(",",array_unique(explode(",",chop($prod_ids,','))));
						//echo $po_sql;//die;

						if($receive_basis==1 || $receive_basis==2)
						{ 
							$rcvRtn_qty_sql = "SELECT b.po_breakdown_id ,sum( b.quantity ) as recv_return_qty from product_details_master a, inv_transaction t, order_wise_pro_details b, wo_po_break_down c, wo_po_details_master d
							where a.id=b.prod_id and a.id=t.prod_id and t.id=b.trans_id and b.po_breakdown_id=c.id and c.job_no_mst=d.job_no and a.id=b.prod_id and  b.po_breakdown_id in ($po_breakdown_ids) and a.item_category_id=4 and b.entry_form in(49) and t.transaction_type=3 and pi_wo_batch_no in($booking_pi_id)  and a.item_group_id='$item_group' $description_cond_rcv and $brand_supref_cond and $item_color_field='$item_color_id' and $gmts_size_field3='$gmts_size_id' and $item_size_cond  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and t.status_active=1 and t.is_deleted=0 and c.status_active=1 and c.is_deleted=0
							group by b.po_breakdown_id";
						}
						else
						{
							$rcvRtn_qty_sql = "SELECT b.po_breakdown_id ,sum( b.quantity ) as recv_return_qty from product_details_master a, inv_transaction t, order_wise_pro_details b, wo_po_break_down c, wo_po_details_master d
							where a.id=b.prod_id and a.id=t.prod_id and t.id=b.trans_id and b.po_breakdown_id=c.id and c.job_no_mst=d.job_no and a.id=b.prod_id and  b.po_breakdown_id in ($po_breakdown_ids)  and a.item_group_id='$item_group' $description_cond_rcv and $brand_supref_cond and $item_color_field='$item_color_id' and $gmts_size_field3='$gmts_size_id' and $item_size_cond  and a.item_category_id=4 and b.entry_form in(49) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  
							group by b.po_breakdown_id";

						} 
						//echo $rcvRtn_qty_sql;
						$totalRcvRtnQty_arr=array();
						$rcvRtn_qtyArray=sql_select($rcvRtn_qty_sql); 
						foreach($rcvRtn_qtyArray as $row)
						{ 
							//[$row[csf('prod_id')]]
							$totalRcvRtnQty_arr[$row[csf('po_breakdown_id')]]=$row[csf('recv_return_qty')];
						}  
						//echo $qty_sql.' ==== '.$rcvRtn_qty_sql ;
						$nameArray=sql_select($po_sql);
						foreach($nameArray as $row)
						{  
							$tot_recv_qnty=$totalRcvQty_arr[$row[csf('id')]];
							$tot_recv_rtn_qnty=$totalRcvRtnQty_arr[$row[csf('id')]];
							//echo $tot_recv_rtn_qnty.'==';
							$tot_recv_qnty=$tot_recv_qnty-$tot_recv_rtn_qnty;
							$all_job .=$row[csf('job_no_prefix_num')].',';
							if ($i%2==0)  
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";
						 	?>
							<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" style="">
								<td width="80">
									<p><? echo $row[csf('po_number')]; ?></p>
									<input type="hidden" name="txtPoId[]" id="txtPoId_<? echo $i; ?>" value="<? echo $row[csf('id')]; ?>">
                                    <input type="hidden" name="txtPoName[]" id="txtPoName_<? echo $i; ?>" value="<? echo $row[csf('po_number')]; ?>">
								</td>
                                <td width="60" style="word-break:break-all"><p><? echo $row[csf('job_no_prefix_num')]; ?></p></td>
                                <td width="65" style="word-break:break-all"><p><? echo $row[csf('int_ref_no')]; ?></p></td>
                                <td width="65" style="word-break:break-all"><P><? echo change_date_format($row[csf('pub_shipment_date')]); ?></P></td>
                                <td width="60" style="word-break:break-all"><P><? echo $row[csf('style_ref')]; ?></P></td>
                                <? 
									if($receive_basis==2 || $wo_pi_basis_id==1) 
									{	
										$tot_wo_qty+=$row[csf('qty')];
										echo "<td width='80' align='right'>".$row[csf('po_quantity')]."</td>";
										echo "<td width='80' align='right' id='woQty_$i'>".number_format($row[csf('qty')],4,'.','')."</td>";
									}
									else 
									{
										echo "<td width='100' align='right'>".$row[csf('po_quantity')]."</td>";
									}
							    ?>
								<td width="80" align="right">
									<? 
									$total_recv_qnty+=$tot_recv_qnty;
									echo number_format($tot_recv_qnty,4); ?>
                                    <input type="hidden" name="txtTotRecvQnty[]" id="txtTotRecvQnty_<? echo $i; ?>" value="<? echo $tot_recv_qnty; ?>">
                                </td>
                                <td width="80" align="right" title="<? echo $row[csf('qty')]."=".$tot_recv_qnty."=".$tot_recv_rtn_qnty; ?>">
									<? 
									$rcv_balance=$row[csf('qty')]-$tot_recv_qnty; //echo $row[csf('qty')].'=='.$tot_recv_qnty.'++'; 
									$total_balance_qnty+=$rcv_balance;
								  	if(number_format($rcv_balance,4,'.','')>0) echo number_format($rcv_balance,4,'.',''); else echo "0.00"; 
								 	?>
									<input type="hidden" name="txtRcvBalance[]" id="txtRcvBalance_<? echo $i; ?>" value="<? echo $rcv_balance; ?>">
								</td>
                                <td width="50" align="center"><? echo $unit_of_measurement[$order_uom]; ?></td>
								<td align="right" width="80" title="0" id="tdRcvQnty_<? echo $i; ?>">
									<input type="text" name="txtRecvQnty[]" id="txtRecvQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:70px" title="<? echo 'dfhbh'; ?>" value="" onKeyUp="calculate_total(<? echo $type;?>);">
								</td>
							</tr>
							<? 
                            $i++; 
						} 
						$all_jobs=implode(",",array_unique(explode(",",chop($all_job,",")))); 
					}
					?>
                    </tbody>
				</table>
                <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="790">
                    <tfoot class="tbl_bottom">
                    	<?
							if($receive_basis==2 || $wo_pi_basis_id==1)
							{
							?>
                            	<td colspan="6">Total</td>
                                <td width="80" id="value_tot_wo_qty"><? echo number_format($tot_wo_qty,4,'.',''); ?></td>
                                <td width="80" id="value_tot_recv_qty"><? echo number_format($total_recv_qnty,4,'.','');?></td>
                                <td width="80" id="value_tot_bal_qty"><? echo number_format($total_balance_qnty,4,'.','');?></td>
                                <td width="50">&nbsp;</td>
                            <?
							}
							else
							{
								?>
                            	<td colspan="7">Total</td>
                            	<?
							}
						?>
                        <td width="82" id="total_recieve"><? echo number_format($tot_trims_receive_qnty,4,'.',''); ?></td>
                    </tfoot>

                </table>
                
                
			</div>
			<table width="790">
				 <tr>
					<td align="center" >
						<input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
					</td>
				</tr>
			</table>
		</div>
		<?
	}
	if($type!=1)
	{
		?>
		</fieldset>
		</form>
		<?
	}
	?>
    </body>
    <script>
	 var tableFilters = 
	 {
		col_operation: {
		   id:["value_tot_wo_qty","value_tot_recv_qty","value_tot_bal_qty"],
		   col:[6,7,8],
		   operation:["sum","sum","sum"],
		   write_method:["innerHTML","innerHTML","innerHTML"]
		}	
	 }
		
	var receive_basis='<?=$receive_basis;?>';
	var wo_pi_basis_id='<?=$wo_pi_basis_id;?>';
	if(receive_basis==2 || wo_pi_basis_id==1){
		setFilterGrid('tbl_list_search',-1,tableFilters);
	}
	else{
		setFilterGrid('tbl_list_search',-1);
	}
		
	</script>          
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    <script type="text/javascript">
    	document.getElementById("job_no_span").textContent=<? echo $all_jobs; ?>;
    </script>
    </html>
    <?
    exit();
}

if($action=="create_po_search_list_view")
{
	$data = explode("_",$data);
	
	$search_string="%".trim($data[0])."%";
	$search_by=$data[1];
	
	if($search_by==1)
		$search_field='b.po_number';
	else if($search_by==2)
		$search_field='a.job_no';
	else
		$search_field='a.style_ref_no';	
		
	$company_id =$data[2];
	$buyer_id =$data[3];
	
	$all_po_id=$data[4];
	
	if($all_po_id!="")
		$po_id_cond=" or b.id in($all_po_id)";
	else 
		$po_id_cond="";
	
	$hidden_po_id=explode(",",$all_po_id);

	if($buyer_id==0) { echo "Please Select Buyer First."; die; }
	
	$sql = "select a.job_no, a.style_ref_no, a.order_uom, b.id, b.po_number, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$company_id and a.buyer_name=$buyer_id and $search_field like '$search_string' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0"; 
	//echo $sql;die; $po_id_cond
	?>
    <div>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table" >
            <thead>
                <th width="40">SL</th>
                <th width="100">Job No</th>
                <th width="110">Style No</th>
                <th width="110">PO No</th>
                <th width="90">PO Quantity</th>
                <th width="50">UOM</th>
                <th>Shipment Date</th>
            </thead>
        </table>
        <div style="width:618px; overflow-y:scroll; max-height:240px;" id="buyer_list_view" align="center">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table" id="tbl_list_search" >
            <?
				$i=1; $po_row_id='';
				$nameArray=sql_select( $sql );
				foreach ($nameArray as $selectResult)
				{
					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";
						
					if(in_array($selectResult[csf('id')],$hidden_po_id)) 
					{
						if($po_row_id=="") $po_row_id=$i; else $po_row_id.=",".$i;
					}
							
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i; ?>)"> 
                        <td width="40" align="center">
                            <? echo $i; ?>
                            <input type="hidden" name="txt_individual_id" id="txt_individual_id<? echo $i ?>" value="<? echo $selectResult[csf('id')]; ?>"/>
                        </td>	
                        <td width="100"><p><? echo $selectResult[csf('job_no')]; ?></p></td>
                        <td width="110"><p><? echo $selectResult[csf('style_ref_no')]; ?></p></td>
                        <td width="110"><p><? echo $selectResult[csf('po_number')]; ?></p></td>
                        <td width="90" align="right"><? echo $selectResult[csf('po_qnty_in_pcs')]; ?></td> 
                        <td width="50" align="center"><p><? echo $unit_of_measurement[$selectResult[csf('order_uom')]]; ?></p></td>
                        <td align="center"><? echo change_date_format($selectResult[csf('pub_shipment_date')]); ?></td>	
                    </tr>
                    <?
                    $i++;
				}
			?>
				<input type="hidden" name="txt_po_row_id" id="txt_po_row_id" value="<? echo $po_row_id; ?>"/>
            </table>
        </div>
         <table width="620" cellspacing="0" cellpadding="0" style="border:none" align="center">
            <tr>
                <td align="center" height="30" valign="bottom">
                    <div style="width:100%"> 
                        <div style="width:50%; float:left" align="left">
                            <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
                        </div>
                        <div style="width:50%; float:left" align="left">
                            <input type="button" name="close" onClick="show_trims_recv();" class="formbutton" value="Close" style="width:100px" />
                        </div>
                    </div>
                </td>
            </tr>
        </table>
	</div>           
<?
	
exit();
}

if($action=="show_ile_load_uom")
{
	$data=explode("_",$data);
	$trim_group_arr =array(); 
	$data_array=sql_select("select id, item_name, order_uom from lib_item_group");
	foreach($data_array as $row)
	{
		$trim_group_arr[$row[csf('id')]]['name']=$row[csf('item_name')];
		$trim_group_arr[$row[csf('id')]]['uom']=$row[csf('order_uom')];
	}
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


if($action=="put_balance_qnty")
{
	$data=explode("__",$data);
	
	$recieve_basis = $data[0];
	$bookingNo_piId = $data[1];
	$bookingNo_piNo = $data[2];
	$item_group = $data[3];
	$item_description = $data[4];
	$brand_supref = $data[5];
	$sensitivity = $data[6];
	$gmts_color_id = $data[7];
	$gmts_size_id = $data[8];
	$item_color_id = $data[9];
	$item_size = $data[10];
	$booking_without_order = $data[11];
	$booking_rate = $data[14];
	if($data[13]==1)
	{
		$rcv_dtls_id = $data[12]; $order_id='';
		$data_array=sql_select("select order_id, order_id_2 from inv_trims_entry_dtls where id='$rcv_dtls_id'");
		$order_id= $data_array[0][csf("order_id")];
		if($data_array[0][csf("order_id_2")]!="") $order_id.=",".$data_array[0][csf("order_id_2")];
	}
	else
	{
		$order_id = $data[12];
	}
	
	if($order_id=="") $order_id=0;
	if($booking_rate>0) $rate_cond=" and ROUND(rate,6)=ROUND($booking_rate,6)";
	// echo $rate_cond;die;
	if($recieve_basis==1 || $recieve_basis==2)
	{
		if($recieve_basis==1)
		{
			
			if($db_type==0)
			{
				$sql="select quantity as qnty, rate, amount as amount from com_pi_item_details where pi_id='$bookingNo_piId' and item_group='$item_group' and trim(REGEXP_REPLACE(item_description, '\s{2,}', ' '))=REGEXP_REPLACE('".trim($item_description)."', '\s{2,}', ' ') and trim(brand_supplier)='".trim($brand_supref)."' and color_id='$gmts_color_id' and item_color='$item_color_id' and size_id='$gmts_size_id' and trim(item_size)='".trim($item_size)."' $rate_cond and status_active=1 and is_deleted=0";
			}
			else
			{
				if($item_size=="") $item_size_cond="item_size is null"; else $item_size_cond=" trim(item_size)='".trim($item_size)."'";
				if($brand_supref=="") $brand_supref_cond="brand_supplier is null"; else $brand_supref_cond="trim(brand_supplier)='".trim($brand_supref)."'";
				if($gmts_size_id=="") $gmts_size_id=0;
				if($item_color_id=="") $item_color_id=0;
				if($gmts_color_id=="") $gmts_color_id=0;
				
				$sql="select sum(quantity) as qnty, avg(rate) as rate, sum(amount) as amount from com_pi_item_details where pi_id='$bookingNo_piId' and item_group='$item_group' and trim(REGEXP_REPLACE(item_description, '\s{2,}', ' '))=REGEXP_REPLACE('".trim($item_description)."', '\s{2,}', ' ') and nvl(color_id,0)='$gmts_color_id' and nvl(item_color,0)='$item_color_id' and nvl(size_id,0)='$gmts_size_id' and $brand_supref_cond and $item_size_cond $rate_cond and status_active=1 and is_deleted=0";
			}
		}
		else if($recieve_basis==2)
		{
			if($db_type==0)
			{
				if($booking_without_order==1)
				{
					$sql = "select trim_qty as qnty, rate, amount as amount from wo_non_ord_samp_booking_dtls where booking_no='$bookingNo_piNo' and trim_group='$item_group' and trim(REGEXP_REPLACE(fabric_description, '\s{2,}', ' '))=REGEXP_REPLACE('".trim($item_description)."', '\s{2,}', ' ') and trim(barnd_sup_ref)='".trim($brand_supref)."' and gmts_color='$gmts_color_id' and gmts_size='$gmts_size_id' and fabric_color='$item_color_id' and trim(item_size)='".trim($item_size)."' $rate_cond and status_active=1 and is_deleted=0";
				}
				else
				{
					if($booking_rate>0) $rate_cond=" and ROUND(c.rate,6)=ROUND($booking_rate,6)";
					$sql="select sum(c.cons) as qnty, avg(c.rate) as rate, sum(c.amount) as amount from wo_booking_dtls b, wo_trim_book_con_dtls c where b.id=c.wo_trim_booking_dtls_id and b.booking_no='$bookingNo_piNo' and b.sensitivity='$sensitivity' and b.trim_group='$item_group' and trim(REGEXP_REPLACE(c.description, '\s{2,}', ' '))=REGEXP_REPLACE('".trim($item_description)."', '\s{2,}', ' ') and trim(c.brand_supplier)='".trim($brand_supref)."' and c.color_number_id='$gmts_color_id' and c.gmts_sizes='$gmts_size_id' and c.item_color='$item_color_id' and trim(c.item_size)='".trim($item_size)."' $rate_cond and b.status_active=1 and b.is_deleted=0 and c.cons>0";
				}
			}
			else
			{
				if($item_size=="")
				{
					$item_size_cond="c.item_size is null"; 
					$item_size_cond_samp="item_size is null";
				}
				else 
				{
					$item_size_cond="trim(c.item_size)='".trim($item_size)."'";
					$item_size_cond_samp="trim(item_size)='".trim($item_size)."'";
				}
				
				if($brand_supref=="") 
				{
					$brand_supref_cond="c.brand_supplier is null"; 
					$brand_supref_cond_samp="barnd_sup_ref is null"; 
				}
				else 
				{
					$brand_supref_cond="trim(c.brand_supplier)='".trim($brand_supref)."'";
					$brand_supref_cond_samp="trim(barnd_sup_ref)='".trim($brand_supref)."'";
				}
				
				if($gmts_size_id=="") $gmts_size_id=0;
				if($item_color_id=="") $item_color_id=0;
				if($gmts_color_id=="") $gmts_color_id=0;
				
				if($booking_without_order==1)
				{
					$sql = "select trim_qty as qnty, rate, amount as amount from wo_non_ord_samp_booking_dtls where booking_no='$bookingNo_piNo' and trim_group='$item_group' and trim(REGEXP_REPLACE(fabric_description, '\s{2,}', ' '))=REGEXP_REPLACE('".trim($item_description)."', '\s{2,}', ' ') and nvl(gmts_color,0)='$gmts_color_id' and nvl(gmts_size,0)='$gmts_size_id' and nvl(fabric_color,0)='$item_color_id' $rate_cond and status_active=1 and is_deleted=0 and $item_size_cond_samp and $brand_supref_cond_samp";
				}
				else
				{
					if($booking_rate>0) $rate_cond=" and ROUND(c.rate,6)=ROUND($booking_rate,6)";
					$sql="select sum(c.cons) as qnty, avg(c.rate) as rate, sum(c.amount) as amount from wo_booking_dtls b, wo_trim_book_con_dtls c where b.id=c.wo_trim_booking_dtls_id and b.booking_no='$bookingNo_piNo' and b.sensitivity='$sensitivity' and b.trim_group='$item_group' and trim(REGEXP_REPLACE(c.description, '\s{2,}', ' '))=REGEXP_REPLACE('".trim($item_description)."', '\s{2,}', ' ') and nvl(c.color_number_id,0)='$gmts_color_id' and nvl(c.gmts_sizes,0)='$gmts_size_id' and nvl(c.item_color,0)='$item_color_id' and $brand_supref_cond and $item_size_cond $rate_cond and b.status_active=1 and b.is_deleted=0 and c.cons>0";
				}
			}
		}
		//echo $sql;die;
		$result=sql_select($sql);
		$qnty=$result[0][csf('qnty')];
		//$rate=$result[0][csf('rate')];
		$rate=$result[0][csf('amount')]/$result[0][csf('qnty')];
		if($recieve_basis==1 || $recieve_basis==2)
		{
			if($booking_rate>0) $rate_cond_rcv=" and ROUND(rate,6)=ROUND($booking_rate,6)";
			if($db_type==0)
			{
				$receive_qnty=return_field_value("sum(b.quantity) as qnty","inv_receive_master m, inv_trims_entry_dtls a, order_wise_pro_details b","m.id=a.mst_id and a.id=b.dtls_id and b.entry_form=24 and m.entry_form=24 and b.po_breakdown_id in($order_id) and m.receive_basis=$recieve_basis and m.booking_id='$bookingNo_piId' and a.item_group_id='$item_group' and trim(REGEXP_REPLACE((case when a.item_description like '%, [BS]' then SUBSTR(a.item_description, 0, LENGTH(a.item_description) - 6) else a.item_description end), '\s{2,}', ' '))=REGEXP_REPLACE('".trim($item_description)."', '\s{2,}', ' ') and trim(a.brand_supplier)='".trim($brand_supref)."' and a.gmts_color_id='$gmts_color_id' and a.item_color='$item_color_id' and a.gmts_size_id ='$item_size_id' and trim(a.item_size)='".trim($item_size)."' and a.status_active=1 and a.is_deleted=0 and m.status_active=1 and m.is_deleted=0 $rate_cond_rcv","qnty");
			}
			else
			{
				if($item_size=="") $item_size_cond="a.item_size is null"; else $item_size_cond="trim(a.item_size)='".trim($item_size)."'";
				if($brand_supref=="") $brand_supref_cond="a.brand_supplier is null"; else $brand_supref_cond="trim(a.brand_supplier)='".trim($brand_supref)."'";
				if($gmts_size_id=="") $gmts_size_id=0;
				if($item_color_id=="") $item_color_id=0;

				if($gmts_color_id=="") $gmts_color_id=0;
				
				/*echo "select sum(a.receive_qnty) as qnty from inv_receive_master m, inv_trims_entry_dtls a where m.id=a.mst_id and m.entry_form=24 and m.receive_basis=$recieve_basis and m.booking_id='$bookingNo_piId' and a.item_group_id='$item_group' and trim(a.item_description)='".trim($item_description)."' and nvl(a.gmts_color_id,0)='$gmts_color_id' and nvl(a.item_color,0)='$item_color_id' and nvl(a.gmts_size_id,0)='$gmts_size_id' and $brand_supref_cond and $item_size_cond and a.status_active=1 and a.is_deleted=0  and m.status_active=1 and m.is_deleted=0 $rate_cond_rcv";die;*/
				$receive_qnty=return_field_value("sum(a.receive_qnty) as qnty","inv_receive_master m, inv_trims_entry_dtls a","m.id=a.mst_id and m.entry_form=24 and m.receive_basis=$recieve_basis and m.booking_id='$bookingNo_piId' and a.item_group_id='$item_group' and trim(REGEXP_REPLACE((case when a.item_description like '%, [BS]' then SUBSTR(a.item_description, 0, LENGTH(a.item_description) - 6) else a.item_description end), '\s{2,}', ' '))=REGEXP_REPLACE('".trim($item_description)."', '\s{2,}', ' ') and nvl(a.gmts_color_id,0)='$gmts_color_id' and nvl(a.item_color,0)='$item_color_id' and nvl(a.gmts_size_id,0)='$gmts_size_id' and $brand_supref_cond and $item_size_cond and a.status_active=1 and a.is_deleted=0  and m.status_active=1 and m.is_deleted=0 $rate_cond_rcv","qnty");
				
			}
			//$chk_next_transaction=return_field_value("id","inv_transaction","transaction_type in(2,3,6) and prod_id=$previous_prod_id and status_active=1 and is_deleted=0 and id >$update_trans_id ","id");
			//echo $rate."=".$qnty."=".$receive_qnty;die;
			$sql_cond_prev="";
			if($item_description!="") $sql_cond_prev.=" and trim(REGEXP_REPLACE((case when c.item_description like '%, [BS]' then SUBSTR(c.item_description, 0, LENGTH(a.item_description) - 6) else c.item_description end), '\s{2,}', ' '))=REGEXP_REPLACE('".trim($item_description)."', '\s{2,}', ' ')";
			if($brand_supref!="") $sql_cond_prev.=" and trim(c.brand_supplier)='".trim($brand_supref)."'";
			if($item_color_id!="") $sql_cond_prev.=" and trim(c.item_color)='".trim($item_color_id)."'";
			if($item_size!="") $sql_cond_prev.=" and trim(c.item_size)='".trim($item_size)."'";
			if($gmts_size_id!="" && $gmts_size_id!=0) $sql_cond_prev_book.=" and trim(c.gmts_size_id)='".trim($gmts_size_id)."'";
			if($gmts_size_id!="" && $gmts_size_id!=0) $sql_cond_prev_prod.=" and trim(c.gmts_size)='".trim($gmts_size_id)."'";
			$gmt_color_rtn_cond="";		
			if($gmts_color_id!="" && $gmts_color_id!=0) $gmt_color_rtn_cond.=" and trim(c.color)='".trim($gmts_color_id)."'";
			
			/*echo $qnty.'**'.$receive_qnty."select b.cons_quantity, d.conversion_factor from inv_transaction b,product_details_master c, lib_item_group d 
			where b.prod_id=c.id and c.item_group_id=d.id and b.transaction_type=3 and b.item_category=4 and c.item_category_id=4 and c.entry_form=24 and b.receive_basis=$recieve_basis and b.pi_wo_batch_no=$bookingNo_piId and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.item_group_id=$item_group $sql_cond_prev $sql_cond_prev_prod $gmt_color_rtn_cond"; die;*/

			$sql_prev_rcv_rtn=sql_select("select b.cons_quantity, d.conversion_factor 
			from inv_transaction b, product_details_master c, lib_item_group d 
			where b.prod_id=c.id and c.item_group_id=d.id and b.transaction_type=3 and b.item_category=4 and c.item_category_id=4 and c.entry_form=24 and b.receive_basis=$recieve_basis and b.pi_wo_batch_no=$bookingNo_piId and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.item_group_id=$item_group $sql_cond_prev $sql_cond_prev_prod $gmt_color_rtn_cond");
			$rcv_rtn_qnty=0;
			foreach($sql_prev_rcv_rtn as $row)
			{
				//$rcv_rtn_qnty+=$row[csf("cons_quantity")]-$row[csf("conversion_factor")];
				$rcv_rtn_qnty+=$row[csf("cons_quantity")];
			}

			$balance_qnty=number_format($qnty,4,'.','')-(number_format($receive_qnty,4,'.','')-number_format($rcv_rtn_qnty,4,'.',''));
		}
		else $balance_qnty=0;
	}
	else
	{
		$balance_qnty='';
		$rate='';
	}
	//echo number_format($qnty,2,'.','')."=".number_format($receive_qnty,2,'.','')."=".number_format($rcv_rtn_qnty,2,'.','');die;
	echo "document.getElementById('txt_bl_qty').value 	= '".number_format($balance_qnty,4,'.','')."';\n";
	echo "document.getElementById('txt_rate').value 	= '".number_format($booking_rate,6,'.','')."';\n";
	echo "document.getElementById('txt_hidden_rate').value 	= '".$booking_rate."';\n";
	exit();	
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$payment_for=str_replace("'", "", $cbo_payment_over_recv);
	
	//$hidden_item_description="'".$hidden_item_description."'";
	//echo "6**".$hidden_sensitivity;die;
	//if($booking_rate>0) $rate_cond=" and ROUND(rate,6)=ROUND($booking_rate,6)";
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0","id","color_name");
	$size_arr = return_library_array("select id, size_name from lib_size","id","size_name");
	if (str_replace("'",'',$update_id) !='')
	{
		$is_audited=return_field_value("is_audited","inv_receive_master","id=".str_replace("'",'',$update_id)." and status_active=1 and is_deleted=0","is_audited");
		if($is_audited==1) {
			echo "40**This MRR is Audited. Save, Update and Delete Not Allowed..";
			die;
		}
	}
	
	$trim_group_arr =array(); 
	$data_array=sql_select("select id, item_name, order_uom from lib_item_group");
	foreach($data_array as $row)
	{
		$trim_group_arr[$row[csf('id')]]['name']=$row[csf('item_name')];
		$trim_group_arr[$row[csf('id')]]['uom']=$row[csf('order_uom')];
	}

	if(str_replace("'","",$txt_hidden_rate)=="" || str_replace("'","",$txt_hidden_rate)==0)
	{
		$txt_hidden_rate = str_replace("'","",$txt_rate);
		$txt_hidden_amount=str_replace("'","",$txt_receive_qnty)*$txt_hidden_rate;
	}
	$hdn_rate=str_replace("'","",$txt_hidden_rate);
	
	$rcv_basis=str_replace("'", "", $cbo_receive_basis);

	if(str_replace("'","",$update_id)!='' && str_replace("'","",$previous_prod_id)!='' && str_replace("'","",$update_trans_id)!='')
	{
		$chk_next_transaction=return_field_value("id","inv_transaction","transaction_type in(2,3,6) and prod_id=$previous_prod_id and status_active=1 and is_deleted=0 and id >$update_trans_id ","id");
		if($chk_next_transaction !="")
		{ 
			echo "18**Update Not allowed.This item is used in another transaction"; disconnect($con);die;
		}
	}
	
	$store_update_upto=str_replace("'","",$store_update_upto);
	if($store_update_upto > 1)
	{
		$cbo_floor=str_replace("'","",$cbo_floor);
		$cbo_room=str_replace("'","",$cbo_room);
		$txt_rack=str_replace("'","",$txt_rack);
		$txt_shelf=str_replace("'","",$txt_shelf);
		$cbo_bin=str_replace("'","",$cbo_bin);
		if($store_update_upto==2)
		{
			$cbo_room=0;
			$txt_rack=0;
			$txt_shelf=0;
			$cbo_bin=0;
		}
		else if($store_update_upto==3)
		{
			$txt_rack=0;
			$txt_shelf=0;
			$cbo_bin=0;
		}
		else if($store_update_upto==4)
		{
			$txt_shelf=0;
			$cbo_bin=0;
		}
		else if($store_update_upto==5)
		{
			$cbo_bin=0;
		}
	}
	else
	{
		$cbo_floor=0;
		$cbo_room=0;
		$txt_rack=0;
		$txt_shelf=0;
		$cbo_bin=0;
	}

	$wobookingQnty=0;
	if($rcv_basis==1 || $rcv_basis==2)
	{
		$variable_set_invent = sql_select("select category,over_rcv_status,over_rcv_percent,over_rcv_payment from variable_inv_ile_standard where company_name=$cbo_company_id and variable_list=23 and category =4 order by id");
		$over_receive_limit = !empty($variable_set_invent) ? $variable_set_invent[0][csf('over_rcv_percent')] : 0;
		
		$up_trans_id=str_replace("'","",$update_trans_id);
		$up_cond="";
		if($up_trans_id!="") $up_cond=" and b.id <> $up_trans_id";
		
		$sql_cond_prev="";
		if(str_replace("'","",$hidden_item_description)!="") $sql_cond_prev.=" and trim(REGEXP_REPLACE((case when c.item_description like '%, [BS]' then SUBSTR(c.item_description, 0, LENGTH(a.item_description) - 6) else c.item_description end), '\s{2,}', ' '))=REGEXP_REPLACE('".trim(str_replace("'","",$hidden_item_description))."', '\s{2,}', ' ')"; else $sql_cond_prev.=" and c.item_description is null";
		if(str_replace("'","",$txt_brand_supref)!="") $sql_cond_prev.=" and trim(c.brand_supplier)='".trim(str_replace("'","",$txt_brand_supref))."'"; else $sql_cond_prev.=" and c.brand_supplier is null";
		if(str_replace("'","",$txt_item_color_id)!="") $sql_cond_prev.=" and trim(c.item_color)='".trim(str_replace("'","",$txt_item_color_id))."'"; else $sql_cond_prev.=" and c.item_color is null";
		if(str_replace("'","",$txt_item_size)!="") $sql_cond_prev.=" and trim(c.item_size)='".trim(str_replace("'","",$txt_item_size))."'"; else $sql_cond_prev.=" and c.item_size is null";
		if(str_replace("'","",$txt_gmts_size_id)!="") $sql_cond_prev_book.=" and trim(c.gmts_size_id)='".trim(str_replace("'","",$txt_gmts_size_id))."'"; else $sql_cond_prev_book.=" and c.gmts_size_id is null";
		if(str_replace("'","",$txt_gmts_size_id)!="") $sql_cond_prev_prod.=" and trim(c.gmts_size)='".trim(str_replace("'","",$txt_gmts_size_id))."'"; else $sql_cond_prev_prod.=" and c.gmts_size is null";
		
		$gmt_color_rtn_cond="";		
		if(str_replace("'","",$txt_gmts_color_id)!="") $gmt_color_rtn_cond.=" and trim(c.color)='".trim(str_replace("'","",$txt_gmts_color_id))."'"; else $gmt_color_rtn_cond.=" and c.color is null";

		$sql_prev_rcv_rtn=sql_select("select b.cons_quantity, d.conversion_factor 
		from inv_transaction b, product_details_master c, lib_item_group d 
		where b.prod_id=c.id and c.item_group_id=d.id and b.transaction_type=3 and b.item_category=4 and c.item_category_id=4 and c.entry_form=24 and b.receive_basis=$rcv_basis and b.pi_wo_batch_no=$txt_booking_pi_id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.item_group_id=$cbo_item_group $sql_cond_prev $sql_cond_prev_prod $gmt_color_rtn_cond");
		$rcv_rtn_qnty=0;
		foreach($sql_prev_rcv_rtn as $row)
		{
			$rcv_rtn_qnty+=$row[csf("cons_quantity")]/$row[csf("conversion_factor")];
		}

		$gmt_color_rcv_cond="";		
		if(str_replace("'","",$txt_gmts_color_id)!="") $gmt_color_rcv_cond.=" and trim(c.gmts_color_id)='".trim(str_replace("'","",$txt_gmts_color_id))."'"; $gmt_color_rcv_cond.=" and c.gmts_color_id is null"; 
		if($hdn_rate>0) $rate_cond=" and ROUND(rate,6)=ROUND($hdn_rate,6)";
		$sql_prev_rcv=sql_select("select sum(b.order_qnty) as rcv_qnty 
		from inv_transaction b, inv_trims_entry_dtls c 
		where b.id=c.trans_id and b.receive_basis=$rcv_basis and b.pi_wo_batch_no=$txt_booking_pi_id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.item_group_id=$cbo_item_group $sql_cond_prev $sql_cond_prev_book $gmt_color_rcv_cond $rate_cond $up_cond");
		$prev_rcv=0;
		if ($sql_prev_rcv[0][csf("rcv_qnty")]!=""){
			$prev_rcv=$sql_prev_rcv[0][csf("rcv_qnty")]-$rcv_rtn_qnty;
		}
		
		if($rcv_basis==2)
		{
			if(str_replace("'","",$booking_without_order)==1)
			{
				$sql_cond_wo="";
				if(str_replace("'","",$hidden_item_description)!="") $sql_cond_wo.=" and trim(c.fabric_description)='".trim(str_replace("'","",$hidden_item_description))."'"; else $sql_cond_wo.=" and c.fabric_description is null";
				if(str_replace("'","",$txt_brand_supref)!="") $sql_cond_wo.=" and trim(c.barnd_sup_ref)='".trim(str_replace("'","",$txt_brand_supref))."'"; else $sql_cond_wo.=" and c.barnd_sup_ref is null";
				if(str_replace("'","",$txt_item_color_id)!="") $sql_cond_wo.=" and c.fabric_color=$txt_item_color_id"; else $sql_cond_wo.=" and c.fabric_color is null";
				if(str_replace("'","",$txt_gmts_color_id)!="") $sql_cond_wo.=" and c.gmts_color=$txt_gmts_color_id "; else $sql_cond_wo.=" and c.gmts_color is null";
				if(str_replace("'","",$txt_item_size)!="") $sql_cond_wo.=" and trim(c.item_size)='".trim(str_replace("'","",$txt_item_size))."'"; else $sql_cond_wo.=" and c.item_size is null";
				if(str_replace("'","",$txt_gmts_size_id)!="") $sql_cond_wo.=" and trim(c.gmts_size)='".trim(str_replace("'","",$txt_gmts_size_id))."'"; else $sql_cond_wo.=" and c.gmts_size is null";
				
				$booking_pi_sql=sql_select("select sum(c.trim_qty) as booking_pi_qnty from wo_non_ord_samp_booking_mst b, wo_non_ord_samp_booking_dtls c
				where b.booking_no=c.booking_no and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.booking_no=$txt_booking_pi_no and c.booking_no=$txt_booking_pi_no and c.trim_group=$cbo_item_group $sql_cond_wo");
			}
			else
			{
				$sql_cond_wo="";
				if(str_replace("'","",$hidden_item_description)!="") $sql_cond_wo.=" and trim(REGEXP_REPLACE(c.description, '\s{2,}', ' '))=REGEXP_REPLACE('".trim(str_replace("'","",$hidden_item_description))."', '\s{2,}', ' ')";
				if(str_replace("'","",$txt_brand_supref)!="") $sql_cond_wo.=" and trim(c.brand_supplier)='".trim(str_replace("'","",$txt_brand_supref))."'"; else $sql_cond_wo.=" and c.brand_supplier is null";
				if(str_replace("'","",$txt_item_color_id)!="") $sql_cond_wo.=" and c.item_color=$txt_item_color_id"; else $sql_cond_wo.=" and c.item_color is null";
				if(str_replace("'","",$txt_gmts_color_id)!="") $sql_cond_wo.=" and c.color_number_id=$txt_gmts_color_id "; else $sql_cond_wo.=" and c.color_number_id is null";
				if(str_replace("'","",$txt_item_size)!="") $sql_cond_wo.=" and trim(c.item_size)='".trim(str_replace("'","",$txt_item_size))."'";  else $sql_cond_wo.=" and c.item_size is null";
				if(str_replace("'","",$txt_gmts_size_id)!="") $sql_cond_wo.=" and trim(c.gmts_sizes)='".trim(str_replace("'","",$txt_gmts_size_id))."'"; else $sql_cond_wo.=" and c.gmts_sizes is null";
				if($hdn_rate>0) $rate_cond_book=" and ROUND(c.rate,6)=ROUND($hdn_rate,6)";
				$booking_pi_sql=sql_select(" select sum(c.cons) as booking_pi_qnty from wo_booking_dtls b, wo_trim_book_con_dtls c
				where b.id=c.wo_trim_booking_dtls_id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.booking_no=$txt_booking_pi_no and c.booking_no=$txt_booking_pi_no and b.trim_group=$cbo_item_group $rate_cond_book $sql_cond_wo");
			}
		}
		else
		{
			$sql_cond_pi="";
			if(str_replace("'","",$hidden_item_description)!="") $sql_cond_pi.=" and trim(REGEXP_REPLACE(item_description, '\s{2,}', ' '))=REGEXP_REPLACE('".trim(str_replace("'","",$hidden_item_description))."', '\s{2,}', ' ')"; else $sql_cond_pi.=" and item_description is null";
			if(str_replace("'","",$txt_brand_supref)!="") $sql_cond_pi.=" and trim(brand_supplier)='".trim(str_replace("'","",$txt_brand_supref))."'"; else $sql_cond_pi.=" and brand_supplier is null";
			if(str_replace("'","",$txt_item_color_id)!="") $sql_cond_pi.=" and item_color=$txt_item_color_id "; else $sql_cond_pi.=" and item_color is null";
			if(str_replace("'","",$txt_gmts_color_id)!="" ) $sql_cond_pi.=" and color_id=$txt_gmts_color_id "; else $sql_cond_pi.=" and color_id is null";
			if(str_replace("'","",$txt_item_size)!="") $sql_cond_pi.=" and trim(item_size)='".trim(str_replace("'","",$txt_item_size))."'";  else $sql_cond_pi.=" and item_size is null";
			if(str_replace("'","",$txt_gmts_size_id)!="" ) $sql_cond_pi.=" and trim(size_id)='".trim(str_replace("'","",$txt_gmts_size_id))."'"; else $sql_cond_pi.=" and size_id is null";
			
			$booking_pi_sql=sql_select("select sum(quantity) as booking_pi_qnty from com_pi_item_details
			where pi_id=$txt_booking_pi_id and status_active=1 and is_deleted=0 and item_group=$cbo_item_group $rate_cond $sql_cond_pi");
		}
		if ($booking_pi_sql[0][csf("booking_pi_qnty")]!=""){
			$wobookingQnty=$booking_pi_sql[0][csf("booking_pi_qnty")];
		}
	}
	
	if ($operation==0)  // Insert Here
	{ 
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		if($rcv_basis==1 || $rcv_basis==2)
		{
			$txt_receive_qty = str_replace("'", "", $txt_receive_qnty);
			$total_recvQnty=$prev_rcv+$txt_receive_qty;
			$woYarnQnty=$wobookingQnty;
			$over_receive_limit_qnty = ($over_receive_limit>0)?($over_receive_limit / 100) * $woYarnQnty:0;			
			$allow_total_val = $woYarnQnty + $over_receive_limit_qnty;
			$overRecvLimitMsg="Over Receive limit = $over_receive_limit% ($over_receive_limit_qnty Kg.)";
			if($allow_total_val<$total_recvQnty && $payment_for==0) 
			{
				$over_msg = ($over_receive_limit>0)?"\nAllowed Quantity = $allow_total_val":"";
				echo "40**Recv. quantity can not be greater than WO/Booking quantity.\n\nWO/Booking quantity = $woYarnQnty \n$overRecvLimitMsg $over_msg";
				disconnect($con);die;
			}
		}
		
		$trims_recv_num=''; $trims_update_id='';
		if(str_replace("'","",$update_id)=="")
		{
			if($db_type==0) $year_cond="YEAR(insert_date)"; 
			else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
			else $year_cond="";//defined Later
			$id = return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master", $con);
			
			$new_trims_recv_system_id = explode("*", return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master",$con,1,$cbo_company_id,'TRE',24,date("Y",time()) ));
		 	
			$field_array="id, recv_number_prefix, recv_number_prefix_num, recv_number, entry_form, item_category, receive_basis, company_id, receive_date, challan_no, challan_date, booking_id, booking_no, booking_without_order, store_id, boe_mushak_challan_no, boe_mushak_challan_date, lc_no, source, knitting_source, supplier_id, currency_id, exchange_rate, fabric_source, variable_setting, inserted_by, insert_date";
			
			$data_array="(".$id.",'".$new_trims_recv_system_id[1]."',".$new_trims_recv_system_id[2].",'".$new_trims_recv_system_id[0]."',24,4,".$cbo_receive_basis.",".$cbo_company_id.",".$txt_receive_date.",".$txt_receive_chal_no.",".$txt_challan_date.",".$txt_booking_pi_id.",".$txt_booking_pi_no.",".$booking_without_order.",".$cbo_store_name.",".$txt_boe_mushak_challan_no.",".$txt_boe_mushak_challan_date.",".$lc_id.",".$cbo_source.",".$suplier_type.",".$cbo_supplier_name.",".$cbo_currency_id.",".$txt_exchange_rate.",".$meterial_source.",".$variable_string_inventory.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			$trims_recv_num=$new_trims_recv_system_id[0];
			$trims_update_id=$id;
		}
		else
		{
			$original_receive_basis=sql_select(" select receive_basis,booking_id from inv_receive_master where id=$update_id");
			if(str_replace("'","",$cbo_receive_basis)!=$original_receive_basis[0][csf('receive_basis')])
			 {
				echo "40**Multiple Receive Basis Not Allow In Same Received ID";disconnect($con);die;
			 }
			 else
			 {
				if(str_replace("'","",$cbo_receive_basis)==1 || str_replace("'","",$cbo_receive_basis)==2) 
				{
					if(str_replace("'","",$txt_booking_pi_id)!=$original_receive_basis[0][csf('booking_id')])
					{
						echo "40**Multiple WO/PI Not Allow In Same Received ID";disconnect($con);die;
					}
				} 
			 }
			
			$field_array_update="receive_basis*receive_date*challan_no*challan_date*booking_id*booking_no*booking_without_order*store_id*boe_mushak_challan_no*boe_mushak_challan_date*lc_no*source*knitting_source*supplier_id*currency_id*exchange_rate*fabric_source*updated_by*update_date";
			
			$data_array_update=$cbo_receive_basis."*".$txt_receive_date."*".$txt_receive_chal_no."*".$txt_challan_date."*".$txt_booking_pi_id."*".$txt_booking_pi_no."*".$booking_without_order."*".$cbo_store_name."*".$txt_boe_mushak_challan_no."*".$txt_boe_mushak_challan_date."*".$lc_id."*".$cbo_source."*".$suplier_type."*".$cbo_supplier_name."*".$cbo_currency_id."*".$txt_exchange_rate."*".$meterial_source."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			
			$trims_recv_num=str_replace("'","",$txt_recieved_id);
			$trims_update_id=str_replace("'","",$update_id);
		}
		
		//details table entry here START-----------------------------------//		

		$ile=return_field_value("standard","variable_inv_ile_standard","source=$cbo_source and company_name=$cbo_company_id and category=4 and status_active=1 and is_deleted=0");
		$ile_cost = str_replace("'","",$txt_ile);
		
		if($db_type==0)
		{
			$concattS = explode(",",return_field_value("concat(trim_uom,',',conversion_factor)","lib_item_group","id=$cbo_item_group")); 
		}
		else
		{
			$concattS = explode(",",return_field_value("trim_uom || ',' || conversion_factor as data","lib_item_group","id=$cbo_item_group","data")); 
		}
		
		$cons_uom = $concattS[0];
		$conversion_factor = $concattS[1];
		
		$exchange_rate = str_replace("'","",$txt_exchange_rate);
		$txt_receive_qnty = str_replace("'","",$txt_receive_qnty);
		$con_qnty = $conversion_factor*$txt_receive_qnty;
		
		$rate = str_replace("'","",$txt_hidden_rate);
		$domestic_rate = return_domestic_rate($rate,$ile_cost,$exchange_rate,$conversion_factor);
		$cons_rate = number_format($domestic_rate,$dec_place[3],".","");
		$con_amount = $cons_rate*$con_qnty;
		$con_ile = $ile/$conversion_factor;
		$con_ile_cost = ($con_ile/100)*$cons_rate;
		
		$item_desc=''; $gmts_color_id=0; $gmts_size_id=0; 
		
		if(str_replace("'","",$cbo_receive_basis)==1 || str_replace("'","",$cbo_receive_basis)==2)
		{
			$item_desc=$hidden_item_description;
			$gmts_color_id=str_replace("'","",$txt_gmts_color_id);
			$gmts_size_id=str_replace("'","",$txt_gmts_size_id);
		}
		else 
		{
			$item_desc=$txt_item_description;
			
			if(str_replace("'","",$txt_gmts_color)!="")
			{ 
				if (!in_array(str_replace("'","",$txt_gmts_color),$new_array_color))
				{
					$gmts_color_id = return_id( str_replace("'","",$txt_gmts_color), $color_arr, "lib_color", "id,color_name","24");  
					$new_array_color[$gmts_color_id]=str_replace("'","",$txt_gmts_color);
				}
				else $gmts_color_id =  array_search(str_replace("'","",$txt_gmts_color), $new_array_color); 
			}
			else
			{
				$gmts_color_id=0;
			}
			
			if(str_replace("'","",$txt_item_color)!="")
			{ 
				if (!in_array(str_replace("'","",$txt_item_color),$new_array_color))
				{
					$txt_item_color_id = return_id( str_replace("'","",$txt_item_color), $color_arr, "lib_color", "id,color_name","24");  
					$new_array_color[$txt_item_color_id]=str_replace("'","",$txt_item_color);
				}
				else $txt_item_color_id =  array_search(str_replace("'","",$txt_item_color), $new_array_color); 
			}
			else
			{
				$txt_item_color_id=0;
			}
			
			if(str_replace("'","",$txt_gmts_size)!="")
			{
				if (!in_array(str_replace("'","",$txt_gmts_size),$new_array_size))
				{
				  $gmts_size_id = return_id( str_replace("'","",$txt_gmts_size), $size_arr, "lib_size", "id,size_name","24");  
				  $new_array_size[$gmts_size_id]=str_replace("'","",$txt_gmts_size);
				}
				else $gmts_size_id =  array_search(str_replace("'","",$txt_gmts_size), $new_array_size); 
			}
			else
			{
				$gmts_size_id=0;
			}
		}
		
		
		
		if(str_replace("'","",$txt_item_size)!="")
		{
			$item_size_cond="item_size=$txt_item_size";
		}
		else 
		{
			if($db_type==0)
			{
				$item_size_cond="item_size=''";
			}
			else
			{
				$item_size_cond="item_size is null";
			}
		}
		
		if(str_replace("'","",$txt_brand_supref)!="")
		{
			$brand_supref_cond="brand_supplier=$txt_brand_supref";
		}
		else 
		{
			if($db_type==0)
			{
				$brand_supref_cond="brand_supplier=''";
			}
			else
			{
				$brand_supref_cond="brand_supplier is null";
			}
			
		}
		
		if($gmts_size_id=="") $gmts_size_id=0;
		if($gmts_color_id=="") $gmts_color_id=0;
		if(str_replace("'","",$txt_item_color_id)=="") $txt_item_color_id=0; 
		$meterial_source=str_replace("'","",$meterial_source);
		$is_buyer_supplied = ($meterial_source==3)?1 : 0;
		$buyer_supplied = ($meterial_source==3)?", [BS]" : "";
		if($db_type==2)
		{
			$item_descrip=str_replace("'","",$item_desc);
			$item_descrip=str_replace("(","[",$item_descrip);
			$item_descrip=str_replace(")","]",$item_descrip);
		}
		else
		{
			$item_descrip=str_replace("'","",$item_desc);
		}
		$item_descrip=$item_descrip.$buyer_supplied;
		$row_prod=sql_select( "select id, current_stock, avg_rate_per_unit, stock_value from product_details_master where company_id=$cbo_company_id and item_category_id=4 and entry_form=24 and item_group_id=$cbo_item_group and trim(REGEXP_REPLACE((case when item_description like '%, [BS]' then SUBSTR(item_description, 0, LENGTH(item_description) - 6) else item_description end), '\s{2,}', ' '))=REGEXP_REPLACE('$item_descrip', '\s{2,}', ' ') and $brand_supref_cond and color='$gmts_color_id' and item_color=$txt_item_color_id and gmts_size='$gmts_size_id' and $item_size_cond and status_active=1 and is_deleted=0" );
		//echo "10**".print_r($row_prod);die;
		
		if(count($row_prod)>0)
		{
			$prod_id=$row_prod[0][csf('id')];

			/*if(str_replace("'","",$update_id)!='')
			{
				//echo "10**select id from inv_transaction where transaction_type in(2,3,6) and prod_id=$previous_prod_id and status_active=1 and is_deleted=0 and id >$update_trans_id  "; die;
				$chk_duplicate_prod_id=return_field_value("id","inv_transaction","transaction_type in(1) and prod_id=$prod_id and status_active=1 and is_deleted=0 and mst_id =$update_id ","id");
				if($chk_duplicate_prod_id !="")
				{ 
					echo "18**Duplicate Product not allowed in Same Received ID"; disconnect($con);die;
				}
			}
           */
			$curr_stock_qnty=$row_prod[0][csf('current_stock')]+$con_qnty;
			$curr_stock_value=$avg_rate_per_unit=0;
			if ($curr_stock_qnty != 0){
				$curr_stock_value=$row_prod[0][csf('stock_value')]+$con_amount;
				$avg_rate_per_unit=number_format($curr_stock_value/$curr_stock_qnty,$dec_place[3],'.','');
			}			
			
			$field_array_prod_update="store_id*avg_rate_per_unit*last_purchased_qnty*current_stock*stock_value*updated_by*update_date";
			$data_array_prod_update=$cbo_store_name."*".$avg_rate_per_unit."*".$con_qnty."*".$curr_stock_qnty."*".$curr_stock_value."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			
		}
		else
		{
			//$prod_id=return_next_id( "id", "product_details_master", 1 ) ;
			$prod_id = return_next_id_by_sequence("PRODUCT_DETAILS_MASTER_PK_SEQ", "product_details_master", $con);
			$item_name =$trim_group_arr[str_replace("'","",$cbo_item_group)]['name'];
			$prod_name_dtls=$item_name.", ".trim(str_replace("'","",$txt_item_description)).$buyer_supplied;
			
			if($db_type==2)
			{
				$prod_name_dtls=str_replace("(","[",$prod_name_dtls);
				$prod_name_dtls=str_replace(")","]",$prod_name_dtls);
			}
			
			$field_array_prod="id, company_id, store_id, item_category_id, entry_form, item_group_id, item_description, product_name_details, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, color, item_color, gmts_size, item_size, brand_supplier, is_buyer_supplied, inserted_by, insert_date";
			//echo "5**$item_desc==$prod_name_dtls";die;
			$data_array_prod="(".$prod_id.",".$cbo_company_id.",".$cbo_store_name.",4,24,".$cbo_item_group.",'".$item_descrip."','".$prod_name_dtls."',".$cons_uom.",".$cons_rate.",".$con_qnty.",".$con_qnty.",".$con_amount.",'".$gmts_color_id."',".$txt_item_color_id.",'".$gmts_size_id."',".$txt_item_size.",".$txt_brand_supref.",".$is_buyer_supplied.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
		}
		
		$max_issue_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$prod_id and store_id=$cbo_store_name and status_active=1", "max_date");       
		if($max_issue_date !="")
		{
			$max_issue_date = date("Y-m-d", strtotime($max_issue_date));
			$receive_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_receive_date)));

			if ($receive_date < $max_issue_date) 
			{
				echo "20**Receive Date Can not Be Less Than Last Transaction Date Of This Item";disconnect($con);
				die;
			}
		}
		//echo "10**nn".die;
		//$id_trans=return_next_id( "id", "inv_transaction", 1 ) ;
		$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
		$field_array_trans="id, mst_id, receive_basis, pi_wo_batch_no, booking_no, booking_without_order, company_id, supplier_id, prod_id, item_category, transaction_type, transaction_date, store_id, order_uom, order_qnty, order_rate, order_amount, order_ile, order_ile_cost, cons_uom, cons_quantity, cons_rate, cons_amount, cons_ile, cons_ile_cost, balance_qnty, balance_amount, payment_over_recv,floor_id,room,rack,self,bin_box, inserted_by, insert_date";
		
		$data_array_trans="(".$id_trans.",".$trims_update_id.",".$cbo_receive_basis.",".$txt_booking_pi_id.",".$txt_booking_pi_no.",".$booking_without_order.",".$cbo_company_id.",".$cbo_supplier_name.",".$prod_id.",4,1,".$txt_receive_date.",".$cbo_store_name.",".$cbo_uom.",".$txt_receive_qnty.",".$rate.",".$txt_hidden_amount.",'".$ile."','".$ile_cost."',".$cons_uom.",".$con_qnty.",".$cons_rate.",".$con_amount.",".$con_ile.",".$con_ile_cost.",".$con_qnty.",".$con_amount.",".$cbo_payment_over_recv.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_bin.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		
		if($db_type==2)
		{
			$txt_item_description=str_replace("(","[",$txt_item_description);
			$txt_item_description=str_replace(")","]",$txt_item_description);
		}
		$save_data=str_replace("'","",$save_data);
		$tot_string=strlen($save_data);
		$count_loop=ceil($tot_string/3900);
		$first_save_data=''; $second_save_data=''; $theRest_save_data='';$count=0; $interval=3900;
		for($i=1;$i<=$count_loop; $i++)
		{
		    if($count_loop>0 && $i==1) $first_save_data=substr($save_data, $count, $interval);
		    if($count_loop>1 && $i==2) $second_save_data=substr($save_data, $count, $interval);
		    if($count_loop>2 && $i==3) $theRest_save_data=substr($save_data, $count, $interval);
		    $count+=3900;
		}
		
		$all_po_ids=str_replace("'","",$all_po_id);
		$tot_po_ids=strlen($all_po_ids);
		$count_ord_loops=ceil($tot_po_ids/3900);
		//echo "10**".$tot_po_ids."<br>". $count_ord_loops;die;
		$first_order_ids=''; $second_order_ids=''; $count_ord=0; $interval_ord=3900;
		for($i=1;$i<=$count_ord_loops; $i++)
		{
		    if($count_ord_loops>0 && $i==1) $first_order_ids=substr($all_po_ids, $count_ord, $interval_ord);
		    if($count_ord_loops>1 && $i==2) $second_order_ids=substr($all_po_ids, $count_ord, $interval_ord);
		    $count_ord+=$interval_ord;
		}
		//echo "10**".$first_order_ids."<br>". $second_order_ids;die;
		//echo "10**".strlen($save_data); die;
		
		//$id_dtls=return_next_id( "id", "inv_trims_entry_dtls", 1 ) ;
		$id_dtls = return_next_id_by_sequence("INV_TRIMS_ENTRY_DTLS_PK_SEQ", "inv_trims_entry_dtls", $con);
		$field_array_dtls="id, mst_id, trans_id, booking_id, booking_no, booking_without_order, prod_id, item_group_id, item_description, brand_supplier, order_uom, order_id, order_id_2, receive_qnty, reject_receive_qnty, rate, amount, ile, ile_cost, gmts_color_id, item_color, gmts_size_id, item_size, save_string, save_string_2, save_string_3, item_description_color_size, cons_uom, cons_qnty, cons_rate, cons_ile, cons_ile_cost, book_keeping_curr, sensitivity, payment_over_recv,floor,room_no,rack_no,self_no,box_bin_no,remarks,inserted_by, insert_date";
		
		$data_array_dtls="(".$id_dtls.",".$trims_update_id.",".$id_trans.",".$txt_booking_pi_id.",".$txt_booking_pi_no.",".$booking_without_order.",".$prod_id.",".$cbo_item_group.",".$item_desc.",".$txt_brand_supref.",".$cbo_uom.",'".$first_order_ids."','".$second_order_ids."',".$txt_receive_qnty.",".$txt_reject_recv_qnty.",".$rate.",".$txt_hidden_amount.",'".$ile."',".$ile_cost.",'".$gmts_color_id."',".$txt_item_color_id.",'".$gmts_size_id."',".$txt_item_size.",'".$first_save_data."','".$second_save_data."','".$theRest_save_data."',".$txt_item_description.",".$cons_uom.",".$con_qnty.",".$cons_rate.",".$con_ile.",".$con_ile_cost.",".$con_amount.",".$hidden_sensitivity.",".$cbo_payment_over_recv.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_bin.",".$txt_remarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		

		$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, quantity, order_rate, order_amount, inserted_by, insert_date";
		$field_array_ord_prod="id, company_id, category_id, prod_id, po_breakdown_id, stock_quantity, last_rcv_qnty, avg_rate, stock_amount, inserted_by, insert_date";
		$field_array_ord_prod_update="avg_rate*last_rcv_qnty*stock_quantity*stock_amount*updated_by*update_date";
		//$id_prop = return_next_id( "id", "order_wise_pro_details", 1 ); $rate
		if(str_replace("'","",$save_data)!="")
		{
			$save_data=explode(",",str_replace("'","",$save_data));$data_array_ord_prod="";
			for($i=0;$i<count($save_data);$i++)
			{
				$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
				$order_dtls=explode("_",$save_data[$i]);
				
				$order_id=$order_dtls[0];
				$order_qnty=$order_dtls[1];
				$order_amount=$order_qnty*$rate;
				
				if($i==0) $add_comma=""; else $add_comma=",";
				
				$data_array_prop.="$add_comma(".$id_prop.",".$id_trans.",1,24,".$id_dtls.",".$order_id.",".$prod_id.",".$order_qnty.",'".$rate."','".$order_amount."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				//$id_prop = $id_prop+1;
			}
		}
		
		$rID=$rID2=$rID3=$rID6=$rID4=$rID6=true;	
		//echo "10** insert into inv_receive_master (".$field_array.") values ".$data_array;oci_rollback($con);disconnect($con);die;
		if(str_replace("'","",$update_id)=="")
		{
			$rID=sql_insert("inv_receive_master",$field_array,$data_array,0);
		}
		else
		{
			$rID=sql_update("inv_receive_master",$field_array_update,$data_array_update,"id",$update_id,1);
		}
		
		if(count($row_prod)>0)
		{
			$rID2=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$prod_id,0);
		}
		else
		{
			//echo "5**insert into product_details_master (".$field_array_prod.") values ".$data_array_prod;die;
			$rID2=sql_insert("product_details_master",$field_array_prod,$data_array_prod,0);
		}
		
		//echo "5**insert into inv_transaction (".$field_array_trans.") values ".$data_array_trans;die;
		$rID3=sql_insert("inv_transaction",$field_array_trans,$data_array_trans,0);
		
		//echo "6**insert into inv_trims_entry_dtls (".$field_array_dtls.") values ".$data_array_dtls;oci_rollback($con);die;
		$rID4=sql_insert("inv_trims_entry_dtls",$field_array_dtls,$data_array_dtls,0);

		//echo "insert into order_wise_pro_details (".$field_array_proportionate.") values ".$data_array_prop;die;
		if($data_array_prop!="" && str_replace("'","",$booking_without_order)!=1)
		{
			$rID6=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,0);
		}
		
		//echo "5** $rID=$rID2=$rID3=$rID6=$rID4=$rID6";oci_rollback($con);disconnect($con);die;		
		//check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID && $rID2 && $rID3 && $rID6 && $rID4 && $rID6)
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
			if($rID && $rID2 && $rID3 && $rID6 && $rID4 && $rID6)
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
		
		if($rcv_basis==1 || $rcv_basis==2)
		{
			$txt_receive_qty = str_replace("'", "", $txt_receive_qnty);
			
			//echo "mahbub".$txt_receive_qty; die;
			$total_recvQnty=$prev_rcv+$txt_receive_qty;
			$woYarnQnty=$wobookingQnty;
			$over_receive_limit_qnty = ($over_receive_limit>0)?($over_receive_limit / 100) * $woYarnQnty:0;			
			$allow_total_val = $woYarnQnty + $over_receive_limit_qnty;
			$overRecvLimitMsg="Over Receive limit = $over_receive_limit% ($over_receive_limit_qnty Kg.)";
			if($allow_total_val<$total_recvQnty && $payment_for==0) 
			{
				$over_msg = ($over_receive_limit>0)?"\nAllowed Quantity = $allow_total_val":"";
				echo "40**Recv. quantity can not be greater than WO/Booking quantity.\n\nWO/Booking quantity = $woYarnQnty \n$overRecvLimitMsg $over_msg";
				disconnect($con);die;
			}
		}
		
		
		//	if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}   
	   $original_receive_basis=sql_select("select a.id,a.receive_basis,a.booking_id,count(b.id) as dtls_row from inv_receive_master a,inv_trims_entry_dtls b where a.id=b.mst_id and a.id=$update_id and b.id!=$update_dtls_id group by a.id,a.receive_basis,a.booking_id");
	   if($original_receive_basis[0][csf('dtls_row')]>0)
	   {
			if(str_replace("'","",$cbo_receive_basis)!=$original_receive_basis[0][csf('receive_basis')])
			 {
				echo "40**Multiple Receive Basis Not Allow In Same Received ID";disconnect($con);die;
			 }
			 else
			 {
				if(str_replace("'","",$cbo_receive_basis)==1 || str_replace("'","",$cbo_receive_basis)==2) 
				{
					if(str_replace("'","",$txt_booking_pi_id)!=$original_receive_basis[0][csf('booking_id')])
					{
						echo "40**Multiple WO/PI Not Allow In Same Received ID";disconnect($con);die;
					}
				} 
			 }
	    }
		$item_desc=''; $gmts_color_id=0; $gmts_size_id=0; 
		
		//echo "6**".$hidden_item_description;die;
		
		if(str_replace("'","",$cbo_receive_basis)==1 || str_replace("'","",$cbo_receive_basis)==2)
		{
			$item_desc=$hidden_item_description;
			$gmts_color_id=str_replace("'","",$txt_gmts_color_id);
			$gmts_size_id=str_replace("'","",$txt_gmts_size_id);
		}
		else 
		{
			$item_desc=$txt_item_description;
			
			if(str_replace("'","",$txt_gmts_color)!="")
			{ 
				if (!in_array(str_replace("'","",$txt_gmts_color),$new_array_color))
				{
					$gmts_color_id = return_id( str_replace("'","",$txt_gmts_color), $color_arr, "lib_color", "id,color_name","24");  
					$new_array_color[$gmts_color_id]=str_replace("'","",$txt_gmts_color);
				}
				else $gmts_color_id =  array_search(str_replace("'","",$txt_gmts_color), $new_array_color); 
			}
			else
			{
				$gmts_color_id=0;
			}
			
			if(str_replace("'","",$txt_item_color)!="")
			{ 
				if (!in_array(str_replace("'","",$txt_item_color),$new_array_color))
				{
					$txt_item_color_id = return_id( str_replace("'","",$txt_item_color), $color_arr, "lib_color", "id,color_name","24");  
					$new_array_color[$txt_item_color_id]=str_replace("'","",$txt_item_color);
				}
				else $txt_item_color_id =  array_search(str_replace("'","",$txt_item_color), $new_array_color); 
			}
			else
			{
				$txt_item_color_id=0;
			}
			
			if(str_replace("'","",$txt_gmts_size)!="")
			{
				if (!in_array(str_replace("'","",$txt_gmts_size),$new_array_size))
				{
				  $gmts_size_id = return_id( str_replace("'","",$txt_gmts_size), $size_arr, "lib_size", "id,size_name","24");  
				  $new_array_size[$gmts_size_id]=str_replace("'","",$txt_gmts_size);
				}
				else $gmts_size_id =  array_search(str_replace("'","",$txt_gmts_size), $new_array_size); 
			}
			else
			{
				$gmts_size_id=0;
			}
		}
		
		$field_array_update="receive_basis*receive_date*challan_no*challan_date*booking_id*booking_no*booking_without_order*store_id*boe_mushak_challan_no*boe_mushak_challan_date*lc_no*source*knitting_source*supplier_id*currency_id*exchange_rate*fabric_source*updated_by*update_date";
			
		$data_array_update=$cbo_receive_basis."*".$txt_receive_date."*".$txt_receive_chal_no."*".$txt_challan_date."*".$txt_booking_pi_id."*".$txt_booking_pi_no."*".$booking_without_order."*".$cbo_store_name."*".$txt_boe_mushak_challan_no."*".$txt_boe_mushak_challan_date."*".$lc_id."*".$cbo_source."*".$suplier_type."*".$cbo_supplier_name."*".$cbo_currency_id."*".$txt_exchange_rate."*".$meterial_source."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		
		
		$ile=return_field_value("standard","variable_inv_ile_standard","source=$cbo_source and company_name=$cbo_company_id and category=4 and status_active=1 and is_deleted=0");
		$ile_cost = str_replace("'","",$txt_ile);
		
		if($db_type==0)
		{
			$concattS = explode(",",return_field_value("concat(trim_uom,',',conversion_factor)","lib_item_group","id=$cbo_item_group")); 
		}
		else
		{
			$concattS = explode(",",return_field_value("trim_uom || ',' || conversion_factor as data","lib_item_group","id=$cbo_item_group","data")); 
		}

		$cons_uom = $concattS[0];
		$conversion_factor = $concattS[1];
		
		$exchange_rate = str_replace("'","",$txt_exchange_rate);
		$txt_receive_qnty = str_replace("'","",$txt_receive_qnty);
		$con_qnty = $conversion_factor*$txt_receive_qnty;
		
		$rate = str_replace("'","",$txt_hidden_rate);
		$domestic_rate = return_domestic_rate($rate,$ile_cost,$exchange_rate,$conversion_factor);
		$cons_rate = number_format($domestic_rate,$dec_place[3],".","");
		$con_amount = $cons_rate*$con_qnty;
		$con_ile = $ile/$conversion_factor;
		$con_ile_cost = ($con_ile/100)*$cons_rate;
		
		
		
		if(str_replace("'","",$txt_item_size)!="")
		{
			$item_size_cond="item_size=$txt_item_size";
		}
		else 
		{
			if($db_type==0)
			{
				$item_size_cond="item_size=''";
			}
			else
			{
				$item_size_cond="item_size is null";
			}
			
		}
		
		if(str_replace("'","",$txt_brand_supref)!="")
		{
			$brand_supref_cond="brand_supplier=$txt_brand_supref";
		}
		else 
		{
			if($db_type==0)
			{
				$brand_supref_cond="brand_supplier=''";
			}
			else
			{
				$brand_supref_cond="brand_supplier is null";
			}
			
		}
		
		if($gmts_size_id=="") $gmts_size_id=0;
		if($gmts_color_id=="") $gmts_color_id=0;
		
		if(str_replace("'","",$txt_item_color_id)=="") $txt_item_color_id=0;
		$meterial_source=str_replace("'","",$meterial_source);
		$is_buyer_supplied = ($meterial_source==3)?1 : 0;
		$buyer_supplied = ($meterial_source==3)?", [BS]" : "";
		if($db_type==2)
		{
			$item_descrip=str_replace("'","",$item_desc);
			$item_descrip=str_replace("(","[",$item_descrip);
			$item_descrip=str_replace(")","]",$item_descrip);
		}
		else
		{
			$item_descrip=str_replace("'","",$item_desc);
		}
		$item_descrip=$item_descrip.$buyer_supplied;
		
		$adjust_sql = sql_select("select a.cons_qnty, a.cons_rate, a.book_keeping_curr, a.payment_over_recv, b.avg_rate_per_unit, b.current_stock, b.stock_value from inv_trims_entry_dtls a, product_details_master b where a.id=$update_dtls_id and a.prod_id=b.id");
		
		$prod_id='';
		$row_prod=sql_select("select id, current_stock, avg_rate_per_unit, stock_value from product_details_master where company_id=$cbo_company_id and item_category_id=4 and entry_form=24 and item_group_id=$cbo_item_group and trim(REGEXP_REPLACE((case when item_description like '%, [BS]' then SUBSTR(item_description, 0, LENGTH(item_description) - 6) else item_description end), '\s{2,}', ' '))=REGEXP_REPLACE('$item_descrip', '\s{2,}', ' ') and $brand_supref_cond and color='$gmts_color_id' and item_color=$txt_item_color_id and gmts_size='$gmts_size_id' and $item_size_cond and status_active=1 and is_deleted=0");
		
		if(count($row_prod)>0)
		{
			$prod_id=$row_prod[0][csf('id')];
			if($prod_id==str_replace("'","",$previous_prod_id))
			{
				$curr_stock_qnty=$row_prod[0][csf('current_stock')]+$con_qnty-$adjust_sql[0][csf('cons_qnty')];
				$curr_stock_value=$avg_rate_per_unit=0;
				if ($curr_stock_qnty != 0){
					$curr_stock_value=$row_prod[0][csf('stock_value')]+$con_amount-$adjust_sql[0][csf('book_keeping_curr')];
					$avg_rate_per_unit=number_format($curr_stock_value/$curr_stock_qnty,$dec_place[3],'.','');
				}				
				
				$field_array_prod_update="store_id*avg_rate_per_unit*last_purchased_qnty*current_stock*stock_value*updated_by*update_date";
				$data_array_prod_update=$cbo_store_name."*".$avg_rate_per_unit."*".$con_qnty."*".$curr_stock_qnty."*".$curr_stock_value."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
				
				if($curr_stock_qnty<0)
				{
					echo "40**Stock cannot be less than zero.";disconnect($con);die;
				}
			}
			else
			{
				$adjust_curr_stock=$adjust_sql[0][csf('current_stock')]-$adjust_sql[0][csf('cons_qnty')];
				$cur_st_value=$cur_st_rate=0;
				if ($adjust_curr_stock != 0){
					$cur_st_value=$adjust_sql[0][csf('stock_value')]-$adjust_sql[0][csf('book_keeping_curr')];
					$cur_st_rate=number_format($cur_st_value/$adjust_curr_stock,$dec_place[3],'.','');
				}				
				
				$field_array_adjust="current_stock*avg_rate_per_unit*stock_value";
				$data_array_adjust=$adjust_curr_stock."*".$cur_st_rate."*".$cur_st_value;
				
				if($adjust_curr_stock<0)
				{
					echo "40**Stock cannot be less than zero.";disconnect($con);die;
				}
				
				$curr_stock_qnty=$row_prod[0][csf('current_stock')]+$con_qnty;
				$curr_stock_value=$avg_rate_per_unit=0;
				if ($curr_stock_qnty != 0){
					$curr_stock_value=$row_prod[0][csf('stock_value')]+$con_amount;
					$avg_rate_per_unit=number_format($curr_stock_value/$curr_stock_qnty,$dec_place[3],'.','');
				}				
				
				$field_array_prod_update="store_id*avg_rate_per_unit*last_purchased_qnty*current_stock*stock_value*updated_by*update_date";
				$data_array_prod_update=$cbo_store_name."*".$avg_rate_per_unit."*".$con_qnty."*".$curr_stock_qnty."*".$curr_stock_value."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			}
		}
		else
		{
			$adjust_curr_stock=$adjust_sql[0][csf('current_stock')]-$adjust_sql[0][csf('cons_qnty')];
			$cur_st_value=$cur_st_rate=0;
			if ($adjust_curr_stock != 0){
				$cur_st_value=$adjust_sql[0][csf('stock_value')]-$adjust_sql[0][csf('book_keeping_curr')];
				$cur_st_rate=number_format($cur_st_value/$adjust_curr_stock,$dec_place[3],'.','');
			}			
			
			$field_array_adjust="current_stock*avg_rate_per_unit*stock_value";
			$data_array_adjust=$adjust_curr_stock."*".$cur_st_rate."*".$cur_st_value;
			
			if($adjust_curr_stock<0)
			{

				echo "40**Stock cannot be less than zero.";
				disconnect($con);die;
			}
			
			
			$prod_id = return_next_id_by_sequence("PRODUCT_DETAILS_MASTER_PK_SEQ", "product_details_master", $con);
			$item_name =$trim_group_arr[str_replace("'","",$cbo_item_group)]['name'].$buyer_supplied;;
			$prod_name_dtls=$item_name.", ".trim(str_replace("'","",$txt_item_description)).$buyer_supplied;
			
			if($db_type==2)
			{
				$prod_name_dtls=str_replace("(","[",$prod_name_dtls);
				$prod_name_dtls=str_replace(")","]",$prod_name_dtls);
			}
			
			$field_array_prod="id, company_id, store_id, item_category_id, entry_form, item_group_id, item_description, product_name_details, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, color, item_color, gmts_size, item_size, brand_supplier, is_buyer_supplied, inserted_by, insert_date";
			
			$data_array_prod="(".$prod_id.",".$cbo_company_id.",".$cbo_store_name.",4,24,".$cbo_item_group.",'".$item_descrip."','".$prod_name_dtls."',".$cons_uom.",".$cons_rate.",".$con_qnty.",".$con_qnty.",".$con_amount.",'".$gmts_color_id."',".$txt_item_color_id.",'".$gmts_size_id."',".$txt_item_size.",".$txt_brand_supref.",".$is_buyer_supplied.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
		}
		
		$max_issue_id = return_field_value("max(id) as max_tans_id", "inv_transaction", "prod_id=$prod_id and status_active=1 and transaction_type in(2,3,6) and id<>$update_trans_id", "max_tans_id");//and store_id=$cbo_store_name      
		if ($max_issue_id > str_replace("'", "", $update_trans_id)) 
		{
			echo "20**Next Transaction Found";disconnect($con);die;
		}
		$max_issue_date = date("Y-m-d", strtotime($max_issue_date));
		$receive_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_receive_date)));
		
		$field_array_trans_update="receive_basis*pi_wo_batch_no*booking_no*booking_without_order*prod_id*transaction_date*supplier_id*store_id*order_uom*order_qnty*order_rate*order_amount*order_ile*order_ile_cost*cons_uom*cons_quantity*cons_rate*cons_amount*cons_ile*cons_ile_cost*balance_qnty*balance_amount*payment_over_recv*floor_id*room*rack*self*bin_box*updated_by*update_date";
		
		$data_array_trans_update=$cbo_receive_basis."*".$txt_booking_pi_id."*".$txt_booking_pi_no."*".$booking_without_order."*".$prod_id."*".$txt_receive_date."*".$cbo_supplier_name."*".$cbo_store_name."*".$cbo_uom."*".$txt_receive_qnty."*".$rate."*".$txt_hidden_amount."*'".$ile."'*'".$ile_cost."'*".$cons_uom."*".$con_qnty."*".$cons_rate."*".$con_amount."*".$con_ile."*".$con_ile_cost."*".$con_qnty."*".$con_amount."*".$cbo_payment_over_recv."*".$cbo_floor."*".$cbo_room."*".$txt_rack."*".$txt_shelf."*".$cbo_bin."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		
		if($db_type==2)
		{
			$txt_item_description=str_replace("(","[",$txt_item_description);
			$txt_item_description=str_replace(")","]",$txt_item_description);
		}
		
		$save_data=str_replace("'","",$save_data);
		$tot_string=strlen($save_data);
		$count_loop=ceil($tot_string/3900);
		$first_save_data=''; $second_save_data=''; $theRest_save_data='';$count=0; $interval=3900;
		for($i=1;$i<=$count_loop; $i++)
		{
		    if($count_loop>0 && $i==1) $first_save_data=substr($save_data, $count, $interval);
		    if($count_loop>1 && $i==2) $second_save_data=substr($save_data, $count, $interval);
		    if($count_loop>2 && $i==3) $theRest_save_data=substr($save_data, $count, $interval);
		    $count+=3900;
		}
		
		$all_po_ids=str_replace("'","",$all_po_id);
		$tot_po_ids=strlen($all_po_ids);
		$count_ord_loops=ceil($tot_po_ids/3900);
		$first_order_ids=''; $second_order_ids=''; $count_ord=0; $interval_ord=3900;
		for($i=1;$i<=$count_ord_loops; $i++)
		{
		    if($count_ord_loops>0 && $i==1) $first_order_ids=substr($all_po_ids, $count_ord, $interval_ord);
		    if($count_ord_loops>1 && $i==2) $second_order_ids=substr($all_po_ids, $count_ord, $interval_ord);
		    $count_ord+=$interval_ord;
		}

		$field_array_dtls_update="prod_id*booking_id*booking_no*booking_without_order*item_group_id*item_description*brand_supplier*order_uom*order_id*order_id_2*receive_qnty*reject_receive_qnty*rate*amount*ile*ile_cost*gmts_color_id*item_color*gmts_size_id*item_size*save_string*save_string_2*save_string_3*item_description_color_size*cons_uom*cons_qnty*cons_rate*cons_ile*cons_ile_cost*book_keeping_curr*sensitivity*payment_over_recv*floor*room_no*rack_no*self_no*box_bin_no*remarks*updated_by*update_date";
		
		$data_array_dtls_update=$prod_id."*".$txt_booking_pi_id."*".$txt_booking_pi_no."*".$booking_without_order."*".$cbo_item_group."*".$item_desc."*".$txt_brand_supref."*".$cbo_uom."*'".$first_order_ids."'*'".$second_order_ids."'*".$txt_receive_qnty."*".$txt_reject_recv_qnty."*".$rate."*".$txt_hidden_amount."*'".$ile."'*".$ile_cost."*'".$gmts_color_id."'*".$txt_item_color_id."*'".$gmts_size_id."'*".$txt_item_size."*'".$first_save_data."'*'".$second_save_data."'*'".$theRest_save_data."'*".$txt_item_description."*".$cons_uom."*".$con_qnty."*".$cons_rate."*".$con_ile."*".$con_ile_cost."*".$con_amount."*".$hidden_sensitivity."*".$cbo_payment_over_recv."*".$cbo_floor."*".$cbo_room."*".$txt_rack."*".$txt_shelf."*".$cbo_bin."*".$txt_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

		$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, quantity, order_rate, order_amount, inserted_by, insert_date";
		
		if(str_replace("'","",$save_data)!="")
		{
			$save_data=explode(",",str_replace("'","",$save_data));$data_array_ord_prod="";
			for($i=0;$i<count($save_data);$i++)
			{
				$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
				$order_dtls=explode("_",$save_data[$i]);
				
				$order_id=$order_dtls[0];
				$order_qnty=$order_dtls[1];
				$order_amount=$order_qnty*$rate;
				
				if($i==0) $add_comma=""; else $add_comma=",";
				
				$data_array_prop.="$add_comma(".$id_prop.",".$update_trans_id.",1,24,".$update_dtls_id.",".$order_id.",".$prod_id.",".$order_qnty.",'".$rate."','".$order_amount."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			}
		}
		
		$rID=$rID2=$rID_adjust=$rID3=$rID4=$delete_prop=$rID6=true;
		
		$rID=sql_update("inv_receive_master",$field_array_update,$data_array_update,"id",$update_id,1);
		if(count($row_prod)>0)
		{
			if($prod_id==str_replace("'","",$previous_prod_id))
			{
				$rID2=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$prod_id,0);
			}
			else
			{
				$rID_adjust=sql_update("product_details_master",$field_array_adjust,$data_array_adjust,"id",$previous_prod_id,0);
				$rID2=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$prod_id,0);
			}
		}
		else
		{
			$rID_adjust=sql_update("product_details_master",$field_array_adjust,$data_array_adjust,"id",$previous_prod_id,0);
			
			$rID2=sql_insert("product_details_master",$field_array_prod,$data_array_prod,0);
		}
		
		$rID3=sql_update("inv_transaction",$field_array_trans_update,$data_array_trans_update,"id",$update_trans_id,0);
		
		$rID4=sql_update("inv_trims_entry_dtls",$field_array_dtls_update,$data_array_dtls_update,"id",$update_dtls_id,0);
		
		$delete_prop=execute_query( "delete from order_wise_pro_details where dtls_id=$update_dtls_id and trans_id=$update_trans_id and entry_form=24",0);
		 
		//echo "insert into order_wise_pro_details (".$field_array_proportionate.") values ".$data_array_prop;die;	
		if($data_array_prop!="" && str_replace("'","",$booking_without_order)!=1)
		{
			$rID6=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,0);
		}
		// echo "6**$rID=$rID2=$rID_adjust=$rID3=$rID4=$delete_prop=$rID6";oci_rollback($con);die;
		
		if($db_type==0)
		{
			if($rID && $rID2 && $rID_adjust && $rID3 && $rID4 && $delete_prop && $rID6)
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
			if($rID && $rID2 && $rID_adjust && $rID3 && $rID4 && $delete_prop && $rID6)
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
		$previous_prod_id=str_replace("'","",$previous_prod_id);
		$update_dtls_id=str_replace("'","",$update_dtls_id);
		$update_trans_id=str_replace("'","",$update_trans_id);
		//echo "10**$update_id=$previous_prod_id=$update_dtls_id=$update_trans_id";die;
		if($update_id>0 && $previous_prod_id>0 && $update_dtls_id>0 && $update_trans_id>0)
		{
			$previous_data_check=sql_select("select id as rcv_id, cons_quantity as rcv_qnty, cons_amount as rcv_amount  from inv_transaction where transaction_type=1 and id=$update_trans_id and prod_id=$previous_prod_id");
			$previous_check_id=$previous_data_check[0][csf("rcv_id")];
			$previous_qnty=$previous_data_check[0][csf("rcv_qnty")];
			$previous_amount=$previous_data_check[0][csf("rcv_amount")];
			
			if($db_type==0) $row_count_cond=" limit 1"; else $row_count_cond=" and rownum<2";
			$next_operation_check=sql_select("select id as next_id, mst_id as mst_id, transaction_type as transaction_type from inv_transaction where id=(select min(id) from inv_transaction where id > $previous_check_id and prod_id=$previous_prod_id and status_active=1 and transaction_type in(2,3,6))");
			if(count($next_operation_check)>0)
			{
				$next_id=$next_operation_check[0][csf("next_id")];
				$next_mst_id=$next_operation_check[0][csf("mst_id")];
				$next_transaction_type=$next_operation_check[0][csf("transaction_type")];

				if($next_transaction_type==1 || $next_transaction_type==4)
				{
					$next_mrr=return_field_value("recv_number as next_mrr_number","inv_receive_master","id=$next_mst_id","next_mrr_number");
				}
				else if($next_transaction_type==2 || $next_transaction_type==3)
				{
					$next_mrr=return_field_value("issue_number as next_mrr_number","inv_issue_master","id=$next_mst_id","next_mrr_number");
				}
				else
				{
					$next_mrr=return_field_value("transfer_system_id as next_mrr_number","inv_item_transfer_mst","id=$next_mst_id","next_mrr_number");
				}
				echo "20**Next Operation No:- $next_mrr  Found, Delete Not Allow.";
				disconnect($con);die;
				//check_table_status( $_SESSION['menu_id'],0);
			}
			
			$after_goods_pi_check=sql_select("select b.id, a.pi_number from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.after_goods_source=2 and b.after_goods_source=2 and a.goods_rcv_status=1 and a.status_active=1 and b.status_active=1 and b.work_order_dtls_id=$previous_check_id $row_count_cond");
			if(count($after_goods_pi_check)>0)
			{
				$pi_no=$after_goods_pi_check[0][csf("pi_number")];
				echo "20**PI No:- $pi_no  Found, Delete Not Allow.";
				disconnect($con);die;
			}
			
			$row_prod=sql_select( "select id, current_stock, avg_rate_per_unit, stock_value 
			from product_details_master where id=$previous_prod_id and status_active=1 and is_deleted=0" );
			$prod_id=$row_prod[0][csf('id')];
		
			$curr_stock_qnty=$row_prod[0][csf('current_stock')]-$previous_qnty;
			if ($curr_stock_qnty != 0){
				$curr_stock_value=$row_prod[0][csf('stock_value')]-$previous_amount;
				if ($curr_stock_value != 0){
					$avg_rate_per_unit=number_format($curr_stock_value/$curr_stock_qnty,$dec_place[3],'.','');
				} else {
					$avg_rate_per_unit=0;
				}
			} else {
				$avg_rate_per_unit=0;
				$curr_stock_value=0;
			}			
			
			$field_array_prod_update="avg_rate_per_unit*current_stock*stock_value*updated_by*update_date";
			$data_array_prod_update=$avg_rate_per_unit."*".$curr_stock_qnty."*".$curr_stock_value."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";			
			
			$row_propotionate=sql_select( "select id, po_breakdown_id, quantity, order_rate, order_amount 
			from order_wise_pro_details where trans_id=$previous_check_id and status_active=1 and is_deleted=0" );
			$propotionate_data=array();
			foreach($row_propotionate as $row)
			{
				$all_order_id.=$row[csf("po_breakdown_id")].",";
				$propotionate_data[$row[csf("po_breakdown_id")]]["quantity"]+=$row[csf("quantity")];
				$propotionate_data[$row[csf("po_breakdown_id")]]["order_amount"]+=$row[csf("order_amount")];
			}
			$all_order_id=chop($all_order_id,",");
			$field_array_prod_ord_update="avg_rate*stock_quantity*stock_amount*updated_by*update_date";
			if($all_order_id!="")
			{
				$prod_order_stock=sql_select("select id, po_breakdown_id, stock_quantity, stock_amount 
				from order_wise_stock where prod_id=$previous_prod_id and po_breakdown_id in($all_order_id) and status_active=1 and is_deleted=0 ");
				foreach($prod_order_stock as $row)
				{
					$current_stock_qnty=$row[csf('stock_quantity')]-$propotionate_data[$row[csf("po_breakdown_id")]]["quantity"];
					$current_stock_value=$row[csf('stock_amount')]-$propotionate_data[$row[csf("po_breakdown_id")]]["order_amount"];
					if($current_stock_value>0 && $current_stock_qnty>0)
					{
						$current_avg_rate=number_format($current_stock_value/$current_stock_qnty,$dec_place[3],'.','');
					}
					else
					{
						$current_avg_rate=0;
					}
					
					
					$ord_prod_id_arr[]=$row[csf('id')];
					$data_array_prod_ord_update[$row[csf('id')]]=explode("*",("".$current_avg_rate."*".$current_stock_qnty."*".$current_stock_value."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				}
			}
			
			
			$field_arr="status_active*is_deleted*updated_by*update_date";
			$data_arr="0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			$rID=$rID2=$rID3=$rID4=$ordProdUpdate=true;
			$rID=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$previous_prod_id,1);
			if(count($ord_prod_id_arr)>0)
			{
				$ordProdUpdate=execute_query(bulk_update_sql_statement("order_wise_stock","id",$field_array_prod_ord_update,$data_array_prod_ord_update,$ord_prod_id_arr));
			}
			//echo "10**$update_trans_id == $update_dtls_id == $update_trans_id";oci_rollback($con);check_table_status( $_SESSION['menu_id'],0);disconnect($con);die;
			$rID2=sql_update("inv_transaction",$field_arr,$data_arr,"id",$update_trans_id,1);
			$rID3=sql_update("inv_trims_entry_dtls",$field_arr,$data_arr,"id",$update_dtls_id,1);
			if($all_order_id!="")
			{
				$rID4=sql_update("order_wise_pro_details",$field_arr,$data_arr,"trans_id",$update_trans_id,1);
			}
			
			//echo "10** $rID && $ordProdUpdate && $rID2 && $rID3 && $rID4";oci_rollback($con);disconnect($con);die;
			if($db_type==0)
			{
				if($rID && $ordProdUpdate && $rID2 && $rID3 && $rID4)
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
	
			if($db_type==2 || $db_type==1 )
			{
				if($rID && $ordProdUpdate && $rID2 && $rID3 && $rID4)
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
			$("#hidden_supplier_id").val(ids[2]);
			parent.emailwindow.hide();
		}
	
    </script>

</head>

<body>
<div align="center" style="width:880px;">
	<form name="searchwofrm"  id="searchwofrm">
		<fieldset style="width:875px; margin-left:3px">
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
						<input type="hidden" name="hidden_supplier_id" id="hidden_supplier_id" >  
                    </th> 
                </thead>
                <tr class="general">
                    <td align="center">
                    	<?
							echo create_drop_down( "cbo_supplier_name", 150,"select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$cbo_company_id' and b.party_type in(4,5) and c.status_active=1 and c.is_deleted=0 $supplier_credential_cond  group by c.id, c.supplier_name order by supplier_name",'id,supplier_name', 1, '-- ALL Supplier --',0);
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
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('cbo_supplier_name').value+'_'+document.getElementById('cbo_year_selection').value, 'create_trims_recv_search_list_view', 'search_div', 'trims_receive_entry_controller', 'setFilterGrid(\'list_view\',-1);')" style="width:100px;" />
                     </td>
                </tr>

                <tr>
                	<td colspan="5" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
                </tr>
           </table>
           <div style="width:100%; margin-top:5px;" id="search_div" align="left"></div>
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
	$year_id =$data[6];
	
	if($supplier_id==0) $supplier_name="%%"; else $supplier_name=$supplier_id;
	$supplier_library = return_library_array("select id, short_name from lib_supplier","id","short_name");
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	
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
		$date_cond="";
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
	
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";//defined Later
	$year_condition="";
	if($year_id>0)
	{
		if($db_type==0)
		{
			$year_condition=" and YEAR(a.insert_date)='$year_id'";
		}
		else
		{
			$year_condition=" and to_char(a.insert_date,'YYYY')='$year_id'";
		}
	}
	
	$userCredential = sql_select("SELECT unit_id as company_id, store_location_id, item_cate_id, supplier_id FROM user_passwd where id=$user_id");
	$supplier_id = $userCredential[0][csf('supplier_id')];
	$store_location_id = $userCredential[0][csf('store_location_id')];
	
	if ($store_location_id !='') {
		$store_location_credential_cond = " and a.store_id in($store_location_id)"; 
	}
	if ($supplier_id !='') {
		$supplier_credential_cond = " and a.supplier_id in($supplier_id)";
	}
	
	$sql = "select a.id, a.recv_number_prefix_num, $year_field, a.recv_number, a.booking_no, a.supplier_id, a.store_id, a.source, a.currency_id, a.receive_date, a.challan_no, a.challan_date, a.is_posted_account, a.knitting_source 
	from inv_receive_master a, inv_transaction b 
	where a.id=b.mst_id and b.transaction_type=1 and a.entry_form=24 and a.is_multi=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_id and a.supplier_id like '$supplier_name' $search_field_cond $date_cond $supplier_credential_cond $store_location_credential_cond $year_condition
	group by a.id, a.recv_number_prefix_num, a.insert_date, a.recv_number, a.booking_no, a.supplier_id, a.store_id, a.source, a.currency_id, a.receive_date, a.challan_no, a.challan_date, a.is_posted_account, a.knitting_source  
	order by a.id desc"; 
	$result=sql_select($sql);
	/*
		$booking_no=sql_select($sql);
		$booking_no_array=array();
		foreach($booking_no as $book_row)
		{
			if($book_row[csf("booking_no")]!=""){
			$booking_no_array[] = "'".$book_row[csf("booking_no")]."'";
			}
		}
		$booking_no_sql=implode(",",$booking_no_array);
	
	 $booking_sql = "select a.id, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.delivery_date, a.currency_id, a.source, a.supplier_id, a.booking_type, a.is_short, to_char(a.insert_date,'YYYY') as year, a.pay_mode, a.fabric_source, 0 as type 
			from wo_booking_mst a, wo_booking_dtls b 
			where a.booking_no=b.booking_no and a.company_id=$company_id  and a.item_category=4 and a.booking_type=2 and a.is_short=2 and a.pay_mode<>2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_no in ($booking_no_sql)
			group by a.id, a.booking_no, a.booking_date, a.delivery_date, a.currency_id, a.source, a.supplier_id, a.booking_no_prefix_num, a.insert_date, a.booking_type, a.is_short, a.pay_mode, a.fabric_source
			union all
			select a.id, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.delivery_date, a.currency_id, a.source, a.supplier_id, a.booking_type, a.is_short, to_char(a.insert_date,'YYYY') as year, a.pay_mode, a.fabric_source, 0 as type 
			from wo_booking_mst a, wo_booking_dtls b 
			where a.booking_no=b.booking_no and a.company_id=$company_id   and a.item_category=4 and a.booking_type=2 and a.is_short=1 and a.pay_mode<>2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_no in ($booking_no_sql)
			group by a.id, a.booking_no, a.booking_date, a.delivery_date, a.currency_id, a.source, a.supplier_id, a.booking_no_prefix_num, a.insert_date, a.booking_type, a.is_short, a.pay_mode, a.fabric_source
			union all
			select s.id, s.booking_no_prefix_num, s.booking_no, s.booking_date, s.delivery_date, s.currency_id, s.source, s.supplier_id, 0 as booking_type, 0 as is_short, to_char(s.insert_date,'YYYY') as year, s.pay_mode, 0 as fabric_source, 1 as type 
			FROM wo_non_ord_samp_booking_mst s, wo_non_ord_samp_booking_dtls t 
			WHERE s.booking_no=t.booking_no and s.company_id=$company_id and s.pay_mode<>2 and s.status_active =1 and s.is_deleted=0 and t.status_active =1 and t.is_deleted=0 and s.item_category=4 and s.booking_no in ($booking_no_sql)
			group by s.id, s.booking_no_prefix_num, s.booking_no, s.booking_date, s.delivery_date, s.currency_id, s.source, s.supplier_id, s.insert_date, s.pay_mode order by type, id"; 
			$paymode_sql=sql_select($booking_sql);
			$paymode_array=array();
			foreach($paymode_sql as $pay_row)
			{
				$paymode_array[$pay_row[csf("booking_no")]]['pay_mode']=$pay_row[csf("pay_mode")];
			}
	*/
	//echo $sql;

	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="860" class="rpt_table">
        <thead>
            <th width="30">Sl.</th>
            <th width="60">Received No</th>
            <th width="50">Year</th>
            <th width="120">WO/PI No</th>
            <th width="80">Supplier</th>
            <th width="90">Store</th>
            <th width="80">Receive date</th>
            <th width="75">Challan No</th>
            <th width="80">Challan Date</th>
            <th width="60">Currency</th>
            <th>Source</th>
        </thead>
	</table>
	<div style="width:860px; max-height:200px; overflow-y:scroll" id="list_container_batch" align="left">	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="840" class="rpt_table" id="list_view">  
        <?
        	$store_arr = return_library_array("select id, store_name from lib_store_location","id","store_name");
            $i=1;
            foreach($result as $row)
            { 
            	//$dataArray=sql_select($sql);
				if($row[csf('knitting_source')]==3 || $row[csf('knitting_source')]==5)
				{
					$supplier_arr=$company_library;
				}
				else
				{
					$supplier_arr=$supplier_library;
				} 
                if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
        		?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('id')]."_".$row[csf('is_posted_account')]."_".$row[csf('supplier_id')]  ; ?>')"> 
                    <td width="30"><p><? echo $i; ?></p></td>
                    <td width="60" align="center"><p><? echo $row[csf('recv_number_prefix_num')]; ?></p></td>
                    <td width="50"><p><? echo $row[csf('year')]; ?></p></td>
                    <td width="120"><p><? echo $row[csf('booking_no')]; ?>&nbsp;</p></td>
                    <td width="80" ><? echo $supplier_arr[$row[csf('supplier_id')]]; ?></td>
                    <td width="90" ><? echo $store_arr[$row[csf('store_id')]]; ?></td>
                    <td width="80" align="center"><? echo $row[csf('receive_date')]; ?></td>
                    <td width="75"><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
                    <td width="80" align="center"><p><? echo $row[csf('challan_date')]; ?>&nbsp;</p></td>
                    <td width="60"><p><? echo $currency[$row[csf('currency_id')]]; ?>&nbsp;</p></td>
                    <td><p><? echo $source[$row[csf('source')]]; ?>&nbsp;</p></td>
                </tr>
        		<?
            	$i++;
            }
        	?>
        </table>
    </div>
	<?	
	/*$store_arr = return_library_array("select id, store_name from lib_store_location","id","store_name");
	$arr=array(3=>$supplier_arr,4=>$store_arr,8=>$currency,9=>$source);
	
	echo create_list_view("list_view", "Received No,Year,WO/PI No,Supplier,Store,Receive date,Challan No,Challan Date,Currency,Source", "60,50,120,80,90,80,75,80,60","860","240",0, $sql, "js_set_value", "id,is_posted_account", "", 1, "0,0,0,supplier_id,store_id,0,0,0,currency_id,source", $arr, "recv_number_prefix_num,year,booking_no,supplier_id,store_id,receive_date,challan_no,challan_date,currency_id,source", "",'','0,0,0,0,0,3,0,3,0,0');*/
	
	exit();
}

if($action=='populate_data_from_trims_recv')
{
	$data_array=sql_select("select id, recv_number, company_id,location_id, receive_basis, booking_id, booking_no, booking_without_order, supplier_id, store_id, source, currency_id, challan_no, receive_date, challan_date, lc_no, exchange_rate, knitting_source, fabric_source, is_audited, variable_setting, boe_mushak_challan_no, boe_mushak_challan_date from inv_receive_master where id='$data'");


	$company_id=$data_array[0][csf("company_id")];
	$variable_inventory_sql=sql_select("select store_method, rack_balance from variable_settings_inventory  where company_name=$company_id and item_category_id=4 and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1");
	$store_method=$variable_inventory_sql[0][csf("store_method")];
	

	foreach ($data_array as $row)
	{ 
		echo "document.getElementById('txt_recieved_id').value 				= '".$row[csf("recv_number")]."';\n";
		echo "document.getElementById('cbo_company_id').value 				= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_receive_basis').value 			= '".$row[csf("receive_basis")]."';\n";
		echo "document.getElementById('store_update_upto').value 			= '" . $store_method . "';\n";

		echo "$('#cbo_company_id').attr('disabled','true')".";\n";
		echo "set_receive_basis();\n";
		
		if($row[csf("lc_no")]>0)
		{
			$lc_no=return_field_value("lc_number","com_btb_lc_master_details","id='".$row[csf("lc_no")]."'");
		}
		
		if($row[csf("booking_no")]!="")
		{
			$pi_basis_id=return_field_value("pi_basis_id","com_pi_master_details","pi_number='".$row[csf("booking_no")]."'");
		}
		
		echo "document.getElementById('txt_receive_date').value 			= '".change_date_format($row[csf("receive_date")])."';\n";
		echo "document.getElementById('txt_boe_mushak_challan_date').value 			= '".change_date_format($row[csf("boe_mushak_challan_date")])."';\n";
		echo "document.getElementById('txt_boe_mushak_challan_no').value 			= '".$row[csf("boe_mushak_challan_no")]."';\n";
		echo "document.getElementById('txt_booking_pi_no').value 			= '".$row[csf("booking_no")]."';\n";
		echo "document.getElementById('txt_wo_pi_basis_id').value 			= '".$pi_basis_id."';\n";
		echo "document.getElementById('txt_booking_pi_id').value 			= '".$row[csf("booking_id")]."';\n";
		echo "document.getElementById('booking_without_order').value 		= '".$row[csf("booking_without_order")]."';\n";
		
		if($row[csf("booking_without_order")]==1)
		{
			echo "$('#txt_buyer_order').attr('disabled','disabled');\n";
			echo "$('#txt_receive_qnty').removeAttr('disabled','disabled');\n";	
		}
		else
		{
			echo "$('#txt_buyer_order').removeAttr('disabled','disabled');\n";
			echo "$('#txt_receive_qnty').attr('disabled','disabled');\n";	
		}

		echo "document.getElementById('txt_receive_chal_no').value 			= '".$row[csf("challan_no")]."';\n";
		echo "document.getElementById('txt_challan_date').value 			= '".change_date_format($row[csf("challan_date")])."';\n";
		echo "document.getElementById('txt_lc_no').value 					= '".$lc_no."';\n";
		echo "document.getElementById('lc_id').value 						= '".$row[csf("lc_no")]."';\n";
		echo "document.getElementById('cbo_source').value 					= '".$row[csf("source")]."';\n";
		echo "document.getElementById('suplier_type').value 				= '".$row[csf("knitting_source")]."';\n";


		echo "load_room_rack_self_bin('requires/trims_receive_entry_controller*4', 'store','store_td', '".$row[csf('company_id')]."','".$row[csf('location_id')]."',this.value);\n";

		echo "document.getElementById('cbo_store_name').value 				= '".$row[csf("store_id")]."';\n";

		echo "load_room_rack_self_bin('requires/trims_receive_entry_controller', 'floor','floor_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."',this.value);\n";
		
		echo "load_drop_down( 'requires/trims_receive_entry_controller','".$row[csf("company_id")]."'+'**'+'".$row[csf("knitting_source")]."'+'**'+'".$row[csf("supplier_id")]."', 'load_drop_down_supplier', 'supplier_td_id' );\n";
		
		echo "document.getElementById('cbo_supplier_name').value 			= '".$row[csf("supplier_id")]."';\n";
		echo "document.getElementById('cbo_currency_id').value 				= '".$row[csf("currency_id")]."';\n";
		echo "document.getElementById('txt_exchange_rate').value 			= '".$row[csf("exchange_rate")]."';\n";
		echo "document.getElementById('update_id').value 					= '".$row[csf("id")]."';\n";
		echo "document.getElementById('meterial_source').value 					= '".$row[csf("fabric_source")]."';\n";
		echo "$('#variable_string_inventory').val('".$row[csf("variable_setting")]."');\n";
		
		$variable_ref=explode("**",$row[csf("variable_setting")]);
		//echo "$('#variable_string_inventory').val('".$variable_ref[1]."');\n";
		if($variable_ref[2]==1)
		{
			echo "$('#rate_td').css('display', 'none');\n";
			echo "$('#amount_td').css('display', 'none');\n";
			echo "$('#book_currency_td').css('display', 'none');\n";
		}
		else
		{
			echo "$('#rate_td').css('display', '');\n";
			echo "$('#amount_td').css('display', '');\n";
			echo "$('#book_currency_td').css('display', '');\n";
		}
		if($variable_ref[3]==2)
		{
			echo "$('#txt_rate').attr('readonly',true);\n";
		}
		else
		{
			echo "$('#txt_rate').attr('readonly',false);\n";
		}

		// Check Audited
		if($row[csf("is_audited")]==1) echo "$('#audited').text('Audited');\n";
		else echo "$('#audited').text('');\n";
		
		echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_trims_receive',1,1);\n";  
		exit();
	}
}

if($action=="show_trims_listview")
{
	$data_ref=explode("__",$data);
	$mst_id=$data_ref[0];
	$variable_string_inventory=$data_ref[1];
	$variable_string_inventory_ref=explode("**",$variable_string_inventory);
	$rate_hide_inventory=$variable_string_inventory_ref[2];

	$item_arr = return_library_array("select id, item_name from lib_item_group","id","item_name");
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0","id","color_name");
	$size_arr = return_library_array("select id, size_name from lib_size","id","size_name");
	/*$mstData=sql_select("select receive_basis, booking_id, booking_no, booking_without_order from inv_receive_master where id='$data'");
	$bookingNo_piId=$mstData[0][csf('booking_id')];
	$receive_basis=$mstData[0][csf('receive_basis')];
	$bookingNo_piNo=$mstData[0][csf('booking_no')];
	$booking_without_order=$mstData[0][csf('booking_without_order')];*/
	
	/*$woPiData=array();
	if($receive_basis==1)
	{
		$sql="select item_group as trim_group, item_description as description, brand_supplier, color_id, item_color, size_id, item_size, sum(quantity) as qnty from com_pi_item_details where pi_id='$bookingNo_piId' and status_active=1 and is_deleted=0 group by item_group, item_description, brand_supplier, color_id, item_color, size_id, item_size";
	}
	else if($receive_basis==2)
	{
		if($booking_without_order==1)
		{
			$sql = "select trim_group, fabric_description as description, gmts_color as color_id, fabric_color as item_color, gmts_size as size_id, item_size, barnd_sup_ref as brand_supplier, sum(trim_qty) as qnty from wo_non_ord_samp_booking_dtls where booking_no='$bookingNo_piNo' and status_active=1 and is_deleted=0 group by trim_group, fabric_description, gmts_color as color_id, fabric_color, gmts_size, item_size, barnd_sup_ref";
		}
		else
		{
			$sql = "select b.trim_group, c.description, c.color_number_id as color_id, c.item_color, c.gmts_sizes as size_id, c.item_size, c.brand_supplier, sum(c.cons) as qnty from wo_booking_dtls b, wo_trim_book_con_dtls c where b.id=c.wo_trim_booking_dtls_id and b.booking_no='$bookingNo_piNo' and b.status_active=1 and b.is_deleted=0 group by b.trim_group, c.description, c.color_number_id, c.item_color, c.gmts_sizes, c.item_size, c.brand_supplier";
		}
	}
	//echo $sql;
	$data_array=sql_select($sql);
	foreach($data_array as $row)
	{
		$woPiData[$row[csf('trim_group')]][$row[csf('description')]][$row[csf('color_id')]][$row[csf('item_color')]][$row[csf('size_id')]][$row[csf('item_size')]][$row[csf('brand_supplier')]]=$row[csf('qnty')];
	}
	
	$recvData=array();
	$receive_sql="select a.item_group_id, a.item_description, a.gmts_color_id, a.item_color, a.gmts_size_id, a.brand_supplier, a.item_size, sum(a.receive_qnty) as qnty from inv_receive_master m, inv_trims_entry_dtls a where m.id=a.mst_id and m.receive_basis='$receive_basis' and m.entry_form=24 and m.booking_id='$bookingNo_piId' and a.status_active=1 and a.is_deleted=0 group by a.item_group_id, a.item_description, a.gmts_color_id, a.item_color, a.gmts_size_id, a.brand_supplier, a.item_size";
	$recvData_array=sql_select($receive_sql);
	foreach($recvData_array as $row)
	{
		$recvData[$row[csf('item_group_id')]][$row[csf('item_description')]][$row[csf('gmts_color_id')]][$row[csf('item_color')]][$row[csf('gmts_size_id')]][$row[csf('item_size')]][$row[csf('brand_supplier')]]=$row[csf('qnty')];
	}*/
	
	//$sql="select id, item_group_id, item_description, item_description_color_size, brand_supplier, receive_qnty, rate, amount, reject_receive_qnty, gmts_color_id, gmts_size_id, item_color, item_size, order_uom, payment_over_recv from inv_trims_entry_dtls where mst_id='$data' and status_active = '1' and is_deleted = '0'";

	$sql="select a.company_id,a.store_id,b.id, b.item_group_id, b.item_description, b.item_description_color_size, b.brand_supplier,  b.receive_qnty,  b.rate,  b.amount,  b.reject_receive_qnty,  b.gmts_color_id, 
	b.gmts_size_id,  b.item_color,  b.item_size,  b.order_uom,  b.payment_over_recv from inv_receive_master a , inv_trims_entry_dtls b where a.id=b.mst_id and  b.mst_id='$mst_id' and a.status_active=1 and a.is_deleted=0 and  b.status_active = '1' and  b.is_deleted = '0'";
	//echo $sql;
	$result=sql_select($sql);
	
	//$arr=array(0=>$item_arr,7=>$unit_of_measurement,8=>$color_arr,9=>$size_arr);
	//echo create_list_view("list_view", "Item Group,Item Description,Brand/Sup Ref,Recv. Qnty, Rate, Amount, Reject Qty, UOM,Item Color,Item Size", "80,120,70,70,60,70,70,50,70","750","200",0, $sql, "get_php_form_data", "id", "'populate_trims_details_form_data'", 0, "item_group_id,0,0,0,0,0,0,order_uom,item_color,0", $arr, "item_group_id,item_description_color_size,brand_supplier,receive_qnty,rate,amount,reject_receive_qnty,order_uom,item_color,item_size", "requires/trims_receive_entry_controller",'','0,0,0,2,2,2,2,0,0,0');
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="660" class="rpt_table">
        <thead>
            <th width="70">Item Group</th>
            <th width="100">Item Description</th>
            <th width="60">Brand/ Sup. Ref.</th>
            <th width="60">Recv. Qty.</th>
            <?
			if($rate_hide_inventory!=1)
			{
				?>
                <th width="50">Rate</th>
                <th width="60">Amount</th>
                <?
			}
			?>
            
            <th width="50">Reject Qty</th>
            <th width="50">UOM</th>
            <th width="80">Item Color</th>
            <th>Item Size</th>
        </thead>
	</table>
	<div style="width:660px; max-height:200px; overflow-y:scroll" id="list_container_batch" align="left">	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="640" class="rpt_table" id="list_view">  
        <?
            $i=1;
            foreach($result as $row)
            {  
                if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
				
				/*$over_qty=0;
				if($row[csf('payment_yes_no')]==1)
				{
					$qnty=$woPiData[$row[csf('item_group_id')]][$row[csf('item_description')]][$row[csf('gmts_color_id')]][$row[csf('item_color')]][$row[csf('gmts_size_id')]][$row[csf('item_size')]][$row[csf('brand_supplier')]];
					$recv_qnty=$recvData[$row[csf('item_group_id')]][$row[csf('item_description')]][$row[csf('gmts_color_id')]][$row[csf('item_color')]][$row[csf('gmts_size_id')]][$row[csf('item_size')]][$row[csf('brand_supplier')]];
					$over_qty=$recv_qnty-$qnty;
					$recvQty=$row[csf('receive_qnty')]-$over_qty;
					$amnt=$recvQty*$row[csf('rate')];
				}
				else
				{
					$recvQty=$row[csf('receive_qnty')];
					$amnt=$recvQty*$row[csf('rate')];
				}*/
        		?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="get_php_form_data('<? echo $row[csf('id')]."_".$row[csf('company_id')]."_".$row[csf('store_id')]; ?>','populate_trims_details_form_data', 'requires/trims_receive_entry_controller');"> 
                    <td width="70"><p><? echo $item_arr[$row[csf('item_group_id')]]; ?></p></td>
                    <td width="100"><p><? echo $row[csf('item_description_color_size')]; ?></p></td>
                    <td width="60"><p><? echo $row[csf('brand_supplier')]; ?>&nbsp;</p></td>
                    <td width="60" align="right"><? echo number_format($row[csf('receive_qnty')],2); ?></td>
                    <?
					if($rate_hide_inventory!=1)
					{
						?>
						<td width="50" align="right"><? echo number_format($row[csf('rate')],6); ?></td>
                    	<td width="60" align="right"><? echo number_format($row[csf('amount')],2); ?></td>
						<?
					}
					?>
                    <td width="50" align="right"><? echo number_format($row[csf('reject_receive_qnty')],2); ?>&nbsp;</p></td>
                    <td width="50" align="center"><p><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?>&nbsp;</p></td>
                    <td width="80"><p><? echo $color_arr[$row[csf('item_color')]]; ?>&nbsp;</p></td>
                    <td><p><? echo $row[csf('item_size')]; ?>&nbsp;</p></td>
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

if($action=='populate_trims_details_form_data')
{
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0","id","color_name");
	$size_arr = return_library_array("select id, size_name from lib_size","id","size_name");
	$data=explode("_", $data);
	//print_r($data);
	$booking_without_order=return_field_value("distinct(a.booking_without_order) as booking_without_order","inv_receive_master a, inv_trims_entry_dtls b","a.id=b.mst_id and b.id=$data[0] and a.entry_form=24","booking_without_order");
	//echo $booking_without_order.test;die;
	
	$po_sql="SELECT b.id as PO_ID, a.buyer_name as BUYER_NAME, a.job_no_prefix_num as JOB_NO, a.style_ref_no as STYLE_REF_NO, b.po_number as PO_NUMBER, b.grouping as INT_REF_NO
	from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$data[1] and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	//echo $po_sql;die;
	$po_sql_result=sql_select($po_sql);
	$po_data=array();
	foreach($po_sql_result as $row)
	{
		$po_data[$row["PO_ID"]]["PO_NUMBER"]=$row["PO_NUMBER"];
		$po_data[$row["PO_ID"]]["BUYER_NAME"]=$row["BUYER_NAME"];
		$po_data[$row["PO_ID"]]["JOB_NO"]=$row["JOB_NO"];
		$po_data[$row["PO_ID"]]["STYLE_REF_NO"]=$row["STYLE_REF_NO"];
		$po_data[$row["PO_ID"]]["INT_REF_NO"]=$row["INT_REF_NO"];
	}
	unset($po_sql_result);

	$data_array=sql_select("select id, trans_id, prod_id, item_group_id, item_description, item_description_color_size, brand_supplier, receive_qnty, rate, amount, reject_receive_qnty, gmts_color_id, gmts_size_id, item_size, order_uom, order_id, order_id_2, save_string, save_string_2, save_string_3, item_color, ile, ile_cost, book_keeping_curr, sensitivity, payment_over_recv,floor,room_no,rack_no,self_no,box_bin_no,remarks from inv_trims_entry_dtls where id='$data[0]'");
	foreach ($data_array as $row)
	{ 
		$order_no=$order_ids=$job_number=$int_ref_no='';
		$order_ids=$row[csf("order_id")];
		if($row[csf("order_id_2")]!="") $order_ids.=",".$row[csf("order_id_2")];
		if($booking_without_order!=1)
		{
			//$order_ids=$row[csf('order_id')];
			$order_ids_arr=array_unique(explode(",",$order_ids));
			//$po_number=$buyerName=$job_number="";
			foreach($order_ids_arr as $po_id)
			{
				$order_no.=$po_data[$po_id]["PO_NUMBER"].",";
				$job_number=$po_data[$po_id]["JOB_NO"];
				$int_ref_no=$po_data[$po_id]["INT_REF_NO"];
				//$buyerName = $buyer_library[$po_data[$po_id]["BUYER_NAME"]];
			}
            $order_no=chop($order_no,",");

			/*if($db_type==0)
			{
				$order_no=return_field_value("group_concat(po_number)","wo_po_break_down","id in($order_ids)");
			}
			else
			{
				$order_no=return_field_value("LISTAGG(cast(po_number as varchar2(4000)), ',') WITHIN GROUP (ORDER BY id) as po_id","wo_po_break_down","id in($order_ids)","po_id");	
			}*/
		}
		$save_string_data=$row[csf("save_string")]."".$row[csf("save_string_2")]."".$row[csf("save_string_3")];	
		echo "document.getElementById('cbo_item_group').value 				= '".$row[csf("item_group_id")]."';\n";
		echo "document.getElementById('cbo_uom').value 						= '".$row[csf("order_uom")]."';\n";
		echo "document.getElementById('txt_amount').value 					= '".number_format($row[csf("amount")],4,'.','')."';\n";
		echo "document.getElementById('txt_hidden_amount').value 			= '".$row[csf("amount")]."';\n";
		echo "document.getElementById('txt_item_description').value 		= '".$row[csf("item_description_color_size")]."';\n";
		echo "document.getElementById('hidden_item_description').value 		= '".$row[csf("item_description")]."';\n";
		echo "document.getElementById('txt_receive_qnty').value 			= '".number_format($row[csf("receive_qnty")],2,'.','')."';\n";
		echo "document.getElementById('txt_reject_recv_qnty').value 		= '".number_format($row[csf("reject_receive_qnty")],2,'.','')."';\n";
		echo "document.getElementById('txt_brand_supref').value 			= '".$row[csf("brand_supplier")]."';\n";
		echo "document.getElementById('cbo_floor').value 					= '".$row[csf("floor")]."';\n";
		if($row[csf("floor")]>0)
		{
			echo "load_room_rack_self_bin('requires/trims_receive_entry_controller', 'room','room_td', '".$data[1]."','"."','".$data[2]."','".$row[csf('floor')]."',this.value);\n";
		}
		echo "document.getElementById('cbo_room').value 					= '".$row[csf("room_no")]."';\n";
		if($row[csf("room_no")]>0)
		{
			echo "load_room_rack_self_bin('requires/trims_receive_entry_controller', 'rack','rack_td', '".$data[1]."','"."','".$data[2]."','".$row[csf('floor')]."','".$row[csf('room_no')]."',this.value);\n";
		}
		echo "document.getElementById('txt_rack').value 					= '".$row[csf("rack_no")]."';\n";
		if($row[csf('rack_no')]>0)
		{
			echo "load_room_rack_self_bin('requires/trims_receive_entry_controller', 'shelf','shelf_td', '".$data[1]."','"."','".$data[2]."','".$row[csf('floor')]."','".$row[csf('room_no')]."','".$row[csf('rack_no')]."',this.value);\n";
		}
		echo "document.getElementById('txt_shelf').value 					= '".$row[csf("self_no")]."';\n";
		if($row[csf("self_no")]>0)
		{
			echo "load_room_rack_self_bin('requires/trims_receive_entry_controller', 'bin','bin_td', '".$data[1]."','"."','".$data[2]."','".$row[csf('floor')]."','".$row[csf('room_no')]."','".$row[csf('rack_no')]."','".$row[csf('self_no')]."',this.value);\n";
		}
		echo "document.getElementById('cbo_bin').value 					= '".$row[csf("box_bin_no")]."';\n";
		echo "document.getElementById('txt_remarks').value 					= '".$row[csf("remarks")]."';\n";
		
		//######## this function call for special charecter exequte ##############//
		echo "set_rate_balance('".$row[csf('item_group_id')]."_".$row[csf('item_description')]."_".$row[csf('brand_supplier')]."_".$row[csf('sensitivity')]."_".$row[csf('gmts_color_id')]."_".$row[csf('gmts_size_id')]."_".$row[csf('item_color')]."_".$row[csf('item_size')]."_".$booking_without_order."_".$data[0]."_".$row[csf("rate")]."_1');\n";
		
		//echo "get_php_form_data(document.getElementById('cbo_receive_basis').value+'_'+document.getElementById('txt_booking_pi_id').value+'_'+document.getElementById('txt_booking_pi_no').value+'_'+".$row[csf('item_group_id')]."+'_'+'".$row[csf('item_description')]."'+'_'+'".$row[csf('brand_supplier')]."'+'_'+'".$row[csf('sensitivity')]."'+'_'+'".$row[csf('gmts_color_id')]."'+'_'+'".$row[csf('gmts_size_id')]."'+'_'+'".$row[csf('item_color')]."'+'_'+'".$row[csf('item_size')]."'+'_'+'".$booking_without_order."'+'_'+'".$row[csf('order_id')]."', 'put_balance_qnty', 'requires/trims_receive_entry_controller')".";\n";
		
		echo "document.getElementById('txt_rate').value 					= '".number_format($row[csf("rate")],6,'.','')."';\n";
		echo "document.getElementById('txt_hidden_rate').value 				= '".$row[csf("rate")]."';\n";
		echo "document.getElementById('txt_buyer_order').value 				= '".$order_no."';\n";
		echo "document.getElementById('ile_td').innerHTML 					= 'ILE% ".$row[csf("ile")]."';\n";
		echo "document.getElementById('txt_ile').value 						= '".$row[csf("ile_cost")]."';\n";
		echo "document.getElementById('txt_gmts_color').value 				= '".$color_arr[$row[csf("gmts_color_id")]]."';\n";
		echo "document.getElementById('txt_gmts_color_id').value 			= '".$row[csf("gmts_color_id")]."';\n";
		echo "document.getElementById('txt_item_color').value 				= '".$color_arr[$row[csf("item_color")]]."';\n";
		echo "document.getElementById('txt_item_color_id').value 			= '".$row[csf("item_color")]."';\n";
		echo "document.getElementById('txt_gmts_size').value 				= '".$size_arr[$row[csf("gmts_size_id")]]."';\n";
		echo "document.getElementById('txt_gmts_size_id').value 			= '".$row[csf("gmts_size_id")]."';\n";
		echo "document.getElementById('txt_item_size').value 				= '".$row[csf("item_size")]."';\n";
		echo "document.getElementById('txt_book_currency').value 			= '".number_format($row[csf("book_keeping_curr")],2,'.','')."';\n";
		echo "document.getElementById('all_po_id').value 					= '".$order_ids."';\n";
		echo "document.getElementById('previous_prod_id').value 			= '".$row[csf("prod_id")]."';\n";
		echo "document.getElementById('update_dtls_id').value 				= '".$row[csf("id")]."';\n";
		echo "document.getElementById('update_trans_id').value 				= '".$row[csf("trans_id")]."';\n";
		echo "document.getElementById('hidden_sensitivity').value 			= '".$row[csf("sensitivity")]."';\n";
		echo "document.getElementById('cbo_payment_over_recv').value 		= '".$row[csf("payment_over_recv")]."';\n";
		echo "document.getElementById('save_data').value 					= '".$save_string_data."';\n";
		echo "document.getElementById('hid_job_no').value 					= '".$job_number."';\n";
		echo "document.getElementById('hid_ir_no').value 					= '".$int_ref_no."';\n";
						
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_trims_receive',1,1);\n";  
		exit();
	}
}

if ($action=="goods_placement_popup")
{
	echo load_html_head_contents("Goods Placement Entry Form", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	
	$dtls_data=sql_select("select item_group_id, item_description, receive_qnty from inv_trims_entry_dtls where id=$update_dtls_id");
	$trim_group_arr =array(); 
	$data_array=sql_select("select id, item_name, order_uom from lib_item_group");
	foreach($data_array as $row)
	{
		$trim_group_arr[$row[csf('id')]]['name']=$row[csf('item_name')];
		$trim_group_arr[$row[csf('id')]]['uom']=$row[csf('order_uom')];
	}
	?> 
	<script>
		
		var permission='<? echo $permission; ?>';
		
		function fn_addRow( i )
		{ 
			var row_num=$('#txt_tot_row').val();
			row_num++;
			
			var clone= $("#tr_"+i).clone();
			clone.attr({
				id: "tr_" + row_num,
			});
			
			clone.find("input,select").each(function(){
				  
			$(this).attr({ 
			  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ row_num },
			  'name': function(_, name) { return name },
			  'value': function(_, value) { return value }              
			});
			 
			}).end();
			
			$("#tr_"+i).after(clone);
			
			$('#txtSelfNo_'+row_num).val('');
			$('#txtBoxBinNo_'+row_num).val('');
			$('#txtCtnNo_'+row_num).val('');
			$('#txtCtnQnty_'+row_num).val('');
			
			$('#increase_'+row_num).removeAttr("value").attr("value","+");
			$('#decrease_'+row_num).removeAttr("value").attr("value","-");
			$('#increase_'+row_num).removeAttr("onclick").attr("onclick","fn_addRow("+row_num+");");
			$('#decrease_'+row_num).removeAttr("onclick").attr("onclick","fn_deleteRow("+row_num+");");
			
			$('#txt_tot_row').val(row_num);
		}
		
		function fn_deleteRow(rowNo) 
		{ 		
			var row_num=$('#tbl_list tbody tr').length;
			if(row_num!=1)
			{
				$("#tr_"+rowNo).remove();
			}
		}
		
		function fnc_goods_placement_entry(operation)
		{
			var dataString=""; var j=0;
			$("#tbl_list").find('tbody tr').each(function()
			{
				var txtRoomNo=$(this).find('input[name="txtRoomNo[]"]').val();
				var txtRackNo=$(this).find('input[name="txtRackNo[]"]').val();
				var txtSelfNo=$(this).find('input[name="txtSelfNo[]"]').val();
				var txtBoxBinNo=$(this).find('input[name="txtBoxBinNo[]"]').val();
				var txtCtnNo=$(this).find('input[name="txtCtnNo[]"]').val();
				var txtCtnQnty=$(this).find('input[name="txtCtnQnty[]"]').val();
				
				if(txtRackNo!="")
				{
					j++;
					
					dataString+='&txtRoomNo_' + j + '=' + txtRoomNo + '&txtRackNo_' + j + '=' + txtRackNo + '&txtSelfNo_' + j + '=' + txtSelfNo + '&txtBoxBinNo_' + j + '=' + txtBoxBinNo + '&txtCtnNo_' + j + '=' + txtCtnNo + '&txtCtnQnty_' + j + '=' + txtCtnQnty;
				}
			});
			
			if(j==0)
			{
				alert("Please Insert At Least One Rack No.");
				return;	
			}
			
			var data="action=save_update_delete_goods_placement&operation="+operation+'&tot_row='+j+get_submitted_data_string('dtls_id',"../../../")+dataString;
			//alert(data);return;
			freeze_window(operation);
			
			http.open("POST","trims_receive_entry_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange=fnc_goods_placement_entry_Reply_info;
		}
		
		function fnc_goods_placement_entry_Reply_info()
		{
			if(http.readyState == 4) 
			{
				//alert(http.responseText);release_freezing();return;
				var reponse=trim(http.responseText).split('**');	
					
				show_msg(reponse[0]);
				
				if((reponse[0]==0 || reponse[0]==1 || reponse[0]==2))
				{
					reset_form('goodsPlacement_1','','','','','dtls_id');
					load_dtls_part();
				}
				
				set_button_status(reponse[2], permission, 'fnc_goods_placement_entry',1);	
				release_freezing();	
			}
		}
		
		function load_dtls_part()
		{
			var list_view_goods_placement = return_global_ajax_value( <? echo $update_dtls_id; ?>, 'load_php_dtls_form', '', 'trims_receive_entry_controller');

			if(list_view_goods_placement!='')
			{
				$("#tbl_list tbody tr").remove();
				$("#tbl_list tbody").append(list_view_goods_placement);
				
				var row_num=$("#tbl_list tbody tr").length;
				$('#txt_tot_row').val(row_num);
			}
		}
		
		function fnc_carton_sticker()
		{
			data=<? echo $update_dtls_id; ?>;
			var url=return_ajax_request_value(data, "print_report_carton_sticker", "trims_receive_entry_controller");
			//alert(url);
			window.open(url,"##");
		}

	
    </script>

</head>

<body>
<div align="center">
	<? echo load_freeze_divs ("../../../",$permission,1); ?>
	<form name="goodsPlacement_1" id="goodsPlacement_1">
		<fieldset style="width:580px;">
        	<table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="500" align="center">
                <thead>
                    <th width="160">Item Group</th>
                    <th width="200">Item Description</th>
                    <th>Qnty</th>
                </thead>
                <tr bgcolor="#FFFFFF">
                    <td><p>&nbsp;<? echo $trim_group_arr[$dtls_data[0][csf('item_group_id')]]['name']; ?></p></td>
                    <td><p>&nbsp;<? echo $dtls_data[0][csf('item_description')]; ?></p></td>
                    <td align="right"><? echo number_format($dtls_data[0][csf('receive_qnty')],2); ?>&nbsp;</td>
                    <input type="hidden" name="dtls_id" id="dtls_id" class="text_boxes" value="<? echo $update_dtls_id; ?>">
                    <input type="hidden" name="txt_tot_row" id="txt_tot_row" class="text_boxes" value="">
                </tr>
            </table>
        </fieldset> 
        <fieldset style="width:770px; margin-top:10px">
            <legend>New Entry</legend>
            <table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="760" id="tbl_list">
            	<thead>
                    <th width="110">Room No</th>
                    <th width="110">Rack No</th>
                    <th width="110">Shelf No</th>
                    <th width="110">Box/Bin</th>
                    <th width="110">Ctn. No</th>
                    <th width="110">Ctn. Qnty</th>
                    <th></th>
                </thead>
                <tbody>
                    <!--<tr id="tr_1">
                        <td>
                            <input type="text" name="txtRoomNo[]" id="txtRoomNo_1" class="text_boxes" style="width:100px;"/>
                        </td>
                        <td>
                            <input type="text" name="txtRackNo[]" id="txtRackNo_1" class="text_boxes" style="width:100px;"/>
                        </td>
                        <td>
                            <input type="text" name="txtSelfNo[]" id="txtSelfNo_1" class="text_boxes" style="width:100px;"/>
                        </td>
                        <td>
                            <input type="text" name="txtBoxBinNo[]" id="txtBoxBinNo_1" class="text_boxes" style="width:100px;"/>
                        </td>
                        <td>
                            <input type="text" name="txtCtnNo[]" id="txtCtnNo_1" class="text_boxes" style="width:100px;"/>
                        </td>
                        <td>
                            <input type="text" name="txtCtnQnty[]" id="txtCtnQnty_1" class="text_boxes_numeric" style="width:100px;"/>
                        </td>
                        <td>
                            <input type="button" id="increase_1" name="increase[]" style="width:35px" class="formbuttonplasminus" value="+" onClick="fn_addRow(1)" />
                            <input type="button" id="decrease_1" name="decrease[]" style="width:35px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(1);"/>
						</td>
                    </tr>-->
                </tbody>    
            </table>
			<table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="760">
             	<tr>
                    <td colspan="7">&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="7" align="center" class="button_container">
						<? 
							echo load_submit_buttons($permission, "fnc_goods_placement_entry", 0,0,"reset_form('goodsPlacement_1','','','','','txt_tot_row*dtls_id');$('#tbl_list tbody tr:not(:first)').remove();",1);
                        ?>
                        <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="parent.emailwindow.hide();" style="width:100px" />
                        <input type="button" name="sticker" class="formbutton" value="Carton Sticker" id="sticker" onClick="fnc_carton_sticker();" style="width:120px" /> 
                    </td>	  
                </tr>
			</table>
		</fieldset>
	</form>
</div>
</body>  
<script>

 	get_php_form_data(<? echo $update_dtls_id; ?>, "populate_data_goods_placement", "trims_receive_entry_controller" );
	load_dtls_part();
	        
</script>		
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if ($action=="save_update_delete_goods_placement")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if($operation==0)  // Insert Here
	{ 
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$field_array="id, entry_form, mst_id, trans_id, dtls_id, prod_id, room_no, rack_no, self_no, box_bin_no, ctn_no, ctn_qnty, inserted_by, insert_date";
		$dtls_data=sql_select("select mst_id, trans_id, prod_id from inv_trims_entry_dtls where id=$dtls_id");
		//$id=return_next_id( "id", "inv_goods_placement", 1 ) ;
		
		$data_array='';
		for($j=1;$j<=$tot_row;$j++)
		{ 
			$id=return_next_id_by_sequence("INV_GOODS_PLACEMENT_PK_SEQ", "inv_goods_placement", $con);	
			$txtRoomNo="txtRoomNo_".$j;
			$txtRackNo="txtRackNo_".$j;
			$txtSelfNo="txtSelfNo_".$j;
			$txtBoxBinNo="txtBoxBinNo_".$j;
			$txtCtnNo="txtCtnNo_".$j;
			$txtCtnQnty="txtCtnQnty_".$j;
			
			if($data_array!="") $data_array.=",";
			$data_array.="(".$id.",24,".$dtls_data[0][csf('mst_id')].",".$dtls_data[0][csf('trans_id')].",".$dtls_id.",".$dtls_data[0][csf('prod_id')].",'".$$txtRoomNo."','".$$txtRackNo."','".$$txtSelfNo."','".$$txtBoxBinNo."','".$$txtCtnNo."','".$$txtCtnQnty."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			//$id = $id+1;
		}
		
		if($data_array!="")
		{
			//echo $data_array;die;
			$rID=sql_insert("inv_goods_placement",$field_array,$data_array,1);
			if($rID) $flag=1; else $flag=0; 
		}
			
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "0**".str_replace("'", '', $dtls_id)."**1";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "5**0**0";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);  
				echo "0**".str_replace("'", '', $dtls_id)."**1";
			}
			else
			{
				oci_rollback($con);
				echo "5**0**0";
			}
		}
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
		
		$dtls_data=sql_select("select mst_id, trans_id, prod_id from inv_trims_entry_dtls where id=$dtls_id");
		$field_array="id, entry_form, mst_id, trans_id, dtls_id, prod_id, room_no, rack_no, self_no, box_bin_no, ctn_no, ctn_qnty, inserted_by, insert_date";
		//$id=return_next_id( "id", "inv_goods_placement", 1 ) ;

		$data_array='';
		for($j=1;$j<=$tot_row;$j++)
		{
			$id=return_next_id_by_sequence("INV_GOODS_PLACEMENT_PK_SEQ", "inv_goods_placement", $con); 	
			$txtRoomNo="txtRoomNo_".$j;
			$txtRackNo="txtRackNo_".$j;
			$txtSelfNo="txtSelfNo_".$j;
			$txtBoxBinNo="txtBoxBinNo_".$j;
			$txtCtnNo="txtCtnNo_".$j;
			$txtCtnQnty="txtCtnQnty_".$j;
			
			if($data_array!="") $data_array.=",";
			$data_array.="(".$id.",24,".$dtls_data[0][csf('mst_id')].",".$dtls_data[0][csf('trans_id')].",".$dtls_id.",".$dtls_data[0][csf('prod_id')].",'".$$txtRoomNo."','".$$txtRackNo."','".$$txtSelfNo."','".$$txtBoxBinNo."','".$$txtCtnNo."','".$$txtCtnQnty."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			//$id = $id+1;
		}
		
		$delete=execute_query("delete from inv_goods_placement where dtls_id=$dtls_id and entry_form=24",0);
		if($delete) $flag=1; else $flag=0;
		
		if($data_array!="")
		{
			//echo $data_array;die;
			$rID=sql_insert("inv_goods_placement",$field_array,$data_array,1);
			if($flag==1)
			{
				if($rID) $flag=1; else $flag=0;
			} 
		}
		
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'", '', $update_id)."**1";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "6**".str_replace("'", '', $update_id)."**1";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);  
				echo "1**".str_replace("'", '', $update_id)."**1";
			}
			else
			{
				oci_rollback($con);
				echo "6**".str_replace("'", '', $update_id)."**1";
			}
		}
		disconnect($con);
		die;
	}
	
	else if ($operation==2)   // Update Here
	{ 
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$delete=execute_query("delete from inv_goods_placement where dtls_id=$dtls_id and entry_form=24",0);
		if($db_type==0)
		{
			if($delete)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'", '', $update_id)."**0";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "6**".str_replace("'", '', $update_id)."**1";
			}
		}
		else if($db_type==2 || $db_type==1)
		{
			if($delete)
			{
				oci_commit($con); 
				echo "1**".str_replace("'", '', $update_id)."**0";
			}
			else
			{
				oci_rollback($con);
				echo "6**".str_replace("'", '', $update_id)."**1";
			}
		}
		disconnect($con);
		die;
	}
}

if ($action=="load_php_dtls_form")
{
	$sql="select room_no, rack_no, self_no, box_bin_no, ctn_no, ctn_qnty from inv_goods_placement where dtls_id=$data and entry_form=24 and status_active=1 and is_deleted=0";
	$result=sql_select($sql);
	$count=count($result);
	
	if($count==0 ) // New Insert
	{
	?>
        <tr id="tr_1">
            <td>
                <input type="text" name="txtRoomNo[]" id="txtRoomNo_1" class="text_boxes" style="width:100px;"/>
            </td>
            <td>
                <input type="text" name="txtRackNo[]" id="txtRackNo_1" class="text_boxes" style="width:100px;"/>
            </td>
            <td>
                <input type="text" name="txtSelfNo[]" id="txtSelfNo_1" class="text_boxes" style="width:100px;"/>
            </td>
            <td>
                <input type="text" name="txtBoxBinNo[]" id="txtBoxBinNo_1" class="text_boxes" style="width:100px;"/>
            </td>
            <td>
                <input type="text" name="txtCtnNo[]" id="txtCtnNo_1" class="text_boxes" style="width:100px;"/>
            </td>
            <td>
                <input type="text" name="txtCtnQnty[]" id="txtCtnQnty_1" class="text_boxes_numeric" style="width:100px;"/>
            </td>
            <td>
                <input type="button" id="increase_1" name="increase[]" style="width:35px" class="formbuttonplasminus" value="+" onClick="fn_addRow(1)" />
                <input type="button" id="decrease_1" name="decrease[]" style="width:35px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(1);"/>
            </td>
        </tr>
    <?
	}
	else // From Update
	{
		$i=0;
		foreach($result as $row)
		{
			$i++;
		?>
			<tr id="tr_<? echo $i; ?>">
                <td>
                    <input type="text" name="txtRoomNo[]" id="txtRoomNo_<? echo $i; ?>" class="text_boxes" style="width:100px;" value="<? echo $row[csf('room_no')]; ?>"/>
                </td>
                <td>
                    <input type="text" name="txtRackNo[]" id="txtRackNo_<? echo $i; ?>" class="text_boxes" style="width:100px;" value="<? echo $row[csf('rack_no')]; ?>"/>
                </td>
                <td>
                    <input type="text" name="txtSelfNo[]" id="txtSelfNo_<? echo $i; ?>" class="text_boxes" style="width:100px;" value="<? echo $row[csf('self_no')]; ?>"/>
                </td>
                <td>
                    <input type="text" name="txtBoxBinNo[]" id="txtBoxBinNo_<? echo $i; ?>" class="text_boxes" style="width:100px;" value="<? echo $row[csf('box_bin_no')]; ?>"/>
                </td>
                <td>
                    <input type="text" name="txtCtnNo[]" id="txtCtnNo_<? echo $i; ?>" class="text_boxes" style="width:100px;" value="<? echo $row[csf('ctn_no')]; ?>"/>
                </td>
                <td>
                    <input type="text" name="txtCtnQnty[]" id="txtCtnQnty_<? echo $i;?>" class="text_boxes_numeric" style="width:100px;" value="<? echo $row[csf('ctn_qnty')]; ?>"/>
                </td>
                <td>
                    <input type="button" id="increase_<? echo $i; ?>" name="increase[]" style="width:35px" class="formbuttonplasminus" value="+" onClick="fn_addRow(<? echo $i; ?>)" />
                    <input type="button" id="decrease_<? echo $i;?>" name="decrease[]" style="width:35px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $i; ?>);"/>
                </td>
            </tr>
		<?
		}		
	}
	
	exit();
}

if ($action=="populate_data_goods_placement")
{
	$result=sql_select("select id from inv_goods_placement where dtls_id=$data and entry_form=24 and status_active=1 and is_deleted=0");
	
	if(count($result)>0) $button_status=1; else $button_status=0;
	
	echo "set_button_status($button_status, '".$_SESSION['page_permission']."', 'fnc_goods_placement_entry',1,1);\n";  
	exit();
}

if($action=="print_report_carton_sticker")
{
	/*define('FPDF_FONTPATH', '../../../ext_resource/pdf/fpdf/font/');
	require('../../../ext_resource/pdf/fpdf.php');
	require('../../../ext_resource/pdf/html_table.php');
	
	foreach (glob(""."*.pdf") as $filename) {			
			@unlink($filename);
		}
		
	$pdf=new PDF();
	$pdf->AddPage();
	$pdf->SetFont('Arial','',12);
	
	$html='<table width="100%" border="1"><tr>
		<td width="200"><table><tr><td>Fuad</td></tr></table></td>
		<td width="200"><table><tr><td>Shahriar</td></tr></table></td>
		</tr></table>';
	$html='<table border="1"><tr>';
	$sql="select a.challan_no, a.receive_date, b.prod_id, b.order_id, c.room_no, c.rack_no, c.self_no, c.box_bin_no, c.ctn_no, c.ctn_qnty from inv_receive_master a, inv_trims_entry_dtls b, inv_goods_placement c where a.id=b.mst_id and a.entry_form=24 and a.item_category=4 and b.id=c.dtls_id and c.entry_form=24 and c.dtls_id=$data";
	$result=sql_select($sql);
	foreach($result as $row)
	{
		$html.='<td><table border="1" rules="all">
		<tr>
		<td width="170"><b>BUYER</b></td><td width="130">'.$row[csf('challan_no')].'</td>
		</tr>
		<tr>
		<td width="170"><b>ORDER-NO</b></td><td width="130"></td>
		</tr>
		<tr>
		<td width="170"><b>NAME OF ITEM</b></td><td width="130"></td>
		</tr>
		<tr>
		<td width="170"><b>CHALLAN-NO</b></td><td width="130"></td>
		</tr>
		<tr>
		<td width="170"><b>RCVD-DATE</b></td><td width="130"></td>
		</tr>
		<tr>
		<td width="170"><b>CTN-NO</b></td><td width="130"></td>
		</tr>
		<tr>
		<td width="170"><b>Room No</</td><td width="130"></td>
		</tr>
		<tr>
		<td width="170"><b>Rack No</b></td><td width="130"></td>
		</tr>
		<tr>
		<td width="170"><b>Self No</b></td><td width="130"></td>
		</tr>
		<tr>
		<td width="170"><b>Box/Bin</b></td><td width="130"></td>
		</tr>
		<tr>
		<td width="170"><b>Ctn. Qty.</b></td><td width="130"></td>
		</tr>
		<tr>
		<td width="170"><b>FIRST ISSUE DATE</b></td><td width="130"></td>
		</tr>
		</table></td>';
	}
	
	$html.='</tr></table>';
	
	$pdf->WriteHTML($html);	
	
	$name = 'carton_sticker_' . date('j-M-Y_h-iA') . '.pdf';
	$pdf->Output( "".$name, 'F');
	echo $name;*/
	
	require_once('../../../ext_resource/pdf/tcpdf_5_9_082/tcc/config/lang/eng.php');
	require_once('../../../ext_resource/pdf/tcpdf_5_9_082/tcc/tcpdf.php');
	header ('Content-type: text/html; charset=utf-8'); 
	$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'RA4', true, 'UTF-8', false);	// create new PDF document
	 
	// set document information
	$pdf->SetCreator('Md. Fuad Shahriar');
	$pdf->SetAuthor('Md. Fuad Shahriar');
	$pdf->SetTitle('Logic ERP');
	$pdf->SetSubject('Goods Placement Carton Sticker');
	//$pdf->SetKeywords('Logic, HRM, Payroll, HRM & Payroll, ID Card');
	
	// remove default header/footer
	$pdf->setPrintHeader(false);
	$pdf->setPrintFooter(false);
	
	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);	//set default monospaced font
	$pdf->SetMargins(12, 15, 8);								//set margins
	$pdf->SetAutoPageBreak(TRUE, 5);						//set auto page breaks
	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);				//set image scale factor
	$pdf->setLanguageArray($l);								//set some language-dependent strings
	$pdf->SetFont('times', '', 12);
	
	foreach (glob(""."*.pdf") as $filename) {			
			@unlink($filename);
		}
		
	$pdf->AddPage();
	/*$html='<table width="100%" border="1"><tr>
		<td width="200"><table><tr><td>Fuad</td></tr></table></td>
		<td width="200"><table><tr><td>Shahriar</td></tr></table></td>
		</tr></table>';*/
	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer","id","buyer_name");
	$i=1; $br=0; $order_no='';	
	$html='<table border="0"><tr>';
	$sql="select a.challan_no, a.receive_date, b.prod_id, b.order_id, b.order_id_2, c.room_no, c.rack_no, c.self_no, c.box_bin_no, c.ctn_no, c.ctn_qnty from inv_receive_master a, inv_trims_entry_dtls b, inv_goods_placement c where a.id=b.mst_id and a.entry_form=24 and a.item_category=4 and b.id=c.dtls_id and c.entry_form=24 and c.dtls_id=$data";
	$result=sql_select($sql);
	foreach($result as $row)
	{
		$order_ids="";
		$order_ids=$row[csf('order_id')];
		if($row[csf('order_id_2')]!="") $order_ids.=",".$row[csf('order_id_2')];
		if($i==1)
		{
			if($row[csf('order_id')]!="")
			{
				$order_data=sql_select("select a.buyer_name, group_concat(b.po_number) as po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in($order_ids)");
				$buyer=$buyer_arr[$order_data[0][csf('buyer_name')]];
				$order_no=$order_data[0][csf('po_number')];
			}
			
			$item_desc=return_field_value("product_name_details","product_details_master","id=".$row[csf('prod_id')]);
		}
		
		$html.='<td><table border="1" rules="all">
		<tr>
		<td width="150"><b>&nbsp;BUYER</b></td><td width="170">&nbsp;'.$buyer.'</td>
		</tr>
		<tr>
		<td width="150"><b>&nbsp;ORDER-NO</b></td><td width="170">&nbsp;'.$order_no.'</td>
		</tr>
		<tr>
		<td width="150"><b>&nbsp;NAME OF ITEM</b></td><td width="170">&nbsp;'.$item_desc.'</td>
		</tr>
		<tr>
		<td width="150"><b>&nbsp;CHALLAN-NO</b></td><td width="170">&nbsp;'.$row[csf('challan_no')].'</td>
		</tr>
		<tr>
		<td width="150"><b>&nbsp;RCVD-DATE</b></td><td width="170">&nbsp;'.change_date_format($row[csf('receive_date')]).'</td>
		</tr>
		<tr>
		<td width="150"><b>&nbsp;CTN-NO</b></td><td width="170">&nbsp;'.$row[csf('ctn_no')].'</td>
		</tr>
		<tr>
		<td width="150"><b>&nbsp;ROOM NO</b></td><td width="170">&nbsp;'.$row[csf('room_no')].'</td>
		</tr>
		<tr>
		<td width="150"><b>&nbsp;RACK NO</b></td><td width="170">&nbsp;'.$row[csf('rack_no')].'</td>
		</tr>
		<tr>
		<td width="150"><b>&nbsp;SHELF NO</b></td><td width="170">&nbsp;'.$row[csf('self_no')].'</td>
		</tr>
		<tr>
		<td width="150"><b>&nbsp;BOX/BIN</b></td><td width="170">&nbsp;'.$row[csf('box_bin_no')].'</td>
		</tr>
		<tr>
		<td width="150"><b>&nbsp;CARTON QTY.</b></td><td width="170">&nbsp;'.$row[csf('ctn_qnty')].'</td>
		</tr>
		<tr>
		<td width="150"><b>&nbsp;FIRST ISSUE DATE</b></td><td width="170">&nbsp;</td>
		</tr>
		</table></td>';
		
		if($i%2==0) {$html.='</tr><tr><td><br><br><br><br></td></tr><tr>';}
		if( $i % 6 == 0 && $i < count( $result ) ) {
				$html .= "</tr></table>";
				$pdf->writeHTML($html, true, false, true, false, '');
				$pdf->AddPage();
				$html='<table border="0"><tr>';
			}
		$i++;
		
	}
	
	$html.='</tr></table>';	
		
	$pdf->writeHTML($html, true, false, true, false, '');
	$name = 'carton_sticker_' . date('j-M-Y_h-iA') . '.pdf';
	$pdf->Output( "".$name, 'F');
	echo $name;	
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
	$data=explode('__',$data);
	$variable_inventory_ref=explode("**",$data[3]);
	$rate_hide_inventory=$variable_inventory_ref[2];
	//print_r ($data);
	
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	//$supplier_library=return_library_array( "select id,supplier_name from lib_supplier", "id","supplier_name"  );
	$item_library=return_library_array( "select id, item_name from lib_item_group", "id", "item_name"  );
	//$pi_library=return_library_array( "select id, pi_number from com_pi_master_details", "id", "pi_number"  );
	$wo_library=return_library_array( "select id, booking_no from wo_booking_mst", "id", "booking_no"  );
	$color_library=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name"  );
	$lc_arr=return_library_array( "select id, lc_number from  com_btb_lc_master_details", "id", "lc_number"  );
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	
	$po_sql="SELECT b.id as PO_ID, a.buyer_name as BUYER_NAME, a.job_no_prefix_num as JOB_NO, a.style_ref_no as STYLE_REF_NO, b.po_number as PO_NUMBER 
	from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$data[0] and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	//echo $po_sql;die;
	$po_sql_result=sql_select($po_sql);
	$po_data=array();
	foreach($po_sql_result as $row)
	{
		$po_data[$row["PO_ID"]]["PO_NUMBER"]=$row["PO_NUMBER"];
		$po_data[$row["PO_ID"]]["BUYER_NAME"]=$row["BUYER_NAME"];
		$po_data[$row["PO_ID"]]["JOB_NO"]=$row["JOB_NO"];
		$po_data[$row["PO_ID"]]["STYLE_REF_NO"]=$row["STYLE_REF_NO"];
	}
	unset($po_sql_result);

	/*$buyer_sql = "SELECT b.po_number, a.buyer_name from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0"; 
	$buyerDataArray=sql_select($buyer_sql);*/

	
	$sql="SELECT id, recv_number, receive_basis, receive_date, booking_id, booking_no, challan_no, challan_date, lc_no, source, store_id, supplier_id, currency_id, exchange_rate,booking_without_order, remarks ,knitting_source 
	from inv_receive_master where id='$data[1]' and status_active=1 and is_deleted=0 and entry_form=24";
	//echo $sql;
	$dataArray=sql_select($sql);
	if($dataArray[0][csf('knitting_source')]==3 || $dataArray[0][csf('knitting_source')]==5)
	{
		$supplier_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	}
	else
	{
		$supplier_library=return_library_array( "select id,supplier_name from lib_supplier", "id","supplier_name"  );
	}
	if($rate_hide_inventory!=1){
		$width=1020;
		$width_px='1020px';
	} 
	else{
		$width=1250;
		$width_px='1250px';
	} 
	
?>
<div style="width:<? echo $width_px; ?>;">
    <table width="<? echo $width; ?>" cellspacing="0" align="right" border="0">
        <tr>
            <td colspan="7" align="center" style="font-size:xx-large"><strong><? echo $company_library[$data[0]]; ?></strong></td>
        </tr>
        <tr class="form_caption">
            <td colspan="7" align="center">
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
            <td colspan="7" align="center" style="font-size:x-large"><strong><u><? echo $data[2]; ?> Challan</u></strong></center></td>
        </tr>
        <br>
        <table cellspacing="0" width="1000" align="center" border="1" rules="all" class="rpt_table">
        <tr>
            <td width="160"><strong>System ID:</strong></td> <td width="175px"><? echo $dataArray[0][csf('recv_number')]; ?></td>
            <td width="130"><strong> Receive Basis :</strong></td><td width="175px" ><? echo $receive_basis_arr[$dataArray[0][csf('receive_basis')]]; ?></td>
            <td width="125"><strong>Received Date:</strong></td><td width="175px"><? echo  change_date_format($dataArray[0][csf('receive_date')]); ?></td>
        </tr>
        <tr>
            <td><strong>WO/PI:</strong></td> <td width="175px"><? echo $dataArray[0][csf('booking_no')]; ?></td>
            <td><strong>Challan No :</strong></td><td width="175px" ><? echo $dataArray[0][csf('challan_no')]; ?></td>
            <td><strong>Challan Date:</strong></td><td width="175px"><? echo  change_date_format($dataArray[0][csf('challan_date')]); ?></td>
        </tr>
        <tr>
        	<td><strong>Supplier:</strong></td><td width="175px"><? echo $supplier_library[$dataArray[0][csf('supplier_id')]]; ?></td>
        </tr>
    </table>
    </table>
        <?
		
		/*	$sql = "select id, pi_number, supplier_id, pi_date, last_shipment_date, pi_basis_id, internal_file_no, currency_id, source from com_pi_master_details where item_category_id='4' and status_active=1 and is_deleted=0 and importer_id=$company_id $search_field_cond";  */
	?>
    <br>
	<div style="width:100%;">
        <table align="right" cellspacing="0" width="<? echo $width; ?>"  border="1" rules="all" class="rpt_table" >
            <thead bgcolor="#dddddd" align="center">
                <th width="30">SL</th>
                <th width="110" align="center">Item Group</th>
                <th width="130" align="center">Item Des.</th>
                <th width="100" align="center">Buyer Name</th>
                <th width="70" align="center">Gmts Color</th>
                <th width="70" align="center">Item Color</th>
                <th width="70" align="center">Item Size</th>
                <th width="70" align="center">Buyer Order</th>
                <th width="40" align="center">Job No</th>
                <th width="40" align="center">UOM</th>
                <th width="70" align="center">WO/PI Qty </th>
                <th width="70" align="center">Rec. Qty </th>
                <?
				if($rate_hide_inventory!=1)
				{
					?>
                    <th width="60" align="center">Rate</th>
                	<th width="70" align="center">Amount</th>
                	<th width="100">Book Currency</th>
                    <?
				}
				?>
                
                <th width="50" align="center">Reject Qty</th>
                <th width="70" align="center">Rack</th>
                <th width="70" align="center">Shelf</th>
                <th width="70" align="center">Box</th>
                <th width="100" align="center">Remarks</th>
            </thead>
    	<?
		if($dataArray[0][csf('receive_basis')]==1 || $dataArray[0][csf('receive_basis')]==2)
		{
			if($dataArray[0][csf('receive_basis')]==2)
			{
				//if($db_type==0) $null_val="c.color_number_id";
				//else if($db_type==2) $null_val="nvl(c.color_number_id,0)";
				if($dataArray[0][csf('booking_without_order')]==0)
				{
				if($db_type==0) $null_val="c.color_number_id,c.item_color,c.gmts_sizes,";
				else if($db_type==2) $null_val="nvl(c.color_number_id,0) as color_number_id,nvl(c.item_color,0) as item_color,nvl(c.gmts_sizes,0) as gmts_sizes,";
				
					$sql_bookingqty =sql_select("select c.cons as wo_qnty,b.id as dtls_id,c.id,b.trim_group as item_group, $null_val c.description,c.brand_supplier,b.sensitivity from wo_booking_mst a, wo_booking_dtls b,wo_trim_book_con_dtls c where a.booking_no=b.booking_no and b.id=c.wo_trim_booking_dtls_id and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.booking_no='".$dataArray[0][csf('booking_no')]."'");
			
				}
				else
				{
					$sql_bookingqty = sql_select("select sum(a.trim_qty) as wo_qnty,a.trim_group as item_group,a.fabric_color as item_color,a.gmts_color as color_number_id,a.fabric_description as description from wo_non_ord_samp_booking_dtls a where  a.booking_no='".$dataArray[0][csf('booking_no')]."' and a.status_active=1 group by  a.trim_group,a.fabric_color,a.gmts_color,a.fabric_description");	
				}
			}
			else
			{
				$sql_bookingqty = sql_select("select sum(b.quantity) as wo_qnty, b.item_group, b.item_color, b.color_id as color_number_id, b.item_description as description, b.size_id, b.item_size 
				from com_pi_master_details a, com_pi_item_details b 
				where a.id=b.pi_id and a.id='".$dataArray[0][csf('booking_id')]."' and a.status_active=1 and b.status_active=1 
				group by b.item_group, b.item_color, b.color_id, b.item_description, b.size_id, b.item_size");	
			}
			foreach($sql_bookingqty as $b_qty)
			{
				$desc=trim(strtolower($b_qty[csf('description')]));
				if($dataArray[0][csf('receive_basis')]==2)
				{
					if($dataArray[0][csf('booking_without_order')]==0)
					{
						$booking_qty_arr[$b_qty[csf('item_group')]][$b_qty[csf('color_number_id')]][$b_qty[csf('item_color')]][$desc][$b_qty[csf('gmts_sizes')]][$b_qty[csf('sensitivity')]][trim($b_qty[csf('brand_supplier')])]+=$b_qty[csf('wo_qnty')];
					}
					else
					{
						$booking_qty_arr[$b_qty[csf('item_group')]][$b_qty[csf('color_number_id')]][$b_qty[csf('item_color')]][$desc]+=$b_qty[csf('wo_qnty')];
					}
				}
				else
				{
					$booking_qty_arr[$b_qty[csf('item_group')]][$b_qty[csf('color_number_id')]][$b_qty[csf('item_color')]][$desc][$b_qty[csf('size_id')]][$b_qty[csf('item_size')]]+=$b_qty[csf('wo_qnty')];
				}
			}
		 }
		/*echo "<pre>";
		print_r($booking_qty_arr);*/
		$lib_rack_arr=return_library_array( "select a.company_id,b.location_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,a.floor_room_rack_name,a.status_active, a.floor_room_rack_id,b.floor_room_rack_dtls_id from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.rack_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and a.company_id=$data[0] order by b.floor_id", "rack_id","floor_room_rack_name" ); 
		$lib_shelf_arr=return_library_array( "select a.company_id,b.location_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,a.floor_room_rack_name,a.status_active, a.floor_room_rack_id,b.floor_room_rack_dtls_id from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.shelf_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and a.company_id=$data[0] order by b.floor_id", "shelf_id","floor_room_rack_name" ); 
		$lib_bin_arr=return_library_array( "select a.company_id,b.location_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,a.floor_room_rack_name,a.status_active, a.floor_room_rack_id,b.floor_room_rack_dtls_id from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.bin_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and a.company_id=$data[0] order by b.floor_id", "bin_id","floor_room_rack_name" ); 

        $i=1; 
		$mst_id=$dataArray[0][csf('id')];
		if($db_type==0)
		{
			$sql_dtls="select b.item_group_id, b.item_description, b.gmts_color_id, b.item_color, b.item_size, b.gmts_size_id as gmts_size, b.sensitivity, b.brand_supplier, b.cons_uom, b.order_uom, group_concat(b.order_id) as order_id, group_concat(b.order_id_2) as order_id_2, max(b.floor) as floor, max(b.room_no) as room_no, max(b.rack_no) as room_no, max(b.self_no) as self_no, max(b.box_bin_no) as box_bin_no, sum(b.receive_qnty) as receive_qnty, avg(b.rate) as rate, sum(b.amount) as amount, sum(b.cons_qnty) as cons_qnty, sum(b.reject_receive_qnty) as reject_receive_qnty, max(b.remarks) as remarks
			from inv_trims_entry_dtls b left join inv_goods_placement c on c.dtls_id=b.id and c.entry_form=24 
			where b.mst_id='$mst_id' and b.status_active='1' and b.is_deleted='0'
			group by b.item_group_id, b.item_description, b.gmts_color_id, b.item_color, b.item_size, b.gmts_size_id, b.sensitivity, b.brand_supplier, b.cons_uom, b.order_uom";
		}
		else
		{
			$sql_dtls="select b.item_group_id, b.item_description, b.gmts_color_id, b.item_color, b.item_size, b.gmts_size_id as gmts_size, b.sensitivity, b.brand_supplier, b.cons_uom, b.order_uom, listagg( cast(b.order_id as varchar(4000)),',') within group (order by b.id) as order_id, listagg( cast(b.order_id_2 as varchar(4000)),',') within group (order by b.id) as order_id_2, max(b.floor) as floor, max(b.room_no) as room_no, max(b.rack_no) as room_no, max(b.self_no) as self_no, max(b.box_bin_no) as box_bin_no, sum(b.receive_qnty) as receive_qnty, avg(b.rate) as rate, sum(b.amount) as amount, sum(b.cons_qnty) as cons_qnty, sum(b.reject_receive_qnty) as reject_receive_qnty, max(b.remarks) as remarks 
			from inv_trims_entry_dtls b left join inv_goods_placement c on c.dtls_id=b.id and c.entry_form=24 
			where b.mst_id='$mst_id' and b.status_active='1' and b.is_deleted='0'
			group by b.item_group_id, b.item_description, b.gmts_color_id, b.item_color, b.item_size, b.gmts_size_id, b.sensitivity, b.brand_supplier, b.cons_uom, b.order_uom";
		}
		
		//echo $sql_dtls;
        $sql_result=sql_select($sql_dtls);
        foreach($sql_result as $row)
        {
			$order_ids=$row[csf('order_id')];
			if($row[csf('order_id_2')]!="") $order_ids.=",".$row[csf('order_id_2')];
			//$order_no=$row[csf('order_id')];
			//print_r($booking_qty_arr);
			$po_data[$row["PO_ID"]]["PO_NUMBER"]=$row["PO_NUMBER"];
			$po_data[$row["PO_ID"]]["BUYER_NAME"]=$row["BUYER_NAME"];
			$po_data[$row["PO_ID"]]["JOB_NO"]=$row["JOB_NO"];
			$po_data[$row["PO_ID"]]["STYLE_REF_NO"]=$row["STYLE_REF_NO"];
           	
			$order_ids_arr=array_unique(explode(",",$order_ids));
			$po_number=$buyerName=$job_number="";
			foreach($order_ids_arr as $po_id)
			{
				$po_number.=$po_data[$po_id]["PO_NUMBER"].",";
				$job_number=$po_data[$po_id]["JOB_NO"];
				$buyerName = $buyer_library[$po_data[$po_id]["BUYER_NAME"]];
			}
            $po_number=chop($po_number,",");
			
			//gmts_size item_size
			$descp=trim(strtolower($row[csf('item_description')]));
			if($dataArray[0][csf('receive_basis')]==2)
			{					
				if($dataArray[0][csf('booking_without_order')]==0)
				{
					$woorder_qty=$booking_qty_arr[$row[csf('item_group_id')]][$row[csf('gmts_color_id')]][$row[csf('item_color')]][$descp][$row[csf('gmts_size')]][$row[csf('sensitivity')]][trim($row[csf('brand_supplier')])];
					//$booking_qty_arr[$b_qty[csf('item_group')]][$b_qty[csf('color_number_id')]][$b_qty[csf('item_color')]][$b_qty[csf('description')]][$b_qty[csf('item_size')]][$b_qty[csf('gmts_size')]][$b_qty[csf('sensitivity')]][$b_qty[csf('brand_supplier')]]=$b_qty[csf('wo_qnty')];
				}
				else
				{
					$woorder_qty=$booking_qty_arr[$row[csf('item_group_id')]][$row[csf('gmts_color_id')]][$row[csf('item_color')]][$descp];	
				}
			}
			else
			{
				$woorder_qty=$booking_qty_arr[$row[csf('item_group_id')]][$row[csf('gmts_color_id')]][$row[csf('item_color')]][$descp][$row[csf('gmts_size')]][$row[csf('item_size')]];	
			}
			
			 if ($i%2==0)  
                $bgcolor="#E9F3FF";
            else
                $bgcolor="#FFFFFF";
			?>
			<tr bgcolor="<? echo $bgcolor; ?>">
				<td><? echo $i; ?></td>
				<td><p><? echo $item_library[$row[csf('item_group_id')]]; ?></p></td>
				<td><p><? echo $row[csf('item_description')]; ?></p></td>
				<td><p><? echo $buyerName; ?></p></td>
				<td><p><? echo $color_library[$row[csf('gmts_color_id')]]; ?></p></td>
				<td><p><? echo $color_library[$row[csf('item_color')]]; ?></p></td>
				<td><p><? echo $row[csf('item_size')]; ?></p></td>
				<td width="200" style="word-break:break-all;"><p><? echo $po_number; ?></p></td>
                <td><p><? echo $job_number; ?></p></td>
				<td align="center"><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?></td>
				<td align="right" title="<? echo $row[csf('item_group_id')].'='.$row[csf('gmts_color_id')].'='.$row[csf('item_color')].'='.$descp; ?>"><? 
				$total_woorder_qty+=$woorder_qty;
				echo number_format($woorder_qty,2,".",""); ?></td>
				<td align="right"><? echo number_format($row[csf('receive_qnty')],2,".",""); ?></td>
				<?
				if($rate_hide_inventory!=1)
				{
					?>
                    <td align="right"><? echo number_format($row[csf('rate')],4,'.',''); ?></td>
					<td align="right"><? echo number_format($row[csf('amount')],4,'.',''); ?></td>
					<td align="right"><? echo $book_currency= number_format($row[csf("amount")]*($dataArray[0][csf('exchange_rate')]),2,'.',''); ?></td>
                    <?
				}
				?>
				<td align="right"><? echo number_format($row[csf('reject_receive_qnty')],2,'.',''); ?></td>
				<td align="center"><p><? echo $lib_rack_arr[$row[csf('rack_no')]];//echo $row[csf('rack_no')]; ?></p></td>
				<td align="center"><p><? echo $lib_shelf_arr[$row[csf('self_no')]];//echo $row[csf('self_no')]; ?></p></td>
				<td align="center"><p><? echo $lib_bin_arr[$row[csf('box_bin_no')]]; //echo $row[csf('box_bin_no')]; ?></p></td>
				<td align="center"><p><? echo $row[csf('remarks')]; //echo $row[csf('box_bin_no')]; ?></p></td>
			</tr>
            <?
			$i++;
			$tot_rec_qty+=$row[csf('receive_qnty')];
			$tot_amount+=$row[csf('amount')];
			$tot_book_currency+=$book_currency;
			$tot_reject_qty+=$row[csf('reject_receive_qnty')];
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
                <td colspan="3" align="right"><b>Total :</b></td>
                <td align="right"><? echo number_format($total_woorder_qty,2); ?></td>
                <td align="right"><? echo number_format($tot_rec_qty,2); ?></td>
                <?
				if($rate_hide_inventory!=1)
				{
					?>
                    <td>&nbsp;</td>
                	<td align="right"><? echo number_format($tot_amount,4,'.',''); ?></td>                
                	<td align="right"><? echo number_format($tot_book_currency,2,'.',''); ?></td>
                    <?
				}
				?>

                <td align="right"><? echo $tot_reject_qty; ?></td>
                <td align="center">&nbsp;</td>
                <td align="center">&nbsp;</td>
                <td align="center">&nbsp;</td>
                <td align="center">&nbsp;</td>
            </tr>
       </table>
       <br>
       <?
		  echo signature_table(35, $data[0], "1250px");
	   ?>
	</div>
</div>
<?
exit();
}

if ($action=="trims_receive_entry_print_2") 
{
	extract($_REQUEST);
	$data=explode('__',$data);
	$variable_inventory_ref=explode("**",$data[3]);
	$rate_hide_inventory=$variable_inventory_ref[2];
	//print_r ($data);
	
	$buyer_name_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	//$supplier_library=return_library_array( "select id,supplier_name from lib_supplier", "id","supplier_name"  );
	$item_library=return_library_array( "select id, item_name from lib_item_group", "id", "item_name"  );
	//$pi_library=return_library_array( "select id, pi_number from com_pi_master_details", "id", "pi_number"  );
	$color_library=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name"  );
	$store_name_arr=return_library_array( "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id='$data[0]' and b.category_type=4 and a.status_active=1 and a.is_deleted=0 group by a.id, a.store_name order by a.store_name", "id", "store_name"  );

	$sql="select id, recv_number,item_category, receive_basis, receive_date, booking_id, booking_no, challan_no, challan_date, lc_no, source, store_id, supplier_id, currency_id, exchange_rate,booking_without_order,pay_mode,knitting_source, boe_mushak_challan_no, boe_mushak_challan_date from inv_receive_master where id='$data[1]' and status_active=1 and is_deleted=0 and entry_form=24 ";
	//echo $sql;
	$dataArray=sql_select($sql);
	$booking_without_order=$dataArray[0][csf('booking_without_order')];

	$wo_library=array();
	if ($booking_without_order==1)
	{
		$wo_library=return_library_array( "select id, pay_mode from wo_non_ord_samp_booking_mst where id='".$dataArray[0][csf('booking_id')]."'", "id", "pay_mode"  );
	}
	else
	{
		$wo_library=return_library_array( "select id, pay_mode from wo_booking_mst where id='".$dataArray[0][csf('booking_id')]."'", "id", "pay_mode"  );
	}
	
	if($dataArray[0][csf('knitting_source')]==3 || $dataArray[0][csf('knitting_source')]==5)
	{
		$supplier_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	}
	else
	{
		$supplier_library=return_library_array( "select id,supplier_name from lib_supplier", "id","supplier_name"  );
	}
	
	if($rate_hide_inventory!=1){
		$width=1120;
		$width_px='1020px';
	} 
	else{
		$width=1250;
		$width_px='1250px';
	} 
	?>
	<div style="width:<? echo $width_px; ?>;">
	    <table width="<? echo $width; ?>" cellspacing="0" align="right" border="0">
	        <tr>
	            <td colspan="7" align="center" style="font-size:xx-large"><strong><? echo $company_library[$data[0]]; ?></strong></td>
	        </tr>
	        <tr class="form_caption">
	            <td colspan="7" align="center">
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
	            <td colspan="7" align="center" style="font-size:x-large"><strong><u>Material Receiving Report</u></strong></center></td>
	        </tr>
	        <br>
	        <table cellspacing="0" width="1000" border="1" rules="all" class="">
		        <tr>
		            <td width="130"><strong>MRR/System ID:</strong></td>
		            <td width="230"><? echo $dataArray[0][csf('recv_number')]; ?></td>
		            <td width="110"><strong> Receive Basis :</strong></td>
		            <td><? echo $receive_basis_arr[$dataArray[0][csf('receive_basis')]]; ?></td>
		            <td ><strong>Received Date:</strong></td>
		            <td><? echo  change_date_format($dataArray[0][csf('receive_date')]); ?></td>
		        </tr>
		        <tr>
		            <td><strong>Challan No :</strong></td> <td width="175px"><? echo $dataArray[0][csf('challan_no')]; ?></td>
		            <td><strong>Currency:</strong></td><td width="175px" ><? echo $currency[$dataArray[0][csf('currency_id')]]; ?></td>
		            <td><strong>Store Name:</strong></td><td width="175px"><? echo $store_name_arr[$dataArray[0][csf('store_id')]]; ?></td>
		        </tr>
		        <tr>
		            <td><strong>Supplier:</strong></td><td width="175px"><? echo $supplier_library[$dataArray[0][csf('supplier_id')]]; ?></td>
		            <td><strong>L/C:</strong></td><td width="175px"><? echo $dataArray[0][csf('lc_no')]; ?></td>
		            <td><strong> Exchange Rate:</strong></td><td width="175px"><? echo $dataArray[0][csf('exchange_rate')]; ?></td>
		        </tr>
		        
		        <tr>
		            <td><strong>WO/PI:</strong></td><td width="175px"><? echo $dataArray[0][csf('booking_no')]; ?></td>
		            <td><strong>Source:</strong></td><td width="175px"><? echo $source[$dataArray[0][csf('source')]]; ?></td>
		            <td><strong>Pay mode:</strong></td><td width="175px"><? if ($dataArray[0][csf('receive_basis')]==2) echo $pay_mode[$wo_library[$dataArray[0][csf('booking_id')]]]; else echo ''; ?></td>
		        </tr>
		        <tr>
		            <td><strong>Item Catagory:</strong></td><td width="175px"><? echo $item_category[$dataArray[0][csf('item_category')]]; ?></td>
					<td><strong>BOE/Mushak Challan No:</strong></td><td width="175px"><? echo $dataArray[0][csf('boe_mushak_challan_no')]; ?></td>
					<td><strong>BOE/Mushak Challan Date:</strong></td><td width="175px"><? echo $dataArray[0][csf('boe_mushak_challan_date')]; ?></td>
		        </tr>
	    	</table>
	    </table>
	    <br>
		<div style="width:100%;">
	        <table cellspacing="0" width="<? echo $width; ?>"  border="1" rules="all" class="rpt_table" >
	            <thead bgcolor="#dddddd">
	                <th width="30">SL</th>
	                <th width="110" align="center">Job/PO/Buyer/Style</th>
	                <th width="110" align="center">Item Group</th>
	                <th width="140" align="center">Item Des.</th>
					<th width="70" align="center">Item Color</th>
                    <th width="70" align="center">Item Size</th>
	                <th width="40" align="center">UOM</th>
	                <th width="70" align="center">WO Qty </th>
	                <th width="70" align="center">Prev. Rec. Qty </th>
	                <th width="70" align="center">Curr. Rec. Qty </th>
	                <th width="70" align="center">Tot. Rec. Qty </th>
	                <th width="70" align="center">WO Balance</th>
	                <?
					if($rate_hide_inventory!=1)
					{
						?>
	                    <th width="60" align="center">Rate</th>
	                	<th width="70" align="center">Amount</th>
	                    <?
					}
					?>

	                <th width="70">Comments</th>
	                <th width="60" align="center">Room No</th>
	                <th width="60" align="center">Rack No</th>
	                <th width="60" align="center">Shelf No</th>
	                <th width="60" align="center">Box/Bin</th>
	            </thead>
	    	<? 

		 	if($dataArray[0][csf('receive_basis')]==1 || $dataArray[0][csf('receive_basis')]==2)
		 	{
				if($dataArray[0][csf('receive_basis')]==2)
				{
					if($dataArray[0][csf('booking_without_order')]==0)
					{
						if($db_type==0) $null_val="c.color_number_id,c.item_color,c.item_size,c.gmts_sizes,";
						else if($db_type==2) $null_val="nvl(c.color_number_id,0) as color_number_id,nvl(c.item_color,0) as item_color,nvl(c.gmts_sizes,0) as gmts_sizes,c.item_size,";

						$sql_bookingqty =sql_select("select c.cons as wo_qnty, b.id as dtls_id, c.id, b.trim_group as item_group, $null_val c.description as description, c.brand_supplier, b.po_break_down_id as po_id, b.sensitivity 
						from wo_booking_mst a, wo_booking_dtls b, wo_trim_book_con_dtls c 
						where a.booking_no=b.booking_no and b.id=c.wo_trim_booking_dtls_id and a.booking_no='".$dataArray[0][csf('booking_no')]."' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
					}
					else
					{
						$sql_bookingqty = sql_select("select sum(a.trim_qty) as wo_qnty, a.trim_group as item_group, a.fabric_color as item_color, a.gmts_color as color_number_id, a.fabric_description as description 
						from wo_non_ord_samp_booking_dtls a where  a.booking_no='".$dataArray[0][csf('booking_no')]."' and a.status_active=1 and a.is_deleted=0
						group by  a.trim_group,a.fabric_color,a.gmts_color,a.fabric_description");	
					}
				}
				else
				{
					$sql_bookingqty = sql_select("select b.QUANTITY as wo_qnty, b.item_group, b.item_color, b.COLOR_ID as color_number_id, b.item_description as description, b.order_id as po_break_down_id, b.size_id, b.item_size 
					from com_pi_master_details a, com_pi_item_details b
					where a.id=b.pi_id and a.id='".$dataArray[0][csf('booking_id')]."' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");	
				}
				
				foreach($sql_bookingqty as $b_qty)
				{
					$desc=trim(strtolower($b_qty[csf('description')]));
					if($dataArray[0][csf('receive_basis')]==2)
					{
						if($dataArray[0][csf('booking_without_order')]==0)
						{
							//if($b_qty[csf('sensitivity')]=='' || $b_qty[csf('sensitivity')]==0) $sensitivity=0;
							//if($b_qty[csf('gmts_size')]=='' || $b_qty[csf('gmts_size')]==0) $gmts_size=0;
							$booking_qty_arr[$b_qty[csf('po_id')]][$b_qty[csf('item_group')]][$b_qty[csf('color_number_id')]][$b_qty[csf('item_color')]][$desc][trim(strtolower($b_qty[csf('item_size')]))][$b_qty[csf('sensitivity')]][trim($b_qty[csf('brand_supplier')])]+=$b_qty[csf('wo_qnty')];
						}
						else
						{
							$booking_qty_arr[$b_qty[csf('item_group')]][$b_qty[csf('color_number_id')]][$b_qty[csf('item_color')]][$desc]+=$b_qty[csf('wo_qnty')];	
						}
					}
					else
					{
						$booking_qty_arr[$b_qty[csf('po_break_down_id')]][$b_qty[csf('item_group')]][$b_qty[csf('color_number_id')]][$b_qty[csf('item_color')]][$desc][$b_qty[csf('size_id')]][$b_qty[csf('item_size')]]+=$b_qty[csf('wo_qnty')];
						
					}
				
				}
			}
			//echo '<pre>';print_r($booking_qty_arr);
	        //$sql_dtls="select b.id,b.mst_id,b.item_group_id, b.item_description, b.order_id, b.gmts_color_id, b.item_color, b.item_size, b.receive_qnty from inv_receive_master a,inv_trims_entry_dtls b left join inv_goods_placement c on c.dtls_id=b.id and c.entry_form=24 where a.booking_no='".$dataArray[0][csf('booking_no')]."' and b.mst_id=a.id and b.status_active='1' and b.is_deleted='0'";
			if($booking_without_order==0)
			{
	        	$sql_dtls="select b.id, b.mst_id, b.item_group_id, b.item_description, c.po_breakdown_id as order_id, b.gmts_color_id, b.item_color, b.item_size, c.quantity as receive_qnty, b.brand_supplier, b.gmts_size_id, b.rate
				from inv_receive_master a, inv_trims_entry_dtls b, order_wise_pro_details c 
				where c.dtls_id=b.id and c.entry_form=24 and b.mst_id=a.id and b.status_active='1' and b.is_deleted='0' and a.booking_no='".$dataArray[0][csf('booking_no')]."'";
			}
			else
			{
				 $sql_dtls="select b.id, b.mst_id, b.item_group_id, b.item_description, b.order_id, b.gmts_color_id, b.item_color, b.item_size, b.receive_qnty, b.brand_supplier, b.gmts_size_id, b.rate
				 from inv_receive_master a, inv_trims_entry_dtls b 
				 where a.entry_form=24 and b.mst_id=a.id and b.status_active='1' and b.is_deleted='0' and a.booking_no='".$dataArray[0][csf('booking_no')]."'";
			}
	//		 	echo $sql_dtls;
		 	$po_breakdown_ids='';
			$sql_result=sql_select($sql_dtls);
			$sql_result_audited=sql_select($sql_dtls);
			foreach($sql_result as $rows)
			{
				//$prev_rcv_data[$row[csf("po_breakdown_id")]][$row[csf("item_group_id")]][trim($row[csf("item_description")])][$row[csf("brand_supplier")]][$row[csf("gmts_color_id")]][$row[csf("item_color")]][$row[csf("gmts_size_id")]][$row[csf("item_size")]][$row[csf("rate")]]["qnty"]+=$row[csf("qnty")];
				$key=$rows[csf('order_id')].$rows[csf('item_group_id')].trim(strtolower($rows[csf('item_description')])).$rows[csf('brand_supplier')].$rows[csf('gmts_color_id')].$rows[csf('item_color')].$rows[csf('gmts_size_id')].$rows[csf('item_size')].$rows[csf('rate')];
				if($rows[csf('mst_id')] != $dataArray[0][csf('id')])
				{
					$prev_qty_arr[$key]+=$rows[csf('receive_qnty')];
				}
				$tot_qty_arr[$key]+=$rows[csf('receive_qnty')];
				$po_breakdown_ids .=$rows[csf('order_id')].',';
				//$prod_ids .=$row[csf('prod_id')].',';
			}
	//			echo "<pre>";
	//			print_r($tot_qty_arr);
			$po_breakdown_ids=implode(",",array_unique(explode(",",chop($po_breakdown_ids,','))));
			$rcvRtn_qty_sql = "SELECT b.po_breakdown_id ,sum( b.quantity ) as recv_return_qty, a.item_group_id as ITEM_GROUP_ID, a.item_description as ITEM_DESCRIPTION, a.color as COLOR, a.item_color as ITEM_COLOR, a.gmts_size as GMTS_SIZE   from product_details_master a, inv_transaction t, order_wise_pro_details b, wo_po_break_down c, wo_po_details_master d
			where a.id=b.prod_id and a.id=t.prod_id and t.id=b.trans_id and b.po_breakdown_id=c.id and c.job_no_mst=d.job_no and a.id=b.prod_id and  b.po_breakdown_id in ($po_breakdown_ids) and a.item_category_id=4 and b.entry_form in(49) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  
			group by b.po_breakdown_id, a.item_group_id , a.item_description, a.color, a.item_color, a.gmts_size";
			// echo $rcvRtn_qty_sql;die;
			$totalRcvRtnQty_arr=array();
			$rcvRtn_qtyArray=sql_select($rcvRtn_qty_sql); 
			foreach($rcvRtn_qtyArray as $row)
			{ 
				$key=$rows[csf('po_breakdown_id')].$rows['item_group_id'].strtoupper($rows['ITEM_DESCRIPTION']).$rows['COLOR'].$rows['ITEM_COLOR'].$rows['GMTS_SIZE'];
				$totalRcvRtnQty_arr[$key]=$row[csf('recv_return_qty')];
			}

			
	        $i=1; 
	        $mst_id=$dataArray[0][csf('id')];
			if($booking_without_order==0)
			{
				$sql_dtls="select b.item_group_id, b.item_description, c.po_breakdown_id as order_id, b.gmts_color_id, b.item_color, b.item_size, b.sensitivity, b.brand_supplier, b.order_uom, b.cons_uom, max(b.room_no) as room_no, max(b.rack_no) as rack_no, max(b.self_no) as self_no, max(b.box_bin_no) as box_bin_no, sum(b.cons_qnty) as cons_qnty, sum(c.quantity) as receive_qnty, b.gmts_size_id, b.rate, sum(c.quantity*b.rate) as amount, sum(c.reject_qty) as reject_receive_qnty,b.remarks
				from inv_trims_entry_dtls b, order_wise_pro_details c 
				where c.dtls_id=b.id and c.entry_form=24 and b.mst_id='$mst_id' and b.status_active='1' and b.is_deleted='0'
				group by b.item_group_id, b.item_description, c.po_breakdown_id, b.gmts_color_id, b.item_color, b.item_size, b.sensitivity, b.brand_supplier, b.order_uom, b.cons_uom,b.remarks, b.gmts_size_id, b.rate";
	        	
			}
			else
			{
	        	$sql_dtls="select b.item_group_id, b.item_description, b.gmts_color_id, b.item_color, b.item_size, b.sensitivity, b.brand_supplier, b.cons_uom, b.order_uom, b.room_no, b.rack_no, b.self_no, b.box_bin_no, b.order_id, b.cons_qnty, b.receive_qnty, b.gmts_size_id, b.rate, b.amount, b.reject_receive_qnty ,b.remarks
				from inv_trims_entry_dtls b  
				where b.mst_id='$mst_id' and b.status_active='1' and b.is_deleted='0'";
			}
	    	//echo $sql_dtls;die;
			$lib_room_arr=return_library_array( "select a.company_id,b.location_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,a.floor_room_rack_name,a.status_active, a.floor_room_rack_id,b.floor_room_rack_dtls_id from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.room_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and a.company_id=$data[0] order by b.floor_id", "room_id","floor_room_rack_name" ); 
			$lib_rack_arr=return_library_array( "select a.company_id,b.location_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,a.floor_room_rack_name,a.status_active, a.floor_room_rack_id,b.floor_room_rack_dtls_id from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.rack_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and a.company_id=$data[0] order by b.floor_id", "rack_id","floor_room_rack_name" ); 
			$lib_shelf_arr=return_library_array( "select a.company_id,b.location_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,a.floor_room_rack_name,a.status_active, a.floor_room_rack_id,b.floor_room_rack_dtls_id from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.shelf_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and a.company_id=$data[0] order by b.floor_id", "shelf_id","floor_room_rack_name" ); 
			$lib_bin_arr=return_library_array( "select a.company_id,b.location_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,a.floor_room_rack_name,a.status_active, a.floor_room_rack_id,b.floor_room_rack_dtls_id from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.bin_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and a.company_id=$data[0] order by b.floor_id", "bin_id","floor_room_rack_name" ); 
	        $sql_result=sql_select($sql_dtls);
			//echo "<pre>";print_r($booking_qty_arr);die;
	        foreach($sql_result as $row)
	        {
				$key=$row[csf('order_id')].$row[csf('item_group_id')].trim(strtolower($row[csf('item_description')])).$row[csf('brand_supplier')].$row[csf('gmts_color_id')].$row[csf('item_color')].$row[csf('gmts_size_id')].$row[csf('item_size')].$row[csf('rate')];
	            if ($i%2==0)  
	                $bgcolor="#E9F3FF";
	            else
	                $bgcolor="#FFFFFF";
	                
	                $order_no=$row[csf('order_id')];
	                if($db_type==0)
	                { 
	                    $po_number = return_field_value("po_number as po_number","wo_po_break_down"," id in ('$order_no')","po_number");
						$po_number_job = return_field_value("a.job_no as job_no","wo_po_details_master a,wo_po_break_down b"," a.job_no=b.job_no_mst and b.id in ('$order_no')","job_no");
						$po_number_buyer = return_field_value("a.buyer_name as buyer_name","wo_po_details_master a,wo_po_break_down b"," a.job_no=b.job_no_mst and b.id in ('$order_no')","buyer_name");
						$style_no = return_field_value("a.style_ref_no as style_ref_no","wo_po_details_master a,wo_po_break_down b"," a.job_no=b.job_no_mst and b.id in ($order_no)","style_ref_no");
	                }
	                else
	                {
	                    $po_number = return_field_value("po_number as po_number","wo_po_break_down"," id in ($order_no)","po_number");
						$po_number_job = return_field_value("a.job_no as job_no","wo_po_details_master a,wo_po_break_down b"," a.job_no=b.job_no_mst and b.id in ($order_no)","job_no");
						$po_number_buyer = return_field_value("a.buyer_name as buyer_name","wo_po_details_master a,wo_po_break_down b","a.job_no=b.job_no_mst and b.id in ($order_no)","buyer_name");
						$style_no = return_field_value("a.style_ref_no as style_ref_no","wo_po_details_master a,wo_po_break_down b"," a.job_no=b.job_no_mst and b.id in ($order_no)","style_ref_no");
							
	                }
					//echo  $po_number.'aaa';
					//$po_number=implode(",",array_unique(explode(",",$po_number)));
					$descp=trim(strtolower($row[csf('item_description')]));
					//echo $descp.'system';
					if($dataArray[0][csf('receive_basis')]==2)
					{
						if($dataArray[0][csf('booking_without_order')]==0)
						{
							// if($row[csf('sensitivity')]=='' || $row[csf('sensitivity')]==0) $sensitivity=0;
							//if($row[csf('gmts_size')]=='' || $row[csf('gmts_size')]==0) $gmts_size=0;
							
							// $po_no=explode(",",$order_no);
							// $woorder_qty=0;
							// foreach($po_no as $po_id)
							 //{
								$woorder_qty=$booking_qty_arr[$order_no][$row[csf('item_group_id')]][$row[csf('gmts_color_id')]][$row[csf('item_color')]][$descp][trim(strtolower($row[csf('item_size')]))][$row[csf('sensitivity')]][trim($row[csf('brand_supplier')])]; 
								//if($woorder_qty<1) echo $order_no."=".$row[csf('item_group_id')]."=".$row[csf('gmts_color_id')]."=".$row[csf('item_color')]."=".$descp."=".trim(strtolower($row[csf('item_size')]))."=".$row[csf('sensitivity')]."=".trim($row[csf('brand_supplier')])."<br>";
							 //}
							
						}
						else
						{
						 	$woorder_qty=$booking_qty_arr[$row[csf('item_group_id')]][$row[csf('gmts_color_id')]][$row[csf('item_color')]][$descp];	
						}
					}
					else
					{
						 $woorder_qty=$booking_qty_arr[$order_no][$row[csf('item_group_id')]][$row[csf('gmts_color_id')]][$row[csf('item_color')]][$descp][$row[csf('gmts_size_id')]][$row[csf('item_size')]];	
					}
					//$buyer=explode(",",$po_number_buyer);
					//$buyer_id='';
					//foreach($buyer as $bid)
					//{
						if($buyer_id=='') $buyer_id=$buyer_name_library[$bid]; else $buyer_id.=",".$buyer_name_library[$bid];
					//}
					$buyer_id=$buyer_name_library[$po_number_buyer];//$po_number_buyer

					$rcvRtnQty=$totalRcvRtnQty_arr[$key];
					$prevRcvQty=$prev_qty_arr[$key]-$rcvRtnQty;
					//$prevRcvQty=$tot_qty_arr[$key]-$rcvRtnQty;
					//$prevRcvQty=$prev_qty_arr[$key];
	//					echo $rcvRtnQty.'++'.$prev_qty_arr[$key];
					$uom_check_arr[$row[csf('order_uom')]]=$row[csf('order_uom')];
	                ?>
	                <tr bgcolor="<? echo $bgcolor; ?>">
	                    <td><? echo $i; ?></td>
	                    <td><p><? echo $po_number_job.'<br>'.$po_number.'<br>'.$buyer_id.'<br>'.$style_no;//$item_category[4]; ?></p></td>
	                    <td><p><? echo $item_library[$row[csf('item_group_id')]] ?></p></td>
	                    <td><p><? echo $row[csf('item_description')]; ?></p></td>
						<td><p><? echo $color_library[$row[csf('item_color')]]; ?></p></td>
				        <td><p><? echo $row[csf('item_size')]; ?></p></td>
	                    <td><p><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?></p></td>
	                    <td align="right"><div style="word-wrap:break-word;">
							<? //$woorder_qty=$booking_qty_arr[$row[csf('item_group_id')]][$row[csf('gmts_color_id')]][$row[csf('item_color')]][$row[csf('item_description')]];
							//$total_woorder_qty+=$woorder_qty;
	                         echo number_format($woorder_qty,2,".","");; 
	                         ?>
	                     </div></td>
	                    <td align="right"><? echo number_format($prevRcvQty,2,".",""); ?></td>
	                    <td align="right"><? echo number_format($row[csf('receive_qnty')],2,".",""); ?></td>
	                    <td align="right"><? echo number_format($prevRcvQty+$row[csf('receive_qnty')],2,".",""); ?></td>
	                   
	                    <td align="right"><? echo number_format($woorder_qty-($prevRcvQty+$row[csf('receive_qnty')]),4,'.',''); ?></td>
	                    <?
						if($rate_hide_inventory!=1)
						{
							?>
		                    <td align="right"><? echo number_format($row[csf('rate')],4,'.',''); ?></td>
	                    	<td align="right"><? echo number_format($row[csf('amount')],2,'.',''); ?></td>
		                    <?
						}
						?>

	                    <td> <?php echo $row[csf('remarks')]; ?></td>
	                    <td align="right"><? echo $lib_room_arr[$row[csf('room_no')]]; ?></td>
	                    <td align="right"><? echo $lib_rack_arr[$row[csf('rack_no')]]; ?></td>
	                    <td align="right"><? echo $lib_shelf_arr[$row[csf('self_no')]]; ?></td>
	                    <td align="right"><? echo $lib_bin_arr[$row[csf('box_bin_no')]]; ?></td>
	                   
	                </tr>
	            <?
				$i++;
				$tot_woorder_qty+=$woorder_qty;
				$tot_prevRcvQty+=$prevRcvQty;
				$tot_rec_qty+=$row[csf('receive_qnty')];
				$tot_amount+=$row[csf('amount')];
				$tot_reject_qty+=$row[csf('reject_receive_qnty')];
				$col_tot_rcv+=$prevRcvQty+$row[csf('receive_qnty')];
	        }
	       ?>
	            <tr bgcolor="#dddddd">
	                <td>&nbsp;</td>
	                <td>&nbsp;</td>
	                <td>&nbsp;</td>
	                <td colspan="4" align="right"><b>Total :</b></td>
	                <td align="right"><?  echo number_format($tot_woorder_qty,2); ?></td>
	                <td align="right"><? echo number_format($tot_prevRcvQty,2); ?></td>
	                <td align="right"><? echo number_format($tot_rec_qty,2); ?></td>
	                <td align="right"><? echo number_format($col_tot_rcv,2); ?></td>
	                <td>&nbsp;</td>
	                <?
					if($rate_hide_inventory!=1)
					{
						?>
	                    <td>&nbsp;</td>
	            		<td align="right"><? echo number_format($tot_amount,2,'.',''); ?></td>
	                    <?
					}
					?>
	                <td align="center">&nbsp;</td>
	                <td align="center">&nbsp;</td>
	                <td align="center">&nbsp;</td>
	                <td align="center">&nbsp;</td>
	                <td align="center">&nbsp;</td>
	            </tr>
	       </table>
	   		
	       <br>
	       <?
			  echo signature_table(35, $data[0], "1010px");
		   ?>
		</div>
	</div>
	<?
	exit();
}


if ($action=="trims_receive_entry_print_3") ////Not Use
{
	extract($_REQUEST);
	$data=explode('*',$data);
	//print_r ($data);
	
	$buyer_name_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	//$supplier_library=return_library_array( "select id,supplier_name from lib_supplier", "id","supplier_name"  );
	$item_library=return_library_array( "select id, item_name from lib_item_group", "id", "item_name"  );
	//$pi_library=return_library_array( "select id, pi_number from com_pi_master_details", "id", "pi_number"  );
	$wo_library=return_library_array( "select id, booking_no from wo_booking_mst", "id", "booking_no"  );
	$color_library=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name"  );
	$store_name_arr=return_library_array( "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id='$data[0]' and b.category_type=4 and a.status_active=1 and a.is_deleted=0 group by a.id, a.store_name order by a.store_name", "id", "store_name"  );

	$sql="select id, recv_number,item_category, receive_basis, receive_date, booking_id, booking_no, challan_no, challan_date, lc_no, source, store_id, supplier_id, currency_id, exchange_rate,booking_without_order,pay_mode,knitting_source from inv_receive_master where id='$data[1]' and status_active=1 and is_deleted=0 and entry_form=24 ";
	//echo $sql;
	$dataArray=sql_select($sql);
	if($dataArray[0][csf('knitting_source')]==3 || $dataArray[0][csf('knitting_source')]==5)
	{
		$supplier_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	}
	else
	{
		$supplier_library=return_library_array( "select id,supplier_name from lib_supplier", "id","supplier_name"  );
	}
	
	?>
	<div style="width:1030px;">
    <table width="1010" cellspacing="0" align="right" border="0">
        <tr>
            <td colspan="7" align="center" style="font-size:xx-large"><strong><? echo $company_library[$data[0]]; ?></strong></td>
        </tr>
        <tr class="form_caption">
            <td colspan="7" align="center">
				<?
					$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]"); 
					foreach ($nameArray as $result)
					{ 
					 echo $result['plot_no'].' '.$result['level_no'].' '.$result['road_no'].' '.$result['block_no'].' '.$result['city'].' '.$result['zip_code'].' '.$result['province'].' '.$country_arr[$result['country_id']];
					}
                ?> 
            </td>
        </tr>
        <tr>
            <td colspan="7" align="center" style="font-size:x-large"><strong><u>Material Receiving Report</u></strong></center></td>
        </tr>
        <tr>
            <td width="130"><strong>MRR/System ID:</strong></td>
            <td width="230"><? echo $dataArray[0][csf('recv_number')]; ?></td>
            <td width="110"><strong> Receive Basis :</strong></td>
            <td><? echo $receive_basis_arr[$dataArray[0][csf('receive_basis')]]; ?></td>
            <td ><strong>Received Date:</strong></td>
            <td><? echo  change_date_format($dataArray[0][csf('receive_date')]); ?></td>
        </tr>
        <tr>
            <td><strong>Challan No :</strong></td> <td width="175px"><? echo $dataArray[0][csf('challan_no')]; ?></td>
            <td><strong>Currency:</strong></td><td width="175px" ><? echo $currency[$dataArray[0][csf('currency_id')]]; ?></td>
            <td><strong>Store Name:</strong></td><td width="175px"><? echo $store_name_arr[$dataArray[0][csf('store_id')]]; ?></td>
        </tr>
        <tr>
            <td><strong>Supplier:</strong></td><td width="175px"><? echo $supplier_library[$dataArray[0][csf('supplier_id')]]; ?></td>
            <td><strong>L/C:</strong></td><td width="175px"><? echo $supplier_library[$dataArray[0][csf('lc_no')]]; ?></td>
            <td><strong> Exchange Rate:</strong></td><td width="175px"><? echo $dataArray[0][csf('exchange_rate')]; ?></td>
        </tr>
        
        <tr>
            <td><strong>WO/PI:</strong></td><td width="175px"><? echo $dataArray[0][csf('booking_no')]; ?></td>
            <td><strong>Source:</strong></td><td width="175px"><? echo $source[$dataArray[0][csf('source')]]; ?></td>
            <td><strong>Pay mode:</strong></td><td width="175px"><? echo $pay_mode[$dataArray[0][csf('pay_mode')]]; ?></td>
        </tr>
        <tr>
            <td><strong>Item Catagory</strong></td><td width="175px"><? echo $item_category[$dataArray[0][csf('item_category')]]; ?></td>
        </tr>
    </table>
    <br>
	<div style="width:100%;">
        <table align="right" cellspacing="0" width="1010"  border="1" rules="all" style="margin-left:-100px;" class="rpt_table" >
            <thead bgcolor="#dddddd" align="center">
                <th width="30">SL</th>
                <th width="110" align="center">Job/PO/Buyer</th>
                <th width="110" align="center">Item Group</th>
                <th width="140" align="center">Item Des.</th>
                <th width="40" align="center">UOM</th>
                <th width="70" align="center">WO/PI Qty </th>
                <th width="70" align="center">Prev. Rec. Qty </th>
                <th width="70" align="center">Curr. Rec. Qty </th>
                <th width="70" align="center">Tot. Rec. Qty </th>
                <th width="70" align="center">WO/PI Blance</th>
                <th width="60" align="center">Rate</th>
                <th width="70" align="center">Amount</th>
                <th>Comments</th>
            </thead>
    		<?
		 
		 if($dataArray[0][csf('receive_basis')]==1 || $dataArray[0][csf('receive_basis')]==2)
		 {
			if($dataArray[0][csf('receive_basis')]==2)
			{
				if($dataArray[0][csf('booking_without_order')]==0)
				{
					if($db_type==0) $null_val="c.color_number_id,c.item_color,c.item_size,c.gmts_sizes,";
				else if($db_type==2) $null_val="nvl(c.color_number_id,0) as color_number_id,nvl(c.item_color,0) as item_color,nvl(c.gmts_sizes,0) as gmts_sizes,c.item_size,";
				
				$sql_bookingqty =sql_select("select c.cons as wo_qnty,b.id as dtls_id,c.id,b.trim_group as item_group, $null_val c.description,c.brand_supplier,b.po_break_down_id as po_id,b.sensitivity from wo_booking_mst a, wo_booking_dtls b,wo_trim_book_con_dtls c where a.booking_no=b.booking_no and b.id=c.wo_trim_booking_dtls_id and a.booking_no='".$dataArray[0][csf('booking_no')]."'");
			
				}
				else
				{
				$sql_bookingqty = sql_select("select sum(a.trim_qty) as wo_qnty,a.trim_group as item_group,a.fabric_color as item_color,a.gmts_color as color_number_id,a.fabric_description as description from wo_non_ord_samp_booking_dtls a where  a.booking_no='".$dataArray[0][csf('booking_no')]."' group by  a.trim_group,a.fabric_color,a.gmts_color,a.fabric_description");	
				}
			}
			else
			{
			$sql_bookingqty = sql_select("select sum(b.quantity) as wo_qnty,b.item_group,b.item_color,b.color_id as color_number_id,b.item_description as description from  com_pi_master_details a,com_pi_item_details b where a.id=b.pi_id and a.id='".$dataArray[0][csf('booking_id')]."' group by b.item_group,b.item_color,b.color_id,b.item_description");	
			}
			foreach($sql_bookingqty as $b_qty)
			{
				
				if($dataArray[0][csf('receive_basis')]==2)
				{
					if($dataArray[0][csf('booking_without_order')]==0)
					{
					//if($b_qty[csf('sensitivity')]=='' || $b_qty[csf('sensitivity')]==0) $sensitivity=0;
					//if($b_qty[csf('gmts_size')]=='' || $b_qty[csf('gmts_size')]==0) $gmts_size=0;
					
					
					$booking_qty_arr[$b_qty[csf('po_id')]][$b_qty[csf('item_group')]][$b_qty[csf('color_number_id')]][$b_qty[csf('item_color')]][$b_qty[csf('description')]][$b_qty[csf('item_size')]][$b_qty[csf('sensitivity')]][$b_qty[csf('brand_supplier')]]=$b_qty[csf('wo_qnty')];
					}
					else
					{
					$booking_qty_arr[$b_qty[csf('item_group')]][$b_qty[csf('color_number_id')]][$b_qty[csf('item_color')]][$b_qty[csf('description')]]=$b_qty[csf('wo_qnty')];	
					}
				}
				else
				{
					$booking_qty_arr[$b_qty[csf('item_group')]][$b_qty[csf('color_number_id')]][$b_qty[csf('item_color')]][$b_qty[csf('description')]]=$b_qty[csf('wo_qnty')];
				}
			
			}
		 }
	//print_r($booking_qty_arr);
        //$sql_dtls="select b.id,b.mst_id,b.item_group_id, b.item_description, b.order_id, b.gmts_color_id, b.item_color, b.item_size, b.receive_qnty from inv_receive_master a,inv_trims_entry_dtls b left join inv_goods_placement c on c.dtls_id=b.id and c.entry_form=24 where a.booking_no='".$dataArray[0][csf('booking_no')]."' and b.mst_id=a.id and b.status_active='1' and b.is_deleted='0'";
        $sql_dtls="select b.id,b.mst_id,b.item_group_id, b.item_description, b.order_id, b.order_id_2, b.gmts_color_id, b.item_color, b.item_size, b.receive_qnty from inv_receive_master a,inv_trims_entry_dtls b left join inv_goods_placement c on c.dtls_id=b.id and c.entry_form=24 where  b.mst_id=a.id and b.status_active='1' and b.is_deleted='0' and a.booking_no='".$dataArray[0][csf('booking_no')]."'";
	 
	 
		$sql_result=sql_select($sql_dtls);
		foreach($sql_result as $rows)
		{
			$order_ids=$rows[csf('order_id')];
			if($rows[csf('order_id_2')]!="") $order_ids.=",".$rows[csf('order_id_2')];
			$key=$order_ids.$rows[csf('item_group_id')].$rows[csf('item_description')].$rows[csf('gmts_color_id')].$rows[csf('item_color')].$rows[csf('item_size')];
			if($rows[csf('mst_id')] < $dataArray[0][csf('id')])
			{
				$prev_qty_arr[$key]+=$rows[csf('receive_qnty')];
			}	 
			$tot_qty_arr[$key]+=$rows[csf('receive_qnty')];	 
		}

	
	/*	$sql_result=sql_select( "select id,item_group_id,item_description, item_code from product_details_master where item_category_id =4");
		foreach($sql_result as $rows)
		{
			$key=$rows[csf('item_description')].$rows[csf('item_group_id')];
			$item_code_arr[$key]=$rows[csf('item_code')];	 
		}*/
	//print_r($item_code_arr);
	
        $i=1; 
        $mst_id=$dataArray[0][csf('id')];
        $sql_dtls="select b.id, b.item_group_id, b.item_description, b.order_id, b.order_id_2, b.gmts_color_id, b.item_color, b.item_size, b.cons_qnty,b.sensitivity,b.brand_supplier, b.cons_uom, b.receive_qnty, b.rate, b.amount, b.reject_receive_qnty,b.prod_id,
        c.room_no, c.rack_no, c.self_no, c.box_bin_no, c.ctn_no, c.ctn_qnty from inv_trims_entry_dtls b left join inv_goods_placement c on c.dtls_id=b.id and c.entry_form=24 where b.mst_id='$mst_id' and b.status_active='1' and b.is_deleted='0'";
       //echo $sql_dtls;
        $sql_result=sql_select($sql_dtls);
        foreach($sql_result as $row)
        {
			$order_ids=$row[csf('order_id')];
			if($row[csf('order_id_2')]!="") $order_ids.=",".$row[csf('order_id_2')];
			$key=$order_ids.$row[csf('item_group_id')].$row[csf('item_description')].$row[csf('gmts_color_id')].$row[csf('item_color')].$row[csf('item_size')];
            if ($i%2==0)  
                $bgcolor="#E9F3FF";
            else
                $bgcolor="#FFFFFF";
                
                $order_no=$row[csf('order_id')];
                if($db_type==0)
                {
                    $po_number = return_field_value("group_concat(po_number) as po_number","wo_po_break_down"," id in ($order_ids)","po_number");
					$po_number_job = return_field_value("group_concat(a.job_no) as job_no","wo_po_details_master a,wo_po_break_down b"," a.job_no=b.job_no_mst and b.id in ($order_ids)","job_no");
					$po_number_buyer = return_field_value("group_concat(a.buyer_name) as buyer_name","wo_po_details_master a,wo_po_break_down b"," a.job_no=b.job_no_mst and b.id in ($order_ids)","buyer_name");;
                }
                else
                {
                    $po_number = return_field_value("LISTAGG(po_number, ',') WITHIN GROUP (ORDER BY id) as po_number","wo_po_break_down"," id in ($order_ids)","po_number");
					$po_number_job = return_field_value("LISTAGG(a.job_no, ',') WITHIN GROUP (ORDER BY a.job_no) as job_no","wo_po_details_master a,wo_po_break_down b"," a.job_no=b.job_no_mst and b.id in ($order_ids)","job_no");
					$po_number_buyer = return_field_value("LISTAGG(a.buyer_name, ',') WITHIN GROUP (ORDER BY a.job_no) as buyer_name","wo_po_details_master a,wo_po_break_down b","a.job_no=b.job_no_mst and b.id in ($order_ids)","buyer_name");
						
                }
				//echo  $po_number.'aaa';
				$po_number=implode(",",array_unique(explode(",",$po_number)));
				if($dataArray[0][csf('receive_basis')]==2)
				{
					if($dataArray[0][csf('booking_without_order')]==0)
					{
						// if($row[csf('sensitivity')]=='' || $row[csf('sensitivity')]==0) $sensitivity=0;
						//if($row[csf('gmts_size')]=='' || $row[csf('gmts_size')]==0) $gmts_size=0;
						
						 $po_no=explode(",",$order_no);
						 $woorder_qty=0;
						 foreach($po_no as $po_id)
						 {
							$woorder_qty+=$booking_qty_arr[$po_id][$row[csf('item_group_id')]][$row[csf('gmts_color_id')]][$row[csf('item_color')]][$row[csf('item_description')]][$row[csf('item_size')]][$row[csf('sensitivity')]][$row[csf('brand_supplier')]]; 
						 }
						
					}
					else
					{
					 $woorder_qty=$booking_qty_arr[$row[csf('item_group_id')]][$row[csf('gmts_color_id')]][$row[csf('item_color')]][$row[csf('item_description')]];	
					}
				}
				else
				{
					 $woorder_qty=$booking_qty_arr[$row[csf('item_group_id')]][$row[csf('gmts_color_id')]][$row[csf('item_color')]][$row[csf('item_description')]];	
				}
				$buyer=explode(",",$po_number_buyer);
				$buyer_id='';
				foreach($buyer as $bid)
				{
					if($buyer_id=='') $buyer_id=$buyer_name_library[$bid]; else $buyer_id.=",".$buyer_name_library[$bid];
				}
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>">
                    <td><? echo $i; ?></td>
                    <td><p><? echo $po_number_job.'<br>'.$po_number.'<br>'.$buyer_id.'<br>'//$item_category[4]; ?></p></td>

                    <td><p><? echo $item_library[$row[csf('item_group_id')]] ?></p></td>
                    <td><p><? echo $row[csf('item_description')]; ?></p></td>
                    <td><p><? echo $unit_of_measurement[$row[csf('cons_uom')]]; ?></p></td>
                    <td align="right"><div style="word-wrap:break-word;">
						<? //$woorder_qty=$booking_qty_arr[$row[csf('item_group_id')]][$row[csf('gmts_color_id')]][$row[csf('item_color')]][$row[csf('item_description')]];
						//$total_woorder_qty+=$woorder_qty;
                         echo number_format($woorder_qty,2,".","");; 
                         ?>
                     </div></td>
                    <td align="right"><? echo number_format($prev_qty_arr[$key],2,".",""); ?></td>
                    <td align="right"><? echo number_format($row[csf('receive_qnty')],2,".",""); ?></td>
                    <td align="right"><? echo number_format($prev_qty_arr[$key]+$row[csf('receive_qnty')],2,".",""); ?></td>
                   
                    <td align="right"><? echo number_format($woorder_qty-($prev_qty_arr[$key]+$row[csf('receive_qnty')]),4,'.',''); ?></td>
                    <td align="right"><? echo number_format($row[csf('rate')],2,'.',''); ?></td>
                    <td align="right"><? echo number_format($row[csf('amount')],2,'.',''); ?></td>
                    <td></td>
                </tr>
            <?
			$i++;
			$tot_rec_qty+=$row[csf('receive_qnty')];
			$tot_amount+=$row[csf('amount')];
			$tot_reject_qty+=$row[csf('reject_receive_qnty')];
        }
       ?>
            <tr bgcolor="#dddddd">
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td align="right"><? //echo number_format($total_woorder_qty,2); ?></td>
                <td align="right"><? //echo number_format($tot_rec_qty,2); ?></td>
                <td>&nbsp;</td>
                <td align="right"></td>
                <td colspan="2" align="right"><b>Total :</b></td>
                <td align="right"><? echo number_format($tot_amount,2,'.',''); ?></td>
                <td align="center">&nbsp;</td>
            </tr>
       </table>
       <br>
       <?
		  echo signature_table(35, $data[0], "1010px");
	   ?>
	</div>
	</div>
<?

exit();
}

if ($action=="trims_receive_entry_print_4_BACKUP")// trims_receive_entry_print_4(BACK UP)
{ 
	extract($_REQUEST);
	$data=explode('__',$data);
	$variable_inventory_ref=explode("**",$data[3]);
	$rate_hide_inventory=$variable_inventory_ref[2];
	//print_r ($data);
	
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	//$supplier_library=return_library_array( "select id,supplier_name from lib_supplier", "id","supplier_name"  );
	$item_library=return_library_array( "select id, item_name from lib_item_group", "id", "item_name"  );
	$pi_library=return_library_array( "select id, pi_number from com_pi_master_details", "id", "pi_number"  );
	$wo_library=return_library_array( "select id, booking_no from wo_booking_mst", "id", "booking_no"  );
	$color_library=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name"  );
	$lc_arr=return_library_array( "select id, lc_number from  com_btb_lc_master_details", "id", "lc_number"  );
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$user_name 		= return_library_array("select id,user_full_name from user_passwd", "id", "user_full_name");
	
	$buyer_id_array=return_library_array( "SELECT b.po_number, a.buyer_name from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0", "po_number", "buyer_name"  );

	/*$buyer_sql = "SELECT b.po_number, a.buyer_name from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0"; 
	$buyerDataArray=sql_select($buyer_sql);*/

	
	$sql="SELECT id, recv_number, receive_basis, receive_date, booking_id, booking_no, challan_no, challan_date, lc_no, source, store_id, supplier_id, currency_id, exchange_rate,booking_without_order, remarks ,knitting_source, audit_by, audit_date, is_audited 
	from inv_receive_master where id='$data[1]' and status_active=1 and is_deleted=0 and entry_form=24";
	//echo $sql;
	$dataArray=sql_select($sql);
	if($dataArray[0][csf('knitting_source')]==3 || $dataArray[0][csf('knitting_source')]==5)
	{
		$supplier_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	}
	else
	{
		$supplier_library=return_library_array( "select id,supplier_name from lib_supplier", "id","supplier_name"  );
	}
	if($rate_hide_inventory!=1){
		$width=1300;
		$width_px='1300px';
	} 
	else{
		$width=1550;
		$width_px='1550px';
	} 
?>
<div style="width:<? echo $width_px; ?>;">
    <table width="<? echo $width; ?>" cellspacing="0" align="right" border="0">
        <tr>
            <td colspan="7" align="center" style="font-size:xx-large"><strong><? echo $company_library[$data[0]]; ?></strong></td>
        </tr>
        <tr class="form_caption">
            <td colspan="7" align="center">
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
            <td colspan="7" align="center" style="font-size:x-large"><strong><u>Material Receiving Report</u></strong></center></td>
        </tr>
        <br>
        <table cellspacing="0" width="1050" align="center" border="1" rules="all" class="rpt_table">
        <tr>
            <td width="160"><strong>System ID:</strong></td> <td width="175px"><? echo $dataArray[0][csf('recv_number')]; ?></td>
            <td width="130"><strong> Receive Basis :</strong></td><td width="175px" ><? echo $receive_basis_arr[$dataArray[0][csf('receive_basis')]]; ?></td>
            <td width="125"><strong>Received Date:</strong></td><td width="175px"><? echo  change_date_format($dataArray[0][csf('receive_date')]); ?></td>
        </tr>
        <tr>
            <td><strong>WO/PI:</strong></td> <td width="175px"><? echo $dataArray[0][csf('booking_no')]; ?></td>
            <td><strong>Challan No :</strong></td><td width="175px" ><? echo $dataArray[0][csf('challan_no')]; ?></td>
            <td><strong>Challan Date:</strong></td><td width="175px"><? echo  change_date_format($dataArray[0][csf('challan_date')]); ?></td>
        </tr>
        <tr>
        	<td><strong>Supplier:</strong></td><td width="175px"><? echo $supplier_library[$dataArray[0][csf('supplier_id')]]; ?></td>
        </tr>
    </table>
    </table>
        <?
		
		/*	$sql = "select id, pi_number, supplier_id, pi_date, last_shipment_date, pi_basis_id, internal_file_no, currency_id, source from com_pi_master_details where item_category_id='4' and status_active=1 and is_deleted=0 and importer_id=$company_id $search_field_cond";  */
	?>
    <br>
	<div style="width:100%;">
        <table cellspacing="0" width="<? echo $width; ?>"  border="1" rules="all" class="rpt_table" >
            <thead bgcolor="#dddddd" align="center" style=" font-size:14px"> <!--style=" font-size:12px"-->
                <th width="30">SL</th>
				<th width="110" align="center">Job/Style</th>
                <th width="110" align="center">Item Group</th>
                <th width="150" align="center">Item Des.</th>
                <th width="100" align="center">Buyer Name</th>
                <th width="70" align="center">Gmts Color</th>
                <th width="70" align="center">Item Color</th>
                <th width="70" align="center">Item Size</th>
                <th width="300" align="center">Buyer Order</th>
                <th width="40" align="center">UOM</th>
                <th width="70" align="center">WO/PI Qty </th>
                <th width="70" align="center">Rec. Qty </th>
                <?
				if($rate_hide_inventory!=1)
				{
					?>
                    <th width="60" align="center">Rate</th>
                	<th width="100" align="center">Payable Amount</th>
                	<th width="100">Book Currency</th>
                    <?
				}
				?>
                <th width="70" align="center">Excess Rcv Qty</th>
                <th width="70" align="center">Reject Qty</th>
                <th width="80" align="center">Total Rcv Qty</th>
                <th align="center">Remarks</th>
            </thead>
    	<?
		if($dataArray[0][csf('receive_basis')]==1 || $dataArray[0][csf('receive_basis')]==2)
		{
			if($dataArray[0][csf('receive_basis')]==2)
			{
				//if($db_type==0) $null_val="c.color_number_id";
				//else if($db_type==2) $null_val="nvl(c.color_number_id,0)";
				if($dataArray[0][csf('booking_without_order')]==0)
				{
				if($db_type==0) $null_val="c.color_number_id,c.item_color,c.gmts_sizes,";
				else if($db_type==2) $null_val="nvl(c.color_number_id,0) as color_number_id,nvl(c.item_color,0) as item_color,nvl(c.gmts_sizes,0) as gmts_sizes,";
				
					$sql_bookingqty =sql_select("select c.cons as wo_qnty,b.id as dtls_id,c.id,b.trim_group as item_group, $null_val c.description,c.brand_supplier,b.sensitivity from wo_booking_mst a, wo_booking_dtls b,wo_trim_book_con_dtls c where a.booking_no=b.booking_no and b.id=c.wo_trim_booking_dtls_id and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.booking_no='".$dataArray[0][csf('booking_no')]."'");
			
				}
				else
				{
					$sql_bookingqty = sql_select("select sum(a.trim_qty) as wo_qnty,a.trim_group as item_group,a.fabric_color as item_color,a.gmts_color as color_number_id,a.fabric_description as description from wo_non_ord_samp_booking_dtls a where  a.booking_no='".$dataArray[0][csf('booking_no')]."' and a.status_active=1 group by  a.trim_group,a.fabric_color,a.gmts_color,a.fabric_description");	
				}
			}
			else
			{
				$sql_bookingqty = sql_select("select sum(b.quantity) as wo_qnty,b.item_group,b.item_color,b.color_id as color_number_id,b.item_description as description from  com_pi_master_details a,com_pi_item_details b where a.id=b.pi_id and a.id='".$dataArray[0][csf('booking_id')]."' and a.status_active=1 and b.status_active=1 group by b.item_group,b.item_color,b.color_id,b.item_description");	
			}
			foreach($sql_bookingqty as $b_qty)
			{
				$desc=trim(strtolower($b_qty[csf('description')]));
				if($dataArray[0][csf('receive_basis')]==2)
				{
					if($dataArray[0][csf('booking_without_order')]==0)
					{
						$booking_qty_arr[$b_qty[csf('item_group')]][$b_qty[csf('color_number_id')]][$b_qty[csf('item_color')]][$desc][$b_qty[csf('gmts_sizes')]][$b_qty[csf('sensitivity')]][trim($b_qty[csf('brand_supplier')])]+=$b_qty[csf('wo_qnty')];
					}
					else
					{
						$booking_qty_arr[$b_qty[csf('item_group')]][$b_qty[csf('color_number_id')]][$b_qty[csf('item_color')]][$desc]+=$b_qty[csf('wo_qnty')];
					}
				}
				else
				{
					$booking_qty_arr[$b_qty[csf('item_group')]][$b_qty[csf('color_number_id')]][$b_qty[csf('item_color')]][$desc]+=$b_qty[csf('wo_qnty')];
				}
			}
		 }
		/*echo "<pre>";
		print_r($booking_qty_arr);*/
		/*$lib_rack_arr=return_library_array( "select a.company_id,b.location_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,a.floor_room_rack_name,a.status_active, a.floor_room_rack_id,b.floor_room_rack_dtls_id from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.rack_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and a.company_id=$data[0] order by b.floor_id", "rack_id","floor_room_rack_name" ); 
		$lib_shelf_arr=return_library_array( "select a.company_id,b.location_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,a.floor_room_rack_name,a.status_active, a.floor_room_rack_id,b.floor_room_rack_dtls_id from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.shelf_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and a.company_id=$data[0] order by b.floor_id", "shelf_id","floor_room_rack_name" ); 
		$lib_bin_arr=return_library_array( "select a.company_id,b.location_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,a.floor_room_rack_name,a.status_active, a.floor_room_rack_id,b.floor_room_rack_dtls_id from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.bin_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and a.company_id=$data[0] order by b.floor_id", "bin_id","floor_room_rack_name" ); */

        $i=1; 
		$mst_id=$dataArray[0][csf('id')];
		if($db_type==0)
		{
			$sql_dtls="select b.item_group_id, b.item_description, b.gmts_color_id, b.item_color, b.item_size, b.gmts_size_id as gmts_size, b.sensitivity, b.brand_supplier, b.cons_uom, b.order_uom, group_concat(b.order_id) as order_id, group_concat(b.order_id_2) as order_id_2, sum( case when a.payment_over_recv=0 then b.receive_qnty else 0 end) as receive_qnty, avg(case when a.payment_over_recv=0 then b.rate else 0 end) as rate, sum(case when a.payment_over_recv=0 then b.amount else 0 end) as amount, sum(case when a.payment_over_recv=0 then b.cons_qnty else 0 end) as cons_qnty, sum(b.reject_receive_qnty) as reject_receive_qnty, sum( case when a.payment_over_recv=1 then b.receive_qnty else 0 end) as over_receive_qnty, max(b.remarks) as remarks
			from inv_transaction a, inv_trims_entry_dtls b left join inv_goods_placement c on c.dtls_id=b.id and c.entry_form=24 
			where a.id=b.trans_id and a.item_category=4 and a.transaction_type=1 and b.mst_id='$mst_id' and b.status_active='1' and b.is_deleted='0'
			group by b.item_group_id, b.item_description, b.gmts_color_id, b.item_color, b.item_size, b.gmts_size_id, b.sensitivity, b.brand_supplier, b.cons_uom, b.order_uom";
		}
		else
		{
			$sql_dtls="select b.item_group_id, b.item_description, b.gmts_color_id, b.item_color, b.item_size, b.gmts_size_id as gmts_size, b.sensitivity, b.brand_supplier, b.cons_uom, b.order_uom, listagg( cast(b.order_id as varchar(4000)),',') within group (order by b.id) as order_id, listagg( cast(b.order_id_2 as varchar(4000)),',') within group (order by b.id) as order_id_2, sum( case when a.payment_over_recv=0 then b.receive_qnty else 0 end) as receive_qnty, avg(case when a.payment_over_recv=0 then b.rate else 0 end) as rate, sum(case when a.payment_over_recv=0 then b.amount else 0 end) as amount, sum(case when a.payment_over_recv=0 then b.cons_qnty else 0 end) as cons_qnty, sum(b.reject_receive_qnty) as reject_receive_qnty, sum( case when a.payment_over_recv=1 then b.receive_qnty else 0 end) as over_receive_qnty, max(b.remarks) as remarks 
			from inv_transaction a, inv_trims_entry_dtls b left join inv_goods_placement c on c.dtls_id=b.id and c.entry_form=24 
			where a.id=b.trans_id and a.item_category=4 and a.transaction_type=1 and b.mst_id='$mst_id' and b.status_active='1' and b.is_deleted='0'
			group by b.item_group_id, b.item_description, b.gmts_color_id, b.item_color, b.item_size, b.gmts_size_id, b.sensitivity, b.brand_supplier, b.cons_uom, b.order_uom";
		}
		
		//echo $sql_dtls;die;
        $sql_result=sql_select($sql_dtls);
        foreach($sql_result as $row)
        {
			$order_ids=$row[csf('order_id')];
			if($row[csf('order_id_2')]!="") $order_ids.=",".$row[csf('order_id_2')];
			//$order_no=$row[csf('order_id')];
			//print_r($booking_qty_arr);
            if ($i%2==0)  
                $bgcolor="#E9F3FF";
            else
                $bgcolor="#FFFFFF";
                
			
			if($db_type==0)
			{
				$po_number = return_field_value("group_concat(po_number) as po_number","wo_po_break_down"," id in ($order_ids)","po_number");
				//$buyername = $buyer_library[$po_number];
			}
			else
			{
				$po_number = return_field_value("LISTAGG(po_number, ',') WITHIN GROUP (ORDER BY id) as po_number","wo_po_break_down"," id in ($order_ids)","po_number");	
				// $po_number = return_field_value("po_number as po_number","wo_po_break_down"," id in ($order_ids)","po_number");
				$po_number_job = return_field_value("LISTAGG(a.job_no, ',') WITHIN GROUP (ORDER BY a.id) as job_no","wo_po_details_master a,wo_po_break_down b"," a.job_no=b.job_no_mst and b.id in ($order_ids)","job_no");
				//$internal_ref = return_field_value("grouping","wo_po_break_down"," id in ($order_ids)","grouping");
				//$po_number_buyer = return_field_value("a.buyer_name as buyer_name","wo_po_details_master a,wo_po_break_down b","a.job_no=b.job_no_mst and b.id in ($order_ids)","buyer_name");
				$style_no = return_field_value("LISTAGG(a.style_ref_no, ',') WITHIN GROUP (ORDER BY a.id) as style_ref_no","wo_po_details_master a,wo_po_break_down b"," a.job_no=b.job_no_mst and b.id in ($order_ids)","style_ref_no");
			}
			$po_number=implode(",",array_unique(explode(",",$po_number)));

			$buyer_id_array = return_field_value("buyer_name","wo_po_details_master a, wo_po_break_down b"," a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.id in ($order_ids) GROUP BY a.buyer_name","buyer_name");
			$buyerName = $buyer_library[$buyer_id_array];
			

			$descp=trim(strtolower($row[csf('item_description')]));
			if($dataArray[0][csf('receive_basis')]==2)
			{					
				if($dataArray[0][csf('booking_without_order')]==0)
				{
					$woorder_qty=$booking_qty_arr[$row[csf('item_group_id')]][$row[csf('gmts_color_id')]][$row[csf('item_color')]][$descp][$row[csf('gmts_size')]][$row[csf('sensitivity')]][trim($row[csf('brand_supplier')])];
					//$booking_qty_arr[$b_qty[csf('item_group')]][$b_qty[csf('color_number_id')]][$b_qty[csf('item_color')]][$b_qty[csf('description')]][$b_qty[csf('item_size')]][$b_qty[csf('gmts_size')]][$b_qty[csf('sensitivity')]][$b_qty[csf('brand_supplier')]]=$b_qty[csf('wo_qnty')];
				}
				else
				{
					$woorder_qty=$booking_qty_arr[$row[csf('item_group_id')]][$row[csf('gmts_color_id')]][$row[csf('item_color')]][$descp];	
				}
			}
			else
			{
				$woorder_qty=$booking_qty_arr[$row[csf('item_group_id')]][$row[csf('gmts_color_id')]][$row[csf('item_color')]][$descp];	
			}
			$all_rcv_qnty=$row[csf('receive_qnty')]+$row[csf('over_receive_qnty')];
			$avgs_rate=$row[csf('amount')]/$row[csf('receive_qnty')];
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="font-size:14px;">
				<td align="center"><? echo $i; ?></td>
				<td><p><? echo implode(", ",array_unique(explode(",",$po_number_job))).'<br>'.implode(", ",array_unique(explode(",",$style_no)));//$item_category[4]; ?></p></td>
				<td><p><? echo $item_library[$row[csf('item_group_id')]]; ?></p></td>

				<td width="150"><p><? echo ucwords(strtolower($row[csf('item_description')])); ?></p></td> 

				<td><p><? echo $buyerName; ?></p></td>
				<td><p><? echo $color_library[$row[csf('gmts_color_id')]]; ?></p></td>
				<td><p><? echo $color_library[$row[csf('item_color')]]; ?></p></td>
				<td><p><? echo $row[csf('item_size')]; ?></p></td>
				<td width="300" style="word-break:break-all;"><p><? echo $po_number; ?></p></td> <!--style="word-break:break-all;"-->
				<td align="center"><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?></td>
				<td align="right" title="<? echo $row[csf('item_group_id')].'='.$row[csf('gmts_color_id')].'='.$row[csf('item_color')].'='.$descp; ?>"><? 
				$total_woorder_qty+=$woorder_qty;
				echo number_format($woorder_qty,2,".",""); ?></td>
				<td align="right"><? echo number_format($row[csf('receive_qnty')],2,".",""); ?></td>
				<?
				if($rate_hide_inventory!=1)
				{
					?>
                    <td align="right"><? echo number_format($avgs_rate,4,'.',''); ?></td>
					<td align="right"><? echo number_format($row[csf('amount')],2,'.',''); ?></td>
					<td align="right"><? echo $book_currency= number_format($row[csf("amount")]*($dataArray[0][csf('exchange_rate')]),2,'.',''); ?></td>
                    <?
				}
				?>

                <td align="right"><? echo number_format($row[csf('over_receive_qnty')],2,'.',''); ?></td>
				<td align="right"><? echo number_format($row[csf('reject_receive_qnty')],2,'.',''); ?></td>
                <td align="right"><? echo number_format($all_rcv_qnty,2,'.',''); ?></td>
				<td align="center"><p><? echo $row[csf('remarks')]; //echo $row[csf('box_bin_no')]; ?></p></td>
			</tr>
            <?
			$i++;
			$tot_rec_qty+=$row[csf('receive_qnty')];
			$tot_over_receive_qnty+=$row[csf('over_receive_qnty')];
			$tot_all_rcv_qnty+=$all_rcv_qnty;
			$tot_amount+=$row[csf('amount')];
			$tot_book_currency+=$book_currency;
			$tot_reject_qty+=$row[csf('reject_receive_qnty')];
        }
       ?>
            <tr bgcolor="#dddddd">
                <td colspan="10" align="right"><b>Total :</b></td>
                <td align="right"><b><? echo number_format($total_woorder_qty,2); ?></b></td>
                <td align="right"><b><? echo number_format($tot_rec_qty,2); ?></b></td>
                <?
				if($rate_hide_inventory!=1)
				{
					?>
                    <td>&nbsp;</td>
                	<td align="right"><b><? echo number_format($tot_amount,2,'.',''); ?></b></td>                
                	<td align="right"><b><? echo number_format($tot_book_currency,2,'.',''); ?></b></td>
                    <?
				}
				?>

                <td align="right"><b><? echo number_format($tot_over_receive_qnty,2,'.',''); ?></b></td>
                <td align="right"><b><? echo number_format($tot_reject_qty,2,'.',''); ?></b></td>
                <td align="right"><b><? echo number_format($tot_all_rcv_qnty,2,'.',''); ?></b></td>
                <td align="center">&nbsp;</td>
            </tr>
       </table>
       <table align="right" cellspacing="0" width="1000"  rules="all" class="rpt_table" >
			<tr>
				<?
				if($dataArray[0][csf("is_audited")]==1){
					?>
					<td><? echo 'Audited By &nbsp;'.$user_name[$dataArray[0][csf("audit_by")]].'&nbsp;'.$dataArray[0][csf("audit_date")]; ?></td>
					<?
				}
				?>				
			</tr>
		</table>
       
        <tr align="left" style="padding-top:30px"><b>In Words:</b>&nbsp;<? echo number_to_words(number_format($tot_amount,2,'.',''),$inWordTxt);?></tr>
        
       <?
		  echo signature_table(35, $data[0],"1300px",'',"3px");
	   ?>
	</div>
</div>
<?
exit();
} 


if ($action=="trims_receive_entry_print_4")
{ 
	extract($_REQUEST);
	$data=explode('__',$data);
	$variable_inventory_ref=explode("**",$data[3]);
	$rate_hide_inventory=$variable_inventory_ref[2];
	//print_r ($data);
	
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	//$supplier_library=return_library_array( "select id,supplier_name from lib_supplier", "id","supplier_name"  );
	$item_library=return_library_array( "select id, item_name from lib_item_group", "id", "item_name"  );
	$pi_library=return_library_array( "select id, pi_number from com_pi_master_details", "id", "pi_number"  );
	$wo_library=return_library_array( "select id, booking_no from wo_booking_mst", "id", "booking_no"  );
	$color_library=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name"  );
	$lc_arr=return_library_array( "select id, lc_number from  com_btb_lc_master_details", "id", "lc_number"  );
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$user_name 		= return_library_array("select id,user_full_name from user_passwd", "id", "user_full_name");
	
	$buyer_id_array=return_library_array( "SELECT b.po_number, a.buyer_name from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0", "po_number", "buyer_name"  );

	/*$buyer_sql = "SELECT b.po_number, a.buyer_name from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0"; 
	$buyerDataArray=sql_select($buyer_sql);*/

	
	$sql="SELECT id, recv_number, receive_basis, receive_date, booking_id, booking_no, challan_no, challan_date, lc_no, source, store_id, supplier_id, currency_id, exchange_rate,booking_without_order, remarks ,knitting_source, audit_by, audit_date, is_audited 
	from inv_receive_master where id='$data[1]' and status_active=1 and is_deleted=0 and entry_form=24";
	//echo $sql;
	$dataArray=sql_select($sql);
	if($dataArray[0][csf('knitting_source')]==3 || $dataArray[0][csf('knitting_source')]==5)
	{
		$supplier_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	}
	else
	{
		$supplier_library=return_library_array( "select id,supplier_name from lib_supplier", "id","supplier_name"  );
	}
	if($rate_hide_inventory!=1){
		$width=1300;
		$width_px='1300px';
	} 
	else{
		$width=1550;
		$width_px='1550px';
	} 
?>
<div style="width:<? echo $width_px; ?>;">
    <table width="<? echo $width; ?>" cellspacing="0" align="right" border="0">
        <tr>
            <td colspan="7" align="center" style="font-size:xx-large"><strong><? echo $company_library[$data[0]]; ?></strong></td>
        </tr>
        <tr class="form_caption">
            <td colspan="7" align="center">
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
            <td colspan="7" align="center" style="font-size:x-large"><strong><u>Material Receiving Report</u></strong></center></td>
        </tr>
        <br>
        <table cellspacing="0" width="1050" align="center" border="1" rules="all" class="rpt_table">
        <tr>
            <td width="160"><strong>System ID:</strong></td> <td width="175px"><? echo $dataArray[0][csf('recv_number')]; ?></td>
            <td width="130"><strong> Receive Basis :</strong></td><td width="175px" ><? echo $receive_basis_arr[$dataArray[0][csf('receive_basis')]]; ?></td>
            <td width="125"><strong>Received Date:</strong></td><td width="175px"><? echo  change_date_format($dataArray[0][csf('receive_date')]); ?></td>
        </tr>
        <tr>
            <td><strong>WO/PI:</strong></td> <td width="175px"><? echo $dataArray[0][csf('booking_no')]; ?></td>
            <td><strong>Challan No :</strong></td><td width="175px" ><? echo $dataArray[0][csf('challan_no')]; ?></td>
            <td><strong>Challan Date:</strong></td><td width="175px"><? echo  change_date_format($dataArray[0][csf('challan_date')]); ?></td>
        </tr>
        <tr>
        	<td><strong>Supplier:</strong></td><td width="175px"><? echo $supplier_library[$dataArray[0][csf('supplier_id')]]; ?></td>
        </tr>
    </table>
    </table>
        <?
		
		// 	$sql = "select id, pi_number, supplier_id, pi_date, last_shipment_date, pi_basis_id, internal_file_no, currency_id, source from com_pi_master_details where item_category_id='4' and status_active=1 and is_deleted=0 and importer_id=$company_id $search_field_cond";  /
	?>
    <br>
	<div style="width:100%;">
        <table cellspacing="0" width="<? echo $width; ?>"  border="1" rules="all" class="rpt_table" >
            <thead bgcolor="#dddddd" align="center" style=" font-size:14px"> <!--style=" font-size:12px"-->
                <th width="30">SL</th>
				<th width="110" align="center">Job/Style</th>
                <th width="110" align="center">Item Group</th>
                <th width="150" align="center">Item Des.</th>
                <th width="100" align="center">Buyer Name</th>
                <th width="70" align="center">Gmts Color</th>
                <th width="70" align="center">Item Color</th>
                <th width="70" align="center">Item Size</th>
                <th width="300" align="center">Buyer Order</th>
                <th width="40" align="center">UOM</th>
                <th width="70" align="center">WO/PI Qty </th>
                <th width="70" align="center">Rec. Qty </th>
                <?
				if($rate_hide_inventory!=1)
				{
					?>
                    <th width="60" align="center">Rate</th>
                	<th width="100" align="center">Payable Amount</th>
                	<th width="100">Book Currency</th>
                    <?
				}
				?>
                <th width="70" align="center">Excess Rcv Qty</th>
                <th width="70" align="center">Reject Qty</th>
                <th width="80" align="center">Total Rcv Qty</th>
                <th align="center">Remarks</th>
            </thead>
    	<?
		if($dataArray[0][csf('receive_basis')]==1 || $dataArray[0][csf('receive_basis')]==2)
		{
			if($dataArray[0][csf('receive_basis')]==2)
			{
				//if($db_type==0) $null_val="c.color_number_id";
				//else if($db_type==2) $null_val="nvl(c.color_number_id,0)";
				if($dataArray[0][csf('booking_without_order')]==0)
				{
					if($db_type==0) $null_val="c.color_number_id,c.item_color,c.gmts_sizes,";
					else if($db_type==2) $null_val="nvl(c.color_number_id,0) as color_number_id,nvl(c.item_color,0) as item_color,nvl(c.gmts_sizes,0) as gmts_sizes,";
				
					$sql_bookingqty =sql_select("select c.cons as wo_qnty,b.id as dtls_id,c.id,b.trim_group as item_group, $null_val c.description,c.brand_supplier,b.sensitivity,c.item_size 
					from wo_booking_mst a, wo_booking_dtls b,wo_trim_book_con_dtls c 
					where a.booking_no=b.booking_no and b.id=c.wo_trim_booking_dtls_id and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.booking_no='".$dataArray[0][csf('booking_no')]."'");
			
				}
				else
				{
					$sql_bookingqty = sql_select("select sum(a.trim_qty) as wo_qnty,a.trim_group as item_group,a.fabric_color as item_color,a.gmts_color as color_number_id,a.fabric_description as description ,a.item_size
					from wo_non_ord_samp_booking_dtls a where  a.booking_no='".$dataArray[0][csf('booking_no')]."' and a.status_active=1 group by  a.trim_group,a.fabric_color,a.gmts_color,a.fabric_description,a.item_size");	
				}
			}
			else
			{
				$sql_bookingqty = sql_select("select sum(b.quantity) as wo_qnty,b.item_group,b.item_color,b.color_id as color_number_id,b.item_description as description,b.item_size from  com_pi_master_details a,com_pi_item_details b where a.id=b.pi_id and a.id='".$dataArray[0][csf('booking_id')]."' and a.status_active=1 and b.status_active=1 group by b.item_group,b.item_color,b.color_id,b.item_description,b.item_size ");	
				
			}
			foreach($sql_bookingqty as $b_qty)
			{
				$desc=trim(strtolower($b_qty[csf('description')]));
				if($dataArray[0][csf('receive_basis')]==2)
				{
					if($dataArray[0][csf('booking_without_order')]==0)
					{
						$booking_qty_arr[$b_qty[csf('item_group')]][$b_qty[csf('color_number_id')]][$b_qty[csf('item_color')]][$desc][$b_qty[csf('gmts_sizes')]][$b_qty[csf('sensitivity')]][trim($b_qty[csf('brand_supplier')])][$b_qty[csf('item_size')]]+=$b_qty[csf('wo_qnty')];
					}
					else
					{
						$booking_qty_arr[$b_qty[csf('item_group')]][$b_qty[csf('color_number_id')]][$b_qty[csf('item_color')]][$desc][$b_qty[csf('item_size')]]+=$b_qty[csf('wo_qnty')];
					}
				}
				else
				{
					$booking_qty_arr[$b_qty[csf('item_group')]][$b_qty[csf('color_number_id')]][$b_qty[csf('item_color')]][$desc][$b_qty[csf('item_size')]]+=$b_qty[csf('wo_qnty')];
				}
			}
		}
		/*echo "<pre>";
		print_r($booking_qty_arr);*/
		/*$lib_rack_arr=return_library_array( "select a.company_id,b.location_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,a.floor_room_rack_name,a.status_active, a.floor_room_rack_id,b.floor_room_rack_dtls_id from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.rack_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and a.company_id=$data[0] order by b.floor_id", "rack_id","floor_room_rack_name" ); 
		$lib_shelf_arr=return_library_array( "select a.company_id,b.location_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,a.floor_room_rack_name,a.status_active, a.floor_room_rack_id,b.floor_room_rack_dtls_id from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.shelf_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and a.company_id=$data[0] order by b.floor_id", "shelf_id","floor_room_rack_name" ); 
		$lib_bin_arr=return_library_array( "select a.company_id,b.location_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,a.floor_room_rack_name,a.status_active, a.floor_room_rack_id,b.floor_room_rack_dtls_id from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.bin_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and a.company_id=$data[0] order by b.floor_id", "bin_id","floor_room_rack_name" ); */

        $i=1; 
		$mst_id=$dataArray[0][csf('id')];
		if($db_type==0)
		{
			$sql_dtls="select b.item_group_id, b.item_description, b.gmts_color_id, b.item_color, b.item_size, b.gmts_size_id as gmts_size, b.sensitivity, b.brand_supplier, b.cons_uom, b.order_uom, group_concat(b.order_id) as order_id, group_concat(b.order_id_2) as order_id_2, sum( case when a.payment_over_recv=0 then b.receive_qnty else 0 end) as receive_qnty, avg(case when a.payment_over_recv=0 then b.rate else 0 end) as rate, sum(case when a.payment_over_recv=0 then b.amount else 0 end) as amount, sum(case when a.payment_over_recv=0 then b.cons_qnty else 0 end) as cons_qnty, sum(b.reject_receive_qnty) as reject_receive_qnty, sum( case when a.payment_over_recv=1 then b.receive_qnty else 0 end) as over_receive_qnty, max(b.remarks) as remarks
			from inv_transaction a, inv_trims_entry_dtls b left join inv_goods_placement c on c.dtls_id=b.id and c.entry_form=24 
			where a.id=b.trans_id and a.item_category=4 and a.transaction_type=1 and b.mst_id='$mst_id' and b.status_active='1' and b.is_deleted='0'
			group by b.item_group_id, b.item_description, b.gmts_color_id, b.item_color, b.item_size, b.gmts_size_id, b.sensitivity, b.brand_supplier, b.cons_uom, b.order_uom";
		}
		else
		{
			$sql_dtls="select b.item_group_id, b.item_description, b.gmts_color_id, b.item_color, b.item_size, b.gmts_size_id as gmts_size, b.sensitivity, b.brand_supplier, b.cons_uom, b.order_uom, listagg( cast(b.order_id as varchar(4000)),',') within group (order by b.id) as order_id, listagg( cast(b.order_id_2 as varchar(4000)),',') within group (order by b.id) as order_id_2, sum( case when a.payment_over_recv=0 then b.receive_qnty else 0 end) as receive_qnty, avg(case when a.payment_over_recv=0 then b.rate else 0 end) as rate, sum(case when a.payment_over_recv=0 then b.amount else 0 end) as amount, sum(case when a.payment_over_recv=0 then b.cons_qnty else 0 end) as cons_qnty, sum(b.reject_receive_qnty) as reject_receive_qnty, sum( case when a.payment_over_recv=1 then b.receive_qnty else 0 end) as over_receive_qnty, max(b.remarks) as remarks 
			from inv_transaction a, inv_trims_entry_dtls b left join inv_goods_placement c on c.dtls_id=b.id and c.entry_form=24 
			where a.id=b.trans_id and a.item_category=4 and a.transaction_type=1 and b.mst_id='$mst_id' and b.status_active='1' and b.is_deleted='0'
			group by b.item_group_id, b.item_description, b.gmts_color_id, b.item_color, b.item_size, b.gmts_size_id, b.sensitivity, b.brand_supplier, b.cons_uom, b.order_uom";
		}
		
		//echo $sql_dtls;die;
		//echo "<pre>";print_r($booking_qty_arr);
        $sql_result=sql_select($sql_dtls);
        foreach($sql_result as $row)
        {
			$order_ids=$row[csf('order_id')];
			if($row[csf('order_id_2')]!="") $order_ids.=",".$row[csf('order_id_2')];
			//$order_no=$row[csf('order_id')];
			//print_r($booking_qty_arr);
            if ($i%2==0)  
                $bgcolor="#E9F3FF";
            else
                $bgcolor="#FFFFFF";
                
			
			if($db_type==0)
			{
				$po_number = return_field_value("group_concat(po_number) as po_number","wo_po_break_down"," id in ($order_ids)","po_number");
				//$buyername = $buyer_library[$po_number];
			}
			else
			{
				$po_number = return_field_value("LISTAGG(po_number, ',') WITHIN GROUP (ORDER BY id) as po_number","wo_po_break_down"," id in ($order_ids)","po_number");	
				// $po_number = return_field_value("po_number as po_number","wo_po_break_down"," id in ($order_ids)","po_number");
				$po_number_job = return_field_value("LISTAGG(a.job_no, ',') WITHIN GROUP (ORDER BY a.id) as job_no","wo_po_details_master a,wo_po_break_down b"," a.job_no=b.job_no_mst and b.id in ($order_ids)","job_no");
				//$internal_ref = return_field_value("grouping","wo_po_break_down"," id in ($order_ids)","grouping");
				//$po_number_buyer = return_field_value("a.buyer_name as buyer_name","wo_po_details_master a,wo_po_break_down b","a.job_no=b.job_no_mst and b.id in ($order_ids)","buyer_name");
				$style_no = return_field_value("LISTAGG(a.style_ref_no, ',') WITHIN GROUP (ORDER BY a.id) as style_ref_no","wo_po_details_master a,wo_po_break_down b"," a.job_no=b.job_no_mst and b.id in ($order_ids)","style_ref_no");
			}
			$po_number=implode(",",array_unique(explode(",",$po_number)));

			$buyer_id_array = return_field_value("buyer_name","wo_po_details_master a, wo_po_break_down b"," a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.id in ($order_ids) GROUP BY a.buyer_name","buyer_name");
			$buyerName = $buyer_library[$buyer_id_array];
			

			$descp=trim(strtolower($row[csf('item_description')]));
			if($dataArray[0][csf('receive_basis')]==2)
			{					
				if($dataArray[0][csf('booking_without_order')]==0)
				{
					//echo $row[csf('item_group_id')]."=".$row[csf('gmts_color_id')]."=".$row[csf('item_color')]."=".$descp."=".$row[csf('gmts_size')]."=".$row[csf('sensitivity')]."=".trim($row[csf('brand_supplier')])."=".$row[csf('item_size')]."<br>";
					$woorder_qty=$booking_qty_arr[$row[csf('item_group_id')]][$row[csf('gmts_color_id')]][$row[csf('item_color')]][$descp][$row[csf('gmts_size')]][$row[csf('sensitivity')]][trim($row[csf('brand_supplier')])][$row[csf('item_size')]];
				}
				else
				{
					$woorder_qty=$booking_qty_arr[$row[csf('item_group_id')]][$row[csf('gmts_color_id')]][$row[csf('item_color')]][$descp][$row[csf('item_size')]];	
				}
			}
			else
			{
				$woorder_qty=$booking_qty_arr[$row[csf('item_group_id')]][$row[csf('gmts_color_id')]][$row[csf('item_color')]][$descp][$row[csf('item_size')]];	
			}
			$all_rcv_qnty=$row[csf('receive_qnty')]+$row[csf('over_receive_qnty')];
			$avgs_rate=$row[csf('amount')]/$row[csf('receive_qnty')];
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="font-size:14px;">
				<td align="center"><? echo $i; ?></td>
				<td><p><? echo implode(", ",array_unique(explode(",",$po_number_job))).'<br>'.implode(", ",array_unique(explode(",",$style_no)));//$item_category[4]; ?></p></td>
				<td><p><? echo $item_library[$row[csf('item_group_id')]]; ?></p></td>

				<td width="150"><p><? echo ucwords(strtolower($row[csf('item_description')])); ?></p></td> 

				<td><p><? echo $buyerName; ?></p></td>
				<td><p><? echo $color_library[$row[csf('gmts_color_id')]]; ?></p></td>
				<td><p><? echo $color_library[$row[csf('item_color')]]; ?></p></td>
				<td><p><? echo $row[csf('item_size')]; ?></p></td>
				<td width="300" style="word-break:break-all;"><p><? echo $po_number; ?></p></td> <!--style="word-break:break-all;"-->
				<td align="center"><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?></td>
				<td align="right" title="<? echo $row[csf('item_group_id')].'='.$row[csf('gmts_color_id')].'='.$row[csf('item_color')].'='.$descp; ?>"><? 
				$total_woorder_qty+=$woorder_qty;
				echo number_format($woorder_qty,2,".",""); ?></td>
				<td align="right"><? echo number_format($row[csf('receive_qnty')],2,".",""); ?></td>
				<?
				if($rate_hide_inventory!=1)
				{
					?>
                    <td align="right"><? echo number_format($avgs_rate,4,'.',''); ?></td>
					<td align="right"><? echo number_format($row[csf('amount')],2,'.',''); ?></td>
					<td align="right"><? echo $book_currency= number_format($row[csf("amount")]*($dataArray[0][csf('exchange_rate')]),2,'.',''); ?></td>
                    <?
				}
				?>

                <td align="right"><? echo number_format($row[csf('over_receive_qnty')],2,'.',''); ?></td>
				<td align="right"><? echo number_format($row[csf('reject_receive_qnty')],2,'.',''); ?></td>
                <td align="right"><? echo number_format($all_rcv_qnty,2,'.',''); ?></td>
				<td align="center"><p><? echo $row[csf('remarks')]; //echo $row[csf('box_bin_no')]; ?></p></td>
			</tr>
            <?
			$i++;
			$tot_rec_qty+=$row[csf('receive_qnty')];
			$tot_over_receive_qnty+=$row[csf('over_receive_qnty')];
			$tot_all_rcv_qnty+=$all_rcv_qnty;
			$tot_amount+=$row[csf('amount')];
			$tot_book_currency+=$book_currency;
			$tot_reject_qty+=$row[csf('reject_receive_qnty')];
        }
       ?>
            <tr bgcolor="#dddddd">
                <td colspan="10" align="right"><b>Total :</b></td>
                <td align="right"><b><? echo number_format($total_woorder_qty,2); ?></b></td>
                <td align="right"><b><? echo number_format($tot_rec_qty,2); ?></b></td>
                <?
				if($rate_hide_inventory!=1)
				{
					?>
                    <td>&nbsp;</td>
                	<td align="right"><b><? echo number_format($tot_amount,2,'.',''); ?></b></td>                
                	<td align="right"><b><? echo number_format($tot_book_currency,2,'.',''); ?></b></td>
                    <?
				}
				?>

                <td align="right"><b><? echo number_format($tot_over_receive_qnty,2,'.',''); ?></b></td>
                <td align="right"><b><? echo number_format($tot_reject_qty,2,'.',''); ?></b></td>
                <td align="right"><b><? echo number_format($tot_all_rcv_qnty,2,'.',''); ?></b></td>
                <td align="center">&nbsp;</td>
            </tr>
       </table>
       <table align="right" cellspacing="0" width="1000"  rules="all" class="rpt_table" >
			<tr>
				<?
				if($dataArray[0][csf("is_audited")]==1){
					?>
					<td><? echo 'Audited By &nbsp;'.$user_name[$dataArray[0][csf("audit_by")]].'&nbsp;'.$dataArray[0][csf("audit_date")]; ?></td>
					<?
				}
				?>				
			</tr>
		</table>
       
        <tr align="left" style="padding-top:30px"><b>In Words:</b>&nbsp;<? echo number_to_words(number_format($tot_amount,2,'.',''),$inWordTxt);?></tr>
        
       <?
		  echo signature_table(35, $data[0],"1300px",'',"3px");
	   ?>
	</div>
</div>
<?
exit();
} 


if ($action=="trims_receive_entry_print_5")   //print 4
{
	extract($_REQUEST);
	$data=explode('__',$data);
	$variable_inventory_ref=explode("**",$data[3]);
	$rate_hide_inventory=$variable_inventory_ref[2];
	//print_r ($data);
	
	$buyer_name_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	//$supplier_library=return_library_array( "select id,supplier_name from lib_supplier", "id","supplier_name"  );
	$item_library=return_library_array( "select id, item_name from lib_item_group", "id", "item_name"  );
	//$pi_library=return_library_array( "select id, pi_number from com_pi_master_details", "id", "pi_number"  );
	$color_library=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name"  );
	$store_name_arr=return_library_array( "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id='$data[0]' and b.category_type=4 and a.status_active=1 and a.is_deleted=0 group by a.id, a.store_name order by a.store_name", "id", "store_name"  );

	$sql="select id, recv_number,item_category, receive_basis, receive_date, booking_id, booking_no, challan_no, challan_date, lc_no, source, store_id, supplier_id, currency_id, exchange_rate,booking_without_order,pay_mode,knitting_source, boe_mushak_challan_no, boe_mushak_challan_date from inv_receive_master where id='$data[1]' and status_active=1 and is_deleted=0 and entry_form=24 ";
	//echo $sql;
	$dataArray=sql_select($sql);
	$booking_without_order=$dataArray[0][csf('booking_without_order')];

	$wo_library=array();
	if ($booking_without_order==1)
	{
		$wo_library=return_library_array( "select id, pay_mode from wo_non_ord_samp_booking_mst where id='".$dataArray[0][csf('booking_id')]."'", "id", "pay_mode"  );
	}
	else
	{
		$wo_library=return_library_array( "select id, pay_mode from wo_booking_mst where id='".$dataArray[0][csf('booking_id')]."'", "id", "pay_mode"  );
	}
	
	if($dataArray[0][csf('knitting_source')]==3 || $dataArray[0][csf('knitting_source')]==5)
	{
		$supplier_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	}
	else
	{
		$supplier_library=return_library_array( "select id,supplier_name from lib_supplier", "id","supplier_name"  );
	}
	
	if($rate_hide_inventory!=1){
		$width=1120;
		$width_px='1020px';
	} 
	else{
		$width=1250;
		$width_px='1250px';
	} 
	?>
	<div style="width:<? echo $width_px; ?>;">
	    <table width="<? echo $width; ?>" cellspacing="0" align="right" border="0" style="padding-bottom: 10px;">
	        <tr>
	            <td colspan="7" align="center" style="font-size:xx-large"><strong><? echo $company_library[$data[0]]; ?></strong></td>
	        </tr>
	        <tr class="form_caption">
	            <td colspan="7" align="center">
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
	            <td colspan="7" align="center" style="font-size:x-large;"><strong><u>Material Receiving Report</u></strong></center></td>
	        </tr>
		</table>
	        <br>
	        <table cellspacing="0" width="1000" border="1" rules="all" class="">
		        <tr>
		            <td width="130"><strong>MRR/System ID:</strong></td>
		            <td width="230"><? echo $dataArray[0][csf('recv_number')]; ?></td>
		            <td width="110"><strong> Receive Basis :</strong></td>
		            <td><? echo $receive_basis_arr[$dataArray[0][csf('receive_basis')]]; ?></td>
		            <td ><strong>Received Date:</strong></td>
		            <td><? echo  change_date_format($dataArray[0][csf('receive_date')]); ?></td>
		        </tr>
		        <tr>
		            <td><strong>Challan No :</strong></td> <td width="175px"><? echo $dataArray[0][csf('challan_no')]; ?></td>
		            <td><strong>Currency:</strong></td><td width="175px" ><? echo $currency[$dataArray[0][csf('currency_id')]]; ?></td>
		            <td><strong>Store Name:</strong></td><td width="175px"><? echo $store_name_arr[$dataArray[0][csf('store_id')]]; ?></td>
		        </tr>
		        <tr>
		            <td><strong>Supplier:</strong></td><td width="175px"><? echo $supplier_library[$dataArray[0][csf('supplier_id')]]; ?></td>
		            <td><strong>L/C:</strong></td><td width="175px"><? echo $dataArray[0][csf('lc_no')]; ?></td>
		            <td><strong> Exchange Rate:</strong></td><td width="175px"><? echo $dataArray[0][csf('exchange_rate')]; ?></td>
		        </tr>
		        
		        <tr>
		            <td><strong>WO/PI:</strong></td><td width="175px"><? echo $dataArray[0][csf('booking_no')]; ?></td>
		            <td><strong>Source:</strong></td><td width="175px"><? echo $source[$dataArray[0][csf('source')]]; ?></td>
		            <td><strong>Pay mode:</strong></td><td width="175px"><? if ($dataArray[0][csf('receive_basis')]==2) echo $pay_mode[$wo_library[$dataArray[0][csf('booking_id')]]]; else echo ''; ?></td>
		        </tr>
		        <tr>
		            <td><strong>Item Catagory:</strong></td><td width="175px"><? echo $item_category[$dataArray[0][csf('item_category')]]; ?></td>
					<td><strong>BOE/Mushak Challan No:</strong></td><td width="175px"><? echo $dataArray[0][csf('boe_mushak_challan_no')]; ?></td>
					<td><strong>BOE/Mushak Challan Date:</strong></td><td width="175px"><? echo $dataArray[0][csf('boe_mushak_challan_date')]; ?></td>
		        </tr>
	    	</table>
	    
	    <br>
		<div style="width:100%;">
	        <table cellspacing="0" width="<? echo $width; ?>"  border="1" rules="all" class="rpt_table" style="margin-bottom:-150px" >
	            <thead bgcolor="#dddddd">
	                <th width="30">SL</th>
	                <th width="110" align="center">Job/PO/Buyer/Style</th>
	                <th width="110" align="center">Item Group</th>
	                <th width="140" align="center">Item Des.</th>
					<th width="70" align="center">Item Color</th>
                    <th width="70" align="center">Item Size</th>
	                <th width="40" align="center">UOM</th>
	                <th width="70" align="center">WO Qty </th>
	                <th width="70" align="center">Prev. Rec. Qty </th>
	                <th width="70" align="center">Curr. Rec. Qty </th>
	                <th width="70" align="center">Tot. Rec. Qty </th>
	                <th width="70" align="center">WO Balance</th>
	                <?
					// if($rate_hide_inventory!=1)
					// {
					// 	?>
	                 	<!-- <th width="60" align="center">Rate</th>
	            	    <th width="70" align="center">Amount</th> -->
	                     <?
					// }
					?>

	                <th width="70">Comments</th>
	                <th width="60" align="center">Room No</th>
	                <th width="60" align="center">Rack No</th>
	                <th width="60" align="center">Shelf No</th>
	                <th width="60" align="center">Box/Bin</th>
	            </thead>
	    	<? 

		 	if($dataArray[0][csf('receive_basis')]==1 || $dataArray[0][csf('receive_basis')]==2)
		 	{
				if($dataArray[0][csf('receive_basis')]==2)
				{
					if($dataArray[0][csf('booking_without_order')]==0)
					{
						if($db_type==0) $null_val="c.color_number_id,c.item_color,c.item_size,c.gmts_sizes,";
						else if($db_type==2) $null_val="nvl(c.color_number_id,0) as color_number_id,nvl(c.item_color,0) as item_color,nvl(c.gmts_sizes,0) as gmts_sizes,c.item_size,";

						$sql_bookingqty =sql_select("select c.cons as wo_qnty, b.id as dtls_id, c.id, b.trim_group as item_group, $null_val c.description as description, c.brand_supplier, b.po_break_down_id as po_id, b.sensitivity 
						from wo_booking_mst a, wo_booking_dtls b, wo_trim_book_con_dtls c 
						where a.booking_no=b.booking_no and b.id=c.wo_trim_booking_dtls_id and a.booking_no='".$dataArray[0][csf('booking_no')]."' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
					}
					else
					{
						$sql_bookingqty = sql_select("select sum(a.trim_qty) as wo_qnty, a.trim_group as item_group, a.fabric_color as item_color, a.gmts_color as color_number_id, a.fabric_description as description 
						from wo_non_ord_samp_booking_dtls a where  a.booking_no='".$dataArray[0][csf('booking_no')]."' and a.status_active=1 and a.is_deleted=0
						group by  a.trim_group,a.fabric_color,a.gmts_color,a.fabric_description");	
					}
				}
				else
				{
					$sql_bookingqty = sql_select("select c.id as trims_cons_dtls_id, c.cons as wo_qnty, b.item_group, c.item_color, c.color_number_id as color_number_id, c.description as description, c.po_break_down_id 
					from com_pi_master_details a, com_pi_item_details b, wo_trim_book_con_dtls c 
					where a.id=b.pi_id and b.work_order_dtls_id=c.wo_trim_booking_dtls_id and a.id='".$dataArray[0][csf('booking_id')]."' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.cons>0");	
				}
				
				foreach($sql_bookingqty as $b_qty)
				{
					$desc=trim(strtolower($b_qty[csf('description')]));
					if($dataArray[0][csf('receive_basis')]==2)
					{
						if($dataArray[0][csf('booking_without_order')]==0)
						{
							//if($b_qty[csf('sensitivity')]=='' || $b_qty[csf('sensitivity')]==0) $sensitivity=0;
							//if($b_qty[csf('gmts_size')]=='' || $b_qty[csf('gmts_size')]==0) $gmts_size=0;
							$booking_qty_arr[$b_qty[csf('po_id')]][$b_qty[csf('item_group')]][$b_qty[csf('color_number_id')]][$b_qty[csf('item_color')]][$desc][trim(strtolower($b_qty[csf('item_size')]))][$b_qty[csf('sensitivity')]][trim($b_qty[csf('brand_supplier')])]+=$b_qty[csf('wo_qnty')];
						}
						else
						{
							$booking_qty_arr[$b_qty[csf('item_group')]][$b_qty[csf('color_number_id')]][$b_qty[csf('item_color')]][$desc]+=$b_qty[csf('wo_qnty')];	
						}
					}
					else
					{
						if($trims_cons_dtls_id_check[$b_qty[csf('trims_cons_dtls_id')]]=="")
						{
							$trims_cons_dtls_id_check[$b_qty[csf('trims_cons_dtls_id')]]=$b_qty[csf('trims_cons_dtls_id')];
							$booking_qty_arr[$b_qty[csf('po_break_down_id')]][$b_qty[csf('item_group')]][$b_qty[csf('color_number_id')]][$b_qty[csf('item_color')]][$desc]+=$b_qty[csf('wo_qnty')];
						}
						
					}
				
				}
			}
			//echo '<pre>';print_r($booking_qty_arr);
	        //$sql_dtls="select b.id,b.mst_id,b.item_group_id, b.item_description, b.order_id, b.gmts_color_id, b.item_color, b.item_size, b.receive_qnty from inv_receive_master a,inv_trims_entry_dtls b left join inv_goods_placement c on c.dtls_id=b.id and c.entry_form=24 where a.booking_no='".$dataArray[0][csf('booking_no')]."' and b.mst_id=a.id and b.status_active='1' and b.is_deleted='0'";
			if($booking_without_order==0)
			{
	        	$sql_dtls="select b.id, b.mst_id, b.item_group_id, b.item_description, c.po_breakdown_id as order_id, b.gmts_color_id, b.item_color, b.item_size, c.quantity as receive_qnty, b.brand_supplier, b.gmts_size_id, b.rate
				from inv_receive_master a, inv_trims_entry_dtls b, order_wise_pro_details c 
				where c.dtls_id=b.id and c.entry_form=24 and b.mst_id=a.id and b.status_active='1' and b.is_deleted='0' and a.booking_no='".$dataArray[0][csf('booking_no')]."'";
			}
			else
			{
				 $sql_dtls="select b.id, b.mst_id, b.item_group_id, b.item_description, b.order_id, b.gmts_color_id, b.item_color, b.item_size, b.receive_qnty, b.brand_supplier, b.gmts_size_id, b.rate
				 from inv_receive_master a, inv_trims_entry_dtls b 
				 where a.entry_form=24 and b.mst_id=a.id and b.status_active='1' and b.is_deleted='0' and a.booking_no='".$dataArray[0][csf('booking_no')]."'";
			}
	//		 	echo $sql_dtls;
		 	$po_breakdown_ids='';
			$sql_result=sql_select($sql_dtls);
			$sql_result_audited=sql_select($sql_dtls);
			foreach($sql_result as $rows)
			{
				//$prev_rcv_data[$row[csf("po_breakdown_id")]][$row[csf("item_group_id")]][trim($row[csf("item_description")])][$row[csf("brand_supplier")]][$row[csf("gmts_color_id")]][$row[csf("item_color")]][$row[csf("gmts_size_id")]][$row[csf("item_size")]][$row[csf("rate")]]["qnty"]+=$row[csf("qnty")];
				$key=$rows[csf('order_id')].$rows[csf('item_group_id')].trim(strtolower($rows[csf('item_description')])).$rows[csf('brand_supplier')].$rows[csf('gmts_color_id')].$rows[csf('item_color')].$rows[csf('gmts_size_id')].$rows[csf('item_size')].$rows[csf('rate')];
				if($rows[csf('mst_id')] != $dataArray[0][csf('id')])
				{
					$prev_qty_arr[$key]+=$rows[csf('receive_qnty')];
				}
				$tot_qty_arr[$key]+=$rows[csf('receive_qnty')];
				$po_breakdown_ids .=$rows[csf('order_id')].',';
				//$prod_ids .=$row[csf('prod_id')].',';
			}
	//			echo "<pre>";
	//			print_r($tot_qty_arr);
			$po_breakdown_ids=implode(",",array_unique(explode(",",chop($po_breakdown_ids,','))));
			$rcvRtn_qty_sql = "SELECT b.po_breakdown_id ,sum( b.quantity ) as recv_return_qty, a.item_group_id as ITEM_GROUP_ID, a.item_description as ITEM_DESCRIPTION, a.color as COLOR, a.item_color as ITEM_COLOR, a.gmts_size as GMTS_SIZE   from product_details_master a, inv_transaction t, order_wise_pro_details b, wo_po_break_down c, wo_po_details_master d
			where a.id=b.prod_id and a.id=t.prod_id and t.id=b.trans_id and b.po_breakdown_id=c.id and c.job_no_mst=d.job_no and a.id=b.prod_id and  b.po_breakdown_id in ($po_breakdown_ids) and a.item_category_id=4 and b.entry_form in(49) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  
			group by b.po_breakdown_id, a.item_group_id , a.item_description, a.color, a.item_color, a.gmts_size";
			// echo $rcvRtn_qty_sql;die;
			$totalRcvRtnQty_arr=array();
			$rcvRtn_qtyArray=sql_select($rcvRtn_qty_sql); 
			foreach($rcvRtn_qtyArray as $row)
			{ 
				$key=$rows[csf('po_breakdown_id')].$rows['item_group_id'].strtoupper($rows['ITEM_DESCRIPTION']).$rows['COLOR'].$rows['ITEM_COLOR'].$rows['GMTS_SIZE'];
				$totalRcvRtnQty_arr[$key]=$row[csf('recv_return_qty')];
			}

			
	        $i=1; 
	        $mst_id=$dataArray[0][csf('id')];
			if($booking_without_order==0)
			{
				$sql_dtls="select b.item_group_id, b.item_description, c.po_breakdown_id as order_id, b.gmts_color_id, b.item_color, b.item_size, b.sensitivity, b.brand_supplier, b.order_uom, b.cons_uom, max(b.room_no) as room_no, max(b.rack_no) as rack_no, max(b.self_no) as self_no, max(b.box_bin_no) as box_bin_no, sum(b.cons_qnty) as cons_qnty, sum(c.quantity) as receive_qnty, b.gmts_size_id, b.rate, sum(c.quantity*b.rate) as amount, sum(c.reject_qty) as reject_receive_qnty,b.remarks
				from inv_trims_entry_dtls b, order_wise_pro_details c 
				where c.dtls_id=b.id and c.entry_form=24 and b.mst_id='$mst_id' and b.status_active='1' and b.is_deleted='0'
				group by b.item_group_id, b.item_description, c.po_breakdown_id, b.gmts_color_id, b.item_color, b.item_size, b.sensitivity, b.brand_supplier, b.order_uom, b.cons_uom,b.remarks, b.gmts_size_id, b.rate";
	        	
			}
			else
			{
	        	$sql_dtls="select b.item_group_id, b.item_description, b.gmts_color_id, b.item_color, b.item_size, b.sensitivity, b.brand_supplier, b.cons_uom, b.order_uom, b.room_no, b.rack_no, b.self_no, b.box_bin_no, b.order_id, b.cons_qnty, b.receive_qnty, b.gmts_size_id, b.rate, b.amount, b.reject_receive_qnty ,b.remarks
				from inv_trims_entry_dtls b  
				where b.mst_id='$mst_id' and b.status_active='1' and b.is_deleted='0'";
			}
	    	//echo $sql_dtls;
			$lib_room_arr=return_library_array( "select a.company_id,b.location_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,a.floor_room_rack_name,a.status_active, a.floor_room_rack_id,b.floor_room_rack_dtls_id from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.room_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and a.company_id=$data[0] order by b.floor_id", "room_id","floor_room_rack_name" ); 
			$lib_rack_arr=return_library_array( "select a.company_id,b.location_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,a.floor_room_rack_name,a.status_active, a.floor_room_rack_id,b.floor_room_rack_dtls_id from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.rack_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and a.company_id=$data[0] order by b.floor_id", "rack_id","floor_room_rack_name" ); 
			$lib_shelf_arr=return_library_array( "select a.company_id,b.location_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,a.floor_room_rack_name,a.status_active, a.floor_room_rack_id,b.floor_room_rack_dtls_id from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.shelf_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and a.company_id=$data[0] order by b.floor_id", "shelf_id","floor_room_rack_name" ); 
			$lib_bin_arr=return_library_array( "select a.company_id,b.location_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,a.floor_room_rack_name,a.status_active, a.floor_room_rack_id,b.floor_room_rack_dtls_id from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.bin_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and a.company_id=$data[0] order by b.floor_id", "bin_id","floor_room_rack_name" ); 
	        $sql_result=sql_select($sql_dtls);
			//echo "<pre>";print_r($booking_qty_arr);
	        foreach($sql_result as $row)
	        {
				$key=$row[csf('order_id')].$row[csf('item_group_id')].trim(strtolower($row[csf('item_description')])).$row[csf('brand_supplier')].$row[csf('gmts_color_id')].$row[csf('item_color')].$row[csf('gmts_size_id')].$row[csf('item_size')].$row[csf('rate')];
	            if ($i%2==0)  
	                $bgcolor="#E9F3FF";
	            else
	                $bgcolor="#FFFFFF";
	                
	                $order_no=$row[csf('order_id')];
	                if($db_type==0)
	                { 
	                    $po_number = return_field_value("po_number as po_number","wo_po_break_down"," id in ('$order_no')","po_number");
						$po_number_job = return_field_value("a.job_no as job_no","wo_po_details_master a,wo_po_break_down b"," a.job_no=b.job_no_mst and b.id in ('$order_no')","job_no");
						$po_number_buyer = return_field_value("a.buyer_name as buyer_name","wo_po_details_master a,wo_po_break_down b"," a.job_no=b.job_no_mst and b.id in ('$order_no')","buyer_name");
						$style_no = return_field_value("a.style_ref_no as style_ref_no","wo_po_details_master a,wo_po_break_down b"," a.job_no=b.job_no_mst and b.id in ($order_no)","style_ref_no");
	                }
	                else
	                {
	                    $po_number = return_field_value("po_number as po_number","wo_po_break_down"," id in ($order_no)","po_number");
						$po_number_job = return_field_value("a.job_no as job_no","wo_po_details_master a,wo_po_break_down b"," a.job_no=b.job_no_mst and b.id in ($order_no)","job_no");
						$po_number_buyer = return_field_value("a.buyer_name as buyer_name","wo_po_details_master a,wo_po_break_down b","a.job_no=b.job_no_mst and b.id in ($order_no)","buyer_name");
						$style_no = return_field_value("a.style_ref_no as style_ref_no","wo_po_details_master a,wo_po_break_down b"," a.job_no=b.job_no_mst and b.id in ($order_no)","style_ref_no");
							
	                }
					//echo  $po_number.'aaa';
					//$po_number=implode(",",array_unique(explode(",",$po_number)));
					$descp=trim(strtolower($row[csf('item_description')]));
					//echo $descp.'system';
					if($dataArray[0][csf('receive_basis')]==2)
					{
						if($dataArray[0][csf('booking_without_order')]==0)
						{
							// if($row[csf('sensitivity')]=='' || $row[csf('sensitivity')]==0) $sensitivity=0;
							//if($row[csf('gmts_size')]=='' || $row[csf('gmts_size')]==0) $gmts_size=0;
							
							// $po_no=explode(",",$order_no);
							// $woorder_qty=0;
							// foreach($po_no as $po_id)
							 //{
								$woorder_qty=$booking_qty_arr[$order_no][$row[csf('item_group_id')]][$row[csf('gmts_color_id')]][$row[csf('item_color')]][$descp][trim(strtolower($row[csf('item_size')]))][$row[csf('sensitivity')]][trim($row[csf('brand_supplier')])]; 
								//if($woorder_qty<1) echo $order_no."=".$row[csf('item_group_id')]."=".$row[csf('gmts_color_id')]."=".$row[csf('item_color')]."=".$descp."=".trim(strtolower($row[csf('item_size')]))."=".$row[csf('sensitivity')]."=".trim($row[csf('brand_supplier')])."<br>";
							 //}
							
						}
						else
						{
						 	$woorder_qty=$booking_qty_arr[$row[csf('item_group_id')]][$row[csf('gmts_color_id')]][$row[csf('item_color')]][$descp];	
						}
					}
					else
					{
						 $woorder_qty=$booking_qty_arr[$order_no][$row[csf('item_group_id')]][$row[csf('gmts_color_id')]][$row[csf('item_color')]][$descp];	
					}
					//$buyer=explode(",",$po_number_buyer);
					//$buyer_id='';
					//foreach($buyer as $bid)
					//{
						if($buyer_id=='') $buyer_id=$buyer_name_library[$bid]; else $buyer_id.=",".$buyer_name_library[$bid];
					//}
					$buyer_id=$buyer_name_library[$po_number_buyer];//$po_number_buyer

					$rcvRtnQty=$totalRcvRtnQty_arr[$key];
					$prevRcvQty=$prev_qty_arr[$key]-$rcvRtnQty;
					//$prevRcvQty=$tot_qty_arr[$key]-$rcvRtnQty;
					//$prevRcvQty=$prev_qty_arr[$key];
	//					echo $rcvRtnQty.'++'.$prev_qty_arr[$key];
					$uom_check_arr[$row[csf('order_uom')]]=$row[csf('order_uom')];
	                ?>
	                <tr bgcolor="<? echo $bgcolor; ?>">
	                    <td><? echo $i; ?></td>
	                    <td><p><? echo $po_number_job.'<br>'.$po_number.'<br>'.$buyer_id.'<br>'.$style_no;//$item_category[4]; ?></p></td>
	                    <td><p><? echo $item_library[$row[csf('item_group_id')]] ?></p></td>
	                    <td><p><? echo $row[csf('item_description')]; ?></p></td>
						<td><p><? echo $color_library[$row[csf('item_color')]]; ?></p></td>
				        <td><p><? echo $row[csf('item_size')]; ?></p></td>
	                    <td><p><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?></p></td>
	                    <td align="right"><div style="word-wrap:break-word;">
							<? //$woorder_qty=$booking_qty_arr[$row[csf('item_group_id')]][$row[csf('gmts_color_id')]][$row[csf('item_color')]][$row[csf('item_description')]];
							//$total_woorder_qty+=$woorder_qty;
	                         echo number_format($woorder_qty,2,".","");; 
	                         ?>
	                     </div></td>
	                    <td align="right"><? echo number_format($prevRcvQty,2,".",""); ?></td>
	                    <td align="right"><? echo number_format($row[csf('receive_qnty')],2,".",""); ?></td>
	                    <td align="right"><? echo number_format($prevRcvQty+$row[csf('receive_qnty')],2,".",""); ?></td>
	                   
	                    <td align="right"><? echo number_format($woorder_qty-($prevRcvQty+$row[csf('receive_qnty')]),4,'.',''); ?></td>
	                    <?
						//if($rate_hide_inventory!=1)
						//{
							?>
		                    <!-- <td align="right"><?// echo number_format($row[csf('rate')],4,'.',''); ?></td>
	                    	<td align="right"><?// echo number_format($row[csf('amount')],2,'.',''); ?></td> -->
		                    <?
						//}
						?>

	                    <td> <?php echo $row[csf('remarks')]; ?></td>
	                    <td align="right"><? echo $lib_room_arr[$row[csf('room_no')]]; ?></td>
	                    <td align="right"><? echo $lib_rack_arr[$row[csf('rack_no')]]; ?></td>
	                    <td align="right"><? echo $lib_shelf_arr[$row[csf('self_no')]]; ?></td>
	                    <td align="right"><? echo $lib_bin_arr[$row[csf('box_bin_no')]]; ?></td>
	                   
	                </tr>
	            <?
				$i++;
				$tot_woorder_qty+=$woorder_qty;
				$tot_prevRcvQty+=$prevRcvQty;
				$tot_rec_qty+=$row[csf('receive_qnty')];
				$tot_amount+=$row[csf('amount')];
				$tot_reject_qty+=$row[csf('reject_receive_qnty')];
				$col_tot_rcv+=$prevRcvQty+$row[csf('receive_qnty')];
	        }
	       ?>
	            <tr bgcolor="#dddddd">
	                <td>&nbsp;</td>
	                <td>&nbsp;</td>
	                <td>&nbsp;</td>
	                <td colspan="4" align="right"><b>Total :</b></td>
	                <td align="right"><?  echo number_format($tot_woorder_qty,2); ?></td>
	                <td align="right"><? echo number_format($tot_prevRcvQty,2); ?></td>
	                <td align="right"><? echo number_format($tot_rec_qty,2); ?></td>
	                <td align="right"><? echo number_format($col_tot_rcv,2); ?></td>
	                <td>&nbsp;</td>
	                <?
					//if($rate_hide_inventory!=1)
					//{
						?>
	                    <!-- <td>&nbsp;</td> -->
	            		<!-- <td align="right"><?// echo number_format($tot_amount,2,'.',''); ?></td> -->
	                    <?
					//}
					?>
	                <td align="center">&nbsp;</td>
	                <td align="center">&nbsp;</td>
	                <td align="center">&nbsp;</td>
	                <td align="center">&nbsp;</td>
	                <td align="center">&nbsp;</td>
	            </tr>
	       </table>
	   		
	       <br>
	       <?
			  echo signature_table(35, $data[0], "1010px");
		   ?>
		</div>
	</div>
	<?
	exit();
}


if($action=="print_button_variable_setting")
{
    $print_report_format=0;
    $print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=6 and report_id=191 and is_deleted=0 and status_active=1");
   	$printButton=explode(',',$print_report_format);

	foreach($printButton as $id)
	{
		if($id==86)$buttonHtml.='<input id="Print1" type="button" class="formbutton" style="width:80px" onClick="fnc_trims_receive(4)" name="print" value="Print">';
		if($id==116)$buttonHtml.='<input id="Print2" type="button" class="formbutton" style="width:80px" onClick="fnc_trims_receive(5)" name="print" value="Print 2">';
		if($id==136)$buttonHtml.='<input id="Print3" type="button" class="formbutton" style="width:80px" onClick="fnc_trims_receive(6)" name="print" value="Print 3">';
		if($id==137)$buttonHtml.='<input id="Print4" type="button" class="formbutton" style="width:80px" onClick="fnc_trims_receive(7)" name="print" value="Print 4">';
	}

   echo "document.getElementById('button_data_panel').innerHTML = '".$buttonHtml."';\n";
   exit();
}

if($action=="file_upload")
{
	header("Content-Type: application/json");
	$filename = time().$_FILES['file']['name']; 
	$location = "../../../file_upload/".$filename; 
    //echo "0**".$filename; die;
	$uploadOk = 1;
	if(empty($mst_id))
	{
		$mst_id=$_GET['mst_id'];
	} 
    
	if(move_uploaded_file($_FILES['file']['tmp_name'], $location))
	{ 
		$uploadOk = 1;
	}
	else
	{ 
		$uploadOk=0; 
	} 
    // echo "0**".$uploadOk; die;
	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}

	$id=return_next_id( "id","COMMON_PHOTO_LIBRARY", 1 ) ;
	$data_array .="(".$id.",'".$mst_id."','trims_receive_entry','file_upload/".$filename."','2','".$filename."','".$pc_date_time."')";
	$field_array="id,master_tble_id,form_name,image_location,file_type,real_file_name,insert_date";
	$rID=sql_insert("COMMON_PHOTO_LIBRARY",$field_array,$data_array,1);

	if($db_type==0)
	{
		if($rID==1 && $uploadOk==1)
		{
			mysql_query("COMMIT");
			echo "0**".$new_system_id[0]."**".$mst_id;
		}
		else
		{
			mysql_query("ROLLBACK");
			echo "10**".$mst_id;
		}
	}
	else if($db_type==2 || $db_type==1 )
	{
		if($rID==1 && $uploadOk==1)
		{
			oci_commit($con);
			echo "0**".$new_system_id[0]."**".$mst_id;
		}
		else
		{
			oci_rollback($con);
			echo "10**".$rID."**".$uploadOk."**INSERT INTO COMMON_PHOTO_LIBRARY(".$field_array.") VALUES ".$data_array;
		}
	}
	disconnect($con);
	die;
}
?>

