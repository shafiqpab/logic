<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create for Cash Incentive Submission Entry
Functionality	:
JS Functions	:
Created by		:	Nayem
Creation date 	: 	7-4-2021
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
echo load_html_head_contents("Cash Incentive Submission Entry", "../../",1,1, $unicode,'','');

?>
<script>
    if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
    var permission='<? echo $permission; ?>';
    function openmypage_sys_no()
    {
        if( form_validation('cbo_company_name','Company Name')==false )
        {
            return;
        }
        var cbo_company_name = $("#cbo_company_name").val();
        var page_link = 'requires/cash_incentive_submission_controller.php?action=system_popup&cbo_company_name='+cbo_company_name;
        var title = 'Search Cash Incentive Submission Entry';

        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900px,height=400px,center=1,resize=0,scrolling=0','../');
        emailwindow.onclose=function()
        {
			var theemail=this.contentDoc.getElementById("selected_id").value;
			var is_sc_lc=this.contentDoc.getElementById("cbo_search_by").value;
            if(theemail!="")
            {
                freeze_window();
                get_php_form_data( theemail+"**"+is_sc_lc, 'populate_data_from_search_popup','requires/cash_incentive_submission_controller');
                $('#cbo_company_name').attr('disabled',true);
                $('#txt_lc_sc_no').attr('disabled',true);
                set_button_status(1, permission, 'fnc_cash_incentive_submission',1);
            }
            release_freezing();
        }
    }
    function openmypage_realizationInfo()
    {
        if (form_validation('cbo_company_name','Company')==false )
        {
            return;
        }
        var beneficiary_name = $("#cbo_company_name").val();
        var page_link='requires/cash_incentive_submission_controller.php?action=proceed_realization_popup_search&beneficiary_name='+beneficiary_name;
        var title='Export Proceeds Realization Entry Form';
        
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=400px,center=1,resize=1,scrolling=0','../');
        emailwindow.onclose=function()
        {
            var theform=this.contentDoc.forms[0];
            var realization_id=this.contentDoc.getElementById("hidden_realization_id").value;
            var is_lc_sc=this.contentDoc.getElementById("hidden_is_lc").value;
            var invoice_id_all=this.contentDoc.getElementById("hidden_invoice_id").value;
            var lc_sc_id=this.contentDoc.getElementById("hidden_lc_sc_id").value;
            var internal_file_no=this.contentDoc.getElementById("hidden_inter_file_no").value;
			var file_no_string=this.contentDoc.getElementById("file_no_string").value;
			
			//alert(file_no_string);
			
            var invoice_id_arr = $.unique(invoice_id_all.split(','));
            var internal_file_no_arr = $.unique(internal_file_no.split(','));
            var sc_lc_id = $.unique(lc_sc_id.split(','));
            var is_lc_sc_arr = $.unique(is_lc_sc.split(','));

            if(is_lc_sc_arr.length>1){
                alert('LS/SC Can Not Mixed');
                return;
            }
			
            /*if(internal_file_no_arr.length>1){
                alert('Different Internal File No Is Not Acceptable');
                return;
            }*/

            if(trim(realization_id)!="")
            {
                freeze_window(5);
                get_php_form_data(realization_id, "populate_data_from_invoice_bill", "requires/cash_incentive_submission_controller" );
                $("#realization_id").val(realization_id);			
                $("#submission_invoice_id").val(invoice_id_arr);			
                $("#is_lc_sc").val(is_lc_sc_arr);			
                $("#lc_sc_id").val(sc_lc_id);			
                $("#txt_internal_file_no").val(internal_file_no_arr);	
				$("#txt_file_no_string").val(file_no_string);		
                release_freezing();
            }
                        
        }
    }
    function numberPopup(id)
    {
        if( form_validation('cbo_company_name','Company Name')==false )
        {
            return;
        }
        var cbo_company_name = $("#cbo_company_name").val();
        var page_link = 'requires/cash_incentive_submission_controller.php?action=btb_lc_popup&cbo_company_name='+cbo_company_name;
        var title = 'Search LC/SC Details';
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=400px,center=1,resize=0,scrolling=0','../')
        emailwindow.onclose=function()
        {
			var theemail=this.contentDoc.getElementById("selected_id").value;
            if(theemail!="")
            {
                var data=theemail;
                get_php_form_data( data, 'load_btb_lc_info','requires/cash_incentive_submission_controller');
                // set_all_onclick();
            }
            release_freezing();
        }
    }

    function calculate_amount(id){
        var net_realize_value = $("#txt_net_realize_value").val();
        if(id==1){
            var special_submitted_chk = $("#special_submitted_chk").val();
            if(special_submitted_chk==0){
                $("#special_submitted_chk").val(1);
                amount= net_realize_value*1*.01;
                $("#special_submitted").val(amount);
            }else{
                $("#special_submitted_chk").val(0);
                $("#special_submitted").val('');
            }
        }
        // if(id==2){
        //     var euro_incentive_chk = $("#euro_incentive_chk").val();
        //     if(euro_incentive_chk==0){
        //         $("#euro_incentive_chk").val(1);
        //         amount= net_realize_value*1*.06;
        //         $("#euro_incentive").val(amount);
        //     }else{
        //         $("#euro_incentive_chk").val(0);
        //         $("#euro_incentive").val('');
        //     }
        // }
        // if(id==3){
        //     var general_incentive_chk = $("#general_incentive_chk").val();
        //     if(general_incentive_chk==0){
        //         $("#general_incentive_chk").val(1);
        //         amount= net_realize_value*1*.04;
        //         $("#general_incentive").val(amount);
        //     }else{
        //         $("#general_incentive_chk").val(0);
        //         $("#general_incentive").val('');
        //     }
        // }
        if(id==4){
            var market_submitted_chk = $("#market_submitted_chk").val();
            if(market_submitted_chk==0){
                $("#market_submitted_chk").val(1);
                amount= net_realize_value*1*.04;
                $("#market_submitted").val(amount);
            }else{
                $("#market_submitted_chk").val(0);
                $("#market_submitted").val('');
            }
        }
        calculate_total_amount();
    }

    function calculate_total_amount()
    {
        var special_submitted = $("#special_submitted").val();
        var euro_incentive = $("#euro_incentive").val();
        var general_incentive = $("#general_incentive").val();
        var market_submitted = $("#market_submitted").val();
        var total_amount=special_submitted*1+euro_incentive*1+general_incentive*1+market_submitted*1
        $("#total_amount").val(total_amount.toFixed(2));


    }

    function fnc_cash_incentive_submission(operation) 
    {
        if (form_validation('cbo_company_name*cbo_bank_name*txt_submission_date*txt_lc_sc_no','Company Name*Bank Name*Submission Date*LS/SC')==false)
        {
            return;
        }
        // *cbo_buyer_name
        var data="action=save_update_delete&operation="+operation+get_submitted_data_string('txt_system_id*update_id*cbo_company_name*cbo_bank_name*txt_submission_date*realization_id*submission_invoice_id*txt_incective_bank_file*txt_net_realize_value*txt_remarks*special_submitted_chk*euro_incentive_chk*general_incentive_chk*market_submitted_chk*special_submitted*euro_incentive*general_incentive*market_submitted*total_amount*is_lc_sc*lc_sc_id*txt_internal_file_no*txt_file_no_string',"../../");
        // alert(data);
        // return;
        freeze_window(operation);
        http.open("POST","requires/cash_incentive_submission_controller.php",true);
        http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        http.send(data);
        http.onreadystatechange = fnc_cash_incentive_submission_reponse;

    }

    function fnc_cash_incentive_submission_reponse()
    {
        if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split('**');
            if(reponse[0]==404)
			{
                alert(reponse[1]);
			}
 
			if(reponse[0]==0 || reponse[0]==1)
			{
				document.getElementById('txt_system_id').value=reponse[1];
				document.getElementById('update_id').value=reponse[2];
                $('#cbo_company_name').attr('disabled',true);
                $('#txt_lc_sc_no').attr('disabled',true);

				set_button_status(1, permission, 'fnc_cash_incentive_submission',1);
			}
			if(reponse[0]==2)
			{
                form_reset_cise();
				reset_form('cashincentivesubmission_1','','','','','');
                set_button_status(0, permission, 'fnc_cash_incentive_submission',1);
			}
			show_msg(reponse[0]);
			release_freezing();
		}
    }

    function form_reset_cise() {
        $('#cbo_company_name').removeAttr('disabled');
        $('#txt_lc_sc_no').removeAttr('disabled');
    }

</script>
<body onLoad="set_hotkey()">
    <div align="center" style="width:100%;">
        <? echo load_freeze_divs ("../../",$permission); ?>
        <form name="cashincentivesubmission_1" id="cashincentivesubmission_1" autocomplete="off">
            <fieldset style="width:850px;">
                <legend>Cash Incentive Submission Entry</legend>
                <table width="800" border="0" cellpadding="0" cellspacing="2" id="tbl_btb">
                    <tr width="780" >
                        <td colspan="3" align="right"><strong>Submission ID</strong></td> 
                        <td colspan="3">
                            <input style="width:140px;" type="text" title="Double Click to Search" onDblClick="openmypage_sys_no()" class="text_boxes" placeholder="Browse" name="txt_system_id" id="txt_system_id" readonly />
                            <input type="hidden" id="update_id">
                        </td>
                    <tr>
                        <td width="100" class="must_entry_caption">Company Name </td>
                        <td width="150">
                             <? echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business in(1,3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $cbo_country_id, "");?>
                        </td>
                        <td width="100" class="must_entry_caption">Bank Name</td>
                        <td width="150" >
                            <?
							echo create_drop_down("cbo_bank_name", 150, "select (bank_name || ' (' || branch_name || ')' ) as bank_name,id from lib_bank where is_deleted=0 and status_active=1 order by bank_name", "id,bank_name", 1, "-- Select Bank --", 0, "");
							?>
                        </td>
                        <td width="100" class="must_entry_caption">Submission Date</td>
                        <td width="150">
                            <input style="width:140px " name="txt_submission_date" id="txt_submission_date" class="datepicker" readonly/>
                        </td>
                    </tr>
                    <tr>
                        <td class="must_entry_caption">LC/SC No </td>
                        <td>
                            <input style="width:140px;" type="text" title="Double Click to Search" onDblClick="openmypage_realizationInfo()" class="text_boxes" placeholder="Browse" name="txt_lc_sc_no" id="txt_lc_sc_no" readonly />
                            <input type="hidden" id="realization_id">
                            <input type="hidden" id="submission_invoice_id">
                            <input type="hidden" id="is_lc_sc">
                            <input type="hidden" id="lc_sc_id">
                            <input type="hidden" id="txt_file_no_string">
                            
                        </td>
                        <td>Export L/c Value</td>
                        <td>
                            <input type="text" name="txt_lc_value" id="txt_lc_value" style="width:140px" class="text_boxes" placeholder="Display" readonly>
                        </td>
                        <td >Invoice Value</td>
                        <td>
                            <input type="text" name="txt_invoice_value" id="txt_invoice_value" style="width:140px" class="text_boxes" placeholder="Display" readonly>
                        </td>
                    </tr>
                    <tr>
                        <td >File Year</td>
                        <td>
                            <input type="text" name="txt_file_year" id="txt_file_year" style="width:140px" class="text_boxes" placeholder="Display" readonly>
                        </td>
                        <td>Bank File No</td>
                        <td>
                            <input type="text" name="txt_bank_file_no" id="txt_bank_file_no" style="width:140px" class="text_boxes" placeholder="Display" readonly>
                        </td>
                        <td>Incective Bank File</td>
                        <td>
                            <input type="text" name="txt_incective_bank_file" id="txt_incective_bank_file" style="width:140px" class="text_boxes">
                        </td>
                    </tr> 
                    <tr>
                        <td >Net Realize Value</td>
                        <td>
                            <input type="text" name="txt_net_realize_value" id="txt_net_realize_value" style="width:140px" class="text_boxes" placeholder="Display" readonly>
                        </td>
                        <!-- <td class="must_entry_caption">Buyer Name</td>
                        <td><?
							echo create_drop_down("cbo_buyer_name", 150, "select id,buyer_name from lib_buyer comp where status_active =1 and is_deleted=0 $buyer_cond", "id,buyer_name", 1, "-- Select Buyer --", 0, "",1);
							?>
                        </td> -->
                        <td>Internal File No</td>
                        <td>
                            <input type="text" name="txt_internal_file_no" id="txt_internal_file_no" style="width:140px" class="text_boxes" placeholder="Display" readonly>
                        </td>
                    </tr>  
                    <tr>
                        <td>Remarks</td>
                        <td colspan="3">
                            <input type="text" name="txt_remarks" id="txt_remarks" style="width:400px" class="text_boxes" >
                        </td>
                    </tr>  
                </table>
                <br>
                <table cellspacing="0" width="250" class="rpt_table" id="tbl_details" >
                    <thead>
                        <tr>
                            <th width="40"></th>
                            <th width="120">Discription</th>
                            <th width="90">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td width="50" align="center">
                                <input type="checkbox" id="special_submitted_chk" name="special_submitted_chk" value="0" onClick="calculate_amount(1)">
                            </td>
                            <td align="right">Submitted to Bank &nbsp;</br> (Special Incentive 1% ) &nbsp;</td>
                            <td>
                                <input type="text" name="special_submitted" id="special_submitted" class="text_boxes_numeric" style="width:80px" readonly />
                            </td>
                        </tr>
                        <tr>
                            <td align="center">
                                <input type="checkbox" id="euro_incentive_chk" name="euro_incentive_chk" value="0" >
                            </td>
                            <td align="right">Euro Zone Incentive &nbsp;</br> (Yarn) &nbsp;</td>
                            <td>
                                <input type="text" name="euro_incentive" id="euro_incentive" class="text_boxes_numeric" style="width:80px"  onkeyup="calculate_total_amount()"/>
                            </td>
                        </tr>
                        <tr>
                            <td align="center">
                                <input type="checkbox" id="general_incentive_chk" name="general_incentive_chk" value="0" >
                            </td>
                            <td align="right">General Incentive &nbsp;</br> (Yarn) &nbsp;</td>
                            <td>
                                <input type="text" name="general_incentive" id="general_incentive" class="text_boxes_numeric" style="width:80px" onKeyUp="calculate_total_amount()"/>
                            </td>
                        </tr>
                        <tr>
                            <td align="center">
                                <input type="checkbox" id="market_submitted_chk" name="market_submitted_chk" value="0" onClick="calculate_amount(4)">
                            </td>
                            <td align="right">Submitted to Bank &nbsp;</br> (New Market 4% ) &nbsp;</td>
                            <td>
                                <input type="text" name="market_submitted" id="market_submitted" class="text_boxes_numeric" style="width:80px" readonly />
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" align="right"><strong>Total &nbsp;</strong></td>
                            <td>
                                <input type="text" name="total_amount" id="total_amount" class="text_boxes_numeric" style="width:80px;font-weight: bold;" readonly />
                            </td>
                        </tr>
                    </tbody>
                </table><br>
                <? echo load_submit_buttons( $permission, "fnc_cash_incentive_submission", 0,0,"form_reset_cise();reset_form('cashincentivesubmission_1','','','','','');",1); ?>
            </fieldset>
        </form>
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>