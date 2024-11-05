<?
/*-------------------------------------------- Comments

Purpose			: 	This form will create for buyer Export LC amendment
					
Functionality	:	
				

JS Functions	:

Created by		:	Bilas 
Creation date 	: 	22-11-2012
Updated by 		: 	Fuad Shahriar	
Update date		: 	22-04-2013	  	   

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
echo load_html_head_contents("Export LC Amendment Form", "../../", 1, 1,'','1','');
?>	

<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  

var permission='<? echo $permission; ?>';


function fnc_amendment_save(operation) 
{ 
	if(operation==2)
	{
		show_msg('13');
		return;
	}
		
	if ( form_validation('txt_export_lc_no*txt_amendment_no*txt_amendment_date*cbo_value_change_by','Export LC No*Amendment No*Amendment Date*Value Changed By')==false )
	{
		return;
	}
	else if(parseInt(Number($("#txt_amendment_no").val()))==0)
	{
		alert("Amendment No Should Be Greater Than 0");
		$("#txt_amendment_no").val('');
		$("#txt_amendment_no").focus();
		return;
	}
	else
	{
		var data="action=save_update_delete_amendment&operation="+operation+get_submitted_data_string('txt_amendment_no*txt_amendment_date*txt_amendment_value*txt_amed_lien_date*cbo_value_change_by*txt_last_shipment_date_amnd*txt_expiry_date_amend*cbo_shipping_mode_amnd*cbo_inco_term*txt_inco_term_place*txt_port_of_entry_amnd*txt_port_of_loading_amnd*txt_port_of_discharge_amnd*cbo_pay_term_amnd*txt_tenor_amnd*txt_claim_adjustment_amnd*cbo_claim_adjust_by*txt_discount_clauses_amnd*txt_remarks_amnd*txt_system_id*update_id*hide_amendment_value*hide_value_change_by*hide_claim_adjustment_amnd*hide_claim_adjust_by',"../../");
		
		freeze_window(operation);
		
		http.open("POST","requires/export_lc_amendment_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_amendment_save_Reply_info;
	}	
}

function fnc_amendment_save_Reply_info()
{
	if(http.readyState == 4) 
	{
		// alert(http.responseText);
		var reponse=http.responseText.split('**');
		show_msg(trim(reponse[0]));	
		
		if((reponse[0]==0 || reponse[0]==1))
		{	
			reset_form('amendmentFrm_1','','','','');
			get_php_form_data( reponse[2], "populate_data_from_export_lc", "requires/export_lc_amendment_controller" );
			set_button_status(0, permission, 'fnc_amendment_save',1);
		}
		else if(reponse[0]==14)
		{
			alert("This is not your last amendment. So You can't change it.");
		}
		else if(reponse[0]==11)
		{
			alert(reponse[1]);
		}
		release_freezing();		
	}
}

function openamendment_popup()
{
 	if ( form_validation('txt_export_lc_no','Export LC No')==false )
	{
		return;
	}
	
	var page_link='requires/export_lc_amendment_controller.php?action=amendment_popup&export_lc_id='+$('#txt_system_id').val();
	var title='Amendment List';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=360px,center=1,resize=1,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var hidden_amendment_no=this.contentDoc.getElementById("hidden_amendment_no").value;
		if(trim(hidden_amendment_no)!="")
		{
			freeze_window(5);
			get_php_form_data( hidden_amendment_no, "get_amendment_data", "requires/export_lc_amendment_controller" );
			release_freezing();
		}
	}
}


function fn_add_export_lc()
{ 
	var page_link='requires/export_lc_amendment_controller.php?action=export_lc_search';
	var title='Export LC Search';
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1050px,height=450px,center=1,resize=1,scrolling=0','../')
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var export_lc_id=this.contentDoc.getElementById("hidden_export_lc_id").value;
		
		if(trim(export_lc_id)!="")
		{
			freeze_window(5);
			reset_form('amendmentFrm_2','','','txt_tot_row,0','$(\'#tbl_order_list tbody tr:not(:first)\').remove();','hidden_selectedID');
			get_php_form_data( export_lc_id, "populate_data_from_export_lc", "requires/export_lc_amendment_controller" );
			show_list_view(export_lc_id,'show_po_active_listview','po_list_view','requires/export_lc_amendment_controller','');
			release_freezing();
		}
	}
	
}

function fnc_po_selection_save(operation)
{
	if(operation==2)
	{
		show_msg('13');
		return;
	}
	
	if( form_validation('txt_export_lc_no*txt_amendment_no','Export LC No*Amendment No')==false )
	{
		return;
	}
	var row_num = $('table#tbl_order_list tbody tr').length;
	var submit_data="";
	for(var j=1;j<=row_num;j++)  
	{
		if(trim($("#txtordernumber_"+j).val())!="")
		{
			if($("#txtattachedqnty_"+j).val()*1 <= 0)
			{
				alert("Please Insert Attach Qnty");
				$("#txtattachedqnty_"+j).focus();
				return;
			}
			submit_data += "*hiddenwopobreakdownid_"+j+"*txtattachedqnty_"+j+"*hiddenunitprice_"+j+"*txtattachedvalue_"+j+"*cbopostatus_"+j+"*txtfabdescrip_"+j+"*txtcategory_"+j+"*txthscode_"+j;
		}
	}
	if(submit_data=="")
	{
		alert("Please Select Order No");
		return;
	}
	var data="action=save_update_delete_lc_order_info&noRow="+row_num+"&operation="+operation+get_submitted_data_string('txt_system_id*update_id*hiddenexportlcorderid'+submit_data,"../../");
	
	freeze_window(operation);
	
	http.open("POST","requires/export_lc_amendment_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fnc_po_selection_save_Reply_info;
}

function fnc_po_selection_save_Reply_info()
{
	if(http.readyState == 4) 
	{
		var reponse=http.responseText.split('**');	

		show_msg(trim(reponse[0]));	
		//alert(http.responseText);
		if((reponse[0]==0 || reponse[0]==1))
		{	
			reset_form('amendmentFrm_2','','','txt_tot_row,0','$(\'#tbl_order_list tbody tr:not(:first)\').remove();','hidden_selectedID');
			show_list_view(reponse[1],'show_po_active_listview','po_list_view','requires/export_lc_amendment_controller','');
			load_po_id(2);
			set_button_status(0, permission, 'fnc_po_selection_save',2);
		}
		else if(reponse[0]==13)
		{
			alert('Bellow Invoice Found. Detach Not Allowed.\n Invoice No: '+reponse[1]+"\n");
		}
		else if (reponse[0]==11)
		{
			alert(reponse[1]);
		}

		/*else if(reponse[0]==14)
		{
			alert("This is not your last amendment. So You can't change it.");
		}*/
		release_freezing(); 
	}
}

function openmypage(page_link,title,row_num)
{
	if( form_validation('txt_export_lc_no*txt_amendment_no','Export LC No*Amendment No')==false )
	{
		return;
	}
	else
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1100px,height=360px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var order_id=this.contentDoc.getElementById("txt_selected_id").value; 
			//alert(order_id);
			if(order_id!="")
			{
				var pre_selectID = $("#hidden_selectedID").val();
				
				if(trim(pre_selectID)=="") $("#hidden_selectedID").val(order_id);else $("#hidden_selectedID").val(pre_selectID+","+order_id); 
				
				var tot_row=$('#txt_tot_row').val();
				
				var data=order_id+"**"+tot_row;
				var list_view_orders = return_global_ajax_value( data, 'order_list_for_attach', '', 'requires/export_lc_amendment_controller');				 
				var order_no=$('#txtordernumber_'+row_num).val();
				
				if(order_no=="")
				{
					$("#tr_"+row_num).remove();
				}
				
				$("#tbl_order_list tbody:last").append(list_view_orders);	
				
				var numRow = $('table#tbl_order_list tbody tr').length; 
				$('#txt_tot_row').val(numRow);
				
				var ddd={ dec_type:2, comma:0, currency:''}
				math_operation( "totalOrderqnty", "txtorderqnty_", "+", numRow );
				math_operation( "totalOrdervalue", "txtordervalue_", "+", numRow, ddd );
				math_operation( "totalAttachedqnty", "txtattachedqnty_", "+", numRow );
				math_operation( "totalAttachedvalue", "txtattachedvalue_", "+", numRow, ddd );
				set_all_onclick();
			}
 	 
		}
		
	}//end else
}


function validate_attach_qnty(row_id)
{
	if( form_validation('txtordernumber_'+row_id,'Order Number')==false )
	{
		$('#txtattachedqnty_'+row_id).val('');
		return;
	}
	else
	{
		var attached_qnty=0; 
		var txt_rate=parseFloat(Number($('#hiddenunitprice_'+row_id).val()));
		var txt_attach_order_qnty=parseInt(Number($('#txtattachedqnty_'+row_id).val()));
		var order_attached_qnty=parseInt(Number($('#order_attached_qnty_'+row_id).val()));
		var txt_order_qnty=parseInt(Number($('#txtorderqnty_'+row_id).val()));
		var hide_attached_qnty=parseInt(Number($('#hideattachedqnty_'+row_id).val()));
		
		var pre_att_value=hide_attached_qnty*txt_rate;
		
		var txt_lc_no=$('#order_attached_lc_no_'+row_id).val();
		var txt_lc_qnty=parseInt(Number($('#order_attached_lc_qty_'+row_id).val()));
		var txt_sc_no=$('#order_attached_sc_no_'+row_id).val();
		var txt_sc_qnty=parseInt(Number($('#order_attached_sc_qty_'+row_id).val()));
		
		attached_qnty=txt_attach_order_qnty+order_attached_qnty;
		
		var msg=''; 
		
		if(attached_qnty>txt_order_qnty)
		{
			if(txt_lc_no=="" && txt_sc_no=="")
			{
				msg='';
			}
			else if(txt_lc_no!="" && txt_sc_no=="")
			{
				msg="\nPrevious Attached Info:\nLC NO: "+txt_lc_no+"; Attached Qty: "+txt_lc_qnty;
			}
			else if(txt_lc_no=="" && txt_sc_no!="")
			{
				msg="\nPrevious Attached Info:\nSC NO: "+txt_sc_no+"; Attached Qty: "+txt_sc_qnty;
			}
			else
			{
				msg="\nPrevious Attached Info:\nLC NO: "+txt_sc_no+"; Attached Qty: "+txt_sc_qnty+"\nSC NO: "+txt_sc_no+"; Attached Qty: "+txt_sc_qnty;
			}
			
			alert("Attached Qnty Exceeded Order Qnty"+msg);
			
			$('#txtattachedqnty_'+row_id).val(hide_attached_qnty);
			$('#txtattachedvalue_'+row_id).val(pre_att_value.toFixed(2));
			calculate_attach_val(row_id);
		}
		else
		{
			calculate_attach_val(row_id);
		}
	}
}

function calculate_attach_val(row_id)
{
	if( form_validation('txtordernumber_'+row_id,'Order Number')==false )
	{
		$('#hiddenunitprice_'+row_id).val('');
		return;
	}
	var attached_val=0;
	var txt_rate=parseFloat(Number($('#hiddenunitprice_'+row_id).val()));
	var txt_attach_order_qnty=parseInt(Number($('#txtattachedqnty_'+row_id).val()));
	attached_val=txt_attach_order_qnty*txt_rate;
	$('#txtattachedvalue_'+row_id).val(attached_val.toFixed(2));
	
	var numRow = $('table#tbl_order_list tbody tr').length; 
	
	var ddd={ dec_type:2, comma:0, currency:''}
	math_operation( "totalAttachedqnty", "txtattachedqnty_", "+", numRow );
	math_operation( "totalAttachedvalue", "txtattachedvalue_", "+", numRow, ddd );
}

function load_po_id(type)
{
	var export_lc_id=$('#txt_system_id').val();
	var update_id=$('#update_id').val();
	var data=export_lc_id+"**"+type+"**"+update_id;
	if(export_lc_id!="")
	{
		get_php_form_data(data, 'populate_attached_po_id', 'requires/export_lc_amendment_controller');
	}
}

function fn_add_date_field()
{
	$("#txt_expiry_date_amend").val(add_days($('#txt_last_shipment_date_amnd').val(),'15'));
}

//fnc_lc_amendment()
function fnc_export_lc_amendment(type)
{
	//alert("su..re");
	if (form_validation('txt_system_id','System ID')==false )
	{
		return;
	}
	if(type==1){
	print_report(5+'**'+$('#txt_system_id').val(),'export_lc_amendment_letter','requires/export_lc_amendment_controller');
	}
	if(type==2){
		print_report(5+'**'+$('#txt_system_id').val(),'request_for_insert_amendment','requires/export_lc_amendment_controller');
	}
	if(type==3){

		if (form_validation('update_id','Amendment No')==false )
		{
			return;
		}
		print_report(5+'**'+$('#txt_system_id').val()+'**'+$('#update_id').val(),'request_for_insert_amendment2','requires/export_lc_amendment_controller');
	}
}


	
</script>

<style>
#currentDataTable input{
	width:135px;
}
#amendmentDataTable input{
	width:135px;
}

</style> 

</head>
 
<body onLoad="set_hotkey()">
	<div style="width:100%;" align="center">																	
     	<? echo load_freeze_divs ("../../",$permission); ?>
     
        <fieldset style="width:1140px; margin-bottom:10px;">
          <form id="amendmentFrm_1" name="amendmentFrm_1" >
            <fieldset style="width:520px; margin-bottom:10px;float:left">
            <legend align="center">Current Record</legend>
            	<table width="100%" class="" id="currentDataTable">
                    <tr>
                        <td>&nbsp;</td>
                        <td align="right" class="must_entry_caption">Export LC No</td>
                        <td colspan="2">
                            <input type="hidden" id="txt_system_id" readonly /> 
                            <input type="text"  name="txt_export_lc_no"  id="txt_export_lc_no" class="text_boxes" placeholder="Double Click To Search" onDblClick="fn_add_export_lc()"  readonly="readonly" >
                        </td>
                    </tr>
                    <tr>
                        <td width="110">Benificiary</td>
                        <td width="135">
							<?
								echo create_drop_down( "cbo_beneficiary_name", 145, "select comp.id, comp.company_name from lib_company comp where comp.status_active =1 and comp.is_deleted=0 and core_business in(1,3) $company_cond order by comp.company_name","id,company_name", 1, "Display", $selected, "",1 );
						   	?> 
                        </td>
                        <td width="109">Buyer Name</td>
						<td>
					  	<? 
                            echo create_drop_down( "cbo_buyer_name", 146, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond group by buy.id,buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "Display", $selected, "" ,1);   
                        ?>	 
						</td>
                    </tr>
                    <tr>
                        <td>Internal File No</td>
                        <td><input type="text" name="txt_internal_file_no" id="txt_internal_file_no" value="Display" class="text_boxes_numeric" disabled="disabled" /></td>
                        <td>LC Value</td>
                        <td><input type="text" name="txt_lc_value" id="txt_lc_value" value="Display" class="text_boxes_numeric" disabled="disabled" ></td>
                    </tr>
                    <tr>
                        <td>Currency</td>
                        <td>
							<?
							   	echo create_drop_down( "cbo_currency_name", 145, $currency,"", 1, "Display", 0, "",1 );
							?>
                        </td>
                        <td>Replacement LC</td>
                        <td>
							<?
							   	echo create_drop_down( "cbo_replacement_lc", 146, $yes_no,"", 1, "Display", 0, "",1 );
							?>
                        </td>
                    </tr>
                    <tr>
                    	<td>Lien Bank</td>
                        <td>
							<?
							   	echo create_drop_down( "cbo_lien_bank", 145, "select bank_name,id from lib_bank where is_deleted=0 and status_active=1 and lien_bank=1 order by bank_name","id,bank_name", 1, "Display", 0, "",1 );
							?>
                        </td>
                        <td>Lien Date</td>
                        <td><input type="text" name="txt_lien_date" id="txt_lien_date" class="datepicker" value="Display" disabled="disabled" ></td>
                    </tr> 
                    <tr>
                    	<td>Last Ship. Date</td>
                        <td><input type="text" name="txt_last_shipment_date" id="txt_last_shipment_date" value="Display" class="datepicker" disabled="disabled"></td>
                        <td>Expiry Date</td>
                        <td><input type="text" name="txt_expiry_date" id="txt_expiry_date" class="datepicker" value="Display" disabled="disabled"></td>
                    </tr> 
                    <tr>
                        <td>Tolerance %</td>
                        <td><input type="text" name="txt_tolerance" id="txt_tolerance" class="text_boxes_numeric" value="Display" disabled="disabled"></td>
                        <td>Shipping Mode</td>
                        <td>
							<?
							   	echo create_drop_down( "cbo_shipping_mode", 146, $shipment_mode,"", 1, "Display", 0, "",1 );
							?>
                        </td>
                    </tr> 
                    <tr>
                        <td>Port of Entry</td>
                        <td><input type="text" name="txt_port_of_entry" id="txt_port_of_entry" class="text_boxes" value="Display" disabled="disabled"/></td>
                        <td>Port of Loading</td>
                        <td><input type="text" name="txt_port_of_loading" id="txt_port_of_loading" class="text_boxes" value="Display" disabled="disabled"/></td>
                    </tr>
                    <tr>
                        <td>Port of Discharge</td>
                        <td><input type="text" name="txt_port_of_discharge" id="txt_port_of_discharge" class="text_boxes" value="Display" disabled="disabled" /></td>
                        <td>Pay Term</td>
                        <td><?
							   	echo create_drop_down( "cbo_pay_term", 146, $pay_term,"", 1, "Display", 0, "", 1, "1,2,3,4" );
							?></td>
                    </tr>
                    <tr>
                        <td>Tenor</td>
                        <td><input type="text" name="txt_tenor" id="txt_tenor" class="text_boxes_numeric" value="Display" disabled="disabled" /></td>
                      <td>Claim Adjust.</td>
                        <td><input type="text" name="txt_claim_adjustment" id="txt_claim_adjustment"  class="text_boxes_numeric" value="Display" disabled="disabled"/></td>
                    </tr>     
                    <tr>
                      	<td>Discount Clauses</td>
                      	<td colspan="4"><textarea name="txt_discount_clauses" id="txt_discount_clauses" style="width:97%" class="text_area" value="Display" disabled="disabled"></textarea></td>
                    </tr>     
                    <tr>
                    	<td>Remarks</td>
               			<td colspan="4">
                        	<textarea name="txt_remarks" id="txt_remarks" style="width:97%" class="text_area" value="Display" disabled="disabled" ></textarea>
                        </td>
                    </tr>
                    <tr>
                    	<td colspan="4" height="18">&nbsp;</td>
                    </tr>     
                </table>
            </fieldset>
            <fieldset style="width:520px; margin-bottom:10px;margin-left:5px;float:left">
            <legend align="center">Amendment Record</legend>
            	<table width="100%" class="" id="amendmentDataTable">
                	<tr>
                    	<td class="must_entry_caption">Amendment No</td>
                        <td>
                        	<input type="text"  name="txt_amendment_no"  id="txt_amendment_no" class="text_boxes_numeric" placeholder="Double Click To Search" onDblClick="openamendment_popup()">
                            <input type="hidden" id="update_id" readonly />
                        </td>
                        <td class="must_entry_caption">Amendment Date</td>
                        <td><input type="text"  name="txt_amendment_date"  id="txt_amendment_date" class="datepicker" readonly></td>
                    </tr>
                    <tr>
                    	<td>Amendment Value</td>
                        <td>
                        	<input type="text" name="txt_amendment_value" id="txt_amendment_value" class="text_boxes_numeric">
                            <input type="hidden" name="hide_amendment_value" id="hide_amendment_value" class="text_boxes_numeric">
                        </td>
                        <td >Amend Lien Date</td>
                        <td><input type="text"  name="txt_amed_lien_date"  id="txt_amed_lien_date" class="datepicker" readonly></td>
                    </tr>
                    <tr>
                        <td class="must_entry_caption">Value Changed By</td>
                        <td>
							<? echo create_drop_down( "cbo_value_change_by", 145, $increase_decrease,"", 1, "--- Select ---", 0, "" ); ?>
                            <input type="hidden" name="hide_value_change_by" id="hide_value_change_by" class="text_boxes_numeric">
                        </td>
						<td></td>
                        <td>
                        </td>
                    </tr>
                    <tr>
                    	<td>Last Ship. Date</td>
                        <td><input type="text"  name="txt_last_shipment_date_amnd"  id="txt_last_shipment_date_amnd" class="datepicker" onChange="fn_add_date_field();"></td>
                        <td>Expiry Date</td>
                        <td><input type="text"  name="txt_expiry_date_amend"  id="txt_expiry_date_amend" class="datepicker"></td>
                    </tr>
                    <tr>
                    	<td>Shipping Mode</td>
                        <td><? echo create_drop_down( "cbo_shipping_mode_amnd", 145, $shipment_mode,"", 1, "--- Select ---", 0, "" ); ?></td>
                        <td>Incoterm</td>
                        <td> 
                        	<?
							   	echo create_drop_down( "cbo_inco_term", 145, $incoterm,"", 1, "--- Select ---", 0, "" );
							?>
                        </td>
                    </tr>
                    <tr>
                    	<td>Incoterm Place</td>
                        <td><input type="text"  name="txt_inco_term_place"  id="txt_inco_term_place" class="text_boxes"></td>
                        <td>Port of Entry</td>
                        <td><input type="text"  name="txt_port_of_entry_amnd"  id="txt_port_of_entry_amnd" class="text_boxes"></td>
                    </tr>
                    <tr>
                    	<td>Port of Loading</td>
                        <td><input type="text"  name="txt_port_of_loading_amnd"  id="txt_port_of_loading_amnd" class="text_boxes"></td>
                        <td>Port of Discharge</td>
                        <td><input type="text"  name="txt_port_of_discharge_amnd"  id="txt_port_of_discharge_amnd" class="text_boxes"></td>
                    </tr>
                    <tr>
                    	<td>Pay Term</td>
                        <td><? echo create_drop_down( "cbo_pay_term_amnd", 145, $pay_term,"", 1, "--- Select ---", 0, "", "", "1,2,3,4" ); ?></td>
                        <td>Tenor</td>
                        <td><input type="text"  name="txt_tenor_amnd"  id="txt_tenor_amnd" class="text_boxes"></td>
                    </tr>
                    <tr>
                      <td>Claim Adjust.</td>
                      <td>
                      	<input type="text" name="txt_claim_adjustment_amnd" id="txt_claim_adjustment_amnd"  class="text_boxes_numeric" />
                        <input type="hidden" name="hide_claim_adjustment_amnd" id="hide_claim_adjustment_amnd"  class="text_boxes_numeric" />
                      </td>
                      <td>Claim Adjust. By</td>
                      <td>
					  	<? echo create_drop_down( "cbo_claim_adjust_by", 145, $increase_decrease,"", 1, "--- Select ---", 0, "" ); ?>
                        <input type="hidden" name="hide_claim_adjust_by" id="hide_claim_adjust_by"  class="text_boxes_numeric" />
                      </td>
                    </tr>
                    <tr>
                      <td>Discount Clauses</td>
                      <td colspan="3"><textarea name="txt_discount_clauses_amnd" id="txt_discount_clauses_amnd" style="width:97%" class="text_area" maxlength="2000" title="Maximum Character 2000" ></textarea></td>
                    </tr>
                    <tr>
                    	<td>Remarks</td>
                        <td colspan="3"><textarea name="txt_remarks_amnd" id="txt_remarks_amnd" style="width:97%" class="text_area" maxlength="255" title="Maximum Character 255" ></textarea></td>
                    </tr>
                    <tr>
                        <td colspan="4" height="20" valign="middle" align="center" class="button_container">
                        <? echo load_submit_buttons( $permission, "fnc_amendment_save", 0,0 ,"reset_form('amendmentFrm_1*amendmentFrm_2','po_list_view','','','$(\'#tbl_order_list tbody tr:not(:first)\').remove();')",1) ; ?>
						<br>
                        <input type="button" value="Lien Letter" id="btn_lc_amendment" name="btn_lc_amendment" class="formbutton" style="width:100px;" onClick="fnc_export_lc_amendment(1)" />
                        <input type="button" value="Request for Insert Amendment" id="btn_lc_amendment" name="btn_lc_amendment" class="formbutton" style="width:180px;" onClick="fnc_export_lc_amendment(2)" />
                        <input type="button" value="Request for insert amendment 2" id="btn_lc_amendment" name="btn_lc_amendment" class="formbutton" style="width:180px;" onClick="fnc_export_lc_amendment(3)" />
                        </td>
                    </tr>
                </table>
            </fieldset>
        </form>
        <div style="clear:both" align="center">
        <input type="button" value="Click for already attached PO" name="Attached PO" id="Attached PO" class="formbutton" onClick="openmypage('requires/export_lc_amendment_controller.php?action=order_popup&types=attached_po_status&buyer_id='+document.getElementById('cbo_buyer_name').value+'&selectID='+document.getElementById('hidden_selectedID').value+'&export_lcID='+document.getElementById('txt_system_id').value+'&company_id='+document.getElementById('cbo_beneficiary_name').value,'Attached PO','')" style="width:210px"/>

        <input type="button" id="image_button" class="image_uploader" style="width:152px; margin-right:120px;" value="CLICK TO ADD FILE" onClick="file_uploader('../../', document.getElementById('txt_system_id').value, '', 'export_lc_amedment', 2, 1)" />

		<form name="amendmentFrm_2" id="amendmentFrm_2" method="POST" action="" >
			<fieldset style="width:1100px; margin:5px">
                <table width="100%" cellspacing="0" cellpadding="0" class="rpt_table" id="tbl_order_list" border="1" rules="all">
                    <thead>
                    	<tr>
                            <th class="must_entry_caption">Order Number</th>
                            <th>Order Qty</th>
                            <th>Order Value</th>
                            <th class="must_entry_caption">Attach. Qty</th>
                            <th>Rate</th>
                            <th>Attach. Val.</th>
                            <th>Style Ref</th>
                            <th>Item</th>
                            <th>Job No.</th>
                             <th>Fabric Description</th>
                            <th>Categroy</th>
                            <th>Hs Code</th>
							<th>Brand</th>
                            <th>Status</th> 
                      	</tr>                         
                    </thead>
                    <tbody>
                        <tr class="general" id="tr_1">
                            <td><input type="text" name="txtordernumber_1" id="txtordernumber_1" class="text_boxes" style="width:100px"  onDblClick= "openmypage('requires/export_lc_amendment_controller.php?action=order_popup&types=order_select_popup&buyer_id='+document.getElementById('cbo_buyer_name').value+'&selectID='+document.getElementById('hidden_selectedID').value+'&export_lcID='+document.getElementById('txt_system_id').value+'&company_id='+document.getElementById('cbo_beneficiary_name').value,'PO Selection Form',1)" readonly= "readonly" placeholder="Double Click" /></td>
                            <td><input type="text" name="txtorderqnty_1" id="txtorderqnty_1" class="text_boxes_numeric" style="width:65px;" readonly= "readonly" /></td>
                            <td><input type="text" name="txtordervalue_1" id="txtordervalue_1" class="text_boxes_numeric" style="width:80px;" readonly= "readonly"/></td>
                            <td><input type="text" name="txtattachedqnty_1" id="txtattachedqnty_1" class="text_boxes_numeric" style="width:65px" onKeyUp="validate_attach_qnty(1)" />
                            	<input type="hidden" name="hideattachedqnty_1" id="hideattachedqnty_1" class="text_boxes_numeric" />
                            </td>
                            <td>
                                <input type="text" name="hiddenunitprice_1" id="hiddenunitprice_1" class="text_boxes_numeric" style="width:50px" onKeyUp="calculate_attach_val(1)" disabled >
                            </td>
                            <td><input type="text" name="txtattachedvalue_1" id="txtattachedvalue_1" class="text_boxes_numeric" style="width:80px" readonly= "readonly"/></td>
                            <td><input type="text" name="txtstyleref_1" id="txtstyleref_1" class="text_boxes" style="width:90px" readonly= "readonly"/></td>
                            <td><input type="text" name="txtitemname_1" id="txtitemname_1" class="text_boxes" style="width:110px" readonly= "readonly"/></td>
                            <td><input type="text" name="txtjobno_1" id="txtjobno_1" class="text_boxes" style="width:80px" readonly= "readonly"/></td>
                            
                                <input type="hidden" name="hiddenwopobreakdownid_1" id="hiddenwopobreakdownid_1" readonly= "readonly" />
                                <input type="hidden" name="order_attached_qnty_1" id="order_attached_qnty_1" readonly= "readonly" />
                                <input type="hidden" name="order_attached_lc_no_1" id="order_attached_lc_no_1" readonly= "readonly" />
                                <input type="hidden" name="order_attached_lc_qty_1" id="order_attached_lc_qty_1" readonly= "readonly" />
                                <input type="hidden" name="order_attached_sc_no_1" id="order_attached_sc_no_1" readonly= "readonly" />
                                <input type="hidden" name="order_attached_sc_qty_1" id="order_attached_sc_qty_1" readonly= "readonly" />
                            
                            <td><input type="text" name="txtfabdescrip_1" id="txtfabdescrip_1" class="text_boxes" style="width:90px" /></td>
                            <td><input type="text" name="txtcategory_1" id="txtcategory_1" class="text_boxes_numeric" style="width:50px" /></td>
                            <td><input type="text" name="txthscode_1" id="txthscode_1" class="text_boxes" style="width:40px"/></td>           
                            <td><input type="text" name="txtbrand_1" id="txtbrand_1" class="text_boxes" style="width:40px" readonly/></td>           
                            <td>                             
                                <? 
                                    echo create_drop_down( "cbopostatus_1", 60, $attach_detach_array,"", 0, "", 1, "" );
                                ?>
                            </td> 
                        </tr>
                    </tbody>
                    <tfoot>
                    	<tr class="tbl_bottom">
                          <td>Total</td>
                          <td><input type="text" name="totalOrderqnty" id="totalOrderqnty" class="text_boxes_numeric" style="width:70px;" readonly= "readonly" /></td>
                          <td><input type="text" name="totalOrdervalue" id="totalOrdervalue" class="text_boxes_numeric" style="width:80px;" readonly= "readonly" /></td>
                          <td><input type="text" name="totalAttachedqnty" id="totalAttachedqnty" class="text_boxes_numeric" style="width:70px;" readonly= "readonly" /></td>
                          <td>&nbsp;</td>
                          <td><input type="text" name="totalAttachedvalue" id="totalAttachedvalue" class="text_boxes_numeric" style="width:80px;" readonly= "readonly" /></td>
                          <td colspan="8">&nbsp;</td>
                        </tr>
                    	<tr>
                        	<td colspan="14" height="50" valign="middle" align="center" class="button_container">
							<? echo load_submit_buttons( $permission, "fnc_po_selection_save", 0,0 ,"reset_form('amendmentFrm_2','','','txt_tot_row,0','$(\'#tbl_order_list tbody tr:not(:first)\').remove();load_po_id(1);','hidden_selectedID')",2) ; ?>
                            <input type="hidden" name="hiddenexportlcorderid" id="hiddenexportlcorderid" readonly= "readonly" /> <!-- for update --> 
                            <input type="hidden" id="hidden_selectedID" readonly= "readonly" />
                            <input type="hidden" name="txt_tot_row" id="txt_tot_row" class="text_boxes_numeric"  readonly= "readonly" value="0" />
                        	</td>
                    	</tr>
                    </tfoot>
                </table>
				<div style="width:100%; margin-top:10px" id="po_list_view" align="left"></div>
			</fieldset>
		</form>
        </div>
        </fieldset>
	</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>