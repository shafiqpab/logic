<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Dyes and Chemical Transfer Requisition
Functionality	:	
JS Functions	:
Created by		:	Nayem 
Creation date 	: 	08-01-2022
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
$userCredential = sql_select("SELECT unit_id as company_id,item_cate_id FROM user_passwd where id=$user_id");
$item_cate_id = $userCredential[0][csf('item_cate_id')];
$company_id = $userCredential[0][csf('company_id')];

if($item_cate_id !='') {
	$cre_cat_arr=explode(",",$item_cate_id);
	$selected_category=array( '5', '6', '7', '23' );
	$filteredArr = array_intersect( $cre_cat_arr, $selected_category );
    $item_cate_credential_cond = implode(",",$filteredArr);
}
else
{
	$item_cate_credential_cond="5,6,7,23";
}

//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Dyes and Chemical Transfer Requisition","../../", 1, 1, '','',''); 
?>	

<script>
    var permission='<? echo $permission; ?>';
    if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";

    function openmypage_systemId()
    {
        var cbo_company_id = $('#cbo_company_id').val();
        var cbo_transfer_criteria = $('#cbo_transfer_criteria').val();

        if (form_validation('cbo_transfer_criteria*cbo_company_id','Transfer Criteria*Company')==false)
        {
            return;
        }
        
        var title = 'Transfer Requisition Info';	
        var page_link = 'requires/chemical_dyes_transfer_requisition_controller.php?cbo_company_id='+cbo_company_id+'&cbo_transfer_criteria='+cbo_transfer_criteria+'&action=system_popup';
        
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=925px,height=380px,center=1,resize=1,scrolling=0','../');
        
        emailwindow.onclose=function()
        {
            var theform=this.contentDoc.forms[0];
            var transfer_id=this.contentDoc.getElementById("transfer_id").value; 
            
            get_php_form_data(transfer_id, "populate_data_from_transfer_master", "requires/chemical_dyes_transfer_requisition_controller" );
            show_list_view(transfer_id,'show_transfer_listview','div_transfer_item_list','requires/chemical_dyes_transfer_requisition_controller','');
            disable_enable_fields( 'cbo_transfer_criteria*cbo_company_id*cbo_company_id_to*txt_requisition_date*cbo_location_name_from*cbo_location_name_to*cbo_store_name_from*cbo_store_name_to', 1, '', '' );
            set_button_status(0, permission, 'fnc_chemical_dyes_transfer_requisition',1,1);
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
            var data=document.getElementById('cbo_company_id').value+"_"+document.getElementById('cbo_store_name_from').value+"_"+document.getElementById('variable_lot').value;
            var page_link='requires/chemical_dyes_transfer_requisition_controller.php?action=account_order_popup&data='+data
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
                        var list_view_item_details = return_global_ajax_value( data, 'load_php_popup_to_form', '', 'requires/chemical_dyes_transfer_requisition_controller');
                        $("#tbl_requisition_item tbody:last").append(list_view_item_details);
                    }
					$('#cbo_store_name_from').attr('disabled',true);
                    release_freezing();
                }
            }
        }
    }

    function fnc_chemical_dyes_transfer_requisition(operation)
    {
        if(operation==4)
        {
            var report_title=$( "div.form_caption" ).html();
            print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+report_title, "general_transfer_requisition_print", "requires/chemical_dyes_transfer_requisition_controller" ) 
            return;
        }
        else if(operation==0 || operation==1 || operation==2)
        {
            /*if(operation==2)
            {
                show_msg('13');
                return;
            }*/
            
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

            var is_approved=$('#is_approved').val();		
            if(is_approved==1 || is_approved==3)
            {
                alert("Update not allowed. This Requisition is already Approved.");
                return;	
            }
            var tot_row=$('#tbl_requisition_item'+' tbody tr').length;
            var dataString1='';
			var variable_lot=$('#variable_lot').val();
            for(var i=1; i<=tot_row; i++)
            {
                if(trim($("#txtReqQnty_"+i).val())!="")
                {
					if(variable_lot==1)
					{
						if(trim($("#txtLot_"+i).val())!="")
						{
							dataString1+=get_submitted_data_string('cboItemCategory_'+i+'*prodId_'+i+'*txtReqQnty_'+i+'*txtRemarks_'+i+'*updateDtlsId_'+i+'*txtLot_'+i,'../../',i);
						}
					}
					else
					{
						dataString1+=get_submitted_data_string('cboItemCategory_'+i+'*prodId_'+i+'*txtReqQnty_'+i+'*txtRemarks_'+i+'*updateDtlsId_'+i,'../../',i);
					}
                    
                }
            }

            var dataString = "txt_system_id*update_id*cbo_transfer_criteria*cbo_company_id*cbo_company_id_to*txt_requisition_date*txt_challan_no*cbo_ready_to_approved*cbo_location_name_from*cbo_location_name_to*cbo_store_name_from*cbo_store_name_to*txt_remarks";
            var data="action=save_update_delete&operation="+operation+get_submitted_data_string(dataString,"../../")+"&tot_row="+tot_row+dataString1;
            
            // alert(data);return;
            freeze_window(operation);
            http.open("POST","requires/chemical_dyes_transfer_requisition_controller.php",true);
            http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
            http.send(data);
            http.onreadystatechange = fnc_chemical_dyes_transfer_requisition_reponse;
        }
    }

    function fnc_chemical_dyes_transfer_requisition_reponse()
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
                
            if(reponse[0]==0 || reponse[0]==1 || (reponse[0]==2 && reponse[3]==1))
            {
                $("#update_id").val(reponse[1]);
                $("#txt_system_id").val(reponse[2]);
                show_list_view(reponse[1],'show_transfer_listview','div_transfer_item_list','requires/chemical_dyes_transfer_requisition_controller','');
                disable_enable_fields( 'cbo_transfer_criteria*cbo_company_id*cbo_company_id_to*txt_requisition_date*cbo_location_name_from*cbo_location_name_to*cbo_store_name_from*cbo_store_name_to', 1, '', '' );
                set_button_status(0, permission, 'fnc_chemical_dyes_transfer_requisition',1,1);
               

                var row_num=$('#tbl_requisition_item tbody tr').length;

				for(var i=1; i<=row_num; i++)
				{				
					$('#txtItemAccount_'+i).val('');
					$('#cboItemCategory_'+i).val(0);
					$('#txtItemGroupName_'+i).val('');
					// $('#txtSubGroup_'+i).val('');
					$('#txtItemDescription_'+i).val('');
					$('#txtLot_'+i).val('');
					// $('#txtItemCode_'+i).val('');
					// $('#txtItemSize_'+i).val('');
					$('#txtUom_'+i).val('');
					$('#txtReqQnty_'+i).val('');
					$('#txtStock_'+i).val('');
					$('#txtRemarks_'+i).val('');
				
				}
                $("#tbl_requisition_item tbody tr:not(:first)").remove();

            }	
            if(reponse[0]==2 && reponse[3]==2)
            {
                release_freezing();
				location.reload();
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
    	reset_form('transferEntry_1','div_transfer_item_list','','',"",'cbo_company_id*cbo_transfer_criteria*txt_requisition_date*variable_lot');
    	var company = $("#cbo_company_id").val();
        var transfer_criteria = $("#cbo_transfer_criteria").val();

        if (transfer_criteria == 1){
            load_drop_down( 'requires/chemical_dyes_transfer_requisition_controller',company+"_"+transfer_criteria, 'load_drop_down_to_company', 'to_company_td' );
        }
    	else if(transfer_criteria == 2)
    	{    		
            load_drop_down( 'requires/chemical_dyes_transfer_requisition_controller',company+"_"+transfer_criteria, 'load_drop_down_to_company', 'to_company_td' );
            $("#cbo_company_id_to").val(company);
            $('#cbo_company_id_to').attr('disabled',true);
    	}
        else
        {
            $("#cbo_company_id_to").val(company);
        } 
		
		   
    }
	
	function lib_variable_check(str)
	{
		var company = $("#cbo_company_id").val();
		var company_to = $("#cbo_company_id_to").val();
		//alert(str);
		if(str==2)
		{
			var lib_variable=return_global_ajax_value( company+"__"+company_to, 'populate_data_lib_data', '', 'requires/chemical_dyes_transfer_requisition_controller');
			var lib_variable_ref=lib_variable.split("__");
            var from_com=lib_variable_ref[0]*1;
            var to_com=lib_variable_ref[1]*1;
			if(from_com != to_com)
			{
				alert("Lot Maintain Variable Must Be Same In Both Company");
				$("#cbo_company_id_to").val(0);
				$("#variable_lot").val(lib_variable_ref[0]);
				return;
			}
		}
		else
		{
			var lib_variable=return_global_ajax_value( company, 'populate_data_lib_data', '', 'requires/chemical_dyes_transfer_requisition_controller');
			var lib_variable_ref=lib_variable.split("__");
		}
		$("#variable_lot").val(lib_variable_ref[0]);
	}

</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../",$permission);  ?>    		 
    <form name="transferEntry_1" id="transferEntry_1" autocomplete="off" >
        <div style="width:880px;">   
            <fieldset style="width:860px;">
                <legend>Dyes And Chemical Transfer Requisition</legend>
                <br>
                <fieldset style="width:860px;">
                    <legend>Transfer Requesition</legend>
                    <table width="850" cellspacing="2" cellpadding="2" border="0" id="tbl_master">
                        <tr>
                            <td colspan="3" align="right"><strong>Transfer Req ID</strong><input type="hidden" name="update_id" id="update_id" /></td>
                            <td colspan="3" >
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
                                    echo create_drop_down( "cbo_company_id", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0,"load_drop_down('requires/chemical_dyes_transfer_requisition_controller',this.value, 'load_drop_down_location_from','from_location_td');load_drop_down('requires/chemical_dyes_transfer_requisition_controller',this.value, 'load_drop_down_location_to','to_location_td');company_onchange(2);lib_variable_check(1);" );
                                ?>
                                <input type="hidden" id="variable_lot" name="variable_lot" />
                            </td>
                            <td class="must_entry_caption">To Company</td>
                            <td id="to_company_td">
                                <? 
                                    echo create_drop_down( "cbo_company_id_to", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "load_drop_down('requires/chemical_dyes_transfer_requisition_controller',this.value,'load_drop_down_location_to','to_location_td' );lib_variable_check(2);","" );
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
                            <td colspan="3">
                                <input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:440px;"/>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="6" align="center">
                                <span id="approved" style="text-align:center; font-size:24px; color:#FF0000;"></span> 
                            </td>
                        </tr>
                    </table>
                </fieldset>
                <br>
                <fieldset style="width:850px;margin-top:10px">
                    <legend>Item Requisition  Details</legend>
                    <table class="rpt_table" width="810" cellspacing="1">
                        <thead>
                            <tr>
                                <th width="100">Item Account</th>
                                <th width="100" class="must_entry_caption">Item Category</th>
                                <th width="100">Item Group</th>
                                <th width="120">Item Description</th>
                                <th width="70">Lot</th>
                                <th width="70">Order UOM</th>
                                <th width="70" class="must_entry_caption">Req. Qty.</th>
                                <th width="70">Stock</th>
                                <th >Remarks</th>
                            </tr>
                        </thead>
                    </table>
                    <div id="item_category_div" style="max-height:200px; overflow-y:scroll;" width="810">
                        <table class="rpt_table" width="792" cellspacing="1" id="tbl_requisition_item">
                            <tbody>
                                <tr class="general" >
                                    <td>
                                        <input type="text" name="txtItemAccount_1" id="txtItemAccount_1" class="text_boxes" value="" style="width:80PX;" placeholder="Double click"  onDblClick="openmypage()" readonly />
                                    </td>
                                    <td>
                                        <? echo create_drop_down( "cboItemCategory_1", 90,$item_category,"", 1, "-- Select --", $selected, "",1,"$item_cate_credential_cond",""); ?> 
                                    </td>
                                    <td>
                                        <input type="text" name="txtItemGroupName_1" id="txtItemGroupName_1" class="text_boxes" value="" style="width:80px;" readonly/>
                                    </td>
                                    <td>
                                        <input type="text" name="txtItemDescription_1" id="txtItemDescription_1" class="text_boxes" value="" style="width:100px;" readonly />
                                    </td>
                                    <td>
                                        <input type="text" name="txtLot_1" id="txtLot_1" class="text_boxes" value="" style="width:50px;"  readonly />
                                    </td>
                                    <td id="tduom_1">
                                        <input type="text" name="txtUom_1" id="txtUom_1" class="text_boxes" value="" style="width:50px;"  readonly />
                                    </td> 
                                    <td>
                                        <input type="text" name="txtReqQnty_1" id="txtReqQnty_1" class="text_boxes_numeric" autocomplete="off" value="" style="width:50px;" onKeyUp="calculate_value(1)"/>
                                    </td>
                                    <td>
                                        <input type="text" name="txtStock_1" id="txtStock_1" class="text_boxes_numeric" value="" style="width:50px;"  readonly />
                                    </td>
                                    <td>
                                        <input type="text" name="txtRemarks_1" id="txtRemarks_1" class="text_boxes" value="" style="width:50px;" />
                                        <input type="hidden" name="prodId_1" id="prodId_1" value="" />
                                        <input type="hidden" name="updateDtlsId_1" id="updateDtlsId_1" />
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </fieldset>
                <table width="850" cellspacing="2" cellpadding="2" border="0" id="tbl_dtls">
                    <tr>
                        <td align="center" colspan="3" class="button_container" width="100%">
                            <?
                                echo load_submit_buttons($permission, "fnc_chemical_dyes_transfer_requisition", 0,1,"reset_form_all()",1);
                            ?>
                        </td>
                    </tr>
                </table>
                <div id="div_transfer_item_list" style="float:left; width:980px;"></div>
            </fieldset>
        </div>
	</form>
</div>  
</body>  
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>

<script>
var company_id=$("#cbo_company_id").val();
if(company_id>0) lib_variable_check(company_id);
</script> 
 
</html>
