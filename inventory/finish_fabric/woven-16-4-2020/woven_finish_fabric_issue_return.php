<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Finish Fabric Issue Return Entry
				
Functionality	:	
JS Functions	:
Created by		:	Kaiyum 
Creation date 	: 	30-04-2018
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
echo load_html_head_contents("Woven Finish Fabric Issue Return Info","../../", 1, 1, '','',''); 

?>	

<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";

function open_issuemrr()
{
	if(form_validation('cbo_company_id','Company Name')==false)
	{
		return;
	}
	else
	{
		var cbo_company_id=$('#cbo_company_id').val();
		var page_link='requires/woven_finish_fabric_issue_return_controller.php?action=mrr_popup&cbo_company_id='+cbo_company_id; 
		var title="Search MRR Popup";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=400px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var mrrNumber=this.contentDoc.getElementById("hidden_recv_number").value.split("_"); // mrr number
			$("#txt_issue_id").val(mrrNumber[0]);
			$("#txt_issue_no").val(mrrNumber[1]);
			$("#cbo_issue_purpose").val(mrrNumber[2]);
			$("#txt_challan_no").val(mrrNumber[3]);
			
			return_qnty_basis(mrrNumber[2]);
			$("#tbl_item_info").find('input,select').val('');
			show_list_view(mrrNumber[0],'show_fabric_desc_listview','list_fabric_desc_container','requires/woven_finish_fabric_issue_return_controller','');
			return;
			
		}
	}
}

function return_qnty_basis(purpose)
{
	//var basis = parseInt($("#cbo_basis").val());
	
	$("#txt_return_qnty").val('');
		
	if(purpose==8)
	{
		$("#txt_return_qnty").attr('onDblClick','');
		$("#txt_return_qnty").attr('placeholder','');
		$("#txt_return_qnty").attr("readonly",false);
		//$("#txt_no_of_roll").attr("readonly",false);
	}
	else
	{
		$("#txt_return_qnty").attr('onDblClick','openmypage_po();');
		$("#txt_return_qnty").attr('placeholder','Double Click To Search');
		$("#txt_return_qnty").attr("readonly",true);
		//$("#txt_no_of_roll").attr("readonly",true);
		$("#txt_return_qnty").attr('ondblclick','openmypage_rtn_qty()');
	}
}
//
function set_form_data(data)
{
	var data_ref=data.split('**');
	reset_form('','','cbo_store_name*txt_batch_no*hidden_batch_id*txt_fabric_desc*before_prod_id*txt_prod_id*txt_color*txt_return_qnty*txt_break_qnty*txt_break_roll*txt_order_id_all*prev_return_qnty*txt_no_of_roll*txt_floor*txt_floor_name*txt_room*txt_room_name*txt_rack*txt_rack_name*txt_shelf*txt_shelf_name*txt_bin*txt_bin_name*txt_remarks*txt_order_numbers*txt_tot_issue*txt_total_return_display*txt_total_return*txt_net_used*hide_net_used*txt_global_stock','','','');
	$("#txt_prod_id").val(data_ref[2]);
	$("#txt_fabric_desc").val(data_ref[3]);

	$("#txt_no_of_roll").val(data_ref[10]);
	$("#txt_tot_issue").val(data_ref[4]);
	$("#txt_total_return_display").val(data_ref[5]);
	var balance=data_ref[4]-data_ref[5];
	$("#txt_net_used").val(balance);
	$("#txt_global_stock").val(data_ref[6]);
	$("#txt_color").val(data_ref[7]);
	$("#cbouom").val(data_ref[9]);
	var issue_purpose=$('#cbo_issue_purpose').val();
	var order_type=data_ref[8];

	if(data_ref[11] == 1)
	{
		$("#txt_floor").val(data_ref[12]);
		$("#txt_floor_name").val(data_ref[13]);
		$("#txt_room").val(data_ref[14]);
		$("#txt_room_name").val(data_ref[15]);
		$("#txt_rack").val(data_ref[16]);
		$("#txt_rack_name").val(data_ref[17]);
		$("#txt_shelf").val(data_ref[18]);
		$("#txt_shelf_name").val(data_ref[19]);
		$("#txt_bin").val(data_ref[20]);
		$("#txt_bin_name").val(data_ref[21]);
	}
	

	$("#cbo_store_name").attr("disabled","disabled");
	
	get_php_form_data(data_ref[0]+'**'+data_ref[1], "populate_details_from_data", "requires/woven_finish_fabric_issue_return_controller");
	
	if(order_type=="")
	{
		$('#txt_return_qnty').removeAttr('placeholder').removeAttr('readonly').removeAttr('onDblClick').attr('placeholder','write');
	}
	else
	{
		$('#txt_return_qnty').removeAttr('placeholder').removeAttr('readonly').removeAttr('onDblClick').attr('placeholder','Double Click To Search').attr('readonly','').attr('onDblClick','openmypage_rtn_qty();');
		openmypage_rtn_qty();
	}
}

function openmypage_rtn_qty() // issue quantity
{
	var cbo_company_name = $('#cbo_company_id').val();
	var txt_issue_id = $('#txt_issue_id').val();
	var txt_prod_id = $('#txt_prod_id').val();
	var update_id = $('#update_id').val();
	var txt_return_qnty = $('#txt_return_qnty').val();
	var roll_maintained = $('#roll_maintained').val();
	
	
	if (form_validation('cbo_company_id*txt_issue_id*txt_prod_id','Company*Issue*Item Description')==false)
	{
		return;
	}
	var title = 'Issue Return Info';	
	var page_link = 'requires/woven_finish_fabric_issue_return_controller.php?cbo_company_name='+cbo_company_name+'&txt_issue_id='+txt_issue_id+'&txt_prod_id='+txt_prod_id+'&txt_return_qnty='+txt_return_qnty+'&update_id='+update_id+'&roll_maintained='+roll_maintained+'&action=return_po_popup';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=390px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
		var tot_qnty=this.contentDoc.getElementById("tot_qnty").value;	 //Access form field with id="emailfield"
		var break_qnty=this.contentDoc.getElementById("break_qnty").value; //Access form field with id="emailfield"
		var break_roll=this.contentDoc.getElementById("break_roll").value; //Access form field with id="emailfield" 
		var break_order_id=this.contentDoc.getElementById("break_order_id").value; //Access form field with id="emailfield"
		var tot_roll=this.contentDoc.getElementById("tot_roll").value; //Access form field with id="emailfield"
		//alert(tot_qnty);return;
		
		$('#txt_return_qnty').val(tot_qnty);
		$('#txt_no_of_roll').val(tot_roll);
		$('#txt_break_qnty').val(break_qnty);
		$('#txt_break_roll').val(break_roll);
		$('#txt_order_id_all').val(break_order_id);
	}
}

function fnc_fabric_issue_rtn(operation)
{
	if(operation==4)
	{
		var report_title=$( "div.form_caption" ).html();
		generate_report_file( $('#cbo_company_id').val()+'*'+$('#txt_system_id').val()+'*'+report_title+'*'+$('#issue_mst_id').val()+'*'+$('#txt_issue_id').val(),'issue_return_print','requires/woven_finish_fabric_issue_return_controller');
		return;
	}
	else if(operation==2)
	{
		show_msg('13');
		return;
	}
	else
	{
		if( form_validation('cbo_company_id*txt_issue_date*txt_issue_no*cbo_store_name*txt_batch_no*txt_fabric_desc*txt_return_qnty','Company Name*Issue Date*Issue No*Store Name*Batch No*Item Description*Return Qnty')==false )
		{
			return;
		}
		var current_date='<? echo date("d-m-Y"); ?>';
		if(date_compare($('#txt_issue_date').val(), current_date)==false)
		{
			alert("Issue Return Date Can not Be Greater Than Current Date");
			return;
		}	
		if($("#txt_return_qnty").val()*1<=0)
		{
			alert("Return Quantity Should be Greater Than Zero(0).");
			return;
		}
		
		if($("#txt_return_qnty").val()*1 > ($("#txt_net_used").val()*1 + $("#prev_return_qnty").val()*1) )
		{
			alert("Return Quantity Not Over Issue Quantity.");
			return;
		}
		
		
		var dataString = "txt_system_id*issue_mst_id*cbo_company_id*txt_issue_date*txt_issue_no*txt_issue_id*txt_challan_no*cbo_store_name*txt_batch_no*hidden_batch_id*txt_fabric_desc*before_prod_id*txt_prod_id*txt_color*txt_return_qnty*txt_break_qnty*txt_break_roll*txt_order_id_all*prev_return_qnty*txt_no_of_roll*txt_floor*txt_floor_name*txt_room*txt_room_name*txt_rack*txt_rack_name*txt_shelf*txt_shelf_name*txt_bin*txt_bin_name*roll_maintained*cbo_issue_purpose*update_id*txt_remarks*cbouom";

		var data="action=save_update_delete&operation="+operation+get_submitted_data_string(dataString,"../../");
		freeze_window(operation);
		http.open("POST","requires/woven_finish_fabric_issue_return_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_fabric_issue_rtn_reponse;
	}
}

function fnc_fabric_issue_rtn_reponse()
{	
	if(http.readyState == 4) 
	{
		var reponse=trim(http.responseText).split('**');
		//alert(reponse);
		//release_freezing();return;	
		if(reponse[0]==20)
		{
			alert(reponse[1]);
			release_freezing();
			return;
		}
		show_msg(reponse[0]); 		
		if(reponse[0]==0 || reponse[0]==1)
		{
			$("#txt_system_id").val(reponse[1]);
			$("#issue_mst_id").val(reponse[2]);
 			disable_enable_fields( 'cbo_company_id*txt_issue_no', 1, "", "" ); // disable true
				
			show_list_view(reponse[2]+'**'+ reponse[3],'show_dtls_list_view','div_details_list_view','requires/woven_finish_fabric_issue_return_controller','');		
			//child form reset here after save data-------------//
			$("#tbl_item_info").find('input,select').val('');
			$("#tbl_display_info").find('input,select').val('');
			set_button_status(0, permission, 'fnc_fabric_issue_rtn',1,1);
			release_freezing();
		}
		release_freezing();
	}
}

function generate_report_file(data,action,page)
{
	window.open("requires/woven_finish_fabric_issue_return_controller.php?data=" + data+'&action='+action, true );
}

function open_returnpopup()
{
	if( form_validation('cbo_company_id','Company Name')==false )
	{
		return;
	}
	
	var company = $("#cbo_company_id").val();
	var roll_maintained = $("#roll_maintained").val();	
	var page_link='requires/woven_finish_fabric_issue_return_controller.php?action=return_number_popup&company='+company; 
	var title="Search Issue Return Popup";
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=850px,height=400px,center=1,resize=0,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0]; 
		var returnNumber=this.contentDoc.getElementById("hidden_return_number").value.split('_'); // mrr number
  		// master part call here
		reset_form('finishFabricEntry_1','div_details_list_view*list_fabric_desc_container','','','','cbo_company_id*roll_maintained');
		
		get_php_form_data(returnNumber[0], "populate_master_from_data", "requires/woven_finish_fabric_issue_return_controller");
		show_list_view(returnNumber[1],'show_fabric_desc_listview','list_fabric_desc_container','requires/woven_finish_fabric_issue_return_controller','');
		show_list_view(returnNumber[0]+'**'+ roll_maintained,'show_dtls_list_view','div_details_list_view','requires/woven_finish_fabric_issue_return_controller','');	  		
		//list view call here
		//show_list_view(returnNumber,'show_dtls_list_view','list_container_yarn','requires/grey_fabric_issue_rtn_controller','');
		disable_enable_fields( 'cbo_company_id*txt_issue_no', 1, "", "" ); // disable true
 	}
}



</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;">
	<? echo load_freeze_divs ("../../",$permission);  ?><br />    		 
    <form name="finishFabricEntry_1" id="finishFabricEntry_1" autocomplete="off" >
    <div style="width:740px; float:left;" align="center"> 
        <fieldset style="width:730px;">
        <legend>Finish Fabric Issue Return Entry</legend>
        	<fieldset style="width:730px;">
                <table width="730" cellspacing="2" cellpadding="2" border="0" id="tbl_master">
                    <tr>
                        <td colspan="3" align="right"><strong>Issue Rtn No</strong></td>
                        <td colspan="3" align="left">
                            <input type="text" name="txt_system_id" id="txt_system_id" class="text_boxes" style="width:150px;" placeholder="Double click to search" onDblClick="open_returnpopup();" readonly />
                            <input type="hidden" id="issue_mst_id" name="issue_mst_id" >
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6">&nbsp;</td>
                    </tr>
                    <tr>
                    	<td class="must_entry_caption">Company</td>
                        <td>
                            <? 
							echo create_drop_down( "cbo_company_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "reset_form('finishFabricEntry_1','div_details_list_view*list_fabric_desc_container','','','','cbo_company_id*roll_maintained');get_php_form_data(this.value,'roll_maintained','requires/woven_finish_fabric_issue_return_controller' );load_drop_down( 'requires/woven_finish_fabric_issue_return_controller', this.value, 'load_drop_down_store', 'store_td' );" );
							?>
                        </td>
                        <td class="must_entry_caption">Return Date</td>
                        <td><input type="text" name="txt_issue_date" id="txt_issue_date" class="datepicker" style="width:138px;" readonly placeholder="Select Date" /></td>
                        <td class="must_entry_caption">Issue No</td>
                        <td>
                        <input type="text" name="txt_issue_no" id="txt_issue_no" class="text_boxes" style="width:138px;" onDblClick="open_issuemrr();" placeholder="Double Click To Search" readonly />
                        <input type="hidden" id="txt_issue_id" name="txt_issue_id" >
                        </td>
                    </tr>
                    <tr>
                        <td >Challan No.</td>
                        <td><input type="text" name="txt_challan_no" id="txt_challan_no" class="text_boxes" style="width:138px;" maxlength="20" title="Maximum 20 Character" /></td>
                        <td style="display:none">Issue Purpose</td>
                        <td style="display:none">
							<?
                                echo create_drop_down("cbo_issue_purpose", 150,$yarn_issue_purpose,"", 0,"",'9',"active_inactive(this.value,0);",'','3,4,8,9,10');
                            ?>
                        </td>
                        <td >&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                </table>
            </fieldset>
            <table width="730" cellspacing="2" cellpadding="2" border="0" id="tbl_dtls">
                <tr>
                    <td width="68%" valign="top">
                        <fieldset>
                        <legend>Details
                            </legend><table id="tbl_item_info"  cellpadding="0" cellspacing="1" width="100%">										
                                <tr>
                                	<td class="must_entry_caption" width="30%">Store Name</td>
                                    <td id="store_td">
                                        <?
                                            echo create_drop_down( "cbo_store_name", 170, "select id, store_name from lib_store_location where find_in_set(2,item_category_id) and status_active=1 and is_deleted=0 order by store_name","id,store_name", 1, "-- Select store --", 0, "" );
                                        ?>	
                                    </td>
                                </tr>
                                <tr>	
                                	<td class="must_entry_caption">Batch No.</td>
                                    <td>
                                        <input type="text" name="txt_batch_no" id="txt_batch_no" class="text_boxes" style="width:158px;" placeholder="Display" readonly disabled/>
                                        <input type="hidden" name="hidden_batch_id" id="hidden_batch_id" readonly />
                                    </td>
                                </tr>						
                                <tr>
                                    <td class="must_entry_caption">Fabric Description</td>
                                    <td id="fabricDesc_td">
                                    	<input type="text" name="txt_fabric_desc" id="txt_fabric_desc" class="text_boxes" style="width:300px;" placeholder="Display" disabled />
                                        <input type="hidden" name="before_prod_id" id="before_prod_id" readonly>
                                        <input type="hidden" id="txt_prod_id" name="txt_prod_id" />
                                    	<!--<input type="hidden" id="txt_issue_id" name="txt_issue_id" />-->
										
                                    </td>
                                </tr>
                                <tr>
                                	<td class="must_entry_caption">UOM</td>
                                	<td>
                                		<?
                                        echo create_drop_down( "cbouom", 70, $unit_of_measurement,'', 1, '-Uom-', 12, "",1,"1,12,23,27" );
                                        ?>
                                	</td>
                                </tr>
                                <tr>
                                    <td>Color</td>						
                                    <td>
                                    	<input type="text" name="txt_color" id="txt_color" class="text_boxes" style="width:158px" placeholder="Display" disabled />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="must_entry_caption">Issue Qnty</td>
                                    <td>
                                        <input class="text_boxes_numeric" type="text" name="txt_return_qnty" id="txt_return_qnty" style="width:158px;" placeholder="Double Click To Search" readonly onDblClick="openmypage_rtn_qty();"   />
                                       <input type="hidden" id="txt_break_qnty" name="txt_break_qnty" > 
                                       <input type="hidden" id="txt_break_roll" name="txt_break_roll" >
                                       <input type="hidden" id="txt_order_id_all" name="txt_order_id_all" >
                                       <input type="hidden" id="prev_return_qnty" name="prev_return_qnty" >
                                        </td>
                                </tr>
                                <tr>
                                    <td>Roll</td>						
                                    <td>
                                    	<input type="text" name="txt_no_of_roll" id="txt_no_of_roll" class="text_boxes_numeric" style="width:158px"/>
                                    </td>
                                </tr>
                                <tr>
									<td>Floor</td>
									<td>
										<input type="hidden" name="txt_floor" id="txt_floor" class="text_boxes" style="width:158px" placeholder="Display" disabled />
										<input type="text" name="txt_floor_name" id="txt_floor_name" class="text_boxes" style="width:158px" placeholder="Display" disabled />
									</td>
								</tr>
								<tr>
									<td>Room</td>
									<td>
										<input type="hidden" name="txt_room" id="txt_room" class="text_boxes" style="width:158px" placeholder="Display" disabled/>
										<input type="text" name="txt_room_name" id="txt_room_name" class="text_boxes" style="width:158px" placeholder="Display" disabled/>
									</td>
								</tr>
                                <tr>
                                    <td>Rack</td>						
                                    <td>
                                    	<input type="hidden" name="txt_rack" id="txt_rack" class="text_boxes" style="width:158px" placeholder="Display" disabled />
                                    	<input type="text" name="txt_rack_name" id="txt_rack_name" class="text_boxes" style="width:158px" placeholder="Display" disabled />
                                    </td>
                                </tr>
                                <tr>
                                    <td>Shelf</td>						
                                    <td>
                                    	<input type="hidden" name="txt_shelf" id="txt_shelf" class="text_boxes" style="width:158px" placeholder="Display" disabled/>
                                    	<input type="text" name="txt_shelf_name" id="txt_shelf_name" class="text_boxes" style="width:158px" placeholder="Display" disabled/>
                                    </td>
                                </tr>
                                <tr>
									<td>Bin/Box</td>
									<td>
										<input type="hidden" name="txt_bin" id="txt_bin" class="text_boxes" style="width:158px" placeholder="Display" disabled/>
										<input type="text" name="txt_bin_name" id="txt_bin_name" class="text_boxes" style="width:158px" placeholder="Display" disabled/>
									</td>
								</tr>
                                <tr>
                                    <td>Remarks</td>						
                                    <td>
                                    	<input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:300px" />
                                    </td>
                                </tr>
							</table>
						</fieldset>
					</td>
					<td width="2%" valign="top"></td>
					<td width="30%" valign="top">
						<fieldset>
                        <legend>Display</legend>					
                            <table id="tbl_display_info"  cellpadding="0" cellspacing="1" width="100%" >				
                                <tr style="display:none;">
                                    <td>Order Numbers</td>						
                                	<td>
                                    	<input type="text" name="txt_order_numbers" id="txt_order_numbers" class="text_boxes" style="width:100px" placeholder="Display" readonly disabled />
                                    </td>
								</tr>
                                <tr>
                                    <td>Fabric Issue</td>						
                                    <td><input class="text_boxes_numeric" type="text" name="txt_tot_issue" id="txt_tot_issue" style="width:100px;" placeholder="Display" readonly disabled /></td>
                                </tr>
                                <tr>
                                    <td>Cumulative Issued</td>
                                    <td>
                                    <input class="text_boxes_numeric" type="hidden" name="txt_total_return" id="txt_total_return" style="width:100px;" placeholder="Display" readonly disabled />
                                    <input class="text_boxes_numeric" type="text" name="txt_total_return_display" id="txt_total_return_display" style="width:100px;" placeholder="Display" readonly disabled />
                                    </td>
                                </tr>					
                                <tr>
                                    <td>Yet to Issue</td>
                                    <td>
                                    <input class="text_boxes_numeric" type="text" name="txt_net_used" id="txt_net_used" style="width:100px;" placeholder="Display" readonly disabled />
                                     <input class="text_boxes_numeric" type="hidden" name="hide_net_used" id="hide_net_used" readonly />
                                    </td>
                                </tr>
                                <tr>
                                    <td>Global Stock</td>
                                    <td><input type="text" name="txt_global_stock" id="txt_global_stock" placeholder="Display" class="text_boxes_numeric" style="width:100px" readonly disabled /></td>
                                </tr>											
                            </table>                  
                       </fieldset>	
              		</td>
				</tr>	 	
                <tr>
                    <td align="center" colspan="3" class="button_container" width="100%">
                        <?
                            echo load_submit_buttons($permission, "fnc_fabric_issue_rtn", 0,1,"reset_form('finishFabricEntry_1','div_details_list_view*list_fabric_desc_container','','cbo_issue_purpose,9','disable_enable_fields(\'cbo_company_id*cbo_issue_purpose\');active_inactive(9,1);')",1);
                        ?>
                        <input type="hidden" name="roll_maintained" id="roll_maintained" readonly>
                        <input type="hidden" id="update_id" name="update_id" value="" />
                    </td>
                </tr>
            </table>
            <div style="width:730px;" id="div_details_list_view"></div>
		</fieldset>
	</div>
    <div id="list_fabric_desc_container" style="width:590px; margin-left:10px; overflow:auto; float:left; padding-top:5px; margin-top:5px; position:relative;"></div>
	</form>
</div>  
</body>  
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
