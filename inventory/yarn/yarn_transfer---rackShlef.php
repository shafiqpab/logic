<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Yarn Transfer Entry
				
Functionality	:	
JS Functions	:
Created by		:	Fuad Shahriar 
Creation date 	: 	22-06-2013
Updated by 		: 	Kausar (Creating Report), Md.Didar (New fields add 1. No of Bag 2. No of Cone 3. Weight per bag)   
Update date		: 	13-01-2014,04-01-2018 
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
echo load_html_head_contents("Yarn Transfer Info","../../", 1, 1, '','',''); 

?>	

<script>
var permission='<? echo $permission; ?>';
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";

function active_inactive(str)
{
	$('#cbo_to_company_id').val(0);
	$('#cbo_company_id').val(0);
	//$('#cbo_store_name').val(0).attr('disabled',true);
	//$('#cbo_to_store').val(0).attr('disabled',true);
	if(str==1)
	{
		$('#cbo_to_company_id').removeAttr('disabled','disabled');	
	}
	else
	{
		$('#cbo_to_company_id').attr('disabled','disabled');
	}
	
}

function openmypage_systemId()
{
	var cbo_company_id = $('#cbo_company_id').val();
	var cbo_transfer_criteria = $('#cbo_transfer_criteria').val();
	if(cbo_transfer_criteria==1)
	{
		if (form_validation('cbo_transfer_criteria*cbo_company_id*cbo_to_company_id','Criteria*From Company*To Company')==false)
		{
			return;
		}
	}
	else
	{
		if (form_validation('cbo_transfer_criteria*cbo_company_id','Criteria*From Company')==false)
		{
			return;
		}
	}
	
	
	var title = 'Item Transfer Info';	
	var page_link = 'requires/yarn_transfer_controller.php?cbo_company_id='+cbo_company_id+'&cbo_transfer_criteria='+cbo_transfer_criteria+'&action=itemTransfer_popup';
	  
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=800px,height=370px,center=1,resize=1,scrolling=0','../');
	
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
		var transfer_id=this.contentDoc.getElementById("transfer_id").value; //Access form field with id="emailfield"
		
		reset_form('transferEntry_1','div_transfer_item_list','','');
		$('#cbo_store_name').attr('disabled',false);
		$('#cbo_to_store').attr('disabled',false);
		get_php_form_data(transfer_id, "populate_data_from_transfer_master", "requires/yarn_transfer_controller" );
		show_list_view(transfer_id,'show_transfer_listview','div_transfer_item_list','requires/yarn_transfer_controller','');
		set_button_status(0, permission, 'fnc_yarn_transfer_entry',1,1);
	}
}

function openmypage_itemDescription()
{
	var cbo_company_id = $('#cbo_company_id').val();
	var cbo_store_name = $('#cbo_store_name').val();

	if (form_validation('cbo_company_id*cbo_store_name','Company')==false)
	{
		return;
	}
	
	var title = 'Item Description Info';	
	var page_link = 'requires/yarn_transfer_controller.php?cbo_company_id='+cbo_company_id+'&cbo_store_name='+cbo_store_name+'&action=itemDescription_popup';
	  
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=970px,height=400px,center=1,resize=1,scrolling=0','../');
	
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];//("search_order_frm"); //Access the form inside the modal window
		var product_id=this.contentDoc.getElementById("product_id").value; //Access form field with id="emailfield"
		get_php_form_data(product_id+"__"+cbo_store_name, "populate_data_from_product_master", "requires/yarn_transfer_controller" );
		$('#cbo_transfer_criteria').attr('disabled',true);
		$('#cbo_company_id').attr('disabled',true);
		$('#cbo_store_name').attr('disabled',true);
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
		print_report( $('#cbo_company_id').val()+'*'+$('#update_id').val()+'*'+report_title, "yarn_transfer_print", "requires/yarn_transfer_controller" ) 
		return;
	}
	else if(operation==0 || operation==1 || operation==2)
	{
		
		if( form_validation('cbo_transfer_criteria*cbo_company_id*txt_transfer_date*cbo_store_name*cbo_to_store*cbo_to_store*txt_item_desc*txt_transfer_qnty','Transfer Criteria*Company*Transfer Date*From Store*To Store*Item Description*Transfered Qnty')==false )
		{
			return;
		}	
		
		var current_date = '<? echo date("d-m-Y"); ?>';
		if (date_compare($('#txt_transfer_date').val(), current_date) == false) {
			alert("Transfer Date Can not Be Greater Than Current Date");
			return;
		}
                
		if($("#cbo_transfer_criteria").val()==1)
		{
			if($("#cbo_company_id").val()*1==$("#cbo_to_company_id").val()*1)
			{
				alert("Same Company Not Allow.");
				return;
			}
			if($("#cbo_to_company_id").val()==0)
			{
				alert("Please Select To Company.");
				$("#cbo_to_company_id").focus();
				return;
			}
			
			if(($("#txt_transfer_qnty").val()*1 > $("#txt_current_stock").val()*1+$("#hidden_transfer_qnty").val()*1)) 
			{
				alert("Transfer Quantity Exceeds Current Stock Quantity.");
				$("#txt_transfer_qnty").focus();
				return;
			}
		}
		else
		{
			if($("#cbo_store_name").val()*1==$("#cbo_to_store").val()*1)
			{
				alert("Same Store Not Allow.");
				return;
			}
		}
		
		var dataString = "txt_system_id*cbo_transfer_criteria*cbo_company_id*cbo_to_company_id*txt_transfer_date*txt_challan_no*cbo_item_category*cbo_store_name*cbo_to_store*txt_yarn_lot*txt_transfer_qnty*txt_yarn_brand*txt_rate*txt_transfer_value*cbo_uom*txt_no_of_bag*txt_no_of_cone*txt_weight_per_bag*update_id*hide_brand_id*hidden_product_id*update_dtls_id*update_trans_issue_id*update_trans_recv_id*previous_from_prod_id*previous_to_prod_id*hidden_transfer_qnty*txt_item_desc*origin_product_id*txt_btb_lc_id*txt_remarks";
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string(dataString,"../../");
		
		//alert(data);
		freeze_window(operation);
		http.open("POST","requires/yarn_transfer_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_yarn_transfer_entry_reponse;
	}
}

function fnc_yarn_transfer_entry_reponse()
{	
	if(http.readyState == 4) 
	{	  
		//release_freezing(); return;		
		var reponse=trim(http.responseText).split('**');		
		if(reponse[0]==30)
		{
			alert(reponse[1]);
			release_freezing();
			return;
		}
		if(reponse[0]==20)
		{
			alert(reponse[1]);
			release_freezing();
			return;
		}
		if(reponse[0]==21)
		{
			alert(reponse[1]);
			release_freezing();
			return;
		}
		show_msg(reponse[0]); 	
			
		if(reponse[0]==0 || reponse[0]==1)
		{
			$("#update_id").val(reponse[1]);
			$("#txt_system_id").val(reponse[2]);
			$('#cbo_transfer_criteria').attr('disabled',true);
			$('#cbo_company_id').attr('disabled',true);
			$('#cbo_to_company_id').attr('disabled',true);
			//$('#cbo_store_name').attr('disabled',false);
			//$('#cbo_to_store').attr('disabled',false);
                        $('#cbo_store_name').attr('disabled',true);
			$('#cbo_to_store').attr('disabled',true);
			
			reset_form('transferEntry_1','','','','','update_id*txt_system_id*cbo_transfer_criteria*cbo_company_id*cbo_to_company_id*txt_transfer_date*txt_challan_no*txt_remarks*cbo_store_name*cbo_to_store');
			show_list_view(reponse[1],'show_transfer_listview','div_transfer_item_list','requires/yarn_transfer_controller','');
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
		load_drop_down( 'requires/yarn_transfer_controller', company_id, 'load_drop_down_store_to', 'store_td' );
	}
}

function openmypage_btb_selection(){
    if( form_validation('cbo_company_id*txt_yarn_lot','Company*Lot No.')==false )
    {
            return;
    }
    var comany_name = $("#cbo_company_id").val();
    var lot_no = $("#txt_yarn_lot").val();
    var page_link='requires/yarn_transfer_controller.php?action=btb_selection_popup&lot_no='+lot_no+'&comany_name='+comany_name; 
    var title="Search BTB Selection Popup";
    emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=720px,height=370px,center=1,resize=0,scrolling=0','../')
    emailwindow.onclose=function()
    {
        var theform=this.contentDoc.forms[0];
        var btb_id = this.contentDoc.getElementById("hidden_btb_id").value;
        var btb_lc_no = this.contentDoc.getElementById("hidden_btb_lc_no").value;

        $('#txt_btb_selection').val(btb_lc_no);
        $('#txt_btb_lc_id').val(btb_id);

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
        <legend>Yarn Transfer Entry</legend>
        <br>
        	<fieldset style="width:900px;">
                <table width="880" cellspacing="2" cellpadding="2" border="0" id="tbl_master">
                    <tr>
                        <td colspan="3" align="right"><strong>Transfer System ID</strong><input type="hidden" name="update_id" id="update_id" /></td>
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
                                echo create_drop_down("cbo_transfer_criteria", 160,$item_transfer_criteria,"", 1,"-- Select --",'0',"active_inactive(this.value);",'','1,2');
                            ?>
                        </td>
                        <td class="must_entry_caption">Company</td>
                        <td>
                            <? 
								echo create_drop_down( "cbo_company_id", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "load_room_rack_self_bin('requires/yarn_transfer_controller', 'store','store_td', this.value);" );
								//load_drop_down( 'requires/yarn_transfer_controller', this.value, 'load_drop_down_store', 'store_td_from' );fn_to_store(this.value);
							?>
                        </td>
                        <td>To Company</td>
                        <td>
                            <? 
								echo create_drop_down( "cbo_to_company_id", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "load_drop_down( 'requires/yarn_transfer_controller', this.value, 'load_drop_down_store_to', 'store_td' );",1 );
							?>
                        </td>
                    </tr>
                    <tr>
                        <td class="must_entry_caption">Transfer Date</td>
                        <td>
                            <input type="text" name="txt_transfer_date" id="txt_transfer_date" class="datepicker" style="width:148px;" readonly placeholder="Select Date" value="<? echo date("d-m-Y");?>" />
                        </td>
                        <td>Challan No.</td>
                        <td>
                            <input type="text" name="txt_challan_no" id="txt_challan_no" class="text_boxes" style="width:148px;" maxlength="20" title="Maximum 20 Character" />
                        </td>
                        <td>Item Category</td>
                        <td>
							<?
                            	echo create_drop_down( "cbo_item_category", 160, $item_category,'', 0, '', '', '','1',1 );
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2"></td>
                        <td>Remarks </td>
                        <td colspan="3">
                            <input type="text" name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:465px;"  placeholder="remarks" />
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
                            <table id="tbl_item_info"  cellpadding="0" cellspacing="1" width="45%" style="float: left;">										
                                <tr>
                                	<td width="30%" class="must_entry_caption">From Store</td>
                                    <td id="store_td_from">
                                        <?
                                            echo create_drop_down( "cbo_store_name_from", 160, $blank_array,"", 1, "--Select store--", 0, "" );
                                        ?>	
                                    </td>
                                </tr>
                                <tr>	
                                	<td class="must_entry_caption">To Store</td>
                                    <td id="store_td_to">
                                   		<?
											echo create_drop_down( "cbo_to_store", 160, $blank_array,"", 1, "--Select store--", 0, "" );
										?>	
                                	</td>
                                </tr>						
                                <tr>
                                    <td class="must_entry_caption">Item Description</td>
                                    <td>
                                    	<input type="text" name="txt_item_desc" id="txt_item_desc" class="text_boxes" style="width:150px;" readonly placeholder="Double Click To Search" onDblClick="openmypage_itemDescription();" /></td>
                                </tr>
                                <tr>
                                    <td>Yarn Lot</td>						
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
                                    <td>Yarn Brand</td>						
                                    <td>
                                    	<input type="text" name="txt_yarn_brand" id="txt_yarn_brand" class="text_boxes" style="width:150px" disabled="disabled" />
                                        <input type="hidden" name="hide_brand_id" id="hide_brand_id" class="text_boxes" style="width:150px" disabled="disabled" />
                                    </td>
                                </tr>
                                <tr>
                                    <td>BTB Selection</td>
                                    <td>
                                        <input type="text" class="text_boxes" id="txt_btb_selection" name="txt_btb_selection" value="" onDblClick="openmypage_btb_selection()"  placeholder="Double Click" style="width:150px;" readonly>
                                        <input type="hidden" class="text_boxes" id="txt_btb_lc_id" name="txt_btb_lc_id" value="">
                                    </td>
                                </tr>
                                
                                <tr>
                                    <td>No of Bag</td>
                                    <td><input type="text" name="txt_no_of_bag" id="txt_no_of_bag" class="text_boxes_numeric" style="width:150px;"/></td>
                                </tr>
                                
                                <tr>
                                    <td>No of Cone</td>
                                    <td><input type="text" name="txt_no_of_cone" id="txt_no_of_cone" class="text_boxes_numeric" style="width:150px;"/></td>
                                </tr>
                                
                                <tr>
                                    <td>Weight per bag</td>
                                    <td><input type="text" name="txt_weight_per_bag" id="txt_weight_per_bag" class="text_boxes_numeric" style="width:150px;"/></td>
                                </tr>
                                
							</table>
							<div style="float: right;">
								<fieldset>
	                        		<legend>From Store</legend>
	                        		<table>
	                        			 <tr>
		                                	<td width="100" class="must_entry_caption">From Store</td>
		                                    <td id="store_td">
		                                        <?
		                                           echo create_drop_down( "cbo_store_name", 130, $blank_array,"", 1, "--Select store--", 0, "" );
		                                        ?>	
		                                    </td>
                               			 </tr>
                               			 <tr>
                               			 	<td>Floor</td>
											<td id="floor_td">
												<? echo create_drop_down( "cbo_floor", 130,"","", 1, "--Select--", 0, "",0 ); ?>
											</td>
                               			 </tr>
                               			 <tr>
                               			 	<td>Room</td>
											<td id="room_td">
												<? echo create_drop_down( "cbo_room", 130,"","", 1, "--Select--", 0, "",0 ); ?>
											</td>
                               			 </tr>
                               			 <tr>
                               			 	<td>Rack</td>
											<td id="rack_td">
												<? echo create_drop_down( "txt_rack", 130,"","", 1, "--Select--", 0, "",0 ); ?>
											</td>
                               			 </tr>
                               			 <tr>
                               			 	<td>Shelf</td>
											<td id="shelf_td">
												<? echo create_drop_down( "txt_shelf", 130,"","", 1, "--Select--", 0, "",0 ); ?>
											</td>
                               			 </tr>
	                        		</table>
	                        	</fieldset>
	                        	<fieldset>
	                        		<legend>To Store</legend>
	                        		<table>
	                        			 <tr>
		                                	<td width="100" class="must_entry_caption">To Store</td>
		                                    <td id="store_to_td">
		                                        <?
		                                           echo create_drop_down( "cbo_to_store_name", 130, $blank_array,"", 1, "--Select store--", 0, "" );
		                                        ?>	
		                                    </td>
                               			 </tr>
                               			 <tr>
                               			 	<td>Floor</td>
											<td id="floor_to_td">
												<? echo create_drop_down( "cbo_to_floor", 130,"","", 1, "--Select--", 0, "",0 ); ?>
											</td>
                               			 </tr>
                               			 <tr>
                               			 	<td>Room</td>
											<td id="room_to_td">
												<? echo create_drop_down( "cbo_to_room", 130,"","", 1, "--Select--", 0, "",0 ); ?>
											</td>
                               			 </tr>
                               			 <tr>
                               			 	<td>Rack</td>
											<td id="rack_to_td">
												<? echo create_drop_down( "txt_to_rack", 130,"","", 1, "--Select--", 0, "",0 ); ?>
											</td>
                               			 </tr>
                               			 <tr>
                               			 	<td>Shelf</td>
											<td id="shelf_to_td">
												<? echo create_drop_down( "txt_to_shelf", 130,"","", 1, "--Select--", 0, "",0 ); ?>
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
											echo create_drop_down( "cbo_uom", 160, $unit_of_measurement,'', 0, "", '', "",1,12 );
											
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
                            echo load_submit_buttons($permission, "fnc_yarn_transfer_entry", 0,1,"reset_form('transferEntry_1','div_transfer_item_list','','','disable_enable_fields(\'cbo_company_id*cbo_transfer_criteria\');active_inactive(0);')",1);
                        ?>
                        <!--<input type="hidden" id="update_id" name="update_id" value="" >-->
                        <input type="hidden" id="hidden_product_id" name="hidden_product_id" value="" >
                        <input type="hidden" id="origin_product_id" name="origin_product_id" value="" >
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
