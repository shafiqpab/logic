<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create General Transfer Requisition
Functionality	:	
JS Functions	:
Created by		:	Nayem 
Creation date 	: 	14-09-2021
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

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("General Transfer Requisition Info","../../", 1, 1, '','',''); 
?>	

<script>
    var permission='<? echo $permission; ?>';
    if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";

    function openmypage_systemId()
    {
        var cbo_company_id = $('#cbo_company_id').val();
        var cbo_transfer_criteria = $('#cbo_transfer_criteria').val(); 
        var company_to = $('#cbo_company_id_to').val();

        if (form_validation('cbo_transfer_criteria*cbo_company_id','Transfer Criteria*Company')==false)
        {
            return;
        }
        
        var title = 'Transfer Requisition Info';	
        var page_link = 'requires/general_transfer_requisition_controller.php?cbo_company_id='+cbo_company_id+'&cbo_transfer_criteria='+cbo_transfer_criteria+'&company_to='+company_to+'&action=system_popup';
        
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=925px,height=380px,center=1,resize=1,scrolling=0','../');
        
        emailwindow.onclose=function()
        {
            var theform=this.contentDoc.forms[0];
            var transfer_id=this.contentDoc.getElementById("transfer_id").value; 
            
            get_php_form_data(transfer_id, "populate_data_from_transfer_master", "requires/general_transfer_requisition_controller" );
            show_list_view(transfer_id,'show_transfer_listview','div_transfer_item_list','requires/general_transfer_requisition_controller','');
            disable_enable_fields( 'cbo_transfer_criteria*cbo_company_id*cbo_company_id_to*txt_requisition_date*cbo_location_name_from*cbo_location_name_to*cbo_store_name_from*cbo_store_name_to', 1, '', '' );
            set_button_status(0, permission, 'fnc_general_transfer_requisition',1,1);
        }
    }

    function openmypage()
    {
        if (form_validation('cbo_company_id*cbo_store_name_from','Company Name*Item Catagory*Store Name')==false)
        {
            return;
        }
        else
        {
            var data=document.getElementById('cbo_company_id').value+"_"+document.getElementById('cbo_store_name_from').value;
            var page_link='requires/general_transfer_requisition_controller.php?action=account_order_popup&data='+data
            var title='Search Item Account';
            emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=880px,height=400px,center=1,resize=0,scrolling=0','')
            
            emailwindow.onclose=function()
            {
                var theemail=this.contentDoc.getElementById("item_1").value;
                // var re_order_lebel=this.contentDoc.getElementById("re_order_lebel").value;
                // alert(theemail); 
                if(theemail!="")
                {
                    var tot_row = $('#tbl_requisition_item tbody tr').length;
                    console.log(theemail);
                    var array = JSON.parse("[" + theemail + "]");
                    var row_num=tot_row;
                    var item_details=$('#txtItemAccount_'+row_num).val();
                    if(item_details=="")
                    {
                        $("#tbl_requisition_item tbody tr:last").remove();
                    }
                    tot_row = $('#tbl_requisition_item tbody tr').length;
                    var cbo_store_name=$('#cbo_store_name_from').val();
                    for(var cnt=0;cnt<array.length;cnt++)
                    {
                        var row=Number(Number(tot_row)+Number(cnt));
                        var data=array[cnt]+"**"+row+"**"+cbo_store_name;	
                        var list_view_item_details = return_global_ajax_value( data, 'load_php_popup_to_form', '', 'requires/general_transfer_requisition_controller');
                        $("#tbl_requisition_item tbody:last").append(list_view_item_details);
                    }
                    release_freezing();
                }
            }
        }
    }

    function fnc_general_transfer_requisition(operation)
    {
        if(operation==4)
        {
            var report_title=$( "div.form_caption" ).html();
            print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+report_title, "general_transfer_requisition_print", "requires/general_transfer_requisition_controller" ) ;
            return;
        }
        else if(operation==0 || operation==1 || operation==2)
        {
            var cbo_transfer_criteria=$('#cbo_transfer_criteria').val();
            if(cbo_transfer_criteria==1)
            {
                if( form_validation('cbo_transfer_criteria*cbo_company_id*cbo_company_id_to*txt_requisition_date*cbo_location_name_from*cbo_location_name_to*cbo_store_name_from*cbo_store_name_to','Transfer Criteria*From Company*To Company*Transfer Date*From Location*To Location*From Store*To Store')==false )
                {
                    return;
                }
            }
            else
            {
                if( form_validation('cbo_transfer_criteria*cbo_company_id*txt_requisition_date*cbo_location_name_from*cbo_location_name_to*cbo_store_name_from*cbo_store_name_to','Transfer Criteria*From Company*Transfer Date*From Location*To Location*From Store*To Store')==false )
                {
                    return;
                }
            }

            /*var current_date='<? echo date("d-m-Y"); ?>';
            if(date_compare($('#txt_requisition_date').val(), current_date)==false)
            {
                alert("Transfer Date Can not Be Greater Than Current Date");
                return;
            }*/
            var is_approved=$('#is_approved').val();		
            if(is_approved==1 || is_approved==3)
            {
                alert("Update not allowed. This Requisition is already Approved.");
                return;	
            }
            var tot_row=$('#tbl_requisition_item'+' tbody tr').length;
            var dataString1='';

            for(var i=1; i<=tot_row; i++)
            {
                if(trim($("#txtReqQnty_"+i).val())!="")
                {                
                    dataString1+=get_submitted_data_string('cboItemCategory_'+i+'*prodId_'+i+'*txtReqQnty_'+i+'*txtRemarks_'+i+'*updateDtlsId_'+i,'../../',i);
                }
            }

            var dataString = "txt_system_id*update_id*cbo_transfer_criteria*cbo_company_id*cbo_company_id_to*txt_requisition_date*txt_challan_no*cbo_ready_to_approved*cbo_location_name_from*cbo_location_name_to*cbo_store_name_from*cbo_store_name_to*txt_remarks";
            var data="action=save_update_delete&operation="+operation+get_submitted_data_string(dataString,"../../")+"&tot_row="+tot_row+dataString1;
            
            // alert(data);return;

            freeze_window(operation);
            http.open("POST","requires/general_transfer_requisition_controller.php",true);
            http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
            http.send(data);
            http.onreadystatechange = fnc_general_transfer_requisition_reponse;
        }
    }

    function fnc_general_transfer_requisition_reponse()
    {	
        if(http.readyState == 4) 
        {	  		
            var reponse=trim(http.responseText).split('**');		
            //alert(http.responseText);release_freezing();return;
            if (reponse[0] * 1 == 20 * 1) {
                alert(reponse[1]);
                release_freezing();
                return;
            }
			
			if (reponse[0]==11) {
                alert(reponse[1]);
                release_freezing();
				show_msg(reponse[0]); 	
                return;
            }
                    
            show_msg(reponse[0]); 	
                
            if(reponse[0]==0 || reponse[0]==1 || reponse[0]==2)
            {
                $("#update_id").val(reponse[1]);
                $("#txt_system_id").val(reponse[2]);
                show_list_view(reponse[1],'show_transfer_listview','div_transfer_item_list','requires/general_transfer_requisition_controller','');
                disable_enable_fields( 'cbo_transfer_criteria*cbo_company_id*cbo_company_id_to*txt_requisition_date*cbo_location_name_from*cbo_location_name_to*cbo_store_name_from*cbo_store_name_to', 1, '', '' );
                set_button_status(0, permission, 'fnc_general_transfer_requisition',1,1);
               

                var row_num=$('#tbl_requisition_item tbody tr').length;

				for(var i=1; i<=row_num; i++)
				{				
					$('#txtItemAccount_'+i).val('');
					$('#cboItemCategory_'+i).val(0);
					$('#txtItemGroupName_'+i).val('');
					$('#txtSubGroup_'+i).val('');
					$('#txtItemDescription_'+i).val('');
					$('#txtItemCode_'+i).val('');
					$('#txtItemSize_'+i).val('');
					$('#txtUom_'+i).val('');
					$('#txtReqQnty_'+i).val('');
					$('#txtStock_'+i).val('');
					$('#txtRemarks_'+i).val('');
				
				}
                $("#tbl_requisition_item tbody tr:not(:first)").remove();

            }	
            release_freezing();
        }
    }

    function reset_form_all()
    {
        disable_enable_fields('cbo_transfer_criteria*cbo_company_id*cbo_company_id_to*txt_requisition_date*cbo_location_name_from*cbo_location_name_to*cbo_store_name_from*cbo_store_name_to',0);
        reset_form('transferEntry_1','div_transfer_item_list','','','');
    }

    function active_inactive(str)
    {
        if(str==2) // Store to Store
        {
            $('#cbo_company_id_to').attr('disabled',true);		
        }
        else
        {
            $('#cbo_company_id_to').attr('disabled',false);
        }
    }

    function calculate_value(id)
    {
        var stock_qty = parseInt($('#txtStock_'+id).val());
        var transfered_qty = parseInt($('#txtReqQnty_'+id).val());
        if (transfered_qty  > stock_qty)
        {    
            $('#txtReqQnty_'+id).val('');
            alert('Over Qty Not Alowed');
        }
    }

    function company_onchange(str) 
    {
    	reset_form('transferEntry_1','div_transfer_item_list','','',"",'cbo_company_id*cbo_transfer_criteria*txt_requisition_date');
    	var company = $("#cbo_company_id").val();
        var transfer_criteria = $("#cbo_transfer_criteria").val();

        if (transfer_criteria == 1){
            load_drop_down( 'requires/general_transfer_requisition_controller',company+"_"+transfer_criteria, 'load_drop_down_to_company', 'to_company_td' );
        }
    	else if(transfer_criteria == 2)
    	{    		
            load_drop_down( 'requires/general_transfer_requisition_controller',company+"_"+transfer_criteria, 'load_drop_down_to_company', 'to_company_td' );
            $("#cbo_company_id_to").val(company);
            $('#cbo_company_id_to').attr('disabled',true);
    	}
        else
        {
            $("#cbo_company_id_to").val(company);
        }    
    }

</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../",$permission);  ?>    		 
    <form name="transferEntry_1" id="transferEntry_1" autocomplete="off" >
        <div style="width:1200px;">
            <fieldset style="width:1200px;">
                <legend>General Transfer Requisition</legend>
                <br>
                <fieldset style="width:860px;">
                    <legend>General Transfer</legend>
                    <table width="850" cellspacing="2" cellpadding="2" border="0" id="tbl_master">
                        <tr>
                            <td colspan="3" align="right"><strong>System ID</strong><input type="hidden" name="update_id" id="update_id" /></td>
                            <td colspan="3" align="left">
                                <input type="text" name="txt_system_id" id="txt_system_id" class="text_boxes" style="width:150px;" placeholder="Double click to search" onDblClick="openmypage_systemId();" readonly />
                            </td>
                        </tr>
                        <tr>
                            <td colspan="6">&nbsp;</td>
                        </tr>
                        <tr>
                            <td class="must_entry_caption">Transfer Criteria</td>
                            <td>
                                <?
                                    echo create_drop_down("cbo_transfer_criteria", 160,$item_transfer_criteria,"", 1,"-- Select --",'0',"company_onchange(1);active_inactive(this.value);",'','1,2');
                                ?>
                            </td>
                            <td class="must_entry_caption">From Company</td>
                            <td>
                                <? 
                                    echo create_drop_down( "cbo_company_id", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0,"load_drop_down('requires/general_transfer_requisition_controller',this.value, 'load_drop_down_location_from','from_location_td');load_drop_down('requires/general_transfer_requisition_controller',this.value, 'load_drop_down_location_to','to_location_td');company_onchange(2);" );
                                ?>
                            </td>
                            <td class="must_entry_caption">To Company</td>
                            <td id="to_company_td">
                                <? 
                                    echo create_drop_down( "cbo_company_id_to", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "load_drop_down('requires/general_transfer_requisition_controller',this.value,'load_drop_down_location_to','to_location_td' );","" );
                                ?>
                            </td>
                        </tr>
                        <tr>    
                            <td class="must_entry_caption">Requisition Date</td>
                            <td>
                                <input type="text" name="txt_requisition_date" id="txt_requisition_date" value="<? echo date("d-m-Y");?>" class="datepicker" style="width:148px;" readonly placeholder="Select Date" />
                            </td>                    	 
                            <td class="must_entry_caption">From Location</td>
                            <td id="from_location_td">
                                <? echo create_drop_down( "cbo_location_name_from", 160, $blank_array,"", 1, "--Select Location--", 0,"" );?>
                            </td>
                            <td class="must_entry_caption">To Location</td>
                            <td id="to_location_td">
                                <? echo create_drop_down( "cbo_location_name_to", 160, $blank_array,"", 1, "--Select Location--", 0,"" );?>
                            </td>
                        </tr>
                        <tr>    
                            <td>Manual Challan No.</td>
                            <td>
                                <input type="text" name="txt_challan_no" id="txt_challan_no" class="text_boxes" style="width:148px;" maxlength="20" title="Maximum 20 Character" />
                            </td> 
                            <td width="100" class="must_entry_caption">From Store</td>
                            <td id="from_store_td">
                                <? echo create_drop_down( "cbo_store_name_from", 160, $blank_array,"", 1, "--Select Store--", 0, "" );?>	
                            </td> 
                            <td width="100" class="must_entry_caption">To Store</td>
                            <td id="to_store_td">
                                <? echo create_drop_down( "cbo_store_name_to", 160, $blank_array,"", 1, "--Select Store--", 0, "" );?>	
                            </td>                 	 
                        </tr>
                        <tr>
                            <td>Ready To Approved</td>
                            <td>
                                <? echo create_drop_down( "cbo_ready_to_approved", 160, $yes_no,"", 1, "-- Select--", 2, "","","" );?>
                                <input type="hidden" name="store_update_upto" id="store_update_upto">
                                <input type="hidden" name="is_approved" id="is_approved" value="">
                            </td>
                            <td>Remarks</td>
                            <td>
                                <input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:148px;"/>
                            </td>
                            <td></td>
                            <td>
                                <span id="approved" style="text-align:center; font-size:24px; color:#FF0000;"></span> 
                            </td>
                        </tr>
                    </table>
                </fieldset>
                <br>
                <fieldset style="margin-top:10px">
                    <legend>Item Details</legend>
                    <div style="width: 1200px;">
                        <table class="rpt_table" width="1200" cellpadding="0" cellspacing="0" border="1" rules="all" >
                            <thead>
                                <tr>
                                    <th width="100">Item Account</th>
                                    <th width="100" class="must_entry_caption">Item Category</th>
                                    <th width="100">Item Group</th>
                                    <th width="100">Item Sub. Group</th>
                                    <th width="280">Item Description</th>
                                    <th width="150">Item Code</th>
                                    <th width="70">Item Size</th>
                                    <th width="70">Cons. UOM</th>
                                    <th width="70" class="must_entry_caption">Req. Qty.</th>
                                    <th width="70">Stock</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                        </table>
                        <div id="item_category_div" style="max-height:200px; overflow-y:scroll; width: 1220px;">
                            <table class="rpt_table" width="1200" cellpadding="0" cellspacing="0" border="1" rules="all"  id="tbl_requisition_item">
                                <tbody>
                                    <tr class="general" >
                                        <td width="100">
                                            <input type="text" name="txtItemAccount_1" id="txtItemAccount_1" class="text_boxes" value="" style="width:85px;" placeholder="Double click"  onDblClick="openmypage()" readonly />
                                        </td>
                                        <td width="100">
                                            <? echo create_drop_down( "cboItemCategory_1", 95,$general_item_category,"", 1, "-- Select --", $selected, "",1,"$item_cate_credential_cond",""); ?>
                                        </td>
                                        <td width="100">
                                            <input type="text" name="txtItemGroupName_1" id="txtItemGroupName_1" class="text_boxes" value="" style="width:85px;" readonly/>
                                        </td>
                                        <td width="100">
                                            <input type="text" name="txtSubGroup_1" id="txtSubGroup_1" class="text_boxes" value="" style="width:85px;" maxlength="200" readonly />
                                        </td>
                                        <td width="280">
                                            <input type="text" name="txtItemDescription_1" id="txtItemDescription_1" class="text_boxes" value="" style="width:265px" readonly />
                                        </td>
                                        <td width="150">
                                            <input type="text" name="txtItemCode_1" id="txtItemCode_1" class="text_boxes" value="" style="width:135px;"  readonly />
                                        </td>
                                        <td width="70">
                                            <input type="text" name="txtItemSize_1" id="txtItemSize_1" class="text_boxes" value="" style="width:55px;"  readonly />
                                        </td>
                                        <td id="tduom_1" width="70">
                                            <input type="text" name="txtUom_1" id="txtUom_1" class="text_boxes" value="" style="width:55px;"  readonly />
                                        </td>
                                        <td width="70">
                                            <input type="text" name="txtReqQnty_1" id="txtReqQnty_1" class="text_boxes_numeric" autocomplete="off" value="" style="width:55px;" onKeyUp="calculate_value(1)"/>
                                        </td>
                                        <td width="70">
                                            <input type="text" name="txtStock_1" id="txtStock_1" class="text_boxes_numeric" value="" style="width:55px;"  readonly />
                                        </td>
                                        <td>
                                            <input type="text" name="txtRemarks_1" id="txtRemarks_1" class="text_boxes" value="" style="width:65px;" />
                                            <input type="hidden" name="prodId_1" id="prodId_1" value="" />
                                            <input type="hidden" name="updateDtlsId_1" id="updateDtlsId_1" />
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </fieldset>
                <table width="850" cellspacing="2" cellpadding="2" border="0" id="tbl_dtls">
                    <tr>
                        <td align="center" colspan="3" class="button_container" width="100%">
                            <?
                                echo load_submit_buttons($permission, "fnc_general_transfer_requisition", 0,1,"reset_form_all()",1);
                            ?>
                        </td>
                    </tr>
                </table>
                <div style="width:980px;float:left;" id="div_transfer_item_list"></div>
            </fieldset>
        </div>
	</form>
</div>  
</body>  
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
