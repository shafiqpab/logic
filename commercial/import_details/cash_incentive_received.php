<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create for Cash Incentive Received Entry
Functionality	:
JS Functions	:
Created by		:	Nayem
Creation date 	: 	10-4-2021
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
echo load_html_head_contents("Cash Incentive Received Entry", "../../",1,1, $unicode,'','');
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
        var page_link = 'requires/cash_incentive_received_controller.php?action=system_popup&cbo_company_name='+cbo_company_name;
        var title = 'Search Cash Incentive Received Entry';

        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px,height=400px,center=1,resize=0,scrolling=0','../')
        emailwindow.onclose=function()
        {
			var theemail=this.contentDoc.getElementById("selected_id").value;
            if(theemail!="")
            {
                // freeze_window();
			    get_php_form_data(theemail, "populate_data_from_search_popup", "requires/cash_incentive_received_controller" );
                show_list_view( theemail, 'details_list_view', 'list_view', 'requires/cash_incentive_received_controller', '' ) ;
                $('#cbo_company_name').attr('disabled',true);
                $('#txt_lc_sc_no').attr('disabled',true);
                calculate_total();
				set_button_status(1, permission, 'fnc_cash_incentive_received',1);
            }
            release_freezing();
        }
    }

    function openmypage_submissionInfo()
    {
        if (form_validation('cbo_company_name','Company')==false )
        {
            return;
        }
        var cbo_company_name = $("#cbo_company_name").val();
        var page_link='requires/cash_incentive_received_controller.php?action=proceed_submission_popup_search&cbo_company_name='+cbo_company_name;
        var title='Export Proceeds Submission Entry Form';
        
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=400px,center=1,resize=1,scrolling=0','../');
        emailwindow.onclose=function()
        {
            var submission_id=this.contentDoc.getElementById("selected_id").value;
            if(trim(submission_id)!="")
            {
                freeze_window(5);
                get_php_form_data(submission_id, "populate_data_from_submission", "requires/cash_incentive_received_controller" );		
                release_freezing();
            }
                        
        }
    }
    
    function fn_inc_decr_row(rowid,type)
    {
        if(type=="increase")
        {
            var row = $("#tbl_details tbody tr:last").attr('id');
            row = row*1+1;
            var responseHtml = return_ajax_request_value(row, 'append_load_details_container', 'requires/cash_incentive_received_controller');
            $("#tbl_details tbody").append(responseHtml);
        }
        else if(type=="decrease")
        {
            var row = $("#tbl_details tbody tr").length;
            if(rowid*1!="" && row*1>1)
            {
                $("#tbl_details tbody tr#"+rowid).remove();
            }
            else
                return;
        }
        calculate_total_amount();
    }

    function fn_commercial_head_display(id)
    {
        if (form_validation('cbo_company_name','Company')==false )
        {
            return;
        }
        // $('#'+fld_name+"_"+seq_no).removeAttr('onblur');
        var page_link='requires/cash_incentive_received_controller.php?action=commercial_head_popup';
        var title='Account Head';
        
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=380px,height=400px,center=1,resize=1,scrolling=0','../');
        emailwindow.onclose=function()
        {
            var theform=this.contentDoc.forms[0];
            var hdn_head_id=this.contentDoc.getElementById("hdn_head_id").value;
            var hdn_head_val=this.contentDoc.getElementById("hdn_head_val").value;
            $('#cboHead_'+id).val(hdn_head_val);
            $('#cboHeadID_'+id).val(hdn_head_id);
        }
    }

    function calculate(i,field_id)
    {
        var DocumentCurrency= $('#documentCurrency_'+i).val()*1; 
        var ConversionRate= $('#conversionRate_'+i).val()*1;
        var DomesticCurrency= $('#domesticCurrency_'+i).val()*1;
        
        if(field_id=="documentCurrency_")
        {
            if(ConversionRate!="" && DomesticCurrency!="")
            {
                var DomsCurr=DocumentCurrency*ConversionRate;
                $('#domesticCurrency_'+i).val(DomsCurr.toFixed(2));
            }
            else if(ConversionRate=="" && DomesticCurrency!="")
            {
                var ConvRate=DomesticCurrency/DocumentCurrency;
                $('#conversionRate_'+i).val(ConvRate.toFixed(4));
            }
            else if(ConversionRate!="" && DomesticCurrency=="")
            {
                var DomsCurr=DocumentCurrency*ConversionRate;
                $('#domesticCurrency_'+i).val(DomsCurr.toFixed(2));
            }
            
        }
        else if(field_id=="conversionRate_")
        {
            if(DocumentCurrency!="" && DomesticCurrency!="")
            {
                var DomsCurr=DocumentCurrency*ConversionRate;
                $('#domesticCurrency_'+i).val(DomsCurr.toFixed(2));
            }
            else if(DocumentCurrency=="" && DomesticCurrency!="")
            {
                var DocCurr=DomesticCurrency/ConversionRate;
                $('#documentCurrency_'+i).val(DocCurr.toFixed(4));
            }
            else if(DocumentCurrency!="" && DomesticCurrency=="")
            {
                var DomsCurr=DocumentCurrency*ConversionRate;
                $('#domesticCurrency_'+i).val(DomsCurr.toFixed(2));
            }
            
        }
        else if(field_id=="domesticCurrency_")
        {
            if(DocumentCurrency!="" && ConversionRate!="")
            {
                var DocCurr=DomesticCurrency/ConversionRate;
                $('#documentCurrency_'+i).val(DocCurr.toFixed(2));
            }
            else if(DocumentCurrency=="" && ConversionRate!="")
            {
                var DocCurr=DomesticCurrency*ConversionRate;
                $('#documentCurrency_'+i).val(DocCurr.toFixed(2));
            }
            else if(DocumentCurrency!="" && ConversionRate=="")
            {
                var ConvRate=DocumentCurrency/DocumentCurrency;
                $('#conversionRate_'+i).val(ConvRate.toFixed(4));
            }
            
        }       
        
        calculate_total(i);
    }

    function calculate_total(i='')
    {
        var total_document_currency=0; var total_domestic_currency=0;
        $("#tbl_details").find('tbody tr').each(function()
        {
            var DocumentCurrency=$(this).find('input[name="documentCurrency[]"]').val()*1;
            var DomesticCurrency=$(this).find('input[name="domesticCurrency[]"]').val()*1;

            total_document_currency=(total_document_currency*1)+DocumentCurrency;
            total_domestic_currency=(total_domestic_currency*1)+DomesticCurrency;
        });
        $('#total_document_currency').val(total_document_currency);
        $('#total_domestic_currency').val(total_domestic_currency);
        if (total_document_currency>$("#txt_incentive_claim_value").val()){
            alert("Document Currency(USD) can not greater then Total Incentive Claim Value(USD)");
            $('#documentCurrency_'+i).val(0);
            calculate(i,'documentCurrency_'+i);
        }
    }

    function fnc_cash_incentive_received(operation) 
    {
        if (form_validation('cbo_company_name*txt_lc_sc_no*txt_received_date*txt_bill_no','Company Name*LC/SC No*Received Date*Bill No')==false)
        {
            return;
        }


        var i=1; var dataString='';var necessity_setup_chk=0;
        $("#tbl_details").find('tbody tr').each(function()
        {
            var cbo_acc_head=$(this).find('input[name="cboHeadID[]"]').val();
            var txt_document_currency=$(this).find('input[name="documentCurrency[]"]').val();
            var txt_conversion_rate=$(this).find('input[name="conversionRate[]"]').val();
            var txt_domestic_currency=$(this).find('input[name="domesticCurrency[]"]').val();
            if (cbo_acc_head==''){
                alert("Account Head can not Blank ");
                necessity_setup_chk=1;
                return;
            }
            dataString+='&cbo_acc_head_' + i + '=' + cbo_acc_head + '&txt_document_currency_' + i + '=' + txt_document_currency + '&txt_conversion_rate_' + i + '=' + txt_conversion_rate + '&txt_domestic_currency_' + i + '=' + txt_domestic_currency;
            i++;
        });
        
        if(necessity_setup_chk !=0){
            return;
        }else{
            var total_row = $("#tbl_details tbody tr").length;
            var data="action=save_update_delete&operation="+operation+'&total_row='+total_row+get_submitted_data_string('txt_system_id*update_id*cbo_company_name*submission_id*invoice_id*txt_received_date*txt_bill_no*txt_remarks*hidden_is_lc*hidden_is_lc_sc_id',"../../")+dataString;
            // alert(data);
            // return;
            freeze_window(operation);
            http.open("POST","requires/cash_incentive_received_controller.php",true);
            http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
            http.send(data);
            http.onreadystatechange = fnc_cash_incentive_received_reponse;
        }
    }

    function fnc_cash_incentive_received_reponse()
    {
        if(http.readyState == 4) 
		{
			var reponse=trim(http.responseText).split('**');
			if(parseInt(trim(reponse[0]))==0 || parseInt(trim(reponse[0]))==1)
			{
				document.getElementById('txt_system_id').value=reponse[1];
				document.getElementById('update_id').value=reponse[2];
                $('#cbo_company_name').attr('disabled',true);
                $('#txt_lc_sc_no').attr('disabled',true);
				set_button_status(1, permission, 'fnc_cash_incentive_received',1);
			}
			if(parseInt(trim(reponse[0]))==2)
			{
				reset_form('cashincentivereceived_1','','','','','');
                set_button_status(0, permission, 'fnc_cash_incentive_received',1);
			}
			show_msg(trim(reponse[0]));
			release_freezing();
		}
    }
    function form_reset_cir() 
    {
        $('#cbo_company_name').removeAttr('disabled');
        $('#txt_lc_sc_no').removeAttr('disabled');
    }
</script>
<body onLoad="set_hotkey()">
    <div align="center" style="width:100%;">
        <? echo load_freeze_divs ("../../",$permission); ?>
        <form name="cashincentivereceived_1" id="cashincentivereceived_1" autocomplete="off" >
            <fieldset style="width:850px;">
                <legend>Cash Incentive Received Entry</legend>
                <table width="830" border="0" cellpadding="0" cellspacing="2">
                    <tr width="800" >
                        <td colspan="3" align="right"><strong>System ID</strong></td> 
                        <td colspan="3">
                            <input style="width:140px;" type="text" title="Double Click to Search" onDblClick="openmypage_sys_no()" class="text_boxes" placeholder="Browse" name="txt_system_id" id="txt_system_id" readonly />
                            <input type="hidden" id="update_id">
                        </td>
                    <tr>
                        <td width="100" class="must_entry_caption">Company Name </td>
                        <td width="150">
                             <? echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business in(1,3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $cbo_country_id, "");?>
                        </td>
                        <td width="100" class="must_entry_caption">Submission ID</td>
                        <td width="150">
                            <input style="width:140px;" type="text" title="Double Click to Search" onDblClick="openmypage_submissionInfo()" class="text_boxes" placeholder="Browse" name="txt_submission" id="txt_submission" readonly />
                            <input type="hidden" id="submission_id">
                            <input type="hidden" id="invoice_id">
                            <input type="hidden" id="hidden_is_lc">
                            <input type="hidden" id="hidden_is_lc_sc_id">
                        </td>
                        <td width="100" class="must_entry_caption">Received Date</td>
                        <td width="150" >
                            <input style="width:140px " name="txt_received_date" id="txt_received_date" class="datepicker" readonly/>
                        </td>
                    </tr>
                    <tr>
                        <td class="must_entry_caption">Bill No</td>
                        <td>
                            <input type="text" name="txt_bill_no" id="txt_bill_no" style="width:140px" class="text_boxes">
                        </td>
                        <td>Buyer Name </td>
                        <td>
							<? echo create_drop_down("cbo_buyer_name", 150, "select id,buyer_name from lib_buyer comp where status_active =1 and is_deleted=0 $buyer_cond", "id,buyer_name", 1, "-- Select Buyer --", 0, "",1);
							?>
                        </td>
                        <td>Bank Name </td>
                        <td>
                            <?
                                if ($db_type==0)
                                {
                                    echo create_drop_down("cbo_bank_name", 150, "select concat(a.bank_name,' (', a.branch_name,')') as bank_name,id from lib_bank where is_deleted=0 and status_active=1 order by bank_name", "id,bank_name", 1, "-- Select Bank --", 0, "",1);
                                }
                                else
                                { 
                                    echo create_drop_down("cbo_bank_name", 150, "select (bank_name || ' (' || branch_name || ')' ) as bank_name,id from lib_bank where is_deleted=0 and status_active=1 order by bank_name", "id,bank_name", 1, "-- Select Bank --", 0, "",1);
                                }
							?>
                        </td>
                    </tr>
                    <tr>
                    	<td>LC/SC No </td>
                        <td>
                            <input style="width:140px;" type="text" class="text_boxes" placeholder="Display" name="txt_lc_sc_no" id="txt_lc_sc_no" readonly />
                        </td>
                        <td >Total Incentive Claim Value(USD)</td>
                        <td>
                            <input type="text" name="txt_incentive_claim_value" id="txt_incentive_claim_value" style="width:140px" class="text_boxes_numeric" placeholder="Display" readonly>
                        </td>
                        
                        <td>Remarks</td>
                        <td>
                            <input type="text" name="txt_remarks" id="txt_remarks" style="width:140px" class="text_boxes" >
                        </td>
                    </tr>  
                </table>
                <br>
				<table id="tbl_details" width="600px" border="0" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
					<thead>						
                        <th class="must_entry_caption">Account Head</th>
                        <th>Document Currency(USD)</th>
                        <th>Conversion Rate</th>
                        <th>Domestic Currency</th>
                        <th>Action</th>						
					</thead>
					<tbody id="list_view">
                        <? $i=1;?>
						<tr class="general" id='<?=$i;?>' align="center">
							<td>
                                <input type="text" name="cboHead[]" id="cboHead_<?=$i;?>" class="text_boxes" style="width:170px;"  onDblClick="fn_commercial_head_display(<?=$i;?>)"  placeholder="Browse" readonly />
                                <input type="hidden" name="cboHeadID[]" id="cboHeadID_<?=$i;?>"/>
							</td>
							<td>
                            	<input type="text" name="documentCurrency[]" id="documentCurrency_<?=$i;?>" class="text_boxes_numeric" style="width:100px;" onKeyUp="calculate(<?=$i;?>,'documentCurrency_')"/>
                            </td>
							<td>
                            	<input type="text" name="conversionRate[]" id="conversionRate_<?=$i;?>" class="text_boxes_numeric" style="width:100px;" onKeyUp="calculate(<?=$i;?>,'conversionRate_')"/>
                            </td>
							<td>
                            	<input type="text" name="domesticCurrency[]" id="domesticCurrency_<?=$i;?>" class="text_boxes_numeric" style="width:100px;" onKeyUp="calculate(<?=$i;?>,'domesticCurrency_')" />
                            </td>
                            <td width="65">
                                <input type="button" id="increaserow_<? echo $i;?>" style="width:30px" class="formbuttonplasminus" value="+" onClick="javascript:fn_inc_decr_row(<? echo $i;?>,'increase');" />
                                <input type="button" id="decreaserow_<? echo $i;?>" style="width:30px" class="formbuttonplasminus" value="-" onClick="javascript:fn_inc_decr_row(<? echo $i;?>,'decrease');" />
                            </td>
						</tr>
					</tbody>
                    <tfoot class="tbl_bottom">
                    	<tr>
                        	<td><strong>Total&nbsp;</strong></td>
                            <td>
                                <input style="width:100px;font-weight: bold;" type="text" class="text_boxes_numeric" id="total_document_currency" readonly />
                            </td>
                            <td></td>
                            <td>
                                <input style="width:100px;font-weight: bold;" type="text" class="text_boxes_numeric" id="total_domestic_currency" readonly />
                            </td>
                        </tr>
                    </tfoot>
				</table>
                <br>
                <? echo load_submit_buttons( $permission, "fnc_cash_incentive_received", 0,0,"form_reset_cir();reset_form('cashincentivereceived_1','','','','','');",1); ?>
            </fieldset>
        </form>
    </div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>