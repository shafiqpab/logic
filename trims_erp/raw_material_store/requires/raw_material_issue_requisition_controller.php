<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
//$user_id = $_SESSION['logic_erp']["user_id"];
//========== user credential start ========
$user_id = $_SESSION['logic_erp']['user_id'];
$userCredential = sql_select("SELECT unit_id as company_id,company_location_id,store_location_id,item_cate_id,supplier_id FROM user_passwd where id='$user_id'");
$company_id 	= $userCredential[0][csf('company_id')];
$supplier_id 	= $userCredential[0][csf('supplier_id')];
$store_location_id = $userCredential[0][csf('store_location_id')];
$item_cate_id 	= $userCredential[0][csf('item_cate_id')];
$company_location_id 	= $userCredential[0][csf('company_location_id')];

if ($company_id !='') {
    $company_credential_cond = " and comp.id in($company_id)";
}

if ($company_location_id !='') {
    $company_location_credential_cond = " and lib_location.id in($company_location_id)";
}

if ($store_location_id !='') {
    $store_location_credential_cond = " and a.id in($store_location_id)";
}
if($item_cate_id !='') {
    $item_cate_credential_cond = $item_cate_id ;
}
else
{
	 $item_cate_credential_cond="".implode(",",array_flip($item_category))."";
}
if ($supplier_id !='') {
    $supplier_credential_cond = "and a.id in($supplier_id)";
}

//========== user credential end ==========

if ($action=="load_drop_down_location")
{
	//echo $company_location_credential_cond; die;
	echo create_drop_down( "cbo_location_name", 150, "select id,location_name from lib_location where company_id='$data' $company_location_credential_cond and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-Select Location-", $selected, "load_drop_down('requires/raw_material_issue_requisition_controller', document.getElementById('cbo_company_name').value+'_'+this.value, 'load_drop_down_floor', 'floor_td');load_drop_down( 'requires/raw_material_issue_requisition_controller', document.getElementById('cbo_company_name').value+'_'+this.value, 'load_drop_down_store','store_td');", '', '', '', '', '',3 );
	exit();	 
}

if ($action=="load_drop_down_type")
{
	if($data==2) $typeArray=$wash_dry_process; else if ($data==3) $typeArray=$wash_laser_desing; else $typeArray=$blank_array;
	echo create_drop_down( "txtWashType_1", 92, $typeArray,"", 1, "-Select Type-", $selected ,"",0,'','','','','','',"txtWashType[]"); 
	exit();
}

if ($action=="load_drop_down_store")
{
	$data=explode("_",$data);
	$company_id=$data[0];
	$location_id=$data[1];

	// echo "select a.id, a.store_name from lib_store_location a where a.is_deleted=0 and a.company_id='$company_id' and location_id=$location_id order by a.store_name";
	

	/*echo create_drop_down('cbo_store_name', 150, "select a.id, a.store_name from lib_store_location a where a.is_deleted=0 and a.company_id='$company_id' and location_id=$location_id order by a.store_name", 'id,store_name', 1, '-- Select --', $selected, "loadStock('$company_id"."_"."$location_id"."_"."'+this.value)");*/

	echo create_drop_down('cbo_store_name', 150, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id and b.category_type in (101,22) $store_location_credential_cond group by a.id, a.store_name order by store_name ", 'id,store_name', 1, '-- Select --', $selected, "loadStock('$company_id"."_"."$location_id"."_"."'+this.value)");
	 exit();
}

if($action=="print_button_variable_setting")
{
    $print_report_format=0;
    $print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=17 and report_id=210 and is_deleted=0 and status_active=1");
    echo "print_report_button_setting('".$print_report_format."');\n";
    exit();
}

if ($action == 'load_stock_by_store') 
{
	$data = explode('**', $data);
	$rowCount = $data[1];
	$itemGroupIds = $data[2];
	$productIds = $data[3];
	$data = explode('_', $data[0]);
	$companyId = $data[0];
	$locationId = $data[1];
	$storeId = $data[2];
	$cons_closing_stock_value = array();

	$itemGroupIdArr = explode(',', $itemGroupIds);
	$productIdArr = explode(',', $productIds);
	
	
	//echo $productIds; die;

	/*$stock_arr = sql_select("select b.id, b.item_category_id, b.item_group_id, b.current_stock, b.section_id
    	from product_details_master b
   		where b.status_active = 1 and b.is_deleted = 0 and b.item_category_id=101 and b.store_id=$storeId and b.company_id=$companyId");*/

	/*echo "select b.id, b.item_category_id, b.item_group_id, b.current_stock, b.section_id
    	from product_details_master b
   		where b.status_active = 1 and b.is_deleted = 0 and b.item_category_id=101 and b.store_id=$storeId and b.company_id=$companyId";*/

   	/*$trans_sql="select b.id, a.transaction_date, a.transaction_type, a.cons_quantity, a.cons_amount, a.batch_lot, a.order_qnty, a.order_amount, a.id, b.section_id
	from inv_transaction a, product_details_master b
	where a.prod_id=b.id and a.status_active=1 and b.item_category_id=101 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$companyId and a.store_id=$storeId";*/

	/*$trans_sql="select a.id, b.current_stock, b.item_group_id
  				from inv_transaction a, product_details_master b
			where a.prod_id=b.id and a.status_active=1 and b.item_category_id=101 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$companyId and a.store_id=$storeId";*/

	$trans_sql = "select distinct a.item_group_id,a.id,sum((case when b.transaction_type in(1,4,5) then b.cons_quantity else 0 end)-(case when b.transaction_type in(2,3,6) then b.cons_quantity else 0 end)) as current_stock
	from product_details_master a, inv_transaction b
	where a.id=b.prod_id and b.store_id=$storeId and a.company_id=$companyId  and a.id in ($productIds) and a.item_group_id in($itemGroupIds) and  a.item_category_id in(101,22) and a.status_active=1 and b.status_active=1 and b.is_deleted=0 group by  a.id,a.item_group_id";

	
	
	//echo $trans_sql;
	$trnasactionData = sql_select($trans_sql);
	//echo count($trnasactionData).jahid;die;
	foreach($trnasactionData as $row_p)
	{
		if(isset($row_p[csf("item_group_id")])) {
			$cons_closing_stock_value[$row_p[csf("item_group_id")]][$row_p[csf("id")]]["current_stock"]+=$row_p[csf("current_stock")];
		} else {
			$cons_closing_stock_value[$row_p[csf("item_group_id")]][$row_p[csf("id")]]["current_stock"]=$row_p[csf("current_stock")];
		}
	}

//echo "<pre>";
//print_r($cons_closing_stock_value);

//echo $rowCount; die;

	for($i=1; $i<=$rowCount; $i++)
	 {
		$stock = $cons_closing_stock_value[$itemGroupIdArr[$i-1]][$productIdArr[$i-1]]['current_stock'] ? $cons_closing_stock_value[$itemGroupIdArr[$i-1]][$productIdArr[$i-1]]['current_stock'] : 0;
		
		//echo $itemGroupIdArr[$i-1]."==".$productIdArr[$i-1];
		
		echo "document.getElementById('txtStock_".$i."').value = '".$stock."';\n";
	}

	exit();
}

if ($action=="load_drop_down_floor")
{
	$data=explode("_",$data);
	$company_id=$data[0];
	$location_id=$data[1];
	if($location_id==0 || $location_id=="") $location_cond=""; else $location_cond=" and b.location_id=$location_id";

	if($company_id==0 && $location_id==0)
	{
		echo create_drop_down( "cbo_floor_id", 150, $blank_array,"", 1, "--Select Floor--", 0, "",0 );
	}
	else
	{
		echo create_drop_down( "cbo_floor_id", 150, "select a.id, a.floor_name from lib_prod_floor a, lib_machine_name b where a.id=b.floor_id and b.category_id=3 and b.company_id=$company_id and b.status_active=1 and b.is_deleted=0 and a.production_process=8 $location_cond group by a.id, a.floor_name order by a.floor_name","id,floor_name", 1, "--Select Floor--", 0, "load_machine();","" );
	}
  	exit();	 
}

if ($action=="load_drop_down_machine")
{
	$data_ex=explode("_",$data);
	$company_id=$data_ex[0];
	$floor_id=$data_ex[1];
	if($floor_id==0 || $floor_id=="") $floor_cond=""; else $floor_cond=" and floor_id=$floor_id";
	if($db_type==0)
	{
		$sql="select id,concat(machine_no,'-',brand) as machine_name from lib_machine_name where category_id=3 and company_id=$company_id and status_active=1 and is_deleted=0 and is_locked=0 $floor_cond order by machine_name";
	}
	else if($db_type==2)
	{
		$sql="select id, machine_no || '-' || brand as machine_name from lib_machine_name where category_id=3 and company_id=$company_id and status_active=1 and is_deleted=0 and is_locked=0 $floor_cond order by machine_name";
	}

	if($company_id==0 && $floor_id==0)
	{
		echo create_drop_down( "cbo_machine_id", 150, $blank_array,"", 1, "--Select Machine--", 0, "",0 );
	}
	else
	{
		echo create_drop_down( "cbo_machine_id", 150, $sql,"id,machine_name", 1, "--Select Machine--", 0, "","" );
	}
	exit();
}

if ($action == "load_drop_down_buyer") 
{
	$exdata=explode("_",$data);
	if($exdata[1]==1)
	{
		echo create_drop_down( "cbo_party_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Party--", $exdata[2], "",0);
	}
	else if($exdata[1]==2)
	{
		echo create_drop_down("cbo_party_name", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$exdata[0]' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (2,3)) order by buy.buyer_name", "id,buyer_name", 1, "--Select Party--", $exdata[2], "", 0);
	}
	else
	{
		echo create_drop_down( "cbo_party_name", 150, $blank_array,"", 1, "--Select Party--", $selected, "",1 );
	}
	exit();
}

if($action=="recipe_popup")
{
	echo load_html_head_contents("Recipe Pop-up","../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	//echo $data.'=='.$cbo_company_name;
	?>
	<script>
		function js_set_value(str)
		{ 
			$("#selected_str_data").val(str);
			parent.emailwindow.hide();
		}
		
		function search_by(val)
		{
			$('#txt_search_string').val('');
			if(val==1 || val==0) $('#search_by_td').html('Wash Job No');
			else if(val==2) $('#search_by_td').html('W/O No');
			else if(val==3) $('#search_by_td').html('Buyer Job');
			else if(val==4) $('#search_by_td').html('Buyer Po');
			else if(val==5) $('#search_by_td').html('Buyer Style');
		}
	</script>
</head>
<body>
<div align="center" style="width:100%;" >
    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="750" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
            <thead> 
                <tr>
                    <th colspan="7"><?php echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --",1 ); ?></th>
                </tr>
                <tr>               	 
                    <th width="140">Recipe</th>
                    <th width="100">Search By</th>
                    <th width="100" id="search_by_td">Buyer Po</th>
                    <th width="60">Job Year</th>
                    <th width="130" colspan="2">Receive Date Range</th>                            
                    <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /> </th>
                </tr>           
            </thead>
            <tbody>
                <tr class="general">
                    <td><input type="hidden" id="selected_str_data">
                        <input type="text" name="txt_search_recipe" id="txt_search_recipe" class="text_boxes" style="width:90px" placeholder="Search Recipe" />
                    </td>
                    <td>
						<?php
                            $search_by_arr=array(1=>"Wash Job No",2=>"W/O No",3=>"Buyer Job",4=>"Buyer Po",5=>"Buyer Style");
                            echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "",4,'search_by(this.value)',"","" );
                        ?>
                    </td>
                    <td align="center">
                        <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:90px" placeholder="" />
                    </td>
                    <td align="center"><?php echo create_drop_down( "cbo_year_selection", 60, $year,"", 1, "-- Select --", date('Y'), "",0 ); ?></td>
                    <td align="center"><input name="txt_date_from" id="txt_date_from" class="datepicker" placeholder="From" style="width:60px"></td>
                    <td align="center"><input name="txt_date_to" id="txt_date_to" class="datepicker" placeholder="To" style="width:60px"></td>
                    <td align="center">
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( '<?php echo $cbo_company_name; ?>'+'_'+document.getElementById('txt_search_recipe').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_year_selection').value, 'create_recipe_search_list_view', 'search_div', 'raw_material_issue_requisition_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" /></td>
                    </tr>
                    <tr class="general">
                        <td colspan="7" align="center" valign="middle"><?php echo load_month_buttons(); ?></td>
                    </tr>
                    
                    </tbody>
                </table>    
            </form>
            <div id="search_div"></div>
        </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if($action=="create_recipe_search_list_view")
{
	$exdata=explode('_',$data);
	$cbo_company_name=$exdata[0];
	$search_recipe=$exdata[1];
	$form_date=$exdata[2];
	$to_date=$exdata[3];
	
	$search_by=$exdata[4];
	$search_str=trim($exdata[5]);
	$search_type =$exdata[6];
	$year =$exdata[7];
	$within_group=$exdata[8];
	
	if($cbo_company_name!=0) $company=" and a.company_id='$cbo_company_name'"; else { echo "Please Select Company First."; die; }
	
	$job_cond=""; $style_cond=""; $po_cond=""; $search_com_cond="";
	if($search_type==1)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num='$search_str'";
			else if($search_by==2) $search_com_cond="and b.order_no='$search_str'";
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num = '$search_str' ";
			else if ($search_by==4) $po_cond=" and b.po_number = '$search_str' ";
			else if ($search_by==5) $style_cond=" and a.style_ref_no = '$search_str' ";
		}
	}
	else if($search_type==4 || $search_type==0)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str%'";  
			else if($search_by==2) $search_com_cond="and b.order_no like '%$search_str%'";  
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '%$search_str%'";  
			else if ($search_by==4) $po_cond=" and b.po_number like '%$search_str%'";   
			else if ($search_by==5) $style_cond=" and a.style_ref_no like '%$search_str%'"; 
		}
	}
	else if($search_type==2)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '$search_str%'";  
			else if($search_by==2) $search_com_cond="and b.order_no like '$search_str%'";  
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '$search_str%'";  
			else if ($search_by==4) $po_cond=" and b.po_number like '$search_str%'";  
			else if ($search_by==5) $style_cond=" and a.style_ref_no like '$search_str%'";  
		}
	}
	else if($search_type==3)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str'";  
			else if($search_by==2) $search_com_cond="and b.order_no like '%$search_str'";  
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '%$search_str'";  
			else if ($search_by==4) $po_cond=" and b.po_number like '%$search_str'";  
			else if ($search_by==5) $style_cond=" and a.style_ref_no like '%$search_str'";  
		}
	}	
	
	$po_ids='';
	
	if($db_type==0) $id_cond="group_concat(b.id)";
	else if($db_type==2) $id_cond="listagg(b.id,',') within group (order by b.id)";
	if(($job_cond!="" && $search_by==3) || ($po_cond!="" && $search_by==4)|| ($style_cond!="" && $search_by==5))
	{
		$po_ids = return_field_value("$id_cond as id", "wo_po_details_master a, wo_po_break_down b", "a.job_no=b.job_no_mst $job_cond $style_cond $po_cond", "id");
	}
	
	if ($po_ids!="") $po_idsCond=" and b.buyer_po_id in ($po_ids)"; else $po_idsCond="";

	if($search_recipe!='') $search_recipe_cond=" and d.recipe_no_prefix_num='$search_recipe'"; else $search_recipe_cond="";

	if($db_type==0)
	{ 
		if ($form_date!="" &&  $to_date!="") $order_rcv_date = "and a.receive_date between '".change_date_format($form_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'"; else $order_rcv_date ="";
		
		$year_select="YEAR(a.insert_date)";
		$year_cond=" and YEAR(a.insert_date)=$year";
	}
	else
	{
		if ($form_date!="" &&  $to_date!="") $order_rcv_date = "and a.receive_date between '".change_date_format($form_date, "", "",1)."' and '".change_date_format($to_date, "", "",1)."'"; else $order_rcv_date ="";
		$year_select="TO_CHAR(a.insert_date,'YYYY')";
		$year_cond=" and to_char(a.insert_date,'YYYY')=$year";
	}
	
	if($within_group==1)
	{
		$party_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	}
	else
	{
		$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	}
	$color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	
	$production_qty_arr=array();
	$prod_data_arr="select a.recipe_id, sum(b.qcpass_qty) as qty from trims_raw_mat_requisition_mst a, trims_raw_mat_requisition_dtls b where a.id=b.mst_id and a.entry_form=301 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.recipe_id";
	$prod_data_res=sql_select($prod_data_arr);
	
	foreach($prod_data_res as $row)
	{
		$production_qty_arr[$row[csf('recipe_id')]]=$row[csf('qty')];
	}
	unset($prod_data_res);
	$po_sql ="Select a.buyer_name, a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['buyer']=$row[csf("buyer_name")];
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
	}
	unset($po_sql_res);
	?>
    <body>
		<div align="center">
			<fieldset style="width:1070px;">
				<form name="searchprocessfrm_1"  id="searchprocessfrm_1" autocomplete="off">
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1070" class="rpt_table" >
						<thead>
							<th width="30">SL</th>
							<th width="50">Recipe</th>
                            <th width="50">Job</th>
                            <th width="80">Buyer</th>
                            <th width="80">WO No</th>
                            <th width="90">Buyer Po</th>
							<th width="90">Buyer Style</th>
                            <th width="90">Gmts. Item</th>
                            <th width="80">Body Part</th>
                            <th width="70">Process Name</th>
                            <th width="70">Wash Type</th>
                            <th width="70">Color</th>
                            <th width="70">Order Qty</th>
                            <th width="70">Prod Qty</th>
                            <th>Balance Qty</th>
						</thead>
					</table>
					<div style="width:1070px; overflow-y:scroll; max-height:300px;">
						<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1050" class="rpt_table" id="list_view" >
							<?
							$sql= "select a.id, a.job_no_prefix_num, a.subcon_job, a.party_id, a.within_group, b.id as order_id, b.order_no, b.buyer_po_id, b.main_process_id, b.gmts_item_id, b.embl_type, b.body_part, c.color_id, sum(c.qnty) as qty, d.id as recipe_id, d.recipe_no_prefix_num, d.recipe_no from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c, pro_recipe_entry_mst d where a.subcon_job=b.job_no_mst and b.id=c.mst_id and a.subcon_job=d.job_no and b.id=d.po_id 
							
							and b.main_process_id=d.embl_name and b.gmts_item_id=d.gmts_item and b.embl_type=d.embl_type and b.body_part=d.body_part and c.color_id=d.color_id
							
							and a.entry_form=295 and d.entry_form=300 and a.status_active=1 and b.status_active=1 and d.status_active=1 $order_rcv_date $company $search_com_cond $search_recipe_cond $po_idsCond group by a.id, a.job_no_prefix_num, a.subcon_job, a.party_id, a.within_group, b.id, b.order_no, b.buyer_po_id, b.main_process_id, b.gmts_item_id, b.embl_type, b.body_part, c.color_id, d.id, d.recipe_no_prefix_num, d.recipe_no order by d.recipe_no_prefix_num DESC";
							//echo $sql; die;
							$sql_res=sql_select($sql);

							$i=1; 
							foreach($sql_res as $row)
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								
								if($row[csf('main_process_id')]==1) $new_subprocess_array= $emblishment_print_type;
								else if($row[csf('main_process_id')]==2) $new_subprocess_array= $emblishment_embroy_type;
								else if($row[csf('main_process_id')]==3) $new_subprocess_array= $emblishment_wash_type;
								else if($row[csf('main_process_id')]==4) $new_subprocess_array= $emblishment_spwork_type;
								else if($row[csf('main_process_id')]==5) $new_subprocess_array= $emblishment_gmts_type;
								else $new_subprocess_array=$blank_array;
								
								$str="";
								$str=$row[csf('recipe_id')].'___'.$row[csf('recipe_no')].'___'.$row[csf('subcon_job')].'___'.$row[csf('order_id')].'___'.$row[csf('order_no')].'___'.$row[csf('party_id')].'___'.$row[csf('within_group')].'___'.$row[csf('qty')].'___'.$row[csf('buyer_po_id')].'___'.$buyer_po_arr[$row[csf("buyer_po_id")]]['po'].'___'.$buyer_po_arr[$row[csf("buyer_po_id")]]['style'];
								
								$prod_qty=0; $balance_qty=0;
								$prod_qty=$production_qty_arr[$row[csf('recipe_id')]];
								$balance_qty=$row[csf('qty')]-$prod_qty;
								
								$buyer_po=""; $buyer_style=""; $buyer_name='';
								$buyer_po_id=explode(",",$row[csf('buyer_po_id')]);
								foreach($buyer_po_id as $po_id)
								{
									if($buyer_po=="") $buyer_po=$buyer_po_arr[$po_id]['po']; else $buyer_po.=','.$buyer_po_arr[$po_id]['po'];
									if($buyer_style=="") $buyer_style=$buyer_po_arr[$po_id]['style']; else $buyer_style.=','.$buyer_po_arr[$po_id]['style'];
									if($buyer_name=="") $buyer_name=$buyer_arr[$buyer_po_arr[$po_id]['buyer']]; else $buyer_name.=','.$buyer_arr[$buyer_po_arr[$po_id]['buyer']];
								}
								$buyer_po=implode(",",array_unique(explode(",",$buyer_po)));
								$buyer_style=implode(",",array_unique(explode(",",$buyer_style)));
								$buyer_name=implode(",",array_unique(explode(",",$buyer_name)));
								?>
								<tr bgcolor="<?php echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<?php echo $i;?>" onClick="js_set_value('<?php echo $str;?>')"> 
									<td width="30" align="center"><?php echo $i; ?></td>	
									<td width="50" align="center"><?php echo $row[csf('recipe_no_prefix_num')]; ?></td>
                                    <td width="50" align="center"><?php echo $row[csf('job_no_prefix_num')]; ?></td>
                                    <td width="80" style="word-break:break-all"><?php echo $buyer_name; ?></td>
                                    <td width="80" style="word-break:break-all"><?php echo $row[csf('order_no')]; ?></td>
                                    
                                    <td width="90" style="word-break:break-all"><?php echo $buyer_po; ?></td>
                                    <td width="90" style="word-break:break-all"><?php echo $buyer_style; ?></td>
                                    <td width="90" style="word-break:break-all"><?php echo $garments_item[$row[csf('gmts_item_id')]]; ?></td>
                                    <td width="80" style="word-break:break-all"><?php echo $body_part[$row[csf('body_part')]]; ?></td>
                                    <td width="70" style="word-break:break-all"><?php echo $emblishment_name_array[$row[csf('main_process_id')]]; ?></td>
                                    <td width="70" style="word-break:break-all"><?php echo $new_subprocess_array[$row[csf('embl_type')]]; ?></td>
                                    <td width="70" style="word-break:break-all"><?php echo $color_arr[$row[csf('color_id')]]; ?></td>
                                    <td width="70" align="right"><?php echo number_format($row[csf('qty')],2); ?></td>
                                    <td width="70" align="right"><?php if($prod_qty>0) {echo number_format($prod_qty,2);} else {echo "";} ?></td>
                                    <td align="right"><?php if($balance_qty!=0) {echo number_format($balance_qty,2);} else {echo "";} ?></td>
								</tr>
								<?
								$i++;
							}
							?>
						</table>
					</div>
				</form>
			</fieldset>
		</div>    
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();	
}


if ($action == "batch_popup") 
{
	echo load_html_head_contents("Batch Info", "../../../", 1, 1, '', '1', '');
	extract($_REQUEST);
	?>
    <script>
        function js_set_value(datas) 
        {
            //alert (batch_id);
            document.getElementById('selected_str_data').value = datas;
            parent.emailwindow.hide();
        }
    </script>
    </head>
    <body>
    <div align="center">
        <fieldset style="width:750px;margin-left:0px;">
            <form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
                <table cellpadding="0" cellspacing="0" border="1" rules="all" width="750" class="rpt_table">
                    <thead>
                        <tr>
                            <th colspan="5"><?php echo create_drop_down("cbo_string_search_type", 130, $string_search_type, '', 1, "-- Searching Type --"); ?></th>
                        </tr>
                        <tr>
                            <th>Batch</th>
                            <th>From Date</th>
                            <th>To Date</th>
                            <th>
                                <input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton"/>
                                <input type="hidden" name="selected_str_data" id="selected_str_data" value="">
                            </th>
                        </tr>
                    </thead>
                    <tr class="general">
                        <td style="display: none"><?php echo create_drop_down("cbo_search_by", 150, $order_source, "", 1, "--Select--", 2, 0, 0); ?></td>
                        <td><input type="text" style="width:240px" class="text_boxes" name="txt_search_common" id="txt_search_common"/></td>
                        <td><input id="txt_date_from" class="datepicker" type="text" value="" style="width:70px;" name="txt_date_from"></td>
                        <td><input id="txt_date_to" class="datepicker" type="text" value="" style="width:70px;" name="txt_date_to"></td>
                        <td align="center"><input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+'<?php echo $cbo_company_name; ?>'+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'create_batch_search_list_view', 'search_div', 'raw_material_issue_requisition_controller', 'setFilterGrid(\'list_view\',-1);')" style="width:100px;"/>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5" align="center">
							<?php echo load_month_buttons(1); ?>
                        </td>
                    </tr>
                </table>
                <div id="search_div" style="margin-top:10px"></div>
            </form>
        </fieldset>
    </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
	<?
	exit();
}

if ($action == "create_batch_search_list_view") 
{
	//print_r ($data);
	$data = explode('_', $data);
	$search_common = trim($data[0]);
	$search_by = $data[1];
	$company_id = $data[2];
	$search_type = $data[3];
	$txt_date_from = $data[4];
	$txt_date_to = $data[5];

	if ($search_common == "") 
	{
		if($db_type==0)
		{ 
			$txt_date_from=change_date_format($txt_date_from,'yyyy-mm-dd'); 
			$txt_date_to=change_date_format($txt_date_to,'yyyy-mm-dd'); 
		}
		else
		{ 
			$txt_date_from=change_date_format($txt_date_from, "", "",1); 
			$txt_date_to=change_date_format($txt_date_to, "", "",1); 
		}

		if(str_replace("'","",trim($txt_date_from))=="" || str_replace("'","",trim($txt_date_to))=="") $date_cond=""; else $date_cond=" and a.batch_date between '$txt_date_from' and '$txt_date_to'";
	}
	else $date_cond="";

	if ($search_type == 1) 
	{
		if ($search_common != '') $batch_cond = " and a.batch_no='$search_common'"; else $batch_cond = "";
	} 
	else if ($search_type == 4 || $search_type == 0) 
	{
		if ($search_common != '') $batch_cond = " and a.batch_no like '%$search_common%'"; else $batch_cond = "";
	} 
	else if ($search_type == 2) 
	{
		if ($search_common != '') $batch_cond = " and a.batch_no like '$search_common%'"; else $batch_cond = "";
	} 
	else if ($search_type == 3) 
	{
		if ($search_common != '') $batch_cond = " and a.batch_no like '%$search_common'"; else $batch_cond = "";
	}

	$po_arr = return_library_array("select id,po_number from wo_po_break_down", 'id', 'po_number');
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$buyer_po_arr=array();
	$po_sql ="Select a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
	}
	unset($po_sql_res);
	
	$order_arr=array(); $colorid_arr=array();
	$order_sql = sql_select("select a.subcon_job, a.within_group, a.party_id, b.id, b.order_no, b.order_quantity, b.buyer_po_id from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.entry_form=295 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	foreach($order_sql as $row)
	{
		$order_arr[$row[csf('id')]]['po']=$row[csf('order_no')];
		$order_arr[$row[csf('id')]]['job']=$row[csf('subcon_job')];
		$order_arr[$row[csf('id')]]['within_group']=$row[csf('within_group')];
		$order_arr[$row[csf('id')]]['party_id']=$row[csf('party_id')];
		$order_arr[$row[csf('id')]]['qty']=$row[csf('order_quantity')];
		$order_arr[$row[csf('id')]]['buyer_po_id']=$row[csf('buyer_po_id')];
	}
	unset($order_sql);
	
	if($db_type==0) $poid_cond="group_concat(b.po_id)";
	else $poid_cond="listagg(b.po_id,',') within group (order by b.po_id)";
	
	$sql = "select a.id, a.batch_no, a.extention_no, a.batch_date, a.batch_weight, a.color_id ,a.operation_type, a.sub_operation, $poid_cond as poid, sum( b.roll_no) as qtypcs from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.entry_form in (316)  and a.process_id='1' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $batch_cond $date_cond
	group by a.id, a.batch_no, a.extention_no, a.batch_date, a.batch_weight, a.color_id,a.operation_type, a.sub_operation order by a.id DESC";
	$nameArray = sql_select($sql);
	//echo $sql;
	?>
    <div>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="618" class="rpt_table">
            <thead>
            <th width="30">SL</th>
            <th width="70">Batch No</th>
            <th width="40">Ex.</th>
            <th width="90">Color</th>
            <th width="80">Batch Weight</th>
            <th width="80">Batch Qty(Pcs)</th>
            <th width="70">Batch Date</th>
            <th>PO No.</th>
            </thead>
        </table>
        <div style="width:618px; overflow-y:scroll; max-height:240px;" align="center">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table" id="list_view">
				<?
				$i = 1;
				foreach ($nameArray as $row) 
				{
					if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
					//$order_no = '';
					$order_id = array_unique(explode(",", $row[csf('poid')]));
 					$order_no =""; $subcon_job=''; $party_id=''; $within_group=''; $qty=0; $buyer_po_id=''; $sub_operation='';
					foreach ($order_id as $idpo)
					{
						if($order_no=="") $order_no=$order_arr[$idpo]['po']; else $order_no.= ",".$order_arr[$idpo]['po'];
						if($subcon_job=="") $subcon_job=$order_arr[$idpo]['job']; else $subcon_job.= ",".$order_arr[$idpo]['job'];
						if($party_id=="") $party_id=$order_arr[$idpo]['party_id']; else $party_id.= ",".$order_arr[$idpo]['party_id'];
						if($within_group=="") $within_group=$order_arr[$idpo]['within_group']; else $within_group.= ",".$order_arr[$idpo]['within_group'];
						if($buyer_po_id=="") $buyer_po_id=$order_arr[$idpo]['buyer_po_id']; else $buyer_po_id.= ",".$order_arr[$idpo]['buyer_po_id'];
						
						$qty+=$order_arr[$idpo]['qty'];
					}	
					
					$order_no=implode(", ",array_unique(explode(",",$order_no)));
					$subcon_job=implode(", ",array_unique(explode(",",$subcon_job)));
					$party_id=implode(", ",array_unique(explode(",",$party_id)));
					$within_group=implode(", ",array_unique(explode(",",$within_group)));	
					$buyer_po_id=implode(", ",array_unique(explode(",",$within_group)));
					
					$exbuyer_po_id=	array_unique(explode(", ", $buyer_po_id));	
					$buyer_po=""; $buyer_style="";
					foreach ($exbuyer_po_id as $idbuyerpo)
					{
						if($buyer_po=="") $buyer_po=$buyer_po_arr[$idbuyerpo]['po']; else $buyer_po.= ",".$buyer_po_arr[$idbuyerpo]['po'];
						if($buyer_style=="") $buyer_style=$buyer_po_arr[$idbuyerpo]['style']; else $buyer_style.= ",".$buyer_po_arr[$idbuyerpo]['style'];
					}
					
					$buyer_po=implode(", ",array_unique(explode(",",$buyer_po)));	
					$buyer_style=implode(", ",array_unique(explode(",",$buyer_style)));
					$suboperation=array_unique(explode(",", $row[csf('sub_operation')]));
					foreach ($suboperation as $sub)
					{
						$sub_operation .=$wash_sub_operation_arr[$sub];
					}
					//$sub_operation = implode(","$wash_sub_operation_arr[array_unique(explode(",", $row[csf('sub_operation')]))]);
					//echo $sub_operation.'=='; 
					$str=$row[csf('id')].'___'.$row[csf('batch_no')].'___'.$subcon_job.'___'.chop($row[csf('poid')],',').'___'.$order_no.'___'.$party_id.'___'.$within_group.'___'.$qty.'___'.$buyer_po_id.'___'.$buyer_po.'___'.$buyer_style.'___'.$row[csf('operation_type')].'___'.chop($sub_operation,',');
					?>
                    <tr bgcolor="<?php echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<?php echo $i; ?>" onClick="js_set_value('<?php echo $str; ?>')">
                        <td width="30"><?php echo $i; ?></td>
                        <td width="70"><p><?php echo $row[csf('batch_no')]; ?></p></td>
                        <td width="40"><?php echo $row[csf('extention_no')]; ?>&nbsp;</td>
                        <td width="90"><p><?php echo $color_arr[$row[csf('color_id')]]; ?></p></td>
                        <td width="80" align="right"><p><?php echo $row[csf('batch_weight')]; ?></p></td>
                        <td width="80" align="right"><p>&nbsp;<?php echo $row[csf('qtypcs')]; ?></p></td>
                        <td width="70" align="center"><p><?php echo change_date_format($row[csf('batch_date')]); ?></p></td>
                        <td><p><?php echo $order_no ; ?>&nbsp;</p></td>
                    </tr>
					<?
					$i++;
				}
				?>
            </table>
        </div>
    </div>
	<?
	exit();
}

if($action=="order_details")
{
	$data=explode("***",$data);
	$company_id=$data[0];
	$batch_id=$data[1];
	$update_id=$data[2];

	$item_group_arr=return_library_array( "select id,item_name from lib_item_group",'id','item_name');
	$color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
	
	$buyer_po_arr=array();
	
	$po_sql ="Select a.style_ref_no, a.buyer_name, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
		$buyer_po_arr[$row[csf("id")]]['buyer']=$row[csf("buyer_name")];
	}
	unset($po_sql_res);
	
	$prod_data_arr=array(); //$recipe_prod_id_arr=array(); $product_data_arr=array();
	if($update_id!=0)
	{	
		//echo "select id, color_size_id, issue_date, production_hour, qcpass_qty, operator_name, shift_id, remarks from trims_raw_mat_requisition_dtls where mst_id=$update_id and status_active=1 and is_deleted=0";
		$prodData=sql_select("select id, po_id, issue_date, production_hour, qcpass_qty, reje_qty,rewash_qty, operator_name, shift_id, remarks from trims_raw_mat_requisition_dtls where mst_id=$update_id and status_active=1 and is_deleted=0");
		foreach($prodData as $row)
		{
			$data_arr[$row[csf('po_id')]]['issue_date']=$row[csf('issue_date')];
			$data_arr[$row[csf('po_id')]]['production_hour']=$row[csf('production_hour')];
			$data_arr[$row[csf('po_id')]]['id']=$row[csf('id')];
			$data_arr[$row[csf('po_id')]]['qcpass_qty']=$row[csf('qcpass_qty')];
			$data_arr[$row[csf('po_id')]]['reje_qty']=$row[csf('reje_qty')];
			$data_arr[$row[csf('po_id')]]['rewash_qty']=$row[csf('rewash_qty')];
			$data_arr[$row[csf('po_id')]]['operator_name']=$row[csf('operator_name')];
			$data_arr[$row[csf('po_id')]]['shift_id']=$row[csf('shift_id')];
			$data_arr[$row[csf('po_id')]]['remarks']=$row[csf('remarks')];
		}
		unset($prodData);
	}
	//echo "<pre>";
	//print_r($data_arr);

	/*$sql= "select  a.id, a.job_no_prefix_num, a.subcon_job, a.party_id, a.within_group, b.id as order_id, b.order_no, b.main_process_id, b.gmts_item_id, b.embl_type, b.body_part, b.buyer_po_id, c.id as color_size_id, c.color_id, c.size_id, sum(c.qnty) as qty, d.id as recipe_id, d.recipe_no_prefix_num, d.recipe_no from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c, pro_recipe_entry_mst d where a.subcon_job=b.job_no_mst and b.id=c.mst_id and a.subcon_job=d.job_no and b.id=d.po_id 
							
	and b.main_process_id=d.embl_name and b.gmts_item_id=d.gmts_item and b.embl_type=d.embl_type and b.body_part=d.body_part and c.color_id=d.color_id
	
	and a.entry_form=295 and d.entry_form=300 and a.status_active=1 and a.company_id='$company_id' and d.id='$recipe_id' group by a.id, a.job_no_prefix_num, a.subcon_job, a.party_id, a.within_group, b.id, b.order_no, b.main_process_id, b.gmts_item_id, b.embl_type, b.body_part, b.buyer_po_id, c.id, c.color_id, c.size_id, d.id, d.recipe_no_prefix_num, d.recipe_no order by c.id ASC";*/

	/*$sql = "select a.id, a.batch_no,a.process_id , a.extention_no, a.batch_weight, a.total_trims_weight, a.batch_date, a.color_id, a.entry_form, b.roll_no, b.po_id, b.batch_qnty as qty ,c.subcon_job, c.party_id, c.within_group, d.id as order_id, d.order_no, d.buyer_po_id, d.main_process_id, d.gmts_item_id, d.embl_type, d.body_part ,e.id as color_size_id from pro_batch_create_mst a, pro_batch_create_dtls b ,subcon_ord_mst c, subcon_ord_dtls d,subcon_ord_breakdown e where a.id=b.mst_id and b.po_id=d.id and c.subcon_job=d.job_no_mst and d.id=e.mst_id and a.id= $batch_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form in (316)";*/
	$sql = "select a.id, a.batch_no, a.process_id, a.extention_no, a.batch_weight, a.total_trims_weight, a.batch_date, a.color_id, a.entry_form, b.roll_no, b.po_id, b.batch_qnty as qty, c.subcon_job, c.party_id, c.within_group, d.id as order_id, d.order_no, d.buyer_po_id, d.main_process_id, d.gmts_item_id, d.gmts_color_id
	from pro_batch_create_mst a, pro_batch_create_dtls b, subcon_ord_mst c, subcon_ord_dtls d where a.id=b.mst_id and b.po_id=d.id and c.subcon_job=d.job_no_mst and c.id=d.mst_id and a.id= $batch_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form in (316)";

	//echo $sql; die;
	$prod_data_arr=sql_select($sql);

	$i=1; 
	foreach($prod_data_arr as $row)
	{
		if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		$process_id = array_unique(explode(",", $row[csf('process_id')]));
		foreach ($process_id as $val)
		{
			if ($process_name == "") $process_name =$wash_type[$val]; else $process_name.= ",".$wash_type[$val];
		}
		$process_name=implode(", ",array_unique(explode(",",$process_name)));
		//echo "<pre>";
		//print_r($prod_data_arr); 
		//echo $prod_data_arr[$row[csf('color_size_id')]]['qcpass_qty']; die;
		$upid=$data_arr[$row[csf('po_id')]]['id'];
		$qcpass_qty=$data_arr[$row[csf('po_id')]]['qcpass_qty'];
		$rej_qty=$data_arr[$row[csf('po_id')]]['reje_qty'];
		$rewash_qty=$data_arr[$row[csf('po_id')]]['rewash_qty'];
		$operator_name=$data_arr[$row[csf('po_id')]]['operator_name'];
		$shift_id=$data_arr[$row[csf('po_id')]]['shift_id'];
		$remarks=$data_arr[$row[csf('po_id')]]['remarks'];
		//echo $data_arr[$row[csf('color_size_id')]]['qcpass_qty']."=="; die;
		?>
		<tr bgcolor="<?php echo $bgcolor; ?>" name="tr[]" id="tr_<?php echo $i;?>">
			<td align="center"><?php echo $i; ?></td>
            <td style="word-break:break-all"><?php echo $buyer_po_arr[$row[csf("buyer_po_id")]]['style']; ?>&nbsp;</td>
            <td style="word-break:break-all"><?php echo $buyer_po_arr[$row[csf("buyer_po_id")]]['po']; ?>&nbsp;</td>
            <td style="word-break:break-all"><?php echo $row[csf("order_no")]; ?>&nbsp;</td>
            <td style="word-break:break-all"><?php echo $garments_item[$row[csf('gmts_item_id')]]; ?>&nbsp;</td>
            <td style="word-break:break-all"><?php echo $color_arr[$row[csf('gmts_color_id')]]; ?>&nbsp;</td>
            <td style="word-break:break-all"><?php echo $process_name; ?>&nbsp;</td>
			<td align="right"><input type="text" name="txtProdQty[]" id="txtProdQty_<?php echo $i;?>" class="text_boxes_numeric" style="width:80px" placeholder="Write" value="<?php echo $qcpass_qty; ?>" onBlur="fnc_total_calculate();" /></td>
            <td><input type="text" name="txtRejQty[]" id="txtRejQty_<?php echo $i;?>" class="text_boxes_numeric" style="width:70px" placeholder="Write" value="<?php echo $rej_qty; ?>" onBlur="fnc_total_calculate();" /></td>
            <td align="right"><input type="text" name="txtReWashQty[]" id="txtReWashQty_<?php echo $i;?>" class="text_boxes_numeric" style="width:80px" placeholder="Write" value="<?php echo $rewash_qty; ?>" onBlur="fnc_total_calculate();" /></td>
            <td>
            	<input type="text" name="txtRemarks[]" id="txtRemarks_<?php echo $i;?>" class="text_boxes" style="width:90px" placeholder="Write" value="<?php echo $remarks; ?>" />
                <input type="hidden" name="updateIdDtls[]" id="updateIdDtls_<?php echo $i;?>" style="width:50px" value="<?php echo $upid; ?>" />
                <input type="hidden" name="txtbuyerPoId[]" id="txtbuyerPoId_<?php echo $i;?>" style="width:50px" value="<?php echo $row[csf('buyer_po_id')]; ?>" />
                <input type="hidden" name="txtPoId[]" id="txtPoId_<?php echo $i;?>" style="width:50px" value="<?php echo $row[csf('po_id')]; ?>" />
				<input type="hidden" name="colorSizeId[]" id="colorSizeId_<?php echo $i;?>" style="width:50px" value="<?php //echo $row[csf('color_size_id')]; ?>" />
            </td>
		</tr>
		<?
		$i++;
	}
	exit();
}

/*/Search Saved data/*/
if($action=="embel_production_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
	//echo $cbo_company_name;
	?>
	  <script>
		function js_set_value( str) 
		{
			$('#hidden_production_data').val( str );
			//alert(str);return;
			parent.emailwindow.hide();
		}
		
		function search_by(val)
		{
			$('#txt_search_string').val('');
			if(val==1 || val==0) $('#search_by_td').html('Job No');
			else if(val==2) $('#search_by_td').html('Requisition No');
		}
	  </script>
    </head>
    <body>
		<div align="center" style="width:100%;" >
             <fieldset>
                <form name="searchjobfrm_1"  id="searchjobfrm_1" autocomplete="off">
                    <table width="670" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                        <thead> 
                        	<tr>
                                <th colspan="7"><?php echo create_drop_down("cbo_string_search_type", 130, $string_search_type, '', 1, "-- Searching Type --"); ?></th>
                            </tr>
                            <tr>              	 
                                <th width="150">Location</th>
                                <!-- <th width="100">Production ID</th> -->
                                <th width="100">Search By</th>
                            	<th width="100" id="search_by_td">Job No</th>
                                <th width="130" colspan="2">Issue Date Range</th>
                                <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:90px;" /></th> 
                            </tr>          
                        </thead>
                        <tbody>
                            <tr class="general">
                                <td><?php echo create_drop_down( "cbo_location_name", 150, "select id, location_name from lib_location where company_id='$cbo_company_name' and status_active =1 and is_deleted=0 order by location_name ASC","id,location_name", 1, "-Select Location-", 0, "","","","","","",3 ); ?></td>
                                <td>
									<?php
                                        $search_by_arr=array(1=>'Job No', 2=>'Requisition No');
                                        echo create_drop_down('cbo_type', 100, $search_by_arr, '', 0, '', 1, 'search_by(this.value)', 0);
                                    ?>
                                </td>
                                <td>
                                    <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:100px" placeholder="" />
                                </td>
                                
                                <td><input name="txt_date_from" id="txt_date_from" class="datepicker" placeholder="From Date" style="width:60px"></td>
                                <td><input name="txt_date_to" id="txt_date_to" class="datepicker" placeholder="To Date" style="width:60px"></td> 
                                <td align="center">
                                    <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( '<?php echo $cbo_company_name; ?>'+'_'+document.getElementById('cbo_location_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_year_selection').value, 'create_production_no_list_view', 'search_div', 'raw_material_issue_requisition_controller', 'setFilterGrid(\'tbl_list_search\',-1)')" style="width:90px;" />
                                    
                                     <input type="hidden" id="hidden_production_data" name="hidden_production_data" value="" />
                                </td>
                            </tr>
                            <tr>
                                <td colspan="7" align="center" valign="middle">
                                    <?php echo load_month_buttons(1);  ?>
                                </td>
                            </tr>
                        </tbody>
                    </table> 
                </form>
             </fieldset>   
             <div id="search_div" ></div>
		</div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}

if($action=="create_production_no_list_view")
{
	$data=explode('_',$data);
	$company_id=$data[0];
	$location_id=$data[1];
	// $prod_no=$data[2];
	// $recipe_no=$data[2];
	$date_from=$data[2];
	$date_to=$data[3];
	$search_type=$data[4];
	$search_year=$data[7];
	$search_by=str_replace("'","",$data[5]);
	$search_str=trim(str_replace("'","",$data[6]));

	if($company_id==0) { echo 'Select Company first'; die; }
	
	if($db_type==0)
	{
		$start_date= change_date_format($date_from,'yyyy-mm-dd');
		$end_date= change_date_format($date_to,'yyyy-mm-dd');
	}
	else
	{
		$start_date= change_date_format($date_from, "", "",1) ;
		$end_date= change_date_format($date_to, "", "",1);
	}

	$date_cond = "";
	if ( $start_date != '' && $end_date != '' )
	{
		if ($db_type == 0) 
		{
			$date_cond ="and a.issue_date between '".change_date_format($start_date, "yyyy-mm-dd", "-")."' and '".change_date_format($end_date, "yyyy-mm-dd", "-")."'";
		} 
		else 
		{
			$date_cond="and a.issue_date between '".change_date_format($start_date, "yyyy-mm-dd", "-", 1)."' and '".change_date_format($end_date, "yyyy-mm-dd", "-", 1)."'";
		}
	} else {
		if ($db_type == 0) 
		{
			$date_cond ="and a.issue_date between '".change_date_format("01-Jan-$search_year", "yyyy-mm-dd", "-")."' and '".change_date_format("31-Dec-$search_year", 'yyyy-mm-dd', '-')."'";
		} 
		else 
		{
			$date_cond="and a.issue_date between '".change_date_format("01-Jan-$search_year", "yyyy-mm-dd", "-", 1)."' and '".change_date_format("31-Dec-$search_year", 'yyyy-mm-dd', '-', 1)."'";
		}
	}

	// echo $date_cond;die;

	$job_cond=""; $style_cond=""; $po_cond=""; $search_com_cond="";

	if($search_type==1)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no='$search_str' ";
			else if($search_by==2) $search_com_cond="and a.requisition_no='$search_str'";
			
			/*if ($search_by==3) $job_cond=" and a.job_no = '$search_str' "; 
			else if ($search_by==4) $po_cond=" and b.po_number = '$search_str' ";
			else if ($search_by==5) $style_cond=" and a.style_ref_no = '$search_str' ";*/
		}
	}
	else if($search_type==4 || $search_type==0)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no like '%$search_str%'";
			else if($search_by==2) $search_com_cond="and a.requisition_no like '%$search_str%'";  
			
			/*if ($search_by==3) $job_cond=" and a.job_no like '%$search_str%'";
			else if ($search_by==4) $po_cond=" and b.po_number like '%$search_str%'"; 
			else if ($search_by==5) $style_cond=" and a.style_ref_no like '%$search_str%'";  */ 
		}
	}
	else if($search_type==2)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no like '$search_str%'";
			else if($search_by==2) $search_com_cond="and a.requisition_no like '$search_str%'";  
			
			/*if ($search_by==3) $job_cond=" and a.job_no like '$search_str%'";
			else if ($search_by==4) $po_cond=" and b.po_number like '$search_str%'";
			else if ($search_by==5) $style_cond=" and a.style_ref_no like '$search_str%'";  */
		}
	}
	else if($search_type==3)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no like '%$search_str' ";
			else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str'";  
			
			/*if ($search_by==3) $job_cond=" and a.job_no like '%$search_str' ";
			else if ($search_by==4) $po_cond=" and b.po_number like '%$search_str'";
			else if ($search_by==5) $style_cond=" and a.style_ref_no like '%$search_str'";  */
		}
	}
	
	$po_ids=''; $buyer_po_arr=array();
	if($db_type==0) $id_cond="group_concat(b.id)";
	else if($db_type==2) $id_cond="listagg(b.id,',') within group (order by b.id)";
	//echo "select $id_cond as id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst $job_cond $style_cond $po_cond";
	if(($job_cond!="" && $search_by==3) || ($po_cond!="" && $search_by==4) || ($style_cond!="" && $search_by==5))
	{
		$po_ids = return_field_value("$id_cond as id", "wo_po_details_master a, wo_po_break_down b", "a.job_no=b.job_no_mst $job_cond $style_cond $po_cond", "id");
	}
	//echo $po_ids;
	if ($po_ids!="") $po_idsCond=" and a.buyer_po_id in ($po_ids)"; else $po_idsCond="";
	
	$spo_ids='';
	if(($search_com_cond!="" && $search_by==1) || ($search_com_cond!="" && $search_by==2))
	{
		$spo_ids = return_field_value("$id_cond as id", "subcon_ord_mst a, subcon_ord_dtls b", "a.subcon_job=b.job_no_mst $search_com_cond", "id");
	}
	if ( $spo_ids!="") $spo_idsCond=" and a.order_id in ($spo_ids)"; else $spo_idsCond="";	
	
	if($location_id !="0") $location_cond= "and b.location_id=$location_id"; else $location_cond= "";
	// if($prod_no!="") $system_no_cond=" and a.sys_no like '%".trim($prod_no)."%'"; else $system_no_cond="";
	// if($recipe_no!="") $recipe_no_cond=" and a.sys_no like '%".trim($recipe_no)."%'"; else $recipe_no_cond="";
	// if($data[4]!="" && $data[5]!="") $date_cond=" and a.issue_date between '".$date_from."' and '".$date_to."'"; else $date_cond="";
	/*$batchData=sql_select("select id, batch_no, operation_type, sub_operation from pro_batch_create_mst where status_active=1 and is_deleted=0");
	foreach($batchData as $row)
	{
		$batch_arr[$row[csf('id')]]['batch_no']=$row[csf('batch_no')];
		$batch_arr[$row[csf('id')]]['sub_operation']=$row[csf('sub_operation')];
		$batch_arr[$row[csf('id')]]['operation_type']=$row[csf('operation_type')];
	}

	foreach($prodData as $row)
	{
		$prod_data_arr[$row[csf('mst_id')]]['issue_date']=change_date_format($row[csf('issue_date')]);
		$prod_data_arr[$row[csf('mst_id')]]['production_hour']=$row[csf('production_hour')];
		$prod_data_arr[$row[csf('mst_id')]]['operator_name']=$row[csf('operator_name')];
		$prod_data_arr[$row[csf('mst_id')]]['shift_id']=$row[csf('shift_id')];
	}

	$pdate_cond=($db_type==2)? " TO_CHAR(production_hour,'HH24:MI') " : " TIME_FORMAT( production_hour, '%H:%i' ) ";
	$prodData=sql_select("select id, mst_id, color_size_id, issue_date, $pdate_cond as production_hour, operator_name, shift_id from trims_raw_mat_requisition_dtls where status_active=1 and is_deleted=0");
	foreach($prodData as $row)
	{
		$prod_data_arr[$row[csf('mst_id')]]['issue_date']=change_date_format($row[csf('issue_date')]);
		$prod_data_arr[$row[csf('mst_id')]]['production_hour']=$row[csf('production_hour')];
		$prod_data_arr[$row[csf('mst_id')]]['operator_name']=$row[csf('operator_name')];
		$prod_data_arr[$row[csf('mst_id')]]['shift_id']=$row[csf('shift_id')];
	}
	
	$buyer_po_arr=array();
	$po_sql ="Select a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
	}
	unset($po_sql_res);

	$order_arr=array();
	$order_sql = sql_select("SELECT a.subcon_job, a.within_group, a.party_id, b.id, b.order_no, b.order_quantity as qty, b.order_uom from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.entry_form=295 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	foreach($order_sql as $row)
	{
		$order_arr[$row[csf('id')]]['order_no']=$row[csf('order_no')];
		$order_arr[$row[csf('id')]]['within_group']=$row[csf('within_group')];
		$order_arr[$row[csf('id')]]['party_id']=$row[csf('party_id')];
		$order_arr[$row[csf('id')]]['qty']+=$row[csf('qty')];
		$order_arr[$row[csf('id')]]['uom']=$row[csf('order_uom')];
	}
	unset($order_sql);*/
	
	//$batch_arr = return_library_array("select id, batch_no from pro_batch_create_mst", 'id', 'batch_no');
	
	?>
	<body>
		<div align="center">
			<fieldset style="width:670px;margin-left:10px">
				<form name="searchprocessfrm_1"  id="searchprocessfrm_1" autocomplete="off">
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="670" class="rpt_table" >
						<thead>
							<th width="30">SL</th>
							<th width="150">Requisition No</th>
                            <th width="90">Issue Date</th>
                            <th width="150">Job No</th>
                            <th>Order No</th>
						</thead>
					</table>
					<div style="width:670px; overflow-y:scroll; max-height:300px;">
						<table cellspacing="0" cellpadding="0" border="1" rules="all" width="650" class="rpt_table" id="tbl_list_search" >
							<?
							/*$sql="select a.id, a.prefix_no_num, a.requisition_no, a.location_id, a.job_id, a.job_no, a.order_id, a.issue_date
							from trims_raw_mat_requisition_mst a, trims_raw_mat_requisition_dtls b
							where a.id=b.mst_id and a.entry_form=427 and a.status_active=1 and a.is_deleted=0 and a.company_id='$company_id' $location_cond $system_no_cond $recipe_no_cond $spo_idscond $po_idscond $date_cond
							group by a.id, a.prefix_no_num, a.requisition_no, a.location_id, a.job_id, a.job_no, a.order_id, a.issue_date
							order by a.id desc";*/

							 $sql = "select a.id, a.job_no, a.requisition_no, a.issue_date, b.order_no
									from trims_raw_mat_requisition_mst a, trims_job_card_mst b
									where a.entry_form=427 $date_cond and a.status_active=1 and b.status_active=1 and a.job_no=b.trims_job and a.company_id='$company_id' $location_cond $search_com_cond
									order by id desc";
							// echo $sql; // die;
							$sql_res=sql_select($sql);

							$i=1;  // $sub_operation=''; $batch_no='';  $operation_type='';
							foreach($sql_res as $row)
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								// $suboperation=array_unique(explode(",", $batch_arr[$row[csf('recipe_id')]]['sub_operation']));
								// foreach ($suboperation as $sub)
								// {
								// 	$sub_operation .=$wash_sub_operation_arr[$sub];
								// }
								//$batch_no=$batch_arr[$row[csf('recipe_id')]]['batch_no'];
								//$operation_type=$batch_arr[$row[csf('recipe_id')]]['operation_type'];
								// $str_data=""; 
								// if($order_arr[$row[csf('po_id')]]['uom']==2) $ord_qty_pcs==$order_arr[$row[csf('po_id')]]['qty']*12; else $ord_qty_pcs==$order_arr[$row[csf('po_id')]]['qty'];
								//$ord_qty_pcs=$order_arr[$row[csf('po_id')]]['qty']*12;
								// $str_data=$row[csf('id')].'***'.$row[csf('sys_no')].'***'.$row[csf('location_id')].'***'.$row[csf('job_no')].'***'.$row[csf('order_id')].'***'.$order_arr[$row[csf('po_id')]]['order_no'].'***'.$order_arr[$row[csf('po_id')]]['within_group'].'***'.$order_arr[$row[csf('po_id')]]['party_id'].'***'.$ord_qty_pcs.'***'.$prod_data_arr[$row[csf('id')]]['issue_date'].'***'.$prod_data_arr[$row[csf('id')]]['production_hour'].'***'.$prod_data_arr[$row[csf('id')]]['operator_name'].'***'.$prod_data_arr[$row[csf('id')]]['shift_id'].'***'.$row[csf('floor_id')].'***'.$row[csf('machine_id')].'***'.$row[csf('job_id')];
								?>
								<tr bgcolor="<?php echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<?php echo $i;?>" onClick="js_set_value('<?php echo $row[csf('id')]; ?>')"> 
									<td width="30" align="center"><?php echo $i; ?></td>	
									<td width="150" align="center"><?php echo $row[csf('requisition_no')]; ?></td>
                                    <td width="90" align="center"><?php echo $row[csf('issue_date')]; ?>&nbsp;</td>
                                    <td width="150" align="center"><?php echo $row[csf('job_no')]; ?></td>
                                    <td align="center"><?php echo $row[csf('order_no')]; ?></td>
								</tr>
								<?
								$i++;
							}
							?>
						</table>
					</div>
				</form>
			</fieldset>
		</div>    
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if ($action=='save_update_delete')
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	if ($operation==0)   // Insert Here==============================================================
	{
        $flag = 1;
        $add_comma = false;
        $field_array_mst = '';
        $data_array_mst = '';
        $field_array_dtls = '';
        $data_array_dtls = '';
        $entryForm = 427;
        $mstId = return_next_id('id', 'trims_raw_mat_requisition_mst', 1);
        $dtlsId = return_next_id('id', 'trims_raw_mat_requisition_dtls', 1);
        $year_cond = '';

        $con = connect();
        if($db_type==0) {
            mysql_query("BEGIN");
        }

        if($db_type==0) $year_cond=" and YEAR(insert_date)";
		else if($db_type==2) $year_cond=" and TO_CHAR(insert_date,'YYYY')";


        $new_return_no=explode('*', return_mrr_number( str_replace("'", "", $cbo_company_name), '', 'RMIR', date("Y",time()), 5, "select id,prefix_no,prefix_no_num from trims_raw_mat_requisition_mst where company_id=$cbo_company_name and entry_form=$entryForm $year_cond=".date('Y',time())." order by id desc", 'prefix_no', 'prefix_no_num' ));

        $field_array_mst = "id, prefix_no, prefix_no_num, requisition_no, company_id, location_id, issue_date, issue_basis, section_id, target_prod_qty, uom_id, store_id, job_no, job_id, order_id, inserted_by, insert_date, status_active, is_deleted, entry_form";
		$data_array_mst="(".$mstId.",'".$new_return_no[1]."','".$new_return_no[2]."','".$new_return_no[0]."',".$cbo_company_name.",".$cbo_location_name.",".$txt_issue_date.",".$cbo_issue_basis.",".$cbo_section.",".$txt_targeted_prod_qty.",".$cbo_uom.",".$cbo_store_name.",".$txt_job_no.",".$hid_job_id.",".$hid_order_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0,$entryForm)";

        $field_array_dtls="id, mst_id, remarks, requisition_qty, break_id, receive_dtls_id, issue_date, item_group_id, section_id, product_id, trim_break_id, store_id, job_no, job_id, uom, inserted_by, insert_date, lot,color_id";

        $tmpRequQty = '';
		//echo "10** ".$total_row; oci_rollback($con);disconnect($con);die;
        for($i=1; $i<=$total_row; $i++) {
            /*$jobDtlsId = 'hdnDtlsId_'.$i;
            $quantity = 'txtDeliveryQty_'.$i;
            $salesOrderId = 'hdnSalesOrderId_'.$i;
            $salesOrderNo = 'hdnSalesOrderNo_'.$i;
            $productId = 'hdnProductId_'.$i;*/

            $itemGroup="hdnItemGroupId_".$i;
            $breakId="hdnBreakId_".$i;
			$rcvDtlsId="hdnRcvDtlsId_".$i;
			$txtRemarks="txtRemarks_".$i;
			$txtRequQty="txtRequQty_".$i;
			$txtLot="txtLot_".$i;
			$txtcolor="txtcolor_".$i;
			$productId="productId_".$i;
			$sectionId="sectionId_".$i;
			$trimsBreakId="trimsBreakId_".$i;
			$cboUom="cboUom_".$i;

			$tmpRequQty .= $$txtRequQty .',';

            $data_array_dtls .= $add_comma ? ',' : ''; // if $add_comma is true, add a comma in the end of $data_array_dtls

            $data_array_dtls.="(".$dtlsId.",".$mstId.",'".$$txtRemarks."',".$$txtRequQty.",'".$$breakId."','".$$rcvDtlsId."',".$txt_issue_date.",'".$$itemGroup."','".$$sectionId."','".$$productId."','".$$trimsBreakId."',".$cbo_store_name.",".$txt_job_no.",".$hid_job_id.",".$$cboUom.",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."','".$$txtLot."','".$$txtcolor."')"; 

            $add_comma = true; // first entry is done. add a comma for next entries
            $dtlsIds .= $dtlsId . ',';
            $dtlsId++; // increment details id by 1
        }

        // echo "10**insert into trims_raw_mat_requisition_mst(".$field_array_mst.") values ".$data_array_mst; die;
        $rID = sql_insert('trims_raw_mat_requisition_mst', $field_array_mst, $data_array_mst, 0);        
      	//echo "10**insert into trims_raw_mat_requisition_dtls(".$field_array_dtls.") values ".$data_array_dtls; oci_rollback($con);disconnect($con);die;
        $rID2 = sql_insert('trims_raw_mat_requisition_dtls', $field_array_dtls, $data_array_dtls, 0);
        //echo '10**'.$rID."=".$rID2;oci_rollback($con);disconnect($con);die;

        if($db_type==0) {
            if($rID && $rID2) {
                mysql_query("COMMIT");              
                echo '0**'.$mstId.'**'.$new_return_no[0].'**'.rtrim($dtlsIds, ',');
            } else {
                mysql_query("ROLLBACK");
                echo '10**'.$id;
            }
        }
        else if($db_type==2) {
            if($rID && $rID2) {
                oci_commit($con);
                echo '0**'.$mstId.'**'.$new_return_no[0].'**'.rtrim($dtlsIds, ',');
            } else {
                oci_rollback($con);
                echo '10**'.$id;
            }
        }
		disconnect($con);die;
	}
	else if ($operation==1)   // Update Here============================================================
	{
		$dtlsIds = '';

		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$field_array="issue_date*store_id*updated_by*update_date";
        $data_array="$txt_issue_date*$cbo_store_name*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
            
        $flag=1;
        $rID=sql_update('trims_raw_mat_requisition_mst', $field_array, $data_array, 'id', $update_id, 0);
		
		$data_array_dtls_update="";
		//$field_array_dtls="id, mst_id, po_id, buyer_po_id, job_no, issue_date, production_hour, qcpass_qty, reje_qty, order_qty, operator_name, shift_id, process_id, wash_type_id, remarks, inserted_by, insert_date, status_active, is_deleted";
		$field_array_dtls_update="requisition_qty*remarks*store_id*updated_by*update_date";
		
		$requisition_no=str_replace("'", "", $txt_production_id);
		$company_id=str_replace("'", "", $cbo_company_name);
		  
	    $iss_sql= "select b.prod_id,a.req_id,  b.cons_quantity as cons_quantity,a.id  from inv_issue_master a, inv_transaction b 
	where a.id=b.mst_id  and b.company_id=$company_id  and b.transaction_type=2 and b.item_category=101 and a.issue_basis=7 and a.entry_form=265 and a.req_id=$update_id";//and a.req_id=$update_id  

	$iss_data_array=sql_select($iss_sql);
	foreach($iss_data_array as $row)
	{
		
		$issue_qty_arr[$row[csf("prod_id")]]+=$row[csf("cons_quantity")];
		$prod_id_arr[$row[csf("prod_id")]]=$row[csf("prod_id")];
		$issue_id_arr[$row[csf("id")]]=$row[csf("id")];
	}
	
	
	 // echo "10**";
	 // echo "<pre>";
	  
	   
		 
		 
	  
	  //$prod_idss = implode(', ', array_unique(array_values($prod_id_arr)));
	//echo $prod_idss;
   //print_r($issue_id_arr);
	   $prod_idss = implode(', ', $prod_id_arr);
	   $issue_idss = implode(', ', $issue_id_arr); 
 	 $iss_return_sql= "select b.prod_id,b.cons_quantity as cons_quantity  from inv_receive_master a, inv_transaction b 
	where a.id=b.mst_id and b.company_id=$company_id and b.transaction_type=4 and b.prod_id in ($prod_idss) and b.issue_id in ($issue_idss) and a.entry_form=266";  

	$iss_return_data_array=sql_select($iss_return_sql);
	foreach($iss_return_data_array as $row)
	{
		
		$issue_return_qty_arr[$row[csf("prod_id")]]+=$row[csf("cons_quantity")];
	}
	//print_r($issue_return_qty_arr);
		
		$data_array_dtls=""; 
		/*$id_dtls=return_next_id( "id", "trims_raw_mat_requisition_dtls", 1 ) ;
		$field_array_dtls="id, mst_id, color_size_id, po_id, buyer_po_id, job_no, issue_date, production_hour, qcpass_qty, reje_qty, rewash_qty, operator_name, shift_id, remarks, inserted_by, insert_date, status_active, is_deleted";*/
		$issave=1; //echo "10**";
		for($i=1;$i<=$total_row;$i++)
		{
			$txtRemarks="txtRemarks_".$i;
			$txtRequQty="txtRequQty_".$i;
			$updateIdDtls="updateIdDtls_".$i;
			$productId="productId_".$i; 
			$issue_qty=$issue_qty_arr[str_replace("'","",$$productId)];
			$issue_return_qty=$issue_return_qty_arr[str_replace("'","",$$productId)];
			
			$balance_qty=number_format($issue_qty, 4, '.', '')-number_format($issue_return_qty,4, '.', '');
			
			//echo $issue_qty."==".$issue_return_qty."==".$balance_qty; die;
			
			$validRequQty="txtRequQty_".$i;
			$totalvalidRequQty=$$validRequQty;  
			if(number_format($totalvalidRequQty,4, '.', '')<number_format($balance_qty,4, '.', ''))
			{
				echo "20**Requisition Quantity Not Allow Less Then Issue Quantity.";disconnect($con);die;
			} 
			// $txtRequQty="txtRequQty_".$i;
			// $txtRemarks="txtRemarks_".$i; 
			$updateIds = str_replace("'","",$$updateIdDtls);
		  //echo "10**".$balance_qty."***";  
			$dtlsIds .= $updateIds . ',';
			
			/*if($db_type==2)
			{
				$txt_hour="";
				$txt_hour=str_replace("'","",$txt_prod_date)." ".str_replace("'","",$txt_reporting_hour);
				$txtreportingHour="to_date('".$txt_hour."','DD MONTH YYYY HH24:MI:SS')";
			}
			else
			{
				$txtreportingHour=$txt_reporting_hour;
			}*/
			$data_array_dtls_update=array();
			if( $updateIds != "")
			{
				$updateIdDtls_array[]=$updateIds;
				//$field_array_dtls_update="po_id*buyer_po_id*job_no*issue_date*production_hour*qcpass_qty*reje_qty*shift_id*process_id*wash_type_id*remarks*updated_by*update_date";
				//if($data_array_dtls_update != "") $data_array_dtls_update .= ","; 	
				$data_array_dtls_update[$updateIds] = explode("*",("".$$txtRequQty."*'".$$txtRemarks."'*".$cbo_store_name."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				$issave=1;
				$id_arr_pro[]=$updateIds;
			}
		}
		// echo "10**".bulk_update_sql_statement("trims_raw_mat_requisition_dtls", "id", $field_array_dtls_update, $data_array_dtls_update, $updateIdDtls_array); die;
		//echo "10**insert into trims_raw_mat_requisition_dtls ($field_array_dtls) values $data_array_dtls "; die;
		//echo "10**".$data_array_dtls_update;die;
		$flag=1;
		if($data_array_dtls_update !="")
		{
			// echo "10**".bulk_update_sql_statement("trims_raw_mat_requisition_dtls", "id", $field_array_dtls_update, $data_array_dtls_update, $updateIdDtls_array); die; 
			$rID2=execute_query(bulk_update_sql_statement('trims_raw_mat_requisition_dtls', 'id', $field_array_dtls_update, $data_array_dtls_update, $updateIdDtls_array ),1);
			
		}
		if($rID==1 && $rID2==1) $flag=1; else $flag=0;
		//echo "10**".$rID."**".$rID2; die;
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'","",$update_id)."**".$requisition_no."**".rtrim($dtlsIds, ',');
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'","",$update_id)."**".$requisition_no."**".rtrim($dtlsIds, ',');
			}
		}
		else if($db_type==2)
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "1**".str_replace("'","",$update_id)."**".$requisition_no."**".rtrim($dtlsIds, ',');
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$update_id)."**".$requisition_no."**".rtrim($dtlsIds, ',');
			}
		}
	}
	else if ($operation==2)   // Delete Here ============================================================
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		// echo "10**".str_replace("'",'',$update_id); die;
		$qc_no=return_field_value( "sys_no", "trims_raw_mat_requisition_mst"," recipe_id=$update_id and status_active=1 and is_deleted=0 and entry_form=302");
		if($qc_no){
			echo "emblQc**".str_replace("'","",$txt_production_id)."**".$qc_no;
			disconnect($con);
			die;
		}
		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		
		$flag=1;
		$rID=sql_delete("trims_raw_mat_requisition_mst",$field_array,$data_array,"id","".$update_id."",1);
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		$rID1=sql_delete("trims_raw_mat_requisition_dtls",$field_array,$data_array,"mst_id","".$update_id."",1);
		if($rID1==1 && $flag==1) $flag=1; else $flag=0;
		//echo "10**".$rID."**".$rID1; die;	
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'",'',$update_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$update_id);
			}
		}
		else if($db_type==2)
		{
			if ($flag==1) {
                oci_commit($con);
                echo "2**" .str_replace("'",'',$update_id);
            } else {
                oci_rollback($con);
                echo "10**" .str_replace("'",'',$update_id);
            }
		}
	}

	disconnect($con);
 	die;
}

function sql_updates($strTable,$arrUpdateFields,$arrUpdateValues,$arrRefFields,$arrRefValues,$commit)
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

	global $con;
	if( strpos($strQuery, "WHERE")==false)  return "0";
	echo "10**".$strQuery; die;
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


if($action=="raw_mat_issue_requisition_print")
{
	extract($_REQUEST);
	$data = explode('*', $data);
	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$size_arr=return_library_array( "select id,size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name" );
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$batch_arr = return_library_array("select id, batch_no from pro_batch_create_mst", 'id', 'batch_no');
	$location_arr = return_library_array("select id, location_name from lib_location", 'id', 'location_name');
	$order_arr=array();
	$order_sql = sql_select("select a.subcon_job, a.within_group, a.party_id, b.id, b.order_no, b.order_id, b.main_process_id, sum (c.qnty) as qty,d.id as job_id
    	from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c , trims_job_card_mst d
   		where a.subcon_job = b.job_no_mst and b.id = c.mst_id and a.entry_form = 255 and a.id=d.received_id and b.job_no_mst=d.received_no and c.job_no_mst=d.received_no and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0
		group by a.subcon_job, a.within_group, a.party_id, b.main_process_id, b.id, b.order_no, b.order_id,d.id");
	foreach($order_sql as $row)
	{
		
		$order_arr[$row[csf('job_id')]]['po']=$row[csf('order_no')];
		$order_arr[$row[csf('job_id')]]['within_group']=$row[csf('within_group')];
		$order_arr[$row[csf('job_id')]]['party_id']=$row[csf('party_id')];
		$order_arr[$row[csf('job_id')]]['qty']=$row[csf('qty')];
		$order_arr[$row[csf('job_id')]]['embl_name']=$row[csf('main_process_id')];

	}
	unset($order_sql);
	
	$buyer_po_arr=array();
	$po_sql ="select a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
	}
	unset($po_sql_res);
	
	
	$sql="select id, prefix_no_num, sys_no, location_id, recipe_id, job_no, order_id from trims_raw_mat_requisition_mst where entry_form=427 and status_active = 1 and is_deleted = 0 and company_id='$company_id' $location_cond $system_no_cond $recipe_no_cond order by id desc";

	$batch_arr = return_library_array("select id, batch_no from pro_batch_create_mst", 'id', 'batch_no');
	$pdate_cond=($db_type==2)? " TO_CHAR(production_hour,'HH24:MI') " : " TIME_FORMAT( production_hour, '%H:%i' ) ";
	$prod_data_arr=array(); $qty_data_arr=array();
	$prodData=sql_select("select id, mst_id, color_size_id, issue_date, $pdate_cond as production_hour, qcpass_qty, operator_name, shift_id, remarks from trims_raw_mat_requisition_dtls where status_active=1 and is_deleted=0 and mst_id='$data[1]'");
	foreach($prodData as $row)
	{
		$prod_data_arr[$row[csf('mst_id')]]['issue_date']=change_date_format($row[csf('issue_date')]);
		$prod_data_arr[$row[csf('mst_id')]]['production_hour']=$row[csf('production_hour')];
		$prod_data_arr[$row[csf('mst_id')]]['operator_name']=$row[csf('operator_name')];
		$prod_data_arr[$row[csf('mst_id')]]['shift_id']=$row[csf('shift_id')];
		
		$qty_data_arr[$row[csf('color_size_id')]]['id']=$row[csf('id')];
		$qty_data_arr[$row[csf('color_size_id')]]['qcpass_qty']=$row[csf('qcpass_qty')];
		$qty_data_arr[$row[csf('color_size_id')]]['remarks']=$row[csf('remarks')];
	}
	unset($prodData);
	
	$sql_mst = "select id, prefix_no_num, requisition_no, location_id, job_no, order_id, target_prod_qty, issue_date,job_id,section_id,inserted_by from trims_raw_mat_requisition_mst where entry_form=427 and company_id='$data[0]' and id='$data[1]'";

	$dataArray = sql_select($sql_mst); 
	$section_id=$dataArray[0][csf("section_id")];
	$inserted_by=$dataArray[0][csf("inserted_by")];


	$party_name="";

	if(  $order_arr[$dataArray[0][csf('job_id')]]['within_group'] ==1) $party_name=$company_library[$order_arr[$dataArray[0][csf('job_id')]]['party_id']];
	else if($order_arr[$dataArray[0][csf('job_id')]]['within_group']==2) $party_name=$buyer_library[$order_arr[$dataArray[0][csf('job_id')]]['party_id']];
	//$brand_arr=return_library_array("select id, brand_name from product_details_master", 'id', 'brand_name');


	$item_sql = "select id, item_group_code, item_name from lib_item_group where is_deleted=0 and status_active=1 and item_category in(101,22)";

	$item_result = sql_select($item_sql);

	foreach ($item_result as $row) {
		$item_arr[$row[csf('id')]]['id'] = $row[csf('id')];
		$item_arr[$row[csf('id')]]['item_group_code'] = $row[csf('item_group_code')];
		$item_arr[$row[csf('id')]]['item_name'] = $row[csf('item_name')];
	}
	
	?>
    <div style="width:930px; font-size:6px">
        <table width="100%" cellpadding="0" cellspacing="0" >
            <tr>
                <td width="70" align="right"> 
                    <img  src='../../<?php echo $imge_arr[str_replace("'","",$data[0])]; ?>' height='100%' width='100%' />
                </td>
                <td>
                    <table width="800" cellspacing="0" align="center">
                        <tr>
                            <td align="center" style="font-size:20px"><strong ><?php echo $company_library[$data[0]]; ?></strong></td>
                        </tr>
                        <tr>
                            <td align="center"  style="font-size:16px"><strong>Unit : <?php echo $location_arr[$dataArray[0][csf('location_id')]]; ?></strong></td>
                        </tr>
                        <tr class="form_caption">
                            <td  align="center" style="font-size:14px">  
                                <?php echo show_company($data[0],'',''); ?> 
                            </td>  
                        </tr>
                        <tr>
                            <td align="center" style="font-size:18px"><strong><?php echo $data[2]; ?></strong></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <table width="100%" cellpadding="0" cellspacing="0" >  
            <tr>
                <td width="130"><strong>Requisition ID:</strong></td>
                <td width="175"><?php echo $dataArray[0][csf('requisition_no')]; ?></td>
                <td width="130"><strong>Party Name: </strong></td>
                <td width="175px"><?php echo $party_name; ?></td>
                <td width="130"><strong>Issue Date:</strong></td>
                <td width="175"><?php echo change_date_format($dataArray[0][csf('issue_date')]); ?></td>
            </tr>
            <tr>
                <td><strong>Job No:</strong></td>
                <td><?php echo $dataArray[0][csf('job_no')]; ?></td>
                <td><strong>Order No:</strong></td>
                <td><?php echo $order_arr[$dataArray[0][csf('job_id')]]['po']; ?></td>

                <td><strong>Target Prod. Qty:</strong></td>
                <td><?php echo $dataArray[0][csf('target_prod_qty')]; ?></td>
            </tr>
        </table>
        <br>
        <div style="width:100%;">
            <table align="right" cellspacing="0" width="930" border="1" rules="all" class="rpt_table">
                <thead bgcolor="#dddddd" align="center"><!-- style="font-size:12px"-->
                    <th width="30">SL</th>
                    <!-- <th width="140">Gmts Item</th>
                    <th width="120">Body Part</th>
                    <th width="110">Process/ Type</th>
                    <th width="120">Color</th>
                    <th width="70">Size</th>
                    <th width="80">Production Qty (Pcs)</th>
                    <th>Remarks</th> -->
                    <th>Item Group</th>
	                <th>Material Description</th>
	                <th>Brand</th>
	                <th>LOT</th>
	                <th>UOM</th>
	                <th>Req. Qty.</th>
	                <th>Requ. Qty.</th>
	                <th>Remarks</th>
                </thead>
				<?
				
				$mstId = $data[1];
				$com_id = $data[0];
				$job_no = $dataArray[0][csf('job_no')];
				$recipe_id = $dataArray[0][csf('recipe_id')];				
                // $sql = "select b.id as update_id, b.mst_id, b.item_group_id, b.break_id, c.item_description, b.uom, b.requisition_qty, d.product_id, b.receive_dtls_id, c.job_quantity, b.remarks, d.specification, d.req_qty,c.job_quantity ,b.trim_break_id,b.section_id,b.product_id,a.job_no as job_no_mst
				// from trims_raw_mat_requisition_mst a, trims_raw_mat_requisition_dtls b, trims_job_card_dtls c, trims_job_card_breakdown d
				// where a.id=$mstId and a.id=b.mst_id and a.job_no=d.job_no_mst and c.id=d.mst_id and b.job_no=c.job_no_mst and b.job_no=d.job_no_mst  and b.product_id = d.product_id and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 
				// order by b.id";
				

				$sql = "SELECT b.id as update_id, b.mst_id, b.item_group_id, b.break_id, c.item_description, b.uom, b.requisition_qty, d.product_id, b.receive_dtls_id, c.job_quantity, b.remarks, d.specification, d.req_qty,c.job_quantity ,b.trim_break_id,b.section_id,b.product_id,a.job_no as job_no_mst, b.lot
				from trims_raw_mat_requisition_mst a, trims_raw_mat_requisition_dtls b, trims_job_card_dtls c, trims_job_card_breakdown d
				where  a.id=$mstId and a.id=b.mst_id and a.job_no=d.job_no_mst and c.id=d.mst_id and b.job_no=c.job_no_mst and b.job_no=d.job_no_mst  and b.product_id = d.product_id and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 
				";
				/*if($section_id==25){
					$sql.= " union all select b.id as update_id, b.mst_id, b.item_group_id, b.break_id, c.item_description, b.uom, b.requisition_qty, d.product_id, b.receive_dtls_id, c.job_quantity, b.remarks, d.specification, d.req_qty,c.job_quantity ,b.trim_break_id,b.section_id,b.product_id,a.job_no as job_no_mst, f.batch_lot
					from trims_raw_mat_requisition_mst a, trims_raw_mat_requisition_dtls b, trims_job_card_dtls c, trims_job_card_breakdown d, product_details_master e,inv_transaction f
					where  d.product_id=e.id and  e.id=f.prod_id  and a.id=$mstId and a.id=b.mst_id and a.job_no=d.job_no_mst and c.id=d.mst_id and b.job_no=c.job_no_mst and b.job_no=d.job_no_mst  and b.product_id = d.product_id and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and e.status_active=1 and f.status_active=1 and e.item_category_id in(22) and f.item_category in (22)  
					group by  b.id, b.mst_id, b.item_group_id, b.break_id, c.item_description, b.uom, b.requisition_qty, d.product_id, b.receive_dtls_id, c.job_quantity, b.remarks, d.specification, d.req_qty,c.job_quantity ,b.trim_break_id,b.section_id,b.product_id,a.job_no , f.batch_lot ";
				}
				$sql.=" order by update_id";*/

				/*$sql= "select b.id as update_id, b.mst_id, b.item_group_id, b.break_id, c.item_description, c.uom, b.requisition_qty, d.product_id, b.receive_dtls_id, c.job_quantity, b.remarks, d.specification, d.req_qty*c.job_quantity as total_req_qty
				from trims_raw_mat_requisition_mst a, trims_raw_mat_requisition_dtls b, trims_job_card_dtls c, trims_job_card_breakdown d
				where a.id=$mstId and a.id=b.mst_id and a.job_no=d.job_no_mst and b.job_no=c.job_no_mst and b.job_no=d.job_no_mst and c.id=d.mst_id and b.product_id=d.product_id and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1
				order by b.id"; //and b.break_id=c.break_id


				echo $sql;  die;
				$sql_res=sql_select($sql);*/
				

				$data_array=sql_select($sql); $dtls_arr=array(); $productArr = array(); $jobcarddtlsidArr = array();
				foreach ($data_array as $row) 
				{
					
					 
						$dtls_arr[$row[csf('item_group_id')]][$row[csf('product_id')]]['item_group_id'] 	=$row[csf('item_group_id')];
						$dtls_arr[$row[csf('item_group_id')]][$row[csf('product_id')]]["trim_break_id"] 	.=$row[csf("trim_break_id")].",";
						$dtls_arr[$row[csf('item_group_id')]][$row[csf('product_id')]]["section_id"] 		=$row[csf("section_id")];
						$dtls_arr[$row[csf('item_group_id')]][$row[csf('product_id')]]["receive_dtls_id"]	=$row[csf("receive_dtls_id")];
						$dtls_arr[$row[csf('item_group_id')]][$row[csf('product_id')]]["book_con_dtls_id"]	.=$row[csf("book_con_dtls_id")].",";
						$dtls_arr[$row[csf('item_group_id')]][$row[csf('product_id')]]["booking_dtls_id"]	.=$row[csf("booking_dtls_id")].",";
						$dtls_arr[$row[csf('item_group_id')]][$row[csf('product_id')]]["break_id"]			.=$row[csf("break_id")].",";
						$dtls_arr[$row[csf('item_group_id')]][$row[csf('product_id')]]["buyer_po_no"]		=$row[csf("buyer_po_no")];
						$dtls_arr[$row[csf('item_group_id')]][$row[csf('product_id')]]["buyer_style_ref"]	=$row[csf("buyer_style_ref")];
						$dtls_arr[$row[csf('item_group_id')]][$row[csf('product_id')]]["item_description"]	=$row[csf("item_description")];
						$dtls_arr[$row[csf('item_group_id')]][$row[csf('product_id')]]["color_id"]			=$row[csf("color_id")];
						$dtls_arr[$row[csf('item_group_id')]][$row[csf('product_id')]]["size_id"]			=$row[csf("size_id")];
						$dtls_arr[$row[csf('item_group_id')]][$row[csf('product_id')]]["sub_section"]		=$row[csf("sub_section")];
						$dtls_arr[$row[csf('item_group_id')]][$row[csf('product_id')]]["uom"]				=$row[csf("uom")];
						$dtls_arr[$row[csf('item_group_id')]][$row[csf('product_id')]]["lot"]				=$row[csf("lot")];
						$dtls_arr[$row[csf('item_group_id')]][$row[csf('product_id')]]["conv_factor"]		=$row[csf("conv_factor")];
						$dtls_arr[$row[csf('item_group_id')]][$row[csf('product_id')]]["total_req_qty"]		+=$row[csf("req_qty")]*$row[csf("job_quantity")];
						$dtls_arr[$row[csf('item_group_id')]][$row[csf('product_id')]]["product_id"]		=$row[csf("product_id")];
						$dtls_arr[$row[csf('item_group_id')]][$row[csf('product_id')]]["description"]		=$row[csf("description")];
						$dtls_arr[$row[csf('item_group_id')]][$row[csf('product_id')]]["specification"]		=$row[csf("specification")];
						$dtls_arr[$row[csf('item_group_id')]][$row[csf('product_id')]]["brand_name"]		=$row[csf("brand_name")];
						$dtls_arr[$row[csf('item_group_id')]][$row[csf('product_id')]]["job_no_mst"]		=$row[csf("job_no_mst")];
						$dtls_arr[$row[csf('item_group_id')]][$row[csf('product_id')]]["requisition_qty"]	+=$row[csf("requisition_qty")];
						$dtls_arr[$row[csf('item_group_id')]][$row[csf('product_id')]]["job_card_dtls_id"]	.=$row[csf('job_card_dtls_id')].',';
						$dtls_arr[$row[csf('item_group_id')]][$row[csf('product_id')]]['req_details_id'] 	=$row[csf('update_id')];
						$dtls_arr[$row[csf('item_group_id')]][$row[csf('product_id')]]['remarks'] 	=$row[csf('remarks')];
					 

					$trimsJobNo = $row[csf('job_no_mst')];
					$productArr[] = $row[csf("product_id")];
					$jobcarddtlsidArr[] = $row[csf("job_card_dtls_id")];
				}
				

				$requisitiondataSql = sql_select("select b.id,b.item_group_id,b.product_id, sum(b.requisition_qty) as total_requisition_qty   from trims_raw_mat_requisition_mst a, trims_raw_mat_requisition_dtls b
			        where a.id=b.mst_id and a.job_no='$trimsJobNo' and a.status_active=1 and b.status_active=1  group by b.id,b.item_group_id,b.product_id");
				$requisition_data_arr = array(); $updaterequisitionQtyArr = array();
				foreach ($requisitiondataSql as $row) 
				{
					$requisition_data_arr[$row[csf('item_group_id')]][$row[csf('product_id')]]['requisition_qty'] += $row[csf('total_requisition_qty')];
					$updaterequisitionQtyArr[$row[csf('id')]]['requisition_qty'] += $row[csf('total_requisition_qty')];
			 	}
					
				
				$productArr = array_unique($productArr);
				$productIds = implode(',', $productArr);
				$products_data_arr = sql_select("select id, brand_name from product_details_master where id in($productIds) and status_active=1");
				$productsArr = array();
				foreach ($products_data_arr as $row) 
				{
					$productsArr[$row[csf('id')]]['id'] = $row[csf('id')];
					$productsArr[$row[csf('id')]]['brand_name'] = $row[csf('brand_name')];
				}

				//echo '<pre>';
				//print_r($jobQtyArr);
				// echo '</pre>';


 			$i=1; $reqTotal=0; $requisitionTotal=0;


 			foreach($dtls_arr as $item_group_id=>$item_group_data) 
			{
				$jobQnty=0;
				foreach($item_group_data as $row) 
				{		
					//$tblRow++;
			        /*$reqQty = $row['total_req_qty'];

			        $reqQty = $row['total_req_qty'];
					$requisitionQty = $row['requisition_qty'];
					$reqTotal += $reqQty;
					$requisitionTotal += $requisitionQty;*/

					//$reqQty = $row['total_req_qty'];

			        $reqQty = $row['total_req_qty'];
					//$requisitionQty = $row['requisition_qty'];
					$reqTotal += $reqQty;
					
			 		


			 		$prevRequisitionQty = $requisition_data_arr[$row['item_group_id']][$row['product_id']]['requisition_qty'];
			        $balance = ($reqQty - $prevRequisitionQty);
					
			        $requisitionQty = $updaterequisitionQtyArr[$row['req_details_id']]['requisition_qty'];//$row['requisition_qty'];

			        $requisitionTotal += $requisitionQty;
			 		
			       
				/*foreach ($sql_res as $row) 
				{
					$reqQty = $row[csf('total_req_qty')];
					$requisitionQty = $row[csf('requisition_qty')];
					$reqTotal += $reqQty;
					$requisitionTotal += $requisitionQty;*/

					/*if($row[csf('main_process_id')]==1) $new_subprocess_array= $emblishment_print_type;
					else if($row[csf('main_process_id')]==2) $new_subprocess_array= $emblishment_embroy_type;
					else if($row[csf('main_process_id')]==3) $new_subprocess_array= $emblishment_wash_type;
					else if($row[csf('main_process_id')]==4) $new_subprocess_array= $emblishment_spwork_type;
					else if($row[csf('main_process_id')]==5) $new_subprocess_array= $emblishment_gmts_type;
					else $new_subprocess_array=$blank_array;*/

					?>
                    <tr bgcolor="<?php echo $bgcolor; ?>">
                        <td><?php echo $i; ?></td>
                        <td>
		                	<?php echo $item_arr[$row['item_group_id']]['item_name']; ?>
		                </td>
		                <td>
		                	<?php echo $row['specification']; ?>
		                </td>
		                <td style="width: 10%" align="right">
		                	<?php echo $brand_arr[$row['product_id']]; ?>
		                </td>
		                <td style="width: 10%">
		                	<?php echo  $row['lot']; ?>
		                </td>
						<td style="width: 10%">
		                	<?php echo $unit_of_measurement[$row['uom']]; ?>
		                </td>
		                <td style="width: 10%" align="right">
		                	<?php echo number_format($reqQty, 3); ?>
		                </td>
		                <td style="width: 10%" align="right">
		                	<?php echo number_format($requisitionQty, 3); ?>
		                </td>
		                <td style="width: 10%">
		                    <?php echo $row['remarks']; ?>
		                </td>

                        <!-- <td><?php //echo $garments_item[$row[csf('gmts_item_id')]]; ?></td>
                        <td><?php //echo $body_part[$row[csf('body_part')]]; ?>&nbsp;</td>
                        <td><?php //echo $new_subprocess_array[$row[csf('embl_type')]]; ?>&nbsp;</td>
                        <td><?php //echo $color_arr[$row[csf('color_id')]]; ?>&nbsp;</td>
                        <td align="center"><?php //echo $size_arr[$row[csf('size_id')]]; ?>&nbsp;</td>
                        <td align="right"><?php //echo number_format($qty_data_arr[$row[csf('color_size_id')]]['qcpass_qty'], 2, '.', ''); ?>&nbsp;</td>
                        <td><?php //echo $qty_data_arr[$row[csf('color_size_id')]]['remarks']; ?>&nbsp;</td> -->
                    </tr>
					<?
					$i++;
				}
			}
				?>
                <tr class="tbl_bottom">
                    <td align="right" colspan="6"><strong>Grand Total</strong></td>
                    <td align="right"><?php echo number_format($reqTotal, 3, '.', ''); ?>&nbsp;</td>
                    <td align="right"><?php echo number_format($requisitionTotal, 3, '.', ''); ?>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
            </table>
            
            <br>
			<?php echo signature_table(239, $com_id, "930px","","",$inserted_by); ?>
        </div>
    </div>
	<?
	exit();
}


if($action=="raw_mat_issue_requisition_print2")
{
	extract($_REQUEST);
	$data = explode('*', $data);
	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$location_arr = return_library_array("select id, location_name from lib_location", 'id', 'location_name');
	$order_arr=array();
    $sql_mst = "select id, prefix_no_num, requisition_no, location_id, job_no, order_id, target_prod_qty, issue_date,job_id,section_id,inserted_by from trims_raw_mat_requisition_mst where entry_form=427 and company_id='$data[0]' and id='$data[1]'";
    $dataArray = sql_select($sql_mst);
	$order_sql = sql_select("select a.subcon_job, a.within_group, a.party_id, b.id, b.order_no, b.order_id, b.main_process_id, sum (c.qnty) as qty,d.id as job_id
    	from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c , trims_job_card_mst d
   		where a.subcon_job = b.job_no_mst and b.id = c.mst_id and a.entry_form = 255 and a.id=d.received_id and b.job_no_mst=d.received_no and c.job_no_mst=d.received_no and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and d.id=".$dataArray[0][csf('job_id')]."
		group by a.subcon_job, a.within_group, a.party_id, b.main_process_id, b.id, b.order_no, b.order_id,d.id");
    $sql_select_trims_requ_dtls = sql_select("select mst_id, buyer_po_no, buyer_style_ref from trims_job_card_dtls where mst_id=".$dataArray[0][csf('job_id')]);
	$buyer_po = array(); $buyer_style = array();
    foreach ($sql_select_trims_requ_dtls as $key => $detailsdata){
        $splitPo = explode(',', trim($detailsdata[csf('buyer_po_no')]));
        $splitPoStyle = explode(',', trim($detailsdata[csf('buyer_style_ref')]));
        foreach ($splitPo as $po){
            array_push($buyer_po, trim($po));
        }
        foreach ($splitPoStyle as $style){
            array_push($buyer_style, trim($style));
        }
	}
//    print_r($order_arr);
    foreach($order_sql as $row)
	{
		$order_arr[$row[csf('job_id')]]['po']=$row[csf('order_no')];
		$order_arr[$row[csf('job_id')]]['within_group']=$row[csf('within_group')];
		$order_arr[$row[csf('job_id')]]['party_id']=$row[csf('party_id')];
		$order_arr[$row[csf('job_id')]]['qty']=$row[csf('qty')];
		$order_arr[$row[csf('job_id')]]['embl_name']=$row[csf('main_process_id')];
	}
    $buyer_po_str = implode(', ', array_unique($buyer_po));
    $buyer_style_str = implode(', ', array_unique($buyer_style));
	unset($order_sql);

//	$sql="select id, prefix_no_num, sys_no, location_id, recipe_id, job_no, order_id from trims_raw_mat_requisition_mst where entry_form=427 and status_active = 1 and is_deleted = 0 and company_id='$company_id' $location_cond $system_no_cond $recipe_no_cond order by id desc";

	$section_id=$dataArray[0][csf("section_id")];
	$inserted_by=$dataArray[0][csf("inserted_by")];


	$party_name="";

	if(  $order_arr[$dataArray[0][csf('job_id')]]['within_group'] ==1) $party_name=$company_library[$order_arr[$dataArray[0][csf('job_id')]]['party_id']];
	else if($order_arr[$dataArray[0][csf('job_id')]]['within_group']==2) $party_name=$buyer_library[$order_arr[$dataArray[0][csf('job_id')]]['party_id']];
	//$brand_arr=return_library_array("select id, brand_name from product_details_master", 'id', 'brand_name');


	$item_sql = "select id, item_group_code, item_name from lib_item_group where is_deleted=0 and status_active=1 and item_category in(101,22)";

	$item_result = sql_select($item_sql);

	foreach ($item_result as $row) {
		$item_arr[$row[csf('id')]]['id'] = $row[csf('id')];
		$item_arr[$row[csf('id')]]['item_group_code'] = $row[csf('item_group_code')];
		$item_arr[$row[csf('id')]]['item_name'] = $row[csf('item_name')];
	}

	?>
    <div style="width:930px; font-size:6px">
        <table width="100%" cellpadding="0" cellspacing="0" >
            <tr>
                <td width="70" align="right">
                    <img  src='../../<?php echo $imge_arr[str_replace("'","",$data[0])]; ?>' height='100%' width='100%' />
                </td>
                <td>
                    <table width="800" cellspacing="0" align="center">
                        <tr>
                            <td align="center" style="font-size:20px"><strong ><?php echo $company_library[$data[0]]; ?></strong></td>
                        </tr>
                        <tr>
                            <td align="center"  style="font-size:16px"><strong>Unit : <?php echo $location_arr[$dataArray[0][csf('location_id')]]; ?></strong></td>
                        </tr>
                        <tr class="form_caption">
                            <td  align="center" style="font-size:14px">
                                <?php echo show_company($data[0],'',''); ?>
                            </td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size:18px"><strong><?php echo $data[2]; ?></strong></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <table width="100%" cellpadding="0" cellspacing="0" >
            <tr>
                <td width="130"><strong>Requisition ID:</strong></td>
                <td width="175"><?php echo $dataArray[0][csf('requisition_no')]; ?></td>
                <td width="130"><strong>Party Name: </strong></td>
                <td width="175px"><?php echo $party_name; ?></td>
                <td width="130"><strong>Issue Date:</strong></td>
                <td width="175"><?php echo change_date_format($dataArray[0][csf('issue_date')]); ?></td>
            </tr>
            <tr>
                <td><strong>Job No:</strong></td>
                <td><?php echo $dataArray[0][csf('job_no')]; ?></td>
                <td><strong>Order No:</strong></td>
                <td><?php echo $order_arr[$dataArray[0][csf('job_id')]]['po']; ?></td>

                <td><strong>Target Prod. Qty:</strong></td>
                <td><?php echo $dataArray[0][csf('target_prod_qty')]; ?></td>
            </tr>
             <tr>
                <td><strong>Buyer PO:</strong></td>
                <td><?php echo $buyer_po_str; ?></td>
                <td><strong>Style:</strong></td>
                <td><?php echo $buyer_style_str; ?></td>
            </tr>
        </table>
        <br>
        <div style="width:100%;">
            <table align="right" cellspacing="0" width="930" border="1" rules="all" class="rpt_table">
                <thead bgcolor="#dddddd" align="center"><!-- style="font-size:12px"-->
                    <th width="30">SL</th>
                    <th>Item Group</th>
	                <th>Material Description</th>
	                <th>Brand</th>
	                <th>LOT</th>
	                <th>UOM</th>
	                <th>Req. Qty.</th>
	                <th>Requ. Qty.</th>
	                <th>Remarks</th>
                </thead>
				<?

				$mstId = $data[1];
				$com_id = $data[0];
				$job_no = $dataArray[0][csf('job_no')];
				$recipe_id = $dataArray[0][csf('recipe_id')];


				$sql = "SELECT b.id as update_id, b.mst_id, b.item_group_id, b.break_id, c.item_description, b.uom, b.requisition_qty, d.product_id, b.receive_dtls_id, c.job_quantity, b.remarks, d.specification, d.req_qty,c.job_quantity ,b.trim_break_id,b.section_id,b.product_id,a.job_no as job_no_mst, b.lot
				from trims_raw_mat_requisition_mst a, trims_raw_mat_requisition_dtls b, trims_job_card_dtls c, trims_job_card_breakdown d
				where  a.id=$mstId and a.id=b.mst_id and a.job_no=d.job_no_mst and c.id=d.mst_id and b.job_no=c.job_no_mst and b.job_no=d.job_no_mst  and b.product_id = d.product_id and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 
				";

				$data_array=sql_select($sql); $dtls_arr=array(); $productArr = array(); $jobcarddtlsidArr = array();
				foreach ($data_array as $row)
				{


						$dtls_arr[$row[csf('item_group_id')]][$row[csf('product_id')]]['item_group_id'] 	=$row[csf('item_group_id')];
						$dtls_arr[$row[csf('item_group_id')]][$row[csf('product_id')]]["trim_break_id"] 	.=$row[csf("trim_break_id")].",";
						$dtls_arr[$row[csf('item_group_id')]][$row[csf('product_id')]]["section_id"] 		=$row[csf("section_id")];
						$dtls_arr[$row[csf('item_group_id')]][$row[csf('product_id')]]["receive_dtls_id"]	=$row[csf("receive_dtls_id")];
						$dtls_arr[$row[csf('item_group_id')]][$row[csf('product_id')]]["book_con_dtls_id"]	.=$row[csf("book_con_dtls_id")].",";
						$dtls_arr[$row[csf('item_group_id')]][$row[csf('product_id')]]["booking_dtls_id"]	.=$row[csf("booking_dtls_id")].",";
						$dtls_arr[$row[csf('item_group_id')]][$row[csf('product_id')]]["break_id"]			.=$row[csf("break_id")].",";
						$dtls_arr[$row[csf('item_group_id')]][$row[csf('product_id')]]["buyer_po_no"]		=$row[csf("buyer_po_no")];
						$dtls_arr[$row[csf('item_group_id')]][$row[csf('product_id')]]["buyer_style_ref"]	=$row[csf("buyer_style_ref")];
						$dtls_arr[$row[csf('item_group_id')]][$row[csf('product_id')]]["item_description"]	=$row[csf("item_description")];
						$dtls_arr[$row[csf('item_group_id')]][$row[csf('product_id')]]["color_id"]			=$row[csf("color_id")];
						$dtls_arr[$row[csf('item_group_id')]][$row[csf('product_id')]]["size_id"]			=$row[csf("size_id")];
						$dtls_arr[$row[csf('item_group_id')]][$row[csf('product_id')]]["sub_section"]		=$row[csf("sub_section")];
						$dtls_arr[$row[csf('item_group_id')]][$row[csf('product_id')]]["uom"]				=$row[csf("uom")];
						$dtls_arr[$row[csf('item_group_id')]][$row[csf('product_id')]]["lot"]				=$row[csf("lot")];
						$dtls_arr[$row[csf('item_group_id')]][$row[csf('product_id')]]["conv_factor"]		=$row[csf("conv_factor")];
						$dtls_arr[$row[csf('item_group_id')]][$row[csf('product_id')]]["total_req_qty"]		+=$row[csf("req_qty")]*$row[csf("job_quantity")];
						$dtls_arr[$row[csf('item_group_id')]][$row[csf('product_id')]]["product_id"]		=$row[csf("product_id")];
						$dtls_arr[$row[csf('item_group_id')]][$row[csf('product_id')]]["description"]		=$row[csf("description")];
						$dtls_arr[$row[csf('item_group_id')]][$row[csf('product_id')]]["specification"]		=$row[csf("specification")];
						$dtls_arr[$row[csf('item_group_id')]][$row[csf('product_id')]]["brand_name"]		=$row[csf("brand_name")];
						$dtls_arr[$row[csf('item_group_id')]][$row[csf('product_id')]]["job_no_mst"]		=$row[csf("job_no_mst")];
						$dtls_arr[$row[csf('item_group_id')]][$row[csf('product_id')]]["requisition_qty"]	+=$row[csf("requisition_qty")];
						$dtls_arr[$row[csf('item_group_id')]][$row[csf('product_id')]]["job_card_dtls_id"]	.=$row[csf('job_card_dtls_id')].',';
						$dtls_arr[$row[csf('item_group_id')]][$row[csf('product_id')]]['req_details_id'] 	=$row[csf('update_id')];
						$dtls_arr[$row[csf('item_group_id')]][$row[csf('product_id')]]['remarks'] 	=$row[csf('remarks')];


					$trimsJobNo = $row[csf('job_no_mst')];
					$productArr[] = $row[csf("product_id")];
					$jobcarddtlsidArr[] = $row[csf("job_card_dtls_id")];
				}


				$requisitiondataSql = sql_select("select b.id,b.item_group_id,b.product_id, sum(b.requisition_qty) as total_requisition_qty   from trims_raw_mat_requisition_mst a, trims_raw_mat_requisition_dtls b
			        where a.id=b.mst_id and a.job_no='$trimsJobNo' and a.status_active=1 and b.status_active=1  group by b.id,b.item_group_id,b.product_id");
				$requisition_data_arr = array(); $updaterequisitionQtyArr = array();
				foreach ($requisitiondataSql as $row)
				{
					$requisition_data_arr[$row[csf('item_group_id')]][$row[csf('product_id')]]['requisition_qty'] += $row[csf('total_requisition_qty')];
					$updaterequisitionQtyArr[$row[csf('id')]]['requisition_qty'] += $row[csf('total_requisition_qty')];
			 	}


				$productArr = array_unique($productArr);
				$productIds = implode(',', $productArr);
				$products_data_arr = sql_select("select id, brand_name from product_details_master where id in($productIds) and status_active=1");
				$productsArr = array();
				foreach ($products_data_arr as $row)
				{
					$productsArr[$row[csf('id')]]['id'] = $row[csf('id')];
					$productsArr[$row[csf('id')]]['brand_name'] = $row[csf('brand_name')];
				}

				//echo '<pre>';
				//print_r($jobQtyArr);
				// echo '</pre>';


 			$i=1; $reqTotal=0; $requisitionTotal=0;


 			foreach($dtls_arr as $item_group_id=>$item_group_data)
			{
				$jobQnty=0;
				foreach($item_group_data as $row)
				{
			        $reqQty = $row['total_req_qty'];

					$reqTotal += $reqQty;

			 		$prevRequisitionQty = $requisition_data_arr[$row['item_group_id']][$row['product_id']]['requisition_qty'];
			        $balance = ($reqQty - $prevRequisitionQty);

			        $requisitionQty = $updaterequisitionQtyArr[$row['req_details_id']]['requisition_qty'];//$row['requisition_qty'];

			        $requisitionTotal += $requisitionQty;

					?>
                    <tr bgcolor="<?php echo $bgcolor; ?>">
                        <td><?php echo $i; ?></td>
                        <td>
		                	<?php echo $item_arr[$row['item_group_id']]['item_name']; ?>
		                </td>
		                <td>
		                	<?php echo $row['specification']; ?>
		                </td>
		                <td style="width: 10%" align="right">
		                	<?php echo $brand_arr[$row['product_id']]; ?>
		                </td>
		                <td style="width: 10%">
		                	<?php echo  $row['lot']; ?>
		                </td>
						<td style="width: 10%">
		                	<?php echo $unit_of_measurement[$row['uom']]; ?>
		                </td>
		                <td style="width: 10%" align="right">
		                	<?php echo number_format($reqQty, 3); ?>
		                </td>
		                <td style="width: 10%" align="right">
		                	<?php echo number_format($requisitionQty, 3); ?>
		                </td>
		                <td style="width: 10%">
		                    <?php echo $row['remarks']; ?>
		                </td>
                    </tr>
					<?
					$i++;
				}
			}
				?>
                <tr class="tbl_bottom">
                    <td align="right" colspan="6"><strong>Grand Total</strong></td>
                    <td align="right"><?php echo number_format($reqTotal, 3, '.', ''); ?>&nbsp;</td>
                    <td align="right"><?php echo number_format($requisitionTotal, 3, '.', ''); ?>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
            </table>

            <br>
			<?php echo signature_table(239, $com_id, "930px","","",$inserted_by); ?>
        </div>
    </div>
	<?
	exit();
}


if($action=="raw_mat_issue_requisition_print22222")
{
	extract($_REQUEST);
	$data = explode('*', $data);
	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$buyer_library = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
	$size_arr=return_library_array( "select id,size_name from lib_size where status_active=1 and is_deleted=0", "id", "size_name" );
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$batch_arr = return_library_array("select id, batch_no from pro_batch_create_mst", 'id', 'batch_no');
	$location_arr = return_library_array("select id, location_name from lib_location", 'id', 'location_name');
	$order_arr=array();
	$order_sql = sql_select("select a.subcon_job, a.within_group, a.party_id, b.id, b.order_no, b.order_id, b.main_process_id, sum (c.qnty) as qty
    	from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c
   		where a.subcon_job = b.job_no_mst and b.id = c.mst_id and a.entry_form = 255 and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0
		group by a.subcon_job, a.within_group, a.party_id, b.main_process_id, b.id, b.order_no, b.order_id");
	foreach($order_sql as $row)
	{
		/*$order_arr[$row[csf('id')]]['po']=$row[csf('order_no')];
		$order_arr[$row[csf('id')]]['within_group']=$row[csf('within_group')];
		$order_arr[$row[csf('id')]]['party_id']=$row[csf('party_id')];
		$order_arr[$row[csf('id')]]['qty']=$row[csf('qty')];
		$order_arr[$row[csf('id')]]['embl_name']=$row[csf('main_process_id')];*/
		
		$order_arr[$row[csf('order_id')]]['po']=$row[csf('order_no')];
		$order_arr[$row[csf('order_id')]]['within_group']=$row[csf('within_group')];
		$order_arr[$row[csf('order_id')]]['party_id']=$row[csf('party_id')];
		$order_arr[$row[csf('order_id')]]['qty']=$row[csf('qty')];
		$order_arr[$row[csf('order_id')]]['embl_name']=$row[csf('main_process_id')];

	}
	unset($order_sql);
	
	$buyer_po_arr=array();
	$po_sql ="select a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
	}
	unset($po_sql_res);
	
	/*$cust_arr=array();
	$cust_buyer_style_array=sql_select("SELECT id, subcon_job, order_id, order_no from subcon_ord_mst where entry_form=295 and status_active=1 and is_deleted=0");
	foreach ($cust_buyer_style_array as $cust_val) 
	{
		$cust_arr[$cust_val[csf('subcon_job')]]['order_no']=$cust_val[csf('order_no')]; 
		$cust_arr[$cust_val[csf('subcon_job')]]['job']=$cust_val[csf('subcon_job')]; 
	}
	unset($cust_buyer_style_array);*/
	$sql="select id, prefix_no_num, sys_no, location_id, recipe_id, job_no, order_id from trims_raw_mat_requisition_mst where entry_form=427 and status_active = 1 and is_deleted = 0 and company_id='$company_id' $location_cond $system_no_cond $recipe_no_cond order by id desc";

	$batch_arr = return_library_array("select id, batch_no from pro_batch_create_mst", 'id', 'batch_no');
	$pdate_cond=($db_type==2)? " TO_CHAR(production_hour,'HH24:MI') " : " TIME_FORMAT( production_hour, '%H:%i' ) ";
	$prod_data_arr=array(); $qty_data_arr=array();
	$prodData=sql_select("select id, mst_id, color_size_id, issue_date, $pdate_cond as production_hour, qcpass_qty, operator_name, shift_id, remarks from trims_raw_mat_requisition_dtls where status_active=1 and is_deleted=0 and mst_id='$data[1]'");
	foreach($prodData as $row)
	{
		$prod_data_arr[$row[csf('mst_id')]]['issue_date']=change_date_format($row[csf('issue_date')]);
		$prod_data_arr[$row[csf('mst_id')]]['production_hour']=$row[csf('production_hour')];
		$prod_data_arr[$row[csf('mst_id')]]['operator_name']=$row[csf('operator_name')];
		$prod_data_arr[$row[csf('mst_id')]]['shift_id']=$row[csf('shift_id')];
		
		$qty_data_arr[$row[csf('color_size_id')]]['id']=$row[csf('id')];
		$qty_data_arr[$row[csf('color_size_id')]]['qcpass_qty']=$row[csf('qcpass_qty')];
		$qty_data_arr[$row[csf('color_size_id')]]['remarks']=$row[csf('remarks')];
	}
	unset($prodData);
	
	$sql_mst = "select id, prefix_no_num, requisition_no, location_id, job_no, order_id, issue_date
		from trims_raw_mat_requisition_mst where entry_form=427 and id='$data[1]'";
	$dataArray = sql_select($sql_mst); $party_name="";
	if(  $order_arr[$dataArray[0][csf('order_id')]]['within_group'] ==1) $party_name=$company_library[$order_arr[$dataArray[0][csf('order_id')]]['party_id']];
	else if($order_arr[$dataArray[0][csf('order_id')]]['within_group']==2) $party_name=$buyer_library[$order_arr[$dataArray[0][csf('order_id')]]['party_id']];

	$brand_arr=return_library_array("select id, brand_name from product_details_master", 'id', 'brand_name');

	$item_sql = "select id, item_group_code, item_name from lib_item_group where is_deleted=0 and status_active=1 and item_category in(101,22)";

	$item_result = sql_select($item_sql);

	foreach ($item_result as $row) {
		$item_arr[$row[csf('id')]]['id'] = $row[csf('id')];
		$item_arr[$row[csf('id')]]['item_group_code'] = $row[csf('item_group_code')];
		$item_arr[$row[csf('id')]]['item_name'] = $row[csf('item_name')];
	}
	
	?>
    <div style="width:930px; font-size:6px">
        <table width="100%" cellpadding="0" cellspacing="0" >
            <tr>
                <td width="70" align="right"> 
                    <img  src='../../<?php echo $imge_arr[str_replace("'","",$data[0])]; ?>' height='100%' width='100%' />
                </td>
                <td>
                    <table width="800" cellspacing="0" align="center">
                        <tr>
                            <td align="center" style="font-size:20px"><strong ><?php echo $company_library[$data[0]]; ?></strong></td>
                        </tr>
                        <tr>
                            <td align="center"  style="font-size:16px"><strong>Unit : <?php echo $location_arr[$dataArray[0][csf('location_id')]]; ?></strong></td>
                        </tr>
                        <tr class="form_caption">
                            <td  align="center" style="font-size:14px">  
                                <?php echo show_company($data[0],'',''); ?> 
                            </td>  
                        </tr>
                        <tr>
                            <td align="center" style="font-size:18px"><strong><?php echo $data[2]; ?></strong></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <table width="100%" cellpadding="0" cellspacing="0" >  
            <tr>
                <td width="130"><strong>Requisition ID:</strong></td>
                <td width="175"><?php echo $dataArray[0][csf('requisition_no')]; ?></td>
                <td width="130"><strong>Party Name: </strong></td>
                <td width="175px"><?php echo $party_name; ?></td>
                <td width="130"><strong>Issue Date:</strong></td>
                <td width="175"><?php echo change_date_format($dataArray[0][csf('issue_date')]); ?></td>
            </tr>
            <tr>
                <td><strong>Job No:</strong></td>
                <td><?php echo $dataArray[0][csf('job_no')]; ?></td>
                <td><strong>Order No:</strong></td>
                <td><?php echo $order_arr[$dataArray[0][csf('order_id')]]['po']; ?></td>
            </tr>
        </table>
        <br>
        <div style="width:100%;">
            <table align="right" cellspacing="0" width="930" border="1" rules="all" class="rpt_table">
                <thead bgcolor="#dddddd" align="center"><!-- style="font-size:12px"-->
                    <th width="30">SL</th>
                    <!-- <th width="140">Gmts Item</th>
                    <th width="120">Body Part</th>
                    <th width="110">Process/ Type</th>
                    <th width="120">Color</th>
                    <th width="70">Size</th>
                    <th width="80">Production Qty (Pcs)</th>
                    <th>Remarks</th> -->
                    <th>Item Group</th>
	                <th>Material Description</th>
	                <th>Brand</th>
	                <th>UOM</th>
	                <th>Req. Qty.</th>
	                <th>Requ. Qty.</th>
	                <th>Remarks</th>
                </thead>
				<?
				
				$mstId = $data[1];
				$com_id = $data[0];
				$job_no = $dataArray[0][csf('job_no')];
				$recipe_id = $dataArray[0][csf('recipe_id')];				

				/*$sql= "select  a.id, a.subcon_job, b.id as order_id, b.main_process_id, b.gmts_item_id, b.embl_type, b.body_part, c.id as color_size_id, c.color_id, c.size_id, sum(c.qnty) as qty from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c, pro_recipe_entry_mst d where a.subcon_job=b.job_no_mst and b.id=c.mst_id and a.subcon_job=d.job_no and b.id=d.po_id 
							
				and b.main_process_id=d.embl_name and b.gmts_item_id=d.gmts_item and b.embl_type=d.embl_type and b.body_part=d.body_part and c.color_id=d.color_id
				and a.entry_form=295 and d.entry_form=300 and a.status_active=1 and a.company_id='$com_id' and d.id='$recipe_id' group by a.id, a.subcon_job, b.id,b.main_process_id, b.gmts_item_id, b.embl_type, b.body_part, c.id, c.color_id, c.size_id, d.recipe_no_prefix_num order by c.id ASC";*/

				$sql= "select b.id as update_id, b.mst_id, b.item_group_id, b.break_id, c.item_description, c.uom, b.requisition_qty, d.product_id, b.receive_dtls_id, c.job_quantity, b.remarks, d.specification, d.req_qty*c.job_quantity as total_req_qty
				from trims_raw_mat_requisition_mst a, trims_raw_mat_requisition_dtls b, trims_job_card_dtls c, trims_job_card_breakdown d
				where a.id=$mstId and a.id=b.mst_id and a.job_no=d.job_no_mst and c.id=d.mst_id and b.break_id=c.break_id and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1
				order by b.id";
				// echo $sql; // die;
				$sql_res=sql_select($sql);
				
 				$i=1; $reqTotal=0; $requisitionTotal=0;

				foreach ($sql_res as $row) 
				{
					$reqQty = $row[csf('total_req_qty')];
					$requisitionQty = $row[csf('requisition_qty')];
					$reqTotal += $reqQty;
					$requisitionTotal += $requisitionQty;
					/*if($row[csf('main_process_id')]==1) $new_subprocess_array= $emblishment_print_type;
					else if($row[csf('main_process_id')]==2) $new_subprocess_array= $emblishment_embroy_type;
					else if($row[csf('main_process_id')]==3) $new_subprocess_array= $emblishment_wash_type;
					else if($row[csf('main_process_id')]==4) $new_subprocess_array= $emblishment_spwork_type;
					else if($row[csf('main_process_id')]==5) $new_subprocess_array= $emblishment_gmts_type;
					else $new_subprocess_array=$blank_array;*/


					?>
                    <tr bgcolor="<?php echo $bgcolor; ?>">
                        <td><?php echo $i; ?></td>
                        <td>
		                	<?php echo $item_arr[$row[csf('item_group_id')]]['item_name']; ?>
		                </td>
		                <td>
		                	<?php echo $row[csf('specification')]; ?>
		                </td>
		                <td style="width: 10%" align="right">
		                	<?php echo $brand_arr[$row[csf('product_id')]]; ?>
		                </td>
		                <td style="width: 10%">
		                	<?php echo $unit_of_measurement[$row[csf('uom')]]; ?>
		                </td>
		                <td style="width: 10%" align="right">
		                	<?php echo number_format($reqQty, 2); ?>
		                </td>
		                <td style="width: 10%" align="right">
		                	<?php echo number_format($requisitionQty, 2); ?>
		                </td>
		                <td style="width: 10%">
		                    <?php echo $row[csf('remarks')]; ?>
		                </td>

                        <!-- <td><?php //echo $garments_item[$row[csf('gmts_item_id')]]; ?></td>
                        <td><?php //echo $body_part[$row[csf('body_part')]]; ?>&nbsp;</td>
                        <td><?php //echo $new_subprocess_array[$row[csf('embl_type')]]; ?>&nbsp;</td>
                        <td><?php //echo $color_arr[$row[csf('color_id')]]; ?>&nbsp;</td>
                        <td align="center"><?php //echo $size_arr[$row[csf('size_id')]]; ?>&nbsp;</td>
                        <td align="right"><?php //echo number_format($qty_data_arr[$row[csf('color_size_id')]]['qcpass_qty'], 2, '.', ''); ?>&nbsp;</td>
                        <td><?php //echo $qty_data_arr[$row[csf('color_size_id')]]['remarks']; ?>&nbsp;</td> -->
                    </tr>
					<?
					$i++;
				}
				?>
                <tr class="tbl_bottom">
                    <td align="right" colspan="5"><strong>Grand Total</strong></td>
                    <td align="right"><?php echo number_format($reqTotal, 2, '.', ''); ?>&nbsp;</td>
                    <td align="right"><?php echo number_format($requisitionTotal, 2, '.', ''); ?>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
            </table>
            
            <br>
			<?php echo signature_table(239, $com_id, "930px"); ?>
        </div>
    </div>
	<?
	exit();
}

if ($action=="job_popup")
	{
		echo load_html_head_contents('Job Popup Info', '../../../', 1, 1, $unicode, '', '');
		?>
		<script>
			function js_set_value(id)
			{ 
				$("#hidden_mst_id").val(id);
				document.getElementById('selected_job').value=id;
				parent.emailwindow.hide();
			}
			
			function fnc_load_party_popup(type,within_group)
			{
				var company = $('#cbo_company_name').val();
				var party_name = $('#cbo_party_name').val();
				var location_name = $('#cbo_location_name').val();
				var within_group = $('#cbo_within_group').val();
				load_drop_down( 'raw_material_issue_requisition_controller', company+'_'+within_group, 'load_drop_down_buyer', 'buyer_td' );
			}
			function search_by(val)
			{
				$('#txt_search_string').val('');
				if(val==1 || val==0)
				{
					$('#search_by_td').html('Requisition No');
				}
				else if(val==2)
				{
					$('#search_by_td').html('Job No');
				}
			}
		</script>
	</head>
	<body>
	<div align="center" style="width:100%;" >
	    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	        <table width="940" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
	            <thead> 
	                <tr>
	                    <th colspan="9"><?php echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
	                </tr>
	                <tr>
	                    <th width="140" class="must_entry_caption">Company Name</th>
	                    <th width="60">Within Group</th>                           
	                    <th width="140">Party Name</th>
	                    <th width="80">Search By</th>
	                    <th width="100" id="search_by_td">Job ID</th>
	                    <th width="80">Section</th>
	                    <th width="60">Year</th>
	                    <th width="170">Date Range</th>                            
	                    <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
	                </tr>           
	            </thead>
	            <tbody>
	                <tr class="general">
	                    <td><input type="hidden" id="selected_job"><?php $data=explode("_",$data); ?>  <!--  echo $data;-->
	                        <?php 
	                        echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $data[0], "fnc_load_party_popup(1,document.getElementById('cbo_within_group').value);",1); ?>
	                    </td>
	                    <td>
	                        <?php echo create_drop_down( "cbo_within_group", 60, $yes_no,"", 0, "--  --", 1, "fnc_load_party_popup(1,this.value);" ); ?>
	                    </td>
	                    <td id="buyer_td">
	                        <?php 
							echo create_drop_down( "cbo_party_name", 150, "","", 1, "-- Select Party --", "", "fnc_load_party_popup(1,this.value);" );   	 	 
	                        ?>
	                    </td>
	                    <td>
	                    	<?
	                            $search_by_arr=array(1=>"Job ID",2=>"W/O No",4=>"Buyer Po",5=>"Buyer Style");
	                            echo create_drop_down( "cbo_type",80, $search_by_arr,"",0, "",1,'search_by(this.value)',0 );
	                        ?>
	                    </td>
	                    <td align="center">
	                        <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:100px" placeholder="" />
	                    </td>
	                    <td><?php echo create_drop_down( 'cbo_section', 80, $trims_section, '', 1, '-- Select Section --', $data[2], '', 1,'','','','','','',"cboSection[]"); ?></td>
	                    <td align="center"><?php echo create_drop_down( "cbo_year_selection", 60, $year,"", 1, "-- Select --", date('Y'), "",0 ); ?></td>
	                    <td align="center">
	                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px">
	                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px">
	                    </td>
	                    <td align="center">
	                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_section').value, 'create_job_search_list_view', 'search_div', 'raw_material_issue_requisition_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:70px;" /></td>
	                    </tr>
	                    <tr>
	                        <td colspan="9" align="center" valign="middle">
	                            <?php echo load_month_buttons();  ?>
	                            <input type="hidden" name="hidden_mst_id" id="hidden_mst_id" class="datepicker" style="width:70px">
	                        </td>
	                    </tr>
	                    <tr>
	                        <td colspan="9" align="center" valign="top" id=""><div id="search_div"></div></td>
	                    </tr>
	                </tbody>
	            </table>    
	            </form>
	        </div>
	    </body>           
	    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	    </html>
	    <?
	    exit();
	}

if ($action=="create_job_search_list_view")
{	
	$data=explode('_',$data);
	$party_id=str_replace("'","",$data[1]);
	$search_by=str_replace("'","",$data[4]);
	$search_str=trim(str_replace("'","",$data[5]));
	$search_type =$data[6];
	$within_group =$data[7];
	$section_id =$data[9];
	if($db_type==0) { $year_cond=" and YEAR(a.insert_date)=$data[8]";   }
	if($db_type==2) {$year_cond=" and to_char(a.insert_date,'YYYY')=$data[8]";}

	if($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	//echo $search_type; die;
	$job_cond=""; $style_cond=""; $po_cond=""; $search_com_cond="";
	/*if($search_str!="")
	{
		$search_com_cond="and a.job_no_prefix_num='$search_str'";
	}*/
	// $search_by_arr=array(1=>"Job ID",2=>"W/O No",3=>"Buyer Job",4=>"Buyer Po",5=>"Buyer Style");
	if($search_type==1)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num='$search_str'";
			else if($search_by==2) $search_com_cond="and a.order_no='$search_str'";
			else if ($search_by==4) $search_com_cond=" and b.buyer_po_no = '$search_str' ";
			else if ($search_by==5) $search_com_cond=" and b.buyer_style_ref = '$search_str' ";
		}
	}
	else if($search_type==2)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '$search_str%'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '$search_str%'";  
			else if ($search_by==4) $search_com_cond=" and b.buyer_po_no like '$search_str%'";
			else if ($search_by==5) $search_com_cond=" and b.buyer_style_ref like '$search_str%'";  
		}
	}
	else if($search_type==3)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str'";  
			else if ($search_by==4) $search_com_cond=" and b.buyer_po_no like '%$search_str'";
			else if ($search_by==5) $search_com_cond=" and b.buyer_style_ref like '%$search_str'";  
		}
	}
	else if($search_type==4 || $search_type==0)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num like '%$search_str%'";  
			else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str%'";  
			else if ($search_by==4) $search_com_cond=" and b.buyer_po_no like '%$search_str%'"; 
			else if ($search_by==5) $search_com_cond=" and b.buyer_style_ref like '%$search_str%'";   
		}
	}
	

	if($party_id!=0) $party_id_cond=" and a.party_id='$party_id'"; else $party_id_cond="";
	if($section_id!=0) $section_id_cond=" and a.section_id='$section_id'"; else $section_id_cond="";

	if($db_type==0)
	{ 
		if ($data[2]!="" &&  $data[3]!="") $delivery_date = "and a.delivery_date between '".change_date_format($data[2],'yyyy-mm-dd')."' and '".change_date_format($data[3],'yyyy-mm-dd')."'"; else $delivery_date ="";
	}
	else
	{
		if ($data[2]!="" &&  $data[3]!="") $delivery_date = "and a.delivery_date between '".change_date_format($data[2], "", "",1)."' and '".change_date_format($data[3], "", "",1)."'"; else $delivery_date ="";
	}
	if ($within_group!=0) $withinGroup=" and a.within_group='$within_group'"; else $withinGroup="";
	if($within_group==1)
	{
		$party_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	}
	else
	{
		$party_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	}
	
	$po_ids='';
	
	
	if($db_type==0) 
	{
		$ins_year_cond="year(a.insert_date)";
	}
	else if($db_type==2)
	{
		$ins_year_cond="TO_CHAR(a.insert_date,'YYYY')";
	}	
	
	$sql= "select a.id, a.trims_job, a.job_no_prefix_num, $ins_year_cond as year, a.company_id, a.location_id, a.party_id,  a.order_no,a.received_no, a.delivery_date,a.section_id 
	from trims_job_card_mst a, trims_job_card_dtls b
	where a.entry_form=257 and a.trims_job=b.job_no_mst and a.id=b.mst_id and a.status_active=1 and b.status_active=1 $delivery_date $company $party_id_cond $search_com_cond  $withinGroup $section_id_cond $year_cond
	group by a.id, a.trims_job, a.job_no_prefix_num, a.insert_date, a.company_id, a.location_id, a.party_id, a.order_no ,a.received_no,a.delivery_date,a.section_id 
	order by a.id DESC";
	//echo $sql;
	$data_array=sql_select($sql);
	?>
     <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="685" >
        <thead>
            <th width="30">SL</th>
            <th width="120">Job No</th>
            <th width="100">Section</th>
            <th width="60">Year</th>
            <th width="120">W/O No</th>
            <th width="120">Receive No</th>
            <th>Delivery Date</th>
        </thead>
        </table>
        <div style="width:685px; max-height:270px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="665" class="rpt_table" id="tbl_po_list">
        <tbody>
            <?php 
            $i=1;
            foreach($data_array as $row)
            {  
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                ?>
                <tr bgcolor="<?php echo $bgcolor; ?>" onClick='js_set_value("<?php echo $row[csf('id')].'_'.$row[csf('trims_job')]; ?>")' style="cursor:pointer" >
                    <td width="30"><?php echo $i; ?></td>
                    <td width="120" style="text-align:center;" ><?php echo $row[csf('job_no_prefix_num')]; ?></td>
                    <td width="100"><?php echo $trims_section[$row[csf('section_id')]]; ?></td>
                    <td width="60" style="text-align:center;"><?php echo $row[csf('year')]; ?></td>
                    <td width="120"><?php echo $row[csf('order_no')]; ?></td>
                    <td width="120"><?php echo $row[csf('received_no')]; ?></td>
                    <td style="text-align:center;"><?php echo change_date_format($row[csf('delivery_date')]); ?></td>
                </tr>
				<?php 
                $i++; 
            }
            ?>
        </tbody>
    </table>
	<?
	exit();
}

if( $action=='order_dtls_list_view' ) 
{ //echo $data;
	$data=explode('**',$data);
	$searchType=$data[0];
	$mstId=$data[1];
	$jobNo="'".$data[2]."'";
	$cbo_section=$data[3];
	$trimsJobNo = '';

	$brand_arr=return_library_array("select id, brand_name from product_details_master", 'id', 'brand_name');

	$item_sql = "select id, item_group_code, item_name from lib_item_group where is_deleted=0 and status_active=1 and item_category in(101,22)";

	$item_result = sql_select($item_sql);

	foreach ($item_result as $row) {
		$item_arr[$row[csf('id')]]['id'] = $row[csf('id')];
		$item_arr[$row[csf('id')]]['item_group_code'] = $row[csf('item_group_code')];
		$item_arr[$row[csf('id')]]['item_name'] = $row[csf('item_name')];
	}

	/*echo '<pre>';
	print_r($data);
	echo '</pre>';*/
	//$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );
	$size_arr=return_library_array( "select id,size_name from lib_size",'id','size_name');
	$tblRow=0; $buyer_po_arr=array();
	$sql = '';

	/*$sql= "select a.id as mst_id, a.order_no,a.received_no,a.section_id, b.id,b.buyer_po_no,b.buyer_po_id, b.buyer_style_ref, b.item_description, b.color_id, b.size_id, b.sub_section, b.uom, b.job_quantity, b.conv_factor ,b.receive_dtls_id,b.book_con_dtls_id , b.booking_dtls_id ,b.break_id
	from trims_job_card_mst a, trims_job_card_dtls b
	where a.entry_form=257 and a.trims_job=b.job_no_mst and a.id=b.mst_id and a.id=$data[1]  and a.status_active=1 and b.status_active=1 
	group by a.id, a.order_no,a.received_no,a.section_id, b.id, b.buyer_po_no,b.buyer_po_id, b.buyer_style_ref, b.item_description, b.color_id, b.size_id, b.sub_section, b.uom, b.job_quantity, b.receive_dtls_id,b.book_con_dtls_id , b.booking_dtls_id , b.conv_factor ,b.break_id
	order by a.id DESC";*/

	if($searchType==1) 
	{
		$sql ="SELECT a.id as trim_break_id, a.mst_id, a.order_id, a.job_no_mst, a.book_con_dtls_id, a.product_id, a.description, a.specification, a.pcs_unit, a.cons_qty, a.process_loss, a.process_loss_qty, a.remarks, b.unit_of_measure as uom, b.item_group_id, b.current_stock, b.brand_name, a.req_qty,c.job_quantity, c.break_id, c.receive_dtls_id,d.section_id, a.lot,c.mst_id as job_id, c.color_id 
		from trims_job_card_breakdown a, product_details_master b, trims_job_card_dtls c, trims_job_card_mst d
		where b.item_category_id in (101,22) and a.product_id=b.id and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and a.is_deleted=0 and a.job_no_mst=$jobNo and a.mst_id=c.id  and d.trims_job=c.job_no_mst and d.id=c.mst_id";
	/*if($cbo_section==25){
		$sql.=" union all select a.id as trim_break_id, a.mst_id, a.order_id, a.job_no_mst, a.book_con_dtls_id, a.product_id, a.description, a.specification, a.pcs_unit, a.cons_qty, a.process_loss, a.process_loss_qty, a.remarks, b.unit_of_measure as uom, b.item_group_id, b.current_stock, b.brand_name, a.req_qty,c.job_quantity, c.break_id, c.receive_dtls_id, d.section_id, e.batch_lot 
	   from trims_job_card_breakdown a, product_details_master b, trims_job_card_dtls c,trims_job_card_mst d, inv_transaction e where 
	   b.item_category_id in (22) and e.item_category in (22) and b.id=e.prod_id and a.product_id=b.id and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 and  e.status_active=1 and a.is_deleted=0 and a.job_no_mst=$jobNo and a.mst_id=c.id  and d.trims_job=c.job_no_mst and d.id=c.mst_id 
	   group by a.id, a.mst_id, a.order_id, a.job_no_mst, a.book_con_dtls_id, a.product_id, a.description, a.specification, a.pcs_unit, a.cons_qty, a.process_loss, a.process_loss_qty, a.remarks, b.unit_of_measure , b.item_group_id, b.current_stock, b.brand_name, a.req_qty,c.job_quantity, c.break_id, c.receive_dtls_id, d.section_id, e.batch_lot";
	    }*/
	} 
	else
	{
		$sql = "SELECT b.id as update_id, b.mst_id, b.item_group_id, b.break_id, c.item_description, b.uom, b.requisition_qty, d.product_id, b.receive_dtls_id, c.job_quantity, b.remarks, d.specification, d.req_qty, c.job_quantity, b.trim_break_id, b.section_id, b.product_id, a.job_no as job_no_mst, b.lot,c.mst_id as job_id, c.color_id 
		from trims_raw_mat_requisition_mst a, trims_raw_mat_requisition_dtls b, trims_job_card_dtls c, trims_job_card_breakdown d
		where a.id=$mstId and a.id=b.mst_id and a.job_no=d.job_no_mst and c.id=d.mst_id and b.job_no=c.job_no_mst and b.job_no=d.job_no_mst  and b.product_id = d.product_id and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 
		order by b.id";
		 
	}
	
	//echo count(sql_select($sql)); die;
	//echo $sql."<br>";
	$data_array=sql_select($sql); $dtls_arr=array(); $productArr = array(); $jobcarddtlsidArr = array(); $trimsJobidArr = array();
	foreach ($data_array as $row) 
	{
		$dtls_arr[$row[csf('item_group_id')]][$row[csf('product_id')]][$row[csf('lot')]]['item_group_id'] 	=$row[csf('item_group_id')];
		$dtls_arr[$row[csf('item_group_id')]][$row[csf('product_id')]][$row[csf('lot')]]["trim_break_id"] 	.=$row[csf("trim_break_id")].",";
		$dtls_arr[$row[csf('item_group_id')]][$row[csf('product_id')]][$row[csf('lot')]]["section_id"] 		=$row[csf("section_id")];
		$dtls_arr[$row[csf('item_group_id')]][$row[csf('product_id')]][$row[csf('lot')]]["receive_dtls_id"]	=$row[csf("receive_dtls_id")];
		$dtls_arr[$row[csf('item_group_id')]][$row[csf('product_id')]][$row[csf('lot')]]["book_con_dtls_id"]	.=$row[csf("book_con_dtls_id")].",";
		$dtls_arr[$row[csf('item_group_id')]][$row[csf('product_id')]][$row[csf('lot')]]["booking_dtls_id"]	.=$row[csf("booking_dtls_id")].",";
		$dtls_arr[$row[csf('item_group_id')]][$row[csf('product_id')]][$row[csf('lot')]]["break_id"]			.=$row[csf("break_id")].",";
		$dtls_arr[$row[csf('item_group_id')]][$row[csf('product_id')]][$row[csf('lot')]]["buyer_po_no"]		=$row[csf("buyer_po_no")];
		$dtls_arr[$row[csf('item_group_id')]][$row[csf('product_id')]][$row[csf('lot')]]["buyer_style_ref"]	=$row[csf("buyer_style_ref")];
		$dtls_arr[$row[csf('item_group_id')]][$row[csf('product_id')]][$row[csf('lot')]]["item_description"]	=$row[csf("item_description")];
		$dtls_arr[$row[csf('item_group_id')]][$row[csf('product_id')]][$row[csf('lot')]]["color_id"]			=$row[csf("color_id")];
		$dtls_arr[$row[csf('item_group_id')]][$row[csf('product_id')]][$row[csf('lot')]]["size_id"]			=$row[csf("size_id")];
		$dtls_arr[$row[csf('item_group_id')]][$row[csf('product_id')]][$row[csf('lot')]]["sub_section"]		=$row[csf("sub_section")];
		$dtls_arr[$row[csf('item_group_id')]][$row[csf('product_id')]][$row[csf('lot')]]["uom"]				=$row[csf("uom")];
		$dtls_arr[$row[csf('item_group_id')]][$row[csf('product_id')]][$row[csf('lot')]]["conv_factor"]		=$row[csf("conv_factor")];
		$dtls_arr[$row[csf('item_group_id')]][$row[csf('product_id')]][$row[csf('lot')]]["total_req_qty"]		+=$row[csf("req_qty")]*$row[csf("job_quantity")];
		$dtls_arr[$row[csf('item_group_id')]][$row[csf('product_id')]][$row[csf('lot')]]["product_id"]		=$row[csf("product_id")];
		$dtls_arr[$row[csf('item_group_id')]][$row[csf('product_id')]][$row[csf('lot')]]["description"]		=$row[csf("description")];
		$dtls_arr[$row[csf('item_group_id')]][$row[csf('product_id')]][$row[csf('lot')]]["specification"]		=$row[csf("specification")];
		$dtls_arr[$row[csf('item_group_id')]][$row[csf('product_id')]][$row[csf('lot')]]["brand_name"]		=$row[csf("brand_name")];
		$dtls_arr[$row[csf('item_group_id')]][$row[csf('product_id')]][$row[csf('lot')]]["job_no_mst"]		=$row[csf("job_no_mst")];
		$dtls_arr[$row[csf('item_group_id')]][$row[csf('product_id')]][$row[csf('lot')]]["requisition_qty"]	+=$row[csf("requisition_qty")];
		$dtls_arr[$row[csf('item_group_id')]][$row[csf('product_id')]][$row[csf('lot')]]["job_card_dtls_id"]	.=$row[csf('job_card_dtls_id')].',';
		$dtls_arr[$row[csf('item_group_id')]][$row[csf('product_id')]][$row[csf('lot')]]['req_details_id'] 	=$row[csf('update_id')];
		$dtls_arr[$row[csf('item_group_id')]][$row[csf('product_id')]][$row[csf('lot')]]['batch_lot'] 	=$row[csf('batch_lot')];
		$dtls_arr[$row[csf('item_group_id')]][$row[csf('product_id')]][$row[csf('lot')]]['lot'] 	=$row[csf('lot')];
		$dtls_arr[$row[csf('item_group_id')]][$row[csf('product_id')]][$row[csf('lot')]]['current_stock'] 	=$row[csf('current_stock')];
		 

		$trimsJobNo = $row[csf('job_no_mst')];
		$productArr[] = $row[csf("product_id")];
		$jobcarddtlsidArr[] = $row[csf("job_card_dtls_id")];
		$trimsJobidArr[] = $row[csf('job_id')];
	}
	
	$trimsJobidArrss = array_unique($trimsJobidArr);
	$job_idss = implode(',', $trimsJobidArrss);
 	
	//echo "<pre>";
	//print_r($jobcarddtlsidArr);
 	 
	$requisitiondataSql = sql_select("select b.id, b.item_group_id, b.product_id, b.lot, sum(b.requisition_qty) as total_requisition_qty   
	from trims_raw_mat_requisition_mst a, trims_raw_mat_requisition_dtls b
    where a.id=b.mst_id and a.job_no='$trimsJobNo' and a.job_id in ($job_idss)  and a.status_active=1 and b.status_active=1  
	group by b.id, b.item_group_id, b.product_id, b.lot");
	$requisition_data_arr = array(); $updaterequisitionQtyArr = array();
	foreach ($requisitiondataSql as $row) 
	{
		$requisition_data_arr[$row[csf('item_group_id')]][$row[csf('product_id')]][$row[csf('lot')]]['requisition_qty'] += $row[csf('total_requisition_qty')];
		$updaterequisitionQtyArr[$row[csf('id')]]['requisition_qty'] += $row[csf('total_requisition_qty')];
 	}
		
	$productArr = array_unique($productArr);
	$productIds = implode(',', $productArr);
	$products_data_arr = sql_select("select id, brand_name from product_details_master where id in($productIds) and status_active=1");
	$productsArr = array();
	foreach ($products_data_arr as $row) 
	{
		$productsArr[$row[csf('id')]]['id'] = $row[csf('id')];
		$productsArr[$row[csf('id')]]['brand_name'] = $row[csf('brand_name')];
	}

	//echo '<pre>';
	//print_r($dtls_arr);
	// echo '</pre>';

	foreach($dtls_arr as $item_group_id=>$item_group_data) 
	{
		$jobQnty=0;
		foreach($item_group_data as $prod_id=>$prod_data) 
		{
			foreach($prod_data as $lot_no=>$row)
			{		
				$tblRow++;
				$reqQty = $row['total_req_qty'];
				
			   /* if($row['total_req_qty']!="")
				{
					$reqQty = $row['total_req_qty'];
				}
				else
				{
					$job_card_dtls_ids=explode(",",chop($row['job_card_dtls_id'],','));
					for($j=0; $j<count($job_card_dtls_ids); $j++)
					{
						$jobQnty +=$jobQtyArr[$job_card_dtls_ids[$j]]['total_req_qty'];
					}
					$reqQty = $jobQnty;
				}*/
									
				//$prevRequisitionQty = $requisitionQtyArr[$row['item_group_id']];
				$prevRequisitionQty = $requisition_data_arr[$row['item_group_id']][$row['product_id']][$lot_no]['requisition_qty'];
				$balance = ($reqQty - $prevRequisitionQty);
				$requisitionQty = $searchType==1 ? 0 : $updaterequisitionQtyArr[$row['req_details_id']]['requisition_qty'];//$row['requisition_qty'];
				$breakId = rtrim($row['break_id'], ',');
				$trim_break_id = rtrim($row['trim_break_id'], ',');
				$balance_for_update=$balance+$requisitionQty;
				$balance_for_update=number_format($balance_for_update,3);
				$balance_for_update=str_replace(",","",trim($balance_for_update));
				//echo $balance_for_update.'=='.$balance.'=='.$requisitionQty.'++';
				//Item Category Item Group Section Sub Section Item Description Item Size UOM Req. Qty. Stock Remarks
				$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name" );

				?>
				<tr bgcolor="<?php echo $bgcolor; ?>" id="row_<?php echo $tblRow; ?>" align="center">
					<td style="width: 10%">
						<input id="txtItemGroup_<?php echo $tblRow; ?>" name="txtItemGroup[]" type="text" class="text_boxes" value="<?php echo $item_arr[$row['item_group_id']]['item_name']; ?>" style="width: 90%" disabled />
					</td>
					<td style="width: 17%">
						<input id="txtdescription_<?php echo $tblRow; ?>" name="txtdescription[]" type="text" class="text_boxes" value="<?php echo $row['specification']; ?>" style="width: 90%" disabled />
					</td>
					<td style="width: 10%" align="right">
						<input type="text" name="txtcolorname[]" id="txtcolorname_<?php echo $tblRow; ?>" class="text_boxes" style="width: 90%;" value="<?php echo $color_library[$row['color_id']];  ?>" disabled />
						<input type="hidden" name="txtcolor[]" id="txtcolor_<?php echo $tblRow; ?>"  value="<?php echo $row['color_id'];  ?>"  />
					</td>
					<td style="width: 10%" align="right">
						<input type="text" name="txtLot[]" id="txtLot_<?php echo $tblRow; ?>" class="text_boxes" style="width: 90%;" value="<?php echo $lot_no;  ?>" disabled />
					</td>
					<td style="width: 10%" align="right">
						<input type="text" name="txtBrand[]" id="txtBrand_<?php echo $tblRow; ?>" class="text_boxes" style="width: 90%;" value="<?php echo $brand_arr[$row['product_id']]; // echo $productsArr[$row[csf('product_id')]]['brand_name'] ?>" disabled />
					</td>
					<td style="width: 7%">
						<?php echo create_drop_down( "cboUom_".$tblRow, 60, $unit_of_measurement,"", 1, "-- Select --", $row['uom'],1, 1,'','','','','','',"cboUom[]"); ?>
					</td>
					<td style="width: 10%" align="right">
						<input type="text" name="txtReqQty[]" id="txtReqQty_<?php echo $tblRow; ?>" class="text_boxes_numeric" style="width: 90%" value="<?php echo number_format($reqQty, 3); ?>" disabled />
					</td>
					<td style="width: 10%" align="right">
						<input type="text" name="txtRequQty[]" id="txtRequQty_<?php echo $tblRow; ?>" class="text_boxes_numeric" style="width: 90%" value="<?php echo number_format($requisitionQty, 3); ?>" onKeyUp="calculateBalance(<?php echo $balance_for_update; ?>, this.value, 'txtBalance_<?php echo $tblRow; ?>');checkBalance(this, <?php echo $balance_for_update; ?>, document.getElementById('txtBalance_<?php echo $tblRow; ?>'), document.getElementById('txtStock_<?php echo $tblRow; ?>'));" onFocus="checkStoreSelection();" />
					</td>
					<td style="width: 10%" align="right">
						<input type="text" name="txtBalance[]" id="txtBalance_<?php echo $tblRow; ?>" class="text_boxes_numeric" style="width: 90%;" value="<?php echo number_format($balance,3); ?>" disabled />
					</td>
					<td style="width: 10%" align="right">
						<input type="text" name="txtStock[]" id="txtStock_<?php echo $tblRow; ?>" class="text_boxes_numeric" style="width: 90%;" value="<?php echo $row['current_stock']; ?>" disabled />
					</td>
					<td style="width: 10%">
						<input type="text" name="txtRemarks[]" id="txtRemarks_<?php echo $tblRow; ?>" class="text_boxes" style="width: 90%" placeholder="Write" value="<?php echo $row['remarks']; ?>" />
						<input type="hidden" name="updateIdDtls[]" id="updateIdDtls_<?php echo $tblRow; ?>" value="<?php echo $row['req_details_id']; ?>" />
						<input type="hidden" name="hdnBreakId[]" id="hdnBreakId_<?php echo $tblRow; ?>" value="<?php echo $breakId; ?>"/>
						<input type="hidden" name="hdnRcvDtlsId[]" id="hdnRcvDtlsId_<?php echo $tblRow; ?>" value="<?php echo $row['receive_dtls_id']; ?>"/>
						<input type="hidden" name="hdnItemGroupId[]" id="hdnItemGroupId_<?php echo $tblRow; ?>" value="<?php echo $row['item_group_id']; ?>"/>
						<input type="hidden" name="hdnBalance[]" id="hdnBalance_<?php echo $tblRow; ?>" value="<?php echo $balance_for_update; ?>"/>
						<input type="hidden" name="productId[]" id="productId_<?php echo $tblRow; ?>" value="<?php echo $row['product_id']; ?>" />
						<input type="hidden" name="sectionId[]" id="sectionId_<?php echo $tblRow; ?>" value="<?php echo $row['section_id']; ?>" />
						<input type="hidden" name="trimsBreakId[]" id="trimsBreakId_<?php echo $tblRow; ?>" value="<?php echo $trim_break_id; ?>" />
						
					</td>
				</tr>
				<?
			}
		}
	}
	exit();
}
 
if ($action=="load_php_data_to_form")
{
	$nameArray=sql_select( "select id, subcon_job, company_id, location_id, party_id, currency_id, exchange_rate, party_location, delivery_date, rec_start_date, rec_end_date, receive_date, within_group, party_location, order_id, order_no, conv_factor,gmts_type from subcon_ord_mst where id='$data' and status_active=1" );
	foreach ($nameArray as $row)
	{
		$buyer_data=$row[csf("company_id")].'_'.$row[csf("within_group")].'_'.$row[csf("party_id")];
		//$floor_data=$row[csf("company_id")].'_'.$row[csf("location_id")];
		echo "document.getElementById('txt_job_no').value 			= '".$row[csf("subcon_job")]."';\n";  
		echo "document.getElementById('txt_job_id').value 			= '".$row[csf("id")]."';\n";  
		echo "document.getElementById('cbo_company_name').value 		= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_within_group').value 	= '".$row[csf("within_group")]."';\n";  
		echo "document.getElementById('cbo_location_name').value 		= '".$row[csf("location_id")]."';\n";  
		echo "load_drop_down( 'requires/raw_material_issue_requisition_controller', '".$buyer_data."', 'load_drop_down_buyer', 'buyer_td' );\n";
		echo "load_drop_down( 'requires/raw_material_issue_requisition_controller', '".$floor_data."', 'load_drop_down_floor', 'floor_td' );\n";
		echo "document.getElementById('hid_order_id').value          = '".$row[csf("order_id")]."';\n";
		echo "document.getElementById('txt_order_no').value         = '".$row[csf("order_no")]."';\n";
		echo "$('#txt_order_no').attr('disabled','true')".";\n";
		//echo "$('#cbo_party_name').attr('disabled','true')".";\n";
		echo "set_button_status(0,'".$_SESSION['page_permission']."', 'fnc_embel_entry',1);\n";	
	}
	exit();	
}


if($action=="show_fabric_desc_listview")
{
	//echo $data; die;
	//$data=explode('_',$data);

	/*$order_id=$data[0];
	$process_id=$data[1];
	$company_id=$data[3];*/
	//echo "select id, style_ref_no from fabric_sales_order_mst where po_id in ($data[0])";die;
	//$batch_arr=return_library_array( "select id, prod_id, item_description from lib_subcon_charge",'id','const_comp');	
	//$gsm_arr=return_library_array( "select id,gsm from lib_subcon_charge",'id','gsm');
	
	//$style_array=return_library_array( "select id, style_ref_no from fabric_sales_order_mst where id in ($data[0])",'id','style_ref_no');
	//$uom_array=return_library_array( "select id, unit_of_measure from product_details_master ",'id','unit_of_measure');

	/*$production_qty_array=array();
	$prod_sql="Select batch_id, cons_comp_id, sum(product_qnty) as product_qnty from subcon_production_dtls where batch_id='$data[2]' and status_active=1 and is_deleted=0 group by  batch_id, cons_comp_id";
	$prod_data_sql=sql_select($prod_sql);
	foreach($prod_data_sql as $row)
	{
		$production_qty_array[$row[csf('batch_id')]][$row[csf('cons_comp_id')]]=$row[csf('product_qnty')];
	}*/

	/*$buyer_po_arr=array();
	$po_sql ="Select a.style_ref_no, b.id, b.po_number,a.job_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
	$po_sql_res=sql_select($po_sql);
	foreach ($po_sql_res as $row)
	{
		$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
		$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
		$buyer_po_arr[$row[csf("id")]]['job']=$row[csf("job_no")];
	}
	unset($po_sql_res);*/
	//var_dump($production_qty_array);
	//$main_batch_allow = return_field_value("dyeing_fin_bill", "variable_settings_subcon", "company_id =$company_id and variable_list=13 and is_deleted=0 and status_active=1");
	//$entry_form_cond='';
	//if($main_batch_allow==1) $entry_form_cond=" and a.entry_form in(0,281) and a.process_id like '%35%' "; else $entry_form_cond="and a.entry_form =281 ";


	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$sql= "select a.id, a.subcon_job, a.job_no_prefix_num, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, a.order_id, a.delivery_date,b.buyer_po_no,b.id as po_id ,b.buyer_style_ref,b.gmts_item_id, b.gmts_color_id, b.buyer_po_id,b.order_quantity,b.order_uom
	from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c  
	where a.entry_form=295 and a.subcon_job=b.job_no_mst and a.id=b.mst_id and a.status_active=1 and b.status_active=1 and b.id=c.mst_id and a.id='$data' and c.process in(2,3)
	group by a.id, a.subcon_job, a.job_no_prefix_num, a.insert_date, a.company_id, a.location_id, a.party_id, a.receive_date, a.order_no, a.order_id, a.delivery_date,b.id,b.buyer_po_no,b.buyer_style_ref,b.gmts_item_id, b.gmts_color_id, b.buyer_po_id,b.order_quantity,b.order_uom
	order by a.id DESC";
	// and b.po_id in ($data[0]) group by a.batch_no, a.extention_no, a.color_id, b.id, b.prod_id, b.item_description 
	$data_array=sql_select($sql);
	?>
     <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="350">
         <thead>  	 	 	 	
            <th width="30">SL</th>
            <th width="60">Buyer Style</th>
            <th width="100">Buyer PO </th>
            <th width="70">Gmts Item</th>
            <th width="70">Gmts Color</th>
            <th>Order Qty</th>
        </thead>
        <tbody>
            <?php 
            $i=1;
            foreach($data_array as $row)
            {  
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				/*$excolor_id=array_unique(explode(",",$row[csf('color_id')]));
				$color_name="";	
				foreach ($excolor_id as $color_id)
				{
					if($color_name=="") $color_name=$color_arr[$color_id]; else $color_name.=','.$color_arr[$color_id];
				}
				$buyer_po=""; $buyer_style="";
				$buyer_po_id=explode(",",$row[csf('buyer_po_id')]);
				foreach($buyer_po_id as $po_id)
				{
					if($buyer_po=="") $buyer_po=$buyer_po_arr[$po_id]['po']; else $buyer_po.=','.$buyer_po_arr[$po_id]['po'];
					if($buyer_style=="") $buyer_style=$buyer_po_arr[$po_id]['style']; else $buyer_style.=','.$buyer_po_arr[$po_id]['style'];
				}
				$buyer_po=implode(",",array_unique(explode(",",$buyer_po)));
				$buyer_style=implode(",",array_unique(explode(",",$buyer_style)));*/
				if($row[csf('order_uom')]==2) $qty_pcs=$row[csf('order_quantity')]*12; else $qty_pcs=$row[csf('order_quantity')];
				
				
                ?>
                 <tr bgcolor="<?php echo $bgcolor; ?>" onClick='set_form_data("<?php echo $row[csf('po_id')]."**".$row[csf('buyer_style_ref')]."**".$row[csf('buyer_po_no')]."**".$row[csf('buyer_po_id')]."**".$row[csf('order_no')]."**".$garments_item[$row[csf('gmts_item_id')]]."**".$color_arr[$row[csf('gmts_color_id')]]."**".$qty_pcs."**".$row[csf('order_id')]; ?>")' style="cursor:pointer" >
                    <td width="30"><?php echo $i; ?></td>
                    <td width="60"><?php echo $row[csf('buyer_style_ref')]; ?></td>
                    <td width="120"><?php echo $row[csf('buyer_po_no')]; ?></td>
                    <td width="80" ><?php echo $garments_item[$row[csf('gmts_item_id')]]; ?></td>
                    <td width="80" ><?php echo $color_arr[$row[csf('gmts_color_id')]]; ?></td>
                    <td ><?php echo $qty_pcs; ?></td>
                </tr>
				<?php 
                $i++; 
            }  
            ?>
        </tbody>
    </table>
<?php    
	exit();
}


if ($action=="dry_production_list_view")
{
	$data=explode('_',$data);
	?>	
	<div style="width:100%;">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="90" align="center">Process Name</th>
                <th width="120" align="center">Wash Type</th>
                <th width="80" align="center">Order Qty</th>
                <th width="80" align="center">Production Qty (Pcs)</th>                    
                <th align="center">Reject Qty (Pcs)</th>
            </thead>
        </table>
    </div>
    <div style="width:100%;max-height:180px; overflow:y-scroll" id="sewing_production_list_view" align="left">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
        <?php  
			$i=1;
			/*$main_batch_allow = return_field_value("dyeing_fin_bill", "variable_settings_subcon", "company_id =$data[1] and variable_list=13 and is_deleted=0 and status_active=1");
			$entry_form_cond='';
			if($main_batch_allow==1) $entry_form_cond=" entry_form in(0,281) and process_id like '%35%'"; else $entry_form_cond=" entry_form =281 ";*/

			$color_arr=return_library_array( "select id,color_name from lib_color",'id','color_name');
			
			$order_sql= "select b.id , b.buyer_po_no ,b.buyer_style_ref,b.gmts_item_id, b.gmts_color_id, b.buyer_po_id,b.order_quantity from subcon_ord_dtls b where b.mst_id='$data[1]' group by b.id,b.buyer_po_no,b.buyer_style_ref,b.gmts_item_id, b.gmts_color_id, b.buyer_po_id,b.order_quantity";
			
			$order_sql_result=sql_select($order_sql); $order_array=array();
			
			foreach ($order_sql_result as $row)
			{
				$order_array[$row[csf("id")]]["po_id"]=$row[csf("po_id")];
				$order_array[$row[csf("id")]]["buyer_po_no"]=$row[csf("buyer_po_no")];
				$order_array[$row[csf("id")]]["buyer_style_ref"]=$row[csf("buyer_style_ref")];
				$order_array[$row[csf("id")]]["gmts_item_id"]=$row[csf("gmts_item_id")];
				$order_array[$row[csf("id")]]["gmts_color_id"]=$row[csf("gmts_color_id")];
				$order_array[$row[csf("id")]]["buyer_po_id"]=$row[csf("buyer_po_id")];
				$order_array[$row[csf("id")]]["order_quantity"]=$row[csf("order_quantity")];
			}
			//print_r($order_array); die;
			$pdate_cond=($db_type==2)? " TO_CHAR(production_hour,'HH24:MI') " : " TIME_FORMAT( production_hour, '%H:%i' ) ";
			$sql="select id, mst_id,po_id, color_size_id, issue_date, $pdate_cond as production_hour, qcpass_qty, reje_qty,operator_name, shift_id, remarks, process_id, wash_type_id, order_qty from trims_raw_mat_requisition_dtls where status_active=1 and is_deleted=0 and mst_id='$data[0]' order by id ASC";
			
			//$machine_arr=return_library_array( "raw_material_issue_requisition_controller.php"; 
			$sql_result =sql_select($sql);
			foreach($sql_result as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					
				/*$process_id=explode(',',$row[csf('process')]);
				$process_val='';
				foreach ($process_id as $val)
				{
					if($process_val=='') $process_val=$conversion_cost_head_array[$val]; else $process_val.=",".$conversion_cost_head_array[$val];
				}

				if($main_batch_allow==1)
				{
					$within_group=1;
				}
				else
				{
					$within_group=$batch_array[$key]['within_group'];
				}*/

				$po_id=$row[csf('po_id')];
				//$order_array[$po_id]['po_id'];
				$buyer_po_no=$order_array[$po_id]['buyer_po_no'];
				$buyer_style_ref=$order_array[$po_id]['buyer_style_ref'];
				$gmts_item_id=$order_array[$po_id]['gmts_item_id'];
				$gmts_color_id=$order_array[$po_id]['gmts_color_id'];
				$buyer_po_id=$order_array[$po_id]['buyer_po_id'];
				$order_quantity=$order_array[$po_id]['order_quantity'];

				$click_data=$po_id."**".$buyer_style_ref."**".$buyer_po_no."**".$buyer_po_id."**".' '."**".$garments_item[$gmts_item_id]."**".$color_arr[$gmts_color_id]."**".$order_quantity."**".$row[csf('process_id')]."**".$row[csf('wash_type_id')]."**".$row[csf('qcpass_qty')]."**".$row[csf('reje_qty')]."**".$row[csf('remarks')]."**".$row[csf('id')];

				if($row[csf('process_id')]==2) $typeArray=$wash_dry_process; else  $typeArray=$wash_laser_desing;
				?>
                 <tr bgcolor="<?php echo $bgcolor; ?>" onClick='set_form_data_update("<?php echo $click_data; ?>")' style="cursor:pointer" >
                    <td width="30" align="center"><?php echo $i; ?></td>
                    <td width="90" align="center"><p><?php echo $wash_type[$row[csf('process_id')]]; ?></p></td>
                    <td width="120" align="center"><p><?php echo $typeArray[$row[csf('wash_type_id')]]; ?></p></td>
                    <td width="80" align="center"><p><?php echo $row[csf('order_qty')]; ?></p></td>
                    <td width="80" align="center"><p><?php echo $row[csf('qcpass_qty')]; ?></p></td>
                    <td  align="center"><p><?php echo $row[csf('reje_qty')]; ?></p></td>
                </tr>
			<?php
            $i++;
        }
        ?>
        </table>
	</div>
	<?	
}


if ($action=="load_mst_php_data_to_form")
{
	//echo "select id, trims_job, company_id, location_id, party_id, currency_id, within_group, delivery_date, party_location, entry_form,  order_id, order_no, order_qty, received_no, received_id, section_id from trims_job_card_mst  where entry_form=257 and id=$data and status_active=1 order by id DESC"; die;

	$data = explode('**', $data);
    $reqType = $data[0];
    $reqMstId = $data[1];
    $nameArray = array();
    $targetQty = array();

    if ($reqType == 1) {
    	$nameArray=sql_select("select id, trims_job, company_id, location_id, party_id, currency_id, within_group, delivery_date, party_location, entry_form, order_id, order_no, order_qty, received_no, received_id, section_id 
	 		from trims_job_card_mst where entry_form=257 and id=$reqMstId and status_active=1 order by id desc");
    	//echo "select sum(job_quantity) as ord_qty, uom from trims_job_card_dtls where mst_id=$reqMstId and status_active=1 group by job_quantity, uom";
		$targetQty = sql_select("select sum(job_quantity) as ord_qty, uom from trims_job_card_dtls where mst_id=$reqMstId and status_active=1 group by uom");
    } else {
    	$nameArray=sql_select("select a.id, a.requisition_no, a.company_id, a.location_id, a.issue_date, a.issue_basis, a.section_id, a.order_id, a.job_id, a.job_no, a.target_prod_qty, a.uom_id, a.store_id, b.order_no
				from trims_raw_mat_requisition_mst a, trims_job_card_mst b
				where a.entry_form=427 and a.id=$reqMstId and a.status_active=1 and b.status_active=1 and a.job_no=b.trims_job");
    }

    /*echo "select a.id, a.requisition_no, a.company_id, a.location_id, a.issue_date, a.issue_basis, a.section_id, a.order_id, a.job_id, a.job_no, a.target_prod_qty, a.uom_id, a.store_id, b.order_no
				from trims_raw_mat_requisition_mst a, trims_job_card_mst b
				where a.entry_form=427 and a.id=$reqMstId and a.status_active=1 and b.status_active=1 and a.job_no=b.trims_job";*/

    // echo "select id, company_id, location_id, issue_date, issue_basis, section_id, order_id, job_id, job_no, target_prod_qty, uom_id, received_id, store_id 
	 		// from trims_job_card_mst where entry_form=427 and id=$reqMstId and status_active=1";
	
	if ($reqType==1) {
		echo "document.getElementById('hid_job_id').value 				= '".$nameArray[0][csf("id")]."';\n";
		echo "document.getElementById('txt_job_no').value 				= '".$nameArray[0][csf("trims_job")]."';\n";
		echo "document.getElementById('cbo_company_name').value 		= '".$nameArray[0][csf("company_id")]."';\n";
		// echo "fnc_load_party(1,".$nameArray[0][csf("within_group")].");\n";	
		echo "document.getElementById('cbo_location_name').value 		= '".$nameArray[0][csf("location_id")]."';\n";
		echo "load_drop_down('requires/raw_material_issue_requisition_controller', '".$nameArray[0][csf("company_id")]."_".$nameArray[0][csf("location_id")]."', 'load_drop_down_store', 'store_td');";
		// echo "document.getElementById('cbo_party_name').value			= '".$nameArray[0][csf("party_id")]."';\n";
		echo "document.getElementById('cbo_section').value			= '".$nameArray[0][csf("section_id")]."';\n";
		echo "document.getElementById('hid_order_id').value          	= '".$nameArray[0][csf("order_id")]."';\n";
		echo "document.getElementById('txt_order_no').value         	= '".$nameArray[0][csf("order_no")]."';\n";
		echo "$('#cbo_company_name').attr('disabled','true')".";\n";
		echo "$('#txt_order_no').attr('disabled','true')".";\n";
		echo "$('#cbo_within_group').attr('disabled','true')".";\n";
		echo "document.getElementById('txt_targeted_prod_qty').value         	= '".$targetQty[0][csf("ord_qty")]."';\n";
		echo "document.getElementById('cbo_uom').value         	= '".$targetQty[0][csf("uom")]."';\n";
		//echo "document.getElementById('txt_recv_no').value 				= '".$nameArray[0][csf("received_no")]."';\n";
		//echo "document.getElementById('hid_recv_id').value 				= '".$nameArray[0][csf("received_id")]."';\n";
		//echo "load_drop_down( 'requires/raw_material_issue_requisition_controller', document.getElementById('cbo_company_name').value, 'load_drop_down_location', 'location_td' );\n";
		//echo "load_drop_down( 'requires/raw_material_issue_requisition_controller', document.getElementById('cbo_company_name').value, 'load_drop_down_buyer', 'buyer_td' );\n";
		//cbo_company_name cbo_location_name cbo_within_group cbo_party_name cbo_party_location txt_delivery_date cbo_section txt_order_no txt_order_qty
		//echo "fnc_load_party(2,".$nameArray[0][csf("within_group")].");\n";	 
		//echo "document.getElementById('cbo_party_location').value		= '".$nameArray[0][csf("party_location")]."';\n";	
		//echo "document.getElementById('txt_delivery_date').value		= '".change_date_format($nameArray[0][csf("delivery_date")])."';\n";  
		//echo "document.getElementById('txt_order_qty').value         	= '".$nameArray[0][csf("order_qty")]."';\n";
		//echo "document.getElementById('cbo_section').value        		= '".$nameArray[0][csf("section_id")]."';\n";
		//echo "$('#cbo_party_name').attr('disabled','true')".";\n";
		//echo "document.getElementById('update_id').value          		= '".$nameArray[0][csf("id")]."';\n";	
		//echo "set_button_status(1,'".$_SESSION['page_permission']."', 'fnc_job_order_entry',1);\n";	
	} else {
		echo "document.getElementById('update_id').value 				= '".$nameArray[0][csf("id")]."';\n";
		echo "document.getElementById('txt_job_no').value 				= '".$nameArray[0][csf("job_no")]."';\n";
		echo "document.getElementById('cbo_company_name').value 		= '".$nameArray[0][csf("company_id")]."';\n";
		echo "load_drop_down('requires/raw_material_issue_requisition_controller', '".$nameArray[0][csf("company_id")]."', 'load_drop_down_location', 'location_td');";
		echo "document.getElementById('cbo_location_name').value 		= '".$nameArray[0][csf("location_id")]."';\n";
		echo "load_drop_down('requires/raw_material_issue_requisition_controller', '".$nameArray[0][csf("company_id")]."_".$nameArray[0][csf("location_id")]."', 'load_drop_down_store', 'store_td');";
		// echo "document.getElementById('cbo_party_name').value			= '".$nameArray[0][csf("party_id")]."';\n";
		echo "document.getElementById('cbo_section').value			= '".$nameArray[0][csf("section_id")]."';\n";
		echo "document.getElementById('hid_order_id').value          	= '".$nameArray[0][csf("order_id")]."';\n";
		echo "document.getElementById('txt_order_no').value         	= '".$nameArray[0][csf("order_no")]."';\n";
		echo "document.getElementById('txt_production_id').value         	= '".$nameArray[0][csf('requisition_no')]."';\n";
		echo "document.getElementById('txt_targeted_prod_qty').value         	= '".$nameArray[0][csf('target_prod_qty')]."';\n";
		echo "document.getElementById('cbo_uom').value         	= '".$nameArray[0][csf('uom_id')]."';\n";
		echo "document.getElementById('cbo_store_name').value         	= '".$nameArray[0][csf('store_id')]."';\n";
		echo "$('#cbo_company_name').attr('disabled','true')".";\n";
		echo "$('#txt_order_no').attr('disabled','true')".";\n";
		echo "$('#cbo_within_group').attr('disabled','true')".";\n";
	}

	exit();	
}

function return_field_value_2($field_name, $table_name, $query_cond, $return_fld_name, $new_conn) // checked 3
{
	// This function will Return Single or Multiple field value
	// concated with seperator having only one row result
	//Return value:  query result as filed value
	// Uses  single field:: return_field_value("buyer_name", "lib_buyer", "id=1");
	// Uses  multi field:: return_field_value("concate(buyer_name,'_',contact_person)", "lib_buyer", "id=1"); do not use concat
	if ($return_fld_name == "") {
		$return_fld_name = $field_name;
	}

	$queryText = "select " . $field_name . " from " . $table_name . " where " . $query_cond . " ";
	return $queryText;
	$nameArray = sql_select($queryText, '', $new_conn);
	foreach ($nameArray as $result) {
		if ($result[csf($return_fld_name)] != "") {
			return $result[csf($return_fld_name)];
		} else {
			return false;
		}
	}

	//die;
}

?>