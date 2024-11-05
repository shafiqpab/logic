<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

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

if ($action=="load_drop_down_location")
{
    $sql="select id,location_name from lib_location where company_id=$data and is_deleted=0 and status_active=1 $location_credential_cond";
    $result=sql_select($sql);
    $selected=0;
    if (count($result)==1) {
        $selected=$result[0][csf('id')];
    }

    echo create_drop_down( "cbo_location_name", 162,$sql,"id,location_name", 1, "-- Select --", $selected, "" );
    die;
}

if($action == "load_drop_down_machine_no")
{
    $data = explode("_", $data);
    $sql = "SELECT id,machine_no from lib_machine_name where category_id=$data[0] and is_deleted=0 and status_active=1 order by machine_no";
    $result = sql_select($sql);
    // echo count($result); die;
    $selected = 0;
    if (count($result)==1) {
        $selected = $result[0][csf('id')];
    }

    echo create_drop_down( "cboMachineNo_".$data[1], 90, $sql, "id,machine_no", 1, "-- Select --", $selected, "", 0, "", "", "", "", "", "", "cboMachineNo[]", "cboMachineNo_".$data[1] );
    die;
}
if($action=="load_drop_down_supplier_new")
{
    extract($data);
	
	$newData = explode("*",$data);
	$company_id = $newData[0];
	$mst_id = $newData[1];
  
    echo create_drop_down( "cbo_supplier", 162, "SELECT distinct a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b, lib_supplier_tag_company c, WO_NON_ORDER_INFO_MST d 
	where a.id=b.supplier_id and a.id=c.supplier_id and a.id = d.supplier_id and b.party_type in(1,5,6,7,8,30,36,37,39,92) and c.tag_company in($company_id) and a.status_active IN(1,3) and a.is_deleted=0 and d.id = $mst_id union all SELECT distinct a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b, lib_supplier_tag_company c 
	where a.id=b.supplier_id and a.id=c.supplier_id and b.party_type in(1,5,6,7,8,30,36,37,39,92) and c.tag_company in($company_id) and a.status_active IN(1) and a.is_deleted=0 ","id,supplier_name","id,supplier_name", 1, "-- Select --", 0, "",0 );
    exit();


    // echo create_drop_down( "cbo_supplier", 162, "SELECT distinct a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b, lib_supplier_tag_company c 
	// where a.id=b.supplier_id and a.id=c.supplier_id and b.party_type in(1,5,6,7,8,30,36,37,39,92) and c.tag_company in($company_id) and a.status_active IN(1) and a.is_deleted=0 
	// union all
	// select distinct a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b, lib_supplier_tag_company c, WO_NON_ORDER_INFO_MST d 
	// where a.id=b.supplier_id and a.id=c.supplier_id and a.id = d.supplier_id and b.party_type in(1,5,6,7,8,30,36,37,39,92) and c.tag_company in($company_id) and a.status_active IN(1,3) and a.is_deleted=0 and d.id = $mst_id
	// order by supplier_name","id,supplier_name","id,supplier_name", 1, "-- Select --", 0, "",0 );
    // exit();
}
if ($action=="load_drop_down_supplier")
{
    echo create_drop_down( "cbo_supplier", 162, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b, lib_supplier_tag_company c where a.id=b.supplier_id and a.id=c.supplier_id and b.party_type in(1,5,6,7,8,30,36,37,39,92) and c.tag_company in($data) and a.status_active=1 and a.is_deleted=0 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select --", 0, "",0 );
    exit();
}

if ($action=="load_drop_down_division")
{
	$sql="select id,division_name from lib_division where company_id=$data and is_deleted=0  and status_active=1";
	$result=sql_select($sql);
	$selected=0;
	if (count($result)==1) {
		$selected=$result[0][csf('id')];
	}
	
	echo create_drop_down( "cbo_division_name", 160,$sql,"id,division_name", 1, "-- Select --", 0, "load_drop_down( 'requires/service_work_order_controller', this.value, 'load_drop_down_department','department_td');" );
	die;
}

if ($action=="load_drop_down_department")
{
	echo create_drop_down( "cbo_department_name", 160,"select id,department_name from lib_department where division_id=$data and is_deleted=0 and status_active=1","id,department_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/service_work_order_controller', this.value, 'load_drop_down_section','section_td');" );
	die;
}

if ($action=="load_drop_down_section")
{
	if ($data != ''){
		echo create_drop_down( "cbo_section_name", 160,"select id,section_name from lib_section where department_id=$data and is_deleted=0 and status_active=1","id,section_name", 1, "-- Select --", $selected, "" );
	} else {
		echo create_drop_down( "cbo_section_name", 160,$blank_array,"", 1, "-- Select --", $selected, "" );
	}
	die;
}

// Conversion Exchange Rate
if($action=="check_conversion_rate")
{
    $data=explode("**",$data);
    if($db_type==0)
    {
        $conversion_date=change_date_format($data[1], "Y-m-d", "-",1);
    }
    else
    {
        $conversion_date=change_date_format($data[1], "d-M-y", "-",1);
    }
    $currency_rate=set_conversion_rate( $data[0], $conversion_date );
    echo "1"."_".$currency_rate;
    exit();
}


if($action=="print_button_variable_setting")
{
    $print_report_format=0;
    $print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=19 and report_id=206 and is_deleted=0 and status_active=1");
    echo "document.getElementById('report_ids').value = '".$print_report_format."';\n";
    echo "print_report_button_setting('".$print_report_format."');\n";
    exit();
}

if($action=="item_description_popup")
{
    echo load_html_head_contents("Item Description Info", "../../../", 1, 1,'','','');
    extract($_REQUEST);
    //echo $company;die;
    ?>
    <script>

    var selected_id = new Array;
    var vselected_name = new Array();
    var selected_attach_id = new Array();

    function check_all_data()
    {
        var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
        tbl_row_count = tbl_row_count - 1;

        for( var i = 1; i <= tbl_row_count; i++ ) {
            eval($('#tr_'+i).attr("onclick"));
        }
    }

    function toggle( x, origColor ) {
        var newColor = 'yellow';
        if ( x.style ) {
            x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
        }
    }

    function js_set_value(id)
    {
        //alert (id);
        var str=id.split("_");
        toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFFF' );
        str=str[1];
        if( jQuery.inArray(  str , selected_id ) == -1 )
        {
            selected_id.push( str );
        }
        else
        {
            for( var i = 0; i < selected_id.length; i++ ) {
                if( selected_id[i] == str  ) break;
            }
            selected_id.splice( i, 1 );
        }
        var id = '';
        for( var i = 0; i < selected_id.length; i++ ) {
            id += selected_id[i] + ',';
        }
        id = id.substr( 0, id.length - 1 );

        $('#item_1').val( id );

    }

    </script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
        <fieldset style="width:580px;">
            <table width="570" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
                <thead>
                    <th>Item Category</th>
                    <th>Item group</th>
                    <th>Item Code</th>
                    <th>Item Description</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th>
                </thead>
                <tbody>
                    <tr>
                        <td>
                        <?
                        echo create_drop_down( "cbo_item_category", 130, "select category_id, short_name from  lib_item_category_list where status_active=1 and is_deleted=0 and category_type=1 and category_id not in(4,11) order by short_name","category_id,short_name", 1, "-- Select --", "", "", 0,"" );
                        ?>
                        </td>
                        <td align="center">
                            <input type="text" style="width:120px" class="text_boxes" name="txt_item_group" id="txt_item_group" />
                        </td>
                        <td align="center">
                            <input type="text" style="width:130px" class="text_boxes" name="txt_item_code" id="txt_item_code" />
                        </td>
                        <td align="center">
                             <input type="text" style="width:130px" class="text_boxes" name="txt_item_description" id="txt_item_description" />
                        </td>
                        <td align="center">
                            <input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company; ?>'+'**'+document.getElementById('cbo_item_category').value+'**'+document.getElementById('txt_item_description').value+'**'+document.getElementById('txt_item_code').value+'**'+document.getElementById('txt_item_group').value, 'item_description_popup_list_view', 'search_div', 'service_work_order_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:100px;" />
                    </td>
                    </tr>
                </tbody>
            </table>
        </fieldset>
        <div style="margin-top:15px" id="search_div"></div>
    </form>
    </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if ($action=="item_description_popup_list_view")
{
    extract($_REQUEST);
    list($company,$itemCategory,$item_description,$item_code,$item_group)=explode('**',$data);
    ?>

    </head>
    <body>
        <div align="center" style="width:100%" >
            <form name="order_popup_1"  id="order_popup_1">
            <fieldset style="width:900px">
            <input type="hidden" id="item_1" />
            <?
            if($item_description!=""){$search_con=" and a.item_description like('%$item_description%')";}
            if($item_code!=""){$search_con .= " and a.item_code like('%$item_code')";}
            if($item_group!=""){$search_con .= " and b.item_name like('%$item_group%')";}
            if($itemCategory){$search_con .= " and item_category_id='$itemCategory'";}

            if($itemIDS!="") $itemIDScond = " and a.id not in ($itemIDS)"; else $itemIDScond = "";
            $arr=array (1=>$item_category,5=>$unit_of_measurement,9=>$row_status);//5=>$unit_of_measurement,

            $sql="select a.id, a.item_account, a.item_category_id, a.item_description, a.item_size, a.item_group_id, a.unit_of_measure, a.current_stock, a.re_order_label, a.status_active, b.item_name, a.order_uom
            from product_details_master a, lib_item_group b
            where a.item_group_id=b.id and a.status_active in(1,3) and a.is_deleted=0 and company_id='$company' and a.item_category_id in (89,51,52,49,90,99,55,21,67,93,59,48,64,15,57,66,45,47,107,54,70,50,37,69,68,18,46,60,62,9,16,17,38,92,65,10,33,44,34,35,63,19,22,61,97,36,56,8,41,40,91,43,53,20,94,32,58,39) and a.entry_form<>24 $itemIDScond $search_con";
            //echo $sql;//die;
            echo  create_list_view ( "list_view","Item Account,Item Category,Item Description,Item Size,Item Group,Order UOM,Stock,Re-Order Level,Product ID,Status", "120,100,140,80,100,80,80,80,80,50","950","250",0, $sql, "js_set_value", "id", "", '', "0,item_category_id,0,0,0,order_uom,0,0,0,status_active", $arr , "item_account,item_category_id,item_description,item_size,item_name,order_uom,current_stock,re_order_label,id,status_active", "", 'setFilterGrid("list_view",-1);','0,0,0,0,0,0,1,1,0,0','',1 );
            ?>
            </fieldset>
            </form>
        </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if ($action=="load_php_popup_to_form_itemDtls")
{
    $explode_data = explode("**",$data);
    $data_id=$explode_data[0];
    $company=$explode_data[1];
    $i=$explode_data[2];

    if($data_id!="")
    {
        $sql="select a.id, a.item_account, a.item_category_id, a.item_description, a.item_size, a.item_group_id, a.unit_of_measure, a.current_stock, a.re_order_label, a.status_active, a.brand_name as brand, a.origin, a.model, b.item_name, a.order_uom
        from product_details_master a, lib_item_group b
        where a.id in ($data_id) and a.status_active in(1,3) and a.item_group_id=b.id";
        $nameArray=sql_select($sql);

        foreach ($nameArray as $val)
        {
            ?>
            <tr class="general" id="tr_<? echo $i;?>">
                <td>
                <?
                    echo create_drop_down( "cboServiceFor_".$i, 90, $service_for_arr, "", 1, "-- Select --",$selected, 0, "", 0, "", "", "", "", "", "cboServiceFor[]", "cboServiceFor_".$i );
                ?>
                </td>
                <td align="center">
                    <input type="text" name="txtServiceDetails[]" id="txtServiceDetails_<?= $i; ?>" class="text_boxes" style="width:120px" onDblClick="fnc_service_details(<?=$i;?>)" placeholder="Double Click To Search" readonly />
                </td>

                <td align="center">
                    <input type="text" name="txtItemDescription[]"; id="txtItemDescription_<?= $i; ?>" class="text_boxes" style="width:130px;" onDblClick="itemDetailsPopup(<?= $i;?>)" placeholder="Double Click To Search" value="<? echo $val[csf("item_description")]; ?>" disabled/>
                    <input type="hidden" name="txt_item_id[]" id="txt_item_id_<? echo $i;?>" value="<? echo $val[csf("id")];?>" />
                    <input type="hidden" name="txt_row_id[]" id="txt_row_id_<? echo $i;?>" value="" />
                    <input type="hidden" name="txt_req_no_id[]" id="txt_req_no_id_<?= $i; ?>"  />
                    <input type="hidden" name="txt_req_dtls_id[]" id="txt_req_dtls_id_<?= $i; ?>"  />
                </td>
                <td align="center">
                    <?
                    echo create_drop_down( "cboItemCategory_".$i, 120, $item_category,"", 1, "-- Select --", $val[csf("item_category_id")], "",1,"","","","","","","cboItemCategory[]","cboItemCategory_".$i );
                    ?>
                </td>
                <td align="center">
                    <?
                    echo create_drop_down( "cboItemGroup_".$i,100,"select id,item_name  from lib_item_group","id,item_name", 1,"Select",$val[csf("item_group_id")], "",1,"","","","","","","cboItemGroup[]","cboItemGroup_".$i );
                    ?>
                </td>
                <td align="center">
                    <? echo create_drop_down( "cboMachineCategory_".$i, 90, $machine_category,"", 1, "--Select--", 0, "load_drop_down( 'requires/service_work_order_controller', this.value+'_'+$i, 'load_drop_down_machine_no','machine_no_td_".$i."' );",0, "", "", "", "", "", "", "cboMachineCategory[]", "cboMachineCategory_".$i ); ?>
                </td>
                <td align="center" id="machine_no_td_<?= $i; ?>">
                    <?
                        echo create_drop_down( "cboMachineNo_".$i, 90, $blank_array, "", 1, "-- Select --", 0, "", 0, "", "", "", "", "", "", "cboMachineNo[]", "cboMachineNo_".$i );
                    ?>
                </td>
                <td align="center">
                    <? echo create_drop_down( "cboUom_".$i, 70, $service_uom_arr,"", 1, "--Select--", 0, "",0, "", "", "", "", "", "", "cboUom[]", "cboUom_".$i ); ?>
                </td>
                <td>
                    <input type="text" name="txtqnty[]" id="txtqnty_<?= $i; ?>" class="text_boxes_numeric" style="width:60px" onKeyUp="calculate_amount(<?= $i; ?>)" />
                    <input type="hidden" name="hiddenqnty[]" id="hiddenqnty_<?= $i; ?>"/>
                </td>
                <td>
                    <input type="text" name="txtrate[]" id="txtrate_<?= $i; ?>" class="text_boxes_numeric" value="" style="width:60px;" onKeyUp="calculate_amount(<?= $i; ?>)" />
                    <input type="hidden" name="hiddentxtrate[]" id="hiddentxtrate_<?= $i; ?>"/>
                </td>
                <td><input type="text" name="txtamount[]" id="txtamount_<?= $i; ?>" class="text_boxes_numeric" style="width:80px;" readonly /></td>
                <td><input type="text" name="txt_service_number[]" id="txt_service_number_<?= $i; ?>" class="text_boxes_numeric" style="width:80px;" /></td>
                <td><input type="text" name="txtremarks[]" id="txtremarks_<?= $i; ?>" class="text_boxes" style="width:120px;" /></td>
                <td>
                    <input type="button" name="increase[]" id="increase_<?= $i; ?>" style="width:18px" class="formbuttonplasminus" value="+" onClick="add_dtls_tr(<?= $i; ?>)" />
                    <input type="button" name="decrease[]" id="decrease_<?= $i; ?>" style="width:18px" class="formbuttonplasminus" value="-" onClick="fnc_delet_dtls_tr(<?= $i; ?>);" />
                    <input type="hidden" name="txtTagMaterials[]" id="txtTagMaterials_1" />
                </td>
            </tr>
            <?
            $i++;
        }
    }
    exit();
}

if ($action=="append_load_details_container")
{
    //echo $data;
    $i = $data;
    ?>
    <tr class="general" id="tr_<?= $i; ?>">
        <td>
            <?
                echo create_drop_down( "cboServiceFor_".$i, 90, $service_for_arr, "", 1, "-- Select --",$selected, 0, "", 0, "", "", "", "", "", "cboServiceFor[]", "cboServiceFor_".$i );
            ?>
        </td>
        <td align="center">
            <input type="text" name="txtServiceDetails[]" id="txtServiceDetails_<?= $i; ?>" class="text_boxes" style="width:120px" onDblClick="fnc_service_details(<?= $i; ?>)" placeholder="Double Click To Search" readonly />
        </td>

        <td align="center">
            <input type="text" name="txtItemDescription[]" id="txtItemDescription_<?= $i; ?>" class="text_boxes" style="width:130px;" onDblClick="itemDetailsPopup(<?= $i; ?>)" placeholder="Double Click To Search" readonly />
            <input type="hidden" name="txt_item_id[]" id="txt_item_id_<? echo $i; ?>" />
            <input type="hidden" name="txt_row_id[]" id="txt_row_id_<? echo $i; ?>" value="" />
            <input type="hidden" name="txt_req_no_id[]" id="txt_req_no_id_<?= $i; ?>"  />
            <input type="hidden" name="txt_req_dtls_id[]" id="txt_req_dtls_id_<?= $i; ?>"  />
        </td>
        <td align="center">
            <? echo create_drop_down( "cboItemCategory_".$i, 120, $blank_array,"", 1, "--Select--", 0, "", 1, "", "", "", "", "", "", "cboItemCategory[]", "cboItemCategory_".$i ); ?>
        </td>
        <td align="center">
            <? echo create_drop_down( "cboItemGroup_".$i, 120, $blank_array,"", 1, "--Select--", 0, "",1, "", "", "", "", "", "", "cboItemGroup[]", "cboItemGroup_".$i ); ?>
        </td>
        <td align="center">
            <? echo create_drop_down( "cboMachineCategory_".$i, 90, $machine_category,"", 1, "--Select--", 0, "load_drop_down( 'requires/service_work_order_controller', this.value+'_'+$i, 'load_drop_down_machine_no','machine_no_td_".$i."' );",0, "", "", "", "", "", "", "cboMachineCategory[]", "cboMachineCategory_".$i ); ?>
        </td>
        <td align="center" id="machine_no_td_<?= $i; ?>">
            <?
                echo create_drop_down( "cboMachineNo_".$i, 90, $blank_array, "", 1, "-- Select --", 0, "", 0, "", "", "", "", "", "", "cboMachineNo[]", "cboMachineNo_".$i );
            ?>
        </td>
        <td align="center">
            <? echo create_drop_down( "cboUom_".$i, 70, $service_uom_arr,"", 1, "--Select--", 0, "",0, "", "", "", "", "", "", "cboUom[]", "cboUom_".$i ); ?>
        </td>
        <td>
            <input type="text" name="txtqnty[]" id="txtqnty_<?= $i; ?>" class="text_boxes_numeric" style="width:60px" onKeyUp="calculate_amount(<?= $i; ?>)" />
            <input type="hidden" name="hiddenqnty[]" id="hiddenqnty_<?= $i; ?>" />
        </td>
        <td>
            <input type="text" name="txtrate[]" id="txtrate_<?= $i; ?>" class="text_boxes_numeric" value="" style="width:60px;" onKeyUp="calculate_amount(<?= $i; ?>)" />
            <input type="hidden" name="hiddentxtrate[]" id="hiddentxtrate_<?= $i; ?>"/>
        </td>
        <td><input type="text" name="txtamount[]" id="txtamount_<?= $i; ?>" class="text_boxes_numeric" style="width:80px;" readonly /></td>
        <td><input type="text" name="txt_service_number[]" id="txt_service_number_<?= $i; ?>" class="text_boxes_numeric" style="width:80px;" /></td>
        <td><input type="text" name="txtremarks[]" id="txtremarks_<?= $i; ?>" class="text_boxes" style="width:120px;" /></td>

        <td>
            <input type="button" name="increase[]" id="increase_<?= $i; ?>" style="width:18px" class="formbuttonplasminus" value="+" onClick="add_dtls_tr(<?= $i; ?>)" />
            <input type="button" name="decrease[]" id="decrease_<?= $i; ?>" style="width:18px" class="formbuttonplasminus" value="-" onClick="fnc_delet_dtls_tr(<?= $i; ?>);" />
            <input type="hidden" name="txtTagMaterials[]" id="txtTagMaterials_<?= $i; ?>" />
        </td>
    </tr>
    <?
    exit();
}

if($action=="remarks_popup")
{
	echo load_html_head_contents("Remarks","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
		<script>
        function js_set_value(val)
        {
            document.getElementById('text_new_remarks').value=val;
            parent.emailwindow.hide();
        }
        </script>
    </head>
    <body>
    <div align="center">
        <fieldset style="width:400px;margin-left:4px;">
            <form name="remarksfrm_1"  id="remarksfrm_1" autocomplete="off">
                <table cellpadding="0" cellspacing="0" width="370" >
                    <tr>
                        <td align="center"><input type="hidden" name="auto_id" id="auto_id" value="<? echo $data; ?>" />
                          <textarea id="text_new_remarks" name="text_new_remarks" class="text_area" title="Maximum 1000 Character" maxlength="1000" style="width:370px; height:250px" placeholder="Remarks Here. Maximum 1000 Character." ><? echo $data; ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td align="center">
                     <input type="button" id="formbuttonplasminus" align="middle" class="formbutton" style="width:100px" value="Close" onClick="js_set_value(document.getElementById('text_new_remarks').value)" />
                        </td>
                    </tr>
                </table>
            </form>
        </fieldset>
    </div>    
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
    
}

if($action=="show_dtls_listview_update")
{
    $data_arr=explode("**",$data);
    $mst_id=$data_arr[0];
    $woBasis=$data_arr[1];

    $sql = "SELECT b.id, a.company_name, a.wo_amount, a.up_charge, a.discount, a.net_wo_amount, a.upcharge_remarks, a.discount_remarks, b.item_id, b.supplier_order_quantity, b.rate, b.amount, b.service_number,  b.remarks, b.service_for, b.service_details, b.uom, b.requisition_no, b.requisition_dtls_id, b.tag_materials,b.gross_rate, b.gross_amount, b.machine_category_id, b.machine_no, b.service_lib_id
    from wo_non_order_info_mst a, wo_non_order_info_dtls b 
    where a.id=$mst_id and a.id=b.mst_id and a.entry_form=484 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 
    order by b.id";
    // echo $sql;die;
    $result = sql_select($sql);
    // echo "<pre>";print_r($result);die;
    foreach ($result as $val) {
        if ($val[csf('item_id')] != '')
        $prod_id.=$val[csf('item_id')].',';
    }

    $prod_ids=rtrim($prod_id,',');
    if ($prod_ids != ''){
        $sql_prod=sql_select("select id, item_description, item_category_id, item_group_id from product_details_master where id in($prod_ids) and status_active in(1,3) and is_deleted=0");
        $prod_arr=array();
        foreach ($sql_prod as $val) {
           $prod_arr[$val[csf('id')]]['item_description']=$val[csf('item_description')];
           $prod_arr[$val[csf('id')]]['item_category_id']=$val[csf('item_category_id')];
           $prod_arr[$val[csf('id')]]['item_group_id']=$val[csf('item_group_id')];
        }
    }

    $i=1;$k=1;
    if($woBasis==2) // independent
    {
        if($k==1)
        {
            ?>
                <table class="rpt_table" width="1380" cellspacing="0" cellpadding="0" id="tbl_dtls_item" border="1" rules="all">
                    <thead>
                        <tr>
                            <th width="90" class="must_entry_caption"><font color="blue">Service For</font></th>
                            <th width="120" class="must_entry_caption"><font color="blue">Service Details</font></th>
                            <th width="130">Item Description</th>
                            <th width="100">Item Category</th>
                            <th width="100">Item Group</th>
                            <th width="90">Machine Category</th>
                            <th width="90">Machine No</th>
                            <th width="70">Uom</th>
                            <th width="60" class="must_entry_caption"><font color="blue">Qnty</font></th>
                            <th width="60" class="must_entry_caption"><font color="blue">Rate</font></th>
                            <th width="90">Amount</th>
                            <th width="80">Service Number</th>
                            <th width="120">Remarks</th>
                            <th width="65">Action</th>
                        </tr>
                    </thead>
                    <tbody id="details_part_list">
            <?
            $k++;
        }
        foreach($result as $val)
        {
            ?>
            <tr class="general" id="tr_<?= $i; ?>">
                <td>
                    <?
                        echo create_drop_down( "cboServiceFor_".$i, 90, $service_for_arr, "", 1, "-- Select --",$val[csf("service_for")], "", 0, "", "", "", "", "", "", "cboServiceFor[]", "cboServiceFor_".$i );
                    ?>
                </td>
                <td align="center">
                    <input name="txtServiceDetails[]" id="txtServiceDetails_<?= $i; ?>" class="text_boxes" style="width:120px" value="<? echo $val[csf("service_details")];?>" onDblClick="fnc_service_details(<?=$i;?>)" placeholder="Double Click To Search" readonly/>
                    <input type="hidden" name="hdnServiceId[]" id="hdnServiceId_<?=$i;?>" value="<? echo $val[csf("service_lib_id")];?>"/>
                </td>

                <td align="center">
                    <input type="text" name="txtItemDescription[]" id="txtItemDescription_<?= $i; ?>" class="text_boxes" style="width:130px;" onDblClick="itemDetailsPopup(<?= $i; ?>)" placeholder="Double Click To Search" value="<? echo $prod_arr[$val[csf('item_id')]]['item_description'];?>" disabled/>
                    <input type="hidden" name="txt_item_id[]" id="txt_item_id_<? echo $i; ?> " value="<? echo $val[csf("item_id")];?>"/>
                    <input type="hidden" name="txt_row_id[]" id="txt_row_id_<? echo $i; ?>" value="<? echo $val[csf("id")];?>" />
                    <input type="hidden" name="txt_req_no_id[]" id="txt_req_no_id_<?= $i; ?>"  />
                    <input type="hidden" name="txt_req_dtls_id[]" id="txt_req_dtls_id_<?= $i; ?>"  />
                </td>
                <td align="center">
                    <? echo create_drop_down( "cboItemCategory_".$i, 120, $item_category,"", 1, "-- Select --", $prod_arr[$val[csf('item_id')]]['item_category_id'], "",0,"","","","","","","cboItemCategory[]","cboItemCategory_".$i ); ?>
                </td>
                <td align="center">
                    <? echo create_drop_down( "cboItemGroup_".$i,100,"select id,item_name  from lib_item_group","id,item_name", 1,"Select",$prod_arr[$val[csf('item_id')]]['item_group_id'], "",1,"","","","","","","cboItemGroup[]","cboItemGroup_".$i ); ?>
                </td>
                <td align="center">
                    <? echo create_drop_down( "cboMachineCategory_".$i, 90, $machine_category,"", 1, "--Select--", $val[csf("machine_category_id")], "load_drop_down( 'requires/service_work_order_controller', this.value+'_'+$i, 'load_drop_down_machine_no','machine_no_td_".$i."' );",0, "", "", "", "", "", "", "cboMachineCategory[]", "cboMachineCategory_".$i ); 
                    ?>
                </td>
                <td align="center" id="machine_no_td_<?= $i; ?>">
                    <?
                        echo create_drop_down( "cboMachineNo_".$i, 90, "SELECT id,machine_no from lib_machine_name where category_id='".$val[csf("machine_category_id")]."' and is_deleted=0 and status_active=1 order by machine_no", "id,machine_no", 1, "-- Select --", $val[csf("machine_no")], "", "", "", "", "", "", "", "", "cboMachineNo[]", "cboMachineNo_".$i );
                    ?>
                </td>
                <td align="center">
                    <? echo create_drop_down( "cboUom_".$i, 70, $service_uom_arr,"", 1, "--Select--", $val[csf("uom")], "",0, "", "", "", "", "", "", "cboUom[]", "cboUom_".$i ); ?>
                </td>
                <td>
                    <input type="text" name="txtqnty[]" id="txtqnty_<?= $i; ?>" class="text_boxes_numeric" style="width:60px" onKeyUp="calculate_amount(<?=$i;?>)" value="<? echo $val[csf("supplier_order_quantity")];?>"/>
                    <input type="hidden" name="hiddenqnty[]" id="hiddenqnty_<?= $i; ?>" value="<? echo $val[csf("supplier_order_quantity")];?>"/>
                </td>
                <td>
                    <input type="text" name="txtrate[]" id="txtrate_<?= $i; ?>" class="text_boxes_numeric" style="width:60px;" onKeyUp="calculate_amount(<?=$i;?>)" value="<? echo $val[csf("gross_rate")]; ?>" />
                    <input type="hidden" name="hiddentxtrate[]" id="hiddentxtrate_<?= $i; ?>" value="<? echo $val[csf("gross_rate")]; ?>"/>
                </td>
                <td><input type="text" name="txtamount[]" id="txtamount_<?= $i; ?>" class="text_boxes_numeric" style="width:80px;" readonly value="<? echo $val[csf("gross_amount")];?>"/></td>
                <td><input type="text" name="txt_service_number[]" id="txt_service_number_<?= $i; ?>" class="text_boxes_numeric" style="width:80px;" value="<? echo $val[csf("service_number")]; ?>" /></td>
                <td><input type="text" name="txtremarks[]" id="txtremarks_<?= $i; ?>" class="text_boxes" style="width:120px;" value="<? echo $val[csf("remarks")];?>" onDblClick="openmypage_remarks(<? echo $i;?>)"/></td>

                <td>
                    <input type="button" name="increase[]" id="increase_<?= $i; ?>" style="width:18px" class="formbuttonplasminus" value="+" onClick="add_dtls_tr(<?= $i; ?>)" />
                    <input type="button" name="decrease[]" id="decrease_<?= $i; ?>" style="width:18px" class="formbuttonplasminus" value="-" onClick="fnc_delet_dtls_tr(<?= $i; ?>);" />
                    <input type="hidden" name="txtTagMaterials[]" id="txtTagMaterials_<?= $i; ?>" />
                </td>
            </tr>
            <?
            $i++;
        }
        ?>
            <tfoot class="tbl_bottom">
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>Total</td>
                    <td style="text-align:center">
                        <input type="text" name="txt_total_amount" id="txt_total_amount" class="text_boxes_numeric" value="<?=$result[0]["WO_AMOUNT"];?>" style="width:80px;" readonly/>
                    </td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right" >Upcharge Remarks:</td>
                    <td colspan="4" align="center"><input type="text" id="txt_up_remarks" name="txt_up_remarks" class="text_boxes" value="<?=$result[0]["UPCHARGE_REMARKS"];?>" style="width:90%;" maxlength="100" placeholder="Maximum 100 Character" disabled/></td>
                    <td>Upcharge</td>
                    <td style="text-align:center">
                        <input type="text" name="txt_upcharge" id="txt_upcharge" class="text_boxes_numeric" value="<?=$result[0]["UP_CHARGE"];?>" style="width:80px;" onKeyUp="calculate_total_amount(2);disable_enable_charge_remarks(1);" />
                    </td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right">Discount Remarks:</td>
                    <td colspan="4" align="center"><input type="text" id="txt_discount_remarks" name="txt_discount_remarks" class="text_boxes" value="<?=$result[0]["DISCOUNT_REMARKS"];?>" style="width:90%;" maxlength="100" placeholder="Maximum 100 Character" /></td>
                    <td>Discount</td>
                    <td style="text-align:center">
                        <input type="text" name="txt_discount" id="txt_discount" class="text_boxes_numeric" value="<?=$result[0]["DISCOUNT"];?>" style="width:80px;" onKeyUp="calculate_total_amount(2);disable_enable_charge_remarks(2);" />
                    </td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>Net Total</td>
                    <td style="text-align:center">
                        <input type="text" name="txt_total_amount_net" id="txt_total_amount_net" class="text_boxes_numeric" value="<?=$result[0]["NET_WO_AMOUNT"];?>" style="width:80px;" readonly/>
                    </td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
            </tfoot>
        <?
    }
    else //requisition
    {
        if($k==1)
        {
            ?>
                <table class="rpt_table" width="1380" cellspacing="0" cellpadding="0" id="tbl_dtls_item" border="1" rules="all">
                    <thead>
                        <tr>
                            <th width="90" class="must_entry_caption"><font color="blue">Service For</font></th>
                            <th width="120" class="must_entry_caption"><font color="blue">Service Details</font></th>
                            <th width="130">Item Description</th>
                            <th width="100">Item Category</th>
                            <th width="100">Item Group</th>
                            <th width="90">Machine Category</th>
                            <th width="90">Machine No</th>
                            <th width="70">Uom</th>
                            <th width="60" class="must_entry_caption"><font color="blue">Qnty</font></th>
                            <th width="60" class="must_entry_caption"><font color="blue">Rate</font></th>
                            <th width="90">Amount</th>
                            <th width="80">Service Number</th>
                            <th width="120">Remarks</th>
                            <th width="65">Matrial List</th>
                            <th >Action</th>
                        </tr>
                    </thead>
                    <tbody id="details_part_list">
            <?
            $k++;
        }
        foreach($result as $val)
        {
            ?>
            <tr class="general" id="tr_<?= $i; ?>">
                <td>
                    <?
                        echo create_drop_down( "cboServiceFor_".$i, 90, $service_for_arr, "", 1, "-- Select --",$val[csf("service_for")], "", 0, "", "", "", "", "", "", "cboServiceFor[]", "cboServiceFor_".$i );
                    ?>
                </td>
                <td align="center">
                    <input name="txtServiceDetails[]" id="txtServiceDetails_<?= $i; ?>" class="text_boxes" style="width:120px" value="<? echo $val[csf("service_details")];?>" readonly/>
                    <input type="hidden" name="hdnServiceId[]" id="hdnServiceId_<?=$i;?>" value="<? echo $val[csf("service_lib_id")];?>"/>
                </td>

                <td align="center">
                    <input type="text" name="txtItemDescription[]" id="txtItemDescription_<?= $i; ?>" class="text_boxes" style="width:130px;"  value="<? echo $prod_arr[$val[csf('item_id')]]['item_description'];?>" <? if($val[csf("service_lib_id")]>0) echo " disabled"; ?> readonly/>
                    <input type="hidden" name="txt_item_id[]" id="txt_item_id_<? echo $i; ?> " value="<? echo $val[csf("item_id")];?>"/>
                    <input type="hidden" name="txt_row_id[]" id="txt_row_id_<? echo $i; ?>" value="<? echo $val[csf("id")];?>" />
                    <input type="hidden" name="txt_req_no_id[]" id="txt_req_no_id_<?= $i; ?>"  value="<? echo $val[csf("requisition_no")];?>"  />
                    <input type="hidden" name="txt_req_dtls_id[]" id="txt_req_dtls_id_<?= $i; ?>"  value="<? echo $val[csf("requisition_dtls_id")];?>"  />
                </td>
                <td align="center">
                    <? echo create_drop_down( "cboItemCategory_".$i, 100, $item_category,"", 1, "-- Select --", $prod_arr[$val[csf('item_id')]]['item_category_id'], "",1,"","","","","","","cboItemCategory[]","cboItemCategory_".$i ); ?>
                </td>
                <td align="center">
                    <? echo create_drop_down( "cboItemGroup_".$i,100,"select id,item_name  from lib_item_group","id,item_name", 1,"Select",$prod_arr[$val[csf('item_id')]]['item_group_id'], "",1,"","","","","","","cboItemGroup[]","cboItemGroup_".$i ); ?>
                </td>
                <td align="center">
                    <? echo create_drop_down( "cboMachineCategory_".$i, 90, $machine_category,"", 1, "--Select--", $val[csf("machine_category_id")], "load_drop_down( 'requires/service_work_order_controller', this.value+'_'+$i, 'load_drop_down_machine_no','machine_no_td_".$i."' );",0, "", "", "", "", "", "", "cboMachineCategory[]", "cboMachineCategory_".$i ); 
                    ?>
                </td>
                <td align="center" id="machine_no_td_<?= $i; ?>">
                    <?
                        echo create_drop_down( "cboMachineNo_".$i, 90, "SELECT id,machine_no from lib_machine_name where category_id='".$val[csf("machine_category_id")]."' and is_deleted=0 and status_active=1 order by machine_no", "id,machine_no", 1, "-- Select --", $val[csf("machine_no")], "", "", "", "", "", "", "", "", "cboMachineNo[]", "cboMachineNo_".$i );
                    ?>
                </td>
                <td align="center">
                    <? echo create_drop_down( "cboUom_".$i, 70, $service_uom_arr,"", 1, "--Select--", $val[csf("uom")], "",1, "", "", "", "", "", "", "cboUom[]", "cboUom_".$i ); ?>
                </td>
                <td>
                    <input type="text" name="txtqnty[]" id="txtqnty_<?= $i; ?>" class="text_boxes_numeric" style="width:60px" onKeyUp="calculate_amount(<?=$i;?>)" value="<? echo $val[csf("supplier_order_quantity")];?>"/>
                    <input type="hidden" name="hiddenqnty[]" id="hiddenqnty_<?= $i; ?>" value="<? echo $val[csf("supplier_order_quantity")];?>"/>
                </td>
                <td>
                    <input type="text" name="txtrate[]" id="txtrate_<?= $i; ?>" class="text_boxes_numeric" style="width:60px;" onKeyUp="calculate_amount(<?=$i;?>)" value="<? echo $val[csf("gross_rate")]; ?>" />
                    <input type="hidden" name="hiddentxtrate[]" id="hiddentxtrate_<?= $i; ?>" value="<? echo $val[csf("gross_rate")]; ?>"/>
                </td>
                <td><input type="text" name="txtamount[]" id="txtamount_<?= $i; ?>" class="text_boxes_numeric" style="width:80px;" readonly value="<? echo $val[csf("gross_amount")];?>"/></td>
                <td><input type="text" name="txt_service_number[]" id="txt_service_number_<?= $i; ?>" class="text_boxes_numeric" style="width:80px;" value="<? echo $val[csf("service_number")]; ?>" /></td>
                <td><input type="text" name="txtremarks[]" id="txtremarks_<?= $i; ?>" class="text_boxes" style="width:120px;" value="<? echo $val[csf("remarks")];?>" onDblClick="openmypage_remarks(<? echo $i;?>)"/></td>

                <td>
                    <a href="#" onClick="fnc_matrial_list(<?=$i;?>)">View</a>
                    <input type="hidden" name="txtTagMaterials[]" id="txtTagMaterials_<?=$i;?>" value="<? echo $val[csf("tag_materials")];?>" />
                </td>
                <td>
                    <input type="button" name="decrease[]" id="decrease_1" style="width:18px" class="formbuttonplasminus" value="-" onClick="fnc_delet_dtls_tr(<?=$i;?>);" />
                </td>
            </tr>
            <?
            $i++;
        }
        ?>
            <tfoot class="tbl_bottom">
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>Total</td>
                    <td style="text-align:center">
                        <input type="text" name="txt_total_amount" id="txt_total_amount" class="text_boxes_numeric" value="<?=$result[0]["WO_AMOUNT"];?>" style="width:80px;" readonly/>
                    </td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right" >Upcharge Remarks:</td>
                    <td colspan="4" align="center"><input type="text" id="txt_up_remarks" name="txt_up_remarks" class="text_boxes" value="<?=$result[0]["UPCHARGE_REMARKS"];?>" style="width:90%;" maxlength="100" placeholder="Maximum 100 Character" disabled/></td>
                    <td>Upcharge</td>
                    <td style="text-align:center">
                        <input type="text" name="txt_upcharge" id="txt_upcharge" class="text_boxes_numeric" value="<?=$result[0]["UP_CHARGE"];?>" style="width:80px;" onKeyUp="calculate_total_amount(2);disable_enable_charge_remarks(1);" />
                    </td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right">Discount Remarks:</td>
                    <td colspan="4" align="center"><input type="text" id="txt_discount_remarks" name="txt_discount_remarks" class="text_boxes" value="<?=$result[0]["DISCOUNT_REMARKS"];?>" style="width:90%;" maxlength="100" placeholder="Maximum 100 Character" disabled/></td>
                    <td>Discount</td>
                    <td style="text-align:center">
                        <input type="text" name="txt_discount" id="txt_discount" class="text_boxes_numeric" value="<?=$result[0]["DISCOUNT"];?>" style="width:80px;" onKeyUp="calculate_total_amount(2);disable_enable_charge_remarks(2);" />
                    </td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>Net Total</td>
                    <td style="text-align:center">
                        <input type="text" name="txt_total_amount_net" id="txt_total_amount_net" class="text_boxes_numeric" value="<?=$result[0]["NET_WO_AMOUNT"];?>" style="width:80px;" readonly/>
                    </td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
            </tfoot>
        <?
    }
    exit();
}

if($action=="save_update_delete")
{
    $process = array( &$_POST );
    extract(check_magic_quote_gpc( $process ));

    $cbo_lc_type = str_replace("'","",$cbo_lc_type);

    if(str_replace("'","",$hidden_delivery_info_dtls)!=''){
        $txt_place_of_delivery=$hidden_delivery_info_dtls;
    }
    if ($operation==0) // Insert Here----------------------------------------------------------
    {
        $con = connect();
        if($db_type==0) { mysql_query("BEGIN"); }
        //table lock here
        //if( check_table_status( 175, 1 )==0 ) { echo "15**0"; disconnect($con);die;}
        $id=return_next_id("id", "wo_non_order_info_mst", 1);
        if($db_type==0){ $insert_date_con="and YEAR(insert_date)=".date('Y',time()).""; }
		else if($db_type==2){ $insert_date_con="and TO_CHAR(insert_date,'YYYY')=".date('Y',time()).""; }

        $new_wo_number=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'SW', date("Y",time()), 5, "select wo_number_prefix,wo_number_prefix_num from wo_non_order_info_mst where company_name=$cbo_company_name $insert_date_con and entry_form = 484 order by id desc", "wo_number_prefix", "wo_number_prefix_num","" ));

        // echo "10**".$new_wo_number[0];die;

        $field_array_mst="id,delivery_place, wo_number_prefix, wo_number_prefix_num, wo_number, company_name, location_id, wo_date, supplier_id, attention, entry_form, pay_mode, source, delivery_date, currency_id, fixed_asset, tenor, asset_no, status_active, is_deleted, inserted_by, insert_date, ready_to_approved, wo_basis_id, requisition_no, attention_to, tag_requisition_no, tag_requisition_id, reference, quot_date, scope_beneficiary, scope_service_provider, payterm_id, wo_amount, up_charge, discount, net_wo_amount,upcharge_remarks,discount_remarks,remarks,division_id,department_id,section_id,lc_type";

        $total_row = str_replace("'","",$total_row);

		$field_array="id, mst_id, item_id, item_category_id, supplier_order_quantity, rate, amount, gross_rate, gross_amount, service_number, remarks, service_for, service_details, service_lib_id, uom, requisition_no, requisition_dtls_id, tag_materials, status_active, is_deleted, inserted_by, insert_date, machine_category_id, machine_no";

        $dtlsid=return_next_id("id", "wo_non_order_info_dtls", 1);
        $dtlsid_check=return_next_id("id", "wo_non_order_info_dtls", 1);
        $data_array=""; $req_no_id_mst='';
        $check_item_id=array();
        for($i=1;$i<=$total_row;$i++)
        {
            if($i>1) $data_array.=",";
            $cboServiceFor      = "cboServiceFor_".$i;
            $txtServiceDetails  = "txtServiceDetails_".$i;
			$hdnServiceId  		= "hdnServiceId_".$i;
            $txtItemDescription = "txtItemDescription_".$i;
            $item_category      = "cboItemCategory_".$i;
            $cboItemGroup       = "cboItemGroup_".$i;
            $txtqnty            = "txtqnty_".$i;
            $txtrate            = "txtrate_".$i;
            $txtamount          = "txtamount_".$i;
            $txt_service_number = "txt_service_number_".$i;
            $txtremarks         = "txtremarks_".$i;
            $item_id            = "txt_item_id_".$i;
            $cboUom             = "cboUom_".$i;
            $req_no_id          = "txt_req_no_id_".$i;
            $req_dtls_id        = "txt_req_dtls_id_".$i;
            $tagMaterials       = "txtTagMaterials_".$i;
            $MachineCategory    = "cboMachineCategory_".$i;
            $MachineNo        = "cboMachineNo_".$i;



            if( str_replace("'","",$$txtqnty) != "" && str_replace("'","",$$txtrate) != "" )
            {
				$perc=(str_replace("'","",$$txtamount)/str_replace("'","",$txt_total_amount))*100;
				$net_amount=($perc*str_replace("'","",$txt_total_amount_net))/100;
				$net_rate=$net_amount/str_replace("'","",$$txtqnty);
				$net_rate=number_format($net_rate,4,'.','');
				$net_amount=number_format($net_amount,4,'.','');

				$data_array.="(".$dtlsid.",".$id.",'".$$item_id."','".$$item_category."','".$$txtqnty."','".$net_rate."','".$net_amount."','".$$txtrate."','".$$txtamount."','".$$txt_service_number."','".$$txtremarks."','".$$cboServiceFor."','".$$txtServiceDetails."','".$$hdnServiceId."','".$$cboUom."','".$$req_no_id."','".$$req_dtls_id."','".$$tagMaterials."',1,0,'".$user_id."','".$pc_date_time."','".$$MachineCategory."','".$$MachineNo."')";

                $dtlsid = $dtlsid + 1;
            }
        }

        $data_array_mst="(".$id.",".$txt_place_of_delivery.",'".$new_wo_number[1]."','".$new_wo_number[2]."','".$new_wo_number[0]."',".$cbo_company_name.",".$cbo_location_name.",".$txt_wo_date.",".$cbo_supplier.",".$txt_attention.",484,".$cbo_pay_mode.",".$cbo_source.",".$txt_delivery_date.",".$cbo_currency.",".$cbo_fixed_asset.",".$txt_tenor.",".$txt_entry_no.",1,0,'".$user_id."','".$pc_date_time."',".$cbo_ready_to_approved.",".$cbo_wo_basis.",".$txt_req_numbers_id.",".$txt_attention_to.",".$txt_tag_req.",".$txt_tag_req_id.",".$txt_quot_ref.",".$txt_quot_date.",".$txt_scope_beneficiary.",".$txt_scope_service_provider.",".$cbo_pay_term.",".$txt_total_amount.",".$txt_upcharge.",".$txt_discount.",".$txt_total_amount_net.",".$txt_up_remarks.",".$txt_discount_remarks.",".$txt_remarks.",".$cbo_division_name.",".$cbo_department_name.",".$cbo_section_name.",".$cbo_lc_type.")";
       	
		//echo "10** insert into wo_non_order_info_dtls ($field_array) values $data_array";oci_rollback($con); disconnect($con);die;
        //echo "10** insert into wo_non_order_info_mst ($field_array_mst) values $data_array_mst";die;
        $rID=sql_insert("wo_non_order_info_mst",$field_array_mst,$data_array_mst,1);
        $dtlsrID=sql_insert("wo_non_order_info_dtls",$field_array,$data_array,1);
        //-----------------------------------------------wo_non_order_info_dtls table insert END here-----------------------------------//
        //echo "5**".$rID."**".$dtlsrID; oci_rollback($con); disconnect($con);die;

        if($db_type==0)
        {
            if($rID && $dtlsrID)
            {
                mysql_query("COMMIT");
                echo "0**".$new_wo_number[0]."**".$id;
            }
            else
            {
                mysql_query("ROLLBACK");
                echo "10**";
            }
        }
        if($db_type==2 || $db_type==1 )
        {
            if($rID && $dtlsrID)
            {
                oci_commit($con);
                echo "0**".$new_wo_number[0]."**".$id;
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
    else if ($operation==1) // Update Here----------------------------------------------------------
    {
        $update_id=str_replace("'","",$update_id);

        $total_row = str_replace("'","",$total_row);
        for($i=1;$i<=$total_row;$i++)
        {
            $txtqnty            = "txtqnty_".$i;
            $txtamount          = "txtamount_".$i;
            $dtls_ID            = "txt_row_id_".$i;
            $dtls_ID=str_replace("'",'',$$dtls_ID);
            $dtlsIdArr[$dtls_ID]['id']=$dtls_ID;
            $dtlsIdArr[$dtls_ID]['amount']=str_replace("'",'',$$txtamount);
            $dtlsIdArr[$dtls_ID]['qnty']=str_replace("'",'',$$txtqnty);
        }
        $wo_result=sql_select("SELECT a.bill_no from subcon_outbound_bill_mst a,subcon_outbound_bill_dtls b where a.id=b.mst_id and a.entry_form =483 and a.status_active=1 and b.status_active=1 and b.wo_non_order_info_dtls_id in  (select c.id from wo_non_order_info_dtls c where c.mst_id = $update_id) group by a.bill_no,b.wo_non_order_info_dtls_id ");
        $wo_data= array();
        if(count($wo_result))
        {
            echo "20**Bill (".$wo_result[0][csf('bill_no')].") Found against this Work Order, Not Update/ Delete Allow";
            die;
        }

        $con = connect();
        if($db_type==0) { mysql_query("BEGIN"); }



        // echo "111**".$txt_wo_number; die;

        $txt_wo_number=str_replace("'", "", $txt_wo_number);

        $wo_approved=return_field_value("is_approved","wo_non_order_info_mst","id=$update_id","is_approved");
		if($wo_approved==1 || $wo_approved==3)
		{
			echo "50**WO Approved, Update or Delete Not Allow";disconnect($con);oci_rollback($con);die;
		}

        $data_array_insert="";
		$field_array_insert="id, mst_id, item_id, item_category_id, supplier_order_quantity, rate, amount, gross_rate, gross_amount, service_number, remarks, service_for, service_details, service_lib_id, uom, requisition_no, requisition_dtls_id, tag_materials, status_active, is_deleted, inserted_by, insert_date, machine_category_id, machine_no";

 		$field_array="item_id*item_category_id*supplier_order_quantity*rate*amount*gross_rate*gross_amount*service_number*remarks*service_for*service_details*service_lib_id*uom*requisition_no*requisition_dtls_id*tag_materials*status_active*is_deleted*updated_by*update_date*machine_category_id*machine_no";

        $dtlsid=return_next_id("id", "wo_non_order_info_dtls", 1);
        //$dtlsid_check=return_next_id("id", "wo_non_order_info_dtls", 1);
        $data_array=array();
        for($i=1;$i<=$total_row;$i++)
        {
            $cboServiceFor      = "cboServiceFor_".$i;
            $txtServiceDetails  = "txtServiceDetails_".$i;
			$hdnServiceId  		= "hdnServiceId_".$i;
            $txtItemDescription = "txtItemDescription_".$i;
            $item_category      = "cboItemCategory_".$i;
            $cboItemGroup       = "cboItemGroup_".$i;
            $txtqnty            = "txtqnty_".$i;
            $txtrate            = "txtrate_".$i;
            $txtamount          = "txtamount_".$i;
            $txt_service_number = "txt_service_number_".$i;
            $txtremarks         = "txtremarks_".$i;
            $item_id            = "txt_item_id_".$i;
            $cboUom             = "cboUom_".$i;
            $req_no_id          = "txt_req_no_id_".$i;
            $req_dtls_id        = "txt_req_dtls_id_".$i;
            $tagMaterials       = "txtTagMaterials_".$i;
            $MachineCategory    = "cboMachineCategory_".$i;
            $MachineNo        = "cboMachineNo_".$i;

            $dtls_ID            = "txt_row_id_".$i;

			$perc=(str_replace("'","",$$txtamount)/str_replace("'","",$txt_total_amount))*100;
			$net_amount=($perc*str_replace("'","",$txt_total_amount_net))/100;
			$net_rate=$net_amount/str_replace("'","",$$txtqnty);
			$net_rate=number_format($net_rate,4,'.','');
			$net_amount=number_format($net_amount,4,'.','');

            //$dtlsID_up          = str_replace("'","",$$dtls_ID);
            //echo $dtls_ID.'system';
            if(str_replace("'",'',$$dtls_ID)=="") // new insert
            {
                if( str_replace("'","",$$txtqnty) != "" && str_replace("'","",$$txtrate) != "")
                {
                    if($data_array_insert!="") $data_array_insert .=",";
                    $data_array_insert.="(".$dtlsid.",".$update_id.",'".$$item_id."','".$$item_category."','".$$txtqnty."','".$net_rate."','".$net_amount."','".$$txtrate."','".$$txtamount."','".$$txt_service_number."','".$$txtremarks."','".$$cboServiceFor."','".$$txtServiceDetails."','".$$hdnServiceId."','".$$cboUom."','".$$req_no_id."','".$$req_dtls_id."','".$$tagMaterials."',1,0,'".$user_id."','".$pc_date_time."','".$$MachineCategory."','".$$MachineNo."')";

                    $dtlsid=$dtlsid+1;
                }
            }
            else  // Update
            {
                //$update_ID[]=$dtlsID_up;
				if( str_replace("'","",$$txtqnty) != "" && str_replace("'","",$$txtrate) != "")
                {
					$deleteId_array[]=str_replace("'",'',$$dtls_ID);
					$updateId_array[]=str_replace("'",'',$$dtls_ID);
					$data_array[str_replace("'",'',$$dtls_ID)]=explode("*",("'".$$item_id."'*'".$$item_category."'*'".$$txtqnty."'*'".$net_rate."'*'".$net_amount."'*'".$$txtrate."'*'".$$txtamount."'*'".$$txt_service_number."'*'".$$txtremarks."'*'".$$cboServiceFor."'*'".$$txtServiceDetails."'*'".$$hdnServiceId."'*'".$$cboUom."'*'".$$req_no_id."'*'".$$req_dtls_id."'*'".$$tagMaterials."'*1*0*'".$user_id."'*'".$pc_date_time."'*'".$$MachineCategory."'*'".$$MachineNo."'"));
				}

            }

        }

        if($update_id>0)
        {
            $mst_id = return_field_value("id","wo_non_order_info_mst","wo_number=$txt_wo_number");

            $field_array_mst="company_name*delivery_place*location_id*wo_date*supplier_id*attention*pay_mode*source*delivery_date*currency_id*fixed_asset*tenor*asset_no*ready_to_approved*wo_basis_id*requisition_no*attention_to*tag_requisition_no*tag_requisition_id*reference*quot_date*scope_beneficiary*scope_service_provider*updated_by*update_date*payterm_id*wo_amount*up_charge*discount*net_wo_amount*upcharge_remarks*discount_remarks*remarks*division_id*department_id*section_id*lc_type";
            $data_array_mst="".$cbo_company_name."*".$txt_place_of_delivery."*".$cbo_location_name."*".$txt_wo_date."*".$cbo_supplier."*".$txt_attention."*".$cbo_pay_mode."*".$cbo_source."*".$txt_delivery_date."*".$cbo_currency."*".$cbo_fixed_asset."*".$txt_tenor."*".$txt_entry_no."*".$cbo_ready_to_approved."*".$cbo_wo_basis."*".$txt_req_numbers_id."*".$txt_attention_to."*".$txt_tag_req."*".$txt_tag_req_id."*".$txt_quot_ref."*".$txt_quot_date."*".$txt_scope_beneficiary."*".$txt_scope_service_provider."*".$user_id."*'".$pc_date_time."'*".$cbo_pay_term."*".$txt_total_amount."*".$txt_upcharge."*".$txt_discount."*".$txt_total_amount_net."*".$txt_up_remarks."*".$txt_discount_remarks."*".$txt_remarks."*".$cbo_division_name."*".$cbo_department_name."*".$cbo_section_name."*".$cbo_lc_type."";
        }
        //echo "10**";die;
        $rID=$deleterID=$dtlsrID=$dtlsUpdaterID=true;

        // Delete Knit Part
        $dtlsUpdate_array = array();
        $sql_dtls="select b.id from wo_non_order_info_mst a, wo_non_order_info_dtls b where a.id=b.mst_id and b.mst_id=$update_id and b.status_active=1 and b.is_deleted=0 and a.entry_form=484 and a.status_active=1 and a.is_deleted=0";
        $nameArray=sql_select($sql_dtls);

        foreach($nameArray as $row)
        {
            $dtlsUpdate_array[]=$row[csf('id')];
        }

        if(implode(',',$deleteId_array) != "")
        {
            $distance_delete_id = array_diff($dtlsUpdate_array, $deleteId_array);
        }
        else
        {
            $distance_delete_id = $dtlsUpdate_array;
        }

        $field_array_dtls_del="updated_by*update_date*status_active*is_deleted";
        $data_array_dtls_del="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
        if(implode(',',$distance_delete_id) != "")
        {
            foreach($distance_delete_id as $id_val)
            {
                $deleterID=sql_delete("wo_non_order_info_dtls",$field_array_dtls_del,$data_array_dtls_del,"id","$id_val",1);
            }
        }
        //if ($delrID) $flag=1;

        // echo "1000**"; print_r($data_array); die;

        if($update_id>0)
        {
            $rID=sql_update("wo_non_order_info_mst",$field_array_mst,$data_array_mst,"id",$update_id,1);
        }

        if($data_array_insert!="")
        {
            //echo "10** insert into wo_non_order_info_dtls ($field_array_insert) values $data_array_insert";die;
            $dtlsrID=sql_insert("wo_non_order_info_dtls",$field_array_insert,$data_array_insert,1);
        }
        if(count($updateId_array)>0)
        {
            // bulk_update_sql_statement( $table, $id_column, $update_column, $data_values, $id_count )
            $dtlsUpdaterID=execute_query(bulk_update_sql_statement("wo_non_order_info_dtls","id",$field_array,$data_array,$updateId_array));
        }

        //echo "10**".$rID."**".$deleterID."**".$dtlsrID."**".$dtlsUpdaterID;oci_rollback($con); disconnect($con);die;

        if($db_type==0)
        {
            if($rID && $deleterID && $dtlsrID && $dtlsUpdaterID)
            {
                mysql_query("COMMIT");
                echo "1**".str_replace("'","",$txt_wo_number)."**".$update_id;
            }
            else
            {
                mysql_query("ROLLBACK");
                echo "10**";
            }
        }
        if($db_type==2 || $db_type==1 )
        {
            if($rID && $deleterID && $dtlsrID && $dtlsUpdaterID)
            {
                oci_commit($con);
                echo "1**".str_replace("'","",$txt_wo_number)."**".$update_id;
            }
            else
            {
                oci_rollback($con);
                echo "10**";
            }
            //echo "1**".$txt_wo_number."**".$update_check."**".$dtlsid_check;
        }
        disconnect($con);
        die;
    }
    else if ($operation==2) // Delete Here--------------------
    {
        //$sql=sql_select("SELECT a.wo_number from wo_non_order_info_mst a,wo_non_order_info_dtls b where a.id=b.mst_id and a.entry_form =484 and a.status_active=1 and b.status_active=1 and b.requisition_dtls_id in (select c.id from inv_purchase_requisition_dtls c where c.mst_id = $update_id)");
		$txt_wo_number = str_replace("'", "", $txt_wo_number);
		$sql=sql_select("SELECT WORK_ORDER_NO from COM_PI_ITEM_DETAILS where status_active=1 and ITEM_CATEGORY_ID = 114 and WORK_ORDER_NO='$txt_wo_number'");

        if(count($sql)>0)
        {
            if($db_type==0)
            {
                mysql_query("ROLLBACK");
            }
            else if($db_type==2 || $db_type==1 )
            {
                oci_rollback($con);
            }
            echo "11**".$sql[0]['WORK_ORDER_NO'];
            disconnect($con);
            die;
        }
        $con = connect();
        if($db_type==0) { mysql_query("BEGIN"); }

        $wo_approved=return_field_value("is_approved","wo_non_order_info_mst","id=$update_id","is_approved");
		if($wo_approved==1 || $wo_approved==3)
		{
			echo "50**WO Approved, Update or Delete Not Allow";disconnect($con);oci_rollback($con);die;
		}

        $txt_wo_number = str_replace("'", "", $txt_wo_number);
        $bill_check = return_field_value("service_wo_num as service_wo_num","subcon_outbound_bill_mst","service_wo_num='$txt_wo_number' and company_id=$cbo_company_name and entry_form=483 and status_active=1 and is_deleted=0","service_wo_num");

        if (count($bill_check) > 0){
            echo "20**Bill Found against this Work Order, Not Update/ Delete Allow";
            disconnect($con);die;
        }

        $mst_sql=sql_select("select id, pay_mode from wo_non_order_info_mst where status_active=1 and entry_form=484 and wo_number = '$txt_wo_number'");
        $mst_id = $mst_sql[0][csf("id")];

        $rID = sql_update("wo_non_order_info_mst",'status_active*is_deleted','0*1',"id",$mst_id,0);
        $dtlsrID = sql_update("wo_non_order_info_dtls",'status_active*is_deleted','0*1',"mst_id",$mst_id,1);
        if($db_type==0)
        {
            if($rID && $dtlsrID)
            {
                mysql_query("COMMIT");
                echo "2**".str_replace("'","",$txt_wo_number);
            }
            else
            {
                mysql_query("ROLLBACK");
                echo "10**";
            }
        }
        //oci_commit($con); oci_rollback($con);
        if($db_type==2 || $db_type==1 )
        {
            if($rID && $dtlsrID)
            {
                oci_commit($con);
                echo "2**".str_replace("'","",$rID);
            }
            else
            {
                oci_rollback($con);
                echo "10**";
            }
            //echo "2**".$rID;
        }
        disconnect($con);
        die;
    }
}

if($action=="wo_popup")
{
    extract($_REQUEST);
    echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
    ?>

    <script>
    function js_set_value(wo_number)
    {
        $("#hidden_wo_number").val(wo_number);
        //alert(wo_number);return;
        parent.emailwindow.hide();
    }

    </script>
    </head>

    <body>
    <div align="center" style="width:100%;" >
        <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="800" cellspacing="0" cellpadding="0" class="rpt_table" align="center">
            <tr>
                <td align="center" width="100%">
                    <table  cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
                         <thead>
                            <th width="160">Item Category</th>
                            <th width="160" align="center">WO Number</th>
                            <th width="200">WO Date Range</th>
                            <th width="80"><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" /></th>
                        </thead>
                        <tr>
                            <td width="160">
                            <?
                                echo create_drop_down( "cboitem_category", 160, "select category_id, short_name from  lib_item_category_list where status_active=1 and is_deleted=0 and category_type=1 and category_id not in(4,11) order by short_name","category_id,short_name", 1, "-- Select --", "", "","","4,11");
                            ?>
                            </td>
                            <td width="160" align="center">
                                <input type="text" style="width:140px" class="text_boxes"  name="txt_wo" id="txt_wo" />
                            </td>
                            <td align="center">
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px"> To
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
                            </td>
                            <td align="center">
                                <input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cboitem_category').value+'_'+document.getElementById('txt_wo').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>, 'create_wo_search_list_view', 'search_div', 'service_work_order_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td  align="center" height="40" valign="middle">
                    <? echo load_month_buttons(1);  ?>
                    <input type="hidden" id="hidden_wo_number" name="hidden_wo_number" value="" />
                </td>
            </tr>
            <tr>
            <td align="center" valign="top" id="search_div"></td>
            </tr>
        </table>
        </form>
    </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
}

if($action=="create_wo_search_list_view")
{

    extract($_REQUEST);
    $ex_data = explode("_",$data);
    $itemCategory = $ex_data[0];
    $txt_wo_number = $ex_data[1];
    $txt_date_from = $ex_data[2];
    $txt_date_to = $ex_data[3];
    $company = $ex_data[4];

    $sql_cond="";
    if(trim($itemCategory)) $sql_cond .= " and b.item_category_id='$itemCategory'";
    if ($txt_wo_number!="") $sql_cond .= " and a.wo_number like '%".trim($txt_wo_number)."'";

    if ($txt_date_from!="" &&  $txt_date_to!="")
    {
        if($db_type==0)
        {
            $sql_cond .= " and a.wo_date between '".change_date_format($txt_date_from, "yyyy-mm-dd", "-")."' and '".change_date_format($txt_date_to, "yyyy-mm-dd", "-")."'";
        }
        else
        {
            $sql_cond .= " and a.wo_date between '".change_date_format($txt_date_from,'','',1)."' and '".change_date_format($txt_date_to,'','',1)."'";
        }
    }

    if (trim($company) !="") $sql_cond .= " and a.company_name='$company'";

    $sql = " select a.id, a.wo_number_prefix_num, a.requisition_no, a.wo_number, a.company_name, a.wo_date, a.supplier_id, a.attention, a.currency_id, a.delivery_date, a.source, a.pay_mode from wo_non_order_info_mst a, wo_non_order_info_dtls b where a.id=b.mst_id and a.entry_form=484 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sql_cond group by a.id, a.wo_number_prefix_num, a.requisition_no, a.wo_number, a.company_name, a.wo_date, a.supplier_id, a.attention, a.currency_id, a.delivery_date, a.source, a.pay_mode order by a.id desc";
    //echo $sql;die;
    $result = sql_select($sql);
    $company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
    $supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');

    $requisition_data=sql_select("select id, requ_no from inv_purchase_requisition_mst where company_id='$company' and status_active=1 and is_deleted=0 and entry_form=526");
    $requisition_arr=array();
    foreach($requisition_data as $row){
        $requisition_arr[$row[csf("id")]]=$row[csf("requ_no")];
    }

    //$arr=array(0=>$company_arr,3=>$pay_mode,4=>$supplier_arr,5=>$source);
    ?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="900" class="rpt_table" align="left">
        <thead>
            <th width="40">SL</th>
            <th width="130">Company</th>
            <th width="120">WO Number</th>
            <th width="120">Requisition No</th>
            <th width="80">WO Date</th>
            <th width="80">Pay Mode</th>
            <th width="130">Supplier</th>
            <th width="100">Source</th>
        </thead>
    </table>
    <div style="width:920px; max-height:220px; overflow-y:scroll">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="900" class="rpt_table" id="tbl_list_search">
        <?
            $i=1;
            foreach ($result as $row)
            {
                if($i%2==0) $bgcolor="#E9F3FF";
                else $bgcolor="#FFFFFF";
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="tr_<? echo $i;?>" onClick="js_set_value('<? echo $row[csf('wo_number')]."_".$row[csf('id')]; ?>')">
                    <td width="40"><? echo $i; ?></td>
                    <td width="130"><p><? echo $company_arr[$row[csf('company_name')]];?></p></td>
                    <td width="120"><p><? echo $row[csf('wo_number')]; ?></p></td>
                    <td width="120"><p><? echo $requisition_arr[$row[csf('requisition_no')]]; ?></p></td>
                    <td width="80" align="center"><p><? echo change_date_format($row[csf('wo_date')]); ?>&nbsp;</p></td>
                    <td width="80"><p><? echo $pay_mode[$row[csf('pay_mode')]]; ?>&nbsp;</p></td>
                    <td width="130"><p><? echo $supplier_arr[$row[csf('supplier_id')]]; ?>&nbsp;</p></td>
                    <td width="100"><p><? echo $source[$row[csf('source')]]; ?></p></td>
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

if($action=="populate_data_from_search_popup")
{

    $sql = "SELECT id, company_name,delivery_place, wo_date, supplier_id, attention, currency_id, delivery_date, source, pay_mode, ready_to_approved, is_approved, location_id, fixed_asset, tenor, asset_no, wo_basis_id, requisition_no, attention_to, tag_requisition_no, tag_requisition_id, reference, quot_date, scope_beneficiary, scope_service_provider, payterm_id, remarks,division_id,department_id,section_id,lc_type from wo_non_order_info_mst where id='$data'";
   // echo $sql;die;
    $result = sql_select($sql);
    foreach($result as $resultRow)
    {
        echo "$('#cbo_company_name').val('".$resultRow[csf("company_name")]."');\n";
        echo "document.getElementById('cbo_division_name').value = '".$resultRow[csf("division_id")]."';\n";
	     echo "load_drop_down( 'requires/service_work_order_controller', document.getElementById('cbo_division_name').value, 'load_drop_down_department','department_td');\n";
	    echo "document.getElementById('cbo_department_name').value = '".$resultRow[csf("department_id")]."';\n";
	    echo "load_drop_down( 'requires/service_work_order_controller', document.getElementById('cbo_department_name').value, 'load_drop_down_section','section_td');\n";
	    echo "document.getElementById('cbo_section_name').value	= '".$resultRow[csf("section_id")]."';\n";
        echo "$('#cbo_location_name').val('".$resultRow[csf("location_id")]."');\n";
        echo "$('#cbo_lc_type').val('".$resultRow[csf("lc_type")]."');\n";


        $hdn_delivery=explode('__',$resultRow[csf("delivery_place")]);

		echo "$('#txt_place_of_delivery').val('".$hdn_delivery[0]."');\n";
        if(count($hdn_delivery)>1)
        {
            echo "$('#hidden_delivery_info_dtls').val('".$resultRow[csf("delivery_place")]."');\n";
        }
        //echo "$('#txt_place_of_delivery').val('".$resultRow[csf("delivery_place")]."');\n";
        //echo "$('#cbo_company_name').attr('disabled',true);\n";
        //echo "$('#cbo_item_category').val('".$resultRow[csf("item_category")]."');\n";
        //echo "$('#cbo_deal_merchant').val('".$resultRow[csf("dealing_marchant")]."');\n";
        //echo "$('#cbo_item_category').attr('disabled',true);\n";

        echo "$('#txt_wo_date').val('".change_date_format($resultRow[csf("wo_date")])."');\n";
        echo "$('#cbo_currency').val('".$resultRow[csf("currency_id")]."');\n";
        echo "check_exchange_rate();\n";
        echo "$('#cbo_wo_basis').val('".$resultRow[csf("wo_basis_id")]."');\n";
        echo  "fn_disable_enable('".$resultRow[csf("wo_basis_id")]."');\n";
        // echo "$('#cbo_wo_basis').attr('disabled',true);\n";
        echo "$('#cbo_pay_mode').val('".$resultRow[csf("pay_mode")]."');\n";

        //echo "fnc_load_supplier('".$resultRow[csf("pay_mode")]."');\n";
        echo "$('#cbo_supplier').val('".$resultRow[csf("supplier_id")]."');\n";

        echo "$('#cbo_source').val('".$resultRow[csf("source")]."');\n";
        echo "$('#txt_delivery_date').val('".change_date_format($resultRow[csf("delivery_date")])."');\n";
        echo "$('#txt_attention').val('".$resultRow[csf("attention")]."');\n";
        //echo "$('#txt_req_numbers_id').val('".$resultRow[csf("requisition_no")]."');\n";

        //$hdn_delivery=explode('__',$resultRow[csf("delivery_place")]);

        //echo "$('#txt_delivery_place').val('".$hdn_delivery[0]."');\n";

        if ($resultRow[csf("is_approved")] == 1) echo "$('#approved').text('Approved');\n";
		else if($resultRow[csf("is_approved")] == 3) echo "$('#approved').text('Partial Approved');\n";
		else echo "$('#approved').text('');\n";

        echo "$('#cbo_ready_to_approved').val('".$resultRow[csf("ready_to_approved")]."');\n";
        echo "$('#cbo_fixed_asset').val('".$resultRow[csf("fixed_asset")]."');\n";
        echo "$('#txt_tenor').val('".$resultRow[csf("tenor")]."');\n";
        echo "$('#txt_entry_no').val('".$resultRow[csf("asset_no")]."');\n";
        echo "$('#txt_remarks').val('".$resultRow[csf("remarks")]."');\n";
        $requNumber="";$i=0;
		if($resultRow[csf("wo_basis_id")]==1) // requisition basis
		{
			$sqlResult = sql_select("select requ_no from inv_purchase_requisition_mst where id in (".$resultRow[csf("requisition_no")].")");
			//print_r($sqlResult);
			foreach($sqlResult as $res)
			{
				if( $i>0 ) $requNumber .= ",";
				$requNumber .= $res[csf("requ_no")];
				$i++;
			}
		}

        echo "$('#txt_req_numbers').val('".$requNumber."');\n";
        echo "$('#txt_req_numbers_id').val('".$resultRow[csf("requisition_no")]."');\n";
        echo "$('#cbo_pay_term').val('".$resultRow[csf("payterm_id")]."');\n";
        echo "$('#txt_attention_to').val('".$resultRow[csf("attention_to")]."');\n";
        echo "$('#txt_tag_req').val('".$resultRow[csf("tag_requisition_no")]."');\n";
        echo "$('#txt_tag_req_id').val('".$resultRow[csf("tag_requisition_id")]."');\n";
        echo "$('#txt_quot_date').val('".change_date_format($resultRow[csf("quot_date")])."');\n";
        echo "$('#txt_scope_beneficiary').val('".$resultRow[csf("scope_beneficiary")]."');\n";
        echo "$('#txt_scope_service_provider').val('".$resultRow[csf("scope_service_provider")]."');\n";
        echo "is_text_field();\n";
        //echo "$('#cbo_inco_term').val('".$resultRow[csf("inco_term_id")]."');\n";
        //echo "$('#txt_tenor').val('".$resultRow[csf("tenor")]."');\n";
       // echo "$('#txt_contact').val('".$resultRow[csf("contact")]."');\n";
        //echo "$('#cbo_payterm_id').val('".$resultRow[csf("payterm_id")]."');\n";
       // echo "$('#cbo_wo_type').val('".$resultRow[csf("wo_type")]."');\n";
       // echo "$('#txt_remarks_mst').val('".$resultRow[csf("remarks")]."');\n";
        echo "$('#txt_quot_ref').val('".$resultRow[csf("reference")]."');\n";

    }
    exit();
}

if ($action == "search_asset_entry")
{
    echo load_html_head_contents("Popup Info", "../../../", 1, 1, $unicode, 1);
    extract($_REQUEST);
    $new_conn=integration_params(3);
    $asset_type = return_library_array("select id, asset_type, asset_type_rename from lib_fam_asset_type where status_active =1 and is_deleted=0 order by id", "id", "asset_type_rename",$new_conn);
    //echo "<pre>";print_r($asset_type);die;
    ?>
    <script>
        var companyName= <? echo $cbo_company_name ?>;
        function js_set_value(id)
        {
            document.getElementById('hidden_system_number').value = id;
            parent.emailwindow.hide();
        }

    </script>
    </head>
    <body>
        <div align="center" style="width:100%;" >
            <form name="searchorderfrm_2"  id="searchorderfrm_2" autocomplete="off">
                <table width="1070" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
                    <thead>
                        <tr>
                            <th width="170" class="must_entry_caption">Company Name</th>
                            <th width="170">Location</th>
                            <th width="110">Asset Type</th>
                            <th width="170">Category</th>
                            <th width="80">Entry No</th>
                            <th width="80">Asset No</th>
                            <th width="210" align="center" >Date Range</th>
                            <th width="80"><input type="reset" name="re_button" id="re_button" value="Reset" style="width:70px" class="formbutton"  /></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <?
                                echo create_drop_down("cbo_company_name", 170, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name", "id,company_name", 1, "-- Select Company --", $cbo_company_name, "load_drop_down( 'general_item_issue_controller', this.value, 'load_drop_down_location_asetpopup', 'src_location_td');", "", "", "", "", "", "", "", "");
                                ?>
                            </td>
                            <td id="src_location_td">
                                <?
                                echo create_drop_down("cbo_location", 170, $blank_array, "", 1, "-- Select Location --", $selected, "", "", "", "", "", "", "", "", "");
                                ?>
                            </td>
                            <td>
                                <?
                                echo create_drop_down("cbo_aseet_type", 110, $asset_type, "", 1, "--- Select ---", $selected, "load_drop_down( 'general_item_issue_controller', this.value, 'load_drop_down_category', 'src_category_td' );", "", "", "", "", "", "", "", "");
                                ?>
                            </td>
                            <td id="src_category_td">
                                <?
                                echo create_drop_down("cbo_category", 170, $blank_array, "", 1, "--- Select ---", $selected, "", "", "", "", "", "", "", "", "");
                                ?>
                            </td>
                            <td>
                                <input type="text" name="txt_entry_no" id="txt_entry_no" style="width:80px;" class="text_boxes">
                            </td>
                            <td>
                                <input type="text" name="asset_number" id="asset_number" style="width:80px;" class="text_boxes">
                            </td>
                            <td align="">
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:66px" placeholder="From" readonly/>-
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:66px" placeholder="To" readonly/>
                            </td>

                            <td align="center">
                                <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view(document.getElementById('asset_number').value + '_' + document.getElementById('cbo_company_name').value + '_' + document.getElementById('cbo_location').value + '_' + document.getElementById('cbo_aseet_type').value + '_' + document.getElementById('cbo_category').value + '_' + document.getElementById('txt_date_from').value + '_' + document.getElementById('txt_date_to').value + '_' + document.getElementById('txt_entry_no').value, 'show_searh_active_listview', 'searh_list_view', 'service_work_order_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" />
                            </td>
                        </tr>
                        <tr>
                            <td align="center" height="40" valign="middle" colspan="8">
                                <?php echo load_month_buttons(1); ?>
                                <input type="hidden" id="hidden_system_number" value="" />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
            <div align="center" valign="top" id="searh_list_view"> </div>
        </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    <script>
        //if(companyName>0)  load_drop_down( 'asset_acquisition_unite_price_change_controller',companyName, 'load_drop_down_location', 'src_location_td');
    </script>
    </html>
    <?php
}


if ($action == "show_searh_active_listview")
{
    $ex_data = explode("_", $data);
    $new_conn=integration_params(3);
    //echo $new_conn.test;die;
    $company_location = return_library_array("select id,location_name from lib_location where status_active =1 and is_deleted=0", "id", "location_name");
    $store_library      = return_library_array("select id,store_name from lib_store_location where status_active=1 and is_deleted=0 and company_id='$ex_data[1]'", "id", "store_name");
    $asset_category_result=sql_select("SELECT ID, ASSET_TYPE_ID, ASSET_CATEGORY_NAME, STATUS_ACTIVE FROM LIB_FAM_ASSET_CATEGORY_TYPE WHERE IS_DELETED=0 ORDER BY ASSET_TYPE_ID,ID",'',$new_conn);
    //echo "<pre>";print_r($asset_category_result);die;
    $fams_asset_category_arr=array();
    foreach($asset_category_result as $row){
        $fams_asset_category_arr[$row['ASSET_TYPE_ID']][$row['ID']]=$row['ASSET_CATEGORY_NAME'];
    }
    unset($asset_category_result);

    if ( trim($ex_data[0]) == 0)
        $asset_number = "";
    else
        $asset_number = " and c.asset_no LIKE '%" . trim($ex_data[0]) . "'";

    if ($ex_data[1] == 0)
        $company_id = "";
    else
        $company_id = " and a.company_id='" . $ex_data[1] . "'";

    if ($ex_data[2] == 0)
        $location = "";
    else
        $location = " and a.location='" . $ex_data[2] . "'";

    if ($ex_data[3] == 0)
        $aseet_type = "";
    else
        $aseet_type = " and a.asset_type='" . $ex_data[3] . "'";

    if ($ex_data[4] == 0)
        $category = "";
    else
        $category = " and a.asset_category='" . $ex_data[4] . "'";

    $txt_date_from = $ex_data[5];
    $txt_date_to = $ex_data[6];

    if ( trim($ex_data[7]," ") == "")
        $entry_no_cond = "";
    else
        $entry_no_cond = " and a.entry_no LIKE '%" . trim($ex_data[7]) . "'";




    if ($ex_data[1] == 0) { echo "Please Company first"; die; }

    if ($db_type == 0)
    {//for mysql
        if ($txt_date_from != "" || $txt_date_to != "") {
            $tran_date = " and a.purchase_date between '" . change_date_format($txt_date_from, 'yyyy-mm-dd') . "' and '" . change_date_format($txt_date_to, 'yyyy-mm-dd') . "'";
        }
        $sql = "SELECT  a.id, a.entry_no, c.asset_no, a.location, a.asset_type, a.asset_category, a.store, a.purchase_date, a.qty  FROM fam_acquisition_mst a, fam_acquisition_sl_dtls c  WHERE a.id=c.mst_id AND a.status_active=1 AND a.is_deleted=0 AND c.status_active=1 AND c.is_deleted=0 $category $aseet_type $location $company_id $asset_number $entry_no_cond $tran_date order by a.id,c.asset_no";
    }

    if ($db_type == 2) {//for oracal
        if ($txt_date_from != "" && $txt_date_to != "") {
            $tran_date = " and a.purchase_date between '" . change_date_format($txt_date_from, '', '', 1) . "' and '" . change_date_format($txt_date_to, '', '', 1) . "'";
        }
        $sql = "SELECT  a.id, a.entry_no, c.asset_no, a.location, a.asset_type, a.asset_category, a.store, a.purchase_date, a.qty  FROM fam_acquisition_mst a, fam_acquisition_sl_dtls c  WHERE a.id=c.mst_id AND a.status_active=1 AND a.is_deleted=0  AND c.status_active=1 AND c.is_deleted=0 $category $aseet_type $location $company_id $asset_number $entry_no_cond $tran_date order by a.id,c.asset_no";
    }
    $prev_asset_no=return_library_array("select raw_issue_challan from inv_transaction where status_active=1 and transaction_type=2 and item_category in(".implode(",",array_flip($general_item_category)).") and raw_issue_challan is not null","raw_issue_challan","raw_issue_challan");
    $result = sql_select($sql,'',$new_conn);
    //echo "<pre>";print_r($result);die;
    ?>
    <table class="rpt_table" rules="all" width="978" cellspacing="0" cellpadding="0" border="0">
        <thead>
            <tr>
                <th width="50">SL No</th>
                <th width="150">Entry No</th>
                <th width="130">Asset No</th>
                <th width="150">Location</th>
                <th width="90">Type</th>
                <th width="90">Category</th>
                <th width="120">Store</th>
                <th width="90">Purchase Date</th>
                <th>Qty</th>
            </tr>
        </thead>
    </table>
    <div style="max-height:300px; width:976px; overflow-y:scroll">
    <table class="rpt_table" id="list_view" rules="all" width="958" height="" cellspacing="0" cellpadding="0" border="0">
    <tbody>
        <?
        foreach($result as $row)
        {
            if($prev_asset_no[$row[csf('entry_no')]]=="")
            {
                $asset_category = $fams_asset_category_arr[$row[csf('asset_type')]];
                $i++;
                $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
                ?>
                <tr onClick="js_set_value('<? echo $row[csf('entry_no')];?>')" style="cursor:pointer" id="tr_<? echo $i; ?>" height="20" bgcolor="<? echo $bgcolor;?>">
                    <td width="50"><? echo $i; ?></td>
                    <td width="150" align="left"><p><? echo $row[csf('entry_no')];?></p></td>
                    <td width="130" align="left"><p><? echo $row[csf('asset_no')];?></p></td>
                    <td width="150" align="left"><p><? echo $company_location[$row[csf('location')]];?></p></td>
                    <td width="90" align="left"><p><? echo $asset_type[$row[csf('asset_type')]];?></p></td>
                    <td width="90" align="left"><p><? echo $asset_category[$row[csf('asset_category')]];?></p></td>
                    <td width="120" align="left"><p><? echo $store_library[$row[csf('store')]];?></p></td>
                    <td width="90" align="left"><p><? echo change_date_format($row[csf("purchase_date")], "dd-mm-yyyy", "-");?></p></td>
                    <td align="right"><p><? echo $row[csf('qty')];?></p></td>
                </tr>
                <?
            }
        }
        ?>
    </tbody>
    </table>
    </div>
    <?
    exit;
}

if ($action=="load_details_container")
{

    $woBasis = $data;

    if($woBasis==2) // independent
    {
        $i=1;
        ?>
        <table class="rpt_table" width="1380" cellspacing="0" cellpadding="0" id="tbl_dtls_item" border="1" rules="all">
            <thead>
                <tr>
                    <th width="90" class="must_entry_caption"><font color="blue">Service For</font></th>
                    <th width="120" class="must_entry_caption"><font color="blue">Service Details</font></th>
                    <th width="130">Item Description</th>
                    <th width="100">Item Category</th>
                    <th width="100">Item Group</th>
                    <th width="90">Machine Category</th>
                    <th width="90">Machine No</th>
                    <th width="70">Service UOM</th>
                    <th width="60" class="must_entry_caption"><font color="blue">Qnty</font></th>
                    <th width="60" class="must_entry_caption"><font color="blue">Rate</font></th>
                    <th width="90">Amount</th>
                    <th width="80">Service Number</th>
                    <th width="120">Remarks</th>
                    <th width="65">Action</th>
                </tr>
            </thead>
            <tbody id="details_part_list">
                <tr class="general" id="tr_1">
                    <td>
                        <?
                            echo create_drop_down( "cboServiceFor_1", 90, $service_for_arr, "", 1, "-- Select --",$selected, 0, "", 0, "", "", "", "", "", "cboServiceFor[]", "cboServiceFor_1" );
                        ?>
                    </td>
                    <td align="center">
                        <input type="text" name="txtServiceDetails[]" id="txtServiceDetails_1" class="text_boxes" style="width:120px" onDblClick="fnc_service_details(1)" placeholder="Double Click To Search" readonly />
                        <input type="hidden" name="hdnServiceId[]" id="hdnServiceId_1"/>
                    </td>

                    <td align="center">
                        <input type="text" name="txtItemDescription[]" id="txtItemDescription_1" class="text_boxes" style="width:130px;" onDblClick="itemDetailsPopup(1)" placeholder="Double Click To Search" readonly />
                        <input type="hidden" name="txt_item_id[]" id="txt_item_id_1" />
                        <input type="hidden" name="txt_row_id[]" id="txt_row_id_1" value="" />
                        <input type="hidden" name="txt_req_no_id[]" id="txt_req_no_id_1"  />
                        <input type="hidden" name="txt_req_dtls_id[]" id="txt_req_dtls_id_1"  />
                    </td>
                    <td align="center">
                        <? echo create_drop_down( "cboItemCategory_1", 120, $blank_array,"", 1, "--Select--", 0, "", 1, "", "", "", "", "", "", "cboItemCategory[]", "cboItemCategory_1" ); ?>
                    </td>
                    <td align="center">
                        <? echo create_drop_down( "cboItemGroup_1", 120, $blank_array,"", 1, "--Select--", 0, "",1, "", "", "", "", "", "", "cboItemGroup[]", "cboItemGroup_1" ); ?>
                    </td>
                    <td align="center">
                        <? echo create_drop_down( "cboMachineCategory_1", 90, $machine_category,"", 1, "--Select--", 0, "load_drop_down( 'requires/service_work_order_controller', this.value+'_'+$i, 'load_drop_down_machine_no','machine_no_td_".$i."' );",0, "", "", "", "", "", "", "cboMachineCategory[]", "cboMachineCategory_1" ); ?>
                    </td>
                    <td align="center" id="machine_no_td_<?= $i; ?>">
                        <?
                            echo create_drop_down( "cboMachineNo_1", 90, $blank_array, "", 1, "-- Select --", 0, "", 0, "", "", "", "", "", "", "cboMachineNo[]", "cboMachineNo_1" );
                        ?>
                    </td>
                    <td align="center">
                        <? echo create_drop_down( "cboUom_1", 70, $service_uom_arr,"", 1, "--Select--", 0, "",0, "", "", "", "", "", "", "cboUom[]", "cboUom_1" ); ?>
                    </td>
                    <td>
                        <input type="text" name="txtqnty[]" id="txtqnty_1" class="text_boxes_numeric" style="width:60px" onKeyUp="calculate_amount(1)" />
                        <input type="hidden" name="hiddenqnty[]" id="hiddenqnty_1"/>
                    </td>
                    <td>
                        <input type="text" name="txtrate[]" id="txtrate_1" class="text_boxes_numeric" value="" style="width:60px;" onKeyUp="calculate_amount(1)" />
                        <input type="hidden" name="hiddentxtrate[]" id="hiddentxtrate_1"/>
                    </td>
                    <td><input type="text" name="txtamount[]" id="txtamount_1" class="text_boxes_numeric" style="width:80px;" readonly /></td>
                    <td><input type="text" name="txt_service_number[]" id="txt_service_number_1" class="text_boxes_numeric" style="width:80px;" /></td>
                    <td><input type="text" name="txtremarks[]" id="txtremarks_1" class="text_boxes" style="width:120px;" /></td>

                    <td>
                        <input type="button" name="increase[]" id="increase_1" style="width:18px" class="formbuttonplasminus" value="+" onClick="add_dtls_tr(1)" />
                        <input type="button" name="decrease[]" id="decrease_1" style="width:18px" class="formbuttonplasminus" value="-" onClick="fnc_delet_dtls_tr(1);" />
                        <input type="hidden" name="txtTagMaterials[]" id="txtTagMaterials_1" />
                    </td>
                </tr>
            </tbody>
            <tfoot class="tbl_bottom">
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>Total</td>
                    <td style="text-align:center">
                        <input type="text" name="txt_total_amount" id="txt_total_amount" class="text_boxes_numeric" value="" style="width:80px;" readonly/>
                    </td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right" >Upcharge Remarks:</td>
                    <td colspan="4" align="center"><input type="text" id="txt_up_remarks" name="txt_up_remarks" class="text_boxes" style="width:90%;" maxlength="100" placeholder="Maximum 100 Character" disabled/></td>
                    <td>Upcharge</td>
                    <td style="text-align:center">
                        <input type="text" name="txt_upcharge" id="txt_upcharge" class="text_boxes_numeric" value="" style="width:80px;" onKeyUp="calculate_total_amount(2);disable_enable_charge_remarks(1);" />
                    </td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right">Discount Remarks:</td>
                    <td colspan="4" align="center"><input type="text" id="txt_discount_remarks" name="txt_discount_remarks" class="text_boxes" style="width:90%;" maxlength="100" placeholder="Maximum 100 Character" disabled/></td>
                    <td>Discount</td>
                    <td style="text-align:center">
                        <input type="text" name="txt_discount" id="txt_discount" class="text_boxes_numeric" value="" style="width:80px;" onKeyUp="calculate_total_amount(2);disable_enable_charge_remarks(2);" />
                    </td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>Net Total</td>
                    <td style="text-align:center">
                        <input type="text" name="txt_total_amount_net" id="txt_total_amount_net" class="text_boxes_numeric" value="" style="width:80px;" readonly/>
                    </td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
            </tfoot>
        </table>
        <?
        exit();
    }
    else //requisition
    {
        ?>
            <table class="rpt_table" width="1380" cellspacing="0" cellpadding="0" id="tbl_dtls_item" border="1" rules="all">
                <thead>
                    <tr>
                        <th width="90" class="must_entry_caption"><font color="blue">Service For</font></th>
                        <th width="120" class="must_entry_caption"><font color="blue">Service Details</font></th>
                        <th width="130">Item Description</th>
                        <th width="100">Item Category</th>
                        <th width="100">Item Group</th>
                        <th width="90">Machine Category</th>
                        <th width="90">Machine No</th>
                        <th width="70">Service UOM</th>
                        <th width="60" class="must_entry_caption"><font color="blue">Qnty</font></th>
                        <th width="60" class="must_entry_caption"><font color="blue">Rate</font></th>
                        <th width="90">Amount</th>
                        <th width="80">Service Number</th>
                        <th width="120">Remarks</th>
                        <th width="100">Matrial List</th>
                        <th >Action</th>
                    </tr>
                </thead>
                <tbody id="details_part_list">
                <!-- append -->
                </tbody>
                <tfoot class="tbl_bottom">
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>Total</td>
                        <td style="text-align:center">
                            <input type="text" name="txt_total_amount" id="txt_total_amount" class="text_boxes_numeric" value="" style="width:80px;" readonly/>
                        </td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td align="right">Upcharge Remarks:</td>
                        <td colspan="4" align="center"><input type="text" id="txt_up_remarks" name="txt_up_remarks" class="text_boxes" style="width:90%;" maxlength="100" placeholder="Maximum 100 Character" disabled/></td>
                        <td>Upcharge</td>
                        <td style="text-align:center">
                            <input type="text" name="txt_upcharge" id="txt_upcharge" class="text_boxes_numeric" value="" style="width:80px;" onKeyUp="calculate_total_amount(2);disable_enable_charge_remarks(1);" />
                        </td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td align="right">Discount Remarks:</td>
                        <td colspan="4" align="center"><input type="text" id="txt_discount_remarks" name="txt_discount_remarks" class="text_boxes" style="width:90%;" maxlength="100" placeholder="Maximum 100 Character" disabled/></td>
                        <td>Discount</td>
                        <td style="text-align:center">
                            <input type="text" name="txt_discount" id="txt_discount" class="text_boxes_numeric" value="" style="width:80px;" onKeyUp="calculate_total_amount(2);disable_enable_charge_remarks(2);" />
                        </td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>Net Total</td>
                        <td style="text-align:center">
                            <input type="text" name="txt_total_amount_net" id="txt_total_amount_net" class="text_boxes_numeric" value="" style="width:80px;" readonly/>
                        </td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                </tfoot>
            </table>
        <?
        exit();
    }

}

if($action=="requitision_popup")
{
    extract($_REQUEST);
    echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
    $app_nes_setup_date=change_date_format(date('d-m-Y'), "", "",1);
    $approval_statusSql="select approval_need, allow_partial from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$company' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '$app_nes_setup_date' and company_id='$company')) and page_id=54 and status_active=1 and is_deleted=0";
    $approval_status_res=sql_select($approval_statusSql);
    $approval_need=$approval_status_res[0][csf("approval_need")];
    $allow_partial=$approval_status_res[0][csf("allow_partial")];
    ?>

    <script>

        var selected_id = new Array;
        var selected_number = new Array;
        var approval_need='<? echo $approval_need; ?>';
        var allow_partial='<? echo $allow_partial; ?>';

        function check_all_data() {
            var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
            tbl_row_count = tbl_row_count - 1;
            for( var i = 1; i <= tbl_row_count; i++ ) {
                $('#tr_'+i).trigger('click');
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
            var old=document.getElementById('txt_row_data').value;
            if(old!="")
            {
                old=old.split(",");
                for(var i=0; i<old.length; i++)
                {
                    js_set_value( old[i] )
                }
            }
        }

        function js_set_value( strParam )
        {
            var splitArr = strParam.split("_");
            var str = splitArr[0];
            var numbers = splitArr[1];
            var ids = splitArr[2]; //requisition id
            var is_approved = splitArr[3];

            if (approval_need==1 && is_approved !=1)
            {
                alert("Please Approve First...");
                return;
            }
            else if (allow_partial==1 && is_approved !=1 && is_approved !=3)
            {
                alert("Please Approve First...");
                return;
            }

            toggle( document.getElementById( 'tr_' + str ), '#FFFFCC' );

            if( jQuery.inArray( ids, selected_id ) == -1 )
            {
                selected_id.push( ids );
                selected_number.push( numbers );

            }
            else
            {
                for( var i = 0; i < selected_id.length; i++ )
                {
                    if( selected_id[i] == ids ) break;
                }
                selected_id.splice( i, 1 );
                selected_number.splice( i, 1 );
            }

            var num =''; var id = '';
            for( var i = 0; i < selected_id.length; i++ )
            {
                id += selected_id[i] + ',';
                num += selected_number[i] + ',';
            }
            id = id.substr( 0, id.length - 1 );
            num = num.substr( 0, num.length - 1 );

            $('#hidden_req_id').val( id );
            $('#hidden_req_number').val( num );
        }

        function reset_hidden()
        {
            $('#hidden_req_number').val('');
            $('#hidden_req_id').val('');
        }

    </script>
    </head>

    <body>
    <div align="center" style="width:100%;" >
        <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="800" cellspacing="0" cellpadding="0" class="rpt_table" align="center">
            <tr>
                <td align="center" width="100%">
                    <table  cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
                         <thead>
                            <th width="160">Item Category</th>
                            <th width="160" align="center">Req. Number</th>
                            <th width="200">Req. Date Range</th>
                            <th width="80"><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" /></th>
                        </thead>
                        <tr>
                            <td width="160">
                            <?
                                echo create_drop_down( "cbo_item_category", 160, "SELECT category_id, short_name from  lib_item_category_list where status_active=1 and is_deleted=0 and category_type=1 and category_id not in(4,11) order by short_name","category_id,short_name", 1, "-- Select --", "", "","","4,11");
                            ?>
                            </td>
                            <td width="160" align="center">
                                <input type="text" style="width:140px" class="text_boxes"  name="txt_req" id="txt_req" />
                            </td>
                            <td align="center">
                                <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px"> To
                                <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
                            </td>
                            <td align="center">
                                <input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_item_category').value+'_'+document.getElementById('txt_req').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>+'_'+document.getElementById('txt_req_numbers_id').value, 'create_req_search_list_view', 'search_div', 'service_work_order_controller', 'setFilterGrid(\'list_view\',-1)');reset_hidden();set_all();" style="width:100px;" />
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td  align="center" height="40" valign="middle">
                    <? echo load_month_buttons(1);  ?>
                    <input type="hidden" id="hidden_req_number" name="hidden_req_number" value="" />
                    <input type="hidden" id="hidden_req_id" name="hidden_req_id" value="" />
                    <input type="hidden" id="txt_req_numbers_id" name="txt_req_numbers_id" value="<?php echo $txt_req_numbers_id; ?>" />
                </td>
            </tr>
            <tr>
            <td align="center" valign="top" id="search_div"></td>
            </tr>
        </table>
        </form>
    </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
}

if($action=="create_req_search_list_view")
{

    extract($_REQUEST);
    $ex_data = explode("_",$data);
    $itemCategory = $ex_data[0];
    $txt_req_no = $ex_data[1];
    $txt_date_from = $ex_data[2];
    $txt_date_to = $ex_data[3];
    $company = $ex_data[4];
    $txt_req_numbers_id = $ex_data[5];

    $sql_cond="";
    if(trim($itemCategory)) $sql_cond .= " and b.item_category='$itemCategory'";
    if ($txt_req_no!="") $sql_cond .= " and a.requ_no like '%".trim($txt_req_no)."'";

    if ($txt_date_from!="" &&  $txt_date_to!="")
    {
        if($db_type==0)
        {
            $sql_cond .= " and a.requisition_date between '".change_date_format($txt_date_from, "yyyy-mm-dd", "-")."' and '".change_date_format($txt_date_to, "yyyy-mm-dd", "-")."'";
        }
        else
        {
            $sql_cond .= " and a.requisition_date between '".change_date_format($txt_date_from,'','',1)."' and '".change_date_format($txt_date_to,'','',1)."'";
        }
    }

    if (trim($company) !="") $sql_cond .= " and a.company_id='$company'";

    $sql = "SELECT a.id as ID, a.requ_no as REQU_NO, a.company_id as COMPANY_ID, a.requisition_date as REQUISITION_DATE, a.pay_mode as PAY_MODE, a.is_approved as IS_APPROVED from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b where a.id=b.mst_id and a.entry_form = 526 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sql_cond group by a.id, a.requ_no, a.company_id, a.requisition_date, a.pay_mode, a.is_approved order by a.id desc";
    //echo $sql;die;
    $result = sql_select($sql);

    $txt_req_numbers_id=explode(",",$txt_req_numbers_id);
    $i=1;
    foreach($result as $row)
    {
        $data=$i."_".$row['REQU_NO']."_".$row['ID'];
        if(in_array($row['ID'],$txt_req_numbers_id))
        {
            if($txt_row_data=="") $txt_row_data=$data; else $txt_row_data.=",".$data;
        }
        $i++;
    }

    $company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
    ?>
     <table cellspacing="0" cellpadding="0" border="1" rules="all" width="650" class="rpt_table" >
        <thead>
            <th width="50">SL</th>
            <th width="150">Company</th>
            <th width="150">Req. Number</th>
            <th width="150">Req. Date</th>
            <th >Pay Mode</th>
        </thead>
    </table>
    <div style="width:652px; overflow-y:scroll; max-height:200px">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="650" class="rpt_table" id="list_view">
            <?
            $i = 1;
            $nameArray = sql_select($sql);
            foreach ($nameArray as $row)
            {
                if ($i % 2 == 0) { $bgcolor = "#E9F3FF";} else { $bgcolor = "#FFFFFF"; }
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="tr_<? echo $i;?>" onClick="js_set_value('<? echo $i."_".$row['REQU_NO']."_".$row['ID']."_".$row['IS_APPROVED']; ?>')">
                    <td width="50" align="center"><? echo "$i"; ?></td>
                    <td width="150" ><p><? echo $company_arr[$row['COMPANY_ID']]; ?></p></td>
                    <td width="150" ><? echo $row['REQU_NO']; ?></td>
                    <td width="150"><p><? echo change_date_format($row['REQUISITION_DATE']); ?></p></td>
                    <td ><p><? echo $pay_mode[$row['PAY_MODE']]; ?>&nbsp;</p></td>
                </tr>
                <?
                $i++;
            }
            ?>
        </table>
        <input type="hidden" name="txt_row_data" id="txt_row_data" value="<?php echo $txt_row_data; ?>"/>
    </div>
    <table width="800" cellspacing="0" cellpadding="0" border="1" align="left">
        <tr>
            <td align="center" height="30" valign="bottom">
                <div style="width:100%">
                    <div style="width:45%; float:left" align="left">
                        <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
                    </div>
                    <div style="width:55%; float:left" align="left">
                        <input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
                    </div>
                </div>
            </td>
        </tr>
    </table>
    <?
    exit();
}

if($action=="show_dtls_listview")
{
	extract($_REQUEST);
	$explodeData = explode("**",$data);
 	$requisition_numberID = str_replace("'","",$explodeData[0]);
	$rowNo = $explodeData[1];

	$item_group_arr=return_library_array( "SELECT id,item_name  from lib_item_group where status_active=1",'id','item_name');

 	$sql = "SELECT a.id as REQUISITION_ID,b.id as REQ_DTLS_ID, a.REQU_NO, b.ID, b.PRODUCT_ID, b.QUANTITY, b.REMARKS, b.SERVICE_FOR, b.SERVICE_DETAILS, b.TAG_MATERIALS, b.SERVICE_UOM, c.ITEM_DESCRIPTION, c.ITEM_CATEGORY_ID, c.ITEM_GROUP_ID,b.RATE,b.AMOUNT, b.SERVICE_LIB_ID
    from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b
    left join product_details_master c on b.product_id=c.id and c.status_active in(1,3)
    where a.id=b.mst_id and a.id in($requisition_numberID) and a.status_active=1 and b.status_active=1 ";
    //echo $sql;die;
	$sqlResult = sql_select($sql);
	if( count($sqlResult)==0 ){ echo "No Data Found";die;}
    $req_dtls_id_arr=array();
    foreach($sqlResult as $row)
    {
        $req_dtls_id_arr[$row["REQ_DTLS_ID"]]=$row["REQ_DTLS_ID"];
    }
    $req_dtls_cond=where_con_using_array($req_dtls_id_arr,0,"requisition_dtls_id");
    $wo_sql="SELECT REQUISITION_DTLS_ID AS REQUISITION_DTLS_ID,sum(amount) as AMOUNT,sum(SUPPLIER_ORDER_QUANTITY) as QNTY FROM WO_NON_ORDER_INFO_DTLS where status_active=1 and is_deleted=0 $req_dtls_cond group by REQUISITION_DTLS_ID";
    //echo $wo_sql;
    $wo_sql=sql_select($wo_sql);

    $requisition_data=array();

    foreach($wo_sql as $row)
    {
        $requisition_data[$row["REQUISITION_DTLS_ID"]]['QNTY']+=$row["QNTY"];
        $requisition_data[$row["REQUISITION_DTLS_ID"]]['AMOUNT']+=$row["AMOUNT"];
    }
    //print_r($requisition_data);
	$i=$rowNo+1; // row no increse 1
    ?>
        <tbody id="details_part_list">
    <?
	foreach($sqlResult as $val)
	{
        // $amount=$val["AMOUNT"]-$requisition_data[$val["REQ_DTLS_ID"]]['AMOUNT'];
        $qnty=$val["QUANTITY"]-$requisition_data[$val["REQ_DTLS_ID"]]['QNTY'];
        if($val["QUANTITY"]>=$requisition_data[$val["REQ_DTLS_ID"]]['QNTY'] && $qnty >0 )
        {
            $amount=$qnty*$val["RATE"];
    	    ?>
            <tr class="general" id="tr_<?=$i;?>">
                <td>
                    <?
                        echo create_drop_down( "cboServiceFor_".$i, 90, $service_for_arr, "", 1, "-- Select --",$val["SERVICE_FOR"], "", 1, "", "", "", "", "", "", "cboServiceFor[]", "cboServiceFor_".$i );
                    ?>
                </td>
                <td align="center">
                    <input type="text" name="txtServiceDetails[]" id="txtServiceDetails_<?=$i;?>" class="text_boxes" style="width:120px" value="<? echo $val["SERVICE_DETAILS"];?>" readonly/>
                    <input type="hidden" name="hdnServiceId[]" id="hdnServiceId_<?=$i;?>" value="<? echo $val["SERVICE_LIB_ID"];?>"/>
                </td>
                <td align="center">
                    <input type="text" name="txtItemDescription[]" id="txtItemDescription_<?=$i;?>" class="text_boxes" style="width:130px;" value="<? echo $val["ITEM_DESCRIPTION"];?>" readonly />
                    <input type="hidden" name="txt_item_id[]" id="txt_item_id_<?=$i;?>" value="<? echo $val["PRODUCT_ID"];?>"/>
                    <input type="hidden" name="txt_row_id[]" id="txt_row_id_<?=$i;?>" value="" />
                    <input type="hidden" name="txt_req_no_id[]" id="txt_req_no_id_<?=$i;?>" value="<? echo $val["REQUISITION_ID"];?>" readonly />
                    <input type="hidden" name="txt_req_dtls_id[]" id="txt_req_dtls_id_<?=$i;?>" value="<? echo $val["ID"];?>" readonly />
                </td>
                <td align="center">
                    <? echo create_drop_down( "cboItemCategory_".$i, 100, $item_category,"", 1, "--Select--", $val["ITEM_CATEGORY_ID"], "", 1, "", "", "", "", "", "", "cboItemCategory[]", "cboItemCategory_".$i ); ?>
                </td>
                <td align="center">
                    <? echo create_drop_down( "cboItemGroup_".$i, 100, $item_group_arr,"", 1, "--Select--", $val["ITEM_GROUP_ID"], "",1, "", "", "", "", "", "", "cboItemGroup[]", "cboItemGroup_".$i ); ?>
                </td>
                <td align="center">
                    <? echo create_drop_down( "cboMachineCategory_".$i, 90, $machine_category,"", 1, "--Select--", 0, "load_drop_down( 'requires/service_work_order_controller', this.value+'_'+$i, 'load_drop_down_machine_no','machine_no_td_".$i."' );",0, "", "", "", "", "", "", "cboMachineCategory[]", "cboMachineCategory_".$i ); ?>
                </td>
                <td align="center" id="machine_no_td_<?= $i; ?>">
                    <?
                        echo create_drop_down( "cboMachineNo_".$i, 90, $blank_array, "", 1, "-- Select --", 0, "", 0, "", "", "", "", "", "", "cboMachineNo[]", "cboMachineNo_".$i );
                    ?>
                </td>
                <td align="center">
                    <? echo create_drop_down( "cboUom_".$i, 70, $service_uom_arr,"", 1, "--Select--", $val["SERVICE_UOM"], "",1, "", "", "", "", "", "", "cboUom[]", "cboUom_".$i ); ?>
                </td>
                <td>
                    <input type="text" name="txtqnty[]" id="txtqnty_<?=$i;?>" class="text_boxes_numeric" style="width:60px" value="<? echo $qnty;?>" onKeyUp="calculate_amount(<?=$i;?>)" />
                    <input type="hidden" name="hiddenqnty[]" id="hiddenqnty_<?=$i;?>"/>
                </td>
                <td>
                    <input type="text" name="txtrate[]" id="txtrate_<?=$i;?>" class="text_boxes_numeric" value="<? echo $val["RATE"];?>" style="width:60px;" onKeyUp="calculate_amount(<?=$i;?>)" />
                    <input type="hidden" name="hiddentxtrate[]" id="hiddentxtrate_<?=$i;?>"/>
                </td>
                <td>
                    <input type="text" name="txtamount[]" id="txtamount_<?=$i;?>" class="text_boxes_numeric" value="<? echo $amount;?>" style="width:80px;" readonly />
                    <input type="hidden" name="txtvalamount[]" id="txtvalamount_<?=$i;?>" class="text_boxes_numeric" value="<? echo $amount;?>" style="width:90px;" readonly />
                </td>
                <td><input type="text" name="txt_service_number[]" id="txt_service_number_<?=$i;?>" class="text_boxes_numeric" style="width:80px;" /></td>
                <td><input type="text" name="txtremarks[]" id="txtremarks_<?=$i;?>" class="text_boxes" style="width:120px;" value="<? echo $val["REMARKS"];?>" onDblClick="openmypage_remarks(<? echo $i;?>)" /></td>

                <td>
                    <a href="#" onClick="fnc_matrial_list(<?=$i;?>)">View</a>
                    <input type="hidden" name="txtTagMaterials[]" id="txtTagMaterials_<?=$i;?>" value="<? echo $val["TAG_MATERIALS"];?>" />
                </td>
                <td>
                    <input type="button" name="decrease[]" id="decrease_1" style="width:18px" class="formbuttonplasminus" value="-" onClick="fnc_delet_dtls_tr(<?=$i;?>);" />
                </td>
            </tr>
            <?
            $i++;
        }
    }
    ?>
        </tbody>
        <tfoot class="tbl_bottom">
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>Total</td>
                <td style="text-align:center">
                    <input type="text" name="txt_total_amount" id="txt_total_amount" class="text_boxes_numeric" value="" style="width:80px;" readonly/>
                </td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td align="right" >Upcharge Remarks:</td>
                <td colspan="4" align="center"><input type="text" id="txt_up_remarks" name="txt_up_remarks" class="text_boxes" style="width:90%;" maxlength="100" placeholder="Maximum 100 Character" disabled/></td>
                <td>Upcharge</td>
                <td style="text-align:center">
                    <input type="text" name="txt_upcharge" id="txt_upcharge" class="text_boxes_numeric" value="" style="width:80px;" onKeyUp="calculate_total_amount(2);disable_enable_charge_remarks(1);" />
                </td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td align="right">Discount Remarks:</td>
                <td colspan="4" align="center"><input type="text" id="txt_discount_remarks" name="txt_discount_remarks" class="text_boxes" style="width:90%;" maxlength="100" placeholder="Maximum 100 Character" disabled/></td>
                <td>Discount</td>
                <td style="text-align:center">
                    <input type="text" name="txt_discount" id="txt_discount" class="text_boxes_numeric" value="" style="width:80px;" onKeyUp="calculate_total_amount(2);disable_enable_charge_remarks(2);" />
                </td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>Net Total</td>
                <td style="text-align:center">
                    <input type="text" name="txt_total_amount_net" id="txt_total_amount_net" class="text_boxes_numeric" value="" style="width:80px;" readonly/>
                </td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
        </tfoot>
    <?
	exit();
}

if ($action=="tag_materials_popup")
{
    echo load_html_head_contents("Item Description Info", "../../../", 1, 1,'','','');
    extract($_REQUEST);
    ?>

    </head>
    <body>
        <div align="center" style="width:100%" >
            <fieldset style="width:900px">
            <?

            $arr=array (1=>$item_category,5=>$unit_of_measurement,9=>$row_status);//5=>$unit_of_measurement,

            $sql="SELECT a.id, a.item_account, a.item_category_id, a.item_description, a.item_size, a.item_group_id, a.unit_of_measure, a.current_stock, a.re_order_label, a.status_active, b.item_name, a.order_uom
            from product_details_master a, lib_item_group b
            where a.item_group_id=b.id and a.status_active in(1,3) and a.is_deleted=0 and a.id in ($tagMaterials) and a.item_category_id in (89,51,52,49,90,99,55,21,67,93,59,48,64,15,57,66,45,47,107,54,70,50,37,69,68,18,46,60,62,9,16,17,38,92,65,10,33,44,34,35,63,19,22,61,97,36,56,8,41,40,91,43,53,20,94,32,58,39) and a.entry_form<>24 ";
            // echo $sql;
            echo  create_list_view ( "list_view","Item Account,Item Category,Item Description,Item Size,Item Group,Order UOM,Stock,Re-Order Level,Product ID,Status", "120,100,140,80,100,80,80,80,80,50","950","250",0, $sql, "", "", "", '', "0,item_category_id,0,0,0,order_uom,0,0,0,status_active", $arr , "item_account,item_category_id,item_description,item_size,item_name,order_uom,current_stock,re_order_label,id,status_active", "", 'setFilterGrid("list_view",-1);','0,0,0,0,0,0,1,1,0,0','' );
            ?>
            </fieldset>
        </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if($action=="tag_req_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,1);
	?>

	<script>
		var selected_id = new Array;
		var selected_number = new Array;
		function check_all_data() {
			var tbl_row_count = document.getElementById( 'table_body' ).rows.length;
			tbl_row_count = tbl_row_count - 1;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				$('#tr_'+i).trigger('click');
			}
		}

		function set_all()
		{
			var old=document.getElementById('txt_row_data').value;
			if(old!="")
			{
				old=old.split(",");
				for(var i=0; i<old.length; i++)
				{
					js_set_value( old[i] )
				}
			}
		}

		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		var kk=0;

		function js_set_value( strParam )
		{
            var chk_st=$('#check_all').is(":checked");
            if(chk_st==true)kk++;

            var splitArr = strParam.split("_");
            var str = splitArr[0];
            var numbers = splitArr[1];
            var ids = splitArr[2];

            toggle( document.getElementById( 'tr_' + str ), '#FFFFCC' );

            if( jQuery.inArray( ids, selected_id ) == -1 ) {
                selected_id.push( ids );
                selected_number.push( numbers );
            }
            else {
                for( var i = 0; i < selected_id.length; i++ ) {
                    if( selected_id[i] == numbers ) break;
                }
                selected_id.splice( i, 1 );
                selected_number.splice( i, 1 );
            }

            var num = id = '';
            for( var i = 0; i < selected_id.length; i++ ) {
                id += selected_id[i] + ',';
                num += selected_number[i] + ',';
            }
            id = id.substr( 0, id.length - 1 );
            num = num.substr( 0, num.length - 1 );

            $('#txt_selected_ids').val( id );
            $('#txt_selected_numbers').val( num );
		}

		function reset_hidden()
		{
			$('#txt_selected_ids').val('');
			$('#txt_selected_numbers').val('');
		}

		</script>

	</head>

	<body>
	<div align="center" style="width:100%;" >

	<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
		<table width="980" cellspacing="0" cellpadding="0" class="rpt_table" align="center">
				<tr>
					<td align="center" width="100%">
						<table  cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
							<thead>
								<th width="150">Company Name</th>
								<th width="150">Store Name</th>
								<th width="100">Reqsition Year</th>
								<th width="100">Reqsition No</th>
								<th width="200">Date Range</th>
								<th width="80"><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" /></th>
							</thead>
							<tr>
								<td width="150">
									<?
										echo create_drop_down( "cbo_company_name", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select --", $company, "",1 );
									?>
								</td>
								<td width="150">
									<?
										echo create_drop_down( "cbo_store_name", 162, "SELECT a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.company_id=$company and b.category_type not in(4,11) $store_location_credential_cond group by a.id,a.store_name order by a.store_name","id,store_name", 1, "-- Select --", "", "", "","");
									?>
								</td>
								<td  align="center"> <? echo create_drop_down("cbo_req_year", 65, create_year_array(), "", 0, "-- --", date("Y", time()), "", 0, ""); ?></td>
								<td  align="center"> <input type="text" id="txt_req_no" name="txt_req_no" class="text_boxes" style="width:100px;" ></td>
								<td align="center">
									<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px"> To
									<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
									<input type="hidden" id="txt_selected_ids" name="txt_selected_ids" value="<? echo $req_numbers_id; ?>" />
									<input type="hidden" id="txt_selected_numbers" name="txt_selected_numbers" value="<? echo $req_numbers; ?>" />
								</td>
								<td align="center">
									<input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'_'+document.getElementById('cbo_store_name').value+'_'+document.getElementById('cbo_req_year').value+'_'+document.getElementById('txt_req_no').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'create_tag_req_search_list_view', 'search_div', 'service_work_order_controller', 'setFilterGrid(\'table_body\',-1)');reset_hidden();set_all();" style="width:100px;" />
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td  align="center" height="40" valign="middle">
						<? echo load_month_buttons(1);  ?>
					</td>
				</tr>
				<tr>
					<td align="center" valign="top" id="search_div"></td>
				</tr>
		</table>
		</form>
	</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}

if($action=="create_tag_req_search_list_view")
{

 	extract($_REQUEST);
	list($companyName,$storeName,$reqsition_year,$req_no,$txt_date_from,$txt_date_to) = explode("_",$data);

    $company=return_library_array( "SELECT id, company_name from lib_company",'id','company_name');
	$location=return_library_array("SELECT id,location_name from lib_location",'id','location_name');
    $store_library=return_library_array( "SELECT id, store_name from  lib_store_location", "id", "store_name"  );
	$department_library=return_library_array("SELECT id,department_name from lib_department",'id','department_name');
	$section_library=return_library_array("SELECT id,section_name from lib_section",'id','section_name');
    $user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");

	$sql_cond="";
 	if($companyName!=0){$sql_cond = " and a.company_id = '".$companyName."'";}
	if($storeName!=0){$sql_cond .= " and a.store_name = '".$storeName."'";}

	if($txt_date_from!="" || $txt_date_to!="")
	{
		if($db_type==0) $sql_cond .= " and a.requisition_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
		else if($db_type==2) $sql_cond .= " and a.requisition_date between '".change_date_format($txt_date_from,'','',-1)."' and '".change_date_format($txt_date_to,'','',-1)."'";
	}

	if ($req_no!="")
	{
		$sql_cond .=" and a.requ_prefix_num=$req_no";
	}

	if($db_type==0){ $sql_cond .=" and year(a.insert_date)=".$reqsition_year.""; }
    else{ $sql_cond .=" and to_char(a.insert_date,'YYYY')=".$reqsition_year.""; }

    $sql = "SELECT a.ID, a.REQU_PREFIX_NUM, a.REQU_NO, a.COMPANY_ID, a.REQUISITION_DATE, a.LOCATION_ID, a.DEPARTMENT_ID,a.SECTION_ID, a.STORE_NAME, a.MANUAL_REQ, a.INSERTED_BY, a.READY_TO_APPROVE, a.IS_APPROVED from inv_purchase_requisition_mst a where a.entry_form = 69 and a.status_active=1 and a.is_deleted=0 $sql_cond order by a.id desc";
	//  echo $sql;
	?>
    <div style="margin-top:10px; width:1120px;">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1100" class="rpt_table" align="left">
            <thead>
                <th width="30">SL</th>
                <th width="60">Requisition No</th>
                <th width="65">Requisition Date</th>
                <th width="110">Company</th>
                <th width="110">Location</th>
                <th width="90">Department</th>
                <th width="140">Section</th>
                <th width="90">Store Name</th>
                <th width="80">Manual Req</th>
                <th width="80">Inserted by</th>
                <th width="80">Ready to Approve</th>
                <th>Approval Status</th>
            </thead>
		</table>
		<div style="width:1120px; overflow-y:scroll; max-height:200px">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1100" class="rpt_table" id="table_body">
                <?php
					$i = 1;
					$txt_row_data = "";
					$hidden_dtls_id = explode(",", $req_dtls_id);
					$nameArray = sql_select($sql);
					foreach ($nameArray as $selectResult)
					{
                        if ($i % 2 == 0) { $bgcolor = "#E9F3FF";} else { $bgcolor = "#FFFFFF"; }
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="tr_<? echo $i;?>" onClick="js_set_value('<? echo $i."_".$selectResult[csf('requ_no')]."_".$selectResult[csf('id')]; ?>')">
								<td width="30" align="center"><?php echo "$i"; ?></td>
								<td width="60" align="center"><p><?php echo $selectResult['REQU_PREFIX_NUM']; ?></p></td>
								<td width="65" align="center"><?php echo change_date_format($selectResult['REQUISITION_DATE']); ?></td>
								<td width="110"><p><?php echo $company[$selectResult['COMPANY_ID']]; ?></p></td>
								<td width="110"><p><?php echo $location[$selectResult['LOCATION_ID']]; ?>&nbsp;</p></td>
								<td width="90"><p><?php echo $department_library[$selectResult['DEPARTMENT_ID']]; ?>&nbsp;</p></td>
								<td width="140"><p><?php echo $section_library[$selectResult['SECTION_ID']]; ?></p></td>
								<td width="90"><p><?php echo $store_library[$selectResult['STORE_NAME']]; ?></p></td>
								<td width="80"><p><?php echo $selectResult['MANUAL_REQ']; ?></p></td>
                                <td width="80" ><?php echo $user_lib_name[$selectResult['INSERTED_BY']]; ?></td>
                                <td width="80" >
                                    <?
                                        if ($selectResult["READY_TO_APPROVE"]==1){ $ready_msg='Yes';}else{ $ready_msg='No';}
                                        echo $ready_msg;
                                    ?>
                                </td>
                                <td align="center"><p>
                                    <?
                                    if ($selectResult["IS_APPROVED"]==1) $approved_msg='Yes';
                                    else if ($selectResult["IS_APPROVED"]==3) $approved_msg='Partial Approved';
                                    else $approved_msg='No';
                                    echo $approved_msg;
                                    ?>
                                </p></td>
							</tr>
							<?
							$i++;
					}
				?>
			</table>
		</div>
        <table width="1100" cellspacing="0" cellpadding="0" border="1" align="left">
            <tr>
                <td align="center" height="30" valign="bottom">
                    <div style="width:100%">
                        <div style="width:45%; float:left" align="left">
                            <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
                        </div>
                        <div style="width:55%; float:left" align="left">
                            <input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
                        </div>
                    </div>
                </td>
            </tr>
        </table>
	</div>
	<?
	exit();
}

if($action=="scope_popup")
{
 	extract($_REQUEST);
    echo load_html_head_contents("scope Popup Info","../../../", 1, 1, $unicode);
    $scope_beneficiary_arr=explode("__",$scope_beneficiary);
    $scope_service_provider_arr=explode("__",$scope_service_provider);

    ?>
    <script>
        function popup_add_dtls_tr(tbl,i)
        {
            if(tbl==1){ var row_num = $('#tbl_schope_beneficiary tbody tr').length; }
            else{ var row_num = $('#tbl_service_provider tbody tr').length; }

            if(row_num == 0){
                var responseHtml = return_ajax_request_value(tbl+'**'+i, 'append_popup_details_container', 'service_work_order_controller');

                if(tbl==1){ $("#tbl_schope_beneficiary tbody").append(responseHtml); }
                else{ $("#tbl_service_provider tbody").append(responseHtml); }

                $('#popup_increasehd_'+tbl+'_'+i).removeAttr("value").attr("value","+");
                $('#popup_decreasehd_'+tbl+'_'+i).removeAttr("value").attr("value","-");
                $('#popup_increasehd_'+tbl+'_'+i).removeAttr("onclick").attr("onclick","popup_add_dtls_tr("+tbl+","+i+");");
                $('#popup_decreasehd_'+tbl+'_'+i).removeAttr("onclick").attr("onclick","popup_delete_dtls_tr("+tbl+","+i+");");

                set_all_onclick();
            }else {
                if(tbl==1){ var lastId = $('#tbl_schope_beneficiary tbody tr:last').attr('id'); }
                else{ var lastId = $('#tbl_service_provider tbody tr:last').attr('id');}

                if (lastId !==  'tr_'+tbl+'_'+i) {
                    return false;
                } else {
                    var rowIdContainer = [];
                    if(tbl==1)
                    {
                        $("#tbl_schope_beneficiary tbody tr").each(function (index){
                            rowIdContainer.push($(this).attr('id').replace('tr_1_', ''));
                        });
                    }
                    else
                    {
                        $("#tbl_service_provider tbody tr").each(function (index){
                            rowIdContainer.push($(this).attr('id').replace('tr_2_', ''));
                        });
                    }

                    var i = Math.max(...rowIdContainer);
                    i++;
                    var responseHtml = return_ajax_request_value(tbl+'**'+i, 'append_popup_details_container', 'service_work_order_controller');
                    if(tbl==1){ $("#tbl_schope_beneficiary tbody").append(responseHtml); }
                    else{ $("#tbl_service_provider tbody").append(responseHtml); }

                    $('#popup_increasehd_'+tbl+'_'+i).removeAttr("value").attr("value", "+");
                    $('#popup_decreasehd_'+tbl+'_'+i).removeAttr("value").attr("value", "-");
                    $('#popup_increasehd_'+tbl+'_'+i).removeAttr("onclick").attr("onclick", "popup_add_dtls_tr("+tbl+","+i+");");
                    $('#popup_decreasehd_'+tbl+'_'+i).removeAttr("onclick").attr("onclick", "popup_delete_dtls_tr("+tbl+","+i+");");

                    set_all_onclick();
                }
            }
        }

        function popup_delete_dtls_tr(tbl,i)
        {
            if(tbl==1)
            {
                var numRow = $('#tbl_schope_beneficiary tbody tr').length;
                if(numRow === 1) {
                    $('#tbl_schope_beneficiary tbody').find('#tr_'+i).remove();
                    var countColumn = $('#tbl_schope_beneficiary thead tr:first').find('th').length;
                    $('#tbl_schope_beneficiary tbody').append('<tr id="first_row_add"><td colspan="'+countColumn+'" style="text-align: center;"><input type="button" value="Add Row" name="addrow" onclick="popup_add_dtls_tr(1,1)" style="width:80px" class="formbutton"></td></tr>');
                }else{
                    $('#tbl_schope_beneficiary tbody').find('#tr_1_'+i).remove();
                }
            }
            else if(tbl==2)
            {
                var numRow = $('#tbl_service_provider tbody tr').length;
                if(numRow === 1) {
                    $('#tbl_service_provider tbody').find('#tr_'+i).remove();
                    var countColumn = $('#tbl_service_provider thead tr:first').find('th').length;
                    $('#tbl_service_provider tbody').append('<tr id="first_row_add"><td colspan="'+countColumn+'" style="text-align: center;"><input type="button" value="Add Row" name="addrow" onclick="popup_add_dtls_tr(2,1)" style="width:80px" class="formbutton"></td></tr>');
                }else{
                    $('#tbl_service_provider tbody').find('#tr_2_'+i).remove();
                }
            }

        }

		function js_set_value()
		{
            var txt_beneficiary='';
            var txt_service_provider='';
            $("#tbl_schope_beneficiary").find('tbody tr').each(function()
            {
                if(txt_beneficiary!=""){txt_beneficiary+="__"}
                txt_beneficiary+=$(this).find('input[name="txt_beneficiary[]"]').val();
            });

            $("#tbl_service_provider").find('tbody tr').each(function()
            {
                if(txt_service_provider!=""){txt_service_provider+="__"}
                txt_service_provider+=$(this).find('input[name="txt_service_provider[]"]').val();
            });
			$('#hdn_scope_beneficiary').val(txt_beneficiary);
			$('#hdn_scope_service_provider').val(txt_service_provider);
			parent.emailwindow.hide();
		}
	</script>
    <div style="margin-top:10px; width:900px;">
        <form name="searchschopefrm_1"  id="searchschopefrm_1" autocomplete="off" >
        <table class="rpt_table" width="680" cellspacing="2" cellpadding="0" border="0" id="tbl_schope_beneficiary">
            <thead>
                <tr>
                    <th width="30">SL</th>
                    <th width="580">Scope of Beneficiary</th>
                    <th >Action</th>
                </tr>
            </thead>
            <tbody>
            <?
                if(count($scope_beneficiary_arr)>0)
                {
                    $i=1;
                    foreach($scope_beneficiary_arr as $row)
                    {
                        ?>
                            <tr class="general" id="tr_1_<?=$i;?>">
                                <td align="center"><?=$i;?></td>
                                <td align="center"><input type="text" name="txt_beneficiary[]" id="txt_beneficiary_<?=$i;?>" value="<?=$row;?>" class="text_boxes" style="width:580px"></td>
                                <td align="center">
                                    <input type="button" name="popup_increase[]" id="popup_increase_1_<?=$i;?>" style="width:18px" class="formbuttonplasminus" value="+" onClick="popup_add_dtls_tr(1,<?=$i;?>)" />
                                    <input type="button" name="popup_decrease[]" id="popup_decrease_1_<?=$i;?>" style="width:18px" class="formbuttonplasminus" value="-" onClick="popup_delete_dtls_tr(1,<?=$i;?>);" />
                                </td>
                            </tr>
                        <?
                        $i++;
                    }

                }
                else
                {
                    ?>
                        <tr class="general" id="tr_1_1">
                            <td align="center">1</td>
                            <td align="center"><input type="text" name="txt_beneficiary[]" id="txt_beneficiary_1" class="text_boxes" style="width:580px"></td>
                            <td align="center">
                                <input type="button" name="popup_increase[]" id="popup_increase_1_1" style="width:18px" class="formbuttonplasminus" value="+" onClick="popup_add_dtls_tr(1,1)" />
                                <input type="button" name="popup_decrease[]" id="popup_decrease_1_1" style="width:18px" class="formbuttonplasminus" value="-" onClick="popup_delete_dtls_tr(1,1);" />
                            </td>
                        </tr>
                    <?
                }
            ?>
            </tbody>
        </table>
        <br>
        <table class="rpt_table" width="680" cellspacing="2" cellpadding="0" border="0" id="tbl_service_provider">
            <thead>
                <tr>
                    <th width="30">SL</th>
                    <th width="580">Scope of Service Provider</th>
                    <th >Action</th>
                </tr>
            </thead>
            <tbody>
            <?
                if(count($scope_service_provider_arr)>0)
                {
                    $i=1;
                    foreach($scope_service_provider_arr as $row)
                    {
                        ?>
                            <tr class="general" id="tr_2_<?=$i;?>">
                                <td align="center"><?=$i;?></td>
                                <td align="center"><input type="text" name="txt_service_provider[]" id="txt_service_provider_<?=$i;?>" value="<?=$row;?>" class="text_boxes" style="width:580px"></td>
                                <td align="center">
                                    <input type="button" name="popup_increase[]" id="popup_increase_2_<?=$i;?>" style="width:18px" class="formbuttonplasminus" value="+" onClick="popup_add_dtls_tr(2,<?=$i;?>)" />
                                    <input type="button" name="popup_decrease[]" id="popup_decrease_2_<?=$i;?>" style="width:18px" class="formbuttonplasminus" value="-" onClick="popup_delete_dtls_tr(2,<?=$i;?>);" />
                                </td>
                            </tr>
                        <?
                    $i++;
                    }
                }
                else
                {
                    ?>
                        <tr class="general" id="tr_2_1">
                            <td align="center">1</td>
                            <td align="center"><input type="text" name="txt_service_provider[]" id="txt_service_provider_1"  class="text_boxes" style="width:580px"></td>
                            <td align="center">
                                <input type="button" name="popup_increase[]" id="popup_increase_2_1" style="width:18px" class="formbuttonplasminus" value="+" onClick="popup_add_dtls_tr(2,1)" />
                                <input type="button" name="popup_decrease[]" id="popup_decrease_2_1" style="width:18px" class="formbuttonplasminus" value="-" onClick="popup_delete_dtls_tr(2,1);" />
                            </td>
                        </tr>
                    <?
                }
            ?>
            </tbody>
        </table>
        <table width="680" cellspacing="2" cellpadding="0" border="0">
            <tr>
                <td align="center">
                    <input type="button" id="btn_close" style="width:100px" class="formbutton" value="Close" onClick="js_set_value();" />
                    <input type="hidden" name="hdn_scope_beneficiary" id="hdn_scope_beneficiary">
                    <input type="hidden" name="hdn_scope_service_provider" id="hdn_scope_service_provider">
                </td>
            </tr>
        </table>
        </form>
	</div>
	<?
	exit();
}

if ($action=="append_popup_details_container")
{
    //echo $data;
    $data = explode("**",$data);
    $tbl=$data[0];
    $i=$data[1];
    ?>
    <tr class="general" id="tr_<?=$tbl.'_'.$i;?>">
        <td><?=$i;?></td>
        <td>
            <?
                if($tbl==1)
                {
                    ?>
                        <input type="text" name="txt_beneficiary[]" id="txt_beneficiary_<?=$i;?>" class="text_boxes" style="width:580px">
                    <?
                }
                else
                {
                    ?>
                        <input type="text" name="txt_service_provider[]" id="txt_service_provider_<?=$i;?>" class="text_boxes" style="width:580px" >
                    <?
                }
            ?>
        </td>
        <td>
            <input type="button" name="popup_increase[]" id="popup_increase_<?=$tbl.'_'.$i;?>" style="width:18px" class="formbuttonplasminus" value="+" onClick="popup_add_dtls_tr(<?=$tbl;?>,<?=$i;?>)" />
            <input type="button" name="popup_decrease[]" id="popup_decrease_<?=$tbl.'_'.$i;?>" style="width:18px" class="formbuttonplasminus" value="-" onClick="popup_delete_dtls_tr(<?=$tbl;?>,<?=$i;?>);" />
        </td>
    </tr>
    <?
    exit();
}

/*if($action=="service_details_popup")
{
    echo load_html_head_contents("Service Details Info", "../../../", 1, 1,'','','');
    extract($_REQUEST);
    ?>
    </head>
    <body>
    <div align="center">
        <h3>Service Details</h3>
        <form name="styleRef_form" id="styleRef_form">
        <fieldset style="width:580px;">
            <textarea name="txt_service_details" id="txt_service_details" cols="90" rows="10" value="<? echo $service_details;?>"><? echo $service_details;?></textarea>
        </fieldset>
        <br>
        <input type="button" id="btn_close" style="width:100px" class="formbutton" value="Close" onClick="parent.emailwindow.hide();" />
    </form>
    </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}*/

if($action=="service_details_popup")
{
    echo load_html_head_contents("Service Details Info", "../../../", 1, 1,'','','');
    extract($_REQUEST);
	
    ?>
     <script>

     var selected_id = new Array, selected_name = new Array(); selected_attach_id = new Array();

     function check_all_data()
        {
            var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
            tbl_row_count = tbl_row_count - 1;

            for( var i = 1; i <= tbl_row_count; i++ ) {
                 eval($('#tr_'+i).attr("onclick"));
            }
        }

    function toggle( x, origColor ) {
            var newColor = 'yellow';
            if ( x.style ) {
                x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
            }
        }

    function js_set_value(id)
    {
        
        var str=id.split("_");
        toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFFF' );
        var id=str[1];
		var name=str[2];
		//alert (id);return;
        if( jQuery.inArray(  id , selected_id ) == -1 ) {
            selected_id.push( id );
			selected_name.push( name );
        }
        else 
		{
            for( var i = 0; i < selected_id.length; i++ ) 
			{
                if( selected_id[i] == id  ) break;
            }
            selected_id.splice( i, 1 );
			selected_name.splice( i, 1 );
        }
        var ids = ''; var names = '';
        for( var i = 0; i < selected_id.length; i++ ) {
            ids += selected_id[i] + ',';
			names += selected_name[i] + ',';
        }
        ids = ids.substr( 0, ids.length - 1 );
		names = names.substr( 0, names.length - 1 );

        $('#hdnServiceId').val( ids );
		$('#hdnServiceName').val( names );
    }

    </script>
    </head>
    <body>
    <div align="center">
        <h3>Service Details</h3>
        <input type="hidden" id="hdnServiceId" name="hdnServiceId" />
        <input type="hidden" id="hdnServiceName" name="hdnServiceName" />
        <form name="styleRef_form" id="styleRef_form">
        <?
		$serivce_sql="select ID, SERVICE_CODE, SERVICE_GROUP, SERVICE_CATEGORY, SERVICE_NAME from LIB_SERVICE_CATEGORY where STATUS_ACTIVE=1";
		$serivce_sql_result=sql_select($serivce_sql);
		if(count($serivce_sql_result)>0)
		{
			?>
            <fieldset style="width:580px;">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="580" class="rpt_table" id="list_view">
                	<thead>
                    	<tr>
                        	<td width="30" align="center" style="font-size:14px; font-weight:bold">SL</td>
                            <td width="130" align="center" style="font-size:14px; font-weight:bold">Service Code</td>
                            <td width="140" align="center" style="font-size:14px; font-weight:bold">Service Group</td>
                            <td width="140" align="center" style="font-size:14px; font-weight:bold">Service Category</td>
                            <td align="center" style="font-size:14px; font-weight:bold">Service Name</td>
                        </tr>
                    </thead>
                    <tbody id="list_view_2">
                    <?
					$i=1;
					foreach($serivce_sql_result as $val)
					{
						 if ($i%2==0) $bgcolor="#E9F3FF";
	                     else $bgcolor="#FFFFFF";
						?>
                        <tr bgcolor="<?=$bgcolor; ?>" id="tr_<?=$i; ?>" onClick="js_set_value('<? echo $i."_".$val["ID"]."_".$val["SERVICE_NAME"]; ?>')" style="cursor:pointer">
                        	<td align="center"><?= $i;?></td>
                            <td align="center" id="serviceCode_<?=$i;?>"><? echo $val["SERVICE_CODE"];?></td>
                            <td align="center" id="serviceGroup_<?=$i;?>"><? echo $val["SERVICE_GROUP"];?></td>
                            <td align="center" id="serviceCategory_<?=$i;?>"><? echo $val["SERVICE_CATEGORY"];?></td>
                            <td align="center" id="serviceName_<?=$i;?>"><? echo $val["SERVICE_NAME"];?></td>
                        </tr>
                        <?
						$i++;
					}
					?>
                    	
                    </tbody>
                </table>
            </fieldset>
            <script type="text/javascript">
                setFilterGrid('list_view_2',-1);
                set_all();
            </script>
            <?
		}
		else
		{
			?>
            <fieldset style="width:580px;">
                <textarea style="height: 80px;" name="txt_service_details" id="txt_service_details" class="text_area" cols="90" rows="10" value="<? echo $service_details;?>"><? echo $service_details;?></textarea>
            </fieldset>
            <?
		}
		?>
        <br>
        <input type="button" id="btn_close" style="width:100px" class="formbutton" value="Close" onClick="parent.emailwindow.hide();" />
    	</form>
    </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if ($action=="load_php_popup_to_lib")
{
    $explode_data = explode("**",$data);
    $i=$explode_data[0];
    $service_id=$explode_data[1];
    $service_for=$explode_data[2];
    //$item=$explode_data[2];

    if($data!="")
    {
        $serivce_sql="select ID, SERVICE_CODE, SERVICE_GROUP, SERVICE_CATEGORY, SERVICE_NAME from LIB_SERVICE_CATEGORY where STATUS_ACTIVE=1 and id in($service_id)";
		$serivce_sql_result=sql_select($serivce_sql);
        //$i=1;
        foreach ($serivce_sql_result as $val)
        {
			?>
			<tr class="general" id="tr_<?= $i; ?>">
				<td>
					<? echo create_drop_down( "cboServiceFor_".$i, 90, $service_for_arr, "", 1, "-- Select --",$service_for, "", "", "", "", "", "", "", "", "cboServiceFor[]", "cboServiceFor_".$i ); ?>
				</td>
				<td align="center">
					<input type="text" name="txtServiceDetails[]" id="txtServiceDetails_<?= $i; ?>" class="text_boxes" style="width:90px;" onDblClick="fncServiceDetails(<?=$i;?>)" placeholder="Double Click To Search" value="<? echo $val["SERVICE_NAME"];?>" readonly/>
					<input type="hidden" name="hdnServiceId[]" id="hdnServiceId_<?= $i; ?>" value="<? echo $val["ID"];?>" />
				</td>
				<td align="center">
					<input type="text" name="txtItemDescription[]" id="txtItemDescription_<?= $i; ?>" class="text_boxes" style="width:130px;" placeholder="Double Click To Search" value="" disabled readonly />
					<input type="hidden" name="txt_item_id[]" id="txt_item_id_<?=$i;?>" value="<? echo $val["PRODUCT_ID"];?>"/>
                    <input type="hidden" name="txt_row_id[]" id="txt_row_id_<?=$i;?>" value="" />
                    <input type="hidden" name="txt_req_no_id[]" id="txt_req_no_id_<?=$i;?>" value="<? echo $val["REQUISITION_ID"];?>" readonly />
                    <input type="hidden" name="txt_req_dtls_id[]" id="txt_req_dtls_id_<?=$i;?>" value="<? echo $val["ID"];?>" readonly />
				</td>
				<td align="center">
					<? echo create_drop_down( "cboItemCategory_".$i, 120, $blank_array,"", 1, "-- Select --",0, "",1,"","","","","","","cboItemCategory[]","cboItemCategory_".$i ); ?>
				</td>
				<td align="center">
					<? echo create_drop_down( "cboItemGroup_".$i,100,$blank_array,"", 1,"Select",0, "",1,"","","","","","","cboItemGroup[]","cboItemGroup_".$i ); ?>
				</td>
                <td align="center">
                    <? echo create_drop_down( "cboMachineCategory_".$i, 90, $machine_category,"", 1, "--Select--", 0, "load_drop_down( 'requires/service_work_order_controller', this.value+'_'+$i, 'load_drop_down_machine_no','machine_no_td_".$i."' );",0, "", "", "", "", "", "", "cboMachineCategory[]", "cboMachineCategory_".$i ); ?>
                </td>
                <td align="center" id="machine_no_td_<?= $i; ?>">
                    <?
                        echo create_drop_down( "cboMachineNo_".$i, 90, $blank_array, "", 1, "-- Select --", 0, "", 0, "", "", "", "", "", "", "cboMachineNo[]", "cboMachineNo_".$i );
                    ?>
                </td>
                <td align="center">
                    <? echo create_drop_down( "cboUom_".$i, 70, $service_uom_arr,"", 1, "--Select--", 1, "",1, "", "", "", "", "", "", "cboUom[]", "cboUom_".$i ); ?>
                </td>
                <td>
                    <input type="text" name="txtqnty[]" id="txtqnty_<?=$i;?>" class="text_boxes_numeric" style="width:60px" value="<? echo $qnty;?>" onKeyUp="calculate_amount(<?=$i;?>)" />
                    <input type="hidden" name="hiddenqnty[]" id="hiddenqnty_<?=$i;?>"/>
                </td>
                <td>
                    <input type="text" name="txtrate[]" id="txtrate_<?=$i;?>" class="text_boxes_numeric" value="" style="width:60px;" onKeyUp="calculate_amount(<?=$i;?>)" />
                    <input type="hidden" name="hiddentxtrate[]" id="hiddentxtrate_<?=$i;?>"/>
                </td>
                <td>
                    <input type="text" name="txtamount[]" id="txtamount_<?=$i;?>" class="text_boxes_numeric" value="" style="width:80px;" readonly />
                    <input type="hidden" name="txtvalamount[]" id="txtvalamount_<?=$i;?>" class="text_boxes_numeric" value="" style="width:90px;" readonly />
                </td>
                <td><input type="text" name="txt_service_number[]" id="txt_service_number_<?=$i;?>" class="text_boxes_numeric" style="width:80px;" /></td>
                <td><input type="text" name="txtremarks[]" id="txtremarks_<?=$i;?>" class="text_boxes" style="width:120px;" value="" /></td>

                <td>
                    <a href="#" onClick="fnc_matrial_list(<?=$i;?>)">View</a>
                    <input type="hidden" name="txtTagMaterials[]" id="txtTagMaterials_<?=$i;?>" value="" />
                    <input type="button" name="decrease[]" id="decrease_1" style="width:18px" class="formbuttonplasminus" value="-" onClick="fnc_delet_dtls_tr(<?=$i;?>);" />
                </td>
			</tr>   
			<?
			$i++;
        }
    }
    exit();
}

if ($action=="service_work_order_print")
{
    extract($_REQUEST);
	$data=explode('*',$data);
    $company=$data[0];
    $mst_id=$data[1];
    $rpt_title=$data[2];
    $template_id=$data[3];
    echo load_html_head_contents($rpt_title,"../../", 1, 1, $unicode,'','')
    ?>
    <link rel="stylesheet" href="../../../css/style_common.css" type="text/css" />
    <?

	$company_library=return_library_array( "SELECT id, company_name from lib_company", "id", "company_name"  );
	$item_name_arr=return_library_array("SELECT id,item_name from lib_item_group", "id","item_name");
	$lib_supplier_name= return_library_array('SELECT id,supplier_name FROM lib_supplier','id','supplier_name');
	$lib_supplier_add = return_library_array('SELECT id,address_1 FROM lib_supplier','id','address_1');;
    $user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");

    $image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
 
    $sql_company=sql_select("SELECT id, company_name, company_short_name, plot_no, level_no, road_no, block_no, city, zip_code from lib_company where status_active=1 and is_deleted=0 and id=$company");
    $com_name=$sql_company[0][csf("company_name")];
    $company_short_name=$sql_company[0][csf("company_short_name")];
    $plot_no=$sql_company[0][csf("plot_no")];
    $level_no=$sql_company[0][csf("level_no")];
    $road_no=$sql_company[0][csf("road_no")];
    $block_no=$sql_company[0][csf("block_no")];
    $city=$sql_company[0][csf("city")];
    $zip_code=$sql_company[0][csf("zip_code")];

    $com_address='';
    if($plot_no !=''){ $com_address.=$plot_no;}
    if($level_no !=''){ $com_address.=", ".$level_no;}
    if($road_no !=''){ $com_address.=", ".$road_no;}
    if($block_no !=''){ $com_address.=", ".$block_no;}
    if($city !=''){ $com_address.=", ".$city;}
    if($zip_code !=''){ $com_address.=", ".$zip_code;}

    $data_sql="SELECT wo_number as WO_NUMBER, wo_date as WO_DATE, supplier_id as SUPPLIER_ID, pay_mode as PAY_MODE, currency_id as CURRENCY_ID, attention as ATTENTION, location_id as LOCATION_ID, fixed_asset as FIXED_ASSET, asset_no as ASSET_NO, inserted_by as INSERTED_BY, up_charge as UP_CHARGE, discount as DISCOUNT, REMARKS, UPCHARGE_REMARKS, DISCOUNT_REMARKS,SCOPE_SERVICE_PROVIDER, SCOPE_BENEFICIARY from wo_non_order_info_mst where id=$mst_id";
    //echo $data_sql;
    $data_result=sql_select($data_sql);
    $inserted_by=$data_result[0]['INSERTED_BY'];
    $asset_no=$data_result[0]['ASSET_NO'];
    $carrency_id=$data_result[0]['CURRENCY_ID'];
    $upcharge=$data_result[0]['UP_CHARGE'];
    $discount=$data_result[0]['DISCOUNT'];
    $up_remark=$data_result[0]['UPCHARGE_REMARKS'];
    $dis_remerk=$data_result[0]['DISCOUNT_REMARKS'];
    $scope_service_provider=$data_result[0]['SCOPE_SERVICE_PROVIDER'];
    $service_provider=explode('__',$scope_service_provider);
    $scope_baneficaly=$data_result[0]['SCOPE_BENEFICIARY'];
    $baneficaly=explode('__',$scope_baneficaly);
    if($db_type==0){ $conversion_date=change_date_format($data_result[0]['WO_DATE'], "Y-m-d", "-",1); }
    else { $conversion_date=change_date_format($data_result[0]['WO_DATE'], "d-M-y", "-",1); }
    $currency_rate=set_conversion_rate( $data_result[0]['CURRENCY_ID'], $conversion_date );
    $com_dtls = fnc_company_location_address($company, $data_result[0]['LOCATION_ID'], 1);
    $i = 0;
    $total_ammount = 0;
    ?>

	<div id="table_row" style="width:930px; margin-left:50px;">
    <table style="float:left" width="200">
    <td width="200px"> <img src="../../<? echo $image_location; ?>" height="89" width="89"></td>
    </table>
        <table style="float:right" align="right" cellspacing="0" width="700" >

            <tbody>
               
                <tr>
               
                    <td align="right" style="font-size:xx-large;" align="right"><strong><? echo $company_library[$company];  ?></strong></td>
                </tr>
                <tr>
                  
                    <td align="right"><strong><? echo $com_address;  ?></strong></td>
                </tr>
                <tr>
                  
                    <td align="right"><strong style="font-size:25px;"><?=$rpt_title;?></strong></td>
                </tr>
            </tbody>
        </table>
        <br>
        <table align="center" cellspacing="0" width="900" >
            <tbody>
                <tr>
                    <td width="150"><b>Work Order No</b></td>
                    <td width="150"><? echo $data_result[0]['WO_NUMBER']; ?></td>
                    <td width="150"><b>WO Date </b></td>
                    <td width="150"><? echo change_date_format($data_result[0]['WO_DATE']); ?></td>
                    <td width="150"><b>Pay Mood</b></td>
                    <td ><? echo $pay_mode[$data_result[0]['PAY_MODE']]; ?></td>
                </tr>
                <tr>
                    <td ><b>Supplier</b></td>
                    <td ><? echo $lib_supplier_name[$data_result[0]['SUPPLIER_ID']]; ?></td>
                    <td ><b>Currency</b></td>
                    <td ><? echo $currency[$data_result[0]['CURRENCY_ID']]; ?></td>
                    <td ><b>Exchange Rate </b></td>
                    <td ><? echo $currency_rate; ?></td>
                </tr>
                <tr>
                    <td ><b>Address</b></td>
                    <td ><? echo $lib_supplier_add[$data_result[0]['SUPPLIER_ID']]; ?></td>
                    <td ><b>Attention</b></td>
                    <td ><? echo $data_result[0]['ATTENTION']; ?></td>
                    <td ><b>Fixed Assets</b></td>
                    <td ><? echo $yes_no[$data_result[0]['FIXED_ASSET']]; ?></td>
                </tr>
                <tr>
                    <td ><b>Remarks</b></td>
                    <td colspan="5"><? echo $data_result[0]['REMARKS']; ?></td>
                </tr>
            </tbody>
        </table>
        <br>
        <table align="center" cellspacing="0" width="960"  border="1" rules="all" class="rpt_table" >
            <thead>
                <th width="80" >Service For</th>
                <th width="100" >Service Details</th>
                <th width="80" >Asset Number</th>
                <th width="120" >Item Description</th>
                <th width="100" >Item Category</th>
                <th width="100" >Item Group</th>
                <th width="40" >UOM</th>
                <th width="60" >Qnty</th>
                <th width="70" >Rate</th>
                <th width="80" >Amount</th>
                <th width="50" >Service Number</th>
                <th >Remarks</th>
            </thead>
            <tbody>
            <?

                 $sql_dtls= "SELECT a.id, a.service_for as SERVICE_FOR, a.service_details as SERVICE_DETAILS,a.supplier_order_quantity as WO_QNTY, a.gross_rate as RATE, a.gross_amount as AMOUNT, a.service_number as SERVICE_NUMBER, a.remarks as REMARKS, b.item_description as ITEM_DESCRIPTION, b.item_category_id as ITEM_CATEGORY_ID, b.item_group_id as ITEM_GROUP_ID, a.uom as UOM

                from wo_non_order_info_dtls a
                left join product_details_master b on a.item_id=b.id and b.status_active in(1,3)
                where a.mst_id=$mst_id and a.status_active=1 ";
                // echo $sql_dtls;
                $sql_result= sql_select($sql_dtls);
                $i=1;
                foreach($sql_result as $row)
                {
                    if ($i%2==0){ $bgcolor="#E9F3FF";}else{ $bgcolor="#FFFFFF";}
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td><? echo $service_for_arr[$row['SERVICE_FOR']]; ?></td>
                        <td align="center"><? echo $row['SERVICE_DETAILS']; ?></td>
                        <td align="center"><?echo $asset_no;?></td>
                        <td align="center"><? echo $row['ITEM_DESCRIPTION']; ?></td>
                        <td align="center"><? echo $item_category[$row["ITEM_CATEGORY_ID"]]; ?></td>
                        <td><? echo $item_name_arr[$row['ITEM_GROUP_ID']]; ?></td>
                        <td align="center"><?=$service_uom_arr[$row["UOM"]];?></td>
                        <td align="right"><? echo number_format($row['WO_QNTY'],2,".",""); ?></td>
                        <td align="right"><? echo number_format($row['RATE'],2,".",""); ?></td>
                        <td align="right"><? echo number_format($row['AMOUNT'],2,".",""); ?></td>
                        <td align="center"><? echo $row['SERVICE_NUMBER']; ?></td>
                        <td><? echo $row['REMARKS']; ?></td>
                    </tr>
                    <?php
                    $tot_wo_amount += $row['AMOUNT'];
                    $i++;
                }
                $grand_tot_wo_amount=$tot_wo_amount+$upcharge-$discount;
            ?>
                <tr>
                    <td colspan="8" >&nbsp;</td>
                    <td align="right" ><strong>Total:&nbsp;</strong></td>
                    <td align="right" ><? echo number_format($tot_wo_amount, 2, '.', ''); ?></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>



                <?
                    if($upcharge)
                    {
                        ?>
                            <tr>
                                <td align="left" colspan="2"  ><strong>Upcharge Remarks:&nbsp;</strong></td>
                                <td align="right"colspan="6" ><? echo $up_remark; ?></td>
                                <td align="right" ><strong>Upcharge:&nbsp;</strong></td>
                                <td align="right" ><? echo number_format($upcharge, 2, '.', ''); ?></td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                        <?
                    }
                    if($discount)
                    {
                        ?>
                            <tr>
                                <td align="left" colspan="2"  ><strong>Discount Remarks:&nbsp;</strong></td>
                                <td align="right"colspan="6" ><? echo $dis_remerk; ?></td>
                                <td align="right" ><strong>Discount:&nbsp;</strong></td>
                                <td align="right" ><? echo number_format($discount, 2, '.', ''); ?></td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                        <?
                    }
                    if($upcharge>0 || $discount>0)
                    {
                        ?>
                            <tr>
                                <td colspan="8" >&nbsp;</td>
                                <td align="right" ><strong>Net Total:&nbsp;</strong></td>
                                <td align="right" ><? echo number_format($grand_tot_wo_amount, 2, '.', ''); ?></td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                        <?
                    }
                ?>
                <tr>
                    <td colspan="12" ><strong>Amount in words:&nbsp;</strong><?echo number_to_words(number_format($grand_tot_wo_amount, 2, '.', ''),$currency[$carrency_id]);?></td>
                </tr>
            </tbody>
        </table>
        <?echo get_spacial_instruction($data_result[0]['WO_NUMBER'],"960px",484);?>
        <br/>
        <table align="center" cellspacing="0" width="960"  border="1" rules="all" class="rpt_table" >
            <thead>
                <tr>
                    <th width="30"><b>SL</b></th>
                    <th><b>Scope of Beneficiary</b></th>
                </tr>
            </thead>
            <tbody>
                <?
                    $i=1;
                    foreach($baneficaly as $value){
                ?>
                    <tr>
                        <td width="30" align="center"><? echo $i++;?></td>
                        <td style="padding-left:5px;"><? echo $value;?></td>
                    </tr>
                    <?
                        }
                    ?>
            </tbody>
        </table>
        <br>
        <table align="center" cellspacing="0" width="960"  border="1" rules="all" class="rpt_table" >
            <thead>
                <tr>
                    <th width="30" ><b>SL</b></th>
                    <th><b>Scope of Service Provider</b></th>
                </tr>
            </thead>
            <tbody>
                <?
                    $i=1;
                    foreach($service_provider as $val){
                ?>
                    <tr>
                        <td width="30" align="center"><? echo $i++;?></td>
                        <td style="padding-left:5px;"><? echo $val;?></td>
                    </tr>
                    <?
                        }
                    ?>
            </tbody>
        </table>
    <? echo signature_table(263, $data[0],"960px",$template_id,0,$user_lib_name[$inserted_by]); ?>
    <script type="text/javascript" src="../../js/jquery.js"></script>
    <?
    exit();
}


if ($action=="service_work_order_print_2")
{
    extract($_REQUEST);
	$data=explode('*',$data);
    $company=$data[0];
    $mst_id=$data[1];
    $rpt_title=$data[2];
    $template_id=$data[3];
    $lc_type=$data[4];
    $pay_term2=$data[5];
     
    echo load_html_head_contents($rpt_title,"../../", 1, 1, $unicode,'','')
    ?>
    <link rel="stylesheet" href="../../../css/style_common.css" type="text/css" />
    <?

	$company_library=return_library_array( "SELECT id, company_name from lib_company", "id", "company_name"  );
	$item_name_arr=return_library_array("SELECT id,item_name from lib_item_group", "id","item_name");
	$lib_supplier_name= return_library_array('SELECT id,supplier_name FROM lib_supplier','id','supplier_name');
	$lib_supplier_add = return_library_array('SELECT id,address_1 FROM lib_supplier','id','address_1');;
    $user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");
	// $lib_terms_condition=return_library_array( "select id, terms from lib_terms_condition where page_id=484 ",'id','terms');
	$sql_group=return_field_value("group_name","lib_group","is_deleted= 0 order by id desc","group_name");



    $sql_company=sql_select("SELECT id, company_name, company_short_name, plot_no, level_no, road_no, block_no, city, zip_code, contact_no, contract_person, bin_no, tin_number, group_id  from lib_company where status_active=1 and is_deleted=0 and id=$company");
    $com_name=$sql_company[0][csf("company_name")];
    $company_short_name=$sql_company[0][csf("company_short_name")];
    $plot_no=$sql_company[0][csf("plot_no")];
    $level_no=$sql_company[0][csf("level_no")];
    $road_no=$sql_company[0][csf("road_no")];
    $block_no=$sql_company[0][csf("block_no")];
    $city=$sql_company[0][csf("city")];
    $zip_code=$sql_company[0][csf("zip_code")];
    $contact_no=$sql_company[0][csf("contact_no")];
    $contract_person=$sql_company[0][csf("contract_person")];
    $bin_no=$sql_company[0][csf("bin_no")];
    $tin_number=$sql_company[0][csf("tin_number")];
    $group_id=$sql_company[0][csf("group_id")];

    $com_address='';
    if($plot_no !=''){ $com_address.=$plot_no;}
    if($level_no !=''){ $com_address.=", ".$level_no;}
    if($road_no !=''){ $com_address.=", ".$road_no;}
    if($block_no !=''){ $com_address.=", ".$block_no;}
    if($city !=''){ $com_address.=", ".$city;}
    if($zip_code !=''){ $com_address.=", ".$zip_code;}

    $data_sql="SELECT wo_number as WO_NUMBER, delivery_place,wo_date as WO_DATE, supplier_id as SUPPLIER_ID, pay_mode as PAY_MODE, currency_id as CURRENCY_ID, attention as ATTENTION, location_id as LOCATION_ID, fixed_asset as FIXED_ASSET, asset_no as ASSET_NO, inserted_by as INSERTED_BY, up_charge as UP_CHARGE, discount as DISCOUNT, REMARKS, UPCHARGE_REMARKS, DISCOUNT_REMARKS, REQUISITION_NO, QUOT_DATE, REFERENCE, SCOPE_SERVICE_PROVIDER, SCOPE_BENEFICIARY, ATTENTION_TO,PAYTERM_ID from wo_non_order_info_mst where id=$mst_id";
    // echo $data_sql; die;
    $data_result=sql_select($data_sql);
    $inserted_by=$data_result[0]['INSERTED_BY'];
    $asset_no=$data_result[0]['ASSET_NO'];
    $currency_id=$data_result[0]['CURRENCY_ID'];
    $upcharge=$data_result[0]['UP_CHARGE'];
    $discount=$data_result[0]['DISCOUNT'];
    $upcharge_remarks=$data_result[0]['UPCHARGE_REMARKS'];
    $discount_remarks=$data_result[0]['DISCOUNT_REMARKS'];
    $req_no=$data_result[0]['REQUISITION_NO'];
    $scope_service_provider=explode('__',$data_result[0]['SCOPE_SERVICE_PROVIDER']);
    $scope_baneficaly=explode('__',$data_result[0]['SCOPE_BENEFICIARY']);
    $work_order_no=$data_result[0]['WO_NUMBER'];
    $supplier_id=$data_result[0]['SUPPLIER_ID'];
    $location_id=$data_result[0]['LOCATION_ID'];
    $payterm=$data_result[0]['PAYTERM_ID'];
   

    if($db_type==0){ $conversion_date=change_date_format($data_result[0]['WO_DATE'], "Y-m-d", "-",1); }
    else { $conversion_date=change_date_format($data_result[0]['WO_DATE'], "d-M-y", "-",1); }
    $currency_rate=set_conversion_rate( $data_result[0]['CURRENCY_ID'], $conversion_date );
    $com_dtls = fnc_company_location_address($company, $data_result[0]['LOCATION_ID'], 1);
	$currency_sign_arr=array(1=>'৳',2=>'$',3=>'€',4=>'€',5=>'$',6=>'£',7=>'¥');


   list($name,$numbers)=explode(",",$data_result[0]['ATTENTION_TO']);

    $sql_supplier = sql_select("SELECT id,supplier_name,contact_no,country_id,web_site,email,address_1,address_2,address_3,address_4,contact_person FROM  lib_supplier WHERE id = $supplier_id");     
    $com_address1=return_field_value('address','lib_location',"COMPANY_ID=$company",'address' );

    $com_address=sql_select("select address from lib_location where company_id=$company and id=$location_id");
    $com_address=$com_address[0]['ADDRESS'];

    foreach($sql_supplier as $supplier_data)
    {
        $row_mst[csf('supplier_id')];
        //if($supplier_data[csf('address_1')]!='')$address_1 = $supplier_data[csf('address_1')].','.' ';else $address_1='';
        if($supplier_data[csf('address_1')]!='')$address_1 = $supplier_data[csf('address_1')];else $address_1='';
        //if($supplier_data[csf('address_2')]!='')$address_2 = $supplier_data[csf('address_2')].','.' ';else $address_2='';
        if($supplier_data[csf('address_2')]!='')$address_2 = $supplier_data[csf('address_2')];else $address_2='';
        if($supplier_data[csf('address_3')]!='')$address_3 = $supplier_data[csf('address_3')];else $address_3='';
        if($supplier_data[csf('address_4')]!='')$address_4 = $supplier_data[csf('address_4')];else $address_4='';
        if($supplier_data[csf('contact_no')]!='')$contact_no = $supplier_data[csf('contact_no')];else $contact_no='';
        if($supplier_data[csf('web_site')]!='')$web_site = $supplier_data[csf('web_site')];else $web_site='';
        if($supplier_data[csf('email')]!='')$email = $supplier_data[csf('email')];else $email='';
        if($supplier_data[csf('contact_person')]!='')$contact_person = $supplier_data[csf('contact_person')];else $contact_person='';
        //if($supplier_data[csf('country_id')]!=0)$country = $supplier_data[csf('country_id')].','.' ';else $country='';
        $country = $supplier_data['country_id'];
        $supplier_address = $address_1;
        $supplier_address2 = $address_2;
        $supplier_country =$country;
        $supplier_phone =$contact_no;
        $supplier_email = $email;
        $supplier_contact_person = $contact_person;
    }

    $sql_select=sql_select("select terms from wo_booking_terms_condition where entry_form=484 and booking_no='$work_order_no'");

    $sql_group=return_field_value("group_name","lib_group","id=$group_id",'group_name');

    $i = 0;
    $total_ammount = 0;
	$sql_data = sql_select("SELECT REQU_NO, DIVISION_ID, DEPARTMENT_ID, SECTION_ID FROM INV_PURCHASE_REQUISITION_MST WHERE ID =$req_no");

    $lib_division=return_library_array( "SELECT id, division_name from lib_division", "id", "division_name"  );
    $lib_department=return_library_array( "SELECT id, department_name from lib_department", "id", "department_name"  );
    $lib_section=return_library_array( "SELECT id, section_name from lib_section", "id", "section_name"  );
    foreach($sql_data as $row){
        $req_no=$row["REQU_NO"];

        $division_id=$row["DIVISION_ID"];
        $department_id=$row["DEPARTMENT_ID"];
        $section_id=$row["SECTION_ID"];
    }

    ?>
    <style>
        .border {
            border: 1px solid black;
            margin-top:1px solid black ;
            border-collapse: collapse;
            padding: 1px;
            }
            body{
            margin-left:10px;
        }
        th, td {
            padding: 2px;
            font-size: 15px;
            }
            div{
                align-content: center;
            }
    </style>

	<div id="table_row" align="center" style="width:960px;">
        <table align="center" cellspacing="0" width="950" >
            <tbody>
                <tr>
                <td rowspan="2" width='120' ><img src='../../<? echo $com_dtls[2]; ?>' height='70' width='120' align="middle" /></td>
                    <td style="font-size:xx-large;" align="center"><strong><? echo $sql_group;  ?></strong></td>
                </tr>
                <!-- <tr>
                    <td align="center"><strong><?// echo $com_address;  ?></strong></td>
                </tr> -->
                <tr>
                    <td align="center"><strong style="font-size:25px;"><? echo "Work Order"//$rpt_title; ?></strong></td>
                </tr>
            </tbody>
        </table>
        <br>
        <table align="center" cellspacing="0" width="1050" >
            <tbody>
                <tr>
                    <td colspan="5"><b><? echo $company_library[$company];  ?></b></td>
                    <td class="border" width="120" ><b>Order Ref:</b></td>
                    <td class="border"><b><? echo $data_result[0]['WO_NUMBER']; ?></b></td>
                </tr>
                <tr>
                    <td width="80" ><b>Address:</b></td>
                    <td align="left"width="200" colspan="4"><? echo $com_address; ?></td>
                    <td class="border" ><b>Order Date:</b></td>
                    <td class="border" ><? echo $data_result[0]['WO_DATE']; ?></td>
                </tr>
                <tr>
                    <td ><b>BIN:</b></td>
                    <td align="left" colspan="4"><? echo $bin_no; ?></td>

                    <td class="border"><b>Service Req No</b></td>
                    <td class="border"><? echo  $req_no; ?></td>
                </tr>
                <tr>
                    <td ><b>TIN:</b></td>
                    <td align="left" colspan="4"><?echo $tin_number;  ?></td>

                    <td class="border" rowspan="2"><b>Quotation Ref:</b></td>
                    <td class="border" rowspan="2"><? echo $data_result[0]['REFERENCE']; ?></td>
                </tr>
                <tr>
                    <td colspan="2"><strong>Delivery Addr.:</strong> <? echo  $data_result[0]['DELIVERY_PLACE']; ?><br>         
                </tr>
                <tr>
                    <td ><b>Contact:</b></td>
                    <td align="left" colspan="4"><? echo $data_result[0]['ATTENTION']; //$contract_person ?></td>

                    <td class="border"><b>Quotation Date:</b></td>
                    <td class="border"><? echo $data_result[0]['QUOT_DATE']; ?></td>
                </tr><tr>
                    <td ><b>Supplier:</b></td>
                    <td align="left" colspan="4"><b><? echo $lib_supplier_name[$supplier_id]; ?></b></td>

                    <td class="border"><b>L/C /Payment Terms</b></td>
                    <td class="border"><? $lcDataArray=[4=>'TT/Pay Order',5=>'FDD/RTGS',6=>'FTT']; 
                                          echo $pay_term[$pay_term2]; 
                                          if($lc_type>0 && $pay_term2!=0){
                                          echo" &nbsp;/ &nbsp;".$lcDataArray[$lc_type];}
                                          else{
                                            echo"  &nbsp;".$lcDataArray[$lc_type];
                                          } ?></td>
                                          
                </tr><tr>
                    <td ><b>Address:</b></td>
                    <td align="left" colspan="4"><? echo $supplier_address; ?></td>

                    <td class="border"><b>Currency</b></td>
                    <td class="border"><? echo $currency[$data_result[0]['CURRENCY_ID']]; ?></td>
                </tr>
                <tr>
                    <td ><b>Attention:</b></td>
                    <td colspan="4"><? echo $name; ?></td>

                    <td class="border"><b>For Division</b></td>
                    <td class="border"><? echo $lib_division[$division_id]; ?></td>
                </tr>
                <tr>
                    <td ><b>Contact No:</b></td>
                    <td colspan="4"><? echo $numbers; ?></td>

                    <td class="border"><b>For Department</b></td>
                    <td class="border"><? echo $lib_department[$department_id]; ?></td>
                </tr>
                <tr>
                    <td ><b>&nbsp;</b></td>
                    <td colspan="4">&nbsp;</td>

                    <td class="border"><b>For Section</b></td>
                    <td class="border"><? echo $lib_section[$section_id]; ?></td>
                </tr>
            </tbody>
        </table>
        <br>
        <table>
            <tr>
               <td width="950" style="font-size: 20px;"  align="left" colspan="6"><b> Please confirm our order with below information:</b></td>
            </tr>
        </table>
        <table align="center" cellspacing="0" width="1050"  border="1" rules="all" class="rpt_table" >
            <thead>
                <th align="center" width="40" ><b>Sl. No.</b></th>
                <th align="center" width="300" ><b>Description of Works</b></th>
                <th align="center" width="300" ><b>Declaration Details</b></th>
                <th width="60" ><b>UOM</b></th>
                <th width="80" ><b>Qnty</b></th>
                <th width="90" ><b>Unit Rate</b></th>
                <th width="120" ><b>Total Value</b></th>
            </thead>
            <tbody>
            <?

                 $sql_dtls= "SELECT a.id, a.service_for as SERVICE_FOR, a.service_details as SERVICE_DETAILS,a.supplier_order_quantity as WO_QNTY, a.gross_rate as RATE, a.gross_amount as AMOUNT, a.service_number as SERVICE_NUMBER, a.remarks as REMARKS, b.item_description as ITEM_DESCRIPTION, b.item_category_id as ITEM_CATEGORY_ID, b.item_group_id as ITEM_GROUP_ID, a.uom as UOM
                from wo_non_order_info_dtls a
                left join product_details_master b on a.item_id=b.id and b.status_active in(1,3)
                where a.mst_id=$mst_id and a.status_active=1 ";
                // echo $sql_dtls;
                $sql_result= sql_select($sql_dtls);
                $i=1;
                foreach($sql_result as $row)
                {
                    if ($i%2==0){ $bgcolor="#E9F3FF";}else{ $bgcolor="#FFFFFF";}
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td align="center"><? echo  $i; ?></td>
                        <td align="left"><? echo $row['SERVICE_DETAILS']; ?></td>
                        <td align="left"><? echo $row['REMARKS']; ?></td>
                        <td align="center"><?=$service_uom_arr[$row["UOM"]];?></td>
                        <td align="right"><? echo number_format($row['WO_QNTY'],2,".",""); ?></td>
                        <td align="right"><? echo number_format($row['RATE'],4,".",""); ?></td>
                        <td align="right"><? echo number_format($row['AMOUNT'],4); ?></td>
                    </tr>
                    <?php
                    $tot_wo_amount += $row['AMOUNT'];
                    $i++;
                }
                $grand_tot_amount=$tot_wo_amount + $upcharge - $discount;
                if($discount_remarks){$rowspan1=1;}else{$rowspan1=2;}
                if($upcharge_remarks){$rowspan2=1; }else{$rowspan2=2;}
                $rowspan3 = 1;
                 ?>
                 <tr >
                    <? if($rowspan1 == 2  && $rowspan2 == 2){  $rowspan3++; ?> <td align="left" colspan="4" rowspan="4"></td> <? }else{ ?> <td align="left" colspan="4" rowspan="<?=$rowspan1?>"><strong></strong></td> <? } ?>

                    <td align="right" colspan="2" ><strong>Total Items Value</strong></td>
                    <td align="right"><? echo number_format($tot_wo_amount,4); ?></td>
                </tr>
                <tr >
                    <? if($rowspan1 == 1){ ?> <td align="left" colspan="3" >&nbsp;&nbsp;<strong><? echo $discount_remarks; ?> </strong></td> <? } ?>
                    <td align="right" colspan="2" ><strong>Discount</strong></td>
                    <td align="right"><? echo number_format($discount,4); ?></td>
                </tr>
                <tr >
                    <? if($rowspan2 == 1){ ?> <td align="left" colspan="3" >&nbsp;&nbsp;<strong><? echo $upcharge_remarks; ?></strong></td> <? }elseif($rowspan1 != 2  && $rowspan2 == 2){ ?> <td align="left" colspan="3" rowspan="<?=$rowspan2?>"><strong></strong></td> <? } ?>
                    <td align="right" colspan="2"><strong>PO Charge</strong></td>
                    <td align="right"><? echo number_format($upcharge,4); ?></td>
                </tr>
                <tr >
                    <? if($rowspan3 == 1 && $rowspan2 == 1){ ?> <td align="left" colspan="3" rowspan="<?=$rowspan2?>"><strong></strong></td> <? } ?>
                    <td align="right" colspan="2" style="font-size:15pt;"><strong>Total Amount </strong></td>
                    <td align="right" style="font-size:15pt;"><strong><? echo $currency_sign_arr[$currency_id]." ". number_format($grand_tot_amount,4); ?></strong></td>
                </tr>
                <tr>
                    <td align="left" colspan="6"  ><b><? echo "In World: ".$currency[$currency_id]." ".number_to_words($grand_tot_amount); ?> Only</b></td>
                </tr>
            </tbody>
        </table>
        <br>
        <?
        $scope_service_provider_arr=array();$scope_baneficaly_arr=array();
        foreach($scope_service_provider as $val)
        {
            if($val){$scope_service_provider_arr[]=$val;}
        }
        foreach($scope_baneficaly as $val)
        {
            if($val){$scope_baneficaly_arr[]=$val;}
        }

        if(count($scope_baneficaly_arr)>0)
        {
            ?>
            <table align="center" cellspacing="0" width="950">
                <tbody>
                    <tr>
                        <td><b><u> Scope of Beneficiary:</u></b></td>
                    </tr>
                    <?
                        $i=1;

                        foreach($scope_baneficaly_arr as $val){
                        ?>
                        <tr>
                            <td><? echo $i++;?>. <span><? echo $val;?></span></td>
                        </tr>
                        <?
                        }
                    ?>
                </tbody>
            </table>
            <?
        }
        ?>
        <br>
        <?
        if(count($scope_service_provider_arr)>0)
        {
            ?>
            <table align="center" cellspacing="0" width="950">
                <tbody>
                    <tr>
                        <td><b><u> Scope of Service Provider:</u></b></td>
                    </tr>
                    <?
                        $i=1;
                        foreach($scope_service_provider_arr as $val){
                        ?>
                        <tr>
                            <td><? echo $i++;?>. <span><? echo $val;?></span></td>
                        </tr>
                        <?
                        }
                    ?>
                </tbody>
            </table>
            <?
        }
        ?>
        <br>
        <table align="center" cellspacing="0" width="950">
            <tbody>
            <tr>
                <td><b><u>Terms & Conditions:</u></b></td>
            </tr>
            <?
                $i=1;
                foreach($sql_select as $tearms){
                $treams_condi=$tearms[csf("terms")];
                ?>
                <tr>
                <td><? echo $i++;?>.<span><?echo $treams_condi;?></span> </td>
                </tr>
                <?
            }
            ?>
            <tr>
        </tbody>
        </table>

    </div>
        <!-- <? //echo get_spacial_instruction($work_order_no,"900px", 484);?> -->

    <? echo signature_table(263, $data[0],"960px",$template_id,40,$user_lib_name[$inserted_by]); ?>
    <script type="text/javascript" src="../../js/jquery.js"></script>
    <?
    exit();
}

if ($action=="service_work_order_print_3")
{
    extract($_REQUEST);
	$data=explode('*',$data);
    $company=$data[0];
    $mst_id=$data[1];
    $rpt_title=$data[2];
    $template_id=$data[3];
    echo load_html_head_contents($rpt_title,"../../", 1, 1, $unicode,'','')
    ?>
    <link rel="stylesheet" href="../../../css/style_common.css" type="text/css" />
    <?

	$company_library=return_library_array( "SELECT id, company_name from lib_company", "id", "company_name"  );
	$item_name_arr=return_library_array("SELECT id,item_name from lib_item_group", "id","item_name");
	$lib_supplier_name= return_library_array('SELECT id,supplier_name FROM lib_supplier','id','supplier_name');
	$lib_supplier_add = return_library_array('SELECT id,address_1 FROM lib_supplier','id','address_1');;
    $user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");

    $sql_company=sql_select("SELECT id, company_name, company_short_name, plot_no, level_no, road_no, block_no, city, zip_code from lib_company where status_active=1 and is_deleted=0 and id=$company");
    $com_name=$sql_company[0][csf("company_name")];
    $company_short_name=$sql_company[0][csf("company_short_name")];
    $plot_no=$sql_company[0][csf("plot_no")];
    $level_no=$sql_company[0][csf("level_no")];
    $road_no=$sql_company[0][csf("road_no")];
    $block_no=$sql_company[0][csf("block_no")];
    $city=$sql_company[0][csf("city")];
    $zip_code=$sql_company[0][csf("zip_code")];

    $com_address='';
    if($plot_no !=''){ $com_address.=$plot_no;}
    if($level_no !=''){ $com_address.=", ".$level_no;}
    if($road_no !=''){ $com_address.=", ".$road_no;}
    if($block_no !=''){ $com_address.=", ".$block_no;}
    if($city !=''){ $com_address.=", ".$city;}
    if($zip_code !=''){ $com_address.=", ".$zip_code;}

    $data_sql="SELECT wo_number as WO_NUMBER, wo_date as WO_DATE, supplier_id as SUPPLIER_ID, pay_mode as PAY_MODE, currency_id as CURRENCY_ID, attention as ATTENTION, location_id as LOCATION_ID, fixed_asset as FIXED_ASSET, asset_no as ASSET_NO, inserted_by as INSERTED_BY, up_charge as UP_CHARGE, discount as DISCOUNT, REMARKS, UPCHARGE_REMARKS, DISCOUNT_REMARKS,SCOPE_SERVICE_PROVIDER, SCOPE_BENEFICIARY from wo_non_order_info_mst where id=$mst_id";
    //echo $data_sql;
    $data_result=sql_select($data_sql);
    $inserted_by=$data_result[0]['INSERTED_BY'];
    $asset_no=$data_result[0]['ASSET_NO'];
    $carrency_id=$data_result[0]['CURRENCY_ID'];
    $upcharge=$data_result[0]['UP_CHARGE'];
    $discount=$data_result[0]['DISCOUNT'];
    $up_remark=$data_result[0]['UPCHARGE_REMARKS'];
    $dis_remerk=$data_result[0]['DISCOUNT_REMARKS'];
    $scope_service_provider=$data_result[0]['SCOPE_SERVICE_PROVIDER'];
    $service_provider=explode('__',$scope_service_provider);
    $scope_baneficaly=$data_result[0]['SCOPE_BENEFICIARY'];
    $baneficaly=explode('__',$scope_baneficaly);
    if($db_type==0){ $conversion_date=change_date_format($data_result[0]['WO_DATE'], "Y-m-d", "-",1); }
    else { $conversion_date=change_date_format($data_result[0]['WO_DATE'], "d-M-y", "-",1); }
    $currency_rate=set_conversion_rate( $data_result[0]['CURRENCY_ID'], $conversion_date );
    $com_dtls = fnc_company_location_address($company, $data_result[0]['LOCATION_ID'], 1);
    $i = 0;
    $total_ammount = 0;
    ?>

	<div id="table_row" style="width:930px; margin-left:50px;">
        <table align="center" cellspacing="0" width="900" >
            <tbody>
                <tr>
                    <td style="font-size:xx-large;" align="center"><strong><? echo $company_library[$company];  ?></strong></td>
                </tr>
                <tr>
                    <td align="center"><strong><? echo $com_address;  ?></strong></td>
                </tr>
                <tr>
                    <td align="center"><strong style="font-size:25px;"><?=$rpt_title;?></strong></td>
                </tr>
            </tbody>
        </table>
        <br>
        <table align="center" cellspacing="0" width="900" >
            <tbody>
                <tr>
                    <td width="150"><b>Work Order No</b></td>
                    <td width="150"><? echo $data_result[0]['WO_NUMBER']; ?></td>
                    <td width="150"><b>WO Date </b></td>
                    <td width="150"><? echo change_date_format($data_result[0]['WO_DATE']); ?></td>
                    <td width="150"><b>Pay Mood</b></td>
                    <td ><? echo $pay_mode[$data_result[0]['PAY_MODE']]; ?></td>
                </tr>
                <tr>
                    <td ><b>Supplier</b></td>
                    <td ><? echo $lib_supplier_name[$data_result[0]['SUPPLIER_ID']]; ?></td>
                    <td ><b>Currency</b></td>
                    <td ><? echo $currency[$data_result[0]['CURRENCY_ID']]; ?></td>
                    <td ><b>Exchange Rate </b></td>
                    <td ><? echo $currency_rate; ?></td>
                </tr>
                <tr>
                    <td ><b>Address</b></td>
                    <td ><? echo $lib_supplier_add[$data_result[0]['SUPPLIER_ID']]; ?></td>
                    <td ><b>Attention</b></td>
                    <td ><? echo $data_result[0]['ATTENTION']; ?></td>
                    <td ><b>Fixed Assets</b></td>
                    <td ><? echo $yes_no[$data_result[0]['FIXED_ASSET']]; ?></td>
                </tr>
                <tr>
                    <td ><b>Remarks</b></td>
                    <td colspan="5"><? echo $data_result[0]['REMARKS']; ?></td>
                </tr>
            </tbody>
        </table>
        <br>
        <table align="center" cellspacing="0" width="960"  border="1" rules="all" class="rpt_table" >
            <thead>
                <th width="80" >Service For</th>
                <th width="100" >Service Details</th>
                <th width="80" >Asset Number</th>
                <th width="120" >Item Description</th>
                <th width="100" >Item Category</th>
                <th width="100" >Item Group</th>
                <th width="40" >UOM</th>
                <th width="60" >Qnty</th>
                <th width="70" >Rate</th>
                <th width="80" >Amount</th>
                <th width="50" >Service Number</th>
                <th >Remarks</th>
            </thead>
            <tbody>
            <?

                 $sql_dtls= "SELECT a.id, a.service_for as SERVICE_FOR, a.service_details as SERVICE_DETAILS,a.supplier_order_quantity as WO_QNTY, a.gross_rate as RATE, a.gross_amount as AMOUNT, a.service_number as SERVICE_NUMBER, a.remarks as REMARKS, b.item_description as ITEM_DESCRIPTION, b.item_category_id as ITEM_CATEGORY_ID, b.item_group_id as ITEM_GROUP_ID, a.uom as UOM

                from wo_non_order_info_dtls a
                left join product_details_master b on a.item_id=b.id and b.status_active in(1,3)
                where a.mst_id=$mst_id and a.status_active=1 ";
                // echo $sql_dtls;
                $sql_result= sql_select($sql_dtls);
                $i=1;
                foreach($sql_result as $row)
                {
                    if ($i%2==0){ $bgcolor="#E9F3FF";}else{ $bgcolor="#FFFFFF";}
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td><? echo $service_for_arr[$row['SERVICE_FOR']]; ?></td>
                        <td align="center"><? echo $row['SERVICE_DETAILS']; ?></td>
                        <td align="center"><?echo $asset_no;?></td>
                        <td align="center"><? echo $row['ITEM_DESCRIPTION']; ?></td>
                        <td align="center"><? echo $item_category[$row["ITEM_CATEGORY_ID"]]; ?></td>
                        <td><? echo $item_name_arr[$row['ITEM_GROUP_ID']]; ?></td>
                        <td align="center"><?=$service_uom_arr[$row["UOM"]];?></td>
                        <td align="right"><? echo number_format($row['WO_QNTY'],2,".",""); ?></td>
                        <td align="right"><? echo number_format($row['RATE'],2,".",""); ?></td>
                        <td align="right"><? echo number_format($row['AMOUNT'],2,".",""); ?></td>
                        <td align="center"><? echo $row['SERVICE_NUMBER']; ?></td>
                        <td><? echo $row['REMARKS']; ?></td>
                    </tr>
                    <?php
                    $tot_wo_amount += $row['AMOUNT'];
                    $i++;
                }
                $grand_tot_wo_amount=$tot_wo_amount+$upcharge-$discount;
            ?>
                <tr>
                    <td colspan="8" >&nbsp;</td>
                    <td align="right" ><strong>Total:&nbsp;</strong></td>
                    <td align="right" ><? echo number_format($tot_wo_amount, 2, '.', ''); ?></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>



                <?
                    if($upcharge)
                    {
                        ?>
                            <tr>
                                <td align="left" colspan="2"  ><strong>Upcharge Remarks:&nbsp;</strong></td>
                                <td align="right"colspan="6" ><? echo $up_remark; ?></td>
                                <td align="right" ><strong>Upcharge:&nbsp;</strong></td>
                                <td align="right" ><? echo number_format($upcharge, 2, '.', ''); ?></td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                        <?
                    }
                    if($discount)
                    {
                        ?>
                            <tr>
                                <td align="left" colspan="2"  ><strong>Discount Remarks:&nbsp;</strong></td>
                                <td align="right"colspan="6" ><? echo $dis_remerk; ?></td>
                                <td align="right" ><strong>Discount:&nbsp;</strong></td>
                                <td align="right" ><? echo number_format($discount, 2, '.', ''); ?></td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                        <?
                    }
                    if($upcharge>0 || $discount>0)
                    {
                        ?>
                            <tr>
                                <td colspan="8" >&nbsp;</td>
                                <td align="right" ><strong>Net Total:&nbsp;</strong></td>
                                <td align="right" ><? echo number_format($grand_tot_wo_amount, 2, '.', ''); ?></td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                        <?
                    }
                ?>
                <tr>
                    <td colspan="12" ><strong>Amount in words:&nbsp;</strong><?echo number_to_words(number_format($grand_tot_wo_amount, 2, '.', ''),$currency[$carrency_id]);?></td>
                </tr>
            </tbody>
        </table>
        <?echo get_spacial_instruction($data_result[0]['WO_NUMBER'],"960px",484);?>
        <br/>

    <? echo signature_table(263, $data[0],"960px",$template_id,0,$user_lib_name[$inserted_by]); ?>
    <script type="text/javascript" src="../../js/jquery.js"></script>
    <?
    exit();
}



if ($action=="service_work_order_po_print")
{
    extract($_REQUEST);
	$data=explode('*',$data);
    $company=$data[0];
    $mst_id=$data[1];
    $rpt_title=$data[2];
    $template_id=$data[3];
    echo load_html_head_contents($rpt_title,"../../", 1, 1, $unicode,'','')
    ?> <link rel="stylesheet" href="../../../css/style_common.css" type="text/css" /> <?

    $currency_sign_arr=array(1=>'৳',2=>'$',3=>'€',4=>'€',5=>'$',6=>'£',7=>'¥');
	$company_library=return_library_array( "SELECT id, company_name from lib_company", "id", "company_name"  );
    $user_lib_name=return_library_array("SELECT id,user_full_name from user_passwd", "id", "user_full_name");

    // $sql_company=sql_select("SELECT id, company_name, company_short_name, plot_no, level_no, road_no, block_no, city, zip_code,contact_no from lib_company where status_active=1 and is_deleted=0 and id=$company");
    // $com_name=$sql_company[0][csf("company_name")];
    // $company_short_name=$sql_company[0][csf("company_short_name")];
    // $plot_no=$sql_company[0][csf("plot_no")];
    // $level_no=$sql_company[0][csf("level_no")];
    // $road_no=$sql_company[0][csf("road_no")];
    // $block_no=$sql_company[0][csf("block_no")];
    // $city=$sql_company[0][csf("city")];
    // $zip_code=$sql_company[0][csf("zip_code")];
    // $phone_no=$sql_company[0][csf("contact_no")];

    // $com_address='';
    // if($plot_no !=''){ $com_address.=$plot_no;}
    // if($level_no !=''){ $com_address.=", ".$level_no;}
    // if($road_no !=''){ $com_address.=", ".$road_no;}
    // if($block_no !=''){ $com_address.=", ".$block_no;}
    // if($city !=''){ $com_address.=", ".$city;}
    // if($zip_code !=''){ $com_address.=", ".$zip_code;}

    $data_sql="SELECT a.wo_number as WO_NUMBER, a.wo_date as WO_DATE,a.location_id as LOCATION_ID, a.supplier_id as SUPPLIER_ID, a.pay_mode as PAY_MODE, a.currency_id as CURRENCY_ID, a.attention as ATTENTION,a.is_approved as IS_APPROVED, a.delivery_date as DELIVERY_DATE, a.inserted_by as INSERTED_BY, a.up_charge as UP_CHARGE, a.discount as DISCOUNT , to_char(a.insert_date, 'DD-MM-YYYY HH:MI:SS AM') as INSERT_DATE, a.upcharge_remarks as UPCHARGE_REMARKS, a.discount_remarks as DISCOUNT_REMARKS, b.user_full_name as USER_FULL_NAME, c.custom_designation as CUSTOM_DESIGNATION
    from wo_non_order_info_mst a
    left join user_passwd b on b.id = a.inserted_by
    left join lib_designation c on c.id = b.designation
    where a.id=$mst_id";
    // echo $data_sql;
    $data_result=sql_select($data_sql);
    $inserted_by=$data_result[0]['INSERTED_BY'];
    $carrency_id=$data_result[0]['CURRENCY_ID'];
    $location_id=$data_result[0]['LOCATION_ID'];
    $is_approved=$data_result[0]['IS_APPROVED'];
    $upcharge=$data_result[0]['UP_CHARGE'];
    $discount=$data_result[0]['DISCOUNT'];
    if($is_approved==1){ $approved_status="Full Approved";}
    else if($is_approved==3){ $approved_status=$is_approved[$is_approved];}
    else{ $approved_status="Not Approved";}

    $supplier_sql=sql_select("SELECT SUPPLIER_NAME,ADDRESS_1,CONTACT_PERSON,CONTACT_NO,EMAIL from lib_supplier where id=".$data_result[0]['SUPPLIER_ID']);
    $location_add=sql_select("SELECT ADDRESS,CONTACT_NO from lib_location where id=$location_id");
    $getReqNumber = sql_select("select requisition_no from wo_non_order_info_mst where id = $mst_id");
    if(count($getReqNumber) > 0){
        $req_numbers_id =  $getReqNumber[0][csf('requisition_no')];
    }else{
        $req_numbers_id = 0;
    }

    $req_details_info_sql = sql_select("select inv_purchase_requisition_mst.id AS ID, inv_purchase_requisition_mst.requ_no AS REQU_NO, lib_store_location.store_name AS STORE_NAME from inv_purchase_requisition_mst LEFT JOIN lib_store_location ON lib_store_location.id = inv_purchase_requisition_mst.store_name WHERE inv_purchase_requisition_mst.id IN ($req_numbers_id)");

    $req_numbers_container = [];
    foreach ($req_details_info_sql as $req_key => $req_data){
        $req_numbers_container[$req_data['REQU_NO']] = $req_data['STORE_NAME'];
    }

    $electronic_sequence_arr = [];
    $get_electronic_sequence_sql = sql_select("select sequence_no as sequence_no from electronic_approval_setup where ENTRY_FORM = 60 and company_id=$company and is_deleted = 0 order by sequence_no asc");
    foreach ($get_electronic_sequence_sql as $sequence){
        $electronic_sequence_arr[] = $sequence['SEQUENCE_NO'];
    }

    $sql_get_checked_user = sql_select("SELECT user_passwd.user_full_name as USER_FULL_NAME, lib_designation.custom_designation as CUSTOM_DESIGNATION, to_char(approval_history.approved_date, 'DD-MM-YYYY HH:MI:SS AM') as APPROVED_DATE from approval_history left join user_passwd on user_passwd.id = approval_history.approved_by left join lib_designation on lib_designation.id = user_passwd.designation where approval_history.entry_form = 60 and approval_history.mst_id = $mst_id and approval_history.sequence_no in(".implode(',', $electronic_sequence_arr).") and approval_history.id = (SELECT max(id) from approval_history where entry_form = 60 and mst_id = $mst_id and sequence_no = ".min($electronic_sequence_arr).") and rownum = 1 and (select max(CURRENT_APPROVAL_STATUS) from approval_history where entry_form = 60 and mst_id = $mst_id) = 1 order by approval_history.approved_no asc");
    $sql_get_approved_user = sql_select("select user_passwd.user_full_name as USER_FULL_NAME, lib_designation.custom_designation as CUSTOM_DESIGNATION, to_char(approval_history.approved_date, 'dd-mm-yyyy hh:mi:ss am') as APPROVED_DATE from approval_history inner join wo_non_order_info_mst on wo_non_order_info_mst.id = approval_history.mst_id left join user_passwd on user_passwd.id = approval_history.approved_by left join lib_designation on lib_designation.id = user_passwd.designation where mst_id = $mst_id and approval_history.entry_form = 60 and wo_non_order_info_mst.is_approved = 1 and approval_history.current_approval_status = 1 and approval_history.sequence_no =".max($electronic_sequence_arr));

    ?>

	<div style="width:1010px;">
        <table align="center" cellspacing="0" width="980" >
            <tbody>
                <tr>
                    <td style="font-size:24px;" ><strong><? echo $company_library[$company];  ?></strong></td>
                    <td align="right"><strong style="font-size:24px; padding-right:50px;"><?=$rpt_title;?></strong></td>
                </tr>
                <tr>
                    <td style="font-size:20px;" ><? echo $location_add[0]['ADDRESS']; ?></td>
                    <td></td>
                </tr>
                <tr>
                    <td style="font-size:20px;" ><? echo "Phone No: ".$location_add[0]['CONTACT_NO']; ?></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
        <br>
        <table align="center" cellspacing="0" width="980" >
            <tbody>
                <tr>
                    <td colspan="3" style="font-size:20px;border-bottom: 1px solid black;" ><strong>Supplier's Details</strong></td>
                    <td ></td>
                    <td style="font-size:20px;border-bottom: 1px solid black;" ><strong>Delivery Address</strong></td>
                </tr>
                <tr>
                    <td width="150" style="font-size:18px;" >Company Name</td>
                    <td width="20" >:</td>
                    <td width="250" style="font-size:18px;"><?=$supplier_sql[0]['SUPPLIER_NAME'];?></td>
                    <td width="180" align="center" rowspan="5" style="font-size:18px;color:red;"><?=$approved_status;?> </td>
                    <td rowspan="5" ></td>
                </tr>
                <tr>
                    <td style="font-size:18px;" >Contact Person</td>
                    <td >:</td>
                    <td style="font-size:18px;"><?=$supplier_sql[0]['CONTACT_PERSON'];?></td>
                </tr>
                <tr>
                    <td style="font-size:18px;" >Address</td>
                    <td >:</td>
                    <td style="font-size:18px;"><?=$supplier_sql[0]['ADDRESS_1'];?></td>
                </tr>
                <tr>
                    <td style="font-size:18px;" >Cell</td>
                    <td >:</td>
                    <td style="font-size:18px;"><?=$supplier_sql[0]['CONTACT_NO'];?></td>
                </tr>
                <tr>
                    <td style="font-size:18px;" >Email</td>
                    <td >:</td>
                    <td style="font-size:18px;"><?=$supplier_sql[0]['EMAIL'];?></td>
                </tr>
            </tbody>
        </table>
        <br>
        <div style="border: 1px solid black; width:980px;">
            <table align="center"  cellspacing="0" width="850" >
                <tbody>
                    <tr>
                        <td width="150" style="font-size:18px;"><b>REQ Number</b></td>
                        <td width="20" >:</td>
                        <td width="250" style="font-size:18px;"><?=implode(', ', array_keys($req_numbers_container))?></td>
                        <td width="150" style="font-size:18px;"><b>Delivery Date </b></td>
                        <td width="20" >:</td>
                        <td style="font-size:18px;"><? echo change_date_format($data_result[0]['DELIVERY_DATE']); ?></td>
                    </tr>
                    <tr>
                        <td style="font-size:18px;"><b>Order Number</b></td>
                        <td width="20" >:</td>
                        <td style="font-size:18px;"><? echo $data_result[0]['WO_NUMBER']; ?></td>
                        <td style="font-size:18px;"><b>Pay Mode</b></td>
                        <td width="20" >:</td>
                        <td style="font-size:18px;"><? echo $pay_mode[$data_result[0]['PAY_MODE']]; ?></td>
                    </tr>
                    <tr>
                        <td style="font-size:18px;"><b>Order Date</b></td>
                        <td width="20" >:</td>
                        <td style="font-size:18px;"><? echo change_date_format($data_result[0]['WO_DATE']); ?></td>
                        <td style="font-size:18px;"><b>Currency</b></td>
                        <td width="20" >:</td>
                        <td style="font-size:18px;"><? echo $currency[$data_result[0]['CURRENCY_ID']]; ?></td>
                    </tr>
                    <tr>
                        <td style="font-size:18px;"><b>Notes</b></td>
                        <td width="20" >:</td>
                        <td style="font-size:18px;"><? //echo $data_result[0]['ATTENTION']; ?></td>
                        <td style="font-size:18px;"><b>Warehouse</b></td>
                        <td width="20" >:</td>
                        <td style="font-size:18px;"><?=implode(', ', array_unique(array_values($req_numbers_container)))?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <table align="left" cellspacing="0" width="980"  border="1" rules="all" class="rpt_table" >
            <thead>
                <tr></tr>
                <th width="30" style="font-size:18px;">SL</th>
                <th width="180" style="font-size:18px;" >Service Details</th>
                <th width="180" style="font-size:18px;" >Item Description</th>
                <th width="80" style="font-size:18px;" >UOM</th>
                <th width="80" style="font-size:18px;">Qty</th>
                <th width="80" style="font-size:18px;">Unit Price</th>
                <th width="100" style="font-size:18px;">Amount</th>
                <th style="font-size:18px;">Remarks</th>
            </thead>
            <tbody>
                <?
                    $sql_dtls= "SELECT a.id, a.service_details as SERVICE_DETAILS,a.supplier_order_quantity as WO_QNTY, a.gross_rate as RATE, a.gross_amount as AMOUNT, a.remarks as REMARKS, b.item_description as ITEM_DESCRIPTION

                    from wo_non_order_info_dtls a
                    left join product_details_master b on a.item_id=b.id and b.status_active in(1,3)
                    where a.mst_id=$mst_id and a.status_active=1 ";
                    // echo $sql_dtls; die;
                    $sql_result= sql_select($sql_dtls);
                    // echo "<pre>"; print_r($sql_result); die;
                    $i=1;
                    foreach($sql_result as $row)
                    {
                        if ($i%2==0){ $bgcolor="#E9F3FF";}else{ $bgcolor="#FFFFFF";}
                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>">
                            <td style="font-size:18px;" ><? echo $i; ?></td>
                            <td style="font-size:18px;" ><? echo $row['SERVICE_DETAILS']; ?></td>
                            <td style="font-size:18px;" ><? echo $row['ITEM_DESCRIPTION']; ?></td>
                            <td style="font-size:18px;" ><? echo $service_uom_arr[$row['UOM']]; ?></td>
                            <td style="font-size:18px;" align="right"><? echo number_format($row['WO_QNTY'],2,".",""); ?></td>
                            <td style="font-size:18px;" align="right"><? echo $currency_sign_arr[$carrency_id].' '.number_format($row['RATE'],4,".",""); ?></td>
                            <td style="font-size:18px;" align="right"><? echo $currency_sign_arr[$carrency_id].' '.number_format($row['AMOUNT'],2,".",","); ?></td>
                            <td style="font-size:18px;" >&nbsp;<? echo $row['REMARKS']; ?></td>
                        </tr>
                        <?php
                        $tot_wo_amount += $row['AMOUNT'];
                        $i++;
                    }
                    $grand_tot_wo_amount=$tot_wo_amount+$upcharge-$discount;
                ?>
                <tr>
                    <td colspan="6" align="right" style="font-size:18px;"><strong>Total:&nbsp;</strong></td>
                    <td align="right" style="font-size:18px;"><? echo $currency_sign_arr[$carrency_id].' '.number_format($tot_wo_amount, 2, '.', ','); ?></td>
                    <td>&nbsp;</td>
                </tr>
                <?
                    if($upcharge)
                    {
                        ?>
                            <tr>
                                <td colspan="2" style="font-size:18px;"><strong>Upcharge Remarks</strong></td>
                                <td colspan="3" style="font-size:18px;"><?=$data_result[0]['UPCHARGE_REMARKS']?></td>
                                <td align="right" style="font-size:18px;"><strong>Upcharge:&nbsp;</strong></td>
                                <td align="right" style="font-size:18px;"><? echo $currency_sign_arr[$carrency_id].' '.number_format($upcharge, 2, '.', ','); ?></td>
                                <td>&nbsp;</td>
                            </tr>
                        <?
                    }
                    if($discount)
                    {
                        ?>
                            <tr>
                                <td colspan="2" style="font-size:18px;"><strong>Discount Remarks</strong></td>
                                <td colspan="3" style="font-size:18px;"><?=$data_result[0]['DISCOUNT_REMARKS']?></td>
                                <td align="right" style="font-size:18px;"><strong>Discount:&nbsp;</strong></td>
                                <td align="right" style="font-size:18px;"><? echo $currency_sign_arr[$carrency_id].' '.number_format($discount, 2, '.', ','); ?></td>
                                <td>&nbsp;</td>
                            </tr>
                        <?
                    }
                    if($upcharge>0 || $discount>0)
                    {
                        ?>
                            <tr>
                                <td colspan="6" align="right" style="font-size:18px;"><strong>Net Total:&nbsp;</strong></td>
                                <td align="right" style="font-size:18px;"><? echo $currency_sign_arr[$carrency_id].' '.number_format($grand_tot_wo_amount, 2, '.', ','); ?></td>
                                <td>&nbsp;</td>
                            </tr>
                        <?
                    }
                ?>
                <tr>
                    <td colspan="7" style="font-size:18px;"><strong>Amount (in word):&nbsp;</strong><?echo number_to_words(number_format($grand_tot_wo_amount, 2, '.', ','),$currency[$carrency_id]);?></td>
                </tr>
            </tbody>
        </table>
        <br/>
        <?echo get_spacial_instruction($data_result[0]['WO_NUMBER'],"980px",484);?>
        <br/>
        <? echo signature_table(263, $data[0],"960px",$template_id,40,$user_lib_name[$inserted_by]); ?>
    </div>
    <script type="text/javascript" src="../../js/jquery.js"></script>
    <?
    exit();
}

if($action=="delivery_info_popup")
{
  	echo load_html_head_contents("Place Of Delivery Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$txt_delivery_info_dtls_ref=explode("__",str_replace("'","",$hidden_delivery_info_dtls));  
	?>
	     
	<script>
		function js_set_value()
		{
	 		var txt_supply_name=$('#txt_supply_name').val();
			var txt_address_name=$('#txt_address_name').val();
			var txt_contact_person=$('#txt_contact_person').val();
			var txt_designation_name=$('#txt_designation_name').val();
			var txt_contact_no=$('#txt_contact_no').val();
			var txt_email=$('#txt_email').val();
            
            if(txt_supply_name!='' || txt_address_name!='' || txt_contact_person!='' || txt_designation_name!='' || txt_contact_no!='' || txt_email!='')
            {
                $('#hdn_delivery_info_dtls').val("__"+txt_supply_name+"__"+txt_address_name+"__"+txt_contact_person+"__"+txt_designation_name+"__"+txt_contact_no+"__"+txt_email);
            }else{
                $('#hdn_delivery_info_dtls').val(null);
            }

			parent.emailwindow.hide();
		}

	</script>

	</head>

	<body>
	<div align="center" style="width:400px;">
	<form name="searchdocfrm_1"  id="searchdocfrm_1" autocomplete="off" >
    <legend>Place Of Delivery Info</legend>
	<table width="380" cellspacing="2" cellpadding="0" border="0" id="tbl_master">
            <tbody>
                <tr>
                	<td width="30" align="center" >1</td>
                	<td width="130" >SUPPLY TO :</td>
                    <td width="170">&nbsp;<input type="text" name="txt_supply_name" id="txt_supply_name" style="width:150px" class="text_boxes" value="<?= $txt_delivery_info_dtls_ref[1];?>" /></td> 
            	</tr>
                <tr>
                	<td width="30" align="center">2</td>
                	<td width="130" >ADDRESS :</td>
                    <td width="170">&nbsp;<input type="text" name="txt_address_name" id="txt_address_name" style="width:150px" class="text_boxes" value="<?= $txt_delivery_info_dtls_ref[2];?>" /></td> 
            	</tr>
                <tr>
                	<td width="30" align="center">3</td>
                	<td width="130" >CONTACT PERSON :</td>
                    <td width="170">&nbsp;<input type="text" name="txt_contact_person" id="txt_contact_person" style="width:150px" class="text_boxes" value="<?= $txt_delivery_info_dtls_ref[3];?>" /></td> 
            	</tr>
                <tr>
                	<td width="30" align="center">4</td>
                	<td width="130" >DESIGNATION :</td>
                    <td width="170">&nbsp;<input type="text" name="txt_designation_name" id="txt_designation_name" style="width:150px" class="text_boxes" value="<?= $txt_delivery_info_dtls_ref[4];?>" /></td> 
            	</tr>
                <tr>
                	<td width="30" align="center">5</td>
                	<td width="130">CONTACT NO. :</td>
                    <td width="170">&nbsp;<input type="text" name="txt_contact_no" id="txt_contact_no" style="width:150px" class="text_boxes" value="<?= $txt_delivery_info_dtls_ref[5];?>" /></td> 
            	</tr>
                <tr>
                	<td width="30" align="center">6</td>
                	<td width="130" >E-MAIL :</td>
                    <td width="170">&nbsp;<input type="text" name="txt_email" id="txt_email" style="width:150px" class="text_boxes" value="<?= $txt_delivery_info_dtls_ref[6];?>" /></td> 
            	</tr>

                <tr><td>&nbsp;</td></tr>
                <tr>
                	<td colspan="4" align="center">
                    <input type="button" id="btn_close" style="width:100px" class="formbutton" value="Close" onClick="js_set_value();" />
                    <input type="hidden" id="hdn_delivery_info_dtls" name="hdn_delivery_info_dtls" />
                    </td>
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

?>
