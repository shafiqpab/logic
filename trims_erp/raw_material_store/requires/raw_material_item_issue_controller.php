<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');


if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
//$user_id = $_SESSION['logic_erp']["user_id"];
//========== user credential start ========
$user_id = $_SESSION['logic_erp']['user_id'];
$userCredential = sql_select("SELECT unit_id as company_id,company_location_id,store_location_id,item_cate_id,supplier_id FROM user_passwd where id='$user_id'");
$company_id = $userCredential[0][csf('company_id')];
$supplier_id = $userCredential[0][csf('supplier_id')];
$store_location_id = $userCredential[0][csf('store_location_id')];
$item_cate_id = $userCredential[0][csf('item_cate_id')];
$company_location_id = $userCredential[0][csf('company_location_id')];

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

//============================================================================

if($db_type==2 || $db_type==1 )
{
	$mrr_date_check="and to_char(insert_date,'YYYY')=".date('Y',time())."";
	$concat="";
	$concat_coma="||";
}
else if ($db_type==0)
{
	$mrr_date_check="and year(insert_date)=".date('Y',time())."";
	$concat="concat";
	$concat_coma=",";

}

//--------------------------------------------------------------------------------------------
$trim_group_arr = return_library_array("select id, trim_uom from lib_item_group","id","trim_uom"); 
$machine_arr = return_library_array("select id, machine_no from lib_machine_name","id","machine_no");

//load drop down company location
if ($action=="load_drop_down_location")
{
	//load_room_rack_self_bin('requires/raw_material_item_issue_controller*4_8_9_10_11_15_16_17_18_19_20_21_22_32_33_34_35_36_37_38_39_40_41_44_45_46_47_48_49_50_51_52_53_54_55_56_57_58_59_60_61_62_63_64_65_66_67_68_69_70_89_90_91_92_93_94_101', 'store','store_td',this.value,'','','','','','','','check_stock(this.value)');
	//echo "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' $company_location_credential_cond order by location_name";

	//echo "SELECT unit_id as company_id,company_location_id,store_location_id,item_cate_id,supplier_id FROM user_passwd where id='$user_id'";
	echo create_drop_down( "cbo_location", 152, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' $company_location_credential_cond order by location_name","id,location_name", 1, "-- Select --", $selected, "enable_disable_loc();load_room_rack_self_bin('requires/raw_material_item_issue_controller*101', 'store','store_td',$('#cbo_company_id').val(), this.value,'','','','','','','','check_stock(this.value)');",0 );
	exit();
}
if ($action=="load_drop_down_buyer")
{
		echo create_drop_down( "cbo_party_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", "", "" );
	
	exit();	 
} 

if ($action=="load_drop_down_buyer_one")
{
	$data=explode("_",$data);
	
	if($data[1]==1) $load_function="fnc_load_party(2,document.getElementById('cbo_within_group').value)";
	else $load_function="";
	
	if($data[1]==1)
	{
		echo create_drop_down( "cbo_party_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $data[2], "$load_function");
	}
	else
	{
		echo create_drop_down( "cbo_party_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $data[2], "" );
	}	
	exit();	 
} 


if ($action=="load_drop_down_location_popup")
{
	echo create_drop_down( "cbo_location_name", 90, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' $company_location_credential_cond order by location_name","id,location_name", 1, "-- Select --", $selected, "",0 );
	exit();
}

if ($action=="load_drop_down_loan_party")
{
	echo create_drop_down( "cbo_loan_party", 170, "select a.id, a.supplier_name from lib_supplier a, lib_supplier_tag_company b
	where a.id=b.supplier_id and b.tag_company=$data and a.status_active=1 and a.is_deleted=0 and a.id in(select supplier_id from lib_supplier_party_type where party_type=91) order by supplier_name","id,supplier_name", 1, "- Select Loan Party -", $selected, "","","" );
	exit();
}

//load drop down knitting company
if($action == "load_drop_down_issue_to"){
	$exDataArr = explode("**", $data);
	$issue_source = $exDataArr[0];
	$company = $exDataArr[1];
	$issuePurpose = $exDataArr[2];
	if ($company == "" || $company == 0) $company_cod = ""; else $company_cod = " and id=$company";

	if ($issue_source == 1)
		echo create_drop_down("cbo_issue_to", 170, "select id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name", "id,company_name", 1, "-- Select --", "", "");

	else if ($issue_source == 3 && $issuePurpose == 1)
		echo create_drop_down("cbo_issue_to", 170, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b, lib_supplier_tag_company c where a.id=b.supplier_id and a.id=c.supplier_id and c.tag_company='$company' and b.party_type in(1,9,20) and a.status_active=1 group by a.id, a.supplier_name order by a.supplier_name", "id,supplier_name", 1, "-- Select --", 0, "", 0);
	else if ($issue_source == 3 && $issuePurpose == 2)
		echo create_drop_down("cbo_issue_to", 170, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b, lib_supplier_tag_company c where a.id=b.supplier_id and a.id=c.supplier_id and c.tag_company='$company' and b.party_type in(1,9,21,24) and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id,supplier_name", 1, "-- Select --", 0, "", 0);
    else if ($issue_source == 3 && $issuePurpose == 15)
        echo create_drop_down("cbo_issue_to", 170, "SELECT a.id, a.buyer_name FROM lib_buyer a, lib_buyer_party_type  b, lib_buyer_tag_company c WHERE a.id = b.buyer_id AND a.id = c.buyer_id AND c.tag_company = $company AND b.party_type IN (80) AND a.status_active = 1 GROUP BY a.id, a.buyer_name ORDER BY a.buyer_name", "id,buyer_name", 1, "-- Select --", 0, "", 0);
	else if ($issue_source == 3)
		echo create_drop_down("cbo_issue_to", 170, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b, lib_supplier_tag_company c where a.id=b.supplier_id and a.id=c.supplier_id and c.tag_company='$company' and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id,supplier_name", 1, "-- Select --", 0, "", 0);
	else if ($issue_source == 0)
		echo create_drop_down("cbo_issue_to", 170, $blank_array, "", 1, "-- Select --", $selected, "", "", "");
	exit();

}

if ($action=="load_drop_down_itemgroupPop")
{
	echo create_drop_down( "cbo_item_group", 180, "select id,item_name from lib_item_group where item_category=$data and status_active=1 and is_deleted=0 order by item_name","id,item_name", 1, "-- Select --", 0, "","" );
	exit();
}
if ($action=="load_drop_down_store_up")
{
	$data=explode("**",$data);
	//echo "select a.id,a.store_name from lib_store_location a, lib_store_location_category b where a.id=b.store_location_id and b.category_type=$data[1] $store_location_credential_cond and a.status_active=1 and a.is_deleted=0 and a.company_id=$data[0] group by a.id,a.store_name order by a.store_name";die;
	echo create_drop_down( "cbo_store_name", 152, "select a.id,a.store_name from lib_store_location a, lib_store_location_category b where a.id=b.store_location_id and b.category_type=$data[1] $store_location_credential_cond and a.status_active=1 and a.is_deleted=0 and a.company_id=$data[0] group by a.id,a.store_name order by a.store_name","id,store_name", 1, "Select Store", 0, "","" );
	exit();
}

if ($action=="load_drop_down_floor")
{
	$data=explode("_",$data);
	$company_id=$data[0];
	$location_id=$data[1];
	$machine_category=$data[2];
	if($location_id==0 || $location_id=="") $location_cond=""; else $location_cond=" and b.location_id=$location_id";
	if($machine_category==0 || $machine_category=="") $category_cond=""; else $category_cond=" and b.category_id=$machine_category";

	//echo "select a.id, a.floor_name from lib_prod_floor a, lib_machine_name b where a.id=b.floor_id and b.company_id=$company_id and b.status_active=1 and b.is_deleted=0 $location_cond $category_cond  group by a.id, a.floor_name order by a.floor_name";die;

	echo create_drop_down( "cbo_floor", 130, "select a.id, a.floor_name from lib_prod_floor a, lib_machine_name b where a.id=b.floor_id and b.company_id=$company_id and b.status_active=1 and b.is_deleted=0 $location_cond $category_cond  group by a.id, a.floor_name order by a.floor_name","id,floor_name", 1, "-- Select Floor --", 0, "load_drop_down( 'requires/raw_material_item_issue_controller',document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_machine_category').value+'_'+this.value, 'load_drop_machine', 'machine_td' );load_drop_down( 'requires/raw_material_item_issue_controller', this.value+'_'+$company_id+'_'+$location_id, 'load_drop_down_sewing_line_floor', 'sewing_line_td' );valid_floor(1);","" );
  exit();
}

//  line drop down
if($action=="load_drop_down_sewing_line_floor")
{
	$explode_data = explode("_",$data);
	//print_r($explode_data);
	//echo "select id,line_name from lib_sewing_line where  is_deleted=0 and status_active=1 and floor_name!=0 and floor_name = $explode_data[0] and company_name=$explode_data[1] and location_name=$explode_data[2] order by line_name";//die;
	echo create_drop_down( "cbo_sewing_line", 120, "select id,line_name from lib_sewing_line where  is_deleted=0 and status_active=1 and floor_name!=0 and floor_name = $explode_data[0] and company_name=$explode_data[1] and location_name=$explode_data[2] order by line_name","id,line_name", 1, "--- Select ---", $selected, "valid_line();",0,0 );
	exit();

}


if ($action=="load_drop_machine")
{
	$data=explode("_",$data);
	$company_id=$data[0];
	$machine_category=$data[1];
	$floor_id=$data[2];
	if($machine_category==0 || $machine_category=="") $machine_cond=""; else $machine_cond=" and category_id=$machine_category";
	if($floor_id==0 || $floor_id=="") $floor_cond=""; else $floor_cond=" and floor_id=$floor_id";

	echo create_drop_down( "cbo_machine_name", 135, "select id, machine_no as machine_name from lib_machine_name where  company_id=$company_id and status_active=1 and is_deleted=0 and is_locked=0 $floor_cond $machine_cond order by machine_no","id,machine_name", 1, "-- Select Machine --", 0, "valid_machine(1);","" );
	exit();
}

//load drop down company department
if ($action=="load_drop_down_department")
{
	//echo "select a.id,a.department_name from  lib_department a, lib_division b where b.id=a.division_id and a.status_active =1 and a.is_deleted=0 and b.company_id='$data' order by department_name";die;
	echo create_drop_down( "cbo_department", 150, "select a.id,a.department_name from  lib_department a, lib_division b where b.id=a.division_id and a.status_active =1 and a.is_deleted=0 and b.company_id='$data' order by department_name","id,department_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/raw_material_item_issue_controller', this.value, 'load_drop_down_section', 'section_td' );",0 );
	exit();
}

//load drop down company section
if ($action=="load_drop_down_section")
{
	echo create_drop_down( "cbo_section", 152, "select id,section_name from lib_section where status_active =1 and is_deleted=0 and department_id='$data' order by section_name","id,section_name", 1, "-- Select --", $selected, "",0 );
	exit();
}
if ($action=="load_room_rack_self_bin")
	{
		load_room_rack_self_bin("requires/raw_material_item_issue_controller",$data);
	}
//load drop down store
/*if ($action=="load_drop_down_store")
{
	//echo create_drop_down( "cbo_store_name", 130, "select a.id,a.store_name from lib_store_location a, lib_store_location_category b where a.id=b.store_location_id and b.category_type in (8,9,10,11) and a.status_active=1 and a.is_deleted=0 and FIND_IN_SET($data,a.company_id) group by a.id order by a.store_name","id,store_name", 1, "-- Select --", "", "","" );

	echo create_drop_down( "cbo_store_name", 130, "select a.id,a.store_name from lib_store_location a, lib_store_location_category b where a.id=b.store_location_id and b.category_type in ( $item_cate_credential_cond ) and a.status_active=1 and a.is_deleted=0 and a.company_id=$data $store_location_credential_cond group by a.id,a.store_name order by a.store_name","id,store_name", 1, "-- Select --", $selected, "","" );
	exit();
}*/

//load drop down item group
if ($action=="load_drop_down_itemgroup")
{
	echo create_drop_down( "cbo_item_group", 150, "select id,item_name from lib_item_group where item_category=$data and status_active=1 and is_deleted=0 order by item_name","id,item_name", 1, "-- Select --", 0, "load_drop_down( 'requires/raw_material_item_issue_controller', this.value, 'load_drop_down_uom', 'uom_td' );","" );
	exit();
}

//load drop down uom
if ($action=="load_drop_down_uom")
{
	if($data==0) $uom=0; else $uom=$trim_group_arr[$data];
	echo create_drop_down( "cbo_uom", 130, $unit_of_measurement, "", 1, "-- Select --", $uom , "", 1);
	exit();
}

if ($action=="item_description_popup")
{
	echo load_html_head_contents("Item popup", "../../../", 1, 1,'','1','');
	extract($_REQUEST);
	$item_cat=str_replace("'","",$item_cat);

	//echo $item_cat.jahid;die;

	?>
	<script>
		function js_set_value(item_description)
		{
			$("#item_description_all").val(item_description);
			//$("#item_description_all").val('lktoilix sdoi;f il;of opod loiioo;potg09p pgsaos 1205 050');
			parent.emailwindow.hide();
		}
		function open_itemCode_popup()
		{
			if( form_validation('cbo_item_category','Item Category Name')==false )
			{
				return;
			}
			var cbo_item_category = $("#cbo_item_category").val();
			var page_link="raw_material_item_issue_controller.php?action=item_code_popup&cbo_item_category="+cbo_item_category;
			var title="Item Code Popup";
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=250px,center=1,resize=1,scrolling=0','../../')
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var item_cote_all=this.contentDoc.getElementById("item_id").value;//alert(item_description_all);
				var splitArr = item_cote_all.split("_");
				$("#hide_product_id").val(splitArr[0]);
				$("#txt_item_code").val(splitArr[1]);
			}
		}
	</script>
	</head>

	<body>
	<div align="center" style="width:100%;" >
	<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
		<table width="880" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
			<thead>
				<tr>
					<th width="230" class="must_entry_caption">Item Category</th>
					<th width="230">Item Group</th>
					<th width="180" style="display:none">Store Name</th>
					<th width="180" >Description</th>
					<th width="130">Product Id</th>
					<th width="130">Item Code</th>
					<th ><input type="reset" name="re_button" id="re_button" value="Reset" style="width:90px" class="formbutton"  /></th>
				</tr>
			</thead>
			<tbody>
				<tr class="general">
					<td>
						<?

						//echo create_drop_down( "cbo_item_category", 180, $item_category,"", 1, "-- Select --", $item_cat, "load_drop_down( 'raw_material_item_issue_controller', this.value, 'load_drop_down_itemgroupPop', 'item_group_td' );load_drop_down( 'raw_material_item_issue_controller', $company_id+'**'+this.value, 'load_drop_down_store_up', 'store_td' );", 0,$item_cat);
						//echo $item_cate_credential_cond;
						echo create_drop_down( "cbo_item_category", 180, $item_category,"", 1, "-- Select --", 0, "load_drop_down( 'raw_material_item_issue_controller', this.value, 'load_drop_down_itemgroupPop', 'item_group_td' );load_drop_down( 'raw_material_item_issue_controller', $company_id+'**'+this.value, 'load_drop_down_store_up', 'store_td' );", 0,"101" );

						?>
					</td>
					<td width="" align="center" id="item_group_td">
						<?
							//$search_by = array(1=>'Return Number');
							//$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";
							echo create_drop_down( "cbo_item_group", 180, $blank_array, "", 1, "-- Select --", 0, "", 0,"" );
						?>
					</td>
					<td align="center" id="store_td"  style="display:none">
						<?
							//$company_id=str_replace("'","",$company_id);
							echo create_drop_down( "cbo_store_name", 180, $blank_array, "", 1, "-- Select --", 0, "", 0,"" );
						?>
					</td>
					<td align="center" >
						<input type="text" id="txt_description" name="txt_description" style="width:100px;" class="text_boxes">
					</td>
					<td align="center">
						<input type="text" id="txt_product_id" name="txt_product_id" style="width:100px;" class="text_boxes">
					</td>
					<td align="center">
						<input type="text" id="txt_item_code" name="txt_item_code" style="width:100px;" class="text_boxes" placeholder="Browse Or Write" onDblClick="open_itemCode_popup();">
						<input type="hidden" id="hide_product_id" name="hide_product_id" >
					</td>
					<td align="center">
						<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_item_category').value+'_'+document.getElementById('cbo_item_group').value+'_'+document.getElementById('cbo_store_name').value+'_'+<? echo $company_id; ?>+'_'+document.getElementById('txt_item_code').value+'_'+document.getElementById('txt_product_id').value+'_'+<? echo $cbo_store_name; ?>+'_'+document.getElementById('txt_description').value, 'create_item_search_list_view', 'search_div', 'raw_material_item_issue_controller', 'setFilterGrid(\'tbl_serial\',-1)')" style="width:90px;" />
					</td>
				</tr>
			</tbody>
		</table>
		<br>
		<div align="center" valign="top" id="search_div"> </div>
	</form>
	</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
    <?

}

if ($action=="create_item_search_list_view")
{

	?>
    <table width="980" cellspacing="0" cellpadding="0" border="1" class="rpt_table" id="" rules="all" >
        <thead>
			<tr>
				<th colspan="10"><?php echo create_drop_down("cbo_string_search_type", 130, $string_search_type, '', 1, "-- Searching Type --",4); ?></th>
			</tr>
            <tr>
                <th width="30">SL No</th>
                <th width="50">Prod Id</th>
                <th width="110">Category</th>
                <th width="110">Group</th>
                <th width="100">Sub Group</th>
                <th width="80">Item Code</th>
                <th width="180">Description</th>
                <th width="100">Section</th>
                <th width="110">Store Name</th>
                <th >Current Stock</th>
            </tr>
        </thead>
    </table>
    <div style="width:980px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;" id="scroll_body">
    <table width="962" cellspacing="0" cellpadding="0" border="1" class="rpt_table" id="tbl_serial" rules="all">
        <tbody>
            <?
            $ex_data = explode("_",$data);
            $item_category_sql = $ex_data[0];
            $item_group = $ex_data[1];
            /* ####  this field not in use now*/
            $store_name = 0;
			/* ####  this field not in use now*/
            $company = $ex_data[3];
			$item_code_name = str_replace("'","",$ex_data[4]);
			$txt_prod_id = str_replace("'","",$ex_data[5]);
			$store_id = $ex_data[6];
			$txt_description = $ex_data[7];

			$entry_cond="";
			if(str_replace("'","",$item_category_sql)==101) $entry_cond="and b.entry_form=334";
            if ($item_category_sql!=0) $item_category_sql=" and a.item_category=$item_category_sql and b.item_category_id=$item_category_sql"; else { echo "Please Select item category."; die; };
			//echo $item_category;
            if( $item_group!=0 )  $item_group=" and b.item_group_id='$item_group'"; else $item_group="";
            if( $store_name!=0 )  $store_name=" and a.store_id='$store_name'"; else $store_name="";
			if( $item_code_name!="" )  $item_code_cond=" and b.item_code='$item_code_name'"; else $item_code_cond="";
			if( $txt_prod_id!="" )  $prod_cond=" and b.id='$txt_prod_id'"; else $prod_cond="";
			if( $store_id>0 )  $store_cond=" and a.store_id='$store_id'"; else $store_cond="";
			if( $txt_description!="" )  $des_cond=" and b.item_description like '%$txt_description%'"; else $des_cond="";
			//echo $store_cond.jahid;die;

            //echo $company;die;
            /*$sql="select a.store_id, b.id as id, sum(case when a.transaction_type in(1,4) then a.balance_qnty else 0 end) as receive, sum(case when a.transaction_type=2 then a.balance_qnty else 0 end) as issue, a.item_category, b.item_group_id,b.sub_group_name,$concat(b.item_description $concat_coma ',' $concat_coma b.item_size) as des, a.store_id
            from  inv_transaction a, product_details_master b
            where a.prod_id=b.id and a.company_id=$company $item_category_sql $item_group $store_name
			group by
					a.prod_id, b.id, b.item_group_id,b.sub_group_name,b.item_description, b.item_size, a.store_id, a.item_category";


			$sql="select  b.id as id, (sum(case when a.transaction_type in(1,4,5) then a.cons_quantity else 0 end) - sum(case when a.transaction_type in(2,3,6) then a.cons_quantity else 0 end)) as balance_stock, sum(case when a.transaction_type in(1,4,5) then a.balance_qnty else 0 end) as receive, sum(case when a.transaction_type in(2,3,6) then a.balance_qnty else 0 end) as issue, b.current_stock, b.item_category_id, b.item_group_id,b.sub_group_name,$concat(b.item_description $concat_coma ',' $concat_coma b.item_size) as des, a.store_id as store_id,b.item_code
            from  inv_transaction a, product_details_master b
            where a.prod_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company $item_category_sql $item_group $store_name $item_code_cond $entry_cond $prod_cond  $store_cond
			group by
					 a.store_id,b.id,b.item_category_id,b.item_group_id,b.sub_group_name,b.item_description, b.item_size,b.current_stock,b.item_code
			having sum(case when a.transaction_type in(1,4,5) then a.cons_quantity else 0 end) - sum(case when a.transaction_type in(2,3,6) then a.cons_quantity else 0 end)>0";
            //echo $sql;*/

			/*$sql="select  b.id as id, (sum(case when a.transaction_type in(1,4,5) then a.cons_quantity else 0 end) - sum(case when a.transaction_type in(2,3,6) then a.cons_quantity else 0 end)) as balance_stock, sum(case when a.transaction_type in(1,4,5) then a.balance_qnty else 0 end) as receive, sum(case when a.transaction_type in(2,3,6) then a.balance_qnty else 0 end) as issue, b.current_stock, b.item_category_id, b.item_group_id,b.sub_group_name, $concat(b.item_description $concat_coma ',' $concat_coma b.item_size) as des, a.store_id as store_id,b.item_code,b.brand_name,b.origin
            from  inv_transaction a, product_details_master b
            where a.prod_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company $item_category_sql $item_group $store_name $item_code_cond $entry_cond $prod_cond  $store_cond
			group by
					 a.store_id,b.id,b.item_category_id,b.item_group_id,b.sub_group_name,b.item_description, b.item_size,b.current_stock,b.item_code,b.brand_name,b.origin
			having sum(case when a.transaction_type in(1,4,5) then a.cons_quantity else 0 end) - sum(case when a.transaction_type in(2,3,6) then a.cons_quantity else 0 end)>0";*/

			/*.............. new dev.................*/
			 $sql="select  b.id as id, (sum(case when a.transaction_type in(1,4,5) then a.cons_quantity else 0 end) - sum(case when a.transaction_type in(2,3,6) then a.cons_quantity else 0 end)) as balance_stock, sum(case when a.transaction_type in(1,4,5) then a.balance_qnty else 0 end) as receive, sum(case when a.transaction_type in(2,3,6) then a.balance_qnty else 0 end) as issue, b.current_stock, b.item_category_id, b.item_group_id,b.sub_group_name, $concat(b.item_description $concat_coma ',' $concat_coma b.item_size) as des, a.store_id as store_id,b.item_code,b.brand_name,b.origin,b.model,b.order_uom ,b.conversion_factor, b.section_id
            from  inv_transaction a, product_details_master b 
            where a.prod_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company $item_category_sql $item_group $store_name $item_code_cond $entry_cond $prod_cond  $store_cond $des_cond
			group by
					 a.store_id,b.id,b.item_category_id,b.item_group_id,b.sub_group_name,b.item_description, b.item_size,b.current_stock,b.item_code,b.brand_name,b.origin,b.model,b.order_uom ,b.conversion_factor, b.section_id
			having sum(case when a.transaction_type in(1,4,5) then a.cons_quantity else 0 end) - sum(case when a.transaction_type in(2,3,6) then a.cons_quantity else 0 end)>0";
			$itemgroup_arr = return_library_array("select id,item_name from lib_item_group where item_category  in (101) and status_active=1 and is_deleted=0",'id','item_name');
            $store_arr = return_library_array("select id,store_name from lib_store_location where company_id=$company and status_active=1 and is_deleted=0 order by store_name",'id','store_name');
            $arr=array(0=>$item_category,1=>$itemgroup_arr,3=>$store_arr);
			// echo $sql;die;
            $result=sql_select($sql);
            $i=1;
            foreach($result as $row)
            {
				if ($i%2==0)$bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				//$balance_stock=$row[csf('balance_stock')]*$row[csf('conversion_factor')];
				$balance_stock=$row[csf('balance_stock')];
				?>
				<input type="hidden" id="item_description_all" value="" style=" width:300px;" />
				<tr bgcolor="<? echo $bgcolor; ?>"  onClick='js_set_value("<? echo $row[csf('id')]; ?>*<? echo $row[csf('des')] ;?>*<? echo $balance_stock;  //echo ($row[csf('receive')]-$row[csf('issue')]) ; ?>*<? echo $row[csf('item_category_id')] ; ?>*<? echo $row[csf('item_group_id')] ; ?>*<? echo $row[csf('store_id')] ; ?>*<? echo $row[csf('brand_name')] ; ?>*<? echo $row[csf('origin')] ; ?>*<? echo $row[csf('model')] ; ?>*<? echo $row[csf('section_id')] ; ?>*<? echo $row[csf('order_uom')] ; ?>")' id="" style="cursor:pointer">
					<td width="30" align="center"><? echo $i;  ?></td>
					<td align="center" width="50"><? echo $row[csf('id')]; ?></td>
					<td width="110" ><? echo $item_category[$row[csf('item_category_id')]]; ?></td>
					<td width="110"><? echo $itemgroup_arr[$row[csf('item_group_id')]] ; ?></td>
					<td width="100"><? echo $row[csf('sub_group_name')] ; ?></td>
					<td width="80"><? echo $row[csf('item_code')] ; ?></td>
					<td width="180"><? echo $row[csf('des')] ; ?></td>
					<td width="100"><? echo $trims_section[$row[csf('section_id')]] ; ?></td>
					<td width="110"><? echo $store_arr[$row[csf('store_id')]] ; ?></td>
					<td align="right" ><?  echo number_format($balance_stock,2); ?></td>
				</tr>
				<?
				$i++;
            }
            ?>
        </tbody>
    </table>
    </div>
    <?
}

if($action=="item_code_popup")
{
	echo load_html_head_contents("Item popup", "../../../", 1, 1,'','1','');
	extract($_REQUEST);
	?>
    <script>
	function js_set_value(str)
	{
  		$("#item_id").val(str);
 		parent.emailwindow.hide();
	}
	</script>
    <input type="hidden" id="item_id" name="item_id">
    <?
	$cbo_item_category=str_replace("'","",$cbo_item_category);
	$sql="select id, product_name_details, item_code from product_details_master where item_category_id='$cbo_item_category'";
	//echo $sql="selece id, product_name_details, item_code from product_details_master where item_category_id='$cbo_item_category'";
	echo create_list_view ( "list_view","Item Description,Item Code", "200","390","200",0, $sql, "js_set_value", "id,item_code", "", 1, "0,0", $arr, "product_name_details,item_code", "0,0", 'setFilterGrid("list_view",-1);');
}








if($action=="serial_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	//echo $txt_received_id; die;
	//echo $current_prod_id; die;

 	$serialStringID = str_replace("'","",$serialStringID);
 	//$serialStringNo = str_replace("'","",$serialStringNo);
	$txt_received_id = str_replace("'","",$txt_received_id);
	$current_prod_id = str_replace("'","",$current_prod_id);

 	?>
	<script>
	var selected_id = new Array();
	var selected_no = new Array();


	var serialNoArr="<? echo $serialStringID; ?>";
 	var chk_selected_no = new Array();
	var chk_selected_id = new Array();
	if(serialNoArr!=""){chk_selected_no=serialNoArr.split(",");}



	function check_all_data()
	{
		var tbl_row_count = document.getElementById( 'hidden_all_id' ).value.split(",");
 		//tbl_row_count = tbl_row_count-1;
		for( var i = 0; i < tbl_row_count.length; i++ ) {
 			if( jQuery.inArray( $('#txt_serial_id' + tbl_row_count[i]).val(), chk_selected_id ) != -1 )
			js_set_value( tbl_row_count[i] );
		}
	}

	function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
				//x.style.backgroundColor = ( $serialStringID != "")? newColor : origColor;
			}
		}

	function js_set_value( str ) { //alert(str);
		toggle( document.getElementById( 'search' + str ), '#FFFFCC' );

		if( jQuery.inArray( $('#txt_serial_id' + str).val(), selected_id ) == -1 ) {
			selected_id.push( $('#txt_serial_id' + str).val() );
			selected_no.push( $('#txt_serial_no' + str).val() );
 		}
		else {
			for( var i = 0; i < selected_id.length; i++ ) {
				if( selected_id[i] == $('#txt_serial_id' + str).val() ) break;
			}
			selected_id.splice( i, 1 );
			selected_no.splice( i, 1 );
		}
		var id = '';	var no = '';
		for( var i = 0; i < selected_id.length; i++ ) {
			id += selected_id[i] + ',';
			no += selected_no[i] + ',';
		}
		id = id.substr( 0, id.length - 1 );
		no = no.substr( 0, no.length - 1 );
  		$('#txt_string_id').val( id );
		$('#txt_string_no').val( no );
	}

	function fn_onClosed()
	{
		var txt_string = $('#txt_string').val();
		if(txt_string==""){ alert("Please Select The Serial"); return;}
		parent.emailwindow.hide();
	}

	</script>
	</head>
	<body>
	<div align="center" style="width:100%;" >
	<form name="searchlcfrm_1" id="searchlcfrm_1" autocomplete="off">
    	<table width="300" cellspacing="0" cellpadding="0" border="0" class="rpt_table" id="tbl_header" >
				<thead>
					<tr>
						<th width="300">Serial No</th>
 					</tr>
				</thead>
        </table>
        <div style="width:300px; min-height:220px">
		<table width="300" cellspacing="0" cellpadding="0" border="0" class="rpt_table" id="tbl_serial" style="overflow:scroll; min-height:200px" >
 				<tbody>
                	<?
						$i=1;
						$sql="select id,serial_no from inv_serial_no_details where prod_id=$current_prod_id and is_issued=0";
						//echo $sql;die;
						$result = sql_select($sql);
						$count=count($result );
						foreach($result as $row)
						{
							if ($i%2==0)$bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							if($new_data=="") $new_data=$row[csf("id")]; else $new_data .=",".$row[csf("id")];
						?>
							<tr bgcolor="<? echo $bgcolor; ?>"  onClick="js_set_value(<? echo $row[csf("id")]; ?>)" id="search<? echo $row[csf("id")]; ?>" style="cursor:pointer">
								<td  width="300">
									<? echo trim($row[csf("serial_no")]); ?>
									<input type="hidden" id="txt_serial_id<? echo $row[csf("id")]; ?>" value="<? echo $row[csf("id")]; ?>" >
                                    <input type="hidden" id="txt_serial_no<? echo $row[csf("id")]; ?>" value="<? echo $row[csf("serial_no")]; ?>" >
								</td>
									<?

									if($count==$i)
									{
									?>
                                    <input type="hidden" id="hidden_all_id" value="<? echo $new_data; ?>" >
                                    <? } ?>
							</tr>
					<?

							$i++;
						}

				?>
				</tbody>
			</table>
            </div>
            <div><input type="button" name="btn_close" class="formbutton" style="width:100px" value="Close" onClick="fn_onClosed()" /></div>
            <!-- Hidden field here-->
			<input type="hidden" id="txt_string_id" value="" />
            <input type="hidden" id="txt_string_no" value="" />
			<!-- -END-->
			</form>
	   </div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>

    <script>
	//alert(serialNoArr);
		if( serialNoArr!="" )
		{
			serialNoArr=serialNoArr.split(",");
			for(var k=0;k<serialNoArr.length; k++)
			{
				js_set_value(serialNoArr[k] );
				//alert(serialNoArr[k]);
			}
		}
	</script>
	</html>
	<?
}

if($action=="order_popup")
{
	echo load_html_head_contents("Job Popup Info","../../../", 1, 1, $unicode,'','');
	extract($_REQUEST);
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
			//var party_name = $('#cbo_party_name').val();
			//var location_name = $('#cbo_location_name').val();
			load_drop_down( 'raw_material_item_issue_controller', company+'_'+within_group, 'load_drop_down_buyer_one', 'buyer_td' );
		}
		function search_by(val)
		{
			$('#txt_search_string').val('');
			if(val==1 || val==0)
			{
				$('#search_by_td').html('System ID');
			}
			else if(val==2)
			{
				$('#search_by_td').html('W/O No');
			}
			else if(val==3)
			{
				$('#search_by_td').html('Buyer Job');
			}
			else if(val==4)
			{
				$('#search_by_td').html('Buyer Po');
			}
			else if(val==5)
			{
				$('#search_by_td').html('Buyer Style');
			}
		}
		function js_set_value(id,po_no)
		{
			$("#hidden_string").val(id+"_"+po_no);
			parent.emailwindow.hide();
		}	
	</script>
</head>
<body>
<div align="center" style="width:100%;" >
    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="940" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
            <thead> 
                <tr>
                    <th colspan="8"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                </tr>
                <tr>               	 
                    <th width="140" class="must_entry_caption">Company Name</th>
                    <th width="100">Within Group</th>                           
                    <th width="140">Party Name</th>
                    <th width="100">Search By</th>
                    <th width="100" id="search_by_td">System ID</th>
                    <th width="170">Ord. Receive Date</th>                            
                    <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
                </tr>           
            </thead>
            <tbody>
                <tr class="general">
                    <td><input type="hidden" id="selected_job"><? //$data=explode("_",$data); //echo $company?>  <!--  echo $data;-->
                        <? 
                        echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $company, "fnc_load_party_popup(1,document.getElementById('cbo_within_group').value);",1); ?>
                    </td>
                    <td>
                        <?php echo create_drop_down( "cbo_within_group", 100, $yes_no,"", 0, "--  --", 2, "fnc_load_party_popup(1,this.value);" ); ?>
                    </td>
                    <td id="buyer_td">
                        <? echo create_drop_down( "cbo_party_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", "", "" );   	 
                        ?>
                    </td>

                    <td>
						<?
                            $search_by_arr=array(1=>"System ID",2=>"W/O No",3=>"Buyer Job",4=>"Buyer Po",5=>"Buyer Style");
                            echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "",1,'search_by(this.value)',0,"","","","1,3,4,5" );
                        ?>
                    </td>
                    <td align="center">
                        <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:100px" placeholder="" />
                    </td>
                   
                    <td align="center">
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px">
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px">
                    </td>
                    <td align="center">
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_year_selection').value, 'create_trims_receive_search_list_view', 'search_div', 'raw_material_item_issue_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:70px;" /></td>
                    </tr>
                    <tr>
                        <td colspan="8" align="center" valign="middle">
						<? echo create_drop_down( "cbo_year_selection", 100, $year,"", 1, "-- Select --", date('Y'), "",0 ); ?>
                            <? echo load_month_buttons();  ?>
                            <input type="hidden" name="hidden_mst_id" id="hidden_mst_id" class="datepicker" style="width:70px">
							<input type="hidden" id="hidden_string">
                        </td>
                    </tr>
                    <tr>
                        <td colspan="8" align="center" valign="top" id=""><div id="search_div"></div></td>
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

if($action=="order_popup_old")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer where find_in_set($company,tag_company) and status_active=1 and is_deleted=0 order by buyer_name",'id','buyer_name');
?>
	<script>
		$(document).ready(function(e) {
            $("#txt_search_common").focus();
        });

		function search_populate(str)
		{
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
			else //if(str==2)
			{
				var buyer_name = '<option value="0">--- Select Buyer ---</option>';
				<?
				foreach($buyer_arr as $key=>$val)
				{
					echo "buyer_name += '<option value=\"$key\">".($val)."</option>';";
				}
				?>
				document.getElementById('search_by_th_up').innerHTML="Select Buyer Name";
				document.getElementById('search_by_td').innerHTML='<select	name="txt_search_common" style="width:230px " class="combo_boxes" id="txt_search_common">'+ buyer_name +'</select>';
			}
		}

	function js_set_value(id,po_no)
	{
		$("#hidden_string").val(id+"_"+po_no);
   		parent.emailwindow.hide();
 	}
    </script>
</head>
<body>
<div align="center" style="width:100%;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	<table width="750" cellspacing="0" cellpadding="0" class="rpt_table" align="center" border="1" rules="all">
    		<tr>
        		<td align="center" width="100%">
            		<table  cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
                   		 <thead>
                        	<th width="130">Search By</th>
                        	<th  width="180" align="center" id="search_by_th_up">Enter Order Number</th>
                        	<th width="200">Date Range</th>
                        	<th width="80"><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" /></th>
                    	</thead>
        				<tr>
                    		<td width="130">
							<?
							$searchby_arr=array(0=>"Order No",1=>"Style Ref. Number",2=>"Buyer Name");
							echo create_drop_down( "txt_search_by", 130, $searchby_arr,"", 1, "-- Select Sample --", $selected, "search_populate(this.value)",0 );
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
                     			<input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>, 'create_po_search_list_view', 'search_div', 'raw_material_item_issue_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:100px;" />
                            </td>
        				</tr>
             		</table>
          		</td>
        	</tr>
        	<tr>
            	<td  align="center" height="40" valign="middle">
					<? echo load_month_buttons();  ?>
                    <input type="hidden" id="hidden_string">
          		</td>
            </tr>
    </table>
    <br>
    <div id="search_div"></div>
    </form>
	</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
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

	$sql_cond="";
	if(trim($txt_search_common)!="")
	{
		if(trim($txt_search_by)==0)
			$sql_cond = " and b.po_number like '%".trim($txt_search_common)."%'";
		else if(trim($txt_search_by)==1)
			$sql_cond = " and a.style_ref_no like '%".trim($txt_search_common)."%'";
		else if(trim($txt_search_by)==2)
			$sql_cond = " and a.buyer_name=trim('$txt_search_common')";
 	}
	if($txt_date_from!="" || $txt_date_to!="") $sql_cond .= " and b.shipment_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
	if(trim($company)!="") $sql_cond .= " and a.company_name='$company'";

 	$sql = "select b.id,a.order_uom,a.buyer_name,a.company_name,a.total_set_qnty,a.set_break_down, a.job_no,a.style_ref_no,a.gmts_item_id,a.location_name,b.shipment_date,b.pub_shipment_date,b.po_number,b.po_quantity ,b.plan_cut
			from wo_po_details_master a, wo_po_break_down b
			where
			a.job_no = b.job_no_mst and
			a.status_active=1 and
			a.is_deleted=0
			$sql_cond";
	//echo $sql;die;
	$result = sql_select($sql);
 	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	?>
    <div style="width:820px;">
     	<table cellspacing="0" width="100%" border="1" class="rpt_table" rules="all">
            <thead>
                <th width="30" >SL</th>
                <th width="80" >Shipment Date</th>
                <th width="120" >Order No</th>
                <th width="150" >Buyer</th>
                <th width="150" >Style</th>
                 <th width="100" >Order Qnty</th>
                <th>Company Name</th>
            </thead>
     	</table>
     </div>
     <div style="width:820px; max-height:220px;overflow-y:scroll;" >
        <table cellspacing="0" width="802" class="rpt_table" id="tbl_po_list" border="1" rules="all">
			<?
			$i=1;
            foreach( $result as $row )
            {
                if ($i%2==0)  $bgcolor="#E9F3FF";
                else $bgcolor="#FFFFFF";
 					?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value(<? echo $row[csf("id")];?>,'<? echo $row[csf("po_number")];?>');" >
							<td width="30" align="center"><? echo $i; ?></td>
							<td width="80" align="center"><p><? echo change_date_format($row[csf("pub_shipment_date")]);?></p></td>
							<td width="120" align="center"><p><? echo $row[csf("po_number")]; ?></p></td>
							<td width="150"><p><? echo $buyer_arr[$row[csf("buyer_name")]];  ?></p></td>
							<td width="150"><p><? echo $row[csf("style_ref_no")]; ?></p></td>
 							<td width="95" align="right" style="padding-right:5px;"><p><? echo $row[csf("po_quantity")];?> </p></td>
							<td><p><?  echo $company_arr[$row[csf("company_name")]];?></p> </td>
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

//data save update delete here------------------------------//
if($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$txt_prod_id=str_replace("'","",$current_prod_id);
    $req_id=str_replace("'", '', $hidden_issue_req_id);
    //echo "10**".$hidden_issue_req_id; die;
    if(str_replace("'", "", $cbo_issue_basis)!=7){
    	if($req_id!="") 
		{
			$requisition_company_id = return_field_value("company_id", "trims_job_card_mst", "id=$req_id", "company_id");
			if($requisition_company_id != str_replace("'", "", $cbo_company_id))
			{
				echo "20**Company must be same of Requisition Company";
				die;
			}
		}
    }
		

    $max_recv_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$txt_prod_id and transaction_type in (1,4,5) and store_id = $cbo_store_name and status_active = 1", "max_date");
    if($max_recv_date != "")
   	{
		$max_recv_date = date("Y-m-d", strtotime($max_recv_date));
		$issue_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_issue_date)));
		if ($issue_date < $max_recv_date)
	    {
            echo "20**Issue Date Can not Be Less Than Last Receive Date Of This Lot";
            die;
		}
   	}

   	if(str_replace("'","",$cbo_issue_basis)==15)
   	{
   		$sql= "select b.product_id, b.lot, b.req_qty, a.break_id as subcon_break_ids from trims_job_card_dtls a, trims_job_card_breakdown b where a.id=b.mst_id and a.status_active=1 and a.mst_id=$req_id";
		$data_array=sql_select($sql);
		foreach($data_array as $row){
			$raw_qty_arr[$row[csf("product_id")]][$row[csf("lot")]]['req_qty']+=$row[csf("req_qty")];
			$raw_qty_arr[$row[csf("product_id")]][$row[csf("lot")]]['subcon_break_ids'].=$row[csf("subcon_break_ids")].',';
		}

		$job_qnty_arr=array();
		$qty_sql ="SELECT a.id, a.booked_qty from subcon_ord_breakdown a where a.booked_qty is not null and a.booked_qty!=0";
		$qty_sql_res=sql_select($qty_sql);
		foreach ($qty_sql_res as $row)
		{
			$job_qnty_arr[$row[csf("id")]]['qnty']=$row[csf("booked_qty")];
		}
		unset($qty_sql_res);
   	}

	$iss_sql= "select b.prod_id, b.batch_lot, sum(b.cons_quantity) as cons_quantity 
	from inv_issue_master a, inv_transaction b 
	where a.id=b.mst_id and a.req_id=$req_id and b.transaction_type=2 and a.issue_basis=15 and a.entry_form=265 group by b.prod_id";

	$iss_data_array=sql_select($iss_sql);
	foreach($iss_data_array as $row){
		if(str_replace("'","",$variable_lot)==1) $batch_lot=$row[csf("batch_lot")]; else  $batch_lot="";
		$issue_qty_arr[$row[csf("prod_id")]][$batch_lot]+=$row[csf("cons_quantity")];
	}

	if( $operation==0 ) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		//table lock here
		//if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		//---------------Check Duplicate product in Same return number ------------------------//
		$txt_prod_id=str_replace("'","",$current_prod_id);
		/*$duplicate = is_duplicate_field("b.id","inv_issue_master a, inv_transaction b","a.id=b.mst_id and a.id=$txt_system_id and b.prod_id=$txt_prod_id and b.transaction_type=2");
		if($duplicate==1 && str_replace("'","",$txt_system_no)!="")
		{
			echo "20**Duplicate Product is Not Allow in Same Return Number.";
			disconnect($con);
			die;
		}*/
		if(str_replace("'","",$variable_lot)==1) $batch_lot=str_replace("'","",$txt_lot_no); else  $batch_lot="";
		if(str_replace("'","",$cbo_issue_basis)==15)
   		{
			$subcon_break_ids=explode(",",chop($raw_qty_arr[$txt_prod_id][$batch_lot]['subcon_break_ids'],','));
			for($j=0; $j<count($subcon_break_ids); $j++)
			{
				$jobQnty +=$job_qnty_arr[$subcon_break_ids[$j]]['qnty'];
			}
			$jobQnty=round($jobQnty); 
			$jobCardQnty=$jobQnty*$raw_qty_arr[$txt_prod_id][$batch_lot]['req_qty'];
			$balance= $jobCardQnty-$issue_qty_arr[$txt_prod_id][$batch_lot];
			if($balance < 0)
			{
				echo "19**Issue Quantity Exceeds Balance Quantity";
				//check_table_status( $_SESSION['menu_id'],0);
				disconnect($con);
				die;
			}
		}
		//$stock_qnty=return_field_value("(sum(case when transaction_type=1 then cons_quantity else 0 end) - sum(case when transaction_type=2 then cons_quantity else 0 end)) as current_stock","inv_transaction","status_active=1 and is_deleted=0 and prod_id=$txt_prod_id","current_stock");
		//$stock_qnty=return_field_value("current_stock as current_stock","product_details_master","status_active=1 and is_deleted=0 and id=$txt_prod_id","current_stock");
		$sql = sql_select("select avg_rate_per_unit,current_stock,stock_value,conversion_factor from product_details_master where id=$txt_prod_id");
		$avg_rate=$stock_qnty=$stock_value=0;
		foreach($sql as $result)
		{
			$avg_rate = $result[csf("avg_rate_per_unit")];
			$stock_qnty = $result[csf("current_stock")];
			$stock_value = $result[csf("stock_value")];
			$conversion_factor = $result[csf("conversion_factor")];
		}
		
 		//echo "10**".str_replace("'","",$cbo_item_category); die;
		if(str_replace("'","",$variable_lot)==1 && str_replace("'","",$cbo_item_category)==22) $lot_cond=" and batch_lot='$batch_lot'"; else $lot_cond="";
		$store_stock_qnty=return_field_value("sum(case when transaction_type in(1,4,5) then cons_quantity else 0 end) - sum(case when transaction_type in(2,3,6) then cons_quantity else 0 end) as balance_stock","inv_transaction","status_active=1 and prod_id=$txt_prod_id and store_id=$cbo_store_name $lot_cond","balance_stock");
		
		
	  //	echo "10**".$store_stock_qnty; die;

		if(str_replace("'","",$txt_issue_qnty)>$store_stock_qnty)
		{
			echo "17**Issue Quantity Exceeds The Current Stock Quantity $txt_issue_qnty";
			//check_table_status( $_SESSION['menu_id'],0);
			disconnect($con);
			die;
		}

 		//issue master table entry here START---------------------------------------//
 		if( str_replace("'","",$txt_system_no) == "" ) //new insert
		{
			//$id=return_next_id("id", "inv_issue_master", 1);
			//$new_mrr_number=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', 'GIS', date("Y",time()), 5, "select issue_number_prefix,issue_number_prefix_num from inv_issue_master where company_id=$cbo_company_id and entry_form=265 $mrr_date_check order by id DESC ", "issue_number_prefix", "issue_number_prefix_num" ));

			$id = return_next_id_by_sequence("INV_ISSUE_MASTER_PK_SEQ", "inv_issue_master", $con);
			$new_mrr_number = explode("*", return_next_id_by_sequence("INV_ISSUE_MASTER_PK_SEQ", "inv_issue_master",$con,1,str_replace("'","",$cbo_company_id),'RMIS',265,date("Y",time())));

			$field_array_master="id,issue_number_prefix, issue_number_prefix_num, issue_number, issue_purpose,loan_party, entry_form, company_id, issue_date, challan_no, req_no,knit_dye_source,knit_dye_company,remarks, req_id,issue_basis, inserted_by, insert_date,section_id";
			$data_array_master="(".$id.",'".$new_mrr_number[1]."','".$new_mrr_number[2]."','".$new_mrr_number[0]."',".$cbo_issue_purpose.",".$cbo_loan_party.", 265,".$cbo_company_id.",".$txt_issue_date.",".$txt_challan_no.",".$txt_issue_req_no.",".$cbo_issue_source.",".$cbo_issue_to.",".$txt_remarks.",'".$req_id."',".$cbo_issue_basis.",'".$user_id."','".$pc_date_time."',".$cbo_section_mst.")";
			//echo "100___**___".$field_array."<br>100___**___".$data_array;die;
			//$rID = sql_insert("inv_issue_master",$field_array_master,$data_array_master,1);
 		}
		else //update
		{
			$new_mrr_number[0]=str_replace("'","",$txt_system_no);
			$id=str_replace("'","",$txt_system_id);
			$field_array_master="issue_purpose*loan_party*issue_date*challan_no*req_no*knit_dye_source*knit_dye_company*remarks*req_id*issue_basis*updated_by*update_date*section_id";
			$data_array_master="".$cbo_issue_purpose."*".$cbo_loan_party."*".$txt_issue_date."*".$txt_challan_no."*".$txt_issue_req_no."*".$cbo_issue_source."*".$cbo_issue_to."*".$txt_remarks."*'".$req_id."'*".$cbo_issue_basis."*'".$user_id."'*'".$pc_date_time."'*".$cbo_section_mst."";
			//echo "20**".$field_array."<br>".$txt_system_id;die;
			//$rID=sql_update("inv_issue_master",$field_array_master,$data_array_master,"id",$id,1);
 		}
		//issue master table entry here END---------------------------------------//

		//product master table information
		

		//inventory TRANSACTION table data entry START----------------------------------------------------------//
		//$transaction_type=array(1=>"Receive",2=>"Issue",3=>"Receive Return",4=>"Issue Return");
		$txt_issue_qnty = str_replace("'","",$txt_issue_qnty);
 		$issue_stock_value = $avg_rate*$txt_issue_qnty;
		//$transactionID = return_next_id("id", "inv_transaction", 1);
		$transactionID = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
		$field_array_trans_insert = "id,mst_id,company_id,prod_id,item_category,transaction_type,transaction_date,store_id,order_id,pi_wo_batch_no,cons_uom,cons_quantity,cons_rate,cons_amount,floor_id,line_id,machine_id,item_return_qty,machine_category,room,rack,self,bin_box,location_id,department_id,section_id,issue_basis,trans_uom,no_of_qty,inserted_by,insert_date,batch_lot";
 		$data_array_trans_insert = "(".$transactionID.",".$id.",".$cbo_company_id.",".$txt_prod_id.",".$cbo_item_category.",2,".$txt_issue_date.",".$cbo_store_name.",".$txt_order_id.",".$txt_wo_batch_no.",".$cbo_uom.",".$txt_issue_qnty.",".$avg_rate.",".$issue_stock_value.",".$cbo_floor.",".$cbo_sewing_line.",".$cbo_machine_name.",".$txt_return_qty.",".$cbo_machine_category.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_bin.",".$cbo_location.",".$cbo_department.",".$cbo_section.",".$cbo_issue_basis.",".$cbo_issue_uom.",".$txt_no_of_qty.",'".$user_id."','".$pc_date_time."',".$txt_lot_no.")";
		//echo $field_array."<br>".$data_array;die;

		//$transID = sql_insert("inv_transaction",$field_array_trans_insert,$data_array_trans_insert,1);

		//inventory TRANSACTION table data entry  END----------------------------------------------------------//

		//if LIFO/FIFO then START -----------------------------------------//
		$field_array_lifu_fifu = "id,recv_trans_id,issue_trans_id,entry_form,prod_id,issue_qnty,item_return_qty,rate,amount,inserted_by,insert_date";
		$update_array_lifu_fifu = "balance_qnty*balance_amount*updated_by*update_date";
		$cons_rate=0;
		$data_array_lifu_fifu="";
		$updateID_array_lifu_fifu=array();
		$update_data_lifu_fifu=array();
		$issueQnty = $txt_issue_qnty;
		//$mrrWiseIsID = return_next_id("id", "inv_mrr_wise_issue_details", 1);

		if($db_type==0)
		{
			$returnString=return_field_value("concat(store_method,'_',allocation)","variable_settings_inventory","company_name=$cbo_company_id and variable_list=17 and item_category_id=$cbo_item_category and status_active=1 and is_deleted=0");
		}
		else
		{
			//echo "select (store_method || '_' || allocation) as store_data from variable_settings_inventory where company_name=$cbo_company_id and variable_list=17 and item_category_id=$cbo_item_category and status_active=1 and is_deleted=0";die;
			$returnString=return_field_value("(store_method || '_' || allocation) as store_data","variable_settings_inventory","company_name=$cbo_company_id and variable_list=17 and item_category_id=$cbo_item_category and status_active=1 and is_deleted=0","store_data");
		}

		$expString = explode("_",$returnString);
		$isLIFOfifo = $expString[0];
		$check_allocation = $expString[1];

		if($isLIFOfifo==2) $cond_lifofifo=" DESC"; else $cond_lifofifo=" ASC";
		$sql = sql_select("select id,cons_rate,balance_qnty,balance_amount from inv_transaction where prod_id=$txt_prod_id and balance_qnty>0 and transaction_type in (1,4,5) and item_category=$cbo_item_category order by id $cond_lifofifo");
		foreach($sql as $result)
		{
			$mrrWiseIsID = return_next_id_by_sequence("INV_MRR_WISE_ISSUE_PK_SEQ", "inv_mrr_wise_issue_details", $con);
			$recv_trans_id = $result[csf("id")]; // this row will be updated
			$balance_qnty = $result[csf("balance_qnty")];
			$balance_amount = $result[csf("balance_amount")];
			$cons_rate = $result[csf("cons_rate")];
			$issueQntyBalance = $balance_qnty-$issueQnty; // minus issue qnty
			$issueStockBalance = $balance_amount-($issueQnty*$cons_rate);
			if($issueQntyBalance>=0)
			{
				$amount = $issueQnty*$cons_rate;
				//for insert
				if($data_array_lifu_fifu!="") $data_array_lifu_fifu .= ",";
				$data_array_lifu_fifu .= "(".$mrrWiseIsID.",".$recv_trans_id.",".$transactionID.",265,".$txt_prod_id.",".$issueQnty.",".$txt_return_qty.",".$cons_rate.",".$amount.",'".$user_id."','".$pc_date_time."')";
				//for update
				$updateID_array_lifu_fifu[]=$recv_trans_id;
				$update_data_lifu_fifu[$recv_trans_id]=explode("*",("".$issueQntyBalance."*".$issueStockBalance."*'".$user_id."'*'".$pc_date_time."'"));
				break;
			}
			else if($issueQntyBalance<0)
			{
				$issueQntyBalance  = $issueQnty-$balance_qnty;
				$issueQnty = $balance_qnty;
				$amount = $issueQnty*$cons_rate;
				//for insert
				if($data_array_lifu_fifu!="") $data_array_lifu_fifu .= ",";
				$data_array_lifu_fifu .= "(".$mrrWiseIsID.",".$recv_trans_id.",".$transactionID.",265,".$txt_prod_id.",".$balance_qnty.",".$txt_return_qty.",".$cons_rate.",".$amount.",'".$user_id."','".$pc_date_time."')";
				//for update
				$updateID_array_lifu_fifu[]=$recv_trans_id;
				$update_data_lifu_fifu[$recv_trans_id]=explode("*",("0*0*'".$user_id."'*'".$pc_date_time."'"));
				$issueQnty = $issueQntyBalance;
			}
			//$mrrWiseIsID++;
		}//end foreach
 		// LIFO/FIFO then END-----------------------------------------------//



		//mrr wise issue data insert here----------------------------//
		/*$mrrWiseIssueID=true;
		if($data_array_lifu_fifu!="")
		{
			$mrrWiseIssueID=sql_insert("inv_mrr_wise_issue_details",$field_array_lifu_fifu,$data_array_lifu_fifu,1);
		}
		//transaction table stock update here------------------------//
		$upTrID=true;
		if(count($updateID_array_lifu_fifu)>0)
		{
 			$upTrID=execute_query(bulk_update_sql_statement("inv_transaction","id",$update_array_lifu_fifu,$update_data_lifu_fifu,$updateID_array_lifu_fifu),1);
		}*/

 		//product master table data UPDATE START----------------------//
  		$currentStock   = $stock_qnty-$txt_issue_qnty;
		$StockValue	 	= $stock_value-($txt_issue_qnty*$avg_rate);
		$avgRate	 	= number_format($StockValue/$currentStock,$dec_place[3],'.','');

		$field_array_product	= "last_issued_qnty*item_return_qty*current_stock*stock_value*updated_by*update_date";
		$data_array_product	= "".$txt_issue_qnty."*".$txt_return_qty."*".$currentStock."*".number_format($StockValue,$dec_place[4],'.','')."*'".$user_id."'*'".$pc_date_time."'";
		//------------------ product_details_master END--------------//
		
		
		
		//echo "insert into inv_issue_master ($field_array_master) values $data_array_master";die;
		//echo "10**insert into inv_transaction ($field_array_trans_insert) values $data_array_trans_insert";die;

		if( str_replace("'","",$txt_system_no) == "" ) //new insert
		{
			$rID = sql_insert("inv_issue_master",$field_array_master,$data_array_master,1);
 		}
		else //update
		{
			$rID=sql_update("inv_issue_master",$field_array_master,$data_array_master,"id",$id,1);
 		}
		$transID = sql_insert("inv_transaction",$field_array_trans_insert,$data_array_trans_insert,1);
		$prodUpdate 	= sql_update("product_details_master",$field_array_product,$data_array_product,"id",$txt_prod_id,1);
		$mrrWiseIssueID=true;
		if($data_array_lifu_fifu!="")
		{
			$mrrWiseIssueID=sql_insert("inv_mrr_wise_issue_details",$field_array_lifu_fifu,$data_array_lifu_fifu,1);
		}
		//transaction table stock update here------------------------//
		$upTrID=true;
		if(count($updateID_array_lifu_fifu)>0)
		{
 			$upTrID=execute_query(bulk_update_sql_statement("inv_transaction","id",$update_array_lifu_fifu,$update_data_lifu_fifu,$updateID_array_lifu_fifu),1);
		}




 		$txt_serial_id 	= trim(str_replace("'","",$txt_serial_id));
		$serialUpdate=true;
 		if($txt_serial_id!="")
		{
			if( strpos(trim($txt_serial_no), ",")>0)
			{
				$se_data=explode(",",str_replace("'","",$txt_serial_no));
				if( (count($se_data)<=str_replace("'","",$txt_issue_qnty)))
				{
					$field_array_serial="issue_trans_id*is_issued*updated_by*update_date";
					$txt_serial_id_arr=explode(",",$txt_serial_id);
					if(count($txt_serial_id_arr)>0)
					{
						foreach($txt_serial_id_arr as $serial_id)
						{
							$update_data_serial[$serial_id]=explode("*",("".$transactionID."*1*'".$user_id."'*'".$pc_date_time."'"));
						}
					}

					$serialUpdate=execute_query(bulk_update_sql_statement("inv_serial_no_details","id",$field_array_serial,$update_data_serial,$txt_serial_id_arr),1);
					//$serialUpdate = execute_query("update inv_serial_no_details set issue_trans_id=$transactionID , is_issued=1 where id in ($txt_serial_id)",1);
				}
				else
				{
					echo "50";
					//check_table_status( $_SESSION['menu_id'],0);
					disconnect($con);
					die;
				}
			}
			else
			{
				$field_array_serial="issue_trans_id*is_issued*updated_by*update_date";
				$txt_serial_id_arr=explode(",",$txt_serial_id);
				if(count($txt_serial_id_arr)>0)
				{
					foreach($txt_serial_id_arr as $serial_id)
					{
						$update_data_serial[$serial_id]=explode("*",("".$transactionID."*1*'".$user_id."'*'".$pc_date_time."'"));
					}
				}

				$serialUpdate=execute_query(bulk_update_sql_statement("inv_serial_no_details","id",$field_array_serial,$update_data_serial,$txt_serial_id_arr),1);

				//$serialUpdate 	= execute_query("update inv_serial_no_details set issue_trans_id=$transactionID, is_issued=1 where id in ($txt_serial_id)",1);

			}
		}
		
		
		
		//echo "10**".$rID." && ".$transID." && ".$mrrWiseIssueID." && ".$upTrID." && ".$prodUpdate." && ".$serialUpdate;oci_rollback($con);die;
		//mysql_query("ROLLBACK");die;

		//release lock table   oci_commit($con); oci_rollback($con);
		//check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID && $transID && $mrrWiseIssueID && $upTrID && $prodUpdate && $serialUpdate)
			{
				mysql_query("COMMIT");
				echo "0**".$new_mrr_number[0]."**".$id."**".$req_id;
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".$new_mrr_number[0]."**".$id."**".$req_id;
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($rID && $transID && $mrrWiseIssueID && $upTrID && $prodUpdate && $serialUpdate)
			{
				oci_commit($con);
				echo "0**".$new_mrr_number[0]."**".$id."**".$req_id;
			}
			else
			{
				oci_rollback($con);
				echo "10**".$new_mrr_number[0]."**".$id."**".$req_id;
			}
		}
		//check_table_status( $_SESSION['menu_id'],0);
		disconnect($con);
		die;
	}
	else if ($operation==1) // Update Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		//table lock here
		//if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		//check update id
		if( str_replace("'","",$update_id) == "" ||  str_replace("'","",$txt_system_no)=="" )
		{
			echo "10";
			//check_table_status( $_SESSION['menu_id'],0);
			disconnect($con);
			die();
		}
		if(str_replace("'","",$variable_lot)==1) $batch_lot=str_replace("'","",$txt_lot_no); else  $batch_lot="";
		if(str_replace("'","",$cbo_issue_basis)==7)
		{	
			 $req_id=str_replace("'", '', $hidden_issue_req_id);
		
			if($req_id!="")
			{
				$requisition_company_id = return_field_value("company_id", "trims_raw_mat_requisition_mst", "id=$req_id", "company_id");
				if($requisition_company_id != str_replace("'", "", $cbo_company_id))
				{
					echo "20**Company must be same of Requisition Company";die;
				}
				$trans_id=str_replace("'","",$update_id);
				$up_cond=$lot_cond=$lot_cond_req="";
				if($trans_id!="") $up_cond=" and b.id <> $trans_id";
				if($batch_lot) $lot_cond=" and batch_lot = '$batch_lot'";
				$prev_req_rcv=sql_select("select sum(b.cons_quantity) as rcv_qnty from inv_issue_master a, inv_transaction b where a.id=b.mst_id and a.entry_form=265 and b.transaction_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.req_id=$req_id and b.prod_id=$current_prod_id $lot_cond $up_cond");
				$prev_req_qnty=$prev_req_rcv[0][csf("rcv_qnty")];
				if($batch_lot) $lot_cond_req=" and lot = '$batch_lot'";
				$sql_req=sql_select("select sum(requisition_qty) as req_qty from trims_raw_mat_requisition_dtls where mst_id=$req_id and product_id=$current_prod_id and status_active=1 and is_deleted=0 $lot_cond_req");
				$cu_req_qnty=($sql_req[0][csf("req_qty")]-$prev_req_qnty)*1;
				$issu_qnty=str_replace("'","",$txt_issue_qnty)*1;
				//echo "10** $issu_qnty = $cu_req_qnty";die;
				if($issu_qnty>$cu_req_qnty)
				{
					echo "20**Issue Quantity Not Allow Over Requisition Quantity \n Requisition Balance Quantity=$cu_req_qnty";die;
				}
			}
		}
		//$curr_stock_qnty+$before_issue_qnty-$txt_issue_qnty
		if(str_replace("'","",$cbo_issue_basis)==15)
   		{
			$subcon_break_ids=explode(",",chop($raw_qty_arr[$txt_prod_id][$batch_lot]['subcon_break_ids'],','));
			for($j=0; $j<count($subcon_break_ids); $j++)
			{
				$jobQnty +=$job_qnty_arr[$subcon_break_ids[$j]]['qnty'];
			}
			$jobQnty=round($jobQnty); 
			$jobCardQnty=$jobQnty*$raw_qty_arr[$txt_prod_id][$batch_lot]['req_qty'];
			//echo "10**".$jobCardQnty.'**'.$jobQnty.'**'.$raw_qty_arr[$txt_prod_id]['req_qty']; die;
			//10**205**205**1
			$balance= $jobCardQnty-$issue_qty_arr[$txt_prod_id][$batch_lot];
			$actual_bal=((number_format($balance,2,'.','')+number_format($issue_qty_arr[$txt_prod_id][$batch_lot],2,'.',''))-str_replace("'","",$txt_issue_qnty));
			//$test_data=number_format($balance,2,'.','')+number_format($issue_qty_arr[$txt_prod_id][$batch_lot],2,'.','');
			//10**-2.05**-2.05**205**207.05
			if($actual_bal < 0)
			{
				echo "19**Issue Quantity Exceeds Balance Quantity  ";
				//check_table_status( $_SESSION['menu_id'],0);
				disconnect($con);
				die;
			}
		}
		
		
		//variable_list=17 is_allocated,  item_category_id=1 is yarn--------------------
		if($db_type==0){
			$returnString=return_field_value("concat(store_method,'_',allocation)","variable_settings_inventory","company_name=$cbo_company_id and variable_list=17 and item_category_id=$cbo_item_category and status_active=1 and is_deleted=0");
		}else{
			$returnString=return_field_value("(store_method || '_' || allocation) as store_data","variable_settings_inventory","company_name=$cbo_company_id and variable_list=17 and item_category_id=$cbo_item_category and status_active=1 and is_deleted=0","store_data");
		}
		$expString = explode("_",$returnString);
		$isLIFOfifo = $expString[0];
		$check_allocation = $expString[1];
		//****************************************** BEFORE ENTRY ADJUST START *****************************************//
		//product master table information
		//before stock update
		if($batch_lot) $lot_cond_trns=" and b.batch_lot = '$batch_lot'";
		$sql = sql_select( "select a.id,a.avg_rate_per_unit,a.current_stock,a.stock_value, b.cons_quantity,b.item_return_qty, b.cons_amount 
		from product_details_master a, inv_transaction b 
		where a.id=b.prod_id and b.id=$update_id and b.transaction_type=2 $lot_cond_trns");

		$before_prod_id=$before_issue_qnty=$before_stock_qnty=$before_stock_value=$before_prod_rate=0;
		foreach($sql as $result)
		{
			$before_prod_id 	= $result[csf("id")];
 			$before_stock_qnty = $result[csf("current_stock")];
			$before_prod_rate = $result[csf("avg_rate_per_unit")];
			$before_stock_value = $result[csf("stock_value")];
			$before_issue_qnty = $result[csf("cons_quantity")];
			$before_return_qty = $result[csf("item_return_qty")];
			$before_issue_value = $result[csf("cons_amount")];
		}
		//current product ID
		$txt_prod_id = str_replace("'","",$current_prod_id);
		$txt_issue_qnty = str_replace("'","",$txt_issue_qnty);
		$sql = sql_select("select avg_rate_per_unit,current_stock,stock_value from product_details_master where id=$txt_prod_id");
		$curr_avg_rate=$curr_stock_qnty=$curr_stock_value=0;
		foreach($sql as $result)
		{
			$curr_avg_rate 	   = $result[csf("avg_rate_per_unit")];
			$curr_stock_qnty 	 = $result[csf("current_stock")];
			$curr_stock_value 	= $result[csf("stock_value")];
		}

		//weighted and average rate START here------------------------//
		//product master table data UPDATE START----------------------//
		$update_array_prod	= "last_issued_qnty*item_return_qty*current_stock*stock_value*updated_by*update_date"; //*allocated_qnty*available_qnty
		if($before_prod_id==$txt_prod_id)
		{
			$adj_stock_qnty = $curr_stock_qnty+$before_issue_qnty-$txt_issue_qnty; // CurrentStock + Before Issue Qnty - Current Issue Qnty
 			//$adj_stock_val  = $curr_stock_value+$before_issue_value-($txt_issue_qnty*$curr_avg_rate); // CurrentStockValue + Before Issue Value - Current Issue Value
			$adj_stock_val  = $adj_stock_qnty*$curr_avg_rate;
			//$adj_avgrate	= number_format($adj_stock_val/$adj_stock_qnty,$dec_place[3],'.','');
			$data_array_prod		= "".$txt_issue_qnty."*".$txt_return_qty."*".$adj_stock_qnty."*".number_format($adj_stock_val,$dec_place[4],'.','')."*'".$user_id."'*'".$pc_date_time."'";

			$stock_qnty=return_field_value("sum(case when transaction_type in(1,4,5) then cons_quantity else 0 end) - sum(case when transaction_type in(2,3,6) then cons_quantity else 0 end) as balance_stock","inv_transaction","status_active=1 and prod_id=$before_prod_id and store_id=$cbo_store_name $lot_cond","balance_stock");
			$latest_current_stock=$stock_qnty+$before_issue_qnty;
			//now current stock
			$curr_avg_rate 		= $curr_avg_rate;
			$curr_stock_qnty 	= $adj_stock_qnty;
			$curr_stock_value 	= $adj_stock_val;
		}
		else
		{
			$updateID_array = $update_data = array();
			$latest_current_stock=return_field_value("sum(case when transaction_type in(1,4,5) then cons_quantity else 0 end) - sum(case when transaction_type in(2,3,6) then cons_quantity else 0 end) as balance_stock","inv_transaction","status_active=1 and prod_id=$txt_prod_id and store_id=$cbo_store_name $lot_cond","balance_stock");
			//$latest_current_stock=return_field_value("current_stock as current_stock","product_details_master","status_active=1 and is_deleted=0 and id=$txt_prod_id","current_stock");
			//before product adjust
			$adj_before_stock_qnty 	= $before_stock_qnty+$before_issue_qnty; // CurrentStock + Before Issue Qnty
			$adj_before_stock_val  	 = $before_stock_value+$before_issue_value; // CurrentStockValue + Before Issue Value
			//$adj_before_avgrate	   = number_format($adj_before_stock_val/$adj_before_stock_qnty,$dec_place[3],'.','');
			$adj_before_avgrate	   = $before_prod_rate;
			$updateID_array_prod[]=$before_prod_id;
			$data_array_prod[$before_prod_id]=explode("*",("".$before_issue_qnty."*".$before_return_qty."*".$adj_before_stock_qnty."*".number_format($adj_before_stock_val,$dec_place[4],'.','')."*'".$user_id."'*'".$pc_date_time."'"));
 			//current product adjust
			$adj_curr_stock_qnty  = $curr_stock_qnty-$txt_issue_qnty; // CurrentStock + Before Issue Qnty
			//$adj_curr_stock_val   = $curr_stock_value-($txt_issue_qnty*$curr_avg_rate); // CurrentStockValue + Before Issue Value
			$adj_curr_stock_val  = $adj_curr_stock_qnty*$curr_avg_rate;
			//$adj_curr_avgrate	 = number_format($adj_curr_stock_val/$adj_curr_stock_qnty,$dec_place[3],'.','');
			//for current product-------------
			//$availableChk = $curr_stock_qnty>=$txt_issue_qnty ?  true : false;
			//$msg="Issue Quantity is exceed the current Stock Quantity";
			$updateID_array_prod[]=$txt_prod_id;
			$data_array_prod[$txt_prod_id]=explode("*",("".$txt_issue_qnty."*".$txt_return_qty."*".$adj_curr_stock_qnty."*".number_format($adj_curr_stock_val,$dec_place[4],'.','')."*'".$user_id."'*'".$pc_date_time."'"));
			//now current stock
			$curr_avg_rate 		= $curr_avg_rate;
			$curr_stock_qnty 	= $adj_curr_stock_qnty;
			$curr_stock_value 	= $adj_curr_stock_val;
		}
		//$stock_qnty=return_field_value("sum(case when transaction_type in(1,4,5) then cons_quantity else 0 end) - sum(case when transaction_type in(2,3,6) then cons_quantity else 0 end) as balance_stock","inv_transaction","status_active=1 and prod_id=$txt_prod_id and store_id=$cbo_store_name","balance_stock");

		if(str_replace("'","",$txt_issue_qnty)>$latest_current_stock)
		{
			echo "17**Issue Quantity Exceeds The Current Stock Quantity";
			//check_table_status( $_SESSION['menu_id'],0);
			disconnect($con);
			die;
		}
  		//------------------ product_details_master END--------------//
		//weighted and average rate END here-------------------------//

 		//transaction table START--------------------------//

		$update_array_trans = "balance_qnty*balance_amount*updated_by*update_date";
		$sql = sql_select("select a.id,a.balance_qnty,a.balance_amount,b.issue_qnty,b.rate,b.amount from inv_transaction a, inv_mrr_wise_issue_details b where a.id=b.recv_trans_id and b.issue_trans_id=$update_id and b.entry_form=265");

		$updateID_array_trans = array();
		$update_data_trans = array();
		foreach($sql as $result)
		{
			$adjBalance = $result[csf("balance_qnty")]+$result[csf("issue_qnty")];
			$adjAmount = $result[csf("balance_amount")]+$result[csf("amount")];
			$updateID_array_trans[]=$result[csf("id")];
			$update_data_trans[$result[csf("id")]]=explode("*",("".$adjBalance."*".$adjAmount."*'".$user_id."'*'".$pc_date_time."'"));

			$trans_data_array[$result[csf("id")]]['qnty']=$adjBalance;
			$trans_data_array[$result[csf("id")]]['amnt']=$adjAmount;
		}
		/*$query2=true;
		if(count($updateID_array_trans)>0)
		{
 			$query2=execute_query(bulk_update_sql_statement("inv_transaction","id",$update_array_trans,$update_data_trans,$updateID_array_trans),0);
		}*/
		//transaction table END----------------------------//
		//LIFO/FIFO  START here------------------------//
		/*$query3=true;
		if(count($update_data_trans)>0)
		{
			 $updateIDArray = implode(",",$update_data_trans);
			 $query3 = execute_query("DELETE FROM inv_mrr_wise_issue_details WHERE issue_trans_id=$update_id and entry_form=265",0);
		}*/
		//****************************************** NEW ENTRY START *****************************************//
		//issue master update START--------------------------------------//
		$field_array_update_issue="issue_purpose*loan_party*issue_date*challan_no*req_no*remarks*issue_basis*updated_by*update_date*section_id";
		$data_array_update_issue="".$cbo_issue_purpose."*".$cbo_loan_party."*".$txt_issue_date."*".$txt_challan_no."*".$txt_issue_req_no."*".$txt_remarks."*".$cbo_issue_basis."*'".$user_id."'*'".$pc_date_time."'*".$cbo_section_mst."";
		/*if(trim(str_replace("'","",$txt_system_id))!="")
		{
			$rID=sql_update("inv_issue_master",$field_array_update_issue,$data_array_update_issue,"id",$txt_system_id,1);
		}*/
		//echo $field_array."<br>".$data_array;."-".;
		//issue master update END---------------------------------------//
		//inventory TRANSACTION table data UPDATE START----------------------------------------------------------//
		//$transaction_type=array(1=>"Receive",2=>"Issue",3=>"Receive Return",4=>"Issue Return");
		$txt_issue_qnty = str_replace("'","",$txt_issue_qnty);
		$avg_rate = $curr_avg_rate; // asign current rate
 		$issue_stock_value = $avg_rate*$txt_issue_qnty;

		$field_array_again = "prod_id*item_category*transaction_type*transaction_date*store_id*order_id*pi_wo_batch_no*cons_uom*cons_quantity*cons_rate*cons_amount*floor_id*line_id*machine_id*item_return_qty*machine_category*room*rack*self*bin_box*location_id*department_id*section_id*issue_basis*trans_uom*no_of_qty*updated_by*update_date*batch_lot";
 		$data_array_again = "".$txt_prod_id."*".$cbo_item_category."*2*".$txt_issue_date."*".$cbo_store_name."*".$txt_order_id."*".$txt_wo_batch_no."*".$cbo_uom."*".$txt_issue_qnty."*".$avg_rate."*".$issue_stock_value."*".$cbo_floor."*".$cbo_sewing_line."*".$cbo_machine_name."*".$txt_return_qty."*".$cbo_machine_category."*".$cbo_room."*".$txt_rack."*".$txt_shelf."*".$cbo_bin."*".$cbo_location."*".$cbo_department."*".$cbo_section."*".$cbo_issue_basis."*".$cbo_issue_uom."*".$txt_no_of_qty."*'".$user_id."'*'".$pc_date_time."'*".$txt_lot_no."";
		//echo $field_array_again."<br>".$data_array_again; die("with kakku");
		//$transID = sql_update("inv_transaction",$field_array_again,$data_array_again,"id",$update_id,0);
 		//inventory TRANSACTION table data UPDATE  END----------------------------------------------------------//
		//if LIFO/FIFO then START -----------------------------------------//
		$field_array_mrr = "id,recv_trans_id,issue_trans_id,entry_form,prod_id,issue_qnty,item_return_qty,rate,amount,inserted_by,insert_date";
		$update_array_tran = "balance_qnty*balance_amount*updated_by*update_date";
		$cons_rate=0;
		/*		$updateID_array_tran_up=array();
				$update_data_tran_up=array();
				$update_data_mrr_insert=array();
		*/		$issueQnty = $txt_issue_qnty;
		//$mrrWiseIsID = return_next_id("id", "inv_mrr_wise_issue_details", 1);

		if($isLIFOfifo==2) $cond_lifofifo=" DESC"; else $cond_lifofifo=" ASC";
		$sql = sql_select("select id,cons_rate,balance_qnty,balance_amount from inv_transaction where prod_id=$txt_prod_id and balance_qnty>0 and transaction_type in (1,4,5) and item_category=$cbo_item_category order by id $cond_lifofifo");

 		foreach($sql as $result)
		{
			$mrrWiseIsID = return_next_id_by_sequence("INV_MRR_WISE_ISSUE_PK_SEQ", "inv_mrr_wise_issue_details", $con);
			$issue_trans_id = $result[csf("id")]; // this row will be updated
			if($trans_data_array[$issue_trans_id]['qnty']=="")
			{
				$balance_qnty = $result[csf("balance_qnty")];
				$balance_amount = $result[csf("balance_amount")];
			}
			else
			{
				$balance_qnty = $trans_data_array[$issue_trans_id]['qnty'];
				$balance_amount = $trans_data_array[$issue_trans_id]['amnt'];
			}
			/*$balance_qnty = $result[csf("balance_qnty")];
			$balance_amount = $result[csf("balance_amount")];*/
			$cons_rate = $result[csf("cons_rate")];
			$issueQntyBalance = $balance_qnty-$issueQnty; // minus issue qnty
			$issueStockBalance = $balance_amount-($issueQnty*$cons_rate);
			if($issueQntyBalance>=0)
			{
				$amount = $issueQnty*$cons_rate;
				//for insert
				if($update_data_mrr_insert!="") $update_data_mrr_insert .= ",";
				$update_data_mrr_insert .= "(".$mrrWiseIsID.",".$issue_trans_id.",".$update_id.",265,".$txt_prod_id.",".$issueQnty.",".$txt_return_qty.",".$cons_rate.",".$amount.",'".$user_id."','".$pc_date_time."')";
				//for update
				$updateID_array_tran_up[]=$issue_trans_id;
				$update_data_tran_up[$issue_trans_id]=explode("*",("".$issueQntyBalance."*".$issueStockBalance."*'".$user_id."'*'".$pc_date_time."'"));
				break;
			}
			else if($issueQntyBalance<0)
			{
				$issueQntyBalance  = $issueQnty-$balance_qnty;
				$issueQnty = $balance_qnty;
				$amount = $issueQnty*$cons_rate;

				//for insert
				if($update_data_mrr_insert!="") $update_data_mrr_insert .= ",";
				$update_data_mrr_insert .= "(".$mrrWiseIsID.",".$issue_trans_id.",".$update_id.",265,".$txt_prod_id.",".$issueQnty.",".$txt_return_qty.",".$cons_rate.",".$amount.",'".$user_id."','".$pc_date_time."')";
				//echo "20**".$data_array;die;
				//for update
				$updateID_array_tran_up[]=$issue_trans_id;
				$update_data_tran_up[$issue_trans_id]=explode("*",("0*0*'".$user_id."'*'".$pc_date_time."'"));
				$issueQnty = $issueQntyBalance;
			}
			//$mrrWiseIsID++;
		}

		//end foreach
 		// LIFO/FIFO then END-----------------------------------------------//
		//echo "insert into inv_mrr_wise_issue_details ($field_array_mrr) values $update_data_mrr_insert";die;
		//mrr wise issue data insert here----------------------------//
		/*$mrrWiseIssueID=true;
		if($update_data_mrr_insert!="")
		{
			$mrrWiseIssueID=sql_insert("inv_mrr_wise_issue_details",$field_array_mrr,$update_data_mrr_insert,0);
		}

		//transaction table stock update here------------------------//
		$upTrID=true;
		if(count($updateID_array_tran_up)>0)
		{
 			$upTrID=execute_query(bulk_update_sql_statement("inv_transaction","id",$update_array_tran,$update_data_tran_up,$updateID_array_tran_up),0);
		}*/

 		//****************************************** NEW ENTRY END *****************************************//

		//echo "20**".$query1." && ".$query2." && ".$query3." && ".$rID." && ".$transID." && ".$upTrID." && ".$mrrWiseIssueID." && ".$serialUpdate;
		//mysql_query("ROLLBACK");die;


		//****************************************** All query execute Bellow*****************************************//
		$query1=$query2=$query3=$rID=$transID=$mrrWiseIssueID=$upTrID=$serialUpdate=$serialDelete=true;
		if($before_prod_id==$txt_prod_id)
		{
 			$query1 = sql_update("product_details_master",$update_array_prod,$data_array_prod,"id",$before_prod_id,0);
		}
		else
		{
			$query1=execute_query(bulk_update_sql_statement("product_details_master","id",$update_array_prod,$data_array_prod,$updateID_array_prod),0);
		}

		if(count($updateID_array_trans)>0)//update receive trans row
		{
 			$query2=execute_query(bulk_update_sql_statement("inv_transaction","id",$update_array_trans,$update_data_trans,$updateID_array_trans),0);
		}

		if(count($update_data_trans)>0)
		{
			 $updateIDArray = implode(",",$update_data_trans);
			 $query3 = execute_query("DELETE FROM inv_mrr_wise_issue_details WHERE issue_trans_id=$update_id and entry_form=265",0);
		}
		if(trim(str_replace("'","",$txt_system_id))!="")
		{
			$rID=sql_update("inv_issue_master",$field_array_update_issue,$data_array_update_issue,"id",$txt_system_id,1);
		}
		$transID = sql_update("inv_transaction",$field_array_again,$data_array_again,"id",$update_id,0);
		if($update_data_mrr_insert!="")
		{
			$mrrWiseIssueID=sql_insert("inv_mrr_wise_issue_details",$field_array_mrr,$update_data_mrr_insert,0);
		}
		//transaction table stock update here------------------------//
		if(count($updateID_array_tran_up)>0)
		{
 			$upTrID=execute_query(bulk_update_sql_statement("inv_transaction","id",$update_array_tran,$update_data_tran_up,$updateID_array_tran_up),0);
		}

		$txt_serial_id 	= trim(str_replace("'","",$txt_serial_id));
 		if($txt_serial_id!="")
		{
			$field_array_serial="issue_trans_id*is_issued*updated_by*update_date";
			$before_serial_id=trim(str_replace("'","",$before_serial_id));$txt_serial_id=trim(str_replace("'","",$txt_serial_id));$update_id=trim(str_replace("'","",$update_id));
			if( strpos(trim($txt_serial_no), ",")>0)
			{
				$se_data=explode(",",str_replace("'","",$txt_serial_no));
				if( (count($se_data)<=str_replace("'","",$txt_issue_qnty)))
				{
					if($before_serial_id !="")
					{
						$txt_before_serial_id_arr=explode(",",$before_serial_id);
						if(count($txt_before_serial_id_arr)>0)
						{
							foreach($txt_before_serial_id_arr as $serial_id)
							{
								$update_data_before_serial[$serial_id]=explode("*",("0*0*'".$user_id."'*'".$pc_date_time."'"));
							}
							$serialDelete=execute_query(bulk_update_sql_statement("inv_serial_no_details","id",$field_array_serial,$update_data_before_serial,$txt_before_serial_id_arr),1);
							//$serialDelete=execute_query("update inv_serial_no_details set issue_trans_id=0 , is_issued=0 where id in ($before_serial_id)",0);
						}
					}
					$txt_serial_id_arr=explode(",",$txt_serial_id);
					if(count($txt_serial_id_arr)>0)
					{
						foreach($txt_serial_id_arr as $serial_id)
						{
							$update_data_serial[$serial_id]=explode("*",("".$update_id."*1*'".$user_id."'*'".$pc_date_time."'"));
						}
					}
					$serialUpdate=execute_query(bulk_update_sql_statement("inv_serial_no_details","id",$field_array_serial,$update_data_serial,$txt_serial_id_arr),1);

					//$serialUpdate = execute_query("update inv_serial_no_details set issue_trans_id=$update_id , is_issued=1 where id in ($txt_serial_id)",0);
				}
				else
				{
					echo "50";
					//check_table_status( $_SESSION['menu_id'],0);
					disconnect($con);
					die;
				}
			}
			else
			{
				/*if($before_serial_id !="")
				{
				$serialDelete	=execute_query("update inv_serial_no_details set issue_trans_id=0 , is_issued=0 where id in ($before_serial_id)",0);
				}

				$serialUpdate 	= execute_query("update inv_serial_no_details set issue_trans_id=$update_id , is_issued=1 where id in ($txt_serial_id)",0);*/

				if($before_serial_id !="")
				{
					//echo "nahid";die;
					$txt_before_serial_id_arr=explode(",",$before_serial_id);
					if(count($txt_before_serial_id_arr)>0)
					{
						foreach($txt_before_serial_id_arr as $serial_id)
						{
							$update_data_before_serial[$serial_id]=explode("*",("0*0*'".$user_id."'*'".$pc_date_time."'"));
						}
						$serialDelete=execute_query(bulk_update_sql_statement("inv_serial_no_details","id",$field_array_serial,$update_data_before_serial,$txt_before_serial_id_arr),1);
						//$serialDelete=execute_query("update inv_serial_no_details set issue_trans_id=0 , is_issued=0 where id in ($before_serial_id)",0);
					}
				}
				//echo $serialDelete;die;
				$txt_serial_id_arr=explode(",",$txt_serial_id);
				if(count($txt_serial_id_arr)>0)
				{
					foreach($txt_serial_id_arr as $serial_id)
					{
						$update_data_serial[$serial_id]=explode("*",("".$update_id."*1*'".$user_id."'*'".$pc_date_time."'"));
					}
				}
				$serialUpdate=execute_query(bulk_update_sql_statement("inv_serial_no_details","id",$field_array_serial,$update_data_serial,$txt_serial_id_arr),1);
			}
		}

		//$query1 $transID $mrrWiseIssueID
		//echo $query1 ."&&". $query2 ."&&". $query3 ."&&". $rID ."&&". $transID ."&&". $mrrWiseIssueID ."&&". $upTrID ."&&". $serialUpdate  ."&&". $serialDelete;die;
		//release lock table
		//check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($query1 &&  $query2 && $query3 && $rID && $transID && $mrrWiseIssueID && $upTrID && $serialUpdate  && $serialDelete)
			{
				mysql_query("COMMIT");
				echo "1**".str_replace("'","",$txt_system_no)."**".str_replace("'","",$txt_system_id)."**".$req_id;
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$txt_system_no)."**".str_replace("'","",$txt_system_id)."**".$req_id;
			}
		}
		//$query1=$query2=$query3=$rID=$transID=$mrrWiseIssueID=$upTrID=$serialUpdate=$serialDelete
		if($db_type==2 || $db_type==1 )
		{
			if($query1 &&  $query2 && $query3 && $rID && $transID && $mrrWiseIssueID && $upTrID && $serialUpdate  && $serialDelete)
			{
				oci_commit($con);
				echo "1**".str_replace("'","",$txt_system_no)."**".str_replace("'","",$txt_system_id)."**".$req_id;
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_system_no)."**".str_replace("'","",$txt_system_id)."**".$req_id;
			}
		}
		//check_table_status( $_SESSION['menu_id'],0);
		disconnect($con);
		die;
 	}
	else if ($operation==2) // Delete Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		// master table delete here---------------------------------------
		$mst_id = str_replace("'","",$txt_system_id);
		if($mst_id=="" || $mst_id==0)
		{
			echo "16**Delete not allowed. Problem occurred"; disconnect($con); die;
		}
		else
		{
			$update_id = str_replace("'","",$update_id);
			$product_id = str_replace("'","",$current_prod_id);
			if( str_replace("'","",$update_id) == "" )
			{
				echo "16**Delete not allowed. Problem occurred";disconnect($con); die;
			}

			//echo "10**select id from inv_transaction where transaction_type in(2,3,6) and prod_id=$product_id and status_active=1 and is_deleted=0 and id >$update_id"; die;
			$chk_next_transaction=return_field_value("id","inv_transaction","transaction_type in(1,2,3,4,5,6) and prod_id=$product_id and status_active=1 and is_deleted=0 and issue_trans_id >$update_id ","id");
			if($chk_next_transaction !="")
			{
				echo "18**Delete not allowed.This item is used in another transaction"; 
				disconnect($con); 
				die;
			}
			else
			{
				//echo "10**select id from inv_mrr_wise_issue_details where prod_id=$product_id and status_active=1 and is_deleted=0 and issue_trans_id=$update_id"; die;
				$mrr_table_id=return_field_value("id","inv_mrr_wise_issue_details","prod_id=$product_id and status_active=1 and is_deleted=0 and issue_trans_id=$update_id ","id");

				$sql = sql_select("select a.prod_id,a.cons_quantity,a.cons_rate,a.cons_amount,b.current_stock,b.stock_value from inv_transaction a, product_details_master b  where a.status_active=1 and a.id=$update_id and a.prod_id=b.id");

				$before_prod_id=$before_receive_qnty=$before_rate=$beforeAmount=$before_brand="";
				$beforeStock=$beforeStockValue=0;
				foreach( $sql as $row)
				{
					$before_prod_id 		= $row[csf("prod_id")];
					$before_receive_qnty 	= $row[csf("cons_quantity")]; //stock qnty
					$before_rate 			= $row[csf("cons_rate")];
					$beforeAmount			= $row[csf("cons_amount")]; //stock value
					$beforeStock			=$row[csf("current_stock")];
					$beforeStockValue		=$row[csf("stock_value")];
					//$beforeAvgRate			=$row[csf("avg_rate_per_unit")];
				}
				//stock value minus here---------------------------//
				$adj_beforeStock			=$beforeStock+$before_receive_qnty;
				$adj_beforeStockValue		=$beforeStockValue+$beforeAmount;
				//$adj_beforeAvgRate			=number_format(($adj_beforeStockValue/$adj_beforeStock),$dec_place[3],'.','');

				$field_array_product="current_stock*stock_value*updated_by*update_date";
				$data_array_product = "".$adj_beforeStock."*".number_format($adj_beforeStockValue,$dec_place[4],'.','')."*'".$user_id."'*'".$pc_date_time."'";

				$field_array_trans="updated_by*update_date*status_active*is_deleted";
				$data_array_trans="".$user_id."*'".$pc_date_time."'*0*1";

				$field_array_mrr="updated_by*update_date*status_active*is_deleted";
				$data_array_mrr="".$user_id."*'".$pc_date_time."'*0*1";

				$rID=sql_update("inv_transaction",$field_array_trans,$data_array_trans,"id",$update_id,1);
				$rID2=sql_update("product_details_master",$field_array_product,$data_array_product,"id",$product_id,1);
				$rID3=sql_update("inv_mrr_wise_issue_details",$field_array_mrr,$data_array_mrr,"id",$mrr_table_id,1);
			}
		}

		if($db_type==0)
		{
			if($rID && $rID2 && $rID3)
			{
				mysql_query("COMMIT");
				echo "2**".str_replace("'","",$txt_system_no)."**".str_replace("'","",$txt_system_id)."**".$req_id;
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "10**".str_replace("'","",$txt_system_no)."**".str_replace("'","",$txt_system_id)."**".$req_id;
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2 && $rID3)
			{
				oci_commit($con);
				echo "2**".str_replace("'","",$txt_system_no)."**".str_replace("'","",$txt_system_id)."**".$req_id;
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$txt_system_no)."**".str_replace("'","",$txt_system_id)."**".$req_id;
			}
		}
		disconnect($con);
		die;
	}
}
if($action=="search_by_drop_down")
{
	echo create_drop_down( "cbo_item_category", 150, $item_category,"", 1, "-- Select --", 0, "", 1,"" );
}

if($action=="mrr_popup")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(sys_id)
		{
			$("#hidden_sys_id").val(sys_id); // mrr number
			parent.emailwindow.hide();
		}
	</script>
	</head>
	<body>
	<div align="center" style="width:100%;" >
	<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
		<table width="780" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
				<thead>
					<tr>
						<th width="100">Search By</th>
						<th width="250" align="center" id="search_by_td_up">Enter Issue No</th>
						<th width="200">Date Range</th>
						<th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>
							<?
								$search_by = array(1=>'Issue No',2=>'Req No',3=>'Challan No',4=>'Item Category');
								$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../../') ";
								echo create_drop_down( "cbo_search_by", 150, $search_by,"",0, "--Select--", "",$dd,0 );
							?>
						</td>
						<td width="" align="center" id="search_by_td">
							<input type="text" style="width:230px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
						</td>
						<td align="center">
							<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" />
							<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date" />
						</td>
						<td align="center">
							<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>+'_'+'<? echo $item_cat; ?>'+'_'+document.getElementById('cbo_year_selection').value, 'create_mrr_search_list_view', 'search_div', 'raw_material_item_issue_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
						</td>
				</tr>
				<tr>
					<td align="center" height="40" valign="middle" colspan="5">
						<? echo load_month_buttons(1);  ?>
					<!-- Hidden field here -->
						<input type="hidden" id="hidden_sys_id" value="hidden_sys_id" />
						<!-- END  -->
					</td>
				</tr>
				</tbody>
			</tr>
			</table>
			<br>
			<div align="center" valign="top" id="search_div"> </div>
			</form>
	</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}

if($action=="create_mrr_search_list_view")
{
	$ex_data = explode("_",$data);
 	$txt_search_by = $ex_data[0];
	$txt_search_common = $ex_data[1];
	$fromDate = $ex_data[2];
	$toDate = $ex_data[3];
	$company = $ex_data[4];
	$item_cat = $ex_data[5];
	$insert_year = $ex_data[6];
 	$company_arr = return_library_array("select id, company_name from lib_company",'id','company_name');
 	$store_arr = return_library_array("select id, store_name from lib_store_location",'id','store_name');
 	$section_arr = return_library_array("select id, section_id from trims_raw_mat_requisition_mst",'id','section_id');


 	$sql_cond="";
	if($fromDate!="" && $toDate!="" )
	{
		if($db_type==0)
		{
			$sql_cond .= " and a.issue_date  between '".change_date_format($fromDate,'yyyy-mm-dd')."' and '".change_date_format($toDate,'yyyy-mm-dd')."'";
		}
		else
		{
			$sql_cond .= " and a.issue_date  between '".change_date_format($fromDate,'yyyy-mm-dd','',-1)."' and '".change_date_format($toDate,'yyyy-mm-dd','',-1)."'";
		}
	}
 	if($company!="" && $company*1!=0) $sql_cond .= " and a.company_id='$company'";


 	if($txt_search_common!="" || $txt_search_common!=0)
	{
		if($txt_search_by==1)
		{
			$sql_cond .= " and a.issue_number like '%$txt_search_common%'";
		}
		else if($txt_search_by==2)
		{
			$sql_cond .= " and a.req_no like '%$txt_search_common%'";
 		}
		else if($txt_search_by==3)
		{
			$sql_cond .= " and a.challan_no like '%$txt_search_common%'";
		}
	}
	//echo "SELECT unit_id as company_id, store_location_id, item_cate_id, supplier_id FROM user_passwd where id='$user_id'";die;
	$userCredential = sql_select("SELECT unit_id as company_id, store_location_id, item_cate_id, supplier_id FROM user_passwd where id='$user_id'");
	//$cre_company_id = $userCredential[0][csf('company_id')];
	$cre_supplier_id = $userCredential[0][csf('supplier_id')];
	$cre_store_location_id = $userCredential[0][csf('store_location_id')];
	$cre_item_cate_id = $userCredential[0][csf('item_cate_id')];

	$credientian_cond="";
	//if($cre_company_id>0) $credientian_cond=" and a.company_id in($cre_company_id)";
	//if($cre_supplier_id!="") $credientian_cond.=" and a.supplier_id in($cre_supplier_id)";
	if($cre_store_location_id!="") $credientian_cond.=" and b.store_id in($cre_store_location_id)";
	if($cre_item_cate_id!="") $credientian_cond.=" and b.item_category in($cre_item_cate_id)";

	if($db_type==0){$sql_cond .=" and year(a.insert_date)=".$insert_year."";}
	else{$sql_cond .=" and to_char(a.insert_date,'YYYY')=".$insert_year."";}

	/*$sql = "select a.id, a.issue_number,a.issue_basis,a.issue_purpose,a.entry_form,a.company_id,a.location_id,a.supplier_id,a.store_id,a.issue_date
			from inv_issue_master a, inv_transaction b
			where a.id=b.mst_id and a.status_active=1 and a.entry_form=265 $sql_cond $credientian_cond
			group by a.id, a.issue_number,a.issue_basis,a.issue_purpose,a.entry_form,a.company_id,a.location_id,a.supplier_id,a.store_id,a.issue_date
			order by a.issue_number";*/

	if($db_type==0)
	{
		$sql = "SELECT a.id, a.issue_number,a.issue_basis,a.issue_purpose,a.entry_form,a.company_id,a.location_id,a.supplier_id,a.store_id,a.issue_date,a.req_no,a.req_id,group_concat(b.item_category) as item_cat_id, sum(b.cons_quantity) as cons_quantity
			from inv_issue_master a, inv_transaction b
			where a.id=b.mst_id and a.status_active=1 and b.status_active=1 and a.entry_form=265 and b.transaction_type=2 and b.item_category in(101,22) $sql_cond $credientian_cond
			group by a.id, a.issue_number,a.issue_basis,a.issue_purpose,a.entry_form,a.company_id,a.location_id,a.supplier_id,a.store_id,a.issue_date,a.req_no,a.req_id
			order by a.id desc";
	}
	else
	{
		$sql = "SELECT a.id, a.issue_number,a.issue_basis,a.issue_purpose,a.entry_form,a.company_id,a.location_id,a.supplier_id,a.store_id,a.issue_date, a.req_no,a.req_id,listagg(cast(b.item_category as varchar(4000)),',') within group (order by b.item_category) as item_cat_id, sum(b.cons_quantity) as cons_quantity
			from inv_issue_master a, inv_transaction b
			where a.id=b.mst_id and a.status_active=1 and b.status_active=1 and a.entry_form=265 and b.transaction_type=2 and b.item_category in(101,22) $sql_cond $credientian_cond
			group by a.id, a.issue_number,a.issue_basis,a.issue_purpose,a.entry_form,a.company_id,a.location_id,a.supplier_id,a.store_id,a.issue_date,a.req_no,a.req_id
			order by a.id desc";
	}

	// echo $sql;die;
	$result = sql_select( $sql );
	?>
    	<div>
            <div style="width:920px;">
                <table cellspacing="0" cellpadding="0" width="920" class="rpt_table" rules="all" border="1">
                    <thead>
						<tr>
							<th colspan="9"><?php echo create_drop_down("cbo_string_search_type", 130, $string_search_type, '', 1, "-- Searching Type --",4); ?></th>
						</tr>
						<tr>
							<th width="50">SL</th>
							<th width="120">Issue No</th>
							<th width="100">Requisition No</th>
							<th width="100">Item Category</th>
							<th width="60">Date</th>
							<th width="150">Purpose</th>
							<th width="120">Req No</th>
							<th width="100">Section</th>
							<th >Issue Qnty</th>
						</tr>
                    </thead>
                </table>
             </div>
            <div style="width:920px;overflow-y:scroll; min-height:200px; max-height:210px;" id="search_div" >
                <table cellspacing="0" cellpadding="0" width="900" class="rpt_table" id="list_view"  rules="all" border="1">
        <?
            $i=1;
            foreach( $result as $row )
			{
                if ($i%2==0)
                    $bgcolor="#E9F3FF";
                else
                    $bgcolor="#FFFFFF";

				//$issuQnty = return_field_value("sum(cons_quantity) as cons_quantity","inv_transaction","mst_id=".$row[csf("id")]." and transaction_type=2 and item_category not in (1,2,3,5,6,7,12,13,14) group by mst_id","cons_quantity");
				$issuQnty =	$row[csf("cons_quantity")];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"  onclick="js_set_value('<? echo $row[csf("id")];?>');">
					<td width="50" align="center"><? echo $i; ?></td>
					<td width="120"><p><? echo $row[csf("issue_number")];?></p></td>
					<td width="100"><p><? echo $row[csf("req_no")];?></p></td>
					<td width="100"><p>
					<?
					$item_cat_arr=array_unique(explode(",",$row[csf("item_cat_id")]));
					$all_item_cat="";
					foreach($item_cat_arr as $cat_id)
					{
						$all_item_cat.=$item_category[$cat_id].",";
					}
					$all_item_cat=chop($all_item_cat,",");
					echo $all_item_cat;
					?></p></td>
					<td width="60"><p><? echo $row[csf("issue_date")]; ?></p></td>
					<td width="150"><p><? echo $general_issue_purpose[$row[csf("issue_purpose")]]; ?></p></td>
					<td width="120"><p><? echo $row[csf("req_no")]; ?></p></td>
					<td width="100"><p><? echo $trims_section[$section_arr[$row[csf("req_id")]]]; ?></p></td>
					<td  align="right"><p><? echo number_format($row[csf("cons_quantity")],4); ?></td>
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


if($action=="populate_data_from_data")
{
	$company_array = return_library_array("select id, company_name from lib_company", "id", "id,company_name");
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier", "id", "id,supplier_name");

	$sql = "SELECT id,issue_number,issue_purpose,loan_party,company_id,issue_date,challan_no,req_no,req_id,knit_dye_source,knit_dye_company, remarks,issue_basis, section_id from inv_issue_master where id='$data' and entry_form=265";
	//echo $sql; 
	$res = sql_select($sql);
	//print_r($company_array); die;
	foreach($res as $row)
	{
		echo "$('#txt_system_no').val('".$row[csf("issue_number")]."');\n";
		echo "$('#txt_system_id').val(".$row[csf("id")].");\n";
		echo "$('#cbo_company_id').val(".$row[csf("company_id")].");\n";
  		echo "$('#cbo_issue_basis').val('".$row[csf("issue_basis")]."');\n";
		echo "$('#cbo_issue_purpose').val('".$row[csf("issue_purpose")]."');\n";
  		echo "$('#cbo_loan_party').val('".$row[csf("loan_party")]."');\n";
 		echo "$('#txt_issue_date').val('".change_date_format($row[csf("issue_date")])."');\n";
 		echo "$('#txt_issue_req_no').val('".$row[csf("req_no")]."');\n";
 		echo "$('#hidden_issue_req_id').val('".$row[csf("req_id")]."');\n";
 		echo "$('#txt_challan_no').val('".$row[csf("challan_no")]."');\n";
  		echo "$('#cbo_issue_source').val('".$row[csf("knit_dye_source")]."');\n";
  		echo "load_drop_down( 'requires/raw_material_item_issue_controller', '".$row[csf("knit_dye_source")]."'+'**'+".$row[csf("company_id")]."+'**'+'".$row[csf("issue_purpose")]."', 'load_drop_down_issue_to', 'cbo_issue_to' );\n";
  		echo "$('#cbo_issue_to').val('".$row[csf("knit_dye_company")]."');\n";
  		echo "$('#cbo_section_mst').val('".$row[csf("section_id")]."');\n";
 		echo "$('#txt_remarks').val('".$row[csf("remarks")]."');\n";
		//clear child form
		echo "$('#tbl_child').find('select,input').val('');\n";

		if($row[csf("issue_purpose")]==5){

			echo "$('#cbo_loan_party').parent().prev('td').css('color', 'blue');\n";
	    }
	    else{

	    	echo "$('#cbo_loan_party').parent().prev('td').css('color', 'black');\n";
	    }
  	}
	exit();
}



if( $action=='order_dtls_list_view_old' ) 
{
	$data=explode('**',$data);
	$brand_arr=return_library_array( "select id, brand_name from product_details_master",'id','brand_name');
	
	$sqlBreak_result =sql_select("SELECT  a.id, a.mst_id, a.order_id, a.job_no_mst, a.book_con_dtls_id, a.product_id, a.description, a.specification, a.pcs_unit, a.cons_qty, a.process_loss, a.process_loss_qty, a.req_qty, a.remarks,b.unit_of_measure as unit,c.break_id as subcon_break_ids from trims_job_card_breakdown a , product_details_master b, trims_job_card_dtls c where a.product_id=b.id and c.id=a.mst_id and c.status_active=1 and c.is_deleted=0  and a.status_active=1 and a.is_deleted=0 and a.job_no_mst='$data[1]'");
	//$break_arr=array(); 
	$break_arr_summery=array();
	foreach($sqlBreak_result as $row)
	{
		$break_arr_summery[$row[csf('description')]][$row[csf('unit')]][$row[csf('product_id')]]['qty']=$row[csf('req_qty')];
		//$break_arr_summery[$row[csf('description')]][$row[csf('unit')]][$row[csf('product_id')]]['ids'].=$row[csf('id')].',';
		$break_arr_summery[$row[csf('description')]][$row[csf('unit')]][$row[csf('product_id')]]['mst_ids'].=$row[csf('mst_id')].',';
		$break_arr_summery[$row[csf('description')]][$row[csf('unit')]][$row[csf('product_id')]]['subcon_break_ids'].=$row[csf('subcon_break_ids')].',';
	}

	$iss_sql= "SELECT b.prod_id, sum(b.cons_quantity) as cons_quantity from inv_issue_master a, inv_transaction b where a.id=b.mst_id and a.req_id=$data[2] and b.transaction_type=2 and a.issue_basis=15 and a.entry_form=265 and a.status_active=1 and b.status_active=1 group by b.prod_id";
	$iss_data_array=sql_select($iss_sql);
	foreach($iss_data_array as $row){
		$issue_qty_arr[$row[csf("prod_id")]]+=$row[csf("cons_quantity")];
	}

	$job_qnty_arr=array();
	$qty_sql ="SELECT a.id, a.booked_qty from subcon_ord_breakdown a where a.booked_qty is not null and a.booked_qty!=0";
	$qty_sql_res=sql_select($qty_sql);
	foreach ($qty_sql_res as $row)
	{
		$job_qnty_arr[$row[csf("id")]]['qnty']=$row[csf("booked_qty")];
	}
	unset($qty_sql_res);
	//echo "<pre>";
	//print_r($job_qnty_arr);
	?>
   	<div style="width:430px;">
	    <table width="410" cellspacing="0" cellpadding="0" border="0" rules="all" class="rpt_table" align="left">
	    	<thead>
	        	<tr>
					<th width="30">SL</th>
					<th width="120">Item</th>
					<th width="100">Brand Name</th>
					<th width="60">UOM</th>
					<th width="100">Total Req.Qty</th>
					<th width="60">Cum. Issue Qty.</th>
	           		<th>Balance</th>
				</tr>
			<tbody>  
	            <?
				$k=1; if ($k%2==0) $bggcolor="#E9F3FF"; else $bggcolor="#FFFFFF";
				foreach($break_arr_summery as $item=>$break_arr_val)
				{
					foreach($break_arr_val as $uom=>$brand_val)
					{
						$jobQnty=0; $jobCardQnty=0; 
						foreach($brand_val as $product_id=>$row)
						{
							//echo "<pre>";
							//print_r($row);
							$subcon_break_ids=explode(",",chop($row['subcon_break_ids'],','));
							for($j=0; $j<count($subcon_break_ids); $j++)
							{
								$jobQnty +=$job_qnty_arr[$subcon_break_ids[$j]]['qnty'];
							}
							$jobQnty=round($jobQnty); 
							$jobCardQnty=$jobQnty*$row['qty'];
							$balance= $jobCardQnty-$issue_qty_arr[$product_id];
							$balance=number_format($balance,2);
							$balance=str_replace(",","",trim($balance));
							?>
							<tr bgcolor="<? echo $bggcolor; ?>" onClick='get_php_form_data("<? echo chop($row['mst_ids'],',').'**'.$product_id.'**'.$balance.'**1';?>","child_form_input_job_card_data","requires/raw_material_item_issue_controller")' style="cursor:pointer" >
								<td width="30"><? echo $k; ?></td>
								<td width="120"><p><? echo $item; ?></p></td>
								<td width="100"><p><? echo $brand_arr[$product_id]; ?></p></td>
								<td width="60"><p><? echo $unit_of_measurement[$uom]; ?></p></td>
								<td width="100"><p><? echo $jobCardQnty;//$qnty;?> </p></td>
								<td width="60"><p><? echo number_format($issue_qty_arr[$product_id],2);?> </p></td>
								<td><p><? echo $balance;?> </p></td>
							</tr>
							<?
							$k++;
						}
					}
				}
				?>
			</tbody>	 
	   	</table>
 	</div>
<?
exit();
}


if( $action=='order_dtls_list_view' ) 
{ //echo $data; die;
	$data=explode('**',$data);
	$brand_arr=return_library_array( "select id, brand_name from product_details_master",'id','brand_name');
	
	$sqlDtls_result =sql_select("select  id, mst_id, job_no_mst, receive_dtls_id, booking_dtls_id, book_con_dtls_id, break_id as subcon_break_ids, buyer_po_id,buyer_po_no, buyer_style_ref, item_description, color_id, size_id, uom, job_quantity,  impression, material_color,conv_factor from trims_job_card_dtls where mst_id=$data[2] and status_active=1 and is_deleted=0");

	$sqlBreak_result =sql_select("select  a.id, a.mst_id, a.order_id, a.job_no_mst, a.book_con_dtls_id, a.product_id, a.description, a.specification, a.pcs_unit, a.cons_qty, a.process_loss, a.process_loss_qty, a.req_qty, a.remarks,b.unit_of_measure as unit from trims_job_card_breakdown a , product_details_master b where a.product_id=b.id and a.job_no_mst='$data[1]' and a.status_active=1 and a.is_deleted=0");
	$break_arr=array(); $break_arr_summery=array();
	foreach($sqlBreak_result as $row)
	{
		$break_arr[$row[csf('mst_id')]]['info'].=$row[csf('description')]."_".$row[csf('pcs_unit')]."_".$row[csf('unit')]."_".$row[csf('req_qty')]."_".$row[csf('process_loss')]."_".$row[csf('remarks')]."_".$row[csf('product_id')]."**";
		$break_arr_summery[$row[csf('description')]][$row[csf('unit')]][$row[csf('product_id')]]+=$row[csf('req_qty')];
	}

	$iss_sql= "SELECT b.prod_id, sum(b.cons_quantity) as cons_quantity from inv_issue_master a, inv_transaction b where a.id=b.mst_id and a.req_id=$data[2] and b.transaction_type=2 and a.issue_basis=15 and a.entry_form=265 and a.status_active=1 and b.status_active=1 group by b.prod_id";
	$iss_data_array=sql_select($iss_sql);
	foreach($iss_data_array as $row){
		$issue_qty_arr[$row[csf("prod_id")]]+=$row[csf("cons_quantity")];
	}

	$qty_sql= "select b.id as receive_details_id,a.id,a.description,a.color_id,a.size_id,b.sub_section,b.booked_uom,b.booked_conv_fac, a.booked_qty  from subcon_ord_dtls b ,subcon_ord_breakdown a where a.mst_id=b.id  and b.job_no_mst=a.job_no_mst and a.booked_qty is not null and a.booked_qty!=0 and a.status_active=1 and a.is_deleted=0";
	
	$qty_sql_res=sql_select($qty_sql);
	foreach ($qty_sql_res as $row)
	{
		//$job_qnty_arr[$row[csf("id")]]['qnty']=$row[csf("booked_qty")];
		$job_qnty_arr[$row[csf("id")]][$row[csf("booked_conv_fac")]]['qnty']=$row[csf("booked_qty")];
	}
	unset($qty_sql_res); $l=1;
	foreach($sqlDtls_result as $row)
	{
		$subcon_break_ids=explode(",",chop($row[csf("subcon_break_ids")],','));
		$job_Qnty=0;
		for($j=0; $j<count($subcon_break_ids); $j++)
		{
			$job_Qnty +=$job_qnty_arr[$subcon_break_ids[$j]][$row[csf("conv_factor")]]['qnty'];
		}
		//print_r($job_qnty_arr);
	
		$joborderquantity=$job_Qnty;
		$jobQnty=number_format($job_Qnty,4);
		$break_data=chop($break_arr[$row[csf('id')]]['info'],"**");
		//echo $break_data.'==';
		$break_info=explode('**',$break_data);

		$pcs_unit='';  $req_quantity='';
		for($j=0; $j<count($break_info); $j++)
		{
			if ($j%2==0) $dtlbgcolor="#f0fbfc"; else $dtlbgcolor="#fcf5f8";
			$break_infos=explode('_',$break_info[$j]);
			$pcs_unit=$job_Qnty/$break_infos[3];
			$req_quantity=$job_Qnty*$break_infos[3];
			$req_quantity_arr[$break_infos[6]] +=$req_quantity;
			$mst_id_arr[$break_infos[6]] .=$row[csf('id')].',';
		}
		$l++;
	}
	/*echo "<pre>";
	print_r($job_qnty_arr);*/
	?>
   	<div style="width:430px;">
	    <table width="410" cellspacing="0" cellpadding="0" border="0" rules="all" class="rpt_table" align="left">
	    	<thead>
	        	<tr>
					<th width="30">SL</th>
					<th width="120">Item</th>
					<th width="100">Brand Name</th>
					<th width="60">UOM</th>
					<th width="100">Total Req.Qty</th>
					<th width="60">Cum. Issue Qty.</th>
	           		<th>Balance</th>
				</tr>
			<tbody>  
	            <?
				$k=1; if ($k%2==0) $bggcolor="#E9F3FF"; else $bggcolor="#FFFFFF";
				foreach($break_arr_summery as $item=>$break_arr_val)
				{
					foreach($break_arr_val as $uom=>$brand_val)
					{
						$jobQnty=0; $jobCardQnty=0; 
						foreach($brand_val as $product_id=>$row)
						{
							//echo "<pre>";
							//print_r($row);
							$subcon_break_ids=explode(",",chop($row['subcon_break_ids'],','));
							/*for($j=0; $j<count($subcon_break_ids); $j++)
							{
								$jobQnty +=$job_qnty_arr[$subcon_break_ids[$j]]['qnty'];
							}*/

							for($j=0; $j<count($subcon_break_ids); $j++)
							{
								//echo $subcon_break_ids[$j].'=='.$row['conv_factor'].'=='.$job_qnty_arr[$subcon_break_ids[$j]][$row['conv_factor']]['qnty'].'++';
								$jobQnty +=$job_qnty_arr[$subcon_break_ids[$j]][$row['conv_factor']]['qnty'];
							}
							//echo $jobQnty.'=='; 
							$jobQnty=round($jobQnty); 
							$jobCardQnty=$jobQnty*$row['qty'];
							$balance= $req_quantity_arr[$product_id] -$issue_qty_arr[$product_id];
							$balance=number_format($balance,2);
							$balance=str_replace(",","",trim($balance));
							$mst_ids=chop($mst_id_arr[$product_id],',');
							$mst_ids=implode(",",array_unique(explode(",",$mst_ids)));
							?>
							<tr bgcolor="<? echo $bggcolor; ?>" onClick='get_php_form_data("<? echo $mst_ids.'**'.$product_id.'**'.$balance.'**1'.'**'.$data[3];?>","child_form_input_job_card_data","requires/raw_material_item_issue_controller")' style="cursor:pointer" >
								<td width="30"><? echo $k; ?></td>
								<td width="120"><p><? echo $item; ?></p></td>
								<td width="100"><p><? echo $brand_arr[$product_id]; ?></p></td>
								<td width="60"><p><? echo $unit_of_measurement[$uom]; ?></p></td>
								<td width="100"><p><? echo $req_quantity_arr[$product_id] ;//$jobCardQnty;//$qnty;?> </p></td>
								<td width="60"><p><? echo number_format($issue_qty_arr[$product_id],2);?> </p></td>
								<td><p><? echo $balance;?> </p></td>
							</tr>
							<?
							$k++;
						}
					}
				}
				?>
			</tbody>	 
	   	</table>
 	</div>
	<?
	exit();
}

if( $action=='order_dtls_list_view_req' ) 
{ 
	//echo $data; die;
	$data=explode('**',$data);
	$brand_arr=return_library_array( "select id, brand_name from product_details_master",'id','brand_name');
	$color_library_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");

	
	$trans_id=$data[4];
	$current_prod_id=$data[5];
	$req_id=$data[2];
	$variable_lot=$data[8];
	$up_cond="";
	if($trans_id!="") $up_cond=" and b.id <> $trans_id";
				
	$prev_req_rcv=sql_select("select b.prod_id, b.batch_lot, b.cons_quantity as cons_quantity from inv_issue_master a, inv_transaction b where a.id=b.mst_id and a.entry_form=265 and b.transaction_type=2 and a.issue_basis=7 and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.req_id=$req_id $up_cond");
		$issue_qty_arr=array();
	foreach($prev_req_rcv as $row)
	{
		if($variable_lot==1) $batch_lot=$row[csf("batch_lot")]; else $batch_lot="";
		$issue_qty_arr[$row[csf("prod_id")]][$batch_lot]+=$row[csf("cons_quantity")];
	}
	
	$issue_rtn_sql = "SELECT b.prod_id as PROD_ID, b.batch_lot as BATCH_LOT, b.cons_quantity as CONS_QUANTITY from inv_issue_master a, inv_transaction b,inv_receive_master c
	where a.req_id=$req_id and a.id=b.issue_id and b.mst_id=c.id and a.issue_basis=7 and a.entry_form=265 and c.entry_form=266 and a.status_active=1 and b.status_active=1 and c.status_active=1 and b.transaction_type=4 and b.item_category in (101,22) ";
	// echo $issue_rtn_sql;
	$issue_rtn_sql_res=sql_select($issue_rtn_sql);
	foreach($issue_rtn_sql_res as $row)
	{
		if($variable_lot==1) $batch_lot=$row["BATCH_LOT"]; else $batch_lot="";
	 	$issue_rtn_qty_arr[$row['PROD_ID']][$batch_lot]+=$row['CONS_QUANTITY'];
	}
	
	if($db_type==0) $mst_id_cond="group_concat(d.mst_id)";
	else if($db_type==2) $mst_id_cond="listagg(d.mst_id,',') within group (order by d.mst_id)";
	
	/*$sql = "SELECT b.id as update_id, b.mst_id, b.item_group_id, b.uom as unit, b.requisition_qty, d.product_id,d.specification,d.description,$mst_id_cond as job_card_dtls_id, b.lot
	from trims_raw_mat_requisition_mst a, trims_raw_mat_requisition_dtls b, trims_job_card_dtls c, trims_job_card_breakdown d
	where a.id=$data[2] and a.entry_form=427 and a.id=b.mst_id and a.job_no=d.job_no_mst  and a.job_no = c.job_no_mst and b.product_id = d.product_id and c.id=d.mst_id  and b.job_no=d.job_no_mst  and b.job_no=c.job_no_mst  and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 group by b.id, b.mst_id, b.item_group_id, b.uom, b.requisition_qty, d.product_id,d.specification,d.description,  b.lot 
	union all
	SELECT b.id as update_id, b.mst_id, b.item_group_id, b.uom as unit, b.requisition_qty, c.id as product_id, null as specification ,null as description, null as job_card_dtls_id, b.lot
	from trims_raw_mat_requisition_mst a, trims_raw_mat_requisition_dtls b ,product_details_master c, inv_transaction d
	where c.id=d.prod_id and a.id=$data[2] and a.entry_form=501 and a.id=b.mst_id and b.product_id = c.id and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1";*/
	
	
	
	$sql = "SELECT b.id as update_id, b.mst_id, b.item_group_id, b.uom as unit, b.requisition_qty, d.product_id,d.specification,d.description,$mst_id_cond as job_card_dtls_id, b.lot, b.color_id
	from trims_raw_mat_requisition_mst a, trims_raw_mat_requisition_dtls b, trims_job_card_dtls c, trims_job_card_breakdown d
	where a.id=$data[2] and a.entry_form=427 and a.id=b.mst_id and a.job_no=d.job_no_mst  and a.job_no = c.job_no_mst and b.product_id = d.product_id and c.id=d.mst_id  and b.job_no=d.job_no_mst  and b.job_no=c.job_no_mst  and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 
	group by b.id, b.mst_id, b.item_group_id, b.uom, b.requisition_qty, d.product_id,d.specification,d.description,  b.lot, b.color_id
	union all
	SELECT b.id as update_id, b.mst_id, b.item_group_id, b.uom as unit, b.requisition_qty, b.product_id as product_id, null as specification ,c.item_description as description, null as job_card_dtls_id, b.lot, b.color_id
	from trims_raw_mat_requisition_mst a, trims_raw_mat_requisition_dtls b ,product_details_master c
	where a.id=$data[2] and a.entry_form=501 and a.id=b.mst_id and b.product_id = c.id and a.status_active=1 and b.status_active=1 and c.status_active=1 ";
	// echo $sql;
	$data_array=sql_select($sql);
	foreach($data_array as $row)
	{
		$break_arr_summery[$row[csf('description')]][$row[csf('unit')]][$row[csf('product_id')]][$row[csf('lot')]]['total_req_qty']+=$row[csf('requisition_qty')];
		$break_arr_summery[$row[csf('description')]][$row[csf('unit')]][$row[csf('product_id')]][$row[csf('lot')]]['job_card_dtls_id'].=$row[csf('job_card_dtls_id')].',';
		$break_arr_summery[$row[csf('description')]][$row[csf('unit')]][$row[csf('product_id')]][$row[csf('lot')]]['lot']=$row[csf('lot')];
		$break_arr_summery[$row[csf('description')]][$row[csf('unit')]][$row[csf('product_id')]][$row[csf('lot')]]['color_id']=$row[csf('color_id')];
	}
	//print_r($break_arr_summery);
	
	$l=1;
	?>
   	<div style="width:780px;">
	    <table width="770" cellspacing="0" cellpadding="0" border="0" rules="all" class="rpt_table" align="left">
	    	<thead>
	        	<tr>
					<th width="30">SL</th>
					<th width="120">Item</th>
					<th width="70">Color</th>
					<th width="70">Lot</th>
					<th width="100">Brand Name</th>
					<th width="60">UOM</th>
					<th width="100">Total Req.Qty</th>
					<th width="60">Cum. Issue Qty.</th>
					<th width="90">Cum. Issue Return Qty.</th>
	           		<th>Balance</th>
				</tr>
			<tbody>  
	            <?
				$k=1; if ($k%2==0) $bggcolor="#E9F3FF"; else $bggcolor="#FFFFFF";
				foreach($break_arr_summery as $item=>$break_arr_val)
				{
					foreach($break_arr_val as $uom=>$brand_val)
					{
						$jobQnty=0; $jobCardQnty=0; 
						foreach($brand_val as $product_id=>$prod_data)
						{
							foreach($prod_data as $lot_no=>$row)
							{
								//echo "<pre>";
								//print_r($row);
								$subcon_break_ids=explode(",",chop($row['subcon_break_ids'],','));
								for($j=0; $j<count($subcon_break_ids); $j++)
								{
									//echo $subcon_break_ids[$j].'=='.$row['conv_factor'].'=='.$job_qnty_arr[$subcon_break_ids[$j]][$row['conv_factor']]['qnty'].'++';
									$jobQnty +=$job_qnty_arr[$subcon_break_ids[$j]][$row['conv_factor']]['qnty'];
								}
								//echo $jobQnty.'=='; 
								$jobQnty=round($jobQnty); 
								$jobCardQnty=$jobQnty*$row['qty'];
								$balance=$row['total_req_qty']-$issue_qty_arr[$product_id][$lot_no]+$issue_rtn_qty_arr[$product_id][$lot_no];
								$balance=number_format($balance,3);
								$balance=str_replace(",","",trim($balance));
								$mst_ids=chop($row['job_card_dtls_id'],',');
								$mst_ids=implode(",",array_unique(explode(",",$mst_ids)));
								$prev_req_qnty=$prev_req_rcv[0][csf("rcv_qnty")];
								$lot=$row['lot'];
	
								?>
								<tr bgcolor="<? echo $bggcolor; ?>" onClick='get_php_form_data("<? echo $mst_ids.'**'.$product_id.'**'.$balance.'**1'.'**'.$data[3].'**'.$data[6].'**'.$data[7].'**'.$lot_no;?>","child_form_input_job_card_data","requires/raw_material_item_issue_controller")' style="cursor:pointer" >
									<td width="30"><? echo $k; ?></td>
									<td width="120"><p><? echo $item; ?></p></td>
									<td width="70"><p><? echo $color_library_arr[$row['color_id']]; ?></p></td>
									<td width="70"><p><? echo $lot_no; ?></p></td>
									<td width="100"><p><? echo $brand_arr[$product_id]; ?></p></td>
									<td width="60"><p><? echo $unit_of_measurement[$uom]; ?></p></td>
									<td width="100" align="right"><p><? echo number_format($row['total_req_qty'],3) ;//$req_quantity_arr[$product_id] ;//$jobCardQnty;//$qnty;?> </p></td>
									<td width="60" align="right"><p><? echo number_format($issue_qty_arr[$product_id],3); ?> </p></td>
									<td width="60" align="right"><p><? echo number_format($issue_rtn_qty_arr[$product_id],3); ?> </p></td>
									<td align="right"><p><? echo  $balance;?> </p></td>
								</tr>
								<?
								$k++;
							}
						}
					}
				}
				?>
			</tbody>	 
	   	</table>
 	</div>
<?
exit();
}

if($action=="child_form_input_job_card_data")
{
	$ex_data = explode("**",$data);
	$balance=$ex_data[2];
	$issue_basis=$ex_data[4];
	$storeId=$ex_data[5];
	$issuereqid=$ex_data[6];
	$lot_no=$ex_data[7];
	
	//print_r($ex_data);
	if($issue_basis==7)// requsition
	{
		//if($lot_no!="") $lot_conds=" and b.batch_lot='$lot_no'"; else $lot_conds="  and b.batch_lot is null";
	  	$trans_sql = "select distinct a.item_group_id, b.store_id, a.id, sum((case when b.transaction_type in(1,4,5) then b.cons_quantity else 0 end)-(case when b.transaction_type in(2,3,6) then b.cons_quantity else 0 end)) as current_stock  
		from product_details_master a, inv_transaction b 
		where a.id=b.prod_id and  a.item_category_id in(101,22)  and a.id=$ex_data[1] and b.store_id=$storeId  and a.status_active=1 and b.status_active=1 and b.is_deleted=0 $lot_conds
		group by  a.id,a.item_group_id,b.store_id";
		//echo $trans_sql;
		$trnasactionData = sql_select($trans_sql);
		//echo count($trnasactionData).jahid;die;
		foreach($trnasactionData as $row_p)
		{
			if(isset($row_p[csf("item_group_id")])) 
			{
				$cons_closing_stock_value[$row_p[csf("item_group_id")]][$row_p[csf("store_id")]][$row_p[csf("id")]]["current_stock"]+=$row_p[csf("current_stock")];
			} 
		}
		//echo "<pre>";
		//print_r($cons_closing_stock_value); die;
		$section_id_arr=return_library_array( "select product_id, section_id from trims_raw_mat_requisition_dtls where  product_id=$ex_data[1] and status_active=1 and  is_deleted=0",'product_id','section_id');
		/*$data_ar = sql_select("SELECT b.id, b.description, c.company_id, c.id as prod_id, c.item_description, c.item_category_id, c.item_group_id,b.cons_qty, c.current_stock,c.brand_name,c.origin,c.model,c.unit_of_measure,c.item_size,c.sub_group_code,c.section_id from trims_job_card_breakdown b, product_details_master c,trims_raw_mat_requisition_dtls d  where b.product_id=c.id and  b.product_id=d.product_id and  c.id=d.product_id  and b.mst_id in($ex_data[0]) and c.id=$ex_data[1] and d.mst_id=$issuereqid  and c.item_category_id in (101,22) and b.status_active=1 and b.is_deleted=0");*/
		if($lot_no!="") $lot_conds_req=" and d.lot='$lot_no'"; else $lot_conds_req=" and d.lot is null";
		if($ex_data[0]=='')
		{
			//$data_sql = "SELECT 0 as id, null as description, c.company_id, c.id as prod_id, c.item_description, c.item_category_id, c.item_group_id, c.current_stock, c.brand_name, c.origin, c.model, c.unit_of_measure, c.item_size, c.sub_group_code, c.section_id, d.lot 
			//from product_details_master c, trims_raw_mat_requisition_dtls d
			//where c.id=d.product_id and c.id=$ex_data[1] and d.mst_id=$issuereqid and c.item_category_id in (101,22) $lot_conds_req";
			
			$data_sql = "SELECT 0 as id, null as description, c.company_id, c.id as prod_id, c.item_description, c.item_category_id, c.item_group_id, c.current_stock, c.brand_name, c.origin, c.model, c.unit_of_measure, c.item_size, c.sub_group_code, e.section_id, d.lot 
			from product_details_master c, trims_raw_mat_requisition_dtls d,trims_raw_mat_requisition_mst e
			where c.id=d.product_id and e.id=d.mst_id and c.id=$ex_data[1] and d.mst_id=$issuereqid and c.item_category_id in (101,22) $lot_conds_req";
		}
		else
		{
			//$data_sql = "SELECT b.id, b.description, c.company_id, c.id as prod_id, c.item_description, c.item_category_id, c.item_group_id, b.cons_qty, c.current_stock,c.brand_name, c.origin, c.model, c.unit_of_measure, c.item_size, c.sub_group_code, c.section_id, d.lot 
			//from trims_job_card_breakdown b, product_details_master c, trims_raw_mat_requisition_dtls d  
			//where b.product_id=c.id and b.product_id=d.product_id and c.id=d.product_id and b.mst_id in($ex_data[0]) and c.id=$ex_data[1] and d.mst_id=$issuereqid  and c.item_category_id in (101,22) and b.status_active=1 and b.is_deleted=0 $lot_conds_req";
			
			$data_sql = "SELECT b.id, b.description, c.company_id, c.id as prod_id, c.item_description, c.item_category_id, c.item_group_id, b.cons_qty, c.current_stock,c.brand_name, c.origin, c.model, c.unit_of_measure, c.item_size, c.sub_group_code, e.section_id, d.lot 
			from trims_job_card_breakdown b, product_details_master c, trims_raw_mat_requisition_dtls d,trims_raw_mat_requisition_mst e  
			where b.product_id=c.id and b.product_id=d.product_id and c.id=d.product_id  and e.id=d.mst_id  and b.mst_id in($ex_data[0]) and c.id=$ex_data[1] and d.mst_id=$issuereqid  and c.item_category_id in (101,22) and b.status_active=1 and b.is_deleted=0 $lot_conds_req";
		}
		// echo $data_sql;
		$data_ar = sql_select($data_sql);
	}
	else 
	{
		if($lot_no!="") $lot_conds_job=" and b.lot='$lot_no'"; else $lot_conds_job=" and b.lot is null";
		$data_ar = sql_select("SELECT b.id, b.description, c.company_id, c.id as prod_id, c.item_description, c.item_category_id, c.item_group_id, b.cons_qty, c.current_stock, c.brand_name, c.origin, c.model, c.unit_of_measure, c.item_size, c.sub_group_code, d.section_id, b.lot 
	   from trims_job_card_breakdown b, product_details_master c, trims_job_card_mst d, trims_job_card_dtls e
	   where d.id=e.mst_id and e.id=b.mst_id and b.product_id=c.id and b.mst_id in($ex_data[0]) and c.id=$ex_data[1] and c.item_category_id in (101,22) and b.status_active=1 and b.is_deleted=0 $lot_conds_job");
		
	}

	foreach ($data_ar as $info)
	{
		
		if($issue_basis==7)// requsition
		{
			if($info[csf("section_id")]!="") { $section_id=$info[csf("section_id")]; } else { $section_id=$section_id_arr[$info[csf("prod_id")]]; }
			//echo $info[csf("prod_id")]; die;
			// $store_id=$store_id_arr[$info[csf("prod_id")]];
			//echo "document.getElementById('cbo_store_name').value 				= '".$storeId."';\n";
			$current_stock=$cons_closing_stock_value[trim($info[csf("item_group_id")])][trim($storeId)][trim($info[csf("prod_id")])]["current_stock"];
 		}
		else 
		{
			$section_id=$info[csf("section_id")];
 			$current_stock=$info[csf("current_stock")];
 		 }
		
		if($current_stock!="")
		{
 		  $current_stock=$current_stock;
		}
		else
		{
 			$current_stock=0;	
		}
		echo "document.getElementById('cbo_item_group').value 			= '".$info[csf("item_group_id")]."';\n";
		echo "document.getElementById('cbo_item_category').value 		= '".$info[csf("item_category_id")]."';\n";
		echo "document.getElementById('txt_item_desc').value 			= '".$info[csf("item_description")].",".$info[csf("item_size")]."';\n";
		echo "document.getElementById('cbo_uom').value 					= '".$info[csf("unit_of_measure")]."';\n";
		echo "document.getElementById('txt_current_stock').value 		= '".$current_stock."';\n";
		echo "document.getElementById('txt_brand').value 				= '".$info[csf("brand_name")]."';\n";
		echo "document.getElementById('txt_lot_no').value 				= '".$info[csf("lot")]."';\n";
		//echo "document.getElementById('hidden_bal_qnty').value 			= '".$info[csf("current_stock")]."';\n";
		echo "document.getElementById('current_prod_id').value 			= '".$info[csf("prod_id")]."';\n";
		echo "document.getElementById('cbo_section_mst').value 				= '".$section_id."';\n";
		echo "document.getElementById('hidden_bal_qnty').value 			= '".$balance."';\n";
		echo "document.getElementById('txt_issue_qnty').value 			= '".$balance."';\n";
		echo "$('#txt_item_desc').attr('disabled', true);\n";
	}
exit();
}


if($action=="show_dtls_list_view")
{
	$ex_data = explode("**",$data);
	$issue_number_id = $ex_data[0];

	$cond="";
	if($issue_number_id!="") $cond .= " and a.id='$issue_number_id'";

	$location_arr=return_library_array("select id,location_name from lib_location",'id','location_name');
	$department_arr=return_library_array("select id,department_name from lib_department",'id','department_name');
	$section_arr=return_library_array("select id,section_name from lib_section",'id','section_name');
	$store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');
	$po_no_arr=return_library_array( "select id, po_number from wo_po_break_down",'id','po_number');
 	$group_arr=return_library_array( "select id,item_name from lib_item_group where item_category in (101,22)  and status_active=1 and is_deleted=0",'id','item_name');

	/*$sql = "select a.issue_number, b.id, b.store_id, b.cons_uom, b.cons_quantity, b.machine_category, b.machine_id, b.prod_id, b.location_id, b.department_id, b.section_id, c.item_category_id, c.product_name_details as item_description, c.item_group_id, b.order_id
			from inv_issue_master a, inv_transaction b, product_details_master c
			where a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=2 and a.entry_form=265 and b.status_active=1 $cond";
			echo $sql;*/
          $sql = "SELECT a.issue_number, b.id, b.store_id, b.cons_uom, b.cons_quantity, b.machine_category, b.machine_id, b.prod_id, b.location_id, b.department_id, b.section_id, c.item_category_id, $concat(c.item_description $concat_coma ',' $concat_coma c.item_size) as item_description, c.item_group_id, b.order_id,c.section_id as trims_section_id, b.batch_lot
			from inv_issue_master a, inv_transaction b, product_details_master c
			where a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=2 and a.entry_form=265 and b.status_active=1 $cond";
	$result = sql_select($sql);
	$i=1;
	$total_qnty=0;
	?>
    	<table class="rpt_table" border="1" cellpadding="2" cellspacing="0" style="width:1100px" rules="all">
        	<thead>
            	<tr>
                	<th>SL</th>
                    <th>Category</th>
                    <th>Group</th>
                    <th>Lot</th>
                    <th>Description</th>
                    <th>Section</th>
                    <th>Store</th>
                    <th>Issue Qnty</th>
                    <th>UOM</th>
                    <th>Serial No</th>
                    <th>Machine Categ.</th>
                    <th>Machine No</th>
                    <th>Buyer Order</th>
                    <th>Loc./Dept./Sec.</th>
                </tr>
            </thead>
            <tbody>
            	<? foreach($result as $row)
				{

					if ($i%2==0)$bgcolor="#E9F3FF";
					else $bgcolor="#FFFFFF";
					if($db_type==0)
					{
						$serialNo=return_field_value("group_concat(serial_no)","inv_serial_no_details","issue_trans_id=".$row[csf("id")]);
						$serialID=return_field_value("group_concat(id)","inv_serial_no_details","issue_trans_id=".$row[csf("id")]);
					}
					else
					{
						$serialNo=return_field_value("LISTAGG(CAST(serial_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY serial_no) as sr","inv_serial_no_details","issue_trans_id=".$row[csf("id")],"sr");
						$serialID=return_field_value("LISTAGG(CAST(id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY id) as id","inv_serial_no_details","issue_trans_id=".$row[csf("id")],"id");
					}
					$total_qnty +=	$row[csf("cons_quantity")];  
					 
					 if($row[csf("trims_section_id")]!="")
					 {
						$section_id=$trims_section[$row[csf("trims_section_id")]];
					}
					else 
					{
						$section_id=$trims_section[$row[csf("section_id")]];
					}
					
 				?>
                	<tr bgcolor="<? echo $bgcolor; ?>" onClick='get_php_form_data("<? echo $row[csf("id")];?>","child_form_input_data","requires/raw_material_item_issue_controller")' style="cursor:pointer" >
                        <td width="30"><? echo $i; ?></td>
                        <td width="100"><p><? echo $item_category[$row[csf("item_category_id")]]; ?></p></td>
                        <td width="100"><p><? echo $group_arr[$row[csf("item_group_id")]]; ?></p></td>
                        <td width="100"><p><? echo $row[csf("batch_lot")]; ?></p></td>
                        <td width="100"><p><? echo $row[csf("item_description")]; ?></p></td>
                        <td width="100"><p><? echo $section_id; ?></p></td>
                        <td width="90"><p><? echo $store_arr[$row[csf("store_id")]]; ?></p></td>
                        <td width="80" align="right"><p><? echo number_format($row[csf("cons_quantity")],3); ?></p></td>
                        <td width="50"><p><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></p></td>
                        <td width="50"><p><? echo $serialNo; ?></p></td>
                        <td width="70"><p><? echo $machine_category[$row[csf("machine_category")]]; ?></p></td>
                        <td width="50"><p><? echo $machine_arr[$row[csf("machine_id")]]; ?></p></td>
                        <td width="80"><p><? echo $po_no_arr[$row[csf("order_id")]]; ?></p></td>
                        <td width="120"><p><? echo $location_arr[$row[csf("location_id")]].', '.$department_arr[$row[csf("department_id")]].', '.$section_id; ?></p></td>
                   </tr>
                <? $i++; } ?>
                	<tfoot>
                            <th colspan="6" align="right">Total :</th>
                            <th><? echo number_format($total_qnty,2); ?></th>
                            <th colspan="7">&nbsp;</th>
                     </tfoot>
            </tbody>
        </table>
    <?
	exit();
}

if($action=="child_form_input_data")
{
	$rcv_dtls_id = $data;
	$sql = "select b.id, b.location_id, b.company_id, c.id as prod_id, c.item_description, c.item_category_id, c.item_group_id, b.item_return_qty, b.cons_quantity, c.current_stock, b.store_id, b.cons_uom, b.order_id, b.pi_wo_batch_no, b.floor_id, b.line_id, b.machine_id, b.machine_category, b.location_id, b.department_id, b.section_id, b.room, b.rack, b.self, b.bin_box ,c.brand_name, c.origin, c.model, b.trans_uom, b.no_of_qty, b.batch_lot
	from inv_transaction b, product_details_master c
	where b.prod_id=c.id and b.id='$rcv_dtls_id' and b.transaction_type=2 and b.status_active=1 and b.item_category  in (101,22) ";
	//echo $sql;//die;
	$result = sql_select($sql);

	foreach($result as $row)
	{
		echo "$('#txt_item_desc').val('".$row[csf("item_description")]."');\n";
		echo "$('#cbo_item_category').val(".$row[csf("item_category_id")].");\n";
		echo "$('#cbo_item_group').val(".$row[csf("item_group_id")].");\n";
		echo "$('#txt_issue_qnty').val(".$row[csf("cons_quantity")].");\n";
		echo "$('#txt_return_qty').val(".$row[csf("item_return_qty")].");\n";
		echo "$('#hidden_p_issue_qnty').val(".$row[csf("cons_quantity")].");\n";
 		echo "$('#cbo_location').val(".$row[csf("location_id")].");\n";
 		echo "$('#cbo_issue_uom').val(".$row[csf("trans_uom")].");\n";
 		echo "$('#txt_no_of_qty').val(".$row[csf("no_of_qty")].");\n";
		echo "$('#txt_lot_no').val('".$row[csf("batch_lot")]."');\n";


 		echo "$('#cbo_item_category').attr('disabled', true);\n";
 		echo "$('#txt_item_desc').attr('disabled', true);\n";
 		echo "$('#cbo_item_group').attr('disabled', true);\n";
		echo "load_room_rack_self_bin('requires/raw_material_item_issue_controller*4_5_6_7_8_9_10_11_15_16_17_18_19_20_21_22_23_32_33_34_35_36_37_38_39_40_41_44_45_46_47_48_49_50_51_52_53_54_55_56_57_58_59_60_61_62_63_64_65_66_67_68_69_70_89_90_91_92_93_94_101_22', 'store','store_td', '".$row[csf('company_id')]."','".$row[csf('location_id')]."',this.value);\n";

		echo "$('#cbo_store_name').val(".$row[csf("store_id")].");\n";
		echo "$('#cbo_store_name').attr('disabled', true);\n";
		echo "$('#cbo_location').attr('disabled', true);\n";
 		echo "$('#cbo_uom').val(".$row[csf("cons_uom")].");\n";

		$currnet_stock=return_field_value("sum(case when transaction_type in(1,4,5) then cons_quantity else 0 end) - sum(case when transaction_type in(2,3,6) then cons_quantity else 0 end) as balance_stock","inv_transaction","status_active=1 and prod_id='".$row[csf("prod_id")]."' and store_id='".$row[csf("store_id")]."'","balance_stock");

		echo "$('#txt_current_stock').val(".($currnet_stock+$row[csf("cons_quantity")]).");\n";
		echo "$('#hidden_bal_qnty').val(".($currnet_stock+$row[csf("cons_quantity")]).");\n";

		//$serialNo=return_field_value("group_concat(serial_no)","inv_serial_no_details","issue_trans_id=".$row[csf("id")]);
		//$serialID=return_field_value("group_concat(id)","inv_serial_no_details","issue_trans_id=".$row[csf("id")]);
		if($db_type==0)
		{
			$serialNo=return_field_value("group_concat(serial_no)","inv_serial_no_details","issue_trans_id=".$row[csf("id")]);
			$serialID=return_field_value("group_concat(id)","inv_serial_no_details","issue_trans_id=".$row[csf("id")]);
		}
		else
		{
			$serialNo=return_field_value("LISTAGG(CAST(serial_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY serial_no) as sr","inv_serial_no_details","issue_trans_id=".$row[csf("id")],"sr");
			$serialID=return_field_value("LISTAGG(CAST(id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY id) as id","inv_serial_no_details","issue_trans_id=".$row[csf("id")],"id");
		}
		echo "$('#txt_serial_no').val('".$serialNo."');\n";
		echo "$('#txt_serial_id').val('".$serialID."');\n";
		echo "$('#before_serial_id').val('".$serialID."');\n";
		echo "$('#cbo_machine_category').val(".$row[csf("machine_category")].");\n";

		//echo "load_drop_down( 'requires/raw_material_item_issue_controller',".$row[csf("company_id")]."+'_'+".$row[csf("location_id")].", 'load_drop_down_floor', 'floor_td' );\n";


		echo "load_room_rack_self_bin('requires/raw_material_item_issue_controller', 'floor','floor_td', '".$row[csf('company_id')]."','".$row[csf('location_id')]."','".$row[csf('store_id')]."',this.value);\n";

		echo "$('#cbo_floor').val(".$row[csf("floor_id")].");\n";
		//load_drop_down( 'requires/raw_material_item_issue_controller', this.value+'_'+$company_id+'_'+$location_id, 'load_drop_down_sewing_line_floor', 'sewing_line_td' );
		echo "load_drop_down( 'requires/raw_material_item_issue_controller',".$row[csf("floor_id")]."+'_'+".$row[csf("company_id")]."+'_'+".$row[csf("location_id")].", 'load_drop_down_sewing_line_floor', 'sewing_line_td' );\n";
		echo "$('#cbo_sewing_line').val(".$row[csf("line_id")].");\n";

		echo "load_drop_down( 'requires/raw_material_item_issue_controller',".$row[csf("company_id")]."+'_'+".$row[csf("machine_category")]."+'_'+".$row[csf("floor_id")].", 'load_drop_machine', 'machine_td' );\n";
		echo "$('#cbo_machine_name').val(".$row[csf("machine_id")].");\n";

		echo "$('#txt_order_id').val(".$row[csf("order_id")].");\n";
		echo "$('#txt_wo_batch_no').val(".$row[csf("pi_wo_batch_no")].");\n";
		//$buyer_order=return_field_value("po_number","wo_po_break_down","id=".$row[csf("order_id")]);
		$buyer_order=return_field_value("order_no","subcon_ord_mst","id=".$row[csf("pi_wo_batch_no")]);
		echo "$('#txt_buyer_order').val('".$buyer_order."');\n";

		echo "$('#cbo_department').val(".$row[csf("department_id")].");\n";
		//echo "load_drop_down( 'requires/raw_material_item_issue_controller',document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_department').value, 'load_drop_down_section', 'section_td');\n";
		echo "load_drop_down( 'requires/raw_material_item_issue_controller', ".$row[csf("department_id")].", 'load_drop_down_section', 'section_td' );\n";
		echo "$('#cbo_section').val(".$row[csf("section_id")].");\n";

		echo "load_room_rack_self_bin('requires/raw_material_item_issue_controller', 'room','room_td', '".$row[csf('company_id')]."','".$row[csf('location_id')]."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."',this.value);\n";

 		echo "$('#cbo_room').val(".$row[csf("room")].");\n";
		echo "fn_room_rack_self_box();\n";

		echo "load_room_rack_self_bin('requires/raw_material_item_issue_controller', 'rack','rack_td', '".$row[csf('company_id')]."','".$row[csf('location_id')]."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."',this.value);\n";

		echo "$('#txt_rack').val('".$row[csf("rack")]."');\n";
		echo "fn_room_rack_self_box();\n";

		echo "load_room_rack_self_bin('requires/raw_material_item_issue_controller', 'shelf','shelf_td', '".$row[csf('company_id')]."','".$row[csf('location_id')]."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."','".$row[csf('rack')]."',this.value);\n";

 		echo "$('#txt_shelf').val(".$row[csf("self")].");\n";
		echo "fn_room_rack_self_box();\n";

		echo "load_room_rack_self_bin('requires/raw_material_item_issue_controller', 'bin','bin_td', '".$row[csf('company_id')]."','".$row[csf('location_id')]."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."','".$row[csf('rack')]."','".$row[csf('self')]."',this.value);\n";

		echo "$('#cbo_bin').val(".$row[csf("bin_box")].");\n";
		echo "$('#current_prod_id').val(".$row[csf("prod_id")].");\n";
        echo "$('#txt_brand').val('".$row[csf("brand_name")]."');\n";
        echo "$('#cbo_origin').val(".$row[csf("origin")].");\n";
        echo "$('#txt_model').val('".$row[csf("model")]."');\n";//new dev
		echo "$('#update_id').val(".$row[csf("id")].");\n";
		echo "$('#txt_issue_req_no').attr('disabled', true);\n"; // issue id:23703
		echo "$('#txt_challan_no').attr('disabled', false);\n";
		echo "$('#txt_remarks').attr('disabled', false);\n";
		echo "set_button_status(1, permission, 'fnc_general_item_issue_entry',1,1);\n";
		//echo "$('#cbo_store_name').attr('disabled', false);\n";
		echo "reset_form('','item_issue_listview','','','','');\n";
		//echo "$('#tbl_master').find('input,select').attr('disabled', false);\n";
	}
	exit();
}



//################################################# function Here #########################################//

//function for domestic rate find--------------//
//parameters rate,ile cost,exchange rate,conversion factor
function return_domestic_rate($rate,$ile_cost,$exchange_rate,$conversion_factor){
	$rate_ile=$rate+$ile_cost;
	$rate_ile_exchange=$rate_ile*$exchange_rate;
	$doemstic_rate=$rate_ile_exchange/$conversion_factor;
	return $doemstic_rate;
}

if ($action=="general_item_issue_print")
{
    extract($_REQUEST);
	$data=explode('*',$data);
	//print_r ($data);

	$sql=" select id,company_id, issue_number,issue_purpose,issue_date, req_no, challan_no, knit_dye_source, knit_dye_company, remarks,loan_party, inserted_by from inv_issue_master where id='$data[1]'";
	$dataArray=sql_select($sql);
	$inserted_by=$dataArray[0][csf("inserted_by")];
    $company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
    if ($dataArray[0]['KNIT_DYE_SOURCE'] == 3 && $dataArray[0]['ISSUE_PURPOSE'] == 1){
        $supplier_library=return_library_array( "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and a.id=c.supplier_id and b.party_type in(1,9,20) and a.status_active=1 group by a.id, a.supplier_name order by a.supplier_name", "id","supplier_name"  );
    }else if ($dataArray[0]['KNIT_DYE_SOURCE'] == 3 && $dataArray[0]['ISSUE_PURPOSE'] == 2){
        $supplier_library=return_library_array( "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and a.id=c.supplier_id and b.party_type in(1,9,21,24) and a.status_active=1 group by a.id, a.supplier_name order by a.supplier_name", "id","supplier_name"  );
    }else if ($dataArray[0]['KNIT_DYE_SOURCE'] == 3 && $dataArray[0]['ISSUE_PURPOSE'] == 15){
        $supplier_library=return_library_array( "SELECT a.id, a.buyer_name as supplier_name FROM lib_buyer a, lib_buyer_party_type  b, lib_buyer_tag_company c WHERE a.id = b.buyer_id AND a.id = c.buyer_id AND b.party_type IN (80) AND a.status_active = 1 GROUP BY a.id, a.buyer_name ORDER BY a.buyer_name", "id","supplier_name"  );
    }else{
        $supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id","supplier_name"  );
    }
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$floor_arr = return_library_array("select id, floor_name from  lib_prod_floor","id","floor_name");
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
?>
<div style="width:1080px;">
    <table width="1060" cellspacing="0" border="0">
        <tr>
            <td colspan="2" rowspan="3">
			<img src="../../../<? echo $image_location; ?>" height="60" width="200" style="float:left;">
			</td>
			<td colspan="4" align="center" style="font-size:22px">
            <strong><? echo $company_library[$data[0]]; ?></strong>
            </td>
        </tr>
        <tr class="form_caption">
        	<td colspan="4" align="center" style="font-size:14px">
				<?
					$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
					foreach ($nameArray as $result)
					{
					?>
						Plot No: <? echo $result['plot_no']; ?>
						Level No: <? echo $result['level_no']?>
						Road No: <? echo $result['road_no']; ?>
						Block No: <? echo $result['block_no'];?>
						City No: <? echo $result['city'];?>
						Zip Code: <? echo $result['zip_code']; ?>
						Province No: <? echo $result['province'];?>
						Country: <? echo $country_arr[$result['country_id']]; ?><br>
						Email Address: <? echo $result['email'];?>
						Website No: <? echo $result['website'];
					}
                ?>
            </td>
        </tr>
        <tr>
            <td colspan="2" align="center" style="font-size:18px"><strong><u>Raw Material Issue challan</u></strong></td>
            <td colspan="2" align="right" id="barcode_img_id"></td>
        </tr>
        <tr>
        	<td width="85"><strong>System ID</strong></td>
            <td width="125px"><strong>: </strong><? echo $dataArray[0][csf('issue_number')]; ?></td>
            <td width="100"><strong>Issue Purpose</strong></td>
            <td width="175px"><strong>: </strong><? echo $general_issue_purpose[$dataArray[0][csf('issue_purpose')]]; ?></td>
            <td width="110"><strong>Loan Party</strong></td>
            <td><strong>: </strong><? echo $supplier_library[$dataArray[0][csf('loan_party')]]; ?></td>
        </tr>
        <tr>
            <td><strong>Issue Date</strong></td>
            <td><strong>: </strong><? echo change_date_format($dataArray[0][csf('issue_date')]); ?></td>
            <td><strong>Issue Req. No</strong></td>
            <td><strong>: </strong><? echo $dataArray[0][csf('req_no')]; ?></td>
            <td><strong>Challan No</strong></td>
            <td width="175px"><strong>: </strong><? echo $dataArray[0][csf('challan_no')]; ?></td>
        </tr>
         <tr>
			<td><strong>Issue Company</strong></td>
			<td><strong>: </strong>
				<?
					if($dataArray[0][csf('knit_dye_source')]==1){
						echo $company_library[$dataArray[0][csf('knit_dye_company')]];
					}
					else {
						echo $supplier_library[$dataArray[0][csf('knit_dye_company')]];
					}
				?>
			</td>
           	<td><strong>Remarks</strong></td>
           	<td colspan="5" ><strong>: </strong><? echo $dataArray[0][csf('remarks')]; ?></td>
       </tr>
    </table>
	<div style="width:100%;">
    <table cellspacing="0" width="1110"  border="1" rules="all" class="rpt_table" >
        <thead bgcolor="#dddddd" style="font-size:13px">
            <th width="30">SL</th>
            <th width="80">Item Category</th>
            <th width="80">Item Group</th>
            <th width="80">Section</th>
            <th width="120">Item Description</th>
            <th width="60">Lot</th>
            <th width="60">Item Size</th>
            <th width="60">Store</th>
            <th width="60">Issue Qty</th>
            <th width="40">UOM</th>
            <th width="60">No Of</th>
            <th width="80">Serial No</th>
            <th width="70">Machine Categ.</th>
            <th width="70">Floor</th>
            <th width="50">Machine No</th>
            <th width="60">Buyer Order</th>
            <th>Loc./Dept./Sec.</th>
        </thead>
        <tbody style="font-size:12px">
<?
	//$mrr_no=$dataArray[0][csf('issue_number')];
	$cond="";
	if($data[1]!="") $cond .= " and a.id='$data[1]'";
	$location_arr=return_library_array("select id,location_name from lib_location",'id','location_name');
	$department_arr=return_library_array("select id,department_name from lib_department",'id','department_name');
	$section_arr=return_library_array("select id,section_name from lib_section",'id','section_name');
	$store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');
	$po_number_arr=return_library_array( "select id, po_number from wo_po_break_down",'id','po_number');
 	$group_arr=return_library_array( "select id,item_name from lib_item_group where item_category in (101,22)  and status_active=1 and is_deleted=0",'id','item_name');

	$i=1;
	$sql_result = sql_select("SELECT a.issue_number, b.id, b.store_id, b.cons_uom, b.cons_quantity, b.floor_id, b.machine_category, b.machine_id, b.prod_id, b.location_id, b.department_id, b.section_id, b.order_id, c.item_category_id, c.item_description, c.item_group_id, c.item_size,c.section_id as trims_section_id,b.no_of_qty,b.trans_uom, b.batch_lot
	from inv_issue_master a, inv_transaction b, product_details_master c where a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and a.entry_form=265 $cond");

	foreach($sql_result as $row)
	{
		if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		if($db_type==0)
		{
			$serialNo=return_field_value("group_concat(serial_no)","inv_serial_no_details","issue_trans_id=".$row[csf("id")]);
			$serialID=return_field_value("group_concat(id)","inv_serial_no_details","issue_trans_id=".$row[csf("id")]);
		}
		else
		{
			$serialNo=return_field_value("LISTAGG(CAST(serial_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY serial_no) as sr","inv_serial_no_details","issue_trans_id=".$row[csf("id")],"sr");
			$serialID=return_field_value("LISTAGG(CAST(id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY id) as id","inv_serial_no_details","issue_trans_id=".$row[csf("id")],"id");
		}

		$cons_quantity=$row[csf('cons_quantity')];
		$cons_quantity_sum += $cons_quantity;
	?>
		<tr bgcolor="<? echo $bgcolor; ?>">
			<td align="center"><? echo $i; ?></td>
			<td><? echo $item_category[$row[csf("item_category_id")]]; ?></td>
			<td><? echo $group_arr[$row[csf("item_group_id")]]; ?></td>
			<td><? echo $trims_section[$row[csf("trims_section_id")]]; ?></td>
			<td><? echo $row[csf("item_description")]; ?></td>
			<td><? echo $row[csf("batch_lot")]; ?></td>
            <td><? echo $row[csf("item_size")]; ?></td>
			<td align="center"><? echo $store_arr[$row[csf("store_id")]]; ?></td>
			<td align="right"><? echo number_format($row[csf("cons_quantity")],3); ?></td>
			<td align="center"><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></td>
			<td align="right"><? echo number_format($row[csf("no_of_qty")],2)." ".$unit_of_measurement[$row[csf("trans_uom")]] ; ?></td>
			<td><? echo $serialNo; ?></td>
			<td><? echo $machine_category[$row[csf("machine_category")]]; ?></td>
			<td ><? echo $floor_arr[$row[csf("floor_id")]]; ?></td>
			<td align="center"><? echo $machine_arr[$row[csf("machine_id")]]; ?></td>
			<td align="center"><? echo $po_number_arr[$row[csf("order_id")]]; ?></td>
			<td><? echo $location_arr[$row[csf("location_id")]].', '.$department_arr[$row[csf("department_id")]].', '.$section_arr[$row[csf("section_id")]]; ?></td>
		</tr>
		<? $i++; } ?>
    </tbody>
    <tfoot style="font-size:13px">
        <tr>
            <th colspan="7" align="right">Total :</th>
            <th align="right" colspan="2"><? echo number_format($cons_quantity_sum, 3); ?></th>
            <th colspan="8">&nbsp;</th>
        </tr>
    </tfoot>
    </table>
        <br>
		 <?
            echo signature_table(157, $data[0], "1060px","","",$inserted_by);
         ?>
	</div>
	</div>
     <script type="text/javascript" src="../../../js/jquery.js"></script>
      <script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
     <script>

	function generateBarcode( valuess ){

			var value = valuess;//$("#barcodeValue").val();
		 // alert(value)
			var btype = 'code39';//$("input[name=btype]:checked").val();
			var renderer ='bmp';// $("input[name=renderer]:checked").val();

			var settings = {
			  output:renderer,
			  bgColor: '#FFFFFF',
			  color: '#000000',
			  barWidth: 1,
			  barHeight: 30,
			  moduleSize:5,
			  posX: 10,
			  posY: 20,
			  addQuietZone: 1
			};
			$("#barcode_img_id").html('11');
			 value = {code:value, rect: false};

			$("#barcode_img_id").show().barcode(value, btype, settings);

		}

	 generateBarcode('<? echo $data[2]; ?>');


	 </script>


<?
exit();
}


if ($action=="item_issue_requisition_popup_search")
{
    echo load_html_head_contents("Item Issue Requisition search From", "../../../", 1, 1,'','1','');
    extract($_REQUEST);

	?>
	<script>

        function hidden_item_value(id)
        {
            //alert (id);
            $('#hidden_item_issue_id').val(id);
			var ref = id.split("_");
			if(ref[6]==1)
			{
				parent.emailwindow.hide();
			}
			else
			{
				alert("Please Approve Requisition First");return;
			}
        }

        function item_issue_requisition_popup()
        {

        	if (form_validation('cbo_company_id','Company')==false)
			{
				alert('Pls, Select Company.');
				return;
			}
            show_list_view ( document.getElementById('cbo_company_id').value+'**'+document.getElementById('txt_indent_date').value+'**'+document.getElementById('txt_required_date').value+'**'+document.getElementById('txt_remarks').value+'**'+document.getElementById('txt_manual_requisition_no').value+'**'+document.getElementById('cbo_location_name').value+'**'+document.getElementById('cbo_division_name').value+'**'+document.getElementById('cbo_department_name').value+'**'+document.getElementById('cbo_section_name').value+'**'+document.getElementById('cbo_sub_section_name').value+'**'+document.getElementById('cbo_delivery_point').value+'**'+document.getElementById('txt_system_id').value, 'items_search_list_view', 'search_div', 'raw_material_item_issue_controller', 'setFilterGrid(\'list_view\',-1)');
        }
        function fnc_sub_section()
         {
             $('#cbo_sub_section_name').css('display','none');
         }
    </script>
	</head>
	<body>
	    <div align="center" style="width:800px;">
	        <form name="searchitemreqfrm" id="searchitemreqfrm">
	            <fieldset style="width:940px; margin-left:3px">
	            <legend>Search</legend>
	                <table cellpadding="0" cellspacing="0" width="20%" class="rpt_table" rules="all">
	                    <thead>
	                        <th class="must_entry_caption">Company</th>
                             <th>Indent No.</th>
                            <th>Indent Date</th>
                             <th>Remarks</th>
                             <th>Manual Requisition No</th>
                            <th align="right">Required Date</th>
                            <th align="right">Location</th>
                            <th align="right">Division</th>
                            <th align="right">Department</th>
                            <th align="right">Section</th>
                            <th align="right">Sub Section</th>
                            <th align="right">Delivery Point</th>
	                        <th><input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" /><input type="hidden" name="id_field" id="id_field" value="" />		</th>
	                    </thead>
	                    <tbody>
	                    <tr>
	                    	<td>
                                    <?
                                        $company="select comp.id,comp.company_name from lib_company comp where  comp.status_active=1 and comp.is_deleted=0  order by company_name";
                                        echo create_drop_down("cbo_company_id",100,$company,"id,company_name",1,"--select--",$cbo_company_id,"load_drop_down( 'raw_material_item_issue_controller', this.value, 'load_drop_down_location_popup','location_td');",1);
                                     ?>
	                  		</td>
                            <td><input type="text" name="txt_system_id" id="txt_system_id" class="text_boxes" style="width:70px" ></td>
                      		<td><input type="text" name="txt_indent_date" id="txt_indent_date" class="datepicker" style="width:70px" ></td>
                      		<td><input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:70px" ></td>
							<td><input type="text" name="txt_manual_requisition_no" id="txt_manual_requisition_no" class="text_boxes" style="width:70px"></td>
                            <td><input type="text" name="txt_required_date" id="txt_required_date" class="datepicker" style="width:70px" readonly></td>
                            <td id="location_td_popup">
			                <?php
                                            echo create_drop_down( "cbo_location_name", 90,$blank_array,"id,location_name", 1, "-- Select --",0,"");
                                        ?>
			                 </td>
				            <td  id="division_td" width="90">
							   <?php
									echo create_drop_down( "cbo_division_name", 90,$blank_array,"", 1, "-- Select --" );
				               ?>
				            </td>
                            <td width="70" id="department_td">
								<?php
                       				 echo create_drop_down( "cbo_department_name", 90,$blank_array,"", 1, "-- Select --" );
                   				?>
				            </td>
                             <td id="section_td"  width="132">
                             	<?
									echo create_drop_down( "cbo_section_name", 90,$blank_array,"", 1, "-- Select --",'' );
								?>
				            </td>
                            <td  id="sub_section_td" width="90">
								<?php
									echo create_drop_down( "cbo_sub_section_name", 90,$blank_array,"", 1, "-- Select --" );
	                			?>
				            </td>
                            <td><input type="text" name="cbo_delivery_point" id="cbo_delivery_point" style="width:90px" class="text_boxes"></td>
	                		<td><input type="hidden" id="hidden_item_issue_id" />
                            <input type="hidden" id="hidden_item_cost_center" />
                            <input type="hidden" id="hidden_itemissue_req_sys_id" />
                            <input type="button" id="search_button" class="formbutton" value="Show" onClick="item_issue_requisition_popup()" style="width:100px;" />
	                  		</td>
	                    </tr>
	                    </tbody>
	                    </table>
	               <div style="width:100%; margin-top:10px;" id="search_div" align="center"></div>
	            </fieldset>
	        </form>
	    </div>
	</body>
    <script>
    set_all_onclick();
    var cbo_company_id=$("#cbo_company_id").val();
    load_drop_down( 'raw_material_item_issue_controller', cbo_company_id, 'load_drop_down_location_popup','location_td_popup');
    </script>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	    <?
	exit();

}


if ($action=="fnc_job_card_items_sys_popup")
{
	echo load_html_head_contents("Job Popup Info","../../../", 1, 1, $unicode,'','');
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
			load_drop_down( 'raw_material_item_issue_controller', company+'_'+within_group, 'load_drop_down_buyer', 'buyer_td' );
		}
		function search_by(val)
		{
			$('#txt_search_string').val('');
			if(val==1 || val==0)
			{
				$('#search_by_td').html('System ID');
			}
			else if(val==2)
			{
				$('#search_by_td').html('W/O No');
			}
			else if(val==3)
			{
				$('#search_by_td').html('Buyer Job');
			}
			else if(val==4)
			{
				$('#search_by_td').html('Buyer Po');
			}
			else if(val==5)
			{
				$('#search_by_td').html('Buyer Style');
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
					<th colspan="8"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
				</tr>
				<tr>
					<th width="140" class="must_entry_caption">Company Name</th>
					<th width="100">Within Group</th>                           
					<th width="140">Party Name</th>
					<th width="100" id="search_by_td">Job ID</th>
					<th width="80">Section</th>
					<th width="100">Year</th>
					<th width="170">Date Range</th>                            
					<th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>
				</tr>           
			</thead>
			<tbody>
				<tr class="general">
					<td><input type="hidden" id="selected_job"><? $data=explode("_",$data); ?>  <!--  echo $data;-->
						<? 
						echo create_drop_down( "cbo_company_name", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $data[0], "fnc_load_party_popup(1,document.getElementById('cbo_within_group').value);",1); ?>
					</td>
					<td>
						<?php echo create_drop_down( "cbo_within_group", 100, $yes_no,"", 0, "--  --", $data[3], "fnc_load_party_popup(1,this.value);" ); ?>
					</td>
					<td id="buyer_td">
						<? echo create_drop_down( "cbo_party_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $data[2], "" );   	 
						?>
					</td>
					<td style="display: none;">
												<?
							$search_by_arr=array(1=>"System ID",2=>"W/O No",3=>"Buyer Job",4=>"Buyer Po",5=>"Buyer Style");
							echo create_drop_down( "cbo_type",100, $search_by_arr,"",0, "",1,'search_by(this.value)',0 );
						?>
					</td>
					<td align="center">
						<input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:100px" placeholder="" />
					</td>
					<td><? echo create_drop_down( "cbo_section", 80, $trims_section,"", 1, "-- Select Section --","",'',0,'','','','','','',"cboSection[]"); ?></td>
					<td align="center"><? echo create_drop_down( "cbo_year_selection", 100, $year,"", 1, "-- Select --", date('Y'), "",0 ); ?></td>
					<td align="center">
						<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px">
						<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px">
					</td>
					<td align="center">
						<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_section').value, 'create_job_search_list_view', 'search_div', 'raw_material_item_issue_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:70px;" /></td>
					</tr>
					<tr>
						<td colspan="8" align="center" valign="middle">
							<? echo load_month_buttons();  ?>
							<input type="hidden" name="hidden_mst_id" id="hidden_mst_id" class="datepicker" style="width:70px">
						</td>
					</tr>
					<tr>
						<td colspan="8" align="center" valign="top" id=""><div id="search_div"></div></td>
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

if($action=="create_job_search_list_view")
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
	if($search_type==1)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num='$search_str'";
			else if($search_by==2) $search_com_cond="and a.order_no='$search_str'";
			
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
			else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str%'";  
			
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
			else if($search_by==2) $search_com_cond="and a.order_no like '$search_str%'";  
			
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
			else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str'";  
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '%$search_str'";  
			else if ($search_by==4) $po_cond=" and b.po_number like '%$search_str'";
			else if ($search_by==5) $style_cond=" and a.style_ref_no like '%$search_str'";  
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
	where a.entry_form=257 and a.trims_job=b.job_no_mst and a.id=b.mst_id and a.status_active=1 and b.status_active=1 $delivery_date $company $buyer $withinGroup $search_com_cond $po_idsCond $withinGroup $section_id_cond $party_id_cond
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
			<? 
			$i=1;
			foreach($data_array as $row)
			{  
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value("<? echo $row[csf('id')].'_'.$row[csf('trims_job')].'_'.$row[csf('section_id')]; ?>")' style="cursor:pointer" >
					<td width="30"><? echo $i; ?></td>
					<td width="120"><? echo $row[csf('trims_job')]; ?></td>
					<td width="100"><? echo $trims_section[$row[csf('section_id')]]; ?></td>
					<td width="60" style="text-align:center;"><? echo $row[csf('year')]; ?></td>
					<td width="120"><? echo $row[csf('order_no')]; ?></td>
					<td width="120"><? echo $row[csf('received_no')]; ?></td>
					<td style="text-align:center;"><? echo change_date_format($row[csf('delivery_date')]); ?></td>
				</tr>
				<? 
				$i++; 
			} 
			?>
		</tbody>
	</table>
	<?    
	exit();
}

if($action=="create_trims_receive_search_list_view")
{	
	$data=explode('_',$data);
	//var_dump($data);
	$party_id=str_replace("'","",$data[1]);
	$search_by=str_replace("'","",$data[4]);
	$search_str=trim(str_replace("'","",$data[5]));
	$search_type =$data[6];
	$within_group =$data[7];
	if($db_type==0) { $year_cond=" and YEAR(a.insert_date)=$data[8]";   }
	if($db_type==2) {$year_cond=" and to_char(a.insert_date,'YYYY')=$data[8]";}

	if($data[0]!=0) $company=" and a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	//echo $search_type; die;
	$job_cond=""; $style_cond=""; $po_cond=""; $search_com_cond="";
	if($search_type==1)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="and a.job_no_prefix_num='$search_str'";
			else if($search_by==2) $search_com_cond="and a.order_no='$search_str'";
			
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
			else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str%'";  
			
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
			else if($search_by==2) $search_com_cond="and a.order_no like '$search_str%'";  
			
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
			else if($search_by==2) $search_com_cond="and a.order_no like '%$search_str'";  
			
			if ($search_by==3) $job_cond=" and a.job_no_prefix_num like '%$search_str'";  
			else if ($search_by==4) $po_cond=" and b.po_number like '%$search_str'";
			else if ($search_by==5) $style_cond=" and a.style_ref_no like '%$search_str'";  
		}
	}	

	if($party_id!=0) $party_id_cond=" and a.party_id='$party_id'"; else $party_id_cond="";

	if($db_type==0)
	{ 
		if ($data[2]!="" &&  $data[3]!="") $order_rcv_date = "and a.receive_date between '".change_date_format($data[2],'yyyy-mm-dd')."' and '".change_date_format($data[3],'yyyy-mm-dd')."'"; else $order_rcv_date ="";
	}
	else
	{
		if ($data[2]!="" &&  $data[3]!="") $order_rcv_date = "and a.receive_date between '".change_date_format($data[2], "", "",1)."' and '".change_date_format($data[3], "", "",1)."'"; else $order_rcv_date ="";
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
	if($db_type==0) $id_cond="group_concat(b.id) as id";
	else if($db_type==2) $id_cond="rtrim(xmlagg(xmlelement(e,b.id,',').extract('//text()') order by b.id).GetClobVal(),',') as id";

	//echo "select $id_cond as id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst $job_cond $style_cond $po_cond";
	if(($job_cond!="" && $search_by==3) || ($po_cond!="" && $search_by==4) || ($style_cond!="" && $search_by==5))
	{
		$po_ids = return_field_value("$id_cond", "wo_po_details_master a, wo_po_break_down b", "a.job_no=b.job_no_mst $job_cond $style_cond $po_cond", "id");
	}
	//echo $po_ids;
	if($db_type==2 && $po_ids!="") $po_ids = $po_ids->load();
	if ($po_ids!="")
	{	//echo $po_ids;
		$po_ids=explode(",",$po_ids);
		$po_idsCond=""; $poIdsCond="";
		//echo count($po_ids); die;
		if($db_type==2 && count($po_ids)>=999)
		{
			$chunk_arr=array_chunk($po_ids,999);
			foreach($chunk_arr as $val)
			{
				$ids=implode(",",$val);
				if($po_idsCond=="")
				{
					$po_idsCond.=" and ( b.buyer_po_id in ( $ids) ";
				}
				else
				{
					$po_idsCond.=" or  b.buyer_po_id in ( $ids) ";
				}
			}
			$po_idsCond.=")";
		}
		else
		{
			$ids=implode(",",$po_ids);
			$po_idsCond.=" and b.buyer_po_id in ($ids) ";
		}
	}
	else if($po_ids=="" && ($job_cond!="" && $search_by==3) || ($po_cond!="" && $search_by==4) || ($style_cond!="" && $search_by==5))
	{
		die;
		//$po_idsCond.=" and b.buyer_po_id in ($ids) ";
	}
	//if ($po_ids!="") $po_idsCond=" and b.buyer_po_id in ($po_ids)"; else $po_idsCond="";
	$color_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');
	$item_name_arr=return_library_array( "select id, item_name from lib_item_group where item_category=4 and status_active=1",'id','item_name');
	
	
	
	$buyer_po_arr=array();
	if($within_group==1)
	{
		$po_sql ="Select a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
		$po_sql_res=sql_select($po_sql);
		foreach ($po_sql_res as $row)
		{
			$buyer_po_arr[$row[csf("id")]]['style']=$row[csf("style_ref_no")];
			$buyer_po_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
		}
		unset($po_sql_res);
	}
	$buyer_po_id_str=""; $buyer_po_no_str=""; $buyer_po_style_str="";
	if($db_type==0) 
	{
		$ins_year_cond="year(a.insert_date)";
		$color_id_str=",group_concat(c.color_id) as color_id";
		if($within_group==1)
		{
			$buyer_po_id_str=",group_concat(b.buyer_po_id) as buyer_po_id";
		}
		else
		{
			$buyer_po_no_str=",group_concat(b.buyer_po_no) as buyer_po_id";
			$buyer_po_style_str=",group_concat(b.buyer_style_ref) as buyer_style";
		}
	}
	else if($db_type==2)
	{
		$ins_year_cond="TO_CHAR(a.insert_date,'YYYY')";
		$color_id_str=" ,rtrim(xmlagg(xmlelement(e,c.color_id,',').extract('//text()') order by c.color_id).GetClobVal(),',') as color_id";
		
		if($within_group==1)
		{
			$buyer_po_id_str=" ,rtrim(xmlagg(xmlelement(e,b.buyer_po_id,',').extract('//text()') order by b.id).GetClobVal(),',') as buyer_po_id";
		}
		else
		{
			$buyer_po_no_str=" ,rtrim(xmlagg(xmlelement(e,b.buyer_po_no,',').extract('//text()') order by b.id).GetClobVal(),',') as buyer_po_no";
			$buyer_po_style_str=" ,rtrim(xmlagg(xmlelement(e,b.buyer_style_ref,',').extract('//text()') order by b.id).GetClobVal(),',') as buyer_style";
		}
	}

	$sql= "select a.id,a.order_id, a.subcon_job,  a.company_id,  a.receive_date, a.order_no, a.delivery_date,a.within_group 
	from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c  
	where a.entry_form=255 and a.subcon_job=b.job_no_mst and a.id=b.mst_id and a.status_active=1 and b.status_active=1 and c.status_active=1 and c.is_deleted=0 $order_rcv_date $company $buyer $withinGroup $search_com_cond $po_idsCond $withinGroup and b.id=c.mst_id  
	group by a.id, a.subcon_job, a.insert_date, a.company_id, a.receive_date, a.order_no, a.delivery_date,a.within_group,a.order_id
	order by a.id DESC";// $buyer_po_id_str $buyer_po_no_str $buyer_po_style_str $color_id_str
	//echo $sql;
	$data_array=sql_select($sql);
	//echo "<pre>";
	//print_r($data_array);
	?>
    <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="525" >
        <thead>
            <th width="30">SL</th>
            <th width="100">Receive No</th>
            <th width="120">W/O No</th>
            <th width="80">Ord Receive Date</th>
            <th >Delivery Date</th>
        </thead>
        </table>
        <div style="width:525px; max-height:270px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="505" class="rpt_table" id="tbl_po_list">
        <tbody>
            <? 
            $i=1; 
            foreach($data_array as $row)
            {  
            	// $color_ids =$buyer_po_ids =$buyer_po_nos =$buyer_styles ='';
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                // $color_ids = $row[csf('color_id')]->load();
               
                // if($within_group!=1)
				// {
				// 	$buyer_po_nos = $row[csf('buyer_po_no')]->load();
                // 	$buyer_styles = $row[csf('buyer_style')]->load();
				// }
				// else
				// {
				// 	$buyer_po_ids = $row[csf('buyer_po_id')]->load();
				// }

				// $excolor_id=array_unique(explode(",",$color_ids));
				// $color_name="";	
				// foreach ($excolor_id as $color_id)
				// {
				// 	if($color_name=="") $color_name=$color_arr[$color_id]; else $color_name.=','.$color_arr[$color_id];
				// }
				// if($within_group==1)
				// {
				// 	$buyer_po=""; $buyer_style="";
				// 	$buyer_po_id=explode(",",$buyer_po_ids);
				// 	foreach($buyer_po_id as $po_id)
				// 	{
				// 		if($buyer_po=="") $buyer_po=$buyer_po_arr[$po_id]['po']; else $buyer_po.=','.$buyer_po_arr[$po_id]['po'];
				// 		if($buyer_style=="") $buyer_style=$buyer_po_arr[$po_id]['style']; else $buyer_style.=','.$buyer_po_arr[$po_id]['style'];
				// 	}
				// 	$buyer_po=implode(",",array_unique(explode(",",$buyer_po)));
				// 	$buyer_style=implode(",",array_unique(explode(",",$buyer_style)));
				// }
				// else
				// {
				// 	$buyer_po=implode(",",array_unique(explode(",",$buyer_po_nos)));
				// 	$buyer_style=implode(",",array_unique(explode(",",$buyer_styles)));
				// }
				// if($row[csf('within_group')]==1) 
				// {
					
				// 	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
				// 	$buyer_buyer=$buyer_arr[$row[csf('buyer_buyer')]]; 
				// }
				// else
				// {
				// 	$buyer_buyer=$row[csf('buyer_buyer')];
				// }
				 
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value("<? echo $row[csf('id')].'_'.$row[csf('order_id')].'_'.$row[csf('order_no')]; ?>")' style="cursor:pointer" >
                    <td width="30"><? echo $i; ?></td>
                    <td width="100"><? echo $row[csf('subcon_job')]; ?></td>
                    <td width="120" style="text-align:center;"><? echo $row[csf('order_no')]; ?></td>
                    <td width="80" style="text-align:center;"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                    <td style="text-align:center;"><? echo change_date_format($row[csf('delivery_date')]); ?></td>
                </tr>
				<? 
                $i++; 
            } 
            ?>
        </tbody>
    </table>
	<?    
	exit();
}

if($action=="req_sys_popup")
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
            else if(val==3) $('#search_by_td').html('WO No');
		}
	  </script>
    </head>
    <body>
		<div align="center" style="width:100%;" >
             <fieldset>
                <form name="searchjobfrm_1"  id="searchjobfrm_1" autocomplete="off">
                    <table width="760" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                        <thead> 
                        	<tr>
                                <th colspan="6"><?php echo create_drop_down("cbo_string_search_type", 130, $string_search_type, '', 1, "-- Searching Type --"); ?></th>
                            </tr>
                            <tr>              	 
                                <th width="150">Location</th>
                                <!-- <th width="100">Production ID</th> -->
                                <th width="150">Search By</th>
                            	<th width="150" id="search_by_td">Requisition No</th>
                                <th width="130" colspan="2">Issue Date Range</th>
                                <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:90px;" /></th> 
                            </tr>          
                        </thead>
                        <tbody>
                            <tr class="general">
                                <td><?php echo create_drop_down( "cbo_location_name", 150, "select id, location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name ASC","id,location_name", 1, "-Select Location-", 0, "","","","","","",3 ); ?></td>
                                <td>
									<?php
                                        $search_by_arr=array(1=>'Job No', 2=>'Requisition No', 3=>'WO No.');
                                        echo create_drop_down('cbo_type', 150, $search_by_arr, '', 0, '', 2, 'search_by(this.value)', 0);
                                    ?>
                                </td>
                                <td>
                                    <input type="text" name="txt_search_string" id="txt_search_string" class="text_boxes" style="width:150px" placeholder="" />
                                </td>
                                
                                <td><input name="txt_date_from" id="txt_date_from" class="datepicker" placeholder="From Date" style="width:60px"></td>
                                <td><input name="txt_date_to" id="txt_date_to" class="datepicker" placeholder="To Date" style="width:60px"></td> 
                                <td align="center">
                                    <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( '<?php echo $data; ?>'+'_'+document.getElementById('cbo_location_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('cbo_type').value+'_'+document.getElementById('txt_search_string').value+'_'+document.getElementById('cbo_year_selection').value, 'create_req_no_list_view', 'search_div', 'raw_material_item_issue_controller', 'setFilterGrid(\'tbl_list_search\',-1)')" style="width:90px;" />
                                    
                                     <input type="hidden" id="hidden_production_data" name="hidden_production_data" value="" />
                                </td>
                            </tr>
                            <tr>
                                <td colspan="6" align="center" valign="middle">
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

if($action=="create_req_no_list_view")
{
	$data=explode('_',$data);
	$company_id=$data[0];
	$location_id=$data[1];
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
			if($search_by==1) $search_com_cond=" and a.job_no='$search_str' ";
			else if($search_by==2) $search_com_cond=" and a.requisition_no='$search_str'";
			else if($search_by==3) $search_com_cond=" and b.order_no='$search_str'";

		}
	}
	else if($search_type==4 || $search_type==0)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond=" and a.job_no like '%$search_str%'";
			else if($search_by==2) $search_com_cond=" and a.requisition_no like '%$search_str%'";
            else if($search_by==3) $search_com_cond=" and b.order_no like '%$search_str%'";
        }
	}
	else if($search_type==2)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="    and a.job_no like '$search_str%'";
			else if($search_by==2) $search_com_cond=" and a.requisition_no like '$search_str%'";
            else if($search_by==3) $search_com_cond=" and b.order_no like '$search_str%'";
        }
	}
	else if($search_type==3)
	{
		if($search_str!="")
		{
			if($search_by==1) $search_com_cond="    and a.job_no like '%$search_str' ";
			else if($search_by==2) $search_com_cond="   and a.order_no like '%$search_str'";
            else if($search_by==3) $search_com_cond=" and b.order_no like '%$search_str'";
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

	$issue_sql = "select a.req_id,sum(b.cons_quantity) as cons_quantity from inv_issue_master a, inv_transaction b
	where a.id=b.mst_id and a.status_active=1 and b.status_active=1 and a.entry_form=265 and b.transaction_type=2 and b.item_category in(101,22) and a.company_id='$company_id'  $sql_cond $credientian_cond group by a.req_id";
	$issue_sql_res=sql_select($issue_sql);
	foreach($issue_sql_res as $row)
	{
	 	$issue_qty_arr[$row[csf('req_id')]]['cons_quantity']+=$row[csf('cons_quantity')];
	}
	$issue_rtn_sql = "SELECT a.req_id as REQ_ID, sum(b.cons_quantity) as CONS_QUANTITY from inv_issue_master a, inv_transaction b,inv_receive_master c
	where a.id=b.issue_id and b.mst_id=c.id and a.issue_basis=7 and a.entry_form=265 and c.entry_form=266 and a.status_active=1 and b.status_active=1 and c.status_active=1 and b.transaction_type=4 and b.item_category in (101,22) and a.company_id='$company_id' group by a.req_id";
	// echo $issue_rtn_sql ;die;
	$issue_rtn_sql_res=sql_select($issue_rtn_sql);
	foreach($issue_rtn_sql_res as $row)
	{
	 	$issue_rtn_qty_arr[$row['REQ_ID']]['cons_quantity']+=$row['CONS_QUANTITY'];
	}
	?>
	<body>
		<div align="center">
			<fieldset style="width:670px;margin-left:10px">
				<form name="searchprocessfrm_1"  id="searchprocessfrm_1" autocomplete="off">
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="670" class="rpt_table" >
						<thead>
							<th width="30">SL</th>
							<th width="150">Requisition No</th>
                            <th width="60">Issue Date</th>
                            <th width="150">Job No</th>
                            <th width="150">Order No</th>
                            <th>Section</th>
						</thead>
					</table>
					<div style="width:670px; overflow-y:scroll; max-height:300px;">
						<table cellspacing="0" cellpadding="0" border="1" rules="all" width="650" class="rpt_table" id="tbl_list_search" >
							<?
							$sql = "select a.id, a.job_no, a.requisition_no, a.issue_date, b.order_no,a.location_id, a.store_id,a.section_id, sum(c.requisition_qty) as requisition_qty
									from trims_raw_mat_requisition_mst a, trims_job_card_mst b , trims_raw_mat_requisition_dtls c
									where a.entry_form in (427,501) and a.id=c.mst_id $date_cond and a.status_active=1 and b.status_active=1 and c.status_active=1  and a.job_no=b.trims_job and a.company_id='$company_id' $location_cond $search_com_cond
									group by a.id, a.job_no, a.requisition_no, a.issue_date, b.order_no,a.location_id, a.store_id,a.section_id
									order by id desc";
							// echo $sql;   die;
							$sql_res=sql_select($sql);
							$i=1; $cum_issue_qty=0; // $sub_operation=''; $batch_no='';  $operation_type='';
							foreach($sql_res as $row)
							{
								$cum_issue_qty=$issue_qty_arr[$row[csf('id')]]['cons_quantity']-$issue_rtn_qty_arr[$row[csf('id')]]['cons_quantity'];
								$requisition_qty=$row[csf('requisition_qty')];
								//echo $requisition_qty.'=='.$cum_issue_qty;
								if($requisition_qty>$cum_issue_qty){
									if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									?>
									<tr bgcolor="<?php echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<?php echo $i;?>" onClick="js_set_value('<?php echo $row[csf('id')].'_'.$row[csf('requisition_no')].'_'.$row[csf('location_id')].'_'.$row[csf('store_id')].'_'.$row[csf('section_id')]; ?>')">
										<td width="30" align="left"><?php echo $i; ?></td>	
										<td width="150" align="left"><?php echo $row[csf('requisition_no')]; ?></td>
	                                    <td width="60" align="left"><?php echo change_date_format($row[csf('issue_date')]); ?>&nbsp;</td>
	                                    <td width="150" align="left"><?php echo $row[csf('job_no')]; ?></td>
	                                    <td width="150" align="left"><?php echo $row[csf('order_no')]; ?></td>
	                                    <td align="left"><?php echo $trims_section[$row[csf('section_id')]]; ?></td>
									</tr>
									<?
									$i++;
								}
								
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




if($action=="items_search_list_view")
{
	$data=explode('**',$data);
	$remarks_no=$data[3];
	$requisition_no=$data[4];
	$delivery=$data[10];
	$indent_no=trim($data[11]);
	//var_dump($data);die;
	if($data[0]!=0){ $company_id=" and company_id = $data[0]";}else{ echo "Select Company"; die;}
	if($data[3]!=''){ $remarks=" and remarks like '$remarks_no%'";}else{ echo "";}
	if($data[4]!=''){ $manual_requisition_no=" and manual_requisition_no like '$requisition_no%'";}else{ echo "";}
	if($data[5]!=0){ $location_id=" and location_id = $data[5]";}else{ echo "";}
	if($data[6]!=0){ $division_id=" and division_id = $data[6]";}else{ echo "";}
	if($data[7]!=0){ $department_id=" and department_id = $data[7]";}else{ echo "";}
	if($data[8]!=0){ $section_id=" and section_id = $data[8]";}else{ echo "";}
	if($data[9]!=0){ $sub_section_id=" and sub_section_id = $data[9]";}else{ echo "";}
	if($data[10]!=''){ $delivery_id=" and delivery_point like '$delivery%'";}else{ echo "";}
	if($data[11]!=''){ $ind_id=" and itemissue_req_sys_id like '%$indent_no'";}else{ echo "";}
	//$date=change_date_format($data[1],'mm-dd-yyyy');
	//if($data[1]!=0){ $indent_date=" and indent_date = $data[1]";}else{ $indent_date=""; }
	$section_library=return_library_array( "select id, section_name from lib_section", "id", "section_name"  );
	$department=return_library_array( "select id, department_name from lib_department", "id", "department_name"  );
	$location=return_library_array( "select id, location_name from lib_location", "id", "location_name"  );
	$division=return_library_array( "select id, division_name from lib_division", "id", "division_name"  );
	$section_library=return_library_array( "select id, section_name from lib_section", "id", "section_name"  );


	$date=$data[1];
	$re_date=$data[2];
		if($data[1]!=0)
	{
		if($db_type==0)
		{
			$indent_date = "and indent_date ='".change_date_format($date,'yyyy-mm-dd')."'";
		}
		else if($db_type==2)
		{
			$indent_date = "and indent_date ='".change_date_format($date,'','',1)."'";
		}
	}
	else
	{
		$indent_date = "";
	}

	if($data[2]!=0)
	{
		if($db_type==0)
		{
			$require_date = "and required_date ='".change_date_format($re_date,'yyyy-mm-dd')."'";
		}
		else if($db_type==2)
		{
			$require_date = "and required_date ='".change_date_format($re_date,'','',1)."'";
		}
	}
	else
	{
		$require_date = "";
	}

	$sql="select id, itemissue_req_sys_id, company_id, indent_date, required_date, location_id, division_id, department_id, section_id, sub_section_id, delivery_point, remarks, manual_requisition_no, is_approved from inv_item_issue_requisition_mst where status_active=1 and is_deleted=0 $remarks $manual_requisition_no $company_id $indent_date $require_date $location_id $division_id $department_id $section_id $sub_section_id $delivery_id $ind_id";
		//echo $sql;// die;
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$arr=array (0=>$company_arr,4=>$location,5=>$division,6=>$department,7=>$section_library);

	echo  create_list_view("list_view", "Company,Indent No.,Indent date,Required Date,Location,Division,Department,Section,Sub Section,Delivery Point", "150,100,80,100,100,80,80,80,80","1030","320",0, $sql, "hidden_item_value", "id,itemissue_req_sys_id,indent_date,location_id,department_id,section_id,is_approved", "", 1, "company_id,0,0,0,location_id,division_id,department_id,section_id", $arr , "company_id,itemissue_req_sys_id,indent_date,required_date,location_id,division_id,department_id,section_id,sub_section_id,delivery_point", "",'','0,0,3,3');

}

if($action=="check_reqn_no")
{
	//echo $data;
	$sql = sql_select("select id,company_id from inv_item_issue_requisition_mst where status_active=1 and is_deleted=0 and itemissue_req_sys_id='$data' ");
    if(count($sql)>0) echo $sql[0][csf('company_id')]."**".$sql[0][csf('id')];
	else{ echo 0; }
	exit();
}

if ($action=="show_item_issue_listview")
{
	//var_dump($data);die;
	if(is_numeric($data))
	{
		$sql="select id, mst_id as rid, req_qty, item_group, item_description, product_id, lot from inv_itemissue_requisition_dtls where mst_id='$data' and status_active=1 and is_deleted=0 ";
	}
	else
	{
		 $sql="select a.id as rid, a.itemissue_req_sys_id, a.department_id, b.mst_id, b.req_qty, b.item_group, b.item_description, b.product_id, b.lot 
		 from inv_item_issue_requisition_mst a, inv_itemissue_requisition_dtls b 
		 where b.mst_id=a.id and a.itemissue_req_sys_id='$data' and a.status_active=1 and a.is_deleted=0";
	}
	$nameArray=sql_select( $sql );

	?>
 	<div style="width:290px;">
	    <table width="290" cellspacing="0" cellpadding="0" border="0" rules="all" class="rpt_table" align="left">
	    	<thead>
				<tr>
					<th width="35">SL</th>
					<th width="80">Item Group</th>
					<th width="95">Item Description</th>
                    <th >Req. Qty.</th>
				</tr>
		</thead>
	     </table>
	<div id="" style="max-height:363px; width:307px; overflow-y:scroll" >
	    <table width="290" cellspacing="0" cellpadding="0" border="0" rules="all"  class="rpt_table" align="left">
			<tbody>
	        <?
			$item_group=return_library_array("select id,item_name from lib_item_group",'id','item_name');
	         $i=1;
			foreach ($nameArray as $selectResult)
			{
				?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="get_php_form_data('<? echo $selectResult[csf('product_id')];?>+**+<? echo $selectResult[csf('req_qty')];?>+**+<? echo $selectResult[csf('rid')];?>','populate_item_details_form_data_dtls','requires/raw_material_item_issue_controller');" >
                    <td width="35"><? echo $i; ?></td>
                    <td width="80"><? echo $item_group[$selectResult[csf("item_group")]];?></td>
                    <td width="95"><? echo $selectResult[csf("item_description")];?></td>
                    <td align="right"><? echo $selectResult[csf("req_qty")];?></td>
                </tr>
            	<? $i++;
			}
			?>
            </tbody>
		</table>
	    </div>
    </div>
	<?
}

if($action=="populate_item_details_form_data_dtls")
{
	$ex_data = explode("**",$data);

	$qnty=sql_select("select sum(a.cons_quantity) as Q from inv_transaction a , inv_issue_master b where a.prod_id=$ex_data[0] and a.mst_id=b.id and b.req_id=$ex_data[2] and a.transaction_type=2 and a.status_active=1 and a.item_category=101");

	$total_qnty=$qnty[0]['Q'];
	$data_ar=sql_select("select id,item_group_id,sub_group_code,item_description,unit_of_measure,current_stock,item_category_id,item_size from product_details_master where id='$ex_data[0]' and item_category_id=101");
	foreach ($data_ar as $info)
	{
		echo "document.getElementById('cbo_item_group').value 			= '".$info[csf("item_group_id")]."';\n";
		echo "document.getElementById('cbo_item_category').value 		= '".$info[csf("item_category_id")]."';\n";
		echo "document.getElementById('txt_item_desc').value 			= '".$info[csf("item_description")].",".$info[csf("item_size")]."';\n";
		echo "document.getElementById('cbo_uom').value 					= '".$info[csf("unit_of_measure")]."';\n";
		echo "document.getElementById('txt_current_stock').value 		= '".$info[csf("current_stock")]."';\n";
		echo "document.getElementById('current_prod_id').value 			= '".$info[csf("id")]."';\n";
		//echo "document.getElementById('hidden_req_qnty').value 			= '".$ex_data[1]."';\n";
		//echo "document.getElementById('total_issued_qnty').value 		= '".$total_qnty."';\n";
		//echo "document.getElementById('cbo_store_name').value 			= '0';\n";
		//echo "document.getElementById('txt_current_stock').value 		= '';\n";

	}
 	echo "load_drop_down( 'requires/raw_material_item_issue_controller',"."document.getElementById('cbo_company_id').value"." + '**' + $ex_data[0], 'load_drop_down_store_for_item', 'store_td' );";

exit();

}

if($action=="chk_issue_requisition_variabe")
{
	
    $sql =  sql_select("select allocation,id from variable_settings_inventory where company_name = $data and variable_list = 24 and is_deleted = 0 and status_active = 1");
	$return_data="";
    if(count($sql)>0)
	{
		$return_data=$sql[0][csf('allocation')];
	}
	else
	{ 
		$return_data=0; 
	}
	
	/*if($db_type==0)
	{
		$necessity_sql=sql_select("select b.approval_need as approval_need, a.id as max_id from approval_setup_mst a, approval_setup_dtls b 
		where a.id=b.mst_id and a.company_id = $data and b.page_id = 23 and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0
		order by a.id desc limit 1");
	}
	else
	{
		$necessity_sql=sql_select("select b.approval_need as approval_need, a.id as max_id from approval_setup_mst a, approval_setup_dtls b 
		where a.id=b.mst_id and a.company_id = $data and b.page_id = 23 and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and a.id=(select max(id) as id from approval_setup_mst where company_id = $data and status_active=1)");
	}
	$return_data.="**".$necessity_sql[0][csf("approval_need")];*/
	
	$variable_lot=return_field_value("auto_transfer_rcv","variable_settings_inventory","company_name=$data and variable_list=32 and status_active=1 and is_deleted=0");
	
	echo $return_data."__".$variable_lot;
	die;
}

if ($action=="load_drop_down_store_for_item")
{

    $data=explode("**",$data);
	$company_id=$data[0];
	$prod_id=$data[1];
	//echo create_drop_down( "cbo_store_name", 130, "select a.id,a.store_name from lib_store_location a, lib_store_location_category b where a.id=b.store_location_id and b.category_type in (8,9,10,11) and a.status_active=1 and a.is_deleted=0 and FIND_IN_SET($data,a.company_id) group by a.id order by a.store_name","id,store_name", 1, "-- Select --", "", "","" );
	$store_res =  sql_select("select a.store_id  from inv_transaction a where a.prod_id = $prod_id and a.company_id = $company_id and a.is_deleted = 0  and a.status_active = 1");
        foreach($store_res as $row)
        {
            $store_ids .= $row[csf('store_id')].",";
        }

        $store_ids = implode(",",array_unique(explode(",", chop($store_ids, ','))));
        if($store_ids){
           // $store_ids = $store_ids;
		    $store_ids = "and a.id in($store_ids)";
        }else{
            $store_ids = "";
        }
	echo create_drop_down( "cbo_store_name", 130, "select a.id,a.store_name from lib_store_location a, lib_store_location_category b where a.id=b.store_location_id and b.category_type in ( $item_cate_credential_cond ) and a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id $store_location_credential_cond $store_ids group by a.id,a.store_name order by a.store_name","id,store_name", 1, "-- Select --", $selected, "get_php_form_data(this.value+'__'+document.getElementById('current_prod_id').value, 'populate_store_prod_data', 'requires/raw_material_item_issue_controller');","" );
	exit();
}


if($action=="populate_store_prod_data")
{
	$data_ref=explode("__",$data);
	$store_id=$data_ref[0];
	$prod_id=$data_ref[1];
	if($prod_id>0 && $store_id>0){
		$store_stock_sql=sql_select("select sum((case when transaction_type in(1,4,5) then cons_quantity else 0 end)-(case when transaction_type in(2,3,6) then cons_quantity else 0 end)) as balance_qnty from inv_transaction where status_active=1 and prod_id=$prod_id and store_id=$store_id");
		$balance = ($store_stock_sql[0][csf("balance_qnty")] =='') ? 0 : $store_stock_sql[0][csf("balance_qnty")];
		echo "document.getElementById('txt_current_stock').value 			= '".$balance."';\n";

	}else{
		echo "document.getElementById('txt_current_stock').value 			= '0';\n";
	}
	exit();
}


?>
