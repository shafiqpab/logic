<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create

Functionality	:
JS Functions	:
Created by		:	Bilas
Creation date 	: 	03-09-2013
Updated by 		: 	Kausar,Jahid,Md mahbubur Rahman
Update date		: 	30-10-2013,15-01-2019
QC Performed BY	:	Creating Report & List view Repair
QC Date			:
Comments		:
*/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;


//========== user credential start ========
$user_id = $_SESSION['logic_erp']['user_id'];
$userCredential = sql_select("SELECT unit_id as company_id,store_location_id,item_cate_id FROM user_passwd where id=$user_id");
$company_id = $userCredential[0][csf('company_id')];
$store_location_id = $userCredential[0][csf('store_location_id')];
$item_cate_id = $userCredential[0][csf('item_cate_id')];

if ($company_id !='') {
    $company_credential_cond = "and comp.id in($company_id)";
}
if ($store_location_id !='') {
    $store_location_credential_cond = "and a.id in($store_location_id)"; 
}
if($item_cate_id !='') {
    $item_cate_credential_cond = $item_cate_id ;  
}
//========== user credential end ==========
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("General Item Transfer Info","../../", 1, 1, '','',''); 

?>	

<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";

function active_inactive(str)
{
	$('#cbo_company_id_to').val(0);
	if(str==1)
	{
		$('#cbo_company_id_to').removeAttr('disabled','disabled');	
	}
	else
	{
		$('#cbo_company_id_to').attr('disabled','disabled');
	}
}

function openmypage_systemId()
{
	var cbo_transfer_criteria = $('#cbo_transfer_criteria').val();
	var cbo_company_id = $('#cbo_company_id').val();

	if (form_validation('cbo_transfer_criteria*cbo_company_id','Transfer Criteria*Company')==false)
	{
		return;
	}
	
	var title = 'Item Transfer Info';	
	var page_link = 'requires/raw_material_item_transfer_controller.php?cbo_transfer_criteria='+cbo_transfer_criteria+'&cbo_company_id='+cbo_company_id+'&action=itemTransfer_popup';
	  
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=370px,center=1,resize=1,scrolling=0','../');
	
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
		var transfer_id=this.contentDoc.getElementById("transfer_id").value; //Access form field with id="emailfield"
		
		reset_form('transferEntry_1','div_transfer_item_list','','');
		get_php_form_data(transfer_id, "populate_data_from_transfer_master", "requires/raw_material_item_transfer_controller" );
		show_list_view(transfer_id,'show_transfer_listview','div_transfer_item_list','requires/raw_material_item_transfer_controller','');
		set_button_status(0, permission, 'fnc_yarn_transfer_entry',1,1);
	}
}

function openmypage_itemDescription()
{
	var cbo_company_id = $('#cbo_company_id').val();
	var cbo_item_category = $('#cbo_item_category').val();
	var cbo_store_name = $('#cbo_store_name').val();

	if (form_validation('cbo_company_id*cbo_store_name*cbo_item_category','Company*From Store*Item Category')==false)
	{
		return;
	}
	
	var title = 'Item Description Info';	
	var page_link = 'requires/raw_material_item_transfer_controller.php?cbo_company_id='+cbo_company_id+'&cbo_item_category='+cbo_item_category+'&cbo_store_name='+cbo_store_name+'&action=itemDescription_popup';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=950px,height=370px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
		var product_id=this.contentDoc.getElementById("product_id").value; //Access form field with id="emailfield"
		get_php_form_data(product_id+"**"+cbo_store_name, "populate_data_from_product_master", "requires/raw_material_item_transfer_controller" );
	}
}

function calculate_value()
{
	var txt_transfer_qnty = $('#txt_transfer_qnty').val()*1;
	var txt_rate = $('#txt_rate').val()*1;
	
	var transfer_value=txt_transfer_qnty*txt_rate;
	$('#txt_transfer_value').val(transfer_value.toFixed(4));
}
 
function fnc_yarn_transfer_entry(operation)
{
	if(operation==4)
	{
		var report_title=$( "div.form_caption" ).html();
		print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+report_title, "yarn_transfer_print", "requires/raw_material_item_transfer_controller" ) 
		return;
	}
	else if(operation==0 || operation==1 || operation==2)
	{
		var current_date='<? echo date("d-m-Y"); ?>';
		if(date_compare($('#txt_transfer_date').val(), current_date)==false)
		{
			alert("Transfer Date Can not Be Greater Than Current Date");
			return;
		}
		
		if( form_validation('cbo_transfer_criteria*cbo_company_id*txt_transfer_date*cbo_store_name*cbo_store_name_to*txt_item_desc*txt_transfer_qnty*cbo_item_category','Transfer Criteria*Company*Transfer Date*From Store*To Store*Item Description*Transfered Qnty*Item Category')==false )
		{
			return;
		}	
		
		if($("#cbo_transfer_criteria").val()==1)
		{
			if($("#cbo_company_id").val()*1==$("#cbo_company_id_to").val()*1)
			{
				alert("Same Company Not Allow.");
				return;
			}
			if($("#txt_transfer_qnty").val()*1>$("#hidden_current_stock").val()*1)
			{
				alert("Trasfer Quantity Can not be Greater Than Current Stock.");
				$("#txt_transfer_qnty").focus();
				return;
			}
			
			if($("#cbo_company_id_to").val()==0)
			{
				alert("Please Select To Company.");
				$("#cbo_company_id_to").focus();
				return;
			}
		}
		else
		{
			if($("#cbo_store_name").val()*1==$("#cbo_store_name_to").val()*1)
			{
				alert("Same Store Not Allow.");
				return;
			}
		}
		
		var dataString = "txt_system_id*cbo_transfer_criteria*cbo_company_id*cbo_company_id_to*txt_transfer_date*txt_challan_no*cbo_item_category*cbo_store_name*cbo_floor*cbo_room*txt_rack*txt_shelf*cbo_store_name_to*cbo_floor_to*cbo_room_to*txt_rack_to*txt_shelf_to*txt_transfer_qnty*txt_rate*txt_transfer_value*cbo_uom*update_id*hidden_product_id*update_dtls_id*update_trans_issue_id*update_trans_recv_id*previous_from_prod_id*previous_to_prod_id*hidden_transfer_qnty*txt_item_desc";
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string(dataString,"../../");
		
		//alert(data);return;
		freeze_window(operation);
		http.open("POST","requires/raw_material_item_transfer_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_yarn_transfer_entry_reponse;
	}
}

function fnc_yarn_transfer_entry_reponse()
{	
	if(http.readyState == 4) 
	{
		//alert(http.responseText);release_freezing();return;	  		
		var reponse=trim(http.responseText).split('**');	
                
        if(reponse[0]*1==20*1)
		{
			alert(reponse[1]);
			release_freezing(); return;
		}
		else if(reponse[0]*1==16*1)
		{
			alert(reponse[1]);
			release_freezing(); return;
		}
		else if(reponse[0]*1==17*1)
		{
			alert(reponse[1]);
			release_freezing(); return;
		}
                
		show_msg(reponse[0]); 	
			
		if(reponse[0]==0 || reponse[0]==1 || reponse[0]==2)
		{
			$("#update_id").val(reponse[1]);
			$("#txt_system_id").val(reponse[2]);
			disable_enable_fields( 'cbo_transfer_criteria*cbo_company_id*cbo_company_id_to', 1, "", "" );
			//$('#cbo_company_id').attr('disabled','disabled');
			disable_enable_fields('cbo_store_name*cbo_item_category*txt_item_desc',0);
			reset_form('transferEntry_1','','','','','update_id*txt_system_id*cbo_transfer_criteria*cbo_company_id*cbo_company_id_to*txt_transfer_date*txt_challan_no');
			show_list_view(reponse[1],'show_transfer_listview','div_transfer_item_list','requires/raw_material_item_transfer_controller','');
			set_button_status(0, permission, 'fnc_yarn_transfer_entry',1,1);
		}	
		release_freezing();
	}
}

function fn_to_store(company_id)
{
	var transfer_criteria=$('#cbo_transfer_criteria').val();
	
	if(transfer_criteria==2)
	{
		load_drop_down( 'requires/raw_material_item_transfer_controller', company_id, 'load_drop_down_store_to', 'store_td' );
	}
}



</script>
</head>

<body onLoad="set_hotkey()">
<div style="width:100%;" align="center">
	<? echo load_freeze_divs ("../../",$permission);  ?><br />    		 
    <form name="transferEntry_1" id="transferEntry_1" autocomplete="off" >
    <div style="width:100%;">   
        <fieldset style="width:1000px;">
        <legend>General Item Transfer Entry</legend>
        <br>
        	<fieldset style="width:900px;">
                <table width="880" cellspacing="2" cellpadding="2" border="0" id="tbl_master">
                    <tr>
                        <td colspan="3" align="right"><strong>Transfer System ID</strong></td>
                        <td colspan="3" align="left">
                            <input type="text" name="txt_system_id" id="txt_system_id" class="text_boxes" style="width:150px;" placeholder="Double click to search" onDblClick="openmypage_systemId();" readonly />
                            <input type="hidden" name="update_id" id="update_id" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6">&nbsp;</td>
                    </tr>
                    <tr>
                        <td class="must_entry_caption">Transfer Criteria</td>
                        <td>
                            <?
                                echo create_drop_down("cbo_transfer_criteria", 160,$item_transfer_criteria,"", 1,"-- Select --",'0',"active_inactive(this.value);",'','1,2');
                            ?>
                        </td>
                        <td class="must_entry_caption">Company</td>
                        <td>
                            <? 
								echo create_drop_down( "cbo_company_id", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond $company_credential_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "load_room_rack_self_bin('requires/raw_material_item_transfer_controller*4_8_9_10_11_15_16_17_18_19_20_21_22_32_33_34_35_36_37_38_39_40_41_44_45_46_47_48_49_50_51_52_53_54_55_56_57_58_59_60_61_62_63_64_65_66_67_68_69_70_89_90_91_92_93_94', 'store','from_store_td', this.value);load_room_rack_self_bin('requires/raw_material_item_transfer_controller*4_8_9_10_11_15_16_17_18_19_20_21_22_32_33_34_35_36_37_38_39_40_41_44_45_46_47_48_49_50_51_52_53_54_55_56_57_58_59_60_61_62_63_64_65_66_67_68_69_70_89_90_91_92_93_94*cbo_store_name_to', 'store','to_store_td', this.value);" );
								//load_drop_down( 'requires/raw_material_item_transfer_controller', this.value, 'load_drop_down_store', 'store_td_from' );fn_to_store(this.value);
							?>
                        </td>
                        <td>To Company</td>
                        <td>
                            <? 
								echo create_drop_down( "cbo_company_id_to", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond $company_credential_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "load_room_rack_self_bin('requires/raw_material_item_transfer_controller*4_8_9_10_11_15_16_17_18_19_20_21_22_32_33_34_35_36_37_38_39_40_41_44_45_46_47_48_49_50_51_52_53_54_55_56_57_58_59_60_61_62_63_64_65_66_67_68_69_70_89_90_91_92_93_94*cbo_store_name_to', 'store','to_store_td', this.value);",1 );
								//load_drop_down( 'requires/raw_material_item_transfer_controller', this.value, 'load_drop_down_store_to', 'store_td' );
							?>
                        </td>
                    </tr>
                    <tr>
                        <td class="must_entry_caption">Transfer Date</td>
                        <td>
                            <input type="text" name="txt_transfer_date" id="txt_transfer_date" class="datepicker" style="width:148px;" readonly placeholder="Select Date" />
                        </td>
                        <td>Challan No.</td>
                        <td>
                            <input type="text" name="txt_challan_no" id="txt_challan_no" class="text_boxes" style="width:148px;" maxlength="20" title="Maximum 20 Character" />
                        </td>
                        
                    </tr>
                </table>
            </fieldset>
            <br>
            <table width="910" cellspacing="2" cellpadding="2" border="0" id="tbl_dtls">
                <tr>
                    <td width="65%" valign="top">
                        <fieldset>
                        <legend>Item Info</legend>
                            <table id="tbl_item_info"  cellpadding="0" cellspacing="1" width="50%" style="float: left;">										
                                <!-- <tr>
                                	<td width="30%" class="must_entry_caption">From Store</td>
                                    <td id="store_td_from">
                                        <?
                                            //echo create_drop_down( "cbo_store_name", 160, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and b.category_type in (4,8,9,10,11,15,16,17,18,19,20,21,22,32,34,35,36,37,38,39) $store_location_credential_cond and a.status_active=1 and a.is_deleted=0 group by a.id, a.store_name order by a.store_name","id,store_name", 1, "--Select store--", 0, "" );
                                        ?>	
                                    </td>
                                </tr>
                                <tr>	
                                	<td class="must_entry_caption">To Store</td>
                                    <td id="store_td">
                                   		<?
											//echo create_drop_down( "cbo_store_name_to", 160, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and  b.category_type in (4,8,9,10,11,15,16,17,18,19,20,21,22,32,34,35,36,37,38,39) $store_location_credential_cond and a.status_active=1 and a.is_deleted=0 group by a.id, a.store_name order by a.store_name","id,store_name", 1, "--Select store--", 0, "" );
										?>	
                                	</td>
                                </tr> -->
                                <tr>
                                <td class="must_entry_caption">Item Category</td>
			                        <td>
										<?
			                            	echo create_drop_down( "cbo_item_category", 160, $item_category,'', 1, '--Select Category--', '', '','0',$item_cate_credential_cond );
			                            ?>
			                        </td>
                        		</tr>						
                                <tr>
                                    <td class="must_entry_caption">Item Description</td>
                                    <td>
                                    	<input type="text" name="txt_item_desc" id="txt_item_desc" class="text_boxes" style="width:150px;" readonly placeholder="Double Click To Search" onDblClick="openmypage_itemDescription();" /></td>
                                </tr>
                                <tr style="display:none;">
                                    <td> Lot</td>						
                                    <td>                       
                                        <input type="text" name="txt_yarn_lot" id="txt_yarn_lot" class="text_boxes" style="width:150px" disabled="disabled" />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="must_entry_caption">Transfered Qnty</td>
                                    <td>
                                    	<input type="text" name="txt_transfer_qnty" id="txt_transfer_qnty" class="text_boxes_numeric" style="width:150px;" onKeyUp="calculate_value( );" /></td>
                                </tr>
                                <tr>
                                    <td>Supplier</td>						
                                    <td>
                                    	<input type="text" name="txt_supplier" id="txt_supplier" class="text_boxes" style="width:150px" disabled="disabled" />
                                        <input type="hidden" name="hide_supplier_id" id="hide_supplier_id" class="text_boxes" style="width:150px" disabled="disabled" />
                                    </td>
                                </tr>
							</table>
							<div style="float: right;">
								<fieldset>
	                        		<legend>From Store</legend>
	                        		<table>
	                        			 <tr>
		                                	<td width="100" class="must_entry_caption">From Store</td>
		                                    <td id="from_store_td">
		                                        <?
		                                           echo create_drop_down( "cbo_store_name", 152, $blank_array,"", 1, "--Select store--", 0, "" );
		                                        ?>	
		                                    </td>
                               			 </tr>
                               			 <tr>
                               			 	<td>Floor</td>
											<td id="floor_td">
												<? echo create_drop_down( "cbo_floor", 152,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
											</td>
                               			 </tr>
                               			 <tr>
                               			 	<td>Room</td>
											<td id="room_td">
												<? echo create_drop_down( "cbo_room", 152,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
											</td>
                               			 </tr>
                               			 <tr>
                               			 	<td>Rack</td>
											<td id="rack_td">
												<? echo create_drop_down( "txt_rack", 152,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
											</td>
                               			 </tr>
                               			 <tr>
                               			 	<td>Shelf</td>
											<td id="shelf_td">
												<? echo create_drop_down( "txt_shelf", 152,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
											</td>
                               			 </tr>
	                        		</table>
	                        	</fieldset>
	                        	<fieldset>
	                        		<legend>To Store</legend>
	                        		<table>
	                        			 <tr>
		                                	<td width="100" class="must_entry_caption">To Store</td>
		                                    <td id="to_store_td">
		                                        <?
		                                           echo create_drop_down( "cbo_store_name_to", 152, $blank_array,"", 1, "--Select store--", 0, "" );
		                                        ?>	
		                                    </td>
                               			 </tr>
                               			 <tr>
                               			 	<td>Floor</td>
											<td id="floor_td_to">
												<? echo create_drop_down( "cbo_floor_to", 152,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
											</td>
                               			 </tr>
                               			 <tr>
                               			 	<td>Room</td>
											<td id="room_td_to">
												<? echo create_drop_down( "cbo_room_to", 152,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
											</td>
                               			 </tr>
                               			 <tr>
                               			 	<td>Rack</td>
											<td id="rack_td_to">
												<? echo create_drop_down( "txt_rack_to", 152,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
											</td>
                               			 </tr>
                               			 <tr>
                               			 	<td>Shelf</td>
											<td id="shelf_td_to">
												<? echo create_drop_down( "txt_shelf_to", 152,$blank_array,"", 1, "--Select--", 0, "",0 ); ?>
											</td>
                               			 </tr>
	                        		</table>
	                        	</fieldset>
                        	</div>
						</fieldset>
					</td>
					<td width="2%" valign="top"></td>
					<td width="40%" valign="top">
						<fieldset>
                        <legend>Display</legend>					
                            <table id="tbl_display_info"  cellpadding="0" cellspacing="1" width="100%" >				
                                <tr>
                                    <td>Current Stock</td>						
                                	<td>
                                    	<input type="text" name="txt_current_stock" id="txt_current_stock" class="text_boxes_numeric" style="width:150px" disabled />
                                    	<input type="hidden" name="hidden_current_stock" id="hidden_current_stock" readonly>
                                    </td>
								</tr>
                                <tr>
                                    <td>Avg. Rate</td>						
                                    <td><input type="text" name="txt_rate" id="txt_rate" class="text_boxes_numeric" style="width:150px" disabled /></td>
                                </tr>
                                <tr>
                                    <td>Transfer Value </td>
                                    <td><input type="text" name="txt_transfer_value" id="txt_transfer_value" class="text_boxes_numeric" style="width:150px" disabled /></td>
                                </tr>					
                                <tr>
                                    <td>UOM</td>
                                    <td>
                                    	<?
										//function create_drop_down( $field_id, $field_width, $query, $field_list, $show_select, $select_text_msg, $selected_index, $onchange_func, $is_disabled, $array_index, $fixed_options, $fixed_values, $not_show_array_index, $tab_index, $new_conn, $field_name )
											echo create_drop_down( "cbo_uom", 160, $unit_of_measurement,'', 1, "Select", '', "",1);
											
										?>
                                    </td>
                                </tr>											
                            </table>                  
                       </fieldset>	
              		</td>
				</tr>	 	
                <tr>
                    <td align="center" colspan="3" class="button_container" width="100%">
                        <?
                            echo load_submit_buttons($permission, "fnc_yarn_transfer_entry", 0,1,"reset_form('transferEntry_1','div_transfer_item_list','','','disable_enable_fields(\'cbo_company_id\');active_inactive(0);')",1);
                        ?>
                        <input type="hidden" id="hidden_product_id" name="hidden_product_id" value="" >
                        <input type="hidden" name="update_dtls_id" id="update_dtls_id" readonly>
                        <input type="hidden" name="update_trans_issue_id" id="update_trans_issue_id" readonly>
                        <input type="hidden" name="update_trans_recv_id" id="update_trans_recv_id" readonly>
                        <input type="hidden" name="previous_from_prod_id" id="previous_from_prod_id" readonly>
                        <input type="hidden" name="previous_to_prod_id" id="previous_to_prod_id" readonly>
                        <input type="hidden" name="hidden_transfer_qnty" id="hidden_transfer_qnty" readonly>
                    </td>
                </tr>
            </table>
            <div style="width:880px;" id="div_transfer_item_list"></div>
		</fieldset>
	</div>
	</form>
</div>    
</body>  
<script src="../../includes/functions_bottom.js" type="text/javascript"></script> 
</html>
