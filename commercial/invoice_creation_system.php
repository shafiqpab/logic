<?
/*-------------------------------------------- Comments

Purpose			: 	This form will create for System generate_invoice
Functionality	:
JS Functions	:
Created by		:	Jahid/Wayasel
Creation date 	: 	30-08-2023
Updated by 		: 	
Update date		: 	
QC Performed BY	:
QC Date			:
Comments		:

*/

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
$userCredential = sql_select("SELECT unit_id as company_id,store_location_id FROM user_passwd where id=$user_id");
$company_id = $userCredential[0][csf('company_id')];
$company_credential_cond = "";
if ($company_id != "") {
    $company_credential_cond = "and comp.id in($company_id)";
}
$user_id = $_SESSION['logic_erp']['user_id'];
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Invoice Creation","../", 1, 1, $unicode,1,1); 
?>
<script>
    if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";
    setFilterGrid("table_body",-1);

    function fnc_invoice_creation(operation)
    {		       
        if(operation==2)
		{
			alert("Delete Not Allow.");
			show_msg(13);return;
		}
        var update_id=$('#update_id').val();

        if(update_id==0){

            if (form_validation('cbo_company_name*cbo_buyer_name*txt_year*count_of_invoice*cbo_invoice_status','Company*Buyer Name*Year*count of invoice*invoice status')==false)
            {
                return;
            }       
        }
        var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_company_name*cbo_buyer_name*txt_year*count_of_invoice*cbo_invoice_status*update_id',"../");
        freeze_window(operation);
        http.open("POST","requires/invoice_creation_system_controller.php",true);
        http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        // alert(data);
        http.send(data);
        http.onreadystatechange = fnc_invoice_creation_response;
    }

    function fnc_invoice_creation_response()
    {
        if(http.readyState == 4)
        {
            var reponse=trim(http.responseText).split('**');
            show_list_view(reponse[3]+'_'+reponse[2],'show_list_view','list_view_charge','requires/invoice_creation_system_controller','setFilterGrid("table_body",-1)');
             set_button_status(1, ".$_SESSION['page_permission'].", 'fnc_invoice_creation',1,1);     
            release_freezing();            
        }
    }

    function Fnc_invocie_show_list_view(){
        var cbo_buyer_name=$('#cbo_buyer_name').val();
		var company_name = $('#cbo_company_name').val();
        show_list_view(company_name+'_'+cbo_buyer_name,'show_list_view','list_view_charge','requires/invoice_creation_system_controller','setFilterGrid("table_body",-1)');
        set_button_status(0, ".$_SESSION['page_permission'].", 'fnc_invoice_creation',1,1);  
    }
    
    function new_window()
	{		
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		$('#list_view tr:first').hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><title></title><link rel="stylesheet" href="../css/style_print.css" type="text/css"/></head><body>'+document.getElementById('item_group_list_view').innerHTML+'</body</html>');
		d.close();
		$('#list_view tr:first').show();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="225px";
	}

    function enable_fields(str){
        var splitStr = str.split('*');
        $.each(splitStr, function (index, val){
            $('#'+val).prop('disabled', false);
        });
    }

    function fnc_last_invoice()
	{
		var cbo_buyer_name=$('#cbo_buyer_name').val();
		var company_name = $('#cbo_company_name').val();
		var txt_year = $('#txt_year').val();
		var data =company_name+'**'+cbo_buyer_name+'**'+txt_year;
		var response=return_global_ajax_value(data, 'last_count_invoice', '', 'requires/invoice_creation_system_controller');
        $('#txt_last_invoice_no').val(response);
        $('#txt_last_invoice_no').attr('readonly',true);	
	}
	
	function fnResetForm()
	{
		reset_form('invoice_creation_1','','','','enable_fields(\'cbo_company_name*cbo_buyer_name*count_of_invoice*txt_year\')','txt_file_date*txt_year');
		set_button_status(0, permission, 'fnc_invoice_creation',1, 0);
	}
</script>

</head>
<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
    <? echo load_freeze_divs ("../",$permission); ?>
    <fieldset style="width:900px; margin-bottom:10px;">
        <legend>Invoice Creation Entry</legend>
        <form name="invoice_creation_1" id="invoice_creation_1" autocomplete="off" method="POST" action="" >
            <table cellpadding="0" cellspacing="1" width="100%">
                <tr><td height="5" colspan="8"></td></tr>
                <tr>
                    <td width="120" class="must_entry_caption" style="padding: 3px 0px;">Company</td>
                    <td width="150">
                        <?
                        echo create_drop_down( "cbo_company_name", 142, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_credential_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", 0, "load_drop_down( 'requires/invoice_creation_system_controller', this.value, 'load_drop_down_buyer_search', 'buyer_td_id' );fnc_last_invoice();Fnc_invocie_show_list_view();" );
                        ?>
                        <input type="hidden" name="update_id" id="update_id" />
                    </td>
                    <td class="must_entry_caption" width="120">Buyer Name</td>
                    <td id="buyer_td_id" width="150">
                        <? echo create_drop_down( "cbo_buyer_name", 142, $blank_array,"", 1, "-- Select Buyer --", 0, "" ); ?>
                    </td>
                    <td class="must_entry_caption" width="120">Invoice Year</td>
                    <td>
                        <?
                        echo create_drop_down( "txt_year",90, $year, "", 0, "--All--", date('Y'), "", "", "");
                        ?>
                    </td>
                </tr>
                <tr>
                	<td class="must_entry_caption">Count Of Invoice</td>
                    <td>
                        <input name="count_of_invoice" id="count_of_invoice" style="width:130px" class="text_boxes_numeric" maxlength="2" value="" title="Maximum Character 2">
                    </td>
                    <td>Last Invoice No</td>
                    <td>
                        <input name="txt_last_invoice_no" id="txt_last_invoice_no" style="width:130px" class="text_boxes" readonly disabled >
                    </td>
                    <td>Status</td>
                    <td>
                        <?
                        echo create_drop_down( "cbo_invoice_status", 142, $row_status,"", 0, "", 1, "" );
                        ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="8" height="50" valign="middle" align="center" class="button_container">
                        <? echo load_submit_buttons( $permission, "fnc_invoice_creation", 0,0,"fnResetForm();",1); ?>
                    </td>
                </tr>
            </table>
        </form>
    </fieldset>
    <div style="width:100%; float:left; margin:auto" align="center">
        <fieldset style="width:900px; margin-top:20px">
            <legend>Invoice No. List View </legend>
            <div style="width:900px; margin-top:3px; margin-bottom: 3px;" id="item_group_list_view" align="left">
                <div id="list_view_charge"></div>
            </div>
        </fieldset>
    </div>
</div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>