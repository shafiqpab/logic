<?
/*-------------------------------------------- Comments

Purpose			: 	This form will create for BL Charge Entry
Functionality	:
JS Functions	:
Created by		:	Nayem
Creation date 	: 	19-4-2021
Updated by 		: 	
Update date		: 	
QC Performed BY	:
QC Date			:
Comments		:

*/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//-------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("BH Commission Entry", "../../",  1, 1, $unicode,'','');

?>
<script>
    if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
    var permission='<? echo $permission; ?>';
    function openmypage_realization()
    {
        var cbo_company_name = $("#cbo_company_name").val();
        var cbo_bank_name = $("#cbo_bank_name").val();
        
        if (form_validation('cbo_company_name*cbo_bank_name','Company*Bank Name')==false )
        {
            return;
        }
        
        var page_link='requires/bh_commission_entry_controller.php?action=proceed_realization_popup_search&cbo_company_name='+cbo_company_name+'&cbo_bank_name='+cbo_bank_name;
        var title='Export Proceeds Realization Entry Form';
        
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=400px,center=1,resize=1,scrolling=0','../');
        emailwindow.onclose=function()
        {
            var theform=this.contentDoc.forms[0];
            var realization_id=this.contentDoc.getElementById("hidden_realization_id").value;
            var invoice_bill_no=this.contentDoc.getElementById("hidden_invoice_bill_no").value;
            var invoice_bill_id=this.contentDoc.getElementById("hidden_invoice_bill_id").value;
            
            if(trim(realization_id)!="")
            {
                freeze_window(5);
                show_list_view(realization_id, 'populate_data_from_invoice_bill', 'commission_tbl', 'requires/bh_commission_entry_controller', '' ) ;
                $('#txt_bill_no').val(invoice_bill_no);
                $('#submission_invoice_id').val(invoice_bill_id);
                $('#realization_id').val(realization_id);
                disable_enable_fields('cbo_company_name*cbo_bank_name',1);		
                release_freezing();
            }
                        
        }
    } 	

    function fnc_bh_commission_entry(operation)
    {
        if(operation==4){
            var form_caption=$( "div.form_caption" ).html();
	 	    print_report( $('#update_id').val()+'*'+form_caption, "bh_commission_print", "requires/bh_commission_entry_controller" )
	 	    return;
        }

        if (form_validation('cbo_company_name*txt_commission_date*realization_id*cbo_bank_name','Company Name*Commision Date*Bank Ref/ Bill No*Bank Name')==false)
		{
			return;
		}
        var total_commission=$("#total_commission_amount").val()*1;
        if(total_commission==0){
            alert('Total Commission Amount Can Not Zero');
            return;
        }

		var data_mst=get_submitted_data_string('txt_system_id*update_id*cbo_company_name*txt_commission_date*cbo_bank_name*txt_bill_no*submission_invoice_id*realization_id*txt_remarks*txt_buying_house_info_dtls*txt_total_amount*txt_upcharge*txt_discount*txt_total_amount_net',"../../");
		// alert(data_mst);

		var data="action=save_update_delete&operation="+operation+data_mst; 
		// alert(data); return;
		freeze_window(operation);
		http.open("POST","requires/bh_commission_entry_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_bh_commission_entry_reponse;
    }

    function fnc_bh_commission_entry_reponse()
    {
        if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split('**');
			if(trim(reponse[0])==0 || trim(reponse[0])==1)
			{
				document.getElementById('txt_system_id').value=reponse[1];
				document.getElementById('update_id').value=reponse[2];
                disable_enable_fields('txt_bill_no',1);
				set_button_status(1, permission, 'fnc_bh_commission_entry',1);
			}
			if(trim(reponse[0])==2)
			{
				reset_form('bhcommissionentry_1','commission_tbl','','','disable_enable_fields(\'txt_bill_no\',0);','');
			}
			show_msg(trim(reponse[0]));
			release_freezing();
		}
    }

    function openmypage_sys_no()
    {
        if( form_validation('cbo_company_name','Company Name')==false )
        {
            return;
        }
        var cbo_company_name = $("#cbo_company_name").val();
        var page_link = 'requires/bh_commission_entry_controller.php?action=system_popup&cbo_company_name='+cbo_company_name;
        var title = 'Search Cash Incentive Submission Entry';

        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900px,height=400px,center=1,resize=0,scrolling=0','../');
        emailwindow.onclose=function()
        {
			var commission_id=this.contentDoc.getElementById("selected_id").value;
            if(commission_id!="")
            {
                freeze_window();
                get_php_form_data( commission_id, 'populate_data_from_search_popup','requires/bh_commission_entry_controller');
                disable_enable_fields('cbo_company_name*cbo_bank_name*txt_bill_no',1);
                set_button_status(1, permission, 'fnc_bh_commission_entry',1);
            }
            release_freezing();
        }
    }

    function fnc_letter_print(letter_type)
	{
        var form_caption=$( "div.form_caption" ).html();
		if (form_validation('update_id','Save Data First')==false)
		{
			alert("Save Data First");
			return;
		}
		else
		{
		print_report(letter_type+'**'+form_caption+'**'+$('#update_id').val(),'print_letter','requires/bh_commission_entry_controller');
		}
	}

    function fn_buying_house_info()
    {
        var txt_buying_house_info_dtls=$('#txt_buying_house_info_dtls').val();
        var page_link='requires/bh_commission_entry_controller.php?action=buying_house_popup&txt_buying_house_info_dtls='+txt_buying_house_info_dtls;
        var title="Buying House Popup";
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title,'width=720px,height=200px,center=1,resize=1,scrolling=0','../');
        emailwindow.onclose=function()
        {
            var theform=this.contentDoc.forms[0];
            var theemail=this.contentDoc.getElementById("hdn_buying_house_info_dtls").value;
            document.getElementById('txt_buying_house_info_dtls').value=theemail;
            var txt_buying_house_info_dtls_ref=theemail.split("__");
            document.getElementById('txt_buying_house_info').value=txt_buying_house_info_dtls_ref[2];
        }
    }

    function calculate_total_amount()
	{
		var txt_total_amount=$('#txt_total_amount').val();
		var txt_upcharge=$('#txt_upcharge').val();
		var txt_discount=$('#txt_discount').val();

		var net_tot_amnt=txt_total_amount*1+txt_upcharge*1-txt_discount*1;
		$('#txt_total_amount_net').val(net_tot_amnt.toFixed(4));
	}
</script>
<body onLoad="set_hotkey()">
    <div align="center" style="width:100%;">
        <? echo load_freeze_divs ("../../",$permission); ?>
        <form name="bhcommissionentry_1" id="bhcommissionentry_1" autocomplete="off">
            <fieldset style="width:1100px;">
                <legend>BH Commission Entry</legend>
                <table width="width:700" border="0" cellpadding="0" cellspacing="2">
                    <tr style="width:700">
                        <td colspan="3" align="right"><strong>System ID</strong></td> 
                        <td colspan="3">
                            <input style="width:140px;" type="text" title="Double Click to Search" onDblClick="openmypage_sys_no()" class="text_boxes" placeholder="Browse" name="txt_system_id" id="txt_system_id" readonly />
                            <input type="hidden" id="update_id">
                        </td>
                    </tr>
                    <tr>
                        <td width="90" class="must_entry_caption">Company</td>
                        <td width="150">
                            <?
                            echo create_drop_down( "cbo_company_name", 140, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business in(1,3) $company_cond order by comp.company_name","id,company_name", 1, "--- Select Company ---",  $cbo_country_id, "");
                            ?>
                        </td>
                        <td width="80" class="must_entry_caption">Commission Date</td>
                        <td width="150">
                            <input style="width:130px " name="txt_commission_date" id="txt_commission_date" class="datepicker" placeholder="Date"/>
                        </td>
                        <td width="80" class="must_entry_caption">Bank Name</td>
                        <td width="150">
                            <? 
                                if ($db_type==0)
                                {
                                    echo create_drop_down("cbo_bank_name", 140, "select concat(a.bank_name,' (', a.branch_name,')') as bank_name,id from lib_bank where is_deleted=0 and status_active=1 order by bank_name", "id,bank_name", 1, "-- Select Bank --", 0, "");
                                }
                                else
                                {
                                    echo create_drop_down("cbo_bank_name", 140, "select (bank_name || ' (' || branch_name || ')' ) as bank_name,id from lib_bank where is_deleted=0 and status_active=1 order by bank_name", "id,bank_name", 1, "-- Select Bank --", 0, "");
                                }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="must_entry_caption">Bank Ref/ Bill No</td>
                        <td>
                            <input style="width:130px;" type="text" title="Double Click to Search" onDblClick="openmypage_realization()" class="text_boxes" placeholder="Browse" name="txt_bill_no" id="txt_bill_no" readonly />
                            <input type="hidden" id="submission_invoice_id">
                            <input type="hidden" id="realization_id">
                        </td>
                        <td>Remarks</td>
                        <td colspan="3">
                            <input type="text" name="txt_remarks" id="txt_remarks" style="width:360px" class="text_boxes">
                        </td>
                    </tr>
                    <tr>
                        <td class="must_entry_caption">Buying House Info</td>
                        <td>
                            <input type="text" name="txt_buying_house_info" id="txt_buying_house_info" class="text_boxes" style="width:130px" onDblClick="fn_buying_house_info()" placeholder="Browse" readonly />
                            <input type="hidden" name="txt_buying_house_info_dtls" id="txt_buying_house_info_dtls" />
                        </td>
                    </tr>
                </table>
                <br>
                <div id="commission_tbl"></div>
                <br>
                <? echo load_submit_buttons( $permission, "fnc_bh_commission_entry", 0,1,"reset_form('bhcommissionentry_1','commission_tbl','','','disable_enable_fields(\'cbo_company_name*cbo_bank_name*txt_bill_no\');','')",1); ?>
                <input type="button" style="width:100px;" id="btn_bank_forwarding"  onClick="fnc_letter_print(1)" class="formbutton printReport" name="btn_bank_forwarding" value="Bank Forwarding" />
            </fieldset>
        </form>
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>