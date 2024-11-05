<?
/*-------------------------------------------- Comments -----------------------
Version (MySql)          :
Version (Oracle)         :
Converted by             :
Converted Date           :
Purpose			         :
Functionality	         :	
JS Functions	         :
Created by		         : Jahid Hasan
Creation date 	         : 08-05-2017
Requirment Client        : Urmi
Requirment By            : 
Requirment type          : 
Requirment               : 
Affected page            : 
Affected Code            :                   
DB Script                : 
Updated by 		         : 		
Update date		         : 		   
QC Performed BY	         :		
QC Date			         :	
Comments		         :
-------------------------------------------------------------------------------*/
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");
require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission'] = $permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Stripe Color Info", "../", 1, 1, $unicode, '', '');
?>
<script type="text/javascript">
    var permission = '<? echo $permission; ?>';
    //Master form---------------------------------------------------------------------------
    function openmypage(page_link, title) {
        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=450px,center=1,resize=1,scrolling=0', '');
        emailwindow.onclose = function () {
            var theform = this.contentDoc.forms[0];
            var sales_order_data = this.contentDoc.getElementById("selected_sales_order").value;
            var sales_data = sales_order_data.split("*");
            freeze_window(5);
            $("#cbo_company_name").val(sales_data[4]).attr('disabled','disabled');
            $("#hdn_within_group").val(sales_data[5]);
            load_drop_down('requires/stripe_color_measurement_controller_urmi', sales_data[4]+'_'+sales_data[5], 'load_drop_down_buyer', 'buyer_td');
            $("#hdn_sales_order").val(sales_data[0]);
            $("#txt_job_no").val(sales_data[1]);
            $("#cbo_buyer_name").val(sales_data[2]).attr('disabled','disabled');;
            $("#txt_style_ref").val(sales_data[3]);
            var fabric_cost_id = '';
            var cbo_color_name = '';
            show_list_view(sales_data[0], 'show_fabric_color_listview', 'fabric_container', 'requires/stripe_color_measurement_controller_urmi', '');
            show_list_view(sales_data[1] + '_' + fabric_cost_id + '_' + cbo_color_name, 'stripe_color_list_view', 'stripe_color_list_view_container', 'requires/stripe_color_measurement_controller_urmi', '');
            release_freezing();
        }
    }
    function set_data(sales_order_id) {
        var data = String("'" + sales_order_id + "'");
        var data = encodeURIComponent(data);
        get_php_form_data(data, 'set_data', "requires/stripe_color_measurement_controller_urmi");
        load_drop_down('requires/stripe_color_measurement_controller_urmi', data, 'load_drop_down_color', 'color_td')
    }

    function open_color_popup() {
        var hdn_sales_order = document.getElementById('txt_job_no').value;
        var hdn_within_group = document.getElementById('hdn_within_group').value;
        var cbogmtsitem = document.getElementById('cbogmtsitem').value;
        var cbofabricnature = document.getElementById('cbofabricnature').value;
        var fabric_cost_id = document.getElementById('fabricdescription').value;
        var cbo_color_name = document.getElementById('cbo_color_name').value;
        var color_attr = $('#cbo_color_name option:selected').attr('data-attr').split("**");
        var sales_dtls_id = color_attr[0];
        var pre_cost_id = color_attr[1];
        var color_type_id = color_attr[2];
        var cbo_company_name = document.getElementById('cbo_company_name').value;
        var cbo_buyer_name = document.getElementById('cbo_buyer_name').value;
        if (cbo_color_name == 0) {
            return;
        }
        var page_link = "requires/stripe_color_measurement_controller_urmi.php?action=open_color_list_view&hdn_sales_order=" + hdn_sales_order + "&cbogmtsitem=" + cbogmtsitem + "&fabric_cost_id=" + fabric_cost_id + "&cbo_color_name=" + cbo_color_name + '&cbo_company_name=' + cbo_company_name + '&cbo_buyer_name=' + cbo_buyer_name + '&sales_dtls_id=' + sales_dtls_id + '&cbogmtsitem=' + cbogmtsitem + '&cbofabricnature=' + cbofabricnature + '&pre_cost_id=' + pre_cost_id + '&color_type_id=' + color_type_id + '&hdn_within_group=' + hdn_within_group;

        emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, "Set Details", 'width=800px,height=400px,center=1,resize=1,scrolling=0', '../')
        emailwindow.onclose = function () {
            show_list_view(hdn_sales_order + '_' + pre_cost_id + '_' + cbo_color_name + '_' + sales_dtls_id, 'stripe_color_list_view', 'stripe_color_list_view_container', 'requires/stripe_color_measurement_controller_urmi', '');
        }
    }
    function show_content_data(fabric_cost_id, cbo_color_name) {
        var sales_data = String(document.getElementById('hdn_sales_order').value + '_' + fabric_cost_id + '_' + document.getElementById('hdn_sales_order').value);
        set_data(sales_data);
        document.getElementById('fabricdescription').value = sales_data;
        document.getElementById('cbo_color_name').value = cbo_color_name;
        open_color_popup();
    }
    function fn_deletebreak_down_tr(fabric_cost_id, cbo_color_name,statusFlag,rowSl,progNo) {
        if (confirm("Are You Sure?")) {
            var permission_array = permission.split("_");
            if (permission_array[2] != 1) {
                alert("You have no delete permission");
                return;
            }
            if(statusFlag==1 || statusFlag==2)
            {
                alert("Found Yarn Dyeing Work Order Sales/Program " + progNo );
                return; 
            }
            var txt_job_no = document.getElementById('txt_job_no').value;
            if (fabric_cost_id != "" && permission_array[2] == 1) {
                var response = return_global_ajax_value(fabric_cost_id + "_" + cbo_color_name + "_" + txt_job_no, 'delete_row', '', 'requires/stripe_color_measurement_controller_urmi');
                if (response * 1 == 11) {
                    alert("Yarn Booking Found for this Job, Delete not Possible");
                    return;
                }
                if (response * 1 == 1) {
                    alert("Data has been Deleted");
                    var fabric_cost_id = '';
                    var cbo_color_name = '';
                    show_list_view(txt_job_no + '_' + fabric_cost_id + '_' + cbo_color_name, 'stripe_color_list_view', 'stripe_color_list_view_container', 'requires/stripe_color_measurement_controller_urmi', '');
                }
                else {
                    alert("Problem Found, Delete not Successfull");
                }
            }
        }
    }
</script>
</head>

<body onLoad="set_hotkey()">

<div style="width:100%;" align="center">
	<? echo load_freeze_divs("../../", $permission); ?>
    <fieldset style="width:1070px;">
        <legend>Sales Order</legend>
        <table width="90%" cellpadding="0" cellspacing="2" align="center">
            <tr>
                <td width="100%" align="left" valign="top">
                    <form name="precosting_1" id="precosting_1" autocomplete="off">
                        <div style="width:1070px;">
                            <table width="100%" cellspacing="2" cellpadding="" border="0">
                                <tr>
                                    <td align="right" width="120" class="must_entry_caption">Sales Order No</td>
                                    <td width="150">
                                        <input style="width:150px;" type="text" title="Double Click to Search"
                                               onDblClick="openmypage('requires/stripe_color_measurement_controller_urmi.php?action=sales_order_popup','Sales Order Selection Form')"
                                               class="text_boxes" placeholder="Browse Sales order No" name="txt_job_no"
                                               id="txt_job_no" readonly/>
                                        <input type="hidden" id="hdn_sales_order"/>
                                        <input type="hidden" id="hdn_within_group"/>
                                    </td>
                                    <td align="right" width="150">Company</td>
                                    <td width="150">
										<?
										echo create_drop_down("cbo_company_name", 160, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name", "id,company_name", 1, "-- Select Company --", $selected, "load_drop_down( 'requires/stripe_color_measurement_controller_urmi',this.value, 'load_drop_down_buyer', 'buyer_td' );", 1);
										?>
                                    </td>
                                    <td align="right" width="150">Cust. Buyer/Buyer</td>
                                    <td id="buyer_td">
										<?
										echo create_drop_down("cbo_buyer_name", 160, $buyer_arr, "", 1, "-- Select Buyer --", $selected, "", 1, "");
										?>
                                    </td>
                                    <td align="right" width="150">Style Ref</td>
                                    <td>
                                        <input class="text_boxes" type="text" style="width:150px;" name="txt_style_ref"
                                               id="txt_style_ref" maxlength="75" title="Maximum 75 Character" readonly/>
                                        <input type="hidden" id="update_id" value=""/>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="100%" align="center" valign="top" id="fabric_container" colspan="8"></td>
                                </tr>
                                <tr>
                                    <td align="center" valign="middle" class="button_container" colspan="8"></td>
                                </tr>
                            </table>
                        </div>
                    </form>
                </td>
            </tr>
        </table>
    </fieldset>
</div>
<div style="width:1070px; margin: 10px auto;">
    <div id="stripe_color_list_view_container"></div>
</div>
</body>
<script>
	<?
	if($txt_job_no != "")
	{
	?>
    var fabric_cost_id = '';
    var cbo_color_name = '';
    var txt_job_no = '<? echo $txt_job_no;?>';
    get_php_form_data(txt_job_no, 'populate_data_from_job_table', "requires/stripe_color_measurement_controller");
    show_list_view(txt_job_no, 'show_fabric_cost_listview', 'cost_container', 'requires/stripe_color_measurement_controller', '');
    show_list_view(txt_job_no + '_' + fabric_cost_id + '_' + cbo_color_name, 'stripe_color_list_view', 'stripe_color_list_view_container', 'requires/stripe_color_measurement_controller', '');
	<?
	}
	?>
</script>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>
