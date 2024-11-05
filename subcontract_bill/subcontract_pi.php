<?
/*-------------------------------------------- Comments

Purpose			: 	This form will create for Export PI entry
					
Functionality	:	
				

JS Functions	:

Created by		:	K.M Nazim Uddin
Creation date 	: 	22-10-2017
Updated by 		: 	
Update date		: 	 

QC Performed BY	:		

QC Date			:	

Comments		:

*/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../includes/common.php');
extract($_REQUEST);
$_SESSION['page_permission']=$permission;
//--------------------------------------------------------------------------------------------------------------------
echo load_html_head_contents("Sub Contract Pro Forma Invoice", "../", 1, 1,'','',''); 
?> 	

<script>

if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../logout.php";  

var permission='<? echo $permission; ?>';

function fnc_pi_mst( operation )
{
	if(operation==4)
	{ 
		 print_report( $('#cbo_exporter_id').val()+'*'+$('#update_id').val(), "print", "requires/subcontract_pi_controller" ) ;
		 return;
	}
	
	if(operation==2)
	{ 
		 show_msg('13');
		 return;
	}
	 
	if (form_validation('cbo_item_category_id*cbo_exporter_id*pi_date*cbo_currency_id','Export Item Category*Exporter*Pi Date*Currency')==false)
	{
		return;
	}
	else
	{ 
		var data="action=save_update_delete&operation="+operation+get_submitted_data_string('cbo_item_category_id*cbo_exporter_id*cbo_within_group*cbo_buyer_name*pi_number*pi_date*last_shipment_date*pi_validity_date*cbo_currency_id*hs_code*txt_internal_file_no*txt_remarks*cbo_advising_bank*update_id',"../");
		  
		freeze_window(operation);
		http.open("POST","requires/subcontract_pi_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_pi_mst_reponse;
	}
}

function fnc_pi_mst_reponse()
{
	if(http.readyState == 4) 
	{
		var response=trim(http.responseText).split('**');
		
		show_msg(trim(response[0]));
		
		if(response[0]==0 || response[0]==1)
		{
			show_msg(trim(response[0]));
			//$("#pi_number").val(reponse[1]); sys_pi_number
			document.getElementById('pi_number').value = response[3];
			document.getElementById('txt_system_id').value = response[2];
			document.getElementById('update_id').value = response[1];
			//$('#sys_pi_number').attr({'disabled': 'disabled'});
			$('#cbo_exporter_id').attr({'disabled': 'disabled'});
			set_button_status(1, permission, 'fnc_pi_mst',1);
		}
		release_freezing();
	}
}

function calculate_amount(i)
{
	var ddd={ dec_type:5, comma:0, currency:''}
	math_operation( 'amount_'+i, 'quantity_'+i+'*rate_'+i, '*','',ddd);
	calculate_total_amount(1);
}

function calculate_total_amount(type)
{
	if(type==1)
	{
		var ddd={ dec_type:5, comma:0, currency:''}
		var numRow = $('table#tbl_pi_item tbody tr').length; 
		math_operation( "txt_total_amount", "amount_", "+", numRow,ddd );
	}
	
	var txt_total_amount=$('#txt_total_amount').val();
	var txt_upcharge=$('#txt_upcharge').val();
	var txt_discount=$('#txt_discount').val();
	
	var net_tot_amnt=txt_total_amount*1+txt_upcharge*1-txt_discount*1;
	$('#txt_total_amount_net').val(net_tot_amnt.toFixed(4));
}

function fnCheckUnCheckAll(checkVal)
{
	for (Looper=0; Looper < document.pimasterform_2.length ; Looper++ )
	{
		var strType = document.pimasterform_2.elements[Looper].type;
		if (strType=="checkbox")
		{
			document.pimasterform_2.elements[Looper].checked=checkVal;
		}   
	}
}

function fnc_pi_item_details( operation )
{
	var update_id = $('#update_id').val();
	var txt_upcharge = $('#txt_upcharge').val();
	var txt_discount = $('#txt_discount').val();
	var cbo_currency_id = $('#cbo_currency_id').val();

	var txt_total_amount=0; var txt_total_amount_net=0;
	
	if(update_id=='')
	{
		alert('Please Save PI First');
		return false;
	}
	
	if(operation==2)
	{
		show_msg('13');
		return false;
	}
	
	var row_num=$('#tbl_pi_item tbody tr').length;
	var data_all=""; var i=0; var selected_row=0;
	
	for (var j=1; j<=row_num; j++)
	{
		var updateIdDtls=$('#updateIdDtls_'+j).val();
		if($('#workOrderChkbox_'+j).is(':checked') || updateIdDtls!="")
		{
			if (form_validation('workOrderNo_'+j+'*quantity_'+j+'*rate_'+j,'WO*Qunatity*Rate')==false)
			{
				return;
			}
			
			i++;
			
			//data_all+="&workOrderNo_" + i + "='" + $('#workOrderNo_'+j).val()+"'"+"&hideWoId_" + i + "='" + $('#hideWoId_'+j).val()+"'"+"&hideWoDtlsId_" + i + "='" + $('#hideWoDtlsId_'+j).val()+"'"+"&construction_" + i + "='" + $('#construction_'+j).val()+"'"+"&composition_" + i + "='" + $('#composition_'+j).val()+"'"+"&colorId_" + i + "='" + $('#colorId_'+j).val()+"'"+"&gsm_" + i + "='" + $('#gsm_'+j).val()+"'"+"&diawidth_" + i + "='" + $('#diawidth_'+j).val()+"'"+"&uom_" + i + "='" + $('#uom_'+j).val()+"'"+"&quantity_" + i + "='" + $('#quantity_'+j).val()+"'"+"&rate_" + i + "='" + $('#rate_'+j).val()+"'"+"&amount_" + i + "='" + $('#amount_'+j).val()+"'"+"&updateIdDtls_" + i + "='" + $('#updateIdDtls_'+j).val()+"'"+"&hideDeterminationId_" + i + "='" + $('#hideDeterminationId_'+j).val()+"'";
				
			txt_total_amount+=$('#amount_'+j).val()*1;
			if($('#workOrderChkbox_'+j).is(':checked')) selected_row++;
			
		}
	}

	if(selected_row<1)
	{
		alert("Please Select WO");
		return;
	}
	//alert(data_all);return;
	
	txt_total_amount_net=txt_total_amount*1+txt_upcharge*1-txt_discount*1;
	
	var data="action=save_update_delete_dtls&operation="+operation+'&total_row='+i+'&update_id='+update_id+'&txt_total_amount='+txt_total_amount+'&txt_upcharge='+txt_upcharge+'&txt_discount='+txt_discount+'&txt_total_amount_net='+txt_total_amount_net+'&cbo_currency_id='+cbo_currency_id+data_all;
	 
	freeze_window(operation);
	
	http.open("POST","requires/subcontract_pi_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fnc_pi_item_details_reponse;
}
		 
function fnc_pi_item_details_reponse()
{
	if(http.readyState == 4) 
	{
		//lert(http.responseText);
		//release_freezing();return;
		var response=http.responseText.split('**'); 
		show_msg(trim(response[0]));
		
		if(response[0]==0 || response[0]==1)
		{	
			show_list_view(response[1], 'pi_details', 'pi_details_container', 'requires/subcontract_pi_controller', '' ) ;
			set_button_status(1, permission, 'fnc_pi_item_details',2);
			$('#check_all').attr('checked',false);
		}
		release_freezing();
	}
}

function openmypage()
{
	var exporter_id 	= $('#cbo_exporter_id').val();
	
	if (form_validation('cbo_exporter_id','Exporter')==false)
	{
		return;
	}
	else
	{ 	
		var title = 'PI Selection Form';	
		var page_link = 'requires/subcontract_pi_controller.php?exporter_id='+exporter_id+'&action=pi_popup';
		  
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=920px,height=450px,center=1,resize=1,scrolling=0','../');
		
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var theemail=this.contentDoc.getElementById("txt_selected_pi_id") //Access form field with id="emailfield"
			
			if(theemail.value!="")
			{
				freeze_window(5);
				get_php_form_data( theemail.value, "populate_data_from_search_popup", "requires/subcontract_pi_controller" );
				show_list_view(theemail.value+'_0_0_'+2, 'pi_details', 'pi_details_container', 'requires/subcontract_pi_controller', '' ) ;
				
				var wo_no=$('#workOrderNo_1').val(); 
				if(wo_no=="")
				{
					set_button_status(0, permission, 'fnc_pi_item_details',2);
				}
				else
				{
					set_button_status(1, permission, 'fnc_pi_item_details',2);
				}
				release_freezing();
			} 
		}
	}
}

function openmypage_wo(row_num)
{
	var exporter_id 	= $('#cbo_exporter_id').val();
	
	if (form_validation('cbo_exporter_id','Exporter')==false)
	{
		return;
	}
	else
	{ 	
		var title = 'Sales/Booking No. Selection Form';	
		var page_link = 'requires/subcontract_pi_controller.php?exporter_id='+exporter_id+'&action=wo_popup';
		  
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=920px,height=450px,center=1,resize=1,scrolling=0','../');
		
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]//("search_order_frm"); //Access the form inside the modal window
			var theemail=this.contentDoc.getElementById("txt_selected_wo_id"); //Access form field with id="emailfield"
			
			if(theemail.value!="")
			{
				//alert(theemail.value);return;
				freeze_window(5);
				var numRow = $('table#tbl_pi_item tbody tr').length; 
				var wo_no=$('#workOrderNo_'+row_num).val(); 
				if(wo_no=="")
				{
					numRow--;
				}
				var data=theemail.value+"**"+numRow;
				var list_view_wo =return_global_ajax_value( data, 'populate_data_wo_form', '', 'requires/subcontract_pi_controller');
				if(wo_no=="")
				{
					$("#row_"+row_num).remove();
				}
				
				$("#tbl_pi_item tbody:last").append(list_view_wo);	
				calculate_total_amount(1);
				release_freezing();
			} 
		}
	}
}

	function openmypage_job()
	{ 		
		var update_id=document.getElementById('update_id').value;
		if(update_id=='')
		{
			alert("Save PI First"); return;
		}
		else
		{
			var data=document.getElementById('cbo_exporter_id').value;
			page_link='requires/subcontract_pi_controller.php?action=job_popup&data='+data;
			title='Subcontrat Order Test';
			
			emailwindow=dhtmlmodal.open('EmailBox','iframe', page_link, title, 'width=790px, height=420px, center=1, resize=0, scrolling=0','')
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0]
				var theemail=this.contentDoc.getElementById("selected_job");
				release_freezing();
				//alert (theemail.value);return;
				if (theemail.value!="")
				{
					freeze_window(5);
					show_list_view(theemail.value+'_'+1, 'pi_details', 'pi_details_container', 'requires/subcontract_pi_controller', '' ) ;
					//set_all_onclick();
					
					// calculate_total_amount(1);
					
					
					set_button_status(0, permission, 'fnc_pi_item_details',1);
					//alert(theemail.value);
					set_button_status(1, permission, 'fnc_pi_mst',1);
					release_freezing();
				}
			}
		}	
	}

</script>

<body onLoad="set_hotkey()">
    <div align="center" style="width:100%;">
        <? echo load_freeze_divs ("../",$permission); ?>
        <div>
			<form name="pimasterform_1" id="pimasterform_1" autocomplete="off"> 
                <fieldset style="width:1050px;">
                    <legend>PI Details</legend>
                    <table width="100%" border="0" cellpadding="0" cellspacing="2">
                    	<tr>
                        	<td colspan="6" height="25" valign="middle" align="center" style="border-bottom:0px solid #666"><strong>&nbsp;&nbsp;&nbsp;&nbsp;System ID</strong><input type="text" name="txt_system_id"  style="width:140px"  id="txt_system_id" class="text_boxes" placeholder="Display" readonly value="" /></td>
                        </tr>
                        <tr>
                            <td width="140" align="right" class="must_entry_caption">Export Item Category</td>
                            <td width="200"> 
                                 <?php echo create_drop_down( "cbo_item_category_id", 151, $export_item_category,'', 1, ' --Select-- ',0,"",0,'20,22,23,24,30,31,35,36,37'); ?>  
                            </td>
                            <td width="140" align="right" class="must_entry_caption">Exporter</td>
                            <td width="200">
                                 <?php echo create_drop_down( "cbo_exporter_id", 151,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name",'id,company_name', 1, 'Select',0,"load_drop_down( 'requires/subcontract_pi_controller',this.value+'_'+document.getElementById('cbo_within_group').value, 'load_drop_down_buyer', 'buyer_td' );",0); ?>       
                            </td>
                            <td width="140" class="must_entry_caption" align="right">Within Group</td>                                              
                            <td>
                                <?php echo create_drop_down( "cbo_within_group", 151, $yes_no,"", 0, "--  --", 0, "load_drop_down( 'requires/subcontract_pi_controller',document.getElementById('cbo_exporter_id').value+'_'+this.value, 'load_drop_down_buyer', 'buyer_td' );" ); ?>
                            </td>
                        </tr>
                        <tr>
                        	<td align="right">Buyer</td>
                            <td id="buyer_td"> 
                                <?php echo create_drop_down( "cbo_buyer_name", 151, $blank_array,"", 1, "-- Select Buyer --", 0, "",1 ); ?>
                            </td>
                            <td align="right" >PI No</td>
                            <td><input type="text" name="pi_number" id="pi_number" class="text_boxes" style="width:140px" placeholder="Double click for PI" onDblClick="openmypage()" maxlength="30" /></td>
                            <td align="right" class="must_entry_caption">PI Date</td>
                            <td><input type="text" name="pi_date" id="pi_date" class="datepicker"  style="width:140px" /></td>
                        </tr>
                        <tr>
                        	<td align="right">Last Shipment Date</td>
                            <td><input type="text" name="last_shipment_date"  style="width:140px"  id="last_shipment_date" class="datepicker" value="" /></td>
                            <td align="right">PI Validity Date</td>
                            <td><input type="text" name="pi_validity_date" id="pi_validity_date"  style="width:140px"  class="datepicker" value="" /></td>
                            <td align="right" class="must_entry_caption">Currency</td>
                            <td>
                                <?php echo create_drop_down( "cbo_currency_id", 151,$currency,'',0,'',2,0,0); ?>       
                            </td>
                        </tr>
                        <tr>
                            <td align="right">HS Code</td>
                            <td><input type="text" name="hs_code" id="hs_code" class="text_boxes"  style="width:140px"  value=""  maxlength="30" /></td>
                            <td align="right">Internal File No</td>
                            <td><input type="text" name="txt_internal_file_no" id="txt_internal_file_no"  style="width:140px"  class="text_boxes_numeric"  maxlength="50" /></td>
                            <td align="right">Advising Bank</td>
							<td>
								<? $sql="select id, (bank_name||' ( '|| branch_name||' )') as bank_name from lib_bank where advising_bank=1";
								echo create_drop_down( "cbo_advising_bank", 151, $sql,'id,bank_name', 0, '',1,0); ?> 
							</td>
                            
                        </tr>
                        <tr>
                         	<td align="right">Remarks</td>
                            <td >
                             	 <input type="text" name="txt_remarks" id="txt_remarks"  style="width:140px;" class="text_boxes" />
                            </td>
                            <td align="right">Terms and Condition</td>
							<!--<td>
								<input type="button" id="set_button" class="image_uploader" style="width:151px;" value="Terms Condition" onClick="open_terms_condition_popup('requires/subcontract_pi_controller.php?action=terms_condition_popup','Terms Condition')" />
							</td>-->
                            <td>
                            <? 
                            include("../terms_condition/terms_condition.php");
                            terms_condition(174,'update_id','../');
                            ?>
                            </td>
                            <td align="right">File</td>
                            <td>
                                 <input type="button" id="image_button" class="image_uploader" style="width:151px" value="CLICK TO ADD FILE" onClick="file_uploader( '../', document.getElementById('update_id').value,'', 'export_pro_forma_invoice',2,1)" />
                        	</td>
                        </tr>
                        <tr>
                            <td colspan="6" height="50" valign="middle" align="center" class="button_container">
                                <input type="hidden" name="update_id" id="update_id" value="" readonly/>
                                <? 
							   		echo load_submit_buttons( $permission, "fnc_pi_mst", 0,1 ,"reset_form('pimasterform_1','','','cbo_currency_id,2','$(\'#tbl_pi_item tbody tr:not(:first)\').remove();')",1);
							    ?>
                             </td>                          			
                        </tr>                        
                    </table>
                </fieldset>
			</form>
            <form name="pimasterform_2" id="pimasterform_2" autocomplete="off">
                <fieldset style="width:1050px; margin-top:10px;">
                    <legend>PI Item Details</legend>
                    <div>
                    	<table class="rpt_table" width="100%" cellspacing="0" cellpadding="0" border="1" rules="all" id="tbl_pi_item">
                        	<thead>
                            	<th width="40">&nbsp;</th>
								<th>Order No</th>
                                <th>Process</th>
                                <th>Embellishment Type</th>
                                <th>Embelishment Description</th>					
                                <th>Item Description</th>
                                <th>Color</th>
                                <th>Sub Process</th>
                                <th>Customer Referance</th> 
                                <th>Order Qty</th>                                                             
                                <th>Rate</th>
                                <th>Amount</th>
                            </thead>
                            <tbody id="pi_details_container">
                            	<tr bgcolor="#E9F3FF" id="row_1" align="center">
                                	<td>
                                        <input type="checkbox" name="workOrderChkbox_1" id="workOrderChkbox_1" value="" />
                                    </td>
                                    <td><input name="txtOrderNo_1" id="txtOrderNo_1" type="text" class="text_boxes" value=""  style="width:75px" placeholder="Double Click" onDblClick="openmypage_job()"/></td>
                        			<td><? echo create_drop_down( "cboProcessName_1", 80, $production_process,"", 1, "--Select Process--",0,"", "","" ); ?></td>                       			
                                    <td class="emb_type_show">
									<?
										$type_array=array(0=>$blank_array,8=>$emblishment_print_type,9=>$emblishment_embroy_type,7=>$emblishment_wash_type,12=>$emblishment_gmts_type);

										if($type_array[$process_id]=="")
										{
											$dropdown_type_array=$blank_array;
										}
										else
										{
											$dropdown_type_array=$type_array[$process_id];
										}

										echo create_drop_down( "cboembtype_1", 170, $dropdown_type_array,"", 1, "-- Select --", "", "","","" );
									?>
									</td>

                                    <td class="descriptions">
                                        <input type="text" id="txtdescription_1" name="txtdescription_1" class="text_boxes descriptions" style="width:140px" value="" />
                                    </td>
    
                                    <td>
                                        <input type="text" id="txtitem_1" name="txtitem_1" class="text_boxes itemdescription" style="width:140px"  value="" readonly /> 
                                    </td>                                    
                                    <td>
                                        <input type="text" name="colorName_1" id="colorName_1" class="text_boxes" value="" style="width:80px" />
                                        <input type="hidden" name="colorId_1" id="colorId_1"/>
                                    </td>                                    
                                    <td><input type="text" name="txSubProcessName_1" id="txSubProcessName_1" class="text_boxes" style="width:140px;" readonly />
                            			<input type="hidden" name="txtSubProcessId_1" id="txtSubProcessId_1" /></td>
                                    <td>
                                        <input type="text" name="txtCustBuyStle_1" id="txtCustBuyStle_1" class="text_boxes" style="width:140px;" readonly value="" />				 
                                    </td>
                                    <td>
                                        <input type="text" name="quantity_1" id="quantity_1" class="text_boxes_numeric" value="" style="width:61px;" onKeyUp="calculate_amount(1)"/>
                                    </td>
                                    <td>
                                        <input type="text" name="rate_1" id="rate_1" class="text_boxes_numeric" value="" style="width:60px;" onKeyUp="calculate_amount(1)" />
                                    </td>
                                    <td>
                                        <input type="text" name="amount_1" id="amount_1" class="text_boxes_numeric" value="" style="width:75px;" readonly/>
                                        <input type="hidden" name="updateIdDtls_1" id="updateIdDtls_1" readonly/>
                                    </td>	
                                </tr>
                            </tbody>
                            <tfoot class="tbl_bottom">
                                <tr>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>Total</td>
                                    <td style="text-align:center">
                                        <input type="text" name="txt_total_amount" id="txt_total_amount" class="text_boxes_numeric" value="" style="width:75px;" readonly/>
                                    </td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>Upcharge</td>
                                    <td style="text-align:center">
                                        <input type="text" name="txt_upcharge" id="txt_upcharge" class="text_boxes_numeric" value="" style="width:75px;" onKeyUp="calculate_total_amount(2)"/>
                                    </td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>Discount</td>
                                    <td style="text-align:center">
                                        <input type="text" name="txt_discount" id="txt_discount" class="text_boxes_numeric" value="" style="width:75px;" onKeyUp="calculate_total_amount(2)"/>
                                    </td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>Net Total</td>
                                    <td style="text-align:center">
                                        <input type="text" name="txt_total_amount_net" id="txt_total_amount_net" class="text_boxes_numeric" value="" style="width:75px;" readonly/>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                        <table style="margin-top:5px" width="100%">
                       		<tr>
                            	<td valign="top" width="15%"><input form="form_all" type="checkbox" name="check_all" id="check_all" value=""  onclick="fnCheckUnCheckAll(this.checked)"/> Check / Uncheck All</td>
                            	<td valign="top" width="85%" colspan="9" align="center" class="button_container">
                                	<? echo load_submit_buttons( $_SESSION['page_permission'], "fnc_pi_item_details", 0,0 ,"reset_form('pimasterform_2','','','','$(\'#tbl_pi_item tbody tr:not(:first)\').remove();')",2) ; ?>
                                </td>
                            </tr>
                        </table>	
                    </div>
                </fieldset>
            </form>
        </div>
    </div>
</body>
<script src="../includes/functions_bottom.js" type="text/javascript"></script>
</html>