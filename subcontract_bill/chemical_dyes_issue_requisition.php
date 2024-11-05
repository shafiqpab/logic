<?
/*************************************************Comments***********************************
* Purpose			: 	This form will create Dyes And Chemical Issue Requisition Entry     *
* Functionality	:                                                                           *	
* JS Functions	:                                                                           *
* Created by		:	Tajik                                                               	*
* Creation date 	: 	03-10-2017                                                          *
* Updated by 		:                                           							*		
* Update date		:                                                          				*		   
* QC Performed BY	:                                                                       *		
* QC Date			:                                                                       *	
* Comments		:                                                                           *
********************************************************************************************/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
echo load_html_head_contents("SubCon Dyes And Chemical Issue Requisition","../", 1, 1, $unicode,1,1); 
//--------------------------------------------------------------------------------------------------------------------

?>	

<script>
	var permission='<? echo $permission; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";

	function rcv_basis_reset()
	{
		//document.getElementById('cbo_receive_basis').value=0;
		reset_form('chemicaldyesissuerequisition_1','list_container_recipe_items*recipe_items_list_view*last_update_message','','','','cbo_company_name');
	} 

	// receipe no poup
	function openmypage_labdipNo()
	{
		var cbo_company_id = $('#cbo_company_name').val();
		var txt_recipe_id = $('#txt_recipe_id').val();
		
		if (form_validation('cbo_company_name','Company')==false)
		{
			return;
		}
		else
		{ 	
			var title = 'Recipe No Selection Form';	
			var page_link = 'requires/chemical_dyes_issue_requisition_controller.php?cbo_company_id='+cbo_company_id+'&recipe_id='+txt_recipe_id+'&action=labdip_popup';
			  
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=410px,center=1,resize=1,scrolling=0','');
			
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
				var recipe_id=this.contentDoc.getElementById("hidden_recipe_id").value;	
				var subprocess_id=this.contentDoc.getElementById("hidden_subprocess_id").value;	
				
				if(recipe_id!="")
				{
					freeze_window(5);
					get_php_form_data(recipe_id, "populate_data_from_recipe_popup", "requires/chemical_dyes_issue_requisition_controller" );
					var cbo_company_id= $('#cbo_company_name').val();
					show_list_view(cbo_company_id+'**'+subprocess_id+"**"+recipe_id, 'item_details', 'list_container_recipe_items', 'requires/chemical_dyes_issue_requisition_controller', '');
					document.getElementById('last_update_message').innerHTML = '';
					$('#is_apply_last_update').val(1);
					release_freezing();
				} 
			}
		}
	}
	
	function apply_last_update()
	{
		if( form_validation('txt_mrr_no','Requisition Number')==false )
		{
			return;
		}
		
		var recipe_id= $('#txt_recipe_id').val();
		var cbo_company_id= $('#cbo_company_name').val();
		if(recipe_id!="")
		{
			freeze_window(5);
			get_php_form_data(recipe_id, "populate_data_from_recipe_popup", "requires/chemical_dyes_issue_requisition_controller" );
			var subprocess_id=return_global_ajax_value(recipe_id, 'get_subprocess_id', '', 'requires/chemical_dyes_issue_requisition_controller');
			show_list_view(cbo_company_id+'**'+subprocess_id+"**"+recipe_id,'item_details','list_container_recipe_items','requires/chemical_dyes_issue_requisition_controller','');
			$('#is_apply_last_update').val(1);
			release_freezing();
		} 
	}
	
	function fnc_chemical_dyes_issue_requisition(operation)
	{
		if(operation==4)
		{
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#cbo_location_name').val(), "chemical_dyes_issue_requisition_print", "requires/chemical_dyes_issue_requisition_controller" );
			return;
		}
		else if(operation==0 || operation==1 || operation==2)
		{
			if(operation==2)
			{
				show_msg('13');
				return;
			}
		
			if( form_validation('cbo_company_name*txt_requisition_date*txt_recipe_no','Company Name*Requisition Date*Recipe No')==false )
			{
				return;
			}
			var mst_data=get_submitted_data_string('txt_mrr_no*update_id*cbo_company_name*cbo_location_name*txt_requisition_date*cbo_receive_basis*txt_batch_id*txt_recipe_id*cbo_method*machine_id*is_apply_last_update',"../");
			
			var row_num=$('#tbl_list_search tbody tr').length;
			for (var i=1; i<=row_num; i++)
			{
				mst_data=mst_data+get_submitted_data_string('txt_prod_id_'+i+'*txt_item_cat_'+i+'*cbo_dose_base_'+i+'*txt_ratio_'+i+'*txt_recipe_qnty_'+i+'*txt_adj_per_'+i+'*cbo_adj_type_'+i+'*txt_reqn_qnty_'+i+'*reqn_qnty_edit_'+i+'*updateIdDtls_'+i+'*txt_subprocess_id_'+i+'*txt_seq_no_'+i,"../",i)	
			}
			
			var data="action=save_update_delete&operation="+operation+'&total_row='+row_num+mst_data;
			freeze_window(operation);
			http.open("POST","requires/chemical_dyes_issue_requisition_controller.php",true);
			http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			http.send(data);
			http.onreadystatechange = fnc_chemical_dyes_issue_requisition_reponse;
		}
	}

	function fnc_chemical_dyes_issue_requisition_reponse()
	{	
		if(http.readyState == 4) 
		{   
			var reponse=trim(http.responseText).split('**');	
			show_msg(reponse[0]);
			
			if((reponse[0]==0 || reponse[0]==1))
			{
				document.getElementById('txt_mrr_no').value = reponse[1];
				document.getElementById('update_id').value = reponse[2];

				$('#cbo_company_name').attr('disabled','disabled');
				document.getElementById('last_update_message').innerHTML = '';
				//reset_form( '', 'list_container_recipe_items', '', '', '', '' ); 
				show_list_view(reponse[2], 'item_details_for_update', 'list_container_recipe_items', 'requires/chemical_dyes_issue_requisition_controller', '');
				set_button_status(1, permission, 'fnc_chemical_dyes_issue_requisition',1,1);
			}
			release_freezing();	
		}
	}

	function open_mrrpopup()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();	
		var page_link='requires/chemical_dyes_issue_requisition_controller.php?action=mrr_popup&company='+company; 
		var title="Requisition Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1020px,height=370px,center=1,resize=0,scrolling=0','')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var reqnId=this.contentDoc.getElementById("hidden_sys_id").value; 
			
			reset_form('','list_container_recipe_items*recipe_items_list_view*last_update_message','','','','');
			
			get_php_form_data(reqnId, "populate_data_from_data", "requires/chemical_dyes_issue_requisition_controller");
			show_list_view(reqnId, 'item_details_for_update', 'list_container_recipe_items', 'requires/chemical_dyes_issue_requisition_controller', '');
		}
	}

	function calculate_requs_qty(i)
	{
		var txt_adj_per = $("#txt_adj_per_"+i).val()*1;
		var cbo_adj_type = $("#cbo_adj_type_"+i).val();	
	    var recipe_qnty=$("#txt_recipe_qnty_"+i).val()*1;	
		
		var requisition_qty=0;
		
		var adj_qty=(txt_adj_per*recipe_qnty)/100;

		if(cbo_adj_type==1)
		{
			requisition_qty=recipe_qnty+adj_qty;
		}
		else if(cbo_adj_type==2)
		{
			requisition_qty=recipe_qnty-adj_qty;
		}
		else
		{
			var requisition_qty=recipe_qnty;
		}

		$("#reqn_qnty_edit_"+i).val(requisition_qty.toFixed(6));
		$("#txt_reqn_qnty_"+i).val(requisition_qty.toFixed(6));
		//$("#reqn_qnty_edit_"+i).val(number_format_common(requisition_qty,5,0));
		//$("#txt_reqn_qnty_"+i).val(number_format_common(requisition_qty,5,0));
	}
	
	function fn_machine_seach()
	{
		var cbo_company_id = $('#cbo_company_name').val();
		var txt_batch_id = $('#txt_batch_id').val();
		
		if (form_validation('cbo_company_name*txt_batch_no','Company*Batch No')==false)
		{
			return;
		}
		else
		{ 	
			var title = 'Machine No Selection Form';	
			var page_link = 'requires/chemical_dyes_issue_requisition_controller.php?cbo_company_id='+cbo_company_id+'&txt_batch_id='+txt_batch_id+'&action=machineNo_popup';
			  
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=755px,height=350px,center=1,resize=1,scrolling=0','');
			
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
				var machine_id=this.contentDoc.getElementById("hidden_machine_id").value;	
				var machine_name=this.contentDoc.getElementById("hidden_machine_name").value;	
				
				$('#machine_id').val(machine_id);
				$('#txt_machine_no').val(machine_name);
			}
		}
	
	}
	
	function without_rate_fnc()
	{
		if( form_validation('txt_mrr_no*cbo_company_name*txt_requisition_date*txt_recipe_no','Requisition Number*Company Name*Requisition Date*Recipe No')==false )
		{
			return;
		}
		var report_title=$( "div.form_caption" ).html();
		print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+report_title+'*'+$('#cbo_location_name').val(), "chemical_dyes_issue_requisition_without_rate_print", "requires/chemical_dyes_issue_requisition_controller" );
		return;	
	}
	
	function print_adding_topping(type)
	{
		if( form_validation('txt_mrr_no','Requisition Number')==false )
		{
			return;
		}
		var report_title=$( "div.form_caption" ).html();
		if(type==1)
		{
	  		print_report($('#update_id').val()+'*'+report_title, "print_adding_topping_without_rate_print", "requires/chemical_dyes_issue_requisition_controller" );
	  		return;	
		}
		else
		{
			print_report($('#update_id').val()+'*'+report_title, "print_adding_topping", "requires/chemical_dyes_issue_requisition_controller" );
			return;	
		}
	}
	function fnResetForm()
	{
		//alert(33);
		$("#cbo_company_name").attr("disabled",false); 
		//disable_enable_fields(\'cbo_company_id*cbo_company_id\',0)
		//reset_form('slittingsqueezing_1','','','','$(\'#list_fabric_desc_container tr:not(:first)\').remove();');
		reset_form('chemicaldyesissuerequisition_1','list_container_recipe_items*recipe_items_list_view*last_update_message','','','','');
	}
</script>
</head>
<body onLoad="set_hotkey()">
	<div style="width:100%;" align="center">
		<? echo load_freeze_divs ("../",$permission);  ?>   		 
        <form name="chemicaldyesissuerequisition_1" id="chemicaldyesissuerequisition_1" autocomplete="off" > 
    		<div style="width:1000px;">       
            	<fieldset style="width:1000px;">
                <legend>Dyes And Chemical Issue Requisition</legend>
                	<table width="950" cellspacing="2" cellpadding="0" border="0" id="tbl_master">
                        <tr>
                            <td colspan="6" align="center">&nbsp;<b>Requisition Number</b>
                                <input type="text" name="txt_mrr_no" id="txt_mrr_no" class="text_boxes" style="width:155px" placeholder="Double Click To Search" onDblClick="open_mrrpopup()" readonly /> <input type="hidden" name="update_id" id="update_id" />
                            </td>
                       </tr>
                       <tr>
                           <td width="130" align="right" class="must_entry_caption">Company Name </td>
                           <td width="170">
                                <? 
                                	echo create_drop_down( "cbo_company_name", 170, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "rcv_basis_reset();load_drop_down( 'requires/chemical_dyes_issue_requisition_controller', this.value, 'load_drop_down_location', 'location_td')","");
                                ?>
                           </td>
                           <td width="130" align="right"> Location </td>
                           <td width="170" id="location_td">
                               <? echo create_drop_down( "cbo_location_name", 170, $blank_array,"", 1, "-- Select Location --", 0, "" ); ?>
                           </td>
                           <td  width="130" align="right" class="must_entry_caption" >Requisition Date </td>
                           <td width="170">
                               <input type="text" name="txt_requisition_date" id="txt_requisition_date" class="datepicker" style="width:160px;" placeholder="Select Date" />
                           </td>
                        </tr>
                        <tr>
                           <td align="right" class="must_entry_caption" > Issue Basis </td>
                           <td>
                                <? echo create_drop_down("cbo_receive_basis",170,$receive_basis_arr,"",0,"- Select Basis -",8,"","1","8"); ?>
                           </td>
                           <td align="right" class="must_entry_caption">Recipe No. </td>
                           <td>
                               <input class="text_boxes"  type="text" name="txt_recipe_no" id="txt_recipe_no" onDblClick="openmypage_labdipNo();" placeholder="Double Click" style="width:160px;"    /> 
                               <input class="text_boxes"  type="hidden" name="txt_recipe_id" id="txt_recipe_id"style="width:160px;"    /> 
                           </td>
                           <td align="right">Batch No </td>
                           <td>
                                <input class="text_boxes" type="text" name="txt_batch_no" id="txt_batch_no" disabled placeholder="Display" style="width:160px;" /> 
                                <input class="text_boxes" type="hidden" name="txt_batch_id" id="txt_batch_id" /> 
                           </td>
                        </tr>
                        <tr>
                           <td align="right">Method</td>
                           <td><? echo create_drop_down( "cbo_method", 170, $dyeing_method,"", 1, "--Select Method--", $selected, "",0 ); ?></td>
                           <td align="right" style="display:none"> Total Liquor (ltr)</td>
                           <td style="display:none">
                                <input type="text" name="txt_tot_liquor" id="txt_tot_liquor" class="text_boxes_numeric" style="width:160px;display:none" placeholder="Display" disabled />
                           </td>
                           <td align="right"> Batch Weight </td>
                           <td>
                                <input type="text" name="txt_batch_weight" id="txt_batch_weight" class="text_boxes_numeric" style="width:160px;" placeholder="Display" disabled />
                           </td>
                            <td align="right">Machine No</td>
                           <td>
                                <input type="text" name="txt_machine_no" id="txt_machine_no" class="text_boxes" style="width:158px;" onDblClick="fn_machine_seach();" placeholder="Browse" readonly/>
                                <input type="hidden" name="machine_id" id="machine_id" class="text_boxes"/>
                           </td>
                       </tr>
                       
					</table>
				</fieldset>
			</div>
            <div style="width:1110px;" >  
                <fieldset>
                    <table cellpadding="0" cellspacing="1" width="100%">
                        <tr> 
                           <td colspan="6" align="center"><div style="color:#F00; font-size:18px" id="last_update_message"></div></td>				
                        </tr>
                        <tr> 
                           <td colspan="6" align="center"><div id="list_container_recipe_items" style="margin-top:10px"></div></td>				
                        </tr>
                        <tr>
                            <td align="center" colspan="5" width="65%" valign="middle" class="button_container">
                            	<input type="hidden" name="update_dtls_id" id="update_dtls_id" readonly>
                                
								<? 
								//	reset_form('chemicaldyesissuerequisition_1','list_container_recipe_items*recipe_items_list_view*last_update_message','','','','');
								
								echo load_submit_buttons($permission, "fnc_chemical_dyes_issue_requisition", 0,1,"fnResetForm();",1); ?> 
                               
                                <span style="position: relative; " >
                                <!-- <input type="button" name="adding_topping" class="formbuttonplasminus" value="Adding/Topping" id="adding_topping" onClick="print_adding_topping();"/> -->
                                <input type="button" name="without_rate" class="formbuttonplasminus" value="Without Rate" id="without_rate" onClick="without_rate_fnc();"/>
                                <!-- <input type="button" name="adding_topping_without_rate" class="formbuttonplasminus" value="Adding/Topping Without Rate" id="adding_topping_without_rate" style="margin-left:0px;" onClick="print_adding_topping(1);"/> -->
                                </span>
                            
                                <input type="button" name="last_update" class="formbuttonplasminus" value="Apply Last Update" id="last_update" onClick="apply_last_update();"/>
                                
                                <input type="hidden" name="is_apply_last_update" id="is_apply_last_update" value="0">
                                 
                            </td>	
                          <tr>
                          	<td></td>
                          </tr>
                       	</tr> 
                       	<tr> 
                           	<td colspan="6" align="center"><div id="recipe_items_list_view" style="margin-top:10px"></div></td>				
                    	</tr>
                    </table>                 
              	</fieldset>
    		</div>
		</form>
	</div>    
</body>  
<script src="../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
