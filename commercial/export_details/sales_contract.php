<?
/*-------------------------------------------- Comments

Purpose			: 	This form will create for buyer sales contract entry
Functionality	:
JS Functions	:
Created by		:	Bilas
Creation date 	: 	04-11-2012
Updated by 		: 	Fuad Shahriar
Update date		: 	20-03-2013
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
echo load_html_head_contents("Sales Contract Form", "../../", 1, 1,'','1','');

?>

<script>
if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../logout.php";

var permission='<? echo $permission; ?>';
 <?
    if($_SESSION['logic_erp']['data_arr'][107])
	{
		$data_arr= json_encode( $_SESSION['logic_erp']['data_arr'][107] );
		echo "var field_level_data= ". $data_arr . ";\n";
	}

	if($_SESSION['logic_erp']['mandatory_field'][107]!="")
	{
		$mandatory_field_arr= json_encode( $_SESSION['logic_erp']['mandatory_field'][107] );
		echo "var mandatory_field_arr= ". $mandatory_field_arr . ";\n";
	}
 ?>

var str_issuing_bank 		= [<? echo substr(return_library_autocomplete( "select distinct(issuing_bank) from com_sales_contract", "issuing_bank"  ), 0, -1); ?>];
var str_port_of_entry 		= [<? echo substr(return_library_autocomplete( "select distinct(port_of_entry) from com_sales_contract", "port_of_entry"  ), 0, -1); ?>];
var str_port_of_loading 	= [<? echo substr(return_library_autocomplete( "select distinct(port_of_loading) from com_sales_contract", "port_of_loading"  ), 0, -1); ?>];
var str_port_of_discharge 	= [<? echo substr(return_library_autocomplete( "select distinct(port_of_discharge) from com_sales_contract", "port_of_discharge"  ), 0, -1); ?>];
var str_inco_term_place 	= [<? echo substr(return_library_autocomplete( "select distinct(inco_term_place) from com_sales_contract", "inco_term_place"  ), 0, -1); ?>];

$(document).ready(function(e)
{
	$("#txt_issuing_bank").autocomplete({
		 source: str_issuing_bank
	});
	$("#txt_port_of_entry").autocomplete({
		 source: str_port_of_entry
	});
	$("#txt_port_of_loading").autocomplete({
		source: str_port_of_loading
	});
	$("#txt_port_of_discharge").autocomplete({
		source: str_port_of_discharge
	});
	$("#txt_inco_term_place").autocomplete({
		source: str_inco_term_place
	});

});


function party_loading_dischage_field(str)
{

	if(str==1)
	{
		reset_form("","","txt_port_of_entry*txt_port_of_loading*txt_port_of_discharge");
	}
	else if(str==2)
	{
		$("#txt_port_of_entry").val("From Supplier Factory");
		$("#txt_port_of_loading").val("From Supplier Factory");
		$("#txt_port_of_discharge").val("To Buyer Factory");
	}
}

function fnc_sales_contract(operation)
{
	/*if(operation==2)
	{
		show_msg('13');
		return;
	}*/
	var variable_setting= document.getElementById('hidden_variable_setting').value;

	if (variable_setting == 1)
	{
		if (form_validation('cbo_beneficiary_name*txt_internal_file_no*txt_year*txt_contract_value*txt_contract_date*cbo_convertible_to_lc*cbo_buyer_name*cbo_lien_bank*txt_last_shipment_date*cbo_pay_term*cbo_export_item_category','Beneficiary Name*Internal File No*Year*Contract Value*Contract Date*Convertable To*Buyer Name*Lean Bank*Shipment Date*Pay Term*Export Item Category')==false)
		{
			return;
		}
	}
	else
	{
		if (form_validation('cbo_beneficiary_name*txt_internal_file_no*txt_year*txt_contract_no*txt_contract_value*txt_contract_date*cbo_convertible_to_lc*cbo_buyer_name*cbo_lien_bank*txt_last_shipment_date*cbo_pay_term*cbo_export_item_category','Beneficiary Name*Internal File No*Year*Contract No*Contract Value*Contract Date*Convertable To*Buyer Name*Lean Bank*Shipment Date*Pay Term*Export Item Category')==false)
		{
			return;
		}
	}

	if('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][107]);?>'){
		if (form_validation('<? echo implode('*',$_SESSION['logic_erp']['mandatory_field'][107]);?>','<? echo implode('*',$_SESSION['logic_erp']['field_message'][107]);?>')==false)
		{					
			return;
		}
	}

	var data="action=save_update_delete_mst&operation="+operation+get_submitted_data_string('cbo_beneficiary_name*txt_internal_file_no*txt_bank_file_no*txt_year*txt_contract_no*txt_contract_value*cbo_currency_name*txt_contract_date*cbo_convertible_to_lc*cbo_buyer_name*txt_applicant_name*cbo_notifying_party*cbo_consignee*cbo_lien_bank*txt_lien_date*txt_issuing_bank*txt_trader*txt_country_origin*txt_last_shipment_date*txt_expiry_date*txt_tolerance*cbo_shipping_mode*cbo_pay_term*txt_tenor*cbo_inco_term*txt_inco_term_place*cbo_contract_source*txt_port_of_entry*txt_port_of_loading*txt_port_of_discharge*txt_shipping_line*txt_doc_presentation_days*txt_max_btb_limit*txt_foreign_comn*txt_local_comn*txt_discount_clauses*txt_claim_adjustment*txt_converted_from*txt_converted_from_id*txt_converted_btb_lc*txt_converted_btb_id*txt_remarks*txt_bl_clause*txt_system_id*txt_attach_row_id*contact_system_id*cbo_export_item_category*hidden_variable_setting*cbo_lc_for*txt_estimated_sc_qnty*cbo_ready_to_approved',"../../");

	freeze_window(operation);

	http.open("POST","requires/sales_contract_controller.php",true);
	http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = sales_contract_mst_Reply_info;
	
}


function sales_contract_mst_Reply_info()
{
	if(http.readyState == 4)
	{
		// alert(http.responseText);
		var reponse=trim(http.responseText).split('**');

		if(reponse[0]==31)
		{
			alert(reponse[1]);release_freezing();return;
		}
		show_msg(trim(reponse[0]));
		if((reponse[0]==0 || reponse[0]==1))
		{
			document.getElementById('txt_system_id').value = reponse[1];
			document.getElementById('contact_system_id').value = reponse[2];
			document.getElementById('txt_contract_no').value = reponse[3];
			$('#cbo_beneficiary_name').attr('disabled',true);
			if ($('#hidden_variable_setting').val()==1) $('#txt_contract_no').attr('disabled',true);
			else $('#txt_contract_no').attr('disabled',false);			
			$('#cbo_buyer_name').attr('disabled',true);
			$('#cbo_lc_for').attr('disabled',true)
			set_button_status(1, permission, 'fnc_sales_contract',1);
		}
		if(reponse[0] == 2)
		{
			location.reload();
		}

		if(reponse[0] == 50)
		{
			alert("This SC is Approved. So Update or Delete not allowed!!");
			release_freezing();
			return;
		}
		release_freezing();
		uploadFile( $("#txt_system_id").val());
	}
}


function fnc_po_selection_save(operation)
{
	if(operation==2)
	{
		show_msg('13');
		return;
	}

	if (form_validation('txt_system_id','Sales Contract No')==false )
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
				alert("Please Insert Attach Qnty");//
				$("#txtattachedqnty_"+j).focus();
				return;
			}
			submit_data += "*hiddenwopobreakdownid_"+j+"*txtattachedqnty_"+j+"*hiddenunitprice_"+j+"*txtattachedvalue_"+j+"*cbopostatus_"+j+"*txtfabdescrip_"+j+"*txtcategory_"+j+"*txthscode_"+j + "*isSales_" + j+ "*hiddensalescontractorderid_" + j+ "*txtcommission_" + j+ "*txtcommissionforeign_" + j;
		}
	}
	if(submit_data=="")
	{
		alert("Please Select Order No");
		return;
	}
	var data="action=save_update_delete_contract_order_info&noRow="+row_num+"&operation="+operation+get_submitted_data_string('txt_system_id'+submit_data,"../../");

	freeze_window(operation);

	http.open("POST","requires/sales_contract_controller.php",true);
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

		if((reponse[0]==0 || reponse[0]==1))
		{
			reset_form('salescontractfrm_2','','','txt_tot_row,0','$(\'#tbl_order_list tbody tr:not(:first)\').remove();','hidden_selectedID');
			show_list_view(reponse[1],'show_po_active_listview','po_list_view','requires/sales_contract_controller','setFilterGrid(\'tbl_list_search\',-1)');
			set_button_status(0, permission, 'fnc_po_selection_save',2);
			load_po_id();
		}
		else if(reponse[0]==13)
		{
			alert('Bellow Invoice Found. Detach Not Allowed.\n Invoice No: '+reponse[1]+"\n");
		}
		else if(reponse[0]==11)
		{
			alert(reponse[1]);
		}
		else if(reponse[0] == 50)
		{
			alert("This SC is Approved. So Update or Delete not allowed!!");
			release_freezing();
			return;
		}

		release_freezing();
	}
}

function openmypage(page_link,title,row_num)
{
	if( form_validation('txt_system_id','System ID')==false )
	{
		$('#contact_system_id').focus();
		return;
	}
	else
	{
		var cbo_export_item_category=$('#cbo_export_item_category').val();
		var cbo_lc_for=$('#cbo_lc_for').val();
		if(cbo_lc_for==2 && cbo_export_item_category !=1 && cbo_export_item_category !=2 && cbo_export_item_category !=3)
		{
			alert("LC For Sample Applicable For Knit Garments Or Woven Garments Or Sweater Garments");return;
		}
		
		var page_link = page_link+'&cbo_export_item_category=' + cbo_export_item_category+'&cbo_lc_for=' + cbo_lc_for;
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1300px,height=360px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0];
			var order_id=this.contentDoc.getElementById("txt_selected_id").value;
			var is_sales = this.contentDoc.getElementById("txt_is_sales").value;
			//alert(order_id);
			if(order_id!="")
			{
				var pre_selectID = $("#hidden_selectedID").val();

				if(trim(pre_selectID)=="") $("#hidden_selectedID").val(order_id);else $("#hidden_selectedID").val(pre_selectID+","+order_id);

				var tot_row=$('#txt_tot_row').val();
				var company_id = $('#cbo_beneficiary_name').val();

				var data=order_id+"**"+tot_row+ "**" + is_sales+ "**" + company_id;
				var list_view_orders = return_global_ajax_value( data, 'order_list_for_attach', '', 'requires/sales_contract_controller');
				var order_no=$('#txtordernumber_'+row_num).val();

				if(order_no=="")
				{
					$("#tr_"+row_num).remove();
				}

				$("#tbl_order_list tbody:last").append(list_view_orders);

				var numRow = $('table#tbl_order_list tbody tr').length;
				$('#txt_tot_row').val(numRow);

				var ddd={ dec_type:2, comma:0, currency:''}
				math_operation( "totalOrderqnty", "txtorderqnty_", "+", numRow, ddd );
				math_operation( "totalOrdervalue", "txtordervalue_", "+", numRow, ddd);
				math_operation( "totalAttachedqnty", "txtattachedqnty_", "+", numRow, ddd );
				math_operation( "totalAttachedvalue", "txtattachedvalue_", "+", numRow, ddd );
				set_all_onclick();
			}

		}

	}//end else
}

function convertible_to_lc_display()
{
	var myTest = document.getElementById("cbo_convertible_to_lc").value;
	var cbo_lc_for = document.getElementById("cbo_lc_for").value;
	if(cbo_lc_for==2)
	{
		$('#cbo_convertible_to_lc').val(2).attr("disabled",true);
		var list_view_po = return_global_ajax_value( cbo_lc_for, 'order_list_presentation', '', 'requires/sales_contract_controller');
		$("#tbl_order_list").html(list_view_po);
		set_all_onclick();
	}
	else
	{
		var list_view_po = return_global_ajax_value( cbo_lc_for, 'order_list_presentation', '', 'requires/sales_contract_controller');
		$("#tbl_order_list").html(list_view_po);
		set_all_onclick();
		$('#cbo_convertible_to_lc').attr("disabled",false);
		if(myTest=='1')
		{
			$('#convert_btb_lc_list').show();
			$('#convert_btb_lc_list_cap').show();
			//$('#salescontractfrm_2').hide();
		}
		else if(myTest=='2')
		{
	
			$('#convert_btb_lc_list').show();
			$('#convert_btb_lc_list_cap').show();
			//$('#salescontractfrm_2').show();
		}
		else if(myTest=='3')
		{
			$('#convert_btb_lc_list').hide();
			$('#convert_btb_lc_list_cap').hide();
			//$('#salescontractfrm_2').hide();
		}
	}
	

}

function fn_add_sales_contract(type)
{
	var beneficiary=document.getElementById('cbo_beneficiary_name').value;

	if (type==1) //Converted From
	{

		if (beneficiary==0)
		{
			alert("Please Select Beneficiary Name ");
			return;
		}
		var page_link='requires/sales_contract_controller.php?beneficiary='+beneficiary+'&action=fake_sc';
		var title='Sales Contract Selection Form';

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=650px,height=350px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]
			var fc_contract_no=this.contentDoc.getElementById("hidden_contract_no").value;
			var fc_contract_id=this.contentDoc.getElementById("hidden_contract_id").value;
			document.getElementById('txt_converted_from').value=fc_contract_no;
			document.getElementById('txt_converted_from_id').value=fc_contract_id;

		}

	}
	else if (type==2) // BTB
	{
		var txt_converted_from=document.getElementById('txt_converted_from_id').value;
		var txt_converted_btb_id=document.getElementById('txt_converted_btb_id').value;
		if (txt_converted_from=="")
		{
			alert("Please Select Converted from Contract No Name ");
			return;
		}

		var page_link='requires/sales_contract_controller.php?sales_contract='+txt_converted_from+'&txt_converted_btb_id='+txt_converted_btb_id+'&action=fake_btb';
		var title='BTB LC Selection Form';

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=750px,height=350px,center=1,resize=1,scrolling=0','../');
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]
			var btb_lc_number=this.contentDoc.getElementById("txt_selected").value;
			var btb_lc_id=this.contentDoc.getElementById("txt_selected_id").value;
			var btb_attach_id=this.contentDoc.getElementById("txt_attach_id").value;

			var txt_converted_btb_lc=document.getElementById('txt_converted_btb_lc').value;
			var txt_converted_btb_id=document.getElementById('txt_converted_btb_id').value;
			var txt_attach_row_id=document.getElementById('txt_attach_row_id').value;

			if(txt_attach_row_id!=btb_attach_id && btb_attach_id!="")
			{
				if(txt_attach_row_id=="")
				{
					document.getElementById('txt_converted_btb_lc').value=btb_lc_number;
					document.getElementById('txt_converted_btb_id').value=btb_lc_id;
					document.getElementById('txt_attach_row_id').value=btb_attach_id;
				}
				else
				{
					document.getElementById('txt_converted_btb_lc').value=txt_converted_btb_lc+"*"+btb_lc_number;
					document.getElementById('txt_converted_btb_id').value=txt_converted_btb_id+","+btb_lc_id;
					document.getElementById('txt_attach_row_id').value=txt_attach_row_id+","+btb_attach_id;
				}
			}

		}

	}
	else if (type==3) //system id
	{
		//alert(beneficiary);
		if (beneficiary==0)
		{
			alert("Please Select Beneficiary Name ");
			return;
		}

		var page_link='requires/sales_contract_controller.php?action=sales_contact_search&beneficiary='+beneficiary;
		//var page_link='requires/sales_contract_controller.php?beneficiary='+beneficiary+'&action=sales_contact_search';
		//var page_link='requires/sales_contract_controller.php?action=sales_contact_search';
		var title='Sales Contract Form';

		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1150px,height=450px,center=1,resize=1,scrolling=0','../')
		emailwindow.onclose=function()
		{

			var theform=this.contentDoc.forms[0];
			var sales_contract_id=this.contentDoc.getElementById("hidden_sales_contract_id").value;
			if(trim(sales_contract_id)!="")
			{
				freeze_window(5);
				reset_form('salescontractfrm_2','','','txt_tot_row,0','$(\'#tbl_order_list tbody tr:not(:first)\').remove();');
				get_php_form_data( sales_contract_id, "populate_data_from_sales_contract", "requires/sales_contract_controller" );
				show_list_view(sales_contract_id,'show_po_active_listview','po_list_view','requires/sales_contract_controller','setFilterGrid(\'tbl_list_search\',-1)');
				release_freezing();
			}

		}
	}
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

function load_po_id()
{
	//var sales_cotract_id=$('#txt_system_id').val();
	//if(sales_cotract_id!="")
	//{
		//get_php_form_data(sales_cotract_id, 'populate_attached_po_id', 'requires/sales_contract_controller');
	//}
}

function fn_add_date_field()
{
	$("#txt_expiry_date").val(add_days($('#txt_last_shipment_date').val(),'15'));
}

//fnc_lien_letter()
function fnc_lien_letter(type)
{
	if (form_validation('txt_system_id','System ID')==false )
	{
		return;
	}
	if (type==1) 
	{
		print_report(3+'**'+$('#txt_system_id').val(),'sales_contact_lien_letter','requires/sales_contract_controller');
	}
	if(type==2){
		print_report(3+'**'+$('#txt_system_id').val(),'sales_contact_lien_letter2','requires/sales_contract_controller');
	}
	if(type==3){
		print_report(3+'**'+$('#txt_system_id').val(),'sales_contact_lien_letter3','requires/sales_contract_controller');
	}
	if(type==4){
		print_report(3+'**'+$('#txt_system_id').val()+'**'+$('#cbo_beneficiary_name').val(),'sales_contact_lien_letter4','requires/sales_contract_controller');
	}
	if(type==5){
		freeze_window();
		var data="action=sales_contact_print&report_type=3"+'&txt_system_id='+$('#txt_system_id').val()+'&cbo_beneficiary_name='+$('#cbo_beneficiary_name').val();
		http.open("POST","requires/sales_contract_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_lien_letter_response;
	}
	if(type==6){
		print_report(3+'**'+$('#txt_system_id').val(),'sales_contact_lien_letter5','requires/sales_contract_controller');
	}
	if(type==7){
		let print_in_pad=0;
		if(confirm("Do you want to print in Pad?\n\nPress Ok to 'Allow'\nPress Cancel to 'Not Allow'")){print_in_pad=1;}
		print_report(3+'**'+$('#txt_system_id').val()+'**'+$('#cbo_beneficiary_name').val()+'**'+print_in_pad,'sales_contact_lien_letter7','requires/sales_contract_controller');
	}
	if (type==8) 
	{
		print_report(3+'**'+$('#txt_system_id').val(),'sales_contact_lien_letter8','requires/sales_contract_controller');
	}
	if (type==9) 
	{
		print_report(3+'**'+$('#txt_system_id').val(),'sales_contact_lien_letter9','requires/sales_contract_controller');
	}
	if (type==10) 
	{
		var txt_system_id = $("#txt_system_id").val();	
		var page_link='requires/sales_contract_controller.php?action=designation_search&txt_system_id='+txt_system_id; 
		var title="Designation Select Bar";
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=620px,height=370px,center=1,resize=0,scrolling=0','../')
		emailwindow.onclose=function()
		{
			var theform=this.contentDoc.forms[0]; 
			var txt_system_id=this.contentDoc.getElementById("txt_system_id").value; 
			$("#txt_system_id").val(txt_system_id);
		}
		//print_report(3+'**'+$('#txt_system_id').val(),'sales_contact_lien_letter10','requires/sales_contract_controller');
	}
	if (type==11)
	{
		var cbo_export_item_category = $("#cbo_export_item_category").val();
		if (cbo_export_item_category != 1){
			alert("This Category only for Knit Garments");return;
		}
		print_report($('#txt_system_id').val()+'**'+$('#cbo_beneficiary_name').val(),'sales_contact_lien_letter11','requires/sales_contract_controller');
	}
	if (type==12)
	{
		var show_textcbo_consignee = $("#show_textcbo_consignee").val();
	
		print_report($('#txt_system_id').val()+'**'+$('#cbo_beneficiary_name').val()+'**'+$('#show_textcbo_consignee').val(),'sales_contact_lien_letter12','requires/sales_contract_controller');
	}
	if (type==13)
	{
	
		print_report(3+'**'+$('#txt_system_id').val(),'sales_contact_lien_letter13','requires/sales_contract_controller');

	}

}

function fnc_lien_letter_response()
{
	if(http.readyState == 4){
		release_freezing();
		var file_data=http.responseText.split("****");
		$('#data_panel').html(file_data[1]);
		$('#print_report_Excel').removeAttr('href').attr('href','requires/'+trim(file_data[0]));
		document.getElementById('print_report_Excel').click();

		var w = window.open("Surprise", "_blank");
		var d = w.document.open();
		d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../css/prt.css" type="text/css" /><title></title></head><body>'+document.getElementById('data_panel').innerHTML+'</body</html>');
		d.close();
	}
}

function fnc_check_list()
{
	//alert("su..re");
	if (form_validation('txt_system_id','System ID')==false )
	{
		return;
	}
	print_report(3+'**'+$('#txt_system_id').val(),'sales_contact_check_list','requires/sales_contract_controller');
}

function fn_file_no()
{
	//alert(1);return;
	if( form_validation('cbo_beneficiary_name','Company Name')==false )
	{
		return;
	}
	var companyID=$('#cbo_beneficiary_name').val();
	var page_link='requires/sales_contract_controller.php?action=file_search&companyID='+companyID;
	var title='File Search Form';
	emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=535px,height=350px,center=1,resize=1,scrolling=0','../');
	emailwindow.onclose=function()
	{
		var theform=this.contentDoc.forms[0];
		var file_ref=this.contentDoc.getElementById("hidden_file_id").value;
		//alert(file_ref[1]);return;
		$('#txt_internal_file_no').val(file_ref);
	}
}

function fn_file_no_library()
{
    //alert(1);return;
    if( form_validation('cbo_beneficiary_name','Company Name')==false )
    {
        return;
    }
    var companyID=$('#cbo_beneficiary_name').val();
    var page_link='requires/sales_contract_controller.php?action=file_search_library&companyID='+companyID;
    var title='File Search From Library';
    emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=535px,height=350px,center=1,resize=1,scrolling=0','../');
    emailwindow.onclose=function()
    {
        var theform=this.contentDoc.forms[0];
        var file_ref=this.contentDoc.getElementById("hidden_file_id").value;
        $('#txt_internal_file_no').val(file_ref);
    }
}

function fn_clear_sc()
{
	$('#txt_converted_from').val("");
	$('#txt_converted_from_id').val("");
}

function sc_lc_popup()
{
    var page_link='requires/sales_contract_controller.php?action=sc_lc_search';
    var title='SC/LC Search';
    emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=535px,height=350px,center=1,resize=1,scrolling=0','../');
    emailwindow.onclose=function()
    {
        var theform=this.contentDoc.forms[0];
        var sc_lc=this.contentDoc.getElementById("js_set").value;
        $('#txt_contract_no').val(sc_lc);
    }
}

function check_variable_setting(beneficiary_id)
{
	var response=return_global_ajax_value(beneficiary_id, 'company_variable_setting_check', '', 'requires/sales_contract_controller').split("__");	
	//alert(response);
	$('#txt_contract_no').val('');
	$('#txt_contract_no').attr('disabled',false);
	if (response[0] == 1) {
		$('#hidden_variable_setting').val(response);		
		$('#txt_contract_no').attr('disabled',true);
	}
	
	if(response[1]==1)
	{
		$('#txt_max_btb_limit').val(response[2]);
		$('#txt_max_btb_limit').attr('disabled',true);
	}
	else
	{
		$('#txt_max_btb_limit').val("");
		$('#txt_max_btb_limit').attr('disabled',false);
	}
}

function sendMail()
{
	if (form_validation('contact_system_id','System Id')==false)
	{
		return;
	}
	var com_id=$('#cbo_beneficiary_name').val();
	var type= 1;
	fnSendMail('../../', '', 1, 0, 0, 1, type, com_id);
	//return; 
}

function call_print_button_for_mail(mail,mail_body,type)
{
	var sys_id=$('#contact_system_id').val();
	var manual =1;
	var data="action=sc&sys_id="+sys_id+'&mail='+mail+'&manual='+manual+'&mail_body='+mail_body;
	freeze_window(operation);
	http.open("POST", "../../auto_mail/lcsc_notification_auto_mail.php", true);
	http.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	http.send(data);
	http.onreadystatechange = function fnc_btb_mst_reponse()
	{
		//alert(http.responseText);
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText);
			alert(reponse);
			release_freezing(); 

		}
	} 
}

function fn_submit_order_list(is_seles)
{
	var row_num = $('table#tbl_list_search tbody tr').length-1;
	//alert(row_num);return;
	var submit_datas="";
	for(var j=1;j<=row_num;j++)
	{
		if ($('#chkOrd_'+j).is(':checked')) 
		{
			if(submit_datas!="") submit_datas+=",";
			submit_datas+=$("#chkOrd_"+j).val();
		}
	}
	var list_view_orders = return_global_ajax_value( submit_datas+'**'+is_seles, 'order_list_for_attach_update', '', 'requires/sales_contract_controller');
	$("#tbl_order_list tbody:last").html(list_view_orders);
	var num_row=$('table#tbl_order_list tbody tr').length;
	set_button_status(1,permission, 'fnc_po_selection_save',2);
	var ddd={ dec_type:2, comma:0, currency:''}
	math_operation( "totalOrderqnty", "txtorderqnty_", "+", num_row, ddd );
	math_operation( "totalOrdervalue", "txtordervalue_", "+", num_row, ddd);
	math_operation( "totalAttachedqnty", "txtattachedqnty_", "+", num_row, ddd );
	math_operation( "totalAttachedvalue", "txtattachedvalue_", "+", num_row, ddd );
	set_all_onclick();
}

function copy_all(str)
{
	var str_ref=str.split("_");
	var num_row=$('table#tbl_order_list tbody tr').length;
	for(var i=str_ref[1]; i<=num_row; i++)
	{
		$("#cbopostatus_"+i).val(str_ref[0]);
	}
}

function fn_all_chk()
{
    if ($('#chkOrd_th').is(':checked')) 
    {
        $("[name='chkOrd[]']").each(function (e) {
            $(this).prop("checked",true);
        });
    }
    else
    {
        $("[name='chkOrd[]']").each(function (e) {
            $(this).prop("checked",false);
        });
    }
}

function uploadFile(mst_id){
	$(document).ready(function() {
		var fd = new FormData();
		var files = $('#pi_mst_file')[0].files; 
		 for (let i = 0; i < files.length; i++) {
				 fd.append('file[]',files[i],files[i].name);
			}
		//fd.append('pi_mst_file',this.file_group_id);
		//fd.append('file', files); 
		$.ajax({ 
			url: 'requires/sales_contract_controller.php?action=file_upload&mst_id='+ mst_id, 
			type: 'post', 
			data: fd, 
			contentType: false, 
			processData: false,
			success: function(response){
				if(response != 0){
					document.getElementById('pi_mst_file').value=null;
				} 
				else{ 
					alert('file not uploaded'); 
				} 
			}, 
		}); 
	}); 
}
</script>

<style>
#salescontractfrm_1 input:not([type=checkbox]) input:not([class=flt]){
	width:152px;
}

/*#salescontractfrm_1 input[type=checkbox]{
	width:10px; Both Works Perfectly
}*/
</style>

</head>

<body onLoad="set_hotkey()">
	<div style="width:100%;" align="center">
     	<? echo load_freeze_divs ("../../",$permission); ?>

        <fieldset style="width:1150px; margin-bottom:10px;">
            <legend>Sales Contract Entry</legend>
            <form name="salescontractfrm_1" id="salescontractfrm_1" autocomplete="off" method="POST" action="" >
                <table cellpadding="0" cellspacing="1" width="100%">
                	<tr>
                    	<td colspan="8" align="center" ><b>System ID</b>
                        	<input type="hidden" name="txt_system_id" id="txt_system_id"  readonly class="text_boxes">
                        	<input type="text" name="contact_system_id" id="contact_system_id"  placeholder="Double Click" onDblClick="fn_add_sales_contract(3)" readonly class="text_boxes">
                        </td>
                    </tr>
                    <tr><td height="5" colspan="8"></td></tr>
                  	<tr>
                    	<td width="110" class="must_entry_caption">Beneficiary</td>
                        <td>
                        	<?
							echo create_drop_down( "cbo_beneficiary_name", 162, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business in(1,3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Beneficiary --", 0, "load_drop_down( 'requires/sales_contract_controller', this.value, 'load_drop_down_buyer_search', 'buyer_td_id' );load_drop_down( 'requires/sales_contract_controller', this.value, 'load_drop_down_applicant_name', 'applicant_name_td' );load_drop_down( 'requires/sales_contract_controller', this.value, 'load_drop_down_notifying_party', 'notifying_party_td' );load_drop_down( 'requires/sales_contract_controller', this.value, 'load_drop_down_consignee', 'consignee_td' );get_php_form_data( this.value, 'get_btb_limit', 'requires/sales_contract_controller' );get_php_form_data( this.value, 'eval_multi_select', 'requires/sales_contract_controller' ); get_php_form_data( this.value, 'file_write_mathod', 'requires/sales_contract_controller' );set_field_level_access(this.value);get_php_form_data( this.value, 'print_button_variable_setting', 'requires/sales_contract_controller' );check_variable_setting(this.value);" );
							?>
							<input type="hidden" name="hidden_variable_setting" id="hidden_variable_setting" value="">
                        </td>
                        <td width="110" class="must_entry_caption">Internal File No</td>
                    	<td><input type="text" name="txt_internal_file_no" id="txt_internal_file_no" style="width:150px" class="text_boxes" /></td>
                        <td width="110">Bank File No</td>
                        <td width="170"><input type="text" name="txt_bank_file_no" id="txt_bank_file_no" style="width:150px" class="text_boxes" maxlength="100" title="Maximum Character 100" /></td>
                        <td width="110" class="must_entry_caption">File Year</td>
                        <td width="170"><input name="txt_year" id="txt_year" style="width:150px" class="text_boxes" maxlength="10" title="Maximum Character 10" ></td>
                    </tr>
                    <tr>
                        <td class="must_entry_caption">Contract Number</td>
                        <td><input type="text" name="txt_contract_no" style="width:150px" id="txt_contract_no" class="text_boxes" onDblClick="sc_lc_popup()" placeholder="Write/Double Click to Search" maxlength="50" title="Maximum Character 50">
                        <input type="hidden" name="txt_contract_id" id="txt_contract_id" style="width:150px" class="text_boxes" ></td>
                        <td class="must_entry_caption">Contract Date</td>
                        <td><input type="text" name="txt_contract_date" id="txt_contract_date" style="width:150px" class="datepicker" readonly /></td>
                        <td>LC For</td>
                        <td>
                            <? echo create_drop_down( "cbo_lc_for", 162, $lc_for_arr,"", 0, "", 1, "convertible_to_lc_display()" ); ?>
                        </td>
                        <td class="must_entry_caption">Convertible to</td>
                        <td>
                            <? echo create_drop_down( "cbo_convertible_to_lc", 162, $convertible_to_lc,"", 1, "--Select--", 0, "convertible_to_lc_display()" ); ?>
                        </td>
                    </tr>

                    <tr>
                        <td class="must_entry_caption">Contract Value</td>
                        <td><input type="text" name="txt_contract_value" style="width:90px" id="txt_contract_value" class="text_boxes_numeric" ><input type="text" name="txt_ini_contract_value" style="width:50px" id="txt_ini_contract_value" class="text_boxes_numeric" placeholder="Initial Value" title="Initial Value" readonly disabled  ></td>
                        <td class="must_entry_caption">Buyer Name</td>
                        <td id="buyer_td_id">
                        	<? echo create_drop_down( "cbo_buyer_name", 162, $blank_array,"", 1, "---- Select ----", 0, "" ); ?>
                        </td>
                        <td>Applicant Name</td>
                        <td id="applicant_name_td">
                            <? echo create_drop_down( "txt_applicant_name", 162, $blank_array,"", 1, "---- Select ----", 0, "" ); ?>
                        </td>
                        <td>Notifying Party</td>
                        <td id="notifying_party_td">
                        	<? echo create_drop_down( "cbo_notifying_party", 162, $blank_array,"", 0, "---- Select ----", 0, "" ); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Consignee</td>
                        <td id="consignee_td">
                      		<? echo create_drop_down( "cbo_consignee", 162, $blank_array,"", 0, "---- Select ----", 0, "" ); ?>
                        </td>
                        <td class="must_entry_caption">Lien Bank</td>
                        <td>
					  		<?
							if ($db_type==0)
							{
								echo create_drop_down( "cbo_lien_bank", 162, "select concat(a.bank_name,' (', a.branch_name,')') as bank_name,id from lib_bank where is_deleted=0 and status_active=1 and lien_bank=1 order by bank_name","id,bank_name", 1, "-- Select Lien Bank --", 0, "" );
							}
							else
							{
								echo create_drop_down( "cbo_lien_bank", 162, "select (bank_name || ' (' || branch_name || ')' ) as bank_name,id from lib_bank where is_deleted=0 and status_active=1 and lien_bank=1 order by bank_name","id,bank_name", 1, "-- Select Lien Bank --", 0, "" );
							}
							?>
                        </td>
                        <td class="must_entry_caption">Lien Date</td>
                        <td><input type="text" name="txt_lien_date" id="txt_lien_date" class="datepicker" style="width:150px" readonly ></td>
                        <td>Issuing Bank</td>
                        <td id="issue_bank_td">
                        	<? echo create_drop_down( "txt_issuing_bank", 162, $blank_array,"", 1, "---- Select ----", 0, "" ); ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="must_entry_caption">Last Shipment Date</td>
                        <td><input type="text" name="txt_last_shipment_date" style="width:150px" id="txt_last_shipment_date" class="datepicker" readonly  onChange="fn_add_date_field();" ></td>
                        <td>Expiry Date</td>
                        <td><input type="text" name="txt_expiry_date" style="width:150px" id="txt_expiry_date" class="datepicker" readonly ></td>
                        <td >Trader</td>
                        <td><input type="text" name="txt_trader" id="txt_trader" style="width:150px" class="text_boxes" /></td>
                        <td>Opposite Country Dest.</td>
                        <td><input type="text" name="txt_country_origin" id="txt_country_origin" style="width:150px" class="text_boxes" /></td>
                    </tr>
                    <tr>
                        <td>Shipping Mode</td>
                        <td>
							<? echo create_drop_down("cbo_shipping_mode", 162, $shipment_mode,"", 0, "", 0, "" );?>
                        </td>
                        <td class="must_entry_caption">Pay Term</td>
                        <td>
							<? echo create_drop_down("cbo_pay_term", 162, $pay_term,"", 1, "--- Select ---", 0, "", "", "1,2,3,4");?>
                        </td>
                        <td>Tenor</td>
                        <td><input type="text" name="txt_tenor" id="txt_tenor" class="text_boxes_numeric" style="width:150px" /></td>
                        <td>Tolerance %</td>
                        <td><input type="text" name="txt_tolerance" id="txt_tolerance" style="width:150px" class="text_boxes_numeric" value="5" ></td>
                    </tr>
                    <tr>
                        <td>Incoterm</td>
                        <td>
							<? echo create_drop_down("cbo_inco_term", 162, $incoterm,"", 0, "", 0, "" );?>
                        </td>
                        <td>Incoterm Place</td>
                        <td><input type="text" name="txt_inco_term_place" id="txt_inco_term_place" style="width:150px" class="text_boxes" value="" maxlength="50" title="Maximum Character 50" /></td>
                        <td>Doc Present Days</td>
                        <td><input type="text" name="txt_doc_presentation_days" id="txt_doc_presentation_days" style="width:150px" class="text_boxes_numeric" maxlength="50" title="Maximum Character 50" /></td>
                        <td>Contract Source</td>
                        <td>
							<? echo create_drop_down( "cbo_contract_source", 162, $contract_source,"",1,"--- Select ---", 0, "party_loading_dischage_field(this.value)"); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Port of Entry</td>
                        <td><input type="text" name="txt_port_of_entry" id="txt_port_of_entry" style="width:150px" class="text_boxes" value="Ctg" maxlength="50" title="Maximum Character 50" /></td>
                        <td>Port of Loading</td>
                        <td><input type="text" name="txt_port_of_loading" id="txt_port_of_loading" style="width:150px" class="text_boxes" value="" maxlength="50" title="Maximum Character 50" /></td>
                        <td class="must_entry_caption">Port of Discharge</td>
                        <td><input type="text" name="txt_port_of_discharge" id="txt_port_of_discharge" style="width:150px" class="text_boxes" maxlength="50" title="Maximum Character 50" /></td>
                        <td>Shipping Line</td>
                        <td><input type="text" name="txt_shipping_line" id="txt_shipping_line" style="width:150px" class="text_boxes" maxlength="50" title="Maximum Character 50" /></td>
                    </tr>

                    <tr>
                        <td class="must_entry_caption">BTB Limit %</td>
                        <td>
                        	<input type="text" name="txt_max_btb_limit" id="txt_max_btb_limit" style="width:150px" class="text_boxes_numeric" value="">
                        </td>
                        <td>Foreign Comn%</td>
                        <td>
                            <input type="text" name="txt_foreign_comn" id="txt_foreign_comn" style="width:150px" class="text_boxes_numeric"  />
                        </td>
                        <td>Local Comn%</td>
                        <td>
                            <input type="text" name="txt_local_comn" id="txt_local_comn" style="width:150px" class="text_boxes_numeric"  />
                        </td>
                        <td>Claim Adjustment</td>
                        <td><input type="text" name="txt_claim_adjustment" id="txt_claim_adjustment" style="width:150px" class="text_boxes_numeric" /></td>
                    </tr>
                    <tr>
                        <td>Converted From
                        <input type="hidden" name="txt_converted_from_id" id="txt_converted_from_id" class="text_boxes" ></td>
                        <td><input type="text" name="txt_converted_from" id="txt_converted_from" placeholder="Double Click" onDblClick="fn_add_sales_contract(1)" readonly class="text_boxes" style="width:120px" ><input type="button" style="width:30; cursor:pointer;border:outset 1px #66CC00; background-image: -moz-linear-gradient(bottom, rgb(136,170,214) 7%, rgb(194,220,255) 10%, rgb(136,170,214) 96%); background-image: -webkit-linear-gradient(bottom, rgb(136,170,214) 7%, rgb(194,220,255) 10%, rgb(136,170,214) 96%); color:#171717; font-size: 13px; font-weight:bold; padding: 1px 2px; border-radius:.7em;" id="btn_clear" value="CLR" onClick="fn_clear_sc()" /></td>
                        <td id="convert_btb_lc_list_cap">Transfered BTB LC</td>
                        <input type="hidden" name="txt_converted_btb_id" id="txt_converted_btb_id" class="text_boxes" >
                        <input type="hidden" name="txt_attach_row_id" id="txt_attach_row_id" class="text_boxes" >
                        <td id="convert_btb_lc_list"><input type="text" readonly placeholder="Double Click" name="txt_converted_btb_lc" onDblClick="fn_add_sales_contract(2)" id="txt_converted_btb_lc" class="text_boxes" style="width:150px"></td>
                        <td>Discount Clauses</td>
                        <td><input type="text" name="txt_discount_clauses" id="txt_discount_clauses" class="text_boxes" style="width:150px" maxlength="2000" title="Maximum Character 2000"></td>
                        <td>BL Clause</td>
                        <td><input type="text" name="txt_bl_clause" id="txt_bl_clause" class="text_boxes" style="width:150px" maxlength="2000" title="Maximum Character 2000"></td>
                    </tr>
                    <tr>
                    	<td class="must_entry_caption">Export Item Category </td>
               			<td>
                        	<?
                        	asort($export_item_category);
                            echo create_drop_down( "cbo_export_item_category", 162, $export_item_category,"", 1, "--- Select ---", 1, "" );
                            // echo create_drop_down( "cbo_currency_name", 162, $currency,"", 0, "", 2, "" );
                            ?>
                        </td>
                    	<td>Remarks</td>
               			<td colspan="3"><input name="txt_remarks" id="txt_remarks" class="text_boxes" style="width:455px" maxlength="255" title="Maximum Character 255"></td>
               			<td></td> 
						<td>
							<input type="button" id="image_button" class="image_uploader" style="width:75px;" value="IMAGE" onClick="file_uploader( '../../', document.getElementById('txt_system_id').value,'', 'sales_contract',1,1)" />
							<input type="button" id="image_button" class="image_uploader" style="width:75px;" value="FILE" onClick="file_uploader( '../../', document.getElementById('txt_system_id').value,'', 'sales_contract',2,1)" />
						</td>
                    </tr>
                     <tr>
                    	<td>Currency</td>
                        <td>
                         	<? echo create_drop_down( "cbo_currency_name", 162, $currency,"", 0, "", 2, "" ); ?>
                        </td>
                    	<td class="must_entry_caption">Estimated SC Qnty</td>
               			<td><input type="text" name="txt_estimated_sc_qnty" id="txt_estimated_sc_qnty" style="width:150px" class="text_boxes_numeric"/></td>
						<td>Ready To Approved</td>
                    	<td><? echo create_drop_down( "cbo_ready_to_approved", 162, $yes_no,"", 1, "-- Select--", 2, "","","" ); ?></td>
						<td align="left">File</td>
						<td align="left">
							<input type="file" multiple id="pi_mst_file" class="image_uploader" style="width:150px" onChange="document.getElementById('txt_file').value=1">
							<input type="hidden" multiple id="txt_file">
						</td>
                    </tr>
                    <tr>
                        <td colspan="8" height="50" valign="middle" align="center" class="button_container">
						<div id="approved" style="float:left; font-size:24px; color:#FF0000;"></div>
						<? echo load_submit_buttons( $permission, "fnc_sales_contract", 0,0,"reset_form('salescontractfrm_1*salescontractfrm_2','po_list_view','','txt_port_of_entry,Ctg*txt_tot_row,0*txt_tolerance,5*cbo_currency_name,2*cbo_export_item_category,1','disable_enable_fields(\'cbo_beneficiary_name*txt_contract_value*txt_last_shipment_date*txt_expiry_date*cbo_shipping_mode*cbo_inco_term*txt_inco_term_place*txt_port_of_entry*txt_port_of_loading*txt_port_of_discharge*cbo_pay_term*txt_tenor*txt_claim_adjustment*txt_discount_clauses*bl_clause*txt_remarks\',0)');convertible_to_lc_display();$('#tbl_order_list tbody tr:not(:first)').remove();",1); ?>

                        <!-- <input type="button" value="Lien Letter" id="btn_lien_letter" name="btn_lien_letter" class="formbutton" style="width:100px;" onClick="fnc_lien_letter(1)" />

                        <input type="button" value="Lien Letter2" id="btn_lien_letter2" name="btn_lien_letter2" class="formbutton" style="width:100px;" onClick="fnc_lien_letter(2)" />
                        <input type="button" value="Lien Export Lc App" id="btn_lien_letter2" name="btn_lien_letter2" class="formbutton" style="width:120px;" onClick="fnc_lien_letter(3)" />
                         <input type="button" value="Lien Lc App2" id="btn_lien_letter3" name="btn_lien_letter3" class="formbutton" style="width:120px;" onClick="fnc_lien_letter(4)" />

                        <input type="button" value="Check List" id="btn_check_list" name="btn_check_list" class="formbutton" style="width:100px;" onClick="fnc_check_list()" />  -->
						<!-- <input type="button" value="Lien Letter 5" id="btn_lien_letter_5" name="btn_lien_letter_5" class="formbutton" style="width:100px;" onClick="fnc_lien_letter(5)" /> -->
						<input type="button" value="Lien Letter 8" id="btn_lien_letter8" name="btn_lien_letter8" class="formbutton" style="width:100px;" onClick="fnc_lien_letter(8)"/>
						<input type="button" value="Lien Letter 9" id="btn_lien_letter9" name="btn_lien_letter9" class="formbutton" style="width:100px;" onClick="fnc_lien_letter(9)"/>

						<input type="button" value="Print FH" id="btn_lien_letter11" name="btn_lien_letter11" class="formbutton" style="width:100px;" onClick="fnc_lien_letter(11)"/>
						
						<span id="button_data_panel"></span>
                        <input class="formbutton" type="button" onClick="sendMail()" value="Mail Send" style="width:80px;">
                        </td>
                    </tr>
                </table>
            </form>
		</fieldset>
        <input type="button" value="Click for already attached PO" name="Attached_PO" id="Attached_PO" class="formbutton" onClick="openmypage('requires/sales_contract_controller.php?action=order_popup&types=attached_po_status&buyer_id='+document.getElementById('cbo_buyer_name').value+'&selectID='+document.getElementById('hidden_selectedID').value+'&sales_contractID='+document.getElementById('txt_system_id').value+'&company_id='+document.getElementById('cbo_beneficiary_name').value+'&lc_sc_no='+document.getElementById('txt_contract_no').value,'Attached PO','')" style="width:210px"/>
		<form name="salescontractfrm_2" id="salescontractfrm_2" method="POST" action="" >
			<fieldset style="width:1350px; margin:5px">
                <table width="100%" cellspacing="0" cellpadding="0" class="rpt_table" id="tbl_order_list" border="1" rules="all">
                    <thead>
                    	<tr>
                            <th class="must_entry_caption">Order Number</th>
                            <th>Acc.PO No.</th>
                            <th>Order Qty</th>
                            <th>Order Value</th>
                            <th class="must_entry_caption">Attach. Qty</th>
                            <th>Rate</th>
                            <th>Attach. Val.</th>
                            <th>Commission Local </th>
                            <th>Commission Foreign</th>
                            <th>Style Ref</th>
                            <th>Style Desc.</th>
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
                            <td><input type="text" name="txtordernumber_1" id="txtordernumber_1" class="text_boxes" style="width:90px"  onDblClick= "openmypage('requires/sales_contract_controller.php?action=order_popup&types=order_select_popup&buyer_id='+document.getElementById('cbo_buyer_name').value+'&selectID='+document.getElementById('hidden_selectedID').value+'&sales_contractID='+document.getElementById('txt_system_id').value+'&company_id='+document.getElementById('cbo_beneficiary_name').value+'&lc_sc_no='+document.getElementById('txt_contract_no').value,'PO Selection Form',1)" readonly= "readonly" placeholder="Double Click" value=""/>
                            <input type="hidden" name="hiddenwopobreakdownid_1" id="hiddenwopobreakdownid_1" readonly value="">
                            <input type="hidden" name="isSales_1" id="isSales_1" value="">
                            </td>
                            <td><input type="text" name="txtaccordernumber_1" id="txtaccordernumber_1" class="text_boxes" style="width:90px;" readonly= "readonly" /></td>
                            <td><input type="text" name="txtorderqnty_1" id="txtorderqnty_1" class="text_boxes_numeric" style="width:60px;" readonly= "readonly" /></td>
                            <td><input type="text" name="txtordervalue_1" id="txtordervalue_1" class="text_boxes_numeric" style="width:80px;" readonly= "readonly"/></td>
                            <td><input type="text" name="txtattachedqnty_1" id="txtattachedqnty_1" class="text_boxes_numeric" style="width:60px" onKeyUp="validate_attach_qnty(1)" />
                            	<input type="hidden" name="hideattachedqnty_1" id="hideattachedqnty_1" class="text_boxes_numeric" style="width:70px; text-align:right"/>
                            </td>
                            <td>
                                <input type="text" name="hiddenunitprice_1" id="hiddenunitprice_1" class="text_boxes_numeric" style="width:50px" onKeyUp="calculate_attach_val(1)" readonly disabled >
                            </td>
                            <td><input type="text" name="txtattachedvalue_1" id="txtattachedvalue_1" class="text_boxes_numeric" style="width:80px" readonly= "readonly"/></td>
							<td><input type="text" name="txtcommission_1" id="txtcommission_1" class="text_boxes_numeric" style="width:80px" readonly= "readonly"/></td>
							<td><input type="text" name="txtcommissionforeign_1" id="txtcommissionforeign_1" class="text_boxes_numeric" style="width:80px" readonly= "readonly"/></td>
                            <td><input type="text" name="txtstyleref_1" id="txtstyleref_1" class="text_boxes" style="width:90px" readonly= "readonly"/></td>
                            <td><input type="text" name="txtStyleDesc_1" id="txtStyleDesc_1" class="text_boxes" style="width:90px" readonly= "readonly"/></td>
                            <td><input type="text" name="txtitemname_1" id="txtitemname_1" class="text_boxes" style="width:80px" readonly= "readonly"/></td>
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
                            <td><input type="text" name="txtbrand_1" id="txtbrand_1" class="text_boxes_numeric" style="width:50px" readonly= "readonly" /></td>
                            <td>
                                <?
                                echo create_drop_down( "cbopostatus_1", 60, $attach_detach_array,"", 0, "", 1, "" );
                                ?>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                    	<tr class="tbl_bottom">
                          <td>&nbsp;</td>
                          <td>Total</td>
                          <td><input type="text" name="totalOrderqnty" id="totalOrderqnty" class="text_boxes_numeric" style="width:60px;" readonly= "readonly" /></td>
                          <td><input type="text" name="totalOrdervalue" id="totalOrdervalue" class="text_boxes_numeric" style="width:80px;" readonly= "readonly" /></td>
                          <td><input type="text" name="totalAttachedqnty" id="totalAttachedqnty" class="text_boxes_numeric" style="width:60px;" readonly= "readonly" /></td>
                          <td>&nbsp;</td>
                          <td><input type="text" name="totalAttachedvalue" id="totalAttachedvalue" class="text_boxes_numeric" style="width:80px;" readonly= "readonly" /></td>
                          <td colspan="11">&nbsp;</td>
                        </tr>
                    	<tr>
                        	<td colspan="18" height="50" valign="middle" align="center" class="button_container">
							<? echo load_submit_buttons( $permission, "fnc_po_selection_save", 0,0 ,"reset_form('salescontractfrm_2','','','txt_tot_row,0','$(\'#tbl_order_list tbody tr:not(:first)\').remove();load_po_id();','')",2) ; ?>
                            <input type="hidden" name="hiddensalescontractorderid" id="hiddensalescontractorderid" readonly= "readonly" /> <!-- for update -->
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
	<div style="display:none" id="data_panel"></div>
	<a id="print_report_Excel" href="" style="text-decoration:none" download hidden>#</a>
</body>
<script>
	set_multiselect('cbo_notifying_party*cbo_consignee','0*0','0','','0*0');
	//$("#cbo_lc_for").val(1);
	$(function(){
		// alert("body loaded");
		for (var property in mandatory_field_arr) {
			$("#"+mandatory_field_arr[property]).parent().prev('td').css("color", "blue");
		}
	});
</script>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>