<?
/*-------------------------------------------- Comments

Purpose			: 	This form will create for Export Information entry
					
Functionality	:	
				

JS Functions	:

Created by		:	Fuad Shahriar 
Creation date 	: 	01-06-2013	 
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
echo load_html_head_contents("Export Information Entry Form", "../../", 1, 1,'','1','');
?>	
 
<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";  
var permission='<? echo $permission; ?>';

function openmypage_LcSc()
{
	var buyerID = $("#cbo_buyer_name").val();
	var invoice_id = $("#update_id").val();
	
	/*if (form_validation('cbo_buyer_name','Buyer')==false )
	{
		return;
	}*/
	
	var page_link='requires/export_information_entry_controller.php?action=lcSc_popup_search&buyerID='+buyerID;
	var title='Export Information Entry Form';
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=750px,height=400px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var lcSc_id=this.contentDoc.getElementById("hidden_lcSc_id").value;
		var is_lcSc=this.contentDoc.getElementById("is_lcSc").value;
		var company_id=this.contentDoc.getElementById("company_id").value;
		
		//alert(company_id);return;
		//alert(lcSc_id+"**"+is_lcSc);
		if(trim(lcSc_id)!="")
		{
			freeze_window(5);
			if(invoice_id=="")
			{
				$('#chk_color_size_rate').removeAttr('checked','checked');
			}
			
			get_php_form_data(lcSc_id+"**"+is_lcSc+"**"+'0'+"**"+company_id, "populate_data_from_lcSc", "requires/export_information_entry_controller" );//+"**"+invoice_id
			release_freezing();
		}
					 
	}
}

function reset_page()
{
	reset_form('exportInformationFrm_1*exportInformationFrm_2','','','','','lc_sc_id*is_lc_sc*cbo_beneficiary_name*cbo_buyer_name*cbo_applicant_name*cbo_lien_bank');
	var lcSc_id=$("#lc_sc_id").val();
	var is_lcSc=$("#is_lc_sc").val();
	var company_id=$("#cbo_beneficiary_name").val();
	if(trim(lcSc_id)!="")
	{
		$('#chk_color_size_rate').removeAttr('checked','checked');
		get_php_form_data(lcSc_id+"**"+is_lcSc+"**"+'0'+"**"+company_id, "populate_data_from_lcSc", "requires/export_information_entry_controller" );
	}
}

function openmypage_Invoice()
{
	var buyerID = $("#cbo_buyer_name").val();
	
	/*if (form_validation('cbo_buyer_name','Buyer')==false )
	{
		return;
	}*/
	
	var page_link='requires/export_information_entry_controller.php?action=invoice_popup_search&buyerID='+buyerID;
	var title='Export Information Entry Form';
	
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=750px,height=400px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var invoice_id=this.contentDoc.getElementById("hidden_invoice_id").value;
		var company_id=this.contentDoc.getElementById("company_id").value;
		
		//alert(lcSc_id+"**"+is_lcSc);return;
		//alert(invoice_id);return;
		
		if(trim(invoice_id)!="")
		{
			freeze_window(5);
			get_php_form_data(invoice_id, "populate_data_from_invoice", "requires/export_information_entry_controller" );
			
			var lcSc_id=$("#lc_sc_id").val();
			var is_lcSc=$("#is_lc_sc").val();
			
			get_php_form_data(lcSc_id+"**"+is_lcSc+"**"+invoice_id+"**"+company_id, "populate_data_from_lcSc", "requires/export_information_entry_controller" );
			release_freezing();
		}
					 
	}
}

function fnc_export_information_entry(operation) 
{ 
	if(operation==2)
	{
		show_msg('13');
		return;
	}
		
	if ( form_validation('cbo_buyer_name*txt_invoice_no*txt_lc_sc_no*txt_invoice_date*txt_invoice_val','Buyer*Invoice No*LC/SC No*Invoice Date*Invoice Value')==false )
	{
		return;
	}
	else
	{
		var tot_row=$('#tot_row').val();
		var total_tolerence_order_qty=$('#total_tolerence_order_qty').val();
		var row_num=$('#tbl_order_list tbody tr').length-1;
		var data_row=0;
		var submit_data="";
		var total_cum_qty=0;
			
		if(tot_row!=0)
		{
			for(var j=1;j<=tot_row;j++)  
			{
				total_cum_qty=(total_cum_qty*1)+($('#cum_invo_qty_'+j).val()*1);
				
				var curr_invo_qty=$('#curr_invo_qty_'+j).val();
				if(curr_invo_qty*1>0)
				{
					data_row++;
					submit_data += "*order_id_"+j+"*order_no_"+j+"*order_qty_"+j+"*order_rate_"+j+"*curr_invo_qty_"+j+"*curr_invo_val_"+j+"*cbo_production_source_"+j+"*actual_po_infos_"+j+"*colorSize_infos_"+j;
				}
			}
			
			if(total_tolerence_order_qty*1<total_cum_qty*1) 
			{
				alert('Cumulitive Qnty execeded tolerance Attached Order Qnty');
				$('#total_current_invoice_qty').focus();
				$('#total_current_invoice_qty').css("background","#FF7373");
				return ;
			}
		}
		else
		{
			submit_data= "";
		}
		
		if($('#chk_color_size_rate').is(':checked')) var color_size_rate=1; else var color_size_rate=0;
		
		if(data_row<1 && tot_row!=0)
		{
			alert("No Invoice Quantity Insert");	
			return;
		}
		//alert(submit_data);return;
		
		var data="action=save_update_delete_mst&operation="+operation+'&tot_row='+tot_row+'&color_size_rate='+color_size_rate+get_submitted_data_string('cbo_buyer_name*txt_invoice_no*lc_sc_id*is_lc_sc*txt_invoice_date*txt_exp_form_no*txt_exp_form_date*cbo_lien_bank*cbo_beneficiary_name*cbo_location*txt_remarks*txt_invoice_val*txt_discount*txt_discount_ammount*txt_bonus*txt_bonus_ammount*txt_claim*txt_claim_ammount*txt_invo_qnty*txt_commission*txt_net_invo_val*cbo_country*update_id*additional_info'+submit_data,"../../");
		
		freeze_window(operation);
		
		http.open("POST","requires/export_information_entry_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange =fnc_export_information_entry_Reply_info;
	}	

}

function fnc_export_information_entry_Reply_info()
{
	
	if(http.readyState == 4) 
	{
		// alert(http.responseText);
		var reponse=trim(http.responseText).split('**');	
			
		if(reponse[0]!=101){show_msg(trim(reponse[0]));}
		
		if((reponse[0]==0 || reponse[0]==1))
		{
			document.getElementById('update_id').value = reponse[1];
			var lcSc_id=$("#lc_sc_id").val();
			var is_lcSc=$("#is_lc_sc").val();
			set_button_status(1, permission, 'fnc_export_information_entry',1);	
			//get_php_form_data(lcSc_id+"**"+is_lcSc+"**"+reponse[1], "populate_data_from_lcSc", "requires/export_information_entry_controller" );
		}
		else if(reponse[0]==14)
		{
			alert(reponse[1]+' Found. Update Not Allowed.');
		}
		else if(reponse[0]==101)
		{
			alert('Invoice No: '+reponse[1]+' is Tagged in Garments Delivery Entry.');
		}
		
		
		release_freezing();	
	}
}

//attached sales contract here 
function fnc_export_information_entry_shipping_info(operation)
{
	if(operation==2)
	{
		show_msg('13');
		return;
	}
	
	var update_id = $('#update_id').val();
	if(update_id=='')
	{
		alert('Please Save Invoice First');
		return false;
	}
		 
	var data="action=save_update_delete_dtls&operation="+operation+get_submitted_data_string('update_id*bl_no*bl_date*bl_rev_date*doc_handover*forwarder_name*etd*feeder_vessel*mother_vessel*etd_destination*txt_eta_destination*ic_recieved_date*inco_term*inco_term_place*shipping_bill_no*shipping_mode*total_carton_qnty*port_of_entry*port_of_loading*port_of_discharge*actual_shipment_date*ex_factory_date*freight_amnt_supplier*freight_amnt_buyer*txt_category_no*txt_hs_code*ship_bl_date*txt_advice_date*txt_advice_amnt*txt_paid_amnt*txt_gsp_co*txt_gsp_co_date*cbo_incentive*txt_cons*txt_co_no*txt_co_date',"../../");
	//alert(data);return;
	freeze_window(operation);
	
	http.open("POST","requires/export_information_entry_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = fnc_export_information_entry_shipping_Reply_info;
		

}

function fnc_export_information_entry_shipping_Reply_info()
{
	if(http.readyState == 4) 
	{
		//alert(http.responseText);
		var reponse=http.responseText.split('**');		 
		show_msg(trim(reponse[0]));	
		
		if((reponse[0]==0 || reponse[0]==1))
		{
			set_button_status(1, permission, 'fnc_export_information_entry_shipping_info',2);	
		}
		release_freezing(); 
				
	}
}

 // function :: calculate rate, discount, amount         
function calculate_value_rate(i)
{
	var curr_invo_qty = $('#curr_invo_qty_'+i).val()*1;
	var unit_price=$('#order_rate_'+i).val()*1;
	var order_qty=$('#order_qty_'+i).val()*1;
	
	var curr_invo_val=curr_invo_qty*unit_price;
	
	var cum_invo_qty=$('#hide_cum_invo_qty_'+i).val()*1+curr_invo_qty-$('#curr_hide_invo_qty_'+i).val()*1;
	var cum_invo_val=$('#hide_cum_invo_val_'+i).val()*1+curr_invo_val-$('#curr_hide_invo_val_'+i).val()*1;
	
	var po_balance_qnty=order_qty-cum_invo_qty;
	
	$('#curr_invo_val_'+i).val(curr_invo_val.toFixed(2));
	$('#cum_invo_qty_'+i).val(cum_invo_qty);
	$('#cum_invo_val_'+i).val(cum_invo_val.toFixed(2));
	$('#po_bl_qty_'+i).val(po_balance_qnty);
	
	var numRow = $('table#tbl_order_list tbody tr').length-2; 
	
	var ddd={ dec_type:2, comma:0, currency:''}
	math_operation( "total_current_invoice_qty", "curr_invo_qty_", "+", numRow );
	math_operation( "total_current_invoice_val", "curr_invo_val_", "+", numRow, ddd );
	
	var total_invoice_val=$('#total_current_invoice_val').val();
	var total_invoice_qnty=$('#total_current_invoice_qty').val();
	
	$('#txt_invoice_val').val(total_invoice_val);
	$('#txt_invo_qnty').val(total_invoice_qnty); 
	
	var txt_discount=$('#txt_discount').val();
	tot_discount = (total_invoice_val*txt_discount)/100;
	tot_discount=tot_discount.toFixed(2);
	$('#txt_discount_ammount').val(tot_discount);
	
	var txt_bonus=$('#txt_bonus').val();
	tot_bonus = (total_invoice_val*txt_bonus)/100;
	tot_bonus=tot_bonus.toFixed(2);
	$('#txt_bonus_ammount').val(tot_bonus);
	
	var txt_claim=$('#txt_claim').val();
	tot_claim = (total_invoice_val*txt_claim)/100;
	tot_claim=tot_claim.toFixed(2);
	$('#txt_claim_ammount').val(tot_claim);
	
	var txt_commission=$('#txt_commission').val();
	tot_commission = (total_invoice_val*txt_commission)/100;
	tot_commission=tot_commission.toFixed(2);
	$('#txt_commission_amt').val(tot_commission);
	
	var otehr_discount=$('#txt_other_discount').val();
	otehr_discount_amt = (total_invoice_val*otehr_discount)/100;
	otehr_discount_amt=otehr_discount_amt.toFixed(2);
	$('#txt_other_discount_amt').val(otehr_discount_amt);
	
	var discount   =  $('#txt_discount_ammount').val();
	var bonus      =  $('#txt_bonus_ammount').val();
	var claim      =  $('#txt_claim_ammount').val();
	var commission  =  $('#txt_commission_amt').val();
	var oterr_discount  =  $('#txt_other_discount_amt').val();
	var upcharge  =  $('#txt_upcharge').val();
	
	
	var total_discount  = (((discount*1)+(bonus*1)+(claim*1)+(commission*1)+(oterr_discount*1))-(upcharge*1));
	var net_invo_val    = (total_invoice_val*1)-(total_discount*1);
	net_invo_val=net_invo_val.toFixed(2);
	$('#txt_net_invo_val').val(net_invo_val);
}

//function for calculate the discounts and bonus value and set it to specified field
function set_discount(field_id)
{
	var total_invoice_val= $('#txt_invoice_val').val();
	
	if(total_invoice_val=='')
	{
		alert('Please Enter Invoice Value');
		$('#'+field_id).val('');
		return false;
	}
	else
	{
		if(field_id=='txt_discount')
		{
			var field_val=$('#txt_discount').val();
			tot_discount = (total_invoice_val*field_val)/100;
			tot_discount=tot_discount.toFixed(2);
			$('#txt_discount_ammount').val(tot_discount);
		}
		else if(field_id=='txt_discount_ammount')
		{
			var field_val=$('#txt_discount_ammount').val();                     
			discount = (field_val*100)/(total_invoice_val*1);
			discount=discount.toFixed(2);
			$('#txt_discount').val(discount);   
		}
		else if(field_id=='txt_bonus')
		{
			var field_val=$('#txt_bonus').val();
			tot_bonus = (total_invoice_val*field_val)/100;
			tot_bonus=tot_bonus.toFixed(2);
			$('#txt_bonus_ammount').val(tot_bonus);
		}
		else if(field_id=='txt_bonus_ammount')
		{
			var field_val=$('#txt_bonus_ammount').val();                     
			bonus = (field_val*100)/(total_invoice_val*1);
			bonus=bonus.toFixed(2);
			$('#txt_bonus').val(bonus);   
		}
		else if(field_id=='txt_claim')
		{
			var field_val=$('#txt_claim').val();
			tot_claim = (total_invoice_val*field_val)/100;
			tot_claim=tot_claim.toFixed(2);
			$('#txt_claim_ammount').val(tot_claim);
		}
		else if(field_id=='txt_claim_ammount')
		{
			var field_val=$('#txt_claim_ammount').val();                     
			claim = (field_val*100)/(total_invoice_val*1);
			claim=claim.toFixed(2);
			$('#txt_claim').val(claim);   
		}
		
		else if(field_id=='txt_commission')
		{
			var field_val=$('#txt_commission').val();
			tot_commission = (total_invoice_val*field_val)/100;
			tot_commission=tot_commission.toFixed(2);
			$('#txt_commission_amt').val(tot_commission);
		}
		else if(field_id=='txt_commission_amt')
		{
			var field_val=$('#txt_commission_amt').val();                     
			commission = (field_val*100)/(total_invoice_val*1);
			commission=commission.toFixed(2);
			$('#txt_commission').val(commission);   
		}
		
		else if(field_id=='txt_other_discount')
		{
			var field_val=$('#txt_other_discount').val();
			other_commission = (total_invoice_val*field_val)/100;
			other_commission=other_commission.toFixed(2);
			$('#txt_other_discount_amt').val(other_commission);
		}
		else if(field_id=='txt_other_discount_amt')
		{
			var field_val=$('#txt_other_discount_amt').val();                     
			other_commission_percent = (field_val*100)/(total_invoice_val*1);
			other_commission_percent=other_commission_percent.toFixed(2);
			$('#txt_other_discount').val(other_commission_percent);   
		}
		
		var discount   =  $('#txt_discount_ammount').val();
		var bonus      =  $('#txt_bonus_ammount').val();
		var claim      =  $('#txt_claim_ammount').val();
		var commission  =  $('#txt_commission_amt').val();
		var other_commission  =  $('#txt_other_discount_amt').val();
		var upcharge  =  $('#txt_upcharge').val();
		var total_discount  = (((discount*1)+(bonus*1)+(claim*1)+(commission*1)+(other_commission*1))-(upcharge*1));
		var net_invo_val    = (total_invoice_val*1)-(total_discount*1);
		net_invo_val=net_invo_val.toFixed(2);
		$('#txt_net_invo_val').val(net_invo_val);
	}     
}

function pop_entry_actual_po(row_id)
{
	var actual_po_infos = $('#actual_po_infos_'+row_id).val();
	var order_id = $('#order_id_'+row_id).val();
	var page_link='requires/export_information_entry_controller.php?action=actual_po_info_popup&actual_po_infos='+actual_po_infos+'&order_id='+order_id;
	var title='Actual Po Entry Info';
	
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=300px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0]
		var actual_infos=this.contentDoc.getElementById("actual_po_infos").value;  
		$('#actual_po_infos_'+row_id).val(actual_infos);            
	}
}

function openpage_colorSize(row_id)
{
	var country_id = $('#cbo_country').val();
	
	if (form_validation('cbo_country','Country')==false )
	{
		return;
	}
	
	if($('#chk_color_size_rate').is(':checked'))
	{
		var order_id = $('#order_id_'+row_id).val();
		var colorSize_infos = $('#colorSize_infos_'+row_id).val();
		var page_link='requires/export_information_entry_controller.php?action=colorSize_infos_popup&colorSize_infos='+colorSize_infos+'&order_id='+order_id+'&country_id='+country_id;
		var title='Color & Size Rate Entry Info';
		
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=900px,height=400px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]
			var colorSize_infos=this.contentDoc.getElementById("colorSize_infos").value; 
			var totInvcQnty=this.contentDoc.getElementById("totInvcQnty").value;  
			var InvcAvgRate=this.contentDoc.getElementById("InvcAvgRate").value; 
			var totInvcAmount=this.contentDoc.getElementById("totInvcAmount").value;
			  
			$('#colorSize_infos_'+row_id).val(colorSize_infos); 
			$('#curr_invo_qty_'+row_id).val(totInvcQnty); 
			$('#order_rate_'+row_id).val(InvcAvgRate); 
			$('#curr_invo_val_'+row_id).val(totInvcAmount);   
			
			calculate_value_rate(row_id);         
		}
	}
}

function active_inactive()
{
	var row_num=$('#tbl_order_list tbody tr').length-2;
	var tot_row=$('#tot_row').val();
	
	if($('#chk_color_size_rate').is(':checked'))
	{
		for(var j=1;j<=tot_row;j++) 
		{
			$('#order_rate_'+j).attr('disabled','disabled');
			$('#curr_invo_qty_'+j).attr('readonly','readonly');
			$('#curr_invo_qty_'+j).attr('placeholder','Double Click');
		}
	}
	else
	{
		for(var j=1;j<=tot_row;j++) 
		{
			$('#order_rate_'+j).removeAttr('disabled','disabled');
			$('#curr_invo_qty_'+j).removeAttr('readonly','readonly');
			$('#curr_invo_qty_'+j).removeAttr('placeholder');
		}
	}
}

function reset_table()
{
	$('#tbl_order_list tbody tr').remove();
	$('#order_details').html("<tr class='general'><td colspan='13'><strong>Please select a LC/SC no to view order details</strong></td></tr>");
}


function openmypage()
{
	var title = 'Invoice Additional Info';
	var additional_info = document.getElementById("additional_info").value;
	//var data='additional_info='+additional_info;
	
	var page_link = 'requires/export_information_entry_controller.php?data='+additional_info+'&action=additional_info_popup';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=720px,height=160px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		
		var additional_infos=this.contentDoc.getElementById("additional_infos").value;
		
		document.getElementById("additional_info").value=additional_infos;
	}
}

//-------------------------------------------order attached js end here --------------------------------//


function fn_print_report()
{
	/*var update_id = $('#update_id').val();
	if(update_id=='')
	{
		alert('Please Save Invoice First');
		return false;
	}*/
	if (form_validation('update_id','Save Data First')==false)
	{
		alert("Save Save Invoice First");
		return;
	}
	else
	{
		 print_report( $('#update_id').val(), "print_invoice", "requires/export_information_entry_controller" ) ;
	}
} 



</script>

</head>
 
<body onLoad="set_hotkey();"> <!--onLoad="set_hotkey();"-->
	<div style="width:100%;" align="center">																	
     	<? echo load_freeze_divs ("../../",$permission); ?>
        <fieldset style="width:1220px; margin-bottom:10px;">
            <legend>Export Information Entry</legend>
            <form name="exportInformationFrm_1" id="exportInformationFrm_1" autocomplete="off" method="POST"  >
                <table cellpadding="0" cellspacing="1" width="1100">
                  	<tr>
                    	<td class="must_entry_caption">LC/SC No</td>
                        <td>
                        	<input type="text" name="txt_lc_sc_no" id="txt_lc_sc_no" style="width:153px" class="text_boxes" placeholder="Double Click to Search" onDblClick="openmypage_LcSc()" readonly />
                            <input type="hidden" name="lc_sc_id" id="lc_sc_id" readonly/>
                            <input type="hidden" name="is_lc_sc" id="is_lc_sc" readonly />
                        </td>
                        <td class="must_entry_caption">Invoice No</td>
                    	<td>
                        	<input type="text" name="txt_invoice_no" id="txt_invoice_no" style="width:153px" class="text_boxes" placeholder="Double Click to Search" onDblClick="openmypage_Invoice()" />
                        </td>
                        <td class="must_entry_caption">Buyer</td>
                        <td>
                        	<?
								echo create_drop_down( "cbo_buyer_name", 165, "select buy.id, buy.buyer_name from lib_buyer buy where buy.status_active =1 and buy.is_deleted=0 $buyer_cond order by buy.buyer_name","id,buyer_name", 1, " Display ", 0, "",1 );
							?>                          
                        </td>
                    </tr>
                    <tr>
                        <td class="must_entry_caption">Invoice Date</td>
                        <td><input name="txt_invoice_date" style="width:153px" id="txt_invoice_date" class="datepicker"  readonly></td>
                        
                        <td>Exp form No</td>
                        <td>
                        	<input type="text"  name="txt_exp_form_no" style="width:153px" id="txt_exp_form_no" class="text_boxes">
                         </td>
                        <td>Exp Form Date</td>
                        <td><input type="text" name="txt_exp_form_date" style="width:153px" id="txt_exp_form_date" class="datepicker" readonly ></td>
                    </tr>
                    <tr>
                        <td>Applicant</td>
                        <td>
                        	<?
								echo create_drop_down( "cbo_applicant_name", 165, "select buy.id, buy.buyer_name from lib_buyer buy where buy.status_active =1 and buy.is_deleted=0 $buyer_cond order by buy.buyer_name","id,buyer_name", 1, " Display ", 0, "",1 );
							?>
                        </td>
                        <td>Lien Bank</td>
                        <td>
							<?
							   	echo create_drop_down( "cbo_lien_bank", 165, "select bank_name,id from lib_bank where is_deleted=0 and status_active=1 and lien_bank=1 order by bank_name","id,bank_name", 1, " Display ", 0, "",1 );
							?>
                        </td>
                        <td>Beneficiary</td>
                       	<td> 
                        	<?
							  echo create_drop_down( "cbo_beneficiary_name", 165, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business in(1,3) $company_cond order by comp.company_name","id,company_name", 1, " Display ", 0, "",1 );
 							?>
                        </td>
                    </tr>
                    <tr>
                        <td>Location</td>
                        <td> 
                            <?
							   	echo create_drop_down( "cbo_location", 165, "select id,location_name from lib_location where status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", 0, "",0 );
							?>
                        </td>
                        <td>Country</td>
                        <td> 
                            <?
							   	echo create_drop_down( "cbo_country", 165, "select id,country_name from  lib_country where status_active =1 and is_deleted=0 order by country_name","id,country_name", 1, "-- Select Country --", 0, "",0 );
							?>
                        </td>
                        <td colspan="2"><input type="button" id="image_button" class="image_uploader" style="width:160px" value="CLICK TO ADD FILE" onClick="file_uploader( '../../', document.getElementById('update_id').value,'', 'export_invoice',2,1)" /></td>
                    </tr>
                    <tr>    
                        <td>Remarks</td>
                        <td colspan="3"><input name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:560px"></td>
                        <td>Color & Size Rate</td>
                        <td> 
                            <input type="checkbox" name="chk_color_size_rate" id="chk_color_size_rate" onClick="active_inactive();">
                        </td>
                    </tr>
                </table>
                <br/>
                <table width="1220" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_order_list_top">
                    <thead>
                    	<tr>
                        	<th colspan="13" >Order Details</th>
                        </tr>
                    	<tr>
                            <th width="115">Order No</th>
                            <th width="95">Article No</th>
                            <th width="85">Shipment Date</th>
                            <th width="80">Attached Order Qnty</th>
                            <th width="70">Rate</th>
                            <th width="95">Current Invoice Qnty</th>
                            <th width="95">Current Invoice Value</th>
                            <th width="90">Cumu Invoice Qnty</th>
                            <th width="85">PO Balance Qnty</th>
                            <th width="95">Cumu Invoice Value</th>
                            <th width="80">Ex-Factory Qnty</th>
                            <th width="105">Merchandiser</th>
                            <th>Production Source</th> 
                      	</tr>                         
                    </thead>
                </table>  
                <div style="width:1220px; max-height:300px; overflow-y:scroll" id="scroll_body">
                    <table width="1202" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_order_list">                   
                        <tbody id="order_details">
                            <tr class="general">
                                <td colspan="13"><strong>Please select a LC/SC no to view order details</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <br>
                 <table cellpadding="0" cellspacing="1" width="1100">
                 	<tr>
                    	<td class="must_entry_caption">
                            Invoice Value
                        </td>
                        <td>
                            <input type="text" name="txt_invoice_val" id="txt_invoice_val" class="text_boxes_numeric" style="width:150px;" disabled="disabled" onKeyUp="set_discount(this.id)"/>
                        </td>
                        <td>
                            Additional Info.
                        </td>
                        <td>
                            <input name="additional_info" placeholder="Double Click to Search" id="additional_info" onDblClick="openmypage(); return false"  class="text_boxes" style="width:150px" autocomplete="off" readonly>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Discount %
                        </td>
                        <td>
                            <input type="text" name="txt_discount" id="txt_discount" class="text_boxes_numeric" style="width:150px;" onKeyUp="set_discount(this.id)"/>
                        </td>
                        <td>
                            Discount Amount
                        </td>
                        <td>
                            <input type="text" name="txt_discount_ammount" id="txt_discount_ammount" class="text_boxes_numeric" style="width:150px" onKeyUp="set_discount(this.id)"/>
                        </td>
                        <td>
                            Annual Bonus %
                        </td>
                        <td>
                            <input type="text" name="txt_bonus" id="txt_bonus" class="text_boxes_numeric" style="width:150px;" onKeyUp="set_discount(this.id)"/>
                        </td>				
                    </tr>			
                    <tr>
                        <td>
                       		Bonus Amount 
                        </td>
                        <td>
                            <input type="text" name="txt_bonus_ammount" id="txt_bonus_ammount" class="text_boxes_numeric" style="width:150px;" onKeyUp="set_discount(this.id)"/>
                        </td>
                        <td>
                            Claim %
                        </td>
                        <td>
                            <input type="text" name="txt_claim" id="txt_claim" class="text_boxes_numeric" style="width:150px;" onKeyUp="set_discount(this.id)"/>
                        </td>
                        <td>
                            Claim Amount
                        </td>
                        <td>
                            <input type="text" name="txt_claim_ammount" id="txt_claim_ammount" class="text_boxes_numeric" style="width:150px;" onKeyUp="set_discount(this.id)"/>
                        </td>
                    </tr>			
                    <tr>
                        <td>
                            Invoice Quantity
                        </td>
                        <td>
                            <input type="text" name="txt_invo_qnty" id="txt_invo_qnty" class="text_boxes_numeric" style="width:150px;" disabled="disabled"/>
                        </td>
                        <td>
                            Commission %
                        </td>
                        <td>
                  			<input style="width:150px;" name="txt_commission" id="txt_commission" class="text_boxes_numeric" onKeyUp="set_discount(this.id)"></td>
                        <td>
                            Commission Amount
                        </td>
                        <td>
                            <input style="width:150px;" name="txt_commission_amt" id="txt_commission_amt" class="text_boxes_numeric" onKeyUp="set_discount(this.id)">
                        </td>
                  </tr>
                  <tr>
                        <td>
                            Other Deduction %
                        </td>
                        <td>
                            <input type="text" name="txt_other_discount" id="txt_other_discount" class="text_boxes_numeric" style="width:150px;" onKeyUp="set_discount(this.id)"/>
                        </td>
                        <td>
                            Other Deduction Amount
                        </td>
                        <td>
                  			<input type="text" name="txt_other_discount_amt" id="txt_other_discount_amt" class="text_boxes_numeric" style="width:150px;" onKeyUp="set_discount(this.id)"/>
                        </td>
                        <td>
                            Add Upcharge
                        </td>
                        <td>
                            <input type="text" name="txt_upcharge" id="txt_upcharge" class="text_boxes_numeric" style="width:150px;" onKeyUp="set_discount(this.id)" />
                        </td>
                  </tr>
                  <tr>
                        <td>
                            Net Invoice Value
                        </td>
                        <td>
                            <input type="text" name="txt_net_invo_val" id="txt_net_invo_val" class="text_boxes_numeric" style="width:150px;" disabled="disabled"/>
                        </td>
                        <td colspan="4">&nbsp;</td>
                  </tr>
                </table>
                <table width="100%">
                	<tr>
                        <td width="60%" colspan="4" valign="middle" align="right" class="button_container">
							<? 
								echo load_submit_buttons( $permission, "fnc_export_information_entry", 0,0 ,"reset_form('exportInformationFrm_1*exportInformationFrm_2','','','','reset_table();')",1) ; 
							?>
                            <input type="hidden" name="update_id" id="update_id" readonly />
                            <input type="hidden" name="tot_row" id="tot_row" readonly />
                        </td>
                        <td colspan="2" align="left" valign="top" class="button_container">   
                            <input type="button" name="close" class="formbutton" value="New Invoice Entry" id="resetPage" onClick="reset_page();" style="width:110px;"/>
                        </td>
                    </tr>
                </table>
            </form>
        </fieldset>
        <fieldset style="width:1220px; margin-bottom:10px;">
            <legend>Shipping Information</legend>
            <form name="exportInformationFrm_2" id="exportInformationFrm_2" autocomplete="off" method="POST"  >
                <table cellpadding="0" cellspacing="1" width="1100">
					<tr>
						<td>BL/Cargo No</td>
                        <td>
                            <input type="text" name="bl_no"  id="bl_no" class="text_boxes" style="width:150px" />
                        </td>
                        <td>BL/Cargo Date</td>
                        <td>
                            <input type="text" name="bl_date" id="bl_date" class="datepicker" style="width:150px" />
                        </td>
                        <td>Original BL Rev. Date</td>
                        <td>
                        	<input type="text" name="bl_rev_date" id="bl_rev_date" class="datepicker" style="width:150px" />
                        </td>
                    </tr>				
                    <tr>
                        <td>Doc. Handover</td>
                        <td>
                            <input type="text" name="doc_handover" id="doc_handover" class="datepicker" style="width:150px" />					
                        </td>					
                        <td>Custom Forwarder Name</td>
                        <td>
						<?
                            echo create_drop_down( "forwarder_name", 162, "select s.id, s.supplier_name from lib_supplier s, lib_supplier_tag_company b where s.status_active =1 and s.is_deleted=0 and b.supplier_id=s.id and s.id in (select supplier_id from lib_supplier_party_type where party_type in (30,31,32)) group by s.id, s.supplier_name order by supplier_name","id,supplier_name", 1, "--Select Frowarder--", $selected, "" );
                            
                        ?>
                        </td>
                        <td>ETD</td>
                        <td>
                            <input type="text" name="etd" id="etd" class="datepicker" style="width:150px" />					
                        </td>					
                    </tr>			
                    <tr>
                        <td>Feeder Vessel</td>
                        <td>
                            <input type="text" name="feeder_vessel" id="feeder_vessel" class="text_boxes" style="width:150px" />
                        </td>
                        <td>Mother Vessel</td>
                        <td>
                            <input type="text" name="mother_vessel" id="mother_vessel" class="text_boxes" style="width:150px" />
                        </td>
                        <td>ETA Date & Destination</td>
                        <td>
                            <input type="text" name="etd_destination" id="etd_destination" class="datepicker" style="width:60px" placeholder="Date" />
                            <input type="text" name="txt_eta_destination" id="txt_eta_destination" class="text_boxes" style="width:80px" placeholder="Destination"/>
                        </td>
                    </tr>			
                    <tr>
                        <td>IC Received Date</td>
                        <td>
                            <input type="text" name="ic_recieved_date" id="ic_recieved_date" class="datepicker" style="width:150px" />
                        </td>
                        <td>Inco Term</td>
                        <td>
                        	<?
							   	echo create_drop_down( "inco_term", 162, $incoterm,"", 1, "-- Select --", 0, "",1 );
							?>
                        </td>
                        <td>Inco Term Place</td>
                        <td>
                            <input type="text"  name="inco_term_place" id="inco_term_place" class="text_boxes"  style="width:150px">
                        </td>
                    </tr>
                    <tr>
                        <td>Shipping Bill No</td>
                        <td>
                            <input type="text" name="shipping_bill_no" id="shipping_bill_no" class="text_boxes" style="width:150px" />
                        </td>
                        <td>Shipping Bill Date </td> 
                        <td>
                            <input type="text" name="ship_bl_date" id="ship_bl_date" class="datepicker" style="width:150px" />
                        </td>
                        <td>Port of Entry</td>
                        <td>
                            <input type="text" name="port_of_entry" id="port_of_entry" class="text_boxes" style="width:150px" />
                        </td>
                    </tr>
                    <tr>
                       <td>Port of Loading</td>
                        <td>
                            <input type="text" name="port_of_loading" id="port_of_loading" class="text_boxes" style="width:150px" />
                        </td>
                        <td>Port of Discharge</td>
                        <td>
                            <input type="text" name="port_of_discharge" id="port_of_discharge" class="text_boxes" style="width:150px" />
                        </td>
                        <td>Internal File No</td>
                        <td>
                            <input type="text" name="internal_file_no" id="internal_file_no" class="text_boxes" style="width:150px" disabled="disabled"/>
                        </td>
                    </tr>
                    <tr>
                        <td>Shipping Mode</td>
                        <td>
                            <?
							   	echo create_drop_down( "shipping_mode", 162, $shipment_mode,"", 1, "-- Select --", 0, "" );
							?>
                        </td>
                        <td>Freight Amount By Supplier</td>
                        <td>
                            <input type="text" name="freight_amnt_supplier" id="freight_amnt_supplier" class="text_boxes_numeric" style="width:150px"/>
                        </td>
                        <td>Ex-factory Date</td>
                        <td>
                            <input type="text" name="ex_factory_date" id="ex_factory_date" class="datepicker" style="width:150px" />
                        </td>
                	</tr>
                    <tr>
                        <td>Actual Ship Date</td>
                        <td>
                            <input type="text" name="actual_shipment_date" id="actual_shipment_date" class="datepicker" style="width:150px" />
                        </td>
                        <td>Freight Amount By Buyer</td>
                        <td>
                            <input type="text" name="freight_amnt_buyer" id="freight_amnt_buyer" class="text_boxes_numeric" style="width:150px"/>
                        </td>
                    	<td>Total Carton Qnty</td>
                        <td>
                            <input type="text" name="total_carton_qnty" id="total_carton_qnty" class="text_boxes_numeric" style="width:150px;" />
                        </td>
                    </tr>
                    <tr>
                        <td>Category No</td>
                        <td>
                            <input type="text" name="txt_category_no" id="txt_category_no" class="text_boxes" style="width:150px;" />
                        </td>
                        <td>HS Code</td>
                        <td>
                            <input type="text" name="txt_hs_code" id="txt_hs_code" class="text_boxes" style="width:150px;" />
                        </td>
                    	<td>Advice Date</td>
                        <td>
                            <input type="text" name="txt_advice_date" id="txt_advice_date" class="datepicker" style="width:150px;" />
                        </td>
                    </tr>
                    <tr>
                        <td>Advice Amount</td>
                        <td>
                            <input type="text" name="txt_advice_amnt" id="txt_advice_amnt" class="text_boxes_numeric" style="width:150px;" />
                        </td>
                        <td>Paid Amount</td>
                        <td>
                            <input type="text" name="txt_paid_amnt" id="txt_paid_amnt" class="text_boxes_numeric" style="width:150px;" />
                        </td>
                    	<td>Incentive Applicable</td>
                        <td>
                        <?
							   	echo create_drop_down( "cbo_incentive", 162, $yes_no,"", 1, "-- Select --", 0, "" );
						?>
                        </td>
                    </tr>
                    <tr>
                        <td>GSP NO</td>
                        <td>
                            <input type="text" name="txt_gsp_co" id="txt_gsp_co" class="text_boxes" style="width:150px;" />
                        </td>
                        <td>GSP  Date</td>
                        <td>
                            <input type="text" name="txt_gsp_co_date" id="txt_gsp_co_date" class="datepicker" style="width:150px;" />
                        </td>
                    	<td>Yarn Cons./Pcs</td>
                        <td><input type="text" name="txt_cons" id="txt_cons" class="text_boxes_numeric" style="width:150px;" /></td>
                    </tr>
                    <tr>
                        <td>CO NO</td>
                        <td>
                            <input type="text" name="txt_co_no" id="txt_co_no" class="text_boxes" style="width:150px;" />
                        </td>
                        <td>CO Date</td>
                        <td>
                            <input type="text" name="txt_co_date" id="txt_co_date" class="datepicker" style="width:150px;" />
                        </td>
                    	<td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="6" height="50" valign="middle" align="center" class="button_container">
							<? 
								echo load_submit_buttons( $permission, "fnc_export_information_entry_shipping_info", 0,0 ,"reset_form('exportInformationFrm_2','','','','','internal_file_no*inco_term')",2) ; 
							?>
                            <input type="button" class="formbutton" id="btn_print_rpt" value="Print Report" style="width:100px;" onClick="fn_print_report()" >
                        </td>
                    </tr>
                </table>
            </form>
        </fieldset>
	</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>


<script>
var invoice_no = [<? echo substr(return_library_autocomplete( "select invoice_no from com_export_invoice_ship_mst where status_active=1 and is_deleted=0", "invoice_no"  ), 0, -1); ?>];
var exp_form_no = [<? echo substr(return_library_autocomplete( "select exp_form_no from com_export_invoice_ship_mst where status_active=1 and is_deleted=0", "exp_form_no"  ), 0, -1); ?>];
var port_of_entry = [<? echo substr(return_library_autocomplete( "select port_of_entry from com_export_invoice_ship_mst where status_active=1 and is_deleted=0", "port_of_entry"  ), 0, -1); ?>];
var port_of_loading = [<? echo substr(return_library_autocomplete( "select port_of_loading from com_export_invoice_ship_mst where status_active=1 and is_deleted=0", "port_of_loading"  ), 0, -1); ?>];
var feeder_vessel = [<? echo substr(return_library_autocomplete( "select feeder_vessel from com_export_invoice_ship_mst where status_active=1 and is_deleted=0", "feeder_vessel"  ), 0, -1); ?>];
var mother_vessel = [<? echo substr(return_library_autocomplete( "select mother_vessel from com_export_invoice_ship_mst where status_active=1 and is_deleted=0", "mother_vessel"  ), 0, -1); ?>];


$("#txt_invoice_no").autocomplete({source:invoice_no });
$("#txt_exp_form_no").autocomplete({source:exp_form_no });
$("#port_of_entry").autocomplete({source:port_of_entry });
$("#port_of_loading").autocomplete({source:port_of_loading });
$("#feeder_vessel").autocomplete({source:feeder_vessel });
$("#mother_vessel").autocomplete({source:mother_vessel });
</script>


</html>