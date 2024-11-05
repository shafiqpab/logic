<?
/*********************Comments******************
* Purpose			: 	This form will create Dyes And Chemical Issue Requisition [Sweater]
* Functionality	:                                                                           
* JS Functions	:                                                                           
* Created by		:	Kausar                                                               	
* Creation date 	: 	05-04-2020                                                         
* Updated by 		:                                           									
* Update date		:                                                          						   
* QC Performed BY	:                                                                       		
* QC Date			:                                                                       	
* Comments		:                                                                           
***************/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
echo load_html_head_contents("Dyes And Chemical Issue Requisition [Sweater]","../../", 1, 1, $unicode,1,1);
$today=date('d-m-Y'); 
//--------------------------------------------------------------------------------------------------------------------

?>	

<script>
	var permission='<? echo $permission; ?>';
	var today='<? echo $today; ?>';
	if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";

	function rcv_basis_reset()
	{
		//document.getElementById('cbo_receive_basis').value=0;
		reset_form('chemicaldyesissuerequisition_1','list_container_recipe_items*recipe_items_list_view*last_update_message','','','','cbo_company_name');
	} 

	// receipe no poup
	function openmypage_recipe()
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
			var page_link = 'requires/chemical_dyes_issue_req_controller.php?cbo_company_id='+cbo_company_id+'&recipe_id='+txt_recipe_id+'&action=recipe_popup';
			  
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1000px,height=450px,center=1,resize=1,scrolling=0','../');
			
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
				var recipedata=this.contentDoc.getElementById("hidden_recipe_id").value;	
				//alert(recipedata);  1288 ___ ___ ___ 2040___OG-EB-19-00065___35656___JL-2___jahangir job copy-1
				var erecipedata=recipedata.split("___");
				if(erecipedata[0]!="")
				{
					freeze_window(5);
					$('#txt_recipe_id').val(erecipedata[0]);
					$('#txt_recipe_no').val(erecipedata[1]);
					$('#txt_job_no').val(erecipedata[2]);
					$('#txt_order_id').val(erecipedata[3]);
					//alert(erecipedata[3]);
					$('#txt_order_no').val(erecipedata[4]);
					
					$('#txtstyleRef').val(erecipedata[5]);
					$('#txtBatchNo').val(erecipedata[6]);
					$('#txtMachineNo').val(erecipedata[7]);
					$('#txtBatchQty').val(erecipedata[8]);
					$('#hidden_operation_id').val(erecipedata[9]);
					$('#txtoperation').val(erecipedata[10]);
					$('#cbo_store_name').val(erecipedata[11]);
					
					show_list_view(cbo_company_id+"**"+erecipedata[0]+"**"+0, 'item_details', 'list_container_recipe_items', 'requires/chemical_dyes_issue_req_controller', '');
					
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
			get_php_form_data(recipe_id, "populate_data_from_recipe_popup", "requires/chemical_dyes_issue_req_controller" );
			var subprocess_id=return_global_ajax_value(recipe_id, 'get_subprocess_id', '', 'requires/chemical_dyes_issue_req_controller');
			show_list_view(cbo_company_id+'**'+subprocess_id+"**"+recipe_id,'item_details','list_container_recipe_items','requires/chemical_dyes_issue_req_controller','../');
			$('#is_apply_last_update').val(1);
			release_freezing();
		} 
	}
	
	function fnc_chemical_dyes_issue_requisition(operation)
	{
		var btn_type=operation;
		if(operation==4 || operation==5)
		{
			if ( $('#txt_req_no').val()=='')
			{
				alert ('Requisition Number Not Save.');
				return;
			}
			var action="";
			if(btn_type==4) action="chemical_dyes_issue_requisition_print"; else if(btn_type==5) action="chemical_dyes_issue_requisition_printb1";
			var report_title=$( "div.form_caption" ).html();
			print_report( $('#cbo_company_name').val()+'*'+$('#update_id').val()+'*'+$('#txt_recipe_id').val()+'*'+report_title, action, "requires/chemical_dyes_issue_req_controller" );
			return;
		}
		else if(operation==0 || operation==1 || operation==2)
		{
			/*if(operation==2)
			{
				show_msg('15');
				return;
			}
		*/
			if( form_validation('cbo_company_name*txt_requisition_date*txt_recipe_no','Company Name*Requisition Date*Recipe No')==false )
			{
				return;
			}
			var mst_data=get_submitted_data_string('txt_req_no*update_id*cbo_company_name*cbo_location_name*txt_requisition_date*cbo_receive_basis*txt_recipe_id*txt_order_id*txt_job_no*is_apply_last_update*hidden_operation_id*txtoperation*cbo_store_name',"../../");
			
			var total_color=($('#hidd_totcolor').val()*1)-1;
			
			var row_num=$('#tbl_list_search tbody tr').length-(total_color*2)-1;
			var j=0; var k=0;
			//alert(total_color+'_'+row_num); return;
			var multicolor_data=""; var row_data="";
			for (var i=1; i<=total_color; i++)
			{
				/*var past_weight=$('#txt_past_weight_'+i).val();
				if(past_weight*1>0)
				{
					*/j++;
					multicolor_data+="&txt_past_weight_" + j + "='" + $('#txt_past_weight_'+i).val()+"'"+"&multicolor_id_" + j + "='" + $('#multicolor_id_'+i).val()+"'"+"&hidd_nprod_id_" + j + "='" + $('#hidd_nprod_id_'+i).val()+"'"+"&hidd_colorrow" + j + "='" + $('#hidd_colorrow'+i).val()+"'";
					
					var colorrow=($('#hidd_colorrow'+i).val()*1)-1;
					//alert(colorrow);
					for (var m=1; m<=colorrow; m++)
					{
						k++;
						row_data+="&product_id_" + k + "='" + $('#product_id_'+i+'_'+m).text()+"'"+"&txt_item_cat_" + k + "='" + $('#txt_item_cat_'+i+'_'+m).val()+"'"+"&txt_group_id_" + k + "='" + $('#txt_group_id_'+i+'_'+m).val()+"'"+"&seq_no_" + k + "='" + $('#seq_no_'+i+'_'+m).text()+"'"+"&ratio_" + k + "='" + $('#ratio_'+i+'_'+m).text()+"'"+"&txt_reqn_qnty_" + k + "='" + $('#txt_reqn_qnty_'+i+'_'+m).val()+"'"+"&txt_adj_per_" + k + "='" + $('#txt_adj_per_'+i+'_'+m).val()+"'"+"&cbo_adj_type_" + k + "='" + $('#cbo_adj_type_'+i+'_'+m).val()+"'"+"&txt_tot_qnty_" + k + "='" + $('#txt_tot_qnty_'+i+'_'+m).val()+"'"+"&updateIdDtls_" + k + "='" + $('#updateIdDtls_'+i+'_'+m).val()+"'"+"&txt_lot_" + k + "='" + trim($('#lot_'+i+'_'+m).text())+"'";
					}
				/*}*/
			}
			
			//alert(row_data);return;
			
			var data="action=save_update_delete&operation="+operation+'&tcolor_row='+j+'&total_row='+k+mst_data+multicolor_data+row_data;
			//alert(data);return;
			freeze_window(operation);
			http.open("POST","requires/chemical_dyes_issue_req_controller.php",true);
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
			
			/*if(trim(reponse[0])=='emblRequ'){
				alert("Dyes And Chemical Issue Requisition Found :"+trim(reponse[2])+"\n So Update/Delete Not Possible")
				release_freezing();
				return;
			}*/
			show_msg(reponse[0]);
			
			if((reponse[0]==0 || reponse[0]==1))
			{
				document.getElementById('txt_req_no').value = reponse[1];
				document.getElementById('update_id').value = reponse[2];
				
				var cbo_company_id=$('#cbo_company_name').val();
				var recipe_id=$('#txt_recipe_id').val();

				$('#cbo_company_name').attr('disabled','disabled');
				document.getElementById('last_update_message').innerHTML = '';
				//reset_form( '', 'list_container_recipe_items', '', '', '', '' ); 
				show_list_view(cbo_company_id+"**"+recipe_id+"**"+reponse[2], 'item_details', 'list_container_recipe_items', 'requires/chemical_dyes_issue_req_controller', '');
				//fnc_req_calculate(2,0);
				//calculate_requs_qty(0);
				set_button_status(1, permission, 'fnc_chemical_dyes_issue_requisition',1,1);
			}
			if(reponse[0]==2)
			{
				location.reload();
			}
			if(reponse[0]==50)
			{
				alert(reponse[1]);
				release_freezing();
				return;
			}
			release_freezing();	
		}
	}

	function open_requisitionpopup()
	{
		if( form_validation('cbo_company_name','Company Name')==false )
		{
			return;
		}
		var company = $("#cbo_company_name").val();	
		var page_link='requires/chemical_dyes_issue_req_controller.php?action=requisition_popup&company='+company; 
		var title="Requisition Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=830px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var reqndata=this.contentDoc.getElementById("hidden_sys_id").value; 
			var ereqsn_data=reqndata.split("___");
			var reqnId=ereqsn_data[0];
			var recipe_id=ereqsn_data[1];
			if(reqnId!="")
			{
				reset_form('','list_container_recipe_items*recipe_items_list_view*last_update_message','','','','');
				
				get_php_form_data(company+"**"+recipe_id+"**"+reqnId, "populate_data_from_data", "requires/chemical_dyes_issue_req_controller");
				show_list_view(company+"**"+recipe_id+"**"+reqnId, 'item_details', 'list_container_recipe_items', 'requires/chemical_dyes_issue_req_controller', '');
				//fnc_req_calculate(2,0);
			}
		}
	}

	function fnResetForm()
	{
		$("#cbo_company_name").attr("disabled",false); 
		//disable_enable_fields(\'cbo_company_id*cbo_company_id\',0)
		reset_form('chemicaldyesissuerequisition_1','list_container_recipe_items*recipe_items_list_view*last_update_message','','','','');
	}
	
	function fnc_req_calculate(type,inc)
	{
		if(type==1)
		{
			var colorrow=($('#hidd_colorrow'+inc).val()*1)-1;
			var past_weight=$('#txt_past_weight_'+inc).val()*1;
			//var ratiotot=$('#ratiotot_'+inc).text()*1;
			var reqsn_tot=0;
			for (var m=1; m<=colorrow; m++)
			{
				var ratio=$('#ratio_'+inc+'_'+m).text()*1;
				
				var req_qty=0;
				req_qty=(ratio/100)*past_weight;
				if(req_qty!=0) req_qty=number_format(req_qty,4,'.','' ); 
				
				$('#txt_reqn_qnty_'+inc+'_'+m).val( req_qty );
				$('#txt_tot_qnty_'+inc+'_'+m).val( req_qty );
				reqsn_tot=(reqsn_tot*1)+(req_qty*1);
			}
			$('#color_reqsnqty_'+inc).text(number_format(reqsn_tot,4,'.','' ));
			$('#color_tot_qnty_'+inc).text(number_format(reqsn_tot,4,'.','' ));
		}
		else
		{
			var total_color=($('#hidd_totcolor').val()*1)-1;
			
			var row_num=$('#tbl_list_search tbody tr').length-(total_color*2)-1;
			
			for (var i=1; i<=total_color; i++)
			{
				var reqsn_tot=0;var reqsnTotal_tot=0;
				var past_weight=$('#txt_past_weight_'+i).val();
				var colorrow=($('#hidd_colorrow'+i).val()*1)-1;
				if(past_weight*1>0)
				{
					for (var m=1; m<=colorrow; m++)
					{
						reqsn_tot=(reqsn_tot*1)+($('#txt_reqn_qnty_'+i+'_'+m).val()*1);
						reqsnTotal_tot=(reqsnTotal_tot*1)+($('#txt_tot_qnty_'+i+'_'+m).val()*1);
					}
				}
				$('#color_reqsnqty_'+i).text(number_format(reqsn_tot,4,'.','' ));
				$('#color_tot_qnty_'+i).text(number_format(reqsnTotal_tot,4,'.','' ));
			}
		}
		
		var total_color=($('#hidd_totcolor').val()*1)-1;
		var total_reqsn_qty=0;var total_reqsnTot_qty=0;
		for (var i=1; i<=total_color; i++)
		{
			var colorreq=$('#color_reqsnqty_'+i).text()*1;
			var colorreqTot=$('#color_tot_qnty_'+i).text()*1;
			total_reqsn_qty=(total_reqsn_qty*1)+colorreq;
			total_reqsnTot_qty=(total_reqsnTot_qty*1)+colorreqTot;
		}
		$('#td_reqsnqty').text(number_format(total_reqsn_qty,4,'.','' ));
		$('#td_tot_qnty').text(number_format(total_reqsnTot_qty,4,'.','' ));
	}
	
	function calculate_requs_qty(inc)
	{
		
		var recp_qnty=$('#txt_reqn_qnty_'+inc).val()*1;
		var adj_per=$('#txt_adj_per_'+inc).val()*1;
		var adj_type=$('#cbo_adj_type_'+inc).val()*1;
		if(adj_type>0 && adj_per>0)
		{
			var calc_qnty=((recp_qnty*adj_per)/100)*1;
			if(adj_type==1)
			{
				$('#txt_tot_qnty_'+inc).val(number_format((recp_qnty+calc_qnty),4,'.','' ))
			}
			else
			{
				$('#txt_tot_qnty_'+inc).val(number_format((recp_qnty-calc_qnty),4,'.','' ))
			}
			
			var inc_ref=inc.split("_");
			var colorrow=($('#hidd_colorrow'+inc_ref[0]).val()*1)-1;
			var color_total=0;
			for (var m=1; m<=colorrow; m++)
			{
				color_total+=$('#txt_tot_qnty_'+inc_ref[0]+'_'+m).val()*1;
			}
			$('#color_tot_qnty_'+inc_ref[0]).text(number_format(color_total,4,'.','' ));
			
			var total_color=($('#hidd_totcolor').val()*1)-1;
			var total_reqsnTot_qty=0;
			for (var i=1; i<=total_color; i++)
			{
				var colorreqTot=$('#color_tot_qnty_'+i).text()*1;
				total_reqsnTot_qty=(total_reqsnTot_qty*1)+colorreqTot;
			}
			
			$('#td_tot_qnty').text(number_format(total_reqsnTot_qty,4,'.','' ));

			//var rowCount = $('#wash_details_container tr').length;
			//math_operation( "color_reqsnqty_"+inc, "reqn_qnty_", "+", inc );
		}
		else
		{
			$('#txt_tot_qnty_'+inc).val(number_format(recp_qnty,4,'.','' ));
			//math_operation( "color_reqsnqty_"+inc, "reqn_qnty_", "+", inc )
			//calculate_requs_qty(inc);

			var inc_ref=inc.split("_");
			var colorrow=($('#hidd_colorrow'+inc_ref[0]).val()*1)-1;
			var color_total=0;
			for (var m=1; m<=colorrow; m++)
			{
				color_total+=$('#txt_tot_qnty_'+inc_ref[0]+'_'+m).val()*1;
			}
			$('#color_tot_qnty_'+inc_ref[0]).text(number_format(color_total,4,'.','' ));
			
			var total_color=($('#hidd_totcolor').val()*1)-1;
			var total_reqsnTot_qty=0;
			for (var i=1; i<=total_color; i++)
			{
				var colorreqTot=$('#color_tot_qnty_'+i).text()*1;
				total_reqsnTot_qty=(total_reqsnTot_qty*1)+colorreqTot;
			}
			
			$('#td_tot_qnty').text(number_format(total_reqsnTot_qty,4,'.','' ));
		}
	}
	
</script>
</head>
<body onLoad="set_hotkey()">
	<div style="width:100%;" align="center">
		<? echo load_freeze_divs ("../../",$permission);  ?>   		 
        <form name="chemicaldyesissuerequisition_1" id="chemicaldyesissuerequisition_1" autocomplete="off" > 
        <div style="width:830px;">       
            <fieldset style="width:820px;">
            <legend>Dyes And Chemical Issue Requisition</legend>
                <table width="810" cellspacing="2" cellpadding="0" border="0" id="tbl_master">
                    <tr>
                        <td colspan="3" align="right"><b>Requisition Number</b></td>
                        <td colspan="3"><input type="text" name="txt_req_no" id="txt_req_no" class="text_boxes" style="width:140px" placeholder="Double Click To Search" onDblClick="open_requisitionpopup();" readonly />
                        	<input type="hidden" name="update_id" id="update_id" />
                        </td>
                    </tr>
                    <tr>
                        <td width="110" class="must_entry_caption">Company Name</td>
                        <td width="155"><? echo create_drop_down( "cbo_company_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-Select Company-", $selected, "rcv_basis_reset(); load_drop_down( 'requires/chemical_dyes_issue_req_controller', this.value, 'load_drop_down_location', 'location_td'); ",""); //get_php_form_data( this.value, 'company_wise_report_button_setting','requires/chemical_dyes_issue_req_controller');?>
                        </td>
                        <td width="110">Location</td>
                        <td width="155" id="location_td"><? echo create_drop_down( "cbo_location_name", 150, $blank_array,"", 1, "-Select Location-", 0, "" ); ?></td>
                        <td width="110" class="must_entry_caption">Requisition Date</td>
                        <td ><input type="text" name="txt_requisition_date" id="txt_requisition_date" class="datepicker" style="width:140px;" value="<? echo date("d-m-Y");?>" placeholder="Select Date" /></td>
                    </tr>
                    <tr>
                        <td class="must_entry_caption">Issue Basis</td>
                        <td><? echo create_drop_down("cbo_receive_basis",150,$receive_basis_arr,"",0,"- Select Basis -",8,"","1","8"); ?></td>
                        <td class="must_entry_caption">Recipe No.</td>
                        <td>
                            <input class="text_boxes" type="text" name="txt_recipe_no" id="txt_recipe_no" onDblClick="openmypage_recipe();" placeholder="Double Click" style="width:140px;"    /> 
                            <input class="text_boxes" type="hidden" name="txt_recipe_id" id="txt_recipe_id"style="width:100px;"    /> 
                        </td>
                        <td>Batch No</td>
                        <td><input class="text_boxes" type="text" name="txtBatchNo" id="txtBatchNo" readonly placeholder="Display" style="width:140px;" /></td>
                    </tr>
                    <tr>
                    	<td>Machine No</td>
                        <td><input class="text_boxes" type="text" name="txtMachineNo" id="txtMachineNo" readonly placeholder="Display" style="width:140px;" /></td>
                    	<td>Gmts Qty. (Pcs)</td>
                        <td><input name="txtBatchQty" id="txtBatchQty" type="text" class="text_boxes_numeric" style="width:140px" readonly placeholder="Display" /></td>
                    	<td>Order No</td>
                        <td>
                            <input class="text_boxes" type="text" name="txt_order_no" id="txt_order_no" placeholder="Display" style="width:140px;" readonly /> 
                            <input class="text_boxes" type="hidden" name="txt_order_id" id="txt_order_id" />
                        </td>
                    </tr>
                    <tr>
                    	<td>Job No</td>
                        <td><input name="txt_job_no" id="txt_job_no" type="text" class="text_boxes" style="width:140px" readonly placeholder="Display" /></td>
                        <td>Style Ref</td>
                    	<td><input name="txtstyleRef" id="txtstyleRef" type="text" class="text_boxes" style="width:140px" readonly placeholder="Display" /></td>
                     	<td>Operation</td>
                    	<td>
						 	<input name="txtoperation" id="txtoperation" type="text" class="text_boxes" style="width:140px" readonly placeholder="Display" />
                          	<input type="hidden" name="hidden_operation_id" id="hidden_operation_id" class="text_boxes_numeric" readonly /> 
                      </td>
                    </tr>
                    <tr>
                       <td class="must_entry_caption">Store Name</td>
                       <td><? echo create_drop_down( "cbo_store_name", 160, "select lib_store_location.id,lib_store_location.store_name from lib_store_location,lib_store_location_category where lib_store_location.id=lib_store_location_category.store_location_id and lib_store_location.status_active=1 and lib_store_location.is_deleted=0  and lib_store_location_category.category_type in(5,6,7,23) group by lib_store_location.id,lib_store_location.store_name order by lib_store_location.store_name","id,store_name", 1, "-- Select Store --", $storeName, "",1 ); ?></td>
                       <td align="right"></td>
                       <td></td>
                       <td align="right"></td>
                       <td></td>
                   </tr>
                </table>
            </fieldset>
        </div>
        <br>
        <div style="width:1130px;" >  
            <fieldset>
                <table cellpadding="0" cellspacing="1" width="100%">
                    <tr> 
                    	<td colspan="8" align="center"><div style="color:#F00; font-size:18px" id="last_update_message"></div></td>				
                    </tr>
                    <tr> 
                    	<td colspan="8" align="center"><div id="list_container_recipe_items" style="margin-top:10px"></div></td>				
                    </tr>
                    <tr>
                        <td align="center" colspan="8" valign="middle" class="button_container">
                        <input type="hidden" name="update_dtls_id" id="update_dtls_id" readonly>
							<? echo load_submit_buttons($permission, "fnc_chemical_dyes_issue_requisition", 0,0,"fnResetForm();",1); ?>
							<input type="button" name="print" id="print" value="Print" onClick="fnc_chemical_dyes_issue_requisition(4);" style="width:100px;" class="formbuttonplasminus" />
                            <span style="position: relative; " >
                            	 <input type="button" name="print1" id="print1" value="Print B1" onClick="fnc_chemical_dyes_issue_requisition(5);" style="width:100px;" class="formbuttonplasminus" />
                            </span>
                            <input type="button" name="last_update" class="formbuttonplasminus" value="Apply Last Update" style="display:none"  id="last_update" onClick="apply_last_update();"/>
                            <input type="hidden" name="is_apply_last_update" id="is_apply_last_update" value="0">
                        </td>
                    </tr>	
                    <tr> 
                    	<td colspan="8" align="center"><div id="recipe_items_list_view" style="margin-top:10px"></div></td>	
                    </tr>
                </table>                 
            </fieldset>
        </div>
		</form>
	</div>    
</body>  
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script type="text/javascript">
	//$('#txt_requisition_date').val(today);
</script> 
</html>
